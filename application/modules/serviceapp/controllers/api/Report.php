<?php defined('BASEPATH') OR exit('No direct script access allowed');
 
class Report extends REST_Controller {

    function __construct(){
        // Construct the parent class
        parent::__construct();
		$this->load->model('Report_model','report_service');
    }

	/** 
	* Get list of all Alerts or single record
	*/
    public function reports_get(){
		$postdata	= $this->get();
		$account_id = (int)$this->get( 'account_id' );
		$report_type= $this->get( 'report_type');
		$limit 		= (int) $this->get( 'limit' );
		$offset 	= (int) $this->get( 'offset' );		
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'report' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$reports = $this->report_service->get_reports( $account_id, $report_type, $postdata, $limit, $offset );
		// Check if the reports data store contains reports (in case the database result returns NULL)
		if ( $reports ){
			// Set the response and exit
			$this->response([
				'status' => true,
				'message' => $this->session->flashdata('message'),
				'report' => $reports,
			], REST_Controller::HTTP_OK);// OK (200) being the HTTP response code
		}else{
			// Set the response and exit
			$this->response([
				'status' => FALSE,
				'message' => $this->session->flashdata('message'),
				'report' => NULL
			], REST_Controller::HTTP_OK);
		}
    }
	
	/*
	* Get report types setup
	*/
	public function report_types_setup_get(){
		$account_id  = (int)$this->get( 'account_id' );
		$report_type = $this->get( 'report_type' );
		$source 	 = ( !empty( $this->get( 'source' ) ) ) ? $this->get( 'source' ) : false;
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'report_setup' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$setup = $this->report_service->get_report_types_setup( $account_id, $report_type, $source );

		if ($setup){
			$this->response([
				'status' => true,
				'message' => 'Report setup data found',
				'report_setup' => $setup,
			], REST_Controller::HTTP_OK);
		}else{
			$this->response([
				'status' => FALSE,
				'message' => 'No data available',
				'report_setup' => NULL
			], REST_Controller::HTTP_OK);
		}
	}


	/*
	* Get Tailored reports report
	*/
	public function tailored_reports_setup_get(){
		$account_id  = (int)$this->get( 'account_id' );
		$report_type = $this->get( 'report_type' );
		$source 	 = ( !empty( $this->get( 'source' ) ) ) ? $this->get( 'source' ) : false;
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'tailored_reports' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$setup = $this->report_service->get_tailored_reports_setup( $account_id, $report_type, $source );

		if( $setup ){
			$this->response([
				'status' => true,
				'message' => 'Report setup data found',
				'tailored_reports' => $setup,
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'status' => FALSE,
				'message' => 'No data available',
				'tailored_reports' => NULL
			], REST_Controller::HTTP_OK);
		}
	}
}
