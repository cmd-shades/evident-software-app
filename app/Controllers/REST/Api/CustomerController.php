<?php

namespace App\Controllers\REST\Api;

use App\Adapter\RESTController;
use App\Models\Service\AccountModel;
use App\Models\Service\CustomerModel;

final class CustomerController extends RESTController
{
	/**
	 * @var \Application\Modules\Service\Models\CustomerModel
	 */
	private $customer_service;
	/**
	 * @var \Application\Modules\Service\Models\AccountModel
	 */
	private $account_service;

	public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->customer_service = new CustomerModel();
        $this->account_service = new AccountModel();
        $this->form_validation->set_error_delimiters(
			$this->config->item('error_start_delimiter', 'ion_auth'),
			$this->config->item('error_end_delimiter', 'ion_auth')
		);
        $this->lang->load('auth');
    }

    /**
    * Create new Customer resource
    */
    public function create_post()
    {
        $customer_data 		= $this->post();
        $account_id	   		= (!empty($this->post('account_id'))) ? ( int ) $this->post('account_id') : false ;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('customer_first_name', 'Customer First Name', 'required');
        $this->form_validation->set_rules('customer_last_name', 'Customer Last Name', 'required');
        #$this->form_validation->set_rules( 'customer_email', 'Customer Email', 'required|valid_email' );
        $this->form_validation->set_rules('customer_mobile', 'Customer Mobile', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Validation errors: '.trim($validation_errors),
                'customer' 			=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid main Account ID',
                'customer' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_customer = $this->customer_service->create_customer($account_id, $customer_data);

        if (!empty($new_customer)) {
            $message = [
                'status' 	=> true,
                'message' 	=> $this->session->flashdata('message'),
                'customer' 	=> $new_customer
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' 	=> false,
                'message' 	=> $this->session->flashdata('message'),
                'customer' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * 	Update customer resource
    */
    public function update_post()
    {
        $post_data		= $this->post();

        $account_id 	= (!empty($post_data['account_id'])) ? ( int ) $post_data['account_id'] : false ;
        $customer_id 	= (!empty($post_data['customer_id'])) ? ( int ) $post_data['customer_id'] : false ;
        $customer_data 	= (!empty($post_data['customer_data'])) ? $post_data['customer_data'] : false ;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('customer_id', 'Customer ID', 'required');
        $this->form_validation->set_rules('customer_data', 'Customer Data', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$customer_id || !$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' 		=> false,
                'message' 		=> 'Invalid data: ',
                'u_customer' 	=> null
            ];

            $message['message'] = (!$customer_id) ? $message['message'].'customer_id, ' : $message['message'];
            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main acocount is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'message' 		=> 'Invalid main Account ID',
                'u_customer' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Validate the customer id.
        $customer_exists = $this->customer_service->get_customers($account_id, $customer_id);
        if (!$customer_exists || empty($customer_id) || (( int ) $customer_id <= 0)) {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'u_customer' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Run customer update
        $updated_customer = $this->customer_service->update_customer($account_id, $customer_id, $customer_data);
        if (!empty($updated_customer)) {
            $message = [
                'status' 		=> true,
                'message' 		=> $this->session->flashdata('message'),
                'u_customer' 	=> $updated_customer
            ];
            $this->response($message, REST_Controller::HTTP_OK); // Resource Updated
        } else {
            $message = [
                'status'		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'u_customer' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all Customers or single record
    */
    public function customers_get()
    {
        $account_id 	= (!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false;
        $customer_id 	= (!empty($this->get('customer_id'))) ? (int) $this->get('customer_id') : false;
        $search_term 	= (!empty($this->get('search_term'))) ? trim(urldecode($this->get('search_term'))) : false;
        $where 		 	= (!empty($this->get('where'))) ? $this->get('where') : [];
        $order_by 		= (!empty($this->get('order_by'))) ? $this->get('order_by') : false;
        $limit 		 	= (!empty($this->get('limit'))) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset 	 	= (!empty($this->get('offset'))) ? (int) $this->get('offset') : DEFAULT_OFFSET;

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> 'Invalid or missing main Account ID',
                'customer' 	=> null,
                'counters' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!empty($customer_id)) {
            $customer = $this->customer_service->get_customers($account_id, $customer_id);
            if (!empty($customer)) {
                $message = [
                    'status' 	=> true,
                    'message' 	=> $this->session->flashdata('message'),
                    'customers' => $customer,
                    'counters' 	=> null
                ];
                $this->response($message, REST_Controller::HTTP_OK);
            } else {
                $message = [
                    'status'	=> false,
                    'message' 	=> $this->session->flashdata('message'),
                    'customers' => null,
                    'counters'  => null
                ];
                $this->response($message, REST_Controller::HTTP_NO_CONTENT);
            }
        }

        $customers = $this->customer_service->get_customers($account_id, $customer_id, $search_term, $where, $order_by, $limit, $offset);

        if (!empty($customers)) {
            $message = [
                'status' 	=> true,
                'message' 	=> $this->session->flashdata('message'),
                'customers' => (!empty($customers->records)) ? $customers->records : (!empty($customers) ? $customers : false),
                'counters'  => (!empty($customers->counters)) ? $customers->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'message' 	=> $this->session->flashdata('message'),
                'customers' => null,
                'counters' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }
    }

    /**
    * Delete Customer resource
    */
    public function delete_get()
    {
        $customer_id 	= (int) $this->get('customer_id');
        $account_id 	= (int) $this->get('account_id');

        //Check and verify that main acocount is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID',
                'customer' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if ($customer_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        $delete_customer = $this->customer_service->delete_customer($account_id, $customer_id);
        if ($delete_customer) {
            $message = [
                'status' 	=> true,
                'message' 	=> $this->session->flashdata('message'),
                'customer' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'message' 	=> $this->session->flashdata('message'),
                'customer' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Search customers **/
    public function lookup_get()
    {
        $account_id 	= (!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false;
        $customer_id 	= (!empty($this->get('customer_id'))) ? (int) $this->get('customer_id') : false;
        $search_term 	= (!empty($this->get('search_term'))) ? trim(urldecode($this->get('search_term'))) : false;
        $where 		 	= (!empty($this->get('where'))) ? $this->get('where') : [];
        $order_by 		= (!empty($this->get('order_by'))) ? $this->get('order_by') : false;
        $limit 		 	= (!empty($this->get('limit'))) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset 	 	= (!empty($this->get('offset'))) ? (int) $this->get('offset') : DEFAULT_OFFSET;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid main Account ID',
                'customers' => null,
                'counters'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $customers = $this->customer_service->get_customers($account_id, $customer_id, $search_term, $where, $order_by, $limit, $offset);

        if (!empty($customers)) {
            $message = [
                'status' 	=> true,
                'message' 	=> $this->session->flashdata('message'),
                'customers' => (!empty($customers->records)) ? $customers->records : (!empty($customers) ? $customers : false),
                'counters'  => (!empty($customers->counters)) ? $customers->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'message' 	=> $this->session->flashdata('message'),
                'customers' => null,
                'counters'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *	Create a customer's contact address record
    **/
    public function create_address_post()
    {
        $contact_data  	= $this->post();
        $account_id		= ( int )$this->post('account_id');
        $customer_id	= ( int )$this->post('customer_id');

        $this->form_validation->set_rules('customer_id', 'Customer ID', 'required');
        $this->form_validation->set_rules('address_type_id', 'Address Type ID', 'required');
        $this->form_validation->set_rules('address_contact_first_name', 'Contact First Name', 'required');
        $this->form_validation->set_rules('address_contact_last_name', 'Contact Last Name', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            ## One of the required fields is invalid
            $message = [
                'status' 			=> false,
                'message' 			=> 'Validation errors: '.trim($validation_errors),
                'address_contact' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid main Account ID.',
                'address_contact' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $address_contact = $this->customer_service->create_contact($account_id, $customer_id, $contact_data);

        if (!empty($address_contact)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'address_contact' 	=> $address_contact
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'address_contact' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /*
    *	Get list of all Customer Addresses
    */
    public function address_contacts_get()
    {
        $account_id   			= (int) $this->get('account_id');
        $customer_id	 		= (int) $this->get('customer_id');
        $customer_address_id	= (int) $this->get('customer_address_id');
        $address_type_id		= $this->get('address_type_id');
        $limit		 			= ($this->get('limit')) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset	 				= ($this->get('offset')) ? (int) $this->get('offset') : 0;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid main Account ID.',
                'address_contacts' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $address_contacts = $this->customer_service->get_address_contacts($account_id, $customer_id, $customer_address_id, $address_type_id, $limit, $offset);

        if (!empty($address_contacts)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'address_contacts' 	=> $address_contacts
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=>$this->session->flashdata('message'),
                'address_contacts' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *	This is to update an address
    */
    public function update_address_post()
    {
        $validation_errors = $post_data = $address_exists = false;

        $post_data 		= $this->post();

        $customer_address_id 	= (!empty($post_data['customer_address_id'])) ? $post_data['customer_address_id'] : false ;
        unset($post_data['customer_address_id']);

        $data 			= (!empty($post_data['dataset'])) ? json_decode($post_data['dataset']) : false ;
        unset($post_data['dataset']);

        $account_id 	= (!empty($post_data['account_id'])) ? $post_data['account_id'] : false ;
        unset($post_data['account_id']);

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('customer_address_id', 'Contact Address ID', 'required');
        $this->form_validation->set_rules('dataset', 'Update Data', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Validation errors: '.trim($validation_errors),
                'updated_address' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'updated_address'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $address_exists = $this->customer_service->get_address_contacts($account_id, false, $customer_address_id);

        if (!$address_exists) {
            $message = [
                'status' 			=> false,
                'message' 			=> "Invalid Contact ID",
                'updated_address' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $updated_address = $this->customer_service->update_address($account_id, $customer_address_id, $data);

        if (!empty($updated_address)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'updated_address' 	=> $updated_address
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'updated_address' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *	This function will delete Address
    */
    public function delete_address_post()
    {
        $validation_errors = $post_data = $address_exists = false;

        $post_data = $this->post();

        $customer_address_id 	= (!empty($post_data['customer_address_id'])) ? $post_data['customer_address_id'] : false ;
        unset($post_data['customer_address_id']);

        $account_id 	= (!empty($post_data['account_id'])) ? $post_data['account_id'] : false ;
        unset($post_data['account_id']);

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('customer_address_id', 'Address ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Validation errors: '.trim($validation_errors),
                'deleted_address' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'deleted_address'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $address_exists = $this->customer_service->get_address_contacts($account_id, false, $customer_address_id);

        if (!$address_exists) {
            $message = [
                'status' 			=> false,
                'message' 			=> "Invalid Contact ID",
                'deleted_address' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $deleted_address = $this->customer_service->delete_address($account_id, $customer_address_id);

        if (!empty($deleted_address)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'deleted_address' 	=> $deleted_address
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'deleted_address' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * 	Add new customer note
    */
    public function create_note_post()
    {
        $new_note = $post_data = $customer_exists = false;

        $post_data 			= $this->post();

        $account_id 		= (!empty($post_data['account_id'])) ? $post_data['account_id'] : false ;
        unset($post_data['account_id']);

        $customer_id 		= (!empty($post_data['customer_id'])) ? $post_data['customer_id'] : false ;
        unset($post_data['customer_id']);

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('customer_id', 'Customer ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Validation errors: '.trim($validation_errors),
                'new_note' 			=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'new_note' 		=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $customer_exists = $this->customer_service->get_customers($account_id, $customer_id);
        if (!$customer_exists) {
            $message = [
                'status' 		=> false,
                'message' 		=> "Invalid Customer ID",
                'new_note' 		=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_note = $this->customer_service->create_note($account_id, $customer_id, $post_data);

        if (!empty($new_note)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'new_note' 			=> $new_note
            ];

            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'new_note' 			=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * 	Get the Customer Notes
    */
    public function notes_get()
    {
        $postset 		= $this->get();

        $account_id 	= (!empty($postset['account_id'])) ? $postset['account_id'] : false ;
        $customer_id 	= (!empty($postset['customer_id'])) ? $postset['customer_id'] : false ;
        $note_id 		= (!empty($postset['note_id'])) ? $postset['note_id'] : false ;
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
                'message' 			=> 'Validation errors: '.trim($validation_errors),
                'notes' 		=> null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'notes' 		=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $notes = $this->customer_service->get_notes($account_id, $customer_id, $note_id, $where, $limit, $offset);

        if (!empty($notes)) {
            $message = [
                'status' 		=> true,
                'message' 		=> $this->session->flashdata('message'),
                'notes' 		=> $notes
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'notes' 		=> false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
