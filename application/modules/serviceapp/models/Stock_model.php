<?php if (!defined('BASEPATH'))exit('No direct script access allowed');

class Stock_model extends CI_Model {

	function __construct(){
		parent::__construct();
		$section 	   = explode("/", $_SERVER["SCRIPT_NAME"]);
		$this->app_root= $_SERVER["DOCUMENT_ROOT"]."/".$section[1]."/";
		$this->app_root= str_replace('/index.php','',$this->app_root);
    }

	/** Searchable fields **/
	private $stock_searchable_fields 		= [ 'stock_items.item_id', 'stock_items.item_name', 'stock_items.item_type', 'stock_items.item_code' ];
	private $bom_searchable_fields   		= [ 'bom_items.item_id', 'bom_items.item_name', 'bom_items.item_type', 'bom_items.item_code' ];
	private $bom_categories_search_fields   = [ 'bom_categories.bom_category_name', 'bom_categories.bom_category_group', 'bom_categories.bom_category_description' ];
	
	/** Primary table name **/
	private $primary_tbl = 'stock_items';
	
	/* 
	*	Get list of Stock items and search though it
	*/	
	public function get_stock_items( $account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){

		$result = false;

		if( !empty( $account_id ) ){
			
			$where = $raw_where = convert_to_array( $where );
			
			if( !empty( $where['ajax_req'] ) ){
				$this->db->select( 'item_name `label`, item_code `value`, item_id, item_category, item_type, item_qty, buy_price, sell_price', false );
				unset( $where['ajax_req'] );
			} else {
				$this->db->select( 'stock_items.*, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false );
			}
			
			$this->db->join( 'user creater', 'creater.id = stock_items.created_by', 'left' )
				->join( 'user modifier', 'modifier.id = stock_items.last_modified_by', 'left' )
				->where( '( stock_items.archived != 1 or stock_items.archived is NULL )' )
				->where( 'stock_items.account_id', $account_id );

			if( isset( $where['item_id'] ) || isset( $where['item_code'] ) ){
				
				$where_condition = ( !empty( $where['item_id'] ) ) ? ['stock_items.item_id'=>$where['item_id']] : ( !empty( $where['item_code'] ) ? ['stock_items.item_code'=>$where['item_code']] : false );
				
				if( !empty( $where_condition ) ){
					$row = $this->db->get_where( 'stock_items', $where_condition )
						->row();

					if( !empty( $row ) ){
						$result = $row;
						$this->session->set_flashdata( 'message','Stock item data found' );
						return $result;
					} else {
						$this->session->set_flashdata( 'message','Stock item data not found' );
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
						foreach( $this->stock_searchable_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}
						
						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				}else{
					foreach( $this->stock_searchable_fields as $k=>$field ){
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
				$this->db->order_by( 'item_id DESC, item_name' );
			}
			
			if( $limit > 0 ){
				$this->db->limit( $limit, $offset );
			}
			
			$query = $this->db->get( 'stock_items' );

			if( $query->num_rows() > 0 ){				
				$result_data = $query->result();

				$result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
				$result->records 			= $result_data;
				$counters 					= $this->stock_items_totals( $account_id, $search_term, $raw_where );
				$result->counters->total 	= ( !empty( $counters->total ) ) ? $counters->total : null;
				$result->counters->pages 	= ( !empty( $counters->pages ) ) ? $counters->pages : null;
				$result->counters->limit  	= ( !empty( $apply_limit ) ) ? $limit : $result->counters->total;
				$result->counters->offset 	= $offset;
				
				$this->session->set_flashdata( 'message','Stock Items data found' );
			} else {
				$this->session->set_flashdata( 'message','No data found' );
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}
		
		return $result;
	}
	
	/** Get Stock item counters **/
	public function stock_items_totals( $account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT ){
		$result = false;
		if( !empty( $account_id ) ){

			if( !empty( $where['ajax_req'] ) ){
				unset( $where['ajax_req'] );
			}
			
			$this->db->select( 'stock_items.item_id', false )
			->join( 'user creater', 'creater.id = stock_items.created_by', 'left' )
			->join( 'user modifier', 'modifier.id = stock_items.last_modified_by', 'left' )
			->where( '( stock_items.archived != 1 or stock_items.archived is NULL )' )
			->where( 'stock_items.account_id', $account_id );

			if( !empty( $search_term ) ){
				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->stock_searchable_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}
						
						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				}else{
					foreach( $this->stock_searchable_fields as $k=>$field ){
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

			$query 			  = $this->db->from( 'stock_items' )->count_all_results();
			$results['total'] = !empty( $query ) ? $query : 0;
			$limit 			  = ( $limit > 0 ) ? $limit : $results['total'];
			$results['pages'] = !empty( $query ) ? ceil( $query / $limit ) : 0;
			return json_decode( json_encode( $results ) );
		}
		return $result;
	}
	
	/*
	* Create new Stock item
	*/
	public function create_stock_item( $account_id = false, $stock_item_data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $stock_item_data ) ){
			$data = $this->ssid_common->_data_prepare( $stock_item_data );
			if( !empty( $data ) ){
				$new_stock_item 				= $this->ssid_common->_filter_data( 'stock_items', $data );
				$new_stock_item['created_by']	= $this->ion_auth->_current_user->id;
				$this->db->insert( 'stock_items', $new_stock_item );
				if( $this->db->trans_status() !== FALSE ){
					$data['item_id'] = $this->db->insert_id();
					$result = $this->get_stock_items( $account_id, false, [ 'item_id'=>$data['item_id'] ] );
					$result = $this->db->get_where( 'stock_items', [ 'account_id' => $account_id, 'item_id' => $data['item_id'] ] )->row();
					$this->session->set_flashdata('message','Stock Item created successfully.');
				}
			} else {
				$this->session->set_flashdata('message','Error parsing your supplied data. Request aborted');
			}
		} else {
			$this->session->set_flashdata('message','Your request is missing required information.');
		}
		return $result;
	}

	
	/** Update an existing Stock Item record **/
	public function update_stock_item( $account_id = false, $item_id = false, $update_data = false  ){
		$result = false;
		if( !empty( $account_id ) && !empty( $item_id )  && !empty( $update_data ) ){

			$ref_condition = [ 'account_id'=>$account_id, 'item_id'=>$item_id ];
			$update_data   = $this->ssid_common->_data_prepare( $update_data );
			$update_data   = $this->ssid_common->_filter_data( 'stock_items', $update_data );
			$record_pre_update = $this->db->get_where( 'stock_items', [ 'account_id'=>$account_id, 'item_id'=>$item_id ] )->row();

			if( !empty( $record_pre_update ) ){
				
				

				$check_conflict = $this->db->select( 'item_id', false )
					->where( 'stock_items.item_name', $update_data['item_name'] )
					->where( 'stock_items.item_code', $update_data['item_code'] )
					->where( 'stock_items.account_id', $account_id )
					->where( 'stock_items.item_id !=', $item_id )
					->where( 'stock_items.archived !=', 1 )
					->limit( 1 )
					->get( 'stock_items' )
					->row();

				if( !$check_conflict ){

					$update_data['last_modified_by'] = $this->ion_auth->_current_user->id;
					$this->db->where( $ref_condition )
						->update( 'stock_items', $update_data );

					$updated_record = $this->get_stock_items( $account_id, $item_id );
					$result 		= ( !empty( $updated_record->records ) ) ? $updated_record->records : ( !empty( $updated_record ) ? $updated_record : false );

					$this->session->set_flashdata( 'message', 'Stock Item updated successfully' );
					return $result;
				} else {
					$this->session->set_flashdata( 'message', 'Item with same name already Exists! Update request aborted' );
					return false;
				}

			} else {
				$this->session->set_flashdata( 'message', 'This Stock Item record does not exist or does not belong to you.' );
				return false;
			}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information.' );
		}
		return $result;
	}
	
	/*
	* Delete Stock Item record
	*/
	public function delete_stock_item( $account_id = false, $item_id = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $item_id ) ){
			$conditions 		= ['account_id'=>$account_id,'item_id'=>$item_id];
			$stock_item_exists 	= $this->db->get_where( 'stock_items',$conditions )->row();
			if( !empty( $stock_item_exists ) ){
				
				$this->db->where( ['account_id'=>$account_id, 'item_code'=>$stock_item_exists->item_code] )
					->update( 'job_consumed_items', ['archived'=>1] );
				
				$this->db->where( $conditions )
					->update( 'stock_items', ['archived'=>1] );
					
				if( $this->db->trans_status() !== FALSE ){
					$this->session->set_flashdata('message','Record deleted successfully.');
					$result = true;
				}
			}else{
				$this->session->set_flashdata('message','Invalid Stock Item ID');
			}

		}else{
			$this->session->set_flashdata('message','No Stock item record found.');
		}
		return $result;
	}

	/** Get Asset types **/
	public function get_stock_item_types( $account_id = false, $category_id = false, $category = false, $grouped = false ){
		$result = null;
		if( $account_id ){
			$this->db->where( 'stock_item_types.account_id', $account_id );
		}else{
			$this->db->where( '( stock_item_types.account_id IS NULL OR stock_item_types.account_id = "" )' );
		}

		$query = $this->db->select( 'stock_item_types.*', false )
			->order_by( 'stock_item_types.stock_item_type' )
			->where( 'stock_item_types.is_active', 1 )
			->get( 'stock_item_types' );

		if( $query->num_rows() > 0 ){
			$result = $query->result();
		}else{
			$result = $this->get_stock_item_types();
		}

		return $result;
	}
	
	/* 
	*	Get BOMS /SORs items and search though it
	*/	
	public function get_bom_items( $account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){

		$result = false;

		if( !empty( $account_id ) ){
			
			$where = $raw_where = convert_to_array( $where );
			
			if( !empty( $where['ajax_req'] ) ){
				$this->db->select( 'item_name `label`, item_code `value`, item_id, item_category, item_type, item_qty, bom_categories.bom_category_name', false );
				unset( $where['ajax_req'] );
			} else {
				$this->db->select( 'bom_items.*, bom_categories.bom_category_name, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false );
			}
			
			$arch_where = "( bom_items.archived != 1 or bom_items.archived is NULL )";
			
			$this->db->join( 'bom_categories', 'bom_categories.bom_category_id = bom_items.bom_category_id', 'left' )
				->join( 'user creater', 'creater.id = bom_items.created_by', 'left' )
				->join( 'user modifier', 'modifier.id = bom_items.last_modified_by', 'left' )
				->where( 'bom_items.is_active', 1 )
				->where( $arch_where )
				->where( 'bom_items.account_id', $account_id );

			if( isset( $where['item_id'] ) || isset( $where['item_code'] ) ){
				
				$where_condition = ( !empty( $where['item_id'] ) ) ? ['bom_items.item_id'=>$where['item_id']] : ( !empty( $where['item_code'] ) ? ['bom_items.item_code'=>$where['item_code']] : false );
				
				if( !empty( $where_condition ) ){
					$row = $this->db->where( 'bom_items.account_id', $account_id )
						->where( $arch_where )
						->get_where( 'bom_items', $where_condition )
						->row();

					if( !empty( $row ) ){
						$result = $row;
						$this->session->set_flashdata( 'message','BOMs/SORs item data found' );
						return $result;
					} else {
						$this->session->set_flashdata( 'message','BOMs/SORs item data not found' );
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
						foreach( $this->bom_searchable_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}
						
						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				}else{
					foreach( $this->bom_searchable_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}
					
					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}
			
			if( isset( $where['bom_category_id'] ) ){
				if( !empty( $where['bom_category_id'] ) ){
					$this->db->where( 'bom_items.bom_category_id', $where['bom_category_id'] );
				}
				unset( $where['bom_category_id'] );
			}

			if( !empty( $where ) ){
				
				if( !empty( $where ) ){
					//$this->db->where( $where );
				}
			}
			
			if( !empty( $order_by ) ){
				$this->db->order_by( $order_by );
			}else{
				$this->db->order_by( 'item_id DESC, item_name' );
			}
			
			if( $limit > 0 ){
				$this->db->limit( $limit, $offset );
			}
			
			$query = $this->db->get( 'bom_items' );

			if( $query->num_rows() > 0 ){				
				$result_data = $query->result();

				$result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
				$result->records 			= $result_data;
				$counters 					= $this->bom_items_totals( $account_id, $search_term, $raw_where );
				$result->counters->total 	= ( !empty( $counters->total ) ) ? $counters->total : null;
				$result->counters->pages 	= ( !empty( $counters->pages ) ) ? $counters->pages : null;
				$result->counters->limit  	= ( !empty( $apply_limit ) ) ? $limit : $result->counters->total;
				$result->counters->offset 	= $offset;
				
				$this->session->set_flashdata( 'message','BOM Items data found' );
			} else {
				$this->session->set_flashdata( 'message','No data found' );
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}
		
		return $result;
	}
	
	/** Get BOMs/SORs item counters **/
	public function bom_items_totals( $account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT ){
		$result = false;
		if( !empty( $account_id ) ){

			if( !empty( $where['ajax_req'] ) ){
				unset( $where['ajax_req'] );
			}
			
			$this->db->select( 'bom_items.item_id', false )
			->join( 'user creater', 'creater.id = bom_items.created_by', 'left' )
			->join( 'user modifier', 'modifier.id = bom_items.last_modified_by', 'left' )
			->where( 'bom_items.is_active', 1 )
			->where( 'bom_items.account_id', $account_id );

			if( !empty( $search_term ) ){
				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->bom_searchable_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}
						
						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				}else{
					foreach( $this->bom_searchable_fields as $k=>$field ){
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

			$query 			  = $this->db->from( 'bom_items' )->count_all_results();
			$results['total'] = !empty( $query ) ? $query : 0;
			$limit 			  = ( !empty( $apply_limit ) ) ? $limit : $results['total'];
			$results['pages'] = !empty( $query ) ? ceil( $query / $limit ) : 0;
			return json_decode( json_encode( $results ) );
		}
		return $result;
	}

	/*
	* 	Delete BOM Item record
	*/
	public function delete_bom_item( $account_id = false, $item_id = false ){
		$result = false;
		
		if( !empty( $account_id ) && !empty( $item_id ) ){
			$conditions 		= ['account_id'=>$account_id,'item_id'=>$item_id];
			$bom_item_exists 	= $this->db->get_where( 'bom_items',$conditions )->row();
			if( !empty( $bom_item_exists ) ){
				
				$this->db->where( ['account_id' => $account_id, 'item_code' => $bom_item_exists->item_code] )
					->update( 'job_consumed_items', ['archived' => 1] );
				
				$this->db->where( $conditions )
					->update( 'bom_items', ['archived' => 1, 'is_active' => NULL] );
					
				if( $this->db->trans_status() !== FALSE ){
					$this->session->set_flashdata( 'message','Record deleted successfully.' );
					$result = true;
				}
			}else{
				$this->session->set_flashdata( 'message','Invalid BOM Item ID' );
			}

		} else {
			$this->session->set_flashdata( 'message','No BOM item record found.' );
		}
		return $result;
	}
	
	
	/** 
	*	Update an existing BOM Item record 
	**/
	public function update_bom_item( $account_id = false, $item_id = false, $update_data = false  ){
		$result = false;
		if( !empty( $account_id ) && !empty( $item_id )  && !empty( $update_data ) ){

			$ref_condition 		= [ 'account_id'=>$account_id, 'item_id'=>$item_id ];
			$update_data   		= $this->ssid_common->_data_prepare( $update_data );
			$update_data   		= $this->ssid_common->_filter_data( 'bom_items', $update_data );
			$record_pre_update 	= $this->db->get_where( 'bom_items', [ 'account_id'=>$account_id, 'item_id'=>$item_id ] )->row();

			if( !empty( $record_pre_update ) ){
				$update_data['last_modified_by'] = $this->ion_auth->_current_user->id;
				
				$arch_where = "( bom_items.archived != 1 or bom_items.archived is NULL )";
				$this->db->where( $ref_condition )
					->where( $arch_where )
					->update( 'bom_items', $update_data );

				$updated_record = $this->get_bom_items( $account_id, false, ["item_id" => $item_id] );
				$result 		= ( !empty( $updated_record->bom_items ) ) ? $updated_record->bom_items : ( !empty( $updated_record ) ? $updated_record : false );

				$this->session->set_flashdata( 'message', 'BOM Item updated successfully' );
			} else {
				$this->session->set_flashdata( 'message', 'This BOM Item record does not exist or does not belong to you.' );
				return false;
			}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information.' );
		}
		return $result;
	}
	
	
	/*
	* 	Create new BOM item
	*/
	public function create_bom_item( $account_id = false, $bom_data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $bom_data ) ){
			$data = $this->ssid_common->_data_prepare( $bom_data );
			if( !empty( $data ) ){
				$new_bom_item 				= $this->ssid_common->_filter_data( 'bom_items', $data );
				$new_bom_item['created_by']= $this->ion_auth->_current_user->id;
				$this->db->insert( 'bom_items' ,$new_bom_item );
				if( $this->db->trans_status() !== FALSE ){
					$data['bom_item_id'] = $this->db->insert_id();
					## $result = $this->get_bom_items( $account_id, false, [ 'item_id'=>$data['bom_item_id'] ] );
					$result = $this->db->get_where( 'bom_items', [ 'account_id' => $account_id, 'item_id' => $data['bom_item_id'] ] )->row();
					$this->session->set_flashdata( 'message','BOM Item created successfully.' );
					return $result;
				}
			}
			$this->session->set_flashdata('message','Error parsing your supplied data. Request aborted');
		} else {
			$this->session->set_flashdata('message','Your request is missing required information.');
		}
		return $result;
	}
	
	
	/*
	*	Get list of BOM Categories and search through them
	*/
	public function get_bom_categories( $account_id = false, $bom_category_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){

		$result = false;

		if( !empty( $account_id ) ){
			$this->db->select( 'bom_categories.*, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false )
				->join( 'user creater', 'creater.id = bom_categories.created_by', 'left' )
				->join( 'user modifier', 'modifier.id = bom_categories.last_modified_by', 'left' )
				->where( 'bom_categories.is_active', 1 )
				->where( 'bom_categories.account_id', $account_id );

				$where = $raw_where = convert_to_array( $where );

			if( !empty( $bom_category_id ) || isset( $where['bom_category_id'] ) ){
				$bom_category_id	= ( !empty( $bom_category_id ) ) ? $bom_category_id : $where['bom_category_id'];
				if( !empty( $bom_category_id ) ){

					$row = $this->db->get_where( 'bom_categories', ['bom_categories.bom_category_id'=>$bom_category_id ] )->row();

					if( !empty( $row ) ){
						$result  				= $row;
						$this->session->set_flashdata( 'message','BOM Categories data found' );
						return $result;
					} else {
						$this->session->set_flashdata( 'message','BOM Categories data not found' );
						return false;
					}
				}
				unset( $where['bom_category_id'], $where['category_ref'] );
			}

			if( !empty( $search_term ) ){
				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->bom_categories_search_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				} else {
					foreach( $this->bom_categories_search_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( !empty( $where ) ){

				if( isset( $where['bom_category_name'] ) ){
					if( !empty( $where['bom_category_name'] ) ){
						$category_group = strtolower( strip_all_whitespace( $where['bom_category_name'] ) );
						$this->db->where( '( bom_categories.bom_category_name = "'.$where['bom_category_name'].'" OR bom_categories.bom_category_group = "'.$category_group.'" )' );
					}
					unset( $where['bom_category_name'] );
				}

				if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}

			if( !empty( $order_by ) ){
				$this->db->order_by( $order_by );
			} else {
				$this->db->order_by( 'bom_category_id DESC, bom_category_name' );
			}
			
			if( $limit > 0 ){
				$this->db->limit( $limit, $offset );
			}

			$query = $this->db->get( 'bom_categories' );


			if( $query->num_rows() > 0 ){

				$result_data = $query->result();

				$result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
				$result->records 			= $result_data;
				$counters 					= $this->get_bom_categories_totals( $account_id, $search_term, $raw_where );
				$result->counters->total 	= ( !empty( $counters->total ) ) ? $counters->total : null;
				$result->counters->pages 	= ( !empty( $counters->pages ) ) ? $counters->pages : null;
				$result->counters->limit  	= ( !empty( $apply_limit ) ) ? $limit : $result->counters->total;
				$result->counters->offset 	= $offset;

				$this->session->set_flashdata( 'message','Categories data found' );
			} else {
				$this->session->set_flashdata( 'message','There\'s currently no Categories setup for your Account' );
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}

		return $result;
	}


	/** Get Category lookup counts **/
	public function get_bom_categories_totals( $account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT ){
		$result = false;
		if( !empty( $account_id ) ){

			$this->db->select( 'bom_categories.bom_category_id', false )
				->where( 'bom_categories.is_active', 1 )
				->where( 'bom_categories.account_id', $account_id );

			$where = $raw_where = convert_to_array( $where );

			if( !empty( $search_term ) ){
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->bom_categories_search_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				} else {
					foreach( $this->bom_categories_search_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( !empty( $where ) ){

				if( isset( $where['bom_category_name'] ) ){
					if( !empty( $where['bom_category_name'] ) ){
						$category_group = strtoupper( strip_all_whitespace( $where['bom_category_name'] ) );
						$this->db->where( '( bom_categories.bom_category_name = "'.$where['bom_category_name'].'" OR bom_categories.bom_category_group = "'.$category_group.'" )' );
					}
					unset( $where['category_name'] );
				}

				if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}

			$query 			  = $this->db->from( 'bom_categories' )->count_all_results();
			$results['total'] = !empty( $query ) ? $query : 0;
			$limit 				= ( $limit > 0 ) ? $limit : $results['total'];
			$results['pages'] = !empty( $query ) ? ceil( $query / $limit ) : 0;
			return json_decode( json_encode( $results ) );
		}
		return $result;
	}
	
	
	/** Add a NEW BOM Category **/
	public function add_bom_category( $account_id = false, $bom_category_data = false ){

		$result = null;

		if( !empty( $account_id ) && !empty( $bom_category_data  ) ){

			foreach( $bom_category_data as $col => $value ){
				if( $col == 'bom_category_name' ){
					$data['bom_category_group'] 	= strtoupper( strip_all_whitespace( $value ) );
				}
				$data[$col] = $value;
			}

			$check_exists = $this->db->where( 'account_id', $account_id )
				->where( '( bom_categories.bom_category_name = "'.$data['bom_category_name'].'" OR bom_categories.bom_category_group = "'.$data['bom_category_group'].'" )' )
				->limit( 1 )
				->get( 'bom_categories' )
				->row();

			$data = $this->ssid_common->_filter_data( 'bom_categories', $data );

			if( !empty( $check_exists  ) ){
				$data['last_modified_by'] = $this->ion_auth->_current_user->id;
				$this->db->where( 'bom_category_id', $check_exists->bom_category_id )
					->update( 'bom_categories', $data );
					$this->session->set_flashdata( 'message', 'This BOM category already exists, record has been updated successfully.' );
					$result = $check_exists;
			}else{
				$data['created_by'] 		= $this->ion_auth->_current_user->id;
				$this->db->insert( 'bom_categories', $data );
				$this->session->set_flashdata( 'message', 'New BOM category added successfully.' );
				$data['bom_category_id'] = (string) $this->db->insert_id();
				$result = $data;
			}

		}else{
			$this->session->set_flashdata( 'message','Error! Missing required information.' );
		}

		return $result;
	}


	/** Update BOM Category **/
	public function update_bom_category( $account_id = false, $bom_category_data = false ){

		$result = null;

		if( !empty( $account_id ) && !empty( $bom_category_data['bom_category_id'] ) && !empty( $bom_category_data ) ){

			foreach( $bom_category_data as $col => $value ){
				if( $col == 'bom_category_name' ){
					$data['bom_category_group'] 	= strtoupper( strip_all_whitespace( $value ) );
				}
				$data[$col] = $value;
			}

			if( !empty( $data['bom_category_id'] ) ){
				$check_conflict = $this->db->where( 'account_id', $account_id )
					->where( '( bom_categories.bom_category_name = "'.$data['bom_category_name'].'" OR bom_categories.bom_category_group = "'.$data['bom_category_group'].'" )' )
					->where( 'bom_categories.bom_category_id !=', $data['bom_category_id'] )
					->get( 'bom_categories' )->row();

				$data = $this->ssid_common->_filter_data( 'bom_categories', $data );

				if( !$check_conflict ){
					$data['last_modified_by'] = $this->ion_auth->_current_user->id;
					$this->db->where( [ 'account_id'=>$account_id, 'bom_category_id'=>$data['bom_category_id'] ] )
						->update( 'bom_categories', $data );
						if( $this->db->trans_status() !== false ){
							$result = $this->get_bom_categories( $account_id, $data['bom_category_id'] );
							$this->session->set_flashdata( 'message', 'BOM category updated successfully.' );
						}

				}else{
					$this->session->set_flashdata( 'message', 'This BOM category does not exists or does not belong to you.' );
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
	* Delete BOM Category record
	*/
	public function delete_bom_category( $account_id = false, $bom_category_id = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $bom_category_id ) ){
			$conditions 		= ['account_id'=>$account_id,'bom_category_id'=>$bom_category_id];
			$category_item_exists 	= $this->db->get_where( 'bom_categories',$conditions )->row();
			if( !empty( $category_item_exists ) ){

				$this->db->where( $conditions )
					->update( 'bom_categories', [
						'is_active'			=>0, 
						'bom_category_name'	=>$category_item_exists->bom_category_name.' (Archived)',  
						'bom_category_group'=> strip_all_whitespace( $category_item_exists->bom_category_name.' (Archived)' ),
					] );
					
				if( $this->db->trans_status() !== FALSE ){
					$this->session->set_flashdata('message','BOM Category deleted successfully.');
					$result = true;
				}
			}else{
				$this->session->set_flashdata( 'message','Invalid BOM Category ID' );
			}

		}else{
			$this->session->set_flashdata( 'message','No Category record found.' );
		}
		return $result;
	}

}