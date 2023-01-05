<?php

namespace App\Controllers\REST\Api;

use App\Adapter\RESTController;
use App\Models\Service\FleetModel;

final class FleetController extends RESTController
{
	/**
	 * @var \Application\Modules\Service\Models\FleetModel
	 */
	private $Fleet_service;

	public function __construct()
    {
        parent::__construct();
        $this->load->library("Ssid_common");
        $this->load->library("form_validation");
        $this->load->library("email");
        $this->Fleet_service = new FleetModel();
    }

    /**
    * 	Get Vehicles List(s)
    */
    public function vehicles_get()
    {
        $postset 		= $this->get();

        $account_id 	= (!empty($postset['account_id'])) ? $postset['account_id'] : false ;
        $vehicle_id 	= (!empty($postset['vehicle_id'])) ? $postset['vehicle_id'] : false ;
        $vehicle_reg 	= (!empty($postset['vehicle_reg'])) ? $postset['vehicle_reg'] : false ;
        $vehicle_barcode= (!empty($postset['vehicle_barcode'])) ? $postset['vehicle_barcode'] : false ;
        $where 			= (!empty($postset['where'])) ? $postset['where'] : false ;
        $limit 			= (!empty($postset['limit'])) ? $postset['limit'] : DEFAULT_LIMIT ;
        $offset 		= (!empty($postset['offset'])) ? $postset['offset'] : false ;

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
                'status' 		=> false,
                'message' 		=> 'Invalid or missing Field(s)',
                'vehicles' 		=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> $this->session->flashdata('message'),
                'vehicles' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $vehicles = $this->Fleet_service->get_vehicles($account_id, $vehicle_id, $vehicle_reg, $vehicle_barcode, $where, $limit, $offset);

        if (!empty($vehicles)) {
            $message = [
                'status' 		=> true,
                'message' 		=> $this->session->flashdata('message'),
                'vehicles' 		=> $vehicles
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'vehicles' 		=> false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * 	Add new vehicle
    */
    public function create_post()
    {
        $post_data 			= $this->post();
        $new_vehicle		= false;
        $vehicle_reg 		= (!empty($post_data['vehicle_reg'])) ? $post_data['vehicle_reg'] : false ;
        $account_id 		= (!empty($post_data['account_id'])) ? $post_data['account_id'] : false ;
        unset($post_data['account_id']);


        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('vehicle_reg', 'Vehicle Registration No', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid or missing Field(s)',
                'new_vehicle' 		=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'new_vehicle' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_vehicle = $this->Fleet_service->create_vehicle($account_id, $post_data);

        if (!empty($new_vehicle)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'new_vehicle' 		=> $new_vehicle
            ];

            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'new_vehicle' 		=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * 	Update Vehicle Profile data
    */
    public function update_post()
    {
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('vehicle_id', 'Vehicle ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            ## One of the required fields is invalid
            $message = [
                'status' 		=> false,
                'message' 		=> 'Invalid or missing Field(s)',
                'contract' 		=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $postset 		= $this->post();
        $account_id 	= (!empty($postset['account_id'])) ? $postset['account_id'] : false ;
        unset($postset['account_id']);
        $vehicle_id 	= (!empty($postset['vehicle_id'])) ? ( int ) $postset['vehicle_id'] : false ;
        unset($postset['vehicle_id']);

        if ($vehicle_id <= 0) {  ## or is not a number
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'updated_vehicle' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $vehicle = $this->Fleet_service->get_vehicles($account_id, $vehicle_id);

        if (!$vehicle) {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'updated_vehicle' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }


        if (!$postset) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'No Update Data provided',
                'updated_vehicle' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }

        $updated_vehicle = $this->Fleet_service->update($account_id, $vehicle_id, $postset);

        if (!empty($updated_vehicle)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'updated_vehicle' 	=> $updated_vehicle
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'updated_vehicle' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    * 	Delete Vehicle Profile data
    */
    public function delete_post()
    {
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('vehicle_id', 'Vehicle ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            ## One of the required fields is invalid
            $message = [
                'status' 		=> false,
                'message' 		=> 'Invalid or missing Field(s)',
                'profile' 		=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $postset 		= $this->post();
        $account_id 	= (!empty($postset['account_id'])) ? $postset['account_id'] : false ;
        unset($postset['account_id']);
        $vehicle_id 	= (!empty($postset['vehicle_id'])) ? ( int ) $postset['vehicle_id'] : false ;
        unset($postset['vehicle_id']);

        if ($vehicle_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'vehicle_deleted' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $vehicle = $this->Fleet_service->get_vehicles($account_id, $vehicle_id);

        if (!$vehicle) {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'vehicle_deleted' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $vehicle_deleted = $this->Fleet_service->delete_vehicle($account_id, $vehicle_id);

        if (!empty($vehicle_deleted)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'vehicle_deleted' 	=> $vehicle_deleted
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'vehicle_deleted' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    * 	Assign Driver to the Vehicle
    *	pd_end_date 	-> previous_driver_end_date
    * 	start_date 		-> new_driver_start_date
    */
    public function assign_driver_to_vehicle_post()
    {
        $postset 		= $this->post();

        $account_id 	= (!empty($postset['account_id'])) ? $postset['account_id'] : false;
        $vehicle_id 	= (!empty($postset['vehicle_id'])) ? $postset['vehicle_id'] : false;
        $driver_id 		= (!empty($postset['driver_id'])) ? $postset['driver_id'] : false;
        $note 			= (!empty($postset['note'])) ? $postset['note'] : false;
        $pd_end_date	= (!empty($postset['pd_end_date'])) ? $postset['pd_end_date'] : false;
        $nd_start_date 	= (!empty($postset['nd_start_date'])) ? $postset['nd_start_date'] : false;
        $audit_id 		= (!empty($postset['audit_id'])) ? $postset['audit_id'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('vehicle_id', 'Vehicle ID', 'required');
        $this->form_validation->set_rules('driver_id', 'Driver ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid or missing Field(s): '.trim($validation_errors),
                'assigned_driver' 	=> null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'assigned_driver' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $assigned_driver = $this->Fleet_service->assign_driver_to_vehicle($account_id, $vehicle_id, $driver_id, $note, $pd_end_date, $nd_start_date, $audit_id);

        if (!empty($assigned_driver)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'assigned_driver' 	=> $assigned_driver
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'assigned_driver' 		=> false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    * 	Remove the Driver from the Vehicle
    */
    public function remove_driver_from_vehicle_post()
    {
        $postset 		= $this->post();

        $account_id 	= (!empty($postset['account_id'])) ? $postset['account_id'] : false;
        $vehicle_id 	= (!empty($postset['vehicle_id'])) ? $postset['vehicle_id'] : false;
        $driver_id 		= (!empty($postset['driver_id'])) ? $postset['driver_id'] : false;
        $note 			= (!empty($postset['note'])) ? $postset['note'] : false;
        $end_date 		= (!empty($postset['end_date'])) ? $postset['end_date'] : false;
        $audit_id 		= (!empty($postset['audit_id'])) ? $postset['audit_id'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('vehicle_id', 'Vehicle ID', 'required');
        $this->form_validation->set_rules('driver_id', 'Driver ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid or missing Field(s): '.trim($validation_errors),
                'removed_driver' 	=> null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'removed_driver' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $removed_driver = $this->Fleet_service->remove_driver_from_vehicle($account_id, $vehicle_id, $driver_id, $note, $end_date, $audit_id);

        if (!empty($removed_driver)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'removed_driver' 	=> $removed_driver
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'removed_driver' 	=> false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *	Search Vehicle by: vehicle_reg, make, model, year, driver name
    */
    public function lookup_get()
    {
        $dataset 		= $this->get();

        $account_id 	= (!empty($dataset['account_id'])) ? $dataset['account_id'] : false ;
        $where 		 	= $this->get('where');
        $order_by    	= $this->get('order_by');
        $limit 		 	= (int) $this->get('limit');
        $offset 	 	= (int) $this->get('offset');
        $vehicle_statuses= $this->get('vehicle_statuses');
        $search_term 	= trim(urldecode($this->get('search_term')));

        $expected_data = [
            'account_id' 	=> $account_id,
            //'search_term' 	=> $search_term
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        //$this->form_validation->set_rules( 'search_term', 'Search Term', 'required' );

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 		=> false,
                'message' 		=> 'Invalid or missing Field(s)',
                'vehicles' 		=> null
            ];
            $message['message'] =  trim($message['message']).trim($validation_errors);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid main Account ID',
                'vehicles' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $get_vehicles = $this->Fleet_service->vehicle_lookup($account_id, $search_term, $vehicle_statuses, $where, $order_by, $limit, $offset);

        if (!empty($get_vehicles)) {
            $message = [
                'status' 	=> true,
                'message' 	=> $this->session->flashdata('message'),
                'vehicles' 	=> $get_vehicles
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'message' 	=> $this->session->flashdata('message'),
                'vehicles' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /*
    *	Get Vehicle Diver change Logs
    */
    public function vehicle_driver_change_logs_get()
    {
        $dataset 		= $this->get();

        $account_id 	= (!empty($dataset['account_id'])) ? $dataset['account_id'] : false ;
        $vehicle_id 	= (!empty($dataset['vehicle_id'])) ? $dataset['vehicle_id'] : false ;
        $vehicle_reg 	= (!empty($dataset['vehicle_reg'])) ? $dataset['vehicle_reg'] : false ;
        $limit 			= (!empty($dataset['limit'])) ? $dataset['limit'] : false ;
        $offset 		= (!empty($dataset['offset'])) ? $dataset['offset'] : false ;

        $expected_data = [
            'account_id' 	=> $account_id,
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        $validation_errors = '';
        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (empty($vehicle_id) && empty($vehicle_reg)) {
            $validation_errors .= "Vehicle ID or Vehicle Reg must be provided.";
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid or missing Field(s): '.trim($validation_errors),
                'vehicle_dc_logs' 	=> null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid main Account ID',
                'vehicle_dc_logs' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $vehicle_dc_logs = $this->Fleet_service->get_fleet_driver_history_log($account_id, $vehicle_id, $vehicle_reg, $limit, $offset);

        if (!empty($vehicle_dc_logs)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'vehicle_dc_logs' 	=> $vehicle_dc_logs
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'vehicle_dc_logs' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * 	Get Vehicle Statuses
    */
    public function vehicle_statuses_get()
    {
        $postset 		= $this->get();

        $account_id 	= (!empty($postset['account_id'])) ? $postset['account_id'] : false;
        $status_id 		= (!empty($postset['status_id'])) ? $postset['status_id'] : false;
        $ordered 		= (!empty($postset['ordered'])) ? $postset['ordered'] : false;

        $expected_data = [
            'account_id' 	=> $account_id ,
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 		=> false,
                'message' 		=> 'Invalid or missing Field(s)',
                'statuses' 		=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> $this->session->flashdata('message'),
                'statuses' 	=> null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $statuses = $this->Fleet_service->get_vehicle_statuses($account_id, $status_id, $ordered);

        if (!empty($statuses)) {
            $message = [
                'status' 		=> true,
                'message' 		=> $this->session->flashdata('message'),
                'statuses' 		=> $statuses
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'statuses' 		=> false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * 	Get Vehicle Suppliers
    */
    public function vehicle_suppliers_get()
    {
        $postset 		= $this->get();

        $account_id 	= (!empty($postset['account_id'])) ? $postset['account_id'] : false;
        $supplier_id 	= (!empty($postset['supplier_id'])) ? $postset['supplier_id'] : false;
        $ordered 		= (!empty($postset['ordered'])) ? $postset['ordered'] : false;

        $expected_data = [
            'account_id' 	=> $account_id ,
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 		=> false,
                'message' 		=> 'Invalid or missing Field(s)',
                'suppliers' 	=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> $this->session->flashdata('message'),
                'suppliers' => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $suppliers = $this->Fleet_service->get_vehicle_suppliers($account_id, $supplier_id, $ordered);

        if (!empty($suppliers)) {
            $message = [
                'status' 		=> true,
                'message' 		=> $this->session->flashdata('message'),
                'suppliers' 	=> $suppliers
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'suppliers' 	=> false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * 	Add new vehicle event
    */
    public function create_vehicle_event_post()
    {
        $vehicle_id = $new_event = $post_data = $account_id = $vehicle_exists = false;

        $post_data 			= $this->post();
        $vehicle_id 		= (!empty($post_data['vehicle_id'])) ? $post_data['vehicle_id'] : $vehicle_id ;
        unset($post_data['vehicle_id']);
        $account_id 		= (!empty($post_data['account_id'])) ? $post_data['account_id'] : $account_id ;
        unset($post_data['account_id']);

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('vehicle_id', 'Vehicle ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid or missing Field(s)',
                'new_event' 		=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'new_event' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $vehicle_exists = $this->Fleet_service->get_vehicles($account_id, $vehicle_id);
        if (!$vehicle_exists) {
            $message = [
                'status' 		=> false,
                'message' 		=> "Invalid Vehicle ID",
                'new_event' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_event = $this->Fleet_service->create_vehicle_event($account_id, $vehicle_id, $post_data);

        if (!empty($new_event)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'new_event' 		=> $new_event
            ];

            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'new_event' 		=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * 	Get Vehicles Event(s)
    */
    public function vehicle_events_get()
    {
        $postset 		= $this->get();

        $account_id 		= (!empty($postset['account_id'])) ? $postset['account_id'] : false ;
        $event_id 			= (!empty($postset['event_id'])) ? $postset['event_id'] : false ;
        $vehicle_id 		= (!empty($postset['vehicle_id'])) ? $postset['vehicle_id'] : false ;
        $vehicle_reg 		= (!empty($postset['vehicle_reg'])) ? $postset['vehicle_reg'] : false ;
        $where 				= (!empty($postset['where'])) ? $postset['where'] : false ;
        $limit 				= (!empty($postset['limit'])) ? $postset['limit'] : DEFAULT_LIMIT ;
        $offset 			= (!empty($postset['offset'])) ? $postset['offset'] : false ;

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
                'status' 			=> false,
                'message' 			=> 'Validation errors: '.trim($validation_errors),
                'vehicle_events' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'vehicle_events' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $vehicle_events = $this->Fleet_service->get_vehicle_events($account_id, $event_id, $vehicle_id, $vehicle_reg, $where, $limit, $offset);

        if (!empty($vehicle_events)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'vehicle_events' 	=> $vehicle_events
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'vehicle_events' 	=> null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    * 	Get Vehicle Event Statuses
    */
    public function event_statuses_get()
    {
        $postset 		= $this->get();

        $account_id 		= (!empty($postset['account_id'])) ? $postset['account_id'] : false;
        $event_status_id 	= (!empty($postset['event_status_id'])) ? $postset['event_status_id'] : false;
        $ordered 			= (!empty($postset['ordered'])) ? $postset['ordered'] : false;

        $expected_data = [
            'account_id' 	=> $account_id ,
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid or missing Field(s)',
                'event_statuses' 	=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'event_statuses' 	=> null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $event_statuses = $this->Fleet_service->get_vehicle_event_statuses($account_id, $event_status_id, $ordered);

        if (!empty($event_statuses)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'event_statuses' 	=> $event_statuses
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'event_statuses' 	=> false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    * 	Get Vehicle Event Categories
    */
    public function event_categories_get()
    {
        $postset 		= $this->get();

        $account_id 		= (!empty($postset['account_id'])) ? $postset['account_id'] : false;
        $category_id 		= (!empty($postset['category_id'])) ? $postset['category_id'] : false;
        $ordered 			= (!empty($postset['ordered'])) ? $postset['ordered'] : false;

        $expected_data = [
            'account_id' 	=> $account_id ,
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid or missing Field(s)',
                'event_categories' 	=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'event_categories' 	=> null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $event_categories = $this->Fleet_service->get_vehicle_event_categories($account_id, $category_id, $ordered);

        if (!empty($event_categories)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'event_categories' 	=> $event_categories
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'event_categories' 	=> false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * 	Get Vehicle Event Types
    */
    public function event_types_get()
    {
        $postset 		= $this->get();

        $account_id 		= (!empty($postset['account_id'])) ? $postset['account_id'] : false;
        $event_type_id 		= (!empty($postset['event_type_id'])) ? $postset['event_type_id'] : false;
        $event_category_id 	= (!empty($postset['event_category_id'])) ? $postset['event_category_id'] : false;
        $ordered 			= (!empty($postset['ordered'])) ? $postset['ordered'] : false;

        $expected_data = [
            'account_id' 	=> $account_id ,
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid or missing Field(s)',
                'event_types' 		=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'event_types' 	=> null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $event_types = $this->Fleet_service->get_vehicle_event_types($account_id, $event_type_id, $event_category_id, $ordered);

        if (!empty($event_types)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'event_types' 		=> $event_types
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'event_types' 	=> false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * 	Get Vehicle Event Types
    */
    public function vehicle_drivers_get()
    {
        $postset 		= $this->get();

        $account_id 	= (!empty($postset['account_id'])) ? $postset['account_id'] : false;
        $driver_id 		= (!empty($postset['driver_id'])) ? $postset['driver_id'] : false;
        $ordered 		= (!empty($postset['ordered'])) ? $postset['ordered'] : false;

        $expected_data = [
            'account_id' 	=> $account_id ,
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid or missing Field(s)',
                'vehicle_drivers' 	=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'vehicle_drivers' 	=> null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $vehicle_drivers = $this->Fleet_service->get_vehicle_drivers($account_id, $driver_id, $ordered);

        if (!empty($vehicle_drivers)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'vehicle_drivers' 	=> $vehicle_drivers
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'vehicle_drivers' 	=> false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * 	Get Vehicle Change Log(s)
    */
    public function vehicle_change_logs_get()
    {
        $postset 		= $this->get();

        $account_id 	= (!empty($postset['account_id'])) ? $postset['account_id'] : false;
        $change_log_id 	= (!empty($postset['change_log_id'])) ? $postset['change_log_id'] : false;
        $vehicle_id 	= (!empty($postset['vehicle_id'])) ? $postset['vehicle_id'] : false;
        $vehicle_reg 	= (!empty($postset['vehicle_reg'])) ? $postset['vehicle_reg'] : false ;
        $where 			= (!empty($postset['where'])) ? $postset['where'] : false ;
        $limit 			= (!empty($postset['limit'])) ? $postset['limit'] : DEFAULT_LIMIT ;
        $offset 		= (!empty($postset['offset'])) ? $postset['offset'] : false ;


        $expected_data = [
            'account_id' 	=> $account_id ,
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 				=> false,
                'message' 				=> 'Invalid or missing Field(s)',
                'vehicle_change_logs' 	=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'vehicle_change_logs' => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $vehicle_change_logs = $this->Fleet_service->get_vehicle_change_log($account_id, $change_log_id, $vehicle_id, $vehicle_reg, $where, $limit, $offset);

        if (!empty($vehicle_change_logs)) {
            $message = [
                'status' 				=> true,
                'message' 				=> $this->session->flashdata('message'),
                'vehicle_change_logs' 	=> $vehicle_change_logs
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 				=> false,
                'message' 				=> $this->session->flashdata('message'),
                'vehicle_change_logs' 	=> false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * 	Get Simple Stats List(s)
    */
    public function simple_stats_get()
    {
        $postset 		= $this->get();

        $account_id 	= (!empty($postset['account_id'])) ? $postset['account_id'] : false ;

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
                'status' 		=> false,
                'message' 		=> 'Invalid or missing Field(s)',
                'simple_stats' 	=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'simple_stats' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $simple_stats = $this->Fleet_service->get_simple_stats($account_id);

        if (!empty($simple_stats)) {
            $message = [
                'status' 		=> true,
                'message' 		=> $this->session->flashdata('message'),
                'simple_stats' 	=> $simple_stats
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'simple_stats' 	=> false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *	Get Vehicle Event Tracker Logs
    */
    public function event_tracking_logs_get()
    {
        $dataset 		= $this->get();

        $account_id 			= (!empty($dataset['account_id'])) ? $dataset['account_id'] : false ;
        $event_tracking_log_id 	= (!empty($dataset['log_id'])) ? $dataset['log_id'] : false ;
        $event_id 				= (!empty($dataset['event_id'])) ? $dataset['event_id'] : false ;
        $vehicle_id 			= (!empty($dataset['vehicle_id'])) ? $dataset['vehicle_id'] : false ;
        $vehicle_reg 			= (!empty($dataset['vehicle_reg'])) ? $dataset['vehicle_reg'] : false ;
        $limit 					= (!empty($dataset['limit'])) ? $dataset['limit'] : false ;
        $offset 				= (!empty($dataset['offset'])) ? $dataset['offset'] : false ;

        $expected_data = [
            'account_id' 	=> $account_id,
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        $validation_errors = '';
        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid or missing Field(s): '.trim($validation_errors),
                'event_tracking_logs' 	=> null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 				=> false,
                'message' 				=> ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid main Account ID',
                'event_tracking_logs' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $event_tracking_logs = $this->Fleet_service->get_event_tracking_logs($account_id, $event_tracking_log_id, $event_id, $vehicle_id, $vehicle_reg, $limit, $offset);

        if (!empty($event_tracking_logs)) {
            $message = [
                'status' 				=> true,
                'message' 				=> $this->session->flashdata('message'),
                'event_tracking_logs' 	=> $event_tracking_logs
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 				=> false,
                'message' 				=> $this->session->flashdata('message'),
                'event_tracking_logs' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *	The function to add a log to the Event tracking section
    *	Required: account_id, event_id, log_note
    */
    public function add_event_tracking_log_post()
    {
        $event_id = $log_note = $post_data = $account_id = false;

        $post_data 			= $this->post();

        $event_id 		= (!empty($post_data['event_id'])) ? $post_data['event_id'] : false ;
        unset($post_data['event_id']);

        $log_note 		= (!empty($post_data['log_note'])) ? $post_data['log_note'] : false ;
        unset($post_data['log_note']);

        $account_id 		= (!empty($post_data['account_id'])) ? $post_data['account_id'] : $account_id ;
        unset($post_data['account_id']);

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('event_id', 'Event ID', 'required');
        $this->form_validation->set_rules('log_note', 'Log Note', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid or missing Field(s)',
                'new_log_note' 		=> null
            ];
            $message['message'] = 'Validation errors: '.trim($validation_errors);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'new_log_note' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $event_exists = $this->Fleet_service->get_vehicle_events($account_id, $event_id);

        if (!$event_exists) {
            $message = [
                'status' 		=> false,
                'message' 		=> "Invalid Event ID",
                'new_log_note' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_log_note = $this->Fleet_service->add_event_tracking_log($account_id, $event_id, $log_note);

        if (!empty($new_log_note)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'new_log_note' 		=> $new_log_note
            ];

            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'new_log_note' 		=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /*
    *	This is to update the vehicle event item.
    *	An initial version will update just the status
    *	The parameters need to be provided in 'data' object
    */
    public function update_vehicle_event_post()
    {
        $event_id = $post_data = $account_id = false;
        $post_data 		= $this->post();

        $event_id 		= (!empty($post_data['event_id'])) ? $post_data['event_id'] : false ;
        unset($post_data['event_id']);

        $data 			= (!empty($post_data['data'])) ? json_decode($post_data['data']) : false ;
        unset($post_data['data']);

        $account_id 	= (!empty($post_data['account_id'])) ? $post_data['account_id'] : $account_id ;
        unset($post_data['account_id']);

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('event_id', 'Event ID', 'required');
        $this->form_validation->set_rules('data', 'Update Data', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid or missing Field(s)',
                'updated_event' 	=> null
            ];
            $message['message'] = 'Validation errors: '.trim($validation_errors);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'updated_event' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $event_exists = $this->Fleet_service->get_vehicle_events($account_id, $event_id);

        if (!$event_exists) {
            $message = [
                'status' 		=> false,
                'message' 		=> "Invalid Event ID",
                'updated_event' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $updated_event = $this->Fleet_service->update_vehicle_event($account_id, $event_id, $data);

        if (!empty($updated_event)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'updated_event' 	=> $updated_event
            ];

            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'updated_event' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
