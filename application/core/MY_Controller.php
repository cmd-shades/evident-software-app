<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    function __construct()
    {
        date_default_timezone_set('Europe/London');
        #require APPPATH . 'libraries/REST_Controller.php';
    }
}
