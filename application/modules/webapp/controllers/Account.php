<?php

namespace Application\Modules\Web\Controllers;

use Application\Extentions\MX_Controller;

class Account extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        redirect('webapp/user/login', 'refresh');
    }

    public function activate($activate_str = false)
    {
        if (!empty($activate_str)) {
            $activate   = $this->webapp_service->api_dispatcher($this->api_end_point . 'account/activate_account', ['activation_code' => $activate_str]);
            $data['activation_data'] = (!empty($activate)) ? $activate : null;
            $this->_render_webpage('account/activate', $data);
        } else {
            redirect('webapp/user/login', 'refresh');
        }
    }
}
