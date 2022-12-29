<?php

namespace Application\Modules\Web\Controllers;

class Home extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        $this->options = ['auth_token'=>$this->auth_token];
    }

    public function index()
    {
        $allowed_modules			= $this->webapp_service->api_dispatcher($this->api_end_point.'access/check_module_access', ['account_id'=>$this->account_id, 'user_id'=>$this->user_id], $this->options);
        $data['permitted_modules'] 	= (!empty($allowed_modules->module_access)) ? $allowed_modules->module_access : null;
        $data['module_count'] 		= (!empty($allowed_modules->module_access)) ? count($allowed_modules->module_access) : 0;
        $data['user'] 				= $this->user;

        $data['asset_totals'] = $this->webapp_service->api_dispatcher($this->api_end_point.'statistics/asset_stats', ['account_id'=>$this->account_id, 'stat_type' => 'total_number'], $this->options, true);
        $data['building_compliance'] = $this->webapp_service->api_dispatcher($this->api_end_point.'audit/audit_stats', ['account_id'=>$this->account_id, 'stat_type' => 'audit_results'], $this->options, true);
        $data['asset_eol'] = $this->webapp_service->api_dispatcher($this->api_end_point.'statistics/asset_stats', ['account_id'=>$this->account_id, 'stat_type' => 'eol'], $this->options, true);
        $data['replacement_cost'] = $this->webapp_service->api_dispatcher($this->api_end_point.'statistics/asset_stats', ['account_id'=>$this->account_id, 'stat_type' => 'replace_cost'], $this->options, true);

        // Redirect if user only has access to 1 module
        if (!empty($data['module_count']) && $data['module_count'] == 1) {
            $module = $data['permitted_modules'][0];
            redirect('webapp/'.$module->module_controller, 'refresh');
        } else {
            $audit_categories	 		= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/audit_categories', [ 'account_id'=>$this->user->account_id, 'where'=>['used_for_stats'=>1] ], ['auth_token'=>$this->auth_token], true);
            $data['evidoc_categories']	= (isset($audit_categories->audit_categories)) ? $audit_categories->audit_categories : null;

            $monthly_jobs_data	 		= $this->webapp_service->api_dispatcher($this->api_end_point.'statistics/job_stats', [ 'account_id'=>$this->user->account_id, 'stat_type'=>'maintenance_calls', 'where'=>['monthly'=>1] ], ['auth_token'=>$this->auth_token], true);
            $data['monthly_jobs_data']	= (isset($monthly_jobs_data->stats)) ? $monthly_jobs_data->stats : null;

            $annual_jobs_data	 		= $this->webapp_service->api_dispatcher($this->api_end_point.'statistics/job_stats', [ 'account_id'=>$this->user->account_id, 'stat_type'=>'maintenance_calls', 'where'=>['annual'=>1] ], ['auth_token'=>$this->auth_token], true);
            $data['annual_jobs_data']	= (isset($annual_jobs_data->stats)) ? $annual_jobs_data->stats : null;

            $data['checklist_counter'] 	= $postdata = [];
            $postdata['account_id'] 	= $this->account_id;
            $api_call 					= $this->webapp_service->api_dispatcher($this->api_end_point.'tesseract/checklist_counter', $postdata, $this->options, true);
            $data['checklist_counter'] 	= (!empty($api_call->checklist_counts)) ? $api_call->checklist_counts : $data['checklist_counter'] ;

            /* 		if( !empty( $_GET['sandbox'] ) && ( $_GET['sandbox'] == 1 ) ){
                        $this->_render_webpage('home/stats', $data, false, true );
                    }else{
                        #$this->_render_webpage('home/index', $data, false, true );
                        #$this->_render_webpage('home/dashboard', $data, false, true );
                        if( in_array( $this->user->user_type_id, EXTERNAL_USER_TYPES ) ){
                            redirect( 'webapp/job/jobs', 'refresh' );
                        } else {
                            if( !empty( $_GET['dashboard2'] ) && ( $_GET['dashboard2'] == 1 ) ){
                                $this->_render_webpage( 'home/stats', $data, false, true );
                            } else {
                                $this->_render_webpage('home/dashboard', $data, false, true );
                            }
                        }
                    } */

            if (!empty($_GET['sandbox']) && ($_GET['sandbox'] == 1)) {
                $this->_render_webpage('home/stats', $data, false, true);
            } else {
                #$this->_render_webpage('home/index', $data, false, true );
                #$this->_render_webpage('home/dashboard', $data, false, true );
                if (in_array($this->user->user_type_id, EXTERNAL_USER_TYPES)) {
                    redirect('webapp/job/jobs', 'refresh');
                } else {
                    if (!empty($_GET['dashboard2']) && ($_GET['dashboard2'] == 1)) {
                        $this->_render_webpage('home/stats', $data, false, true);
                    } else {
                        $this->_render_webpage('home/dashboard_new', $data, false, true);
                    }
                }
            }
        }
    }

    public function stats()
    {
        $allowed_modules			= $this->webapp_service->api_dispatcher($this->api_end_point.'access/check_module_access', ['account_id'=>$this->account_id, 'user_id'=>$this->user_id], $this->options);
        $data['permitted_modules'] 	= (!empty($allowed_modules->module_access)) ? $allowed_modules->module_access : null;
        $data['module_count'] 		= (!empty($allowed_modules->module_access)) ? count($allowed_modules->module_access) : 0;
        $data['user'] 				= $this->user;

        $data['asset_totals'] = $this->webapp_service->api_dispatcher($this->api_end_point.'statistics/asset_stats', ['account_id'=>$this->account_id, 'stat_type' => 'total_number'], $this->options, true);
        $data['building_compliance'] = $this->webapp_service->api_dispatcher($this->api_end_point.'audit/audit_stats', ['account_id'=>$this->account_id, 'stat_type' => 'audit_results'], $this->options, true);
        $data['asset_eol'] = $this->webapp_service->api_dispatcher($this->api_end_point.'statistics/asset_stats', ['account_id'=>$this->account_id, 'stat_type' => 'eol'], $this->options, true);
        $data['replacement_cost'] = $this->webapp_service->api_dispatcher($this->api_end_point.'statistics/asset_stats', ['account_id'=>$this->account_id, 'stat_type' => 'replace_cost'], $this->options, true);

        // Redirect if user only has access to 1 module
        if (!empty($data['module_count']) && $data['module_count'] == 1) {
            $module = $data['permitted_modules'][0];
            redirect('webapp/'.$module->module_controller, 'refresh');
        } else {
            $audit_categories	 		= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/audit_categories', [ 'account_id'=>$this->user->account_id, 'where'=>['used_for_stats'=>1] ], ['auth_token'=>$this->auth_token], true);

            $data['evidoc_categories']	= (isset($audit_categories->audit_categories)) ? $audit_categories->audit_categories : null;
            $monthly_jobs_data	 		= $this->webapp_service->api_dispatcher($this->api_end_point.'statistics/job_stats', [ 'account_id'=>$this->user->account_id, 'stat_type'=>'maintenance_calls', 'where'=>['monthly'=>1] ], ['auth_token'=>$this->auth_token], true);
            $data['monthly_jobs_data']	= (isset($monthly_jobs_data->stats)) ? $monthly_jobs_data->stats : null;

            $annual_jobs_data	 		= $this->webapp_service->api_dispatcher($this->api_end_point.'statistics/job_stats', [ 'account_id'=>$this->user->account_id, 'stat_type'=>'maintenance_calls', 'where'=>['annual'=>1] ], ['auth_token'=>$this->auth_token], true);
            $data['annual_jobs_data']	= (isset($annual_jobs_data->stats)) ? $annual_jobs_data->stats : null;

            $this->_render_webpage('home/stats', $data, false, true);
        }
    }


