<?php

namespace Application\Modules\Service\Controllers\Api;

class Quote extends REST_Controller {

    function __construct(){
        // Construct the parent class
        parent::__construct();
		$this->load->model('Quote_model','quote_service');
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
		$this->lang->load('auth');
    }

	/**
	* Create new Quote resource
	*/
	public function create_post(){

		$quote_data = $this->post();
		$account_id = $this->post('account_id');

		$this->form_validation->set_rules('account_id', 'Main Account ID ', 'required');
		$this->form_validation->set_rules('customer_id', 'Customer ID ', 'required');

		if ($this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' => FALSE,
				'message' => 'Invalid Quote data: ',
				'quote' => NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => ( $this->session->flashdata('message') ) ? $this->session->flashdata('message') : 'Invalid main Account ID',
				'quote' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		$new_quote = $this->quote_service->create_quote( $account_id , $quote_data );

		if( !empty($new_quote) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'quote' => $new_quote
			];
			$this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'quote' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
    }

	/**
	* Update user resource
	*/
	public function update_post(){
        $quote_data		= $this->post();
        $quote_id 		= (int) $this->post( 'quote_id' );
        $account_id 	= (int) $this->post( 'account_id' );
        $customer_id 	= (int) $this->post( 'customer_id' );

		$this->form_validation->set_rules( 'quote_id', 'Quote ID ', 'required' );
		$this->form_validation->set_rules( 'customer_id', 'Customer ID ', 'required' );

		if( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			## One of the required fields is invalid
			$message = [
				'status' => FALSE,
				'message' => 'Invalid Quote data: ',
				'quote' => NULL
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => ( $this->session->flashdata('message') ) ? $this->session->flashdata('message') : 'Invalid main Account ID',
				'quote' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}

        ## Validate the quote id.
        if ( $quote_id <= 0 ){
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

		$quote = $this->quote_service->get_quotes( $account_id, $quote_id );
		if( !$quote ){
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'quote' => NULL
			];
			$this->response($message, REST_Controller::HTTP_NO_CONTENT);
		}

		## Run quote update
		$updated_quote = $this->quote_service->update_quote( $account_id, $quote_id, $quote_data);
		if( !empty($updated_quote) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'quote' => $updated_quote
			];
			$this->response($message, REST_Controller::HTTP_OK); // Resource Updated
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'quote' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
    }

	/**
	* Get list of all Quotes or single record
	*/
    public function quotes_get(){
		$quote_id 	 = (int) $this->get('quote_id');
		$account_id  = (int) $this->get('account_id');
		$customer_id = (int) $this->get('customer_id');

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> ( $this->session->flashdata('message') ) ? $this->session->flashdata('message') : 'Invalid main Account ID',
				'quote' 	=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		$quotes = $this->quote_service->get_quotes( $account_id, $quote_id, $customer_id );

		if( !empty($quotes) ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata('message'),
				'quotes' 	=> $quotes
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata('message'),
				'quotes' 	=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
    }

	/**
	* Delete Quote resource
	*/
    public function delete_get(){

		$postset 		= $this->get();

 		$account_id = ( !empty( $postset['account_id'] ) ) ? ( int ) $postset['account_id'] : false ;
		$quote_id 	= ( !empty( $postset['quote_id'] ) ) ? ( int ) $postset['quote_id'] : false ;

		$expected_data = [
			'account_id' 	=> $account_id,
			'quote_id' 		=> $quote_id
		];

		$this->form_validation->set_data( $expected_data );
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'quote_id', 'Quote ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Validation errors: '.trim( $validation_errors ) ,
				'quote' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if ( $quote_id <= 0 ){
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> ( $this->session->flashdata('message') ) ? $this->session->flashdata('message') : 'Invalid main Account ID',
				'quote' 	=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		$delete_quote = $this->quote_service->delete_quote( $account_id, $quote_id );

		if( !empty($delete_quote) ){
			$message = [
				'status'	=> TRUE,
				'message' 	=> $this->session->flashdata('message'),
				'quote' 	=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata('message'),
				'quote' 	=> NULL
			];
			$this->response($message, REST_Controller::HTTP_NO_CONTENT);
		}
    }


	/**
	* 	Get list of Temporary Items from the Quote
	*/
    public function temp_items_get(){
		$item_id 	 	= ( !empty( $this->get( 'item_id' ) ) ) ? ( int ) $this->get( 'item_id' ) :  false ;
		$account_id 	= ( !empty( $this->get( 'account_id' ) ) ) ? ( int ) $this->get( 'account_id' ) :  false ;

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
				'message' 			=> 'Validation errors: '.trim( $validation_errors ) ,
				'temp_items' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> ( $this->session->flashdata('message') ) ? $this->session->flashdata('message') : 'Invalid main Account ID',
				'temp_items' 		=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		$temp_items = $this->quote_service->get_temp_items( $account_id, $item_id );

		if( !empty( $temp_items ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata('message'),
				'temp_items' 	=> $temp_items
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata('message'),
				'temp_items' 	=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
    }


	/**
	* 	Create new Temp Quote Item
	*/
	public function create_temp_item_post(){

		$post_data 		= $this->post();
		$new_temp_item	= false;

		$account_id 	= ( !empty( $post_data['account_id'] ) ) ? $post_data['account_id'] : false ;
		$temp_item_data = ( !empty( $post_data ) ) ? $post_data : false ;

        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'original_price', 'Original Price', 'required' );
        $this->form_validation->set_rules( 'sell_price', 'Sell Price', 'required' );
        $this->form_validation->set_rules( 'item_name', 'Item Name', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Validation errors: '.trim( $validation_errors ) ,
				'new_temp_item' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'new_temp_item' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$new_temp_item = $this->quote_service->create_temp_item( $account_id, $temp_item_data );

		if( !empty( $new_temp_item ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'new_temp_item' 	=> $new_temp_item
			];

			$this->response( $message, REST_Controller::HTTP_CREATED ); // CREATED (201) being the HTTP response code
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'new_temp_item' 	=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}


	/**
	* 	Delete Quote Temp Item resource
	*/
	public function delete_temp_item_post(){

		$postset 	= $this->post();

 		$account_id 	= ( !empty( $postset['account_id'] ) ) ? ( int ) $postset['account_id'] : false ;
		$temp_item_id 	= ( !empty( $postset['temp_item_id'] ) ) ? ( int ) $postset['temp_item_id'] : false ;
		$delete_all 	= ( !empty( $postset['delete_all'] ) ) ? $postset['delete_all'] : false ;

		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Validation errors: '.trim( $validation_errors ) ,
				'temp_item' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if ( empty( $temp_item_id ) &&  empty( $delete_all ) ){
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST ); // BAD_REQUEST (400) being the HTTP response code
        }

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> ( $this->session->flashdata( 'message' ) ) ? $this->session->flashdata( 'message' ) : 'Invalid main Account ID',
				'temp_item' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$deleted_temp_item = $this->quote_service->delete_temp_item( $account_id, $temp_item_id, $delete_all );

		if( !empty( $deleted_temp_item ) ){
			$message = [
				'status'		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'temp_item' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'temp_item' 	=> NULL
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

		$quick_stats = $this->quote_service->get_quick_stats( $account_id, $where, $limit, $offset );

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
	* 	Get Quote Statuses
	*/
	public function quote_statuses_get(){

		$postset 		= $this->get();

 		$account_id 	= ( !empty( $postset['account_id'] ) ) ? $postset['account_id'] : false ;

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
				'status' 		=> FALSE,
				'message' 		=> 'Invalid or missing Field(s): '.trim( $validation_errors ),
				'statuses' 		=> NULL
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'statuses' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$statuses = $this->quote_service->get_quote_statuses( $account_id );

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

}
