<?php

namespace App\Controllers\Web;

use App\Extensions\MX\Controller as MX_Controller;
class Project extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->module_id 	   = $this->webapp_service->_get_module_id($this->router->fetch_class());
        $this->load->library('pagination');
        $this->load->model('serviceapp/project_model', 'project_service');
        $this->load->model('serviceapp/site_model', 'site_service');
        $this->load->model('serviceapp/asset_model', 'asset_service');

        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }
    }

    /**
    * Index file
    **/
    public function index()
    {
        $this->projects('details');
    }

    /*
    *	The main Index page
    */
    public function stats($project_id = false, $page = "details")
    {
        if (!empty($project_id)) {
            $section = (!empty($page)) ? $page : $this->router->fetch_method();

            # Check module-item access
            $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
            if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
                $this->_render_webpage('errors/access-denied', false);
            } else {
                $postdata 	  				 = array('account_id'=>$this->user->account_id, 'project_id' => $project_id);
                $data["project_information"] = $this->webapp_service->api_dispatcher($this->api_end_point.'project/projects', $postdata, $this->options, true);

                if ($data["project_information"]) {
                    $postdata 	  		= array('account_id'=>$this->user->account_id, "stat_type" => "audit_result_status");
                    $data["building_compliance"]	= $this->webapp_service->api_dispatcher($this->api_end_point.'site/site_stats', $postdata, $this->options, true);


                    $postdata 	  	= array( 'account_id'=>$this->user->account_id, 'stat_type'=>'periodic_audits' );
                    $data["audit_stats"]	= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/audit_stats', $postdata, $this->options, true);


                    $postdata 	  		= array( 'account_id'=>$this->user->account_id, "stat_type" =>"replace_cost"  );
                    $eol_replacement	= $this->webapp_service->api_dispatcher($this->api_end_point.'asset/asset_stats', $postdata, $this->options, true);
                    $data["replacement_cost"] = ($eol_replacement->status) ? array("replacement_cost" => $eol_replacement->asset_stats->replacement_cost, "status" => $eol_replacement->status) : array(null, "status" => "false");


                    $postdata 	  		= array( 'account_id'=>$this->user->account_id, "stat_type" =>"eol"  );
                    $data['eol_full_replacement']	= $this->webapp_service->api_dispatcher($this->api_end_point.'asset/asset_stats', $postdata, $this->options, true);


                    $this->_render_webpage('project/project_stats', $data);
                } else {
                    redirect('webapp/project/projects', 'refresh');
                }
            }
        } else {
            redirect('webapp/project/projects', 'refresh');
        }
    }


    /*
    *	Function to show the projects - get most recent Project Profiles
    */
    public function projects($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $project_types		 		= $this->webapp_service->api_dispatcher($this->api_end_point.'project/project_types', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true);
            $data['project_types'] 		= (isset($project_types->project_types)) ? $project_types->project_types : null;

            $project_statuses		 	= $this->webapp_service->api_dispatcher($this->api_end_point.'project/project_statuses', ['account_id'=>false], ['auth_token'=>$this->auth_token], true);
            $data['project_statuses'] 	= (isset($project_statuses->project_statuses)) ? $project_statuses->project_statuses : null;

            $data['project_stats'] 		= [];
            $this->_render_webpage('project/index', $data);
        }
    }

    /**  Search through Projects **/
    public function projects_lookup($page = 'projects')
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
                'account_id'	=> $this->user->account_id,
                'search_term'	=> $search_term,
                'where'			=> $where,
                'order_by'		=> $order_by,
                'limit'			=> $limit,
                'offset'		=> $offset
            ];

            $search_result		= $this->webapp_service->api_dispatcher($this->api_end_point.'project/projects', $postdata, [ 'auth_token'=>$this->auth_token ], true);

            $projects	= (isset($search_result->projects)) ? $search_result->projects : null;

            if (!empty($projects)) {
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

                $return_data = $this->load_projects_view($projects);
                if (!empty($pagination)) {
                    $return_data .= '<tr><td colspan="8" style="padding: 0;">';
                    $return_data .= $page_display.$pagination;
                    $return_data .= '</td></tr>';
                }
            } else {
                $return_data .= '<tr><td colspan="8" style="padding: 0 8px;"><br/>';
                $return_data .= (isset($search_result->message)) ? $search_result->message : 'No records found';
                $return_data .= '</td></tr>';
            }
        }

        print_r($return_data);
        die();
    }

    /*
    * Prepare Projects views
    */
    private function load_projects_view($projects_data)
    {
        $return_data = '';
        if (!empty($projects_data)) {
            foreach ($projects_data as $k => $project_details) {
                $return_data .= '<tr>';
                $return_data .= '<td><a href="'.base_url('/webapp/project/profile/'.$project_details->project_id).'" >'.ucwords($project_details->project_name).'</a></td>';
                $return_data .= '<td>'.$project_details->project_type.'</td>';
                $return_data .= '<td>'.(valid_date($project_details->project_start_date) ? date('d.m.Y', strtotime($project_details->project_start_date)) : '').'</td>';
                $return_data .= '<td>'.(valid_date($project_details->project_finish_date) ? date('d.m.Y', strtotime($project_details->project_finish_date)) : '').'</td>';
                $return_data .= '<td>'.strtoupper($project_details->project_ref).'</td>';
                $return_data .= '<td>'.$project_details->project_status.'</td>';
                $return_data .= '<td>'.$project_details->project_lead_name.'</td>';
                $return_data .= '<td>'.$project_details->ownership.'</td>';
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

    /** Create new project **/
    public function new_project($page = 'details')
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $message 				= ($this->session->flashdata('message')) ? $this->session->flashdata('message') : null;
            $data['message']		= $message;

            $this->_render_webpage('project/project_create_new', $data);
        }
    }

    /**
    * Do Project Creation
    */
    public function create_project()
    {
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $return_data = [
            'status'=>0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'details');

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            ;
        } else {
            $postdata 	  = array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $project	  = $this->webapp_service->api_dispatcher($this->api_end_point.'project/create', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  = (isset($project->project)) ? $project->project : null;
            $message	  = (isset($project->message)) ? $project->message : 'Oops! There was an error processing your request.';

            if (!empty($result)) {
                $return_data['status'] 	= 1;
                $return_data['project'] = $result;
            }
            $return_data['status_msg'] 	= $message;
        }

        print_r(json_encode($return_data));
        die();
    }



    /*
    *	To show the Project Profile data.
    */
    public function profile($project_id = false, $page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } elseif (!empty($project_id)) {
            $project_details		   	= $this->webapp_service->api_dispatcher($this->api_end_point.'project/projects', ['account_id'=>$this->user->account_id,'project_id'=>$project_id], ['auth_token'=>$this->auth_token], true);
            $data['project_details'] 	= (isset($project_details->projects)) ? $project_details->projects : null;

            if (!empty($data['project_details'])) {
                $run_admin_check 	= false;
                #Get allowed access for the logged in user
                $data['permissions']= $item_access;
                $data['active_tab']	= $page;

                $module_items 			 = $this->webapp_service->api_dispatcher($this->api_end_point.'access/account_modules_items', ['account_id'=>$this->user->account_id, 'module_id'=>$this->module_id ], ['auth_token'=>$this->auth_token], true);
                $data['module_tabs']	 = (isset($module_items->module_items)) ? $module_items->module_items : null;
                $reordered_tabs 		 = reorder_tabs($data['module_tabs']);
                $data['module_tabs'] 	 = (!empty($reordered_tabs['module_tabs'])) ? $reordered_tabs['module_tabs'] : $data['module_tabs'];
                $data['more_list_active']= (!empty($reordered_tabs['more_list']) && in_array($page, $reordered_tabs['more_list'])) ? true : false;

                $project_types		 	  = $this->webapp_service->api_dispatcher($this->api_end_point.'project/project_types', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true);
                $data['project_types'] 	  = (isset($project_types->project_types)) ? $project_types->project_types : null;


                $project_statuses		  = $this->webapp_service->api_dispatcher($this->api_end_point.'project/project_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true);
                $data['project_statuses'] = (isset($project_statuses->project_statuses)) ? $project_statuses->project_statuses : null;

                $operatives		  	  = $this->webapp_service->api_dispatcher($this->api_end_point.'user/users', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true);
                $data['operatives']   = (isset($operatives->users)) ? $operatives->users : null;

                switch($page) {
                    case 'actions':
                    case 'project_actions':
                        $proj_actions	= $this->webapp_service->api_dispatcher($this->api_end_point.'project/project_actions', ['account_id'=>$this->user->account_id, 'project_id' => $project_id], ['auth_token'=>$this->auth_token], true);
                        $data['project_actions'] = (isset($proj_actions->project_actions) ? $proj_actions->project_actions : null);
                        $data['project_id'] = $project_id;
                        $data['include_page'] = 'project_actions.php';
                        break;

                    case 'workflows':
                    case 'project_workflows':
                        $data['pagination_enabled'] = false;
                        $data['include_page'] = 'project_workflows.php';
                        break;

                    case 'details':
                    default:

                        $data['include_page'] = 'project_details.php';
                        break;
                }
            }

            //Run the admin check if tab needs only admin
            if (!empty($run_admin_check)) {
                if ((!admin_check($this->user->is_admin, false, (!empty($data['permissions']) ? $data['permissions']->is_admin : false)))) {
                    $data['admin_no_access'] = true;
                }
            }
            $this->_render_webpage('project/profile', $data);
        } else {
            redirect('webapp/project/dashboard', 'refresh');
        }
    }


    /**
    * Update Project Profile Details
    **/
    public function update_project($project_id = false, $page = 'details')
    {
        $return_data = [
            'status'=>0
        ];

        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $project_id = ($this->input->post('project_id')) ? $this->input->post('project_id') : (!empty($project_id) ? $project_id : null);

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        ## Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            ;
        } else {
            $postdata 	  = array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $update_project = $this->webapp_service->api_dispatcher($this->api_end_point.'project/update', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  = (isset($update_project->project)) ? $update_project->project : null;
            $message	  = (isset($update_project->message)) ? $update_project->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] = 1;
                $return_data['project']   = $update_project;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    /*
    *	To delete the Project Profile
    */
    public function delete_project()
    {
        $return_data = [
            'status' => 0
        ];

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'details');
        if (!$this->user->is_admin && empty($item_access->can_delete) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postdata 			= $this->input->post('postdata');

            if (!empty($postdata['account_id']) && !empty($postdata['project_id'])) {
                $url 						= 'project/delete';
                $API_result					= $this->ssid_common->api_call($url, $postdata, $method = 'POST');
                $return_data['status'] 		= (!empty($API_result->status) && ($API_result->status == true)) ? $API_result->status : 0 ;
            }

            $return_data['project'] 		= (!empty($API_result->project)) ? $API_result->project : null ;
            $return_data['status_msg'] 		= (!empty($API_result->message)) ? $API_result->message : 'Oops! There was an error processing your request.';
            ;
        }

        print_r(json_encode($return_data));
        die();
    }

    /*
    *	Function to create Project Action
    */
    public function create_project_action()
    {
        $return_data = ['status' => 0, 'message' => 'An unknown error has occured!'];

        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'project_actions');

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['message'] = "User does not have access!";
        } else {
            $postdata 	  		= array_merge(array('account_id' => $this->user->account_id), $this->input->post());
            $create_pa	= $this->webapp_service->api_dispatcher($this->api_end_point.'project/create_project_action', $postdata, $this->options, false);

            $result      = (!empty($create_pa->project_action)) ? $create_pa->project_action : null;
            $return_data['message']	 = (!empty($create_pa->message)) ? $create_pa->message : 'Oops! There was an error processing your request.';

            if (!empty($result)) {
                $return_data['status'] = 1;
                $return_data['project_action_result'] = $result;
            }
        }
        print_r(json_encode($return_data));
        die();
    }

    public function archive_project_action()
    {
        $return_data = ['status' => 0, 'message' => 'An unknown error has occurred!'];

        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'project_actions');

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['message'] = "User does not have access!";
        } else {
            $postdata   = array_merge(array('account_id' => $this->user->account_id, 'is_active' => 0), $this->input->post());
            $update_action	= $this->webapp_service->api_dispatcher($this->api_end_point.'project/update_project_action', $postdata, $this->options, false);
            $return_data = !empty($update_action) ? $update_action : false;
        }

        print_r(json_encode($return_data));
        die();
    }


    /*
    *	Function to create a new Project Worflow
    */
    public function add_workflow()
    {
        $data['active_class'] 		= 'add_project';
        $data['feedback'] 			= !empty($this->session->flashdata('feedback')) ? ($this->session->flashdata('feedback')) : false ;
        $postset 					= $this->input->post('postdata');

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'workflow_items');
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            if (!empty($postset)) {
                $postdata = [];
                $postdata = $postset;
                $postdata["account_id"]		= $postset['account_id'];
                unset($postset['account_id']);
                $postdata["project_id"]	= $postset['project_id'];
                unset($postset['project_id']);

                $url 						= 'project/add_workflow';
                $API_result					= $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if ((!empty($API_result->new_workflow))) {
                    if ((!empty($API_result->message))) {
                        $this->session->set_flashdata('feedback', $API_result->message);
                    }
                    redirect('webapp/project/profile/'.$postdata['project_id'].'/workflow_items', 'refresh');
                } else {
                    if ((!empty($API_result->message))) {
                        $this->session->set_flashdata('feedback', $API_result->message);
                    }
                    redirect('webapp/project/profile/'.$postdata['project_id'].'/workflow_items', 'refresh');
                }
            }

            redirect('webapp/project/dashboard', 'refresh');
        }
    }


    /*
    *	To update the batch of Project Profile data.
    */
    public function batch_update_workflow()
    {
        $post_data 				= $this->input->post('postdata');

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'workflow_items');
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            if (!empty($post_data['account_id']) && !empty($post_data['batch_update'])) {
                $postdata 	= false;
                $url 		= 'project/batch_workflow_update';

                foreach ($post_data['batch_update'] as $key => $row) {
                    if (!empty($row['check']) && (strtolower($row['check'])  == 'yes')) {
                        $postdata['batch_update_data'][$key] = $row;
                    }
                }

                if (!empty($postdata['batch_update_data'])) {
                    $postdata['account_id']		= $post_data['account_id'];
                    $postdata['batch_update']	= json_encode($postdata['batch_update_data']);

                    $updated_workflows			= $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                    if ((!empty($updated_workflows->status)) && ($updated_workflows->status == 1)) {
                        $this->session->set_flashdata('feedback', (!empty($updated_workflows->message)) ? ($updated_workflows->message) : "The Workflow Items have been updated successfully.");
                    } else {
                        $this->session->set_flashdata('feedback', (!empty($updated_workflows->message)) ? ($updated_workflows->message) : "The Workflow Items hasn't been updated.");
                    }
                } else {
                    $this->session->set_flashdata('feedback', (!empty($updated_workflows->message)) ? ($updated_workflows->message) : "You need to pick Workflow Items to do an update.");
                }

                redirect('webapp/project/profile/'.$post_data['project_id'].'/workflow_items', 'refresh');
            } else {
                redirect('webapp/project/dashboard/', 'refresh');
            }
        }
    }


    /*
    *	To delete the Project Profile
    */
    public function delete_project_action($project_action_id = false)
    {
        $account_id 			= $this->user->account_id;
        $project_id			= $this->uri->segment(5, 0);
        $referring_page			= $this->uri->segment(6, 0);

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'workflow_items');
        if (!$this->user->is_admin && empty($item_access->can_delete) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            if (!empty($project_action_id) && !empty($account_id)) {
                $postdata['workflow_id']	= $project_action_id;
                $postdata['account_id']		= $account_id;
                $url 						= 'project/delete_workflow';

                $deleted_project_action					= $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if ((!empty($deleted_project_action->status)) && ($deleted_project_action->status == 1)) {
                    $this->session->set_flashdata('feedback', (!empty($deleted_project_action->message)) ? ($deleted_project_action->message) : "The Workflow Item has been deleted successfully.");
                } else {
                    $this->session->set_flashdata('feedback', (!empty($deleted_project_action->message)) ? ($deleted_project_action->message) : "The Workflow Item has NOT been deleted.");
                }
            }

            redirect('webapp/project/profile/'.$project_id.'/'.$referring_page, 'refresh');
        }
    }


    /*
    *	To link Sites to the specific Project
    */
    public function link_sites()
    {
        $account_id 			= $this->user->account_id;
        $post_data 				= $this->input->post('postdata');

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'linked_sites');
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            if (!empty($account_id) && !empty($post_data['project_id']) && !empty($post_data['sites'])) {
                $postdata['account_id']		= $account_id;
                $postdata['project_id']	= $post_data['project_id'];

                $postdata['sites']			= (is_array($post_data['sites'])) ? urlencode(trim(implode(", ", $post_data['sites']))) : $post_data['sites'] ;
                $url 						= 'project/link_sites';
                $linked_sites				= $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if ((!empty($linked_sites->status)) && ($linked_sites->status == 1)) {
                    $this->session->set_flashdata('feedback', (!empty($linked_sites->message)) ? ($linked_sites->message) : "The Site(Sites) has (have) been linked successfuly.");
                } else {
                    $this->session->set_flashdata('feedback', (!empty($linked_sites->message)) ? ($linked_sites->message) : "The Site(s) has NOT been linked.");
                }
            }

            redirect('webapp/project/profile/'.$post_data['project_id'].'/linked_sites', 'refresh');
        }
    }

    /*
    *	Project lookup / search
    */
    public function lookup($page = 'details')
    {
        $return_data = '';

        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data .= $this->config->item('ajax_access_denied');
        } else {
            $post = $this->input->post();

            # Setup search parameters
            $search_term   		= (!empty($post['search_term'])) ? urlencode($post['search_term']) : false;
            $project_statuses	= (!empty($post['project_statuses'])) ? $post['project_statuses'] : false;
            $project_types		= (!empty($post['project_types'])) ? $post['project_types'] : false;
            $limit		   		= (!empty($post['limit'])) ? $post['limit'] : 10;
            $start_index   		= (!empty($post['start_index'])) ? $post['start_index'] : 0;
            $offset		   		= (!empty($start_index)) ? (($start_index - 1) * $limit) : 0;
            $where		   		= false;
            $order_by			= urlencode('c.project_id DESC');

            # Prepare postdata
            $postdata = [
                'account_id'		=> $this->user->account_id,
                'search_term'		=> $search_term,
                'project_statuses'	=> $project_statuses,
                'project_types'	=> $project_types,
                'where'				=> $where,
                'order_by'			=> $order_by,
                'limit'				=> $limit,
                'offset'			=> $offset
            ];


            $url 					= 'project/lookup';
            $search_result			= $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $projects				= (isset($search_result->projects)) ? $search_result->projects : null;

            if (!empty($projects)) {
                ## Create pagination
                $counters 		= $this->project_service->get_total_projects($this->user->account_id, $search_term, $project_statuses, $project_types, $where, $limit, $offset);

                ## Direct access to count, this should only return counters
                $page_number	= ($start_index > 0) ? $start_index : 1;

                if (!empty($counters->pages) && ($counters->pages > 1)) {
                    $page_display = '<span class="pull-left no_page_of">Page <strong>'.$page_number.'</strong> of <strong>'.$counters->pages.'</strong></span>';
                } else {
                    $page_display = '';
                }

                if ($counters->total > 0) {
                    $config['total_rows'] 	= $counters->total;
                    $config['per_page'] 	= $limit;
                    $config['current_page'] = $page_number;
                    $pagination_setup 		= _pagination_config('webapp/project/dashboard');
                    $config					= array_merge($config, $pagination_setup);
                    $this->pagination->initialize($config);
                    $pagination 			= $this->pagination->create_links();
                }

                $return_data = $this->load_project_view($projects);

                if (!empty($pagination)) {
                    $return_data .= '<tr><td colspan="8" style="padding: 0;">';
                    $return_data .= $page_display.$pagination;
                    $return_data .= '</td></tr>';
                }
            } else {
                $return_data .= '<tr><td colspan="8" class="end">';
                $return_data .= (isset($search_result->message)) ? $search_result->message : 'No records found';
                $return_data .= '</td></tr>';
            }
        }

        print_r($return_data);
        die();
    }


    /*
    * 	Prepare project views
    */
    private function load_project_view($project_data)
    {
        $return_data = '';
        if (!empty($project_data)) {
            foreach ($project_data as $k => $project_details) {
                $return_data .= '<tr data-id="'.$project_details->project_id.'" >';
                $return_data .= '<td data-label="Project Name"><a href="'.base_url('/webapp/project/profile/'.$project_details->project_id).'" >'.$project_details->project_name.'</a></td>';
                $return_data .= '<td data-label="Project Reference">'.$project_details->project_ref.'</td>';
                $return_data .= '<td data-label="Project Type">'.$project_details->type_name.'</td>';
                $return_data .= '<td data-label="Project Status">'.$project_details->status_name.'</td>';
                $return_data .= '<td data-label="Project Lead Name">'.$project_details->project_lead_name.'</td>';
                $return_data .= '<td data-label="Project Start Date">'.$project_details->start_date.'</td>';
                $return_data .= '<td data-label="Project End Date">'.$project_details->end_date.'</td>';
                $return_data .= '<td data-label="Created On">'.($project_details->date_created).'</td>';
                $return_data .= '</tr>';
            }

            if (!empty($pagination)) {
                $return_data .= '<tr><td colspan="8" style="padding: 0;">';
                $return_data .= $page_display.$pagination;
                $return_data .= '</td></tr>';
            }
        } else {
            $return_data .= '<tr><td colspan="8"><p style="width: 100%;">'.$this->config->item("no_records").'</p></td></tr>';
        }
        return $return_data;
    }



    /*
    *	Sites lookup / search
    */
    public function sites_lookup($page = 'linked_sites')
    {
        $return_data = '';

        $section = (!empty($page)) ? $page : $this->router->fetch_method();
        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data .= $this->config->item('ajax_access_denied');
        } else {
            $post = $this->input->post();

            # Setup search parameters
            $search_term   		= (!empty($post['search_term'])) ? urlencode($post['search_term']) : false;
            $project_id   		= (!empty($post['project_id'])) ? urlencode($post['project_id']) : false;
            $limit		   		= (!empty($post['limit'])) ? $post['limit'] : 20;
            $start_index   		= (!empty($post['start_index'])) ? $post['start_index'] : 0;
            $offset		   		= (!empty($start_index)) ? (($start_index - 1) * $limit) : 0;
            $where		   		= false;
            $order_by			= false;

            # prepare postdata
            $postdata = [
                'account_id'		=> $this->user->account_id,
                'search_term'		=> $search_term,
                'where'				=> $where,
                'order_by'			=> $order_by,
                'limit'				=> $limit,
                'offset'			=> $offset
            ];

            $url 					= 'site/lookup';
            $search_result			= $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $sites				= (isset($search_result->sites)) ? $search_result->sites : null;

            if (!empty($sites)) {
                ## Create pagination
                $counters 		= $this->site_service->get_total_sites($this->user->account_id, $search_term, $where);

                $page_number	= ($start_index > 0) ? $start_index : 1;
                $page_display	= '<span style="margin:15px 0px;" class="pull-left">Page <strong>'.$page_number.'</strong> of <strong>'.$counters->pages.'</strong></span>';

                $return_data = $this->load_sites_view($sites);
            } else {
                $return_data .= '<tr><td colspan="8" class="end">';
                $return_data .= (isset($search_result->message)) ? $search_result->message : 'No records found';
                $return_data .= '</td></tr>';
            }
        }

        $dataset['sites'] 		= $return_data;
        $dataset['status_msg'] 	= $search_result->message;
        $dataset['type_msg'] 	= $search_result->status;

        print_r(json_encode($dataset));
        die();
    }


    /*
    *	To unlink the Site from the Project
    */
    public function unlink_site($site_id = false, $project_id = false, $page = "linked_sites")
    {
        $account_id 			= $this->user->account_id;
        $project_id			= $this->uri->segment(5, 0);
        $referring_page			= $this->uri->segment(6, 0);

        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            if (!empty($site_id) && !empty($account_id)) {
                $postdata['site_id']		= $site_id;
                $postdata['account_id']		= $account_id;
                $postdata['project_id']	= $project_id;
                $url 						= 'project/unlink_site';

                $unlinked_site					= $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if ((!empty($unlinked_site->status)) && ($unlinked_site->status == 1)) {
                    $this->session->set_flashdata('feedback', (!empty($unlinked_site->message)) ? ($unlinked_site->message) : "The Site  has been unlinked successfully.");
                } else {
                    $this->session->set_flashdata('feedback', (!empty($unlinked_site->message)) ? ($unlinked_site->message) : "The Site has NOT been unlinked.");
                }
            }

            redirect('webapp/project/profile/'.$project_id.'/'.$referring_page, 'refresh');
        }
    }

    /*
    * 	Prepare Sites view
    */
    private function load_sites_view($sites_data)
    {
        $return_data = '';
        if (!empty($sites_data)) {
            foreach ($sites_data as $k => $sites_details) {
                $return_data .= '<tr data-id="'.$sites_details->site_id.'">';
                $return_data .= '<td data-label="Site ID" class="width_80"><a href="'.base_url('/webapp/site/profile/'.$sites_details->site_id).'">'.str_pad($sites_details->site_id, 4, '0', STR_PAD_LEFT).'</a></td>';
                $return_data .= '<td data-label="Site Name" class="width_240">'.$sites_details->site_name.'</td>';
                $return_data .= '<td data-label="Summary Line" class="width_240">'.$sites_details->summaryline.'</td>';
                $return_data .= '<td data-label="Site Reference" class="width_120">'.$sites_details->site_reference.'</td>';
                $return_data .= '<td data-label="Site Postcodes" class="width_120">'.$sites_details->site_postcodes.'</td>';
                $return_data .= '<td data-label="Date Created" class="width_120">'.$sites_details->date_created.'</td>';
                $return_data .= '<td data-label="Link Site" class="width_80"><input type="checkbox" name="postdata[sites][]" value="'.$sites_details->site_id.'" /></td>';
                $return_data .= '</tr>';
            }
        } else {
            $return_data .= '<tr><td colspan="8"><p style="width: 100%;">'.$this->config->item("no_records").'</p></td></tr>';
        }
        return $return_data;
    }

    /*
    * Project Schedules lookup / search
    */
    public function schedules_lookup($asset_id = false, $page = 'details')
    {
        $return_data = '';

        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            $return_data .= $this->config->item('ajax_access_denied');
        } else {
            # Setup search parameters
            $search_term   	= ($this->input->post('search_term')) ? $this->input->post('search_term') : false;
            $where   	   	= ($this->input->post('where')) ? $this->input->post('where') : false;
            $limit		   	= (!empty($where['limit'])) ? $where['limit'] : DEFAULT_LIMIT;
            $start_index   	= ($this->input->post('start_index')) ? $this->input->post('start_index') : DEFAULT_OFFSET;
            $offset		   	= (!empty($start_index)) ? (($start_index - 1) * $limit) : 0;
            $order_by	   	= ($this->input->post('order_by')) ? $this->input->post('order_by') : false;

            #prepare postdata
            $postdata = [
                'account_id'	=> $this->user->account_id,
                'search_term'	=> $search_term,
                'where'			=> $where,
                'order_by'		=> $order_by,
                'limit'			=> $limit,
                'offset'		=> $offset
            ];

            $search_result	= $this->webapp_service->api_dispatcher($this->api_end_point.'job/schedules', $postdata, ['auth_token'=>$this->auth_token], true);

            $schedules			= (isset($search_result->schedules)) ? $search_result->schedules : null;

            if (!empty($schedules)) {
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

                $return_data = $this->load_schedules_view($schedules);
                if (!empty($pagination)) {
                    $return_data .= '<tr style="border-bottom:1px solid #red" ><td colspan="5" style="padding: 0; border-bottom:#f4f4f4">';
                    $return_data .= $page_display.$pagination;
                    $return_data .= '</td></tr>';
                }
            } else {
                $return_data .= '<tr><td colspan="5" style="padding: 0;"><br/>';
                $return_data .= $this->config->item("no_records");
                $return_data .= '</td></tr>';
            }
        }

        print_r($return_data);
        die();
    }

    /*
    * Prepare schedules view
    */
    private function load_schedules_view($schedules_data)
    {
        $return_data = '';
        if (!empty($schedules_data)) {
            foreach ($schedules_data as $k => $schedule_details) {
                $return_data .= '<tr>';
                $return_data .= '<td><a href="'.base_url('/webapp/job/schedule_profile/'.$schedule_details->schedule_id).'" >'.$schedule_details->schedule_name.'</a></td>';
                $return_data .= '<td>'.$schedule_details->frequency_name.'</td>';
                $return_data .= '<td>'.$schedule_details->activities_total.'</td>';
                $return_data .= '<td>'.$schedule_details->schedule_status.'</td>';
                $return_data .= '<td>';
                $return_data .= '<class class="row pull-right">';
                $return_data .= '<div class="col-md-6" ><a href="'.base_url('/webapp/job/schedule_profile/'.$schedule_details->schedule_id).'" ><i title="Click here to view this schedule record" class="fas fa-edit text-blue pointer"></i></a></div>';
                $return_data .= '<div class="col-md-6 delete-item" ><i title="Click here to delete this Schedule" class="delete-item fas fa-trash-alt text-red pointer"></i></div>';
                $return_data .= '</span>';
                $return_data .= '</td>';
                $return_data .= '</tr>';
            }

            if (!empty($pagination)) {
                $return_data .= '<tr><td colspan="5" style="padding: 0;">';
                $return_data .= $page_display.$pagination;
                $return_data .= '</td></tr>';
            }
        } else {
            $return_data .= '<tr><td colspan="5"><br/>'.$this->config->item("no_records").'</td></tr>';
        }
        return $return_data;
    }


    public function get_project_workflows()
    {
        $return_data 		= [ 'status'=>0 ];
        $postdata 	  		= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
        $proj_workflow_data	= $this->webapp_service->api_dispatcher($this->api_end_point.'project/project_workflows', $postdata, $this->options, true);

        $result				= (!empty($proj_workflow_data->project_workflows)) ? $proj_workflow_data->project_workflows : null;
        $message	  		= (isset($proj_workflow_data->message)) ? $proj_workflow_data->message : 'Oops! There was an error processing your request.';
        $result_counters    = (!empty($proj_workflow_data->counters)) ? $proj_workflow_data->counters : null;

        if (!empty($result)) {
            $return_data['status'] = 1;
            $return_data['project_workflows'] = $result;
            $return_data['page_counter'] = $result_counters;
        }

        $return_data['status_msg'] = $message;
        print_r(json_encode($return_data));
        die();
    }

    public function get_workflow_entries()
    {
        $return_data 		= [ 'status'=>0 ];
        $postdata 	  		= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
        $workflow_entry_data	= $this->webapp_service->api_dispatcher($this->api_end_point.'project/workflow_entries', $postdata, $this->options, true);

        $result				= (!empty($workflow_entry_data->workflow_entries)) ? $workflow_entry_data->workflow_entries : null;
        $result_counters    = (!empty($workflow_entry_data->counters)) ? $workflow_entry_data->counters : null;
        $message	  		= (isset($workflow_entry_data->message)) ? $workflow_entry_data->message : 'Oops! There was an error processing your request.';

        if (!empty($result)) {
            $return_data['status'] = 1;
            $return_data['workflow_entries'] = $result;
            $return_data['page_counter'] = $result_counters;
        }

        $return_data['status_msg'] = $message;
        print_r(json_encode($return_data));
        die();
    }


    /** Archive Project **/
    public function arcive_project($project_id = false, $page = 'details')
    {
        $return_data = [
            'status'=>0
        ];

        $section 		= ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $project_id 	= ($this->input->post('project_id')) ? $this->input->post('project_id') : (!empty($project_id) ? $project_id : null);

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postdata 	  		= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $archive_project	= $this->webapp_service->api_dispatcher($this->api_end_point.'project/archive_project', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  		= (isset($archive_project->status)) ? $archive_project->status : null;
            $message	  		= (isset($archive_project->message)) ? $archive_project->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status']		= 1;
                $return_data['project'] 	= $result;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }
}
