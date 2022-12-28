<?php

namespace Application\Modules\Web\Controllers;

//Load the alaert manager interface controller
require('AlertManager.php');

class Alert extends MX_Controller {

	function __construct(){
		parent::__construct();
		if( !$this->identity() ){
			redirect('webapp/user/login', 'refresh');
		}
		
		//$this->amgr = new AlertManager();
		
		$this->module_id = $this->webapp_service->_get_module_id( $this->router->fetch_class() );
		$this->load->library('pagination');
		$this->load->model('serviceapp/Site_model','site_service');
		$this->load->model('serviceapp/Address_Bank_model','address_bank_service');
	
		$this->module_access = $this->webapp_service->check_access( $this->user, $this->module_id );
	}
	
	function index(){
		
		#debug( $this->user );
		
		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );
		if( !$this->user->is_admin && !$module_access ){
			$this->_render_webpage( 'errors/access-denied', false );
		}else{
			$site_statuses 			= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/site_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['site_statuses']	= ( isset($site_statuses->site_statuses) ) ? $site_statuses->site_statuses : null;;
			$data['current_user']	= $this->user;
			
			$site_compliance 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'alert_handler/site_compliance', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['site_compliance']= ( isset( $site_compliance->site_compliance ) ) ? $site_compliance->site_compliance : null;
			$this->_render_webpage('alert/index', $data);
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
	
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
	
		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		
		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		}else if( $site_id ){
			$run_admin_check 	 = false;
			$site_details		 = $this->webapp_service->api_dispatcher( $this->api_end_point.'site/sites', ['account_id'=>$this->user->account_id,'site_id'=>$site_id], ['auth_token'=>$this->auth_token], true );
			$data['site_details']= ( isset($site_details->sites) ) ? $site_details->sites : null;
			if( !empty( $data['site_details'] ) ){
				$run_admin_check 	= false;
				#Get allowed access for the logged in user
				$data['permissions']= $item_access;
				$data['active_tab']	= $page;
				
				$module_items 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'access/account_modules_items', ['account_id'=>$this->user->account_id, 'module_id'=>$this->module_id ], ['auth_token'=>$this->auth_token], true );			
				$data['module_tabs']= ( isset( $module_items->module_items ) ) ? $module_items->module_items : null;

				$site_panels		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/panels', ['account_id'=>$this->user->account_id, 'site_id'=>$site_id, 'include_assets'=>true ], ['auth_token'=>$this->auth_token], true );
				$data['site_panels']    = ( isset( $site_panels->site_panels ) ) ? $site_panels->site_panels : null;
				
				$site_dwellings		  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/dwellings', ['account_id'=>$this->user->account_id,'site_id'=>$site_id], ['auth_token'=>$this->auth_token], true );
				$data['site_dwellings']	= ( isset( $site_dwellings->site_dwellings ) ) ? $site_dwellings->site_dwellings : [];
				$data['existing_dwellings'] = array_column( object_to_array( $data['site_dwellings'] ), 'address_id' );
				
				switch( $page ){
					case 'jobs':
						// $site_jobs 		  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/jobs', ['account_id'=>$this->user->account_id,'site_id'=>$site_id], ['auth_token'=>$this->auth_token], true );
						// $data['site_jobs']= ( isset($site_jobs->jobs) ) ? $site_jobs->jobs : null;
						// $data['include_page'] = 'site_jobs.php';
						break;
					case 'dwellings':
						$site_addresses	 		= $this->address_bank_service->get_addresses( $data['site_details']->site_postcodes ); // allow direct access to address for speed as this is likely to be big lists
						$data['site_addresses'] = ( !empty( $site_addresses ) ) ? $site_addresses : [];
						
						$data['include_page'] 	= 'site_dwellings.php';						
						break;
					case 'contracts':
						$site_contracts		  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/contracts', ['account_id'=>$this->user->account_id,'site_id'=>$site_id], ['auth_token'=>$this->auth_token], true );
						$data['site_contracts']	= ( isset($site_contracts->site_contracts) ) ? $site_contracts->site_contracts : [];
						
						$data['include_page'] = 'site_contracts.php';
						break;
					case 'alerts':
						$site_alerts			= $this->webapp_service->api_dispatcher( $this->api_end_point.'alert_handler/alerts', ['account_id'=>$this->account_id,'site_id'=>$site_id], ['auth_token'=>$this->auth_token], true );
						$data['site_alerts']	= ( isset($site_alerts->alerts) ) ? $site_alerts->alerts : null;
						$data['event_site_id']	= ( !empty( $data['site_alerts'] ) ) ? key( (array)$data['site_alerts'] ) : null;
						$data['include_page'] = 'site_alerts.php';
						break;
					case 'documents':
						$audit_group 			= 'site';
						$audit_documents		= $this->webapp_service->api_dispatcher( $this->api_end_point.'document_handler/document_list', ['account_id'=>$this->user->account_id, 'site_id'=>$site_id, 'audit_group'=>$audit_group ], ['auth_token'=>$this->auth_token], true );
						$data['audit_documents']= ( isset( $audit_documents->documents->{$this->user->account_id} ) ) ? $audit_documents->documents->{$this->user->account_id} : null;
						$data['include_page'] = 'site_documents.php';
						break;
					case 'audits':
						$site_audits	  	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/audits', ['account_id'=>$this->user->account_id, 'site_id'=>$site_id ], ['auth_token'=>$this->auth_token], true );
						$data['site_audits'] = ( isset( $site_audits->audits ) ) ? $site_audits->audits : null;
						$data['include_page'] = 'site_audits.php';
						break;
					case 'devices':
						$data['include_page'] = 'site_devices.php';
						break;
					case 'details':
					default:
						$event_statuses		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/event_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
						$data['event_statuses'] = ( isset( $event_statuses->event_statuses ) ) ? $event_statuses->event_statuses : null;
						
						$data['include_page'] 	= 'site_details.php';			
						break;
				}

			}
			
