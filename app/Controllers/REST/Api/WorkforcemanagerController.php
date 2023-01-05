<?php

namespace App\Controllers\REST\Api;

use App\Adapter\RESTController;
use App\Models\Service\WorkforcemanagerModel;

final class WorkforcemanagerController extends RESTController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library("Ssid_common");
        $this->load->library("form_validation");
        $this->load->library("email");
        $this->Workforcemanager_service = new WorkforcemanagerModel();
    }

    /**
    * 	Add new Engineer Profile
    */
    public function add_profile_post()
    {
        $post_data 		= $this->post();
        $new_profile	= false;
        $account_id 	= (!empty($post_data['account_id'])) ? $post_data['account_id'] : false ;
        $user_id 		= (!empty($post_data['user_id'])) ? $post_data['user_id'] : false ;

        ## which fields do I really need?
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('user_id', 'User ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            ## One of the required fields is invalid
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid or missing Field(s)',
                'new_profile' 		=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'new_profile' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $user_profile = $this->ion_auth->get_user_by_id($account_id, $user_id);

        if (empty($user_profile)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'No User found.',
                'new_profile' 		=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_profile = $this->Workforcemanager_service->add_profile($account_id, $user_id, $post_data);

        if (!empty($new_profile)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'new_profile' 		=> $new_profile
            ];

            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'new_profile' 		=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * 	Get Profile(s)
    */
    public function get_profile_post($account_id = false, $profile_id = false, $where = false, $limit = 50, $offset = false)
    {
        $postset 		= $this->post();

        $account_id 	= (!empty($postset['account_id'])) ? $postset['account_id'] : $account_id;
        $profile_id 	= (!empty($postset['profile_id'])) ? $postset['profile_id'] : $profile_id;
        $where 			= (!empty($postset['where'])) ? $postset['where'] : $where;
        $limit 			= (!empty($postset['limit'])) ? $postset['limit'] : $limit;
        $offset 		= (!empty($postset['offset'])) ? $postset['offset'] : $offset;

        ## which fields do I really need?
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            ## One of the required fields is invalid
            $message = [
                'status' 		=> false,
                'message' 		=> 'Invalid or missing Field(s)',
                'profile' 		=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> $this->session->flashdata('message'),
                'job' 		=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $profiles = $this->Workforcemanager_service->get_profile($account_id, $profile_id, $where, $limit, $offset);

        if (!empty($profiles)) {
            $message = [
                'status' 		=> true,
                'message' 		=> $this->session->flashdata('message'),
                'profile' 		=> $profiles
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'profile' 		=> false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Update operative profile
    */

    public function update_post($account_id = false, $profile_id = false)
    {
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('profile_id', 'Profile ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            ## One of the required fields is invalid
            $message = [
                'status' 		=> false,
                'message' 		=> 'Invalid or missing Field(s)',
                'profile' 		=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $postset 		= $this->post();
        $account_id 	= (!empty($postset['account_id'])) ? $postset['account_id'] : $account_id;
        unset($postset['account_id']);
        $profile_id 	= (!empty($postset['profile_id'])) ? ( int ) $postset['profile_id'] : $profile_id;
        unset($postset['profile_id']);

        $profile_data	= $postset;

        if ($profile_id <= 0) {  ## or is not a number
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> $this->session->flashdata('message'),
                'job' 		=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $profile = $this->Workforcemanager_service->get_profile($account_id, $profile_id);

        if (!$profile) {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'profile' 			=> null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }

        $updated_profile = $this->Workforcemanager_service->update_profile($account_id, $profile_id, $profile_data);

        if (!empty($updated_profile)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'updated_profile' 	=> $updated_profile
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'updated_profile' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Update operative profile
    */

    public function delete_post($account_id = false, $profile_id = false)
    {
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('profile_id', 'Profile ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            ## One of the required fields is invalid
            $message = [
                'status' 		=> false,
                'message' 		=> 'Invalid or missing Field(s)',
                'profile' 		=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $postset 		= $this->post();
        $account_id 	= (!empty($postset['account_id'])) ? $postset['account_id'] : $account_id;
        unset($postset['account_id']);
        $profile_id 	= (!empty($postset['profile_id'])) ? ( int ) $postset['profile_id'] : $profile_id;
        unset($postset['profile_id']);

        $profile_data	= $postset;

        if ($profile_id <= 0) {  ## or is not a number
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> $this->session->flashdata('message'),
                'job' 		=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $profile = $this->Workforcemanager_service->get_profile($account_id, $profile_id);

        if (!$profile) {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'profile' 			=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $profile_deleted = $this->Workforcemanager_service->delete_profile($account_id, $profile_id);

        if (!empty($profile_deleted)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'profile_deleted' 	=> $profile_deleted
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'profile_deleted' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
