<?php

namespace Application\Service\Controllers\Api;

use App\Libraries\REST_Controller;

defined('BASEPATH') || exit('No direct script access allowed');

class Audit extends REST_Controller
{
    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('Audit_model', 'audit_service');
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->lang->load('auth');
    }

    /**
    * Create new Audit resource
    */
    public function create_post()
    {
        $audit_data     = $this->post();
        $site_id        = $this->post('site_id');
        $asset_id       = $this->post('asset_id');
        $vehicle_reg    = $this->post('vehicle_reg');
        $account_id     = $this->post('account_id');
        $audit_type_id  = $this->post('audit_type_id');

        $this->form_validation->set_rules('audit_type_id', 'Audit Type', 'required');
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid Audit data: ',
                'audit' => null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID',
                'audit' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_audit = $this->audit_service->create_audit($account_id, $audit_type_id, $audit_data);

        if (!empty($new_audit)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'audit' => $new_audit
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'audit' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Update user resource
    */
    public function update_post()
    {
        $audit_data = $this->post();
        $audit_id   = (int) $this->post('audit_id');
        $account_id = (int) $this->post('account_id');

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('audit_id', 'Audit ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid data: ',
                'audit' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'] . 'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'audit' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Validate the audit id.
        if ($audit_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        $audit = $this->audit_service->get_audits($account_id, $audit_id);
        if (!$audit) {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'audit' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Run audit update
        $updated_asset = $this->audit_service->update_asset($account_id, $audit_id, $audit_data);
        if (!empty($updated_asset)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'audit' => $updated_asset
            ];
            $this->response($message, REST_Controller::HTTP_OK); // Resource Updated
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'audit' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all Audits or single record
    */
    public function audits_get()
    {
        $account_id         = (int) $this->get('account_id');
        $audit_id           = (int) $this->get('audit_id');
        $asset_id           = (int) $this->get('asset_id');
        $site_id            = (int) $this->get('site_id');
        $audit_status       = urldecode($this->get('audit_status'));
        $vehicle_reg        = urldecode($this->get('vehicle_reg'));
        $inc_responses      = $this->get('inc_responses');
        $where              = ($this->get('where')) ? $this->get('where') : [];
        $order_by           = ($this->get('order_by')) ? $this->get('order_by') : false;
        $limit              = ($this->get('limit')) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset             = ($this->get('offset')) ? (int) $this->get('offset') : 0;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID',
                'audits' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $audit      = $this->audit_service->get_audits($account_id, $audit_id, $asset_id, $site_id, $vehicle_reg, $audit_status, $inc_responses, $where, $order_by, $limit, $offset);
        if (!empty($audit)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'audits' => $audit
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'audits' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Delete Audit resource
    */
    public function delete_get()
    {
        $account_id = (int) $this->get('account_id');
        $audit_id   = (int) $this->get('audit_id');

        if ($audit_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'audit' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $delete_asset = $this->audit_service->delete_asset($account_id, $audit_id);

        if (!empty($delete_asset)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'audit' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'audit' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Search through list of Audits
    */
    public function lookup_get()
    {
        $account_id     = (int) $this->get('account_id');
        $where          = $this->get('where');
        $order_by       = $this->get('order_by');
        $limit          = ($this->get('limit')) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset         = ($this->get('offset')) ? (int) $this->get('offset') : 0;
        $audit_statuses = $this->get('audit_statuses');
        $audit_types    = $this->get('audit_types');
        $search_term    = trim(urldecode($this->get('search_term')));

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'audits' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $audit_lookup = $this->audit_service->audit_lookup($account_id, $search_term, $audit_types, $audit_statuses, $where, $order_by, $limit, $offset);
        ;

        if (!empty($audit_lookup)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'audits' => $audit_lookup
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'audits' => $audit_lookup
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all audit types
    */
    public function audit_types_get()
    {
        $account_id   = (int) $this->get('account_id');
        $audit_group  = $this->get('audit_group');
        $audit_type_id = $this->get('audit_type_id');
        $category     = $this->get('category');
        $categorized  = $this->get('categorized');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'audit_types' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $audit_types = $this->audit_service->get_audit_types($account_id, $audit_group, $audit_type_id, $categorized, $category);

        if (!empty($audit_types)) {
            $message = [
                'status' => true,
                'message' => 'Audit types records found',
                'audit_types' => $audit_types
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => 'No records found',
                'audit_types' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all Audit questions based on assessment type
    */
    public function audit_questions_get()
    {
        $account_id     = (int) $this->get('account_id');
        $audit_type_id  = (int) $this->get('audit_type_id');
        $asset_type_id  = (int) $this->get('asset_type_id');
        $section_ref    = strip_all_whitespace($this->get('section_ref'));
        $segment        = $this->get('segment');
        $segmented      = $this->get('segmented');
        $un_grouped     = $this->get('un_grouped');

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('audit_type_id', 'Account ID', 'required');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid main Account ID',
                'audit_questions' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $questions  = $this->audit_service->get_audit_questions($account_id, $audit_type_id, $asset_type_id, $section_ref, $segment, $segmented, $un_grouped);

        if (!empty($questions)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'audit_questions' => $questions
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => 'No records found',
                'audit_questions' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Get Get a list of required Sectiuons **/
    public function required_sections_get()
    {
        $account_id     = (int) $this->get('account_id');
        $audit_type_id  = (int) $this->get('audit_type_id');
        $asset_type_id  = (int) $this->get('asset_type_id');
        $audit_id       = (int) $this->get('audit_id');
        $un_grouped     = $this->get('un_grouped');
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('audit_type_id', 'Account ID', 'required');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid main Account ID',
                'required_sections' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $required_sections  = $this->audit_service->get_required_sections($account_id, $audit_type_id, $asset_type_id, $audit_id, $un_grouped);

        if (!empty($required_sections)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'required_sections' => $required_sections
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => 'No records found',
                'required_sections' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Get Get a list of required Sectiuons **/
    public function asset_list_get()
    {
        $account_id     = (int) $this->get('account_id');
        $site_id        = (int) $this->get('site_id');
        $check_range    = (!empty($this->get('check_range'))) ? $this->get('check_range') : DEFAULT_AUDIT_CHECK_RANGE;
        $req_percentage = (!empty($this->get('req_percentage'))) ? $this->get('req_percentage') : DEFAULT_AUDIT_REQ_PERCENTAGE;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        #$this->form_validation->set_rules('site_id', 'Site ID', 'required');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid main Account ID',
                'asset_list' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $asset_list = $this->audit_service->get_audit_asset_list($account_id, $site_id, $check_range, $req_percentage);

        if (!empty($asset_list)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'asset_list' => $asset_list
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => 'No records found',
                'asset_list' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all locations
    */
    public function locations_get()
    {
        $account_id     = (int) $this->get('account_id');
        $location_group = $this->get('location_group');
        $grouped        = (int) $this->get('grouped');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'locations' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $locations = $this->ssid_common->get_locations($account_id, $location_group, false, false, false, false, $grouped);

        if (!empty($locations)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'locations' => $locations
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'locations' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
