<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class Dashboard extends MX_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model( 'serviceapp/Dashboard_model','dashboard_service' );
		
		if( !$this->identity() ){
			redirect( 'webapp/user/login', 'refresh' );
		}
		
		$this->data_configs = [
			'base_url' 		=> base_url(),
			'root_folder' 	=> basename( base_url() ),
			'api_end_point' => base_url() . SERVICE_END_POINT,
			#'api_end_point' => 'http://77.68.92.77/evident-core/' . SERVICE_END_POINT,
			'account_id' 	=> $this->user->account_id
		];

	}

	function index(){
		
		$data = array_merge( [], $this->data_configs);
		$this->_render_webpage( 'dashboard/main', $data );	
    }

	function site( $site_id = false ){
		$data = array_merge( [], $this->data_configs);
		$this->_render_webpage( 'dashboard/site', $data );
    }

	
	function discipline(){
		$data = array_merge( [], $this->data_configs);
		$this->_render_webpage( 'dashboard/discipline', $data );
    }

	function outcomes(){
		$data = array_merge( [], $this->data_configs);
		$this->_render_webpage( 'dashboard/outcomes', $data );
    }

	function fire(){
		$data = array_merge( [], $this->data_configs);
		$this->_render_webpage( 'dashboard/fire', $data );
	}
	
	function electricity(){
		$data = array_merge( [], $this->data_configs);
		$this->_render_webpage( 'dashboard/electricity', $data );
    }
	
	function security(){
		$data = array_merge( [], $this->data_configs);
		$this->_render_webpage( 'dashboard/security', $data );
    }
	
	function water(){
		$data = array_merge( [], $this->data_configs);
		$this->_render_webpage( 'dashboard/water', $data );
    }
	
	function gas(){
		$data = array_merge( [], $this->data_configs);
		$this->_render_webpage( 'dashboard/gas', $data );
    }

	function specialist(){
		$data = array_merge( [], $this->data_configs);
		$this->_render_webpage( 'dashboard/specialist', $data );
    }

	function building(){
		$data = array_merge( [], $this->data_configs);
		$this->_render_webpage( 'dashboard/building', $data );
    }

	

}