/* GANTT FUNCTIONS */

    // a non-ajax get projects call
    public function get_projects()
    {
        $return_data 		= [ 'status'=>0 ];
        $postdata           =   ['account_id'=>$this->user->account_id];
        $project_data 		= $this->webapp_service->api_dispatcher($this->api_end_point.'project/projects', $postdata, ['auth_token'=>$this->auth_token], true);

        $result      = (!empty($project_data->projects)) ? $project_data->projects : null;
        $message	 = (!empty($project_data->message)) ? $project_data->message : 'Oops! There was an error processing your request.';

        if (!empty($result)) {
            $return_data['status'] = 1;
            $return_data['stats'] = $result;
        }

        $return_data['status_msg'] = $message;

        return $return_data;
    }

    public function gantt()
    {
        $data['project_data'] = $this->get_projects();

        // requires 2 months after the current date
        $data['viewMonths'] = array(
            (new DateTime('2019-09-01'))->format('Y-m-d'),
            (new DateTime('2019-10-01'))->format('Y-m-d'),
            (new DateTime('2019-11-01'))->format('Y-m-d'),
            (new DateTime('2020-00-01'))->format('Y-m-d'),
            (new DateTime('2020-01-01'))->format('Y-m-d'),
            (new DateTime('2020-02-01'))->format('Y-m-d'),
            (new DateTime('2020-03-01'))->format('Y-m-d'),
        );

        $data['monthCount'] = count($data['viewMonths']) - 1;

        $this->_render_webpage('home/gantt', $data, false, true);
    }

    public function evitime()
    {
        $this->_render_webpage('home/evitime', false, false, true);
    }

