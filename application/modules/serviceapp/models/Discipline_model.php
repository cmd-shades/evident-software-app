<?php

namespace Application\Modules\Service\Models;

class Discipline_model extends CI_Model {

	function __construct(){
		parent::__construct();
		$section 	   = explode("/", $_SERVER["SCRIPT_NAME"]);
		$this->app_root= $_SERVER["DOCUMENT_ROOT"]."/".$section[1]."/";
		$this->app_root= str_replace('/index.php','',$this->app_root);
    }

	/** Searchable fields **/
	private $searchable_fields  		= [ 'discipline.discipline_id', 'discipline_name', 'discipline.discipline_desc' ];
	private $acc_disc_searchable_fields = [ 'account_discipline.account_discipline_id', 'account_discipline.account_discipline_name', 'account_discipline.account_discipline_desc', 'discipline.discipline_name' ];
	
	
	/* 
	*	Get list of Disciplines and search though them
	*/	
	public function get_disciplines( $account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){

		$result = false;

		if( !empty( $account_id ) ){
			$this->db->select( 'discipline.*, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false )
				->join( 'user creater', 'creater.id = discipline.created_by', 'left' )
				->join( 'user modifier', 'modifier.id = discipline.last_updated_by', 'left' )
				->where( 'discipline.is_active', 1 );
			
			$where = $raw_where = convert_to_array( $where );
			
			if( isset( $where['discipline_id'] ) ){
				
				if( !empty( $where['discipline_id'] ) ){
					$row = $this->db->get_where( 'discipline', [ 'discipline_id'=>$where['discipline_id'] ] )->row();
					if( !empty( $row ) ){
						$result = $row;
						$this->session->set_flashdata( 'message','Disciplines data found' );
						return $result;
					} else {
						$this->session->set_flashdata( 'message','Disciplines data not found' );
						return false;
					}
				}
				unset( $where['discipline_id'] );
			}

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
						
						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				}else{
					foreach( $this->searchable_fields as $k=>$field ){
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
				$this->db->order_by( 'discipline_name' );
			}
			
			$query = $this->db->get( 'discipline' );

			if( $query->num_rows() > 0 ){				
				$result_data = $query->result();

				$result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
				
				$result->records 			= $result_data;
				$counters 					= $this->disciplines_totals( $account_id, $search_term, $raw_where );
				$result->counters->total 	= ( !empty( $counters->total ) ) ? $counters->total : null;
				$result->counters->pages 	= ( !empty( $counters->pages ) ) ? $counters->pages : null;
				$result->counters->limit  	= $limit;
				$result->counters->offset 	= $offset;
				
				$this->session->set_flashdata( 'message','Disciplines data found' );
			} else {
				$this->session->set_flashdata( 'message','No data found' );
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}
		
		return $result;
	}
	
	/** Get Discipline counters **/
	public function disciplines_totals( $account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT ){
		$result = false;
		if( !empty( $account_id ) ){
			
			$where = convert_to_array( $where );
			
			$this->db->select( 'discipline.discipline_id', false )
				->join( 'user creater', 'creater.id = discipline.created_by', 'left' )
				->join( 'user modifier', 'modifier.id = discipline.last_updated_by', 'left' )
				->where( 'discipline.is_active', 1 );

			if( !empty( $search_term ) ){
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->searchable_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}
						
						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				}else{
					foreach( $this->searchable_fields as $k=>$field ){
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

			$query 			  	= $this->db->from( 'discipline' )->count_all_results();
			$results['total'] 	= !empty( $query ) ? $query : 0; //xyz
			$limit 				= ( $limit > 0 ) ? $limit : $results['total'];
			$results['pages'] 	= !empty( $query ) ? ceil($results['total'] / $limit ) : 0;
			return json_decode( json_encode( $results ) );
		}
		return $result;
	}
	
	/*
	* Create new Discipline
	*/
	public function create_discipline( $account_id = false, $discipline_data = false ){

		$result = null;

		if( !empty( $account_id ) && !empty( $discipline_data  ) ){

			foreach( $discipline_data as $col => $value ){
				if( $col == 'discipline_name' ){
					$data['discipline_ref'] = strtoupper( $this->generate_discipline_ref( $account_id, $discipline_data ) );
				}
				$data[$col] = $value;
			}

			$check_exists = $this->db->where( '( discipline.discipline_name = "'.$data['discipline_name'].'" OR discipline.discipline_ref = "'.$data['discipline_ref'].'" )' )
				->limit( 1 )
				->get( 'discipline' )
				->row();

			$data = $this->ssid_common->_filter_data( 'discipline', $data );
			unset( $data['account_id'] );
			if( !empty( $check_exists  ) ){
				$data['last_updated_by'] = $this->ion_auth->_current_user->id;
				$this->db->where( 'discipline_id', $check_exists->discipline_id )
					->update( 'discipline', $data );
					$this->session->set_flashdata( 'message', 'This Discipline already exists, record has been updated successfully.' );
					$result = $check_exists;
			} else {
				$data['created_by'] 		= $this->ion_auth->_current_user->id;
				$this->db->insert( 'discipline', $data );
				$this->session->set_flashdata( 'message', 'New Discipline added successfully.' );
				$data['discipline_id'] = (string) $this->db->insert_id();
				$result = $data;
			}

		} else {
			$this->session->set_flashdata( 'message','Error! Missing required information.' );
		}

		return $result;
	}


	/** Update Discipline **/
	public function update_discipline( $account_id = false, $discipline_id = false, $discipline_data = false ){

		$result = null;

		if( !empty( $account_id ) && !empty( $discipline_id ) && !empty( $discipline_data ) ){

			foreach( $discipline_data as $col => $value ){
				if( $col == 'discipline_name' ){
					$data['discipline_ref'] = strtoupper( $this->generate_discipline_ref( $account_id, $discipline_data ) );
				}
				$data[$col] = $value;
			}

			if( !empty( $data['discipline_id'] ) ){
				$check_conflict = $this->db->where( '( discipline.discipline_name = "'.$data['discipline_name'].'" OR discipline.discipline_ref = "'.$data['discipline_ref'].'" )' )
					->where( 'discipline.discipline_id !=', $data['discipline_id'] )
					->get( 'discipline' )->row();

				$data = $this->ssid_common->_filter_data( 'discipline', $data );

				if( !$check_conflict ){
					unset( $data['account_id'] );
					$data['last_updated_by'] = $this->ion_auth->_current_user->id;
					$this->db->where( [ 'account_id'=>$account_id, 'discipline_id'=>$data['discipline_id'] ] )
						->update( 'discipline', $data );
						
						if( $this->db->trans_status() !== false ){
							$result = $this->get_disciplines( $account_id, $data['discipline_id'] );
							$this->session->set_flashdata( 'message', 'Discipline updated successfully.' );
						}
				} else {
					$this->session->set_flashdata( 'message', 'This Discipline does not exists or does not belong to you.' );
					$result = false;
				}
			} else {
				$this->session->set_flashdata( 'message','Error! Missing required information.' );
			}

		}else{
			$this->session->set_flashdata( 'message','Error! Missing required information.' );
		}

		return $result;
	}

	/*
	* Delete Discipline Item record
	*/
	public function delete_discipline( $account_id = false, $discipline_id = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $discipline_id ) ){
			$conditions 		= ['account_id'=>$account_id,'discipline_id'=>$discipline_id];
			$discipline_exists 	= $this->db->get_where('discipline',$conditions)->row();
			if( !empty( $discipline_exists ) ){
				$this->db->where( $conditions )->delete( 'discipline' );
				if( $this->db->trans_status() !== FALSE ){
					$this->session->set_flashdata('message','Record deleted successfully.');
					$result = true;
				}
			}else{
				$this->session->set_flashdata('message','Invalid Discipline Item ID');
			}

		}else{
			$this->session->set_flashdata('message','No Discipline record found.');
		}
		return $result;
	}

	
	/** Generate Schedule Ref **/
	private function generate_discipline_ref( $account_id = false, $data = false ){
		if( !empty( $account_id ) && !empty( $data ) ){
			$discipline_ref = $account_id;
			$discipline_ref .= ( !empty( $data['discipline_name'] ) ) 			? strip_all_whitespace( $data['discipline_name'] ) : '';
			$discipline_ref .= ( !empty( $data['account_discipline_name'] ) ) 	? strip_all_whitespace( $data['account_discipline_name'] ) : '';
			$discipline_ref .= ( !empty( $data['contract_id'] ) ) 				? $data['contract_id'] : '';
			$discipline_ref .= ( !empty( $data['site_id'] ) ) 					? $data['site_id'] : '';
			$discipline_ref .= ( !empty( $data['asset_id'] ) ) 					? $data['asset_id'] : '';
			$discipline_ref .= ( !empty( $data['job_type_id'] ) ) 				? $data['job_type_id'] : '';
			$discipline_ref .= lean_string( APP_VERSION );
		} else {
			$discipline_ref = $account_id.$this->ssid_common->generate_random_password();
		}
		return strtoupper( $discipline_ref );
	}


	/**
	* Activate Account Discipline(s)
	**/
	public function activate_account_disciplines( $account_id = false, $data = false ){

		$result = null;

		if( !empty( $account_id ) && !empty( $data['activation_data'] ) ){
			
			$activation_data 		= convert_to_array( $data['activation_data'] );
			$activation_account_id 	= !empty( $activation_data['account_id'] ) 		? $activation_data['account_id'] : false;
			$activation_disciplines	= !empty( $activation_data['discipline_id'] ) 	? $activation_data['discipline_id'] : false;

			if( !empty( $activation_account_id ) && !empty( $activation_disciplines ) ){
				
				if( $this->account_service->check_account_status( $activation_account_id, true ) ){
					$processed_data = [];
					$activation_disciplines = is_array( $activation_disciplines ) ? $activation_disciplines : ( is_object( $activation_disciplines ) ? object_to_array( $activation_disciplines ) : [$activation_disciplines] );
					foreach( $activation_disciplines as $k => $discipline_id ){
						$check_exists = $this->db->get_where( 'discipline', [ 'discipline_id' => $discipline_id ] )->row();
						if( !empty( $check_exists ) ){
							$data = [
								'account_id' 					=> $activation_account_id,
								'discipline_id' 				=> $discipline_id,
								'account_discipline_name' 		=> $check_exists->discipline_name,
								'account_discipline_ref' 		=> $this->generate_discipline_ref( $activation_account_id, (array) $check_exists ),
								'account_discipline_desc' 		=> $check_exists->discipline_desc,
								'account_discipline_desc' 		=> $check_exists->discipline_desc,
								'account_discipline_image_url' 	=> $check_exists->discipline_image_url,
								'account_discipline_status' 	=> ( $check_exists->is_active == 1 ) ? 'Active' : 'Unavailable',
								'is_active' 					=> 1,
							];
							$activate_discipline = $this->_create_account_discipline( $account_id, $data );
							$processed_data[]	 = !empty( $activate_discipline ) ? $activate_discipline : [] ;
						}
						
					}
					
					if( !empty( $processed_data ) ){
						
						if( count( $activation_disciplines ) == count( $processed_data ) ){
							$this->session->set_flashdata( 'message','Account Disciplines activated successfully' );
						} else {
							$this->session->set_flashdata( 'message','Partial Success. Some of the supplied discipline IDs may have been invalid and were omitted. Valid one were acvitvated successfully.' );
						}
						
						$result = $processed_data;
					} else {
						$this->session->set_flashdata( 'message','No Account Disciplines were activated due to invalid data' );
					}
					
				} else {
					$this->session->set_flashdata( 'message','The activation Account ID is invalid' );
				}
				
			} else {
				$this->session->set_flashdata( 'message','Error! Missing required information.' );
			}

		} else {
			$this->session->set_flashdata( 'message','Error! Missing required information.' );
		}

		return $result;
	}
	
	
	/**
	* Deactivate Discipline 
	**/
	public function deactivate_account_disciplines( $account_id = false, $data = false ){

		$result = null;

		if( !empty( $account_id ) && !empty( $data['deactivation_data'] ) ){

			$deactivation_data 			= convert_to_array( $data['deactivation_data'] );
			$deactivation_account_id 	= !empty( $deactivation_data['account_id'] ) 		? $deactivation_data['account_id'] : false;
			$deactivation_disciplines	= !empty( $deactivation_data['discipline_id'] ) 	? $deactivation_data['discipline_id'] : false;


			$deactivation_disciplines = is_array( $deactivation_disciplines ) ? $deactivation_disciplines : ( is_object( $deactivation_disciplines ) ? object_to_array( $deactivation_disciplines ) : [$deactivation_disciplines] );
			foreach( $deactivation_disciplines as $k => $discipline_id ){
				$check_exists = $this->db->get_where( 'account_discipline', [ 'discipline_id' => $discipline_id ] )->row();
				if( !empty( $check_exists ) ){

					$this->db->where( 'account_discipline.account_id', $deactivation_account_id )
						->where( 'account_discipline.discipline_id', $discipline_id )
						->update( 'account_discipline', [ 'is_active'=>0, 'last_updated_by'=>$this->ion_auth->_current_user->id, 'last_updated_on'=>_datetime() ] );

					if( $this->db->trans_status() !== false ){
						$processed_data[]	 = $discipline_id;
					}
				}
			}
			
			if( !empty( $processed_data ) ){
				
				if( count( $deactivation_disciplines ) == count( $processed_data ) ){
					$this->session->set_flashdata( 'message','Account Disciplines De-activated successfully' );
				} else {
					$this->session->set_flashdata( 'message','Partial Success. Some of the supplied discipline IDs may have been invalid and were omitted. Valid one were De-acvitvated successfully.' );
				}
				
				$result = $processed_data;
			} else {
				$this->session->set_flashdata( 'message','No Account Disciplines were De-activated due to invalid data' );
			}

		} else {
			$this->session->set_flashdata( 'message','Error! Missing required information.' );
		}

		return $result;
	}
	
	
	/** Create Account Discipline **/
	private function _create_account_discipline( $account_id = false , $discipline_data = false ){
		
		$result = null;

		if( !empty( $account_id ) && !empty( $discipline_data ) ){
			
			$check_exists 	= $this->db->get_where( 'account_discipline', [ 'account_id' => $discipline_data['account_id'], 'discipline_id' => $discipline_data['discipline_id'] ] )->row();
			$data 			= $this->ssid_common->_filter_data( 'account_discipline', $discipline_data );

			if( !empty( $check_exists ) ){
				$data['account_discipline_name'] 	= !empty( $check_exists->account_discipline_name ) 	? $check_exists->account_discipline_name 	: $data['account_discipline_name'];
				$data['account_discipline_ref'] 	= !empty( $check_exists->account_discipline_ref ) 	? $check_exists->account_discipline_ref 	: $data['account_discipline_ref'];
				$data['account_discipline_desc'] 	= !empty( $check_exists->account_discipline_desc ) 	? $check_exists->account_discipline_desc 	: $data['account_discipline_desc'];
				$data['account_discipline_status'] 	= !empty( $check_exists->account_discipline_status )? $check_exists->account_discipline_status 	: $data['account_discipline_status'];
				$data['account_discipline_image_url']= !empty( $check_exists->account_discipline_image_url )? $check_exists->account_discipline_image_url 	: $data['account_discipline_image_url'];
				$data['last_updated_on'] 			= _datetime();
				$data['last_updated_by'] 			= !empty( $this->ion_auth->_current_user->id ) ? $this->ion_auth->_current_user->id : 1;
				$data['activated_on'] 	 			= ( !empty( $check_exists->activated_on ) && $check_exists->account_discipline_status != 'Active' ) ? _datetime() : $check_exists->activated_on;

				$this->db->where( 'account_discipline.account_discipline_id', $check_exists->account_discipline_id )
					->update( 'account_discipline', $data );
				
			} else {
				$data['created_on'] 	 = _datetime();
				$data['created_by'] 	 = !empty( $this->ion_auth->_current_user->id ) ? $this->ion_auth->_current_user->id : 1;
				$data['activated_on'] 	 = _datetime();
				$this->db->insert( 'account_discipline', $data );
			}
			
			if( $this->db->trans_status() !== false ){
				$result = $this->db->get_where( 'account_discipline', [ 'account_id' => $data['account_id'], 'discipline_id' => $data['discipline_id'] ] )->row();
			}

		} else {
			$this->session->set_flashdata( 'message','Error! Missing required information.' );
		}
		
		return $result;
	}
	
	/** Update Account Discipline **/
	public function update_account_discipline( $account_id = false, $account_discipline_id = false, $discipline_data = false ){
		
		$result = null;

		if( !empty( $account_id ) && !empty( $account_discipline_id ) && !empty( $discipline_data ) ){
			
			$data 			= $this->ssid_common->_filter_data( 'account_discipline', $discipline_data );
			$check_exists 	= $this->db->get_where( 'account_discipline', [ 'account_discipline_id' => $account_discipline_id ] )->row();
			
			if( !empty( $check_exists ) ){
				
				$data['account_discipline_name'] 	= !empty( $data['account_discipline_name'] ) 	? $data['account_discipline_name']  	: $check_exists->account_discipline_name;
				$data['account_discipline_ref'] 	= $this->generate_discipline_ref( $data['account_id'], (array) $data );
				$data['account_discipline_desc'] 	= !empty( $data['account_discipline_desc'] ) 	? $data['account_discipline_desc']  	: $check_exists->account_discipline_desc;
				$data['account_discipline_status'] 	= !empty( $data['account_discipline_status'] ) 	? $data['account_discipline_status']  	: $check_exists->account_discipline_status;
				$data['account_discipline_image_url']= !empty( $data['account_discipline_image_url'] )? $data['account_discipline_image_url']: '';
				$data['last_updated_on'] = _datetime();
				$data['last_updated_by'] = $this->ion_auth->_current_user->id;
				$data['activated_on'] 	 = ( !empty( $check_exists->activated_on ) && $check_exists->account_discipline_status != 'Active' ) ? _datetime() : $check_exists->activated_on;
				
				$this->db->where( 'account_discipline.account_discipline_id', $check_exists->account_discipline_id )
					->update( 'account_discipline', $data );
					
				
				if( $this->db->trans_status() !== false ){
					$this->session->set_flashdata( 'message','Discipline record updated successfully!' );
					$result = $this->db->get_where( 'account_discipline', [ 'account_discipline_id' => $account_discipline_id ] )->row();
				} else {
					$this->session->set_flashdata( 'message','Error! There was a problem processing your request' );
				}
				
			} else {
				
				$activation_account_id  = $discipline_data['activation_account_id'];
				$discipline_id 			= $discipline_data['discipline_id'];
				
				if( !empty( $activation_account_id ) && !empty( $discipline_id ) ){
				
					if( $this->account_service->check_account_status( $activation_account_id, true ) ){
						$check_exists 	= $this->db->get_where( 'discipline', [ 'discipline_id' => $discipline_id ] )->row();
						if( !empty( $check_exists ) ){
							$new_record = [
								'account_id' 					=> $activation_account_id,
								'discipline_id' 				=> $discipline_id,
								'account_discipline_name' 		=> !empty( $data['account_discipline_name'] ) 	? $data['account_discipline_name']  	: $check_exists->discipline_name,
								'account_discipline_ref' 		=> $this->generate_discipline_ref( $activation_account_id, (array) $check_exists ),
								'account_discipline_desc' 		=> !empty( $data['account_discipline_desc'] ) 	? $data['account_discipline_desc']  	: $check_exists->discipline_desc,
								'account_discipline_image_url' 	=> !empty( $data['account_discipline_image_url'] ) 	? $data['account_discipline_image_url']: $check_exists->discipline_image_url,
								'account_discipline_status' 	=> ( $check_exists->is_active == 1 ) ? 'Active' : 'Unavailable',
								'created_on' 					=> _datetime(),
								'created_by' 					=> $this->ion_auth->_current_user->id,
								'activated_on' 					=> _datetime(),
							];

							$this->db->insert( 'account_discipline', $new_record );
							
							if( $this->db->trans_status() !== false ){
								$this->session->set_flashdata( 'message','Discipline record created successfully!' );
								$result = $this->db->get_where( 'account_discipline', [ 'account_discipline_id' => $this->db->insert_id() ] )->row();
							} else {
								$this->session->set_flashdata( 'message','Error! There was a problem processing your request' );
							}
							
						} else {
							$this->session->set_flashdata( 'message','The invalid Discipline ID' );
						}

					} else {
						$this->session->set_flashdata( 'message','The activation Account ID is invalid' );
					}
					
				}

			}
				
		} else {
			$this->session->set_flashdata( 'message','Error! Missing required information.' );
		}
		
		return $result;
	}
	
	
	/* 
	*	Get list of Account Disciplines and search though them
	*/	
	public function get_account_disciplines( $account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){

		$result = false;

		if( !empty( $account_id ) ){
			$this->db->select( 'account_discipline.*, discipline.discipline_name, discipline.discipline_colour, discipline.discipline_colour_hex, discipline.discipline_image_url, discipline.discipline_icon, CONCAT( creater.first_name, " ", creater.last_name ) `created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `last_updated_by`', false )
				->join( 'user creater', 'creater.id = account_discipline.created_by', 'left' )
				->join( 'user modifier', 'modifier.id = account_discipline.last_updated_by', 'left' )
				->join( 'discipline', 'discipline.discipline_id = account_discipline.discipline_id', 'left' )
				->where( 'account_discipline.account_id', $account_id )
				->where( 'account_discipline.is_active', 1 );
			
			$where = $raw_where = convert_to_array( $where );

			if( isset( $where['account_discipline_id'] ) || isset( $where['discipline_id'] ) ){
				
				if( !empty( $where['discipline_id'] ) || !empty( $where['account_discipline_id'] ) ){
					
					if( !empty( $where['discipline_id'] )){
						$row = $this->db->get_where( 'account_discipline', [ 'account_discipline.discipline_id'=>$where['discipline_id'] ] )->row();
					} else {
						$row = $this->db->get_where( 'account_discipline', [ 'account_discipline.account_discipline_id'=>$where['account_discipline_id'] ] )->row();
					}
					
					if( !empty( $row ) ){
						$result = $row;
						$this->session->set_flashdata( 'message','Account Disciplines data found' );
						return $result;
					} else {
						$this->session->set_flashdata( 'message','Account Disciplines data not found' );
						return false;
					}
				}
				unset( $where['account_discipline_id'], $where['account_discipline_id'] );
			}

			if( !empty( $search_term ) ){
				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->acc_disc_searchable_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}
						
						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				}else{
					foreach( $this->acc_disc_searchable_fields as $k=>$field ){
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
				$this->db->order_by( 'account_discipline_status, account_discipline_name' );
			}
			
			$query = $this->db->get( 'account_discipline' );
			
			if( $query->num_rows() > 0 ){				
				$result_data = $query->result();

				$result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
				$result->records 			= $result_data;
				$counters 					= $this->account_disciplines_totals( $account_id, $search_term, $raw_where );
				$result->counters->total 	= ( !empty( $counters->total ) ) ? $counters->total : null;
				$result->counters->pages 	= ( !empty( $counters->pages ) ) ? $counters->pages : null;
				$result->counters->limit  	= ( !empty( $apply_limit ) ) ? $limit : $result->counters->total;
				$result->counters->offset 	= $offset;
				
				$this->session->set_flashdata( 'message','Account Disciplines data found' );
			} else {
				$this->session->set_flashdata( 'message','No data found' );
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}
		
		return $result;
	}
	
	/** Get Account Discipline counters **/
	public function account_disciplines_totals( $account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT ){
		$result = false;
		if( !empty( $account_id ) ){
			
			$where = convert_to_array( $where );
			
			$this->db->select( 'account_discipline.account_discipline_id', false )
				->join( 'discipline', 'discipline.discipline_id = account_discipline.discipline_id', 'left' )
				->where( 'account_discipline.account_id', $account_id );

			if( !empty( $search_term ) ){
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->acc_disc_searchable_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}
						
						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				}else{
					foreach( $this->acc_disc_searchable_fields as $k=>$field ){
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

			$query 			  	= $this->db->from( 'account_discipline' )->count_all_results();
			$results['total'] 	= !empty( $query ) ? $query : 0;
			$limit 				= ( $limit > 0 ) ? $limit : $results['total'];
			$results['pages'] 	= !empty( $query ) ? ceil($results['total'] / $limit ) : 0;
			return json_decode( json_encode( $results ) );
		}
		return $result;
	}
	
	/** Get Account Discipline Statistics **/
	public function get_discipline_stats( $account_id = false, $where = false, $order_by = false ){
		$result = false;
		if( !empty( $account_id ) ){
			
			$where = convert_to_array( $where );
			
			$date_range = !empty( $where['date_range'] ) ? $where['date_range'] : '7';
			
			if( !empty( $where['discipline_id'] )){
				$this->db->where( 'account_discipline.discipline_id', $where['discipline_id'] );
				$include_site_info = true;
			}

			$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 7 days ' ) );
			$date_to 	= _datetime();
			if( !empty( $date_range ) ){
				switch( strtolower( $date_range ) ){
					# Last 7 Days to date inclusive
					case '7':
					case '7days':
					case '7 days':
					case '1week':
					case '1 week':
						$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 7 days ' ) );;
						break;
						
					# Last 30 Days to date inclusive
					case '30':
					case '30days':
					case '30 days':
					case '1month':
					case '1 month':
						$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 30 days ' ) );;
						break;
						
					# Last 90 Days to date inclusive
					case '90':
					case '90days':
					case '90 days':
					case '3months':
					case '3 months':
						$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 90 days ' ) );;
						break;
						
					# Last 180 Days to date inclusive
					case '180':
					case '180days':
					case '180 days':
					case '6months':
					case '6 months':
						$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 180 days ' ) );;
						break;
						
					# Last 365 Days to date inclusive
					case '365':
					case '365days':
					case '365 days':
					case '12months':
					case '12 months':
					case '1year':
					case '1 year':
						$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 1 year' ) );;
						break;
					
				}
			}
			
			##Apply custom Date range
			$date_range = !empty( $where['date_range'] ) ? $where['date_range'] : '7 days';
			$date_from 	= !empty( $where['date_from'] )  ? date( 'Y-m-d 00:00:01', strtotime( _datetime( $where['date_from'] ) ) )  : $date_from;
			$date_to 	= !empty( $where['date_to'] ) 	 ? date( 'Y-m-d 23:59:59', strtotime( _datetime( $where['date_to'] ) ) ) : $date_to;;
			
			$query = $this->db->select( 'account_discipline.*, discipline.discipline_name, discipline.discipline_colour, discipline.discipline_colour_hex, discipline.discipline_image_url, discipline.discipline_icon', false )
				->join( 'discipline', 'discipline.discipline_id = account_discipline.discipline_id', 'left' )
				->where( 'account_discipline.account_id', $account_id )
				->where( 'account_discipline.is_active', 1 )
				->order_by( 'account_discipline.account_discipline_name' )
				->get( 'account_discipline' );

			if( $query->num_rows() > 0 ){
				$stats_data = [];
				foreach(  $query->result() as $k => $row ){
					
					$attendance_info = $this->get_discipline_attendance_info( $account_id, $row->discipline_id, $where );
					$attd_info = (object) [
						'scheduled_visits' => !empty( $attendance_info->scheduled_visits ) ? $attendance_info->scheduled_visits : '0',
						'completed_visits' => !empty( $attendance_info->completed_visits ) ? $attendance_info->completed_visits : '0'
					];
					
					$outcomes_info   = $this->get_discipline_outcomes_info( $account_id, $row->discipline_id, $where );
					$outcm_info = (object) [
						'total_inspections'  => !empty( $outcomes_info->total_inspections ) ? $outcomes_info->total_inspections : '0',
						'passed_inspections' => !empty( $outcomes_info->passed_inspections ) ? $outcomes_info->passed_inspections : '0',
						'failed_inspections' => !empty( $outcomes_info->failed_inspections ) ? $outcomes_info->failed_inspections : '0',
						'recommendations' 	 => !empty( $outcomes_info->recommendations ) ? $outcomes_info->recommendations : '0',
					];
					
					$stats_data[$k] = [
						'profile_info' 	=> [
							'discipline_id' 		=> !empty( $row->discipline_id ) 				? $row->discipline_id : null,
							'discipline_name' 		=> !empty( $row->account_discipline_name ) 		? $row->account_discipline_name : $row->discipline_name,
							'discipline_ref'  		=> !empty( $row->account_discipline_ref ) 		? $row->account_discipline_ref : null,
							'discipline_status' 	=> !empty( $row->account_discipline_status ) 	? $row->account_discipline_status : null,
							'discipline_image_url' 	=> !empty( $row->account_discipline_image_url ) ? $row->account_discipline_image_url : $row->discipline_image_url,
							'discipline_icon' 		=> !empty( $row->discipline_icon ) 				? $row->discipline_icon : null,
							'discipline_colour' 	=> !empty( $row->discipline_colour ) 			? $row->discipline_colour : null,
							'discipline_colour_hex' => !empty( $row->discipline_colour_hex ) 		? $row->discipline_colour_hex : null,
							'account_discipline_id' => !empty( $row->account_discipline_id ) 		? $row->account_discipline_id : null,
						],
						'attendance_info' 	=> [
							'scheduled_visits' 		=> $attd_info->scheduled_visits,
							'completed_visits' 		=> $attd_info->completed_visits,
							'completed_percentage' 	=> ( $attd_info->completed_visits > 0 ) ? ( number_format( ( ($attd_info->completed_visits / $attd_info->scheduled_visits)*100 ), 1 ) ) : '0',
							'pending_visits' 		=> $attd_info->scheduled_visits - $attd_info->completed_visits,
							'pending_percentage' 	=> ( $attd_info->completed_visits > 0 ) ? ( number_format( ( ( ( $attd_info->scheduled_visits - $attd_info->completed_visits ) / $attd_info->scheduled_visits )*100 ), 1 ) ) : '0',
						],
						'outcomes_info' 	=> [
							'total_inspections' 		=> $outcm_info->total_inspections,
							'passed_inspections' 		=> $outcm_info->passed_inspections,
							'passed_inspections_percent'=> ( $outcm_info->passed_inspections > 0 ) ? ( number_format( ( ( $outcm_info->passed_inspections / $outcm_info->total_inspections )*100 ), 1 ) ) : '0',
							'failed_inspections' 		=> $outcm_info->failed_inspections,
							'failed_inspections_percent'=> ( $outcm_info->failed_inspections > 0 ) ? ( number_format( ( ( $outcm_info->failed_inspections / $outcm_info->total_inspections )*100 ), 1 ) ) : '0',
							'recommendations' 		 	=> $outcm_info->recommendations,
							'recommendations_percent'	=> ( $outcm_info->recommendations > 0 ) ? ( number_format( ( ( $outcm_info->recommendations/$outcm_info->total_inspections )*100 ), 1 ) ) : '0',
						],
					];
					
					if( !empty( $include_site_info ) ){
						
						$total_buildings		 = $this->get_buildings_by_discipline_totals( $account_id, $row->discipline_id );;
					
						$stats_data[$k]['buildings_info'] = [
							'total_buildings' => !empty( $total_buildings->total ) ? $total_buildings->total : '0',
							'buildings_list'  => null
						];
						
						#$contact_info		  = $this->get_contacts_by_discipline( $account_id, $row->discipline_id );;
						
						$stats_data[$k]['contact_info']  = [
							'primary_contact' 	=> [
								'first_name' 	=> 'Ruth',
								'last_name' 	=> 'Taylor',
								'telephone' 	=> '020 8760 5283',
								'email' 		=> 'ruthtaylor@evidentsoftware.co.uk',
								'role' 			=> 'Buildings Safety Manager',
							],
							'secondary_contact' => [
								'first_name' 	=> 'Jake',
								'last_name' 	=> 'Archer',
								'telephone' 	=> '020 8760 5283',
								'email' 		=> 'support@evidentsoftware.co.uk',
								'role' 			=> 'Buildings Safety Supervisor',
							],
						];
						$stats_data[$k]['outcomes_total'] = $outcm_info->total_inspections;
					}
				}

				$result = [
					'data' 		 => $stats_data,
					'extra_info' => [
						'last_updated' 	=> date( 'h:i:s A', strtotime( _datetime() ) ),
						'date_range' 	=> $date_range,
						'date_from' 	=> $date_from,
						'date_to' 		=> $date_to,
					]				
				];
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}
		return $result;
	}


	/** Get Building Disciplin Stats **/
	public function get_building_stats( $account_id = false, $site_id = false, $where = false, $order_by = false ){
		
		$result = false;
		
		if( !empty( $account_id ) && !empty( $site_id ) ){
			
			$where 		= convert_to_array( $where );
			$date_range	= !empty( $where['date_range'] ) ? $where['date_range'] : '7 days';
			
			$site = $this->db->select( 'site.*', false )
				->where( 'site.site_id', $site_id )
				->where( 'site.account_id', $account_id )				
				->get( 'site' )->row();
				
			if( !empty( $site ) ){
				
				$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 7 days ' ) );
				$date_to 	= _datetime();
				
				if( !empty( $where['date_range'] ) ){
					switch( strtolower( $where['date_range'] ) ){
						# Last 7 Days to date inclusive
						default:
						case '7':
						case '7days':
						case '7 days':
						case '1week':
						case '1 week':
							$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 7 days ' ) );;
							break;
							
						# Last 30 Days to date inclusive
						case '30':
						case '30days':
						case '30 days':
						case '1month':
						case '1 month':
							$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 30 days ' ) );;
							break;
							
						# Last 90 Days to date inclusive
						case '90':
						case '90days':
						case '90 days':
						case '3months':
						case '3 months':
							$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 90 days ' ) );;
							break;
							
						# Last 180 Days to date inclusive
						case '180':
						case '180days':
						case '180 days':
						case '6months':
						case '6 months':
							$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 180 days ' ) );;
							break;
							
						# Last 365 Days to date inclusive
						case '365':
						case '365days':
						case '365 days':
						case '12months':
						case '12 months':
						case '1year':
						case '1 year':
							$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 1 year' ) );;
							break;
						
					}
				}
				
				##Apply custom Date range
				$date_range = !empty( $where['date_range'] ) ? $where['date_range'] : '7 days';
				$date_from 	= !empty( $where['date_from'] )  ? date( 'Y-m-d 00:00:01', strtotime( _datetime( $where['date_from'] ) ) )  : $date_from;
				$date_to 	= !empty( $where['date_to'] ) 	 ? date( 'Y-m-d 23:59:59', strtotime( _datetime( $where['date_to'] ) ) ) : date( 'Y-m-d H:i:s', strtotime( _datetime() ) );
				
				$data = [
					'building_summary_info' => [
						'site_id' 			=> $site->site_id,
						'site_name' 		=> $site->site_name,
						'site_reference'	=> $site->site_reference,
						'site_fra'			=> '00/00/00',
						#'audit_status'		=> 'Passed',
						'building_type'		=> $site->building_type,
						'number_of_floors'	=> !empty( $site->number_of_floors ) ? $site->number_of_floors : 20,
						'total_dwellings'	=> !empty( $site->total_dwellings )  ? $site->total_dwellings : 20,
						'max_residents'		=> !empty( $site->total_dwellings )  ? $site->total_dwellings : 300,
						'build_year'		=> !empty( $site->build_year )  ? $site->build_year : 300,
						'tenure'			=> !empty( $site->build_year )  ? $site->build_year : 'Mixed',
						'square_meters'		=> '25000',
						'frame'				=> 'Concrete',
						'roof'				=> 'Concrete',
						'combustibility'	=> '20-40%',
						'cladding'			=> 'Corium',
					],
					'contact_info'  => [
						'primary_contact' 	=> [
							'first_name' 	=> 'Ruth',
							'last_name' 	=> 'Taylor',
							'telephone' 	=> '020 8760 5283',
							'email' 		=> 'ruthtaylor@evidentsoftware.co.uk',
							'role' 			=> 'Buildings Safety Manager',
						],
						'secondary_contact' => [
							'first_name' 	=> 'Jake',
							'last_name' 	=> 'Archer',
							'telephone' 	=> '020 8760 5283',
							'email' 		=> 'support@evidentsoftware.co.uk',
							'role' 			=> 'Buildings Safety Supervisor',
						],
					]
				];
				
				if( !empty( $where['discipline_id'] )){
					$this->db->where( 'account_discipline.discipline_id', $where['discipline_id'] );
				}
				
				## Add Site Disciplines Info
				$query = $this->db->select( 'account_discipline.*, discipline.discipline_name, discipline.discipline_colour, discipline.discipline_colour_hex, discipline.discipline_image_url, discipline.discipline_icon', false )
					->join( 'discipline', 'discipline.discipline_id = account_discipline.discipline_id', 'left' )
					->where( 'account_discipline.account_id', $account_id )
					->where( 'account_discipline.is_active', 1 )
					->order_by( 'account_discipline.account_discipline_name' )
					->get( 'account_discipline' );

				if( $query->num_rows() > 0 ){
					$stats_data = [];
					foreach(  $query->result() as $k => $row ){
						
						$where['site_id'] = $site_id;
						
						$attendance_info = $this->get_discipline_attendance_info( $account_id, $row->discipline_id, $where );
						$attd_info = (object) [
							'scheduled_visits' => !empty( $attendance_info->scheduled_visits ) ? $attendance_info->scheduled_visits : '0',
							'completed_visits' => !empty( $attendance_info->completed_visits ) ? $attendance_info->completed_visits : '0'
						];
						
						$outcomes_info   = $this->get_discipline_outcomes_info( $account_id, $row->discipline_id, $where );
						$outcm_info = (object) [
							'total_inspections'  => !empty( $outcomes_info->total_inspections ) ? $outcomes_info->total_inspections : '0',
							'passed_inspections' => !empty( $outcomes_info->passed_inspections ) ? $outcomes_info->passed_inspections : '0',
							'failed_inspections' => !empty( $outcomes_info->failed_inspections ) ? $outcomes_info->failed_inspections : '0',
							'recommendations' 	 => !empty( $outcomes_info->recommendations ) ? $outcomes_info->recommendations : '0',
						];
						
						$stats_data[$k] = [
							'profile_info' 	=> [
								'discipline_id' 		=> !empty( $row->discipline_id ) 				? $row->discipline_id : null,
								'discipline_name' 		=> !empty( $row->account_discipline_name ) 		? $row->account_discipline_name : $row->discipline_name,
								'discipline_ref'  		=> !empty( $row->account_discipline_ref ) 		? $row->account_discipline_ref : null,
								'discipline_status' 	=> !empty( $row->account_discipline_status ) 	? $row->account_discipline_status : null,
								'discipline_image_url' 	=> !empty( $row->account_discipline_image_url ) ? $row->account_discipline_image_url : $row->discipline_image_url,
								'discipline_icon' 		=> !empty( $row->discipline_icon ) 				? $row->discipline_icon : null,
								'discipline_colour' 	=> !empty( $row->discipline_colour ) 			? $row->discipline_colour : null,
								'discipline_colour_hex' => !empty( $row->discipline_colour_hex ) 		? $row->discipline_colour_hex : null,
								'account_discipline_id' => !empty( $row->account_discipline_id ) 		? $row->account_discipline_id : null,
							],
							'attendance_info' 	=> [
								'scheduled_visits' 		=> $attd_info->scheduled_visits,
								'completed_visits' 		=> $attd_info->completed_visits,
								'completed_percentage' 	=> ( $attd_info->completed_visits > 0 ) ? number_format( ( ($attd_info->completed_visits / $attd_info->scheduled_visits)*100 ), 1 ) : '0',
								'pending_visits' 		=> $attd_info->scheduled_visits - $attd_info->completed_visits,
								'pending_percentage' 	=> ( $attd_info->scheduled_visits ) ? number_format( ( ( ( $attd_info->scheduled_visits - $attd_info->completed_visits ) / $attd_info->scheduled_visits )*100 ), 1 ) : '0',
							],
							'outcomes_info' 	=> [
								'total_inspections' 		=> $outcm_info->total_inspections,
								'passed_inspections' 		=> $outcm_info->passed_inspections,
								'passed_inspections_percent'=> ( $outcm_info->passed_inspections > 0 ) ? number_format( ( ( $outcm_info->passed_inspections / $outcm_info->total_inspections )*100 ), 1 ) : '0',
								'failed_inspections' 		=> $outcm_info->failed_inspections,
								'failed_inspections_percent'=> ( $outcm_info->failed_inspections > 0 ) ? number_format( ( ( $outcm_info->failed_inspections / $outcm_info->total_inspections )*100 ), 1 ) : '0',
								'recommendations' 		 	=> $outcm_info->recommendations,
								'recommendations_percent'	=> ( $outcm_info->recommendations > 0 ) ? number_format( ( ( $outcm_info->recommendations/$outcm_info->total_inspections )*100 ), 1 ) : '0',
							],
						];
					}
					$data['building_disciplines_info'] = $stats_data;
				}
				
				$result = [
					'data' 		 => $data,
					'extra_info' => [
						'last_updated' 	=> _datetime(),
						'date_range' 	=> $date_range,
						'date_from' 	=> $date_from,
						'date_to' 		=> $date_to,
					]				
				];
				
				$this->session->set_flashdata( 'message','Building Stats data retrieved successfully' );
				
			} else {
				$this->session->set_flashdata( 'message','Invalid Site ID' );
			}
		
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}
		return $result;
	}
	
	/** Get Building Discipline Stats **/
	public function get_building_outcomes( $account_id = false, $discipline_id = false, $site_id = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		
		$result = false;
		
		if( !empty( $account_id ) && !empty( $discipline_id )  && !empty( $site_id ) ){
			
			$where				= convert_to_array( $where );
			$where['site_id']	= $site_id;
			
			if( !empty( $where['site_id'] ) ){
				$where['detailed_info']	= 1;
			}
			
			$site_discipline = $this->get_buildings_by_discipline( $account_id, $discipline_id, $where );

			if( !empty( $site_discipline ) ){
				
				
				$date_range	= !empty( $where['date_range'] ) ? $where['date_range'] : '7 days';

				if( !empty( $date_range ) ){
					switch( strtolower( $date_range ) ){
						# Last 7 Days to date inclusive
						default:
						case '7':
						case '7days':
						case '7 days':
						case '1week':
						case '1 week':
							$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 7 days ' ) );;
							break;
							
						# Last 30 Days to date inclusive
						case '30':
						case '30days':
						case '30 days':
						case '1month':
						case '1 month':
							$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 30 days ' ) );;
							break;
							
						# Last 90 Days to date inclusive
						case '90':
						case '90days':
						case '90 days':
						case '3months':
						case '3 months':
							$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 90 days ' ) );;
							break;
							
						# Last 180 Days to date inclusive
						case '180':
						case '180days':
						case '180 days':
						case '6months':
						case '6 months':
							$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 180 days ' ) );;
							break;
							
						# Last 365 Days to date inclusive
						case '365':
						case '365days':
						case '365 days':
						case '12months':
						case '12 months':
						case '1year':
						case '1 year':
							$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 1 year' ) );;
							break;
						
					}
				}				
				
				$date_to 	= _datetime();
				$date_from 	= !empty( $where['date_from'] )  ? date( 'Y-m-d 00:00:01', strtotime( _datetime( $where['date_from'] ) ) )  : $date_from;
				$date_to 	= !empty( $where['date_to'] ) 	 ? date( 'Y-m-d 23:59:59', strtotime( _datetime( $where['date_to'] ) ) ) : $date_to;
				
				$site_discipline->extra_info 	= [
					'last_updated' 	=> _datetime(),
					'date_range' 	=> $date_range,
					'date_from' 	=> $date_from,
					'date_to' 		=> $date_to,
				];
				
				return $site_discipline;
			}
			
		}
		
		return $result;
		
	}
	
	
	/** Get Building Overdue Jobs **/
	public function get_overdue_jobs( $account_id = false, $site_id = false, $discipline_id = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		
		$result = false;
		
		if( !empty( $account_id ) && !empty( $site_id ) ){
			
			$where 				= convert_to_array( $where );
			$where['site_id']	= $site_id;
			
			$site_details = $this->db->select( 'site.site_id, site.site_name, site.site_postcodes', false )
				->where( 'site.account_id', $account_id )
				->where( 'site.site_id', $site_id )
				->where( 'site.archived !=', 1 )
				->get( 'site' )
				->row();
			
			if( $site_details ){
				
				$discipline_id = !empty( $where['discipline_id'] ) ? $where['discipline_id'] : $discipline_id;
				
				if( !empty( $discipline_id ) ){
					$this->db->where( 'account_discipline.discipline_id', $discipline_id );
				}
				
				$query = $this->db->select( 'account_discipline.*, discipline.discipline_name, discipline.discipline_colour, discipline.discipline_colour_hex, discipline.discipline_image_url, discipline.discipline_icon', false )
					->join( 'discipline', 'discipline.discipline_id = account_discipline.discipline_id', 'left' )
					->where( 'account_discipline.account_id', $account_id )
					->where( 'account_discipline.is_active', 1 )
					->order_by( 'account_discipline.account_discipline_name' )
					->get( 'account_discipline' );

				if( $query->num_rows() > 0 ){
					
					$stats_data = [];
					foreach(  $query->result() as $k => $row ){
						
						$overdue_jobs_info = $this->get_overdue_jobs_by_discipline( $account_id, $row->discipline_id, $where );

						$stats_data = !empty( $overdue_jobs_info ) ? array_merge( $stats_data, $overdue_jobs_info ) : $stats_data;
					}
					
					$site_details->job_disciplines = $stats_data;
					$result = $site_details;
					$this->session->set_flashdata( 'message','Overdue Jobs data found' );
				} else {
					$this->session->set_flashdata( 'message','No data found' );
				}
				
				
			} else {
				$this->session->set_flashdata( 'message','No data found matching your criteria' );
			}		
		}
		
		return $result;
		
	}
	
	
	/*
	* Get Building Recommendations
	**/
	public function get_building_recommendations( $account_id = false, $site_id = false, $discipline_id = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		
		$result = false;
		
		if( !empty( $account_id ) && !empty( $site_id ) ){
			
			$where = convert_to_array( $where );
			
			$site_details = $this->db->select( 'site.site_id, site.site_name, site.site_postcodes', false )
				->where( 'site.account_id', $account_id )
				->where( 'site.site_id', $site_id )
				->where( 'site.archived !=', 1 )
				->get( 'site' )
				->row();
			
			if( $site_details ){
				
				$random_job_id = rand( 1000, 5000 );
				
				$site_details->building_recommendations = [
					[ 
						'id' 				=> 1,
						'record_type' 		=> 'recommendation',
						'recommendation' 	=> 'Replace 1st floor emergency light',
						'action_due_date' 	=> date( 'd-m-Y', strtotime( '+ 2 days' ) ),
						'priority_rating' 	=> '1',
						'asset_id' 			=> '10000',
						'job_id' 			=> $random_job_id + 1,
					],
					[ 
						'id' 				=> 2,
						'record_type' 		=> 'recommendation',
						'recommendation' 	=> 'Damaged smoke detector',
						'action_due_date' 	=> date( 'd-m-Y', strtotime( '+ 3 days' ) ),
						'priority_rating' 	=> '1',
						'asset_id' 			=> '10080',
						'job_id' 			=> $random_job_id + 2,
					],
					[ 
						'id' 				=> 3,
						'record_type' 		=> 'recommendation',
						'recommendation' 	=> 'Damaged 2nd floor fire door',
						'action_due_date' 	=> date( 'd-m-Y', strtotime( '+ 9 days' ) ),
						'priority_rating' 	=> '2',
						'asset_id' 			=> '10200',
						'job_id' 			=> $random_job_id + 3,
					],
					[ 
						'id' 				=> 4,
						'record_type' 		=> 'recommendation',
						'recommendation' 	=> 'Damaged 22nd floor fire door',
						'action_due_date' 	=> date( 'd-m-Y', strtotime( '+ 11 days' ) ),
						'priority_rating' 	=> '2',
						'asset_id' 			=> '10310',
						'job_id' 			=> $random_job_id + 4,
					],
					[ 
						'id' 				=> 4,
						'record_type' 		=> 'recommendation',
						'recommendation' 	=> 'Damaged 3rd floor emergency light',
						'action_due_date' 	=> date( 'd-m-Y', strtotime( '+ 14 days' ) ),
						'priority_rating' 	=> '3',
						'asset_id' 			=> '10011',
					]
				];
				$result = $site_details;
			}
		}
		
		return $result;
		
	}
	
	
	/**
	* BUildings Outcomes Summary
	**/
	public function get_building_outcomes_summary( $account_id = false, $site_id = false, $discipline_id = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;
		
		if( !empty( $account_id ) && !empty( $site_id ) ){
			
			$where		= convert_to_array( $where );
			
			$date_range = !empty( $where['date_range'] ) ? $where['date_range'] : '7';
			
			if( !empty( $date_range ) ){
					switch( strtolower( $date_range ) ){
						# Last 7 Days to date inclusive
						default:
						case '7':
						case '7days':
						case '7 days':
						case '1week':
						case '1 week':
							$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 7 days ' ) );;
							break;
							
						# Last 30 Days to date inclusive
						case '30':
						case '30days':
						case '30 days':
						case '1month':
						case '1 month':
							$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 30 days ' ) );;
							break;
							
						# Last 90 Days to date inclusive
						case '90':
						case '90days':
						case '90 days':
						case '3months':
						case '3 months':
							$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 90 days ' ) );;
							break;
							
						# Last 180 Days to date inclusive
						case '180':
						case '180days':
						case '180 days':
						case '6months':
						case '6 months':
							$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 180 days ' ) );;
							break;
							
						# Last 365 Days to date inclusive
						case '365':
						case '365days':
						case '365 days':
						case '12months':
						case '12 months':
						case '1year':
						case '1 year':
							$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 1 year' ) );;
							break;
						
					}
				}
			
			$date_to 	= _datetime();
			$date_from 	= !empty( $where['date_from'] )  ? date( 'Y-m-d 00:00:01', strtotime( _datetime( $where['date_from'] ) ) )  : $date_from;
			$date_to 	= !empty( $where['date_to'] ) 	 ? date( 'Y-m-d 23:59:59', strtotime( _datetime( $where['date_to'] ) ) ) 	: $date_to;;

			$site_details = $this->db->select( 'site.site_id, site.site_name, site.site_postcodes', false )
				->where( 'site.account_id', $account_id )
				->where( 'site.site_id', $site_id )
				->where( 'site.archived !=', 1 )
				->get( 'site' )
				->row();
			
			if( $site_details ){
				
				$discipline_id = !empty( $where['discipline_id'] ) ? $where['discipline_id'] : $discipline_id;
				
				if( !empty( $discipline_id ) ){
					$this->db->where( 'account_discipline.discipline_id', $discipline_id );
				}
				
				$query = $this->db->select( 'account_discipline.*, discipline.discipline_name, discipline.discipline_colour, discipline.discipline_colour_hex, discipline.discipline_image_url, discipline.discipline_icon', false )
					->join( 'discipline', 'discipline.discipline_id = account_discipline.discipline_id', 'left' )
					->where( 'account_discipline.account_id', $account_id )
					->where( 'account_discipline.is_active', 1 )
					->order_by( 'account_discipline.account_discipline_name' )
					->get( 'account_discipline' );

				if( $query->num_rows() > 0 ){
					$stats_data = [];
					foreach(  $query->result() as $k => $row ){
						$random_multiplier = rand( 1, 10 );
						$stats_data[] = [
							'discipline_id' 			=> $row->discipline_id,
							'discipline_name' 			=> $row->account_discipline_name,
							'discipline_colour' 		=> trim( $row->discipline_colour ),
							'discipline_colour_hex' 	=> $row->discipline_colour_hex,
							'discipline_image_url' 		=> $row->account_discipline_image_url,
							'total_inspections' 		=> $random_multiplier*200,
							'passed_inspections' 		=> $random_multiplier*168,
							'passed_inspections_percent'=> number_format( ( ( ($random_multiplier*168) / ($random_multiplier*200) )*100 ), 1 ),
							'failed_inspections' 		=> $random_multiplier*12,
							'failed_inspections_percent'=> number_format( ( ( ($random_multiplier*12) / ($random_multiplier*200) )*100 ), 1 ),
							'recommendations' 		 	=> $random_multiplier*20,
							'recommendations_percent'	=> number_format( ( ( ($random_multiplier*20)/($random_multiplier*200) )*100 ), 1 ),
						];
					}
				}
				
				$site_details->outcomes_info = $stats_data;
				$site_details->extra_info 	 = [
					'last_updated' 	=> _datetime(),
					'date_range' 	=> $date_range,
					'date_from' 	=> $date_from,
					'date_to' 		=> $date_to,
				];
				$result = $site_details;
				$this->session->set_flashdata( 'message','Overdue Jobs data found' );
			} else {
				$this->session->set_flashdata( 'message','No data found matching your criteria' );
			}		
		}
		
		return $result;
	}
	
	/**
	* Get Dscipline Attendance information
	*/
	public function get_discipline_attendance_info( $account_id = false, $discipline_id = false, $where = false ){

		$result = false;
		if( !empty( $account_id ) && !empty( $discipline_id ) ){
			$where		= convert_to_array( $where );
			$site_id	= !empty( $where['site_id'] ) ? $where['site_id'] : false;
			$date_range	= !empty( $where['date_range'] ) ? $where['date_range'] : '7 days';
			
			if( !empty( $date_range ) ){
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
			}
			
			$date_from 	= !empty( $where['date_from'] )  ? date( 'Y-m-d', strtotime( _datetime( $where['date_from'] ) ) )   : $date_from;
			$date_to 	= !empty( $where['date_to'] ) 	 ? date( 'Y-m-d', strtotime( _datetime( $where['date_to'] ) ) ) 	: date( 'Y-m-d', strtotime( _datetime() ) );

			if( !empty( $site_id ) ){
				$this->db->where( 'job.site_id', $site_id );
			}

			$this->db->where( '( ( job.job_date >= "'.$date_from.'" ) OR ( job.due_date >= "'.$date_from.'" ) )' );
			$this->db->where( '( ( job.job_date <= "'.$date_to.'" ) OR ( job.due_date <= "'.$date_to.'" ) )' );
		
			$this->db->select( 'job_types.discipline_id, account_discipline.account_discipline_name `discipline_name`, SUM( CASE WHEN status_group IN ( "inprogress", "failed", "successful" ) THEN 1 ELSE 0 END) AS `completed_visits`,
				SUM(CASE WHEN job_id > 0 THEN 1 ELSE 0 END) AS `scheduled_visits`', false
			);
				
			$attendance_info = $this->db->join( 'job_statuses', 'job_statuses.status_id = job.status_id', 'left' )
				->join( 'job_types', 'job_types.job_type_id = job.job_type_id', 'left' )
				->join( 'account_discipline', 'job_types.discipline_id = account_discipline.discipline_id', 'left' )
				->where( 'job.account_id', $account_id )
				->where( 'job_types.account_id', $account_id )
				->where( 'account_discipline.account_id', $account_id )
				->where( 'job_types.discipline_id', $discipline_id )
				->order_by('job_statuses.job_status')
				->group_by('job_types.discipline_id')
				#->group_by('job.job_id')
				->where( 'job.archived !=', 1 )
				->get( 'job' )
				->row();

			if( !empty( $attendance_info ) ){
				$result = $attendance_info;
			}
		}
		return $result;
		
	}
	
	
	/**
	* Get Discipline Inspection Outcomes
	*/	
	public function get_discipline_outcomes_info( $account_id = false, $discipline_id = false, $where = false ){

		$result = false;
		if( !empty( $account_id ) && !empty( $discipline_id ) ){
			$where			= convert_to_array( $where );
			$site_id		= !empty( $where['site_id'] ) ? $where['site_id'] : false;
			$date_range		= !empty( $where['date_range'] ) ? $where['date_range'] : '7 days';
			$detailed_info 	= !empty( $where['detailed_info'] ) ? $where['detailed_info'] : false;
			
			##Extract Asset IDs or simply use the Site ID
			if( !empty( $site_id ) ){
				
				$assets = $this->db->select( 'asset.asset_id', false )
					->get_where( 'asset', [ 'asset.site_id' => $site_id ] );
				
				if( $assets->num_rows() > 0 ){
					$asset_ids = array_column( $assets->result_array(), 'asset_id' );
					$asset_ids_str 	= implode( ',', $asset_ids ); 
					$sql_combi 		= '( audit.site_id = "'.$site_id.'" OR audit.asset_id IN ('.$asset_ids_str.' ) )';
				} else {
					$sql_combi		= '( audit.site_id = "'.$site_id.'" )';
				}
				
				$this->db->where( $sql_combi );
			}
			
			if( !empty( $date_range ) ){
				switch( strtolower( $date_range ) ){
					# Last 7 Days to date inclusive
					default:
					case '7':
					case '7days':
					case '7 days':
					case '1week':
					case '1 week':
						$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 7 days ' ) );;
						break;
						
					# Last 30 Days to date inclusive
					case '30':
					case '30days':
					case '30 days':
					case '1month':
					case '1 month':
						$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 30 days ' ) );;
						break;
						
					# Last 90 Days to date inclusive
					case '90':
					case '90days':
					case '90 days':
					case '3months':
					case '3 months':
						$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 90 days ' ) );;
						break;
						
					# Last 180 Days to date inclusive
					case '180':
					case '180days':
					case '180 days':
					case '6months':
					case '6 months':
						$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 180 days ' ) );;
						break;
						
					# Last 365 Days to date inclusive
					case '365':
					case '365days':
					case '365 days':
					case '12months':
					case '12 months':
					case '1year':
					case '1 year':
						$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 1 year' ) );;
						break;
					
				}
			}
			
			$date_from 	= !empty( $where['date_from'] )  ? date( 'Y-m-d 00:00:01', strtotime( _datetime( $where['date_from'] ) ) )   : $date_from;
			$date_to 	= !empty( $where['date_to'] ) 	 ? date( 'Y-m-d 23:59:59', strtotime( _datetime( $where['date_to'] ) ) ) 	 : date( 'Y-m-d H:i:s', strtotime( _datetime() ) );

			$this->db->where( '( audit.date_created >= "'.$date_from.'" )' );
			$this->db->where( '( audit.date_created <= "'.$date_to.'" )' );
		
			if( !empty( $detailed_info ) ){
				$this->db->select( 'audit.audit_id, audit.audit_status, audit.date_created, audit_result_statuses.result_status, audit_result_statuses.result_status_group `audit_outcome`, audit.job_id, audit_types.audit_type_id, audit_types.audit_type, audit_types.discipline_id, account_discipline.account_discipline_name `discipline_name`', false
				)->group_by('audit.audit_id');
			} else {
				$this->db->select( 'audit_types.discipline_id, account_discipline.account_discipline_name `discipline_name`, 
					SUM( CASE WHEN result_status_group IN ( "passed", "not_set" ) THEN 1 ELSE 0 END) AS `passed_inspections`,
					SUM( CASE WHEN result_status_group IN ( "failed" ) THEN 1 ELSE 0 END) AS `failed_inspections`,
					SUM( CASE WHEN result_status_group IN ( "recommendations" ) THEN 1 ELSE 0 END) AS `recommendations`,
					SUM( CASE WHEN audit_id > 0 THEN 1 ELSE 0 END) AS `total_inspections`', false
				)->group_by('audit_types.discipline_id');
			}

			$query = $this->db->join( 'audit_result_statuses', 'audit_result_statuses.audit_result_status_id = audit.audit_result_status_id', 'left' )
				->join( 'audit_types', 'audit_types.audit_type_id = audit.audit_type_id', 'left' )
				->join( 'account_discipline', 'audit_types.discipline_id = account_discipline.discipline_id', 'left' )
				->where( 'audit.account_id', $account_id )
				->where( 'audit_types.account_id', $account_id )
				->where( 'account_discipline.account_id', $account_id )
				->where( 'audit_types.discipline_id', $discipline_id )
				->where( 'audit.audit_status', 'Completed' )
				->order_by('audit_result_statuses.result_status')
				->where( 'audit.archived !=', 1 )
				->get( 'audit' );

			if( $query->num_rows() > 0 ){
				
				if( !empty( $detailed_info ) ){
				
					$outcomes_info = [];
					foreach( $query->result() as $k => $row ){
						$outcomes_info[$row->audit_type_id]['audit_type_id'] 	= $row->audit_type_id;
						$outcomes_info[$row->audit_type_id]['audit_type'] 		= $row->audit_type;
						$outcomes_info[$row->audit_type_id]['total_outcomes'] 	= !empty( $outcomes_info[$row->audit_type_id]['total_outcomes'] ) ? ( ( $outcomes_info[$row->audit_type_id]['total_outcomes'] ) + 1 ) : 1;
						$outcomes_info[$row->audit_type_id]['audit_outcomes'][] = [
							'audit_id' 		=> $row->audit_id,
							'audit_status' 	=> $row->audit_status,
							'date_created' 	=> $row->date_created,
							'audit_outcome'	=> $row->audit_outcome,
							'outcome_note'	=> 'EviDoc ID: '.$row->audit_id.' &nbsp;Status: '.$row->audit_status,
							'job_id'		=> $row->job_id,
							'task_id'		=> null
						];
					}
					
				} else {
					$outcomes_info = $query->result()[0];
				}
				
				$result = $outcomes_info;
			}
		}
		return $result;
	}
	
	
	/**
	* Get Overdue Jobs By Discipline
	*/
	public function get_overdue_jobs_by_discipline( $account_id = false, $discipline_id = false, $where = false ){

		$result = false;
		if( !empty( $account_id ) && !empty( $discipline_id ) ){
			$where		= convert_to_array( $where );
			$site_id	= !empty( $where['site_id'] ) ? $where['site_id'] : false;
			$date_range	= !empty( $where['date_range'] ) ? $where['date_range'] : '7 days';
			
			if( !empty( $date_range ) ){
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
			}
			
			#$date_from 	= !empty( $where['date_from'] )  ? date( 'Y-m-d', strtotime( _datetime( $where['date_from'] ) ) )   : $date_from;
			#$date_to 	= !empty( $where['date_to'] ) 	 ? date( 'Y-m-d', strtotime( _datetime( $where['date_to'] ) ) ) 	: date( 'Y-m-d', strtotime( _datetime() ) );
			$today 		= date( 'Y-m-d', strtotime( _datetime() ) );

			##Extract Asset IDs or simply use the Site ID
			if( !empty( $site_id ) ){
				
				$assets = $this->db->select( 'asset.asset_id', false )
					->get_where( 'asset', [ 'asset.site_id' => $site_id ] );
				
				if( $assets->num_rows() > 0 ){
					$asset_ids = array_column( $assets->result_array(), 'asset_id' );
					$asset_ids_str 	= implode( ',', $asset_ids ); 
					$sql_combi 		= '( job.site_id = "'.$site_id.'" OR job.asset_id IN ('.$asset_ids_str.' ) )';
				} else {
					$sql_combi		= '( job.site_id = "'.$site_id.'" )';
				}
				
				$this->db->where( $sql_combi );
			}

			#$this->db->where( '( ( job.job_date >= "'.$date_from.'" ) OR ( job.due_date >= "'.$date_from.'" ) )' );
			#$this->db->where( '( ( job.job_date <= "'.$date_to.'" ) OR ( job.due_date <= "'.$date_to.'" ) )' );
			$this->db->where( 'job.job_date <', $today );

			$query = $this->db->select( 'job.job_id, job.job_date, job.due_date, job_statuses.job_status, job_types.job_type_id, job_types.job_type, job_types.discipline_id, account_discipline.account_discipline_name `discipline_name`, discipline.discipline_colour, discipline.discipline_colour_hex, discipline.discipline_image_url', false )
				->join( 'job_statuses', 'job_statuses.status_id = job.status_id', 'left' )
				->join( 'job_types', 'job_types.job_type_id = job.job_type_id', 'left' )
				->join( 'account_discipline', 'job_types.discipline_id = account_discipline.discipline_id', 'left' )
				->join( 'discipline', 'discipline.discipline_id = account_discipline.discipline_id', 'left' )
				->where( 'job.account_id', $account_id )
				->where( 'job_types.account_id', $account_id )
				->where( 'account_discipline.account_id', $account_id )
				->where( 'job_types.discipline_id', $discipline_id )
				->where_not_in( 'job_statuses.status_group', [ 'unassigned', 'successful', 'failed', 'cancelled' ] )
				->order_by( 'job_statuses.job_status' )
				->group_by( 'job.job_id' )
				->get( 'job' );

			if( $query->num_rows() > 0 ){
				$overdue_jobs = [];
				foreach( $query->result() as $k => $row ){
					$overdue_jobs[$row->discipline_id]['discipline_id'] 		= $row->discipline_id;
					$overdue_jobs[$row->discipline_id]['discipline_name'] 		= $row->discipline_name;
					$overdue_jobs[$row->discipline_id]['discipline_colour'] 	= $row->discipline_colour;
					$overdue_jobs[$row->discipline_id]['discipline_colour_hex'] = $row->discipline_colour_hex;
					$overdue_jobs[$row->discipline_id]['discipline_image_url'] 	= $row->discipline_image_url;
					$overdue_jobs[$row->discipline_id]['jobs_total'] 			= !empty( $overdue_jobs[$row->discipline_id]['jobs_total'] ) ? ( ( $overdue_jobs[$row->discipline_id]['jobs_total'] ) + 1 ) : 1;
					$overdue_jobs[$row->discipline_id]['jobs_info'][] 			= [
						'job_id' 		=> $row->job_id,
						'job_type' 		=> $row->job_type,
						'job_date' 		=> $row->job_date,
						'due_date' 		=> $row->due_date,
						'job_status' 	=> $row->job_status
					];
				}
				$result = $overdue_jobs;
			}
		}
		return $result;
	}
	
	
	/** Get Building Discipline Stats **/
	public function get_buildings_by_discipline( $account_id = false, $discipline_id = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		
		$result = false;
		
		if( !empty( $account_id ) && !empty( $discipline_id ) ){

			$linked_site_ids 	= [];
			
			$job_types 			= $this->db->select( 'job_type_id' )->get_where( 'job_types', [ 'account_id'=> $account_id, 'discipline_id' => $discipline_id ] );
			
			if( $job_types->num_rows() > 0 ){
				
				## Get any linked Sites via Assets
				$ids 		= array_column( $job_types->result_array(), 'job_type_id' );
				$assets 	= $this->db->select( 'job.asset_id' )
					->where( 'account_id', $account_id )
					->where( 'job.asset_id > 0' )
					->where_in( 'job_type_id', $ids )
					->group_by( 'asset_id' )
					->get( 'job' );
					
				if( $assets->num_rows() > 0 ){
					$asset_ids 		= array_column( $assets->result_array(), 'asset_id' );
					$asset_ids_str 	= implode( ',', $asset_ids ); 

					$sites 	= $this->db->select( 'asset.site_id' )
						->where( 'account_id', $account_id )
						->where_in( 'asset_id', $asset_ids )
						->group_by( 'asset_id' )
						->get( 'asset' );
					
					if( $sites->num_rows() > 0 ){
						$site_recs 		= array_column( $sites->result_array(), 'site_id' );
						$linked_site_ids		= array_merge( $linked_site_ids, $site_recs );
					}
				}

				
				## Get any linked Sites via Jobs
				$sites 	= $this->db->select( 'job.site_id' )
					->where( 'account_id', $account_id )
					->where( 'job.site_id > 0' )
					->where_in( 'job_type_id', $ids )
					->group_by( 'site_id' )
					->get( 'job' );
			
				if( $sites->num_rows() > 0 ){
					$site_ids 		= array_column( $sites->result_array(), 'site_id' );
					$linked_site_ids= array_merge( $linked_site_ids, $site_ids );
				}
			}
			
			$where			= $raw_where = convert_to_array( $where );
			$date_from 		= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 7 days ' ) );
			$site_id		= !empty( $where['site_id'] ) ? $where['site_id'] : false;
			$date_range 	= !empty( $where['date_range'] ) ? $where['date_range'] : '7 days';
			
			if( !empty( $date_range ) ){
				switch( strtolower( $date_range ) ){
					# Last 7 Days to date inclusive
					default:
					case '7':
					case '7days':
					case '7 days':
					case '1week':
					case '1 week':
						$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 7 days ' ) );;
						break;
						
					# Last 30 Days to date inclusive
					case '30':
					case '30days':
					case '30 days':
					case '1month':
					case '1 month':
						$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 30 days ' ) );;
						break;
						
					# Last 90 Days to date inclusive
					case '90':
					case '90days':
					case '90 days':
					case '3months':
					case '3 months':
						$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 90 days ' ) );;
						break;
						
					# Last 180 Days to date inclusive
					case '180':
					case '180days':
					case '180 days':
					case '6months':
					case '6 months':
						$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 180 days ' ) );;
						break;
						
					# Last 365 Days to date inclusive
					case '365':
					case '365days':
					case '365 days':
					case '12months':
					case '12 months':
					case '1year':
					case '1 year':
						$date_from 	= date( 'Y-m-d 00:00:01', strtotime( _datetime().' - 1 year' ) );;
						break;
					
				}
			}
			
			$date_to 	= _datetime();
			$date_from 	= !empty( $where['date_from'] )  ? date( 'Y-m-d 00:00:01', strtotime( _datetime( $where['date_from'] ) ) )  : $date_from;
			$date_to 	= !empty( $where['date_to'] ) 	 ? date( 'Y-m-d 23:59:59', strtotime( _datetime( $where['date_to'] ) ) ) : $date_to;
			
			if( !empty( $linked_site_ids ) ){
				
				$raw_where['linked_site_ids'] = $linked_site_ids;
				
				$this->db->select( 'site.site_id, site.site_name, site.site_postcodes, site.site_address_id' )
					->where( 'site.archived !=',1 )
					->where( 'site.account_id', $account_id )
					->where_in( 'site.site_id', $linked_site_ids );

					if( $limit > 0 ){
						$this->db->limit( $limit, $offset );
					}
					
					if( !empty( $site_id ) ){
						$this->db->where( 'site.site_id', $site_id );
					}
					
				$query = $this->db->group_by( 'site.site_id' )
					->get( 'site' );

				if( $query->num_rows() > 0 ){
					
					if( !empty( $site_id ) ){
						$where['detailed_info'] = 1;
						$row 				 = $query->result()[0];
						$outcomes_info 		 = $this->get_discipline_outcomes_info( $account_id, $discipline_id, $where );

						$outcomes_total		 = !empty( $outcomes_info ) ? array_sum( array_column( $outcomes_info, 'total_outcomes' ) ) : '0';
						$row->outcome_status = null;
						$row->outcomes_info  = !empty( $outcomes_info ) ? array_values( $outcomes_info ) : '0';
						$row->total_outcomes = $outcomes_total;
						$this->session->set_flashdata( 'message','Building Disciplines data found' );
						return $row;
						
					} else {
					
						$data = [];
						foreach( $query->result() as $key => $row ){
							$where['detailed_info'] = 1;
							$where['site_id'] 	 = $row->site_id;
							$outcomes_info 		 = $this->get_discipline_outcomes_info( $account_id, $discipline_id, $where );
							$outcomes_total		 = !empty( $outcomes_info ) ? array_sum( array_column( $outcomes_info, 'total_outcomes' ) ) : '0';
							$row->outcome_status = null;
							$row->total_outcomes = $outcomes_total;;
							$data[$key] = $row;
						}
						
						$result = ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
					
						$collated_result = [
							'data' 		 => $data,
							'extra_info' => [
								'last_updated' 	=> _datetime(),
								'date_range' 	=> $date_range,
								'date_from' 	=> $date_from,
								'date_to' 		=> $date_to,
							]				
						];

						$result->records 			= $collated_result;
						$counters 					= $this->get_buildings_by_discipline_totals( $account_id, $discipline_id, $raw_where, $limit );
						$result->counters->total 	= ( !empty( $counters->total ) ) ? $counters->total : count( $data );
						$result->counters->pages 	= ( !empty( $counters->pages ) ) ? $counters->pages : 3;
						$result->counters->limit  	= !empty( $limit > 0 ) ? $limit : count( $result->records );
						$result->counters->offset 	= 0;
						$this->session->set_flashdata( 'message','Building Disciplines data found' );
					}
				} else {
					$this->session->set_flashdata( 'message','No data found matching your criteria' );
				}
			}

			return $result;

		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}
		return $result;
	}
	
	
	
	/**
	* Get Site Disciplines Totals
	*/
	public function get_buildings_by_discipline_totals( $account_id = false, $discipline_id = false, $where = false, $limit = DEFAULT_LIMIT ){
		if( !empty( $account_id ) && !empty( $discipline_id ) ){
			
			if( !empty( $where['linked_site_ids'] ) ){
				$linked_site_ids = $where['linked_site_ids'];
			} else {
				
				$linked_site_ids 	= [];
			
				$job_types 			= $this->db->select( 'job_type_id' )->get_where( 'job_types', [ 'account_id'=> $account_id, 'discipline_id' => $discipline_id ] );
				
				if( $job_types->num_rows() > 0 ){
					
					## Get any linked Sites via Assets
					$ids 		= array_column( $job_types->result_array(), 'job_type_id' );

					$assets 	= $this->db->select( 'job.asset_id' )
						->where( 'account_id', $account_id )
						->where( 'job.asset_id > 0' )
						->where_in( 'job_type_id', $ids )
						->group_by( 'asset_id' )
						->get( 'job' );

					if( $assets->num_rows() > 0 ){
						$asset_ids 		= array_column( $assets->result_array(), 'asset_id' );
						$asset_ids_str 	= implode( ',', $asset_ids ); 

						$sites 	= $this->db->select( 'asset.site_id' )
							->where( 'account_id', $account_id )
							->where_in( 'asset_id', $asset_ids )
							->group_by( 'asset_id' )
							->get( 'asset' );
						
						if( $sites->num_rows() > 0 ){
							$site_recs 		= array_column( $sites->result_array(), 'site_id' );
							$linked_site_ids		= array_merge( $linked_site_ids, $site_recs );
						}
					}

					
					## Get any linked Sites via Jobs
					$sites 	= $this->db->select( 'job.site_id' )
						->where( 'account_id', $account_id )
						->where( 'job.site_id > 0' )
						->where_in( 'job_type_id', $ids )
						->group_by( 'site_id' )
						->get( 'job' );
					
					if( $sites->num_rows() > 0 ){
						$site_ids 		= array_column( $sites->result_array(), 'site_id' );
						$linked_site_ids= array_merge( $linked_site_ids, $site_ids );
					}
				}
			}

			if( !empty( $linked_site_ids ) ){
				
				$query = $this->db->select( 'site.site_id' )
					->where( 'site.account_id', $account_id )
					->where_in( 'site.site_id', $linked_site_ids )
					->where( 'site.archived !=',1 )
					->group_by( 'site.site_id' )
					->get( 'site' );

					$results['total'] = !empty( $query->num_rows() ) ? $query->num_rows() : 0;
					$limit 			  = ( !empty( $limit > 0 ) ) ? $limit : $results['total'];
					$results['pages'] = !empty( $query->num_rows() ) ? ceil( $query->num_rows() / $limit ) : 0;

				return json_decode( json_encode( $results ) );
			}			
		}
		return false;
	}
}