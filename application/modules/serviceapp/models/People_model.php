<?php if (!defined('BASEPATH'))exit('No direct script access allowed');

class People_model extends CI_Model {

	function __construct(){
		parent::__construct();
		#$this->load->model('Panel_model','panel_service');
		$section 	   = explode("/", $_SERVER["SCRIPT_NAME"]);
		$this->app_root= $_SERVER["DOCUMENT_ROOT"]."/".$section[1]."/";
		$this->app_root= str_replace('/index.php','',$this->app_root);
		$this->load->library('upload');
    }

	/** Searchable fields **/
	private $searchable_fields  = ['people.person_id', 'people.user_id', 'people.status_id', 'people.preferred_name', 'people.department_id', 'people.job_title_id',  'user.first_name',  'user.last_name'];

	/*
	* Get Person single records or multiple records
	*/
	public function get_people( $account_id=false, $user_id = false, $person_id = false, $departments = false, $personal_email = false, $job_level = false, $where = false, $order_by = false, $limit=DEFAULT_LIMIT, $offset=DEFAULT_OFFSET ){
		$result = false;
		$this->db->select('people.*, user.account_user_id, user.email, user.phone, user.mobile_number, user.first_name, user.last_name, user_statuses.status, people_job_titles.job_title, people_departments.department_name, countries.country_name `nationality`',false)
			->join('people_departments', 'people_departments.department_id = people.department_id', 'left')
			->join('people_job_titles', 'people_job_titles.job_title_id = people.job_title_id', 'left')
			->join('user_statuses', 'user_statuses.status_id = people.status_id', 'left')
			->join('user', 'user.id = people.user_id')
			->join('countries', 'countries.country_id = people.nationality_id', 'left')
			->where('people.account_id',$account_id)
			->where('people.is_active =',1);

		if( $user_id || $person_id ){

			$where = ( !empty( $person_id ) ) ? ['people.person_id'=>$person_id] : ( ( !empty( $user_id) ) ? ['people.user_id'=>$user_id] : [] );

			$row = $this->db->get_where('people', $where )->row();

			if( !empty($row) ){
				$this->session->set_flashdata('message','Personal record found');
				$result = $row;
			}else{
				$this->session->set_flashdata('message','Personal record not found');
			}
			return $result;
		}

		if( $personal_email ){
			$this->db->where_in('people.personal_email', trim( $personal_email ) );
		}

		if( $departments ){
			$departments =  ( ( (int)$departments ) > 0 ) ? [$departments] : ( ( !is_array( $departments ) ) ? json_decode( $departments ) : $departments );
			$this->db->where_in('people.department_id', $departments );
		}

		$people = $this->db->order_by( 'first_name ASC' )
			->limit( $limit, $offset )
			->get('people');

		if( $people->num_rows() > 0 ){
			$this->session->set_flashdata('message','People records found');
			$result = $people->result();
		}else{
			$this->session->set_flashdata('message','People record(s) not found');
		}
		return $result;
	}

