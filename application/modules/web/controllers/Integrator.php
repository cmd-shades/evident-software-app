<?php

namespace Application\Modules\Web\Controllers;

use Application\Extentions\MX_Controller;

class Integrator extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (false === $this->identity()) {
            redirect("webapp/user/login", 'refresh');
        }
        $this->module_id       = $this->webapp_service->_get_module_id($this->router->fetch_class());
        $this->load->model("serviceapp/Integrator_model", "integrator_service");
        $this->load->library('pagination');
    }


    public function index($integrator_id = false)
    {
        if ($integrator_id) {
            redirect('webapp/integrator/profile/' . $integrator_id, 'refresh');
        }

        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'details');

        if (!$this->user->is_admin && !$module_access) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $data['current_user']           = $this->user;
            $data['module_id']              = $this->module_id;

            $this->_render_webpage('integrator/index', $data);
        }
    }


    /**
    *   Create new integrator
    **/
    public function create()
    {
        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'details');

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $data = false;

            $data['si_currencies']          = $postdata = [];
            $postdata['account_id']         = $this->user->account_id;
            $postdata['where']['module_id']         = 2; ## Taken from the Site module, but also created own
            $postdata['where']['setting_name_id']   = 7; ## 'Sale Currencies'
            $url                            = 'settings/settings';
            $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $data['si_currencies']          = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->settings)) ? $API_result->settings : null;

            $data['systems']                = $postdata = [];
            $postdata['account_id']         = $this->user->account_id;
            $API_call                       = $this->webapp_service->api_dispatcher($this->api_end_point . 'systems/systems', ['account_id' => $this->user->account_id], ['auth_token' => $this->auth_token], true);
            $data['systems']                = (isset($API_call->status) && ($API_call->status == true) && !empty($API_call->systems)) ? $API_call->systems : null;

            $data['territories']        = $postdata = [];
            $postdata['account_id']     = $this->user->account_id;
            $url                        = 'content/territories';
            $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $data['territories']        = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->territories)) ? $API_result->territories : null;

            $this->_render_webpage('integrator/integrator_create', $data);
        }
    }


    public function create_integrator($page = "details")
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
                $postdata               = [];
                $post_data              = $this->input->post();

                $postdata                   = $post_data;
                $postdata['account_id']     = $this->user->account_id;
                $postdata['integrator_name'] = !empty($post_data['integrator_details']['integrator_name']) ? $post_data['integrator_details']['integrator_name'] : false;

                $url            = 'integrator/create';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result)) {
                    $return_data['integrator']      = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->new_integrator)) ? $API_result->new_integrator : null;
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


    //View integrator profile
    public function profile($integrator_id = false, $page = 'details')
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        ## Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } elseif ($integrator_id) {
            $run_admin_check            = false;

            $API_call                   = $this->webapp_service->api_dispatcher($this->api_end_point . 'integrator/integrator', ['account_id' => $this->user->account_id,'integrator_id' => $integrator_id], ['auth_token' => $this->auth_token], true);
            $data['integrator_details'] = (isset($API_call->integrator)) ? $API_call->integrator : null;

            if (!empty($data['integrator_details'])) {
                ## Get allowed access for the logged in user
                $data['permissions'] = $item_access;
                $data['active_tab'] = $page;

                $module_items       = $this->webapp_service->api_dispatcher($this->api_end_point . 'access/account_modules_items', ['account_id' => $this->user->account_id, 'module_id' => $this->module_id ], ['auth_token' => $this->auth_token], true);
                $data['module_tabs'] = (isset($module_items->module_items)) ? $module_items->module_items : null;

                switch ($page) {
                    case 'details':
                    default:
                        $data['territories']                    = $postdata = $API_result = [];
                        $postdata['account_id']                 = $this->user->account_id;
                        $url                                    = 'content/territories';
                        $API_result                             = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['territories']                    = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->territories)) ? $API_result->territories : null;

                        $data['remaining_territories']          = $postdata = $API_result = [];
                        $postdata['account_id']                 = $this->user->account_id;
                        $postdata['where']['not_added']         = 'yes';
                        $postdata['where']['integrator_id']     = $integrator_id;
                        $url                                    = 'integrator/territories';
                        $API_result                             = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['remaining_territories']          = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->territories)) ? $API_result->territories : null;

                        $data['si_currencies']                  = $postdata = $API_result = [];
                        $postdata['account_id']                 = $this->user->account_id;
                        $postdata['where']['module_id']         = 2; ## Taken from the Site module, but also created own
                        $postdata['where']['setting_name_id']   = 7; ## 'Sale Currencies'
                        $url                                    = 'settings/settings';
                        $API_result                             = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['si_currencies']                  = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->settings)) ? $API_result->settings : null;

                        $data['remaining_systems']              = $postdata = $API_result = [];
                        $postdata['account_id']                 = $this->user->account_id;
                        $postdata['where']['not_added']         = 'yes';
                        $postdata['where']['integrator_id']     = $integrator_id;
                        $url                                    = 'integrator/systems';
                        $API_result                             = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['remaining_systems']              = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->systems)) ? $API_result->systems : null;

                        $integrator_documents               = $this->webapp_service->api_dispatcher($this->api_end_point . 'document_handler/document_list', ['account_id' => $this->user->account_id, 'system_integrator_id' => $integrator_id, 'document_group' => 'integrator' ], ['auth_token' => $this->auth_token], true);
                        $data['integrator_documents']       = (isset($integrator_documents->documents->{$this->user->account_id})) ? $integrator_documents->documents->{$this->user->account_id} : null;

                        $data['include_page']       = 'integrator_details.php';
                        break;
                }
            }

            ## Run the admin check if tab needs only admin
            if (!empty($run_admin_check)) {
                if ((!admin_check($this->user->is_admin, false, (!empty($data['permissions']) ? $data['permissions']->is_admin : false)))) {
                    $data['admin_no_access'] = true;
                }
            }

            $this->_render_webpage('integrator/profile', $data);
        } else {
            redirect('webapp/integrator', 'refresh');
        }
    }



    public function update($page = "details")
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

            if (!empty($post_data) && !empty($post_data['system_integrator_id'])) {
                $postdata                       = [];
                $postdata['account_id']         = $this->user->account_id;
                $postdata['integrator_data']    = $post_data['integrator_details'];
                $postdata['integrator_id']      = (!empty($post_data['system_integrator_id'])) ? $post_data['system_integrator_id'] : false ;

                $url            = 'integrator/update';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                ## u_integrator = updated_integrator
                if (!empty($API_result)) {
                    $return_data['u_integrator']    = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->u_integrator)) ? $API_result->u_integrator : null;
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


    public function lookup()
    {
        $return_data = '';

        # Check module access
        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section = "details");

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data .= $this->config->item('ajax_access_denied');
        } else {
            # Setup search parameters
            $search_term        = ($this->input->post('search_term')) ? $this->input->post('search_term') : false;
            $limit              = ($this->input->post('limit')) ? $this->input->post('limit') : DEFAULT_LIMIT;
            $start_index        = ($this->input->post('start_index')) ? $this->input->post('start_index') : 0;
            $offset             = (!empty($start_index)) ? (($start_index - 1) * $limit) : 0;
            $order_by           = false;
            $where              = false;

            #prepare post data
            $postdata = [
                'account_id'        => $this->user->account_id,
                'search_term'       => $search_term,
                'order_by'          => $order_by,
                'limit'             => $limit,
                'offset'            => $offset,
                'where'             => $where,
            ];

            $API_call       = $this->webapp_service->api_dispatcher($this->api_end_point . 'integrator/lookup', $postdata, ['auth_token' => $this->auth_token], true);
            $integrator     = (isset($API_call->integrator)) ? $API_call->integrator : null;

            if (!empty($integrator)) {
                ## Create pagination
                $counters       = $this->integrator_service->get_total_integrator($this->user->account_id, $search_term, $where);
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

                $return_data = $this->load_integrator_view($integrator);
                if (!empty($pagination)) {
                    $return_data .= '<tr><td colspan="8" style="padding: 0;">';
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
    *   Prepare content view
    */
    private function load_integrator_view($integrator_data = false)
    {
        $return_data = '';

        if (!empty($integrator_data)) {
            foreach ($integrator_data as $k => $row) {
                $return_data .= '<tr>';
                $return_data .= '<td>' . (!empty($row->system_integrator_id) ? $row->system_integrator_id : '') . '</td>';
                $return_data .= '<td><a href="' . base_url('/webapp/integrator/profile/' . $row->system_integrator_id) . '" >' . (!empty($row->integrator_name) ? $row->integrator_name : '') . '</a></td>';
                $return_data .= '<td>' . (!empty($row->integrator_email) ? $row->integrator_email : '') . '</td>';
                $return_data .= '<td>' . (validate_date($row->start_date) ? format_date_client($row->start_date) : '') . '</td>';
                $return_data .= '<td>' . (!empty($row->invoice_currency_name) ? $row->invoice_currency_name : '') . '</td>';
                $return_data .= '</tr>';
            }

            if (!empty($pagination)) {
                $return_data .= '<tr><td colspan="4" style="padding: 0;">';
                $return_data .= $page_display . $pagination;
                $return_data .= '</td></tr>';
            }
        } else {
            $return_data .= '<tr><td colspan="5"><br/>' . $this->config->item("no_records") . '</td></tr>';
        }
        return $return_data;
    }


    public function delete_integrator($page = "details")
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
            if (!empty($post_data) && !empty($post_data['system_integrator_id'])) {
                $postdata                   = [];
                $postdata['account_id']     = $this->user->account_id;
                $postdata['integrator_id']  = (!empty($post_data['system_integrator_id'])) ? $post_data['system_integrator_id'] : false ;

                $url            = 'integrator/delete';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                ## d_integrator = deleted_integrator
                if (!empty($API_result)) {
                    $return_data['d_integrator']    = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->d_integrator)) ? $API_result->d_integrator : null;
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


    public function update_address()
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

        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data) && !empty($post_data['address_id'])) {
                $postdata                   = [];
                $postdata['account_id']     = $this->user->account_id;
                $postdata['address_id']     = (!empty($post_data['address_id'])) ? $post_data['address_id'] : false ;
                $postdata['address_data']   = (!empty($post_data)) ? $post_data : false ;

                $url            = 'integrator/update_address';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result)) {
                    $return_data['u_address']   = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->u_address)) ? $API_result->u_address : null;
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


    public function add_territory($page = "details")
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
                $postdata['account_id']             = $this->user->account_id;
                $postdata['integrator_id']          = (!empty($post_data['integrator_id'])) ? (int) $post_data['integrator_id'] : false ;
                $postdata['territories']            = (!empty($post_data['territories'])) ? $post_data['territories'] : false ;

                $url            = 'integrator/add_territory';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result->status) && ($API_result->status == true)) {
                    $return_data['new_territory']   = (isset($API_result->new_territory) && !empty($API_result->new_territory)) ? $API_result->new_territory : null ;
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


    /*
    *   Function to delete a clearance entry
    */
    public function delete_territory($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = "details");

        if (!$this->user->is_admin && empty($item_access->can_delete) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data)) {
                $postdata['account_id']     = $this->user->account_id;
                $postdata['territory_id']   = (!empty($post_data['territory_id'])) ? (int) $post_data['territory_id'] : false ;
                $url                        = 'integrator/delete_territory';

                $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                ## d_clearance - deleted clearance
                if (!empty($API_result->status) && ($API_result->status == true)) {
                    $return_data['d_territory']     = (!empty($API_result->d_territory)) ? $API_result->d_territory : null ;
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



    public function add_system($page = "details")
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
                $postdata['account_id']             = $this->user->account_id;
                $postdata['integrator_id']          = (!empty($post_data['integrator_id'])) ? (int) $post_data['integrator_id'] : false ;
                $postdata['integrator_systems']     = (!empty($post_data['integrator_systems'])) ? $post_data['integrator_systems'] : false ;

                $url            = 'integrator/add_system';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result->status) && ($API_result->status == true)) {
                    $return_data['new_system']  = (isset($API_result->new_system) && !empty($API_result->new_system)) ? $API_result->new_system : null ;
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


    /*
    *   Function to delete a system entry
    */
    public function delete_system($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = "details");

        if (!$this->user->is_admin && empty($item_access->can_delete) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data)) {
                $postdata['account_id']     = $this->user->account_id;
                $postdata['system_id']      = (!empty($post_data['system_id'])) ? (int) $post_data['system_id'] : false ;
                $url                        = 'integrator/delete_system';

                $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                ## d_clearance - deleted system
                if (!empty($API_result->status) && ($API_result->status == true)) {
                    $return_data['d_system']        = (!empty($API_result->d_system)) ? $API_result->d_system : null ;
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
    *   Upload Integrator files
    */
    public function upload_docs($integrator_id)
    {
        if (!empty($integrator_id)) {
            $postdata   = array_merge(['account_id' => $this->user->account_id], $this->input->post());
            $doc_upload = $this->document_service->upload_files($this->user->account_id, $postdata, $document_group = 'integrator', $folder = 'integrator');
            redirect('webapp/integrator/profile/' . $integrator_id);
        } else {
            redirect('webapp/integrator', 'refresh');
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
                $postdata['doc_group']      = "integrator";
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


    /*
    *   Function to disable the integrator with given date
    */
    public function disable($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = "details");

        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data)) {
                $postdata['account_id']         = $this->user->account_id;
                $postdata['integrator_id']      = (!empty($post_data['integrator_id'])) ? $post_data['integrator_id'] : false ;
                $postdata['disable_date']       = (!empty($post_data['disable_date'])) ? $post_data['disable_date'] : false ;

                $url                = 'integrator/disable';
                $API_result         = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result)) {
                    $return_data['integrator']  = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->integrator)) ? $API_result->integrator : null;
                    $return_data['status']          = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']      = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']      = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }
}
