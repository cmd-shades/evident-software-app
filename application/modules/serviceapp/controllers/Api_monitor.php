<?php

namespace Application\Service\Controllers\Api;

use System\Core\CI_Controller;

class Api_monitor extends CI_Controller
{
    public function index()
    {
        $this->load->helper('url');
        $this->load->view('serviceapp/api_monitor');
    }
}
