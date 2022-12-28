<?php // defined('BASEPATH') OR exit('No direct script access allowed');

class ErrorManager extends MX_Controller {

	function __construct(){
		parent::__construct();		
	}
	
	function index(){
		$this->error404();
	}

	public function error404(){
		$this->load->view( 'errors/html/error_404' );
	}
	
	public function error_exception(){
		$this->load->view( 'errors/html/error_exception' );
	}
}