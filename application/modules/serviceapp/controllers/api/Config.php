<?php defined('BASEPATH') OR exit('No direct script access allowed');
 
class Config extends REST_Controller {

    function __construct(){
        // Construct the parent class
        parent::__construct();
		$this->load->model( 'Config_model','config_service' );
    }

	/**
	* Get list of all Available/Active Confgurable Entries
	*/
    public function config_data_get(){
		$entry_id	 	= !empty( $this->get('entry_id') ) 			? (int) $this->get('entry_id') : false;
		$search_term	= ( !empty( $this->get( 'search_term' ) ) ) ?  trim( urldecode( $this->get( 'search_term' ) ) ) : false ;
		$where 		 	= ( !empty( $this->get( 'where' ) ) ) 		? $this->get( 'where' ) : false ;
		$order_by 		= ( !empty( $this->get( 'order_by' ) ) ) 	? $this->get( 'order_by' ) : false ;
		$limit		 	= ( $this->get( 'limit' ) ) 				? (int) $this->get( 'limit' ) : DEFAULT_LIMIT;
		$offset	 		= ( !empty( $this->get( 'offset' ) ) ) 		? (int) $this->get( 'offset' ) : 0 ;
		
		$config_data = $this->config_service->get_config_data( $entry_id, $search_term, $where, $order_by, $limit, $offset );
		
		if( !empty( $config_data ) ){
			$message = [
				'status' 		=> TRUE,
				'http_code' 	=> REST_Controller::HTTP_OK,
				'message' 		=> $this->session->flashdata('message'),
				'config_data' 	=> !empty( $config_data->records ) ? $config_data->records : $config_data,
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'message' 		=> $this->session->flashdata('message'),
				'config_data' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_NO_CONTENT );
		}
    }
	
	
		/** Create a New Config Entry **/
	public function add_config_entry_post(){
		$config_entry_data 	= $this->post();
		$account_id			= (int) $this->post( 'account_id' );
		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'entry_name', 'Config Entry Name', 'required' );
		$this->form_validation->set_rules( 'entry_url_link', 'Config Entry URL', 'required' );
		$this->form_validation->set_rules( 'entry_img_url', 'Config IMG Url', 'required' );
		$this->form_validation->set_rules( 'entry_description', 'Config Entry Description', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}
		
		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'message' 		=> 'Invalid data: ',
				'config_entry' 	=> NULL,				
			];
			
			$message['message'] = ( !$account_id )? $message['message'].'account_id, ' : $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 		=> 'Invalid main Account ID.',
				'config_entry' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$new_config_entry 	= $this->config_service->add_config_entry( $config_entry_data );
		
		if( !empty( $new_config_entry ) ){
			$message = [
				'status' 		=> TRUE,
				'http_code' 	=> REST_Controller::HTTP_CREATED,
				'message' 		=> $this->session->flashdata( 'message' ),
				'config_entry' 	=> $new_config_entry
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
				'message' 		=> $this->session->flashdata('message'),
				'config_entry' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
}
