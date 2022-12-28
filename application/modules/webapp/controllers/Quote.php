<?php

namespace Application\Modules\Web\Controllers;

class Quote extends MX_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model( 'User_model','user_service' );
		$this->userdata = $this->user_service->userdata;
		$this->jobs_response = $this->user_service->jobs_response;
		$this->view_customers = $this->user_service->view_customers;
		$this->module_id 	   = $this->webapp_service->_get_module_id( $this->router->fetch_class() );
		$access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$access ){
			//$this->_render_webpage('errors/access-denied', false, false, true );
		}

		if( !$this->identity() ){
			redirect( 'webapp/user/login', 'refresh' );
		}
	}


	function index(){
		$data = false;

		redirect('webapp/quote/dashboard', 'refresh');
	}

	function dashboard(){
		$data = false;

		$data['feedback'] 			= !empty( $this->session->flashdata( 'feedback' ) ) ? ( $this->session->flashdata( 'feedback' ) ) : false ;
		$data['active_class'] 		= 'quote_dashboard';

		$postdata['account_id'] 	= $this->user->account_id;


		$data['quick_stats']		= false;
		$request					= $this->ssid_common->api_call( 'quote/quick_stats', $postdata, 'GET' );
		if( !empty( $request->status ) && ( $request->status == 1 ) ){
			$data['quick_stats']	=  ( !empty( $request->quick_stats ) ) ? $request->quick_stats : false ;
		}

		
		
		
		$data['quotes']				= false;
		$request					= $this->ssid_common->api_call( 'quote/quotes', $postdata, 'GET' );

		if( !empty( $request->status ) && ( $request->status == 1 ) ){
			$data['quotes']			=  ( !empty( $request->quotes ) ) ? $request->quotes : false ;
		}

		$this->_render_webpage( 'quote/dashboard', $data );
	}


	/*
	*	Function to show the quotes details.
	*/
	public function profile( $quote_id = false, $page = "profile" ){

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->is_admin && !$item_access ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else if( !empty( $quote_id ) ){


				$data['feedback'] 			= !empty( $this->session->flashdata( 'feedback' ) ) ? ( $this->session->flashdata( 'feedback' ) ) : false ;
				$data['active_class'] 		= 'profile';
				
				$postdata['account_id'] 	= $this->user->account_id;
				$postdata['quote_id'] 		= $quote_id;
				
				$data['quote_statuses'] 	= false;
				$request					= $this->ssid_common->api_call( 'quote/quote_statuses', $postdata, $method = 'GET' );
				if( !empty( $request->status ) && ( $request->status == 1 ) ){
					$data['quote_statuses']		= ( !empty( $request->statuses ) ) ? $request->statuses :  false ;
				}

				$data['quote_data']			= false;
				$request					= $this->ssid_common->api_call( 'quote/quotes', $postdata, $method = 'GET' );
				if( !empty( $request->status ) && ( $request->status == 1 ) ){
					$data['quote_data']		= ( !empty( $request->quotes ) ) ? $request->quotes :  false ;
				}

				if( !empty( $data['quote_data']->customer_id ) ){
					$postdata['customer_id'] 	= $data['quote_data']->customer_id;
					$request					= $this->ssid_common->api_call( 'customer/customers', $postdata, $method = 'GET' );

					if( !empty( $request->status ) && ( $request->status == 1 ) ){
						$data['customer_data']	= ( !empty( $request->customers ) ) ? $request->customers :  false ;
					} else {
						$data['customer_data'] 	= false;
					}
				} else {
					$data['customer_data'] 		= false;
				}

				switch( $page ){
					case 'quote_items':

						// $quote_items 		  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/jobs', ['account_id'=>$this->user->account_id,'site_id'=>$site_id], ['auth_token'=>$this->auth_token], true );


						$data['quote_items']= ( isset($quote_items->jobs) ) ? $quote_items->jobs : null;
						$data['include_page'] = 'quote_items.php';
						break;
					case 'customer_details':
						$data['include_page'] = 'quote_items.php';
						break;
					case 'notes':
						$data['include_page'] = 'quote_items.php';
						break;
					case 'review':
						$data['include_page'] = 'quote_items.php';
						break;
					default:
						$data['include_page'] = 'quote_items.php';
						break;
				}


				$this->_render_webpage( 'quote/profile', $data );
		} else {
			redirect( 'webapp/quote/dashboard', 'refresh' );
		}
	}


	/*
	*	Function to add a new quote with some items and link it to the customer. If a new customer required...?
	*/
	public function add_quote(){
		$data['feedback'] 			= !empty( $this->session->flashdata( 'feedback' ) ) ? ( $this->session->flashdata( 'feedback' ) ) : false ;
		$data['active_class'] 		= 'add_quote';
		
		$postdata['account_id'] 	= $this->user->account_id;

		$data['quote_statuses'] 	= false;
		$request					= $this->ssid_common->api_call( 'quote/quote_statuses', $postdata, $method = 'GET' );
		if( !empty( $request->status ) && ( $request->status == 1 ) ){
			$data['quote_statuses']		= ( !empty( $request->statuses ) ) ? $request->statuses :  false ;
		}

		$data['customer_list']		= false;
		$request					= $this->ssid_common->api_call( 'customer/customers', $postdata, $method = 'GET' );
		if( !empty( $request->status ) && ( $request->status == 1 ) ){
			$data['customer_list']		= ( !empty( $request->customers ) ) ? $request->customers :  false ;
		}

		$data['billable_items']	= false;
		$request					= $this->ssid_common->api_call( 'billable_item/items', $postdata, $method = 'GET' );
		if( !empty( $request->status ) && ( $request->status == 1 ) ){
			$data['billable_items']	= ( !empty( $request->items ) ) ? $request->items :  false ;
		}

		$this->_render_webpage( 'quote/add_quote', $data );
	}

	
	/** Get billable items **/
	public function billable_items(){

		if( !$this->identity() ){
			$return_data['message'] = "Access denied! Please login";	
		}
	
		$postdata['account_id'] 	= $this->user->account_id;
		
		$data['billable_items']		= false;
		$request					= $this->ssid_common->api_call( 'billable_item/items', $postdata, $method = 'GET' );
		
		$output = "<select>";
		
		if( !empty( $request->status ) && ( $request->status == 1 ) ){
			$return_data['billable_items'] 	= ( !empty( $request->items ) ) ? $request->items :  false ;

			foreach( $return_data['billable_items'] as $item ) {
				$output .= "<option value=". $item->item_id .">".$item->item_name."</li>";
			}

			$output .= "</ul>";
			echo $output;
			
			
			$return_data['output'] 			= $output;
			$return_data['status'] 			= 1;
			$return_data['status_msg']		= ( !empty( $request->message ) ) ? ( $request->message ) : '' ;
			
		} else {
			$output .= "<li>No Item Found!!</li>";
			$output .= "</ul>";
			echo $output;
			
			$return_data['output'] 			= $output;
			$return_data['billable_items'] 	= false;
			$return_data['status'] 			= 0;
			$return_data['status_msg']		= ( !empty( $request->message ) ) ? ( $request->message ) : '' ;
		}
		
		
		return $return_data;
			 
		
		/* 

		$new_site	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'site/create', $postdata, ['auth_token'=>$this->auth_token] );
		$result		  = ( isset($new_site->site) ) ? $new_site->site : null;
		$message	  = ( isset($new_site->message) ) ? $new_site->message : 'Oops! There was an error processing your request.';  
		if( !empty( $result ) ){
			$return_data['site']   = $new_site;
		}
		$return_data['status_msg'] = $message;
	
		$return_data = [
			'status'=>0
		]; */
				

/* 		print_r( json_encode( $return_data ) );
		die(); */
	}

}