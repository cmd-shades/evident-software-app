<?php

namespace Application\Modules\Service\Controllers\Api;

class Contract extends REST_Controller {

    function __construct(){
        parent::__construct();
		$this->load->library( "Ssid_common" );
		$this->load->library( "form_validation" );
		$this->load->library( "email" );
		$this->load->model( "Contract_model", "contract_service" );
    }

	/**
	* 	Get contract Profile(s)
	*/
	public function contracts_get( $account_id = false, $contract_id = false, $where = false, $limit = 999, $offset = false ){

		$postset 		= $this->get();

		$account_id 	= ( !empty( $postset['account_id'] ) ) 	? $postset['account_id'] 	: $account_id;
		$contract_id 	= ( !empty( $postset['contract_id'] ) ) ? $postset['contract_id'] 	: $contract_id;
		$where 			= ( !empty( $postset['where'] ) ) 		? $postset['where'] 		: $where;
		$limit 			= ( !empty( $postset['limit'] ) ) 		? $postset['limit'] 		: $limit;
		$offset 		= ( !empty( $postset['offset'] ) ) 		? $postset['offset'] 		: $offset;

 		$expected_data = [
			'account_id' => $account_id
		];

		$this->form_validation->set_data( $expected_data );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid or missing Field(s)',
				'contract' 		=> NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) ? 'Validation errors: '.trim( $validation_errors ) : trim( $message['message'] );
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'contract' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$contract = $this->contract_service->get_contract( $account_id, $contract_id, $where, $limit, $offset );

