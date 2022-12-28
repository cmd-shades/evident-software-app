<?php

namespace Application\Modules\Service\Models;

class Contract_model extends CI_Model {

	function __construct(){
		parent::__construct();
		$section 	   = explode("/", $_SERVER["SCRIPT_NAME"]);
		$this->app_root= $_SERVER["DOCUMENT_ROOT"]."/".$section[1]."/";
		$this->app_root= str_replace('/index.php','',$this->app_root);
    }

	private $contract_buildings_searchable_fields  	= ['site.site_id', 'site_name', 'site.status_id', 'site_reference', 'site_address_id', 'summaryline', 'site_postcodes', 'estate_name'];

	/*
	*	Get Contract Profile record(s) data
	*/
	public function get_contract( $account_id = false, $contract_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;

		if( !empty( $account_id ) ){

			#Limit access by contract to External User Types
			if( in_array( $this->ion_auth->_current_user()->user_type_id, EXTERNAL_USER_TYPES ) ){
				$contract_access = $this->get_linked_people( $account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1] );
				$allowed_access  = !empty( $contract_access ) ? array_column( $contract_access, 'contract_id' ) : [];
				if( !empty( $allowed_access ) ){
					$this->db->where_in( "c.contract_id", $allowed_access );
				} else{
					$this->session->set_flashdata( 'message','No data found matching your criteria' );
					return false;
				}
			}

			$this->db->select( "c.contract_id, c.account_id, c.contract_ref, c.contract_name, c.contract_type_id, pt.type_name, c.contract_status_id, ps.status_name, c.contract_lead_id, CONCAT( u1.first_name, ' ', u1.last_name ) `contract_lead_name`, c.start_date, c.end_date, c.date_created, CONCAT( u.first_name, ' ', u.last_name ) `created_by_name`, c.last_modified, c.last_modified_by, CONCAT( u2.first_name, ' ', u2.last_name ) `last_modified_by_name`, c.description, c.last_note, c.archived, c.ownership" );

			if( !empty( $contract_id ) ){
				$this->db->where( "c.contract_id", $contract_id );
			}

			if( !empty( $where ) ){
				if( is_object( $where ) ){
					$where = get_object_vars( $where );
				}
				$this->db->where( $where );
			}

			$this->db->join( "user u", "u.id = c.created_by", "left" );
			$this->db->join( "user u1", "u1.id = c.contract_lead_id", "left" );
			$this->db->join( "user u2", "u2.id = c.last_modified_by", "left" );
			$this->db->join( "contract_type pt", "pt.type_id = c.contract_type_id", "left" );
			$this->db->join( "contract_status ps", "ps.status_id = c.contract_status_id", "left" );

			
			$arch_where = "( c.archived != 1 or c.archived is NULL )";
			$this->db->where( $arch_where );

			$this->db->where( "c.account_id", $account_id );

			if( $limit > 0 ){
				$this->db->limit( $limit, $offset );
			}

			$this->db->order_by( "c.contract_name, c.contract_id DESC" );

			$query = $this->db->get( "contract `c`" );

			#return $this->db->last_query();
			
			if( !empty( $query->num_rows() ) && ( $query->num_rows() > 0 ) ){
				$result 	= $query->result();
				$this->session->set_flashdata( 'message','Contract(s) data found.' );
			} else {
				$this->session->set_flashdata( 'message','Contract(s) data not found.' );
			}
		} else {
			$this->session->set_flashdata( 'message','No Account details provided.' );
		}

