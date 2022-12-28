<?php if (!defined('BASEPATH'))exit('No direct script access allowed');

class Risk_Assessment_model extends CI_Model {

	function __construct(){
		parent::__construct();		
		$this->load->model('serviceapp/Document_Handler_model','document_service');	
		$this->audit_group = 'risk_assessment';
	}
	
	
	private $risk_searchable_fields = ['raqb.risk_text', 'raqb.risk_harm', 'raqb.persons_at_risk', 'raqb.risk_rating' ];
	/* 
	*	Get list of Risk items and search though the bank
	*/	
	public function get_risks( $account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){

		$result = false;

		if( !empty( $account_id ) ){

			$where = $raw_where = convert_to_array( $where );
			
			if( !empty( $where['ajax_req'] ) ){
				$this->db->select( 'raqb.risk_text `label`, raqb.risk_code `value`, raqb.risk_id, raqb.risk_code, raqb.risk_harm, raqb.risk_rating, raqb.persons_at_risk, raqb.control_measures', false );
				unset( $where['ajax_req'] );
			} else {
				$this->db->select( 'raqb.*, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false );
			}
			
			$this->db->join( 'user creater', 'creater.id = raqb.created_by', 'left' )
				->join( 'user modifier', 'modifier.id = raqb.last_modified_by', 'left' )
				->where( 'raqb.is_active', 1 )
				->where( 'raqb.account_id', $account_id );

			if( isset( $where['risk_id'] ) || isset( $where['risk_code'] ) ){
				
				$where_condition = ( !empty( $where['risk_id'] ) ) ? ['raqb.risk_id'=>$where['risk_id']] : ( !empty( $where['risk_code'] ) ? ['raqb.risk_code'=>$where['risk_code']] : false );
				
				if( !empty( $where_condition ) ){
					$row = $this->db->where( 'raqb.account_id', $account_id )
						->get_where( 'risk_assessment_question_bank raqb', $where_condition )
						->row();

					if( !empty( $row ) ){
						$result = $row;
						$this->session->set_flashdata( 'message','Risk details data found' );
						return $result;
					} else {
						$this->session->set_flashdata( 'message','No data found' );
						return false;
					}
				}
			}

			if( !empty( $search_term ) ){
				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->risk_searchable_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}
						
						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				}else{
					foreach( $this->risk_searchable_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}
					
					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}
			
			if( !empty( $where ) ){
				
				if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}
			
			if( !empty( $order_by ) ){
				$this->db->order_by( $order_by );
			}else{
				$this->db->order_by( 'raqb.risk_text, raqb.risk_id DESC' );
			}
			
			if( $limit > 0 ){
				$this->db->limit( $limit, $offset );
			}
			
			$query = $this->db->get( 'risk_assessment_question_bank raqb' );

			if( $query->num_rows() > 0 ){				
				$result_data = $query->result();

				$result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
				$result->records 			= $result_data;
				$counters 					= $this->risk_items_totals( $account_id, $search_term, $raw_where, $limit );
				$result->counters->total 	= ( !empty( $counters->total ) ) ? $counters->total : null;
				$result->counters->pages 	= ( !empty( $counters->pages ) ) ? $counters->pages : null;
				$result->counters->limit  	= ( !empty( $apply_limit ) ) ? $limit : $result->counters->total;
				$result->counters->offset 	= $offset;
				
				$this->session->set_flashdata( 'message','Risk Items data found' );
			} else {
				$this->session->set_flashdata( 'message','No data found' );
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}
		
		return $result;
	}
	
	/** Get Risk item counters **/
	public function risk_items_totals( $account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT ){
		$result = false;
		if( !empty( $account_id ) ){

			if( !empty( $where['ajax_req'] ) ){
				unset( $where['ajax_req'] );
			}
			
			$this->db->select( 'raqb.risk_id', false )
			->join( 'user creater', 'creater.id = raqb.created_by', 'left' )
			->join( 'user modifier', 'modifier.id = raqb.last_modified_by', 'left' )
			->where( 'raqb.is_active', 1 )
			->where( 'raqb.account_id', $account_id );

			if( !empty( $search_term ) ){
				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->risk_searchable_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}
						
						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				}else{
					foreach( $this->risk_searchable_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}
					
					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}
			
			if( !empty( $where ) ){
				
				if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}

			$query 			  = $this->db->from( 'risk_assessment_question_bank raqb' )->count_all_results();
			$results['total'] = !empty( $query ) ? $query : 0;
			$limit 			  = ( !empty( $apply_limit ) ) ? $limit : $results['total'];
			$results['pages'] = !empty( $query ) ? ceil( $query / $limit ) : 0;
			return json_decode( json_encode( $results ) );
		}
		return $result;
	}
	
