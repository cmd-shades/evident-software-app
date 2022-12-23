<?php

namespace Application\Service\Controllers\Api;

use App\Libraries\REST_Controller;

class Notification extends REST_Controller
{
    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('Notification_model', 'notification_service');
    }

    /**
    * Create new Device resource
    */
    public function add_apns_token_post()
    {
        $apns_data = $this->post();
        $user_id     = (int)$this->post('user_id');
        $account_id  = (int)$this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('user_id', 'User ID', 'required');
        $this->form_validation->set_rules('device_token', 'APNS Device Token', 'required');
        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid APNS data: ',
                'device_token' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'] . 'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $saved = $this->notification_service->add_apns_token($apns_data);

        if (!empty($saved)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'device_token' => $saved
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'device_token' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /*
    * Push Notification to Devices
    */
    public function push_notification_post()
    {
        $apns_data = $this->post();
        $user_id     = (int)$this->post('user_id');
        $account_id  = (int)$this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('mtitle', 'Message Title', 'required');
        $this->form_validation->set_rules('mdesc', 'Message Description', 'required');
        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid Job data: ',
                'push_notification' => null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID',
                'push_notification' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $push = $this->notification_service->iOS($account_id, $user_id, $apns_data);

        if (!empty($push)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'push_notification' => $push
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'push_notification' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all Notification for a device
    */
    public function notifications_get()
    {
        $user_id     = (int) $this->get('user_id');
        $account_id  = (int) $this->get('account_id');
        $device_token = $this->get('device_token');
        $limit       = $this->get('limit');
        $offset      = $this->get('offset');

        $notifications = $this->notification_service->get_device_notifications($device_token, $account_id, $user_id, $limit, $offset);
        if (!empty($notifications)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'device_notifications' => $notifications
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'device_notifications' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
