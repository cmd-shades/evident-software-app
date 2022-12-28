<?php

namespace Application\Modules\Web\Controllers;

class People extends MX_Controller {

	function __construct(){
		parent::__construct();

		if( !$this->identity() ){
			redirect('webapp/user/login', 'refresh');
		}

		$this->module_id 	   = $this->webapp_service->_get_module_id( $this->router->fetch_class() );
		$this->load->library('pagination');
		$this->load->model('serviceapp/People_model','people_service');
		$this->load->model('serviceapp/Address_model','address_service');

	}

	//redirect if needed, otherwise display the employee / people list
	function index(){

		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){
			//access denied
			$this->_render_webpage( 'errors/access-denied', false );
		}else{
			redirect('webapp/people/people', 'refresh');
		}
	}

	/** Get list of people **/
	public function people( $person_id = false ){

		if( $person_id ){
			redirect('webapp/people/profile/'.$person_id, 'refresh');
		}

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		#if( !$this->user->is_admin && !$module_access ){
		if( !$this->user->is_admin ){
			$this->_render_webpage( 'errors/access-denied', false );
		}else{
			$user_statuses	 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'user/statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['user_statuses'] 	= ( isset( $user_statuses->user_statuses ) ) ? $user_statuses->user_statuses : null;

			$departments		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/departments', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['departments'] 	= ( isset($departments->departments) ) ? $departments->departments : null;

			$this->_render_webpage('people/index', $data);
		}
	}

	//View user profile
	function profile( $person_id = false, $page = 'details' ){

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else if( $person_id ){

			$person_details		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/people', ['account_id'=>$this->user->account_id,'person_id'=>$person_id], ['auth_token'=>$this->auth_token], true );
			$data['person_details']	= ( isset($person_details->people) ) ? $person_details->people : null;

			if( !empty( $data['person_details'] ) ){

				$data["template_version"]			= 1;

				## overview attributes - ZONE 1
				$data["overview_attributes"] 	  	= false;
				$postdata["account_id"] = $this->user->account_id;
				$postdata["where"] 		= [
					"module_id"		=> $this->module_id,
					"zone_id"		=> "1",
				];
				$API_call	 	  				= $this->webapp_service->api_dispatcher( $this->api_end_point.'attribute/attributes', $postdata, ['auth_token'=>$this->auth_token], true );
				$data['overview_attributes']	= ( !empty( $API_call->attributes ) ) ? $API_call->attributes : null;
				## overview attributes - end

						## responses to the attributes from the zone #1
						$data['overview_responses']		= false;
						$postdata 						= false;
						$postdata["account_id"] 		= $this->user->account_id;
						$postdata["where"] 				= [
							"module_id"			=> $this->module_id,
							"module_item_id"	=> ( !empty( $module_item_id ) ) ? ( int ) $module_item_id : false,
							"profile_id"		=> $person_id,
							"zone_id"			=> 1
						];

						$API_call	 	  				= $this->webapp_service->api_dispatcher( $this->api_end_point.'attribute/responses', $postdata, ['auth_token'=>$this->auth_token], true );
						$data['overview_responses']		= ( !empty( $API_call->responses ) ) ? $API_call->responses : null;

				$run_admin_check 	= false;

				#Get allowed access for the logged in user
				$data['permissions']= $item_access;
				$data['active_tab']	= $page;

				$module_items 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'access/account_modules_items', ['account_id'=>$this->user->account_id, 'module_id'=>$this->module_id ], ['auth_token'=>$this->auth_token], true );
				$data['module_tabs']= ( isset( $module_items->module_items ) ) ? $module_items->module_items : null;

				$data['unordered_tabs'] = ( isset( $module_items->module_items ) ) ? $module_items->module_items : null;

				$reordered_tabs 		 = reorder_tabs( $data['module_tabs'] );
				$data['module_tabs'] 	 = ( !empty( $reordered_tabs['module_tabs'] ) ) ? $reordered_tabs['module_tabs'] : $data['module_tabs'];

				$data['more_list_active']= ( !empty( $reordered_tabs['more_list'] ) && in_array( $page, $reordered_tabs['more_list'] )  ) ? true : false;

				switch( $page ){
					case 'assets':
						$assigned_assets	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/assets', [ 'limit' => -1, 'account_id'=>$this->user->account_id,'where'=>array('assignee' => $person_id)], ['auth_token'=>$this->auth_token], true );
						$data['assigned_assets']= ( isset( $assigned_assets->assets ) ) ? $assigned_assets->assets : null;

						$data['include_page'] = 'person_assets.php';
						break;

					case 'contacts':
						$run_admin_check 	  = true;
						$address_types	 	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'address/address_types', ['account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
						$data['address_types']= ( isset( $address_types->address_types ) ) ? $address_types->address_types : null;

						$address_contacts	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'people/address_contacts', ['account_id'=>$this->user->account_id, 'person_id'=>$person_id ], ['auth_token'=>$this->auth_token], true );
						$data['address_contacts']= ( isset( $address_contacts->address_contacts ) ) ? $address_contacts->address_contacts : null;

						$data['relationships'] = contact_relationships();

						$data['include_page'] = 'person_contacts.php';
						break;

					case 'health':
						$run_admin_check 	  = true;
						$data['include_page'] = 'person_health.php';
						break;

					case 'positions':
						$run_admin_check 	= true;
						$departments		= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/departments', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
						$data['departments']= ( isset($departments->departments) ) ? $departments->departments : null;

						$job_titles		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/job_titles', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
						$data['job_titles'] = ( isset( $job_titles->job_titles ) ) ? $job_titles->job_titles : null;

						$job_positions		  = $this->webapp_service->api_dispatcher( $this->api_end_point.'people/job_positions', ['account_id'=>$this->user->account_id,'person_id'=>$person_id], ['auth_token'=>$this->auth_token], true );
						$data['job_positions']= ( isset( $job_positions->job_positions ) ) ? $job_positions->job_positions : null;

						$data['include_page'] = 'person_positions.php';
						break;
					case 'events':
						$run_admin_check 	= true;
						$job_titles		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/job_titles', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
						$data['job_titles'] = ( isset( $job_titles->job_titles ) ) ? $job_titles->job_titles : null;

						$data['include_page'] = 'person_events.php';
						break;
					case 'documents':
						$personal_documents		= $this->webapp_service->api_dispatcher( $this->api_end_point.'document_handler/document_list', ['account_id'=>$this->user->account_id, 'person_id'=>$person_id, 'document_group'=>'people' ], ['auth_token'=>$this->auth_token], true );
						$data['personal_documents']= ( isset( $personal_documents->documents->{$this->user->account_id} ) ) ? $personal_documents->documents->{$this->user->account_id} : null;
						$data['include_page'] = 'person_documents.php';
						break;


					case 'attributes':
						$data['show_information']		= 0;

						$data['excluded_response_types'] = ["file", "signature"];
						$data['response_types_w_options'] = ["radio","checkbox","select" ];

						$postdata 						= false;
						$module_item_id					= false;

						$postdata["account_id"] 		= $this->user->account_id;
						$postdata["module_id"] 			= $this->module_id;
						$postdata["module_item_name"] 	= $page;
						$API_call	 	  				= $this->webapp_service->api_dispatcher( $this->api_end_point.'access/module_items', $postdata, ['auth_token'=>$this->auth_token], true );
						$module_item					= ( !empty( $API_call->module_items ) ) ? $API_call->module_items : null;
						$module_item_id					= $module_item[0]->module_item_id;
						$data['module_item_id']			= $module_item_id;
						$data['module_id']				= $this->module_id;

						$data['manage_attributes']		= false;
						$postdata 						= false;
						$postdata["account_id"] 		= $this->user->account_id;
						$postdata["where"] 				= [
							"module_item_id"	=> ( !empty( $module_item_id ) ) ? ( int ) $module_item_id : false,
							"module_id"			=> $this->module_id,
							"zone_id"			=> "2",
						];
						$API_call	 	  				= $this->webapp_service->api_dispatcher( $this->api_end_point.'attribute/attributes', $postdata, ['auth_token'=>$this->auth_token], true );
						$data['manage_attributes']		= ( !empty( $API_call->attributes ) ) ? $API_call->attributes : null;

						## responses to the attributes
						$data['manage_responses']		= false;
						$postdata 						= false;
						$postdata["account_id"] 		= $this->user->account_id;
						$postdata["where"] 				= [
							"module_id"			=> $this->module_id,
							"module_item_id"	=> ( !empty( $module_item_id ) ) ? ( int ) $module_item_id : false,
							"profile_id"		=> $person_id,
						];
						$API_call	 	  				= $this->webapp_service->api_dispatcher( $this->api_end_point.'attribute/responses', $postdata, ['auth_token'=>$this->auth_token], true );
						$data['manage_responses']		= ( !empty( $API_call->responses ) ) ? $API_call->responses : null;

						## get attribute sections
						$data['attribute_sections']		= false;
						$postdata 						= false;
						$postdata["account_id"] 		= $this->user->account_id;
						$postdata["where"] 				= [
							"module_id"			=> $this->module_id,
							"module_item_id"	=> ( !empty( $module_item_id ) ) ? ( int ) $module_item_id : false,
							/* "organized"			=> false */
						];
						$API_call	 	  				= $this->webapp_service->api_dispatcher( $this->api_end_point.'attribute/sections', $postdata, ['auth_token'=>$this->auth_token], true );
						$data['attribute_sections']		= ( !empty( $API_call->status ) && !empty( $API_call->sections ) ) ? $API_call->sections : false ;

						## get attribute groups
						$data['attribute_groups']		= false;
						$postdata 						= false;
						$postdata["account_id"] 		= $this->user->account_id;
						$postdata["where"] 				= [
							"module_id"			=> $this->module_id,
							"module_item_id"	=> ( !empty( $module_item_id ) ) ? ( int ) $module_item_id : false,
							"organized"			=> true
						];
						$API_call	 	  				= $this->webapp_service->api_dispatcher( $this->api_end_point.'attribute/groups', $postdata, ['auth_token'=>$this->auth_token], true );
						$data['attribute_groups']		= ( !empty( $API_call->status ) && !empty( $API_call->groups ) ) ? $API_call->groups : false ;


						## get response types
						$data['response_types']			= false;
						$postdata 						= false;
						$postdata["account_id"] 		= $this->user->account_id;
						$postdata["where"] 				= [];
						$API_call	 	  				= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/response_types', $postdata, ['auth_token'=>$this->auth_token], true );
						$data['response_types']		= ( !empty( $API_call->status ) && !empty( $API_call->response_types ) ) ? $API_call->response_types : false ;

						$data['include_page'] 			= 'person_attributes.php';
						break;
					case 'cost':
						$cost_tracking	  	  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'cost/cost_items', ['account_id'=>$this->user->account_id, 'where'=>[ 'person_id'=>$person_id ] ], ['auth_token'=>$this->auth_token], true );
						$data['cost_tracking'] 	= ( isset( $cost_tracking->cost_items) ) ? $cost_tracking->cost_items : null;

						$cost_item_types	  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'cost/cost_item_types', ['account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
						$data['cost_item_types']= ( isset( $cost_item_types->cost_item_types) ) ? $cost_item_types->cost_item_types : null;

						$data['include_page'] 	= 'site_cost_tracking.php';
						break;

					case 'workpattern':
					case 'work_pattern':

						$data['week_days'] 				= week_days();

						#$personal_skills	  	   = $this->webapp_service->api_dispatcher( $this->api_end_point.'people/personal_skills', ['account_id'=>$this->user->account_id, 'where'=>[ 'person_id'=>$person_id ] ], ['auth_token'=>$this->auth_token], true );
						#$data['personal_skills'] = ( isset( $personal_skills->personal_skills) ) ? $personal_skills->personal_skills : null;

						$skills					 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'diary/skills', [ 'account_id'=>$this->user->account_id ], [ 'auth_token'=>$this->auth_token ], true );
						$data['available_skills']		= ( isset( $skills->skills ) ) ? $skills->skills : null;

						$data['personal_skills']		= ( !empty( $data['person_details']->personal_skills ) ) ? $data['person_details']->personal_skills : false;

						$data['linked_skills']			= ( !empty( $data['personal_skills'] ) ) ? array_column( $data['personal_skills'], 'skill_id' ) : [];

						$regions						= $this->webapp_service->api_dispatcher( $this->api_end_point.'diary/regions', [ 'account_id'=>$this->user->account_id ], [ 'auth_token'=>$this->auth_token ], true );
						$data['available_regions']		= ( isset( $regions->regions ) ) ? $regions->regions : null;

						$data['assigned_regions']		= ( !empty( $data['person_details']->assigned_regions ) ) ? $data['person_details']->assigned_regions : false;
						$data['linked_regions']			= ( !empty( $data['assigned_regions'] ) ) ? array_column( $data['assigned_regions'], 'region_id' ) : [];

						$data['preset_shifts_patterns'] = preset_shifts_patterns();
						$data['shift_allowed_times'] 	= shift_allowed_times();

						$data['include_page'] 			= 'person_work_pattern.php';
						break;

					case 'details':
					default:
						$data['countries']		= $this->ssid_common->get_countries();

						$people_categories		= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/people_category', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
						$data['people_categories'] 	= ( !empty( $people_categories->categories ) ) ? $people_categories->categories : null;

						$departments		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/departments', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
						$data['departments'] 	= ( isset($departments->departments) ) ? $departments->departments : null;

						$user_statuses		  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'user/statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
						$data['user_statuses'] 	= ( isset($user_statuses->user_statuses) ) ? $user_statuses->user_statuses : null;

						$data['include_page'] 	= 'person_details.php';
						break;
				}

				//Run the admin check if tab needs only admin
				if( !empty( $run_admin_check ) ){
					if( ( !admin_check( $this->user->is_admin, false, ( !empty( $data['permissions'] ) ? $data['permissions']->is_admin : false ) ) ) ){
						$data['admin_no_access'] = true;
					}
				}

			} else {
				$this->_render_webpage( 'errors/profile-data-not-found', false );
			}

			$this->_render_webpage('people/profile', $data, '');
		}else{
			redirect('webapp/people', 'refresh');
		}
	}

	/** Create new people **/
	public function create( $page = 'details' ){

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		}else{
			$departments	 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/departments', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['departments']		= ( isset($departments->departments) ) ? $departments->departments : null;

			$job_titles		 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/job_titles', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['job_titles'] 		= ( isset( $job_titles->job_titles ) ) ? $job_titles->job_titles : null;

			$people_categories			= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/people_category', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['people_categories'] 	= ( !empty( $people_categories->categories ) ) ? $people_categories->categories : null;

			$user_types 				= $this->webapp_service->api_dispatcher( $this->api_end_point.'user/user_types', ['account_id'=>$this->user->account_id], false, true );
			$data['user_types']			= ( isset( $user_types->user_types ) ) ? $user_types->user_types : null;

			$this->_render_webpage('people/person_create_new', $data);
		}
	}

	/** Do people creation **/
	public function create_person(){
		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$return_data = [
			'status'=>0
		];

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		} else {
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$new_people	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'people/create', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  = ( isset($new_people->person) ) ? $new_people->person : null;
			$message	  = ( isset($new_people->message) ) ? $new_people->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
				$return_data['person']   = $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	/**
	* Delete people (set as archived )
	**/
	public function delete_person( $person_id = false ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$person_id = ( $this->input->post( 'person_id' ) ) ? $this->input->post( 'person_id' ) : ( !empty( $person_id ) ? $person_id : null );

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_delete ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$delete_people = $this->webapp_service->api_dispatcher( $this->api_end_point.'people/delete', $postdata, ['auth_token'=>$this->auth_token], true );
			$result		  = ( isset($delete_people->status) ) ? $delete_people->status : null;
			$message	  = ( isset($delete_people->message) ) ? $delete_people->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status']= 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	/*
	* People lookup / search
	*/
	public function lookup( $page = 'details' ){

		$return_data = '';

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){

			$return_data .= $this->config->item( 'ajax_access_denied' );

		}else{

			# Setup search parameters
			$search_term   = ( $this->input->post( 'search_term' ) ) ? $this->input->post( 'search_term' ) : false;
			$where   	   = ( $this->input->post( 'where' ) ) ? $this->input->post( 'where' ) : false;
			$limit		   = ( !empty( $where['limit'] ) )  ? $where['limit']  : (  ( $this->input->post( 'limit') ) ? $this->input->post( 'limit' ) : DEFAULT_LIMIT );
			$start_index   = ( $this->input->post( 'start_index' ) ) ? $this->input->post( 'start_index' ) : DEFAULT_OFFSET;
			$offset		   = ( !empty( $start_index ) ) ? ( ( $start_index - 1 ) * $limit ) : 0;
			$order_by	   = ( $this->input->post( 'order_by' ) ) ? $this->input->post( 'order_by' ) : false;

			#prepare postdata
			$postdata = [
				'account_id'	=>$this->user->account_id,
				'search_term'	=>$search_term,
				'where'			=>$where,
				'order_by'		=>$order_by,
				'limit'			=>$limit,
				'offset'		=>$offset
			];

			$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/lookup', $postdata, ['auth_token'=>$this->auth_token], true );

			$people			= ( isset( $search_result->people ) ) ? $search_result->people : null;

			if( !empty( $people ) ){

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

				$return_data = $this->load_people_view( $people );
				if( !empty($pagination) ){
					$return_data .= '<tr><td colspan="7" style="padding: 0;">';
						$return_data .= $page_display.$pagination;
					$return_data .= '</td></tr>';
				}
			}else{
				$return_data .= '<tr><td colspan="7" style="padding: 0;"><br/>';
					$return_data .= ( isset($search_result->message) ) ? $search_result->message : 'No records found';
				$return_data .= '</td></tr>';
			}
		}

		print_r( $return_data );
		die();
	}

	/*
	* Prepare people views
	*/
	private function load_people_view( $people_data ){
		$return_data = '';
		if( !empty($people_data) ){
			foreach( $people_data as $k => $person_details ){
				$return_data .= '<tr>';
					//$return_data .= '<td><a href="'.base_url('/webapp/people/profile/'.$person_details->user_id).'" >'.$person_details->account_user_id.'</a></td>';
					$return_data .= '<td><a href="'.base_url('/webapp/people/profile/'.$person_details->person_id).'" >'.ucwords( strtolower( $person_details->first_name.' '.$person_details->last_name ) ).'</a></td>';
					$return_data .= '<td>'.$person_details->preferred_name.'</td>';
					$return_data .= '<td>'.$person_details->personal_email.'</td>';
					$return_data .= '<td>'.$person_details->department_name.'</td>';
					$return_data .= '<td>'.$person_details->job_title.'</td>';
					$return_data .= '<td>'.$person_details->status.'</td>';
				$return_data .= '</tr>';
			}

			if( !empty($pagination) ){
				$return_data .= '<tr><td colspan="6" style="padding: 0;">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
		}else{
			$return_data .= '<tr><td colspan="6"><br/>'.$this->config->item("no_records").'</td></tr>';
		}
		return $return_data;
	}


	/** Update SIte Details **/
	public function update_person( $person_id = false, $page = 'details' ){
		$return_data = [
			'status'=>0
		];

		$section 	= ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$person_id  = ( $this->input->post( 'person_id' ) ) ? $this->input->post( 'person_id' ) : ( !empty( $person_id ) ? $person_id : null );

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata 	   = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );

			$updates_people= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/update', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  = ( isset( $updates_people->person ) ) ? $updates_people->person : null;
			$message	  = ( isset( $updates_people->message ) ) ? $updates_people->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status']= 1;
				$return_data['person'] = $result	;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
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
					$audit .= '<tr><td colspan="2"><table style="width:100%;display:table">';
					$audit .= '<tr><th width="10%">ID</th><th width="75%">Audit Question</th><th width="15%">Response</th></tr>';
						foreach( $audit_record->audit_responses as $k=>$audit_item ){ $k++;
							$audit .= '<tr><td>'.$k.'</td><td>'.$audit_item->question.'</td><td>'.$audit_item->response.'</td></tr>';
						}
					$audit .= '</table></td></tr>';
			$audit .= '</table>';
		}
		return $audit;
	}

	/** Get list of addresses by postcode **/
	public function get_addresses_by_postcode( $postcodes = false ){
		$postcodes = ( $this->input->post("postcodes") ) ? $this->input->post("postcodes") : $postcodes;
		if( $postcodes ){
			$addresses_list = "";
			$addresses = $this->address_service->get_addresses( $postcodes );
			if( $addresses ){
				$addresses_list = "<option>Please select address...</option>";
				foreach( $addresses as $address ){
					$addresses_list .= '<option value = "'.$address["main_address_id"].'" data-addressline1="'.$address["addressline1"].'"  data-addressline2="'.$address["addressline2"].'"  data-addressline3="'.$address["addressline3"].'"  data-posttown="'.$address["posttown"].'"  data-county="'.$address["county"].'"  data-postcode="'.$address["postcode"].'" >'.$address["summaryline"].'</option>';
				}
			} else {
				$addresses_list = "<option>Please select</option>";
				$addresses_list .= '<option value = "" data-addressline1=""  data-addressline2=""  data-posttown=""  data-county=""  data-postcode="" >Select this to add address manualy</option>';
			}

		} else {
			$addresses_list = "<option disabled='disabled'>Please provide a postcode.</option>";
		}
		$data["addresses_list"] = $addresses_list;
		echo json_encode( $data );
	}

	/** Search the syste users table for a user matching a specified name **/
	public function search_for_user( $account_id = false, $search_term = false ){
		$account_id = ( !empty( $this->input->post( 'account_id' ) ) ) ? $this->input->post( 'account_id' ) : $account_id;
		$search_term = ( !empty( $this->input->post( 'userdata' ) ) ) ? $this->input->post( 'userdata' ) : $search_term;
		if( !empty( $account_id ) && !empty( $search_term ) ){
			$users_list = '';
			$grouped_users = $this->people_service->find_user_records( $account_id, $search_term );

			if( $grouped_users ){
				$users_list = '<option value="" >Please select user...</option>';
				foreach( $grouped_users as $exists => $users ){

					foreach( $users as $exists_key => $user ){
						if( $exists == 'exists' ){
							$users_list .= '<option value = "'.$user->id.'" data-personal_email="'.$user->email.'" disabled >'.$user->first_name.' '.$user->last_name.' (Already exists)</option>';
						}else{
							$users_list .= '<option value = "'.$user->id.'" ';
							$users_list .= 'data-personal_email="'.$user->email.'" ';
							$users_list .= 'data-preferred_name="'.$user->username.'" ';
							$users_list .= 'data-user_type_id="'.$user->user_type_id.'" ';
							$users_list .= 'data-first_name="'.$user->first_name.'" ';
							$users_list .= 'data-last_name="'.$user->last_name.'" ';
							$users_list .= '>'.$user->first_name.' '.$user->last_name.'</option>';
						}
					}
				}

			} else {
				$users_list = '<option>Please select</option>';
				$users_list .= '<option value="" data-personal_email="" >Select this to add user manualy</option>';
			}
		}

		$users_list .= '<option value="">No, create as new (also create a system user)</option>';

		$data['users_list'] = $users_list;
		echo json_encode( $data );
	}

	/** Do address-contact creation **/
	public function create_contact(){
		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$return_data = [
			'status'=>0
		];

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata 	  	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$address_contact= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/create_contact', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  	= ( isset( $address_contact->address_contact ) ) ? $address_contact->address_contact : null;
			$message	  	= ( isset( $address_contact->message ) ) ? $address_contact->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	public function upload_people( $account_id = false ){

		if( !empty( $account_id ) && !empty( $_FILES['upload_file']['name'] ) ){

			$process_file = $this->people_service->upload_people( $account_id );

			if( $process_file ){
				redirect( '/webapp/people/review/'.$account_id );
			}

		}else{
			redirect( '/' );
		}
	}

	/** Review People **/
	public function review( $account_id = false ){

		if( !empty( $account_id ) ){
			$pending 			= $this->people_service->get_pending_upload_records( $account_id );
			$data['pending']	= ( !empty( $pending ) ) ? $pending : null;

			$user_types 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'user/user_types', ['account_id'=>$this->user->account_id], false, true );
			$data['user_types']	= ( isset( $user_types->user_types ) ) ? $user_types->user_types : null;

			$departments		= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/departments', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['departments']= ( isset($departments->departments) ) ? $departments->departments : null;

			$job_titles		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/job_titles', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['job_titles'] = ( isset( $job_titles->job_titles ) ) ? $job_titles->job_titles : null;
			$this->_render_webpage('people/pending_creation', $data);
		}

	}

	/** Do address-contact creation **/
	public function update_temp_data( $temp_user_id = false ){

		$return_data = [
			'status'=>0
		];

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, 'details' );
		if( !$this->is_admin && !$item_access ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata = ( $this->input->post('people') ) ? $this->input->post('people') : false;
			if( !empty( $postdata[$temp_user_id] ) ){
				$update_temp_data= $this->people_service->update_temp_data( $this->user->account_id, $temp_user_id, $postdata[$temp_user_id] );
			}
			$message = ( !empty( $update_temp_data ) ) ? 'Temp record updated successfully' : 'Oops! There was an error processing your request, update failed!';
			if( !empty( $update_temp_data ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	/** Create people records **/
	public function create_people(){

		$return_data = [
			'status'	=> 0,
			'all_done'	=> 0,
		];

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, 'details' );
		if( !$this->is_admin && !$item_access ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata = ( $this->input->post() ) ? $this->input->post() : false;
			if( !empty( $postdata ) ){
				$total_records = count( array_keys( $postdata['people'] ) );
				$new_people_records = $this->people_service->create_people( $this->user->account_id, $postdata );
			}
			$message = ( !empty( $new_people_records ) ) ? count( $new_people_records ).' new people records created successfully' : 'Oops! There was an error processing your request, update failed!';
			if( !empty( $new_people_records ) ){

				$return_data['status'] 	 = 1;
				$return_data['all_done'] = ( $total_records == count( $new_people_records ) ) ? 1 : 0;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/** Drop people records **/
	public function drop_people(){

		$return_data = [
			'status'=>0
		];

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, 'details' );
		if( !$this->is_admin && !$item_access ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata = ( $this->input->post() ) ? $this->input->post() : false;
			if( !empty( $postdata ) ){
				$new_people_records = $this->people_service->create_people( $this->user->account_id, $postdata );
			}
			$message = ( !empty( $new_people_records ) ) ? count( $new_people_records ).' new people records created successfully' : 'Oops! There was an error processing your request, update failed!';
			if( !empty( $new_people_records ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/** Upload files. This is a Web-client only function **/
	public function upload_docs( $person_id ){

		if( !empty( $person_id ) ){

			$postdata 	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$doc_upload	= $this->document_service->upload_files( $this->user->account_id, $postdata, $document_group = 'people', $folder = 'people' );

			redirect('webapp/people/profile/'.$person_id.'/documents' );

		}else{
			redirect('webapp/people', 'refresh');
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
	public function delete_cost_item( $person_id = false ){
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


	/**
	* Add Personal Skills
	**/
	public function add_personal_skills(){

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
			$personal_skills = $this->webapp_service->api_dispatcher( $this->api_end_point.'people/add_personal_skills' , $postdata, ['auth_token'=>$this->auth_token] );
			$result		  	 = ( isset( $personal_skills->personal_skills ) ) 	? $personal_skills->personal_skills : null;
			$message	  	 = ( isset( $personal_skills->message ) )  			? $personal_skills->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 	 = 1;
				$text_color 			 = 'green';
			}
			$return_data['status_msg'] = '<span class="text-'.$text_color.'">'.$message.'</span>';
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/**
	* Remove Personal Skill(s)
	**/
	public function remove_personal_skill( $skill_id = false ){
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
			$unlink_skill 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/remove_personal_skills', $postdata, ['auth_token'=>$this->auth_token] );
			$result			= ( isset( $unlink_skill->status ) )  ? $unlink_skill->status : null;
			$message		= ( isset( $unlink_skill->message ) ) ? $unlink_skill->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/**
	* Assign regions to a Person
	**/
	public function assign_regions(){

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
			$personal_regions= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/assign_regions' , $postdata, ['auth_token'=>$this->auth_token] );
			$result		  	 = ( isset( $personal_regions->assigned_regions ) ) 	? $personal_regions->assigned_regions : null;
			$message	  	 = ( isset( $personal_regions->message ) )  			? $personal_regions->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 	 = 1;
				$text_color 			 = 'green';
			}
			$return_data['status_msg'] = '<span class="text-'.$text_color.'">'.$message.'</span>';
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/**
	* Un-assign Regions
	**/
	public function unassign_region( $region_id = false ){
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
			$unlink_region 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/unassign_regions', $postdata, ['auth_token'=>$this->auth_token] );
			$result			= ( isset( $unlink_region->status ) )  ? $unlink_region->status : null;
			$message		= ( isset( $unlink_region->message ) ) ? $unlink_region->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	/** Create Diary Schedule Records **/
	public function create_diary_resource(){
		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$return_data = [
			'status'=>0
		];

		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		} else {
			$postdata 	  	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$diary_resource	= $this->webapp_service->api_dispatcher( $this->api_end_point.'diary/create_diary_resource', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  	= ( isset( $diary_resource->diary_resource ) )  ? $diary_resource->diary_resource : null;
			$message	  	= ( isset( $diary_resource->message ) ) 		? $diary_resource->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 			= 1;
				$return_data['diary_resource']  = $result;
			}
			$return_data['status_msg'] 			= $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/*
	*	Update contact address
	*/
	public function update_contact( $page = "contacts" ){
		$result['status'] 	= 0;

		$section 			= ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access 		= $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$result['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postset 						= $this->input->post();
			if( !empty( $postset ) ){
				$postdata = [];

				$postdata["contact_id"] 	= ( !empty( $postset["contact_id"] ) ) ? ( int ) $postset["contact_id"] : false ;
				$postdata["dataset"] 		= $postset;
				$postdata["account_id"]		= $this->user->account_id;

				$url 						= 'people/update_contact';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( ( !empty( $API_result->status ) && ( $API_result->status == true ) ) ){
					$result = [
						'status'			=> $API_result->status,
						'status_msg'		=> $API_result->message,
						'updated_contact'	=> $API_result->updated_contact,
					];
				} else {
					if( ( !empty( $API_result->message ) ) ){
						$result['status'] 		= false ;
						$result['status_msg'] 	= $API_result->message;
					} else {
						$result['status'] 		= false ;
						$result['status_msg'] 	= 'Something went wrong';
					}
				}
			}
		}
		print_r( json_encode( $result ) );
		die();
	}


	/*
	*	Delete contact address
	*/
	public function delete_contact( $page="contacts" ){
		$result['status'] 	= 0;

		$section 			= ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access 		= $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_delete ) && empty( $item_access->is_admin ) ){
			$result['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postset 						= $this->input->post();

			if( !empty( $postset ) ){
				$postdata = [];
				$postdata["contact_id"]		= ( !empty( $postset["contact_id"] ) ) ? ( int ) $postset["contact_id"] : false ;
				$postdata["account_id"]		= $this->user->account_id;

				$url 						= 'people/delete_contact';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( ( !empty( $API_result->status ) && ( $API_result->status == true ) ) ){
					$result = [
						'status'			=> $API_result->status,
						'status_msg'		=> $API_result->message,
						'deleted_contact'	=> $API_result->deleted_contact,
					];
				} else {
					if( ( !empty( $API_result->message ) ) ){
						$result['status'] = false ;
						$result['status_msg'] = $API_result->message;
					} else {
						$result['status'] = false ;
						$result['status_msg'] = 'Something went wrong';
					}
				}
			}
		}
		print_r( json_encode( $result ) );
		die();
	}
	
	
	
	public function get_contact_details( $page = 'contacts' ){
		$return_data = [
			'status' => 0
		];

		# Check module-item access
		$section 		= ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access 	= $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data .= $this->config->item( 'ajax_access_denied' );
		} else {
			$postset 						= $this->input->post();

			if( !empty( $postset ) ){
				$postdata = [];
				$postdata['contact_id'] 	= ( !empty( $postset['contact_id'] ) ) ? ( int ) $postset['contact_id'] : NULL ;
				$postdata['account_id']		= $this->user->account_id;
				$postdata['person_id']		= ( !empty( $postset['person_id'] ) ) ? ( int ) $postset['person_id'] : NULL ;

				$url 						= 'people/address_contacts';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );

				$return_data = [
					'status'				=> ( !empty( $API_result->status ) ) ? $API_result->status : FALSE ,
					'message'				=> ( !empty( $API_result->message ) ) ? $API_result->message : "No response" ,
				];

				$return_data['address_contacts'] =	( !empty( $API_result->address_contacts ) ) ? $this->load_contact_details_view( $API_result->address_contacts, $postdata['person_id'] ) : false;

				print_r( json_encode( $return_data ) );
				die();
			}
		}
	}


	/*
	* 	Prepare a view for the Contact update
	*/
	private function load_contact_details_view( $dataset = false, $person_id = false ){

		if( !empty( $dataset ) && !empty( $person_id ) ){

			$postdata 				= [];

			$address_types					= false;
			$postdata["account_id"]			= $this->user->account_id;
			$url 							= 'address/address_types';
			$API_result						= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
			$address_types					= ( !empty( $API_result->address_types ) ) ? $API_result->address_types : null;

			$address_contacts 			 	= false;
			$postdata["account_id"]			= $this->user->account_id;
			$postdata["person_id"]			= $person_id;
			$url 							= 'people/address_contacts';
			$API_result						= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
			$address_contacts				= ( !empty( $API_result->address_contacts ) ) ? $API_result->address_contacts : null;

			$relationships		 			= contact_relationships();

			$return_data = '';

			$return_data .= '<form id="contact_update_in_modal">';
			$return_data .= '<input type="hidden" name="contact_id" value="'.$dataset->contact_id.'" />';

			$return_data .= '<div style="width:100%;">';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Address Type:</label>';
			$return_data .= '<select name="address_type_id" class="form-control" required><option value="">Please select</option>';

			if( !empty( $address_types ) ){
				foreach( $address_types as $row ){
					$return_data .= '<option value="'.( $row->address_type_id ).'" ';
					if( !empty( $dataset->address_type_id ) && ( $dataset->address_type_id == $row->address_type_id ) ){
						$return_data .= 'selected="selected"';
					}

					$return_data .='>'.( $row->address_type ).'</option>';
				}
			}
			$return_data .= '</select>';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Addressee First name:</label>';
			$return_data .= '<input class="form-control" placeholder="Addressee First name" type="text" name="contact_first_name" value="'.( !empty( $dataset->contact_first_name ) ? ( $dataset->contact_first_name ) : '' ).'" required="required" />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Addressee Last name:</label>';
			$return_data .= '<input class="form-control" placeholder="Addressee Last name" type="text" name="contact_last_name" value="'.( !empty( $dataset->contact_last_name ) ? ( $dataset->contact_last_name ) : '' ).'" required="required" />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Mobile:</label>';
			$return_data .= '<input class="form-control" placeholder="Mobile" type="text" name="contact_mobile" value="'.( !empty( $dataset->contact_mobile ) ? ( $dataset->contact_mobile ) : '' ).'" required="required" />';
			$return_data .= '</div>';

			/*
			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Telephone:</label>';
			$return_data .= '<input class="form-control" placeholder="Telephone" type="text" name="contact_number" value="'.( !empty( $dataset->contact_number ) ? ( $dataset->contact_number ) : '' ).'" required="required" />';
			$return_data .= '</div>';
			*/

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Email:</label>';
			$return_data .= '<input class="form-control" placeholder="Email address" type="text" name="contact_email" value="'.( !empty( $dataset->contact_email ) ? ( $dataset->contact_email ) : '' ).'" />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Relationship to person:</label>';
			$return_data .= '<select name="relationship" class="form-control" required="required"><option value="">Please select</option>';

			if( !empty( $relationships ) ){
				foreach( $relationships as $row ){
					$return_data .= '<option value="'.( $row ).'" '.( ( ( !empty( $dataset->relationship ) ) && ( strtolower( $dataset->relationship ) == strtolower( $row ) ) ) ? "selected='selected'" : "" ).' >'.( $row ).'</option>';
				}
			}
			$return_data .= '</select>';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Address Line 1:</label>';
			$return_data .= '<input class="form-control" placeholder="Address Line 1" type="text" name="address_line1" value="'.( !empty( $dataset->address_line1 ) ? ( $dataset->address_line1 ) : '' ).'" />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Address Line 2:</label>';
			$return_data .= '<input class="form-control" placeholder="Address Line 2" type="text" name="address_line2" value="'.( !empty( $dataset->address_line2 ) ? ( $dataset->address_line2 ) : '' ).'" />';
			$return_data .= '</div>';

			/*
			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Address Line 3:</label>';
			$return_data .= '<input class="form-control" placeholder="Address Line 3" type="text" name="address_line3" value="'.( !empty( $dataset->address_line3 ) ? ( $dataset->address_line3 ) : '' ).'" />';
			$return_data .= '</div>';
			*/

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Address Town:</label>';
			$return_data .= '<input class="form-control" placeholder="Address Town" type="text" name="address_town" value="'.( !empty( $dataset->address_town ) ? ( $dataset->address_town ) : '' ).'" />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Address County:</label>';
			$return_data .= '<input class="form-control" placeholder="Address County" type="text" name="address_county" value="'.( !empty( $dataset->address_county ) ? ( $dataset->address_county ) : '' ).'" />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Address Postcode:</label>';
			$return_data .= '<input class="form-control" placeholder="Address Postcode" type="text" name="address_postcode" value="'.( !empty( $dataset->address_postcode ) ? ( $dataset->address_postcode ) : '' ).'" />';
			$return_data .= '</div>';


/* 			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Contact Note:</label>';
			$return_data .= '<textarea name="contact_note" class="form-control" placeholder="Contact Note" rows="3">'.( !empty( $dataset->contact_note ) ? ( string ) $dataset->contact_note : ''  ).'</textarea>';
			$return_data .= '</div>'; */

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Date Created:</label>';
			$return_data .= '<input class="form-control" placeholder="'.( date( 'd/m/Y' ) ).'" data-date-format="DD/MM/Y" type="text" value="'.( validate_date( $dataset->created_on ) ? format_datetime_client( $dataset->created_on ) : '' ).'" readonly="readonly" />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Created By:</label>';
			$return_data .= '<input class="form-control" placeholder="" type="text" value="'.( !empty( $dataset->created_by ) ? ( $dataset->created_by ) : '' ).'" readonly="readonly" />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Last Modified:</label>';
			$return_data .= '<input class="form-control" data-date-format="DD/MM/Y" type="text" value="'.( validate_date( $dataset->last_modified ) ? format_datetime_client( $dataset->last_modified ) : '' ).'" readonly="readonly" />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Modified By:</label>';
			$return_data .= '<input class="form-control" type="text" value="'.( !empty( $dataset->modified_by ) ? ( $dataset->modified_by ) : '' ).'" readonly="readonly" />';
			$return_data .= '</div>';

			$return_data .= '</div><div class="row"><div class="col-md-4 pull-right">';

			$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, "positions" );
			if( $this->user->is_admin || !empty( $item_access->can_edit ) || !empty( $item_access->is_admin ) ){
				$return_data .= '<button id="updateContactBtn" class="btn-success btn-next btn btn-sm btn-block btn-flow margin_top_8">Update Contact</button>';
			} else {
				$return_data .= '<button class="btn-success btn btn-sm btn-flow btn-success btn-next submit no-permissions" disabled style="width: 100%;margin-top: 10px;">No Permissions</button>';
			}

			$return_data .= '</div></div>';
			$return_data .= '</form>';

		} else {
			$return_data .= '<div width="100%">';
			$return_data .= '<div><div colspan="2">'.$this->config->item( "no_data" ).'</div></div>';
			$return_data .= '</div>';
		}

		return $return_data;
	}



	
	/*
	*	Delete a document from the person profile
	*/
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
				$postdata['document_group'] = "people";
				$url						= 'document_handler/delete_document';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				## d_document = deleted_document
				if( !empty( $API_result ) ){
					$return_data['d_document']	= ( isset( $API_result->status ) && ( $API_result->status == true ) ) ? $API_result->documents : null;
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
