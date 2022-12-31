<?php

namespace Application\Modules\Service\Controllers\Api;

use App\Adapter\RESTController;
use Application\Modules\Service\Models\ProviderModel;

class Provider extends RESTController
{
	/**
	 * @var \Application\Modules\Service\Models\ProviderModel
	 */
	private $provider_service;

	public function __construct()
    {
        parent::__construct();
        $this->load->library("Ssid_common");
        $this->load->library("form_validation");
        $this->load->library("email");
        $this->provider_service = new ProviderModel();
    }

    /**
    *   Get Provider(s)
    */
    public function provider_get()
    {
        $get_set        = $this->get();

        $account_id     = (!empty($get_set['account_id'])) ? $get_set['account_id'] : false;
        $provider_id    = (!empty($get_set['provider_id'])) ? $get_set['provider_id'] : false;
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
                'status'            => false,
                'message'           => 'Validation errors: ' . $validation_errors,
                'content_provider'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'content_provider'  => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $content_provider = $this->provider_service->get_provider($account_id, $provider_id, $where, $unorganized, $limit, $offset);

        if (!empty($content_provider)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'content_provider'  => $content_provider
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'content_provider'  => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get content Provider Category(ies)
    */
    public function provider_categories_get()
    {
        $get_set        = $this->get();
        $account_id     = (!empty($get_set['account_id'])) ? $get_set['account_id'] : false;
        $category_id    = (!empty($get_set['category_id'])) ? $get_set['category_id'] : false;
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
                'status'                => false,
                'message'               => 'Validation errors: ' . $validation_errors,
                'provider_categories'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'provider_categories'   => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $provider_categories = $this->provider_service->get_provider_category($account_id, $category_id, $where, $limit, $offset);

        if (!empty($provider_categories)) {
            $message = [
                'status'                => true,
                'message'               => $this->session->flashdata('message'),
                'provider_categories'   => $provider_categories
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'provider_categories'   => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Create new Provider
    */
    public function create_post()
    {
        $post_set       = $this->post();

        $account_id     = (!empty($post_set['account_id'])) ? $post_set['account_id'] : false;
        $provider_data  = (!empty($post_set['provider_data'])) ? $post_set['provider_data'] : false;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('provider_data', 'provider Data', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'new_provider'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid main Account ID',
                'new_provider'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_provider = $this->provider_service->create_provider($account_id, $provider_data);

        if (!empty($new_provider)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'new_provider'  => $new_provider
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'new_provider'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Update Provider
    */
    public function update_post()
    {
        $post_set       = $this->post();

        $account_id     = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $provider_id    = (!empty($post_set['provider_id'])) ? (int) $post_set['provider_id'] : false;
        $provider_data  = (!empty($post_set['provider_data'])) ? $post_set['provider_data'] : false;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('provider_id', 'Provider ID', 'required');
        $this->form_validation->set_rules('provider_data', 'provider Data', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'u_provider'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid main Account ID',
                'u_provider'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $provider_exists = $this->provider_service->get_provider($account_id, $provider_id);

        if (!$provider_exists) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid Provider ID',
                'u_provider'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $u_provider = $this->provider_service->update_provider($account_id, $provider_id, $provider_data);

        if (!empty($u_provider)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'u_provider'    => $u_provider
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'u_provider'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Provider Lookup
    */
    public function lookup_get()
    {
        $get_data = $this->get();

        $account_id     = (!empty($get_data['account_id'])) ? (int) $get_data['account_id'] : '' ;
        $limit          = (!empty($get_data['limit']) && ($get_data['limit'] > 0)) ? (int) $get_data['limit'] : '' ;
        $offset         = (!empty($get_data['offset'])) ? (int) $get_data['offset'] : '' ;
        $where          = (!empty($get_data['where'])) ? $get_data['where'] : '' ;
        $order_by       = (!empty($get_data['order_by'])) ? $get_data['order_by'] : '' ;
        $search_term    = (!empty($get_data['search_term'])) ? trim(urldecode($get_data['search_term'])) : '' ;


        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'    => false,
                'message'   => 'Invalid main Account ID.',
                'provider'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $provider_lookup = $this->provider_service->provider_lookup($account_id, $search_term, $where, $order_by, $limit, $offset);

        if (!empty($provider_lookup)) {
            $message = [
                'status'    => true,
                'message'   => $this->session->flashdata('message'),
                'provider'  => $provider_lookup
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'    => false,
                'message'   => $this->session->flashdata('message'),
                'provider'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Delete Provider
    */
    public function delete_post()
    {
        $post_set       = $this->post();

        $account_id     = (!empty($post_set['account_id'])) ? $post_set['account_id'] : false;
        $provider_id    = (!empty($post_set['provider_id'])) ? $post_set['provider_id'] : false;

        $this->form_validation->set_rules('provider_id', 'Provider ID', 'required');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'd_provider'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid main Account ID',
                'd_provider'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $provider_exists = $this->provider_service->get_provider($account_id, $provider_id);

        if (!$provider_exists) {
            $message = [
                'status'        => false,
                'message'       => "Incorrect Provider ID",
                'd_provider'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $d_provider = $this->provider_service->delete_provider($account_id, $provider_id);

        if (!empty($d_provider)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'd_provider'    => $d_provider
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'd_provider'    => null
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

        $d_territory     = $this->provider_service->delete_territory($account_id, $territory_id);

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

        $territories = $this->provider_service->get_territories($account_id, $territory_id, $where, $unorganized, $limit, $offset);

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
        $integrator_id          = (!empty($post_set['provider_id'])) ? $post_set['provider_id'] : false;
        $territories            = (!empty($post_set['territories'])) ? $post_set['territories'] : false;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('provider_id', 'Provider ID(s)', 'required');
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

        $new_territory = $this->provider_service->add_territory($account_id, $integrator_id, $territories);

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


    /**
    *   Get Packet Identifiers(PID) - not assigned to any provider
    */
    public function packet_identifiers_get()
    {
        $get_set        = $this->get();

        $account_id     = (!empty($get_set['account_id'])) ? $get_set['account_id'] : false;
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
                'identifiers'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'identifiers'   => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $identifiers = $this->provider_service->get_packet_identifiers($account_id, $where, $limit, $offset);

        if (!empty($identifiers)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'identifiers'   => $identifiers
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'identifiers'   => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get Provider Packet Identifiers(PID)
    */
    public function provider_packet_identifiers_get()
    {
        $get_set        = $this->get();

        $account_id     = (!empty($get_set['account_id'])) ? $get_set['account_id'] : false;
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
                'identifiers'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'identifiers'   => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $identifiers = $this->provider_service->get_provider_packet_identifiers($account_id, $where, $limit, $offset);

        if (!empty($identifiers)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'identifiers'   => $identifiers
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'identifiers'   => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get Definitions(SD/HD)
    */
    public function definition_get()
    {
        $get_set        = $this->get();

        $account_id     = (!empty($get_set['account_id'])) ? $get_set['account_id'] : false;
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
                'definition'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'definition'    => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $definition = $this->provider_service->get_definition($account_id, $where, $limit, $offset);

        if (!empty($definition)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'definition'    => $definition
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'definition'    => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get Codec Type (audio, video, subtitle)
    */
    public function codec_type_get()
    {
        $get_set        = $this->get();

        $account_id     = (!empty($get_set['account_id'])) ? $get_set['account_id'] : false;
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
                'type'          => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'type'          => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $type = $this->provider_service->get_codec_type($account_id, $where, $limit, $offset);

        if (!empty($type)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'type'          => $type
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'type'          => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }




    /**
    *   Get Codec Name (H264, mp2... )
    */
    public function codec_name_get()
    {
        $get_set        = $this->get();

        $account_id     = (!empty($get_set['account_id'])) ? $get_set['account_id'] : false;
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
                'name'          => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'name'          => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $name = $this->provider_service->get_codec_name($account_id, $where, $limit, $offset);

        if (!empty($name)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'name'          => $name
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'name'          => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



   /**
    *   Add Packet Identifier to the Provider
    */
    public function add_identifier_to_provider_post()
    {
        $post_set               = $this->post();
        $account_id             = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $provider_id            = (!empty($post_set['provider_id'])) ? (int) $post_set['provider_id'] : false;
        $dataset                = (!empty($post_set['dataset'])) ? $post_set['dataset'] : false;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('provider_id', 'Provider ID(s)', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Validation errors: ' . $validation_errors,
                'added_identifier'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => 'Invalid main Account ID',
                'added_identifier'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $provider_exists = $this->provider_service->get_provider($account_id, $provider_id);

        if (!$provider_exists) {
            $message = [
                'status'            => false,
                'message'           => "Incorrect Provider ID",
                'added_identifier'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $added_identifier = $this->provider_service->add_identifier_to_provider($account_id, $provider_id, $dataset);

        if (!empty($added_identifier)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'added_identifier'  => $added_identifier
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'added_identifier'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }





   /**
    *   Update Provider Packet Identifier data
    */
    public function update_provider_pid_post()
    {
        $post_set               = $this->post();
        $account_id             = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $identifier_id          = (!empty($post_set['identifier_id'])) ? (int) $post_set['identifier_id'] : false; ## Provider Packet Identifier
        $dataset                = (!empty($post_set['dataset'])) ? $post_set['dataset'] : false;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('identifier_id', 'Identifier ID(s)', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'                => false,
                'message'               => 'Validation errors: ' . $validation_errors,
                'updated_identifier'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'message'               => 'Invalid main Account ID',
                'updated_identifier'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $identifier_exists = $this->provider_service->get_provider_packet_identifiers($account_id, ["identifier_id" => $identifier_id]);

        if (!$identifier_exists) {
            $message = [
                'status'                => false,
                'message'               => "Incorrect Identifier ID",
                'updated_identifier'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $updated_identifier = $this->provider_service->update_provider_pid($account_id, $identifier_id, $dataset);

        if (!empty($updated_identifier)) {
            $message = [
                'status'                => true,
                'message'               => $this->session->flashdata('message'),
                'updated_identifier'    => $updated_identifier
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'updated_identifier'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }






   /**
    *   Delete Provider Packet Identifier data
    */
    public function delete_provider_pid_post()
    {
        $post_set               = $this->post();
        $account_id             = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $identifier_id          = (!empty($post_set['identifier_id'])) ? (int) $post_set['identifier_id'] : false; ## Provider Packet Identifier

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('identifier_id', 'Identifier ID(s)', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'                => false,
                'message'               => 'Validation errors: ' . $validation_errors,
                'deleted_identifier'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'message'               => 'Invalid main Account ID',
                'deleted_identifier'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $identifier_exists = $this->provider_service->get_provider_packet_identifiers($account_id, ["identifier_id" => $identifier_id]);

        if (!$identifier_exists) {
            $message = [
                'status'                => false,
                'message'               => "Incorrect Identifier ID",
                'deleted_identifier'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $deleted_identifier = $this->provider_service->delete_provider_pid($account_id, $identifier_id);

        if (!empty($deleted_identifier)) {
            $message = [
                'status'                => true,
                'message'               => $this->session->flashdata('message'),
                'deleted_identifier'    => $deleted_identifier
            ];
            $this->response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'deleted_identifier'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


   /**
    *   Add Price Plan to the Provider
    */
    public function add_provider_price_plan_post()
    {
        $post_set               = $this->post();
        $account_id             = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $provider_id            = (!empty($post_set['provider_id'])) ? (int) $post_set['provider_id'] : false;
        $price_plan_details     = (!empty($post_set['price_plan_details'])) ? $post_set['price_plan_details'] : false;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('provider_id', 'Provider ID(s)', 'required');
        $this->form_validation->set_rules('price_plan_details', 'Price Plan Details', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'                => false,
                'message'               => 'Validation errors: ' . $validation_errors,
                'provider_price_plan'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'message'               => 'Invalid main Account ID',
                'provider_price_plan'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $provider_exists = $this->provider_service->get_provider($account_id, $provider_id);

        if (!$provider_exists) {
            $message = [
                'status'                => false,
                'message'               => "Incorrect Provider ID",
                'provider_price_plan'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $provider_price_plan = $this->provider_service->add_provider_price_plan($account_id, $provider_id, $price_plan_details);

        if (!empty($provider_price_plan)) {
            $message = [
                'status'                => true,
                'message'               => $this->session->flashdata('message'),
                'provider_price_plan'   => $provider_price_plan
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'provider_price_plan'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Get Provider Price Plans
    */
    public function provider_price_plan_get()
    {
        $get_set        = $this->get();

        $account_id     = (!empty($get_set['account_id'])) ? $get_set['account_id'] : false;
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
                'price_plan'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'price_plan'            => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $price_plan = $this->provider_service->get_provider_price_plan($account_id, $where, $limit, $offset);

        if (!empty($price_plan)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'price_plan'    => $price_plan
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'price_plan'    => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Delete Provider Price Plan
    */
    public function delete_provider_price_plan_post()
    {
        $post_set               = $this->post();
        $account_id             = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $provider_plan_id       = (!empty($post_set['provider_plan_id'])) ? (int) $post_set['provider_plan_id'] : false; ## Provider Packet Identifier

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('provider_plan_id', 'Provider Plan ID(s)', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'                => false,
                'message'               => 'Validation errors: ' . $validation_errors,
                'deleted_price_plan'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'message'               => 'Invalid main Account ID',
                'deleted_price_plan'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $price_plan_exists = $this->provider_service->get_provider_price_plan($account_id, ["provider_plan_id" => $provider_plan_id]);

        if (!$price_plan_exists) {
            $message = [
                'status'                => false,
                'message'               => "Incorrect Price Plan ID",
                'deleted_price_plan'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $deleted_price_plan = $this->provider_service->delete_provider_price_plan($account_id, $provider_plan_id);

        if (!empty($deleted_price_plan)) {
            $message = [
                'status'                => true,
                'message'               => $this->session->flashdata('message'),
                'deleted_price_plan'    => $deleted_price_plan
            ];
            $this->response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'deleted_price_plan'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get Price Plans
    */
    public function price_plan_get()
    {
        $get_set        = $this->get();

        $account_id     = (!empty($get_set['account_id'])) ? $get_set['account_id'] : false;
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
                'price_plan'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'price_plan'            => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $price_plan = $this->provider_service->get_price_plan($account_id, $where, $limit, $offset);

        if (!empty($price_plan)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'price_plan'    => $price_plan
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'price_plan'    => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
