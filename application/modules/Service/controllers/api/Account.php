<?php

namespace Application\Modules\Service\Controllers\Api;

use App\Adapter\RESTController;
use Application\Modules\Service\Models\AccountModel;
use Application\Modules\Service\Models\ModulesModel;

class Account extends RESTController
{
	/**
	 * @var \Application\Modules\Service\Models\AccountModel
	 */
	private AccountModel $account_service;
	/**
	 * @var \Application\Modules\Service\Models\ModulesModel
	 */
	private ModulesModel $module_service;

	public function __construct(AccountModel $account_service, ModulesModel $module_service)
    {
        // Construct the parent class
        parent::__construct();
		$this->account_service = $account_service;
		$this->module_service = $module_service;
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->lang->load('auth');

        $this->super_admin_list = SUPER_ADMIN_ACCESS;
    }

    /**
    * 	Create new Account resource
    */
    public function create_post()
    {
        $account_data 	= $this->post();
        $tables 		= $this->config->item('tables', 'ion_auth');

        $this->form_validation->set_rules('account_name', 'Account Name', 'required|min_length[6]');
        $this->form_validation->set_rules('account_email', 'Account Email', 'required|valid_email|is_unique[' . $tables['account'] . '.account_email]');
        $this->form_validation->set_rules('account_email', 'Your Email Address', 'required|valid_email|is_unique[' . $tables['user'] . '.email]');
        $this->form_validation->set_rules('account_first_name', 'Account First Name', 'required|min_length[2]');
        $this->form_validation->set_rules('account_last_name', 'Account Last Name', 'required|min_length[2]');
        $this->form_validation->set_rules('account_mobile', 'Contact mobile number', 'required|regex_match[/^[0-9]{11}$/]'); //{11} for 11 digits number
        $this->form_validation->set_rules('admin_username', 'Admin username', 'required|min_length[5]');
        #$this->form_validation->set_rules( 'admin_username', 'Admin username', 'required|min_length[6]|alpha_numeric');
        #$this->form_validation->set_rules('admin_username', 'Admin username', 'required|is_unique[' . $tables['user'] . '.username]');
        $this->form_validation->set_rules('package_id', 'Package ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            ## One of the required fields is invalid
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> 'Invalid Account data: ',
                'account' 	=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_account = $this->account_service->create_account($account_data);

        if (!empty($new_account)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'account' 	=> $new_account
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' => false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' => $this->session->flashdata('message'),
                'account' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Update user resource
    */
    public function update_post()
    {
        $account_data		= $this->post();
        $account_id  		= (int) $this->post('account_id');
        $admin_account_id  	= (int) $this->post('admin_account_id');//This is different from the account being updated
        $tables 			= $this->config->item('tables', 'ion_auth');
        $this->form_validation->set_rules('account_name', 'Account Name', 'required');
        $this->form_validation->set_rules('account_email', 'Account Email', 'required|valid_email|callback_check_account_email');
        $this->form_validation->set_rules('account_first_name', 'Account First Name', 'required');
        $this->form_validation->set_rules('account_last_name', 'Account Last Name', 'required');

        ## Validate the account id.
        if ($account_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            ## One of the required fields is invalid
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> 'Invalid Account data: ',
                'account' 	=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($admin_account_id) && (!in_array($this->ion_auth->_current_user->id, $this->super_admin_list))) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid admin Account ID or insufficient permissions',
                'account' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $account = $this->account_service->get_accounts($account_id);

        if (!$account) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> $this->session->flashdata('message'),
                'account' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }

        ## Run account update
        $updated_account = $this->account_service->update_account($admin_account_id, $account_data);
        if (!empty($updated_account)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'account' 	=> $updated_account
            ];
            $this->response($message, REST_Controller::HTTP_OK); // Resource Updated
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> $this->session->flashdata('message'),
                'account' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get list of all Accounts or single record
    */
    public function accounts_get()
    {
        $account_id 	= (int) $this->get('account_id');
        $accounts = $this->account_service->get_accounts($account_id);
        if (!empty($accounts)) {
            $message = [
                'status' 	=> true,
                'message' 	=> $this->session->flashdata('message'),
                'account' 	=> $accounts
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'message' 	=> $this->session->flashdata('message'),
                'account' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }
    }

    /**
    * Delete Account resource
    */
    public function delete_get()
    {
        $account_id 	= (int) $this->get('account_id');
        if ($account_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid main Account ID',
                'account' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $delete_account = $this->account_service->delete_account($account_id);

        if (!empty($delete_account)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'account' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> $this->session->flashdata('message'),
                'account' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }
    }

    /**
    * Get list of all Available/Active Modules
    */
    public function modules_get()
    {
        $active_only = (int) $this->get('active_only');
        $module_id	 = !empty($this->get('module_id')) ? (int) $this->get('module_id') : false;
        $active_only = ($this->get('active_only') !== null) ? $this->get('active_only') : true;
        $modules 	 = $this->module_service->get_modules($module_id, $active_only);

        if (!empty($modules)) {
            $message = [
                'status' => true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'modules' 	=> $modules
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> $this->session->flashdata('message'),
                'modules' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }
    }

    /**
    * Get a list if Terms and conditions
    */
    public function terms_and_conditions_get()
    {
        $terms_and_conditions = account_terms_and_conditions();
        if (!empty($terms_and_conditions)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'ts_and_cs' => $terms_and_conditions
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> $this->session->flashdata('message'),
                'ts_and_cs' => null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }
    }

    /**
    * Activate New Account and admin user
    */
    public function activate_account_post()
    {
        $account_id 	 = $this->post('account_id');
        $activation_code = $this->post('activation_code');
        if (empty($activation_code) && empty($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'account_activated' => false
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $activate_account = $this->account_service->activate_account($account_id, $activation_code);

        if (!empty($activate_account)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'account_activated' => $activate_account
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'account_activated' => null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }
    }

    /**
    * Get list of all Packages
    */
    public function packages_get()
    {
        $packages = $this->account_service->get_packages();
        if (!empty($packages)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'packages' 	=> $packages
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No data found',
                'packages' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /*
    * Copy account options
    */
    public function copy_account_options_post()
    {
        $post_data		= $this->post();
        $account_id  	= (int) $this->post('account_id');
        $table_options  = $this->post('table_options');

        ## Validate the account id.
        if ($account_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_OK,
                'message' 			=> ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid main Account ID',
                'account_options' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $account_options = $this->account_service->copy_account_options($account_id, $table_options);

        if (!empty($account_options)) {
            $message = [
                'status' 			=> true,
                'http_code' 		=> REST_Controller::HTTP_OK,
                'message' 			=> $this->session->flashdata('message'),
                'account_options' 	=> $account_options
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
                'message' 			=> 'No data found',
                'account_options' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Get Configurable Tables **/
    public function configurable_tables_get()
    {
        $account_id = (int) $this->get('account_id');
        $module_id 	= (int) $this->get('module_id');
        $grouped 	= $this->get('grouped');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 				=> false,
                'message' 				=> 'Invalid main Account ID',
                'configurable_tables' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $configurable_tables = $this->account_service->get_configurable_tables($account_id, $module_id, $grouped);

        if (!empty($configurable_tables)) {
            $message = [
                'status' 				=> true,
                'message' 				=> $this->session->flashdata('message'),
                'configurable_tables' 	=> $configurable_tables
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 				=> false,
                'message' 				=> $this->session->flashdata('message'),
                'configurable_tables' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Get Configurable Tables **/
    public function config_table_data_get()
    {
        $account_id = (int) $this->get('account_id');
        $table_name = $this->get('table_name');
        $options 	= $this->get('options');
        $filters 	= $this->get('filters');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid main Account ID',
                'config_table_data' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $config_table_data = $this->account_service->get_config_table_data($account_id, $table_name, $options, $filters);

        if (!empty($config_table_data)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'table_data' => $config_table_data
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'table_data' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Generate Membership number **/
    public function membership_number_post()
    {
        $postdata	= $this->post();
        $account_id = (int) $this->post('account_id');

        ## Validate the account id.
        if ($account_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_OK,
                'message' 			=> ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid main Account ID',
                'membership_number' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $membership_number = $this->account_service->generate_membership_number(false, false, $postdata);

        if (!empty($membership_number)) {
            $message = [
                'status' 			=> true,
                'http_code' 		=> REST_Controller::HTTP_OK,
                'message' 			=> $this->session->flashdata('message'),
                'membership_number' => $membership_number
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
                'message' 			=> ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'No data found',
                'membership_number' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Search through list of Accounts
    */
    public function lookup_get()
    {
        $account_id 	= (int) $this->get('account_id');
        $limit 		 	= (!empty($this->get('limit'))) ? (int) $this->get('limit') : DEFAULT_LIMIT;
        $offset 	 	= (!empty($this->get('offset'))) ? (int) $this->get('offset') : DEFAULT_OFFSET;
        $where 		 	= $this->get('where');
        $order_by    	= $this->get('order_by');
        $search_term 	= trim(urldecode($this->get('search_term')));

        if (!$this->account_service->check_account_status($account_id) && (!in_array($this->ion_auth->_current_user->id, $this->super_admin_list))) {
            $message = [
                'status' 	=> false,
                'message' 	=> 'Invalid main Account ID or insufficient permissions.',
                'accounts' 	=> null,
                'counters' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $account_lookup = $this->account_service->account_lookup($account_id, $search_term, $where, $order_by, $limit, $offset);

        if (!empty($account_lookup)) {
            $message = [
                'status' 	=> true,
                'message' 	=> $this->session->flashdata('message'),
                'accounts' 	=> (!empty($account_lookup->records)) ? $account_lookup->records : null,
                'counters' 	=> (!empty($account_lookup->counters)) ? $account_lookup->counters : null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'message' 	=> $this->session->flashdata('message'),
                'accounts' 	=> (!empty($account_lookup->records)) ? $account_lookup->records : null,
                'counters' 	=> (!empty($account_lookup->counters)) ? $account_lookup->counters : null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
    /** Verify the uniqueness of an Account email **/
    public function check_account_email($account_email = false)
    {
        if ($this->post('account_id')) {
            $account_id = $this->post('account_id');
        } else {
            $account_id = '';
        }

        $result = $this->account_service->check_unique_accoount_email($account_id, $account_email);
        if ($result == 0) {
            $response = true;
        } else {
            $this->form_validation->set_message('check_account_email', 'Account email must be unique');
            $response = false;
        }
        return $response;
    }


    /** Get Configurable Modules **/
    public function system_modules_get()
    {
        $user_id  		    = (int) $this->get('user_id');
        $account_id  		= (int) $this->get('account_id');
        $admin_account_id  	= (int) $this->get('admin_account_id');
        $module_id  	   =  $this->get('module_id') ? $this->get('module_id') : false;

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if ($account_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        if (!$this->account_service->check_account_status($admin_account_id) && (!in_array($this->ion_auth->_current_user->id, $this->super_admin_list))) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid admin Account ID or insufficient permissions',
                'modules' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $modules = $this->account_service->get_system_modules($user_id, $account_id, $admin_account_id, $module_id);

        if (!empty($modules)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'modules' 	=> $modules
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No data found',
                'modules' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /* Updates a system module's attributes - Including Name and Order */
    public function update_base_module_post()
    {
        $user_id                = (int) $this->post('user_id');
        $account_id  		    = (int) $this->post('account_id');
        $admin_account_id  	    = (int) $this->post('admin_account_id');
        $module_update_id       = $this->post('module_id');
        $module_update_data     = $this->post('module_data');

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('module_id', 'Module ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            ## One of the required fields is invalid
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> 'Invalid Account data: ',
                'updated_module' 	=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if ($account_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        if (!$this->account_service->check_account_status($admin_account_id) && (!in_array($this->ion_auth->_current_user->id, $this->super_admin_list))) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid admin Account ID or insufficient permissions',
                'updated_module' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $updated_module = $this->account_service->update_base_module($user_id, $account_id, $admin_account_id, $module_update_id, $module_update_data);

        if (!empty($updated_module)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'updated_module' => $updated_module
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> $this->session->flashdata('message'),//'An error ocurred while attempting to update!',
                'updated_module' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /* Gets all tables from the database with the exception of blacklisted tables */
    public function all_tables_get()
    {
        $user_id  		    = (int) $this->get('user_id');
        $account_id  		= (int) $this->get('account_id');
        $admin_account_id  	= (int) $this->get('admin_account_id');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if ($account_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        if (!$this->account_service->check_account_status($admin_account_id) && (!in_array($this->ion_auth->_current_user->id, $this->super_admin_list))) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid admin Account ID or insufficient permissions',
                'available_tables' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $tables = $this->account_service->fetch_all_tables($user_id, $account_id, $admin_account_id);

        if (!empty($tables)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_OK,
                'message' 	=> $this->session->flashdata('message'),
                'available_tables' 	=> $tables
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message' 	=> 'No data found',
                'available_tables' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /* gets the columns of a given table */
    public function table_columns_get()
    {
        $user_id  		    = (int) $this->get('user_id');
        $account_id  		= (int) $this->get('account_id');
        $admin_account_id  	= (int) $this->get('admin_account_id');
        $table_name         = $this->get('table_name');

        $this->form_validation->set_rules('table_name', 'Table Name', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if ($account_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        if (!$this->account_service->check_account_status($admin_account_id) && (!in_array($this->ion_auth->_current_user->id, $this->super_admin_list))) {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 			=> ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid admin Account ID or insufficient permissions',
                'available_columns' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $tables = $this->account_service->get_table_columns($user_id, $account_id, $admin_account_id, $table_name);

        if (!empty($tables)) {
            $message = [
                'status' 			=> true,
                'http_code' 		=> REST_Controller::HTTP_OK,
                'message' 			=> $this->session->flashdata('message'),
                'available_columns' => $tables
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
                'message' 			=> 'No data found',
                'available_columns' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /* updates the config/settings table with new values */
    public function update_config_table_post()
    {
        $user_id  		    = (int) $this->post('user_id');
        $account_id  		= (int) $this->post('account_id');
        $admin_account_id  	= (int) $this->post('admin_account_id');
        $config_data        = json_decode($this->post('config_data'));

        $this->form_validation->set_rules('config_data', 'Table Config Data', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if ($account_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        if (!$this->account_service->check_account_status($admin_account_id) && (!in_array($this->ion_auth->_current_user->id, $this->super_admin_list))) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid admin Account ID or insufficient permissions',
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $update_success = $this->account_service->update_config_table($user_id, $account_id, $admin_account_id, $config_data);

        $message = [
            'status' 	=> $update_success,
            'http_code' => REST_Controller::HTTP_OK,
            'message' 	=> $this->session->flashdata('message'),
        ];
        $this->response($message, REST_Controller::HTTP_OK);
    }

    /* deletes a config item/setting item from the settings table */
    public function delete_config_entry_post()
    {
        $user_id  		    = (int) $this->post('user_id');
        $account_id  		= (int) $this->post('account_id');
        $admin_account_id  	= (int) $this->post('admin_account_id');
        $config_entry_id    = (int) $this->post('config_entry_id');

        $this->form_validation->set_rules('config_entry_id', 'Config Entry ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if ($account_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        if (!$this->account_service->check_account_status($admin_account_id) && (!in_array($this->ion_auth->_current_user->id, $this->super_admin_list))) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid admin Account ID or insufficient permissions',
                'config_entry' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $update_success = $this->account_service->delete_config_entry($user_id, $account_id, $admin_account_id, $config_entry_id);

        $message = [
            'status' 		=> $update_success,
            'http_code' 	=> REST_Controller::HTTP_OK,
            'message' 		=> $this->session->flashdata('message'),
            'config_entry' 	=> null
        ];
        $this->response($message, REST_Controller::HTTP_OK);
    }


    /**
    * Get Account Details
    */
    public function client_list_get()
    {
        $access_token = $this->get('client_access_token');

        if (empty($access_token) || (strtolower($access_token) != strtolower(CLIENT_ACCESS_TOKEN))) {
            $message = [
                'status' 		=> false,
                'http_code' 	=> REST_Controller::HTTP_BAD_REQUEST,
                'message' 		=> 'Unauthorized access!',
                'client_list' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $accounts = $this->account_service->get_client_list($access_token);
        if (!empty($accounts)) {
            $message = [
                'status' 		=> true,
                'http_code' 	=> REST_Controller::HTTP_OK,
                'message' 		=> $this->session->flashdata('message'),
                'client_list' 	=> $accounts
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
                'message' 		=> $this->session->flashdata('message'),
                'client_list'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
