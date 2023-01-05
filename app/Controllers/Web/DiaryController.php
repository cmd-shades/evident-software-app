<?php

namespace App\Controllers\Web;

use App\Extensions\MX\Controller as MX_Controller;
use App\Models\Service\DiaryModel;
use App\Models\Service\JobModel;

class Diary extends MX_Controller
{
	/**
	 * @var \App\Models\Service\JobModel $job_service
	 */
	private $job_service;
	/**
	 * @var \App\Models\Service\DiaryModel $diary_service
	 */
	private $diary_service;

	public function __construct()
    {
        parent::__construct();

        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        $this->module_id 	   = $this->webapp_service->_get_module_id($this->router->fetch_class());
        $this->load->library('pagination');
		$this->job_service = new JobModel();
		$this->diary_service = new DiaryModel();
    }

    //redirect if needed, otherwise display the user list
    public function index()
    {
        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            //access denied
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $this->planner();
            ## $this->diary();
        }
    }

    //redirect if needed, otherwise display the user list
    public function diary()
    {
        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            //access denied
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $this->planner();
            ## redirect( 'webapp/diary/scheduler', 'refresh' );
        }
    }

    //Planner
    public function planner()
    {
        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            //access denied
            $this->_render_webpage('errors/access-denied', false);
        } else {
            ## check if there is a date submitted
            $post_data = $this->input->post();
            $data['resource_date'] = $resource_date = (!empty($post_data['resource_date'])) ? date('Y-m-d', strtotime($post_data['resource_date'])) : date('Y-m-d') ;

            ## All unassigned jobs, despite the date
            $data['jobs'] = $postdata 			= [];
            $postdata["account_id"] 			= $this->user->account_id;
            $postdata["limit"] 					= 9999;
            $postdata["where"]["assigned_to"] 	= -1;
            $API_call							= $this->webapp_service->api_dispatcher($this->api_end_point.'job/jobs', $postdata, ['auth_token'=>$this->auth_token], true);
            $data['jobs']						= (!empty($API_call->jobs)) ? $API_call->jobs : $data['jobs'] ;

            ## all availability for the specific day, all scheduled jobs for the day
            $data['operatives_with_job'] = $data['operatives_without_job'] = $postdata = [];
            $postdata['account_id'] 			= $this->user->account_id;
            $postdata['limit'] 					= 9999;
            $postdata['where']['resource_date'] = $resource_date;
            if ($this->user->is_primary_user && !$this->user->is_admin) {
                $postdata['where']['associated_user_id']     = $this->user->id;
            }
            $API_call		= $this->webapp_service->api_dispatcher($this->api_end_point.'diary/field_operatives', $postdata, ['auth_token'=>$this->auth_token], true);
            $data['operatives_with_job']	= (isset($API_call->field_operative->with_jobs)) ? $API_call->field_operative->with_jobs : null;
            $data['operatives_without_job']	= (isset($API_call->field_operative->without_jobs)) ? $API_call->field_operative->without_jobs : null;

            $this->_render_webpage('diary/planner', $data);
            ## $this->_render_webpage( 'routing/dragdrop', $data );
        }
    }

    public function test()
    {
        //$this->_render_webpage( 'diary/progress', $schedule_context );
        $this->_render_webpage('errors/error_404', array("heading" => "heading text", "message" => "message text"));
    }


    public function commit_jobs()
    {
        $return_data = [
            'status' => 0
        ];

        $section = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        $true = false;

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin) && $true) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postset 					= $this->input->post();

            if (!empty($postset)) {
                $postdata 		= [];
                $postdata['account_id'] = $this->user->account_id;
                $postdata['job_batch'] 	= (!empty($postset['jobBatch']) ? $postset['jobBatch'] : false);

                $API_call = $this->webapp_service->api_dispatcher($this->api_end_point.'diary/route_jobs', $postdata, ['auth_token'=>$this->auth_token]);

                if ((!empty($API_call->status) && ($API_call->status == true))) {
                    $return_data = [
                        'status'		=> $API_call->status,
                        'status_msg'	=> $API_call->message,
                        'routed_jobs'	=> $API_call->routed_jobs,
                    ];
                } else {
                    if ((!empty($API_call->message))) {
                        $return_data['status'] = false ;
                        $return_data['status_msg'] = $API_call->message;
                    }
                }
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    public function unschedule_job($page = "details")
    {
        $return_data = [
            'status' => 0
        ];

        $section = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        $true = false;

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin) && $true) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postset 					= $this->input->post();

            if (!empty($postset)) {
                $postdata 		= [];
                $postdata['account_id'] = $this->user->account_id;
                $postdata['job_id'] 	= (!empty($postset['job_id']) ? $postset['job_id'] : false);

                $API_call = $this->webapp_service->api_dispatcher($this->api_end_point.'diary/unschedule_job', $postdata, ['auth_token'=>$this->auth_token]);

                if ((!empty($API_call->status) && ($API_call->status == true))) {
                    $return_data = [
                        'status'			=> $API_call->status,
                        'status_msg'		=> $API_call->message,
                        'unscheduled_job'	=> $API_call->unscheduled_job,
                    ];
                } else {
                    if ((!empty($API_call->message))) {
                        $return_data['status'] = false ;
                        $this->session->set_flashdata('feedback', $API_call->message);
                    }
                }
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    public function progress()
    {
        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            //access denied
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $postdata["account_id"] 		= $this->user->account_id;
            $postdata["limit"] 				= 9999;
            $postdata["where"]["job_date"] 	= date('Y-m-d');

            $API_call			= $this->webapp_service->api_dispatcher($this->api_end_point.'job/jobs', $postdata, ['auth_token'=>$this->auth_token], true);
            $data['all_jobs']	= (!empty($API_call->jobs) ? $API_call->jobs : false);

            $API_call		= $this->webapp_service->api_dispatcher($this->api_end_point.'diary/field_operatives', ['account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true);
            $data['operatives_with_job']	= (isset($API_call->field_operative->with_jobs)) ? $API_call->field_operative->with_jobs : null;
            $data['operatives_without_job']	= (isset($API_call->field_operative->without_jobs)) ? $API_call->field_operative->without_jobs : null;

            $all_user_jobs 	= $data['operatives_with_job'];
            $all_jobs 		= $data['all_jobs'];

            $schedule_context['user_jobs_formatted'] = array();
            foreach ($all_user_jobs as $user_jobs) {
                $user_id 			= $user_jobs->id;
                $user_first_name 	= $user_jobs->first_name;
                $user_last_name 	= $user_jobs->last_name;
                $user_postcode 		= $user_jobs->postcode;
                $user_slots 		= $user_jobs->user_slots;

                $schedule_context['totalJobs'] = array();

                foreach ($user_jobs->assigned_jobs as $assigned_job) {
                    foreach ($all_jobs as $scan_job) {
                        if ($assigned_job->job_id == $scan_job->job_id) {
                            // assuming each job slot is 1 hour.
                            $job_duration 		= $scan_job->job_duration * 1;
                            $job_postcode		= $scan_job->address_postcode;
                            $extend_string 		= "+ " . round($job_duration) . " hours";
                            $job_start_time 	= $assigned_job->start_time;
                            $job_end_time 		= date("Y-m-d H:i:s", strtotime($extend_string, strtotime($job_start_time)));
                            $job_type_desc 		= $scan_job->job_type_desc;

                            array_push(
                                $schedule_context['totalJobs'],
                                array(
                                    'job_start' 		=> $job_start_time,
                                    'job_postcode' 		=> $job_postcode,
                                    'job_end' 			=> $job_end_time,
                                    'job_status_id' 	=> $scan_job->status_id ,
                                    'job_description' 	=> $job_type_desc,
                                    'user_id' 			=> $user_id
                                )
                            );
                        }
                    }
                }
                array_push(
                    $schedule_context['user_jobs_formatted'],
                    array(
                        'user_id' 			=> $user_id,
                        'user_first_name' 	=> $user_first_name,
                        'user_last_name' 	=> $user_last_name,
                        'user_postcode' 	=> $user_postcode,
                        'user_slots' 		=> $user_slots,
                        'user_jobs' 		=> $schedule_context['totalJobs']
                    )
                );
            }

            ## $this->load->view( 'diary/progress', $schedule_context );
            $this->_render_webpage('diary/progress', $schedule_context);
        }
    }


    /** Show List Routed Jobs by Date and Engineer **/
    public function job_progress($job_date = false)
    {
        #$data_source 				= $this->config->item( 'app_path' ).'/modules/webapp/views/job/jobs.json';
        #$context['engineer_data'] 	= file_get_contents( $data_source );

        $job_date = ($this->input->post('job_date')) ? $this->input->post('job_date') : (!empty($job_date) ? $job_date : date('d-m-Y'));
        $params   = [ 'account_id'=>$this->user->account_id, 'where'=>['grouped_by_date'=>1] ];
        if (!empty($job_date)) {
            $params['where']['job_date'] = $job_date;
        }

        $engineer_data		 		= $this->webapp_service->api_dispatcher($this->api_end_point.'diary/routed_jobs', $params, ['auth_token'=>$this->auth_token], true);
        $context['engineer_data'] 	= (isset($engineer_data->jobs)) ? json_encode(( object )[ 'jobs'=>$engineer_data->jobs ]) : null;
        $context['timeline_start'] 	= '';
        $context['timeline_end'] 	= '';
        $this->_render_webpage('diary/job_progress', $context);
    }

    /** View Diary Jobs by All STatuses **/
    public function overview()
    {
        $engineer_data		 	= $this->webapp_service->api_dispatcher($this->api_end_point.'diary/routed_jobs', [ 'account_id'=>$this->user->account_id, 'where'=>['all_statuses'=>1] ], ['auth_token'=>$this->auth_token], true);
        $data['engineer_data'] 	= (isset($engineer_data->jobs)) ? $engineer_data->jobs : null;
        $this->_render_webpage('diary/jobs_overview', $data);
    }

    //Manage Skills - Overview page
    public function manage_skills($skill_id = false, $page = 'details')
    {
        $toggled	= (!empty($this->input->get('toggled')) ? $this->input->get('toggled') : false);
        $section 	= (!empty($page)) ? $page : (!empty($this->input->get('page')) ? $this->input->get('page') : 'details');
        $skill_id  	= (!empty($skill_id)) ? $skill_id : (!empty($this->input->get('skill_id')) ? $this->input->get('skill_id') : ((!empty($this->input->get('skill_id')) ? $this->input->get('skill_id') : null)));

        if (!empty($skill_id)) {
            $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
            if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
                $this->_render_webpage('errors/access-denied', false);
            } else {
                $default_params = $params = [ 'account_id'=>$this->user->account_id, 'where'=>[ 'skill_id'=>$skill_id ] ];
                $skill_details = $this->webapp_service->api_dispatcher($this->api_end_point.'diary/skills', $params, [ 'auth_token'=>$this->auth_token ], true);
                if (!empty($skill_details->skills)) {
                    $data['skill_details']  		= $skill_details->skills;
                    $associated_job_types  		 	= $this->webapp_service->api_dispatcher($this->api_end_point.'diary/associated_job_types', ['account_id'=>$this->user->account_id, 'skill_id'=>$skill_id ], ['auth_token'=>$this->auth_token], true);
                    $data['associated_job_types']	= (isset($associated_job_types->associated_job_types)) ? $associated_job_types->associated_job_types : null;

                    $skilled_people  		 		= $this->webapp_service->api_dispatcher($this->api_end_point.'diary/skilled_people', ['account_id'=>$this->user->account_id, 'skill_id'=>$skill_id ], ['auth_token'=>$this->auth_token], true);
                    $data['skilled_people']			= (isset($skilled_people->skilled_people)) ? $skilled_people->skilled_people : null;

                    $this->_render_webpage('diary/skills/skill_details_profile', $data);
                } else {
                    redirect('webapp/diary/manage_skills', 'refresh');
                }
            }
        } else {
            $this->_render_webpage('diary/skills/manage_skills', false, false, true);
        }
    }

    /*
    * Skills Bank List / Search
    */
    public function skills_bank($page = 'details')
    {
        $return_data = '';

        $section 	 = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            $return_data .= $this->config->item('ajax_access_denied');
        } else {
            # Setup search parameters
            $search_term   = ($this->input->post('search_term')) ? $this->input->post('search_term') : false;
            $where   	   = ($this->input->post('where')) ? $this->input->post('where') : false;
            $limit		   = (!empty($where['limit'])) ? $where['limit'] : DEFAULT_LIMIT;
            $start_index   = ($this->input->post('start_index')) ? $this->input->post('start_index') : DEFAULT_OFFSET;
            $offset		   = (!empty($start_index)) ? (($start_index - 1) * $limit) : 0;
            $order_by	   = ($this->input->post('order_by')) ? $this->input->post('order_by') : false;

            #prepare postdata
            $postdata = [
                'account_id'	=>$this->user->account_id,
                'search_term'	=>$search_term,
                'where'			=>$where,
                'order_by'		=>$order_by,
                'limit'			=>$limit,
                'offset'		=>$offset
            ];

            $search_result	= $this->webapp_service->api_dispatcher($this->api_end_point.'diary/skills', $postdata, [ 'auth_token'=>$this->auth_token ], true);

            $skills		= (isset($search_result->skills)) ? $search_result->skills : null;

            if (!empty($skills)) {
                ## Create pagination
                $counters 		= (isset($search_result->counters)) ? $search_result->counters : null;
                $page_number	= ($start_index > 0) ? $start_index : 1;
                $page_display	= '<span style="margin:15px 0px;" class="pull-left">Page <strong>'.$page_number.'</strong> of <strong>'.(!empty($counters->pages) ? $counters->pages : "").'</strong></span>';

                if (!empty($counters->total) && ($counters->total > 0)) {
                    $config['total_rows'] 	= $counters->total;
                    $config['per_page'] 	= $limit;
                    $config['current_page'] = $page_number;
                    $pagination_setup 		= _pagination_config();
                    $config					= array_merge($config, $pagination_setup);
                    $this->pagination->initialize($config);
                    $pagination 			= $this->pagination->create_links();
                }

                $return_data = $this->load_skills_view($skills);

                if (!empty($pagination)) {
                    $return_data .= '<tr><td colspan="5" style="padding: 0;">';
                    $return_data .= $page_display.$pagination;
                    $return_data .= '</td></tr>';
                }
            } else {
                $return_data .= '<tr><td colspan="5" style="padding: 0 8px;"><br/>';
                $return_data .= (isset($search_result->message)) ? $search_result->message : 'No records found';
                $return_data .= '</td></tr>';
            }
        }

        print_r($return_data);
        die();
    }


    /*
    * Skill bank views
    */
    private function load_skills_view($skills_data)
    {
        $return_data = '';
        if (!empty($skills_data)) {
            foreach ($skills_data as $k => $skill) {
                $return_data .= '<tr>';
                $return_data .= '<td>'.$skill->skill_name.'</td>';
                $return_data .= '<td>'.$skill->skill_description.'</td>';
                $return_data .= '<td>'.$skill->skill_level.'</td>';
                $return_data .= '<td>'.(!empty($skill->is_active) ? 'Active' : 'Disabled').'</td>';
                $return_data .= '<td><span class="pull-right"><a href="'.base_url('/webapp/diary/manage_skills/'.$skill->skill_id).'" ><i class="far fa-edit"></i> Open</a></span></td>';


                $return_data .= '</tr>';
            }

            if (!empty($pagination)) {
                $return_data .= '<tr><td colspan="5" style="padding: 0;">';
                $return_data .= $page_display.$pagination;
                $return_data .= '</td></tr>';
            }
        } else {
            $return_data .= '<tr><td colspan="5"><br/>'.$this->config->item('no_records').'</td></tr>';
        }
        return $return_data;
    }


    /*
    * Add New Skill
    */
    public function new_skill($page = 'details')
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $this->_render_webpage('diary/skills/skill_add_new', $data = false);
        }
    }


    /**
    * Create new Skill
    */
    public function add_skill()
    {
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $text_color  = 'red';
        $return_data = [
            'status'=>0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            ;
        } else {
            $postdata 	= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $new_skill 	= $this->webapp_service->api_dispatcher($this->api_end_point.'diary/add_skill', $postdata, ['auth_token'=>$this->auth_token]);
            $result		= (isset($new_skill->skill)) ? $new_skill->skill : null;
            $message	= (isset($new_skill->message)) ? $new_skill->message : 'Oops! There was an error processing your request.';
            $exists	  	= (!empty($new_skill->exists)) ? $new_skill->exists : false;
            if (!empty($result)) {
                $return_data['status'] 			= 1;
                $return_data['skill'] 			= $result;
                $return_data['already_exists']  = $exists;
                $text_color 					= 'green';
            }
            $return_data['status_msg'] = '<span class="text-'.$text_color.'">'.$message.'</span>';
        }

        print_r(json_encode($return_data));
        die();
    }



    /** Update Skill Profile Details **/
    public function update_skill($skill_id = false, $page = 'details')
    {
        $return_data = [
            'status'=>0
        ];

        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $skill_id = ($this->input->post('skill_id')) ? $this->input->post('skill_id') : (!empty($skill_id) ? $skill_id : null);

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            ;
        } else {
            $postdata 	  = array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $update_skill = $this->webapp_service->api_dispatcher($this->api_end_point.'diary/update_skill', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  = (isset($update_skill->skill)) ? $update_skill->skill : null;
            $message	  = (isset($update_skill->message)) ? $update_skill->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] = 1;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }



    public function scheduler()
    {
        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            //access denied
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $postdata["account_id"] = $this->user->account_id;
            $postdata["limit"] 		= DEFAULT_MAX_LIMIT;
            $postdata["where"]["assigned_to"] = -1;
            ## $postdata["where"]["job_date"] = date( 'Y-m-d' );
            $API_call		= $this->webapp_service->api_dispatcher($this->api_end_point.'job/jobs', $postdata, ['auth_token'=>$this->auth_token], true);
            $data['jobs']	= (!empty($API_call->jobs) ? $API_call->jobs : false);

            $postdata = [];
            $postdata["account_id"] = $this->user->account_id;
            $postdata["limit"] 		= DEFAULT_MAX_LIMIT;
            $API_call				= $this->webapp_service->api_dispatcher($this->api_end_point.'diary/skills', $postdata, ['auth_token'=>$this->auth_token], true);
            $data['skills']			= (!empty($API_call->skills) ? $API_call->skills : false);

            $postdata = [];
            $postdata["account_id"] = $this->user->account_id;
            $API_call				= $this->webapp_service->api_dispatcher($this->api_end_point.'diary/resources_lookup', $postdata, ['auth_token'=>$this->auth_token], true);
            $data['resource']		= (!empty($API_call->resources) ? $API_call->resources : false);

            $this->_render_webpage('diary/scheduler', $data);
        }
    }


    public function resources_lookup()
    {
        $result['return_data'] = '';

        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module-item access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->is_admin && !$module_access) {
            $result['return_data'] = $this->config->item('ajax_access_denied');
        } else {
            $where 			= [];
            $return_data 	= "";

            $post_data = $this->input->post();

            # Setup search parameters
            $search_term   		= (!empty($post_data['search_term'])) ? $post_data['search_term'] : false;
            $limit		   		= (!empty($post_data['limit'])) ? $post_data['limit'] : DEFAULT_LIMIT ;
            $start_index   		= (!empty($post_data['start_index'])) ? ( int ) $post_data['start_index'] : 0;
            $offset		   		= (!empty($start_index)) ? (($start_index - 1) * $limit) : DEFAULT_OFFSET;
            $order_by	   		= false;

            if (!empty($post_data['where']['regions'])) {
                $where['regions'] 	= $post_data['where']['regions'];
            }

            if (!empty($post_data['where']['skills'])) {
                $where['skills'] 	= $post_data['where']['skills'];
            }

            if (!empty($post_data['where']['dates'])) {
                $where['dates'] 	= $post_data['where']['dates'];
            }

            if (!empty($post_data['where']['days'])) {
                $where['days'] 	= $post_data['where']['days'];
            }

            if (!empty($post_data['where']['start_times'])) {
                $where['start_times'] 	= $post_data['where']['start_times'];
            }

            if (!empty($post_data['where']['finish_times'])) {
                $where['finish_times'] 	= $post_data['where']['finish_times'];
            }

            #prepare postdata
            $postdata = [
                'account_id'		=> $this->user->account_id,
                'search_term'		=> urlencode($search_term),
                'where'				=> (!empty($where)) ? urlencode(json_encode($where)) : false ,
                'order_by'			=> $order_by,
                'limit'				=> $limit,
                'offset'			=> $offset
            ];

            $url 				= 'diary/resources_lookup';
            $API_result			= $this->ssid_common->api_call($url, $postdata, $method = 'GET');

            $resources_lookup			= (isset($API_result->resources)) ? $API_result->resources : null;
            $data['available_options']	= (isset($API_result->available_options)) ? $API_result->available_options : null;

            ## dynamic fields
            $counters			= (isset($API_result->counters)) ? $API_result->counters : null;

            if (!empty($counters)) {
                $counters_data	= $this->load_counters_view($counters);
            } else {
                $counters_data	= "<p>No data for counters</p>";
            }

            $message	 				= (isset($API_result->message)) ? $API_result->message : 'Request completed!';

            if (!empty($resources_lookup)) {
                ## Create pagination - Direct access to count, this should only return counters
                $counters 		= $this->diary_service->get_total_resources($this->user->account_id, $search_term, (!empty($postdata['where'])) ? $postdata['where'] : false);
                $page_number	= ($start_index > 0) ? $start_index : 1;
                $page_display	= '<span style="margin:15px 0px;" class="pull-left">Page <strong>'.$page_number.'</strong> of <strong>'.$counters->pages.'</strong></span>';

                if ($counters->total > 0) {
                    $config['total_rows'] 	= $counters->total;
                    $config['per_page'] 	= $limit;
                    $config['current_page'] = $page_number;
                    $pagination_setup 		= _pagination_config();
                    $config					= array_merge($config, $pagination_setup);
                    $this->pagination->initialize($config);
                    $pagination 			= $this->pagination->create_links();
                }

                $return_data = $this->load_resources_view($resources_lookup);

                if (!empty($pagination)) {
                    $return_data .= '<tr><td colspan="8" style="padding: 0;">';
                    $return_data .= $page_display.$pagination;
                    $return_data .= '</td></tr>';
                }
            } else {
                $return_data .= '<br/>';
                $return_data .= (isset($search_result->message)) ? $search_result->message : 'No records found';
            }
        }

        $result['return_data']		= $return_data;
        $result['counters_data']	= $counters_data;

        print_r(json_encode($result));
        die();
    }


    /*
    * 	Prepare resources view
    */
    private function load_resources_view($resources_lookup = false)
    {
        $return_data = '';
        if (!empty($resources_lookup)) {
            foreach ($resources_lookup as $k => $row) {
                $return_data .= '<tr class="single_resource" data-resource_id="'.$row->resource_id.'">';
                $return_data .= '<td width="20%" class="'.$row->user_id.'">'.$row->user_full_name.'</td>';
                $return_data .= '<td width="15%">'.(validate_date($row->ref_date) ? format_date_client($row->ref_date) : '&nbsp;').'</td>';
                $return_data .= '<td width="10%">'.(!empty($row->day) ? $row->day : '&nbsp;').'</td>';
                $return_data .= '<td width="15%">'.(!empty($row->start_time) ? $row->start_time : '&nbsp;').'</td>';
                $return_data .= '<td width="15%">'.(!empty($row->finish_time) ? $row->finish_time : '&nbsp;').'</td>';
                /* $return_data .= '<td width="10%">'.( !empty( $row->base_hours ) ? $row->base_hours : '&nbsp;' ).'</td>'; */
                $return_data .= '<td width="10%" class="text-center" >'.(!empty($row->base_slots) ? ($row->base_slots + 0) : '&nbsp;').'</td>';
                $return_data .= '<td width="10%" class="text-center" >'.(!empty($row->consumed_slots) ? ($row->consumed_slots + 0) : '&nbsp;').'</td>';
                $return_data .= '<td width="10%" class="text-center" >'.(!empty($row->base_slots) ? ($row->base_slots - $row->consumed_slots) : '&nbsp;').'</td>';
                $return_data .= '</tr>';
            }

            if (!empty($pagination)) {
                $return_data .= '<tr><td colspan="8" style="padding: 0;">';
                $return_data .= $page_display.$pagination;
                $return_data .= '</td></tr>';
            }
        } else {
            $return_data .= '<tr><td colspan="8"><br/>'.$this->config->item("no_records").'</td></tr>';
        }
        return $return_data;
    }


    private function load_counters_view($counters_data = false)
    {
        $result = ''; ## returns string

        if (!empty($counters_data)) {
            foreach ($counters_data as $key => $row) {
                $result .= '<div class="row">';
                $result .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
                $result .= '<div class="blue_box '.$key.'">';
                $result .= '<div class="row">';
                $result .= '<legend>'.((!empty($row->heading)) ? $row->heading : '').'</legend>';
                $result .= '</div>';
                $result .= '<div class="row">';
                $result .= '<div class="blue_numbers">'.((!empty($row->numbers)) ? $row->numbers : '').'</div>';
                $result .= '</div>';
                $result .= '</div>';
                $result .= '</div>';
                $result .= '</div>';
            }
        }

        return $result;
    }

    //Manage Regions - Overview page
    public function manage_regions($region_id = false, $page = 'details')
    {
        $toggled	= (!empty($this->input->get('toggled')) ? $this->input->get('toggled') : false);
        $section 	= (!empty($page)) ? $page : (!empty($this->input->get('page')) ? $this->input->get('page') : 'details');
        $region_id  	= (!empty($region_id)) ? $region_id : (!empty($this->input->get('region_id')) ? $this->input->get('region_id') : ((!empty($this->input->get('region_id')) ? $this->input->get('region_id') : null)));

        if (!empty($region_id)) {
            $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
            if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
                $this->_render_webpage('errors/access-denied', false);
            } else {
                $default_params = $params = [ 'account_id'=>$this->user->account_id, 'where'=>[ 'region_id'=>$region_id ] ];
                $region_details = $this->webapp_service->api_dispatcher($this->api_end_point.'diary/regions', $params, [ 'auth_token'=>$this->auth_token ], true);
                if (!empty($region_details->regions)) {
                    $data['region_details']  		= $region_details->regions;
                    $associated_job_types  		 	= $this->webapp_service->api_dispatcher($this->api_end_point.'diary/associated_job_types', ['account_id'=>$this->user->account_id, 'region_id'=>$region_id ], ['auth_token'=>$this->auth_token], true);
                    $data['associated_job_types']	= (isset($associated_job_types->associated_job_types)) ? $associated_job_types->associated_job_types : null;

                    $assigned_operatives  		 	= $this->webapp_service->api_dispatcher($this->api_end_point.'diary/assigned_people', ['account_id'=>$this->user->account_id, 'region_id'=>$region_id ], ['auth_token'=>$this->auth_token], true);
                    $data['assigned_operatives']	= (isset($assigned_operatives->assigned_people)) ? $assigned_operatives->assigned_people : null;

                    $this->_render_webpage('diary/regions/region_details_profile', $data);
                } else {
                    redirect('webapp/diary/manage_regions', 'refresh');
                }
            }
        } else {
            $this->_render_webpage('diary/regions/manage_regions', false, false, true);
        }
    }

    /*
    * Regions List / Search
    */
    public function regions_list($page = 'details')
    {
        $return_data = '';

        $section 	 = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            $return_data .= $this->config->item('ajax_access_denied');
        } else {
            # Setup search parameters
            $search_term   = ($this->input->post('search_term')) ? $this->input->post('search_term') : false;
            $where   	   = ($this->input->post('where')) ? $this->input->post('where') : false;
            $limit		   = (!empty($where['limit'])) ? $where['limit'] : DEFAULT_LIMIT;
            $start_index   = ($this->input->post('start_index')) ? $this->input->post('start_index') : DEFAULT_OFFSET;
            $offset		   = (!empty($start_index)) ? (($start_index - 1) * $limit) : 0;
            $order_by	   = ($this->input->post('order_by')) ? $this->input->post('order_by') : false;

            #prepare postdata
            $postdata = [
                'account_id'	=>$this->user->account_id,
                'search_term'	=>$search_term,
                'where'			=>$where,
                'order_by'		=>$order_by,
                'limit'			=>$limit,
                'offset'		=>$offset
            ];

            $search_result	= $this->webapp_service->api_dispatcher($this->api_end_point.'diary/regions', $postdata, [ 'auth_token'=>$this->auth_token ], true);

            $regions		= (isset($search_result->regions)) ? $search_result->regions : null;

            if (!empty($regions)) {
                ## Create pagination
                $counters 		= (isset($search_result->counters)) ? $search_result->counters : null;
                $page_number	= ($start_index > 0) ? $start_index : 1;
                $page_display	= '<span style="margin:15px 0px;" class="pull-left">Page <strong>'.$page_number.'</strong> of <strong>'.(!empty($counters->pages) ? $counters->pages : "").'</strong></span>';

                if (!empty($counters->total) && ($counters->total > 0)) {
                    $config['total_rows'] 	= $counters->total;
                    $config['per_page'] 	= $limit;
                    $config['current_page'] = $page_number;
                    $pagination_setup 		= _pagination_config();
                    $config					= array_merge($config, $pagination_setup);
                    $this->pagination->initialize($config);
                    $pagination 			= $this->pagination->create_links();
                }

                $return_data = $this->load_regions_view($regions);
                if (!empty($pagination)) {
                    $return_data .= '<tr><td colspan="4" style="padding: 0;">';
                    $return_data .= $page_display.$pagination;
                    $return_data .= '</td></tr>';
                }
            } else {
                $return_data .= '<tr><td colspan="4" style="padding: 0 8px;"><br/>';
                $return_data .= (isset($search_result->message)) ? $search_result->message : 'No records found';
                $return_data .= '</td></tr>';
            }
        }

        print_r($return_data);
        die();
    }

    /*
    * Region list views
    */
    private function load_regions_view($regions_data)
    {
        $return_data = '';
        if (!empty($regions_data)) {
            foreach ($regions_data as $k => $region) {
                $return_data .= '<tr>';
                $return_data .= '<td><a href="'.base_url('/webapp/diary/manage_regions/'.$region->region_id).'" >'.$region->region_name.'</a></td>';
                $return_data .= '<td>'.$region->region_description.'</td>';
                $return_data .= '<td>'.(!empty($region->region_postcodes) ? $region->region_postcodes : '').'</td>';
                $return_data .= '<td>'.(!empty($region->is_active) ? 'Active' : 'Disabled').'</td>';
                $return_data .= '</tr>';
            }

            if (!empty($pagination)) {
                $return_data .= '<tr><td colspan="4" style="padding: 0;">';
                $return_data .= $page_display.$pagination;
                $return_data .= '</td></tr>';
            }
        } else {
            $return_data .= '<tr><td colspan="4"><br/>'.$this->config->item('no_records').'</td></tr>';
        }
        return $return_data;
    }


    /*
    * Add New Region
    */
    public function new_region($page = 'details')
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            // $data['region_postcodes'] = array_to_object( [[
            // "postcode_area"=> "CM",
            // "postcode_district"=> "CM6",
            // "posttown"=> "Warlingham",
            // "county"=> "Surrey"
            // ],
            // [
            // "postcode_area"=> "CR",
            // "postcode_district"=> "CR0",
            // "posttown"=> "Croydon",
            // "county"=> "Surrey"
            // ]] );

            $this->_render_webpage('diary/regions/region_add_new', $data = false);
        }
    }


    /**
    * Create new Region
    */
    public function add_region()
    {
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $text_color  = 'red';
        $return_data = [
            'status'=>0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            ;
        } else {
            $postdata 	= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $new_region 	= $this->webapp_service->api_dispatcher($this->api_end_point.'diary/add_region', $postdata, ['auth_token'=>$this->auth_token]);
            $result		= (isset($new_region->region)) ? $new_region->region : null;
            $message	= (isset($new_region->message)) ? $new_region->message : 'Oops! There was an error processing your request.';
            $exists	  	= (!empty($new_region->exists)) ? $new_region->exists : false;
            if (!empty($result)) {
                $return_data['status'] 			= 1;
                $return_data['region'] 			= $result;
                $return_data['already_exists']  = $exists;
                $text_color 					= 'green';
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }



    /** Update Region Profile Details **/
    public function update_region($region_id = false, $page = 'details')
    {
        $return_data = [
            'status'=>0
        ];

        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $region_id = ($this->input->post('region_id')) ? $this->input->post('region_id') : (!empty($region_id) ? $region_id : null);

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            ;
        } else {
            $postdata 	  = array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $update_region= $this->webapp_service->api_dispatcher($this->api_end_point.'diary/update_region', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  = (isset($update_region->region)) ? $update_region->region : null;
            $message	  = (isset($update_region->message)) ? $update_region->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] = 1;
                $return_data['region'] = $result;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    /** Search addresses for Postcode coverage**/
    public function search_postcode_areas($page = 'details')
    {
        $return_data = [];

        if (!$this->identity()) {
            $return_data .= 'Access denied! Please login';
        }

        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);
        if (!$this->user->is_admin && !$module_access) {
            $return_data .= $this->config->item('ajax_access_denied');
        } else {
            # Setup search parameters
            $view_type 	   = 'overview';
            $search_term   = ($this->input->post('search_term')) ? $this->input->post('search_term') : false;
            $limit		   = ($this->input->post('limit')) ? $this->input->post('limit') : -1;
            $start_index   = ($this->input->post('start_index')) ? $this->input->post('start_index') : 0;
            $offset		   = (!empty($start_index)) ? (($start_index - 1) * $limit) : 0;
            $order_by	   = ($this->input->post('order_by')) ? $this->input->post('order_by') : false;
            ;
            $where		   = [];

            #prepare postdata
            $postdata = [
                'account_id'=>$this->user->account_id,
                'search_term'=>$search_term,
                'where'=>$where,
                'order_by'=>$order_by,
                'limit'=>$limit,
                'offset'=>$offset
            ];

            $search_result	 = $this->webapp_service->api_dispatcher($this->api_end_point.'diary/address_regions', $postdata, ['auth_token'=>$this->auth_token], true);
            $postcode_areas	 = (isset($search_result->postcode_areas)) ? $search_result->postcode_areas : null;
            if (!empty($postcode_areas)) {
                $return_data['postcode_areas'] = $this->load_regions_postcodes_view($postcode_areas);
            } else {
                $return_data['postcode_areas'] = '<div class="col-xs-12">';
                $return_data['postcode_areas'] .= '<span class="text-red" >There\'s currently no data matching your criteria.</span>&nbsp;&nbsp;';
                #$return_data['postcode_areas'] .= '<span title="Click to add a new entry if not listed" class="add-new-address-region pointer"><i class="fas fa-plus text-green" ></i> Add New</span>';
                $return_data['postcode_areas'] .= '</div>';
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    /*
    * Region postcode selection view
    */
    private function load_regions_postcodes_view($region_postcode_data)
    {
        $return_data = '';
        if (!empty($region_postcode_data)) {
            if (count($region_postcode_data) > 2) {
                $return_data .= '<div class="col-md-2 col-md-4 col-xs-6"><label class="pointer">';
                $return_data .='<input type="checkbox" id="check-all-postcodes" /> All';
                $return_data .= '</label></div>';
            }

            foreach ($region_postcode_data as $k => $postcode_data) {
                $return_data .= '<div class="col-md-2 col-md-4 col-xs-6"><label class="pointer">';
                $return_data .='<input type="checkbox" class="styled-checkbox postcode-checks" name="region_postcodes[]" value="'.$postcode_data->postcode_district.'" /> '.$postcode_data->postcode_district;
                $return_data .= '</label></div>';
            }

            $return_data .= '<div class="col-md-2 col-md-4 col-xs-6"><label class="pointer">';
            #$return_data .='<span class="add-new-address-region"><i class="fas fa-plus text-green" title="Add a new entry if not listed" ></i> Add New</span>';
            $return_data .= '</label></div>';
        } else {
            $return_data .= '<div class="col-xs-12">There\'s currently no options to select from.</div>';
        }
        return $return_data;
    }


    /**
    * Assign a person to a people
    **/
    public function assign_person()
    {
        $section 	 = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
        $text_color  = 'red';
        $return_data = [
            'status'=>0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postdata 	  	 = array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $assigned_people = $this->webapp_service->api_dispatcher($this->api_end_point.'people/assign_peoples', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  	 = (isset($assigned_people->assigned_people)) ? $assigned_people->assigned_people : null;
            $message	  	 = (isset($assigned_people->message)) ? $assigned_people->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] 	 = 1;
                $text_color 			 = 'green';
            }
            $return_data['status_msg'] = '<span class="text-'.$text_color.'">'.$message.'</span>';
        }

        print_r(json_encode($return_data));
        die();
    }


    /**
    * Un-assign People
    **/
    public function remove_skilled_person($people_id = false)
    {
        $return_data = [
            'status'=>0
        ];

        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && !$item_access && empty($item_access->can_delete)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            ;
        } else {
            $postdata 		= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $unlink_person	= $this->webapp_service->api_dispatcher($this->api_end_point.'diary/remove_skilled_people', $postdata, ['auth_token'=>$this->auth_token]);
            $result			= (isset($unlink_person->status)) ? $unlink_person->status : null;
            $message		= (isset($unlink_person->message)) ? $unlink_person->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] = 1;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }

    /** Resource planning and Routing **/
    public function routing($page = 'details')
    {
        $section 	 = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $date_from		 	= (!empty($this->input->post('date_from'))) ? $this->input->post('date_from') : date('Y-m-d');
            $date_to		 	= (!empty($this->input->post('date_to'))) ? $this->input->post('date_to') : date('Y-m-d', strtotime($date_from.' + 4 weeks'));

            $data["range"] 		= array("date_to" => $date_to, "date_from" => $date_from);

            $postdata 			= array_merge([ 'account_id'=>$this->user->account_id, 'where'=>['pool_jobs'=>1] ], $this->input->post());
            $job_id	= $data['job_id'] = (!empty($this->input->get('job'))) ? $this->input->get('job') : (!empty($this->input->get('job')) ? $this->input->get('job') : false);

            if (!empty($job_id)) {
                $postdata['job_id'] 		= $job_id;
                $un_booked_jobs 			= $this->webapp_service->api_dispatcher($this->api_end_point.'job/jobs', $postdata, ['auth_token'=>$this->auth_token], true);
                $data['job_details'] 		= !empty($un_booked_jobs->jobs) ? $un_booked_jobs->jobs : false;

                if (!empty($data['job_details'])) {
                    $data['un_booked_jobs'] = !empty($un_booked_jobs->jobs) ? $un_booked_jobs->jobs : false;
                    $params = array_merge(
                        [ 'account_id'=>$this->user->account_id, 'where'=>[
                            'date_from'			=> $date_from,
                            'date_to'			=> $date_to,
                            'job_type_id'		=> $data['job_details']->job_type_id,
                            'region_id'			=> $data['job_details']->region_id,
                            'weekly_view'		=> true
                        ]],
                        $this->input->post()
                    );

                    if ($this->user->is_primary_user && !$this->user->is_admin) {
                        $params['where']['associated_user_id']     = $this->user->id;
                    }

                    $api_call = $this->webapp_service->api_dispatcher($this->api_end_point.'diary/available_resource', $params, ['auth_token'=>$this->auth_token], true);
                    $data['available_resource'] = !empty($api_call->available_resource) ? $api_call->available_resource : false;
                } else {
                    #redirect( 'webapp/diary/routing', 'refresh' );
                    redirect('webapp/job/jobs', 'refresh');
                }
            } else {
                $un_booked_jobs 		= $this->webapp_service->api_dispatcher($this->api_end_point.'job/lookup', $postdata, ['auth_token'=>$this->auth_token], true);
                $data['un_booked_jobs'] = !empty($un_booked_jobs->jobs) ? $un_booked_jobs->jobs : false;
                $data['page_counters']  = !empty($un_booked_jobs->counters) ? $un_booked_jobs->counters : false;
            }

            $this->_render_webpage('diary/resource/routing', $data);
        }
    }

    /*
    * 	Add postcode districts to a region
    **/
    public function add_region_postcodes()
    {
        $section 	 = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
        $text_color  = 'red';
        $return_data = [
            'status'=>0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postdata 	  	 = array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $region_postcodes = $this->webapp_service->api_dispatcher($this->api_end_point.'diary/add_region_postcodes', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  	 = (isset($region_postcodes->region_postcodes)) ? $region_postcodes->region_postcodes : null;
            $message	  	 = (isset($region_postcodes->message)) ? $region_postcodes->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] 	 = 1;
                $return_data['regions']  = $result;
                $text_color 			 = '';
            }
            $return_data['status_msg'] = '<span class="text-'.$text_color.'">'.$message.'</span>';
        }

        print_r(json_encode($return_data));
        die();
    }


    /**
    * Remove Postcode area from region
    **/
    public function remove_region_postcodes($postcode_district = false)
    {
        $return_data = [
            'status'=>0
        ];

        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && !$item_access && empty($item_access->can_delete)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            ;
        } else {
            $postdata 					= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $remove_region_postcodes	= $this->webapp_service->api_dispatcher($this->api_end_point.'diary/remove_region_postcodes', $postdata, ['auth_token'=>$this->auth_token]);
            $result						= (isset($remove_region_postcodes->status)) ? $remove_region_postcodes->status : null;
            $message					= (isset($remove_region_postcodes->message)) ? $remove_region_postcodes->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] = 1;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    /**
    * 	Get Single resource details
    **/
    public function get_resource($page = "details")
    {
        $return_data = [
            'status' => 0,
        ];

        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && !$item_access && empty($item_access->can_view)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data['resource_id'])) {
                $postdata['where']['resource_id']		= $post_data['resource_id'] ;
                $postdata['account_id'] 				= $this->user->account_id;
                $API_call								= $this->webapp_service->api_dispatcher($this->api_end_point.'diary/diary_resource', $postdata, ['auth_token'=>$this->auth_token], true);
                $return_data['resource']				= (isset($API_call->diary_resource)) ? $this->load_resource_view($API_call->diary_resource[0]) : null;
                $return_data['status']					= (isset($API_call->status)) ? $API_call->status : null;
                $return_data['status_msg']				= (isset($API_call->message)) ? $API_call->message : 'Oops! There was an error processing your request.';
            } else {
                $return_data['status_msg'] = "Missing Resource ID";
            }
        }

        print_r(json_encode($return_data));
        die();
    }



    /*
    *	Single Resource view
    */
    private function load_resource_view($resource_data = false)
    {
        $return_data = '';
        if (!empty($resource_data)) {
            $ops_params = [
                'account_id'=>$this->user->account_id,
                'where'		=>['include_admins'=>1],
                'limit'		=>-1
            ];
            ## Apply Primary User conditions
            if ($this->user->is_primary_user && !$this->user->is_admin) {
                $ops_params['where']['associated_user_id'] 	= $this->user->id;
            }
            $API_Call		= $this->webapp_service->api_dispatcher($this->api_end_point.'user/field_operatives', $ops_params, ['auth_token'=>$this->auth_token], true);
            $operatives   	= (isset($API_Call->field_operatives)) ? $API_Call->field_operatives : null;

            $return_data = '';

            $return_data .= '<form id="resource_update_in_modal">';
            $return_data .= '<input type="hidden" name="resource_id" value="'.$resource_data->resource_id.'" />';

            $return_data .= '<div style="width:100%;">';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Field Operative</label>';
            $return_data .= '<select name="user_id" class="form-control" required><option value="">Please select</option>';

            if (!empty($operatives)) {
                foreach ($operatives as $row) {
                    $return_data .= '<option value="'.($row->id).'" ';
                    if (!empty($resource_data->user_id) && ($resource_data->user_id == $row->id)) {
                        $return_data .= 'selected="selected"';
                    }
                    $return_data .='>'.($row->first_name." ".$row->last_name).'</option>';
                }
            }
            $return_data .= '</select>';
            $return_data .= '</div>';


            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Date</label>';
            $return_data .= '<input class="form-control datetimepicker reference_date" placeholder="Date" type="text" name="ref_date" value="'.(!empty($resource_data->ref_date) ? $resource_data->ref_date : '').'" required="required" />';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Day</label>';
            $return_data .= '<input class="form-control day-field" placeholder="Day" type="text" name="day" value="'.(!empty($resource_data->day) ? ($resource_data->day) : '').'" required="required" readonly />';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Start Time</label>';
            $return_data .= '<input class="form-control timepicker" placeholder="Start Time" type="text" name="start_time" value="'.(!empty($resource_data->start_time) ? date('H:i', strtotime($resource_data->start_time)) : '').'" required="required" />';

            ## format:'H:i'

            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Finish Time</label>';
            $return_data .= '<input class="form-control timepicker" placeholder="Finish Time" type="text" name="finish_time" value="'.(!empty($resource_data->finish_time) ? date('H:i', strtotime($resource_data->finish_time)) : '').'" required="required" />';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group hide">';
            $return_data .= '<label class="input-group-addon">Lunch Allowance</label>';
            $return_data .= '<input class="form-control timepicker" placeholder="Lunch Allowance" type="text" name="lunch_allowance" value="'.(!empty($resource_data->lunch_allowance) ? ($resource_data->lunch_allowance) : '').'" required="required" />';
            $return_data .= '</div>';

            /* 			$return_data .= '<div class="input-group form-group hide">';
                        $return_data .= '<label class="input-group-addon">Break Allowance</label>';
                        $return_data .= '<input class="form-control timepicker" placeholder="Break Allowance" type="text" name="break_allowance" value="'.( !empty( $resource_data->break_allowance ) ? ( $resource_data->break_allowance ) : '' ).'" required="required" />';
                        $return_data .= '</div>';

                        $return_data .= '<div class="input-group form-group hide">';
                        $return_data .= '<label class="input-group-addon">Break Allowance</label>';
                        $return_data .= '<input class="form-control timepicker" placeholder="Break Allowance" type="text" name="break_allowance" value="'.( !empty( $resource_data->break_allowance ) ? ( $resource_data->break_allowance ) : '' ).'" required="required" />';
                        $return_data .= '</div>'; */

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Note</label>';
            $return_data .= '<textarea name="notes" class="form-control" placeholder="Note" rows="3">'.(!empty($resource_data->notes) ? ( string ) $resource_data->notes : '').'</textarea>';
            $return_data .= '</div>';

            $return_data .= '</div><div class="row">';

            $item_access = $this->webapp_service->check_access($this->user, $this->module_id, "positions");

            $return_data .= '<div class="col-md-6 pull-right">';
            if ($this->user->is_admin || !empty($item_access->can_edit) || !empty($item_access->is_admin)) {
                $return_data .= '<button id="updateResource" class="btn-success btn-next btn btn-sm btn-block btn-flow margin_top_8" type="submit">Update Resource</button>';
            } else {
                $return_data .= '<button class="btn-success btn btn-sm btn-flow btn-success btn-next submit no-permissions" disabled style="width: 100%;margin-top: 10px;">No Permissions</button>';
            }
            $return_data .= '</div>';

            $return_data .= '<div class="col-md-6 pull-right">';
            if ($this->user->is_admin || !empty($item_access->can_delete) || !empty($item_access->is_admin)) {
                $return_data .= '<button id="deleteResource" data-resource_id="'.$resource_data->resource_id.'" class="btn-danger btn-next btn btn-sm btn-block btn-flow margin_top_8" type="button">Delete Resource</button>';
            } else {
                $return_data .= '<button class="btn-danger btn btn-sm btn-flow btn-success btn-next submit no-permissions" disabled style="width: 100%;margin-top: 10px;">No Permissions</button>';
            }
            $return_data .= '</div>';
            $return_data .= '</div>';

            $return_data .= '</div>';
            $return_data .= '</form>';
        } else {
            $return_data .= '<div width="100%">';
            $return_data .= '<div><div colspan="2">'.$this->config->item("no_data").'</div></div>';
            $return_data .= '</div>';
        }

        return $return_data;
    }

    /*
    *	Update a single diary resource
    */
    public function update_resource($page = 'details')
    {
        $return_data = [
            'status' => 0
        ];

        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            ;
        } else {
            $post_data = ($this->input->post()) ? ($this->input->post()) : null ;

            if (!empty($post_data) && !empty($post_data['resource_id'])) {
                $postdata 	= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
                $API_call 	= $this->webapp_service->api_dispatcher($this->api_end_point.'diary/update_resource', $postdata, ['auth_token'=>$this->auth_token]);
                $result		  = (isset($API_call->diary_resource)) ? $API_call->diary_resource : null;
                $message	  = (isset($API_call->message)) ? $API_call->message : 'Oops! There was an error processing your request.';
                if (!empty($result)) {
                    $return_data['status'] = 1;
                }
                $return_data['status_msg'] = $message;
            } else {
                $return_data['status_msg'] = "Required data is missing";
            }
        }

        print_r(json_encode($return_data));
        die();
    }



    /**
    * 	Delete resource
    **/
    public function delete_resource($resource_id = false, $page = "details")
    {
        $return_data = [
            'status' => 0
        ];

        $section 		= ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_delete) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $resource_id 	= ($this->input->post('resource_id')) ? $this->input->post('resource_id') : (!empty($resource_id) ? $resource_id : null);
            if (!empty($resource_id)) {
                $postdata 	= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
                $API_call 	= $this->webapp_service->api_dispatcher($this->api_end_point.'diary/delete_resource', $postdata, ['auth_token'=>$this->auth_token]);
                $result		= (isset($API_call->status)) ? $API_call->status : null;
                $message	= (isset($API_call->message)) ? $API_call->message : 'Oops! There was an error processing your request.';
                if (!empty($result)) {
                    $return_data['status']= 1;
                }
                $return_data['status_msg'] = $message;
            } else {
                $return_data['status_msg'] = "Resource ID is a required";
            }
        }

        print_r(json_encode($return_data));
        die();
    }
}
