<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Provider extends MX_Controller {

	function __construct(){
		parent::__construct();

		if( false === $this->identity() ){
			redirect( "webapp/user/login", 'refresh' );
		}

		$this->module_id 	   = $this->webapp_service->_get_module_id( $this->router->fetch_class() );

		$this->load->model( 'serviceapp/Provider_model', 'provider_service' );
		$this->load->library( 'pagination' );
	}


	public function index( $provider_id = false ){
		if( $provider_id ){
			redirect( 'webapp/provider/profile/'.$provider_id, 'refresh');
		}

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'details' );

		if( !$this->user->is_admin && !$module_access ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			$data['current_user']			= $this->user;
			$data['module_id']				= $this->module_id;

			$data['provider_categories']			= $postdata	= [];
			$postdata['account_id']					= $this->user->account_id;
			$postdata['where']['module_id'] 		= 4; ## Taken from the Provider module
			$postdata['where']['setting_name_id'] 	= 33; ## 'Provider Categories'
			$url									= 'settings/settings';
			$API_result								= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
			$data['provider_categories']			= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->settings ) ) ? $API_result->settings : null;

			$this->_render_webpage( 'provider/index', $data );
		}
	}


	/**
	*	Create new provider
	**/
	public function create(){

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'details' );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			$data = false;

			$data['provider_categories']	= $postdata = [];
			$postdata['account_id']			= $this->user->account_id;
			$url							= 'provider/provider_categories';
			$API_result						= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
			$data['provider_categories']	= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->provider_categories ) ) ? $API_result->provider_categories : null;

			$data['territories']			= $postdata = [];
			$postdata['account_id']			= $this->user->account_id;
			$url							= 'content/territories';
			$API_result						= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
			$data['territories']			= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->territories ) ) ? $API_result->territories : null;

			$this->_render_webpage( 'provider/provider_create', $data );
		}
	}


	public function create_provider( $page = "details" ){

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
			if( !empty( $post_data ) ){

				$postdata				= [];
				$post_data 				= $this->input->post();

				$postdata['provider_data'] 	= $post_data;
				$postdata['account_id']		= $this->user->account_id;

				$url			= 'provider/create';
				$API_result		= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( !empty( $API_result ) ){
					$return_data['provider']		= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->new_provider ) ) ? $API_result->new_provider : null;
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


	//View Provider profile
	function profile( $provider_id = false, $page = 'details' ){

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		## Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else if( $provider_id ){
			$run_admin_check 		 	= false;
			$API_call		 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'provider/provider', ['account_id'=>$this->user->account_id,'provider_id'=>$provider_id], ['auth_token'=>$this->auth_token], true );
			$data['provider_details']	= ( isset( $API_call->content_provider ) ) ? $API_call->content_provider : null;
			if( !empty( $data['provider_details'] ) ){
				## Get allowed access for the logged in user
				$data['permissions']= $item_access;
				$data['active_tab']	= $page;

				$module_items 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'access/account_modules_items', ['account_id'=>$this->user->account_id, 'module_id'=>$this->module_id ], ['auth_token'=>$this->auth_token], true );
				$data['module_tabs']= ( isset( $module_items->module_items ) ) ? $module_items->module_items : null;

				switch( $page ){
					case 'details':
					default:
						$data['territories']					= $postdata = $API_result = [];

						$data['provider_categories']			= $postdata	= [];
						$postdata['account_id']					= $this->user->account_id;
						$postdata['where']['module_id'] 		= 4; ## Taken from the Site module, but also created own
						$postdata['where']['setting_name_id'] 	= 33; ## 'Provider Categories'
						$url									= 'settings/settings';
						$API_result								= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
						$data['provider_categories']			= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->settings ) ) ? $API_result->settings : null;


						$data['territories']					= $postdata = $API_result = [];
						$postdata['account_id']					= $this->user->account_id;
						$url									= 'content/territories';
						$API_result								= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
						$data['territories']					= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->territories ) ) ? $API_result->territories : null;

						$data['remaining_territories']			= $postdata = $API_result = [];
						$postdata['account_id']					= $this->user->account_id;
						$postdata['where']['not_added']			= 'yes';
						$postdata['where']['provider_id'] 	= $provider_id;
						$url									= 'provider/territories';
						$API_result								= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
						$data['remaining_territories']			= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->territories ) ) ? $API_result->territories : null;

						$data['provider_documents']		= $postdata = $API_result = [];
						$postdata['account_id']			= $this->user->account_id;
						$postdata['provider_id']		= $provider_id;
						$postdata['document_group']		= 'provider';
						$url							= 'document_handler/document_list';
						$API_result						= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
						$data['provider_documents']		= ( isset( $API_result->documents->{$this->user->account_id} ) ) ? $API_result->documents->{$this->user->account_id} : null;


						## packets identifiers already assigned to the provider
						$data['provider_packet_identifiers']		= $postdata = $API_result = [];
						$postdata['account_id']			= $this->user->account_id;
						$postdata['where']['provider_id']		= $provider_id;
						$url							= 'provider/provider_packet_identifiers';
						$API_result						= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
						$data['provider_packet_identifiers']		= ( isset( $API_result->identifiers ) ) ? $API_result->identifiers : null;

						## packet/codec identifier adding
						## - get definition(s)
						$data['definitions']			= $postdata = $API_result = [];
						$postdata['account_id']			= $this->user->account_id;
						$url							= 'provider/definition';
						$API_result						= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
						$data['definitions']			= ( isset( $API_result->definition ) ) ? $API_result->definition : null;

						## - get language(s)
						$data['language_phrases']		= $postdata = [];
						$postdata['account_id']			= $this->user->account_id;
						$url							= 'content/phrase_languages';
						$API_result						= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
						$data['language_phrases']		= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->phrase_languages ) ) ? $API_result->phrase_languages : null;

						## - get codec type(s)
						$data['codec_types']			= $postdata = [];
						$postdata['account_id']			= $this->user->account_id;
						$url							= 'provider/codec_type';
						$API_result						= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
						$data['codec_types']			= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->type ) ) ? $API_result->type : null;

						## - get codec name(s)
						$data['codec_names']		= $postdata = [];
						$postdata['account_id']		= $this->user->account_id;
						$url						= 'provider/codec_name';
						$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
						$data['codec_names']		= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->name ) ) ? $API_result->name : null;

						## - get all channel(s)
						$data['channels']			= $postdata = [];
						$postdata['account_id']		= $this->user->account_id;
						$url						= 'channel/channel';
						$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
						$data['channels']			= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->channel ) ) ? $API_result->channel : null;
						
						## - get price plan(s)
						$data['price_plans']		= $postdata = [];
						$postdata['account_id']		= $this->user->account_id;
						$postdata['where']['provider_id']		= $provider_id;
						$url						= 'provider/provider_price_plan';
						$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
						$data['price_plans']		= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->price_plan ) ) ? $API_result->price_plan : null;
						
						## - get Royalty report setting(s)
						$data['royalty_settings']	= $postdata = [];
						$postdata['account_id']		= $this->user->account_id;
						$postdata['where']['provider_id']			= $provider_id;
						$url						= 'report/settings_value';
						$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
						$data['royalty_settings']	= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->settings_value ) ) ? $API_result->settings_value : null;
						
						$data['report_category_id']	= 1; ## royalty reports
						$data['report_type_id']		= 1; ## royalty reports by the provider

						$data['include_page'] 			= 'provider_details.php';
						break;
				}
			}

			## Run the admin check if tab needs only admin
			if( !empty( $run_admin_check ) ){
				if( ( !admin_check( $this->user->is_admin, false, ( !empty( $data['permissions'] ) ? $data['permissions']->is_admin : false ) ) ) ){
					$data['admin_no_access'] = true;
				}
			}

			$this->_render_webpage( 'provider/profile', $data );
		} else {
			redirect( 'webapp/provider', 'refresh' );
		}
	}

	/*
	* 	Function to delete a clearance entry
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
				$url						= 'provider/delete_territory';

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

			if( !empty( $post_data ) && !empty( $post_data['provider_id'] ) ){

				$postdata					= [];
				$postdata['account_id']		= $this->user->account_id;
				$postdata['provider_data'] 	= $post_data;
				$postdata['provider_id'] 	= ( !empty( $post_data['provider_id'] ) ) ? $post_data['provider_id'] : false ;

				$url			= 'provider/update';
				$API_result		= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				## u_provider = updated_provider
				if( !empty( $API_result ) ){
					$return_data['u_provider']	= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->u_provider ) ) ? $API_result->u_provider : null;
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

			if( !empty( $this->input->post( 'category_id' ) ) ){
				$where['category_id'] = $this->input->post( 'category_id' );
			}

			#prepare postdata
			$postdata = [
				'account_id'		=> $this->user->account_id,
				'search_term'		=> $search_term,
				'order_by'			=> $order_by,
				'limit'				=> $limit,
				'offset'			=> $offset,
				'where'				=> $where,
			];

			if( !empty( $this->input->post( 'filter[category_id]' ) ) ){
				$postdata['where'] = [
					'category_id' => $this->input->post( 'filter[category_id]' ),
				];
			}

			$API_call	= $this->webapp_service->api_dispatcher( $this->api_end_point.'provider/lookup', $postdata, ['auth_token'=>$this->auth_token], true );
			$provider	= ( isset( $API_call->provider ) ) ? $API_call->provider : null;

			if( !empty( $provider ) ){

				## Create pagination
				$counters 		= $this->provider_service->get_total_provider( $this->user->account_id, $search_term, $where, $order_by, $limit, $offset );//Direct access to count, this should only return a number
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

				$return_data = $this->load_provider_view( $provider );
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
	private function load_provider_view( $provider_data = false ){
		$return_data = '';

		if( !empty( $provider_data ) ){
			foreach( $provider_data as $k => $row ){
				$return_data .= '<tr>';
					$return_data .= '<td>'.( !empty( $row->provider_id ) ? $row->provider_id : '' ).'</td>';
					$return_data .= '<td><a href="'.base_url( '/webapp/provider/profile/'.$row->provider_id ).'" >'.( !empty( $row->provider_name ) ? $row->provider_name : '' ).'</a></td>';
					$return_data .= '<td>'.( !empty( $row->provider_description ) ? $row->provider_description : '' ).'</td>';
					$return_data .= '<td>'.( !empty( $row->provider_category_name ) ? $row->provider_category_name : '' ).'</td>';
					$return_data .= '<td>'.( validate_date( $row->last_modified_date ) ? format_date_client( $row->last_modified_date ) : '' ).'</td>';
				$return_data .= '</tr>';
			}

			if( !empty( $pagination ) ){
				$return_data .= '<tr><td colspan="4" style="padding: 0;">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
		} else {
			$return_data .= '<tr><td colspan="8"><br/>'.$this->config->item( "no_records" ).'</td></tr>';
		}
		return $return_data;
	}



	public function delete_provider( $page = "details" ){

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

			if( !empty( $post_data ) && !empty( $post_data['provider_id'] ) ){

				$postdata					= [];
				$postdata['account_id']		= $this->user->account_id;
				$postdata['provider_id'] 	= ( !empty( $post_data['provider_id'] ) ) ? $post_data['provider_id'] : false ;

				$url			= 'provider/delete';
				$API_result		= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				## d_provider = deleted_provider
				if( !empty( $API_result ) ){
					$return_data['d_provider']	= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->d_provider ) ) ? $API_result->d_provider : null;
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


	public function check_reference( $page = "details" ){
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		$return_data = [
			'status' => 0
		];

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = "details" );

		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {

			$post_data = $this->input->post();
			if( !empty( $post_data ) ){

				$account_id	= $this->user->account_id;
				$reference 	=( !empty(  $post_data['reference'] ) ) ?  $post_data['reference'] : false ;
				$module 	=( !empty(  $post_data['module'] ) ) ?  $post_data['module'] : false ;

				$reference_exists		= $this->ssid_common->check_reference( $account_id, $reference, $module );

				if( !empty( $reference_exists ) ){
					$return_data['reference']	= ( isset( $reference_exists ) && !empty( $reference_exists ) ) ? $reference_exists : null;
					$return_data['status'] 		= true;
					$return_data['status_msg'] 	= "The Reference code already exists";
				} else {
					$return_data['status_msg'] 	= "This Reference Code seems to be unique";
				}
			} else {
				$return_data['status_msg'] = "No data submitted;";
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/**
	*	Upload Provider files
	*/
	public function upload_docs( $provider_id ){

		if( !empty( $provider_id ) ){
			$postdata 	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$doc_upload	= $this->document_service->upload_files( $this->user->account_id, $postdata, $document_group = 'provider', $folder = 'provider' );
			redirect( 'webapp/provider/profile/'.$provider_id );

		} else {
			redirect( 'webapp/provider', 'refresh' );
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

		if( !$this->user->is_admin && empty( $item_access->can_delete ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {

			$post_data = $this->input->post();
			if( !empty( $post_data ) && !empty( $post_data['document_id'] ) ){

				$postdata					= [];
				$postdata['account_id']		= $this->user->account_id;
				$postdata['document_id'] 	= ( !empty( $post_data['document_id'] ) ) ? $post_data['document_id'] : false ;
				$postdata['doc_group'] 		= "provider";
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

				$postdata['account_id']				= $this->user->account_id;
				$postdata['provider_id']			= ( !empty( $post_data['provider_id'] ) ) ? ( int ) $post_data['provider_id'] : false ;
				$postdata['territories']			= ( !empty( $post_data['territories'] ) ) ? $post_data['territories'] : false ;

				$url			= 'provider/add_territory';
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


	public function packet_identifiers( $page = "details" ){
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		$return_data = [
			'status' => 0
		];

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = "details" );

		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {

			$post_data = $this->input->post();

			if( !empty( $post_data ) ){

				$postdata['account_id']						= $this->user->account_id;

				if( !empty( $post_data['definition_id'] && ( (int) $post_data['definition_id'] > 0 ) ) ){
					$postdata['where']['definition_id']		=  ( int ) $post_data['definition_id'] ;
				}

				if( !empty( $post_data['type_id'] ) && ( (int) $post_data['type_id'] > 0 ) ){
					$postdata['where']['type_id']			=  ( int ) $post_data['type_id'] ;
				}

				$url			= 'provider/packet_identifiers';
				$API_result		= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );

				if( !empty( $API_result->status ) && ( $API_result->status == true ) ){
					$return_data['options']			= ( isset( $API_result->identifiers ) && !empty( $API_result->identifiers ) ) ? $this->load_packet_identifiers_view( $API_result->identifiers ) : null ;
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


	private function load_packet_identifiers_view( $data ){
		$result = '<option value="">Please select</option>';
		if( !empty( $data ) ){
			foreach( $data as $pi_row ){
				$result .= '<option value="'.$pi_row->identifier_id.'">';
				$result .= ( ( !empty( $pi_row->definition_name ) ) ? ucwords( html_escape( $pi_row->definition_name ) ) : '' );
				$result .= ( ( !empty( $pi_row->type_name ) ) ? " | ".ucwords( html_escape( $pi_row->type_name ) ) : '' );
				$result .= ( ( !empty( $pi_row->identifier_name ) ) ? " | ".ucwords( html_escape( $pi_row->identifier_name ) ) : '' );
				/* $result .= ( ( !empty( $pi_row->language_name ) ) ? " | ".ucwords( html_escape( $pi_row->language_name ) ) : '' ); */
				$result .= ( ( !empty( $pi_row->is_adult ) && ( $pi_row->is_adult > 0 ) ) ? ' (Adult)' : '' );
				$result .= '</option>';
			}
		}

		return $result;
	}




	public function add_pid_to_provider( $page = "details" ){
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

			if( !empty( $post_data ) && !empty( $post_data['provider_id'] ) && ( ( int ) $post_data['provider_id'] > 0 ) ){

				$postdata['dataset'] 		= $post_data;
				$postdata['account_id']		= $this->user->account_id;
				$postdata['provider_id']	= ( int ) $post_data['provider_id'] ;

				$url			= 'provider/add_identifier_to_provider';
				$API_result		= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( !empty( $API_result->status ) && ( $API_result->status == true ) ){
					$return_data['added_identifier']	= ( isset( $API_result->added_identifier ) && !empty( $API_result->added_identifier ) ) ? $API_result->added_identifier : null ;
					$return_data['status'] 				= 1;
					$return_data['status_msg'] 			= ( isset( $API_result->message ) && !empty( $API_result->message ) ) ? $API_result->message : null ;
				} else {
					$return_data['status_msg'] 			= ( !empty( $API_result->message ) ) ? $API_result->message : 'There was an error processing your request';
				}
			} else {
				$return_data['status_msg'] = "Incomplete data set submitted;";
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}



	public function edit_pid_modal( $page = "details" ){
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		$return_data = [
			'status' => 0
		];

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = "details" );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {

			$post_data = $this->input->post();

			if( !empty( $post_data ) && !empty( $post_data['identifier_id'] ) && ( ( int ) $post_data['identifier_id'] > 0 ) ){

				$postdata['account_id']				= $this->user->account_id;
				$postdata['where']['identifier_id']	= ( int ) $post_data['identifier_id'] ;
				$url								= 'provider/provider_packet_identifiers';
				$API_result							= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );

				if( !empty( $API_result->status ) && ( $API_result->status == true ) ){
					$return_data['identifier']		= ( isset( $API_result->identifiers ) && !empty( $API_result->identifiers ) ) ? $this->load_identifier_edit_modal( $API_result->identifiers[0] ) : null ;
					$return_data['status'] 			= 1;
					$return_data['status_msg'] 		= ( isset( $API_result->message ) && !empty( $API_result->message ) ) ? $API_result->message : null ;
				} else {
					$return_data['status_msg'] 		= ( !empty( $API_result->message ) ) ? $API_result->message : 'There was an error processing your request';
				}
			} else {
				$return_data['status_msg'] = "Incomplete data set submitted;";
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	private function load_identifier_edit_modal( $data ){
		$result = false;
		if( !empty( $data ) ){
			$return_data = '';


			## packets identifiers already assigned to the provider
			$packet_identifiers			= $postdata = $API_result = [];
			$postdata['account_id']		= $this->user->account_id;
			$url						= 'provider/packet_identifiers';
			$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
			$packet_identifiers			= ( isset( $API_result->identifiers ) ) ? $API_result->identifiers : null;


			## ****  This section is for dynamic filter when editing the Packet Identifier - future functionality ****
			## - get definition(s)
			/* $definitions					= $postdata = $API_result = [];
			$postdata['account_id']			= $this->user->account_id;
			$url							= 'provider/definition';
			$API_result						= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
			$definitions					= ( isset( $API_result->definition ) ) ? $API_result->definition : null; */

			## - get codec type(s)
			/* $codec_types					= $postdata = [];
			$postdata['account_id']			= $this->user->account_id;
			$url							= 'provider/codec_type';
			$API_result						= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
			$codec_types					= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->type ) ) ? $API_result->type : null; */

			## - get codec name(s)
			/* $codec_names				= $postdata = [];
			$postdata['account_id']		= $this->user->account_id;
			$url						= 'provider/codec_name';
			$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
			$codec_names				= ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->name ) ) ? $API_result->name : null; */

			$return_data .= '<input type="hidden" name="identifier_id" value="'.( ( !empty( $data->identifier_id ) ) ? ( int ) $data->identifier_id : '' ).'" />';

			/*  **** This is future functionality - needed for filters for editing the Packet identifier (not finished yet) ****

			$return_data .= '<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><label class="input-label">Definition</label>';
			$return_data .=' <select name="definition_id" class="input-field" title="Video Definition"><option value="">Please select</option>';
			if( !empty( $definitions ) ){
				foreach( $definitions as $def_row ){
					$return_data .= '<option value="'.( ( !empty( $def_row->definition_id ) ) ? $def_row->definition_id : '' ).'">'.( ( !empty( $def_row->definition_name ) ) ? ucwords( html_escape( $def_row->definition_name ) ) : '' ).'</option>';
				}
			}
			$return_data .= '</select>';
			$return_data .= '</div>';

			$return_data .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><label class="input-label">Packet Type</label>';
			$return_data .= '<select name="type_id" class="input-field" title="Packet Type"><option value="">Please select</option>';
			if( !empty( $codec_types ) ){
				foreach( $codec_types as $t_row ){
					$return_data .= '<option value="'.( ( !empty( $t_row->type_id ) ) ? $t_row->type_id : '' ).'">'.( ( !empty( $t_row->type_name ) ) ? ucwords( html_escape( $t_row->type_name ) ) : '' ).'</option>';
				}
			}
			$return_data .= '</select>';
			$return_data .= '</div>'; */

			$return_data .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><label class="input-label">Packet Identifier</label>';
			$return_data .= '<select name="packet_identifier_id" class="input-field" title="Packet Identifier (PID)"><option value="">Please select</option>';

			if( !empty( $packet_identifiers ) ){
				foreach( $packet_identifiers as $pi_row ){
					$return_data .= '<option value="'.$pi_row->identifier_id.'"'.( ( !empty( $data->identifier_id ) && ( $data->packet_identifier_id == $pi_row->identifier_id ) ) ? ' selected="selected"' : '' ).'>';
					$return_data .= ( ( !empty( $pi_row->definition_name ) ) ? ucwords( html_escape( $pi_row->definition_name ) ) : '' );
					$return_data .= ( ( !empty( $pi_row->type_name ) ) ? " | ".ucwords( html_escape( $pi_row->type_name ) ) : '' );
					$return_data .= ( ( !empty( $pi_row->identifier_name ) ) ? " | ".ucwords( html_escape( $pi_row->identifier_name ) ) : '' );
					/* $result .= ( ( !empty( $pi_row->language_name ) ) ? " | ".ucwords( html_escape( $pi_row->language_name ) ) : '' ); */
					$return_data .= ( ( !empty( $pi_row->is_adult ) && ( $pi_row->is_adult > 0 ) ) ? ' (Adult)' : '' );
					$return_data .= '</option>';
				}
			}
			$return_data .= '</select>';
			$return_data .= '</div>';

			$return_data .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><label class="input-label">Description</label>';
			$return_data .= '<textarea class="input-field" name="description" placeholder="Description">'.( ( !empty( $data->description ) ) ? html_escape( $data->description ) : ''  ).'</textarea>';
			$return_data .= '</div>';

			$return_data .= '</div>';
			$return_data .= '<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
			$return_data .= '<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
			$return_data .= '<button class="btn btn-block btn-update btn-primary" type="submit" data-content_section="content">Update</button>';
			$return_data .= '</div></div></div></div>';

			$result = $return_data;
		}
		return $result;
	}




	public function update_provider_pid( $page = "details" ){
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		$return_data = [
			'status' => 0
		];

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = "details" );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {

			$post_data = $this->input->post();

			## identifier - taken from 'provider identifier'
			## packet identifier - taken from 'packet identifier'
			if( !empty( $post_data ) && !empty( $post_data['identifier_id'] ) && ( ( int ) $post_data['identifier_id'] > 0 ) ){

				$postdata['dataset'] 		= $post_data;
				$postdata['account_id']		= $this->user->account_id;
				$postdata['identifier_id']	= ( int ) $post_data['identifier_id'] ;

				$url			= 'provider/update_provider_pid';
				$API_result		= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( !empty( $API_result->status ) && ( $API_result->status == true ) ){
					$return_data['updated_identifier']	= ( isset( $API_result->updated_identifier ) && !empty( $API_result->updated_identifier ) ) ? $API_result->updated_identifier : null ;
					$return_data['status'] 				= 1;
					$return_data['status_msg'] 			= ( isset( $API_result->message ) && !empty( $API_result->message ) ) ? $API_result->message : null ;
				} else {
					$return_data['status_msg'] 			= ( !empty( $API_result->message ) ) ? $API_result->message : 'There was an error processing your request';
				}
			} else {
				$return_data['status_msg'] = "Incomplete data set submitted;";
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}



	public function delete_provider_pid( $page = "details" ){
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

			if( !empty( $post_data ) && !empty( $post_data['identifier_id'] ) && ( ( int ) $post_data['identifier_id'] > 0 ) ){

				$postdata['account_id']		= $this->user->account_id;
				$postdata['identifier_id']	= ( int ) $post_data['identifier_id'] ;

				$url			= 'provider/delete_provider_pid';
				$API_result		= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( !empty( $API_result->status ) && ( $API_result->status == true ) ){
					$return_data['deleted_identifier']	= ( isset( $API_result->deleted_identifier ) ) ? $API_result->deleted_identifier : null ;
					$return_data['status'] 				= 1;
					$return_data['status_msg'] 			= ( isset( $API_result->message ) && !empty( $API_result->message ) ) ? $API_result->message : null ;
				} else {
					$return_data['status_msg'] 			= ( !empty( $API_result->message ) ) ? $API_result->message : 'There was an error processing your request';
				}
			} else {
				$return_data['status_msg'] = "Incomplete data set submitted;";
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	public function add_price_plan( $page = "details" ){
		
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		$return_data = [
			'status' => 0
		];

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = "details" );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {

			$price_plan_details = $this->input->post();
			if( ( !empty( $price_plan_details ) ) && ( !empty( $price_plan_details['price_plan_name'] ) ) && ( !empty( $price_plan_details['provider_id'] ) ) ){

				$postdata['account_id']				= $this->user->account_id;
				$postdata['price_plan_details']		= $price_plan_details;
				$postdata['provider_id']			= ( int ) $price_plan_details['provider_id'];

				$url			= 'provider/add_provider_price_plan';
				$API_result		= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( !empty( $API_result->status ) && ( $API_result->status == true ) ){
					$return_data['provider_price_plan']	= ( isset( $API_result->provider_price_plan ) && !empty( $API_result->provider_price_plan ) ) ? $API_result->provider_price_plan : null ;
					$return_data['status'] 				= 1;
					$return_data['status_msg'] 			= ( isset( $API_result->message ) && !empty( $API_result->message ) ) ? $API_result->message : null ;
				} else {
					$return_data['status_msg'] 			= ( !empty( $API_result->message ) ) ? $API_result->message : 'There was an error processing your request';
				}
			} else {
				$return_data['status_msg'] = "No required data submitted;";
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	

	public function delete_provider_price_plan( $page = "details" ){
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

			if( !empty( $post_data ) && !empty( $post_data['provider_plan_id'] ) && ( ( int ) $post_data['provider_plan_id'] > 0 ) ){

				$postdata['account_id']			= $this->user->account_id;
				$postdata['provider_plan_id']	= ( int ) $post_data['provider_plan_id'] ;

				$url			= 'provider/delete_provider_price_plan';
				$API_result		= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( !empty( $API_result->status ) && ( $API_result->status == true ) ){
					$return_data['deleted_price_plan']	= ( isset( $API_result->deleted_price_plan ) ) ? $API_result->deleted_price_plan : null ;
					$return_data['status'] 				= 1;
					$return_data['status_msg'] 			= ( isset( $API_result->message ) && !empty( $API_result->message ) ) ? $API_result->message : null ;
				} else {
					$return_data['status_msg'] 			= ( !empty( $API_result->message ) ) ? $API_result->message : 'There was an error processing your request';
				}
			} else {
				$return_data['status_msg'] = "Incomplete data set submitted;";
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	public function provider_price_plan( $page = "details" ){
		
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		$return_data = [
			'status' => 0
		];

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = "details" );

		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			
			$post_data = $this->input->post();

			$postdata['account_id']				= $this->user->account_id;
			$postdata['where']['provider_id']	= ( !empty( $post_data['provider_id'] ) ) ? ( int ) $post_data['provider_id'] : false ;
			$url								= 'provider/provider_price_plan';
			$API_result							= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
			
			if( !empty( $API_result->status ) && $API_result->status == true ){
				$return_data['provider_price_plan']	= $this->load_price_plan_view( $API_result->price_plan );
			}
			$return_data['status_msg'] 			= ( !empty( $API_result->message ) ) ? $API_result->message : 'There was an error processing your request';
			$return_data['status'] 				= ( !empty( $API_result->status ) ) ? $API_result->status : false;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	private function load_price_plan_view( $provider_price_plan_data = false ){
		$return_data = '';
		
		if( !empty( $provider_price_plan_data ) ){
				$return_data .= '<option value="">Select the Price Plan</option>';
			foreach( $provider_price_plan_data as $plan_row ){
				$return_data .= '<option value="'.$plan_row->plan_id.'">'.$plan_row->price_plan_name.'</option>';
			} 
		} else {
			/* $return_data .= '<option value="">Airtime Plan(s) not found for this Provider</option>'; */
		}
			
		return $return_data;
	}


	public function update_report_settings( $page = "details" ){
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		$return_data = [
			'status' => 0
		];

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = "details" );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$post_data = $this->input->post();

			if( !empty( $post_data ) ){

				$postdata['dataset'] 		= $post_data;
				$postdata['account_id']		= $this->user->account_id;
				$url						= 'report/update_report_settings';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( !empty( $API_result->status ) && ( $API_result->status == true ) ){
					$return_data['report_settings']		= ( isset( $API_result->setting ) && !empty( $API_result->setting ) ) ? $API_result->setting : null ;
					$return_data['status'] 				= 1;
					$return_data['status_msg'] 			= ( isset( $API_result->message ) && !empty( $API_result->message ) ) ? $API_result->message : null ;
				} else {
					$return_data['status_msg'] 			= ( !empty( $API_result->message ) ) ? $API_result->message : 'There was an error processing your request';
				}
			} else {
				$return_data['status_msg'] = "Incomplete data set submitted;";
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
}