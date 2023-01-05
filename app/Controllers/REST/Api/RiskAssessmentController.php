<?php

namespace App\Controllers\REST\Api;

/*@comment: Risk Assessment, abbreviated as RA in all system references */

use App\Adapter\RESTController;
use App\Models\Service\JobModel;
use App\Models\Service\RiskAssessmentModel;

final class RiskAssessmentController extends RESTController
{
	/**
	 * @var \Application\Modules\Service\Models\RiskAssessmentModel
	 */
	private $ra_service;
	/**
	 * @var \Application\Modules\Service\Models\JobModel
	 */
	private $job_service;

	public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->ra_service = new RiskAssessmentModel();
        $this->job_service = new JobModel();
        $this->form_validation->set_error_delimiters(
			$this->config->item('error_start_delimiter', 'ion_auth'),
			$this->config->item('error_end_delimiter', 'ion_auth')
		);
        $this->lang->load('auth');
    }

    /**
    * Get list of all Risk Assessment questions based on assessment type
    */
    public function questions_get()
    {
        $account_id	  = (int) $this->get('account_id');
        $risk_segment = $this->get('risk_segment');
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid main Account ID',
                'ra_questions' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $questions 	= $this->ra_service->get_ra_questions($account_id, $risk_segment);

        if (!empty($questions)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'ra_questions' => $questions
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => 'No records found',
                'ra_questions' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Create a new Risk Assessment (ra) log
    */
    public function create_post()
    {
        $postdata 		= $this->post();
        $account_id	    = (int)$this->post('account_id');
        $doc_type	    = (int)$this->post('doc_type');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('job_id', 'Jojb ID', 'required');
        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid Site data: ',
                'ra_record' => null
            ];
            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main acocount is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID',
                'ra_record' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $ra_record = $this->ra_service->create_ra($account_id, $postdata);

        if (!empty($ra_record)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'ra_record' => $ra_record
            ];
            $this->response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'ra_record' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Get risk assessment record(s) **/
    public function ra_records_get()
    {
        $account_id	 	= (int) $this->get('account_id');
        $job_id 		= (int) $this->get('job_id');
        $assessment_id 	= (int) $this->get('assessment_id');
        $inc_responses 	= (int) $this->get('inc_responses');
        $limit 			= (int) $this->get('limit');
        $offset 		= (int) $this->get('offset');

        $this->form_validation->set_data(['account_id'=>$account_id]);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        //Check and verify that main acocount is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid or missing main Account ID',
                'ra_records' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $ra_records = $this->ra_service->get_ra_records($account_id, $assessment_id, $job_id, $inc_responses, $limit, $offset);

        if (!empty($ra_records)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'ra_records' => $ra_records
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'ra_records' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Check the RA completion status **/
    public function check_ra_status_get()
    {
        $account_id		= (int) $this->get('account_id');
        $assessment_id 	= (int) $this->get('assessment_id');
        $job_id 		= (int) $this->get('job_id');
        $filter 		= trim(urldecode($this->get('filter')));

        $this->form_validation->set_data(['account_id'=>$account_id]);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid main Account ID',
                'ra_status' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $ra_status 	= $this->ra_service->check_ra_status($account_id, $job_id, $assessment_id);

        if (!empty($ra_status)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'ra_status' => $ra_status
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => 'Risk assessment record not found',
                'ra_status' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all Risk or single record /  Search list
    */
    public function risks_get()
    {
        $account_id 	= (!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false ;
        $search_term	= (!empty($this->get('search_term'))) ? trim(urldecode($this->get('search_term'))) : false ;
        $where 		 	= (!empty($this->get('where'))) ? $this->get('where') : false ;
        $order_by 		= (!empty($this->get('order_by'))) ? $this->get('order_by') : false ;
        $limit		 	= ($this->get('limit')) ? $this->get('limit') : DEFAULT_LIMIT;
        $offset	 		= (!empty($this->get('offset'))) ? $this->get('offset') : 0 ;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID.',
                'risks' 	=> null,
                'counters' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $risks = $this->ra_service->get_risks($account_id, $search_term, $where, $order_by, $limit, $offset);

        if (!empty($risks)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'risks' 	=> (!empty($risks->records)) ? $risks->records : $risks,
                'counters' 	=> (!empty($risks->counters)) ? $risks->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message'	=> $this->session->flashdata('message'),
                'risks' 	=> null,
                'counters' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all Job Types that are associated to a specific Risk
    */
    public function associated_job_types_get()
    {
        $account_id = (!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false ;
        $risk_id 	= (!empty($this->get('risk_id'))) ? (int) $this->get('risk_id') : false ;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID.',
                'associated_job_types' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $associated_job_types = $this->ra_service->get_associated_job_types($account_id, $risk_id);

        if (!empty($associated_job_types)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'associated_job_types' 	=> $associated_job_types
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message'	=> $this->session->flashdata('message'),
                'associated_job_types' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Create new Rick resource
    */
    public function create_risk_item_post()
    {
        $risk_item_data	= $this->post();
        $account_id		= (int)$this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('risk_text', 'Rick Name', 'required');
        $this->form_validation->set_rules('risk_code', 'Rick Code', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 		=> 'Invalid data: ',
                'risk_item' 	=> null
            ];

            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 		=> 'Invalid main Account ID.',
                'risk_item' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_risk_item = $this->ra_service->create_risk_item($account_id, $risk_item_data);

        if (!empty($new_risk_item)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_CREATED,
                'message' 		=> $this->session->flashdata('message'),
                'risk_item' 	=> $new_risk_item
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
                'message' 		=> $this->session->flashdata('message'),
                'risk_item' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Update Rick resource
    */
    public function update_risk_item_post()
    {
        $item_data = $this->post();
        $risk_id 	= !empty($this->post('risk_id')) ? (int) $this->post('risk_id') : false;
        $account_id = !empty($this->post('account_id')) ? (int) $this->post('account_id') : false;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('risk_id', 'Rick Item ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' 	=> 'Invalid data: ',
                'risk_item'=> null
            ];

            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID.',
                'risk_item'=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if ($risk_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        $item = $this->ra_service->get_risks($account_id, false, ["risk_id" =>$risk_id]);
        if (!$item) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> $this->session->flashdata('message'),
                'risk_item'=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Run item update
        $updated_risk_item = $this->ra_service->update_risk_item($account_id, $risk_id, $item_data);
        if (!empty($updated_risk_item)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'risk_item'=> $updated_risk_item
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> $this->session->flashdata('message'),
                'risk_item'=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Delete Rick Item resource
    */
    public function delete_risk_item_post()
    {
        $account_id = (int) $this->post('account_id');
        $risk_id 	= (int) $this->post('risk_id');

        if ($risk_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'Invalid main Account ID.',
                'risk_item' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $delete_risk_item = $this->ra_service->delete_risk_item($account_id, $risk_id);

        if (!empty($delete_risk_item)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'risk_item'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NOT_MODIFIED,
                'message' 	=> $this->session->flashdata('message'),
                'risk_item' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
