<?php

namespace Application\Service\Controllers\Api;

use App\Libraries\REST_Controller;

class Distribution extends REST_Controller
{
    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('Distribution_model', 'distribution_service');
        $this->load->model('Coggins_Api_model', 'coggins_service');
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->lang->load('auth');
    }

    /** Get Distribution Groups **/
    public function distribution_groups_get()
    {
        $account_id             = (int) $this->get('account_id');
        $distribution_group_id  = (int) $this->get('distribution_group_id');
        $search_term            = (!empty($this->get('search_term'))) ? trim(urldecode($this->get('search_term'))) : false ;
        $where                  = (!empty($this->get('where'))) ? $this->get('where') : false;
        $order_by               = (!empty($this->get('order_by'))) ? $this->get('order_by') : false;
        $limit                  = (!empty($this->get('limit'))) ? $this->get('limit') : false;
        $offset                 = (!empty($this->get('offset'))) ? $this->get('offset') : false;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'               => 'Invalid main Account ID',
                'distribution_groups'   => null,
                'counters'              => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $distribution_groups = $this->distribution_service->get_distribution_groups($account_id, $distribution_group_id, $search_term, $where, $order_by, $limit, $offset);

        if (!empty($distribution_groups)) {
            $message = [
                'status'                => true,
                'http_code'             => REST_Controller::HTTP_OK,
                'message'               => $this->session->flashdata('message'),
                'distribution_groups'   => (!empty($distribution_groups->records)) ? $distribution_groups->records : (!empty($distribution_groups) ? $distribution_groups : null),
                'counters'              => (!empty($distribution_groups->counters)) ? $distribution_groups->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_NO_CONTENT,
                'message'               => 'No records found',
                'distribution_groups'   => null,
                'counters'              => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Create a Distribution Group **/
    public function add_distribution_group_post()
    {
        $distribution_group_data = $this->post();
        $account_id        = (int) $this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('distribution_group', 'Distribution Group', 'required');
        $this->form_validation->set_rules('associated_territory_id', 'Associated Territory', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_BAD_REQUEST,
                'message'           => 'Invalid data: ',
                'distribution_group' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'] . 'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'           => 'Invalid main Account ID.',
                'distribution_group' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_distribution_group = $this->distribution_service->add_distribution_group($account_id, $distribution_group_data);

        if (!empty($new_distribution_group)) {
            $message = [
                'status'            => true,
                'http_code'         => REST_Controller::HTTP_OK,
                'message'           => $this->session->flashdata('message'),
                'distribution_group' => $new_distribution_group
            ];
            $this->response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_BAD_REQUEST,
                'message'           => $this->session->flashdata('message'),
                'distribution_group' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Update Distribution Group **/
    public function update_distribution_group_post()
    {
        $distribution_group_data = $this->post();
        $account_id           = (int) $this->post('account_id');

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('distribution_group', 'Distribution Group', 'required');
        $this->form_validation->set_rules('associated_territory_id', 'Territory', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_BAD_REQUEST,
                'message'           => 'Invalid data: ',
                'distribution_group' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'] . 'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'           => 'Invalid main Account ID.',
                'distribution_group' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $update_distribution_group = $this->distribution_service->update_distribution_group($account_id, $distribution_group_data);

        if (!empty($update_distribution_group)) {
            $message = [
                'status'            => true,
                'http_code'         => REST_Controller::HTTP_OK,
                'message'           => $this->session->flashdata('message'),
                'distribution_group' => $update_distribution_group
            ];
            $this->response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_NOT_MODIFIED,
                'message'           => $this->session->flashdata('message'),
                'distribution_group' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Delete Distribution Group
    */
    public function delete_distribution_group_post()
    {
        $account_id             = (int) $this->post('account_id');
        $distribution_group_id  = (int) $this->post('distribution_group_id');

        if ($distribution_group_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_NO_CONTENT,
                'message'           => 'Invalid main Account ID.',
                'distribution_group' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $delete_distribution_group = $this->distribution_service->delete_distribution_group($account_id, $distribution_group_id);

        if (!empty($delete_distribution_group)) {
            $message = [
                'status'            => true,
                'http_code'         => REST_Controller::HTTP_OK,
                'message'           => $this->session->flashdata('message'),
                'distribution_group' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_NOT_MODIFIED,
                'message'           => $this->session->flashdata('message'),
                'distribution_group' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Add Distribution Group Sites **/
    public function add_distribution_group_sites_post()
    {
        $postdata               = $this->post();
        $account_id             = $this->post('account_id');
        $distribution_group_id  = $this->post('distribution_group_id');
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('distribution_group_id', 'Distribution Group ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            ## One of the required fields is invalid
            $message = [
                'status'                    => false,
                'http_code'                 => REST_Controller::HTTP_BAD_REQUEST,
                'message'                   => 'Invalid Job data: ',
                'distribution_group_sites'  => null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                    => false,
                'http_code'                 => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'                   => 'Invalid main Account ID',
                'distribution_group_sites'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_items = $this->distribution_service->add_distribution_group_sites($account_id, $distribution_group_id, $postdata);

        if (!empty($new_items)) {
            $message = [
                'status'                    => true,
                'http_code'                 => REST_Controller::HTTP_OK,
                'message'                   => $this->session->flashdata('message'),
                'distribution_group_sites'  => $new_items
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'                    => false,
                'http_code'                 => REST_Controller::HTTP_NO_CONTENT,
                'message'                   => $this->session->flashdata('message'),
                'distribution_group_sites'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Get Distribution Group Sites **/
    public function distribution_group_sites_get()
    {
        $account_id             = (int) $this->get('account_id');
        $distribution_group_id  = (int) $this->get('distribution_group_id');
        $where                  = (!empty($this->get('where'))) ? $this->get('where') : false ;
        $order_by               = (!empty($this->get('order_by'))) ? $this->get('order_by') : false ;
        $limit                  = ($this->get('limit')) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset                 = (!empty($this->get('offset'))) ? (int) $this->get('offset') : 0 ;

        $this->form_validation->set_data(['account_id' => $account_id, 'distribution_group_id' => $distribution_group_id ]);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('distribution_group_id', 'Distribution Group ID', 'required');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                    => false,
                'http_code'                 => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'                   => ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid main Account ID',
                'distribution_group_sites'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $distribution_group_sites   = $this->distribution_service->get_distribution_group_sites($account_id, $distribution_group_id, $where, $order_by, $limit, $offset);

        if (!empty($distribution_group_sites)) {
            $message = [
                'status'                    => true,
                'http_code'                 => REST_Controller::HTTP_OK,
                'message'                   => $this->session->flashdata('message'),
                'distribution_group_sites'  => $distribution_group_sites
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                    => false,
                'http_code'                 => REST_Controller::HTTP_NO_CONTENT,
                'message'                   => 'No data found',
                'distribution_group_sites' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Get Distribution Bundles **/
    public function distribution_bundles_get()
    {
        $account_id             = (int) $this->get('account_id');
        $distribution_group_id  = (int) $this->get('distribution_group_id');
        $distribution_bundle_id = (int) $this->get('distribution_bundle_id');
        $search_term            = (!empty($this->get('search_term'))) ? trim(urldecode($this->get('search_term'))) : false ;
        $where                  = (!empty($this->get('where'))) ? $this->get('where') : false;
        $order_by               = (!empty($this->get('order_by'))) ? $this->get('order_by') : false;
        $limit                  = (!empty($this->get('limit'))) ? (int) $this->get('limit') : false;
        $offset                 = (!empty($this->get('offset'))) ? (int) $this->get('offset') : false;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'               => 'Invalid main Account ID',
                'distribution_bundles'  => null,
                'counters'              => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $distribution_bundles = $this->distribution_service->get_distribution_bundles($account_id, $distribution_group_id, $distribution_bundle_id, $search_term, $where, $order_by, $limit, $offset);

        if (!empty($distribution_bundles)) {
            $message = [
                'status'                => true,
                'http_code'             => REST_Controller::HTTP_OK,
                'message'               => $this->session->flashdata('message'),
                'distribution_bundles'  => (!empty($distribution_bundles->records)) ? $distribution_bundles->records : (!empty($distribution_bundles) ? $distribution_bundles : null),
                'counters'              => (!empty($distribution_bundles->counters)) ? $distribution_bundles->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_NO_CONTENT,
                'message'               => 'No records found',
                'distribution_bundles'  => null,
                'counters'              => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Create a Distribution Bundle **/
    public function add_distribution_bundle_post()
    {
        $distribution_bundle_data = $this->post();
        $account_id        = (int) $this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('distribution_bundle', 'Distribution Bundle', 'required');
        $this->form_validation->set_rules('distribution_group_id', 'Distribution Bundle', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_BAD_REQUEST,
                'message'           => 'Invalid data: ',
                'distribution_bundle' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'] . 'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'           => 'Invalid main Account ID.',
                'distribution_bundle' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_distribution_bundle = $this->distribution_service->add_distribution_bundle($account_id, $distribution_bundle_data);

        if (!empty($new_distribution_bundle)) {
            $message = [
                'status'            => true,
                'http_code'         => REST_Controller::HTTP_OK,
                'message'           => $this->session->flashdata('message'),
                'distribution_bundle' => $new_distribution_bundle
            ];
            $this->response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_BAD_REQUEST,
                'message'           => $this->session->flashdata('message'),
                'distribution_bundle' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Update Distribution Bundle **/
    public function update_distribution_bundle_post()
    {
        $distribution_bundle_data = $this->post();
        $account_id           = (int) $this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('distribution_bundle', 'Bundle Name', 'required');
        $this->form_validation->set_rules('distribution_group_id', 'Distribution Group ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_BAD_REQUEST,
                'message'           => 'Invalid data: ',
                'distribution_bundle' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'] . 'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'           => 'Invalid main Account ID.',
                'distribution_bundle' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $update_distribution_bundle = $this->distribution_service->update_distribution_bundle($account_id, $distribution_bundle_data);

        if (!empty($update_distribution_bundle)) {
            $message = [
                'status'            => true,
                'http_code'         => REST_Controller::HTTP_OK,
                'message'           => $this->session->flashdata('message'),
                'distribution_bundle' => $update_distribution_bundle
            ];
            $this->response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_NOT_MODIFIED,
                'message'           => $this->session->flashdata('message'),
                'distribution_bundle' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Delete Distribution Bundle
    */
    public function delete_distribution_bundle_post()
    {
        $account_id             = (int) $this->post('account_id');
        $distribution_bundle_id = (int) $this->post('distribution_bundle_id');

        if ($distribution_bundle_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_NO_CONTENT,
                'message'           => 'Invalid main Account ID.',
                'distribution_bundle' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $delete_distribution_bundle = $this->distribution_service->delete_distribution_bundle($account_id, $distribution_bundle_id);

        if (!empty($delete_distribution_bundle)) {
            $message = [
                'status'            => true,
                'http_code'         => REST_Controller::HTTP_OK,
                'message'           => $this->session->flashdata('message'),
                'distribution_bundle' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_NOT_MODIFIED,
                'message'           => $this->session->flashdata('message'),
                'distribution_bundle' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Create a Distribution Group Provider **/
    public function add_distribution_group_provider_post()
    {
        $distribution_group_provider_data = $this->post();
        $account_id             = (int) $this->post('account_id');
        $distribution_group_id  = (int) $this->post('distribution_group_id');

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('distribution_group_id', 'Distribution Group ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status'                     => false,
                'http_code'                  => REST_Controller::HTTP_BAD_REQUEST,
                'message'                    => 'Invalid data: ',
                'distribution_group_provider' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'] . 'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                     => false,
                'http_code'                  => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'                    => 'Invalid main Account ID.',
                'distribution_group_provider' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_distribution_group_provider = $this->distribution_service->add_distribution_group_providers($account_id, $distribution_group_id, $distribution_group_provider_data);

        if (!empty($new_distribution_group_provider)) {
            $message = [
                'status'                     => true,
                'http_code'                  => REST_Controller::HTTP_OK,
                'message'                    => $this->session->flashdata('message'),
                'distribution_group_provider' => $new_distribution_group_provider
            ];
            $this->response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = [
                'status'                     => false,
                'http_code'                  => REST_Controller::HTTP_BAD_REQUEST,
                'message'                    => $this->session->flashdata('message'),
                'distribution_group_provider' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Delete Distribution Group Provider
    */
    public function delete_distribution_group_provider_post()
    {
        $account_id             = (int) $this->post('account_id');
        $combination_id = (int) $this->post('combination_id');

        if ($combination_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                     => false,
                'http_code'                  => REST_Controller::HTTP_NO_CONTENT,
                'message'                    => 'Invalid main Account ID.',
                'distribution_group_provider' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $delete_distribution_group_provider = $this->distribution_service->delete_distribution_group_provider($account_id, $combination_id);

        if (!empty($delete_distribution_group_provider)) {
            $message = [
                'status'                        => true,
                'http_code'                     => REST_Controller::HTTP_OK,
                'message'                       => $this->session->flashdata('message'),
                'distribution_group_provider'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                     => false,
                'http_code'                  => REST_Controller::HTTP_NOT_MODIFIED,
                'message'                    => $this->session->flashdata('message'),
                'distribution_group_provider' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Get Distribution Bundle Content **/
    public function bundle_content_get()
    {
        $account_id             = (int) $this->get('account_id');
        $distribution_bundle_id = (int) $this->get('distribution_bundle_id');
        $bundle_content_id      = (int) $this->get('bundle_content_id');
        $where                  = (!empty($this->get('where'))) ? $this->get('where') : false;
        $order_by               = (!empty($this->get('order_by'))) ? $this->get('order_by') : false ;
        $limit                  = ($this->get('limit')) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset                 = (!empty($this->get('offset'))) ? (int) $this->get('offset') : 0;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'           => 'Invalid main Account ID',
                'bundle_content'    => null,
                'counters'          => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $bundle_content = $this->distribution_service->get_distribution_bundle_content($account_id, $distribution_bundle_id, $bundle_content_id, $where, $order_by, $limit, $offset);

        if (!empty($bundle_content)) {
            $message = [
                'status'            => true,
                'http_code'         => REST_Controller::HTTP_OK,
                'message'           => $this->session->flashdata('message'),
                'bundle_content'    => (!empty($bundle_content->records)) ? $bundle_content->records : (!empty($bundle_content) ? $bundle_content : null),
                'counters'          => (!empty($bundle_content->counters)) ? $bundle_content->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_NO_CONTENT,
                'message'           => 'No records found',
                'bundle_content'    => null,
                'counters'          => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Update Bundle Content record **/
    public function update_bundle_content_post()
    {
        $bundle_content_data = $this->post();
        $account_id           = (int) $this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        #$this->form_validation->set_rules( 'bundle_content_id', 'Bundle Content ID', 'required' );
        #$this->form_validation->set_rules( 'distribution_bundle_id', 'Bundle ID', 'required' );

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_BAD_REQUEST,
                'message'       => 'Invalid data: ',
                'bundle_content' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'] . 'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'       => 'Invalid main Account ID.',
                'bundle_content' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $update_bundle_content = $this->distribution_service->update_bundle_content($account_id, $bundle_content_data);

        if (!empty($update_bundle_content)) {
            $message = [
                'status'        => true,
                'http_code'     => REST_Controller::HTTP_OK,
                'message'       => $this->session->flashdata('message'),
                'bundle_content' => $update_bundle_content
            ];
            $this->response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_NOT_MODIFIED,
                'message'       => $this->session->flashdata('message'),
                'bundle_content' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Get Distribution Bundle Content for Auto-removal**/
    public function auto_remove_content_get()
    {
        $account_id             = (int) $this->get('account_id');
        $distribution_group_id  = (int) $this->get('distribution_group_id');
        $bundle_content_id      = (int) $this->get('bundle_content_id');
        $where                  = (!empty($this->get('where'))) ? $this->get('where') : false;
        $order_by               = (!empty($this->get('order_by'))) ? $this->get('order_by') : false ;
        $limit                  = ($this->get('limit')) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset                 = (!empty($this->get('offset'))) ? (int) $this->get('offset') : 0;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'           => 'Invalid main Account ID',
                'bundle_content'    => null,
                'counters'          => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $bundle_content = $this->distribution_service->get_auto_remove_content($account_id, $distribution_group_id, $where, $order_by, $limit, $offset);

        if (!empty($bundle_content)) {
            $message = [
                'status'            => true,
                'http_code'         => REST_Controller::HTTP_OK,
                'message'           => $this->session->flashdata('message'),
                'bundle_content'    => (!empty($bundle_content->records)) ? $bundle_content->records : (!empty($bundle_content) ? $bundle_content : null),
                'counters'          => (!empty($bundle_content->counters)) ? $bundle_content->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_NO_CONTENT,
                'message'           => 'No records found',
                'bundle_content'    => null,
                'counters'          => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Check Bundle Validity for Dispatch
    **/
    public function check_bundle_validity_get()
    {
        $account_id             = (int) $this->get('account_id');
        $distribution_bundle_id = (int) $this->get('distribution_bundle_id');
        $where                  = $this->get('where');

        $this->form_validation->set_data(['account_id' => $account_id, 'distribution_bundle_id' => $distribution_bundle_id ]);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('distribution_bundle_id', 'Distribution Bundle ID', 'required');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'           => ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid main Account ID',
                'bundle_validity'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $bundle_validity    = $this->distribution_service->check_bundle_validity($account_id, $distribution_bundle_id, $where);

        if (!empty($bundle_validity)) {
            $message = [
                'status'            => true,
                'http_code'         => REST_Controller::HTTP_OK,
                'message'           => $this->session->flashdata('message'),
                'bundle_validity'   => $bundle_validity
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_NO_CONTENT,
                'message'           => 'No data found',
                'bundle_validity'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Send Distribution Bundle for CDSto pick up **/
    public function send_distribution_bundle_post()
    {
        $postdata               = $this->post();
        $account_id             = (int) $this->post('account_id');
        $distribution_group_id  = (int) $this->post('distribution_group_id');
        $distribution_bundle_id = (int) $this->post('distribution_bundle_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('distribution_group_id', 'Ditribution Group ID', 'required');
        $this->form_validation->set_rules('distribution_bundle_id', 'Bundle ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_BAD_REQUEST,
                'message'           => 'Invalid data: ',
                'distribution_bundle' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'] . 'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'           => 'Invalid main Account ID.',
                'distribution_bundle' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $send_distribution_bundle = $this->distribution_service->send_distribution_bundle($account_id, $distribution_group_id, $distribution_bundle_id, $postdata);

        if (!empty($send_distribution_bundle['data']) && (isset($send_distribution_bundle['status']) && ($send_distribution_bundle['status'] != false))) {
            $message = [
                'status'             => true,
                'http_code'          => REST_Controller::HTTP_OK,
                'message'            => (isset($send_distribution_bundle['message']) && (!empty($send_distribution_bundle['message']))) ? $send_distribution_bundle['message'] : $this->session->flashdata('message'),
                'distribution_bundle' => $send_distribution_bundle['data']
            ];
            $this->response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = [
                'status'             => false,
                'http_code'          => REST_Controller::HTTP_NOT_MODIFIED,
                'message'            => (isset($send_distribution_bundle['message']) && (!empty($send_distribution_bundle['message']))) ? $send_distribution_bundle['message'] : $this->session->flashdata('message'),
                'distribution_bundle' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Get Running
    **/
    public function cds_running_get()
    {
        $get_set        = $this->get();
        $account_id     = (!empty($get_set['account_id'])) ? (int) $get_set['account_id'] : false;
        $where          = (!empty($get_set['where'])) ? $get_set['where'] : false;

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
                'status'                => false,
                'message'               => 'Validation errors: ' . $validation_errors,
                'running'               => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'               => 'Invalid main Account ID',
                'running'               => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $running = $this->coggins_service->get_running($account_id, $where);

        if (!empty($running->data)) {
            $message = [
                'status'                => true,
                'http_code'             => REST_Controller::HTTP_OK,
                'message'               => $this->session->flashdata('message'),
                'running'               => $running->data,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_NO_CONTENT,
                'message'               => 'No records found',
                'running'               => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get Distribution Server (d_servers) saved in CaCTi (non - Coggins call)
    **/
    public function distribution_server_get()
    {
        $get_set        = $this->get();
        $account_id     = (!empty($get_set['account_id'])) ? (int) $get_set['account_id'] : false;
        $where          = (!empty($get_set['where'])) ? $get_set['where'] : false;

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
                'status'                => false,
                'message'               => 'Validation errors: ' . $validation_errors,
                'd_servers'                 => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'               => 'Invalid main Account ID',
                'd_servers'             => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $distribution_servers = $this->distribution_service->get_distribution_servers($account_id, $where);

        if (!empty($distribution_servers)) {
            $message = [
                'status'                => true,
                'http_code'             => REST_Controller::HTTP_OK,
                'message'               => $this->session->flashdata('message'),
                'd_servers'             => $distribution_servers,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_NO_CONTENT,
                'message'               => 'No records found',
                'd_servers'             => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get a list of available servers (a_servers) from Coggins
    **/
    public function available_servers_get()
    {
        $get_set        = $this->get();
        $account_id     = (!empty($get_set['account_id'])) ? (int) $get_set['account_id'] : false;
        $where          = (!empty($get_set['where'])) ? $get_set['where'] : false;

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
                'status'                => false,
                'message'               => 'Validation errors: ' . $validation_errors,
                'a_servers'             => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'               => 'Invalid main Account ID',
                'a_servers'             => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $available_servers = $this->distribution_service->get_available_servers($account_id, $where);

        if (!empty($available_servers)) {
            $message = [
                'status'                => true,
                'http_code'             => REST_Controller::HTTP_OK,
                'message'               => $this->session->flashdata('message'),
                'a_servers'             => $available_servers,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_NO_CONTENT,
                'message'               => (!empty($this->session->flashdata('message'))) ? $this->session->flashdata('message') : 'No records found' ,
                'a_servers'             => null,
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Link the server from Coggins and add it to the CaCTi
    **/
    public function add_server_post()
    {
        $postdata               = $this->post();
        $account_id             = (!empty($postdata['account_id'])) ? (int) $postdata['account_id'] : '' ;
        $coggins_server_id      = (!empty($postdata['coggins_server_id'])) ? (int) $postdata['coggins_server_id'] : '' ;
        $notification_points    = (!empty($postdata['notification_points'])) ? $postdata['notification_points'] : '' ;
        $server_data            = (!empty($postdata['server_data'])) ? $postdata['server_data'] : '' ;
        $server_description     = (!empty($postdata['server_description'])) ? $postdata['server_description'] : '' ;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('coggins_server_id', 'Coggins Server ID', 'required');
        $this->form_validation->set_rules('notification_points', 'Notification Point(s)', 'required');
        $this->form_validation->set_rules('server_data', 'Data of the server', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if ((isset($validation_errors) && !empty($validation_errors))) {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_BAD_REQUEST,
                'message'           => 'Validation errors: ' . $validation_errors,
                'server'            => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$account_id || !$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'           => 'Invalid main Account ID.',
                'server'            => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $server = $this->distribution_service->add_distribution_server($account_id, $coggins_server_id, $notification_points, $server_data, $server_description);

        if (!empty($server)) {
            $message = [
                'status'            => true,
                'http_code'         => REST_Controller::HTTP_OK,
                'message'           => $this->session->flashdata('message'),
                'server'            => $server
            ];
            $this->response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_NOT_MODIFIED,
                'message'           => $this->session->flashdata('message'),
                'server'            => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Get Waiting in the Queue from Coggins
    **/
    public function cds_queue_waiting_get()
    {
        $get_set        = $this->get();
        $account_id     = (!empty($get_set['account_id'])) ? (int) $get_set['account_id'] : false;
        $where          = (!empty($get_set['$where'])) ? $get_set['$where'] : false;

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
                'status'                => false,
                'message'               => 'Validation errors: ' . $validation_errors,
                'queue_waiting'         => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'               => 'Invalid main Account ID',
                'queue_waiting'         => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $queue_waiting = $this->coggins_service->get_queue_waiting($account_id, $where);

        if (!empty($queue_waiting->data)) {
            $message = [
                'status'                => true,
                'http_code'             => REST_Controller::HTTP_OK,
                'message'               => $this->session->flashdata('message'),
                'queue_waiting'         => $queue_waiting->data,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_NO_CONTENT,
                'message'               => 'No records found',
                'queue_waiting'         => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get Completed in the Queue from Coggins
    **/
    public function cds_queue_completed_get()
    {
        $get_set        = $this->get();
        $account_id     = (!empty($get_set['account_id'])) ? (int) $get_set['account_id'] : false;
        $where          = (!empty($get_set['where'])) ? $get_set['where'] : false;

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
                'status'                => false,
                'message'               => 'Validation errors: ' . $validation_errors,
                'queue_completed'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'               => 'Invalid main Account ID',
                'queue_completed'       => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $queue_completed = $this->coggins_service->get_queue_finished($account_id, $where);

        if (!empty($queue_completed->data)) {
            $message = [
                'status'                => true,
                'http_code'             => REST_Controller::HTTP_OK,
                'message'               => $this->session->flashdata('message'),
                'queue_completed'       => $queue_completed->data,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_NO_CONTENT,
                'message'               => 'No records found',
                'queue_completed'       => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get Waiting in the Queue from Coggins
    **/
    public function cds_completed_get()
    {
        $get_set        = $this->get();
        $account_id     = (!empty($get_set['account_id'])) ? (int) $get_set['account_id'] : false;
        $where          = (!empty($get_set['where'])) ? $get_set['where'] : false;

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
                'status'                => false,
                'message'               => 'Validation errors: ' . $validation_errors,
                'completed'             => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'               => 'Invalid main Account ID',
                'completed'             => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $completed = $this->coggins_service->get_completed($account_id, $where);

        if (!empty($completed->data)) {
            $message = [
                'status'                => true,
                'http_code'             => REST_Controller::HTTP_OK,
                'message'               => $this->session->flashdata('message'),
                'completed'             => $completed->data,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_NO_CONTENT,
                'message'               => 'No records found',
                'completed'             => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Cancel Distribution Bundle on Coggins
    */
    public function cancel_distribution_bundle_post()
    {
        $post_data                  = $this->post();
        $account_id                 = (!empty($post_data['account_id'])) ? (int) $post_data['account_id'] : '' ;
        $distribution_bundle_id     = (!empty($post_data['distribution_bundle_id'])) ? (int) $post_data['distribution_bundle_id'] : '' ;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('distribution_bundle_id', 'Distribution Bundle ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : false ;
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_BAD_REQUEST,
                'message'           => 'Validation errors: ' . $validation_errors,
                'cancelled_bundle'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if ((!$account_id) || ($distribution_bundle_id <= 0)) {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_BAD_REQUEST,
                'message'           => 'Invalid data: Account ID or Bundle ID',
                'cancelled_bundle	' => null
            ];
            $this->response(null, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_BAD_REQUEST,
                'message'           => 'Invalid main Account ID.',
                'cancelled_bundle'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $cancel_distribution_bundle = $this->distribution_service->cancel_distribution_bundle($account_id, $distribution_bundle_id);

        if (!empty($cancel_distribution_bundle)) {
            $message = [
                'status'            => true,
                'http_code'         => REST_Controller::HTTP_OK,
                'message'           => $this->session->flashdata('message'),
                'cancelled_bundle'  => $cancel_distribution_bundle
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_NOT_MODIFIED,
                'message'           => $this->session->flashdata('message'),
                'cancelled_bundle'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   Delete notification point for the Coggins server
    */
    public function delete_notification_point_post()
    {
        $post_set       = $this->post();

        $account_id     = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $point_id       = (!empty($post_set['point_id'])) ? (int) $post_set['point_id'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('point_id', 'Notification Point ID', 'required|is_natural_no_zero');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'point'         => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'point'         => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## check the point exists
        $notification_point_exists = $this->distribution_service->get_notification_point($account_id, ["point_id" => $point_id]);
        if ((!$notification_point_exists) || empty($point_id) || ((int) $point_id <= 0)) {
            $message = [
                'status'        => false,
                'message'       => "Invalid Point ID",
                'point'         => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $deleted_point = $this->distribution_service->delete_notification_point($account_id, $point_id);

        if (!empty($deleted_point)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'point'         => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'point'         => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   Add server notification point to CaCTI's server
    */
    public function add_notification_point_post()
    {
        $post_set       = $this->post();

        $postdata       = (!empty($post_set)) ? $post_set : false;
        $account_id     = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $server_id      = (!empty($post_set['server_id'])) ? (int) $post_set['server_id'] : false;
        $email          = (!empty($post_set['email'])) ? $post_set['email'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('server_id', 'Server ID', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('email', 'Notification Point email', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'point'         => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'point'         => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## check the point exists
        $notification_point_exists = false;
        $notification_point_exists = $this->distribution_service->get_notification_point($account_id, ["server_id" => $server_id, "email" => $email]);

        if ($notification_point_exists) {
            $message = [
                'status'        => false,
                'message'       => "Notification point already exists",
                'point'         => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_point = $this->distribution_service->add_single_notification_point($account_id, $postdata);

        if (!empty($new_point)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'point'         => $new_point
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'point'         => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /*
    *   Update server description (only) and notification point(s)
    */
    public function update_server_post()
    {
        $post_set       = $this->post();
        $postdata       = (!empty($post_set)) ? $post_set : false;
        $account_id     = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $server_id      = (!empty($post_set['server_id'])) ? (int) $post_set['server_id'] : false;
        $description    = (!empty($post_set['description'])) ? $post_set['description'] : false;
        $email          = (!empty($post_set['email'])) ? $post_set['email'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('server_id', 'Server ID', 'required|is_natural_no_zero');
        // $this->form_validation->set_rules( 'email', 'Notification Point(s) email data', 'required' );

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'server'        => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'server'        => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## check the server exists
        $server_exists = false;
        $server_exists = $this->distribution_service->get_distribution_servers($account_id, ["server_id" => $server_id]);

        if (!$server_exists) {
            $message = [
                'status'        => false,
                'message'       => "Can't find the provided Server",
                'server'        => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $update_server = $this->distribution_service->update_server($account_id, $server_id, $postdata);

        if (!empty($update_server)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'server'        => $update_server
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'server'        => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   Archive the server and delete notification point(s)
    */
    public function delete_server_post()
    {
        $post_set       = $this->post();
        $postdata       = (!empty($post_set)) ? $post_set : false;
        $account_id     = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $server_id      = (!empty($post_set['server_id'])) ? (int) $post_set['server_id'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('server_id', 'Server ID', 'required|is_natural_no_zero');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'server'        => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'server'        => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## check the server exists
        $server_exists = false;
        $server_exists = $this->distribution_service->get_distribution_servers($account_id, ["server_id" => $server_id]);

        if (!$server_exists) {
            $message = [
                'status'        => false,
                'message'       => "Can't find the provided Server",
                'server'        => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $deleted_server = $this->distribution_service->delete_server($account_id, $server_id);

        if ($deleted_server != false) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'server'        => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'server'        => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
