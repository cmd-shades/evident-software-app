<?php if (!defined('BASEPATH'))exit('No direct script access allowed');

class Customer_model extends CI_Model {

	function __construct(){
		parent::__construct();
		$this->load->model( 'serviceapp/Contract_model','contract_service' );
    }

	private $customer_search_fields	= [ 'business_name', 'customer_first_name', 'customer_last_name', 'customer_email', 'customer_work_telephone', 'customer_main_telephone', 'customer_mobile', 'address_postcode', 'addresses.postcode' ];

	/*
	* Get Customers single records or multiple records
	*/
	public function get_customers( $account_id = false, $customer_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){

		$result = false;
		
		#Limit access by contract to External User Types
		if( !$customer_id  ){
			if( in_array( $this->ion_auth->_current_user()->user_type_id, EXTERNAL_USER_TYPES ) ){
				$contract_access = $this->contract_service->get_linked_people( $account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1] );
				$allowed_access  = !empty( $contract_access ) ? array_column( $contract_access, 'contract_id' ) : [];
				if( !empty( $allowed_access ) ){
					$this->db->where_in( 'customer.contract_id', $allowed_access );
				} else{
					$this->session->set_flashdata( 'message','No data found matching your criteria' );
					return false;
				}
			}
		}
		
		$this->db->select( 'customer.*, GROUP_CONCAT( customer_addresses.address_postcode SEPARATOR " , " ) as `customer_postcodes`',false )
			->select( 'addresses.postcode `main_postcode`' )
			->join( 'customer_addresses', 'customer_addresses.customer_id = customer.customer_id', 'left' )
			->join( 'addresses', 'addresses.main_address_id = customer.address_id', 'left' )
			->where( 'customer.archived !=', 1 )
			->where( 'customer.account_id',$account_id );


		if( !empty( $account_id ) ){
			$this->db->where( 'customer.account_id', $account_id );
		}

		$where = $raw_where = convert_to_array( $where );

		$customer_id = !empty( $customer_id ) ? $customer_id : ( !empty( $where['customer_id'] ) ? $where['customer_id'] : false );

		if( !empty( $customer_id ) ){

			$row = $this->db->get_where( 'customer',['customer.customer_id'=>$customer_id] )->row();

			if( !empty( $row ) ){
				
				##Customer default address
				$row->default_address = null;
				if( !empty( $row->address_id ) ){
					$default_address = $this->db->select( 'addrs.main_address_id,addrs.addressline1 `address_line1`,addrs.addressline2 `address_line2`,addrs.addressline3 `address_line3`,addrs.posttown `address_town`,addrs.county `address_county`,addrs.postcode `address_postcode`,addrs.summaryline, CONCAT( addrs.addressline1,", ",addrs.addressline2,", ",addrs.posttown, ", ",addrs.posttown,", ",addrs.postcode ) `short_address`, addrs.organisation `address_business_name`,addrs.xcoords `gps_latitude`,addrs.ycoords `gps_longitude`', false )
						->get_where( 'addresses `addrs`', [ 'main_address_id'=>$row->address_id ] )
						->row();
					$row->default_address = ( !empty( $default_address ) ) ? $default_address : null;
				}
				
				## give more, additional data to the single profile
				$this->db->select( 'customer_addresses.*', false );
				$this->db->select( 'address_types.address_type_id, address_types.address_type, address_types.address_type_group', false );

				$this->db->join( 'address_types', 'address_types.address_type_id = customer_addresses.address_type_id', 'left' );
			
				$arch_where = '( customer_addresses.archived != 1 or customer_addresses.archived is NULL )';
				$this->db->where( $arch_where );

				$addresses = $this->db->get_where( 'customer_addresses', ['customer_addresses.account_id' => $account_id, 'customer_id' => $customer_id] )->result();

				if( !empty( $addresses ) ){
					$row->addresses = $addresses;
				} else {
					$row->addresses = null;
				}

				$this->session->set_flashdata( 'message','Customer record found' );
				$result = $row;
			}else{
				$this->session->set_flashdata( 'message','Customer record not found' );
			}
			return $result;

		}

