<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends REST_Controller {

    function __construct(){
        // Construct the parent class
        parent::__construct();
		$this->load->model('Report_model','report_service');
    }

	/**
	* 	Get list of all Alerts or single record
	*/
    public function reports_post(){
		$postdata		= $this->post();
		$account_id 	= ( int ) $this->post( 'account_id' );
		$report_type	= $this->post( 'report_type' );
		$limit 			= ( int ) $this->post( 'limit' );
		$offset 		= ( int ) $this->post( 'offset' );

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID.',
				'report' 	=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		$reports = $this->report_service->get_reports( $account_id, $report_type, $postdata, $limit, $offset );
		// Check if the reports data store contains reports (in case the database result returns NULL)
		if ($reports){
			// Set the response and exit
			$this->response([
				'status' 	=> true,
				'message' 	=> $this->session->flashdata('message'),
				'report' 	=> $reports,
			], REST_Controller::HTTP_OK);// OK (200) being the HTTP response code
		}else{
			// Set the response and exit
			$this->response([
				'status' 	=> FALSE,
				'message' 	=> $this->session->flashdata('message'),
				'report' 	=> NULL
			], REST_Controller::HTTP_OK);
		}
    }

	/*
	* 	Get report types setup
	*	This is the first, initial version left here for the legacy reasons
	*/
	public function report_types_setup_get(){
		$account_id  = (int)$this->get('account_id');
		$report_type = $this->get( 'report_type' );

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid main Account ID.',
				'report_setup' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$setup = $this->report_service->get_report_types_setup( $account_id, $report_type );

		if( $setup ){
			$this->response([
				'status' 		=> true,
				'message' 		=> 'Report setup data found',
				'report_setup' 	=> $setup,
			], REST_Controller::HTTP_OK );
		} else {
			$this->response([
				'status' 		=> FALSE,
				'message' 		=> 'No data available',
				'report_setup' 	=> NULL
			], REST_Controller::HTTP_OK );
		}
	}


	/*
	* 	Get report type setup - Version for the Royalty and Basic reports
	* 	This is basic get to pull the available types of the report from the DB
	*/
	public function report_type_get(){

		$get_set 		= $this->get();
		$account_id 	= ( !empty( $get_set['account_id'] ) ) ? $get_set['account_id'] : false;
		$where 			= ( !empty( $get_set['where'] ) ) ? $get_set['where'] : false;

 		$expected_data 	= [
			'account_id' 	=> $account_id,
		];

		$this->form_validation->set_data( $expected_data );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid or missing required Field(s): '.$validation_errors ,
				'type' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID.',
				'type' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$report_type = $this->report_service->get_report_type( $account_id, $where );

		if( $report_type ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'type' 		=> $report_type
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'No data available',
				'type' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/*
	* 	Get report type setup - Version for the Royalty and Basic reports
	* 	This is basic get to pull the available categories of the report from the DB
	*/
	public function report_category_get(){

		$get_set 		= $this->get();
		$account_id 	= ( !empty( $get_set['account_id'] ) ) ? $get_set['account_id'] : false;
		$where 			= ( !empty( $get_set['where'] ) ) ? $get_set['where'] : false;

 		$expected_data 	= [
			'account_id' 	=> $account_id,
		];

		$this->form_validation->set_data( $expected_data );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid or missing required Field(s): '.$validation_errors ,
				'category' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID.',
				'category' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$report_category = $this->report_service->get_report_category( $account_id );

		if( $report_category ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'category' 	=> $report_category
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'No data available',
				'category' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/*
	* 	Get report settings
	*/
	public function setting_get(){
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
				'setting' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'setting' 		=> NULL
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$setting = $this->report_service->get_setting( $account_id, $where, $limit, $offset );

		if( !empty( $setting ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'setting' 		=> $setting
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'setting' 		=> false
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/*
	* 	Get report viewing stats expected files by the provider
	*/
	public function expected_files_get(){
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
				'files' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'files' 		=> NULL
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$expected_files = $this->report_service->get_expected_files( $account_id, $where, $limit, $offset );

		if( !empty( $expected_files ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'files' 		=> $expected_files
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'files' 		=> false
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/**
	* 	Will processed uploaded viewing stats and generate a CSV
	* 	Takes as a parameter a list of uploaded files
	*/
    public function process_viewing_stats_post(){
		$postdata		= $this->post();
		$account_id 	= ( !empty( $postdata['account_id'] ) ) ? ( int ) $postdata['account_id'] : false ;
		$provider_id 	= ( !empty( $postdata['provider_id'] ) ) ? ( int ) $postdata['provider_id'] : false ;
		$month_id 		= ( !empty( $postdata['month_id'] ) ) ? ( int ) $postdata['month_id'] : false ;
		$year 			= ( !empty( $postdata['year'] ) ) ? ( int ) $postdata['year'] : false ;
		$viewing_stats 	= ( !empty( $postdata['viewing_stats'] ) ) ? $postdata['viewing_stats'] : false ;

		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'provider_id', 'Provider ID', 'required' );
		$this->form_validation->set_rules( 'month_id', 'Month ID', 'required' );
		$this->form_validation->set_rules( 'viewing_stats', 'Viewing Stats ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Validation errors: '.$validation_errors,
				'stats' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid main Account ID.',
				'stats' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$stats = $this->report_service->process_viewing_stats( $account_id, $provider_id, $month_id, $year, $viewing_stats );

		if( $stats ){
			$this->response([
				'status' 		=> true,
				'message' 		=> $this->session->flashdata( 'message' ),
				'stats' 		=> $stats,
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'stats' 		=> NULL
			], REST_Controller::HTTP_OK);
		}
    }


	/*
	* 	Update (Royalty) report Setting(s)
	*	Takes an array of settings or just one setting
	*/
	public function update_report_settings_post(){
		$post_set 		= $this->post();
		$account_id 	= ( !empty( $post_set['account_id'] ) ) ? ( int ) $post_set['account_id'] : false;
		$dataset 		= ( !empty( $post_set['dataset'] ) ) ? $post_set['dataset'] : false;

        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'dataset', 'Update data', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid or missing required Field(s): '.$validation_errors ,
				'setting' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID.',
				'setting' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$setting = $this->report_service->update_report_settings( $account_id, $dataset );

		if( $setting ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'setting' 	=> $setting
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'No data available',
				'setting' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/*
	* 	Get Royalty type(s)
	*/
	public function royalty_type_get(){
		$get_set 		= $this->get();
		$account_id 	= ( !empty( $get_set['account_id'] ) ) ? $get_set['account_id'] : false;
		$where 			= ( !empty( $get_set['where'] ) ) ? $get_set['where'] : false;

 		$expected_data 	= [
			'account_id' 		=> $account_id,
		];

		$this->form_validation->set_data( $expected_data );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid or missing required Field(s): '.$validation_errors ,
				'royalty_type' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid main Account ID.',
				'royalty_type' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$royalty_type = $this->report_service->get_royalty_type( $account_id, $where );

		if( $royalty_type ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'royalty_type' 	=> $royalty_type
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'No data available',
				'royalty_type' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/*
	* 	Get Royalty service(s)
	*/
	public function royalty_service_get(){
		$get_set 		= $this->get();
		$account_id 	= ( !empty( $get_set['account_id'] ) ) ? $get_set['account_id'] : false;
		$where 			= ( !empty( $get_set['where'] ) ) ? $get_set['where'] : false;

 		$expected_data 	= [
			'account_id' 		=> $account_id,
		];

		$this->form_validation->set_data( $expected_data );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid or missing required Field(s): '.$validation_errors ,
				'royalty_service' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid main Account ID.',
				'royalty_service' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$royalty_service = $this->report_service->get_royalty_service( $account_id, $where );

		if( $royalty_service ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'royalty_service' 	=> $royalty_service
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'No data available',
				'royalty_service' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/*
	* 	Get Royalty unit(s)
	*/
	public function royalty_unit_get(){
		$get_set 		= $this->get();
		$account_id 	= ( !empty( $get_set['account_id'] ) ) ? $get_set['account_id'] : false;
		$where 			= ( !empty( $get_set['where'] ) ) ? $get_set['where'] : false;

 		$expected_data 	= [
			'account_id' 		=> $account_id,
		];

		$this->form_validation->set_data( $expected_data );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid or missing required Field(s): '.$validation_errors ,
				'royalty_unit' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid main Account ID.',
				'royalty_unit' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$royalty_unit = $this->report_service->get_royalty_unit( $account_id, $where );

		if( $royalty_unit ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'royalty_unit' 		=> $royalty_unit
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'No data available',
				'royalty_unit' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/*
	* 	Get Royalty Setting(s) values against the site
	*/
	public function site_royalty_setting_get(){

		$this->load->model( "site_model", "site_service" );

		$get_set 		= $this->get();
		$account_id 	= ( !empty( $get_set['account_id'] ) ) ? $get_set['account_id'] : false;
		$site_id 		= ( !empty( $get_set['site_id'] ) ) ? ( int ) $get_set['site_id'] : false;
		$where 			= ( !empty( $get_set['where'] ) ) ? $get_set['where'] : false;

 		$expected_data 	= [
			'account_id' 	=> $account_id,
			'site_id' 		=> $site_id,
		];

		$this->form_validation->set_data( $expected_data );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'site_id', 'Site ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 				=> FALSE,
				'message' 				=> 'Invalid or missing required Field(s): '.$validation_errors ,
				'site_royalty_setting' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 				=> FALSE,
				'message' 				=> 'Invalid main Account ID.',
				'site_royalty_setting' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

        // Validate the site id
		$site_exists = $this->site_service->get_sites( $account_id, $site_id );
        if ( ( !$site_id ) || ( $site_id <= 0 ) || ( !$site_exists ) ){
            $this->response( NULL, REST_Controller::HTTP_BAD_REQUEST );
        }

		$site_royalty_setting = $this->report_service->get_site_royalty_setting( $account_id, $site_id, $where );

		if( $site_royalty_setting ){
			$message = [
				'status' 				=> TRUE,
				'message' 				=> $this->session->flashdata( 'message' ),
				'site_royalty_setting' 	=> $site_royalty_setting
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 				=> FALSE,
				'message' 				=> 'No data available',
				'site_royalty_setting' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/*
	* 	Get Royalty Setting(s) values
	*/
	public function settings_value_get(){

		$get_set 		= $this->get();
		$account_id 	= ( !empty( $get_set['account_id'] ) ) ? $get_set['account_id'] : false;
		$where 			= ( !empty( $get_set['where'] ) ) ? $get_set['where'] : false;

 		$expected_data 	= [
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
				'message' 			=> 'Invalid or missing required Field(s): '.$validation_errors ,
				'settings_value' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid main Account ID.',
				'settings_value' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$settings_value = $this->report_service->get_royalty_setting_value( $account_id, $where );

		if( $settings_value ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'settings_value' 	=> $settings_value
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'No data available',
				'settings_value' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}

	/*
	* 	Update/Create Royalty Setting(s) values against the site
	*/
	public function update_site_royalty_setting_post(){

		$post_set 		= $this->post();
		$account_id 	= ( !empty( $post_set['account_id'] ) ) ? $post_set['account_id'] : false;
		$post_data 		= ( !empty( $post_set['post_data'] ) ) ? $post_set['post_data'] : false;

        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'post_data', 'Post Data', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid or missing required Field(s): '.$validation_errors ,
				'site_setting' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid main Account ID.',
				'site_setting' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$site_setting = $this->report_service->update_site_royalty_setting( $account_id, $post_data );

		if( $site_setting ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'site_setting' 		=> $site_setting
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'No data available',
				'site_setting' 		=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/*
	* 	Get Report(s)
	*/
	public function report_get(){

		$get_set 		= $this->get();
		$account_id 	= ( !empty( $get_set['account_id'] ) ) ? $get_set['account_id'] : false;
		$where 			= ( !empty( $get_set['where'] ) ) ? $get_set['where'] : false;

 		$expected_data 	= [
			'account_id' 	=> $account_id,
		];

		$this->form_validation->set_data( $expected_data );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid or missing required Field(s): '.$validation_errors ,
				'report'	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID.',
				'report' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$report = $this->report_service->get_report( $account_id, $where );

		if( $report ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'report' 	=> $report
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 	=> FALSE,
				'message'	=> 'No data available',
				'report' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/*
	* 	Delete Report(s)
	*/
	public function delete_post(){

		$post_set 		= $this->post();
		$account_id 	= ( !empty( $post_set['account_id'] ) ) ? ( int ) $post_set['account_id'] : false;
		$report_id 		= ( !empty( $post_set['report_id'] ) ) ? ( int ) $post_set['report_id'] : false;

        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'report_id', 'Report ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid or missing required Field(s): '.$validation_errors ,
				'd_report'	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID.',
				'd_report' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$d_report = $this->report_service->delete_report( $account_id, $report_id );

		if( $d_report ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'd_report' 	=> $d_report
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 	=> FALSE,
				'message'	=> 'No data available',
				'd_report' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/*
	* 	Get Simply Report(s)
	*/
	public function simple_report_get(){

		$get_set 		= $this->get();
		$account_id 	= ( !empty( $get_set['account_id'] ) ) ? ( int ) $get_set['account_id'] : false;
		$category_group = ( !empty( $get_set['category_group'] ) ) ? $get_set['category_group'] : false;
		$type_id 		= ( !empty( $get_set['type_id'] ) ) ? ( int ) $get_set['type_id'] : false;
		$where 			= ( !empty( $get_set['where'] ) ) ? $get_set['where'] : false;

 		$expected_data 	= [
			'account_id' 		=> $account_id,
			'category_group' 	=> $category_group,
			'type_id' 			=> $type_id,
		];

		$this->form_validation->set_data( $expected_data );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'category_group', 'Category Group', 'required' );
        $this->form_validation->set_rules( 'type_id', 'Type ID', 'required' );

		if( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid or missing Field(s): '.$validation_errors ,
				'report'	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 	=> FALSE,
				'message' 	=> 'Invalid main Account ID.',
				'report' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$simple_report = $this->report_service->get_simple_report( $account_id, $category_group, $type_id, $where );

		if( $simple_report ){
			$message = [
				'status' 	=> TRUE,
				'message' 	=> $this->session->flashdata( 'message' ),
				'report' 	=> $simple_report
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 	=> FALSE,
				'message'	=> 'No data available',
				'report' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
}
