<?php

namespace Application\Modules\Service\Models;

class Diary_model extends CI_Model {

	function __construct(){
		parent::__construct();
		$this->resource_searchable_fields = ['user_id', 'u3.first_name', 'u3.last_name'];
		$this->load->model( 'serviceapp/People_model','people_service' );
    }

	private $job_types_search_fields		= [ 'job_types.job_type', 'job_types.job_type_desc' ];
	private $skills_bank_search_fields 		= [ 'skill_name', 'skill_name', 'skill_description' ];
	private $postcode_areas_search_fields 	= [ 'postcode_district', /*'postcode_area', 'posttown', /*'county'*/ ];
	private $diary_regions_search_fields 	= [ 'postcode_district', 'postcode_area', 'posttown', 'county' ];
	private $address_regions_search_fields 	= [ 'postcode_district', /*'postcode_area'*/ ];
	private $default_days_view				= 30; //in days
	private $max_no_weeks_view				= 4; //in weeks

	/*
	* Get Diary-Dates single records or multiple records
	*/
	public function get_field_operatives( $account_id = false, $operative_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET, $order_by = false ){

		$result = false;

		if( $account_id ){

			$this->load->model( 'serviceapp/Job_model','job_service' );

			$types_array 			= false;
			$this->db->where_in( "user_type_group", ["field_operative", "account_admin"] );
			$field_operative_types 	= $this->db->get( "user_types" )->result_array();
			$types_array 			= array_column( $field_operative_types, "id" );

			## if( !empty( $types_array ) ){

				## get home address type id by the Account ID
				$address_type = $this->db->get_where( "address_types", ["account_id" => $account_id, "address_type_group" => "homeaddress" ] )->row();
				if( !empty( $address_type->address_type_id ) ){
					$addres_type_id = $address_type->address_type_id;
					
					if( !empty( $where ) ){
						$where = convert_to_array( $where );
					}
					
					$associated_user_id = $user_ids = false;

					if( !empty( $where ) ){
						if( !empty( $where['associated_user_id'] ) ){
							$associated_user_id = $where['associated_user_id'];
							unset( $where['associated_user_id'] );
							$helper_query = $this->db->get_where( "associated_users", ["account_id" => $account_id, "primary_user_id" => $associated_user_id] )->result_array();
							if( !empty( $helper_query ) ){
								$user_ids = array_column( $helper_query, 'user_id' );
								if( !empty( $user_ids ) ){
									$user_ids[] = $associated_user_id;
								}
							}
						}
					}

					$this->db->select( "u.account_id `account_user_id`, u.id, u.first_name, u.last_name, CONCAT( u.first_name, ' ', u.last_name ) `user_fullname`, u.email", false );
					$this->db->select( "a.addressline1, a.addressline2, a.addressline3, a.summaryline, a.postcode, a.posttown, a.number, a.summaryline `user_address`" );
					$this->db->select( "pca.address_line1 `home_address_line1`, pca.address_line2 `home_address_line2`, pca.address_line3 `home_address_line3`,  pca.address_town `home_address_town`,  pca.address_county `home_address_county`, pca.address_postcode `home_address_postcode`, pca.contact_mobile, CONCAT( pca.address_line1, ', ', pca.address_line2, ', ', pca.address_town, ', ', pca.address_county, ', ', pca.address_postcode ) `home_user_address`" );

					$this->db->join( "people_contact_addresses pca","pca.person_id = u.id", "left" );
					$this->db->join( "addresses a", "a.main_address_id = pca.address_id", "left" );
					$this->db->join( "diary_resource_schedule drs", "drs.user_id = u.id", "left" );
					$this->db->join( "people p", "p.person_id = u.id", "left" );

					if( !empty( $operative_id ) ){
						$this->db->where( "u.id", $operative_id );
					}
					
					if( !empty( $user_ids ) ){
						$this->db->where_in( "u.id", $user_ids );
					}
					
					## $this->db->where_in( "u.user_type_id", $types_array );
					$this->db->where( "u.can_be_assigned_jobs", 1 );
					
					$this->db->where( "pca.address_type_id", $addres_type_id );
					$this->db->where( "u.active", 1 );
					$this->db->where( "u.account_id", $account_id );
					
					## addresses not archived
					$pca_arch_where = "( pca.archived != 1 or pca.archived is NULL )";
					$this->db->where( $pca_arch_where );
					
					## Can Do/Can Accept triggers
					$this->db->where( "p.can_accept_jobs", 1 );
					$this->db->where( "p.can_do_jobs", 1 );

					if( !empty( $where ) ){
						if( !empty( $where['resource_date'] ) ){
							$resource_date = $where['resource_date'];
							$this->db->where( 'drs.ref_date', $resource_date );
							$this->db->where( 'drs.archived !=', 1 );
							unset( $where['resource_date'] );
						}

						if( !empty( $where ) ){
							$this->db->where( $where );
						}
					}

					$this->db->order_by( 'u.first_name ASC' );
					$query = $this->db->get( "user u", $limit, $offset );

					if( $query->num_rows() > 0 ){
						foreach( $query->result() as $key => $row ){
							$assigned_jobs = $this->job_service->get_jobs( $account_id, false, [ "assigned_to" => $row->id, "job_date"=>$resource_date, "assignees" => "not_use" ] );
							
							if( !empty( $assigned_jobs ) ){
								$result['with_jobs'][$key] 					= $row;
								$result['with_jobs'][$key]->user_id			= $row->id;
								$result['with_jobs'][$key]->user_slots 		= 10;
								$result['with_jobs'][$key]->assigned_jobs	= $assigned_jobs;
							} else {
								$result['without_jobs'][$key] 					= $row;
								$result['without_jobs'][$key]->user_id			= $row->id;
								$result['without_jobs'][$key]->user_slots 		= 10;
							}
						}
						$this->session->set_flashdata( 'message', 'Field Operatives found' );
					} else {
						$this->session->set_flashdata('message','Field Operatives not found');
					}
				} else {
					$this->session->set_flashdata( 'message', 'No "Home Address" address type found' );
					return $result;
				}
			## } else {
			## 	$this->session->set_flashdata( 'message', 'No required user type found' );
			##	return $result;
			## }
		}
		
		return $result;
	}

