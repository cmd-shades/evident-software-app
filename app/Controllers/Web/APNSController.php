<?php

namespace App\Controllers\Web;

use App\Extensions\MX\Controller as MX_Controller;

class APNSController extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }
    }

    public function index()
    {
        $data['users'] 			= $this->ion_auth->get_users_by_account_id($this->account_id);
        $url	  				= $this->api_end_point.'alert/site_packets';
        $options  				= ['method'=>'GET','auth_token'=>$this->auth_token];
        $postdata 				= $this->ssid_common->_prepare_curl_post_data(['account_id'=>$this->user->account_id]);
        $response 				= $this->ssid_common->doCurl($url, $postdata, $options);
        $data['site_packets'] 	= (isset($response->site_packets)) ? $response->site_packets : null;
        $data['apns_status']	= $this->session->flashdata('apns_status');
        $data['apns_feedback'] 	= $this->session->flashdata('apns_feedback');
        $data['alert_status']	= $this->session->flashdata('alert_status');
        $data['alert_feedback'] = $this->session->flashdata('alert_feedback');
        $this->_render_webpage('apns/index', $data);
    }

    //Add a new audit
    public function push()
    {
        if ($this->input->post()) {
            $options  		= ['auth_token'=>$this->auth_token];
            $postdata 		= $this->ssid_common->_prepare_curl_post_data($this->input->post());
            $push_message	= $this->ssid_common->doCurl($this->api_end_point.'notification/push_notification', $postdata, $options);
            $push_status	= (isset($push_message->status)) ? $push_message->status : null;
            $push_feedback	= (isset($push_message->message)) ? $push_message->message : null;
            $push_result	= (isset($push_message->push_notification)) ? $push_message->push_notification : null;

            if (!empty($push_result)) {
                $this->session->set_flashdata('apns_status', $push_status);
                $this->session->set_flashdata('apns_feedback', $push_feedback);
            }
        }
        redirect('webapp/apns/index/', 'refresh');
    }

    //Trigger an alert
    public function trigger()
    {
        if ($this->input->post()) {
            $options  		= ['auth_token'=>$this->auth_token];
            $postdata 		= $this->ssid_common->_prepare_curl_post_data($this->input->post());
            $trigger_alert	= $this->ssid_common->doCurl($this->api_end_point.'alert/trigger_alert', $postdata, $options);
            $alert_status	= (isset($trigger_alert->status)) ? $trigger_alert->status : null;
            $alert_feedback	= (isset($trigger_alert->message)) ? $trigger_alert->message : null;
            $trigger_result	= (isset($trigger_alert->trigger_alert)) ? $trigger_alert->trigger_alert : null;

            if (!empty($trigger_result)) {
                $this->session->set_flashdata('alert_status', $alert_status);
                $this->session->set_flashdata('alert_feedback', $alert_feedback);
            }
        }
        redirect('webapp/apns/index/', 'refresh');
    }
}