		return $result;
	}


	/*
	*	Function to get contract statuses for specific account_id. If they aren't exists get the default ones
	*/
	public function get_contract_statuses( $account_id = false, $status_id = false, $ordered = false ){
		$result = false;

		if( !empty( $status_id ) ){
			$select = "SELECT * FROM contract_status WHERE status_id = $status_id";
		} else {
			$select = "SELECT * FROM contract_status WHERE account_id = $account_id
						UNION ALL SELECT * FROM contract_status WHERE account_id = 0
					AND NOT EXISTS
						( SELECT 1 FROM contract_status WHERE account_id = $account_id )";
		}
		$query = $this->db->query( $select );

		if( $query->num_rows() > 0 ){
			
			$this->session->set_flashdata( 'message','Contract status(es) found.' );
 			$ordered = format_boolean( $ordered );
			if( $ordered ){
				foreach( $query->result_array() as $key => $row ){
					$result[$row['status_id']] = $row;
				}
			} else {
				$result = $query->result_array();
			}
		} else {
			$this->session->set_flashdata( 'message','Contract status(es) not found.' );
		}
		return $result;
	}


	/*
	*	Function to get contract types for specific account_id. If they aren't exists get the default ones
	*/
	public function get_contract_types( $account_id = false, $contract_type_id = false, $ordered = false ){
		$result = false;

		if( !empty( $contract_type_id ) ){
			$select = "SELECT * FROM contract_type WHERE type_id = $contract_type_id";
		} else {
			$select = "SELECT * FROM contract_type WHERE account_id = $account_id
						UNION ALL SELECT * FROM contract_type WHERE account_id = 0
					AND ( NOT EXISTS
						( SELECT 1 FROM contract_type WHERE account_id = $account_id ) )";
		}

		$query = $this->db->query( $select );

		if( $query->num_rows() > 0 ){
 			$ordered = format_boolean( $ordered );
			if( $ordered ){
				foreach( $query->result_array() as $key => $row ){
					$result[$row['type_id']] = $row;
				}
			} else {
				$result = $query->result_array();
			}
		}

		return $result;
	}


	/*
	*	Create a new contract Profile with the unique reference
	*/
	public function add_contract( $account_id = false, $post_data = false ){

		$result = false;
		if( !empty( $account_id ) && !empty( $post_data ) ){

			## validate the postdata
			$data = [];
			foreach( $post_data as $key => $value ){
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
				} elseif( in_array($key, format_long_date_columns() ) ){
					$value = format_datetime_db( $value );
				} else {
					$value = trim( $value );
				}
				$data[$key] = $value;
			}

			## build unique reference
			$unique_ref_code = 'UNKNOWN';
			if( !empty( $post_data['contract_type_id'] ) ){
				$contract_code = $this->get_contract_types( $account_id, $post_data['contract_type_id'] );
				if( !empty( $contract_code ) ){
					$unique_ref_code = $contract_code[0]['type_code'];
				}
			}

			$last_contract_id = 1;
			$this->db->select( "max( account_counter ) `last_contract_id`" );
			$query = $this->db->get_where( 'contract', ['account_id' => $account_id ] )->row();

			if( !empty( $query->last_contract_id ) && $query->last_contract_id != 0 ){
				$unique_ref_number 			= ( $query->last_contract_id + 1 );
				$data['account_counter'] 	= $unique_ref_number;
			} else {
				$unique_ref_number 			= $last_contract_id;
				$data['account_counter'] 	= $last_contract_id;
			}

			$data['account_id']		= $account_id;
			$data['date_created'] 	= date( 'Y-m-d H:i:s' );
			$data['created_by'] 	= $this->ion_auth->_current_user()->id;
			#$data['contract_ref']	= strtoupper( $unique_ref_code.$unique_ref_number.$account_id );
			$data['contract_ref']	= $this->_generate_contract_ref( $account_id, $data );

			## check conflicts
			$conflict = $this->db->get_where( 'contract', ['contract_ref' => $data['contract_ref'] ] )->row();

			if( !$conflict ){
				$data = $this->ssid_common->_filter_data( 'contract', $data );
				$this->db->insert( 'contract', $data );

				if( ( $this->db->trans_status() !== FALSE ) && ( $this->db->affected_rows() > 0 ) ){
					$data['contract_id'] = $this->db->insert_id();
					$this->session->set_flashdata( 'message', 'Contract has been created successfully.' );
					$result = $data;
				}
			} else {
				$this->session->set_flashdata( 'message', 'There is a unique reference conflict.' );
			}
		} else {
			$this->session->set_flashdata( 'message', 'No contract or Account data supplied.' );
		}

		return $result;
	}


	/*
	*	Update Contract
	*/
	public function update( $account_id = false, $contract_id = false, $contract_data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $contract_id ) && !empty( $contract_data ) ){
			$data = [];

			foreach( $contract_data as $key => $value ){
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
				}else{
					$value = trim( $value );
				}
				$data[$key] = $value;
			}

			$data['last_modified'] 		= date( 'Y-m-d H:i:s' );
			$data['last_modified_by'] 	= $this->ion_auth->_current_user()->id;

			if( !empty( $data ) ){
				
				$pre_update_record = $this->db->select( 'contract_id, contract_lead_id' )
					->get_where( 'contract', [ 'account_id'=>$account_id, 'contract_id'=>$contract_id ] )
					->row();
				
				$data['contract_ref']	= $this->_generate_contract_ref( $account_id, $data );

				## check conflicts
				$conflict = $this->db->where( 'contract.contract_id !=', $contract_id )
					->get_where( 'contract', ['contract_ref' => $data['contract_ref'] ] )->row();

				if( !$conflict ){
					$this->db->where( 'contract_id', $contract_id )->update( 'contract', $data );
					if( ( $this->db->trans_status() !== FALSE ) && ( $this->db->affected_rows() > 0 ) ){
						
						## Update Associated Users
						##if( !empty( $data['contract_lead_id'] ) && ( $data['contract_lead_id'] != $pre_update_record->contract_lead_id ) ){
							$this->update_contract_associated_users( $account_id, $data['contract_lead_id'], $pre_update_record->contract_lead_id );
						##}
						
						$this->session->set_flashdata( 'message','Contract Profile updated successfully.' );
						$result = $this->db->get_where( 'contract', ['contract_id' => $contract_id ] )->row();
					}else{
						$this->session->set_flashdata( 'message','The Contract profile hasn\'t been changed.' );
					}
				} else {
					$this->session->set_flashdata( 'message','Another contract with this Reference alredy exists!' );
				}
	
			}
		} else{
			$this->session->set_flashdata( 'message','No Account ID, no Contract Id or no new data supplied.' );
		}
		return $result;
	}

	/** Update Primary User **/
	public function update_contract_associated_users( $account_id = false, $new_primary_user_id = false, $old_primary_user_id = false ){
		return false;
		$result = false;
		if( !empty( $account_id ) && !empty( $new_primary_user_id ) ){
			
			if( !empty( $old_primary_user_id ) ){
				
				## Updated Associated Users
				$this->db->where( 'user.account_id', $account_id )
					->where( 'user.associated_user_id', $old_primary_user_id )
					->update( 'user', ['associated_user_id'=>$new_primary_user_id] );
					
				## Update Linked Contract Users
				$this->db->where( 'contract_people.account_id', $account_id )
					->where( 'contract_people.contract_lead_id', $old_primary_user_id )
					->update( 'contract_people', ['contract_lead_id'=>$new_primary_user_id] );
					
				## Drop Primary User Permission
				$this->db->where( 'user.account_id', $account_id )
					->where( 'user.id', $old_primary_user_id )
					->update( 'user', ['is_primary_user'=>0] );
				
				## Grant Primary User				
				$this->db->where( 'user.account_id', $account_id )
					->where( 'user.id', $new_primary_user_id )
					->update( 'user', ['is_primary_user'=>1, 'associated_user_id'=>$new_primary_user_id] );
			} else {
				## Grant Primary User
				$this->db->where( 'user.account_id', $account_id )
					->where( 'user.id', $new_primary_user_id )
					->update( 'user', ['is_primary_user'=>1, 'associated_user_id'=>$new_primary_user_id] );
			}
		}
		return $result;
	}

	/*
	*	Delete Contract Profile
	*/
	public function delete_contract( $account_id = false, $contract_id = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $contract_id ) ){
			$data = [ 'archived'=>1 ];
			$this->db->where( 'contract_id', $contract_id )
					->update( 'contract', $data );
			if( ( $this->db->trans_status() !== FALSE ) && ( $this->db->affected_rows() > 0 ) ){
				$this->session->set_flashdata( 'message','Contract Profile deleted successfully.' );
				$result = true;
			} else {
				$this->session->set_flashdata( 'message', 'No Contract has been deleted.' );
				$result = false;
			}
		} else {
			$this->session->set_flashdata( 'message', 'No Contract ID supplied.' );
			$result = true;
		}
		return $result;
	}


	/*
	*	Create a new workflow with the contract unique reference
	*/
	public function add_workflow( $account_id = false, $contract_id = false, $post_data = false ){

		$result = false;
		if( !empty( $account_id ) && !empty( $contract_id ) && !empty( $post_data ) ){

			## validate the postdata
			$data = [];
			foreach( $post_data as $key => $value ){
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
				} elseif( in_array($key, format_long_date_columns() ) ){
					$value = format_datetime_db( $value );
				} else {
					$value = trim( $value );
				}
				$data[$key] = $value;
			}

			$data['wf_status']			= ( !empty( $data['wf_status'] ) ) ? $data['wf_status'] : "Awaiting Action" ;
			$data['account_id']			= $account_id;
			$data['wf_date_created'] 	= date( 'Y-m-d H:i:s' );
			$data['wf_created_by'] 		= $this->ion_auth->_current_user()->id;
			$data['wf_reference']		= $contract_id.'-'.$post_data['wf_reference'];

			$data = $this->ssid_common->_filter_data( 'contract_workflow', $data );
			$this->db->insert( 'contract_workflow', $data );

			if( ( $this->db->trans_status() !== FALSE ) && ( $this->db->affected_rows() > 0 ) ){
				$data['workflow_id'] = $this->db->insert_id();
				$this->session->set_flashdata( 'message', 'Action has been created successfully.' );
				$result = $this->db->get_where( 'contract_workflow', ['wf_id' => $data['workflow_id'] ] )->row();
			}

		} else {
			$this->session->set_flashdata( 'message', 'No contract or Account data supplied.' );
		}

		return $result;
	}


	/*
	*	Get Contract Profile record(s) data
	*/
	public function get_workflows( $account_id = false, $workflow_id = false, $contract_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;

		if( !empty( $account_id ) ){

			$this->db->select( "cw.*" );
			$this->db->select( "cwn.*" );
			$this->db->select( "CONCAT( u.first_name,' ',u.last_name ) `wf_created_by_fullname`" );
			$this->db->select( "CONCAT( u1.first_name,' ',u1.last_name ) `wf_assignee_fullname`" );
			$this->db->select( "CONCAT( u2.first_name,' ',u2.last_name ) `previous_assignee_fullname`" );
			$this->db->select( "CONCAT( u3.first_name,' ',u3.last_name ) `wf_modified_by_fullname`" );

			$this->db->where( "cw.account_id", $account_id );

			if( !empty( $workflow_id ) ){
				$this->db->where( "wf_id", $workflow_id );
			}

			if( !empty( $contract_id ) ){
				$this->db->where( "contract_id", $contract_id );
			}

			if( !empty( $where ) ){
				if( is_object( $where ) ){
					$where = get_object_vars( $where );
				}
				$this->db->where( $where );
			}

			$this->db->join( "user u", "u.id = cw.wf_created_by", "left" );
			$this->db->join( "user u1", "u1.id = cw.assignee", "left" );
			$this->db->join( "user u2", "u2.id = cw.previous_assignee", "left" );
			$this->db->join( "user u3", "u3.id = cw.wf_modified_by", "left" );
			$this->db->join( "contract_wf_names cwn", "cwn.wf_name_id = cw.wf_name_id", "left" );

			$arch_where = "( cw.archived != 1 or cw.archived is NULL )";
			$this->db->where( $arch_where );

			$query = $this->db->get( "contract_workflow `cw`", $limit, $offset );

			if( !empty( $query->num_rows() ) && ( $query->num_rows() > 0 ) ){
				$result 	= $query->result();
				$this->session->set_flashdata( 'message','Workflow(s) data found.' );
			} else {
				$this->session->set_flashdata( 'message','Workflow(s) data not found.' );
			}
		} else {
			$this->session->set_flashdata( 'message','No Account or Contract details provided.' );
		}

		return $result;
	}



	/*
	*	Update Workflow Item
	*/
	public function update_workflow( $account_id = false, $workflow_id = false, $workflow_data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $workflow_id ) && !empty( $workflow_data ) ){
			$data = [];
			foreach( $workflow_data as $key => $value ){
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
				} elseif( in_array($key, format_long_date_columns() ) ){
					$value = format_datetime_db( $value );
				}else{
					$value = trim( $value );
				}
				$data[$key] = $value;
			}

			$data['wf_date_modified'] 	= date( 'Y-m-d H:i:s' );
			$data['wf_modified_by'] 	= $this->ion_auth->_current_user()->id;

			if( !empty( $data ) ){
				$this->db->where( 'wf_id', $workflow_id )->update( 'contract_workflow', $data );
				if( ( $this->db->trans_status() !== FALSE ) && ( $this->db->affected_rows() > 0 ) ){
					$this->session->set_flashdata( 'message','Workflow Item updated successfully.' );
					$result = $this->db->get_where( 'contract_workflow', ['wf_id' => $workflow_id ] )->row();
				}else{
					$this->session->set_flashdata( 'message', 'The Workflow profile hasn\'t been changed.' );
				}
			}
		} else{
			$this->session->set_flashdata( 'message','No Account ID, no Workflow Id or no new data supplied.' );
		}
		return $result;
	}





	/*
	*	Batch Update Workflow items
	*/
	public function batch_workflow_update( $account_id = false, $batch_workflow_data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $batch_workflow_data ) ){
			$data = [];

			foreach( $batch_workflow_data as $wf_id => $wf_item ){
				foreach( $wf_item as $key => $value ){
					if( strtolower( $key ) != "check" ){
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
						} elseif( in_array($key, format_long_date_columns() ) ){
							$value = format_datetime_db( $value );
						}else{
							$value = trim( $value );
						}
						$data[$wf_id][$key] = $value;
					}
				}
				$data[$wf_id]['wf_id'] 				= $wf_id;
				$data[$wf_id]['wf_date_modified'] 	= date( 'Y-m-d H:i:s' );
				$data[$wf_id]['wf_modified_by'] 	= $this->ion_auth->_current_user()->id;
			}

			if( !empty( $data ) ){
				$this->db->update_batch( 'contract_workflow', $data, 'wf_id' );

				if( ( $this->db->trans_status() !== FALSE ) && ( $this->db->affected_rows() > 0 ) ){
					$this->session->set_flashdata( 'message','Workflow Items updated successfully.' );
					$result = true;
				}else{
					$this->session->set_flashdata( 'message', 'The Workflow profiles hasn\'t been changed.' );
				}
			}
		} else{
			$this->session->set_flashdata( 'message','No Account ID, or no new data supplied.' );
		}
		return $result;
	}


	/*
	*	Delete Workflow Profile
	*/
	public function delete_workflow( $account_id = false, $workflow_id = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $workflow_id ) ){
			$data = [ 'archived'=>1 ];
			$this->db->where( 'wf_id', $workflow_id )
					->update( 'contract_workflow', $data );
			if( ( $this->db->trans_status() !== FALSE ) && ( $this->db->affected_rows() > 0 ) ){
				$this->session->set_flashdata( 'message', 'Workflow Profile deleted successfully.' );
				$result = true;
			} else {
				$this->session->set_flashdata( 'message', 'No Workflow has been deleted.' );
				$result = false;
			}
		} else {
			$this->session->set_flashdata( 'message', 'No Workflow ID or Account ID supplied' );
			$result = true;
		}
		return $result;
	}



	/*
	*	Function to get contract types for specific account_id. If they aren't exists get the default ones
	*/
	public function get_wf_task_names( $account_id = false, $wf_name_id = false ){
		$result = false;

		if( !empty( $wf_name_id ) ){
			$select = "SELECT * FROM contract_wf_names WHERE wf_name_id = $wf_name_id";
		} else {
			$select = "SELECT * FROM contract_wf_names WHERE account_id = $account_id
						UNION ALL SELECT * FROM contract_wf_names WHERE account_id = 0
					AND ( NOT EXISTS
						( SELECT 1 FROM contract_wf_names WHERE account_id = $account_id ) )";
		}

		$query = $this->db->query( $select );

		if( $query->num_rows() > 0 ){
			$result = $query->result_array();
			$this->session->set_flashdata( 'message', 'Task Names have been found.' );
		} else {
			$this->session->set_flashdata( 'message', 'Task Names not found.' );
		}

		return $result;
	}


	/*
	*	Function to add contract id to the Site(s)
	*/
	public function link_sites_to_contract( $account_id = false, $contract_id = false, $sites = false ){

		$result = false;

		if( !empty( $account_id ) && !empty( $contract_id ) && !empty( $contract_id ) ){
			$sites = array_map( 'trim', explode( ',', $sites ) );
		}

		$verify_contract = $this->db->get_where( "contract", ["account_id" => $account_id, "contract_id" => $contract_id] )->row();

		if( !$verify_contract ){
			$this->session->set_flashdata( 'message', 'Provided Contract ID doesn\'t exists.' );
		} else {

			foreach( $sites as $site_id ){
				if( !empty( $site_id ) ){

					## check if site has been already assigned to the contract
					$row = $this->db->get_where( "sites_contracts", ["account_id" => $account_id, "contract_id" => $contract_id, "site_id" => $site_id] )->row();

					$site = $this->db->get_where( "site", ["account_id" => $account_id, "site_id" => $site_id] )->row();

					if( !empty( $row ) ){
						
						if( !empty( $site ) ){
							$update_data[] = [
								"link_id" 		=> $row->link_id,
								"account_id" 	=> $account_id,
								"contract_id" 	=> $contract_id,
								"site_id" 		=> ( int ) $site_id,
								"modified_date"	=> date( 'Y-m-d H:i:s' ),
								"modified_by"	=> $this->ion_auth->_current_user()->id
							];
						}
						
					} else {
						if( !empty( $site ) ){
							$data[] = [
								"account_id" 	=> $account_id,
								"contract_id" 	=> $contract_id,
								"site_id" 		=> ( int ) $site_id,
								"created_date"	=> date( 'Y-m-d H:i:s' ),
								"created_by"	=> $this->ion_auth->_current_user()->id
							];
						}
					}

				}
			}

			if( !empty( $data ) ){
				$query = $this->db->insert_batch( "sites_contracts", $data );
				if( !empty( $this->db->affected_rows() ) && ( $this->db->affected_rows() > 0 ) ){
					$this->session->set_flashdata( 'message', 'Sites/Contracts linked successfully.' );
					$result = true;
				} else {
					$this->session->set_flashdata( 'message', 'There is no change to Sites/Contracts.' );
				}
			}
			
			if( !empty( $update_data ) ){
				$query = $this->db->update_batch( "sites_contracts", $update_data, 'link_id' );
				if( !empty( $this->db->affected_rows() ) && ( $this->db->affected_rows() > 0 ) ){
					$this->session->set_flashdata( 'message', 'Sites/Contracts linked successfully.' );
					$result = true;
				} else {
					$this->session->set_flashdata( 'message', 'There is no change to Sites/Contracts.' );
				}
			}
			
		}
		return $result;
	}


	/*
	*	Get linked Site(s)
	*/
	public function get_linked_sites( $account_id = false, $contract_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = 0 ){
		$result = false;

		if( !empty( $account_id ) && !empty( $contract_id ) ){

			$where = convert_to_array( $where );
			
			if( isset( $where['ignore_schedule_check'] ) ){
				if( !empty( $where['ignore_schedule_check'] ) ){
					$ignore_schedule_check = true;
				}
				unset( $where['ignore_schedule_check'] );
			}
		
			if( !empty( $ignore_schedule_check ) ){
				
			} else {
				$exclude_sites = $this->_linked_buildings_to_exclude( $account_id, $contract_id, $where );
				if( !empty( $exclude_sites ) && is_array( $exclude_sites ) ){			
					$this->db->where_not_in( 'sc.site_id', $exclude_sites );
				}
			}

			$this->db->select( "sc.*" );
			$this->db->select( "s.*" );
			$this->db->select( "a.summaryline" );
			$this->db->select( "CONCAT( u.first_name, ' ', u.last_name ) `created_by_fullname`" );

			$this->db->join( "site s", "sc.site_id = s.site_id", "left" );
			$this->db->join( "addresses a", "s.site_address_id = a.main_address_id", "left" );
			$this->db->join( "user u", "sc.created_by = u.id", "left" );

			$this->db->where( "sc.account_id", $account_id );
			
			$contract_ids = ( is_array( $contract_id ) ) ? $contract_id : [ $contract_id ];
			$this->db->where_in( "sc.contract_id", $contract_ids );

			$arch_where = "( s.archived != 1 or s.archived is NULL )";
			$this->db->where( $arch_where );

			if( isset( $where['ids_only'] ) ){
				if( !empty( $where['ids_only'] ) ){
					$ids_only = true;
				}
				unset( $where['ids_only'] );
			}

			if( isset( $where['include_schedules_info'] ) ){
				if( !empty( $where['include_schedules_info'] ) ){
					$include_schedules_info = true;
				}
				unset( $where['include_schedules_info'] );
			}

			if( $limit > 0 ){
				$this->db->limit( $limit, $offset );
			}

			$query = $this->db->get( "sites_contracts sc" );
			
			if( !empty( $query->num_rows() ) && ( $query->num_rows() > 0 ) ){
				
				if( !empty( $ids_only ) ){
					$result = array_column( $query->result_array(), 'site_id' );	
				} else {
					if( !empty( $include_schedules_info ) ){
						$this->load->model( 'Job_model','job_service' );
						$result = [];
						foreach( $query->result() as $k => $row ){
							$site_schedules = $this->job_service->get_schedules( $account_id, false, [ 'site_id'=>$row->site_id, 'schedule_summary'=>1 ] );
							$row->schedules_summary = !empty( $site_schedules ) ? $site_schedules : false;
							$result[$k] = $row;
						}

					} else {
						$result = $query->result();
					}
				}
				$this->session->set_flashdata( 'message','Linked Building(s) data found.' );
			} else {
				$this->session->set_flashdata( 'message','No linked Building(s) data found.' );
			}
		} else {
			$this->session->set_flashdata( 'message','No Account or Contract details provided.' );
		}

		return $result;
	}


	/*
	* 	Search Contracts by: contract_name, contract_ref, contract_lead_name
	*/
	public function contract_lookup( $account_id = false, $search_term = false, $contract_statuses= false, $contract_types = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){

		$result = false;

		if( !empty( $account_id ) ){

			#Limit access by contract to External User Types
			if( in_array( $this->ion_auth->_current_user()->user_type_id, EXTERNAL_USER_TYPES ) ){
				$contract_access = $this->get_linked_people( $account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1] );
				$allowed_access  = !empty( $contract_access ) ? array_column( $contract_access, 'contract_id' ) : [];
				if( !empty( $allowed_access ) ){
					$this->db->where_in( "c.contract_id", $allowed_access );
				} else{
					$this->session->set_flashdata( 'message','No data found matching your criteria' );
					return false;
				}
			}

			$this->db->select( "c.contract_id, c.account_id, c.contract_ref, c.contract_name, c.contract_type_id, pt.type_name, c.contract_status_id, ps.status_name, c.contract_lead_id, CONCAT( u1.first_name, ' ', u1.last_name ) `contract_lead_name`, c.start_date, c.end_date, c.date_created, CONCAT( u.first_name, ' ', u.last_name ) `created_by_name`, c.last_modified, c.last_modified_by, CONCAT( u2.first_name, ' ', u2.last_name ) `last_modified_by_name`, c.description, c.last_note, c.archived, c.ownership", false );

			if( !empty( $contract_id ) ){
				$this->db->where( "c.contract_id", $contract_id );
			}

			$where = $raw_where = convert_to_array( $where );

			if( !empty( $where ) ){
				if( is_object( $where ) ){
					$where = get_object_vars( $where );
				}
				$this->db->where( $where );
			}

			$this->db->join( "user u", "u.id = c.created_by", "left" );
			$this->db->join( "user u1", "u1.id = c.contract_lead_id", "left" );
			$this->db->join( "user u2", "u2.id = c.last_modified_by", "left" );
			$this->db->join( "contract_type pt", "pt.type_id = c.contract_type_id", "left" );
			$this->db->join( "contract_status ps", "ps.status_id = c.contract_status_id", "left" );

			$arch_where = "( c.archived != 1 or c.archived is NULL )";
			$this->db->where( $arch_where );

			$this->db->where( "c.account_id", $account_id );

			if( !empty( $search_term ) ){
				$search_fields['c.contract_name'] 		= $search_term;
				$search_fields['c.contract_ref']		= $search_term;
				$search_fields['u1.first_name']			= $search_term;
				$search_fields['u1.last_name']			= $search_term;

				$where = format_like_to_where( $search_fields );
				$this->db->where( $where );
			}

			if( $contract_statuses ){
				$contract_statuses = ( !is_array( $contract_statuses ) ) ? json_decode( $contract_statuses ) : $contract_statuses;
				$this->db->where_in( 'ps.status_id', $contract_statuses );
			}

			if( $contract_types ){
				$contract_types = ( !is_array( $contract_types ) ) ? json_decode( $contract_types ) : $contract_types;
				$this->db->where_in( 'c.contract_type_id', $contract_types );
			}


			if( !empty( $order_by ) ){
				$this->db->order_by( $order_by );
			}

			if( $limit > 0 ){
				$this->db->limit( $limit, $offset );
			}

			$query = $this->db->get( "contract `c`" );

			if( $query->num_rows() > 0 ){
				$result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
				$result->records 			= $query->result();
				$counters 					= $this->get_total_contracts( $account_id, $search_term, $contract_statuses, $contract_types, $raw_where, $limit );
				$result->counters->total 	= ( !empty( $counters->total ) ) ? $counters->total : null;
				$result->counters->pages 	= ( !empty( $counters->pages ) ) ? $counters->pages : null;
				$result->counters->limit  	= ( $limit > 0 ) ? ( int ) $limit : $result->counters->total;
				$result->counters->offset 	= ( int ) $offset;
				$this->session->set_flashdata('message','Contract(s) data found.');
			}else{
				$this->session->set_flashdata('message','No records found matching your criteria.');
			}
		} else {
			$this->session->set_flashdata( 'message','No Account or Search Term provided.' );
		}
		return $result;
	}


	/*
	* 	Search Contracts by: contract_name, contract_ref, contract_lead_name
	*/
	public function get_total_contracts( $account_id = false, $search_term = false, $contract_statuses = false, $contract_types = false, $where = false, $limit = DEFAULT_LIMIT ){
		$result = false;

		if( !empty( $account_id ) ){

			#limit access by contract
			if( in_array( $this->ion_auth->_current_user()->user_type_id, EXTERNAL_USER_TYPES ) ){
				$contract_access = $this->get_linked_people( $account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1] );
				$allowed_access  = !empty( $contract_access ) ? array_column( $contract_access, 'contract_id' ) : [];
				if( !empty( $allowed_access ) ){
					$this->db->where_in( "c.contract_id", $allowed_access );
				} else{
					$this->session->set_flashdata( 'message','No data found matching your criteria' );
					return false;
				}
			}

			$this->db->select( "c.contract_id, c.account_id, c.contract_ref, c.contract_name, c.contract_type_id, pt.type_name, c.contract_status_id, ps.status_name, c.contract_lead_id, CONCAT( u1.first_name, ' ', u1.last_name ) `contract_lead_name`, c.start_date, c.end_date, c.date_created, CONCAT( u.first_name, ' ', u.last_name ) `created_by_name`, c.last_modified, c.last_modified_by, CONCAT( u2.first_name, ' ', u2.last_name ) `last_modified_by_name`, c.description, c.last_note, c.archived, c.ownership", false );

			if( !empty( $contract_id ) ){
				$this->db->where( "c.contract_id", $contract_id );
			}

			if( !empty( $where ) ){
				if( is_object( $where ) ){
					$where = get_object_vars( $where );
				}
				$this->db->where( $where );
			}

			$this->db->join( "user u", "u.id = c.created_by", "left" );
			$this->db->join( "user u1", "u1.id = c.contract_lead_id", "left" );
			$this->db->join( "user u2", "u2.id = c.last_modified_by", "left" );
			$this->db->join( "contract_type pt", "pt.type_id = c.contract_type_id", "left" );
			$this->db->join( "contract_status ps", "ps.status_id = c.contract_status_id", "left" );

			$arch_where = "( c.archived != 1 or c.archived is NULL )";
			$this->db->where( $arch_where );

			$this->db->where( "c.account_id", $account_id );

			if( !empty( $search_term ) ){
				$search_fields['c.contract_name'] 		= $search_term;
				$search_fields['c.contract_ref']		= $search_term;
				$search_fields['u1.first_name']			= $search_term;
				$search_fields['u1.last_name']			= $search_term;

				$where = format_like_to_where( $search_fields );
				$this->db->where( $where );
			}

			if( $contract_statuses ){
				$contract_statuses = ( !is_array( $contract_statuses ) ) ? json_decode( $contract_statuses ) : $contract_statuses;
				$this->db->where_in( 'ps.status_id', $contract_statuses );
			}

			if( $contract_types ){
				$contract_types = ( !is_array( $contract_types ) ) ? json_decode( $contract_types ) : $contract_types;
				$this->db->where_in( 'c.contract_type_id', $contract_types );
			}

			$query = $this->db->get( "contract `c`" );

		} else {
			$this->session->set_flashdata( 'message','No Account or Search Term provided.' );
		}

		$query 			  = $this->db->from( 'project `p`' )->count_all_results();
		$results['total'] = !empty( $query ) ? $query : 0;
		$limit 			  = ( !empty( $limit > 0 ) ) ? $limit : $results['total'];
		$results['pages'] = !empty( $query ) ? ceil( $query / $limit ) : 0;

		return json_decode( json_encode( $results ) );
	}



	/*
	*	Unlink Stie from the Contract
	*/
	public function unlink_site_from_contract( $account_id = false, $contract_id = false, $site_id = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $contract_id ) && !empty( $site_id ) ){

			$contract = $this->get_contract( $account_id, $contract_id );

			if( !empty( $contract ) ){

				$this->db->select( "link_id" );
				$where = [
					"account_id" 	=> $account_id,
					"contract_id" 	=> $contract_id,
					"site_id" 		=> $site_id,
				];
				$link_id = $this->db->get_where( "sites_contracts", $where )->row()->link_id;

				if( !empty( $link_id ) ){
					$this->db->where( "link_id", $link_id );
					$unlink_site = $this->db->delete( "sites_contracts", $where );
					if( $this->db->affected_rows() > 0 ){
						$result = true;
						$this->session->set_flashdata( 'message', 'Building has been unlinked from the contract.' );
					} else {
						$this->session->set_flashdata( 'message', 'Unlink Building request failed.' );
					}
				} else {
					$this->session->set_flashdata( 'message', 'Unlink Building request failed.' );
				}
			} else {
				$this->session->set_flashdata( 'message', 'No Contract has been found.' );
			}
		} else {
			$this->session->set_flashdata( 'message', 'Request is missing required information' );
		}
		return $result;
	}


	/*
	* 	Get quick stats
	*/
	public function get_quick_stats( $account_id = false, $where = false, $offset = 0, $limit = 100 ){
		$result = false;

		if( !empty( $account_id ) ){

			#Limit access by contract to External User Types
			if( in_array( $this->ion_auth->_current_user()->user_type_id, EXTERNAL_USER_TYPES ) ){
				$contract_access = $this->get_linked_people( $account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1] );
				$allowed_access  = !empty( $contract_access ) ? array_column( $contract_access, 'contract_id' ) : [];
				if( !empty( $allowed_access ) ){
					$this->db->where_in( "contract.contract_id", $allowed_access );
				} else{
					$this->session->set_flashdata( 'message','No data found matching your criteria' );
					return false;
				}
			}


			if( !empty( $where ) ){
				if( is_object( $where ) ){
					$where = get_object_vars( $where );
				}
				$this->db->where( $where );
			}

			$this->db->select( "contract_status_id, count( contract_id ) `contract_number`, contract_status.status_name", false );
			$this->db->join( "contract_status", "contract_status.status_id = contract.contract_status_id", "left" );
			$this->db->group_by( "contract_status_id" );
			$this->db->where( "contract.account_id", $account_id );
			$query = $this->db->get( "contract" );

			if( $query->num_rows() > 0 ){
				foreach( $query->result() as $key => $row ){
					$result[$row->contract_status_id] = $row->contract_number;
				}
				$this->session->set_flashdata( 'message','Stats found' );
			} else {
				$this->session->set_flashdata( 'message','Stats not found' );
			}
		} else {
			$this->session->set_flashdata( 'message','No Account ID Provided' );
		}
		return $result;
	}

	/*
	*	Get all assets attached to a contract
	*/
	public function get_linked_assets( $account_id = false, $contract_id = false, $asset_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = 0 ){

		$result = false;

		if( !empty( $account_id ) ){

			$this->db->select( 'contract.contract_name, ca.*, asset.*, contract.contract_id `contract_id`, contract_status.status_name, contract_type.type_name, asset_types.asset_type, categories.category_name, CONCAT( user.first_name, " ", user.last_name ) `created_by`' )
				->join( 'contract', 'contract.contract_id = ca.contract_id', 'left' )
				->join( 'contract_status', 'contract_status.status_id = contract.contract_status_id', 'left' )
				->join( 'contract_type', 'contract_type.type_id = contract.contract_type_id', 'left' )
				->join( 'asset', 'asset.asset_id = ca.asset_id', 'left' )
				->join( 'asset_types', 'asset_types.asset_type_id = asset.asset_type_id', 'left' )
				->join( 'audit_categories `categories`', 'categories.category_id = asset_types.category_id', 'left' )
				->join( 'user', 'ca.created_by = user.id', 'left' )
				->where( 'ca.account_id', $account_id );

				if( !empty( $contract_id ) ){
					$this->db->where( 'ca.contract_id', $contract_id );
				}

				if( !empty( $asset_id ) ){
					$this->db->where( 'ca.asset_id', $asset_id );
				}

				if( $limit > 0 ){
					$this->db->limit( $limit, $offset );
				}

			$query = $this->db->get( 'contract_assets ca' );

			if( $query->num_rows() > 0 ){
				$result = $query->result();
				$this->session->set_flashdata( 'message','Linked assets / contracts data found.' );
			} else {
				$this->session->set_flashdata( 'message','Linked assets / contracts data not found.' );
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information.' );
		}

		return $result;

	}

	/**
	* Get all Stock and BOMS attached to a Contract
	*/
	public function get_contract_consumed_items( $account_id = false, $contract_id = false, $item_type = false, $grouped = false ){
		$result = false;
		if( $contract_id ){

			if( !empty( $item_type ) ){
				$item_type = $this->_get_item_type( $item_type );
			}

			$job_ids 	= $this->get_contract_jobs( $account_id, $contract_id, ['ids_only'=>1] );

			if( !empty( $job_ids ) && is_array( $job_ids ) ){
				$job_ids = implode( ',', $job_ids );
				$sql_str = "( SELECT job_consumed_items.id, job_consumed_items.job_id, job_consumed_items.item_code, job_consumed_items.item_qty, job_consumed_items.price, job_consumed_items.price_adjusted, job_consumed_items.item_type, stock_items.item_name
							FROM job_consumed_items JOIN stock_items ON job_consumed_items.item_code = stock_items.item_code
							WHERE job_consumed_items.job_id IN (".$job_ids.") ";
							if( !empty( $account_id ) ){
								$sql_str .= "AND stock_items.account_id = '". $account_id."' ";
							}
							if( !empty( $item_type ) ){
								$sql_str .= "AND job_consumed_items.item_type = '". $item_type."' ";
							}
				$sql_str .= "ORDER BY stock_items.item_name ) ";
				$sql_str .= "UNION ALL ";
				$sql_str .= "( SELECT job_consumed_items.id, job_consumed_items.job_id, job_consumed_items.item_code, job_consumed_items.item_qty, job_consumed_items.price, job_consumed_items.price_adjusted, job_consumed_items.item_type, bom_items.item_name
							FROM job_consumed_items JOIN bom_items ON job_consumed_items.item_code = bom_items.item_code
							WHERE job_consumed_items.job_id IN (".$job_ids.") ";
							if( !empty( $account_id ) ){
								$sql_str .= "AND bom_items.account_id = '". $account_id."' ";
							}
							if( !empty( $item_type ) ){
								$sql_str .= "AND job_consumed_items.item_type = '". $item_type."' ";
							}
				$sql_str .= "ORDER BY bom_items.item_name ) ";

				$query = $this->db->query( $sql_str );

				if( $query->num_rows() > 0 ){
					if( $grouped ){
						$data = [];
						foreach( $result = $query->result() as $k => $row ){
							$group 			= ( in_array( $row->item_type, ['bom','boms'] ) ) ? 'boms' : 'stock';
							$data[$group][] = $row;
						}
						$result = $data;
					} else {
						$result = $query->result();
					}
					$this->session->set_flashdata( 'message','Consumed items found' );
				} else {
					$this->session->set_flashdata( 'message','No data found' );
				}
			} else {
				$this->session->set_flashdata( 'message','No data found' );
			}
		}else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}

		return $result;
	}


	/** Fetch all Job IDs attached to a contract **/
	public function get_contract_jobs( $account_id = false, $contract_id = false, $where = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $contract_id ) ){
			$check_contract_exists = $this->db->get_where( 'contract', [ 'account_id'=>$account_id, 'contract_id'=>$contract_id ] )->row();
			if( !empty( $check_contract_exists ) ){

				$where = convert_to_array( $where );
				if( !empty( $where['ids_only'] ) ){
					$this->db->select( 'job.job_id', false );
					$ids_only = true;
					unset( $where['ids_only'] );
				} else {
					$this->db->select( 'job.*, job.account_id', false );
				}

				$query = $this->db->join( 'sites_contracts', 'job.site_id = sites_contracts.site_id', 'left' )
					//->join( 'sites_contracts', 'contract.contract_id = site.contract_id', 'left' )
					->join( 'contract', 'contract.contract_id = sites_contracts.contract_id', 'left' )
					->join( 'customer', 'customer.customer_id = job.customer_id', 'left' )
					->where( 'job.account_id', $account_id )
					->where( 'contract.contract_id', $contract_id )
					->or_where( 'sites_contracts.contract_id', $contract_id )
					->or_where( 'job.contract_id', $contract_id )
					->or_where( 'customer.contract_id', $contract_id )
					->group_by( 'job.job_id' )
					->get( 'job' );
					
				if( $query->num_rows() > 0 ){
					$result = ( !empty( $ids_only ) ) ? array_column( $query->result(), 'job_id' ) : $query->result();
					$this->session->set_flashdata( 'message','Contract Jobs found' );
				} else {
					$this->session->set_flashdata( 'message','No data found' );
				}
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}
		return $result;
	}


	/**
	* Assign People to a Contract
	*/
	public function link_people( $account_id = false, $contract_id = false, $people_data = false ){

		$result = false;

		if( !empty( $account_id ) && !empty( $contract_id ) && !empty( $people_data ) ){

			$people_data 		= convert_to_array( $people_data );
			$person_ids			= !empty( $people_data['linked_people'] ) ? $people_data['linked_people'] : false;
			$contract_lead_id	= !empty( $people_data['contract_lead_id'] ) ? $people_data['contract_lead_id'] : $this->ion_auth->_current_user->id;
			$person_ids			= ( is_json( $person_ids ) ) ? json_decode( $person_ids ) : $person_ids;

			if( !empty( $person_ids ) ){
				$person_ids 	= array_diff( $person_ids, [ $contract_id ] );
				foreach( $person_ids as $person_id ){
					$condition = $data = [
						'contract_id'		=> $contract_id,
						'person_id'			=> $person_id,
						'account_id'		=> $account_id,
						'contract_lead_id'	=> $contract_lead_id
					];

					$check_exists = $this->db->get_where( 'contract_people', $data )->row();
					if( !empty( $check_exists ) ){
						$data['last_modified_by'] = $this->ion_auth->_current_user->id;
						$this->db->where( 'contract_people.id', $check_exists->id  )
							->update( 'contract_people', $data );
					} else {
						$data['linked_by'] = $this->ion_auth->_current_user->id;
						$this->db->insert( 'contract_people', $data );
					}
					
					## Update record with associated user
					/* $this->db->where( 'account_id', $account_id )
						->where( 'id', $person_id )
						->update( 'user', [ 'associated_user_id'=>$contract_lead_id ] ); */
				}

				if( $this->db->affected_rows() > 0 || ( $this->db->trans_status() !== false ) ){
					$result = $this->get_linked_people( $account_id, $contract_id );
					$this->session->set_flashdata( 'message', 'People linked successfully.' );
				}
			} else {
				$this->session->set_flashdata( 'message', 'There was a problem problem processing your request.' );
			}
		} else {
			$this->session->set_flashdata( 'message', 'Your request is missing required information.' );
		}
		return $result;
	}


	/**
	* Unlink People from Contract
	*/
	public function unlink_people( $account_id = false, $contract_id = false, $postdata = false ){
		$result = false;
		if( !empty( $contract_id ) && !empty( $postdata ) ){

			$postdata 			= convert_to_array( $postdata );
			$linked_people		= !empty( $postdata['linked_people'] ) ? $postdata['linked_people'] : false;
			$contract_lead_id	= !empty( $people_data['contract_lead_id'] ) ? $people_data['contract_lead_id'] : $this->ion_auth->_current_user->id;
			$linked_people		= ( is_json( $linked_people ) ) ? json_decode( $linked_people ) : $linked_people;
			$deleted			= [];

			if( !empty( $linked_people ) ){
				foreach( $linked_people as $k => $val ){
					$data = [
						'contract_id'	=> $contract_id,
						'person_id'		=> $val
					];

					$check_exists = $this->db->limit(1)->get_where( 'contract_people', $data )->row();
					if( !empty( $check_exists ) ){
						$this->db->where( $data );
						$this->db->delete( 'contract_people' );
						$this->ssid_common->_reset_auto_increment( 'contract_people', 'id' );
					}
					$deleted[] = $data;
					
					## Update record with associated user
					/* $this->db->where( 'user.account_id', $account_id )
						->where( 'user.id', $val )
						->update( 'user', [ 'associated_user_id'=>null ] ); */
					
				}
			} else if( !empty( $postdata['person_id'] ) ) {
				$data = [
					'contract_id'	=> $contract_id,
					'person_id'		=> $postdata['person_id']
				];

				$check_exists = $this->db->limit(1)->get_where( 'contract_people', $data )->row();
				if( !empty( $check_exists ) ){
					$this->db->where( $data );
					$this->db->delete( 'contract_people' );
					$deleted[] = $data;
					$this->ssid_common->_reset_auto_increment( 'contract_people', 'id' );
					
					## Update record with associated user
					/* $this->db->where( 'user.account_id', $account_id )
						->where( 'user.id', $postdata['person_id'] )
						->update( 'user', [ 'associated_user_id'=>null ] ); */
					
				}
			}

			if( !empty( $deleted ) ){
				$result = $deleted;
				$this->session->set_flashdata( 'message','Person/People unlinked successfully' );
			} else {
				$this->session->set_flashdata( 'message','No People were unlinked' );
			}
		} else {
			$this->session->set_flashdata( 'message','You request is missing required information' );
		}
		return $result;
	}


	/*
	* 	Get a list of all linked People based on contract
	*/
	public function get_linked_people( $account_id = false, $contract_id = false, $person_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = 0 ){
		$result = null;
		if( !empty( $account_id ) ){

			$where		 	= convert_to_array( $where );
			$contract_id 	= !empty( $contract_id ) ? $contract_id : ( !empty( $where['contract_id'] ) ? $where['contract_id'] : false );
			$person_id 		= !empty( $person_id ) ? $person_id : ( !empty( $where['person_id'] ) 		? $where['person_id'] : false );
			$as_arraay		= ( !empty( $where['as_arraay'] ) ) ? true : false;

			if( !empty( $contract_id ) ){
				$this->db->where( 'cp.contract_id',$contract_id );
			}

			if( !empty( $person_id ) ){
				$this->db->select( 'contract.contract_name, cp.contract_id,cp.date_linked, concat(creator.first_name," ",creator.last_name) `linked_by`, concat(modifier.first_name," ",modifier.last_name) `last_modified_by`, cp.contract_lead_id, concat(contract_lead.first_name," ",contract_lead.last_name) `contract_leader`',false )
					->join( 'contract', 'contract.contract_id = cp.contract_id', 'left' )
					->join(	'user creator', 'creator.id = cp.linked_by', 'left')
					->join(	'user modifier', 'modifier.id = cp.last_modified_by', 'left')
					->join(	'user contract_lead', 'contract_lead.id = cp.contract_lead_id', 'left')
					->where( 'cp.person_id',$person_id )
					->where( 'contract.archived !=', 1 )
					->where( 'contract.account_id',$account_id )
					->group_by( 'cp.contract_id' );
			} else {
				$this->db->select( 'person.first_name, person.last_name, person.email, cp.id, cp.contract_id,cp.date_linked, concat(creator.first_name," ",creator.last_name) `linked_by`, concat(modifier.first_name," ",modifier.last_name) `last_modified_by`, people.*, cp.contract_lead_id, concat(contract_lead.first_name," ",contract_lead.last_name) `contract_leader`',false )
					->join( 'people', 'people.person_id = cp.person_id', 'left' )
					->join(	'user person', 'person.id = people.person_id', 'left')
					->join(	'user creator', 'creator.id = cp.linked_by', 'left')
					->join(	'user modifier', 'modifier.id = cp.last_modified_by', 'left')
					->join(	'user contract_lead', 'contract_lead.id = cp.contract_lead_id', 'left')
					->where( 'people.is_active', 1 )
					->where( 'people.account_id', $account_id );
			}

			$query = $this->db->get( 'contract_people `cp`' );

			if( $query->num_rows() > 0 ){
				$this->session->set_flashdata( 'message','Linked people data found.' );
				$result = !empty( $as_arraay ) ? $query->result() : $query->result_array();
			}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information.' );
		}
		return $result;
	}


	/**
	* 	Get all Stock and BOMS attached to a Contract - CSV export version
	*/
	public function get_contract_consumed_items_export( $account_id = false, $contract_id = false, $item_type = false, $grouped = false, $where = false ){
		$result = false;

		if( !empty( $contract_id ) && !empty( $item_type ) ){

			if( !empty( $item_type ) ){
				$item_type = $this->_get_item_type( $item_type );
			}

			$job_ids 	= $this->get_contract_jobs( $account_id, $contract_id, ['ids_only'=>1] );

			if( !empty( $job_ids ) && is_array( $job_ids ) ){

				$date_from 	= false;
				$date_to 	= false;

				$where = convert_to_array( $where );

				if( !empty( $where ) ){
					if( !empty( $where['date_from'] ) ){
						$date_from = format_date_db( $where['date_from'] );
						unset( $where['date_from'] );
					}

					if( !empty( $where['date_to'] ) ){
						$date_to = format_date_db( $where['date_to'] );
						unset( $where['date_to'] );
					}
				}

				$job_ids = implode( ',', $job_ids );
				$sql_str = "( SELECT job_consumed_items.id, job_consumed_items.job_id, job_consumed_items.item_code, job_consumed_items.item_qty, job_consumed_items.price, job_consumed_items.price_adjusted, job_consumed_items.item_type, stock_items.item_name
							FROM job_consumed_items JOIN stock_items ON job_consumed_items.item_code = stock_items.item_code
							LEFT JOIN job ON job_consumed_items.job_id = job.job_id
							WHERE job_consumed_items.job_id IN (".$job_ids.") ";

							if( !empty( $account_id ) ){
								$sql_str .= "AND stock_items.account_id = '". $account_id."' ";
							}
							if( !empty( $item_type ) ){
								$sql_str .= "AND job_consumed_items.item_type = '". $item_type."' ";
							}

							if( !empty( $date_from ) ){
								$sql_str .= ' AND ( job.job_date >= "'.$date_from.'" )';
							}

							if( !empty( $date_to ) ){
								$sql_str .= ' AND ( job.job_date <= "'.$date_to.'" )';
							}

				$sql_str .= "ORDER BY stock_items.item_name ) ";
				$sql_str .= "UNION ALL ";
				$sql_str .= "( SELECT job_consumed_items.id, job_consumed_items.job_id, job_consumed_items.item_code, job_consumed_items.item_qty, job_consumed_items.price, job_consumed_items.price_adjusted, job_consumed_items.item_type, bom_items.item_name
							FROM job_consumed_items JOIN bom_items ON job_consumed_items.item_code = bom_items.item_code
							LEFT JOIN job ON job_consumed_items.job_id = job.job_id
							WHERE job_consumed_items.job_id IN (".$job_ids.") ";
							if( !empty( $account_id ) ){
								$sql_str .= "AND bom_items.account_id = '". $account_id."' ";
							}
							if( !empty( $item_type ) ){
								$sql_str .= "AND job_consumed_items.item_type = '". $item_type."' ";
							}

							if( !empty( $date_from ) ){
								$sql_str .= ' AND ( job.job_date >= "'.$date_from.'" )';
							}

							if( !empty( $date_to ) ){
								$sql_str .= ' AND ( job.job_date <= "'.$date_to.'" )';
							}
				$sql_str .= "ORDER BY bom_items.item_name ) ";
				$query = $this->db->query( $sql_str );

				if( $query->num_rows() > 0 ){
					if( $grouped ){
						$data = [];
						foreach( $result = $query->result() as $k => $row ){
							$group 			= ( in_array( $row->item_type, ['bom','boms'] ) ) ? 'boms' : 'stock';
							
							if( !empty( $row->job_id ) ){
								$this->db->select( "customer.business_name, customer.customer_first_name, customer.customer_last_name, customer.customer_email, customer.customer_type", false );
								$this->db->select( "job.job_id, job.job_date, job.job_duration", false );
								$this->db->select( "job_types.job_type", false );
								$this->db->select( "job_statuses.job_status", false );
								$this->db->select( "CONCAT( user.first_name, ' ', user.last_name ) `job_assignee`", false );
								$this->db->select( "address_types.address_type, customer_addresses.address_line1, customer_addresses.address_line2, customer_addresses.address_town, customer_addresses.address_postcode", false );
								
								$this->db->join( "job", "job.customer_id=customer.customer_id", "left" );
								$this->db->join( "job_types", "job_types.job_type_id=job.job_type_id", "left" );
								$this->db->join( "job_statuses", "job_statuses.status_id=job.status_id", "left" );
								$this->db->join( "user", "user.id=job.assigned_to", "left" );
								$this->db->join( "customer_addresses", "customer_addresses.customer_id=customer.customer_id", "left" );
								$this->db->join( "address_types", "address_types.address_type_id=customer_addresses.address_type_id", "left" );
								
								
								$this->db->where( "job.job_id", $row->job_id );
								$this->db->where( "job.account_id", $account_id );
								$this->db->where( "address_types.address_type_group", "main" );
								
								$query = $this->db->get( "customer" );

								$query_result = [
									"business_name" 		=> NULL,
									"customer_first_name" 	=> NULL,
									"customer_last_name" 	=> NULL,
									"customer_email" 		=> NULL,
									"customer_type" 		=> NULL,
									"job_date" 				=> NULL,
									"job_duration" 			=> NULL,
									"job_type" 				=> NULL,
									"job_status" 			=> NULL,
									"job_assignee" 			=> NULL,
									"address_type"			=> NULL,
									"address_line1"			=> NULL,
									"address_line2"			=> NULL,
									"address_town"			=> NULL,
									"address_postcode"		=> NULL,
								];
								
								if( $query->num_rows() > 0 ){
									$query_result = $query->result_array()[0];
								}
								$row = array_merge( (array) $row, $query_result );
							}
							$data[$group][] = $row;
						}
						$result = $data;
					} else {
						$result = $query->result();
					}
					$this->session->set_flashdata( 'message','Consumed items found' );
				} else {
					$this->session->set_flashdata( 'message','No data found' );
				}
			} else {
				$this->session->set_flashdata( 'message','No data found' );
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}


		$item_type = ( in_array( $item_type, ['bom','boms'] ) ) ? 'boms' : $item_type ;
		if( !empty( $result[$item_type] ) ){
			$report_name	= "Contract Consumed Items - ".( ucfirst( $item_type ) );
			$document_path 	= '_export_downloads/'.$account_id.'/';
			$upload_path   	= $this->app_root.$document_path;

			if( !is_dir( $upload_path ) ){
				if( !mkdir( $upload_path, 0755, true ) ){
					$this->session->set_flashdata( 'message', 'Error: Unable to create upload location' );
					return false;
				}
			}
			
			$result 		= object_to_array( $result[$item_type] );
			$headers 		= explode( ', ', ucwords( str_replace( '_', ' ', implode( ', ', array_keys( $result[0] ) ) ) ) );
			$data 			= array_to_csv( $result, $headers );
			$file_name 		= $report_name.' - '.date( 'dmYHi' ).'.csv';
			$file_path 		= $upload_path.$file_name;

			if( write_file( $upload_path.$file_name, $data ) ){

				// if( $req_source == 'web-client' ){
					// force_download( $report_name, file_get_contents( $file_path ) );
				// }else{
				$result = [
					'timestamp' 	=> date('d.m.Y H:i:s'),
					'expires_at' 	=> date('d.m.Y H:i:s', strtotime( '+1 hour' ) ),
					'file_name' 	=> $file_name,
					'file_path' 	=> $file_path,
					'file_link' 	=> base_url( $document_path.$file_name )
				];
				//}

			}
		}
		return $result;
	}


	/** Get Item Type **/
	private function _get_item_type( $item_type = false ){
		switch( strtolower( $item_type ) ){
			case 'bom':
			case 'boms':
			case 'sor':
			case 'sors':
				$item_type = 'bom';
				break;
			case 'stock':
			default:
				$item_type = 'stock';
				break;
		}
		return $item_type;
	}
	
	
	/** Generate Contract Ref **/
	private function _generate_contract_ref( $account_id = false, $data = false ){
		if( !empty( $account_id ) && !empty( $data ) ){
			$contract_ref = string_initials( $data['contract_name'] );
			$contract_ref .= ( !empty( $data['contract_type_id'] ) ) ? $data['contract_type_id'] : '';
			$contract_ref .= $account_id;
		} else {
			$contract_ref = $account_id.$this->ssid_common->generate_random_password();
		}
		return strtoupper( $contract_ref );
	}
	
	
	/*
	* Search through Contract Buildings list
	*/
	public function contract_buildings_lookup( $account_id = false, $contract_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;
		if( !empty( $account_id ) && !empty( $contract_id ) ){
			
			#Limit access by contract to External User Types
			if( in_array( $this->ion_auth->_current_user()->user_type_id, EXTERNAL_USER_TYPES ) ){
				$contract_access 	= $this->contract_service->get_linked_people( $account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1] );
				$allowed_contracts  = !empty( $contract_access ) ? array_column( $contract_access, 'contract_id' ) : [];
				if( !empty( $allowed_contracts ) ){
					$contract_access 	= $this->contract_service->get_linked_sites( $account_id, $allowed_contracts, [ 'ids_only'=>1, 'ignore_schedule_check'=> 1 ], -1 );
					$allowed_sites  = !empty( $contract_access ) ? $contract_access : [];
					$this->db->where_in( 'site.site_id', $allowed_sites );
				} else{
					$this->session->set_flashdata( 'message','No data found matching your criteria' );
					return false;
				}
			}
			
			$where 			= $raw_where = convert_to_array( $where );
			
			$contract_id 	= !empty( $contract_id ) ? $contract_id : ( !empty( $where['contract_id'] ) ? $where['contract_id'] : false );
		
			$this->db->select('site.*, sites_contracts.contract_id, site_statuses.status_name, site_event_statuses.event_tracking_status_id, site_event_statuses.event_tracking_status, site_event_statuses.status_group, site_event_statuses.hex_color, site_event_statuses.icon_class, addrs.main_address_id, addrs.addressline1 `address_line_1`, addrs.addressline2 `address_line_2`,addrs.postcode `postcode`, addrs.summaryline, addrs.xcoords `gps_latitude`,addrs.ycoords `gps_longitude`, audit_result_statuses.*',false)
				->join( 'site','site.site_id = sites_contracts.site_id','left' )
				->join( 'addresses addrs','addrs.main_address_id = site.site_address_id','left' )
				->join( 'site_statuses','site_statuses.status_id = site.status_id','left' )
				->join( 'site_event_statuses','site_event_statuses.event_tracking_status_id = site.event_tracking_status_id','left' )
				->join( 'audit_result_statuses','audit_result_statuses.audit_result_status_id = site.audit_result_status_id','left' )
				->where( 'site.account_id',$account_id )
				->where( 'sites_contracts.contract_id',$contract_id )
				// ->where( 'site.archived !=',1 )
				->where( "( site.archived != 1 or site.archived is NULL )" )
				->group_by( 'sites_contracts.site_id' );

			if( !empty( $search_term ) ){

				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {

					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){

						foreach( $this->contract_buildings_searchable_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						if( !empty($search_where['site.status_id']) ){
							$search_where['site_statuses.status_name'] =   trim( $term );
							unset($search_where['site.status_id']);
						}

						if( !empty($search_where['site.site_address_id']) ){
							$search_where['addrs.summaryline'] =   trim( $term );
							unset($search_where['site.site_address_id']);
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );

					}

				}else{

					foreach( $this->contract_buildings_searchable_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					if( !empty($search_where['site.status_id']) ){
						$search_where['site_statuses.status_name'] =  $search_term;
						unset($search_where['site.status_id']);
					}

					if( !empty($search_where['site.site_address_id']) ){
						$search_where['addrs.summaryline'] =  $search_term;
						unset($search_where['site.site_address_id']);
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );

				}

			}
	
			if( isset( $where['result_status_id'] ) ){
				if( !empty( $where['result_status_id'] ) ){
					$this->db->where( 'site.audit_result_status_id', $where['result_status_id'] );
				}
				unset( $where['result_status_id'] );
			}

			if( $order_by ){
				$order = $this->ssid_common->_clean_order_by( $order_by, 'site' );
				if( !empty( $order ) ){ $this->db->order_by( $order ); }
			}else{
				$this->db->order_by( 'site.site_reference, site.site_name' );
			}

			$query = $this->db->limit( $limit, $offset )
				->get( 'sites_contracts' );

			if( $query->num_rows() > 0 ){
				$result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
				$result->records 			= $query->result();
				$counters 					= $this->get_total_contract_buildings( $account_id, $contract_id, $search_term, $raw_where, $limit );
				$result->counters->total 	= ( !empty( $counters->total ) ) ? $counters->total : null;
				$result->counters->pages 	= ( !empty( $counters->pages ) ) ? $counters->pages : null;
				$result->counters->limit  	= ( int ) $limit;
				$result->counters->offset 	= ( int ) $offset;
				
				$this->session->set_flashdata('message','Records found.');
			}else{
				$this->session->set_flashdata('message','No records found matching your criteria.');
			}
		}

		return $result;
	}

	/*
	* Get total Conract Buildings count for the search
	*/
	public function get_total_contract_buildings( $account_id = false, $contract_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;
		if( !empty( $account_id ) && !empty( $contract_id ) ){
			
			#Limit access by contract to External User Types
			if( in_array( $this->ion_auth->_current_user()->user_type_id, EXTERNAL_USER_TYPES ) ){
				$contract_access 	= $this->contract_service->get_linked_people( $account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1] );
				$allowed_contracts  = !empty( $contract_access ) ? array_column( $contract_access, 'contract_id' ) : [];
				if( !empty( $allowed_contracts ) ){
					$contract_access 	= $this->contract_service->get_linked_sites( $account_id, $allowed_contracts, [ 'ids_only'=>1, 'ignore_schedule_check'=> 1 ], -1 );
					$allowed_sites  = !empty( $contract_access ) ? $contract_access : [];
					$this->db->where_in( 'site.site_id', $allowed_sites );
				} else{
					$this->session->set_flashdata( 'message','No data found matching your criteria' );
					return false;
				}
			}
			
			$where = convert_to_array( $where );
			
			$this->db->select( 'sites_contracts.site_id',false )
				->join( 'site','site.site_id = sites_contracts.site_id','left' )
				->join( 'addresses addrs','addrs.main_address_id = site.site_address_id','left' )
				->join( 'site_statuses','site_statuses.status_id = site.status_id','left' )
				->join( 'site_event_statuses','site_event_statuses.event_tracking_status_id = site.event_tracking_status_id','left' )
				->join( 'audit_result_statuses','audit_result_statuses.audit_result_status_id = site.audit_result_status_id','left' )
				->where( 'site.account_id',$account_id )
				->where( 'sites_contracts.contract_id',$contract_id )
				// ->where( 'site.archived !=',1 )
				->where( "( site.archived != 1 or site.archived is NULL )" )
				->group_by( 'sites_contracts.site_id' );

			if( !empty( $search_term ) ){

				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {

					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){

						foreach( $this->contract_buildings_searchable_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						if( !empty($search_where['site.status_id']) ){
							$search_where['site_statuses.status_name'] =   trim( $term );
							unset($search_where['site.status_id']);
						}

						if( !empty($search_where['site.site_address_id']) ){
							$search_where['addrs.summaryline'] =   trim( $term );
							unset($search_where['site.site_address_id']);
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );

					}

				} else {

					foreach( $this->contract_buildings_searchable_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					if( !empty($search_where['site.status_id']) ){
						$search_where['site_statuses.status_name'] =  $search_term;
						unset($search_where['site.status_id']);
					}

					if( !empty($search_where['site.site_address_id']) ){
						$search_where['addrs.summaryline'] =  $search_term;
						unset($search_where['site.site_address_id']);
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			//Check for a setting that specifies whether or not to only get monitored sites
			if( isset( $where['site_statuses'] ) ){
				$where['site_statuses'] = ( is_array( $where['site_statuses'] ) ) ? $where['site_statuses'] : ( is_string( $where['site_statuses'] ) ? str_to_array( $where['site_statuses'] ) : $where['site_statuses'] );
				if( !empty( $where['site_statuses'] ) ){
					$this->db->where_in( 'site.status_id', $where['site_statuses'] );
				}
				unset( $where['site_statuses'] );
			}

			if( isset( $where['result_status_id'] ) ){
				if( !empty( $where['result_status_id'] ) ){
					$this->db->where( 'site.audit_result_status_id', $where['result_status_id'] );
				}
				unset( $where['result_status_id'] );
			}

			if( $order_by ){
				$order = $this->ssid_common->_clean_order_by( $order_by, 'site' );
				if( !empty( $order ) ){ $this->db->order_by( $order ); }
			}else{
				$this->db->order_by( 'site.site_name' );
			}

			$query = $this->db->from( 'sites_contracts' )->count_all_results();
			$results['total'] = !empty( $query ) ? $query : 0;
			$results['pages'] = !empty( $query ) ? ceil( $query / $limit ) : 0;
			return json_decode( json_encode( $results ) );
		}
		return $result;
	}

	/** Get linked Buildings to exclude **/
	private function _linked_buildings_to_exclude( $account_id = false, $contract_id = false, $where = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $contract_id ) ){
			$where 			= convert_to_array( $where );
			
			$schedule_freqs = $this->db->select( 'frequency_id, frequency_name, frequency_group, activities_required' )
				->get_where( 'schedule_frequencies', [ 'schedule_frequencies.account_id'=> $account_id ] );
			
			$frequency_id 	= !empty( $where['frequency_id'] ) ? $where['frequency_id'] : false;
			
			$contract_ids = ( is_array( $contract_id ) ) ? $contract_id : [ $contract_id ];
			
			$this->db->select( 'schedule_site_tracker.site_id',false )
				->where( 'schedule_site_tracker.account_id',$account_id )
				->where_in( 'schedule_site_tracker.contract_id', $contract_id )
				->where( 'schedule_site_tracker.is_active', 1 );
			if( !empty( $frequency_id ) ){
				$this->db->where( 'schedule_site_tracker.frequency_id',$frequency_id );

				if( $schedule_freqs->num_rows() > 0 ){

					foreach( $schedule_freqs->result() as $k => $frequency ){
						if( $frequency->frequency_id == $frequency_id ){
							
							switch( $frequency->frequency_group ){
								case 'annually':

									$date_from	= date( 'Y-01-01' );
									$date_to	= date( 'Y-12-31' );
								
									break;
									
								case 'biannually':
								
									$first_day_of_half_year = $date_from  	= date( 'Y-06-01' );
									$last_day_of_half_year 	= $date_to		= date( 'Y-12-31' );
								
									break;
									
								case 'triannually':
								
									$date_from  = date( 'Y-04-01' );
									$date_to 	= date( 'Y-06-30' );
								
									break;
									
								case 'quarterly':
								
									$date_from 	= date( 'Y-01-01' );
									$date_to 	= date( 'Y-12-31' );
								
									break;
									
								case 'monthly':
								
									$date_from 	= date( 'Y-m-01' );
									$date_to 	= date( 'Y-m-t' );

									break;

								case 'weekly':
								
									$date_from 	= date( 'Y-m-d', strtotime( 'next Sunday -1 week', strtotime( 'this sunday' ) ) );
									$date_to 	= date( 'Y-m-d', strtotime( 'next Sunday -1 week + 6 days', strtotime( 'this sunday' ) ) );
								
									break;
									
								case 'daily':
								
									$date_from = $date_to = date( 'Y-m-d' );
								
									break;
								
							}
							
						}
					}
					
					if( !empty( $date_from ) && !empty( $date_to ) ){
						$this->db->where( 'date_created >=',$date_from );
						$this->db->where( 'date_created <=',$date_to );
					}
				}
				
			}
			
			$query = $this->db->group_by( 'site_id' )
				->get( 'schedule_site_tracker' );

			if( $query->num_rows() > 0 ){
				$result = array_column( $query->result_array(), 'site_id' );
			}
		}
		return $result;
	}
}