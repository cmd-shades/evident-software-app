<?php

namespace Application\Modules\Web\Controllers;

use Application\Extentions\MX_Controller;

class Site extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        $this->module_id        = $this->webapp_service->_get_module_id($this->router->fetch_class());
        $this->months           = ['January', 'February','March','April','May','June','July','August','September','October','November', 'December'];
        $this->load->library('pagination');
        $this->load->model('serviceapp/Site_model', 'site_service');
        $this->load->model('serviceapp/Device_model', 'device_service');
        $this->load->model('serviceapp/Address_Bank_model', 'address_bank_service');

        $this->invoice_to           = ['integrator', 'site'];
    }


    //redirect if needed, otherwise display the user list
    public function index()
    {
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            //access denied
            $this->_render_webpage('errors/access-denied', false);
        } else {
            redirect('webapp/site/sites', 'refresh');
        }
    }

    /** Get list of sites **/
    public function sites($site_id = false)
    {
        if ($site_id) {
            redirect('webapp/site/profile/' . $site_id, 'refresh');
        }

        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $data['module_id']      = $this->module_id;
            $site_statuses          = $this->webapp_service->api_dispatcher($this->api_end_point . 'site/site_statuses', ['account_id' => $this->user->account_id], ['auth_token' => $this->auth_token], true);
            $data['site_statuses']  = (isset($site_statuses->site_statuses)) ? $site_statuses->site_statuses : null;
            ;
            $data['current_user']   = $this->user;
            $room_totalizer         = $this->webapp_service->api_dispatcher($this->api_end_point . 'site/room_totalizer', ['account_id' => $this->user->account_id], ['auth_token' => $this->auth_token], true);
            $data['room_totalizer'] = (isset($room_totalizer->room_totalizer)) ? $room_totalizer->room_totalizer : null;
            ;
            $this->_render_webpage('site/index', $data);
        }
    }

    //View site profile
    public function profile($site_id = false, $page = 'details')
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, 'details');
        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } elseif ((int) $site_id) {
            $run_admin_check        = false;

            $data['site_details']   = $postdata = [];
            $postdata['account_id'] = $this->user->account_id;
            $postdata['site_id']    = (int) $site_id;
            $site_details           = $this->webapp_service->api_dispatcher($this->api_end_point . 'site/sites', $postdata, ['auth_token' => $this->auth_token], true);
            $data['site_details']   = (isset($site_details->sites)) ? $site_details->sites : null;

            if (!empty($data['site_details'])) {
                $run_admin_check    = false;
                #Get allowed access for the logged in user
                $data['permissions'] = $item_access;
                $data['active_tab'] = $page;

                $module_items       = $this->webapp_service->api_dispatcher($this->api_end_point . 'access/account_modules_items', ['account_id' => $this->user->account_id, 'module_id' => $this->module_id ], ['auth_token' => $this->auth_token], true);
                $data['module_tabs'] = (isset($module_items->module_items)) ? $module_items->module_items : null;

                $data['delivery_mechanism']     = $postdata = [];
                $postdata['account_id']         = $this->user->account_id;
                $postdata['where']['module_id']         = $this->module_id; ## Site module
                $postdata['where']['setting_name_group']    = "2_delivery_mechanism"; ## 'Delivery Mechanism'
                $url                            = 'settings/settings';
                $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                $data['delivery_mechanism']     = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->settings)) ? $API_result->settings : null;

                ## - get price plan(s)
                $data['all_price_plans']            = $postdata = [];
                $postdata['account_id']                 = $this->user->account_id;
                ## $postdata['where']['price_plan_type']    = "airtime"; ## Airtime Price plan
                $url                                    = 'provider/price_plan';
                $API_result                             = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                $data['all_price_plans']            = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->price_plan)) ? $API_result->price_plan : null;

                $data['sale_currencies']        = $postdata = [];
                $postdata['account_id']         = $this->user->account_id;
                $postdata['where']['module_id']         = $this->module_id; ## Site module
                $postdata['where']['setting_name_id']   = 7; ## 'Sale Currencies'
                $url                            = 'settings/settings';
                $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                $data['sale_currencies']        = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->settings)) ? $API_result->settings : null;

                $data['no_of_titles_packages']              = $postdata = [];
                $postdata['account_id']                     = $this->user->account_id;
                $postdata['where']['module_id']             = $this->module_id; ## Site module
                $postdata['where']['setting_name_group']    = "2_no_of_titles"; ## 'no of titles'
                $url                                        = 'settings/settings';
                $API_result                                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                $data['no_of_titles_packages']              = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->settings)) ? $API_result->settings : null;

                $data['films_per_month']                    = $postdata = [];
                $postdata['account_id']                     = $this->user->account_id;
                $postdata['where']['setting_name_group']    = "2_films_per_month"; ## 'films per month'
                $url                                        = 'settings/settings';
                $API_result                                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                $data['films_per_month']                = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->settings)) ? $API_result->settings : null;

                switch ($page) {
                    case 'documents':
                        $data['include_page']           = 'site_documents.php';
                        break;

                    case 'product':
                        $data['content_providers']      = $postdata = [];
                        $postdata['account_id']         = $this->user->account_id;

                        if (!empty($data['site_details']->system_type_id)) {
                            $postdata['where']['system_type_id'] = $data['site_details']->system_type_id;
                        }

                        $url                            = 'provider/provider';
                        $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['content_providers']      = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->content_provider)) ? $API_result->content_provider : null;


                        $data['content_providers_linear']   = $postdata = [];
                        $postdata['account_id']             = $this->user->account_id;
                        $postdata['where']['setting_group_name']    = "channel";
                        $url                                = 'provider/provider';
                        $API_result                         = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['content_providers_linear']   = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->content_provider)) ? $API_result->content_provider : null;


                        $data['product_statuses']       = $postdata = [];
                        $postdata['account_id']         = $this->user->account_id;
                        $postdata['where']['module_id']             = $this->module_id; ## Site module
                        $postdata['where']['setting_name_group']    = "2_product_status"; ## 'product status'
                        $url                            = 'settings/settings';
                        $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['product_statuses']       = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->settings)) ? $API_result->settings : null;

                        $data['include_page']           = 'site_product.php';
                        break;

                    case 'inventory':
                        /* $inventory_params = [
                            'account_id'=>$this->user->account_id,
                            'where'=>[
                                'grouped'   => 1,
                                'site_id'   => $site_id,
                            ],
                            'limit' => -1
                        ];

                        $distribution_content       = $this->webapp_service->api_dispatcher( $this->api_end_point.'distribution/bundle_content', $inventory_params, ['auth_token'=>$this->auth_token], true );
                        $distribution_content       = ( isset( $distribution_content->bundle_content ) )? $distribution_content->bundle_content : null;

                        $data['current_films']      = !empty( $distribution_content->current_films )    ? $distribution_content->current_films : false;
                        $data['library_films']      = !empty( $distribution_content->library_films )    ? $distribution_content->library_films : false;
                        $data['recylable_films']    = !empty( $distribution_content->recylable_films )  ? $distribution_content->recylable_films : false;
                        $data['all_films']          = !empty( $distribution_content->all_films )        ? $distribution_content->all_films : false;
                        $data['include_page']       = 'site_inventory.php'; */
                        break;


                    case 'details':
                    default:
                        $data['invoice_to']             = $this->invoice_to;
                        $data['systems']                = $postdata = [];
                        $url                            = 'systems/systems';
                        $postdata['account_id']         = $this->user->account_id;
                        $postdata['where']['integrator_id'] = $data['site_details']->system_integrator_id;
                        $API_call                       = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['systems']                = (isset($API_call->status) && ($API_call->status == true) && !empty($API_call->systems)) ? $API_call->systems : $data['systems'];

                        $data['charge_frequencies']     = $postdata = [];
                        $postdata['account_id']         = $this->user->account_id;
                        $postdata['where']['module_id']             = $this->module_id; ## Site module
                        $postdata['where']['setting_name_group']    = "2_charge_frequency"; ## 'charge_frequency'
                        $url                            = 'settings/settings';
                        $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['charge_frequencies']     = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->settings)) ? $API_result->settings : null;

                        $data['content_providers_linear']   = $postdata = [];
                        $postdata['account_id']             = $this->user->account_id;
                        $postdata['where']['setting_group_name']    = "channel";
                        $url                                = 'provider/provider';
                        $API_result                         = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['content_providers_linear']   = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->content_provider)) ? $API_result->content_provider : null;

                        $data['distribution_groups']    = $postdata = [];
                        $postdata['account_id']         = $this->user->account_id;
                        $postdata['where']['module_id']         = $this->module_id; ## Site module
                        $postdata['where']['setting_name_id']   = 26; ## 'Distribution Groups' - name from the setting_name table

                        if (!empty($data['site_details']->content_territory_id)) {
                            $postdata['where']['setting_territory_id']      = $data['site_details']->content_territory_id;
                        }
                        $url                            = 'settings/settings';
                        $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');

                        $data['distribution_groups']    = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->settings)) ? $API_result->settings : null;

                        $data['invoice_currencies'] = $postdata = [];
                        $postdata['account_id']     = $this->user->account_id;
                        $postdata['where']['module_id']         = $this->module_id; ## Site module
                        $postdata['where']['setting_name_id']   = 7; ## 'Charge Frequency' - name from the setting_name table
                        $url                        = 'settings/settings';
                        $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['invoice_currencies'] = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->settings)) ? $API_result->settings : null;

                        $data['site_products']          = $postdata = [];
                        $url                            = 'product/product';
                        $postdata['account_id']         = $this->user->account_id;
                        $postdata['where']['site_id']   = $site_id;
                        $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['site_products']          = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->product)) ? $API_result->product : $data['site_products'];

                        $data['site_monthly_value']     = $postdata = [];
                        $url                            = 'site/site_value';
                        $postdata['account_id']         = $this->user->account_id;
                        $postdata['site_id']            = $site_id;
                        $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['site_monthly_value']     = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->site_value)) ? $API_result->site_value : $data['site_monthly_value'];

                        $data['content_providers']      = $postdata = [];
                        $postdata['account_id']         = $this->user->account_id;
                        $url                            = 'provider/provider';
                        if (!empty($data['site_details']->system_type_id)) {
                            $postdata['where']['system_type_id'] = $data['site_details']->system_type_id;
                        }
                        $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['content_providers']      = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->content_provider)) ? $API_result->content_provider : null;

                        $data['territories']            = $postdata = [];
                        $postdata['account_id']         = $this->user->account_id;
                        $url                            = 'content/territories';
                        $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['territories']            = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->territories)) ? $API_result->territories : null;

                        $data['time_zones']             = $postdata = [];
                        $postdata['account_id']         = $this->user->account_id;
                        $url                            = 'site/time_zones';
                        $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['time_zones']             = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->time_zones)) ? $API_result->time_zones : null;

                        $data['product_types']          = $postdata = [];
                        $postdata['account_id']         = $this->user->account_id;
                        $postdata['where']['module_id']         = $this->module_id; ## Site module
                        $postdata['where']['setting_name_id']   = 20; ## 'Charge Frequency' - name from the setting_name table
                        $url                            = 'settings/settings';
                        $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['product_types']          = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->settings)) ? $API_result->settings : null;

                        $data['product_statuses']       = $postdata = [];
                        $postdata['account_id']         = $this->user->account_id;
                        $postdata['where']['module_id']             = $this->module_id; ## Site module
                        $postdata['where']['setting_name_group']    = "2_product_status"; ## 'product status'
                        $url                            = 'settings/settings';
                        $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['product_statuses']       = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->settings)) ? $API_result->settings : null;

                        $data['system_integrators']     = $postdata = [];
                        $postdata['account_id']         = $this->user->account_id;
                        $postdata['where']['integrator_status'] = "active";
                        $url                            = 'integrator/integrator';
                        $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['system_integrators']     = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->integrator)) ? $API_result->integrator : null;
                        $data['operating_companies']    = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->integrator)) ? $API_result->integrator : null;

                        $data['age_rating']                 = $postdata = [];
                        $postdata['account_id']             = $this->user->account_id;
                        $postdata['where']['is_visible_on_cacti'] = 1;
                        $url                            = 'content/age_rating';
                        $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['age_rating']             = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->age_rating)) ? $API_result->age_rating : null;

                        ## - get price plan(s)
                        $data['price_plans']        = $postdata = [];
                        $postdata['account_id']     = $this->user->account_id;
                        $url                        = 'provider/price_plan';
                        $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['price_plans']        = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->price_plan)) ? $API_result->price_plan : null;

                        $site_documents                 = $this->webapp_service->api_dispatcher($this->api_end_point . 'document_handler/document_list', ['account_id' => $this->user->account_id, 'site_id' => $site_id, 'document_group' => 'site' ], ['auth_token' => $this->auth_token], true);
                        $data['site_documents']         = (isset($site_documents->documents->{$this->user->account_id})) ? $site_documents->documents->{$this->user->account_id} : null;

                        ## Site Inventory
                        $inventory_params = [
                            'account_id' => $this->user->account_id,
                            'where' => [
                                'grouped'   => 1,
                                'site_id'   => $site_id,
                            ],
                            'limit' => -1
                        ];

                        $distribution_content       = $this->webapp_service->api_dispatcher($this->api_end_point . 'distribution/bundle_content', $inventory_params, ['auth_token' => $this->auth_token], true);
                        $distribution_content       = (isset($distribution_content->bundle_content)) ? $distribution_content->bundle_content : null;

                        ## For now Royalty mean just for the UIP which is...
                        $data['royalty_provider_id'] = $postdata = [];
                        $postdata['account_id']     = $this->user->account_id;
                        $postdata['where']['provider_reference_code']   = 'uip';
                        $url                        = 'provider/provider';
                        $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['royalty_provider_id'] = (isset($API_result->status) && ($API_result->status == true) && !empty((current((array) $API_result->content_provider))->provider_id)) ? (current((array) $API_result->content_provider))->provider_id : null;

                        $data['royalty_types']      = $postdata = [];
                        $postdata['account_id']     = $this->user->account_id;
                        $url                        = 'report/royalty_type';
                        $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['royalty_types']      = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->royalty_type)) ? $API_result->royalty_type : null;

                        $data['royalty_services']   = $postdata = [];
                        $postdata['account_id']     = $this->user->account_id;
                        $url                        = 'report/royalty_service';
                        $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['royalty_services']       = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->royalty_service)) ? $API_result->royalty_service : null;

                        $data['royalty_units']      = $postdata = [];
                        $postdata['account_id']     = $this->user->account_id;
                        $url                        = 'report/royalty_unit';
                        $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['royalty_units']      = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->royalty_unit)) ? $API_result->royalty_unit : null;

                        // Royalty Minimum Guarantee
                        $data['site_mg_royalty_setting']    = $postdata = [];
                        $postdata['account_id']             = $this->user->account_id;
                        $postdata['site_id']                = $site_id;
                        $postdata['where']['royalty_type_id']   = 1;
                        $url                                = 'report/site_royalty_setting';
                        $API_result                         = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['site_mg_royalty_setting']    = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->site_royalty_setting)) ? $API_result->site_royalty_setting : null;

                        // Royalty Revenue Share
                        $data['site_rs_royalty_setting']    = $postdata = [];
                        $postdata['account_id']             = $this->user->account_id;
                        $postdata['site_id']                = $site_id;
                        $postdata['where']['royalty_type_id']   = 2;
                        $url                                = 'report/site_royalty_setting';
                        $API_result                         = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['site_rs_royalty_setting']    = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->site_royalty_setting)) ? $API_result->site_royalty_setting : null;


                        $data['current_films']      = !empty($distribution_content->current_films) ? $distribution_content->current_films : false;
                        $data['library_films']      = !empty($distribution_content->library_films) ? $distribution_content->library_films : false;
                        $data['recylable_films']    = !empty($distribution_content->recylable_films) ? $distribution_content->recylable_films : false;
                        $data['all_films']          = !empty($distribution_content->all_films) ? $distribution_content->all_films : false;

                        $data['include_page']       = 'site_details.php';
                        break;
                }
            }


            //Run the admin check if tab needs only admin
            if (!empty($run_admin_check)) {
                if ((!admin_check($this->user->is_admin, false, (!empty($data['permissions']) ? $data['permissions']->is_admin : false)))) {
                    $data['admin_no_access'] = true;
                }
            }

            $this->_render_webpage('site/profile', $data);
        } else {
            redirect('webapp/site', 'refresh');
        }
    }

    /*
    * Site lookup / search
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
            $block_statuses = ($this->input->post('block_statuses')) ? $this->input->post('block_statuses') : false;
            $limit         = ($this->input->post('limit')) ? $this->input->post('limit') : DEFAULT_LIMIT;
            $start_index   = ($this->input->post('start_index')) ? $this->input->post('start_index') : 0;
            $offset        = (!empty($start_index)) ? (($start_index - 1) * $limit) : 0;
            $order_by      = false;
            $where         = false;

            #prepare postdata
            $postdata = [
                'account_id'        => $this->user->account_id,
                'search_term'       => $search_term,
                'block_statuses'    => $block_statuses,
                'where'             => $where,
                'order_by'          => $order_by,
                'limit'             => $limit,
                'offset'            => $offset
            ];


            $search_result  = $this->webapp_service->api_dispatcher($this->api_end_point . 'site/lookup', $postdata, ['auth_token' => $this->auth_token], true);
            $sites          = (isset($search_result->sites)) ? $search_result->sites : null;
            if (!empty($sites)) {
                ## Create pagination
                $counters       = $this->site_service->get_total_sites($this->user->account_id, $search_term, $block_statuses, $where, $order_by, $limit, $offset);//Direct access to count, this should only return a number
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

                $return_data = $this->load_sites_view($sites);
                if (!empty($pagination)) {
                    $return_data .= '<tr><td colspan="6" style="padding: 0;">';
                    $return_data .= $page_display . $pagination;
                    $return_data .= '</td></tr>';
                }
            } else {
                $return_data .= '<tr><td colspan="6">';
                $return_data .= (isset($search_result->message)) ? $search_result->message : 'No records found';
                $return_data .= '</td></tr>';
            }
        }

        print_r($return_data);
        die();
    }

    /*
    * Prepare sites views
    */
    private function load_sites_view($sites_data)
    {
        $return_data = '';

        if (!empty($sites_data)) {
            foreach ($sites_data as $k => $site_details) {
                $return_data .= '<tr>';
                $return_data .= '<td>' . ((!empty($site_details->site_id)) ? $site_details->site_id : '') . '</td>';
                $return_data .= '<td><a href="' . base_url('/webapp/site/profile/' . $site_details->site_id) . '" >' . ((!empty($site_details->site_name)) ? ($site_details->site_name) : '') . '</a></td>';
                $return_data .= '<td>' . ((!empty($site_details->system_integrator_name)) ? ($site_details->system_integrator_name) : '') . '</td>';
                $return_data .= '<td>' . $site_details->status_name . '</td>';
                // $string = implode( ", " , array_filter( [( $site_details->addressline ), ( $site_details->postcode ), ( $site_details->posttown ) ] ) );
                // $return_data .= '<td>'.$string.'</td>';
                $return_data .= '<td>' . ((!empty($site_details->fulladdress)) ? $site_details->fulladdress : '') . '</td>';
                $return_data .= '<td>' . $site_details->number_of_rooms . '</td>';
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
    *   Create new site
    **/
    public function create()
    {
        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'details');

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $data                           = false;

            $data['invoice_to']             = $this->invoice_to;

            $data['systems']                = $postdata = [];
            $postdata['account_id']         = $this->user->account_id;
            $API_call                       = $this->webapp_service->api_dispatcher($this->api_end_point . 'systems/systems', ['account_id' => $this->user->account_id], ['auth_token' => $this->auth_token], true);
            $data['systems']                = (isset($API_call->status) && ($API_call->status == true) && !empty($API_call->systems)) ? $API_call->systems : null;

            $data['charge_frequencies']     = $postdata = [];
            $postdata['account_id']         = $this->user->account_id;
            $postdata['where']['module_id']         = $this->module_id; ## Site module
            $postdata['where']['setting_name_id']   = 2; ## 'Charge Frequency' - name from the setting_name table
            $url                            = 'settings/settings';
            $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $data['charge_frequencies']     = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->settings)) ? $API_result->settings : null;

            $data['invoice_currencies']     = $postdata = [];
            $postdata['account_id']         = $this->user->account_id;
            $postdata['where']['module_id']         = $this->module_id; ## Site module
            $postdata['where']['setting_name_id']   = 7; ## 'Invoice Currency' - name from the setting_name table
            $url                            = 'settings/settings';
            $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $data['invoice_currencies']     = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->settings)) ? $API_result->settings : null;

            $data['system_integrators']     = $postdata = [];
            $postdata['account_id']         = $this->user->account_id;
            $postdata['where']['integrator_status'] = "active";
            $url                            = 'integrator/integrator';
            $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $data['system_integrators']     = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->integrator)) ? $API_result->integrator : null;
            $data['operating_company']      = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->integrator)) ? $API_result->integrator : null;

            $data['age_rating']                 = $postdata = [];
            $postdata['account_id']             = $this->user->account_id;
            $postdata['where']['is_visible_on_cacti'] = 1;
            $url                            = 'content/age_rating';
            $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $data['age_rating']             = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->age_rating)) ? $API_result->age_rating : null;

            $data['territories']            = $postdata = [];
            $postdata['account_id']         = $this->user->account_id;
            $url                            = 'content/territories';
            $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $data['territories']            = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->territories)) ? $API_result->territories : null;

            $data['time_zones']             = $postdata = [];
            $postdata['account_id']         = $this->user->account_id;
            $url                            = 'site/time_zones';
            $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $data['time_zones']             = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->time_zones)) ? $API_result->time_zones : null;

            $this->_render_webpage('site/site_create_new', $data);
        }
    }

    /**
    *   The AJAX site creation
    **/
    public function create_site($page = "details")
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
            $site_data = $this->input->post();
            if (!empty($site_data['site_details'])) {
                $post_data              = $this->input->post();

                $postdata               = array_merge(['account_id' => $this->user->account_id], $post_data);
                $postdata['site_name']  = (!empty($post_data['site_details']['site_name'])) ? $post_data['site_details']['site_name'] : false ;

                $postdata['site_details']['restrictions'] = (!empty($post_data['site_details']['restrictions'])) ? json_encode($post_data['site_details']['restrictions']) : false ;

                $url            = 'site/create';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');
                $result         = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->new_site)) ? $API_result->new_site : null;

                $message        = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                if (!empty($result)) {
                    $return_data['status'] = 1;
                    $return_data['site']   = $result;
                }
                $return_data['status_msg'] = $message;
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    /** Update SIte Details **/
    public function update_site($page = 'details')
    {
        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data['site_id'])) {
                $postdata       = [];
                $postdata['account_id']     = $this->user->account_id;
                $postdata['update_data']    = $post_data;
                $postdata['site_id']        = $post_data['site_id'];

                $postdata['update_data']['site_details']['restrictions']    = (!empty($post_data['site_details']['restrictions'])) ? json_encode($post_data['site_details']['restrictions']) : '' ;

                $updates_site               = $this->webapp_service->api_dispatcher($this->api_end_point . 'site/update', $postdata, ['auth_token' => $this->auth_token]);
                $result                     = (isset($updates_site->site)) ? $updates_site->site : null;
                $message                    = (isset($updates_site->message)) ? $updates_site->message : 'Request completed!';
                if (!empty($result)) {
                    $return_data['status'] = 1;
                    $return_data['site']   = $result;
                }
                $return_data['status_msg'] = $message;
            } else {
                $return_data['status_msg'] = 'No Site ID supplied';
            }
        }

        print_r(json_encode($return_data));
        die();
    }

    public function delete_site($page = 'details')
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

            if (!empty($post_data) && !empty($post_data['site_id'])) {
                $postdata                   = [];
                $postdata['account_id']     = $this->user->account_id;
                $postdata['site_id']        = (!empty($post_data['site_id'])) ? $post_data['site_id'] : false ;

                $url            = 'site/delete';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                ## d_site = deleted_site
                if (!empty($API_result)) {
                    $return_data['d_site']  = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->d_site)) ? $API_result->d_site : null;
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
    *   Duplicate site record
    **/
    public function duplicate_site()
    {
        $section     = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
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
            $postdata        = array_merge(['account_id' => $this->user->account_id], $this->input->post());
            $duplicate_site  = $this->webapp_service->api_dispatcher($this->api_end_point . 'site/duplicate_site', $postdata, ['auth_token' => $this->auth_token]);
            $result          = (isset($duplicate_site->site)) ? $duplicate_site->site : null;
            $message         = (isset($duplicate_site->message)) ? $duplicate_site->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status']   = 1;
                $return_data['site']     = $result;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }



    /*
    *   Function to disable the site with given date
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
                $postdata['site_id']            = (!empty($post_data['site_id'])) ? $post_data['site_id'] : false ;
                $postdata['disable_date']       = (!empty($post_data['disable_site_date'])) ? $post_data['disable_site_date'] : false ;

                $url                = 'site/disable';
                $API_result         = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result)) {
                    $return_data['disabled_site']   = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->disabled_site)) ? $API_result->disabled_site : null;
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


    /**
    *   Upload Site files. This is a Web-client only function
    */
    public function upload_docs($site_id)
    {
        if (!empty($site_id)) {
            $postdata   = array_merge(['account_id' => $this->user->account_id], $this->input->post());
            $doc_upload = $this->document_service->upload_files($this->user->account_id, $postdata, $document_group = 'site', $folder = 'site');
            redirect('webapp/site/profile/' . $site_id);
        } else {
            redirect('webapp/site', 'refresh');
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
                $postdata['doc_group']      = "site";
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



    public function generate_viewing_stats($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data) && (!empty($post_data['site_id']))) {
                $postdata['account_id']     = $this->user->account_id;
                $postdata['site_id']        = (!empty($post_data['site_id'])) ? $post_data['site_id'] : false ;

                $url                        = 'site/generate_viewing_stats';
                $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                $viewing_stats               = (!empty($API_result->viewing_stats)) ? $API_result->viewing_stats : false ;

                if (isset($viewing_stats)) {
                    if (!empty($viewing_stats->file_name)) {
                        ## Evident version $docName =  strtoupper($postdata['file_type']) . ' Export - ' . $postdata['content_id'] . ' ' . date('Y-m-d H:i:s');
                        $docName        =  (!empty($viewing_stats->file_name)) ? html_escape($viewing_stats->file_name) : "Viewing_stats_" . date('Y-m-d H:i:s') . "csv";
                        $fileName       =  (!empty($viewing_stats->file_name)) ? html_escape($viewing_stats->file_name) : "Viewing_stats_" . date('Y-m-d H:i:s') . "csv";
                        $docReference   = time() . '_' . $fileName;
                        $docLink        = (!empty($viewing_stats->file_link)) ? $viewing_stats->file_link : $viewing_stats->file_link;
                        $docLocation    =  (!empty($viewing_stats->document_location)) ? $viewing_stats->document_location . $fileName : '' ;

                        $document_data = [
                            "account_id"        => $this->user->account_id,
                            "site_id"           => $postdata['site_id'],
                            "module"            => "site",
                            "doc_type"          => "Site",
                            "document_name"     => $docName,
                            "doc_reference"     => $docReference,
                            "document_link"     => $docLink,
                            'document_location' => $docLocation
                        ];
                        $target_table = "site_document_uploads";

                        $this->document_service->_create_document_placeholder($this->user->account_id, $document_data, $target_table);

                        $file_content = str_replace("&rsquo;", "'", htmlspecialchars_decode(mb_convert_encoding(file_get_contents(escapefile_url($viewing_stats->file_link)), "HTML-ENTITIES", "UTF-8")));

                        if (isset($viewing_stats->file_name) && !empty($file_content)) {
                            force_download($viewing_stats->file_name, $file_content);
                        } else {
                        }
                    } else {
                        if (!empty($API_result->message)) {
                            $this->session->set_flashdata('stats_message', $API_result->message);
                        } else {
                            $this->session->set_flashdata('stats_message', 'No viewing stats available.');
                        }
                        redirect('webapp/site/profile/' . $postdata['site_id'], 'refresh');
                    }
                } else {
                }

                redirect('webapp/site/profile/' . $postdata['site_id'], 'refresh');
            } else {
                redirect('webapp/site/', 'refresh');
            }
        }
    }

    /**
    *   Update Site Window Months list
    **/
    public function update_window($page = 'details')
    {
        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data['site_id'])) {
                $postdata       = [];
                $postdata['account_id']     = $this->user->account_id;
                $postdata['months']         = (!empty($post_data['month'])) ? json_encode($post_data['month']) : null ;
                $postdata['site_id']        = $post_data['site_id'];
                $url                        = 'site/update_window';
                $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');
                $result                     = (isset($API_result->window)) ? $API_result->window : null;
                $message                    = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                if (!empty($result)) {
                    $return_data['status'] = 1;
                    $return_data['window']   = $result;
                }
                $return_data['status_msg'] = $message;
            } else {
                $return_data['status_msg'] = 'No Site ID supplied';
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    /**
    *   Site Devices lookup
    **/
    public function devices_lookup($page = 'details')
    {
        $return_data = "";

        # Check module-item access
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data .= '<tr><td colspan="6">';
            $return_data .= $this->config->item('ajax_access_denied');
            $return_data .= '</td></tr>';
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data['site_id'])) {
                $active_links           = ['inactive_multiple_link','inactive_multiple_unlink', 'inactive_multiple_delete','inactive_create_n_link_at', 'inactive_unlink_at' ];

                # Setup search parameters
                $where                  = false;
                $where['search_term']   = (!empty($post_data['search_term'])) ? $post_data['search_term'] : false ;
                $where['site_id']       = (!empty($post_data['site_id'])) ? (int) $post_data['site_id'] : false ;
                $limit                  = (!empty($post_data['limit'])) ? (int) $post_data['limit'] : DEFAULT_LIMIT ;
                $start_index            = (!empty($post_data['start_index'])) ? (int) $post_data['start_index'] : 0 ;
                $offset                 = (!empty($post_data['start_index'])) ? (int) (($start_index - 1) * $limit) : 0 ;

                #prepare postdata
                $postdata = [
                    'account_id'        => $this->user->account_id,
                    'where'             => $where,
                    'limit'             => $limit,
                    'offset'            => $offset
                ];

                $url                    = 'device/devices_lookup';
                $API_result             = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                $devices                = (isset($API_result->devices)) ? $API_result->devices : null;

                if (!empty($devices)) {
                    // Create pagination
                    $counters       = $this->device_service->get_total_devices($this->user->account_id, $where, $limit, $offset);
                    $page_number    = ($start_index > 0) ? $start_index : 1;
                    $page_display   = '<span style="margin:15px 0px;" class="pull-left">Page <strong>' . $page_number . '</strong> of <strong>' . $counters->pages . '</strong></span>';

                    if ($counters->total > 0) {
                        $pagination_setup               = _pagination_config("devices");
                        $config['total_rows']           = $counters->total;
                        $config['per_page']             = $limit;
                        $config['current_page']         = (string) $page_number;
                        $config                         = array_merge($config, $pagination_setup);

                        $this->pagination->initialize($config);
                        $pagination             = $this->pagination->create_links();
                    }


                    $temp_data      = $this->load_devices_view($devices, $active_links);
                    $return_data    = $temp_data['lookup'];
                    $active_links   = $temp_data['active_links'];
                    if (!empty($pagination)) {
                        $return_data .= '<tr><td colspan="7" style="padding: 0;">';
                        $return_data .= $page_display . $pagination;
                        $return_data .= '</td></tr>';
                    }
                } else {
                    $return_data    .= '<tr><td colspan="7">';
                    $return_data    .= (isset($API_result->message)) ? $API_result->message : 'No records found';
                    $return_data    .= '</td></tr>';
                }
            } else {
                $return_data        .= '<tr><td colspan="7">No Site ID supplied</td></tr>';
            }
        }

        print_r(json_encode(["table_data" => $return_data, "active_links" => $active_links]));
        die();
    }


    private function load_devices_view($devices_data = false, $active_links = false)
    {
        $return_data['lookup'] = '';
        $active_links = (!empty($active_links)) ? $active_links : ['inactive_multiple_link','inactive_multiple_unlink', 'inactive_multiple_delete','inactive_create_n_link_at', 'inactive_unlink_at' ];

        if (!empty($devices_data)) {
            foreach ($devices_data as $k => $device_details) {
                ## if at least one device is unlinked - it has easel reference but the status is disconnected
                if ((!empty($device_details->external_reference_id)) && ((!empty($device_details->airtime_status)) && (strtolower($device_details->airtime_status) == 'disconnected'))) {
                    $active_links = array_replace($active_links, [0 => 'active_multiple_link']);
                }

                ## if at least one device is linked - it has easel reference and the status is connected
                if ((!empty($device_details->external_reference_id)) && ((!empty($device_details->airtime_status)) && (strtolower($device_details->airtime_status) == 'connected'))) {
                    $active_links = array_replace($active_links, [1 => 'active_multiple_unlink']);
                }

                ## if a site has at least one device attached
                if (!empty($device_details->device_unique_id) && empty($device_details->external_reference_id)) {
                    $active_links = array_replace($active_links, [2 => 'active_multiple_delete']);
                }

                ## if a site has at least one device not connected to Easel (Airtime) with the error message
                if (
                    (empty($device_details->external_reference_id) || ($device_details->external_reference_id == null)) ||
                    (!empty($device_details->external_reference_id) && (!empty($device_details->create_error))) ||
                    (!empty($device_details->external_reference_id) && (!empty($device_details->link_error) || !empty($device_details->connect_error)))
                ) {
                    $active_links = array_replace($active_links, [3 => 'active_create_n_link_at']);
                }

                ## if a site has at least one device connected to Easel (Airtime) with the disconnection error message
                if (!empty($device_details->external_reference_id) && ((!empty($device_details->airtime_status)) && (strtolower($device_details->airtime_status) == 'connected')) && (!empty($device_details->disconnect_error) || !empty($device_details->unlink_error))) {
                    $active_links = array_replace($active_links, [4 => 'active_unlink_at']);
                }

                $return_data['lookup'] .= '<tr data-device_id="' . ((!empty($device_details->device_id)) ? ($device_details->device_id) : '') . '">';
                $return_data['lookup'] .= '<td>' . ((!empty($device_details->device_unique_id)) ? ($device_details->device_unique_id) : '') . '</td>';
                $return_data['lookup'] .= '<td>' . ((!empty($device_details->product_name)) ? $device_details->product_name : '') . '</td>';
                $return_data['lookup'] .= '<td>' . ((!empty($device_details->platform_description)) ? $device_details->platform_description : ((!empty($device_details->platform_name)) ? $device_details->platform_name : '')) . '</td>';
                $return_data['lookup'] .= '<td>' . ((!empty($device_details->external_reference_id)) ? $device_details->external_reference_id : '') . '</td>';
                $return_data['lookup'] .= '<td>' . ((!empty($device_details->airtime_status)) ? $device_details->airtime_status : '') . '</td>';
                ## sync_errors line
                $return_data['lookup'] .= '<td>';

                if (!empty($device_details->create_error)) {
                    $return_data['lookup'] .= '<img class="sync-error-icon" src="' . base_url("assets/images/icons/xs-add-AT-error.png") . '" alt="' . $device_details->create_error . '" title="' . $device_details->create_error . '" />';
                } else {
                    if (!empty($device_details->connect_error)) {
                        $return_data['lookup'] .= '<img class="sync-error-icon" src="' . base_url("assets/images/icons/xs-link-AT-error.png") . '" alt="' . $device_details->connect_error . '" title="' . $device_details->connect_error . '" />';
                    } else {
                        if (!empty($device_details->disconnect_error)) {
                            $return_data['lookup'] .= '<img class="sync-error-icon" src="' . base_url("assets/images/icons/xs-unlink-AT-error.png") . '" alt="' . $device_details->disconnect_error . '" title="' . $device_details->disconnect_error . '" />';
                        } else {
                            if (!empty($device_details->link_error)) {
                                $return_data['lookup'] .= '<img class="sync-error-icon" src="' . base_url("assets/images/icons/xs-link-AT-error.png") . '" alt="' . $device_details->link_error . '" title="' . $device_details->link_error . '" />';
                            } else {
                                if (!empty($device_details->unlink_error)) {
                                    $return_data['lookup'] .= '<img class="sync-error-icon" src="' . base_url("assets/images/icons/xs-unlink-AT-error.png") . '" alt="' . $device_details->unlink_error . '" title="' . $device_details->unlink_error . '" />';
                                } else {
                                    $return_data['lookup'] .= "";
                                }
                            }
                        }
                    }
                }

                $return_data['lookup'] .= '</td>';
                $return_data['lookup'] .= '<td><a href="#/" class="reallocate_device_link" data-device_id="' . ($device_details->device_id) . '" data-device_unique_id="' . ((!empty($device_details->device_unique_id)) ? $device_details->device_unique_id : '') . '"><img class="device_reallocate_img" style="max-width:40px;" src="' . (base_url("assets/images/icons/Device-Reallocate.png")) . '" alt="Device Reallocate" title="Device Reallocate" /></a>';

                $return_data['lookup'] .= '</tr>';
            }
        } else {
            $return_data['lookup'] .= '<tr><td colspan="6"><br/>' . $this->config->item("no_records") . '</td></tr>';
        }

        $return_data['active_links'] = $active_links;

        return $return_data;
    }


    public function upload_devices($site_id = false, $page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        ## Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $account_id     = $this->user->account_id;
            $post_data      = $this->input->post();

            if (!empty($post_data['uploaded'])) {
                $process_file   = $this->device_service->upload_devices($account_id);

                if ($process_file) {
                    redirect('/webapp/site/review_devices/' . $site_id);
                }
            }

            $data['site_id'] = (!empty($site_id)) ? $site_id : false ;
            $this->_render_webpage('site/site_upload_devices', $data);
        }
    }


    /**
    *   Review uploaded devices - 'devices_tmp_upload'
    **/
    public function review_devices($site_id = false)
    {
        $account_id = $this->account_id;

        if (!empty($account_id)) {
            $pending                = $this->device_service->get_pending_upload_records($account_id);
            $data['pending']        = (!empty($pending)) ? $pending : null;
            $data['site_id']        = (!empty($site_id)) ? (int) $site_id : false ;

            $this->_render_webpage('site/devices_pending_creation', $data);
        }
    }



    public function create_devices($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data)) {
                ## this will be batch upload
                $postdata['account_id']         = $this->user->account_id;
                $postdata['batch_devices']  = (!empty($post_data['batch_devices'])) ? $post_data['batch_devices'] : false ;

                $url            = 'device/add_batch_devices';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result->status) && ($API_result->status == true)) {
                    $return_data['all_done']        = 1;
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

    public function drop_temp_devices($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_delete) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data)) {
                ## this will be batch delete
                $postdata['account_id']         = $this->user->account_id;
                $postdata['batch_devices']      = (!empty($post_data['batch_devices'])) ? $post_data['batch_devices'] : false ;

                $url                = 'device/remove_clearance_from_tmp';
                $API_result         = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result->status) && ($API_result->status == true)) {
                    $return_data['all_done']        = 1;
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
    *   Delete devices
    **/
    public function delete_devices($site_id = false, $page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        ## Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_delete) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            if (!empty($site_id)) {
                $data               = [];
                $data['site_id']    = $site_id;

                $this->_render_webpage('site/includes/delete_devices', $data);
            } else {
                redirect("webapp/site/sites");
            }
        }
    }



    /**
    *   Delete Devices lookup
    **/
    public function delete_devices_lookup($page = 'details')
    {
        $return_data = "";

        # Check module-item access
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data .= '<tr><td colspan="6">';
            $return_data .= $this->config->item('ajax_access_denied');
            $return_data .= '</td></tr>';
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data['site_id'])) {
                # Setup search parameters
                $where                  = false;
                $where['search_term']   = (!empty($post_data['search_term'])) ? $post_data['search_term'] : false ;
                $where['site_id']       = (!empty($post_data['site_id'])) ? (int) $post_data['site_id'] : false ;
                $where['external_reference_id']     =  "" ;
                $where['status']        =  '!connected';
                $limit                  = (!empty($post_data['limit'])) ? (int) $post_data['limit'] : DEFAULT_LIMIT ;
                $start_index            = (!empty($post_data['start_index'])) ? (int) $post_data['start_index'] : 0 ;
                $offset                 = (!empty($post_data['start_index'])) ? (int) (($start_index - 1) * $limit) : 0 ;

                #prepare postdata
                $postdata = [
                    'account_id'        => $this->user->account_id,
                    'where'             => $where,
                    'limit'             => $limit,
                    'offset'            => $offset
                ];

                $url                    = 'device/devices_lookup';
                $API_result             = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                $devices                = (isset($API_result->devices)) ? $API_result->devices : null;

                if (!empty($devices)) {
                    // Create pagination
                    $counters       = $this->device_service->get_total_devices($this->user->account_id, $where, $limit, $offset);
                    $page_number    = ($start_index > 0) ? $start_index : 1;
                    $page_display   = '<span style="margin:15px 0px;" class="pull-left">Page <strong>' . $page_number . '</strong> of <strong>' . $counters->pages . '</strong></span>';

                    if ($counters->total > 0) {
                        $pagination_setup       = _pagination_config("devices");
                        $config['total_rows']   = $counters->total;
                        $config['per_page']     = $limit;
                        $config['current_page'] = $page_number;
                        $config                 = array_merge($config, $pagination_setup);
                        $this->pagination->initialize($config);
                        $pagination             = $this->pagination->create_links();
                    }

                    $return_data        = $this->load_delete_devices_view($devices);

                    if (!empty($pagination)) {
                        $return_data .= '<tr><td colspan="6" style="padding: 0;">';
                        $return_data .= $page_display . $pagination;
                        $return_data .= '</td></tr>';
                    }
                } else {
                    $return_data    .= '<tr><td colspan="6">';
                    $return_data    .= (isset($API_result->message)) ? $API_result->message : 'No records found';
                    $return_data    .= '</td></tr>';
                }
            } else {
                $return_data        .= '<tr><td colspan="6">No Site ID supplied</td></tr>';
            }
        }

        print_r($return_data);
        die();
    }


    private function load_delete_devices_view($devices_data = false)
    {
        if (!empty($devices_data)) {
            $return_data = '';

            foreach ($devices_data as $k => $device_details) {
                $return_data .= '<tr>';
                $return_data .= '<td>' . ((!empty($device_details->device_unique_id)) ? ($device_details->device_unique_id) : '') . '</a></td>';
                $return_data .= '<td>' . ((!empty($device_details->product_name)) ? $device_details->product_name : '') . '</td>';
                $return_data .= '<td>' . ((!empty($device_details->platform_description)) ? $device_details->platform_description : ((!empty($device_details->platform_name)) ? $device_details->platform_name : '')) . '</td>';

                $return_data .= '<td class="check-box border_bottom"><div class="checkbox pull-right"><input type="hidden" name="devices_to_delete[' . $device_details->device_id . '][checked]" value="0" /><label><input type="checkbox" name="devices_to_delete[' . $device_details->device_id . '][checked]" value="1" class="chkdelete_devices" ></label></div></td>';
                $return_data .= '</tr>';
            }
        } else {
            $return_data .= '<tr><td colspan="6"><br/>' . $this->config->item("no_records") . '</td></tr>';
        }

        return $return_data;
    }



    public function devices_delete($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_delete) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data)) {
                $devices_ids = [];

                if (!empty($post_data['devices_to_delete'])) {
                    foreach ($post_data['devices_to_delete'] as $key => $row) {
                        if (!empty($row['checked']) && ($row['checked'] == 1)) {
                            $devices_ids[] = $key;
                        }
                    }

                    if (!empty($devices_ids)) {
                        $postdata['account_id']     = $this->user->account_id;
                        $postdata['devices_ids']    = $devices_ids;
                        $url                        = 'device/delete';
                        $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                        if (!empty($API_result->status) && ($API_result->status == true)) {
                            $return_data['status']          = 1;
                            $return_data['status_msg']      = (isset($API_result->message) && !empty($API_result->message)) ? $API_result->message : "Devices removed from the system" ;
                            $return_data['stats']           = (isset($API_result->stats) && !empty($API_result->stats)) ? $API_result->stats : false;
                        } else {
                            $return_data['status_msg']      = (!empty($API_result->message)) ? $API_result->message : 'There was an error processing your request';
                        }
                    } else {
                        $return_data['status_msg']      = 'No devices were selected';
                    }
                } else {
                    $return_data['status_msg']      = 'No devices data were provided';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }



    /**
    *   Add and connect devices on Airtime
    **/
    public function airtime_connect($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data['site_id'])) {
                $postdata['account_id']     = $this->user->account_id;
                $postdata['site_id']        = (int) $post_data['site_id'];
                $url                        = 'device/airtime_connect';
                $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result->status) && ($API_result->status == true)) {
                    $return_data['status']          = 1;
                    $return_data['status_msg']      = (isset($API_result->message) && !empty($API_result->message)) ? $API_result->message : "Devices connected to Airtime" ;
                    $return_data['stats']           = (isset($API_result->stats) && !empty($API_result->stats)) ? $API_result->stats : false;
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
    *   Disconnect / unlink device(s). Site ID required.
    **/
    public function unlink_device($site_id = false)
    {
        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'details');

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            if (!empty($site_id)) {
                $data = false;

                $data['site_id'] = (int) html_escape($site_id);
                $this->_render_webpage('site/includes/unlink_device', $data);
            } else {
                redirect('webapp/site/sites', 'refresh');
            }
        }
    }




    /**
    *   Site Devices lookup when we need to perform an action: unlink...
    **/
    public function action_devices_lookup($page = 'details')
    {
        $return_data = "";

        # Check module-item access
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data .= '<tr><td colspan="6">';
            $return_data .= $this->config->item('ajax_access_denied');
            $return_data .= '</td></tr>';
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data['site_id'])) {
                // $active_links            = ['inactive_multiple_link','inactive_multiple_unlink', 'inactive_multiple_delete','inactive_create_n_link_at', 'inactive_unlink_at' ];

                # Setup search parameters
                $where                  = false;
                $where['search_term']   = (!empty($post_data['search_term'])) ? $post_data['search_term'] : false ;
                $where['site_id']       = (!empty($post_data['site_id'])) ? (int) $post_data['site_id'] : false ;
                $where['action']        = (!empty($post_data['action'])) ? $post_data['action'] : false ;
                $limit                  = (!empty($post_data['limit'])) ? (int) $post_data['limit'] : DEFAULT_LIMIT ;
                $start_index            = (!empty($post_data['start_index'])) ? (int) $post_data['start_index'] : 0 ;
                $offset                 = (!empty($post_data['start_index'])) ? (int) (($start_index - 1) * $limit) : 0 ;

                #prepare postdata
                $postdata = [
                    'account_id'        => $this->user->account_id,
                    'where'             => $where,
                    'limit'             => $limit,
                    'offset'            => $offset
                ];

                $url                    = 'device/devices_lookup';
                $API_result             = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                $devices                = (isset($API_result->devices)) ? $API_result->devices : null;

                if (!empty($devices)) {
                    // Create pagination
                    $counters       = $this->device_service->get_total_devices($this->user->account_id, $where, $limit, $offset);
                    $page_number    = ($start_index > 0) ? $start_index : 1;
                    $page_display   = '<span style="margin:15px 0px;" class="pull-left">Page <strong>' . $page_number . '</strong> of <strong>' . $counters->pages . '</strong></span>';

                    if ($counters->total > 0) {
                        $pagination_setup       = _pagination_config("devices");
                        $config['total_rows']   = $counters->total;
                        $config['per_page']     = $limit;
                        $config['current_page'] = $page_number;
                        $config                 = array_merge($config, $pagination_setup);
                        $this->pagination->initialize($config);
                        $pagination             = $this->pagination->create_links();
                    }

                    $temp_data      = $this->load_action_devices_view($devices, $where['action']);
                    $return_data    = $temp_data['lookup'];

                    if (!empty($pagination)) {
                        $return_data .= '<tr><td colspan="6" style="padding: 0;">';
                        $return_data .= $page_display . $pagination;
                        $return_data .= '</td></tr>';
                    }
                } else {
                    $return_data    .= '<tr><td colspan="6">';
                    $return_data    .= (isset($API_result->message)) ? $API_result->message : 'No records found';
                    $return_data    .= '</td></tr>';
                }
            } else {
                $return_data        .= '<tr><td colspan="6">No Site ID supplied</td></tr>';
            }
        }

        print_r(json_encode(["table_data" => $return_data]));
        die();
    }


    private function load_action_devices_view($devices_data = false, $action = false)
    {
        $return_data['lookup'] = '';

        if (!empty($devices_data)) {
            foreach ($devices_data as $k => $device_details) {
                $return_data['lookup'] .= '<tr data-device_id="' . ((!empty($device_details->device_id)) ? ($device_details->device_id) : '') . '">';
                $return_data['lookup'] .= '<td>' . ((!empty($device_details->device_unique_id)) ? ($device_details->device_unique_id) : '') . '</td>';
                $return_data['lookup'] .= '<td>' . ((!empty($device_details->product_name)) ? $device_details->product_name : '') . '</td>';
                $return_data['lookup'] .= '<td>' . ((!empty($device_details->platform_description)) ? ucwords($device_details->platform_description) : ((!empty($device_details->platform_name)) ? ucwords($device_details->platform_name) : '')) . '</td>';
                $return_data['lookup'] .= '<td>' . ((!empty($device_details->external_reference_id)) ? $device_details->external_reference_id : '') . '</td>';
                if (!empty($action) && in_array($action, ['unlinked_to_link'])) {
                    // $return_data['lookup'] .= '<td>'.( ( !empty( $device_details->airtime_status ) ) ? $device_details->airtime_status : '' ).'</td>';
                } else {
                    $return_data['lookup'] .= '<td>' . ((!empty($device_details->easel_segment_id)) ? $device_details->easel_segment_id : '') . '</td>';
                }
                $return_data['lookup'] .= '<td>';
                $return_data['lookup'] .= '<input type="checkbox"';

                if (!empty($action) && in_array($action, ['unlinked_to_link'])) {
                } else {
                    $return_data['lookup'] .= ' data-easel_segment_id="' . ((!empty($device_details->easel_segment_id)) ? ($device_details->easel_segment_id) : '') . '" ';
                }

                $return_data['lookup'] .= ' data-external_reference_id="' . ((!empty($device_details->external_reference_id)) ? ($device_details->external_reference_id) : '') . '"  data-product_id="' . ((!empty($device_details->product_id)) ? ($device_details->product_id) : '') . '" name="device_id" value="' . ((!empty($device_details->device_id)) ? ($device_details->device_id) : '') . '" />';
                $return_data['lookup'] .= '</td>';
                $return_data['lookup'] .= '</tr>';
            }
        } else {
            $return_data['lookup'] .= '<tr><td colspan="6"><br/>' . $this->config->item("no_records") . '</td></tr>';
        }

        return $return_data;
    }


    public function unlink_devices($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data      = $this->input->post();
            $devices_data   = json_decode($post_data['devices_data'], true);

            if (!empty($devices_data)) {
                $postdata['account_id']     = $this->user->account_id;
                $postdata['devices_data']   = $devices_data;
                $url                        = 'device/airtime_unlink';
                $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result->status) && ($API_result->status == true)) {
                    $return_data['status']          = 1;
                    $return_data['status_msg']      = (isset($API_result->message) && !empty($API_result->message)) ? $API_result->message : "Devices unlinked from Segments" ;
                } else {
                    $return_data['status_msg']      = (!empty($API_result->message)) ? $API_result->message : 'There was an error processing your request';
                }
            } else {
                $return_data['status_msg'] = "Error sending required data;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    /**
    *   Reconnect / link back device(s). Site ID required.
    **/
    public function link_device($site_id = false)
    {
        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'details');

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            if (!empty($site_id)) {
                $data = false;

                $data['site_id'] = (int) html_escape($site_id);
                $this->_render_webpage('site/includes/link_device', $data);
            } else {
                redirect('webapp/site/sites', 'refresh');
            }
        }
    }



    public function link_devices($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data      = $this->input->post();
            $devices_data   = json_decode($post_data['devices_data'], true);

            if (!empty($devices_data)) {
                $postdata                           = [];
                $postdata['account_id']             = $this->user->account_id;
                $postdata['devices_data']           = $devices_data;
                $url                                = 'device/airtime_reconnect';
                $API_result                         = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result->status) && ($API_result->status == true)) {
                    $return_data['status']          = 1;
                    $return_data['status_msg']      = (isset($API_result->message) && !empty($API_result->message)) ? $API_result->message : "Devices linked to the Segment(s)" ;
                } else {
                    $return_data['status_msg']      = (!empty($API_result->message)) ? $API_result->message : 'There was an error processing your request';
                }
            } else {
                $return_data['status_msg'] = "Error sending required data;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }



    /**
    *   Disconnect devices on Airtime and clear unlinking/disconnecting errors
    **/
    public function airtime_disconnect($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data['site_id'])) {
                $postdata['account_id']     = $this->user->account_id;
                $postdata['site_id']        = (int) $post_data['site_id'];
                $url                        = 'device/airtime_disconnect';
                $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result->status) && ($API_result->status == true)) {
                    $return_data['status']          = 1;
                    $return_data['status_msg']      = (isset($API_result->message) && !empty($API_result->message)) ? $API_result->message : "Devices disconnected from Airtime" ;
                    $return_data['stats']           = (isset($API_result->stats) && !empty($API_result->stats)) ? $API_result->stats : false;
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
    *   The function to obtain system types based on the provided Integrator ID
    */
    public function get_systems_by_integrator($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data['integrator_id'])) {
                $postdata['account_id']     = $this->user->account_id;
                $postdata['where']['integrator_id'] = (int) $post_data['integrator_id'];
                $url                        = 'systems/systems';
                $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');

                if (!empty($API_result->status) && ($API_result->status == true)) {
                    $return_data['status']          = 1;
                    $return_data['status_msg']      = (isset($API_result->message) && !empty($API_result->message)) ? $API_result->message : "Devices disconnected from Airtime" ;
                    $return_data['systems']         = (isset($API_result->systems) && !empty($API_result->systems)) ? $this->load_systems_select($API_result->systems) : false;
                } else {
                    $return_data['status_msg']      = (!empty($API_result->message)) ? $API_result->message : 'There was an error processing your request';
                }
            } else {
                $return_data['status_msg']          = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    /*
    *   Prepare system integrators select
    */
    private function load_systems_select($systems_data)
    {
        $return_data = '';

        if (!empty($systems_data)) {
            foreach ($systems_data as $k => $system) {
                $return_data .= '<option value="' . ((!empty($system->system_type_id)) ? $system->system_type_id : '') . '" title="' . ((!empty($system->name)) ? $system->name : '') . '">' . ((!empty($system->name)) ? $system->name : '') . '</option>';
            }
        } else {
        }
        return $return_data;
    }


    /*
    *   This is to add market to 'invited'
    */
    public function activate_market($page = "details")
    {
        $return_data = false;

        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status'        => 0,
            'status_msg'    => '',
            'data'          => false
        ];

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data['product_id'])) {
                $postdata['account_id']     = $this->user->account_id;
                $postdata['product_id']     = (int) $post_data['product_id'];
                $url                        = 'product/activate_market';
                $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result->status) && ($API_result->status == true)) {
                    $return_data['status']          = 1;
                    $return_data['status_msg']      = (isset($API_result->message) && !empty($API_result->message)) ? $API_result->message : "Market Activated" ;
                } else {
                    $return_data['status_msg']      = (!empty($API_result->message)) ? $API_result->message : 'There was an error processing your request';
                }
            } else {
                $return_data['status_msg']          = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    /*
    *   This is to remove market from 'invited'
    */
    public function deactivate_market($page = "details")
    {
        $return_data = false;

        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data['product_id'])) {
                $postdata['account_id']     = (int) $this->user->account_id;
                $postdata['product_id']     = (int) $post_data['product_id'];
                $url                        = 'product/deactivate_market';
                $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result->status) && ($API_result->status == true)) {
                    $return_data['status']          = 1;
                    $return_data['status_msg']      = (isset($API_result->message) && !empty($API_result->message)) ? $API_result->message : "Market Deactivated" ;
                } else {
                    $return_data['status_msg']      = (!empty($API_result->message)) ? $API_result->message : 'There was an error processing your request';
                }
            } else {
                $return_data['status_msg']          = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }





    /*
    *   Site lookup for re-allocation
    */
    public function realocate_to_site($page = 'details')
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
            $block_statuses = ($this->input->post('block_statuses')) ? $this->input->post('block_statuses') : false;
            $limit         = ($this->input->post('limit')) ? $this->input->post('limit') : 9999;
            $start_index   = ($this->input->post('start_index')) ? $this->input->post('start_index') : 0;
            $offset        = (!empty($start_index)) ? (($start_index - 1) * $limit) : 0;
            $order_by      = false;
            $where         = false;
            $where['airtime_product_only'] = "yes";

            #prepare postdata
            $postdata = [
                'account_id'        => $this->user->account_id,
                'search_term'       => $search_term,
                'block_statuses'    => $block_statuses,
                'where'             => $where,
                'order_by'          => $order_by,
                'limit'             => $limit,
                'offset'            => $offset
            ];

            $search_result  = $this->webapp_service->api_dispatcher($this->api_end_point . 'site/lookup', $postdata, ['auth_token' => $this->auth_token], true);
            $sites          = (isset($search_result->sites)) ? $search_result->sites : null;
            if (!empty($sites)) {
                ## Create pagination
                $counters       = $this->site_service->get_total_sites($this->user->account_id, $search_term, $block_statuses, $where, $order_by, $limit, $offset);//Direct access to count, this should only return a number
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

                $return_data = $this->load_sites_dropdown($sites);
                if (!empty($pagination)) {
                    $return_data .= '<tr><td colspan="6" style="padding: 0;">';
                    $return_data .= $page_display . $pagination;
                    $return_data .= '</td></tr>';
                }
            } else {
                $return_data .= '<tr><td colspan="6">';
                $return_data .= (isset($search_result->message)) ? $search_result->message : 'No records found';
                $return_data .= '</td></tr>';
            }
        }

        print_r($return_data);
        die();
    }

    /*
    *   Prepare sites views
    */
    private function load_sites_dropdown($sites_data)
    {
        $return_data = '';

        if (!empty($sites_data)) {
            foreach ($sites_data as $k => $site_details) {
                $return_data .= '<option value="">Select the site</option>';
                $return_data .= '<option value="' . $site_details->site_id . '">' . ((!empty($site_details->site_name)) ? $site_details->site_name : '') . '</option>';
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
    *   This is to get all products associated with the specific site ID
    */
    public function get_product_by_site_id($page = "details")
    {
        $return_data = false;

        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data['site_id'])) {
                $postdata['account_id']                 = (int) $this->user->account_id;
                $postdata['where']['site_id']           = (int) $post_data['site_id'];
                $postdata['where']['product_type_id']   = 71;
                $url                                    = 'product/product';
                $API_result                             = $this->ssid_common->api_call($url, $postdata, $method = 'GET');

                if (!empty($API_result->status) && ($API_result->status == true) && (!empty($API_result->product))) {
                    $return_data['dataset']         = $this->load_product_dropdown($API_result->product);
                    $return_data['status']          = 1;
                    $return_data['status_msg']      = (isset($API_result->message) && !empty($API_result->message)) ? $API_result->message : "Product(s) Found" ;
                } else {
                    $return_data['status_msg']      = (!empty($API_result->message)) ? $API_result->message : 'There was an error processing your request';
                }
            } else {
                $return_data['status_msg']          = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }

    private function load_product_dropdown($product_data = false)
    {
        $result_string = '<option value="">Please select</option>';
        if (!empty($product_data)) {
            foreach ($product_data as $pd_key => $pd_row) {
                $result_string .= '<option value="' . $pd_row->product_id . '">' . $pd_row->product_name . '</option>';
            }
        }

        return $result_string;
    }


    /*
    *   This is to assign the single device to different site and product including respective calls to Easel
    *   ## bulk devices reallocation??? - To be built upon request
    */
    public function reallocate_device($page = "details")
    {
        $return_data = false;

        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data['device_id']) && !empty($post_data['product_id'])) {
                $postdata['account_id']     = (int) $this->user->account_id;
                $postdata['device_id']      = (int) $post_data['device_id'];
                $postdata['product_id']     = (int) $post_data['product_id'];
                $url                        = 'device/reallocate';
                $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result->status) && ($API_result->status == true) && (!empty($API_result->device))) {
                    $return_data['dataset']         = $API_result->device;
                    $return_data['status']          = 1;
                    $return_data['status_msg']      = (isset($API_result->message) && !empty($API_result->message)) ? $API_result->message : "Device Reallocated" ;
                } else {
                    $return_data['status_msg']      = (!empty($API_result->message)) ? $API_result->message : 'There was an error processing your request';
                }
            } else {
                $return_data['status_msg']          = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    /*
    *   Site lookup by product site ID
    */
    public function site_by_product_type($page = 'details')
    {
        $return_data = '';

        # Check module access
        $section = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data .= $this->config->item('ajax_access_denied');
        } else {
            $where          = false;
            $post_data      = $this->input->post();

            $postdata       = [];
            $postdata       = [
                'account_id'            => $this->user->account_id,
                'product_type_id'       => 71,
                'where'                 => $where
            ];

            $url                        = 'site/site_by_product_type';
            $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $sites                      = (isset($API_result->site)) ? $API_result->site : null;

            if (!empty($sites)) {
                $return_data = $this->load_site_by_product_dropdown($sites);
            }
        }

        print_r($return_data);
        die();
    }


    /*
    *   Prepare sites views
    */
    private function load_site_by_product_dropdown($sites = false)
    {
        $return_data = '';
        $return_data .= '<option value="">Select the site</option>';

        if (!empty($sites)) {
            foreach ($sites as $k => $site_details) {
                $return_data .= '<option value="' . $site_details->site_id . '">' . ((!empty($site_details->site_name)) ? $site_details->site_name : '') . '</option>';
            }
        } else {
            $return_data .= '<tr><td colspan="4"><br/>' . $this->config->item("no_records") . '</td></tr>';
        }
        return $return_data;
    }
}
