<?php

namespace App\Controllers\Web;

use App\Extensions\MX\Controller as MX_Controller;
use App\Models\Service\DashboardModel;

class DashboardController extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('serviceapp/Dashboard_model', 'dashboard_service');
		$this->dashboard_service = new DashboardModel();

        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        $this->data_configs = [
            'base_url' 		=> base_url(),
            'root_folder' 	=> basename(base_url()),
            'api_end_point' => base_url() . SERVICE_END_POINT,
            #'api_end_point' => 'http://77.68.92.77/evident-core/' . SERVICE_END_POINT,
            'account_id' 	=> $this->user->account_id
        ];
    }

    public function index()
    {
        $data = array_merge([], $this->data_configs);
        $this->_render_webpage('dashboard/main', $data);
    }

    public function site($site_id = false)
    {
        $data = array_merge([], $this->data_configs);
        $this->_render_webpage('dashboard/site', $data);
    }


    public function discipline()
    {
        $data = array_merge([], $this->data_configs);
        $this->_render_webpage('dashboard/discipline', $data);
    }

    public function outcomes()
    {
        $data = array_merge([], $this->data_configs);
        $this->_render_webpage('dashboard/outcomes', $data);
    }

    public function fire()
    {
        $data = array_merge([], $this->data_configs);
        $this->_render_webpage('dashboard/fire', $data);
    }

    public function electricity()
    {
        $data = array_merge([], $this->data_configs);
        $this->_render_webpage('dashboard/electricity', $data);
    }

    public function security()
    {
        $data = array_merge([], $this->data_configs);
        $this->_render_webpage('dashboard/security', $data);
    }

    public function water()
    {
        $data = array_merge([], $this->data_configs);
        $this->_render_webpage('dashboard/water', $data);
    }

    public function gas()
    {
        $data = array_merge([], $this->data_configs);
        $this->_render_webpage('dashboard/gas', $data);
    }

    public function specialist()
    {
        $data = array_merge([], $this->data_configs);
        $this->_render_webpage('dashboard/specialist', $data);
    }

    public function building()
    {
        $data = array_merge([], $this->data_configs);
        $this->_render_webpage('dashboard/building', $data);
    }
}
