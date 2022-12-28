<?php

namespace Application\Modules\Web\Controllers;

class Site extends MX_Controller {

	function __construct(){
		parent::__construct();

		if( !$this->identity() ){
			redirect('webapp/user/login', 'refresh');
		}

		$this->module_id 	   = $this->webapp_service->_get_module_id( $this->router->fetch_class() );
		$this->load->library('pagination');
		$this->load->model('serviceapp/Site_model','site_service');
		$this->load->model('serviceapp/Address_Bank_model','address_bank_service');
		#$this->module_access 	= $this->webapp_service->check_access( $this->user, $this->module_id );
		
		$this->demo_accounts 	= [17, 15];
		if( in_array( $this->account_id, $this->demo_accounts ) ) {
			$this->doc_type_groups	= ["Signed Contract", "Surveys", "H+S", "Wayleave", "Finance", "Others", "Certs", "As Builts", "FRA's", "Resident Engagement Strategy","Complaints Handling Procedure", "Operations and Maintenance", "Floor Plan", "Fire Emergency Evacuation Plan", "Building Assurance Certificate"];
		} else {
			$this->doc_type_groups	= ["Signed Contract", "Surveys", "H+S", "Wayleave", "Finance", "Others", "Certs", "As Builts", "FRA's", "Resident Engagement Strategy","Complaints Handling Procedure", "FEEP", "BIM", "Building Regulations", "C and Ms", "Resident Engagement", "Complaints", "Floor Plans", "Safety Assurance" ];
		}
		
		$this->location_types 	= [ '1'=>'Residence', '2'=>'Communal area', '3'=>'Private area' ];//Refer to DB table location_types
		
		$this->priority_ratings 	= [ 'Low', 'Medium', 'High' ];
	}

