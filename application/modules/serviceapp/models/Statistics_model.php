<?php

namespace Application\Service\Models;

defined('BASEPATH') || exit('No direct script access allowed');

use System\Core\CI_Model;

class Statistics_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /*
    * Get Jobs statistics
    */
    public function get_job_statistics($account_id = false, $job_date = false, $assigned_to = false, $date_from = false, $date_to = false, $offset = 0, $limit = 1000)
    {
        $result = false;

        $this->db->select('SUM(CASE WHEN job_status = "Assigned"  THEN 1 ELSE 0 END) AS Assigned,
			SUM(CASE WHEN job_status = "In Progress" THEN 1 ELSE 0 END) AS `InProgress`,
			SUM(CASE WHEN job_status = "Cancelled"  THEN 1 ELSE 0 END) AS Cancelled,
			SUM(CASE WHEN job_status = "Failed" THEN 1 ELSE 0 END) AS Failed,
			SUM(CASE WHEN job_status = "Successful" THEN 1 ELSE 0 END) AS Successful,
			SUM(CASE WHEN ( job_status = "Un-assigned" OR job_status = "" OR job_status IS NULL ) THEN 1 ELSE 0 END) AS `Unassigned`,			
			SUM(CASE WHEN job_id > 0 THEN 1 ELSE 0 END) AS TotalJobs', false);

        if ($account_id) {
            $this->db->where('customer_jobs.account_id', $account_id);
        }

        if ($assigned_to) {
            $this->db->select('concat(user.first_name," ",user.last_name) `assignee_name`', false)
            ->join('user', 'user.id = customer_jobs.assigned_to', 'left');
            $this->db->where('assigned_to', $assigned_to);
        }

        if ($date_from) {
            $date_from  = date('Y-m-d', strtotime($date_from));
            $date_to    = ( !empty($date_to) ) ? date('Y-m-d', strtotime($date_to)) : date('Y-m-d');
            $this->db->where('job_date >=', $date_from);
            $this->db->where('job_date <=', $date_to);
        } elseif ($job_date) {
            $job_date = date('Y-m-d', strtotime($job_date));
            $this->db->where('job_date', $job_date);
        }

        $job = $this->db->order_by('job_status')
            ->offset($offset)
            ->limit($limit)
            ->get('customer_jobs');

        if ($job->num_rows() > 0) {
            $this->session->set_flashdata('message', 'Job stats found');
            $result = $job->result()[0];
        } else {
            $this->session->set_flashdata('message', 'Job stats not available');
        }
        return $result;
    }
}
