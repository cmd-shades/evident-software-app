<?php if (!defined('BASEPATH'))exit('No direct script access allowed');

class Quote_model extends CI_Model {

	function __construct(){
		parent::__construct();
		$this->load->model('Billable_item_model','billable_item_service');
    }

	/*
	* Get Quotes single records or multiple records
	*/
	public function get_quotes( $account_id = false, $quote_id = false, $customer_id=false, $offset=0, $limit=20 ){
		$result = false;
		$this->db->select( 'quote.*',false );
		$this->db->select( 'c.business_name, c.customer_first_name, c.customer_last_name, c.customer_email', false );

		$this->db->join( "customer `c`", "c.customer_id = quote.customer_id", "left" );

		$this->db->where( 'quote.archived != ', 1 );
		$this->db->where( 'quote.account_id', $account_id );
		if( $quote_id ){
			$row = $this->db->get_where( 'quote',['quote_id'=>$quote_id] )->row();
			if( !empty( $row ) ){
				$this->session->set_flashdata( 'message','Quote found' );
				$quote_items 		= $this->get_quotes_items($quote_id);
				$row->quote_items 	= ( !empty($quote_items) ) ? $quote_items : false;
				$result 			= $row;
			}else{
				$this->session->set_flashdata('message','Quote not found');
			}
			return $result;
		}

		if( $customer_id ){
			$this->db->where( 'customer_id',$customer_id );
		}

		$quote = $this->db->order_by( 'quote_id' )
			->offset( $offset )
			->limit( $limit )
			->get( 'quote' );

		if( $quote->num_rows() > 0 ){
			$this->session->set_flashdata( 'message','Quote records found' );
			#$quotes_data = new stdClass();
			$quotes_data = [];
			foreach( $quote->result() as $key=>$row ){
				$quote_items 		= $this->get_quotes_items( $row->quote_id );
				$row->quote_items 	= ( !empty($quote_items) ) ? $quote_items : false;
				#$quotes_data->{$key} 	= $row;
				$quotes_data[] 		= $row;
			}
			$result = $quotes_data;
		}else{
			$this->session->set_flashdata('message','Quote record(s) not found');
		}
		return $result;
	}