	public function route_jobs( $account_id = false, $job_batch = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $job_batch ) ){

			$job_batch = convert_to_array( $job_batch );

			foreach( $job_batch as $key => $row ){
				$data[$key] 						= $row;
				$data[$key]['status_id'] 			= 1;
				$data[$key]['last_modified'] 		= date( 'Y-m-d H:i:s' );
				$data[$key]['last_modified_by'] 	= $this->ion_auth->_current_user->id;
			}

			$query = $this->db->update_batch( 'job', $data, 'job_id' );

			if( $this->db->trans_status() !== FALSE ){
				$this->session->set_flashdata( 'message', 'Your job has been routed successfully' );
				$result = true;
			} else {
				$this->session->set_flashdata( 'message', 'The routing process wasn\'t successful' );
			}
		} else {
			$this->session->set_flashdata( 'message','Required data missing: Accont ID or Job(s) batch' );
		}
		return $result;
	}


	public function unschedule_job( $account_id = false, $job_id = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $job_id ) ){
			$data = [
				"assigned_to" 		=> NULL,
				"status_id"			=> 2,
				"job_order"			=> NULL,
				"last_modified"		=> date( "Y-m-d H:i:s" ),
				"last_modified_by"	=> $this->ion_auth->_current_user->id,
			];

			$query = $this->db->update( 'job', $data, ['job_id'=>$job_id] );

			if( $this->db->affected_rows() > 0 ){

				$job = $this->db->get_where( "job", [ "account_id" => $account_id, "job_id" => $job_id ] )->row();
				if( !empty( $job ) ){
					$result = $job;
				}

				$this->session->set_flashdata( 'message', 'The job has been unscheduled' );
			} else {
				$this->session->set_flashdata( 'message', 'The request couldn\'t be processed' );
			}
		} else {
			$this->session->set_flashdata( 'message','Required data missing: Accont ID or Job(s) batch' );
		}

		return $result;
	}

	/** Get all Routed Jobs for a Specific Date or Date range **/
	public function get_routed_jobs( $account_id = false, $where = false, $limit = false, $offset = false, $order_by = false, $grouped_by_date = false ){

		$result = false;

		if( !empty( $account_id ) ){

			$exempt_status_groups = [ 'failed','successful','unassigned','cancelled'];

			$this->db->select('job_id, job_date, job_types.job_type, job_types.job_type_desc, works_required `job_description`, job_duration, job_travel_time, job_order, job.status_id, job_statuses.job_status, job_statuses.status_group, job_statuses.job_status, job_statuses.status_group, assigned_to, CONCAT(user.first_name," ",user.last_name) `assignee`, addrs.postcode `job_postcode`,addrs.summaryline `job_address`',false )
				->join( 'addresses addrs','addrs.main_address_id = job.address_id','left' )
				->join( 'job_types','job_types.job_type_id = job.job_type_id','left' )
				->join( 'job_statuses','job_statuses.status_id = job.status_id','left' )
				->join( 'user','user.id = job.assigned_to','left' )
				->where( 'job.account_id', $account_id )
				->where( 'job.assigned_to >', 0 )
				->where( 'archived !=', 1 );

				$this->db->where( 'job.account_id', $account_id );

			if( !empty( $where ) ){

				$where = convert_to_array( $where );

				if( !isset( $where['all_statuses'] ) && empty(  $where['all_statuses']) ){
					$this->db->where_not_in( 'job_statuses.status_group', $exempt_status_groups );
				}

				if( isset( $where['assigned_to'] ) ){
					if( !empty( $where['assigned_to'] ) ){
						$this->db->where( 'job.assigned_to', $where['assigned_to'] );
					}
					unset( $where['assigned_to'] );
				}

				if( !empty( $where['date_from'] ) ){
					$date_from 	= date('Y-m-d',strtotime( $where['date_from'] ) );
					$date_to 	= ( !empty( $where['date_to'] ) ) ? date('Y-m-d',strtotime( $where['date_to'] ) ) : date( 'Y-m-d' );
					$this->db->where( 'job_date >=', $date_from );
					$this->db->where( 'job_date <=', $date_to );
				} else if ( !empty( $where['job_date'] ) ){
					$job_date = date( 'Y-m-d',strtotime( $where['job_date'] ) );
					$this->db->where( 'job_date', $job_date );
				}

				if( isset( $where['grouped_by_date'] ) ){
					if( !empty( $where['grouped_by_date'] ) ){
						$grouped_by_date = $where['grouped_by_date'];
					}
					unset( $where['grouped_by_date'] );
				}

			}

			$query = $this->db->order_by( 'assigned_to, job_date, job_order' )
				->limit( $limit, $offset )
				->get( 'job' );

			if( $query->num_rows() > 0 ){
				$engineer_jobs = [];
				foreach( $query->result() as $k => $row ){
					$row->job_order = ( string ) ( $k + 1 );

					if( $grouped_by_date ){
						$date_group = date( 'Y-m-d', strtotime( $row->job_date ) );
						if( !isset( $engineer_jobs[$row->assigned_to]['engineer_data'] ) ){
							$engineer_jobs[$row->assigned_to]['engineer_data'] = $this->get_engineer_data( $account_id, $row->assigned_to );
						}
						$engineer_jobs[$row->assigned_to]['engineer_jobs'][$date_group][] = $row;
					} else {
						if( !isset( $engineer_jobs[$row->assigned_to]['engineer_data'] ) ){
							$engineer_jobs[$row->assigned_to]['engineer_data'] = $this->get_engineer_data( $account_id, $row->assigned_to );
						}
						$engineer_jobs[$row->assigned_to]['engineer_jobs'][] = $row;
					}

				}
				$result = $engineer_jobs;
				$this->session->set_flashdata('message','Job records found');
			} else {
				$this->session->set_flashdata('message','Job record(s) not found');
			}
		} else {
			$this->session->set_flashdata('message','Your request is missing required information');
		}
		return $result;
	}

	/** Get Engineer's Data **/
	public function get_engineer_data( $account_id = false, $engineer_id = false, $date = false ){
		$result = null;
		if( !empty( $account_id ) ){

			if( $engineer_id ){
				$this->db->where( 'u.id', $engineer_id );
			}

			$query = $this->db->select( 'u.id `engineer_id`, u.first_name, u.last_name, a.summaryline `home_address`, a.postcode `home_postcode`, ', false )
				->join( 'people p', 'p.person_id = u.id' )
				->join( 'people_contact_addresses pca', 'pca.person_id = p.person_id', 'left' )
				->join( 'address_types at', 'at.address_type_id = pca.address_type_id', 'left' )
				->join( 'addresses a', 'a.main_address_id = pca.address_id', 'left' )
				->where( 'u.account_id', $account_id )
				#->where_in( 'at.address_type_group', ['residential','homeaddress`' ] )
				->get( 'user u' );

			if( $query->num_rows() > 0 ){
				if( $engineer_id ){
					$row = $query->result()[0];
					$row->availability = [
						'start_time' => '06:00',
						'finish_time' => '20:00',
						'total_slots' => '10',
						'on_call' => true
					];
					$result = $row;
				} else {
					$data 	= [];
					foreach( $query->result() as $k => $row ){
						$row->availability = [
							'start_time' => '06:00',
							'finish_time' => '20:00',
							'total_slots' => '10',
							'on_call' => true
						];
						$data[$k] = $row;
					}
					$result = $data;
				}
			}
		}
		return $result;
	}

	/** Get Postcode Areas **/
	public function get_postcode_areas( $account_id = false, $search_term = false, $where = false, $limit = 100, $offset = DEFAULT_OFFSET ){

		$result = false;

		if( !empty( $account_id ) ){

			if( !empty( $search_term ) ){

				$search_term  = trim( urldecode( $search_term ) );
				$search_term  = str_replace( '.', ',', $search_term );

				$search_where = [];
				if( strpos( $search_term, ',') !== false ) {
					$multiple_terms = explode( ',', $search_term );
					foreach( $multiple_terms as $term ){
						if( !empty( $term ) ){
							foreach( $this->postcode_areas_search_fields as $k=>$field ){
								$search_where[$field] = trim( $term );
							}
							$where_combo = format_like_to_where( $search_where );
							$this->db->or_where( $where_combo );
						}
					}
				} else if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						if( !empty( $term ) ){
							foreach( $this->postcode_areas_search_fields as $k=>$field ){
								$search_where[$field] = trim( $term );
							}
							$where_combo = format_like_to_where( $search_where );
							$this->db->where( $where_combo );
						}
					}
				} else {

					foreach( $this->postcode_areas_search_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( !empty( $where ) ){

				$where = convert_to_array( $where );

				if( isset( $where['postcode_district'] ) ){
					if( !empty( $where['postcode_district'] ) ){
						$this->db->where( 'addr.postcode_district', $where['postcode_district'] );
					}
					unset( $where['postcode_district'] );
				}

			}


			if( $limit > 0 ){
				$this->db->limit( $limit, $offset );
			}

			#$query = $this->db->select( 'postcode_area, postcode_district, posttown, county', false )
			$query = $this->db->select( 'postcode_area, postcode_district, posttown, county', false )
				->order_by( 'LENGTH( addr.postcode_district ), addr.postcode_district, addr.postcode_district' )
				->group_by( 'addr.postcode_district' )
				->get( 'addresses addr' );

			if( $query->num_rows() > 0 ){
				$this->session->set_flashdata( 'message','Postcode areas data found.' );
				$result = $query->result();
			} else {
				$this->session->set_flashdata( 'message','Postcode areas data not found.' );
			}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information.' );
		}

		return $result;
	}

	/** Create a new Skill Set record **/
	public function add_skill_set( $account_id = false, $skill_set_data = false ){

		$result = null;

		if( !empty( $account_id ) && !empty( $skill_set_data ) ){

			foreach( $skill_set_data as $col => $value ){
				if( $col == 'skill_name' ){
					$data['skill_ref'] = strtolower( strip_all_whitespace( $value ) );
				}
				$data[$col] = $value;
			}

			if( !empty( $data['override_existing'] ) && !empty( $data['skill_id'] ) ){
				$override_existing = true;
				//User said override the current record
				$check_exists = $this->db->select( 'skills_bank.*', false )
					->where( 'skills_bank.account_id', $account_id )
					->where( 'skills_bank.skill_id', $data['skill_id'] )
					->get( 'skills_bank' )->row();

			} else {

				unset( $data['skill_id'] );
				$check_exists = $this->db->select( 'skills_bank.*', false )
					->where( 'skills_bank.account_id', $account_id )
					->where( '( skills_bank.skill_name = "'.$data['skill_name'].'" OR skills_bank.skill_ref = "'.$data['skill_ref'].'" )' )
					->limit( 1 )
					->get( 'skills_bank' )
					->row();
			}

			$data = $this->ssid_common->_filter_data( 'skills_bank', $data );

			if( !empty( $check_exists  ) ){

				if( !empty( $override_existing ) ){
					$data['last_modified_by'] = $this->ion_auth->_current_user->id;
					$this->db->where( 'skill_id', $check_exists->skill_id )
						->update( 'skills_bank', $data );

					$this->session->set_flashdata( 'message', 'This Skill Set already exists, record has been updated successfully.' );
					$result = $check_exists;
				} else {
					$this->session->set_flashdata( 'message', 'This Skill Set already exists, Would you like to override it?' );
					$this->session->set_flashdata( 'already_exists', 'True' );
					$result = $check_exists;
				}

			} else {
				$data['created_by'] = $this->ion_auth->_current_user->id;
				$this->db->insert( 'skills_bank', $data );
				$data['skill_id']	= $this->db->insert_id();
				$data = $this->get_skills( $account_id, $data['skill_id'] );
				$this->session->set_flashdata( 'message', 'New Skill Set created successfully.' );
				$result = $data;
			}

		} else {
			$this->session->set_flashdata( 'message','Error! Missing required information.' );
		}

		return $result;
	}

	/** Update an existing Skill Set record **/
	public function update_skill( $account_id = false, $skill_id = false, $update_data = false  ){
		$result = false;
		if( !empty( $account_id ) && !empty( $skill_id )  && !empty( $update_data ) ){

			$ref_condition = [ 'account_id'=>$account_id, 'skill_id'=>$skill_id ];
			$update_data   = $this->ssid_common->_data_prepare( $update_data );
			$update_data   = $this->ssid_common->_filter_data( 'skills_bank', $update_data );
			$record_pre_update = $this->db->get_where( 'skills_bank', [ 'account_id'=>$account_id, 'skill_id'=>$skill_id ] )->row();

			if( !empty( $record_pre_update ) ){

				$update_data['skill_ref'] 			= strtolower( strip_all_whitespace( $update_data['skill_name'] ) );
				$skill_set_where = '( skills_bank.skill_name = "'.$update_data['skill_name'].'" OR skills_bank.skill_ref = "'. $update_data['skill_ref'] .'" )';;

				$check_conflict = $this->db->select( 'skill_id', false )
					->where( 'skills_bank.account_id', $account_id )
					->where( 'skills_bank.skill_id !=', $skill_id )
					->where( $skill_set_where )
					->limit( 1 )
					->get( 'skills_bank' )
					->row();

				if( !$check_conflict ){

					$update_data['last_modified_by'] = $this->ion_auth->_current_user->id;
					$this->db->where( $ref_condition )
						->update( 'skills_bank', $update_data );

					$updated_record = $this->get_skills( $account_id, $skill_id );
					$result 		= ( !empty( $updated_record->records ) ) ? $updated_record->records : ( !empty( $updated_record ) ? $updated_record : false );

					$this->session->set_flashdata( 'message', 'Skill profile record updated successfully' );
					return $result;
				} else {
					$this->session->set_flashdata( 'message', 'Skill profile record updated successfully' );
					return false;
				}

			} else {
				$this->session->set_flashdata( 'message', 'This Skill profile record does not exist or does not belong to you.' );
				return false;
			}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information.' );
		}
		return $result;
	}

	/*
	*	Get list of Skill sets and search through it
	*/
	public function get_skills( $account_id = false, $skill_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;

		if( !empty( $account_id ) ){
			$this->db->select( 'skills_bank.*, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false )
				->join( 'user creater', 'creater.id = skills_bank.created_by', 'left' )
				->join( 'user modifier', 'modifier.id = skills_bank.last_modified_by', 'left' )
				->where( 'skills_bank.is_active', 1 )
				->where( 'skills_bank.account_id', $account_id );

			$where = $raw_where = convert_to_array( $where );

			if( !empty( $skill_id ) || isset( $where['skill_id'] ) ){
				$skill_id	= ( !empty( $skill_id ) ) ? $skill_id : $where['skill_id'];
				if( !empty( $skill_id ) ){

					$row = $this->db->get_where( 'skills_bank', ['skill_id'=>$skill_id ] )->row();

					if( !empty( $row ) ){
						$result = $row;
						$this->session->set_flashdata( 'message','Skill Sets data found' );
						return $result;
					} else {
						$this->session->set_flashdata( 'message','Skill Sets data not found' );
						return false;
					}
				}
				unset( $where['skill_id'], $where['skill_ref'] );
			}

			if( !empty( $search_term ) ){
				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->skills_bank_search_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				} else {
					foreach( $this->skills_bank_search_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( !empty( $where ) ){

				if( isset( $where['skill_name'] ) ){
					if( !empty( $where['skill_name'] ) ){
						$skill_ref = strtoupper( strip_all_whitespace( $where['skill_name'] ) );
						$this->db->where( '( skills_bank.skill_name = "'.$where['skill_name'].'" OR skills_bank.skill_ref = "'.$skill_ref.'" )' );
					}
					unset( $where['skill_name'] );
				}

				if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}

			if( !empty( $order_by ) ){
				$this->db->order_by( $order_by );
			} else {
				$this->db->order_by( 'skill_id DESC, skill_name' );
			}

			if( $limit > 0 ){
				$this->db->limit( $limit, $offset );
			}

			$query = $this->db->get( 'skills_bank' );

			if( $query->num_rows() > 0 ){

				$result_data = $query->result();

				$result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
				$result->records 			= $result_data;
				$counters 					= $this->skills_bank_totals( $account_id, $search_term, $raw_where );
				$result->counters->total 	= ( !empty( $counters->total ) ) ? $counters->total : null;
				$result->counters->pages 	= ( !empty( $counters->pages ) ) ? $counters->pages : null;
				$result->counters->limit  	= ( !empty( $apply_limit ) ) ? $limit : $result->counters->total;
				$result->counters->offset 	= $offset;

				$this->session->set_flashdata( 'message','Skill Sets data found' );
			} else {
				$this->session->set_flashdata( 'message','There\'s currently no Skill Sets setup for your Account' );
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}

		return $result;
	}

	/** Get Skill Set lookup counts **/
	public function skills_bank_totals( $account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT ){
		$result = false;
		if( !empty( $account_id ) ){

			$this->db->select( 'skills_bank.skill_id', false )
				->where( 'skills_bank.is_active', 1 )
				->where( 'skills_bank.account_id', $account_id );

			$where = $raw_where = convert_to_array( $where );

			if( !empty( $search_term ) ){
				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->skills_bank_search_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				} else {
					foreach( $this->skills_bank_search_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( !empty( $where ) ){

				if( isset( $where['skill_name'] ) ){
					if( !empty( $where['skill_name'] ) ){
						$skill_ref = strtoupper( strip_all_whitespace( $where['skill_name'] ) );
						$this->db->where( '( skills_bank.skill_name = "'.$where['skill_name'].'" OR skills_bank.skill_ref = "'.$skill_ref.'" )' );
					}
					unset( $where['skill_name'] );
				}

				if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}

			$query 			  = $this->db->from( 'skills_bank' )->count_all_results();
			$limit 				= ( $limit > 0 ) ? $limit : $results['total'];
			$results['total'] = !empty( $query ) ? $query : 0;
			$results['pages'] = !empty( $query ) ? ceil( $query / $limit ) : 0;
			return json_decode( json_encode( $results ) );
		}
		return $result;
	}



	/*
	* 	Search through diary resources
	*/
	public function resources_lookup( $account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;
		if( !empty( $account_id ) ){

			$this->db->select( 'drs.*', false );

			$this->db->select( 'CONCAT( u1.first_name, " ", u1.last_name ) created_by_full_name', false );
			$this->db->select( 'CONCAT( u2.first_name, " ", u2.last_name ) modified_by_full_name', false );
			$this->db->select( 'CONCAT( u3.first_name, " ", u3.last_name ) user_full_name', false );

			$this->db->join( 'user u1', 'u1.id = drs.created_by', 'left' );
			$this->db->join( 'user u2', 'u2.id = drs.last_modified_by', 'left' );
			$this->db->join( 'user u3', 'u3.id = drs.user_id', 'left' );

			$this->db->where( 'drs.account_id', $account_id );
			$this->db->where( 'drs.ref_date >= "'.date( 'Y-m-d' ).'"' );

			$arch_where = "( drs.archived != 1 or drs.archived is NULL )";
			$this->db->where( $arch_where );

			if( !empty( $search_term ) ){

				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->resource_searchable_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}

				} else {
					foreach( $this->resource_searchable_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( $where ){
				$where = convert_to_array( urldecode( $where ) );

				## filters
				if( !empty( $where['skill_set'] ) ){
					$skill_set = $where['skill_set'];
					unset( $where['skill_set'] );
				}

				if( !empty( $where['region'] ) ){
					$region = $where['region'];
					unset( $where['region'] );
				}

				if( !empty( $where['dates'] ) ){
					$dates = $where['dates'];
					foreach( $dates as $key => $row ){
						$formatted_dates[$key] = format_date_db( $row );
					}
					$this->db->where_in( "ref_date", $formatted_dates );
					unset( $where['dates'] );
				}

				if( !empty( $where['days'] ) ){
					$days = $where['days'];
					$this->db->where_in( "day", $days );
					unset( $where['days'] );
				}

				if( !empty( $where['start_times'] ) ){
					$start_times = $where['start_times'];
					$this->db->where_in( "start_time", $start_times );
					unset( $where['start_times'] );
				}

				if( !empty( $where['finish_times'] ) ){
					$finish_times = $where['finish_times'];
					$this->db->where_in( "finish_time", $finish_times );
					unset( $where['finish_times'] );
				}

				if( !empty( $where['timeframe'] ) ){
					$timeframe = $where['timeframe'];

					$date 				= date( 'Y-m-d' );
					if( $timeframe > 0 ){
						$date_from 			= date( 'Y-m-d', strtotime( $date.' + '.$timeframe.' days' ) );
						$where_due_dates 	= '( ( event_review_date >="'.$date.'" ) AND ( event_review_date <= "'.$date_from.'" ) )';
					} else {
						$where_due_dates 	= '( event_review_date <"'.$date.'" )';
					}
					$this->db->where( $where_due_dates );
					unset( $where['timeframe'] );
				}

				if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}

			if( $order_by ){
				$this->db->order_by( $order_by );
			} else {
				$this->db->order_by( 'user_full_name ASC' );
			}

			if( $limit > 0 ){
				$this->db->limit( $limit, $offset );
			} else {
				$this->db->limit( DEFAULT_MAX_LIMIT, $offset );
			}


			$query = $this->db->get( 'diary_resource_schedule drs' );

			if( $query->num_rows() > 0 ){

				$result_set = $query->result();

				$table = "diary_resource_schedule";
				$result['data'] 				= array_slice( $result_set, 0, DEFAULT_LIMIT );
				$result['available_options'] 	= $this->get_available_options( $account_id, $table );
				$result['counters']				= $this->get_result_counters( $account_id, $result_set, $function = 'resources_lookup' );

				$this->session->set_flashdata( 'message','Records found.' );
			} else {
				$this->session->set_flashdata( 'message','No records found matching your criteria.' );
			}
		}

		return $result;
	}


	/*
	* 	Get total resource count for the lookup
	*/
	public function get_total_resources( $account_id = false, $search_term = false, $where = false ){
		$result = false;
		if( !empty( $account_id ) ){

			$this->db->select( 'drs.*', false );

			$this->db->select( 'CONCAT( u1.first_name, " ", u1.last_name ) created_by_full_name', false );
			$this->db->select( 'CONCAT( u2.first_name, " ", u2.last_name ) modified_by_full_name', false );
			$this->db->select( 'CONCAT( u3.first_name, " ", u3.last_name ) user_full_name', false );

			$this->db->join( 'user u1', 'u1.id = drs.created_by', 'left' );
			$this->db->join( 'user u2', 'u2.id = drs.last_modified_by', 'left' );
			$this->db->join( 'user u3', 'u3.id = drs.user_id', 'left' );

			$this->db->where( 'drs.account_id', $account_id );
			$this->db->where( 'drs.ref_date >= "'.date( 'Y-m-d' ).'"' );

			$arch_where = "( drs.archived != 1 or drs.archived is NULL )";
			$this->db->where( $arch_where );

			if( !empty( $search_term ) ){

				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->resource_searchable_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}

				} else {
					foreach( $this->resource_searchable_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( $where ){
				$where = convert_to_array( urldecode( $where ) );

				## filters
				if( !empty( $where['skill_set'] ) ){
					$skill_set = $where['skill_set'];
					unset( $where['skill_set'] );
				}

				if( !empty( $where['region'] ) ){
					$region = $where['region'];
					unset( $where['region'] );
				}

				if( !empty( $where['dates'] ) ){
					$dates = $where['dates'];
					foreach( $dates as $key => $row ){
						$formatted_dates[$key] = format_date_db( $row );
					}
					$this->db->where_in( "ref_date", $formatted_dates );
					unset( $where['dates'] );
				}

				if( !empty( $where['days'] ) ){
					$days = $where['days'];
					$this->db->where_in( "day", $days );
					unset( $where['days'] );
				}

				if( !empty( $where['start_times'] ) ){
					$start_times = $where['start_times'];
					$this->db->where_in( "start_time", $start_times );
					unset( $where['start_times'] );
				}

				if( !empty( $where['finish_times'] ) ){
					$finish_times = $where['finish_times'];
					$this->db->where_in( "finish_time", $finish_times );
					unset( $where['finish_times'] );
				}

				if( !empty( $where['timeframe'] ) ){
					$timeframe = $where['timeframe'];

					$date 				= date( 'Y-m-d' );
					if( $timeframe > 0 ){
						$date_from 			= date( 'Y-m-d', strtotime( $date.' + '.$timeframe.' days' ) );
						$where_due_dates 	= '( ( event_review_date >="'.$date.'" ) AND ( event_review_date <= "'.$date_from.'" ) )';
					} else {
						$where_due_dates 	= '( event_review_date <"'.$date.'" )';
					}
					$this->db->where( $where_due_dates );
					unset( $where['timeframe'] );
				}

				if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}

			$query = $this->db->from( 'diary_resource_schedule drs' )->count_all_results();
			$results['total'] = !empty( $query ) ? $query : 0;
			$results['pages'] = !empty( $query ) ? ceil( $query / DEFAULT_LIMIT ) : 0;
			return json_decode( json_encode( $results ) );
		}
		return $result;
	}



	public function get_available_options( $account_id = false, $table = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $table ) ){

			switch( $table ){
				case "diary_resource_schedule":

				$options_column = ["day", "start_time"];
				foreach( $options_column as $column ){
					$query = false;
					$this->db->select( $column )->distinct();
					$query = $this->db->get( $table );
					foreach( $query->result_array() as $row ){
						$result[$column][] = $row[$column];
					}
				}
			}
		}
		return $result;
	}


	public function get_result_counters( $account_id = false, $data = false, $function = 'resources_lookup' ){
		$result = false;

		if( !empty( $account_id ) && !empty( $data ) && !empty( $function ) ){
			switch( $function ){
				case 'resources_lookup' :
					$result['slots_count']		= [
						"heading"	=> "Slots",
						"numbers"	=>	array_sum( array_column( $data, 'base_slots' ) )
					];
					$result['operatives_count'] = [
						"heading"	=> "Operatives",
						"numbers"	=> count( array_unique( array_column( $data, 'user_id' ) ) ),
						];
					$result['days_count'] 		= [
						"heading"	=> "Days",
						"numbers"	=> count( array_unique( array_column( $data, 'ref_date' ) ) ),
					];
				default:
				break;
			}
		}

		return $result;
	}


	/** Create a new Region record **/
	public function add_region( $account_id = false, $region_data = false ){

		$result = null;

		if( !empty( $account_id ) && !empty( $region_data ) ){

			if( !empty( $region_data['region_postcodes'] ) ){
				$region_postcodes = $region_data['region_postcodes'];
				unset( $region_data['region_postcodes'] );
			}

			foreach( $region_data as $col => $value ){
				if( $col == 'region_name' ){
					$data['region_ref'] = strtolower( strip_all_whitespace( $value ) );
				}
				
				if( $col == 'notification_emails' ){
					$value = json_encode( array_map( 'trim', array_filter( explode( ',', $value ) ) ) );
				}
				
				$data[$col] = $value;
			}

			if( !empty( $data['override_existing'] ) && !empty( $data['region_id'] ) ){
				$override_existing = true;
				//User said override the current record
				$check_exists = $this->db->select( 'diary_regions.*', false )
					->where( 'diary_regions.account_id', $account_id )
					->where( 'diary_regions.region_id', $data['region_id'] )
					->get( 'diary_regions' )->row();

			} else {

				unset( $data['region_id'] );
				$check_exists = $this->db->select( 'diary_regions.*', false )
					->where( 'diary_regions.account_id', $account_id )
					->where( '( diary_regions.region_name = "'.$data['region_name'].'" OR diary_regions.region_ref = "'.$data['region_ref'].'" )' )
					->limit( 1 )
					->get( 'diary_regions' )
					->row();
			}

			$data = $this->ssid_common->_filter_data( 'diary_regions', $data );

			if( !empty( $check_exists  ) ){

				if( !empty( $override_existing ) ){
					$data['last_modified_by'] = $this->ion_auth->_current_user->id;
					$this->db->where( 'region_id', $check_exists->region_id )
						->update( 'diary_regions', $data );

					$this->session->set_flashdata( 'message', 'This Region already exists, record has been updated successfully.' );
					$result = $check_exists;
				} else {
					$this->session->set_flashdata( 'message', 'This Region already exists, Would you like to override it?' );
					$this->session->set_flashdata( 'already_exists', 'True' );
					$result = $check_exists;
				}

			} else {
				$data['created_by'] = $this->ion_auth->_current_user->id;
				$this->db->insert( 'diary_regions', $data );
				$data['region_id']	= $this->db->insert_id();
				$data = $this->get_regions( $account_id, $data['region_id'] );
				$this->session->set_flashdata( 'message', 'New Region created successfully.' );
				$result = $data;
			}

			## Update Postcode Coverage areas
			if( !empty( $region_postcodes ) ){
				$add_postcodes = $this->add_region_postcodes( $account_id, $result->region_id, [ 'region_postcodes'=>$region_postcodes ] );
			}

		} else {
			$this->session->set_flashdata( 'message','Error! Missing required information.' );
		}

		return $result;
	}


	/** Update an existing Region record **/
	public function update_region( $account_id = false, $region_id = false, $update_data = false  ){
		$result = false;
		if( !empty( $account_id ) && !empty( $region_id )  && !empty( $update_data ) ){

			$ref_condition = [ 'account_id'=>$account_id, 'region_id'=>$region_id ];
			$update_data   = $this->ssid_common->_data_prepare( $update_data );
			$update_data   = $this->ssid_common->_filter_data( 'diary_regions', $update_data );
			$record_pre_update = $this->db->get_where( 'diary_regions', [ 'account_id'=>$account_id, 'region_id'=>$region_id ] )->row();

			if( !empty( $record_pre_update ) ){

				if( !empty( $update_data['notification_emails'] ) ){
					$update_data['notification_emails'] = json_encode( array_map( 'trim', array_filter( explode( ',', $update_data['notification_emails'] ) ) ) );
				}

				$update_data['region_ref'] 			= strtolower( strip_all_whitespace( $update_data['region_name'] ) );
				$region_where = '( diary_regions.region_name = "'.$update_data['region_name'].'" OR diary_regions.region_ref = "'. $update_data['region_ref'] .'" )';;

				$check_conflict = $this->db->select( 'region_id', false )
					->where( 'diary_regions.account_id', $account_id )
					->where( 'diary_regions.region_id !=', $region_id )
					->where( $region_where )
					->limit( 1 )
					->get( 'diary_regions' )
					->row();

				if( !$check_conflict ){

					$update_data['last_modified_by'] = $this->ion_auth->_current_user->id;
					$this->db->where( $ref_condition )
						->update( 'diary_regions', $update_data );

					$updated_record = $this->get_regions( $account_id, $region_id );
					$result 		= ( !empty( $updated_record->records ) ) ? $updated_record->records : ( !empty( $updated_record ) ? $updated_record : false );

					$this->session->set_flashdata( 'message', 'Region profile record updated successfully' );
					return $result;
				} else {
					$this->session->set_flashdata( 'message', 'Region profile record updated successfully' );
					return false;
				}

			} else {
				$this->session->set_flashdata( 'message', 'This Region profile record does not exist or does not belong to you.' );
				return false;
			}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information.' );
		}
		return $result;
	}


	/*
	* Delete a Region
	*/
	public function delete_region( $account_id = false, $region_id = false ){
		//
	}

	/*
	*	Get list of Region sets and search through it
	*/
	public function get_regions( $account_id = false, $region_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){

		$result = false;

		if( !empty( $account_id ) ){
			
			## group_concat_max_len is by default limited to 1024 only. 
			$ini_query = "SET session group_concat_max_len=15000";
			$this->db->query( $ini_query );
		
			$this->db->select( 'diary_regions.*, GROUP_CONCAT( DISTINCT drp.`postcode_district` SEPARATOR ", " ) AS `region_postcodes`, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false )
				->join( 'user creater', 'creater.id = diary_regions.created_by', 'left' )
				->join( 'user modifier', 'modifier.id = diary_regions.last_modified_by', 'left' )
				->join( 'diary_region_postcodes drp', 'drp.region_id = diary_regions.region_id', 'left' )
				->where( 'diary_regions.is_active', 1 )
				->where( 'diary_regions.account_id', $account_id )
				->group_by( 'diary_regions.region_id' );

			$where = $raw_where = convert_to_array( $where );

			if( !empty( $region_id ) || isset( $where['region_id'] ) ){
				$region_id	= ( !empty( $region_id ) ) ? $region_id : $where['region_id'];
				if( !empty( $region_id ) ){

					$row = $this->db->get_where( 'diary_regions', ['diary_regions.region_id'=>$region_id ] )->row();

					if( !empty( $row ) ){
						$row->region_areas 			= $this->get_region_postcodes( $account_id, $region_id );
						$row->notification_emails	= is_json( $row->notification_emails ) ? implode( ', ', json_decode( $row->notification_emails ) ) : $row->notification_emails;
						
						$result  				= $row;
						$this->session->set_flashdata( 'message','Regions data found' );
						return $result;
					} else {
						$this->session->set_flashdata( 'message','Regions data not found' );
						return false;
					}
				}
				unset( $where['region_id'], $where['region_ref'] );
			}



			if( !empty( $search_term ) ){
				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->diary_regions_search_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				} else {
					foreach( $this->diary_regions_search_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( !empty( $where ) ){

				if( isset( $where['region_name'] ) ){
					if( !empty( $where['region_name'] ) ){
						$region_ref = strtoupper( strip_all_whitespace( $where['region_name'] ) );
						$this->db->where( '( diary_regions.region_name = "'.$where['region_name'].'" OR diary_regions.region_ref = "'.$region_ref.'" )' );
					}
					unset( $where['region_name'] );
				}

				if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}

			if( !empty( $order_by ) ){
				$this->db->order_by( $order_by );
			} else {
				$this->db->order_by( 'region_id DESC, region_name' );
			}

			if( $limit > 0 ){
				$this->db->limit( $limit, $offset );
			}

			$query = $this->db->get( 'diary_regions' );

			if( $query->num_rows() > 0 ){

				$result_data = $query->result();

				$result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
				$result->records 			= $result_data;
				$counters 					= $this->diary_regions_totals( $account_id, false, $raw_where );
				$result->counters->total 	= ( !empty( $counters->total ) ) ? $counters->total : null;
				$result->counters->pages 	= ( !empty( $counters->pages ) ) ? $counters->pages : null;
				$result->counters->limit  	= ( !empty( $apply_limit ) ) ? $limit : $result->counters->total;
				$result->counters->offset 	= $offset;

				$this->session->set_flashdata( 'message','Regions data found' );
			} else {
				$this->session->set_flashdata( 'message','There\'s currently no Regions setup for your Account' );
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}

		return $result;
	}


	/** Get Region lookup counts **/
	public function diary_regions_totals( $account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT ){
		$result = false;
		if( !empty( $account_id ) ){

			$this->db->select( 'diary_regions.region_id', false )
				->where( 'diary_regions.is_active', 1 )
				->where( 'diary_regions.account_id', $account_id );

			$where = $raw_where = convert_to_array( $where );

			if( !empty( $search_term ) ){
				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->diary_regions_search_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				} else {
					foreach( $this->diary_regions_search_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( !empty( $where ) ){

				if( isset( $where['region_name'] ) ){
					if( !empty( $where['region_name'] ) ){
						$region_ref = strtoupper( strip_all_whitespace( $where['region_name'] ) );
						$this->db->where( '( diary_regions.region_name = "'.$where['region_name'].'" OR diary_regions.region_ref = "'.$region_ref.'" )' );
					}
					unset( $where['region_name'] );
				}

				if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}

			$query 			  = $this->db->from( 'diary_regions' )->count_all_results();
			$results['total'] = !empty( $query ) ? $query : 0;
			$limit 				= ( $limit > 0 ) ? $limit : $results['total'];
			$results['pages'] = !empty( $query ) ? ceil( $query / $limit ) : 0;
			return json_decode( json_encode( $results ) );
		}
		return $result;
	}


	/** Add Region Postcodes **/
	public function add_region_postcodes( $account_id = false, $region_id = false, $postdata = false ){
		$result = false;
		if( !empty( $region_id ) && !empty( $region_id ) && !empty( $postdata ) ){

			$postdata 		 = convert_to_array( $postdata );

			$region_postcodes= !empty( $postdata['region_postcodes'] ) ? $postdata['region_postcodes'] : false;
			$region_postcodes= ( is_json( $region_postcodes ) ) ? json_decode( $region_postcodes ) : $region_postcodes;
			$total			 = [];

			if( !empty( $region_postcodes ) ){
				foreach( $region_postcodes as $k => $val ){
					$postcode_area 	= $this->get_address_regions( $account_id, false, [ 'postcode_district'=>$val, 'result_as_array'=>1 ] );
					$postcode_area 	= ( !empty( $postcode_area[0] ) ) ? $postcode_area[0] : [];
					$postcode_area 	= is_object( $postcode_area ) ? object_to_array( $postcode_area ) : $postcode_area;
					$params 		= [ 'postcode_district'=>$val, 'region_id'=>$region_id, 'account_id'=>$account_id ];
					$data   		= ( !empty( $postcode_area ) ) ? array_merge( $params, $postcode_area ) : $params;
					$check_exists 	= $this->db->limit( 1 )->get_where( 'diary_region_postcodes', $params )->row();
					if( !$check_exists ){
						$this->db->insert( 'diary_region_postcodes', $data );
						$rec_id = $this->db->insert_id();
					}else{
						$rec_id = $check_exists->id;
					}

					$data 	 = $this->get_region_postcodes( $account_id, $region_id, [ 'id'=>$rec_id ] );
					$total[] = $data;
				}
			} else if( !empty( $postdata['postcode_area'] ) ) {

				$postcode_area 	= $this->get_address_regions( $account_id, false, [ 'postcode_district'=>$postdata['postcode_area'], 'result_as_array'=>1 ] );
				$postcode_area 	= ( !empty( $postcode_area[0] ) ) ? $postcode_area[0] : [];
				$postcode_area 	= is_object( $postcode_area ) ? object_to_array( $postcode_area ) : $postcode_area;
				$params 		= [ 'postcode_district'=>$postdata['postcode_area'], 'region_id'=>$region_id, 'account_id'=>$account_id ];
				$data   		= ( !empty( $postcode_area ) ) ? array_merge( $params, $postcode_area ) : $params;
				$check_exists 	= $this->db->limit( 1 )->get_where( 'diary_region_postcodes', $params )->row();

				if( !$check_exists ){
					$this->db->insert( 'diary_region_postcodes', $data );
					$rec_id = $this->db->insert_id();
				}else{
					$rec_id = $check_exists->id;
				}

				$data 	 = $this->get_region_postcodes( $account_id, $region_id, [ 'id'=>$rec_id ] );
				$total[] = $data;
			}

			if( !empty( $total ) ){
				$result = $total;
				$this->session->set_flashdata('message','Region postcode areas added successfully');
			} else {
				$this->session->set_flashdata('message','Region postcode areas not found');
			}
		} else {
			$this->session->set_flashdata('message','You request is missing required information');
		}
		return $result;
	}


	/** Get a list of Region Postcodes to a Region **/
	public function get_region_postcodes( $account_id = false, $region_id = false, $where = false ){

		$result = false;

		if( !empty( $account_id ) && !empty( $region_id ) ){

			if( !empty( $where ) ){
				$where = convert_to_array( $where );
				$region_id = ( !empty( $region_id ) ) ? $region_id : ( !empty( $where['region_id'] ) ? $where['region_id'] : false );
			}

			if( !empty( $account_id ) ){
				$this->db->where( 'drp.account_id', $account_id );
			}

			if( !empty( $region_id ) ){
				$this->db->where( 'drp.region_id', $region_id );
			}

			if( !empty( $where['id'] ) ){
				$row = $this->db->select( 'drp.*', false )
					->where( 'drp.id', $where['id'] )
					->get( 'diary_region_postcodes drp' )
					->row();

				return $row;
			}

			if( !empty( $where['postcode_area'] ) ){

				$postcodes_arr	= ( is_array( $where['postcode_area'] ) ) ? $where['postcode_area'] : ( is_string( $where['postcode_area'] ) ? [$where['postcode_area']] : false );

				if( !empty( $postcodes_arr ) && is_array( $postcodes_arr ) ){
					$this->db->where_in( 'drp.postcode_area', $postcodes_arr );
				}
			}

			if( !empty( $where['postcode_district'] ) ){

				$postcodes_arr	= ( is_array( $where['postcode_district'] ) ) ? $where['postcode_district'] : ( is_string( $where['postcode_district'] ) ? [$where['postcode_district']] : false );

				if( !empty( $postcodes_arr ) && is_array( $postcodes_arr ) ){
					$this->db->where_in( 'drp.postcode_district', $postcodes_arr );
				}
			}

			$query = $this->db->select( 'drp.*, dr.region_id', false )
				->join( 'diary_regions dr', 'dr.region_id = drp.region_id' )
				->order_by( 'LENGTH( drp.postcode_district ), drp.postcode_district' )
				->get( 'diary_region_postcodes drp' );

			if( $query->num_rows() > 0 ){
				$result = $query->result();
				$this->session->set_flashdata('message','Region Postcodes found');
			} else {
				$this->session->set_flashdata('message','No postcode areas found');
			}
		} else {
			$this->session->set_flashdata('message','You request is missing required information');
		}

		return $result;

	}


	/** Add people to a region **/
	public function assign_people( $account_id = false, $region_id = false, $postdata = false ){
		$result = false;
		if( !empty( $region_id ) && !empty( $postdata ) ){

			$postdata 		 			= convert_to_array( $postdata );
			$assigned_people			= !empty( $postdata['assigned_people'] ) ? $postdata['assigned_people'] : false;
			$assigned_people			= ( is_json( $assigned_people ) ) ? json_decode( $assigned_people ) : $assigned_people;
			$total = $invalid_person 	= [];

			if( !empty( $assigned_people ) ){
				foreach( $assigned_people as $k => $val ){

					$check_person_exists = $this->db->get_where( 'people', [ 'account_id'=>$account_id, 'person_id'=>$val ] )->row();

					if( !empty( $check_person_exists ) ){
						$data = [
							'region_id'=>$region_id,
							'person_id'=>$val,
							'account_id'=>$account_id
						];

						$check_exists = $this->db->limit(1)->get_where( 'people_assigned_regions', $data )->row();
						if( !$check_exists ){
							$this->db->insert( 'people_assigned_regions', $data );
							$rec_id = $this->db->insert_id();
						} else {
							$rec_id = $check_exists->id;
						}

						$data 	 = $this->get_assigned_people( $account_id, $region_id, [ 'id'=>$rec_id ] );
						$total[] = $data;
					} else {
						$invalid_person[] = $val;
					}

				}
			} else if( !empty( $postdata['person_id'] ) ) {

				$check_person_exists = $this->db->get_where( 'people', [ 'account_id'=>$account_id, 'person_id'=>$val ] )->row();

				if( !empty( $check_person_exists ) ){
					$data = [
						'region_id'=>$region_id,
						'person_id'=>$postdata['person_id'],
						'account_id'=>$account_id
					];

					$check_exists = $this->db->limit(1)->get_where( 'people_assigned_regions', $data )->row();
					if( !$check_exists ){
						$this->db->insert( 'people_assigned_regions', $data );
						$rec_id = $this->db->insert_id();
					} else {
						$rec_id = $check_exists->id;
					}

					$data 	 = $this->get_assigned_people( $account_id, $region_id, [ 'id'=>$rec_id ] );
					$total[] = $data;
				} else {
					$invalid_person[] = $postdata['person_id'];
				}

			}

			if( !empty( $total ) ){
				$result = $total;
				$this->session->set_flashdata('message','Regions assigned successfully');
			} else {
				$this->session->set_flashdata('message','The supplied person IDs are invalid');
			}
		} else {
			$this->session->set_flashdata('message','You request is missing required information');
		}
		return $result;
	}


	/** Get a list of all People assigned to a Region **/
	public function get_assigned_people( $account_id = false, $region_id = false, $where = false ){

		$result = false;

		if( !empty( $account_id ) ){

			$this->db->select( 'user.first_name, user.last_name, concat(user.first_name," ",user.last_name) `full_name`, par.person_id, dr.region_id, GROUP_CONCAT( DISTINCT( sb.`skill_name` ) SEPARATOR ", " ) AS `personal_skills`', false )
				->join( 'people p', 'p.person_id = par.person_id' )
				->join( 'user', 'p.person_id = user.id' )
				->join( 'people_skillset ps', 'ps.person_id = par.person_id', 'left' )
				->join( 'skills_bank sb', 'ps.skill_id = sb.skill_id', 'left' )
				->join( 'diary_regions dr', 'dr.region_id = par.region_id' );

			if( !empty( $where ) ){
				$where 		= convert_to_array( $where );
			}

			$region_id 	= ( !empty( $region_id ) ) ? $region_id : ( !empty( $where['region_id'] ) ? $where['region_id'] : false );

			if( !empty( $region_id ) ){
				$this->db->where( 'par.region_id', $region_id );
			}

			if( !empty( $where['person_id'] ) ){

				$people_arr	= ( is_array( $where['person_id'] ) ) ? $where['person_id'] : ( is_string( $where['person_id'] ) ? [$where['person_id']] : false );

				if( !empty( $people_arr ) && is_array( $people_arr ) ){
					$this->db->where_in( 'par.person_id', $people_arr );
				}

				if( !empty( $region_id ) ){
					$this->db->group_by( 'par.person_id' );
				} else {
					$this->db->group_by( 'par.region_id' );
				}

			} else {
				$this->db->group_by( 'par.person_id' );
			}

			if( !empty( $where['id'] ) ){
				$row = $this->db->get_where( 'people_assigned_regions par', [ 'par.account_id'=>$account_id, 'par.id'=>$where['id'] ] )->row();
				if( !empty( $row ) ){
					return $row;
				}
				return false;
			}

			if( !empty( $account_id ) ){
				$this->db->where( 'par.account_id', $account_id );
			}

			$query = $this->db->get( 'people_assigned_regions par' );

			if( $query->num_rows() > 0 ){
				$result = $query->result();
				$this->session->set_flashdata( 'message','Assigned people data found' );
			} else {
				$this->session->set_flashdata( 'message','No assigned people found' );
			}
		} else {
			$this->session->set_flashdata( 'message','You request is missing required information' );
		}

		return $result;

	}


	/** Un-assign people from a Regions **/
	public function unassign_people( $account_id = false, $region_id = false, $postdata = false ){
		$result = false;
		if( !empty( $region_id ) && !empty( $postdata ) ){

			$postdata 			= convert_to_array( $postdata );
			$assigned_people	= !empty( $postdata['assigned_people'] ) ? $postdata['assigned_people'] : false;
			$assigned_people	= ( is_json( $assigned_people ) ) ? json_decode( $assigned_people ) : $assigned_people;
			$deleted			= [];

			if( !empty( $assigned_people ) ){
				foreach( $assigned_people as $k => $val ){
					$data = [
						'region_id'=>$region_id,
						'person_id'=>$val
					];

					$check_exists = $this->db->limit(1)->get_where( 'people_assigned_regions', $data )->row();
					if( !empty( $check_exists ) ){
						$this->db->where( $data );
						$this->db->delete( 'people_assigned_regions' );
						$this->ssid_common->_reset_auto_increment( 'people_assigned_regions', 'id' );
					}
					$deleted[] = $data;
				}
			} else if( !empty( $postdata['person_id'] ) ) {
				$data = [
					'region_id'=>$region_id,
					'person_id'=>$postdata['person_id']
				];

				$check_exists = $this->db->limit(1)->get_where( 'people_assigned_regions', $data )->row();
				if( !empty( $check_exists ) ){
					$this->db->where( $data );
					$this->db->delete( 'people_assigned_regions' );
					$deleted[] = $data;
					$this->ssid_common->_reset_auto_increment( 'people_assigned_regions', 'id' );
				}
			}

			if( !empty( $deleted ) ){
				$result = $deleted;
				$this->session->set_flashdata('message','Person/Person un-assigned successfully');
			} else {
				$this->session->set_flashdata('message','No person/people were removed');
			}
		} else {
			$this->session->set_flashdata('message','You request is missing required information');
		}
		return $result;
	}


	/** Add a skill to people / person **/
	public function add_skilled_people( $account_id = false, $skill_id = false, $postdata = false ){
		$result = false;
		if( !empty( $skill_id ) && !empty( $postdata ) ){

			$postdata 		 		 = convert_to_array( $postdata );
			$skilled_people			 = !empty( $postdata['skilled_people'] )	? $postdata['skilled_people'] 	: false;
			$skilled_people			 = ( is_json( $skilled_people ) ) 			? json_decode( $skilled_people ): $skilled_people;
			$total = $invalid_person = [];

			if( !empty( $skilled_people ) ){
				foreach( $skilled_people as $k => $val ){

					$check_person_exists = $this->db->get_where( 'people', [ 'account_id'=>$account_id, 'person_id'=>$val ] )->row();

					if( !empty( $check_person_exists ) ){
						$data = [
							'skill_id'	=> $skill_id,
							'person_id'	=> $val,
							'account_id'=> $account_id
						];

						$check_exists = $this->db->limit(1)->get_where( 'people_skillset', $data )->row();
						if( !$check_exists ){
							$this->db->insert( 'people_skillset', $data );
							$rec_id = $this->db->insert_id();
						} else {
							$rec_id = $check_exists->id;
						}

						$data 	 = $this->get_skilled_people( $account_id, $skill_id, [ 'id'=>$rec_id ] );
						$total[] = $data;
					} else {
						$invalid_person[] = $val;
					}

				}
			} else if( !empty( $postdata['person_id'] ) ) {

				$check_person_exists = $this->db->get_where( 'people', [ 'account_id'=>$account_id, 'person_id'=>$val ] )->row();

				if( !empty( $check_person_exists ) ){
					$data = [
						'skill_id'	=>$skill_id,
						'person_id'	=>$postdata['person_id'],
						'account_id'=>$account_id
					];

					$check_exists = $this->db->limit(1)->get_where( 'people_skillset', $data )->row();
					if( !$check_exists ){
						$this->db->insert( 'people_skillset', $data );
						$rec_id = $this->db->insert_id();
					} else {
						$rec_id = $check_exists->id;
					}

					$data 	 = $this->get_skilled_people( $account_id, $skill_id, [ 'id'=>$rec_id ] );
					$total[] = $data;
				} else {
					$invalid_person[] = $postdata['person_id'];
				}

			}

			if( !empty( $total ) ){
				$result = $total;
				$this->session->set_flashdata('message','Skills assigned successfully');
			} else {
				$this->session->set_flashdata('message','The supplied person IDs are invalid');
			}
		} else {
			$this->session->set_flashdata('message','You request is missing required information');
		}
		return $result;
	}


	/** Get a list of all People assigned to a Skill **/
	public function get_skilled_people( $account_id = false, $skill_id = false, $where = false ){

		$result = false;

		if( !empty( $account_id ) ){

			$this->db->select( 'user.first_name, user.last_name, concat( user.first_name," ",user.last_name ) `full_name`, pss.person_id, pca.address_line1, pca.address_line2, pca.address_town, pca.address_postcode', false )
				->join( 'people p', 'p.person_id = pss.person_id' )
				->join( 'user', 'p.person_id = user.id' )
				->join( 'skills_bank sb', 'pss.skill_id = sb.skill_id', 'left' )
				->join( 'people_contact_addresses pca', 'pca.person_id = user.id', 'left' )
				->where( 'pca.archived !=', 1 );

			if( !empty( $where ) ){
				$where 		= convert_to_array( $where );
			}

			$skill_id 	= ( !empty( $skill_id ) ) ? $skill_id : ( !empty( $where['skill_id'] ) ? $where['skill_id'] : false );

			if( !empty( $skill_id ) ){
				
				if( is_array( $skill_id ) ){
					$this->db->where_in( 'pss.skill_id', $skill_id );
				} else {
					$this->db->where( 'pss.skill_id', $skill_id );
				}				
			}

			if( !empty( $where['person_id'] ) ){

				$people_arr	= ( is_array( $where['person_id'] ) ) ? $where['person_id'] : ( is_string( $where['person_id'] ) ? [$where['person_id']] : false );

				if( !empty( $people_arr ) && is_array( $people_arr ) ){
					$this->db->where_in( 'pss.person_id', $people_arr );
				}

				if( !empty( $skill_id ) ){
					$this->db->group_by( 'pss.person_id' );
				} else {
					$this->db->group_by( 'pss.skill_id' );
				}

			} else {
				$this->db->group_by( 'pss.person_id' );
			}

			if( !empty( $where['id'] ) ){
				$row = $this->db->get_where( 'people_skillset pss', [ 'pss.account_id'=>$account_id, 'pss.id'=>$where['id'] ] )->row();
				if( !empty( $row ) ){
					return $row;
				}
				return false;
			}

			if( !empty( $account_id ) ){
				$this->db->where( 'pss.account_id', $account_id );
			}

			if( isset( $where['grouped'] ) ){
				
				if( !empty( $where['grouped'] ) ){
					$grouped = 1;
				}
				unset( $where['grouped'] );
			}
			
			$this->db->order_by( 'pca.address_postcode' );
			
			$query = $this->db->get( 'people_skillset pss' );

			if( $query->num_rows() > 0 ){
				if( !empty( $grouped ) ){
					$data = [];
					foreach( $query->result() as $key => $row ){
						$row->person			= ucwords( strtolower( $row->first_name.' '.$row->last_name ) );
						$row->home_postcode		= strtoupper( $row->address_postcode);
						$row->home_address		= ucwords( strtolower( $row->address_line1.', '.$row->address_town ) ).', '.strtoupper( $row->address_postcode);
						$row->personal_skills	= is_array( $skill_id ) ? $skill_id : [$skill_id];						
						$data[$row->person_id] = $row;
					}
					$result = $data;
				} else {
					$result = $query->result();
				}
				
				$this->session->set_flashdata( 'message','Skilled people data found' );
			} else {
				$this->session->set_flashdata( 'message','No skilled people found' );
			}
		} else {
			$this->session->set_flashdata( 'message','You request is missing required information' );
		}

		return $result;

	}

	/** Un-assign a skill from a person / people **/
	public function remove_skilled_people( $account_id = false, $skill_id = false, $postdata = false ){
		$result = false;
		if( !empty( $skill_id ) && !empty( $postdata ) ){

			$postdata 			= convert_to_array( $postdata );
			$skilled_people	= !empty( $postdata['skilled_people'] ) ? $postdata['skilled_people'] : false;
			$skilled_people	= ( is_json( $skilled_people ) ) ? json_decode( $skilled_people ) : $skilled_people;
			$deleted			= [];

			if( !empty( $skilled_people ) ){
				foreach( $skilled_people as $k => $val ){
					$data = [
						'skill_id'=>$skill_id,
						'person_id'=>$val
					];

					$check_exists = $this->db->limit(1)->get_where( 'people_skillset', $data )->row();
					if( !empty( $check_exists ) ){
						$this->db->where( $data );
						$this->db->delete( 'people_skillset' );
						$this->ssid_common->_reset_auto_increment( 'people_skillset', 'id' );
					}
					$deleted[] = $data;
				}
			} else if( !empty( $postdata['person_id'] ) ) {
				$data = [
					'skill_id'=>$skill_id,
					'person_id'=>$postdata['person_id']
				];

				$check_exists = $this->db->limit(1)->get_where( 'people_skillset', $data )->row();
				if( !empty( $check_exists ) ){
					$this->db->where( $data );
					$this->db->delete( 'people_skillset' );
					$deleted[] = $data;
					$this->ssid_common->_reset_auto_increment( 'people_skillset', 'id' );
				}
			}

			if( !empty( $deleted ) ){
				$result = $deleted;
				$this->session->set_flashdata( 'message','Skill removed from Person/Person successfully' );
			} else {
				$this->session->set_flashdata( 'message','No person/people were removed' );
			}
		} else {
			$this->session->set_flashdata( 'message','You request is missing required information' );
		}
		return $result;
	}


	/** Create Availability **/
	public function create_diary_resource( $account_id = false, $postdata = false ){
		$result = false;
		if( !empty( $account_id ) ){
			$postdata = convert_to_array( $postdata );
			$resource = !empty( $postdata['resource'] ) ? $postdata['resource'] : false;
			$resource = is_json( $resource ) ? json_decode( $resource ) : ( is_object( $resource ) ? object_to_array( $resource ) : $resource );
			if( !empty( $resource ) ){
				$processed = [];
				foreach( $resource as $person_id => $resource_data ){
					$check_person_exists = $this->db->get_where( 'people', [ 'account_id'=>$account_id, 'person_id'=>$person_id ] )->row();
					if( !empty( $check_person_exists ) ){
						$diary_schedules = $this->_generate_diary_schedule( $account_id, $person_id, $resource_data );
						if( !empty( $diary_schedules ) ){
							$processed[$person_id]['new-schedules'] 	= !empty( $diary_schedules['new-schedules'] ) 		? $diary_schedules['new-schedules'] : 0;
							$processed[$person_id]['updated-schedules'] = !empty( $diary_schedules['updated-schedules'] ) 	? $diary_schedules['updated-schedules'] : 0;
						}
					}
				}
				$this->session->set_flashdata('message','Diary resource added successfully');
				$result = $processed;
			} else {
				$this->session->set_flashdata('message','You request is diary resource information');
			}
		} else {
			$this->session->set_flashdata('message','You request is missing required information');
		}
		return $result;
	}

	/** Generate schedules **/
	public function _generate_diary_schedule( $account_id = false, $person_id = false, $postdata = false ){
		$result = [];
		if( !empty( $account_id ) && !empty( $person_id )  && !empty( $postdata ) ){
			$postdata		 = ( is_object( $postdata ) ) ? object_to_array( $postdata ) : $postdata;
			$week_start_date = date( "Y-m-d", strtotime( $postdata['week_beginning'] ) ); //Get the start_time date.
			$weeks_counter 	 = 1;
			$data 			 = [];
			$weeks_ahead 	 = intval( $postdata['weeks_ahead'] );
			$lunch_allowance = ( !empty( $postdata['lunch_allowance'] ) ) ? date( 'H:i:s', strtotime( $postdata['lunch_allowance'] ) ) : '00:00:00';
			$break_allowance = ( !empty( $postdata['break_allowance'] ) ) ? date( 'H:i:s', strtotime( $postdata['break_allowance'] ) ) : '00:00:00';

			## PROCESS BASED ON NUMBER OF WEEKS AHEAD
			while( $weeks_counter <= $weeks_ahead ){

				## PREPARE ROTA RECORDS FOR DB
				foreach( $postdata['shift_times'] as $day_counter => $shift_times ){
					if( !empty( $shift_times['start_time'] ) && !empty( $shift_times['finish_time'] ) ){
						$day_counter--;
						$ref_date = date( "Y-m-d", strtotime( $week_start_date. "+".$day_counter." days" ) );
						$day_counter++;

						## Reference Data
						$reference_data = [
							'person_id'		 => $person_id,
							'ref_date'		 => $ref_date,
							'shift_times'	 => [
								'start_time' => $postdata['shift_times'][$day_counter]['start_time'],
								'finish_time'=> $postdata['shift_times'][$day_counter]['finish_time']
							]
						];

						## Lunch and Break allowance
						$start_time				= ( !empty( $postdata['shift_times'][$day_counter]['start_time'] ) )  ? date( 'H:i:s', strtotime( $postdata['shift_times'][$day_counter]['start_time'] ) )  : '00:00:00';
						$finish_time			= ( !empty( $postdata['shift_times'][$day_counter]['finish_time'] ) ) ? date( 'H:i:s', strtotime( $postdata['shift_times'][$day_counter]['finish_time'] ) ) : '';
						$base_hours				= date( 'H:i:s', $this->_cal_time_diff( $start_time, $finish_time ) );
						$actual_hours			= date( 'H:i:s', $this->_cal_time_diff( $lunch_allowance, $base_hours ) );
						$actual_hours			= date( 'H:i:s', $this->_cal_time_diff( $break_allowance, $actual_hours ) );

						$data[]  = array(
							'account_id' 		=> $account_id,
							'user_id'			=> $person_id,
							'ref_date'			=> $ref_date,
							'day'				=> date( 'l', strtotime( $ref_date ) ),
							'start_time'		=> $start_time,
							'finish_time'		=> $finish_time,
							'lunch_allowance'	=> ( !empty( $lunch_allowance ) ) ? ( $lunch_allowance ) 	: '00:00:00',
							'break_allowance'	=> ( !empty( $break_allowance ) ) ? $break_allowance 		: '00:00:00',
							'base_hours'		=> $base_hours,
							'actual_hours'		=> $actual_hours,
							'base_slots'		=> $this->_convert_to_float( $base_hours ),
							'actual_slots'		=> $this->_convert_to_float( $actual_hours ),
							'created_by'		=> $this->ion_auth->_current_user->id,
							'schedule_type'		=> ( !empty( $postdata['schedule_type'] ) ) ? ucwords( $postdata['schedule_type'] ) : 'Working time',
							'schedule_ref'		=> $this->_generate_schedule_ref( $account_id, $person_id, $reference_data )
						);

					}
				}

				$week_start_date = date( 'Y-m-d',strtotime( $postdata['week_beginning'] . '+' . $weeks_counter.' weeks' ) );
				$weeks_counter++;
			}

			## CHECK IF TO UPDATE OR ADD
			$resources_to_add = $resources_to_update = [];

			if( !empty( $data ) ){

				foreach( $data as $k => $resource_record ){
					$conditions = [ 'account_id'=>$account_id, 'user_id'=>$resource_record['user_id'], 'ref_date'=>$resource_record['ref_date'] ];
					$query = $this->db->where( $conditions )
						->where('archived !=', 1 )
						->get( 'diary_resource_schedule' );

					if( $query->num_rows() > 0 ){
						$row 								= $query->result_array()[0];
						$resource_record['resource_id'] 	= $row['resource_id'];
						$resource_record['last_modified_by']= $this->session->userdata( 'id' );
						$resources_to_update[$k] 			= $resource_record;
					}else{
						#$resource_record['base_hours'] 	= abs( ( strtotime( $resource_record['finish_time'] ) - strtotime( $resource_record['start_time'] ) ) / 3600 );
						$resources_to_add[$k] 	 		= $resource_record;
					}
				}

				## BATCH INSERT/UPDATE
				if( count( $resources_to_add ) > 0 ){
					$this->db->insert_batch( 'diary_resource_schedule', $resources_to_add );
					$result['new-schedules'] 	= ( $this->db->affected_rows() > 0 ) ? count( $resources_to_add ) : 0;
				}

				if( count( $resources_to_update ) > 0 ){

					$this->db->update_batch( 'diary_resource_schedule', $resources_to_update, 'resource_id' );
					$result['updated-schedules']= ( $this->db->affected_rows() > 0 || ( $this->db->trans_status() !== false ) ) ? count( $resources_to_update ) : 0;
				}
			}
		}
		return $result;
	}


	/** Convert time to hours as a decimal **/
	private function _convert_to_float( $time_str = false ){
		if( !empty( $time_str ) ){
			$time_data 	= explode( ':', $time_str );
			$hrs 		= !empty( $time_data[0] ) ? abs( $time_data[0]) : '0';
			$mins 		= !empty( $time_data[1] ) ? ( ( $time_data[1] ) /60 ) : '0';
			$time_str	= $hrs + $mins;
		}
		return $time_str;
	}

	/** Time Difference **/
	private function _cal_time_diff( $time_1 = false, $time_2 = false ){
		date_default_timezone_set( 'UTC' );
		if( !empty( $time_1 ) && !empty( $time_2 ) ){
			$result = strtotime( $time_2 ) - strtotime( $time_1 );
		}
		return ( $result ) ? $result : false;
	}


	/** Generate a unique schedule ref**/
	private function _generate_schedule_ref( $account_id = false, $person_id = false, $data = false ){
		$result 			= 'DRS.'.$account_id;
		if( !empty( $account_id ) && !empty( $person_id )  && !empty( $data ) ){
			$ref_date 		= !empty( $data['ref_date'] )					? '.'.date( 'ymd', strtotime( $data['ref_date'] ) ) : '';
			$start_time		= !empty( $data['shift_times']['start_time'] )  ? '.'.date( 'Hi', strtotime( $data['shift_times']['start_time'] ) )  : '';
			$finish_time	= !empty( $data['shift_times']['finish_time'] ) ? '.'.date( 'Hi', strtotime( $data['shift_times']['finish_time'] ) ) : '';
			$result 	   .= $ref_date.$start_time.$finish_time.'.'.$person_id;
		}
		return $result;
	}


	/**
	* Get Available diary resource
	**/
	public function get_available_resource( $account_id = false, $where = false , $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;
		if( !empty( $account_id ) ){
			
			$this->load->model( 'serviceapp/Job_model','job_service' );

			$where 					= convert_to_array( $where );
			$associated_user_id 	= $user_ids = false;

			if( !empty( $where ) ){
				if( !empty( $where['associated_user_id'] ) ){
					$associated_user_id = $where['associated_user_id'];
					unset( $where['associated_user_id'] );
					$helper_query = $this->db->get_where( "associated_users", ["account_id" => $account_id, "primary_user_id" => $associated_user_id] )->result_array();
					if( !empty( $helper_query ) ){
						$user_ids = array_column( $helper_query, 'user_id' );
						if( !empty( $user_ids ) ){
							$user_ids[] = $associated_user_id;
						}
					}
				}
				
				
				## CHECK FOR JOB TYPE
				$skilled_person_ids 		= [];
				if( !empty( $where['job_type_id'] ) ){

					## get those who can do the job
					$skilled_personnel 			= $this->job_service->get_required_skills( $account_id, $where['job_type_id'], [ 'people_skills'=> 1 ] );

					$skilled_person_ids 		= ( !empty( $skilled_personnel ) ) ? array_keys( $skilled_personnel ) : false;
					unset( $where['job_type_id'] );
				}
				
				## check for the region
				$engineer_ids_by_region 	= [];
				if( !empty( $where['region_id'] ) ){

					$region_id 					= $where['region_id'];
					
					$this->db->select( "person_id", false );
					$this->db->where( "account_id", $account_id );
					$this->db->where( "region_id", $region_id );
					$query_reg = $this->db->get( "people_assigned_regions" )->result_array();
					
					$engineer_ids_by_region 	= array_map( function( $value ){ return $value['person_id']; }, $query_reg );
					
					unset( $where['region_id'] );
				}
			}

			if( !empty( $where['period_days'] ) ){
				$period_days= ( int ) $where['period_days'];
				$date_from 	= date( 'Y-m-d' );
				$date_to 	= date( 'Y-m-d', strtotime( $date_from.' + '.$period_days.' days' ) );
			} else if( $where['date_from'] ){
				$date_from 	= date( 'Y-m-d', strtotime( $where['date_from'] ) );
				$date_to 	= ( !empty( $where['date_to'] ) ) ? date( 'Y-m-d',strtotime( $where['date_to'] ) ) : date('Y-m-d');
			}else if( $where['ref_date'] ){
				$date_from 	= $date_to 	= date( 'Y-m-d',strtotime( $where['ref_date'] ) );
			}

			if( !empty( $where['weekly_view'] ) ){
				$weekly_view 	= true;
				$start_of_week 	= get_start_of_week_date( $date_from );
			}

			if( !empty( $order_by ) ){
				$this->db->order_by( $order_by  );
			} else {
				$this->db->order_by( 'ref_date ASC' );
			}

			if( !empty( $date_from ) && !empty( $date_to ) ){
				$datetime_1 = date_create( $date_from );
				$datetime_2 = date_create( $date_to );
				$interval   = date_diff( $datetime_1, $datetime_2 );

				if( abs( $interval->format( '%R%a' ) ) > $this->default_days_view ){
					# Force
					$date_to = date( 'Y-m-d', strtotime( $date_from.' + '.$this->default_days_view.' days' ) );
				}
			}

			$this->db->select( 'user_id, resource_id, account_id, ref_date, day, start_time, finish_time, lunch_allowance, break_allowance, base_hours, actual_hours, base_slots, actual_slots, consumed_slots', false );
			
			if( !empty( $date_from ) ){
				$this->db->where( 'ref_date >=', $date_from );
			}
			
			if( !empty( $date_to ) ){
				$this->db->where( 'ref_date <=', $date_to );
			}
			
			$this->db->where( 'account_id', $account_id );
			$this->db->where( 'archived !=', 1 );
			if( !empty( $user_ids ) ){
				$this->db->where_in( "user_id", $user_ids );
			}
			
			if( !empty( $skilled_person_ids ) ){
				$this->db->where_in( "user_id", $skilled_person_ids );
			}
			
			if( !empty( $engineer_ids_by_region ) ){
				$this->db->where_in( "user_id", $engineer_ids_by_region );
			}
			
			$query = $this->db->get( 'diary_resource_schedule' );

			if( $query->num_rows() > 0 ){
				
				if( !empty( $weekly_view ) ){
					$skilled_personnel = ( !empty( $skilled_personnel ) ) ? $skilled_personnel : false;
					$data 	= $this->get_resource_by_weeks( $account_id, $query->result(), [ 'start_of_week'=>$start_of_week, 'skilled_personnel'=>$skilled_personnel, 'no_of_weeks'=>4 ] );
					$result = $data;

				} else if( !empty( $skilled_personnel ) ){
					$data = [];
					foreach( $query->result() as $k => $row ){
						$booked_jobs 					= $this->job_service->get_jobs_by_status( $account_id, false, [ 'job_date'=>$row->ref_date, 'assigned_to'=>$row->user_id, 'customer_details' => false ] );
						
						$available_resource			 	= $skilled_personnel[$row->user_id];
						
						$row->booked_jobs				= !empty( $booked_jobs->records ) ? $booked_jobs->records : ( !empty( $booked_jobs ) ? $booked_jobs : null );
						$actual_booked_slots			= !empty( $row->booked_jobs ) ? ( array_sum( array_column( $row->booked_jobs, 'job_duration' ) ) ) : 0;
						$row->booked_slots				= (string) $actual_booked_slots;
						
						$booked_postcodes				= !empty( $row->booked_jobs ) ? ( array_column( $row->booked_jobs, 'address_postcode' ) ) : [];
						$row->booked_postcodes			= $booked_postcodes;
						
						$booked_postcode_districts		= !empty( $row->booked_jobs ) ? ( array_column( $row->booked_jobs, 'postcode_district' ) ) : [];
						$row->booked_postcode_districts= $booked_postcode_districts;
						
						$available_resource['availability'] 	= $row;
						$data[$row->ref_date][$row->user_id] 	= $available_resource;
					}

					$result = $data;
				} else {
					$data 	= [];
					foreach( $query->result() as $k => $row ){
						$user 				= $this->get_user_address_details( $account_id, $row->user_id );
						$home_address		= '';
						$home_address		.= !empty( $user->address_line1 ) ? $user->address_line1.', ' : '';
						$home_address		.= !empty( $user->address_town ) ? $user->address_town.', ' : '';
						$home_address		.= !empty( $user->address_postcode ) ? $user->address_postcode : '';

						$booked_jobs 				= $this->job_service->get_jobs_by_status( $account_id, false, [ 'job_date'=>$row->ref_date, 'assigned_to'=>$row->user_id, 'customer_details' => 'yes' ] );
						$row->booked_jobs			= !empty( $booked_jobs->records ) ? $booked_jobs->records : ( !empty( $booked_jobs ) ? $booked_jobs : null );
						
						$actual_booked_slots		= !empty( $row->booked_jobs ) ? ( array_sum( array_column( $row->booked_jobs, 'job_duration' ) ) ) : 0;
						$row->booked_slots			= (string) $actual_booked_slots;
						
						$booked_postcodes			= !empty( $row->booked_jobs ) ? ( array_column( $row->booked_jobs, 'address_postcode' ) ) : [];
						$row->booked_postcodes		= $booked_postcodes;
						
						$booked_postcode_districts	= !empty( $row->booked_jobs ) ? ( array_column( $row->booked_jobs, 'postcode_district' ) ) : [];
						$row->booked_postcode_districts= $booked_postcode_districts;

						$available_resource = [
							'person' 			=> ( $user->first_name.' '.$user->last_name ),
							'person_id' 		=> $row->user_id,
							'home_postcode' 	=> !empty( $user->address_postcode ) ? $user->address_postcode : '',
							'home_address' 		=> $home_address,
							'personal_skills' 	=> null,
							'availability' 		=> $row,
						];
						$data[$row->ref_date][$row->user_id] 	= $available_resource;
					}
					$result = $data;
				}

				if( !empty( $result ) ){
					$this->session->set_flashdata( 'message', 'Resource availability found' );
				} else {
					$this->session->set_flashdata( 'message', 'No resource available for the supplied criteria' );
				}

			} else {
				$this->session->set_flashdata( 'message', 'No resource available for the supplied criteria' );
			}
		}
		return $result;
	}


	/** Load Data by Week **/
	public function get_resource_by_weeks( $account_id = false, $resource_data = false, $where = false ){
		$result = false;
		$where	= convert_to_array( $where );
		if( !empty( $account_id ) && !empty( $resource_data ) && !empty( $where['start_of_week'] ) ){
			$no_of_weeks	= ( !empty( $where['no_of_weeks'] ) && $where['no_of_weeks'] > $this->max_no_weeks_view ) ? $this->max_no_weeks_view : ( ( !empty( $where['no_of_weeks'] ) ) ? $where['no_of_weeks'] : $this->max_no_weeks_view );
			
			$i 				= 1;
			while( $i <= $no_of_weeks ) {
				$week_no 		= 'week_'.$i;
				$data[$week_no]	= [ 'monday'=>[], 'tuesday'=>[], 'wednesday'=>[], 'thursday'=>[], 'friday'=>[], 'saturday'=>[], 'sunday'=>[] ];
				$i++;
			}

			$skilled_personnel   = !empty( $where['skilled_personnel'] ) ? $where['skilled_personnel'] : false;

			$start_of_week  = date( 'Y-m-d', strtotime( $where['start_of_week'] ) );
			$end_of_week	= date( 'Y-m-d', strtotime( $start_of_week.' + 6 days' ) );

			$week_1_start	= $start_of_week;
			$week_1_end		= $end_of_week;

			$week_2_start	= date( 'Y-m-d', strtotime( $week_1_start.' + 7 days' ) );
			$week_2_end		= date( 'Y-m-d', strtotime( $week_2_start.' + 6 days' ) );

			$week_3_start	= date( 'Y-m-d', strtotime( $week_2_start.' + 7 days' ) );
			$week_3_end		= date( 'Y-m-d', strtotime( $week_3_start.' + 6 days' ) );

			$week_4_start	= date( 'Y-m-d', strtotime( $week_3_start.' + 7 days' ) );
			$week_4_end		= date( 'Y-m-d', strtotime( $week_4_start.' + 6 days' ) );

			foreach( $resource_data as $k => $row ){

				$weekday 	= $this->get_weekday( $row->ref_date );

				if( !empty( $skilled_personnel ) ){
					$booked_jobs 					= $this->job_service->get_jobs_by_status( $account_id, false, [ 'job_date'=>$row->ref_date, 'assigned_to'=>$row->user_id, 'customer_details' => 'yes' ] );
					$available_resource			 	= $skilled_personnel[$row->user_id];
					$row->booked_jobs				= !empty( $booked_jobs->records ) ? $booked_jobs->records : ( !empty( $booked_jobs ) ? $booked_jobs : null );
					$available_resource['availability'] = $row;
				} else {
					$user 				= $this->get_user_address_details( $account_id, $row->user_id );
					$home_address		= '';
					$home_address		.= !empty( $user->address_line1 ) ? $user->address_line1.', ' : '';
					$home_address		.= !empty( $user->address_town ) ? $user->address_town.', ' : '';
					$home_address		.= !empty( $user->address_postcode ) ? $user->address_postcode : '';

					$booked_jobs 		= $this->job_service->get_jobs_by_status( $account_id, false, [ 'job_date'=>$row->ref_date, 'assigned_to'=>$row->user_id, 'customer_details' => 'yes' ] );
					$row->booked_jobs	= !empty( $booked_jobs->records ) ? $booked_jobs->records : ( !empty( $booked_jobs ) ? $booked_jobs : null );
					$available_resource = [
						'person' 			=> ( $user->first_name.' '.$user->last_name ),
						'person_id' 		=> $row->user_id,
						'home_postcode' 	=> !empty( $user->address_postcode ) ? $user->address_postcode : '',
						'home_address' 		=> $home_address,
						'personal_skills' 	=> null,
						'availability' 		=> $row,
					];
				}

				switch( $row->ref_date ){
					case ( $row->ref_date >= $week_1_start && $row->ref_date <= $week_1_end ):
						//Week 1
						$data['week_1'][strtolower( $weekday )][$row->ref_date][$row->user_id] = $available_resource;
						break;

					case ( $row->ref_date >= $week_2_start && $row->ref_date <= $week_2_end ):
						//Week 2
						$data['week_2'][strtolower( $weekday )][$row->ref_date][$row->user_id] = $available_resource;
						break;

					case ( $row->ref_date >= $week_3_start && $row->ref_date <= $week_3_end ):
						//Week 3
						$data['week_3'][strtolower( $weekday )][$row->ref_date][$row->user_id] = $available_resource;
						break;

					case ( $row->ref_date >= $week_4_start && $row->ref_date <= $week_4_end ):
						//Week 4
						$data['week_4'][strtolower( $weekday )][$row->ref_date][$row->user_id] = $available_resource;
						break;

				}

			}
			$result = $data;
		}
		return $result;
	}

	/** Get Day of the Week **/
	public 	function get_weekday( $date = null ){
		return date( 'l', strtotime( $date ) );
	}

	/** Get operatives' address details **/
	public function get_user_address_details( $account_id = false, $user_id = false ){
		$result = false;
		if( !empty( $account_id ) ){
			$this->db->select( 'u.id `person_id`, u.first_name, u.last_name, concat( u.first_name," ",u.last_name) `full_name`, pca.address_line1, pca.address_line2, pca.address_town, pca.address_postcode' )
				->join( 'people_contact_addresses pca', 'pca.person_id = u.id', 'left' )
				->group_by( 'u.id' );

				if( !empty( $user_id ) ){
					$this->db->where( 'u.id', $user_id );
				}

			$query = $this->db->get( 'user u' );
			if( $query->num_rows() > 0 ){
				if( !empty( $user_id ) ){
					$result = $query->result()[0];
				} else {
					$result = $query->result();
				}
				$this->session->set_flashdata('message','User address details data found');
			} else {
				$this->session->set_flashdata('message','No data found');
			}
		} else {
			$this->session->set_flashdata('message','Your request is missing required information');
		}
		return $result;
	}


	/** Remove a district from a Regions **/
	public function remove_region_postcodes( $account_id = false, $region_id = false, $postdata = false ){
		$result = false;
		if( !empty( $region_id ) && !empty( $postdata ) ){

			$postdata 			= convert_to_array( $postdata );
			$assigned_district	= !empty( $postdata['assigned_district'] ) ? $postdata['assigned_district'] : false;
			$assigned_district	= ( is_json( $assigned_district ) ) ? json_decode( $assigned_district ) : $assigned_district;
			$deleted			= [];

			if( !empty( $assigned_district ) ){
				foreach( $assigned_district as $k => $val ){
					$data = [
						'region_id'			=>$region_id,
						'postcode_district'	=>$val
					];

					$check_exists = $this->db->limit(1)->get_where( 'diary_region_postcodes', $data )->row();
					if( !empty( $check_exists ) ){
						$this->db->where( $data );
						$this->db->delete( 'diary_region_postcodes' );
						$this->ssid_common->_reset_auto_increment( 'diary_region_postcodes', 'id' );
					}
					$deleted[] = $data;
				}
			} else if( !empty( $postdata['postcode_district'] ) ) {
				$data = [
					'region_id'			=>$region_id,
					'postcode_district'	=>$postdata['postcode_district']
				];

				$check_exists = $this->db->limit(1)->get_where( 'diary_region_postcodes', $data )->row();
				if( !empty( $check_exists ) ){
					$this->db->where( $data );
					$this->db->delete( 'diary_region_postcodes' );
					$deleted[] = $data;
					$this->ssid_common->_reset_auto_increment( 'diary_region_postcodes', 'id' );
				}
			}

			if( !empty( $deleted ) ){
				$result = $deleted;
				$this->session->set_flashdata('message','Postcode area remove successfully');
			} else {
				$this->session->set_flashdata('message','No Postcode area(s) were removed');
			}
		} else {
			$this->session->set_flashdata('message','You request is missing required information');
		}
		return $result;
	}


	/** Search Address [Postcode] Regions **/
	public function get_address_regions( $account_id = false, $search_term = false, $where = false, $limit = 2000, $offset = DEFAULT_OFFSET ){
		$result = false;

		if( !empty( $account_id ) ){

			if( !empty( $search_term ) ){
				$search_term  = trim( urldecode( $search_term ) );
				$search_term  = str_replace( '.', ',', $search_term );
				$search_where = [];
				if( strpos( $search_term, ',') !== false ) {
					$multiple_terms = explode( ',', $search_term );
					foreach( $multiple_terms as $term ){
						if( !empty( $term ) ){
							foreach( $this->address_regions_search_fields as $k=>$field ){
								$search_where[$field] = trim( $term );
							}
							$where_combo = format_like_to_where( $search_where );
							$this->db->or_where( $where_combo );
						}
					}
				} else if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						if( !empty( $term ) ){
							foreach( $this->address_regions_search_fields as $k=>$field ){
								$search_where[$field] = trim( $term );
							}
							$where_combo = format_like_to_where( $search_where );
							$this->db->where( $where_combo );
						}
					}
				} else {

					foreach( $this->address_regions_search_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( !empty( $where ) ){

				$where = convert_to_array( $where );

				if( isset( $where['postcode_district'] ) ){
					if( !empty( $where['postcode_district'] ) ){
						$this->db->where( 'addr.postcode_district', $where['postcode_district'] );
					}
					unset( $where['postcode_district'] );
				}

			}


			if( $limit > 0 ){
				$this->db->limit( $limit, $offset );
			}

			$query = $this->db->select( 'postcode_area, postcode_district, posttown, county', false )
				->order_by( 'LENGTH( addr.postcode_district ), addr.postcode_district' )
				->group_by( 'addr.postcode_district' )
				->get( 'addresses_postcode_regions addr' );

			if( $query->num_rows() > 0 ){
				$this->session->set_flashdata( 'message','Postcode areas data found.' );
				$result = $query->result();
			} else {
				$this->session->set_flashdata( 'message','Postcode areas data not found.' );
			}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information.' );
		}

		return $result;
	}


	/** 
	*	Read Availability Details
	**/
	public function get_diary_resource( $account_id = false, $where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET, $order_by = false ){
		$result = false;
		
		if( !empty( $account_id ) ){
			
			if( !empty( $where ) ){
				$where = convert_to_array( $where );
				if( !empty( $where ) ){
					if( !empty( $where['resource_id'] ) ){
						$resource_id = $where['resource_id'];
						$this->db->where( "drs.resource_id", $resource_id );
						unset( $where['resource_id'] );
					}
					
					if( !empty( $where['resource_date'] ) ){
						$resource_date = $where['resource_date'];
						$this->db->where( 'drs.ref_date', $resource_date );
						unset( $where['resource_date'] );
					}
					
					if( !empty( $where['user_id'] ) ){
						$user_id = $where['user_id'];
						$this->db->where( 'drs.user_id', $user_id );
						unset( $where['user_id'] );
					}

					if( !empty( $where ) ){
						$this->db->where( $where );
					}
					
				}
			}
			
			$this->db->select( "drs.*", false );
			$this->db->select( "p.preferred_name", false ); ## just prepared the link to the people table if needed
			$this->db->select( "pc.category_name_alt", false );

			$this->db->join( "people `p`", "drs.user_id = p.user_id", "left" );
			$this->db->join( "people_categories `pc`", "pc.category_id = p.category_id", "left" );
			
			$arch_where = "( drs.archived != 1 or drs.archived is NULL )";
			$this->db->where( $arch_where );
			$this->db->where( 'drs.account_id', $account_id );
			
			
			if( $limit > 0 ){
				$this->db->limit( $limit, $offset );
			}
			
			if( !empty( $order_by ) ){
				$this->db->order_by( $order_by );
			}
			$query = $this->db->get( "diary_resource_schedule `drs`" );
			
			if( $query->num_rows() > 0 ){
				$result = $query->result();
				$this->session->set_flashdata( 'message', 'Resource(s) found.' );
			} else {
				$this->session->set_flashdata( 'message', 'Resource(s) not found.' );
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information.' );
		}
		
		return $result;
	}



	/**
	* 	Update single diary resource
	**/
	public function update_resource( $account_id = false, $resource_id = false , $resource_data = false ){
		$result = false;
		if( !empty( $account_id ) && ( !empty( $resource_id ) ) && !empty( $resource_data ) ){

			$resource_data		 = ( is_object( $resource_data ) ) ? object_to_array( $resource_data ) : $resource_data;
			
			if( !empty( $resource_data['user_id'] ) ){
				$person_exists = $this->db->get_where( 'people', [ 'account_id'=>$account_id, 'person_id'=>$resource_data['user_id'], 'is_active'=>1 ] )->row();
				
				if( $person_exists ){
					$lunch_allowance 		= ( !empty( $postdata['lunch_allowance'] ) ) ? date( 'H:i:s', strtotime( $postdata['lunch_allowance'] ) ) : '00:00:00';
					$break_allowance 		= ( !empty( $postdata['break_allowance'] ) ) ? date( 'H:i:s', strtotime( $postdata['break_allowance'] ) ) : '00:00:00';
					$base_hours				= date( 'H:i:s', $this->_cal_time_diff( $resource_data['start_time'], $resource_data['finish_time'] ) );
					$actual_hours			= date( 'H:i:s', $this->_cal_time_diff( $lunch_allowance, $base_hours ) );
					$actual_hours			= date( 'H:i:s', $this->_cal_time_diff( $break_allowance, $actual_hours ) );
					
					$data  = array(
						'account_id' 		=> $account_id,
						'user_id'			=> $person_exists->person_id,
						'ref_date'			=> $resource_data['ref_date'],
						'day'				=> date( 'l', strtotime( $resource_data['ref_date'] ) ),
						'start_time'		=> $resource_data['start_time'],
						'finish_time'		=> $resource_data['finish_time'],
						'lunch_allowance'	=> ( !empty( $lunch_allowance ) ) ? ( $lunch_allowance ) 	: '00:00:00',
						'break_allowance'	=> ( !empty( $break_allowance ) ) ? $break_allowance 		: '00:00:00',
						'base_hours'		=> $base_hours,
						'actual_hours'		=> $actual_hours,
						'base_slots'		=> $this->_convert_to_float( $base_hours ),
						'actual_slots'		=> $this->_convert_to_float( $actual_hours ),
						'schedule_type'		=> ( !empty( $postdata['schedule_type'] ) ) ? ucwords( $postdata['schedule_type'] ) : 'Working time',
						'notes'				=> $resource_data['notes']
					);

					if( !empty( $data ) ){
						## $conditions = [ 'account_id'=>$account_id, 'user_id'=>$data['user_id'], 'ref_date'=>$data['ref_date'] ];
						$conditions = [ 'account_id'=>$account_id, 'resource_id'=>$resource_id ];
						$query = $this->db->where( $conditions )
							->where('archived !=', 1 )
							->get( 'diary_resource_schedule' );

						if( $query->num_rows() > 0 ){
							$row 								= $query->result_array()[0];
							$data['last_modified_by']			= $this->ion_auth->_current_user->id;
							$this->db->update( 'diary_resource_schedule', $data, ['account_id'=>$account_id, 'resource_id'=>$row['resource_id'] ] );
							$res_id = $row['resource_id'];
							
						} else {
							## Reference Data
							$reference_data = [
								'person_id'		 => $person_exists->person_id,
								'ref_date'		 => $data['ref_date'],
								'shift_times'	 => [
									'start_time' => $data['start_time'],
									'finish_time'=> $data['finish_time']
								]
							];
							
							$data['created_by']			= $this->ion_auth->_current_user->id;
							$data['schedule_ref']		= $this->_generate_schedule_ref( $account_id, $person_exists->person_id, $reference_data );
						
							$this->db->insert( 'diary_resource_schedule', $data );
							$res_id = ( !empty( $this->db->insert_id() ) ? $this->db->insert_id() : false );
						}

						if( ( $this->db->trans_status() !== FALSE ) && ( !empty( $res_id ) ) ){
							$result = $this->get_diary_resource( $account_id, ['resource_id'=>$res_id] );
							$this->session->set_flashdata( 'message', 'The resource has been updated' );
						} else {
							$this->session->set_flashdata( 'message', 'There was an error processing the entry.' );
						}

					} else{
						$this->session->set_flashdata( 'message', 'There was an error with the dataset.' );
					}
					
				} else {
					$this->session->set_flashdata( 'message', 'The Person not found in the system.' );
				}
			} else {
				$this->session->set_flashdata( 'message', 'The Person ID is missing.' );
			}
			
		} else {
			$this->session->set_flashdata( 'message', 'Your request is missing required information.' );
		}
		
		return $result;
	}
	

	/** Create Availability **/
	public function delete_diary_resource( $account_id = false, $resource_id = false ){
		$result = false;
		
		if( !empty( $account_id ) && !empty( $resource_id ) ){
			
			$arch_data = [
				"archived_by" 		=> $this->ion_auth->_current_user->id,
				/* "last_modified_by" 	=> $this->ion_auth->_current_user->id, ## an extra protection layer - we will see who modified the item before archiving */
				"date_archived" 	=> date( 'Y-m-d H:i:s' ),
				"archived"			=> 1,
			];
			
			$this->db->update( "diary_resource_schedule", $arch_data, ["account_id" => $account_id, "resource_id" => $resource_id] );
			
			if( $this->db->trans_status() !== FALSE ){
				$result = true;
				$this->session->set_flashdata( 'message', 'The Resource has been deleted' );
			} else {
				$this->session->set_flashdata( 'message', 'No Resource has been deleted.' );
			}
		} else {
			$this->session->set_flashdata( 'message', 'Your request is missing required information.' );
		}
		
		return $result;
	}
	
	/** Check UN-AVAILABLE dates **/
	public function get_unavailable_dates( $account_id = false, $data = false, $where = false ){
		
		$result = false;
		
		if( !empty( $account_id ) && !empty( $data ) ){
			
			$where = convert_to_array( $where );
			
			if( !empty( $where['date_from'] ) ){
				$date_from 	= date('Y-m-d',strtotime( $where['date_from'] ) );
				$date_to 	= ( !empty( $where['date_to'] ) ) ? ( date('Y-m-d', strtotime( $where['date_to'] ) ) ) : ( date( 'Y-m-d', strtotime( $date_from.' + 2 months' ) ) );
				if( strtotime( $date_to ) < strtotime( $date_from ) ){
					$date_to = date( 'Y-m-d', strtotime( $date_from.' + 2 months' ) );
				} else if( strtotime( $date_to ) >  strtotime( $date_from.' + 2 months' ) ){
					$date_to = date( 'Y-m-d', strtotime( $date_from.' + 2 months' ) );
				}

			} else if ( !empty( $where['job_date'] ) ){
				$date_from 	= date( 'Y-m-d',strtotime( $where['job_date'] ) );
				$date_to 	= date( 'Y-m-d', strtotime( $date_from.' + 2 months' ) );
			}

			$query = $this->db->select( 'DATE_FORMAT( drs.ref_date, "%d/%m/%Y" ) `ref_date`, 
				SUM( CAST( drs.actual_slots AS DECIMAL(4,2) ) ) `actual_slots`,
				SUM( CAST( drs.consumed_slots AS DECIMAL(4,2) ) ) `consumed_slots`
				', false )
				->where( 'drs.account_id', $account_id )
				->where( 'drs.ref_date >=', $date_from )
				->where( 'drs.ref_date <=', $date_to )
				->where( 'consumed_slots >= actual_slots' )
				->group_by( 'drs.ref_date' )
				->get( 'diary_resource_schedule drs' );

			if( $query->num_rows() > 0 ){
				$result = array_column( $query->result_array(), 'ref_date' );
				$this->session->set_flashdata( 'message', 'Un-available Dates Data found.' );
			} else {
				$this->session->set_flashdata( 'message', 'No Data found' );
			}
		} else{
			$this->session->set_flashdata( 'message', 'Your request is missing required information.' );
		}
		return $result;
	}
	
	
	/** Check dates with Availability **/
	public function get_available_dates( $account_id = false, $data = false, $where = false ){
		
		$result = false;
		
		if( !empty( $account_id ) && !empty( $data ) ){
			
			$where = convert_to_array( $where );
			
			if( !empty( $where['date_from'] ) ){
				$date_from 	= date('Y-m-d',strtotime( $where['date_from'] ) );
				$date_to 	= ( !empty( $where['date_to'] ) ) ? ( date('Y-m-d', strtotime( $where['date_to'] ) ) ) : ( date( 'Y-m-d', strtotime( $date_from.' + 2 months' ) ) );
				if( strtotime( $date_to ) < strtotime( $date_from ) ){
					$date_to = date( 'Y-m-d', strtotime( $date_from.' + 2 months' ) );
				} else if( strtotime( $date_to ) >  strtotime( $date_from.' + 2 months' ) ){
					$date_to = date( 'Y-m-d', strtotime( $date_from.' + 2 months' ) );
				}

			} else if ( !empty( $where['job_date'] ) ){
				$date_from 	= date( 'Y-m-d',strtotime( $where['job_date'] ) );
				$date_to 	= date( 'Y-m-d', strtotime( $date_from.' + 2 months' ) );
			}

			$query = $this->db->select( 'DATE_FORMAT( drs.ref_date, "%d/%m/%Y" ) `ref_date`, 
				SUM( CAST( drs.actual_slots AS DECIMAL(4,2) ) ) `actual_slots`,
				SUM( CAST( drs.consumed_slots AS DECIMAL(4,2) ) ) `consumed_slots`
				', false )
				->where( 'drs.account_id', $account_id )
				->where( 'drs.ref_date >=', $date_from )
				->where( 'drs.ref_date <=', $date_to )
				->where( 'actual_slots > consumed_slots' )
				->group_by( 'drs.ref_date' )
				->get( 'diary_resource_schedule drs' );

			if( $query->num_rows() > 0 ){
				$result = array_column( $query->result_array(), 'ref_date' );
				$this->session->set_flashdata( 'message', 'Un-available Dates Data found.' );
			} else {
				$this->session->set_flashdata( 'message', 'No Data found' );
			}
		} else{
			$this->session->set_flashdata( 'message', 'Your request is missing required information.' );
		}
		return $result;
	}
	
	
	/**
	* Get Available diary resource - Optimized
	**/
	public function get_available_engineer_resource( $account_id = false, $where = false , $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;
		if( !empty( $account_id ) ){
			
			$where 					= convert_to_array( $where );
			$associated_user_id 	= $user_ids = false;

			if( !empty( $where ) ){
				
				## Get Associated Users
				$associated_user_id = $user_ids = false;
				
				if( !empty( $where['associated_user_id'] ) ){
					$associated_user_id = $where['associated_user_id'];
					unset( $where['associated_user_id'] );
					$helper_query = $this->db->get_where( "associated_users", ["account_id" => $account_id, "primary_user_id" => $associated_user_id] )->result_array();
					if( !empty( $helper_query ) ){
						$user_ids = array_column( $helper_query, 'user_id' );
						if( !empty( $user_ids ) ){
							$user_ids[] = $associated_user_id;
						}
					}
				}
				
				## CHECK FOR JOB TYPE
				$skilled_person_ids 		= [];
				if( !empty( $where['job_type_id'] ) ){

					$job_type_id = convert_to_array( $where['job_type_id'] );
					$job_type_id = is_array( $job_type_id ) ? $job_type_id : [ $job_type_id ];

					$skills_query = $this->db->select( 'jrs.job_type_id, ss.skill_id' )
						->where( 'jrs.account_id', $account_id )
						->where_in( 'jrs.job_type_id', $job_type_id )
						->join( 'job_types jt', 'jt.job_type_id = jrs.job_type_id' )
						->join( 'skills_bank ss', 'ss.skill_id = jrs.skill_id' )
						->get( 'job_type_required_skills jrs' );
					
					if( $skills_query->num_rows() > 0 ){
						$skill_res 			= $skills_query->result_array();
						$skill_ids			= array_column( $skill_res, 'skill_id' );
					}
					
					## get those who can do the job
					$skilled_personnel 			= $this->get_skilled_people( $account_id, $skill_ids, [ 'grouped'=>1 ] );
					$skilled_person_ids 		= ( !empty( $skilled_personnel ) ) ? array_keys( $skilled_personnel ) : false;
					unset( $where['job_type_id'] );
				}
				
				## check for the region
				$engineer_ids_by_region 	= [];
				if( !empty( $where['region_id'] ) ){

					$region_id = $where['region_id'];
					$region_id = convert_to_array( $where['region_id'] );
					$region_id = is_array( $region_id ) ? $region_id : [ $region_id ];
					
					$this->db->select( "person_id", false );
					$this->db->where( "account_id", $account_id );
					$this->db->where_in( "region_id", $region_id );
					$query_reg = $this->db->get( "people_assigned_regions" )->result_array();
					
					$engineer_ids_by_region 	= array_map( function( $value ){ return $value['person_id']; }, $query_reg );
					
					unset( $where['region_id'] );
				}
			}

			if( isset( $where['date_from'] ) ){
				$date_from 	= date( 'Y-m-d', strtotime( $where['date_from'] ) );
				$date_to 	= ( !empty( $where['date_to'] ) ) ? date( 'Y-m-d',strtotime( $where['date_to'] ) ) : date('Y-m-d');
			}else if( $where['ref_date'] ){
				$date_from 	= $date_to 	= date( 'Y-m-d',strtotime( $where['ref_date'] ) );
			}

			if( !empty( $order_by ) ){
				$this->db->order_by( $order_by  );
			} else {
				$this->db->order_by( 'ref_date ASC' );
			}

			$this->db->select( 'diary_resource_schedule.user_id, resource_id, diary_resource_schedule.account_id, ref_date, day, start_time, finish_time, lunch_allowance, break_allowance, base_hours, actual_hours, base_slots, actual_slots, consumed_slots', false )
				->join( 'people', 'people.person_id = diary_resource_schedule.user_id' );
			
			if( !empty( $date_from ) ){
				$this->db->where( 'ref_date >=', $date_from );
			}
			
			if( !empty( $date_to ) ){
				$this->db->where( 'ref_date <=', $date_to );
			}
			
			$this->db->where( 'diary_resource_schedule.account_id', $account_id );
			$this->db->where( 'diary_resource_schedule.archived !=', 1 );
			$this->db->where( 'people.is_active', 1 );
			
			if( !empty( $user_ids ) ){
				$this->db->where_in( "diary_resource_schedule.user_id", $user_ids );
			}
			
			if( !empty( $skilled_person_ids ) ){
				$this->db->where_in( "diary_resource_schedule.user_id", $skilled_person_ids );
			}
			
			if( !empty( $engineer_ids_by_region ) ){
				$this->db->where_in( "diary_resource_schedule.user_id", $engineer_ids_by_region );
			}
			
			$query = $this->db->get( 'diary_resource_schedule' );

			if( $query->num_rows() > 0 ){
				
				if( !empty( $skilled_personnel ) ){

					$data = [];
					
					foreach( $query->result() as $k => $row ){
						
						$booked_jobs 							= $this->_get_assigned_jobs( $account_id, [ 'job_date'=>$row->ref_date, 'assigned_to'=>$row->user_id ] );
						
						$available_resource			 			= $skilled_personnel[$row->user_id];

						$row->booked_jobs						= !empty( $booked_jobs->records ) ? $booked_jobs->records : ( !empty( $booked_jobs ) ? $booked_jobs : null );
						$actual_booked_slots					= !empty( $row->booked_jobs ) ? ( array_sum( array_column( $row->booked_jobs, 'job_duration' ) ) ) : 0;
						$row->booked_slots						= (string) $actual_booked_slots;
						
						$booked_postcodes						= !empty( $row->booked_jobs ) ? ( array_column( $row->booked_jobs, 'address_postcode' ) ) : [];
						$row->booked_postcodes					= array_unique( $booked_postcodes );
						
						$booked_postcode_area					= !empty( $row->booked_jobs ) ? ( array_map( 'trim', array_unique( array_column( $row->booked_jobs, 'postcode_area' ) ) ) ) : [];
						$booked_postcode_districts				= !empty( $row->booked_jobs ) ? ( array_map( 'trim', array_unique( array_column( $row->booked_jobs, 'postcode_district' ) ) ) ) : [];

						$row->booked_postcode_areas 			= $booked_postcode_area;
						$row->booked_postcode_districts 		= $booked_postcode_districts;

						$available_resource->availability 		= $row;
						$data[$row->ref_date][$row->user_id] 	= $available_resource;
					}

					$result = $data;
				} else {
					$data 	= [];
					foreach( $query->result() as $k => $row ){
						$user 				= $this->get_user_address_details( $account_id, $row->user_id );
						$home_address		= '';
						$home_address		.= !empty( $user->address_line1 ) ? $user->address_line1.', ' : '';
						$home_address		.= !empty( $user->address_town ) ? $user->address_town.', ' : '';
						$home_address		.= !empty( $user->address_postcode ) ? $user->address_postcode : '';

						$booked_jobs 				= $this->_get_assigned_jobs( $account_id, [ 'job_date'=>$row->ref_date, 'assigned_to'=>$row->user_id ] );
						$row->booked_jobs			= !empty( $booked_jobs->records ) ? $booked_jobs->records : ( !empty( $booked_jobs ) ? $booked_jobs : null );
						
						$actual_booked_slots		= !empty( $row->booked_jobs ) ? ( array_sum( array_column( $row->booked_jobs, 'job_duration' ) ) ) : 0;
						$row->booked_slots			= (string) $actual_booked_slots;
						
						$booked_postcodes			= !empty( $row->booked_jobs ) ? ( array_column( $row->booked_jobs, 'address_postcode' ) ) : [];
						$row->booked_postcodes		= $booked_postcodes;
						
						$booked_postcode_area					= !empty( $row->booked_jobs ) ? ( array_map( 'trim', array_unique( array_column( $row->booked_jobs, 'postcode_area' ) ) ) ) : [];
						$booked_postcode_districts				= !empty( $row->booked_jobs ) ? ( array_map( 'trim', array_unique( array_column( $row->booked_jobs, 'postcode_district' ) ) ) ) : [];

						$row->booked_postcode_areas 			= $booked_postcode_area;
						$row->booked_postcode_districts 		= $booked_postcode_districts;
						
						$available_resource = [
							'person' 			=> ( $user->first_name.' '.$user->last_name ),
							'person_id' 		=> $row->user_id,
							'home_postcode' 	=> !empty( $user->address_postcode ) ? $user->address_postcode : '',
							'home_address' 		=> $home_address,
							'personal_skills' 	=> null,
							'availability' 		=> $row,
						];
						$data[$row->ref_date][$row->user_id] 	= $available_resource;
					}
					$result = $data;
				}

				if( !empty( $result ) ){
					$this->session->set_flashdata( 'message', 'Resource availability found' );
				} else {
					$this->session->set_flashdata( 'message', 'No resource available for the supplied criteria' );
				}

			} else {
				$this->session->set_flashdata( 'message', 'No resource available for the supplied criteria' );
			}
		}
		return $result;
	}

	/** Get Booked Jobs By Skilled Personnel by Group **/
	public function _get_assigned_jobs( $account_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;

		$this->db->select('job.*, job_types.job_type, job_statuses.job_status, job_statuses.status_group, fc.fail_code, fc.fail_code_text, fc.fail_code_desc, fc.fail_code_group, CONCAT(user.first_name," ",user.last_name) `assignee`, addrs.main_address_id,addrs.addressline1 `address_line_1`, addrs.addressline2 `address_line_2`,addrs.addressline3 `address_line_3`,addrs.posttown `address_city`,addrs.county `address_county`, addrs.postcode `address_postcode`, postcode_area, postcode_district, postcode_sector, addrs.summaryline `summaryline`, CONCAT( addrs.addressline1,", ",addrs.addressline2,", ",addrs.posttown, ", ",addrs.posttown,", ",addrs.postcode ) `short_address`, addrs.organisation `address_business_name`',false)
			->join( 'addresses addrs','addrs.main_address_id = job.address_id','left' )
			->join( 'job_types','job_types.job_type_id = job.job_type_id','left' )
			->join( 'job_statuses','job_statuses.status_id = job.status_id','left' )
			->join( 'job_fail_codes fc','fc.fail_code_id = job.fail_code_id','left' )
			->join( 'user','user.id = job.assigned_to','left' )
			->where( 'job.archived !=', 1 );

		if( !empty( $account_id ) ){
			$this->db->where( 'job.account_id', $account_id );
		}

		$where = $raw_where = !empty( $where ) 	? convert_to_array( $where ) 	: false;

		if( !empty( $where ) ){

			if( isset( $where['assigned_to'] ) ){
				if( !empty( $where['assigned_to'] ) ){
					$this->db->where( 'job.assigned_to', $where['assigned_to'] );
				}
				unset( $where['assigned_to'] );
			}

			if( !empty( $where['job_date'] ) ){
				$job_date 	= date('Y-m-d',strtotime( $where['job_date'] ) );
				$this->db->where( 'job_date',$job_date );
			} else if( !empty( $where['date_from'] ) ){
				$date_from 	= date('Y-m-d',strtotime( $where['date_from'] ) );
				$date_to 	= ( !empty( $where['date_to'] ) ) ? date('Y-m-d',strtotime( $where['date_to'] ) ) : date( 'Y-m-d' );
				$this->db->where( 'job_date >=',$date_from );
				$this->db->where( 'job_date <=',$date_to );
			}else if( !empty( $where['job_date'] ) ){
				$job_date = date( 'Y-m-d',strtotime( $where['job_date'] ) );
				$this->db->where( 'job_date', $job_date );
			}

			unset( $where['date_from'], $where['date_to'] );
		}

		if( $limit > 0 ){
			$this->db->limit( $limit, $offset );
		}

		$job = $this->db->order_by( 'job_id desc, job_date desc, job_type' )
			->get( 'job' );
		
		if( $job->num_rows() > 0 ){
			$result = $job->result();
			$this->session->set_flashdata('message','Job records found');
		}else{
			$this->session->set_flashdata('message','Job record(s) not found');
		}

		return $result;
	}
	
}
