<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Diary extends REST_Controller {

    function __construct(){
        // Construct the parent class
        parent::__construct();
		$this->load->model( 'Diary_model', 'diary_service' );
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
		$this->lang->load('auth');
    }


	/**
	* 	Get list of all Working engineers. Returns:
	*	 - User Type - 3: Field Operative
	*	 - Home addresses
	*	 - User Slots for specific date (?)
	*/
    public function field_operatives_get(){
		$get_data 		= $this->get();

		$account_id 	=  !empty( $get_data['account_id'] ) ? ( int ) $get_data['account_id'] : false ;
		$operative_id 	=  !empty( $get_data['operative_id'] ) ? ( int ) $get_data['operative_id'] : false ;
		$where			=  !empty( $get_data['where'] ) ? $get_data['where'] : false ;
		$limit 			= ( !empty( $get_data['limit'] ) ) ? ( int ) $get_data['limit'] : false ;
		$offset 		= ( !empty( $get_data['offset'] ) ) ? ( int ) $get_data['offset'] : false ;
		$order_by 		= ( !empty( $get_data['order_by'] ) ) ? $get_data['order_by'] : false ;

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
				'status' 			=> FALSE,
				'message' 			=> 'Validation errors: '.trim( $validation_errors ),
				'field_operative' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid main Account ID.',
				'field_operative' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$field_operative = $this->diary_service->get_field_operatives( $account_id, $operative_id, $where, $limit, $offset, $order_by );

		if( !empty( $field_operative ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'field_operative' 	=> $field_operative
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'field_operative' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }


	/*
	*	This function will update the job status, order, assignee
	*/
	public function route_jobs_post(){
		$validation_errors = $post_data = false;

		$post_data 		= $this->post();

		$account_id 	= ( !empty( $post_data['account_id'] ) ) ? $post_data['account_id'] : false ;
		unset( $post_data['account_id'] );

		$job_batch 		= ( !empty( $post_data['job_batch'] ) ) ? $post_data['job_batch'] : false ;
		unset( $post_data['job_batch'] );

        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'job_batch', 'Job(s) Batch', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid or missing Field(s)',
				'routed_jobs' 	=> NULL
			];
			$message['message'] = 'Validation errors: '.trim( $validation_errors );
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$routed_jobs = $this->diary_service->route_jobs( $account_id, $job_batch );

		if( !empty( $routed_jobs ) ){
			$message = [
				'status' 		=> true,
				'message' 		=> $this->session->flashdata( 'message' ),
				'routed_jobs' 	=> $routed_jobs
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'routed_jobs' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/*
	*	This function will de-schedule the job
	*/
	public function unschedule_job_post(){
		$validation_errors = $post_data = false;

		$post_data 		= $this->post();

		$account_id 	= ( !empty( $post_data['account_id'] ) ) ? $post_data['account_id'] : false ;
		unset( $post_data['account_id'] );

		$job_id 		= ( !empty( $post_data['job_id'] ) ) ? $post_data['job_id'] : false ;
		unset( $post_data['job_id'] );

        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'job_id', 'Job ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid or missing Field(s)',
				'unscheduled_job' 	=> NULL
			];
			$message['message'] = 'Validation errors: '.trim( $validation_errors );
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$unscheduled_job = $this->diary_service->unschedule_job( $account_id, $job_id );

		if( !empty( $unscheduled_job ) ){
			$message = [
				'status' 			=> true,
				'message' 			=> $this->session->flashdata( 'message' ),
				'unscheduled_job' 	=> $unscheduled_job
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'unscheduled_job' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/** Get List of Routed Jobs **/
	public function routed_jobs_get(){
		$account_id		= !empty( $this->get( 'account_id' ) ) 	? ( int ) $this->get( 'account_id' ) : false ;
		$where			= !empty( $this->get( 'where' ) ) 		? $this->get( 'where' ) : false ;
		$limit			= !empty( $this->get( 'limit' ) ) 		? ( int ) $this->get( 'limit' ) : DEFAULT_LIMIT ;
		$offset			= !empty( $this->get( 'offset' ) ) 		? ( int ) $this->get( 'offset' ) : 0 ;
		$order_by		= !empty( $this->get( 'order_by' ) ) 	? $this->get( 'order_by' ) : false ;

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
				'message' 	=> 'Validation errors: '.trim( $validation_errors ),
				'jobs' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID.',
				'jobs' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$routed_jobs = $this->diary_service->get_routed_jobs( $account_id, $where, $limit, $offset, $order_by );

		if( !empty( $routed_jobs ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'jobs' 	=> $routed_jobs
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 	=> FALSE,
				'message'	=> $this->session->flashdata( 'message' ),
				'jobs' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/** Get List of Routed Jobs **/
	public function engineer_data_get(){
		$account_id		= !empty( $this->get( 'account_id' ) ) 	? ( int ) $this->get( 'account_id' ) : false ;
		$engineer_id	= !empty( $this->get( 'engineer_id' ) ) ? $this->get( 'engineer_id' ) : false ;

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
				'message' 		=> 'Validation errors: '.trim( $validation_errors ),
				'engineer_data' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid main Account ID.',
				'engineer_data' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$engineer_data = $this->diary_service->get_engineer_data( $account_id, $engineer_id );

		if( !empty( $engineer_data ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'engineer_data' => $engineer_data
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'engineer_data' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/** Get Diary Zones / Regions **/
	public function postcode_areas_get(){
		$account_id		= !empty( $this->get( 'account_id' ) ) 	? $this->get( 'account_id' ) : false ;
		$search_term	= !empty( $this->get( 'search_term' ) ) ? $this->get( 'search_term' ) : false ;
		$where			= !empty( $this->get( 'where' ) ) 		? $this->get( 'where' ) : false ;
		$limit			= !empty( $this->get( 'limit' ) ) 		? ( int ) $this->get( 'limit' ) : DEFAULT_LIMIT ;
		$offset			= !empty( $this->get( 'offset' ) ) 		? ( int ) $this->get( 'offset' ) : 0 ;
		$order_by		= !empty( $this->get( 'order_by' ) ) 	? $this->get( 'order_by' ) : false ;

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
				'message' 		=> 'Validation errors: '.trim( $validation_errors ),
				'postcode_areas'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID.',
				'postcode_areas' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$postcode_areas = $this->diary_service->get_postcode_areas( $account_id, $search_term, $where, $limit, $offset, $order_by );

		if( !empty( $postcode_areas ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'postcode_areas' 	=> $postcode_areas
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'postcode_areas' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/** Create a New Skill Set resource **/
	public function add_skill_post(){
		$skill_set_data 	= $this->post();
		$account_id			= (int) $this->post( 'account_id' );

		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'skill_name', 'Skill Name', 'required' );
		$this->form_validation->set_rules( 'skill_description', 'Skill Description', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> 'Invalid data: ',
				'skill' 	=> NULL,
				'exists' 	=> NULL
			];

			$message['message'] = ( !$account_id )? $message['message'].'account_id, ' : $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> 'Invalid main Account ID.',
				'skill' 	=> NULL,
				'exists' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$new_skill_set 	= $this->diary_service->add_skill_set( $account_id, $skill_set_data );
		$exists 		= $this->session->flashdata( 'already_exists' );

		if( !empty( $new_skill_set ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_CREATED,
				'message' 	=> $this->session->flashdata( 'message' ),
				'skill' 	=> $new_skill_set,
				'exists' 	=> ( !empty( $exists ) ) ? true : false
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> $this->session->flashdata('message'),
				'skill' 	=> NULL,
				'exists' 	=> ( !empty( $exists ) ) ? true : false
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/**
	* Update Skill Set record
	*/
	public function update_skill_post(){
        $skill_set_data 	= $this->post();
        $account_id 		= ( int ) $this->post('account_id');
        $skill_id 			= ( int ) $this->post('skill_id');

		$this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
		$this->form_validation->set_rules('skill_id', 'Skill Set ID', 'required');
		$this->form_validation->set_rules('skill_name', 'Skill Name', 'required');

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> 'Invalid data: ',
				'skill' 	=> NULL
			];

			$message['message'] = (!$account_id)? $message['message'].'account_id, ': $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> 'Invalid main Account ID.',
				'skill' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

        ## Validate the Skill set id.
        if ( $skill_id <= 0 ){
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST );
        }

		## Run update call
		$updated_skill_set = $this->diary_service->update_skill( $account_id, $skill_id, $skill_set_data );

		if( !empty( $updated_skill_set ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'skill' 	=> $updated_skill_set
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> $this->session->flashdata( 'message' ),
				'skill' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }

	/**
	* Get list of all Skill sets by account_id
	*/
	public function skills_get(){

		$account_id 	= ( !empty( $this->get( 'account_id' ) ) ) 	? (int) $this->get( 'account_id' ) : false ;
		$skill_id 		= ( !empty( $this->get( 'skill_id' ) ) ) 	? (int) $this->get( 'skill_id' ) : false ;
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
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> 'Invalid data: ',
				'skill' 	=> NULL
			];

			$message['message'] = (!$account_id)? $message['message'].'account_id, ': $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> 'Invalid main Account ID.',
				'skills' 	=> NULL,
				'counters' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$skills = $this->diary_service->get_skills( $account_id, $skill_id, $search_term, $where, $order_by, $limit, $offset );

		if( !empty( $skills ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'skills' 	=> ( !empty( $skills->records ) ) ? $skills->records : ( !empty( $skills ) ? $skills: null ),
				'counters' 	=> ( !empty( $skills->counters ) ) ? $skills->counters : null,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message'	=> $this->session->flashdata( 'message' ),
				'skills' 	=> null,
				'counters' 	=> null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/**
	* 	Search through diary resources
	*/
	public function resources_lookup_get(){

		$get_data = $this->get();

		$account_id 	= ( !empty( $get_data['account_id'] ) ) ? ( int ) $get_data['account_id'] 	: false;
		$limit 			= ( !empty( $get_data['limit'] ) ) 		? ( int ) $get_data['limit'] 		: DEFAULT_LIMIT;
		$offset 		= ( !empty( $get_data['offset'] ) ) 	? ( int ) $get_data['offset'] 		: DEFAULT_OFFSET ;
		$where 			= ( !empty( $get_data['where'] ) ) 		? $get_data['where'] 				: false ;
		$order_by 		= ( !empty( $get_data['order_by'] ) ) 	? $get_data['order_by'] 			: false ;
		$search_term 	= ( !empty( $get_data['search_term'] ) )? trim( urldecode( $this->get( 'search_term' ) ) ) : false;

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid main Account ID.',
				'resources' 		=> NULL,
				'available_options' => NULL,
				'counters' 			=> NULL,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$resources = $this->diary_service->resources_lookup( $account_id, $search_term, $where, $order_by, $limit, $offset );

		if( !empty( $resources ) ){
			$message = [
				'status' 				=> TRUE,
				'message' 				=> $this->session->flashdata( 'message' ),
				'resources' 			=> $resources['data'],
				'available_options' 	=> $resources['available_options'],
				'counters' 				=> $resources['counters'],
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'resources' 		=> NULL,
				'available_options' => NULL,
				'counters' 			=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/** Create a New Region resource **/
	public function add_region_post(){
		$region_data 	= $this->post();
		$account_id		= (int) $this->post( 'account_id' );
		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'region_name', 'Region Name', 'required' );
		$this->form_validation->set_rules( 'region_description', 'Region Description', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}
		
		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> 'Invalid data: ',
				'region' 	=> NULL,
				'exists' 	=> NULL
			];
			
			$message['message'] = ( !$account_id )? $message['message'].'account_id, ' : $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> 'Invalid main Account ID.',
				'region' 	=> NULL,
				'exists' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$new_region 	= $this->diary_service->add_region( $account_id, $region_data );
		$exists 		= $this->session->flashdata( 'already_exists' );
		
		if( !empty( $new_region ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_CREATED,
				'message' 	=> $this->session->flashdata( 'message' ),
				'region' 	=> $new_region,
				'exists' 	=> ( !empty( $exists ) ) ? true : false
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> $this->session->flashdata('message'),
				'region' 	=> NULL,
				'exists' 	=> ( !empty( $exists ) ) ? true : false
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	
	/**
	* Update Region record
	*/
	public function update_region_post(){
        $region_data 	= $this->post();
        $account_id 		= ( int ) $this->post('account_id');
        $region_id 			= ( int ) $this->post('region_id');

		$this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
		$this->form_validation->set_rules('region_id', 'Region ID', 'required');
		$this->form_validation->set_rules('region_name', 'Region Name', 'required');		
		
		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> 'Invalid data: ',
				'region' 	=> NULL
			];

			$message['message'] = (!$account_id)? $message['message'].'account_id, ': $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> 'Invalid main Account ID.',
				'region' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

        ## Validate the Region id.
        if ( $region_id <= 0 ){
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST );
        }

		## Run update call
		$updated_region = $this->diary_service->update_region( $account_id, $region_id, $region_data );
		
		if( !empty( $updated_region ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'region' 	=> $updated_region
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> $this->session->flashdata( 'message' ),
				'region' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
	
	
	/**
	* Get list of all Regions by account_id
	*/
	public function regions_get(){
		
		$account_id 	= ( !empty( $this->get( 'account_id' ) ) ) 	? (int) $this->get( 'account_id' ) : false ;
		$region_id 		= ( !empty( $this->get( 'region_id' ) ) ) 	? (int) $this->get( 'region_id' ) : false ;
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
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_BAD_REQUEST,
				'message' 	=> 'Invalid data: ',
				'region' 	=> NULL
			];

			$message['message'] = (!$account_id)? $message['message'].'account_id, ': $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> 'Invalid main Account ID.',
				'regions' 	=> NULL,
				'counters' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$regions = $this->diary_service->get_regions( $account_id, $region_id, $search_term, $where, $order_by, $limit, $offset );
		
		if( !empty( $regions ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'regions' 	=> ( !empty( $regions->records ) ) ? $regions->records : ( !empty( $regions ) ? $regions: null ),
				'counters' 	=> ( !empty( $regions->counters ) ) ? $regions->counters : null,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message'	=> $this->session->flashdata( 'message' ),
				'regions' 	=> null,
				'counters' 	=> null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/** Add Region Postcodes **/
	public function add_region_postcodes_post(){
		$postdata 	= $this->post();
		$account_id = !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;
		$region_id 	= !empty( $this->post( 'region_id' ) ) 	? ( int ) $this->post( 'region_id' ) 	: false;
		
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'region_id', 'Region ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid Job data: ',
				'region_postcodes' 	=> NULL	
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid main Account ID',
				'region_postcodes' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$region_postcodes = $this->diary_service->add_region_postcodes( $account_id, $region_id, $postdata );
		
		if( !empty( $region_postcodes ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'region_postcodes' 	=> $region_postcodes
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); // CREATED (201) being the HTTP response code
		}else{
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'region_postcodes' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	
	/**
	* Get list of all Postcodes associated to a Region
	*/
	public function region_postcodes_get(){
		
		$account_id 	= ( !empty( $this->get( 'account_id' ) ) ) 	? (int) $this->get( 'account_id' ) : false ;
		$region_id 		= ( !empty( $this->get( 'region_id' ) ) ) 	? (int) $this->get( 'region_id' ) : false ;
		$search_term	= ( !empty( $this->get( 'search_term' ) ) ) ?  trim( urldecode( $this->get( 'search_term' ) ) ) : false ;
		$where 		 	= ( !empty( $this->get( 'where' ) ) ) 		? $this->get( 'where' ) : false ;
		$order_by 		= ( !empty( $this->get( 'order_by' ) ) ) 	? $this->get( 'order_by' ) : false ;
		$limit		 	= ( $this->get( 'limit' ) ) 				? (int) $this->get( 'limit' ) : DEFAULT_LIMIT;
		$offset	 		= ( !empty( $this->get( 'offset' ) ) ) 		? (int) $this->get( 'offset' ) : 0 ;
		
		$this->form_validation->set_data( [ 'account_id'=>$account_id, /*'region_id'=>$region_id*/ ] );
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		#$this->form_validation->set_rules( 'region_id', 'Region ID', 'required' );
		
		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> 'Invalid data: ',
				'region_postcodes' 	=> NULL
			];

			$message['message'] = (!$account_id)? $message['message'].'account_id, ': $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> 'Invalid main Account ID.',
				'region_postcodes' 	=> NULL,
				'counters' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$region_postcodes = $this->diary_service->get_region_postcodes( $account_id, $region_id, $search_term, $where, $order_by, $limit, $offset );
		
		if( !empty( $region_postcodes ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'region_postcodes' 	=> !empty( $region_postcodes ) ? $region_postcodes: null,
				'counters' 	=> ( !empty( $region_postcodes->counters ) ) ? $region_postcodes->counters : null,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message'	=> $this->session->flashdata( 'message' ),
				'region_postcodes' 	=> null,
				'counters' 	=> null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/** Assign people to a region **/
	public function assign_people_post(){
		$postdata 	= $this->post();
		$account_id = !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;
		$region_id 	= !empty( $this->post( 'region_id' ) ) 	? ( int ) $this->post( 'region_id' ) 	: false;
		
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'region_id', 'Region ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid Region\'s data: ',
				'assigned_people'=> NULL	
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 				=> FALSE,
				'message' 				=> 'Invalid main Account ID',
				'assigned_people' 	=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$assigned_people = $this->diary_service->assign_people( $account_id, $region_id, $postdata );
		
		if( !empty( $assigned_people ) ){
			$message = [
				'status' 				=> TRUE,
				'message' 				=> $this->session->flashdata( 'message' ),
				'assigned_people' 	=> $assigned_people
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); 
		}else{
			$message = [
				'status' 				=> FALSE,
				'message' 				=> $this->session->flashdata( 'message' ),
				'assigned_people' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	
	/** Get a list of all People assigned to a Region **/
	public function assigned_people_get(){
		$account_id	= (int) $this->get( 'account_id' );
		$region_id 	= (int) $this->get( 'region_id' );
		$where 		= $this->get( 'where' );
		
		$this->form_validation->set_data( ['account_id'=>$account_id, /*'region_id'=>$region_id*/ ] );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        #$this->form_validation->set_rules( 'region_id', 'Region ID', 'required' );
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 			=> ( $this->session->flashdata('message' ) ) ? $this->session->flashdata('message' ) : 'Invalid main Account ID',
				'assigned_people'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$assigned_people 	= $this->diary_service->get_assigned_people( $account_id, $region_id, $where );
		
		if( !empty( $assigned_people ) ){
			$message = [
				'status' 			=> TRUE,
				'http_code' 		=> REST_Controller::HTTP_OK,
				'message' 			=> $this->session->flashdata('message' ),
				'assigned_people'=> $assigned_people
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
				'message' 			=> 'Assigned people not found',
				'assigned_people'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/** Remove a person/people from a Region **/
	public function unassign_people_post(){
		$postdata 	= $this->post();
		$account_id = !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;
		$region_id 	= !empty( $this->post( 'region_id' ) ) 	? ( int ) $this->post( 'region_id' ) 	: false;
		
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'region_id', 'Region ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			$message = [
				'status' => FALSE,
				'message' => 'Invalid request data: ',
				'unassign_people' => NULL	
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID',
				'unassign_people' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$unassign_people = $this->diary_service->unassign_people( $account_id, $region_id, $postdata );
		
		if( !empty( $unassign_people ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata( 'message' ),
				'unassign_people' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata( 'message' ),
				'unassign_people' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	
		/** Create Diary Resource and availability **/
	public function create_diary_resource_post(){
		$postdata 	= $this->post();
		$account_id = !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;

		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
	
		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid request data: ',
				'diary_resource'=> NULL	
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid main Account ID',
				'diary_resource' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$diary_resource = $this->diary_service->create_diary_resource( $account_id, $postdata );
		
		if( !empty( $diary_resource ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'diary_resource' 	=> $diary_resource
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); 
		}else{
			$message = [
				'status'			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'diary_resource' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}

	
	/** Add a skill to people / person **/
	public function add_skilled_people_post(){
		$postdata 	= $this->post();
		$account_id = !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;
		$skill_id 	= !empty( $this->post( 'skill_id' ) ) 	? ( int ) $this->post( 'skill_id' ) 	: false;
		
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'skill_id', 'Region ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid Region\'s data: ',
				'skilled_people'=> NULL	
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid main Account ID',
				'skilled_people' 	=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$add_skilled_people = $this->diary_service->add_skilled_people( $account_id, $skill_id, $postdata );
		
		if( !empty( $add_skilled_people ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'skilled_people' 	=> $add_skilled_people
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); 
		}else{
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'skilled_people' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	
	/** Get a list of all People skilled in a particular skill**/
	public function skilled_people_get(){
		$account_id	= (int) $this->get( 'account_id' );
		$skill_id 	= (int) $this->get( 'skill_id' );
		$where 		= $this->get( 'where' );
		
		$this->form_validation->set_data( ['account_id'=>$account_id, /*'skill_id'=>$skill_id*/ ] );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        #$this->form_validation->set_rules( 'skill_id', 'Skill ID', 'required' );
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 			=> ( $this->session->flashdata('message' ) ) ? $this->session->flashdata('message' ) : 'Invalid main Account ID',
				'skilled_people'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$skilled_people 	= $this->diary_service->get_skilled_people( $account_id, $skill_id, $where );
		
		if( !empty( $skilled_people ) ){
			$message = [
				'status' 			=> TRUE,
				'http_code' 		=> REST_Controller::HTTP_OK,
				'message' 			=> $this->session->flashdata('message' ),
				'skilled_people'	=> $skilled_people
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
				'message' 			=> 'No skilled people found',
				'skilled_people'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/** Remove a skill from person/people **/
	public function remove_skilled_people_post(){
		$postdata 	= $this->post();
		$account_id = !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;
		$skill_id 	= !empty( $this->post( 'skill_id' ) ) 	? ( int ) $this->post( 'skill_id' ) 	: false;
		
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'skill_id', 'Region ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			$message = [
				'status' => FALSE,
				'message' => 'Invalid request data: ',
				'skilled_people' => NULL	
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID',
				'skilled_people' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$remove_skilled_people = $this->diary_service->remove_skilled_people( $account_id, $skill_id, $postdata );
		
		if( !empty( $remove_skilled_people ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata( 'message' ),
				'skilled_people' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata( 'message' ),
				'skilled_people' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	/** Get Resource availability **/
	public function available_resource_get(){
		$account_id	= !empty( $this->get( 'account_id' ) ) 	? ( int ) $this->get( 'account_id' ) 	: false;
		$where 		= !empty( $this->get( 'where' ) ) 		? $this->get( 'where' )					: false;
		$order_by	= !empty( $this->get( 'order_by' ) ) 	? $this->get( 'order_by' )				: false;
		$limit 		= !empty( $this->get( 'limit' ) ) 		? $this->get( 'limit' )					: 60;
		$offset 	= !empty( $this->get( 'offset' ) ) 		? $this->get( 'offset' )				: DEFAULT_OFFSET;
		
		$this->form_validation->set_data( ['account_id'=>$account_id] );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 			=> ( $this->session->flashdata('message' ) ) ? $this->session->flashdata('message' ) : 'Invalid main Account ID',
				'available_resource'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$available_resource 		= $this->diary_service->get_available_resource( $account_id, $where, $order_by, $limit, $offset );
		
		if( !empty( $available_resource ) ){
			$message = [
				'status' 			=> TRUE,
				'http_code' 		=> REST_Controller::HTTP_OK,
				'message' 			=> $this->session->flashdata('message' ),
				'available_resource'=> $available_resource
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
				'message' 			=> 'No resource available',
				'available_resource'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/** Remove a postcode district from a Region **/
	public function remove_region_postcodes_post(){
		$postdata 	= $this->post();
		$account_id = !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;
		$region_id 	= !empty( $this->post( 'region_id' ) ) 	? ( int ) $this->post( 'region_id' ) 	: false;
		
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'region_id', 'Region ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid request data: ',
				'postcode_district' => NULL	
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid main Account ID',
				'postcode_district' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$postcode_district = $this->diary_service->remove_region_postcodes( $account_id, $region_id, $postdata );
		
		if( !empty( $postcode_district ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'postcode_district' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'postcode_district' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	
	/** Get Address / Regions **/
	public function address_regions_get(){
		$account_id		= !empty( $this->get( 'account_id' ) ) 	? $this->get( 'account_id' ) : false ;
		$search_term	= !empty( $this->get( 'search_term' ) ) ? $this->get( 'search_term' ) : false ;
		$where			= !empty( $this->get( 'where' ) ) 		? $this->get( 'where' ) : false ;
		$limit			= !empty( $this->get( 'limit' ) ) 		? ( int ) $this->get( 'limit' ) : DEFAULT_LIMIT ;
		$offset			= !empty( $this->get( 'offset' ) ) 		? ( int ) $this->get( 'offset' ) : 0 ;
		$order_by		= !empty( $this->get( 'order_by' ) ) 	? $this->get( 'order_by' ) : false ;

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
				'message' 		=> 'Validation errors: '.trim( $validation_errors ),
				'postcode_areas'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID.',
				'postcode_areas' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$postcode_areas = $this->diary_service->get_address_regions( $account_id, $search_term, $where, $limit, $offset, $order_by );

		if( !empty( $postcode_areas ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'postcode_areas' 	=> $postcode_areas
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'postcode_areas' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	
	/**
	* 	Get Resource details
	*/
    public function diary_resource_get(){
		$get_data 		= $this->get();

		$account_id 	= ( !empty( $get_data['account_id'] ) ) ? ( int ) $get_data['account_id'] : false ;
		$where			= ( !empty( $get_data['where'] ) ) ? $get_data['where'] : false ;
		$limit 			= ( !empty( $get_data['limit'] ) ) ? ( int ) $get_data['limit'] : false ;
		$offset 		= ( !empty( $get_data['offset'] ) ) ? ( int ) $get_data['offset'] : false ;
		$order_by 		= ( !empty( $get_data['order_by'] ) ) ? $get_data['order_by'] : false ;

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
				'status' 			=> FALSE,
				'message' 			=> 'Validation errors: '.trim( $validation_errors ),
				'diary_resource' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid main Account ID.',
				'diary_resource' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$diary_resource = $this->diary_service->get_diary_resource( $account_id, $where, $limit, $offset, $order_by );

		if( !empty( $diary_resource ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'diary_resource' 	=> $diary_resource
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'diary_resource' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
	
	
	/**
	* 	Update Resource details
	*/
    public function update_resource_post(){
		$resource_data 		= $this->post();
		
		$account_id 		= ( !empty( $resource_data['account_id'] ) ) ? ( int ) $resource_data['account_id'] : false ;
		unset( $resource_data['account_id'] );
		
		$resource_id		= ( !empty( $resource_data['resource_id'] ) ) ? ( int ) $resource_data['resource_id'] : false ;
		unset( $resource_data['resource_id'] );
		
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'resource_id', 'Resource ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
				'message' 			=> 'Validation errors: '.trim( $validation_errors ),
				'diary_resource' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 			=> 'Invalid main Account ID.',
				'diary_resource' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$diary_resource_exists = $this->diary_service->get_diary_resource( $account_id, ["resource_id"=>$resource_id] );
		if( !( $resource_data ) || !( $diary_resource_exists ) || !( $resource_id > 0 ) || !( ( int ) $resource_id ) ){
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_BAD_REQUEST,
				'message' 			=> 'Invalid Resource Data/ID.',
				'diary_resource' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$updated_diary_resource = $this->diary_service->update_resource( $account_id, $resource_id, $resource_data );

		if( !empty( $updated_diary_resource ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'http_code' 		=> REST_Controller::HTTP_OK,
				'diary_resource' 	=> $updated_diary_resource
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
				'message' 			=> $this->session->flashdata( 'message' ),
				'diary_resource' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
	
	
	/** 
	*	Delete an existing Diary Resource 
	**/
	public function delete_resource_post(){
		
		$diary_resource = false;
		$postdata 		= $this->post();
		$account_id 	= !empty( $postdata['account_id'] ) ? ( int ) $postdata['account_id'] : false;
		$resource_id 	= !empty( $postdata['resource_id'] ) ? ( int ) $postdata['resource_id'] : false;
		
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required|is_natural_no_zero' );
		$this->form_validation->set_rules( 'resource_id', 'Resource ID', 'required|is_natural_no_zero' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid request data: ',
				'diary_resource'=> NULL	
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid main Account ID',
				'diary_resource'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$resource_exists = $this->diary_service->get_diary_resource( $account_id, ["resource_id" => $resource_id] );
		if( !$resource_exists ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid Resource ID',
				'diary_resource'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$diary_resource = $this->diary_service->delete_diary_resource( $account_id, $resource_id );

		if( $diary_resource ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'diary_resource'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'diary_resource'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	
	/**
	* 	Get Un-available Dates
	*/
    public function unavailable_dates_get(){
		
		$required_data 		= $this->get();

		$account_id 	= ( !empty( $required_data['account_id'] ) )? ( ( int ) $required_data['account_id'] )	: false ;
		$where			= ( !empty( $required_data['where'] ) ) 	? ( $required_data['where'] 			)	: false ;
		$limit 			= ( !empty( $required_data['limit'] ) ) 	? ( ( int ) $required_data['limit'] 	)	: false ;
		$offset 		= ( !empty( $required_data['offset'] ) ) 	? ( ( int ) $required_data['offset'] 	)	: false ;
		$order_by 		= ( !empty( $required_data['order_by'] ) ) 	? ( $required_data['order_by'] 			)	: false ;

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
				'status' 			=> FALSE,
				'message' 			=> 'Validation errors: '.trim( $validation_errors ),
				'unavailable_dates' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid main Account ID.',
				'unavailable_dates' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$unavailable_dates = $this->diary_service->get_unavailable_dates( $account_id, $required_data, $where, $limit, $offset, $order_by );

		if( !empty( $unavailable_dates ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'unavailable_dates' => $unavailable_dates
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'unavailable_dates' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
	
	
	/**
	* 	Get Available Dates
	*/
    public function available_dates_get(){
		
		$required_data 		= $this->get();

		$account_id 	= ( !empty( $required_data['account_id'] ) )? ( ( int ) $required_data['account_id'] )	: false ;
		$where			= ( !empty( $required_data['where'] ) ) 	? ( $required_data['where'] 			)	: false ;
		$limit 			= ( !empty( $required_data['limit'] ) ) 	? ( ( int ) $required_data['limit'] 	)	: false ;
		$offset 		= ( !empty( $required_data['offset'] ) ) 	? ( ( int ) $required_data['offset'] 	)	: false ;
		$order_by 		= ( !empty( $required_data['order_by'] ) ) 	? ( $required_data['order_by'] 			)	: false ;

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
				'status' 			=> FALSE,
				'message' 			=> 'Validation errors: '.trim( $validation_errors ),
				'available_dates' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid main Account ID.',
				'available_dates' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$available_dates = $this->diary_service->get_available_dates( $account_id, $required_data, $where, $limit, $offset, $order_by );

		if( !empty( $available_dates ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'available_dates' => $available_dates
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'available_dates' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
	
	
	/** Get Resource availability - Optimized **/
	public function available_engineer_resource_get(){
		$account_id	= !empty( $this->get( 'account_id' ) ) 	? ( int ) $this->get( 'account_id' ) 	: false;
		$where 		= !empty( $this->get( 'where' ) ) 		? $this->get( 'where' )					: false;
		$order_by	= !empty( $this->get( 'order_by' ) ) 	? $this->get( 'order_by' )				: false;
		$limit 		= !empty( $this->get( 'limit' ) ) 		? $this->get( 'limit' )					: 60;
		$offset 	= !empty( $this->get( 'offset' ) ) 		? $this->get( 'offset' )				: DEFAULT_OFFSET;
		
		$this->form_validation->set_data( ['account_id'=>$account_id] );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 			=> ( $this->session->flashdata('message' ) ) ? $this->session->flashdata('message' ) : 'Invalid main Account ID',
				'available_resource'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$available_resource 		= $this->diary_service->get_available_engineer_resource( $account_id, $where, $order_by, $limit, $offset );
		
		if( !empty( $available_resource ) ){
			$message = [
				'status' 			=> TRUE,
				'http_code' 		=> REST_Controller::HTTP_OK,
				'message' 			=> $this->session->flashdata('message' ),
				'available_resource'=> $available_resource
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
				'message' 			=> 'No resource available',
				'available_resource'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
}
