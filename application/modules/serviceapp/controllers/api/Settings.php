<?php

namespace Application\Modules\Service\Controllers\Api;
 
class Settings extends REST_Controller {

    function __construct(){
        // Construct the parent class
        parent::__construct();
		$this->load->model( 'Settings_model','settings_service' );
    }

	/** 
	* Process an Add Request from the Settings module
	*/
    public function add_option_post(){
		$postdata   = $this->post();
		$account_id = (int) $this->post('account_id');
		$table_name = $this->post('table_name');
		
		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
        $this->form_validation->set_rules( 'module_id', 'Module Name', 'required' );
        $this->form_validation->set_rules( 'table_name', 'Table Name', 'required' );
		
		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}
	
		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			## One of the required fields is invalid
			$message = [
				'status' => FALSE,
				'message' => 'Invalid data: ',
				'add_option' => NULL
			];
			
			$message['message'] = (!$account_id)? $message['message'].'account_id, ': $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID',
				'add_option' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$add_option = $this->settings_service->add_table_option( $account_id, $table_name, $postdata );
		
		if( !empty( $add_option ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata( 'message' ),
				'add_option' => null
			];
			$this->response( $message, REST_Controller::HTTP_OK ); 
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata( 'message' ),
				'add_option' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
    }
	
	/** Retrieve an Option **/
	public function fetch_option_get(){
        $postdata 	= $this->get();
		$account_id = (int) $this->get( 'account_id' );
		$record_id  = (int) $this->get( 'record_id' );
		$table_name = $this->get( 'table_name' );
		
		if ( $record_id <= 0 ){
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST ); // BAD_REQUEST (400) being the HTTP response code
        }
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'fetch_option' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$fetch_option = $this->settings_service->fetch_table_option( $account_id, $table_name, $postdata );
		
		if( !empty( $fetch_option ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'fetch_option' => $fetch_option
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'fetch_option' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}
	
	/** 
	* Process a Delete Request from the Settings module
	*/
    public function delete_option_post(){
		$account_id  = (int) $this->post('account_id');
		$table_name  = $this->post('table_name');
		$postdata  	 = $this->post();
		
		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
        $this->form_validation->set_rules( 'module_id', 'Module Name', 'required' );
        $this->form_validation->set_rules( 'table_name', 'Table Name', 'required' );
        $this->form_validation->set_rules( 'record_id', 'Record ID', 'required' );
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID',
				'delete_option' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$delete_option = $this->settings_service->delete_table_option( $account_id, $table_name, $postdata );
		
		if( !empty( $delete_option ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata( 'message' ),
				'delete_option' => null
			];
			$this->response( $message, REST_Controller::HTTP_OK ); 
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata( 'message' ),
				'delete_option' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
    }
	
	/** 
	* Process an Edit Request from the Settings module
	*/
    public function edit_option_post(){
		$postdata   = $this->post();
		$account_id = (int) $this->post('account_id');
		$table_name = $this->post('table_name');
		
		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
        $this->form_validation->set_rules( 'module_id', 'Module Name', 'required' );
        $this->form_validation->set_rules( 'table_name', 'Table Name', 'required' );
		
		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}
	
		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			## One of the required fields is invalid
			$message = [
				'status' => FALSE,
				'message' => 'Invalid data: ',
				'edit_option' => NULL
			];
			
			$message['message'] = (!$account_id)? $message['message'].'account_id, ': $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID',
				'edit_option' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$edit_option = $this->settings_service->edit_table_option( $account_id, $table_name, $postdata );
		
		if( !empty( $edit_option ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata( 'message' ),
				'edit_option' => $edit_option
			];
			$this->response( $message, REST_Controller::HTTP_OK ); 
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata( 'message' ),
				'edit_option' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
    }

}
