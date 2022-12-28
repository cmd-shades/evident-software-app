<?php

namespace Application\Modules\Web\Controllers;

class Config extends MX_Controller {

	function __construct(){
		parent::__construct();
		if( !$this->identity() ){
			redirect('webapp/user/login', 'refresh');
		}

		$this->module_id 	   = $this->webapp_service->_get_module_id( $this->router->fetch_class() );
		$this->load->library( 'pagination' );
		$this->options = ['auth_token'=>$this->auth_token];		
	}
	
	function configs(){
		$data['message'] = true;
		$this->_render_webpage('configs/index', $data, false, true );
	}
	
	function index(){
		$data['message'] = true;
		$this->_render_webpage('configs/index', $data, false, true );
	}
	
	/*
	* Config Entries List / Search
	*/
	public function config_entries_list( $page = 'details' ){

		$return_data = '';

		$section 	 = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		
		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){
			
			$return_data .= $this->config->item( 'ajax_access_denied' );
			
		}else{

			# Setup search parameters
			$search_term   = ( $this->input->post( 'search_term' ) )? $this->input->post( 'search_term' ) : false;
			$where   	   = ( $this->input->post( 'where' ) ) 		? $this->input->post( 'where' ) : false;
			$limit		   = ( !empty( $where['limit'] ) )  		? $where['limit']  : DEFAULT_LIMIT;
			$start_index   = ( $this->input->post( 'start_index' ) )? $this->input->post( 'start_index' ) : DEFAULT_OFFSET;
			$offset		   = ( !empty( $start_index ) ) 			? ( ( $start_index - 1 ) * $limit ) : 0;
			$order_by	   = ( $this->input->post( 'order_by' ) ) 	? $this->input->post( 'order_by' ) : false;
			
			#prepare postdata
			$postdata = [
				'account_id'	=>$this->user->account_id,
				'search_term'	=>$search_term,
				'where'			=>$where,
				'order_by'		=>$order_by,
				'limit'			=>$limit,
				'offset'		=>$offset
			];

			$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'config/config_data', $postdata, [ 'auth_token'=>$this->auth_token ], true );
			
			$entries		= ( isset( $search_result->config_data ) ) ? $search_result->config_data : null;

