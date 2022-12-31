<?php

namespace Application\Modules\Service\Controllers\Api;

use App\Adapter\RESTController;
use Application\Modules\Service\Models\TesseractModel;

class Tesseract extends RESTController
{
    public function __construct()
    {
        parent::__construct();
        $this->tesseract_service = new TesseractModel();
    }


    /** User Authentication **/
    public function user_login_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 			=> 'Invalid main Account ID',
                'authenticate_user' => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $authenticate_user = $this->tesseract_service->user_login($account_id, $postdata);

        if (!empty($authenticate_user)) {
            $message = [
                'status' 			=> true,
                'http_code' 		=> REST_Controller::HTTP_OK,
                'message' 			=> $this->session->flashdata('message'),
                'authenticate_user' => (!empty($authenticate_user->records)) ? $authenticate_user->records : (!empty($authenticate_user) ? $authenticate_user : null),
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
                'message' 			=> 'No records found',
                'authenticate_user' => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Check TESS User Authentication **/
    public function authenticate_user_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 			=> 'Invalid main Account ID',
                'authenticate_user' => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $authenticate_user = $this->tesseract_service->authenticate_user($account_id, $postdata);

        if (!empty($authenticate_user)) {
            $message = [
                'status' 			=> true,
                'http_code' 		=> REST_Controller::HTTP_OK,
                'message' 			=> $this->session->flashdata('message'),
                'authenticate_user' => (!empty($authenticate_user->records)) ? $authenticate_user->records : (!empty($authenticate_user) ? $authenticate_user : null),
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
                'message' 			=> 'No records found',
                'authenticate_user' => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Create a New Product On Tess **/
    public function create_serialized_product_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 			=> 'Invalid main Account ID',
                'serialized_product'=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $serialized_product = $this->tesseract_service->create_serialized_product($account_id, $postdata);

        if (!empty($serialized_product)) {
            $message = [
                'status' 			=> true,
                'http_code' 		=> REST_Controller::HTTP_OK,
                'message' 			=> $this->session->flashdata('message'),
                'serialized_product'=> !empty($serialized_product) ? $serialized_product : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
                'message' 			=> 'No records found',
                'serialized_product'=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Update Serialised Product On Tess **/
    public function update_serialized_product_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 			=> 'Invalid main Account ID',
                'serialized_product'=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $serialized_product = $this->tesseract_service->update_serialized_product($account_id, $postdata);

        if (!empty($serialized_product)) {
            $message = [
                'status' 			=> true,
                'http_code' 		=> REST_Controller::HTTP_OK,
                'message' 			=> $this->session->flashdata('message'),
                'serialized_product'=> !empty($serialized_product) ? $serialized_product : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
                'message' 			=> 'No records found',
                'serialized_product'=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Retrieve Serialised Product On Tess **/
    public function retrieve_serialized_product_get()
    {
        $account_id = (int) $this->get('account_id');
        $postdata   = $this->get();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 			=> 'Invalid main Account ID',
                'serialized_product'=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $serialized_product = $this->tesseract_service->retrieve_serialized_product($account_id, $postdata);

        if (!empty($serialized_product)) {
            $message = [
                'status' 			=> true,
                'http_code' 		=> REST_Controller::HTTP_OK,
                'message' 			=> $this->session->flashdata('message'),
                'serialized_product'=> !empty($serialized_product) ? $serialized_product : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
                'message' 			=> 'No records found',
                'serialized_product'=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Create a New Site On Tess **/
    public function create_site_record_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 		=> 'Invalid main Account ID',
                'site_record'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $site_record = $this->tesseract_service->create_site_record($account_id, $postdata);

        if (!empty($site_record)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_OK,
                'message' 		=> $this->session->flashdata('message'),
                'site_record'	=> !empty($site_record) ? $site_record : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
                'message' 		=> 'No records found',
                'site_record'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Update Site Record On Tess **/
    public function update_site_record_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 		=> 'Invalid main Account ID',
                'site_record'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $site_record = $this->tesseract_service->update_site_record($account_id, $postdata);

        if (!empty($site_record)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_OK,
                'message' 		=> $this->session->flashdata('message'),
                'site_record'	=> !empty($site_record) ? $site_record : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
                'message' 		=> 'No records found',
                'site_record'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /** Retrieve Site Record On Tess **/
    public function retrieve_site_record_get()
    {
        $account_id = (int) $this->get('account_id');
        $postdata   = $this->get();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 		=> 'Invalid main Account ID',
                'site_record'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $site_record = $this->tesseract_service->retrieve_site_record($account_id, $postdata);

        if (!empty($site_record)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_OK,
                'message' 		=> $this->session->flashdata('message'),
                'site_record'	=> !empty($site_record) ? $site_record : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
                'message' 		=> 'No records found',
                'site_record'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Create a New Product On Tess **/
    public function create_product_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'product'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $product = $this->tesseract_service->create_product($account_id, $postdata);

        if (!empty($product)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'product'	=> !empty($product) ? $product : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'product'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Update Product Record On Tess **/
    public function update_product_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'product'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $product = $this->tesseract_service->update_product($account_id, $postdata);

        if (!empty($product)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'product'	=> !empty($product) ? $product : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'product'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Retrieve Product Record On Tess **/
    public function retrieve_product_get()
    {
        $account_id = (int) $this->get('account_id');
        $postdata   = $this->get();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'product'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $product = $this->tesseract_service->retrieve_product($account_id, $postdata);

        if (!empty($product)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'product'	=> !empty($product) ? $product : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'product'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


        /** Create a New Job Call On Tess **/
    public function create_job_call_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'job_call'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $job_call = $this->tesseract_service->create_job_call($account_id, $postdata);

        if (!empty($job_call)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'job_call'	=> !empty($job_call) ? $job_call : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'job_call'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Update Job Call Record On Tess **/
    public function update_job_call_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'job_call'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $job_call = $this->tesseract_service->update_job_call($account_id, $postdata);

        if (!empty($job_call)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'job_call'	=> !empty($job_call) ? $job_call : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'job_call'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /** Retrieve Job Call Record On Tess **/
    public function retrieve_job_call_get()
    {
        $account_id = (int) $this->get('account_id');
        $postdata   = $this->get();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'job_call'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $job_call = $this->tesseract_service->retrieve_job_call($account_id, $postdata);

        if (!empty($job_call)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'job_call'	=> !empty($job_call) ? $job_call : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'job_call'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Retrieve Job Task/Responses  - Evidoc Responses On Tess **/
    public function retrieve_responses_get()
    {
        $account_id = (int) $this->get('account_id');
        $postdata   = $this->get();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'responses'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $responses = $this->tesseract_service->retrieve_responses($account_id, $postdata);

        if (!empty($responses)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'responses'	=> !empty($responses) ? $responses : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'responses'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Update Response On Tess **/
    public function update_response_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'checklist'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $checklist = $this->tesseract_service->update_response($account_id, $postdata);

        if (!empty($checklist)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'checklist'	=> $checklist,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'checklist'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Create a New FSR Call On Tess **/
    public function create_old_fsr_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'fsr_call'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $fsr_call = $this->tesseract_service->create_fsr_call($account_id, $postdata);

        if (!empty($fsr_call)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'fsr_call'	=> !empty($fsr_call) ? $fsr_call : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'fsr_call'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Retrieve Job by Call Number **/
    public function job_by_call_number_get($account_id = false, $call_number = false)
    {
        $account_id 	= (int) $this->get('account_id');
        $call_number 	= (!empty($this->get('call_numbers'))) ? $this->get('call_numbers') : (!empty($this->get('call_number')) ? $this->get('call_number') : false);

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'job'		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $job = $this->tesseract_service->get_job_by_call_number($account_id, $call_number);

        if (!empty($job)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'job'		=> !empty($job) ? $job : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> $this->session->flashdata('message'),
                'job'		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Retrieve Jobs by Site Number **/
    public function jobs_by_site_number_get($account_id = false, $site_number = false)
    {
        $params 		= $this->get();
        $account_id 	= (int) $this->get('account_id');
        $site_number   	= !empty($this->get('site_number')) ? $this->get('site_number') : $site_number;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'jobs'		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $jobs = $this->tesseract_service->get_jobs_by_site_number($account_id, $site_number, $params);

        if (!empty($jobs)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'jobs'		=> !empty($jobs) ? $jobs : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'jobs'		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Update Job/Call Record On Tess **/
    public function update_job_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'job'		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $tesseract_job = $this->tesseract_service->update_job($account_id, $postdata);

        if (!empty($tesseract_job)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'job'		=> !empty($tesseract_job) ? $tesseract_job : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'job'		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Get All Jobs for specific Tesseract Sites **/
    public function retrieve_site_jobs_get()
    {
        $account_id 		= (int) $this->get('account_id');
        $site_numbers 		= (!empty($this->get('site_numbers'))) ? $this->get('site_numbers') : (!empty($this->get('site_number')) ? $this->get('site_number') : false);
        $where 		 		= (!empty($this->get('where'))) ? $this->get('where') : [];
        $orderByColumn 		= (!empty($this->get('orderByColumn'))) ? $this->get('orderByColumn') : false;
        $sortBy 			= (!empty($this->get('sortBy'))) ? $this->get('sortBy') : false;
        $limit 		 		= !empty($this->get('limit')) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset 			= !empty($this->get('offset')) ? (int) $this->get('offset') : DEFAULT_OFFSET;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'jobs'		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $jobs = $this->tesseract_service->retrieve_site_jobs($account_id, $site_numbers, $where, $orderByColumn, $sortBy, $limit, $offset);

        if (!empty($jobs)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'jobs'		=> !empty($jobs) ? $jobs : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'jobs'		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Retrieve Site Record by Site Number **/
    public function site_by_site_number_get($account_id = false, $site_number = false)
    {
        $account_id 	= (int) $this->get('account_id');
        $site_number   	= !empty($this->get('site_number')) ? $this->get('site_number') : $site_number;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'site'		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $site = $this->tesseract_service->get_site_by_site_number($account_id, $site_number);

        if (!empty($site)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'site'		=> !empty($site) ? $site : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'site'		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Create a New Blob **/
    public function create_blob_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'blob'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $blob = $this->tesseract_service->create_blob($account_id, $postdata);

        if (!empty($blob)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'blob'		=> !empty($blob) ? $blob : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'blob'		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** CUpdate an Existing Blob **/
    public function update_blob_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'blob'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $blob = $this->tesseract_service->update_blob($account_id, $postdata);

        if (!empty($blob)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'blob'		=> !empty($blob) ? $blob : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'blob'		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Send Attachment **/
    public function send_attachment_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'attachment'=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $attachment = $this->tesseract_service->send_attachment($account_id, $postdata);

        if (!empty($attachment)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'attachment'=> !empty($attachment) ? $attachment : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'attachment'=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Create Attachment **/
    public function create_attachment_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'attachment'=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $attachment = $this->tesseract_service->create_attachment($account_id, $postdata);

        if (!empty($attachment)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'attachment'=> !empty($attachment) ? $attachment : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'attachment'=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Retrieve Checklist(s) **/
    public function checklists_get($account_id = false, $checklist_id = false)
    {
        $account_id 	= (int) $this->get('account_id');
        $checklist_id   = !empty($this->get('checklist_id')) ? $this->get('checklist_id') : ((!empty($this->get('id'))) ? $this->get('id') : $checklist_id);

        $params 		= $this->get();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'checklists'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        #$checklist = $this->tesseract_service->_get_checklists_locally( $account_id, $params );
        $checklist = $this->tesseract_service->get_checklists($account_id, $checklist_id, $params);

        if (!empty($checklist)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'checklists'	=> !empty($checklist) ? $checklist : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'checklists'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Retrieve Checklist Questions By Checklist ID **/
    public function questions_by_checklist_id_get($account_id = false, $checklist_id = false)
    {
        $account_id 	= (int) $this->get('account_id');
        $checklist_id   = !empty($this->get('checklist_id')) ? $this->get('checklist_id') : ((!empty($this->get('id'))) ? $this->get('id') : $checklist_id);
        $params 		= $this->get();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 				=> 'Invalid main Account ID',
                'checklist_questions'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $checklist_questions = $this->tesseract_service->get_questions_by_checklist_id($account_id, $checklist_id, $params);

        if (!empty($checklist_questions)) {
            $message = [
                'status' 				=> true,
                'http_code' 			=> REST_Controller::HTTP_OK,
                'message' 				=> $this->session->flashdata('message'),
                'checklist_questions'	=> !empty($checklist_questions) ? $checklist_questions : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 				=> false,
                'http_code'				=> REST_Controller::HTTP_NO_CONTENT,
                'message' 				=> 'No records found',
                'checklist_questions'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Retrieve Checklist Criteria By Checklist ID **/
    public function checklist_criteria_get($account_id = false, $checklist_id = false)
    {
        $account_id 	= (int) $this->get('account_id');
        $checklist_id   = !empty($this->get('checklist_id')) ? $this->get('checklist_id') : ((!empty($this->get('id'))) ? $this->get('id') : $checklist_id);
        $params 		= $this->get();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 				=> 'Invalid main Account ID',
                'checklist_criteria'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $checklist_criteria = $this->tesseract_service->get_checklist_criteria($account_id, $checklist_id, $params);

        if (!empty($checklist_criteria)) {
            $message = [
                'status' 				=> true,
                'http_code' 			=> REST_Controller::HTTP_OK,
                'message' 				=> $this->session->flashdata('message'),
                'checklist_criteria'	=> !empty($checklist_criteria) ? $checklist_criteria : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 				=> false,
                'http_code'				=> REST_Controller::HTTP_NO_CONTENT,
                'message' 				=> 'No records found',
                'checklist_criteria'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Retrieve Criteria Field By Criteria ID **/
    public function checklist_criteria_field_get($account_id = false, $criteria_id = false)
    {
        $account_id 	= (int) $this->get('account_id');
        $criteria_id    = !empty($this->get('criteria_id')) ? $this->get('criteria_id') : ((!empty($this->get('id'))) ? $this->get('id') : $criteria_id);
        $params 		= $this->get();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 				=> 'Invalid main Account ID',
                'checklist_criteria_field'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $checklist_criteria_field = $this->tesseract_service->get_checklist_criteria_field($account_id, $criteria_id, $params);

        if (!empty($checklist_criteria_field)) {
            $message = [
                'status' 				=> true,
                'http_code' 			=> REST_Controller::HTTP_OK,
                'message' 				=> $this->session->flashdata('message'),
                'checklist_criteria_field'	=> !empty($checklist_criteria_field) ? $checklist_criteria_field : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 				=> false,
                'http_code'				=> REST_Controller::HTTP_NO_CONTENT,
                'message' 				=> 'No records found',
                'checklist_criteria_field'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Create a New Checklist Response Set **/
    public function create_checklist_response_set_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 				=> 'Invalid main Account ID',
                'checklist_response_set'=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $checklist_response_set = $this->tesseract_service->create_checklist_response_set($account_id, $postdata);

        if (!empty($checklist_response_set)) {
            $message = [
                'status' 				=> true,
                'http_code' 			=> REST_Controller::HTTP_OK,
                'message' 				=> $this->session->flashdata('message'),
                'checklist_response_set'=> !empty($checklist_response_set) ? $checklist_response_set : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_NO_CONTENT,
                'message' 				=> 'No records found',
                'checklist_response_set'=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Update an Existing Checklist Response Set **/
    public function update_checklist_response_set_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 				=> 'Invalid main Account ID',
                'checklist_response_set'=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $checklist_response_set = $this->tesseract_service->update_checklist_response_set($account_id, $postdata);

        if (!empty($checklist_response_set)) {
            $message = [
                'status' 				=> true,
                'http_code' 			=> REST_Controller::HTTP_OK,
                'message' 				=> $this->session->flashdata('message'),
                'checklist_response_set'=> !empty($checklist_response_set) ? $checklist_response_set : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_NO_CONTENT,
                'message' 				=> 'No records found',
                'checklist_response_set'=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Retrieve Checklist Response Set By Call Number **/
    public function checklist_response_set_by_call_number_get($account_id = false, $call_number = false)
    {
        $account_id 	= (int) $this->get('account_id');
        $call_number   = !empty($this->get('checklist_id')) ? $this->get('checklist_id') : ((!empty($this->get('id'))) ? $this->get('id') : $call_number);
        $params 		= $this->get();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 				=> 'Invalid main Account ID',
                'checklist_response_set'=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $checklist_response_set = $this->tesseract_service->get_checklist_response_set_by_call_number($account_id, $call_number, $params);

        if (!empty($checklist_response_set)) {
            $message = [
                'status' 				=> true,
                'http_code' 			=> REST_Controller::HTTP_OK,
                'message' 				=> $this->session->flashdata('message'),
                'checklist_response_set'=> !empty($checklist_response_set) ? $checklist_response_set : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 				=> false,
                'http_code'				=> REST_Controller::HTTP_NO_CONTENT,
                'message' 				=> 'No records found',
                'checklist_response_set'=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Create a New Task **/
    public function create_task_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'task'		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $task = $this->tesseract_service->create_task($account_id, $postdata);

        if (!empty($task)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'task'		=> !empty($task) ? $task : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'task'		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Update an Existing Task **/
    public function update_task_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'task'		=>	null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $task = $this->tesseract_service->update_task($account_id, $postdata);

        if (!empty($task)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'task'		=> !empty($task) ? $task : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'task'		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Retrieve Task(s) **/
    public function tasks_get($account_id = false, $task_num = false, $task_call_num = false)
    {
        $account_id 	= (int) $this->get('account_id');
        $task_num   	= !empty($this->get('task_num')) ? $this->get('task_num') : $task_num;
        $params 		= $this->get();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'tasks'		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $task = $this->tesseract_service->get_tasks($account_id, $task_num, $params);

        if (!empty($task)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'tasks'	=> !empty($task) ? $task : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'tasks'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Lookup Required Checklists by Call Type & Task Information **/
    public function lookup_checklists_by_job_type_get($account_id = false)
    {
        $account_id 	= (int) $this->get('account_id');
        $params 		= $this->get();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 				=> 'Invalid main Account ID',
                'required_checklists'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $required_checklists = $this->tesseract_service->lookup_required_checklists_by_job_type($account_id, $params);

        if (!empty($required_checklists)) {
            $message = [
                'status' 				=> true,
                'http_code' 			=> REST_Controller::HTTP_OK,
                'message' 				=> $this->session->flashdata('message'),
                'required_checklists'	=> !empty($required_checklists) ? $required_checklists : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 				=> false,
                'http_code'				=> REST_Controller::HTTP_NO_CONTENT,
                'message' 				=> 'No records found',
                'required_checklists'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Create Bulk Job Types from Checklist Ref Data **/
    public function create_job_types_from_checklist_ref_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 				=> 'Invalid main Account ID',
                'checklist_job_types'=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $checklist_job_types = $this->tesseract_service->create_job_types_from_checklist_ref($account_id, $postdata);

        if (!empty($checklist_job_types)) {
            $message = [
                'status' 				=> true,
                'http_code' 			=> REST_Controller::HTTP_OK,
                'message' 				=> $this->session->flashdata('message'),
                'checklist_job_types'=> !empty($checklist_job_types) ? $checklist_job_types : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_NO_CONTENT,
                'message' 				=> 'No records found',
                'checklist_job_types'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Create Checklist Response **/
    public function create_checklist_responses_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 				=> 'Invalid main Account ID',
                'checklist_response'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $checklist_response = $this->tesseract_service->create_checklist_responses($account_id, $postdata);

        if (!empty($checklist_response)) {
            $message = [
                'status' 				=> true,
                'http_code' 			=> REST_Controller::HTTP_OK,
                'message' 				=> $this->session->flashdata('message'),
                'checklist_response'	=> !empty($checklist_response) ? $checklist_response : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_NO_CONTENT,
                'message' 				=> 'No records found',
                'checklist_response'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Create a New Field Service Report **/
    public function create_fsr_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'fsr'		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $fsr = $this->tesseract_service->create_fsr($account_id, $postdata);

        if (!empty($fsr)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'fsr'		=> !empty($fsr) ? $fsr : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'fsr'		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Update an Existing Field Service Report **/
    public function update_fsr_post()
    {
        $account_id = (int) $this->post('account_id');
        $postdata   = $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'fsr'		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $fsr = $this->tesseract_service->update_fsr($account_id, $postdata);

        if (!empty($fsr)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'fsr'		=> !empty($fsr) ? $fsr : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'fsr'		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Retrieve Field Service Report By FSR Number **/
    public function fsr_by_fsr_number_get($account_id = false, $fsr_number = false)
    {
        $account_id 	= (int) $this->get('account_id');
        $fsr_number   	= !empty($this->get('checklist_id')) ? $this->get('checklist_id') : ((!empty($this->get('id'))) ? $this->get('id') : $call_number);
        $params 		= $this->get();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'fsr'		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $fsr = $this->tesseract_service->get_fsr_by_fsr_number($account_id, $fsr_number, $params);

        if (!empty($fsr)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'fsr'		=> !empty($fsr) ? $fsr : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code'	=> REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'fsr'		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Retrieve Field Service Report By Call Number **/
    public function fsr_by_call_number_get($account_id = false, $call_number = false)
    {
        $account_id 	= (int) $this->get('account_id');
        $call_number    = !empty($this->get('call_number')) ? $this->get('call_number') : $call_number;
        $params 		= $this->get();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'fsr'		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $fsr = $this->tesseract_service->get_fsr_by_call_number($account_id, $call_number, $params);

        if (!empty($fsr)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'fsr'		=> !empty($fsr) ? $fsr : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code'	=> REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No records found',
                'fsr'		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *	Get Checklist counts
    **/
    public function checklist_counter_get()
    {
        $account_id = (!empty($this->get('account_id'))) ? ( int ) $this->get('account_id') : false ;
        $where   	= (!empty($this->get('where'))) ? $this->get('where') : false ;
        $view_type  = (!empty($this->get('view_type'))) ? $this->get('view_type') : 'by_status' ;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 			=> 'Invalid main Account ID',
                'checklist_counts'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $checklist_counts = $this->tesseract_service->get_checklist_counter($account_id, $where, $view_type);

        if (!empty($checklist_counts)) {
            $message = [
                'status' 			=> true,
                'http_code' 		=> REST_Controller::HTTP_OK,
                'message' 			=> $this->session->flashdata('message'),
                'checklist_counts'	=> $checklist_counts,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
                'message' 			=> 'No records found',
                'checklist_counts'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Get Checklist Job Types
    */
    public function checklist_job_types_get()
    {
        $req_data 		= $this->get();
        $account_id 	= ( int ) $this->get('account_id');
        $job_type_id 	= ( int ) $this->get('job_type_id');
        $where 			= !empty($this->get('where')) ? $this->get('where') : false;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> 'Invalid main Account ID.',
                'job_types' => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $checklist_job_types = $this->tesseract_service->get_checklist_job_types($account_id, $job_type_id, $where);

        if (!empty($checklist_job_types)) {
            $message = [
                'status' 	=> true,
                'message' 	=> $this->session->flashdata('message'),
                'job_types' => (!empty($checklist_job_types)) ? $checklist_job_types : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'message' 	=> $this->session->flashdata('message'),
                'job_types' => (!empty($checklist_job_types)) ? $checklist_job_types : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Get Checklist Frequencies
    */
    public function checklist_frequencies_get()
    {
        $req_data 		= $this->get();
        $account_id 	= ( int ) $this->get('account_id');
        $job_type_id 	= ( int ) $this->get('job_type_id');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 				=> false,
                'message' 				=> 'Invalid main Account ID.',
                'checklist_frequencies' => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $checklist_frequencies = $this->tesseract_service->get_checklist_frequencies($account_id);

        if (!empty($checklist_frequencies)) {
            $message = [
                'status' 				=> true,
                'message' 				=> $this->session->flashdata('message'),
                'checklist_frequencies' => (!empty($checklist_frequencies)) ? $checklist_frequencies : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 				=> false,
                'message' 				=> $this->session->flashdata('message'),
                'checklist_frequencies' => (!empty($checklist_frequencies)) ? $checklist_frequencies : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *	Get list of Tesseract Job Statuses
    **/
    public function tess_job_statuses_get()
    {
        $message = [
            'status' 			=> true,
            'message' 			=> 'Tessseract Job statuses found',
            'tess_job_statuses' => [ 'COMP', 'DOWN', /*'DISP',*/ 'WAIT' ]
        ];
        $this->response($message, REST_Controller::HTTP_OK);
    }

    /**
    * Refresh Evident Jobs with Tesseract Data
    */
    public function refresh_evident_jobs_get()
    {
        $account_id 	= ( int ) $this->get('account_id');
        $call_numbers 	= (!empty($this->get('call_numbers'))) ? $this->get('call_numbers') : (!empty($this->get('call_number')) ? $this->get('call_number') : false);

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> 'Invalid main Account ID.',
                'jobs' 		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $jobs = $this->tesseract_service->refresh_evident_jobs($account_id, $call_numbers);

        if (!empty($jobs)) {
            $message = [
                'status' 	=> true,
                'message' 	=> $this->session->flashdata('message'),
                'jobs' 		=> (!empty($jobs)) ? $jobs : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'message' 	=> $this->session->flashdata('message'),
                'jobs' 		=> (!empty($jobs)) ? $jobs : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Change Notification API **/
    public function change_notification_get()
    {
        $account_id 	= ( int ) $this->get('account_id');
        $call_number 	= (!empty($this->get('call_number'))) ? $this->get('call_number') : null;

        if (!empty($call_number)) {
            $check_exists = $this->db->where('job.account_id', $account_id)
                ->where('job.external_job_ref', $call_number)
                ->get('job')
                ->row();

            if (!empty($check_exists)) {
                $this->db->where('job.account_id', $account_id)
                    ->where('job.external_job_ref', $call_number)
                    ->update('job', ['special_instructions'=>'Tesseract Notification received at '._datetime() ]);

                $get_it = $this->db->where('job.account_id', $account_id)
                    ->where('job.external_job_ref', $call_number)
                    ->get('job')
                    ->row();

                $message = [
                    'status' 	=> true,
                    'message' 	=> 'Hello you request made it to Evident Successfully',
                    'job' 		=> (!empty($get_it)) ? $get_it : null,
                ];
                $this->response($message, REST_Controller::HTTP_OK);
            } else {
                $message = [
                    'status' 	=> true,
                    'message' 	=> 'Invalid Job/Call number',
                    'job' 		=> null,
                ];
                $this->response($message, REST_Controller::HTTP_OK);
            }
        } else {
            $message = [
                'status' 	=> false,
                'message' 	=> $this->session->flashdata('message'),
                'job' 		=> (!empty($call_number)) ? $call_number : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
