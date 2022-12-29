<?php

namespace Application\Modules\Web\Controllers;

use Application\Extentions\MX_Controller;

class Marketing extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        $this->module_id        = $this->webapp_service->_get_module_id($this->router->fetch_class());
        $this->module_access    = $this->webapp_service->check_access($this->user, $this->module_id);
    }


    public function index()
    {
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            redirect('webapp/marketing/dashboard', 'refresh');
        }
    }


    /**
    *   The main Dashboard
    **/
    public function dashboard()
    {
        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $data                           = false;

            $data['marketing_modules']      = $postdata = [];
            $postdata['account_id']         = $this->user->account_id;
            $url                            = 'marketing/modules';
            $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $data['marketing_modules']      = (isset($API_result->modules)) ? $API_result->modules : null;

            $this->_render_webpage('marketing/dashboard', $data);
        }
    }


    /**
    *   The Current Titles view
    **/
    public function current_titles()
    {
        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $data                           = false;

            $data['flash_message']          = (!empty($this->session->flashdata('message'))) ? $this->session->flashdata('message') : false ;

            $data['product_types']          = $postdata = [];
            $postdata['account_id']         = $this->user->account_id;
            $postdata['where']['setting_name_group']    = "2_product_type"; ## 'product status'
            $url                            = 'settings/settings';
            $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $data['product_types']          = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->settings)) ? $API_result->settings : null;

            $data['territories']            = $postdata = [];
            $postdata['account_id']         = $this->user->account_id;
            $url                            = 'content/territories';
            $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $data['territories']            = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->territories)) ? $API_result->territories : null;

            $data['content_providers']  = $postdata = [];
            $postdata['account_id']     = $this->user->account_id;
            $url                        = 'provider/provider';
            $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $data['content_providers']  = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->content_provider)) ? $API_result->content_provider : null;

            $this->_render_webpage('marketing/current_titles', $data);
        }
    }


    /**
    *   Generate PDF data
    **/
    public function generate_pdf()
    {
        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data) && (int) !empty($post_data['territory_id']) && !empty($post_data['provider_id']) && !empty($post_data['product_name'])) {
                ## 1. Generate PDF data
                $postdata                       = [];
                $postdata['account_id']         = $this->user->account_id;
                $postdata['territory_id']       = (int) $post_data['territory_id'];
                $postdata['provider_ids']       = (array) $post_data['provider_id'];
                $postdata['product_name']       = $post_data['product_name'];
                $postdata['limit']              = (!empty($post_data['limit'])) ? $post_data['limit'] : 100 ;

                $url                            = 'content/generate_pdf_data';

                $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');
                if (!empty($API_result->pdf_data)) {
                    ## 2. Get / Set (custom) settings
                    $document_setup = [
                        "pdf_type"          => strtolower($post_data['product_name']),
                        "pdf_data"          => $API_result->pdf_data,
                        "pdf_category"      => "pdf_marketing",
                        "account_id"        => $this->user->account_id,
                    ];

                    ## 3. Generate PDF
                    $this->load->view('/evipdf/marketing_pdf_generator.php', $document_setup);
                } else {
                    $this->session->set_flashdata('message', 'No result based on given criteria');
                    redirect('webapp/marketing/current_titles/', 'refresh');
                }
            } else {
                $this->session->set_flashdata('message', 'Required data is missing');
                redirect('webapp/marketing/current_titles/', 'refresh');
            }
        }
    }
}
