<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Channel extends MX_Controller {

	function __construct(){
		parent::__construct();
		
		if( false === $this->identity() ){
			redirect( "webapp/user/login", 'refresh' );
		}
		$this->module_id 	   = $this->webapp_service->_get_module_id( $this->router->fetch_class() );
		$this->load->model( "serviceapp/Channel_model", "channel_service" );
		$this->load->library( 'pagination' );
	}
	
	
	public function index( $channel_id = false ){
		if( $channel_id ){
			redirect( 'webapp/channel/profile/'.$channel_id, 'refresh');
		}

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'details' );
				
		if( !$this->user->is_admin && !$module_access ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			$data['current_user']			= $this->user;
			$data['module_id']				= $this->module_id;

			$this->_render_webpage( 'channel/index', $data );
		}
	}
	
	
	/** 
	*	Create new Channel - not confirmed
	**/
	public function create(){

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'details' );

		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			$data = false;
			
			$data['providers']			= $postdata	= [];
			$postdata['account_id']		= $this->user->account_id;
			$url						= 'provider/provider';
			$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
			$data['providers']			= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->content_provider ) ) ? $API_result->content_provider : null;
			
			$data['territories']		= $postdata = [];
			$postdata['account_id']		= $this->user->account_id;
			$url						= 'content/territories';
			$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
			$data['territories']		= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->territories ) ) ? $API_result->territories : null;

			$this->_render_webpage( 'channel/create', $data );
		}
	}
	
	
	public function create_channel( $page = "details" ){
	
		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
		$return_data = [
			'status' => 0
		];

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$post_data = $this->input->post();
			if( !empty( $post_data ) && !empty( $post_data['channel_name'] ) ){

				$postdata					= [];
				$postdata['channel_data']	= $post_data;
				$postdata['account_id']		= $this->user->account_id;
				$postdata['channel_name']	= $post_data['channel_name'];
				$url						= 'channel/create';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( !empty( $API_result ) ){
					$return_data['channel']		= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->channel ) ) ? $API_result->channel : null;
					$return_data['status'] 		= ( isset( $API_result->status ) ) ? $API_result->status : false ;
					$return_data['status_msg'] 	= ( isset( $API_result->message ) ) ? $API_result->message : 'Request completed!';
				} else {
					$return_data['status_msg'] 	= ( isset( $API_result->message ) ) ? $API_result->message : 'Request completed but unsuccessful!';
				}
			} else {
				$return_data['status_msg'] = "No data submitted;";
			}
		}
		
		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	//View channel profile
	function profile( $channel_id = false, $page = 'details' ){

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		## Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else if( $channel_id ){
			$run_admin_check 		 				= false;
			
			$data['channel_details']				= $postdata = $API_result = [];
			$postdata['account_id']					= $this->user->account_id;
			$postdata['where']['channel_id']		= $channel_id;
			$url									= 'channel/channel';
			$API_result								= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
			$data['channel_details']				= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->channel ) ) ? $API_result->channel : null;
			
			
			if( !empty( $data['channel_details'] ) ){
				## Get allowed access for the logged in user
				$data['permissions']= $item_access;
				$data['active_tab']	= $page;
				
				$module_items 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'access/account_modules_items', ['account_id'=>$this->user->account_id, 'module_id'=>$this->module_id ], ['auth_token'=>$this->auth_token], true );
				$data['module_tabs']= ( isset( $module_items->module_items ) ) ? $module_items->module_items : null;

				switch( $page ){
					case 'details':
					default:
						$data['territories']					= $postdata = $API_result = [];
						$postdata['account_id']					= $this->user->account_id;
						$url									= 'content/territories';
						$API_result								= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
						$data['territories']					= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->territories ) ) ? $API_result->territories : null;
						
						$data['remaining_territories']			= $postdata = $API_result = [];
						$postdata['account_id']					= $this->user->account_id;
						$postdata['where']['not_added']			= 'yes';
						$postdata['where']['channel_id'] 		= $channel_id;
						$url									= 'channel/territories';
						$API_result								= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
						$data['remaining_territories']			= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->territories ) ) ? $API_result->territories : null;
						
						$data['providers']						= $postdata	= [];
						$postdata['account_id']					= $this->user->account_id;
						$url									= 'provider/provider';
						$API_result								= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
						$data['providers']						= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->content_provider ) ) ? $API_result->content_provider : null;
						
						$channel_documents				= $this->webapp_service->api_dispatcher( $this->api_end_point.'document_handler/document_list', ['account_id'=>$this->user->account_id, 'channel_id'=>$channel_id, 'document_group'=>'channel' ], ['auth_token'=>$this->auth_token], true );
						$data['channel_documents']		= ( isset( $channel_documents->documents->{$this->user->account_id} ) ) ? $channel_documents->documents->{$this->user->account_id} : null;
					
						$data['include_page'] 		= 'channel_details.php';
						break;
				}
			}

			## Run the admin check if tab needs only admin
			if( !empty( $run_admin_check ) ){
				if( ( !admin_check( $this->user->is_admin, false, ( !empty( $data['permissions'] ) ? $data['permissions']->is_admin : false ) ) ) ){
					$data['admin_no_access'] = true;
				}
			}

			$this->_render_webpage( 'channel/profile', $data );
		} else {
			redirect( 'webapp/channel', 'refresh' );
		}
	}
	
	
	
	public function update( $page = "details" ){
	
		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
		$return_data = [
			'status' => 0
		];

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {

			$post_data = $this->input->post();
			
			if( !empty( $post_data ) && !empty( $post_data['channel_id'] ) ){

				$postdata						= [];
				$postdata['account_id']			= $this->user->account_id;
				$postdata['channel_data'] 		= $post_data['channel_details'];
				$postdata['channel_id'] 		= ( !empty( $post_data['channel_id'] ) ) ? $post_data['channel_id'] : false ;

				$url			= 'channel/update';
				$API_result		= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				## u_channel = updated_channel
				if( !empty( $API_result ) ){
					$return_data['u_channel']	= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->u_channel ) ) ? $API_result->u_channel : null;
					$return_data['status'] 		= ( isset( $API_result->status ) ) ? $API_result->status : false ;
					$return_data['status_msg'] 	= ( isset( $API_result->message ) ) ? $API_result->message : 'Request completed!';
				} else {
					$return_data['status_msg'] 	= ( isset( $API_result->message ) ) ? $API_result->message : 'Request completed but unsuccessful!';
				}
			} else {
				$return_data['status_msg'] = "No data submitted;";
			}
		}
		
		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	public function lookup(){
		
		$return_data = '';

		# Check module access
		$section 		= ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access 	= $this->webapp_service->check_access( $this->user, $this->module_id, $section = "details" );

		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data .= $this->config->item( 'ajax_access_denied' );
		} else {

			# Setup search parameters
			$search_term   		= ( $this->input->post( 'search_term' ) ) ? $this->input->post( 'search_term' ) : false;
			$limit		   		= ( $this->input->post( 'limit' ) )  ? $this->input->post( 'limit' )  : DEFAULT_LIMIT;
			$start_index   		= ( $this->input->post( 'start_index' ) )  ? $this->input->post( 'start_index' )  : 0;
			$offset		   		= ( !empty( $start_index ) ) ? ( ( $start_index - 1 ) * $limit ) : 0;
			$order_by	   		= false;
			$where		   		= false;
			
			#prepare post data
			$postdata = [
				'account_id'		=> $this->user->account_id,
				'search_term'		=> $search_term,
				'order_by'			=> $order_by,
				'limit'				=> $limit,
				'offset'			=> $offset,
				'where'				=> $where,
			];
			
			$API_call		= $this->webapp_service->api_dispatcher( $this->api_end_point.'channel/lookup', $postdata, ['auth_token'=>$this->auth_token], true );
			$channel		= ( isset( $API_call->channel ) ) ? $API_call->channel : null;
			
			if( !empty( $channel ) ){

				## Create pagination
				$counters 		= $this->channel_service->get_total_channel( $this->user->account_id, $search_term, $where );
				$page_number	= ( $start_index > 0 ) ? $start_index : 1;
				$page_display	= '<span style="margin:15px 0px;" class="pull-left">Page <strong>'.$page_number.'</strong> of <strong>'.$counters->pages.'</strong></span>';

				if( $counters->total > 0 ){
					$config['total_rows'] 	= $counters->total;
					$config['per_page'] 	= $limit;
					$config['current_page'] = $page_number;
					$pagination_setup 		= _pagination_config();
					$config					= array_merge( $config, $pagination_setup );
					$this->pagination->initialize($config);
					$pagination 			= $this->pagination->create_links();
				}

				$return_data = $this->load_channel_view( $channel );
				if( !empty( $pagination ) ){
					$return_data .= '<tr><td colspan="8" style="padding: 0;">';
						$return_data .= $page_display.$pagination;
					$return_data .= '</td></tr>';
				}
			} else {
				$return_data .= '<tr><td colspan="8">';
				$return_data .= ( isset( $search_result->message ) ) ? $search_result->message : 'No records found';
				$return_data .= '</td></tr>';
			}
		}


		print_r( $return_data );
		die();
	}

	
	/*
	* 	Prepare content view
	*/
	private function load_channel_view( $channel_data = false ){
		$return_data = '';
		
		if( !empty( $channel_data ) ){
			foreach( $channel_data as $k => $row ){
				$return_data .= '<tr>';
					$return_data .= '<td>'.( !empty( $row->channel_id ) ? $row->channel_id : '' ).'</td>';
					$return_data .= '<td><a href="'.base_url( '/webapp/channel/profile/'.$row->channel_id ).'" >'.( !empty( $row->channel_name ) ? $row->channel_name : '' ).'</a></td>';
					$return_data .= '<td>'.( !empty( $row->provider_name ) ? $row->provider_name : '' ).'</td>';
					$return_data .= '<td>'.( validate_date( $row->distribution_start_date ) ? format_date_client( $row->distribution_start_date ) : '' ).'</td>';
					$return_data .= '<td>'.( !empty( $row->is_channel_ott ) ? 'Yes' : 'No' ).'</td>';
				$return_data .= '</tr>';
			}

			if( !empty( $pagination ) ){
				$return_data .= '<tr><td colspan="4" style="padding: 0;">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
		} else {
			$return_data .= '<tr><td colspan="5"><br/>'.$this->config->item( "no_records" ).'</td></tr>';
		}
		return $return_data;
	}
	
	
	public function delete_channel( $page = "details" ){
	
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		
		$return_data = [
			'status' => 0
		];

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = "details" );
		
		if( !$this->user->is_admin && empty( $item_access->can_delete ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$post_data = $this->input->post();

			if( !empty( $post_data['channel_id'] ) ){

				$postdata					= [];
				$postdata['account_id']		= $this->user->account_id;
				$postdata['channel_id'] 	= ( !empty( $post_data['channel_id'] ) ) ? $post_data['channel_id'] : false ;
				
				$url			= 'channel/delete';
				$API_result		= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );
				
				if( !empty( $API_result ) ){
					$return_data['d_channel']	= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->d_channel ) ) ? $API_result->d_channel : null;
					$return_data['status'] 		= ( isset( $API_result->status ) ) ? $API_result->status : false ;
					$return_data['status_msg'] 	= ( isset( $API_result->message ) ) ? $API_result->message : 'Request completed!';
				} else {
					$return_data['status_msg'] 	= ( isset( $API_result->message ) ) ? $API_result->message : 'Request completed but unsuccessful!';
				}
			} else {
				$return_data['status_msg'] = "No data submitted;";
			}
		}
		
		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	
	
	public function add_territory( $page = "details" ){
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		$return_data = [
			'status' => 0
		];

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = "details" );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {

			$post_data = $this->input->post();
			if( !empty( $post_data ) ){

				$postdata['account_id']			= $this->user->account_id;
				$postdata['channel_id']			= ( !empty( $post_data['channel_id'] ) ) ? ( int ) $post_data['channel_id'] : false ;
				$postdata['territories']		= ( !empty( $post_data['territories'] ) ) ? $post_data['territories'] : false ;

				$url			= 'channel/add_territory';
				$API_result		= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );
				
				if( !empty( $API_result->status ) && ( $API_result->status == true ) ){
					$return_data['new_territory']	= ( isset( $API_result->new_territory ) && !empty( $API_result->new_territory ) ) ? $API_result->new_territory : null ;
					$return_data['status'] 			= 1;
					$return_data['status_msg'] 		= ( isset( $API_result->message ) && !empty( $API_result->message ) ) ? $API_result->message : null ;
				} else {
					$return_data['status_msg'] 		= ( !empty( $API_result->message ) ) ? $API_result->message : 'There was an error processing your request';
				}
			} else {
				$return_data['status_msg'] = "No data submitted;";
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/*
	* 	Function to delete a territory entry
	*/
	public function delete_territory( $page = "details" ){
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		$return_data = [
			'status' => 0
		];

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = "details" );

		if( !$this->user->is_admin && empty( $item_access->can_delete ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {

			$post_data = $this->input->post();
			if( !empty( $post_data ) ){

				$postdata['account_id']		= $this->user->account_id;
				$postdata['territory_id']	= ( !empty( $post_data['territory_id'] ) ) ? ( int ) $post_data['territory_id'] : false ;
				$url						= 'channel/delete_territory';

				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				## d_clearance - deleted clearance
				if( !empty( $API_result->status ) && ( $API_result->status == true ) ){
					$return_data['d_territory']		= ( !empty( $API_result->d_territory ) ) ? $API_result->d_territory : null ;
					$return_data['status'] 			= 1;
					$return_data['status_msg'] 		= ( isset( $API_result->message ) && !empty( $API_result->message ) ) ? $API_result->message : null ;
				} else {
					$return_data['status_msg'] 		= ( !empty( $API_result->message ) ) ? $API_result->message : 'There was an error processing your request';
				}
			} else {
				$return_data['status_msg'] = "No data submitted;";
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	
	/** 
	*	Upload channel files
	*/
	public function upload_docs( $channel_id ){

		if( !empty( $channel_id ) ){
			$postdata 	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$doc_upload	= $this->document_service->upload_files( $this->user->account_id, $postdata, $document_group = 'channel', $folder = 'channel' );
			redirect( 'webapp/channel/profile/'.$channel_id );

		} else {
			redirect( 'webapp/channel', 'refresh' );
		}
	}
	
	
	
	public function delete_document( $page = "details" ){
	
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		
		$return_data = [
			'status' => 0
		];

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = "details" );
		
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {

			$post_data = $this->input->post();
			if( !empty( $post_data ) && !empty( $post_data['document_id'] ) ){

				$postdata					= [];
				$postdata['account_id']		= $this->user->account_id;
				$postdata['document_id'] 	= ( !empty( $post_data['document_id'] ) ) ? $post_data['document_id'] : false ;
				$postdata['doc_group'] 		= "channel";
				$url						= 'document_handler/delete_document';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );
				
				## d_document = deleted_document
				if( !empty( $API_result ) ){
					$return_data['d_document']	= ( isset( $API_result->status ) && ( $API_result->status == true ) ) ? $API_result->d_document : null;
					$return_data['status'] 		= ( isset( $API_result->status ) ) ? $API_result->status : false ;
					$return_data['status_msg'] 	= ( isset( $API_result->message ) ) ? $API_result->message : 'Request completed!';
				} else {
					$return_data['status_msg'] 	= ( isset( $API_result->message ) ) ? $API_result->message : 'Request completed but unsuccessful!';
				}
			} else {
				$return_data['status_msg'] = "No data submitted;";
			}
		}
		
		print_r( json_encode( $return_data ) );
		die();
	}
}