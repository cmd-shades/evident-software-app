<?php defined('BASEPATH') OR exit('No direct script access allowed');

class People extends MX_Controller {

	function __construct(){
		parent::__construct();

		if( !$this->identity() ){
			redirect('webapp/user/login', 'refresh');
		}

		$this->module_id 	   = $this->webapp_service->_get_module_id( $this->router->fetch_class() );
		$access = $this->webapp_service->check_access( $this->user, $this->module_id );

		$this->load->library('pagination');
		$this->load->model('serviceapp/People_model','people_service');
		$this->load->model('serviceapp/Address_model','address_service');
		
		$this->locations = ["London", "Manchester", "Newcastle", "St. Ives", "South", "North West", "Midlands", "North East"];
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
	public function people( $person_id = false, $page = "details" ){

		if( $person_id ){
			redirect('webapp/people/profile/'.$person_id, 'refresh');
		}

		# Check module-item access
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage('errors/access-denied', false);
		} else {

			$data['permissions']	= $item_access;

			$user_statuses	 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'user/statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['user_statuses'] 	= ( isset( $user_statuses->user_statuses ) ) ? $user_statuses->user_statuses : null;

			$departments		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/departments', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['departments'] 	= ( isset($departments->departments) ) ? $departments->departments : null;

			$simple_stats		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/simple_stats', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['simple_stats']	= ( isset( $simple_stats ) && ( $simple_stats->status == true ) && !empty( $simple_stats->simple_stats ) ) ? ( $simple_stats->simple_stats ) : ( false ) ;

			$this->_render_webpage('people/index', $data);
		}
	}

	//View user profile
	function profile( $person_id = false, $page = 'details' ){

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage('errors/access-denied', false);
		} else if( $person_id ){

			$data['permissions']	= $item_access;

			$person_details		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/people', ['account_id'=>$this->user->account_id,'person_id'=>$person_id], ['auth_token'=>$this->auth_token], true );
			$data['person_details']	= ( isset( $person_details->people ) ) ? $person_details->people : null;

			if( !empty( $data['person_details'] ) ){
				$data['permitted_actions'] = ( !empty( $item_access->item_permissions ) ) ? $this->ssid_common->permitted_actions( $item_access->item_permissions ) : [];

				$module_items 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'access/account_modules_items', ['account_id'=>$this->user->account_id, 'module_id'=>$this->module_id ], ['auth_token'=>$this->auth_token], true );
				$data['module_tabs']= ( isset( $module_items->module_items ) ) ? $module_items->module_items : null;

				switch( $page ){

					case 'positions':
						$data['active_tab'] 	= 'positions';

						$departments			= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/departments', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
						$data['departments']	= ( isset($departments->departments) ) ? $departments->departments : null;

						$job_titles		 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/job_titles', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
						$data['job_titles'] 	= ( isset( $job_titles->job_titles ) ) ? $job_titles->job_titles : null;

						$job_positions		  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/job_positions', ['account_id'=>$this->user->account_id,'person_id'=>$person_id], ['auth_token'=>$this->auth_token], true );
						$data['job_positions']	= ( isset( $job_positions->job_positions ) ) ? $job_positions->job_positions : null;

						$line_managers		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/people', ['account_id'=>$this->user->account_id, "limit"=>9999], ['auth_token'=>$this->auth_token], true );
						$data['line_managers']	= ( isset( $line_managers->people ) ) ? $line_managers->people : null;

						$data['locations']		= ( $this->locations );
						
						$data['include_page'] = 'person_positions.php';
						break;

					case 'contacts':
						$data['active_tab']   	= 'contacts';

						$address_types	 	  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'address/address_types', ['account_id'=>$this->user->account_id, 'address_type_id'=>[2,3,4] ], ['auth_token'=>$this->auth_token], true );
						$data['address_types']	= ( isset( $address_types->address_types ) ) ? $address_types->address_types : null;

						$address_contacts	  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/address_contacts', ['account_id'=>$this->user->account_id, 'person_id'=>$person_id ], ['auth_token'=>$this->auth_token], true );
						$data['address_contacts']= ( isset( $address_contacts->address_contacts ) ) ? $address_contacts->address_contacts : null;

						$data['relationships'] 	= contact_relationships();
						

						$data['include_page'] 	= 'person_contacts.php';
						break;


					case 'health':
						$data['active_tab']   = 'health';

						$data['health_log'] = false;
						$postdata = [];
						$postdata["account_id"]		= $this->user->account_id;
						$postdata["person_id"]		= $person_id;
						$url 						= 'people/health_log';
						$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
						if( ( !empty( $API_result->status ) ) && ( $API_result->status == 1 ) ){
							$data['health_log'] 	= ( !empty( $API_result->health_log ) ) ? $API_result->health_log : $data['health_log'] ;
						}

						$data["q_types"]			= false; ## Questionnaire Types
						$url 						= 'people/questionnaire_types';
						$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
						if( ( !empty( $API_result->status ) ) && ( $API_result->status == 1 ) ){
							$data['q_types'] 		= ( !empty( $API_result->q_types ) ) ? $API_result->q_types : $data['q_types'] ;
						}

						$data["q_results"]			= false; ## Questionnaire Results
						$url 						= 'people/questionnaire_results';
						$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );

						if( ( !empty( $API_result->status ) ) && ( $API_result->status == 1 ) ){
							$data['q_results'] 		= ( !empty( $API_result->q_results ) ) ? $API_result->q_results : $data['q_results'] ;
						}

						$data["health_log_notes"]	= false;
						$postdata["person_id"]		= $person_id;
						$postdata["limit"]			= 9999;
						$postdata["order_by"]		= false;
						$url 						= 'people/health_log_notes';
						$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
						if( ( !empty( $API_result->status ) ) && ( $API_result->status == 1 ) ){
							$data['health_log_notes'] 		= ( !empty( $API_result->h_log_notes ) ) ? $API_result->h_log_notes : $data['health_log_notes'] ;
						}

						$data['include_page'] 	= 'person_health.php';
						break;


					case 'events':
						$data['active_tab'] 		= 'events';
						$postdata 					= [];
						$data['event_supervisor'] 	= $data['event_types'] = $data['event_categories'] = $data['events'] = false;

						$postdata["account_id"]		= $this->user->account_id;
						$postdata["limit"]			= 999;
						$url 						= 'people/people';
						$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
						$data['event_supervisor'] 	= ( !empty( $API_result->people ) ) ? $API_result->people : $data['event_supervisor'] ;

						$url 						= 'people/event_categories';
						$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
						$data['event_categories'] 	= ( !empty( $API_result->event_categories ) ) ? $API_result->event_categories : $data['event_categories'] ;

						$url 						= 'people/events';
						$postdata['person_id']		= $person_id;
						$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
						$data['events'] 			= ( !empty( $API_result->events ) ) ? $API_result->events : $data['events'] ;

						$job_titles		 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/job_titles', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
						$data['job_titles'] 		= ( isset( $job_titles->job_titles ) ) ? $job_titles->job_titles : null;

						$data['include_page'] 		= 'person_events.php';
						break;

					case 'assets':
						$data['active_tab']  		= 'assets';

						$assigned_assets	  		= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/assets', ['account_id'=>$this->user->account_id,'assigned_to'=>$person_id], ['auth_token'=>$this->auth_token], true );
						$data['assigned_assets']	= ( isset( $assigned_assets->assets ) ) ? $assigned_assets->assets : null;

						$data['include_page'] 		= 'person_assets.php';
						break;


					case 'history':
						$data['active_tab']   			= 'history'; 
						$postdata 						= [];
						$postdata["account_id"]			= $this->user->account_id;
						$postdata["limit"]				= 9999;
						$postdata["person_id"]			= $person_id;
						$url							= 'people/person_change_logs';
						$API_result						= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
						$data['person_change_logs'] 	= ( ( $API_result->status == true ) && !empty( $API_result->person_change_logs )  ) ? ( $API_result->person_change_logs ) : ( false ) ;

						$data['include_page'] 			= 'person_history.php';
						break;

					case 'checklist':
						$data['active_tab']   		= 'checklist';

						$postdata = [
							'account_id'	=> $this->user->account_id,
							'category'		=> 'procedures'
						];
						$checklist_questions		= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/checklist_questions', $postdata, ['auth_token'=>$this->auth_token], true );
						$data['checklist_questions']= ( isset( $checklist_questions->checklist_questions ) ) ? $checklist_questions->checklist_questions : null;

						$postdata = [
							'account_id'	=> $this->user->account_id,
							'person_id'		=> $person_id,
							'category'		=> 'procedures'
						];
						$checklist_answers		= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/checklist_answers', $postdata, ['auth_token'=>$this->auth_token], true );
						$data['checklist_answers']= ( isset( $checklist_answers->checklist_answers ) ) ? $checklist_answers->checklist_answers : null;

						$data['include_page'] 		= 'person_checklist.php';
						break;

