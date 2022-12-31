<?php

namespace Application\Modules\Service\Controllers\Api;

use App\Adapter\RESTController;
use Application\Modules\Service\Models\AuditModel;

class Audit extends RESTController
{
    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->evidocs_service = new AuditModel();
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->lang->load('auth');
    }

    /** Get Evidoc Categories **/
    public function evidoc_categories_get()
    {
        $account_id   	= (int) $this->get('account_id');
        $category_id   	= (int) $this->get('category_id');
        $search_term  	= (!empty($this->get('search_term'))) ? trim(urldecode($this->get('search_term'))) : false ;
        $where 		  	= (!empty($this->get('where'))) ? $this->get('where') : false;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 			=> 'Invalid main Account ID.',
                'audit_categories' 	=> null,
                'counters' 			=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $audit_categories = $this->evidocs_service->get_audit_categories($account_id, $category_id, $search_term, $where);

        if (!empty($audit_categories)) {
            $message = [
                'status' 			=> true,
                'http_code' 		=> REST_Controller::HTTP_OK,
                'message' 			=> 'Evidocs categories data found',
                'audit_categories' 	=> (!empty($audit_categories->records)) ? $audit_categories->records : (!empty($audit_categories) ? $audit_categories : null),
                'counters' 			=> (!empty($audit_categories->counters)) ? $audit_categories->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
                'message' 			=> 'No records found',
                'audit_categories' 	=> null,
                'counters' 			=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Create new Evidocs resource
    */
    public function create_post()
    {
        $audit_data 	= $this->post();
        $site_id 		= $this->post('site_id');
        $asset_id 		= $this->post('asset_id');
        $vehicle_reg	= $this->post('vehicle_reg');
        $account_id 	= $this->post('account_id');
        $audit_type_id 	= $this->post('audit_type_id');

        $this->form_validation->set_rules('audit_type_id', 'Evidocs Type', 'required');
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid Evidocs data: ',
                'audit' => null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
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

        $new_audit = $this->evidocs_service->create_audit($account_id, $audit_type_id, $audit_data);

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
    * Update evidoc resource
    */
    public function update_post()
    {
        $audit_data = $this->post();
        $audit_id 	= (int) $this->post('audit_id');
        $account_id = (int) $this->post('account_id');

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('audit_id', 'Evidocs ID', 'required');

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

            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
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
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        $audit = $this->evidocs_service->get_audits($account_id, $audit_id);
        if (!$audit) {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'audit' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Run audit update
        $updated_asset = $this->evidocs_service->update_asset($account_id, $audit_id, $audit_data);
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
    * Get list of all Evidocss or single record
    */
    public function audits_get()
    {
        $account_id 		= (int) $this->get('account_id');
        $audit_id 			= (int) $this->get('audit_id');
        $asset_id 			= (int) $this->get('asset_id');
        $site_id 			= (int) $this->get('site_id');
        $person_id 			= (int) $this->get('person_id');
        $job_id 			= (int) $this->get('job_id');
        $audit_status 		= urldecode($this->get('audit_status'));
        $vehicle_reg 		= urldecode($this->get('vehicle_reg'));
        $inc_responses 		= $this->get('inc_responses');
        $where		 		= ($this->get('where')) ? $this->get('where') : [];
        $order_by		 	= ($this->get('order_by')) ? $this->get('order_by') : false;
        $limit		 		= ($this->get('limit')) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset	 			= ($this->get('offset')) ? (int) $this->get('offset') : 0;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> 'Invalid main Account ID',
                'audits' 	=> null,
                'counters' 	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $audit 		= $this->evidocs_service->get_audits($account_id, $audit_id, $asset_id, $site_id, $vehicle_reg, $person_id, $job_id, $audit_status, $inc_responses, $where, $order_by, $limit, $offset);
        if (!empty($audit)) {
            $message = [
                'status' 	=> true,
                'message' 	=> $this->session->flashdata('message'),
                'audits' 	=> (!empty($audit->records)) ? $audit->records : (!empty($audit) ? $audit : null),
                'counters' 	=> (!empty($audit->counters)) ? $audit->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'message' 	=> $this->session->flashdata('message'),
                'audits' 	=> (!empty($audit->records)) ? $audit->records : (!empty($audit) ? $audit : null),
                'counters' 	=> (!empty($audit->counters)) ? $audit->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Delete Evidocs resource
    */
    public function delete_get()
    {
        $account_id = (int) $this->get('account_id');
        $audit_id 	= (int) $this->get('audit_id');

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

        $delete_asset = $this->evidocs_service->delete_asset($account_id, $audit_id);

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
    * Search through list of Evidocss
    */
    public function lookup_get()
    {
        $account_id 	= (!empty($this->get('account_id'))) ? ( int ) $this->get('account_id') : false ;
        $search_term	= (!empty($this->get('search_term'))) ? trim(urldecode($this->get('search_term'))) : false ;
        $where 		 	= (!empty($this->get('where'))) ? $this->get('where') : false ;
        $order_by 		= (!empty($this->get('order_by'))) ? $this->get('order_by') : false ;
        $limit		 	= ($this->get('limit')) ? ( int ) $this->get('limit') : DEFAULT_LIMIT;
        $offset	 		= (!empty($this->get('offset'))) ? ( int ) $this->get('offset') : 0 ;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> 'Invalid main Account ID.',
                'audits' 	=> null,
                'audits' 	=> null,
                'counters' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $audit_lookup = $this->evidocs_service->audit_lookup($account_id, $search_term, $where, $order_by, $limit, $offset);

        if (!empty($audit_lookup)) {
            $message = [
                'status' 	=> true,
                'message' 	=> $this->session->flashdata('message'),
                'audits' 	=> (!empty($audit_lookup->records)) ? $audit_lookup->records : null,
                'counters' 	=> (!empty($audit_lookup->counters)) ? $audit_lookup->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'message'	=> $this->session->flashdata('message'),
                'audits' 	=> (!empty($audit_lookup->records)) ? $audit_lookup->records : null,
                'counters' 	=> (!empty($audit_lookup->counters)) ? $audit_lookup->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all audit types
    */
    public function audit_types_get()
    {
        $account_id   	= (int) $this->get('account_id');
        $audit_type	  	= $this->get('audit_type');
        $audit_group  	= $this->get('audit_group');
        $audit_type_id	= $this->get('audit_type_id');
        $category  	  	= $this->get('category');
        $categorized  	= $this->get('categorized');
        $un_grouped   	= $this->get('un_grouped');
        $asset_type_id	= $this->get('asset_type_id');
        $apply_limit  	= $this->get('apply_limit');
        $category_id  	= $this->get('category_id');
        $audit_frequency= $this->get('audit_frequency');
        $frequency_id	= $this->get('frequency_id');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'audit_types' => null,
                'counters' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $audit_types = $this->evidocs_service->get_audit_types($account_id, $audit_group, $audit_type, $audit_type_id, $categorized, $category, $un_grouped, $asset_type_id, $apply_limit, $category_id, $audit_frequency, $frequency_id);

        if (!empty($audit_types)) {
            $message = [
                'status' 	=> true,
                'message' 	=> $this->session->flashdata('message'),
                'audit_types' 	=> (!empty($audit_types->records)) ? $audit_types->records : null,
                'counters' 	=> (!empty($audit_types->counters)) ? $audit_types->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'message'	=> $this->session->flashdata('message'),
                'audit_types' 	=> null,
                'counters' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Get list of Evidoc Types /  Search list **/
    public function evidoc_types_get()
    {
        $account_id 	= (!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false ;
        $search_term	= (!empty($this->get('search_term'))) ? trim(urldecode($this->get('search_term'))) : false ;
        $where 		 	= (!empty($this->get('where'))) ? $this->get('where') : false ;
        $order_by 		= (!empty($this->get('order_by'))) ? $this->get('order_by') : false ;
        $limit		 	= ($this->get('limit')) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset	 		= (!empty($this->get('offset'))) ? (int) $this->get('offset') : 0 ;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> 'Invalid main Account ID.',
                'evidoc_types' 	=> null,
                'counters' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $evidoc_types = $this->evidocs_service->get_evidoc_types($account_id, $search_term, $where, $order_by, $limit, $offset);

        if (!empty($evidoc_types)) {
            $message = [
                'status' 	=> true,
                'message' 	=> $this->session->flashdata('message'),
                'evidoc_types' 	=> (!empty($evidoc_types->records)) ? $evidoc_types->records : $evidoc_types,
                'counters' 	=> (!empty($evidoc_types->counters)) ? $evidoc_types->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'message'	=> $this->session->flashdata('message'),
                'evidoc_types' 	=> null,
                'counters' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all Evidocs questions based on assessment type
    */
    public function audit_questions_get()
    {
        $account_id	  	= (int) $this->get('account_id');
        $question_id	= (int) $this->get('question_id');
        $audit_type_id	= (int) $this->get('audit_type_id');
        $asset_type_id	= (int) $this->get('asset_type_id');
        $section_ref 	= strip_all_whitespace($this->get('section_ref'));
        $segment 		= $this->get('segment');
        $segmented		= $this->get('segmented');
        $un_grouped		= $this->get('un_grouped');
        $sectioned		= $this->get('sectioned');

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

        $questions 	= $this->evidocs_service->get_audit_questions($account_id, $audit_type_id, $asset_type_id, $section_ref, $segment, $segmented, $un_grouped, $sectioned, $question_id);

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
        $account_id	  	= (int) $this->get('account_id');
        $audit_type_id	= (int) $this->get('audit_type_id');
        $asset_type_id	= (int) $this->get('asset_type_id');
        $audit_id		= (int) $this->get('audit_id');
        $un_grouped		= $this->get('un_grouped');
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

        $required_sections 	= $this->evidocs_service->get_required_sections($account_id, $audit_type_id, $asset_type_id, $audit_id, $un_grouped);

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
        $account_id	  	= (int) $this->get('account_id');
        $site_id		= (int) $this->get('site_id');
        $period_days	= (!empty($this->get('period_days'))) ? $this->get('period_days') : DEFAULT_PERIOD_DAYS;
        $req_percentage	= (!empty($this->get('req_percentage'))) ? $this->get('req_percentage') : DEFAULT_AUDIT_REQ_PERCENTAGE;

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

        $asset_list = $this->evidocs_service->get_audit_asset_list($account_id, $site_id, $period_days, $req_percentage);

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
        $account_id  	= (int) $this->get('account_id');
        $location_group = $this->get('location_group');
        $grouped     	= (int) $this->get('grouped');

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
                'message'=> $this->session->flashdata('message'),
                'locations' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all audit result statuses
    */
    public function result_statuses_get()
    {
        $account_id   			= (int) $this->get('account_id');
        $audit_result_status_id = (int) $this->get('audit_result_status_id');
        $audit_result_group		= $this->get('audit_result_group');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'audit_result_statuses' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $audit_result_statuses = $this->evidocs_service->get_audit_result_statuses($account_id, $audit_result_status_id, $audit_result_group);

        if (!empty($audit_result_statuses)) {
            $message = [
                'status' => true,
                'message' => 'Asset statuses records found',
                'audit_result_statuses' => $audit_result_statuses
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => 'No records found',
                'audit_result_statuses' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get Evidocs Dashboard Stats
    */
    public function audit_stats_get()
    {
        $account_id 	= (int) $this->get('account_id');
        $stat_type 		= $this->get('stat_type');
        $period_days 	= ($this->get('period_days')) ? $this->get('period_days') : 90;
        $date_from 		= ($this->get('date_from')) ? $this->get('date_from') : false;
        $date_to 		= ($this->get('date_to')) ? $this->get('date_to') : date('Y-m-d');
        $where 			= ($this->get('where')) ? $this->get('where') : false;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID',
                'type' => (!empty($stat_type)) ? $stat_type : 'audit_stats',
                'audit_stats' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $audit_stats = $this->evidocs_service->get_audit_stats($account_id, $stat_type, $period_days, $date_from, $date_to, $where);

        if (!empty($audit_stats)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'type' => (!empty($stat_type)) ? $stat_type : 'audit_stats',
                'audit_stats' => $audit_stats
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'type' => (!empty($stat_type)) ? $stat_type : 'audit_stats',
                'audit_stats' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * 	Get list of all audit types
    */
    public function audit_result_statuses_get()
    {
        $get_data = $this->get();

        $account_id   				= (!empty($get_data['account_id'])) ? ( int ) $get_data['account_id'] : false ;
        $audit_result_status_id   	= (!empty($get_data['audit_result_status_id'])) ? ( int ) $get_data['audit_result_status_id'] : false ;
        $result_status_group   		= (!empty($get_data['result_status_group'])) ? ( int ) $get_data['result_status_group'] : false ;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid main Account ID.',
                'results_statuses' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $results_statuses = $this->evidocs_service->get_audit_result_statuses($account_id, $audit_result_status_id, $result_status_group);

        if (!empty($results_statuses)) {
            $message = [
                'status' 			=> true,
                'message' 			=> 'Evidocs types records found',
                'results_statuses' 	=> $results_statuses
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> 'No records found',
                'results_statuses' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * 	Get 'Quick Stats' for the look up function
    */
    public function lookup_w_instant_stats_get()
    {
        $get_data = $this->get();

        $account_id 	= (!empty($get_data['account_id'])) ? ( int ) $get_data['account_id'] : false ;
        $search_term	= (!empty($get_data['search_term'])) ? trim(urldecode($get_data['search_term'])) : false ;
        $where 		 	= (!empty($get_data['where'])) ? $get_data['where'] : false ;
        $order_by 		= (!empty($get_data['order_by'])) ? $get_data['order_by'] : false ;
        $limit		 	= (!empty($get_data['limit'])) ? ( int ) $get_data['limit'] : DEFAULT_LIMIT;
        $offset	 		= (!empty($get_data['offset'])) ? ( int ) $get_data['offset'] : 0 ;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> 'Invalid main Account ID.',
                'dataset' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $dataset = $this->evidocs_service->get_lookup_w_instant_stats($account_id, $search_term, $where, $order_by, $limit, $offset);

        if (!empty($dataset)) {
            $message = [
                'status' 	=> true,
                'message' 	=> 'Evidocss and Instant Stats generated',
                'dataset' 	=> $dataset
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'message' 	=> 'Evidocss and Instant Stats not generated',
                'dataset' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get Evidocs Schedule(s)
    **/
    public function audit_schedule_get()
    {
        $get_data = false;
        $get_data = $this->get();

        $account_id 	= (!empty($get_data['account_id'])) ? ( int ) $get_data['account_id'] : false ;
        $where 			= (!empty($get_data['where'])) ? $get_data['where'] : false ;
        $order_by 		= (!empty($get_data['order_by'])) ? $get_data['order_by'] : false ;
        $limit		 	= (!empty($get_data['limit'])) ? ( int ) $get_data['limit'] : DEFAULT_LIMIT;
        $offset	 		= (!empty($get_data['offset'])) ? ( int ) $get_data['offset'] : 0 ;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID',
                'audit_schedule' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $audit_schedule = $this->evidocs_service->get_audit_schedule($account_id, $where, $order_by, $limit, $offset);

        if (!empty($audit_schedule)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'audit_schedule' => $audit_schedule
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'audit_schedule' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Get Evidocs Exceptions
    **/
    public function exceptions_get()
    {
        $get_data = $this->get();

        $account_id 	= (!empty($get_data['account_id'])) ? ( int ) $get_data['account_id'] : false ;
        $search_term	= (!empty($get_data['search_term'])) ? $get_data['search_term'] : false;
        $where 			= (!empty($get_data['where'])) ? $get_data['where'] : false ;
        $order_by 		= (!empty($get_data['order_by'])) ? $get_data['order_by'] : false ;
        $limit		 	= (!empty($get_data['limit'])) ? ( int ) $get_data['limit'] : DEFAULT_LIMIT;
        $offset	 		= (!empty($get_data['offset'])) ? ( int ) $get_data['offset'] : 0 ;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'		=> false,
                'message' 		=> 'Invalid main Account ID',
                'exceptions' 	=> null,
                'counters'		=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $exceptions = $this->evidocs_service->exceptions_lookup($account_id, $search_term, $where, $order_by, $limit, $offset);

        if (!empty($exceptions)) {
            $message = [
                'status' 		=> true,
                'message' 		=> $this->session->flashdata('message'),
                'exceptions' 	=> (!empty($exceptions->records)) ? $exceptions->records : (!empty($exceptions) ? $exceptions : null),
                'counters' 		=> (!empty($exceptions->counters)) ? $exceptions->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'exceptions' 	=> null,
                'counters'		=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all action statuses
    */
    public function action_statuses_get()
    {
        $get_data = $this->get();

        $account_id   			= (!empty($get_data['account_id'])) ? ( int ) $get_data['account_id'] : false ;
        $action_status_id   	= (!empty($get_data['action_status_id'])) ? ( int ) $get_data['action_status_id'] : false ;
        $action_status_group	= (!empty($get_data['action_status_group'])) ? ( int ) $get_data['action_status_group'] : false ;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid main Account ID.',
                'action_statuses' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $action_statuses = $this->evidocs_service->get_action_statuses($account_id, $action_status_id, $action_status_group);

        if (!empty($action_statuses)) {
            $message = [
                'status' 			=> true,
                'message' 			=> 'Action status records found',
                'action_statuses' 	=> $action_statuses
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> 'No records found',
                'action_statuses' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * 	Create new Exception log
    */
    public function create_exception_log_post()
    {
        $data 			= $this->post();
        $account_id 	= (!empty($data['account_id'])) ? ( int ) $data['account_id'] : false ;
        $exception_id 	= (!empty($data['exception_id'])) ? ( int ) $data['exception_id'] : false ;
        $data			= (!empty($data['data'])) ? $data['data'] : false ;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('exception_id', 'Exception ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 		=> false,
                'message' 		=> 'Invalid Exception data: '.(isset($validation_errors) && !empty($validation_errors)) ? $validation_errors : $message['message'],
                'exception_log' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'message' 		=> 'Invalid main Account ID',
                'exception_log' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $exception_log = $this->evidocs_service->create_exception_log($account_id, $exception_id, $data);

        if (!empty($exception_log)) {
            $message = [
                'status' 		=> true,
                'message' 		=> $this->session->flashdata('message'),
                'exception_log' => $exception_log
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'exception_log' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * 	Get Exception Log(s)
    **/
    public function exception_logs_get()
    {
        $get_data 			= $this->get();
        $account_id 		= (!empty($get_data['account_id'])) ? ( int ) $get_data['account_id'] : false ;
        $exception_log_id 	= (!empty($get_data['exception_log_id'])) ? ( int ) $get_data['exception_log_id'] : false ;
        $where 				= (!empty($get_data['where'])) ? $get_data['where'] : false ;
        $order_by 			= (!empty($get_data['order_by'])) ? $get_data['order_by'] : false ;
        $limit		 		= (!empty($get_data['limit'])) ? ( int ) $get_data['limit'] : DEFAULT_LIMIT;
        $offset	 			= (!empty($get_data['offset'])) ? ( int ) $get_data['offset'] : 0 ;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'			=> false,
                'message' 			=> 'Invalid main Account ID',
                'exception_logs' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $exception_logs = $this->evidocs_service->get_exception_log($account_id, $exception_log_id, $where, $order_by, $limit, $offset);

        if (!empty($exception_logs)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'exception_logs' 	=> $exception_logs
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'exception_logs' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all Evidocs questions based on Evidoc type
    */
    public function evidoc_questions_get()
    {
        $account_id		= (int) $this->get('account_id');
        $search_term	= (!empty($this->get('search_term'))) ? trim(urldecode($this->get('search_term'))) : false ;
        $where 			= (!empty($this->get('where'))) ? $this->get('where') : false ;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('evidoc_type_id', 'Evidoc Type ID', 'required');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid main Account ID',
                'evidoc_questions' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $evidoc_questions 	= $this->evidocs_service->get_evidoc_questions($account_id, $search_term, $where);

        if (!empty($evidoc_questions)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'evidoc_questions' => $evidoc_questions
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => 'No records found',
                'evidoc_questions' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all Evidoc Groups
    */
    public function evidoc_groups_get()
    {
        $account_id   	 = (int) $this->get('account_id');
        $evidoc_group_id = (int) $this->get('evidoc_group_id');
        $where			 = $this->get('where');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'evidoc_groups' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $evidoc_groups = $this->evidocs_service->get_evidoc_groups($account_id, $evidoc_group_id, $where);

        if (!empty($evidoc_groups)) {
            $message = [
                'status' => true,
                'message' => 'Asset statuses records found',
                'evidoc_groups' => $evidoc_groups
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => 'No records found',
                'evidoc_groups' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all Evidoc Frequncies
    */
    public function evidoc_frequencies_get()
    {
        $account_id   = (int) $this->get('account_id');
        $frequency_id = $this->get('frequency_id');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'evidoc_frequencies' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $evidoc_frequencies = $this->evidocs_service->get_evidoc_frequencies($account_id, $frequency_id);

        if (!empty($evidoc_frequencies)) {
            $message = [
                'status' => true,
                'message' => 'Evidocs types records found',
                'evidoc_frequencies' => $evidoc_frequencies
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => 'No records found',
                'evidoc_frequencies' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Get Evidoc Segments **/
    public function evidoc_segments_get()
    {
        $account_id   = (int) $this->get('account_id');
        $where 		  = $this->get('where');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'evidoc_segments' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $evidoc_segments = $this->evidocs_service->get_evidoc_segments($account_id = false, $where); //Remove false assignment to enforce use of account ID

        if (!empty($evidoc_segments)) {
            $message = [
                'status' => true,
                'message' => 'Evidocs Response types found',
                'evidoc_segments' => $evidoc_segments
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => 'No records found',
                'evidoc_segments' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all Evidoc Sections
    */
    public function evidoc_sections_get()
    {
        $account_id   = (int) $this->get('account_id');
        $where 		  = $this->get('where');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'evidoc_sections' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $evidoc_sections = $this->evidocs_service->get_evidoc_sections($account_id, $where);

        if (!empty($evidoc_sections)) {
            $message = [
                'status' => true,
                'message' => 'Evidocs types records found',
                'evidoc_sections' => $evidoc_sections
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => 'No records found',
                'evidoc_sections' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all Evidoc Sections
    */
    public function evidoc_type_sections_get()
    {
        $account_id = (int) $this->get('account_id');
        $where 		= $this->get('where');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'evidoc_type_sections' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $evidoc_type_sections = $this->evidocs_service->get_evidoc_type_sections($account_id, $where);

        if (!empty($evidoc_type_sections)) {
            $message = [
                'status' => true,
                'message' => 'Evidocs types records found',
                'evidoc_type_sections' => $evidoc_type_sections
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => 'No records found',
                'evidoc_type_sections' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Create a New Evidoc Type **/
    public function create_evidoc_type_post()
    {
        $evidoc_type_data = $this->post();
        $account_id		  = (int) $this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('audit_type', 'Evidoc Name', 'required');
        $this->form_validation->set_rules('audit_group', 'Evidoc group ID', 'required');
        $this->form_validation->set_rules('category_id', 'Evidoc Category', 'required');
        $this->form_validation->set_rules('audit_frequency', 'Evidoc Frequency', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid data: ',
                'evidoc_type' => null,
                'exists' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'evidoc_type' => null,
                'exists' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_evidoc_type = $this->evidocs_service->create_evidoc_type($account_id, $evidoc_type_data);
        $exists 		 = $this->session->flashdata('already_exists');

        if (!empty($new_evidoc_type)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'evidoc_type' => $new_evidoc_type,
                'exists' => (!empty($exists)) ? true : false
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'evidoc_type' => null,
                'exists' => (!empty($exists)) ? true : false
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Get List of Response Types **/
    public function response_types_get()
    {
        $account_id   = (int) $this->get('account_id');
        $where 		  = $this->get('where');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'response_types' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $response_types = $this->evidocs_service->get_response_types($account_id, $where);

        if (!empty($response_types)) {
            $message = [
                'status' => true,
                'message' => 'Evidocs Response types found',
                'response_types' => $response_types
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => 'No records found',
                'response_types' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Create a Evidoc Section **/
    public function add_new_section_post()
    {
        $evidoc_section_data = $this->post();
        $account_id		  	 = (int) $this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid data: ',
                'evidoc_section' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'evidoc_section' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_evidoc_section = $this->evidocs_service->add_new_section($account_id, $evidoc_section_data);

        if (!empty($new_evidoc_section)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'evidoc_section' => $new_evidoc_section
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'evidoc_section' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Create a New Evidoc Question **/
    public function add_evidoc_question_post()
    {
        $evidoc_question_data = $this->post();
        $account_id		  	  = (int) $this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('question', 'Evidoc Question', 'required');
        $this->form_validation->set_rules('audit_type_id', 'Evidoc Type ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid data: ',
                'evidoc_question' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID',
                'evidoc_question' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $evidoc_question = $this->evidocs_service->add_evidoc_question($account_id, $evidoc_question_data);

        if (!empty($evidoc_question)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'evidoc_question' => $evidoc_question
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'evidoc_question' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Update an Evidoc name **/
    public function update_evidoc_name_post()
    {
        $evidoc_name_data 	= $this->post();
        $account_id 		= ( int ) $this->post('account_id');
        $audit_type_id 		= ( int ) $this->post('audit_type_id');

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('audit_type_id', 'Evidoc name ID', 'required');
        $this->form_validation->set_rules('audit_type', 'Evidoc name', 'required');
        $this->form_validation->set_rules('audit_group', 'Evidoc group', 'required');
        $this->form_validation->set_rules('category_id', 'Evidoc Category', 'required');
        $this->form_validation->set_rules('audit_frequency', 'Evidoc Frequency', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid data: ',
                'evidoc_name' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'evidoc_name' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Validate the evidoc name id.
        if ($audit_type_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        ## Run update call
        $updated_evidoc_name = $this->evidocs_service->update_evidoc_name($account_id, $audit_type_id, $evidoc_name_data);

        if (!empty($updated_evidoc_name)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'evidoc_name' => $updated_evidoc_name
            ];
            $this->response($message, REST_Controller::HTTP_OK); // Resource Updated
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'evidoc_name' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Update Evidoc Question **/
    public function update_question_post()
    {
        $question_data 	= $this->post();
        $account_id 		= ( int ) $this->post('account_id');
        $question_id 		= ( int ) $this->post('question_id');

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('question_id', 'Question ID', 'required');
        $this->form_validation->set_rules('question', 'Question label', 'required');
        $this->form_validation->set_rules('section', 'Evidoc Section', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid data: ',
                'evidoc_question' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'evidoc_question' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Validate the evidoc name id.
        if ($question_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        ## Run update call
        $updated_question = $this->evidocs_service->update_question($account_id, $question_id, $question_data);

        if (!empty($updated_question)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'evidoc_question' => $updated_question
            ];
            $this->response($message, REST_Controller::HTTP_OK); // Resource Updated
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'evidoc_question' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Delete Question resource
    */
    public function delete_question_get()
    {
        $account_id 	= (int) $this->get('account_id');
        $question_id 	= (int) $this->get('question_id');

        if ($question_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'question' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $delete_question = $this->evidocs_service->delete_question($account_id, $question_id);

        if (!empty($delete_question)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'question' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'question' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Get Evidoc Categories **/
    public function audit_categories_get()
    {
        $account_id   	= (int) $this->get('account_id');
        $category_id   	= (int) $this->get('category_id');
        $search_term  	= (!empty($this->get('search_term'))) ? trim(urldecode($this->get('search_term'))) : false ;
        $where 		  	= (!empty($this->get('where'))) ? $this->get('where') : false;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 			=> 'Invalid main Account ID.',
                'audit_categories' 	=> null,
                'counters' 			=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $audit_categories = $this->evidocs_service->get_audit_categories($account_id, $category_id, $search_term, $where);

        if (!empty($audit_categories)) {
            $message = [
                'status' 			=> true,
                'http_code' 		=> REST_Controller::HTTP_OK,
                'message' 			=> 'Evidocs categories data found',
                'audit_categories' 	=> (!empty($audit_categories->records)) ? $audit_categories->records : (!empty($audit_categories) ? $audit_categories : null),
                'counters' 			=> (!empty($audit_categories->counters)) ? $audit_categories->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
                'message' 			=> 'No records found',
                'audit_categories' 	=> null,
                'counters' 			=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Create a Evidoc Category **/
    public function add_category_post()
    {
        $evidoc_category_data = $this->post();
        $account_id		  	  = (int) $this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid data: ',
                'evidoc_category' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'evidoc_category' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_evidoc_category = $this->evidocs_service->add_category($account_id, $evidoc_category_data);

        if (!empty($new_evidoc_category)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'evidoc_category' => $new_evidoc_category
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'evidoc_category' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Update Category **/
    public function update_category_post()
    {
        $evidoc_category_data = $this->post();
        $account_id		  	  = (int) $this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid data: ',
                'evidoc_category' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'evidoc_category' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $update_category = $this->evidocs_service->update_category($account_id, $evidoc_category_data);

        if (!empty($update_category)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'evidoc_category' => $update_category
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'evidoc_category' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Delete Category Record
    */
    public function delete_category_post()
    {
        $account_id 	= (int) $this->post('account_id');
        $category_id 	= (int) $this->post('category_id');

        if ($category_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'Invalid main Account ID.',
                'category' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $delete_category = $this->evidocs_service->delete_audit_category($account_id, $category_id);

        if (!empty($delete_category)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'category'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NOT_MODIFIED,
                'message' 	=> $this->session->flashdata('message'),
                'category' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Get list of all audit progress statuses
    */
    public function progress_statuses_get()
    {
        $account_id   			= (!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false ;
        $where   				= (!empty($this->get('where'))) ? $this->get('where') : false ;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 					=> false,
                'message' 					=> 'Invalid main Account ID.',
                'audit_progress_statuses' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $audit_progress_statuses = $this->evidocs_service->get_audit_progress_statuses($account_id, $where);

        if (!empty($audit_progress_statuses)) {
            $message = [
                'status' 					=> true,
                'message' 					=> $this->session->flashdata('message'),
                'audit_progress_statuses' 	=> $audit_progress_statuses
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 					=> false,
                'message' 					=> 'No records found',
                'audit_progress_statuses' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * 	Get list of statuses for exceptions
    */
    public function exception_statuses_get()
    {
        $get 				= $this->get();

        $account_id 		= (!empty($get['account_id'])) ? $get['account_id'] : false ;
        $action_status_id 	= (!empty($get['action_status_id'])) ? $get['action_status_id'] : false ;
        $where				= (!empty($get['where'])) ? $get['where'] : false ;

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
                'status' 				=> false,
                'message' 				=> 'Validation errors: '.$validation_errors,
                'exception_statuses' 	=> null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 				=> false,
                'message' 				=> 'Invalid main Account ID.',
                'exception_statuses' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $exception_statuses = $this->evidocs_service->get_exception_statuses($account_id, $action_status_id, $where);

        if (!empty($exception_statuses)) {
            $message = [
                'status' 				=> true,
                'message' 				=> $this->session->flashdata('message'),
                'exception_statuses' 	=> $exception_statuses
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 				=> false,
                'message' 				=> $this->session->flashdata('message'),
                'exception_statuses' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Re-Order Questions
    */
    public function reorder_questions_post()
    {
        $account_id 	= (int) $this->post('account_id');
        $audit_type_id 	= (int) $this->post('audit_type_id');
        $params 		= $this->post();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'Invalid main Account ID.',
                'questions' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if ($audit_type_id <= 0) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' 	=> 'Missing required fields',
                'questions' => null
            ];
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        $reorder_questions = $this->evidocs_service->reorder_evidoc_questions($account_id, $audit_type_id, $params);

        if (!empty($reorder_questions)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'questions'	=> $reorder_questions
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NOT_MODIFIED,
                'message' 	=> $this->session->flashdata('message'),
                'questions' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Delete Evidoc Type
    */
    public function delete_evidoc_type_post()
    {
        $postdata 		= $this->post();
        $account_id 	= (!empty($postdata['account_id'])) ? (int) $postdata['account_id'] : false ;
        $audit_type_id 	= (!empty($postdata['audit_type_id'])) ? (int) $postdata['audit_type_id'] : false ;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('audit_type_id', 'Evidoc Type ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 		=> $validation_errors,
                'evidoc_type' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 		=> 'Invalid main Account ID.',
                'evidoc_type' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $evidoc_type = $this->evidocs_service->delete_evidoc_type($account_id, $audit_type_id);

        if (!empty($evidoc_type)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_OK,
                'message' 		=> $this->session->flashdata('message'),
                'evidoc_type' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
                'message' 		=> $this->session->flashdata('message'),
                'evidoc_type' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Clone Evidoc Type
    */
    public function clone_evidoc_type_post()
    {
        $evidoc_type_data 	= $this->post();
        $account_id 		= ( int ) $this->post('account_id');
        $audit_type_id 		= ( int ) $this->post('cloned_audit_type_id');

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('cloned_audit_type_id', 'Evidoc Type ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 		=> 'Invalid data: ',
                'evidoc_type'	=> null
            ];

            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 		=> 'Invalid main Account ID.',
                'evidoc_type'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Validate the Evidoc Type id.
        if ($audit_type_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        $cloned_evidoc_type = $this->evidocs_service->clone_evidoc_type($account_id, $audit_type_id, $evidoc_type_data);

        if (!empty($cloned_evidoc_type)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_OK,
                'message' 		=> $this->session->flashdata('message'),
                'evidoc_type'	=> $cloned_evidoc_type
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
                'message' 		=> $this->session->flashdata('message'),
                'evidoc_type'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Get Audit Generic Assets **/
    public function generic_assets_get()
    {
        $account_id 	= (!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false ;
        $audit_id 		= (!empty($this->get('audit_id'))) ? (int) $this->get('audit_id') : false ;
        $where 		 	= (!empty($this->get('where'))) ? $this->get('where') : false ;
        $order_by 		= (!empty($this->get('order_by'))) ? $this->get('order_by') : false ;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 		=> 'Invalid main Account ID.',
                'generic_assets'=> null,
                'counters' 		=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $generic_assets = $this->evidocs_service->get_audit_generic_assets($account_id, $audit_id, $where, $order_by);

        if (!empty($generic_assets)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_OK,
                'message' 		=> $this->session->flashdata('message'),
                'generic_assets'=> (!empty($generic_assets->records)) ? $generic_assets->records : $generic_assets,
                'counters' 		=> (!empty($generic_assets->counters)) ? $generic_assets->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
                'message'		=> $this->session->flashdata('message'),
                'generic_assets'=> null,
                'counters' 		=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
