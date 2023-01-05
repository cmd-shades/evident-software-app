<?php

namespace App\Controllers\REST;

use App\Controllers\BaseController;
use System\Core\Loader;

final class ApiMonitorController extends BaseController
{
	public function index()
    {
        $this->load->helper('url');
        $this->load->view('serviceapp/api_monitor');
    }
}
