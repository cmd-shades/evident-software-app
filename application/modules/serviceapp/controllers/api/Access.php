<?php

namespace Application\Modules\Service\Controllers\Api;
 
class Access extends REST_Controller {

    function __construct(){
        // Construct the parent class
        parent::__construct();
		$this->load->model( 'Access_model','access_service' );
		$this->load->model( 'Modules_model','modules_service' );
    }

	/** 
	* Check user's access to module(s)
	*/
    public function check_module_access_get(){
		$user_id  	 	= ( !empty( $this->get('user_id') ) ) ? (int) $this->get('user_id') : false ;
		$account_id  	= (int) $this->get('account_id');
		$module_id	 	= $this->get('module_id');
		$app_uuid	 	= $this->get('app_uuid');
		$as_list	 	= $this->get('as_list');
		$module_visible = (int) $this->get('module_visible');
		
		$this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('user_id', 'User ID', 'required');
		
		if( $user_id !== ( int ) $this->ion_auth->_current_user->id ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid user ID',
				'module_access' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID',
				'module_access' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$module_access = $this->access_service->check_module_access( $account_id, $user_id, $module_id, $app_uuid, $as_list );
		
		if( !empty( $module_access ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'module_access' => $module_access
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'module_access' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
    }
	
	/** 
	* Check user's access to module(s)
	*/
    public function check_module_access_post(){
		$user_id  	 	= ( !empty( $this->post('user_id') ) ) ? (int) $this->post('user_id') : false ;
		$account_id  	= (int) $this->post('account_id');
		$module_id	 	= $this->post('module_id');
		$app_uuid	 	= $this->post('app_uuid');
		$as_list	 	= $this->post('as_list');
		$module_visible = (int) $this->post('module_visible');
		
		$this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('user_id', 'User ID', 'required');
		
		if( $user_id !== ( int ) $this->ion_auth->_current_user->id ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid user ID',
				'module_access' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID',
				'module_access' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$module_access = $this->access_service->check_module_access( $account_id, $user_id, $module_id, $app_uuid, $as_list );
		
		if( !empty( $module_access ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'module_access' => $module_access
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'module_access' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
    }
	
	/** 
	* Check and get module item permissions
	*/
	public function module_item_permissions_post(){
		$user_id  	 	= (int) $this->post('user_id');
		$account_id  	= (int) $this->post('account_id');
		$module_id	 	= $this->post('module_id');
		$module_item	= $this->post('module_item');
		$module_item_id	= $this->post('module_item_id');
		$as_list	    = $this->post('as_list');
		$this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('user_id', 'User ID', 'required');
        #$this->form_validation->set_rules('module_id', 'Module ID', 'required');
        #$this->form_validation->set_rules('module_item_id', 'Module Item ID', 'required');
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => ( $this->session->flashdata('message') ) ? $this->session->flashdata('message') : 'Invalid main Account ID',
				'mod_item_access' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$item_access = $this->access_service->get_module_item_access( $account_id, $user_id, $module_id, $module_item_id, $module_item, $as_list );
		
		if( !empty($item_access) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'mod_item_access' => $item_access
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'mod_item_access' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
	}
	
	/** Get Module items **/
	public function account_modules_items_get(){
		
		$account_id 	= (int) $this->get('account_id');
		$module_id  	= (int) $this->get('module_id');
		$detailed  		= (int) $this->get('detailed');
		$mobile_visible = (int) $this->get('mobile_visible');

		$this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('user_id', 'User ID', 'required');
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => ( $this->session->flashdata('message') ) ? $this->session->flashdata('message') : 'Invalid main Account ID',
				'module_items' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$module_items = $this->access_service->get_module_items( $account_id, $module_id, $detailed, $mobile_visible );
		
		if( !empty( $module_items ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'module_items' => $module_items
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'module_items' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}
	
	/** Update User Module Permissions **/
	public function update_module_permissions_post(){
		
		$user_id  	 = (int) $this->post('user_id');
		$account_id  = (int) $this->post('account_id');
		$perms_data	 = $this->post('permissions');
		
		$this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('user_id', 'User ID', 'required');
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID',
				'module_permissions' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$updated = $this->access_service->update_module_permissions( $account_id, $user_id, $perms_data );
		
		if( !empty( $updated ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'module_permissions' => $updated
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'module_permissions' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}
	

	/** 
	*	Get Module items 
	**/
	public function module_items_get(){
		
		$get_data = $this->get();
		
		$account_id 		= ( !empty( $get_data['account_id'] ) ) ? ( int ) $get_data['account_id'] : false ;
		$item_id 			= ( !empty( $get_data['item_id'] ) ) ? ( int ) $get_data['item_id'] : false ;
		$module_id 			= ( !empty( $get_data['module_id'] ) ) ? ( int ) $get_data['module_id'] : false ;
		$module_item_name 	= ( !empty( $get_data['module_item_name'] ) ) ? $get_data['module_item_name'] : false ;
		
		$expected_data = [
			'account_id' => $account_id
		];

		$this->form_validation->set_data( $expected_data );
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		
		if( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}
		
		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Validation errors: '.$validation_errors,
				'module_items' 	=> NULL
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> ( $this->session->flashdata( 'message' ) ) ? $this->session->flashdata( 'message' ) : 'Invalid main Account ID',
				'module_items' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$module_items = $this->modules_service->get_module_items( $account_id, $item_id, $module_id, $module_item_name );
		
		if( !empty( $module_items ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'module_items' 	=> $module_items
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'module_items' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
}
