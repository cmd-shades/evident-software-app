<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Audit extends MX_Controller {

	function __construct(){
		parent::__construct();
		
		if( !$this->identity() ){
			redirect('webapp/user/login', 'refresh');
		}
		
		$this->module_id 	   = $this->webapp_service->_get_module_id( $this->router->fetch_class() );
		$this->load->library('pagination');		
	
		$this->module_access = $this->webapp_service->check_access( $this->user, $this->module_id );
		
		$this->load->model('serviceapp/Audit_model','audit_service');
	
	}

	//redirect if needed, otherwise display the user list
	function index(){
		
		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){
			//access denied
			$this->_render_webpage( 'errors/access-denied', false );
		}else{
			redirect('webapp/audit/audits', 'refresh');
		}		
	}

	/** Get list of audits **/
	public function audits( $audit_id = false ){
		
		if( $audit_id ){
			redirect('webapp/audit/profile/'.$audit_id, 'refresh');
		}
		
		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){
			$this->_render_webpage('errors/access-denied', false);
		}else{
			$data['audit_statuses'] = ['Completed','In Progress'];
			
			$audit_types		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/audit_types', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['audit_types'] 	= ( isset($audit_types->audit_types) ) ? $audit_types->audit_types : null;

			$this->_render_webpage('audit/index', $data);
		}
	}

	//View user profile
	function profile( $audit_id = false, $page = 'details' ){
	
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
	
		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		
		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage('errors/access-denied', false);	
		}else if( $audit_id ){
			$run_admin_check 	  = false;
			$audit_details		  = $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/audits', ['account_id'=>$this->user->account_id,'audit_id'=>$audit_id], ['auth_token'=>$this->auth_token], true );
			$data['audit_details']= ( isset($audit_details->audits) ) ? $audit_details->audits : null;
			if( !empty( $data['audit_details'] ) ){
				
				#Get allowed access for the logged in user
				$data['permissions']= $item_access;
				$data['active_tab']	= $page;
				
				$module_items 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'access/account_modules_items', ['account_id'=>$this->user->account_id, 'module_id'=>$this->module_id ], ['auth_token'=>$this->auth_token], true );			
				$data['module_tabs']= ( isset( $module_items->module_items ) ) ? $module_items->module_items : null;			
				
				switch( $page ){
					case 'documents':
					
						if( in_array( strtolower( $data['audit_details']->audit_group ), ['asset'] ) ){
							$audit_group = 'asset';
						}else if( in_array( strtolower( $data['audit_details']->audit_group ), ['site'] ) ){
							$audit_group = 'site';
						}else if( in_array( strtolower( $data['audit_details']->audit_group), ['vehicle'] ) ){
							$audit_group = 'fleet';
						}else{
							$audit_group = 'other';
						}

						$audit_documents		= $this->webapp_service->api_dispatcher( $this->api_end_point.'document_handler/document_list', ['account_id'=>$this->user->account_id, 'audit_id'=>$audit_id, 'audit_group'=>$audit_group ], ['auth_token'=>$this->auth_token], true );
						$data['audit_documents']= ( isset( $audit_documents->documents->{$this->user->account_id} ) ) ? $audit_documents->documents->{$this->user->account_id} : null;
						$data['include_page'] 	= 'audit_documents.php';
						break;
						
					case 'details':
					default:
						$audit_types		 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/audit_types', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
						$data['audit_types'] 	= ( isset($audit_types->audit_types) ) ? $audit_types->audit_types : null;

						$users		  	  		= $this->webapp_service->api_dispatcher( $this->api_end_point.'user/users', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
						$data['users']  	  	= ( isset($users->users) ) ? $users->users : null;
						
						$data['include_page'] 	= 'audit_details.php';			
						break;
				}
			}
			
			//Run the admin check if tab needs only admin
			if( !empty( $run_admin_check ) ){
				if( ( !admin_check( $this->user->is_admin, false, ( !empty( $data['permissions'] ) ? $data['permissions']->is_admin : false ) ) ) ){
					$data['admin_no_access'] = true;
				}
			}
			
			$this->_render_webpage('audit/profile', $data, '');
		}else{
			redirect('webapp/audit', 'refresh');
		}
	}
	
	/** Create new audit **/
	public function create( $page = 'details' ){
		
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		
		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage('errors/access-denied', false);
		}else{
			$audit_types	 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/audit_types', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
			$data['audit_types']= ( isset($audit_types->audit_types) ) ? $audit_types->audit_types : null;

			$this->_render_webpage('audit/audit_create_new', $data);
		}
	}
	
	/** Do audit creation **/
	public function create_audit(){
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
			$new_audit	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/create', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  = ( isset($new_audit->audit) ) ? $new_audit->audit : null;
			$message	  = ( isset($new_audit->message) ) ? $new_audit->message : 'Request completed!';  
			if( !empty( $result ) ){
				$return_data['status'] = 1;
				$return_data['audit']   = $new_audit;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}
	
	/*
	* Audit lookup / search
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
			$audit_types   = ( $this->input->post( 'audit_types' ) ) ? $this->input->post( 'audit_types' ) : false;
			$audit_statuses= ( $this->input->post( 'audit_statuses' ) ) ? $this->input->post( 'audit_statuses' ) : false;
			$limit		   = ( $this->input->post( 'limit' ) )  ? $this->input->post( 'limit' )  : DEFAULT_LIMIT;
			$start_index   = ( $this->input->post( 'start_index' ) )  ? $this->input->post( 'start_index' )  : 0;
			$offset		   = ( !empty( $start_index ) ) ? ( ( $start_index - 1 ) * $limit ) : DEFAULT_OFFSET;
			$order_by	   = false;
			$where		   = false;
			
			#prepare postdata
			$postdata = [
				'account_id'=>$this->user->account_id,
				'search_term'=>$search_term,
				'audit_types'=>$audit_types,
				'audit_statuses'=>$audit_statuses,
				'where'=>$where,
				'order_by'=>$order_by,
				'limit'=>$limit,
				'offset'=>$offset
			];

			$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/lookup', $postdata, ['auth_token'=>$this->auth_token], true );
			
			$audits			= ( isset( $search_result->audits ) ) ? $search_result->audits : null;

			if( !empty($audits) ){

				## Create pagination
				$counters 		= $this->audit_service->get_total_audits( $this->user->account_id, $search_term, $audit_statuses, $audit_types, $where, $order_by, $limit, $offset );//Direct access to count, this should only return counters
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

				$return_data = $this->load_audits_view( $audits );
				if( !empty($pagination) ){
					$return_data .= '<tr><td colspan="6" style="padding: 0;">';
						$return_data .= $page_display.$pagination;
					$return_data .= '</td></tr>';
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
	* Prepare audits views
	*/
	private function load_audits_view( $audits_data ){
		$return_data = '';
		if( !empty($audits_data) ){
			foreach( $audits_data as $k => $audit_details ){
				$return_data .= '<tr>';
					//$return_data .= '<td><a href="'.base_url('/webapp/audit/profile/'.$audit_details->audit_id).'" >'.$audit_details->audit_id.'</a></td>';
					$return_data .= '<td><a href="'.base_url('/webapp/audit/profile/'.$audit_details->audit_id).'" >'.$audit_details->alt_audit_type.'</a></td>';
					$return_data .= '<td><a href="'.base_url('/webapp/audit/profile/'.$audit_details->audit_id).'" >'.$audit_details->asset_name.'</a></td>';
					$return_data .= '<td><a href="'.base_url('/webapp/audit/profile/'.$audit_details->audit_id).'" >'.$audit_details->site_name.'</a></td>';			
					$return_data .= '<td><a href="'.base_url('/webapp/audit/profile/'.$audit_details->audit_id).'" >'.$audit_details->vehicle_reg.'</a></td>';
					$return_data .= '<td>'.$audit_details->created_by.'</td>';
					$return_data .= '<td>'.$audit_details->audit_status.'</td>';
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
	public function update_audit( $audit_id = false, $page = 'details' ){
		$return_data = [
			'status'=>0
		];
		
		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
		
		$audit_id = ( $this->input->post( 'audit_id' ) ) ? $this->input->post( 'audit_id' ) : ( !empty( $audit_id ) ? $audit_id : null );
		
		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';	
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		}else{
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$updates_audit= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/update', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  = ( isset($updates_audit->audit) ) ? $updates_audit->audit : null;
			$message	  = ( isset($updates_audit->message) ) ? $updates_audit->message : 'Request completed!';  
			if( !empty( $result ) ){
				$return_data['status']= 1;
				$return_data['audit'] = $result	;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();	
	}
	
	/** Upload a Audit document **/
	public function upload_docs( $audit_id = false, $page = 'details' ){
		$return_data = [
			'status'=>0
		];
		
		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
		
		$audit_id = ( $this->input->post( 'audit_id' ) ) ? $this->input->post( 'audit_id' ) : ( !empty( $audit_id ) ? $audit_id : null );
		
		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';	
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
			redirect('webapp/audit', 'refresh');
		}else{
			$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$upload_doc   = $this->webapp_service->api_dispatcher( $this->api_end_point.'document_handler/upload', $postdata, ['auth_token'=>$this->auth_token] );
			$result		  = ( isset($upload_doc->documents) ) ? $upload_doc->documents : null;
			$message	  = ( isset($upload_doc->message) ) ? $upload_doc->message : 'Request completed!';  
			if( !empty( $result ) ){
				redirect('webapp/audit/profile/'.$audit_id.'/documents', 'refresh');
			}			
		}
		redirect('webapp/audit/', 'refresh');
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

		if( !empty($assessment_id) ){
			$ra_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'risk_assessment/ra_records', ['account_id'=>$this->user->account_id,'assessment_id'=>$assessment_id], ['auth_token'=>$this->auth_token], true );
			$result		= ( isset($ra_result->ra_records) ) ? $ra_result->ra_records : null;
			$message	= ( isset($ra_result->message) ) ? $ra_result->message : 'Request completed!';
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
					$ra .= '<tr><th>Risks Completed</th><td><i class="far  '.( ( $ra_record->risks_completed == 1 ) ? " fa-check-circle text-green " : " fa-times-circle text-red" ).' "></i></td></tr>';	
					$ra .= '<tr><th>Documents Uploaded</th><td><i class="far  '.( ( $ra_record->documents_uploaded == 1 ) ? " fa-check-circle text-green " : " fa-times-circle text-red" ).' "></i></td></tr>';	
					$ra .= '<tr><th>Signature Uploaded</th><td><i class="far  '.( ( $ra_record->signature_uploaded == 1 ) ? " fa-check-circle text-green " : " fa-times-circle text-red" ).' "></i></td></tr>';	
					$ra .= '<tr><th colspan="2">&nbsp;</th></tr>';
					$ra .= '<tr><th colspan="2"><span style="font-weight:400">RESPONSES</span><hr></th></tr>';
					$ra .= '<tr><td colspan="2"><table style="width:100%;display:table">';
					$ra .= '<tr><th width="10%">ID</th><th width="75%">Risk Text / Question</th><th width="15%">Response</th></tr>';
						foreach( $ra_record->ra_responses as $k=>$ra_item ){ $k++;
							$ra .= '<tr><td>'.$k.'</td><td>'.$ra_item->risk_text.'</td><td>'.$ra_item->risk_response.'</td></tr>';
						}
					$ra .= '</table></td></tr>';					
			$ra .= '</table>';
		}
		return $ra;
	}
	
	/** Download access **/
	public function download( $audit_id = false ){
		if( !empty( $audit_id ) ){
			$options  = ['method'=>'GET','auth_token'=>$this->auth_token];
			$postdata = $this->ssid_common->_prepare_curl_post_data( ['account_id'=>$this->account_id,'audit_id'=>$audit_id] );
			$audit_details = $this->ssid_common->doCurl( $this->api_end_point.'audit/audits', $postdata, $options );
			$audit_data = ( isset($audit_details->audits) ) ? $audit_details->audits : null;
			
			if( !empty( $audit_data ) ){
			
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
					'document_content'	=> $audit_data ,
					'generic_details'	=>[
						'document_name'		=> $audit_data->audit_type,
						'document_date'		=> date('l, jS F Y'),
						'referrence_number'	=> ''
					],
					'data_details'		=> $audit_data,
				];

				$this->ssid_common->create_pdf( 'pdf-templates/custom/audits_pdf_template', $setup, true  );

			}
			
		}
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
			$message	= ( isset($audit_result->message) ) ? $audit_result->message : 'Request completed!';
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
	
	/** Populate Modal **/
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
					$audit .= '<tr><th width="10%">ID</th><th width="40%">Audit Question</th><th width="30%">Response</th><th width="20%">Extra Info</th></tr>';
						foreach( $audit_record->audit_responses as $k=>$audit_item ){ $k++;
							$audit .= '<tr>';
								$audit .= '<td>'.$k.'.</td>';
								$audit .= '<td>'.$audit_item->question.'</td>';
								if( is_object( $audit_item->response ) ){
									$audit .= '<td><table width="100%">';
										foreach( $audit_item->response->list  as $zone => $resp ) {
											$audit .= '<tr><th width="30%">'.$zone.':</th><td>'.$resp.'<td><tr>';
										}
									$audit .= '</table></td>';
								}else{
									$audit .= '<td>'.$audit_item->response.'</td>';
								}								
								$audit .= '<td>'.$audit_item->response_extra.'</td>';
							$audit .= '</tr>';
						}
					$audit .= '</table></td></tr>';					
			$audit .= '</table>';
		}
		return $audit;
	}
}