	/** Get Risk Assessment(s) **/
	public function get_ra_records( $account_id = false, $assessment_id = false, $job_id = false, $inc_responses = false, $limit=20, $offset=0 ){
		$result = false;

		if( !empty( $job_id ) ){
			$job_details 	= $this->db->select( 'job_type_id', false )->limit( 1 )->get_where( 'job', ['account_id'=>$account_id, 'job_id'=>$job_id] )->row();
			if( !empty( $job_details ) ){
				$expected_risks 	= $this->job_service->get_associated_risks( $account_id, $job_details->job_type_id, true );
				$ra_expected_risks	= ( !empty( $expected_risks ) ) ? count( $expected_risks ) : 0;
			}
			$this->db->where( 'risk_assessment.job_id',$job_id ); 
		}

		$this->db->select('risk_assessment.*, concat(user.first_name," ",user.last_name) `created_by`,concat(modifier.first_name," ",modifier.last_name) `last_modified_by`',false)
			->join('user','user.id = risk_assessment.created_by','left')
			->join('user modifier','modifier.id = risk_assessment.last_modified_by','left')
			->where('risk_assessment.account_id',$account_id);
		
		if( $assessment_id ){
			$row = $this->db->get_where('risk_assessment',['risk_assessment.account_id'=>$account_id,'assessment_id'=>$assessment_id])->row();			
			if( !empty( $row ) ){
				#$uploaded_docs 			= $this->document_service->get_document_list( $account_id, $this->audit_group, ['assessment_id'=>$assessment_id] );
				#$row->uploaded_docs = ( !empty($uploaded_docs[$account_id]) ) ? $uploaded_docs[$account_id] : null;
				$row->ra_responses  	= $this->get_ra_responses( $assessment_id );
				$row->ra_expected_risks	= !empty( $ra_expected_risks ) ? $ra_expected_risks : 0;
				$this->session->set_flashdata('message','Risk assessment record found');
				$result = $row;
			}else{
				$this->session->set_flashdata('message','Risk assessment not found');
			}
			return $result;
		}

		$query = $this->db->order_by('assessment_id desc')
			->limit( $limit, $offset )
			->get('risk_assessment');

		if( $query->num_rows() > 0 ){
			
			if( $inc_responses ){
				foreach( $query->result() as $k=>$row ){
					$row->ra_responses 		= $this->get_ra_responses( $row->assessment_id );
					$row->ra_expected_risks	= !empty( $ra_expected_risks ) ? $ra_expected_risks : 0;
					$result[$k] = $row;
				}
			}else{
				#$result = $query->result();
				foreach( $query->result() as $k=>$row ){
					$row->ra_responses 		= $this->get_ra_responses( $row->assessment_id );
					$row->ra_expected_risks	= !empty( $ra_expected_risks ) ? $ra_expected_risks : 0;
					$result[$k] = $row;
				}
			}

			$this->session->set_flashdata('message','Risk assessment records found');			
		}else{
			$this->session->set_flashdata('message','Risk assessment record(s) not found');
		}
		return $result;
	}
	
