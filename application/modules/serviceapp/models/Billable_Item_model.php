<?php

namespace Application\Modules\Service\Models;

class Billable_item_model extends CI_Model {

	function __construct(){
		parent::__construct();
    }

	/*
	* Get Billable_items single records or multiple records
	*/
	public function get_billable_items( $item_id = false,  $item_code=false, $item_category = false, $search_term = false, $offset=0, $limit=100, $order_by = false ){
		$result = false;
		if($item_id){
			$row = $this->db->get_where('billable_items',['item_id'=>$item_id])->row();
			if( !empty($row) ){
				$this->session->set_flashdata('message','Item found');
				$result = $row;
			}else{
				$this->session->set_flashdata('message','Item not found');
			}
			return $result;
		}

		if($item_code){
			$row = $this->db->get_where('billable_items',['item_code'=>$item_code])->row();
			if( !empty($row) ){
				$this->session->set_flashdata('message','Item found');
				$result = $row;
			}else{
				$this->session->set_flashdata('message','Item not found');
			}
			return $result;
		}

		if($item_category){
			$this->db->where('item_category',$item_category);
		}

		if($search_term){
			$this->db->or_like('item_category',$search_term);
			$this->db->or_like('item_type',$search_term);
			$this->db->or_like('item_name',$search_term);
			$this->db->or_like('item_code',$search_term);
			$this->db->or_like('item_supplier',$search_term);
		}

		if( !empty( $order_by ) ){
			$this->db->order_by( $order_by );
		} else {
			$this->db->order_by( 'item_category, item_name, item_code' );
		}

		$billable_items = $this->db->offset( $offset )
			->limit( $limit )
			->get( 'billable_items' );

		if( $billable_items->num_rows() > 0 ){
			$this->session->set_flashdata('message','Item records found');
			$result = $billable_items->result();
		}else{
			$this->session->set_flashdata('message','Item record(s) not found');
		}
		return $result;
	}

	/*
	* Create new Billable_item
	*/
	public function create_billable_item( $billable_item_data = false ){
		$result = false;
		if( !empty($billable_item_data) ){
			$data = [];
			foreach( $billable_item_data as $key=>$value ){
				$data[$key] = trim($value);
			}

			if( !empty($data) ){

				$item_exists = $this->db->get_where('billable_items',['item_code'=>$data['item_code']])->row();

				if( !$item_exists ){
					$this->db->insert('billable_items',$data);
					if( $this->db->trans_status() !== FALSE ){
						$data['item_id'] = $this->db->insert_id();
						$result = $this->get_billable_items($data['item_id']);
						$this->session->set_flashdata('message','Item record created successfully.');
					}
				}else{
					$result = false;
					$this->session->set_flashdata('message','An Item record with this Item Code already exists.');
				}
			}
		}else{
			$this->session->set_flashdata('message','No Item data supplied.');
		}
		return $result;
	}

	/*
	* Update Billable_item record
	*/
	public function update_billable_item( $item_id = false, $billable_item_data = false ){
		$result = false;
		if( !empty($item_id) && !empty($billable_item_data) ){
			$data = [];
			foreach( $billable_item_data as $key=>$value ){
				$data[$key] = trim($value);;
			}

			if( !empty($data) ){

				$this->db->where('item_id !=',$item_id);
				$item_exists = $this->db->get_where('billable_items',['item_code'=>$data['item_code']])->row();

				if( !$item_exists ){
					$data['last_modified'] 	 = date('Y-m-d H:i:s');
					$this->db->where('item_id',$item_id)->update('billable_items',$data);
					if( $this->db->trans_status() !== FALSE ){
						$this->session->set_flashdata('message','Item record updated successfully.');
						$result = $data;
					}else{
						$this->session->set_flashdata('message','There was an Error while trying to upate the Item record.');
					}
				}else{
					$this->session->set_flashdata('message','An Item record with the same Item Code already exists.');
				}
			}
		}else{
			$this->session->set_flashdata('message','No Item data supplied.');
		}
		return $result;
	}

	/*
	* Delete Billable_item record
	*/
	public function delete_billable_item( $item_id = false ){
		$result = false;
		if( $item_id ){
			$data = ['archived'=>1];
			$this->db->where('item_id',$item_id)
				->update('billable_items',$data);
			if( $this->db->trans_status() !== FALSE ){
				$this->session->set_flashdata('message','Record deleted successfully.');
				$result = true;
			}
		}else{
			$this->session->set_flashdata('message','No Item ID supplied.');
		}
		return $result;
	}


	/*
	* Get Temp items single records or multiple records
	*/
	public function get_temp_quote_items( $item_id = false, $item_code=false, $item_category = false, $search_term = false, $offset=0, $limit=100, $order_by = false ){
		$result = false;
		if( $item_id ){
			$row = $this->db->get_where( 'temp_quote_items',['item_id'=>$item_id])->row();
			if( !empty( $row ) ){
				$this->session->set_flashdata( 'message','Item found' );
				$result = $row;
			} else {
				$this->session->set_flashdata( 'message','Item not found' );
			}
			return $result;
		}

		if( $item_code ){
			$row = $this->db->get_where( 'temp_quote_items',['item_code'=>$item_code] )->row();

			if( !empty( $row ) ){
				$this->session->set_flashdata( 'message','Item found' );
				$result = $row;
			} else {
				$this->session->set_flashdata( 'message','Item not found' );
			}
			return $result;
		}

		if( $search_term ){
			$this->db->or_like( 'item_name', $search_term );
			$this->db->or_like( 'item_code', $search_term );
		}

		if( !empty( $order_by ) ){
			$this->db->order_by( $order_by );
		} else {
			$this->db->order_by( 'item_id DESC' );
		}

		$temp_quote_items = $this->db->offset( $offset )
			->limit( $limit )
			->get( 'temp_quote_items' );

		if( $temp_quote_items->num_rows() > 0 ){
			$this->session->set_flashdata( 'message','Item records found' );
			$result = $temp_quote_items->result();
		}else{
			$this->session->set_flashdata( 'message','Item record(s) not found' );
		}
		return $result;
	}

}