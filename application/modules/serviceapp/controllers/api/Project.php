<?php

namespace Application\Modules\Service\Controllers\Api;

class Project extends REST_Controller {

    function __construct(){
        parent::__construct();
		$this->load->library( "Ssid_common" );
		$this->load->library( "form_validation" );
		$this->load->library( "email" );
		$this->load->model( "Project_model", "project_service" );
    }

	
	/**
	* 	Get project Profile(s)
	*/
	public function projects_get(){

		$postset 		= $this->get();

		$account_id 	= ( !empty( $postset['account_id'] ) ) ? $postset['account_id'] : false;
		$project_id 	= ( !empty( $postset['project_id'] ) ) ? $postset['project_id'] : false;
		$search_term	= ( !empty( $this->get( 'search_term' ) ) ) ?  trim( urldecode( $this->get( 'search_term' ) ) ) : false ;
		$where 			= ( !empty( $postset['where'] ) ) ? $postset['where'] : false;
		$order_by 		= ( !empty( $postset['order_by'] ) ) ? $postset['order_by'] : false;
		$limit 			= ( !empty( $postset['limit'] ) ) ? $postset['limit'] : DEFAULT_LIMIT;
		$offset 		= ( !empty( $postset['offset'] ) ) ? $postset['offset'] : DEFAULT_OFFSET;

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
				'status' 	=> FALSE,
				'http_code' => REST_Controller::BAD_REQUEST,
				'message' 	=> 'Invalid or missing Field(s)',
				'projects' 	=> NULL,
				'counters' 	=> NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) ? 'Validation errors: '.trim( $validation_errors ) : trim( $message['message'] );
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> $this->session->flashdata( 'message' ),
				'projects' 	=> NULL,
				'counters' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$projects = $this->project_service->get_projects( $account_id, $project_id, $search_term, $where, $order_by, $limit, $offset );

