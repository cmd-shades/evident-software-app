<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Premises extends MX_Controller {

	function __construct(){
		parent::__construct();

		if( !$this->identity() ){
			redirect('webapp/user/login', 'refresh');
		}

		$this->module_id 	   = $this->webapp_service->_get_module_id( $this->router->fetch_class() );
		$this->load->library('pagination');

		$this->load->model('serviceapp/Premises_model','premises_service');
		$this->priority_ratings 	= [ 'Low', 'Medium', 'High' ];

	}

	//redirect if needed, otherwise display the user list
	function index(){

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );
		if( !$this->user->is_admin && !$module_access ){
			$this->_render_webpage( 'errors/access-denied', false );
		}else{
			redirect('webapp/premises/premises', 'refresh');
		}
	}

	/** Get list of premises **/
	public function premises( $premises_id = false ){

		if( $premises_id ){
			redirect('webapp/premises/profile/'.$premises_id, 'refresh');
		}

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){
			$this->_render_webpage( 'errors/access-denied', false );
		}else{
			$premises_statuses		  		= $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/premises_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['premises_statuses'] 	= ( isset($premises_statuses->premises_statuses) ) ? $premises_statuses->premises_statuses : null;

			$premises_types		 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/premises_types', ['account_id'=>$this->user->account_id, 'limit'=>-1], ['auth_token'=>$this->auth_token], true );
			$data['premises_types'] 		= ( isset( $premises_types->premises_types ) ) ? $premises_types->premises_types : null;

			$premises_categories			= $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/premises_categories', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['premises_categories'] 	= ( isset( $premises_categories->premises_categories ) ) ? $premises_categories->premises_categories : null;


			$this->_render_webpage('premises/index', $data);
		}
	}

	//View Premises profile - V2
	function profile( $premises_id = false, $page = 'details' ){

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else if( $premises_id ){
			$premises_details		   = $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/premises', ['account_id'=>$this->user->account_id,'premises_id'=>$premises_id], ['auth_token'=>$this->auth_token], true );
			$data['premises_details'] = ( isset($premises_details->premises) ) ? $premises_details->premises : null;

			if( !empty( $data['premises_details'] ) ){

				## overview attributes
				$data["overview_attributes"] 	  	= false;
				$postdata["account_id"] = $this->user->account_id;
				$postdata["where"] 		= [
					"module_id"		=> $this->module_id,
					"zone_id"		=> "1",
				];
				$API_call	 	  				= $this->webapp_service->api_dispatcher( $this->api_end_point.'attribute/attributes', $postdata, ['auth_token'=>$this->auth_token], true );
				$data['overview_attributes']	= ( !empty( $API_call->attributes ) ) ? $API_call->attributes : null;
				## overview attributes - end


				$run_admin_check		= false;
				#Get allowed access for the logged in user
				$data['permissions']= $item_access;
				$data['active_tab']	= $page;

				$module_items 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'access/account_modules_items', ['account_id'=>$this->user->account_id, 'module_id'=>$this->module_id ], ['auth_token'=>$this->auth_token], true );
				$data['module_tabs']= ( isset( $module_items->module_items ) ) ? $module_items->module_items : null;

				#$reordered_tabs 		 = reorder_tabs( $data['module_tabs'] );
				#$data['module_tabs'] 	 = ( !empty( $reordered_tabs['module_tabs'] ) ) ? $reordered_tabs['module_tabs'] : $data['module_tabs'];
				$data['more_list_active']= ( !empty( $reordered_tabs['more_list'] ) && in_array( $page, $reordered_tabs['more_list'] )  ) ? true : false;

				switch( $page ){
					case 'evidocs':
					case 'audits':
						$premises_audits	  	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/audits', ['account_id'=>$this->user->account_id, 'premises_id'=>$premises_id ], ['auth_token'=>$this->auth_token], true );
						$data['premises_audits'] = ( isset($premises_audits->audits) ) ? $premises_audits->audits : null;
						$data['include_page'] = 'premises_audits.php';
						break;
					case 'location':

						$data['sites'] 				= $postdata	= [];
						$postdata['account_id']		= $this->user->account_id;
						$postdata['where']['organized'] = 1;
						$api_call		  	  		= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/sites', $postdata, ['auth_token'=>$this->auth_token], true );
						$data['sites'] 				= ( isset( $api_call->sites ) ) ? $api_call->sites : null;

						

						$data['include_page'] 		= 'premises_location.php';
						break;

					case 'jobs':

						$premises_jobs 		  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/jobs', ['account_id'=>$this->user->account_id,'where'=>['premises_id' => $premises_id ] ], ['auth_token'=>$this->auth_token], true );
						$data['premises_jobs']   = ( isset( $premises_jobs->jobs ) ) 		? $premises_jobs->jobs : null;

						$job_types		 	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_types', ['account_id'=>$this->user->account_id, "limit"=>99], ['auth_token'=>$this->auth_token], true );
						$data['job_types'] 	  = ( isset( $job_types->job_types ) ) 		? $job_types->job_types : null;

						$job_statuses		  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_statuses', ['account_id'=>false], ['auth_token'=>$this->auth_token], true );
						$data['job_statuses'] = ( isset( $job_statuses->job_statuses ) ) ? $job_statuses->job_statuses : null;

						$operatives		  	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'user/users', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
						$data['operatives']   = ( isset( $operatives->users ) ) ? $operatives->users : null;

						$data['job_durations']= job_durations();

						$data['include_page'] = 'premises_jobs.php';
						break;

					case 'details':
					default:
						$premises_types	 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/premises_types', ['account_id'=>$this->user->account_id, 'where'=>['grouped'=>1 ], 'limit' =>-1 ], ['auth_token'=>$this->auth_token], true );
						$data['premises_types']	= ( isset($premises_types->premises_types) ) ? $premises_types->premises_types : null;

						$premises_locations		= $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/premises_locations', ['account_id'=>$this->user->account_id, 'limit' =>-1 ], ['auth_token'=>$this->auth_token], true );
						$data['premises_locations']= ( isset($premises_locations->premises_locations) ) ? $premises_locations->premises_locations : null;

						$users		  	  		= $this->webapp_service->api_dispatcher( $this->api_end_point.'user/users', ['account_id'=>$this->user->account_id, 'limit' =>-1 ], ['auth_token'=>$this->auth_token], true );
						$data['users']  	  	= ( isset($users->users) ) ? $users->users : null;

						$sites		  		  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/sites', $postdata, ['auth_token'=>$this->auth_token], true );
						$data['sites'] 			= ( isset( $sites->sites ) ) ? $sites->sites : null;
						
						$data['site_zones']			= $postdata	= [];
						if( !empty( $data['premises_details']->site_id ) ){
							$postdata['account_id']			= $this->user->account_id;
							$postdata['where']['site_id']	= ( !empty( $data['premises_details']->site_id ) ) ? ( int ) $data['premises_details']->site_id : false ;
							$api_call						= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/site_zones', $postdata, ['auth_token'=>$this->auth_token], true );
							$data['site_zones']				= ( !empty( $api_call->site_zones ) ) ? $api_call->site_zones : false ;
						}

						$data['site_locations'] 	= $postdata	= [];
						if( !empty( $data['premises_details']->location_id ) ){
							$postdata['account_id']		= $this->user->account_id;
							$postdata['site_id']		= ( !empty( $data['premises_details']->site_id ) ) ? ( int ) $data['premises_details']->site_id : false ;
							$postdata['where']['location_id']		= ( !empty( $data['premises_details']->location_id ) ) ? ( int ) $data['premises_details']->location_id : false ;
							$api_call					= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/locations', $postdata, ['auth_token'=>$this->auth_token], true );
							$data['site_locations']	= ( !empty( $api_call->site_locations ) ) ? $api_call->site_locations : false ;
						}
						
						$data['include_page'] 	= 'premises_details.php';
						break;
				}
			}

			//Run the admin check if tab needs only admin
			if( !empty( $run_admin_check ) ){
				if( ( !admin_check( $this->user->is_admin, false, ( !empty( $data['permissions'] ) ? $data['permissions']->is_admin : false ) ) ) ){
					$data['admin_no_access'] = true;
				}
			}

			$this->_render_webpage('premises/profile', $data, '');
		}else{
			redirect('webapp/premises', 'refresh');
		}
	}


	/** Create new premises **/
	public function create( $page = 'details' ){

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			$premises_types	 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/premises_types', ['account_id'=>$this->user->account_id, 'where'=>['grouped'=>1], 'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
			$data['premises_types']	= ( isset($premises_types->premises_types) ) ? $premises_types->premises_types : null;
			$this->_render_webpage('premises/premises_create', $data);
		}
	}

	/** Do premises creation **/
	public function create_premises(){

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

			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$new_premises = $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/create_premises', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  = ( isset( $new_premises->premises ) ) 	? $new_premises->premises : null;
			$message	  = ( isset( $new_premises->message) ) ? $new_premises->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status']  = 1;
				$return_data['premises']= $result;
			}
			$return_data['status_msg']  = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/**
	* Delete premises (set as archived )
	**/
	public function delete_premises( $premises_id = false ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$premises_id = ( $this->input->post( 'premises_id' ) ) ? $this->input->post( 'premises_id' ) : ( !empty( $premises_id ) ? $premises_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && !$item_access && empty( $item_access->can_delete ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$delete_premises = $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/delete', $postdata, ['auth_token'=>$this->auth_token], true );
			$result		  = ( isset($delete_premises->status) ) ? $delete_premises->status : null;
			$message	  = ( isset($delete_premises->message) ) ? $delete_premises->message : 'Something went wrong, please try again!';
			if( !empty( $result ) ){
				$return_data['status']= 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	/*
	* Premises lookup / search
	*/
	public function lookup( $page = 'details' ){

		$return_data = '';

		if( !$this->identity() ){
			$return_data .= 'Access denied! Please login';
		}

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );
		if( !$this->user->is_admin && !$module_access ){
			$return_data .= $this->config->item( 'ajax_access_denied' );
		}else{
			# Setup search parameters
			$view_type 	   		= 'overview';
			$search_term   		= ( $this->input->post( 'search_term' ) ) ? $this->input->post( 'search_term' ) : false;
			$premises_types   		= ( $this->input->post( 'premises_types' ) ) ? $this->input->post( 'premises_types' ) : false;
			$premises_statuses		= ( $this->input->post( 'premises_statuses' ) ) ? $this->input->post( 'premises_statuses' ) : false;
			$premises_categories	= ( $this->input->post( 'premises_categories' ) ) ? $this->input->post( 'premises_categories' ) : false;
			$limit		   		= ( $this->input->post( 'limit' ) )  ? $this->input->post( 'limit' )  : DEFAULT_LIMIT;
			$start_index   		= ( $this->input->post( 'start_index' ) )  ? $this->input->post( 'start_index' )  : 0;
			$offset		   		= ( !empty( $start_index ) ) ? ( ( $start_index - 1 ) * $limit ) : 0;
			$order_by	   		= ( $this->input->post( 'order_by' ) ) ? $this->input->post( 'order_by' ) : false;;
			$where		   		= [];
			if( !empty( $this->input->post( 'period_days' ) ) || ( $this->input->post( 'period_days' ) == '0' ) ){
				$where['period_days'] = ( $this->input->post( 'period_days' ) == 0 ) ? '0' : $this->input->post( 'period_days' );
				$view_type = 'eol';
			}

			if( !empty( $this->input->post( 'audit_result_status_id' ) ) ){
				$where['premises.audit_result_status_id'] = $this->input->post( 'audit_result_status_id' );
				$view_type = 'result_status';
			}

			#prepare postdata
			$postdata = [
				'account_id'		=>$this->user->account_id,
				'search_term'		=>$search_term,
				'premises_types'		=>$premises_types,
				'premises_statuses'	=>$premises_statuses,
				'premises_categories'	=>$premises_categories,
				'where'				=>$where,
				'order_by'			=>$order_by,
				'limit'				=>$limit,
				'offset'			=>$offset
			];

			$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/premises_lookup', $postdata, ['auth_token'=>$this->auth_token], true );

			$premises			= ( isset( $search_result->premises ) ) ? $search_result->premises : null;
			$counters		= ( isset( $search_result->counters ) )  ? $search_result->counters 	: null;

			if( !empty( $premises ) ){

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

				$return_data = $this->load_premises_view( $premises, $view_type );

				if( !empty($pagination) ){
					$return_data .= '<tr><td colspan="4" style="padding: 0;">';
						$return_data .= $page_display.$pagination;
					$return_data .= '</td></tr>';
				}
			}else{
				$return_data .= '<br/>';
				$return_data .= ( isset($search_result->message) ) ? $search_result->message : 'No records found';
				$return_data .= '<br/><br/>';
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	/*
	* 	Prepare premises views
	*/
	private function load_premises_view( $premises_data, $view_type = false ){
		$return_data = '';
		if( !empty( $premises_data ) ){
			
			foreach( $premises_data as $k => $premises_details ){
				$return_data .= '<tr>';
					$return_data .= '<td><a href="'.base_url( '/webapp/premises/profile/'.$premises_details->premises_id ).'" >'.$premises_details->premises_id.'</a></td>';
					$return_data .= '<td><a href="'.base_url( '/webapp/premises/profile/'.$premises_details->premises_id ).'" >'.$premises_details->premises_ref.'</a></td>';
					$return_data .= '<td>'.( ucwords( $premises_details->premises_type ) ).'</td>';
					$return_data .= '<td>'.( ucwords( $premises_details->premises_desc ) ).'</td>';
					$return_data .= '<td>'.( ucwords( $premises_details->primary_attribute ) ).'</td>';
				$return_data .= '</tr>';
			}

			if( !empty( $pagination ) ){
				$return_data .= '<tr><td colspan="4" style="padding: 0;">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
			
		} else {
			$return_data .= '<tr><td colspan="4"><br/>'.$this->config->item("no_records").'</td></tr>';
		}
		return $return_data;
	}


	/** Update SIte Details **/
	public function update_premises( $premises_id = false, $page = 'details', $version = '2' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) 	? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$premises_id = ( $this->input->post( 'premises_id' ) ) ? $this->input->post( 'premises_id' ) : ( !empty( $premises_id ) ? $premises_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );

			$updated_premises= $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/update', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  = ( isset( $updated_premises->premises ) ) ? $updated_premises->premises : null;
			$message	  = ( isset( $updated_premises->message ) ) ? $updated_premises->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status']= 1;
				$return_data['premises'] = $result	;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}






	public function premises_types( $premises_type_id = false, $page = 'details' ){
		$toggled			= ( !empty( $this->input->get( 'toggled' ) ) ? $this->input->get( 'toggled' ) : false );
		$section 			= ( !empty( $page) ) ? $page : ( !empty( $this->input->get( 'page' ) ) ? $this->input->get( 'page' ) : 'details' );
		$premises_type_id  	= ( !empty( $premises_type_id ) ) ? $premises_type_id : ( !empty( $this->input->get( 'premises_type_id' ) ) ? $this->input->get( 'premises_type_id' ) : ( ( !empty( $this->input->get( 'id' ) ) ? $this->input->get( 'id' ) : null ) ) );

		if( !empty( $premises_type_id ) ){
			$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
			if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
				$this->_render_webpage( 'errors/access-denied', false );
			} else {

				$default_params = $params =[ 'account_id'=>$this->user->account_id, 'where'=>[ 'premises_type_id'=>$premises_type_id ], 'limit'=>-1 ];
				$premises_type_details = $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/premises_types', $params, [ 'auth_token'=>$this->auth_token ], true );

				if( !empty( $premises_type_details->premises_types ) ){

					$data['premises_type_details']		= $premises_type_details->premises_types;

					$disciplines	 					= $this->webapp_service->api_dispatcher( $this->api_end_point.'discipline/account_disciplines', [ 'account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
					$data['disciplines']				= ( isset( $disciplines->account_disciplines ) ) ? $disciplines->account_disciplines : null;

					$premises_type_attributes 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/premises_type_attributes', [ 'account_id'=>$this->user->account_id, 'premises_type_id'=>$premises_type_id ], ['auth_token'=>$this->auth_token], true );
					$data['premises_type_attributes']	= ( isset( $premises_type_attributes->attributes ) ) ? $premises_type_attributes->attributes : null;

					$attributes_bucket 					= $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/premises_type_attributes', [ 'account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );

					$data['attributes_bucket']			= ( isset( $attributes_bucket->attributes ) ) ? $attributes_bucket->attributes : null;

					$data['linked_attributes'] 			= !empty( $data['premises_type_attributes'] ) ? array_column( $data['premises_type_attributes'], 'attribute_ref' ) : [];

					$response_types	 					= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/response_types', $default_params, ['auth_token'=>$this->auth_token], true );
					$data['response_types']				= ( isset( $response_types->response_types ) ) ? $response_types->response_types : null;

					$this->_render_webpage( 'premises/premises_type_profile', $data );
				} else {
					redirect( 'webapp/premises/premises_types', 'refresh');
				}
			}
		} else {
			$this->_render_webpage( 'premises/premises_types', false, false, true );
		}
	}


	/*
	*	Get Premises Types List
	*/
	public function premises_types_list( $page = 'details' ){

		$return_data = '';

		$section 	 = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){

			$return_data .= $this->config->item( 'ajax_access_denied' );

		} else {

			# Setup search parameters
			$search_term   = ( $this->input->post( 'search_term' ) ) 	? $this->input->post( 'search_term' ) : false;
			$where   	   = ( $this->input->post( 'where' ) ) 			? $this->input->post( 'where' ) : false;
			$limit		   = ( !empty( $where['limit'] ) )  			? $where['limit']  : DEFAULT_LIMIT;
			$start_index   = ( $this->input->post( 'start_index' ) ) 	? $this->input->post( 'start_index' ) : DEFAULT_OFFSET;
			$offset		   = ( !empty( $start_index ) ) 				? ( ( $start_index - 1 ) * $limit ) : 0;
			$order_by	   = ( $this->input->post( 'order_by' ) ) 		? $this->input->post( 'order_by' ) : false;

			#prepare postdata
			$postdata = [
				'account_id'	=>$this->user->account_id,
				'search_term'	=>$search_term,
				'where'			=>$where,
				'order_by'		=>$order_by,
				'limit'			=>$limit,
				'offset'		=>$offset
			];

			$search_result		= $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/premises_types', $postdata, [ 'auth_token'=>$this->auth_token ], true );
			$premises_types		= ( isset( $search_result->premises_types ) ) ? $search_result->premises_types : null;

			if( !empty( $premises_types ) ){

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

				$return_data = $this->load_premises_types_view( $premises_types );
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
	*	Premises types views
	*/
	private function load_premises_types_view( $premises_types_data = false ){
		$return_data = '';
		if( !empty( $premises_types_data ) ){
			foreach( $premises_types_data as $k => $premises_types_details ){
				$return_data .= '<tr>';
					$return_data .= '<td><a href="'.base_url( '/webapp/premises/premises_types/'.$premises_types_details->premises_type_id ).'" >'. ucwords( $premises_types_details->premises_type ) .'</a></td>';
					$return_data .= '<td>'.( ( !empty( $premises_types_details->attribute_name ) ) ? ucwords( $premises_types_details->attribute_name ) : '' ).'</td>';
					$return_data .= '<td>'.( ( !empty( $premises_types_details->premises_type_desc ) ) ? ucfirst( $premises_types_details->premises_type_desc ) : '' ).'</td>';
					$return_data .= '<td>'.( !empty( $premises_types_details->date_created ) ? date( 'd-m-Y H:i:s', strtotime( $premises_types_details->date_created ) ) : '' ).'</td>';
					$return_data .= '<td>'.( !empty( $premises_types_details->is_active ) ? 'Active' : 'Disabled' ).'</td>';
				$return_data .= '</tr>';
			}

			if( !empty( $pagination ) ){
				$return_data .= '<tr><td colspan="5" style="padding: 0;">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
		}else{
			$return_data .= '<tr><td colspan="5"><br/>'.$this->config->item( "no_records" ).'</td></tr>';
		}
		return $return_data;
	}


	/** Create new Premises Type **/
	public function create_premises_type( $page = 'details' ){

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			
			$disciplines	 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'discipline/account_disciplines', [ 'account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
			$data['disciplines']		= ( isset( $disciplines->account_disciplines ) ) ? $disciplines->account_disciplines : null;

			$this->_render_webpage( 'premises/premises_type_create', $data );

		}
	}

	
	/** Add new Premises Type **/
	public function add_premises_type(){

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
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		} else {
			$postdata 	  	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$premises_type 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/add_premises_type', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  	= ( isset( $premises_type->premises_type ) ) ? $premises_type->premises_type : null;
			$message	  	= ( isset( $premises_type->message ) )  ? $premises_type->message : 'Oops! There was an error processing your request.';

			if( !empty( $result ) ){
				$return_data['status'] 			= 1;
				$return_data['premises_type']  	= $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	

	/*
	*	Update the Premises Type details
	*/
	public function update_premises_type( $attribute_id = false, $page = 'details' ){
		$return_data = [
			'status' => 0
		];

		$section 		= ( $this->input->post( 'page' ) ) 	? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
		$attribute_id 	= ( $this->input->post( 'attribute_id' ) ) ? $this->input->post( 'attribute_id' ) : ( !empty( $attribute_id ) ? $attribute_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	  		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$updated_attribute	= $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/update_premises_type', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  		= ( isset( $updated_attribute->premises_type ) ) ? $updated_attribute->premises_type : null;
			$message	  		= ( isset( $updated_attribute->message ) ) ? $updated_attribute->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status']			= 1;
				$return_data['premises_type'] 	= $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}



	/**
	* 	Delete Premises Type (archiving )
	**/
	public function delete_premises_type( $premises_type_id = false, $page = "details" ){
		$return_data = [
			'status' => 0
		];

		$section 		= ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
		$premises_type_id 	= ( $this->input->post( 'premises_type_id' ) ) ? $this->input->post( 'premises_type_id' ) : ( !empty( $premises_type_id ) ? $premises_type_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && !$item_access && empty( $item_access->can_delete ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		} else {
			if( $premises_type_id ){
				$postdata 	  	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
				$api_call 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/delete_premises_type', $postdata, ['auth_token'=>$this->auth_token] );
				$result		  	= ( isset( $api_call->status ) ) ? $api_call->status : null;
				$message	  	= ( isset( $api_call->message ) ) ? $api_call->message : 'Something went wrong, please try again!';
				if( !empty( $result ) ){
					$return_data['status'] = 1;
				}
				$return_data['status_msg'] = $message;
			} else {
				$return_data['status_msg'] = 'Missing Premises Type ID';
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/**
	*	Delete Premises Type Attribute
	**/
	public function delete_premises_type_attribute( $attribute_id = false, $page = 'details' ){
		$return_data = [
			'status'=>0
		];

		$section 		= ( $this->input->post( 'page' ) ) 	? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
		$attribute_id 	= ( $this->input->post( 'attribute_id' ) ) ? $this->input->post( 'attribute_id' ) : ( !empty( $attribute_id ) ? $attribute_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 	  		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$deleted_attribute	= $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/delete_premises_type_attribute', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  		= ( isset( $deleted_attribute->status ) )  ? $deleted_attribute->status : null;
			$message	  		= ( isset( $deleted_attribute->message ) ) ? $deleted_attribute->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status']		= 1;
				$return_data['attribute'] 	= $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}



	/**
	*	Add from the bank a new Premises attribute
	*/
	public function add_premises_type_attribute(){

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
		} else {
			$postdata 	  	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$API_call		= $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/add_premises_type_attribute', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  	 	 	= ( isset( $API_call->premises_type_attribute ) ) ? $API_call->premises_type_attribute : null;
			$message	  	 	 	= ( isset( $API_call->message ) ) ? $API_call->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 					= 1;
				$return_data['premises_type_attribute']	= $result;
				$text_color 							= 'auto';
			}
			$return_data['status_msg'] = '<span class="text-'.$text_color.'">'.$message.'</span>';
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	/*
	* Sites Premises lookup / search
	*/
	public function premises_lookup( $site_id = false, $page = 'details' ){

		$return_data = '';

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){

			$return_data .= $this->config->item( 'ajax_access_denied' );

		}else{

			# Setup search parameters
			$search_term   	= ( $this->input->post( 'search_term' ) ) 	? $this->input->post( 'search_term' ) : false;
			$where   	   	= ( $this->input->post( 'where' ) ) 		? $this->input->post( 'where' ) : false;
			$limit		   	= ( !empty( $where['limit'] ) )  			? $where['limit']  : DEFAULT_LIMIT;
			$start_index   	= ( $this->input->post( 'start_index' ) ) 	? $this->input->post( 'start_index' ) : DEFAULT_OFFSET;
			$offset		   	= ( !empty( $start_index ) ) 				? ( ( $start_index - 1 ) * $limit ) : 0;
			$order_by	   	= ( $this->input->post( 'order_by' ) ) 		? $this->input->post( 'order_by' ) : false;

			#prepare postdata
			$postdata = [
				'account_id'	=> $this->user->account_id,
				'search_term'	=> $search_term,
				'where'			=> $where,
				'order_by'		=> $order_by,
				'limit'			=> $limit,
				'offset'		=> $offset
			];

			$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/premises_lookup', $postdata, ['auth_token'=>$this->auth_token], true );

			$premises			= ( isset( $search_result->premises ) ) ? $search_result->premises : null;

			if( !empty( $premises ) ){

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
					$this->pagination->initialize($config);
					$pagination 			= $this->pagination->create_links();
				}

				$return_data = $this->load_premises_view2( $premises );
				if( !empty($pagination) ){
					$return_data .= '<tr style="border-bottom:1px solid #red" ><td colspan="7" style="padding: 0; border-bottom:#f4f4f4">';
						$return_data .= $page_display.$pagination;
					$return_data .= '</td></tr>';
				}
			}else{
				$return_data .= '<tr><td colspan="7" style="padding: 0;"><br/>';
					$return_data .= $this->config->item("no_records");
				$return_data .= '</td></tr>';
			}
		}

		print_r( $return_data );
		die();
	}


	/*
	*	Prepare Building premises view
	*/
	private function load_premises_view2( $premises_data ){
		$return_data = '';
		if( !empty( $premises_data ) ){
			foreach( $premises_data as $k => $premises_details ){
				$return_data .= '<tr>';
					$return_data .= '<td><a href="'.base_url( '/webapp/premises/profile/'.$premises_details->premises_id ).'" >'.$premises_details->premises_id.'</a></td>';
					$return_data .= '<td>'.( ucwords( $premises_details->premises_type ) ).'</td>';
					$return_data .= '<td>'.( ucwords( $premises_details->premises_desc ) ).'</td>';
					$return_data .= '<td>'.( !empty( $premises_details->primary_attribute ) ? $premises_details->primary_attribute : '' ).'</td>';
					$return_data .= '<td>'.( !empty( $premises_details->premises_status ) ? $premises_details->premises_status : '' ).'</td>';
					$return_data .= '<td width="10%">';
						$return_data .= '<class class="row pull-right">';
							$return_data .= '<div class="col-md-6" ><a href="'.base_url( '/webapp/premises/profile/'.$premises_details->premises_id ).'" ><i title="Click here to view this premises record" class="fas fa-edit text-blue pointer"></i></a></div>';
							$return_data .= '<div class="hide col-md-6 delete-item" ><i title="Click here to delete this Premises" class="delete-item fas fa-trash-alt text-red pointer"></i></div>';
						$return_data .= '</span>';
					$return_data .= '</td>';
				$return_data .= '</tr>';
			}

			if( !empty( $pagination ) ){
				$return_data .= '<tr><td colspan="6" style="padding: 0;">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
		}else{
			$return_data .= '<tr><td colspan="6"><br/>'.$this->config->item("no_records").'</td></tr>';
		}
		return $return_data;
	}



	/*
	*	Manage Premises Type Attributes - Overview page
	*/
	public function premises_type_attributes( $type_attribute_id = false, $page = 'details' ){

		$toggled	= ( !empty( $this->input->get( 'toggled' ) ) 	? $this->input->get( 'toggled' ) 	: false );
		$section 	= ( !empty( $page) ) 							? $page 							: ( !empty( $this->input->get( 'page' ) ) ? $this->input->get( 'page' ) : 'details' );
		$attribute_id  	= ( !empty( $attribute_id) ) 				? $attribute_id 					: ( !empty( $this->input->get( 'attribute_id' ) ) ? $this->input->get( 'attribute_id' ) : ( ( !empty( $this->input->get( 'attribute_id' ) ) ? $this->input->get( 'attribute_id' ) : null ) ) );
		
		if( !empty( $attribute_id ) ){
			$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
			if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
				$this->_render_webpage( 'errors/access-denied', false );
			} else {
				
				$default_params 	= $params =[ 'account_id'=>$this->user->account_id, 'where'=>[ 'attribute_id'=>$attribute_id ] ];
				$attribute_details 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/premises_type_attributes', $params, [ 'auth_token'=>$this->auth_token ], true );

				if( !empty( $attribute_details->premises_type_attributes ) ){
					$data['attribute_details']  = $attribute_details->premises_type_attributes;
					$response_types	 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/response_types', $default_params, ['auth_token'=>$this->auth_token], true );
					$data['response_types']	= ( isset( $response_types->response_types ) ) ? $response_types->response_types : null;
					$this->_render_webpage( 'premises/attributes/attribute_details_profile', $data );					
				}else{
					redirect( 'webapp/premises/manage_attributes', 'refresh' );
				}
			}
		} else {
			$this->_render_webpage( 'premises/attributes/manage_attributes', false, false, true );
		}
		
	}


	/*
	*	Premises Type Attributes List / Search
	*/
	public function attributes_lookup( $page = 'details' ){

		$return_data = '';
		$section 	 = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){
			$return_data .= $this->config->item( 'ajax_access_denied' );

		} else {

			# Setup search parameters
			$search_term   = ( $this->input->post( 'search_term' ) )	? $this->input->post( 'search_term' ) 	: false;
			$where   	   = ( $this->input->post( 'where' ) ) 			? $this->input->post( 'where' ) 		: false;
			$limit		   = ( !empty( $where['limit'] ) )  			? $where['limit']  						: 100;
			$start_index   = ( $this->input->post( 'start_index' ) )	? $this->input->post( 'start_index' ) 	: DEFAULT_OFFSET;
			$offset		   = ( !empty( $start_index ) ) 				? ( ( $start_index - 1 ) * $limit ) 	: 0;
			$order_by	   = ( $this->input->post( 'order_by' ) ) 		? $this->input->post( 'order_by' ) 		: false;

			#prepare postdata
			$postdata = [
				'account_id'	=> $this->user->account_id,
				'search_term'	=> $search_term,
				'where'			=> $where,
				'order_by'		=> $order_by,
				'limit'			=> $limit,
				'offset'		=> $offset
			];

			$API_call					= $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/premises_type_attributes', $postdata, ['auth_token'=>$this->auth_token], true );
			$premises_type_attributes	= ( isset( $API_call->attributes ) ) ? $API_call->attributes : null;

			if( !empty( $premises_type_attributes ) ){

				## Create pagination
				$counters 		= ( isset( $API_call->counters ) ) ? $API_call->counters : null;
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

				$return_data = $this->load_premises_type_attributes_view( $premises_type_attributes );
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
	*	Premises Type Attributes list view
	*/
	private function load_premises_type_attributes_view( $premises_type_attributes_data = false ){
		$return_data = '';
		if( !empty( $premises_type_attributes_data ) ){

			foreach( $premises_type_attributes_data as $k => $attribute ){
				$return_data .= '<tr>';
					$return_data .= '<td><a href="'.base_url( '/webapp/premises/premises_type_attributes/'.$attribute->attribute_id ).'" >'.ucwords( $attribute->attribute_name ).'</a></td>';
					$return_data .= '<td>'.( ( !empty( $attribute->attribute_ref ) ) ? $attribute->attribute_ref : '' ).'</td>';
					$return_data .= '<td>'.( ( !empty( $attribute->response_type_alt ) ) ? $attribute->response_type_alt : '' ).'</td>';
					$return_data .= '<td>'.( ( is_array( $attribute->response_options ) ) ? implode( " | ", $attribute->response_options ) : ( is_object( $attribute->response_options ) ? json_encode( $attribute->response_options ) : $attribute->response_options ) ).'</td>';
					$return_data .= '<td><span class="pull-right">'.( ( $attribute->is_active == 1 ) ? "Active" : "In-active" ).'</span></td>';
				$return_data .= '</tr>';
			}

			if( !empty( $pagination ) ){
				$return_data .= '<tr><td colspan="5" style="padding: 0;">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
		} else {
			$return_data .= '<tr><td colspan="5"><br/>'.$this->config->item( "no_records" ).'</td></tr>';
		}
		return $return_data;
	}
	
	
	/** 
	*	Update Premises Type Attribute
	**/
	public function update_premises_type_attribute( $attribute_id = false, $page = 'details' ){
		$return_data = [
			'status'=>0
		];

		$section 		= ( $this->input->post( 'page' ) ) 	? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
		$attribute_id 	= ( $this->input->post( 'attribute_id' ) ) ? $this->input->post( 'attribute_id' ) : ( !empty( $attribute_id ) ? $attribute_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	  		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			
			$updated_attribute	= $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/update_premises_type_attribute', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  		= ( isset( $updated_attribute->attribute ) ) ? $updated_attribute->attribute : null;
			$message	  		= ( isset( $updated_attribute->message ) ) ? $updated_attribute->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status']		= 1;
				$return_data['attribute'] 	= $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/**
	* Create new Generic Premises Attribute
	*/
	public function new_premises_attribute(){
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'details' );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		}else{
			$default_params 		= [ 'account_id'=>$this->user->account_id, 'limit'=>-1 ];
			$response_types	 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/response_types', $default_params, ['auth_token'=>$this->auth_token], true );
			$data['response_types']	= ( isset( $response_types->response_types ) ) ? $response_types->response_types : null;
			$this->_render_webpage( 'premises/attributes/attribute_add_new', $data );
		}
	}
	
	/** Fetch Pre-Premises type attributes **/
	public function fetch_preset_attributes(){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && !$item_access && empty( $item_access->can_delete ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata 					= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$premises_type_attributes	= $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/premises_type_attributes', $postdata, ['auth_token'=>$this->auth_token], true );
			$result						= ( isset( $premises_type_attributes->attributes ) )  	? $premises_type_attributes->attributes : null;
			$message					= ( isset( $premises_type_attributes->message ) ) 			? $premises_type_attributes->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 			= 1;
				$return_data['attributes_data'] = $this->load_premises_type_preset_attributes( $result );
			} else {
				$return_data['status_msg'] = 'There\'s currently no attributes set for this Premises type!';
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	//Load Pre-Premises Type Attributes view
	private function load_premises_type_preset_attributes( $attributes_data = false ){
		$return_data = '';
		if( !empty( $attributes_data ) ){
			foreach( $attributes_data as $k => $attribute ){
				$return_data .= '<div class="col-md-12 col-sm-12 col-xs-12">';

					$append_classes 		= ( $attribute->is_mandatory 	== 1 ) ? 'required' : '';

					switch ( $attribute->response_type ){

						default:
						case 'date':
						case 'datepicker':
						case 'short_text':
						case 'numbers_only':
							$append_classes 	.= ( in_array( $attribute->response_type, [ 'datepicker', 'date' ] ) ) 	? ' datepicker2' 	: '';
							$append_classes 	.= ( $attribute->response_type 	== 'numbers_only' ) ? ' numbers-only' 	: '';

							$apply_max_length	= 'maxlength="125"';

							$return_data .= '<div class="input-group form-group">';
								$return_data .= '<label class="input-group-addon" >'.ucwords( $attribute->attribute_name ).'</label>';
								$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_id]" 	value="'.$attribute->attribute_id.'">';
								$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_name]" 	value="'.htmlspecialchars( $attribute->attribute_name ).'">';
								$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][response_type]" 	value="'.$attribute->response_type.'">';
								$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][ordering]" 		value="'.$attribute->ordering.'">';
								$return_data .= '<input type="text" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_value]" 	value="" class="form-control '.$append_classes.'" '.$apply_max_length.' placeholder="Enter the '.ucwords( $attribute->attribute_name ).' here..." >';
							$return_data .= '</div>';

							break;

						case 'long_text':

							$return_data .= '<div class="input-group form-group">';
								$return_data .= '<label class="input-group-addon" >'.ucwords( $attribute->attribute_name ).'</label>';
								$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_id]" 	value="'.$attribute->attribute_id.'">';
								$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_name]" 	value="'.htmlspecialchars( $attribute->attribute_name ).'">';
								$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][response_type]" 	value="'.$attribute->response_type.'">';
								$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][ordering]" 		value="'.$attribute->ordering.'">';
								$return_data .= '<textarea type="text" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_value]" 	value="" rows="4" class="form-control '.$append_classes.'" class="form-control" placeholder="Enter the '.ucwords( $attribute->attribute_name ).' here..." ></textarea>';
							$return_data .= '</div>';

							break;

						case 'single_choice':
						case 'multiple_choice':

							$return_data .= '<div class="input-group form-group">';
								$return_data .= '<label class="control-label"><strong>'.ucwords( $attribute->attribute_name ).'</strong></label>';
								$return_data .= '<div class="col-md-12" >';
									$return_data .= '<div class="row" >';

										if( $attribute->response_type == 'single_choice' ){
											if( !empty( $attribute->response_options ) ){ foreach( $attribute->response_options as $k => $option ){
												$return_data .= '<div class="col-md-4 col-sm-4 col-xs-12" >';
													$return_data .= '<label class="pointer" >';
														$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_id]" 	value="'.$attribute->attribute_id.'">';
														$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_name]" 	value="'.htmlspecialchars( $attribute->attribute_name ).'">';
														$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][response_type]" 	value="'.$attribute->response_type.'">';
														$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][ordering]" 		value="'.$attribute->ordering.'">';
														$return_data .= '<input type="radio"    name="premises_attributes['.$attribute->attribute_id.'][attribute_value]"  value="'.$option.'" id="optionsRadio'.$k.'" > '.$option;
													$return_data .= '</label>';
												$return_data .= '</div>';

											} } else {
												$return_data .= '<div class="col-md-12 col-sm-12 col-xs-12" >No options set for this attribute.</div>';
											}
										} else if ( $attribute->response_type == 'multiple_choice' ){
											if( !empty( $attribute->response_options ) ){
												$return_data .= '<div class="col-md-6 col-sm-46 col-xs-12" >';
													$return_data .= '<label class="pointer" >';
														$return_data .= '<input class="check-all" type="checkbox" id="check-all'.$attribute->attribute_id.'" data-attribute_id="'.$attribute->attribute_id.'"  > Tick all';
													$return_data .= '</label>';
												$return_data .= '</div>';
												foreach( $attribute->response_options as $k => $option ){
													$return_data .= '<div class="col-md-6 col-sm-6 col-xs-12" >';
														$return_data .= '<label class="pointer" >';
															$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_id]" 	value="'.$attribute->attribute_id.'">';
															$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_name]" 	value="'.htmlspecialchars( $attribute->attribute_name ).'">';
															$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][response_type]" 	value="'.$attribute->response_type.'">';
															$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][ordering]" 		value="'.$attribute->ordering.'">';
															$return_data .= '<input type="checkbox" name="premises_attributes['.$attribute->attribute_id.'][attribute_value][]" class="check-options check-opts'.$attribute->attribute_id.'" data-attribute_id="'.$attribute->attribute_id.'" value="'.$option.'" id="optionsCheckbox'.$k.'" > '.$option;
														$return_data .= '</label>';
													$return_data .= '</div>';
												}

											} else {
												$return_data .= '<div class="col-md-12 col-sm-12 col-xs-12" >No options set for this attribute.</div>';
											}
										}

									$return_data .= '</div>';
								$return_data .= '</div>';
							$return_data .= '</div>';

							break;
							
						case 'file':
						case 'photo':
						case 'image':
				
							$return_data .= '<div class="input-group form-group">';
								$return_data .= '<label class="input-group-addon" >'.ucwords( $attribute->attribute_name ).'</label>';
								$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_id]" 	value="'.$attribute->attribute_id.'">';
								$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_name]" 	value="'.htmlspecialchars( $attribute->attribute_name ).'">';
								$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][response_type]" 	value="'.$attribute->response_type.'">';
								$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][ordering]" 		value="'.$attribute->ordering.'">';
								$return_data .= '<input type="hidden" 	name="premises_attributes['.$attribute->attribute_id.'][attribute_value]" 	value="'.$attribute->response_type.'" >';
								$return_data .= '<span class="control-fileupload pointer">';
									$return_data .= '<label for="premises_image" class="pointer text-left">Please choose a file on your computer <i class="fas fa-upload"></i></label><input name="user_files[]" type="file" id="premises_image" >';
								$return_data .= '</span>';
							$return_data .= '</div>';

							break;	

					}

				$return_data .= '</div>';
			}
		}else{
			$return_data .= '<div>'.$this->config->item( 'no_records' ).'</div>';
		}
		return $return_data;
	}
	
	
	/** Get list of addresses by postcode **/
	public function get_addresses_by_postcode( $postcodes = false ){
		$postcodes = ( $this->input->post("postcodes") ) ? $this->input->post("postcodes") : $postcodes;
		
		if( $postcodes ){
			
			$this->load->model( 'serviceapp/Address_model','address_service' );
			
			$addresses_list = "";
			$addresses = $this->address_service->get_addresses( $postcodes );

			if( $addresses ){
				$first_address 		= array_values( $addresses )[0];
				$postcode_district 	= !empty( $postcode_district ) ? trim( urldecode( $postcode_district ) ) : trim( substr( urldecode( $postcodes ), 0, -3 ) );
				$addresses_list .= '<input type="hidden" class="address_postcode_district" value="'.strtoupper( $postcode_district ).'">';
				$addresses_list .= '<select id="address_lookup_result" name="address_id" class="form-control" style="width:100%; margin-bottom:20px;">';
				$addresses_list .= '<option>Please select address...</option>';
				foreach( $addresses as $address ){
					$addresses_list .= '<option value = "'.$address["main_address_id"].'" data-addressline1="'.$address["addressline1"].'"  data-addressline2="'.$address["addressline2"].'"  data-addressline3="'.$address["addressline3"].'"  data-posttown="'.$address["posttown"].'"  data-county="'.$address["county"].'"  data-postcode="'.$address["postcode"].'" >'.$address["summaryline"].'</option>';
				}
				$addresses_list .= '</select>';
			} else {
				$postcode_district 	= trim( substr( urldecode( $postcodes ), 0, -3 ) );
				$addresses_list .= '<input type="hidden" class="address_postcode_district" value="'.strtoupper( $postcode_district ).'">';
				$addresses_list .= '<select id="address_lookup_result" name="address_id" class="form-control" style="width:100%; margin-bottom:20px;">';
					$addresses_list .= '<option value = "" data-addressline1=""  data-addressline2=""  data-posttown=""  data-county=""  data-postcode="" >Address not found</option>';
				$addresses_list .= '</select>';
			}

		} else {
			$addresses_list = '<input type="hidden" class="address_postcode_district" value="">';
			$addresses_list .= '<select id="address_lookup_result" name="address_id" class="form-control" style="width:100%; margin-bottom:20px;">';
				$addresses_list .= '<option disabled="disabled">Please provide a postcode.</option>';
			$addresses_list .= '</select>';
		}
		$data["addresses_list"] = $addresses_list;
		echo json_encode( $data );
	}
	
}
