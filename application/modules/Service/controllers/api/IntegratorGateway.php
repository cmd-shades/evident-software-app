<?php

namespace Application\Modules\Service\Controllers\Api;

use App\Libraries\REST_Controller;

class IntegratorGateway extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('IntegratorGateway_model', 'IntegratorGateway_services');
    }

    public function check_integrator_token($received_token = false)
    {
        if (isset($received_token) && !empty($received_token) && $received_token === INTEGRATOR_TOKEN) {
            return true;
        } else {
            $this->form_validation->set_message('check_integrator_token', 'The {field} is invalid');
            return false;
        }
    }


    public function check_integrator_fixed_id($integrator_id = false)
    {
        if (isset($integrator_id) && !empty($integrator_id) && ($integrator_id === INTEGRATOR_FIXED_ID)) {
            return true;
        } else {
            $this->form_validation->set_message('check_integrator_fixed_id', 'The {field} is invalid');
            return false;
        }
    }


    public function log_request($request_data)
    {
        if (LOG_INTEGRATOR_REQUEST) {
            $log_data = [
                "request_headers"           => (!empty($request_data['headers'])) ? json_encode($request_data['headers']) : null ,
                "request_get_parameters"    => (!empty($request_data['get_parameters'])) ? json_encode($request_data['get_parameters']) : null ,
                "cacti_response"            => (!empty($request_data['response'])) ? json_encode($request_data['response']) : null ,
            ];

            $this->db->insert("integrator_api_usage_log", $log_data);
        }
    }


    /**
    *   Test service if it is running
    */
    public function isRunning_get()
    {
        $headers        = [];
        $headers        = $this->input->request_headers();
        $api_key        = (isset($headers['api_key']) && !empty($headers['api_key']) && ($headers['api_key'])) ? $headers['api_key'] : null ;

        $get_input      = $this->input->get();
        $integrator_id  = (!empty($get_input['integrator_id'])) ? (int) ($get_input['integrator_id']) : null ;

        $expected_data  = [
            'api_key'           => $api_key ,
            'integrator_id'     => $integrator_id
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('api_key', 'API key', 'required|callback_check_integrator_token');
        $this->form_validation->set_rules('integrator_id', 'Integrator ID', 'required|is_natural_no_zero|callback_check_integrator_fixed_id');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_BAD_REQUEST,
                'message'       => 'Validation errors: ' . $validation_errors,
                'servers'       => null
            ];

            ## log functionality
            $log_data = [];
            $log_data = [
                "api_end_point"     => 'isRunning_get',
                "headers"           => (!empty($headers)) ? $headers : '' ,
                "get_parameters"    => (!empty($get_input)) ? $get_input : '' ,
                "response"          => json_encode($message) ,
            ];
            $this->log_request($log_data);

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $message = [
            'status'        => true,
            'http_code'     => REST_Controller::HTTP_OK,
            'message'       => "Services are available",
            'running'       => true,
            // 'api_key'        => $api_key
        ];

        ## log functionality
        $log_data = [];
        $log_data = [
            "api_end_point"     => 'isRunning_get',
            "headers"           => (!empty($headers)) ? $headers : '' ,
            "get_parameters"    => (!empty($get_input)) ? $get_input : '' ,
            "response"          => json_encode($message) ,
        ];
        $this->log_request($log_data);

        $this->response($message, REST_Controller::HTTP_OK);
    }


    /**
    *   Active Sites request
    *   Required:
    *    - integrator token
    *    - integrator ID (fixed - 32)
    */
    public function activeSites_get()
    {
        ## headers
        $headers        = [];
        $headers        = $this->input->request_headers();
        $api_key        = (isset($headers['api_key']) && !empty($headers['api_key']) && ($headers['api_key'])) ? $headers['api_key'] : null ;

        ## input values and validation
        $get_input      = $this->input->get();
        $integrator_id  =  (!empty($get_input['integrator_id'])) ? (int) $get_input['integrator_id'] : null ;
        $site_ids       =  (!empty($get_input['site_ids']) && validate_array_of_integers(json_decode($get_input['site_ids']))) ? json_decode($get_input['site_ids']) : null ;
        $limit          = (!empty($get_input['limit'])) ? (int) $get_input['limit'] : null ;

        ## validation for required data
        $expected_data  = [
            'api_key'           => $api_key ,
            'integrator_id'     => $integrator_id
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('api_key', 'API key', 'required|callback_check_integrator_token');
        $this->form_validation->set_rules('integrator_id', 'Integrator ID', 'required|is_natural_no_zero|callback_check_integrator_fixed_id');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_BAD_REQUEST,
                'message'       => 'Validation errors: ' . $validation_errors,
                'sites'         => null
            ];

            ## log functionality
            $log_data = [];
            $log_data = [
                "api_end_point"     => 'activeSites_get',
                "headers"           => (!empty($headers)) ? $headers : '' ,
                "get_parameters"    => (!empty($get_input)) ? $get_input : '' ,
                "response"          => json_encode($message) ,
            ];
            $this->log_request($log_data);

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $active_sites = $this->IntegratorGateway_services->get_active_sites($integrator_id, $site_ids, $limit);

        if (($active_sites['status'] != false) && (!empty($active_sites['data']))) {
            $message = [
                'status'        => true,
                'http_code'     => REST_Controller::HTTP_OK,
                'message'       => !empty($active_sites['message']) ? ($active_sites['message']) : "Sites data found",
                'sites'         => ($active_sites['data'])
            ];

            ## log functionality
            $log_data = [];
            $log_data = [
                "api_end_point"     => 'activeSites_get',
                "headers"           => (!empty($headers)) ? $headers : '' ,
                "get_parameters"    => (!empty($get_input)) ? $get_input : '' ,
                "response"          => json_encode($message) ,
            ];
            $this->log_request($log_data);

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_NO_CONTENT,
                'message'       => !empty($active_sites['message']) ? ($active_sites['message']) : "Sites data not found",
                'sites'         => false,
            ];

            ## log functionality
            $log_data = [];
            $log_data = [
                "api_end_point"     => 'activeSites_get',
                "headers"           => (!empty($headers)) ? $headers : '' ,
                "get_parameters"    => (!empty($get_input)) ? $get_input : '' ,
                "response"          => json_encode($message) ,
            ];
            $this->log_request($log_data);

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Installed Films request
    *   Required:
    *    - integrator token
    *    - integrator ID (fixed - 32)
    *    - content IDs (optional)
    */
    public function installedFilms_get()
    {
        // tracking requests -

        ## headers
        $headers        = [];
        $headers        = $this->input->request_headers();
        $api_key        = (isset($headers['api_key']) && !empty($headers['api_key']) && ($headers['api_key'])) ? $headers['api_key'] : null ;

        ## input values and validation
        $get_input      = $this->input->get();
        $integrator_id  = (!empty($get_input['integrator_id'])) ? (int) $get_input['integrator_id'] : null ;
        $site_ids       =  (!empty($get_input['site_ids']) && validate_array_of_integers(json_decode($get_input['site_ids']))) ? json_decode($get_input['site_ids']) : null ;
        $limit          = (!empty($get_input['limit'])) ? (int) $get_input['limit'] : null ;

        ## validation for required data
        $expected_data  = [
            'api_key'           => $api_key ,
            'integrator_id'     => $integrator_id
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('api_key', 'API key', 'required|callback_check_integrator_token');
        $this->form_validation->set_rules('integrator_id', 'Integrator ID', 'required|is_natural_no_zero|callback_check_integrator_fixed_id');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_BAD_REQUEST,
                'message'       => 'Validation errors: ' . $validation_errors,
                'films'         => null
            ];

            ## log functionality
            $log_data = [];
            $log_data = [
                "api_end_point"     => 'installedFilms_get',
                "headers"           => (!empty($headers)) ? $headers : '' ,
                "get_parameters"    => (!empty($get_input)) ? $get_input : '' ,
                "response"          => json_encode($message) ,
            ];
            $this->log_request($log_data);

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $installed_films = $this->IntegratorGateway_services->get_installed_films($integrator_id, $limit, $site_ids);

        if (($installed_films['status'] != false) && (!empty($installed_films['data']))) {
            $message = [
                'status'        => true,
                'http_code'     => REST_Controller::HTTP_OK,
                'message'       => !empty($installed_films['message']) ? ($installed_films['message']) : "Films data found",
                'films'         => ($installed_films['data'])
            ];

            ## log functionality
            $log_data = [];
            $log_data = [
                "api_end_point"     => 'installedFilms_get',
                "headers"           => (!empty($headers)) ? $headers : '' ,
                "get_parameters"    => (!empty($get_input)) ? $get_input : '' ,
                "response"          => json_encode($message) ,
            ];
            $this->log_request($log_data);

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_NO_CONTENT,
                'message'       => !empty($installed_films['message']) ? ($installed_films['message']) : "Films data not found",
                'films'         => false,
            ];

            ## log functionality
            $log_data = [];
            $log_data = [
                "api_end_point"     => 'installedFilms_get',
                "headers"           => (!empty($headers)) ? $headers : '' ,
                "get_parameters"    => (!empty($get_input)) ? $get_input : '' ,
                "response"          => json_encode($message) ,
            ];
            $this->log_request($log_data);

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
