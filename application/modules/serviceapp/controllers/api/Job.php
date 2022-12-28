<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class Job extends REST_Controller {

    function __construct(){
        // Construct the parent class
        parent::__construct();
		$this->load->model( 'Job_model','job_service' );
		$this->load->model( 'serviceapp/Diary_model','diary_service' );
		$this->load->model( 'serviceapp/People_model','people_service' );
		$this->form_validation->set_error_delimiters($this->config->item( 'error_start_delimiter', 'ion_auth' ), $this->config->item( 'error_end_delimiter', 'ion_auth' ));
		$this->lang->load( 'auth' );
    }

	/**
	* Create new Job resource
	*/
	public function create_post(){

		$job_data 	 = $this->post();
		$account_id  = $this->post( 'account_id' );
		#$this->form_validation->set_rules( 'job_date', 'Job Date', 'required' );
		$this->form_validation->set_rules( 'job_type_id', 'Job Type', 'required' );
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		#$this->form_validation->set_rules( 'site_id', 'Site ID', 'required' );
		#$this->form_validation->set_rules( 'customer_id', 'Customer ID', 'required' );
		#$this->form_validation->set_rules( 'address_id', 'Address details', 'required' );

		if ($this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_BAD_REQUEST,
				'message' 	=> 'Invalid Job data: ',
				'job' 		=> NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> 'Invalid main Account ID',
				'job' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$new_job = $this->job_service->create_job( $account_id, $job_data);

		if( !empty($new_job) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'job' 		=> $new_job
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		}else{
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NOT_MODIFIED,
				'message' 	=> $this->session->flashdata( 'message' ),
				'job' 		=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
    }

	/**
	* Update job resource
	*/
	public function update_post(){
        $job_data	= $this->post();
        $job_id 	= ( !empty( $this->post( 'job_id' ) ) ) ? (int) $this->post( 'job_id' ) : false;
		$account_id = (int) $this->post( 'account_id' );
		$this->form_validation->set_rules( 'job_id', 'Job ID', 'required' );
		#$this->form_validation->set_rules( 'job_date', 'Job Date', 'required' );
		#$this->form_validation->set_rules( 'customer_id', 'Customer ID', 'required' );
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if ($this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_BAD_REQUEST,
				'message' 	=> 'Invalid Job data: ',
				'job' 		=> NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> 'Invalid main Account ID',
				'job' 		=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}

        ## Validate the job id.
        if ( $job_id <= 0 ){
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST ); // BAD_REQUEST (400) being the HTTP response code
        }

		$job = $this->job_service->get_jobs( $account_id, $job_id );
		if( !$job ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_BAD_REQUEST,
				'message' 	=> $this->session->flashdata( 'message' ),
				'job' 		=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		## Run job update
		$updated_job = $this->job_service->update_job( $account_id, $job_id, $job_data);
		if( !empty($updated_job) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'job' 		=> $updated_job
			];
			$this->response($message, REST_Controller::HTTP_OK); // Resource Updated
		}else{
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NOT_MODIFIED,
				'message' 	=> $this->session->flashdata( 'message' ),
				'job' 		=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
    }

	/**
	* Get list of all Jobs or single record
	*/
    public function jobs_get(){
		$account_id = (int) $this->get( 'account_id' );
		$job_id 	= (int) $this->get( 'job_id' );
		$where 		= $this->get( 'where' );
		$order_by	= $this->get( 'order_by' );
		$limit	 	= ( $this->get( 'limit' ) ) ? (int) $this->get( 'limit' ) : DEFAULT_LIMIT;
		$offset	 	= ( $this->get( 'offset' ) ) ? (int) $this->get( 'offset' ) : 0;

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' => 'Invalid main account ID',
				'job' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

        $jobs = $this->job_service->get_jobs( $account_id, $job_id, $where, $limit, $offset );

		if( !empty($jobs) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'jobs' 		=> $jobs
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> $this->session->flashdata( 'message' ),
				'jobs' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }

	/**
	* Delete Job resource
	*/
    public function delete_get(){
        $job_id 	= (int) $this->get( 'job_id' );
        $account_id = (int) $this->get( 'account_id' );

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> ( $this->session->flashdata( 'message' ) ) ? $this->session->flashdata( 'message' ) : 'Invalid main Account ID',
				'job' 		=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		if ( $job_id <= 0 ){
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

		$delete_job = $this->job_service->delete_job( $account_id, $job_id );

		if( !empty($delete_job) ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'job' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'job' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_NO_CONTENT );
		}
    }

	/**
	* Get list of all Job durations
	*/
	public function job_durations_get(){

		$job_durations = job_durations();

		if( !empty($job_durations) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> 'Job-duration records found',
				'job_durations' => $job_durations
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'No records found',
				'job_durations' => NULL
			];
			$this->response($message, REST_Controller::HTTP_NO_CONTENT);
		}
	}


	/**
	* Get list of all Job types
	*/
	public function job_types_get(){

		$account_id 	= ( !empty( $this->get( 'account_id' ) ) ) ? (int) $this->get( 'account_id' ) : false ;
		$job_type_id 	= ( !empty( $this->get( 'job_type_id' ) ) ) ? (int) $this->get( 'job_type_id' ) : false ;
		$search_term	= ( !empty( $this->get( 'search_term' ) ) ) ?  trim( urldecode( $this->get( 'search_term' ) ) ) : false ;
		$where 		 	= ( !empty( $this->get( 'where' ) ) ) ? $this->get( 'where' ) : false ;
		$order_by 		= ( !empty( $this->get( 'order_by' ) ) ) ? $this->get( 'order_by' ) : false ;
		$limit		 	= ( $this->get( 'limit' ) ) ? (int) $this->get( 'limit' ) : DEFAULT_LIMIT;
		$offset	 		= ( !empty( $this->get( 'offset' ) ) ) ? (int) $this->get( 'offset' ) : 0 ;

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> 'Invalid main Account ID.',
				'job_types' => NULL,
				'counters' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$job_types = $this->job_service->get_job_types( $account_id, $job_type_id, $search_term, $where, $order_by, $limit, $offset );

		if( !empty( $job_types ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'job_types' => ( !empty( $job_types->records ) ) ? $job_types->records : null,
				'counters' 	=> ( !empty( $job_types->counters ) ) ? $job_types->counters : null,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message'	=> $this->session->flashdata( 'message' ),
				'job_types' => null,
				'counters' 	=> null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/** Create a New Job Type **/
	public function create_job_type_post(){
		$job_type_data  = $this->post();
		$account_id		= (int) $this->post( 'account_id' );
		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'job_type', 'Job Type', 'required' );
		$this->form_validation->set_rules( 'job_type_desc', 'Job Type Description', 'required' );
		#$this->form_validation->set_rules( 'job_rate', 'Job Rate', 'required' );
		#$this->form_validation->set_rules( 'category_id', 'Job Category', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			## One of the required fields is invalid
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> 'Invalid data: ',
				'job_type' 	=> NULL,
				'exists' 	=> NULL
			];

			$message['message'] = (!$account_id)? $message['message'].'account_id, ' : $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> 'Invalid main Account ID.',
				'job_type' 	=> NULL,
				'exists' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$new_job_type 	= $this->job_service->create_job_type( $account_id, $job_type_data );
		$exists 		= $this->session->flashdata( 'already_exists' );

		if( !empty( $new_job_type ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_CREATED,
				'message' 	=> $this->session->flashdata( 'message' ),
				'job_type' 	=> $new_job_type,
				'exists' 	=> ( !empty( $exists ) ) ? true : false
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> $this->session->flashdata( 'message' ),
				'job_type' 	=> NULL,
				'exists' 	=> ( !empty( $exists ) ) ? true : false
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/**
	* Update Job Type record
	*/
	public function update_job_type_post(){
        $job_type_data 	= $this->post();
        $account_id 	= ( int ) $this->post( 'account_id' );
        $job_type_id 	= ( int ) $this->post( 'job_type_id' );

		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'job_type_id', 'Job Type ID', 'required' );
		$this->form_validation->set_rules( 'job_type', 'Job Type', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			## One of the required fields is invalid
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> 'Invalid data: ',
				'job_type' 	=> NULL
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
				'job_type' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

        ## Validate the Job type id.
        if ( $job_type_id <= 0 ){
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST ); // BAD_REQUEST (400) being the HTTP response code
        }

		## Run update call
		$updated_job_type = $this->job_service->update_job_type( $account_id, $job_type_id, $job_type_data );

		if( !empty( $updated_job_type ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'job_type' 	=> $updated_job_type
			];
			$this->response( $message, REST_Controller::HTTP_OK ); // Resource Updated
		}else{
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> $this->session->flashdata( 'message' ),
				'job_type' 	=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
    }

	/**
	* Get list of all Job statuses
	*/
	public function job_statuses_get(){
		$account_id   = $this->get( 'account_id' );
		$status_id    = $this->get( 'status_id' );
		$grouped 	  = $this->get( 'grouped' );
		$status_group = $this->get( 'status_group' );
		$job_statuses = $this->job_service->get_job_statuses( $account_id, $status_id, $grouped, $status_group );

		if( !empty( $job_statuses ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> 'Job-status records found',
				'job_statuses' 	=> $job_statuses
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'No records found',
				'job_statuses' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_NO_CONTENT );
		}
	}

	/**
	* Search through list of Jobs
	*/
	public function lookup_get(){
		$account_id 	= ( int ) $this->get( 'account_id' );
		$job_id 		= ( int ) $this->get( 'job_id' );
		$limit 		 	= ( !empty( $this->get( 'limit' ) ) )  ? (int) $this->get( 'limit' )  : DEFAULT_LIMIT;
		$offset 	 	= ( !empty( $this->get( 'offset' ) ) ) ? (int) $this->get( 'offset' ) : DEFAULT_OFFSET;
		$where 		 	= $this->get( 'where' );
		$order_by    	= $this->get( 'order_by' );
		$search_term 	= trim( urldecode( $this->get( 'search_term' ) ) );

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID.',
				'job' 		=> NULL,
				'counters' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$job_lookup = $this->job_service->job_lookup( $account_id, $job_id, $search_term, $where, $order_by, $limit, $offset );

		if( !empty( $job_lookup ) ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'job' 		=> ( !empty( $job_lookup->records ) ) ? $job_lookup->records : null,
				'counters' 	=> ( !empty( $job_lookup->counters ) ) ? $job_lookup->counters : null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'job' 		=> ( !empty( $job_lookup->records ) ) ? $job_lookup->records : null,
				'counters' 	=> ( !empty( $job_lookup->counters ) ) ? $job_lookup->counters : null
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}

	/*
	* Check if rick assessment has been completed for a specific Job on a particular day
	*/
	public function check_risk_assessment_get(){
		$job_id 	= (int) $this->get( 'job_id' );
        $account_id = (int) $this->get( 'account_id' );
		$job_date 	= ( $this->get( 'job_date' ) ) ? $this->get( 'job_date' ) : false;

		$this->form_validation->set_rules( 'job_id', 'Job ID', 'required' );
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'job_date', 'Job Date', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' => FALSE,
				'message' => 'Invalid Job data: ',
				'risks_assessed' => NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main account ID',
				'risks_assessed' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		$check_risk_assessmeny = $this->job_service->check_risk_assessment( $account_id, $job_id, $job_date );

	}

	/**
	* Get all Job stats
	*/
	public function job_stats_get(){
		$account_id = !empty( $this->get( 'account_id' ) ) 	? $this->get( 'account_id' ) 	: false;
		$where 		= !empty( $this->get( 'where' ) ) 		? $this->get( 'where' ) 		: false;;

		$job_stats 	= $this->job_service->get_job_statistics( $account_id, $where );

		if( !empty($job_stats) ){
			$message = [
				'status' => TRUE,
				'message' => 'Job stats records found',
				'job_stats' => $job_stats
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' => FALSE,
				'message' => 'No records found',
				'job_stats' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_NO_CONTENT );
		}
	}

	/** Add Required Stock for a Job **/
	public function add_required_items_post(){
		$postdata 	 = $this->post();
		$account_id  = $this->post( 'account_id' );
		$job_id  	 = $this->post( 'job_id' );
		$item_type   = $this->post( 'item_type' );
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'job_id', 'Job ID', 'required' );
		#$this->form_validation->set_rules( 'item_type', 'Item Type', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' => FALSE,
				'message' => 'Invalid Job data: ',
				'required_items' => NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID',
				'required_items' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$new_items = $this->job_service->add_required_items( $account_id, $job_id, $item_type, $postdata );

		if( !empty( $new_items ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata( 'message' ),
				'required_items' => $new_items
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); // CREATED (201) being the HTTP response code
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata( 'message' ),
				'required_items' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

	}

	/** Add Consumed Stock for a Job **/
	public function add_consumed_items_post(){
		$postdata 	 = $this->post();
		$account_id  = $this->post( 'account_id' );
		$job_id  	 = $this->post( 'job_id' );
		$item_type   = $this->post( 'item_type' );
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'job_id', 'Job ID', 'required' );
		#$this->form_validation->set_rules( 'item_type', 'Item Type', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' => FALSE,
				'message' => 'Invalid Job data: ',
				'consumed_items' => NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID',
				'consumed_items' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		$new_items = $this->job_service->add_consumed_items( $account_id, $job_id, $item_type, $postdata );

		if( !empty( $new_items ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata( 'message' ),
				'consumed_items' => $new_items
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); // CREATED (201) being the HTTP response code
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata( 'message' ),
				'consumed_items' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/** Get a list of associated risks **/
	public function associated_risks_get(){
		$account_id		= (int) $this->get( 'account_id' );
		$job_type_id 	= (int) $this->get( 'job_type_id' );
		$where 			= ( !empty( $this->get( 'where' ) ) ) ? $this->get( 'where' ) : false;

		$this->form_validation->set_data( ['account_id'=>$account_id, 'job_type_id'=>$job_type_id ] );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'job_type_id', 'Job Type ID', 'required' );

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' => ( $this->session->flashdata( 'message' ) ) ? $this->session->flashdata( 'message' ) : 'Invalid main Account ID',
				'associated_risks' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$associated_risks 	= $this->job_service->get_associated_risks( $account_id, $job_type_id, $where );

		if( !empty( $associated_risks ) ){
			$message = [
				'status' => TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' => $this->session->flashdata( 'message' ),
				'associated_risks' => $associated_risks
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' => FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' => 'Associated Risks not found',
				'associated_risks' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/** Add Associated Risks **/
	public function add_associated_risks_post(){
		$postdata 	 = $this->post();
		$account_id  = $this->post( 'account_id' );
		$job_id  	 = $this->post( 'job_type_id' );
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'job_type_id', 'Job Type ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' => FALSE,
				'message' => 'Invalid Job data: ',
				'associated_risks' => NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID',
				'associated_risks' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		$associated_risks = $this->job_service->add_associated_risks( $account_id, $job_id, $postdata );

		if( !empty( $associated_risks ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata( 'message' ),
				'associated_risks' => $associated_risks
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); // CREATED (201) being the HTTP response code
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata( 'message' ),
				'associated_risks' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/** Remove Associated Risks from a Job Type **/
	public function remove_associated_risks_post(){
		$postdata 	 = $this->post();
		$account_id  = $this->post( 'account_id' );
		$job_type_id = $this->post( 'job_type_id' );
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'job_type_id', 'Job Type ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' => FALSE,
				'message' => 'Invalid Job data: ',
				'associated_risks' => NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID',
				'associated_risks' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$remove_risks = $this->job_service->remove_associated_risks( $account_id, $job_type_id, $postdata );

		if( !empty( $remove_risks ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata( 'message' ),
				'associated_risks' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata( 'message' ),
				'associated_risks' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/** Get required Items **/
	public function required_items_get(){
		$account_id	= (int) $this->get( 'account_id' );
		$job_id 	= (int) $this->get( 'job_id' );
		$item_type 	= $this->get( 'item_type' );

		$this->form_validation->set_data( ['account_id'=>$account_id, 'job_id'=>$job_id ] );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'job_id', 'Job ID', 'required' );

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' => ( $this->session->flashdata( 'message' ) ) ? $this->session->flashdata( 'message' ) : 'Invalid main Account ID',
				'required_items' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$required_items 	= $this->job_service->get_required_items( $account_id, $job_id, $item_type );

		if( !empty( $required_items ) ){
			$message = [
				'status' => TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' => $this->session->flashdata( 'message' ),
				'required_items' => $required_items
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' => FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' => 'No data found',
				'required_items' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/** Get Consumed Items **/
	public function consumed_items_get(){
		$account_id	= (int) $this->get( 'account_id' );
		$job_id 	= (int) $this->get( 'job_id' );
		$item_type 	= $this->get( 'item_type' );
		$grouped 	= $this->get( 'grouped' );

		$this->form_validation->set_data( ['account_id'=>$account_id, 'job_id'=>$job_id ] );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'job_id', 'Job ID', 'required' );

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' => ( $this->session->flashdata( 'message' ) ) ? $this->session->flashdata( 'message' ) : 'Invalid main Account ID',
				'consumed_items' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$consumed_items 	= $this->job_service->get_consumed_items( $account_id, $job_id, $item_type, $grouped );

		if( !empty( $consumed_items ) ){
			$message = [
				'status' => TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' => $this->session->flashdata( 'message' ),
				'consumed_items' => $consumed_items
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' => FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' => 'No data found',
				'consumed_items' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/** Commit Jobs to assigned Engineer. **/
	public function commit_jobs_post(){
		$postdata 	= $this->post();
		$account_id = ( !empty( $this->post( 'account_id' ) ) ) ? (int) $this->post( 'account_id' ) : false;
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid Job data: ',
				'commit_jobs' => NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID',
				'commit_jobs' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$commit_jobs =  $this->job_service->commit_jobs( $account_id, $postdata );

		if( !empty( $commit_jobs ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata( 'message' ),
				'commit_jobs' => $commit_jobs
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata( 'message' ),
				'commit_jobs' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

	}

	/** Get a list of required skills **/
	public function required_skills_get(){
		$account_id 	= !empty( $this->get( 'account_id' ) ) 	? ( int ) $this->get( 'account_id' ) 	: false;
		$job_type_id	= !empty( $this->get( 'job_type_id' ) ) ? ( int ) $this->get( 'job_type_id' ) 	: false;
		$where			= $this->get( 'where' );

		$this->form_validation->set_data( ['account_id'=>$account_id, 'job_type_id'=>$job_type_id ] );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'job_type_id', 'Job Type ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid Job data: ',
				'required_skills' => NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' => ( $this->session->flashdata( 'message' ) ) ? $this->session->flashdata( 'message' ) : 'Invalid main Account ID',
				'required_skills' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$required_skills 	= $this->job_service->get_required_skills( $account_id, $job_type_id, $where );

		if( !empty( $required_skills ) ){
			$message = [
				'status' => TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' => $this->session->flashdata( 'message' ),
				'required_skills' => $required_skills
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' => FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' => 'Required Skills not found',
				'required_skills' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/** Add Required Skills **/
	public function add_required_skills_post(){
		$postdata 	 = $this->post();
		$account_id  = !empty( $this->post( 'account_id' ) ) 	? ( int ) $this->post( 'account_id' ) 	: false;
		$job_type_id = !empty( $this->post( 'job_type_id' ) ) 	? ( int ) $this->post( 'job_type_id' ) 	: false;

		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'job_type_id', 'Job Type ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' => FALSE,
				'message' => 'Invalid Job data: ',
				'required_skills' => NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID',
				'required_skills' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		$required_skills = $this->job_service->add_required_skills( $account_id, $job_type_id, $postdata );

		if( !empty( $required_skills ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata( 'message' ),
				'required_skills' => $required_skills
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); // CREATED (201) being the HTTP response code
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata( 'message' ),
				'required_skills' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/** Remove Required Skills from a Job Type **/
	public function remove_required_skill_post(){
		$postdata 	 = $this->post();
		$account_id  = !empty( $this->post( 'account_id' ) ) 	? ( int ) $this->post( 'account_id' ) 	: false;
		$job_type_id = !empty( $this->post( 'job_type_id' ) ) 	? ( int ) $this->post( 'job_type_id' ) 	: false;

		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'job_type_id', 'Job Type ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' => FALSE,
				'message' => 'Invalid Job data: ',
				'required_skill' => NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID',
				'required_skill' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$remove_skills = $this->job_service->remove_required_skills( $account_id, $job_type_id, $postdata );

		if( !empty( $remove_skills ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata( 'message' ),
				'required_skill' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata( 'message' ),
				'required_skill' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/**
	* Get Job Completion stats
	*/
	public function job_completion_stats_get(){

		$account_id = !empty( $this->get( 'account_id' ) ) 	? $this->get( 'account_id' ) : false ;
		$job_date 	= !empty( $this->get( 'job_date' ) ) 		? $this->get( 'job_date' ) : false ;
		$date_from 	= !empty( $this->get( 'date_from' ) ) 	? $this->get( 'date_from' ) : false ;
		$date_to 	= !empty( $this->get( 'date_to' ) ) 		? $this->get( 'date_to' ) : false ;
		$where 		= !empty( $this->get( 'where' ) ) 		? $this->get( 'where' ) : false ;
		$limit 		= !empty( $this->get( 'limit' ) ) 		? $this->get( 'limit' ) : false ;
		$offset 	= !empty( $this->get( 'offset' ) ) 		? $this->get( 'offset' ) : false ;

		$this->form_validation->set_data( ['account_id'=>$account_id ] );
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' => FALSE,
				'message' => 'Invalid Job data: ',
				'job_completion_stats' => NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID',
				'job_completion_stats' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$job_stats 	= $this->job_service->get_job_completion_stats( $account_id, $job_date, $date_from, $date_to, $where, $limit, $offset );

		if( !empty( $job_stats ) ){
			$message = [
				'status' => TRUE,
				'message' => 'Job stats records found',
				'type' => 'job_completion_stats',
				'job_completion_stats' => $job_stats
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' => FALSE,
				'message' => 'No records found',
				'job_completion_stats' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_NO_CONTENT );
		}
	}


	/** Add Dynamic Risks to a Job **/
	public function add_dynamic_risks_post(){
		$postdata 	 = $this->post();
		$account_id  = $this->post( 'account_id' );
		$job_id  	 = $this->post( 'job_id' );
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'job_id', 'Job ID', 'required' );
		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid Job data: ',
				'dynamic_risks' => NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid main Account ID',
				'dynamic_risks' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		$dynamic_risks = $this->job_service->add_dynamic_risks( $account_id, $job_id, $postdata );

		if( !empty( $dynamic_risks ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'dynamic_risks' => $dynamic_risks
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); // CREATED (201) being the HTTP response code
		}else{
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'dynamic_risks' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/** Remove Dynamic Risks from a Job **/
	public function remove_dynamic_risks_post(){
		$postdata 	 	= $this->post();
		$account_id  	= $this->post( 'account_id' );
		$job_id 		= $this->post( 'job_id' );

		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'job_id', 'Job ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid Job data: ',
				'dynamic_risks' => NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid main Account ID',
				'dynamic_risks' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$remove_dynamic_risks = $this->job_service->remove_dynamic_risks( $account_id, $job_id, $postdata );

		if( !empty( $remove_dynamic_risks ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'dynamic_risks' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		}else{
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'dynamic_risks' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/** Create a New Fail Code **/
	public function create_fail_code_post(){
		$fail_code_data  = $this->post();
		$account_id		 = (int) $this->post( 'account_id' );
		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'fail_code_text', 'Fail Code Text', 'required' );
		$this->form_validation->set_rules( 'fail_code_desc', 'Fail Code Description', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> 'Invalid data: ',
				'fail_code' => NULL,
				'exists' 	=> NULL
			];

			$message['message'] = (!$account_id)? $message['message'].'account_id, ' : $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> 'Invalid main Account ID.',
				'fail_code' => NULL,
				'exists' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$new_fail_code 	= $this->job_service->create_fail_code( $account_id, $fail_code_data );
		$exists 		= $this->session->flashdata( 'already_exists' );

		if( !empty( $new_fail_code ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_CREATED,
				'message' 	=> $this->session->flashdata( 'message' ),
				'fail_code' 	=> $new_fail_code,
				'exists' 	=> ( !empty( $exists ) ) ? true : false
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> $this->session->flashdata( 'message' ),
				'fail_code' 	=> NULL,
				'exists' 	=> ( !empty( $exists ) ) ? true : false
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/**
	* Get list of all Fail codes
	*/
	public function fail_codes_get(){

		$account_id 	= ( !empty( $this->get( 'account_id' ) ) ) 		? (int) $this->get( 'account_id' ) 					: false ;
		$fail_code_id 	= ( !empty( $this->get( 'fail_code_id' ) ) )	? (int) $this->get( 'fail_code_id' ) 				: false ;
		$search_term	= ( !empty( $this->get( 'search_term' ) ) ) 	? trim( urldecode( $this->get( 'search_term' ) ) ) 	: false ;
		$where 		 	= ( !empty( $this->get( 'where' ) ) ) 			? $this->get( 'where' ) 							: false ;
		$order_by 		= ( !empty( $this->get( 'order_by' ) ) ) 		? $this->get( 'order_by' ) 							: false ;
		$limit		 	= ( $this->get( 'limit' ) ) 					? (int) $this->get( 'limit' ) 						: DEFAULT_LIMIT;
		$offset	 		= ( !empty( $this->get( 'offset' ) ) ) 			? (int) $this->get( 'offset' ) 						: 0 ;

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> 'Invalid main Account ID.',
				'fail_codes'=> NULL,
				'counters' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$fail_codes = $this->job_service->get_fail_codes( $account_id, $fail_code_id, $search_term, $where, $order_by, $limit, $offset );

		if( !empty( $fail_codes ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'fail_codes'=> ( !empty( $fail_codes->records ) )  ? $fail_codes->records : null,
				'counters' 	=> ( !empty( $fail_codes->counters ) ) ? $fail_codes->counters : null,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message'	=> $this->session->flashdata( 'message' ),
				'fail_codes'=> null,
				'counters' 	=> null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/**
	* Update Fail Code record
	*/
	public function update_fail_code_post(){
        $fail_code_data = $this->post();
        $account_id 	= ( int ) $this->post( 'account_id' );
        $fail_code_id 	= ( int ) $this->post( 'fail_code_id' );

		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'fail_code_id', 'Fail Code ID', 'required' );
		$this->form_validation->set_rules( 'fail_code_text', 'Fail Code', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			## One of the required fields is invalid
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> 'Invalid data: ',
				'fail_code' => NULL
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
				'fail_code' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

        ## Validate the Fail code id.
        if ( $fail_code_id <= 0 ){
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST );
        }

		## Run update call
		$updated_fail_code = $this->job_service->update_fail_code( $account_id, $fail_code_id, $fail_code_data );

		if( !empty( $updated_fail_code ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'fail_code' => $updated_fail_code
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> $this->session->flashdata( 'message' ),
				'fail_code' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }

	/**
	* Get list of all Jobs grouped by all statuses or a specific one
	*/
    public function jobs_by_status_get(){
		$account_id 	= (int) $this->get( 'account_id' );
		$status_group 	= $this->get( 'status_group' );
		$where 			= $this->get( 'where' );
		$order_by		= $this->get( 'order_by' );
		$limit	 		= ( $this->get( 'limit' ) ) 	? ( int ) $this->get( 'limit' ) 	: DEFAULT_LIMIT;
		$offset	 		= ( $this->get( 'offset' ) ) 	? ( int ) $this->get( 'offset' ) 	: 0;

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main account ID',
				'jobs' 		=> NULL,
				'counters'	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

        $jobs = $this->job_service->get_jobs_by_status( $account_id, $status_group, $where, $limit, $offset );

		if( !empty( $jobs ) ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'jobs' 		=> ( $jobs->records ) 	? $jobs->records : ( !empty( $jobs ) ? $jobs : NULL ),
				'counters'	=> ( $jobs->counters ) 	? $jobs->counters : NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'jobs' 		=> NULL,
				'counters'	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }

	/** Update Job Consumed Items **/
	public function update_consumed_items_post(){
		$postdata 	= $this->post();
		$account_id = !empty( $this->post( 'account_id' ) ) ? $this->post( 'account_id' ) 	: false;
		$job_id 	= !empty( $this->post( 'job_id' ) ) 	? $this->post( 'job_id' ) 		: false;

		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'job_id', 'Job ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			## One of the required fields is invalid
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'message' 		=> 'Invalid data: ',
				'consumed_items'=> NULL
			];

			$message['message'] = (!$account_id)? $message['message'].'account_id, ': $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> 'Invalid main Account ID.',
				'consumed_items'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$consumed_items = $this->job_service->update_consumed_items( $account_id, $job_id, $postdata );

		if( !empty( $consumed_items ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'consumed_items'=> ( $consumed_items ) 	? $consumed_items	: NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'consumed_items'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/** Delete consumed Items  from  a Job **/
	public function delete_consumed_items_post(){
		$postdata 	= $this->post();
		$account_id = !empty( $this->post( 'account_id' ) ) ? $this->post( 'account_id' ) 	: false;
		$job_id 	= !empty( $this->post( 'job_id' ) ) 	? $this->post( 'job_id' ) 		: false;

		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'job_id', 'Job ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			## One of the required fields is invalid
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'message' 		=> 'Invalid data: ',
				'consumed_items'=> NULL
			];

			$message['message'] = (!$account_id)? $message['message'].'account_id, ': $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> 'Invalid main Account ID.',
				'consumed_items'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$consumed_items = $this->job_service->delete_consumed_items( $account_id, $job_id, $postdata );

		if( !empty( $consumed_items ) ){
			$message = [
				'status' 		=> TRUE,
				'http_code' 	=> REST_Controller::HTTP_OK,
				'message' 		=> $this->session->flashdata( 'message' ),
				'consumed_items'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'message' 		=> $this->session->flashdata( 'message' ),
				'consumed_items'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


		/**
	* Get list of all Schedule Frequencies
	*/
	public function schedule_frequencies_get(){

		$account_id 	= ( !empty( $this->get( 'account_id' ) ) ) 		? (int) $this->get( 'account_id' ) : false ;
		$frequency_id 	= ( !empty( $this->get( 'frequency_id' ) ) ) 	? (int) $this->get( 'frequency_id' ) : false ;
		$search_term	= ( !empty( $this->get( 'search_term' ) ) ) 	?  trim( urldecode( $this->get( 'search_term' ) ) ) : false ;
		$where 		 	= ( !empty( $this->get( 'where' ) ) ) 			? $this->get( 'where' ) : false ;
		$order_by 		= ( !empty( $this->get( 'order_by' ) ) ) 		? $this->get( 'order_by' ) : false ;
		$limit		 	= ( $this->get( 'limit' ) ) 					? (int) $this->get( 'limit' ) : DEFAULT_LIMIT;
		$offset	 		= ( !empty( $this->get( 'offset' ) ) ) ? (int) $this->get( 'offset' ) : 0 ;

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 				=> FALSE,
				'http_code' 			=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 				=> 'Invalid main Account ID.',
				'schedule_frequencies' 	=> NULL,
				'counters' 				=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$schedule_frequencies = $this->job_service->get_schedule_frequencies( $account_id, $frequency_id, $search_term, $where, $order_by, $limit, $offset );

		if( !empty( $schedule_frequencies ) ){
			$message = [
				'status' 				=> TRUE,
				'http_code' 			=> REST_Controller::HTTP_OK,
				'message' 				=> $this->session->flashdata( 'message' ),
				'schedule_frequencies' 	=> ( !empty( $schedule_frequencies->records ) ) ? $schedule_frequencies->records : null,
				'counters' 				=> ( !empty( $schedule_frequencies->counters ) ) ? $schedule_frequencies->counters : null,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 				=> FALSE,
				'http_code' 			=> REST_Controller::HTTP_NO_CONTENT,
				'message'				=> $this->session->flashdata( 'message' ),
				'schedule_frequencies' 	=> null,
				'counters' 				=> null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/** Create a New Schedule Frequency **/
	public function create_schedule_frequency_post(){
		$schedule_frequency_data  = $this->post();
		$account_id				  = (int) $this->post( 'account_id' );
		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'frequency_name', 'Schedule Frequency', 'required' );
		$this->form_validation->set_rules( 'frequency_desc', 'Schedule Frequency Description', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			## One of the required fields is invalid
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_BAD_REQUEST,
				'message' 			=> 'Invalid data: ',
				'schedule_frequency'=> NULL,
				'exists' 	=> NULL
			];

			$message['message'] 	= (!$account_id)? $message['message'].'account_id, ' : $message['message'];
			$message['message'] 	= ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 			=> 'Invalid main Account ID.',
				'schedule_frequency'=> NULL,
				'exists' 			=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$new_schedule_frequency 	= $this->job_service->create_schedule_frequency( $account_id, $schedule_frequency_data );
		$exists 		= $this->session->flashdata( 'already_exists' );

		if( !empty( $new_schedule_frequency ) ){
			$message = [
				'status' 			=> TRUE,
				'http_code' 		=> REST_Controller::HTTP_CREATED,
				'message' 			=> $this->session->flashdata( 'message' ),
				'schedule_frequency'=> $new_schedule_frequency,
				'exists' 			=> ( !empty( $exists ) ) ? true : false
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 				=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
				'message' 			=> $this->session->flashdata( 'message' ),
				'schedule_frequency'=> NULL,
				'exists' 			=> ( !empty( $exists ) ) ? true : false
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/**
	* Update Schedule Frequency record
	*/
	public function update_schedule_frequency_post(){
        $schedule_frequency_data 	= $this->post();
        $account_id 	= ( int ) $this->post( 'account_id' );
        $frequency_id 	= ( int ) $this->post( 'frequency_id' );

		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'frequency_id', 'Schedule Frequency ID', 'required' );
		$this->form_validation->set_rules( 'frequency_name', 'Schedule Frequency', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			## One of the required fields is invalid
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_BAD_REQUEST,
				'message' 			=> 'Invalid data: ',
				'schedule_frequency'=> NULL
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
				'schedule_frequency'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

        ## Validate the Schedule Frequency id.
        if ( $frequency_id <= 0 ){
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST );
		}

		$updated_schedule_frequency = $this->job_service->update_schedule_frequency( $account_id, $frequency_id, $schedule_frequency_data );

		if( !empty( $updated_schedule_frequency ) ){
			$message = [
				'status' 			=> TRUE,
				'http_code' 		=> REST_Controller::HTTP_OK,
				'message' 			=> $this->session->flashdata( 'message' ),
				'schedule_frequency'=> $updated_schedule_frequency
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
				'message' 			=> $this->session->flashdata( 'message' ),
				'schedule_frequency'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }


	/** Create Schedule record(s) **/
	public function create_schedules_post(){
		$postdata 		= $this->post();
		$account_id 	= !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;
		$frequency_id 	= !empty( $this->post( 'frequency_id' ) ) ? ( int ) $this->post( 'frequency_id' ) 	: false;
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'frequency_id', 'Frequency ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_BAD_REQUEST,
				'message' 	=> 'Invalid request data: ',
				'schedules'	=> NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> 'Invalid main Account ID',
				'schedules' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$schedules = $this->job_service->create_schedules( $account_id, $frequency_id, $postdata );

		if( !empty( $schedules ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_CREATED,
				'message' 	=> $this->session->flashdata( 'message' ),
				'schedules' => $schedules
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status'	=> FALSE,
				'http_code' => REST_Controller::HTTP_BAD_REQUEST,
				'message' 	=> $this->session->flashdata( 'message' ),
				'schedules' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/** Update / Edit Schedule record **/
	public function update_schedule_post(){
		$postdata 	= $this->post();
		$account_id = !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;
		$schedule_id= !empty( $this->post( 'schedule_id' ) ) ? ( int ) $this->post( 'schedule_id' ) : false;

		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status'  	=> FALSE,
				'http_code' => REST_Controller::HTTP_BAD_REQUEST,
				'message' 	=> 'Invalid request data: ',
				'schedule'	=> NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> 'Invalid main Account ID',
				'schedule' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$schedule = $this->job_service->update_site_schedule( $account_id, $schedule_id, $postdata );

		if( !empty( $schedule ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'schedule' 	=> $schedule
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		}else{
			$message = [
				'status'	=> FALSE,
				'http_code' => REST_Controller::HTTP_BAD_REQUEST,
				'message' 	=> $this->session->flashdata( 'message' ),
				'schedule' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/** Get list of Schedules **/
	public function schedules_get(){

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
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> 'Invalid data: ',
				'schedules' => NULL
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
				'schedules' => NULL,
				'counters' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$schedules = $this->job_service->get_schedules( $account_id, $search_term, $where, $order_by, $limit, $offset );

		if( !empty( $schedules ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'schedules' => ( !empty( $schedules->records ) )  ? $schedules->records : ( !empty( $schedules ) ? $schedules: null ),
				'counters' 	=> ( !empty( $schedules->counters ) ) ? $schedules->counters : null,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message'	=> 'No schedules data found',
				'schedules' => null,
				'counters' 	=> null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/** Delete an existing Schedule record **/
	public function delete_schedule_post(){
		$postdata 	 = $this->post();
		$account_id  = !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;
		$schedule_id = !empty( $this->post( 'schedule_id' ) ) ? ( int ) $this->post( 'schedule_id' ) 	: false;

		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_BAD_REQUEST,
				'message'	=> 'Invalid request data: ',
				'schedule'	=> NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> 'Invalid main Account ID',
				'schedule'	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		## Validate the Schedule id.
        if ( $schedule_id <= 0 ){
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST );
        }

		$schedule = $this->job_service->get_schedules( $account_id, false, [ 'schedule_id' => $schedule_id ] );
		if( !$schedule ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_BAD_REQUEST,
				'message' 	=> $this->session->flashdata( 'message' ),
				'schedule' 	=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		$schedule = $this->job_service->delete_schedule( $account_id, $schedule_id );

		if( !empty( $schedule ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'schedule'	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		}else{
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> $this->session->flashdata( 'message' ),
				'schedule'	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/** Create Schedule Activity record **/
	public function create_schedule_activities_post(){
		$postdata 	= $this->post();
		$account_id = !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;
		$schedule_id= !empty( $this->post( 'schedule_id' ) ) 	? ( int ) $this->post( 'schedule_id' ) 	: false;

		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'schedule_id', 'Site ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_BAD_REQUEST,
				'message' 			=> 'Invalid request data: ',
				'schedule_activities'	=> NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> 'Invalid main Account ID',
				'schedule_activities' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$activities = $this->job_service->create_schedule_activities( $account_id, $schedule_id, $postdata );

		if( !empty( $activities ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_CREATED,
				'message' 	=> $this->session->flashdata( 'message' ),
				'schedule_activities' 	=> $activities
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status'	=> FALSE,
				'http_code' => REST_Controller::HTTP_BAD_REQUEST,
				'message' 	=> $this->session->flashdata( 'message' ),
				'schedule_activities' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/** Update / Edit Activity record **/
	public function update_schedule_activity_post(){
		$postdata 	= $this->post();
		$account_id = !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;
		$activity_id= !empty( $this->post( 'activity_id' ) ) ? ( int ) $this->post( 'activity_id' ) : false;

		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status'  			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_BAD_REQUEST,
				'message' 			=> 'Invalid request data: ',
				'schedule_activity'	=> NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 			=> 'Invalid main Account ID',
				'schedule_activity' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$activity = $this->job_service->update_schedule_activity( $account_id, $activity_id, $postdata );

		if( !empty( $activity ) ){
			$message = [
				'status' 			=> TRUE,
				'http_code' 		=> REST_Controller::HTTP_OK,
				'message' 			=> $this->session->flashdata( 'message' ),
				'schedule_activity' => $activity
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		}else{
			$message = [
				'status'			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_BAD_REQUEST,
				'message' 			=> $this->session->flashdata( 'message' ),
				'schedule_activity' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/** Get list of Activities **/
	public function schedule_activities_get(){

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
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> 'Invalid data: ',
				'schedule_activities' => NULL
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
				'schedule_activities' => NULL,
				'counters' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$activities = $this->job_service->get_schedule_activities( $account_id, $search_term, $where, $order_by, $limit, $offset );

		if( !empty( $activities ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'schedule_activities' => ( !empty( $activities->records ) )  ? $activities->records : ( !empty( $activities ) ? $activities: null ),
				'counters' 	=> ( !empty( $activities->counters ) ) ? $activities->counters : null,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message'	=> 'No activities data found',
				'schedule_activities' => null,
				'counters' 	=> null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/** Delete an existing Activity record **/
	public function delete_activity_post(){
		$postdata 	= $this->post();
		$account_id = !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;

		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_BAD_REQUEST,
				'message'	=> 'Invalid request data: ',
				'schedule_activity'	=> NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> 'Invalid main Account ID',
				'schedule_activity'	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$activity = $this->job_service->delete_activity( $account_id, $postdata );

		if( !empty( $activity ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'schedule_activity'	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		}else{
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> $this->session->flashdata( 'message' ),
				'schedule_activity'	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/**
	* Delete Job Type
	*/
    public function delete_job_type_post(){

		$postdata 					= $this->post();
        $account_id 				= ( !empty( $postdata['account_id'] ) ) 			? (int) $postdata['account_id'] : false ;
        $job_type_id 				= ( !empty( $postdata['job_type_id'] ) ) 			? (int) $postdata['job_type_id'] : false ;

		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'job_type_id', 'Job Type ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $validation_errors,
				'job_type' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID.',
				'job_type' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$job_type = $this->job_service->delete_job_type( $account_id, $job_type_id );

		if( !empty( $job_type ) ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'job_type' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'job_type' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }


	/**
	* Get list of all Job ETA Statuses
	*/
	public function eta_statuses_get(){

		$eta_statuses = eta_statuses();

		if( !empty( $eta_statuses ) ){
			$message = [
				'status' 		=> TRUE,
				'http_code' 	=> REST_Controller::HTTP_OK,
				'message' 		=> 'Job ETA Statuses records found',
				'eta_statuses'  => $eta_statuses
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'message' 		=> 'No records found',
				'eta_statuses'  => NULL
			];
			$this->response( $message, REST_Controller::HTTP_NO_CONTENT );
		}

	}


	/**
	* Get list of all Job Tracking Statuses
	*/
	public function job_tracking_statuses_get(){

		$account_id 	= ( !empty( $this->get( 'account_id' ) ) ) ? (int) $this->get( 'account_id' ) : false ;
		$job_tracking_id 	= ( !empty( $this->get( 'job_tracking_id' ) ) ) ? (int) $this->get( 'job_tracking_id' ) : false ;
		$search_term	= ( !empty( $this->get( 'search_term' ) ) ) ?  trim( urldecode( $this->get( 'search_term' ) ) ) : false ;
		$where 		 	= ( !empty( $this->get( 'where' ) ) ) ? $this->get( 'where' ) : false ;
		$order_by 		= ( !empty( $this->get( 'order_by' ) ) ) ? $this->get( 'order_by' ) : false ;
		$limit		 	= ( $this->get( 'limit' ) ) ? (int) $this->get( 'limit' ) : DEFAULT_LIMIT;
		$offset	 		= ( !empty( $this->get( 'offset' ) ) ) ? (int) $this->get( 'offset' ) : 0 ;

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 				=> FALSE,
				'http_code' 			=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 				=> 'Invalid main Account ID.',
				'job_tracking_statuses' => NULL,
				'counters' 				=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$job_tracking_statuses = $this->job_service->get_job_tracking_statuses( $account_id, $job_tracking_id, $search_term, $where, $order_by, $limit, $offset );

		if( !empty( $job_tracking_statuses ) ){
			$message = [
				'status' 				=> TRUE,
				'http_code' 			=> REST_Controller::HTTP_OK,
				'message' 				=> $this->session->flashdata( 'message' ),
				'job_tracking_statuses' => ( !empty( $job_tracking_statuses->records ) ) ? $job_tracking_statuses->records : null,
				'counters' 				=> ( !empty( $job_tracking_statuses->counters ) ) ? $job_tracking_statuses->counters : null,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 				=> FALSE,
				'http_code' 			=> REST_Controller::HTTP_NO_CONTENT,
				'message'				=> $this->session->flashdata( 'message' ),
				'job_tracking_statuses' => null,
				'counters' 				=> null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/** Create a New Job Tracking Status **/
	public function create_job_tracking_status_post(){
		$job_tracking_status_data  = $this->post();
		$account_id		= (int) $this->post( 'account_id' );
		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'job_tracking_status', 'Job Tracking Status', 'required' );
		$this->form_validation->set_rules( 'job_tracking_desc', 'Job Tracking Description', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			## One of the required fields is invalid
			$message = [
				'status' 				=> FALSE,
				'http_code' 			=> REST_Controller::HTTP_NO_CONTENT,
				'message' 				=> 'Invalid data: ',
				'job_tracking_status' 	=> NULL,
			];

			$message['message'] = (!$account_id)? $message['message'].'account_id, ' : $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 			=> 'Invalid main Account ID.',
				'job_tracking_status' 	=> NULL,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$new_job_tracking_status 	= $this->job_service->create_job_tracking_status( $account_id, $job_tracking_status_data );
		$exists 		= $this->session->flashdata( 'already_exists' );

		if( !empty( $new_job_tracking_status ) ){
			$message = [
				'status' 			=> TRUE,
				'http_code' 		=> REST_Controller::HTTP_CREATED,
				'message' 			=> $this->session->flashdata( 'message' ),
				'job_tracking_status' 	=> $new_job_tracking_status,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
				'message' 			=> $this->session->flashdata( 'message' ),
				'job_tracking_status' 	=> NULL,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/**
	* Update Job Tracking Status record
	*/
	public function update_job_tracking_status_post(){
        $job_tracking_status_data 	= $this->post();
        $account_id 				= ( int ) $this->post( 'account_id' );
        $job_tracking_id 	= ( int ) $this->post( 'job_tracking_id' );

		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'job_tracking_id', 'Job Tracking Status ID', 'required' );
		$this->form_validation->set_rules( 'job_tracking_status', 'Job Tracking Status', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			## One of the required fields is invalid
			$message = [
				'status' 				=> FALSE,
				'http_code' 			=> REST_Controller::HTTP_BAD_REQUEST,
				'message' 				=> 'Invalid data: ',
				'job_tracking_status' 	=> NULL
			];

			$message['message'] = (!$account_id)? $message['message'].'account_id, ': $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 				=> FALSE,
				'http_code' 			=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 				=> 'Invalid main Account ID.',
				'job_tracking_status' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

        ## Validate the Job Tracking Status id.
        if ( $job_tracking_id <= 0 ){
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST );
        }

		## Run update call
		$updated_job_tracking_status = $this->job_service->update_job_tracking_status( $account_id, $job_tracking_id, $job_tracking_status_data );

		if( !empty( $updated_job_tracking_status ) ){
			$message = [
				'status' 				=> TRUE,
				'http_code' 			=> REST_Controller::HTTP_OK,
				'message' 				=> $this->session->flashdata( 'message' ),
				'job_tracking_status' 	=> $updated_job_tracking_status
			];
			$this->response( $message, REST_Controller::HTTP_OK ); // Resource Updated
		}else{
			$message = [
				'status' 				=> FALSE,
				'http_code' 			=> REST_Controller::HTTP_NO_CONTENT,
				'message' 				=> $this->session->flashdata( 'message' ),
				'job_tracking_status' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }



	/**
	* Delete Job Tracking Status
	*/
    public function delete_job_tracking_status_post(){

		$postdata 			= $this->post();
        $account_id 		= ( !empty( $postdata['account_id'] ) ) 	 ? (int) $postdata['account_id'] : false ;
        $job_tracking_id 	= ( !empty( $postdata['job_tracking_id'] ) ) ? (int) $postdata['job_tracking_id'] : false ;

		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'job_tracking_id', 'Job Tracking Status ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 				=> FALSE,
				'http_code' 			=> REST_Controller::HTTP_BAD_REQUEST,
				'message' 				=> $validation_errors,
				'job_tracking_status' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 				=> FALSE,
				'http_code' 			=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 				=> 'Invalid main Account ID.',
				'job_tracking_status' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$job_tracking_status = $this->job_service->delete_job_tracking_status( $account_id, $job_tracking_id );

		if( !empty( $job_tracking_status ) ){
			$message = [
				'status' 				=> TRUE,
				'http_code' 			=> REST_Controller::HTTP_OK,
				'message' 				=> $this->session->flashdata( 'message' ),
				'job_tracking_status' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 				=> FALSE,
				'http_code' 			=> REST_Controller::HTTP_NOT_MODIFIED,
				'message' 				=> $this->session->flashdata( 'message' ),
				'job_tracking_status' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }


	/**
	*	Get list of Activities with associated Evidocs
	**/
	public function schedule_activities_w_evidocs_get(){

		$account_id 	= ( !empty( $this->get( 'account_id' ) ) ) 	? (int) $this->get( 'account_id' ) : false ;
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
				'status' 				=> FALSE,
				'http_code' 			=> REST_Controller::HTTP_NO_CONTENT,
				'message' 				=> 'Invalid errors: '.( $validation_errors ),
				'schedule_activities' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 				=> FALSE,
				'http_code' 			=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 				=> 'Invalid main Account ID.',
				'schedule_activities' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$activities = $this->job_service->get_schedule_activities_w_evidocs( $account_id, $where, $order_by, $limit, $offset );

		if( !empty( $activities ) ){
			$message = [
				'status' 				=> TRUE,
				'http_code' 			=> REST_Controller::HTTP_OK,
				'message' 				=> $this->session->flashdata( 'message' ),
				'schedule_activities' 	=> ( !empty( $activities->records ) )  ? $activities->records : ( !empty( $activities ) ? $activities: null ),
				'uploaded_docs' 		=> ( !empty( $activities->uploaded_docs ) )  ? $activities->uploaded_docs : ( !empty( $uploaded_docs ) ? $uploaded_docs: null ),
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 				=> FALSE,
				'http_code' 			=> REST_Controller::HTTP_NO_CONTENT,
				'message'				=> 'No activities data found',
				'schedule_activities' 	=> null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/**
	* Get The Status of a Single Job
	*/
	public function job_status_get(){
		$account_id   	= $this->get( 'account_id' );
		$job_id    		= $this->get( 'job_id' );
		$job_statuses 	= $this->job_service->get_job_status( $account_id, $job_id );

		if( !empty( $job_statuses ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> 'Job status data found',
				'job_status'=> $job_statuses
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> 'No records found',
				'job_status'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/** Add Required BOM Items **/
	public function add_required_boms_post(){
		$postdata 	 = $this->post();
		$account_id  = $this->post( 'account_id' );
		$job_id  	 = $this->post( 'job_type_id' );
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'job_type_id', 'Job Type ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid Job data: ',
				'required_boms' => NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid main Account ID',
				'required_boms' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		$required_boms = $this->job_service->add_required_boms( $account_id, $job_id, $postdata );

		if( !empty( $required_boms ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'required_boms' => $required_boms
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		}else{
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'required_boms' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/** Remove Required BOM Items from a Job Type **/
	public function remove_required_boms_post(){
		$postdata 	 = $this->post();
		$account_id  = $this->post( 'account_id' );
		$job_type_id = $this->post( 'job_type_id' );
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'job_type_id', 'Job Type ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid Job data: ',
				'required_boms' => NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid main Account ID',
				'required_boms' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$remove_boms = $this->job_service->remove_required_boms( $account_id, $job_type_id, $postdata );

		if( !empty( $remove_boms ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'required_boms' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		}else{
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'required_boms' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/**
	* Get list of all Symptom codes (REF: TESSERACT/SCCI)
	*/
	public function symptom_codes_get(){

		$account_id 	= ( !empty( $this->get( 'account_id' ) ) ) 		? (int) $this->get( 'account_id' ) 					: false ;
		$symptom_code_id= ( !empty( $this->get( 'symptom_code_id' ) ) )	? (int) $this->get( 'symptom_code_id' ) 		: false ;
		$search_term	= ( !empty( $this->get( 'search_term' ) ) ) 	? trim( urldecode( $this->get( 'search_term' ) ) ) 	: false ;
		$where 		 	= ( !empty( $this->get( 'where' ) ) ) 			? $this->get( 'where' ) 							: false ;
		$order_by 		= ( !empty( $this->get( 'order_by' ) ) ) 		? $this->get( 'order_by' ) 							: false ;
		$limit		 	= ( $this->get( 'limit' ) ) 					? (int) $this->get( 'limit' ) 						: DEFAULT_LIMIT;
		$offset	 		= ( !empty( $this->get( 'offset' ) ) ) 			? (int) $this->get( 'offset' ) 						: 0 ;

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> 'Invalid main Account ID.',
				'symptom_codes'	=> NULL,
				'counters' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$symptom_codes = $this->job_service->get_symptom_codes( $account_id, $symptom_code_id, $search_term, $where, $order_by, $limit, $offset );

		if( !empty( $symptom_codes ) ){
			$message = [
				'status' 		=> TRUE,
				'http_code' 	=> REST_Controller::HTTP_OK,
				'message' 		=> $this->session->flashdata( 'message' ),
				'symptom_codes'	=> ( !empty( $symptom_codes->records ) )  ? $symptom_codes->records : null,
				'counters' 		=> ( !empty( $symptom_codes->counters ) ) ? $symptom_codes->counters : null,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'message'		=> $this->session->flashdata( 'message' ),
				'symptom_codes'	=> null,
				'counters' 		=> null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/**
	* Get list of all Fault codes (REF: TESSERACT/SCCI)
	*/
	public function fault_codes_get(){

		$account_id 	= ( !empty( $this->get( 'account_id' ) ) ) 		? (int) $this->get( 'account_id' ) 					: false ;
		$fault_code_id= ( !empty( $this->get( 'fault_code_id' ) ) )	? (int) $this->get( 'fault_code_id' ) 		: false ;
		$search_term	= ( !empty( $this->get( 'search_term' ) ) ) 	? trim( urldecode( $this->get( 'search_term' ) ) ) 	: false ;
		$where 		 	= ( !empty( $this->get( 'where' ) ) ) 			? $this->get( 'where' ) 							: false ;
		$order_by 		= ( !empty( $this->get( 'order_by' ) ) ) 		? $this->get( 'order_by' ) 							: false ;
		$limit		 	= ( $this->get( 'limit' ) ) 					? (int) $this->get( 'limit' ) 						: DEFAULT_LIMIT;
		$offset	 		= ( !empty( $this->get( 'offset' ) ) ) 			? (int) $this->get( 'offset' ) 						: 0 ;

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> 'Invalid main Account ID.',
				'fault_codes'	=> NULL,
				'counters' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$fault_codes = $this->job_service->get_fault_codes( $account_id, $fault_code_id, $search_term, $where, $order_by, $limit, $offset );

		if( !empty( $fault_codes ) ){
			$message = [
				'status' 		=> TRUE,
				'http_code' 	=> REST_Controller::HTTP_OK,
				'message' 		=> $this->session->flashdata( 'message' ),
				'fault_codes'	=> ( !empty( $fault_codes->records ) )  ? $fault_codes->records : null,
				'counters' 		=> ( !empty( $fault_codes->counters ) ) ? $fault_codes->counters : null,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'message'		=> $this->session->flashdata( 'message' ),
				'fault_codes'	=> null,
				'counters' 		=> null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
		/**
	* Get list of all Repair codes (REF: TESSERACT/SCCI)
	*/
	public function repair_codes_get(){

		$account_id 	= ( !empty( $this->get( 'account_id' ) ) ) 		? (int) $this->get( 'account_id' ) 					: false ;
		$repair_code_id= ( !empty( $this->get( 'repair_code_id' ) ) )	? (int) $this->get( 'repair_code_id' ) 		: false ;
		$search_term	= ( !empty( $this->get( 'search_term' ) ) ) 	? trim( urldecode( $this->get( 'search_term' ) ) ) 	: false ;
		$where 		 	= ( !empty( $this->get( 'where' ) ) ) 			? $this->get( 'where' ) 							: false ;
		$order_by 		= ( !empty( $this->get( 'order_by' ) ) ) 		? $this->get( 'order_by' ) 							: false ;
		$limit		 	= ( $this->get( 'limit' ) ) 					? (int) $this->get( 'limit' ) 						: DEFAULT_LIMIT;
		$offset	 		= ( !empty( $this->get( 'offset' ) ) ) 			? (int) $this->get( 'offset' ) 						: 0 ;

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> 'Invalid main Account ID.',
				'repair_codes'	=> NULL,
				'counters' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$repair_codes = $this->job_service->get_repair_codes( $account_id, $repair_code_id, $search_term, $where, $order_by, $limit, $offset );

		if( !empty( $repair_codes ) ){
			$message = [
				'status' 		=> TRUE,
				'http_code' 	=> REST_Controller::HTTP_OK,
				'message' 		=> $this->session->flashdata( 'message' ),
				'repair_codes'	=> ( !empty( $repair_codes->records ) )  ? $repair_codes->records : null,
				'counters' 		=> ( !empty( $repair_codes->counters ) ) ? $repair_codes->counters : null,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'message'		=> $this->session->flashdata( 'message' ),
				'repair_codes'	=> null,
				'counters' 		=> null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	

	/** Get Assigned Jobs By Engineer **/
	public function assigned_jobs_by_engineer_get(){

		$account_id 	= ( !empty( $this->get( 'account_id' ) ) ) 	? (int) $this->get( 'account_id' ) 	: false ;
		$job_date		= ( !empty( $this->get( 'engineer_id' ) ) )	? (int) $this->get( 'engineer_id' ) : false ;
		$engineer_id	= ( !empty( $this->get( 'engineer_id' ) ) )	? (int) $this->get( 'engineer_id' ) : false ;
		$where 		 	= ( !empty( $this->get( 'where' ) ) ) 		? $this->get( 'where' ) 			: false ;
		$order_by 		= ( !empty( $this->get( 'order_by' ) ) ) 	? $this->get( 'order_by' ) 			: false ;

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> 'Invalid main Account ID.',
				'assigned_jobs'	=> NULL,
				'job_types'		=> null,
				'regions'		=> null,
				'counters' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$assigned_jobs = $this->job_service->get_assigned_jobs_by_engineer( $account_id, $engineer_id, $where, $order_by );

		if( !empty( $assigned_jobs ) ){
			$message = [
				'status' 		=> TRUE,
				'http_code' 	=> REST_Controller::HTTP_OK,
				'message' 		=> $this->session->flashdata( 'message' ),
				'assigned_jobs'	=> ( !empty( $assigned_jobs->records ) )  ? $assigned_jobs->records  : null,
				'job_types'		=> ( !empty( $assigned_jobs->job_types ) )  ? $assigned_jobs->job_types  : null,
				'regions'		=> ( !empty( $assigned_jobs->regions ) )  ? $assigned_jobs->regions  : null,
				'counters' 		=> ( !empty( $assigned_jobs->counters ) ) ? $assigned_jobs->counters : null,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'message'		=> $this->session->flashdata( 'message' ),
				'assigned_jobs'	=> null,
				'job_types'		=> null,
				'regions'		=> null,
				'counters' 		=> null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

	}
	
	
	/** Bulk Re-assign Jobs **/
	public function bulk_reassign_jobs_post(){
		$postdata  	= $this->post();
		$account_id					= (int) $this->post( 'account_id' );
		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'jobs_to_reassign[]', 'Jobs to re-assign', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			## One of the required fields is invalid
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'message' 		=> 'Invalid data: ',
				'reassign_jobs' => NULL,
			];

			$message['message'] = (!$account_id)? $message['message'].'account_id, ' : $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> 'Invalid main Account ID.',
				'reassign_jobs' => NULL,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$new_jobs_to_reassign 	= $this->job_service->bulk_reassign_jobs( $account_id, $postdata );
		$exists 		= $this->session->flashdata( 'already_exists' );

		if( !empty( $new_jobs_to_reassign ) ){
			$message = [
				'status' 		=> TRUE,
				'http_code' 	=> REST_Controller::HTTP_CREATED,
				'message' 		=> $this->session->flashdata( 'message' ),
				'reassign_jobs' => $new_jobs_to_reassign,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'message' 		=> $this->session->flashdata( 'message' ),
				'reassign_jobs' => NULL,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/**
	* Search through list of Jobs
	*/
	public function job_search_get(){
		$account_id 	= ( int ) $this->get( 'account_id' );
		$job_id 		= ( int ) $this->get( 'job_id' );
		$limit 		 	= ( !empty( $this->get( 'limit' ) ) )  ? (int) $this->get( 'limit' )  : DEFAULT_LIMIT;
		$offset 	 	= ( !empty( $this->get( 'offset' ) ) ) ? (int) $this->get( 'offset' ) : DEFAULT_OFFSET;
		$where 		 	= $this->get( 'where' );
		$order_by    	= $this->get( 'order_by' );
		$search_term 	= trim( urldecode( $this->get( 'search_term' ) ) );

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID.',
				'job' 		=> NULL,
				'counters' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$job_lookup = $this->job_service->job_search( $account_id, $job_id, $search_term, $where, $order_by, $limit, $offset );

		if( !empty( $job_lookup ) ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'job' 		=> ( !empty( $job_lookup->records ) ) ? $job_lookup->records : null,
				'counters' 	=> ( !empty( $job_lookup->counters ) ) ? $job_lookup->counters : null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'job' 		=> ( !empty( $job_lookup->records ) ) ? $job_lookup->records : null,
				'counters' 	=> ( !empty( $job_lookup->counters ) ) ? $job_lookup->counters : null
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}
	
	
	/**
	* Do an Exact Match Search through list of Jobs
	*/
	public function advanced_job_search_get(){
		$postdata 		= $this->get();
		$account_id 	= ( int ) $this->get( 'account_id' );
		$order_by    	= $this->get( 'order_by' );

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID.',
				'jobs' 		=> NULL,
				'counters' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$advanced_search = $this->job_service->advanced_job_search( $account_id, $postdata, $order_by );

		if( !empty( $advanced_search ) ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'jobs' 		=> ( !empty( $advanced_search ) ) ? $advanced_search : null,
				'counters' 	=> ( !empty( $advanced_search ) ) ? $advanced_search : null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'jobs' 		=> ( !empty( $advanced_search) ) ? $advanced_search : null,
				'counters' 	=> ( !empty( $advanced_search ) ) ? $advanced_search : null
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}
	
	
	/** Get Un-Assigned Jobs By Engineer **/
	public function un_assigned_jobs_get(){

		$account_id 	= ( !empty( $this->get( 'account_id' ) ) ) 	? (int) $this->get( 'account_id' ) 	: false ;
		$where 		 	= ( !empty( $this->get( 'where' ) ) ) 		? $this->get( 'where' ) 			: false ;
		$order_by 		= ( !empty( $this->get( 'order_by' ) ) ) 	? $this->get( 'order_by' ) 			: false ;

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> 'Invalid main Account ID.',
				'un_assigned_jobs'	=> NULL,
				'job_types'		=> null,
				'regions'		=> null,
				'counters' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$un_assigned_jobs = $this->job_service->get_un_assigned_jobs( $account_id, $where, $order_by );

		if( !empty( $un_assigned_jobs ) ){
			$message = [
				'status' 		=> TRUE,
				'http_code' 	=> REST_Controller::HTTP_OK,
				'message' 		=> $this->session->flashdata( 'message' ),
				'un_assigned_jobs'	=> ( !empty( $un_assigned_jobs->records ) )  ? $un_assigned_jobs->records  : null,
				'job_types'		=> ( !empty( $un_assigned_jobs->job_types ) )  ? $un_assigned_jobs->job_types  : null,
				'regions'		=> ( !empty( $un_assigned_jobs->regions ) )  ? $un_assigned_jobs->regions  : null,
				'counters' 		=> ( !empty( $un_assigned_jobs->counters ) ) ? $un_assigned_jobs->counters : null,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'message'		=> $this->session->flashdata( 'message' ),
				'un_assigned_jobs'	=> null,
				'job_types'		=> null,
				'regions'		=> null,
				'counters' 		=> null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

	}
	
	
	/** Get Completed Checklists against a Job **/
	public function completed_checklists_get(){
		
		$account_id 	= ( !empty( $this->get( 'account_id' ) ) ) 	? (int) $this->get( 'account_id' ) 	: false ;
		$job_id 		= ( !empty( $this->get( 'job_id' ) ) ) 		? 	(int) $this->get( 'job_id' ) 		: false ;
		$site_id 		= ( !empty( $this->get( 'site_id' ) ) ) 	? 	(int) $this->get( 'site_id' ) 		: false ;
		$where 		 	= ( !empty( $this->get( 'where' ) ) ) 		? $this->get( 'where' ) 			: false ;
		$order_by 		= ( !empty( $this->get( 'order_by' ) ) ) 	? $this->get( 'order_by' ) 			: false ;

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 				=> FALSE,
				'http_code' 			=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 				=> 'Invalid main Account ID.',
				'completed_checklists'	=> NULL,
				'counters' 				=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$completed_checklists = $this->job_service->get_completed_checklists( $account_id, $job_id, $site_id, $where, $order_by );

		if( !empty( $completed_checklists ) ){
			$message = [
				'status' 				=> TRUE,
				'http_code' 			=> REST_Controller::HTTP_OK,
				'message' 				=> $this->session->flashdata( 'message' ),
				'completed_checklists'	=> ( !empty( $completed_checklists->records ) )  ? $completed_checklists->records  : $completed_checklists,
				'counters' 				=> ( !empty( $completed_checklists->counters ) ) ? $completed_checklists->counters : null,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 				=> FALSE,
				'http_code' 			=> REST_Controller::HTTP_NO_CONTENT,
				'message'				=> $this->session->flashdata( 'message' ),
				'completed_checklists'	=> null,
				'counters' 				=> null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

	}
	
	
	/**
	* Search through list of Checklist
	*/
	public function checklist_search_get(){
		$account_id 	= ( int ) $this->get( 'account_id' );
		$job_id 		= ( int ) $this->get( 'job_id' );
		$limit 		 	= ( !empty( $this->get( 'limit' ) ) )  ? (int) $this->get( 'limit' )  : DEFAULT_LIMIT;
		$offset 	 	= ( !empty( $this->get( 'offset' ) ) ) ? (int) $this->get( 'offset' ) : DEFAULT_OFFSET;
		$where 		 	= $this->get( 'where' );
		$order_by    	= $this->get( 'order_by' );
		$search_term 	= trim( urldecode( $this->get( 'search_term' ) ) );

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID.',
				'checklist' => NULL,
				'counters' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$checklists_lookup = $this->job_service->checklist_search( $account_id, $job_id, $search_term, $where, $order_by, $limit, $offset );

		if( !empty( $checklists_lookup ) ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'checklist' => ( !empty( $checklists_lookup->records ) ) ? $checklists_lookup->records : $checklists_lookup,
				'counters' 	=> ( !empty( $checklists_lookup->counters ) ) ? $checklists_lookup->counters : null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'checklist' => ( !empty( $checklists_lookup->records ) ) ? $checklists_lookup->records : null,
				'counters' 	=> ( !empty( $checklists_lookup->counters ) ) ? $checklists_lookup->counters : null
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}
	
	
	/** Add Required Checklists **/
	public function add_required_checklists_post(){
		$postdata 	 = $this->post();
		$account_id  = !empty( $this->post( 'account_id' ) ) 	? ( int ) $this->post( 'account_id' ) 	: false;
		$job_type_id = !empty( $this->post( 'job_type_id' ) ) 	? ( int ) $this->post( 'job_type_id' ) 	: false;

		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'job_type_id', 'Job Type ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' 				=> FALSE,
				'message' 				=> 'Invalid Job data: ',
				'required_checklists' 	=> NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 				=> FALSE,
				'message' 				=> 'Invalid main Account ID',
				'required_checklists' 	=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		$required_checklists = $this->job_service->add_required_checklists( $account_id, $job_type_id, $postdata );

		if( !empty( $required_checklists ) ){
			$message = [
				'status' 				=> TRUE,
				'message' 				=> $this->session->flashdata( 'message' ),
				'required_checklists' 	=> $required_checklists
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); 
		}else{
			$message = [
				'status' 				=> FALSE,
				'message' 				=> $this->session->flashdata( 'message' ),
				'required_checklists' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	

	/** Remove Required Checklists from a Job Type **/
	public function remove_required_checklist_post(){
		$postdata 	 = $this->post();
		$account_id  = !empty( $this->post( 'account_id' ) ) 	? ( int ) $this->post( 'account_id' ) 	: false;
		$job_type_id = !empty( $this->post( 'job_type_id' ) ) 	? ( int ) $this->post( 'job_type_id' ) 	: false;

		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'job_type_id', 'Job Type ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' 				=> FALSE,
				'message' 				=> 'Invalid Job data: ',
				'required_checklist' 	=> NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 				=> FALSE,
				'message' 				=> 'Invalid main Account ID',
				'required_checklist' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$remove_checklists = $this->job_service->remove_required_checklists( $account_id, $job_type_id, $postdata );

		if( !empty( $remove_checklists ) ){
			$message = [
				'status' 				=> TRUE,
				'message' 				=> $this->session->flashdata( 'message' ),
				'required_checklist' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		}else{
			$message = [
				'status' 				=> FALSE,
				'message' 				=> $this->session->flashdata( 'message' ),
				'required_checklist' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/**
	* Clone Schedule
	*/
	public function clone_schedule_post(){
        $schedule_data 	= $this->post();
        $account_id 	= ( int ) $this->post( 'account_id' );
        $schedule_id 	= ( int ) $this->post( 'schedule_id' );

		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'schedule_id', 'Schedule ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			## One of the required fields is invalid
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_BAD_REQUEST,
				'message' 	=> 'Invalid data: ',
				'schedule'	=> NULL
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
				'schedule'	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

        ## Validate the Schedule id.
        if ( $schedule_id <= 0 ){
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST );
		}

		$schedule = $this->job_service->get_schedules( $account_id, false, [ 'schedule_id' => $schedule_id ] );
		if( !$schedule ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_BAD_REQUEST,
				'message' 	=> $this->session->flashdata( 'message' ),
				'schedule' 	=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	

		$cloned_schedule = $this->job_service->clone_schedule( $account_id, $schedule_id, $schedule_data );

		if( !empty( $cloned_schedule ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'schedule'	=> $cloned_schedule
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> $this->session->flashdata( 'message' ),
				'schedule'	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
	
	
	/**
	* Clone Jobs from a Schedule
	*/
	public function clone_jobs_post(){
        $params 			= $this->post();
        $account_id 		= ( int ) $this->post( 'account_id' );
		$schedule_id 		= ( int ) $this->post( 'schedule_id' );
        $cloned_schedule_id = ( int ) $this->post( 'cloned_schedule_id' );

		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'schedule_id', 'Schedule ID', 'required' );
		$this->form_validation->set_rules( 'cloned_schedule_id', 'Source Schedule ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			## One of the required fields is invalid
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_BAD_REQUEST,
				'message' 	=> 'Invalid data: ',
				'jobs'		=> NULL
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
				'jobs'		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$cloned_jobs = $this->job_service->clone_activity_jobs( $account_id, $schedule_id, $cloned_schedule_id, $params );

		if( !empty( $cloned_jobs ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'jobs'		=> $cloned_jobs
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> $this->session->flashdata( 'message' ),
				'jobs'		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
	
	
	/**
	* Complete Scheduling Process
	*/
	public function complete_scheduling_process_post(){
        $schedule_data 	= $this->post();
        $account_id 	= ( int ) $this->post( 'account_id' );
        $schedule_id 	= ( int ) $this->post( 'schedule_id' );

		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'schedule_id', 'Schedule ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			## One of the required fields is invalid
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_BAD_REQUEST,
				'message' 	=> 'Invalid data: ',
				'schedule'	=> NULL
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
				'schedule'	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

        ## Validate the Schedule id.
        if ( $schedule_id <= 0 ){
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST );
		}

		$schedule = $this->job_service->complete_scheduling_process( $account_id, $schedule_id, $schedule_data );

		if( !empty( $schedule ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'schedule'	=> $schedule
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> $this->session->flashdata( 'message' ),
				'schedule'	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
	
	
	/** Create Schedule record(s) Revised version **/
	public function create_schedules_revised_post(){
		$postdata 		= $this->post();
		$account_id 	= !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;
		$frequency_id 	= !empty( $this->post( 'frequency_id' ) ) ? ( int ) $this->post( 'frequency_id' ) 	: false;
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'frequency_id', 'Frequency ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_BAD_REQUEST,
				'message' 	=> 'Invalid request data: ',
				'schedules'	=> NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> 'Invalid main Account ID',
				'schedules' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$schedules = $this->job_service->create_schedules_revised( $account_id, $frequency_id, $postdata );

		if( !empty( $schedules ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_CREATED,
				'message' 	=> $this->session->flashdata( 'message' ),
				'schedules' => $schedules
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status'	=> FALSE,
				'http_code' => REST_Controller::HTTP_BAD_REQUEST,
				'message' 	=> $this->session->flashdata( 'message' ),
				'schedules' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/** Get Job Assets **/
	public function job_assets_get(){
		
		$account_id 	= ( !empty( $this->get( 'account_id' ) ) ) 	? (int) $this->get( 'account_id' ) 	: false ;
		$job_id 		= ( !empty( $this->get( 'job_id' ) ) ) 		? 	(int) $this->get( 'job_id' ) 		: false ;
		$site_id 		= ( !empty( $this->get( 'site_id' ) ) ) 	? 	(int) $this->get( 'site_id' ) 		: false ;
		$where 		 	= ( !empty( $this->get( 'where' ) ) ) 		? $this->get( 'where' ) 			: false ;
		$order_by 		= ( !empty( $this->get( 'order_by' ) ) ) 	? $this->get( 'order_by' ) 			: false ;

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> 'Invalid main Account ID.',
				'job_assets'	=> NULL,
				'counters' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$job_assets = $this->job_service->get_job_assets( $account_id, $job_id, $where, $order_by );

		if( !empty( $job_assets ) ){
			$message = [
				'status' 		=> TRUE,
				'http_code' 	=> REST_Controller::HTTP_OK,
				'message' 		=> $this->session->flashdata( 'message' ),
				'job_assets'	=> ( !empty( $job_assets->records ) )  ? $job_assets->records  : $job_assets,
				'counters' 				=> ( !empty( $job_assets->counters ) ) ? $job_assets->counters : null,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'message'		=> $this->session->flashdata( 'message' ),
				'job_assets'	=> null,
				'counters' 		=> null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/** Link assets to a Job **/
	public function link_assets_to_job_post(){
		$account_id	= (int) $this->post( 'account_id' );
		$job_id 	= (int) $this->post( 'job_id' );
		$postdata 	= $this->post();

		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'job_id', 'Job ID', 'required' );

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> ( $this->session->flashdata( 'message' ) ) ? $this->session->flashdata( 'message' ) : 'Invalid main Account ID',
				'linked_assets' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$linked_assets 	= $this->job_service->link_job_assets( $account_id, $job_id, $postdata );

		if( !empty( $linked_assets ) ){
			$message = [
				'status' 		=> TRUE,
				'http_code' 	=> REST_Controller::HTTP_OK,
				'message' 		=> $this->session->flashdata( 'message' ),
				'linked_assets' => $linked_assets
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
				'message' 		=> 'No data found',
				'linked_assets' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/** Unlink Job Assets from a Job **/
	public function unlink_job_assets_post(){
		$postdata 	 = $this->post();
		$account_id  = !empty( $this->post( 'account_id' ) )? ( int ) $this->post( 'account_id' ) 	: false;
		$job_id 	 = !empty( $this->post( 'job_id' ) ) 	? ( int ) $this->post( 'job_id' ) 	: false;

		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'job_id', 'Job ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid Job data: ',
				'job_asset'	=> NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID',
				'job_asset' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$remove_job_assets = $this->job_service->unlink_job_assets( $account_id, $job_id, $postdata );

		if( !empty( $remove_job_assets ) ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'job_asset' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		}else{
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'job_asset' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/**
	* Complete Schedule Processing (Revised)
	*/
	public function complete_schedule_processing_revised_post(){
        $schedule_data 	= $this->post();
        $account_id 	= ( int ) $this->post( 'account_id' );
        $schedule_id 	= ( int ) $this->post( 'schedule_id' );

		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'schedule_id', 'Schedule ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			## One of the required fields is invalid
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_BAD_REQUEST,
				'message' 	=> 'Invalid data: ',
				'schedule'	=> NULL
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
				'schedule'	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

        ## Validate the Schedule id.
        if ( $schedule_id <= 0 ){
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST );
		}

		$schedule = $this->job_service->complete_schedule_processing_revised( $account_id, $schedule_id, $schedule_data );

		if( !empty( $schedule ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'schedule'	=> $schedule
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> $this->session->flashdata( 'message' ),
				'schedule'	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
	
}