					case 'security':
						$data['active_tab']   		= 'security';
						$data['screening_type']			= ['Basic DBS', 'Enhanced DBS', 'BS7858', 'NPCC Letter', 'Intruder', 'Provisional Screening Certificate' ];
						$data['screening_result']		= ['Pending', 'Passed', 'Failed'];

						$data['screening_supervisor']	= [];
						$postdata["account_id"]			= $this->user->account_id;
						$postdata["limit"]				= 999;
						$url 							= 'people/people';
						$API_result						= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
						$data['screening_supervisor'] 	= ( !empty( $API_result->people ) ) ? $API_result->people : $data['event_supervisor'] ;

						$security_screening_logs		= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/security_logs', ['account_id'=>$this->user->account_id,'person_id'=>$person_id], ['auth_token'=>$this->auth_token], true );
						$data['security_screening_logs']= ( isset( $security_screening_logs->security_logs ) ) ? $security_screening_logs->security_logs : null;

						$data['include_page'] 			= 'person_security.php';
						break;

					case 'fines':
						$data['active_tab']   		= 'fines';
						$person_fines				= $this->webapp_service->api_dispatcher( $this->api_end_point.'fleet/fines', ['account_id'=>$this->user->account_id, "driver_id" => $person_id], ['auth_token'=>$this->auth_token], true );
						$data['person_fines']		= ( isset( $person_fines->fines ) && ( !empty( $person_fines->fines ) ) ) ? $person_fines->fines : null;

						$data['include_page'] 		= 'person_fines.php';
						break;


					case 'leaver':
						$data['active_tab']   		= 'leaver';
						$leaver_reasons				= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/leaver_reasons', ['account_id'=>$this->user->account_id,"ordered" => true], ['auth_token'=>$this->auth_token], true );
						$data['leaver_reasons']		= ( isset( $leaver_reasons->reasons ) ) ? $leaver_reasons->reasons : null;

						$leaver_log					= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/leaver_log', ['account_id'=>$this->user->account_id, "person_id" => $person_id, "ordered" => true, "limit" => 999], ['auth_token'=>$this->auth_token], true );
						$data['leaver_logs']		= ( isset( $leaver_log->logs ) && !empty( $leaver_log->logs ) ) ? $leaver_log->logs : null;

						$data['include_page'] 		= 'person_leaver.php';
						break;

					case 'training':
						$data['active_tab']   		= 'training';

						$postdata = [
							'account_id'	=> $this->user->account_id,
							'category'		=> 'training'
						];
						$checklist_questions		= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/checklist_questions', $postdata, ['auth_token'=>$this->auth_token], true );
						$data['checklist_questions']= ( isset( $checklist_questions->checklist_questions ) ) ? $checklist_questions->checklist_questions : null;

						$postdata = [
							'account_id'	=> $this->user->account_id,
							'person_id'		=> $person_id,
							'category'		=> 'training'
						];
						$checklist_answers		= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/checklist_answers', $postdata, ['auth_token'=>$this->auth_token], true );
						$data['checklist_answers']= ( isset( $checklist_answers->checklist_answers ) ) ? $checklist_answers->checklist_answers : null;

						$data['include_page'] 		= 'person_training.php';
						break;
						
					case 'documents':
						$data['active_tab']   		= 'documents';
						$audit_documents		= $this->webapp_service->api_dispatcher( $this->api_end_point.'document_handler/document_list', ['account_id'=>$this->user->account_id, 'person_id'=>urlencode( $data['person_details']->person_id ), 'audit_group'=>'people' ], ['auth_token'=>$this->auth_token], true );
						$data['audit_documents']= ( isset($audit_documents->documents->{$this->user->account_id}) ) ? $audit_documents->documents->{$this->user->account_id} : null;
						$data['include_page'] 	= 'person_documents.php';
						break;

					case 'details':
					default:
						$data['active_tab']   		= 'details';
						$data['user_titles']		= ["Mr", "Mrs", "Miss", "Ms"];
						$data['countries']			= $this->ssid_common->get_countries();

						$departments		 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/departments', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
						$data['departments'] 		= ( isset($departments->departments) ) ? $departments->departments : null;

