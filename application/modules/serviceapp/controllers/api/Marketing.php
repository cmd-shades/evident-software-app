<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class Marketing extends REST_Controller {

    function __construct(){
        parent::__construct();
		$this->load->model( 'Marketing_model','marketing_service' );
    }
	
	/**
	* 	Available Sub-modules for the Marketing module
	*/
	public function modules_get(){

		$account_id		= ( int ) $this->get( 'account_id' );
		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
				'message' 		=> 'Validation errors: '.$validation_errors,
				'modules' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( ( !$account_id ) || ( !$this->account_service->check_account_status( $account_id ) ) ){
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> 'Invalid main Account ID.',
				'modules' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$modules = $this->marketing_service->get_modules( $account_id );

		if( !empty( $modules ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'modules' 	=> $modules,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> $this->session->flashdata( 'message' ),
				'modules' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
}