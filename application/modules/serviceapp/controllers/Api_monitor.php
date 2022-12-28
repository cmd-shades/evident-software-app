<?php

namespace Application\Modules\Service\Controllers;

class Api_monitor extends CI_Controller {

    public function index()
    {
        $this->load->helper('url');
        $this->load->view('serviceapp/api_monitor');
    }
}