			//Run the admin check if tab needs only admin
			if( !empty( $run_admin_check ) ){
				if( ( !admin_check( $this->user->is_admin, false, ( !empty( $data['permissions'] ) ? $data['permissions']->is_admin : false ) ) ) ){
					$data['admin_no_access'] = true;
				}
			}
			
			$this->_render_webpage('alert/profile', $data);
		}else{
			redirect('webapp/alert', 'refresh');
		}
	}
	
	/*
	* Loads Sites / search
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
			$search_term   = ( $this->input->post( 'search_term' ) ) ? $this->input->post( 'search_term' ) : false;
			$block_statuses= ( $this->input->post( 'block_statuses' ) ) ? $this->input->post( 'block_statuses' ) : false;
			$limit		   = ( $this->input->post( 'limit' ) )  ? $this->input->post( 'limit' )  : DEFAULT_LIMIT;
			$start_index   = ( $this->input->post( 'start_index' ) )  ? $this->input->post( 'start_index' )  : 0;
			$offset		   = ( !empty( $start_index ) ) ? ( ( $start_index - 1 ) * $limit ) : 0;
			$order_by	   = false;
			$where		   = ( $this->input->post( 'monitored' ) ) ? ['monitored'=>$this->input->post( 'monitored' ) ] : false;
			
			#prepare postdata
			$postdata = [
				'account_id'=>$this->user->account_id,
				'search_term'=>$search_term,
				'block_statuses'=>$block_statuses,
				'where'=>$where,
				'order_by'=>$order_by,
				'limit'=>$limit,
				'offset'=>$offset
			];

			
			$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/lookup', $postdata, ['auth_token'=>$this->auth_token], true );
			$sites			= ( isset( $search_result->sites ) ) ? $search_result->sites : null;
			
			if( !empty( $sites ) ){

				## Create pagination
				$counters 		= $this->site_service->get_total_sites( $this->user->account_id, $search_term, $block_statuses, $where, $order_by, $limit, $offset );//Direct access to count, this should only return a number
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

				$return_data = $this->load_sites_view( $sites );
				if( !empty($pagination) ){
					$return_data .= '<div class="row" style="margin:-15px 0px;"><hr><div class="col-md-12">';
						$return_data .= $page_display.$pagination;
					$return_data .= '</div></div>';
				}				
			}else{
				$return_data .= '<br/>';
				$return_data .= ( isset($search_result->message) ) ? $search_result->message : 'No records found';
			}
		}

		print_r( $return_data );
		die();
	}
	
	/*
	* Prepare sites views
	*/
	private function load_sites_view( $sites_data ){
		$return_data = '';
		if( !empty($sites_data) ){
			foreach( $sites_data as $k => $site_details ){
				$return_data .= '<a href="'.base_url('/webapp/alert/profile/'.$site_details->site_id).'" ><div class="col-md-6 col-sm-6 col-xs-12" >';
					$return_data .= '<div class="alert alert-'.strtolower( $site_details->status_group ).'" role="alert">';
						$return_data .= '<div class="row">';
							$return_data .= '<div class="col-md-8 col-sm-8 col-xs-8" ><strong title="Click to go to '.$site_details->site_name.'" style="font-size:16px; font-weight:300">'.ucwords( strtolower( $site_details->site_name ) ).' - '.strtoupper( $site_details->postcode ).'</strong> <!-- <br/><small style="font-weight:300">'.$site_details->summaryline.'</small> --></div>';
							$return_data .= '<div class="col-md-4 col-sm-4 col-xs-4" ><span title="Click to view Alerts" class="pull-right" style="vertical-align:middle">'.strtoupper( ( !empty( $site_details->event_tracking_status ) ) ? $site_details->event_tracking_status : "Status Not Set" ).'</span></div>';
							#$return_data .= '<div class="col-md-4 col-sm-4 col-xs-4" ><span class="pull-right" style="vertical-align:middle" title="This Site is '.strtoupper( $site_details->event_tracking_status ).'" >'.$site_details->icon_class.'</span></div>';
						$return_data .= '</div>';
					$return_data .= '</div>';
				$return_data .= '</div></a>';
			}

			if( !empty($pagination) ){
				$return_data .= '<div class="row"><div class="col-md-12">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</div></div>';
			}
		}else{
			$return_data .= '<div class="row"><div class="col-md-12"><br/>'.$this->config->item("no_records").'</div></div>';
		}
		return $return_data;
	}
	
	/** Get addresses by postcode(s). This is a direct access function to the addresses model, no authenitcation required **/
	public function get_addresses_by_postcode( $postcodes = false ){
		
		$postcodes = ( $this->input->post("postcodes") ) ? $this->input->post("postcodes") : $postcodes;

		if( $postcodes ){
			$addresses_list = "";
			$addresses = $this->address_bank_service->get_addresses( urldecode($postcodes) );
			
			if( !empty( $addresses ) ){
				foreach( $addresses as $address ){
					$addresses_list .= '<div class="checkbox" style="margin:0">';
						$addresses_list .= '<label><input type="checkbox" name="site_dwellings[]" checked="checked" value = "'.$address["main_address_id"].'" />'.$address["summaryline"].'</label>';
					$addresses_list .= '</div>';
				}
				$addresses_list .= "<hr>";
			} else {
				$addresses_list = "<p>There are no address records found for this postcode.</p>";
			}
			
		} else {
			$addresses_list = "<p>Please provide a postcode.</p>";
		}
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
}
	