			if( !empty( $entries ) ){

				## Create pagination
				$counters 		= ( isset( $search_result->counters ) ) ? $search_result->counters : null;
				$page_number	= ( $start_index > 0 ) ? $start_index : 1;
				$page_display	= '<span style="margin:10px 0px;" class="pull-left">Page <strong>'.$page_number.'</strong> of <strong>'.( !empty( $counters->pages ) ? $counters->pages : "" ).'</strong></span>';
				
				if( !empty( $counters->total ) && ( $counters->total > 0 ) ){
					$config['total_rows'] 	= $counters->total;
					$config['per_page'] 	= $limit;
					$config['current_page'] = $page_number;
					$pagination_setup 		= _pagination_config();
					$config					= array_merge( $config, $pagination_setup ); 
					$this->pagination->initialize( $config );
					$pagination 			= $this->pagination->create_links();
				}

				$return_data = $this->load_config_entries_view( $entries );
				if( !empty( $pagination ) ){
					$return_data .= '<div class="row">';
						$return_data .= $page_display.$pagination;
					$return_data .= '</div>';
				}				
			}else{
				$return_data .= '<div class="row" >';
					$return_data .= '<div class="col-md-12" ><br/>';
						$return_data .= ( isset( $search_result->message ) ) ? $search_result->message : 'No records to display';
					$return_data .= '</div>';
				$return_data .= '</div>';
			}
		}

		print_r( $return_data );
		die();
	}
	
	
	/*
	* Config Entry list views
	*/
	private function load_config_entries_view( $entries_data ){
		$return_data = '';
		if( !empty( $entries_data ) ){
			foreach( $entries_data as $k => $entry ){

				$return_data .= '<div class="col-md-1">';
					$return_data .= '<div>';									
						$return_data .= '<a href="'.base_url( '/webapp/'.$entry->entry_url_link ).'">';
							$return_data .= '<div class="x_panel no-border module-item bg-transparent">';
								$return_data .= '<img style="width:100%" src="'.base_url( $entry->entry_img_url ).'" />';
								$return_data .= '<h6 class="text-grey"><em>';
									$return_data .= strtoupper( $entry->entry_name );
									#$return_data .= ( ( $this->user->is_admin && ( in_array( $this->user->id, $this->super_admin_list ) ) ) ? '&nbsp;&nbsp;<span class="eidt-entry" data-entry_id="'.$entry->entry_id.'" ><i class="fas fa-edit" title="Edit Entry" ></i></span>' : '' );
								$return_data .= '</em></h6>';
							$return_data .= '</div>';
						$return_data .= '</a>';
					$return_data .= '</div>';
				$return_data .= '</div>';
			}

			if( !empty( $pagination ) ){
				$return_data .= '<div class="row"><div class="col-md-12">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</div></div>';
			}
			
		}else{
			$return_data .= '<div class="col-md-12" ><div class="col-md-12"><br/>'.$this->config->item( 'no_records' ).'</div></div>';
		}
		return $return_data;
	}


	/** 
	* Create new Config Entry
	*/
	public function add_config_entry(){

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$return_data = [
			'status'=>0
		];

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		} else {
			$postdata 	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$new_entry 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'config/add_config_entry', $postdata, ['auth_token'=>$this->auth_token] );
			$result		= ( isset( $new_entry->config_entry ) ) ? $new_entry->config_entry : null;
			$message	= ( isset( $new_entry->message ) ) ? $new_entry->message : 'Oops! There was an error processing your request.';

			if( !empty( $result ) ){
				$return_data['status'] 			= 1;
				$return_data['config_entry'] 	= $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	

	//Unveried Addresses - Overview page
	function unverified_addresses( $main_address_id = false, $page = 'details' ){

		$toggled		 = ( !empty( $this->input->get( 'toggled' ) ) ? $this->input->get( 'toggled' ) : false );
		$section 		 = ( !empty( $page) ) ? $page : ( !empty( $this->input->get( 'page' ) ) ? $this->input->get( 'page' ) : 'details' );
		$main_address_id = ( !empty( $main_address_id) ) ? $main_address_id : ( !empty( $this->input->get( 'main_address_id' ) ) ? $this->input->get( 'main_address_id' ) : ( ( !empty( $this->input->get( 'main_address_id' ) ) ? $this->input->get( 'main_address_id' ) : null ) ) );
		
		if( !empty( $main_address_id ) ){

			$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
			if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
				$this->_render_webpage( 'errors/access-denied', false );
			}else{
				$default_params 				= $params = [ 'account_id'=>$this->user->account_id, 'where'=>[ 'main_address_id'=>$main_address_id ] ];
				$unverified_address_details 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'address/unverified_addresses', $params, [ 'auth_token'=>$this->auth_token ], true );

				if( !empty( $unverified_address_details->unverified_addresses ) ){
					$data['unverified_address_details']	= $unverified_address_details->unverified_addresses;
					$data['linked_job_types']			= false;

					$this->_render_webpage( 'configs/addresses/unverified_address_profile', $data );					
				}else{
					redirect( 'webapp/configs/unverified_addresses', 'refresh' );
				}
			}
		} else {
			
			$this->_render_webpage( 'configs/addresses/unverified_addresses_manage', false, false, true );
		}
	}
	
	
	/*
	* Unveried Addresses List / Search
	*/
	public function unverified_addresses_list( $page = 'details' ){

		$return_data = '';

		$section 	 = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		
		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){
			
			$return_data .= $this->config->item( 'ajax_access_denied' );
			
		}else{

			# Setup search parameters
			$search_term   = ( $this->input->post( 'search_term' ) )? $this->input->post( 'search_term' ) : false;
			$where   	   = ( $this->input->post( 'where' ) ) 		? $this->input->post( 'where' ) : false;
			$limit		   = ( !empty( $where['limit'] ) )  		? $where['limit']  : DEFAULT_LIMIT;
			$start_index   = ( $this->input->post( 'start_index' ) )? $this->input->post( 'start_index' ) : DEFAULT_OFFSET;
			$offset		   = ( !empty( $start_index ) ) 			? ( ( $start_index - 1 ) * $limit ) : 0;
			$order_by	   = ( $this->input->post( 'order_by' ) ) 	? $this->input->post( 'order_by' ) : false;
			
			#prepare postdata
			$postdata = [
				'account_id'	=>$this->user->account_id,
				'search_term'	=>$search_term,
				'where'			=>$where,
				'order_by'		=>$order_by,
				'limit'			=>$limit,
				'offset'		=>$offset
			];

			$search_result		= $this->webapp_service->api_dispatcher( $this->api_end_point.'address/unverified_addresses', $postdata, [ 'auth_token'=>$this->auth_token ], true );
			
			$unverified_addresses		= ( isset( $search_result->unverified_addresses ) ) ? $search_result->unverified_addresses : null;

			if( !empty( $unverified_addresses ) ){

				## Create pagination
				$counters 		= ( isset( $search_result->counters ) ) ? $search_result->counters : null;
				$page_number	= ( $start_index > 0 ) ? $start_index : 1;
				$page_display	= '<span style="margin:15px 0px;" class="pull-left">Page <strong>'.$page_number.'</strong> of <strong>'.( !empty( $counters->pages ) ? $counters->pages : "" ).'</strong></span>';
				
				if( !empty( $counters->total ) && ( $counters->total > 0 ) ){
					$config['total_rows'] 	= $counters->total;
					$config['per_page'] 	= $limit;
					$config['current_page'] = $page_number;
					$pagination_setup 		= _pagination_config();
					$config					= array_merge( $config, $pagination_setup ); 
					$this->pagination->initialize( $config );
					$pagination 			= $this->pagination->create_links();
				}
				
				$return_data = $this->load_unverified_addresses_view( $unverified_addresses );
				if( !empty( $pagination ) ){
					$return_data .= '<tr><td colspan="5" style="padding: 0;">';
						$return_data .= $page_display.$pagination;
					$return_data .= '</td></tr>';
				}				
			}else{
				$return_data .= '<tr><td colspan="5" style="padding: 0 8px;"><br/>';
					$return_data .= ( isset( $search_result->message ) ) ? $search_result->message : 'No records found';
				$return_data .= '</td></tr>';
			}
		}

		print_r( $return_data );
		die();
	}
	
	/*
	* Unveried Address list views
	*/
	private function load_unverified_addresses_view( $unverified_addresses_data ){
		$return_data = '';
		if( !empty( $unverified_addresses_data ) ){
			
			foreach( $unverified_addresses_data as $k => $address ){

				$return_data .= '<tr>';
					$return_data .= '<td><a href="'.base_url( '/webapp/config/unverified_addresses/'.$address->main_address_id ).'" >'.$address->addressline1.'</a></td>';									
					$return_data .= '<td>'.$address->addressline2.'</td>';									
					$return_data .= '<td>'.( !empty( $address->posttown ) ? $address->posttown : '' ).'</td>';									
					$return_data .= '<td>'.( !empty( $address->postcode ) ? $address->postcode : '' ).'</td>';									
					$return_data .= '<td><span class="pull-right" >'.( !empty( $address->verified ) ? 'Verified' : 'Unverified' ).'</span></td>';													
				$return_data .= '</tr>';
			}

			if( !empty( $pagination ) ){
				$return_data .= '<tr><td colspan="5" style="padding: 0;">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
		}else{
			$return_data .= '<tr><td colspan="5"><br/>'.$this->config->item( 'no_records' ).'</td></tr>';
		}
		return $return_data;
	}
	
	
	/*
	* Add New Unveried Address
	*/
	public function new_unverified_address( $page = 'details' ){

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		}else{
			$this->_render_webpage( 'configs/addresses/unverified_address_create', $data = false );
		}
	}
	
	
	/** 
	* Add a new Unverified Address
	**/
	public function add_unverified_address(){

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$text_color  = 'red';
		$return_data = [
			'status'=>0
		];

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata 	  	 = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$unverified_address 	 = $this->webapp_service->api_dispatcher( $this->api_end_point.'address/add_unverified_address', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  	 = ( isset( $unverified_address->unverified_address ) ) ? $unverified_address->unverified_address : null;
			$message	  	 = ( isset( $unverified_address->message ) )  ? $unverified_address->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 	 			= 1;
				$return_data['unverified_address']	= $result;
				$text_color 			 			= 'auto';
			}
			$return_data['status_msg'] = '<span class="text-'.$text_color.'">'.$message.'</span>';
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/** Update Unverified Address Profile Details **/
	public function update_unverified_address( $main_address_id = false, $page = 'details' ){
		$return_data = [
			'status'=>0
		];
		
		$section 	= ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
		
		$main_address_id = ( $this->input->post( 'main_address_id' ) ) ? $this->input->post( 'main_address_id' ) : ( !empty( $main_address_id ) ? $main_address_id : null );
		
		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';	
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$update_unverified_address= $this->webapp_service->api_dispatcher( $this->api_end_point.'address/update_unverified_address', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  = ( isset( $update_unverified_address->unverified_address ) )   ? $update_unverified_address->unverified_address : null;
			$message	  = ( isset( $update_unverified_address->message ) ) ? $update_unverified_address->message : 'Oops! There was an error processing your request.';  
			if( !empty( $result ) ){
				$return_data['status'] 				= 1;
				$return_data['unverified_address'] 	= $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();	
	}
	
	
	/**
	* Delete Unverified Address Record
	**/
	public function delete_unverified_address( $main_address_id = false ){
		$return_data = [
			'status'=>0
		];

		$section 		 = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$main_address_id = ( $this->input->post( 'main_address_id' ) ) ? $this->input->post( 'main_address_id' ) : ( !empty( $main_address_id ) ? $main_address_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_delete ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	  		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$delete_unverified_address_item 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'address/delete_unverified_address', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  		= ( isset( $delete_unverified_address_item->status ) )  ? $delete_unverified_address_item->status  : null;
			$message	  		= ( isset( $delete_unverified_address_item->message ) ) ? $delete_unverified_address_item->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
}
	