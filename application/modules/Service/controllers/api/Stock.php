<?php

namespace Application\Modules\Service\Controllers\Api;

use App\Adapter\RESTController;
use Application\Modules\Service\Models\StockModel;

class Stock extends RESTController
{
	/**
	 * @var \Application\Modules\Service\Models\StockModel
	 */
	private $stock_service;

	public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->stock_service = new StockModel();
        $this->form_validation->set_error_delimiters(
			$this->config->item('error_start_delimiter', 'ion_auth'),
			$this->config->item('error_end_delimiter', 'ion_auth')
		);
        $this->lang->load('auth');
    }

    /**
    * Create new Stock resource
    */
    public function create_post()
    {
        $stock_item_data= $this->post();
        $account_id		= (int)$this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('item_name', 'Stock Item Name', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 		=> 'Invalid data: ',
                'stock_item' 	=> null
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
                'stock_item' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_stock_item = $this->stock_service->create_stock_item($account_id, $stock_item_data);

        if (!empty($new_stock_item)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_CREATED,
                'message' 		=> $this->session->flashdata('message'),
                'stock_item' 	=> $new_stock_item
            ];
            $this->response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
                'message' 		=> $this->session->flashdata('message'),
                'stock_item' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Update Stock resource
    */
    public function update_stock_item_post()
    {
        $stock_data = $this->post();
        $item_id 	= !empty($this->post('item_id')) ? (int) $this->post('item_id') : false;
        $account_id = !empty($this->post('account_id')) ? (int) $this->post('account_id') : false;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('item_id', 'Stock Item ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' 	=> 'Invalid data: ',
                'stock_item'=> null
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
                'stock_item'=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if ($item_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        $stock = $this->stock_service->get_stock_items($account_id, false, ["item_id" =>$item_id]);
        if (!$stock) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> $this->session->flashdata('message'),
                'stock_item'=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Run stock update
        $updated_stock_item = $this->stock_service->update_stock_item($account_id, $item_id, $stock_data);
        if (!empty($updated_stock_item)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'stock_item'=> $updated_stock_item
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> $this->session->flashdata('message'),
                'stock_item'=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all Stocks or single record /  Search list
    */
    public function stock_items_get()
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
                'stock_items' 	=> null,
                'counters' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $stock_items = $this->stock_service->get_stock_items($account_id, $search_term, $where, $order_by, $limit, $offset);

        if (!empty($stock_items)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'stock_items' 	=> (!empty($stock_items->records)) ? $stock_items->records : $stock_items,
                'counters' 	=> (!empty($stock_items->counters)) ? $stock_items->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message'	=> $this->session->flashdata('message'),
                'stock_items' 	=> null,
                'counters' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Delete Stock tem resource
    */
    public function delete_stock_item_post()
    {
        $account_id = (int) $this->post('account_id');
        $item_id 	= (int) $this->post('item_id');

        if ($item_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'Invalid main Account ID.',
                'stock_item'=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $delete_stock_item = $this->stock_service->delete_stock_item($account_id, $item_id);

        if (!empty($delete_stock_item)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'stock_item'=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NOT_MODIFIED,
                'message' 	=> $this->session->flashdata('message'),
                'stock' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all stock item types
    */
    public function stock_item_types_get()
    {
        $account_id   = (int) $this->get('account_id');
        $where  	  = $this->get('where');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'stock_item_types' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $stock_types = $this->stock_service->get_stock_item_types($account_id, $where);

        if (!empty($stock_types)) {
            $message = [
                'status' => true,
                'message' => 'Stock item types data found',
                'stock_item_types' => $stock_types
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => 'No records found',
                'stock_item_types' => null
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
                'stock_items' => $billable_items
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'stock_items' => null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }
    }

    /**
    * Get list of all BOMS/SORs or single record /  Search list
    */
    public function bom_items_get()
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
                'bom_items' => null,
                'counters' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $bom_items = $this->stock_service->get_bom_items($account_id, $search_term, $where, $order_by, $limit, $offset);

        if (!empty($bom_items)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'bom_items' => (!empty($bom_items->records)) ? $bom_items->records : $bom_items,
                'counters' 	=> (!empty($bom_items->counters)) ? $bom_items->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message'	=> $this->session->flashdata('message'),
                'bom_items' 	=> null,
                'counters' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    *	Delete BOM item resource
    */
    public function delete_bom_items_post()
    {
        $account_id = (int) $this->post('account_id');
        $item_id 	= (int) $this->post('item_id');

        if ($item_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> 'Invalid main Account ID.',
                'bom_item' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $delete_bom_item = $this->stock_service->delete_bom_item($account_id, $item_id);

        if (!empty($delete_bom_item)) {
            $message = [
                'status' 	=> true,
                'message' 	=> $this->session->flashdata('message'),
                'bom_item' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'message' 	=> $this->session->flashdata('message'),
                'bom_item' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *	Update BOM profile
    */
    public function update_bom_item_post()
    {
        $bom_data 	= $this->post();

        $item_id 	= !empty($this->post('item_id')) ? (int) $this->post('item_id') : false;
        $account_id = !empty($this->post('account_id')) ? (int) $this->post('account_id') : false;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('item_id', 'BOM Item ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' 	=> 'Invalid data: ',
                'bom_items'	=> null
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
                'bom_items'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if ($item_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        $bom = $this->stock_service->get_bom_items($account_id, false, ["item_id" => $item_id]);
        if (!$bom) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> $this->session->flashdata('message'),
                'bom_items'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Run BOM update
        $updated_bom_item = $this->stock_service->update_bom_item($account_id, $item_id, $bom_data);
        if (!empty($updated_bom_item)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'bom_items'	=> $updated_bom_item
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> $this->session->flashdata('message'),
                'bom_items'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *	Create BOM profile
    */
    public function create_bom_item_post()
    {
        $bom_data 	= $this->post();

        $item_name 	= !empty($this->post('item_name')) ? (int) $this->post('item_name') : false;
        $item_code 	= !empty($this->post('item_code')) ? (int) $this->post('item_code') : false;
        $account_id = !empty($this->post('account_id')) ? (int) $this->post('account_id') : false;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('item_name', 'BOM Item Name', 'required');
        $this->form_validation->set_rules('item_code', 'BOM Item Code', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' 	=> 'Invalid data: ',
                'bom_items'=> null
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
                'bom_items'=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_bom_item = $this->stock_service->create_bom_item($account_id, $bom_data);
        if (!empty($new_bom_item)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'bom_items'	=> $new_bom_item
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> $this->session->flashdata('message'),
                'bom_items'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Get BOM Categories **/
    public function bom_categories_get()
    {
        $account_id   	= (int) $this->get('account_id');
        $bom_category_id   	= (int) $this->get('bom_category_id');
        $search_term  	= (!empty($this->get('search_term'))) ? trim(urldecode($this->get('search_term'))) : false ;
        $where 		  	= (!empty($this->get('where'))) ? $this->get('where') : false;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 			=> 'Invalid main Account ID.',
                'bom_categories' 	=> null,
                'counters' 			=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $bom_categories = $this->stock_service->get_bom_categories($account_id, $bom_category_id, $search_term, $where);

        if (!empty($bom_categories)) {
            $message = [
                'status' 			=> true,
                'http_code' 		=> REST_Controller::HTTP_OK,
                'message' 			=> 'BOMs categories data found',
                'bom_categories' 	=> (!empty($bom_categories->records)) ? $bom_categories->records : (!empty($bom_categories) ? $bom_categories : null),
                'counters' 			=> (!empty($bom_categories->counters)) ? $bom_categories->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
                'message' 			=> 'No records found',
                'bom_categories' 	=> null,
                'counters' 			=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Create a BOM Category **/
    public function add_bom_category_post()
    {
        $bom_category_data = $this->post();
        $account_id		   = (int) $this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('bom_category_name', 'Category Name', 'required');
        $this->form_validation->set_rules('bom_category_description', 'Category Description', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 		=> 'Invalid data: ',
                'bom_category' 	=> null
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
                'bom_category' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_bom_category = $this->stock_service->add_bom_category($account_id, $bom_category_data);

        if (!empty($new_bom_category)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_OK,
                'message' 		=> $this->session->flashdata('message'),
                'bom_category' 	=> $new_bom_category
            ];
            $this->response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 		=> $this->session->flashdata('message'),
                'bom_category' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Update BOM Category **/
    public function update_bom_category_post()
    {
        $bom_category_data = $this->post();
        $account_id		  	  = (int) $this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('bom_category_name', 'BOM Category Name', 'required');
        $this->form_validation->set_rules('bom_category_description', 'BOM Category Description', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 		=> 'Invalid data: ',
                'bom_category' 	=> null
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
                'bom_category' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $update_category = $this->stock_service->update_bom_category($account_id, $bom_category_data);

        if (!empty($update_category)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_OK,
                'message' 		=> $this->session->flashdata('message'),
                'bom_category' 	=> $update_category
            ];
            $this->response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NOT_MODIFIED,
                'message' 		=> $this->session->flashdata('message'),
                'bom_category' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Delete  BOM Category Item
    */
    public function delete_bom_category_post()
    {
        $account_id 		= (int) $this->post('account_id');
        $bom_category_id 	= (int) $this->post('bom_category_id');

        if ($bom_category_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
                'message' 		=> 'Invalid main Account ID.',
                'bom_category' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $delete_category = $this->stock_service->delete_bom_category($account_id, $bom_category_id);

        if (!empty($delete_category)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_OK,
                'message' 		=> $this->session->flashdata('message'),
                'bom_category'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NOT_MODIFIED,
                'message' 		=> $this->session->flashdata('message'),
                'bom_category' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
