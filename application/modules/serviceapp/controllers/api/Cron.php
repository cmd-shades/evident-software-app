<?php

namespace Application\Service\Controllers\Api;

defined('BASEPATH') || exit('No direct script access allowed');

class Cron extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }
        $this->options = ['auth_token' => $this->auth_token];
    }

    public function index()
    {
        echo "Hello, World" . PHP_EOL;
    }
}
