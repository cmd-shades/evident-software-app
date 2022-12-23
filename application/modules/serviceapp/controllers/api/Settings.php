<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );
 
class Settings extends REST_Controller {
	function __construct(){
        // Construct the parent class
        parent::__construct();
		$this->load->model( "Settings_model", "settings_service" );
		$this->load->library( "Ssid_common" );
		$this->load->library( "form_validation" );
		$this->load->library( "email" );
    }

	/** 
	*	Get settings result based on given parameter(s)
	*/
	public function settings_get(){
		
		$get_set 		= $this->get();
		
		$account_id 	= ( !empty( $get_set['account_id'] ) ) ? ( int ) $get_set['account_id'] : false;
		$setting_id		= ( !empty( $get_set['setting_id'] ) ) ? ( int ) $get_set['setting_id'] : false;
		$where			= ( !empty( $get_set['where'] ) ) ? $get_set['where'] : false;
		$limit 			= ( !empty( $get_set['limit'] ) ) ? ( int ) $get_set['limit'] : false;
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
				'message'		=> 'Validation errors: '.$validation_errors,
				'settings' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'settings' 		=> NULL
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}

		## Validate the setting ID.
        if ( !empty( $setting_id ) && ( ( int ) $setting_id <= 0 ) ){
            $message = [
				'status' 		=> FALSE,
				'message' 		=> "Invalid Setting ID",
				'settings' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
        }
		
		$settings = $this->settings_service->get_settings( $account_id, $setting_id, $where, $limit, $offset );

		if( !empty( $settings ) ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'settings' 	=> $settings
			];
			$this->response($message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'settings' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
	
	
	/** 
	*	Get settings result based on given parameter(s)
	*/
	public function setting_names_get(){
		
		$get_set 		= $this->get();
		
		$account_id 		= ( !empty( $get_set['account_id'] ) ) ? ( int ) $get_set['account_id'] : false;
		$setting_name_id	= ( !empty( $get_set['setting_name_id'] ) ) ? ( int ) $get_set['setting_name_id'] : false;
		$where				= ( !empty( $get_set['where'] ) ) ? $get_set['where'] : false;
		$limit 				= ( !empty( $get_set['limit'] ) ) ? ( int ) $get_set['limit'] : false;
		$offset 			= ( !empty( $get_set['offset'] ) ) ? $get_set['offset'] : false;
		
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
				'status' 			=> FALSE,
				'message' 			=> 'Validation errors: '.$validation_errors,
				'setting_names' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'setting_names' 	=> NULL
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}

		## Validate the setting ID.
        if ( !empty( $setting_name_id ) && ( ( int ) $setting_name_id <= 0 ) ){
            $message = [
				'status' 			=> FALSE,
				'message' 			=> "Invalid Setting ID",
				'setting_names' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
        }
		
		$setting_names = $this->settings_service->get_setting_name( $account_id, $setting_name_id, $where, $limit, $offset );

		if( !empty( $setting_names ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'setting_names' 	=> $setting_names
			];
			$this->response($message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'setting_names' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
    }
	
	
	public function update_setting_post(){
		$post_set 		= $this->post();
		
		$account_id 	= ( !empty( $post_set['account_id'] ) ) ? ( int ) $post_set['account_id'] : false;
		$setting_id		= ( !empty( $post_set['setting_id'] ) ) ? ( int ) $post_set['setting_id'] : false;
		$setting_data	= ( !empty( $post_set['setting_data'] ) ) ? $post_set['setting_data'] : false;
		
 		$expected_data = [
			'account_id' 	=> $account_id ,
			'setting_id' 	=> $setting_id ,
			'setting_data' 	=> $setting_data ,
		];

		$this->form_validation->set_data( $expected_data );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'setting_id', 'Setting ID', 'required' );
        $this->form_validation->set_rules( 'setting_data', 'Setting Data', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Validation errors: '.$validation_errors,
				'u_setting' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'u_setting' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		## check the setting exists
		$setting_exists = $this->settings_service->get_settings( $account_id, $setting_id );
		if( ( !$setting_exists ) || empty( $setting_id ) || ( ( int ) $setting_id <= 0 ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> "Invalid Setting ID",
				'u_setting' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$u_setting = $this->settings_service->update_setting( $account_id, $setting_id, $setting_data );

		if( !empty( $u_setting ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'u_setting' 	=> $u_setting
			];
			$this->response($message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'u_setting' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
		
	public function delete_setting_post(){
		$post_set 		= $this->post();
		
		$account_id 	= ( !empty( $post_set['account_id'] ) ) ? ( int ) $post_set['account_id'] : false;
		$setting_id		= ( !empty( $post_set['setting_id'] ) ) ? ( int ) $post_set['setting_id'] : false;
		
 		$expected_data = [
			'account_id' 	=> $account_id ,
			'setting_id' 	=> $setting_id ,
		];

		$this->form_validation->set_data( $expected_data );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'setting_id', 'Setting ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Validation errors: '.$validation_errors,
				'd_setting' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'd_setting' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		## check the setting exists
		$setting_exists = $this->settings_service->get_settings( $account_id, $setting_id );
		if( ( !$setting_exists ) || empty( $setting_id ) || ( ( int ) $setting_id <= 0 ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> "Invalid Setting ID",
				'd_setting' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$d_setting = $this->settings_service->delete_setting( $account_id, $setting_id );

		if( !empty( $d_setting ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'd_setting' 	=> $d_setting
			];
			$this->response($message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'd_setting' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	
	public function create_post(){
		$post_set 		= $this->post();
		
		$account_id 		= ( !empty( $post_set['account_id'] ) ) ? ( int ) $post_set['account_id'] : false;
		$module_id			= ( !empty( $post_set['module_id'] ) ) ? ( int ) $post_set['module_id'] : false;
		$setting_data 		= ( !empty( $post_set['setting_data'] ) ) ? $post_set['setting_data'] : false ;
		$setting_name_data 	= ( !empty( $post_set['setting_name_data'] ) ) ? $post_set['setting_name_data'] : false ;
	
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'module_id', 'Module ID', 'required' );
        $this->form_validation->set_rules( 'setting_data', 'Setting Data', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Validation errors: '.$validation_errors,
				'setting' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'setting' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$setting = $this->settings_service->create_setting( $account_id, $module_id, $setting_name_data, $setting_data );

		if( !empty( $setting ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'setting' 		=> $setting
			];
			$this->response($message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'setting' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
}