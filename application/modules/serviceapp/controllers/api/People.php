<?php

namespace Application\Service\Controllers\Api;

use App\Libraries\REST_Controller;

defined('BASEPATH') || exit('No direct script access allowed');

class People extends REST_Controller
{
    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('People_model', 'people_service');
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->lang->load('auth');
    }

    /**
    * Create new Person resource
    */
    public function create_post()
    {
        $people_data = $this->post();
        $account_id  = (int)$this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('department_id', 'Department ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid data: ',
                'people' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'] . 'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'people' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_person = $this->people_service->create_person($account_id, $people_data);

        if (!empty($new_person)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'people' => $new_person
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'people' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Update person resource
    */
    public function update_post()
    {
        $people_data = $this->post();
        $user_id    = (int) $this->post('user_id');
        $person_id  = (int) $this->post('person_id');
        $account_id = (int) $this->post('account_id');

        $this->form_validation->set_rules('user_id', 'User ID', 'required');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('person_id', 'Person ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid data: ',
                'person' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'] . 'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'person' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Validate the person id.
        if ($person_id <= 0 && $user_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        $person = $this->people_service->get_people($account_id, $user_id, $person_id);

        if (!$person) {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'person' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $current_account = $this->ion_auth->_current_user()->account_id;

        ## Stop illegal updates
        if (($current_account != $account_id) || ($user_id != $person_id)) {
            $message = [
                'status' => false,
                'message' => 'Illegal operation. This is not your resource!',
                'person' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Run person update
        $updated_person = $this->people_service->update_person($account_id, $person_id, $people_data);

        if (!empty($updated_person)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'person' => $updated_person
            ];
            $this->response($message, REST_Controller::HTTP_OK); // Resource Updated
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'person' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all People or a single person / employee
    */
    public function people_get()
    {
        $account_id         = (int) $this->get('account_id');
        $user_id            = (int)$this->get('user_id');
        $person_id          = (int) $this->get('person_id');
        $personal_email     = $this->get('email');
        $job_level          = $this->get('job_level');
        $departments        = $this->get('departments');
        $where              = $this->get('where');
        $order_by           = $this->get('order_by');
        $limit              = ($this->get('limit')) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset             = ($this->get('offset')) ? (int) $this->get('offset') : 0;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID',
                'people' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $people = $this->people_service->get_people($account_id, $user_id, $person_id, $departments, $personal_email, $job_level, $where, $order_by, $limit, $offset);

        if (!empty($people)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'people' => $people
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'people' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Delete Person resource
    */
    public function delete_get()
    {
        $account_id = (int) $this->get('account_id');
        $person_id  = (int) $this->get('person_id');

        if ($person_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'person' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $delete_person = $this->people_service->delete_person($account_id, $person_id);

        if (!empty($delete_person)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'person' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'person' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Search through list of Persons
    */
    public function lookup_get()
    {
        $account_id     = (int) $this->get('account_id');
        $limit          = (int) $this->get('limit');
        $offset         = (int) $this->get('offset');
        $where          = $this->get('where');
        $order_by       = $this->get('order_by');
        $departments    = $this->get('departments');
        $user_statuses  = $this->get('user_statuses');
        $search_term    = trim(urldecode($this->get('search_term')));

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'people' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $people_lookup = $this->people_service->people_lookup($account_id, $search_term, $departments, $user_statuses, $where, $order_by, $limit, $offset);

        if (!empty($people_lookup)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'people' => $people_lookup
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'people' => $people_lookup
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all departments
    */
    public function departments_get()
    {
        $account_id      = (int) $this->get('account_id');
        $department_id   = (int)$this->get('department_id');
        $department_group = urldecode($this->get('department_group'));
        $grouped         = $this->get('grouped');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'departments' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $departments = $this->people_service->get_departments($account_id, $department_id, $department_group, $grouped);

        if (!empty($departments)) {
            $message = [
                'status' => true,
                'message' => 'Department records found',
                'departments' => $departments
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => 'No records found',
                'departments' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
    /**
    * Get list of all Job titles
    */
    public function job_titles_get()
    {
        $account_id      = (int) $this->get('account_id');
        $job_title_id    = (int)$this->get('job_title_id');
        $job_area        = urldecode($this->get('job_area'));
        $job_level       = urldecode($this->get('job_level'));
        $group_by        = urldecode($this->get('group_by'));

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'job_titles' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $job_titles = $this->people_service->get_job_titles($account_id, $job_title_id, $job_area, $job_level, $group_by);

        if (!empty($job_titles)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'job_titles' => $job_titles
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => 'No records found',
                'job_titles' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all Person-event logs by person id
    */
    public function event_logs_get()
    {
        $person_id   = (int) $this->get('person_id');
        $account_id  = (int) $this->get('account_id');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'event_logs' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $event_logs = $this->people_service->get_event_logs($account_id, $person_id);

        if (!empty($event_logs)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'event_logs' => $event_logs
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => 'No records found',
                'event_logs' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all Job Poasitions
    */
    public function job_positions_get()
    {
        $account_id     = (int) $this->get('account_id');
        $person_id      = (int)$this->get('person_id');
        $position_id    = (int)$this->get('position_id');
        $job_title_id   = (int)$this->get('job_title_id');
        $job_start_date = urldecode($this->get('job_start_date'));
        $job_end_date   = urldecode($this->get('job_end_date'));
        $limit          = ($this->get('limit')) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset         = ($this->get('offset')) ? (int) $this->get('offset') : 0;


        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'job_positions' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $job_positions = $this->people_service->get_job_positions($account_id, $person_id, $position_id, $job_title_id, $job_start_date, $job_end_date, $limit, $offset);

        if (!empty($job_positions)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'job_positions' => $job_positions
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'job_positions' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Create a person's contact address record **/
    public function create_contact_post()
    {
        $contact_data  = $this->post();
        $account_id  = (int)$this->post('account_id');
        $person_id   = (int)$this->post('person_id');

        $this->form_validation->set_rules('person_id', 'Person\'s ID', 'required');
        $this->form_validation->set_rules('contact_first_name', 'Contact First Name', 'required');
        $this->form_validation->set_rules('contact_last_name', 'Contact Last Name', 'required');
        $this->form_validation->set_rules('relationship', 'Contact Relationship', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid data: ',
                'address_contact' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'] . 'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'address_contact' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $address_contact = $this->people_service->create_contact($account_id, $person_id, $contact_data);

        if (!empty($address_contact)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'address_contact' => $address_contact
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'address_contact' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /* Get list of all Contact Persons */
    public function address_contacts_get()
    {
        $account_id     = (int) $this->get('account_id');
        $person_id      = (int) $this->get('person_id');
        $contact_id     = (int) $this->get('contact_id');
        $address_type_id = $this->get('address_type_id');
        $limit          = ($this->get('limit')) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset         = ($this->get('offset')) ? (int) $this->get('offset') : 0;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'address_contacts' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $address_contacts = $this->people_service->get_address_contacts($account_id, $person_id, $contact_id, $address_type_id, $limit, $offset);

        if (!empty($address_contacts)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'address_contacts' => $address_contacts
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'address_contacts' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   Get Health data against the person
    */
    public function health_log_get()
    {
        $postset = $account_id = $person_id = $health_log_id = $limit = $offset = $order_by = $validation_errors = false;

        $postset        = $this->get();

        $account_id     = (!empty($postset['account_id'])) ? $postset['account_id'] : false ;
        $person_id      = (!empty($postset['person_id'])) ? $postset['person_id'] : false ;
        $health_log_id  = (!empty($postset['health_log_id'])) ? $postset['health_log_id'] : false ;
        $limit          = (!empty($postset['limit'])) ? $postset['limit'] : DEFAULT_LIMIT ;
        $offset         = (!empty($postset['offset'])) ? $postset['offset'] : false ;
        $order_by       = (!empty($postset['order_by'])) ? $postset['order_by'] : false ;

        $expected_data = [
            'account_id'    => $account_id,
            'person_id'     => $person_id
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('person_id', 'Person ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '' ;
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid or missing Field(s)',
                'health_log'    => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . ' ' . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'health_log'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## check if the person exists
        $person = $this->people_service->get_people($account_id, false, $person_id, false, false, false, false, $order_by, $limit, $offset);

        if (!$person) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'health_log'    => null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }

        $health_log = $this->people_service->get_health_log($account_id, $person_id, $health_log_id, $limit, $offset, $order_by);

        if (!empty($health_log)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'health_log'    => $health_log
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'health_log'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Add new health log
    */
    public function create_health_log_post()
    {
        $user_id = $person_id = $health_log = $post_data = $account_id = $person_exists = false;

        $post_data          = $this->post();

        $account_id         = (!empty($post_data['account_id'])) ? $post_data['account_id'] : $account_id ;
        unset($post_data['account_id']);

        $user_id            = (!empty($post_data['user_id'])) ? $post_data['user_id'] : $user_id ;
        unset($post_data['user_id']);

        $person_id          = (!empty($post_data['person_id'])) ? $post_data['person_id'] : $person_id ;
        unset($post_data['person_id']);

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('person_id', 'Person ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Invalid or missing Field(s)',
                'health_log'        => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'health_log'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $person_exists = $this->people_service->get_people($account_id, $user_id, $person_id);
        if (!$person_exists) {
            $message = [
                'status'        => false,
                'message'       => "Incorrect User ID or Person ID",
                'health_log'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $health_log = $this->people_service->create_health_log($account_id, $person_id, $post_data);

        if (!empty($health_log)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'health_log'        => $health_log
            ];

            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'health_log'        => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Create new Event
    */
    public function create_event_post()
    {
        $user_id = $person_id = $event = $post_data = $account_id = $person_exists = false;

        $post_data          = $this->post();
        $account_id         = (!empty($post_data['account_id'])) ? $post_data['account_id'] : $account_id ;
        unset($post_data['account_id']);

        $user_id            = (!empty($post_data['user_id'])) ? $post_data['user_id'] : $user_id ;
        unset($post_data['user_id']);

        $person_id          = (!empty($post_data['person_id'])) ? $post_data['person_id'] : $person_id ;
        unset($post_data['person_id']);

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('person_id', 'Person ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Invalid or missing Field(s)',
                'new_event'         => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'new_event'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $person_exists = $this->people_service->get_people($account_id, $user_id, $person_id);
        if (!$person_exists) {
            $message = [
                'status'        => false,
                'message'       => "Incorrect User ID or Person ID",
                'new_event'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_event = $this->people_service->create_event($account_id, $person_id, $post_data);

        if (!empty($new_event)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'new_event'         => $new_event
            ];

            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'new_event'         => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get Event Types
    */
    public function event_types_get()
    {
        $postset            = $this->get();

        $account_id         = (!empty($postset['account_id'])) ? $postset['account_id'] : false;
        $event_type_id      = (!empty($postset['event_type_id'])) ? $postset['event_type_id'] : false;
        $event_category_id  = (!empty($postset['event_category_id'])) ? (int)$postset['event_category_id'] : false;
        $ordered            = (!empty($postset['ordered'])) ? $postset['ordered'] : false;

        $expected_data = [
            'account_id'    => $account_id ,
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid or missing Field(s)',
                'event_types'   => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'event_types'   => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $event_types = $this->people_service->get_event_types($account_id, $event_type_id, $event_category_id, $ordered);

        if (!empty($event_types)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'event_types'   => $event_types
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'event_types'   => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   Get Events
    */
    public function events_get()
    {
        $postset = $validation_errors = false;

        $postset        = $this->get();

        $account_id     = (!empty($postset['account_id'])) ? $postset['account_id'] : false ;
        $event_id       = (!empty($postset['event_id'])) ? $postset['event_id'] : false ;
        $person_id      = (!empty($postset['person_id'])) ? $postset['person_id'] : false ;
        $event_type_id  = (!empty($postset['event_type_id'])) ? $postset['event_type_id'] : false ;
        $where          = (!empty($postset['where'])) ? $postset['where'] : false ;
        $limit          = (!empty($postset['limit'])) ? $postset['limit'] : DEFAULT_LIMIT ;
        $offset         = (!empty($postset['offset'])) ? $postset['offset'] : false ;
        $order_by       = (!empty($postset['order_by'])) ? $postset['order_by'] : false ;

        $expected_data = [
            'account_id'    => $account_id
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '' ;
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid or missing Field(s)',
                'events'        => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . ' ' . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'events'        => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## check if the person exists if provided
        if (!empty($person_id)) {
            $person = $this->people_service->get_people($account_id, false, $person_id, false, false, false, false, $order_by, $limit, $offset);

            if (!$person) {
                $message = [
                    'status'    => false,
                    'message'   => $this->session->flashdata('message'),
                    'events'    => null
                ];
                $this->response($message, REST_Controller::HTTP_NO_CONTENT);
            }
        }

        $events = $this->people_service->get_events($account_id, $event_id, $person_id, $event_type_id, $where, $limit, $offset, $order_by);

        if (!empty($events)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'events'        => $events
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'events'        => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   Get Health Questionnaire Types based on an Account ID
    */
    public function questionnaire_types_get()
    {
        $postset = $validation_errors = false;

        $postset        = $this->get();

        $account_id     = (!empty($postset['account_id'])) ? $postset['account_id'] : false ;
        $q_type_id      = (!empty($postset['q_type_id'])) ? $postset['q_type_id'] : false ;
        $q_type_group   = (!empty($postset['q_type_group'])) ? $postset['q_type_group'] : false ;
        $ordered        = (!empty($postset['ordered'])) ? $postset['ordered'] : false ;


        $expected_data = [
            'account_id'    => $account_id
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '' ;
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid or missing Field(s)',
                'q_types'       => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . ' ' . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'q_types'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $q_types = $this->people_service->get_q_types($account_id, $q_type_id, $q_type_group, $ordered);

        if (!empty($q_types)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'q_types'       => $q_types
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'q_types'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /*
    *   Get Health Questionnaire Result based on an Account ID
    */
    public function questionnaire_results_get()
    {
        $postset = $validation_errors = false;

        $postset        = $this->get();

        $account_id     = (!empty($postset['account_id'])) ? $postset['account_id'] : false ;
        $q_result_id    = (!empty($postset['q_result_id'])) ? $postset['q_result_id'] : false ;
        $q_result_group = (!empty($postset['q_result_group'])) ? $postset['q_result_group'] : false ;
        $ordered        = (!empty($postset['ordered'])) ? $postset['ordered'] : false ;


        $expected_data = [
            'account_id'    => $account_id
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '' ;
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid or missing Field(s)',
                'q_results'     => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . ' ' . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'q_results'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $q_results = $this->people_service->get_q_result($account_id, $q_result_id, $q_result_group, $ordered);

        if (!empty($q_results)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'q_results'     => $q_results
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'q_results'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   This function is to update the health log.
    *   The parameters need to be provided in 'data' object
    */
    public function update_log_status_post()
    {
        $validation_errors = $post_data = $health_log_exists = false;
        $post_data      = $this->post();

        $health_log_id  = (!empty($post_data['health_log_id'])) ? $post_data['health_log_id'] : false ;
        unset($post_data['health_log_id']);

        $data           = (!empty($post_data['data'])) ? json_decode($post_data['data']) : false ;
        unset($post_data['data']);

        $account_id     = (!empty($post_data['account_id'])) ? $post_data['account_id'] : $account_id ;
        unset($post_data['account_id']);

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('health_log_id', 'Health Log ID', 'required');
        $this->form_validation->set_rules('data', 'Update Data', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid or missing Field(s)',
                'updated_log'   => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'updated_log'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $health_log_exists = $this->people_service->get_health_log($account_id, $person_id = false, $health_log_id);

        if (!$health_log_exists) {
            $message = [
                'status'        => false,
                'message'       => "Invalid Health Log ID",
                'updated_log'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $updated_log = $this->people_service->update_health_log($account_id, $health_log_id, $data);

        if (!empty($updated_log)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'updated_log'   => $updated_log
            ];

            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'updated_log'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   Get Notes for the health log
    */
    public function health_log_notes_get()
    {
        $postset = $validation_errors = false;

        $postset            = $this->get();

        $account_id         = (!empty($postset['account_id'])) ? $postset['account_id'] : false ;
        $person_id          = (!empty($postset['person_id'])) ? $postset['person_id'] : false ;
        $health_log_id      = (!empty($postset['health_log_id'])) ? $postset['health_log_id'] : false ;
        $health_note_id     = (!empty($postset['health_note_id'])) ? $postset['health_note_id'] : false ;
        $where              = (!empty($postset['where'])) ? $postset['where'] : false ;
        $limit              = (!empty($postset['limit'])) ? $postset['limit'] : DEFAULT_LIMIT ;
        $offset             = (!empty($postset['offset'])) ? $postset['offset'] : false ;
        $order_by           = (!empty($postset['order_by'])) ? $postset['order_by'] : false ;

        $expected_data = [
            'account_id'    => $account_id
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '' ;
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid or missing Field(s)',
                'h_log_notes'   => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . ' ' . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'h_log_notes'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $h_log_notes = $this->people_service->get_health_log_notes($account_id, $person_id, $health_note_id, $health_log_id, $where, $limit, $offset, $order_by);

        if (!empty($h_log_notes)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'h_log_notes'   => $h_log_notes
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'h_log_notes'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Create new Health Log Note
    */
    public function create_health_log_note_post()
    {
        $post_data = $health_log_exists = false;

        $post_data          = $this->post();

        $account_id         = (!empty($post_data['account_id'])) ? $post_data['account_id'] : false ;
        unset($post_data['account_id']);

        $person_id          = (!empty($post_data['person_id'])) ? $post_data['person_id'] : false ;
        unset($post_data['person_id']);

        $dataset            = (!empty($post_data['dataset'])) ? json_decode($post_data['dataset']) : false ;
        unset($post_data['dataset']);

        $health_log_id      = (!empty($post_data['health_log_id'])) ? $post_data['health_log_id'] : false ;
        unset($post_data['health_log_id']);


        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('dataset', 'Dataset', 'required');
        $this->form_validation->set_rules('health_log_id', 'Health Log ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'                => false,
                'message'               => 'Invalid or missing Field(s)',
                'new_health_log_note'   => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'new_health_log_note'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $health_log_exists = $this->people_service->get_health_log($account_id, false, $health_log_id);
        if (!$health_log_exists) {
            $message = [
                'status'                => false,
                'message'               => "Incorrect Health Log ID",
                'new_health_log_note'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_health_log_note = $this->people_service->create_health_log_note($account_id, $health_log_id, $person_id, $dataset);

        if (!empty($new_health_log_note)) {
            $message = [
                'status'                    => true,
                'message'                   => $this->session->flashdata('message'),
                'new_health_log_note'       => $new_health_log_note
            ];

            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'                    => false,
                'message'                   => $this->session->flashdata('message'),
                'new_health_log_note'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   This is to update an Event
    */
    public function update_event_post()
    {
        $validation_errors = $post_data = $event_exists = false;

        $post_data      = $this->post();

        $event_id   = (!empty($post_data['event_id'])) ? $post_data['event_id'] : false ;
        unset($post_data['event_id']);


        $data           = (!empty($post_data['dataset'])) ? json_decode($post_data['dataset']) : false ;
        unset($post_data['dataset']);

        $account_id     = (!empty($post_data['account_id'])) ? $post_data['account_id'] : $account_id ;
        unset($post_data['account_id']);

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('event_id', 'Event ID', 'required');
        $this->form_validation->set_rules('dataset', 'Update Data', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Invalid or missing Field(s)',
                'updated_event'     => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'updated_event'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $event_exists = $this->people_service->get_events($account_id, $event_id);

        if (!$event_exists) {
            $message = [
                'status'            => false,
                'message'           => "Invalid Event ID",
                'updated_event'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $updated_event = $this->people_service->update_event($account_id, $event_id, $data);

        if (!empty($updated_event)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'updated_event'     => $updated_event
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'updated_event'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get Simple Stats List(s)
    */
    public function simple_stats_get()
    {
        $postset        = $this->get();

        $account_id     = (!empty($postset['account_id'])) ? $postset['account_id'] : false ;

        $expected_data = [
            'account_id' => $account_id
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid or missing Field(s)',
                'simple_stats'  => null
            ];
            $message['message'] = 'Validation errors: ' . trim($message['message']) . ' ' . trim($validation_errors);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'simple_stats'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $simple_stats = $this->people_service->get_simple_stats($account_id);

        if (!empty($simple_stats)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'simple_stats'  => $simple_stats
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'simple_stats'  => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   Get Person Change log history
    */
    public function person_change_logs_get()
    {
        $postset = $validation_errors = false;

        $postset            = $this->get();

        $account_id         = (!empty($postset['account_id'])) ? $postset['account_id'] : false ;
        $person_id          = (!empty($postset['person_id'])) ? $postset['person_id'] : false ;
        $change_log_id      = (!empty($postset['change_log_id'])) ? $postset['change_log_id'] : false ;
        $log_type           = (!empty($postset['log_type'])) ? $postset['log_type'] : false ;
        $where              = (!empty($postset['where'])) ? $postset['where'] : false ;
        $limit              = (!empty($postset['limit'])) ? $postset['limit'] : DEFAULT_LIMIT ;
        $offset             = (!empty($postset['offset'])) ? $postset['offset'] : false ;
        $order_by           = (!empty($postset['order_by'])) ? $postset['order_by'] : false ;

        $expected_data = [
            'account_id'    => $account_id
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '' ;
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'                => false,
                'message'               => 'Invalid or missing Field(s)',
                'person_change_logs'    => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . ' ' . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'person_change_logs'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $person_change_logs = $this->people_service->get_person_change_logs($account_id, $person_id, $change_log_id, $log_type, $where, $limit, $offset, $order_by);

        if (!empty($person_change_logs)) {
            $message = [
                'status'                => true,
                'message'               => $this->session->flashdata('message'),
                'person_change_logs'    => $person_change_logs
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'person_change_logs'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get Checklist Questions(s)
    */
    public function checklist_questions_get()
    {
        $postset        = $this->get();

        $account_id     = (!empty($postset['account_id'])) ? $postset['account_id'] : false ;
        $question_id    = (!empty($postset['question_id'])) ? $postset['question_id'] : false ;
        $category       = (!empty($postset['category'])) ? $postset['category'] : false ;
        $item_type      = (!empty($postset['item_type'])) ? $postset['item_type'] : false ;
        $item_group     = (!empty($postset['item_group'])) ? $postset['item_group'] : false ;
        $where          = (!empty($postset['where'])) ? $postset['where'] : false ;
        $limit          = (!empty($postset['limit'])) ? $postset['limit'] : false ;
        $offset         = (!empty($postset['offset'])) ? $postset['offset'] : false ;
        $order_by       = (!empty($postset['order_by'])) ? $postset['order_by'] : false ;
        $ordered        = (!empty($postset['ordered'])) ? $postset['ordered'] : true ;

        $expected_data = [
            'account_id' => $account_id
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'                => false,
                'message'               => 'Invalid or missing Field(s)',
                'checklist_questions'   => null
            ];
            $message['message'] = 'Validation errors: ' . trim($message['message']) . ' ' . trim($validation_errors);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'checklist_questions'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $checklist_questions = $this->people_service->get_checklist_questions($account_id, $question_id, $category, $item_type, $item_group, $where, $limit, $offset, $order_by, $ordered);

        if (!empty($checklist_questions)) {
            $message = [
                'status'                => true,
                'message'               => $this->session->flashdata('message'),
                'checklist_questions'   => $checklist_questions
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'checklist_questions'   => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Update Checklist
    */
    public function update_checklist_post()
    {
        $postset        = $this->post();

        $account_id     = (!empty($postset['account_id'])) ? $postset['account_id'] : false ;
        $response_id    = (!empty($postset['response_id'])) ? $postset['response_id'] : false ;
        $person_id      = (!empty($postset['person_id'])) ? $postset['person_id'] : false ;
        $category       = (!empty($postset['category'])) ? $postset['category'] : false ;
        $item_type      = (!empty($postset['item_type'])) ? urldecode($postset['item_type']) : false ;
        $answers        = (!empty($postset['answers'])) ? $postset['answers'] : false ;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('answers', 'Answers', 'required');
        $this->form_validation->set_rules('person_id', 'Person ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'                => false,
                'message'               => 'Invalid or missing Field(s)',
                'updated_checklist'     => null
            ];
            $message['message'] = 'Validation errors: ' . trim($message['message']) . ' ' . trim($validation_errors);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'updated_checklist'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $updated_checklist = $this->people_service->update_checklist($account_id, $response_id, $person_id, $category, $item_type, $answers);

        if (!empty($updated_checklist)) {
            $message = [
                'status'                => true,
                'message'               => $this->session->flashdata('message'),
                'updated_checklist'     => $updated_checklist
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'updated_checklist'     => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Get Checklist Answers
    */
    public function checklist_answers_get()
    {
        $postset        = $this->get();

        $account_id     = (!empty($postset['account_id'])) ? $postset['account_id'] : false ;
        $person_id      = (!empty($postset['person_id'])) ? $postset['person_id'] : false ;
        $category       = (!empty($postset['category'])) ? $postset['category'] : false ;
        $response_id    = (!empty($postset['response_id'])) ? $postset['response_id'] : false ;
        $where          = (!empty($postset['where'])) ? $postset['where'] : false ;
        $limit          = (!empty($postset['limit'])) ? $postset['limit'] : false ;
        $offset         = (!empty($postset['offset'])) ? $postset['offset'] : false ;
        $ordered        = (!empty($postset['ordered'])) ? $postset['ordered'] : true ;

        $expected_data = [
            'account_id'    => $account_id,
            'person_id'     => $person_id
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('person_id', 'Person ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'                => false,
                'message'               => 'Invalid or missing Field(s)',
                'checklist_answers'     => null
            ];
            $message['message'] = 'Validation errors: ' . trim($message['message']) . ' ' . trim($validation_errors);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'checklist_answers'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $checklist_answers = $this->people_service->get_checklist_answers($account_id, $person_id, $category, $response_id, $where, $limit, $offset, $ordered);

        if (!empty($checklist_answers)) {
            $message = [
                'status'                => true,
                'message'               => $this->session->flashdata('message'),
                'checklist_answers'     => $checklist_answers
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'checklist_answers'     => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Add new security screening log
    */
    public function create_security_log_post()
    {
        $security_log = $post_data = $person_exists = false;

        $post_data          = $this->post();

        $account_id         = (!empty($post_data['account_id'])) ? $post_data['account_id'] : false ;
        unset($post_data['account_id']);

        $person_id          = (!empty($post_data['person_id'])) ? $post_data['person_id'] : false ;
        unset($post_data['person_id']);

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('person_id', 'Person ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Invalid or missing Field(s)',
                'security_log'      => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'security_log'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $person_exists = $this->people_service->get_people($account_id, false, $person_id);
        if (!$person_exists) {
            $message = [
                'status'        => false,
                'message'       => "Incorrect User ID or Person ID",
                'security_log'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $security_log = $this->people_service->create_security_log($account_id, $person_id, $post_data);

        if (!empty($security_log)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'security_log'      => $security_log
            ];

            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'security_log'      => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /*
    *   Get Health data against the person
    */
    public function security_logs_get()
    {
        $postset = $validation_errors = false;

        $postset        = $this->get();

        $account_id     = (!empty($postset['account_id'])) ? $postset['account_id'] : false ;
        $person_id      = (!empty($postset['person_id'])) ? $postset['person_id'] : false ;
        $log_id         = (!empty($postset['log_id'])) ? $postset['log_id'] : false ;
        $limit          = (!empty($postset['limit'])) ? $postset['limit'] : DEFAULT_LIMIT ;
        $offset         = (!empty($postset['offset'])) ? $postset['offset'] : false ;
        $order_by       = (!empty($postset['order_by'])) ? $postset['order_by'] : false ;

        $expected_data = [
            'account_id'    => $account_id,
            'person_id'     => $person_id
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('person_id', 'Person ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '' ;
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid or missing Field(s)',
                'security_logs' => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . ' ' . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'security_logs' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## check if the person exists
        $person = $this->people_service->get_people($account_id, false, $person_id);

        if (!$person) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'security_logs' => null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }

        $security_logs = $this->people_service->get_security_logs($account_id, $person_id, $log_id, $limit, $offset, $order_by);

        if (!empty($security_logs)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'security_logs' => $security_logs
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'security_logs' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Update Security Screening Log
    */
    public function update_security_log_post()
    {
        $postset        = $this->post();

        $account_id     = (!empty($postset['account_id'])) ? $postset['account_id'] : false ;
        unset($postset['account_id']);

        $person_id      = (!empty($postset['person_id'])) ? $postset['person_id'] : false ;
        unset($postset['person_id']);

        $log_id         = (!empty($postset['log_id'])) ? $postset['log_id'] : false ;
        unset($postset['log_id']);

        $data = $postset;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('log_id', 'Log ID', 'required');
        $this->form_validation->set_rules('person_id', 'Person ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'                => false,
                'message'               => 'Invalid or missing Field(s)',
                'updated_security_log'  => null
            ];
            $message['message'] = 'Validation errors: ' . trim($message['message']) . ' ' . trim($validation_errors);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'updated_security_log'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $log_exists = $this->people_service->get_security_logs($account_id, $person_id, $log_id);

        if (!$log_exists) {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'updated_security_log'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $updated_security_log = $this->people_service->update_security_log($account_id, $log_id, $person_id, $data);

        if (!empty($updated_security_log)) {
            $message = [
                'status'                => true,
                'message'               => $this->session->flashdata('message'),
                'updated_security_log'  => $updated_security_log
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'updated_security_log'  => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /*
    *   Get a list of the leaving company reasons for the leaver by the account ID
    */
    public function leaver_reasons_get()
    {
        $postset = $validation_errors = false;

        $postset        = $this->get();

        $account_id     = (!empty($postset['account_id'])) ? $postset['account_id'] : false ;
        $reason_id      = (!empty($postset['reason_id'])) ? $postset['reason_id'] : false ;
        $ordered        = (!empty($postset['ordered'])) ? $postset['ordered'] : false ;

        $expected_data = [
            'account_id'    => $account_id,
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '' ;
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid or missing Field(s)',
                'reasons'       => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . ' ' . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'reasons' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $reasons = $this->people_service->get_leaver_reasons($account_id, $reason_id, $ordered);

        if (!empty($reasons)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'reasons'       => $reasons
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'reasons' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /*
    *   Create a log for the leaver
    */
    public function create_leavers_log_post()
    {
        $postset = $validation_errors = false;

        $postset        = $this->post();
        $account_id     = (!empty($postset['account_id'])) ? $postset['account_id'] : false ;
        unset($postset['account_id']);

        $person_id      = (!empty($postset['person_id'])) ? $postset['person_id'] : false ;
        unset($postset['person_id']);

        $postdata       = (!empty($postset['data'])) ? json_decode($postset['data']) : false ;
        unset($postset['data']);

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('person_id', 'Person ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '' ;
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Invalid or missing Field(s)',
                'new_leavers_log'   => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . ' ' . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'new_leavers_log'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }


        ## check if the person exists
        $person = $this->people_service->get_people($account_id, false, $person_id);

        if (!$person) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'new_leavers_log'   => null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }

        $new_leavers_log = $this->people_service->create_leavers_log($account_id, $person_id, $postdata);

        if (!empty($new_leavers_log)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'new_leavers_log'   => $new_leavers_log
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'new_leavers_log'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   Get a logs for the specific person or account
    */
    public function leaver_log_get()
    {
        $postset = $validation_errors = false;

        $postset        = $this->get();

        $account_id     = (!empty($postset['account_id'])) ? (int) $postset['account_id'] : false ;
        $person_id      = (!empty($postset['person_id'])) ? (int) $postset['person_id'] : false ;
        $leaver_log_id  = (!empty($postset['leaver_log_id'])) ? (int) $postset['leaver_log_id'] : false ;
        $ordered        = (!empty($postset['ordered'])) ? $postset['ordered'] : false ;
        $limit          = (!empty($postset['limit'])) ? (int) $postset['limit'] : DEFAULT_LIMIT ;
        $offset         = (!empty($postset['offset'])) ? (int) $postset['offset'] : false ;

        $expected_data = [
            'account_id'    => $account_id,
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '' ;
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid or missing Field(s)',
                'logs'      => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . ' ' . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'logs' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!empty($person_id)) {
            ## check if the person exists
            $person = $this->people_service->get_people($account_id, false, $person_id);

            if (!$person) {
                $message = [
                    'status'            => false,
                    'message'           => $this->session->flashdata('message'),
                    'new_leavers_log'   => null
                ];
                $this->response($message, REST_Controller::HTTP_NO_CONTENT);
            }
        }

        $logs = $this->people_service->get_leaver_logs($account_id, $person_id, $leaver_log_id, $ordered, $limit, $offset);

        if (!empty($logs)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'logs'          => $logs
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'logs'          => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Get Event Category list
    */
    public function event_categories_get()
    {
        $postset        = $this->get();

        $account_id     = (!empty($postset['account_id'])) ? $postset['account_id'] : false;
        $category_id    = (!empty($postset['category_id'])) ? $postset['category_id'] : false;
        $category_group = (!empty($postset['category_group'])) ? $postset['category_group'] : false;
        $ordered        = (!empty($postset['ordered'])) ? $postset['ordered'] : false;

        $expected_data = [
            'account_id'    => $account_id ,
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Invalid or missing Field(s)',
                'event_categories'  => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'event_categories'  => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $event_categories = $this->people_service->get_event_categories($account_id, $category_id, $category_group, $ordered);

        if (!empty($event_categories)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'event_categories'  => $event_categories
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'event_categories'  => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /*
    *   This is to update a Position
    */
    public function update_position_post()
    {
        $validation_errors = $post_data = $position_exists = false;

        $post_data      = $this->post();

        $position_id    = (!empty($post_data['position_id'])) ? $post_data['position_id'] : false ;
        unset($post_data['position_id']);


        $data           = (!empty($post_data['dataset'])) ? json_decode($post_data['dataset']) : false ;
        unset($post_data['dataset']);

        $account_id     = (!empty($post_data['account_id'])) ? $post_data['account_id'] : false ;
        unset($post_data['account_id']);

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('position_id', 'Position ID', 'required');
        $this->form_validation->set_rules('dataset', 'Update Data', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Invalid or missing Field(s)',
                'updated_position'  => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'updated_position'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $position_exists = $this->people_service->get_job_positions($account_id, false, $position_id);

        if (!$position_exists) {
            $message = [
                'status'            => false,
                'message'           => "Invalid Position ID",
                'updated_position'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $updated_position = $this->people_service->update_position($account_id, $position_id, $data);

        if (!empty($updated_position)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'updated_position'  => $updated_position
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'updated_position'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   This is to delete a Position
    */
    public function delete_position_post()
    {
        $validation_errors = $post_data = $position_exists = false;

        $post_data      = $this->post();

        $position_id    = (!empty($post_data['position_id'])) ? $post_data['position_id'] : false ;
        unset($post_data['position_id']);

        $account_id     = (!empty($post_data['account_id'])) ? $post_data['account_id'] : false ;
        unset($post_data['account_id']);

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('position_id', 'Position ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Invalid or missing Field(s)',
                'deleted_position'  => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'deleted_position'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $position_exists = $this->people_service->get_job_positions($account_id, false, $position_id);

        if (!$position_exists) {
            $message = [
                'status'            => false,
                'message'           => "Invalid Position ID",
                'deleted_position'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $deleted_position = $this->people_service->delete_position($account_id, $position_id);

        if (!empty($deleted_position)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'deleted_position'  => $deleted_position
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'deleted_position'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   This is to delete an Event
    */
    public function delete_event_post()
    {
        $validation_errors = $post_data = $event_exists = false;

        $post_data      = $this->post();

        $event_id   = (!empty($post_data['event_id'])) ? $post_data['event_id'] : false ;
        unset($post_data['event_id']);

        $account_id     = (!empty($post_data['account_id'])) ? $post_data['account_id'] : false ;
        unset($post_data['account_id']);

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('event_id', 'Event ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Invalid or missing Field(s)',
                'deleted_event'     => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'deleted_event' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $event_exists = $this->people_service->get_events($account_id, $event_id);

        if (!$event_exists) {
            $message = [
                'status'            => false,
                'message'           => "Invalid Event ID",
                'deleted_event'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $deleted_event = $this->people_service->delete_event($account_id, $event_id);

        if (!empty($deleted_event)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'deleted_event'     => $deleted_event
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'deleted_event'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   This function will delete a Security log
    */
    public function delete_security_log_post()
    {
        $validation_errors = $post_data = $security_log_exists = false;

        $post_data      = $this->post();

        $log_id     = (!empty($post_data['security_log_ID'])) ? $post_data['security_log_ID'] : false ;
        unset($post_data['position_id']);

        $account_id     = (!empty($post_data['account_id'])) ? $post_data['account_id'] : false ;
        unset($post_data['account_id']);

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('security_log_ID', 'Security Log ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'                => false,
                'message'               => 'Invalid or missing Field(s)',
                'deleted_security_log'  => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'deleted_security_log'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $security_log_exists = $this->people_service->get_security_logs($account_id, false, $log_id);

        if (!$security_log_exists) {
            $message = [
                'status'                => false,
                'message'               => "Invalid Security ID",
                'deleted_security_log'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $deleted_security_log = $this->people_service->delete_security_log($account_id, $log_id);

        if (!empty($deleted_security_log)) {
            $message = [
                'status'                => true,
                'message'               => $this->session->flashdata('message'),
                'deleted_security_log'  => $deleted_security_log
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'deleted_security_log'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   This is to update a contact address
    */
    public function update_contact_post()
    {
        $validation_errors = $post_data = $contact_exists = false;

        $post_data      = $this->post();

        $contact_id     = (!empty($post_data['contact_id'])) ? $post_data['contact_id'] : false ;
        unset($post_data['contact_id']);

        $data           = (!empty($post_data['dataset'])) ? json_decode($post_data['dataset']) : false ;
        unset($post_data['dataset']);

        $account_id     = (!empty($post_data['account_id'])) ? $post_data['account_id'] : false ;
        unset($post_data['account_id']);

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('contact_id', 'Contact ID', 'required');
        $this->form_validation->set_rules('dataset', 'Update Data', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Invalid or missing Field(s)',
                'updated_contact'   => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'updated_contact'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $contact_exists = $this->people_service->get_address_contacts($account_id, false, $contact_id);

        if (!$contact_exists) {
            $message = [
                'status'            => false,
                'message'           => "Invalid Contact ID",
                'updated_contact'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $updated_contact = $this->people_service->update_contact($account_id, $contact_id, $data);

        if (!empty($updated_contact)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'updated_contact'   => $updated_contact
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'updated_contact'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   This function will delete Contact Address
    */
    public function delete_contact_post()
    {
        $validation_errors = $post_data = $contact_exists = false;

        $post_data      = $this->post();

        $contact_id     = (!empty($post_data['contact_id'])) ? $post_data['contact_id'] : false ;
        unset($post_data['contact_id']);

        $account_id     = (!empty($post_data['account_id'])) ? $post_data['account_id'] : false ;
        unset($post_data['account_id']);

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('contact_id', 'Contact ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Invalid or missing Field(s)',
                'deleted_contact'   => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'deleted_contact'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $contact_exists = $this->people_service->get_address_contacts($account_id, false, $contact_id);

        if (!$contact_exists) {
            $message = [
                'status'            => false,
                'message'           => "Invalid Contact ID",
                'deleted_contact'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $deleted_contact = $this->people_service->delete_contact($account_id, $contact_id);

        if (!empty($deleted_contact)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'deleted_contact'   => $deleted_contact
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'deleted_contact'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