		if( !empty( $contract ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'contract' 		=> $contract
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'contract' 		=> false
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/**
	* 	Add new contract Profile
	*/
	public function add_post(){

		$post_data 		= $this->post();
		$new_contract	= false;
		$account_id 	= ( !empty( $post_data['account_id'] ) ) ? ( int ) $post_data['account_id'] : false ;
		unset( $post_data['account_id'] );

        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid or missing Field(s)',
				'new_contract' 		=> NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) ? 'Validation errors: '.trim( $validation_errors ) : trim( $message['message'] );
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'new_contract' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$new_contract = $this->contract_service->add_contract( $account_id, $post_data );

		if( !empty( $new_contract ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'new_contract' 		=> $new_contract
			];

			$this->response( $message, REST_Controller::HTTP_CREATED ); // CREATED (201) being the HTTP response code
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'new_contract' 		=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}


	/**
	* 	Get contract Statuses
	*/
	public function contract_statuses_get(){

		$postset 		= $this->get();

		$account_id 	= ( !empty( $postset['account_id'] ) ) ? $postset['account_id'] : false;
		$status_id 		= ( !empty( $postset['status_id'] ) ) ? $postset['status_id'] : false;
		$ordered 		= ( !empty( $postset['ordered'] ) ) ? $postset['ordered'] : false;

 		$expected_data = [
			'account_id' 	=> $account_id ,
		];

		$this->form_validation->set_data( $expected_data );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid or missing Field(s)',
				'statuses' 		=> NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) ? 'Validation errors: '.trim( $validation_errors ) : trim( $message['message'] );
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'statuses' 	=> NULL
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$statuses = $this->contract_service->get_contract_statuses( $account_id, $status_id, $ordered );

		if( !empty( $statuses ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'statuses' 		=> $statuses
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'statuses' 		=> false
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/**
	* 	Get contract Types
	*/
	public function contract_types_get( $account_id = false, $contract_type_id = false ){

		$postset 			= $this->get();

		$account_id 		= ( !empty( $postset['account_id'] ) ) ? $postset['account_id'] : $account_id;
		$contract_type_id 	= ( !empty( $postset['contract_type_id'] ) ) ? $postset['contract_type_id'] : $contract_type_id;

 		$expected_data = [
			'account_id' 	=> $account_id ,
		];

		$this->form_validation->set_data( $expected_data );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid or missing Field(s)',
				'types' 		=> NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) ? 'Validation errors: '.trim( $validation_errors ) : trim( $message['message'] );
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'types' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$types = $this->contract_service->get_contract_types( $account_id, $contract_type_id );

		if( !empty( $types ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'types' 		=> $types
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'types' 		=> false
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/**
	* 	Update Contract Profile data
	*/
	public function update_post( $account_id = false, $contract_id = false ){

        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'contract_id', 'Contract ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid or missing Field(s)',
				'contract' 		=> NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) ? 'Validation errors: '.trim( $validation_errors ) : trim( $message['message'] );
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$postset 		= $this->post();
		$account_id 	= ( !empty( $postset['account_id'] ) ) ? $postset['account_id'] : $account_id;
		unset( $postset['account_id'] );
		$contract_id 	= ( !empty( $postset['contract_id'] ) ) ? ( int ) $postset['contract_id'] : $contract_id;
		unset( $postset['contract_id'] );

        $contract_data	= $postset;

		if ( $contract_id <= 0 ){  ## or is not a number
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST ); // BAD_REQUEST (400) being the HTTP response code
        }

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'updated_contract' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$contract = $this->contract_service->get_contract( $account_id, $contract_id );

		if( !$contract ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'updated_contract' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}


		if( !$contract_data ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'No Update Data provided',
				'updated_contract' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$updated_contract = $this->contract_service->update( $account_id, $contract_id, $contract_data );

		if( !empty( $updated_contract ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'updated_contract' 	=> $updated_contract
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'updated_contract' 	=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}


	/**
	* 	Delete Contract Profile data
	*/
	public function delete_post(){
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'contract_id', 'Contract ID', 'required' );

		if( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid or missing Field(s)',
				'profile' 		=> NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) ? 'Validation errors: '.trim( $validation_errors ) : trim( $message['message'] );
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$postset 		= $this->post();
		$account_id 	= ( !empty( $postset['account_id'] ) ) ? $postset['account_id'] : $account_id;
		unset( $postset['account_id'] );
		$contract_id 	= ( !empty( $postset['contract_id'] ) ) ? ( int ) $postset['contract_id'] : $contract_id;
		unset( $postset['contract_id'] );

        $profile_data	= $postset;

        if ( $contract_id <= 0 ){  ## or is not a number
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST ); // BAD_REQUEST (400) being the HTTP response code
        }

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'contract_deleted' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$contract = $this->contract_service->get_contract( $account_id, $contract_id );

		if( !$contract ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'contract_deleted' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$contract_deleted = $this->contract_service->delete_contract( $account_id, $contract_id );

		if( !empty( $contract_deleted ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'contract_deleted' 	=> $contract_deleted
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'contract_deleted' 	=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}

	}

	/**
	* 	Add new contract Profile
	*/
	public function add_workflow_post(){

		$post_data 		= $this->post();
		$new_workflow	= false;
		$account_id 	= ( !empty( $post_data['account_id'] ) ) ? $post_data['account_id'] : false ;
		$contract_id 	= ( !empty( $post_data['contract_id'] ) ) ? $post_data['contract_id'] : false ;
		unset( $post_data['account_id'] );

        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'contract_id', 'Contract ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid or missing Field(s)',
				'new_workflow' 		=> NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) ? 'Validation errors: '.trim( $validation_errors ) : trim( $message['message'] );
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'new_workflow' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$new_workflow = $this->contract_service->add_workflow( $account_id, $contract_id, $post_data );

		if( !empty( $new_workflow ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'new_workflow' 		=> $new_workflow
			];

			$this->response( $message, REST_Controller::HTTP_CREATED ); // CREATED (201) being the HTTP response code
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'new_workflow' 		=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}


	/**
	* 	Get contract Workflow(s)
	*/
	public function workflows_get( $account_id = false, $contract_id = false, $workflow_id = false, $where = false, $limit = 50, $offset = false ){

		$postset 		= $this->get();

 		$account_id 	= ( !empty( $postset['account_id'] ) ) ? $postset['account_id'] : $account_id;
		$contract_id 	= ( !empty( $postset['contract_id'] ) ) ? $postset['contract_id'] : $contract_id;
		$workflow_id 	= ( !empty( $postset['workflow_id'] ) ) ? $postset['workflow_id'] : $workflow_id;
		$where 			= ( !empty( $postset['where'] ) ) ? $postset['where'] : $where;
		$limit 			= ( !empty( $postset['limit'] ) ) ? $postset['limit'] : $limit;
		$offset 		= ( !empty( $postset['offset'] ) ) ? $postset['offset'] : $offset;

 		$expected_data = [
			'account_id' 	=> $account_id ,
			'contract_id' 	=> $contract_id
		];

		$this->form_validation->set_data( $expected_data );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'contract_id', 'Contract ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid or missing Field(s): '.trim( $validation_errors ),
				'workflows' 	=> NULL
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'workflows' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$workflows = $this->contract_service->get_workflows( $account_id, $workflow_id, $contract_id, $where, $limit, $offset );

		if( !empty( $workflows ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'workflows' 	=> $workflows
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'workflows' 	=> false
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}



	/**
	* 	Update Workflow item data
	*/
	public function update_workflow_post( $account_id = false, $workflow_id = false ){

        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'workflow_id', 'Workflow ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Validation errors: '.trim( $validation_errors ),
				'workflow' 		=> NULL
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$postset 		= $this->post();
		$account_id 	= ( !empty( $postset['account_id'] ) ) ? $postset['account_id'] : $account_id;
		unset( $postset['account_id'] );
		$workflow_id 	= ( !empty( $postset['workflow_id'] ) ) ? ( int ) $postset['workflow_id'] : $workflow_id;
		unset( $postset['workflow_id'] );

        $workflow_data	= $postset;

		if( !$workflow_data ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'No Update Data provided',
				'updated_workflow' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if ( $workflow_id <= 0 ){  ## or is not a number
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST ); // BAD_REQUEST (400) being the HTTP response code
        }

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'updated_workflow' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$workflow = $this->contract_service->get_workflows( $account_id, $workflow_id );

		if( !$workflow ){
			$message = [
				'status' 				=> FALSE,
				'message' 				=> $this->session->flashdata( 'message' ),
				'updated_workflow' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_NO_CONTENT );
		}

		$updated_workflow = $this->contract_service->update_workflow( $account_id, $workflow_id, $workflow_data );

		if( !empty( $updated_workflow ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'updated_workflow' 	=> $updated_workflow
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'updated_workflow' 	=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}


	/**
	* 	Delete Workflow Item data
	*/
	public function delete_workflow_post(){
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'workflow_id', 'Workflow ID', 'required' );

		if( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid or missing Field(s)',
				'workflow_deleted' 	=> NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) ? 'Validation errors: '.trim( $validation_errors ) : trim( $message['message'] );
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$postset 		= $this->post();
		$account_id 	= ( !empty( $postset['account_id'] ) ) ? $postset['account_id'] : $account_id;
		unset( $postset['account_id'] );
		$workflow_id 	= ( !empty( $postset['workflow_id'] ) ) ? ( int ) $postset['workflow_id'] : $workflow_id;
		unset( $postset['workflow_id'] );


        if ( $workflow_id <= 0 ){  ## or is not a number
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST ); // BAD_REQUEST (400) being the HTTP response code
        }

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'workflow_deleted' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$workflow = $this->contract_service->get_workflows( $account_id, $workflow_id );

		if( !$workflow ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'workflow_deleted' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$workflow_deleted = $this->contract_service->delete_workflow( $account_id, $workflow_id );

		if( !empty( $workflow_deleted ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'workflow_deleted' 	=> $workflow_deleted
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'workflow_deleted' 	=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}


	/**
	* 	Get contract Statuses
	*/
	public function wf_task_names_get(){

		$postset 		= $this->get();
		$account_id 	= ( !empty( $postset['account_id'] ) ) ? $postset['account_id'] : false;
		$wf_name_id 	= ( !empty( $postset['wf_name_id'] ) ) ? $postset['wf_name_id'] : false;

		$expected_data = [
			'account_id' => $account_id
		];

		$this->form_validation->set_data( $expected_data );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Validation errors: '.trim( $validation_errors ) ,
				'wf_task_names' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'wf_task_names' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$wf_task_names = $this->contract_service->get_wf_task_names( $account_id, $wf_name_id );

		if( !empty( $wf_task_names ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'wf_task_names' => $wf_task_names
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'wf_task_names' => false
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}




	/**
	* 	Update Workflow item data
	*/
	public function batch_workflow_update_post(){

        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 				=> FALSE,
				'message' 				=> 'Validation errors: '.trim( $validation_errors ),
				'updated_batch_wf' 		=> NULL
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$postset 		= $this->post();
		$account_id 	= ( !empty( $postset['account_id'] ) ) ? $postset['account_id'] : false ;
		unset( $postset['account_id'] );
		$batch_update 	= ( !empty( $postset['batch_update'] ) ) ? json_decode( $postset['batch_update'] ) : false;
		unset( $postset );

		if( !$batch_update ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'No Update Data provided',
				'updated_batch_wf' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'updated_batch_wf' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$updated_batch_wf = $this->contract_service->batch_workflow_update( $account_id, $batch_update );

		if( !empty( $updated_batch_wf ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'updated_batch_wf' 	=> $updated_batch_wf
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'updated_batch_wf' 	=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}




	/**
	* 	To Link Sites to the Contract
	*/
	public function link_sites_post(){

        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'contract_id', 'Contract ID', 'required' );
        $this->form_validation->set_rules( 'sites', 'Site(s)', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '' ;
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Validation errors: '.trim( $validation_errors ),
				'linked_sites' 		=> NULL
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$postset 		= $this->post();
		$account_id 	= ( !empty( $postset['account_id'] ) ) ? $postset['account_id'] : false ;
		unset( $postset['account_id'] );
		$contract_id 	= ( !empty( $postset['contract_id'] ) ) ? $postset['contract_id'] : false ;
		unset( $postset['contract_id'] );
		$sites_data 	= ( !empty( $postset['sites'] ) ) ? $postset['sites'] : false ;
		unset( $postset );

		if( !$sites_data ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'No Update Data provided',
				'linked_sites' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'linked_sites' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$linked_sites = $this->contract_service->link_sites_to_contract( $account_id, $contract_id, $sites_data );

		if( !empty( $linked_sites ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'linked_sites' 		=> $linked_sites
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'linked_sites' 		=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}

	/**
	* 	Get linked sites(s) for specific account and contract
	*/
	public function linked_sites_get(){

		$postset 		= $this->get();
 		$account_id 	= ( !empty( $postset['account_id'] ) ) ? $postset['account_id'] : false ;
		$contract_id 	= ( !empty( $postset['contract_id'] ) ) ? $postset['contract_id'] : false ;
		$where 			= ( !empty( $postset['where'] ) ) ? $postset['where'] : false ;
		$limit 			= ( !empty( $postset['limit'] ) ) ? $postset['limit'] : 1000 ;
		$offset 		= ( !empty( $postset['offset'] ) ) ? $postset['offset'] : false ;

 		$expected_data = [
			'account_id' 	=> $account_id ,
			'contract_id' 	=> $contract_id
		];

		$this->form_validation->set_data( $expected_data );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'contract_id', 'Contract ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid or missing Field(s): '.trim( $validation_errors ),
				'linked_sites_data' 	=> NULL
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'linked_sites_data' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$linked_sites_data = $this->contract_service->get_linked_sites( $account_id, $contract_id, $where, $limit, $offset );

		if( !empty( $linked_sites_data ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'linked_sites_data' 	=> $linked_sites_data
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'linked_sites_data' 	=> false
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/*
	*	Search Contract by: contract_name, contract_ref, first_name, last_name 
	*	and filter by contract_statuses, contract_types
	*/
	public function lookup_get(){
		$dataset 			= $this->get();

		$account_id 		= ( !empty( $dataset['account_id'] ) ) ? ( int ) $dataset['account_id'] : false ;
		$where 		 		= ( !empty( $dataset['where'] ) ) ? $dataset['where'] : false ;
		$order_by    		= ( !empty( $dataset['order_by'] ) ) ? $dataset['order_by'] : false ;
		$limit 		 		= ( !empty( $dataset['limit'] ) ) ? ( int ) $dataset['limit'] : false ;
		$offset 	 		= ( !empty( $dataset['offset'] ) ) ? ( int ) $dataset['offset'] : false ;
		$contract_statuses 	= ( !empty( $dataset['contract_statuses'] ) ) ? $dataset['contract_statuses'] : false ;
		$contract_types 	= ( !empty( $dataset['contract_types'] ) ) ? $dataset['contract_types'] : false ;
		$search_term 		= ( !empty( $dataset['search_term'] ) ) ? trim( urldecode( $dataset['search_term'] ) ) : false;

 		$expected_data = [
			'account_id' 	=> $account_id,
		];

		$this->form_validation->set_data( $expected_data );
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid or missing Field(s)',
				'contracts' 	=> NULL,
				'counters' 		=> NULL
			];
			$message['message'] =  trim( $message['message'] ).trim( $validation_errors );
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> ( $this->session->flashdata( 'message' ) ) ? $this->session->flashdata( 'message' ) : 'Invalid main Account ID',
				'contracts' => NULL,
				'counters' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$get_contracts = $this->contract_service->contract_lookup( $account_id, $search_term, $contract_statuses, $contract_types, $where, $order_by, $limit, $offset );

		if( !empty( $get_contracts ) ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'contracts' => ( !empty( $get_contracts->records ) ) 	? $get_contracts->records : ( !empty( $get_contracts ) ? $get_contracts: null ),
				'counters' 	=> ( !empty( $get_contracts->counters ) ) 	? $get_contracts->counters : null,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'contracts' => NULL,
				'counters' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	
	/**
	* 	Unlink The Site from the Contract data
	*/
	public function unlink_site_post(){
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'contract_id', 'Contract ID', 'required' );
        $this->form_validation->set_rules( 'site_id', 'Site ID', 'required' );

		if( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid or missing Field(s)',
				'unlinked_site' 	=> NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) ? 'Validation errors: '.trim( $validation_errors ) : trim( $message['message'] );
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$postset 		= $this->post();
		$account_id 	= ( !empty( $postset['account_id'] ) ) ? $postset['account_id'] : false;
		unset( $postset['account_id'] );
		$contract_id 	= ( !empty( $postset['contract_id'] ) ) ? ( int ) $postset['contract_id'] : false;
		unset( $postset['contract_id'] );
		$site_id 	= ( !empty( $postset['site_id'] ) ) ? ( int ) $postset['site_id'] : false;
		unset( $postset['site_id'] );

        if ( $site_id <= 0 ){  ## or is not a number
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST ); // BAD_REQUEST (400) being the HTTP response code
        }

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'unlinked_site' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$linked_contract = $this->contract_service->get_contract( $account_id, $contract_id );

		if( !$linked_contract ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'unlinked_site' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$unlinked_site = $this->contract_service->unlink_site_from_contract( $account_id, $contract_id, $site_id );

		if( !empty( $unlinked_site ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'unlinked_site' 	=> $unlinked_site
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'unlinked_site' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/**
	* 	Get Quick Stats with very small portion of data
	*/
	public function quick_stats_get(){

		$postset 		= $this->get();

 		$account_id 	= ( !empty( $postset['account_id'] ) ) ? $postset['account_id'] : false ;
		$where 			= ( !empty( $postset['where'] ) ) ? $postset['where'] : false ;
		$limit 			= ( !empty( $postset['limit'] ) ) ? $postset['limit'] : 100 ;
		$offset 		= ( !empty( $postset['offset'] ) ) ? $postset['offset'] : false ;

 		$expected_data = [
			'account_id' 	=> $account_id
		];

		$this->form_validation->set_data( $expected_data );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid or missing Field(s): '.trim( $validation_errors ),
				'quick_stats' 		=> NULL
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'quick_stats' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$quick_stats = $this->contract_service->get_quick_stats( $account_id, $where, $limit, $offset );

		if( !empty( $quick_stats ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'quick_stats' 		=> $quick_stats
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'quick_stats' 		=> false
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	/** 
	* Get list of all asset linked to a contract
	*/
	public function linked_assets_get(){

		$postdata 		= $this->get();
 		$account_id 	= !empty( $this->get( 'account_id' ) ) 	? ( int ) $this->get( 'account_id' ) : false ;
		$contract_id	= !empty( $this->get( 'contract_id' ) ) ? ( int ) $this->get( 'contract_id' ) : false ;
		$asset_id		= !empty( $this->get( 'asset_id' ) ) 	? ( int ) $this->get( 'asset_id' ) : false ;
		$where 			= !empty( $this->get( 'where' ) )		? $this->get( 'where' ) : false ;
		$limit 			= !empty( $this->get( 'limit' )	)		? (int) $this->get( 'limit' ) : DEFAULT_LIMIT;
		$offset 		= !empty( $this->get( 'offset' ) )		? (int) $this->get( 'offset' ): 0;

		$this->form_validation->set_data( [
			'account_id' => $account_id
		] );
		
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );        

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}
		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid or missing Field(s): '.trim( $validation_errors ),
				'linked_assets' 	=> NULL
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}


		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'linked_assets' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$linked_assets = $this->contract_service->get_linked_assets( $account_id, $contract_id, $asset_id, $where, $limit, $offset );

		if( !empty( $linked_assets ) ){
			$message = [
				'status'		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'linked_assets'	=> $linked_assets
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'linked_assets' => false
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/** Get Consumed Items **/
	public function consumed_items_get(){
		$account_id	= (int) $this->get( 'account_id' );
		$contract_id= (int) $this->get( 'contract_id' );
		$item_type 	= $this->get( 'item_type' );
		$grouped 	= $this->get( 'grouped' );

		$this->form_validation->set_data( ['account_id'=>$account_id, 'contract_id'=>$contract_id ] );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'contract_id', 'Contract ID', 'required' );

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> ( $this->session->flashdata( 'message' ) ) ? $this->session->flashdata( 'message' ) : 'Invalid main Account ID',
				'consumed_items'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$consumed_items 	= $this->contract_service->get_contract_consumed_items( $account_id, $contract_id, $item_type, $grouped );
		
		if( !empty( $consumed_items ) ){
			$message = [
				'status' 		=> TRUE,
				'http_code' 	=> REST_Controller::HTTP_OK,
				'message' 		=> $this->session->flashdata( 'message' ),
				'consumed_items'=> $consumed_items
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'message' 		=> 'No data found',
				'consumed_items'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/** Link people to a Contract **/
	public function link_people_post(){
		$postdata 		= $this->post();
		$account_id 	= !empty( $this->post( 'account_id' ) ) 		? ( int ) $this->post( 'account_id' ) 	: false;
		$contract_id= !empty( $this->post( 'contract_id' ) ) 	? ( int ) $this->post( 'contract_id' ) 	: false;

		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'contract_id', 'Contract ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid request data: ',
				'people' 	=> NULL	
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message'	=> 'Invalid main Account ID',
				'people' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$linked_people = $this->contract_service->link_people( $account_id, $contract_id, $postdata );
		
		if( !empty( $linked_people ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_CREATED,
				'message' 	=> $this->session->flashdata( 'message' ),
				'people' 	=> $linked_people
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); 
		}else{
			$message = [
				'status'	=> FALSE,
				'http_code' => REST_Controller::HTTP_NOT_MODIFIED,
				'message'	=> $this->session->flashdata( 'message' ),
				'people'	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	
	/** Get a list of linked people **/
	public function linked_people_get(){
		
		$account_id		= !empty( $this->get( 'account_id' ) ) 	? (int) $this->get( 'account_id' ) 		: false;
		$person_id 		= !empty( $this->get( 'person_id' ) ) 	? (int) $this->get( 'person_id' ) 		: false;
		$contract_id 	= !empty( $this->get( 'contract_id' ) ) ? (int) $this->get( 'contract_id' ) : false;
		$where 			= !empty( $this->get( 'where' ) ) 		? $this->get( 'where' ) : false;

		$this->form_validation->set_data( ['account_id'=>$account_id, /*'contract_id'=>$contract_id*/ ] );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> ( $this->session->flashdata('message' ) ) ? $this->session->flashdata('message' ) : 'Invalid main Account ID',
				'people'	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$linked_people 	= $this->contract_service->get_linked_people( $account_id, $contract_id, $person_id,$where );
		
		if( !empty( $linked_people ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata('message' ),
				'people'	=> $linked_people
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> 'Linked people not found',
				'people'	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/** Remove a Person from a Contract **/
	public function unlink_people_post(){
		$postdata 	= $this->post();
		$account_id = !empty( $this->post( 'account_id' ) ) 	? ( int ) $this->post( 'account_id' ) 	: false;
		$contract_id= !empty( $this->post( 'contract_id' ) ) 	? ( int ) $this->post( 'contract_id' ) 	: false;
		
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'contract_id', 'Contract ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_BAD_REQUEST,
				'message'	=> 'Invalid request data: ',
				'people' 	=> NULL	
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message'	=> 'Invalid main Account ID',
				'people' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$unlink_people = $this->contract_service->unlink_people( $account_id, $contract_id, $postdata );
		
		if( !empty( $unlink_people ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message'	=> $this->session->flashdata( 'message' ),
				'people' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_BAD_REQUEST,
				'message'	=> $this->session->flashdata( 'message' ),
				'people' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	
	/** 
	*	Get Consumed Items 
	**/
	public function consumed_items_export_get(){
		$account_id		= (int) $this->get( 'account_id' );
		$contract_id	= (int) $this->get( 'contract_id' );
		$item_type 		= $this->get( 'item_type' );
		$grouped 		= $this->get( 'grouped' );
		$where			= $this->get( 'where' );

		$this->form_validation->set_data( ['account_id'=>$account_id, 'contract_id'=>$contract_id ] );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'contract_id', 'Contract ID', 'required' );

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> ( $this->session->flashdata( 'message' ) ) ? $this->session->flashdata( 'message' ) : 'Invalid main Account ID',
				'consumed_items'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$consumed_items 	= $this->contract_service->get_contract_consumed_items_export( $account_id, $contract_id, $item_type, $grouped, $where );
		
		if( !empty( $consumed_items ) ){
			$message = [
				'status' 		=> TRUE,
				'http_code' 	=> REST_Controller::HTTP_OK,
				'message' 		=> $this->session->flashdata( 'message' ),
				'consumed_items'=> $consumed_items
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'message' 		=> 'No data found',
				'consumed_items'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/**
	* Search through list of Contract Buildings
	*/
	public function contract_buildings_get(){
		$account_id 	= ( int ) $this->get( 'account_id' );
		$contract_id 	= ( int ) $this->get( 'contract_id' );
		$limit 		 	= ( !empty( $this->get( 'limit' ) ) )  ? (int) $this->get( 'limit' )  : DEFAULT_LIMIT;
		$offset 	 	= ( !empty( $this->get( 'offset' ) ) ) ? (int) $this->get( 'offset' ) : DEFAULT_OFFSET;
		$where 		 	= $this->get( 'where' );
		$order_by    	= $this->get( 'order_by' );
		$search_term 	= trim( urldecode( $this->get( 'search_term' ) ) );

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [	
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID.',
				'buildings' => NULL,
				'counters' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$contract_buildings = $this->contract_service->contract_buildings_lookup( $account_id, $contract_id, $search_term, $where, $order_by, $limit, $offset );

		if( !empty( $contract_buildings ) ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'buildings' => ( !empty( $contract_buildings->records ) ) ? $contract_buildings->records : null,
				'counters' 	=> ( !empty( $contract_buildings->counters ) ) ? $contract_buildings->counters : null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'buildings' => ( !empty( $contract_buildings->records ) ) ? $contract_buildings->records : null,
				'counters' 	=> ( !empty( $contract_buildings->counters ) ) ? $contract_buildings->counters : null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
}