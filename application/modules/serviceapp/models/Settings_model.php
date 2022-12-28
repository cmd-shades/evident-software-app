<?php

namespace Application\Modules\Service\Models;

class Settings_model extends CI_Model {

	function __construct(){
		parent::__construct();
		$this->load->model( 'Asset_model','asset_service' );	
		$this->load->model( 'Audit_model','evidocs_service' );
		$this->load->model( 'People_model','people_service' );
		$this->load->model( 'Site_model','site_service' );
		$this->load->model( 'Fleet_model', 'fleet_service' );
    }

	/*
	* Add Table Options
	*/
	public function add_table_option( $account_id = false, $table_name = false, $postdata = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $table_name ) ){
			$check_exists = $this->db->get_where( 'settings_configurable_tables', [ 'account_id'=>$account_id, 'table_name'=>$table_name ] )->row();
			if( !empty( $check_exists ) ){
				
				switch( strtolower( $table_name ) ){
					case 'audit_categories':
						$added 	= $this->evidocs_service->add_category( $account_id, $postdata );
						break;
					
					case 'asset_types':
						$added 	= $this->asset_service->add_asset_type( $account_id, $postdata );
						break;
					
					case 'asset_statuses':
						$added 	= $this->asset_service->add_asset_status( $account_id, $postdata );
						break;
				}
				
				$result	= ( !empty( $added ) ) ? $added : false;
				$message= $this->session->flashdata('message');
				
				if( !empty( $result ) ){
					$this->session->set_flashdata( 'message', $message );
				}
			}
		}
		return $result;
	}
	
	
	/*
	* Process a Delete Option request
	*/
	public function delete_table_option( $account_id = false, $table_name = false, $postdata = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $table_name ) ){
			
			$record_id  = !empty( $postdata['record_id'] ) ? $postdata['record_id'] : null;
			$action  	= !empty( $postdata['action'] ) ? $postdata['action'] : 'archive';

			$check_exists = $this->db->get_where( 'settings_configurable_tables', [ 'account_id'=>$account_id, 'table_name'=>$table_name ] )->row();
			if( !empty( $check_exists ) && !empty( $record_id ) ){
				
				$table_info  = $this->db->list_fields( $table_name );
				$primary_key = ( !empty( $table_info[0] ) ) ? $table_info[0] : false;
				
				if( !empty( $primary_key ) ){
					$update_data = [
						'is_active'=>0,
						'last_modified_by'=>0
					];
					$update_data[$primary_key] = $record_id;
					
					if( $action == 'delete' ){
						## Delete completely
						$this->db->where( 'account_id', $account_id )
							->where( $primary_key, $record_id )
							->delete( $table_name );
							
						$message = 'Option deleted successfully';
					}else if( $action == 'archive' ){
						## Simply Archive it
						$this->db->where( 'account_id', $account_id )
							->where( $primary_key, $record_id )
							->update( $table_name, $update_data );
						#$message = 'Option archived successfully';
						$message = 'Option deleted successfully';
					}
					
					if( $this->db->trans_status() !== false ){
						$result = true;
						$this->session->set_flashdata( 'message', $message );
					}

				} else {
					$result = false;
					$this->session->set_flashdata('message','The Supplied table is not configurable');				
				}
			}else{
				$result = false;
				$this->session->set_flashdata('message','Access Denied: This option is not available to you for deletion!');
			}
		}else{
			$this->session->set_flashdata('message','Access Denied: Missing required data');
		}
		return $result;
	}
	
	/** Get Option record **/
	public function fetch_table_option( $account_id = false, $table_name = false, $postdata = false ){

		$result = false;
		if( !empty( $account_id ) && !empty( $table_name ) ){
			$record_id  = !empty( $postdata['record_id'] ) ? $postdata['record_id'] : null;
			$action  	= !empty( $postdata['action'] ) ? $postdata['action'] : 'archive';

			$check_exists = $this->db->get_where( 'settings_configurable_tables', [ 'account_id'=>$account_id, 'table_name'=>$table_name ] )->row();
			if( !empty( $check_exists ) && !empty( $record_id ) ){
				
				$table_info  = $this->db->list_fields( $table_name );
				$primary_key = ( !empty( $table_info[0] ) ) ? $table_info[0] : false;
				
				if( !empty( $primary_key ) ){
					$where[$primary_key] = $record_id;
					switch( strtolower( $table_name ) ){
						case 'audit_categories':
							$record_data	= $this->evidocs_service->get_audit_categories( $account_id, $where );
							break;
						
						case 'asset_types':
							$record_data	= $this->asset_service->get_asset_types( $account_id, $record_id );
							break;
						
						case 'asset_statuses':
							$record_data	= $this->asset_service->get_asset_statuses( $account_id, $where );
							break;
					}

					$result	= ( !empty( $record_data ) ) ? $record_data : false;
					$message= $this->session->flashdata('message');
					
					if( !empty( $result ) ){
						$message = ( !empty( $message ) ) ? $message : 'Record data found';
						$this->session->set_flashdata( 'message', $message );
					}
					
				} else {
					$result = false;
					$this->session->set_flashdata('message','The Supplied table is not configurable');				
				}
			}else{
				$result = false;
				$this->session->set_flashdata('message','Access Denied: This option is not available to you for deletion!');
			}
		}else{
			$this->session->set_flashdata('message','Access Denied: Missing required data');
		}
		return $result;
	}
	
	/*
	* Edit Table Options
	*/
	public function edit_table_option( $account_id = false, $table_name = false, $postdata = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $table_name ) ){
			$check_exists = $this->db->get_where( 'settings_configurable_tables', [ 'account_id'=>$account_id, 'table_name'=>$table_name ] )->row();
			if( !empty( $check_exists ) ){
				
				switch( strtolower( $table_name ) ){
					case 'audit_categories':
						$updated 	= $this->evidocs_service->update_category( $account_id, $postdata );
						break;
					
					case 'asset_types':
						$updated 	= $this->asset_service->update_asset_type( $account_id, $postdata );
						break;
					
					case 'asset_statuses':
						$updated 	= $this->asset_service->update_asset_status( $account_id, $postdata );
						break;
				}
				
				$result	= ( !empty( $updated ) ) ? $updated : false;
				$message= $this->session->flashdata('message');
				
				if( !empty( $result ) ){
					$this->session->set_flashdata( 'message', $message );
				}
			}
		}
		return $result;
	}

}