<?php

namespace Application\Service\Controllers\Api;

use App\Libraries\REST_Controller;

defined('BASEPATH') || exit('No direct script access allowed');

class Systems extends REST_Controller
{
    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('Systems_model', 'systems_service');
        $this->load->model('Account_model', 'account_service');
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->lang->load('auth');
    }


    /**
    *   Get system(s) list
    **/
    public function systems_get()
    {
        $get_data = $this->get();

        $account_id         = (!empty($get_data['account_id'])) ? (int) $get_data['account_id'] : false ;
        $system_type_id     = (!empty($get_data['system_type_id'])) ? (int) $get_data['system_type_id'] : false ;
        $unorganized        = (!empty($get_data['unorganized'])) ? $get_data['unorganized'] : false ;
        $where              = (!empty($get_data['where'])) ? $get_data['where'] : false ;

        $expected_data = [
            'account_id' => $account_id
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '' ;
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . trim($validation_errors),
                'systems'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'systems'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $systems = $this->systems_service->get_system($account_id, $system_type_id, $unorganized, $where);

        if (!empty($systems)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'systems'       => $systems
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'systems'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Search through list of Systems
    */
    public function lookup_get()
    {
        $get_data = $this->get();

        $account_id     = (!empty($get_data['account_id'])) ? (int) $get_data['account_id'] : false ;
        $search_term    = (!empty($get_data['search_term'])) ? trim(urldecode($get_data['search_term'])) : false;
        $where          = (!empty($get_data['where'])) ? $get_data['where'] : [];
        $order_by       = (!empty($get_data['order_by'])) ? $get_data['order_by'] : false ;
        $limit          = (!empty($get_data['limit'])) ? $get_data['limit'] : false;
        $offset         = (!empty($get_data['offset'])) ? $get_data['offset'] : false;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'    => false,
                'message'   => 'Invalid main Account ID.',
                'systems'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $system_lookup = $this->systems_service->systems_lookup($account_id, $search_term, $where, $order_by, $limit, $offset);

        if (!empty($system_lookup)) {
            $message = [
                'status'    => true,
                'message'   => $this->session->flashdata('message'),
                'systems'   => $system_lookup
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'    => false,
                'message'   => $this->session->flashdata('message'),
                'systems'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Get DRM(s) list - Digital Rights Management
    **/
    public function drm_types_get()
    {
        $get_data = $this->get();

        $account_id         = (!empty($get_data['account_id'])) ? (int) $get_data['account_id'] : false ;
        $drm_type_id        = (!empty($get_data['drm_type_id'])) ? (int) $get_data['drm_type_id'] : false ;
        $unorganized        = (!empty($get_data['unorganized'])) ? $get_data['unorganized'] : false ;

        $expected_data = [
            'account_id' => $account_id
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '' ;
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . trim($validation_errors),
                'drm_types'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'drm_types'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $drm_types = $this->systems_service->get_drm_types($account_id, $drm_type_id, $unorganized);

        if (!empty($drm_types)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'drm_types'     => $drm_types
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'drm_types'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get Delivery mechansm list
    **/
    public function delivery_mechanism_get()
    {
        $get_data = $this->get();

        $account_id         = (!empty($get_data['account_id'])) ? (int) $get_data['account_id'] : false ;
        $mechanism_id       = (!empty($get_data['mechanism_id'])) ? (int) $get_data['mechanism_id'] : false ;
        $unorganized        = (!empty($get_data['unorganized'])) ? $get_data['unorganized'] : false ;

        $expected_data = [
            'account_id' => $account_id
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '' ;
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Validation errors: ' . trim($validation_errors),
                'mechanism_types'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'mechanism_types'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $mechanism_types = $this->systems_service->get_mechanism_types($account_id, $mechanism_id, $unorganized);

        if (!empty($mechanism_types)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'mechanism_types'   => $mechanism_types
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'mechanism_types'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Create new System
    */
    public function create_post()
    {
        $system_data        = $this->post();
        $account_id     = (!empty($this->post('account_id'))) ? (int) $this->post('account_id') : false ;
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('name', 'System Name', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'new_system'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'    => false,
                'message'   => 'Invalid main Account ID',
                'new_system'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_system = $this->systems_service->create_system($account_id, $system_data);

        if (!empty($new_system)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'new_system'    => $new_system
            ];
            $this->response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'new_system'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Update system profile
    */
    public function update_post()
    {
        $system_data    = $this->post();
        $system_type_id         = (!empty($this->post('system_type_id'))) ? (int) $this->post('system_type_id') : '' ;
        $account_id     = (!empty($this->post('account_id'))) ? (int) $this->post('account_id') : '' ;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('system_type_id', 'System ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Invalid data: ' . $validation_errors,
                'updated_system'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if ($system_type_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => 'Invalid main Account ID',
                'updated_system'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $system = $this->systems_service->get_system($account_id, $system_type_id);
        if (!$system) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'updated_system'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $updated_system = $this->systems_service->update_system($account_id, $system_type_id, $system_data);
        if (!empty($updated_system)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'updated_system'    => $updated_system
            ];
            $this->response($message, REST_Controller::HTTP_OK); // Resource Updated
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'updated_system'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Delete System profile
    */
    public function delete_post()
    {
        $post_data      = $this->post();
        $account_id     = (!empty($post_data['account_id'])) ? (int) $post_data['account_id'] : false ;
        $system_type_id         = (!empty($post_data['system_type_id'])) ? (int) $post_data['system_type_id'] : false ;

        if ($system_type_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'    => false,
                'message'   => 'Invalid main Account ID',
                'd_system'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        // check if the system exists
        $system_exists = $this->systems_service->get_system($account_id, $system_type_id);
        if (!$system_exists) {
            $message = [
                'status'    => false,
                'message'   => 'Invalid System ID',
                'd_system'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }


        $deleted_system     = $this->systems_service->delete_system($account_id, $system_type_id);
        if (!empty($deleted_system)) {
            $message = [
                'status'    => true,
                'message'   => $this->session->flashdata('message'),
                'd_system'  => true
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'    => false,
                'message'   => $this->session->flashdata('message'),
                'd_system'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get Provider(s)
    */
    public function providers_get()
    {
        $get_set        = $this->get();

        $account_id     = (!empty($get_set['account_id'])) ? $get_set['account_id'] : false;
        $provider_id    = (!empty($get_set['provider_id'])) ? $get_set['provider_id'] : false;
        $where          = (!empty($get_set['where'])) ? $get_set['where'] : false;
        $limit          = (!empty($get_set['limit'])) ? $get_set['limit'] : false;
        $offset         = (!empty($get_set['offset'])) ? $get_set['offset'] : false;

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
                'message'       => 'Validation errors: ' . $validation_errors,
                'providers'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'providers'     => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $providers = $this->systems_service->get_providers($account_id, $provider_id, $where, $limit, $offset);

        if (!empty($providers)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'providers'     => $providers
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'providers'     => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Delete Provider from the System
    */
    public function delete_provider_post()
    {
        $post_data              = $this->post();
        $account_id             = (!empty($post_data['account_id'])) ? (int) $post_data['account_id'] : false ;
        $system_providers_id    = (!empty($post_data['system_providers_id'])) ? (int) $post_data['system_providers_id'] : false ;
        $where                  = (!empty($post_data['where'])) ? $post_data['where'] : false ;


        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '' ;
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . trim($validation_errors),
                'systems'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'    => false,
                'message'   => 'Invalid main Account ID',
                'system'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $deleted_provider   = $this->systems_service->delete_provider($account_id, $system_providers_id, $where);
        if ($deleted_provider) {
            $message = [
                'status'    => true,
                'message'   => $this->session->flashdata('message'),
                'system'    => true
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'    => false,
                'message'   => $this->session->flashdata('message'),
                'system'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    *   Add Provider to the System
    */
    public function add_provider_post()
    {
        $post_set           = $this->post();
        $account_id         = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $system_type_id     = (!empty($post_set['system_type_id'])) ? $post_set['system_type_id'] : false;                  ## could be an array
        $approval_date      = (!empty($post_set['approval_date'])) ? $post_set['approval_date'] : false;                    ## always single date
        $providers          = (!empty($post_set['providers'])) ? $post_set['providers'] : false;                        ## always as an array - even if only one item

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('system_type_id', 'System ID(s)', 'required');
        $this->form_validation->set_rules('approval_date', 'Approval Date', 'required');
        $this->form_validation->set_rules('providers', 'Provider(s)', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Validation errors: ' . $validation_errors,
                'new_provider'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => 'Invalid main Account ID',
                'new_provider'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_provider = $this->systems_service->add_provider($account_id, $system_type_id, $approval_date, $providers);

        if (!empty($new_provider)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'new_provider'  => $new_provider
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'new_provider'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
