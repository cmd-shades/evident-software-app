<?php

namespace Application\Modules\Service\Controllers\Api;

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
