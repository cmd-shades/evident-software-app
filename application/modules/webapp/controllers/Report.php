<?php

namespace Application\Modules\Web\Controllers;

defined('BASEPATH') || exit('No direct script access allowed');

use Application\Extentions\MX_Controller;

class Report extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }
        $this->module_id        = $this->webapp_service->_get_module_id($this->router->fetch_class());
        $this->options          = ['auth_token' => $this->auth_token];
        $this->months           = ['January', 'February','March','April','May','June','July','August','September','October','November', 'December'];
    }


    public function index()
    {
        $this->reports();
    }

    public function reports()
    {
        if (!empty($this->session->flashdata("feedback"))) {
            $data['feedback']           = $this->session->flashdata("feedback");
        }

        $data['months']                 = $this->months;

        $data['report_categories']      = $postdata = [];
        $postdata['account_id']         = $this->user->account_id;
        $url                            = 'report/report_category';
        $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
        $data['report_categories']      = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->category)) ? $API_result->category : null;

        $data['content_providers']      = $postdata = [];
        $postdata['account_id']         = $this->user->account_id;
        $url                            = 'provider/provider';
        $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
        $data['content_providers']      = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->content_provider)) ? $API_result->content_provider : null;

        $data['report_settings']        = $postdata = [];
        $postdata['account_id']         = $this->user->account_id;
        $url                            = 'report/setting';
        ## $API_result                      = $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
        $data['report_settings']        = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->setting)) ? $API_result->setting : null;

        if ($this->input->post()) {
            $postdata    = array_merge(['account_id' => $this->user->account_id], $this->input->post());
            $report_data = $this->webapp_service->api_dispatcher($this->api_end_point . 'report/reports', $postdata, $this->options);
            $data['report_data'] = (isset($report_data->report)) ? $report_data->report : null;

            if (!empty($data['report_data']->file_link)) {
                force_download($data['report_data']->file_name, file_get_contents($data['report_data']->file_path));
            }
        }

        $setup_data                 = $this->webapp_service->api_dispatcher($this->api_end_point . 'report/report_types_setup', ['account_id' => $this->user->account_id], $this->options, true);

        $data['setup_data']         = (!empty($setup_data)) ? $setup_data->report_setup : null;
        $data['user']               = $this->user;
        $this->_render_webpage('report/index', $data);
    }


    public function get_report_types($page = "details")
    {
        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data) && !empty($post_data['category_id'])) {
                $postdata                   = [];
                $postdata['account_id']     = $this->user->account_id;
                $postdata['where']['category_id']   = (!empty($post_data['category_id'])) ? $post_data['category_id'] : false ;
                $url                        = 'report/report_type';
                $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');

                if (!empty($API_result)) {
                    $return_data['report_types']    = (isset($API_result->status) && ($API_result->status == true)) ? $this->report_types_select($API_result->type) : null;
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


    private function report_types_select($types = false)
    {
        $result = false;
        if (!empty($types)) {
            $result .= '<option value="">Please select</option>';

            foreach ($types as $type) {
                $result .= '<option value="' . $type->type_id . '"' . (($type->is_visible == true) ? '' : 'disabled="disabled" style="background: lightgrey;"') . '>' . $type->type_name . '</option>';
            }
        }
        return $result;
    }

    /*
    *   A list of sites with product from specified provider
    */
    public function expected_files($page = "details")
    {
        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $section = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data) && !empty($post_data['provider_id'])) {
                $postdata                           = [];
                $postdata['account_id']             = $this->user->account_id;
                $postdata['where']['provider_id']   = (!empty($post_data['provider_id'])) ? $post_data['provider_id'] : false ;
                $postdata['where']['month_id']      = (!empty($post_data['month_id'])) ? $post_data['month_id'] : false ;
                $url                                = 'report/expected_files';
                $API_result                         = $this->ssid_common->api_call($url, $postdata, $method = 'GET');

                if (!empty($API_result)) {
                    $return_data['files']           = (isset($API_result->status) && ($API_result->status == true)) ? $this->expected_files_list($API_result->files) : null;
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


    private function expected_files_list($files = false)
    {
        $result = false;
        if (!empty($files)) {
            foreach ($files as $file) {
                $result[]   = $file->filename . '.csv';
            }
        }

        return $result;
    }


    /**
    *   Upload Viewing Stats files. This is a Web-client only function
    */
    public function upload_docs()
    {
        $postdata       = [];
        $postdata       = $this->input->post();
        $provider_id    = (!empty($postdata['provider_id'])) ? (int) $postdata['provider_id'] : false ;
        $category_group = (!empty($postdata['category_group'])) ? $postdata['category_group'] : false ;

        if (!empty($category_group) && strtolower($category_group) == "royalty_report") {
            if (!empty($provider_id)) { ## why provider? - To know under which section it goes. We may use in the future also report_category_id, report_type_id.
                $postdata   = array_merge(['account_id' => $this->user->account_id], $postdata);

                ## royalty_report

                $api_call   = $this->document_service->upload_files($this->user->account_id, $postdata, $document_group = 'report_viewing_stats', $folder = 'report_viewing_stats');

                if (!empty($api_call['documents'])) {
                    $postdata_new                   = [];

                    // Process uploaded documents
                    $postdata_new['account_id']     = $this->user->account_id;
                    $postdata_new['provider_id']    = (!empty($postdata['provider_id'])) ? $postdata['provider_id'] : '' ;
                    $postdata_new['month_id']       = (!empty($postdata['month'])) ? $postdata['month'] : '' ;
                    $postdata_new['year']           = (!empty($postdata['year'])) ? $postdata['year'] : '' ;
                    $postdata_new['viewing_stats']  = $api_call['documents'];
                    $url                            = 'report/process_viewing_stats';
                    $API_result                     = $this->ssid_common->api_call($url, $postdata_new, $method = 'POST');

                    $feedback                       = [];
                    $feedback['message']            = (isset($API_result->message) && (!empty($API_result->message))) ? $API_result->message : false;
                    $feedback['status']             = isset($API_result->status) ? $API_result->status : false;

                    if (!empty($feedback)) {
                        $this->session->set_flashdata('feedback', $feedback);
                    }

                    redirect('webapp/report/reports', 'refresh');
                } else {
                    // file upload failed
                }
            } else {
                // no required data provided
            }
        } elseif (!empty($category_group) && (strtolower($category_group) == "basic_report")) {
            $dataset                    = [];
            $dataset['account_id']      = $this->user->account_id;
            $dataset['category_group']  = (!empty($postdata['category_group'])) ? $postdata['category_group'] : '' ;
            $dataset['type_id']         = (!empty($postdata['report_type_id'])) ? $postdata['report_type_id'] : '' ;
            $url                        = 'report/simple_report';
            $API_result                 = $this->ssid_common->api_call($url, $dataset, $method = 'GET');
            // debug( $API_result, "print", false );
            if (!empty($API_result->report->file_link)) {
                force_download($API_result->report->file_path, null, true);
            }
        } else {
            $this->session->set_flashdata('feedback', [ "status" => false, "message" => "No Report Category provided"]);
            redirect('webapp/report/reports', 'refresh');
        }
    }


    /*
    *   Just get the values for the settings - associated with the Royalty Reports for UIP provider.
    */
    public function settings_value($page = "details")
    {
        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $section = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data) && !empty($post_data['royalty_type_id']) && !empty($post_data['royalty_service_id']) && !empty($post_data['provider_id'])) {
                $postdata                                   = [];
                $postdata['account_id']                     = $this->user->account_id;
                $postdata['where']['provider_id']           = (!empty($post_data['provider_id'])) ? $post_data['provider_id'] : false ;
                $postdata['where']['royalty_type_id']       = (!empty($post_data['royalty_type_id'])) ? $post_data['royalty_type_id'] : false ;
                $postdata['where']['royalty_service_id']    = (!empty($post_data['royalty_service_id'])) ? $post_data['royalty_service_id'] : false ;
                $url                                        = 'report/settings_value';
                $API_result                                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');

                if (!empty($API_result)) {
                    $return_data['settings_values'] = (isset($API_result->status) && ($API_result->status == true)) ? $API_result->settings_value : null;
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
    *   Update Site Settings values (Royalty path)
    **/
    public function update_site_royalty_setting($page = 'details')
    {
        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data)) {
                $postdata                   = [];
                $postdata['account_id']     = $this->user->account_id;
                $postdata['post_data']      = $post_data;
                $url                        = 'report/update_site_royalty_setting';
                $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');
                $result                     = (isset($API_result->site_setting)) ? $API_result->site_setting : null;
                $message                    = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                if (!empty($result)) {
                    $return_data['status']          = 1;
                    $return_data['site_setting']    = $result;
                }
                $return_data['status_msg'] = $message;
            } else {
                $return_data['status_msg'] = 'No Required Data supplied';
            }
        }

        print_r(json_encode($return_data));
        die();
    }



    /**
    *   Get Reports AJAX call. At initial stage it will be filtered by the Category ID
    **/
    public function get_reports($page = 'details')
    {
        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $section = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data) && !empty($post_data['category_id'])) {
                $postdata                           = [];
                $postdata['account_id']             = $this->user->account_id;
                $postdata['where']['category_id']   = (!empty($post_data['category_id'])) ? $post_data['category_id'] : false ;
                $url                                = 'report/report';
                $API_result                         = $this->ssid_common->api_call($url, $postdata, $method = 'GET');

                if (isset($API_result->status) && ($API_result->status == true)) {
                    $return_data['reports']         = (!empty($API_result->report)) ? $this->report_view($API_result->report) : null;
                    $return_data['status']          = $API_result->status;
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


    private function report_view($reports = false)
    {
        $result = "";
        if (!empty($reports)) {
            foreach ($reports as $export_group_key => $export_group) {
                $result .= '<div class="x_panel tile group-container no-background">';
                $result .= '<h4 class="legend inner-legend"><i class="fas fa-caret-up"></i>' . $export_group_key . '</h4>';
                $result .= '<div class="row group-content" style="display: none;">';

                foreach ($export_group as $i => $files) {
                    $result .= '<div class="row">';
                    $result .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">';
                    $result .= '<div class="row">';

                    foreach ($files as $k => $file) {
                        $result .= '<div class="row-link-container">';
                        $result .= '<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 document-link-container"><a target="_blank" href="' . $file->document_link . '">' . $file->document_name . '</a></div>';
                        $result .= '<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"><span class="pull-right"><a target="_blank" href="' . $file->document_link . '"><i class="fas fa-download"></i></a> &nbsp;&nbsp;&nbsp;<i class="fas fa-trash-alt text-red delete-report" data-report_id="' . ((!empty($file->report_id)) ? $file->report_id : '') . '"></i></span></div>';
                        $result .= '</div>';
                    }

                    $result .= '</div>';
                    $result .= '</div>';
                    $result .= '</div>';
                }
                $result .= '</div>';
                $result .= '</div>';
            }
        }

        return $result;
    }


    public function delete_report($page = "details")
    {
        $return_data    = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $section        = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_delete) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data) && !empty($post_data['report_id'])) {
                $postdata                   = [];
                $postdata['account_id']     = $this->user->account_id;
                $postdata['report_id']      = (!empty($post_data['report_id'])) ? $post_data['report_id'] : false ;
                $url                        = 'report/delete';
                $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');
                if (!empty($API_result) && ($API_result->status == true)) {
                    $return_data['d_report']    = (isset($API_result->status) && !empty($API_result->d_report)) ? $API_result->d_report : null;
                    $return_data['status']      = $API_result->status;
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : (!empty($API_result->error) ? $API_result->error : "Request completed but unsuccessful!");
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }
}