		if( !empty( $projects ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code'	=> REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'projects' 	=> ( !empty( $projects->records ) ) 	? $projects->records : ( !empty( $projects ) ? $projects: null ),
				'counters' 	=> ( !empty( $projects->counters ) ) 	? $projects->counters : null,
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 	=> FALSE,
				'http_code'	=> REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> $this->session->flashdata( 'message' ),
				'projects' 	=> false,
				'counters' 	=> NULL
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/**
	* Create new Project resource 
	*/
	public function create_post(){
		
		$project_data = $this->post();
		$account_id	   = (int)$this->post('account_id');
		
		$this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
		$this->form_validation->set_rules('project_name', 'Project Name', 'required');
               
		if ($this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}
			
		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){		
			## One of the required fields is invalid
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_BAD_REQUEST,
				'message' 	=> 'Invalid Project data: ',
				'project' 	> NULL	
			];
			$message['message'] = (!$account_id)? $message['message'].'account_id, ': $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		//Check and verify that main acocount is valid
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> 'Invalid main Account ID',
				'project' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$new_project = $this->project_service->create_project( $account_id, $project_data );

		if( !empty($new_project) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata('message'),
				'project' 	=> $new_project
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		}else{
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> $this->session->flashdata('message'),
				'project' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
    }


	/**
	* 	Get project Types
	*/
	public function project_types_get( $account_id = false, $project_type_id = false ){

		$postset 			= $this->get();

		$account_id 		= ( !empty( $postset['account_id'] ) ) ? $postset['account_id'] : $account_id;
		$project_type_id 	= ( !empty( $postset['project_type_id'] ) ) ? $postset['project_type_id'] : $project_type_id;

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
				'project_types' 		=> NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) ? 'Validation errors: '.trim( $validation_errors ) : trim( $message['message'] );
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'project_types' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$project_types = $this->project_service->get_projects_types( $account_id, $project_type_id );

		if( !empty( $project_types ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'project_types' => $project_types
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'project_types' => false
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/**
	* 	Update Project Profile data
	*/
	public function update_post(){

		$postdata		= $this->post();
		$account_id 	= ( !empty( $postdata['account_id'] ) ) ? $postdata['account_id'] : false;
		$project_id 	= ( !empty( $postdata['project_id'] ) ) ? ( int ) $postdata['project_id'] : false;

        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'project_id', 'Project ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> 'Invalid or missing Field(s)',
				'project' 		=> NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) ? 'Validation errors: '.trim( $validation_errors ) : trim( $message['message'] );
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if ( $project_id <= 0 ){  ## or is not a number
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST );
        }

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> $this->session->flashdata( 'message' ),
				'project' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$project = $this->project_service->get_projects( $account_id, $project_id );

		if( !$project ){
			$message = [
				'status' 	=> FALSE,
				'http_code'	=> REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> $this->session->flashdata( 'message' ),
				'project' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$updated_project = $this->project_service->update( $account_id, $project_id, $postdata );

		if( !empty( $updated_project ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'project' 	=> $updated_project
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NOT_MODIFIED,
				'message' 	=> $this->session->flashdata( 'message' ),
				'project' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/**
	* Archive Project resource
	*/
    public function archive_project_post(){
		
		$postdata 					= $this->post();
        $account_id 				= ( !empty( $postdata['account_id'] ) ) ? (int) $postdata['account_id'] : false ;
        $project_id 				= ( !empty( $postdata['project_id'] ) ) ? (int) $postdata['project_id'] : false ;
		
		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'project_id', 'Project ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}
	
		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $validation_errors,
				'project' 	=> NULL 	
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID.',
				'project' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$project = $this->project_service->archive_project( $account_id, $project_id );
		
		if( !empty( $project ) ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'project' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'project' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
	
	/**
	* 	Get project Statuses
	*/
	public function project_action_types_get(){

		$postset 		= $this->get();
		$account_id 	= ( !empty( $postset['account_id'] ) ) ? $postset['account_id'] : false;
		$project_action_name 	= ( !empty( $postset['project_action_name'] ) ) ? $postset['project_action_name'] : false;

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
				'project_action_types' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'project_action_types' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$project_action_types = $this->project_service->get_projects_task_names( $account_id, $project_action_name );

		if( !empty( $project_action_types ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'project_action_types' => $project_action_types
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'project_action_types' => false
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	

	/**
	* Get list of Project types
	*/
	public function project_statuses_get(){
		$account_id   			= $this->get( 'account_id' );
		$project_status_id    	= $this->get( 'project_status_id' );
		$project_status_group 	= $this->get( 'project_project_status_group' );
		$grouped 				= $this->get( 'grouped' );
		$project_statuses 		= $this->project_service->get_project_statuses( $account_id, $project_status_id, $project_status_group, $grouped );

		if( !empty( $project_statuses ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> 'Project statuses data found',
				'project_statuses' 	=> $project_statuses
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'No records found',
				'project_statuses' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_NO_CONTENT );
		}
	}
	

	/*
	*	Search Project by: project_name, project_ref, first_name, last_name 
	*	and filter by project_statuses, project_types
	*/
	public function lookup_get(){
		$dataset 			= $this->get();

		$account_id 		= ( !empty( $dataset['account_id'] ) ) ? ( int ) $dataset['account_id'] : false ;
		$where 		 		= ( !empty( $dataset['where'] ) ) ? $dataset['where'] : false ;
		$order_by    		= ( !empty( $dataset['order_by'] ) ) ? $dataset['order_by'] : false ;
		$limit 		 		= ( !empty( $dataset['limit'] ) ) ? ( int ) $dataset['limit'] : false ;
		$offset 	 		= ( !empty( $dataset['offset'] ) ) ? ( int ) $dataset['offset'] : false ;
		$project_statuses 	= ( !empty( $dataset['project_statuses'] ) ) ? $dataset['project_statuses'] : false ;
		$project_types 	= ( !empty( $dataset['project_types'] ) ) ? $dataset['project_types'] : false ;
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
				'projects' 	=> NULL
			];
			$message['message'] =  trim( $message['message'] ).trim( $validation_errors );
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> ( $this->session->flashdata( 'message' ) ) ? $this->session->flashdata( 'message' ) : 'Invalid main Account ID',
				'projects' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$get_projectss = $this->project_service->project_lookup( $account_id, $search_term, $project_statuses, $project_types, $where, $order_by, $limit, $offset );

		if( !empty( $get_projectss ) ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'projects' => $get_projectss
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'projects' => NULL
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

		$quick_stats = $this->project_service->get_quick_stats( $account_id, $where, $limit, $offset );

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
	
	
	/** Create Project Action record(s) **/
	public function create_project_action_post(){
		$postdata 		= $this->post();
		$account_id 	= !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;
		$project_id 	= !empty( $this->post( 'project_id' ) ) ? ( int ) $this->post( 'project_id' ) 	: false;
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'project_id', 'Project ID', 'required' );
		
		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_BAD_REQUEST,
				'message' 			=> 'Invalid request data: ',
				'project_action'	=> NULL	
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 			=> 'Invalid main Account ID',
				'project_action' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$project_actions = $this->project_service->create_project_action( $account_id, $project_id, $postdata );
		
		if( !empty( $project_actions ) ){
			$message = [
				'status' 			=> TRUE,
				'http_code' 		=> REST_Controller::HTTP_CREATED,
				'message' 			=> $this->session->flashdata( 'message' ),
				'project_action' 	=> $project_actions
			];
			$this->response( $message, REST_Controller::HTTP_OK ); 
		}else{
			$message = [
				'status'			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_BAD_REQUEST,
				'message' 			=> $this->session->flashdata( 'message' ),
				'project_action' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	
	/** Update / Edit Project Action record **/
	public function update_project_action_post(){
		$postdata 	= $this->post();
		$account_id = !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;
		$project_action_id= !empty( $this->post( 'project_action_id' ) ) ? ( int ) $this->post( 'project_action_id' ) : false;

		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			$message = [
				'status'  		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
				'message' 		=> 'Invalid request data: ',
				'project_action'=> NULL	
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> 'Invalid main Account ID',
				'project_action'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$project_action = $this->project_service->update_project_action( $account_id, $project_action_id, $postdata );
		
		if( !empty( $project_action ) ){
			$message = [
				'status' 		=> TRUE,
				'http_code' 	=> REST_Controller::HTTP_OK,
				'message' 		=> $this->session->flashdata( 'message' ),
				'project_action'=> $project_action
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); 
		}else{
			$message = [
				'status'			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_BAD_REQUEST,
				'message' 			=> $this->session->flashdata( 'message' ),
				'project_action' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	
	/** Get list of Project Actions **/
	public function project_actions_get(){

		$account_id 	= ( !empty( $this->get( 'account_id' ) ) ) 	? (int) $this->get( 'account_id' ) : false ;
		$search_term	= ( !empty( $this->get( 'search_term' ) ) ) ?  trim( urldecode( $this->get( 'search_term' ) ) ) : false ;
		$where 		 	= ( !empty( $this->get( 'where' ) ) ) 		? $this->get( 'where' ) : false ;
		$order_by 		= ( !empty( $this->get( 'order_by' ) ) ) 	? $this->get( 'order_by' ) : false ;
		$limit		 	= ( $this->get( 'limit' ) ) 				? (int) $this->get( 'limit' ) : DEFAULT_LIMIT;
		$offset	 		= ( !empty( $this->get( 'offset' ) ) ) 		? (int) $this->get( 'offset' ) : 0 ;

		$this->form_validation->set_data( [ 'account_id'=>$account_id ] );
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
				'message' 			=> 'Invalid data: ',
				'project_actions' 	=> NULL
			];

			$message['message'] = (!$account_id)? $message['message'].'account_id, ': $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 			=> 'Invalid main Account ID.',
				'project_actions' 	=> NULL,
				'counters' 			=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$project_actions = $this->project_service->get_project_actions( $account_id, $search_term, $where, $order_by, $limit, $offset );

		if( !empty( $project_actions ) ){
			$message = [
				'status' 			=> TRUE,
				'http_code' 		=> REST_Controller::HTTP_OK,
				'message' 			=> $this->session->flashdata( 'message' ),
				'project_actions' 	=> ( !empty( $project_actions->records ) )  ? $project_actions->records : ( !empty( $project_actions ) ? $project_actions: null ),
				'counters' 			=> ( !empty( $project_actions->counters ) ) ? $project_actions->counters : null,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
				'message'			=> 'No project actions data found',
				'project_actions' 	=> null,
				'counters' 			=> null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/** Delete an existing Project Action record **/
	public function delete_project_action_post(){
		$postdata 	= $this->post();
		$account_id = !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;
		
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
				'message'		=> 'Invalid request data: ',
				'project_action'=> NULL	
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> 'Invalid main Account ID',
				'project_action'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$project_action = $this->project_service->delete_project_action( $account_id, $postdata );
		
		if( !empty( $project_action ) ){
			$message = [
				'status' 		=> TRUE,
				'http_code' 	=> REST_Controller::HTTP_OK,
				'message' 		=> $this->session->flashdata( 'message' ),
				'project_action'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		}else{
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> $this->session->flashdata( 'message' ),
				'project_action'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}

	
	/** 
	* Create Project Workflow record(s) 
	**/
	public function create_project_workflow_post(){
		$postdata 		= $this->post();
		$account_id 	= !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;
		$project_id 	= !empty( $this->post( 'project_id' ) ) ? ( int ) $this->post( 'project_id' ) 	: false;
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'project_id', 'Project ID', 'required' );
		
		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_BAD_REQUEST,
				'message' 			=> 'Invalid request data: ',
				'project_workflow'	=> NULL	
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 			=> 'Invalid main Account ID',
				'project_workflow' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$project_workflows = $this->project_service->create_project_workflow( $account_id, $project_id, $postdata );
		
		if( !empty( $project_workflows ) ){
			$message = [
				'status' 			=> TRUE,
				'http_code' 		=> REST_Controller::HTTP_CREATED,
				'message' 			=> $this->session->flashdata( 'message' ),
				'project_workflow' 	=> $project_workflows
			];
			$this->response( $message, REST_Controller::HTTP_OK ); 
		}else{
			$message = [
				'status'			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_BAD_REQUEST,
				'message' 			=> $this->session->flashdata( 'message' ),
				'project_workflow' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	
	/** 
	*Update / Edit Project Workflow record 
	**/
	public function update_project_workflow_post(){
		$postdata 	= $this->post();
		$account_id = !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;
		$workflow_id= !empty( $this->post( 'workflow_id' ) ) ? ( int ) $this->post( 'workflow_id' ) : false;

		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			$message = [
				'status'  		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
				'message' 		=> 'Invalid request data: ',
				'project_workflow'=> NULL	
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> 'Invalid main Account ID',
				'project_workflow'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$project_workflow = $this->project_service->update_project_workflow( $account_id, $workflow_id, $postdata );
		
		if( !empty( $project_workflow ) ){
			$message = [
				'status' 		=> TRUE,
				'http_code' 	=> REST_Controller::HTTP_OK,
				'message' 		=> $this->session->flashdata( 'message' ),
				'project_workflow'=> $project_workflow
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); 
		}else{
			$message = [
				'status'			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_BAD_REQUEST,
				'message' 			=> $this->session->flashdata( 'message' ),
				'project_workflow' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	
	/** 
	* Get list of Project Workflows 
	**/
	public function project_workflows_get(){

		$account_id 	= ( !empty( $this->get( 'account_id' ) ) ) 	? (int) $this->get( 'account_id' ) : false ;
		$search_term	= ( !empty( $this->get( 'search_term' ) ) ) ?  trim( urldecode( $this->get( 'search_term' ) ) ) : false ;
		$where 		 	= ( !empty( $this->get( 'where' ) ) ) 		? $this->get( 'where' ) : false ;
		$order_by 		= ( !empty( $this->get( 'order_by' ) ) ) 	? $this->get( 'order_by' ) : false ;
		$limit		 	= ( $this->get( 'limit' ) ) 				? (int) $this->get( 'limit' ) : DEFAULT_LIMIT;
		$offset	 		= ( !empty( $this->get( 'offset' ) ) ) 		? (int) $this->get( 'offset' ) : 0 ;

		$this->form_validation->set_data( [ 'account_id'=>$account_id ] );
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
				'message' 			=> 'Invalid data: ',
				'project_workflows' => NULL
			];

			$message['message'] = (!$account_id)? $message['message'].'account_id, ': $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 			=> 'Invalid main Account ID.',
				'project_workflows' => NULL,
				'counters' 			=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$project_workflows = $this->project_service->get_project_workflows( $account_id, $search_term, $where, $order_by, $limit, $offset );

		if( !empty( $project_workflows ) ){
			$message = [
				'status' 			=> TRUE,
				'http_code' 		=> REST_Controller::HTTP_OK,
				'message' 			=> $this->session->flashdata( 'message' ),
				'project_workflows' => ( !empty( $project_workflows->records ) )  ? $project_workflows->records : ( !empty( $project_workflows ) ? $project_workflows: null ),
				'counters' 			=> ( !empty( $project_workflows->counters ) ) ? $project_workflows->counters : null,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
				'message'			=> 'No project actions data found',
				'project_workflows' => null,
				'counters' 			=> null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/** 
	* Delete an existing Project Workflow record 
	**/
	public function delete_project_workflow_post(){
		$postdata 	= $this->post();
		$account_id = !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;
		
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
				'message'		=> 'Invalid request data: ',
				'project_workflow'=> NULL	
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> 'Invalid main Account ID',
				'project_workflow'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$project_workflow = $this->project_service->delete_project_workflow( $account_id, $postdata );
		
		if( !empty( $project_workflow ) ){
			$message = [
				'status' 		=> TRUE,
				'http_code' 	=> REST_Controller::HTTP_OK,
				'message' 		=> $this->session->flashdata( 'message' ),
				'project_workflow'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		}else{
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> $this->session->flashdata( 'message' ),
				'project_workflow'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	
	/** 
	* Create Project Workflow record(s) 
	**/
	public function create_workflow_entry_post(){
		$postdata 		= $this->post();
		$account_id 	= !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;
		$workflow_id 	= !empty( $this->post( 'workflow_id' ) ) ? ( int ) $this->post( 'workflow_id' ) 	: false;
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'project_id', 'Project ID', 'required' );
		$this->form_validation->set_rules( 'workflow_id', 'Workflow ID', 'required' );
		
		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_BAD_REQUEST,
				'message' 			=> 'Invalid request data: ',
				'workflow_entry'	=> NULL	
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 			=> 'Invalid main Account ID',
				'workflow_entry' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$workflow_entries = $this->project_service->create_workflow_entry( $account_id, $workflow_id, $postdata );
		
		if( !empty( $workflow_entries ) ){
			$message = [
				'status' 			=> TRUE,
				'http_code' 		=> REST_Controller::HTTP_CREATED,
				'message' 			=> $this->session->flashdata( 'message' ),
				'workflow_entry' 	=> $workflow_entries
			];
			$this->response( $message, REST_Controller::HTTP_OK ); 
		}else{
			$message = [
				'status'			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_BAD_REQUEST,
				'message' 			=> $this->session->flashdata( 'message' ),
				'workflow_entry' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	
	/** 
	*Update / Edit Project Workflow record 
	**/
	public function update_workflow_entry_post(){
		$postdata 	= $this->post();
		$account_id = !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;
		$entry_id	= !empty( $this->post( 'entry_id' ) ) ? ( int ) $this->post( 'entry_id' ) : false;

		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'workflow_id', 'Workflow ID', 'required' );
		$this->form_validation->set_rules( 'entry_id', 'Entry ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			$message = [
				'status'  		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
				'message' 		=> 'Invalid request data: ',
				'workflow_entry'=> NULL	
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> 'Invalid main Account ID',
				'workflow_entry'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$workflow_entry = $this->project_service->update_workflow_entry( $account_id, $entry_id, $postdata );
		
		if( !empty( $workflow_entry ) ){
			$message = [
				'status' 		=> TRUE,
				'http_code' 	=> REST_Controller::HTTP_OK,
				'message' 		=> $this->session->flashdata( 'message' ),
				'workflow_entry'=> $workflow_entry
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); 
		}else{
			$message = [
				'status'			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_BAD_REQUEST,
				'message' 			=> $this->session->flashdata( 'message' ),
				'workflow_entry' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	
	/** 
	* Get list of Project Workflows 
	**/
	public function workflow_entries_get(){

		$account_id 	= ( !empty( $this->get( 'account_id' ) ) ) 	? (int) $this->get( 'account_id' ) : false ;
		$search_term	= ( !empty( $this->get( 'search_term' ) ) ) ?  trim( urldecode( $this->get( 'search_term' ) ) ) : false ;
		$where 		 	= ( !empty( $this->get( 'where' ) ) ) 		? $this->get( 'where' ) : false ;
		$order_by 		= ( !empty( $this->get( 'order_by' ) ) ) 	? $this->get( 'order_by' ) : false ;
		$limit		 	= ( $this->get( 'limit' ) ) 				? (int) $this->get( 'limit' ) : DEFAULT_LIMIT;
		$offset	 		= ( !empty( $this->get( 'offset' ) ) ) 		? (int) $this->get( 'offset' ) : 0 ;

		$this->form_validation->set_data( [ 'account_id'=>$account_id ] );
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
				'message' 			=> 'Invalid data: ',
				'workflow_entries' => NULL
			];

			$message['message'] = (!$account_id)? $message['message'].'account_id, ': $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 			=> 'Invalid main Account ID.',
				'workflow_entries' => NULL,
				'counters' 			=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$workflow_entries = $this->project_service->get_workflow_entries( $account_id, $search_term, $where, $order_by, $limit, $offset );

		if( !empty( $workflow_entries ) ){
			$message = [
				'status' 			=> TRUE,
				'http_code' 		=> REST_Controller::HTTP_OK,
				'message' 			=> $this->session->flashdata( 'message' ),
				'workflow_entries' 	=> ( !empty( $workflow_entries->records ) )  ? $workflow_entries->records : ( !empty( $workflow_entries ) ? $workflow_entries: null ),
				'counters' 			=> ( !empty( $workflow_entries->counters ) ) ? $workflow_entries->counters : null,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
				'message'			=> 'No Workflow entries data found',
				'workflow_entries' 	=> null,
				'counters' 			=> null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/** 
	* Delete an existing Project Workflow entry 
	**/
	public function delete_workflow_entry_post(){
		$postdata 	= $this->post();
		$account_id = !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;
		
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
				'message'		=> 'Invalid request data: ',
				'workflow_entry'=> NULL	
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> 'Invalid main Account ID',
				'workflow_entry'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$workflow_entry = $this->project_service->delete_workflow_entry( $account_id, $postdata );
		
		if( !empty( $workflow_entry ) ){
			$message = [
				'status' 		=> TRUE,
				'http_code' 	=> REST_Controller::HTTP_OK,
				'message' 		=> $this->session->flashdata( 'message' ),
				'workflow_entry'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		}else{
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> $this->session->flashdata( 'message' ),
				'workflow_entry'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
}