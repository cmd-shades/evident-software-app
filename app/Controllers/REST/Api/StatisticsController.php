<?php

namespace App\Controllers\REST\Api;

use App\Adapter\RESTController;
use App\Models\Service\StatisticsModel;

final class StatisticsController extends RESTController
{
	/**
	 * @var \Application\Modules\Service\Models\StatisticsModel
	 */
	private $stats_service;

	public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->stats_service = new StatisticsModel();
        $this->form_validation->set_error_delimiters(
			$this->config->item('error_start_delimiter', 'ion_auth'),
			$this->config->item('error_end_delimiter', 'ion_auth')
		);
        $this->lang->load('auth');
    }

    /**
    * Get Dashboard counters
    */
    public function job_stats_get()
    {
        $account_id 	= (int) $this->get('account_id');
        $stat_type 		= ($this->get('stat_type')) ? $this->get('stat_type') : false;
        $where 			= ($this->get('where')) ? $this->get('where') : false;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> 'Invalid main Account ID',
                'type' 		=> 'job_stats',
                'stats' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $job_stats = $this->stats_service->get_job_stats($account_id, $stat_type, $where);

        if (!empty($job_stats)) {
            $message = [
                'status' 	=> true,
                'message' 	=> $this->session->flashdata('message'),
                'type' 		=> 'job_stats',
                'stats' 	=> $job_stats
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'message' 	=> $this->session->flashdata('message'),
                'type' 		=> 'job_stats',
                'stats' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get Asset Dashboard Stats
    */

    public function asset_stats_get()
    {
        $account_id 	= (int) $this->get('account_id');
        $stat_type 		= $this->get('stat_type');
        $period_days 	= ($this->get('period_days')) ? $this->get('period_days') : 90;
        $where 			= ($this->get('where')) ? $this->get('where') : false;
        $date_from 		= ($this->get('date_from')) ? $this->get('date_from') : false;
        $date_to 		= ($this->get('date_to')) ? $this->get('date_to') : date('Y-m-d');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'message' 		=> 'Invalid main Account ID',
                'stat_type' 	=> (!empty($stat_type)) ? $stat_type : 'asset_stats',
                'asset_stats' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $asset_stats = $this->stats_service->get_asset_stats($account_id, $stat_type, $period_days, $where, $date_from, $date_to);

        if (!empty($asset_stats)) {
            $message = [
                'status' 		=> true,
                'message' 		=> $this->session->flashdata('message'),
                'stat_type' 	=> (!empty($stat_type)) ? $stat_type : 'asset_stats',
                'asset_stats' 	=> $asset_stats
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'stat_type' 	=> (!empty($stat_type)) ? $stat_type : 'asset_stats',
                'asset_stats' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get Building stats
    */
    public function buildings_stats_get()
    {
        $account_id = !empty($this->get('account_id')) ? $this->get('account_id') : false;
        $stat_type 	= !empty($this->get('stat_type')) ? $this->get('stat_type') : false;
        $where 		= !empty($this->get('where')) ? $this->get('where') : false;
        ;

        $buildings_stats 	= $this->stats_service->get_buildings_stats($account_id, $stat_type, $where);

        if (!empty($buildings_stats)) {
            $message = [
                'status' 			=> true,
                'message' 			=> 'Building stats data found',
                'stat_type' 	=> (!empty($stat_type)) ? $stat_type : 'buildings_stats',
                'buildings_stats'	=> $buildings_stats
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> 'No records found',
                'stat_type' 		=> (!empty($stat_type)) ? $stat_type : 'buildings_stats',
                'buildings_stats'	=> null
            ];
            $this->response($message, REST_Controller::HTTP_NO_CONTENT);
        }
    }

    /**
    * Get Evidocs Dashboard Stats
    */
    public function evidoc_stats_get()
    {
        $account_id 	= (int) $this->get('account_id');
        $stat_type 		= $this->get('stat_type');
        $where 			= ($this->get('where')) ? $this->get('where') : false;
        $period_days 	= ($this->get('period_days')) ? $this->get('period_days') : 90;
        $date_from 		= ($this->get('date_from')) ? $this->get('date_from') : false;
        $date_to 		= ($this->get('date_to')) ? $this->get('date_to') : date('Y-m-d');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'message' 		=> 'Invalid main Account ID',
                'stat_type' 	=> (!empty($stat_type)) ? $stat_type : 'audit_stats',
                'audit_stats' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $audit_stats = $this->stats_service->get_audit_stats($account_id, $stat_type, $period_days, $where, $date_from, $date_to);

        if (!empty($audit_stats)) {
            $message = [
                'status' 		=> true,
                'message' 		=> $this->session->flashdata('message'),
                'stat_type' 	=> (!empty($stat_type)) ? $stat_type : 'audit_stats',
                'audit_stats' 	=> $audit_stats
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'stat_type' 	=> (!empty($stat_type)) ? $stat_type : 'audit_stats',
                'audit_stats' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Get Evidocs Exceptions Stats
    */
    public function exceptions_stats_get()
    {
        $account_id 	= (int) $this->get('account_id');
        $stat_type 		= $this->get('stat_type');
        $where 			= ($this->get('where')) ? $this->get('where') : false;
        $period_days 	= ($this->get('period_days')) ? $this->get('period_days') : 90;
        $date_from 		= ($this->get('date_from')) ? $this->get('date_from') : false;
        $date_to 		= ($this->get('date_to')) ? $this->get('date_to') : date('Y-m-d');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid main Account ID',
                'stat_type' 		=> (!empty($stat_type)) ? $stat_type : 'audit_stats',
                'exceptions_stats' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $exceptions_stats = $this->stats_service->exceptions_status_stats($account_id, $stat_type, $period_days, $where, $date_from, $date_to);

        if (!empty($exceptions_stats)) {
            $message = [
                'status' 		=> true,
                'message' 		=> $this->session->flashdata('message'),
                'stat_type' 	=> (!empty($stat_type)) ? $stat_type : 'exceptions_stats',
                'exceptions_stats' 	=> $exceptions_stats
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'stat_type' 	=> (!empty($stat_type)) ? $stat_type : 'exceptions_stats',
                'exceptions_stats' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Get Planned works Statistics (Schedules)
    */
    public function schedules_stats_get()
    {
        $account_id 	= (int) $this->get('account_id');
        $stat_type 		= $this->get('stat_type');
        $where 			= !empty($this->get('where')) ? $this->get('where') : false;
        $date_from 		= ($this->get('date_from')) ? $this->get('date_from') : false;
        $date_to 		= ($this->get('date_to')) ? $this->get('date_to') : date('Y-m-d');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid main Account ID',
                'stat_type' 		=> (!empty($stat_type)) ? $stat_type : 'schedules_stats',
                'schedules_stats' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $schedules_stats = $this->stats_service->get_schedules_stats($account_id, $stat_type, $where, $date_from, $date_to);

        if (!empty($schedules_stats)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'stat_type' 		=> (!empty($stat_type)) ? $stat_type : 'schedules_stats',
                'schedules_stats' 	=> $schedules_stats
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'stat_type' 		=> (!empty($stat_type)) ? $stat_type : 'schedules_stats',
                'schedules_stats' 	=> null
            ];
        }
    }
}
