<?php

namespace Application\Modules\Web\Controllers;

use Application\Extentions\MX_Controller;

class Systems extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        $this->module_id       = $this->webapp_service->_get_module_id($this->router->fetch_class());
        $this->module_access = $this->webapp_service->check_access($this->user, $this->module_id);
        $this->load->library('pagination');
        $this->load->model('serviceapp/Systems_model', 'systems_service');
    }

    public function index()
    {
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            //access denied
            $this->_render_webpage('errors/access-denied', false);
        } else {
            redirect('webapp/systems/systems', 'refresh');
        }
    }

    /** Get list of sites **/
    public function systems($system_type_id = false)
    {
        if ($system_type_id) {
            redirect('webapp/systems/profile/' . $system_type_id, 'refresh');
        }

        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $data = false;

            $data['current_user']           = $this->user;
            $data['module_id']              = $this->module_id;

            $this->_render_webpage('systems/index', $data);
        }
    }

    ## View System profile
    public function profile($system_type_id = false, $page = 'details')
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } elseif ($system_type_id) {
            $run_admin_check        = false;
            $API_call               = $this->webapp_service->api_dispatcher($this->api_end_point . 'systems/systems', ['account_id' => $this->user->account_id,'system_type_id' => $system_type_id], ['auth_token' => $this->auth_token], true);
            $data['systems_details']    = (isset($API_call->systems)) ? $API_call->systems : null;

            if (!empty($data['systems_details'])) {
                $run_admin_check    = false;
                #Get allowed access for the logged in user
                $data['permissions'] = $item_access;
                $data['active_tab'] = $page;

                $module_items       = $this->webapp_service->api_dispatcher($this->api_end_point . 'access/account_modules_items', ['account_id' => $this->user->account_id, 'module_id' => $this->module_id ], ['auth_token' => $this->auth_token], true);
                $data['module_tabs'] = (isset($module_items->module_items)) ? $module_items->module_items : null;

                switch ($page) {
                    case 'details':
                    default:
                        $postdata['account_id'] = $this->user->account_id;

                        $data['drm_types']  = $postdata = [];
                        $postdata['account_id']                 = $this->user->account_id;
                        $postdata['where']['module_id']         = 3; ## Pulled from the Content Management module $this->module_id;
                        $postdata['where']['setting_name_id']   = 12;
                        $url                                    = 'settings/settings';
                        $API_result                             = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['drm_types']                      = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->settings)) ? $API_result->settings : null;

                        $data['delivery_mechanism_types']       = $postdata = [];
                        $postdata['account_id']                 = $this->user->account_id;
                        $postdata['where']['module_id']         = $this->module_id; ## Site module
                        $postdata['where']['setting_name_id']   = 30;
                        $url                                    = 'settings/settings';
                        $API_result                             = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['delivery_mechanism_types']       = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->settings)) ? $API_result->settings : null;

                        $data['providers']  = $postdata = [];
                        $postdata['account_id']                 = $this->user->account_id;
                        $url                                    = 'systems/providers';
                        $API_result                             = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['providers']          = (isset($API_result->providers)) ? $API_result->providers : null;

                        $data['remaining_providers']    = $postdata = [];
                        $postdata['account_id']                 = $this->user->account_id;
                        $postdata['where']['system_type_id']    = $system_type_id;
                        $postdata['where']['not_added']         = 'yes';
                        $url                                    = 'systems/providers';
                        $API_result                             = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['remaining_providers']            = (isset($API_result->providers)) ? $API_result->providers : null;

                        $systems_documents                  = $this->webapp_service->api_dispatcher($this->api_end_point . 'document_handler/document_list', ['account_id' => $this->user->account_id, 'system_type_id' => $system_type_id, 'document_group' => 'systems' ], ['auth_token' => $this->auth_token], true);
                        $data['systems_documents']          = (isset($systems_documents->documents->{$this->user->account_id})) ? $systems_documents->documents->{$this->user->account_id} : null;

                        $data['include_page']   = 'systems_details.php';
                        break;
                }
            }

            //Run the admin check if tab needs only admin
            if (!empty($run_admin_check)) {
                if ((!admin_check($this->user->is_admin, false, (!empty($data['permissions']) ? $data['permissions']->is_admin : false)))) {
                    $data['admin_no_access'] = true;
                }
            }

            $this->_render_webpage('systems/profile', $data);
        } else {
            redirect('webapp/systems', 'refresh');
        }
    }

    /*
    *   Systems lookup / search
    */
    public function lookup($page = 'details')
    {
        $return_data = '';

        # Check module access
        $section = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data .= $this->config->item('ajax_access_denied');
        } else {
            # Setup search parameters
            $search_term   = ($this->input->post('search_term')) ? $this->input->post('search_term') : false;
            $limit         = ($this->input->post('limit')) ? $this->input->post('limit') : DEFAULT_LIMIT;
            $start_index   = ($this->input->post('start_index')) ? $this->input->post('start_index') : 0;
            $offset        = (!empty($start_index)) ? (($start_index - 1) * $limit) : 0;
            $order_by      = false;
            $where         = false;

            #prepare postdata
            $postdata = [
                'account_id'        => $this->user->account_id,
                'search_term'       => $search_term,
                'where'             => $where,
                'order_by'          => $order_by,
                'limit'             => $limit,
                'offset'            => $offset
            ];


            $API_call   = $this->webapp_service->api_dispatcher($this->api_end_point . 'systems/lookup', $postdata, ['auth_token' => $this->auth_token], true);
            $systems        = (isset($API_call->systems)) ? $API_call->systems : null;
            if (!empty($systems)) {
                ## Create pagination
                $counters       = $this->systems_service->get_total_systems($this->user->account_id, $search_term, $where, $order_by, $limit, $offset);//Direct access to count, this should only return a number
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

                $return_data = $this->load_systems_view($systems);
                if (!empty($pagination)) {
                    $return_data .= '<tr><td colspan="6" style="padding: 0;">';
                    $return_data .= $page_display . $pagination;
                    $return_data .= '</td></tr>';
                }
            } else {
                $return_data .= '<tr><td colspan="8">';
                $return_data .= (isset($search_result->message)) ? $search_result->message : 'No records found';
                $return_data .= '</td></tr>';
            }
        }

        print_r($return_data);
        die();
    }

    /*
    *   Prepare Systems views
    */
    private function load_systems_view($systems)
    {
        $return_data = '';

        if (!empty($systems)) {
            foreach ($systems as $k => $system_details) {
                $return_data .= '<tr>';
                $return_data .= '<td>' . $system_details->system_type_id . '</td>';
                $return_data .= '<td><a href="' . base_url('/webapp/systems/profile/' . $system_details->system_type_id) . '" >' . $system_details->name . '</a></td>';
                $return_data .= '<td>' . ((!empty($system_details->is_local_server) && ($system_details->is_local_server > 0)) ? 'Yes' : 'No') . '</td>';
                $return_data .= '<td>' . $system_details->drm_type_name . '</td>';
                $return_data .= '<td>' . ((!empty($system_details->is_approved_by_provider) && ($system_details->is_approved_by_provider > 0)) ? 'Yes' : 'No') . '</td>';
                $return_data .= '<td>' . $system_details->approval_date . '</td>';
                $return_data .= '<td>' . $system_details->provider_name . '</td>';
                $return_data .= '<td>' . $system_details->delivery_mechanism_name . '</td>';
                $return_data .= '</tr>';
            }

            if (!empty($pagination)) {
                $return_data .= '<tr><td colspan="4" style="padding: 0;">';
                $return_data .= $page_display . $pagination;
                $return_data .= '</td></tr>';
            }
        } else {
            $return_data .= '<tr><td colspan="4"><br/>' . $this->config->item("no_records") . '</td></tr>';
        }
        return $return_data;
    }


    /**
    *   Create new system
    **/
    public function create()
    {
        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'details');

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $data = false;

            $postdata['account_id'] = $this->user->account_id;

            $data['drm_types']  = $postdata = [];
            $postdata['account_id']         = $this->user->account_id;
            $postdata['where']['module_id']         = 3;  ## $this->module_id; the item list is pulled from the content module
            $postdata['where']['setting_name_id']   = 12;
            $url                            = 'settings/settings';
            $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $data['drm_types']      = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->settings)) ? $API_result->settings : null;

            $data['delivery_mechanism_types']   = $postdata = [];
            $postdata['account_id']         = $this->user->account_id;
            $postdata['where']['module_id']         = $this->module_id;
            $postdata['where']['setting_name_id']   = 30;
            $url                            = 'settings/settings';
            $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $data['delivery_mechanism_types']       = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->settings)) ? $API_result->settings : null;

            $this->_render_webpage('systems/systems_create_new', $data);
        }
    }

    public function check_reference($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = "details");

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data)) {
                $account_id = $this->user->account_id;
                $reference  = (!empty($post_data['reference'])) ? $post_data['reference'] : false ;
                $module     = (!empty($post_data['module'])) ? $post_data['module'] : false ;

                $reference_exists       = $this->ssid_common->check_reference($account_id, $reference, $module);

                if (!empty($reference_exists)) {
                    $return_data['reference']   = (isset($reference_exists) && !empty($reference_exists)) ? $reference_exists : null;
                    $return_data['status']      = true;
                    $return_data['status_msg']  = "The Reference code already exists";
                } else {
                    $return_data['status_msg']  = "This Reference Code seems to be unique";
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    /**
    *   The AJAX system creation
    **/
    public function create_system($page = "details")
    {
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $system_data = $this->input->post();
            if (!empty($system_data['system_details'])) {
                $post_data          = $this->input->post();

                $postdata           = array_merge(['account_id' => $this->user->account_id], $post_data);
                $postdata['name']   = (!empty($post_data['system_details']['name'])) ? $post_data['system_details']['name'] : false ;

                $url            = 'systems/create';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');
                $result         = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->new_system)) ? $API_result->new_system : null;

                $message        = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                if (!empty($result)) {
                    $return_data['status'] = 1;
                    $return_data['system']   = $result;
                }
                $return_data['status_msg'] = $message;
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    /**
    *   Update System Details
    **/
    public function update_system($system_type_id = false, $page = 'details')
    {
        $return_data = [
            'status' => 0
        ];

        $section        = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
        $system_type_id         = ($this->input->post('system_type_id')) ? $this->input->post('system_type_id') : $system_type_id ;

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            ;
        } else {
            $postdata   = array_merge(['account_id' => $this->user->account_id], $this->input->post());
            $API_call   = $this->webapp_service->api_dispatcher($this->api_end_point . 'systems/update', $postdata, ['auth_token' => $this->auth_token]);
            $result     = (isset($API_call->updated_system)) ? $API_call->updated_system : null;
            $message    = (isset($API_call->message)) ? $API_call->message : 'Request completed!';
            if (!empty($result)) {
                $return_data['status']      = 1;
                $return_data['upd_system']  = $result;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    public function delete_system($system_type_id = false, $page = 'details')
    {
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data) && !empty($post_data['system_type_id'])) {
                $postdata                   = [];
                $postdata['account_id']     = $this->user->account_id;
                $postdata['system_type_id']         = (!empty($post_data['system_type_id'])) ? $post_data['system_type_id'] : false ;

                $url            = 'systems/delete';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                ## d_system = deleted_system
                if (!empty($API_result)) {
                    $return_data['d_system']    = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->d_system)) ? $API_result->d_system : null;
                    $return_data['status']      = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }

    /*
    *   Delete provider attached to the system
    */
    public function delete_provider($page = 'details')
    {
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_delete) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data['provider_id'])) {
                $postdata                           = [];
                $postdata['account_id']             = $this->user->account_id;
                $postdata['where']['system_id']     = (!empty($post_data['system_id'])) ? $post_data['system_id'] : false ;
                $postdata['where']['provider_id']   = (!empty($post_data['provider_id'])) ? $post_data['provider_id'] : false ;

                $url            = 'systems/delete_provider';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result)) {
                    $return_data['systems']     = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->systems)) ? $API_result->systems : null;
                    $return_data['status']      = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    /*
    *   Add provider's approval date to the system
    */
    public function add_provider($page = 'details')
    {
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data)) {
                $postdata['account_id']         = $this->user->account_id;
                $postdata['system_type_id']     = (!empty($post_data['system_type_id'])) ? (int) $post_data['system_type_id'] : false ;
                $postdata['approval_date']      = (!empty($post_data['approval_date'])) ? $post_data['approval_date'] : false ;
                $postdata['providers']          = (!empty($post_data['providers'])) ? $post_data['providers'] : false ;

                $url            = 'systems/add_provider';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result->status) && ($API_result->status == true)) {
                    $return_data['new_provider']    = (isset($API_result->new_provider) && !empty($API_result->new_provider)) ? $API_result->new_provider : null ;
                    $return_data['status']          = 1;
                    $return_data['status_msg']      = (isset($API_result->message) && !empty($API_result->message)) ? $API_result->message : null ;
                } else {
                    $return_data['status_msg']      = (!empty($API_result->message)) ? $API_result->message : 'There was an error processing your request';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }



    /**
    *   Upload Systems files. This is a Web-client only function
    */
    public function upload_docs($system_type_id)
    {
        if (!empty($system_type_id)) {
            $postdata   = array_merge(['account_id' => $this->user->account_id], $this->input->post());
            $doc_upload = $this->document_service->upload_files($this->user->account_id, $postdata, $document_group = 'systems', $folder = 'systems');
            redirect('webapp/systems/profile/' . $system_type_id);
        } else {
            redirect('webapp/systems', 'refresh');
        }
    }




    public function delete_document($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = "details");

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data) && !empty($post_data['document_id'])) {
                $postdata                   = [];
                $postdata['account_id']     = $this->user->account_id;
                $postdata['document_id']    = (!empty($post_data['document_id'])) ? $post_data['document_id'] : false ;
                $postdata['doc_group']      = "systems";
                $url                        = 'document_handler/delete_document';
                $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                ## d_document = deleted_document
                if (!empty($API_result)) {
                    $return_data['d_document']  = (isset($API_result->status) && ($API_result->status == true)) ? $API_result->d_document : null;
                    $return_data['status']      = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }
}
