<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends REST_Controller {
	
    function __construct(){
        // Construct the parent class
        parent::__construct();
		$this->load->model('Account_model','account_service');
		$this->load->model('Modules_model','module_service');
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
		$this->lang->load('auth');		
    }
	
	/** 
	* Create new Account resource 
	*/
	public function create_post(){
		
		$account_data 	= $this->post();
		$tables 		= $this->config->item('tables','ion_auth');
		$this->form_validation->set_rules('account_name', 'Account Name', 'required');
        $this->form_validation->set_rules('account_email', 'Account Email', 'required|valid_email|is_unique[' . $tables['account'] . '.account_email]');
        $this->form_validation->set_rules('account_email', 'Your Email Address', 'required|valid_email|is_unique[' . $tables['user'] . '.email]');
		$this->form_validation->set_rules('account_first_name', 'Account First Name', 'required');
        $this->form_validation->set_rules('account_last_name', 'Account Last Name', 'required');
		$this->form_validation->set_rules('admin_username', 'Admin username', 'required|is_unique[' . $tables['user'] . '.username]');
		
		if ($this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}
	
		if( isset($validation_errors) && !empty($validation_errors) ){		
			## One of the required fields is invalid
			$message = [
				'status' => FALSE,
				'message' => 'Invalid Account data: ',
				'account' => NULL
			];
			$message['message'] = ( isset($validation_errors) && !empty($validation_errors) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$new_account = $this->account_service->create_account($account_data);
				
		if( !empty($new_account) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'account' => $new_account
			];
			$this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'account' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		} 
    }

	/** 
	* Update user resource 
	*/
	public function update_post(){
        $account_data	= $this->post();
        $account_id  	= (int) $this->post('account_id');
		$tables 		= $this->config->item('tables','ion_auth');
		$this->form_validation->set_rules('account_name', 'Account Name', 'required');
        $this->form_validation->set_rules('account_email', 'Account Email', 'required|valid_email|is_unique[' . $tables['account'] . '.account_email]');
        $this->form_validation->set_rules('account_first_name', 'Account First Name', 'required');
        $this->form_validation->set_rules('account_last_name', 'Account Last Name', 'required');
				
        ## Validate the account id.
        if ( $account_id <= 0 ){
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }
		
		if ($this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}
	
		if( isset($validation_errors) && !empty($validation_errors) ){		
			## One of the required fields is invalid
			$message = [
				'status' => FALSE,
				'message' => 'Invalid Account data: ',
				'account' => NULL	
			];
			$message['message'] = ( isset($validation_errors) && !empty($validation_errors) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => ( $this->session->flashdata('message') ) ? $this->session->flashdata('message') : 'Invalid main Account ID',
				'account' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$account = $this->account_service->get_accounts( $account_id );
		if( !$account ){
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'account' => NULL
			];
			$this->response($message, REST_Controller::HTTP_NO_CONTENT);			
		}
		
		## Run account update
		$updated_account = $this->account_service->update_account( $account_id, $account_data);
		if( !empty($updated_account) ){		
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'account' => $updated_account
			];
			$this->response($message, REST_Controller::HTTP_OK); // Resource Updated
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'account' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
    }

	/** 
	* Get list of all Accounts or single record
	*/
    public function accounts_get(){
		$account_id 	= (int) $this->get('account_id');
        $accounts = $this->account_service->get_accounts( $account_id );		
		if( !empty($accounts) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'account' => $accounts
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'account' => NULL
			];
			$this->response($message, REST_Controller::HTTP_NO_CONTENT);
		}
    }
	
	/**
	* Delete Account resource
	*/
    public function delete_get(){
        $account_id 	= (int) $this->get('account_id');
		if ( $account_id <= 0 ){
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => ( $this->session->flashdata('message') ) ? $this->session->flashdata('message') : 'Invalid main Account ID',
				'account' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$delete_account = $this->account_service->delete_account( $account_id );
		if( !empty($delete_account) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'account' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'account' => NULL
			];
			$this->response($message, REST_Controller::HTTP_NO_CONTENT);
		}
    }
	
	/** 
	* Get list of all Available/Active Modules
	*/
    public function modules_get(){
		$modules = $this->module_service->get_modules( false, true );		
		if( !empty($modules) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'modules' => $modules
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'modules' => NULL
			];
			$this->response($message, REST_Controller::HTTP_NO_CONTENT);
		}
    }
	
	/**
	* Get a list if Terms and conditions
	*/
	public function terms_and_conditions_get(){
		$terms_and_conditions = account_terms_and_conditions();		
		if( !empty($terms_and_conditions) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'ts_and_cs' => $terms_and_conditions
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'ts_and_cs' => NULL
			];
			$this->response($message, REST_Controller::HTTP_NO_CONTENT);
		}
	}
	
	/**
	* Activate New Account and admin user
	*/
	public function activate_account_post(){
		$account_id 	 = $this->post( 'account_id' );
		$activation_code = $this->post( 'activation_code' );
		if( empty( $activation_code ) && empty( $account_id ) ){
			$message = [
				'status' => false,
				'message' => $this->session->flashdata('message'),
				'account_activated' => false
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$activate_account = $this->account_service->activate_account( $account_id, $activation_code );
		
		if( !empty($activate_account) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'account_activated' => $activate_account
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'account_activated' => NULL
			];
			$this->response($message, REST_Controller::HTTP_NO_CONTENT);
		}
	}

}
