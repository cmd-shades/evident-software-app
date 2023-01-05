<?php

namespace App\Controllers\REST\Api;

use App\Adapter\RESTController;
use App\Models\Service\CostModel;

final class CostController extends RESTController
{
	/**
	 * @var \Application\Modules\Service\Models\CostModel
	 */
	private $cost_service;

	public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->cost_service = new CostModel();
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->lang->load('auth');
    }

    /**
    * Create new Cost resource
    */
    public function create_post()
    {
        $cost_item_data	= $this->post();
        $account_id		= (int)$this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('cost_item_name', 'Cost Item Name', 'required');
        $this->form_validation->set_rules('cost_item_value', 'Cost Item Value', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'http_code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' => 'Invalid data: ',
                'cost_item' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' => 'Invalid main Account ID.',
                'cost_item' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_cost_item = $this->cost_service->create_cost_item($account_id, $cost_item_data);

        if (!empty($new_cost_item)) {
            $message = [
                'status' => true,
                'http_code' => REST_Controller::HTTP_CREATED,
                'message' => $this->session->flashdata('message'),
                'cost_item' => $new_cost_item
            ];
            $this->response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' => false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' => $this->session->flashdata('message'),
                'cost_item' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Update user resource
    */
    public function update_post()
    {
        $cost_data = $this->post();
        $cost_id 	= (int) $this->post('cost_id');
        $account_id = (int) $this->post('account_id');

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('cost_id', 'Cost ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid data: ',
                'cost' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'cost' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Validate the cost id.
        if ($cost_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        $cost = $this->cost_service->get_costs($account_id, $cost_id);
        if (!$cost) {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'cost' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Run cost update
        $updated_cost = $this->cost_service->update_cost($account_id, $cost_id, $cost_data);
        if (!empty($updated_cost)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'cost' => $updated_cost
            ];
            $this->response($message, REST_Controller::HTTP_OK); // Resource Updated
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'cost' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all Costs or single record /  Search list
    */
    public function cost_items_get()
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
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID.',
                'cost_items' 	=> null,
                'counters' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $cost_items = $this->cost_service->get_cost_items($account_id, $search_term, $where, $order_by, $limit, $offset);

        if (!empty($cost_items)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'cost_items' 	=> (!empty($cost_items->records)) ? $cost_items->records : $cost_items,
                'counters' 	=> (!empty($cost_items->counters)) ? $cost_items->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message'	=> $this->session->flashdata('message'),
                'cost_items' 	=> null,
                'counters' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Delete Cost tem resource
    */
    public function delete_cost_item_get()
    {
        $account_id 	= (int) $this->get('account_id');
        $cost_item_id 	= (int) $this->get('cost_item_id');

        if ($cost_item_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'cost_item' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $delete_cost_item = $this->cost_service->delete_cost_item($account_id, $cost_item_id);

        if (!empty($delete_cost_item)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'cost_item' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'cost' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all cost item types
    */
    public function cost_item_types_get()
    {
        $account_id   = (int) $this->get('account_id');
        $where  	  = $this->get('where');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'cost_item_types' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $cost_types = $this->cost_service->get_cost_item_types($account_id, $where);

        if (!empty($cost_types)) {
            $message = [
                'status' => true,
                'message' => 'Cost item types data found',
                'cost_item_types' => $cost_types
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => 'No records found',
                'cost_item_types' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    public function lookup_get()
    {
        $search_term 	= trim(urldecode($this->get('search_term')));
        $limit 			= (int) $this->get('limit');
        $offset 		= (int) $this->get('offset');
        if (empty($search_term)) {
            //$this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }
        $billable_items = $this->billable_item_service->get_billable_items(false, false, false, $search_term, $offset, $limit);
        if (!empty($billable_items)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'items' => $billable_items
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'items' => null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }
    }
}
