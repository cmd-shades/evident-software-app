<?php

namespace Application\Modules\Web\Controllers;

class Asset extends MX_Controller {

	function __construct(){
		parent::__construct();

		if( !$this->identity() ){
			redirect('webapp/user/login', 'refresh');
		}

		$this->module_id 	   = $this->webapp_service->_get_module_id( $this->router->fetch_class() );
		$this->load->library('pagination');

		$this->load->model('serviceapp/Asset_model','asset_service');
		$this->priority_ratings 	= [ 'Low', 'Medium', 'High' ];

	}

	//redirect if needed, otherwise display the user list
	function index(){

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );
		if( !$this->user->is_admin && !$module_access ){
			$this->_render_webpage( 'errors/access-denied', false );
		}else{
			redirect('webapp/asset/assets', 'refresh');
		}
	}

	/** Get list of assets **/
	public function assets( $asset_id = false ){

		if( $asset_id ){
			redirect('webapp/asset/profile/'.$asset_id, 'refresh');
		}

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){
			$this->_render_webpage( 'errors/access-denied', false );
		}else{
			$asset_statuses		  		= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/asset_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['asset_statuses'] 	= ( isset($asset_statuses->asset_statuses) ) ? $asset_statuses->asset_statuses : null;

			$asset_types		 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/asset_types', ['account_id'=>$this->user->account_id, 'limit'=>-1], ['auth_token'=>$this->auth_token], true );
			$data['asset_types'] 		= ( isset( $asset_types->asset_types ) ) ? $asset_types->asset_types : null;
			
			$asset_categories			= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/asset_categories', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['asset_categories'] 	= ( isset( $asset_categories->asset_categories ) ) ? $asset_categories->asset_categories : null;
			

			$this->_render_webpage('asset/index', $data);
		}
	}

	//View Asset profile - V2
	function profile( $asset_id = false, $page = 'details' ){

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		}else if( $asset_id ){
			$asset_details		   = $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/assets', ['account_id'=>$this->user->account_id,'asset_id'=>$asset_id], ['auth_token'=>$this->auth_token], true );
			$data['asset_details'] = ( isset($asset_details->assets) ) ? $asset_details->assets : null;
			if( !empty( $data['asset_details'] ) ){

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

				$data['priority_ratings']= $this->priority_ratings;

				switch( $page ){
					case 'evidocs':
					case 'audits':
						$asset_audits	  	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/audits', ['account_id'=>$this->user->account_id, 'asset_id'=>$asset_id ], ['auth_token'=>$this->auth_token], true );
						$data['asset_audits'] = ( isset($asset_audits->audits) ) ? $asset_audits->audits : null;
						$data['include_page'] = 'asset_audits.php';
						break;
					case 'tracking':
						$asset_tracking		 	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/tracking', ['account_id'=>$this->user->account_id, 'asset_id'=>$asset_id ], ['auth_token'=>$this->auth_token], true );
						$data['asset_tracking']   = ( isset($asset_tracking->documents->{$this->user->account_id}) ) ? $asset_tracking->documents->{$this->user->account_id} : null;
						$data['include_page'] = 'asset_tracking.php';
						break;
					case 'linked_assets':
						#$asset_connectivity	 	  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/connectivity', ['account_id'=>$this->user->account_id, 'asset_id'=>$asset_id ], ['auth_token'=>$this->auth_token], true );
						#$data['asset_connectivity'] = ( isset( $asset_connectivity->connectivity ) ) ? $asset_connectivity->connectivity : null;

						$data['parent_assets'] 		= ( !empty( $data['asset_details']->parent_assets ) ) ? $data['asset_details']->parent_assets : false;
						$data['child_assets'] 		= ( !empty( $data['asset_details']->child_assets ) ) ? $data['asset_details']->child_assets : false;
						$data['linked_assets']		= ( !empty( $data['asset_details']->child_assets ) ) ? array_merge( array_column( $data['asset_details']->child_assets, 'asset_id' ), [$asset_id] ) : [$asset_id];

						$available_assets	 	  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/assets', ['account_id'=>$this->user->account_id, 'limit'=>-1, /*['site_id'=>$data['asset_details']->site_id ]*/ ], ['auth_token'=>$this->auth_token], true );
						$data['available_assets'] 	= ( isset( $available_assets->assets ) ) ? $available_assets->assets : null;

						$data['include_page'] 	  	= 'asset_linked_assets.php';
						break;

					case 'location':

						$data['sites'] 				= $postdata	= [];
						$postdata['account_id']		= $this->user->account_id;
						$postdata['where']['organized'] = 1;
						$api_call		  	  		= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/sites', $postdata, ['auth_token'=>$this->auth_token], true );
						$data['sites'] 				= ( isset( $api_call->sites ) ) ? $api_call->sites : null;

						$data['site_zones']			= $postdata	= [];
						if( !empty( $data['asset_details']->site_id ) ){
							$postdata['account_id']		= $this->user->account_id;
							$postdata['where']['site_id']		= ( !empty( $data['asset_details']->site_id ) ) ? ( int ) $data['asset_details']->site_id : false ;
							$api_call					= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/site_zones', $postdata, ['auth_token'=>$this->auth_token], true );
							$data['site_zones']			= ( !empty( $api_call->site_zones ) ) ? $api_call->site_zones : false ;
						}

						$data['site_locations'] 	= $postdata	= [];
						if( !empty( $data['asset_details']->location_id ) ){
							$postdata['account_id']		= $this->user->account_id;
							$postdata['site_id']		= ( !empty( $data['asset_details']->site_id ) ) ? ( int ) $data['asset_details']->site_id : false ;
							$postdata['where']['location_id']		= ( !empty( $data['asset_details']->location_id ) ) ? ( int ) $data['asset_details']->location_id : false ;
							$api_call					= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/locations', $postdata, ['auth_token'=>$this->auth_token], true );
							$data['site_locations']	= ( !empty( $api_call->site_locations ) ) ? $api_call->site_locations : false ;
						}

						$data['include_page'] 		= 'asset_location.php';
						break;

					case 'cost':
						$cost_tracking	  	  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'cost/cost_items', ['account_id'=>$this->user->account_id, 'where'=>[ 'asset_id'=>$asset_id ] ], ['auth_token'=>$this->auth_token], true );
						$data['cost_tracking'] 	= ( isset( $cost_tracking->cost_items) ) ? $cost_tracking->cost_items : null;

						$cost_item_types	  	 = $this->webapp_service->api_dispatcher( $this->api_end_point.'cost/cost_item_types', ['account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
						$data['cost_item_types'] = ( isset( $cost_item_types->cost_item_types) ) ? $cost_item_types->cost_item_types : null;

						$data['include_page'] 	= 'asset_cost_tracking.php';
						break;
					case 'contracts':

						$linked_contracts	  	 = $this->webapp_service->api_dispatcher( $this->api_end_point.'contract/linked_assets', ['account_id'=>$this->user->account_id, 'asset_id'=>$asset_id ], ['auth_token'=>$this->auth_token], true );
						$data['linked_contracts']= ( isset( $linked_contracts->linked_assets) ) ? $linked_contracts->linked_assets : null;

						$available_contracts	  	 = $this->webapp_service->api_dispatcher( $this->api_end_point.'contract/contracts', ['account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
						$data['available_contracts'] = ( isset( $available_contracts->contract) ) ? $available_contracts->contract : null;
						$data['include_page'] 	= 'asset_linked_contracts.php';
						break;
					case 'jobs':

						$asset_jobs 		  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/jobs', ['account_id'=>$this->user->account_id,'where'=>['asset_id' => $asset_id ] ], ['auth_token'=>$this->auth_token], true );
						$data['asset_jobs']   = ( isset( $asset_jobs->jobs ) ) 		? $asset_jobs->jobs : null;

						$job_types		 	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_types', ['account_id'=>$this->user->account_id, "limit"=>99], ['auth_token'=>$this->auth_token], true );
						$data['job_types'] 	  = ( isset( $job_types->job_types ) ) 		? $job_types->job_types : null;

						$job_statuses		  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_statuses', ['account_id'=>false], ['auth_token'=>$this->auth_token], true );
						$data['job_statuses'] = ( isset( $job_statuses->job_statuses ) ) ? $job_statuses->job_statuses : null;

						$operatives		  	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'user/users', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
						$data['operatives']   = ( isset( $operatives->users ) ) ? $operatives->users : null;

						$data['job_durations']= job_durations();

						$data['include_page'] = 'asset_jobs.php';
						break;
					case 'schedules':

						$asset_schedules		= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/schedules', ['account_id'=>$this->user->account_id, 'where'=>['asset_id'=>$asset_id] ], ['auth_token'=>$this->auth_token], true );
						$data['asset_schedules']= ( isset( $asset_schedules->schedules ) ) ? $asset_schedules->schedules : null;

						$data['include_page'] 	= 'asset_schedules.php';
						break;

					case 'details':
					default:
						$asset_types	 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/asset_types', ['account_id'=>$this->user->account_id, 'where'=>['grouped'=>1 ], 'limit' =>-1 ], ['auth_token'=>$this->auth_token], true );
						$data['asset_types']	= ( isset($asset_types->asset_types) ) ? $asset_types->asset_types : null;

						$asset_statuses		  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/asset_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
						$data['asset_statuses'] = ( isset($asset_statuses->asset_statuses) ) ? $asset_statuses->asset_statuses : null;

						$asset_locations		= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/asset_locations', ['account_id'=>$this->user->account_id, 'limit' =>-1 ], ['auth_token'=>$this->auth_token], true );
						$data['asset_locations']= ( isset($asset_locations->asset_locations) ) ? $asset_locations->asset_locations : null;

						$users		  	  		= $this->webapp_service->api_dispatcher( $this->api_end_point.'user/users', ['account_id'=>$this->user->account_id, 'limit' =>-1 ], ['auth_token'=>$this->auth_token], true );
						$data['users']  	  	= ( isset($users->users) ) ? $users->users : null;

						$event_statuses		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/event_statuses', ['account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
						$data['event_statuses'] = ( isset( $event_statuses->event_statuses ) ) ? $event_statuses->event_statuses : null;

						$site_panels		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/panels', ['account_id'=>$this->user->account_id, 'site_id'=>false, 'include_assets'=>false ], ['auth_token'=>$this->auth_token], true );
						$data['site_panels']    = ( isset( $site_panels->site_panels ) ) ? $site_panels->site_panels : null;

						$monitored				= ( in_array( $data['asset_details']->asset_group, ['panel'] ) ) ? 1 : 0;

						$postdata =	[
							'account_id' 	=> $this->user->account_id,
							'site_id' 		=> false,
							'monitored'		=> $monitored,
						];
						$postdata['where']['organized'] = true;

						$sites		  		  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/sites', $postdata, ['auth_token'=>$this->auth_token], true );
						$data['sites'] 			= ( isset( $sites->sites ) ) ? $sites->sites : null;

						$audit_result_statuses			= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/result_statuses', ['account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
						$data['audit_result_statuses']  = ( isset( $audit_result_statuses->audit_result_statuses ) ) ? $audit_result_statuses->audit_result_statuses : null;

						$linked_contracts	  	 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'contract/linked_assets', ['account_id'=>$this->user->account_id, 'asset_id'=>$asset_id ], ['auth_token'=>$this->auth_token], true );
						$data['linked_contracts']	= ( isset( $linked_contracts->linked_assets) ) ? $linked_contracts->linked_assets : null;
						$data['linked_contracts']	= ( !empty( $data['linked_contracts'] ) ) ? array_column( $data['linked_contracts'], 'contract_id' ) : [];

						$available_contracts	  	 = $this->webapp_service->api_dispatcher( $this->api_end_point.'contract/contracts', ['account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
						$data['available_contracts'] = ( isset( $available_contracts->contract) ) ? $available_contracts->contract : null;

						$data['include_page'] 	= 'asset_details.php';
						break;
				}
			}

			//Run the admin check if tab needs only admin
			if( !empty( $run_admin_check ) ){
				if( ( !admin_check( $this->user->is_admin, false, ( !empty( $data['permissions'] ) ? $data['permissions']->is_admin : false ) ) ) ){
					$data['admin_no_access'] = true;
				}
			}

			$this->_render_webpage('asset/profile', $data, '');
		}else{
			redirect('webapp/asset', 'refresh');
		}
	}


	/** Create new asset **/
	public function create( $page = 'details' ){

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			$asset_types	 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/asset_types', ['account_id'=>$this->user->account_id, 'where'=>['grouped'=>1], 'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
			$data['asset_types']	= ( isset($asset_types->asset_types) ) ? $asset_types->asset_types : null;

			$asset_locations		= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/asset_locations', ['account_id'=>$this->user->account_id, 'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
			$data['asset_locations']= ( isset($asset_locations->asset_locations) ) ? $asset_locations->asset_locations : null;

			$location_groups		= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/location_groups', ['account_id'=>$this->user->account_id, 'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
			$data['location_groups']= ( isset( $location_groups->location_groups) ) ? $location_groups->location_groups : null;

			$sites		  		  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/sites', ['account_id'=>$this->user->account_id, 'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
			$data['sites'] 			= ( isset( $sites->sites ) ) ? $sites->sites : null;

			$audit_categories	 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/audit_categories', [ 'account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
			$data['evidoc_categories']	= ( isset( $audit_categories->audit_categories ) ) ? $audit_categories->audit_categories : null;

			$assets					= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/assets', ['account_id'=>$this->user->account_id, 'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
			$data['existing_assets']= ( isset( $assets->assets ) ) ? $assets->assets : null;

			$attributes_bucket 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/asset_type_attributes', [ 'account_id'=>$this->user->account_id, 'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
			$data['attributes_bucket']	= ( isset( $attributes_bucket->asset_type_attributes ) ) ? $attributes_bucket->asset_type_attributes : null;

			## orig:: $default_params = $params =[ 'account_id'=>$this->user->account_id, 'where'=>[ 'asset_type_id'=>$asset_type_id ] ];
			$default_params = $params =[ 'account_id'=>$this->user->account_id, 'limit'=>-1 ];

			$response_types	 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/response_types', $default_params, ['auth_token'=>$this->auth_token], true );
			$data['response_types']		= ( isset( $response_types->response_types ) ) ? $response_types->response_types : null;

			$audit_categories	 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/audit_categories', [ 'account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
			$data['asset_categories']	= ( isset( $audit_categories->audit_categories ) ) ? $audit_categories->audit_categories : null;

			$data['sub_categories']		= sub_categories();

			$disciplines	 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'discipline/account_disciplines', [ 'account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
			$data['disciplines']		= ( isset( $disciplines->account_disciplines ) ) ? $disciplines->account_disciplines : null;

			$message 				= ( $this->session->flashdata('message') ) ? $this->session->flashdata('message') : null;
			$data['message']		= $message;

			$this->_render_webpage('asset/asset_create', $data);
		}
	}

	public function modal_add_asset_attribute(){
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			$default_params = $params =[ 'account_id'=>$this->user->account_id ];

			$attributes_bucket 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/asset_type_attributes', [ 'account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
			$data['attributes_bucket']	= ( isset( $attributes_bucket->asset_type_attributes ) ) ? $attributes_bucket->asset_type_attributes : null;

			$response_types	 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/response_types', $default_params, ['auth_token'=>$this->auth_token], true );
			$data['response_types']		= ( isset( $response_types->response_types ) ) ? $response_types->response_types : null;

			$this->load->view('asset/asset_type_attribute_add_new.php', $data);
		}

	}

	/** Create new asset **/
	public function create_asset_type( $page = 'details' ){

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			
			$audit_categories	 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/audit_categories', [ 'account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );

			$data['evidoc_categories']	= ( isset( $audit_categories->audit_categories ) ) ? $audit_categories->audit_categories : null;
			$data['sub_categories']		= sub_categories();
			
			$disciplines	 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'discipline/account_disciplines', [ 'account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
			$data['disciplines']		= ( isset( $disciplines->account_disciplines ) ) ? $disciplines->account_disciplines : null;

			$this->_render_webpage('asset/asset_type_create', $data);

		}
	}

	/**
	* 	Delete asset type(set as archived )
	**/
	public function delete_asset_type( $asset_type_id = false ){
		$return_data = [
			'status' => 0
		];

		$section 		= ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
		$asset_type_id 	= ( $this->input->post( 'asset_type_id' ) ) ? $this->input->post( 'asset_type_id' ) : ( !empty( $asset_id ) ? $asset_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && !$item_access && empty( $item_access->can_delete ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		} else {
			if( $asset_type_id ){
				$postdata 	  	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
				$api_call 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/delete_asset_type', $postdata, ['auth_token'=>$this->auth_token] );
				$result		  	= ( isset( $api_call->status ) ) ? $api_call->status : null;
				$message	  	= ( isset( $api_call->message ) ) ? $api_call->message : 'Something went wrong, please try again!';
				if( !empty( $result ) ){
					$return_data['status'] = 1;
				}
				$return_data['status_msg'] = $message;
			} else {
				$return_data['status_msg'] = 'No asset type ID was entered!';
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/** Do asset creation **/
	public function create_asset(){

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
		}else{

			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$new_asset	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/create_asset', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  = ( isset( $new_asset->asset ) ) 	? $new_asset->asset : null;
			$message	  = ( isset( $new_asset->message) ) ? $new_asset->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status']  = 1;
				$return_data['asset']   = $result;
			}
			$return_data['status_msg']  = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/**
	* Delete asset (set as archived )
	**/
	public function delete_asset( $asset_id = false ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$asset_id = ( $this->input->post( 'asset_id' ) ) ? $this->input->post( 'asset_id' ) : ( !empty( $asset_id ) ? $asset_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && !$item_access && empty( $item_access->can_delete ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$delete_asset = $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/delete', $postdata, ['auth_token'=>$this->auth_token], true );
			$result		  = ( isset($delete_asset->status) ) ? $delete_asset->status : null;
			$message	  = ( isset($delete_asset->message) ) ? $delete_asset->message : 'Something went wrong, please try again!';
			if( !empty( $result ) ){
				$return_data['status']= 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	/*
	* Asset lookup / search
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
			$asset_types   		= ( $this->input->post( 'asset_types' ) ) ? $this->input->post( 'asset_types' ) : false;
			$asset_statuses		= ( $this->input->post( 'asset_statuses' ) ) ? $this->input->post( 'asset_statuses' ) : false;
			$asset_categories	= ( $this->input->post( 'asset_categories' ) ) ? $this->input->post( 'asset_categories' ) : false;
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
				$where['asset.audit_result_status_id'] = $this->input->post( 'audit_result_status_id' );
				$view_type = 'result_status';
			}

			#prepare postdata
			$postdata = [
				'account_id'		=>$this->user->account_id,
				'search_term'		=>$search_term,
				'asset_types'		=>$asset_types,
				'asset_statuses'	=>$asset_statuses,
				'asset_categories'	=>$asset_categories,
				'where'				=>$where,
				'order_by'			=>$order_by,
				'limit'				=>$limit,
				'offset'			=>$offset
			];

			$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/lookup', $postdata, ['auth_token'=>$this->auth_token], true );

			$assets			= ( isset( $search_result->assets ) ) ? $search_result->assets : null;
			$counters		= ( isset( $search_result->counters ) )  ? $search_result->counters 	: null;
			
			if( !empty( $assets ) ){

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

				$return_data = $this->load_assets_view( $assets, $view_type );

				if( !empty($pagination) ){
					$return_data .= '<tr><td colspan="6" style="padding: 0;">';
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
	* 	Prepare assets views
	*/
	private function load_assets_view( $assets_data, $view_type = false ){
		$return_data = '';
		if( !empty( $assets_data ) ){
			foreach( $assets_data as $k => $asset_details ){

				if( $view_type == 'eol' ){

					if( valid_date( $asset_details->end_of_life_date ) ){
						$expiry_date= date( 'd-m-Y', strtotime( $asset_details->end_of_life_date ) );
						$date1 		= new DateTime();
						$date2 		= new DateTime( $asset_details->end_of_life_date );
						$interval 	= $date1->diff( $date2 );
						$difference = $interval->format("%r%a");
						$days_due	= ( $difference == 0 ) ? 'Due today' : ( ( $difference < 0 ) ? $difference.' days ago' : $difference.' days' );
					}else{
						$expiry_date= '';
						$days_due	= '';
					}

					$return_data .= '<tr>';
						//$return_data .= '<td><a href="'.base_url('/webapp/asset/profile/'.$asset_details->asset_id).'" >'.$asset_details->asset_id.'</a></td>';
						$return_data .= '<td><a href="'.base_url('/webapp/asset/profile/'.$asset_details->asset_id).'" >#'.$asset_details->asset_id.' '.$asset_details->asset_unique_id . '</a></td>';
						$return_data .= '<td>'.$asset_details->asset_type.'</td>';
						$return_data .= '<td>'.$asset_details->asset_unique_id.'</td>';
						$return_data .= '<td>'.$expiry_date.'</td>';
						$return_data .= '<td>'.$days_due.'</td>';
						$return_data .= '<td><span class="pull-right">&pound;'.( number_format( $asset_details->purchase_price, 2 ) ).'</span></td>';
					$return_data .= '</tr>';

				} else {
					$return_data .= '<tr>';
						$return_data .= '<td><a href="'.base_url( '/webapp/asset/profile/'.$asset_details->asset_id ).'" >'.$asset_details->asset_unique_id.'</a></td>';
						$return_data .= '<td>'.( ucwords( $asset_details->asset_type ) ).'</td>';
						$return_data .= '<td>'.( ucwords( $asset_details->category_name ) ).'</td>';
						$return_data .= '<td>'.( ucwords( $asset_details->primary_attribute ) ).'</td>';
						$return_data .= '<td>'.$asset_details->assigned_to.'</td>';
						$return_data .= '<td>'.$asset_details->result_status.'</td>';
					$return_data .= '</tr>';
				}
			}

			if( !empty($pagination) ){
				$return_data .= '<tr><td colspan="5" style="padding: 0;">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
		}else{
			$return_data .= '<tr><td colspan="5"><br/>'.$this->config->item("no_records").'</td></tr>';
		}
		return $return_data;
	}


	/** Update SIte Details **/
	public function update_asset( $asset_id = false, $page = 'details', $version = '2' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) 	? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$asset_id = ( $this->input->post( 'asset_id' ) ) ? $this->input->post( 'asset_id' ) : ( !empty( $asset_id ) ? $asset_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );

			$updated_asset= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/update', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  = ( isset( $updated_asset->asset ) ) ? $updated_asset->asset : null;
			$message	  = ( isset( $updated_asset->message ) ) ? $updated_asset->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status']= 1;
				$return_data['asset'] = $result	;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	/**  View records by Evidocs Result status **/
	public function result_status( $result_group = false ){

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'details' );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		}else{
			$postdata 	  		 	= array_merge( ['account_id'=>$this->user->account_id], $this->input->get() );
			$asset_result_statuses 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/result_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );


			$data['result_statuses']= ( isset( $asset_result_statuses->audit_result_statuses ) ) ? $asset_result_statuses->audit_result_statuses : null;
			$data['selected_group'] = ( !empty( $this->input->get( 'group' ) ) ) ? $this->input->get( 'group' ) : 'all';
			$data['date_from'] 		= ( !empty( $this->input->get( 'date_from' ) ) ) ? $this->input->get( 'date_from' ) : false;
			$data['date_to'] 		= ( !empty( $this->input->get( 'date_to' ) ) ) ? $this->input->get( 'date_to' ) : false;
			$this->_render_webpage('asset/asset_result_status', $data);
		}
	}

	/** Upload a Asset document **/
	public function upload_docs( $asset_id = false, $page = 'details' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$asset_id = ( $this->input->post( 'asset_id' ) ) ? $this->input->post( 'asset_id' ) : ( !empty( $asset_id ) ? $asset_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
			redirect('webapp/asset', 'refresh');
		}else{
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$upload_doc   = $this->webapp_service->api_dispatcher( $this->api_end_point.'document_handler/upload', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  = ( isset($upload_doc->documents) ) ? $upload_doc->documents : null;
			$message	  = ( isset($upload_doc->message) ) ? $upload_doc->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				redirect('webapp/asset/profile/'.$asset_id.'/documents', 'refresh');
			}
		}
		redirect('webapp/asset/', 'refresh');
	}

	/*
	* Load a audit record
	*/
	public function view_audit_record( $audit_id = false ){

		$audit_id 	= ( $this->input->post( 'audit_id' ) ) ? $this->input->post( 'audit_id' ) : ( !empty( $audit_id ) ? $audit_id : null );

		$return_data = [
			'status'=>0,
			'audit_record'=>null,
			'status_msg'=>'Invalid paramaters'
		];

		if( !empty($audit_id) ){
			$audit_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/audits', ['account_id'=>$this->user->account_id,'audit_id'=>$audit_id], ['auth_token'=>$this->auth_token], true );
			$result		= ( isset($audit_result->audits) ) ? $audit_result->audits : null;
			$message	= ( isset($audit_result->message) ) ? $audit_result->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$audit = $this->load_audit( $result );
				$return_data['status'] 	  = 1;
				$return_data['audit_record'] = $audit;
			}
			$return_data['status_msg'] = $message;
		}
		print_r( json_encode( $return_data ) );
		die();
	}

	private function load_audit( $audit_record = false ){
		$audit = '';
		if( !empty( $audit_record ) ){
			$audit .= '<table style="width:100%">';
					$audit .= '<tr><th width="30%">Audit ID</th><td>'.$audit_record->audit_id.'</td></tr>';
					$audit .= '<tr><th>Date Audited</th><td>'.date('d-m-Y',strtotime( $audit_record->date_created )).'</td></tr>';
					$audit .= '<tr><th>Audited by</th><td>'.$audit_record->created_by.'</td></tr>';
					$audit .= '<tr><th>Questions Completed</th><td><i class="far  '.( ( $audit_record->questions_completed == 1 ) ? " fa-check-circle text-green " : " fa-times-circle text-red" ).' "></i></td></tr>';
					$audit .= '<tr><th>Documents Uploaded</th><td><i class="far  '.( ( $audit_record->documents_uploaded == 1 ) ? " fa-check-circle text-green " : " fa-times-circle text-red" ).' "></i></td></tr>';
					$audit .= '<tr><th>Signature Uploaded</th><td><i class="far  '.( ( $audit_record->signature_uploaded == 1 ) ? " fa-check-circle text-green " : " fa-times-circle text-red" ).' "></i></td></tr>';
					$audit .= '<tr><th colspan="2">&nbsp;</th></tr>';
					$audit .= '<tr><th colspan="2"><span style="font-weight:400">RESPONSES</span><hr></th></tr>';
					$audit .= '<tr><td colspan="2"><table style="width:100%;display:table;font-size:90%">';
					$audit .= '<tr><th width="10%">ID</th><th width="50%">Audit Question</th><th width="20%">Response</th><th width="20%">Extra Info</th></tr>';
						foreach( $audit_record->audit_responses as $k=>$audit_item ){ $k++;
							$audit .= '<tr><td>'.$k.'</td><td>'.$audit_item->question.'</td><td>'.$audit_item->response.'</td><td>'.$audit_item->response_extra.'</td></tr>';
						}
					$audit .= '</table></td></tr>';
			$audit .= '</table>';
		}
		return $audit;
	}

	/** Add new location **/
	public function add_new_location(){

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
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$new_location = $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/add_new_location', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  = ( isset( $new_location->location ) ) ? $new_location->location : null;
			$message	  = ( isset( $new_location->message ) ) ? $new_location->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 	= 1;
				$return_data['location']= $new_location;
				$text_color 			= 'green';
			}
			$return_data['status_msg'] = '<span class="text-'.$text_color.'">'.$message.'</span>';
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	/** Upload multiple assets. This is a Web-client only feature **/
	public function upload_assets( $account_id = false ){

		if( $this->account_service->check_account_status( $account_id ) ){

			$message = ( $this->session->flashdata('message') ) ? $this->session->flashdata('message') : 'Something wrong with the supplied Account ID';

			if( !empty( $_FILES['upload_file']['name'] ) ){

				$process_file = $this->asset_service->upload_assets( $account_id );

				if( $process_file ){
					redirect( '/webapp/asset/review/'.$account_id );
				}

			}else{
				$this->session->set_flashdata( 'message', 'No files selected. Please try again.' );
			}

		}else{
			$message = ( $this->session->flashdata('message') ) ? $this->session->flashdata('message') : 'Something wrong with the supplied Account ID';
			$this->session->set_flashdata( 'message', $message );
			redirect( '/webapp/asset/create' );
		}
	}

	/** Review People **/
	public function review( $account_id = false ){

		if( $this->account_service->check_account_status( $account_id ) ){
			$pending 		= $this->asset_service->get_pending_upload_records( $account_id );
			$data['pending']= ( !empty( $pending ) ) ? $pending : null;

			$asset_types 	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/asset_types', ['account_id'=>$this->user->account_id, 'grouped'=>1 ], ['auth_token'=>$this->auth_token], true );
			$data['asset_types']= ( isset( $asset_types->asset_types ) ) ? $asset_types->asset_types : null;

			$this->_render_webpage('asset/asset_pending_creation.php', $data);
		}else{
			$message = ( $this->session->flashdata('message') ) ? $this->session->flashdata('message') : 'Something wrong with the supplied Account ID';
			$this->session->set_flashdata( 'message', $message );
			redirect( '/webapp/asset/create' );
		}

	}

	/** Do address-contact creation **/
	public function update_temp_data( $temp_asset_id = false ){

		$return_data = [
			'status'=>0
		];

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, 'details' );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata = ( $this->input->post('assets') ) ? $this->input->post('assets') : false;
			if( !empty( $postdata[$temp_asset_id] ) ){
				$update_temp_data= $this->asset_service->update_temp_data( $this->user->account_id, $temp_asset_id, $postdata[$temp_asset_id] );
			}
			$message = ( !empty( $update_temp_data ) ) ? 'Temp record updated successfully' : 'Something went wrong, update failed!';
			if( !empty( $update_temp_data ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	/** Create assets records **/
	public function create_assets(){

		$return_data = [
			'status'=>0,
			'all_done'=>0,
		];

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, 'details' );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata = ( $this->input->post() ) ? $this->input->post() : false;
			if( !empty( $postdata ) ){
				$total_records = count( array_keys( $postdata['assets'] ) );
				$new_asset_records = $this->asset_service->create_bulk_assets( $this->user->account_id, $postdata );
			}
			$message = ( !empty( $new_asset_records ) ) ? count( $new_asset_records ).' new asset records created successfully' : 'Something went wrong, update failed!';
			if( !empty( $new_asset_records ) ){

				$return_data['status'] 	 = 1;
				$return_data['all_done'] = ( $total_records == count( $new_asset_records ) ) ? 1 : 0;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	/**  VIew records by ENd OF LIFE (EOL) status group **/
	public function eol( $period_days = '180' ){

		$get = $this->input->get();

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'details' );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		}else{

			$postdata 	  		 	= array_merge( ['account_id'=>$this->user->account_id], $this->input->get() );
			$eol_statuses 		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/eol_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['eol_statuses']	= ( isset( $eol_statuses->eol_statuses ) ) ? $eol_statuses->eol_statuses : null;
			$data['selected_group'] = ( !empty( $this->input->get( 'period_days' ) ) || ( $this->input->get( 'period_days' ) == '0' ) ) ? $this->input->get( 'period_days' ) : '365';
			$this->_render_webpage('asset/end_of_life', $data);
		}
	}

	/** Add a new Cost Item **/
	public function add_cost_item(){

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
			$postdata 	  	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$cost_item = $this->webapp_service->api_dispatcher( $this->api_end_point.'cost/create', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  	= ( isset( $cost_item->cost_item ) ) ? $cost_item->cost_item : null;
			$message	  	= ( isset( $cost_item->message ) )  ? $cost_item->message : 'Oops! There was an error processing your request.';

			if( !empty( $result ) ){
				$return_data['status'] 		= 1;
				$return_data['cost_item'] 	= $result;
				$text_color 				= 'green';
			}
			$return_data['status_msg'] = '<span class="text-'.$text_color.'">'.$message.'</span>';
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	/**
	* Delete cost item record
	**/
	public function delete_cost_item( $asset_id = false ){
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
			$postdata 	  	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$drop_cost_item = $this->webapp_service->api_dispatcher( $this->api_end_point.'cost/delete_cost_item', $postdata, ['auth_token'=>$this->auth_token], true );
			$result		    = ( isset( $drop_cost_item->status ) ) ? $drop_cost_item->status : null;
			$message	    = ( isset( $drop_cost_item->message ) ) ? $drop_cost_item->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status']= 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/** Add new Asset Type **/
	public function add_asset_type(){

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
			$postdata 	  	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$asset_type 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/add_asset_type', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  	= ( isset( $asset_type->asset_type ) ) ? $asset_type->asset_type : null;
			$message	  	= ( isset( $asset_type->message ) )  ? $asset_type->message : 'Oops! There was an error processing your request.';

			if( !empty( $result ) ){
				$return_data['status'] 		= 1;
				$return_data['asset_type']  = $result;
				$text_color 				= 'green';
			}
			$return_data['status_msg'] = '<span class="text-'.$text_color.'">'.$message.'</span>';
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/*
	* Update Asset Type details
	*/

	public function update_asset_type( $asset_type_id = false, $page = 'details' ){
		$color_class  = 'red';
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$asset_id = ( $this->input->post( 'asset_type_id' ) ) ? $this->input->post( 'asset_type_id' ) : ( !empty( $asset_type_id ) ? $asset_type_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$asset_type_id		 = ( !empty( $this->input->post( 'asset_type_id' ) ) ) ? $this->input->post( 'asset_type_id' ) : $asset_type_id;
			$postdata 	  		 = array_merge( ['account_id'=>$this->user->account_id, 'asset_type_id'=>$asset_type_id ], $this->input->post() );
			
			$updates_asset_type = $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/update_asset_type', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  		 = ( isset( $updates_asset_type->asset_type ) ) ? $updates_asset_type->asset_type : null;
			$message	  		 = ( isset( $updates_asset_type->message ) ) ? $updates_asset_type->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status']		= 1;
				$return_data['asset_type']  = $result;
				$color_class				= 'auto';
			}
			$return_data['status_msg'] = '<span class="text-'.$color_class.'">'.$message.'</span>';
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	/*
	* Schedules lookup / search
	*/
	public function schedules_lookup( $asset_id = false, $page = 'details' ){

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

			$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/schedules', $postdata, ['auth_token'=>$this->auth_token], true );

			$schedules			= ( isset( $search_result->schedules ) ) ? $search_result->schedules : null;

			if( !empty( $schedules ) ){

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

				$return_data = $this->load_schedules_view( $schedules );
				if( !empty($pagination) ){
					$return_data .= '<tr style="border-bottom:1px solid #red" ><td colspan="5" style="padding: 0; border-bottom:#f4f4f4">';
						$return_data .= $page_display.$pagination;
					$return_data .= '</td></tr>';
				}
			}else{
				$return_data .= '<tr><td colspan="5" style="padding: 0;"><br/>';
					$return_data .= $this->config->item("no_records");
				$return_data .= '</td></tr>';
			}
		}

		print_r( $return_data );
		die();
	}

	/*
	* Prepare schedules view
	*/
	private function load_schedules_view( $schedules_data ){
		$return_data = '';
		if( !empty( $schedules_data ) ){
			foreach( $schedules_data as $k => $schedule_details ){
				$return_data .= '<tr>';
					$return_data .= '<td><a href="'.base_url( '/webapp/job/schedule_profile/'.$schedule_details->schedule_id).'" >'.$schedule_details->schedule_name.'</a></td>';
					$return_data .= '<td>'.( date( 'd-m-Y', strtotime( $schedule_details->first_activity_due_date ) ) ).'</td>';
					$return_data .= '<td>'.$schedule_details->frequency_name.'</td>';
					$return_data .= '<td>'.$schedule_details->activities_total.'</td>';
					$return_data .= '<td>'.$schedule_details->schedule_status.'</td>';
					$return_data .= '<td>';
						$return_data .= '<class class="row pull-right">';
							$return_data .= '<div class="col-md-6" ><a href="'.base_url( '/webapp/job/schedule_profile/'.$schedule_details->schedule_id).'" ><i title="Click here to view this schedule record" class="fas fa-edit text-blue pointer"></i></a></div>';
							$return_data .= '<div class="col-md-6 delete-item" ><i title="Click here to delete this Schedule" class="delete-item fas fa-trash-alt text-red pointer"></i></div>';
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


	//Manage Asset types
	function asset_types( $asset_type_id = false, $page = 'details' ){

		$toggled		= ( !empty( $this->input->get( 'toggled' ) ) ? $this->input->get( 'toggled' ) : false );
		$section 		= ( !empty( $page) ) ? $page : ( !empty( $this->input->get( 'page' ) ) ? $this->input->get( 'page' ) : 'details' );
		$asset_type_id  = ( !empty( $asset_type_id) ) ? $asset_type_id : ( !empty( $this->input->get( 'asset_type_id' ) ) ? $this->input->get( 'asset_type_id' ) : ( ( !empty( $this->input->get( 'id' ) ) ? $this->input->get( 'id' ) : null ) ) );

		if( !empty( $asset_type_id ) ){
			$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
			if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
				$this->_render_webpage( 'errors/access-denied', false );
			}else{

				$default_params = $params =[ 'account_id'=>$this->user->account_id, 'where'=>[ 'asset_type_id'=>$asset_type_id ], 'limit'=>-1 ];

				$asset_type_details = $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/asset_types', $params, [ 'auth_token'=>$this->auth_token ], true );

				if( !empty( $asset_type_details->asset_types ) ){

					$data['asset_type_details']		= $asset_type_details->asset_types;

					$asset_type_attributes 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/asset_type_attributes', [ 'account_id'=>$this->user->account_id, 'asset_type_id'=>$asset_type_id ], ['auth_token'=>$this->auth_token], true );
					$data['asset_type_attributes']	= ( isset( $asset_type_attributes->asset_type_attributes ) ) ? $asset_type_attributes->asset_type_attributes : null;

					$attributes_bucket 				= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/asset_type_attributes', [ 'account_id'=>$this->user->account_id, 'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
					$data['attributes_bucket']		= ( isset( $attributes_bucket->asset_type_attributes ) ) ? $attributes_bucket->asset_type_attributes : null;

					$response_types	 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/response_types', $default_params, ['auth_token'=>$this->auth_token], true );
					$data['response_types']		= ( isset( $response_types->response_types ) ) ? $response_types->response_types : null;

					$audit_categories	 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/audit_categories', [ 'account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
					$data['asset_categories']	= ( isset( $audit_categories->audit_categories ) ) ? $audit_categories->audit_categories : null;

					$data['general_file_types'] = generic_file_types();

					$data['asset_groups']		= asset_sub_categories();

					$disciplines	 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'discipline/account_disciplines', [ 'account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
					$data['disciplines']		= ( isset( $disciplines->account_disciplines ) ) ? $disciplines->account_disciplines : null;

					$this->_render_webpage( 'asset/asset_type_profile', $data );
				} else {
					redirect( 'webapp/asset/asset_types', 'refresh');
				}
			}
		} else {
			$this->_render_webpage( 'asset/asset_types', false, false, true );
		}
	}

	/*
	* Asset Types List
	*/
	public function asset_types_list( $page = 'details' ){

		$return_data = '';

		$section 	 = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){

			$return_data .= $this->config->item( 'ajax_access_denied' );

		}else{

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

			$search_result		= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/asset_types', $postdata, [ 'auth_token'=>$this->auth_token ], true );
			$asset_types		= ( isset( $search_result->asset_types ) ) ? $search_result->asset_types : null;

			if( !empty( $asset_types ) ){

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

				$return_data = $this->load_asset_types_view( $asset_types );
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
	* Prepare Asset types views
	*/
	private function load_asset_types_view( $asset_types_data ){
		$return_data = '';
		if( !empty( $asset_types_data ) ){

			$asset_sub_categories = asset_sub_categories();

			foreach( $asset_types_data as $k => $asset_type_details ){

				$asset_group 		= $asset_type_details->asset_group;
				$asset_sub_category	= ( !empty( $asset_group ) && !empty( $asset_sub_categories[$asset_group] ) ) ? $asset_sub_categories[$asset_group] : false;

				$return_data .= '<tr>';
					$return_data .= '<td><a href="'.base_url( '/webapp/asset/asset_types/'.$asset_type_details->asset_type_id ).'" >'. ucwords( $asset_type_details->asset_type ) .'</a></td>';
					$return_data .= '<td>'.ucwords( $asset_type_details->asset_group ). ( !empty( $asset_sub_category ) ? ' <small>('.$asset_sub_category.')</small>' : '' ) .'</td>';
					$return_data .= '<td>'.$asset_type_details->asset_type_desc.'</td>';
					$return_data .= '<td>'.( !empty( $asset_type_details->date_created ) ? date( 'd-m-Y H:i:s', strtotime( $asset_type_details->date_created ) ) : '' ).'</td>';
					$return_data .= '<td>'.( !empty( $asset_type_details->is_active ) ? 'Active' : 'Disabled' ).'</td>';
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


	/**
	* Create new Asset type
	*/
	public function add_asset_type_attribute(){

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
			$postdata 	  		 = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$asset_type_attribute= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/add_asset_type_attribute', $postdata, ['auth_token'=>$this->auth_token] );

			$result		  	 	 = ( isset( $asset_type_attribute->asset_type_attribute ) ) ? $asset_type_attribute->asset_type_attribute : null;
			$message	  	 	 = ( isset( $asset_type_attribute->message ) ) ? $asset_type_attribute->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 				= 1;
				$return_data['asset_type_attribute']= $result;
				$text_color 						= 'auto';
			}
			$return_data['status_msg'] = '<span class="text-'.$text_color.'">'.$message.'</span>';
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/**
	* Link an asset to a assets
	**/
	public function link_assets(){

		$section 	 = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
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
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	  	 = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$linked_assets 	 = $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/link_assets' , $postdata, ['auth_token'=>$this->auth_token] );

			$result		  	 = ( isset( $linked_assets->linked_assets ) ) 	? $linked_assets->linked_assets : null;
			$message	  	 = ( isset( $linked_assets->message ) )  		? $linked_assets->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 	 = 1;
				$text_color 			 = 'auto';
			}
			$return_data['status_msg'] = '<span class="text-'.$text_color.'">'.$message.'</span>';
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/**
	* Un-link Assets
	**/
	public function unlink_assets( $asset_id = false ){
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
			$postdata 		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$unlink_asset	= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/unlink_assets', $postdata, ['auth_token'=>$this->auth_token] );
			$result			= ( isset( $unlink_asset->status ) )  ? $unlink_asset->status : null;
			$message		= ( isset( $unlink_asset->message ) ) ? $unlink_asset->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/** Fetch Asset type attributes **/
	public function fetch_attributes(){
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
			$postdata 				= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$asset_type_attributes	= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/asset_type_attributes', $postdata, ['auth_token'=>$this->auth_token], true );
			$result					= ( isset( $asset_type_attributes->asset_type_attributes ) )  	? $asset_type_attributes->asset_type_attributes : null;
			$message				= ( isset( $asset_type_attributes->message ) ) 			? $asset_type_attributes->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 			= 1;
				$return_data['attributes_data'] = $this->load_attributes_view( $result );
			} else {
				$return_data['status_msg'] = 'There\'s currently no attributes set for this Asset type!';
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	//Load Attributes view
	private function load_attributes_view( $attributes_data = false ){
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
								$return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_id]" 	value="'.$attribute->attribute_id.'">';
								$return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_name]" 	value="'.htmlspecialchars( $attribute->attribute_name ).'">';
								$return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][response_type]" 	value="'.$attribute->response_type.'">';
								$return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][ordering]" 		value="'.$attribute->ordering.'">';
								$return_data .= '<input type="text" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_value]" 	value="" class="form-control '.$append_classes.'" '.$apply_max_length.' placeholder="Enter the '.ucwords( $attribute->attribute_name ).' here..." >';
							$return_data .= '</div>';

							break;

						case 'long_text':

							$return_data .= '<div class="input-group form-group">';
								$return_data .= '<label class="input-group-addon" >'.ucwords( $attribute->attribute_name ).'</label>';
								$return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_id]" 	value="'.$attribute->attribute_id.'">';
								$return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_name]" 	value="'.htmlspecialchars( $attribute->attribute_name ).'">';
								$return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][response_type]" 	value="'.$attribute->response_type.'">';
								$return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][ordering]" 		value="'.$attribute->ordering.'">';
								$return_data .= '<textarea type="text" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_value]" 	value="" rows="4" class="form-control '.$append_classes.'" class="form-control" placeholder="Enter the '.ucwords( $attribute->attribute_name ).' here..." ></textarea>';
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
														$return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_id]" 	value="'.$attribute->attribute_id.'">';
														$return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_name]" 	value="'.htmlspecialchars( $attribute->attribute_name ).'">';
														$return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][response_type]" 	value="'.$attribute->response_type.'">';
														$return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][ordering]" 		value="'.$attribute->ordering.'">';
														$return_data .= '<input type="radio"    name="asset_attributes['.$attribute->attribute_id.'][attribute_value]"  value="'.$option.'" id="optionsRadio'.$k.'" > '.$option;
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
															$return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_id]" 	value="'.$attribute->attribute_id.'">';
															$return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_name]" 	value="'.htmlspecialchars( $attribute->attribute_name ).'">';
															$return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][response_type]" 	value="'.$attribute->response_type.'">';
															$return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][ordering]" 		value="'.$attribute->ordering.'">';
															$return_data .= '<input type="checkbox" name="asset_attributes['.$attribute->attribute_id.'][attribute_value][]" class="check-options check-opts'.$attribute->attribute_id.'" data-attribute_id="'.$attribute->attribute_id.'" value="'.$option.'" id="optionsCheckbox'.$k.'" > '.$option;
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
								$return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_id]" 	value="'.$attribute->attribute_id.'">';
								$return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_name]" 	value="'.htmlspecialchars( $attribute->attribute_name ).'">';
								$return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][response_type]" 	value="'.$attribute->response_type.'">';
								$return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][ordering]" 		value="'.$attribute->ordering.'">';
								$return_data .= '<input type="hidden" 	name="asset_attributes['.$attribute->attribute_id.'][attribute_value]" 	value="'.$attribute->response_type.'" >';
								$return_data .= '<span class="control-fileupload pointer">';
									$return_data .= '<label for="asset_image" class="pointer text-left">Please choose a file on your computer <i class="fas fa-upload"></i></label><input name="user_files[]" type="file" id="asset_image" >';
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


	/**  View records by Audit Result status **/
	public function audit_result_status( $result_group = 'all' ){

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'details' );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		}else{
			$postdata 	  		 	= array_merge( ['account_id'=>$this->user->account_id], $this->input->get() );
			$audit_result_statuses 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/result_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['result_statuses']= ( isset( $audit_result_statuses->audit_result_statuses ) ) ? $audit_result_statuses->audit_result_statuses : null;
			$data['selected_group'] = ( !empty( $this->input->get( 'group' ) ) ) ? $this->input->get( 'group' ) : 'all';
			$this->_render_webpage('site/site_audit_result_status', $data);
		}
	}


	/** Get list of Zones and Locations **/
	public function get_site_zones( $site_id = false ){
		$site_id = ( $this->input->post( 'site_id' ) ) ? $this->input->post( 'site_id' ) : $site_id;
		if( $site_id ){
			$site_zones_data 		= '';
			$zone_locations_data 	= '';
			$site_zones 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/site_zones', ['account_id'=>$this->user->account_id, 'where'=>['site_id'=>$site_id, 'limit'=> -1 ] ], ['auth_token'=>$this->auth_token], true );
			$site_zones				= ( isset( $site_zones->site_zones ) )  ? $site_zones->site_zones : null;
			if( !empty( $site_zones ) ){
				$site_zones_data 	 = '<option value="">Please select Zone...</option>';
				$zone_locations_data = '<option value="">Please select Location...</option>';
				foreach( $site_zones as $zone ){
					$site_zones_data .= '<option value = "'.$zone->zone_id.'" >'.$zone->zone_name.'</option>';
					if( !empty( $zone->zone_locations ) ){
						foreach( $zone->zone_locations as $key => $location ){
							$zone_locations_data .= '<option value="'.$location->location_id.'" class="opts opt'.$zone->zone_id.'" >'.$location->location_name.'</option>';
						}
					}
				}
			} else {
				$site_zones_data 		= '<option>No Zones available yet</option>';
				$zone_locations_data 	= '<option>No Locations available yet</option>';
			}

		} else {
			$site_zones_data = '<option disabled="disabled">Please .</option>';
		}
		$data['site_zones'] 	= $site_zones_data;
		$data['zone_locations'] = $zone_locations_data;

		echo json_encode( $data );
	}


	/** Get Site Locations **/
	public function get_site_locations( $site_id = false, $zone_id = false ){
		$site_id = ( $this->input->post( 'site_id' ) ) ? $this->input->post( 'site_id' ) : $site_id;
		$zone_id = ( $this->input->post( 'zone_id' ) ) ? $this->input->post( 'zone_id' ) : $zone_id;
		if( $site_id && $zone_id ){
			$site_locations_data 		= '';

			$data['site_locations'] 		= $postdata	= [];
			$postdata['account_id']			= $this->user->account_id;
			$postdata['site_id']			= ( !empty( $site_id ) ) ? ( int ) $site_id : false ;
			$postdata['where']['zone_id']	= ( !empty( $zone_id ) ) ? ( int ) $zone_id : false ;
			$api_call						= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/locations', $postdata, ['auth_token'=>$this->auth_token], true );

			$site_locations					= ( !empty( $api_call->site_locations ) ) ? $api_call->site_locations : false ;

			if( !empty( $site_locations ) ){
				$site_locations_data = '<option value="">Please select Location...</option>';
				foreach( $site_locations as $location ){
					$site_locations_data .= '<option value="'.$location->location_id.'" class="opts opt'.$location->location_id.'" >'.$location->location_name.'</option>';
				}
			} else {
				$site_locations_data 	= '<option>No Locations available yet</option>';
			}

		} else {
			$site_locations_data = '<option disabled="disabled">Please pick the Zone .</option>';
		}
		$data['site_locations'] = $site_locations_data;

		echo json_encode( $data );
	}
	
	
	//Manage Asset Type Attributes - Overview page
	function attributes( $attribute_id = false, $page = 'details' ){

		$toggled	= ( !empty( $this->input->get( 'toggled' ) ) 	? $this->input->get( 'toggled' ) 	: false );
		$section 	= ( !empty( $page) ) 							? $page 							: ( !empty( $this->input->get( 'page' ) ) ? $this->input->get( 'page' ) : 'details' );
		$attribute_id  	= ( !empty( $attribute_id) ) 				? $attribute_id 					: ( !empty( $this->input->get( 'attribute_id' ) ) ? $this->input->get( 'attribute_id' ) : ( ( !empty( $this->input->get( 'attribute_id' ) ) ? $this->input->get( 'attribute_id' ) : null ) ) );
		
		if( !empty( $attribute_id ) ){
			$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
			if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
				$this->_render_webpage( 'errors/access-denied', false );
			}else{
				
				$default_params = $params =[ 'account_id'=>$this->user->account_id, 'where'=>[ 'attribute_id'=>$attribute_id ] ];
				$attribute_details = $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/asset_type_attributes', $params, [ 'auth_token'=>$this->auth_token ], true );

				if( !empty( $attribute_details->asset_type_attributes ) ){
					$data['attribute_details']  = $attribute_details->asset_type_attributes;
					$response_types	 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/response_types', $default_params, ['auth_token'=>$this->auth_token], true );
					$data['response_types']	= ( isset( $response_types->response_types ) ) ? $response_types->response_types : null;
					$this->_render_webpage( 'asset/attributes/attribute_details_profile', $data );					
				}else{
					redirect( 'webapp/asset/manage_attributes', 'refresh' );
				}
			}
		} else {
			$this->_render_webpage( 'asset/attributes/manage_attributes', false, false, true );
		}
	}
	
	
	/*
	* Asset Type Attributes List / Search
	*/
	public function attributes_lookup( $page = 'details' ){

		$return_data = '';

		$section 	 = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		
		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){
			
			$return_data .= $this->config->item( 'ajax_access_denied' );
			
		}else{

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

			$search_result			= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/asset_type_attributes', $postdata, [ 'auth_token'=>$this->auth_token ], true );
			$asset_type_attributes	= ( isset( $search_result->asset_type_attributes ) ) ? $search_result->asset_type_attributes : null;

			if( !empty( $asset_type_attributes ) ){

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
				
				$return_data = $this->load_asset_type_attributes_view( $asset_type_attributes );
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
	* Asset Type Attributes views
	*/
	private function load_asset_type_attributes_view( $asset_type_attributes_data ){
		$return_data = '';
		if( !empty( $asset_type_attributes_data ) ){

			foreach( $asset_type_attributes_data as $k => $attribute ){
				$return_data .= '<tr>';
					$return_data .= '<td><a href="'.base_url( '/webapp/asset/attributes/'.$attribute->attribute_id ).'" >'.ucwords( $attribute->attribute_name ).'</a></td>';
					$return_data .= '<td>'.$attribute->attribute_ref.'</td>';									
					$return_data .= '<td>'.$attribute->response_type_alt.'</td>';									
					$return_data .= '<td>'.( ( is_array( $attribute->response_options ) ) ? implode( " | ", $attribute->response_options ) : ( is_object( $attribute->response_options ) ? json_encode( $attribute->response_options ) : $attribute->response_options ) ).'</td>';
					$return_data .= '<td><span class="pull-right">'.( ( $attribute->is_active == 1 ) ? "Active" : "In-active" ).'</span></td>';
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
	
	
	/**
	* Create new Generic Attribute
	*/
	public function new_attribute(){
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'details' );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		}else{
			$default_params 		= [ 'account_id'=>$this->user->account_id, 'limit'=>-1 ];
			$response_types	 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/response_types', $default_params, ['auth_token'=>$this->auth_token], true );
			$data['response_types']	= ( isset( $response_types->response_types ) ) ? $response_types->response_types : null;
			$this->_render_webpage( 'asset/attributes/attribute_add_new', $data );
		}
	}
	
	
	/** Update Asset Type Attribute **/
	public function update_asset_type_attribute( $attribute_id = false, $page = 'details' ){
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
			$updated_attribute	= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/update_asset_type_attribute', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  		= ( isset( $updated_attribute->asset_type_attribute ) ) ? $updated_attribute->asset_type_attribute : null;
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
	
	
	/** Delete Asset Type Attribute **/
	public function delete_asset_type_attribute( $attribute_id = false, $page = 'details' ){
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
			$updated_attribute	= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/delete_asset_type_attribute', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  		= ( isset( $updated_attribute->status ) )  ? $updated_attribute->status : null;
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
	
	//Manage Categories - Overview page
	function categories( $category_id = false, $page = 'details' ){
		
		$toggled	= ( !empty( $this->input->get( 'toggled' ) ) ? $this->input->get( 'toggled' ) : false );
		$section 	= ( !empty( $page) ) ? $page : ( !empty( $this->input->get( 'page' ) ) ? $this->input->get( 'page' ) : 'details' );
		$category_id  	= ( !empty( $category_id) ) ? $category_id : ( !empty( $this->input->get( 'category_id' ) ) ? $this->input->get( 'category_id' ) : ( ( !empty( $this->input->get( 'category_id' ) ) ? $this->input->get( 'category_id' ) : null ) ) );
		
		if( !empty( $category_id ) ){
			
			$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
			if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
				$this->_render_webpage( 'errors/access-denied', false );
			}else{
				$default_params 	= $params = [ 'account_id'=>$this->user->account_id, 'where'=>[ 'category_id'=>$category_id ] ];
				$category_details 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/audit_categories', $params, [ 'auth_token'=>$this->auth_token ], true );

				if( !empty( $category_details->audit_categories ) ){
					$data['category_details']  		= $category_details->audit_categories;
					$linked_audit_types  		= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/linked_audit_types', ['account_id'=>$this->user->account_id, 'category_id'=>$category_id ], ['auth_token'=>$this->auth_token], true );			
					$data['linked_audit_types']	= ( isset( $linked_audit_types->linked_audit_types ) ) ? $linked_audit_types->linked_audit_types : null;
					
					$assigned_operatives  		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/assigned_people', ['account_id'=>$this->user->account_id, 'category_id'=>$category_id ], ['auth_token'=>$this->auth_token], true );			
					$data['assigned_operatives']	= ( isset( $assigned_operatives->assigned_people ) ) ? $assigned_operatives->assigned_people : null;
					
					$this->_render_webpage( 'audit/categories/category_details_profile', $data );					
				}else{
					redirect( 'webapp/asset/categories', 'refresh' );
				}
			}
		} else {
			$this->_render_webpage( 'audit/categories/manage_categories', false, false, true );
		}
		
	}
}