	//redirect if needed, otherwise display the user list
	function index(){

		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){
			//access denied
			//$this->_render_webpage('errors/access-denied', false, false, true );
		}else{
			redirect( 'webapp/site/sites', 'refresh' );
		}
	}

	/** Get list of sites **/
	public function sites( $site_id = false ){

		if( $site_id ){
			redirect('webapp/site/profile/'.$site_id, 'refresh');
		}

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){
			$this->_render_webpage( 'errors/access-denied', false );
		}else{
			$site_statuses 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/site_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['site_statuses']	= ( isset($site_statuses->site_statuses) ) ? $site_statuses->site_statuses : null;;
			$data['current_user']	= $this->user;
			$this->_render_webpage('site/index', $data);
		}
	}

	//View user profile
	function profile( $site_id = false, $page = 'details' ){

		$section 		= ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		$module_access  = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
		
			# Check module-item access
			$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
			if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
				$this->_render_webpage( 'errors/access-denied', false );
			}else if( $site_id ){
				$run_admin_check 	 = false;
				$site_details		 = $this->webapp_service->api_dispatcher( $this->api_end_point.'site/sites', ['account_id'=>$this->user->account_id,'site_id'=>$site_id], ['auth_token'=>$this->auth_token], true );
				$data['site_details']= ( isset($site_details->sites) ) ? $site_details->sites : null;
				if( !empty( $data['site_details'] ) ){
					
					#$postcode_addresses	 		= $this->address_bank_service->get_addresses( urldecode( $data['site_details']->address_postcode ) ); // allow direct access to address for speed as this is likely to be big lists
					$postcode_addresses	 		= $this->address_bank_service->get_addresses( urldecode( 'CR0 4GE' ) ); // allow direct access to address for speed as this is likely to be big lists
					$data['postcode_addresses'] = ( !empty( $postcode_addresses ) ) ? $postcode_addresses : [];
					
					$run_admin_check 	= false;
					#Get allowed access for the logged in user
					
					$data['active_tab']	= $page;
					$tab_permissions	= !empty( $item_access->tab_permissions ) ? $item_access->tab_permissions : false;
					$data['tab_permissions'] = !empty( $tab_permissions->{$page} ) ? $tab_permissions->{$page} : $item_access;
					$data['permissions']= (object) array_merge( (array)$module_access, (array) $data['tab_permissions'] );

					$module_items 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'access/account_modules_items', ['account_id'=>$this->user->account_id, 'module_id'=>$this->module_id ], ['auth_token'=>$this->auth_token], true );
					$data['module_tabs']= ( isset( $module_items->module_items ) ) ? $module_items->module_items : null;
					
					
					$data['more_list_active']= ( !empty( $reordered_tabs['more_list'] ) && in_array( $page, $reordered_tabs['more_list'] )  ) ? true : false;

					$site_panels		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/panels', ['account_id'=>$this->user->account_id, 'site_id'=>$site_id, 'include_assets'=>true ], ['auth_token'=>$this->auth_token], true );
					$data['site_panels']    = ( isset( $site_panels->site_panels ) ) ? $site_panels->site_panels : null;

					$site_locations		  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/site_locations', ['account_id'=>$this->user->account_id, 'where'=>['site_id'=>$site_id ] ], ['auth_token'=>$this->auth_token], true );
					$data['site_locations']	= ( isset( $site_locations->locations ) ) ? $site_locations->locations : [];
					
					$data['existing_locations'] = array_column( object_to_array( $data['site_locations'] ), 'address_id' );
					
					$data['site_postcode'] 	= ( !empty( $data['site_details']->address_postcode ) ) ? $data['site_details']->address_postcode : false;

					$params 	  				= array_merge( ['account_id'=>$this->user->account_id, 'where'=>['site_id'=>$site_id], 'stat_type' => 'assets_by_category'], $this->input->post() );
					$assets_by_category			= $this->webapp_service->api_dispatcher( $this->api_end_point.'//statistics/asset_stats', $params, $this->options, true );
					$data['assets_by_category']	= ( !empty( $assets_by_category->asset_stats ) ) 	? $assets_by_category->asset_stats : false;
					
					$data['total_assets']   = ( !empty( $data['assets_by_category']->totals->grand_total ) ) ? $data['assets_by_category']->totals->grand_total : 0;

					$data['grouped_assets'] = ( !empty( $data['assets_by_category']->stats ) ) ? re_sort_array( $data['assets_by_category']->stats, 'category_name', 'asort' ) : false;

					$site_zones		  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/site_zones', ['account_id'=>$this->user->account_id, 'where'=>['site_id'=>$site_id ] ], ['auth_token'=>$this->auth_token], true );
					$data['site_zones']	= !empty( $site_zones->site_zones ) ? $site_zones->site_zones : false;

					$data['priority_ratings']= $this->priority_ratings;
					
					$postcode_regions     		= $this->webapp_service->api_dispatcher( $this->api_end_point.'diary/regions', ['account_id'=>$this->user->account_id, 'limit'=>-1], [ 'auth_token'=>$this->auth_token ], true );
					$data['postcode_regions']   = ( isset( $postcode_regions->regions ) ) ? $postcode_regions->regions : null;

					switch( $page ){
						case 'assets':
							$assigned_assets	  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/assets', ["account_id"=>$this->user->account_id,'where'=>['site_id' => $site_id, 'grouped_by'=>'floor' ], 'limit'=> -1 ], ['auth_token'=>$this->auth_token], true );
							$data['assigned_assets']= ( isset( $assigned_assets->assets ) ) ? $assigned_assets->assets : null;
							$data['include_page'] = 'site_assets.php';
							break;
						case 'audits':
						case 'evidocs':
							$site_audits	  	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/audits', ['account_id'=>$this->user->account_id, 'site_id'=>$site_id ], ['auth_token'=>$this->auth_token], true );
							$data['site_audits'] = ( isset( $site_audits->audits ) ) ? $site_audits->audits : null;
							$data['include_page'] = 'site_audits.php';
							break;
							
						case 'asset_evidocs':
							$site_audits	  	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/audits', ['account_id'=>$this->user->account_id, 'site_id'=>$site_id, 'where'=>[ 'asset_evidocs'=>1 ] ], ['auth_token'=>$this->auth_token], true );
							$data['site_audits'] = ( isset( $site_audits->audits ) ) ? $site_audits->audits : null;
							$data['include_page'] = 'site_assets_evidocs.php';
							break;
							
						case 'jobs':
							$site_jobs 		  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/jobs', ['account_id'=>$this->user->account_id,'where'=>['site_id' => $site_id], 'limit'=> -1 ], ['auth_token'=>$this->auth_token], true );
							$data['site_jobs']= ( isset($site_jobs->jobs) ) ? $site_jobs->jobs : null;

							$job_types		 	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_types', ['account_id'=>$this->user->account_id, 'limit'=>-1], ['auth_token'=>$this->auth_token], true );
							$data['job_types'] 	  = ( isset($job_types->job_types) ) ? $job_types->job_types : null;

							$job_statuses		  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_statuses', ['account_id'=>false], ['auth_token'=>$this->auth_token], true );
							$data['job_statuses'] = ( isset($job_statuses->job_statuses) ) ? $job_statuses->job_statuses : null;

							$operatives		  	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'user/users', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
							$data['operatives']   = ( isset($operatives->users) ) ? $operatives->users : null;
							
							$data['job_durations']= job_durations();
							
							$data['include_page'] = 'site_jobs.php';
							break;
						case 'locations':
							#$site_addresses	 		= $this->address_bank_service->get_addresses( $data['site_details']->address_postcode ); // allow direct access to address for speed as this is likely to be big lists
							#$data['site_addresses'] = ( !empty( $site_addresses ) ) ? $site_addresses : [];
					
							$data['location_types'] = (object) $this->location_types;
					
							$data['include_page'] 	= 'site_locations.php';
							break;
						case 'contracts':
							$site_contracts		  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/contracts', ['account_id'=>$this->user->account_id,'site_id'=>$site_id], ['auth_token'=>$this->auth_token], true );
							$data['site_contracts']	= ( isset($site_contracts->site_contracts) ) ? $site_contracts->site_contracts : [];

							$data['include_page'] = 'site_contracts.php';
							break;
						case 'documents':
							$data['doc_type_groups'] 	= $this->doc_type_groups;
							$site_documents				= $this->webapp_service->api_dispatcher( $this->api_end_point.'document_handler/document_list', ['account_id'=>$this->user->account_id, 'site_id'=>$site_id, 'document_group'=>'site' ], ['auth_token'=>$this->auth_token], true );
							$data['site_documents']		= ( isset( $site_documents->documents->{$this->user->account_id} ) ) ? $site_documents->documents->{$this->user->account_id} : null;
							$data['include_page'] 		= 'site_documents.php';
							break;
						case 'devices':
							$data['include_page'] = 'site_devices.php';
							break;

						case 'cost':
							$cost_tracking	  	  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'cost/cost_items', ['account_id'=>$this->user->account_id, 'where'=>[ 'site_id'=>$site_id ] ], ['auth_token'=>$this->auth_token], true );
							$data['cost_tracking'] 	= ( isset( $cost_tracking->cost_items) ) ? $cost_tracking->cost_items : null;
							
							$cost_item_types	  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'cost/cost_item_types', ['account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
							$data['cost_item_types']= ( isset( $cost_item_types->cost_item_types) ) ? $cost_item_types->cost_item_types : null;
							
							$data['include_page'] 	= 'site_cost_tracking.php';
							break;
			
						case 'schedules':

							#$site_schedules			= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/schedules', ['account_id'=>$this->user->account_id, 'where'=>['site_id'=>$site_id] ], ['auth_token'=>$this->auth_token], true );
							#$data['site_schedules']	= ( isset( $site_schedules->schedules ) ) ? $site_schedules->schedules : null;
							$data['include_page'] 	= 'site_schedules.php';
							break;
							
						case 'systems':

							$expected_systems			= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/expected_systems', ['account_id'=>$this->user->account_id, 'site_id'=>$site_id ], ['auth_token'=>$this->auth_token], true );
							$data['expected_systems']	= ( isset( $expected_systems->expected_systems ) ) ? $expected_systems->expected_systems : null;
							
							$installed_systems			= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/installed_systems', ['account_id'=>$this->user->account_id, 'site_id'=>$site_id ], ['auth_token'=>$this->auth_token], true );
							$data['installed_systems']	= ( isset( $installed_systems->installed_systems ) ) ? $installed_systems->installed_systems : null;
							
							$data['include_page'] 	= 'site_systems.php';
							break;
							
						case 'sub_blocks':

							$sub_blocks				= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/site_sub_blocks', ['account_id'=>$this->user->account_id, 'where'=>['site_id'=>$site_id] ], ['auth_token'=>$this->auth_token], true );
							$data['sub_blocks']		= ( isset( $sub_blocks->site_sub_blocks ) ) ? $sub_blocks->site_sub_blocks : null;
							
							$data['include_page'] 	= 'site_sub_blocks.php';
							break;
						
						case 'checklists':
							if( !empty( $data['site_details']->external_site_ref ) ){
								$checklists_data		  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/completed_checklists', ['account_id'=>$this->user->account_id, 'site_id'=>$site_id], ['auth_token'=>$this->auth_token], true );
								$data['checklists_data'] 	= ( isset( $checklists_data->completed_checklists ) ) ? $checklists_data->completed_checklists : false;
							}
							$data['include_page'] = 'site_checklist_details.php';
							break;
							
						case 'premises':

							$premises_types		 	  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'premises/premises_types', ['account_id'=>$this->user->account_id, 'limit'=>-1], ['auth_token'=>$this->auth_token], true );
							$data['premises_types'] 	= ( isset($premises_types->premises_types) ) ? $premises_types->premises_types : null;

							$data['include_page'] 	= 'site_premises.php';
							break;
							
						case 'details':
						default:
							$event_statuses		 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/event_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
							$data['event_statuses'] 		= ( isset( $event_statuses->event_statuses ) ) ? $event_statuses->event_statuses : null;

							$audit_result_statuses			= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/result_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
							$data['audit_result_statuses'] 	= ( isset( $audit_result_statuses->audit_result_statuses ) ) ? $audit_result_statuses->audit_result_statuses : null;
							
							$data['current_account'] 		= $this->account_id;
							$data['demo_accounts'] 			= $this->demo_accounts;
						
						$site_statuses 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/site_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
						$data['site_statuses']	= ( isset($site_statuses->site_statuses) ) ? $site_statuses->site_statuses : null;;

							$data['include_page'] 			= 'site_details.php';
							break;
					}
				}

				//Run the admin check if tab needs only admin
				if( !empty( $run_admin_check ) ){
					if( ( !admin_check( $this->user->is_admin, false, ( !empty( $data['permissions'] ) ? $data['permissions']->is_admin : false ) ) ) ){
						$data['admin_no_access'] = true;
					}
				}

				$this->_render_webpage('site/profile', $data);
			}else{
				redirect('webapp/site', 'refresh');
			}
		}
	}

	/*
	* Site lookup / search
	*/
	public function lookup( $page = 'details' ){

		$return_data['stats'] 	= '';
		$return_data['sites']	= '';

		# Check module access
		$section 		= ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		$module_access 	= $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && !$module_access ){
			$return_data['sites'] .= $this->config->item( 'ajax_access_denied' );
		} else {

			# Setup search parameters
			$where		   	= ( $this->input->post( 'where' ) ) 		? $this->input->post( 'where' ) : [];
			$search_term   	= ( $this->input->post( 'search_term' ) ) 	? $this->input->post( 'search_term' ) : false;
			$limit		   	= ( $this->input->post( 'limit' ) )  		? $this->input->post( 'limit' )  : DEFAULT_LIMIT;
			$start_index   	= ( $this->input->post( 'start_index' ) )  	? $this->input->post( 'start_index' )  : 0;
			$offset		   	= ( !empty( $start_index ) ) 				? ( ( $start_index - 1 ) * $limit ) : 0;
			$order_by	   	= false;
			$stats			= false;

			$view_type = 'overview';
			if( !empty( $this->input->post( 'audit_result_status_id' ) ) || !empty( $where['result_status_id'] )  || !empty( $where['group'] ) ){
				$where['result_status_id'] = !empty( $where['result_status_id'] ) ? $where['result_status_id'] : $this->input->post( 'audit_result_status_id' );
				if( empty( $where['result_status_id'] ) ){
					unset( $where['result_status_id'] );
				}
				$view_type = 'result_status';
			}

			if( !empty( $this->input->post( 'site_statuses' ) ) ){
				$where['site_statuses']	= $this->input->post( 'site_statuses' );
			}
			
			if( !empty( $this->input->post( 'result_statuses' ) ) ){
				$where['result_statuses'] = $this->input->post( 'result_statuses' );
			}
			
			#prepare postdata
			$postdata = [
				'account_id'		=> $this->user->account_id,
				'search_term'		=> $search_term,
				'limit'				=> $limit,
				'offset'			=> $offset,
				'order_by'			=> $order_by,
				'where'				=> $where,
			];

			$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/lookup', $postdata, ['auth_token'=>$this->auth_token], true );

			$sites			= ( isset( $search_result->sites ) ) ? $search_result->sites : null;

			$lookup_instant_stats	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/lookup_instant_stats', $postdata, ['auth_token'=>$this->auth_token], true );
			$stats					= ( isset( $lookup_instant_stats->stats ) ) ? $lookup_instant_stats->stats : null;
				
			if( !empty( $stats ) ){
				$return_data['stats'] = $this->load_stats_view( $stats );
			} else {
				$return_data['stats'] = false;
			}
			
			if( !empty($sites) ){

				## Create pagination
				$counters 		= $this->site_service->get_total_sites( $this->user->account_id, $search_term, $where, $order_by, $limit, $offset );//Direct access to count, this should only return a number
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

				$return_data['sites'] = $this->load_sites_view( $sites, $view_type );
				if( !empty($pagination) ){
					$return_data['sites'] .= '<tr><td colspan="5" style="padding: 0;">';
						$return_data['sites'] .= $page_display.$pagination;
					$return_data['sites'] .= '</td></tr>';
				}
			}else{
				$return_data['sites'] .= '<br/>';
				$return_data['sites'] .= ( isset($search_result->message) ) ? $search_result->message : 'No records found';
				$return_data['sites'] .= '<br/><br/>';
			}
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	/*
	* Prepare sites views
	*/
	private function load_sites_view( $sites_data, $view_type = false ){
		$return_data = '';
		if( !empty($sites_data) ){
			foreach( $sites_data as $k => $site_details ){
				$short_site_address  = '';
				$short_site_address .= !empty( $site_details->address_line_2 ) ? $site_details->address_line_2 : '';
				$short_site_address .= !empty( $site_details->address_line_3 ) ? $site_details->address_line_3 : '';
				
				$return_data .= '<tr>';
					//$return_data .= '<td>'.$site_details->site_id.'</td>';
					$return_data .= '<td><a href="'.base_url('/webapp/site/profile/'.$site_details->site_id).'" >'.$site_details->site_name.'</a></td>';
					$return_data .= '<td>'.( !empty( $short_site_address ) ? $short_site_address : $site_details->summaryline ).'</td>';
					$return_data .= '<td>'.$site_details->estate_name.'</td>';
					$return_data .= '<td>'.( !empty( $site_details->site_postcodes ) ? strtoupper( $site_details->site_postcodes ) : $site_details->postcode ).'</td>';
					if( $view_type == 'result_status' ){
						$return_data .= '<td><a href="'.base_url('/webapp/site/profile/'.$site_details->site_id).'" >'.$site_details->result_status_alt.'</a></td>';
					}else{
						$return_data .= '<td>'.$site_details->status_name.'</td>';
					}
					/* $return_data .= '<td>'.( ( !empty( $site_details->result_status ) ) ? $site_details->result_status : "---" ).'</td>'; */
				$return_data .= '</tr>';
			}

			if( !empty($pagination) ){
				$return_data .= '<tr><td colspan="5" style="padding: 0;">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
		}else{
			$return_data .= '<tr><td colspan="4"><br/>'.$this->config->item("no_records").'</td></tr>';
		}
		return $return_data;
	}

	/** Create new site **/
	public function create(){

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'details' );

		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			
			$postcode_regions     		= $this->webapp_service->api_dispatcher( $this->api_end_point.'diary/regions', ['account_id'=>$this->user->account_id, 'limit'=>-1], [ 'auth_token'=>$this->auth_token ], true );
			$data['postcode_regions']   = ( isset( $postcode_regions->regions ) ) ? $postcode_regions->regions : null;

			$this->_render_webpage('site/site_create_new', $data);
		}
	}

	/** Do site creation **/
	public function create_site(){
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
			$new_site	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'site/create', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  = ( isset($new_site->site) ) ? $new_site->site : null;
			$message	  = ( isset($new_site->message) ) ? $new_site->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
				$return_data['site']   = $new_site;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}

	/** Get addresses by postcode(s). This is a direct access function to the addresses model, no authenitcation required **/
	public function get_addresses_by_postcode( $postcodes = false, $site_id = false ){

		$postcodes 	= ( $this->input->post("postcodes") ) 	? $this->input->post("postcodes") : $postcodes;
		$site_id	= ( $this->input->post("site_id") ) 	? $this->input->post("site_id") : $site_id;

		$existing_locations		= [];
		if( !empty( $site_id ) ){
			$site_locations	  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/site_locations', ['account_id'=>$this->user->account_id, 'where'=>['site_id'=>$site_id ] ], ['auth_token'=>$this->auth_token], true );
			$site_locations		= ( isset( $site_locations->locations ) ) ? $site_locations->locations : [];
			$existing_locations = array_column( object_to_array( $site_locations ), 'address_id' );			
		}
		
		if( $postcodes ){
			$addresses_list = "";
			$site_address 	= "<option value=''>Please select</option>";
			$addresses = $this->address_bank_service->get_addresses( urldecode( $postcodes ) );

			if( !empty( $addresses ) ){
				$addresses_list .= '<div class="site-locations-container">';
					$addresses_list .= '<div class="checkbox" style="margin:0">';
						$addresses_list .= '<label>';
							$addresses_list .= '<input type="checkbox" class="" id="check_all" /> Tick all';//Type 1 residence
						$addresses_list .= '</label>';
					$addresses_list .= '</div>';
					foreach( $addresses as $address ){
						
						if( !empty( $existing_locations ) && in_array( $address["main_address_id"], $existing_locations) ){
							$is_checked = 'checked="checked"';
							$check_style= 'color:green;font-weight:400;font-style:italic';								
						}else{
							$is_checked = $check_style = '';								
						}
						
						//Dwelings list
						$addresses_list .= '<div class="checkbox" style="margin:0">';
							$addresses_list .= '<label style="'.$check_style.'">';
								$addresses_list .= '<input type="checkbox" '.$is_checked.' class="address-chks" name="site_locations['.$address["main_address_id"].'][address_id]" value = "'.$address["main_address_id"].'" />'.$address["summaryline"];
								#$addresses_list .= '<input type="hidden"   name="site_locations['.$address["main_address_id"].'][location_type_id]" value = "1" />';//Type 1 residence
							$addresses_list .= '</label>';
						$addresses_list .= '</div>';
						
						//Site address selection list
						$site_address .= '<option value = "'.$address["main_address_id"].'">'.$address["summaryline"].'</option>';
					}
					$addresses_list .= '<hr>';
				$addresses_list .= '</div>';
			} else {
				$addresses_list = '<p>There are no address records found for this postcode.</p>';
			}

		} else {
			$addresses_list = "<p>Please provide a postcode.</p>";
		}
		$data["site_address"] 	= $site_address;
		$data["addresses_list"] = $addresses_list;
		
		echo json_encode( $data );
	}

	/** Update SIte Details **/
	public function update_site( $site_id = false, $page = 'details' ){

		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$site_id = ( $this->input->post( 'site_id' ) ) ? $this->input->post( 'site_id' ) : ( !empty( $site_id ) ? $site_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$updates_site = $this->webapp_service->api_dispatcher( $this->api_end_point.'site/update', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  = ( isset($updates_site->site) ) ? $updates_site->site : null;
			$message	  = ( isset($updates_site->message) ) ? $updates_site->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
				$return_data['site']   = $updates_site;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}

		/*
	* Load an audit record
	*/
	public function view_audit_record( $audit_id = false ){

		$audit_id 	= ( $this->input->post( 'audit_id' ) ) ? $this->input->post( 'audit_id' ) : ( !empty( $audit_id ) ? $audit_id : null );

		$return_data = [
			'status'=>0,
			'audit_record'=>null,
			'status_msg'=>'Invalid paramaters'
		];

		if( !empty( $audit_id ) ){
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
					$audit .= '<tr><th width="30%">Evidoc ID</th><td>'.$audit_record->audit_id.'</td></tr>';
					$audit .= '<tr><th>Date Submitted</th><td>'.( valid_date( $audit_record->evidoc_completion_date ) ? date('d-m-Y H:i:s',strtotime( $audit_record->evidoc_completion_date ) ) : date('d-m-Y',strtotime( $audit_record->date_created )) ).'</td></tr>';
					$audit .= '<tr><th>Submitted by</th><td>'.$audit_record->created_by.'</td></tr>';
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

	/** Upload files. This is a Web-client only function **/
	public function upload_docs( $site_id ){

		if( !empty( $site_id ) ){

			$postdata 	= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );

			$document_group = 'site';
			$folder		 = 'sites';

			$doc_upload	= $this->document_service->upload_files( $this->user->account_id, $postdata, $document_group = 'site', $folder = 'sites' );

			redirect('webapp/site/profile/'.$site_id.'/documents' );

		}else{
			redirect('webapp/site', 'refresh');
		}
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
	
	
	private function load_stats_view( $stats = false ){
		$output = '';
		
		if( !empty( $stats ) ){
			$stats_no = count( get_object_vars( $stats ) );

			foreach( $stats as $key => $value ){
				$output .= "<div class=\"col-md-".( ceil( 12/$stats_no ) )." col-sm-".( ceil( 12/$stats_no ) )." col-xs-12\" style=\"margin:0\">";
					$output .= "<div class=\"row\">";
						$output .= "<h5 class=\"text-bold text-center\">".( ucwords( str_replace( "_", " ", $key ) ) )."</h5>";
						$output .= "<h3 class=\"text-center\">".( $value )."</h3>";
					$output .= "</div>";
				$output .= "</div>";
			}
		} else {
			$output .= "<div class=\"col-md-12 col-sm-12 col-xs-12\" style=\"margin:0\">";
				$output .= "<div class=\"row\">";
					$output .= "<h5 class=\"text-bold text-center\">No Stats available</h5>";
					$output .= "<h3 class=\"text-center\">&nbsp;</h3>";
				$output .= "</div>";
			$output .= "</div>";
		}
		
		return $output;
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
	public function delete_cost_item( $site_id = false ){
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
	
	
	/*
	* Site Locations lookup / search
	*/
	public function locations_lookup( $site_id = false, $page = 'details' ){
		
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
			
			$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/site_locations', $postdata, ['auth_token'=>$this->auth_token], true );

			$locations			= ( isset( $search_result->locations ) ) ? $search_result->locations : null;

			if( !empty( $locations ) ){

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

				$return_data = $this->load_site_locations_view( $locations );
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
	* Prepare locations view
	*/
	private function load_site_locations_view( $locations_data ){
		$return_data = '';
		if( !empty( $locations_data ) ){
			
			$location_types = $this->location_types;
			foreach( $locations_data as $k => $location_details ){
				$resident_name = '';
				$resident_name .= ( !empty( $location_details->resident_salutation ) ) ? $location_details->resident_salutation.' ' : '';
				$resident_name .= ( !empty( $location_details->resident_first_name ) ) ? $location_details->resident_first_name.' ' : '';
				$resident_name .= ( !empty( $location_details->resident_last_name ) )  ? $location_details->resident_last_name.' ' : '';
				
				$return_data .= '<tr>';
					$return_data .= '<td width="25%" >'.( !empty( $location_details->zone_name ) ? $location_details->zone_name : '' ).'</td>';					
					$return_data .= '<td width="20%" >'.( !empty( $location_details->location_name ) ? $location_details->location_name : '' ).'</td>';					
					#$return_data .= '<td width="20%" ><a href="'.base_url( '/webapp/site/location_profile/'.$location_details->location_id).'" >'.$location_details->address_line1.'</a></td>';									
					$return_data .= '<td width="25%" >'. ucwords( strtolower( $resident_name ) ) .'</td>';
					$return_data .= '<td width="20%" >'.$location_details->location_type.'</td>';									
					// $return_data .= '<td>';
						// $return_data .= '<select class="form-control" style="width:90%" >';					
							// foreach( $location_types as $type_id => $type ){
								// $return_data .= '<option value="'.$type_id.'" '.( ( $type_id == $location_details->location_type_id ) ? "selected=selected" : "" ).' >'.$type.'</option>';
							// }
						// $return_data .= '</select>';
					// $return_data .= '</td>';					
					#$return_data .= '<td width="15%" >'.( !empty( $location_details->sub_block_name ) ? ucwords( strtolower( $location_details->sub_block_name ) ) : '' ) .'</td>';	
					$return_data .= '<td width="10%" >';									
						$return_data .= '<class class="row pull-right">';
							$return_data .= '<div class="col-md-3" ><a href="'.base_url( '/webapp/site/location_profile/'.$location_details->location_id).'" ><i title="Click here to view this dwelling record" class="fas fa-edit text-blue pointer"></i></a></div>';
							#$return_data .= '<div class="col-md-3" ><i title="Click here to book a Job for this this dwelling" class="fas fa-briefcase text-green pointer"></i></div>';
							#$return_data .= '<div class="col-md-3 unlink-item" ><i title="Click here to un-link this dwelling record from the current site" class="fas fa-link text-red pointer"></i></div>';
							$return_data .= '<div class="col-md-3 delete-item" data-location_id="'.$location_details->location_id.'" ><i title="Click here to delete" class="fas fa-trash-alt text-red pointer"></i></div>';
						$return_data .= '</span>';
					$return_data .= '</td>';									
				$return_data .= '</tr>';
			}

			if( !empty( $pagination ) ){
				$return_data .= '<tr><td colspan="5" style="padding: 0;">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
		}else{
			$return_data .= '<tr><td colspan="5"><br/>'.$this->config->item("no_records").'</td></tr>';
		}
		return $return_data;
	}
	
	/** View a Dwelling profile **/
	public function location_profile( $location_id = false, $page = 'locations' ){
		
		if( !$location_id ){
			redirect( 'webapp/site/sites', 'refresh' );
		}

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
		
		# Check item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata 					= ['account_id'=>$this->user->account_id, 'where'=>['location_id'=>$location_id] ];
			$location_record			= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/site_locations', $postdata, ['auth_token'=>$this->auth_token], true );

			$data['location_details']	= ( isset( $location_record->locations ) ) ? $location_record->locations : null;

			if( !empty( $data['location_details'] ) ){
				
				$data['location_types'] = $this->location_types;
				
				switch( $page ){
					case 'schedules':
						$location_schedules 	 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/schedules', ['account_id'=>$this->user->account_id,'where'=>['location_id' => $location_id ] ], ['auth_token'=>$this->auth_token], true );
						$data['location_schedules'] = ( isset( $location_schedules->schedules ) ) 		? $location_schedules->schedules : null;
						$data['include_page'] 		= 'location_schedules.php';
						break;

					case 'assets':
						$location_assets 		 = $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/lookup', ['account_id'=>$this->user->account_id,'where'=>['location_id' => $location_id ] ], ['auth_token'=>$this->auth_token], true );
						$data['location_assets'] = ( isset( $location_assets->assets ) ) 		? $location_assets->assets : null;
						$data['include_page'] = 'location_assets.php';
						break;
					
					case 'jobs':
					
						$location_jobs 		  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/jobs', ['account_id'=>$this->user->account_id,'where'=>['location_id' => $location_id ] ], ['auth_token'=>$this->auth_token], true );
						$data['location_jobs']= ( isset( $location_jobs->jobs ) ) 		? $location_jobs->jobs : null;
						
						$job_types		 	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_types', ['account_id'=>$this->user->account_id, 'limit'=>-1], ['auth_token'=>$this->auth_token], true );
						$data['job_types'] 	  = ( isset( $job_types->job_types ) ) 		? $job_types->job_types : null;

						$job_statuses		  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_statuses', ['account_id'=>false], ['auth_token'=>$this->auth_token], true );
						$data['job_statuses'] = ( isset( $job_statuses->job_statuses ) ) ? $job_statuses->job_statuses : null;

						$operatives		  	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'user/users', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
						$data['operatives']   = ( isset( $operatives->users ) ) ? $operatives->users : null;
						
						$data['job_durations']= job_durations();
					
						$data['include_page'] = 'location_jobs.php';
						break;
					
					default:
					case 'details':
						$site_zones 		  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/site_zones', ['account_id'=>$this->user->account_id,'where'=>['site_id' => $data['location_details']->site_id ] ], ['auth_token'=>$this->auth_token], true );
						$data['site_zones']		= ( isset( $site_zones->site_zones ) ) ? $site_zones->site_zones : null;

						$data['include_page'] 	= 'location_details.php';
						
						break;
				}
				
				$this->_render_webpage('site/locations/location_details_profile', $data);
			} else {
				redirect( 'webapp/site/sites', 'refresh' );
			}
			
		}
	}
	
	
	/** Add Sites Locations **/
	public function add_site_location( $page = 'details' ){
		$return_data = [
			'status' => 0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata 	  		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$update_location 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/create_site_locations', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  		= ( isset( $update_location->locations ) )   	? $update_location->locations : null;
			$message	  		= ( isset( $update_location->message ) ) 		? $update_location->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	/** Update Location Profile Details **/
	public function update_site_location( $location_id = false, $page = 'details' ){
		$return_data = [
			'status' => 0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$location_id = ( $this->input->post( 'location_id' ) ) ? $this->input->post( 'location_id' ) : ( !empty( $location_id ) ? $location_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$update_location = $this->webapp_service->api_dispatcher( $this->api_end_point.'site/update_site_location', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  = ( isset( $update_location->location ) )   ? $update_location->location : null;
			$message	  = ( isset( $update_location->message ) ) ? $update_location->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/** Delete Site Location **/
	public function delete_site_location( $site_id = false, $location_id = false, $page = 'details' ){
		$return_data = [
			'status'=>0
		];

		$section 		= ( $this->input->post( 'page' ) ) 	? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$location_id 	= ( $this->input->post( 'location_id' ) ) ? $this->input->post( 'location_id' ) : ( !empty( $location_id ) ? $location_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	  		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$delete_attribute	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/delete_site_location', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  		= ( isset( $delete_attribute->status ) )  ? $delete_attribute->status : null;
			$message	  		= ( isset( $delete_attribute->message ) ) ? $delete_attribute->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status']			= 1;
				$return_data['site_location'] 	= $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/*
	* Buildings Schedules lookup / search
	*/
	public function schedules_lookup( $site_id = false, $page = 'details' ){
		
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
	* Prepare Building schedules view
	*/
	private function load_schedules_view( $schedules_data ){
		$return_data = '';
		if( !empty( $schedules_data ) ){
			foreach( $schedules_data as $k => $schedule_details ){
				## For cloning purposes
				$first_activity_due_date = date( 'd-m-Y', strtotime( $schedule_details->first_activity_due_date. ' + 1 year' ) ); 
				$return_data .= '<tr>';
					$return_data .= '<td width="5%" ><a href="'.base_url( '/webapp/job/schedule_profile/'.$schedule_details->schedule_id).'" >'.$schedule_details->schedule_id.'</a></td>';
					$return_data .= '<td width="28%">';
						$return_data .= '<a href="'.base_url( '/webapp/job/schedule_profile/'.$schedule_details->schedule_id).'" >'.$schedule_details->schedule_name.'</a>';
							if( !empty( $schedule_details->scheduled_job_types ) ){ foreach( $schedule_details->scheduled_job_types as $key => $job_type ){
								$return_data .= '<div><small>'.$job_type->job_type.'</small></div>';
							} } else {
								$return_data .= '<div><small class="text-red">No Job Types linked</small></div>';
							}
					$return_data .= '</td>';					
					$return_data .= '<td width="12%">'.( date( 'd-m-Y H:i:s', strtotime( $schedule_details->date_created ) ) ).'</td>';
					#$return_data .= '<td width="5%">'.( !empty( $schedule_details->scheduled_sites ) ? ( !is_array( $schedule_details->scheduled_sites ) ? count( object_to_array( $schedule_details->scheduled_sites ) ) : count( $schedule_details->scheduled_sites ) ) : '' ).'</td>';
					$return_data .= '<td width="10%">'.$schedule_details->frequency_name.'</td>';
					$return_data .= '<td width="10%">'.$schedule_details->activities_total.'</td>';
					$return_data .= '<td width="10%">'.( ( !empty( $schedule_details->expiry_date ) && valid_date( $schedule_details->expiry_date ) ) ? date( 'd-m-Y H:i:s', strtotime( $schedule_details->expiry_date ) ) : '' ).'</td>';
					$return_data .= '<td width="10%">'.$schedule_details->schedule_status.'</td>';
					$return_data .= '<td width="5%" class="text-center"><span>'.( !empty( $schedule_details->is_cloned ) && ( $schedule_details->is_cloned == 1 ) ? 'Y' : '' ).'</span></td>';
					$return_data .= '<td width="10%">';
						$return_data .= '<class class="row pull-right">';
							$return_data .= '<div class="col-md-4" ><a class="clone-schedule-btn" data-schedule_id="'.$schedule_details->schedule_id.'" data-first_activity_due_date="'.$first_activity_due_date.'" ><i title="Click here to clone this schedule record" class="fas fa-copy text-blue pointer"></i></a></div>';
							$return_data .= '<div class="col-md-4" ><a href="'.base_url( '/webapp/job/schedule_profile/'.$schedule_details->schedule_id ).'" ><i title="Click here to view this schedule record" class="fas fa-edit text-blue pointer"></i></a></div>';
							$return_data .= '<div class="hide col-md-4 delete-item" ><i title="Click here to delete this Schedule" class="delete-item fas fa-trash-alt text-red pointer"></i></div>';
						$return_data .= '</span>';
					$return_data .= '</td>';
				$return_data .= '</tr>';
			}

			if( !empty( $pagination ) ){
				$return_data .= '<tr><td colspan="10" style="padding: 0;">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
		}else{
			$return_data .= '<tr><td colspan="10"><br/>'.$this->config->item("no_records").'</td></tr>';
		}
		return $return_data;
	}
	
	
	
	/** Delete Site Profile **/
	public function delete_site( $page = 'details' ){

		$return_data = [
			'status'=>0
		];

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$section 		= ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
		$item_access 	= $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_delete ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		} else {
			$postdata 	  				= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$delete_site 				= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/delete', $postdata, ['auth_token'=>$this->auth_token], true );
			$message	  				= ( isset( $delete_site->message ) ) ? $delete_site->message : 'Oops! There was an error processing your request.';
			$return_data['result'] 		= ( isset( $delete_site->status ) ) ? true : false ;
			$return_data['status'] 		= ( isset( $delete_site->status ) ) ? $delete_site->status : false ;
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/** Delete Document Resource **/
	public function delete_document( $document_id = false, $page = 'details' ){
		$return_data = [
			'status'=>0
		];

		$section 		= ( $this->input->post( 'page' ) ) 	? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$document_id 	= ( $this->input->post( 'document_id' ) ) ? $this->input->post( 'document_id' ) : ( !empty( $document_id ) ? $document_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	  		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$delete_document	= $this->webapp_service->api_dispatcher( $this->api_end_point.'document_handler/delete_document', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  		= ( isset( $delete_document->status ) )  ? $delete_document->status : null;
			$message	  		= ( isset( $delete_document->message ) ) ? $delete_document->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status']		= 1;
				$return_data['document'] 	= $result;
			}
			$return_data['status_msg'] 		= $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	/** View Sub Block profile **/
	public function sub_block_profile( $sub_block_id = false, $page = 'sub_blocks' ){
		
		if( !$sub_block_id ){
			redirect( 'webapp/site/sites', 'refresh' );
		}

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
		
		# Check item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata 				= ['account_id'=>$this->user->account_id, 'where'=>['sub_block_id'=>$sub_block_id] ];

			$sub_block_record		= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/site_sub_blocks', $postdata, ['auth_token'=>$this->auth_token], true );
			$data['sub_block_details']	= ( isset( $sub_block_record->site_sub_blocks ) ) ? $sub_block_record->site_sub_blocks : null;

			if( !empty( $data['sub_block_details'] ) ){
				
				switch( $page ){
					default:
					case 'details':
						$data['include_page'] 	= 'sub_block_details.php';
						
						break;
				}
				
				$this->_render_webpage('site/sub_blocks/sub_block_profile', $data);
			} else {
				redirect( 'webapp/site/sites', 'refresh' );
			}
			
		}
	}
	
	/** Add Site Sub Block **/
	public function add_site_sub_block( $page = 'details' ){
		$return_data = [
			'status' => 0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata 	  		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$update_location 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/add_site_sub_block', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  		= ( isset( $update_location->site_sub_block ) )   	? $update_location->site_sub_block : null;
			$message	  		= ( isset( $update_location->message ) ) 			? $update_location->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/** Update Sub Block Profile Details **/
	public function update_sub_block( $sub_block_id = false, $page = 'details' ){
		$return_data = [
			'status' => 0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$sub_block_id = ( $this->input->post( 'sub_block_id' ) ) ? $this->input->post( 'sub_block_id' ) : ( !empty( $sub_block_id ) ? $sub_block_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$update_sub_block = $this->webapp_service->api_dispatcher( $this->api_end_point.'site/update_site_sub_block', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  = ( isset( $update_sub_block->site_sub_block ) )  ? $update_sub_block->site_sub_block : null;
			$message	  = ( isset( $update_sub_block->message ) ) 		? $update_sub_block->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/** Delete Site Sub Block **/
	public function delete_sub_block( $site_id = false, $sub_block_id = false, $page = 'details' ){
		$return_data = [
			'status'=>0
		];

		$section 		= ( $this->input->post( 'page' ) ) 	? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		$sub_block_id 	= ( $this->input->post( 'sub_block_id' ) ) ? $this->input->post( 'sub_block_id' ) : ( !empty( $sub_block_id ) ? $sub_block_id : null );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	  		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$delete_attribute	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/delete_site_sub_block', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  		= ( isset( $delete_attribute->status ) )  ? $delete_attribute->status : null;
			$message	  		= ( isset( $delete_attribute->message ) ) ? $delete_attribute->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status']		= 1;
				$return_data['sub_block'] 	= $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/** Get list of sites **/
	public function non_compliant_buildings( $system_id = false, $range_index = false ){

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			$site_statuses 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/site_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['site_statuses']	= ( isset($site_statuses->site_statuses) ) ? $site_statuses->site_statuses : null;;
			$data['current_user']	= $this->user;
			
			$this->_render_webpage( 'site/non_compliant_buildings', $data);
		}
	}
	
	
	/*
	* No Compliant Building lookup
	*/
	public function non_compliant_buildings_lookup( $page = 'details' ){

		$return_data = '';

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){

			$return_data .= $this->config->item( 'ajax_access_denied' );

		} else {

			# Setup search parameters
			$building_id   		= ( $this->input->post( 'building_id' ) ) ? $this->input->post( 'building_id' ) : false;
			$search_term   	= ( $this->input->post( 'search_term' ) ) ? $this->input->post( 'search_term' ) : false;
			$where   	   	= ( $this->input->post( 'where' ) ) ? $this->input->post( 'where' ) : false;
			$limit		   	= ( !empty( $where['limit'] ) )  ? $where['limit']  : DEFAULT_LIMIT;
			$start_index   	= ( $this->input->post( 'start_index' ) ) ? $this->input->post( 'start_index' ) : DEFAULT_OFFSET;
			$offset		   	= ( !empty( $start_index ) ) ? ( ( $start_index - 1 ) * $limit ) : 0;
			$order_by	   	= ( $this->input->post( 'order_by' ) ) ? $this->input->post( 'order_by' ) : false;

			#prepare postdata
			$postdata = [
				'account_id'	=> $this->user->account_id,
				'building_id'		=> $building_id,
				'search_term'	=> $search_term,
				'where'			=> $where,
				'order_by'		=> $order_by,
				'limit'			=> $limit,
				'offset'		=> $offset
			];

			$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/non_compliant_buildings', $postdata, ['auth_token'=>$this->auth_token], true );

			$buildings			= ( isset( $search_result->buildings ) ) ? $search_result->buildings : null;

			if( !empty( $buildings ) ){

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

				$return_data = $this->load_non_compliant_buildings( $buildings );
				if( !empty( $pagination ) ){
					$return_data .= '<tr><td colspan="5" style="padding: 0;">';
						$return_data .= $page_display.$pagination;
					$return_data .= '</td></tr>';
				}
			}else{
				$return_data .= '<tr><td colspan="5" style="padding: 0;"><br/>';
					$return_data .= ( isset($search_result->message) ) ? $search_result->message : 'No records found';
				$return_data .= '</td></tr>';
			}
		}

		print_r( $return_data );
		die();
	}


	/*
	* Prepare Non Compliant Buildings views
	*/
	private function load_non_compliant_buildings( $buildings_data, $view_type = false ){
		$return_data = '';
		if( !empty( $buildings_data ) ){
			foreach( $buildings_data as $k => $site_details ){
				
				$dateOne = new DateTime();
				if( valid_date( $site_details->audit_result_timestamp ) ){
					$dateTwo 		= new DateTime( $site_details->audit_result_timestamp );
					$days_elapsed 	= $dateTwo->diff( $dateOne )->format( "%a" ).' days';
				} else {
					$days_elapsed 	= '';
				}
				
				$short_site_address  = '';
				$short_site_address .= !empty( $site_details->address_line_2 ) ? $site_details->address_line_2 : '';
				$short_site_address .= !empty( $site_details->address_line_3 ) ? $site_details->address_line_3 : '';
				
				$return_data .= '<tr>';
					$return_data .= '<td><a href="'.base_url('/webapp/site/profile/'.$site_details->site_id).'" >'.$site_details->site_name.'</a></td>';
					$return_data .= '<td>'.( !empty( $site_details->estate_name ) ? $site_details->estate_name.' - ' : '' ).strtoupper( !empty( $site_details->site_postcodes ) ? $site_details->site_postcodes : $site_details->postcode ).'</td>';
					$return_data .= '<td><a href="'.base_url('/webapp/site/profile/'.$site_details->site_id).'/systems" >'.( !empty( $site_details->installed_systems ) ? $site_details->installed_systems : "" ).'</a></td>';
					$return_data .= '<td>'.$site_details->result_status_alt.'</td>';
					$return_data .= '<td>'.( valid_date( $site_details->audit_result_timestamp ) ? date( 'd-m-Y H:i:s', strtotime( $site_details->audit_result_timestamp ) ) : '' ).'</td>';
					$return_data .= '<td>'.$days_elapsed.'</td>';
				$return_data .= '</tr>';
			}

			if( !empty( $pagination ) ){
				$return_data .= '<tr><td colspan="5" style="padding: 0;">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
		}else{
			$return_data .= '<tr><td colspan="5"><br/>'.$this->config->item("no_records").'</td></tr>';
		}
		return $return_data;
	}
	
	
	/** Upload Buildings **/
	public function upload_buildings(){
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
			$this->_render_webpage( 'site/uploads/upload_buildings', $data );
		}
	}
	
	
	/** Upload Buildings Files **/
	public function submit_upload_buildings_file( $account_id = false ){

		if( !empty( $account_id ) && !empty( $_FILES['upload_file']['name'] ) ){

			$processed_file = $this->document_service->upload_buildings( $account_id );

			if( $processed_file ){
				redirect( '/webapp/site/pending_building_uploads/'.$account_id );
			}

		} else {
			$this->session->set_flashdata( 'message', 'No files were selected' );	
			redirect( '/webapp/site/upload_buildings/' );
		}
	}


	/** Review Uploaded Buildings **/
	public function pending_building_uploads( $account_id = false ){

		if( !empty( $account_id ) ){
			$pending_buildings 			= $this->document_service->get_pending_upload_buildings( $account_id );
			$data['pending_buildings']	= ( !empty( $pending_buildings ) ) ? $pending_buildings : null;
			
			$building_types		 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'building/building_types', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['building_types'] 	= ( isset($building_types->building_types) ) ? $building_types->building_types : null;

			$this->_render_webpage('/site/uploads/pending_building_uploads', $data);
		} else {
			redirect( '/webapp/site/upload_buildings/' );
		}

	}
	
	
	/** Process Building Uploads **/
	public function process_building_uploads( $account_id = false ){
		
		$account_id = $this->user->account_id;
		if( !empty( $account_id ) && ( $this->input->post( 'sites_data' ) ) ){
			$postdata 	= $this->input->post();
			$result 	= $this->document_service->process_building_uploads( $account_id, $postdata );
			if( $result ){
				$data['processed_data'] 	= json_decode( json_encode( $result ) );
				if( !empty( $data['processed_data'] ) ){
					$data['successful_records'] = isset( $data['processed_data']->buildings_created_successfully ) ? $data['processed_data']->buildings_created_successfully : false;
					unset( $data['processed_data']->buildings_created_successfully );
					$this->_render_webpage('/site/uploads/processed_building_uploads', $data );
				} else {
					redirect( '/webapp/site/pending_building_uploads/'.$account_id );
				}
			} else {
				redirect( '/webapp/site/pending_building_uploads/'.$account_id );
			}
		} else {
			$this->session->set_flashdata( 'message', 'Invalid paramaters' );	
			redirect( '/webapp/site/upload_buildings/' );
		}

	}
	
	/** Delete Temp Building Records **/
	public function drop_temp_building_records( $account_id = false ){
		$account_id = $this->user->account_id;
		if( !empty( $account_id ) && ( $this->input->post( 'sites_data' ) ) ){
			$postdata 	= $this->input->post();
			$result 	= $this->document_service->drop_temp_building_records( $account_id, $postdata );
		} else {
			$this->session->set_flashdata( 'message', 'Invalid paramaters' );	
		}
		redirect( '/webapp/site/pending_building_uploads/'.$account_id );
	}
	
	
	/** Pull Tesseract Sites **/
	public function fetch_tesseract_sites_by_site_number( $site_number = false, $page = 'details' ){
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
			$postdata 	  		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$tesseract_sites 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'tesseract/site_by_site_number', $postdata, ['auth_token'=>$this->auth_token], true );
			$result		  		= ( isset( $tesseract_sites->site ) ) ? $tesseract_sites->site : null;
			$message	  		= ( isset( $tesseract_sites->message ) ) ? $tesseract_sites->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 			= 1;
				$return_data['tesseract_sites'] = $result;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/** Get list of Zone locations **/
	public function get_zone_locations(){
		
		$postdata 		 = ( $this->input->post() ) ? $this->input->post() : false;
		$zone_locations  = '';
		$site_id 		 = !empty( $postdata['site_id'] ) ? $postdata['site_id']: false;
		$zone_id 		 = !empty( $postdata['zone_id'] ) ? $postdata['zone_id']: false;
			
		if( !empty( $site_id ) && !empty( $zone_id ) ){
			$result		  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/site_locations', ['account_id'=>$this->user->account_id, 'where'=>[ 'site_id'=>$site_id, 'zone_id'=>$zone_id ], 'limit'=> -1 ], ['auth_token'=>$this->auth_token], true );
			$locations		= !empty( $result->locations ) ? $result->locations : false;

			if( $locations ){
				$zone_locations .= '<option value="">Please select location...</option>';
				foreach( $locations as $location ){
					$zone_locations .= '<option value = "'.$location->location_id.'" >'.$location->zone_name.' - '.$location->location_name.'</option>';
				}
			} else {
				$zone_locations .= '<option value="" disabled="disabled" >No locations found for this Zone</option>';
			}

		} else {
			$zone_locations .= '<option disabled="disabled">No locations found</option>';
		}
		$data['zone_locations'] = $zone_locations;
		echo json_encode( $data );
	}
	
	
	/** Add Site Zone **/
	public function add_site_zone( $page = 'details' ){
		$return_data = [
			'status' => 0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata 	  		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$new_zone 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/add_site_zone', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  		= ( isset( $new_zone->site_zone ) )   	? $new_zone->site_zone : null;
			$message	  		= ( isset( $new_zone->message ) ) 	? $new_zone->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['zone']   = $result;
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/** Add New Location **/
	public function add_new_location( $page = 'details' ){
		$return_data = [
			'status' => 0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata 	  		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$new_location 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/create_site_locations', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  		= ( isset( $new_location->locations ) )   	? $new_location->locations : null;
			$message	  		= ( isset( $new_location->message ) ) 	? $new_location->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['location']   = $result;
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	
	/**
	* Clone Site Record
	**/
	public function clone_site( $site_id = false ){

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
			
			$postdata 	  	= array_merge( [ 'account_id'=>$this->user->account_id ], $this->input->post() );
			$clone_site   	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/clone_site' , $postdata, ['auth_token'=>$this->auth_token] );
			$result		  	= ( isset( $clone_site->site ) ) 	? $clone_site->site : null;
			$message	  	= ( isset( $clone_site->message ) )  	? $clone_site->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status']  = 1;
				$return_data['site_id'] = $result->site_id;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
}