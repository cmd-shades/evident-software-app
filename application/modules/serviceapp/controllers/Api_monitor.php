<?php

namespace Application\Service\Controllers\Api;

use System\Core\CI_Controller;

defined('BASEPATH') || exit('No direct script access allowed');

class Api_monitor extends CI_Controller
{
    public function index()
    {
        $this->load->helper('url');
        $this->load->view('serviceapp/api_monitor');
    }
}
