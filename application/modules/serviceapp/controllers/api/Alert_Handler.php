<?php defined('BASEPATH') OR exit('No direct script access allowed');
 
class Alert_Handler extends REST_Controller {

    function __construct(){
        // Construct the parent class
        parent::__construct();
		$this->load->model('Alert_Handler_model','alert_service');
		$this->load->model('Account_model','account_service');
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
		$this->lang->load('auth');
    }
	
		/**
	* Create new Alert resource 
	*/
	public function create_post(){		
		$alert_data = $this->post();
		$account_id	= (int)$this->post('account_id');
    }

	/** 
	* Update alert resource 
	*/
	public function update_post(){
        $alert_data	= $this->post();
        $alert_id 	= (int) $this->post('alert_id');
        $account_id = (int) $this->post('account_id');
    }

	/** 
	* Get list of all Alerts or single record
	*/
    public function alerts_get(){
		$event_tracking_status_id = $this->get('event_tracking_status_id');
		$packet_id 		= $this->get('packet_id');
		$site_id 		= $this->get('site_id');
		$panel_id 		= $this->get('panel_id');
		$account_id 	= (int)$this->get('account_id');
		$limit 			= (int)$this->get('limit');
		$offset 		= (int)$this->get('offset');
		$filter 		= $this->get('filter');
		
		if( !$account_id ){
			## One of the required fields is invalid
			$message = [
				'status' => FALSE,
				'message' => 'Main Account ID is required',
				'alerts' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'alerts' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$alerts = $this->alert_service->get_alerts( $account_id, $event_tracking_status_id, $packet_id, $site_id, $panel_id, $filter, $limit, $offset );
				
		// Check if the alerts data store contains alerts (in case the database result returns NULL)
		if ($alerts){
			// Set the response and exit
			$this->response([
				'status' => true,
				'message' => $this->session->flashdata('message'),
				'alerts' => $alerts,
			], REST_Controller::HTTP_OK);// OK (200) being the HTTP response code
		}else{
			// Set the response and exit
			$this->response([
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'alerts' => NULL
			], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
		}
        
    }
	
	/**
	* Delete Alert resource
	*/
    public function delete_get(){
        $alert_id 	= (int) $this->get('alert_id');
        $account_id = (int) $this->get('account_id');
		
		if ( $alert_id <= 0 ){
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }
		
		//Check and verify that main acocount is valid
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID',
				'alert' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$delete_alert = $this->alert_service->delete_alert( $account_id, $alert_id );
		if( !empty($delete_alert) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'alert' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'alert' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
    }
	
	
	
	/**
	* Trigger a new Alert manually
	*/
	public function trigger_alert_post(){		
		$account_id	 = (int)$this->post('account_id');
		$packet_id	 = $this->post('packet_id');
		$trigger_type= $this->post('trigger_type');
		
		$this->form_validation->set_rules('packet_id', 'Packet ID', 'required');
		$this->form_validation->set_rules('account_id', 'Account ID', 'required');
		$this->form_validation->set_rules('trigger_type', 'Trigger Type', 'required');
               
		if ($this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}
	
		if( isset($validation_errors) && !empty($validation_errors) ){		
			## One of the required fields is invalid
			$message = [
				'status' => FALSE,
				'message' => 'Invalid Trigger data: ',
				'trigger_alert' => NULL	
			];
			$message['message'] = ( isset($validation_errors) && !empty($validation_errors) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'trigger_alert' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		$trigger_alert = $this->alert_service->trigger_alert( $account_id, $packet_id, $trigger_type );	
		if ($trigger_alert){
			$this->response([
				'status' => true,
				'message' => $this->session->flashdata('message'),
				'trigger_alert' => $trigger_alert,
			], REST_Controller::HTTP_OK);
		}else{
			$this->response([
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'trigger_alert' => NULL
			], REST_Controller::HTTP_OK);
		}
    }
	
	public function site_packets_get(){
		$account_id	= $this->get('account_id');
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'site_packets' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$site_packets = $this->alert_service->get_site_packets( $account_id );	
		if ($site_packets){
			$this->response([
				'status' => true,
				'message' => $this->session->flashdata('message'),
				'site_packets' => $site_packets,
			], REST_Controller::HTTP_OK);
		}else{
			$this->response([
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'site_packets' => NULL
			], REST_Controller::HTTP_OK);
		}
	}  
	
	/** 
	* Get list of all SIA Codes or single record
	*/
    public function sia_codes_get(){
		$code_id 	= (int) $this->get('code_id');
		$sia_code 	= $this->get('sia_code');
		$sia_zone 	= $this->get('sia_zone');
		$sia_type 	= $this->get('sia_type');
		$grouped 	= $this->get('grouped');
		
        $sia_codes  = $this->alert_service->get_sia_codes( $code_id, $sia_code, $sia_zone, $sia_type, $grouped );

		if( !empty( $sia_codes ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'sia_codes' => $sia_codes
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'sia_codes' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
    }
	
	/** Get Site Compliance **/
	public function site_compliance_get(){
		$account_id 				= (int) $this->get('account_id');
		$site_id  					= (int) $this->get('site_id');
		$event_tracking_status_id  		= (int) $this->get('event_tracking_status_id');
		$event_tracking_status_id  	= $this->get('event_tracking_status_id');
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID',
				'site_compliance' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$site_compliance = $this->alert_service->get_site_compliance( $account_id, $site_id, $event_tracking_status_id, $event_tracking_status_id );

		if( !empty( $site_compliance ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message'),
				'site_compliance' => $site_compliance
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); // CREATED (201) being the HTTP response code
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'site_compliance' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}

}