	/*
	* Create new Person
	*/
	public function create_person( $account_id=false, $person_data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $person_data ) ){
			$data = [];
			foreach( $person_data as $key=>$value ){
				if( in_array($key, format_date_columns() ) && !empty( $value ) ){
					if( !empty( $value ) ){
						$data[$key] = format_datetime_db($value);
					}
				}else{
					$data[$key] = ( !is_array( $value ) ) ? trim($value) : $value;
				}

			}

			if( !empty( $data['user_id'] ) || !empty( $data['person_id'] ) ){

				# Verify that this is a user that already exists and belows to the account of the current user
				$user_id 	 = ( !empty( $data['user_id'] ) ) ? $data['user_id'] : ( !empty( $data['person_id'] ) ? $data['person_id'] : false );
				$verify_user = $this->ion_auth->get_user_by_id( $account_id, $user_id );

				if( !empty( $verify_user ) ){

					$where = "( people.user_id = '".$user_id."' OR people.person_id = '".$user_id."' )";
					$person_exists = $this->db->where( $where )
						->where('people.account_id', $account_id)
						->get('people')->row();

					if( !$person_exists ){
						$new_person 			 	 = $this->ssid_common->_filter_data( 'people', $data );
						$new_person['person_id'] 	 = $user_id;
						$new_person['personal_email']= ( !empty( $new_person['personal_email'] ) ) ? $new_person['personal_email'] : $verify_user->email;
						$new_person['created_by']	 = ( !empty( $this->ion_auth->_current_user->id ) ) ? $this->ion_auth->_current_user->id : null;

						$this->db->insert('people',$new_person);

						if( $this->db->trans_status() !== FALSE ){
							## Create a position log
							if( !empty( $data['job_title_id'] ) ){
								$data['person_id'] 		= $user_id;
								$data['job_start_date'] = !empty( $data['start_date'] ) ? date( 'Y-m-d', strtotime( $data['start_date'] ) ) : date( 'Y-m-d' );
								$this->create_position_log( $account_id, $user_id, $data );
							}

							$result = $this->get_people( $account_id, $user_id, $user_id );
							$this->session->set_flashdata('message','Person record created successfully.');
						}

					}else{
						$new_person 			 	 	= $this->ssid_common->_filter_data( 'people', $data );
						$new_person['last_modified_by'] = $this->ion_auth->_current_user->id;

						$this->db->where( 'people.account_id', $account_id )
							->where( "( people.user_id = '".$user_id."' OR people.person_id = '".$user_id."' )")
							->update( 'people', $new_person );
						if( $this->db->trans_status() !== FALSE ){
							$result = $this->get_people($account_id, $user_id, $user_id );
							$this->session->set_flashdata('message','Person record already exists, details updated successfully.');
						}
					}

				}else{
					$this->session->set_flashdata('message','Illegal operation. This user resource does not exist or does not below to you!');
					return false;
				}
			}else{

				$user_id = $this->_create_user_from_person_data( $account_id, $data );

				if( !empty( $user_id ) ){
					$data['user_id'] = $user_id;
					//Continue with creating a person record
					$result = $this->create_person( $account_id, $data );
				}else{
					$this->session->set_flashdata('message', $this->session->flashdata('message') );
					return false;
				}

			}
		}else{
			$this->session->set_flashdata('message','No Person data supplied.');
		}
		return $result;
	}

	/*
	* Update Person record
	*/
	public function update_person( $account_id = false, $person_id = false, $person_data = false ){

		$result = false;
		if( !empty($account_id) && !empty($person_id) && !empty($person_data) ){

			$data = $position_data = [];

			if( !empty( $person_data['position'] ) ){
				$position_data 	= ( !is_array( $person_data['position'] ) ) ? json_decode( $person_data['position'] ) : $person_data['position'];
				unset( $person_data['position'] );
			}

			foreach( $person_data as $key=>$value ){
				if( in_array($key, format_date_columns() ) ){
					$value = format_datetime_db( $value );
				}else{
					$value = ( !is_array( $value ) ) ? trim($value) : $value;
				}
				$data[$key] = $value;
			}

			if( !empty( $data ) ){

				#Check if this person already before we attempt to update them
				$conditions = [ 'account_id'=>$account_id, 'person_id'=>$person_id ];
				$query = $this->db->get_where( 'people', $conditions );

				if( $query->num_rows() > 0 ){

					$datab4update = $query->result()[0];

					$user_record  = $this->ion_auth->get_user_by_id( $account_id, $person_id );

					$update_user_data = [];

					if( !empty( $data['first_name'] )  && ( strtolower( $data['first_name'] ) != strtolower( $user_record->first_name ) ) ){
						$update_user_data['first_name'] = $data['first_name'];
					}

					if( !empty( $data['last_name'] )  && ( strtolower( $data['last_name'] ) != strtolower( $user_record->last_name ) ) ){
						$update_user_data['last_name'] = $data['last_name'];
					}

					if( !empty( $update_user_data ) ){
						$this->db->where( ['account_id'=>$account_id, 'id'=>$person_id] )->update( 'user', $update_user_data );
					}

					$update_data = $this->ssid_common->_filter_data( 'people', $data );
					$update_data['last_modified_by'] 	= $this->ion_auth->_current_user->id;
					$update_data['last_modified'] 		= date( 'Y-m-d H:i:s' );
					$this->db->where( $conditions );
					$this->db->update('people',$update_data);

					if( $this->db->trans_status() !== false ){

						## Add position log, only if the Job title has changed
						if( !empty( $data['job_title_id'] ) && ( ( $data['job_title_id'] != $datab4update->job_title_id ) || ( !empty( $position_data ) ) ) ){
							$position_data = is_object( $position_data ) ? object_to_array( $position_data ) : $position_data;
							$data = array_merge( $data, $position_data );
							$this->create_position_log( $account_id, $person_id, $data );
						}
						
						## create a log
						$log_history_data = [
							"log_type" 			=> "details",
							"entry_id" 			=> $person_id,
							"person_id" 		=> $person_id,
							"action" 			=> "update personal info",
							"previous_values" 	=> json_encode( $datab4update ),
							"current_values" 	=> json_encode( $update_data ),
						];
						
						$succ_log 	= $this->create_people_history_change_log( $account_id, $person_id, $log_history_data );

						$result = $this->get_people( $account_id, $person_id );
						$this->session->set_flashdata('message','Personal data updated successfully');
					}

				}else{
					$this->session->set_flashdata('message','Illegal operation. Access denied');
				}
			}
		}else{
			$this->session->set_flashdata('message','No Personal data supplied');
		}
		return $result;
	}

	/*
	* Delete Person record
	*/
	public function delete_person( $account_id = false, $person_id = false ){
		$result = false;
		if( $this->account_service->check_account_status( $account_id ) && !empty($person_id) ){
			$conditions 	= ['account_id'=>$account_id,'person_id'=>$person_id];
			$person_exists 	= $this->db->get_where('people',$conditions)->row();
			if( !empty($person_exists) ){
				$data = [
					'is_active'			=> NULL ,
					'last_modified_by'	=>$this->ion_auth->_current_user()->id
				];
				$this->db->where( $conditions )->update('people',$data);
				if( $this->db->trans_status() !== FALSE ){
					$this->session->set_flashdata('message','The profile has been deactivated.');
					$result = true;
				}
			}else{
				$this->session->set_flashdata('message','Invalid Person ID.');
			}

		}else{
			$this->session->set_flashdata('message','No Person record found.');
		}
		return $result;
	}

	/** Get Person types **/
	public function get_departments( $account_id = false, $department_id = false, $department_group = false, $grouped = false ){
		$result = null;
		if( $account_id ){
			$this->db->where( 'people_departments.account_id', $account_id );

			if( $department_group ){
				$this->db->where( 'people_departments.department_group', $department_group );
			}

		}else{
			$this->db->where( '( people_departments.account_id IS NULL OR people_departments.account_id = "" )' );
		}

		if( !empty( $department_id ) ){
			$this->db->where( 'people_departments.department_id', $department_id );
		}

		$query = $this->db->select( 'people_departments.*', false )
			->where( 'people_departments.is_active', 1 )
			->get( 'people_departments' );

		if( $query->num_rows() > 0 ){
			$result = $query->result();
		}else{
			#$result = $this->get_departments();
		}

		#Grouped result
		if( !empty( $grouped ) ){
			$data = [];
			foreach( $result as $k => $row ){
				$data[$row->department_group][] = $row;
			}
			$result = $data;
		}

		return $result;
	}


	/** Get Job Titles **/
	public function get_job_titles( $account_id = false, $job_title_id = false, $job_area = false, $job_level = false, $group_by = false ){
		$result = null;
		if( $account_id ){
			$this->db->where( 'people_job_titles.account_id', $account_id );

			if( $job_area ){
				$this->db->where( 'people_job_titles.job_area', $job_area );
			}

			if( $job_level ){
				$this->db->where( 'people_job_titles.job_level', $job_level );
			}

		}else{
			$this->db->where( '( people_job_titles.account_id IS NULL OR people_job_titles.account_id = "" )' );
		}

		if( !empty( $job_title_id ) ){
			$this->db->where( 'people_job_titles.job_title_id', $job_title_id );
		}
		
		$this->db->order_by( "people_job_titles.job_title ASC" );

		$query = $this->db->select( 'people_job_titles.*', false )
			->where( 'people_job_titles.is_active', 1 )
			->get( 'people_job_titles' );

		if( $query->num_rows() > 0 ){
			$result = $query->result();
		}else{
			#$result = $this->get_job_titles();
		}

		#Grouped result
		if( !empty( $group_by ) ){
			$data = [];
			foreach( $result as $k => $row ){
				if( ( !is_int( $group_by ) ) && ( !empty( $row->{$group_by} ) ) ){
					$data[$row->{$group_by}][] = $row;
				}else{
					$data[$row->job_area][] = $row;
				}
			}
			$result = $data;
		}

		return $result;
	}

	/*
	* Search through assets
	*/
	public function people_lookup( $account_id = false, $search_term = false, $departments = false, $user_statuses = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;
		if( !empty( $account_id ) ){
			$this->db->select('people.*, user.account_user_id, user.first_name, user.last_name, user_statuses.status, people_job_titles.job_title, people_departments.department_name',false)
			->join('people_departments', 'people_departments.department_id = people.department_id', 'left')
			->join('people_job_titles', 'people_job_titles.job_title_id = people.job_title_id', 'left')
			->join('user_statuses', 'user_statuses.status_id = people.status_id', 'left')
			->join('user', 'user.id = people.user_id')
			->where('people.account_id',$account_id)
			->where('people.is_active =',1);

			if( !empty( $search_term ) ){

				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->searchable_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						if( !empty($search_where['people.preferred_email']) ){
							$search_where['user.email'] =  trim( $term );
							$search_where['people.preferred_email'] =  trim( $term );
						}

						if( !empty($search_where['people.department_id']) ){
							$search_where['people_departments.department_name'] =  trim( $term );
							unset($search_where['people.department_id']);
						}

						if( !empty($search_where['people.job_title_id']) ){
							$search_where['people_job_titles.job_title'] 	=  trim( $term );
							$search_where['people_job_titles.job_specialty']=  trim( $term );
							$search_where['people_job_titles.job_level'] 	=  trim( $term );
							unset($search_where['people.job_title_id']);
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}

				}else{
					foreach( $this->searchable_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					if( !empty($search_where['people.preferred_name']) ){
						$search_where['user.email'] =  $search_term;
					}

					if( !empty($search_where['people.department_id']) ){
						$search_where['people_departments.department_name'] =  $search_term;
						unset($search_where['people.department_id']);
					}

					if( !empty($search_where['people.job_title_id']) ){
						$search_where['people_job_titles.job_title'] 	=  $search_term;
						$search_where['people_job_titles.job_specialty']=  $search_term;
						$search_where['people_job_titles.job_level'] 	=  $search_term;
						unset($search_where['people.job_title_id']);
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( $departments ){
				$departments = ( !is_array( $departments ) ) ? json_decode( $departments ) : $departments;
				$this->db->where_in( 'people.department_id', $departments );
			}

			if( $user_statuses ){
				$user_statuses = ( !is_array( $user_statuses ) ) ? json_decode( $user_statuses ) : $user_statuses;
				$this->db->where_in( 'people.status_id', $user_statuses );
			}

			if( $where ){
				$this->db->where( $where );
			}

			if( $order_by ){
				$this->db->order_by( $order_by );
			}else{
				$this->db->order_by( 'user.first_name, people.preferred_name' );
			}

			$query = $this->db->limit( $limit, $offset )
				->get('people');

			if( $query->num_rows() > 0 ){
				$result = $query->result();
				$this->session->set_flashdata('message','Records found.');
			}else{
				$this->session->set_flashdata('message','No records found matching your creteria.');
			}
		}

		return $result;
	}

	/*
	* Get total asset count for the search
	*/
	public function get_total_people( $account_id = false, $search_term = false, $departments = false, $user_statuses = false, $where = false ){
		$result = false;
		if( !empty( $account_id ) ){

			$this->db->select('people.person_id',false)
				->join('people_departments', 'people_departments.department_id = people.department_id', 'left')
				->join('people_job_titles', 'people_job_titles.job_title_id = people.job_title_id', 'left')
				->join('user_statuses', 'user_statuses.status_id = people.status_id', 'left')
				->join('user', 'user.id = people.user_id', 'left' )
				->where('people.account_id',$account_id)
				->where('people.is_active =',1);

			if( !empty( $search_term ) ){

				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->searchable_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						if( !empty($search_where['people.preferred_name']) ){
							$search_where['user.email'] =  trim( $term );
						}

						if( !empty($search_where['people.department_id']) ){
							$search_where['people_departments.department_name'] =  trim( $term );
							unset($search_where['people.department_id']);
						}

						if( !empty($search_where['people.job_title_id']) ){
							$search_where['people_job_titles.job_title'] 	=  trim( $term );
							$search_where['people_job_titles.job_specialty']=  trim( $term );
							$search_where['people_job_titles.job_level'] 	=  trim( $term );
							unset($search_where['people.job_title_id']);
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}

				}else{
					foreach( $this->searchable_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					if( !empty($search_where['people.preferred_name']) ){
						$search_where['user.email'] =  $search_term;
					}

					if( !empty($search_where['people.department_id']) ){
						$search_where['people_departments.department_name'] =  $search_term;
						unset($search_where['people.department_id']);
					}

					if( !empty($search_where['people.job_title_id']) ){
						$search_where['people_job_titles.job_title'] 	=  $search_term;
						$search_where['people_job_titles.job_specialty']=  $search_term;
						$search_where['people_job_titles.job_level'] 	=  $search_term;
						unset($search_where['people.job_title_id']);
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( $departments ){
				$departments = ( !is_array( $departments ) ) ? json_decode( $departments ) : $departments;
				$this->db->where_in( 'people.department_id', $departments );
			}

			if( $user_statuses ){
				$user_statuses = ( !is_array( $user_statuses ) ) ? json_decode( $user_statuses ) : $user_statuses;
				$this->db->where_in( 'people.status_id', $user_statuses );
			}

			if( $where ){
				$this->db->where( $where );
			}

			$query = $this->db->from('people')->count_all_results();
			$results['total'] = !empty( $query ) ? $query : 0;
			$results['pages'] = !empty( $query ) ? ceil( $query / DEFAULT_LIMIT ) : 0;
			return json_decode( json_encode( $results ) );
		}
		return $result;
	}

	/** Create a new user from the submitted Person / HR data **/
	private function _create_user_from_person_data( $account_id = false, $user_data = false ){
		$result = null;
		if( !empty( $account_id ) && !empty( $user_data['personal_email'] ) && !empty( $user_data['first_name'] ) && !empty( $user_data['last_name'] ) ){

			//Check if user exists with this email address
			$user_exists = $this->db->select( 'user.id')
				->get_where( 'user', ['email'=>trim( $user_data['personal_email'] )] )
				->row();

			if ( !empty( $user_exists ) ){
				$this->session->set_flashdata('message','User already exists with this email address! Returning user record.');
				return $user_exists->id;
			}

			//call create user function
			$user_data['email'] = $user_data['personal_email'];
			$user_id 			= $this->ion_auth->register( $user_data['email'], DEFAULT_PASSWORD, $user_data['email'], $user_data );
			if( !empty ( $user_id ) ){
				//Create default permissions
				$this->_assign_default_permissions( $account_id, $user_id );

				$result = $user_id;
			}else{
				$message = ( $this->ion_auth->errors() ) ? 'Email address '.$this->ion_auth->errors() : 'Something went wrong while trying to create a user resource!';
				$this->session->set_flashdata( 'message', $message );
			}
		}else{
			$this->session->set_flashdata('message','Email address, first and last names are all required fields to create a new person resource!');
		}
		return $result;
	}

	/** Create Position log **/
	public function create_position_log( $account_id=false, $person_id=false, $data = false ){

		$result = $last_position = false;
		if( !empty( $account_id ) && !empty( $person_id ) && !empty( $data ) ){

			$data['created_by'] 	= $this->ion_auth->_current_user->id;
			$data['job_start_date'] = ( !empty( $data['job_start_date'] ) ) ? format_date_db( $data['job_start_date'] ) : date( 'Y-m-d' );
			$data['job_end_date'] 	= ( !empty( $data['job_end_date'] ) ) ? format_date_db( $data['job_end_date'] ) : null;

			$last_position = $this->db->limit( 1 )
				->where( 'person_id', $person_id )
				->order_by( 'position_id desc' )
				->get( 'people_job_positions' )
				->row();
							
			if( !empty( $last_position ) && ( !( validate_date( $last_position->job_end_date ) ) ) ){
				if( empty( $last_position->job_end_date ) ){
					$this->db->where( 'position_id', $last_position->position_id )
						->update( 'people_job_positions', ['job_end_date' => date( 'Y-m-d', strtotime( $data['job_start_date'] . ' -1 day' ) ), 'last_modified_by'=>$this->ion_auth->_current_user->id] );
				}
			}

			$position_data = $this->ssid_common->_filter_data( 'people_job_positions', $data );
			$this->db->insert( 'people_job_positions', $position_data );
			if( $this->db->affected_rows() > 0 ){
				
				$new_log_id = $this->db->insert_id();
				
				## create a log
				$log_history_data = [
					"log_type" 			=> "positions",
					"entry_id" 			=> $new_log_id,
					"person_id" 		=> $person_id,
					"action" 			=> "create a new log",
					"previous_values" 	=> NULL,
					"current_values" 	=> json_encode( $data ),
				];
				
				$succ_log 	= $this->create_people_history_change_log( $account_id, $person_id, $log_history_data );
				$result 	= $this->db->get_where( "people_job_positions", ["position_id"=>$new_log_id] )->row();
			}
		}
		return $result;
	}

	/** Get position for a particular person or account **/
	public function get_job_positions( $account_id=false, $person_id=false, $position_id=false, $job_title_id=false, $job_start_date=false, $job_end_date=false, $limit = DEFAULT_LIMIT, $offset = 0 ){
		$result = null;
		if( !empty( $account_id ) ){
			$this->db->select('positions.*, jt.job_title, jt.job_level, jt.job_area, people_departments.department_name, concat(user.first_name," ",user.last_name) `created_by_full_name`, concat(modifier.first_name," ",modifier.last_name) `modified_by`, CONCAT( u1.first_name, " ", u1.last_name ) `line_manager_full_name`,  CONCAT( u2.first_name, " ", u2.last_name ) `last_modified_by_ful_name`', false)
				->where('positions.account_id',$account_id);

				if( $person_id ){
					$this->db->where('positions.person_id',$person_id);
				}

				if( $position_id ){
					$this->db->where('positions.position_id',$position_id);
				}

				if( $job_title_id ){
					$this->db->where('positions.job_title_id',$job_title_id);
				}

				if( !empty( $job_start_date ) && !empty( $job_end_date ) ){
					$this->db->where('positions.job_start_date >=', date( 'Y-m-d', strtotime( $job_start_date ) ) );
					$this->db->where('positions.job_end_date <=', date( 'Y-m-d', strtotime( $job_end_date ) ) );
				}else{
					if( $job_start_date ){
						$this->db->where('positions.job_start_date', date( 'Y-m-d', strtotime( $job_start_date ) ) );
					}

					if( $job_end_date ){
						$this->db->where('positions.job_end_date', date( 'Y-m-d', strtotime( $job_end_date ) ) );
					}
				}
				
				$arch_where = "( positions.archived != 1 or positions.archived is NULL )";
				$this->db->where( $arch_where );

				$this->db->limit( $limit, $offset );

			$query = $this->db->join('user','user.id = positions.created_by','left')
				->join('people_job_titles jt','jt.job_title_id = positions.job_title_id','left')
				->join('people_departments','people_departments.department_id = positions.department_id','left')
				->join('user modifier','modifier.id = positions.last_modified_by','left')
				->join('user u1','u1.id = positions.line_manager_id','left')
				->join('user u2','u2.id = positions.last_modified_by','left')
				->order_by('positions.position_id desc')
				->get('people_job_positions positions');
			if( $query->num_rows() > 0 ){
				$this->session->set_flashdata('message','Position data found');
				$result = $query->result();
			}else{
				$this->session->set_flashdata('message','Position data not found');
			}
		}else{
			$this->session->set_flashdata('message','No parameters supplied for request');
		}
		return $result;
	}

	/** Create a persons contact record **/
	public function create_contact( $account_id = false, $person_id = false, $contact_data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $person_id )  && !empty( $contact_data ) ){
			$data = [];
			foreach( $contact_data as $key=>$value ){
				$data[$key] = ( !is_array( $value ) ) ? trim( $value ) : $value;
			}
			if( !empty( $data ) ){
				$new_contact = $this->ssid_common->_filter_data( 'people_contact_addresses', $data );
				$new_contact['created_by'] = $this->ion_auth->_current_user->id;
				$this->db->insert( 'people_contact_addresses', $new_contact );
				if( $this->db->trans_status() !== false ){
					$contact_id = $this->db->insert_id();
					
					## create a log
					$log_history_data = [
						"log_type" 			=> "contacts",
						"entry_id" 			=> $contact_id,
						"person_id" 		=> $person_id,
						"action" 			=> "create a new contact",
						"previous_values" 	=> NULL,
						"current_values" 	=> json_encode( $data ),
					];
					
					$succ_log 	= $this->create_people_history_change_log( $account_id, $person_id, $log_history_data );
				
					$result = $this->get_address_contacts( $account_id, false, $contact_id );
					$this->session->set_flashdata('message','Address Contact added successfully');
				}
			}else{
				$this->session->set_flashdata('message','An error occurred while adding an address contact!');
			}
		}else{
			$this->session->set_flashdata('message','Required parameters not supplied!');
		}
		return $result;
	}

	/** Get list of all contacts attached to a person **/
	public function get_address_contacts( $account_id=false, $person_id=false, $contact_id=false, $address_type_id=false, $limit = DEFAULT_LIMIT, $offset = 0 ){
		$result = null;
		if( !empty( $account_id ) ){
			$this->db->select('people_contact_addresses.*, address_types.address_type, concat(user.first_name," ",user.last_name) `created_by`, concat(modifier.first_name," ",modifier.last_name) `modified_by`', false)
				->where('people_contact_addresses.account_id',$account_id)
				->join('user','user.id = people_contact_addresses.created_by','left')
				->join('user modifier','modifier.id = people_contact_addresses.last_modified_by','left')
				->join('address_types','address_types.address_type_id = people_contact_addresses.address_type_id','left');

				if( $contact_id ){
					$row = $this->db->get_where('people_contact_addresses', ['contact_id'=>$contact_id])->row();
					if( !empty($row) ){
						$this->session->set_flashdata('message','Contact details record found');
						$result = $row;
					}else{
						$this->session->set_flashdata('message','Contact details not found');
					}
					return $result;
				}

				if( $person_id ){
					$this->db->where('people_contact_addresses.person_id',$person_id);
				}
				
				$arch_where = "( people_contact_addresses.archived != 1 or people_contact_addresses.archived is NULL )";
				$this->db->where( $arch_where );

				$query = $this->db->limit( $limit, $offset )
					->order_by('people_contact_addresses.contact_first_name')
					->get('people_contact_addresses');
			if( $query->num_rows() > 0 ){
				$this->session->set_flashdata('message','Contacts data found');
				$result = $query->result();
			}else{
				$this->session->set_flashdata('message','Contacts data not found');
			}
		}else{
			$this->session->set_flashdata('message','No parameters supplied for request');
		}
		return $result;
	}

	/** Search for user pre-creations of people records **/
	public function find_user_records( $account_id = false, $search_term = false ){
		$result = null;
		if( !empty(  $account_id ) && !empty( $search_term ) ){
			$found_users = [];
			$users = $this->ion_auth->user_lookup( $account_id, $search_term );
			if( !empty( $users ) ){
				foreach( $users as $user ){
					$perosn_exists = $this->get_people( $account_id, $user->id );
					if( !empty( $perosn_exists ) ){
						$found_users['exists'][] 			= $user;
					}else{
						$found_users['person_not_found'][]  = $user;
					}
				}
				$result = $found_users;
			}
		}else{
			$this->session->set_flashdata('message','No parameters supplied for request');
		}
		return $result;

	}

	/** Process People Upload **/
	public function upload_people( $account_id = false ){
		$result = null;
		if( !empty ( $account_id ) ){
			$uploaddir  = $this->app_root. 'assets' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;

			if( !file_exists( $uploaddir) ){
				mkdir( $uploaddir );
			}
			
			$this->db->truncate( 'people_tmp_upload' );

			for( $i=0; $i < count( $_FILES['upload_file']['name'] ); $i++ ) {
				//Get the temp file path
				$tmpFilePath = $_FILES['upload_file']['tmp_name'][$i];
				if ( $tmpFilePath != '' ){
					$uploadfile = $uploaddir . basename( $_FILES['upload_file']['name'][$i] ); //Setup our new file path
					if ( move_uploaded_file( $tmpFilePath, $uploadfile) ) {
						//If FILE is CSV process differently
						$ext = pathinfo( $uploadfile, PATHINFO_EXTENSION );
						if ( $ext == 'csv' ){
							$processed = csv_file_to_array( $uploadfile );
							if( !empty( $processed ) ){
								$data = $this->_save_temp_data( $account_id, $processed );
								if( $data ){
									unlink( $uploadfile );
									$result = true;
								}
							}
						}
					}
				}
			}
		}
		return $result;
	}

	/** Process uploaded array **/
	private function _save_temp_data( $account_id = false, $raw_data = false ){
		$result = null;
		if( !empty( $account_id ) && !empty( $raw_data ) ){
			$exists = $new = [];
			foreach( $raw_data as $k => $record ){
				$record['user_type_id'] = ( !empty( $record['user_type_id'] ) ) ? $record['user_type_id'] : 2;
				$record['is_active'] 	= 1;
				$check_exists = $this->db->where( ['account_id'=>$account_id, 'personal_email'=>$record['personal_email'] ] )
					->limit( 1 )
					->get( 'people_tmp_upload' )
					->row();
				if( !empty( $check_exists ) ){
					$exists[] 	= $this->ssid_common->_filter_data( 'people_tmp_upload', $record );
				}else{
					$new[]  	= $this->ssid_common->_filter_data( 'people_tmp_upload', $record );
				}
			}

			//Updated existing
			if( !empty( $exists ) ){
				$this->db->update_batch( 'people_tmp_upload', $exists, 'personal_email' );
			}

			//Insert new records
			if( !empty( $new ) ){
				$this->db->insert_batch( 'people_tmp_upload', $new );
			}

			$result = ( $this->db->trans_status() !== false ) ? true : false;
		}
		return $result;
	}

	/** Get records pending from upload **/
	public function get_pending_upload_records( $account_id = false ){
		$result = null;
		if( !empty( $account_id ) ){
			$query = $this->db->where( 'account_id', $account_id )
				->order_by( 'personal_email' )
				->get( 'people_tmp_upload' );

			if( $query->num_rows() > 0 ){
				$data = [];
				foreach( $query->result() as $k => $row ){
					$check = $this->db->select( 'user.id, people.person_id' )
						->join( 'people', 'people.user_id = user.id', 'left' )
						->where( 'user.account_id', $account_id )
						->where( 'user.email', $row->personal_email )
						->limit( 1 )
						->get( 'user' )
						->row();
					if( !empty( $check->person_id ) ){
						$data['existing-records'][] = ( array ) $row;
					}else{
						$data['new-records'][] = ( array ) $row;
					}
				}
				$result = $data;
			}
		}
		return $result;
	}

		/*
	* Update Person record
	*/
	public function update_temp_data( $account_id = false, $temp_user_id = false, $temp_data = false ){

		$result = false;
		if( !empty( $account_id ) && !empty( $temp_user_id ) && !empty( $temp_data ) ){
			$data  = [];
			$where = [
				'account_id'=>$account_id,
				'temp_user_id'=>$temp_user_id
			];

			foreach( $temp_data as $key => $value ){
				$data[$key] = trim( $value );
			}

			$update_data = array_merge( $data, $where );
			$this->db->where( $where )
				->update( 'people_tmp_upload', $update_data );

			$result = ( $this->db->trans_status() !== 'false' ) ? true : false;
		}
		return $result;
	}

	/** Create People **/
	public function create_people( $account_id = false, $postdata = false ){
		$result = null;

		if( !empty( $account_id ) && !empty( $postdata['people'] ) ){
			$to_delete = $processed = [];
			foreach( $postdata['people'] as $temp_user_id => $update_record ) {
				#get temp data
				if( !empty( $update_record['checked'] ) ){
					
					$get_temp_record = (array) $this->db->get_where( 'people_tmp_upload', [ 'temp_user_id'=>$temp_user_id ] )->row();
					$user_id = $this->_create_user_from_person_data( $account_id, $get_temp_record );

					if( !empty( $user_id ) ){
						$get_temp_record['user_id']   = $user_id;
						$get_temp_record['person_id'] = $user_id;
						//Continue with creating a person record
						$new_person = $this->create_person( $account_id, $get_temp_record );

						if( !empty( $new_person ) ){
							$processed[] = $new_person;
							$to_delete[$temp_user_id] = $temp_user_id;
						}else{
							$user_failed[] = $get_temp_record;
						}
					}else{
						$user_failed[] = $get_temp_record;
					}
				}
			}

			if( !empty( $processed ) ){
				$result = $processed;
				//Delete processed records
				if( !empty( $to_delete ) ){
					$this->db->where_in( 'temp_user_id', $to_delete )
						->delete( 'people_tmp_upload' );

					$this->ssid_common->_reset_auto_increment( 'people_tmp_upload', 'temp_user_id' ); //House keeping
				}
				$this->session->set_flashdata('message','People records added successfully.');
			}
		}
		return $result;
	}

	/** Assign default module access and permissions **/
	private function _assign_default_permissions( $account_id = false, $user_id = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $user_id ) ){
			//At this time simply copy the main account holder's permission #2
			$module_access = $this->db->select( 'user_id, module_id, account_id, has_access, is_module_admin', false )
				->where( [ 'account_id'=>$account_id, 'user_id'=> 2 ] )
				->get( 'user_module_access' );

			if( $module_access->num_rows() > 0 ){
				$mod_array 		= [];
				$mod_item_perms = [];
				foreach( $module_access->result_array() as $k =>$record ){
					$record['user_id'] = $user_id;
					$mod_array[$k] = $record;

					## Get module item perms
					/*$module_items = $this->db->select( 'user_id, account_id, module_id, module_item_id, item_permissions', false )
						->where( [ 'account_id'=>$account_id, 'user_id'=> 2 ] )
						->get( 'user_module_item_permissions' );

						foreach( $module_items->result_array() as $key =>$perm ){
							$perm['user_id'] 	= $user_id;
							$mod_item_perms[$key] = $perm;
						}*/

				}

				//Batch insert
				if( !empty( $mod_array ) ){
					$this->db->insert_batch( 'user_module_access', $mod_array );

					if( ( $this->db->trans_status() !== false ) && !empty( $mod_item_perms ) ){
						$this->db->insert_batch( 'user_module_item_permissions', $mod_item_perms );
					}
				}
				$result = ( $this->db->trans_status() !== false ) ? true : false;
			}
		}
		return $result;
	}


	/*
	*	The function to create a health log against the person's record
	*/
	public function create_health_log( $account_id = false, $person_id = false, $post_data = false ){

		$result = false;

		if( !empty( $account_id ) && !empty( $person_id ) && !empty( $post_data ) ){

			## validate the postdata
			$data = [];
			foreach( $post_data as $key => $value ){
				if( in_array( $key, format_name_columns() ) ){
					$value = format_name( $value );
				} elseif( in_array( $key, format_email_columns() ) ){
					$value = format_email( $value );
				} elseif( in_array( $key, format_number_columns() ) ){
					$value = format_number( $value );
				} elseif ( in_array( $key, format_boolean_columns() ) ){
					$value = format_boolean( $value );
				} elseif ( in_array( $key, format_date_columns() ) ){
					$value = format_date_db( $value );
				} elseif( in_array( $key, format_long_date_columns() ) ){
					$value = format_datetime_db( $value );
				} else {
					$value = trim( $value );
				}
				$data[$key] = $value;
			}

			$data['account_id']		= $account_id;
			$data['person_id']		= $person_id;
			$data['log_status']		= ( !empty( $data['log_status'] ) ) ? $data['log_status'] : 'Pending' ;
			$data['created_date'] 	= date( 'Y-m-d H:i:s' );
			$data['created_by'] 	= $this->ion_auth->_current_user()->id;

			$data = $this->ssid_common->_filter_data( 'people_health_log', $data );
			$this->db->insert( 'people_health_log', $data );

			if( ( $this->db->trans_status() !== FALSE ) && ( $this->db->affected_rows() > 0 ) ){
				$data['health_log'] = $this->db->insert_id();
				
				## create a log
				$log_history_data = [
					"log_type" 			=> "health",
					"entry_id" 			=> $data['health_log'],
					"person_id" 		=> $person_id,
					"action" 			=> "add a new health log",
					"previous_values" 	=> NULL,
					"current_values" 	=> json_encode( $data ),
				];
				
				$succ_log 	= $this->create_people_history_change_log( $account_id, $person_id, $log_history_data );
				
				$this->session->set_flashdata( 'message', 'Health Log has been created successfully.' );
				$result = $data;
			}
		} else {
			$this->session->set_flashdata( 'message', 'No Person ID or Account or correct data supplied.' );
		}

		return $result;
	}


	/*
	*	The function to get a health log against the person's record
	*/
	public function get_health_log( $account_id = false, $person_id = false, $health_log_id = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET, $order_by = false ){
		$result = false;

		if( !empty( $account_id ) ){
			$this->db->select( "phl.*", false );
			$this->db->select( "concat( u.first_name,' ',u.last_name ) as `created_by_full_name`", false );
			$this->db->select( "concat( u1.first_name,' ',u1.last_name ) as `last_modified_by_full_name`", false );
			$this->db->select( "phqr.q_result_name", false );
			$this->db->select( "phqt.q_type_name", false );


			if( !empty( $person_id ) ){
				$this->db->where( "phl.person_id", $person_id );
			}

			if( !empty( $health_log_id ) ){
				$this->db->where( "phl.health_log_id", $health_log_id );
			}

			/*
			if( !empty( $where ) ){
				if( is_object( $where ) ){
					$where = get_object_vars( $where );
				}
				$this->db->where( $where );
			}  */

			$this->db->join( "user u", "u.id = phl.created_by", "left" );
			$this->db->join( "user u1", "u1.id = phl.last_modified_by", "left" );
			$this->db->join( "people_health_questionnaire_result `phqr`", "phqr.q_result_id = phl.medical_qnaire_result_id", "left" );
			$this->db->join( "people_health_questionnaire_type `phqt`", "phqt.q_type_id = phl.medical_qnaire_type_id", "left" );

			$arch_where = "( phl.archived != 1 or phl.archived is NULL )";
			$this->db->where( $arch_where );

			$this->db->where( "phl.account_id", $account_id );

			if( !empty( $order_by ) ){
				$this->db->order_by( $order_by );
			} else {
				$this->db->order_by( 'phl.health_log_id DESC' );
			}

			$query = $this->db->get( "people_health_log `phl`", $limit, $offset );

			if( !empty( $query->num_rows() ) && ( $query->num_rows() > 0 ) ){
				$result 	= $query->result();
				$this->session->set_flashdata( 'message','Health Log(s) data found.' );

			} else {
				$this->session->set_flashdata( 'message','Health Log(s) data not found.' );
			}
		} else {
			$this->session->set_flashdata( 'message','No Account details provided.' );
		}

		return $result;
	}



	/*
	*	The function to create a health log against the person's record
	*/
	public function create_event( $account_id = false, $person_id = false, $post_data = false ){

		$result = false;

		if( !empty( $account_id ) && !empty( $person_id ) && !empty( $post_data ) ){

			## validate the postdata
			$data = [];
			foreach( $post_data as $key => $value ){
				if( in_array( $key, format_name_columns() ) ){
					$value = format_name( $value );
				} elseif( in_array( $key, format_email_columns() ) ){
					$value = format_email( $value );
				} elseif( in_array( $key, format_number_columns() ) ){
					$value = format_number( $value );
				} elseif ( in_array( $key, format_boolean_columns() ) ){
					$value = format_boolean( $value );
				} elseif ( in_array( $key, format_date_columns() ) ){
					$value = format_date_db( $value );
				} elseif( in_array( $key, format_long_date_columns() ) ){
					$value = format_datetime_db( $value );
				} else {
					$value = trim( $value );
				}
				$data[$key] = $value;
			}

			$data['account_id']		= $account_id;
			$data['event_status']	= 'Pending';
			$data['person_id']		= $person_id;
			$data['created_date'] 	= date( 'Y-m-d H:i:s' );
			$data['created_by'] 	= $this->ion_auth->_current_user()->id;


			$data = $this->ssid_common->_filter_data( 'people_events', $data );
			$this->db->insert( 'people_events', $data );

			if( ( $this->db->trans_status() !== FALSE ) && ( $this->db->affected_rows() > 0 ) ){
				
				$new_event_id = $this->db->insert_id();
				$result = $this->db->get_where( "people_events", ["event_id" => $new_event_id] )->row();
				
				## create a log
				$log_history_data = [
					"log_type" 			=> "events",
					"entry_id" 			=> $new_event_id,
					"person_id" 		=> $person_id,
					"action" 			=> "create a new event",
					"previous_values" 	=> NULL,
					"current_values" 	=> json_encode( $data ),
				];
				
				$succ_log 	= $this->create_people_history_change_log( $account_id, $person_id, $log_history_data );

				$this->session->set_flashdata( 'message', 'New Event has been created successfully.' );
			}
		} else {
			$this->session->set_flashdata( 'message', 'No Person ID or Account or correct data supplied.' );
		}
		return $result;
	}


	/*
	*	Function to get event types for specific account_id. If they aren't exists get the default ones
	*/
	public function get_event_types( $account_id = false, $event_type_id = false, $event_category_id = false, $ordered = false ){
		$result = false;

		if( !empty( $event_type_id ) ){
			$select = "SELECT * FROM people_event_types WHERE ( is_active = 1 )  AND ( event_type_id = $event_type_id )";
		} else if( !empty( $event_category_id ) ){
			$select = "SELECT * FROM people_event_types WHERE event_category_id = $event_category_id and account_id = $account_id";
		} else {
			$select = "SELECT * FROM people_event_types WHERE ( is_active = 1 ) AND ( account_id = $account_id )
						UNION ALL SELECT * FROM people_event_types WHERE ( is_active = 1 ) AND ( account_id = 0 )
					AND NOT EXISTS
						( SELECT 1 FROM people_event_types WHERE ( is_active = 1 ) AND ( account_id = $account_id ) )";
		}
		$query = $this->db->query( $select );

		if( $query->num_rows() > 0 ){
			$this->session->set_flashdata( 'message','Event Type(s) found.' );
 			$ordered = format_boolean( $ordered );
			if( $ordered ){
				foreach( $query->result_array() as $key => $row ){
					$result[$row['event_category_id']][$row['event_type_id']] = $row;
				}
			} else {
				$result = $query->result_array();
			}
		} else {
			$this->session->set_flashdata( 'message','Event Type(s) not found.' );
		}
		return $result;
	}


	/*
	*	Get Event(s)
	*/
	public function get_events( $account_id = false, $event_id = false, $person_id = false, $event_type_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = 0, $order_by = false ){
		$result = false;

		if( !empty( $account_id ) ){


			$this->db->select( "pe.*", false);
			$this->db->select( "concat( u.first_name, ' ', u.last_name ) as `created_by_fullname`" );
			$this->db->select( "concat( u1.first_name, ' ', u1.last_name ) as `event_supervisor_fullname`" );
			$this->db->select( "concat( u2.first_name, ' ', u2.last_name ) as `last_modify_by_fullname`" );
			$this->db->select( "concat( u3.first_name, ' ', u3.last_name ) as `person_fullname`" );
			$this->db->select( "pet.event_type_name" );
			$this->db->select( "pec.category_name" );

			if( !empty( $event_id ) ){
				$this->db->where( "pe.event_id", $event_id );
			}

			if( !empty( $person_id ) ){
				$this->db->where( "pe.person_id", $person_id );
			}

			if( !empty( $event_type_id ) ){
				$this->db->where( "pe.event_type_id", $event_type_id );
			}

			if( !empty( $event_type_id ) ){
				$this->db->where( "pe.event_type_id", $event_type_id );
			}

			if( !empty( $where ) ){
				if( is_object( $where ) ){
					$where = get_object_vars( $where );
				}
				$this->db->where( $where );
			}

			$this->db->join( "user u3", "u3.id = pe.person_id", "left" );
			$this->db->join( "user u", "u.id = pe.created_by", "left" );
			$this->db->join( "user u1", "u1.id = pe.event_supervisor_id", "left" );
			$this->db->join( "user u2", "u2.id = pe.last_modified_by", "left" );
			$this->db->join( "people_event_types `pet`", "pet.event_type_id = pe.event_type_id", "left" );
			$this->db->join( "people_event_categories `pec`", "pe.event_category_id = pec.category_id", "left" );


			$arch_where = "( pe.archived != 1 or pe.archived is NULL )";
			$this->db->where( $arch_where );

			$this->db->where( "pe.account_id", $account_id );

			if( !empty( $order_by ) ){
				$this->db->order_by( $order_by );
			} else {
				$this->db->order_by( "pe.event_id DESC" );
			}

			$query = $this->db->get( "people_events `pe`", $limit, $offset );

			if( !empty( $query->num_rows() ) && ( $query->num_rows() > 0 ) ){
				$result 	= $query->result();

				if( !empty( $vehicle_id ) || !empty( $vehicle_reg ) ){
					foreach( $result as $key => $value ){
						$people_events_log 					= $this->get_people_events_log( $account_id, $event_log_id, $person_id );
						$result[$key]->people_events_log 	= ( !empty( $people_events_log ) ) ? $people_events_log : NULL ;
					}
				}

				$this->session->set_flashdata( 'message','Event(s) data found.' );

			} else {
				$this->session->set_flashdata( 'message','Event(s) data not found.' );
			}
		} else {
			$this->session->set_flashdata( 'message','No Account details provided.' );
		}

		return $result;
	}



	/*
	*	Function to get People -> Health Questionnaire types for specific account_id. If they aren't exists get the default ones
	*/
	public function get_q_types( $account_id = false, $q_type_id = false, $q_type_group = false, $ordered = false ){
		$result = false;

		if( !empty( $account_id ) ){
			if( !empty( $q_type_id ) ){
				$select = "SELECT * FROM people_health_questionnaire_type WHERE ( is_active = 1 )  AND ( q_type_id = $q_type_id )";

				if( !empty( $q_type_group ) ){
					$select .= " AND ( q_type_group = $q_type_group )";
				}
			} else {
				$select = "SELECT * FROM people_health_questionnaire_type WHERE ( is_active = 1 ) AND ( account_id = $account_id )";
				if( !empty( $q_type_group ) ){
					$select .= " AND ( q_type_group = $q_type_group )";
				}
				$select .= "UNION ALL SELECT * FROM people_health_questionnaire_type WHERE ( is_active = 1 ) AND ( account_id = 0 )";
				if( !empty( $q_type_group ) ){
					$select .= " AND ( q_type_group = $q_type_group )";
				}
				$select .= "AND NOT EXISTS";
				$select .= "( SELECT 1 FROM people_health_questionnaire_type WHERE ( is_active = 1 ) AND ( account_id = $account_id ) )";
				if( !empty( $q_type_group ) ){
					$select .= " AND ( q_type_group = $q_type_group )";
				}
			}
			$query = $this->db->query( $select );

			if( $query->num_rows() > 0 ){
				$this->session->set_flashdata( 'message','Questionnaire Type(s) found.' );
				$ordered = format_boolean( $ordered );
				if( $ordered ){
					foreach( $query->result_array() as $key => $row ){
						$result[$row['q_type_id']] = $row;
					}
				} else {
					$result = $query->result_array();
				}
			} else {
				$this->session->set_flashdata( 'message','Questionnaire Type(s) not found.' );
			}
		} else {
			$this->session->set_flashdata( 'message','No Account ID provided.' );
		}
		return $result;
	}


	/*
	*	Function to get People -> Health Questionnaire result for specific account_id. If they aren't exists get the default ones
	*/
	public function get_q_result( $account_id = false, $q_result_id = false, $q_result_group = false, $ordered = false ){
		$result = false;

		if( !empty( $account_id ) ){
			if( !empty( $q_result_id ) ){
				$select = "SELECT * FROM people_health_questionnaire_result WHERE ( is_active = 1 )  AND ( q_result_id = $q_result_id )";

				if( !empty( $q_result_group ) ){
					$select .= " AND ( q_result_group = $q_result_group )";
				}
			} else {
				$select = "SELECT * FROM people_health_questionnaire_result WHERE ( is_active = 1 ) AND ( account_id = $account_id )";
				if( !empty( $q_result_group ) ){
					$select .= " AND ( q_result_group = $q_result_group )";
				}
				$select .= "UNION ALL SELECT * FROM people_health_questionnaire_result WHERE ( is_active = 1 ) AND ( account_id = 0 )";
				if( !empty( $q_result_group ) ){
					$select .= " AND ( q_result_group = $q_result_group )";
				}
				$select .= "AND NOT EXISTS";
				$select .= "( SELECT 1 FROM people_health_questionnaire_result WHERE ( is_active = 1 ) AND ( account_id = $account_id ) )";
				if( !empty( $q_result_group ) ){
					$select .= " AND ( q_result_group = $q_result_group )";
				}
			}
			$query = $this->db->query( $select );

			if( $query->num_rows() > 0 ){
				$this->session->set_flashdata( 'message','Questionnaire Result(s) found.' );
				$ordered = format_boolean( $ordered );
				if( $ordered ){
					foreach( $query->result_array() as $key => $row ){
						$result[$row['q_result_id']] = $row;
					}
				} else {
					$result = $query->result_array();
				}
			} else {
				$this->session->set_flashdata( 'message','Questionnaire Result(s) not found.' );
			}
		} else {
			$this->session->set_flashdata( 'message','No Account ID provided.' );
		}
		return $result;

	}


	/*
	*	Update Health Log
	*/
	public function update_health_log( $account_id = false, $health_log_id = false, $health_log_data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $health_log_id ) && !empty( $health_log_data ) ){
			$data = [];

			foreach( $health_log_data as $key => $value ){
				if( in_array( $key, format_name_columns() ) ){
					$value = format_name( $value );
				} elseif( in_array( $key, format_email_columns() ) ){
					$value = format_email( $value );
				}elseif( in_array( $key, format_number_columns() ) ){
					$value = format_number( $value );
				} elseif ( in_array( $key, format_boolean_columns() ) ){
					$value = format_boolean( $value );
				} elseif ( in_array( $key, format_date_columns() ) ){
					$value = format_date_db( $value );
				} elseif( in_array( $key, format_long_date_columns() ) ){
					$value = format_datetime_db( $value );
				} else {
					$value = trim( $value );
				}
				$data[$key] = $value;
			}

			$data['modified_date'] 	= date( 'Y-m-d H:i:s' );
			$data['modified_by'] 	= $this->ion_auth->_current_user()->id;

			if( !empty( $data ) ){
				$log4update = $this->db->get_where( "people_health_log", ["health_log_id" => $health_log_id] )->row();
				
				$data =  $this->ssid_common->_filter_data( 'people_health_log', $data );
				$restricted_columns = ['created_by', 'created_date', 'archived'];
				foreach( $data as $key => $value ){
					if( in_array( $key, $restricted_columns ) ){
						unset( $data[$key] );
					}
				}

				$this->db->where( 'health_log_id', $health_log_id )->update( 'people_health_log', $data );

				if( ( $this->db->trans_status() !== FALSE ) && ( $this->db->affected_rows() > 0 ) ){
					$result = $this->get_health_log( $account_id, false, $health_log_id );

					## create a log
					$log_history_data = [
						"log_type" 			=> "health",
						"entry_id" 			=> $health_log_id,
						"person_id" 		=> $log4update->person_id,
						"action" 			=> "update a health log",
						"previous_values" 	=> json_encode( $log4update ),
						"current_values" 	=> json_encode( $result ),
					];
					$succ_log 	= $this->create_people_history_change_log( $account_id, $person_id, $log_history_data );
				
					$this->session->set_flashdata( 'message','Health Log updated successfully.' );

				} else {
					$this->session->set_flashdata( 'message','The Health Log hasn\'t been changed.' );
				}

			}
		} else {
			$this->session->set_flashdata( 'message','No Account ID, no Health Log Id or no new data supplied.' );
		}
		return $result;
	}


	/*
	*	To retrieve the health log notes
	*/
	public function get_health_log_notes( $account_id = false, $person_id = false, $health_note_id = false, $health_log_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = false, $order_by = false ){
		$result = false;
		if( !empty( $account_id ) ){

			$this->db->select( "phln.*, concat( u.first_name,' ', u.last_name ) as `created_by_full_name`" );

			if( !empty( $health_note_id ) ){
				$this->db->where( "phln.health_note_id", $health_note_id );
			}

			if( !empty( $person_id ) ){
				$this->db->where( "phln.person_id", $person_id );
			}


			if( !empty( $health_log_id ) ){
				$this->db->where( "phln.health_log_id", $health_log_id );
			}

			if( !empty( $where ) ){
				if( is_object( $where ) ){
					$where = get_object_vars( $where );
				}
				$this->db->where( $where );
			}

			$this->db->join( "user u", "u.id = phln.created_by", "left" );

			$arch_where = "( phln.archived != 1 or phln.archived is NULL )";
			$this->db->where( $arch_where );

			$this->db->where( "phln.account_id", $account_id );

			$this->db->order_by( "phln.health_log_id DESC, phln.health_note_id DESC" );

			$query = $this->db->get( "people_health_log_notes `phln`", $limit, $offset );

			if( !empty( $query->num_rows() ) && ( $query->num_rows() > 0 ) ){
				foreach( $query->result() as $row ){
					$result[$row->health_log_id][] = $row;
				}

				$this->session->set_flashdata( 'message','Health Log Note(s) data found.' );

			} else {
				$this->session->set_flashdata( 'message','Health Log Note(s) data not found.' );
			}

		} else {
			$this->session->set_flashdata( 'message','No Account ID, no Health Log Id or no new data supplied.' );
		}

		return $result;
	}


	/*
	*	The function to create a health log against the person's record
	*/
	public function create_health_log_note( $account_id = false, $health_log_id = false, $person_id = false, $post_data = false ){

		$result = false;

		if( !empty( $account_id ) && !empty( $health_log_id ) && !empty( $post_data ) ){

			## validate the postdata
			$data = [];
			foreach( $post_data as $key => $value ){
				if( in_array( $key, format_name_columns() ) ){
					$value = format_name( $value );
				} elseif( in_array( $key, format_email_columns() ) ){
					$value = format_email( $value );
				} elseif( in_array( $key, format_number_columns() ) ){
					$value = format_number( $value );
				} elseif ( in_array( $key, format_boolean_columns() ) ){
					$value = format_boolean( $value );
				} elseif ( in_array( $key, format_date_columns() ) ){
					$value = format_date_db( $value );
				} elseif( in_array( $key, format_long_date_columns() ) ){
					$value = format_datetime_db( $value );
				} else {
					$value = trim( $value );
				}
				$data[$key] = $value;
			}

			$data['account_id']		= ( !empty( $post_data->account_id ) ) ? ( int ) $post_data->account_id : $account_id ;
			$data['health_log_id']	= ( !empty( $post_data->health_log_id ) ) ? ( int ) $post_data->health_log_id : $health_log_id ;
			$data['person_id']		= ( !empty( $post_data->person_id ) ) ? ( int ) $post_data->person_id : $person_id ;
			$data['date_created'] 	= date( 'Y-m-d H:i:s' );
			$data['created_by'] 	= $this->ion_auth->_current_user()->id;


			$data = $this->ssid_common->_filter_data( 'people_health_log_notes', $data );
			$this->db->insert( 'people_health_log_notes', $data );

			if( ( $this->db->trans_status() !== FALSE ) && ( $this->db->affected_rows() > 0 ) ){
				$new_log_id = $this->db->insert_id();
				$result = $this->db->get_where( "people_health_log_notes", ["health_note_id" => $new_log_id] )->row();
				
				## create a log
				$log_history_data = [
					"log_type" 			=> "health",
					"entry_id" 			=> $new_log_id,
					"person_id" 		=> $data['person_id'],
					"action" 			=> "create a new note for the health log",
					"previous_values" 	=> NULL,
					"current_values" 	=> json_encode( $data ),
				];
				$succ_log 	= $this->create_people_history_change_log( $account_id, $person_id, $log_history_data );
				
				$this->session->set_flashdata( 'message', 'New Comment has been added successfully.' );
			}
		} else {
			$this->session->set_flashdata( 'message', 'No Health Log ID or Account or correct data supplied.' );
		}
		return $result;
	}


	/*
	*	Update Event
	*/
	public function update_event( $account_id = false, $event_id = false, $event_data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $event_id ) && !empty( $event_data ) ){
			$data = [];

			foreach( $event_data as $key => $value ){
				if( in_array( $key, format_name_columns() ) ){
					$value = format_name( $value );
				} elseif( in_array( $key, format_email_columns() ) ){
					$value = format_email( $value );
				}elseif( in_array( $key, format_number_columns() ) ){
					$value = format_number( $value );
				} elseif ( in_array( $key, format_boolean_columns() ) ){
					$value = format_boolean( $value );
				} elseif ( in_array( $key, format_date_columns() ) ){
					$value = format_date_db( $value );
				} elseif( in_array( $key, format_long_date_columns() ) ){
					$value = format_datetime_db( $value );
				} else {
					$value = trim( $value );
				}
				$data[$key] = $value;
			}

			$data['last_modified'] 		= date( 'Y-m-d H:i:s' );
			$data['last_modified_by'] 	= $this->ion_auth->_current_user()->id;

			if( !empty( $data ) ){
				
				$event4update = $this->db->get_where( "people_events", ["event_id" => $event_id ] )->row();
				
				$data =  $this->ssid_common->_filter_data( 'people_events', $data );
				$restricted_columns = ['created_by', 'created_date', 'archived'];
				foreach( $data as $key => $value ){
					if( in_array( $key, $restricted_columns ) ){
						unset( $data[$key] );
					}
				}
				
				if( !empty( $data['event_category_id'] ) && in_array( $data['event_category_id'], [7,8] ) ){
					$data['event_title'] = NULL ;
				} else {
					$data['event_type_id'] = NULL ;
				}

				$this->db->where( 'event_id', $event_id )->update( 'people_events', $data );

				if( ( $this->db->trans_status() !== FALSE ) && ( $this->db->affected_rows() > 0 ) ){

					$result = $this->get_events( $account_id, $event_id );

					## create a log
					$log_history_data = [
						"log_type" 			=> "events",
						"entry_id" 			=> $event_id,
						"person_id" 		=> $event4update->person_id,
						"action" 			=> "update an event",
						"previous_values" 	=> json_encode( $event4update ),
						"current_values" 	=> json_encode( $data ),
					];
					
					$succ_log 	= $this->create_people_history_change_log( $account_id, $event4update->person_id, $log_history_data );

					$this->session->set_flashdata( 'message','The Event has been updated successfully.' );

				} else {
					$this->session->set_flashdata( 'message','The Event hasn\'t been changed.' );
				}

			}
		} else {
			$this->session->set_flashdata( 'message','No Account ID, no Event Id or no new data supplied.' );
		}
		return $result;
	}


	/*
	*	The function to produce the simple statistics for the People manager.
	* 	Account ID required
	*/
	public function get_simple_stats( $account_id = false ){
		$result = false;

		if( !empty( $account_id ) ){
			$this->db->select( "
				SUM( CASE when p.employment_status = 'employee' THEN 1 ELSE 0 END ) as `employees`,
				SUM( CASE when p.employment_status = 'subcontractor' THEN 1 ELSE 0 END ) as `subcontractors`,
				SUM( CASE when p.status_id IN ( 1 ) THEN 1 ELSE 0 END ) as `active`,
			", false );

			$this->db->where( "p.account_id", $account_id );
			$this->db->order_by( "p.person_id DESC" );

			$query = $this->db->get( "people p" );

			if( !empty( $query->num_rows() ) && ( $query->num_rows() > 0 ) ){
				$result = $query->result();
				$this->session->set_flashdata( 'message','People stats found' );
			} else {
				$this->session->set_flashdata( 'message','No Instant Stats are available at the moment.' );
			}

		} else {
			$this->session->set_flashdata( 'message','No Account details provided.' );
		}
		return $result;
	}
	
	
	/*
	*	Function to create a log after any action on People module
	*/
	public function create_people_history_change_log( $account_id = false, $person_id = false, $log_data = false ){
		if( !empty( $account_id ) && !empty( $log_data ) ){
			$data["account_id"] 	= $account_id;
			$data["created_by"] 	= $this->ion_auth->_current_user()->id;
			$data["created_date"] 	= date( 'Y-m-d H:i:s' );

			$data = array_merge( $data, $log_data );
			
			$data = $this->ssid_common->_filter_data( "people_history_change_log", $data );
			$this->db->insert( "people_history_change_log", $data );
			
			if( $this->db->affected_rows() > 0 ){
				## $this->session->set_flashdata( 'message','The action has been succesfully Logged in the System.' );
				return $this->db->insert_id();
			} else {
				## $this->session->set_flashdata( 'message','The action has NOT been Logged in the System.' );
				return false;
			}

		} else {
			$this->session->set_flashdata( 'message','No Account details or no Log Data provided.' );
		}
	}


	/*
	*	To retrieve the person history logs
	*/
	public function get_person_change_logs( $account_id = false, $person_id = false, $change_log_id = false, $log_type = false, $where = false, $limit = DEFAULT_LIMIT, $offset = false, $order_by = false ){
		$result = false;
		if( !empty( $account_id ) ){

			$this->db->select( "phcl.*, concat( u.first_name,' ', u.last_name ) as `created_by_full_name`" );

			if( !empty( $change_log_id ) ){
				$this->db->where( "phcl.change_log_id", $change_log_id );
			}

			if( !empty( $person_id ) ){
				$this->db->where( "phcl.person_id", $person_id );
			}

			if( !empty( $log_type ) ){
				$this->db->where( "phcl.log_type", $log_type );
			}

			if( !empty( $where ) ){
				if( is_object( $where ) ){
					$where = get_object_vars( $where );
				}
				$this->db->where( $where );
			}

			$this->db->join( "user u", "u.id = phcl.created_by", "left" );

			$arch_where = "( phcl.archived != 1 or phcl.archived is NULL )";
			$this->db->where( $arch_where );

			$this->db->where( "phcl.account_id", $account_id );

			$this->db->order_by( "phcl.change_log_id DESC" );

			$query = $this->db->get( "people_history_change_log `phcl`", $limit, $offset );

			if( !empty( $query->num_rows() ) && ( $query->num_rows() > 0 ) ){
				$result = $query->result();
				$this->session->set_flashdata( 'message','Person History Log(s) data found.' );

			} else {
				$this->session->set_flashdata( 'message','Person History Log(s) data not found.' );
			}

		} else {
			$this->session->set_flashdata( 'message','No Account ID, no Health Log Id or no new data supplied.' );
		}

		return $result;
	}


	
	/*
	*	The function to get checklist questions
	* 	Account ID required
	*/
	public function get_checklist_questions( $account_id = false, $question_id = false, $category = false, $item_type = false, $item_group = false, $where = false, $limit = 999, $offset = false, $order_by = false, $ordered = false ){
		$result = false;
		if( !empty( $account_id ) ){

			$this->db->select( "pcq.*, concat( u.first_name,' ', u.last_name ) as `created_by_full_name`" );

			if( !empty( $question_id ) ){
				$this->db->where( "pcq.question_id", $question_id );
			}

			if( !empty( $category ) ){
				$this->db->where( "pcq.category", $category );
			}

			if( !empty( $item_type ) ){
				$this->db->where( "pcq.item_type", $item_type );
			}

			if( !empty( $item_group ) ){
				$this->db->where( "pcq.item_group", $item_group );
			}

			if( !empty( $where ) ){
				if( is_object( $where ) ){
					$where = get_object_vars( $where );
				}
				$this->db->where( $where );
			}

			$this->db->join( "user u", "u.id = pcq.created_by", "left" );

			$this->db->where( "pcq.is_active", 1 );
			$this->db->where( "pcq.account_id", $account_id );

			if( !empty( $order_by ) ){
				$this->db->order_by( $order_by );
			} else {
				$this->db->order_by( "pcq.category DESC, pcq.item_type DESC, pcq.item_group, pcq.item_order ASC" );
			}

			$query = $this->db->get( "people_checklist_questions `pcq`", $limit, $offset );

			if( !empty( $query->num_rows() ) && ( $query->num_rows() > 0 ) ){
				
				if( !empty( $ordered ) ){
					foreach( $query->result() as $row ){
						$result[$row->category][$row->item_type][$row->item_group][] = $row;
					}
				} else {
					$result = $query->result();
				}

				$this->session->set_flashdata( 'message','Checklist question(s) found.' );

			} else {
				$this->session->set_flashdata( 'message','Checklist question(s) not found.' );
			}

		} else {
			$this->session->set_flashdata( 'message','No Account ID supplied.' );
		}

		return $result;
	}

	
	/*
	*	Update Checklist
	*/
	public function update_checklist( $account_id = false, $response_id = false, $person_id = false, $category = false, $item_type = false, $answers = false ){
			$result = false;
	
		if( !empty( $account_id ) && !empty( $person_id ) && !empty( $category ) && !empty( $item_type ) && !empty( $answers ) ){
			
			$checklist_exists = $result = $response_id = false;

			$data = [
				"account_id" 	=> $account_id,
				"person_id" 	=> $person_id,
				"category" 		=> $category,
				"item_type" 	=> $item_type,
				"answers" 		=> $answers,
			];
			
			$checklist_exists = $this->db->get_where( "people_checklist_responses", [ "account_id" => $account_id, "person_id" => $person_id, "category" => $category, "item_type" => $item_type ] )->row();
			
			if( !empty( $checklist_exists ) ){
				
				$data['last_modified'] 		= date( 'Y-m-d H:i:s' );
				$data['last_modified_by'] 	= $this->ion_auth->_current_user()->id;
				
				$data =  $this->ssid_common->_filter_data( 'people_checklist_responses', $data );
				$query = $this->db->update( "people_checklist_responses", $data, [ "response_id" => $checklist_exists->response_id ] );
				$response_id = $checklist_exists->response_id;
				
			} else {

				$data["created_date"] 	= date( 'Y-m-d H:i:s' );
				$data["created_by"] 	= $this->ion_auth->_current_user()->id;
				
				$data =  $this->ssid_common->_filter_data( 'people_checklist_responses', $data );
				$query = $this->db->insert( "people_checklist_responses", $data );
				$response_id = $this->db->insert_id();
			}

			if( ( $this->db->trans_status() !== FALSE ) && ( $this->db->affected_rows() > 0 ) ){
				
				## create a history log
				$log_history_data = [
					"log_type" 			=> ( ( $data['category'] == "training" ) ? "training" : "checklist" ),
					"entry_id" 			=> $response_id,
					"person_id" 		=> $person_id,
					"action" 			=> "update the ".( ( $data['category'] == "training" ) ? "training" : "checklist" ),
					"previous_values" 	=> ( !empty( $checklist_exists ) ) ? json_encode( $checklist_exists ) : NULL ,
					"current_values" 	=> json_encode( $data ),
				];
				
				$succ_log 	= $this->create_people_history_change_log( $account_id, $person_id, $log_history_data );

				$result = $this->db->get_where( "people_checklist_responses", ["response_id" => $response_id] )->row();
				$this->session->set_flashdata( 'message','The Operation has been processed successfully.' );

			} else {
				$this->session->set_flashdata( 'message','The Checklist hasn\'t been changed.' );
			}

		} else {
			$this->session->set_flashdata( 'message','No Account ID, no Person Id, No category, No Item Type or no Answers data supplied.' );
		}
		
		return $result;
	}
	

	/*
	*	The function to get checklist answers
	* 	Account ID, Person ID required
	*/
	public function get_checklist_answers( $account_id = false, $person_id = false, $category = false, $response_id = false, $where = false, $limit = 999, $offset = false, $ordered = false ){
		$result = false;

		if( !empty( $account_id ) && !empty( $person_id ) ){

			$this->db->select( "pcr.*" );
			$this->db->select( "concat( u1.first_name,' ', u1.last_name ) as `created_by_full_name`" );
			$this->db->select( "concat( u2.first_name,' ', u2.last_name ) as `last_modified_by_full_name`" );

			if( !empty( $person_id ) ){
				$this->db->where( "pcr.person_id", $person_id );
			}
			if( !empty( $category ) ){
				$this->db->where( "pcr.category", $category );
			}

			if( !empty( $response_id ) ){
				$this->db->where( "pcr.response_id", $response_id );
			}

			if( !empty( $where ) ){
				if( is_object( $where ) ){
					$where = get_object_vars( $where );
				}
				$this->db->where( $where );
			}

			$this->db->join( "user u1", "u1.id = pcr.created_by", "left" );
			$this->db->join( "user u2", "u2.id = pcr.last_modified_by", "left" );

			$arch_where = "( pcr.archived != 1 or pcr.archived is NULL )";
			$this->db->where( $arch_where );
			
			$this->db->where( "pcr.account_id", $account_id );

			if( !empty( $order_by ) ){
				$this->db->order_by( $order_by );
			} else {
				$this->db->order_by( "pcr.response_id ASC" );
			}

			$query = $this->db->get( "people_checklist_responses `pcr`", $limit, $offset );

			if( !empty( $query->num_rows() ) && ( $query->num_rows() > 0 ) ){
				
				if( !empty( $ordered ) ){
					foreach( $query->result() as $row ){
						$result[$row->category][$row->item_type][] = $row;
					}
				} else {
					$result = $query->result();
				}

				$this->session->set_flashdata( 'message','Checklist answers found.' );

			} else {
				$this->session->set_flashdata( 'message','Checklist answers not found.' );
			}

		} else {
			$this->session->set_flashdata( 'message','No Account ID or Person ID supplied.' );
		}

		return $result;
	}
	

	/*
	*	The function to create a security screening log against the person's record
	*/
	public function create_security_log( $account_id = false, $person_id = false, $post_data = false ){

		$result = false;

		if( !empty( $account_id ) && !empty( $person_id ) && !empty( $post_data ) ){

			## validate the postdata
			$data = [];
			foreach( $post_data as $key => $value ){
				if( in_array( $key, format_name_columns() ) ){
					$value = format_name( $value );
				} elseif( in_array( $key, format_email_columns() ) ){
					$value = format_email( $value );
				} elseif( in_array( $key, format_number_columns() ) ){
					$value = format_number( $value );
				} elseif ( in_array( $key, format_boolean_columns() ) ){
					$value = format_boolean( $value );
				} elseif ( in_array( $key, format_date_columns() ) ){
					$value = format_date_db( $value );
				} elseif( in_array( $key, format_long_date_columns() ) ){
					$value = format_datetime_db( $value );
				} else {
					$value = trim( $value );
				}
				$data[$key] = $value;
			}

			$data['account_id']		= $account_id;
			$data['person_id']		= $person_id;
			$data['log_status']		= ( !empty( $data['log_status'] ) ) ? $data['log_status'] : 'Pending' ;
			$data['created_date'] 	= date( 'Y-m-d H:i:s' );
			$data['created_by'] 	= $this->ion_auth->_current_user()->id;

			$data = $this->ssid_common->_filter_data( 'people_security_screening_logs', $data );
			$this->db->insert( 'people_security_screening_logs', $data );

			if( ( $this->db->trans_status() !== FALSE ) && ( $this->db->affected_rows() > 0 ) ){
				$new_log_id = $this->db->insert_id();
				
				## create a log
				$log_history_data = [
					"log_type" 			=> "security",
					"entry_id" 			=> $new_log_id,
					"person_id" 		=> $person_id,
					"action" 			=> "add a new security screening log",
					"previous_values" 	=> NULL,
					"current_values" 	=> json_encode( $data ),
				];
				
				$succ_log 	= $this->create_people_history_change_log( $account_id, $person_id, $log_history_data );
				
				$this->session->set_flashdata( 'message', 'Security Screening Log has been created successfully.' );
				$result 	= $this->db->get_where( "people_security_screening_logs", [ "log_id" => $new_log_id ] );
			}
		} else {
			$this->session->set_flashdata( 'message', 'No Person ID or Account or correct data supplied.' );
		}

		return $result;
	}
	
	

	/*
	*	The function to get a security screening log against the person's record
	*/
	public function get_security_logs( $account_id = false, $person_id = false, $log_id = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET, $order_by = false ){
		$result = false;

		if( !empty( $account_id ) ){
			$this->db->select( "pscl.*", false );
			$this->db->select( "concat( u.first_name,' ',u.last_name ) as `created_by_full_name`", false );
			$this->db->select( "concat( u1.first_name,' ',u1.last_name ) as `last_modified_by_full_name`", false );
			$this->db->select( "concat( u2.first_name,' ',u2.last_name ) as `sent_by_full_name`", false );

			if( !empty( $person_id ) ){
				$this->db->where( "pscl.person_id", $person_id );
			}

			if( !empty( $log_id ) ){
				$this->db->where( "pscl.log_id", $log_id );
			}

			$this->db->join( "user u", "u.id = pscl.created_by", "left" );
			$this->db->join( "user u1", "u1.id = pscl.modified_by", "left" );
			$this->db->join( "user u2", "u2.id = pscl.sent_by", "left" );

			$arch_where = "( pscl.archived != 1 or pscl.archived is NULL )";
			$this->db->where( $arch_where );

			$this->db->where( "pscl.account_id", $account_id );

			if( !empty( $order_by ) ){
				$this->db->order_by( $order_by );
			} else {
				$this->db->order_by( 'pscl.log_id DESC' );
			}

			$query = $this->db->get( "people_security_screening_logs `pscl`", $limit, $offset );

			if( !empty( $query->num_rows() ) && ( $query->num_rows() > 0 ) ){
				$result 	= $query->result();
				$this->session->set_flashdata( 'message','Health Log(s) data found.' );

			} else {
				$this->session->set_flashdata( 'message','Health Log(s) data not found.' );
			}
		} else {
			$this->session->set_flashdata( 'message','No Account details provided.' );
		}

		return $result;
	}

	
	/*
	*	Update Security Screening Log
	*/
	public function update_security_log( $account_id = false, $log_id = false, $person_id = false, $post_data = false ){
			$result = false;
	
		if( !empty( $account_id ) && !empty( $person_id ) && !empty( $log_id ) && !empty( $post_data ) ){
			
			## validate the postdata
			$data = [];
			foreach( $post_data as $key => $value ){
				if( in_array( $key, format_name_columns() ) ){
					$value = format_name( $value );
				} elseif( in_array( $key, format_email_columns() ) ){
					$value = format_email( $value );
				} elseif( in_array( $key, format_number_columns() ) ){
					$value = format_number( $value );
				} elseif ( in_array( $key, format_boolean_columns() ) ){
					$value = format_boolean( $value );
				} elseif ( in_array( $key, format_date_columns() ) ){
					$value = format_date_db( $value );
				} elseif( in_array( $key, format_long_date_columns() ) ){
					$value = format_datetime_db( $value );
				} else {
					$value = trim( $value );
				}
				$data[$key] = $value;
			}
			
			$log_id_exists = $processed_log_id = false;
		
			$log_exists = $this->db->get_where( "people_security_screening_logs", [ "account_id" => $account_id, "person_id" => $person_id, "log_id" => $log_id] )->row();
			
			if( !empty( $log_exists ) ){
				
				$data['modified_date'] 	= date( 'Y-m-d H:i:s' );
				$data['modified_by'] 	= $this->ion_auth->_current_user()->id;
				
				$data =  $this->ssid_common->_filter_data( 'people_security_screening_logs', $data );
				$query = $this->db->update( "people_security_screening_logs", $data, [ "log_id" => $log_exists->log_id ] );
				
				$data['action'] 		= 'update security log';
				$data['previous_values']= json_encode( $log_exists );
				
				$processed_log_id 		= $log_exists->log_id;
				
			} else {

				$data["created_date"] 	= date( 'Y-m-d H:i:s' );
				$data["created_by"] 	= $this->ion_auth->_current_user()->id;
				
				$data =  $this->ssid_common->_filter_data( 'people_security_screening_logs', $data );
				$query = $this->db->insert( "people_security_screening_logs", $data );
				$processed_log_id = $this->db->insert_id();
				
				$data['action'] 		= 'create security log';
				$data['previous_values']= NULL;
			}

			if( ( $this->db->trans_status() !== FALSE ) && ( $this->db->affected_rows() > 0 ) ){

				$result = $this->db->get_where( "people_security_screening_logs", ["log_id" => $processed_log_id] )->row();
				
				$log_history_data = $data;
				$succ_log 	= $this->create_people_history_change_log( $account_id, $person_id, $log_history_data );

				$this->session->set_flashdata( 'message','The Security Screening Log change has been processed.' );

			} else {
				$this->session->set_flashdata( 'message','The Security Screening log hasn\'t been changed.' );
			}

		} else {
			$this->session->set_flashdata( 'message','No Account ID, no Person Id, no Log ID or no data supplied.' );
		}
		
		return $result;
	}
	
	
	/*
	*	The function to get a list of reasons for the leaver to leave the company. Account ID required. 
	*/
	public function get_leaver_reasons( $account_id = false, $reason_id = false, $ordered = false ){
		$result = false;

		if( !empty( $account_id ) ){
			$this->db->select( "plr.*", false );

			if( !empty( $reason_id ) ){
				$this->db->where( "plr.reason_id", $reason_id );
			}

			$this->db->where( "plr.is_active", 1 );
			$this->db->where( "plr.account_id", $account_id );
			$this->db->order_by( 'plr.reason_id ASC' );

			$query = $this->db->get( "people_leaver_reason `plr`" );

			if( !empty( $query->num_rows() ) && ( $query->num_rows() > 0 ) ){
				if( !empty( $ordered ) ){
					foreach( $query->result() as $key => $row ){
						$result[$row->reason_id] = $row;
					}
				} else {
					$result = $query->result();
				}
				
				$this->session->set_flashdata( 'message','Leave Reason(s) data found.' );

			} else {
				$this->session->set_flashdata( 'message','Leave Reason(s) data not found.' );
			}
		} else {
			$this->session->set_flashdata( 'message','No Account details provided.' );
		}

		return $result;
	}
	
	/*
	*	The function to log details for the leaver
	*/
	public function create_leavers_log( $account_id = false, $person_id = false, $postdata = false ){
		$result = false;
		if( !empty( $account_id ) ){
			if( !empty( $person_id ) ){
				if( !empty( $postdata ) ){

					## validate the postdata
					$data = [];
					foreach( $postdata as $key => $value ){
						if( in_array( $key, format_name_columns() ) ){
							$value = format_name( $value );
						} elseif( in_array( $key, format_email_columns() ) ){
							$value = format_email( $value );
						} elseif( in_array( $key, format_number_columns() ) ){
							$value = format_number( $value );
						} elseif ( in_array( $key, format_boolean_columns() ) ){
							$value = format_boolean( $value );
						} elseif ( in_array( $key, format_date_columns() ) ){
							$value = format_date_db( $value );
						} elseif( in_array( $key, format_long_date_columns() ) ){
							$value = format_datetime_db( $value );
						} else {
							$value = trim( $value );
						}
						$data[$key] = $value;
					}
					
					$data["created_by"] 	= $this->ion_auth->_current_user()->id;
					$data['created_date'] 	= date( "Y-m-d H:i:s" );
				
					$data =  $this->ssid_common->_filter_data( 'people_leaver_log', $data );
					$query = $this->db->insert( "people_leaver_log", $data );

					if( ( $this->db->trans_status() !== FALSE ) && ( $this->db->affected_rows() > 0 ) ){
						$processed_log_id = $this->db->insert_id();
						$result = $this->db->get_where( "people_leaver_log", ["leaver_log_id" => $processed_log_id] )->row();

						## create a log
						$log_history_data = [
							"log_type" 			=> "leaver",
							"entry_id" 			=> $processed_log_id,
							"person_id" 		=> $person_id,
							"action" 			=> "add a leaver's details log",
							"previous_values" 	=> NULL,
							"current_values" 	=> json_encode( $result ),
						];

						$succ_log 	= $this->create_people_history_change_log( $account_id, $person_id, $log_history_data );

						$this->session->set_flashdata( 'message','The Leaver Details Log change has been created.' );

					} else {
						$this->session->set_flashdata( 'message','The Leaver Details log hasn\'t been created.' );
					}
						
				} else {
					$this->session->set_flashdata( 'message','No Data provided.' );
				}
			} else {
				$this->session->set_flashdata( 'message','No Person ID provided.' );
			}
		} else {
			$this->session->set_flashdata( 'message','No Account details provided.' );
		}
		
		return $result;
	}
	
	
	/*
	*	The function is to get a logs for leavers. Required parameter: account_id. Possible parameters: person_id, leaver_log_id, ordered
	*/
	public function get_leaver_logs( $account_id = false, $person_id = false, $leaver_log_id = false, $ordered = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;

		if( !empty( $account_id ) ){
			$this->db->select( "pll.*", false );
			
			$this->db->select( "plr.reason_name", false );
			$this->db->select( "CONCAT( u1.first_name, ' ', u1.last_name) `created_by_full_name`", false );

			if( !empty( $person_id ) ){
				$this->db->where( "pll.person_id", $person_id );
			}
			
			if( !empty( $leaver_log_id ) ){
				$this->db->where( "pll.leaver_log_id", $leaver_log_id );
			}
			
			$this->db->join( "people_leaver_reason `plr`", "plr.reason_id = pll.leaving_reason_id ", "left" );
			$this->db->join( "user `u1`", "u1.id = pll.created_by", "left" );


			$this->db->where( "pll.account_id", $account_id );
			$this->db->order_by( 'pll.leaver_log_id DESC' );

			$query = $this->db->get( "people_leaver_log `pll`" );

			if( !empty( $query->num_rows() ) && ( $query->num_rows() > 0 ) ){
				if( !empty( $ordered ) ){
					foreach( $query->result() as $key => $row ){
						$result[$row->leaver_log_id] = $row;
					}
				} else {
					$result = $query->result();
				}
				
				$this->session->set_flashdata( 'message','Leave Reason(s) data found.' );

			} else {
				$this->session->set_flashdata( 'message','Leave Reason(s) data not found.' );
			}
		} else {
			$this->session->set_flashdata( 'message','No Account details provided.' );
		}

		return $result;
	}
	
	
	/*
	*	Function to get event categories for specific account_id. If they aren't exists get the default ones
	*/
	public function get_event_categories( $account_id = false, $category_id = false, $category_group = false, $ordered = false ){
		$result = false;

		if( !empty( $category_id ) ){
			$select = "SELECT * FROM people_event_categories WHERE ( is_active = 1 )  AND ( category_id = $category_id )";
		} else {
			$select = "SELECT * FROM people_event_categories WHERE ( is_active = 1 ) AND ( account_id = $account_id )
						UNION ALL SELECT * FROM people_event_categories WHERE ( is_active = 1 ) AND ( account_id = 0 )
					AND NOT EXISTS
						( SELECT 1 FROM people_event_categories WHERE ( is_active = 1 ) AND ( account_id = $account_id ) )";
		}
		$query = $this->db->query( $select );

		if( $query->num_rows() > 0 ){
			$this->session->set_flashdata( 'message','Event Category(ies) found.' );
 			$ordered = format_boolean( $ordered );
			if( $ordered ){
				foreach( $query->result_array() as $key => $row ){
					$result[$row['category_id']] = $row;
				}
			} else {
				$result = $query->result_array();
			}
		} else {
			$this->session->set_flashdata( 'message','Event Category(ies) not found.' );
		}
		return $result;
	}
	
	
	/*
	*	Update Position
	*/
	public function update_position( $account_id = false, $position_id = false, $position_data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $position_id ) && !empty( $position_data ) ){
			$data = [];

			foreach( $position_data as $key => $value ){
				if( in_array( $key, format_name_columns() ) ){
					$value = format_name( $value );
				} elseif( in_array( $key, format_email_columns() ) ){
					$value = format_email( $value );
				}elseif( in_array( $key, format_number_columns() ) ){
					$value = format_number( $value );
				} elseif ( in_array( $key, format_boolean_columns() ) ){
					$value = format_boolean( $value );
				} elseif ( in_array( $key, format_date_columns() ) ){
					$value = format_date_db( $value );
				} elseif( in_array( $key, format_long_date_columns() ) ){
					$value = format_datetime_db( $value );
				} else {
					$value = trim( $value );
				}
				$data[$key] = $value;
			}

			$data['last_modified'] 		= date( 'Y-m-d H:i:s' );
			$data['last_modified_by'] 	= $this->ion_auth->_current_user()->id;

			if( !empty( $data ) ){
				
				$position4update = $this->db->get_where( "people_job_positions", ["position_id" => $position_id ] )->row();
				
				$data =  $this->ssid_common->_filter_data( 'people_job_positions', $data );
				$restricted_columns = ['created_by', 'created_date', 'archived'];
				foreach( $data as $key => $value ){
					if( in_array( $key, $restricted_columns ) ){
						unset( $data[$key] );
					}
				}
				
				$this->db->where( 'position_id', $position_id )->update( 'people_job_positions', $data );

				if( ( $this->db->trans_status() !== FALSE ) && ( $this->db->affected_rows() > 0 ) ){

					$result = $this->get_job_positions( $account_id, false, $position_id );
					
					## create a log
					$log_history_data = [
						"log_type" 			=> "positions",
						"entry_id" 			=> $position_id,
						"person_id" 		=> $position4update->person_id,
						"action" 			=> "update a position",
						"previous_values" 	=> json_encode( $position4update ),
						"current_values" 	=> json_encode( $result ),
					];
					
					$succ_log 	= $this->create_people_history_change_log( $account_id, $position4update->person_id, $log_history_data );

					$this->session->set_flashdata( 'message','The Position has been updated successfully.' );

				} else {
					$this->session->set_flashdata( 'message','The Position hasn\'t been changed.' );
				}

			}
		} else {
			$this->session->set_flashdata( 'message','No Account ID, no Position Id or no new data supplied.' );
		}
		return $result;
	}
	
	
	/*
	*	Delete Position
	*/
	public function delete_position( $account_id = false, $position_id = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $position_id ) ){
			
			$positionb4delete = $this->db->get_where( "people_job_positions", ["position_id" => $position_id ] )->row();
			
			$data = [
				'archived'			=> 1 ,
				'last_modified_by'	=>$this->ion_auth->_current_user()->id
			];

			$query = $this->db->update( 'people_job_positions', $data, ["account_id"=>$account_id, "position_id" => $position_id] );
			
			if( $this->db->trans_status() !== FALSE && $this->db->affected_rows() > 0 ){
				
				$position_after_delete = $this->db->get_where( "people_job_positions", ["position_id" => $position_id ] )->row();
				
				## create a log
				$log_history_data = [
					"log_type" 			=> "positions",
					"entry_id" 			=> $position_id,
					"person_id" 		=> $positionb4delete->person_id,
					"action" 			=> "delete a position",
					"previous_values" 	=> json_encode( $positionb4delete ),
					"current_values" 	=> json_encode( $position_after_delete ),
				];
				
				$succ_log 	= $this->create_people_history_change_log( $account_id, $positionb4delete->person_id, $log_history_data );
				
				$this->session->set_flashdata( 'message','The Job Position has been deleted.' );
				$result = true;
			} else {
				$this->session->set_flashdata( 'message','The Job Position hasn\'t been deleted.' );
			}
		} else {
			$this->session->set_flashdata( 'message','Invalid Position ID or missing Account ID.' );
		}
		return $result;
	}
	
	
	/*
	*	Delete an Event
	*/
	public function delete_event( $account_id = false, $event_id = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $event_id ) ){
			
			$eventb4delete = $this->db->get_where( "people_events", ["event_id" => $event_id ] )->row();
			
			$data = [
				'archived'			=> 1 ,
				'last_modified_by'	=>$this->ion_auth->_current_user()->id
			];

			$query = $this->db->update( 'people_events', $data, ["account_id"=>$account_id, "event_id" => $event_id] );
			
			if( $this->db->trans_status() !== FALSE && $this->db->affected_rows() > 0 ){
				
				$event_after_delete = $this->db->get_where( "people_events", ["event_id" => $event_id ] )->row();
				
				## create a log
				$log_history_data = [
					"log_type" 			=> "events",
					"entry_id" 			=> $event_id,
					"person_id" 		=> $eventb4delete->person_id,
					"action" 			=> "delete an event",
					"previous_values" 	=> json_encode( $eventb4delete ),
					"current_values" 	=> json_encode( $event_after_delete ),
				];
				
				$succ_log 	= $this->create_people_history_change_log( $account_id, $eventb4delete->person_id, $log_history_data );				
				
				$this->session->set_flashdata( 'message','The Event has been deleted.' );
				$result = true;
			} else {
				$this->session->set_flashdata( 'message','The Event hasn\'t been deleted.' );
			}
		} else {
			$this->session->set_flashdata( 'message','Invalid Event ID or missing Account ID.' );
		}
		return $result;
	}
	
	
	/*
	*	Delete a Security log
	*/
	public function delete_security_log( $account_id = false, $log_id = false ){
		
		$result = false;
		if( !empty( $account_id ) && !empty( $log_id ) ){
			
			$sec_log_b4delete = $this->db->get_where( "people_security_screening_logs", ["log_id" => $log_id ] )->row();
			
			$data = [
				'archived'		=> 1 ,
				'modified_by'	=>$this->ion_auth->_current_user()->id
			];

			$query = $this->db->update( 'people_security_screening_logs', $data, ["account_id"=>$account_id, "log_id" => $log_id] );
			
			if( $this->db->trans_status() !== FALSE && $this->db->affected_rows() > 0 ){
				
				$sec_log_after_delete = $this->db->get_where( "people_security_screening_logs", ["log_id" => $log_id ] )->row();
				
				## create a log
				$log_history_data = [
					"log_type" 			=> "security",
					"entry_id" 			=> $log_id,
					"person_id" 		=> $sec_log_b4delete->person_id,
					"action" 			=> "delete a security log",
					"previous_values" 	=> json_encode( $sec_log_b4delete ),
					"current_values" 	=> json_encode( $sec_log_after_delete ),
				];
				
				$succ_log 	= $this->create_people_history_change_log( $account_id, $sec_log_b4delete->person_id, $log_history_data );	
				
				$this->session->set_flashdata( 'message','The Security Log has been deleted.' );
				$result = true;
			} else {
				$this->session->set_flashdata( 'message','The Security Log hasn\'t been deleted.' );
			}
		} else {
			$this->session->set_flashdata( 'message','Invalid Security Log ID or missing Account ID.' );
		}
		return $result;
	}
	
	
	
	/*
	*	Update Contact Address
	*/
	public function update_contact( $account_id = false, $contact_id = false, $contact_data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $contact_id ) && !empty( $contact_data ) ){
			$data = [];

			foreach( $contact_data as $key => $value ){
				if( in_array( $key, format_name_columns() ) ){
					$value = format_name( $value );
				} elseif( in_array( $key, format_email_columns() ) ){
					$value = format_email( $value );
				}elseif( in_array( $key, format_number_columns() ) ){
					$value = format_number( $value );
				} elseif ( in_array( $key, format_boolean_columns() ) ){
					$value = format_boolean( $value );
				} elseif ( in_array( $key, format_date_columns() ) ){
					$value = format_date_db( $value );
				} elseif( in_array( $key, format_long_date_columns() ) ){
					$value = format_datetime_db( $value );
				} else {
					$value = trim( $value );
				}
				$data[$key] = $value;
			}

			$data['last_modified'] 		= date( 'Y-m-d H:i:s' );
			$data['last_modified_by'] 	= $this->ion_auth->_current_user()->id;

			if( !empty( $data ) ){
				
				$contactb4update = $this->db->get_where( "people_contact_addresses", ["contact_id" => $contact_id ] )->row();
				
				$data =  $this->ssid_common->_filter_data( 'people_contact_addresses', $data );
				$restricted_columns = ['created_by', 'created_date', 'archived'];
				foreach( $data as $key => $value ){
					if( in_array( $key, $restricted_columns ) ){
						unset( $data[$key] );
					}
				}
				
				$this->db->where( 'contact_id', $contact_id )->update( 'people_contact_addresses', $data );

				if( ( $this->db->trans_status() !== FALSE ) && ( $this->db->affected_rows() > 0 ) ){

					$result = $this->get_address_contacts( $account_id, false, $contact_id );
					
					## create a log
					$log_history_data = [
						"log_type" 			=> "contacts",
						"entry_id" 			=> $contact_id,
						"person_id" 		=> $contactb4update->person_id,
						"action" 			=> "update a contact",
						"previous_values" 	=> json_encode( $contactb4update ),
						"current_values" 	=> json_encode( $result ),
					];
					
					$succ_log 	= $this->create_people_history_change_log( $account_id, $contactb4update->person_id, $log_history_data );

					$this->session->set_flashdata( 'message','The Contact Address has been updated successfully.' );

				} else {
					$this->session->set_flashdata( 'message','The Contact Address hasn\'t been changed.' );
				}

			}
		} else {
			$this->session->set_flashdata( 'message','No Account ID, no Contact Id or no new data supplied.' );
		}
		return $result;
	}


	/*
	*	Delete Address Contact
	*/
	public function delete_contact( $account_id = false, $contact_id = false ){
		
		$result = false;
		if( !empty( $account_id ) && !empty( $contact_id ) ){
			
			$contactb4delete = $this->db->get_where( "people_contact_addresses", ["contact_id" => $contact_id ] )->row();
			
			$data = [
				'archived'			=> 1 ,
				'last_modified_by'	=> $this->ion_auth->_current_user()->id
			];

			$query = $this->db->update( 'people_contact_addresses', $data, ["account_id" => $account_id, "contact_id" => $contact_id] );
			
			if( $this->db->trans_status() !== FALSE && $this->db->affected_rows() > 0 ){
				
				$contact_after_delete = $this->db->get_where( "people_contact_addresses", ["contact_id" => $contact_id ] )->row();
				
				## create a log
				$log_history_data = [
					"log_type" 			=> "contacts",
					"entry_id" 			=> $contact_id,
					"person_id" 		=> $contactb4delete->person_id,
					"action" 			=> "delete a contact address",
					"previous_values" 	=> json_encode( $contactb4delete ),
					"current_values" 	=> json_encode( $contact_after_delete ),
				];
				
				$succ_log 	= $this->create_people_history_change_log( $account_id, $contactb4delete->person_id, $log_history_data );	
				
				$this->session->set_flashdata( 'message','The Contact Address has been deleted.' );
				$result = true;
			} else {
				$this->session->set_flashdata( 'message','The Contact Address hasn\'t been deleted.' );
			}
		} else {
			$this->session->set_flashdata( 'message','Invalid Contact ID or missing Account ID.' );
		}
		return $result;
	}

}