		if( !empty( $search_term ) ){
			$search_term  = trim( urldecode( $search_term ) );
			$search_where = [];
			if( strpos( $search_term, ' ') !== false ) {
				$multiple_terms = explode( ' ', $search_term );
				foreach( $multiple_terms as $term ){
					foreach( $this->customer_search_fields as $k=>$field ){
						$search_where[$field] = trim( $term );
					}

					/* 	NOT IN USE (?) - no such  field on the list 'customer_addresses.address_postcode'
					if( !empty( $search_where['customer_addresses.address_postcode'] ) ){
						$search_where['customer_addresses.address_postcode'] =  trim( $term );
						unset($search_where['customer_addresses.address_postcode']);
					} */

					if( !empty( $search_where['postcode'] ) ){
						$search_where['addresses.postcode'] =  trim( $term );
						unset( $search_where['postcode'] );
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			} else {
				foreach( $this->customer_search_fields as $k=>$field ){
					$search_where[$field] = $search_term;
				}
				/* 	NOT IN USE (?) - no such  field on the list 'customer_addresses.address_postcode'
				if( !empty( $search_where['customer_addresses.address_postcode'] ) ){
					$search_where['customer_addresses.address_postcode'] =  trim( $search_term );
					unset($search_where['customer_addresses.address_postcode']);
				}
				*/
				
				if( !empty( $search_where['postcode'] ) ){
					$search_where['addresses.postcode'] =  trim( $search_term );
					unset( $search_where['postcode'] );
				}
				
				$where_combo = format_like_to_where( $search_where );
				$this->db->where( $where_combo );
			}
		}

		if( !empty( $order_by ) ){
			$this->db->order_by( $order_by );
		} else {
			$this->db->order_by( 'customer.customer_id DESC, customer.customer_first_name' );
		}

		if( $limit > 0 ){
			$this->db->limit( $limit, $offset );
		}
		
		$query = $this->db->group_by( 'customer.customer_id' )
			->get( 'customer' );

		if( $query->num_rows() > 0 ){

			$result_data = $query->result();

			$result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
			$result->records 			= $result_data;
			$counters 					= $this->get_customer_totals( $account_id, $search_term, $raw_where, $limit );
			$result->counters->total 	= ( !empty( $counters->total ) ) ? $counters->total : null;
			$result->counters->pages 	= ( !empty( $counters->pages ) ) ? $counters->pages : null;
			$result->counters->limit  	= ( !empty( $limit ) ) 	 ? $limit : $result->counters->total;
			$result->counters->offset 	= $offset;

			$this->session->set_flashdata( 'message','Customer(s) data found.' );
		}else{
			$this->session->set_flashdata( 'message','No records found matching your criteria.' );
		}

		return $result;
	}


	/** Get Customer lookup totals **/
	public function get_customer_totals( $account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT ){

		$result = false;

		#Limit access by contract to External User Types
		if( in_array( $this->ion_auth->_current_user()->user_type_id, EXTERNAL_USER_TYPES ) ){
			$contract_access = $this->contract_service->get_linked_people( $account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1] );
			$allowed_access  = !empty( $contract_access ) ? array_column( $contract_access, 'contract_id' ) : [];
			if( !empty( $allowed_access ) ){
				$this->db->where_in( 'customer.contract_id', $allowed_access );
			} else{
				$this->session->set_flashdata( 'message','No data found matching your criteria' );
				return false;
			}
		}

		$this->db->select( 'customer.customer_id',false )
			->join( 'customer_addresses', 'customer_addresses.customer_id = customer.customer_id', 'left' )
			->join( 'addresses', 'addresses.main_address_id = customer.address_id', 'left' )
			->where( 'customer.archived !=',1)
			->where( 'customer.account_id',$account_id );

		if( !empty( $account_id ) ){
			$this->db->where( 'customer.account_id', $account_id );
		}

		$where = $raw_where = convert_to_array( $where );

		$customer_id = !empty( $customer_id ) ? $customer_id : ( !empty( $where['customer_id'] ) ? $where['customer_id'] : false );

		if( !empty( $customer_id ) ){

			$row = $this->db->get_where( 'customer',['customer_id'=>$customer_id] )->row();

			if( !empty( $row ) ){
				$this->session->set_flashdata( 'message','Customer record found' );
				$result = $row;
			}else{
				$this->session->set_flashdata( 'message','Customer record not found' );
			}
			return $result;

		}

		if( !empty( $search_term ) ){
			$search_term  = trim( urldecode( $search_term ) );
			$search_where = [];
			if( strpos( $search_term, ' ') !== false ) {
				$multiple_terms = explode( ' ', $search_term );
				foreach( $multiple_terms as $term ){
					foreach( $this->customer_search_fields as $k=>$field ){
						$search_where[$field] = trim( $term );
					}

					/* 	NOT IN USE (?) - no such  field on the list 'customer_addresses.address_postcode'
					if( !empty( $search_where['customer_addresses.address_postcode'] ) ){
						$search_where['customer_addresses.address_postcode'] =  trim( $term );
						unset( $search_where['customer_addresses.address_postcode'] );
					} */
					
					if( !empty( $search_where['postcode'] ) ){
						$search_where['addresses.postcode'] =  trim( $term );
						unset( $search_where['postcode'] );
					}
					
					

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			} else {
				foreach( $this->customer_search_fields as $k=>$field ){
					$search_where[$field] = $search_term;
				}
				
				/* 	NOT IN USE (?)
				if( !empty( $search_where['customer_addresses.address_postcode'] ) ){
					$search_where['customer_addresses.address_postcode'] =  trim( $search_term );
					unset( $search_where['customer_addresses.address_postcode'] );
				}
				*/
				
				if( !empty( $search_where['postcode'] ) ){
					$search_where['addresses.postcode'] =  trim( $term );
					unset( $search_where['postcode'] );
				}
				
				$where_combo = format_like_to_where( $search_where );
				$this->db->where( $where_combo );
			}
		}

		$query = $this->db->group_by( 'customer.customer_id' )
			->from( 'customer' )->count_all_results();

		$results['total'] = !empty( $query ) ? $query : 0;
		$limit 			  = ( !empty( $limit > 0 ) ) ? $limit : $results['total'];
		$results['pages'] = !empty( $query ) ? ceil( $query / $limit ) : 0;
		return json_decode( json_encode( $results ) );
	}

	/*
	*	Create new Customer
	*/
	public function create_customer( $account_id = false, $customer_data = false ){
		$result = false;
		if( !empty( $account_id  ) && !empty( $customer_data ) ){
			
			$customer_address = false;
			
			if( !empty( $customer_data['customer_address'] ) ){
				$customer_address 		 				= convert_to_array( $customer_data['customer_address'] );
				$data['address_id'] 	 				= ( !empty( $customer_data['address_id'] ) ? $customer_data['address_id'] : ( !empty( $customer_address['address_id'] ) ? $customer_address['address_id'] : null ) );
				$customer_address['main_address_id'] 	= $customer_data['address_id'];
				unset( $customer_data['customer_address'], $customer_address['address_id'] );
			}

			$data = $this->ssid_common->_data_prepare( $customer_data );

			if( !empty( $data ) ){
				$create_data = $this->ssid_common->_filter_data( 'customer', $data );
				$this->db->insert( 'customer',$create_data );
				if( $this->db->trans_status() !== FALSE ){
					$data['customer_id'] = $this->db->insert_id();
					
					## customer created - save the address
					if( !empty( $customer_address ) ){
						$address = $this->create_contact( $account_id, $data['customer_id'], $customer_address );
					}
					$result = $this->get_customers( $account_id, $data['customer_id'] );
					$this->session->set_flashdata( 'message','Customer record created successfully.' );
				}
			}
		} else {
			$this->session->set_flashdata( 'message','No Customer data supplied.' );
		}
		return $result;
	}


	/*
	* 	Update Customer record
	*/
	public function update_customer( $account_id = false, $customer_id = false, $customer_data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $customer_id ) && !empty( $customer_data ) ){
			$check_customer = $this->db->get_where( 'customer',['account_id'=>$account_id, 'customer_id'=>$customer_id] )->row();
			if( !empty( $check_customer ) ){
				$data = $this->ssid_common->_data_prepare( $customer_data );

				if( !empty( $data ) ){

					$update_data = $this->ssid_common->_filter_data( 'customer', $data );

					$this->db->where( 'customer_id', $customer_id )
						->where( 'account_id', $account_id)
						->update( 'customer', $update_data);
					if( $this->db->trans_status() !== FALSE ){
						$result = $result = $this->get_customers( $account_id, $customer_id );
						$this->session->set_flashdata( 'message','Customer record updated successfully.' );
					}
				}
			} else {
				$this->session->set_flashdata( 'message','Foreign customer record. Access denied.');
			}
		} else {
			$this->session->set_flashdata( 'message','No Customer data supplied.' );
		}
		return $result;
	}

	/*
	* 	Delete Customer record
	*/
	public function delete_customer( $account_id = false, $customer_id = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $customer_id ) ){
			$check_customer = $this->db->get_where('customer',['account_id'=>$account_id, 'customer_id'=>$customer_id])->row();
			if( !empty( $check_customer ) ){
				$this->db->where( 'account_id',$account_id )
					->where( 'customer_id',$customer_id )
					->update( 'customer',['archived'=>1] );
				if( $this->db->trans_status() !== false ){

					//Delete associated addresses as well
					$this->db->where( 'customer_id',$customer_id )->delete('customer_addresses' );
					$result = true;
					$this->session->set_flashdata( 'message','Customer record deleted successfully.' );
				}
			}else{
				$this->session->set_flashdata( 'message','Foreign customer record. Access denied.' );
			}
		}
		return $result;
	}


	/**
	*	Create a customer contact record
	**/
	public function create_contact( $account_id = false, $customer_id = false, $contact_data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $customer_id )  && !empty( $contact_data ) ){
			$data = [];
			
			foreach( $contact_data as $key => $value ){
				$data[$key] = ( is_string( $value ) ) ? trim( $value ) : $value;
			}
			
			if( !empty( $data ) ){
				$data['main_address_id'] 		= !empty( $data['address_id'] ) ? $data['address_id'] : ( !empty( $data['main_address_id'] ) ? $data['main_address_id'] : null );
				$data['account_id']				= !empty( $data['account_id'] ) ? $data['account_id'] : $account_id;
				$data['customer_id']			= !empty( $data['customer_id'] ) ? $data['customer_id'] : $customer_id;
				$data['created_by'] 			= $this->ion_auth->_current_user->id;
				$new_contact 					= $this->ssid_common->_filter_data( 'customer_addresses', $data );
				$this->db->insert( 'customer_addresses', $new_contact );
				
				if( $this->db->affected_rows() > 0 ){
					$contact_id = $this->db->insert_id();
					$result 	= $this->db->get_where( "customer_addresses", ["account_id" => $account_id, "customer_address_id" => $contact_id]  )->row();
					$this->set_customer_default_address( $account_id, $customer_id, $data );
					$this->session->set_flashdata( 'message','Address Contact added successfully' );
				}
			} else {
				$this->session->set_flashdata('message','An error occurred while adding an address contact!');
			}
		} else {
			$this->session->set_flashdata('message','Required parameters not supplied!');
		}
		return $result;
	}


	/**
	*	Get all customer addresses
	**/
	public function get_address_contacts( $account_id=false, $customer_id=false, $customer_address_id=false, $address_type_id=false, $limit = DEFAULT_LIMIT, $offset = 0 ){
		$result = null;
		if( !empty( $account_id ) ){
			$arch_where = "( customer_addresses.archived != 1 or customer_addresses.archived is NULL )";

			$this->db->select('customer_addresses.*, address_types.address_type, concat(user.first_name," ",user.last_name) `created_by`, concat(modifier.first_name," ",modifier.last_name) `modified_by`', false)
				->where('customer_addresses.account_id', $account_id)
				->where( $arch_where )
				->join('user','user.id = customer_addresses.created_by','left')
				->join('user modifier','modifier.id = customer_addresses.last_modified_by','left')
				->join('address_types','address_types.address_type_id = customer_addresses.address_type_id','left');

				if( $customer_address_id ){
					$row = $this->db->get_where( 'customer_addresses', ['customer_address_id' => $customer_address_id])->row();
					if( !empty($row) ){
						$this->session->set_flashdata('message','Contact details record found');
						$result = $row;
					}else{
						$this->session->set_flashdata('message','Contact details not found');
					}
					return $result;
				}

				if( $customer_id ){
					$this->db->where('customer_addresses.customer_id', $customer_id);
				}

				$query = $this->db->limit( $limit, $offset )
					->order_by('customer_addresses.address_contact_first_name')
					->get('customer_addresses');
			if( $query->num_rows() > 0 ){
				$this->session->set_flashdata('message','Addresses data found');
				$result = $query->result();
			}else{
				$this->session->set_flashdata('message','Addresses data not found');
			}
		}else{
			$this->session->set_flashdata('message','No parameters supplied for request');
		}
		return $result;
	}


	/** 
	* Check and set customer default address (if not set already)
	*/
	public function set_customer_default_address( $account_id = false, $customer_id = false, $address_data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $customer_id ) && !empty( $address_data ) ){
			if( !empty( $address_data['address_type_id'] ) ){
				$address_id 	= !empty( $address_data['address_id'] ) ? $address_data['address_id'] : ( !empty( $address_data['main_address_id'] ) ? $address_data['main_address_id'] : '' );
				#$address_type  	= $this->db->get_where( 'address_types', [ 'account_id'=>$account_id, 'address_type_id'=>$address_data['address_type_id'] ] )->row();
				$customer_data 	= $this->db->select( 'customer_id, address_id' )->get_where( 'customer', [ 'account_id'=>$account_id, 'customer_id'=>$customer_id ] )->row();
				if( !empty( $address_id ) && !empty( $customer_data ) && empty( trim( $customer_data->address_id ) ) ){
					$this->db->where( 'customer_id', $customer_id )
						->update( 'customer', ['address_id'=>$address_id] );
					$result = true;
				}
			}
		}
		return $result;
	}
	

	/*
	*	Update Contact Address
	*/
	public function update_address( $account_id = false, $customer_address_id = false, $contact_data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $customer_address_id ) && !empty( $contact_data ) ){
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

				$data = $this->ssid_common->_filter_data( 'customer_addresses', $data );
				$restricted_columns = ['created_by', 'created_date', 'archived'];
				foreach( $data as $key => $value ){
					if( in_array( $key, $restricted_columns ) ){
						unset( $data[$key] );
					}
				}

				$this->db->where( 'customer_address_id', $customer_address_id )->update( 'customer_addresses', $data );

				if( $this->db->affected_rows() > 0 ){

					$result = $this->get_address_contacts( $account_id, false, $customer_address_id );

					$this->session->set_flashdata( 'message','The Address has been updated successfully.' );

				} else {
					$this->session->set_flashdata( 'message','The Address hasn\'t been changed.' );
				}

			}
		} else {
			$this->session->set_flashdata( 'message','No Account ID, no Address Id or no new data supplied.' );
		}
		return $result;
	}


	/*
	*	Delete Address Contact
	*/
	public function delete_address( $account_id = false, $customer_address_id = false ){

		$result = false;

		if( !empty( $account_id ) && !empty( $customer_address_id ) ){

			$data = [
				'archived'			=> 1 ,
				'active'			=> NULL ,
				'last_modified_by'	=> $this->ion_auth->_current_user()->id
			];

			$query = $this->db->update( 'customer_addresses', $data, ["account_id" => $account_id, "customer_address_id" => $customer_address_id] );

			if( $this->db->affected_rows() > 0 ){
				$this->session->set_flashdata( 'message','The Address has been deleted.' );
				$result = true;
			} else {
				$this->session->set_flashdata( 'message','The Address hasn\'t been deleted.' );
			}
		} else {
			$this->session->set_flashdata( 'message','Invalid Address ID or missing Account ID.' );
		}
		return $result;
	}


	/*
	*	Function to create a note for the customer
	*/
	public function create_note( $account_id = false, $customer_id = false, $post_data = false ){
		$result = $note_id = false;

		if( ( !empty( $account_id ) ) && ( !empty( $customer_id ) ) && ( !empty( $post_data ) ) ){

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

			$current_customer = $this->get_customers( $account_id, $customer_id );
			if( !empty( $current_customer ) ){

				$data['account_id']		= $account_id;
				$data['customer_id']	= $customer_id;
				$data['created_by'] 	= $this->ion_auth->_current_user()->id;

				$data = $this->ssid_common->_filter_data( 'customer_notes', $data );
				$this->db->insert( "customer_notes", $data );

				if( $this->db->affected_rows() > 0 ){
					$note_id = $this->db->insert_id();

					$result = $this->db->get_where( "customer_notes", [ "note_id" => $note_id ] )->row();
					$this->session->set_flashdata( 'message', 'The Customer Note has been created successfully.' );
				} else {
					$this->session->set_flashdata( 'message', 'The Customer Note hasn\t been created.' );
				}
			} else {
				$this->session->set_flashdata( 'message','Invalid Customer ID.' );
			}

		} else {
			$this->session->set_flashdata( 'message','No Valid Data provided - missing Account ID, Customer ID or Post Data.' );
		}
		return $result;
	}


	/*
	*	To retrieve the customer note(s)
	*/
	public function get_notes( $account_id = false, $customer_id = false, $note_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;
		if( !empty( $account_id ) ){

			$this->db->select( "cn.*", false );
			$this->db->select( "c.business_name", false );

			$this->db->select( "concat( u1.first_name,' ',u1.last_name ) as `created_by_full_name`", false );
			$this->db->select( "concat( u2.first_name,' ',u2.last_name ) as `driver_full_name`", false );

			if( !empty( $note_id ) ){
				$this->db->where_in( "cn.note_id", $note_id );
			}

			$where = convert_to_array( $where );

			if( isset( $where['customer_id'] ) || !empty( $customer_id ) ){
				
				$customer_id = !empty( $where['customer_id'] ) ? $where['customer_id'] : $customer_id;

				if( !empty( $customer_id ) ){
					$this->db->where( "cn.customer_id", $customer_id );
					unset( $where['customer_id'] );
				}
			}

			if( !empty( $where ) ){
				$this->db->where( $where );
			}

			$this->db->join( "user u1", "u1.id = cn.created_by", "left" );
			$this->db->join( "user u2", "u2.id = cn.modified_by", "left" );
			$this->db->join( "customer c", "c.customer_id = cn.customer_id", "left" );

			$arch_where = "( cn.archive != 1 or cn.archive is NULL )";
			$this->db->where( $arch_where );

			$this->db->where( "cn.account_id", $account_id );

			$this->db->order_by( "cn.note_id DESC" );

			$query = $this->db->get( "customer_notes `cn`", $limit, $offset );

			if( !empty( $query->num_rows() ) && ( $query->num_rows() > 0 ) ){
				$dataset = $query->result();

				if( !empty( $note_id ) ){
					$result = $dataset[0];
				} else {
					foreach( $dataset as $row ){
						$result[$row->note_id] = $row;
					}
				}
				$this->session->set_flashdata( 'message','Customer Note(s) data found.' );

			} else {
				$this->session->set_flashdata( 'message','Customer Note(s) data not found.' );
			}

		} else {
			$this->session->set_flashdata( 'message','No Account ID supplied.' );
		}

		return $result;
	}
}