<?php

namespace Application\Modules\Service\Models;

class Access_model extends CI_Model {

	function __construct(){
		parent::__construct();
		$this->load->model('Modules_model','module_service');
		$this->load->model('Account_model','account_service');
    }

	/*
	* Check user-access to a module or application
	*/
	public function check_module_access( $account_id=false, $user_id=false , $module_id=false, $app_uuid = false, $as_list = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $user_id ) ){
			$access = $this->module_service->get_allowed_modules( $account_id, $user_id, $module_id, $app_uuid, $as_list );
			if( !empty( $access ) ){
				$result = $access;
				$this->session->set_flashdata('message','Module access verified');
			}else{
				$result = false;
				$this->session->set_flashdata('message','Access Denied: No permissions to view this module / app');
			}
		}else{
			$this->session->set_flashdata('message','Access Denied: Missing required data');
		}
		return $result;
	}
	
	/*
	* Check user access to a module item
	*/
	public function get_module_item_access( $account_id = false, $user_id = false , $module_id = false, $module_item_id = false, $module_item = false, $as_list = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $user_id ) ){
			$permissions = $this->module_service->get_module_item_permissions( $account_id, $user_id, $module_id, $module_item_id, $module_item, $as_list );
			if( !empty( $permissions ) ){
				$result = $permissions;
				$this->session->set_flashdata('message','Module item permissions verified');
			}else{
				$result = false;
				$this->session->set_flashdata('message','Access Denied: No permissions to view this module item');
			}
		}
		return $result;
	}
	
	/*
	* Check user access to a module item
	*/
	public function get_module_items( $account_id = false, $module_id = false, $detailed = false, $mobile_visible = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $module_id ) ){
		
			$module_items = $this->account_service->get_account_modules_items( $module_id, $detailed, $mobile_visible );
			if( !empty( $module_items ) ){
				$result = $module_items;
				$this->session->set_flashdata('message','Module items found');
			}else{
				$result = false;
				$this->session->set_flashdata('message','No Module items found');
			}
		}
		return $result;
	}
	
	/** Update user Permissions **/
	public function update_module_permissions( $account_id = false, $user_id = false, $permissions_data = false ){

		$result = [];
		if( !empty( $account_id ) && !empty( $user_id ) && !empty( $permissions_data ) ){
			
			$has_module_access  = true;
			
			$permissions_data 	= verify_array( $permissions_data );
			
			foreach( $permissions_data as $module_id => $mod_items_data ){
				
				if( !empty( $mod_items_data['module_access'] ) ){
					
					$updated_module_access = $mod_items_data['module_access'];
					
					$updated_module_access = verify_array( $updated_module_access );
					
					if( !empty( $updated_module_access ) ){
						$updated_module_access['account_id'] = $account_id;
						$this->module_service->update_module_access( $account_id, $user_id, $updated_module_access );
						$result[$module_id]['module_access'] = $updated_module_access;
					}

					unset( $mod_items_data['module_access'] );

				}else{
					##Check module access
					$module_access = $this->module_service->get_allowed_modules( $account_id, $user_id, $module_id );
					if( !$module_access ){
						//Grant minimal module access
						$this->module_service->update_module_access( $account_id, $user_id, ['module_id'=>$module_id, 'account_id'=>$account_id, 'has_access'=>1 ] );
					}
				}

				if( !empty( $mod_items_data ) ){
					foreach( $mod_items_data as $mod_item_id => $permissions ){
						$perms = [
							'account_id'=> $account_id,
							'user_id'=> $user_id,
							'module_id'=> $module_id,
							'module_item_id'=> $mod_item_id
						];
						
						$update_data = $this->ssid_common->_filter_data( 'user_module_item_permissions', array_merge( $perms, $permissions ) );
						
						//Check if permission already exists
						$check = $this->db->where( $perms )->limit( 1 )->get( 'user_module_item_permissions' )->row();
					
						$update_data['last_modified_by'] = $this->ion_auth->_current_user->id;
					
						if( !empty( $check ) ){
							$this->db->where( $perms )
								->update( 'user_module_item_permissions', $update_data );
						}else{
							$this->db->insert( 'user_module_item_permissions', $update_data );
						}
						
						if( $this->db->trans_status() !== false ){
							$result[$module_id][$mod_item_id][] = $update_data;
						}
					}				
				}				
			}
			
			if( !empty( $result ) ){
				$this->session->set_flashdata( 'message', 'Permissions updated successfully' );
			}else{
				$this->session->set_flashdata( 'message', 'Update request failed!' );
			}
		}else{
			$this->session->set_flashdata( 'message', 'Invalid request parameters!' );
		}
		return $result;
	}
	
}