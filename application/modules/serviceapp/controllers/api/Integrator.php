<?php

namespace Application\Service\Controllers\Api;

use App\Libraries\REST_Controller;

defined('BASEPATH') || exit('No direct script access allowed');

class Integrator extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library("Ssid_common");
        $this->load->library("form_validation");
        $this->load->library("email");
        $this->load->model("Integrator_model", "integrator_service");
    }


    /**
    *   Create new Integrator
    */
    public function create_post()
    {
        $post_set               = $this->post();

        $account_id             = (!empty($post_set['account_id'])) ? $post_set['account_id'] : false;
        $integrator_name        = (!empty($post_set['integrator_name'])) ? $post_set['integrator_name'] : false;
        $integrator_data        = $post_set;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('integrator_name', 'Integrator Name', 'required');
        $this->form_validation->set_rules('integrator_details', 'Integrator Details', 'required');


        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Validation errors: ' . $validation_errors,
                'new_integrator'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => 'Invalid main Account ID',
                'new_integrator'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_integrator = $this->integrator_service->create_integrator($account_id, $integrator_name, $integrator_data);

        if (!empty($new_integrator)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'new_integrator'    => $new_integrator
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'new_integrator'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get Integrator(s)
    */
    public function integrator_get()
    {
        $get_set        = $this->get();

        $account_id     = (!empty($get_set['account_id'])) ? $get_set['account_id'] : false;
        $integrator_id  = (!empty($get_set['integrator_id'])) ? $get_set['integrator_id'] : false;
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
                'integrator'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'integrator'    => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $integrator = $this->integrator_service->get_integrator($account_id, $integrator_id, $where, $limit, $offset);

        if (!empty($integrator)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'integrator'    => $integrator
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'integrator'    => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   System Integrator Lookup
    */
    public function lookup_get()
    {
        $get_data = $this->get();

        $account_id     = (!empty($get_data['account_id'])) ? (int) $get_data['account_id'] : false ;
        $limit          = (!empty($get_data['limit']) && ($get_data['limit'] > 0)) ? (int) $get_data['limit'] : false ;
        $offset         = (!empty($get_data['offset'])) ? (int) $get_data['offset'] : '' ;
        $where          = (!empty($get_data['where'])) ? $get_data['where'] : '' ;
        $order_by       = (!empty($get_data['order_by'])) ? $get_data['order_by'] : '' ;
        $search_term    = (!empty($get_data['search_term'])) ? trim(urldecode($get_data['search_term'])) : '' ;


        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid main Account ID.',
                'integrator'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $integrator_lookup = $this->integrator_service->integrator_lookup($account_id, $search_term, $where, $order_by, $limit, $offset);

        if (!empty($integrator_lookup)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'integrator'    => $integrator_lookup
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'integrator'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Update Integrator
    */
    public function update_post()
    {
        $post_set           = $this->post();

        $account_id         = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $integrator_id      = (!empty($post_set['integrator_id'])) ? (int) $post_set['integrator_id'] : false;
        $integrator_data    = (!empty($post_set['integrator_data'])) ? $post_set['integrator_data'] : false;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('integrator_id', 'Integrator ID', 'required');
        $this->form_validation->set_rules('integrator_data', 'Integrator Data', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'u_integrator'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid main Account ID',
                'u_integrator'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $integrator_exists = $this->integrator_service->get_integrator($account_id, $integrator_id);

        if (!$integrator_exists) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid Integrator ID',
                'u_integrator'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $u_integrator = $this->integrator_service->update_integrator($account_id, $integrator_id, $integrator_data);

        if (!empty($u_integrator)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'u_integrator'  => $u_integrator
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'u_integrator'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Delete Integrator
    */
    public function delete_post()
    {
        $post_set       = $this->post();

        $account_id     = (!empty($post_set['account_id'])) ? $post_set['account_id'] : false;
        $integrator_id  = (!empty($post_set['integrator_id'])) ? $post_set['integrator_id'] : false;

        $this->form_validation->set_rules('integrator_id', 'Integrator ID', 'required');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'd_integrator'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid main Account ID',
                'd_integrator'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $integrator_exists = $this->integrator_service->get_integrator($account_id, $integrator_id);

        if (!$integrator_exists) {
            $message = [
                'status'        => false,
                'message'       => "Incorrect Integrator ID",
                'd_integrator'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $d_integrator = $this->integrator_service->delete_integrator($account_id, $integrator_id);

        if (!empty($d_integrator)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'd_integrator'  => $d_integrator
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'd_integrator'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Update System Integrator Address
    */
    public function update_address_post()
    {
        $post_set           = $this->post();

        $account_id         = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $address_id         = (!empty($post_set['address_id'])) ? (int) $post_set['address_id'] : false;
        $address_data   = (!empty($post_set['address_data'])) ? $post_set['address_data'] : false;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('address_id', 'Address ID', 'required');
        $this->form_validation->set_rules('address_data', 'Address Data', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'u_address'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid main Account ID',
                'u_address'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $address_exists = $this->integrator_service->get_integrator_addresses($account_id, false, $address_id);

        if (!$address_exists) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid Address ID',
                'u_address'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $u_address = $this->integrator_service->update_address($account_id, $address_id, $address_data);

        if (!empty($u_address)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'u_address'     => $u_address
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'u_address'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get Territory(ies)
    */
    public function territories_get()
    {
        $get_set        = $this->get();

        $account_id     = (!empty($get_set['account_id'])) ? $get_set['account_id'] : false;
        $territory_id   = (!empty($get_set['territory_id'])) ? $get_set['territory_id'] : false;
        $where          = (!empty($get_set['where'])) ? $get_set['where'] : false;
        $unorganized    = (!empty($get_set['unorganized'])) ? $get_set['unorganized'] : false;
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
                'territories'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'territories'   => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $territories = $this->integrator_service->get_territories($account_id, $territory_id, $where, $unorganized, $limit, $offset);

        if (!empty($territories)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'territories'   => $territories
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'territories'   => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Add Territory
    */
    public function add_territory_post()
    {
        $post_set               = $this->post();
        $account_id             = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $integrator_id          = (!empty($post_set['integrator_id'])) ? $post_set['integrator_id'] : false;
        $territories            = (!empty($post_set['territories'])) ? $post_set['territories'] : false;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('integrator_id', 'Integrator ID(s)', 'required');
        $this->form_validation->set_rules('territories', 'Territory(ies)', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Validation errors: ' . $validation_errors,
                'new_territory'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => 'Invalid main Account ID',
                'new_territory'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_territory = $this->integrator_service->add_territory($account_id, $integrator_id, $territories);

        if (!empty($new_territory)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'new_territory'     => $new_territory
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'new_territory'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   Delete territory from the integrator
    */
    public function delete_territory_post()
    {
        $post_set           = $this->post();

        $account_id         = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $territory_id       = (!empty($post_set['territory_id'])) ? (int) $post_set['territory_id'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('territory_id', 'Territory ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Validation errors: ' . $validation_errors,
                'd_territory'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'd_territory'       => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $d_territory     = $this->integrator_service->delete_territory($account_id, $territory_id);

        if (!empty($d_territory)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'd_territory'       => $d_territory
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'd_territory'       => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Add System
    */
    public function add_system_post()
    {
        $post_set                   = $this->post();
        $account_id                 = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $integrator_id              = (!empty($post_set['integrator_id'])) ? $post_set['integrator_id'] : false;
        $integrator_systems         = (!empty($post_set['integrator_systems'])) ? $post_set['integrator_systems'] : false;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('integrator_id', 'Integrator ID', 'required');
        $this->form_validation->set_rules('integrator_systems', 'System(s)', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Validation errors: ' . $validation_errors,
                'new_system'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => 'Invalid main Account ID',
                'new_system'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_system = $this->integrator_service->add_system($account_id, $integrator_id, $integrator_systems);

        if (!empty($new_system)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'new_system'        => $new_system
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'new_system'        => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   Delete system from the integrator
    */
    public function delete_system_post()
    {
        $post_set           = $this->post();

        $account_id         = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $system_id          = (!empty($post_set['system_id'])) ? (int) $post_set['system_id'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('system_id', 'System ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Validation errors: ' . $validation_errors,
                'd_system'          => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'd_system'          => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $d_system    = $this->integrator_service->delete_system($account_id, $system_id);

        if (!empty($d_system)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'd_system'          => $d_system
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'd_system'          => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Get System(s)
    */
    public function systems_get()
    {
        $get_set        = $this->get();

        $account_id     = (!empty($get_set['account_id'])) ? $get_set['account_id'] : false;
        $system_id      = (!empty($get_set['system_id'])) ? $get_set['system_id'] : false;
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

        $systems = $this->integrator_service->get_systems($account_id, $system_id, $where, $limit, $offset);

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
                'systems'       => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Disable Integrator
    */
    public function disable_post()
    {
        $post_data      = $this->post();
        $account_id     = (!empty($post_data['account_id'])) ? (int) $post_data['account_id'] : false ;
        $integrator_id  = (!empty($post_data['integrator_id'])) ? (int) $post_data['integrator_id'] : false ;
        $disable_date   = (!empty($post_data['disable_date'])) ? $post_data['disable_date'] : false ;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('integrator_id', 'Integrator ID', 'required');
        $this->form_validation->set_rules('disable_date', 'Disable Date', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Validation errors: ' . $validation_errors,
                'integrator'        => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => 'Invalid main Account ID',
                'integrator'        => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $d_integrator = $this->integrator_service->disable_integrator($account_id, $integrator_id, $disable_date);

        if (!empty($d_integrator)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'integrator'        => $d_integrator
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'integrator'        => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


/**
    *   Get Integrator Territory(ies)
    */
    public function integrator_territories_get()
    {
        $postdata       = $this->get();

        $account_id     = (!empty($postdata['account_id'])) ? $postdata['account_id'] : false;
        $integrator_id  = (!empty($postdata['integrator_id'])) ? $postdata['integrator_id'] : false;
        $where          = (!empty($postdata['where'])) ? $postdata['where'] : false;
        $limit          = (!empty($postdata['limit'])) ? $postdata['limit'] : false;
        $offset         = (!empty($postdata['offset'])) ? $postdata['offset'] : false;

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
                'integrator_territories' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'integrator_territories' => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $integrator_territories = $this->integrator_service->get_integrator_territories($account_id, $integrator_id, $where, $limit, $offset);

        if (!empty($integrator_territories)) {
            $message = [
                'status'                => true,
                'message'               => $this->session->flashdata('message'),
                'integrator_territories' => $integrator_territories
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'integrator_territories' => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
