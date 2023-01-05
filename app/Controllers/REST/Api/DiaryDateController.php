<?php

namespace App\Controllers\REST\Api;

use App\Adapter\RESTController;
use App\Models\Service\DiaryDateModel;

final class DiaryDateController extends RESTController
{
	/**
	 * @var \Application\Modules\Service\Models\DiaryDateModel
	 */
	private $diary_date_service;

	public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->diary_date_service = new DiaryDateModel();
        $this->form_validation->set_error_delimiters(
			$this->config->item('error_start_delimiter', 'ion_auth'),
			$this->config->item('error_end_delimiter', 'ion_auth')
		);
        $this->lang->load('auth');
    }

    /**
    * Create new Diary Date resource
    */
    public function create_post()
    {
        $diary_date_data= $this->post();
        $account_id  	= $this->post('account_id');

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('diary_date', 'Diary Date', 'required');
        $this->form_validation->set_rules('declared_slots', 'Declared slots', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid Diary Date data: ',
                'diary_date' => null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID',
                'diary_date' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_diary_date = $this->diary_date_service->create_diary_date($account_id, $diary_date_data);

        if (!empty($new_diary_date)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'diary_date' => $new_diary_date
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'diary_date' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Update diary_date resource
    */
    public function update_post()
    {
        $diary_date_data= $this->post();
        $account_id 	= (int) $this->post('account_id');
        $diary_date_id 	= (int) $this->post('diary_date_id');
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('diary_date', 'Diary Date', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid Diary Date data: ',
                'diary_date' => null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID',
                'diary_date' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Validate the diary_date id.
        if ($diary_date_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        $diary_date = $this->diary_date_service->get_diary_dates($diary_date_id);
        if (!$diary_date) {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'diary_date' => null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }

        ## Run diary_date update
        $updated_diary_date = $this->diary_date_service->update_diary_date($account_id, $diary_date_id, $diary_date_data);
        if (!empty($updated_diary_date)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'diary_date' => $updated_diary_date
            ];
            $this->response($message, REST_Controller::HTTP_OK); // Resource Updated
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'diary_date' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all Diary-Date or single record
    */
    public function dates_get()
    {
        $diary_date_id 	= (int) $this->get('diary_date_id');
        $account_id 	= (int) $this->get('account_id');
        $diary_date		= ($this->get('diary_date')) ? $this->get('diary_date') : null;

        if (!$account_id) {
            $message = [
                'status' => false,
                'message' => 'Account ID is required',
                'diary_date' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main account ID',
                'diary_date' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $diary_date = $this->diary_date_service->get_diary_dates($account_id, $diary_date_id, $diary_date);

        if (!empty($diary_date)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'diary_date' => $diary_date
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'diary_date' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Delete Diary Date resource
    */
    public function delete_get()
    {
        $diary_date_id 	= (int) $this->get('diary_date_id');
        $account_id 	= (int) $this->get('account_id');
        if ($diary_date_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }
        $delete_diary_date = $this->diary_date_service->delete_diary_date($account_id, $diary_date_id);
        if (!empty($delete_diary_date)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'diary_date' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'diary_date' => null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }
    }
}
