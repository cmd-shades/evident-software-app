<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends MX_Controller {

	function __construct(){
		parent::__construct();
	}
	
	function index(){
		redirect('webapp/user/login', 'refresh');
	}
	
	function activate( $activate_str = false ){
		
		if( !empty( $activate_str ) ){
			$activate 	= $this->webapp_service->api_dispatcher( $this->api_end_point.'account/activate_account', ['activation_code'=>$activate_str] );
			$data['activation_data']= ( !empty( $activate ) ) ? $activate : null;
			$this->_render_webpage('account/activate', $data );
		}else{
			redirect('webapp/user/login', 'refresh');
		}
	}
}
	