<?php

namespace App\Controllers\REST\Api;

use App\Adapter\RESTController;
use App\Models\Service\PremisesModel;

final class PremisesController extends RESTController
{
	/**
	 * @var \Application\Modules\Service\Models\PremisesModel
	 */
	private $premises_service;

	public function __construct()
    {
        parent::__construct();
        $this->premises_service = new PremisesModel();
        $this->form_validation->set_error_delimiters(
			$this->config->item('error_start_delimiter', 'ion_auth'),
			$this->config->item('error_end_delimiter', 'ion_auth')
		);
        $this->lang->load('auth');
    }

    /**
    * Create a New  Premises record
    */
    public function create_premises_post()
    {
        $premises_data 	= $this->post();
        $account_id	 	= (int)$this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('premises_type_id', 'Premises Type', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' 	=> 'Invalid data: ',
                'premises' 	=> null
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
                'premises' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_premises = $this->premises_service->create_premises($account_id, $premises_data);

        if (!empty($new_premises)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'premises' 	=> $new_premises
            ];
            $this->response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> $this->session->flashdata('message'),
                'premises' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Update premises resource
    */
    public function update_premises_post()
    {
        $premises_data 	= $this->post();
        $premises_id 	= (int) $this->post('premises_id');
        $account_id 	= (int) $this->post('account_id');

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('premises_id', 'Premises ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' 	=> 'Invalid data: ',
                'premises' 	=> null
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
                'premises' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Validate the premises id.
        if ($premises_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        $premises = $this->premises_service->get_premises($account_id, $premises_id);
        if (!$premises) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> $this->session->flashdata('message'),
                'premises' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Run premises update
        $updated_premises = $this->premises_service->update_premises($account_id, $premises_id, $premises_data);
        if (!empty($updated_premises)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'premises' 	=> $updated_premises
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> $this->session->flashdata('message'),
                'premises' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Get list of all Premisess or single record version 2
    */
    public function premises_get()
    {
        $account_id			= ($this->get('account_id')) ? (int) $this->get('account_id') : false;
        $premises_id		= ($this->get('premises_id')) ? (int) $this->get('premises_id') : false;
        $premises_unique_id	= ($this->get('premises_unique_id')) ? $this->get('premises_unique_id') : false;
        $where		 		= ($this->get('where')) ? $this->get('where') : false;
        $order_by		 	= ($this->get('order_by')) ? $this->get('order_by') : false;
        $limit		 		= ($this->get('limit')) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset	 			= ($this->get('offset')) ? (int) $this->get('offset') : DEFAULT_OFFSET;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'premises' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $premises 		= $this->premises_service->get_premises($account_id, $premises_id, $premises_unique_id, $where, $order_by, $limit, $offset);

        if (!empty($premises)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'premises' 	=> $premises
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> $this->session->flashdata('message'),
                'premises' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Search through list of Premises
    */
    public function premises_lookup_get()
    {
        $account_id 		= (int) $this->get('account_id');
        $site_id 			= (int) $this->get('site_id');
        $where 		 		= (!empty($this->get('where'))) ? $this->get('where') : [];
        $order_by 			= (!empty($this->get('order_by'))) ? $this->get('order_by') : false;
        $limit 		 		= !empty($this->get('limit')) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset 			= !empty($this->get('offset')) ? (int) $this->get('offset') : DEFAULT_OFFSET;
        $premises_types 	= $this->get('premises_types');
        $search_term 		= trim(urldecode($this->get('search_term')));

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID.',
                'premises' 	=> null,
                'counters' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Add Site ID to the where
        if (!empty($site_id)) {
            $where = convert_to_array($where);
            $where['premises.site_id'] = $site_id;
        }

        $premises_lookup = $this->premises_service->premises_lookup($account_id, $search_term, $premises_types, $where, $order_by, $limit, $offset);
        ;

        if (!empty($premises_lookup)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'premises' 	=> (!empty($premises_lookup->records)) ? $premises_lookup->records : (!empty($premises_lookup) ? $premises_lookup : false),
                'counters'  => (!empty($premises_lookup->counters)) ? $premises_lookup->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> $this->session->flashdata('message'),
                'premises' 	=> null,
                'counters' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Delete Premises record
    */
    public function delete_get()
    {
        $account_id 	= (int) $this->get('account_id');
        $premises_id 	= (int) $this->get('premises_id');

        if ($premises_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID.',
                'premises' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $delete_premises = $this->premises_service->delete_premises($account_id, $premises_id);

        if (!empty($delete_premises)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'premises' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> $this->session->flashdata('message'),
                'premises' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Add a Premises Type
    **/
    public function add_premises_type_post()
    {
        $premises_type_data = $this->post();
        $account_id		 	= intval($this->post('account_id'));
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('premises_type', 'Premises Type', 'required');
        $this->form_validation->set_rules('discipline_id', 'Discipline', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 		=> 'Invalid data: ',
                'premises_type' => null
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
                'premises_type' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_premises_type = $this->premises_service->add_premises_type($account_id, $premises_type_data);

        if (!empty($new_premises_type)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_OK,
                'message' 		=> $this->session->flashdata('message'),
                'premises_type' => $new_premises_type
            ];
            $this->response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
                'message' 		=> $this->session->flashdata('message'),
                'premises_type' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Edit/ Update a Premises Type
    **/
    public function update_premises_type_post()
    {
        $premises_type_data = $this->post();
        $account_id		 	= (int) $this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        // $this->form_validation->set_rules('discipline_id', 'Discipline ID', 'required' );
        $this->form_validation->set_rules('premises_type', 'Premises Type', 'required');
        $this->form_validation->set_rules('premises_type_id', 'Premises Type ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 		=> 'Invalid data: ',
                'premises_type'	=> null
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
                'premises_type'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $updated_premises_type = $this->premises_service->update_premises_type($account_id, $premises_type_data);

        if (!empty($updated_premises_type->records)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_OK,
                'message' 		=> $this->session->flashdata('message'),
                'premises_type'	=> $updated_premises_type->records
            ];
            $this->response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
                'message' 		=> $this->session->flashdata('message'),
                'premises_type'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Get list of all Premises types
    */
    public function premises_types_get()
    {
        $account_id 	= (!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false ;
        $premises_type_id 	= (!empty($this->get('premises_type_id'))) ? (int) $this->get('premises_type_id') : false ;
        $search_term	= (!empty($this->get('search_term'))) ? trim(urldecode($this->get('search_term'))) : false ;
        $where 		 	= (!empty($this->get('where'))) ? $this->get('where') : false ;
        $order_by 		= (!empty($this->get('order_by'))) ? $this->get('order_by') : false ;
        $limit		 	= ($this->get('limit')) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset	 		= (!empty($this->get('offset'))) ? (int) $this->get('offset') : 0 ;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 		=> 'Invalid main Account ID.',
                'premises_types' 	=> null,
                'counters' 		=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
        $premises_types = $this->premises_service->get_premises_types($account_id, $premises_type_id, $search_term, $where, $limit, $offset);

        if (!empty($premises_types)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_OK,
                'message' 		=> $this->session->flashdata('message'),
                'premises_types'=> (!empty($premises_types->records)) ? $premises_types->records : null,
                'counters' 		=> (!empty($premises_types->counters)) ? $premises_types->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
                'message'		=> $this->session->flashdata('message'),
                'premises_types'=> null,
                'counters' 		=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Delete Premise Type
    */
    public function delete_premises_type_post()
    {
        $postdata 			= $this->post();
        $account_id 		= (!empty($postdata['account_id'])) ? intval($postdata['account_id']) : false ;
        $premises_type_id 	= (!empty($postdata['premises_type_id'])) ? intval($postdata['premises_type_id']) : false ;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('premises_type_id', 'Premise Type ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 		=> $validation_errors,
                'premises_type' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 		=> 'Invalid main Account ID.',
                'premises_type' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $premises_type = $this->premises_service->delete_premises_type($account_id, $premises_type_id);

        if (!empty($premises_type)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_OK,
                'message' 		=> $this->session->flashdata('message'),
                'premises_type' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
                'message' 		=> $this->session->flashdata('message'),
                'premises_type' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *	Get list of all Premises Type Attributes
    */
    public function premises_type_attributes_get()
    {
        $account_id 		= (!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false ;
        $premises_type_id 	= (!empty($this->get('premises_type_id'))) ? (int) $this->get('premises_type_id') : false ;
        $attribute_id 		= (!empty($this->get('attribute_id'))) ? (int) $this->get('attribute_id') : false ;
        $search_term		= (!empty($this->get('search_term'))) ? trim(urldecode($this->get('search_term'))) : false ;
        $where 		 		= (!empty($this->get('where'))) ? $this->get('where') : false ;
        $order_by 			= (!empty($this->get('order_by'))) ? $this->get('order_by') : false ;
        $limit		 		= ($this->get('limit')) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset	 			= (!empty($this->get('offset'))) ? (int) $this->get('offset') : 0 ;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 		=> 'Invalid main Account ID.',
                'attributes' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $premises_type_attributes = $this->premises_service->get_premises_type_attributes($account_id, $premises_type_id, $attribute_id, $search_term, $where, $limit, $offset, $order_by);

        if (!empty($premises_type_attributes)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_OK,
                'message' 		=> $this->session->flashdata('message'),
                'attributes'	=> (!empty($premises_type_attributes)) ? $premises_type_attributes : null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
                'message'		=> $this->session->flashdata('message'),
                'attributes'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Get List of Premise Type Attribute Response Types **/
    public function response_types_get()
    {
        $account_id   = (int) $this->get('account_id');
        $where 		  = $this->get('where');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'status' 		=> false,
                'message' 		=> 'Invalid main Account ID.',
                'response_types'=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $response_types = $this->premises_service->get_response_types($account_id, $where);

        if (!empty($response_types)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_OK,
                'message' 		=> 'Attributes Response types found',
                'response_types'=> $response_types
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
                'message' 		=> 'No records found',
                'response_types'=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *	Delete Premises Type Attribute
    */
    public function delete_premises_type_attribute_post()
    {
        $postdata 			= $this->post();

        $account_id 		= (!empty($postdata['account_id'])) ? intval($postdata['account_id']) : false ;
        $premises_type_id 	= (!empty($postdata['premises_type_id'])) ? intval($postdata['premises_type_id']) : false ;
        $attribute_id 		= (!empty($postdata['attribute_id'])) ? intval($postdata['attribute_id']) : false ;
        $source				= (!empty($postdata['source'])) ? $postdata['source'] : false ;

        if (!empty($source) && (strtolower($source) == "type_attribute_profile")) {
        } else {
            $this->form_validation->set_rules('premises_type_id', 'Premise Type ID', 'required');
        }

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('attribute_id', 'Attribute ID', 'required');


        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 		=> $validation_errors,
                'attribute' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 		=> 'Invalid main Account ID.',
                'attribute' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $premises_type_attribute = $this->premises_service->delete_premises_type_attribute($account_id, $premises_type_id, $attribute_id);

        if (!empty($premises_type_attribute)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_OK,
                'message' 		=> $this->session->flashdata('message'),
                'attribute' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
                'message' 		=> $this->session->flashdata('message'),
                'attribute' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *	Add an Attribute to the Premises Type
    **/
    public function add_premises_type_attribute_post()
    {
        $premises_type_attribute_data 	= $this->post();
        $account_id		  	  		= (int) $this->post('account_id');
        $item_type		  	  		= $this->post('item_type');

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('attribute_name', 'Attribute Name', 'required');

        if ($item_type != 'generic') {
            $this->form_validation->set_rules('premises_type_id', 'Premises Type ID', 'required');
        }

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            $message = [
                'status' 					=> false,
                'message' 					=> 'Invalid data: ',
                'premises_type_attribute' 	=> null
            ];

            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 					=> false,
                'message' 					=> 'Invalid main Account ID',
                'premises_type_attribute' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $premises_type_attribute = $this->premises_service->add_premises_type_attribute($account_id, $premises_type_attribute_data);

        if (!empty($premises_type_attribute)) {
            $message = [
                'status' 					=> true,
                'message' 					=> $this->session->flashdata('message'),
                'premises_type_attribute' 	=> $premises_type_attribute
            ];
            $this->response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = [
                'status' 					=> false,
                'message' 					=> $this->session->flashdata('message'),
                'premises_type_attribute' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *	Update Premises Type Attribute
    **/
    public function update_premises_type_attribute_post()
    {
        $attribute_data 	= $this->post();
        $account_id 		= ( int ) $this->post('account_id');
        $attribute_id 		= ( int ) $this->post('attribute_id');

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('attribute_id', 'Attribute ID', 'required');
        $this->form_validation->set_rules('attribute_name', 'Attribute Name', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            $message = [
                'status' 		=> false,
                'message' 		=> 'Invalid data: ',
                'attribute' 	=> null
            ];

            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'message' 		=> 'Invalid main Account ID.',
                'attribute' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Validate the asset type id.
        if ($attribute_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        ## Run update call
        $updated_attribute = $this->premises_service->update_premises_type_attribute($account_id, $attribute_id, $attribute_data);

        if (!empty($updated_attribute)) {
            $message = [
                'status' 		=> true,
                'message' 		=> $this->session->flashdata('message'),
                'attribute' 	=> $updated_attribute
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'attribute' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
