<?php

namespace Application\Modules\Web\Controllers;

use Application\Extentions\MX_Controller;

class User extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->module_id       = $this->_get_module_id();
        $this->load->library('pagination');
        $this->load->model('serviceapp/Modules_model', 'module_service');
    }

    //redirect if needed, otherwise display the user list
    public function index()
    {
        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            //access denied
            $this->_render_webpage('errors/access-denied', false);
        } else {
            #$this->users();
            redirect('webapp/user/users', 'refresh');
        }
    }

    //log the user in
    public function login()
    {
        $data = "";

        $post_data = $this->input->post();
        if (!empty($post_data)) {
            if (!empty($post_data['username']) && !empty($post_data['password'])) {
                $url      = $this->api_end_point . 'user/login';
                $postdata = $this->ssid_common->_prepare_curl_post_data($post_data);
                $response = $this->ssid_common->doCurl($url, $postdata);

                if (isset($response->auth_token) && !empty($response->auth_token)) {
                    //Everything is good, please proceed to home
                    $this->session->set_userdata('auth_data', $response);

                    if (!empty($response->user->change_password) && ($response->user->change_password == 1)) {
                        //Password change is required
                        $this->session->set_flashdata('username', $post_data['username']);
                        $this->session->set_flashdata('message', $response->message);
                        redirect('webapp/user/change_password', 'refresh');
                    } else {
                        redirect('/webapp/home', 'refresh');
                    }
                } elseif (isset($response->message) && !empty($response->message)) {
                    // $this->session->set_flashdata('message', $this->ion_auth->errors());
                    $this->session->set_flashdata('username', $post_data['username']);
                    $this->session->set_flashdata('message', $response->message);
                    redirect('webapp/user/login', 'refresh');
                ## $this->_render_webpage( 'user/login', $data );
                } else {
                    $this->session->set_flashdata('username', $post_data['username']);
                    redirect('webapp/user/login', 'refresh');
                }
            } else {
                $this->session->set_flashdata('message', ((!empty($post_data['username'])) ? "The User Password is missing" : (!empty($post_data['password']) ? "The User Name is missing" : "The Password and the User Name are missing")));
                $this->session->set_flashdata('username', ((!empty($post_data['username'])) ? $post_data['username'] : null));

                ## Log out current session
                $this->session->sess_destroy();

                $this->_render_webpage('user/login', $data);
            }
        } else {
            $this->_render_webpage('user/login', $data);
        }
    }

    /** Get list of users **/
    public function users($user_id = false)
    {
        if ($user_id) {
            redirect('webapp/user/profile/' . $user_id, 'refresh');
        }

        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        #Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $user_types             = $this->webapp_service->api_dispatcher($this->api_end_point . 'user/user_types', ['account_id' => $this->user->account_id], false, true);
            $data['user_types']     = (isset($user_types->user_types)) ? $user_types->user_types : null;
            $data['current_user']   = $this->user;

            $user_statuses          = $this->webapp_service->api_dispatcher($this->api_end_point . 'user/statuses', ['account_id' => $this->user->account_id], ['auth_token' => $this->auth_token], true);
            $data['user_statuses']  = (isset($user_statuses->user_statuses)) ? $user_statuses->user_statuses : null;

            $this->_render_webpage('user/index', $data);
        }
    }

    //View user profile
    public function profile($user_id = false, $page = 'details')
    {
        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } elseif ($user_id) {
            $account_modules        = $this->webapp_service->api_dispatcher($this->api_end_point . 'user/account_modules', ['account_id' => $this->user->account_id,'inc_mod_item' => 0,'categorized' => 1], ['auth_token' => $this->auth_token], true);
            $user_details           = $this->webapp_service->api_dispatcher($this->api_end_point . 'user/users', ['account_id' => $this->user->account_id,'id' => $user_id], ['auth_token' => $this->auth_token], true);
            $data['user_details']   = (isset($user_details->user)) ? $user_details->user : null;
            if (!empty($data['user_details'])) {
                $run_admin_check    = false;
                #Get allowed access for the logged in user
                $data['permissions'] = $item_access;
                $data['active_tab'] = $page;

                $module_items       = $this->webapp_service->api_dispatcher($this->api_end_point . 'access/account_modules_items', ['account_id' => $this->user->account_id, 'module_id' => $this->module_id ], ['auth_token' => $this->auth_token], true);
                $data['module_tabs'] = (isset($module_items->module_items)) ? $module_items->module_items : null;
            }

            switch ($page) {
                case 'permissions':
                    $run_admin_check            = true;
                    $data['account_modules'] = (isset($account_modules->account_modules)) ? $account_modules->account_modules : null;
                    $data['include_page']    = 'user_permissions.php';
                    break;
                case 'addresses':
                    $run_admin_check      = true;
                    $data['include_page'] = 'user_addresses.php';
                    break;
                case 'confidential':
                    $run_admin_check      = true;
                    $data['include_page'] = 'user_confidential.php';
                    break;
                case 'details':
                default:
                    $user_types             = $this->webapp_service->api_dispatcher($this->api_end_point . 'user/user_types', ['account_id' => $this->user->account_id], false, true);
                    $data['user_types']     = (isset($user_types->user_types)) ? $user_types->user_types : null;

                    $data['include_page'] = 'user_details.php';
                    break;
            }

            //Run the admin check if tab needs only admin
            if (!empty($run_admin_check)) {
                if ((!admin_check($this->user->is_admin, false, (!empty($data['permissions']) ? $data['permissions']->is_admin : false)))) {
                    $data['admin_no_access'] = true;
                }
            }

            $this->_render_webpage('user/profile', $data);
        } else {
            redirect('webapp/user', 'refresh');
        }
    }

    /** Update user permissions **/
    public function update_user_permissions($id = false, $page = 'permissions')
    {
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $return_data = [
            'status' => 0
        ];
        $user_id = ($this->input->post('id')) ? $this->input->post('id') : (!empty($id) ? $id : null);

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            ;
        } else {
            $postdata     = array_merge(['account_id' => $this->user->account_id], $this->input->post());
            $updates_perms = $this->webapp_service->api_dispatcher($this->api_end_point . 'user/permissions', $postdata, ['auth_token' => $this->auth_token]);
            $result       = (isset($updates_perms->permissions)) ? $updates_perms->permissions : null;
            $message      = (isset($updates_perms->message)) ? $updates_perms->message : 'Request completed!';
            if (!empty($result)) {
                $return_data['status'] = 1;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }

    /** Update user details **/
    public function update_user($id = false)
    {
        $return_data = [
            'status' => 0
        ];
        $user_id = ($this->input->post('id')) ? $this->input->post('id') : (!empty($id) ? $id : null);
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : 'details');

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postdata     = array_merge(['account_id' => $this->user->account_id], $this->input->post());

            $updates_user = $this->webapp_service->api_dispatcher($this->api_end_point . 'user/update', $postdata, ['auth_token' => $this->auth_token]);
            $result       = (isset($updates_user->user)) ? $updates_user->user : null;
            $message      = (isset($updates_user->message)) ? $updates_user->message : 'Request completed!';
            if (!empty($result)) {
                $return_data['status'] = 1;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }

    //log the user out
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('/webapp/user/login', 'refresh');
    }

    //change password
    public function change_password()
    {
        $data = false;
        $this->_render_webpage('user/change_password', $data);
    }

    //forgot password
    public function forgot_password()
    {
        $data = false;
        $this->_render_webpage('user/forgot_password', $data);
    }

    //reset password - final step for forgotten password
    public function reset_password($code = null)
    {
        $data = false;
        $this->_render_webpage('user/reset_password', $data);
    }

    //activate the user
    public function activate($id, $code = false)
    {
        $data = false;
        $this->_render_webpage('user/activate', $data);
    }

    //deactivate the user
    public function deactivate($id = null)
    {
        $data = false;
        $this->_render_webpage('user/deactivate', $data);
    }

    //create a new user
    public function create($page = 'details')
    {
        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $user_types             = $this->webapp_service->api_dispatcher($this->api_end_point . 'user/user_types', ['account_id' => $this->user->account_id], false, true);
            $data['user_types']     = (isset($user_types->user_types)) ? $user_types->user_types : null;

            $account_modules        = $this->webapp_service->api_dispatcher($this->api_end_point . 'user/account_modules', ['account_id' => $this->user->account_id,'inc_mod_item' => 1], ['auth_token' => $this->auth_token], true);
            $data['account_modules'] = (isset($account_modules->account_modules)) ? $account_modules->account_modules : null;


            $this->_render_webpage('user/create_user', $data);
        }
    }

    //create new user
    public function create_user($page = 'details')
    {
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            ;
        } else {
            $postdata     = array_merge(['account_id' => $this->user->account_id], $this->input->post());
            $new_user     = $this->webapp_service->api_dispatcher($this->api_end_point . 'user/register', $postdata, ['auth_token' => $this->auth_token]);
            $result       = (isset($new_user->user)) ? $new_user->user : null;
            $message      = (isset($new_user->message)) ? $new_user->message : 'Request completed!';
            if (!empty($result)) {
                $return_data['status'] = 1;
                $return_data['user']   = $new_user;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }

    public function _get_csrf_nonce()
    {
        $this->load->helper('string');
        $key   = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $this->session->set_flashdata('csrfkey', $key);
        $this->session->set_flashdata('csrfvalue', $value);
        return array($key => $value);
    }

    public function _valid_csrf_nonce()
    {
        if ($this->input->post($this->session->flashdata('csrfkey')) !== false && $this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue')) {
            return true;
        } else {
            return false;
        }
    }

    private function _get_module_id()
    {
        $result     = false;
        $module_id  = $this->webapp_service->get_module_id_by_controller($this->router->fetch_class());
        if (!empty($module_id)) {
            $result = (!is_array($module_id)) ? $module_id : $module_id[$this->router->fetch_class()];
        }
        return $result;
    }

    private function _check_module_role($module_id = false)
    {
        $result = false;
        if ($module_id) {
            $permissions = (isset($this->user->permissions->{$module_id})) ? $this->user->permissions->{$module_id}->permissions : false;
            $result      = ($permissions && in_array($module_id . '_admin', $permissions)) ? true : false;
        }
        return $result;
    }

    /**
    *   Delete user (set as archived )
    **/
    public function delete_user($user_id = false, $page = 'details')
    {
        $return_data = [
            'status' => 0
        ];

        $section    = ($this->input->post('page')) ? $this->input->post('page') : (!empty($page) ? $page : "details");
        $user_id    = ($this->input->post('user_id')) ? $this->input->post('user_id') : (!empty($user_id) ? $user_id : null);

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_delete) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postdata     = array_merge(['account_id' => $this->user->account_id], $this->input->post());
            $delete_people = $this->webapp_service->api_dispatcher($this->api_end_point . 'user/delete', $postdata, ['auth_token' => $this->auth_token], true);
            $result       = (isset($delete_people->status)) ? $delete_people->status : null;
            $message      = (isset($delete_people->message)) ? $delete_people->message : 'Something went wrong!';
            if (!empty($result)) {
                $return_data['status'] = 1;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }

    /*
    * User lookup / search
    */
    public function lookup()
    {
        $return_data = '';

        if (!$this->identity()) {
            $return_data .= 'Access denied! Please login';
        }

        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            $return_data .= $this->config->item('ajax_access_denied');
        } else {
            # Setup search parameters
            $search_term   = ($this->input->post('search_term')) ? $this->input->post('search_term') : false;
            $user_types    = ($this->input->post('user_types')) ? $this->input->post('user_types') : false;
            $user_statuses = ($this->input->post('user_statuses')) ? $this->input->post('user_statuses') : false;
            $limit         = ($this->input->post('limit')) ? $this->input->post('limit') : DEFAULT_LIMIT;
            $start_index   = ($this->input->post('start_index')) ? $this->input->post('start_index') : 0;
            $offset        = (!empty($start_index)) ? (($start_index - 1) * $limit) : 0;
            $order_by      = false;
            $where         = false;

            #prepare postdata
            $postdata = [
                'account_id' => $this->user->account_id,
                'search_term' => $search_term,
                'user_types' => $user_types,
                'user_statuses' => $user_statuses,
                'where' => $where,
                'order_by' => $order_by,
                'limit' => $limit,
                'offset' => $offset
            ];

            $search_result  = $this->webapp_service->api_dispatcher($this->api_end_point . 'user/lookup', $postdata, ['auth_token' => $this->auth_token], true);
            $users          = (isset($search_result->users)) ? $search_result->users : null;

            if (!empty($users)) {
                ## Create pagination
                $counters       = $this->ion_auth->get_total_users($this->user->account_id, $search_term, $user_types, $user_statuses, $where, $limit, $offset);//Direct access to count, this should only return a number
                $page_number    = ($start_index > 0) ? $start_index : 1;
                $page_display   = '<span style="margin:15px 0px;" class="pull-left">Page <strong>' . $page_number . '</strong> of <strong>' . $counters->pages . '</strong></span>';

                if ($counters->total > 0) {
                    $config['total_rows']   = $counters->total;
                    $config['per_page']     = $limit;
                    $config['current_page'] = $page_number;
                    $pagination_setup       = _pagination_config();
                    $config                 = array_merge($config, $pagination_setup);
                    $this->pagination->initialize($config);
                    $pagination             = $this->pagination->create_links();
                }

                $return_data = $this->load_users_view($users);
                if (!empty($pagination)) {
                    $return_data .= '<tr><td colspan="5" style="padding: 0;">';
                    $return_data .= $page_display . $pagination;
                    $return_data .= '</td></tr>';
                }
            } else {
                $return_data .= '<br/>';
                $return_data .= (isset($search_result->message)) ? $search_result->message : 'No records found';
            }
        }

        print_r($return_data);
        die();
    }

    /*
    * Prepare Users views
    */
    private function load_users_view($users_data)
    {
        $return_data = '';
        if (!empty($users_data)) {
            foreach ($users_data as $k => $user_details) {
                $return_data .= '<tr>';
                $return_data .= '<td>' . $user_details->id . '</td>';
                $return_data .= '<td><a href="' . base_url('/webapp/user/profile/' . $user_details->id) . '" >' . ucwords(strtolower((!empty($user_details->first_name) ? $user_details->first_name . " " : "") . $user_details->last_name)) . '</a></td>';
                $return_data .= '<td>' . $user_details->email . '</td>';
                $return_data .= '<td>' . $user_details->username . '</td>';
                $return_data .= '<td>' . $user_details->user_type_name . '</td>';
                $return_data .= '<td>' . (($user_details->active == 1) ? 'Active' : 'In-active') . '</td>';
                #$return_data .= '<td><span class="checkbox text-center" style="margin:0"><label><input type="checkbox" name="user_ids[]" value="'.$user_details->id.'"/></label></span></td>';
                $return_data .= '</tr>';
            }

            if (!empty($pagination)) {
                $return_data .= '<tr><td colspan="5" style="padding: 0;">';
                $return_data .= $page_display . $pagination;
                $return_data .= '</td></tr>';
            }
        } else {
            $return_data .= '<tr><td colspan="5"><br/>' . $this->config->item("no_records") . '</td></tr>';
        }
        return $return_data;
    }

    /*
    * Load a audit record
    */
    public function module_items_list($user_id = false, $module_id = false)
    {
        $user_id    = ($this->input->post('user_id')) ? $this->input->post('user_id') : (!empty($user_id) ? $user_id : null);
        $module_id  = ($this->input->post('module_id')) ? $this->input->post('module_id') : (!empty($module_id) ? $module_id : null);

        $return_data = [
            'status' => 0,
            'module_items' => null,
            'status_msg' => 'Invalid paramaters'
        ];

        if (!empty($module_id)) {
            $module_access      = $this->module_service->get_module_access($this->user->account_id, $user_id, $module_id);

            $module_permissions = (!empty($module_access[0])) ? (is_array($module_access[0]) ? array_to_object($module_access[0]) : $module_access[0]) : [];

            $module_items = $this->webapp_service->api_dispatcher($this->api_end_point . 'access/account_modules_items', ['account_id' => $this->user->account_id,'module_id' => $module_id, 'detailed' => 1 ], ['auth_token' => $this->auth_token], true);
            $result     = (!empty($module_items->module_items)) ? $module_items->module_items : null;
            $message    = (isset($module_items->message)) ? $module_items->message : 'Something went wrong!';
            if (!empty($result)) {
                $allowed_items      = $this->webapp_service->api_dispatcher($this->api_end_point . 'access/module_item_permissions', ['account_id' => $this->user->account_id,'module_id' => $module_id, 'user_id' => $user_id, 'as_list' => 1], ['auth_token' => $this->auth_token]);
                $permitted_actions  = (!empty($allowed_items->mod_item_access)) ? $allowed_items->mod_item_access : null;
                $module = $this->load_module_permissions($result, $module_permissions, $permitted_actions);
                $return_data['status']       = 1;
                $return_data['module_items'] = $module;
            }
            $return_data['status_msg'] = $message;
        }
        print_r(json_encode($return_data));
        die();
    }

    private function load_module_permissions($module_record = false, $module_permissions = false, $permitted_actions = false)
    {
        $module = '';
        if (!empty($module_record)) {
            $moduel_id  = $module_record->module_details->module_id;
            $module     = '<div class="module-access-grp" >';
            $module     .= '<table style="width:100%">';
            $checked_perms = (!empty($permitted_actions->{$moduel_id})) ? object_to_array($permitted_actions->{$moduel_id}) : [];
            $can_view   = $can_view = $can_view = $can_view = $can_view =
            //$item_perms = ( !empty( $permitted_actions[] ) )
                    #$module .= '<tr><th width="30%">Module ID</th><td>'.$module_record->module_details->module_id.'</td></tr>';
                    $module .= '<tr>';
            $module .= '<th colspan="2" width="100%">';
            $module .= '<span id="feedback_message" style="font-weight:400;color:green" class="pull-right"></span>';
            $module .= '<table width="100%">';
            $module .= '<tr>';
            $module .= '<td width="50%">' . $module_record->module_details->module_name . '</td>';
            $module .= '<input type="hidden" name="permissions[' . $module_record->module_details->module_id . '][module_access][module_id]" value="' . $module_record->module_details->module_id . '" >';
            $module .= '<input type="hidden" name="permissions[' . $module_record->module_details->module_id . '][module_access][has_access]" value="0" >';
            $module .= '<td width="30%"><div class="checkbox"><label><input class="module-access grant-access" type="checkbox" name="permissions[' . $module_record->module_details->module_id . '][module_access][has_access]" value="1" data-module_id="' . $module_record->module_details->module_id . '" ' . (!empty($module_permissions->has_access) ? 'checked' : '') . ' > <em>Grant/Revoke Access</em></label></div></td>';
            $module .= '<input type="hidden" name="permissions[' . $module_record->module_details->module_id . '][module_access][is_module_admin]" value="0" >';
            $module .= '<td width="20%"><div class="checkbox pull-right"><label><input class="module-access grant-admin-access" type="checkbox" name="permissions[' . $module_record->module_details->module_id . '][module_access][is_module_admin]" value="1" data-module_id="' . $module_record->module_details->module_id . '" ' . (!empty($module_permissions->is_module_admin) ? 'checked' : '') . ' > <em>Is Admin</em></label></div></td>';
            $module .= '</tr>';
            $module .= '</table>';

            // $module .= '<div classs="pull-left">'.$module_record->module_details->module_name.'</div>';
            // $module .= '<span class="pull-right">';
            // $module .= '<div class="row">';
            // $module .= '<div class="col-md-12 col-sm-12 col-xs-12 checkbox"><label><input class="" type="checkbox" name="" > <em>Grant Access</em></label></div>';
            // $module .= '</div>';
            // $module .= '</span>';
            $module .= '</th>';
            $module .= '</tr>';

            $module .= '<tr><th colspan="2" style="text-align:center"><hr></th></tr>';
            #$module .= '<tr><th colspan="2" style="text-align:center">Tab Access Permission</th></tr>';
            foreach ($module_record->module_items as $k => $item) {
                $mod_item_id = $item->module_item_id;

                $module .= '<tr>';
                $module .= '<th width="18%"><span title="Access permission to the ' . ucwords($item->module_item_name) . ' tab ">' . ucwords($item->module_item_name) . '</span></th>';
                $module .= '<td>';
                $module .= '<div class="row mod-item-grp' . $item->module_item_id . '">';
                $module .= '<input type="hidden" name="[module_id]" value="' . $module_record->module_details->module_id . '" >';

                $module .= '<input type="hidden" name="permissions[' . $module_record->module_details->module_id . '][' . $item->module_item_id . '][can_view]" value="0" >';

                $module .= '<input type="hidden" name="permissions[' . $module_record->module_details->module_id . '][' . $item->module_item_id . '][can_view]" value="0" >';
                $module .= '<div class="col-md-2 col-sm-4 col-xs-12 checkbox"><label><input class="view mod-item-chk perms_' . $k . '_' . $item->module_item_id . ' " type="checkbox" name="permissions[' . $module_record->module_details->module_id . '][' . $item->module_item_id . '][can_view]" value="1" data-mod_item_class="perms_' . $k . '_' . $item->module_item_id . '" data-module_id="' . $item->module_id . '" data-module_item_id="' . $item->module_item_id . '" ' . (!empty($checked_perms[$mod_item_id]['can_view']) ? 'checked' : '') . ' > <em>View</em></label></div>';

                $module .= '<input type="hidden" name="permissions[' . $module_record->module_details->module_id . '][' . $item->module_item_id . '][can_add]" value="0" >';
                $module .= '<div class="col-md-2 col-sm-4 col-xs-12 checkbox"><label><input class="mod-item-chk perms_' . $k . '_' . $item->module_item_id . ' " type="checkbox" name="permissions[' . $module_record->module_details->module_id . '][' . $item->module_item_id . '][can_add]" value="1" data-mod_item_class="perms_' . $k . '_' . $item->module_item_id . '" data-module_id="' . $item->module_id . '" data-module_item_id="' . $item->module_item_id . '" ' . (!empty($checked_perms[$mod_item_id]['can_add']) ? 'checked' : '') . ' > <em>Add</em></label></div>';

                $module .= '<input type="hidden" name="permissions[' . $module_record->module_details->module_id . '][' . $item->module_item_id . '][can_edit]" value="0" >';
                $module .= '<div class="col-md-2 col-sm-4 col-xs-12 checkbox"><label><input class="mod-item-chk perms_' . $k . '_' . $item->module_item_id . ' " type="checkbox" name="permissions[' . $module_record->module_details->module_id . '][' . $item->module_item_id . '][can_edit]" value="1" data-mod_item_class="perms_' . $k . '_' . $item->module_item_id . '" data-module_id="' . $item->module_id . '" data-module_item_id="' . $item->module_item_id . '" ' . (!empty($checked_perms[$mod_item_id]['can_edit']) ? 'checked' : '') . '  > <em>Edit</em></label></div>';

                $module .= '<input type="hidden" name="permissions[' . $module_record->module_details->module_id . '][' . $item->module_item_id . '][can_delete]" value="0" >';
                $module .= '<div class="col-md-2 col-sm-4 col-xs-12 checkbox"><label><input class="mod-item-chk perms_' . $k . '_' . $item->module_item_id . ' " type="checkbox" name="permissions[' . $module_record->module_details->module_id . '][' . $item->module_item_id . '][can_delete]" value="1" data-mod_item_class="perms_' . $k . '_' . $item->module_item_id . '" data-module_id="' . $item->module_id . '" data-module_item_id="' . $item->module_item_id . '" ' . (!empty($checked_perms[$mod_item_id]['can_delete']) ? 'checked' : '') . '  > <em>Delete</em></label></div>';

                $module .= '<input type="hidden" name="permissions[' . $module_record->module_details->module_id . '][' . $item->module_item_id . '][is_admin]" value="0" >';
                $module .= '<div class="col-md-3 col-sm-4 col-xs-12 checkbox"><label><input class="mod-item-chk perms_' . $k . '_' . $item->module_item_id . ' check-all" id="perms_' . $k . '_' . $item->module_item_id . '" type="checkbox" name="permissions[' . $module_record->module_details->module_id . '][' . $item->module_item_id . '][is_admin]" value="1" data-mod_item_class="perms_' . $k . '_' . $item->module_item_id . '" data-module_id="' . $item->module_id . '" data-module_item_id="' . $item->module_item_id . '" ' . (!empty($checked_perms[$mod_item_id]['is_admin']) ? 'checked' : '') . '  > <em>Full Access</em></label></div>';
                $module .= '</td>';
                $module .= '</td>';
                $module .= '</tr>';
            }
            $module .= '</table>';
            $module .= '</div>';
        }
        return $module;
    }

    /** Update Module Permissions **/
    public function update_module_permissions($user_id = false, $module_id = false)
    {
        $data['status'] = 0;
        $data['status_msg']     = 'Hello one';

        if (!empty($user_id)) {
            $data   = ($this->input->post()) ? $this->input->post() : null;

            if (!empty($data)) {
                $postdata     = array_merge(['account_id' => $this->user->account_id, 'user_id' => $user_id ], $this->input->post());

                $update_perms = $this->webapp_service->api_dispatcher($this->api_end_point . 'access/update_module_permissions', $postdata, ['auth_token' => $this->auth_token]);

                $result       = (!empty($update_perms->module_permissions)) ? $update_perms->module_permissions : null;
                $message      = (isset($update_perms->message)) ? $update_perms->message : 'Something went wrong!';

                if (!empty($result)) {
                    $data['status']     = 1;
                    $data['status_msg'] = $message;
                }
            }
        }
        print_r(json_encode($data));
        die();
    }
}