						$user_statuses		  		= $this->webapp_service->api_dispatcher( $this->api_end_point.'user/statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
						$data['user_statuses'] 		= ( isset($user_statuses->user_statuses) ) ? $user_statuses->user_statuses : null;

						$data['include_page'] 		= 'person_details.php';
						break;
				}
			}

			$this->_render_webpage('people/profile', $data, '');
		}else{
			redirect('webapp/people', 'refresh');
		}
	}

	/** Create new people **/
	public function create( $page = 'details' ){

		# Check module-item access
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage('errors/access-denied', false);
		}else{
			$departments	 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/departments', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['departments']= ( isset($departments->departments) ) ? $departments->departments : null;

			$job_titles		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/job_titles', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['job_titles'] = ( isset( $job_titles->job_titles ) ) ? $job_titles->job_titles : null;

			$this->_render_webpage('people/person_create_new', $data);
		}
	}

	/** Do people creation **/
	public function create_person( $page="details" ){
		# Check module-item access
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$new_people	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'people/create', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  = ( isset($new_people->people) ) ? $new_people->people : null;
			$message	  = ( isset($new_people->message) ) ? $new_people->message : 'Something went wrong!';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
				$return_data['people']   = $new_people;
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

		# Check module-item access
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_delete ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$delete_people = $this->webapp_service->api_dispatcher( $this->api_end_point.'people/delete', $postdata, ['auth_token'=>$this->auth_token], true );
			$result		  = ( isset($delete_people->status) ) ? $delete_people->status : null;
			$message	  = ( isset($delete_people->message) ) ? $delete_people->message : 'Something went wrong!';
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

		# Check module-item access
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data .= $this->config->item( 'ajax_access_denied' );
		}else{

			# Setup search parameters
			$search_term   = ( $this->input->post( 'search_term' ) ) ? $this->input->post( 'search_term' ) : false;
			$departments   = ( $this->input->post( 'departments' ) ) ? $this->input->post( 'departments' ) : false;
			$user_statuses = ( $this->input->post( 'user_statuses' ) ) ? $this->input->post( 'user_statuses' ) : false;
			$limit		   = ( $this->input->post( 'limit' ) )  ? $this->input->post( 'limit' )  : DEFAULT_LIMIT;
			$start_index   = ( $this->input->post( 'start_index' ) )  ? $this->input->post( 'start_index' )  : 0;
			$offset		   = ( !empty( $start_index ) ) ? ( ( $start_index - 1 ) * $limit ) : 0;
			$order_by	   = false;
			$where		   = false;

			#prepare postdata
			$postdata = [
				'account_id'	=>$this->user->account_id,
				'search_term'	=>$search_term,
				'departments'	=>$departments,
				'user_statuses'	=>$user_statuses,
				'where'			=>$where,
				'order_by'		=>$order_by,
				'limit'			=>$limit,
				'offset'		=>$offset
			];


			$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/lookup', $postdata, ['auth_token'=>$this->auth_token], true );
			$people			= ( isset( $search_result->people ) ) ? $search_result->people : null;

			if( !empty($people) ){

				## Create pagination
				$counters 		= $this->people_service->get_total_people( $this->user->account_id, $search_term, $departments, $user_statuses, $where );//Direct access to count, this should only return counters
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
					$return_data .= '<td>'.$person_details->user_id.'</td>';
					$return_data .= '<td><a href="'.base_url('/webapp/people/profile/'.$person_details->person_id).'" >'.ucwords( strtolower( $person_details->first_name.' '.$person_details->last_name ) ).'</a></td>';
					$return_data .= '<td>'.$person_details->preferred_name.'</td>';
					$return_data .= '<td>'.$person_details->work_email.'</td>';
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


	/** 
	*	Update Person 
	**/
	public function update_person( $person_id = false, $page = 'positions' ){
		$return_data = [
			'status'=>0
		];

		# Check module-item access
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		} else {
			$postdata 	   = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );

			$updates_people= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/update', $postdata, ['auth_token'=>$this->auth_token] );

			$result		  = ( isset( $updates_people->person ) ) ? $updates_people->person : null;
			$message	  = ( isset( $updates_people->message ) ) ? $updates_people->message : 'Something went wrong!';
			if( !empty( $result ) ){
				$return_data['status']= 1;
				$return_data['person'] = $result	;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	
	/**
	*	Upload files. This is a Web-client only function
	**/
	public function upload_docs( $person_id = false, $page = 'documents' ){

		$post_data 	= $person_id = false;
		$post_data 	= $this->input->post();
		$person_id 	= ( !empty( $post_data['person_id'] ) ) ? ( int )$post_data['person_id'] : $person_id ;

		if( !empty( $person_id ) ){

			# Check module-item access
			$section 		= ( !empty( $page ) ) ? $page : $this->router->fetch_method();
			$item_access 	= $this->webapp_service->check_access( $this->user, $this->module_id, $section );

			if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
				$this->_render_webpage( 'errors/access-denied', false );
			} else {

				$postdata 		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );

				$document_group = 'people';
				$folder		 	= 'people';

				$doc_upload		= $this->document_service->upload_files( $this->user->account_id, $postdata, $document_group = 'people', $folder = 'people' );

				redirect( 'webapp/people/profile/'.$person_id.'/documents' );
			}
		} else {
			redirect( 'webapp/people', 'refresh' );
		}
	}

	
	/*
	* 	Load a audit record - not in use yet
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
			$message	= ( isset($audit_result->message) ) ? $audit_result->message : 'Something went wrong!';
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
					foreach( $users as $exists => $user ){
						if( $exists == 'exists' ){
							$users_list .= '<option value = "'.$user->id.'" data-personal_email="'.$user->email.'" disabled >'.$user->first_name.' '.$user->last_name.' (Already exists)</option>';
						}else{
							$users_list .= '<option value = "'.$user->id.'" data-personal_email="'.$user->email.'" data-preferred_name="'.$user->email.'" >'.$user->first_name.' '.$user->last_name.'</option>';
						}
					}
				}
			} else {
				$users_list = '<option>Please select</option>';
				$users_list .= '<option value = "" data-personal_email="" >Select this to add user manualy</option>';
			}
		}else{
			$users_list = '<option value="" >No, create as new (also create a system user)</option>';
		}

		$data['users_list'] = $users_list;
		echo json_encode( $data );
	}



	/** Do address-contact creation **/
	public function create_contact( $page = "contacts" ){
		$return_data = [
			'status'=>0
		];

		# Check module-item access
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata 	  	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$address_contact= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/create_contact', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  	= ( isset( $address_contact->address_contact ) ) ? $address_contact->address_contact : null;
			$message	  	= ( isset( $address_contact->message ) ) ? $address_contact->message : 'Something went wrong!';
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
			$pending 		= $this->people_service->get_pending_upload_records( $account_id );
			$data['pending']= ( !empty( $pending ) ) ? $pending : null;

			$user_types 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'user/user_types', ['account_id'=>$this->user->account_id], false, true );
			$data['user_types']	= ( isset($user_types->user_types) ) ? $user_types->user_types : null;

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

		if( !$this->identity() ){
			$return_data['message'] = "Access denied! Please login";
		}

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, 'details' );
		if( !$this->is_admin && !$item_access ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata = ( $this->input->post('people') ) ? $this->input->post('people') : false;
			if( !empty( $postdata[$temp_user_id] ) ){
				$update_temp_data= $this->people_service->update_temp_data( $this->user->account_id, $temp_user_id, $postdata[$temp_user_id] );
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

	/** Create people records **/
	public function create_people(){

		$return_data = [
			'status'=>0,
			'all_done'=>0,
		];

		# Check module-item access
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		} else {
			$postdata = ( $this->input->post() ) ? $this->input->post() : false;
			if( !empty( $postdata ) ){
				$total_records = count( array_keys( $postdata['people'] ) );
				$new_people_records = $this->people_service->create_people( $this->user->account_id, $postdata );
			}
			$message = ( !empty( $new_people_records ) ) ? count( $new_people_records ).' new people records created successfully' : 'Something went wrong, update failed!';
			if( !empty( $new_people_records ) ){

				$return_data['status'] 	 = 1;
				$return_data['all_done'] = ( $total_records == count( $new_people_records ) ) ? 1 : 0;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/** Create people records **/
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
			$message = ( !empty( $new_people_records ) ) ? count( $new_people_records ).' new people records created successfully' : 'Something went wrong, update failed!';
			if( !empty( $new_people_records ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}



	/**
	*	Create Health Questionnaire entry
	**/
 	public function create_health_log( $person_id = false, $page = "health" ){
		$return_data = [
			'status'=>0
		];

		# Check module-item access
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postset 					= $this->input->post();
			if( !empty( $postset ) ){
				$postdata = [];
				$postdata 					= $postset;
				$postdata["account_id"]		= $this->user->account_id;
				$url 						= 'people/create_health_log';
				
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( ( !empty( $API_result->status ) && ( $API_result->status == true ) ) ){

					$status = ( string ) $API_result->status;
					$return_data = [
						'status'		=> ( string ) $API_result->status,
						'status_msg'	=> $API_result->message,
						'health_log'	=> $API_result->health_log,
					];
				} else {
					if( ( !empty( $API_result->message ) ) ){
						$return_data['status'] = false ;
						$this->session->set_flashdata( 'feedback', $API_result->message );
					}
				}
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/**
	*	 An AJAX call to get Health Log(s)
	**/
 	public function get_health_log( $page = 'health' ){
		$return_data = [
			'status' => 0
		];

		# Check module-item access
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data .= $this->config->item( 'ajax_access_denied' );
		} else {

			$postset 						= $this->input->post();
			if( !empty( $postset ) ){
				$postdata = [];
				$postdata 					= $postset;
				$postdata["account_id"]		= $this->user->account_id;

				$url 						= 'people/health_log';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
				$return_data = [
					'status'		=> $API_result->status,
					'message'		=> $API_result->message,
				];

				$return_data['health_log'] =	( !empty( $API_result->health_log ) ) ? $this->load_health_logs_view( $API_result->health_log ) : false;

				print_r( json_encode( $return_data ) );
				die();
			}
		}
	}

	/*
	*	Prepare a view for Health log(s)
	*/
	private function load_health_logs_view( $data ){

		$return_data = '<table width="100%">';

		if( !empty( $data[0] ) ){
			$return_data .= '<tr>';
			$return_data .= '<td width="40%"><strong>Questionnaire</strong></td>';
			$return_data .= '<td><strong>'.$data[0]->q_type_name.'</strong> completed <strong>'.( ( validate_date( $data[0]->medical_qnaire_date ) ) ? date( 'd/m/Y', strtotime( $data[0]->medical_qnaire_date ) ) : '' ).'</strong> by <strong>'.$data[0]->created_by_full_name.'</strong></td>';
			$return_data .= '</tr>';

			$return_data .= '<tr>';
			$return_data .= '<td width="40%"><strong>Created</strong></td>';
			$return_data .= '<td> By <strong>'.$data[0]->created_by_full_name.'</strong> on <strong>'.( ( !empty( $data[0]->created_date ) ) && ( validate_date( $data[0]->created_date ) ) ? date( 'd/m/Y H:i:s', strtotime( $data[0]->created_date ) ) : '' ).'</strong></td>';
			$return_data .= '</tr>';

			$return_data .= '<tr>';
			$return_data .= '<td width="40%"><strong>Last Modified</strong></td>';
			$return_data .= '<td>'.( ( !empty( $data[0]->last_modified_by_full_name ) ) ?  'By <strong>'.$data[0]->last_modified_by_full_name.'</strong>' : '' ).( ( ( !empty( $data[0]->last_modified ) ) && ( validate_date( $data[0]->last_modified ) ) ) ? ' on <strong>'.( date( 'd/m/Y H:i:s', strtotime( $data[0]->last_modified ) ) ) : '' ).'</strong></td>';
			$return_data .= '</tr>';

			$return_data .= '<tr>';
			$return_data .= '<td width="40%"><strong>Questionnaire Result</strong></td>';
			$return_data .= '<td>'.$data[0]->q_result_name.'</td>';
			$return_data .= '</tr>';

			$return_data .= '<tr>';
			$return_data .= '<td width="40%"><strong>H&S Assessment Required</strong></td>';
			$return_data .= '<td><i class="far '.( ( $data[0]->medical_hs_assessment_req == 1 ) ? 'fa-check-circle text-green' : 'fa-times-circle text-red' ).'"></i></td>';
			$return_data .= '</tr>';

			$return_data .= '<tr>';
			$return_data .= '<td width="40%"><strong>H&S Adjustment Note</strong></td>';
			$return_data .= '<td>'.$data[0]->medical_hs_adjustment_note.'</td>';
			$return_data .= '</tr>';

			$return_data .= '<tr>';
			$return_data .= '<td width="40%"><strong>Medical Conditions</strong></td>';
			$return_data .= '<td><i class="far '.( ( $data[0]->medical_conditions == 1 ) ? 'fa-check-circle text-green' : 'fa-times-circle text-red' ).'"></i></td>';
			$return_data .= '</tr>';

			$return_data .= '<tr>';
			$return_data .= '<td width="40%"><strong>Medical Conditions Note</strong></td>';
			$return_data .= '<td>'.$data[0]->medical_conditions_note.'</td>';
			$return_data .= '</tr>';

			$return_data .= '<tr>';
			$return_data .= '<td width="40%"><strong>Medical Disability</strong></td>';
			$return_data .= '<td><i class="far '.( ( $data[0]->medical_disability == 1 ) ? 'fa-check-circle text-green' : 'fa-times-circle text-red' ).'"></i></td>';
			$return_data .= '</tr>';

			$return_data .= '<tr>';
			$return_data .= '<td width="40%"><strong>Medical Disability Note</strong></td>';
			$return_data .= '<td>'.$data[0]->medical_disability_note.'</td>';
			$return_data .= '</tr>';

			$return_data .= '<tr>';
			$return_data .= '<td width="40%"><strong>General Note</strong></td>';
			$return_data .= '<td>'.$data[0]->general_note.'</td>';
			$return_data .= '</tr>';

			$return_data .= '<tr>';
			$return_data .= '<td width="40%"><strong>Is the review required?</strong></td>';
			$return_data .= '<td><i class="far '.( ( $data[0]->is_review_required == 1 ) ? 'fa-check-circle text-green' : 'fa-times-circle text-red' ).'"></i></td>';
			$return_data .= '</tr>';

			$return_data .= '<tr>';
			$return_data .= '<td width="40%"><strong>Review Date</strong></td>';
			$return_data .= '<td>'.( ( validate_date( $data[0]->review_date ) ) ? format_date_client( $data[0]->review_date ) : '' ).'</td>';
			$return_data .= '</tr>';

			$return_data .= '<tr>';
			$return_data .= '<td width="40%" style="height: 28px;"><strong>Add a comment</strong></td>';
			$return_data .= '<td rowspan="2">';
			$return_data .= '<form id="log_status_update">
				<input type="hidden" name="account_id" value="'.$this->user->account_id.'" />
				<input type="hidden" name="health_log_id" value="'.$data[0]->health_log_id.'" />
				<input type="hidden" name="person_id" value="'.$data[0]->person_id.'" />
				<input type="hidden" name="status_change" value="" />
				<input type="hidden" name="health_log_previous_status" value="'.$data[0]->log_status.'" />
				<textarea class="log_note_in_modal" name="note"></textarea>';

			$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, "Health" );
			if( $this->user->is_admin || !empty( $item_access->can_edit ) || !empty( $item_access->is_admin ) ){
				$return_data .= '<button class="addNewNoteBtn col-md-3 btn-success btn btn-sm btn-flow btn-success btn-next submit">Add Comment</button>';
			} else {
				$return_data .= '<button class="col-md-3 btn-success btn btn-sm btn-flow btn-success btn-next submit no-permissions" disabled>No Permissions</button>';
			}

			$return_data .= '</form></td>';
			$return_data .= '</tr>';
			$return_data .= '<tr>';
			$return_data .= '<td width="40%" style="vertical-align: top;">&nbsp;</td>';
			$return_data .= '</tr>';


		} else {
			$return_data .= '<tr><td colspan="2"><br/>'.$this->config->item( "no_records" ).'</td></tr>';
		}

		return $return_data .= '</table>';
	}


	/**
	*	Create Event
	**/
 	public function create_event( $page = "events" ){
		$return_data = [
			'status' => 0
		];

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postset 					= $this->input->post();
			if( !empty( $postset ) ){
				$postdata = [];
				$postdata 					= $postset;
				$url 						= 'people/create_event';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( ( !empty( $API_result->status ) && ( $API_result->status == true ) ) ){

					$status = ( string ) $API_result->status;
					$return_data = [
						'status'		=> ( string ) $API_result->status,
						'status_msg'	=> $API_result->message,
						'new_event'		=> $API_result->new_event,
					];
				} else {
					if( ( !empty( $API_result->message ) ) ){
						$return_data['status'] = false ;
						$this->session->set_flashdata( 'feedback', $API_result->message );
					}
				}
			}
		}
		print_r( json_encode( $return_data ) );
		die();
	}

	/**
	*	 An AJAX call to get Event Details
	**/
 	public function get_event_details( $page = 'events' ){
		$return_data = [
			'status' => 0
		];

		# Check module-item access
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data .= $this->config->item( 'ajax_access_denied' );
		} else {

			$postset 						= $this->input->post();
			if( !empty( $postset ) ){
				$postdata = [];
				$postdata 					= $postset;

				$url 						= 'people/events';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );

				$return_data = [
					'status'				=> $API_result->status,
					'message'				=> $API_result->message,
				];

				$return_data['event_details'] =	( !empty( $API_result->events ) ) ? $this->load_event_details_view( $API_result->events ) : false;

				print_r( json_encode( $return_data ) );
				die();
			}
		}
	}


	/*
	* 	Prepare a view for the Event log update
	*/
	private function load_event_details_view( $data ){

		$postdata 				= [];

		$event_supervisor 		= false;
		$postdata["account_id"]	= $this->user->account_id;
		$postdata["limit"]		= 999;
		$url 					= 'people/people';
		$API_result				= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
		$event_supervisor	 	= ( !empty( $API_result->people ) ) ? $API_result->people : $data['event_supervisor'] ;

		$event_categories		= false;
		$url 					= 'people/event_categories';
		$API_result				= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
		$event_categories 		= ( !empty( $API_result->event_categories ) ) ? $API_result->event_categories : $data['event_categories'] ;

		$event_types		 	= false;
		$url 					= 'people/event_types';
		$postdata["category_id"]= $data[0]->event_category_id;
		$API_result				= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
		$event_types		 	= ( !empty( $API_result->event_types ) ) ? $API_result->event_types : $data['event_types'] ;

		$events			 		= false;
		$url 					= 'people/events';
		$API_result				= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
		$events 				= ( !empty( $API_result->events ) ) ? $API_result->events : $data['events'] ;

		$job_titles		 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/job_titles', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
		$job_titles 			= ( isset( $job_titles->job_titles ) ) ? $job_titles->job_titles : null;

		$return_data = '';

		if( !empty( $data ) ){
			$return_data .= '<form id="event_update_in_modal">';
			$return_data .= '<input type="hidden" name="event_id" value="'.$data[0]->event_id.'" />';

			$return_data .= '<div style="width:100%;">';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Event Category:</label>';
			$return_data .= '<select name="event_category_id" class="form-control" required><option value="">Please select</option>';

			if( !empty( $event_categories ) ){
				foreach( $event_categories as $row ){
					$return_data .= '<option value="'.( $row->category_id ).'" ';
					if( !empty( $data[0]->event_category_id ) && ( $data[0]->event_category_id == $row->category_id ) ){
						$return_data .= 'selected="selected"';
					}

					$return_data .='>'.( $row->category_name ).'</option>';
				}
			}
			$return_data .= '</select>';
			$return_data .= '</div>';

			$return_data .= '<div class="event_type_container input-group form-group"'.( ( !in_array( $data[0]->event_category_id, [7,8] ) ) ? 'style="display: none;"' : "" ).'>';
			$return_data .= '<label class="input-group-addon">Event Type:</label>';
			$return_data .= '<select name="event_type_id" class="event_type_ids form-control" required><option value="">Please select</option>';

			if( !empty( $event_types ) ){
				foreach( $event_types as $row ){
					$return_data .= '<option value="'.( $row->event_type_id ).'" '.( ( ( !empty( $data[0]->event_type_id ) ) && ( $data[0]->event_type_id == $row->event_type_id ) ) ? "selected='selected'" : "" ).' >'.( $row->event_type_name ).'</option>';
				}
			}
			$return_data .= '</select>';
			$return_data .= '</div>';

			$return_data .= '<div class="event_title_container input-group form-group"'.( ( in_array( $data[0]->event_category_id, [7,8] ) ) ? ' style="display: none;"' : "" ).'>';
			$return_data .= '<label class="input-group-addon">Event Title:</label>';
			$return_data .= '<input class="form-control" type="text" name="event_title" value="'.( !empty( $data[0]->event_title ) ? ( string ) $data[0]->event_title : '' ).'" />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Event Supervisor:</label>';
			$return_data .= '<select name="event_supervisor_id" class="form-control"><option value="">Please select</option>';

			if( !empty( $event_supervisor ) ){
				foreach( $event_supervisor as $row ){
					$return_data .= '<option value="'.( $row->person_id ).'" '.( ( ( !empty( $data[0]->event_supervisor_id ) ) && ( $data[0]->event_supervisor_id == $row->person_id ) ) ? "selected='selected'" : "" ).' >'.( $row->first_name.' '.$row->last_name ).'</option>';
				}
			}
			$return_data .= '</select>';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Event Date:</label>';
			$return_data .= '<input class="datetimepicker form-control" placeholder="'.( date( 'd/m/Y' ) ).'" data-date-format="DD/MM/Y" type="text" name="event_date" value="'.( validate_date( $data[0]->event_date ) ? format_date_client( $data[0]->event_date ) : '' ).'" />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Event Status:</label>';
			$return_data .= '<select name="event_status" class="form-control"><option>Please select</option>';
			$return_data .= '<option value="Pending" '.( ( ( !empty( $data[0]->event_status ) ) && ( strtolower( $data[0]->event_status ) == "pending" ) ) ? "selected='selected'" : "" ).'>Pending</option>';
			$return_data .= '<option value="Completed" '.( ( ( !empty( $data[0]->event_status ) ) && ( strtolower( $data[0]->event_status ) == "completed" ) ) ? "selected='selected'" : "" ).'>Completed</option>';
			$return_data .= '</select>';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Event Review Date:</label>';
			$return_data .= '<input class="datetimepicker form-control" placeholder="'.( date( 'd/m/Y' ) ).'" data-date-format="DD/MM/Y" type="text" name="event_review_date" value="'.( validate_date( $data[0]->event_review_date ) ? format_date_client( $data[0]->event_review_date ) : '' ).'" />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Event Note:</label>';
			$return_data .= '<textarea name="event_note" class="form-control" placeholder="Event Note" rows="3">'.( !empty( $data[0]->event_note ) ? ( string ) $data[0]->event_note : ''  ).'</textarea>';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Date Created:</label>';
			$return_data .= '<input class="datetimepicker form-control" placeholder="'.( date( 'd/m/Y' ) ).'" data-date-format="DD/MM/Y" type="text" disabled value="'.( validate_date( $data[0]->date_created ) ? format_date_client( $data[0]->date_created ) : '' ).'" />';
			$return_data .= '</div>';

			$return_data .= '</div><div class="row"><div class="col-md-4 pull-right">';

			$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, "events" );
			if( $this->user->is_admin || !empty( $item_access->can_edit ) || !empty( $item_access->is_admin ) ){
				$return_data .= '<button id="updateEventBtn" class="btn-success btn-next btn btn-sm btn-block btn-flow margin_top_8">Update Event</button>';
			} else {
				$return_data .= '<button class="btn-success btn btn-sm btn-flow btn-success btn-next submit no-permissions" disabled style="width: 100%;margin-top: 10px;">No Permissions</button>';
			}

			$return_data .= '</div></div>';
			$return_data .= '</form>';


			$return_data .= '<div class="col-md-4 pull-left delete_div">';
			$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, "events" );
			if( !$this->user->is_admin && !empty( $item_access->can_delete ) || !empty( $item_access->is_admin ) ){
				$return_data .= '<button id="deleteEventBtn" class="btn-danger btn btn-sm btn-block" data-event_ID="'.$data[0]->event_id.'">Delete Event</button>';
			} else {
				$return_data .= '<button class="btn-danger btn btn-sm btn-flow btn-next submit no-permissions pull-left push-left" disabled style="width: 100%;margin-top: 10px;">No Permissions</button>';
			}
			$return_data .= '</div>';

		} else {
			$return_data .= '<div width="100%">';
			$return_data .= '<div><div colspan="2">'.$this->config->item( "no_data" ).'</div></div>';
			$return_data .= '</div>';
		}

		return $return_data;
	}


	/**
	* 	Update Health Log status
	**/
	public function update_health_log_status( $health_log_id = false, $page = 'health' ){

		$return_data['status'] = 0;

		$health_log_id 			= ( $this->input->post( 'health_log_id' ) ) ? $this->input->post( 'health_log_id' ) : $health_log_id ;
		$upddata['log_status'] 	= ( $this->input->post( 'log_status' ) ) ? $this->input->post( 'log_status' ) : false ;

		# Check module-item access
		$section 			= ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access 		= $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {

			if( !empty( $health_log_id ) ){
				$postdata['data']			= $upddata;
				$postdata['account_id'] 	= $this->user->account_id;
				$postdata['health_log_id'] 	= $health_log_id;

				$url 					= 'people/update_log_status';
				$API_result				= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );
				$result		  			= ( isset( $API_result->updated_log ) ) ? $API_result->updated_log : null;
				$message	  			= ( isset( $API_result->message ) ) ? $API_result->message : 'Request completed!';

				if( !empty( $result ) ){
					$return_data['status']		= 1;
					$return_data['updated_log'] = $result	;
				}

				$return_data['status_msg'] = $message;

			} else {
				$return_data['status_msg'] 	= "Missing Health log ID";
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/**
	*	 An AJAX call to get Health Log Note(s)
	**/
 	public function get_health_log_notes( $page = 'health' ){
		$result = false;

		# Check module-item access
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$result['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {

			$postset 						= $this->input->post();
			if( !empty( $postset ) ){
				$postdata = [];
				$postdata["health_log_id"] 	= ( !empty( $postset["health_log_id"] ) ) ? ( int ) $postset["health_log_id"] : false ;
				$postdata["account_id"]		= $this->user->account_id;

				$url 						= 'people/health_log_notes';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
				$result = [
					'status'				=> $API_result->status,
					'message'				=> $API_result->message,
				];

				$result['health_log_notes'] =	( !empty( $API_result->h_log_notes ) ) ? $this->load_health_log_notes_view( $API_result->h_log_notes ) : $this->load_health_log_notes_view();

				print_r( json_encode( $result ) );
				die();
			}
		}
	}


	/*
	* 	Prepare a view for Note(s) related to the Health Log
	*/
	private function load_health_log_notes_view( $data = false ){

		$return_data = '';
		$return_data = '<table width="100%">';
		$return_data .= '<tr>';
		$return_data .= '<th width="10%">Comment ID</th>';
		$return_data .= '<th width="60%">Comment</th>';
		$return_data .= '<th width="15%">Date Created</th>';
		$return_data .= '<th width="15%">Created By</th>';
		$return_data .= '</tr>';

		if( !empty( $data ) ){
			foreach( $data as $current){
				foreach( $current as $key => $row ){
					$return_data .= '<tr>';
					$return_data .= '<td>'.$row->health_note_id.'</td>';
					$return_data .= '<td>'.$row->note.'</td>';
					$return_data .= '<td>'.( ( validate_date( $row->date_created ) ) ? format_date_client( $row->date_created ) : '' ).'</td>';
					$return_data .= '<td>'.$row->created_by_full_name.'</td>';
					$return_data .= '</tr>';
				}
			}
		} else {
			$return_data .= '<tr><td colspan="5"><br/>'.$this->config->item( "no_records" ).'</td></tr>';
		}

		$return_data .= '</table>';

		return $return_data;
	}


	/**
	*	Create Health Note
	**/
 	public function create_health_note( $page = "health" ){
		$result['status'] = 0;

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$result['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postset 						= $this->input->post();
			if( !empty( $postset ) ){

				$postdata = [];
				$postdata["health_log_id"] 	= ( !empty( $postset["health_log_id"] ) ) ? ( int ) $postset["health_log_id"] : false ;
				$postdata["person_id"] 		= ( !empty( $postset["person_id"] ) ) ? ( int ) $postset["person_id"] : false ;
				$postdata["dataset"] 		= $postset;
				$postdata["account_id"]		= $this->user->account_id;

				$url 						= 'people/create_health_log_note';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( ( !empty( $API_result->status ) && ( $API_result->status == true ) ) ){

					$result = [
						'status'				=> $API_result->status,
						'status_msg'			=> $API_result->message,
						'new_health_log_note'	=> $API_result->new_health_log_note,
					];
				} else {
					if( ( !empty( $API_result->message ) ) ){
						$result['status'] = false ;
						$result['status_msg'] = $API_result->message;
					}
				}
			}
		}
		print_r( json_encode( $result ) );
		die();
	}


	/*
	*	Update event
	*/
	public function update_event( $page = "events" ){
		$result['status'] 	= 0;

		$section 			= ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access 		= $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$result['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postset 						= $this->input->post();
			if( !empty( $postset ) ){
				$postdata = [];
				$postdata["event_id"] 		= ( !empty( $postset["event_id"] ) ) ? ( int ) $postset["event_id"] : false ;
				$postdata["dataset"] 		= $postset;
				$postdata["account_id"]		= $this->user->account_id;

				$url 						= 'people/update_event';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( ( !empty( $API_result->status ) && ( $API_result->status == true ) ) ){
					$result = [
						'status'			=> $API_result->status,
						'status_msg'		=> $API_result->message,
						'updated_event'		=> $API_result->updated_event,
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


	/*
	*	Update checklist
	*/
	public function update_checklist( $page = "checklist" ){

		# Check module-item access
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$result['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postset 						= $this->input->post();
			if( !empty( $postset ) ){

				$postdata = [];
				$postdata["account_id"]		= $this->user->account_id;
				$postdata["person_id"] 		= ( !empty( $postset["person_id"] ) ) ? ( int ) $postset["person_id"] : false ;
				$postdata["category"] 		= ( !empty( $postset["category"] ) ) ? urlencode( $postset["category"] )  : false ;
				$postdata["item_type"] 		= ( !empty( $postset["item_type"] ) ) ? urlencode( $postset["item_type"] ) : false ;
				$postdata["answers"] 		= ( !empty( $postset["answers"] ) ) ? json_encode( $postset["answers"] ) : false ;

				if( !empty( $postset["response_id"] ) ) {
					$postdata["response_id"] 		=  ( int ) $postset["response_id"] ;
				}

				$url 						= 'people/update_checklist';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( ( !empty( $API_result->status ) && ( $API_result->status == true ) ) ){

					$result = [
						'status'				=> $API_result->status,
						'status_msg'			=> $API_result->message,
						'updated_checklist'		=> $API_result->updated_checklist,
					];
				} else {
					if( ( !empty( $API_result->message ) ) ){
						$result['status'] = false ;
						$result['status_msg'] = $API_result->message;
					}
				}
			}
		}

		print_r( json_encode( $result ) );
		die();
	}


	/**
	*	Create Security Screening Log
	**/
 	public function create_security_log( $page = "security" ){

		$return_data['status']  = 0;

		# Check module-item access
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg']	= $this->config->item( 'ajax_access_denied' );
		} else {
			$postset 					= $this->input->post();
			if( !empty( $postset ) ){
				$postdata = [];
				$postdata 					= $postset;
				$postdata["account_id"]		= $this->user->account_id;
				$url 						= 'people/create_security_log';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( ( !empty( $API_result->status ) && ( $API_result->status == true ) ) ){

					$return_data = [
						'status'		=> ( string ) $API_result->status,
						'status_msg'	=> $API_result->message,
						'security_log'	=> $API_result->security_log,
					];
				} else {
					if( ( !empty( $API_result->message ) ) ){
						$return_data['status_msg']	= $API_result->message;
					} else {
						$return_data['status_msg']	= "Something went wrong";
					}
				}
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/**
	*	 An AJAX call to get Security Screening Log(s)
	**/
  	public function security_logs( $page = 'security' ){

		$return_data['status'] = 0;

		# Check module-item access
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data .= $this->config->item( 'ajax_access_denied' );
		} else {

			$postset 						= $this->input->post();
			if( !empty( $postset ) ){
				$postdata = [];
				$postdata 					= $postset;
				$postdata["account_id"]		= $this->user->account_id;

				$url 						= 'people/security_logs';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
				$result['status_msg'] 		= ( !empty( $API_result->message ) ) ? ( $API_result->message ) : false;
				$result['status'] 			= ( !empty( $API_result->status ) ) ? ( $API_result->status ) : false;

				$result['security_logs'] 	= ( !empty( $API_result->security_logs ) ) ? $this->load_security_logs_view( $API_result->security_logs ) : false;
			}
		}
		print_r( json_encode( $result ) );
		die();
	}


	/*
	* 	Prepare a view for Security Screening log with ability to update
	*/
	private function load_security_logs_view( $data ){

		$return_data = '<div class="col-md-12 col-sm-12 col-xs-12">';

		if( !empty( $data ) ){

			$screening_supervisor			= [];
			$postdata["account_id"]			= $this->user->account_id;
			$postdata["limit"]				= 999;
			$url 							= 'people/people';
			$API_result						= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
			$screening_supervisor 			= ( !empty( $API_result->people ) ) ? $API_result->people : $data['event_supervisor'] ;

			$screening_type					= ['Basic DBS', 'Enhanced DBS', 'BS7858', 'NPCC Letter', 'Intruder', 'Provisional Screening Certificate' ];
			$screening_result				= ['Pending', 'Passed', 'Failed'];

			$return_data .= '<form id="update_security_log">';
			$return_data .= '<input type="hidden" name="page" value="health" />';
			$return_data .= '<input type="hidden" name="person_id" value="'.$data[0]->person_id.'" />';
			$return_data .= '<input type="hidden" name="log_id" value="'.$data[0]->log_id.'" />';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Valid From</label>';
			$return_data .= '<input name="valid_from" value="'.( ( validate_date( $data[0]->valid_from ) ) ? format_date_client( $data[0]->valid_from ) : '' ).'" class="form-control datetimepicker" placeholder="'.( date( "d/m/Y" ) ).'" data-date-format="DD/MM/Y" />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Expiry Date</label>';
			$return_data .= '<input name="expiry_date" value="'.( ( validate_date( $data[0]->expiry_date ) ) ? format_date_client( $data[0]->expiry_date ) : '' ).'" class="form-control datetimepicker" placeholder="'.( date( "d/m/Y" ) ).'" data-date-format="DD/MM/Y" />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Reminder Date</label>';
			$return_data .= '<input name="reminder_date" value="'.( ( validate_date( $data[0]->reminder_date ) ) ? format_date_client( $data[0]->reminder_date ) : '' ).'" class="form-control datetimepicker" placeholder="'.( date( "d/m/Y" ) ).'" data-date-format="DD/MM/Y" />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Sent Date</label>';
			$return_data .= '<input value="'.( ( validate_date( $data[0]->sent_date ) ) ? format_date_client( $data[0]->sent_date ) : '' ).'" class="form-control datetimepicker" placeholder="'.( date( "d/m/Y" ) ).'" data-date-format="DD/MM/Y" readonly />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Sent By</label>';
			$return_data .= '<select name="sent_by" class="form-control" required>';
			$return_data .= '<option value="">Please select</option>';

			if( !empty( $screening_supervisor ) ){
				foreach( $screening_supervisor as $key => $row ){
					$return_data .= '<option value="'.$row->person_id.'" '.( ( !empty( $data[0]->sent_by ) && ( $data[0]->sent_by == $row->person_id ) ) ? 'selected="selected"' : '' ).'>'.$row->first_name.' '.$row->last_name.'</option>';
				}
			} else {
				'<option value="3">Amanda Simmons</option>';
			}
			$return_data .= '</select></div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Reference Number</label>';
			$return_data .= '<input type="text" name="ref_number" class="form-control" placeholder="Reference Number" value="'.( ( !empty( $data[0]->ref_number ) ) ? $data[0]->ref_number : '' ).'" />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Screening Provider</label>';
			$return_data .= '<input type="text" name="screening_provider" class="form-control" placeholder="Screening Provider" value="'.( ( !empty( $data[0]->screening_provider ) ) ? $data[0]->screening_provider : '' ).'" />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Screening Type</label>';
			$return_data .= '<select name="screening_type" class="form-control" required>';
			$return_data .= '<option value="">Please select</option>';
			if( !empty( $screening_type ) ){
				foreach( $screening_type as $key => $row ){
				$return_data .= '<option value="'.$row.'" '.( ( !empty( $data[0]->screening_type ) && ( strtolower( $data[0]->screening_type ) == strtolower( $row ) ) ) ? 'selected="selected"' : '' ).' >'.$row.'</option>';
				}

			} else {
				$return_data .= '<option value="Basic DBS">Basic DBS</option><option value="Enhanced DBS">Enhanced DBS</option><option value="BS7858">BS7858</option>
				<option value="NPCC Letter">NPCC Letter</option><option value="Intruder">Intruder</option><option value="Provisional Screening Certificate">Provisional Screening Certificate</option>';
			}
			$return_data .= '</select></div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Screening Result</label>';
			$return_data .= '<select name="screening_result" class="form-control" required>';
			$return_data .= '<option value="">Please select</option>';

			if( !empty( $screening_result ) ){
				foreach( $screening_result as $key => $row ){
					$return_data .= '<option value="'.$row.'" '.( ( !empty( $data[0]->screening_result ) && ( strtolower( $data[0]->screening_result ) == strtolower( $row ) ) ) ? 'selected="selected"' : '' ).'>'.$row.'</option>';
				}

			} else {
				$return_data .= '<option value="Pending">Pending</option><option value="Passed">Passed</option><option value="Failed">Failed</option>';
			}
			$return_data .= '</select></div>';


			$return_data .= '<div class="input-group form-group checkbox executive_acceptance_of_risk" style="display: '.( ( ( !empty( $data[0]->screening_result ) && ( strtolower( $data[0]->screening_result ) == "failed" ) ) ? 'table' : 'none' ) ).';">';
			$return_data .= '<label class="input-group-addon">Executive Acceptance of Risk</label>';
			$return_data .= '<label class="input-group checkbox_in_input">';
			$return_data .= '<input name="executive_acceptance_of_risk" type="hidden" value="no">';
			$return_data .= '<input name="executive_acceptance_of_risk" class="form-control" type="checkbox" value="yes" '.( ( ( !empty( $data[0]->executive_acceptance_of_risk ) && ( strtolower( $data[0]->executive_acceptance_of_risk ) == true ) ) ? 'checked="checked"' : '' ) ).' >';
			$return_data .= '<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>';
			$return_data .= '</label></div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<textarea name="screening_note" class="form-control" rows="3" placeholder="Notes">'.( ( !empty( $data[0]->screening_note ) ) ? $data[0]->screening_note : '' ).'</textarea>';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Created By</label>';
			$return_data .= '<input type="text" class="form-control" placeholder="Created By" value="'.( ( !empty( $data[0]->created_by_full_name ) ) ? $data[0]->created_by_full_name : '' ).'" readonly />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Created Date</label>';
			$return_data .= '<input value="'.( ( validate_date( $data[0]->created_date ) ) ? format_date_client( $data[0]->created_date ) : '' ).'" class="form-control datetimepicker" placeholder="'.( date( "d/m/Y" ) ).'" data-date-format="DD/MM/Y" readonly />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Last Modified:</label>';
			$return_data .= '<input class="form-control" data-date-format="DD/MM/Y" type="text" value="'.( validate_date( $data[0]->modified_date ) ? format_datetime_client( $data[0]->modified_date ) : '' ).'" readonly="readonly" />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Last Modified By:</label>';
			$return_data .= '<input class="form-control" type="text" value="'.( !empty( $data[0]->last_modified_by_full_name ) ? ( $data[0]->last_modified_by_full_name ) : '' ).'" readonly="readonly" />';
			$return_data .= '</div>';

			$return_data .= '<div class="row"><div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 pull-right">';

			$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, "security" );
			if( $this->user->is_admin || !empty( $item_access->can_edit ) || !empty( $item_access->is_admin ) ){
				$return_data .= '<button id="updateSecurityLog" class="btn-success btn btn-sm btn-flow btn-success btn-next submit" style="width: 100%;">Update</button>';
			} else {
				$return_data .= '<button class="btn-success btn btn-sm btn-flow btn-success btn-next submit no-permissions" disabled style="width: 100%;margin-top: 10px;">No Permissions</button>';
			}

			$return_data .= '</div></div>';
			$return_data .= '</form>';

			$return_data .= '<div class="col-md-4 pull-left delete_div">';
			$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, "security" );
			if( $this->user->is_admin || !empty( $item_access->can_delete ) || !empty( $item_access->is_admin ) ){
				$return_data .= '<button id="deleteSecurityBtn" class="btn-danger btn btn-sm btn-block" data-security_log_ID="'.$data[0]->log_id.'">Delete Log</button>';
			} else {
				$return_data .= '<button class="btn-danger btn btn-sm btn-flow btn-next submit no-permissions pull-left push-left" disabled style="width: 100%;margin-top: 10px;">No Permissions</button>';
			}
			$return_data .= '</div>';


		} else {
			$return_data .= '<td colspan="5">'.$this->config->item( "no_records" ).'</td>';
		}

		$return_data .= '</div>';

		return $return_data;
	}


	/*
	*	Update Security log
	*/
	public function update_security_log( $page = "security" ){
		$return_data['status'] = 0;

		# Check module-item access
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postset 						= $this->input->post();
			if( !empty( $postset ) ){

				$postdata 					= $postset;
				$postdata["account_id"]		= $this->user->account_id;
				$postdata["person_id"] 		= ( !empty( $postset["person_id"] ) ) ? ( int ) $postset["person_id"] : false ;
				$postdata["log_id"] 		= ( !empty( $postset["log_id"] ) ) ? urlencode( $postset["log_id"] )  : false ;

				$url 						= 'people/update_security_log';

				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( ( !empty( $API_result->status ) && ( $API_result->status == true ) ) ){

					$return_data = [
						'status'				=> $API_result->status,
						'status_msg'			=> $API_result->message,
						'updated_security_log'	=> $API_result->updated_security_log,
					];
				} else {
					if( ( !empty( $API_result->message ) ) ){
						$return_data['status_msg'] = $API_result->message;
					}
				}
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/*
	*	Add a leaver's details log
	*/
	public function create_leavers_details_log( $page = "leaver" ){
		$return_data['status'] = 0;

		# Check module-item access
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postset 						= $this->input->post();
			if( !empty( $postset ) ){

				$postdata["data"] 			= $postset;
				$postdata["account_id"]		= $this->user->account_id;
				$postdata["person_id"] 		= ( !empty( $postset["person_id"] ) ) ? ( int ) $postset["person_id"] : false ;
				$url 						= 'people/create_leavers_log';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( ( !empty( $API_result->status ) && ( $API_result->status == true ) ) ){
					$return_data = [
						'status'			=> $API_result->status,
						'status_msg'		=> $API_result->message,
						'new_leavers_log'	=> $API_result->new_leavers_log,
					];
				} else {
					if( ( !empty( $API_result->message ) ) ){
						$return_data['status_msg'] = $API_result->message;
					}
				}
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	public function get_event_type_by_cat_id( $event_category_id = false, $page = 'events' ){
		$return_data = [
			'status' => 0
		];

		$event_category_id = ( $this->input->post( 'event_category_id' ) ) ? $this->input->post( 'event_category_id' ) : $event_category_id ;

		# Check module-item access
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {

			$postdata['account_id']			=	$this->user->account_id;
			$postdata['event_category_id']	=	$event_category_id;
			$url 							= 'people/event_types';
			$API_result						= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );


			$result		  					= ( isset( $API_result->event_types ) ) ? $API_result->event_types : null;
			$message	  					= ( isset($API_result->message) ) ? $API_result->message : 'Request completed!';

			if( !empty( $result ) ){
				$return_data['status']		= 1;
				$return_data['event_types'] = $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	public function get_position_details( $page = 'positions' ){
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
				$postdata 					= $postset;

				$url 						= 'people/job_positions';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );

				$return_data = [
					'status'				=> $API_result->status,
					'message'				=> $API_result->message,
				];

				$return_data['position_details'] =	( !empty( $API_result->job_positions ) ) ? $this->load_position_details_view( $API_result->job_positions ) : false;

				print_r( json_encode( $return_data ) );
				die();
			}
		}
	}


	/*
	* 	Prepare a view for the Position log update
	*/
	private function load_position_details_view( $dataset ){

		$postdata 				= [];

		$departments			= false;
		$postdata["account_id"]	= $this->user->account_id;
		$url 					= 'people/departments';
		$API_result				= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
		$departments			= ( !empty( $API_result->departments ) ) ? $API_result->departments : null;

		$url 					= 'people/job_titles';
		$API_result				= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
		$job_titles				= ( !empty( $API_result->job_titles ) ) ? $API_result->job_titles : null;

		$position_types			= ["Fixed Term Contract Conditional", "Fixed Term Contract Confirmed", "Permanent Conditional", "Permanent Confirmed", "Temporary Conditional", "Temporary Confirmed"];
		$businesses				= ["TechLive"];
		
		$locations				= $this->locations;

		$url 					= 'people/people';
		$postdata["limit"]		= 999;
		$API_result				= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
		$line_managers			= ( !empty( $API_result->people ) ) ? $API_result->people : null;
		$people_list			= $line_managers;

		$url 					= 'people/job_positions';
		$postdata["person_id"]	= $dataset[0]->person_id;
		$API_result				= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
		$job_positions			= ( !empty( $API_result->job_positions ) ) ? $API_result->job_positions : null;

		$return_data = '';

		if( !empty( $dataset ) ){
			$return_data .= '<form id="position_update_in_modal">';
			$return_data .= '<input type="hidden" name="position_id" value="'.$dataset[0]->position_id.'" />';

			$return_data .= '<div style="width:100%;">';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Department:</label>';
			$return_data .= '<select name="department_id" class="form-control" required><option value="">Please select</option>';

			if( !empty( $departments ) ){
				foreach( $departments as $row ){
					$return_data .= '<option value="'.( $row->department_id ).'" ';
					if( !empty( $dataset[0]->department_id ) && ( $dataset[0]->department_id == $row->department_id ) ){
						$return_data .= 'selected="selected"';
					}

					$return_data .='>'.( $row->department_name ).'</option>';
				}
			}
			$return_data .= '</select>';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Job Title:</label>';
			$return_data .= '<select name="job_title_id" class="form-control" required><option value="">Please select</option>';

			if( !empty( $job_titles ) ){
				foreach( $job_titles as $row ){
					$return_data .= '<option value="'.( $row->job_title_id ).'" '.( ( ( !empty( $dataset[0]->job_title_id ) ) && ( $dataset[0]->job_title_id == $row->job_title_id ) ) ? "selected='selected'" : "" ).' >'.( $row->job_title ).'</option>';
				}
			}
			$return_data .= '</select>';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Position Type:</label>';
			$return_data .= '<select name="position_type" class="form-control" required><option value="">Please select</option>';

			if( !empty( $position_types ) ){
				foreach( $position_types as $row ){
					$return_data .= '<option value="'.( $row ).'" '.( ( ( !empty( $dataset[0]->position_type ) ) && ( strtolower( $dataset[0]->position_type ) == strtolower( $row ) ) ) ? "selected='selected'" : "" ).' >'.( $row ).'</option>';
				}
			}
			$return_data .= '</select>';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Business:</label>';
			$return_data .= '<select name="business" class="form-control"><option value="">Please select</option>';

			if( !empty( $businesses ) ){
				foreach( $businesses as $row ){
					$return_data .= '<option value="'.( $row ).'" '.( ( ( !empty( $dataset[0]->business ) ) && ( strtolower( $dataset[0]->business ) == strtolower( $row ) ) ) ? "selected='selected'" : "" ).' >'.( $row ).'</option>';
				}
			}
			$return_data .= '</select>';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Locations:</label>';
			$return_data .= '<select name="location" class="form-control"><option value="">Please select</option>';

			if( !empty( $locations ) ){
				asort( $locations );
				foreach( $locations as $key => $row ){
					$return_data .= '<option value="'.( $row ).'" '.( ( ( !empty( $dataset[0]->location ) ) && ( strtolower( $dataset[0]->location ) == strtolower( $row ) ) ) ? "selected='selected'" : "" ).' >'.( $row ).'</option>';
				}
			}
			$return_data .= '</select>';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Line Manager:</label>';
			$return_data .= '<select name="line_manager_id" class="form-control"><option>Please select</option>';
			if( !empty( $line_managers ) ){
				foreach( $line_managers as $row ){
					$return_data .= '<option value="'.( $row->person_id ).'" '.( ( ( !empty( $dataset[0]->line_manager_id ) ) && ( $dataset[0]->line_manager_id == $row->person_id ) ) ? "selected='selected'" : "" ).' >'.( ucfirst( $row->first_name ) ).' '.( ucfirst( $row->last_name ) ).'</option>';
				}
			}
			$return_data .= '</select>';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Position Start Date:</label>';
			$return_data .= '<input class="datetimepicker form-control" placeholder="'.( date( 'd/m/Y' ) ).'" data-date-format="DD/MM/Y" type="text" name="job_start_date" value="'.( validate_date( $dataset[0]->job_start_date ) ? format_date_client( $dataset[0]->job_start_date ) : '' ).'" />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Position End Date:</label>';
			$return_data .= '<input class="datetimepicker form-control" placeholder="'.( date( 'd/m/Y' ) ).'" data-date-format="DD/MM/Y" type="text" name="job_end_date" value="'.( validate_date( $dataset[0]->job_end_date ) ? format_date_client( $dataset[0]->job_end_date ) : '' ).'" />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Position Note:</label>';
			$return_data .= '<textarea name="position_notes" class="form-control" placeholder="Position Note" rows="3">'.( !empty( $dataset[0]->position_notes ) ? ( string ) $dataset[0]->position_notes : ''  ).'</textarea>';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Date Created:</label>';
			$return_data .= '<input class="form-control" placeholder="'.( date( 'd/m/Y' ) ).'" data-date-format="DD/MM/Y" type="text" value="'.( validate_date( $dataset[0]->date_created ) ? format_datetime_client( $dataset[0]->date_created ) : '' ).'" readonly="readonly" />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Created By:</label>';
			$return_data .= '<input class="form-control" placeholder="" type="text" value="'.( !empty( $dataset[0]->created_by_full_name ) ? ( $dataset[0]->created_by_full_name ) : '' ).'" readonly="readonly" />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Last Modified:</label>';
			$return_data .= '<input class="form-control" data-date-format="DD/MM/Y" type="text" value="'.( validate_date( $dataset[0]->last_modified ) ? format_datetime_client( $dataset[0]->last_modified ) : '' ).'" readonly="readonly" />';
			$return_data .= '</div>';

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Last Modified By:</label>';
			$return_data .= '<input class="form-control" type="text" value="'.( !empty( $dataset[0]->last_modified_by_ful_name ) ? ( $dataset[0]->last_modified_by_ful_name ) : '' ).'" readonly="readonly" />';
			$return_data .= '</div>';
			$return_data .= '</div><div class="row"><div class="col-md-4 pull-right">';

			$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, "positions" );
			if( $this->user->is_admin || !empty( $item_access->can_edit ) || !empty( $item_access->is_admin ) ){
				$return_data .= '<button id="updatePositionBtn" class="btn-success btn-next btn btn-sm btn-block btn-flow margin_top_8">Update Position</button>';
			} else {
				$return_data .= '<button class="btn-success btn btn-sm btn-flow btn-success btn-next submit no-permissions" disabled style="width: 100%;margin-top: 10px;">No Permissions</button>';
			}

			$return_data .= '</div></div>';
			$return_data .= '</form>';

			$return_data .= '<div class="col-md-4 pull-left delete_div">';
			$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, "positions" );
			if( $this->user->is_admin || !empty( $item_access->can_delete ) || !empty( $item_access->is_admin ) ){
				$return_data .= '<button id="deletePositionBtn" class="btn-danger btn btn-sm btn-block" data-position_ID="'.$dataset[0]->position_id.'">Delete Position</button>';
			} else {
				$return_data .= '<button class="btn-danger btn btn-sm btn-flow btn-next submit no-permissions pull-left push-left" disabled style="width: 100%;margin-top: 10px;">No Permissions</button>';
			}
			$return_data .= '</div>';
		} else {
			$return_data .= '<div width="100%">';
			$return_data .= '<div><div colspan="2">'.$this->config->item( "no_data" ).'</div></div>';
			$return_data .= '</div>';
		}

		return $return_data;
	}


	/*
	*	Update position
	*/
	public function update_position( $page = "positions" ){
		$result['status'] 	= 0;

		$section 			= ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access 		= $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$result['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postset 						= $this->input->post();
			if( !empty( $postset ) ){
				$postdata = [];
				$postdata["position_id"] 	= ( !empty( $postset["position_id"] ) ) ? ( int ) $postset["position_id"] : false ;
				$postdata["dataset"] 		= $postset;
				$postdata["account_id"]		= $this->user->account_id;

				$url 						= 'people/update_position';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( ( !empty( $API_result->status ) && ( $API_result->status == true ) ) ){
					$result = [
						'status'			=> $API_result->status,
						'status_msg'		=> $API_result->message,
						'updated_position'	=> $API_result->updated_position,
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


	/*
	*	Delete position
	*/
	public function delete_position( $page = "positions" ){
		$result['status'] 	= 0;

		$section 			= ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access 		= $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_delete ) && empty( $item_access->is_admin ) ){
			$result['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postset 						= $this->input->post();
			if( !empty( $postset ) ){
				$postdata = [];
				$postdata["position_id"] 	= ( !empty( $postset["position_id"] ) ) ? ( int ) $postset["position_id"] : false ;
				$postdata["account_id"]		= $this->user->account_id;

				$url 						= 'people/delete_position';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( ( !empty( $API_result->status ) && ( $API_result->status == true ) ) ){
					$result = [
						'status'			=> $API_result->status,
						'status_msg'		=> $API_result->message,
						'deleted_position'	=> $API_result->deleted_position,
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


	/*
	*	Update Event
	*/
	public function delete_event( $page = "events" ){
		$result['status'] 	= 0;

		$section 			= ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access 		= $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_delete ) && empty( $item_access->is_admin ) ){
			$result['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postset 						= $this->input->post();
			if( !empty( $postset ) ){
				$postdata = [];
				$postdata["event_id"] 	= ( !empty( $postset["event_id"] ) ) ? ( int ) $postset["event_id"] : false ;
				$postdata["account_id"]		= $this->user->account_id;

				$url 						= 'people/delete_event';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( ( !empty( $API_result->status ) && ( $API_result->status == true ) ) ){
					$result = [
						'status'			=> $API_result->status,
						'status_msg'		=> $API_result->message,
						'deleted_event'		=> $API_result->deleted_event,
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


	public function delete_security_log( $page="security" ){
		$result['status'] 	= 0;

		$section 			= ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$item_access 		= $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_delete ) && empty( $item_access->is_admin ) ){
			$result['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postset 						= $this->input->post();

			if( !empty( $postset ) ){
				$postdata = [];
				$postdata["security_log_ID"]= ( !empty( $postset["security_log_ID"] ) ) ? ( int ) $postset["security_log_ID"] : false ;
				$postdata["account_id"]		= $this->user->account_id;

				$url 						= 'people/delete_security_log';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( ( !empty( $API_result->status ) && ( $API_result->status == true ) ) ){
					$result = [
						'status'			=> $API_result->status,
						'status_msg'		=> $API_result->message,
						'deleted_security_log'	=> $API_result->deleted_security_log,
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
			$postdata["address_type_id"]	= [2,3,4];
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
			

			$return_data .= '<div class="input-group form-group">';
			$return_data .= '<label class="input-group-addon">Contact Note:</label>';
			$return_data .= '<textarea name="contact_note" class="form-control" placeholder="Contact Note" rows="3">'.( !empty( $dataset->contact_note ) ? ( string ) $dataset->contact_note : ''  ).'</textarea>';
			$return_data .= '</div>';
			
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
			
			/*
			$return_data .= '<div class="col-md-4 pull-left delete_div">';
			$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, "positions" );
			if( $this->user->is_admin || !empty( $item_access->can_delete ) || !empty( $item_access->is_admin ) ){
				$return_data .= '<button id="deletePositionBtn" class="btn-danger btn btn-sm btn-block" data-position_ID="'.$dataset->position_id.'">Delete Position</button>';
			} else {
				$return_data .= '<button class="btn-danger btn btn-sm btn-flow btn-next submit no-permissions pull-left push-left" disabled style="width: 100%;margin-top: 10px;">No Permissions</button>';
			}
			$return_data .= '</div>';
			*/
		} else {
			$return_data .= '<div width="100%">';
			$return_data .= '<div><div colspan="2">'.$this->config->item( "no_data" ).'</div></div>';
			$return_data .= '</div>';
		}

		return $return_data;
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
}