<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Job extends MX_Controller {

	function __construct(){
		parent::__construct();

		if( !$this->identity() ){
			redirect('webapp/user/login', 'refresh');
		}

		$this->module_id 	   = $this->webapp_service->_get_module_id( $this->router->fetch_class() );
		$this->load->library('pagination');
		$this->load->model('serviceapp/Job_model','job_service');
		
		$this->tess_linked_accounts = [8];
		$this->priority_ratings 	= [ 'Low', 'Medium', 'High' ];
		
	}

	//redirect if needed, otherwise display the user list
	function index(){

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){
			//access denied
			$this->_render_webpage( 'errors/access-denied', false );
		}else{
			redirect('webapp/job/overview', 'refresh');
		}
	}


	/** Get list of jobs **/
	public function jobs( $job_id = false ){

		redirect('webapp/job/overview', 'refresh');

		if( $job_id ){
			redirect('webapp/job/profile/'.$job_id, 'refresh');
		}

		#Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {

			$postdata				= ['account_id'=>$this->user->account_id, 'job_date'=>date('Y-m-d')];
			$job_stats				= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_stats', $postdata, ['auth_token'=>$this->auth_token], true );
			$data['job_stats']  	= ( isset($job_stats->job_stats) ) ? $job_stats->job_stats : null;

			$job_statuses		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['job_statuses']	= ( isset($job_statuses->job_statuses) ) ? $job_statuses->job_statuses : null;

			$job_types		 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_types', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['job_types'] 		= ( isset($job_types->job_types) ) ? $job_types->job_types : null;

			$this->_render_webpage( 'job/index', $data );
		}
	}


	//View Job profile
	function profile( $job_id = false, $page = 'details' ){

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		}else if( $job_id ){
			$job_details		 = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/jobs', ['account_id'=>$this->user->account_id,'job_id'=>$job_id], ['auth_token'=>$this->auth_token], true );
			$data['job_details'] = ( isset($job_details->jobs) ) ? $job_details->jobs : null;
			if( !empty( $data['job_details'] ) ){
				$contract_id 		= $data['job_details']->contract_id;
				$bom_category_id 	= $data['job_details']->bom_category_id;
				$run_admin_check 	= false;
				#Get allowed access for the logged in user
				$data['permissions']= $item_access;
				$tab_permissions	= !empty( $item_access->tab_permissions ) ? $item_access->tab_permissions : false;
				$data['active_tab']	= $page;
				
				$module_items 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'access/account_modules_items', ['account_id'=>$this->user->account_id, 'module_id'=>$this->module_id ], ['auth_token'=>$this->auth_token], true );
				$data['module_tabs']= ( isset( $module_items->module_items ) ) ? $module_items->module_items : null;

				#$reordered_tabs 		 = reorder_tabs( $data['module_tabs'] );
				#$data['module_tabs'] 	 = ( !empty( $reordered_tabs['module_tabs'] ) ) ? $reordered_tabs['module_tabs'] : $data['module_tabs'];
				$data['more_list_active']= ( !empty( $reordered_tabs['more_list'] ) && in_array( $page, $reordered_tabs['more_list'] )  ) ? true : false;

				$data['tab_permissions'] = !empty( $tab_permissions->{$page} ) ? $tab_permissions->{$page} : false;

				$data['priority_ratings']= $this->priority_ratings;

				switch( $page ){
					case 'stock':
						
						$toggled					= ( !empty( $this->input->get( 'toggled' ) ) ? $this->input->get( 'toggled' ) : false );
						$data['toggled_section']	= $toggled;

						$stock_and_boms	 = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/consumed_items', ['account_id'=>$this->user->account_id, 'job_id'=>$job_id, 'grouped'=>1 ], ['auth_token'=>$this->auth_token], true );
						$consumed_items  = ( isset( $stock_and_boms->consumed_items ) ) ? $stock_and_boms->consumed_items : null;
						$data['job_details']->consumed_items = $consumed_items;

						if( $this->input->get('action') ){
							$getdata = array_merge(['account_id'=>$this->user->account_id], $this->input->get() );
							$this->download_consumed_items( $job_id, $data['job_details'] );
						}

						$stock_items 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'stock/stock_items', ['account_id'=>$this->user->account_id, 'where'=>['ajax_req'=>1], 'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
						$stock_items			= ( isset( $stock_items->stock_items ) ) ? $stock_items->stock_items : null;
						$data['stock_items']	= ( !empty( $stock_items ) ) ? json_encode( $stock_items ) : [];

						$bom_items 				= $this->webapp_service->api_dispatcher( $this->api_end_point.'stock/bom_items', ['account_id'=>$this->user->account_id, 'where'=>['ajax_req'=>1, 'bom_category_id'=>$bom_category_id ], 'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
						$bom_items				= ( isset( $bom_items->bom_items ) ) ? $bom_items->bom_items : null;

						$data['bom_items']		= ( !empty( $bom_items ) ) ? json_encode( $bom_items ) : false;
						$data['include_page'] 	= 'job_stock.php';
						break;
					case 'communication':
						$data['include_page'] = 'job_communication.php';
						break;
					case 'risk-assessment':
					case 'risk_assessment':
						$ra_records		 	  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'risk_assessment/ra_records', ['account_id'=>$this->user->account_id, 'job_id'=>$job_id ], ['auth_token'=>$this->auth_token], true );
						$data['ra_records']   	= ( isset( $ra_records->ra_records ) ) ? $ra_records->ra_records : null;
						$data['completed_risks']= ( !empty( $data['job_details']->ra_responses ) ) ? array_column( $data['job_details']->ra_responses, 'risk_id' ) : [];
						$data['include_page'] 	= 'job_risk_assessment.php';
						$data['ra_responses']   = ( !empty( $data['ra_records'][0]->ra_responses ) ) ? $data['ra_records'][0]->ra_responses : false;

						break;
					case 'documents':
						$ra_docs		 	  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'document_handler/document_list', ['account_id'=>$this->user->account_id, 'job_id'=>$job_id, 'audit_group'=>'risk_assessment' ], ['auth_token'=>$this->auth_token], true );
						$data['ra_docs']   	  	= ( isset($ra_docs->documents->{$this->user->account_id}) ) ? $ra_docs->documents->{$this->user->account_id} : null;
						
						$job_documents		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'document_handler/document_list', ['account_id'=>$this->user->account_id, 'job_id'=>$job_id, 'document_group'=>'job' ], ['auth_token'=>$this->auth_token], true );
						$data['job_documents']  = ( isset( $job_documents->documents->{$this->user->account_id} ) ) ? $job_documents->documents->{$this->user->account_id} : null;
						
						$data['include_page'] 	= 'job_documents.php';
						break;

					case 'contract':
						$contract_details 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'contract/contracts', ['account_id'=>$this->user->account_id, 'contract_id'=>$data['job_details']->contract_id], ['auth_token'=>$this->auth_token], true );
						$contract_details			= ( isset( $contract_details->contract ) ) ? $contract_details->contract : null;
						$data['contract_details']	= !empty( $contract_details[0] ) ? $contract_details[0] : false;
						$data['include_page'] = 'job_contracts.php';
						break;

					case 'evidocs':
						
						if( !empty( $data['job_details']->linked_evidoc_id ) ){
							$evidoc_details		  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/audits', ['account_id'=>$this->user->account_id, 'audit_id'=>$data['job_details']->linked_evidoc_id], ['auth_token'=>$this->auth_token], true );
							$data['evidoc_details'] = ( isset( $evidoc_details->audits ) ) ? $evidoc_details->audits : false;
						}

						$job_evidocs		  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/audits', ['account_id'=>$this->user->account_id, 'job_id' => $job_id, 'inc_responses'=> 1, 'where'=>[ 'grouped'=>1 ] ], ['auth_token'=>$this->auth_token], true );
						$data['job_evidocs'] 	= ( isset( $job_evidocs->audits ) ) ? $job_evidocs->audits : false;
						$data['include_page']	= 'job_evidocs.php';
						break;

					case 'checklists':
						if( !empty( $data['job_details']->external_job_ref ) ){
							$checklists_data		  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/completed_checklists', ['account_id'=>$this->user->account_id, 'job_id'=>$data['job_details']->job_id], ['auth_token'=>$this->auth_token], true );
							$data['checklists_data'] 	= ( isset( $checklists_data->completed_checklists ) ) ? $checklists_data->completed_checklists : false;
						}

						$data['include_page'] = 'job_checklist_details.php';
						break;

					case 'tasks':
	
						$job_tasks		  		= $this->webapp_service->api_dispatcher( $this->api_end_point.'task/tasks', [ 'account_id'=>$this->user->account_id, 'where' => ['job_id'=> $job_id, 'detailed_info'=>1 ] ], ['auth_token'=>$this->auth_token], true );
						$data['job_tasks'] 		= ( isset( $job_tasks->tasks ) ) ? $job_tasks->tasks : false;
						$data['include_page'] 	= 'job_tasks.php';
						break;
						
					case 'assets':
					case 'job_assets':
						$site_assets		  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/assets', [ 'account_id'=>$this->user->account_id, 'where' => [ 'site_id'=> $data['job_details']->site_id ], 'limit' => -1 ], ['auth_token'=>$this->auth_token], true );
						$data['site_assets'] 	= ( isset( $site_assets->assets ) ) ? $site_assets->assets : false;
						
						$job_assets		  		= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_assets', [ 'account_id'=>$this->user->account_id, 'job_id'=> $job_id, 'where' => [ 'grouped'=>1 ] ], ['auth_token'=>$this->auth_token], true );
						$data['job_assets'] 	= ( isset( $job_assets->job_assets ) ) ? $job_assets->job_assets : false;

						$data['linked_assets']	= $this->_unpack_assets_from_disciplines( $this->user->account_id, $data['job_assets'] );
						$data['include_page'] 	= 'job_assets.php';
						break;

					case 'details':
					default:

						if( !empty( $contract_id ) ) {
							$linked_people	 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'contract/linked_people', ['account_id'=>$this->user->account_id, 'contract_id'=>$contract_id], ['auth_token'=>$this->auth_token], true );
							$linked_people				= ( isset( $linked_people->people ) ) ? $linked_people->people : null;
						}

						$data['restricted_people']		= !empty( $linked_people ) ? array_column( $linked_people, 'user_id' ) : [];
						$data['eta_statuses']			= eta_statuses();
						$data['job_durations']			= job_durations();

						$job_types		 	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_types', ['account_id'=>$this->user->account_id, 'where'=>['contract_id'=>$contract_id ], 'limit'=>-1], ['auth_token'=>$this->auth_token], true );

						$data['job_types'] 	  = ( isset($job_types->job_types) ) ? $job_types->job_types : null;
						$job_statuses		  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_statuses', ['account_id'=>false], ['auth_token'=>$this->auth_token], true );
						$data['job_statuses'] = ( isset($job_statuses->job_statuses) ) ? $job_statuses->job_statuses : null;

						$fail_codes		  	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/fail_codes', ['account_id'=>$this->user->account_id, 'limit'=>-1], ['auth_token'=>$this->auth_token], true );
						$data['fail_codes']   = ( isset( $fail_codes->fail_codes ) ) ? $fail_codes->fail_codes : null;

						$ops_params			  = [
							'account_id'=>$this->user->account_id, 
							'where'		=>['include_admins'=>1], 
							'limit'		=>-1
						];
						
						## Apply Primary User conditions
						if( $this->user->is_primary_user && !$this->user->is_admin ){
							$ops_params['where']['associated_user_id'] 	= $this->user->id;
						}
						
						$operatives		  	  		= $this->webapp_service->api_dispatcher( $this->api_end_point.'user/field_operatives', $ops_params, ['auth_token'=>$this->auth_token], true );
						$data['operatives']   		= ( isset( $operatives->field_operatives ) ) ? $operatives->field_operatives : null;

						$tracking_statuses		  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_tracking_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
						$data['tracking_statuses'] 	= ( isset( $tracking_statuses->job_tracking_statuses ) ) ? $tracking_statuses->job_tracking_statuses : null;

						$postcode_regions     		= $this->webapp_service->api_dispatcher( $this->api_end_point.'diary/regions', ['account_id'=>$this->user->account_id, 'limit'=>-1], [ 'auth_token'=>$this->auth_token ], true );
						$data['postcode_regions']   = ( isset( $postcode_regions->regions ) ) ? $postcode_regions->regions : null;

						$job_type_params 			= [ 'account_id'=>$this->user->account_id, 'where'=>[ 'job_type_id'=>$data['job_details']->job_type_id ] ];
						$job_type_details 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_types', $job_type_params, [ 'auth_token'=>$this->auth_token ], true );
						$data['job_type_details'] 	= !empty( $job_type_details->job_types ) ? $job_type_details->job_types: null;

						$dates_with_availability	= $this->webapp_service->api_dispatcher( $this->api_end_point.'diary/available_dates', [ 'account_id'=>$this->user->account_id, 'where'=>[ 'date_from'=>date( 'Y-m-d' ) ] ], [ 'auth_token'=>$this->auth_token ], true );

						$data['dates_with_availability'] 	= !empty( $dates_with_availability->available_dates ) ? $dates_with_availability->available_dates: null;
						$data['dates_with_availability']	= json_encode( $data['dates_with_availability'] );

						$data['include_page'] = 'job_details.php';
						break;
				}
			}

			//Run the admin check if tab needs only admin
			if( !empty( $run_admin_check ) ){
				if( ( !admin_check( $this->user->is_admin, false, ( !empty( $data['permissions'] ) ? $data['permissions']->is_admin : false ) ) ) ){
					$data['admin_no_access'] = true;
				}
			}
			$this->_render_webpage('job/profile', $data );
		}else{
			redirect('webapp/job', 'refresh');
		}
	}

	/*
	* Job lookup / search
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
			$job_id   		= ( $this->input->post( 'job_id' ) ) ? $this->input->post( 'job_id' ) : false;
			$search_term   	= ( $this->input->post( 'search_term' ) ) ? $this->input->post( 'search_term' ) : false;
			$where   	   	= ( $this->input->post( 'where' ) ) ? $this->input->post( 'where' ) : false;
			$limit		   	= ( !empty( $where['limit'] ) )  ? $where['limit']  : DEFAULT_LIMIT;
			$start_index   	= ( $this->input->post( 'start_index' ) ) ? $this->input->post( 'start_index' ) : DEFAULT_OFFSET;
			$offset		   	= ( !empty( $start_index ) ) ? ( ( $start_index - 1 ) * $limit ) : 0;
			$order_by	   	= ( $this->input->post( 'order_by' ) ) ? $this->input->post( 'order_by' ) : false;

			#prepare postdata
			$postdata = [
				'account_id'	=> $this->user->account_id,
				'job_id'		=> $job_id,
				'search_term'	=> $search_term,
				'where'			=> $where,
				'order_by'		=> $order_by,
				'limit'			=> $limit,
				'offset'		=> $offset
			];

			$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/lookup', $postdata, ['auth_token'=>$this->auth_token], true );

			$job			= ( isset( $search_result->job ) ) ? $search_result->job : null;

			if( !empty( $job ) ){

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

				$return_data = $this->load_jobs_view( $job );
				if( !empty($pagination) ){
					$return_data .= '<tr><td colspan="8" style="padding: 0;">';
						$return_data .= $page_display.$pagination;
					$return_data .= '</td></tr>';
				}
			}else{
				$return_data .= '<tr><td colspan="8" style="padding: 0;"><br/>';
					$return_data .= ( isset($search_result->message) ) ? $search_result->message : 'No records found';
				$return_data .= '</td></tr>';
			}
		}

		print_r( $return_data );
		die();
	}

	/*
	* Prepare jobs views
	*/
	private function load_jobs_view( $jobs_data = false ){
		$return_data = '';
		if( !empty( $jobs_data ) ){
			foreach( $jobs_data as $k => $job_details ){
				$return_data .= '<tr>';
					$return_data .= '<td><a href="'.base_url('/webapp/job/profile/'.$job_details->job_id).'" >'.$job_details->job_id.'</a> &nbsp;&nbsp;<small title="This is an uploaded Job" class="" style="font-size:80%; display:'.( ( $job_details->is_uploaded == 1 ) ? 'inline-block' : 'none' ).'"><i class="far fa-arrow-alt-circle-up"></i></small></td>';
					$return_data .= '<td>'.( ( valid_date( $job_details->job_date ) ) ? date( 'd-m-Y', strtotime( $job_details->job_date ) ) : '' ).'</td>';
					$return_data .= '<td>'.( ( valid_date( $job_details->created_on ) ) ? date( 'd-m-Y H:i:s', strtotime( $job_details->created_on ) ) : '' ).'</td>';
					$return_data .= '<td>'.$job_details->job_type.'</td>';
					$return_data .= '<td>'.( ( !empty( $job_details->postcode ) ) ? $job_details->postcode : ( !empty( $job_details->address_postcode ) ? $job_details->address_postcode : ( !empty( $job_details->customer_postcode ) ? $job_details->customer_postcode : "" ) ) ).'</td>';
					#$return_data .= '<td>'.( !empty( $job_details->contract_name ) ? $job_details->contract_name : "" ).'</td>';
					$return_data .= '<td>'.$job_details->assignee.'</td>';
					$return_data .= '<td>'.$job_details->job_status.'</td>';
					$return_data .= '<td>'.$job_details->job_tracking_status.'</td>';
				$return_data .= '</tr>';
			}

			if( !empty($pagination) ){
				$return_data .= '<tr><td colspan="8" style="padding: 0;">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
		}else{
			$return_data .= '<tr><td colspan="8"><br/>'.$this->config->item("no_records").'</td></tr>';
		}
		return $return_data;
	}


	/** Update Job Details **/
	public function update_job( $job_id = false, $page = 'details' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$job_id = ( $this->input->post( 'job_id' ) ) ? $this->input->post( 'job_id' ) : ( !empty( $job_id ) ? $job_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$updates_job = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/update', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  = ( isset($updates_job->job) ) ? $updates_job->job : null;
			$message	  = ( isset($updates_job->message) ) ? $updates_job->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	/** Upload a Job document **/
	public function upload_docs( $job_id = false, $page = 'details' ){
		
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$job_id = ( $this->input->post( 'job_id' ) ) ? $this->input->post( 'job_id' ) : ( !empty( $job_id ) ? $job_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		}else{
			
			if( !empty( $job_id ) ){

				$postdata 	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
				$folder		= !empty( $postdata['doc_type'] ) ? $postdata['doc_type'] : 'others';
				$doc_upload	= $this->document_service->upload_files( $this->user->account_id, $postdata, $document_group = 'job', $folder );
				
				redirect('webapp/job/profile/'.$job_id.'/documents' );

			}else{
				redirect('webapp/job', 'refresh');
			}
			
		}
		redirect('webapp/job/', 'refresh');
	}


	/*
	* Load a ra record
	*/
	public function view_ra_record( $assessment_id = false ){

		$assessment_id 	= ( $this->input->post( 'assessment_id' ) ) ? $this->input->post( 'assessment_id' ) : ( !empty( $assessment_id ) ? $assessment_id : null );

		$return_data = [
			'status'=>0,
			'ra_record'=>null,
			'status_msg'=>'Invalid paramaters'
		];
		if( !empty( $assessment_id ) ){
			$ra_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'risk_assessment/ra_records', ['account_id'=>$this->user->account_id,'assessment_id'=>$assessment_id], ['auth_token'=>$this->auth_token], true );
			$result		= ( isset( $ra_result->ra_records ) ) ? $ra_result->ra_records : null;
			$message	= ( isset( $ra_result->message ) ) ? $ra_result->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$ra = $this->load_ra( $result );
				$return_data['status'] 	  = 1;
				$return_data['ra_record'] = $ra;
			}
			$return_data['status_msg'] = $message;
		}
		print_r( json_encode( $return_data ) );
		die();
	}

	private function load_ra( $ra_record = false ){
		$ra = '';
		if( !empty( $ra_record ) ){
			$ra .= '<table style="width:100%">';
					$ra .= '<tr><th width="30%">Assessment ID</th><td>'.$ra_record->assessment_id.'</td></tr>';
					$ra .= '<tr><th>Date Submitted</th><td>'.date('d-m-Y',strtotime( $ra_record->date_created )).'</td></tr>';
					$ra .= '<tr><th>Submitted by</th><td>'.$ra_record->created_by.'</td></tr>';
					$ra .= '<tr><th>Total Expected</th><td><i class="far  '.( ( $ra_record->risks_completed == 1 ) ? " fa-check-circle text-green " : " fa-times-circle text-red" ).' "></i></td></tr>';
					$ra .= '<tr><th>Total Completed</th><td><i class="far  '.( ( $ra_record->risks_completed == 1 ) ? " fa-check-circle text-green " : " fa-times-circle text-red" ).' "></i></td></tr>';
					$ra .= '<tr><th colspan="2">&nbsp;</th></tr>';
					$ra .= '<tr><th colspan="2"><hr><span style="font-weight:400">RESPONSES</span><hr></th></tr>';
					$ra .= '<tr><td colspan="2"><table style="width:100%;display:table">';
					$ra .= '<tr><th width="10%">ID</th><th width="75%">Risk Name / Is Risk available?</th><th width="15%">Response</th></tr>';
						if( !empty( $ra_record->ra_responses ) ){
							foreach( $ra_record->ra_responses as $k=>$ra_item ){ $k++;
								$ra .= '<tr><td>'.$k.'</td><td>'.$ra_item->risk_text.'</td><td>'.$ra_item->risk_response.'</td></tr>';
							}
						} else {
							$ra .= '<tr><td colspan="3">No responses submitted yet!</td></tr>';
						}
					$ra .= '</table></td></tr>';
			$ra .= '</table>';
		}
		return $ra;
	}

	/** Create new job **/
	public function create(){

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'details' );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		}else{
			$job_types		 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_types', ['account_id'=>$this->user->account_id, 'order_by'=>urlencode('job_type'), 'limit'=>-1], ['auth_token'=>$this->auth_token], true );
			$data['job_types'] 		= ( isset( $job_types->job_types ) ) ? $job_types->job_types : null;
			$data['job_durations']	= job_durations();
			$this->_render_webpage( 'job/job_create_new', $data );
		}
	}


	/** Do Job creation **/
	public function create_job(){

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$return_data = [
			'status'=>0
		];

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'details' );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$new_job	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/create', $postdata, ['auth_token'=>$this->auth_token] );

			$result		  = ( isset( $new_job->job ) ) ? $new_job->job : null;
			$message	  = ( isset( $new_job->message ) ) ? $new_job->message : 'Something went wrong';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
				$return_data['job']   = $new_job;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	/**
	* Delete job (set as archived )
	**/
	public function delete_job( $job_id = false ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$job_id = ( $this->input->post( 'job_id' ) ) ? $this->input->post( 'job_id' ) : ( !empty( $job_id ) ? $job_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_delete ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$delete_job = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/delete', $postdata, ['auth_token'=>$this->auth_token], true );
			$result		  = ( isset($delete_job->status) ) ? $delete_job->status : null;
			$message	  = ( isset($delete_job->message) ) ? $delete_job->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status']= 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	/** Add Consumed Stock & BOMs to a Job **/
	public function add_job_consumed_items(){

		$section 	 = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
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

			if( $postdata['add_type'] == 'required_items' ){
				$ref_key = 'required_items';
			} else if( $postdata['add_type'] == 'consumed_items' ){
				$ref_key = 'consumed_items';
			} else {
				$ref_key = 'consumed_items';
			}

			$consumed_items  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/add_'.$ref_key , $postdata, ['auth_token'=>$this->auth_token] );

			$result		  	 = ( isset( $consumed_items->{$ref_key} ) ) ? $consumed_items->{$ref_key} : null;
			$message	  	 = ( isset( $consumed_items->message ) )  	? $consumed_items->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 	 = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	/** Update Required item (Qty) **/
	public function update_consumed_items(){

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$return_data = [
			'status'=>0
		];

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'details' );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$updated_item = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/update_consumed_items', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  = ( isset( $updated_item->consumed_items ) ) 	? $updated_item->consumed_items : null;
			$message	  = ( isset( $updated_item->message ) ) 		? $updated_item->message : 'Something went wrong';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	
	/**
	* Remove Consumed Item from Job
	**/
	public function delete_consumed_item( $question_id = false ){
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
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$drop_item 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/delete_consumed_items', $postdata, ['auth_token'=>$this->auth_token] );
			$result		= ( isset( $drop_item->status ) ) ? $drop_item->status : null;
			$message	= ( isset( $drop_item->message ) ) ? $drop_item->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	//Manage Job types
	function job_types( $job_type_id = false, $page = 'details' ){

		$toggled		= ( !empty( $this->input->get( 'toggled' ) ) ? $this->input->get( 'toggled' ) : false );
		$section 		= ( !empty( $page) ) ? $page : ( !empty( $this->input->get( 'page' ) ) ? $this->input->get( 'page' ) : 'details' );
		$job_type_id  = ( !empty( $job_type_id) ) ? $job_type_id : ( !empty( $this->input->get( 'job_type_id' ) ) ? $this->input->get( 'job_type_id' ) : ( ( !empty( $this->input->get( 'id' ) ) ? $this->input->get( 'id' ) : null ) ) );

		if( !empty( $job_type_id ) ){
			$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
			if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
				$this->_render_webpage( 'errors/access-denied', false );
			}else{

				$default_params 	= $params =[ 'account_id'=>$this->user->account_id, 'where'=>[ 'job_type_id'=>$job_type_id ] ];
				
				$local_records_only = false;
				if( in_array( $this->account_id, $this->tess_linked_accounts ) ) {
					$local_records_only	= 1;
					$params['where']['local_records_only'] = $local_records_only;
				}
				
				$job_type_details 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_types', $params, [ 'auth_token'=>$this->auth_token ], true );

				if( !empty( $job_type_details->job_types ) ){

					$data['job_type_details']  = $job_type_details->job_types;
					$risk_items 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'risk_assessment/risks', ['account_id'=>$this->user->account_id, 'where'=>['ajax_req'=>1], 'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
					$risk_items				= ( isset( $risk_items->risks ) ) ? $risk_items->risks : null;
					$data['risk_items'] 	= ( !empty( $risk_items ) ) ? json_encode( $risk_items ) : false;
					$data['available_risks']= $risk_items;
					$data['linked_risks']	= ( !empty( $data['job_type_details']->associated_risks ) ) ? array_column( $data['job_type_details']->associated_risks, 'risk_id' ) : [];

					$evidoc_types	 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/evidoc_types', [ 'account_id'=>$this->user->account_id, 'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
					$data['available_evidocs']	= ( isset( $evidoc_types->evidoc_types ) ) ? $evidoc_types->evidoc_types : null;

					$skills					 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'diary/skills', [ 'account_id'=>$this->user->account_id, 'limit'=>-1 ], [ 'auth_token'=>$this->auth_token ], true );
					$data['available_skills']	= ( isset( $skills->skills ) ) ? $skills->skills : null;
					$data['linked_skills']		= ( !empty( $data['job_type_details']->required_skills ) ) ? array_column( $data['job_type_details']->required_skills, 'skill_id' ) : [];

					$bom_items 				= $this->webapp_service->api_dispatcher( $this->api_end_point.'stock/bom_items', ['account_id'=>$this->user->account_id, 'where'=>['ajax_req'=>1], 'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
					$bom_items				= ( isset( $bom_items->bom_items ) ) ? $bom_items->bom_items : null;

					$data['bom_items'] 		= ( !empty( $bom_items ) ) ? json_encode( $bom_items ) : false;
					$data['available_boms'] = $bom_items;

					$data['linked_boms']	= ( !empty( $data['job_type_details']->required_boms ) ) ? array_column( $data['job_type_details']->required_boms, 'item_id' ) : [];

					$available_contracts	  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'contract/contracts', ['account_id'=>$this->user->account_id, 'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
					$data['available_contracts']= ( isset( $available_contracts->contract) ) ? $available_contracts->contract : null;

					$data['job_durations']		= job_durations();
					
					$bom_categories 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'stock/bom_categories', ['account_id'=>$this->user->account_id, 'limit'=>-1 ], [ 'auth_token'=>$this->auth_token ], true );
					$data['bom_categories']		= ( isset( $bom_categories->bom_categories ) ) ? $bom_categories->bom_categories : null;
					
					$data['external_job_types']	= array_to_object( [ [ 'external_job_type_code'=>'AC1', 'external_job_type'=>'ASL - Asset Collection 1' ] ] );

					$available_checklists 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'tesseract/checklists', ['account_id'=>$this->user->account_id, 'where'=>[ 'local_records_only' => $local_records_only ], 'limit'=>-1 ], [ 'auth_token'=>$this->auth_token ], true );
					$data['available_checklists']	= ( isset( $available_checklists->checklists ) ) ? $available_checklists->checklists : null;
					$data['linked_checklists']		= ( !empty( $data['job_type_details']->required_checklists ) ) ? array_column( $data['job_type_details']->required_checklists, 'checklist_id' ) : [];

					$disciplines	 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'discipline/account_disciplines', [ 'account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
					$data['disciplines']		= ( isset( $disciplines->account_disciplines ) ) ? $disciplines->account_disciplines : null;

					$this->_render_webpage( 'job/job_type_profile', $data );
				}else{
					redirect( 'webapp/job/job_types', 'refresh' );
				}
			}
		} else {
			$this->_render_webpage( 'job/job_types', false, false, true );
		}
	}

	/*
	* Job Type List / Search
	*/
	public function job_types_list( $page = 'details' ){

		$return_data = '';

		$section 	 = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){

			$return_data .= $this->config->item( 'ajax_access_denied' );

		}else{

			# Setup search parameters
			$search_term   = ( $this->input->post( 'search_term' ) ) ? $this->input->post( 'search_term' ) : false;
			$where   	   = ( $this->input->post( 'where' ) ) ? $this->input->post( 'where' ) : false;
			$limit		   = ( !empty( $where['limit'] ) )  ? $where['limit']  : DEFAULT_LIMIT;
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

			$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_types', $postdata, [ 'auth_token'=>$this->auth_token ], true );

			$job_types		= ( isset( $search_result->job_types ) ) ? $search_result->job_types : null;

			if( !empty( $job_types ) ){

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

				$return_data = $this->load_job_types_view( $job_types );
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
	* Prepare Job Types views
	*/
	private function load_job_types_view( $job_types_data ){
		$return_data = '';
		if( !empty( $job_types_data ) ){

			foreach( $job_types_data as $k => $job_type_details ){

				$return_data .= '<tr>';
					$return_data .= '<td><a href="'.base_url( '/webapp/job/job_types/'.$job_type_details->job_type_id ).'" >'. ucwords( $job_type_details->job_type ) .'</a></td>';
					$return_data .= '<td>'.$job_type_details->job_type_desc.'</td>';
					$return_data .= '<td>'.( !empty( $job_type_details->date_created ) ? date( 'd-m-Y H:i:s', strtotime( $job_type_details->date_created ) ) : '' ).'</td>';
					$return_data .= '<td><span class="pull-right">'.( !empty( $job_type_details->is_active ) ? 'Active' : 'Disabled' ).'</span></td>';
				$return_data .= '</tr>';
			}

			if( !empty( $pagination ) ){
				$return_data .= '<tr><td colspan="4" style="padding: 0;">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
		}else{
			$return_data .= '<tr><td colspan="4"><br/>'.$this->config->item( "no_records" ).'</td></tr>';
		}
		return $return_data;
	}

	/*
	* Create New Job Types
	*/
	public function new_job_type( $page = 'details' ){

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		}else{
			$evidoc_groups	 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/evidoc_groups', ['account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
			$data['evidoc_groups']	= ( isset( $evidoc_groups->evidoc_groups ) ) ? $evidoc_groups->evidoc_groups : null;

			$evidoc_frequencies	 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/evidoc_frequencies', ['account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
			$data['evidoc_frequencies'] = ( isset( $evidoc_frequencies->evidoc_frequencies ) ) ? $evidoc_frequencies->evidoc_frequencies : null;

			$audit_categories	 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/audit_categories', [ 'account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
			$data['evidoc_categories']	= ( isset( $audit_categories->audit_categories ) ) ? $audit_categories->audit_categories : null;

			$asset_types	 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/asset_types', [ 'account_id'=>$this->user->account_id, 'grouped'=>1 ], ['auth_token'=>$this->auth_token], true );
			$data['asset_types']		= ( isset( $asset_types->asset_types ) ) ? $asset_types->asset_types : null;

			$evidoc_types	 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/evidoc_types', [ 'account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
			$data['available_evidocs']	= ( isset( $evidoc_types->evidoc_types ) ) ? $evidoc_types->evidoc_types : null;

			$risks						= $this->webapp_service->api_dispatcher( $this->api_end_point.'risk_assessment/risks', [ 'account_id'=>$this->user->account_id, 'limit' => -1 ], [ 'auth_token'=>$this->auth_token ], true );
			$data['available_risks']	= ( isset( $risks->risks ) ) ? $risks->risks : null;

			$skills						= $this->webapp_service->api_dispatcher( $this->api_end_point.'diary/skills', [ 'account_id'=>$this->user->account_id ], [ 'auth_token'=>$this->auth_token ], true );
			$data['available_skills']= ( isset( $skills->skills ) ) ? $skills->skills : null;

			$available_contracts	  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'contract/contracts', ['account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
			$data['available_contracts']= ( isset( $available_contracts->contract) ) ? $available_contracts->contract : null;

			$bom_items 				= $this->webapp_service->api_dispatcher( $this->api_end_point.'stock/bom_items', ['account_id'=>$this->user->account_id, 'where'=>['ajax_req'=>1], 'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
			$bom_items				= ( isset( $bom_items->bom_items ) ) ? $bom_items->bom_items : null;

			$data['bom_items'] 		= ( !empty( $bom_items ) ) ? json_encode( $bom_items ) : false;
			$data['available_boms'] = $bom_items;

			$data['linked_boms']	= ( !empty( $data['job_type_details']->required_boms ) ) ? array_column( $data['job_type_details']->required_boms, 'item_id' ) : [];

			$data['defined_slas']		= defined_slas();
			$data['job_type_sub_types']	= job_type_sub_types();
			$data['job_durations']		= job_durations();

			$bom_categories 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'stock/bom_categories', ['account_id'=>$this->user->account_id, 'limit'=>-1 ], [ 'auth_token'=>$this->auth_token ], true );
			$data['bom_categories']		= ( isset( $bom_categories->bom_categories ) ) ? $bom_categories->bom_categories : null;
			
			$available_checklists 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'tesseract/checklists', ['account_id'=>$this->user->account_id, 'limit'=>-1 ], [ 'auth_token'=>$this->auth_token ], true );
			$data['available_checklists']	= ( isset( $available_checklists->checklists ) ) ? $available_checklists->checklists : null;

			$disciplines	 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'discipline/account_disciplines', [ 'account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
			$data['disciplines']		= ( isset( $disciplines->account_disciplines ) ) ? $disciplines->account_disciplines : null;

			$this->_render_webpage( 'job/job_type_add_new', $data );
		}
	}

	/**
	* Create new Job type
	*/
	public function create_job_type(){

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
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 	  	 = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$new_job_type = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/create_job_type', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  	 = ( isset( $new_job_type->job_type ) ) ? $new_job_type->job_type : null;
			$message	  	 = ( isset( $new_job_type->message ) ) ? $new_job_type->message : 'Oops! There was an error processing your request.';
			$exists	  	 	 = ( !empty( $new_job_type->exists ) ) ? $new_job_type->exists : false;

			if( !empty( $result ) ){
				$return_data['status'] 			= 1;
				$return_data['job_type'] 		= $result;
				$return_data['already_exists']  = $exists;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	/** Update Job Type Details **/
	public function update_job_type( $job_type_id = false, $page = 'details' ){
		$color_class  = 'red';
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$audit_id = ( $this->input->post( 'job_type_id' ) ) ? $this->input->post( 'job_type_id' ) : ( !empty( $job_type_id ) ? $job_type_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$job_type_id		 = ( !empty( $this->input->post( 'job_type_id' ) ) ) ? $this->input->post( 'job_type_id' ) : $job_type_id;
			$postdata 	  		 = array_merge( ['account_id'=>$this->user->account_id, 'job_type_id'=>$job_type_id ], $this->input->post() );
			$update_job_type 	 = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/update_job_type', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  		 = ( isset( $update_job_type->job_type ) ) ? $update_job_type->job_type : null;
			$message	  		 = ( isset( $update_job_type->message ) ) ? $update_job_type->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status']	= 1;
				$return_data['job_type']= $result;
				$color_class			= 'green';
			}
			$return_data['status_msg'] = '<span class="text-'.$color_class.'">'.$message.'</span>';
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/** Add Required Stock to a Job **/
	public function add_associated_risks(){

		$section 	 = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
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
			$associated_risks= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/add_associated_risks' , $postdata, ['auth_token'=>$this->auth_token] );
			$result		  	 = ( isset( $associated_risks->associated_risks ) ) ? $associated_risks->associated_risks : null;
			$message	  	 = ( isset( $associated_risks->message ) )  ? $associated_risks->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 	 = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/**
	* Remove Associated Risk from Job
	**/
	public function remove_associated_risks( $question_id = false ){
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
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$drop_risk 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/remove_associated_risks', $postdata, ['auth_token'=>$this->auth_token] );
			$result		= ( isset( $drop_risk->status ) ) ? $drop_risk->status : null;
			$message	= ( isset( $drop_risk->message ) ) ? $drop_risk->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	//Manage Risks - Overview page
	function risks( $risk_id = false, $page = 'details' ){

		$toggled	= ( !empty( $this->input->get( 'toggled' ) ) ? $this->input->get( 'toggled' ) : false );
		$section 	= ( !empty( $page) ) ? $page : ( !empty( $this->input->get( 'page' ) ) ? $this->input->get( 'page' ) : 'details' );
		$risk_id  	= ( !empty( $risk_id) ) ? $risk_id : ( !empty( $this->input->get( 'risk_id' ) ) ? $this->input->get( 'risk_id' ) : ( ( !empty( $this->input->get( 'risk_id' ) ) ? $this->input->get( 'risk_id' ) : null ) ) );

		if( !empty( $risk_id ) ){
			$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
			if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
				$this->_render_webpage( 'errors/access-denied', false );
			}else{
				$default_params = $params =[ 'account_id'=>$this->user->account_id, 'where'=>[ 'risk_id'=>$risk_id ] ];
				$risk_details = $this->webapp_service->api_dispatcher( $this->api_end_point.'risk_assessment/risks', $params, [ 'auth_token'=>$this->auth_token ], true );
				if( !empty( $risk_details->risks ) ){
					$data['risk_details']  		 	= $risk_details->risks;
					$associated_job_types  		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'risk_assessment/associated_job_types', ['account_id'=>$this->user->account_id, 'risk_id'=>$risk_id ], ['auth_token'=>$this->auth_token], true );
					$data['associated_job_types']	= ( isset( $associated_job_types->associated_job_types ) ) ? $associated_job_types->associated_job_types : null;
					$this->_render_webpage( 'job/risks/risk_details_profile', $data );
				}else{
					redirect('webapp/job/risks', 'refresh');
				}
			}
		} else {
			$this->_render_webpage('job/risks/manage_risks', false, false, true );
		}
	}

	/*
	* Risks Bank List / Search
	*/
	public function risks_bank( $page = 'details' ){

		$return_data = '';

		$section 	 = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){

			$return_data .= $this->config->item( 'ajax_access_denied' );

		}else{

			# Setup search parameters
			$search_term   = ( $this->input->post( 'search_term' ) ) ? $this->input->post( 'search_term' ) : false;
			$where   	   = ( $this->input->post( 'where' ) ) ? $this->input->post( 'where' ) : false;
			$limit		   = ( !empty( $where['limit'] ) )  ? $where['limit']  : DEFAULT_LIMIT;
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

			$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'risk_assessment/risks', $postdata, [ 'auth_token'=>$this->auth_token ], true );

			$risks		= ( isset( $search_result->risks ) ) ? $search_result->risks : null;

			if( !empty( $risks ) ){

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

				$return_data = $this->load_risks_view( $risks );
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
	* Risk bank views
	*/
	private function load_risks_view( $risks_data ){
		$return_data = '';
		if( !empty( $risks_data ) ){

			foreach( $risks_data as $k => $risk ){

				$return_data .= '<tr>';
					$return_data .= '<td>'.$risk->risk_text.'</td>';
					$return_data .= '<td>'.$risk->risk_harm.'</td>';
					$return_data .= '<td>'.$risk->risk_rating.'</td>';
					$return_data .= '<td>'.$risk->residual_risk.'</td>';
					#$return_data .= '<td>'.( !empty( $risk->is_active ) ? 'Active' : 'Disabled' ).'</td>';
					$return_data .= '<td><span class="pull-right"><a href="'.base_url( '/webapp/job/risks/'.$risk->risk_id ).'" ><i class="far fa-edit"></i> Open</a></span></td>';
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

	/** Prepare Stock / BOM data **/
	private function download_consumed_items( $job_id = false, $job_data = false ){
		if( !empty( $job_id ) && !empty( $job_data ) ){
			$setup['document_setup'] = [
				'recipient_details'	=>[
					'show_recipient'	=> false,
					'recipient_name' 	=> 'James Bond Esq.',
					'address_line1'		=> 'The Reef',
					'address_line2'		=> 'C/O Initiative Property Management',
					'address_line3'		=> 'Suite 4, Lansdowne Place',
					'address_town'		=> 'Bournemouth',
					'address_country'	=> 'Surrey',
					'address_postcode'	=> 'BH8 8EW',
				],
				'document_content'	=> $job_data ,
				'generic_details'	=>[
					'document_name'		=> 'JOB CONSUMED STOCK / BOM / SORS SUMMARY',
					'audit_frequency'	=> null,
					'document_date'		=> date('l, jS F Y'),
					'referrence_number'	=> ''
				],
				'data_details'		=> null,
			];
			$this->ssid_common->create_pdf( 'pdf-templates/custom/job_stock_boms.php', $setup, true  );

		}
	}

	/**
	* Add Required Skills to a Job Type
	**/
	public function add_required_skills(){

		$section 	 = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
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
			$required_skills = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/add_required_skills' , $postdata, ['auth_token'=>$this->auth_token] );
			$result		  	 = ( isset( $required_skills->required_skills ) ) 	? $required_skills->required_skills : null;
			$message	  	 = ( isset( $required_skills->message ) )  			? $required_skills->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 	 = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/**
	* Remove Required Skills from Job type
	**/
	public function remove_required_skill( $skill_id = false ){
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
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$unlink_skill 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/remove_required_skill', $postdata, ['auth_token'=>$this->auth_token] );
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

	/** Check the Required skills and  **/
	public function check_required_skills(){
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
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$required_skills= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/required_skills', $postdata, ['auth_token'=>$this->auth_token], true );
			$result			= ( isset( $required_skills->required_skills ) )  	? $required_skills->required_skills : null;
			$message		= ( isset( $required_skills->message ) ) 			? $required_skills->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 		= 1;
				$return_data['skills_data'] = $this->load_skilled_people_view( $result );
			} else {
				$return_data['status_msg'] = 'There\'s currently no Skilled operatives to do this type of Job!';
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	//Load Skilled people view
	private function load_skilled_people_view( $skilled_people_data = false ){
		$return_data = '';
		if( !empty( $skilled_people_data ) ){
			$return_data .= '<option value="" >Please select operative</option>';
			foreach( $skilled_people_data as $k => $skills_data ){
				$skills = '';
				foreach( $skills_data->personal_skills as $key => $skill ){
					$skills .= ( $key != 0 ) ? ', '.$skill->skill_name : $skill->skill_name;
				}
				$return_data .= '<option value="'.$skills_data->person_id.'" >'.$skills_data->person.' - '.$skills.'</option>';
			}
		}else{
			$return_data .= '<option disabled>'.$this->config->item( 'no_records' ).'</option>';
		}
		return $return_data;
	}

	//Manage Schedule frequencies
	function schedule_frequencies( $frequency_id = false, $page = 'details' ){

		$toggled		= ( !empty( $this->input->get( 'toggled' ) ) ? $this->input->get( 'toggled' ) : false );
		$section 		= ( !empty( $page) ) ? $page : ( !empty( $this->input->get( 'page' ) ) ? $this->input->get( 'page' ) : 'details' );
		$frequency_id  	= ( !empty( $frequency_id) ) ? $frequency_id : ( !empty( $this->input->get( 'frequency_id' ) ) ? $this->input->get( 'frequency_id' ) : ( ( !empty( $this->input->get( 'id' ) ) ? $this->input->get( 'id' ) : null ) ) );

		if( !empty( $frequency_id ) ){
			$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
			if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
				$this->_render_webpage( 'errors/access-denied', false );
			}else{

				$default_params = $params =[ 'account_id'=>$this->user->account_id, 'where'=>[ 'frequency_id'=>$frequency_id ] ];
				$frequency_details = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/schedule_frequencies', $params, [ 'auth_token'=>$this->auth_token ], true );

				if( !empty( $frequency_details->schedule_frequencies ) ){

					$data['frequency_details']  = $frequency_details->schedule_frequencies;

					$this->_render_webpage( 'job/schedules/schedule_frequency_profile', $data );
				}else{
					redirect('webapp/job/schedules/schedule_frequencies', 'refresh');
				}
			}
		} else {
			$this->_render_webpage('job/schedules/schedule_frequencies', false, false, true );
		}
	}

	/*
	* Job Type List / Search
	*/
	public function schedule_frequencies_list( $page = 'schedules' ){

		$return_data = '';

		$section 	 = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){

			$return_data .= $this->config->item( 'ajax_access_denied' );

		}else{

			# Setup search parameters
			$search_term   = ( $this->input->post( 'search_term' ) ) ? $this->input->post( 'search_term' ) : false;
			$where   	   = ( $this->input->post( 'where' ) ) ? $this->input->post( 'where' ) : false;
			$limit		   = ( !empty( $where['limit'] ) )  ? $where['limit']  : DEFAULT_LIMIT;
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

			$search_result			= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/schedule_frequencies', $postdata, [ 'auth_token'=>$this->auth_token ], true );

			$schedule_frequencies	= ( isset( $search_result->schedule_frequencies ) ) ? $search_result->schedule_frequencies : null;

			if( !empty( $schedule_frequencies ) ){

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

				$return_data = $this->load_schedule_frequencies_view( $schedule_frequencies );
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
	* Prepare Schedule Frequencies views
	*/
	private function load_schedule_frequencies_view( $schedule_frequencies_data ){
		$return_data = '';
		if( !empty( $schedule_frequencies_data ) ){

			foreach( $schedule_frequencies_data as $k => $frequency_details ){

				$return_data .= '<tr>';
					$return_data .= '<td>'. ucwords( $frequency_details->frequency_name ) .'</td>';
					$return_data .= '<td>'.$frequency_details->frequency_desc.'</td>';
					$return_data .= '<td>'.( !empty( $frequency_details->date_created ) ? date( 'd-m-Y H:i:s', strtotime( $frequency_details->date_created ) ) : '' ).'</td>';
					$return_data .= '<td>'.( !empty( $frequency_details->is_active ) ? 'Active' : 'Disabled' ).'</td>';
					$return_data .= '<td><a href="'.base_url( '/webapp/job/schedule_frequencies/'.$frequency_details->frequency_id ).'" ><i class="far fa-edit"></i> Open</a></td>';
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

	/*
	* Add New Schedule Frequency wizard
	*/
	public function new_schedule_frequency( $page = 'details' ){

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		}else{
			$evidoc_frequencies	 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/evidoc_frequencies', ['account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
			$data['frequency_groups'] 	= ( isset( $evidoc_frequencies->evidoc_frequencies ) ) ? $evidoc_frequencies->evidoc_frequencies : null;
			$this->_render_webpage( 'job/schedules/new_schedule_frequency', $data );
		}
	}

	/**
	* Create new Schedule Frequency
	*/
	public function create_schedule_frequency(){

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
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 	  	 = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$new_schedule_frequency = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/create_schedule_frequency', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  	 = ( isset( $new_schedule_frequency->schedule_frequency ) ) ? $new_schedule_frequency->schedule_frequency : null;
			$message	  	 = ( isset( $new_schedule_frequency->message ) ) ? $new_schedule_frequency->message : 'Oops! There was an error processing your request.';
			$exists	  	 	 = ( !empty( $new_schedule_frequency->exists ) ) ? $new_schedule_frequency->exists : false;

			if( !empty( $result ) ){
				$return_data['status'] 				= 1;
				$return_data['schedule_frequency']	= $result;
				$return_data['already_exists']  	= $exists;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}








	/** Manage Schedules **/
	public function new_schedule( $contract_id = false, $site_id = false, $location_id = false, $asset_id = false, $frequency_id = false ){
		$contract_id	= ( !empty( $this->input->get( 'contract_id' ) ) 	? $this->input->get( 'contract_id' ) 	: $contract_id );
		$site_id		= ( !empty( $this->input->get( 'site_id' ) ) 		? $this->input->get( 'site_id' ) 		: $site_id );
		$location_id	= ( !empty( $this->input->get( 'location_id' ) ) 	? $this->input->get( 'location_id' ) 	: $location_id );
		$asset_id		= ( !empty( $this->input->get( 'asset_id' ) ) 		? $this->input->get( 'asset_id' ) 		: $asset_id );
		$frequency_id	= ( !empty( $this->input->get( 'frequency_id' ) ) 	? $this->input->get( 'frequency_id' ) 	: $frequency_id );
		$params 		= $params = [ 'account_id'=>$this->user->account_id ];

		$schedule_frequencies 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/schedule_frequencies', $params, [ 'auth_token'=>$this->auth_token ], true );
		$data['schedule_frequencies'] 	= isset( $schedule_frequencies->schedule_frequencies ) ? $schedule_frequencies->schedule_frequencies : false;

		if( !empty( $contract_id ) ){
			
			## Get contract details
			$params['contract_id']   	= $contract_id;
			$contract_details		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'contract/contracts', $params, ['auth_token'=>$this->auth_token], true );
			$data['contract_details']	= ( isset( $contract_details->contract[0] ) ) ? $contract_details->contract[0] : null;
			$data['audit_group']	 	= 'contract';
			$data['frequency_id'] 	 	= $frequency_id;
			
			$audit_categories	 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/audit_categories', [ 'account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
			$data['evidoc_categories']	= ( isset( $audit_categories->audit_categories ) ) ? $audit_categories->audit_categories : null;

			if( !empty( $site_id ) ){
				$contract_buildings	 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/sites', [ 'account_id'=>$this->user->account_id, 'site_id'=>$site_id ], ['auth_token'=>$this->auth_token], true );
				$data['selected_site_id']	= $site_id;
				$data['contract_buildings']	= ( isset( $contract_buildings->sites ) ) ? [ $contract_buildings->sites ] : null;
			}  else {
				$data['contract_buildings'] = false;
			}

			$params['contract_id']  = $contract_id;
			$params['where']   		= [
				'include_schedules_info' => 1
			];
			
		} else if( !empty( $site_id ) ){
			## Get site details
			$params['site_id']   	 = $site_id;
			$site_details		 	 = $this->webapp_service->api_dispatcher( $this->api_end_point.'site/sites', $params, ['auth_token'=>$this->auth_token], true );
			$data['site_details']	 = ( isset( $site_details->sites ) ) ? $site_details->sites : null;

			$asset_params			 = array_merge( $params, [ 'where'=>[ 'grouped'=>1, 'site_id'=>$site_id ] ] );
			#$site_assets		 	 = $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/linked_assets', $asset_params, ['auth_token'=>$this->auth_token], true );
			#$data['building_assets'] = ( isset( $site_assets->linked_assets ) ) ? $site_assets->linked_assets : null;
			$data['building_assets'] = false;

			$site_asset_types	 	 	 = $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/asset_types_by_category', $asset_params, ['auth_token'=>$this->auth_token], true );
			$data['building_categories'] = ( isset( $site_asset_types->asset_types ) ) ? $site_asset_types->asset_types : null;

			$data['audit_group']	 = 'site';
		} else if( !empty( $location_id ) ){
			## Get location details
			$params['location_id'] 	 = $location_id;
			$site_details		 	 = $this->webapp_service->api_dispatcher( $this->api_end_point.'site/site_locations', $params, ['auth_token'=>$this->auth_token], true );
			$data['location_details']= ( isset( $site_details->locations ) ) ? $site_details->locations : null;
			$data['audit_group']	 = 'site';
		} else if( !empty( $asset_id ) ){
			## Get asset details
			$params['asset_id']  	 = $asset_id;
			$asset_details		 	 = $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/assets', $params, ['auth_token'=>$this->auth_token], true );
			$data['asset_details']	 = ( isset( $asset_details->assets ) ) ? $asset_details->assets : null;
			$data['audit_group']	 = 'asset';
		} else {
			//Get list details
		}

		$this->_render_webpage( 'job/schedules/index', $data );
	}

	/** Get all Evidocs required for a Schedule **/
	public function fetch_evidocs( $page = 'schedules' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );

			$frequency_id 	= !empty( $postdata['where']['frequency_id'] ) ? $postdata['where']['frequency_id'] : '';
			$frequency_name = !empty( $postdata['where']['frequency_name'] ) ? urldecode( $postdata['where']['frequency_name'] ) : 'this Inspection';
			unset( $postdata['where']['frequency_name'] );

			$evidoc_types	= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/evidoc_types', $postdata, ['auth_token'=>$this->auth_token], true );
			$result			= ( isset( $evidoc_types->evidoc_types ) )  	? $evidoc_types->evidoc_types : null;
			$message		= ( isset( $evidoc_types->message ) ) 			? $evidoc_types->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 		 = 1;
				$return_data['evidocs_data'] = $this->load_evidoc_types_view( $result );
			} else {
				$return_data['status_msg'] = '<strong>No data found due to one or all of the reasons below:-</strong><br/><br/>';
				$return_data['status_msg'] .= '- No EviDocs matching the selected Frequency! <a href="'.base_url( 'webapp/audit/evidoc_names' ).'" target="_blank" title="Click here to view existing EviDocs or create a new one!" >Manage</a><br/><br/>';
				$return_data['status_msg'] .= '- The existing EviDocs linked to <a href="'.base_url( 'webapp/audit/evidoc_names?frequency_id='.$frequency_id ).'" target="_blank" title="Click here to go to the setup page!" >'.$frequency_name.'</a> do not have any Questions yet!<br/><br/>';
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	//Load Schedule Evidocs
	private function load_evidoc_types_view( $evidoc_types = false ){
		$return_data = '';
		if( !empty( $evidoc_types ) ){
			$return_data .= '<option value="" >Please search / select EviDoc</option>';
			foreach( $evidoc_types as $k => $evidoc_type ){
				$return_data .= '<option value="'.$evidoc_type->audit_type_id.'" >'.$evidoc_type->audit_type.'</option>';
			}
		}else{
			$return_data .= '<option disabled>'.$this->config->item( 'no_records' ).'</option>';
		}
		return $return_data;
	}


	/** Get all Job Types required for a Schedule attached to an EviDoc **/
	public function fetch_job_types( $page = 'schedules' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$job_types		= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_types', $postdata, ['auth_token'=>$this->auth_token], true );
			$result			= ( isset( $job_types->job_types ) )  	? $job_types->job_types : null;
			$message		= ( isset( $job_types->message ) ) 		? $job_types->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 		 	= 1;
				$return_data['job_types_data'] 	= $this->load_select_job_types_view( $result );
			} else {
				$return_data['status_msg'] = 'There\'s currently no Job Types matching the selected EviDoc!';
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	//Load Job Types in Select view
	private function load_select_job_types_view( $job_types = false ){
		$return_data = '';
		if( !empty( $job_types ) ){
			$return_data .= '<option value="" >Please search / select Job Type</option>';
			foreach( $job_types as $k => $job_type ){
				$return_data .= '<option value="'.$job_type->job_type_id.'" data-job_type="'.$job_type->job_type.'" >'.$job_type->job_type.'</option>';
			}
		}else{
			$return_data .= '<option disabled>'.$this->config->item( 'no_records' ).'</option>';
		}
		return $return_data;
	}

	/** Create schedules **/
	public function create_schedules(){

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$return_data = [
			'status'=>0
		];

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'details' );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$schedules	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/create_schedules', $postdata, ['auth_token'=>$this->auth_token] );

			$result		  = ( isset( $schedules->schedules ) ) 	? $schedules->schedules : null;
			$message	  = ( isset( $schedules->message ) ) 	? $schedules->message : 'Oops! There was an error processing your request.';

			if( !empty( $result ) ){
				$return_data['status'] 		= 1;
				$return_data['schedules']   = $result;
			}
			$return_data['status_msg'] 		= $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	/** View a Schedule profile **/
	public function schedule_profile( $schedule_id = false, $page = 'schedules' ){

		if( !$schedule_id ){
			redirect( 'webapp/job/jobs', 'refresh' );
		}

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		# Check item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 					= ['account_id'=>$this->user->account_id, 'where'=>['schedule_id'=>$schedule_id] ];
			$schedule_record			= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/schedules', $postdata, ['auth_token'=>$this->auth_token], true );

			$data['schedule_details']	= ( isset( $schedule_record->schedules ) ) ? $schedule_record->schedules : null;
			
			
			
			if( !empty( $data['schedule_details'] ) ){
				#$schedule_jobs 		  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/jobs', ['account_id'=>$this->user->account_id,'where'=>['schedule_id' => $schedule_id ] ], ['auth_token'=>$this->auth_token], true );
				#$data['schedule_jobs']	= ( isset( $schedule_jobs->jobs ) ) 		? $schedule_jobs->jobs : null;

				$schedule_activities 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/schedule_activities', ['account_id'=>$this->user->account_id,'where'=>['schedule_id' => $schedule_id ], 'limit'=> -1 ], ['auth_token'=>$this->auth_token], true );
				$data['schedule_activities']= ( isset( $schedule_activities->schedule_activities ) ) 		? $schedule_activities->schedule_activities : null;

				$job_types		 	  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_types', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
				$data['job_types'] 	  	= ( isset( $job_types->job_types ) ) 		? $job_types->job_types : null;

				$job_statuses		  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_statuses', ['account_id'=>false], ['auth_token'=>$this->auth_token], true );
				$data['job_statuses'] 	= ( isset( $job_statuses->job_statuses ) ) ? $job_statuses->job_statuses : null;

				$ops_params			  = [
					'account_id'=>$this->user->account_id, 
					'where'		=>['include_admins'=>1], 
					'limit'		=>-1
				];

				## Apply Primary User conditions
				if( $this->user->is_primary_user && !$this->user->is_admin ){
					$ops_params['where']['associated_user_id'] 	= $this->user->id;
				}
				
				$operatives		  	  		= $this->webapp_service->api_dispatcher( $this->api_end_point.'user/field_operatives', $ops_params, ['auth_token'=>$this->auth_token], true );
				$data['operatives']   		= ( isset( $operatives->field_operatives ) ) ? $operatives->field_operatives : null;

				$data['job_durations']		= job_durations();

				$this->_render_webpage('job/schedules/schedule_details_profile', $data);
			} else {
				redirect( 'webapp/job/jobs', 'refresh' );
			}
		}
	}

	/*
	* Get list of All created schedules
	*/
	//Overall List of Schedules. This has currently been agreed as not required as user should access this via assets/sites/contract/locations etc.
	function schedules( $frequency_id = false, $page = 'details' ){
		$data['rows'] = '';
		$this->_render_webpage('job/schedules/schedules', $data);
	}

	public function schedules_list( $page = 'schedules' ){

		$return_data = '';

		$section 	 = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){

			$return_data .= $this->config->item( 'ajax_access_denied' );

		}else{

			# Setup search parameters
			$search_term   = ( $this->input->post( 'search_term' ) ) ? $this->input->post( 'search_term' ) : false;
			$where   	   = ( $this->input->post( 'where' ) ) ? $this->input->post( 'where' ) : false;
			$limit		   = ( !empty( $where['limit'] ) )  ? $where['limit']  : DEFAULT_LIMIT;
			$start_index   = ( $this->input->post( 'start_index' ) ) ? $this->input->post( 'start_index' ) : DEFAULT_OFFSET;
			$offset		   = ( !empty( $start_index ) ) ? ( ( $start_index - 1 ) * $limit ) : 0;
			$order_by	   = ( $this->input->post( 'order_by' ) ) ? $this->input->post( 'order_by' ) : false;

			#prepare postdata
			$postdata = [
				'account_id'	=> $this->user->account_id,
				'search_term'	=> $search_term,
				'where'			=> $where,
				'order_by'		=> $order_by,
				'limit'			=> $limit,
				'offset'		=> $offset
			];

			$search_result		= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/schedules', $postdata, [ 'auth_token'=>$this->auth_token ], true );

			$schedules	= ( isset( $search_result->schedules ) ) ? $search_result->schedules : null;

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
					$this->pagination->initialize( $config );
					$pagination 			= $this->pagination->create_links();
				}

				$return_data = $this->load_schedules_view( $schedules );
				if( !empty( $pagination ) ){
					$return_data .= '<tr><td colspan="8" style="padding: 0;">';
						$return_data .= $page_display.$pagination;
					$return_data .= '</td></tr>';
				}
			}else{
				$return_data .= '<tr><td colspan="8" style="padding: 0 8px;"><br/>';
					$return_data .= ( isset( $search_result->message ) ) ? $search_result->message : 'No records found';
				$return_data .= '</td></tr>';
			}
		}

		print_r( $return_data );
		die();
	}

	/*
	* Prepare Schedules views
	*/
	private function load_schedules_view( $schedules_data ){
		$return_data = '';
		if( !empty( $schedules_data ) ){

			foreach( $schedules_data as $k => $schedule_details ){

				$return_data .= '<tr>';
					$return_data .= '<td><a href="'.base_url( '/webapp/job/schedule_profile/'.$schedule_details->schedule_id ).'" >'. $schedule_details->schedule_id .'</a></td>';
					$return_data .= '<td><a href="'.base_url( '/webapp/job/schedule_profile/'.$schedule_details->schedule_id ).'" >'. ucwords( $schedule_details->schedule_name ) .'</a></td>';
					$return_data .= '<td>'.$schedule_details->schedule_ref.'</td>';
					$return_data .= '<td class="text-center center"><span class="text-center center">'.$schedule_details->activities_total.'</span></td>';
					$return_data .= '<td>'.( ( valid_date( $schedule_details->date_created ) ) ? date( 'd-m-Y', strtotime( $schedule_details->date_created ) ) : '' ).'</td>';
					$return_data .= '<td>'.( ( valid_date( $schedule_details->first_activity_due_date ) ) ? date( 'd-m-Y', strtotime( $schedule_details->first_activity_due_date ) ) : '' ).'</td>';
					$return_data .= '<td>'.$schedule_details->schedule_status.'</td>';
				$return_data .= '</tr>';
			}

			if( !empty( $pagination ) ){
				$return_data .= '<tr><td colspan="8" style="padding: 0;">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
		}else{
			$return_data .= '<tr><td colspan="8"><br/>'.$this->config->item( "no_records" ).'</td></tr>';
		}
		return $return_data;
	}


	/** Delete Job Type **/
	public function delete_job_type( $job_type_id = false, $page = 'details' ){
		$return_data = [
			'status'=>0
		];

		$section 		= ( $this->input->post( 'page' ) ) 	? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$job_type_id 	= ( $this->input->post( 'job_type_id' ) ) ? $this->input->post( 'job_type_id' ) : ( !empty( $job_type_id ) ? $job_type_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	  		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$delete_job_type	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/delete_job_type', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  		= ( isset( $delete_job_type->status ) )  ? $delete_job_type->status : null;
			$message	  		= ( isset( $delete_job_type->message ) ) ? $delete_job_type->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status']		= 1;
				$return_data['job_type'] 	= $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	//Manage Stock Items - Overview page
	function stock( $item_id = false, $page = 'details' ){

		$toggled	= ( !empty( $this->input->get( 'toggled' ) ) ? $this->input->get( 'toggled' ) : false );
		$section 	= ( !empty( $page) ) ? $page : ( !empty( $this->input->get( 'page' ) ) ? $this->input->get( 'page' ) : 'details' );
		$item_id  	= ( !empty( $item_id) ) ? $item_id : ( !empty( $this->input->get( 'item_id' ) ) ? $this->input->get( 'item_id' ) : ( ( !empty( $this->input->get( 'item_id' ) ) ? $this->input->get( 'item_id' ) : null ) ) );
		$item_code 	= ( !empty( $this->input->get( 'item_code' ) )  ? $this->input->get( 'item_code' ) : false );

		if( !empty( $item_id ) || !empty( $item_code ) ){
			$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
			if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
				$this->_render_webpage( 'errors/access-denied', false );
			}else{
				$default_params = $params =[ 'account_id'=>$this->user->account_id, 'where'=>[ 'item_id'=>$item_id ] ];
				
				if( !empty( $item_code ) ){
					$params['where']['item_code'] = $item_code;
				}
				
				$stock_item_details = $this->webapp_service->api_dispatcher( $this->api_end_point.'stock/stock_items', $params, [ 'auth_token'=>$this->auth_token ], true );

				if( !empty( $stock_item_details->stock_items ) ){
					$data['stock_item_details']  	= $stock_item_details->stock_items;
					$associated_job_types  		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/associated_job_types', ['account_id'=>$this->user->account_id, 'item_id'=>$item_id ], ['auth_token'=>$this->auth_token], true );
					$data['associated_job_types']	= ( isset( $associated_job_types->associated_job_types ) ) ? $associated_job_types->associated_job_types : null;
					$this->_render_webpage( 'job/stock/stock_item_profile', $data );
				}else{
					redirect('webapp/job/stock', 'refresh');
				}
			}
		} else {
			$this->_render_webpage('job/stock/manage_stock_items', false, false, true );
		}
	}

	/*
	* Stock Items Bank List / Search
	*/
	public function stock_bank( $page = 'details' ){

		$return_data = '';

		$section 	 = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){

			$return_data .= $this->config->item( 'ajax_access_denied' );

		}else{

			# Setup search parameters
			$search_term   = ( $this->input->post( 'search_term' ) ) ? $this->input->post( 'search_term' ) : false;
			$where   	   = ( $this->input->post( 'where' ) ) ? $this->input->post( 'where' ) : false;
			$limit		   = ( !empty( $where['limit'] ) )  ? $where['limit']  : DEFAULT_LIMIT;
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

			$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'stock/stock_items', $postdata, [ 'auth_token'=>$this->auth_token ], true );

			$stock		= ( isset( $search_result->stock_items ) ) ? $search_result->stock_items : null;

			if( !empty( $stock ) ){

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

				$return_data = $this->load_stock_view( $stock );
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
	* Stock Items bank views
	*/
	private function load_stock_view( $stock_data ){
		$return_data = '';
		if( !empty( $stock_data ) ){

			foreach( $stock_data as $k => $stock_item ){

				$return_data .= '<tr>';
					$return_data .= '<td><a href="'.base_url( '/webapp/job/stock/'.$stock_item->item_id ).'" >'.$stock_item->item_name.'</a></td>';
					$return_data .= '<td>'.$stock_item->item_type.'</td>';
					$return_data .= '<td>'.$stock_item->item_code.'</td>';
					$return_data .= '<td>'.$stock_item->item_supplier.'</td>';
					$return_data .= '<td><span class="pull-right" >'.( !empty( $stock_item->is_active ) ? 'Active' : 'Disabled' ).'</span></td>';
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

	/** Update Stock Item Profile Details **/
	public function update_stock_item( $stock_item_id = false, $page = 'details' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$stock_item_id = ( $this->input->post( 'stock_item_id' ) ) ? $this->input->post( 'stock_item_id' ) : ( !empty( $stock_item_id ) ? $stock_item_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$update_stock_item= $this->webapp_service->api_dispatcher( $this->api_end_point.'stock/update_stock_item', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  = ( isset( $update_stock_item->stock_item ) ) ? $update_stock_item->stock_item : null;
			$message	  = ( isset( $update_stock_item->message ) ) 	? $update_stock_item->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 		= 1;
				$return_data['stock_item'] 	= $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/**
	* Delete Stock Item
	**/
	public function delete_stock_item( $item_id = false ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$item_id = ( $this->input->post( 'item_id' ) ) ? $this->input->post( 'item_id' ) : ( !empty( $item_id ) ? $item_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_delete ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$delete_stock_item = $this->webapp_service->api_dispatcher( $this->api_end_point.'stock/delete_stock_item', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  = ( isset( $delete_stock_item->status ) )  ? $delete_stock_item->status  : null;
			$message	  = ( isset( $delete_stock_item->message ) ) ? $delete_stock_item->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/**
	*	Manage BOM's items
	**/
	public function boms( $bom_id = false, $page = 'details' ){

		$toggled	= ( !empty( $this->input->get( 'toggled' ) ) ? $this->input->get( 'toggled' ) : false );
		$section 	= ( !empty( $page) ) ? $page : ( !empty( $this->input->get( 'page' ) ) ? $this->input->get( 'page' ) : 'details' );
		$bom_id  	= ( !empty( $bom_id) ) ? $bom_id : ( !empty( $this->input->get( 'bom_id' ) ) ? $this->input->get( 'bom_id' ) : ( ( !empty( $this->input->get( 'bom_id' ) ) ? $this->input->get( 'bom_id' ) : null ) ) );
		$item_code 	= ( !empty( $this->input->get( 'item_code' ) )  ? $this->input->get( 'item_code' ) : false );

		if( !empty( $bom_id ) || !empty( $item_code ) ){
			$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
			if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
				$this->_render_webpage( 'errors/access-denied', false );
			} else {
				$default_params = $params =[ 'account_id'=>$this->user->account_id, 'where'=>[ 'item_id'=>$bom_id ] ];
				
				if( !empty( $item_code ) ){
					$params['where']['item_code'] = $item_code;
				}

				$bom_details = $this->webapp_service->api_dispatcher( $this->api_end_point.'stock/bom_items', $params, [ 'auth_token'=>$this->auth_token ], true );
				if( !empty( $bom_details->bom_items ) ){
					$data['bom_details']  		 	= $bom_details->bom_items;
					## $associated_job_types  		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'risk_assessment/associated_job_types', ['account_id'=>$this->user->account_id, 'risk_id'=>$risk_id ], ['auth_token'=>$this->auth_token], true );
					## $data['associated_job_types']	= ( isset( $associated_job_types->associated_job_types ) ) ? $associated_job_types->associated_job_types : null;
					
					$bom_categories 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'stock/bom_categories', ['account_id'=>$this->user->account_id, 'limit'=>-1 ], [ 'auth_token'=>$this->auth_token ], true );
					$data['bom_categories']		= ( isset( $bom_categories->bom_categories ) ) ? $bom_categories->bom_categories : null;
					
					$this->_render_webpage( 'job/boms/bom_details_profile', $data );
				}else{
					redirect('webapp/job/boms', 'refresh');
				}
			}
		} else {
			$this->_render_webpage( 'job/boms/manage_boms', false, false, true );
		}
	}


	/*
	*	BOMS Bank List / Search
	*/
	public function boms_bank( $page = 'details' ){

		$return_data = '';

		$section 	 = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){
			$return_data .= $this->config->item( 'ajax_access_denied' );
		} else {

			# Setup search parameters
			$search_term   = ( $this->input->post( 'search_term' ) ) ? $this->input->post( 'search_term' ) : false;
			$where   	   = ( $this->input->post( 'where' ) ) ? $this->input->post( 'where' ) : false;
			$limit		   = ( !empty( $where['limit'] ) )  ? $where['limit']  : DEFAULT_LIMIT;
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

			$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'stock/bom_items', $postdata, [ 'auth_token'=>$this->auth_token ], true );
			$boms			= ( isset( $search_result->bom_items ) ) ? $search_result->bom_items : null;

			if( !empty( $boms ) ){

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

				$return_data = $this->load_boms_view( $boms );

				if( !empty( $pagination ) ){
					$return_data .= '<tr><td colspan="6" style="padding: 0;">';
						$return_data .= $page_display.$pagination;
					$return_data .= '</td></tr>';
				}
			}else{
				$return_data .= '<tr><td colspan="6" style="padding: 0 8px;"><br/>';
					$return_data .= ( isset( $search_result->message ) ) ? $search_result->message : 'No records found';
				$return_data .= '</td></tr>';
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/*
	* 	BOM bank views
	*/
	private function load_boms_view( $boms_data ){
		$return_data = '';
		if( !empty( $boms_data ) ){
			foreach( $boms_data as $k => $bom ){
				$return_data .= '<tr>';
					$return_data .= '<td><a href="'.base_url( '/webapp/job/boms/'.$bom->item_id ).'" >'.$bom->item_name.'</a></td>';
					$return_data .= '<td><a href="'.base_url( '/webapp/job/boms/'.$bom->item_id ).'" >'.$bom->item_code.'</a></td>';
					$return_data .= '<td>'.$bom->bom_category_name.'</td>';
					$return_data .= '<td class="text-right">'.$bom->item_qty.'</td>';
					$return_data .= '<td class="text-right">'.$bom->item_revenue.'</td>';
					$return_data .= '<td class="text-right">'.$bom->item_cost.'</td>';
				$return_data .= '</tr>';
			}

			if( !empty( $pagination ) ){
				$return_data .= '<tr><td colspan="6" style="padding: 0;">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
		}else{
			$return_data .= '<tr><td colspan="6"><br/>'.$this->config->item( "no_records" ).'</td></tr>';
		}
		return $return_data;
	}

	/**
	* 	Delete BOM Item
	**/
	public function delete_bom_item( $page = 'details' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_delete ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$api_call 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'stock/delete_bom_items', $postdata, ['auth_token'=>$this->auth_token] );
			$result		= ( isset( $api_call->status ) )  ? $api_call->status  : null;
			$message	= ( isset( $api_call->message ) ) ? $api_call->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/**
	*	Update BOM Item
	**/
	public function update_bom_item( $page = 'details' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
		$item_id = ( $this->input->post( 'item_id' ) ) ? $this->input->post( 'item_id' ) : null;

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$API_call	= $this->webapp_service->api_dispatcher( $this->api_end_point.'stock/update_bom_item', $postdata, ['auth_token'=>$this->auth_token] );
			$result		= ( isset( $API_call->bom_items ) ) ? $API_call->bom_items : null;
			$message	= ( isset( $API_call->message ) ) 	? $API_call->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 		= 1;
				$return_data['stock_item'] 	= $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/**
	*	Create BOM item
	**/
	public function new_bom( $page = 'details' ){
		$section 	= ( !empty( $page) ) ? $page : ( !empty( $this->input->get( 'page' ) ) ? $this->input->get( 'page' ) : 'details' );
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			$bom_categories 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'stock/bom_categories', ['account_id'=>$this->user->account_id, 'limit'=>-1 ], [ 'auth_token'=>$this->auth_token ], true );
			$data['bom_categories']		= ( isset( $bom_categories->bom_categories ) ) ? $bom_categories->bom_categories : null;

			$this->_render_webpage( 'job/boms/bom_create_profile', $data );
		}
	}


	/**
	*	Create BOM Item
	**/
	public function create_bom_item( $page = 'details' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$API_call	= $this->webapp_service->api_dispatcher( $this->api_end_point.'stock/create_bom_item', $postdata, ['auth_token'=>$this->auth_token] );

			$result		= ( isset( $API_call->bom_items ) ) ? $API_call->bom_items : null;
			$message	= ( isset( $API_call->message ) ) 	? $API_call->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 		= 1;
				$return_data['bom_item'] 	= $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/**
	*	Create Stock item
	**/
	public function new_stock( $page = 'details' ){
		$section 	= ( !empty( $page) ) ? $page : ( !empty( $this->input->get( 'page' ) ) ? $this->input->get( 'page' ) : 'details' );
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			$data = [];
			$this->_render_webpage( 'job/stock/stock_create_profile', $data );
		}
	}


	/**
	*	Create Stock Item
	**/
	public function create_stock_item( $page = 'details' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$API_call	= $this->webapp_service->api_dispatcher( $this->api_end_point.'stock/create', $postdata, ['auth_token'=>$this->auth_token] );
			$result		= ( isset( $API_call->stock_item ) ) ? $API_call->stock_item : null;
			$message	= ( isset( $API_call->message ) ) 	? $API_call->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 		= 1;
				$return_data['stock_item'] 	= $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/** Manage Job Tracking Statuses - Overview page **/
	function tracking_statuses( $job_tracking_id = false, $page = 'details' ){

		$toggled	= ( !empty( $this->input->get( 'toggled' ) ) ? $this->input->get( 'toggled' ) : false );
		$section 	= ( !empty( $page) ) ? $page : ( !empty( $this->input->get( 'page' ) ) ? $this->input->get( 'page' ) : 'details' );
		$job_tracking_id  = ( !empty( $job_tracking_id) ) ? $job_tracking_id : ( !empty( $this->input->get( 'job_tracking_id' ) ) ? $this->input->get( 'job_tracking_id' ) : ( ( !empty( $this->input->get( 'job_tracking_id' ) ) ? $this->input->get( 'job_tracking_id' ) : null ) ) );

		if( !empty( $job_tracking_id ) ){

			$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
			if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
				$this->_render_webpage( 'errors/access-denied', false );
			}else{
				$default_params = $params = [ 'account_id'=>$this->user->account_id, 'where'=>[ 'job_tracking_id'=>$job_tracking_id ] ];
				$job_tracking_status_details = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_tracking_statuses', $params, [ 'auth_token'=>$this->auth_token ], true );
				if( !empty( $job_tracking_status_details->job_tracking_statuses ) ){
					$data['job_tracking_details'] = $job_tracking_status_details->job_tracking_statuses;
					$this->_render_webpage( 'job/tracking_statuses/tracking_status_profile', $data );
				}else{
					redirect( 'webapp/job/tracking_statuses', 'refresh' );
				}
			}
		} else {
			$this->_render_webpage( 'job/tracking_statuses/tracking_statuses', false, false, true );
		}
	}
	

	/*
	* Job Tracking Statuses List / Search
	*/
	public function job_tracking_statuses( $page = 'details' ){

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

			$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_tracking_statuses', $postdata, [ 'auth_token'=>$this->auth_token ], true );

			$job_tracking_statuses		= ( isset( $search_result->job_tracking_statuses ) ) ? $search_result->job_tracking_statuses : null;

			if( !empty( $job_tracking_statuses ) ){

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

				$return_data = $this->load_job_tracking_statuses_view( $job_tracking_statuses );
				if( !empty( $pagination ) ){
					$return_data .= '<tr><td colspan="4" style="padding: 0;">';
						$return_data .= $page_display.$pagination;
					$return_data .= '</td></tr>';
				}
			}else{
				$return_data .= '<tr><td colspan="4" style="padding: 0 8px;"><br/>';
					$return_data .= ( isset( $search_result->message ) ) ? $search_result->message : 'No records found';
				$return_data .= '</td></tr>';
			}
		}

		print_r( $return_data );
		die();
	}

	/*
	* Job Tracking Status list views
	*/
	private function load_job_tracking_statuses_view( $job_tracking_statuses_data ){
		$return_data = '';
		if( !empty( $job_tracking_statuses_data ) ){

			foreach( $job_tracking_statuses_data as $k => $job_tracking_status ){

				$return_data .= '<tr>';
					$return_data .= '<td><a href="'.base_url( '/webapp/job/tracking_statuses/'.$job_tracking_status->job_tracking_id ).'" >'.$job_tracking_status->job_tracking_status.'</a></td>';
					$return_data .= '<td>'.$job_tracking_status->job_tracking_desc.'</td>';
					$return_data .= '<td>'.( !empty( $job_tracking_status->job_tracking_group ) ? $job_tracking_status->job_tracking_group : '' ).'</td>';
					$return_data .= '<td>'.( !empty( $job_tracking_status->is_active ) ? 'Active' : 'Disabled' ).'</td>';
				$return_data .= '</tr>';
			}

			if( !empty( $pagination ) ){
				$return_data .= '<tr><td colspan="4" style="padding: 0;">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
		}else{
			$return_data .= '<tr><td colspan="4"><br/>'.$this->config->item( 'no_records' ).'</td></tr>';
		}
		return $return_data;
	}


	/*
	* Add New Job Tracking Status
	*/
	public function new_tracking_status( $page = 'details' ){

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		}else{
			$this->_render_webpage( 'job/tracking_statuses/add_new_tracking_status', $data = false );
		}
	}


	/**
	* Create new Job Tracking Status
	*/
	public function add_job_tracking_status(){

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
			$new_job_tracking_status 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/create_job_tracking_status', $postdata, ['auth_token'=>$this->auth_token] );
			$result		= ( isset( $new_job_tracking_status->job_tracking_status ) ) ? $new_job_tracking_status->job_tracking_status : null;
			$message	= ( isset( $new_job_tracking_status->message ) ) ? $new_job_tracking_status->message : 'Oops! There was an error processing your request.';
			$exists	  	= ( !empty( $new_job_tracking_status->exists ) ) ? $new_job_tracking_status->exists : false;
			if( !empty( $result ) ){
				$return_data['status'] 				= 1;
				$return_data['job_tracking_status']	= $result;
				$return_data['already_exists']  	= $exists;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/** Update Job Tracking Status Profile Details **/
	public function update_job_tracking_status( $job_tracking_id = false, $page = 'details' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$job_tracking_id = ( $this->input->post( 'job_tracking_id' ) ) ? $this->input->post( 'job_tracking_id' ) : ( !empty( $job_tracking_id ) ? $job_tracking_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$update_job_tracking_status= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/update_job_tracking_status', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  = ( isset( $update_job_tracking_status->job_tracking_status ) )   ? $update_job_tracking_status->job_tracking_status : null;
			$message	  = ( isset( $update_job_tracking_status->message ) ) ? $update_job_tracking_status->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
				$return_data['job_tracking_status'] = $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/**
	* Delete Job Track Status
	**/
	public function delete_job_tracking_status( $job_tracking_id = false ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$job_tracking_id = ( $this->input->post( 'job_tracking_id' ) ) ? $this->input->post( 'job_tracking_id' ) : ( !empty( $job_tracking_id ) ? $job_tracking_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_delete ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	  				= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$delete_job_tracking_status = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/delete_job_tracking_status', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  				= ( isset( $delete_job_tracking_status->status ) )  ? $delete_job_tracking_status->status  : null;
			$message	  				= ( isset( $delete_job_tracking_status->message ) ) ? $delete_job_tracking_status->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	/** Get all Assets By the Selected Category ID **/
	public function fetch_assets_by_category( $page = 'schedules' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$params 	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$params['where']['grouped'] = 1;
			$assets_data	= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/assets_by_asset_type', $params, ['auth_token'=>$this->auth_token], true );
			$result			= ( isset( $assets_data->assets ) )  	? $assets_data->assets : null;
			$message		= ( isset( $assets_data->message ) ) 	? $assets_data->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 		= 1;
				$return_data['assets_data'] = $this->load_asset_data_view( $result );
			} else {
				$return_data['status_msg'] 	= 'There\'s currently no Assets Matching matching the selected Categories!';
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	//Load Assets By Asset Type / Category in Select view
	private function load_asset_data_view( $results_data = false ){
		$return_data = '';
		if( !empty( $results_data ) ){
			$return_data .= '<div>';
			foreach( $results_data as $asset_type => $assets_data ){
				$asset_type_ref = lean_string( $assets_data->asset_type->asset_type );
				$return_data .= '<div class="col-md-12" >';
					$return_data .= '<div class="alert bg-blue pointer" >';
						$return_data .= '<div class="row">';
							$return_data .= '<div class="col-md-12 col-sm-12 col-xs-12">';
								$return_data .= '<span><label class="text-white pointer"  ><input name="asset_type_id[]" id="group-chk-'.$asset_type_ref.'" class="group-check-all" data-group_check_id="group-chk-'.$asset_type_ref.'" type="checkbox" value="'.$assets_data->asset_type->asset_type_id.'" > &nbsp;'.$assets_data->asset_type->asset_type.' ('.count( $assets_data->assets ).')</label></span>';
								$return_data .= '<span class="pull-right pointer asset-types-toggle" data-asset_types_check_id="'.$asset_type_ref.'" >View list</span>';
							$return_data .= '</div>';
						$return_data .= '</div>';
					$return_data .= '</div>';

					$return_data .= '<div class="'.$asset_type_ref.' x_panel" style="display:none;" >';
						$return_data .= '<div class="col-md-12 col-sm-12 col-xs-12">';
							$return_data .= '<ul>';
								foreach( $assets_data->assets as $key => $asset ){
									$primary_attrib = !empty( $asset->attribute_value ) ? ( is_json( $asset->attribute_value ) ? json_decode( $asset->attribute_value ) : $asset->attribute_value ) : '';
									$primary_attrib = !empty( $primary_attrib ) ? ( is_array( $primary_attrib ) ? implode( ', ',  $primary_attrib ) : $primary_attrib ) : false;
									$primary_attrib = !empty( $primary_attrib ) ? ' ('.$primary_attrib.') ' : '';
									$return_data .= '<div><label class="pointer" ><input name="asset_id[]" class="group-check group-chk-'.$asset_type_ref.'" data-group_class="group-check-chk-'.$asset_type_ref.'" type="checkbox" data-asset_type_id="'.$assets_data->asset_type->asset_type_id.'" data-asset_type="'.$assets_data->asset_type->asset_type.'"  data-asset_unique_id="'.$asset->asset_unique_id.'" value="'.$asset->asset_id.'" > &nbsp;'.$asset->asset_type.' - '.$asset->asset_unique_id.$primary_attrib.'</label></div>';
								}
							$return_data .= '</ul>';
						$return_data .= '</div>';
					$return_data .= '</div>';

				$return_data .= '</div>';
			}
			$return_data .= '</div>';
		}else{
			$return_data .= '</div>';
		}
		return $return_data;
	}


	/** Get all Evidocs required for Multiple Schedules **/
	public function fetch_evidocs_multi_display( $page = 'schedules' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );

			$frequency_id 	= !empty( $postdata['where']['frequency_id'] ) ? $postdata['where']['frequency_id'] : '';
			$frequency_name = !empty( $postdata['where']['frequency_name'] ) ? urldecode( $postdata['where']['frequency_name'] ) : 'this Inspection';
			$assets_data 	= !empty( $postdata['where']['assets_data'] ) ? $postdata['where']['assets_data'] : false;
			unset( $postdata['where']['frequency_name'],  $postdata['where']['assets_data'] );

			$evidoc_types	= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/evidoc_types', $postdata, ['auth_token'=>$this->auth_token], true );
			$result			= ( isset( $evidoc_types->evidoc_types ) )  	? $evidoc_types->evidoc_types : null;
			$message		= ( isset( $evidoc_types->message ) ) 			? $evidoc_types->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) && !empty( $assets_data ) ){
				$assets_data = $this->reorganize_assets_data( $assets_data );
				$return_data['status'] 		 = 1;
				$return_data['evidocs_data'] = $this->load_evidoc_types_multi_display_view( $assets_data, $result );
			} else {
				$return_data['status_msg'] = '<strong>No data found due to one or all of the reasons below:-</strong><br/><br/>';
				$return_data['status_msg'] .= '- No EviDocs matching the selected Frequency! <a href="'.base_url( 'webapp/audit/evidoc_names' ).'" target="_blank" title="Click here to view existing EviDocs or create a new one!" >Manage</a><br/><br/>';
				$return_data['status_msg'] .= '- The existing EviDocs linked to <a href="'.base_url( 'webapp/audit/evidoc_names?frequency_id='.$frequency_id ).'" target="_blank" title="Click here to go to the setup page!" >'.$frequency_name.'</a> do not have any Questions yet!<br/><br/>';
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	//Load Schedule Evidocs for Multiple Schedules
	private function load_evidoc_types_multi_display_view( $multi_assets_data = false, $evidoc_types = false ){

		$return_data = '';

		if( !empty( $multi_assets_data ) && !empty( $evidoc_types ) ){

			$return_data .= '<div>';
			foreach( $multi_assets_data as $asset_type => $assets_data ){
				$asset_type_ref = lean_string( $assets_data->asset_type->asset_type );
				$return_data .= '<div class="col-md-12" >';
					$return_data .= '<div class="alert bg-blue pointer" >';
						$return_data .= '<div class="row">';
							$return_data .= '<div class="col-md-12 col-sm-12 col-xs-12">';
								$return_data .= '<div class="row">';
									$return_data .= '<div class="col-md-4 col-sm-4">';
										$return_data .= '<div><strong>'.$assets_data->asset_type->frequency_name.'</strong></div>';
									$return_data .= '</div>';
									$return_data .= '<div class="col-md-4 col-sm-4">';
										$return_data .= '<span><label class="text-white pointer"  ><input name="asset_type_id[]" id="group-chk-'.$asset_type_ref.'" class="group-check-all" data-group_check_id="group-chk-'.$asset_type_ref.'" checked type="checkbox" value="'.$assets_data->asset_type->asset_type_id.'" > &nbsp;<strong>'.$assets_data->asset_type->asset_type.' ('.count( object_to_array( $assets_data->assets ) ).')</strong></label></span>';
										$return_data .= '<span class="hide pull-right pointer asset-types-toggle" data-asset_types_check_id="'.$asset_type_ref.'" >View list</span>';
									$return_data .= '</div>';
									$return_data .= '<div class="col-md-4 col-sm-4">';
										$return_data .= '<div><strong>Select Evidoc Type</strong>';
											$return_data .= '<select id="evidoc'.$assets_data->asset_type->asset_type_id.'" name="evidoc_type_id" class="form-control evidoc-type-select" >';
												foreach( $evidoc_types as $k => $evidoc_type ){
													$return_data .= '<option value="'.$evidoc_type->audit_type_id.'" data-evidoc_type_id="'.$evidoc_type->audit_type_id.'" data-evidoc_type="'.$evidoc_type->audit_type.'" >'.$evidoc_type->audit_type.'</option>';
												}
											$return_data .= '</select>';
										$return_data .= '</div>';
									$return_data .= '</div>';
								$return_data .= '</div>';
							$return_data .= '</div>';
						$return_data .= '</div>';
					$return_data .= '</div>';

					$return_data .= '<div class="'.$asset_type_ref.' x_panel" style="display:none;" >';
						$return_data .= '<div class="col-md-12 col-sm-12 col-xs-12">';
							$return_data .= '<ul>';
								foreach( $assets_data->assets as $key => $asset ){
									$return_data .= '<div><label class="pointer" ><input name="asset_id[]" class="group-check group-chk-'.$asset_type_ref.'" data-group_class="group-check-chk-'.$asset_type_ref.'" checked type="checkbox" data-asset_type_id="'.$assets_data->asset_type->asset_type_id.'" data-asset_type="'.$assets_data->asset_type->asset_type.'"  data-asset_unique_id="'.$asset->asset_unique_id.'" value="'.$asset->asset_id.'" > &nbsp;'.$asset->asset_type.' - '.$asset->asset_unique_id.'</label></div>';
								}
							$return_data .= '</ul>';
						$return_data .= '</div>';
					$return_data .= '</div>';

				$return_data .= '</div>';
			}
			$return_data .= '</div>';

		} else {

			$return_data .= '<div>'.$this->config->item( 'no_records' ).'</div>';

		}

		return $return_data;
	}

	/** Reorganize an Array received from AJAX call **/
	private function reorganize_assets_data( $assets_data = false ){
		$result = [];
		if( !empty( $assets_data ) ){
			foreach( $assets_data as $k => $asset ){
				if( !empty( $asset['frequency_id'] ) ){
					$result[$asset['asset_type_id']]['asset_type'] 	= [ 'asset_type_id'=>$asset['asset_type_id'], 'asset_type'=>$asset['asset_type'], 'frequency_id'=>$asset['frequency_id'] , 'frequency_name'=>$asset['frequency_name'] ];
					$result[$asset['asset_type_id']]['assets'][$asset['asset_id']] 	= [ 'asset_id'=>$asset['asset_id'], 'asset_unique_id'=>$asset['asset_unique_id'], 'asset_type_id'=>$asset['asset_type_id'] , 'asset_type'=>$asset['asset_type'] ];
					if( !empty( $asset['evidoc_type_id'] ) ){
						$result[$asset['asset_type_id']]['asset_type']['evidoc_type_id'] 				 = $asset['evidoc_type_id'];
						$result[$asset['asset_type_id']]['asset_type']['evidoc_type'] 				 	 = $asset['evidoc_type'];
						$result[$asset['asset_type_id']]['assets'][$asset['asset_id']]['evidoc_type_id'] = $asset['evidoc_type_id'];
						$result[$asset['asset_type_id']]['assets'][$asset['asset_id']]['evidoc_type'] 	 = $asset['evidoc_type'];
					}

					if( !empty( $asset['job_type_id'] ) ){
						$result[$asset['asset_type_id']]['asset_type']['job_type_id'] 				 = $asset['job_type_id'];
						$result[$asset['asset_type_id']]['asset_type']['job_type'] 				 	 = $asset['job_type'];
						$result[$asset['asset_type_id']]['assets'][$asset['asset_id']]['job_type_id']= $asset['job_type_id'];
						$result[$asset['asset_type_id']]['assets'][$asset['asset_id']]['job_type'] 	 = $asset['job_type'];
					}

				}
			}
			$result = array_to_object( $result );
		}
		return $result;
	}


	public function reorganize_assets_data_ajax( $postdata = false ){
		$return_data['status'] = 0;
		$return_data['result'] = false;
		$postdata 		= $this->input->post();
		$assets_data 	= !empty( $postdata['assets_data'] ) ? $postdata['assets_data'] : false;

		if( !empty( $assets_data ) ){
			$result = $this->reorganize_assets_data( $assets_data );

			if( !empty( $result ) ){
				$return_data['status'] = 1;
				$return_data['result'] = $result;
			}
		}
		print_r( json_encode( $return_data ) );
		die();
	}

	/** Get all Job Types required for Multiple Schedules attached to an EviDoc **/
	public function fetch_job_types_multiple_display( $page = 'schedules' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );

			$assets_data 	= !empty( $postdata['where']['assets_data'] ) ? $postdata['where']['assets_data'] : false;
			unset( $postdata['where']['assets_data'] );

			$job_types		= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_types', $postdata, ['auth_token'=>$this->auth_token], true );
			$result			= ( isset( $job_types->job_types ) )  	? $job_types->job_types : null;
			$message		= ( isset( $job_types->message ) ) 		? $job_types->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) && !empty( $assets_data ) ){
				$assets_data = $this->reorganize_assets_data( $assets_data );
				$return_data['status'] 		 	= 1;
				$return_data['job_types_data'] 	= $this->load_job_types_multiple_display_view( $assets_data, $result );
			} else {
				$return_data['status_msg'] = 'There\'s currently no Job Types matching the selected EviDoc!';
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	//Load Job Types for multiple Schedules
	private function load_job_types_multiple_display_view( $multi_assets_data = false, $job_types = false ){
		$return_data = '';
		if( !empty( $multi_assets_data ) && !empty( $job_types ) ){

			$return_data .= '<div>';
			foreach( $multi_assets_data as $asset_type => $assets_data ){
				$asset_type_ref = lean_string( $assets_data->asset_type->asset_type );
				$return_data .= '<div class="col-md-12" >';
					$return_data .= '<div class="alert bg-blue pointer" >';
						$return_data .= '<div class="row">';
							$return_data .= '<div class="col-md-12 col-sm-12 col-xs-12">';
								$return_data .= '<div class="row">';
									$return_data .= '<div class="col-md-3 col-sm-4">';
										$return_data .= '<div><strong>'.$assets_data->asset_type->frequency_name.'</strong></div>';
									$return_data .= '</div>';
									$return_data .= '<div class="col-md-3 col-sm-4">';
										$return_data .= '<input id="ass-type'.$assets_data->asset_type->asset_type_id.'" type="hidden" class="selected-options" data-asset_type_id="'.$assets_data->asset_type->asset_type_id.'" data-evidoc_type_id="'.$assets_data->asset_type->evidoc_type_id.'" data-evidoc_type="'.$assets_data->asset_type->evidoc_type.'" data-total_assets="'.count( object_to_array( $assets_data->assets ) ).'"  value="'.$assets_data->asset_type->asset_type.'">';
										$return_data .= '<span><label class="text-white pointer"  ><input onclick="return false" name="asset_type_id[]" id="job-group-chk-'.$asset_type_ref.'" class="job-group-check-all" data-group_check_id="job-group-chk-'.$asset_type_ref.'" checked type="checkbox" value="'.$assets_data->asset_type->asset_type_id.'" > &nbsp;<strong>'.$assets_data->asset_type->asset_type.'</strong></label></span>';
										$return_data .= '<span class="hide pull-right pointer asset-types-toggle" data-asset_types_check_id="'.$asset_type_ref.'" >View list</span>';
									$return_data .= '</div>';
									$return_data .= '<div class="col-md-3 col-sm-4">';
										$return_data .= '<div><strong>'.urldecode( $assets_data->asset_type->evidoc_type ).'</strong></div>';
									$return_data .= '</div>';
									$return_data .= '<div class="col-md-3 col-sm-4">';
										$return_data .= '<div><strong>Select Job Type</strong>';
											$return_data .= '<select id="jobtype'.$assets_data->asset_type->asset_type_id.'" name="job_type_id[]" class="form-control job-type-options" >';
												foreach( $job_types as $k => $job_type ){
													$return_data .= '<option value="'.$job_type->job_type_id.'" data-evidoc_type_id="'.$job_type->evidoc_type_id.'" data-evidoc_type="'.$job_type->audit_type.'" data-job_type_id="'.$job_type->job_type_id.'" data-job_type="'.$job_type->job_type.'" >'.$job_type->job_type.'</option>';
												}
											$return_data .= '</select>';
										$return_data .= '</div>';
									$return_data .= '</div>';
								$return_data .= '</div>';
							$return_data .= '</div>';
						$return_data .= '</div>';
					$return_data .= '</div>';

					$return_data .= '<div class="'.$asset_type_ref.' x_panel" style="display:none;" >';
						$return_data .= '<div class="col-md-12 col-sm-12 col-xs-12">';
							$return_data .= '<ul>';
								foreach( $assets_data->assets as $key => $asset ){
									$return_data .= '<div><label class="pointer" ><input name="asset_id[]" class="job-group-check job-group-chk-'.$asset_type_ref.'" data-group_class="job-group-check-chk-'.$asset_type_ref.'" checked type="checkbox" data-asset_type_id="'.$assets_data->asset_type->asset_type_id.'" data-asset_type="'.$assets_data->asset_type->asset_type.'" data-asset_unique_id="'.$asset->asset_unique_id.'" value="'.$asset->asset_id.'" > &nbsp;'.$asset->asset_type.' - '.$asset->asset_unique_id.'</label></div>';
								}
							$return_data .= '</ul>';
						$return_data .= '</div>';
					$return_data .= '</div>';

				$return_data .= '</div>';
			}
			$return_data .= '</div>';

		}else{
			$return_data .= '<div>'.$this->config->item( 'no_records' ).'</div>';
		}
		return $return_data;
	}

	/** Prepare Schedule Placeholders **/
	public function prepare_schedule_placeholders( $frequency_id = false ){

		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$schedule_data  = !empty( $postdata['where'] ) ? $postdata['where'] : false;
			$assets_data    = !empty( $schedule_data['assets_data'] ) ? $schedule_data['assets_data'] : false;
			unset( $schedule_data['assets_data'] );
			if( !empty( $schedule_data ) && !empty( $assets_data ) ){
				$assets_data = $this->reorganize_assets_data( $assets_data );
				$return_data['status'] 		 	= 1;
				$return_data['schedules_data'] 	= $this->load_schedule_placeholders_view( $schedule_data, $assets_data );
			} else {
				$return_data['status_msg'] = 'There was a problem processing your Schedule data!';
			}
		}

		print_r( json_encode( $return_data ) );
		die();

	}

	//Load Job Types for multiple Schedules
	private function load_schedule_placeholders_view( $schedule_data = false, $assets_data = false ){
		$return_data = '';
		if( !empty( $schedule_data ) && !empty( $assets_data ) ){
			$assets_data		= array_to_object( $assets_data );
			$freqeuncyId  		= $schedule_data['frequency_id'];
			$activityName  		= $schedule_data['frequency_name'];
			$activityNameTag  	= $schedule_data['frequency_name'];
			$activityCount 		= $schedule_data['total_activities'];
			$activityInterval 	= $schedule_data['activity_interval'];
			$activity_percentage= $schedule_data['activity_interval'];
			$dueDate			= date( 'd-m-Y', strtotime( $schedule_data['due_date'] ) );
			$assetCheckCount	= $schedule_data['number_of_checks'];
			$x 					= 1;
			$activityName  		= 'Asset '.$activityName;
			$totalActivitiesDue	= 0;
			$totalAssets		= [];

			$return_data .= '<table class="table table-responsive" style="width:100%" >';

				$return_data .= '<tr style="display:none"><td colspan="4" >';
					$return_data .= '<input type="hidden" name="frequency_id" value="'.$freqeuncyId.'" />';
					$return_data .= '<input type="hidden" name="schedule_name" value="'.$activityName.'" />';
				$return_data .= '</td></tr>';
				
				for( $i = 1; $i <= $activityCount; $i++ ){
					$return_data .= '<tr>';
						$return_data .=  '<td colspan="4" width="100%" class="bg-grey" ><h4><strong>'.$activityName.' Activity '.$x.'</strong></h4></td>';
					$return_data .= '</tr>';

					// Set Due Date to End of the Month
					$dateObj 			= new DateTime( $dueDate ); 
					$last_day_of_month 	= $dateObj->format( 'Y-m-t' );
					
					switch( strtolower( $activityNameTag ) ){
						case 'weekly':
						case 'weekly inspection':
						case 'weekly-inspection':
							$processedDueDate	 = isset( $processedDueDate ) 	 ? date( 'd-m-Y', strtotime( $processedDueDate.' + 7 days' ) ) : date( 'd-m-Y', strtotime( $dueDate ) );
							$processedJobDueDate = isset( $processedJobDueDate ) ? date( 'd-m-Y', strtotime( $processedJobDueDate.' + 7 days' ) ) : date( 'd-m-Y', strtotime( $dueDate ) );
							$activityInterval_counter	 = '+7 days';
							break;
							
						case 'monthly':
						case 'monthly inspection':
						case 'monthly-inspection':
							$activityInterval_counter	= '+1 month';
							$processedDueDate	 		= ( ( date( 'd-m-Y', strtotime( $last_day_of_month ) ) > date( 'd-m-Y', strtotime( $dueDate ) ) ) ? date( 'd-m-Y', strtotime( $last_day_of_month ) ) : date( 'd-m-d', strtotime( $dueDate.' + 28 days' ) ) );
							$processedJobDueDate 		= isset( $processedJobDueDate ) ? date( 'd-m-Y', strtotime( $processedJobDueDate.' + 1 month' ) ) : date( 'd-m-Y', strtotime( $dueDate ) );
							break;
							
						default:
							$processedDueDate			= ( ( date( 'd-m-Y', strtotime( $last_day_of_month ) ) > date( 'd-m-Y', strtotime( $dueDate ) ) ) ? date( 'd-m-Y', strtotime( $last_day_of_month ) ) : date( 'd-m-Y', strtotime( $dueDate ) ) );
							$processedJobDueDate		= date( 'd-m-Y', strtotime( $dueDate ) );
							$activityInterval_counter 	= (int)$activityInterval.' days';
							break;
					}

					foreach( $assets_data as $key => $assets_obj ){

						$assets_array			= object_to_array( $assets_obj->assets );
						$total_assets 			= count( $assets_array );
						$total_assets_to_check 	= ( !empty( $consecutive_checks ) ) ? $consecutive_checks : ceil( ( ( $total_assets * $assetCheckCount ) / $activityCount ) );

						$return_data .= '<tr>';
							$return_data .=  '<td colspan="4"  >&nbsp;&nbsp;&nbsp;<strong>'.strtoupper( $assets_obj->asset_type->asset_type ).' ('.$total_assets.')</strong><span class="pull-right hide" >Total Assets: '.$total_assets.' | To Be Checked: '.$total_assets_to_check.'</span></td>';
						$return_data .= '</tr>';
						$return_data .= '<tr>';
							$return_data .=  '<td  colspan="4" >';
								$return_data .= '<div class="col-md-12" >';
									$looper = 0;
									$return_data .= '<div class="row" >';
										$return_data .=  '<div class="col-md-3" ><strong><small>ASSET UNIQUE ID</small></strong></div>';
										$return_data .=  '<div class="col-md-4" ><strong><small>EVIDOC NAME</small></strong></div>';
										$return_data .=  '<div class="col-md-3" ><strong><small>JOB TYPE</small></strong></div>';
										$return_data .=  '<div class="col-md-2" ><strong><small>DUE DATE</small></strong></div>';
									$return_data .= '</div>';

									foreach( $assets_obj->assets as $k => $asset ){
										
										$activity_name = ordinal( $x ).' Visit - ' .$activityName; 
										$return_data .= '<input type="hidden" name="schedule_activities[multi]['.$x.']['.$asset->asset_id.'][asset_id]" value="'.$asset->asset_id.'" />';
										$return_data .= '<input type="hidden" name="schedule_activities[multi]['.$x.']['.$asset->asset_id.'][activities_total]" value="'.$activityCount.'" />';
										$return_data .= '<input type="hidden" name="schedule_activities[multi]['.$x.']['.$asset->asset_id.'][job_type_id]" value="'.$asset->job_type_id.'" />';
										$return_data .= '<input type="hidden" name="schedule_activities[multi]['.$x.']['.$asset->asset_id.'][activity_name]" value="'.$activity_name.'" />';
										$return_data .= '<input type="hidden" name="schedule_activities[multi]['.$x.']['.$asset->asset_id.'][proportion]" value="" />';
										$return_data .= '<input type="hidden" name="schedule_activities[multi]['.$x.']['.$asset->asset_id.'][due_date]" value="'.$processedDueDate.'" />';
										$return_data .= '<input type="hidden" name="schedule_activities[multi]['.$x.']['.$asset->asset_id.'][job_due_date]" value="'.$dueDate.'" />';

										$return_data .= '<div class="row" >';
											$return_data .=  '<div class="col-md-3" >&nbsp;&nbsp;&nbsp;'.$asset->asset_unique_id.'</div>';
											$return_data .=  '<div class="col-md-4" >'.urldecode( $asset->evidoc_type ).'</div>';
											$return_data .=  '<div class="col-md-3" >'.urldecode( $asset->job_type ).'</div>';
											$return_data .=  '<div class="col-md-2" >'.$processedDueDate.'</div>';
										$return_data .= '</div>';
										$totalActivitiesDue++;
										$totalAssets[$asset->asset_id] = $asset->asset_id;
									}
									
								$return_data .= '</div>';
							$return_data .=  '</td>';
						$return_data .= '</tr>';
					}

					$dueDate 		= date( 'd-m-Y', strtotime( $dueDate.' '.$activityInterval_counter ) );
					$x++;
				}
				
				$return_data .= '<tr style="display:none"><td colspan="4" >';
					$return_data .= '<input class="total_assets" id="total_assets" type="hidden" value="'.count( $totalAssets ).'" />';
					$return_data .= '<input class="total_activities_due" id="total_activities_due" type="hidden" value="'.$totalActivitiesDue.'" />';
				$return_data .= '</td></tr>';
			$return_data .= '</table>';
		}
		return $return_data;
	}



	/**
	*	Download schedule data as a PDF
	**/
	public function download_schedule( $schedule_id = false){

		if( $schedule_id ){
			$postdata 				= ['account_id'=>$this->user->account_id, 'where'=>['schedule_id'=>$schedule_id] ];
			$API_call				= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/schedules', $postdata, ['auth_token'=>$this->auth_token], true );
			$schedule_details		= ( isset( $API_call->schedules ) ) ? $API_call->schedules : ( object )[];

			$API_call 									= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/schedule_activities_w_evidocs', ['account_id'=>$this->user->account_id,'where'=>['schedule_id' => $schedule_id ] ], ['auth_token'=>$this->auth_token], true );
			$schedule_details->schedule_activities 		= ( isset( $API_call->schedule_activities ) ) ? $API_call->schedule_activities : null;
			$schedule_details->uploaded_docs 			= ( isset( $API_call->uploaded_docs ) ) ? $API_call->uploaded_docs : null;

			if( !empty( $schedule_details ) ){
				$this->fetch( $schedule_id, $schedule_details  );
			} else {
				redirect( 'webapp/job/schedules/', 'refresh' );
			}

		} else {
			redirect( 'webapp/job/schedules/', 'refresh' );
		}
	}


	/**
	*	Fetch file for the Schedule Download
	**/
	public function fetch( $schedule_id = false, $schedule_data = false ){

		if( !empty( $schedule_id ) && !empty( $schedule_data ) ){
			$custom_logo_file		 = '/assets/images/accounts/'.$this->user->account_id.'/main-logo-small.png';
			$setup['document_setup'] = [
				'recipient_details'	=>[
					'show_recipient'	=> false,
					'recipient_name' 	=> 'James Bond Esq.',
					'address_line1'		=> 'The Reef',
					'address_line2'		=> 'C/O Initiative Property Management',
					'address_line3'		=> 'Suite 4, Lansdowne Place',
					'address_town'		=> 'Bournemouth',
					'address_country'	=> 'Surrey',
					'address_postcode'	=> 'BH8 8EW',
				],
				'document_content'	=> $schedule_data,
				'generic_details'	=>[
					'document_name'			=> ( !empty( $schedule_data->schedule_name ) ) ? $schedule_data->schedule_name : '' ,
					'schedule_frequency'	=> ( !empty( $schedule_data->frequency_name ) ) ? ' ('.$schedule_data->frequency_name.') ' : '',
					'document_date'			=> date( 'l, jS F Y' ),
					'referrence_number'		=> '',
					'custom_logo'			=> ( file_exists( $this->appDir.$custom_logo_file ) ) ? base_url( $custom_logo_file ) : false, //This link should be saved in the DB account configs!
					'custom_log_dimensions'	=> 'width="200px"',
					'custom_footer'			=> DOCUMENT_POWERED_BY,
					'image_preview'			=> true,
				],
			];

			$this->ssid_common->create_pdf_from_template( 'evipdf/templates/schedule_pdf_template.php', $setup );

		} else {
			redirect('webapp/audit/', 'refresh');
		}
	}
	
	
	/**
	* Remove Associated BOM Item from Job
	**/
	public function remove_required_boms( $question_id = false ){
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
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$drop_bom 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/remove_required_boms', $postdata, ['auth_token'=>$this->auth_token] );
			$result		= ( isset( $drop_bom->status ) ) ? $drop_bom->status : null;
			$message	= ( isset( $drop_bom->message ) ) ? $drop_bom->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/** Add Required BOM to a Job **/
	public function add_required_boms(){

		$section 	 = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
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
			$required_boms	 = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/add_required_boms' , $postdata, ['auth_token'=>$this->auth_token] );
			
			$result		  	 = ( isset( $required_boms->required_boms ) ) ? $required_boms->required_boms : null;
			$message	  	 = ( isset( $required_boms->message ) )  ? $required_boms->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 	 = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	
		/**
	*	Create Risk item
	**/
	public function new_risk_item( $page = 'details' ){
		$section 	= ( !empty( $page) ) ? $page : ( !empty( $this->input->get( 'page' ) ) ? $this->input->get( 'page' ) : 'details' );
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			$data = [];
			$this->_render_webpage( 'job/risks/create_new_risk_item', $data );
		}
	}


	/**
	*	Create Risk Item
	**/
	public function create_risk_item( $page = 'details' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$API_call	= $this->webapp_service->api_dispatcher( $this->api_end_point.'risk_assessment/create_risk_item', $postdata, ['auth_token'=>$this->auth_token] );
			$result		= ( isset( $API_call->risk_item ) ) ? $API_call->risk_item : null;
			$message	= ( isset( $API_call->message ) ) 	? $API_call->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 		= 1;
				$return_data['risk_item'] 	= $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/** Update Risk Item Profile Details **/
	public function update_risk_item( $risk_id = false, $page = 'details' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$risk_id = ( $this->input->post( 'risk_id' ) ) ? $this->input->post( 'risk_id' ) : ( !empty( $risk_id ) ? $risk_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	  		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$update_risk_item	= $this->webapp_service->api_dispatcher( $this->api_end_point.'risk_assessment/update_risk_item', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  		= ( isset( $update_risk_item->risk_item ) ) 	? $update_risk_item->risk_item : null;
			$message	  		= ( isset( $update_risk_item->message ) ) 	? $update_risk_item->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 		= 1;
				$return_data['risk_item'] 	= $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/**
	* Delete Risk Item
	**/
	public function delete_risk_item( $risk_id = false ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$risk_id = ( $this->input->post( 'risk_id' ) ) ? $this->input->post( 'risk_id' ) : ( !empty( $risk_id ) ? $risk_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_delete ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	  		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$delete_risk_item 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'risk_assessment/delete_risk_item', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  		= ( isset( $delete_risk_item->status ) )  ? $delete_risk_item->status  : null;
			$message	  		= ( isset( $delete_risk_item->message ) ) ? $delete_risk_item->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	//BOM Categories - Overview page
	function bom_categories( $bom_category_id = false, $page = 'details' ){

		$toggled		 = ( !empty( $this->input->get( 'toggled' ) ) ? $this->input->get( 'toggled' ) : false );
		$section 		 = ( !empty( $page) ) ? $page : ( !empty( $this->input->get( 'page' ) ) ? $this->input->get( 'page' ) : 'details' );
		$bom_category_id = ( !empty( $bom_category_id) ) ? $bom_category_id : ( !empty( $this->input->get( 'bom_category_id' ) ) ? $this->input->get( 'bom_category_id' ) : ( ( !empty( $this->input->get( 'bom_category_id' ) ) ? $this->input->get( 'bom_category_id' ) : null ) ) );
		
		if( !empty( $bom_category_id ) ){
			
			$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
			if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
				$this->_render_webpage( 'errors/access-denied', false );
			}else{
				$default_params 		= $params = [ 'account_id'=>$this->user->account_id, 'where'=>[ 'bom_category_id'=>$bom_category_id ] ];
				$bom_category_details 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'stock/bom_categories', $params, [ 'auth_token'=>$this->auth_token ], true );

				if( !empty( $bom_category_details->bom_categories ) ){
					$data['bom_category_details']	= $bom_category_details->bom_categories;
					$linked_job_types  				= $this->webapp_service->api_dispatcher( $this->api_end_point.'stock/linked_job_types', ['account_id'=>$this->user->account_id, 'bom_category_id'=>$bom_category_id ], ['auth_token'=>$this->auth_token], true );			
					$data['linked_job_types']		= ( isset( $linked_job_types->linked_job_types ) ) ? $linked_job_types->linked_job_types : null;

					$this->_render_webpage( 'job/boms/category_details_profile', $data );					
				}else{
					redirect( 'webapp/bom/categories', 'refresh' );
				}
			}
		} else {
			$this->_render_webpage( 'job/boms/manage_categories', false, false, true );
		}
	}
	
	
	/*
	* BOM Categories List / Search
	*/
	public function bom_categories_list( $page = 'details' ){

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

			$search_result		= $this->webapp_service->api_dispatcher( $this->api_end_point.'stock/bom_categories', $postdata, [ 'auth_token'=>$this->auth_token ], true );
			
			$bom_categories		= ( isset( $search_result->bom_categories ) ) ? $search_result->bom_categories : null;

			if( !empty( $bom_categories ) ){

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
				
				$return_data = $this->load_bom_categories_view( $bom_categories );
				if( !empty( $pagination ) ){
					$return_data .= '<tr><td colspan="4" style="padding: 0;">';
						$return_data .= $page_display.$pagination;
					$return_data .= '</td></tr>';
				}				
			}else{
				$return_data .= '<tr><td colspan="4" style="padding: 0 8px;"><br/>';
					$return_data .= ( isset( $search_result->message ) ) ? $search_result->message : 'No records found';
				$return_data .= '</td></tr>';
			}
		}

		print_r( $return_data );
		die();
	}
	
	/*
	* BOMs Category list views
	*/
	private function load_bom_categories_view( $categories_data ){
		$return_data = '';
		if( !empty( $categories_data ) ){
			
			foreach( $categories_data as $k => $category ){

				$return_data .= '<tr>';
					$return_data .= '<td><a href="'.base_url( '/webapp/job/bom_categories/'.$category->bom_category_id ).'" >'.$category->bom_category_name.'</a></td>';									
					$return_data .= '<td>'.$category->bom_category_description.'</td>';									
					$return_data .= '<td>'.( !empty( $category->bom_category_group ) ? $category->bom_category_group : '' ).'</td>';									
					$return_data .= '<td>'.( !empty( $category->is_active ) ? 'Active' : 'Disabled' ).'</td>';													
				$return_data .= '</tr>';
			}

			if( !empty( $pagination ) ){
				$return_data .= '<tr><td colspan="4" style="padding: 0;">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
		}else{
			$return_data .= '<tr><td colspan="4"><br/>'.$this->config->item( 'no_records' ).'</td></tr>';
		}
		return $return_data;
	}
	
	
	/*
	* Add New BOM Category
	*/
	public function new_category( $page = 'details' ){

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		}else{
			$this->_render_webpage( 'job/boms/category_add_new', $data = false );
		}
	}
	
	
	/** 
	* Add a new BOM Category
	**/
	public function add_bom_category(){

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
			$postdata 	  	 = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$bom_category 	 = $this->webapp_service->api_dispatcher( $this->api_end_point.'stock/add_bom_category', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  	 = ( isset( $bom_category->bom_category ) ) ? $bom_category->bom_category : null;
			$message	  	 = ( isset( $bom_category->message ) )  ? $bom_category->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 	 	= 1;
				$return_data['bom_category']= $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/** Update BOM Category Profile Details **/
	public function update_bom_category( $bom_category_id = false, $page = 'details' ){
		$return_data = [
			'status'=>0
		];
		
		$section 	= ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
		
		$bom_category_id = ( $this->input->post( 'bom_category_id' ) ) ? $this->input->post( 'bom_category_id' ) : ( !empty( $bom_category_id ) ? $bom_category_id : null );
		
		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';	
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$update_category= $this->webapp_service->api_dispatcher( $this->api_end_point.'stock/update_bom_category', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  = ( isset( $update_category->bom_category ) )   ? $update_category->bom_category : null;
			$message	  = ( isset( $update_category->message ) ) ? $update_category->message : 'Oops! There was an error processing your request.';  
			if( !empty( $result ) ){
				$return_data['status'] 		= 1;
				$return_data['category'] 	= $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();	
	}
	
	
	/**
	* Delete BOM Category Record
	**/
	public function delete_bom_category( $bom_category_id = false ){
		$return_data = [
			'status'=>0
		];

		$section 		 = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$bom_category_id = ( $this->input->post( 'bom_category_id' ) ) ? $this->input->post( 'bom_category_id' ) : ( !empty( $bom_category_id ) ? $bom_category_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_delete ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	  		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$delete_category_item 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'stock/delete_bom_category', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  		= ( isset( $delete_category_item->status ) )  ? $delete_category_item->status  : null;
			$message	  		= ( isset( $delete_category_item->message ) ) ? $delete_category_item->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/** Schedule Activities View **/
	public function schedule_activities( $activity_id = false ){

		#Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			$data = [];
			$this->_render_webpage( 'job/schedules/schedule_activities_overview', $data );
		}
	}
	
	/** 
	* Schedule activities lookup / search
	*/
	public function schedule_activities_lookup( $page = 'details' ){
		
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
			
			$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/schedule_activities', $postdata, ['auth_token'=>$this->auth_token], true );

			$schedule_activities			= ( isset( $search_result->schedule_activities ) ) ? $search_result->schedule_activities : null;

			if( !empty( $schedule_activities ) ){

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

				$return_data = $this->load_schedule_activities_view( $schedule_activities );
				
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
	* Prepare Schedule activities view
	*/
	private function load_schedule_activities_view( $schedule_activities_data ){
		$return_data = '';
		if( !empty( $schedule_activities_data ) ){
			foreach( $schedule_activities_data as $k => $activity_details ){
				$return_data .= '<tr>';
					$return_data .= '<td><a href="'.base_url( '/webapp/job/profile/'.$activity_details->job_id).'" >'.$activity_details->activity_name.'</a></td>';
					$return_data .= '<td>'.$activity_details->status.'</td>';
					$return_data .= '<td>'.( date( 'd-m-Y', strtotime( $activity_details->due_date ) ) ).'</td>';
					$return_data .= '<td><a href="'.base_url( '/webapp/job/profile/'.$activity_details->job_id).'" >'.$activity_details->job_id.'</a></td>';
					$return_data .= '<td>'.$activity_details->job_type.'</td>';
					$return_data .= '<td>'.$activity_details->job_status.'</td>';
					$return_data .= '<td>'.( !empty( $activity_details->assignee ) ? $activity_details->assignee : "" ).'</td>';
				$return_data .= '</tr>';
			}

			if( !empty( $pagination ) ){
				$return_data .= '<tr><td colspan="7" style="padding: 0;">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
		}else{
			$return_data .= '<tr><td colspan="7"><br/>'.$this->config->item("no_records").'</td></tr>';
		}
		return $return_data;
	}
	
	
	/** Upload Jobs **/
	public function upload_jobs(){
		if( !$this->identity() ){
			redirect('webapp/user/login', 'refresh');
		}

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			$data['error_message'] = $this->session->flashdata( 'message' );
			$this->_render_webpage( 'job/uploads/upload_jobs', $data );
		}
	}
	
	
	/** Upload Jobs Files **/
	public function submit_upload_jobs_file( $account_id = false ){

		if( !empty( $account_id ) && !empty( $_FILES['upload_file']['name'] ) ){

			$processed_file = $this->document_service->upload_jobs( $account_id );

			if( $processed_file ){
				redirect( '/webapp/job/pending_job_uploads/'.$account_id );
			}

		} else {
			$this->session->set_flashdata( 'message', 'No files were selected' );	
			redirect( '/webapp/job/upload_jobs/' );
		}
	}

	/** Review Uploaded Jobs **/
	public function pending_job_uploads( $account_id = false ){

		if( !empty( $account_id ) ){
			$pending_jobs 			= $this->document_service->get_pending_upload_jobs( $account_id );
			$data['pending_jobs']	= ( !empty( $pending_jobs ) ) ? $pending_jobs : null;
			
			$job_types		 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_types', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['job_types'] 		= ( isset($job_types->job_types) ) ? $job_types->job_types : null;

			$this->_render_webpage('/job/uploads/pending_job_uploads', $data);
		} else {
			redirect( '/webapp/job/upload_jobs/' );
		}

	}
	
	
	/** Process Upload Jobs **/
	public function process_job_uploads( $account_id = false ){

		#$processed 					= '{"jobs_created_successfully":[{"temp_job_id":"1","account_id":"8","contract_name":"BT RETAIL TSG","job_number":null,"client_reference":"360904","salutation":"Mr","customer_last_name":"Smith Jay","address_line1":"52","address_line2":"Love Digital TV Ltd","address_line3":"125 High Street","address_town":"Croydon","address_county":"Surrey","address_postcode":"CR0 9XP","customer_main_telephone":"1818118181","customer_email":"","job_type":"Smoke Detector QTRLY Inspection","job_tracking_status":null,"date_created":null,"job_notes":"Aerial install","access_requirements":"","timestamp":"2020-04-29 07:54:02","upload_status":"Pending","region_id":"2","address_id":"6","job_type_id":"89","checked":"1","postcode_addresses":"[{\"main_address_id\":\"6\",\"addressline1\":\"Advantage Healthcare Group Ltd\",\"addressline2\":\"Grosvenor House, 125 High Street\",\"addressline3\":\"Croydon, CR0 9XP\",\"summaryline\":\"Advantage Healthcare Group Ltd, Grosvenor House, 125 High Street, Croydon, Surrey, CR0 9XP\",\"number\":\"125\",\"premise\":\"Grosvenor House, 125\",\"street\":\"High Street\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 9XP\",\"postcode_nospaces\":\"CR09XP\",\"postcode_sector\":\"CR0 9\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"Advantage Healthcare Group Ltd\",\"buildingname\":\"Grosvenor House\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR09XP.AdvantageHealthcareGroupLtd\",\"associate_key\":null,\"datecreated\":\"2015-05-07 00:32:33\",\"lastmodified\":null,\"activesky\":\"Y\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"7\",\"addressline1\":\"Amicushorizon Ltd\",\"addressline2\":\"Grosvenor House, 125 High Street\",\"addressline3\":\"Croydon, CR0 9XP\",\"summaryline\":\"Amicushorizon Ltd, Grosvenor House, 125 High Street, Croydon, Surrey, CR0 9XP\",\"number\":\"125\",\"premise\":\"Grosvenor House, 125\",\"street\":\"High Street\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 9XP\",\"postcode_nospaces\":\"CR09XP\",\"postcode_sector\":\"CR0 9\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"Amicushorizon Ltd\",\"buildingname\":\"Grosvenor House\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR09XP.AmicushorizonLtd\",\"associate_key\":null,\"datecreated\":\"2015-05-07 00:32:33\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"8\",\"addressline1\":\"Civil Service Pensioners Alliance\",\"addressline2\":\"Grosvenor House, 125 High Street\",\"addressline3\":\"Croydon, CR0 9XP\",\"summaryline\":\"Civil Service Pensioners Alliance, Grosvenor House, 125 High Street, Croydon, Surrey, CR0 9XP\",\"number\":\"125\",\"premise\":\"Grosvenor House, 125\",\"street\":\"High Street\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 9XP\",\"postcode_nospaces\":\"CR09XP\",\"postcode_sector\":\"CR0 9\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"Civil Service Pensioners Alliance\",\"buildingname\":\"Grosvenor House\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR09XP.CivilServicePensionersAlliance\",\"associate_key\":null,\"datecreated\":\"2015-05-07 00:32:33\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"9\",\"addressline1\":\"Country Managing Agents\",\"addressline2\":\"Grosvenor House, 125 High Street\",\"addressline3\":\"Croydon, CR0 9XP\",\"summaryline\":\"Country Managing Agents, Grosvenor House, 125 High Street, Croydon, Surrey, CR0 9XP\",\"number\":\"125\",\"premise\":\"Grosvenor House, 125\",\"street\":\"High Street\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 9XP\",\"postcode_nospaces\":\"CR09XP\",\"postcode_sector\":\"CR0 9\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"Country Managing Agents\",\"buildingname\":\"Grosvenor House\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR09XP.CountryManagingAgents\",\"associate_key\":null,\"datecreated\":\"2015-05-07 00:32:33\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"10\",\"addressline1\":\"Humane Recruitment\",\"addressline2\":\"Grosvenor House, 125 High Street\",\"addressline3\":\"Croydon, CR0 9XP\",\"summaryline\":\"Humane Recruitment, Grosvenor House, 125 High Street, Croydon, Surrey, CR0 9XP\",\"number\":\"125\",\"premise\":\"Grosvenor House, 125\",\"street\":\"High Street\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 9XP\",\"postcode_nospaces\":\"CR09XP\",\"postcode_sector\":\"CR0 9\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"Humane Recruitment\",\"buildingname\":\"Grosvenor House\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR09XP.HumaneRecruitment\",\"associate_key\":null,\"datecreated\":\"2015-05-07 00:32:33\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"11\",\"addressline1\":\"Spectrum Selsdon Ltd\",\"addressline2\":\"Grosvenor House, 125 High Street\",\"addressline3\":\"Croydon, CR0 9XP\",\"summaryline\":\"Spectrum Selsdon Ltd, Grosvenor House, 125 High Street, Croydon, Surrey, CR0 9XP\",\"number\":\"125\",\"premise\":\"Grosvenor House, 125\",\"street\":\"High Street\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 9XP\",\"postcode_nospaces\":\"CR09XP\",\"postcode_sector\":\"CR0 9\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"Spectrum Selsdon Ltd\",\"buildingname\":\"Grosvenor House\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR09XP.SpectrumSelsdonLtd\",\"associate_key\":null,\"datecreated\":\"2015-05-07 00:32:33\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"11001\",\"addressline1\":\"Civil Service Pensioners Alliance\",\"addressline2\":\"Grosvenor House, 125 High Street\",\"addressline3\":\"Croydon, CR0 9XP\",\"summaryline\":\"Civil Service Pensioners Alliance, Grosvenor House, 125 High Street, Croydon, Surrey, CR0 9XP\",\"number\":\"125\",\"premise\":\"Grosvenor House, 125\",\"street\":\"High Street\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 9XP\",\"postcode_nospaces\":\"CR09XP\",\"postcode_sector\":\"CR0 9\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"Civil Service Pensioners Alliance\",\"buildingname\":\"Grosvenor House\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR09XP.CivilServicePensionersAlliance.CivilServicePensionersAlliance.GrosvenorHouse125HighStreet\",\"associate_key\":null,\"datecreated\":\"2018-12-13 12:34:30\",\"lastmodified\":\"2019-05-02 13:49:45\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"11002\",\"addressline1\":\"Country Managing Agents\",\"addressline2\":\"Grosvenor House, 125 High Street\",\"addressline3\":\"Croydon, CR0 9XP\",\"summaryline\":\"Country Managing Agents, Grosvenor House, 125 High Street, Croydon, Surrey, CR0 9XP\",\"number\":\"125\",\"premise\":\"Grosvenor House, 125\",\"street\":\"High Street\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 9XP\",\"postcode_nospaces\":\"CR09XP\",\"postcode_sector\":\"CR0 9\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"Country Managing Agents\",\"buildingname\":\"Grosvenor House\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR09XP.CountryManagingAgents.CountryManagingAgents.GrosvenorHouse125HighStreet\",\"associate_key\":null,\"datecreated\":\"2018-12-13 12:34:30\",\"lastmodified\":\"2019-05-02 13:49:45\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"11003\",\"addressline1\":\"Humane Recruitment\",\"addressline2\":\"Grosvenor House, 125 High Street\",\"addressline3\":\"Croydon, CR0 9XP\",\"summaryline\":\"Humane Recruitment, Grosvenor House, 125 High Street, Croydon, Surrey, CR0 9XP\",\"number\":\"125\",\"premise\":\"Grosvenor House, 125\",\"street\":\"High Street\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 9XP\",\"postcode_nospaces\":\"CR09XP\",\"postcode_sector\":\"CR0 9\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"Humane Recruitment\",\"buildingname\":\"Grosvenor House\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR09XP.HumaneRecruitment.HumaneRecruitment.GrosvenorHouse125HighStreet\",\"associate_key\":null,\"datecreated\":\"2018-12-13 12:34:30\",\"lastmodified\":\"2019-05-02 13:49:45\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"11004\",\"addressline1\":\"I I T A Ltd, Grosvenor House\",\"addressline2\":\"125 High Street\",\"addressline3\":\"Croydon, CR0 9XP\",\"summaryline\":\"I I T A Ltd, Grosvenor House, 125 High Street, Croydon, Surrey, CR0 9XP\",\"number\":\"125\",\"premise\":\"Grosvenor House, 125\",\"street\":\"High Street\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 9XP\",\"postcode_nospaces\":\"CR09XP\",\"postcode_sector\":\"CR0 9\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"I I T A Ltd\",\"buildingname\":\"Grosvenor House\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR09XP.IITALtdGrosvenorHouse.IITALtd,GrosvenorHouse.125HighStreet\",\"associate_key\":null,\"datecreated\":\"2018-12-13 12:34:30\",\"lastmodified\":\"2019-05-02 13:49:45\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"11005\",\"addressline1\":\"Interserve Healthcare Ltd\",\"addressline2\":\"Grosvenor House, 125 High Street\",\"addressline3\":\"Croydon, CR0 9XP\",\"summaryline\":\"Interserve Healthcare Ltd, Grosvenor House, 125 High Street, Croydon, Surrey, CR0 9XP\",\"number\":\"125\",\"premise\":\"Grosvenor House, 125\",\"street\":\"High Street\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 9XP\",\"postcode_nospaces\":\"CR09XP\",\"postcode_sector\":\"CR0 9\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"Interserve Healthcare Ltd\",\"buildingname\":\"Grosvenor House\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR09XP.InterserveHealthcareLtd.InterserveHealthcareLtd.GrosvenorHouse125HighStreet\",\"associate_key\":null,\"datecreated\":\"2018-12-13 12:34:30\",\"lastmodified\":\"2019-05-02 13:49:45\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"11006\",\"addressline1\":\"Love Digital TV Ltd\",\"addressline2\":\"Grosvenor House, 125 High Street\",\"addressline3\":\"Croydon, CR0 9XP\",\"summaryline\":\"Grosvenor House, 125 High Street, Croydon, Surrey, CR0 9XP\",\"number\":\"125\",\"premise\":\"Grosvenor House, 125\",\"street\":\"High Street\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 9XP\",\"postcode_nospaces\":\"CR09XP\",\"postcode_sector\":\"CR0 9\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"Love Digital TV Ltd\",\"buildingname\":\"Grosvenor House\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR09XP.LoveDigitalTVLtd.LoveDigitalTVLtd.GrosvenorHouse125HighStreet\",\"associate_key\":null,\"datecreated\":\"2018-12-13 12:34:30\",\"lastmodified\":\"2019-05-02 13:49:45\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"11007\",\"addressline1\":\"Optivo, Grosvenor House\",\"addressline2\":\"125 High Street\",\"addressline3\":\"Croydon, CR0 9XP\",\"summaryline\":\"Optivo, Grosvenor House, 125 High Street, Croydon, Surrey, CR0 9XP\",\"number\":\"125\",\"premise\":\"Grosvenor House, 125\",\"street\":\"High Street\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 9XP\",\"postcode_nospaces\":\"CR09XP\",\"postcode_sector\":\"CR0 9\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"Optivo\",\"buildingname\":\"Grosvenor House\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR09XP.OptivoGrosvenorHouse.Optivo,GrosvenorHouse.125HighStreet\",\"associate_key\":null,\"datecreated\":\"2018-12-13 12:34:30\",\"lastmodified\":\"2019-05-02 13:49:45\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null}]","suggested_address":false,"job_types":"[{\"job_type_id\":\"89\",\"job_type\":\"Smoke Detector QTRLY inspection\",\"job_type_ref\":\"smokedetectorqtrlyinspection\",\"job_group\":\"Smoke Detector Qtrly Inspection\",\"job_type_desc\":\"Use this for smoke detector inspections.\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"175\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-01-21 07:00:39\",\"created_by\":\"156\",\"last_modified\":\"2020-04-27 11:20:19\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"93\",\"job_type\":\"Automatic Opening Vents Inspection & Servicing\",\"job_type_ref\":\"automaticopeningventsinspection&servicing\",\"job_group\":\"Automatic Opening Vents Inspection & Servicing\",\"job_type_desc\":\"use this when inspecting an AOV\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"70.50\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"182\",\"contract_id\":\"23\",\"stock_required\":\"1\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"1\",\"date_created\":\"2020-03-09 14:54:50\",\"created_by\":\"150\",\"last_modified\":\"2020-04-28 15:33:31\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"95\",\"job_type\":\"Emergency Lighting monthly inspection & servicing\",\"job_type_ref\":\"emergencylightingmonthlyinspection&servicing\",\"job_group\":\"Emergency Lighting Monthly Inspection & Servicing\",\"job_type_desc\":\"use this for monthly inspection of emergency lights\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"186\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-03-11 12:08:15\",\"created_by\":\"150\",\"last_modified\":\"2020-04-27 11:22:27\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"96\",\"job_type\":\"Test Job Type\",\"job_type_ref\":\"testjobtype\",\"job_group\":\"Test Job Type\",\"job_type_desc\":\"Test Job Type\",\"job_type_subtype\":\"Service Call\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"2\",\"base_priority_rating\":\"100\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"0\",\"evidoc_type_id\":\"0\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-04-28 16:28:05\",\"created_by\":\"1\",\"last_modified\":\"2020-04-28 16:28:21\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"}]","contract_id":"23","main_address_id":"6","is_uploaded":1,"uploaded_record":1,"customer_id":43},{"temp_job_id":"2","account_id":"8","contract_name":"BT RETAIL TSG","job_number":null,"client_reference":"361474","salutation":"Ms","customer_last_name":"Veiga Tania","address_line1":"2","address_line2":"Acacia Avenue","address_line3":"Patcham","address_town":"Brighton","address_county":"Sussex","address_postcode":"BA3 2AW","customer_main_telephone":"1818118181","customer_email":"","job_type":"Automatic Opening Vents Inspection & Servicing","job_tracking_status":null,"date_created":null,"job_notes":"Aerial install","access_requirements":"","timestamp":"2020-04-29 07:54:02","upload_status":"Pending","region_id":false,"address_id":"912","job_type_id":"93","checked":"1","postcode_addresses":"[{\"main_address_id\":\"912\",\"addressline1\":\"Brynlyn, Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Brynlyn, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Brynlyn\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Brynlyn\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.BrynlynRadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:06\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"913\",\"addressline1\":\"Hawksmoor, Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Hawksmoor, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Hawksmoor\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Hawksmoor\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.HawksmoorRadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:06\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"914\",\"addressline1\":\"Lammas, Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Lammas, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Lammas\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Lammas\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.LammasRadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:06\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"915\",\"addressline1\":\"Lissett, Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Lissett, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Lissett\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Lissett\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.LissettRadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:06\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"916\",\"addressline1\":\"Lynden, Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Lynden, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Lynden\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Lynden\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.LyndenRadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:06\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"917\",\"addressline1\":\"Priory Nursery, Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Priory Nursery, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Priory Nursery\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Priory Nursery\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.PrioryNurseryRadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:06\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"918\",\"addressline1\":\"Quesnel, Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Quesnel, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Quesnel\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Quesnel\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.QuesnelRadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:06\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"919\",\"addressline1\":\"Romark, Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Romark, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Romark\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Romark\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.RomarkRadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:06\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"920\",\"addressline1\":\"Somer View, Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Somer View, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Somer View\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Somer View\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.SomerViewRadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:06\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"921\",\"addressline1\":\"St. Elmo, Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"St. Elmo, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"St. Elmo\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"St. Elmo\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.StElmoRadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:06\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"922\",\"addressline1\":\"The Gable, Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"The Gable, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"The Gable\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"The Gable\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.TheGableRadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:06\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"923\",\"addressline1\":\"Walton, Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Walton, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Walton\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Walton\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.WaltonRadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:06\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"924\",\"addressline1\":\"Winter Cottage, Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Winter Cottage, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Winter Cottage\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Winter Cottage\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.WinterCottageRadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:06\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"925\",\"addressline1\":\"Flat 1, Wishford Mews\",\"addressline2\":\"Radstock Road, Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Flat 1, Wishford Mews, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Flat 1, Wishford Mews\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Wishford Mews\",\"subbuildingname\":\"Flat 1\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.Flat1WishfordMews\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:06\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"926\",\"addressline1\":\"Flat 2, Wishford Mews\",\"addressline2\":\"Radstock Road, Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Flat 2, Wishford Mews, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Flat 2, Wishford Mews\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Wishford Mews\",\"subbuildingname\":\"Flat 2\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.Flat2WishfordMews\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:06\",\"lastmodified\":null,\"activesky\":\"Y\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"927\",\"addressline1\":\"Flat 3, Wishford Mews\",\"addressline2\":\"Radstock Road, Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Flat 3, Wishford Mews, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Flat 3, Wishford Mews\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Wishford Mews\",\"subbuildingname\":\"Flat 3\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.Flat3WishfordMews\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:06\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"928\",\"addressline1\":\"Flat 4, Wishford Mews\",\"addressline2\":\"Radstock Road, Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Flat 4, Wishford Mews, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Flat 4, Wishford Mews\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Wishford Mews\",\"subbuildingname\":\"Flat 4\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.Flat4WishfordMews\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"929\",\"addressline1\":\"Flat 5, Wishford Mews\",\"addressline2\":\"Radstock Road, Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Flat 5, Wishford Mews, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Flat 5, Wishford Mews\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Wishford Mews\",\"subbuildingname\":\"Flat 5\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.Flat5WishfordMews\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"930\",\"addressline1\":\"Flat 6, Wishford Mews\",\"addressline2\":\"Radstock Road, Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Flat 6, Wishford Mews, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Flat 6, Wishford Mews\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Wishford Mews\",\"subbuildingname\":\"Flat 6\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.Flat6WishfordMews\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"931\",\"addressline1\":\"Flat 7, Wishford Mews\",\"addressline2\":\"Radstock Road, Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Flat 7, Wishford Mews, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Flat 7, Wishford Mews\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Wishford Mews\",\"subbuildingname\":\"Flat 7\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.Flat7WishfordMews\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"932\",\"addressline1\":\"Flat 8, Wishford Mews\",\"addressline2\":\"Radstock Road, Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Flat 8, Wishford Mews, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Flat 8, Wishford Mews\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Wishford Mews\",\"subbuildingname\":\"Flat 8\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.Flat8WishfordMews\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"933\",\"addressline1\":\"Flat 9, Wishford Mews\",\"addressline2\":\"Radstock Road, Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Flat 9, Wishford Mews, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Flat 9, Wishford Mews\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Wishford Mews\",\"subbuildingname\":\"Flat 9\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.Flat9WishfordMews\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"934\",\"addressline1\":\"Flat 10, Wishford Mews\",\"addressline2\":\"Radstock Road, Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Flat 10, Wishford Mews, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Flat 10, Wishford Mews\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Wishford Mews\",\"subbuildingname\":\"Flat 10\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.Flat10WishfordMews\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"Y\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"935\",\"addressline1\":\"Flat 11, Wishford Mews\",\"addressline2\":\"Radstock Road, Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Flat 11, Wishford Mews, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Flat 11, Wishford Mews\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Wishford Mews\",\"subbuildingname\":\"Flat 11\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.Flat11WishfordMews\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"Y\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"936\",\"addressline1\":\"Flat 12, Wishford Mews\",\"addressline2\":\"Radstock Road, Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Flat 12, Wishford Mews, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Flat 12, Wishford Mews\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Wishford Mews\",\"subbuildingname\":\"Flat 12\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.Flat12WishfordMews\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"937\",\"addressline1\":\"Flat 13, Wishford Mews\",\"addressline2\":\"Radstock Road, Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Flat 13, Wishford Mews, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Flat 13, Wishford Mews\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Wishford Mews\",\"subbuildingname\":\"Flat 13\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.Flat13WishfordMews\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"938\",\"addressline1\":\"Flat 14, Wishford Mews\",\"addressline2\":\"Radstock Road, Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"Flat 14, Wishford Mews, Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"\",\"premise\":\"Flat 14, Wishford Mews\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"Wishford Mews\",\"subbuildingname\":\"Flat 14\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.Flat14WishfordMews\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"939\",\"addressline1\":\"46 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"46 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"46\",\"premise\":\"46\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.46RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"940\",\"addressline1\":\"46A Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"46A Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"46A\",\"premise\":\"46A\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.46ARadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"941\",\"addressline1\":\"47 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"47 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"47\",\"premise\":\"47\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.47RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"942\",\"addressline1\":\"48 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"48 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"48\",\"premise\":\"48\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.48RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"943\",\"addressline1\":\"49 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"49 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"49\",\"premise\":\"49\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.49RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"944\",\"addressline1\":\"50 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"50 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"50\",\"premise\":\"50\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.50RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"945\",\"addressline1\":\"51 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"51 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"51\",\"premise\":\"51\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.51RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"946\",\"addressline1\":\"52 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"52 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"52\",\"premise\":\"52\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.52RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"947\",\"addressline1\":\"53 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"53 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"53\",\"premise\":\"53\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.53RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"948\",\"addressline1\":\"54 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"54 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"54\",\"premise\":\"54\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.54RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"949\",\"addressline1\":\"55 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"55 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"55\",\"premise\":\"55\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.55RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"950\",\"addressline1\":\"56 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"56 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"56\",\"premise\":\"56\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.56RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"951\",\"addressline1\":\"57 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"57 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"57\",\"premise\":\"57\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.57RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"952\",\"addressline1\":\"58 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"58 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"58\",\"premise\":\"58\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.58RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"953\",\"addressline1\":\"59 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"59 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"59\",\"premise\":\"59\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.59RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"954\",\"addressline1\":\"60 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"60 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"60\",\"premise\":\"60\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.60RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"955\",\"addressline1\":\"61 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"61 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"61\",\"premise\":\"61\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.61RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"956\",\"addressline1\":\"63 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"63 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"63\",\"premise\":\"63\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.63RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"957\",\"addressline1\":\"64 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"64 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"64\",\"premise\":\"64\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.64RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"958\",\"addressline1\":\"65 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"65 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"65\",\"premise\":\"65\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.65RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"959\",\"addressline1\":\"66 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"66 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"66\",\"premise\":\"66\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.66RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"960\",\"addressline1\":\"67 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"67 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"67\",\"premise\":\"67\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.67RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"961\",\"addressline1\":\"68 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"68 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"68\",\"premise\":\"68\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.68RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"962\",\"addressline1\":\"69 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"69 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"69\",\"premise\":\"69\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.69RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"963\",\"addressline1\":\"70 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"70 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"70\",\"premise\":\"70\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.70RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"964\",\"addressline1\":\"71 Radstock Road\",\"addressline2\":\"Midsomer Norton\",\"addressline3\":\"Radstock, BA3 2AW\",\"summaryline\":\"71 Radstock Road, Midsomer Norton, Radstock, Somerset, BA3 2AW\",\"number\":\"71\",\"premise\":\"71\",\"street\":\"Radstock Road\",\"posttown\":\"Radstock\",\"county\":\"Somerset\",\"postcode\":\"BA3 2AW\",\"postcode_nospaces\":\"BA32AW\",\"postcode_sector\":\"BA3 2\",\"postcode_district\":\"BA3\",\"postcode_area\":\"BA\",\"postcode_qi\":\"3\",\"xcoords\":\"0\",\"ycoords\":\"0\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"Midsomer Norton\",\"uniquereference\":\"BA32AW.71RadstockRoad\",\"associate_key\":null,\"datecreated\":\"2015-05-22 14:27:07\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null}]","suggested_address":false,"job_types":"[{\"job_type_id\":\"89\",\"job_type\":\"Smoke Detector QTRLY inspection\",\"job_type_ref\":\"smokedetectorqtrlyinspection\",\"job_group\":\"Smoke Detector Qtrly Inspection\",\"job_type_desc\":\"Use this for smoke detector inspections.\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"175\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-01-21 07:00:39\",\"created_by\":\"156\",\"last_modified\":\"2020-04-27 11:20:19\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"93\",\"job_type\":\"Automatic Opening Vents Inspection & Servicing\",\"job_type_ref\":\"automaticopeningventsinspection&servicing\",\"job_group\":\"Automatic Opening Vents Inspection & Servicing\",\"job_type_desc\":\"use this when inspecting an AOV\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"70.50\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"182\",\"contract_id\":\"23\",\"stock_required\":\"1\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"1\",\"date_created\":\"2020-03-09 14:54:50\",\"created_by\":\"150\",\"last_modified\":\"2020-04-28 15:33:31\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"95\",\"job_type\":\"Emergency Lighting monthly inspection & servicing\",\"job_type_ref\":\"emergencylightingmonthlyinspection&servicing\",\"job_group\":\"Emergency Lighting Monthly Inspection & Servicing\",\"job_type_desc\":\"use this for monthly inspection of emergency lights\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"186\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-03-11 12:08:15\",\"created_by\":\"150\",\"last_modified\":\"2020-04-27 11:22:27\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"96\",\"job_type\":\"Test Job Type\",\"job_type_ref\":\"testjobtype\",\"job_group\":\"Test Job Type\",\"job_type_desc\":\"Test Job Type\",\"job_type_subtype\":\"Service Call\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"2\",\"base_priority_rating\":\"100\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"0\",\"evidoc_type_id\":\"0\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-04-28 16:28:05\",\"created_by\":\"1\",\"last_modified\":\"2020-04-28 16:28:21\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"}]","contract_id":"23","main_address_id":"912","is_uploaded":1,"uploaded_record":1,"customer_id":44},{"temp_job_id":"3","account_id":"8","contract_name":"BT RETAIL TSG","job_number":null,"client_reference":"362309","salutation":"Mrs","customer_last_name":"Saucy Dana","address_line1":"3","address_line2":"Acacia Avenue","address_line3":"Patcham","address_town":"Brighton","address_county":"Sussex","address_postcode":"CM19 5FF","customer_main_telephone":"1818118181","customer_email":"","job_type":"Smoke Detector QTRLY Inspection","job_tracking_status":null,"date_created":null,"job_notes":"Aerial install","access_requirements":"","timestamp":"2020-04-29 07:54:02","upload_status":"Pending","region_id":false,"address_id":"11376","job_type_id":"89","checked":"1","postcode_addresses":"[{\"main_address_id\":\"11376\",\"addressline1\":\"1 Caravan Site\",\"addressline2\":\"Elizabeth Way\",\"addressline3\":\"Harlow, CM19 5FF\",\"summaryline\":\"1 Caravan Site, Elizabeth Way, Harlow, Essex, CM19 5FF\",\"number\":\"\",\"premise\":\"1 Caravan Site\",\"street\":\"Elizabeth Way\",\"posttown\":\"Harlow\",\"county\":\"Essex\",\"postcode\":\"CM19 5FF\",\"postcode_nospaces\":\"CM195FF\",\"postcode_sector\":\"B69 4\",\"postcode_district\":\"B69\",\"postcode_area\":\"B\",\"postcode_qi\":\"69\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"1 Caravan Site\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CM195FF.1CaravanSite.1CaravanSite.ElizabethWay\",\"associate_key\":null,\"datecreated\":\"2019-04-09 13:26:44\",\"lastmodified\":\"2019-04-09 13:50:54\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"11377\",\"addressline1\":\"2 Caravan Site\",\"addressline2\":\"Elizabeth Way\",\"addressline3\":\"Harlow, CM19 5FF\",\"summaryline\":\"2 Caravan Site, Elizabeth Way, Harlow, Essex, CM19 5FF\",\"number\":\"\",\"premise\":\"2 Caravan Site\",\"street\":\"Elizabeth Way\",\"posttown\":\"Harlow\",\"county\":\"Essex\",\"postcode\":\"CM19 5FF\",\"postcode_nospaces\":\"CM195FF\",\"postcode_sector\":\"B69 4\",\"postcode_district\":\"B69\",\"postcode_area\":\"B\",\"postcode_qi\":\"69\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"2 Caravan Site\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CM195FF.2CaravanSite.2CaravanSite.ElizabethWay\",\"associate_key\":null,\"datecreated\":\"2019-04-09 13:26:44\",\"lastmodified\":\"2019-04-09 13:50:54\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"11378\",\"addressline1\":\"3 Caravan Site\",\"addressline2\":\"Elizabeth Way\",\"addressline3\":\"Harlow, CM19 5FF\",\"summaryline\":\"3 Caravan Site, Elizabeth Way, Harlow, Essex, CM19 5FF\",\"number\":\"\",\"premise\":\"3 Caravan Site\",\"street\":\"Elizabeth Way\",\"posttown\":\"Harlow\",\"county\":\"Essex\",\"postcode\":\"CM19 5FF\",\"postcode_nospaces\":\"CM195FF\",\"postcode_sector\":\"B69 4\",\"postcode_district\":\"B69\",\"postcode_area\":\"B\",\"postcode_qi\":\"69\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"3 Caravan Site\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CM195FF.3CaravanSite.3CaravanSite.ElizabethWay\",\"associate_key\":null,\"datecreated\":\"2019-04-09 13:26:44\",\"lastmodified\":\"2019-04-09 13:50:54\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"11379\",\"addressline1\":\"4 Caravan Site\",\"addressline2\":\"Elizabeth Way\",\"addressline3\":\"Harlow, CM19 5FF\",\"summaryline\":\"4 Caravan Site, Elizabeth Way, Harlow, Essex, CM19 5FF\",\"number\":\"\",\"premise\":\"4 Caravan Site\",\"street\":\"Elizabeth Way\",\"posttown\":\"Harlow\",\"county\":\"Essex\",\"postcode\":\"CM19 5FF\",\"postcode_nospaces\":\"CM195FF\",\"postcode_sector\":\"B69 4\",\"postcode_district\":\"B69\",\"postcode_area\":\"B\",\"postcode_qi\":\"69\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"4 Caravan Site\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CM195FF.4CaravanSite.4CaravanSite.ElizabethWay\",\"associate_key\":null,\"datecreated\":\"2019-04-09 13:26:44\",\"lastmodified\":\"2019-04-09 13:50:54\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null}]","suggested_address":false,"job_types":"[{\"job_type_id\":\"89\",\"job_type\":\"Smoke Detector QTRLY inspection\",\"job_type_ref\":\"smokedetectorqtrlyinspection\",\"job_group\":\"Smoke Detector Qtrly Inspection\",\"job_type_desc\":\"Use this for smoke detector inspections.\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"175\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-01-21 07:00:39\",\"created_by\":\"156\",\"last_modified\":\"2020-04-27 11:20:19\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"93\",\"job_type\":\"Automatic Opening Vents Inspection & Servicing\",\"job_type_ref\":\"automaticopeningventsinspection&servicing\",\"job_group\":\"Automatic Opening Vents Inspection & Servicing\",\"job_type_desc\":\"use this when inspecting an AOV\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"70.50\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"182\",\"contract_id\":\"23\",\"stock_required\":\"1\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"1\",\"date_created\":\"2020-03-09 14:54:50\",\"created_by\":\"150\",\"last_modified\":\"2020-04-28 15:33:31\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"95\",\"job_type\":\"Emergency Lighting monthly inspection & servicing\",\"job_type_ref\":\"emergencylightingmonthlyinspection&servicing\",\"job_group\":\"Emergency Lighting Monthly Inspection & Servicing\",\"job_type_desc\":\"use this for monthly inspection of emergency lights\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"186\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-03-11 12:08:15\",\"created_by\":\"150\",\"last_modified\":\"2020-04-27 11:22:27\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"96\",\"job_type\":\"Test Job Type\",\"job_type_ref\":\"testjobtype\",\"job_group\":\"Test Job Type\",\"job_type_desc\":\"Test Job Type\",\"job_type_subtype\":\"Service Call\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"2\",\"base_priority_rating\":\"100\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"0\",\"evidoc_type_id\":\"0\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-04-28 16:28:05\",\"created_by\":\"1\",\"last_modified\":\"2020-04-28 16:28:21\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"}]","contract_id":"23","main_address_id":"11376","is_uploaded":1,"uploaded_record":1,"customer_id":45},{"temp_job_id":"7","account_id":"8","contract_name":"BT RETAIL TSG","job_number":null,"client_reference":"362768","salutation":"Mr","customer_last_name":"Bush Brian","address_line1":"7","address_line2":"Acacia Avenue","address_line3":"Patcham","address_town":"Brighton","address_county":"Sussex","address_postcode":"CR0 2HP","customer_main_telephone":"1818118181","customer_email":"","job_type":"Emergency Lighting Monthly Inspection & Servicing","job_tracking_status":null,"date_created":null,"job_notes":"Aerial install","access_requirements":"","timestamp":"2020-04-29 07:54:02","upload_status":"Pending","region_id":false,"address_id":"27322","job_type_id":"89","checked":"1","postcode_addresses":"[{\"main_address_id\":\"27322\",\"addressline1\":\"49 The Crescent\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2HP\",\"summaryline\":\"49 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"49\",\"premise\":\"49\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"G15 7\",\"postcode_district\":\"G15\",\"postcode_area\":\"G\",\"postcode_qi\":\"15\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.49TheCrescent.49TheCrescent.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":\"2020-03-24 16:18:49\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"27323\",\"addressline1\":\"51 The Crescent\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2HP\",\"summaryline\":\"51 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"51\",\"premise\":\"51\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"G15 7\",\"postcode_district\":\"G15\",\"postcode_area\":\"G\",\"postcode_qi\":\"15\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.51TheCrescent.51TheCrescent.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":\"2020-03-24 16:18:49\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"27324\",\"addressline1\":\"53 The Crescent\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2HP\",\"summaryline\":\"53 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"53\",\"premise\":\"53\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"G15 7\",\"postcode_district\":\"G15\",\"postcode_area\":\"G\",\"postcode_qi\":\"15\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.53TheCrescent.53TheCrescent.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":\"2020-03-24 16:18:49\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"27325\",\"addressline1\":\"55 The Crescent\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2HP\",\"summaryline\":\"55 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"55\",\"premise\":\"55\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"G15 7\",\"postcode_district\":\"G15\",\"postcode_area\":\"G\",\"postcode_qi\":\"15\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.55TheCrescent.55TheCrescent.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":\"2020-03-24 16:18:49\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"27326\",\"addressline1\":\"Flat 1\",\"addressline2\":\"57 The Crescent\",\"addressline3\":\"Croydon, CR0 2HP\",\"summaryline\":\"Flat 1, 57 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"57\",\"premise\":\"Flat 1, 57\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 1\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.Flat1.Flat1.57TheCrescent\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"27327\",\"addressline1\":\"Flat 2\",\"addressline2\":\"57 The Crescent\",\"addressline3\":\"Croydon, CR0 2HP\",\"summaryline\":\"Flat 2, 57 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"57\",\"premise\":\"Flat 2, 57\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 2\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.Flat2.Flat2.57TheCrescent\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"27328\",\"addressline1\":\"Flat 3\",\"addressline2\":\"57 The Crescent\",\"addressline3\":\"Croydon, CR0 2HP\",\"summaryline\":\"Flat 3, 57 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"57\",\"premise\":\"Flat 3, 57\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 3\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.Flat3.Flat3.57TheCrescent\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"27329\",\"addressline1\":\"Flat 4\",\"addressline2\":\"57 The Crescent\",\"addressline3\":\"Croydon, CR0 2HP\",\"summaryline\":\"Flat 4, 57 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"57\",\"premise\":\"Flat 4, 57\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 4\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.Flat4.Flat4.57TheCrescent\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"27330\",\"addressline1\":\"Flat 5\",\"addressline2\":\"57 The Crescent\",\"addressline3\":\"Croydon, CR0 2HP\",\"summaryline\":\"Flat 5, 57 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"57\",\"premise\":\"Flat 5, 57\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 5\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.Flat5.Flat5.57TheCrescent\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"27331\",\"addressline1\":\"Flat 6\",\"addressline2\":\"57 The Crescent\",\"addressline3\":\"Croydon, CR0 2HP\",\"summaryline\":\"Flat 6, 57 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"57\",\"premise\":\"Flat 6, 57\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 6\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.Flat6.Flat6.57TheCrescent\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"27332\",\"addressline1\":\"Flat 7\",\"addressline2\":\"57 The Crescent\",\"addressline3\":\"Croydon, CR0 2HP\",\"summaryline\":\"Flat 7, 57 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"57\",\"premise\":\"Flat 7, 57\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 7\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.Flat7.Flat7.57TheCrescent\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"27333\",\"addressline1\":\"Flat 8\",\"addressline2\":\"57 The Crescent\",\"addressline3\":\"Croydon, CR0 2HP\",\"summaryline\":\"Flat 8, 57 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"57\",\"premise\":\"Flat 8, 57\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 8\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.Flat8.Flat8.57TheCrescent\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"27334\",\"addressline1\":\"59 The Crescent\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2HP\",\"summaryline\":\"59 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"59\",\"premise\":\"59\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"G15 7\",\"postcode_district\":\"G15\",\"postcode_area\":\"G\",\"postcode_qi\":\"15\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.59TheCrescent.59TheCrescent.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":\"2020-03-24 16:18:49\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"27335\",\"addressline1\":\"61 The Crescent\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2HP\",\"summaryline\":\"61 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"61\",\"premise\":\"61\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"G15 7\",\"postcode_district\":\"G15\",\"postcode_area\":\"G\",\"postcode_qi\":\"15\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.61TheCrescent.61TheCrescent.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":\"2020-03-24 16:18:49\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"27336\",\"addressline1\":\"63 The Crescent\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2HP\",\"summaryline\":\"63 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"63\",\"premise\":\"63\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"G15 7\",\"postcode_district\":\"G15\",\"postcode_area\":\"G\",\"postcode_qi\":\"15\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.63TheCrescent.63TheCrescent.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":\"2020-03-24 16:18:49\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"27337\",\"addressline1\":\"67 The Crescent\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2HP\",\"summaryline\":\"67 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"67\",\"premise\":\"67\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"G15 7\",\"postcode_district\":\"G15\",\"postcode_area\":\"G\",\"postcode_qi\":\"15\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.67TheCrescent.67TheCrescent.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":\"2020-03-24 16:18:49\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"27338\",\"addressline1\":\"69 The Crescent\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2HP\",\"summaryline\":\"69 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"69\",\"premise\":\"69\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"G15 7\",\"postcode_district\":\"G15\",\"postcode_area\":\"G\",\"postcode_qi\":\"15\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.69TheCrescent.69TheCrescent.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":\"2020-03-24 16:18:49\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"27339\",\"addressline1\":\"71 The Crescent\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2HP\",\"summaryline\":\"71 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"71\",\"premise\":\"71\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"G15 7\",\"postcode_district\":\"G15\",\"postcode_area\":\"G\",\"postcode_qi\":\"15\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.71TheCrescent.71TheCrescent.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":\"2020-03-24 16:18:49\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"27340\",\"addressline1\":\"73 The Crescent\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2HP\",\"summaryline\":\"73 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"73\",\"premise\":\"73\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"G15 7\",\"postcode_district\":\"G15\",\"postcode_area\":\"G\",\"postcode_qi\":\"15\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.73TheCrescent.73TheCrescent.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":\"2020-03-24 16:18:49\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"27341\",\"addressline1\":\"75 The Crescent\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2HP\",\"summaryline\":\"75 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"75\",\"premise\":\"75\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"G15 7\",\"postcode_district\":\"G15\",\"postcode_area\":\"G\",\"postcode_qi\":\"15\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.75TheCrescent.75TheCrescent.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":\"2020-03-24 16:18:49\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"27342\",\"addressline1\":\"77 The Crescent\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2HP\",\"summaryline\":\"77 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"77\",\"premise\":\"77\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"G15 7\",\"postcode_district\":\"G15\",\"postcode_area\":\"G\",\"postcode_qi\":\"15\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.77TheCrescent.77TheCrescent.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":\"2020-03-24 16:18:49\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"27343\",\"addressline1\":\"79 The Crescent\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2HP\",\"summaryline\":\"79 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"79\",\"premise\":\"79\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"G15 7\",\"postcode_district\":\"G15\",\"postcode_area\":\"G\",\"postcode_qi\":\"15\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.79TheCrescent.79TheCrescent.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":\"2020-03-24 16:18:49\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"27344\",\"addressline1\":\"81 The Crescent\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2HP\",\"summaryline\":\"81 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"81\",\"premise\":\"81\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"G15 7\",\"postcode_district\":\"G15\",\"postcode_area\":\"G\",\"postcode_qi\":\"15\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.81TheCrescent.81TheCrescent.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":\"2020-03-24 16:18:49\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"27345\",\"addressline1\":\"83 The Crescent\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2HP\",\"summaryline\":\"83 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"83\",\"premise\":\"83\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"G15 7\",\"postcode_district\":\"G15\",\"postcode_area\":\"G\",\"postcode_qi\":\"15\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.83TheCrescent.83TheCrescent.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":\"2020-03-24 16:18:49\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"27346\",\"addressline1\":\"85 The Crescent\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2HP\",\"summaryline\":\"85 The Crescent, Croydon, Surrey, CR0 2HP\",\"number\":\"85\",\"premise\":\"85\",\"street\":\"The Crescent\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2HP\",\"postcode_nospaces\":\"CR02HP\",\"postcode_sector\":\"G15 7\",\"postcode_district\":\"G15\",\"postcode_area\":\"G\",\"postcode_qi\":\"15\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02HP.85TheCrescent.85TheCrescent.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-10-16 11:57:42\",\"lastmodified\":\"2020-03-24 16:18:49\",\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null}]","suggested_address":false,"job_types":"[{\"job_type_id\":\"89\",\"job_type\":\"Smoke Detector QTRLY inspection\",\"job_type_ref\":\"smokedetectorqtrlyinspection\",\"job_group\":\"Smoke Detector Qtrly Inspection\",\"job_type_desc\":\"Use this for smoke detector inspections.\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"175\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-01-21 07:00:39\",\"created_by\":\"156\",\"last_modified\":\"2020-04-27 11:20:19\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"93\",\"job_type\":\"Automatic Opening Vents Inspection & Servicing\",\"job_type_ref\":\"automaticopeningventsinspection&servicing\",\"job_group\":\"Automatic Opening Vents Inspection & Servicing\",\"job_type_desc\":\"use this when inspecting an AOV\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"70.50\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"182\",\"contract_id\":\"23\",\"stock_required\":\"1\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"1\",\"date_created\":\"2020-03-09 14:54:50\",\"created_by\":\"150\",\"last_modified\":\"2020-04-28 15:33:31\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"95\",\"job_type\":\"Emergency Lighting monthly inspection & servicing\",\"job_type_ref\":\"emergencylightingmonthlyinspection&servicing\",\"job_group\":\"Emergency Lighting Monthly Inspection & Servicing\",\"job_type_desc\":\"use this for monthly inspection of emergency lights\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"186\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-03-11 12:08:15\",\"created_by\":\"150\",\"last_modified\":\"2020-04-27 11:22:27\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"96\",\"job_type\":\"Test Job Type\",\"job_type_ref\":\"testjobtype\",\"job_group\":\"Test Job Type\",\"job_type_desc\":\"Test Job Type\",\"job_type_subtype\":\"Service Call\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"2\",\"base_priority_rating\":\"100\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"0\",\"evidoc_type_id\":\"0\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-04-28 16:28:05\",\"created_by\":\"1\",\"last_modified\":\"2020-04-28 16:28:21\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"}]","contract_id":"23","main_address_id":"27322","is_uploaded":1,"uploaded_record":1,"customer_id":47},{"temp_job_id":"8","account_id":"8","contract_name":"BT RETAIL TSG","job_number":null,"client_reference":"362806","salutation":"Ms","customer_last_name":"King Christine","address_line1":"8","address_line2":"Acacia Avenue","address_line3":"Patcham","address_town":"Brighton","address_county":"Sussex","address_postcode":"CR0 2JB","customer_main_telephone":"1818118181","customer_email":"","job_type":"Automatic Opening Vents Inspection & Servicing","job_tracking_status":null,"date_created":null,"job_notes":"Aerial install","access_requirements":"","timestamp":"2020-04-29 07:54:02","upload_status":"Pending","region_id":"2","address_id":"31916","job_type_id":"89","checked":"1","postcode_addresses":"[{\"main_address_id\":\"31916\",\"addressline1\":\"30 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"30 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"30\",\"premise\":\"30\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.30WhitehorseRoad.30WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31917\",\"addressline1\":\"32 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"32 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"32\",\"premise\":\"32\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.32WhitehorseRoad.32WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31918\",\"addressline1\":\"34 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"34 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"34\",\"premise\":\"34\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.34WhitehorseRoad.34WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31919\",\"addressline1\":\"36 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"36 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"36\",\"premise\":\"36\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.36WhitehorseRoad.36WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31920\",\"addressline1\":\"38 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"38 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"38\",\"premise\":\"38\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.38WhitehorseRoad.38WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31921\",\"addressline1\":\"40 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"40 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"40\",\"premise\":\"40\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.40WhitehorseRoad.40WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31922\",\"addressline1\":\"42 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"42 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"42\",\"premise\":\"42\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.42WhitehorseRoad.42WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31923\",\"addressline1\":\"44 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"44 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"44\",\"premise\":\"44\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.44WhitehorseRoad.44WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31924\",\"addressline1\":\"46 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"46 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"46\",\"premise\":\"46\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.46WhitehorseRoad.46WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31925\",\"addressline1\":\"48 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"48 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"48\",\"premise\":\"48\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.48WhitehorseRoad.48WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31926\",\"addressline1\":\"50 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"50 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"50\",\"premise\":\"50\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.50WhitehorseRoad.50WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31927\",\"addressline1\":\"52 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"52 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"52\",\"premise\":\"52\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.52WhitehorseRoad.52WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31928\",\"addressline1\":\"54 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"54 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"54\",\"premise\":\"54\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.54WhitehorseRoad.54WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31929\",\"addressline1\":\"56 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"56 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"56\",\"premise\":\"56\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.56WhitehorseRoad.56WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31930\",\"addressline1\":\"58 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"58 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"58\",\"premise\":\"58\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.58WhitehorseRoad.58WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31931\",\"addressline1\":\"60 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"60 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"60\",\"premise\":\"60\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.60WhitehorseRoad.60WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31932\",\"addressline1\":\"62 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"62 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"62\",\"premise\":\"62\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.62WhitehorseRoad.62WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31933\",\"addressline1\":\"64 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"64 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"64\",\"premise\":\"64\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.64WhitehorseRoad.64WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31934\",\"addressline1\":\"66 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"66 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"66\",\"premise\":\"66\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.66WhitehorseRoad.66WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31935\",\"addressline1\":\"68 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"68 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"68\",\"premise\":\"68\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.68WhitehorseRoad.68WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31936\",\"addressline1\":\"70 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"70 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"70\",\"premise\":\"70\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.70WhitehorseRoad.70WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31937\",\"addressline1\":\"72 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"72 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"72\",\"premise\":\"72\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.72WhitehorseRoad.72WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31938\",\"addressline1\":\"74 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"74 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"74\",\"premise\":\"74\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.74WhitehorseRoad.74WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31939\",\"addressline1\":\"76 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"76 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"76\",\"premise\":\"76\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.76WhitehorseRoad.76WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31940\",\"addressline1\":\"78 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"78 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"78\",\"premise\":\"78\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.78WhitehorseRoad.78WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31941\",\"addressline1\":\"80 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"80 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"80\",\"premise\":\"80\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.80WhitehorseRoad.80WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31942\",\"addressline1\":\"82 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"82 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"82\",\"premise\":\"82\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.82WhitehorseRoad.82WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31943\",\"addressline1\":\"84 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"84 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"84\",\"premise\":\"84\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.84WhitehorseRoad.84WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31944\",\"addressline1\":\"86 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"86 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"86\",\"premise\":\"86\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.86WhitehorseRoad.86WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31945\",\"addressline1\":\"88 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"88 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"88\",\"premise\":\"88\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.88WhitehorseRoad.88WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31946\",\"addressline1\":\"90 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"90 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"90\",\"premise\":\"90\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.90WhitehorseRoad.90WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31947\",\"addressline1\":\"92 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"92 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"92\",\"premise\":\"92\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.92WhitehorseRoad.92WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31948\",\"addressline1\":\"94 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"94 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"94\",\"premise\":\"94\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.94WhitehorseRoad.94WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31949\",\"addressline1\":\"96 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"96 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"96\",\"premise\":\"96\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.96WhitehorseRoad.96WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31950\",\"addressline1\":\"96A Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"96A Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"96A\",\"premise\":\"96A\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.96AWhitehorseRoad.96AWhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31951\",\"addressline1\":\"98 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"98 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"98\",\"premise\":\"98\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.98WhitehorseRoad.98WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31952\",\"addressline1\":\"98A Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"98A Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"98A\",\"premise\":\"98A\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.98AWhitehorseRoad.98AWhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31953\",\"addressline1\":\"100 Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"100 Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"100\",\"premise\":\"100\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.100WhitehorseRoad.100WhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"31954\",\"addressline1\":\"100A Whitehorse Road\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 2JB\",\"summaryline\":\"100A Whitehorse Road, Croydon, Surrey, CR0 2JB\",\"number\":\"100A\",\"premise\":\"100A\",\"street\":\"Whitehorse Road\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 2JB\",\"postcode_nospaces\":\"CR02JB\",\"postcode_sector\":\"CR0 2\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR02JB.100AWhitehorseRoad.100AWhitehorseRoad.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-03 12:46:16\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null}]","suggested_address":false,"job_types":"[{\"job_type_id\":\"89\",\"job_type\":\"Smoke Detector QTRLY inspection\",\"job_type_ref\":\"smokedetectorqtrlyinspection\",\"job_group\":\"Smoke Detector Qtrly Inspection\",\"job_type_desc\":\"Use this for smoke detector inspections.\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"175\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-01-21 07:00:39\",\"created_by\":\"156\",\"last_modified\":\"2020-04-27 11:20:19\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"93\",\"job_type\":\"Automatic Opening Vents Inspection & Servicing\",\"job_type_ref\":\"automaticopeningventsinspection&servicing\",\"job_group\":\"Automatic Opening Vents Inspection & Servicing\",\"job_type_desc\":\"use this when inspecting an AOV\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"70.50\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"182\",\"contract_id\":\"23\",\"stock_required\":\"1\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"1\",\"date_created\":\"2020-03-09 14:54:50\",\"created_by\":\"150\",\"last_modified\":\"2020-04-28 15:33:31\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"95\",\"job_type\":\"Emergency Lighting monthly inspection & servicing\",\"job_type_ref\":\"emergencylightingmonthlyinspection&servicing\",\"job_group\":\"Emergency Lighting Monthly Inspection & Servicing\",\"job_type_desc\":\"use this for monthly inspection of emergency lights\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"186\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-03-11 12:08:15\",\"created_by\":\"150\",\"last_modified\":\"2020-04-27 11:22:27\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"96\",\"job_type\":\"Test Job Type\",\"job_type_ref\":\"testjobtype\",\"job_group\":\"Test Job Type\",\"job_type_desc\":\"Test Job Type\",\"job_type_subtype\":\"Service Call\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"2\",\"base_priority_rating\":\"100\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"0\",\"evidoc_type_id\":\"0\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-04-28 16:28:05\",\"created_by\":\"1\",\"last_modified\":\"2020-04-28 16:28:21\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"}]","contract_id":"23","main_address_id":"31916","is_uploaded":1,"uploaded_record":1,"customer_id":48}],"invalid_contracts":[{"temp_job_id":"4","account_id":"8","contract_name":"BT RETAIL TSG2","job_number":null,"client_reference":"362660","salutation":"Miss","customer_last_name":"Centeno Alejandre M C","address_line1":"4","address_line2":"Acacia Avenue","address_line3":"Patcham","address_town":"Brighton","address_county":"Sussex","address_postcode":"SE17 2HE","customer_main_telephone":"1818118181","customer_email":"","job_type":"Automatic Opening Vents Inspection & Servicing","job_tracking_status":null,"date_created":null,"job_notes":"Aerial install","access_requirements":"Parking at back of garden","timestamp":"2020-04-29 07:54:02","upload_status":"Pending","region_id":"3","address_id":"13445","job_type_id":"93","checked":"1","postcode_addresses":"[{\"main_address_id\":\"13445\",\"addressline1\":\"Flat 1, Ringsfield House\",\"addressline2\":\"East Street\",\"addressline3\":\"London, SE17 2HE\",\"summaryline\":\"Flat 1, Ringsfield House, East Street, London, Greater London, SE17 2HE\",\"number\":\"\",\"premise\":\"Flat 1, Ringsfield House\",\"street\":\"East Street\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE17 2HE\",\"postcode_nospaces\":\"SE172HE\",\"postcode_sector\":\"SE17 2\",\"postcode_district\":\"SE17\",\"postcode_area\":\"SE\",\"postcode_qi\":\"17\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"Ringsfield House\",\"subbuildingname\":\"Flat 1\",\"dependentlocality\":\"\",\"uniquereference\":\"SE172HE.Flat1RingsfieldHouse.Flat1,RingsfieldHouse.EastStreet\",\"associate_key\":null,\"datecreated\":\"2019-06-12 13:33:53\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"13446\",\"addressline1\":\"Flat 2, Ringsfield House\",\"addressline2\":\"East Street\",\"addressline3\":\"London, SE17 2HE\",\"summaryline\":\"Flat 2, Ringsfield House, East Street, London, Greater London, SE17 2HE\",\"number\":\"\",\"premise\":\"Flat 2, Ringsfield House\",\"street\":\"East Street\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE17 2HE\",\"postcode_nospaces\":\"SE172HE\",\"postcode_sector\":\"SE17 2\",\"postcode_district\":\"SE17\",\"postcode_area\":\"SE\",\"postcode_qi\":\"17\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"Ringsfield House\",\"subbuildingname\":\"Flat 2\",\"dependentlocality\":\"\",\"uniquereference\":\"SE172HE.Flat2RingsfieldHouse.Flat2,RingsfieldHouse.EastStreet\",\"associate_key\":null,\"datecreated\":\"2019-06-12 13:33:53\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"13447\",\"addressline1\":\"Flat 3, Ringsfield House\",\"addressline2\":\"East Street\",\"addressline3\":\"London, SE17 2HE\",\"summaryline\":\"Flat 3, Ringsfield House, East Street, London, Greater London, SE17 2HE\",\"number\":\"\",\"premise\":\"Flat 3, Ringsfield House\",\"street\":\"East Street\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE17 2HE\",\"postcode_nospaces\":\"SE172HE\",\"postcode_sector\":\"SE17 2\",\"postcode_district\":\"SE17\",\"postcode_area\":\"SE\",\"postcode_qi\":\"17\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"Ringsfield House\",\"subbuildingname\":\"Flat 3\",\"dependentlocality\":\"\",\"uniquereference\":\"SE172HE.Flat3RingsfieldHouse.Flat3,RingsfieldHouse.EastStreet\",\"associate_key\":null,\"datecreated\":\"2019-06-12 13:33:53\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"13448\",\"addressline1\":\"Flat 4, Ringsfield House\",\"addressline2\":\"East Street\",\"addressline3\":\"London, SE17 2HE\",\"summaryline\":\"Flat 4, Ringsfield House, East Street, London, Greater London, SE17 2HE\",\"number\":\"\",\"premise\":\"Flat 4, Ringsfield House\",\"street\":\"East Street\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE17 2HE\",\"postcode_nospaces\":\"SE172HE\",\"postcode_sector\":\"SE17 2\",\"postcode_district\":\"SE17\",\"postcode_area\":\"SE\",\"postcode_qi\":\"17\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"Ringsfield House\",\"subbuildingname\":\"Flat 4\",\"dependentlocality\":\"\",\"uniquereference\":\"SE172HE.Flat4RingsfieldHouse.Flat4,RingsfieldHouse.EastStreet\",\"associate_key\":null,\"datecreated\":\"2019-06-12 13:33:53\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"13449\",\"addressline1\":\"Flat 5, Ringsfield House\",\"addressline2\":\"East Street\",\"addressline3\":\"London, SE17 2HE\",\"summaryline\":\"Flat 5, Ringsfield House, East Street, London, Greater London, SE17 2HE\",\"number\":\"\",\"premise\":\"Flat 5, Ringsfield House\",\"street\":\"East Street\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE17 2HE\",\"postcode_nospaces\":\"SE172HE\",\"postcode_sector\":\"SE17 2\",\"postcode_district\":\"SE17\",\"postcode_area\":\"SE\",\"postcode_qi\":\"17\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"Ringsfield House\",\"subbuildingname\":\"Flat 5\",\"dependentlocality\":\"\",\"uniquereference\":\"SE172HE.Flat5RingsfieldHouse.Flat5,RingsfieldHouse.EastStreet\",\"associate_key\":null,\"datecreated\":\"2019-06-12 13:33:53\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"13450\",\"addressline1\":\"Flat 6, Ringsfield House\",\"addressline2\":\"East Street\",\"addressline3\":\"London, SE17 2HE\",\"summaryline\":\"Flat 6, Ringsfield House, East Street, London, Greater London, SE17 2HE\",\"number\":\"\",\"premise\":\"Flat 6, Ringsfield House\",\"street\":\"East Street\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE17 2HE\",\"postcode_nospaces\":\"SE172HE\",\"postcode_sector\":\"SE17 2\",\"postcode_district\":\"SE17\",\"postcode_area\":\"SE\",\"postcode_qi\":\"17\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"Ringsfield House\",\"subbuildingname\":\"Flat 6\",\"dependentlocality\":\"\",\"uniquereference\":\"SE172HE.Flat6RingsfieldHouse.Flat6,RingsfieldHouse.EastStreet\",\"associate_key\":null,\"datecreated\":\"2019-06-12 13:33:53\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"13451\",\"addressline1\":\"Flat 7, Ringsfield House\",\"addressline2\":\"East Street\",\"addressline3\":\"London, SE17 2HE\",\"summaryline\":\"Flat 7, Ringsfield House, East Street, London, Greater London, SE17 2HE\",\"number\":\"\",\"premise\":\"Flat 7, Ringsfield House\",\"street\":\"East Street\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE17 2HE\",\"postcode_nospaces\":\"SE172HE\",\"postcode_sector\":\"SE17 2\",\"postcode_district\":\"SE17\",\"postcode_area\":\"SE\",\"postcode_qi\":\"17\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"Ringsfield House\",\"subbuildingname\":\"Flat 7\",\"dependentlocality\":\"\",\"uniquereference\":\"SE172HE.Flat7RingsfieldHouse.Flat7,RingsfieldHouse.EastStreet\",\"associate_key\":null,\"datecreated\":\"2019-06-12 13:33:53\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"13452\",\"addressline1\":\"Flat 8, Ringsfield House\",\"addressline2\":\"East Street\",\"addressline3\":\"London, SE17 2HE\",\"summaryline\":\"Flat 8, Ringsfield House, East Street, London, Greater London, SE17 2HE\",\"number\":\"\",\"premise\":\"Flat 8, Ringsfield House\",\"street\":\"East Street\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE17 2HE\",\"postcode_nospaces\":\"SE172HE\",\"postcode_sector\":\"SE17 2\",\"postcode_district\":\"SE17\",\"postcode_area\":\"SE\",\"postcode_qi\":\"17\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"Ringsfield House\",\"subbuildingname\":\"Flat 8\",\"dependentlocality\":\"\",\"uniquereference\":\"SE172HE.Flat8RingsfieldHouse.Flat8,RingsfieldHouse.EastStreet\",\"associate_key\":null,\"datecreated\":\"2019-06-12 13:33:53\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"13453\",\"addressline1\":\"Flat 9, Ringsfield House\",\"addressline2\":\"East Street\",\"addressline3\":\"London, SE17 2HE\",\"summaryline\":\"Flat 9, Ringsfield House, East Street, London, Greater London, SE17 2HE\",\"number\":\"\",\"premise\":\"Flat 9, Ringsfield House\",\"street\":\"East Street\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE17 2HE\",\"postcode_nospaces\":\"SE172HE\",\"postcode_sector\":\"SE17 2\",\"postcode_district\":\"SE17\",\"postcode_area\":\"SE\",\"postcode_qi\":\"17\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"Ringsfield House\",\"subbuildingname\":\"Flat 9\",\"dependentlocality\":\"\",\"uniquereference\":\"SE172HE.Flat9RingsfieldHouse.Flat9,RingsfieldHouse.EastStreet\",\"associate_key\":null,\"datecreated\":\"2019-06-12 13:33:53\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"13454\",\"addressline1\":\"Flat 10, Ringsfield House\",\"addressline2\":\"East Street\",\"addressline3\":\"London, SE17 2HE\",\"summaryline\":\"Flat 10, Ringsfield House, East Street, London, Greater London, SE17 2HE\",\"number\":\"\",\"premise\":\"Flat 10, Ringsfield House\",\"street\":\"East Street\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE17 2HE\",\"postcode_nospaces\":\"SE172HE\",\"postcode_sector\":\"SE17 2\",\"postcode_district\":\"SE17\",\"postcode_area\":\"SE\",\"postcode_qi\":\"17\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"Ringsfield House\",\"subbuildingname\":\"Flat 10\",\"dependentlocality\":\"\",\"uniquereference\":\"SE172HE.Flat10RingsfieldHouse.Flat10,RingsfieldHouse.EastStreet\",\"associate_key\":null,\"datecreated\":\"2019-06-12 13:33:53\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"13455\",\"addressline1\":\"Flat 11, Ringsfield House\",\"addressline2\":\"East Street\",\"addressline3\":\"London, SE17 2HE\",\"summaryline\":\"Flat 11, Ringsfield House, East Street, London, Greater London, SE17 2HE\",\"number\":\"\",\"premise\":\"Flat 11, Ringsfield House\",\"street\":\"East Street\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE17 2HE\",\"postcode_nospaces\":\"SE172HE\",\"postcode_sector\":\"SE17 2\",\"postcode_district\":\"SE17\",\"postcode_area\":\"SE\",\"postcode_qi\":\"17\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"Ringsfield House\",\"subbuildingname\":\"Flat 11\",\"dependentlocality\":\"\",\"uniquereference\":\"SE172HE.Flat11RingsfieldHouse.Flat11,RingsfieldHouse.EastStreet\",\"associate_key\":null,\"datecreated\":\"2019-06-12 13:33:53\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"13456\",\"addressline1\":\"Flat 12, Ringsfield House\",\"addressline2\":\"East Street\",\"addressline3\":\"London, SE17 2HE\",\"summaryline\":\"Flat 12, Ringsfield House, East Street, London, Greater London, SE17 2HE\",\"number\":\"\",\"premise\":\"Flat 12, Ringsfield House\",\"street\":\"East Street\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE17 2HE\",\"postcode_nospaces\":\"SE172HE\",\"postcode_sector\":\"SE17 2\",\"postcode_district\":\"SE17\",\"postcode_area\":\"SE\",\"postcode_qi\":\"17\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"Ringsfield House\",\"subbuildingname\":\"Flat 12\",\"dependentlocality\":\"\",\"uniquereference\":\"SE172HE.Flat12RingsfieldHouse.Flat12,RingsfieldHouse.EastStreet\",\"associate_key\":null,\"datecreated\":\"2019-06-12 13:33:53\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"13457\",\"addressline1\":\"Flat 13, Ringsfield House\",\"addressline2\":\"East Street\",\"addressline3\":\"London, SE17 2HE\",\"summaryline\":\"Flat 13, Ringsfield House, East Street, London, Greater London, SE17 2HE\",\"number\":\"\",\"premise\":\"Flat 13, Ringsfield House\",\"street\":\"East Street\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE17 2HE\",\"postcode_nospaces\":\"SE172HE\",\"postcode_sector\":\"SE17 2\",\"postcode_district\":\"SE17\",\"postcode_area\":\"SE\",\"postcode_qi\":\"17\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"Ringsfield House\",\"subbuildingname\":\"Flat 13\",\"dependentlocality\":\"\",\"uniquereference\":\"SE172HE.Flat13RingsfieldHouse.Flat13,RingsfieldHouse.EastStreet\",\"associate_key\":null,\"datecreated\":\"2019-06-12 13:33:53\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"13458\",\"addressline1\":\"Flat 14, Ringsfield House\",\"addressline2\":\"East Street\",\"addressline3\":\"London, SE17 2HE\",\"summaryline\":\"Flat 14, Ringsfield House, East Street, London, Greater London, SE17 2HE\",\"number\":\"\",\"premise\":\"Flat 14, Ringsfield House\",\"street\":\"East Street\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE17 2HE\",\"postcode_nospaces\":\"SE172HE\",\"postcode_sector\":\"SE17 2\",\"postcode_district\":\"SE17\",\"postcode_area\":\"SE\",\"postcode_qi\":\"17\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"Ringsfield House\",\"subbuildingname\":\"Flat 14\",\"dependentlocality\":\"\",\"uniquereference\":\"SE172HE.Flat14RingsfieldHouse.Flat14,RingsfieldHouse.EastStreet\",\"associate_key\":null,\"datecreated\":\"2019-06-12 13:33:53\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"13459\",\"addressline1\":\"Flat 15, Ringsfield House\",\"addressline2\":\"East Street\",\"addressline3\":\"London, SE17 2HE\",\"summaryline\":\"Flat 15, Ringsfield House, East Street, London, Greater London, SE17 2HE\",\"number\":\"\",\"premise\":\"Flat 15, Ringsfield House\",\"street\":\"East Street\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE17 2HE\",\"postcode_nospaces\":\"SE172HE\",\"postcode_sector\":\"SE17 2\",\"postcode_district\":\"SE17\",\"postcode_area\":\"SE\",\"postcode_qi\":\"17\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"Ringsfield House\",\"subbuildingname\":\"Flat 15\",\"dependentlocality\":\"\",\"uniquereference\":\"SE172HE.Flat15RingsfieldHouse.Flat15,RingsfieldHouse.EastStreet\",\"associate_key\":null,\"datecreated\":\"2019-06-12 13:33:53\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"13460\",\"addressline1\":\"Flat 16, Ringsfield House\",\"addressline2\":\"East Street\",\"addressline3\":\"London, SE17 2HE\",\"summaryline\":\"Flat 16, Ringsfield House, East Street, London, Greater London, SE17 2HE\",\"number\":\"\",\"premise\":\"Flat 16, Ringsfield House\",\"street\":\"East Street\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE17 2HE\",\"postcode_nospaces\":\"SE172HE\",\"postcode_sector\":\"SE17 2\",\"postcode_district\":\"SE17\",\"postcode_area\":\"SE\",\"postcode_qi\":\"17\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"Ringsfield House\",\"subbuildingname\":\"Flat 16\",\"dependentlocality\":\"\",\"uniquereference\":\"SE172HE.Flat16RingsfieldHouse.Flat16,RingsfieldHouse.EastStreet\",\"associate_key\":null,\"datecreated\":\"2019-06-12 13:33:53\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"13461\",\"addressline1\":\"Flat 17, Ringsfield House\",\"addressline2\":\"East Street\",\"addressline3\":\"London, SE17 2HE\",\"summaryline\":\"Flat 17, Ringsfield House, East Street, London, Greater London, SE17 2HE\",\"number\":\"\",\"premise\":\"Flat 17, Ringsfield House\",\"street\":\"East Street\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE17 2HE\",\"postcode_nospaces\":\"SE172HE\",\"postcode_sector\":\"SE17 2\",\"postcode_district\":\"SE17\",\"postcode_area\":\"SE\",\"postcode_qi\":\"17\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"Ringsfield House\",\"subbuildingname\":\"Flat 17\",\"dependentlocality\":\"\",\"uniquereference\":\"SE172HE.Flat17RingsfieldHouse.Flat17,RingsfieldHouse.EastStreet\",\"associate_key\":null,\"datecreated\":\"2019-06-12 13:33:53\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"13462\",\"addressline1\":\"Flat 18, Ringsfield House\",\"addressline2\":\"East Street\",\"addressline3\":\"London, SE17 2HE\",\"summaryline\":\"Flat 18, Ringsfield House, East Street, London, Greater London, SE17 2HE\",\"number\":\"\",\"premise\":\"Flat 18, Ringsfield House\",\"street\":\"East Street\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE17 2HE\",\"postcode_nospaces\":\"SE172HE\",\"postcode_sector\":\"SE17 2\",\"postcode_district\":\"SE17\",\"postcode_area\":\"SE\",\"postcode_qi\":\"17\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"Ringsfield House\",\"subbuildingname\":\"Flat 18\",\"dependentlocality\":\"\",\"uniquereference\":\"SE172HE.Flat18RingsfieldHouse.Flat18,RingsfieldHouse.EastStreet\",\"associate_key\":null,\"datecreated\":\"2019-06-12 13:33:53\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"13463\",\"addressline1\":\"Flat 19, Ringsfield House\",\"addressline2\":\"East Street\",\"addressline3\":\"London, SE17 2HE\",\"summaryline\":\"Flat 19, Ringsfield House, East Street, London, Greater London, SE17 2HE\",\"number\":\"\",\"premise\":\"Flat 19, Ringsfield House\",\"street\":\"East Street\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE17 2HE\",\"postcode_nospaces\":\"SE172HE\",\"postcode_sector\":\"SE17 2\",\"postcode_district\":\"SE17\",\"postcode_area\":\"SE\",\"postcode_qi\":\"17\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"Ringsfield House\",\"subbuildingname\":\"Flat 19\",\"dependentlocality\":\"\",\"uniquereference\":\"SE172HE.Flat19RingsfieldHouse.Flat19,RingsfieldHouse.EastStreet\",\"associate_key\":null,\"datecreated\":\"2019-06-12 13:33:53\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"13464\",\"addressline1\":\"Flat 20, Ringsfield House\",\"addressline2\":\"East Street\",\"addressline3\":\"London, SE17 2HE\",\"summaryline\":\"Flat 20, Ringsfield House, East Street, London, Greater London, SE17 2HE\",\"number\":\"\",\"premise\":\"Flat 20, Ringsfield House\",\"street\":\"East Street\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE17 2HE\",\"postcode_nospaces\":\"SE172HE\",\"postcode_sector\":\"SE17 2\",\"postcode_district\":\"SE17\",\"postcode_area\":\"SE\",\"postcode_qi\":\"17\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"Ringsfield House\",\"subbuildingname\":\"Flat 20\",\"dependentlocality\":\"\",\"uniquereference\":\"SE172HE.Flat20RingsfieldHouse.Flat20,RingsfieldHouse.EastStreet\",\"associate_key\":null,\"datecreated\":\"2019-06-12 13:33:53\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"13465\",\"addressline1\":\"Flat 21, Ringsfield House\",\"addressline2\":\"East Street\",\"addressline3\":\"London, SE17 2HE\",\"summaryline\":\"Flat 21, Ringsfield House, East Street, London, Greater London, SE17 2HE\",\"number\":\"\",\"premise\":\"Flat 21, Ringsfield House\",\"street\":\"East Street\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE17 2HE\",\"postcode_nospaces\":\"SE172HE\",\"postcode_sector\":\"SE17 2\",\"postcode_district\":\"SE17\",\"postcode_area\":\"SE\",\"postcode_qi\":\"17\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"Ringsfield House\",\"subbuildingname\":\"Flat 21\",\"dependentlocality\":\"\",\"uniquereference\":\"SE172HE.Flat21RingsfieldHouse.Flat21,RingsfieldHouse.EastStreet\",\"associate_key\":null,\"datecreated\":\"2019-06-12 13:33:53\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"13466\",\"addressline1\":\"Flat 22, Ringsfield House\",\"addressline2\":\"East Street\",\"addressline3\":\"London, SE17 2HE\",\"summaryline\":\"Flat 22, Ringsfield House, East Street, London, Greater London, SE17 2HE\",\"number\":\"\",\"premise\":\"Flat 22, Ringsfield House\",\"street\":\"East Street\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE17 2HE\",\"postcode_nospaces\":\"SE172HE\",\"postcode_sector\":\"SE17 2\",\"postcode_district\":\"SE17\",\"postcode_area\":\"SE\",\"postcode_qi\":\"17\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"Ringsfield House\",\"subbuildingname\":\"Flat 22\",\"dependentlocality\":\"\",\"uniquereference\":\"SE172HE.Flat22RingsfieldHouse.Flat22,RingsfieldHouse.EastStreet\",\"associate_key\":null,\"datecreated\":\"2019-06-12 13:33:53\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"13467\",\"addressline1\":\"Flat 23, Ringsfield House\",\"addressline2\":\"East Street\",\"addressline3\":\"London, SE17 2HE\",\"summaryline\":\"Flat 23, Ringsfield House, East Street, London, Greater London, SE17 2HE\",\"number\":\"\",\"premise\":\"Flat 23, Ringsfield House\",\"street\":\"East Street\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE17 2HE\",\"postcode_nospaces\":\"SE172HE\",\"postcode_sector\":\"SE17 2\",\"postcode_district\":\"SE17\",\"postcode_area\":\"SE\",\"postcode_qi\":\"17\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"Ringsfield House\",\"subbuildingname\":\"Flat 23\",\"dependentlocality\":\"\",\"uniquereference\":\"SE172HE.Flat23RingsfieldHouse.Flat23,RingsfieldHouse.EastStreet\",\"associate_key\":null,\"datecreated\":\"2019-06-12 13:33:53\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"13468\",\"addressline1\":\"Flat 24, Ringsfield House\",\"addressline2\":\"East Street\",\"addressline3\":\"London, SE17 2HE\",\"summaryline\":\"Flat 24, Ringsfield House, East Street, London, Greater London, SE17 2HE\",\"number\":\"\",\"premise\":\"Flat 24, Ringsfield House\",\"street\":\"East Street\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE17 2HE\",\"postcode_nospaces\":\"SE172HE\",\"postcode_sector\":\"SE17 2\",\"postcode_district\":\"SE17\",\"postcode_area\":\"SE\",\"postcode_qi\":\"17\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"Ringsfield House\",\"subbuildingname\":\"Flat 24\",\"dependentlocality\":\"\",\"uniquereference\":\"SE172HE.Flat24RingsfieldHouse.Flat24,RingsfieldHouse.EastStreet\",\"associate_key\":null,\"datecreated\":\"2019-06-12 13:33:53\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null}]","suggested_address":false,"job_types":"[{\"job_type_id\":\"89\",\"job_type\":\"Smoke Detector QTRLY inspection\",\"job_type_ref\":\"smokedetectorqtrlyinspection\",\"job_group\":\"Smoke Detector Qtrly Inspection\",\"job_type_desc\":\"Use this for smoke detector inspections.\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"175\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-01-21 07:00:39\",\"created_by\":\"156\",\"last_modified\":\"2020-04-27 11:20:19\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"93\",\"job_type\":\"Automatic Opening Vents Inspection & Servicing\",\"job_type_ref\":\"automaticopeningventsinspection&servicing\",\"job_group\":\"Automatic Opening Vents Inspection & Servicing\",\"job_type_desc\":\"use this when inspecting an AOV\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"70.50\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"182\",\"contract_id\":\"23\",\"stock_required\":\"1\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"1\",\"date_created\":\"2020-03-09 14:54:50\",\"created_by\":\"150\",\"last_modified\":\"2020-04-28 15:33:31\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"95\",\"job_type\":\"Emergency Lighting monthly inspection & servicing\",\"job_type_ref\":\"emergencylightingmonthlyinspection&servicing\",\"job_group\":\"Emergency Lighting Monthly Inspection & Servicing\",\"job_type_desc\":\"use this for monthly inspection of emergency lights\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"186\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-03-11 12:08:15\",\"created_by\":\"150\",\"last_modified\":\"2020-04-27 11:22:27\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"96\",\"job_type\":\"Test Job Type\",\"job_type_ref\":\"testjobtype\",\"job_group\":\"Test Job Type\",\"job_type_desc\":\"Test Job Type\",\"job_type_subtype\":\"Service Call\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"2\",\"base_priority_rating\":\"100\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"0\",\"evidoc_type_id\":\"0\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-04-28 16:28:05\",\"created_by\":\"1\",\"last_modified\":\"2020-04-28 16:28:21\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"}]"}],"invalid_addresses":[{"temp_job_id":"6","account_id":"8","contract_name":"BT RETAIL TSG","job_number":null,"client_reference":"362718","salutation":"Mr","customer_last_name":"Davison Antonia","address_line1":"6","address_line2":"Acacia Avenue","address_line3":"Patcham","address_town":"Brighton","address_county":"Sussex","address_postcode":"SW15 3","customer_main_telephone":"1818118181","customer_email":"","job_type":"Automatic Opening Vents Inspection & Servicing","job_tracking_status":null,"date_created":null,"job_notes":"Aerial install","access_requirements":"","timestamp":"2020-04-29 07:54:02","upload_status":"Pending","region_id":false,"job_type_id":"95","checked":"1","postcode_addresses":false,"suggested_address":false,"job_types":"[{\"job_type_id\":\"89\",\"job_type\":\"Smoke Detector QTRLY inspection\",\"job_type_ref\":\"smokedetectorqtrlyinspection\",\"job_group\":\"Smoke Detector Qtrly Inspection\",\"job_type_desc\":\"Use this for smoke detector inspections.\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"175\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-01-21 07:00:39\",\"created_by\":\"156\",\"last_modified\":\"2020-04-27 11:20:19\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"93\",\"job_type\":\"Automatic Opening Vents Inspection & Servicing\",\"job_type_ref\":\"automaticopeningventsinspection&servicing\",\"job_group\":\"Automatic Opening Vents Inspection & Servicing\",\"job_type_desc\":\"use this when inspecting an AOV\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"70.50\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"182\",\"contract_id\":\"23\",\"stock_required\":\"1\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"1\",\"date_created\":\"2020-03-09 14:54:50\",\"created_by\":\"150\",\"last_modified\":\"2020-04-28 15:33:31\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"95\",\"job_type\":\"Emergency Lighting monthly inspection & servicing\",\"job_type_ref\":\"emergencylightingmonthlyinspection&servicing\",\"job_group\":\"Emergency Lighting Monthly Inspection & Servicing\",\"job_type_desc\":\"use this for monthly inspection of emergency lights\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"186\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-03-11 12:08:15\",\"created_by\":\"150\",\"last_modified\":\"2020-04-27 11:22:27\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"96\",\"job_type\":\"Test Job Type\",\"job_type_ref\":\"testjobtype\",\"job_group\":\"Test Job Type\",\"job_type_desc\":\"Test Job Type\",\"job_type_subtype\":\"Service Call\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"2\",\"base_priority_rating\":\"100\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"0\",\"evidoc_type_id\":\"0\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-04-28 16:28:05\",\"created_by\":\"1\",\"last_modified\":\"2020-04-28 16:28:21\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"}]","contract_id":"23","main_address_id":null}],"invalid_job_types":[{"temp_job_id":"5","account_id":"8","contract_name":"BT RETAIL TSG","job_number":null,"client_reference":"362743","salutation":"Mr","customer_last_name":"Bunjun Ashley","address_line1":"5","address_line2":"Acacia Avenue","address_line3":"Patcham","address_town":"Brighton","address_county":"Sussex","address_postcode":"SE16 3SL","customer_main_telephone":"1818118181","customer_email":"","job_type":"Automatic Opening Vents Inspection & Servicing","job_tracking_status":null,"date_created":null,"job_notes":"Aerial install","access_requirements":"","timestamp":"2020-04-29 07:54:02","upload_status":"Pending","region_id":"3","address_id":"37027","job_type_id":"","checked":"1","postcode_addresses":"[{\"main_address_id\":\"37027\",\"addressline1\":\"65 Rouel Road\",\"addressline2\":\"London\",\"addressline3\":\"SE16 3SL\",\"summaryline\":\"65 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"65\",\"premise\":\"65\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.65RouelRoad.65RouelRoad.London\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37028\",\"addressline1\":\"67 Rouel Road\",\"addressline2\":\"London\",\"addressline3\":\"SE16 3SL\",\"summaryline\":\"67 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"67\",\"premise\":\"67\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.67RouelRoad.67RouelRoad.London\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37029\",\"addressline1\":\"69 Rouel Road\",\"addressline2\":\"London\",\"addressline3\":\"SE16 3SL\",\"summaryline\":\"69 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"69\",\"premise\":\"69\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.69RouelRoad.69RouelRoad.London\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37030\",\"addressline1\":\"71 Rouel Road\",\"addressline2\":\"London\",\"addressline3\":\"SE16 3SL\",\"summaryline\":\"71 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"71\",\"premise\":\"71\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.71RouelRoad.71RouelRoad.London\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37031\",\"addressline1\":\"73 Rouel Road\",\"addressline2\":\"London\",\"addressline3\":\"SE16 3SL\",\"summaryline\":\"73 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"73\",\"premise\":\"73\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.73RouelRoad.73RouelRoad.London\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37032\",\"addressline1\":\"Flat 1\",\"addressline2\":\"75 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 1, 75 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"75\",\"premise\":\"Flat 1, 75\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 1\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat1.Flat1.75RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37033\",\"addressline1\":\"Flat 2\",\"addressline2\":\"75 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 2, 75 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"75\",\"premise\":\"Flat 2, 75\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 2\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat2.Flat2.75RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37034\",\"addressline1\":\"77 Rouel Road\",\"addressline2\":\"London\",\"addressline3\":\"SE16 3SL\",\"summaryline\":\"77 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"77\",\"premise\":\"77\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.77RouelRoad.77RouelRoad.London\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37035\",\"addressline1\":\"Flat 1\",\"addressline2\":\"79 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 1, 79 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"79\",\"premise\":\"Flat 1, 79\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 1\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat1.Flat1.79RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37036\",\"addressline1\":\"Flat 2\",\"addressline2\":\"79 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 2, 79 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"79\",\"premise\":\"Flat 2, 79\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 2\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat2.Flat2.79RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37037\",\"addressline1\":\"Flat 3\",\"addressline2\":\"79 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 3, 79 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"79\",\"premise\":\"Flat 3, 79\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 3\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat3.Flat3.79RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37038\",\"addressline1\":\"Flat 4\",\"addressline2\":\"79 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 4, 79 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"79\",\"premise\":\"Flat 4, 79\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 4\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat4.Flat4.79RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37039\",\"addressline1\":\"Flat 5\",\"addressline2\":\"79 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 5, 79 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"79\",\"premise\":\"Flat 5, 79\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 5\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat5.Flat5.79RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37040\",\"addressline1\":\"Flat 6\",\"addressline2\":\"79 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 6, 79 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"79\",\"premise\":\"Flat 6, 79\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 6\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat6.Flat6.79RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37041\",\"addressline1\":\"Flat 7\",\"addressline2\":\"79 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 7, 79 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"79\",\"premise\":\"Flat 7, 79\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 7\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat7.Flat7.79RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37042\",\"addressline1\":\"Flat 8\",\"addressline2\":\"79 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 8, 79 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"79\",\"premise\":\"Flat 8, 79\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 8\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat8.Flat8.79RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37043\",\"addressline1\":\"Flat 9\",\"addressline2\":\"79 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 9, 79 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"79\",\"premise\":\"Flat 9, 79\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 9\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat9.Flat9.79RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37044\",\"addressline1\":\"Flat 10\",\"addressline2\":\"79 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 10, 79 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"79\",\"premise\":\"Flat 10, 79\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 10\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat10.Flat10.79RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37045\",\"addressline1\":\"Flat 11\",\"addressline2\":\"79 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 11, 79 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"79\",\"premise\":\"Flat 11, 79\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 11\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat11.Flat11.79RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37046\",\"addressline1\":\"Flat 12\",\"addressline2\":\"79 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 12, 79 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"79\",\"premise\":\"Flat 12, 79\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 12\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat12.Flat12.79RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37047\",\"addressline1\":\"Flat 13\",\"addressline2\":\"79 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 13, 79 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"79\",\"premise\":\"Flat 13, 79\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 13\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat13.Flat13.79RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37048\",\"addressline1\":\"Flat 14\",\"addressline2\":\"79 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 14, 79 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"79\",\"premise\":\"Flat 14, 79\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 14\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat14.Flat14.79RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37049\",\"addressline1\":\"Flat 15\",\"addressline2\":\"79 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 15, 79 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"79\",\"premise\":\"Flat 15, 79\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 15\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat15.Flat15.79RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37050\",\"addressline1\":\"Flat 16\",\"addressline2\":\"79 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 16, 79 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"79\",\"premise\":\"Flat 16, 79\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 16\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat16.Flat16.79RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37051\",\"addressline1\":\"Flat 17\",\"addressline2\":\"79 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 17, 79 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"79\",\"premise\":\"Flat 17, 79\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 17\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat17.Flat17.79RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37052\",\"addressline1\":\"Flat 18\",\"addressline2\":\"79 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 18, 79 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"79\",\"premise\":\"Flat 18, 79\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 18\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat18.Flat18.79RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37053\",\"addressline1\":\"Flat 19\",\"addressline2\":\"79 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 19, 79 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"79\",\"premise\":\"Flat 19, 79\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 19\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat19.Flat19.79RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37054\",\"addressline1\":\"Flat 20\",\"addressline2\":\"79 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 20, 79 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"79\",\"premise\":\"Flat 20, 79\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 20\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat20.Flat20.79RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37055\",\"addressline1\":\"Flat 21\",\"addressline2\":\"79 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 21, 79 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"79\",\"premise\":\"Flat 21, 79\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 21\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat21.Flat21.79RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37056\",\"addressline1\":\"Flat 22\",\"addressline2\":\"79 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 22, 79 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"79\",\"premise\":\"Flat 22, 79\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 22\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat22.Flat22.79RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37057\",\"addressline1\":\"Flat 23\",\"addressline2\":\"79 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 23, 79 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"79\",\"premise\":\"Flat 23, 79\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 23\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat23.Flat23.79RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"37058\",\"addressline1\":\"Flat 24\",\"addressline2\":\"79 Rouel Road\",\"addressline3\":\"London, SE16 3SL\",\"summaryline\":\"Flat 24, 79 Rouel Road, London, Greater London, SE16 3SL\",\"number\":\"79\",\"premise\":\"Flat 24, 79\",\"street\":\"Rouel Road\",\"posttown\":\"London\",\"county\":\"Greater London\",\"postcode\":\"SE16 3SL\",\"postcode_nospaces\":\"SE163SL\",\"postcode_sector\":\"SE16 3\",\"postcode_district\":\"SE16\",\"postcode_area\":\"SE\",\"postcode_qi\":\"16\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"Flat 24\",\"dependentlocality\":\"\",\"uniquereference\":\"SE163SL.Flat24.Flat24.79RouelRoad\",\"associate_key\":null,\"datecreated\":\"2020-02-18 15:24:50\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null}]","suggested_address":false,"job_types":"[{\"job_type_id\":\"89\",\"job_type\":\"Smoke Detector QTRLY inspection\",\"job_type_ref\":\"smokedetectorqtrlyinspection\",\"job_group\":\"Smoke Detector Qtrly Inspection\",\"job_type_desc\":\"Use this for smoke detector inspections.\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"175\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-01-21 07:00:39\",\"created_by\":\"156\",\"last_modified\":\"2020-04-27 11:20:19\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"93\",\"job_type\":\"Automatic Opening Vents Inspection & Servicing\",\"job_type_ref\":\"automaticopeningventsinspection&servicing\",\"job_group\":\"Automatic Opening Vents Inspection & Servicing\",\"job_type_desc\":\"use this when inspecting an AOV\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"70.50\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"182\",\"contract_id\":\"23\",\"stock_required\":\"1\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"1\",\"date_created\":\"2020-03-09 14:54:50\",\"created_by\":\"150\",\"last_modified\":\"2020-04-28 15:33:31\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"95\",\"job_type\":\"Emergency Lighting monthly inspection & servicing\",\"job_type_ref\":\"emergencylightingmonthlyinspection&servicing\",\"job_group\":\"Emergency Lighting Monthly Inspection & Servicing\",\"job_type_desc\":\"use this for monthly inspection of emergency lights\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"186\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-03-11 12:08:15\",\"created_by\":\"150\",\"last_modified\":\"2020-04-27 11:22:27\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"96\",\"job_type\":\"Test Job Type\",\"job_type_ref\":\"testjobtype\",\"job_group\":\"Test Job Type\",\"job_type_desc\":\"Test Job Type\",\"job_type_subtype\":\"Service Call\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"2\",\"base_priority_rating\":\"100\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"0\",\"evidoc_type_id\":\"0\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-04-28 16:28:05\",\"created_by\":\"1\",\"last_modified\":\"2020-04-28 16:28:21\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"}]","contract_id":"23","main_address_id":"37027","is_uploaded":1,"uploaded_record":1,"customer_id":46},{"temp_job_id":"9","account_id":"8","contract_name":"BT RETAIL TSG","job_number":null,"client_reference":"362814","salutation":"Mrs","customer_last_name":"Ellis John","address_line1":"9","address_line2":"Acacia Avenue","address_line3":"Patcham","address_town":"Brighton","address_county":"Sussex","address_postcode":"CR0 8JS","customer_main_telephone":"1818118181","customer_email":"","job_type":"Automatic Opening Vents Inspection & Servicing 2","job_tracking_status":null,"date_created":null,"job_notes":"Aerial install","access_requirements":"","timestamp":"2020-04-29 07:54:02","upload_status":"Pending","region_id":"2","address_id":"30742","job_type_id":"","checked":"1","postcode_addresses":"[{\"main_address_id\":\"30742\",\"addressline1\":\"Stormguard\",\"addressline2\":\"3 Fir Tree Gardens\",\"addressline3\":\"Croydon, CR0 8JS\",\"summaryline\":\"Stormguard, 3 Fir Tree Gardens, Croydon, Surrey, CR0 8JS\",\"number\":\"3\",\"premise\":\"3\",\"street\":\"Fir Tree Gardens\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 8JS\",\"postcode_nospaces\":\"CR08JS\",\"postcode_sector\":\"CR0 8\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"Stormguard\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR08JS.Stormguard.Stormguard.3FirTreeGardens\",\"associate_key\":null,\"datecreated\":\"2019-12-02 14:58:09\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"30743\",\"addressline1\":\"1 Fir Tree Gardens\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 8JS\",\"summaryline\":\"1 Fir Tree Gardens, Croydon, Surrey, CR0 8JS\",\"number\":\"1\",\"premise\":\"1\",\"street\":\"Fir Tree Gardens\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 8JS\",\"postcode_nospaces\":\"CR08JS\",\"postcode_sector\":\"CR0 8\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR08JS.1FirTreeGardens.1FirTreeGardens.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-02 14:58:09\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"30744\",\"addressline1\":\"5 Fir Tree Gardens\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 8JS\",\"summaryline\":\"5 Fir Tree Gardens, Croydon, Surrey, CR0 8JS\",\"number\":\"5\",\"premise\":\"5\",\"street\":\"Fir Tree Gardens\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 8JS\",\"postcode_nospaces\":\"CR08JS\",\"postcode_sector\":\"CR0 8\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR08JS.5FirTreeGardens.5FirTreeGardens.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-02 14:58:09\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"30745\",\"addressline1\":\"7 Fir Tree Gardens\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 8JS\",\"summaryline\":\"7 Fir Tree Gardens, Croydon, Surrey, CR0 8JS\",\"number\":\"7\",\"premise\":\"7\",\"street\":\"Fir Tree Gardens\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 8JS\",\"postcode_nospaces\":\"CR08JS\",\"postcode_sector\":\"CR0 8\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR08JS.7FirTreeGardens.7FirTreeGardens.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-02 14:58:09\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"30746\",\"addressline1\":\"9 Fir Tree Gardens\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 8JS\",\"summaryline\":\"9 Fir Tree Gardens, Croydon, Surrey, CR0 8JS\",\"number\":\"9\",\"premise\":\"9\",\"street\":\"Fir Tree Gardens\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 8JS\",\"postcode_nospaces\":\"CR08JS\",\"postcode_sector\":\"CR0 8\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR08JS.9FirTreeGardens.9FirTreeGardens.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-02 14:58:09\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"30747\",\"addressline1\":\"11 Fir Tree Gardens\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 8JS\",\"summaryline\":\"11 Fir Tree Gardens, Croydon, Surrey, CR0 8JS\",\"number\":\"11\",\"premise\":\"11\",\"street\":\"Fir Tree Gardens\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 8JS\",\"postcode_nospaces\":\"CR08JS\",\"postcode_sector\":\"CR0 8\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR08JS.11FirTreeGardens.11FirTreeGardens.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-02 14:58:09\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"30748\",\"addressline1\":\"13 Fir Tree Gardens\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 8JS\",\"summaryline\":\"13 Fir Tree Gardens, Croydon, Surrey, CR0 8JS\",\"number\":\"13\",\"premise\":\"13\",\"street\":\"Fir Tree Gardens\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 8JS\",\"postcode_nospaces\":\"CR08JS\",\"postcode_sector\":\"CR0 8\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR08JS.13FirTreeGardens.13FirTreeGardens.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-02 14:58:09\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"30749\",\"addressline1\":\"15 Fir Tree Gardens\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 8JS\",\"summaryline\":\"15 Fir Tree Gardens, Croydon, Surrey, CR0 8JS\",\"number\":\"15\",\"premise\":\"15\",\"street\":\"Fir Tree Gardens\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 8JS\",\"postcode_nospaces\":\"CR08JS\",\"postcode_sector\":\"CR0 8\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR08JS.15FirTreeGardens.15FirTreeGardens.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-02 14:58:09\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"30750\",\"addressline1\":\"17 Fir Tree Gardens\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 8JS\",\"summaryline\":\"17 Fir Tree Gardens, Croydon, Surrey, CR0 8JS\",\"number\":\"17\",\"premise\":\"17\",\"street\":\"Fir Tree Gardens\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 8JS\",\"postcode_nospaces\":\"CR08JS\",\"postcode_sector\":\"CR0 8\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR08JS.17FirTreeGardens.17FirTreeGardens.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-02 14:58:09\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"30751\",\"addressline1\":\"19 Fir Tree Gardens\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 8JS\",\"summaryline\":\"19 Fir Tree Gardens, Croydon, Surrey, CR0 8JS\",\"number\":\"19\",\"premise\":\"19\",\"street\":\"Fir Tree Gardens\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 8JS\",\"postcode_nospaces\":\"CR08JS\",\"postcode_sector\":\"CR0 8\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR08JS.19FirTreeGardens.19FirTreeGardens.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-02 14:58:09\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"30752\",\"addressline1\":\"21 Fir Tree Gardens\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 8JS\",\"summaryline\":\"21 Fir Tree Gardens, Croydon, Surrey, CR0 8JS\",\"number\":\"21\",\"premise\":\"21\",\"street\":\"Fir Tree Gardens\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 8JS\",\"postcode_nospaces\":\"CR08JS\",\"postcode_sector\":\"CR0 8\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR08JS.21FirTreeGardens.21FirTreeGardens.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-02 14:58:09\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"30753\",\"addressline1\":\"23 Fir Tree Gardens\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 8JS\",\"summaryline\":\"23 Fir Tree Gardens, Croydon, Surrey, CR0 8JS\",\"number\":\"23\",\"premise\":\"23\",\"street\":\"Fir Tree Gardens\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 8JS\",\"postcode_nospaces\":\"CR08JS\",\"postcode_sector\":\"CR0 8\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR08JS.23FirTreeGardens.23FirTreeGardens.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-02 14:58:09\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"30754\",\"addressline1\":\"25 Fir Tree Gardens\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 8JS\",\"summaryline\":\"25 Fir Tree Gardens, Croydon, Surrey, CR0 8JS\",\"number\":\"25\",\"premise\":\"25\",\"street\":\"Fir Tree Gardens\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 8JS\",\"postcode_nospaces\":\"CR08JS\",\"postcode_sector\":\"CR0 8\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR08JS.25FirTreeGardens.25FirTreeGardens.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-02 14:58:09\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"30755\",\"addressline1\":\"27 Fir Tree Gardens\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 8JS\",\"summaryline\":\"27 Fir Tree Gardens, Croydon, Surrey, CR0 8JS\",\"number\":\"27\",\"premise\":\"27\",\"street\":\"Fir Tree Gardens\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 8JS\",\"postcode_nospaces\":\"CR08JS\",\"postcode_sector\":\"CR0 8\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR08JS.27FirTreeGardens.27FirTreeGardens.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-02 14:58:09\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"30756\",\"addressline1\":\"29 Fir Tree Gardens\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 8JS\",\"summaryline\":\"29 Fir Tree Gardens, Croydon, Surrey, CR0 8JS\",\"number\":\"29\",\"premise\":\"29\",\"street\":\"Fir Tree Gardens\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 8JS\",\"postcode_nospaces\":\"CR08JS\",\"postcode_sector\":\"CR0 8\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR08JS.29FirTreeGardens.29FirTreeGardens.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-02 14:58:09\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"30757\",\"addressline1\":\"31 Fir Tree Gardens\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 8JS\",\"summaryline\":\"31 Fir Tree Gardens, Croydon, Surrey, CR0 8JS\",\"number\":\"31\",\"premise\":\"31\",\"street\":\"Fir Tree Gardens\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 8JS\",\"postcode_nospaces\":\"CR08JS\",\"postcode_sector\":\"CR0 8\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR08JS.31FirTreeGardens.31FirTreeGardens.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-02 14:58:09\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"30758\",\"addressline1\":\"33 Fir Tree Gardens\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 8JS\",\"summaryline\":\"33 Fir Tree Gardens, Croydon, Surrey, CR0 8JS\",\"number\":\"33\",\"premise\":\"33\",\"street\":\"Fir Tree Gardens\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 8JS\",\"postcode_nospaces\":\"CR08JS\",\"postcode_sector\":\"CR0 8\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR08JS.33FirTreeGardens.33FirTreeGardens.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-02 14:58:09\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"30759\",\"addressline1\":\"35 Fir Tree Gardens\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 8JS\",\"summaryline\":\"35 Fir Tree Gardens, Croydon, Surrey, CR0 8JS\",\"number\":\"35\",\"premise\":\"35\",\"street\":\"Fir Tree Gardens\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 8JS\",\"postcode_nospaces\":\"CR08JS\",\"postcode_sector\":\"CR0 8\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR08JS.35FirTreeGardens.35FirTreeGardens.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-02 14:58:09\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"30760\",\"addressline1\":\"37 Fir Tree Gardens\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 8JS\",\"summaryline\":\"37 Fir Tree Gardens, Croydon, Surrey, CR0 8JS\",\"number\":\"37\",\"premise\":\"37\",\"street\":\"Fir Tree Gardens\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 8JS\",\"postcode_nospaces\":\"CR08JS\",\"postcode_sector\":\"CR0 8\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR08JS.37FirTreeGardens.37FirTreeGardens.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-02 14:58:09\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"30761\",\"addressline1\":\"39 Fir Tree Gardens\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 8JS\",\"summaryline\":\"39 Fir Tree Gardens, Croydon, Surrey, CR0 8JS\",\"number\":\"39\",\"premise\":\"39\",\"street\":\"Fir Tree Gardens\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 8JS\",\"postcode_nospaces\":\"CR08JS\",\"postcode_sector\":\"CR0 8\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR08JS.39FirTreeGardens.39FirTreeGardens.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-02 14:58:09\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"30762\",\"addressline1\":\"41 Fir Tree Gardens\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 8JS\",\"summaryline\":\"41 Fir Tree Gardens, Croydon, Surrey, CR0 8JS\",\"number\":\"41\",\"premise\":\"41\",\"street\":\"Fir Tree Gardens\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 8JS\",\"postcode_nospaces\":\"CR08JS\",\"postcode_sector\":\"CR0 8\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR08JS.41FirTreeGardens.41FirTreeGardens.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-02 14:58:09\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"30763\",\"addressline1\":\"43 Fir Tree Gardens\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 8JS\",\"summaryline\":\"43 Fir Tree Gardens, Croydon, Surrey, CR0 8JS\",\"number\":\"43\",\"premise\":\"43\",\"street\":\"Fir Tree Gardens\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 8JS\",\"postcode_nospaces\":\"CR08JS\",\"postcode_sector\":\"CR0 8\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR08JS.43FirTreeGardens.43FirTreeGardens.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-02 14:58:09\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"30764\",\"addressline1\":\"45 Fir Tree Gardens\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 8JS\",\"summaryline\":\"45 Fir Tree Gardens, Croydon, Surrey, CR0 8JS\",\"number\":\"45\",\"premise\":\"45\",\"street\":\"Fir Tree Gardens\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 8JS\",\"postcode_nospaces\":\"CR08JS\",\"postcode_sector\":\"CR0 8\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR08JS.45FirTreeGardens.45FirTreeGardens.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-02 14:58:09\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null},{\"main_address_id\":\"30765\",\"addressline1\":\"47 Fir Tree Gardens\",\"addressline2\":\"Croydon\",\"addressline3\":\"CR0 8JS\",\"summaryline\":\"47 Fir Tree Gardens, Croydon, Surrey, CR0 8JS\",\"number\":\"47\",\"premise\":\"47\",\"street\":\"Fir Tree Gardens\",\"posttown\":\"Croydon\",\"county\":\"Surrey\",\"postcode\":\"CR0 8JS\",\"postcode_nospaces\":\"CR08JS\",\"postcode_sector\":\"CR0 8\",\"postcode_district\":\"CR0\",\"postcode_area\":\"CR\",\"postcode_qi\":\"0\",\"xcoords\":\"\",\"ycoords\":\"\",\"organisation\":\"\",\"buildingname\":\"\",\"subbuildingname\":\"\",\"dependentlocality\":\"\",\"uniquereference\":\"CR08JS.47FirTreeGardens.47FirTreeGardens.Croydon\",\"associate_key\":null,\"datecreated\":\"2019-12-02 14:58:09\",\"lastmodified\":null,\"activesky\":\"N\",\"activeskyq\":\"N\",\"lastmodifiedby\":null}]","suggested_address":false,"job_types":"[{\"job_type_id\":\"89\",\"job_type\":\"Smoke Detector QTRLY inspection\",\"job_type_ref\":\"smokedetectorqtrlyinspection\",\"job_group\":\"Smoke Detector Qtrly Inspection\",\"job_type_desc\":\"Use this for smoke detector inspections.\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"175\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-01-21 07:00:39\",\"created_by\":\"156\",\"last_modified\":\"2020-04-27 11:20:19\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"93\",\"job_type\":\"Automatic Opening Vents Inspection & Servicing\",\"job_type_ref\":\"automaticopeningventsinspection&servicing\",\"job_group\":\"Automatic Opening Vents Inspection & Servicing\",\"job_type_desc\":\"use this when inspecting an AOV\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"70.50\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"182\",\"contract_id\":\"23\",\"stock_required\":\"1\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"1\",\"date_created\":\"2020-03-09 14:54:50\",\"created_by\":\"150\",\"last_modified\":\"2020-04-28 15:33:31\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"95\",\"job_type\":\"Emergency Lighting monthly inspection & servicing\",\"job_type_ref\":\"emergencylightingmonthlyinspection&servicing\",\"job_group\":\"Emergency Lighting Monthly Inspection & Servicing\",\"job_type_desc\":\"use this for monthly inspection of emergency lights\",\"job_type_subtype\":\"Inspection\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"\",\"base_priority_rating\":\"\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"1\",\"evidoc_type_id\":\"186\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-03-11 12:08:15\",\"created_by\":\"150\",\"last_modified\":\"2020-04-27 11:22:27\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"},{\"job_type_id\":\"96\",\"job_type\":\"Test Job Type\",\"job_type_ref\":\"testjobtype\",\"job_group\":\"Test Job Type\",\"job_type_desc\":\"Test Job Type\",\"job_type_subtype\":\"Service Call\",\"job_base_rate\":\"0.00\",\"job_base_rate_adjustable\":\"0\",\"job_base_duration\":\"1.0\",\"base_sla\":\"2\",\"base_priority_rating\":\"100\",\"estimated_slots\":null,\"method_statement\":null,\"ra_required\":\"0\",\"nps_required\":\"0\",\"csat_required\":\"0\",\"evidoc_required\":\"0\",\"evidoc_type_id\":\"0\",\"contract_id\":\"23\",\"stock_required\":\"0\",\"boms_required\":\"0\",\"signature_required\":\"0\",\"damage_details_req\":\"0\",\"date_created\":\"2020-04-28 16:28:05\",\"created_by\":\"1\",\"last_modified\":\"2020-04-28 16:28:21\",\"last_modified_by\":\"1\",\"account_id\":\"8\",\"category_id\":\"0\",\"bom_category_id\":\"0\",\"is_active\":\"1\",\"archived\":\"0\"}]","contract_id":"23","main_address_id":"30742","is_uploaded":1,"uploaded_record":1,"customer_id":49}]}';
		#$data['processed_data'] 	= json_decode( $processed );
		#$data['successful_records'] = isset( $data['processed_data']->jobs_created_successfully ) ? $data['processed_data']->jobs_created_successfully : false;
		#unset( $data['processed_data']->jobs_created_successfully );
		
		#debug($data['processed_data']);
		#$this->_render_webpage('/job/uploads/processed_job_uploads', $data );
		
		$account_id = $this->user->account_id;
		if( !empty( $account_id ) && ( $this->input->post( 'jobs_data' ) ) ){
			$postdata 	= $this->input->post();
			$result 	= $this->document_service->process_job_uploads( $account_id, $postdata );
			if( $result ){
				$data['processed_data'] 	= json_decode( json_encode( $result ) );
				if( !empty( $data['processed_data'] ) ){
					$data['successful_records'] = isset( $data['processed_data']->jobs_created_successfully ) ? $data['processed_data']->jobs_created_successfully : false;
					unset( $data['processed_data']->jobs_created_successfully );
					$this->_render_webpage('/job/uploads/processed_job_uploads', $data );
				} else {
					redirect( '/webapp/job/pending_job_uploads/'.$account_id );
				}
			} else {
				redirect( '/webapp/job/pending_job_uploads/'.$account_id );
			}
		} else {
			$this->session->set_flashdata( 'message', 'Invalid paramaters' );	
			redirect( '/webapp/job/upload_jobs/' );
		}

	}
	
	/** Delete Temp Records **/
	public function drop_temp_records( $account_id = false ){
		$account_id = $this->user->account_id;
		if( !empty( $account_id ) && ( $this->input->post( 'jobs_data' ) ) ){
			$postdata 	= $this->input->post();
			$result 	= $this->document_service->drop_temp_records( $account_id, $postdata );
		} else {
			$this->session->set_flashdata( 'message', 'Invalid paramaters' );	
		}
		redirect( '/webapp/job/pending_job_uploads/'.$account_id );
	}
	
	
	/** Pull External Jobs **/
	public function pull_jobs(){
		if( !$this->identity() ){
			redirect('webapp/user/login', 'refresh');
		}

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			$data['error_message'] = $this->session->flashdata( 'message' );
			$this->_render_webpage( 'job/third_party_jobs/pull_jobs', $data );
		}
	}
	
	
	/** 
	* Fetch External Jobs By Site Number
	**/
	public function fetch_external_jobs(){

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$return_data = [
			'status'=>0,
			'request_outcome'=>'No'
		];

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		} else {
			$postdata 	  	 = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );

			$external_jobs 	 = $this->webapp_service->api_dispatcher( $this->api_end_point.'tesseract/retrieve_job_calls', $postdata, ['auth_token'=>$this->auth_token], true );
			$result		  	 = ( isset( $external_jobs->job_calls ) ) ? $external_jobs->job_calls : null;
			$message	  	 = ( isset( $external_jobs->message ) )  ? $external_jobs->message : 'Oops! There was an error processing your request.';
			$outcome_message = '';
			
			if( !empty( $result ) ){
				$return_data['status'] 	= 1;

				$total_new_jobs 	 = count( $result->new_jobs );
				$total_existing_jobs = count( $result->existing_jobs );
				
				if( ( $total_new_jobs + $total_existing_jobs ) == 0 ){
					$outcome_message .= '<div class="text-red">There was an error processing your request. No records were created on Evident!</div>';
				} else {
					
					$outcome_message .= '<div class="text-green" ><strong>'. $total_new_jobs .'</strong> were created successfully.</div><br/>';
					
					$outcome_message .= '<div class="text-orange" ><strong>'. $total_existing_jobs .'</strong> pre-existing records and were updated successfully.</div><br/>';
				
				}

				#$return_data['external_jobs']	= $result;
			} else {
				$outcome_message .= '<div class="text-red">There were no records found matching your criteria!</div>';
			}

			$return_data['request_outcome']	= $outcome_message;
			$return_data['status_msg'] 		= $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	/** 
	* Fetch Available Resource
	*/
	public function fetch_available_resource( $job_id = false, $ref_date = false, $job_postcode = false, $job_type_id = false, $region_id = false ){

		$job_id 		= ( $this->input->post( 'job_id' ) ) 		? $this->input->post( 'job_id' ) 		: ( !empty( $job_id ) 			? $job_id 	: null );
		$ref_date 		= ( $this->input->post( 'ref_date' ) ) 		? $this->input->post( 'ref_date' ) 		: ( !empty( $ref_date ) 		? $ref_date : null );
		$job_type_id 	= ( $this->input->post( 'job_type_id' ) ) 	? $this->input->post( 'job_type_id' ) 	: ( !empty( $job_type_id )		? $job_type_id : null );
		$region_id 		= ( $this->input->post( 'region_id' ) ) 	? $this->input->post( 'region_id' ) 	: ( !empty( $region_id ) 		? $region_id : null );
		$job_postcode 	= ( $this->input->post( 'job_postcode' ) ) 	? urldecode( $this->input->post( 'job_postcode' ) ) 	: ( !empty( $job_postcode ) 	? urldecode( $job_postcode ) : null );

		$return_data = [
			'status'			=> 0,
			'resource_records'	=> null,
			'status_msg'		=> 'Invalid paramaters'
		];
		
		if( !empty( $ref_date ) ){
			
			## all availability for the specific day, all scheduled jobs for the day
			$ref_date 			= date( 'Y-m-d', strtotime( $ref_date ) );
			$where = [
				'date_from' 	=> $ref_date,
				'date_to' 		=> $ref_date,
				'job_type_id' 	=> $job_type_id,
				'region_id' 	=> $region_id
			];

			if( $this->user->is_primary_user && !$this->user->is_admin ){
				$where['associated_user_id']     = $this->user->id;
			}

			$available_resource	= $this->webapp_service->api_dispatcher( $this->api_end_point.'diary/available_engineer_resource', ['account_id'=>$this->user->account_id,'where'=>$where ], ['auth_token'=>$this->auth_token], true );
			$result				= ( isset( $available_resource->available_resource ) ) ? $available_resource->available_resource : null;
			$message	= ( isset( $available_resource->message ) ) ? $available_resource->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$result = !empty( $result->{$ref_date} ) ? $result->{$ref_date} : $result;
				$res_record = $this->load_available_resource( $job_id, $ref_date, $job_postcode, $result );
				$return_data['status'] 	  		 	= 1;
				$return_data['resource_records'] 	= $res_record['res_record'];
				$return_data['postcode_coverage'] 	= $res_record['postcode_coverage'];
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	private function load_available_resource( $job_id = false, $job_date = false, $job_postcode = false, $resource_records = false ){
		$result 	= [
			'postcode_coverage' => [],
			'res_record' 		=> ''
		];
		$res_record = '';
		if( !empty( $job_id ) && !empty( $job_date ) && !empty( $resource_records ) ){
			
			if( !empty( $job_postcode ) ){
				$job_postcode_district	= explode( " ", $job_postcode );
				$job_postcode_area		= preg_replace( '/[0-9]+/', '', $job_postcode_district[0] );
			}

			$postcode_coverage = [];
			$res_record .= '<table id="engineer-resource" class="table" width="100%" style="border-top:none;"><tr><td>';
			
			$top_list_records 		= '';
			$bottom_list_records	= '';
			
			foreach( $resource_records as $k => $row ){
				
				if( !empty( $row->home_postcode ) ){
					$engineer_home_postcode	= explode( " ", $row->home_postcode );
					$engineer_postcode_area	= preg_replace( '/[0-9]+/', '', $engineer_home_postcode[0] );
				}
				
				$engineer_coverage 	= [];
				$global_classes 	= '';

				if( !empty( $row->availability->booked_postcode_areas ) ){
					$global_classes  	= str_replace( ",", " ", implode( ",", object_to_array( $row->availability->booked_postcode_areas ) ) );
					$postcode_coverage 	= array_unique( array_merge( $postcode_coverage, object_to_array( $row->availability->booked_postcode_areas ) ) );
					$engineer_coverage	= object_to_array( $row->availability->booked_postcode_areas );
				}

				$engineer_postcode_area_match = ( !empty( $job_postcode_area ) && !empty( $engineer_postcode_area ) && ( $job_postcode_area == $engineer_postcode_area ) ) ? 'engineer-postcode-area-match top-list' : '';

				$single_row = '<div class="global-class '.$global_classes.'" data-engineer_id="'.$row->person_id.'">';			
					$single_row .= '<div class="panel panel-default" >';			
						$single_row .= '<div class="user-element panel-body '.$engineer_postcode_area_match.' ">';
							
							$single_row .= '<div class="row">';
								$single_row .= '<div class="col-md-4">';
									$single_row .= '<input class="assigned-engineer pull-left" id="rad'.$k.'" type="radio" name="assigned_to" value="'.$row->person_id.'" />';
									$single_row .= '<label class="pointer text-bold engineer-row" for="rad'.$k.'" style="width:95%; margin: 0px 0px 10px; padding-left:10px">';
										$single_row .= $row->person;
									$single_row .= '</label>';
								$single_row .= '</div>';
								
								$single_row .= '<div class="col-md-5">'.( !empty( $row->home_address ) ? $row->home_address : "<span class='text-red'>Home address not set!</span>" ).'</div>';
								
								$single_row .= '<div class="col-md-3">';
									$single_row .= '<span class="pull-right"><span>Used: '.( number_format( $row->availability->booked_slots, 2 ) + 0 ).' hrs</span> &nbsp; &nbsp;<span>Free: '.( number_format( ( $row->availability->actual_slots - $row->availability->booked_slots ), 2 ) + 0 ).' hrs</span></span>';							
									$single_row .= '<div class="pull-right"><a class="view-booked-jobs pointer '.( !empty( $row->availability->booked_jobs ) ? '' : 'hide' ).'" data-user_id="'.$row->person_id.'" >View Booked Jobs</a></div>';
								$single_row .= '</div>';
								
								$single_row .= '<div class="clearfix"></div>';
								if( !empty( $row->availability->booked_jobs ) ){ 
									$single_row .= '<div class="col-md-12 view-booked-jobs-'.$row->person_id.'" style="display:none" >';
										$single_row .= '<div class="col-md-12" style="margin-left:12px">';
											$single_row .= '<table class="table margin-bottom-0 margin-top-10" >';
												foreach( $row->availability->booked_jobs as $col => $booked_job ){
													$single_row .= '<tr>';
														$single_row .= '<td width="5%"><a target="_blank" href="'.( base_url( '/webapp/job/profile/'.$booked_job->job_id ) ).'">'.$booked_job->job_id.'</a></td>';
														$single_row .= '<td width="40%"><a target="_blank" href="'.( base_url( '/webapp/job/profile/'.$booked_job->job_id ) ).'">'.$booked_job->job_type.'</a></td>';
														$single_row .= '<td width="15%">'.( date( "d-m-Y", strtotime( $booked_job->job_date ) ) ).'</td>';
														$single_row .= '<td width="15%">'.$booked_job->address_postcode.'</td>';
														$single_row .= '<td width="5%">'.$booked_job->job_duration.'</td>';
													$single_row .= '</tr>';
												}
											$single_row .= '</table>';
										$single_row .= '</div>';
									$single_row .= '</div>';
								}
							$single_row .= '</div>';
						
						$single_row .= '</div>'; 				
					$single_row .= '</div>';				
				$single_row .= '</div>';

				## Append to Top/Bottom
				if( !empty( $engineer_postcode_area_match ) ){
					$top_list_records 	 .= $single_row;
				} else {
					$bottom_list_records .= $single_row;
				}
				
			}
			
			$res_record .= $top_list_records;
			$res_record .= $bottom_list_records;
			
			$res_record .= '</td></tr></table>';
		}
		
		if( !empty( $postcode_coverage ) ){
			$postcode_filters  = '';
			$postcode_coverage = array_filter( $postcode_coverage, 'strlen' );
			sort( $postcode_coverage );
			foreach ( $postcode_coverage as $key => $val) {
				$postcode_filters .= '<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">';
					$postcode_filters .= '<label class="pointer" for="postcode-'.( strtoupper( trim( $val ) ) ).'">';
					$postcode_filters .= '<input type="checkbox" name="postcode_coverage[]" data-postcode_area="'.( strtoupper( trim( $val ) ) ).'" id="postcode-'.( strtoupper( trim( $val ) ) ).'" value="'.$val.'" class="postcode-areas" /> <span class="postcode_area">'.strtoupper( $val ).'</span></label>';
				$postcode_filters .= '</div>';
			}
			$result['postcode_coverage'] = $postcode_filters;
		}

		$result['res_record'] 		 = $res_record;
		
		return $result;
	}
	
	/** Bulk Job Reassign **/
	public function reassign( $job_date = false, $postcode_area = false, $job_type_id = false, $region_id = false ){
		
		if( !$this->identity() ){
			redirect('webapp/user/login', 'refresh');
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'details' );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			
			$job_date 			= ( $this->input->get( 'job_date' ) ) 		? $this->input->get( 'job_date' ) 		: ( !empty( $job_date ) 		? $job_date : date( 'Y-m-d' ) );
			$job_type_id 		= ( $this->input->get( 'job_type_id' ) ) 	? $this->input->get( 'job_type_id' ) 	: ( !empty( $job_type_id )		? $job_type_id : null );
			$region_id 			= ( $this->input->get( 'region_id' ) ) 		? $this->input->get( 'region_id' ) 		: ( !empty( $region_id ) 		? $region_id : null );
		
			$ref_date 			= date( 'Y-m-d', strtotime( $job_date ) );
			$where = [
				'job_date' 		=> $ref_date,
				'ref_date' 		=> $ref_date,
				'job_type_id' 	=> $job_type_id,
				'region_id' 	=> $region_id
			];

			/* $available_resource			= $this->webapp_service->api_dispatcher( $this->api_end_point.'diary/available_engineer_resource', ['account_id'=>$this->user->account_id,'where'=>$where ], ['auth_token'=>$this->auth_token], true );
			$available_resource			= ( isset( $available_resource->available_resource ) ) ? $available_resource->available_resource : null;
			$ data['available_resource'] = !empty( $available_resource->{$ref_date} ) ? $available_resource->{$ref_date} : $result;
			*/
			// $assigned_jobs				= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/assigned_jobs_by_engineer', ['account_id'=>$this->user->account_id,'where'=>$where ], ['auth_token'=>$this->auth_token], true );
			// $data['assigned_jobs']		= ( isset( $assigned_jobs->assigned_jobs ) ) ? $assigned_jobs->assigned_jobs : null;

			$job_types						= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_types', ['account_id'=>$this->user->account_id,'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
			$data['job_types']				= ( isset( $job_types->job_types ) ) ? $job_types->job_types : null;
			
			$regions 						= $this->webapp_service->api_dispatcher( $this->api_end_point.'diary/regions', [ 'account_id'=>$this->user->account_id, 'limit'=>-1 ], [ 'auth_token'=>$this->auth_token ], true );
			$data['regions']				= ( isset( $regions->regions ) ) ? $regions->regions : null;
			
			$data['job_date']			=  date( 'd-m-Y', strtotime( $job_date ) );
			
			$this->_render_webpage( 'job/inc/reassign_jobs', $data );
		}
		
	}
	
	
	/**
	* Bulk Re-assign Jobs
	*/
	public function bulk_reassign_jobs(){
		$return_data = [
			'status'=>0
		];

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'details' );

		if( !$this->user->is_admin && empty( $item_access->can_delete ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 	  	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$reassign_jobs 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/bulk_reassign_jobs', $postdata, ['auth_token'=>$this->auth_token] );
			$result		    = ( isset( $reassign_jobs->reassign_jobs ) )   ? $reassign_jobs->reassign_jobs : null;
			$message	  	= ( isset( $reassign_jobs->message ) ) ? $reassign_jobs->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/** 
	* Fetch Assigned Jobs Data
	*/
	public function fetch_assigned_jobs_data( $job_date = false, $job_type_id = false, $region_id = false ){

		$job_date 		= ( $this->input->post( 'job_date' ) ) 		? $this->input->post( 'job_date' ) 		: ( !empty( $job_date ) 		? $job_date : null );
		$job_type_id 	= ( $this->input->post( 'job_type_id' ) ) 	? $this->input->post( 'job_type_id' ) 	: ( !empty( $job_type_id )		? $job_type_id : null );
		$region_id 		= ( $this->input->post( 'region_id' ) ) 	? $this->input->post( 'region_id' ) 	: ( !empty( $region_id ) 		? $region_id : null );
		$job_postcode 	= ( $this->input->post( 'job_postcode' ) ) 	? urldecode( $this->input->post( 'job_postcode' ) ) 	: ( !empty( $job_postcode ) 	? urldecode( $job_postcode ) : null );
		$where 			= ( $this->input->post( 'where' ) ) 		? $this->input->post( 'where' ) 		: false;

		$return_data = [
			'status'			=> 0,
			'assigned_jobs'	=> null,
			'status_msg'		=> 'Invalid paramaters'
		];
		
		if( !empty( $job_date ) ){
			$job_date 			= date( 'Y-m-d', strtotime( $job_date ) );
			$where = [
				'job_date' 					=> $job_date,
				'ref_date' 					=> $job_date,
				'job_type_id' 				=> $job_type_id,
				'region_id' 				=> $region_id,
				'exclude_successful_jobs' 	=> !empty( $where['exclude_successful_jobs'] ) ? $where['exclude_successful_jobs'] : false,
			];
			
			if( $this->user->is_primary_user && !$this->user->is_admin ){
				$where['associated_user_id']     = $this->user->id;
			}
			
			$available_resource	= $this->webapp_service->api_dispatcher( $this->api_end_point.'diary/available_engineer_resource', ['account_id'=>$this->user->account_id,'where'=>$where ], ['auth_token'=>$this->auth_token], true );
			$engineers_result	= ( isset( $available_resource->available_resource ) ) ? $available_resource->available_resource : null;

			$jobs_data			= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/assigned_jobs_by_engineer', ['account_id'=>$this->user->account_id,'where'=>$where ], ['auth_token'=>$this->auth_token], true );
			$jobs_result		= ( isset( $jobs_data->assigned_jobs ) ) ? $jobs_data->assigned_jobs : null;
			$job_types			= ( isset( $jobs_data->job_types ) ) ? $jobs_data->job_types : null;
			$regions			= ( isset( $jobs_data->regions ) ) ? $jobs_data->regions : null;
			$message			= ( isset( $jobs_data->message ) ) ? $jobs_data->message : null;

			if( !empty( $jobs_result ) ){
				$assigned_jobs 		= !empty( $jobs_result->assigned_jobs ) 	? $jobs_result->assigned_jobs 		: $jobs_result;
				$assigned_jobs 		= $this->load_assigned_jobs( $assigned_jobs );
				$engineer_resource	= !empty( $engineers_result->{$job_date} ) 	? $engineers_result->{$job_date} 	: false;
				
				if( !empty( $engineer_resource ) ){
					$available_resource = $this->load_engineer_resource( $engineer_resource );
				}
				
				$job_type_options 	= $this->load_job_types_options( $job_types );
				$region_options 	= $this->load_region_options( $regions );
				
				$return_data['status'] 	  			= 1;
				$return_data['assigned_jobs'] 		= $assigned_jobs;
				$return_data['available_resource'] 	= $available_resource;
				$return_data['job_type_options'] 	= $job_type_options;
				$return_data['region_options'] 		= $region_options;
				$return_data['job_date'] 			= date( 'd-m-Y', strtotime( $job_date ) );
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	private function load_assigned_jobs( $assigned_jobs = false ){
		
		$return_str = '';
		
		if( !empty( $assigned_jobs ) ){
			$return_str .= '<table class="table" style="width:100%;">';
				foreach( $assigned_jobs as $key => $engineer_details ){
					$return_str .= '<tr class="user-element pointer" >';
						$return_str .= '<th width="3%" ><input class="check-all-jobs grouped-jobs-'.$engineer_details->engineer_id.'" data-engineer_ref="'.$key.'" id="eng-'.$key.'" type="checkbox" /></th>';
						$return_str .= '<th width="25%" ><label class="pointer assigned-row text-normalweight" data-engineer_id="'.$engineer_details->engineer_id.'" for="eng-'.$key.'" >'.$engineer_details->engineer_name.'</label></th>';
						$return_str .= '<th width="42%" ><label class="pointer assigned-row text-normalweight" data-engineer_id="'.$engineer_details->engineer_id.'" >'.( !empty( $engineer_details->home_address ) ? $engineer_details->home_address : "" ).'</label></th>';
						$return_str .= '<th width="20%" ><span class="pull-right"><a class="pointer assigned-row" data-engineer_id="'.$engineer_details->engineer_id.'" >Total Jobs: '.( !empty( $engineer_details->total_jobs ) ? ( $engineer_details->total_jobs ) : 0 ).' </a></span></th>';
					$return_str .= '</tr>';

					$return_str .= '<tr class="engineer-jobs assigned-jobs-'.$engineer_details->engineer_id.'" style="display:none" >';
						$return_str .= '<td colspan="4" >';
							$return_str .= '<table class="table no-border" style="width:100%; font-size:100%;">';
								if( $engineer_details->status_jobs ) { foreach( $engineer_details->status_jobs as $job_status => $status_jobs ){
									
									
									$return_str .= '<tr style="border-bottom: 1px dashed #ccc">';
										$return_str .= '<th width="30%" style="padding-left:30px;" >JOB TYPE</th>';
										$return_str .= '<th width="10%" >JOB DATE</th>';
										$return_str .= '<th width="10%" >DUE DATE</th>';
										$return_str .= '<th width="30%" >SITE REF / SITE ADDRESS</th>';
										$return_str .= '<th width="10%" ><span class="pull-right" >JOB STATUS</span></th>';
									$return_str .= '</tr>';
									
									foreach( $status_jobs as $status_key => $status_job ){
										$return_str .= '<tr class="user-element no-border">';
											$return_str .= '<td class="no-border" style="padding-left:30px;">';
												$return_str .= '<label class="pointer text-bold" for="job-check'.$engineer_details->engineer_id.$status_key.'" style="font-size:100%; width:95%; margin: 0px 0px 0px; padding-left:0px">';
													$return_str .= '<input class="job-rows grouped-jobs-'.$engineer_details->engineer_id.'" id="job-check'.$engineer_details->engineer_id.$status_key.'" type="checkbox" name="jobs_to_reassign['.$status_job->job_id.']" value="'.$status_job->job_id.'" /> &nbsp;'.$status_job->job_type;
												$return_str .= '</label>';																	
											$return_str .= '</td>';
											$return_str .= '<td class="no-border" >'.( date( 'd-m-Y', strtotime( $status_job->job_date ) ) ).'</td>';
											$return_str .= '<td class="no-border" >'.( date( 'd-m-Y', strtotime( $status_job->due_date ) ) ).'</td>';
											$return_str .= '<td class="no-border" >'.$status_job->site_reference.' - '.$status_job->site_name.', '.$status_job->site_postcodes.'</td>';
											$return_str .= '<td class="no-border" ><span class="pull-right" >'.$status_job->job_status.'</span></td>';
										$return_str .= '</tr>';
									}
								} }
							$return_str .= '</table>';
						$return_str .= '</td>';
					$return_str .= '</tr>';
				}
			$return_str .= '</table>';
		}
		
		return $return_str;
	}
	
	
	private function load_engineer_resource( $availble_engineers = false ){
		
		$return_str = '';
		
		if(  !empty( $availble_engineers ) ){
			foreach( $availble_engineers as $engineer_id => $engineer_resource ){
				$return_str .= '<div class="panel panel-default" >';			
					$return_str .= '<div class="user-element panel-body">';
						$return_str .= '<div class="row">';
							$return_str .= '<div class="col-md-12">';
								$return_str .= '<input class="assigned-engineer pull-left pointer" id="rad'.$engineer_id.'" type="radio" name="assigned_to" value="'.$engineer_id.'" />';
								$return_str .= '<label class="pointer text-bold engineer-row" for="rad'.$engineer_id.'" style="width:95%; margin: 0px 0px 5px; padding-left:5px">';
									$return_str .= '<span>'.$engineer_resource->person.'<span> <span class="pull-right text-lightweight small" ><span >Used: '. ( number_format( $engineer_resource->availability->booked_slots, 2 ) + 0 ).'hrs</span> &nbsp;<span>Free: '. ( number_format( ( $engineer_resource->availability->actual_slots - $engineer_resource->availability->booked_slots ), 2 ) + 0 ) .'hrs</span></span>';
									$return_str .= '<div class="text-lightweight small">'.$engineer_resource->home_address.'</div>';
								$return_str .= '</label>';
							$return_str .= '</div>';
						$return_str .= '</div>';
					$return_str .= '</div>';
				$return_str .= '</div>';
			}
		}
		return $return_str;
		
	}
	
	private function load_job_types_options( $job_type_options = false ){
		$return_str = '';
		if( !empty( $job_type_options ) ){
			foreach( $job_type_options as $k => $job_type ){
				$return_str .= '<div class="col-md-6 filter-item">';	
					$return_str .= '<input id="fil-jobtype-'.$k.'" type="checkbox" class="filter-checkbox job-type-filters" value="'.$job_type->job_type_id.'" >';				
					$return_str .= '<label for = "fil-jobtype-'.$k.'" class="filter-label">'.ucwords( $job_type->job_type ).'</label>';				
				$return_str .= '</div>';				
			}
		}
		return $return_str;
	}
	
	private function load_region_options( $region_options = false ){
		$return_str = '';
		if( !empty( $region_options ) ){
			foreach( $region_options as $key => $region ){
				$return_str .= '<div class="col-md-6 filter-item">';	
					$return_str .= '<input id="fil-region-'.$key.'" type="checkbox" class="filter-checkbox region-filters" value="'.$region->region_id.'" >';				
					$return_str .= '<label for = "fil-region-'.$key.'" class="filter-label">'.ucwords( $region->region_name ).'</label>';				
				$return_str .= '</div>';				
			}
		}
		return $return_str;
	}
	
	
	/**
	* Jobs Overview
	*/
	public function overview( $job_id = false ){

		if( $job_id ){
			redirect('webapp/job/profile/'.$job_id, 'refresh');
		}

		#Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {

			$postdata				= ['account_id'=>$this->user->account_id, 'job_date'=>date('Y-m-d')];
			
			$tracking_statuses		  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_tracking_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['tracking_statuses'] 	= ( isset( $tracking_statuses->job_tracking_statuses ) ) ? $tracking_statuses->job_tracking_statuses : null;

			$job_statuses		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['job_statuses']	= ( isset($job_statuses->job_statuses) ) ? $job_statuses->job_statuses : null;

			$job_types				= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_types', ['account_id'=>$this->user->account_id,'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
			$data['job_types']		= ( isset( $job_types->job_types ) ) ? $job_types->job_types : null;

			$reactive_job_types			= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_types', ['account_id'=>$this->user->account_id,'limit'=>-1, 'where'=>['is_reactive'=>1 ] ], ['auth_token'=>$this->auth_token], true );
			$data['reactive_job_types'] = ( isset( $reactive_job_types->job_types ) ) ? $reactive_job_types->job_types : null;

			$scheduled_job_types			= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_types', ['account_id'=>$this->user->account_id,'limit'=>-1, 'where'=>['is_scheduled'=>1 ] ], ['auth_token'=>$this->auth_token], true );
			$data['scheduled_job_types'] = ( isset( $scheduled_job_types->job_types ) ) ? $scheduled_job_types->job_types : null;

			$regions 				= $this->webapp_service->api_dispatcher( $this->api_end_point.'diary/regions', [ 'account_id'=>$this->user->account_id, 'limit'=>-1 ], [ 'auth_token'=>$this->auth_token ], true );
			$data['regions']		= ( isset( $regions->regions ) ) ? $regions->regions : null;

			$contracts	  			= $this->webapp_service->api_dispatcher( $this->api_end_point.'contract/contracts', ['account_id'=>$this->user->account_id, 'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
			$data['contracts']		= ( isset( $contracts->contract) ) ? $contracts->contract : null;

			$operatives		  	  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'user/field_operatives', [ 'account_id'=>$this->user->account_id, 'where'=>['include_admins'=>1], 'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
			$data['operatives']   	= ( isset( $operatives->field_operatives ) ) ? $operatives->field_operatives : null;

			$disciplines			= $this->webapp_service->api_dispatcher( $this->api_end_point.'discipline/account_disciplines', ['account_id'=>$this->user->account_id,'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
			$data['disciplines']	= ( isset( $disciplines->account_disciplines ) ) ? $disciplines->account_disciplines : null;

			$data['searchable_fields'] = array_to_object( [
				[ 'table_name' => 'addresses',  'column_name' 	=> 'postcode', 'column_label'	=> 'Postcode' ],
				[ 'table_name' => 'job', 	  'column_name' 	=> 'job_id', 'column_label'		=> 'Job ID' ],
				/*[ 'table_name' => 'customer', 'column_name' 	=> 'customer_id', 'column_label'=> 'Customer ID' ]*/
				[ 'table_name' => 'customer', 'column_name' 	=> 'customer_last_name', 'column_label'=> 'Customer Surname' ]
			] );


			$data['discipline_id']	= !empty( $this->input->get( 'discipline_id' ) ) 	? $this->input->get( 'discipline_id' ) : false;
			$data['overdue_jobs']	= !empty( $this->input->get( 'overdue_jobs' ) ) 	? $this->input->get( 'overdue_jobs' ) : false;
			$data['date_range']		= !empty( $this->input->get( 'date_range' ) ) 		? $this->input->get( 'date_range' ) : false;
			$range_dates 			= $this->dates_from_date_range( $this->user->account_id, $data['date_range'] );

			$data['date_from']		= !empty( $this->input->get( 'date_from' ) ) 		? $this->input->get( 'date_from' ) 	: ( !empty( $range_dates->date_from ) 	? $range_dates->date_from 	: false );
			$data['date_to']		= !empty( $this->input->get( 'date_to' ) ) 			? $this->input->get( 'date_to' ) 	: ( !empty( $range_dates->date_to ) 	? $range_dates->date_to 	: false );

			if(isset($_COOKIE['dev'])){
				dd($data);
			}

			$this->_render_webpage( 'job/overview', $data );
		}
	}
	
	
	/*
	* Job Search
	*/
	public function job_search( $page = 'details' ){

		// dd($this->input->post());

		$return_data = '';

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){

			$return_data .= $this->config->item( 'ajax_access_denied' );

		}else{

			# Setup search parameters
			$job_id   		= ( $this->input->post( 'job_id' ) ) ? $this->input->post( 'job_id' ) : false;
			$search_term   	= ( $this->input->post( 'search_term' ) ) ? $this->input->post( 'search_term' ) : false;
			$where   	   	= ( $this->input->post( 'where' ) ) ? $this->input->post( 'where' ) : false;
			$limit		   	= ( !empty( $where['limit'] ) )  ? $where['limit']  : DEFAULT_LIMIT;
			$start_index   	= ( $this->input->post( 'start_index' ) ) ? $this->input->post( 'start_index' ) : DEFAULT_OFFSET;
			$offset		   	= ( !empty( $start_index ) ) ? ( ( $start_index - 1 ) * $limit ) : 0;
			$order_by	   	= ( $this->input->post( 'order_by' ) ) ? $this->input->post( 'order_by' ) : false;

			#prepare postdata
			$postdata = [
				'account_id'	=> $this->user->account_id,
				'job_id'		=> $job_id,
				'search_term'	=> $search_term,
				'where'			=> $where,
				'order_by'		=> $order_by,
				'limit'			=> $limit,
				'offset'		=> $offset
			];

			# clean up date where and from before query
			// if( $where['date_from'] == 'DD/MM/YY') {
			// 	dd($this->input->post());
			// 	$where['date_from'] = null;
			// }

			// if( $where['date_to'] == 'DD/MM/YY') {
			// 	$where['date_to'] = null;
			// }

			if(isset($where['date_from'])){
				if( $where['date_from'] == 'DD/MM/YY' ||  empty($where['date_from'])) {
					// throw new Exception('date_from: I am triggered');
					// dd('date_from: I am triggered');
					unset($where['date_from']);
				}
			}

			if(isset($where['date_to'])){
				if( $where['date_to'] == 'DD/MM/YY' ||  empty($where['date_to'])) {
					// throw new Exception('date_to: I am triggered');
					// dd('date_to: I am triggered');
					unset($where['date_to']);
				}
			}

			// if( $where['date_from'] == 'DD/MM/YY' || $where['date_to'] == 'DD/MM/YY') {
			// 	unset($where['date_from']);
			// 	unset($where['date_to']);
			// 	throw new Exception('I am triggered');
			// }

		
 
			$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_search', $postdata, ['auth_token'=>$this->auth_token], true );

			$job			= ( isset( $search_result->job ) ) ? $search_result->job : null;

			if( !empty( $job ) ){

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

				$return_data = $this->load_job_search_results( $job );
				if( !empty($pagination) ){
					$return_data .= '<tr><td colspan="9" style="padding: 0;">';
						$return_data .= $page_display.$pagination;
					$return_data .= '</td></tr>';
				}
			}else{
				$return_data .= '<tr><td colspan="9" style="padding: 0;"><br/>';
					$return_data .= ( isset($search_result->message) ) ? $search_result->message : 'No records found';
				$return_data .= '</td></tr>';
			}
		}

		print_r( $return_data );
		die();
	}

	/*
	* Prepare jobs views
	*/
	private function load_job_search_results( $jobs_data = false ){
		$return_data = '';
		if( !empty( $jobs_data ) ){
			foreach( $jobs_data as $k => $job_details ){
				
				$trimmed_contract_name = $job_details->contract_name;
				if( strlen( $job_details->contract_name ) > 28 ) {
					$trimmed_contract_name = explode( "\n", wordwrap( $job_details->contract_name, 28 ));
					$trimmed_contract_name = $trimmed_contract_name[0] . '...';
				}

				$return_data .= '<tr>';
					$return_data .= '<td><a href="'.base_url('/webapp/job/profile/'.$job_details->job_id).'" >'.$job_details->job_id.'</a>&nbsp;&nbsp;&nbsp;<a target="_blank" href="'.base_url('/webapp/job/profile/'.$job_details->job_id).'" ><img style="width:30px;width: 10px; position: relative; top: -3px;" src="'.( base_url( 'assets/images/dashboard-icons/pop-up.png' ) ).'" /></a><small title="This is an uploaded Job" class="" style="font-size:80%; display:'.( ( $job_details->is_uploaded == 1 ) ? 'inline-block' : 'none' ).'"><i class="far fa-arrow-alt-circle-up"></i></small></td>';
					$return_data .= '<td><a style="font-weight: 500;" href="'.base_url('/webapp/job/profile/'.$job_details->job_id).'" >'.$job_details->job_type.'</a></td>';
					$return_data .= '<td>'.( ( valid_date( $job_details->job_date ) ) ? date( 'd-m-Y', strtotime( $job_details->job_date ) ) : '' ).'</td>';
					$return_data .= '<td><span class="context-menu" title="'.$job_details->works_required.'">'.( strlen( $job_details->works_required ) > 30 ? substr( $job_details->works_required , 0, 30 )."..." : $job_details->works_required ).'</span></td>';
					#$return_data .= '<td>'.( ( valid_date( $job_details->created_on ) ) ? date( 'd-m-Y H:i:s', strtotime( $job_details->created_on ) ) : '' ).'</td>';
					$return_data .= '<td>'.( ( !empty( $job_details->site_name ) ) ? $job_details->site_name : "" ).( ( !empty( $job_details->postcode ) ) ? ', '.$job_details->postcode : "" ).'</td>';
					$return_data .= '<td>'.$job_details->assignee.'</td>';
					$return_data .= '<td>'.$job_details->job_status.'</td>';
					#$return_data .= '<td>'.$job_details->job_tracking_status.'</td>';
					$return_data .= '<td>'.( !empty( $job_details->discipline_name ) ? $job_details->discipline_name : '' ).'</td>';
					$return_data .= '<td>'.$job_details->region_name.'</td>';
					#$return_data .= '<td><span class="pointer" title="'.$job_details->contract_name.'">'.$job_details->contract_name.'</span></td>';
				$return_data .= '</tr>';
			}

			if( !empty($pagination) ){
				$return_data .= '<tr><td colspan="9" style="padding: 0; font-weight:300">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
		}else{
			$return_data .= '<tr><td colspan="9"><br/>'.$this->config->item("no_records").'</td></tr>';
		}
		return $return_data;
	}


	/*
	* Exact Match Search
	*/
	public function advanced_job_search( $search_fields = false ){

		$return_data = [
			'status'		=> 0,
			'search_results'=> null,
			'status_msg'	=> 'Invalid paramaters'
		];

		if( $this->input->post() ){
			$postdata 	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$search		= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/advanced_job_search', $postdata, ['auth_token'=>$this->auth_token], true );
			$result		= ( isset( $search->jobs ) ) 	? $search->jobs : null;
			$message	= ( isset( $search->message ) ) ? $search->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_str = $this->load_search_results( $result );
				$return_data['status'] 	  		= 1;
				$return_data['search_results'] 	= $return_str;
			}
			$return_data['status_msg'] = $message;
		}
		print_r( json_encode( $return_data ) );
		die();
	}

	private function load_search_results( $search_results = false ){
		$return_data = '';
		if( !empty( $search_results ) ){
			foreach( $search_results as $k => $job_details ){
				
				$trimmed_contract_name = $job_details->contract_name;
				if( strlen( $job_details->contract_name ) > 28 ) {
					$trimmed_contract_name = explode( "\n", wordwrap( $job_details->contract_name, 28 ));
					$trimmed_contract_name = $trimmed_contract_name[0] . '...';
				}

				$return_data .= '<tr>';
					$return_data .= '<td><a href="'.base_url('/webapp/job/profile/'.$job_details->job_id).'" >'.$job_details->job_id.'</a> &nbsp;&nbsp;<small title="This is an uploaded Job" class="" style="font-size:80%; display:'.( ( $job_details->is_uploaded == 1 ) ? 'inline-block' : 'none' ).'"><i class="far fa-arrow-alt-circle-up"></i></small></td>';
					$return_data .= '<td><a style="font-weight: 500;" href="'.base_url('/webapp/job/profile/'.$job_details->job_id).'" >'.$job_details->job_type.'</a></td>';
					$return_data .= '<td>'.( ( valid_date( $job_details->job_date ) ) ? date( 'd-m-Y', strtotime( $job_details->job_date ) ) : '' ).'</td>';
					$return_data .= '<td><span class="context-menu" title="'.$job_details->works_required.'">'.( strlen( $job_details->works_required ) > 255 ? substr( $job_details->works_required , 0, 255 )."..." : $job_details->works_required ).'</span></td>';
					$return_data .= '<td>'.( ( !empty( $job_details->site_name ) ) ? $job_details->site_name : "" ).( ( !empty( $job_details->postcode ) ) ? ', '.$job_details->postcode : "" ).'</td>';
					$return_data .= '<td>'.$job_details->assignee.'</td>';
					$return_data .= '<td>'.$job_details->job_status.'</td>';
					$return_data .= '<td>'.( !empty( $job_details->discipline_name ) ? $job_details->discipline_name : '' ).'</td>';
					$return_data .= '<td>'.$job_details->region_name.'</td>';
				$return_data .= '</tr>';
			}
		}
		return $return_data;
	}
	
	
	/** Bulk Job Reassign **/
	public function assign( $contract_id = false, $job_date = false, $postcode_area = false, $job_type_id = false, $region_id = false ){
		
		redirect( 'webapp/job/bulk_assign', 'refresh' );
		
		if( !$this->identity() ){
			redirect('webapp/user/login', 'refresh');
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'details' );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			
			if( in_array( $this->user->user_type_id, EXTERNAL_USER_TYPES ) ){
				$linked_contract		= $this->webapp_service->api_dispatcher( $this->api_end_point.'contract/linked_people', ['account_id'=>$this->user->account_id, 'where'=>['person_id'=>$this->user->id] ], ['auth_token'=>$this->auth_token], true );
				$data['linked_contract']= ( isset( $linked_contract->people ) ) ? $linked_contract->people : false;
				$linked_contract_id		= !empty( $data['linked_contract'] ) 	? array_unique( array_column( $data['linked_contract'], 'contract_id' ) ) : false;
				$linked_contract_id		= !empty( $linked_contract_id[0] ) 		? $linked_contract_id[0]	: false;
			}
			
			$contract_id 		= !empty( $linked_contract_id ) ? $linked_contract_id : ( ( $this->input->post( 'contract_id' ) ) 	? $this->input->post( 'contract_id' ) 	: ( ( $this->input->get( 'contract_id' ) ) ? $this->input->get( 'contract_id' ) : $contract_id ) );
			$job_date 			= ( $this->input->post( 'job_date' ) ) 		? $this->input->post( 'job_date' ) 	: ( ( $this->input->get( 'job_date' ) ) ? $this->input->get( 'job_date' ) : date( 'Y-m-d' ) );
			$due_date 			= ( $this->input->post( 'due_date' ) ) 		? $this->input->post( 'due_date' ) 	: ( ( $this->input->get( 'due_date' ) ) ? $this->input->get( 'due_date' ) : date( 'Y-m-d' ) );
			$job_type_id 		= ( $this->input->get( 'job_type_id' ) ) 	? $this->input->get( 'job_type_id' ) 	: ( !empty( $job_type_id )		? $job_type_id : null );
			$region_id 			= ( $this->input->get( 'region_id' ) ) 		? $this->input->get( 'region_id' ) 		: ( !empty( $region_id ) 		? $region_id : null );
			$include_blank_dates= ( $this->input->post( 'contract_id' ) ) 	? $this->input->post( 'include_blank_dates' ) 	: ( ( $this->input->get( 'include_blank_dates' ) ) ? $this->input->get( 'include_blank_dates' ) : 0 );
			
			$dateObj 			= new DateTime( $due_date ); 
			$last_day_of_month 	= $dateObj->format( 'Y-m-t' );
			$processedDueDate	= ( ( date( 'd-m-Y', strtotime( $last_day_of_month ) ) > date( 'd-m-Y', strtotime( $due_date ) ) ) ? date( 'd-m-Y', strtotime( $last_day_of_month ) ) : date( 'd-m-Y', strtotime( $due_date ) ) );
			#$due_date			= date( 'Y-m-d', strtotime( $processedDueDate ) );
			$due_date			= date( 'Y-m-d', strtotime( $due_date ) );
			
			$ref_date 			= date( 'Y-m-d', strtotime( $job_date ) );
			$where = [
				'contract_id' 	=> $contract_id,
				'job_date' 		=> $ref_date,
				'ref_date' 		=> $ref_date,
				'job_type_id' 	=> $job_type_id,
				'region_id' 	=> $region_id,
				'due_date' 		=> $due_date
			];

			$job_types_where = [
				'contract_id' 	=> $contract_id
			];
			
			$job_types						= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_types', ['account_id'=>$this->user->account_id, 'where'=>$job_types_where, 'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
			$data['job_types']				= ( isset( $job_types->job_types ) ) ? $job_types->job_types : null;
			
			$regions 						= $this->webapp_service->api_dispatcher( $this->api_end_point.'diary/regions', [ 'account_id'=>$this->user->account_id, 'limit'=>-1 ], [ 'auth_token'=>$this->auth_token ], true );
			$data['regions']				= ( isset( $regions->regions ) ) ? $regions->regions : null;
			
			$contract_id_filter				= !empty( $linked_contract_id ) ? $linked_contract_id : false;
			
			$contracts	  					= $this->webapp_service->api_dispatcher( $this->api_end_point.'contract/contracts', ['account_id'=>$this->user->account_id, 'contract_id'=>$contract_id_filter, 'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
			$data['contracts']				= ( isset( $contracts->contract) ) ? $contracts->contract : null;

			$data['job_date']				=  date( 'd-m-Y', strtotime( $job_date ) );
			$data['due_date']				=  date( 'd-m-Y', strtotime( $due_date ) );

			$data['contract_id']			=  $contract_id;
			$data['include_blank_dates']	=  $include_blank_dates;
			
			$this->_render_webpage( 'job/inc/assign_jobs', $data );
		}
		
	}
	
	
	/** 
	* Fetch Un-Assigned Jobs Data
	*/
	public function fetch_un_assigned_jobs_data( $contract_id = false, $job_date = false, $job_type_id = false, $region_id = false, $grouped = 1, $show_blanks = false ){

		$contract_id 	= ( $this->input->post( 'contract_id' ) ) 	? $this->input->post( 'contract_id' ) 	: ( ( $this->input->get( 'contract_id' ) ) ? $this->input->get( 'contract_id' ) : $contract_id );
		$job_date 		= ( $this->input->post( 'job_date' ) ) 		? $this->input->post( 'job_date' ) 		: ( !empty( $job_date ) 		? $job_date : null );
		$due_date 		= ( $this->input->post( 'due_date' ) ) 		? $this->input->post( 'due_date' ) 		: ( !empty( $due_date ) 		? $due_date : null );
		
		$job_type_id 	= ( $this->input->post( 'job_type_id' ) ) 	? $this->input->post( 'job_type_id' ) 	: ( !empty( $job_type_id )		? $job_type_id : null );
		$region_id 		= ( $this->input->post( 'region_id' ) ) 	? $this->input->post( 'region_id' ) 	: ( !empty( $region_id ) 		? $region_id : null );
		$job_postcode 	= ( $this->input->post( 'job_postcode' ) ) 	? urldecode( $this->input->post( 'job_postcode' ) ) 	: ( !empty( $job_postcode ) 	? urldecode( $job_postcode ) : false );
		$grouped 		= ( $this->input->post( 'grouped' ) ) 		? urldecode( $this->input->post( 'grouped' ) ) 	: ( !empty( $grouped ) 			? $grouped 			: null );
		$include_blank_dates = ( $this->input->post( 'include_blank_dates' ) ) ? $this->input->post( 'include_blank_dates' ) : ( !empty( $include_blank_dates ) ? $include_blank_dates : false );

		$dateObj 			= new DateTime( $due_date ); 
		$last_day_of_month 	= $dateObj->format( 'Y-m-t' );
		$processedDueDate	= ( ( date( 'd-m-Y', strtotime( $last_day_of_month ) ) > date( 'd-m-Y', strtotime( $due_date ) ) ) ? date( 'd-m-Y', strtotime( $last_day_of_month ) ) : date( 'd-m-Y', strtotime( $due_date ) ) );
		#$due_date			= date( 'Y-m-d', strtotime( $processedDueDate ) );

		$return_data = [
			'status'			=> 0,
			'un_assigned_jobs'	=> null,
			'status_msg'		=> 'Invalid paramaters'
		];
		
		if( !empty( $job_date ) ){
			$job_date 			= date( 'Y-m-d', strtotime( $job_date ) );
			$due_date 			= !empty( $due_date ) ? date( 'Y-m-d', strtotime( $due_date ) ) : null;
			$where = [
				'contract_id' 		=> $contract_id,
				'job_date' 			=> $job_date,
				'ref_date' 			=> $job_date,
				'due_date' 			=> $due_date,
				'job_type_id' 		=> $job_type_id,
				'region_id' 		=> $region_id,
				'grouped' 	 		=> $grouped,
				'include_blank_dates' => $include_blank_dates,
			];

			if( $this->user->is_primary_user && !$this->user->is_admin ){
				$where['associated_user_id']     = $this->user->id;
			}
			
			$available_resource	= $this->webapp_service->api_dispatcher( $this->api_end_point.'diary/available_engineer_resource', ['account_id'=>$this->user->account_id,'where'=>$where ], ['auth_token'=>$this->auth_token], true );
			$engineers_result	= ( isset( $available_resource->available_resource ) ) ? $available_resource->available_resource : null;

			$jobs_data			= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/un_assigned_jobs', ['account_id'=>$this->user->account_id,'where'=>$where ], ['auth_token'=>$this->auth_token], true );
			
			$jobs_result		= ( isset( $jobs_data->un_assigned_jobs ) ) ? $jobs_data->un_assigned_jobs : null;

			$job_types			= ( isset( $jobs_data->job_types ) ) ? $jobs_data->job_types : null;
			$regions			= ( isset( $jobs_data->regions ) ) ? $jobs_data->regions : null;
			$message			= ( isset( $jobs_data->message ) ) ? $jobs_data->message : null;

			if( !empty( $jobs_result ) ){
				$un_assigned_jobs 		= !empty( $jobs_result->un_assigned_jobs ) 	? $jobs_result->un_assigned_jobs 		: $jobs_result;
				$un_assigned_jobs 		= $this->load_un_assigned_jobs( $un_assigned_jobs, $grouped );
				$engineer_resource	= !empty( $engineers_result->{$job_date} ) 	? $engineers_result->{$job_date} 	: false;
				$available_resource = $this->load_engineer_resource( $engineer_resource );
				
				$job_type_options 	= $this->load_job_types_options( $job_types );
				$region_options 	= $this->load_region_options( $regions );
				
				$return_data['status'] 	  			= 1;
				$return_data['un_assigned_jobs'] 	= $un_assigned_jobs;
				$return_data['available_resource'] 	= $available_resource;
				$return_data['job_type_options'] 	= $job_type_options;
				$return_data['region_options'] 		= $region_options;
				$return_data['job_date'] 			= date( 'd-m-Y', strtotime( $job_date ) );
				$return_data['due_date'] 			= date( 'd-m-Y', strtotime( $due_date ) );
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	private function load_un_assigned_jobs( $un_assigned_jobs = false, $grouped = false ){
		
		$return_str = '';
		
		if( !empty( $un_assigned_jobs ) ){
			if( !empty( $grouped ) ){
				$return_str .= '<table class="table" style="width:100%;">';
				foreach( $un_assigned_jobs as $key => $job_type_details ){
					$return_str .= '<tr class="user-element pointer" >';
						$return_str .= '<th width="3%" ><input class="check-all-jobs grouped-jobs-'.$job_type_details->job_type_id.'" data-job_type_ref="'.$key.'" id="j-type-'.$key.'" type="checkbox" /></th>';
						$return_str .= '<th width="70%" ><label class="pointer un-assigned-row text-normalweight" data-job_type_id="'.$job_type_details->job_type_id.'" for="j-type-'.$key.'" >'.$job_type_details->job_type.'</label></th>';
						$return_str .= '<th width="27%" ><span class="pull-right" ><a class="pointer un-assigned-row" data-job_type_id="'.$job_type_details->job_type_id.'" >'.( !empty( $job_type_details->total_jobs ) ? ( $job_type_details->total_jobs ) : 0 ).' Job'.( ( $job_type_details->total_jobs != 1 ) ? "s" : "" ).' </a></span></th>';
					$return_str .= '</tr>';

					$return_str .= '<tr class="job-type-jobs job-type-jobs-'.$job_type_details->job_type_id.'" style="display:none" >';
						$return_str .= '<td colspan="3" >';
							$return_str .= '<table class="table no-border" style="width:100%; font-size:100%;">';
								if( $job_type_details->jobs_list ) {
									
									$return_str .= '<tr style="border-bottom: 1px dashed #ccc">';
										$return_str .= '<th width="25%" style="padding-left:30px;" >JOB TYPE</th>';
										$return_str .= '<th width="10%" >JOB DATE</th>';
										$return_str .= '<th width="10%" >DUE DATE</th>';
										$return_str .= '<th width="25%" >SITE ID / SITE NAME</th>';
										$return_str .= '<th width="10%" >JOB STATUS</th>';
										$return_str .= '<th width="10%" ><span class="pull-right" >JOB DURATION</span></th>';
									$return_str .= '</tr>';
								
									foreach( $job_type_details->jobs_list as $job_key => $job_details ){
									
										$return_str .= '<tr class="user-element no-border text-bold">';
											$return_str .= '<td class="no-border" style="padding-left:30px;">';
												$return_str .= '<label class="pointer text-bold" for="job-check'.$job_type_details->job_type_id.$job_key.'" style="font-size:100%; width:95%; margin: 0px 0px 0px; padding-left:0px">';
													$return_str .= '<input class="job-rows grouped-jobs-'.$job_type_details->job_type_id.'" id="job-check'.$job_type_details->job_type_id.$job_key.'" type="checkbox" name="jobs_to_reassign['.$job_details->job_id.']" value="'.$job_details->job_id.'" /> &nbsp;'.$job_details->job_type;
												$return_str .= '</label>';																	
											$return_str .= '</td>';
											$return_str .= '<td class="no-border" >'.( valid_date( $job_details->job_date ) ? date( 'd-m-Y', strtotime( $job_details->job_date ) ) : '<span class="text-red">No Job Date</span>' ).'</td>';
											$return_str .= '<td class="no-border" >'.( valid_date( $job_details->due_date ) ? date( 'd-m-Y', strtotime( $job_details->due_date ) ) : '<span class="text-red">Due Date</span>' ).'</td>';
											$return_str .= '<td class="no-border" ><strong>'.$job_details->site_id.' - '.$job_details->site_name.', '.$job_details->site_postcodes.'</strong></td>';
											$return_str .= '<td class="no-border" >'.$job_details->job_status.'</td>';
											$return_str .= '<td class="no-border" ><span class="pull-right" >'.$job_details->job_duration.' (Hrs)</span></td>';
										$return_str .= '</tr>';
									}
								}
							$return_str .= '</table>';
						$return_str .= '</td>';
					$return_str .= '</tr>';
				}
				$return_str .= '</table>';
			} else {
				$job_type_details = $un_assigned_jobs;
				$return_str .= '<table class="table no-border" style="width:100%; font-size:100%;">';
					foreach( $job_type_details as $job_key => $job_details ){
						$return_str .= '<tr class="user-element no-border">';
							$return_str .= '<td width="30%" class="no-border">';
								$return_str .= '<label class="pointer" for="job-check'.$job_details->job_type_id.$job_key.'" style="font-size:100%; width:95%; margin: 0px 0px 0px; padding-left:0px">';
									$return_str .= '<input class="job-rows grouped-jobs-'.$job_details->job_type_id.'" id="job-check'.$job_details->job_type_id.$job_key.'" type="checkbox" name="jobs_to_reassign['.$job_details->job_id.']" value="'.$job_details->job_id.'" /> &nbsp;'.$job_details->job_type;
								$return_str .= '</label>';																	
							$return_str .= '</td>';
							$return_str .= '<td width="15%" class="no-border" >'.( valid_date( $job_details->due_date ) ? date( 'd-m-Y', strtotime( $job_details->due_date ) ) : '<span class="text-red">Due Date</span>' ).'</td>';
							$return_str .= '<td width="15%" class="no-border" >'.( valid_date( $job_details->job_date ) ? date( 'd-m-Y', strtotime( $job_details->job_date ) ) : '<span class="text-red">No Job Date</span>' ).'</td>';
							$return_str .= '<td width="20%" class="no-border" >'.$job_details->address_line_1.', '.$job_details->address_city.' '.$job_details->job_postcode.'</td>';
							$return_str .= '<td width="10%" class="no-border" >'.$job_details->job_status.'</td>';
							$return_str .= '<td width="10%" class="no-border" ><span class="pull-right" >'.( !empty( $job_details->job_duration ) ? $job_details->job_duration.' hrs' : '' ).'</span></td>';
						$return_str .= '</tr>';
					}
				$return_str .= '</table>';
			}
		}
		
		return $return_str;
	}
	
	
	/** Get all Evidocs required for Multiple for Building Schedules **/
	public function fetch_building_evidocs_display( $page = 'schedules' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			
			$frequency_id 	= !empty( $postdata['where']['frequency_id'] ) ? $postdata['where']['frequency_id'] : '';
			$frequency_name = !empty( $postdata['where']['frequency_name'] ) ? urldecode( $postdata['where']['frequency_name'] ) : 'this Inspection';
			$sites_data 	= !empty( $postdata['where']['sites_data'] ) ? $postdata['where']['sites_data'] : false;
			unset( $postdata['where']['frequency_name'],  $postdata['where']['sites_data'] );
			$evidoc_types	= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/evidoc_types', $postdata, ['auth_token'=>$this->auth_token], true );
			$result			= ( isset( $evidoc_types->evidoc_types ) )  	? $evidoc_types->evidoc_types : null;

			$message		= ( isset( $evidoc_types->message ) ) 			? $evidoc_types->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) && !empty( $sites_data ) ){
				#$sites_data = $this->reorganize_assets_data( $sites_data );
				$return_data['status'] 		 = 1;
				$return_data['evidocs_data'] = $this->building_evidocs_display( $sites_data, $result );
			} else {
				$return_data['status_msg'] = '<strong>No data found due to one or all of the reasons below:-</strong><br/><br/>';
				$return_data['status_msg'] .= '- No EviDocs matching the selected Frequency! <a href="'.base_url( 'webapp/audit/evidoc_names' ).'" target="_blank" title="Click here to view existing EviDocs or create a new one!" >Manage</a><br/><br/>';
				$return_data['status_msg'] .= '- The existing EviDocs linked to <a href="'.base_url( 'webapp/audit/evidoc_names?frequency_id='.$frequency_id ).'" target="_blank" title="Click here to go to the setup page!" >'.$frequency_name.'</a> do not have any Questions yet!<br/><br/>';
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	/** Load Multi Site Display View**/
	private function building_evidocs_display( $multi_sites_data = false, $evidoc_types = false ){

		$return_data = '';

		if( !empty( $multi_sites_data ) && !empty( $evidoc_types ) ){

			$return_data .= '<div>';
			foreach( $multi_sites_data as $key => $site_data ){
				$site_data = (object) $site_data;
				$site_name_ref = strip_all_whitespace( $site_data->site_name );
				$return_data .= '<div class="col-md-12" >';
					$return_data .= '<div class="alert bg-blue pointer" >';
						$return_data .= '<div class="row">';
							$return_data .= '<div class="col-md-12 col-sm-12 col-xs-12">';
								$return_data .= '<div class="row">';
									$return_data .= '<div class="col-md-4 col-sm-4">';
										$return_data .= '<div><strong>'.$site_data->frequency_name.'</strong></div>';
									$return_data .= '</div>';
									
									$return_data .= '<div class="col-md-4 col-sm-4">';
										$return_data .= '<span><label class="text-white pointer"  ><input name="site_id[]" id="group-chk-'.$site_name_ref.'" data-site_id="'.$site_data->site_id.'" data-site_name="'.$site_data->site_name.'" data-site_postcode="'.$site_data->site_postcode.'" data-address_id="'.$site_data->address_id.'" class="group-check-all" data-group_check_id="group-chk-'.$site_name_ref.'" checked type="checkbox" value="'.$site_data->site_id.'" > &nbsp;<strong>'.$site_data->site_name.', '.strtoupper( $site_data->site_postcode ).'</strong></label></span>';
									$return_data .= '</div>';
									
									$return_data .= '<div class="col-md-4 col-sm-4">';
										$return_data .= '<div><strong>Select Evidoc Type</strong>';
											$return_data .= '<select id="evidoc'.$site_data->site_id.'" name="evidoc_type_id" class="form-control evidoc-type-select" >';
												foreach( $evidoc_types as $k => $evidoc_type ){
													$return_data .= '<option value="'.$evidoc_type->audit_type_id.'" data-evidoc_type_id="'.$evidoc_type->audit_type_id.'" data-evidoc_type="'.$evidoc_type->audit_type.'" >'.$evidoc_type->audit_type.'</option>';
												}
											$return_data .= '</select>';
										$return_data .= '</div>';
									$return_data .= '</div>';
								$return_data .= '</div>';
							$return_data .= '</div>';
						$return_data .= '</div>';
					$return_data .= '</div>';

				$return_data .= '</div>';
			}
			$return_data .= '</div>';

		} else {

			$return_data .= '<div>'.$this->config->item( 'no_records' ).'</div>';

		}

		return $return_data;
	}
	
	
	/** Get all Job Types required for Multi-Building Schedules display **/
	public function fetch_job_types_multi_building_display( $page = 'schedules' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$sites_data 	= !empty( $postdata['where']['sites_data'] ) ? $postdata['where']['sites_data'] : false;
			unset( $postdata['where']['sites_data'] );

			$job_types		= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_types', $postdata, ['auth_token'=>$this->auth_token], true );
			$result			= ( isset( $job_types->job_types ) )  	? $job_types->job_types : null;
			$message		= ( isset( $job_types->message ) ) 		? $job_types->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) && !empty( $sites_data ) ){
				#$sites_data = $this->reorganize_sites_data( $sites_data );
				$return_data['status'] 		 	= 1;
				$return_data['job_types_data'] 	= $this->load_job_types_multi_building_display( $sites_data, $result );
			} else {
				$return_data['status_msg'] = 'There\'s currently no Job Types matching the selected EviDoc!';
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	//Load Job Types for multiple Schedules
	private function load_job_types_multi_building_display( $multi_sites_data = false, $job_types = false ){
		$return_data = '';
		if( !empty( $multi_sites_data ) && !empty( $job_types ) ){

			$return_data .= '<div>';
			foreach( $multi_sites_data as $key => $site_data ){
				$site_data = (object) $site_data;
				$site_name_ref = strip_all_whitespace( $site_data->site_name );
				$return_data .= '<div class="col-md-12" >';
					$return_data .= '<div class="alert bg-blue pointer" >';
						$return_data .= '<div class="row">';
							$return_data .= '<div class="col-md-12 col-sm-12 col-xs-12">';
								$return_data .= '<div class="row">';
									$return_data .= '<div class="col-md-3 col-sm-3">';
										$return_data .= '<div><strong>'.$site_data->frequency_name.'</strong></div>';
									$return_data .= '</div>';
									$return_data .= '<div class="col-md-3 col-sm-3">';
										$return_data .= '<span><label class="text-white pointer"  ><input name="site_id[]" id="group-chk-'.$site_name_ref.'" data-site_name="'.$site_data->site_name.'" data-address_id="'.$site_data->address_id.'" data-site_postcode="'.$site_data->site_postcode.'" class="group-check-all" data-group_check_id="group-chk-'.$site_name_ref.'" checked type="checkbox" value="'.$site_data->site_id.'" > &nbsp;<strong>'.$site_data->site_name.', '.strtoupper( $site_data->site_postcode ).'</strong></label></span>';
									$return_data .= '</div>';
									$return_data .= '<div class="col-md-3 col-sm-3">';
										$return_data .= '<input type="hidden" value="'.$site_data->evidoc_type_id.'" />';
										$return_data .= '<div><strong>'.urldecode( $site_data->evidoc_type ).'</strong></div>';
									$return_data .= '</div>';
									$return_data .= '<div class="col-md-3 col-sm-3">';
										$return_data .= '<div><strong>Select Job Type</strong>';
											$return_data .= '<select id="jobtype'.$site_data->site_id.'" name="job_type_id[]" class="form-control job-type-options" >';
												foreach( $job_types as $k => $job_type ){
													$return_data .= '<option value="'.$job_type->job_type_id.'" data-evidoc_type_id="'.$job_type->evidoc_type_id.'" data-evidoc_type="'.$job_type->audit_type.'" data-job_type_id="'.$job_type->job_type_id.'" data-job_type="'.$job_type->job_type.'" >'.$job_type->job_type.'</option>';
												}
											$return_data .= '</select>';
										$return_data .= '</div>';
									$return_data .= '</div>';
								$return_data .= '</div>';
							$return_data .= '</div>';
						$return_data .= '</div>';
					$return_data .= '</div>';

				$return_data .= '</div>';
			}
			$return_data .= '</div>';

		}else{
			$return_data .= '<div>'.$this->config->item( 'no_records' ).'</div>';
		}
		return $return_data;
	}
	
	
	/** Prepare Building Schedule Placeholders **/
	public function prepare_building_schedule_placeholders( $frequency_id = false ){

		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$schedule_data  = !empty( $postdata['where'] ) ? $postdata['where'] : false;
			$sites_data    = !empty( $schedule_data['sites_data'] ) ? $schedule_data['sites_data'] : false;
			unset( $schedule_data['sites_data'] );

			if( !empty( $schedule_data ) && !empty( $sites_data ) ){
				$return_data['status'] 		 	= 1;
				$return_data['schedules_data'] 	= $this->load_building_schedule_placeholders_view( $schedule_data, $sites_data );
			} else {
				$return_data['status_msg'] = 'There was a problem processing your Schedule data!';
			}
		}

		print_r( json_encode( $return_data ) );
		die();

	}

	//Load Job Types for multiple Building Schedules
	private function load_building_schedule_placeholders_view( $schedule_data = false, $sites_data = false ){
		$return_data = '';
		if( !empty( $schedule_data ) && !empty( $sites_data ) ){
			$sites_data				= array_to_object( $sites_data );
			$contractId  			= $schedule_data['contract_id'];
			$freqeuncyId  			= $schedule_data['frequency_id'];
			$activityName  			= $schedule_data['frequency_name'];
			$activityNameTag  		= $schedule_data['frequency_name'];
			$activityCount 			= $schedule_data['total_activities'];
			$activityInterval 		= $schedule_data['activity_interval'];
			$activity_percentage	= $schedule_data['activity_interval'];
			$dueDate				= date( 'd-m-Y', strtotime( $schedule_data['due_date'] ) );
			$assetCheckCount		= $schedule_data['number_of_checks'];
			$i 						= 1;
			$totalContractActivities= 0;
			$activityName  			= 'Building '.$activityName;

			$return_data .= '<table class="table table-responsive" style="width:100%" >';

				$return_data .= '<input type="hidden" name="frequency_id" value="'.$freqeuncyId.'" />';
				$return_data .= '<input type="hidden" name="schedule_name" value="'.$activityName.'" />';

				for( $i = 1; $i <= $activityCount; $i++ ){
					$return_data .= '<tr>';
						$return_data .=  '<td colspan="4" width="100%" class="bg-grey" ><h4><strong>'.$activityName.' Activity '.$i.'</strong></h4></td>';
					$return_data .= '</tr>';

					// Set Due Date to End of the Month
					$dateObj 			= new DateTime( $dueDate ); 
					$last_day_of_month 	= $dateObj->format( 'Y-m-t' );
					#$last_day_of_month  = date( 'Y-m-t', strtotime( $dueDate. '+ 1 month' ) );
					#$processedDueDate	= ( ( date( 'd-m-Y', strtotime( $last_day_of_month ) ) > date( 'd-m-Y', strtotime( $dueDate ) ) ) ? date( 'd-m-Y', strtotime( $last_day_of_month ) ) : date( 'd-m-Y', strtotime( $dueDate ) ) );

					switch( strtolower( $activityNameTag ) ){
						case 'weekly':
						case 'weekly inspection':
						case 'weekly-inspection':
							$processedDueDate	 = isset( $processedDueDate ) 	 ? date( 'd-m-Y', strtotime( $processedDueDate.' + 7 days' ) ) : date( 'd-m-Y', strtotime( $dueDate ) );
							$processedJobDueDate = isset( $processedJobDueDate ) ? date( 'd-m-Y', strtotime( $processedJobDueDate.' + 7 days' ) ) : date( 'd-m-Y', strtotime( $dueDate ) );
							$activityInterval_counter	 = '+7 days';
							break;
							
						case 'monthly':
						case 'monthly inspection':
						case 'monthly-inspection':
							$activityInterval_counter	 = '+1 month';
							$processedDueDate	 = ( ( date( 'd-m-Y', strtotime( $last_day_of_month ) ) > date( 'd-m-Y', strtotime( $dueDate ) ) ) ? date( 'd-m-Y', strtotime( $last_day_of_month ) ) : date( 'd-m-d', strtotime( $dueDate.' + 28 days' ) ) );
							$processedJobDueDate = isset( $processedJobDueDate ) ? date( 'd-m-Y', strtotime( $processedJobDueDate.' + 1 month' ) ) : date( 'd-m-Y', strtotime( $dueDate ) );
							break;
							
						case 'every4weeks':
						case 'every4weeks inspection':
						case 'every4weeks-inspection':
							$activityInterval_counter	 = '+28 days';
							$processedDueDate	 = ( ( date( 'd-m-Y', strtotime( $last_day_of_month ) ) > date( 'd-m-Y', strtotime( $dueDate ) ) ) ? date( 'd-m-Y', strtotime( $last_day_of_month ) ) : date( 'd-m-d', strtotime( $dueDate.' + 28 days' ) ) );
							$processedJobDueDate = isset( $processedJobDueDate ) ? date( 'd-m-Y', strtotime( $processedJobDueDate.' + 28 days' ) ) : date( 'd-m-Y', strtotime( $dueDate ) );
							break;	
							
						default:
							$processedDueDate	= ( ( date( 'd-m-Y', strtotime( $last_day_of_month ) ) > date( 'd-m-Y', strtotime( $dueDate ) ) ) ? date( 'd-m-Y', strtotime( $last_day_of_month ) ) : date( 'd-m-Y', strtotime( $dueDate ) ) );
							$processedJobDueDate= date( 'd-m-Y', strtotime( $dueDate ) );
							$activityInterval_counter = (int)$activityInterval.' days';
							break;
					}

						$return_data .= '<tr>';
							$return_data .=  '<td  colspan="4" >';
								$return_data .= '<div class="col-md-12" >';

									$return_data .= '<div class="row" >';
										$return_data .=  '<div class="col-md-3" ><strong><small>SITE NAME</small></strong></div>';
										$return_data .=  '<div class="col-md-2" ><strong><small>EVIDOC NAME</small></strong></div>';
										$return_data .=  '<div class="col-md-3" ><strong><small>JOB TYPE</small></strong></div>';
										$return_data .=  '<div class="col-md-2" ><strong><small>JOB DATE</small></strong></div>';
										$return_data .=  '<div class="col-md-2" ><strong><small>DUE DATE</small></strong></div>';
									$return_data .= '</div>';
								$return_data .= '</div>';
							$return_data .= '</td>';
						$return_data .= '</tr>';

						foreach( $sites_data as $key => $site_obj ){

							$return_data .= '<tr>';
								$return_data .=  '<td  colspan="4" >';
									$return_data .= '<div class="col-md-12" >';
									
										$activity_name = ordinal( $i ).' Visit - ' .$activityName; 

										$return_data .= '<input type="hidden" name="schedule_activities[multi]['.$i.']['.$site_obj->site_id.'][site_id]" value="'.$site_obj->site_id.'" />';
										$return_data .= '<input type="hidden" name="schedule_activities[multi]['.$i.']['.$site_obj->site_id.'][contract_id]" value="'.$site_obj->contract_id.'" />';
										$return_data .= '<input type="hidden" name="schedule_activities[multi]['.$i.']['.$site_obj->site_id.'][frequency_id]" value="'.$freqeuncyId.'" />';
										$return_data .= '<input type="hidden" name="schedule_activities[multi]['.$i.']['.$site_obj->site_id.'][evidoc_type_id]" value="'.$site_obj->evidoc_type_id.'" />';
										$return_data .= '<input type="hidden" name="schedule_activities[multi]['.$i.']['.$site_obj->site_id.'][address_id]" value="'.$site_obj->address_id.'" />';
										$return_data .= '<input type="hidden" name="schedule_activities[multi]['.$i.']['.$site_obj->site_id.'][activities_total]" value="'.$activityCount.'" />';
										$return_data .= '<input type="hidden" name="schedule_activities[multi]['.$i.']['.$site_obj->site_id.'][job_type_id]" value="'.$site_obj->job_type_id.'" />';
										$return_data .= '<input type="hidden" name="schedule_activities[multi]['.$i.']['.$site_obj->site_id.'][activity_name]" value="'.$activity_name.'" />';
										$return_data .= '<input type="hidden" name="schedule_activities[multi]['.$i.']['.$site_obj->site_id.'][proportion]" value="" />';
										$return_data .= '<input type="hidden" name="schedule_activities[multi]['.$i.']['.$site_obj->site_id.'][due_date]" value="'.( !empty( $exceptionDate ) ? $exceptionDate : $processedDueDate ).'" />';

										$return_data .= '<div class="row" >';
											$return_data .=  '<div class="col-md-3" >&nbsp;&nbsp;&nbsp;'.$site_obj->site_name.', '.strtoupper( $site_obj->site_postcode ).'</div>';
											$return_data .=  '<div class="col-md-2" >'.urldecode( $site_obj->evidoc_type ).'</div>';
											$return_data .=  '<div class="col-md-3" >'.urldecode( $site_obj->job_type ).'</div>';
											$return_data .=  '<div class="col-md-2" >';
												$return_data .= '<input type="text" class="datepicker form-control" name="schedule_activities[multi]['.$i.']['.$site_obj->site_id.'][job_due_date]" value="'.$processedJobDueDate.'" />';
											$return_data .=  '</div>';												
											$return_data .=  '<div class="col-md-2" >'.$processedDueDate.'</div>';												
										$return_data .= '</div>';

									$return_data .= '</div>';
								$return_data .=  '</td>';
							$return_data .= '</tr>';
							$totalContractActivities++;
							$totalSites[$site_obj->site_id] = $site_obj->site_id;
						}

					$dueDate 		= date( 'd-m-Y', strtotime( $dueDate.' '.$activityInterval_counter ) );

				}
				
				$return_data .= '<tr style="display:none"><td colspan="4" >';
					$return_data .= '<input class="total_sites" id="total_sites" type="hidden" value="'.count( $totalSites ).'" />';
					$return_data .= '<input class="total_activities_due" id="total_activities_due" type="hidden" value="'.$totalContractActivities.'" />';
				$return_data .= '</td></tr>';
				
			$return_data .= '</table>';
		}
		return $return_data;
	}
	
	
	/** Get all Contract Buildings **/
	public function fetch_contract_buildings( $contract_id = false, $page = 'schedules' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$where				= !empty( $this->input->post( 'where' ) ) 	? $this->input->post( 'where' ) : false;
			$contract_id		= !empty( $where['contract_id'] ) 			? $where['contract_id'] 		: $contract_id;
			$frequency_id		= !empty( $where['frequency_id'] ) 			? $where['frequency_id'] 		: false;
			
			$params 			= [
				'account_id' 	=> $this->user->account_id,
				'contract_id' 	=> $contract_id,
				'where'			=> [
					'frequency_id' => $frequency_id
				],
				'limit'			=> SCHEDULE_BUILDINGS_LIMIT
			];
			
			$contract_buildings	= $this->webapp_service->api_dispatcher( $this->api_end_point.'contract/linked_sites', $params, ['auth_token'=>$this->auth_token], true );
			$contract_buildings	= ( isset( $contract_buildings->linked_sites_data ) ) ? $contract_buildings->linked_sites_data : null;

			$message		= ( isset( $buildings->message ) ) 		? $buildings->message : 'Oops! There was an error processing your request.';
			if( !empty( $contract_id ) && !empty( $contract_buildings ) ){
				$return_data['status'] 		 		= 1;
				$return_data['contract_buildings'] 	= $this->load_contract_buildings( $contract_id, $contract_buildings );
			} else {
				$return_data['status_msg'] = '<p>There are currently no Buildings linked to this Contract. Please link them <a target="_blank" href="'. base_url( '/webapp/contract/profile/'.$contract_id.'/linked_sites' ).'" >here</a>. If you\'ve already done that, <a href="" onClick="window.location.reload();" >click here</a> to restart this process.</p>';
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	//Load Contract Buildings
	private function load_contract_buildings( $contract_id = false, $contract_buildings = false ){
		$return_data = '';
		if( !empty( $contract_id ) && !empty( $contract_buildings ) ){
				
			$return_data .= '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">';
				$return_data .= '<div class="small alert bg-blue panel-chk pointer" >';
					$return_data .= '<div class="row">';
						$return_data .= '<div class="col-md-12 col-sm-12 col-xs-12">';
							$return_data .= '<span><label class="text-white pointer text-bold"  ><input id="buildings-chk-all" data-buildings_check="buildings-chk" type="checkbox" > &nbsp;Check / Uncheck all </label></span>';
						$return_data .= '</div>';
					$return_data .= '</div>';
				$return_data .= '</div>';
			$return_data .= '</div>';
		
			foreach( $contract_buildings as $ref => $site_data ){
				$site_ref = strip_all_whitespace( $site_data->site_name.$site_data->site_postcodes );
				$return_data .= '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">';
					$return_data .= '<div class="small alert bg-blue panel-chk pointer" data-site_ref="'.$site_ref.'" >';
						$return_data .= '<div class="row">';
							$return_data .= '<div class="col-md-12 col-sm-12 col-xs-12">';
								$return_data .= '<span><label class="text-white pointer"  ><input name="site_id[]" id="site-chk-'.$site_ref.'" class="buildings-chk site-check-all" data-site_name="'.$site_data->site_name.'" data-site_postcode="'.$site_data->site_postcodes.'" data-address_id="'.$site_data->site_address_id.'" data-site_check_id="site-chk-'.$site_ref.'" type="checkbox" value="'.$site_data->site_id.'" > &nbsp;<small>'.ucwords( $site_data->site_name ).', '.strtoupper( $site_data->site_postcodes ).'</small></label></span>';
								$return_data .= '<span class="pull-right pointer contract-buildings-toggle" data-contract_site_check_ref="'.$site_ref.'" >'. ( ( !empty( $site_data->site_reference ) ) ? $site_data->site_reference : "0" ).'</span>';
								#$return_data .= '<span class="pull-right pointer contract-buildings-toggle" data-contract_site_check_ref="'.$site_ref.'" ><small>'. ( ( !empty( $site_data->schedules_summary ) ) ? count( $site_data->schedules_summary ) : "0" ).' existing Schedules</small></span>';
							$return_data .= '</div>';
						$return_data .= '</div>';
					$return_data .= '</div>';
				$return_data .= '</div>';
			
				$return_data .= '<div class="'.$site_ref.'" style="display:none;" >';
					$return_data .= '<div style="margin-left:15px;" >';
						$return_data .= '<table class="table table-responsive" >';
							$return_data .= '<tr>';
								$return_data .= '<td class="text-bold">Schedule Name</td>';
								$return_data .= '<td class="text-bold">Status</td>';
								$return_data .= '<td class="text-bold">Due Date</td>';
								$return_data .= '<td class="text-bold">Date Created</td>';
							$return_data .= '</tr>';
							if( !empty( $site_data->schedules_summary ) ){ foreach( $site_data->schedules_summary as $key => $schedules_data ){
								$return_data .= '<tr>';
									$return_data .= '<td><a href="'.base_url( '/webapp/job/schedule_profile/'.$schedules_data->schedule_id ).'" target="_blank" >'.ucwords( $schedules_data->schedule_name ).'</a></td>';
									$return_data .= '<td>'.$schedules_data->schedule_status.'</td>';
									$return_data .= '<td>'.valid_date( $schedules_data->first_activity_due_date ) ? date ( 'd-m-Y', strtotime( $schedules_data->first_activity_due_date ) ) : ''.'</td>';
									$return_data .= '<td>'.valid_date( $schedules_data->date_created ) ? date ( 'd-m-Y', strtotime( $schedules_data->date_created ) ) : ''.'</td>';
								$return_data .= '</tr>';
							} }
						$return_data .= '</table>';
					$return_data .= '</div>';
				$return_data .= '</div>';
			}
			
		} else {
			$return_data .= '<div>'.$this->config->item( 'no_records' ).'</div>';
		}
		return $return_data;
	}
	
	/** Pull Tesseract Jobs linked to Sites **/
	public function pull_tesseract_jobs( $site_number = false, $page = 'details' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$site_number = ( $this->input->post( 'site_number' ) ) ? $this->input->post( 'site_number' ) : ( !empty( $site_number ) ? $site_number : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$tesseract_jobs = $this->webapp_service->api_dispatcher( $this->api_end_point.'tesseract/retrieve_site_jobs', $postdata, ['auth_token'=>$this->auth_token], true );
			$result		  = ( isset( $tesseract_jobs->jobs ) ) ? $tesseract_jobs->jobs : null;
			$message	  = ( isset( $tesseract_jobs->message ) ) ? $tesseract_jobs->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 			= 1;
				$return_data['tesseract_jobs'] 	= $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/** Pull Tesseract Jobs linked to Sites **/
	public function fetch_tesseract_jobs_by_site_number( $site_number = false, $page = 'details' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$site_number = ( $this->input->post( 'site_number' ) ) ? $this->input->post( 'site_number' ) : ( !empty( $site_number ) ? $site_number : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$tesseract_jobs = $this->webapp_service->api_dispatcher( $this->api_end_point.'tesseract/retrieve_site_jobs', $postdata, ['auth_token'=>$this->auth_token], true );
			$result		  = ( isset( $tesseract_jobs->jobs ) ) ? $tesseract_jobs->jobs : null;
			$message	  = ( isset( $tesseract_jobs->message ) ) ? $tesseract_jobs->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 			= 1;
				$return_data['tesseract_jobs'] 	= $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/** Pull Tesseract Jobs by Call Number **/
	public function fetch_tesseract_jobs_by_call_number( $call_number = false, $page = 'details' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$call_number = ( $this->input->post( 'call_number' ) ) ? $this->input->post( 'call_number' ) : ( !empty( $call_number ) ? $call_number : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$tesseract_jobs = $this->webapp_service->api_dispatcher( $this->api_end_point.'tesseract/job_by_call_number', $postdata, ['auth_token'=>$this->auth_token], true );
			$result		  = ( isset( $tesseract_jobs->job ) ) ? $tesseract_jobs->job : null;
			$message	  = ( isset( $tesseract_jobs->message ) ) ? $tesseract_jobs->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 			= 1;
				$return_data['tesseract_jobs'] 	= $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/**
	* Checklists Overview
	*/
	public function checklists( $checklist_id = false ){

		if( $checklist_id ){
			redirect( 'webapp/job/profile/'.$checklist_id, 'refresh' );
		}

		#Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			$data = [];
			$this->_render_webpage( 'job/checklists/checklists_overview', $data );
		}
	}
	
	
	/*
	* Checklists Search
	*/
	public function checklist_search( $page = 'details' ){

		$return_data = '';

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){

			$return_data .= $this->config->item( 'ajax_access_denied' );

		}else{

			# Setup search parameters
			$checklist_id   		= ( $this->input->post( 'job_id' ) ) ? $this->input->post( 'job_id' ) : false;
			$search_term   	= ( $this->input->post( 'search_term' ) ) ? $this->input->post( 'search_term' ) : false;
			$where   	   	= ( $this->input->post( 'where' ) ) ? $this->input->post( 'where' ) : false;
			$limit		   	= ( !empty( $where['limit'] ) )  ? $where['limit']  : DEFAULT_LIMIT;
			$start_index   	= ( $this->input->post( 'start_index' ) ) ? $this->input->post( 'start_index' ) : DEFAULT_OFFSET;
			$offset		   	= ( !empty( $start_index ) ) ? ( ( $start_index - 1 ) * $limit ) : 0;
			$order_by	   	= ( $this->input->post( 'order_by' ) ) ? $this->input->post( 'order_by' ) : false;

			#prepare postdata
			$postdata = [
				'account_id'	=> $this->user->account_id,
				'job_id'		=> $checklist_id,
				'search_term'	=> $search_term,
				'where'			=> $where,
				'order_by'		=> $order_by,
				'limit'			=> $limit,
				'offset'		=> $offset
			];
			
			$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/checklist_search', $postdata, ['auth_token'=>$this->auth_token], true );

			$checklist			= ( isset( $search_result->checklist ) ) ? $search_result->checklist : null;

			if( !empty( $checklist ) ){

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

				$return_data = $this->load_checklist_search_results( $checklist );
				if( !empty($pagination) ){
					$return_data .= '<tr><td colspan="6" style="padding: 0;">';
						$return_data .= $page_display.$pagination;
					$return_data .= '</td></tr>';
				}
			}else{
				$return_data .= '<tr><td colspan="6" style="padding: 0;"><br/>';
					$return_data .= ( isset($search_result->message) ) ? $search_result->message : 'No records found';
				$return_data .= '</td></tr>';
			}
		}

		print_r( $return_data );
		die();
	}


	/*
	* Prepare Checklists views
	*/
	private function load_checklist_search_results( $checklists_data = false ){
		$return_data = '';
		if( !empty( $checklists_data ) ){
			foreach( $checklists_data as $k => $checklist_details ){
				$return_data .= '<tr>';
					$return_data .= '<td><a href="'.base_url( '/webapp/job/checklist_profile/'.$checklist_details->job_id ).'" >'.$checklist_details->job_type.'</a></td>';
					$return_data .= '<td>'.$checklist_details->site_name.'</td>';
					$return_data .= '<td>'.$checklist_details->site_reference.'</td>';
					$return_data .= '<td>'.$checklist_details->assignee.'</td>';
					$return_data .= '<td>'.( ( valid_date( $checklist_details->finish_time ) ) ? date( 'd-m-Y', strtotime( $checklist_details->finish_time ) ) : '' ).'</td>';
					$return_data .= '<td>'.$checklist_details->job_status.'</td>';
				$return_data .= '</tr>';
			}

			if( !empty($pagination) ){
				$return_data .= '<tr><td colspan="6" style="padding: 0; font-weight:300">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
		}else{
			$return_data .= '<tr><td colspan="6"><br/>'.$this->config->item("no_records").'</td></tr>';
		}
		return $return_data;
	}
	
	
	/** View Checklists profile **/
	public function checklist_profile( $job_id = false, $page = 'result' ){

		if( !$job_id ){
			redirect( 'webapp/job/jobs', 'refresh' );
		}

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		# Check item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 					= ['account_id'=>$this->user->account_id, 'job_id'=>$job_id ];
			$checklist_record			= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/checklist_search', $postdata, ['auth_token'=>$this->auth_token], true );
			$data['checklist_details']	= ( isset( $checklist_record->checklist ) ) ? $checklist_record->checklist : null;
			
			$data['active_tab']		= $page;
			$data['module_tabs'] 	= array_to_object( [
				[	
					'module_item_id' 		=> 1,
					'module_item_tab' 		=> 'result',
					'module_item_name' 		=> 'result',
					'module_item_url_link' 	=> null,
					'module_item_desc' 		=> 'Access to JOb Checklist details',
					'module_item_icon_class'=> null,
					'module_item_sort' 		=> 1,
					'is_active' 			=> 1,
					'module_id' 			=> 8,
					'mobile_visible' 		=> 1,
					'show_in_sidebar' 		=> 0,
					'module_name' 			=> 'Jobs'
				],
				[	
					'module_item_id' 		=> 2,
					'module_item_tab' 		=> 'documents',
					'module_item_name' 		=> 'documents',
					'module_item_url_link' 	=> null,
					'module_item_desc' 		=> 'Access to JOb Checklist docs',
					'module_item_icon_class'=> null,
					'module_item_sort' 		=> 2,
					'is_active' 			=> 1,
					'module_id' 			=> 8,
					'mobile_visible' 		=> 1,
					'show_in_sidebar' 		=> 0,
					'module_name' 			=> 'Jobs'
				]				
			] );

			switch( $page ){
				case 'documents':
					$job_documents		 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'document_handler/document_list', ['account_id'=>$this->user->account_id, 'job_id'=>$job_id, 'document_group'=>'job' ], ['auth_token'=>$this->auth_token], true );
					$data['checklist_documents']= ( isset( $job_documents->documents->{$this->user->account_id} ) ) ? $job_documents->documents->{$this->user->account_id} : null;
					$data['include_page'] 		= 'checklist_documents.php';
					break;
					
				default:
				case 'result':
					$data['include_page'] 		= 'checklist_details.php';
					break;
			}

			if( !empty( $data['checklist_details'] ) ){
				$this->_render_webpage('job/checklists/checklist_profile', $data);
			} else {
				redirect( 'webapp/job/overview', 'refresh' );
			}
		}
	}
	
	
	/** Get all Checklist linked Job Types required for a Schedule **/
	public function fetch_checklist_job_types( $page = 'schedules' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$frequency 			= $this->input->post( 'frequency' );
			$request_data 		= [
				'account_id'=> $this->user->account_id, 
				'where'		=> [ 'frequency' => $frequency ]
			];

			$checklist_job_types	 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'tesseract/checklist_job_types', $request_data, ['auth_token'=>$this->auth_token], true );
			$result			= ( isset( $checklist_job_types->job_types ) ) 	? $checklist_job_types->job_types : null;
			$message		= ( isset( $checklist_job_types->message ) ) 	? $checklist_job_types->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 		 		= 1;
				$return_data['checklist_job_types'] 	= $this->load_checklist_job_types_view( $frequency, $result );
			} else {
				$return_data['status_msg'] = 'There\'s currently no Checklist Job Types matching your criteria';
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	//Load Contract Buildings
	private function load_checklist_job_types_view( $frequency = false, $checklist_job_types = false ){
		$return_data = '';
		if( !empty( $frequency ) && !empty( $checklist_job_types ) ){
			
			$return_data .= '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">';
				$return_data .= '<div class="small alert bg-blue panel-chk pointer" >';
					$return_data .= '<div class="row">';
						$return_data .= '<div class="col-md-12 col-sm-12 col-xs-12">';
							$return_data .= '<span><label class="text-white pointer text-bold"  ><input id="jobtypes-chk-all" data-jobtypes_check="jobtypes-chk" type="checkbox" > &nbsp;Check / Uncheck all </label></span>';
						$return_data .= '</div>';
					$return_data .= '</div>';
				$return_data .= '</div>';
			$return_data .= '</div>';
			
			foreach( $checklist_job_types as $ref => $freq_job_types ){
				foreach( $freq_job_types as $ref => $checklist_job_type ){
					$return_data .= '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">';
						$return_data .= '<div class="small alert bg-blue panel-chk pointer" data-job_type_ref="'.$checklist_job_type->job_type_ref.'" >';
							$return_data .= '<div class="row">';
								$return_data .= '<div class="col-md-12 col-sm-12 col-xs-12">';
									$return_data .= '<span><label class="text-white pointer"  ><input name="job_type_id[]" id="job_type-chk-'.$checklist_job_type->job_type_ref.'" class="jobtypes-chk job_type-check-all" data-job_type="'.$checklist_job_type->job_type.'"  data-job_type_check_id="job_type-chk-'.$checklist_job_type->job_type_ref.'" type="checkbox" value="'.$checklist_job_type->job_type_id.'" > &nbsp;'.$checklist_job_type->job_type.'</label></span>';
								$return_data .= '</div>';
							$return_data .= '</div>';
						$return_data .= '</div>';
					$return_data .= '</div>';
				}
			}
		}
		return $return_data;
	}
	
	/** 
	* Refresh Evident Job with Tesseract Data
	**/
	public function refresh_evident_job( $call_number = false, $page = 'details' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$request_data 		= [
				'account_id'	=> $this->user->account_id, 
				'call_number'	=> !empty( $this->input->post( 'call_number' ) ) ? $this->input->post( 'call_number' ) : $call_number
			];

			$refreshed_job	= $this->webapp_service->api_dispatcher( $this->api_end_point.'tesseract/refresh_evident_jobs', $request_data, ['auth_token'=>$this->auth_token], true );
			$result			= ( isset( $refreshed_job->jobs ) ) 	? $refreshed_job->jobs : null;
			$message		= ( isset( $refreshed_job->message ) ) 	? $refreshed_job->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 		= 1;
				$return_data['job'] 		= $result;
				$return_data['status_msg']	= $message;
			} else {
				$message = 'Refresh request failed! Please try again.';
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/**
	* Add Required Checklists to a Job Type
	**/
	public function add_required_checklists(){

		$section 	 = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
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

			$required_checklists = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/add_required_checklists' , $postdata, ['auth_token'=>$this->auth_token] );
			$result		  	 = ( isset( $required_checklists->required_checklists ) ) 	? $required_checklists->required_checklists : null;
			$message	  	 = ( isset( $required_checklists->message ) )  			? $required_checklists->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 	 = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/**
	* Remove Required Checklists from Job type
	**/
	public function remove_required_checklist( $checklist_id = false ){
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
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$unlink_checklist 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/remove_required_checklist', $postdata, ['auth_token'=>$this->auth_token] );
			$result			= ( isset( $unlink_checklist->status ) )  ? $unlink_checklist->status : null;
			$message		= ( isset( $unlink_checklist->message ) ) ? $unlink_checklist->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/** Get all EviDoc Types matching a specific Discipline **/
	public function evidoc_types_by_discipline( $page = 'details' ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$evidoc_types	= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/evidoc_types', $postdata, ['auth_token'=>$this->auth_token], true );
			$result			= ( isset( $evidoc_types->evidoc_types ) )  ? $evidoc_types->evidoc_types : null;
			$message		= ( isset( $evidoc_types->message ) ) 		? $evidoc_types->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 		 		= 1;
				$return_data['evidoc_types_data'] 	= $this->load_evidoc_types_by_discipline_view( $result );
			} else {
				$return_data['status_msg'] = 'There\'s currently no Evidoc Types matching the selected Discipline!';
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	//Load Evidoc Types by Discipline
	private function load_evidoc_types_by_discipline_view( $evidoc_types_data ){
		$return_data = '';
		if( !empty( $evidoc_types_data ) ){			
			$return_data .= '<select id="evidoc_type_id" name="evidoc_type_id" class="form-control" style="width:100%; display:block; margin-bottom:10px;" data-label_text="Linked Evidoc" >';
				$return_data .= '<option value="" >Search / Select the Evidoc</option>';
				foreach( $evidoc_types_data as $k => $evidoc_type ) {
					$return_data .= '<option value="'.$evidoc_type->audit_type_id.'" >'.ucwords( $evidoc_type->audit_type.' - '.$evidoc_type->audit_frequency ).' '.( !empty( $evidoc_type->audit_group ) ? '('.$evidoc_type->audit_group.')' : "" ).'</option>';
				}
			$return_data .= '</select>';
		}
		return $return_data;
	}
	
	
	private function dates_from_date_range( $account_id = false, $date_range = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $date_range ) ){
			$date_to 	= date( 'Y-m-d', strtotime( _datetime() ) );
			switch( strtolower( $date_range ) ){
				# Last 7 Days to date inclusive
				default:
				case '7':
				case '7days':
				case '7 days':
				case '1week':
				case '1 week':
					$date_from 	= date( 'Y-m-d', strtotime( _datetime().' - 7 days ' ) );;
					break;
					
				# Last 30 Days to date inclusive
				case '30':
				case '30days':
				case '30 days':
				case '1month':
				case '1 month':
					$date_from 	= date( 'Y-m-d', strtotime( _datetime().' - 30 days ' ) );;
					break;
					
				# Last 90 Days to date inclusive
				case '90':
				case '90days':
				case '90 days':
				case '3months':
				case '3 months':
					$date_from 	= date( 'Y-m-d', strtotime( _datetime().' - 90 days ' ) );;
					break;
					
				# Last 180 Days to date inclusive
				case '180':
				case '180days':
				case '180 days':
				case '6months':
				case '6 months':
					$date_from 	= date( 'Y-m-d', strtotime( _datetime().' - 180 days ' ) );;
					break;
					
				# Last 365 Days to date inclusive
				case '365':
				case '365days':
				case '365 days':
				case '12months':
				case '12 months':
				case '1year':
				case '1 year':
					$date_from 	= date( 'Y-m-d', strtotime( _datetime().' - 1 year' ) );;
					break;
				
			}
			
			$result = (object)[
				'date_from' => trim( $date_from ),
				'date_to' 	=> trim( $date_to ),
			];
		}
		return $result;
	}
	
	
	/** Bulk Job Re-Book **/
	public function bulk_rebook( $job_date = false, $postcode_area = false, $job_type_id = false, $region_id = false ){
		
		if( !$this->identity() ){
			redirect('webapp/user/login', 'refresh');
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'details' );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			
			$job_date 			= ( $this->input->get( 'job_date' ) ) 		? $this->input->get( 'job_date' ) 		: ( !empty( $job_date ) 		? $job_date : date( 'Y-m-d' ) );
			$job_type_id 		= ( $this->input->get( 'job_type_id' ) ) 	? $this->input->get( 'job_type_id' ) 	: ( !empty( $job_type_id )		? $job_type_id : null );
			$region_id 			= ( $this->input->get( 'region_id' ) ) 		? $this->input->get( 'region_id' ) 		: ( !empty( $region_id ) 		? $region_id : null );
		
			$ref_date 			= date( 'Y-m-d', strtotime( $job_date ) );
			$where = [
				'job_date' 		=> $ref_date,
				'ref_date' 		=> $ref_date,
				'job_type_id' 	=> $job_type_id,
				'region_id' 	=> $region_id
			];

			$job_types						= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_types', ['account_id'=>$this->user->account_id,'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
			$data['job_types']				= ( isset( $job_types->job_types ) ) ? $job_types->job_types : null;
			
			$regions 						= $this->webapp_service->api_dispatcher( $this->api_end_point.'diary/regions', [ 'account_id'=>$this->user->account_id, 'limit'=>-1 ], [ 'auth_token'=>$this->auth_token ], true );
			$data['regions']				= ( isset( $regions->regions ) ) ? $regions->regions : null;
			
			$data['job_date']			=  date( 'd-m-Y', strtotime( $job_date ) );
			
			$this->_render_webpage( 'job/inc/bulk_rebook_jobs', $data );
		}
		
	}
	
	
	/** Bulk Job Reassign **/
	public function bulk_assign( $contract_id = false, $job_date = false, $postcode_area = false, $job_type_id = false, $region_id = false ){
		
		if( !$this->identity() ){
			redirect('webapp/user/login', 'refresh');
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'details' );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			
			if( in_array( $this->user->user_type_id, EXTERNAL_USER_TYPES ) ){
				$linked_contract		= $this->webapp_service->api_dispatcher( $this->api_end_point.'contract/linked_people', ['account_id'=>$this->user->account_id, 'where'=>['person_id'=>$this->user->id] ], ['auth_token'=>$this->auth_token], true );
				$data['linked_contract']= ( isset( $linked_contract->people ) ) ? $linked_contract->people : false;
				$linked_contract_id		= !empty( $data['linked_contract'] ) 	? array_unique( array_column( $data['linked_contract'], 'contract_id' ) ) : false;
				$linked_contract_id		= !empty( $linked_contract_id[0] ) 		? $linked_contract_id[0]	: false;
			}
			
			$contract_id 		= !empty( $linked_contract_id ) ? $linked_contract_id : ( ( $this->input->post( 'contract_id' ) ) 	? $this->input->post( 'contract_id' ) 	: ( ( $this->input->get( 'contract_id' ) ) ? $this->input->get( 'contract_id' ) : $contract_id ) );
			$job_date 			= ( $this->input->post( 'job_date' ) ) 		? $this->input->post( 'job_date' ) 	: ( ( $this->input->get( 'job_date' ) ) ? $this->input->get( 'job_date' ) : date( 'Y-m-d' ) );
			$due_date 			= ( $this->input->post( 'due_date' ) ) 		? $this->input->post( 'due_date' ) 	: ( ( $this->input->get( 'due_date' ) ) ? $this->input->get( 'due_date' ) : date( 'Y-m-d' ) );
			$job_type_id 		= ( $this->input->get( 'job_type_id' ) ) 	? $this->input->get( 'job_type_id' ) 	: ( !empty( $job_type_id )		? $job_type_id : null );
			$region_id 			= ( $this->input->get( 'region_id' ) ) 		? $this->input->get( 'region_id' ) 		: ( !empty( $region_id ) 		? $region_id : null );
			$include_blank_dates= ( $this->input->post( 'contract_id' ) ) 	? $this->input->post( 'include_blank_dates' ) 	: ( ( $this->input->get( 'include_blank_dates' ) ) ? $this->input->get( 'include_blank_dates' ) : 0 );
			
			$dateObj 			= new DateTime( $due_date ); 
			$last_day_of_month 	= $dateObj->format( 'Y-m-t' );
			$processedDueDate	= ( ( date( 'd-m-Y', strtotime( $last_day_of_month ) ) > date( 'd-m-Y', strtotime( $due_date ) ) ) ? date( 'd-m-Y', strtotime( $last_day_of_month ) ) : date( 'd-m-Y', strtotime( $due_date ) ) );
			#$due_date			= date( 'Y-m-d', strtotime( $processedDueDate ) );
			$due_date			= date( 'Y-m-d', strtotime( $due_date ) );
			
			$ref_date 			= date( 'Y-m-d', strtotime( $job_date ) );
			$where = [
				'contract_id' 	=> $contract_id,
				'job_date' 		=> $ref_date,
				'ref_date' 		=> $ref_date,
				'job_type_id' 	=> $job_type_id,
				'region_id' 	=> $region_id,
				'due_date' 		=> $due_date
			];

			$job_types_where = [
				'contract_id' 	=> $contract_id
			];
			
			$job_types						= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_types', ['account_id'=>$this->user->account_id, 'where'=>$job_types_where, 'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
			$data['job_types']				= ( isset( $job_types->job_types ) ) ? $job_types->job_types : null;
			
			$regions 						= $this->webapp_service->api_dispatcher( $this->api_end_point.'diary/regions', [ 'account_id'=>$this->user->account_id, 'limit'=>-1 ], [ 'auth_token'=>$this->auth_token ], true );
			$data['regions']				= ( isset( $regions->regions ) ) ? $regions->regions : null;
			
			$contract_id_filter				= !empty( $linked_contract_id ) ? $linked_contract_id : false;
			
			$contracts	  					= $this->webapp_service->api_dispatcher( $this->api_end_point.'contract/contracts', ['account_id'=>$this->user->account_id, 'contract_id'=>$contract_id_filter, 'limit'=>-1 ], ['auth_token'=>$this->auth_token], true );
			$data['contracts']				= ( isset( $contracts->contract) ) ? $contracts->contract : null;

			$data['job_date']				=  date( 'd-m-Y', strtotime( $job_date ) );
			$data['due_date']				=  date( 'd-m-Y', strtotime( $due_date ) );

			$data['contract_id']			=  $contract_id;
			$data['include_blank_dates']	=  $include_blank_dates;
			
			$this->_render_webpage( 'job/inc/bulk_assign_jobs', $data );
		}
	}
	
	
	/**
	* Clone Schedule Record
	**/	
	public function clone_schedule( $schedule_id = false, $page = 'details' ){

		$section 	 = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
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
			$demo = $this->input->post();
	
			$postdata 	  	  = array_merge( [ 'account_id'=>$this->user->account_id ], $this->input->post() );
			$clone_schedule   = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/clone_schedule', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  	  = ( isset( $clone_schedule->schedule ) ) 	? $clone_schedule->schedule : null;
			$message	  	  = ( isset( $clone_schedule->message ) )  	? $clone_schedule->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 	 = 1;
				$return_data['schedule'] = $result;
			}
			
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/**
	* Clone Jobs from Schedule
	**/	
	public function clone_jobs( $jobs_id = false, $page = 'details' ){

		$section 	 = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
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
			$postdata 	  	  = array_merge( [ 'account_id'=>$this->user->account_id ], $this->input->post() );
			$clone_jobs   	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/clone_jobs', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  	  = ( isset( $clone_jobs->jobs ) ) 		? $clone_jobs->jobs : null;
			$message	  	  = ( isset( $clone_jobs->message ) )  	? $clone_jobs->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 	= 1;
				$return_data['jobs'] 	= $result;
			}
			
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/**
	* Complete Scheduling Process
	**/	
	public function complete_scheduling_process( $schedule_id = false, $page = 'details' ){

		$section 	 = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
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
			$postdata 	  	  = array_merge( [ 'account_id'=>$this->user->account_id ], $this->input->post() );
			$schedules   	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/complete_scheduling_process', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  	  = ( isset( $schedules->schedule ) ) 	? $schedules->schedule : null;
			$message	  	  = ( isset( $schedules->message ) )  	? $schedules->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 		= 1;
				$return_data['schedule'] 	= $result;
			}
			
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/** Prepare Main Job container Placeholders **/
	public function prepare_job_placeholders( $frequency_id = false ){

		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$schedule_data  = !empty( $postdata['where'] ) ? $postdata['where'] : false;
			$assets_data    = !empty( $schedule_data['assets_data'] ) ? $schedule_data['assets_data'] : false;
			unset( $schedule_data['assets_data'] );
			if( !empty( $schedule_data ) && !empty( $assets_data ) ){
				$assets_data = $this->reorganize_assets_data( $assets_data );
				$return_data['status'] 		 	= 1;
				$return_data['schedules_data'] 	= $this->load_job_placeholders_view( $schedule_data, $assets_data );
			} else {
				$return_data['status_msg'] = 'There was a problem processing your Schedule data!';
			}
		}

		print_r( json_encode( $return_data ) );
		die();

	}

	//Load Main Job container for multiple assets
	private function load_job_placeholders_view( $schedule_data = false, $assets_data = false ){
		$return_data = '';
		if( !empty( $schedule_data ) && !empty( $assets_data ) ){
			$assets_data		= array_to_object( $assets_data );
			$freqeuncyId  		= $schedule_data['frequency_id'];
			$activityName  		= $schedule_data['frequency_name'];
			$activityNameTag  	= $schedule_data['frequency_name'];
			$activityCount 		= $schedule_data['total_activities'];
			$activityInterval 	= $schedule_data['activity_interval'];
			$activity_percentage= $schedule_data['activity_interval'];
			$dueDate			= date( 'd-m-Y', strtotime( $schedule_data['due_date'] ) );
			$assetCheckCount	= $schedule_data['number_of_checks'];
			$x 					= 1;
			$activityName  		= 'Asset '.$activityName;
			$totalActivitiesDue	= 0;
			$totalAssets		= [];

			$return_data .= '<table class="table table-responsive" style="width:100%" >';

				$return_data .= '<tr style="display:none"><td colspan="4" >';
					$return_data .= '<input type="hidden" name="frequency_id" value="'.$freqeuncyId.'" />';
					$return_data .= '<input type="hidden" name="schedule_name" value="'.$activityName.'" />';
				$return_data .= '</td></tr>';
				
				for( $i = 1; $i <= $activityCount; $i++ ){
					$return_data .= '<tr>';
						$return_data .=  '<td colspan="4" width="100%" class="bg-grey" ><h4><strong>'.$activityName.' Activity '.$x.'</strong></h4></td>';
					$return_data .= '</tr>';

					// Set Due Date to End of the Month
					$dateObj 			= new DateTime( $dueDate ); 
					$last_day_of_month 	= $dateObj->format( 'Y-m-t' );
					
					switch( strtolower( $activityNameTag ) ){
						case 'weekly':
						case 'weekly inspection':
						case 'weekly-inspection':
							$processedDueDate	 = isset( $processedDueDate ) 	 ? date( 'd-m-Y', strtotime( $processedDueDate.' + 7 days' ) ) : date( 'd-m-Y', strtotime( $dueDate ) );
							$processedJobDueDate = isset( $processedJobDueDate ) ? date( 'd-m-Y', strtotime( $processedJobDueDate.' + 7 days' ) ) : date( 'd-m-Y', strtotime( $dueDate ) );
							$activityInterval_counter	 = '+7 days';
							break;
							
						case 'monthly':
						case 'monthly inspection':
						case 'monthly-inspection':
							$activityInterval_counter	= '+1 month';
							$processedDueDate	 		= ( ( date( 'd-m-Y', strtotime( $last_day_of_month ) ) > date( 'd-m-Y', strtotime( $dueDate ) ) ) ? date( 'd-m-Y', strtotime( $last_day_of_month ) ) : date( 'd-m-Y', strtotime( $last_day_of_month ) ) );
							$processedJobDueDate 		= isset( $processedJobDueDate ) ? date( 'd-m-Y', strtotime( $processedJobDueDate.' + 1 month' ) ) : date( 'd-m-Y', strtotime( $dueDate ) );
							break;
							
						default:
							$processedDueDate			= ( ( date( 'd-m-Y', strtotime( $last_day_of_month ) ) > date( 'd-m-Y', strtotime( $dueDate ) ) ) ? date( 'd-m-Y', strtotime( $last_day_of_month ) ) : date( 'd-m-Y', strtotime( $dueDate ) ) );
							$processedJobDueDate		= date( 'd-m-Y', strtotime( $dueDate ) );
							$activityInterval_counter 	= (int)$activityInterval.' days';
							break;
					}

					foreach( $assets_data as $key => $assets_obj ){

						$assets_array			= object_to_array( $assets_obj->assets );
						$total_assets 			= count( $assets_array );
						$total_assets_to_check 	= ( !empty( $consecutive_checks ) ) ? $consecutive_checks : ceil( ( ( $total_assets * $assetCheckCount ) / $activityCount ) );

						$return_data .= '<tr>';
							$return_data .=  '<td colspan="4"  >&nbsp;&nbsp;&nbsp;<strong>'.strtoupper( $assets_obj->asset_type->asset_type ).' ('.$total_assets.')</strong><span class="pull-right hide" >Total Assets: '.$total_assets.' | To Be Checked: '.$total_assets_to_check.'</span></td>';
						$return_data .= '</tr>';
						$return_data .= '<tr>';
							$return_data .=  '<td  colspan="4" >';
								$return_data .= '<div class="col-md-12" >';
									$looper = 0;
									$return_data .= '<div class="row" >';
										$return_data .=  '<div class="col-md-3" ><strong><small>ASSET UNIQUE ID</small></strong></div>';
										$return_data .=  '<div class="col-md-4" ><strong><small>EVIDOC NAME</small></strong></div>';
										$return_data .=  '<div class="col-md-4" ><strong><small>JOB TYPE</small></strong></div>';
										$return_data .=  '<div class="col-md-1" ><strong><small>DUE DATE</small></strong></div>';
									$return_data .= '</div>';

									foreach( $assets_obj->assets as $k => $asset ){
										
										$activity_name = ordinal( $x ).' Visit - ' .$activityName; 
										$return_data .= '<input type="hidden" name="schedule_activities['.$x.']['.$asset->asset_id.'][asset_id]" value="'.$asset->asset_id.'" />';
										$return_data .= '<input type="hidden" name="schedule_activities['.$x.']['.$asset->asset_id.'][activities_total]" value="'.$activityCount.'" />';
										$return_data .= '<input type="hidden" name="schedule_activities['.$x.']['.$asset->asset_id.'][job_type_id]" value="'.$asset->job_type_id.'" />';
										$return_data .= '<input type="hidden" name="schedule_activities['.$x.']['.$asset->asset_id.'][activity_name]" value="'.$activity_name.'" />';
										$return_data .= '<input type="hidden" name="schedule_activities['.$x.']['.$asset->asset_id.'][proportion]" value="" />';
										$return_data .= '<input type="hidden" name="schedule_activities['.$x.']['.$asset->asset_id.'][due_date]" value="'.$processedDueDate.'" />';
										$return_data .= '<input type="hidden" name="schedule_activities['.$x.']['.$asset->asset_id.'][job_due_date]" value="'.$dueDate.'" />';

										$return_data .= '<div class="row" >';
											$return_data .=  '<div class="col-md-3" >&nbsp;&nbsp;&nbsp;'.$asset->asset_unique_id.'</div>';
											$return_data .=  '<div class="col-md-4" >'.urldecode( $asset->evidoc_type ).'</div>';
											$return_data .=  '<div class="col-md-4" >'.urldecode( $asset->job_type ).'</div>';
											$return_data .=  '<div class="col-md-1" >'.$processedDueDate.'</div>';
										$return_data .= '</div>';
										$totalActivitiesDue++;
										$totalAssets[$asset->asset_id] = $asset->asset_id;
									}
									
								$return_data .= '</div>';
							$return_data .=  '</td>';
						$return_data .= '</tr>';
					}

					$dueDate 		= date( 'd-m-Y', strtotime( $dueDate.' '.$activityInterval_counter ) );
					$x++;
				}
				
				$return_data .= '<tr style="display:none"><td colspan="4" >';
					$return_data .= '<input class="total_assets" id="total_assets" type="hidden" value="'.count( $totalAssets ).'" />';
					$return_data .= '<input class="total_activities_due" id="total_activities_due" type="hidden" value="'.$totalActivitiesDue.'" />';
				$return_data .= '</td></tr>';
			$return_data .= '</table>';
		}
		return $return_data;
	}
	
	
	/** Create schedules **/
	public function create_schedules_revised(){

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$return_data = [
			'status'=>0
		];

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'details' );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$schedules	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/create_schedules_revised', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  = ( isset( $schedules->schedules ) ) 	? $schedules->schedules : null;
			$message	  = ( isset( $schedules->message ) ) 	? $schedules->message : 'Oops! There was an error processing your request.';

			if( !empty( $result ) ){
				$return_data['status'] 		= 1;
				$return_data['schedules']   = $result;
			}
			$return_data['status_msg'] 		= $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	public function _unpack_assets_from_disciplines( $account_id = false , $asset_types_assets = false ){
		$result = [];
		if( !empty( $account_id ) && !empty( $asset_types_assets ) ){
			foreach( $asset_types_assets as $type_asset ){
				$result = array_merge( $result, array_column( object_to_array( $type_asset->assets ), 'asset_id' ) );
			}
			$result = $result;
		}
		return $result;
	}
	
	/** Link assets to be checkled to an existing Job **/
	public function link_assets_to_job(){

		$section 	 = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
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
		} else {
			$postdata 	  	 = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			
			$link_assets	 = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/link_assets_to_job' , $postdata, ['auth_token'=>$this->auth_token] );
			$result		  	 = ( isset( $link_assets->linked_assets ) ) ? $link_assets->linked_assets 	: null;
			$message	  	 = ( isset( $link_assets->message ) )  		? $link_assets->message 		: 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 	 = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/**
	* Remove Asset to e checked from Job type
	**/
	public function unlink_asset_from_job( $job_id = false ){
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
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$unlink_asset 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/unlink_job_assets', $postdata, ['auth_token'=>$this->auth_token] );
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


	/** Delete Schedule **/
	public function delete_schedule( $schedule_id = false, $page = 'schedules' ){
		$return_data = [
			'status'=>0
		];

		$section 		= ( $this->input->post( 'page' ) ) 	? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$schedule_id 	= ( $this->input->post( 'schedule_id' ) ) ? $this->input->post( 'schedule_id' ) : ( !empty( $schedule_id ) ? $schedule_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	  		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$delete_schedule	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/delete_schedule', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  		= ( isset( $delete_schedule->status ) )  ? $delete_schedule->status : null;
			$message	  		= ( isset( $delete_schedule->message ) ) ? $delete_schedule->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status']		= 1;
				$return_data['schedule'] 	= $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/**
	* Complete Schedule Processing (Revised)
	**/	
	public function complete_schedule_processing_revised( $schedule_id = false, $page = 'details' ){

		$section 	 = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
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
			$postdata 	  	  = array_merge( [ 'account_id'=>$this->user->account_id ], $this->input->post() );
			$schedules   	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/complete_schedule_processing_revised', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  	  = ( isset( $schedules->schedule ) ) 	? $schedules->schedule : null;
			$message	  	  = ( isset( $schedules->message ) )  	? $schedules->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 		= 1;
				$return_data['schedule'] 	= $result;
			}
			
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}

}