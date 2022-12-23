<?php

namespace Application\Service\Controllers\Api;

use App\Libraries\REST_Controller;

defined('BASEPATH') || exit('No direct script access allowed');

class Statistics extends REST_Controller
{
    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('Statistics_model', 'stats_service');
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->lang->load('auth');
    }

    /**
    * Get Dashboard counters
    */
    public function job_stats_get()
    {
        $account_id     = (int) $this->get('account_id');
        $assigned_to    = (int) $this->get('assigned_to');
        $job_date       = ($this->get('job_date')) ? $this->get('job_date') : date('Y-m-d');
        $date_from      = ($this->get('date_from')) ? $this->get('date_from') : false;
        $date_to        = ($this->get('date_to')) ? $this->get('date_to') : date('Y-m-d');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID',
                'type' => 'job_stats',
                'stats' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $job_stats = $this->stats_service->get_job_statistics($account_id, $job_date, $assigned_to, $date_from, $date_to);

        if (!empty($job_stats)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'type' => 'job_stats',
                'stats' => $job_stats
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'type' => 'job_stats',
                'stats' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
