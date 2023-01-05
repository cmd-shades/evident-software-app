<?php

namespace App\Controllers\REST\Api;

use App\Adapter\RESTController;
use App\Models\Service\BillableItemModel;

final class BillableItemController extends RESTController
{
    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->billable_item_service = new BillableItemModel();
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->lang->load('auth');
    }

    /**
    * Create new Item resource
    */
    public function create_post()
    {
        $item_data = $this->post();
        $this->form_validation->set_rules('item_name', 'Item Name', 'required');
        $this->form_validation->set_rules('item_category', 'Item Category', 'required');
        $this->form_validation->set_rules('item_code', 'Item Code', 'required');
        $this->form_validation->set_rules('buy_price', 'Item Buy Price', 'required');
        $this->form_validation->set_rules('sell_price', 'Item Sell Price', 'required');
        $this->form_validation->set_rules('item_supplier', 'Item Supplier', 'required');


        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid Item data: ',
                'item' => null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_item = $this->billable_item_service->create_billable_item($item_data);

        if (!empty($new_item)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'item' => $new_item
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'item' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Update user resource
    */
    public function update_post()
    {
        $item_data	= $this->post();
        $item_id 	= (int) $this->post('item_id');
        $this->form_validation->set_rules('item_name', 'Item Name', 'required');
        $this->form_validation->set_rules('item_category', 'Item Category', 'required');
        $this->form_validation->set_rules('item_code', 'Item Code', 'required');
        $this->form_validation->set_rules('buy_price', 'Item Buy Price', 'required');
        $this->form_validation->set_rules('sell_price', 'Item Sell Price', 'required');
        $this->form_validation->set_rules('item_supplier', 'Item Supplier', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid Item data: ',
                'item' => null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }


        ## Validate the item id.
        if ($item_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        $item = $this->billable_item_service->get_billable_items($item_id);
        if (!$item) {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'item' => null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }

        ## Run item update
        $updated_item = $this->billable_item_service->update_billable_item($item_id, $item_data);
        if (!empty($updated_item)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'item' => $updated_item
            ];
            $this->response($message, REST_Controller::HTTP_OK); // Resource Updated
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'item' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all Items or single record
    */
    public function items_get()
    {
        $item_id 		= (int) $this->get('item_id');
        $item_code 		= $this->get('item_code');
        $item_category 	= $this->get('item_category');
        $item_supplier 	= $this->get('item_supplier');
        $item_search 	= $this->get('search_term');
        $offset 		= (int) $this->get('offset');
        $limit 			= (int) $this->get('limit');
        $order_by		= (!empty($this->input->get('order_by'))) ? ($this->input->get('order_by')) : false ;



        $items 			= $this->billable_item_service->get_billable_items($item_id, $item_code, $item_category, $item_search, $offset, $limit, $order_by);

        if (!empty($items)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'items' => $items
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

    /**
    * Delete Item resource
    */
    public function delete_get()
    {
        $item_id 	= (int) $this->get('item_id');
        if ($item_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }
        $delete_item = $this->billable_item_service->delete_billable_items($item_id);
        if (!empty($delete_item)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'items' => null
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
