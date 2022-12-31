<?php

namespace Application\Modules\Service\Controllers\Api;

use App\Adapter\RESTController;
use Application\Modules\Service\Models\DisciplineModel;

class Discipline extends RESTController
{
    public function __construct()
    {
        parent::__construct();
        $this->discipline_service = new DisciplineModel();
        $this->form_validation->set_error_delimiters(
			$this->config->item('error_start_delimiter', 'ion_auth'),
			$this->config->item('error_end_delimiter', 'ion_auth')
		);
        $this->lang->load('auth');
    }

    /**
    * Create new Discipline resource
    */
    public function create_discipline_post()
    {
        $discipline_data	= $this->post();
        $account_id			= (int)$this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('discipline_name', 'Discipline Name', 'required');
        $this->form_validation->set_rules('discipline_desc', 'Discipline Desc', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 		=> 'Invalid data: ',
                'discipline' 	=> null
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
                'discipline' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_discipline = $this->discipline_service->create_discipline($account_id, $discipline_data);

        if (!empty($new_discipline)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_CREATED,
                'message' 		=> $this->session->flashdata('message'),
                'discipline' 	=> $new_discipline
            ];
            $this->response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
                'message' 		=> $this->session->flashdata('message'),
                'discipline' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Update Discipline record
    */
    public function update_discipline_post()
    {
        $discipline_data 	= $this->post();
        $account_id 	= ( int ) $this->post('account_id');
        $discipline_id 	= ( int ) $this->post('discipline_id');

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('discipline_id', 'Discipline ID', 'required');
        $this->form_validation->set_rules('discipline_name', 'Discipline', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
                'message' 		=> 'Invalid data: ',
                'discipline' 	=> null
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
                'discipline' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Validate the Discipline id.
        if ($discipline_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        ## Run update call
        $updated_discipline = $this->discipline_service->update_discipline($account_id, $discipline_id, $discipline_data);

        if (!empty($updated_discipline)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'discipline' 	=> $updated_discipline
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> $this->session->flashdata('message'),
                'discipline' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all Disciplines /  Search list
    */
    public function disciplines_get()
    {
        $admin_account_id	=  !empty($this->get('admin_account_id')) ? $this->get('admin_account_id') : ((!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false);
        $account_id 		= (!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false ;
        $search_term		= (!empty($this->get('search_term'))) ? trim(urldecode($this->get('search_term'))) : false ;
        $where 		 		= (!empty($this->get('where'))) ? $this->get('where') : false ;
        $order_by 			= (!empty($this->get('order_by'))) ? $this->get('order_by') : false ;
        $limit		 		= ($this->get('limit')) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset	 			= (!empty($this->get('offset'))) ? (int) $this->get('offset') : 0 ;

        if (!$this->account_service->check_account_status($admin_account_id)) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 		=> 'Invalid main Account ID.',
                'disciplines' 	=> null,
                'counters' 		=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $disciplines = $this->discipline_service->get_disciplines($account_id, $search_term, $where, $order_by, $limit, $offset);

        if (!empty($disciplines)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_OK,
                'message' 		=> $this->session->flashdata('message'),
                'disciplines' 	=> (!empty($disciplines->records)) ? $disciplines->records : $disciplines,
                'counters' 		=> (!empty($disciplines->counters)) ? $disciplines->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
                'message'		=> $this->session->flashdata('message'),
                'disciplines' 	=> null,
                'counters' 		=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * ADMIN FUNC. Delete Discipline tem resource
    */
    public function delete_discipline_get()
    {
        $admin_account_id	=  !empty($this->get('admin_account_id')) ? $this->get('admin_account_id') : ((!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false);
        $account_id 		= (int) $this->get('account_id');
        $discipline_id 		= (int) $this->get('discipline_id');

        if ($discipline_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        if (!$this->account_service->check_account_status($admin_account_id)) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 		=> 'Invalid main Account ID.',
                'discipline' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $delete_discipline = $this->discipline_service->delete_discipline($account_id, $discipline_id);

        if (!empty($delete_discipline)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_OK,
                'message' 		=> $this->session->flashdata('message'),
                'discipline' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
                'message' 		=> $this->session->flashdata('message'),
                'discipline' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    * ADMIN FUNC. Activate Account Discipline(s)
    */
    public function activate_account_disciplines_post()
    {
        $activation_data	= $this->post();
        $account_id			= (int)$this->post('account_id');
        $admin_account_id	=  !empty($this->post('admin_account_id')) ? $this->post('admin_account_id') : ((!empty($this->post('account_id'))) ? (int) $this->post('account_id') : false);
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('activation_data[]', 'Activation data', 'required');


        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 				=> 'Invalid data: ',
                'account_disciplines' 	=> null
            ];

            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($admin_account_id)) {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 				=> 'Invalid main Account ID.',
                'account_disciplines' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $active_account_disciplines = $this->discipline_service->activate_account_disciplines($account_id, $activation_data);

        if (!empty($active_account_disciplines)) {
            $message = [
                'status' 				=> true,
                'http_code' 			=> REST_Controller::HTTP_CREATED,
                'message' 				=> $this->session->flashdata('message'),
                'account_disciplines' 	=> $active_account_disciplines
            ];
            $this->response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_NO_CONTENT,
                'message' 				=> $this->session->flashdata('message'),
                'account_disciplines' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Deaactivate Account Discipline(s)
    */
    public function deactivate_account_disciplines_post()
    {
        $deactivation_data	= $this->post();
        $account_id			= (int)$this->post('account_id');
        $admin_account_id	=  !empty($this->post('admin_account_id')) ? $this->post('admin_account_id') : ((!empty($this->post('account_id'))) ? (int) $this->post('account_id') : false);
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('deactivation_data[]', 'Deactivation data', 'required');


        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 				=> 'Invalid data: ',
                'account_disciplines' 	=> null
            ];

            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($admin_account_id)) {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 				=> 'Invalid main Account ID.',
                'account_disciplines' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $deactived_account_disciplines = $this->discipline_service->deactivate_account_disciplines($account_id, $deactivation_data);

        if (!empty($deactived_account_disciplines)) {
            $message = [
                'status' 				=> true,
                'http_code' 			=> REST_Controller::HTTP_CREATED,
                'message' 				=> $this->session->flashdata('message'),
                'account_disciplines' 	=> $deactived_account_disciplines
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_NO_CONTENT,
                'message' 				=> $this->session->flashdata('message'),
                'account_disciplines' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


        /**
    * Get list of all Account Disciplines /  Search list
    */
    public function account_disciplines_get()
    {
        $account_id 	= (!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false ;
        $admin_account_id	=  !empty($this->get('admin_account_id')) ? $this->get('admin_account_id') : ((!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false);
        $search_term	= (!empty($this->get('search_term'))) ? trim(urldecode($this->get('search_term'))) : false ;
        $where 		 	= (!empty($this->get('where'))) ? $this->get('where') : false ;
        $order_by 		= (!empty($this->get('order_by'))) ? $this->get('order_by') : false ;
        $limit		 	= ($this->get('limit')) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset	 		= (!empty($this->get('offset'))) ? (int) $this->get('offset') : 0 ;

        if (!$this->account_service->check_account_status($admin_account_id)) {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 			=> 'Invalid main Account ID.',
                'account_disciplines' 	=> null,
                'counters' 			=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $account_disciplines = $this->discipline_service->get_account_disciplines($account_id, $search_term, $where, $order_by, $limit, $offset);

        if (!empty($account_disciplines)) {
            $message = [
                'status' 			=> true,
                'http_code' 		=> REST_Controller::HTTP_OK,
                'message' 			=> $this->session->flashdata('message'),
                'account_disciplines'=> (!empty($account_disciplines->records)) ? $account_disciplines->records : $account_disciplines,
                'counters' 			=> (!empty($account_disciplines->counters)) ? $account_disciplines->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
                'message'			=> $this->session->flashdata('message'),
                'account_disciplines'=> null,
                'counters' 			=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Create/activate Account Discipline record
    */
    public function create_account_discipline_post()
    {
        $account_discipline_data 	= $this->post();
        $account_id 	= ( int ) $this->post('account_id');
        $admin_account_id	=  !empty($this->post('admin_account_id')) ? $this->post('admin_account_id') : ((!empty($this->post('account_id'))) ? (int) $this->post('account_id') : false);
        $discipline_id 	= ( int ) $this->post('discipline_id');

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('discipline_id', 'Default Discipline ID', 'required');
        $this->form_validation->set_rules('account_discipline_name', 'Discipline Name', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
                'message' 			=> 'Invalid data: ',
                'account_discipline'=> null
            ];

            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($admin_account_id)) {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 			=> 'Invalid main Account ID.',
                'account_discipline'=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Validate the Account Discipline id.
        if ($discipline_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        ## Run add call
        $new_account_discipline = $this->discipline_service->create_account_discipline($account_id, $account_discipline_data);

        if (!empty($new_account_discipline)) {
            $message = [
                'status' 			=> true,
                'http_code' 		=> REST_Controller::HTTP_OK,
                'message' 			=> $this->session->flashdata('message'),
                'account_discipline'=> $new_account_discipline
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
                'message' 			=> $this->session->flashdata('message'),
                'account_discipline'=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Update Account Discipline record
    */
    public function update_account_discipline_post()
    {
        $account_discipline_data 	= $this->post();
        $account_id 			= ( int ) $this->post('account_id');
        $admin_account_id		=  !empty($this->post('admin_account_id')) ? $this->post('admin_account_id') : ((!empty($this->post('account_id'))) ? (int) $this->post('account_id') : false);
        $account_discipline_id 	= ( int ) $this->post('account_discipline_id');

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('account_discipline_id', 'Account Discipline ID', 'required');
        $this->form_validation->set_rules('account_discipline_name', 'Discipline Name', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
                'message' 			=> 'Invalid data: ',
                'account_discipline'=> null
            ];

            $message['message'] = (!$account_id) ? $message['message'].'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($admin_account_id)) {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 			=> 'Invalid main Account ID.',
                'account_discipline'=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Validate the Account Discipline id.
        if ($account_discipline_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        ## Run update call
        $updated_account_discipline = $this->discipline_service->update_account_discipline($account_id, $account_discipline_id, $account_discipline_data);

        if (!empty($updated_account_discipline)) {
            $message = [
                'status' 			=> true,
                'http_code' 		=> REST_Controller::HTTP_OK,
                'message' 			=> $this->session->flashdata('message'),
                'account_discipline'=> $updated_account_discipline
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
                'message' 			=> $this->session->flashdata('message'),
                'account_discipline'=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Get active Account Discipline Statistics
    */
    public function discipline_stats_get()
    {
        $account_id 	= (!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false ;
        $discipline_id 	= (!empty($this->get('discipline_id'))) ? (int) $this->get('discipline_id') : false;
        $date_range 	= (!empty($this->get('date_range'))) ? $this->get('date_range') : false;
        $date_from 		= (!empty($this->get('date_from'))) ? $this->get('date_from') : false;
        $date_to 		= (!empty($this->get('date_to'))) ? $this->get('date_to') : false;

        $where 		 	= (!empty($this->get('where'))) ? $this->get('where') : [];
        $order_by 		= (!empty($this->get('order_by'))) ? $this->get('order_by') : false ;
        $limit		 	= ($this->get('limit')) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset	 		= (!empty($this->get('offset'))) ? (int) $this->get('offset') : 0 ;

        if (!empty($discipline_id)) {
            $where['discipline_id'] = $discipline_id;
        }

        if (!empty($date_range)) {
            $where['date_range'] = $date_range;
        }

        if (!empty($discipline_id)) {
            $where['date_from'] = $date_from;
        }

        if (!empty($date_to)) {
            $where['date_to'] = $date_to;
        }

        /* if( !$this->account_service->check_account_status( $account_id ) ){
            $message = [
                'status' 			=> FALSE,
                'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 			=> 'Invalid main Account ID.',
                'discipline_stats' 	=> NULL,
            ];
            $this->response( $message, REST_Controller::HTTP_OK );
        } */

        $discipline_stats = $this->discipline_service->get_discipline_stats($account_id, $where);

        if (!empty($discipline_stats)) {
            $message = [
                'status' 			=> true,
                'http_code' 		=> REST_Controller::HTTP_OK,
                'message' 			=> $this->session->flashdata('message'),
                'discipline_stats'	=> (!empty($discipline_stats)) ? $discipline_stats : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
                'message'			=> $this->session->flashdata('message'),
                'discipline_stats'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Get Building Statistics
    */
    public function building_stats_get()
    {
        $account_id = (!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false ;
        $site_id 	= (!empty($this->get('site_id'))) ? (int) $this->get('site_id') : false;
        $where 		= (!empty($this->get('where'))) ? $this->get('where') : [];

        /* if( !$this->account_service->check_account_status( $account_id ) ){
            $message = [
                'status' 			=> FALSE,
                'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 			=> 'Invalid main Account ID.',
                'building_stats' 	=> NULL,
            ];
            $this->response( $message, REST_Controller::HTTP_OK );
        } */

        $required_data = [
            'account_id' => $account_id,
            'site_id' 	 => $site_id
        ];

        $this->form_validation->set_data($required_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('site_id', 'Site ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 		=> 'Invalid or missing Field(s)',
                'building_stats'=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $building_stats = $this->discipline_service->get_building_stats($account_id, $site_id, $where);

        if (!empty($building_stats)) {
            $message = [
                'status' 			=> true,
                'http_code' 		=> REST_Controller::HTTP_OK,
                'message' 			=> $this->session->flashdata('message'),
                'building_stats'	=> (!empty($building_stats)) ? $building_stats : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
                'message'			=> $this->session->flashdata('message'),
                'building_stats'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Get Site Overdue Jobs
    */
    public function overdue_jobs_get()
    {
        $account_id 	= (!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false ;
        $discipline_id 	= (!empty($this->get('discipline_id'))) ? (int) $this->get('discipline_id') : false;
        $site_id 		= (!empty($this->get('site_id'))) ? (int) $this->get('site_id') : false;
        $where 			= (!empty($this->get('where'))) ? $this->get('where') : [];

        /* if( !$this->account_service->check_account_status( $account_id ) ){
            $message = [
                'status' 		=> FALSE,
                'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 		=> 'Invalid main Account ID.',
                'overdue_jobs' 	=> NULL,
            ];
            $this->response( $message, REST_Controller::HTTP_OK );
        } */

        $required_data = [
            'account_id' 	=> $account_id,
            'site_id' 		=> $site_id,
        ];

        $this->form_validation->set_data($required_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('site_id', 'Site ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 		=> 'Invalid or missing Field(s)',
                'overdue_jobs'	=> null,
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $outcomes = $this->discipline_service->get_overdue_jobs($account_id, $site_id, $discipline_id, $where);

        if (!empty($outcomes)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_OK,
                'message' 		=> $this->session->flashdata('message'),
                'overdue_jobs'	=> (!empty($outcomes)) ? $outcomes : false,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
                'message'		=> $this->session->flashdata('message'),
                'overdue_jobs'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Get Building Recommendations
    */
    public function building_recommendations_get()
    {
        $account_id 	= (!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false ;
        $discipline_id 	= (!empty($this->get('discipline_id'))) ? (int) $this->get('discipline_id') : false;
        $site_id 		= (!empty($this->get('site_id'))) ? (int) $this->get('site_id') : false;
        $where 			= (!empty($this->get('where'))) ? $this->get('where') : [];

        /* if( !$this->account_service->check_account_status( $account_id ) ){
            $message = [
                'status' 		=> FALSE,
                'http_code' 	=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 		=> 'Invalid main Account ID.',
                'recommendations' 	=> NULL,
            ];
            $this->response( $message, REST_Controller::HTTP_OK );
        } */

        $required_data = [
            'account_id' 	=> $account_id,
            'site_id' 		=> $site_id,
        ];

        $this->form_validation->set_data($required_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('site_id', 'Site ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 		=> 'Invalid or missing Field(s)',
                'recommendations'	=> null,
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $outcomes = $this->discipline_service->get_building_recommendations($account_id, $site_id, $discipline_id, $where);

        if (!empty($outcomes)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_OK,
                'message' 		=> $this->session->flashdata('message'),
                'recommendations'	=> (!empty($outcomes)) ? $outcomes : false,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_NO_CONTENT,
                'message'		=> $this->session->flashdata('message'),
                'recommendations'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Get Buildings List by Discipline ID
    */
    public function buildings_by_discipline_get()
    {
        $account_id 	= (!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false ;
        $discipline_id 	= (!empty($this->get('discipline_id'))) ? (int) $this->get('discipline_id') : false;
        $where 			= (!empty($this->get('where'))) ? $this->get('where') : [];
        $order_by 			= (!empty($this->get('order_by'))) ? $this->get('order_by') : false ;
        $limit		 		= ($this->get('limit')) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset	 			= (!empty($this->get('offset'))) ? (int) $this->get('offset') : 0 ;

        /* if( !$this->account_service->check_account_status( $account_id ) ){
            $message = [
                'status' 	=> FALSE,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID.',
                'buildings' => NULL,
                'counters'  => ( !empty( $buildings->counters ) ) ? $buildings->counters : null,
            ];
            $this->response( $message, REST_Controller::HTTP_OK );
        } */

        $required_data = [
            'account_id' 	=> $account_id,
            'discipline_id' => $discipline_id
        ];

        $this->form_validation->set_data($required_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('discipline_id', 'Discipline ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 		=> 'Invalid or missing Field(s)',
                'buildings'		=> null,
                'counters'		=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $buildings = $this->discipline_service->get_buildings_by_discipline($account_id, $discipline_id, $where, $order_by, $limit, $offset);

        if (!empty($buildings)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'buildings'	=> (!empty($buildings->records)) ? $buildings->records : $buildings,
                'counters'  => (!empty($buildings->counters)) ? $buildings->counters : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message'	=> $this->session->flashdata('message'),
                'buildings'	=> null,
                'counters'  => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Get Discipline Outcomes By Site
    */
    public function building_outcomes_get()
    {
        $account_id 	= (!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false ;
        $discipline_id 	= (!empty($this->get('discipline_id'))) ? (int) $this->get('discipline_id') : false;
        $site_id 		= (!empty($this->get('site_id'))) ? (int) $this->get('site_id') : false;
        $where 			= (!empty($this->get('where'))) ? $this->get('where') : [];

        /* if( !$this->account_service->check_account_status( $account_id ) ){
            $message = [
                'status' 			=> FALSE,
                'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 			=> 'Invalid main Account ID.',
                'building_outcomes' => NULL,
            ];
            $this->response( $message, REST_Controller::HTTP_OK );
        } */

        $required_data = [
            'account_id' 	=> $account_id,
            'site_id' 		=> $site_id,
            'discipline_id' => $discipline_id
        ];

        $this->form_validation->set_data($required_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('site_id', 'Site ID', 'required');
        $this->form_validation->set_rules('discipline_id', 'Discipline ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 			=> 'Invalid or missing Field(s)',
                'building_outcomes'	=> null,
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $outcomes = $this->discipline_service->get_building_outcomes($account_id, $discipline_id, $site_id, $where);

        if (!empty($outcomes)) {
            $message = [
                'status' 			=> true,
                'http_code' 		=> REST_Controller::HTTP_OK,
                'message' 			=> $this->session->flashdata('message'),
                'building_outcomes'	=> (!empty($outcomes)) ? $outcomes : false,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
                'message'			=> $this->session->flashdata('message'),
                'building_outcomes'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Get Buildings Outcomes Summary
    */
    public function building_outcomes_summary_get()
    {
        $account_id 	= (!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false ;
        $discipline_id 	= (!empty($this->get('discipline_id'))) ? (int) $this->get('discipline_id') : false;
        $site_id 		= (!empty($this->get('site_id'))) ? (int) $this->get('site_id') : false;
        $where 			= (!empty($this->get('where'))) ? $this->get('where') : [];

        /* if( !$this->account_service->check_account_status( $account_id ) ){
            $message = [
                'status' 			=> FALSE,
                'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 			=> 'Invalid main Account ID.',
                'outcomes_summary' 	=> NULL,
            ];
            $this->response( $message, REST_Controller::HTTP_OK );
        } */

        $required_data = [
            'account_id' 	=> $account_id,
            'site_id' 		=> $site_id,
        ];

        $this->form_validation->set_data($required_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('site_id', 'Site ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 			=> 'Invalid or missing Field(s)',
                'outcomes_summary'	=> null,
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $outcomes = $this->discipline_service->get_building_outcomes_summary($account_id, $site_id, $discipline_id, $where);

        if (!empty($outcomes)) {
            $message = [
                'status' 			=> true,
                'http_code' 		=> REST_Controller::HTTP_OK,
                'message' 			=> $this->session->flashdata('message'),
                'outcomes_summary'	=> (!empty($outcomes)) ? $outcomes : false,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
                'message'			=> $this->session->flashdata('message'),
                'outcomes_summary'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Get Discipline Attendance Info
    */
    public function discipline_attendance_get()
    {
        $account_id 	= (!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false ;
        $discipline_id 	= (!empty($this->get('discipline_id'))) ? (int) $this->get('discipline_id') : false;
        $where 			= (!empty($this->get('where'))) ? $this->get('where') : [];

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 				=> 'Invalid main Account ID.',
                'discipline_attendance'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $required_data = [
            'account_id' 	=> $account_id,
            'discipline_id' => $discipline_id
        ];

        $this->form_validation->set_data($required_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('discipline_id', 'Site ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 				=> 'Invalid or missing Field(s)',
                'discipline_attendance'	=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $discipline_attendance = $this->discipline_service->get_discipline_attendance_info($account_id, $discipline_id, $where);

        if (!empty($discipline_attendance)) {
            $message = [
                'status' 				=> true,
                'http_code' 			=> REST_Controller::HTTP_OK,
                'message' 				=> $this->session->flashdata('message'),
                'discipline_attendance'	=> (!empty($discipline_attendance)) ? $discipline_attendance : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_NO_CONTENT,
                'message'				=> $this->session->flashdata('message'),
                'discipline_attendance'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Get Discipline Outcomes Info
    */
    public function discipline_outcomes_get()
    {
        $account_id 	= (!empty($this->get('account_id'))) ? (int) $this->get('account_id') : false ;
        $discipline_id 	= (!empty($this->get('discipline_id'))) ? (int) $this->get('discipline_id') : false;
        $where 			= (!empty($this->get('where'))) ? $this->get('where') : [];

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 				=> 'Invalid main Account ID.',
                'discipline_outcomes'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $required_data = [
            'account_id' 	=> $account_id,
            'discipline_id' => $discipline_id
        ];

        $this->form_validation->set_data($required_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('discipline_id', 'Site ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 				=> 'Invalid or missing Field(s)',
                'discipline_outcomes'	=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.trim($validation_errors) : trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $discipline_outcomes = $this->discipline_service->get_discipline_outcomes_info($account_id, $discipline_id, $where);

        if (!empty($discipline_outcomes)) {
            $message = [
                'status' 				=> true,
                'http_code' 			=> REST_Controller::HTTP_OK,
                'message' 				=> $this->session->flashdata('message'),
                'discipline_outcomes'	=> (!empty($discipline_outcomes)) ? $discipline_outcomes : null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 				=> false,
                'http_code' 			=> REST_Controller::HTTP_NO_CONTENT,
                'message'				=> $this->session->flashdata('message'),
                'discipline_outcomes'	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
