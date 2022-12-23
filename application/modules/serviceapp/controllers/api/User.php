<?php

namespace Application\Service\Controllers\Api;
/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */

use App\Libraries\REST_Controller;
use Firebase\JWT\JWT;

class User extends REST_Controller
{
    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit']    = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit']   = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
        $this->load->model('Modules_model', 'module_service');
        $this->load->model('Account_model', 'account_service');
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->lang->load('auth');
        $this->tables = $this->config->item('tables', 'ion_auth');
    }

    public function login_post()
    {
        $username     = ($this->post('username')) ? $this->post('username') : $this->post('email');
        $password     = $this->post('password');
        $invalidLogin = ['invalid' => $username];
        if (!$username || !$password) {
            $this->response($invalidLogin, REST_Controller::HTTP_NOT_FOUND);
        }

        $user = $this->ion_auth->login($username, $password);


        if (isset($user->id) && !empty($user->id)) {
            ## Check access to the module
            $app_uuid = $this->post('app_uuid');
            if (!empty($app_uuid)) {
                $permission = $this->module_service->get_module_access($user->account_id, $user->id, false, $app_uuid);
                if (!$permission) {
                    $this->response([
                        'auth_token' => null,
                        'app_uuid' => $app_uuid,
                        'status' => false,
                        'message' => 'User autheniticated successfully. Access denied',
                        'user' => null
                    ], REST_Controller::HTTP_OK);
                }
            }

            $date   = new DateTime();
            $expiry = $date->getTimestamp() + 60 * 240 * 2;
            $token  = [
                'iss' => base_url(),
                'sub' => $user->id,
                'iat' => $date->getTimestamp(),
                'exp' => $expiry,
                'nbf' => $date->getTimestamp(),
                'data' => [
                    'user' => $user
                ]
            ];

            $output = [
                'auth_token' => JWT::encode($token, API_SECRET_KEY, API_JWT_ALGORITHM),
                'app_uuid' => (!empty($app_uuid)) ? $app_uuid : null,
                'status' => true,
                'message' => $this->ion_auth->messages(),
                'user' => $user
            ];

            ## Harvest Device token if user has authorised push notifications
            if ($this->post('device_token')) {
                $this->_save_apns(['user_id' => $user->id,'account_id' => $user->account_id,'device_token' => $this->post('device_token')]);
            }

            $this->response($output, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            $flash_message  = $this->session->flashdata('message');
            $message        = ($this->ion_auth->messages()) ? $this->ion_auth->messages() : $flash_message;
            $this->response([
                'auth_token' => null,
                'status' => false,
                'message' => ($flash_message) ? $flash_message : (($message) ? $message : 'Invalid username or password'),
                'user' => null
            ], REST_Controller::HTTP_OK);
        }
    }

    public function authenticate_post()
    {
        $auth_token = $this->post('auth_token');
        if (!empty($auth_token)) {
            try {
                $decoded_token  = JWT::decode($token, API_SECRET_KEY, API_JWT_ALGORITHM);
                $this->response([
                    'status' => true,
                    'message' => 'Token validated',
                    'token' => $auth_token
                ], REST_Controller::HTTP_OK);
            } catch (Exception $e) {
                $this->response([
                    'status' => false,
                    'message' => $e,
                    'token' => null
                ], REST_Controller::HTTP_OK);
            }
        }
        $invalid_token = [
            'status' => false,
            'message' => 'Invalid token',
            'token' => $auth_token
        ];
        $this->response($invalid_token, REST_Controller::HTTP_NOT_FOUND);
    }

    public function users_get()
    {
        $id         = $this->get('id');
        $account_id = (int)$this->get('account_id');
        if (!$account_id) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Main Account ID is required',
                'users' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        // If the id parameter doesn't exist return all the users
        if ($id === null && !empty($account_id)) {
            $users = $this->ion_auth->get_users_by_account_id($account_id);

            // Check if the users data store contains users (in case the database result returns NULL)
            if ($users) {
                // Set the response and exit
                $this->response([
                    'status' => true,
                    'message' => 'User records were found',
                    'users' => $users,
                ], REST_Controller::HTTP_OK);// OK (200) being the HTTP response code
            } else {
                // Set the response and exit
                $this->response([
                    'status' => false,
                    'message' => 'No users were found',
                    'users' => null
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }

        // Find and return a single record for a particular user.
        $id = (int) $id;

        // Validate the id.
        if ($id <= 0) {
            // Invalid id, set the response and exit.
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        $user = $this->ion_auth->get_user_by_id($account_id, $id);
        #$user->permitted_modules = $this->module_service->get_allowed_modules( $user->account_id, $user->id );
        if (!empty($user)) {
            $this->response([
                'status' => true,
                'message' => 'User record found',
                'user' => $user
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            $this->response([
                'status' => false,
                'message' => 'User could not be found',
                'user' => null
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
    }

    /** Create new user **/
    public function register_post()
    {
        $identity   = $this->config->item('identity', 'ion_auth');
        $email      = $this->post('email');
        $username   = ($this->post('username')) ? trim(strtolower($this->post('username'))) : (($identity == 'email' || $this->post('email')) ? trim(strtolower($this->post('email'))) : strtolower(trim($this->post('first_name')) . trim($this->post('last_name'))));
        $password   = $this->post('password');
        $account_id = (int)$this->post('account_id');
        $first_name = ucwords(strtolower($this->post('first_name')));
        $last_name  = ucwords(strtolower($this->post('last_name')));

        ## Validate Passwords
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required');
        $this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'required');
        $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email|is_unique[' . $this->tables['user'] . '.email]');

        $this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]|password_check[1,1,1]');
        $this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
        } else {
            $this->form_validation->set_data(['username' => $username]);
            $this->form_validation->set_rules('username', 'Username', 'required|is_unique[' . $this->tables['user'] . '.username]');
            if ($this->form_validation->run() == false) {
                $invalid_username = (validation_errors()) ? 'The supplied username (' . $username . ') has already been used. Please use a unique First name/Last name combination.' : false;
            }
        }

        if (!$email || !$username || !$password || !$account_id || (isset($validation_errors) && !empty($validation_errors)) || (isset($invalid_username) && !empty($invalid_username))) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid data: ',
                'user' => null
            ];

            $message['message'] = (!$email) ? $message['message'] . 'email, ' : $message['message'];
            $message['message'] = (!$username) ? $message['message'] . 'username, ' : $message['message'];
            $message['message'] = (!$password) ? $message['message'] . 'password, ' : $message['message'];
            $message['message'] = (!$account_id) ? $message['message'] . 'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $message['message'] = (isset($invalid_username) && !empty($invalid_username)) ? 'Validation errors: ' . $invalid_username : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'user' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $user_id = $this->ion_auth->register($username, $password, $email, $this->post());

        if (!empty($user_id)) {
            $new_user = $this->ion_auth->get_user_by_id($account_id, $user_id);
            if (!$new_user) {
                $this->response([
                    'status' => false,
                    'message' => 'Errors with user registration',
                    'user' => null
                ], REST_Controller::HTTP_OK);
            }

            $message = [
                'status' => true,
                'message' => $this->ion_auth->messages(),
                'user' => $new_user
            ];

            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' => false,
                'message' => $this->ion_auth->errors(),
                'user' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Update user resource **/
    public function update_post()
    {
        $id         = (int) $this->post('id');
        $account_id = (int)$this->post('account_id');
        $email      = $this->post('email');

        ## Validate Passwords
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('id', 'User ID', 'required');
        #$this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'required');

        if ($this->input->post('password')) {
            $this->post('password');
            $this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]|password_check[1,1,1]');
            $this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');
        }

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
        }

        if ((isset($password) && !$password) || !$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid data: ',
                'user' => null
            ];

            $message['message'] = (isset($email) && !$email) ? $message['message'] . 'email, ' : $message['message'];
            $message['message'] = (isset($username) && !$username) ? $message['message'] . 'username, ' : $message['message'];
            $message['message'] = (isset($password) && !$password) ? $message['message'] . 'password, ' : $message['message'];
            $message['message'] = (!$account_id) ? $message['message'] . 'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $message['message'] = (isset($invalid_username) && !empty($invalid_username)) ? 'Validation errors: ' . $invalid_username : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Validate the id.
        if ($id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        $user = $this->ion_auth->get_user_by_id($account_id, $id);

        $current_account = $this->ion_auth->_current_user()->account_id;

        ## Stop illegal updates
        if ($current_account != $account_id) {
            $message = [
                'status' => false,
                'message' => 'Illegal operation. This is not your resource!',
                'user' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$user) {
            $message = [
                'status' => false,
                'message' => 'User record not found',
                'user' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $groups         = $this->ion_auth->groups()->result_array();
        $currentGroups  = $this->ion_auth->get_user_groups($id)->result();

        // Only allow updating groups if user is admin
        if ($this->ion_auth->is_admin($id)) {
            //Update the groups user belongs to
            $groupData = $this->post('groups');
            if (isset($groupData) && !empty($groupData)) {
                $this->ion_auth->remove_from_group('', $id);
                foreach ($groupData as $grp) {
                    $this->ion_auth->add_to_group($grp, $id);
                }
            }
        }

        ## Run user update
        $updated = $this->ion_auth->update($account_id, $user->id, $this->post());
        if (!$updated) {
            $message = [
                'status' => false,
                'message' => $this->ion_auth->errors(),
                'user' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $updated_user = $this->ion_auth->get_user_by_id($account_id, $id);
        $message = [
            'status' => true,
            'message' => $this->ion_auth->messages(),
            'user' => $updated_user
        ];
        $this->response($message, REST_Controller::HTTP_OK);
    }


    /** Update user resource **/
    public function permissions_post()
    {
        $id         = (int) $this->post('id');
        $account_id = (int)$this->post('account_id');

        ## Validate Passwords
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('id', 'User ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status' => false,
                'message' => 'Invalid data: ',
                'permissions' => null
            ];
            $message['message'] = (!$account_id) ? $message['message'] . 'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $message['message'] = (isset($invalid_username) && !empty($invalid_username)) ? 'Validation errors: ' . $invalid_username : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'permissions' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Validate the user id.
        if ($id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        $user = $this->ion_auth->get_user_by_id($account_id, $id);
        $current_account = $this->ion_auth->_current_user()->account_id;

        ## Stop illegal updates
        if ($current_account != $account_id) {
            $message = [
                'status' => false,
                'message' => 'Illegal operation. This is not your resource!',
                'permissions' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$user) {
            $message = [
                'status' => false,
                'message' => 'User not found',
                'permissions' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        // Only allow updating groups if user is admin
        if (!$this->ion_auth->is_admin($id)) {
            $message = [
                'status' => false,
                'message' => 'You do not have admin permissions to complete this request!',
                'permissions' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Run update user permission
        $permissions = $this->module_service->set_permissions($account_id, $user->id, $this->post());
        if (!empty($permissions)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'permissions' => $permissions
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'permissions' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Delete user resource
    */
    public function delete_get()
    {
        $account_id = (int) $this->get('account_id');
        $user_id    = (int) $this->get('user_id');

        if ($user_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID.',
                'user' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $delete_user = $this->ion_auth->archive_user($account_id, $user_id);

        if (!empty($delete_user)) {
            $message = [
                'status'    => true,
                'message'   => $this->session->flashdata('message'),
                'user'      => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'    => false,
                'message'   => $this->session->flashdata('message'),
                'user'      => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Get list of all user-types **/
    public function user_types_get()
    {
        $user_type_id = (int) $this->get('user_type_id');
        $user_types   = $this->ion_auth->get_user_types($user_type_id);
        if (!empty($user_types)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'user_types' => $user_types
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'user_types' => null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }
    }

    public function account_modules_get()
    {
        $account_id     = (int) $this->get('account_id');
        $inc_mod_item   = $this->get('inc_mod_item');
        $categorized    = $this->get('categorized');
        $account_modules = $this->account_service->get_account_modules($account_id, $inc_mod_item, $categorized);
        if (!empty($account_modules)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'account_modules' => $account_modules
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'account_modules' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /*
    * Save an iOS Device's APNS token
    */
    private function _save_apns($apns_data = false)
    {
        $result = false;
        if (!empty($apns_data)) {
            $this->load->model('Notification_model', 'notification_service');
            $save = $this->notification_service->add_apns_token($apns_data);
            if ($save) {
                $result = true;
            }
        }
        return $result;
    }

    /** Search user **/
    public function lookup_get()
    {
        $account_id  = $this->get('account_id');
        $where       = $this->get('where');
        $order_by    = $this->get('order_by');
        $limit       = (int) $this->get('limit');
        $offset      = (int) $this->get('offset');
        $user_types  = $this->get('user_types');
        $user_statuses = $this->get('user_statuses');
        $search_term = trim(urldecode($this->get('search_term')));

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid main Account ID',
                'customers' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $get_users = $this->ion_auth->user_lookup($account_id, $search_term, $user_types, $user_statuses, $where, $order_by, $limit, $offset);

        if (!empty($get_users)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'users' => $get_users
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'users' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Get a list of all available user-statuses **/
    public function statuses_get()
    {
        $account_id = (int) $this->get('account_id');
        $status_id  = (int) $this->get('status_id');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Invalid main Account ID',
                'user_statuses' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $user_statuses  = $this->ion_auth->get_user_statuses($account_id, $status_id);

        if (!empty($user_statuses)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'user_statuses' => $user_statuses
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'user_statuses' => null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }
    }
}
