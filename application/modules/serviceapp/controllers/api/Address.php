<?php

namespace Application\Service\Controllers\Api;

use App\Libraries\REST_Controller;

class Address extends REST_Controller
{
    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('Address_model', 'address_service');
        $this->load->model('Address_Bank_model', 'addressbank_service');
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->lang->load('auth');
    }

    /**
    * Create new Address resource
    */
    public function create_post()
    {
        $address_data = $this->post();
        $this->form_validation->set_rules('address_type', 'Address Type', 'required');
        $this->form_validation->set_rules('address_contact_first_name', 'Address Contact First Name', 'required');
        $this->form_validation->set_rules('address_contact_last_name', 'Address Contact Last Name', 'required');
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('customer_id', 'Customer ID', 'required');
        $this->form_validation->set_rules('main_address_id', 'Please provide Address Details', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid Address data: ',
                'address' => null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_address = $this->address_service->create_address($address_data);

        if (!empty($new_address)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'address' => $new_address
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'address' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Update user resource
    */
    public function update_post()
    {
        $address_data   = $this->post();
        $address_id     = (int) $this->post('address_id');

        $this->form_validation->set_rules('address_type', 'Address Type', 'required');
        $this->form_validation->set_rules('address_contact_first_name', 'Address Contact First Name', 'required');
        $this->form_validation->set_rules('address_contact_last_name', 'Address Contact Last Name', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid Address data: ',
                'address' => null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }


        ## Validate the address id.
        if ($address_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        $address = $this->address_service->get_address($address_id);
        if (!$address) {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'address' => null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }

        ## Run address update
        $updated_address = $this->address_service->update_address($address_id, $address_data);
        if (!empty($updated_address)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'address' => $updated_address
            ];
            $this->response($message, REST_Controller::HTTP_OK); // Resource Updated
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'address' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all Addresss or single record
    */
    public function addresses_get()
    {
        $address_id     = (int) $this->get('address_id');
        $customer_id    = (int) $this->get('customer_id');
        $archived       = $this->get('archived');
        $postcode       = trim(urldecode($this->get('address_postcode')));
        $address        = $this->address_service->get_address($address_id, $customer_id, $postcode, $archived);
        if (!empty($address)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'address' => $address
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'address' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Delete Address resource
    */
    public function delete_get()
    {
        $customer_id    = (int) $this->get('customer_id');
        $address_id     = (int) $this->get('address_id');

        if (!$customer_id) {
            $message = [
                'status' => false,
                'message' => 'Customer ID is required',
                'address' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if ($address_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        $delete_address = $this->address_service->delete_address($customer_id, $address_id);
        if (!empty($delete_address)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'address' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'address' => null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }
    }

    public function lookup_get()
    {
        $search_term    = trim(urldecode($this->get('search_term')));
        if (empty($search_term)) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }
        $verified_addresses = $this->addressbank_service->get_addresses($search_term);
        if (!empty($verified_addresses)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'addresses' => $verified_addresses
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'addresses' => null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }
    }

    /**
    * Get list of all address tytpes
    */
    public function address_types_get()
    {
        $account_id         = (int) $this->get('account_id');
        $address_type_id    = urldecode($this->get('address_type_id'));
        $address_type_group = urldecode($this->get('address_type_group'));
        $grouped            = $this->get('grouped');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID',
                'address_types' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $address_types = $this->address_service->get_address_types($account_id, $address_type_id, $address_type_group, $grouped);

        if (!empty($address_types)) {
            $message = [
                'status' => true,
                'message' => 'Address types records found',
                'address_types' => $address_types
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => 'No records found',
                'address_types' => null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }
    }
}