	/** Create a New Risk Assessment log **/
	public function create_ra( $account_id = false, $postdata = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $postdata ) ){
			$job_id 	= $postdata['job_id'];
			$responses 	= $postdata['responses'];
			unset( $postdata['responses'] );
			$assessment_id = ( !empty( $postdata['assessment_id'] ) ) ? $postdata['assessment_id'] : null;
			$assessment_id = $this->_update_risk_assessment( $account_id, $assessment_id, $postdata );
			if( !empty( $assessment_id ) && !empty( $responses ) ){
				$save_responses = $this->_save_ra_responses( $account_id, $assessment_id, $postdata, $responses );
			}
			
			## Upload any files if they exist
			if(  !empty( $_FILES['user_files']['name'] ) ) {
				$postdata['assessment_id']  = $assessment_id;
				$uploaded_docs = $this->document_service->upload_files( $account_id, $postdata, $this->audit_group );					
			}
			
			## Check completion status
			$status = $this->check_ra_status( $account_id, $job_id, $assessment_id );
			
			## Do a Quick qpdate to the RA record 
			if( !empty( $status ) ){
				$quick_data = [];					
				if( ( strtolower( $status->status ) == 'completed' ) || ( $status->completed_risks >= $status->expected_risks ) ){
					$quick_data['risks_completed'] 	= 1;
					$quick_data['status'] 			= 'Completed';
					$quick_data['finish_time'] 		= date( 'Y-m-d H:i:s' );
				}
				
				if( !empty( $quick_data ) ){
					$this->quick_ra_update( $account_id, $assessment_id, $quick_data );
				}
			}
			
			## Get created record
			$result = $this->get_ra_records( $account_id, $assessment_id );

			if( !empty( $result ) ){
				$this->session->set_flashdata('message','Risk assessment details saved successfully');
			}else{
				$this->session->set_flashdata('message','An error occurred while updating your Risk assessment record');
			}
			
		}
		return $result;
	}
	
	/** Create risk assesment main record **/
	public function _update_risk_assessment( $account_id = false, $assessment_id = false, $data = false ){

		$result = false;
		if( !empty( $account_id ) && !empty( $data ) ){
			$assessment_id = ( !empty( $assessment_id ) ) ? $assessment_id : ( !empty( $data['assessment_id'] ) ? $data['assessment_id'] : null );

			$ra_data = $this->ssid_common->_filter_data( 'risk_assessment', $data );
			if( $assessment_id ){

				#Check if thie RA record actually exists
				$check_exists = $this->db->get_where( 'risk_assessment', ['account_id'=>$account_id, 'assessment_id'=>$assessment_id] )->row();
				if( !empty( $check_exists ) ){
					$update_ra = $ra_data;
					$update_ra['last_modified_by'] 	  = $this->ion_auth->_current_user->id;
					$update_ra['finish_gps_latitude'] = !empty( $data['finish_gps_latitude'] ) ? $data['finish_gps_latitude'] : null;
					$update_ra['finish_gps_longitude']= !empty( $data['finish_gps_longitude'] ) ? $data['finish_gps_longitude'] : null;
					
					#Update ra record
					$this->db->where( 'account_id', $account_id )
						->where( 'assessment_id', $assessment_id )
						->update( 'risk_assessment', $update_ra );
					
					$result = ( $this->db->trans_status() ) ? $check_exists->assessment_id : false;
				}

			}else{

				#Check if this RA record actually exists
				$check_exists = $this->db
					->order_by( 'assessment_id desc' )
					->limit( 1 )
					->like( 'date_created', date('Y-m-d'), 'after' )
					->where( 'status !=', 'Completed' )
					->where( ['account_id'=>$account_id, 'job_id'=>$data['job_id']] )
					->get( 'risk_assessment' )->row();

				if( !empty( $check_exists ) ){
					$update_ra = $ra_data;
					$update_ra['last_modified_by'] = $this->ion_auth->_current_user->id;
					$this->db->where( 'account_id', $account_id )
						->where( 'assessment_id', $check_exists->assessment_id )
						->update( 'risk_assessment', $update_ra );
					
					$result = ( $this->db->trans_status() ) ? $check_exists->assessment_id : false;
				}else{
					$ra_data['status'] 		 = 'In progress';
					$ra_data['start_time'] 	 = date( 'Y-m-d H:i:s' );				
					$ra_data['created_by'] 	 = $this->ion_auth->_current_user->id;				
					$ra_data['gps_latitude'] = !empty( $data['gps_latitude'] ) ? $data['gps_latitude'] : null;
					$ra_data['gps_longitude']= !empty( $data['gps_longitude'] ) ? $data['gps_longitude'] : null;

					$this->db->insert( 'risk_assessment', $ra_data );
					$result = ( $this->db->trans_status() ) ? $this->db->insert_id() : false;
				}
			}
		}
		return $result;
	}
	
	/* Process risk assesment responses */
	private function _save_ra_responses( $account_id = false, $assessment_id = false, $data = false, $responses = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $assessment_id ) && !empty( $data ) ){
			if( !empty( $responses ) ){
				$resp_data = [];
				foreach( $responses as $k=>$row ){
					$risk_record = $this->db->limit(1)->get_where( 'risk_assessment_question_bank', ['account_id'=>$account_id, 'risk_id'=>$row['risk_id'] ] )->row();
					if( !empty( $risk_record ) ){
						$row = array_merge( $row, (array)$risk_record );
						$new_row 			 	 = $this->ssid_common->_filter_data( 'risk_assessment_responses', $row );
						$new_row['created_by']	 = $this->ion_auth->_current_user->id;
						$new_row['assessment_id']= $assessment_id;
						$resp_data[$k] 		 	 = $new_row;
						unset($new_row['date_created']);
					}
				}

				## Insert responses
				if( !empty( $resp_data ) ){
					$conditions = ['assessment_id'=>$assessment_id];
					$this->db->where_in( 'risk_id', array_column( $resp_data, 'risk_id' ) )
						->where( $conditions )->delete( 'risk_assessment_responses' );

					$this->ssid_common->_reset_auto_increment( 'risk_assessment_responses', 'id' );//House keeping, preserve auto-increment
					
					$this->db->insert_batch( 'risk_assessment_responses', $resp_data );						
				}
				$result = ( $this->db->trans_status() !== false ) ? true : false;
			}
		}
		return $result;
	}
	
	/*
	* Get list of Risk Assessment Questions for a specific RA type
	*/
	public function get_ra_questions( $account_id = false, $ra_segment = false, $segmented = false ){
		$result = false;
		if( !empty($account_id) ){
			$this->db->select('raqb.*',false)
				->where('raqb.account_id',$account_id);

			if( $ra_segment ){
				$this->db->where('raqb.risk_segment',$ra_segment);
			}
				
			$query = $this->db->where('raqb.is_active',1)
				->get('risk_assessment_question_bank raqb');

			if( $query->num_rows() > 0 ){
				foreach( $query->result() as $row ){
					if( $segmented ){
						$result[$row->risk_segment][] = $row->risk_id;
					}else{
						$row->response_options = json_decode($row->response_options);
						$result[$row->risk_section][] = $row;
					}
				}
				$this->session->set_flashdata('message','Risk Assessment questions found');
			}else{
				$this->session->set_flashdata('message','No records found');
			}
		}			
		return $result;
	}

	/*
	* Get list of RA responses
	*/
	public function get_ra_responses( $assessment_id = false, $job_id = false, $segmented = false, $result_as_array = false ){
		$result = null;
		if( !empty( $assessment_id ) || !empty( $job_id ) ){
			
			$this->db->select('rar.*',false)
				->join( 'risk_assessment', 'risk_assessment.assessment_id = rar.assessment_id', 'left' );

			if( $assessment_id ){
				$this->db->where( 'rar.assessment_id',$assessment_id );
			}
		
			if( $job_id ){
				$this->db->where('risk_assessment.job_id',$job_id);
			}
			
			$query = $this->db->order_by('rar.risk_segment, rar.risk_section, rar.risk_id')
				->get('risk_assessment_responses rar');

			if( $query->num_rows() > 0 ){
				
				if( $segmented ){
					foreach( $query->result() as $row ){
						$result[$row->risk_segment][] = $row->id;
					}
				}else{
					if( !empty( $result_as_array ) ){
						$result = $query->result_array();
					} else {
						$result = $query->result();
					}
				}				
				$this->session->set_flashdata('message','Risk assessment responses found');
			}else{
				$this->session->set_flashdata('message','No records found');
			}
		}			
		return $result;
	}
	
	/** Check RA completion status **/
	public function check_ra_status( $account_id = false, $job_id = false, $assessment_id = false){
		$result = false;
		if( !empty( $account_id ) && ( !empty( $job_id ) || !empty( $assessment_id ) ) ){
			$this->db->select( 'job_id, assessment_id, status', false )
				->where( 'account_id', $account_id );
				
			if( $job_id ){
				$this->db->where( 'job_id', $job_id );
			}
			
			if( $assessment_id ){
				$this->db->where( 'assessment_id', $assessment_id );
			}
			
			$query = $this->db->get( 'risk_assessment' );
			if( $query->num_rows() > 0 ){
				$result = $query->result()[0];
			}else{
				$result = (object)[
					'job_id'=>( string ) $job_id,
					'assessment_id'=>( string ) $assessment_id,
					'status'=>null
				];
			}
			
			$result->expected_risks  = 0;
			$result->completed_risks = 0;
			
			# Get expected list of Risks
			$job_id 		= ( !empty( $job_id ) ) ? $job_id : $result->job_id;
			$assessment_id 	= ( !empty( $assessment_id ) ) ? $assessment_id : $result->assessment_id;
			$job_details 	= $this->db->select( 'job_type_id', false )->limit( 1 )->get_where( 'job', ['account_id'=>$account_id, 'job_id'=>$job_id] )->row();
			
			if( !empty( $job_details ) ){
				$assoc_risks 			= $this->job_service->get_associated_risks( $account_id, $job_details->job_type_id, [ 'job_id'=>$job_id ] );
				$expected_risks 		= ( !empty( $assoc_risks ) ) ? count( $assoc_risks ) : 0;
				$result->expected_risks = ( string ) $expected_risks;
			}

			# Check how many have been completed
			$completed_risks 		= $this->get_ra_responses( $assessment_id, false, false, true );
			$result->completed_risks= ( string ) ( ( !empty( $completed_risks ) ) ? count( $completed_risks ) : 0 );
			$result->pending_risks	= ( string ) ( abs( $result->expected_risks - $result->completed_risks ) );
			
			if( $result->completed_risks >= $result->expected_risks ){
				$result->status = 'Completed';
			} else {
				$result->status = 'In progress';
			}
			$this->session->set_flashdata( 'message','Risk assessment status retrieved' );
		}
		return $result;
	}

	/** Quick udpdate to the risk assessment record **/
	public function quick_ra_update( $account_id, $assessment_id, $data ){
		$result = false;
		if( $account_id && $assessment_id && $data ){
			
			$data = $this->ssid_common->_filter_data( 'risk_assessment', $data );
			$data['last_modified_by'] = $this->ion_auth->_current_user->id;
			$this->db->where('account_id',$account_id)
				->where('assessment_id',$assessment_id)
				->update('risk_assessment', $data);
				
			if( $this->db->trans_status() !== false ){
				$result = true;				
			}
		}
		return $result;
	}
	
	/** Get details of all Job Types to which a Risk is associated **/
	public function get_associated_job_types( $account_id = false, $risk_id = false ){
		
		$result = false;
		
		if( !empty( $risk_id ) ){			
			$query = $this->db->select( 'jar.*, job_types.job_type, job_types.job_type_desc, job_types.ra_required' )
				->join( 'job_types', 'job_types.job_type_id = jar.job_type_id', 'left' )
				->where( 'jar.risk_id', $risk_id )
				->order_by( 'job_types.job_type' )
				->get( 'job_associated_risks jar' );
				
			if( $query->num_rows() > 0 ){
				$result = $query->result();
				$this->session->set_flashdata('message','Associated Job types found');
			} else {
				$this->session->set_flashdata('message','No associated Job types found');
			}
		} else {
			$this->session->set_flashdata('message','You request is missing required information');
		}
		return $result;		
	}


	/*
	* Create new Risk item
	*/
	public function create_risk_item( $account_id = false, $risk_item_data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $risk_item_data ) ){
			$data = $this->ssid_common->_data_prepare( $risk_item_data );
			if( !empty( $data ) ){
				$new_risk_item 				= $this->ssid_common->_filter_data( 'risk_assessment_question_bank', $data );
				$new_risk_item['created_by']	= $this->ion_auth->_current_user->id;
				$this->db->insert( 'risk_assessment_question_bank', $new_risk_item );
				if( $this->db->trans_status() !== FALSE ){
					$data['risk_id'] = $this->db->insert_id();
					$result = $this->get_risks( $account_id, false, [ 'risk_id'=>$data['risk_id'] ] );
					$result = $this->db->get_where( 'risk_assessment_question_bank', [ 'account_id' => $account_id, 'risk_id' => $data['risk_id'] ] )->row();
					$this->session->set_flashdata('message','Risk Item created successfully.');
				}
			} else {
				$this->session->set_flashdata('message','Error parsing your supplied data. Request aborted');
			}
		} else {
			$this->session->set_flashdata('message','Your request is missing required information.');
		}
		return $result;
	}

	
	/** Update an existing Risk Item record **/
	public function update_risk_item( $account_id = false, $risk_id = false, $update_data = false  ){
		$result = false;
		if( !empty( $account_id ) && !empty( $risk_id )  && !empty( $update_data ) ){

			$ref_condition = [ 'account_id'=>$account_id, 'risk_id'=>$risk_id ];
			$update_data   = $this->ssid_common->_data_prepare( $update_data );
			$update_data   = $this->ssid_common->_filter_data( 'risk_assessment_question_bank', $update_data );
			$record_pre_update = $this->db->get_where( 'risk_assessment_question_bank', [ 'account_id'=>$account_id, 'risk_id'=>$risk_id ] )->row();

			if( !empty( $record_pre_update ) ){

				$check_conflict = $this->db->select( 'risk_id', false )
					->where( 'risk_assessment_question_bank.risk_text', $update_data['risk_text'] )
					->where( 'risk_assessment_question_bank.risk_code', $update_data['risk_code'] )
					->where( 'risk_assessment_question_bank.account_id', $account_id )
					->where( 'risk_assessment_question_bank.risk_id !=', $risk_id )
					->where( 'risk_assessment_question_bank.is_active', 1 )
					->limit( 1 )
					->get( 'risk_assessment_question_bank' )
					->row();

				if( !$check_conflict ){

					$update_data['last_modified_by'] = $this->ion_auth->_current_user->id;
					$this->db->where( $ref_condition )
						->update( 'risk_assessment_question_bank', $update_data );

					$updated_record = $this->get_risks( $account_id, false, [ 'risk_id'=>$risk_id ] );
					$result 		= ( !empty( $updated_record->records ) ) ? $updated_record->records : ( !empty( $updated_record ) ? $updated_record : false );

					$this->session->set_flashdata( 'message', 'Risk Item updated successfully' );
					return $result;
				} else {
					$this->session->set_flashdata( 'message', 'Item with same name already Exists! Update request aborted' );
					return false;
				}

			} else {
				$this->session->set_flashdata( 'message', 'This Risk Item record does not exist or does not belong to you.' );
				return false;
			}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information.' );
		}
		return $result;
	}
	
	/*
	* Delete Risk Item record
	*/
	public function delete_risk_item( $account_id = false, $risk_id = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $risk_id ) ){
			$conditions 		= ['account_id'=>$account_id,'risk_id'=>$risk_id];
			$risk_item_exists 	= $this->db->get_where( 'risk_assessment_question_bank',$conditions )->row();
			if( !empty( $risk_item_exists ) ){
				
				$this->db->where( 'risk_id', $risk_id )->delete( 'job_associated_risks' );
				$this->ssid_common->_reset_auto_increment( 'job_associated_risks', 'associate_id' );
				
				$this->db->where( 'risk_id', $risk_id )->delete( 'job_dynamic_risks' );
				$this->ssid_common->_reset_auto_increment( 'job_dynamic_risks', 'dynamic_risk_id' );

				$this->db->where( $conditions )
					->update( 'risk_assessment_question_bank', ['is_active'=>0, 'risk_code'=>strtoupper( $risk_item_exists->risk_code.'_ARC' ) ] );
					
				if( $this->db->trans_status() !== FALSE ){
					$this->session->set_flashdata('message','Record deleted successfully.');
					$result = true;
				}
			}else{
				$this->session->set_flashdata('message','Invalid Risk Item ID');
			}

		}else{
			$this->session->set_flashdata('message','No Risk item record found.');
		}
		return $result;
	}
}