<?php

namespace Application\Modules\Service\Controllers\Api;
 
class Asset extends REST_Controller {

    function __construct(){
        // Construct the parent class
        parent::__construct();
		$this->load->model('Asset_model','asset_service');		
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
		$this->lang->load('auth');
    }
	
	/**
	* Delete Asset resource
	*/
    public function delete_get(){
        $account_id = (int) $this->get('account_id');
        $asset_id 	= (int) $this->get('asset_id');
		
		if ( $asset_id <= 0 ){
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'asset' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$delete_asset = $this->asset_service->delete_asset( $account_id, $asset_id );
		
		if( !empty($delete_asset) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'asset' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'asset' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
    }
	
	/**
	* Search through list of Assets
	*/
	public function lookup_get(){
		$account_id 		= (int) $this->get('account_id');
		$site_id 			= (int) $this->get('site_id');
		$where 		 		= ( !empty( $this->get('where') ) )? $this->get('where') : [];
		$order_by 			= ( !empty( $this->get('order_by') ) )? $this->get('order_by') : false;
		$limit 		 		= !empty( $this->get('limit') ) ? (int) $this->get('limit') : DEFAULT_LIMIT;
		$offset 			= !empty( $this->get('offset') ) ? (int) $this->get('offset') : DEFAULT_OFFSET;
		$asset_statuses 	= $this->get('asset_statuses');
		$asset_types 		= $this->get('asset_types');
		$asset_categories 	= $this->get('asset_categories');
		$search_term 		= trim( urldecode( $this->get('search_term') ) );
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 	=> 'Invalid main Account ID.',
				'assets' 	=> NULL,
				'counters' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		//Add Site ID to the where
		if( !empty( $site_id ) ){
			$where = convert_to_array( $where );
			$where['asset.site_id'] = $site_id;
		}
		
		$asset_lookup = $this->asset_service->asset_lookup( $account_id, $search_term, $asset_statuses, $asset_types, $asset_categories, $where, $order_by, $limit, $offset );;
		
		if( !empty( $asset_lookup ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'assets' 	=> ( !empty( $asset_lookup->records ) ) 	? $asset_lookup->records : ( !empty( $asset_lookup ) ? $asset_lookup : false ),
				'counters'  => ( !empty( $asset_lookup->counters ) ) 	? $asset_lookup->counters : NULL,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> $this->session->flashdata( 'message' ),
				'assets' 	=> NULL,
				'counters' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

	}
	
	/** 
	* Add an Asset Type
	**/
	public function add_asset_type_post(){
		$asset_type_data = $this->post();
		$account_id		 = (int) $this->post('account_id');
        /**/
		$this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
		#$this->form_validation->set_rules('category_id', 'Asset Category', 'required');
		$this->form_validation->set_rules('asset_type', 'Asset Type', 'required');
		$this->form_validation->set_rules('discipline_id', 'Discipline', 'required');

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}
	
		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			## One of the required fields is invalid
			$message = [
				'status' => FALSE,
				'message' => 'Invalid data: ',
				'asset_type' => NULL
			];
			
			$message['message'] = (!$account_id)? $message['message'].'account_id, ' : $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'asset_type' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$new_asset_type = $this->asset_service->add_asset_type( $account_id, $asset_type_data );

		if( !empty( $new_asset_type ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata( 'message' ),
				'asset_type' => $new_asset_type
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); // CREATED (201) being the HTTP response code
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata( 'message' ),
				'asset_type' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	/** 
	* Edit/ Update an Asset Type
	**/
	public function update_asset_type_post(){
		$asset_type_data = $this->post();
		$account_id		 = (int) $this->post('account_id');
		$this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
		#$this->form_validation->set_rules('category_id', 'Asset Category', 'required');
		$this->form_validation->set_rules('discipline_id', 'Discipline ID', 'required');
		$this->form_validation->set_rules('asset_type', 'Asset Type', 'required');
		$this->form_validation->set_rules('asset_type_id', 'Asset Type ID', 'required');
		$this->form_validation->set_rules('asset_group', 'Asset Group', 'required');

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}
	
		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid data: ',
				'asset_type'=> NULL
			];
			
			$message['message'] = (!$account_id)? $message['message'].'account_id, ' : $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID.',
				'asset_type'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$updated_asset_type = $this->asset_service->update_asset_type( $account_id, $asset_type_data );

		if( !empty( $updated_asset_type ) ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'asset_type'=> $updated_asset_type
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		}else{
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'asset_type'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}

	
	/**
	* Get list of all Asset types
	*/
	public function asset_types_get(){
		
		$account_id 	= ( !empty( $this->get( 'account_id' ) ) ) ? (int) $this->get( 'account_id' ) : false ;
		$asset_type_id 	= ( !empty( $this->get( 'asset_type_id' ) ) ) ? (int) $this->get( 'asset_type_id' ) : false ;
		$search_term	= ( !empty( $this->get( 'search_term' ) ) ) ?  trim( urldecode( $this->get( 'search_term' ) ) ) : false ;
		$where 		 	= ( !empty( $this->get( 'where' ) ) ) ? $this->get( 'where' ) : false ;
		$order_by 		= ( !empty( $this->get( 'order_by' ) ) ) ? $this->get( 'order_by' ) : false ;
		$limit		 	= ( $this->get( 'limit' ) ) ? (int) $this->get( 'limit' ) : DEFAULT_LIMIT;
		$offset	 		= ( !empty( $this->get( 'offset' ) ) ) ? (int) $this->get( 'offset' ) : 0 ;

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> 'Invalid main Account ID.',
				'asset_types' 	=> NULL,
				'counters' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
        $asset_types = $this->asset_service->get_asset_types( $account_id, $asset_type_id, $search_term, $where, $limit, $offset );

		if( !empty( $asset_types ) ){
			$message = [
				'status' 		=> TRUE,
				'http_code' 	=> REST_Controller::HTTP_OK,
				'message' 		=> $this->session->flashdata( 'message' ),
				'asset_types' 	=> ( !empty( $asset_types->records ) ) ? $asset_types->records : null,
				'counters' 		=> ( !empty( $asset_types->counters ) ) ? $asset_types->counters : null,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'message'		=> $this->session->flashdata( 'message' ),
				'asset_types' 	=> null,
				'counters' 		=> null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/**
	* Get list of all asset statuses
	*/
	public function asset_statuses_get(){
		$account_id   = (int) $this->get('account_id');
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'asset_statuses' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$asset_statuses = $this->asset_service->get_asset_statuses( $account_id );
		
		if( !empty($asset_statuses) ){
			$message = [
				'status' => TRUE,
				'message' => 'Asset statuses records found',
				'asset_statuses' => $asset_statuses
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message' => 'No records found',
				'asset_statuses' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}
	
	/** 
	* Add an Asset Status
	**/
	public function add_asset_status_post(){
		$asset_status_data 	= $this->post();
		$account_id		 	= (int) $this->post('account_id');
		
		$this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
		$this->form_validation->set_rules('status_name', 'Status Name', 'required');
		$this->form_validation->set_rules('status_group', 'Status Group', 'required');

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}
	
		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			## One of the required fields is invalid
			$message = [
				'status' => FALSE,
				'message' => 'Invalid data: ',
				'asset_status' => NULL
			];
			
			$message['message'] = (!$account_id)? $message['message'].'account_id, ' : $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'asset_status' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$new_asset_status = $this->asset_service->add_asset_status( $account_id, $asset_status_data );

		if( !empty( $new_asset_status ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata( 'message' ),
				'asset_status' => $new_asset_status
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); // CREATED (201) being the HTTP response code
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata( 'message' ),
				'asset_status' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	/**
	* Get list of all asset locations
	*/
	public function asset_locations_get(){
		$account_id  = (int) $this->get('account_id');
		$asset_id    = (int) $this->get('asset_id');
		$grouped     = (int) $this->get('grouped');

		$required_fields = [
			'account_id' => $account_id
		];

		$this->form_validation->set_data( $required_fields );
        $this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $validation_errors,
				'asset_locations' => NULL
			];			
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'asset_locations' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$asset_locations = $this->ssid_common->get_locations( $account_id, $location_group = 'asset', $asset_id, false, false, false, $grouped );

		if( !empty( $asset_locations ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'asset_locations' => $asset_locations
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message'=> $this->session->flashdata('message'),
				'asset_locations' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}
	
	/**
	* Get list of all asset logs
	*/
	public function asset_change_logs_get(){
		$asset_id   = (int) $this->get('asset_id');
		$account_id = (int) $this->get('account_id');
		

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'asset_logs' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$asset_logs = $this->asset_service->get_asset_change_logs( $account_id, $asset_id );
		
		if( !empty($asset_logs) ){
			$message = [
				'status' => TRUE,
				'message' => 'Asset change log records found',
				'asset_logs' => $asset_logs
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message' => 'No records found',
				'asset_logs' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}
	
	/**
	* Get list of all location groups
	*/
	public function location_groups_get(){
		
		$account_id   = (int) $this->get('account_id');
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'location_groups' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$location_groups = $this->ssid_common->get_location_groups( $account_id );
		
		if( !empty( $location_groups ) ){
			$message = [
				'status' => TRUE,
				'message' => 'Location groups found',
				'location_groups' => $location_groups
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message' => 'No records found',
				'location_groups' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}
	
	/**
	* Create new location
	*/
	public function add_new_location_post(){
		
		$location_data 	= $this->post();
		$account_id		= (int) $this->post('account_id');
		$this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
		$this->form_validation->set_rules('location_group', 'Location Group', 'required');
		$this->form_validation->set_rules('location_name', 'Location Name', 'required');

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}
	
		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			## One of the required fields is invalid
			$message = [
				'status' => FALSE,
				'message' => 'Invalid data: ',
				'location' => NULL
			];
			
			$message['message'] = (!$account_id)? $message['message'].'account_id, ' : $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'location' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$new_location = $this->ssid_common->add_new_location( $account_id, $location_data );

		if( !empty( $new_location ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'location' => $new_location
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); // CREATED (201) being the HTTP response code
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'location' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
    }
	
	/**
	* Get Asset Dashboard Stats
	*/
    public function asset_stats_get(){
        $account_id 	= (int) $this->get('account_id');
        $stat_type 		= $this->get('stat_type');
        $period_days 	= ( $this->get('period_days') ) ? $this->get('period_days') : 90;
        $date_from 		= ( $this->get('date_from') ) ? $this->get('date_from') : false;
		$date_to 		= ( $this->get('date_to') ) ? $this->get('date_to') : date('Y-m-d');

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID',
				'type' => ( !empty( $stat_type ) ) ? $stat_type : 'asset_stats',
				'asset_stats' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$asset_stats = $this->asset_service->get_asset_stats( $account_id, $stat_type, $period_days, $date_from, $date_to );
		
		if( !empty( $asset_stats ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'type' => ( !empty( $stat_type ) ) ? $stat_type : 'asset_stats',
				'asset_stats' => $asset_stats
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message' ),
				'type' => 'asset_stats',
				'asset_stats' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
	
	/**
	* Get all asset EOL Group statuses
	*/
	public function eol_statuses_get(){
		$account_id  = (int) $this->get('account_id');
		$eol_group   = $this->get('eol_group');
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'eol_statuses' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$eol_statuses = $this->asset_service->get_eol_statuses( $account_id, $eol_group );
		
		if( !empty( $eol_statuses ) ){
			$message = [
				'status' => TRUE,
				'message' => 'Records found',
				'eol_statuses' => $eol_statuses
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message' => 'No records found',
				'eol_statuses' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}
	
	
	/** Get List of Attribute Response Types **/
	public function response_types_get(){
		$account_id   = (int) $this->get( 'account_id' );
		$where 		  = $this->get( 'where' );

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid main Account ID.',
				'response_types'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$response_types = $this->asset_service->get_response_types( $account_id, $where );

		if( !empty( $response_types ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> 'Attributes Response types found',
				'response_types'=> $response_types
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'No records found',
				'response_types'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/** Create an Asset Type Attribute **/
	public function add_asset_type_attribute_post(){
		$asset_type_attribute_data 	= $this->post();
		$account_id		  	  		= (int) $this->post('account_id');
		$item_type  	  			= $this->post( 'item_type' );
		
		$this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
		$this->form_validation->set_rules('attribute_name', 'Attribute Name', 'required');
		if( !empty( $item_type ) && $item_type == 'generic' ){
			//$this->form_validation->set_rules('asset_type_id', 'Asset Type ID', 'required');
		} else {
			$this->form_validation->set_rules('asset_type_id', 'Asset Type ID', 'required');
		}

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}
	
		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			$message = [
				'status' 				=> FALSE,
				'message' 				=> 'Invalid data: ',
				'asset_type_attribute' 	=> NULL
			];
			
			$message['message'] = (!$account_id)? $message['message'].'account_id, ' : $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 				=> FALSE,
				'message' 				=> 'Invalid main Account ID',
				'asset_type_attribute' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$asset_type_attribute = $this->asset_service->add_asset_type_attribute( $account_id, $asset_type_attribute_data );

		if( !empty( $asset_type_attribute ) ){
			$message = [
				'status' 				=> TRUE,
				'message' 				=> $this->session->flashdata( 'message' ),
				'asset_type_attribute' 	=> $asset_type_attribute
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		}else{
			$message = [
				'status' 				=> FALSE,
				'message' 				=> $this->session->flashdata('message'),
				'asset_type_attribute' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	
	/** Update Asset Type Attribute **/
	public function update_asset_type_attribute_post(){
		
        $attribute_data 	= $this->post();
        $account_id 		= ( int ) $this->post('account_id');
        $attribute_id 		= ( int ) $this->post('attribute_id');

		$this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
		$this->form_validation->set_rules('attribute_id', 'Attribute ID', 'required');
		$this->form_validation->set_rules('attribute_name', 'Attribute Name', 'required');
		
		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			$message = [
				'status' 				=> FALSE,
				'message' 				=> 'Invalid data: ',
				'asset_type_attribute' 	=> NULL
			];

			$message['message'] = (!$account_id)? $message['message'].'account_id, ': $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 				=> FALSE,
				'message' 				=> 'Invalid main Account ID.',
				'asset_type_attribute' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

        ## Validate the asset type id.
        if ( $attribute_id <= 0 ){
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST );
        }

		## Run update call
		$updated_attribute = $this->asset_service->update_asset_type_attribute( $account_id, $attribute_id, $attribute_data );
		
		if( !empty( $updated_attribute ) ){
			$message = [
				'status' 				=> TRUE,
				'message' 				=> $this->session->flashdata('message'),
				'asset_type_attribute' 	=> $updated_attribute
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 				=> FALSE,
				'message' 				=> $this->session->flashdata('message'),
				'asset_type_attribute' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
	
	
	/**
	* Delete Asset Type Attribute
	*/
    public function delete_asset_type_attribute_post(){
		
		$postdata 					= $this->post();
		
        $account_id 				= ( !empty( $postdata['account_id'] ) ) 			? (int) $postdata['account_id'] : false ;
        $attribute_id 	= ( !empty( $postdata['attribute_id'] ) )? (int) $postdata['attribute_id'] : false ;
        $asset_type_id 				= ( !empty( $postdata['asset_type_id'] ) ) 			? (int) $postdata['asset_type_id'] : false ;
		
		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'attribute_id', 'Asset Type Attribute ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}
	
		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 				=> FALSE,
				'message' 				=> $validation_errors,
				'asset_type_attribute' 	=> NULL 	
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$asset_type_exists = $this->asset_service->get_asset_type_attributes( $account_id, false, $attribute_id );
		
		if ( ( $attribute_id <= 0 ) || ( ! ( ( int ) $attribute_id ) || !$asset_type_exists ) ){
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST );
        }
		
		//If this is this already attached to an Asset Type
		if( ( !empty( $asset_type_exists->asset_type_id ) && empty( $asset_type_id ) ) || ( $asset_type_exists->asset_type_id != $asset_type_id ) ){
			$message = [
				'status' 				=> FALSE,
				'message' 				=> 'You must provide an Asset Type ID for this record! ',
				'asset_type_attribute' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 				=> FALSE,
				'message' 				=> 'Invalid main Account ID.',
				'asset_type_attribute' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$asset_type_attribute = $this->asset_service->delete_asset_type_attribute( $account_id, $asset_type_id, $attribute_id );
		
		if( !empty( $asset_type_attribute ) ){
			$message = [
				'status' 				=> TRUE,
				'message' 				=> $this->session->flashdata( 'message' ),
				'asset_type_attribute' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 				=> FALSE,
				'message' 				=> $this->session->flashdata( 'message' ),
				'asset_type_attribute' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
	
	
	/**
	* Get list of all Attributes based on Asset type
	*/
	public function asset_type_attributes_get(){

		$where 			= ( !empty( $this->get( 'where' ) )  ) 			? $this->get('where') : false ;	
		$account_id 	= ( !empty( $this->get( 'account_id' ) )  ) 	? ( int ) $this->get('account_id') : false ;	
		$asset_type_id 	= ( !empty( $this->get( 'asset_type_id' ) )  ) 	? ( int ) $this->get('asset_type_id') : false ;	
		$attribute_id 	= ( !empty( $this->get( 'attribute_id' ) )  ) 	? ( int ) $this->get('attribute_id') : ( !empty( $where['attribute_id'] ) ? $where['attribute_id'] : false ) ;
		$search_term 	= trim( urldecode( $this->get('search_term') ) );
		$limit	 		= ( $this->get( 'limit' ) ) 					? (int) $this->get( 'limit' ) 		: DEFAULT_LIMIT;
		$offset			= ( $this->get( 'offset' ) ) 					? (int) $this->get( 'offset' )		: DEFAULT_OFFSET;

		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'asset_type_id', 'Asset Type ID', 'required' );

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 				=> FALSE,
				'message' 				=> ( $this->session->flashdata( 'message' ) ) ? $this->session->flashdata( 'message' ) : 'Invalid main Account ID',
				'asset_type_attributes' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$asset_type_attributes 	= $this->asset_service->get_asset_type_attributes( $account_id, $asset_type_id, $attribute_id, $search_term, $where, $limit, $offset );

		if( !empty( $asset_type_attributes ) ){
			$message = [
				'status' 				=> TRUE,
				'message' 				=> $this->session->flashdata( 'message' ),
				'asset_type_attributes' => $asset_type_attributes
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 				=> FALSE,
				'message' 				=> 'No records found',
				'asset_type_attributes' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	/** Link asset(s) to another asset **/
	public function link_assets_post(){
		$postdata 		= $this->post();
		$account_id 	= !empty( $this->post( 'account_id' ) ) 		? ( int ) $this->post( 'account_id' ) 	: false;
		$parent_asset_id= !empty( $this->post( 'parent_asset_id' ) ) 	? ( int ) $this->post( 'parent_asset_id' ) 	: false;

		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'parent_asset_id', 'Parent Asset ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid request data: ',
				'linked_assets' => NULL	
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid main Account ID',
				'linked_assets' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$linked_assets = $this->asset_service->link_assets( $account_id, $parent_asset_id, $postdata );
		
		if( !empty( $linked_assets ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'linked_assets' => $linked_assets
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); 
		}else{
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'linked_assets' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	
	/** Get a list of linked assets **/
	public function linked_assets_get(){
		$account_id			= !empty( $this->get( 'account_id' ) ) 		? (int) $this->get( 'account_id' ) 		: false;
		$asset_id 			= !empty( $this->get( 'asset_id' ) ) 		? (int) $this->get( 'asset_id' ) 		: false;
		$parent_asset_id 	= !empty( $this->get( 'parent_asset_id' ) ) ? (int) $this->get( 'parent_asset_id' ) : false;
		$where 				= !empty( $this->get( 'where' ) ) 			? $this->get( 'where' ) : false;

		$this->form_validation->set_data( ['account_id'=>$account_id, /*'parent_asset_id'=>$parent_asset_id*/ ] );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        #$this->form_validation->set_rules( 'parent_asset_id', 'Region ID', 'required' );
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 			=> ( $this->session->flashdata('message' ) ) ? $this->session->flashdata('message' ) : 'Invalid main Account ID',
				'linked_assets'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$linked_assets 	= $this->asset_service->get_linked_assets( $account_id, $parent_asset_id, $asset_id,$where );
		
		if( !empty( $linked_assets ) ){
			$message = [
				'status' 			=> TRUE,
				'http_code' 		=> REST_Controller::HTTP_OK,
				'message' 			=> $this->session->flashdata('message' ),
				'linked_assets'=> $linked_assets
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
				'message' 			=> 'Linked assets not found',
				'linked_assets'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/** Remove a an asset from another asset **/
	public function unlink_assets_post(){
		$postdata 	= $this->post();
		$account_id = !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;
		$parent_asset_id 	= !empty( $this->post( 'parent_asset_id' ) ) 	? ( int ) $this->post( 'parent_asset_id' ) 	: false;
		
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'parent_asset_id', 'Parent Asset ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid request data: ',
				'unlink_assets' => NULL	
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid main Account ID',
				'unlink_assets' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$unlink_assets = $this->asset_service->unlink_asset( $account_id, $parent_asset_id, $postdata );
		
		if( !empty( $unlink_assets ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'unlink_assets' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		}else{
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'unlink_assets' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	
	/**
	* Create new Asset resource 
	*/
	public function create_asset_post(){
		
		$asset_data 	= $this->post();
		$account_id	 	= (int)$this->post( 'account_id' );
		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'asset_attributes[]', 'Asset attributes', 'required' );
		$this->form_validation->set_rules( 'asset_type_id', 'Asset Type', 'required' );
		
		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}
	
		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid data: ',
				'asset' 	=> NULL
			];
			
			$message['message'] = (!$account_id)? $message['message'].'account_id, ': $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID.',
				'asset' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$new_asset = $this->asset_service->create_asset( $account_id, $asset_data );

		if( !empty($new_asset ) ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'asset' 	=> $new_asset
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		} else {
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata('message'),
				'asset' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
    }
	
	
	/** 
	* Update asset resource 
	*/
	public function update_post(){

        $asset_data = $this->post();
        $asset_id 	= (int) $this->post( 'asset_id' );
        $account_id = (int) $this->post( 'account_id' );
		
		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'asset_id', 'Asset ID', 'required' );		

		if ($this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}
	
		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			## One of the required fields is invalid
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid data: ',
				'asset' 	=> NULL
			];
			
			$message['message'] = ( !$account_id )? $message['message'].'account_id, ': $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID.',
				'asset' 	=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}

        ## Validate the asset id.
        if ( $asset_id <= 0 ){
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
        }
		
		$asset = $this->asset_service->get_assets( $account_id, $asset_id );
		if( !$asset ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'asset' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		## Run asset update
		$updated_asset = $this->asset_service->update_asset( $account_id, $asset_id, $asset_data);
		if( !empty( $updated_asset ) ){		
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'asset' 	=> $updated_asset
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'asset' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
	
	
	/** 
	* Get list of all Assets or single record version 2
	*/
    public function assets_get(){
		$account_id			= ( $this->get( 'account_id' ) ) 		? (int) $this->get( 'account_id' ) 	: false;
		$asset_id			= ( $this->get( 'asset_id' ) ) 			? (int) $this->get( 'asset_id' ) 	: false;
		$asset_unique_id	= ( $this->get( 'asset_unique_id') ) 	? $this->get( 'asset_unique_id' ) 	: false;
		$where		 		= ( $this->get( 'where' ) ) 			? $this->get( 'where' ) 			: false;
		$order_by		 	= ( $this->get( 'order_by' ) ) 			? $this->get( 'order_by' ) 			: false;
		$limit		 		= ( $this->get( 'limit' ) ) 			? (int) $this->get( 'limit' ) 		: DEFAULT_LIMIT;
		$offset	 			= ( $this->get( 'offset' ) ) 			? (int) $this->get( 'offset' )		: DEFAULT_OFFSET;

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> 'Invalid main Account ID',
				'assets' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$assets 		= $this->asset_service->get_assets( $account_id, $asset_id, $asset_unique_id, $where, $order_by, $limit, $offset );

		if( !empty( $assets ) ){
			$message = [
				'status' 	=> TRUE,
				'http_code' => REST_Controller::HTTP_OK,
				'message' 	=> $this->session->flashdata( 'message' ),
				'assets' 	=> $assets
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 	=> FALSE,
				'http_code' => REST_Controller::HTTP_NO_CONTENT,
				'message' 	=> $this->session->flashdata( 'message' ),
				'assets' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
	
	
	/** Get list of all asset sub categories **/
	public function asset_sub_categories_get(){
		$account_id 	= (int) $this->get('account_id');
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> ( $this->session->flashdata('message') ) ? $this->session->flashdata('message') : 'Invalid main Account ID',
				'sub_categories'=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$sub_categories = sub_categories();	
		
		if( !empty($sub_categories) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> 'Sub categories data found',
				'sub_categories'=> $sub_categories
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'No data found',
				'sub_categories'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_NO_CONTENT );
		}
	}
	
	
	/**
	* 	Soft Delete Asset Type, wipe out the attributes
	*/
    public function delete_asset_type_post(){
		
		$post_data = $this->post();
		
        $account_id 	= ( !empty( $post_data['account_id'] ) ) ? (int) $post_data['account_id'] : false ;
        $asset_type_id 	= ( !empty( $post_data['asset_type_id'] ) ) ? (int) $post_data['asset_type_id'] : false ;

		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'asset_type_id', 'Asset Type ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}
	
		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $validation_errors,
				'd_asset_type' 	=> NULL 	## d_asset_type - deleted asset type
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$asset_type_exists = $this->asset_service->get_asset_types( $account_id, $asset_type_id );
		
		if ( ( $asset_type_id <= 0 ) || ( ! ( ( int ) $asset_type_id ) || !$asset_type_exists ) ){
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST ); // BAD_REQUEST (400) being the HTTP response code
        }
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid main Account ID.',
				'd_asset_type' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$d_asset_type = $this->asset_service->delete_asset_type( $account_id, $asset_type_id );
		
		if( !empty( $d_asset_type ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'd_asset_type' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'd_asset_type' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
	
	
	/**
	* Get list of all Asset categories
	*/
	public function asset_categories_get(){
		
		$account_id 	= ( !empty( $this->get( 'account_id' ) ) ) ? (int) $this->get( 'account_id' ) : false ;
		$category_id 	= ( !empty( $this->get( 'category_id' ) ) ) ? (int) $this->get( 'category_id' ) : false ;
		$where 		 	= ( !empty( $this->get( 'where' ) ) ) ? $this->get( 'where' ) : false ;

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 				=> FALSE,
				'message' 				=> 'Invalid main Account ID.',
				'asset_categories' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
        $asset_categories = $this->asset_service->get_asset_categories( $account_id, $category_id, $where );

		if( !empty( $asset_categories ) ){
			$message = [
				'status' 				=> TRUE,
				'message' 				=> $this->session->flashdata( 'message' ),
				'asset_categories' 		=> $asset_categories,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 				=> FALSE,
				'message'				=> $this->session->flashdata( 'message' ),
				'asset_categories' 		=> null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	
	/**
	* Get list of all Asset categories
	*/
	public function asset_types_by_category_get(){
		
		$account_id 	= ( !empty( $this->get( 'account_id' ) ) ) ? (int) $this->get( 'account_id' ) : false ;
		$where 		 	= ( !empty( $this->get( 'where' ) ) ) ? $this->get( 'where' ) : false ;

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid main Account ID.',
				'asset_types' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
        $asset_types = $this->asset_service->get_asset_types_by_category( $account_id, $where );

		if( !empty( $asset_types ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'asset_types' 		=> $asset_types,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 			=> FALSE,
				'message'			=> $this->session->flashdata( 'message' ),
				'asset_types' 		=> null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/**
	* /** Get all Assets By Asset Type / Category
	*/
	public function assets_by_asset_type_get(){
		
		$account_id 	= ( !empty( $this->get( 'account_id' ) ) ) ? (int) $this->get( 'account_id' ) : false ;
		$where 		 	= ( !empty( $this->get( 'where' ) ) ) ? $this->get( 'where' ) : false ;

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid main Account ID.',
				'assets' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
        $assets_data = $this->asset_service->get_assets_by_asset_type( $account_id, $where );

		if( !empty( $assets_data ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'assets' 		=> $assets_data
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 		=> FALSE,
				'message'		=> $this->session->flashdata( 'message' ),
				'assets' 		=> null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/**
	* Get list of all Asset Attributes (raw data)
	*/
	public function asset_attributes_get(){

		$account_id 	= ( !empty( $this->get( 'account_id' ) )  ) 	? ( int ) $this->get('account_id') : false ;	
		$where	 		= ( $this->get( 'where' ) ) 					? (int) $this->get( 'where' ) 		: false;
		$limit	 		= ( $this->get( 'limit' ) ) 					? (int) $this->get( 'limit' ) 		: DEFAULT_LIMIT;
		$offset			= ( $this->get( 'offset' ) ) 					? (int) $this->get( 'offset' )		: DEFAULT_OFFSET;

		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 			=> ( $this->session->flashdata( 'message' ) ) ? $this->session->flashdata( 'message' ) : 'Invalid main Account ID',
				'asset_attributes'  => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$asset_attributes 	= $this->asset_service->get_asset_attribute_values2( $account_id, false, false, $where, $limit, $offset );

		if( !empty( $asset_attributes ) ){
			$message = [
				'status' 			=> TRUE,
				'http_code' 		=> REST_Controller::HTTP_OK,
				'message' 			=> $this->session->flashdata( 'message' ),
				'asset_attributes'  => $asset_attributes
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
				'message' 			=> 'No records found',
				'asset_attributes'  => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
}

