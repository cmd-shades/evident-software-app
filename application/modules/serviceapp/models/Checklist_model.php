<?php

namespace Application\Modules\Service\Models;

class Checklist_model extends CI_Model {

	function __construct(){
		$this->load->model( 'serviceapp/Site_model','site_service');
		$this->load->model( 'serviceapp/Customer_model','customer_service');
    }

	//Searchable fields
	private $searchable_fields  				= [ 'job.job_id', 'job.status_id', 'job.job_type_id', 'job.assigned_to', 'addrs.postcode_nospaces','addrs.postcode', 'customer_addresses.address_postcode', 'job.job_tracking_id', 'job.external_job_ref', 'job.external_job_call_status' ];
	private $job_types_search_fields			= [ 'job_types.job_type', 'job_types.job_type_desc' ];
	private $fail_codes_search_fields			= [ 'fail_code', 'fail_code_text', 'fail_code_desc', 'fail_code_group' ];
	private $schedule_search_fields				= [ 'schedule_name', 'frequency_group'];
	private $schedule_frequencies_search_fields	= [ 'frequency_name', 'frequency_ref', 'frequency_desc' ];
	private $job_tracking_statuses_search_fields= [ 'job_tracking_id', 'job_tracking_status', 'job_tracking_desc' ];
	private $activity_search_fields				= [ 'activity_name', 'status', 'job_type', 'job_status' ];
	private $minimal_searchable_fields  		= [ 'job.job_id', 'job.status_id', 'job.job_type_id', 'job.assigned_to', 'job.contract_id', 'job.region_id', 'job.address_id', 'job.job_tracking_id', 'job.external_job_ref', 'job.external_job_call_status' ];
	private $tesseract_linked_statuses 			= [ 'enroute', 'onsite', 'successful', 'onhold' ];
	private $checklist_searchable_fields  		= [ 'job.job_id', 'job_types.job_type', 'job.assigned_to', 'site.site_name', 'site.site_reference', 'user.first_name', 'user.last_name', 'job_statuses.job_status', 'site.postcodes', 'site.site_reference' ];

	/** Check Jobs Access **/
	private function _check_jobs_access( $user = false ){
		$result = false;
		if( !empty( $user ) ){
			if( !empty( $user->is_primary_user ) ){
				$result = false;
			} else if( !$user->is_primary_user && !empty( $user->associated_user_id ) ){
				$result = [$user->associated_user_id, $user->id];
			} else if( !$user->is_primary_user && !$user->associated_user_id ){

				if( in_array( $user->user_type_id, EXTERNAL_USER_TYPES ) ){
					$contract_access = $this->contract_service->get_linked_people( $user->account_id, false, $user->id, ['as_arraay'=>1] );
					$lead_assignees  = !empty( $contract_access ) ? array_filter( array_column( $contract_access, 'contract_lead_id' ) ) : [];
					if( !empty( $lead_assignees ) ){
						#$result = $lead_assignees;
						$result = [];
					} else {
						$result = [];
					}
				} else {
					$result = [];
				}

			} else {
				$result = [];
			}
		}
		return $result;
	}


	/*
	* Search through Jobs
	*/
	public function checklist_job_search( $account_id = false, $job_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;
		if( !empty( $account_id ) ){

			$where = $raw_where 	= ( !empty( $where ) ) ? convert_to_array( $where ) : false;
			$assignees 	= $this->_check_jobs_access( $this->ion_auth->_current_user() );

			#Limit access by contract to External User Types
			if( in_array( $this->ion_auth->_current_user()->user_type_id, EXTERNAL_USER_TYPES ) ){
				if( !empty( $this->ion_auth->_current_user()->is_primary_user ) ){

					## Get associated users
					if( !$job_id  ){
						$group_assignees = $this->ion_auth->get_associated_users( $account_id, $this->ion_auth->_current_user()->id, false, ['as_arraay'=>1] );
						if( !empty( $group_assignees ) ){
							$group_assignees = ( !empty( $group_assignees ) ) ? array_column( $group_assignees, 'user_id' ) : [$this->ion_auth->_current_user()->id];
							$group_assignees = ( !in_array( $this->ion_auth->_current_user()->id, $group_assignees ) ) ? array_merge( $group_assignees , [$this->ion_auth->_current_user()->id] ) : $group_assignees;
							$raw_where['group_assignees']	= $group_assignees;
							$this->db->where_in( 'job.assigned_to', $group_assignees );
						} else {
							$contract_access = $this->contract_service->get_linked_people( $account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1] );
							$allowed_access  = !empty( $contract_access ) ? array_column( $contract_access, 'contract_id' ) : [];
							if( !empty( $allowed_access ) ){
								$this->db->where_in( 'job_types.contract_id', $allowed_access );
							} else{
								$this->session->set_flashdata( 'message','No data found matching your criteria' );
								return false;
							}
						}
					}
				} else {
					$contract_access = $this->contract_service->get_linked_people( $account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1] );
					$allowed_access  = !empty( $contract_access ) ? array_column( $contract_access, 'contract_id' ) : [];
					if( !empty( $allowed_access ) ){
						$this->db->where_in( 'job_types.contract_id', $allowed_access );
					} else{
						$this->session->set_flashdata( 'message','No data found matching your criteria' );
						return false;
					}
				}
			}

