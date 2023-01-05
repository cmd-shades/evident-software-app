<?php

namespace App\Controllers\Web;

use App\Extensions\MX\Controller as MX_Controller;
class Report extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->module_id 	   = $this->webapp_service->_get_module_id($this->router->fetch_class());
        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }
        $this->options = ['auth_token'=>$this->auth_token];
    }

    public function index()
    {
        $this->reports();
    }

    public function reports($page = 'details')
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            if ($this->input->post()) {
                $postdata    		 		= array_merge(['account_id'=>$this->user->account_id], $this->input->post());

                $report_type		 		= $postdata['report_type'];

                $report_data 		 		= $this->webapp_service->api_dispatcher($this->api_end_point.'report/reports', $postdata, $this->options, true);
                $data['report_data'] 		= (isset($report_data->report)) ? $report_data->report : null;

                $data['feedback_msg']		= (isset($report_data->message)) ? $report_data->message : 'No data matching your report criteria.';
                $data['chked_report_type'] 	= $report_type;

                if (!empty($data['report_data']->file_link)) {
                    force_download($data['report_data']->file_name, file_get_contents($data['report_data']->file_path));
                }
            }

            $contracts 				= $this->webapp_service->api_dispatcher($this->api_end_point.'contract/contracts', ['account_id'=>$this->user->account_id, 'limit'=>-1], $this->options, true);

            $setup_data 			= $this->webapp_service->api_dispatcher($this->api_end_point.'report/report_types_setup', ['account_id'=>$this->user->account_id], $this->options, true);
            $tailored_reports		= $this->webapp_service->api_dispatcher($this->api_end_point.'report/tailored_reports_setup', ['account_id'=>$this->user->account_id], $this->options, true);

            $data['contracts']  		= (!empty($contracts->contract)) ? $contracts->contract : null;
            $data['tailored_contracts'] = $data['contracts'];

            $data['setup_data'] 		= (!empty($setup_data)) ? $setup_data->report_setup : null;
            $data['tailored_reports'] 	= (!empty($tailored_reports)) ? $tailored_reports->tailored_reports : null;

            $data['user'] 		= $this->user;
            $this->_render_webpage('report/index', $data);
        }
    }
}
