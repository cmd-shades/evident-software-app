<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends MX_Controller {

	function __construct(){
		parent::__construct();
		if( !$this->identity() ){
			redirect('webapp/user/login', 'refresh'  );
		}		
		$this->options = ['auth_token'=>$this->auth_token];
	}
	
	function index(){		
		echo "Hello, World" . PHP_EOL;
	}
}
	