			$raw_where['assignees']	= $assignees;

				$this->db->select( 'job.*, job_types.job_type, job_statuses.job_status, CONCAT(user.first_name," ",user.last_name) `assignee`, site.site_name, site.site_postcodes, site.site_reference,
					CASE WHEN job_statuses.job_status IN ( "Assigned" ) THEN "Not Started"
					WHEN job_statuses.job_status IN ( "In Progress", "On Hold", "En Route", "On Site" ) THEN "In Progress"
					WHEN job_statuses.job_status IN ( "Failed", "Cancelled" ) THEN "Cancelled"
					WHEN job_statuses.job_status IN ( "Successful" ) THEN "Completed" ELSE job_statuses.job_status END AS `checklist_status`', false )
				->join( 'job_types', 'job_types.job_type_id = job.job_type_id', 'left' )
				->join( 'site', 'site.site_id = job.site_id', 'left' )
				->join( 'tesseract_checklist_response', 'tesseract_checklist_response.job_id = job.job_id', 'left' )
				->join( 'job_statuses','job_statuses.status_id = job.status_id','left' )
				->join( 'user','user.id = job.assigned_to','left' )
				->where( 'job.account_id',$account_id )
				#->where( 'job.external_job_created_on >= "2021-06-01 00:00:01" ' ) //From June 2021 onwards
				->where( 'job.archived !=', 1 )
				->where( '( site.external_site_ref IS NOT NULL AND site.external_site_ref != "" )' )
				->where_in( 'site.external_site_ref', [ 'HHG1425','HHG1581','HHG1582','HHG1583','HHG1592','HHG1593','HHG1594','HHG1595','HHG1596','HHG1597','HHG1601','HHG2989','HHG3180','HHG3204','HHG3221','HHG3222','HHG1601','HHG0871','HHG0872','HHG0878','HHG0879','HHG0881','HHG0881','HHG3019','HHG3181' ] );


			if( isset( $where['group_status'] ) ){
				$where['group_status'] = is_array( $where['group_status'] ) ? $where['group_status'] : [$where['group_status']];
				if( !empty( $where['group_status'] ) && !in_array( 'all', array_map( 'strtolower', $where['group_status'] ) ) ){
					$group_status_id = [];
					foreach( $where['group_status'] as $k => $grp_status ){
						switch( $grp_status ){
							case 'in_progress':
								$group_status_id = array_merge( $group_status_id, [3,7,8,9] );
								break;
							case 'cancelled':
								$group_status_id = array_merge( $group_status_id, [5,6] );
								break;
							case 'completed':
								$group_status_id = array_merge( $group_status_id, [4] );
								break;
							case 'not_started':
							default:
								$group_status_id = array_merge( $group_status_id, [1,2] );
								break;
						}
					}
					$this->db->where_in( 'job.status_id', $group_status_id );
				}
				unset( $where['group_status'] );
			}

			if( !empty( $search_term ) ){

				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->minimal_searchable_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						if( !empty( $search_where['job.contract_id'] ) ){
							$search_where['contract.contract_name'] 		 =  trim( $term );
							unset( $search_where['job.contract_id'] );
						}
						
						if( !empty( $search_where['job.region_id'] ) ){
							$search_where['diary_regions.region_name'] 		 =  trim( $term );
							unset( $search_where['job.region_id'] );
						}

						if( !empty( $search_where['job.address_id'] ) ){
							unset( $search_where['job.address_id'] );
						}

						if( !empty( $search_where['job.status_id'] ) ){
							$search_where['job_statuses.job_status'] =  trim( $term );
							unset($search_where['job.status_id']);
						}

						if( !empty( $search_where['job.job_type_id'] ) ){
							$search_where['job_types.job_type'] =  trim( $term );
							unset($search_where['job.job_type_id']);
						}

						if( !empty($search_where['job.assigned_to']) ){
							$search_where['user.first_name'] =  trim( $term );
							$search_where['user.last_name'] =  trim( $term );
							unset($search_where['job.assigned_to']);
						}

						if( !empty($search_where['job.job_date']) ){
							$job_date = date( 'Y-m-d', strtotime( $term ) );
							if( valid_date( $job_date ) ){
								$search_where['job.job_date'] =  $job_date;
							}
							unset($search_where['job.job_date']);
						}

						if( !empty( $search_where['job.job_tracking_id'] ) ){
							$search_where['job_tracking_statuses.job_tracking_status'] =  trim( $term );
							unset($search_where['job.job_tracking_id']);
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}

				} else {
					foreach( $this->minimal_searchable_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					if( !empty( $search_where['job.contract_id'] ) ){
						$search_where['contract.contract_name'] 		 =  trim( $search_term );
						unset( $search_where['job.contract_id'] );
					}
					
					if( !empty( $search_where['job.region_id'] ) ){
						$search_where['diary_regions.region_name'] 		 =  trim( $search_term );
						unset( $search_where['job.region_id'] );
					}

					if( !empty( $search_where['job.address_id'] ) ){
						unset( $search_where['job.address_id'] );
					}

					if( !empty( $search_where['job.status_id'] ) ){
						$search_where['job_statuses.job_status'] =  trim( $search_term );
						unset($search_where['job.status_id']);
					}

					if( !empty($search_where['job.job_type_id']) ){
						$search_where['job_types.job_type'] =  trim( $search_term );
						unset($search_where['job.job_type_id']);
					}

					if( !empty($search_where['job.assigned_to']) ){
						$search_where['user.first_name'] =  trim( $search_term );
						$search_where['user.last_name'] =  trim( $search_term );
						unset($search_where['job.assigned_to']);
					}

					if( !empty($search_where['job.job_date']) ){
						$job_date = date( 'Y-m-d', strtotime( $search_term ) );
						if( valid_date( $job_date ) ){
							$search_where['job.job_date'] =  $job_date;
						}
						unset($search_where['job.job_date']);
					}

					if( !empty( $search_where['job.job_tracking_id'] ) ){
						$search_where['job_tracking_statuses.job_tracking_status'] =  trim( $search_term );
						unset($search_where['job.job_tracking_id']);
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( isset( $where['contract_id'] ) ){
				if( !empty( $where['contract_id'] ) ){
					$contract_id = $where['contract_id'];
					$contract_id = convert_to_array( $where['contract_id'] );
					$contract_id = is_array( $contract_id ) ? $contract_id : [ $contract_id ];
					$this->db->where_in('job_types.contract_id', $contract_id );
				}
				unset( $where['contract_id'] );
			}
			
			if( isset( $where['region_id'] ) ){
				if( !empty( $where['region_id'] ) ){
					$region_id = $where['region_id'];
					$region_id = convert_to_array( $where['region_id'] );
					$region_id = is_array( $region_id ) ? $region_id : [ $region_id ];
					$this->db->where_in('job.region_id', $region_id );
				}
				unset( $where['region_id'] );
			}
			
			if( isset( $where['job_type_id'] ) ){
				if( !empty( $where['job_type_id'] ) ){
					$job_types = ( !is_array( $where['job_type_id'] ) && ( (int) $where['job_type_id'] > 0 ) ) ? [ $where['job_type_id'] ] : ( ( is_array( $where['job_type_id'] ) ) ? $where['job_type_id'] : ( is_object( $where['job_type_id'] ) ? object_to_array( $where['job_type_id'] ) : [] ) );
					$this->db->where_in('job.job_type_id', $job_types );
				}
				unset( $where['job_type_id'] );
			}

				if( isset( $where['status_id'] ) ){
				if( !empty( $where['status_id'] ) ){
					$status_id = $where['status_id'];
					$status_id = convert_to_array( $where['status_id'] );
					$status_id = is_array( $status_id ) ? $status_id : [ $status_id ];
					$this->db->where_in('job.status_id', $status_id );
				}
				unset( $where['status_id'] );
			}
			
			if( isset( $where['job_type_id'] ) ){
				if( !empty( $where['job_type_id'] ) ){
					$job_types = ( !is_array( $where['job_type_id'] ) && ( (int) $where['job_type_id'] > 0 ) ) ? [ $where['job_type_id'] ] : ( ( is_array( $where['job_type_id'] ) ) ? $where['job_type_id'] : ( is_object( $where['job_type_id'] ) ? object_to_array( $where['job_type_id'] ) : [] ) );
					$this->db->where_in('job.job_type_id', $job_types );
				}
				unset( $where['job_type_id'] );
			}

			if( isset( $where['job_date_start'] ) || isset( $where['job_date_end'] ) ){
				if( !empty( $where['job_date_start'] ) ){
					$this->db->where( 'job.job_date >=', format_date_db( $where['job_date_start'] ) );
				}
				unset( $where['job_date_start'] );

				if( !empty( $where['job_date_end'] ) ){
					$this->db->where( 'job.job_date <=', format_date_db( $where['job_date_end'] ) );
					unset( $where['job_date_end'] );
				}
				unset( $where['job_date_end'] );
			}

			if( isset( $where['created_on_start'] ) || isset( $where['created_on_end'] ) ){
				if( !empty( $where['created_on_start'] ) ){
					$this->db->where( 'job.created_on >=', format_date_db( $where['created_on_start'] ).' 00:00:00' );
				}
				unset( $where['created_on_start'] );

				if( !empty( $where['created_on_end'] ) ){
					$this->db->where( 'job.created_on <=', format_date_db( $where['created_on_end'] ).' 23:59:59' );
				}
				unset( $where['created_on_end'] );
			}

			if( isset( $where['job_date'] ) ){
				if( !empty( $where['job_date'] ) ){
					$sjob_date = date( 'Y-m-d', strtotime( $where['job_date'] ) );
					$this->db->where( 'job.job_date', $sjob_date );
					unset( $where['job_date'] );
				}
			} else {
				if( isset( $where['date_from'] ) || isset( $where['date_to'] ) ){

					if( !empty( $where['date_from'] ) ){
						$this->db->where( 'job.job_date >=', date( 'Y-m-d', strtotime( format_date_db( $where['date_from'] ) ) ) );
					}

					if( !empty( $where['date_to'] ) ){
						$this->db->where( 'job.job_date <=', date( 'Y-m-d', strtotime( format_date_db( $where['date_to'] ) ) ) );
					}
					unset( $where['date_from'], $where['date_to'] );
				}
			}

			## Combined assignees
			if( !empty( $assignees ) ){
				if( !empty( $where['assigned_to'] ) ){
					$assignees[] 		= $where['assigned_to'];
				}
				$this->db->where_in( 'job.assigned_to', $assignees );
			} else {
				if( isset( $where['assigned_to'] ) ){
					if( !empty( $where['assigned_to'] ) ){
						$assigned_to = $where['assigned_to'];
						$assigned_to = convert_to_array( $where['assigned_to'] );
						$assigned_to = is_array( $assigned_to ) ? $assigned_to : [ $assigned_to ];
						$this->db->where_in('job.assigned_to', $assigned_to );

					}
					unset( $where['assigned_to'] );
				}
			}

			if( !empty( $where ) ){
				$this->db->where( $where );
			}

			if( $order_by ){
				$this->db->order_by( $order_by );
			}else{
				$this->db->order_by( 'job.job_id desc, job.job_date desc' );
			}

			if( $limit > 0 ){
				$this->db->limit( $limit, $offset );
			}

			$query = $this->db->group_by( 'job.job_id' )->get( 'job' );

			if( $query->num_rows() > 0 ){
				$data 						= [];
				$result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
				foreach( $query->result() as $k => $row ){
					$address_postcode = $this->db->select( 'postcode', false )
						->get_where( 'addresses', [ 'main_address_id'=>$row->address_id ] )
						->row();
					$row->postcode	= !empty( $address_postcode->postcode ) ? $address_postcode->postcode : null;
					$data[$k] = $row;
				}
				
				$result->records 			= $data;
				$counters 					= $this->get_checklist_jobs_search_totals( $account_id, $search_term, $raw_where, $limit );
				$result->counters->total 	= ( !empty( $counters->total ) ) ? $counters->total : null;
				$result->counters->pages 	= ( !empty( $counters->pages ) ) ? $counters->pages : null;
				$result->counters->limit  	= ( int ) $limit;
				$result->counters->offset 	= ( int ) $offset;
				$this->session->set_flashdata( 'message','Records found.' );
			}else{
				$this->session->set_flashdata( 'message','No records found matching your criteria.' );
			}
		}

		return $result;
	}

	/*
	* Get total site count for the search
	*/
	public function get_checklist_jobs_search_totals( $account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT ){

		$result = false;

		if( !empty( $account_id ) ){
			
			$where 		= $raw_where = ( !empty( $where ) ) ? convert_to_array( $where ) : false;

			#Limit access by contract to External User Types
			if( in_array( $this->ion_auth->_current_user()->user_type_id, EXTERNAL_USER_TYPES ) ){
				if( !empty( $this->ion_auth->_current_user()->is_primary_user ) ){

					$group_assignees = !empty( $where['group_assignees'] ) ? $where['group_assignees'] : false;
					if( $group_assignees ){
						$this->db->where_in( 'job.assigned_to', $group_assignees );
					} else {

						$group_assignees = $this->ion_auth->get_associated_users( $account_id, $this->ion_auth->_current_user()->id, false, ['as_arraay'=>1] );
						if( !empty( $group_assignees ) ){
							$group_assignees = ( !empty( $group_assignees ) ) ? array_column( $group_assignees, 'user_id' ) : [$this->ion_auth->_current_user()->id];
							$group_assignees = ( !in_array( $this->ion_auth->_current_user()->id, $group_assignees ) ) ? array_merge( $group_assignees , [$this->ion_auth->_current_user()->id] ) : $group_assignees;
							$raw_where['group_assignees']	= $group_assignees;
							$this->db->where_in( 'job.assigned_to', $group_assignees );
						} else {
							$contract_access = $this->contract_service->get_linked_people( $account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1] );
							$allowed_access  = !empty( $contract_access ) ? array_column( $contract_access, 'contract_id' ) : [];
							if( !empty( $allowed_access ) ){
								$this->db->where_in( 'job_types.contract_id', $allowed_access );
							} else{
								$this->session->set_flashdata( 'message','No data found matching your criteria' );
								return false;
							}
						}

					}
					unset( $where['group_assignees'] );
				} else {
					$contract_access = $this->contract_service->get_linked_people( $account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1] );
					$allowed_access  = !empty( $contract_access ) ? array_column( $contract_access, 'contract_id' ) : [];
					if( !empty( $allowed_access ) ){
						$this->db->where_in( 'job_types.contract_id', $allowed_access );
					} else{
						$this->session->set_flashdata( 'message','No data found matching your criteria' );
						return false;
					}
				}
			}

			## Extract pre-assigned Assignees
			if( isset( $where['assignees'] ) ){
				$assignees 	= $where['assignees'];
				unset( $where['assignees'] );
			}

			$this->db->select( 'job.job_id', false )
				->join( 'job_types', 'job_types.job_type_id = job.job_type_id', 'left' )
				->join( 'site', 'site.site_id = job.site_id', 'left' )
				->join( 'tesseract_checklist_response', 'tesseract_checklist_response.job_id = job.job_id', 'left' )
				->join( 'job_statuses','job_statuses.status_id = job.status_id','left' )
				->join( 'user','user.id = job.assigned_to','left' )
				->where( 'job.account_id',$account_id )
				#->where( 'job.external_job_created_on >= "2021-06-01 00:00:01" ' ) //From June 2021 onwards
				->where( 'job.archived !=', 1 )
				->where( '( site.external_site_ref IS NOT NULL AND site.external_site_ref != "" )' )
				#->where_in( 'site.external_site_ref', [ 'HHG3204','HHG3019','HHG1581','HHG1582','HHG1583','HHG3181','HHG1592','HHG1593','HHG2989','HHG1594','HHG1595','HHG1596','HHG1597','HHG1601','HHG1601','HHG1425','HHG3180','HHG3221','HHG3222' ] );
				->where_in( 'site.external_site_ref', [ 'HHG1425','HHG1581','HHG1582','HHG1583','HHG1592','HHG1593','HHG1594','HHG1595','HHG1596','HHG1597','HHG1601','HHG2989','HHG3180','HHG3204','HHG3221','HHG3222','HHG1601','HHG0871','HHG0872','HHG0878','HHG0879','HHG0881','HHG0881','HHG3019','HHG3181' ] );

			if( !empty( $search_term ) ){

				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->minimal_searchable_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						if( !empty( $search_where['job.contract_id'] ) ){
							$search_where['contract.contract_name'] 		 =  trim( $term );
							unset( $search_where['job.contract_id'] );
						}
						
						if( !empty( $search_where['job.region_id'] ) ){
							$search_where['diary_regions.region_name'] 		 =  trim( $term );
							unset( $search_where['job.region_id'] );
						}

						if( !empty( $search_where['job.address_id'] ) ){
							// $search_where['addrs.postcode'] 		 =  trim( $term );
							// $search_where['addrs.postcode_nospaces'] =  trim( $term );
							unset( $search_where['job.address_id'] );
						}

						if( !empty( $search_where['job.status_id'] ) ){
							$search_where['job_statuses.job_status'] =  trim( $term );
							unset($search_where['job.status_id']);
						}

						if( !empty($search_where['job.job_type_id']) ){
							$search_where['job_types.job_type'] =  trim( $term );
							unset($search_where['job.job_type_id']);
						}

						if( !empty($search_where['job.assigned_to']) ){
							$search_where['user.first_name'] =  trim( $term );
							$search_where['user.last_name'] =  trim( $term );
							unset($search_where['job.assigned_to']);
						}

						if( !empty($search_where['job.job_date']) ){
							$job_date = date( 'Y-m-d', strtotime( $term ) );
							if( valid_date( $job_date ) ){
								$search_where['job.job_date'] =  $job_date;
							}
							unset($search_where['job.job_date']);
						}

						if( !empty( $search_where['job.job_tracking_id'] ) ){
							$search_where['job_tracking_statuses.job_tracking_status'] =  trim( $term );
							unset($search_where['job.job_tracking_id']);
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}

				}else{
					foreach( $this->minimal_searchable_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					if( !empty( $search_where['job.contract_id'] ) ){
						$search_where['contract.contract_name'] 		 =  trim( $search_term );
						unset( $search_where['job.contract_id'] );
					}
					
					if( !empty( $search_where['job.region_id'] ) ){
						$search_where['diary_regions.region_name'] 		 =  trim( $search_term );
						unset( $search_where['job.region_id'] );
					}

					if( !empty( $search_where['job.address_id'] ) ){
						unset( $search_where['job.address_id'] );
					}

					if( !empty( $search_where['job.status_id'] ) ){
						$search_where['job_statuses.job_status'] =  trim( $search_term );
						unset($search_where['job.status_id']);
					}

					if( !empty($search_where['job.job_type_id']) ){
						$search_where['job_types.job_type'] =  trim( $search_term );
						unset($search_where['job.job_type_id']);
					}

					if( !empty($search_where['job.assigned_to']) ){
						$search_where['user.first_name'] =  trim( $search_term );
						$search_where['user.last_name'] =  trim( $search_term );
						unset($search_where['job.assigned_to']);
					}

					if( !empty($search_where['job.job_date']) ){
						$job_date = date( 'Y-m-d', strtotime( $search_term ) );
						if( valid_date( $job_date ) ){
							$search_where['job.job_date'] =  $job_date;
						}
						unset($search_where['job.job_date']);
					}

					if( !empty( $search_where['job.job_tracking_id'] ) ){
						$search_where['job_tracking_statuses.job_tracking_status'] =  trim( $search_term );
						unset($search_where['job.job_tracking_id']);
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( isset( $where['group_status'] ) ){
				$where['group_status'] = is_array( $where['group_status'] ) ? $where['group_status'] : [$where['group_status']];
				if( !empty( $where['group_status'] ) && !in_array( 'all', array_map( 'strtolower', $where['group_status'] ) ) ){
					$group_status_id = [];
					foreach( $where['group_status'] as $k => $grp_status ){
						switch( $grp_status ){
							case 'in_progress':
								$group_status_id = array_merge( $group_status_id, [3,7,8,9] );
								break;
							case 'cancelled':
								$group_status_id = array_merge( $group_status_id, [5,6] );
								break;
							case 'completed':
								$group_status_id = array_merge( $group_status_id, [4] );
								break;
							case 'not_started':
							default:
								$group_status_id = array_merge( $group_status_id, [1,2] );
								break;
						}
					}
					$this->db->where_in( 'job.status_id', $group_status_id );
				}
				unset( $where['group_status'] );
			}

			if( isset( $where['status_id'] ) ){
				if( !empty( $where['status_id'] ) ){
					$status_id = $where['status_id'];
					$status_id = convert_to_array( $where['status_id'] );
					$status_id = is_array( $status_id ) ? $status_id : [ $status_id ];
					$this->db->where_in('job.status_id', $status_id );
				}
				unset( $where['status_id'] );
			}			
			
			if( isset( $where['contract_id'] ) ){
				if( !empty( $where['contract_id'] ) ){
					$contract_id = $where['contract_id'];
					$contract_id = convert_to_array( $where['contract_id'] );
					$contract_id = is_array( $contract_id ) ? $contract_id : [ $contract_id ];
					$this->db->where_in('job_types.contract_id', $contract_id );
				}
				unset( $where['contract_id'] );
			}
			
			if( isset( $where['region_id'] ) ){
				if( !empty( $where['region_id'] ) ){
					$region_id = $where['region_id'];
					$region_id = convert_to_array( $where['region_id'] );
					$region_id = is_array( $region_id ) ? $region_id : [ $region_id ];
					$this->db->where_in('job.region_id', $region_id );
				}
				unset( $where['region_id'] );
			}

			if( isset( $where['job_type_id'] ) ){
				if( !empty( $where['job_type_id'] ) ){
					$job_types = ( !is_array( $where['job_type_id'] ) && ( (int) $where['job_type_id'] > 0 ) ) ? [ $where['job_type_id'] ] : ( ( is_array( $where['job_type_id'] ) ) ? $where['job_type_id'] : ( is_object( $where['job_type_id'] ) ? object_to_array( $where['job_type_id'] ) : [] ) );
					$this->db->where_in('job.job_type_id', $job_types );
				}
				unset( $where['job_type_id'] );
			}

			if( isset( $where['job_date_start'] ) || isset( $where['job_date_end'] ) ){
				if( !empty( $where['job_date_start'] ) ){
					$this->db->where( 'job.job_date >=', format_date_db( $where['job_date_start'] ) );
				}
				unset( $where['job_date_start'] );

				if( !empty( $where['job_date_end'] ) ){
					$this->db->where( 'job.job_date <=', format_date_db( $where['job_date_end'] ) );
					unset( $where['job_date_end'] );
				}
				unset( $where['job_date_end'] );
			}

			if( isset( $where['created_on_start'] ) || isset( $where['created_on_end'] ) ){
				if( !empty( $where['created_on_start'] ) ){
					$this->db->where( 'job.created_on >=', format_date_db( $where['created_on_start'] ).' 00:00:00' );
				}
				unset( $where['created_on_start'] );

				if( !empty( $where['created_on_end'] ) ){
					$this->db->where( 'job.created_on <=', format_date_db( $where['created_on_end'] ).' 23:59:59' );
				}
				unset( $where['created_on_end'] );
			}

			if( isset( $where['job_date'] ) ){
				if( !empty( $where['job_date'] ) ){
					$sjob_date = date( 'Y-m-d', strtotime( $where['job_date'] ) );
					$this->db->where( 'job.job_date', $sjob_date );
					unset( $where['job_date'] );
				}
			} else {
				if( isset( $where['date_from'] ) || isset( $where['date_to'] ) ){

					if( !empty( $where['date_from'] ) ){
						$this->db->where( 'job.job_date >=', date( 'Y-m-d', strtotime( format_date_db( $where['date_from'] ) ) ) );
					}

					if( !empty( $where['date_to'] ) ){
						$this->db->where( 'job.job_date <=', date( 'Y-m-d', strtotime( format_date_db( $where['date_to'] ) ) ) );
					}
					unset( $where['date_from'], $where['date_to'] );
				}
			}

			## Limit Jobs based on Associated User's Jobs
			if( !empty( $assignees ) ){
				if( !empty( $where['assigned_to'] ) ){
					$assignees[] 		= $where['assigned_to'];
				}
				$this->db->where_in( 'job.assigned_to', $assignees );
			} else {
				if( isset( $where['assigned_to'] ) ){
					if( !empty( $where['assigned_to'] ) ){
						$assigned_to = $where['assigned_to'];
						$assigned_to = convert_to_array( $where['assigned_to'] );
						$assigned_to = is_array( $assigned_to ) ? $assigned_to : [ $assigned_to ];
						$this->db->where_in('job.assigned_to', $assigned_to );

					}
					unset( $where['assigned_to'] );
				}
			}

			if( !empty( $where ) ){
				$this->db->where( $where );
			}

			$query = $this->db->group_by( 'job.job_id' )->get( 'job' );
			
			$results['total'] = !empty( $query->num_rows() ) ? $query->num_rows() : 0;
			$limit 			  = ( !empty( $limit > 0 ) ) ? $limit : $results['total'];
			$results['pages'] = !empty( $query->num_rows() ) ? ceil( $query->num_rows() / $limit ) : 0;

			return json_decode( json_encode( $results ) );
		}

		return $result;
	}
	
	
	## Get Completed Checklists against a Job
	public function get_completed_checklists( $account_id = false, $job_id = false, $site_id = false, $where = false, $order_by = false ){
		$result = false;
		if( !empty( $account_id ) ){
			
			$where = convert_to_array( $where );

			if( !empty( $site_id ) ){
				
				$site_jobs = $this->db->select( 'job_id', false )
					->where( '( job.external_job_ref != "" OR job.external_job_ref IS NOT NULL )' )
					->where( 'job.site_id', $site_id )
					->where( 'job.account_id', $account_id )
					->get( 'job' );
				
				if( $site_jobs->num_rows() > 0 ){
					$job_ids = array_column( $site_jobs->result_array(), 'job_id' );
				}
				
			} else {
				
				if( !empty( $job_id ) ){
					$job_ids = [$job_id];
				}
				
			}
		
			if( !empty( $where['un_grouped'] ) ){
				$un_grouped = true;
			}
		
			if( !empty( $job_ids ) ){

				$query = $this->db->select( 'resps.response_checklist_id, checks.evi_checklist_id, checks.checklist_id, checks.checklist_hashcode, checks.checklist_desc, job.job_id, job.job_date, job_types.job_type, job.external_job_call_status, CONCAT(user.first_name," ",user.last_name) `completed_by`', false )
					->join( 'tesseract_checklist checks', 'checks.checklist_id = resps.response_checklist_id', 'left' )
					->join( 'job', 'job.job_id = resps.job_id', 'left' )
					->join( 'job_types', 'job.job_type_id = job_types.job_type_id', 'left' )
					->join( 'user','user.id = resps.created_by','left' )
					->where_in( 'resps.job_id', $job_ids )
					->where( 'resps.account_id', $account_id )
					
					->order_by( 'resps.response_responseset_id' )
					->group_by( 'resps.response_checklist_id' )
					->get( 'tesseract_checklist_response `resps`' );

				if( $query->num_rows() > 0 ){
					$data = [];
					if( !empty( $site_id ) ){
						
						foreach( $query->result() as $k => $row ){
							$data[$row->job_id]['jobs_data'] = [
								'job_id' 		=> $row->job_id,
								'job_type' 		=> $row->job_type,
								'job_date' 		=> $row->job_date,
							];
							
							$data[$row->job_id]['checklists_data'][$row->checklist_id] = [
								'job_id' 			=> $row->job_id,
								'checklist_id' 		=> $row->checklist_id,
								'checklist_desc' 	=> $row->checklist_desc,
								'checklist_hashcode'=> $row->checklist_hashcode,
								'completed_by'		=> $row->completed_by,
								'responses_data'	=> null
							];
							
							$respoonses = $this->db->select( 'resps.*' )
								->where( 'resps.response_checklist_id', $row->checklist_id )
								->where( 'resps.account_id', $account_id )
								->where( 'resps.job_id', $row->job_id )
								->order_by( 'resps.response_question_order' )
								->get( 'tesseract_checklist_response `resps`' );
								
							if( $respoonses->num_rows() > 0 ){
								$data[$row->job_id]['checklists_data'][$row->checklist_id]['responses_data'] = $respoonses->result();
							}
						}
						
					} else {

						foreach( $query->result() as $k => $row ){
							
							if( !empty( $un_grouped ) ){

								$data[$k] = [
									'job_id' 			=> $row->job_id,
									'checklist_id' 		=> $row->checklist_id,
									'checklist_desc' 	=> $row->checklist_desc,
									'checklist_hashcode'=> $row->checklist_hashcode,
									'completed_by'		=> $row->completed_by,
									'responses_data'	=> null
								];
								
								$respoonses = $this->db->select( 'resps.*' )
									->where( 'resps.response_checklist_id', $row->checklist_id )
									->where( 'resps.account_id', $account_id )
									->where( 'resps.job_id', $job_id )
									->order_by( 'resps.response_question_order' )
									->get( 'tesseract_checklist_response `resps`' );
									
								if( $respoonses->num_rows() > 0 ){
									$data[$k]['responses_data'] = $respoonses->result();
								}
								
							} else {
								$data[$row->checklist_id] = [
									'job_id' 			=> $row->job_id,
									'checklist_id' 		=> $row->checklist_id,
									'checklist_desc' 	=> $row->checklist_desc,
									'checklist_hashcode'=> $row->checklist_hashcode,
									'completed_by'		=> $row->completed_by,
									'responses_data'	=> null
								];
								
								$respoonses = $this->db->select( 'resps.*' )
									->where( 'resps.response_checklist_id', $row->checklist_id )
									->where( 'resps.account_id', $account_id )
									->where( 'resps.job_id', $job_id )
									->order_by( 'resps.response_question_order' )
									->get( 'tesseract_checklist_response `resps`' );
									
								if( $respoonses->num_rows() > 0 ){
									$data[$row->checklist_id]['responses_data'] = $respoonses->result();
								}
							}
							
						}
					}
					$result = !empty( $data ) ? $data : false;
					$this->session->set_flashdata( 'message','Completed checklists data found' );
				} else {
					$this->session->set_flashdata( 'message','No Completed checklists data found' );
				}
			}
			
		}
		return $result;
	}


	/**
	* Search through list of Checklists
	*/
	public function checklist_search( $account_id = false, $job_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		
		$result = false;
		
		if( !empty( $account_id ) ){

			$where 	= $raw_where = convert_to_array( $where );
			
			$this->db->select( 'job.*, job_types.job_type, job_statuses.job_status, CONCAT(user.first_name," ",user.last_name) `assignee`, site.site_name, site.site_reference, site.site_postcodes', false )
				->join( 'job_types', 'job_types.job_type_id = job.job_type_id', 'left' )
				->join( 'site', 'site.site_id = job.site_id', 'left' )
				->join( 'tesseract_checklist_response', 'tesseract_checklist_response.job_id = job.job_id' )
				->join( 'job_statuses','job_statuses.status_id = job.status_id','left' )
				->join( 'user','user.id = job.assigned_to','left' )
				->where( 'job.archived !=', 1 )
				->where( 'job.account_id',$account_id )
				->where( 'job.external_job_ref > 0' );

			$job_id 	= !empty( $job_id ) ? $job_id : ( !empty( $where['job_id'] ) ? $where['job_id'] : false );
			
			if( !empty( $job_id ) ){

				$row = $this->db->get_where( 'job', ['job.job_id' => $job_id ] )->row();

				if( !empty( $row ) ){
					$row->checklists_data = $this->get_completed_checklists( $account_id, $job_id );
					$this->session->set_flashdata( 'message','Checklist record found' );
					$result = $row;
				} else {
					$this->session->set_flashdata( 'message','Checklist record not found' );
				}
				return $result;

			}
			
			if( !empty( $search_term ) ){
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->checklist_searchable_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				} else {
					foreach( $this->checklist_searchable_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}
					
					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( !empty( $order_by ) ){
				$this->db->order_by( $order_by );
			} else {
				$this->db->order_by( 'job.job_id DESC, job.job_id' );
			}

			if( $limit > 0 ){
				$this->db->limit( $limit, $offset );
			}
			
			$query = $this->db->group_by( 'job.job_id' )
				->get( 'job' );

			if( $query->num_rows() > 0 ){
				$data 						= [];
				$result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
				$result->records 			= $query->result();
				$counters 					= $this->get_checklist_search_totals( $account_id, $search_term, $raw_where, $limit );
				$result->counters->total 	= ( !empty( $counters->total ) ) ? $counters->total : null;
				$result->counters->pages 	= ( !empty( $counters->pages ) ) ? $counters->pages : null;
				$result->counters->limit  	= ( int ) $limit;
				$result->counters->offset 	= ( int ) $offset;
				$this->session->set_flashdata( 'message','Records found.' );
			} else {
				$this->session->set_flashdata( 'message','No records found matching your criteria.' );
			}

			return $result;
			
		}
	}

	
	/*
	* Get total Checklists count for the search
	*/
	public function get_checklist_search_totals( $account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT ){
		if( !empty( $account_id ) ){
			
			$where 			= $raw_where = convert_to_array( $where );
			
			$this->db->select( 'job.job_id', false )
				->join( 'job_types', 'job_types.job_type_id = job.job_type_id', 'left' )
				->join( 'site', 'site.site_id = job.site_id', 'left' )
				->join( 'tesseract_checklist_response', 'tesseract_checklist_response.job_id = job.job_id' )
				->join( 'job_statuses','job_statuses.status_id = job.status_id','left' )
				->join( 'user','user.id = job.assigned_to','left' )
				->where( 'job.archived !=', 1 )
				->where( 'job.account_id',$account_id )
				->where( 'job.external_job_ref > 0' );
				
			if( !empty( $search_term ) ){
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ' ) !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->checklist_searchable_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				} else {
					foreach( $this->checklist_searchable_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}
					
					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}
			
			$query = $this->db->group_by( 'job.job_id' )
				->get( 'job' );
			
			$results['total'] = !empty( $query->num_rows() ) ? $query->num_rows() : 0;
			$limit 			  = ( !empty( $limit > 0 ) ) ? $limit : $results['total'];
			$results['pages'] = !empty( $query->num_rows() ) ? ceil( $query->num_rows() / $limit ) : 0;

			return json_decode( json_encode( $results ) );

		}
	}	
	
}