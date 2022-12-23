<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MX_Controller {

	function __construct(){
		parent::__construct();
		if( !$this->identity() ){
			redirect('webapp/user/login', 'refresh');
		}

		$this->options = ['auth_token'=>$this->auth_token];		
	}
	
	function index(){
		$allowed_modules			= $this->webapp_service->api_dispatcher( $this->api_end_point.'access/check_module_access', ['account_id'=>$this->account_id, 'user_id'=>$this->user_id], $this->options );
		$data['permitted_modules'] 	= ( !empty( $allowed_modules->module_access ) ) ? $allowed_modules->module_access : null;
		$data['module_count'] 		= ( !empty( $allowed_modules->module_access ) ) ? count($allowed_modules->module_access) : 0;
		$data['user'] 				= $this->user;
		$this->_render_webpage( 'home/index', $data, false, true );
	}

	function tests(){
		$allowed_modules			= $this->webapp_service->api_dispatcher( $this->api_end_point.'access/check_module_access', ['account_id'=>$this->account_id, 'user_id'=>$this->user_id], $this->options );
		$data['permitted_modules'] 	= ( !empty( $allowed_modules->module_access ) ) ? $allowed_modules->module_access : null;
		$data['module_count'] 		= ( !empty( $allowed_modules->module_access ) ) ? count($allowed_modules->module_access) : 0;
		$data['user'] 				= $this->user;
		$this->_render_webpage( 'home/tests', $data, false, true );
	}
}
	