/* GANTT FUNCTION END */


    /** Get Site related stats **/
    public function evidoc_stats()
    {
        $return_data 		= [ 'status'=>0 ];
        $postdata 	  		= array_merge(['account_id'=>$this->user->account_id, 'stat_type' => 'completion'], $this->input->post());
        $evidoc_status_stats	= $this->webapp_service->api_dispatcher($this->api_end_point.'statistics/evidoc_stats', $postdata, $this->options, true);

        $result				= (!empty($evidoc_status_stats->audit_stats)) ? $evidoc_status_stats->audit_stats : null;
        $message	  		= (isset($evidoc_status_stats->message)) ? $evidoc_status_stats->message : 'Oops! There was an error processing your request.';

        if (!empty($result)) {
            $return_data['status'] = 1;
            $return_data['stats'] = $result;
        }

        $return_data['status_msg'] = $message;
        print_r(json_encode($return_data));
        die();
    }




    public function dashboard()
    {
        redirect('webapp/home/index', 'refresh');
    }

    /** Get Asset stats **/
    public function asset_stats()
    {
        $return_data 		= [ 'status'=>0 ];
        $postdata 	  		= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
        $asset_status_stats	= $this->webapp_service->api_dispatcher($this->api_end_point.'asset/asset_stats', $postdata, $this->options, true);
        $result				= (!empty($asset_status_stats->asset_stats)) ? $asset_status_stats->asset_stats : null;
        $message	  		= (isset($asset_status_stats->message)) ? $asset_status_stats->message : 'Oops! There was an error processing your request.';

        if (!empty($result)) {
            $return_data['status'] = 1;
            $return_data['stats']  = $result;
        }
        $return_data['status_msg'] = $message;
        print_r(json_encode($return_data));
        die();
    }

    /** Get Site related stats **/
    public function site_stats()
    {
        $return_data 		= [ 'status'=>0 ];
        $postdata 	  		= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
        $site_status_stats	= $this->webapp_service->api_dispatcher($this->api_end_point.'site/site_stats', $postdata, $this->options, true);

        $result				= (!empty($site_status_stats->site_stats)) ? $site_status_stats->site_stats : null;
        $message	  		= (isset($site_status_stats->message)) ? $site_status_stats->site_stats : 'Oops! There was an error processing your request.';

        if (!empty($result)) {
            $return_data['status'] = 1;
            $return_data['stats']  = $result->stats;
            $return_data['totals'] = $result->totals;
        }
        $return_data['status_msg'] = $message;
        print_r(json_encode($return_data));
        die();
    }

    /** Get Audit related statistics **/
    public function audit_stats()
    {
        $return_data  	= [ 'status'=>0 ];
        $postdata 	  	= array_merge(['account_id'=>$this->user->account_id, 'stat_type'=>'periodic_audits' ], $this->input->post());

        $audit_stats	= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/audit_stats', $postdata, $this->options, true);

        $result			= (!empty($audit_stats->audit_stats)) ? $audit_stats->audit_stats : null;
        $message		= (isset($audit_stats->message)) ? $audit_stats->audit_stats : 'Oops! There was an error processing your request.';

        if (!empty($result)) {
            $return_data['status'] = 1;
            $return_data['stats']  = !empty($result->stats) ? $result->stats : false;
            $return_data['totals'] = !empty($result->totals) ? $result->totals : false;
            $return_data['dates'] 	= !empty($result->dates) ? $result->dates : false;
        }

        $return_data['status_msg'] = $message;
        print_r(json_encode($return_data));
        die();
    }


    public function job_completition_stats()
    {
        $return_data 			= [ 'status'=>0 ];

        $postdata 	  			= array_merge(['account_id'=>$this->user->account_id, 'data_target' => 'bar'], $this->input->post());
        $job_completion_stats	= $this->webapp_service->api_dispatcher($this->api_end_point.'job/job_completion_stats', $postdata, $this->options, true);
        $message	  			= (isset($job_completion_stats->message)) ? $job_completion_stats->message : 'Oops! There was an error processing your request.';

        if (!empty($job_completion_stats)) {
            $return_data['status'] = 1;
            $return_data["stats"] = $job_completion_stats;
        } else {
            $return_data['status_msg'] = $message;
        }

        echo json_encode($return_data);
        die();
    }


    public function asset_compliance_stats()
    {
        $return_data 			= [ 'status'=>0 ];

        $postdata 	  			= array_merge(['account_id'=>$this->user->account_id, 'stat_type' => 'outcome_result'], $this->input->post());
        $asset_compliance_stats	= $this->webapp_service->api_dispatcher($this->api_end_point.'/statistics/asset_stats', $postdata, $this->options, true);
        $message	  			= (isset($asset_compliance_stats->message)) ? $asset_compliance_stats->message : 'Oops! There was an serror processing your request.';

        $result	= (!empty($asset_compliance_stats->asset_stats)) ? $asset_compliance_stats->asset_stats : false;

        if ($result) {
            $return_data['status'] = 1;
            $return_data['stats']  = !empty($result->stats) ? $result->stats : false;
            $return_data['totals'] = !empty($result->totals) ? $result->totals : false;
            $return_data['dates'] 	= !empty($result->dates) ? $result->dates : false;
        } else {
            $return_data['status_msg'] = $message;
        }

        echo json_encode($return_data);
        die();
    }

    public function evidoc_exception_stats()
    {
        $return_data 			= [ 'status'=>0 ];

        $postdata 	  			= array_merge(['account_id'=>$this->user->account_id, 'stat_type' => 'action_status'], $this->input->post());
        $evidoc_exception_stats	= $this->webapp_service->api_dispatcher($this->api_end_point.'/statistics/exceptions_stats', $postdata, $this->options, true);
        $message	  			= (isset($evidoc_exception_stats->message)) ? $evidoc_exception_stats->message : 'Oops! There was an serror processing your request.';

        $result	= (!empty($evidoc_exception_stats->exceptions_stats)) ? $evidoc_exception_stats->exceptions_stats : false;

        if ($result) {
            if (!empty($result->totals)) {
                $return_data['status'] = 1;
                $return_data['stats']  = !empty($result->stats) ? $result->stats : false;
                $return_data['totals'] = !empty($result->totals) ? $result->totals : false;
            } else {
                $return_data['status_msg'] = "No data is avaliable";
            }
        } else {
            $return_data['status_msg'] = $message;
        }

        echo json_encode($return_data);
        die();
    }

    public function assets_by_category_stats()
    {
        $return_data 			= [ 'status'=>0 ];

        $postdata 	  			= array_merge(['account_id'=>$this->user->account_id, 'stat_type' => 'assets_by_category'], $this->input->post());
        $assets_by_category_stats	= $this->webapp_service->api_dispatcher($this->api_end_point.'//statistics/asset_stats', $postdata, $this->options, true);
        $message	  			= (isset($assets_by_category_stats->message)) ? $assets_by_category_stats->message : 'Oops! There was an serror processing your request.';

        $result	= (!empty($assets_by_category_stats->asset_stats)) ? $assets_by_category_stats->asset_stats : false;

        if ($result) {
            if (!empty($result->totals)) {
                $return_data['status'] = 1;
                $return_data['stats']  = !empty($result->stats) ? re_sort_array($result->stats, 'category_name', 'asort') : false;//Re-sorted alphabetically
                $return_data['totals'] = !empty($result->totals) ? $result->totals : false;
            } else {
                $return_data['status_msg'] = "No data is avaliable";
            }
        } else {
            $return_data['status_msg'] = $message;
        }

        echo json_encode($return_data);
        die();
    }


    public function asset_by_group_stats()
    {
        $return_data 			= [ 'status'=>0 ];

        $postdata 	  			= array_merge(['account_id'=>$this->user->account_id, 'stat_type' => 'total_number'], $this->input->post());
        $asset_by_group_stats	= $this->webapp_service->api_dispatcher($this->api_end_point.'//statistics/asset_stats', $postdata, $this->options, true);
        $message	  			= (isset($asset_by_group_stats->message)) ? $asset_by_group_stats->message : 'Oops! There was an serror processing your request.';

        $result	= (!empty($asset_by_group_stats->asset_stats)) ? $asset_by_group_stats->asset_stats : false;

        if ($result) {
            if (!empty($result->totals)) {
                $return_data['status'] = 1;
                $return_data['stats']  = !empty($result->stats) ? $result->stats : false;
                $return_data['totals'] = !empty($result->totals) ? $result->totals : false;
            } else {
                $return_data['status_msg'] = "No data is avaliable";
            }
        } else {
            $return_data['status_msg'] = $message;
        }

        echo json_encode($return_data);
        die();
    }

    public function schedule_stats()
    {
        $return_data 			= [ 'status'=>0 ];

        $postdata 	  			= array_merge(['account_id'=>$this->user->account_id, 'stat_type' => 'schedules'], $this->input->get());

        $schedule_stats			= $this->webapp_service->api_dispatcher($this->api_end_point.'/statistics/schedules_stats', $postdata, $this->options, true);

        $message	  			= (isset($schedule_stats->message)) ? $schedule_stats->message : 'Oops! There was an serror processing your request.';

        $result	= (!empty($schedule_stats->schedules_stats)) ? $schedule_stats->schedules_stats : false;

        if ($result) {
            $return_data['status'] = 1;
            $return_data['stats']  = $schedule_stats->schedules_stats->stats;
            $return_data['totals'] = $schedule_stats->schedules_stats->totals;
        } else {
            $return_data['status_msg'] = $message;
        }

        echo json_encode($return_data);
        die();
    }


    /** Get Building Compliance stats **/
    public function buildings_stats()
    {
        $return_data 			= [ 'status'=>0 ];
        $postdata 	  			= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
        $building_status_stats	= $this->webapp_service->api_dispatcher($this->api_end_point.'statistics/buildings_stats', $postdata, $this->options, true);
        $result				= (!empty($building_status_stats->buildings_stats)) ? $building_status_stats->buildings_stats : null;
        $message	  		= (isset($building_status_stats->message)) ? $building_status_stats->buildings_stats : 'Oops! There was an error processing your request.';

        if (!empty($result)) {
            $return_data['status'] = 1;
            $return_data['stats']  = $result;
            $return_data['totals'] = null;
        }
        $return_data['status_msg'] = $message;
        print_r(json_encode($return_data));
        die();
    }
}
