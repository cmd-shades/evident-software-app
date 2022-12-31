<?php

namespace Application\Modules\Service\Controllers\Api;

use Application\Extensions\MX_Controller;

class Cron extends MX_Controller
{
	/**
	 * @var array|null[]
	 */
	private $options = [];

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
