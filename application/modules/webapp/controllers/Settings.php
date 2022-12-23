<?php

namespace Application\Modules\Web\Controllers;

use Application\Extentions\MX_Controller;

class Settings extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        $this->module_id        = $this->webapp_service->_get_module_id($this->router->fetch_class());
        $this->module_access    = $this->webapp_service->check_access($this->user, $this->module_id);
        $this->load->library('pagination');
    }

    private $allowed_modules = [2,3,4,5];

    public function index()
    {
        if (!$this->user->is_admin && !$this->module_access) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            redirect('webapp/settings/modules', 'refresh');
        }
    }

    /**
    *   Get list of settings or the single setting (profile)
    *   It should be organized by modules
    **/
    public function settings()
    {
        if (!$this->user->is_admin && !$this->module_access) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            redirect('webapp/settings/modules', 'refresh');
        }
    }

    public function modules($module_id = false)
    {
        if ($module_id) {
            redirect('webapp/settings/module/' . $module_id, 'refresh');
        }

        # Check module access
        if (!$this->user->is_admin && !$this->module_access) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $data = false;

            $postdata = $data['module_access'] = [];
            $url                        = 'access/check_module_access';
            $postdata["account_id"]     = $this->user->account_id;
            $postdata["user_id"]        = $this->user->id;
            $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');
            $data['module_access']      = (($API_result->status > 0) && (!empty($API_result->module_access))) ? $API_result->module_access : false ;

            $data['allowed_modules']    = $this->allowed_modules;

            $data['current_user']   = $this->user;

            $this->_render_webpage('settings/modules', $data);
        }
    }


    public function module($module_id = false)
    {
        if (!$module_id) {
            redirect('webapp/settings/modules/', 'refresh');
        } else {
            # Check module access
            $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

            if (!$this->user->is_admin && !$module_access) {
                $this->_render_webpage('errors/access-denied', false);
            } else {
                ## List of all settings by the module id
                $data = false;

                $data['current_user']           = $this->user;
                $data['module_id']              = $module_id;
                $data['allowed_modules']        = $this->allowed_modules;

                $postdata = $data['module_access']  = [];
                $url                                = 'access/check_module_access';
                $postdata["account_id"]             = $this->user->account_id;
                $postdata["user_id"]                = $this->user->id;
                $API_result                         = $this->ssid_common->api_call($url, $postdata, $method = 'POST');
                $data['module_access']              = (($API_result->status > 0) && (!empty($API_result->module_access))) ? $API_result->module_access : false ;

                ## module info
                $postdata = $data['modules']        = [];
                $url                                = 'access/modules';
                $postdata["account_id"]             = $this->user->account_id;
                $postdata["module_id"]              = $module_id;
                $API_result                         = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                if (isset($API_result)) {
                    $data['modules']                = (($API_result->status == true) && !empty($API_result->modules)) ? ($API_result->modules) : ($data['modules']) ;
                }

                ##  this will be moved into the lookup
                $postdata = $data['settings']       = [];
                $url                                = 'settings/settings';
                $postdata["account_id"]             = $this->user->account_id;
                $postdata["where"]["module_id"]     = $module_id;
                $API_result                         = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                if (isset($API_result)) {
                    $data['settings']               = (($API_result->status == true) && !empty($API_result->settings)) ? ($API_result->settings) : ($data['settings']) ;
                }

                ##groups
                $postdata = $data['setting_names']  = [];
                $url                                = 'settings/setting_names';
                $postdata["account_id"]             = $this->user->account_id;
                $postdata["where"]["module_id"]     = $module_id;
                $API_result                         = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                if (isset($API_result)) {
                    $data['setting_names']          = (($API_result->status == true) && !empty($API_result->setting_names)) ? ($API_result->setting_names) : ($data['setting_names']) ;
                }

                ## genre types
                $postdata = $data['genre_types']    = [];
                $url                                = 'content/genre_types';
                $postdata["account_id"]             = $this->user->account_id;
                $API_result                         = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                if (isset($API_result)) {
                    $data['genre_types']            = (($API_result->status == true) && !empty($API_result->genre_types)) ? ($API_result->genre_types) : ($data['genre_types']) ;
                }

                ## Age Classivication
                $postdata = $data['age_classifications']    = [];
                $url                                = 'content/age_classifications';
                $postdata["account_id"]             = $this->user->account_id;
                $API_result                         = $this->ssid_common->api_call($url, $postdata, $method = 'GET');

                if (isset($API_result)) {
                    $data['age_classifications']    = (($API_result->status == true) && !empty($API_result->age_classifications)) ? ($API_result->age_classifications) : ($data['age_classifications']) ;
                }

                $this->_render_webpage('settings/module', $data);
            }
        }
    }


    ## An AJAX call to get the setting item view
    public function setting_items($page = "details")
    {
        $return_data = "";

        # Check module-item access
        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data .= $this->config->item('ajax_access_denied');
        } else {
            $postdata = $data['settings'] = [];

            $postdata['account_id'] = $this->user->account_id;
            if (!empty($this->input->post('setting_name_id'))) {
                $postdata['where']['setting_name_id']   = $this->input->post('setting_name_id');
            }

            if (!empty($this->input->post('moduleID'))) {
                $postdata['where']['module_id']     = $this->input->post('moduleID');
            }
            $url                                    = 'settings/settings';
            $API_result                             = $this->ssid_common->api_call($url, $postdata, $method = 'GET');

            if (isset($API_result)) {
                $data['settings']               = (($API_result->status == true) && !empty($API_result->settings)) ? ($API_result->settings) : ($data['settings']) ;
            }

            if (!empty($data['settings'])) {
                $return_data = $this->load_setting_items_view($data['settings']);
            } else {
                $return_data .= '<tr><td colspan="3">';
                $return_data .= (isset($API_result->message)) ? '<h4>' . $API_result->message . '</h4>' : '<h4>No records found</h4>';
                $return_data .= '<span class="new_item"><button class="btn-success btn-block btn-small create-setting-modal" data-setting_name_id="' . $this->input->post('setting_name_id') . '" href="#create-setting-modal" role="button" data-toggle="modal">Add ' . $this->input->post('setting_name') . ' item</button></span>';
                $return_data .= '</td></tr>';
            }
        }

        print_r($return_data);
        die();
    }



    private function load_setting_items_view($settings = false)
    {
        $return_data = '';

        if (!empty($settings)) {
            $return_data .= '<h4>' . (reset($settings)->setting_name) . '</h4><span class="new_item"><button class="btn-success btn-block btn-small create-setting-modal" data-setting_name_id="' . reset($settings)->setting_name_id . '" href="#create-setting-modal" role="button" data-toggle="modal">Add ' . (reset($settings)->setting_name) . ' item</button></span>';
            $return_data .= '<table class="table table-responsive">';
            $return_data .= '<thead>';
            $return_data .= '<tr class="">';
            $return_data .= '<th>Setting Value</th>';
            $return_data .= '<th>Value Description</th>';
            $return_data .= '<th>Value Order</th>';
            $return_data .= '</thead>';
            $return_data .= '<tbody>';

            if (!empty($settings)) {
                foreach ($settings as $k => $setting) {
                    $return_data .= '<tr class="clickable" data-setting_id="' . $setting->setting_id . '" href="#setting-profile-modal" role="button" class="btn" data-toggle="modal">';
                    $return_data .= '<td>' . $setting->setting_value . '</td>';
                    $return_data .= '<td>' . ((!empty($setting->value_desc)) ? ($setting->value_desc) : '') . '</td>';
                    $return_data .= '<td>' . ((!empty($setting->setting_order)) ? ($setting->setting_order) : '') . '</td>';
                    $return_data .= '</tr>';
                }
            } else {
                $return_data .= '<tr><td colspan="4"><br/>' . $this->config->item("no_records") . '</td></tr>';
            }

            $return_data .= '</tbody>';
            $return_data .= '</table>';
        }
        return $return_data;
    }


    public function item_data($page = "details")
    {
        $return_data = [
            "status" => 0,
        ];

        ## Check module access
        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data) && !empty($post_data['setting_id'])) {
                $postdata = $data['item_data'] = [];
                $postdata['account_id']     = $this->user->account_id;
                $postdata['setting_id']     = (!empty($post_data['setting_id'])) ? $post_data['setting_id'] : false ;

                $url                = 'settings/settings';
                $API_result         = $this->ssid_common->api_call($url, $postdata, $method = 'GET');

                if (isset($API_result)) {
                    $return_data['item_data']               = (($API_result->status == true) && !empty($API_result->settings)) ? ($this->load_setting_update_view($API_result->settings)) : ($data['settings']) ;
                    $return_data['status']      = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "No Setting ID submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    private function load_setting_update_view($setting_item = false)
    {
        $return_data = '';
        if (!empty($setting_item)) {
            $url                        = 'access/check_module_access';
            $postdata["account_id"]     = $this->user->account_id;
            $postdata["user_id"]        = $this->user->id;
            $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');
            $module_access              = (($API_result->status > 0) && (!empty($API_result->module_access))) ? $API_result->module_access : false ;

            $allowed_modules            = $this->allowed_modules;

            $territories                = $postdata = [];
            $postdata['account_id']     = $this->user->account_id;
            $url                        = 'content/territories';
            $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $territories                = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->territories)) ? $API_result->territories : null;

            $return_data .= '<div class="slide-group"><form id="setting_update_in_modal">';

            $return_data .= '<input type="hidden" name="setting_id" value="' . ((!empty($setting_item->setting_id)) ? $setting_item->setting_id : '') . '" />';

            $return_data .= '<div class="input-container">';
            $return_data .= '<p>For the setting Name: <span class="setting-name" style="font-size: 120%; font-weight: 800;">' . ((!empty($setting_item->setting_name)) ? $setting_item->setting_name : '') . '</span></p>';
            $return_data .= '</div>';

            $return_data .= '<div class="input-container">';
            $return_data .= '<label class="input-label">Setting Value:</label>';
            $return_data .= '<input class="input-field" name="setting_value" value="' . ((!empty($setting_item->setting_value)) ? $setting_item->setting_value : '') . '" />';
            $return_data .= '</div>';

            $return_data .= '<div class="input-container">';
            $return_data .= '<label class="input-label">Value Description:</label>';
            $return_data .= '<input class="input-field" name="value_desc" value="' . ((!empty($setting_item->value_desc)) ? $setting_item->value_desc : '') . '" />';
            $return_data .= '</div>';

            $return_data .= '<div class="input-container">';
            $return_data .= '<label class="input-label">Setting Order:</label>';
            $return_data .= '<input class="input-field" name="setting_order" value="' . ((!empty($setting_item->setting_order)) ? $setting_item->setting_order : '') . '" />';
            $return_data .= '</div>';

            if (!empty($setting_item->setting_name_id) && ($setting_item->setting_name_id == 26)) {
                $return_data .= '<div class="input-container">';
                $return_data .= '<label class="input-label">Group Territory:</label>';
                $return_data .= '<select name="setting_territory_id" class="input-field" required><option value="">Please select</option>';

                if (!empty($territories)) {
                    foreach ($territories as $row) {
                        $return_data .= '<option value="' . ($row->territory_id) . '" ' . (!empty($setting_item->setting_territory_id) && ($setting_item->setting_territory_id == $row->territory_id) ? 'selected="selected"' : '') . '>' . ($row->country) . '</option>';
                    }
                }
                $return_data .= '</select>';
                $return_data .= '</div>';
            }

            $return_data .= '<div class="rows update_div" style="position: absolute;bottom: 30px;width: 50%;">';
            $return_data .= '<div>';
            $item_access = $this->webapp_service->check_access($this->user, $this->module_id, "details");
            if ($this->user->is_admin || !empty($item_access->can_edit) || !empty($item_access->is_admin)) {
                $return_data .= '<button id="updateSettingBtn" class="btn-success btn-block pull-left" style="width: 195px;">Update Value</button>';
            } else {
                $return_data .= '<button class="btn-success btn-block no-permissions" disabled style="width: 100%;margin-top: 10px;">No Permissions</button>';
            }
            $return_data .= '</div>';
            $return_data .= '</div>';

            $return_data .= '</form>';

            $return_data .= '<form id="delete_update_in_modal">';
            $return_data .= '<div class="pull-right delete_div">';
            $return_data .= '<div class="row">';
            $item_access = $this->webapp_service->check_access($this->user, $this->module_id, "details");
            if ($this->user->is_admin || !empty($item_access->can_delete) || !empty($item_access->is_admin)) {
                $return_data .= '<button id="deleteSettingBtn" style="width: 195px;" type="submit" class="btn-danger btn-block" data-setting_id="' . ((!empty($setting_item->setting_id)) ? $setting_item->setting_id : '') . '">Delete Value</button>';
            } else {
                $return_data .= '<button class="btn-danger btn btn-sm btn-flow btn-next submit no-permissions pull-left no-permission-btn" disabled>No Permissions</button>';
            }
            $return_data .= '</div>';
            $return_data .= '</div>';
            $return_data .= '</form>';
            $return_data .= '</div>';
        }
        return $return_data;
    }


    public function update_setting($page = "details")
    {
        $return_data['status'] = false;

        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data) && !empty($post_data['setting_id'])) {
                $postdata                   = [];
                $postdata['account_id']     = $this->user->account_id;
                $postdata['setting_data']   = $post_data;
                $postdata['setting_id']     = (!empty($post_data['setting_id'])) ? $post_data['setting_id'] : false ;

                $url            = 'settings/update_setting';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                ## u_setting = updated_setting
                if (!empty($API_result)) {
                    $return_data['u_setting']   = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->u_setting)) ? $API_result->u_setting : null;
                    $return_data['status']      = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }

            print_r(json_encode($return_data));
            die();
        }
    }


    public function delete_setting($page = "details")
    {
        $return_data['status'] = false;

        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data) && !empty($post_data['setting_id'])) {
                $postdata                   = [];
                $postdata['account_id']     = $this->user->account_id;
                $postdata['setting_id']     = (!empty($post_data['setting_id'])) ? $post_data['setting_id'] : false ;

                $url            = 'settings/delete_setting';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result)) {
                    $return_data['d_setting']   = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->d_setting)) ? $API_result->d_setting : null;
                    $return_data['status']      = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }

            print_r(json_encode($return_data));
            die();
        }
    }


    public function create_setting($page = "details")
    {
        $return_data['status'] = false;

        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data) && !empty($post_data['module_id'])) {
                $postdata                       = [];
                $postdata['account_id']         = $this->user->account_id;
                $postdata['module_id']          = (!empty($post_data['module_id'])) ? $post_data['module_id'] : false ;

                ## name already exists - taken ID from the form given to the setting_data
                if (!empty($post_data['setting_name_id'])) {
                    $postdata['setting_data']['setting_name_id']    = $post_data['setting_name_id'];

                ## or
                ## name doesn't exist. Is the fresh name given to the form?
                } elseif (!empty($post_data['setting_name'])) {
                    ## Name is given
                    $postdata['setting_name_data']['setting_name']      = $post_data['setting_name'];

                    ## ...with possibly description
                    if (!empty($post_data['setting_name_desc'])) {
                        $postdata['setting_name_data']['setting_name_desc'] = $post_data['setting_name_desc'];
                    }
                } else {
                    # Either name or ID given - shouldn't process
                    $return_data['status_msg'] = "Missing Setting Name";
                    print_r(json_encode($return_data));
                    die();
                }


                ## If all data is in place - proceed
                if (!empty($post_data['value'])) {
                    $postdata['setting_data']['values'] = $post_data['value'];
                    $url            = 'settings/create';
                    $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                    if (!empty($API_result)) {
                        $return_data['setting'] = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->setting)) ? $API_result->setting : null;
                        $return_data['status']      = (isset($API_result->status)) ? $API_result->status : false ;
                        $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                    } else {
                        $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request uncompleted!';
                    }
                } else {
                    $return_data['status_msg'] = "Values for the Setting not submitted";
                }
            } else {
                $return_data['status_msg'] = "No Module or no data submitted";
            }

            print_r(json_encode($return_data));
            die();
        }
    }



    ## An AJAX call to get the setting item view
    public function get_territories($page = "details")
    {
        $return_data = "";

        # Check module-item access
        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data .= $this->config->item('ajax_access_denied');
        } else {
            $postdata = $data['settings'] = [];

            $data['territories']        = $postdata = [];
            $postdata['account_id']     = $this->user->account_id;
            $url                        = 'content/territories';
            $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');

            if (isset($API_result)) {
                $data['territories']                = (($API_result->status == true) && !empty($API_result->territories)) ? ($API_result->territories) : ($data['territories']) ;
            }

            if (!empty($data['territories'])) {
                $return_data = $this->load_territories_view($data['territories']);
            } else {
                $return_data .= '<tr><td colspan="3">';
                $return_data .= (isset($API_result->message)) ? $API_result->message : '<h4>No records found</h4>';
                $return_data .= '<span class="new_item"><button class="btn-success btn-block btn-small create-setting-modal" data-setting_name_id="' . $this->input->post('setting_name_id') . '" href="#create-setting-modal" role="button" data-toggle="modal">Add ' . $this->input->post('setting_name') . ' item</button></span>';
                $return_data .= '</td></tr>';
            }
        }

        print_r($return_data);
        die();
    }



    private function load_territories_view($territories = false)
    {
        $return_data = '';

        if (!empty($territories)) {
            $return_data .= '<h4>Territories</h4><span class="new_item"><button class="btn-success btn-block btn-small create-territory-modal" data-territory_id="" href="#create-territory-modal" role="button" data-toggle="modal">Add Territory item</button></span>';
            $return_data .= '<table class="table table-responsive">';
            $return_data .= '<thead>';
            $return_data .= '<tr class="">';
            $return_data .= '<th>Country</th>';
            $return_data .= '<th>Code</th>';
            $return_data .= '</thead>';
            $return_data .= '<tbody>';

            if (!empty($territories)) {
                foreach ($territories as $k => $territory) {
                    $return_data .= '<tr class="clickable" data-territory_id="' . $territory->territory_id . '" href="#territory-profile-modal" role="button" class="btn" data-toggle="modal">';
                    $return_data .= '<td>' . $territory->country . '</td>';
                    $return_data .= '<td>' . ((!empty($territory->code)) ? ($territory->code) : '') . '</td>';
                    $return_data .= '</tr>';
                }
            } else {
                $return_data .= '<tr><td colspan="4"><br/>' . $this->config->item("no_records") . '</td></tr>';
            }

            $return_data .= '</tbody>';
            $return_data .= '</table>';
        }
        return $return_data;
    }


    public function territories_data($page = "details")
    {
        $return_data = [
            "status" => 0,
        ];

        ## Check module access
        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data) && !empty($post_data['territory_id'])) {
                $postdata = $data['item_data'] = [];
                $postdata['account_id']     = $this->user->account_id;
                $postdata['territory_id']   = (!empty($post_data['territory_id'])) ? $post_data['territory_id'] : false ;
                $url                        = 'content/territories';
                $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');

                if (isset($API_result)) {
                    $return_data['item_data']               = (($API_result->status == true) && !empty($API_result->territories)) ? ($this->load_territory_update_view(current($API_result->territories))) : ($data['settings']) ;
                    $return_data['status']      = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "No Setting ID submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    private function load_territory_update_view($territory_item = false)
    {
        $return_data = '';
        if (!empty($territory_item)) {
            $url                        = 'access/check_module_access';
            $postdata["account_id"]     = $this->user->account_id;
            $postdata["user_id"]        = $this->user->id;
            $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');
            $module_access              = (($API_result->status > 0) && (!empty($API_result->module_access))) ? $API_result->module_access : false ;

            $allowed_modules            = $this->allowed_modules;

            $return_data .= '<div class="slide-group"><form id="territory_update_in_modal">';

            $return_data .= '<input type="hidden" name="territory_id" value="' . ((!empty($territory_item->territory_id)) ? $territory_item->territory_id : '') . '" />';

            $return_data .= '<div class="input-container">';
            $return_data .= '<p><span class="setting-name" style="font-size: 120%; font-weight: 800;">' . ((!empty($territory_item->country)) ? $territory_item->country : '') . '</span></p>';
            $return_data .= '</div>';

            $return_data .= '<div class="input-container">';
            $return_data .= '<label class="input-label">Country:</label>';
            $return_data .= '<input class="input-field" name="country" value="' . ((!empty($territory_item->country)) ? $territory_item->country : '') . '" />';
            $return_data .= '</div>';

            $return_data .= '<div class="input-container">';
            $return_data .= '<label class="input-label">Code:</label>';
            $return_data .= '<input class="input-field" name="code" value="' . ((!empty($territory_item->code)) ? $territory_item->code : '') . '" />';
            $return_data .= '</div>';

            $return_data .= '<div class="rows update_div" style="position: absolute;bottom: 30px;width: 50%;">';
            $return_data .= '<div>';
            $item_access = $this->webapp_service->check_access($this->user, $this->module_id, "details");
            if ($this->user->is_admin || !empty($item_access->can_edit) || !empty($item_access->is_admin)) {
                $return_data .= '<button id="updateTerritoryBtn" class="btn-success btn-block pull-left" style="width: 195px;">Update Territory</button>';
            } else {
                $return_data .= '<button class="btn-success btn-block no-permissions" disabled style="width: 100%;margin-top: 10px;">No Permissions</button>';
            }
            $return_data .= '</div>';
            $return_data .= '</div>';

            $return_data .= '</form>';

            $return_data .= '<form id="delete_update_in_modal">';
            $return_data .= '<div class="pull-right delete_div">';
            $return_data .= '<div class="row">';
            $item_access = $this->webapp_service->check_access($this->user, $this->module_id, "details");
            if ($this->user->is_admin || !empty($item_access->can_delete) || !empty($item_access->is_admin)) {
                $return_data .= '<button id="deleteTerritoryBtn" style="width: 195px;" type="submit" class="btn-danger btn-block" data-territory_id="' . ((!empty($territory_item->territory_id)) ? $territory_item->territory_id : '') . '">Delete Value</button>';
            } else {
                $return_data .= '<button class="btn-danger btn btn-sm btn-flow btn-next submit no-permissions pull-left no-permission-btn" disabled>No Permissions</button>';
            }
            $return_data .= '</div>';
            $return_data .= '</div>';
            $return_data .= '</form>';
            $return_data .= '</div>';
        }
        return $return_data;
    }


    public function add_territory($page = "details")
    {
        $return_data['status'] = false;

        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data)) {
                $postdata                       = [];

                if (!empty($post_data['country'])) {
                    $postdata['account_id']         = $this->user->account_id;
                    $postdata['territory_data']     = (!empty($post_data)) ? $post_data : false ;
                } else {
                    # Country is missing - shouldn't process
                    $return_data['status_msg'] = "Missing Country Name";
                    print_r(json_encode($return_data));
                    die();
                }

                $url            = 'content/add_territory';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result)) {
                    $return_data['setting'] = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->new_territory)) ? $API_result->new_territory : null;
                    $return_data['status']      = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request uncompleted!';
                }
            } else {
                $return_data['status_msg'] = "No Module or no data submitted";
            }

            print_r(json_encode($return_data));
            die();
        }
    }


    public function delete_territory($page = "details")
    {
        $return_data['status'] = false;

        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data)) {
                $postdata                       = [];

                if (!empty($post_data['territory_id'])) {
                    $postdata['account_id']     = $this->user->account_id;
                    $postdata['territory_id']   = $post_data['territory_id'];

                    $url            = 'content/delete_territory';
                    $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                    if (!empty($API_result)) {
                        $return_data['status']      = (isset($API_result->status)) ? $API_result->status : false ;
                        $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                    } else {
                        $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request uncompleted!';
                    }
                } else {
                    # Country is missing - shouldn't process
                    $return_data['status_msg'] = "Missing Territory ID";
                }
            } else {
                $return_data['status_msg'] = "No required data submitted";
            }

            print_r(json_encode($return_data));
            die();
        }
    }


    public function update_territory($page = "details")
    {
        $return_data['status'] = false;

        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data) && !empty($post_data['territory_id'])) {
                $postdata                       = [];
                $postdata['account_id']         = $this->user->account_id;
                $postdata['territory_data']     = $post_data;
                $postdata['territory_id']       = $post_data['territory_id'];

                $url            = 'content/update_territory';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                ## u_territory = updated_territory
                if (!empty($API_result)) {
                    $return_data['u_territory'] = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->u_territory)) ? $API_result->u_territory : null;
                    $return_data['status']      = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }

            print_r(json_encode($return_data));
            die();
        }
    }




    /**
    *   Get the Distribution Server(s) from the CaCTi
    */
    public function get_distribution_server($page = "details")
    {
        $return_data = "";

        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data .= $this->config->item('ajax_access_denied');
        } else {
            $postdata                   = [];
            $postdata['account_id']     = $this->user->account_id;
            $url                        = 'distribution/distribution_server';
            $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');

            if (($API_result->status == true) && (!empty($API_result->d_servers))) {
                $return_data    = $this->load_ds_view($API_result->d_servers);
            } else {
                $return_data    .= '<tr><td colspan="3">';
                $return_data    .= (!empty($API_result->message)) ? '<h4>' . $API_result->message . '</h4>' : '<h4>No records found</h4>';
                $return_data    .= '<span class="new_item"><button class="btn-success btn-block btn-small create-distribution-server-modal">Add Server</button></span>';
                $return_data .= '</td></tr>';
            }
        }

        print_r($return_data);
        die();
    }


    /**
    *   A view for the distribution server(s)
    */
    private function load_ds_view($servers = false)
    {
        $return_data = '';

        $return_data .= '<h4>Distribution Servers</h4><span class="new_item"><button class="btn-success btn-block btn-small create-distribution-server-modal">Add Server</button></span>';
        $return_data .= '<table class="table table-responsive">';
        $return_data .= '<thead>';
        $return_data .= '<tr>';
        $return_data .= '<th>Server Name</th>';
        $return_data .= '<th>Notifications point(s)</th>';
        $return_data .= '<th title="The last logged into the CaCTi server status">Server Running Status</th>';
        $return_data .= '<th>Descriptions</th>';
        $return_data .= '</thead>';
        $return_data .= '<tbody>';

        if (!empty($servers)) {
            foreach ($servers as $k => $server) {
                $return_data .= '<tr class="clickable" data-server_id="' . $server->server_id . '" href="#server-profile-modal" role="button" class="btn" data-toggle="modal">';
                $return_data .= '<td>' . ((!empty($server->server_name)) ? $server->server_name : '') . '</td>';
                if (!empty($server->notification_points)) {
                    $return_data .= '<td>';
                    $str = "";
                    $str = implode(", ", array_column($server->notification_points, "email"));
                    if (strlen($str) > 100) {
                        $return_data .= strWordCut($str, 100);
                    } else {
                        $return_data .= ($str);
                    }
                    $return_data .= '</td>';
                } else {
                    $return_data .= '<td> - </td>';
                }

                $return_data .= '<td>' . ((!empty($server->coggins_running)) ? ucwords($server->coggins_running) : '') . '</td>';
                $return_data .= '<td>' . ((!empty($server->description)) ? $server->description : '') . '</td>';
                $return_data .= '</tr>';
            }
        } else {
            $return_data .= '<tr><td colspan="4"><br/>' . $this->config->item("no_records") . '</td></tr>';
        }

        $return_data .= '</tbody>';
        $return_data .= '</table>';

        return $return_data;
    }


    /**
    *   Get the list of available Distribution Server(s) from the Coggins
    */
    public function get_available_servers($page = "details")
    {
        $return_data = "";

        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data .= $this->config->item('ajax_access_denied');
        } else {
            $postdata                   = [];
            $postdata['account_id']     = $this->user->account_id;
            ## filter
            $postdata['where']['filter']['field']   = "status";
            $postdata['where']['filter']['value']   = "active";
            ## ordering
            $postdata['where']['order']['field']    = "company";
            $postdata['where']['order']['seq']      = "up";

            $url                        = 'distribution/available_servers';
            $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');

            if (($API_result->status == true) && (!empty($API_result->a_servers))) {
                $return_data    .= $this->select_view($API_result->a_servers);
            } else {
                $return_data    .= '<tr><td colspan="3">';
                $return_data    .= (!empty($API_result->message)) ? '<h4>' . $API_result->message . '</h4>' : '<h4>No records found</h4>';
                $return_data    .= '<span class="new_item"><button class="btn-success btn-block btn-small create-distribution-server-modal">Add Server</button></span>';
                $return_data    .= '</td></tr>';
            }
        }

        print_r($return_data);
        die();
    }


    private function select_view($data = false)
    {
        $result = "";
        if (!empty($data)) {
            $result .= '<option value="">Please select server</option>';
            foreach ($data as $row) {
                $result .= '<option value="' . $row->server->id . '" data-server_data="' . (base64_encode(json_encode($row))) . '">' . (ucfirst($row->server->name)) . ' (' . (ucfirst($row->server->running)) . ')</option>';
            }
        }
        return $result;
    }



    public function add_server($page = "details")
    {
        $return_data['status'] = false;

        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data)) {
                $postdata                       = [];

                if (!empty($post_data['coggins_server_id'] && !empty($post_data['notification_points']) && !empty($post_data['server_data']))) {
                    $postdata['account_id']             = $this->user->account_id;
                    $postdata['coggins_server_id']      = (!empty($post_data['coggins_server_id'])) ? (int) $post_data['coggins_server_id'] : false ;
                    $postdata['notification_points']    = (!empty($post_data['notification_points'])) ? ($post_data['notification_points']) : false ;
                    $postdata['server_description']     = (!empty($post_data['server_description'])) ? ($post_data['server_description']) : false ;
                    $postdata['server_data']            = (!empty($post_data['server_data'])) ? convert_to_array($post_data['server_data']) : false ;
                } else {
                    $return_data['status_msg'] = "Missing required data";
                    print_r(json_encode($return_data));
                    die();
                }

                $url                = 'distribution/add_server';
                $API_result         = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result)) {
                    $return_data['server']      = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->server)) ? $API_result->server : null;
                    $return_data['status']      = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request uncompleted!';
                }
            } else {
                $return_data['status_msg'] = "No data submitted";
            }

            print_r(json_encode($return_data));
            die();
        }
    }


    /**
    *   Get Genres saved in CaCTi
    */
    public function get_genres($page = "details")
    {
        $return_data = "";

        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data .= $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            $postdata                   = [];
            $postdata['account_id']     = $this->user->account_id;
            $postdata['where']['genre_type_id'] = (!empty($post_data['genre_type_id'])) ? (int) $post_data['genre_type_id'] : false ;
            $url                        = 'content/genres';
            $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');

            if (($API_result->status == true) && (!empty($API_result->genres))) {
                $return_data    = $this->load_genre_view($API_result->genres, (!empty($post_data['genre_type_name'])) ? $post_data['genre_type_name'] : false, (!empty($post_data['genre_type_id'])) ? $post_data['genre_type_id'] : false);
            } else {
                $return_data    .= '<tr><td colspan="3">';
                $return_data    .= (!empty($API_result->message)) ? '<h4>' . $API_result->message . '</h4>' : '<h4>Setting(s) data not found</h4>';
                $return_data    .= '<span class="new_item"><button class="btn-success btn-block btn-small create-genre-modal" data-genre_type_name="' . ((!empty($post_data['genre_type_name'])) ? $post_data['genre_type_name'] : '') . '" data-genre_type_id="' . ((!empty($post_data['genre_type_id'])) ? $post_data['genre_type_id'] : '') . '">Add Genre</button></span>';
                $return_data .= '</td></tr>';
            }
        }

        print_r($return_data);
        die();
    }


    private function load_genre_view($data = false, $genre_type_name = false, $genre_type_id = false)
    {
        $return_data = '';
        if (!empty($data)) {
            $return_data .= '<h4>' . ((!empty($genre_type_name)) ? $genre_type_name : 'Genre(s)') . '</h4><span class="new_item"><button class="btn-success btn-block btn-small create-genre-modal" data-genre_type_name="' . ((!empty($genre_type_name)) ? $genre_type_name : '') . '" data-genre_type_id="' . ((!empty($genre_type_id)) ? $genre_type_id : '') . '">Add Genre</button></span>';
            $return_data .= '<table class="table table-responsive">';
            $return_data .= '<thead>';
            $return_data .= '<tr>';
            $return_data .= '<th>Genre Name</th>';
            $return_data .= '<th>Easel ID</th>';
            $return_data .= '<th>Created Date</th>';
            $return_data .= '</thead>';
            $return_data .= '<tbody>';

            if (!empty($data)) {
                foreach ($data as $k => $genre) {
                    $return_data .= '<tr class="clickable" data-genre_id="' . $genre->genre_id . '" href="#genre-profile-modal" role="button" class="btn" data-toggle="modal">';
                    $return_data .= '<td>' . ((!empty($genre->genre_name)) ? $genre->genre_name : '') . '</td>';
                    $return_data .= '<td>' . ((!empty($genre->easel_id)) ? $genre->easel_id : '') . '</td>';
                    $return_data .= '<td>' . ((!empty($genre->created_date)) ? ucwords($genre->created_date) : '') . '</td>';
                    $return_data .= '</tr>';
                }
            } else {
                $return_data .= '<tr><td colspan="4"><br/>' . $this->config->item("no_records") . '</td></tr>';
            }

            $return_data .= '</tbody>';
            $return_data .= '</table>';
        }

        return $return_data;
    }



    public function create_genre($page = "details")
    {
        $return_data['status']      = false;
        $return_data['genre']       = false;
        $return_data['status_msg']  = "Request failed";

        $section                = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access            = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data) && !empty($post_data['genre_name']) && !empty($post_data['genre_type_id'])) {
                $postdata                       = [];
                $postdata['account_id']         = $this->user->account_id;
                $postdata['genre_name']         = $post_data['genre_name'];
                $postdata['genre_type_id']      = $post_data['genre_type_id'];

                $url            = 'content/genre';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->genre)) {
                    $return_data['genre']       = $API_result->genre;
                    $return_data['status']      = $API_result->status;
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "Insufficient data provided";
            }

            print_r(json_encode($return_data));
            die();
        }
    }





    /**
    *   Get Age rating(s)
    */
    public function get_age_rating($page = "details")
    {
        $return_data = "";

        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data .= $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            $postdata                   = [];
            $postdata['account_id']     = $this->user->account_id;
            $postdata['where']['age_classification_id'] = (!empty($post_data['age_classification_id'])) ? (int) $post_data['age_classification_id'] : false ;
            $url                        = 'content/age_rating';
            $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');

            if (($API_result->status == true) && (!empty($API_result->age_rating))) {
                $return_data    = $this->load_age_rating_view($API_result->age_rating, (!empty($post_data['age_classification_name'])) ? $post_data['age_classification_name'] : false, (!empty($post_data['age_classification_id'])) ? $post_data['age_classification_id'] : false);
            } else {
                $return_data    .= '<tr><td colspan="3">';
                $return_data    .= (!empty($API_result->message)) ? '<h4>' . $API_result->message . '</h4>' : '<h4>Setting(s) data not found</h4>';
                $return_data    .= '<span class="new_item" style="display: none;"><button class="btn-success btn-block btn-small create-age-rating-modal" data-age_classification_name="' . ((!empty($post_data['age_classification_name'])) ? $post_data['age_classification_name'] : '') . '" data-age_classification_id="' . ((!empty($post_data['age_classification_id'])) ? $post_data['age_classification_id'] : '') . '">Add Age Rating</button></span>';
                $return_data .= '</td></tr>';
            }
        }

        print_r($return_data);
        die();
    }


    private function load_age_rating_view($data = false, $age_classification_name = false, $age_classification_id = false)
    {
        $return_data = '';
        if (!empty($data)) {
            $return_data .= '<h4>' . ((!empty($age_classification_name)) ? $age_classification_name : 'Age Rating(s)') . '</h4><span class="new_item" style="display: none;"><button class="btn-success btn-block btn-small create-age-rating-modal" data-age_classification_name="' . ((!empty($age_classification_name)) ? $age_classification_name : '') . '" data-age_classification_id="' . ((!empty($age_classification_id)) ? $age_classification_id : '') . '">Add Age Rating</button></span>';
            $return_data .= '<table class="table table-responsive">';
            $return_data .= '<thead>';
            $return_data .= '<tr>';
            $return_data .= '<th>Genre Name</th>';
            $return_data .= '<th>Easel ID</th>';
            $return_data .= '<th>Created Date</th>';
            $return_data .= '</thead>';
            $return_data .= '<tbody>';

            if (!empty($data)) {
                foreach ($data as $k => $rating) {
                    $return_data .= '<tr class="clickable" data-age_rating_id="' . $rating->age_rating_id . '" href="#age-rating-profile-modal" role="button" class="btn" data-toggle="modal">';
                    $return_data .= '<td>' . ((!empty($rating->age_rating_name)) ? $rating->age_rating_name : '') . '</td>';
                    $return_data .= '<td>' . ((!empty($rating->easel_id)) ? $rating->easel_id : '') . '</td>';
                    $return_data .= '<td>' . ((!empty($rating->created_date)) ? ucwords($rating->created_date) : '') . '</td>';
                    $return_data .= '</tr>';
                }
            } else {
                $return_data .= '<tr><td colspan="4"><br/>' . $this->config->item("no_records") . '</td></tr>';
            }

            $return_data .= '</tbody>';
            $return_data .= '</table>';
        }

        return $return_data;
    }



    /**
    *   Get single Server data
    */
    public function get_server($page = "details")
    {
        $return_data = [
            "item_data"     => [],
            "status"        => false,
            "status_msg"    => "",
        ];

        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data['status_msg'] .= $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            $postdata                   = [];
            $postdata['account_id']     = $this->user->account_id;
            $postdata['where']['server_id'] = (!empty($post_data['server_id'])) ? (int) $post_data['server_id'] : false ;
            $url                        = 'distribution/distribution_server';
            $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');

            if (isset($API_result->d_servers)) {
                $return_data['item_data']   = (($API_result->status == true) && !empty($API_result->d_servers)) ? ($this->load_server_view($API_result->d_servers)) : (false) ;
                $return_data['status']      = (isset($API_result->status)) ? $API_result->status : false ;
                $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
            } else {
                $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                // $return_data     .= '<tr><td colspan="3">';
                // $return_data     .= ( !empty( $API_result->message ) ) ? '<h4>'.$API_result->message.'</h4>' : '<h4>Setting(s) data not found</h4>';
                // $return_data     .= '<span class="new_item"><button class="btn-success btn-block btn-small create-distribution-server-modal">Add Server</button></span>';
                // $return_data .= '</td></tr>';
            }
        }

        print_r(json_encode($return_data));
        die();
    }

    private function load_server_view($data = false)
    {
        $return_data = '';
        if (!empty($data)) {
            // $url                     = 'access/check_module_access';
            // $postdata["account_id"]      = $this->user->account_id;
            // $postdata["user_id"]     = $this->user->id;
            // $API_result                  = $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );
            // $module_access               = ( ( $API_result->status > 0 ) && ( !empty( $API_result->module_access ) ) ) ? $API_result->module_access : false ;

            // $territories             = $postdata = [];
            // $postdata['account_id']      = $this->user->account_id;
            // $url                     = 'content/territories';
            // $API_result                  = $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
            // $territories             = ( isset( $API_result->status ) && ( $API_result->status == true ) && !empty( $API_result->territories ) ) ? $API_result->territories : null;
            $return_data .= '<div class="slide-group" style="padding-bottom: 70px;">';
            $return_data .= '<form id="servers_update_in_modal">';

            $return_data .= '<input type="hidden" name="server_id" value="' . ((!empty($data->server_id)) ? $data->server_id : '') . '" />';

            $return_data .= '<div class="input-container">';
            $return_data .= '<p>For the setting Name: <span class="setting-name" style="font-size: 120%; font-weight: 800;">Distribution Servers</span></p>';
            $return_data .= '</div>';

            $return_data .= '<div class="input-container">';
            $return_data .= '<label class="input-label-40" style="background: #eee;">Server Name:</label>';
            $return_data .= '<input class="input-field-60" style="background: #eee;" value="' . ((!empty($data->server_name)) ? $data->server_name : '') . '" disabled="disabled" />';
            $return_data .= '</div>';

            $return_data .= '<div class="input-container">';
            $return_data .= '<label class="input-label-40">Server Description:</label>';
            $return_data .= '<input class="input-field-60" name="description" value="' . ((!empty($data->description)) ? $data->description : '') . '" />';
            $return_data .= '</div>';

            $return_data .= '<div class="input-container">';
            $return_data .= '<p style="margin-top: 20px;">Notification points: <span class="" style="font-size: 120%; font-weight: 800;"><span class="pull-right add-point" data-server_id="' . ((!empty($data->server_id)) ? $data->server_id : '') . '"><a type="button" class="" data-toggle="modal" data-target="#addPoint"><i class="fas fa-plus-circle"></i></a></span></span></p>';
            $return_data .= '</div>';

            if (!empty($data->notification_points)) {
                foreach ($data->notification_points as $point) {
                    $return_data .= '<div class="input-container">';
                    $return_data .= '<label class="input-label-40">Email:</label>';
                    $return_data .= '<input class="input-field-60" style=" padding-right: 40px;" data-point_id = "' . ((!empty($point->point_id)) ? $point->point_id : '') . '"  name="email[' . ((!empty($point->point_id)) ? $point->point_id : '') . ']" value="' . ((!empty($point->email)) ? $point->email : '') . '" />';
                    $return_data .= '<span id="point' . ($point->point_id) . '" class="delete_point" style="position: absolute; top: 10px; right: 10px;" data-point_id="' . ((!empty($point->point_id)) ? $point->point_id : '') . '"><a href="#" id="link_point' . ($point->point_id) . '" class="a_link"><i id="trash_point' . ($point->point_id) . '" class="fas fa-trash-alt" style="font-size: 20px; color: #b7001f;"></i></a></span>';
                    $return_data .= '</div>';
                }
            }

            $return_data .= '<div class="rows update_div" style="position: absolute;bottom: 30px;width: 50%;">';
            $return_data .= '<div>';
            $item_access = $this->webapp_service->check_access($this->user, $this->module_id, "details");
            if ($this->user->is_admin || !empty($item_access->can_edit) || !empty($item_access->is_admin)) {
                $return_data .= '<button id="updateServerBtn" class="btn-success btn-block pull-left" style="width: 195px;">Update Server Details</button>';
            } else {
                $return_data .= '<button class="btn-success btn-block no-permissions" disabled style="width: 100%;margin-top: 10px;">No Permissions</button>';
            }
            $return_data .= '</div>';
            $return_data .= '</div>';

            $return_data .= '</form>';

            $return_data .= '<form id="delete_server_in_modal">';
            $return_data .= '<div class="pull-right delete_div">';
            $return_data .= '<div class="row">';
            $item_access = $this->webapp_service->check_access($this->user, $this->module_id, "details");
            if ($this->user->is_admin || !empty($item_access->can_delete) || !empty($item_access->is_admin)) {
                $return_data .= '<button id="deleteServerBtn" style="width: 195px;" type="submit" class="btn-danger btn-block" data-server_id="' . ((!empty($data->server_id)) ? $data->server_id : '') . '">Delete Server</button>';
            } else {
                $return_data .= '<button class="btn-danger btn btn-sm btn-flow btn-next submit no-permissions pull-left no-permission-btn" disabled>No Permissions</button>';
            }
            $return_data .= '</div>';
            $return_data .= '</div>';
            $return_data .= '</form>';
            $return_data .= '</div>';
        }

        return $return_data;
    }


    /*
    *   To delete a server notification point
    */
    public function delete_notification_point($page = "details")
    {
        $return_data = [
            "status"        => false,
            "status_msg"    => "",
        ];

        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data['status_msg'] .= $this->config->item('ajax_access_denied');
        } else {
            $post_data                  = $this->input->post();
            $postdata                   = [];
            $postdata['account_id']     = $this->user->account_id;
            $postdata['point_id']       = (!empty($post_data['pointId'])) ? (int) $post_data['pointId'] : false ;
            $url                        = 'distribution/delete_notification_point';
            $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

            if (isset($API_result->status) && ($API_result->status == true)) {
                $return_data['status']      = $API_result->status;
                $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
            } else {
                $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    /*
    *   To add a server notification point
    */
    public function add_notification_point($page = "details")
    {
        $return_data = [
            "data"          => false,
            "status"        => false,
            "status_msg"    => "",
        ];

        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data['status_msg'] .= $this->config->item('ajax_access_denied');
        } else {
            $post_data                  = $this->input->post();
            $postdata['account_id']     = $this->user->account_id;
            $postdata['server_id']      = (!empty($post_data['server_id'])) ? (int) $post_data['server_id'] : false ;
            $postdata['email']          = (!empty($post_data['email'])) ? $post_data['email'] : false ;
            $url                        = 'distribution/add_notification_point';
            $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

            if (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->point)) {
                $return_data['data']        = $API_result->point;
                $return_data['status']      = $API_result->status;
                $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
            } else {
                $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
            }
        }

        print_r(json_encode($return_data));
        die();
    }



    /*
    *   To update a server details / notification points
    */
    public function update_server($page = "details")
    {
        $return_data = [
            "data"          => false,
            "status"        => false,
            "status_msg"    => "",
        ];

        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data['status_msg'] .= $this->config->item('ajax_access_denied');
        } else {
            $post_data                  = $this->input->post();
            $postdata['account_id']     = $this->user->account_id;
            $postdata['server_id']      = (!empty($post_data['server_id'])) ? (int) $post_data['server_id'] : false ;
            $postdata['description']    = (!empty($post_data['description'])) ? $post_data['description'] : false ;
            $postdata['email']          = (!empty($post_data['email'])) ? ($post_data['email']) : false ;
            $url                        = 'distribution/update_server';
            $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

            if (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->server)) {
                $return_data['data']        = $API_result->server;
                $return_data['status']      = $API_result->status;
                $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
            } else {
                $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
            }
        }

        print_r(json_encode($return_data));
        die();
    }

    /*
    *   To delete a server details / notification points
    */
    public function delete_server($page = "details")
    {
        $return_data = [
            "data"          => false,
            "status"        => false,
            "status_msg"    => "",
        ];

        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data['status_msg'] .= $this->config->item('ajax_access_denied');
        } else {
            $post_data                  = $this->input->post();
            $postdata['account_id']     = $this->user->account_id;
            $postdata['server_id']      = (!empty($post_data['server_id'])) ? (int) $post_data['server_id'] : false ;
            $url                        = 'distribution/delete_server';
            $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

            if (isset($API_result->status) && ($API_result->status == true)) {
                $return_data['status']      = $API_result->status;
                $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
            } else {
                $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
            }
        }

        print_r(json_encode($return_data));
        die();
    }

    /*
    *   Not done yet
    */
    public function create_age_rating($page = "details")
    {
        $return_data['status']      = false;
        $return_data['age_rating']      = false;
        $return_data['status_msg']  = "Request failed";

        $section                = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access            = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data) && !empty($post_data['age_classification_name']) && !empty($post_data['age_classification_id'])) {
                print_r(json_encode($return_data));
                die();

                $postdata                               = [];
                $postdata['account_id']                 = $this->user->account_id;
                $postdata['age_classification_name']    = html_escape($post_data['age_classification_name']);
                $postdata['age_classification_id']      = (int) $post_data['age_classification_id'];

                $url                                    = 'content/age_rating';
                $API_result                             = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->age_rating)) {
                    $return_data['age_rating']          = $API_result->age_rating;
                    $return_data['status']              = $API_result->status;
                    $return_data['status_msg']          = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']          = (isset($API_result->message)) ? $API_result->message : 'Request unsuccessful!';
                }
            } else {
                $return_data['status_msg']              = "Insufficient data provided";
            }

            print_r(json_encode($return_data));
            die();
        }
    }
}