	/*
	* Create new Quote
	*/
	public function create_quote( $account_id = false, $quote_data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $quote_data ) ){

			$data = [];
			if( isset( $quote_data['quote_items'] ) && !empty( $quote_data['quote_items'] ) ){
				$quote_items = $quote_data['quote_items'];
				unset( $quote_data['quote_items'] );
			}
			
			foreach( $quote_data as $key=>$value ){
				$data[trim( $key )] = trim($value);
			}

			if( !empty( $data ) ){
				$data['created_by'] = $this->ion_auth->_current_user()->id;

				$data = $this->ssid_common->_filter_data( 'quote', $data );
				
				$this->db->insert( 'quote',$data );
				if( $this->db->trans_status() !== FALSE ){
					$data['quote_id'] = $this->db->insert_id();

					if( !empty( $data['quote_id'] ) && ( isset( $quote_items ) && !empty( $quote_items ) ) ){

						foreach( $quote_items as $item_code=>$qty ){
							$insert_quote_item = [
								'quote_id'	=> $data['quote_id'],
								'item_code'	=> $item_code,
								'item_qty'	=> (int)$qty
							];

							$this->create_quote_item( $data['quote_id'], $insert_quote_item );
						}
					}
					$result = $this->get_quotes( $account_id, $data['quote_id']);
					
					$this->session->set_flashdata('message','Quote record created successfully.');
				}
			}
		}else{
			$this->session->set_flashdata('message','No Quote data supplied.');
		}

		return $result;
	}

	/*
	* Update Quote record
	*/
	public function update_quote( $account_id = false, $quote_id = false, $quote_data = false ){
		$result = false;
		if( !empty($account_id) && !empty($quote_id) && !empty($quote_data) ){
			$data = [];
			if( isset($quote_data['quote_items']) && !empty($quote_data['quote_items']) ){
				$quote_items = $quote_data['quote_items'];
				unset($quote_data['quote_items']);
			}
			foreach( $quote_data as $key=>$value ){
				$data[$key] = trim($value);
			}

			if( !empty($data) ){
				$data['last_modified'] 	 		= date('Y-m-d H:i:s');
				$this->db->where('quote_id',$quote_id)->update('quote',$data);
				if( $this->db->trans_status() !== FALSE ){
					if( isset( $quote_items ) && !empty( $quote_items ) ){
						foreach( $quote_items as $item_code => $qty ){
							$update_quote_item = [
								'quote_id'=>$data['quote_id'],
								'item_code'=>$item_code,
								'item_qty'=>(int)$qty
							];
							$this->create_quote_item( $data['quote_id'], $update_quote_item );
						}
					}

					$result = $this->get_quotes( $account_id, $quote_id);
					$this->session->set_flashdata('message','Quote record updated successfully.');
				}else{
					$this->session->set_flashdata('message','There was an Error while trying to update the Quote record.');
				}
			}
		}else{
			$this->session->set_flashdata('message','No Quote data supplied.');
		}
		return $result;
	}

	/*
	* 	Delete Quote record
	*/
	public function delete_quote( $account_id = false, $quote_id = false ){
		$result = false;
		if( $quote_id ){
			$quote_exists = $this->db->get_where('quote',['account_id'=>$account_id, 'quote_id'=>$quote_id])->row();
			if( !empty($quote_exists) ){
				$data = ['archived'=>1];
				$this->db->where('quote_id',$quote_id)->update('quote',$data);
				if( $this->db->trans_status() !== FALSE ){
					$this->session->set_flashdata('message','Record deleted successfully.');
					$result = true;
					$this->db->where('quote_id',$quote_id)->update('quote_items',$data);
				}
			}else{
				$this->session->set_flashdata('message','Invalid Quote ID.');
			}
		}else{
			$this->session->set_flashdata('message','No Quote ID supplied.');
		}
		return $result;
	}

	/*
	* Create quote item(s)
	*/
	public function create_quote_item( $quote_id = false, $quote_item_data = false ){
		$result = false;
		
		if( !empty( $quote_id ) && !empty( $quote_item_data ) ){

			$billable_item 		= $this->billable_item_service->get_billable_items( false, $quote_item_data['item_code'] );
			$temp_quote_item 	= $this->billable_item_service->get_temp_quote_items( false, $quote_item_data['item_code'] );
			

			if( empty( $billable_item ) && ( !empty( $temp_quote_item ) ) ){
				$billable_item = $temp_quote_item;
			}

			if( !empty( $billable_item ) ){
				$quote_item_data['item_name'] 		= $billable_item->item_name;
				$quote_item_data['original_price'] 	= $billable_item->sell_price;
				$quote_item_data['quoted_price']	= $billable_item->sell_price;
				$where = array('quote_id'=>$quote_item_data['quote_id'],'item_code'=>$quote_item_data['item_code']);
				$check_exists = $this->db->get_where('quote_items',$where)->row();
				
				if( !empty($check_exists) ){

					if( !$quote_item_data['item_qty'] ){
						$this->db->where($where);
						$this->db->delete('quote_items');
						$result = true;
					}else{
						$this->db->where( $where );
						$this->db->update('quote_items',$quote_item_data);
						if( $this->db->trans_status() !== FALSE ){
							$this->session->set_flashdata('message','Quote item updated successfully.');
							$result = true;
						}
					}
				}else{
					if( $quote_item_data['item_qty'] > 0 ){
						$quote_item_data['created_by'] = $this->ion_auth->_current_user()->id;
						$this->db->insert('quote_items',$quote_item_data);
						if( $this->db->trans_status() !== FALSE ){
							$this->session->set_flashdata('message','Quote item created successfully.');
							$result = true;
						}
					}
				}
			} else {
				$this->session->set_flashdata('message','Billable Item not found.');
			}
		}
		return $result;
	}

	/*
	* Get list of all items contained in a quote
	*/
	public function get_quotes_items( $quote_id = false, $item_code = false, $quick_view = false , $offset = 0, $limit = 100 ){
		$result = false;

		if( $quick_view ){
			$this->db->select( 'quote_id, item_code, item_name, item_qty', false );
			$this->db->select( 'item_qty * quoted_price `quoted_value`', false );
		} else {
			$this->db->select( '*', false );
		}

		if( $quote_id ){
			$this->db->where('quote_id',$quote_id);
		}

		$this->db->select( 'quoted_price * item_qty `quoted_value`',false );

		if( $item_code ){
			$this->db->where('item_code',$item_code);
		}

		$quote_items = $this->db->order_by('item_code')
			->offset( $offset )
			->limit( $limit )
			->get('quote_items');

		if( $quote_items->num_rows() > 0 ){
			$this->session->set_flashdata('message','Quote items found');
			$result = $quote_items->result();
		}else{
			$this->session->set_flashdata('message','Quote items not found');
		}
		return $result;
	}





	/*
	* 	Get Quote Temporary Items
	*/
	public function get_temp_items( $account_id = false, $item_id = false, $offset = 0, $limit = 100 ){
		$result = false;

		$this->db->select( '*', false );

		if( $item_id ){
			$this->db->where( 'item_id',$item_id );
		}

		$this->db->limit( $limit );
		$this->db->offset( $offset );
		$this->db->order_by( 'created_date DESC, item_name ASC' );

		$query = $this->db->get( 'temp_quote_items' );

		if( ( !empty( $query->num_rows() ) ) && ( $query->num_rows() > 0 ) ){
			$this->session->set_flashdata('message','Quote items found');
			$result  = $query->result();
		} else {
			$this->session->set_flashdata('message','Quote items not found');
		}

		return $result;
	}


	/*
	*	Create a New Temporary Item needed to the Quote
	*/
	public function create_temp_item( $account_id = false, $post_data = false ){

		$result = false;
		if( !empty( $account_id ) && !empty( $post_data ) ){

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

			$date_part = date( 'ymd-Hi' );
			$data['item_code']	= 'T'.$account_id.$date_part;
			$check = $this->db->get_where( "temp_quote_items", array( 'item_code' => $data['item_code'] ) )->row();

			if( !empty( $check ) ){
				$this->session->set_flashdata( 'message', 'This item already exists.' );
			} else {
				$data = $this->ssid_common->_filter_data( 'temp_quote_items', $data );
				$this->db->insert( 'temp_quote_items', $data );

				if( ( $this->db->trans_status() !== FALSE ) && ( $this->db->affected_rows() > 0 ) ){
					$data['item_id'] = $this->db->insert_id();
					$this->session->set_flashdata( 'message', 'The Custom Item has been created successfully.' );
					$result = $data;
				} else {
					$this->session->set_flashdata( 'message', 'The Custom Item hasn\'t been created.' );
				}
			}

		} else {
			$this->session->set_flashdata( 'message', 'No Account or data supplied.' );
		}

		return $result;
	}


	/*
	* 	Delete Custom Quote Item
	*/
	public function delete_temp_item( $account_id = false, $temp_item_id = false, $delete_all = false ){
		$result = false;
		if( !empty( $account_id ) ){
			if ( !empty( $temp_item_id ) ){
				$item_exists = $this->db->get_where( 'temp_quote_items', ['account_id' => $account_id, 'item_id' => $temp_item_id] )->row();
				if( !empty( $item_exists ) ){
					$this->db->where( ['account_id' => $account_id, "item_id" => $temp_item_id] )->delete( 'temp_quote_items' );

					if( $this->db->trans_status() !== FALSE ){
						$this->session->set_flashdata( 'message','Custom Quote item deleted successfully.' );
						$result = true;
					} else {
						$this->session->set_flashdata( 'message', 'Couldn\'t delete the custom item' );
					}
				} else {
					$this->session->set_flashdata( 'message','Invalid Custom Item ID.' );
				}
			} else if( !empty( $delete_all ) && ( strtolower( $delete_all ) == 'yes' ) ){
				$this->db->where( 'account_id', $account_id )->delete( 'temp_quote_items' );

				if( $this->db->trans_status() !== FALSE ){
					$this->session->set_flashdata( 'message', 'All Custom Quote items deleted successfully.' );
					$result = true;
				} else {
					$this->session->set_flashdata( 'message', 'Couldn\'t delete all custom items' );
				}
			} else {
				$this->session->set_flashdata( 'message','No Custom Item ID or Delete All confirmation supplied.' );
			}
		} else {
			$this->session->set_flashdata( 'message', 'No Account ID supplied.' );
		}
		return $result;
	}


	/*
	* 	Get quick stats
	*/
	public function get_quick_stats( $account_id = false, $where = false, $offset = 0, $limit = 100 ){
		$result = false;

		if( !empty( $account_id ) ){

			if( !empty( $where ) ){
				if( is_object( $where ) ){
					$where = get_object_vars( $where );
				}
				$this->db->where( $where );
			}

			$this->db->select( "quote_status, count( quote_id ) `quote_number`", false );
			$this->db->group_by( "quote_status" );
			$query = $this->db->get( "quote" );

			if( $query->num_rows() > 0 ){
				foreach( $query->result() as $key => $row ){
					$result[$row->quote_status] = $row->quote_number;
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
	* 	Get list of quote statuses
	*/
	public function get_quote_statuses( $account_id = false ){
		$result = false;

		if( !empty( $account_id ) ){
			$select = "SELECT * FROM quote_statuses WHERE account_id=$account_id
						UNION ALL SELECT * FROM quote_statuses WHERE (account_id IS NULL or account_id = 0  )
					AND NOT EXISTS
						( SELECT 1 FROM quote_statuses WHERE account_id = $account_id )";
			$query = $this->db->query( $select );

			if( $query->num_rows() > 0 ){
				$result = $query->result();
				$this->session->set_flashdata( 'message','Statuses found' );
			} else {
				$this->session->set_flashdata( 'message','Statuses not found' );
			}
		} else {
			$this->session->set_flashdata( 'message','No Account ID Provided' );
		}

		return $result;
	}
}