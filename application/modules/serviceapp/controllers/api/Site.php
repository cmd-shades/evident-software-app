<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends REST_Controller {

    function __construct(){
        // Construct the parent class
        parent::__construct();
		$this->load->model( 'Site_model','site_service' );
		$this->load->model( 'Account_model','account_service' );
		$this->form_validation->set_error_delimiters( $this->config->item( 'error_start_delimiter', 'ion_auth' ), $this->config->item( 'error_end_delimiter', 'ion_auth' ) );
		$this->lang->load( 'auth' );
    }

	/**
	*	Create new Site
	*/
	public function create_post(){

		$site_data 	= $this->post();
		$account_id	   = ( int ) $this->post( 'account_id' );
		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'site_name', 'Site Name', 'required' );

		if( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Validation errors: '.$validation_errors,
				'new_site' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		//Check and verify that main account is valid
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID',
				'new_site' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$new_site = $this->site_service->create_site( $account_id, $site_data );

		if( !empty( $new_site ) ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'new_site' 	=> $new_site
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); // CREATED (201) being the HTTP response code
		} else {
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'new_site' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }

	/**
	* 	Update site resource
	*/
	public function update_post(){
		
		$post_set 		= $this->post();
		
        $account_id 	= ( !empty( $post_set['account_id'] ) ) ? ( int ) $post_set['account_id'] : false ; 
        $site_id 		= ( !empty( $post_set['site_id'] ) ) ? ( int ) $post_set['site_id'] : false ; 
        $update_data 	= ( !empty( $post_set['update_data'] ) ) ? $post_set['update_data'] : false ; 
		
		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'site_id', 'Site ID', 'required' );
		$this->form_validation->set_rules( 'update_data', 'Update Data', 'required' );

		if( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '' ;
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Validation errors: '.$validation_errors,
				'site' 		=> NULL
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}

        ## Validate the site id.
		$site_exists = $this->site_service->get_sites( $account_id, $site_id );
        if ( ( !$site_id ) || ( $site_id <= 0 ) || ( !$site_exists ) ){
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST ); // BAD_REQUEST (400) being the HTTP response code
        }

		//Check and verify that main account is valid
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID',
				'site' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		## Run site update
		$updated_site = $this->site_service->update_site( $account_id, $site_id, $update_data );
		if( !empty( $updated_site ) ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'site' 		=> $updated_site
			];
			$this->response( $message, REST_Controller::HTTP_OK ); // Resource Updated
		} else {
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message'),
				'site' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }

	/** 
	* 	Get list of all Sites or single record
	*/
    public function sites_get(){
		$site_id 		= (int)$this->get('site_id');
		$account_id 	= (int)$this->get('account_id');
		$site_unique_id = trim( urldecode( $this->get('site_unique_id') ) );
		$where 			= ( !empty( $this->get('where') ) ) ? $this->get( 'where' ) : [];
		$order_by		= ( !empty( $this->get('order_by') ) ) ? $this->get( 'order_by' ) : false;
		$limit 			= (int)$this->get('limit');
		$offset 		= (int)$this->get('offset');
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'sites' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
        $sites = $this->site_service->get_sites( $account_id, $site_id, $where, $order_by, $limit, $offset );

		if( !empty( $sites ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'sites' =>$sites
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'sites' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }

	/**
	* 	Delete Site resource
	*/
    public function delete_post(){

		$post_data = $this->post();
        $account_id 	= ( !empty( $post_data['account_id'] ) ) ? ( int ) $post_data['account_id'] : false ;
        $site_id 		= ( !empty( $post_data['site_id'] ) ) ? ( int ) $post_data['site_id'] : false ;

		if ( $site_id <= 0 ){
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST ); // BAD_REQUEST (400) being the HTTP response code
        }

		//Check and verify that main account is valid
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID',
				'site' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$delete_site = $this->site_service->delete_site( $account_id, $site_id );
		if( !empty( $delete_site ) ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata('message'),
				'site' 		=> true
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata('message'),
				'site' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }

	/**
	* Get all contracts attached to the Site
	*/
	public function contracts_get(){
		$account_id = (int) $this->get('account_id');
		$site_id 	= (int) $this->get('site_id');

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'site_contracts' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$site_contracts = $this->site_service->get_site_contracts( $account_id, $site_id );

		if( !empty($site_contracts) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'site_contracts' => $site_contracts
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'site_contracts' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/**
	* Search through list of Sites
	*/
	public function lookup_get(){
		$account_id 	= (int) $this->get('account_id');
		$where 		 	= ( !empty( $this->get('where') ) ) ? $this->get('where') : [];
		$order_by    	= $this->get('order_by');
		$limit 		 	= (int) $this->get('limit');
		$offset 	 	= (int) $this->get('offset');
		$block_statuses = $this->get('block_statuses');
		$search_term 	= trim( urldecode( $this->get('search_term') ) );
		$alarmed		= $this->get( 'alarmed' );

		if( !empty( $alarmed ) ){
			$where['alarmed'] = $alarmed;
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'sites' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$site_lookup = $this->site_service->site_lookup( $account_id, $search_term, $block_statuses, $where, $order_by, $limit, $offset );

		if( !empty($site_lookup) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'sites' => $site_lookup
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'sites' =>$site_lookup
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/**
	* Get list of all site logs
	*/
	public function site_change_logs_get(){
		$site_id   = (int) $this->get('site_id');
		$account_id= (int) $this->get('account_id');


		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'site_logs' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$site_logs = $this->site_service->get_site_change_logs( $account_id, $site_id );

		if( !empty($site_logs) ){
			$message = [
				'status' => TRUE,
				'message' => 'Site change log records found',
				'site_logs' => $site_logs
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' => FALSE,
				'message' => 'No records found',
				'site_logs' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/**
	* Get list of all site statuses
	*/
	public function site_statuses_get(){

		$account_id   = (int) $this->get('account_id');

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'site_statuses' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$site_statuses = $this->site_service->get_site_statuses( $account_id );

		if( !empty($site_statuses) ){
			$message = [
				'status' => TRUE,
				'message' => 'Asset statuses records found',
				'site_statuses' => $site_statuses
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' => FALSE,
				'message' => 'No records found',
				'site_statuses' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	
	/** 
	*	Get system type(s) list
	**/
	public function system_types_get(){
		
		$get_data = $this->get();
		
		$account_id   		= ( !empty( $get_data['account_id'] ) ) ? ( int ) $get_data['account_id'] : false ;
		$system_type_id   	= ( !empty( $get_data['system_type_id'] ) ) ? ( int ) $get_data['system_type_id'] : false ;
		$unorganized   		= ( !empty( $get_data['unorganized'] ) ) ? $get_data['unorganized'] : false ;
		
		
		$expected_data = [
			'account_id' => $account_id
		];

		$this->form_validation->set_data( $expected_data );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '' ;
		}
		
		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Validation errors: '.trim( $validation_errors ),
				'system_types' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'system_types' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$system_types = $this->site_service->get_system_types( $account_id, $system_type_id, $unorganized );

		if( !empty( $system_types ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'system_types' 	=> $system_types
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'system_types' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	/** 
	*	Get charge frequency(ies) list
	**/
	public function charge_frequencies_get(){
		
		$get_data = $this->get();
		
		$account_id   			= ( !empty( $get_data['account_id'] ) ) ? ( int ) $get_data['account_id'] : false ;
		$charge_frequency_id   	= ( !empty( $get_data['charge_frequency_id'] ) ) ? ( int ) $get_data['charge_frequency_id'] : false ;
		$unorganized   			= ( !empty( $get_data['unorganized'] ) ) ? $get_data['unorganized'] : false ;
		
		
		$expected_data = [
			'account_id' => $account_id
		];

		$this->form_validation->set_data( $expected_data );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '' ;
		}
		
		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 				=> FALSE,
				'message' 				=> 'Validation errors: '.trim( $validation_errors ),
				'charge_frequencies' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 				=> FALSE,
				'message' 				=> $this->session->flashdata( 'message' ),
				'charge_frequencies' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$charge_frequencies = $this->site_service->get_charge_frequencies( $account_id, $charge_frequency_id, $unorganized );

		if( !empty( $charge_frequencies ) ){
			$message = [
				'status' 				=> TRUE,
				'message' 				=> $this->session->flashdata( 'message' ),
				'charge_frequencies' 	=> $charge_frequencies
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 				=> FALSE,
				'message' 				=> $this->session->flashdata( 'message' ),
				'charge_frequencies' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	
	/** 
	*	Get package(s) list
	**/
	public function package_get(){
		
		$get_data = $this->get();
		
		$account_id   	= ( !empty( $get_data['account_id'] ) ) ? ( int ) $get_data['account_id'] : false ;
		$package_id   	= ( !empty( $get_data['package_id'] ) ) ? ( int ) $get_data['package_id'] : false ;
		$where   		= ( !empty( $get_data['where'] ) ) ? $get_data['where'] : false ;
		$unorganized   	= ( !empty( $get_data['unorganized'] ) ) ? $get_data['unorganized'] : false ;
		
		$expected_data = [
			'account_id' => $account_id
		];

		$this->form_validation->set_data( $expected_data );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '' ;
		}
		
		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Validation errors: '.trim( $validation_errors ),
				'package' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'package' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$package = $this->site_service->get_package( $account_id, $package_id, $where, $unorganized );

		if( !empty( $package ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'package' 		=> $package
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'package' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}



	/** 
	*	Get Operating Company(s) list
	**/
	public function operating_company_get(){
		
		$get_data = $this->get();
		
		$account_id   	= ( !empty( $get_data['account_id'] ) ) ? ( int ) $get_data['account_id'] : false ;
		$company_id   	= ( !empty( $get_data['company_id'] ) ) ? ( int ) $get_data['company_id'] : false ;
		$where   		= ( !empty( $get_data['where'] ) ) ? $get_data['where'] : false ;
		$unorganized   	= ( !empty( $get_data['unorganized'] ) ) ? $get_data['unorganized'] : false ;
		
		$expected_data = [
			'account_id' => $account_id
		];

		$this->form_validation->set_data( $expected_data );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '' ;
		}
		
		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Validation errors: '.trim( $validation_errors ),
				'company' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'company' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$company = $this->site_service->get_operating_company( $account_id, $company_id, $where, $unorganized );

		if( !empty( $company ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'company' 		=> $company
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'company' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
 	/** 
	*	Get time zone(s) list
	**/
	public function time_zones_get(){
		
		$get_data = $this->get();
		
		$account_id   	= ( !empty( $get_data['account_id'] ) ) ? ( int ) $get_data['account_id'] : false ;
		$time_zone_id 	= ( !empty( $get_data['time_zone_id'] ) ) ? ( int ) $get_data['time_zone_id'] : false ;
		$where   		= ( !empty( $get_data['where'] ) ) ? $get_data['where'] : false ;
		$limit   		= ( !empty( $get_data['limit'] ) ) ? $get_data['limit'] : DEFAULT_LIMIT ;
		$offset   		= ( !empty( $get_data['offset'] ) ) ? $get_data['offset'] : DEFAULT_OFFSET ;
		
		$expected_data = [
			'account_id' => $account_id
		];

		$this->form_validation->set_data( $expected_data );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '' ;
		}
		
		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Validation errors: '.trim( $validation_errors ),
				'time_zones' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'time_zones' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$time_zones = $this->site_service->get_time_zones( $account_id, $time_zone_id, $where, $limit, $offset );

		if( !empty( $time_zones ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'time_zones' 	=> $time_zones
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'time_zones' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/** 
	* 	Get Site Room totalizer data
	*/
    public function room_totalizer_get(){
		$account_id 	= !empty( $this->get( 'account_id' ) ) ? (int)$this->get( 'account_id' ) : false;
		$site_id 		= !empty( $this->get( 'site_id' ) ) ? (int)$this->get( 'site_id' ) : false;
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid main Account ID.',
				'room_totalizer'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
        $room_totalizer = $this->site_service->get_room_totalizer( $account_id, $site_id );

		if( !empty( $room_totalizer ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'room_totalizer'=> $room_totalizer
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'room_totalizer'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
	
	
	/**
	* Duplicate site record
	*/
	public function duplicate_site_post(){
		$site_id   = (int) $this->post( 'site_id' );
		$account_id= (int) $this->post( 'account_id' );

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID.',
				'site' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$duplicate_site = $this->site_service->duplicate_site( $account_id, $site_id );

		if( !empty( $duplicate_site ) ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'site' 		=> $duplicate_site
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'No records found',
				'site' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/*
	*	Function to calculate the monthly value of the site
	*/ 
	public function site_value_get(){
		$get_set 		= $this->get();

		$account_id 	= ( !empty( $get_set['account_id'] ) ) ? $get_set['account_id'] : false;
		$site_id 		= ( !empty( $get_set['site_id'] ) ) ? $get_set['site_id'] : false;

 		$expected_data = [
			'account_id' 	=> $account_id ,
			'site_id' 		=> $site_id ,
		];

		$this->form_validation->set_data( $expected_data );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'site_id', 'Site ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid or missing Field(s)',
				'site_value' 		=> NULL
			];
			$message['message'] = 'Validation errors: '.trim( $validation_errors ) . trim( $message['message'] );
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'site_value' 		=> NULL
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$site_exists = $this->site_service->get_sites( $account_id, $site_id );

		if( !$site_exists ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'Incorrect Data: Site ID' ),
				'site_value' 		=> NULL
			];

			$this->response( $message, REST_Controller::HTTP_OK );

		}

		$site_value = $this->site_service->get_site_value( $account_id, $site_id );

		if( !empty( $site_value ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'site_value' 		=> $site_value
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'site_value' 		=> false
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	
	/**
	*	Disable Site
	*/
	public function disable_post(){

		$post_data 		= $this->post();
		$account_id	   	= ( !empty( $post_data['account_id'] ) ) ? ( int ) $post_data['account_id'] : false ;
		$site_id	   	= ( !empty( $post_data['site_id'] ) ) ? ( int ) $post_data['site_id'] : false ;
		$disable_date	= ( !empty( $post_data['disable_date'] ) ) ? $post_data['disable_date'] : false ;
		
		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'site_id', 'Site ID', 'required' );
		$this->form_validation->set_rules( 'disable_date', 'Disable Date', 'required' );

		if( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Validation errors: '.$validation_errors,
				'disabled_site' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		//Check and verify that main account is valid
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid main Account ID',
				'disabled_site' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$disabled_site = $this->site_service->disable_site( $account_id, $site_id, $disable_date );

		if( !empty( $disabled_site ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'disabled_site' 	=> $disabled_site
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); // CREATED (201) being the HTTP response code
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'disabled_site' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
	
	
	/** 
	* 	Get list of all Sites for Distributions Groups
	*/
    public function distribution_sites_get(){
		$site_id 		= (int)$this->get('site_id');
		$account_id 	= (int)$this->get('account_id');
		$where 			= ( !empty( $this->get('where') ) ) ? $this->get( 'where' ) : [];
		$order_by		= ( !empty( $this->get('order_by') ) ) ? $this->get( 'order_by' ) : false;
		$limit 			= (int)$this->get('limit');
		$offset 		= (int)$this->get('offset');
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid main Account ID.',
				'distribution_sites'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
        $sites = $this->site_service->get_distribution_sites( $account_id, $site_id, $where, $order_by, $limit, $offset );

		if( !empty( $sites ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'distribution_sites'=> $sites
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 			 => FALSE,
				'message' 			 => $this->session->flashdata( 'message' ),
				'distribution_sites' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
	
	
	
	
	/*
	*	Function to generate (to mock up) the monthly viewing statistics for the site
	*/ 
	public function generate_viewing_stats_post(){

		$post_set 		= $this->post();

		$account_id 	= ( !empty( $post_set['account_id'] ) ) ? $post_set['account_id'] : false;
		$site_id		= ( !empty( $post_set['site_id'] ) ) ? $post_set['site_id'] : false;

        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'site_id', 'Site ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Validation errors: '.$validation_errors,
				'viewing_stats' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'viewing_stats' 	=> NULL
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$viewing_stats = $this->site_service->generate_viewing_stats( $account_id, $site_id );

		if( !empty( $viewing_stats ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'viewing_stats' 	=> $viewing_stats
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'viewing_stats' 	=> false
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/**
	* 	Update site window (operating months)
	*/
	public function update_window_post(){
		
		$post_set 		= $this->post();
		
        $account_id 	= ( !empty( $post_set['account_id'] ) ) ? ( int ) $post_set['account_id'] : false ; 
        $site_id 		= ( !empty( $post_set['site_id'] ) ) ? ( int ) $post_set['site_id'] : false ; 
        $months 		= ( !empty( $post_set['months'] ) ) ? $post_set['months'] : false ; 
		
		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'site_id', 'Site ID', 'required' );

		if( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '' ;
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Validation errors: '.$validation_errors,
				'window' 	=> NULL
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}

        // Validate the site id.
		$site_exists = $this->site_service->get_sites( $account_id, $site_id );
        if ( ( !$site_id ) || ( $site_id <= 0 ) || ( !$site_exists ) ){
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST ); // BAD_REQUEST (400) being the HTTP response code
        }

		//Check and verify that main account is valid
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID',
				'window'	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$upd_window = $this->site_service->update_window( $account_id, $site_id, $months );
		if( !empty( $upd_window ) ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'window' 	=> $upd_window
			];
			$this->response( $message, REST_Controller::HTTP_OK ); // Resource Updated
		} else {
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message'),
				'window' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
	

	
	/**
	*	get list of sites by providing the product type ID
	*/
	public function site_by_product_type_get(){
		
		$get_data 			= $this->get();
		
		$account_id 		= ( !empty( $get_data['account_id'] ) ) ? ( int ) $get_data['account_id'] : false ;
		$product_type_id 	= ( !empty( $get_data['product_type_id'] ) ) ? ( int ) $get_data['product_type_id'] : false ;
		$where 				= ( !empty( $get_data['where'] ) ) ? $get_data['where'] : false ;
		
		$expected_data = [
			'account_id' 		=> $account_id,
			'product_type_id' 	=> $product_type_id
		];

		$this->form_validation->set_data( $expected_data );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required|is_natural_no_zero' );
        $this->form_validation->set_rules( 'product_type_id', 'product Type ID', 'required|is_natural_no_zero' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '' ;
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID.',
				'site' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$site = $this->site_service->get_site_by_product_type_id( $account_id, $product_type_id, $where );

		if( !empty( $site ) ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'site' 		=> $site
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'site' 		=> false
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
}
