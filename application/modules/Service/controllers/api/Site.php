<?php

namespace Application\Modules\Service\Controllers\Api;

use App\Adapter\RESTController;
use Application\Modules\Service\Models\AccountModel;
use Application\Modules\Service\Models\SiteModel;

class Site extends RESTController
{
	/**
	 * @var \Application\Modules\Service\Models\SiteModel
	 */
	private $site_service;
	/**
	 * @var \Application\Modules\Service\Models\AccountModel
	 */
	private $account_service;

	public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->site_service = new SiteModel();
        $this->account_service = new AccountModel();
        $this->form_validation->set_error_delimiters(
			$this->config->item('error_start_delimiter', 'ion_auth'),
			$this->config->item('error_end_delimiter', 'ion_auth')
		);
        $this->lang->load('auth');
    }

    /**
    * Create new Site resource
    */
    public function create_post()
    {
        $site_data = $this->post();
        $account_id	   = (int)$this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('site_name', 'Site Name', 'required');
        //$this->form_validation->set_rules('site_unique_id', 'Site Unique ID', 'is_unique[site.site_unique_id]');
        //$this->form_validation->set_rules('site_address_id', 'Site Address Name', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid Site data: ',
                'site' => null
            ];
            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main acocount is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID',
                'site' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_site = $this->site_service->create_site($site_data);

        if (!empty($new_site)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'site' => $new_site
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'site' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Update site resource
    */
    public function update_post()
    {
        $site_data	= $this->post();
        $site_id 	= (int) $this->post('site_id');
        $account_id = (int) $this->post('account_id');

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('site_id', 'Site ID', 'required');
        //$this->form_validation->set_rules('site_unique_id', 'Site Unique ID', 'required');
        #$this->form_validation->set_rules('site_address_id', 'Site Address Name', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$site_id || !$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid data: ',
                'site' => null
            ];

            $message['message'] = (!$site_id) ? $message['message'].'site_id, ' : $message['message'];
            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Validate the site id.
        if ($site_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        //Check and verify that main acocount is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID',
                'site' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $site = $this->site_service->get_sites($account_id, $site_id);
        if (!$site) {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'site' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Run site update
        $updated_site = $this->site_service->update_site($account_id, $site_id, $site_data);
        if (!empty($updated_site)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'site' => $updated_site
            ];
            $this->response($message, REST_Controller::HTTP_OK); // Resource Updated
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'site' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all Sites or single record
    */
    public function sites_get()
    {
        $site_id 		= (int)$this->get('site_id');
        $account_id 	= (int)$this->get('account_id');
        $site_unique_id = trim(urldecode($this->get('site_unique_id')));
        $where 			= !empty($this->get('where')) ? $this->get('where') : [];
        $order_by		= !empty($this->get('order_by')) ? $this->get('order_by') : false;
        $limit 			= !empty($this->get('limit')) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset 		= !empty($this->get('offset')) ? (int) $this->get('offset') : DEFAULT_OFFSET;
        $alarmed		= $this->get('alarmed');

        if (!empty($alarmed)) {
            $where['alarmed'] = $alarmed;
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'sites' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $sites = $this->site_service->get_sites($account_id, $site_id, $site_unique_id, $where, $order_by, $limit, $offset);

        if (!empty($sites)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'sites' =>$sites
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'sites' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Delete Site resource
    */
    public function delete_get()
    {
        $site_id 	= (int) $this->get('site_id');
        $account_id 	= (int) $this->get('account_id');

        if ($site_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        //Check and verify that main acocount is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID',
                'site' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $delete_site = $this->site_service->delete_site($account_id, $site_id);
        if (!empty($delete_site)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'site' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'site' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get all contracts attached to the Site
    */
    public function contracts_get()
    {
        $account_id = (int) $this->get('account_id');
        $site_id 	= (int) $this->get('site_id');
        $grouped	= $this->get('grouped');
        $grouped	= ($grouped == '0') ? false : true;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'site_contracts' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $site_contracts = $this->site_service->get_site_contracts($account_id, $site_id, $grouped);

        if (!empty($site_contracts)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'site_contracts' => $site_contracts
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'site_contracts' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Search through list of Sites
    */
    public function lookup_get()
    {
        $account_id 	= (!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false ;
        $search_term	= (!empty($this->get('search_term'))) ? trim(urldecode($this->get('search_term'))) : false ;
        $where 		 	= (!empty($this->get('where'))) ? $this->get('where') : false ;
        $order_by 		= (!empty($this->get('order_by'))) ? $this->get('order_by') : false ;
        $limit		 	= (!empty($this->get('limit'))) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset	 		= (!empty($this->get('offset'))) ? (int) $this->get('offset') : 0 ;
        $alarmed	 	= (!empty($this->get('alarmed'))) ? (int) $this->get('alarmed') : 0 ;

        if (!empty($alarmed)) {
            $where['alarmed'] = $alarmed;
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> 'Invalid main Account ID.',
                'sites' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $site_lookup = $this->site_service->site_lookup($account_id, $search_term, $where, $order_by, $limit, $offset);

        if (!empty($site_lookup)) {
            $message = [
                'status' 	=> true,
                'message' 	=> $this->session->flashdata('message'),
                'sites' 	=> $site_lookup
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'message' 	=> $this->session->flashdata('message'),
                'sites' 	=> $site_lookup
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all site logs
    */
    public function site_change_logs_get()
    {
        $site_id   = (int) $this->get('site_id');
        $account_id= (int) $this->get('account_id');


        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'site_logs' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $site_logs = $this->site_service->get_site_change_logs($account_id, $site_id);

        if (!empty($site_logs)) {
            $message = [
                'status' => true,
                'message' => 'Site change log records found',
                'site_logs' => $site_logs
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => 'No records found',
                'site_logs' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all site statuses
    */
    public function site_statuses_get()
    {
        $account_id   = (int) $this->get('account_id');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'site_statuses' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $site_statuses = $this->site_service->get_site_statuses($account_id);

        if (!empty($site_statuses)) {
            $message = [
                'status' => true,
                'message' => 'Site statuses records found',
                'site_statuses' => $site_statuses
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => 'No records found',
                'site_statuses' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all locations on the block
    */
    public function locations_get()
    {
        $account_id = (int) $this->get('account_id');
        $site_id 	= (int) $this->get('site_id');
        $where 		= (!empty($this->get('where'))) ? $this->get('where') : false ;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid main Account ID.',
                'site_locations' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $site_locations = $this->site_service->get_locations($account_id, $site_id, $where);

        if (!empty($site_locations)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'site_locations' 	=> $site_locations
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'site_locations' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Get a list of a statuses **/
    public function event_statuses_get()
    {
        $account_id   = (int) $this->get('account_id');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid main Account ID.',
                'event_statuses' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $event_statuses = $this->site_service->get_site_event_statuses($account_id = false);

        if (!empty($event_statuses)) {
            $message = [
                'status' 			=> true,
                'message' 			=> 'Event statuses found',
                'event_statuses' 	=> $event_statuses
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> 'No records found',
                'event_statuses' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Get Statistics related to SITE **/
    public function site_stats_get()
    {
        $account_id 	= (int) $this->get('account_id');
        $stat_type 		= $this->get('stat_type');
        $date_from 		= ($this->get('date_from')) ? $this->get('date_from') : false;
        $date_to 		= ($this->get('date_to')) ? $this->get('date_to') : date('Y-m-d');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID',
                'type' => (!empty($stat_type)) ? $stat_type : 'site_stats',
                'site_stats' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $site_stats = $this->site_service->get_site_stats($account_id, $stat_type, $date_from, $date_to);

        if (!empty($site_stats)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'type' => (!empty($stat_type)) ? $stat_type : 'site_stats',
                'site_stats' => $site_stats
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'type' => (!empty($stat_type)) ? $stat_type : 'site_stats',
                'site_stats' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * 	Get 'Quick Stats' for the look up function
    */
    public function lookup_instant_stats_get()
    {
        $get_data = false;
        $get_data = $this->get();


        $site_statuses = $this->get('site_statuses');
        $alarmed		= $this->get('alarmed');

        $account_id 	= (!empty($get_data['account_id'])) ? ( int ) $get_data['account_id'] : false ;
        $search_term	= (!empty($get_data['search_term'])) ? trim(urldecode($get_data['search_term'])) : false ;
        $where 		 	= (!empty($get_data['where'])) ? $get_data['where'] : false ;
        $order_by 		= (!empty($get_data['order_by'])) ? $get_data['order_by'] : false ;
        $limit		 	= (!empty($get_data['limit'])) ? ( int ) $get_data['limit'] : DEFAULT_LIMIT;
        $offset	 		= (!empty($get_data['offset'])) ? ( int ) $get_data['offset'] : 0 ;
        $site_statuses	= (!empty($get_data['site_statuses'])) ? $get_data['site_statuses'] : 0 ;
        $alarmed	 	= (!empty($get_data['alarmed'])) ? $get_data['alarmed'] : 0 ;

        if (!empty($alarmed)) {
            $where['alarmed'] = $alarmed;
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> 'Invalid main Account ID.',
                'stats' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $stats = $this->site_service->get_lookup_instant_stats($account_id, $search_term, $site_statuses, $where, $order_by, $limit, $offset);

        if (!empty($stats)) {
            $message = [
                'status' 	=> true,
                'message' 	=> 'Instant Stats generated',
                'stats' 	=> $stats
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'message' 	=> 'Instant Stats couldn\'t be generated',
                'stats' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Create Location record **/
    public function create_site_locations_post()
    {
        $postdata 	= $this->post();
        $account_id = !empty($this->post('account_id')) ? ( int ) $this->post('account_id') : false;
        $site_id 	= !empty($this->post('site_id')) ? ( int ) $this->post('site_id') : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('site_id', 'Site ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' 	=> 'Invalid request data: ',
                'locations'	=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'locations' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $locations = $this->site_service->create_site_locations($account_id, $site_id, $postdata);

        if (!empty($locations)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_CREATED,
                'message' 	=> $this->session->flashdata('message'),
                'locations' => $locations
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'	=> false,
                'http_code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' 	=> $this->session->flashdata('message'),
                'locations' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Update / Edit Location record **/
    public function update_site_location_post()
    {
        $postdata 	= $this->post();
        $account_id = !empty($this->post('account_id')) ? ( int ) $this->post('account_id') : false;
        $location_id= !empty($this->post('location_id')) ? ( int ) $this->post('location_id') : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'  	=> false,
                'http_code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' 	=> 'Invalid request data: ',
                'location'	=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'location' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $location = $this->site_service->update_site_location($account_id, $location_id, $postdata);

        if (!empty($location)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'location' 	=> $location
            ];
            $this->response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = [
                'status'	=> false,
                'http_code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' 	=> $this->session->flashdata('message'),
                'location' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Get list of Locations **/
    public function site_locations_get()
    {
        $account_id 	= (!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false ;
        $location_id 	= (!empty($this->get('location_id'))) ? (int) $this->get('location_id') : false ;
        $search_term	= (!empty($this->get('search_term'))) ? trim($this->get('search_term')) : false ;
        $where 		 	= (!empty($this->get('where'))) ? $this->get('where') : false ;
        $order_by 		= (!empty($this->get('order_by'))) ? $this->get('order_by') : false ;
        $limit		 	= ($this->get('limit')) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset	 		= (!empty($this->get('offset'))) ? (int) $this->get('offset') : 0 ;

        $this->form_validation->set_data([ 'account_id'=>$account_id ]);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'Invalid data: ',
                'locations' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID.',
                'locations' => null,
                'counters' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $locations = $this->site_service->get_site_locations($account_id, $location_id, $search_term, $where, $order_by, $limit, $offset);

        if (!empty($locations)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'locations' => (!empty($locations->records)) ? $locations->records : (!empty($locations) ? $locations : null),
                'counters' 	=> (!empty($locations->counters)) ? $locations->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message'	=> 'No locations data found',
                'locations' => null,
                'counters' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Delete an existing Location record **/
    /**
    */
    public function delete_site_location_post()
    {
        $postdata 		= $this->post();

        $account_id 	= (!empty($postdata['account_id'])) ? (int) $postdata['account_id'] : false ;
        $site_id 		= (!empty($postdata['site_id'])) ? (int) $postdata['site_id'] : false ;
        $location_id 	= (!empty($postdata['location_id'])) ? (int) $postdata['location_id'] : false ;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('site_id', 'Site ID', 'required');
        $this->form_validation->set_rules('location_id', 'Location ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' 	=> $validation_errors,
                'location' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $site_location_exists = $this->site_service->get_site_locations($account_id, $location_id, false, ['site_id'=>$site_id]);

        if (($location_id <= 0) || (! (( int ) $location_id) || !$site_location_exists)) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID.',
                'location' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $site_location = $this->site_service->delete_site_location($account_id, $site_id, $location_id);

        if (!empty($site_location)) {
            $message = [
                'status' 			=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'location' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NOT_MODIFIED,
                'message' 	=> $this->session->flashdata('message'),
                'location' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Unlink a location from a Site **/
    public function unlink_locations_post()
    {
        $postdata 	= $this->post();
        $account_id = !empty($this->post('account_id')) ? ( int ) $this->post('account_id') : false;
        $location_id= !empty($this->post('location_id')) ? ( int ) $this->post('location_id') : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('location_id', 'Location ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' 	=> 'Invalid request data: ',
                'location' 	=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' 	=> 'Invalid main Account ID',
                'location' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $unlink_locations = $this->site_service->unlink_locations($account_id, $location_id, $postdata);

        if (!empty($unlink_locations)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'location' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' 	=> $this->session->flashdata('message'),
                'location' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Create a Site Zone **/
    public function add_site_zone_post()
    {
        $site_zone_data	= $this->post();
        $account_id		= (int) $this->post('account_id');
        $site_id		= (int) $this->post('site_id');

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('site_id', 'Site ID', 'required');
        $this->form_validation->set_rules('zone_name', 'Zone Name', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            $message = [
                'status' 	=> false,
                'message' 	=> 'Invalid data: ',
                'site_zone' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> 'Invalid main Account ID.',
                'site_zone' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_site_zone = $this->site_service->add_site_zone($account_id, $site_id, $site_zone_data);

        if (!empty($new_site_zone)) {
            $message = [
                'status' 	=> true,
                'message' 	=> $this->session->flashdata('message'),
                'site_zone' => $new_site_zone
            ];
            $this->response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = [
                'status' 	=> false,
                'message' 	=> $this->session->flashdata('message'),
                'site_zone' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Update Site Zone **/
    public function update_site_zone_post()
    {
        $site_zone_data 	  = $this->post();
        $account_id		  	  = (int) $this->post('account_id');
        $site_id		  	  = (int) $this->post('site_id');
        $zone_id		  	  = (int) $this->post('zone_id');

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('site_id', 'Site ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            $message = [
                'status' 	=> false,
                'message' 	=> 'Invalid data: ',
                'site_zone' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> 'Invalid main Account ID.',
                'site_zone' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $update_site_zone = $this->site_service->update_site_zone($account_id, $zone_id, $site_zone_data);

        if (!empty($update_site_zone)) {
            $message = [
                'status' 	=> true,
                'message' 	=> $this->session->flashdata('message'),
                'site_zone' => $update_site_zone
            ];
            $this->response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = [
                'status' 	=> false,
                'message' 	=> $this->session->flashdata('message'),
                'site_zone' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Get Site Zones **/
    public function site_zones_get()
    {
        $account_id = !empty($this->get('account_id')) ? (int) $this->get('account_id') : false;
        $where 		= !empty($this->get('where')) ? $this->get('where') : false;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> 'Invalid main Account ID.',
                'site_zones'=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $site_zones = $this->site_service->get_site_zones($account_id, $where);

        if (!empty($site_zones)) {
            $message = [
                'status' 	=> true,
                'message' 	=> 'Site Zones found',
                'site_zones'=> $site_zones
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'message' 	=> 'No records found',
                'site_zones'=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Get list of Site / Building systems
    */
    public function expected_systems_get()
    {
        $account_id   	= (int) $this->get('account_id');
        $site_id   		= (int) $this->get('site_id');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid main Account ID.',
                'expected_systems' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $expected_systems = $this->site_service->get_expected_systems($account_id, $site_id);

        if (!empty($expected_systems)) {
            $message = [
                'status' 			=> true,
                'message' 			=> 'Building system records found',
                'expected_systems' 	=> $expected_systems
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> 'No records found',
                'expected_systems'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Get list of Site / Building systems
    */
    public function installed_systems_get()
    {
        $account_id = !empty($this->get('account_id')) ? (int) $this->get('account_id') : false;
        $site_id 	= !empty($this->get('site_id')) ? (int) $this->get('site_id') : false;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid main Account ID.',
                'installed_systems' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $installed_systems = $this->site_service->get_installed_systems($account_id, $site_id);

        if (!empty($installed_systems)) {
            $message = [
                'status' 			=> true,
                'message' 			=> 'Installed systems data found',
                'installed_systems' => $installed_systems
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> 'No records found',
                'installed_systems'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


        /** Create a Site Sub Block **/
    public function add_site_sub_block_post()
    {
        $site_sub_block_data	= $this->post();
        $account_id		= (int) $this->post('account_id');
        $site_id		= (int) $this->post('site_id');

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('site_id', 'Site ID', 'required');
        $this->form_validation->set_rules('sub_block_name', 'Sub Block Name', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 		=> 'Invalid data: ',
                'site_sub_block'=> null
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
                'site_sub_block'=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_site_sub_block = $this->site_service->add_site_sub_block($account_id, $site_id, $site_sub_block_data);

        if (!empty($new_site_sub_block)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_CREATED,
                'message' 		=> $this->session->flashdata('message'),
                'site_sub_block'=> $new_site_sub_block
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
                'message' 		=> $this->session->flashdata('message'),
                'site_sub_block'=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Update Site Sub Block **/
    public function update_site_sub_block_post()
    {
        $site_sub_block_data 	  = $this->post();
        $account_id		  	  = (int) $this->post('account_id');
        $site_id		  	  = (int) $this->post('site_id');
        $sub_block_id		  = (int) $this->post('sub_block_id');

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('site_id', 'Site ID', 'required');
        $this->form_validation->set_rules('sub_block_id', 'Sub Block ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            $message = [
                'status' 		=> false,
                'message' 		=> 'Invalid data: ',
                'site_sub_block'=> null
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
                'site_sub_block'=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $update_site_sub_block = $this->site_service->update_site_sub_block($account_id, $sub_block_id, $site_sub_block_data);

        if (!empty($update_site_sub_block)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_OK,
                'message' 		=> $this->session->flashdata('message'),
                'site_sub_block'=> $update_site_sub_block
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NOT_MODIFIED,
                'message' 		=> $this->session->flashdata('message'),
                'site_sub_block'=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Get Site Sub Blocks **/
    public function site_sub_blocks_get()
    {
        $account_id = !empty($this->get('account_id')) ? (int) $this->get('account_id') : false;
        $where 		= !empty($this->get('where')) ? $this->get('where') : false;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 			=> 'Invalid main Account ID.',
                'site_sub_blocks'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $site_sub_blocks = $this->site_service->get_site_sub_blocks($account_id, $where);

        if (!empty($site_sub_blocks)) {
            $message = [
                'status' 			=> true,
                'http_code' 		=> REST_Controller::HTTP_OK,
                'message' 			=> 'Site Sub Blocks found',
                'site_sub_blocks'	=> $site_sub_blocks
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
                'message' 			=> 'No records found',
                'site_sub_blocks'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Delete an existing Sub Block record
    */
    public function delete_site_sub_block_post()
    {
        $postdata 		= $this->post();

        $account_id 	= (!empty($postdata['account_id'])) ? (int) $postdata['account_id'] : false ;
        $site_id 		= (!empty($postdata['site_id'])) ? (int) $postdata['site_id'] : false ;
        $sub_block_id 	= (!empty($postdata['sub_block_id'])) ? (int) $postdata['sub_block_id'] : false ;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('site_id', 'Site ID', 'required');
        $this->form_validation->set_rules('sub_block_id', 'Sub Block ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' 	=> $validation_errors,
                'sub_block' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $sub_block_exists = $this->site_service->get_site_sub_blocks($account_id, ['site_id'=>$site_id, 'sub_block_id'=>$sub_block_id]);

        if (($sub_block_id <= 0) || (! (( int ) $sub_block_id) || !$sub_block_exists)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' 	=> $validation_errors,
                'sub_block' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID.',
                'sub_block' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $site_sub_block = $this->site_service->delete_site_sub_block($account_id, $site_id, $sub_block_id);

        if (!empty($site_sub_block)) {
            $message = [
                'status' 			=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'sub_block' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NOT_MODIFIED,
                'message' 	=> $this->session->flashdata('message'),
                'sub_block' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Get list of all Buildings or single record
    */
    public function non_compliant_buildings_get()
    {
        $account_id 	= (int)$this->get('account_id');
        $search_term	= (!empty($this->get('search_term'))) ? trim(urldecode($this->get('search_term'))) : false ;
        $where 			= !empty($this->get('where')) ? $this->get('where') : [];
        $order_by		= !empty($this->get('order_by')) ? $this->get('order_by') : false;
        $limit 			= !empty($this->get('limit')) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset 		= !empty($this->get('offset')) ? (int) $this->get('offset') : DEFAULT_OFFSET;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID.',
                'buildings' => null,
                'counters' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $buildings = $this->site_service->get_non_compliant_buildings($account_id, $search_term, $where, $order_by, $limit, $offset);

        if (!empty($buildings)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'buildings' => (!empty($buildings->records)) ? $buildings->records : (!empty($buildings) ? $buildings : null),
                'counters'  => (!empty($buildings->counters)) ? $buildings->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> $this->session->flashdata('message'),
                'buildings' => null,
                'counters'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Associate Buildings to a User **/
    public function associate_buildings_post()
    {
        $postdata 		= $this->post();
        $account_id 	= !empty($this->post('account_id')) ? ( int ) $this->post('account_id') : false;
        $user_id		= !empty($this->post('user_id')) ? ( int ) $this->post('user_id') : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('user_id', 'User ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 				=> 'Invalid request data: ',
                'associated_buildings' 	=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'	=> 'Invalid main Account ID',
                'associated_buildings' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $associated_buildings = $this->site_service->associate_buildings($account_id, $user_id, $postdata);

        if (!empty($associated_buildings)) {
            $message = [
                'status' 				=> true,
                'http_code' 			=> REST_Controller::HTTP_CREATED,
                'message' 				=> $this->session->flashdata('message'),
                'associated_buildings' 	=> $associated_buildings
            ];
            $this->response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = [
                'status'				=> false,
                'http_code' 			=> REST_Controller::HTTP_NOT_MODIFIED,
                'message'				=> $this->session->flashdata('message'),
                'associated_buildings'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Get a list of linked associated_buildings **/
    public function associated_buildings_get()
    {
        $account_id		= !empty($this->get('account_id')) ? (int) $this->get('account_id') : false;
        $site_id 		= !empty($this->get('site_id')) ? (int) $this->get('site_id') : false;
        $user_id		= !empty($this->get('user_id')) ? (int) $this->get('user_id') : false;
        $where 			= !empty($this->get('where')) ? $this->get('where') : false;

        $this->form_validation->set_data(['account_id'=>$account_id, /*'user_id'=>$user_id*/ ]);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 				=> ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid main Account ID',
                'associated_buildings'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $associated_buildings 	= $this->site_service->get_user_associated_buildings($account_id, $user_id, $site_id, $where);

        if (!empty($associated_buildings)) {
            $message = [
                'status' 				=> true,
                'http_code' 			=> REST_Controller::HTTP_OK,
                'message' 				=> $this->session->flashdata('message'),
                'associated_buildings'	=> $associated_buildings
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_NO_CONTENT,
                'message' 				=> 'Associated Buildings not found',
                'associated_buildings'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Remove a Person from a Primary User **/
    public function disassociate_buildings_post()
    {
        $postdata 		= $this->post();
        $account_id 	= !empty($this->post('account_id')) ? ( int ) $this->post('account_id') : false;
        $user_id		= !empty($this->post('user_id')) ? ( int ) $this->post('user_id') : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('user_id', 'User ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_BAD_REQUEST,
                'message'				=> 'Invalid request data: ',
                'associated_buildings' 	=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'				=> 'Invalid main Account ID',
                'associated_buildings' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $unassociate_buildings = $this->site_service->disassociate_buildings($account_id, $user_id, $postdata);

        if (!empty($unassociate_buildings)) {
            $message = [
                'status' 				=> true,
                'http_code' 			=> REST_Controller::HTTP_OK,
                'message'				=> $this->session->flashdata('message'),
                'associated_buildings' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_BAD_REQUEST,
                'message'				=> $this->session->flashdata('message'),
                'associated_buildings' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Clone Site
    */
    public function clone_site_post()
    {
        $site_data 	= $this->post();
        $account_id = ( int ) $this->post('account_id');
        $site_id 	= ( int ) $this->post('cloned_site_id');

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('cloned_site_id', 'Site ID to clone', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' 	=> 'Invalid data: ',
                'site'		=> null
            ];

            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID.',
                'site'		=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Validate the Site id.
        if ($site_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        $cloned_site = $this->site_service->clone_site($account_id, $site_id, $site_data);

        if (!empty($cloned_site)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'site'		=> $cloned_site
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> $this->session->flashdata('message'),
                'site'		=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
