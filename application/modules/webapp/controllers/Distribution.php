<?php

namespace Application\Modules\Web\Controllers;

defined('BASEPATH') || exit('No direct script access allowed');

use Application\Extentions\MX_Controller;

class Distribution extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (false === $this->identity()) {
            redirect("webapp/user/login", 'refresh');
        }

        $this->module_id       = $this->webapp_service->_get_module_id($this->router->fetch_class());
        $this->load->model('serviceapp/Distribution_model', 'distribution_service');
        $this->load->library('pagination');
    }

    //Redirect if needed
    public function index()
    {
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            //access denied
            $this->_render_webpage('errors/access-denied', false);
        } else {
            redirect('webapp/distribution/distributions', 'refresh');
        }
    }

    /** Distribution Groups Overview **/
    public function distributions($distribution_group_id = false)
    {
        if ($distribution_group_id) {
            redirect('webapp/distribution/profile/' . $distribution_group_id, 'refresh');
        }

        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $data['current_user']       = $this->user;
            $data['module_id']          = $this->module_id;
            $this->_render_webpage('distribution/index', $data);
        }
    }


    /**
    *   Create New Distribution Group
    **/
    public function create_group()
    {
        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'details');

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $territories            = $this->webapp_service->api_dispatcher($this->api_end_point . 'content/territories', ['account_id' => $this->user->account_id ], ['auth_token' => $this->auth_token], true);
            $data['territories']    = (isset($territories->status) && ($territories->status == true) && !empty($territories->territories)) ? $territories->territories : null;

            $system_integrators         = $this->webapp_service->api_dispatcher($this->api_end_point . 'integrator/lookup', ['account_id' => $this->user->account_id, 'limit' => -1 ], ['auth_token' => $this->auth_token], true);
            $data['system_integrators'] = (isset($system_integrators->status) && ($system_integrators->status == true) && !empty($system_integrators->integrator)) ? $system_integrators->integrator : null;

            $params['account_id']                     = $this->user->account_id;
            $params['where']['module_id']             = 2; ## Site module
            $params['where']['setting_name_group']    = '2_no_of_titles'; ## 'no of titles'

            $no_of_titles_packages          = $this->webapp_service->api_dispatcher($this->api_end_point . 'settings/settings', $params, ['auth_token' => $this->auth_token], true);
            $data['no_of_titles_packages']  = (isset($no_of_titles_packages->status) && ($no_of_titles_packages->status == true) && !empty($no_of_titles_packages->settings)) ? $no_of_titles_packages->settings : null;


            ## delivery points - delivery servers taken from Cacti with the current, updated 'running' value field
            $postdata                   = [];
            $postdata['account_id']     = $this->user->account_id;
            $postdata['where']['check_running'] = 'yes';
            $url                        = 'distribution/distribution_server';
            $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $data['distribution_servers']   = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->d_servers)) ? $API_result->d_servers : null;


            $fpm_params = [
                'account_id' => $this->user->account_id,
                'where' => [
                    'module_id'             => 2,
                    'setting_name_group'    => '2_films_per_month'
                ]
            ];
            $films_per_month            = $this->webapp_service->api_dispatcher($this->api_end_point . 'settings/settings', $fpm_params, ['auth_token' => $this->auth_token], true);
            $data['films_per_month']    = (isset($films_per_month->status) && ($films_per_month->status == true) && !empty($films_per_month->settings)) ? $films_per_month->settings : null;

            $available_providers            = $this->webapp_service->api_dispatcher($this->api_end_point . 'provider/provider', [ 'account_id' => $this->user->account_id ], ['auth_token' => $this->auth_token], true);
            $data['available_providers']    = (isset($available_providers->status) && ($available_providers->status == true) && !empty($available_providers->content_provider)) ? $available_providers->content_provider : null;

            $this->_render_webpage('distribution/groups/distribution_group_create', $data);
        }
    }


    public function create_distribution_group($page = "details")
    {
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'details');

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg']  = $this->config->item('ajax_access_denied');
        } else {
            $postdata                   = array_merge(['account_id' => $this->user->account_id], $this->input->post());
            $new_distribution_group     = $this->webapp_service->api_dispatcher($this->api_end_point . 'distribution/add_distribution_group', $postdata, ['auth_token' => $this->auth_token]);
            $result                     = (isset($new_distribution_group->distribution_group)) ? $new_distribution_group->distribution_group : null;
            $message                    = (isset($new_distribution_group->message)) ? $new_distribution_group->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status']              = 1;
                $return_data['distribution_group']  = $result;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    /** Update Distribution Group Profile Details **/
    public function update_distribution_group($distribution_group_id = false, $page = 'details')
    {
        $return_data = [
            'status' => 0
        ];

        $section    = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $distribution_group_id = ($this->input->post('distribution_group_id')) ? $this->input->post('distribution_group_id') : (!empty($distribution_group_id) ? $distribution_group_id : null);

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
            $update_distribution_group = $this->webapp_service->api_dispatcher($this->api_end_point . 'distribution/update_distribution_group', $postdata, ['auth_token' => $this->auth_token]);
            $result       = (isset($update_distribution_group->distribution_group)) ? $update_distribution_group->distribution_group : null;
            $message      = (isset($update_distribution_group->message)) ? $update_distribution_group->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status']              = 1;
                $return_data['distribution_group']  = $result;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    //View Distribution Group profile
    public function profile($distribution_group_id = false, $page = 'details')
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        ## Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } elseif ($distribution_group_id) {
            $run_admin_check            = false;
            $data                       = [];

            $distribution_group_details         = $this->webapp_service->api_dispatcher($this->api_end_point . 'distribution/distribution_groups', ['account_id' => $this->user->account_id,'distribution_group_id' => $distribution_group_id], ['auth_token' => $this->auth_token], true);
            $data['distribution_group_details'] = (isset($distribution_group_details->distribution_groups)) ? $distribution_group_details->distribution_groups : null;

            if (!empty($data['distribution_group_details'])) {
                ## Get allowed access for the logged in user
                $data['permissions'] = $item_access;
                $data['active_tab'] = $page;
                $data['module_id']  = $this->module_id;

                $module_items       = $this->webapp_service->api_dispatcher($this->api_end_point . 'access/account_modules_items', ['account_id' => $this->user->account_id, 'module_id' => $this->module_id ], ['auth_token' => $this->auth_token], true);
                $data['module_tabs'] = (isset($module_items->module_items)) ? $module_items->module_items : null;

                $provider_details   = !empty($data['distribution_group_details']->distribution_group_providers) ? object_to_array($data['distribution_group_details']->distribution_group_providers) : [];

                $site_params = [
                    'account_id' => $this->user->account_id,
                    'where' => [
                        'territory_id'              => $data['distribution_group_details']->associated_territory_id,
                        'distribution_group_id'     => $data['distribution_group_details']->distribution_group_id,
                        'provider_details'          => !empty($provider_details) ? $provider_details : false
                    ],
                    'limit' => -1
                ];

                $available_sites            = $this->webapp_service->api_dispatcher($this->api_end_point . 'site/distribution_sites', $site_params, ['auth_token' => $this->auth_token], true);
                $data['available_sites']    = (isset($available_sites->distribution_sites)) ? $available_sites->distribution_sites : null;

                $providers                  = !empty($data['distribution_group_details']->distribution_group_providers) ? $data['distribution_group_details']->distribution_group_providers : false;
                $providers                  = !empty($providers) ? array_unique(array_column(object_to_array($providers), 'provider_id')) : false;

                $data['active_sites']       = $data['available_sites'];

                $linked_sites               = (!empty($data['distribution_group_details']->linked_group_sites)) ? object_to_array($data['distribution_group_details']->linked_group_sites) : [];
                $data['linked_sites']       = !empty($linked_sites) ? array_column($linked_sites, 'site_id') : [];

                switch ($page) {
                    case 'bundles':
                        $content_params = [
                            'account_id'    => $this->user->account_id,
                            'where'         => [
                                'territory_id'          => $data['distribution_group_details']->associated_territory_id,
                                'provider_id'           => $providers,
                                'distribution_group_id' => $distribution_group_id
                            ],
                            'limit' => -1
                        ];

                        $available_content          = $this->webapp_service->api_dispatcher($this->api_end_point . 'content/distribution_content', $content_params, ['auth_token' => $this->auth_token], true);
                        $data['available_content']  = (isset($available_content->content)) ? $available_content->content : null;

                        $distribution_bundle_id     = !empty($this->input->get('distribution_bundle_id')) ? $this->input->get('distribution_bundle_id') : (!empty($this->input->post('distribution_bundle_id')) ? $this->input->post('distribution_bundle_id') : false);

                        $data['compile_review']     = !empty($this->input->get('compile_review')) ? $this->input->get('compile_review') : (!empty($this->input->post('compile_review')) ? $this->input->post('compile_review') : false);

                        $bundle_params = [
                            'account_id'            => $this->user->account_id,
                            'distribution_bundle_id' => $distribution_bundle_id,
                            'limit'                 => -1
                        ];

                        $data['distribution_bundle_id'] = $distribution_bundle_id;

                        if (!empty($distribution_bundle_id) && !empty($data['compile_review'])) {
                            $compile_review_data            = $this->webapp_service->api_dispatcher($this->api_end_point . 'distribution/check_bundle_validity', $bundle_params, ['auth_token' => $this->auth_token], true);
                            $data['compile_review_data']    = (isset($compile_review_data->bundle_validity)) ? $compile_review_data->bundle_validity : null;
                        }

                        $show_cds_running       = (!empty($this->input->get('show_cds_running')) && ($this->input->get('show_cds_running') == "yes")) ? true : false ;
                        if ($show_cds_running) {
                            $data['running_bundles']    = $postdata = [];
                            $postdata['account_id']     = $this->user->account_id;
                            $url                        = 'distribution/cds_running';
                            $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                            $data['running_bundles']    = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->running)) ? $API_result->running : null;
                        }

                        $show_cds_queueWaiting  = (!empty($this->input->get('show_cds_queueWaiting')) && ($this->input->get('show_cds_queueWaiting') == "yes")) ? true : false ;
                        if ($show_cds_queueWaiting) {
                            $data['queue_waiting_bundles']  = $postdata = [];
                            $postdata['account_id']         = $this->user->account_id;
                            $url                            = 'distribution/cds_queue_waiting';
                            $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                            $data['queue_waiting_bundles']  = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->queue_waiting)) ? $API_result->queue_waiting : null;
                        }

                        $show_cds_completed     = (!empty($this->input->get('show_cds_completed')) && ($this->input->get('show_cds_completed') == "yes")) ? true : false ;
                        if ($show_cds_completed) {
                            $data['completed_bundles']      = $postdata = [];
                            $postdata['account_id']         = $this->user->account_id;
                            $url                            = 'distribution/cds_queue_completed';
                            $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                            $data['completed_bundles']      = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->queue_completed)) ? $API_result->queue_completed : null;
                        }

                        $data['include_page']       = 'bundles/distribution_bundles_list.php';
                        break;

                    case 'inventory':
                        $inventory_params = [
                            'account_id' => $this->user->account_id,
                            'where' => [
                                'grouped'   => 1,
                                'distribution_group_id' => $distribution_group_id,
                            ],
                            'limit' => -1
                        ];

                        $distribution_content       = $this->webapp_service->api_dispatcher($this->api_end_point . 'distribution/bundle_content', $inventory_params, ['auth_token' => $this->auth_token], true);
                        $distribution_content       = (isset($distribution_content->bundle_content)) ? $distribution_content->bundle_content : null;

                        $data['current_films']      = !empty($distribution_content->current_films) ? $distribution_content->current_films : false;
                        $data['library_films']      = !empty($distribution_content->library_films) ? $distribution_content->library_films : false;
                        $data['recylable_films']    = !empty($distribution_content->recylable_films) ? $distribution_content->recylable_films : false;
                        $data['all_films']          = !empty($distribution_content->all_films) ? $distribution_content->all_films : false;

                        $data['auto_remove']        = !empty($this->input->get('auto_remove')) ? $this->input->get('auto_remove') : false;
                        $data['total_to_remove']    = !empty($this->input->get('total_to_remove')) ? $this->input->get('total_to_remove') : false;
                        $data['films_added']        = (!empty($this->input->get('films_added')) && validate_array_of_integers(json_decode($this->input->get('films_added')))) ? json_decode($this->input->get('films_added')) : false;
                        $data['base_line']          = !empty($this->input->get('base_line')) ? $this->input->get('base_line') : 0;

                        if (!empty($data['auto_remove'])) {
                            $auto_remove_params = [
                                'account_id'            => $this->user->account_id,
                                'distribution_group_id' => $distribution_group_id,
                                'where'                 => [
                                    'content_in_use'        => 1,
                                    'total_to_remove'       => (!empty($data['total_to_remove'])) ? $data['total_to_remove'] : '' ,
                                    'films_added'           => (!empty($data['films_added'])) ? $data['films_added'] : false,
                                ],
                                'limit' => -1
                            ];

                            $auto_remove_content        = $this->webapp_service->api_dispatcher($this->api_end_point . 'distribution/auto_remove_content', $auto_remove_params, ['auth_token' => $this->auth_token], true);
                            $auto_remove_content        = (isset($auto_remove_content->bundle_content)) ? $auto_remove_content->bundle_content : null;
                        }

                        $data['content_to_remove']      = !empty($auto_remove_content->to_remove) ? $auto_remove_content->to_remove : [];

                        $data['include_page']           = 'inventory/distribution_group_inventory.php';
                        break;

                    case 'details':
                    default:
                        $params['account_id']                     = $this->user->account_id;
                        $params['where']['module_id']             = 2; ## Site module
                        $params['where']['setting_name_group']    = '2_no_of_titles'; ## 'no of titles'

                        $no_of_titles_packages          = $this->webapp_service->api_dispatcher($this->api_end_point . 'settings/settings', $params, ['auth_token' => $this->auth_token], true);
                        $data['no_of_titles_packages']  = (isset($no_of_titles_packages->status) && ($no_of_titles_packages->status == true) && !empty($no_of_titles_packages->settings)) ? $no_of_titles_packages->settings : null;

                        $territory_params = [
                            'account_id' => $this->user->account_id,
                            'integrator_id' => $data['distribution_group_details']->system_integrator_id,
                            'limit' => -1
                        ];

                        $integrator_territories = $this->webapp_service->api_dispatcher($this->api_end_point . 'integrator/integrator_territories', $territory_params, ['auth_token' => $this->auth_token], true);
                        $data['territories']    = (isset($integrator_territories->status) && ($integrator_territories->status == true) && !empty($integrator_territories->integrator_territories)) ? $integrator_territories->integrator_territories : null;

                        $system_integrators         = $this->webapp_service->api_dispatcher($this->api_end_point . 'integrator/lookup', ['account_id' => $this->user->account_id, 'limit' => -1 ], ['auth_token' => $this->auth_token], true);
                        $data['system_integrators'] = (isset($system_integrators->status) && ($system_integrators->status == true) && !empty($system_integrators->integrator)) ? $system_integrators->integrator : null;

                        $provider_params = [
                            'account_id' => $this->user->account_id,
                            'where' => [
                                'integrator_id' => $data['distribution_group_details']->system_integrator_id,
                                'territory_id'  => $data['distribution_group_details']->associated_territory_id,
                            ],
                            'limit' => -1
                        ];

                        $available_providers        = $this->webapp_service->api_dispatcher($this->api_end_point . 'provider/provider', $provider_params, ['auth_token' => $this->auth_token], true);
                        $data['available_providers'] = (isset($available_providers->status) && ($available_providers->status == true) && !empty($available_providers->content_provider)) ? $available_providers->content_provider : null;


                        ## delivery points - delivery servers taken from Cacti with the current, updated 'running' value field
                        $postdata                   = [];
                        $postdata['account_id']     = $this->user->account_id;
                        $postdata['where']['check_running'] = 'yes';
                        $url                        = 'distribution/distribution_server';
                        $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['distribution_servers']   = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->d_servers)) ? $API_result->d_servers : null;

                        $fpm_params = [
                            'account_id' => $this->user->account_id,
                            'where' => [
                                'module_id'             => 2,
                                'setting_name_group'    => '2_films_per_month'
                            ]
                        ];
                        $films_per_month            = $this->webapp_service->api_dispatcher($this->api_end_point . 'settings/settings', $fpm_params, ['auth_token' => $this->auth_token], true);
                        $data['films_per_month']    = (isset($films_per_month->status) && ($films_per_month->status == true) && !empty($films_per_month->settings)) ? $films_per_month->settings : null;

                        $data['include_page']   = 'groups/distribution_group_details.php';
                        break;
                }
            }

            ## Run the admin check if tab needs only admin
            if (!empty($run_admin_check)) {
                if ((!admin_check($this->user->is_admin, false, (!empty($data['permissions']) ? $data['permissions']->is_admin : false)))) {
                    $data['admin_no_access'] = true;
                }
            }

            $this->_render_webpage('distribution/profile', $data);
        } else {
            redirect('webapp/distribution_group', 'refresh');
        }
    }

    /*
    *   Distribution Group lookup / search
    */
    public function distribution_group_lookup($page = 'details')
    {
        $return_data = '';

        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            $return_data .= $this->config->item('ajax_access_denied');
        } else {
            # Setup search parameters
            $distribution_group_id  = ($this->input->post('distribution_group_id')) ? $this->input->post('distribution_group_id') : false;
            $search_term            = ($this->input->post('search_term')) ? $this->input->post('search_term') : false;
            $where                  = ($this->input->post('where')) ? $this->input->post('where') : false;
            $limit                  = (!empty($where['limit'])) ? $where['limit'] : DEFAULT_LIMIT;
            $start_index            = ($this->input->post('start_index')) ? $this->input->post('start_index') : DEFAULT_OFFSET;
            $offset                 = (!empty($start_index)) ? (($start_index - 1) * $limit) : 0;
            $order_by               = ($this->input->post('order_by')) ? $this->input->post('order_by') : false;

            #prepare postdata
            $postdata = [
                'account_id'            => $this->user->account_id,
                'distribution_group_id' => $distribution_group_id,
                'search_term'           => $search_term,
                'where'                 => $where,
                'order_by'              => $order_by,
                'limit'                 => $limit,
                'offset'                => $offset
            ];

            $search_result  = $this->webapp_service->api_dispatcher($this->api_end_point . 'distribution/distribution_groups', $postdata, ['auth_token' => $this->auth_token], true);

            $distribution_groups            = (isset($search_result->distribution_groups)) ? $search_result->distribution_groups : null;

            if (!empty($distribution_groups)) {
                ## Create pagination
                $counters       = (isset($search_result->counters)) ? $search_result->counters : null;
                $page_number    = ($start_index > 0) ? $start_index : 1;
                $page_display   = '<span style="margin:15px 0px;" class="pull-left">Page <strong>' . $page_number . '</strong> of <strong>' . (!empty($counters->pages) ? $counters->pages : "") . '</strong></span>';

                if (!empty($counters->total) && ($counters->total > 0)) {
                    $config['total_rows']   = $counters->total;
                    $config['per_page']     = $limit;
                    $config['current_page'] = $page_number;
                    $pagination_setup       = _pagination_config();
                    $config                 = array_merge($config, $pagination_setup);
                    $this->pagination->initialize($config);
                    $pagination             = $this->pagination->create_links();
                }

                $return_data = $this->load_distribution_groups_view($distribution_groups);
                if (!empty($pagination)) {
                    $return_data .= '<tr><td colspan="5" style="padding: 0;">';
                    $return_data .= $page_display . $pagination;
                    $return_data .= '</td></tr>';
                }
            } else {
                $return_data .= '<tr><td colspan="5" style="padding: 0;"><br/>';
                $return_data .= (isset($search_result->message)) ? $search_result->message : 'No records found';
                $return_data .= '</td></tr>';
            }
        }

        print_r($return_data);
        die();
    }

    /*
    *   Prepare distribution_group view
    */
    private function load_distribution_groups_view($distribution_group_data = false)
    {
        $return_data = '';

        if (!empty($distribution_group_data)) {
            foreach ($distribution_group_data as $k => $row) {
                $return_data .= '<tr>';
                $return_data .= '<td>' . ((!empty($row->distribution_group_id)) ? $row->distribution_group_id : '') . '</td>';
                $return_data .= '<td><a href="' . base_url('/webapp/distribution/profile/' . $row->distribution_group_id) . '" >' . (!empty($row->distribution_group) ? $row->distribution_group : '') . '</a></td>';
                $return_data .= '<td>' . ((!empty($row->territory)) ? $row->territory : '') . '</td>';
                #$return_data .= '<td>'.( ( !empty( $row->package_size ) ) ? $row->package_size : '' ).'</td>';
                $return_data .= '<td><span class="pull-right">' . (!empty($row->is_active) ? 'Yes' : 'No') . '</span></td>';
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

    /*
    * Delete Distribution group
    */
    public function delete_distribution_group($distribution_group_id = false, $page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data['status'] = 0;

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data) && !empty($post_data['distribution_group_id'])) {
                $delete_group   = $this->webapp_service->api_dispatcher($this->api_end_point . 'distribution/delete_distribution_group', ['account_id' => $this->user->account_id, 'distribution_group_id' => $distribution_group_id ], ['auth_token' => $this->auth_token]);
                $result         = !empty($delete_group->status) ? true : false;
                $message        = (isset($delete_group->message)) ? $delete_group->message : 'Oops! There was an error processing your request.';

                ## d_distribution_group = deleted_distribution_group
                if (!empty($result)) {
                    $return_data['status']      = true;
                    $return_data['status_msg']  = $message;
                } else {
                    $return_data['status_msg']  = $message;
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    /*
    * Fetch Site records by Package Size
    */
    public function fetch_sites($associated_territory_id = false)
    {
        $provider_details   = ($this->input->post('provider_details')) ? $this->input->post('provider_details') : null;

        $return_data = [
            'status'        => 0,
            'site_record'   => null,
            'status_msg'    => 'Invalid parameters'
        ];

        if (!empty($provider_details)) {
            $params = [
                'account_id' => $this->user->account_id,
                'where' => [
                    'territory_id'              => (($this->input->post('associated_territory_id')) ? $this->input->post('associated_territory_id') : (!empty($associated_territory_id) ? $associated_territory_id : null)),
                    'system_integrator_id'      => ($this->input->post('system_integrator_id')) ? $this->input->post('system_integrator_id') : false,
                    'provider_details'          => $provider_details,
                ],
                'limit' => -1
            ];

            $site_result    = $this->webapp_service->api_dispatcher($this->api_end_point . 'site/distribution_sites', $params, ['auth_token' => $this->auth_token], true);
            $result     = (isset($site_result->distribution_sites)) ? $site_result->distribution_sites : null;
            $message    = (isset($site_result->message)) ? $site_result->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $site_records = $this->load_filtered_sites($result);
                $return_data['status']          = 1;
                $return_data['site_records']    = $site_records;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }

    /** Populate Filtered Sites **/
    private function load_filtered_sites($site_records = false)
    {
        $return_data = '';
        if (!empty($site_records)) {
            $return_data .= '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12"><label><input type="checkbox" id="tick_all_sites" value=""> All Sites</label></div>';
            foreach ($site_records as $k => $site) {
                $return_data .= '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">';
                $return_data .= '<label for="site-' . (strtolower(trim($site->site_id))) . '">';
                $return_data .= '<input type="checkbox" name="linked_sites[]" id="site-' . (strtolower(trim($site->site_id))) . '" value="' . $site->site_id . '" class="site-records" /> <span class="site_name">' . ucwords(strtolower($site->site_name)) . '</span></label>';
                $return_data .= '</div>';
            }
        } else {
            $return_data .= '<div class="col-md-12 col-sm-6 col-xs-12">';
            $return_data .= 'There is currently no sites matching your criteria!';
            $return_data .= '</div>';
        }
        return $return_data;
    }


    /*
    * Fetch Content Providers
    */
    public function fetch_content_providers($integrator_id = false, $territory_id = false)
    {
        $integrator_id  = ($this->input->post('integrator_id')) ? $this->input->post('integrator_id') : (!empty($integrator_id) ? $integrator_id : null);
        $territory_id   = ($this->input->post('territory_id')) ? $this->input->post('territory_id') : (!empty($territory_id) ? $territory_id : null);

        $return_data = [
            'status'            => 0,
            'content_providers' => null,
            'status_msg'        => 'Invalid paramaters'
        ];

        if (!empty($integrator_id)) {
            $params = [
                'account_id' => $this->user->account_id,
                'where' => [
                    'integrator_id' => $integrator_id,
                    'territory_id'  => $territory_id,
                ],
                'limit' => -1
            ];

            $available_providers    = $this->webapp_service->api_dispatcher($this->api_end_point . 'provider/provider', $params, ['auth_token' => $this->auth_token], true);
            $result                 = (isset($available_providers->status) && ($available_providers->status == true) && !empty($available_providers->content_provider)) ? $available_providers->content_provider : null;

            $message    = (isset($available_providers->message)) ? $available_providers->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $content_providers                  = $this->load_content_providers($result);
                $return_data['status']              = 1;
                $return_data['content_providers']   = $content_providers;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }

    /** Populate Load Content Providers **/
    private function load_content_providers($content_providers = false)
    {
        $return_data = '';
        if (!empty($content_providers)) {
            $return_data .= '<select id="provider_selection" class="input-field" style="width:100%" >';
            $return_data .= '<option value="">Please select Provider</option>';
            foreach ($content_providers as $k => $provider) {
                $return_data .= '<option value="' . $provider->provider_id . '" data-provider_name="' . $provider->provider_name . '">' . $provider->provider_name . '</option>';
            }
            $return_data .= '</select>';
        } else {
            $return_data .= '<div class="col-md-12 col-sm-6 col-xs-12">';
            $return_data .= 'There is currently no Providers matching your criteria!';
            $return_data .= '</div>';
        }
        return $return_data;
    }

    /** Create a New Distribution Bundle **/
    public function create_distribution_bundle($page = "details")
    {
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'details');

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg']  = $this->config->item('ajax_access_denied');
        } else {
            $postdata                   = array_merge(['account_id' => $this->user->account_id], $this->input->post());
            $new_distribution_bundle        = $this->webapp_service->api_dispatcher($this->api_end_point . 'distribution/add_distribution_bundle', $postdata, ['auth_token' => $this->auth_token]);
            $result                     = (isset($new_distribution_bundle->distribution_bundle)) ? $new_distribution_bundle->distribution_bundle : null;
            $message                    = (isset($new_distribution_bundle->message)) ? $new_distribution_bundle->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status']              = 1;
                $return_data['distribution_bundle'] = $result;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    /** Update Distribution Group Bundle Profile Details **/
    public function update_distribution_bundle($distribution_bundle_id = false, $page = 'details')
    {
        $return_data = [
            'status' => 0
        ];

        $section    = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $distribution_bundle_id = ($this->input->post('distribution_bundle_id')) ? $this->input->post('distribution_bundle_id') : (!empty($distribution_bundle_id) ? $distribution_bundle_id : null);

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
            $update_distribution_bundle = $this->webapp_service->api_dispatcher($this->api_end_point . 'distribution/update_distribution_bundle', $postdata, ['auth_token' => $this->auth_token]);
            $result       = (isset($update_distribution_bundle->distribution_bundle)) ? $update_distribution_bundle->distribution_bundle : null;
            $message      = (isset($update_distribution_bundle->message)) ? $update_distribution_bundle->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status']              = 1;
                $return_data['distribution_bundle'] = $result;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    /*
    *   Distribution Group Bundles lookup / search
    */
    public function distribution_bundles_lookup($distribution_group_id = false, $page = 'details')
    {
        $return_data = '';

        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            $return_data .= $this->config->item('ajax_access_denied');
        } else {
            # Setup search parameters
            $distribution_group_id  = ($this->input->post('distribution_group_id')) ? $this->input->post('distribution_group_id') : $distribution_group_id;
            $distribution_bundle_id = ($this->input->post('distribution_bundle_id')) ? $this->input->post('distribution_bundle_id') : false;
            $search_term            = ($this->input->post('search_term')) ? $this->input->post('search_term') : false;
            $where                  = ($this->input->post('where')) ? $this->input->post('where') : false;
            $limit                  = (!empty($where['limit'])) ? $where['limit'] : DEFAULT_LIMIT;
            $start_index            = ($this->input->post('start_index')) ? $this->input->post('start_index') : DEFAULT_OFFSET;
            $offset                 = (!empty($start_index)) ? (($start_index - 1) * $limit) : 0;
            $order_by               = ($this->input->post('order_by')) ? $this->input->post('order_by') : false;

            #prepare postdata
            $postdata = [
                'account_id'            => $this->user->account_id,
                'distribution_group_id' => $distribution_group_id,
                'distribution_bundle_id' => $distribution_bundle_id,
                'search_term'           => $search_term,
                'where'                 => $where,
                'order_by'              => $order_by,
                'limit'                 => $limit,
                'offset'                => $offset
            ];

            ## Please, remember this call is related to the calls on the Coggins side (queueWaiting, queueRunning, queueFinished)
            ## - if Coggins is down this will return something after a long period of time.

            $search_result          = $this->webapp_service->api_dispatcher($this->api_end_point . 'distribution/distribution_bundles', $postdata, ['auth_token' => $this->auth_token], true);
            $distribution_bundles   = (isset($search_result->distribution_bundles)) ? $search_result->distribution_bundles : null;

            if (!empty($distribution_bundles)) {
                ## Create pagination
                $counters       = (isset($search_result->counters)) ? $search_result->counters : null;
                $page_number    = ($start_index > 0) ? $start_index : 1;
                $page_display   = '<span style="margin:15px 0px;" class="pull-left">Page <strong>' . $page_number . '</strong> of <strong>' . (!empty($counters->pages) ? $counters->pages : "") . '</strong></span>';

                if (!empty($counters->total) && ($counters->total > 0)) {
                    $config['total_rows']   = $counters->total;
                    $config['per_page']     = $limit;
                    $config['current_page'] = $page_number;
                    $pagination_setup       = _pagination_config();
                    $config                 = array_merge($config, $pagination_setup);
                    $this->pagination->initialize($config);
                    $pagination             = $this->pagination->create_links();
                }

                $return_data = $this->load_distribution_bundles_view($distribution_bundles);
                if (!empty($pagination)) {
                    $return_data .= '<tr><td colspan="7" style="padding: 0;">';
                    $return_data .= $page_display . $pagination;
                    $return_data .= '</td></tr>';
                }
            } else {
                $return_data .= '<tr><td colspan="5" style="padding: 0;"><br/>';
                $return_data .= (isset($search_result->message)) ? $search_result->message : 'No records found';
                $return_data .= '</td></tr>';
            }
        }

        print_r($return_data);
        die();
    }

    /*
    *   Prepare distribution_bundle view
    */
    private function load_distribution_bundles_view($distribution_bundle_data = false)
    {
        $return_data = '';

        if (!empty($distribution_bundle_data)) {
            ## Collapsible Accordion
            /* foreach( $distribution_bundle_data as $k => $row ){
                $return_data .= '<div class="bundle-record row" >';
                    $return_data .= '<div class="col-md-3 col-xs-12" >'.( ( !empty( $row->distribution_bundle ) ) ? $row->distribution_bundle : '' ).'</div>';
                    $return_data .= '<div class="col-md-3 col-xs-12" >'.( ( valid_date( $row->license_start_date ) ) ? date( 'd/m/Y', strtotime( $row->license_start_date ) ) : '' ).'</div>';
                    $return_data .= '<div class="col-md-3 col-xs-12" >'.( ( !empty( $row->distribution_bundle ) ) ? $row->distribution_bundle : '' ).'</div>';
                    $return_data .= '<div class="col-md-3 col-xs-12" ><span class="pull-right">'.( !empty( $row->is_active ) ? 'Active' : 'Inactive' ).'</span></div>';
                $return_data .= '</div>';

                $return_data .= '<div class="bundle-details row" style="display:none" >';
                    $return_data .= '<div class="col-md-12">Show Bundle Details...</div>';
                $return_data .= '</div>';
            } */

            ## Tabular
            foreach ($distribution_bundle_data as $k => $row) {
                $return_data .= '<tr class="pointer" data-distribution_bundle_id="' . $row->distribution_bundle_id . '" data-coggins_uid="' . $row->coggins_uid . '" data-coggins_progress="' . $row->coggins_progress . '">';
                $return_data .= '<td class="click-view-bundle" >' . ((!empty($row->distribution_bundle)) ? $row->distribution_bundle : '') . '</td>';
                $return_data .= '<td class="click-view-bundle" >' . ((valid_date($row->license_start_date)) ? date('d/m/Y', strtotime($row->license_start_date)) : '') . '</td>';

                if ((in_array(strtolower(trim($row->send_status)), ['sent','complete','completed']))) {
                    $return_data .= '<td class="click-view-bundle"><i class="far fa-check-circle text-green" title="Distribution Bundle has been sent"></i>&nbsp;' . ((!empty($row->send_status)) ? ucfirst($row->send_status) : '') . '</td>';
                } elseif ((in_array(strtolower(trim($row->send_status)), ['error']))) {
                    $return_data .= '<td class="click-view-bundle" title="' . ((!empty($row->error_message)) ? $row->error_message : 'There is an error sending the distribution') . '"><i class="far fa-clock text-orange"></i>&nbsp;' . ((!empty($row->send_status)) ? ucfirst($row->send_status) : '') . '</td>';
                } else {
                    $return_data .= '<td class="click-view-bundle" ><i class="far fa-clock text-orange" title="Distribution Bundle is planned to be sent" ></i>&nbsp;' . ((!empty($row->send_status)) ? ucfirst($row->send_status) : '') . '</td>';
                }

                if (in_array(strtolower($row->send_status), ["sending"])) {
                    $return_data .= '<td  class="click-view-bundle" data-distribution_bundle_id="' . $row->distribution_bundle_id . '">' . ((!empty($row->coggins_progress)) ?
                    number_format($row->coggins_progress, 2, '.', '') : '0') . '</td>';
                    $return_data .= '<td  class="click-view-bundle" data-distribution_bundle_id="' . $row->distribution_bundle_id . '">' . ((!empty($row->coggins_errors)) ?
                    number_format($row->coggins_errors, 0, '.', '') : '0') . '</td>';
                } elseif (in_array(strtolower($row->send_status), ["sent"])) {
                    $return_data .= '<td  class="click-view-bundle" data-distribution_bundle_id="' . $row->distribution_bundle_id . '">' . ((!empty($row->coggins_progress)) ?
                    number_format($row->coggins_progress, 2, '.', '') : '100') . '</td>';
                    $return_data .= '<td  class="click-view-bundle" data-distribution_bundle_id="' . $row->distribution_bundle_id . '">' . ((!empty($row->coggins_errors)) ?
                    number_format($row->coggins_errors, 0, '.', '') : '0') . '</td>';
                } else {
                    $return_data .= '<td  class="click-view-bundle" data-distribution_bundle_id="' . $row->distribution_bundle_id . '">N/A</td>';
                    $return_data .= '<td  class="click-view-bundle" data-distribution_bundle_id="' . $row->distribution_bundle_id . '">N/A</td>';
                }

                $return_data .= '<td  class="click-view-bundle" data-distribution_bundle_id="' . $row->distribution_bundle_id . '" >' . ((valid_date($row->send_status_timestamp)) ? date('d/m/Y H:i:s A', strtotime($row->send_status_timestamp)) : '') . '</td>';
                if (!empty($row->send_status) && in_array(strtolower($row->send_status), ["scheduled", "sending", "processing"])) {
                    $return_data .= '<td><span class="pull-right cancel-distro-bundle pointer" data-distribution_bundle_id="' . $row->distribution_bundle_id . '" title="Cancel this Distribution Bundle"><img src="' . (base_url("assets/images/bundle-cancel.png")) . '" alt="" /></span><span class="pull-right refresh-distro-bundle pointer" data-distribution_bundle_id="' . $row->distribution_bundle_id . '" title="Refresh this Distribution Bundle" ><img src="' . (base_url("assets/images/bundle-refresh.png")) . '" alt="" /></span></td>';
                } elseif (!empty($row->send_status) && in_array(strtolower($row->send_status), ["error"])) {
                    $return_data .= '<td><span class="pull-right cancel-distro-bundle pointer" data-distribution_bundle_id="' . $row->distribution_bundle_id . '" title="Cancel this Distribution Bundle"><img src="' . (base_url("assets/images/bundle-cancel.png")) . '" alt="" /></span></td>';
                } elseif (!empty($row->send_status) && in_array(strtolower($row->send_status), ["planned", "cancelled", "sent", "complete", "completed"])) {
                    $return_data .= '<td><span class="pull-right delete-distro-bundle pointer" data-distribution_bundle_id="' . $row->distribution_bundle_id . '" title="Delete this Distribution Bundles" ><i class="fas fa-trash-alt"></i></span></td>';
                } else {
                    $return_data .= '<td>&nbsp;</td>';
                }
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
    * Delete Distribution group
    */
    public function delete_distribution_bundle($distribution_bundle_id = false, $page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data['status'] = 0;

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data) && !empty($post_data['distribution_bundle_id'])) {
                $delete_group   = $this->webapp_service->api_dispatcher($this->api_end_point . 'distribution/delete_distribution_bundle', ['account_id' => $this->user->account_id, 'distribution_bundle_id' => $distribution_bundle_id ], ['auth_token' => $this->auth_token]);
                $result         = !empty($delete_group->status) ? true : false;
                $message        = (isset($delete_group->message)) ? $delete_group->message : 'Oops! There was an error processing your request.';

                ## d_distribution_bundle = deleted_distribution_bundle
                if (!empty($result)) {
                    $return_data['status']      = true;
                    $return_data['status_msg']  = $message;
                } else {
                    $return_data['status_msg']  = $message;
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    /** Add a New Distribution Group Provider **/
    public function add_distribution_group_provider($page = "details")
    {
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'details');

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg']  = $this->config->item('ajax_access_denied');
        } else {
            $postdata                       = array_merge(['account_id' => $this->user->account_id], $this->input->post());
            $new_distribution_group_provider = $this->webapp_service->api_dispatcher($this->api_end_point . 'distribution/add_distribution_group_provider', $postdata, ['auth_token' => $this->auth_token]);
            $result                         = (isset($new_distribution_group_provider->distribution_group_provider)) ? $new_distribution_group_provider->distribution_group_provider : null;
            $message                        = (isset($new_distribution_group_provider->message)) ? $new_distribution_group_provider->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status']                      = 1;
                $return_data['distribution_group_provider'] = $result;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    /*
    * Delete Distribution group Provider
    */
    public function delete_distribution_group_provider($combination_id = false, $page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data['status'] = 0;

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data) && !empty($post_data['combination_id'])) {
                $delete_provider = $this->webapp_service->api_dispatcher($this->api_end_point . 'distribution/delete_distribution_group_provider', ['account_id' => $this->user->account_id, 'combination_id' => $combination_id ], ['auth_token' => $this->auth_token]);
                $result         = !empty($delete_provider->status) ? true : false;
                $message        = (isset($delete_provider->message)) ? $delete_provider->message : 'Oops! There was an error processing your request.';

                if (!empty($result)) {
                    $return_data['status']      = true;
                    $return_data['status_msg']  = $message;
                } else {
                    $return_data['status_msg']  = $message;
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    /*
    * Load a distribution_bundle record
    */
    public function view_distribution_bundle_record($distribution_bundle_id = false)
    {
        $distribution_bundle_id     = ($this->input->post('distribution_bundle_id')) ? $this->input->post('distribution_bundle_id') : (!empty($distribution_bundle_id) ? $distribution_bundle_id : null);

        $return_data = [
            'status'                => 0,
            'distribution_bundle'   => null,
            'status_msg'            => 'Invalid parameters'
        ];

        if (!empty($distribution_bundle_id)) {
            $distribution_bundle_result = $this->webapp_service->api_dispatcher($this->api_end_point . 'distribution/distribution_bundles', ['account_id' => $this->user->account_id,'distribution_bundle_id' => $distribution_bundle_id], ['auth_token' => $this->auth_token], true);
            $result     = (isset($distribution_bundle_result->distribution_bundles)) ? $distribution_bundle_result->distribution_bundles : null;
            $message    = (isset($distribution_bundle_result->message)) ? $distribution_bundle_result->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $distribution_bundle = $this->load_distribution_bundle($result);
                $return_data['status']              = 1;
                $return_data['distribution_bundle'] = $distribution_bundle;
            }
            $return_data['status_msg'] = $message;
        }
        print_r(json_encode($return_data));
        die();
    }

    /** Populate Bundle Modal **/
    private function load_distribution_bundle($distribution_bundle = false)
    {
        $return_data = '';
        if (!empty($distribution_bundle)) {
            $return_data .= '<input type="hidden" id="distribution_bundle_id" name="distribution_bundle_id" value="' . $distribution_bundle->distribution_bundle_id . '" >';
            $return_data .= '<div class="row" >';
            $return_data .= '<div class="col-md-6 col-xs-12" >';
            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Distribution Bundle</label>';
            $return_data .= '<input name="distribution_bundle" readonly class="form-control" type="text" placeholder="Distribution Bundle" value="' . (!empty($distribution_bundle->distribution_bundle) ? $distribution_bundle->distribution_bundle : "") . '" />';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">License Start Date</label>';
            $return_data .= '<input readonly class="form-control" type="text" placeholder="License Start Date" value="' . (valid_date($distribution_bundle->license_start_date) ? date('d/m/Y', strtotime($distribution_bundle->license_start_date)) : "") . '" />';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Bundle Status</label>';
            $return_data .= '<input readonly class="form-control" type="text" placeholder="Bundle Status" value="' . ((!empty($distribution_bundle->send_status)) ? ucwords($distribution_bundle->send_status) : '') . '"';
            if (in_array(strtolower($distribution_bundle->send_status), ["error"]) && !empty($distribution_bundle->error_message)) {
                $return_data .= ' title="' . $distribution_bundle->error_message . '"';
            }
            $return_data .= '  />';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<div>&nbsp;</div>';
            $return_data .= '</div>';

            $return_data .= '</div>';

            $return_data .= '<div class="col-md-6 col-xs-12" id="bundle-status-form" >';
            $return_data .= '<div class="input-group form-group" style="' . ((isset($distribution_bundle->base_line) && ((int) $distribution_bundle->base_line == 1)) ? 'display: none;' : '') . '">';
            $return_data .= '<input class="checked-content" id="schedule-bundle" type="checkbox" ' . ((!empty($distribution_bundle->schedule_date_time)) ? 'checked="checked"' : '') . ' />';
            $return_data .= '<label class="distro-label" for="schedule-bundle" style="margin-left: 10px;">Schedule Bundle</label>';
            $return_data .= '</div>';

            $return_data .= '<div class="schedule-bundle-container ' . ((!empty($distribution_bundle->schedule_date_time)) ? ' el-shown' : 'el-hidden') . '" style="width: 100%; float: left;">'; // schedule bundle container - start
            $return_data .= '<div class="input-group form-group">';
            // .( ( strtolower( $distribution_bundle->send_status ) == 'scheduled' ) ? ' el-shown' : ' el-hidden' ).'">';
            $return_data .= '<label class="input-group-addon">Schedule Date & Time</label>';
            $return_data .= '<input name="schedule_date_time" class="form-control datetimepicker" type="text" placeholder="Schedule Date & Time" value="' . ((isset($distribution_bundle->schedule_date_time) && validate_date($distribution_bundle->schedule_date_time)) ? date('d/m/Y H:i', strtotime($distribution_bundle->schedule_date_time)) : date('d/m/Y H:i')) . '" />';
            $return_data .= '</div>';
            $return_data .= '<div class="form-group" style="margin-top: 10px;margin-bottom: 20px;">';
            $return_data .= '<legend class="legend-header font-italic"><em>Save the Schedule Date & Time and proceed to Compile the bundle</em></legend>';
            $return_data .= '<button id="update-distribution_bundle-btn" class="btn btn-info" type="button" style="margin-top: -6px;">To Compile</button';
            $return_data .= '</div>';
            $return_data .= '</div>'; // schedule bundle container - end

            $return_data .= '</div>';
            $return_data .= '</div>';

            $return_data .= '<div class="clearfix"></div>';

            $return_data .= '<div class="row" >';
            $return_data .= '<div class="col-md-12" >';
            if (!empty($distribution_bundle->bundle_sites)) {
                $return_data .= '<div class="col-md-6" >';
                $return_data .= '<legend>Bundle Sites</legend>';
                $return_data .= '<div class="bundle-sites">';
                foreach ($distribution_bundle->bundle_sites as $col => $site) {
                    $return_data .= '<div class="row">';
                    $return_data .= '<div class="col-md-10" ><a target="_blank" href="' . (base_url('/webapp/site/profile/' . $site->site_id)) . '" >' . $site->site_name . '</a></div>';
                    $return_data .= '<div class="col-md-2" ><span class="hide pull-left remove-site pointer" data-site_id="' . $site->site_id . '" title="Remove this Site from this Bundle" ><i class="fas fa-trash text-red"></i></span></div>';
                    $return_data .= '</div>';
                }
                $return_data .= '</div>';
                $return_data .= '</div>';
            }

            if (!empty($distribution_bundle->bundle_content)) {
                $return_data .= '<div class="col-md-6" >';
                $return_data .= '<legend>Bundle Content</legend>';
                $return_data .= '<table class="table" style="font-size:95%;">';
                foreach ($distribution_bundle->bundle_content as $k => $film) {
                    $return_data .= '<tr class="row" style="border-top: none">';
                    $return_data .= '<td style="border-top: none"><a target="_blank" href="' . (base_url('/webapp/content/profile/' . $film->content_id)) . '" >' . $film->provider_name . '</a></td>';
                    $return_data .= '<td style="border-top: none">' . (date('d/m/Y', strtotime($film->clearance_date))) . '</td>';
                    $return_data .= '<td style="border-top: none"><a target="_blank" href="' . (base_url('/webapp/content/profile/' . $film->content_id)) . '" >' . $film->title . ' | ' . $film->age_rating_name . '</a></td>';
                    $return_data .= '<td style="border-top: none"><span class="pointer">' . (($film->content_in_use == 1) ? '<i title="Film is currently in use" class="fas fa-check-circle text-green"></i>' : '<i title="Film is currently not in use" class="fas fa-times text-red"></i>') . '</span></td>';
                    $return_data .= '</tr>';
                }
                $return_data .= '</table>';
                $return_data .= '</div>';
            }

            $return_data .= '</div>';
            $return_data .= '</div>';
        }
        return $return_data;
    }


    /*
    * Fetch Integrator Territories
    */
    public function fetch_integrator_territories($integrator_id = false)
    {
        $integrator_id  = ($this->input->post('integrator_id')) ? $this->input->post('integrator_id') : (!empty($integrator_id) ? $integrator_id : null);

        $return_data = [
            'status'                => 0,
            'integrator_territories' => null,
            'status_msg'            => 'Invalid paramaters'
        ];

        if (!empty($integrator_id)) {
            $params = [
                'account_id'    => $this->user->account_id,
                'integrator_id' => $integrator_id,
                'limit'         => -1
            ];

            $integrator_territories = $this->webapp_service->api_dispatcher($this->api_end_point . 'integrator/integrator_territories', $params, ['auth_token' => $this->auth_token], true);
            $result                 = (isset($integrator_territories->status) && ($integrator_territories->status == true) && !empty($integrator_territories->integrator_territories)) ? $integrator_territories->integrator_territories : null;
            $message    = (isset($integrator_territories->message)) ? $integrator_territories->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $integrator_territories                 = $this->load_integrator_territories($result);
                $return_data['status']                  = 1;
                $return_data['integrator_territories']  = $integrator_territories;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }

    /** Populate Integrator Territories **/
    private function load_integrator_territories($integrator_territories = false)
    {
        $return_data = '';
        if (!empty($integrator_territories)) {
            $return_data .= '<select id="associated_territory_id" name="associated_territory_id" class="form-control required" style="width:100%; margin-bottom:10px; background-color:none" data-label_text="Linked Territory" >';
            $return_data .= '<option value="">Please select Territory</option>';
            foreach ($integrator_territories as $k => $territory_id) {
                $return_data .= '<option value="' . $territory_id->territory_id . '" data-territory_name="' . $territory_id->territory_name . '">' . $territory_id->territory_name . '</option>';
            }
            $return_data .= '</select>';
        } else {
            $return_data .= '<div class="col-md-12 col-sm-6 col-xs-12">';
            $return_data .= 'There is no data matching your criteria!';
            $return_data .= '</div>';
        }
        return $return_data;
    }


    /*
    * Load a Bundle Content record
    */
    public function view_bundle_content_record($bundle_content_id = false)
    {
        $bundle_content_id  = ($this->input->post('bundle_content_id')) ? $this->input->post('bundle_content_id') : (!empty($bundle_content_id) ? $bundle_content_id : null);

        $return_data = [
            'status'            => 0,
            'bundle_content'    => null,
            'status_msg'        => 'Invalid paramaters'
        ];

        if (!empty($bundle_content_id)) {
            $bundle_content_result  = $this->webapp_service->api_dispatcher($this->api_end_point . 'distribution/bundle_content', ['account_id' => $this->user->account_id, 'bundle_content_id' => $bundle_content_id], ['auth_token' => $this->auth_token], true);
            $result     = (isset($bundle_content_result->bundle_content)) ? $bundle_content_result->bundle_content : null;
            $message    = (isset($bundle_content_result->message)) ? $bundle_content_result->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $bundle_content = $this->load_bundle_content($result);
                $return_data['status']          = 1;
                $return_data['bundle_content']  = $bundle_content;
            }
            $return_data['status_msg'] = $message;
        }
        print_r(json_encode($return_data));
        die();
    }

    /** Populate Bundle Content Modal **/
    private function load_bundle_content($bundle_content = false)
    {
        $return_data = '';
        if (!empty($bundle_content)) {
            $return_data .= '<div class="col-md-12 row" >';
            $return_data .= '<input type="hidden" name="distribution_bundle_id"  value="' . $bundle_content->distribution_bundle_id . '" >';
            $return_data .= '<input type="hidden" name="bundle_content_id" value="' . $bundle_content->bundle_content_id . '" >';
            $return_data .= '<table class="table no-border">';
            $return_data .= '<tr>';
            $return_data .= '<td width="20%" class="text-bold">Title</td>';
            $return_data .= '<td>' . $bundle_content->title . '</td>';
            $return_data .= '</tr>';
            $return_data .= '<tr>';
            $return_data .= '<td width="20%" class="text-bold">License Date</td>';
            $return_data .= '<td>' . (valid_date($bundle_content->license_start_date) ? date('d/m/Y', strtotime($bundle_content->license_start_date)) : '') . '</td>';
            $return_data .= '</tr>';
            $return_data .= '</table>';
            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon" >Action</label>';
            $return_data .= '<select class="form-control" name="content_in_use" >';
            $return_data .= '<option value="1" ' . ($bundle_content->content_in_use == 1 ? "selected=selected" : "") . ' >In use (Current)</option>';
            $return_data .= '<option value="0" ' . ($bundle_content->content_in_use != 1 ? "selected=selected" : "") . '>Remove (Library)</option>';
            $return_data .= '</select>';
            $return_data .= '</div>';
            $return_data .= '<div class="remove-date" style="display:block">';
            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon" >Removal Date</label>';
            $return_data .= '<input name="removal_date" class="form-control datetimepicker" type="text" placeholder="Removal Date" value="' . (valid_date($bundle_content->removal_date) ? date('d/m/Y', strtotime($bundle_content->removal_date)) : '') . '" />';
            $return_data .= '</div>';
            $return_data .= '</div>';
            $return_data .= '</div>';
        }
        return $return_data;
    }


    /** Update Bundle Content Record Details **/
    public function update_bundle_content($bundle_content_id = false, $page = 'details')
    {
        $return_data = [
            'status' => 0
        ];

        $section    = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $bundle_content_id = ($this->input->post('bundle_content_id')) ? $this->input->post('bundle_content_id') : (!empty($bundle_content_id) ? $bundle_content_id : null);

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
            $update_bundle_content = $this->webapp_service->api_dispatcher($this->api_end_point . 'distribution/update_bundle_content', $postdata, ['auth_token' => $this->auth_token]);
            $result       = (isset($update_bundle_content->bundle_content)) ? $update_bundle_content->bundle_content : null;
            $message      = (isset($update_bundle_content->message)) ? $update_bundle_content->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status']              = 1;
                $return_data['bundle_content']  = $result;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    /**
    *   Send Distribution Bundle
    **/
    public function send_distribution_bundle($distribution_bundle_id = false, $page = 'details')
    {
        $return_data = [
            'status' => 0
        ];

        $section    = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $distribution_bundle_id = ($this->input->post('distribution_bundle_id')) ? $this->input->post('distribution_bundle_id') : (!empty($distribution_bundle_id) ? $distribution_bundle_id : null);

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            ;
        } else {
            $postdata                   = array_merge(['account_id' => $this->user->account_id], $this->input->post());
            $send_distribution_bundle   = $this->webapp_service->api_dispatcher($this->api_end_point . 'distribution/send_distribution_bundle', $postdata, ['auth_token' => $this->auth_token]);
            $result                     = (isset($send_distribution_bundle->distribution_bundle)) ? $send_distribution_bundle->distribution_bundle : null;
            $message                    = (isset($send_distribution_bundle->message)) ? $send_distribution_bundle->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status']              = 1;
                $return_data['distribution_bundle'] = $result;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }



    /*
    *   Cancel the sending Distribution bundle on Coggins
    */
    public function cancel_distribution_bundle($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data['status'] = 0;

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_delete) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data) && !empty($post_data['distribution_bundle_id'])) {
                $postdata                           = [];
                $postdata['account_id']             = $this->user->account_id;
                $postdata['distribution_bundle_id'] = $post_data['distribution_bundle_id'];

                $API_call   = $this->webapp_service->api_dispatcher($this->api_end_point . 'distribution/cancel_distribution_bundle', $postdata, ['auth_token' => $this->auth_token]);
                $result         = !empty($API_call->status) ? true : false;
                $message        = (isset($API_call->message)) ? $API_call->message : 'Oops! There was an error processing your request.';

                if (!empty($result)) {
                    $return_data['status']      = true;
                    $return_data['status_msg']  = $message;
                } else {
                    $return_data['status_msg']  = $message;
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }
}
