<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class Channel extends REST_Controller {

    function __construct(){
        parent::__construct();
		$this->load->library( "Ssid_common" );
		$this->load->library( "form_validation" );
		$this->load->library( "email" );
		$this->load->model( "Channel_model", "channel_service" );
    }


	/**
	*	Create new Channel
	*/
	public function create_post(){

		$post_set 				= $this->post();

		$account_id 			= ( !empty( $post_set['account_id'] ) ) ? $post_set['account_id'] : false;
		$channel_name 			= ( !empty( $post_set['channel_name'] ) ) ? $post_set['channel_name'] : false;
		$channel_data			= ( !empty( $post_set['channel_data'] ) ) ? ( $post_set['channel_data'] ) : false;

		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'channel_name', 'Channel Name', 'required' );
		$this->form_validation->set_rules( 'channel_data', 'Channel Data', 'required' );

		if( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Validation errors: '.$validation_errors,
				'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
				'channel' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		//Check and verify that main account is valid
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid main Account ID',
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'channel' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$channel = $this->channel_service->create( $account_id, $channel_name, $channel_data );

		if( !empty( $channel ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'http_code' 	=> REST_Controller::HTTP_OK,
				'channel' 		=> $channel
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); // CREATED (201) being the HTTP response code
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'channel' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }


	/**
	* 	Get Channel(s)
	*/
	public function channel_get(){

		$get_set 		= $this->get();

		$account_id 	= ( !empty( $get_set['account_id'] ) ) ? $get_set['account_id'] : false;
		$where			= ( !empty( $get_set['where'] ) ) ? $get_set['where'] : false;
		$limit 			= ( !empty( $get_set['limit'] ) ) ? $get_set['limit'] : false;
		$offset 		= ( !empty( $get_set['offset'] ) ) ? $get_set['offset'] : false;

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
				'message' 		=> 'Validation errors: '.$validation_errors,
				'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
				'channel' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'channel' 		=> NULL
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$channel = $this->channel_service->get_channel( $account_id, $where, $limit, $offset );

		if( !empty( $channel ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'http_code' 	=> REST_Controller::HTTP_OK,
				'channel' 		=> $channel
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'channel' 		=> false
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/**
	* 	System Channel Lookup - not reviewed yet
	*/
	public function lookup_get(){
		$get_data = $this->get();

		$account_id 	= ( !empty( $get_data['account_id'] ) ) ? ( int ) $get_data['account_id'] : false ;
		$limit 			= ( !empty( $get_data['limit'] ) && ( $get_data['limit'] > 0 ) ) ? ( int ) $get_data['limit'] : false ;
		$offset 		= ( !empty( $get_data['offset'] ) ) ? ( int ) $get_data['offset'] : '' ;
		$where 			= ( !empty( $get_data['where'] ) ) ? $get_data['where'] : '' ;
		$order_by 		= ( !empty( $get_data['order_by'] ) ) ? $get_data['order_by'] : '' ;
		$search_term 	= ( !empty( $get_data['search_term'] ) ) ? trim( urldecode( $get_data['search_term'] ) ) : '' ;


		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid main Account ID.',
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'channel' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$channel_lookup = $this->channel_service->channel_lookup( $account_id, $search_term, $where, $order_by, $limit, $offset );

		if( !empty( $channel_lookup ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'http_code' 	=> REST_Controller::HTTP_OK,
				'channel' 		=> $channel_lookup
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'channel' 		=>NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/**
	*	Update Channel
	*/
	public function update_post(){

		$post_set 			= $this->post();

		$account_id 		= ( !empty( $post_set['account_id'] ) ) ?  ( int ) $post_set['account_id'] : false;
		$channel_id 		= ( !empty( $post_set['channel_id'] ) ) ?  ( int ) $post_set['channel_id'] : false;
		$channel_data 		= ( !empty( $post_set['channel_data'] ) ) ? $post_set['channel_data'] : false;

		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'channel_id', 'Channel ID', 'required' );
		$this->form_validation->set_rules( 'channel_data', 'Channel Data', 'required' );

		if( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Validation errors: '.$validation_errors,
				'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
				'u_channel' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		//Check and verify that main account is valid
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid main Account ID',
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'u_channel' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$channel_exists = $this->channel_service->get_channel( $account_id, ['channel_id' => $channel_id] );

		if( !$channel_exists ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid Channel ID',
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'u_channel' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$u_channel = $this->channel_service->update_channel( $account_id, $channel_id, $channel_data );

		if( !empty( $u_channel ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'http_code' 	=> REST_Controller::HTTP_OK,
				'u_channel' 	=> $u_channel
			];
			$this->response( $message, REST_Controller::HTTP_OK ); // CREATED (201) being the HTTP response code
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'u_channel' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }



	/**
	*	Delete Channel
	*/
	public function delete_post(){

 		$post_set 		= $this->post();

		$account_id 	= ( !empty( $post_set['account_id'] ) ) ? $post_set['account_id'] : false;
		$channel_id 	= ( !empty( $post_set['channel_id'] ) ) ? $post_set['channel_id'] : false;

		$this->form_validation->set_rules( 'channel_id', 'Channel ID', 'required' );
		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );

		if( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Validation errors: '.$validation_errors,
				'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
				'd_channel' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		//Check and verify that main account is valid
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid main Account ID',
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'd_channel' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$channel_exists = $this->channel_service->get_channel( $account_id, ["channel_id" => $channel_id ] );

		if( !$channel_exists ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> "Incorrect Channel ID",
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'd_channel' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$d_channel = $this->channel_service->delete_channel( $account_id, $channel_id );

		if( !empty( $d_channel ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'http_code' 	=> REST_Controller::HTTP_OK,
				'd_channel' 	=> $d_channel
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); // CREATED (201) being the HTTP response code
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'd_channel' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
	
	
	/**
	* 	Get Territory(ies)
	*/
	public function territories_get(){

		$get_set 		= $this->get();

		$account_id 	= ( !empty( $get_set['account_id'] ) ) ? $get_set['account_id'] : false;
		$territory_id 	= ( !empty( $get_set['territory_id'] ) ) ? $get_set['territory_id'] : false;
		$where			= ( !empty( $get_set['where'] ) ) ? $get_set['where'] : false;
		$unorganized 	= ( !empty( $get_set['unorganized'] ) ) ? $get_set['unorganized'] : false;
		$limit 			= ( !empty( $get_set['limit'] ) ) ? $get_set['limit'] : false;
		$offset 		= ( !empty( $get_set['offset'] ) ) ? $get_set['offset'] : false;

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
				'message' 		=> 'Validation errors: '.$validation_errors,
				'territories' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'territories' 	=> NULL
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$territories = $this->channel_service->get_territories( $account_id, $territory_id, $where, $unorganized, $limit, $offset );

		if( !empty( $territories ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'territories' 	=> $territories
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'territories' 	=> false
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	

	/**
	* 	Get Channel Territory(ies)
	*/
	public function channel_territories_get(){

		$postdata 		= $this->get();

		$account_id 	= ( !empty( $postdata['account_id'] ) ) 	? $postdata['account_id'] : false;
		$channel_id 	= ( !empty( $postdata['channel_id'] ) ) 	? $postdata['channel_id'] : false;
		$where			= ( !empty( $postdata['where'] ) ) 			? $postdata['where'] : false;
		$limit 			= ( !empty( $postdata['limit'] ) ) 			? $postdata['limit'] : false;
		$offset 		= ( !empty( $postdata['offset'] ) ) 		? $postdata['offset'] : false;

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
				'status' 				=> FALSE,
				'message' 				=> 'Validation errors: '.$validation_errors,
				'channel_territories'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 				=> FALSE,
				'message' 				=> $this->session->flashdata( 'message' ),
				'channel_territories'=> NULL
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$channel_territories = $this->channel_service->get_channel_territories( $account_id, $channel_id, $where, $limit, $offset );

		if( !empty( $channel_territories ) ){
			$message = [
				'status' 				=> TRUE,
				'message' 				=> $this->session->flashdata( 'message' ),
				'channel_territories'=> $channel_territories
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 				=> FALSE,
				'message' 				=> $this->session->flashdata( 'message' ),
				'channel_territories'=> false
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/**
	*	Add Territory
	*/
	public function add_territory_post(){

		$post_set 				= $this->post();
		$account_id 			= ( !empty( $post_set['account_id'] ) ) ? ( int ) $post_set['account_id'] : false;
		$channel_id 			= ( !empty( $post_set['channel_id'] ) ) ? $post_set['channel_id'] : false;
		$territories 			= ( !empty( $post_set['territories'] ) ) ? $post_set['territories'] : false;

		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'channel_id', 'Channel ID(s)', 'required' );
		$this->form_validation->set_rules( 'territories', 'Territory(ies)', 'required' );

		if( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Validation errors: '.$validation_errors,
				'new_territory' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		//Check and verify that main account is valid
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid main Account ID',
				'new_territory' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$new_territory = $this->channel_service->add_territory( $account_id, $channel_id, $territories );

		if( !empty( $new_territory ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'new_territory' 	=> $new_territory
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); // CREATED (201) being the HTTP response code
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'new_territory' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
	
	
	/*
	*	Delete territory from the channel
	*/
	public function delete_territory_post(){
		$post_set 			= $this->post();

		$account_id 		= ( !empty( $post_set['account_id'] ) ) ? ( int ) $post_set['account_id'] : false;
		$territory_id 		= ( !empty( $post_set['territory_id'] ) ) ? ( int ) $post_set['territory_id'] : false;

        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'territory_id', 'Territory ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Validation errors: '.$validation_errors,
				'd_territory' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'd_territory' 		=> NULL
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$d_territory	 = $this->channel_service->delete_territory( $account_id, $territory_id );

		if( !empty( $d_territory ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'd_territory' 		=> $d_territory
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'd_territory' 		=> false
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
}