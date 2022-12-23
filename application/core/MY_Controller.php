<?php

namespace Application\Core;

use System\Core\CI_Controller;

class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        date_default_timezone_set('Europe/London');
        #require APPPATH . 'libraries/REST_Controller.php';
    }
}
