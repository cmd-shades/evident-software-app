<?php

namespace Application\Modules\Web\Controllers;

class Audit extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        $this->module_id 	   = $this->webapp_service->_get_module_id($this->router->fetch_class());
        $this->load->library('pagination');

        $this->module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        $this->load->model('serviceapp/Audit_model', 'audit_service');
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
            redirect('webapp/audit/audits', 'refresh');
        }
    }

    /** Get list of audits **/
    public function audits($audit_id = false)
    {
        if ($audit_id) {
            redirect('webapp/audit/profile/'.$audit_id, 'refresh');
        }

        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $data['audit_statuses'] = ['Completed','In Progress'];

            $audit_types		 	= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/audit_types', ['account_id'=>$this->user->account_id, 'un_grouped'=>1 ], ['auth_token'=>$this->auth_token], true);
            $data['audit_types']	= (isset($audit_types->audit_types)) ? $audit_types->audit_types : null;

            $results_statuses			= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/audit_result_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true);
            $data['results_statuses']	= (!empty($results_statuses->results_statuses)) ? $results_statuses->results_statuses : null ;

            $this->_render_webpage('audit/index', $data);
        }
    }

    //View object profile
    public function profile($audit_id = false, $page = 'details')
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } elseif ($audit_id) {
            $run_admin_check 	  = false;
            $audit_details		  = $this->webapp_service->api_dispatcher($this->api_end_point.'audit/audits', ['account_id'=>$this->user->account_id,'audit_id'=>$audit_id, 'where'=>['sectioned'=>1]], ['auth_token'=>$this->auth_token], true);
            $data['audit_details']= (isset($audit_details->audits)) ? $audit_details->audits : null;
            if (!empty($data['audit_details'])) {
                #Get allowed access for the logged in user
                $data['permissions']= $item_access;
                $data['active_tab']	= $page;

                $module_items 		= $this->webapp_service->api_dispatcher($this->api_end_point.'access/account_modules_items', ['account_id'=>$this->user->account_id, 'module_id'=>$this->module_id ], ['auth_token'=>$this->auth_token], true);
                $data['module_tabs']= (isset($module_items->module_items)) ? $module_items->module_items : null;

                $reordered_tabs 		 = reorder_tabs($data['module_tabs']);
                $data['module_tabs'] 	 = (!empty($reordered_tabs['module_tabs'])) ? $reordered_tabs['module_tabs'] : $data['module_tabs'];
                $data['more_list_active']= (!empty($reordered_tabs['more_list']) && in_array($page, $reordered_tabs['more_list'])) ? true : false;

                switch($page) {
                    case 'documents':

                        if (in_array(strtolower($data['audit_details']->audit_group), ['asset'])) {
                            $audit_group = 'asset';
                        } elseif (in_array(strtolower($data['audit_details']->audit_group), ['site', 'building'])) {
                            $audit_group = 'site';
                        } elseif (in_array(strtolower($data['audit_details']->audit_group), ['vehicle','fleet'])) {
                            $audit_group = 'fleet';
                        } elseif (in_array(strtolower($data['audit_details']->audit_group), ['job'])) {
                            $audit_group = 'job';
                        } elseif (in_array(strtolower($data['audit_details']->audit_group), ['customer'])) {
                            $audit_group = 'customer';
                        } elseif (in_array(strtolower($data['audit_details']->audit_group), ['people', 'person'])) {
                            $audit_group = 'people';
                        } else {
                            $audit_group = 'other';
                        }

                        $data['document_group']	= $audit_group;
                        $audit_documents		= $this->webapp_service->api_dispatcher($this->api_end_point.'document_handler/document_list', ['account_id'=>$this->user->account_id, 'audit_id'=>$audit_id, 'document_group'=>$audit_group ], ['auth_token'=>$this->auth_token], true);
                        $data['audit_documents']= (isset($audit_documents->documents->{$this->user->account_id})) ? $audit_documents->documents->{$this->user->account_id} : null;
                        $data['include_page'] 	= 'audit_documents.php';
                        break;
                    case 'outcome':
                    case 'outcomes':
                        $audit_outcomes		 	= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/exceptions', ['account_id'=>$this->user->account_id, 'where'=>['audit_id'=>$audit_id] ], ['auth_token'=>$this->auth_token], true);
                        $data['audit_outcomes'] = (isset($audit_outcomes->exceptions->result)) ? $audit_outcomes->exceptions->result : null;
                        $data['include_page'] 	= 'audit_outcomes.php';
                        break;

                    case 'details':
                    default:
                        $audit_types		 	= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/audit_types', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true);
                        $data['audit_types'] 	= (isset($audit_types->audit_types)) ? $audit_types->audit_types : null;

                        $users		  	  		= $this->webapp_service->api_dispatcher($this->api_end_point.'user/users', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true);
                        $data['users']  	  	= (isset($users->users)) ? $users->users : null;

                        $data['include_page'] 	= 'audit_details.php';
                        break;
                }
            }

            //Run the admin check if tab needs only admin
            if (!empty($run_admin_check)) {
                if ((!admin_check($this->user->is_admin, false, (!empty($data['permissions']) ? $data['permissions']->is_admin : false)))) {
                    $data['admin_no_access'] = true;
                }
            }

            $this->_render_webpage('audit/profile', $data, '');
        } else {
            redirect('webapp/audit', 'refresh');
        }
    }

    /** Create new audit **/
    public function create($page = 'details')
    {
        redirect('webapp/audit/audits', 'refresh');

        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $audit_types	 	= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/audit_types', ['account_id'=>$this->user->account_id, 'categorized'=>1 ], ['auth_token'=>$this->auth_token], true);
            $data['audit_types']= (isset($audit_types->audit_types)) ? $audit_types->audit_types : null;
            $this->_render_webpage('audit/audit_create_new', $data);
        }
    }

    /** Do audit creation **/
    public function create_audit()
    {
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

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
            $postdata 	  = array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $new_audit	  = $this->webapp_service->api_dispatcher($this->api_end_point.'audit/create', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  = (isset($new_audit->audit)) ? $new_audit->audit : null;
            $message	  = (isset($new_audit->message)) ? $new_audit->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] = 1;
                $return_data['audit']   = $new_audit;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }

    /*
    * Evidocs lookup / search
    */
    public function lookup($page = 'audits')
    {
        $return_data['stats'] 	= '<p>No stats available<p>';
        $return_data['audits']	= '';

        # Check module access
        $section 		= (!empty($page)) ? $page : $this->router->fetch_method();
        $module_access 	= $this->webapp_service->check_access($this->user, $this->module_id);
        if (!$this->user->is_admin && !$module_access) {
            $return_data['audits'] .= $this->config->item('ajax_access_denied');
        } else {
            # Setup search parameters
            $where 				= [];
            $search_term   		= ($this->input->post('search_term')) ? $this->input->post('search_term') : false;
            $limit		   		= ($this->input->post('limit')) ? $this->input->post('limit') : DEFAULT_LIMIT;
            $start_index   		= ($this->input->post('start_index')) ? $this->input->post('start_index') : 0;
            $offset		   		= (!empty($start_index)) ? (($start_index - 1) * $limit) : DEFAULT_OFFSET;
            $order_by	   		= false;
            $stats				= false;

            if (!empty($this->input->post('audit_types'))) {
                $where['audit_types'] = $this->input->post('audit_types');
            }

            if (!empty($this->input->post('next_audit_dates'))) {
                $where['next_audit_dates'] = $this->input->post('next_audit_dates');
            }

            if (!empty($this->input->post('audit_statuses'))) {
                $where['audit_statuses'] = json_decode(urldecode(json_encode($this->input->post('audit_statuses'))));
            }

            if (!empty($this->input->post('result_statuses'))) {
                $where['result_statuses'] = $this->input->post('result_statuses');
            }

            if (!empty($this->input->post('eol_dates'))) {
                $where['eol_dates'] = $this->input->post('eol_dates');
            }

            $view_type = 'overview';
            if (!empty($this->input->post('audit_result_status_id'))) {
                $where['audit.audit_result_status_id'] = $this->input->post('audit_result_status_id');
                $view_type = 'result_status';
            }

            #prepare postdata
            $postdata = [
                'account_id'		=> $this->user->account_id,
                'search_term'		=> $search_term,
                'limit'				=> $limit,
                'offset'			=> $offset,
                'order_by'			=> $order_by,
                'where'				=> $where,
            ];

            ## search result with stats:
            ##$sws_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/lookup_w_instant_stats', $postdata, ['auth_token'=>$this->auth_token], true );
            ##$audits		= ( isset( $sws_result->dataset->audits ) ) ? $sws_result->dataset->audits : null;
            ##$stats		= ( isset( $sws_result->dataset->stats ) ) ? $sws_result->dataset->stats : null;

            $result		= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/lookup', $postdata, ['auth_token'=>$this->auth_token], true);
            $audits		= (isset($result->audits)) ? $result->audits : null;
            $stats		= (isset($result->stats)) ? $result->stats : null;
            $counters	= (isset($result->counters)) ? $result->counters : null;

            if (!empty($stats)) {
                $return_data['stats'] = $this->load_stats_view($stats);
            }

            if (!empty($audits)) {
                ## Create pagination
                ## $counters 		= $this->audit_service->get_total_audits( $this->user->account_id, $search_term, $where, $order_by, $limit, $offset );//Direct access to count, this should only return counters
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

                $return_data['audits'] = $this->load_audits_view($audits, $view_type);
                if (!empty($pagination)) {
                    $return_data['audits'] .= '<tr><td colspan="9" style="padding: 0;">';
                    $return_data['audits'] .= $page_display.$pagination;
                    $return_data['audits'] .= '</td></tr>';
                }
            } else {
                $return_data['audits'] .= '<tr><td colspan="9">';
                $return_data['audits'] .= (isset($search_result->message)) ? $search_result->message : $this->config->item("no_records").'</td></tr>';
            }
        }

        print_r(json_encode($return_data));
        die();
    }

    /*
    * Prepare audits views
    */
    private function load_audits_view($audits_data, $view_type = false)
    {
        $return_data = '';

        if (!empty($audits_data)) {
            foreach ($audits_data as $k => $audit_details) {
                $return_data .= '<tr>';
                $return_data .= '<td><a href="'.base_url('/webapp/audit/profile/'.$audit_details->audit_id).'" >'.$audit_details->alt_audit_type.'</a></td>';
                $return_data .= '<td><a href="'.base_url('/webapp/asset/profile/'.$audit_details->asset_id).'" >'.((!empty($audit_details->asset_unique_id)) ? $audit_details->asset_unique_id : '').'</a></td>';
                #$return_data .= '<td><a href="'.base_url('/webapp/site/profile/'.$audit_details->site_id).'" >'.$audit_details->site_name.'</a></td>';
                #$return_data .= '<td width="10%"><a href="'.base_url('/webapp/audit/profile/'.$audit_details->audit_id).'" >'.$audit_details->vehicle_reg.'</a></td>';
                $return_data .= '<td width="10%"><a href="'.base_url('/webapp/audit/profile/'.$audit_details->audit_id).'" >'.$audit_details->result_status.'</a></td>';
                $return_data .= '<td width="10%">'.ucwords($audit_details->created_by).'</td>';
                $return_data .= '<td width="10%">'.$audit_details->audit_status.'</td>';

                /*if ( $view_type == 'result_status' ){

                    // $return_data .= '<td width="10%">'.ucwords( $audit_details->created_by ).'</td>';
                    // #$return_data .= '<td width="10%"><a href="'.base_url('/webapp/audit/profile/'.$audit_details->audit_id).'" >'.$audit_details->result_status.'</a></td>';

                }else{
                    // if( !empty( $audit_details->asset_id ) && !empty( $audit_details->asset_next_audit_date ) ){
                        // $return_data .= '<td><a href="'.base_url('/webapp/audit/profile/'.$audit_details->audit_id).'" >'.$audit_details->asset_next_audit_date.'</a></td>';
                    // } else if ( !empty( $audit_details->site_id ) && !empty( $audit_details->site_next_audit_date ) ){
                        // $return_data .= '<td><a href="'.base_url('/webapp/audit/profile/'.$audit_details->audit_id).'" >'.$audit_details->site_next_audit_date.'</a></td>';
                    // } else if ( !empty( $audit_details->vehicle_id ) && !empty( $audit_details->fleet_vehicle_next_audit_date ) ){
                        // $return_data .= '<td><a href="'.base_url('/webapp/audit/profile/'.$audit_details->audit_id).'" >'.$audit_details->fleet_vehicle_next_audit_date.'</a></td>';
                    // } else {
                        // $return_data .= '<td><a href="'.base_url('/webapp/audit/profile/'.$audit_details->audit_id).'" > --- </a></td>';
                    // }


                    // if( !empty( $audit_details->asset_id ) && !empty( $audit_details->end_of_life_date ) ){
                        // $return_data .= '<td width="10%"><a href="'.base_url('/webapp/audit/profile/'.$audit_details->audit_id).'" >'.$audit_details->end_of_life_date.'</a></td>';
                    // } else {
                        // $return_data .= '<td width="10%"><a href="'.base_url('/webapp/audit/profile/'.$audit_details->audit_id).'" > --- </a></td>';
                    // }
                    // $return_data .= '<td width="10%">'.ucwords( $audit_details->created_by ).'</td>';

                }*/

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

    /** Update SIte Details **/
    public function update_audit($audit_id = false, $page = 'details')
    {
        $return_data = [
            'status'=>0
        ];

        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $audit_id = ($this->input->post('audit_id')) ? $this->input->post('audit_id') : (!empty($audit_id) ? $audit_id : null);

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postdata 	  = array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $updates_audit= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/update', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  = (isset($updates_audit->audit)) ? $updates_audit->audit : null;
            $message	  = (isset($updates_audit->message)) ? $updates_audit->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status']= 1;
                $return_data['audit'] = $result	;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }

    /** Upload a Evidocs document **/
    public function upload_docs($audit_id = false, $page = 'details')
    {
        $return_data = [
            'status'=>0
        ];

        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $audit_id = ($this->input->post('audit_id')) ? $this->input->post('audit_id') : (!empty($audit_id) ? $audit_id : null);

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            redirect('webapp/audit', 'refresh');
        } else {
            $postdata 		= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $document_group = !empty($postdata['document_group']) ? $postdata['document_group'] : 'other';
            $folder		 	= !empty($postdata['document_group']) ? $postdata['document_group'] : 'other';
            $doc_upload		= $this->document_service->upload_files($this->user->account_id, $postdata, $document_group, $folder);

            redirect('webapp/audit/profile/'.$audit_id.'/documents');
        }
        redirect('webapp/audit/', 'refresh');
    }

    /*
    * Load a ra record
    */
    public function view_ra_record($assessment_id = false)
    {
        $assessment_id 	= ($this->input->post('assessment_id')) ? $this->input->post('assessment_id') : (!empty($assessment_id) ? $assessment_id : null);

        $return_data = [
            'status'=>0,
            'ra_record'=>null,
            'status_msg'=>'Invalid paramaters'
        ];

        if (!empty($assessment_id)) {
            $ra_result	= $this->webapp_service->api_dispatcher($this->api_end_point.'risk_assessment/ra_records', ['account_id'=>$this->user->account_id,'assessment_id'=>$assessment_id], ['auth_token'=>$this->auth_token], true);
            $result		= (isset($ra_result->ra_records)) ? $ra_result->ra_records : null;
            $message	= (isset($ra_result->message)) ? $ra_result->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $ra = $this->load_ra($result);
                $return_data['status'] 	  = 1;
                $return_data['ra_record'] = $ra;
            }
            $return_data['status_msg'] = $message;
        }
        print_r(json_encode($return_data));
        die();
    }

    private function load_ra($ra_record = false)
    {
        $ra = '';
        if (!empty($ra_record)) {
            $ra .= '<table style="width:100%">';
            $ra .= '<tr><th width="30%">Assessment ID</th><td>'.$ra_record->assessment_id.'</td></tr>';
            $ra .= '<tr><th>Date Submitted</th><td>'.date('d-m-Y', strtotime($ra_record->date_created)).'</td></tr>';
            $ra .= '<tr><th>Submitted by</th><td>'.$ra_record->created_by.'</td></tr>';
            $ra .= '<tr><th>Risks Completed</th><td><i class="far  '.(($ra_record->risks_completed == 1) ? " fa-check-circle text-green " : " fa-times-circle text-red").' "></i></td></tr>';
            $ra .= '<tr><th>Documents Uploaded</th><td><i class="far  '.(($ra_record->documents_uploaded == 1) ? " fa-check-circle text-green " : " fa-times-circle text-red").' "></i></td></tr>';
            $ra .= '<tr><th>Signature Uploaded</th><td><i class="far  '.(($ra_record->signature_uploaded == 1) ? " fa-check-circle text-green " : " fa-times-circle text-red").' "></i></td></tr>';
            $ra .= '<tr><th colspan="2">&nbsp;</th></tr>';
            $ra .= '<tr><th colspan="2"><span style="font-weight:400">RESPONSES</span><hr></th></tr>';
            $ra .= '<tr><td colspan="2"><table style="width:100%;display:table">';
            $ra .= '<tr><th width="10%">ID</th><th width="75%">Risk Text / Question</th><th width="15%">Response</th></tr>';
            foreach ($ra_record->ra_responses as $k=>$ra_item) {
                $k++;
                $ra .= '<tr><td>'.$k.'</td><td>'.$ra_item->risk_text.'</td><td>'.$ra_item->risk_response.'</td></tr>';
            }
            $ra .= '</table></td></tr>';
            $ra .= '</table>';
        }
        return $ra;
    }


    /** Download audit file **/
    public function download($audit_id = false)
    {
        if ($audit_id) {
            $audit_details  = $this->webapp_service->api_dispatcher($this->api_end_point.'audit/audits', ['account_id'=>$this->account_id,'audit_id'=>$audit_id, 'where' => ['sectioned' => true]], ['auth_token'=>$this->auth_token], true);
            $audit_data 	= (isset($audit_details->audits)) ? $audit_details->audits : null;

            if (!empty($audit_data)) {
                $audit_uuid = $this->webapp_service->update_audit_uuid($audit_id);
                $this->fetch($audit_id, $audit_uuid, $audit_data);
            } else {
                redirect('webapp/audit/', 'refresh');
            }
        } else {
            redirect('webapp/audit/', 'refresh');
        }
    }


    /** fetch file **/
    public function fetch($audit_id = false, $audit_uuid = false, $audit_data = false)
    {
        if (!empty($audit_id) && !empty($audit_uuid) && !empty($audit_data)) {
            $contract_id 			 = (!empty($audit_data->contract_id)) ? $audit_data->contract_id : (!empty($audit_data->linked_contract_id) ? $audit_data->linked_contract_id : false);
            $default_evidoc_logo	 = '_account_assets/accounts/'.$this->user->account_id.'/contracts/default-evidoc-logo.png';
            $contract_evidoc_logo	 = '_account_assets/accounts/'.$this->user->account_id.'/contracts/'.$this->user->account_id.'-evidoc-logo.png';
            #$contract_evidoc_logo	 = '_account_assets/accounts/'.$this->user->account_id.'/contracts/'.$contract_id.'-evidoc-logo.png';
            $custom_logo			 = (file_exists($this->appDir.$contract_evidoc_logo)) ? $contract_evidoc_logo : ((file_exists($this->appDir.$default_evidoc_logo)) ? $default_evidoc_logo : false);
            #$custom_logo			 = ( file_exists( $this->appDir.$contract_evidoc_logo ) ) ? base_url( $contract_evidoc_logo ) : base_url( $default_evidoc_logo );

            $setup['document_setup'] = [
                'recipient_details'	=>[
                    'show_recipient'	=> false,
                    'recipient_name' 	=> 'James Bond Esq.',
                    'address_line1'		=> 'The Reef',
                    'address_line2'		=> 'C/O Initiative Property Management',
                    'address_line3'		=> 'Suite 4, Lansdowne Place',
                    'address_town'		=> 'Bournemouth',
                    'address_country'	=> 'Surrey',
                    'address_postcode'	=> 'BH8 8EW',
                ],
                'document_content'	=> $audit_data,
                'generic_details'	=>[
                    'document_name'			=> (!empty($audit_data->audit_type)) ? $audit_data->audit_type : (!empty($audit_data->alt_audit_type) ? $audit_data->alt_audit_type : 'Evidocs Record'),
                    'audit_frequency'		=> (!empty($audit_data->audit_frequency)) ? ' - '.$audit_data->audit_frequency.'' : '',
                    'document_date'			=> date('l, jS F Y'),
                    'referrence_number'		=> '',
                    'custom_logo'			=> (!empty($custom_logo)) ? base_url($custom_logo) : false, //This link should be saved in the DB account configs!
                    'custom_log_dimensions'	=> 'width="80px"',
                    'custom_footer'			=> DOCUMENT_POWERED_BY,
                    'image_preview'			=> true,
                ],
            ];

            $this->ssid_common->create_pdf_from_template('evipdf/templates/audit_pdf_template.php', $setup);
        } else {
            redirect('webapp/audit/', 'refresh');
        }
    }

    /*
    * Load a audit record
    */
    public function view_audit_record($audit_id = false)
    {
        $audit_id 	= ($this->input->post('audit_id')) ? $this->input->post('audit_id') : (!empty($audit_id) ? $audit_id : null);

        $return_data = [
            'status'=>0,
            'audit_record'=>null,
            'status_msg'=>'Invalid paramaters'
        ];

        if (!empty($audit_id)) {
            $audit_result	= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/audits', ['account_id'=>$this->user->account_id,'audit_id'=>$audit_id], ['auth_token'=>$this->auth_token], true);
            $result		= (isset($audit_result->audits)) ? $audit_result->audits : null;
            $message	= (isset($audit_result->message)) ? $audit_result->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $audit = $this->load_audit($result);
                $return_data['status'] 	  = 1;
                $return_data['audit_record'] = $audit;
            }
            $return_data['status_msg'] = $message;
        }
        print_r(json_encode($return_data));
        die();
    }

    /** Populate Modal **/
    private function load_audit($audit_record = false)
    {
        $audit = '';
        if (!empty($audit_record)) {
            $audit .= '<table style="width:100%">';
            $audit .= '<tr><th width="30%">Evidoc ID</th><td>'.$audit_record->audit_id.'</td></tr>';
            $audit .= '<tr><th width="30%">Evidoc Reference</th><td>'.$audit_record->audit_reference.'</td></tr>';
            $audit .= '<tr><th>Date Submitted</th><td>'.(valid_date($audit_record->evidoc_completion_date) ? date('d-m-Y H:i:s', strtotime($audit_record->evidoc_completion_date)) : date('d-m-Y', strtotime($audit_record->date_created))).'</td></tr>';
            $audit .= '<tr><th>Submitted by</th><td>'.$audit_record->created_by.'</td></tr>';
            #$audit .= '<tr><th>Questions Completed</th><td><i class="far  '.( ( $audit_record->questions_completed == 1 ) ? " fa-check-circle text-green " : " fa-times-circle text-red" ).' "></i></td></tr>';
            #$audit .= '<tr><th>Documents Uploaded</th><td><i class="far  '.( ( $audit_record->documents_uploaded == 1 ) ? " fa-check-circle text-green " : " fa-times-circle text-red" ).' "></i></td></tr>';
            #$audit .= '<tr><th>Signature Uploaded</th><td><i class="far  '.( ( $audit_record->signature_uploaded == 1 ) ? " fa-check-circle text-green " : " fa-times-circle text-red" ).' "></i></td></tr>';
            $audit .= '<tr><th colspan="2">&nbsp;</th></tr>';
            $audit .= '<tr><th colspan="2"><span style="font-weight:400">RESPONSES</span><hr></th></tr>';
            $audit .= '<tr><td colspan="2"><table style="width:100%;display:table;font-size:90%">';
            $audit .= '<tr><th width="10%">ID</th><th width="40%">Evidoc Question</th><th width="30%">Response</th><th width="20%">Extra Info</th></tr>';
            foreach ($audit_record->audit_responses as $k=>$audit_item) {
                $k++;
                $audit .= '<tr>';
                $audit .= '<td>'.$k.'.</td>';
                $audit .= '<td>'.$audit_item->question.'</td>';
                if (is_object($audit_item->response)) {
                    $audit .= '<td><table width="100%">';
                    foreach ($audit_item->response->list  as $zone => $resp) {
                        $audit .= '<tr><th width="30%">'.$zone.':</th><td>'.$resp.'<td><tr>';
                    }
                    $audit .= '</table></td>';
                } else {
                    $audit .= '<td>'.$audit_item->response.'</td>';
                }
                $audit .= '<td>'.$audit_item->response_extra.'</td>';
                $audit .= '</tr>';
            }
            $audit .= '</table></td></tr>';
            $audit .= '</table>';
        }
        return $audit;
    }

    /**  View records by Evidocs Result status **/
    public function result_status($result_group = false)
    {
        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'details');
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $postdata 	  		 	= array_merge(['account_id'=>$this->user->account_id], $this->input->get());
            $audit_result_statuses 	= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/result_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true);
            $data['result_statuses']= (isset($audit_result_statuses->audit_result_statuses)) ? $audit_result_statuses->audit_result_statuses : null;
            $data['selected_group'] = (!empty($this->input->get('group'))) ? $this->input->get('group') : 'all';
            $data['date_from'] 		= (!empty($this->input->get('date_from'))) ? $this->input->get('date_from') : false;
            $data['date_to'] 		= (!empty($this->input->get('date_to'))) ? $this->input->get('date_to') : false;
            $this->_render_webpage('audit/audit_result_status', $data);
        }
    }

    public function action_status($result_group = false)
    {
        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'details');
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $postdata 	  		 	= array_merge(['account_id'=>$this->user->account_id], $this->input->get());
            $audit_result_statuses 	= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/result_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true);
            $data['result_statuses']= (isset($audit_result_statuses->audit_result_statuses)) ? $audit_result_statuses->audit_result_statuses : null;
            $data['selected_group'] = (!empty($this->input->get('group'))) ? $this->input->get('group') : 'all';
            $data['date_from'] 		= (!empty($this->input->get('date_from'))) ? $this->input->get('date_from') : false;
            $data['date_to'] 		= (!empty($this->input->get('date_to'))) ? $this->input->get('date_to') : false;
            $this->_render_webpage('audit/audit_action_status', $data);
        }
    }


    public function audit_completion_status($result_group = false)
    {
        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'details');
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $postdata 	  		 	= array_merge(['account_id'=>$this->user->account_id], $this->input->get());
            $audit_result_statuses 	= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/result_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true);
            $data['result_statuses']= (isset($audit_result_statuses->audit_result_statuses)) ? $audit_result_statuses->audit_result_statuses : null;

            ## evidoc_completion_statuses
            $postdata 	  		 	= array_merge(['account_id'=>$this->user->account_id], $this->input->get());
            $API_request 			= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/progress_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true);
            $data['progress_statuses']= (isset($API_request->audit_progress_statuses)) ? $API_request->audit_progress_statuses : null;

            $data['selected_group'] = (!empty($this->input->get('group'))) ? $this->input->get('group') : 'all';
            $data['date_from'] 		= (!empty($this->input->get('date_from'))) ? $this->input->get('date_from') : false;
            $data['date_to'] 		= (!empty($this->input->get('date_to'))) ? $this->input->get('date_to') : false;
            $this->_render_webpage('audit/audit_completion_status', $data);
        }
    }

    private function load_stats_view($stats = false)
    {
        $output = '';

        if (!empty($stats)) {
            $stats_no = count(get_object_vars($stats));

            foreach ($stats as $key => $value) {
                $output .= "<div class=\"col-md-".(ceil(12/$stats_no))." col-sm-".(ceil(12/$stats_no))." col-xs-12\" style=\"margin:0\">";
                $output .= "<div class=\"row\">";
                $output .= "<h5 class=\"text-bold text-center\">".(ucwords(str_replace("_", " ", $key)))."</h5>";
                $output .= "<h3 class=\"text-center\">".($value)."</h3>";
                $output .= "</div>";
                $output .= "</div>";
            }
        } else {
            $output .= "<div class=\"col-md-12 col-sm-12 col-xs-12\" style=\"margin:0\">";
            $output .= "<div class=\"row\">";
            $output .= "<h5 class=\"text-bold text-center\">No Stats available</h5>";
            $output .= "<h3 class=\"text-center\">&nbsp;</h3>";
            $output .= "</div>";
            $output .= "</div>";
        }

        return $output;
    }

    public function exceptions()
    {
        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'details');
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $API_call 	= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/exception_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true);
            $data['exception_statuses'] = (isset($API_call->exception_statuses)) ? $API_call->exception_statuses : null;
            $postdata 	  		 		= array_merge(['account_id'=>$this->user->account_id], $this->input->get());
            $exceptions 				= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/exceptions', $postdata, ['auth_token'=>$this->auth_token], true);
            $data['selected_group'] 	= (!empty($this->input->get('group'))) ? $this->input->get('group') : 'all';
            $data['date_from'] 			= (!empty($this->input->get('date_from'))) ? $this->input->get('date_from') : false;
            $data['date_to'] 			= (!empty($this->input->get('date_to'))) ? $this->input->get('date_to') : false;

            $this->_render_webpage('audit/exceptions', $data);
        }
    }

    /*
    * 	Evidocs lookup / search
    */
    public function exceptions_lookup($page = 'audits')
    {
        $return_data['stats'] 		= '<p>No stats available<p>';
        $return_data['exc_data']	= '';

        # Check module access
        $section 		= (!empty($page)) ? $page : $this->router->fetch_method();
        $module_access 	= $this->webapp_service->check_access($this->user, $this->module_id);
        if (!$this->user->is_admin && !$module_access) {
            $return_data['audits'] .= $this->config->item('ajax_access_denied');
        } else {
            # Setup search parameters
            $where 				= [];
            $search_term   		= ($this->input->post('search_term')) ? $this->input->post('search_term') : false;
            $limit		   		= ($this->input->post('limit')) ? $this->input->post('limit') : DEFAULT_LIMIT;
            $start_index   		= ($this->input->post('start_index')) ? $this->input->post('start_index') : 0;
            $offset		   		= (!empty($start_index)) ? (($start_index - 1) * $limit) : DEFAULT_OFFSET;
            $order_by	   		= false;
            $stats				= false;

            if (!empty($this->input->post('record_type'))) {
                $where['record_type'] = $this->input->post('record_type');
            }

            /* if( !empty( $this->input->post( 'audit_result_status_id' ) ) ){
                $where['audit_exceptions.audit_result_status_id'] = $this->input->post( 'audit_result_status_id' );
            } */

            if (!empty($this->input->post('action_status_id'))) {
                $where['action_status_id'] = $this->input->post('action_status_id');
            }

            $view_type = 'overview';

            $postdata = [
                'account_id'		=> $this->user->account_id,
                'search_term'		=> $search_term,
                'limit'				=> $limit,
                'offset'			=> $offset,
                'order_by'			=> $order_by,
                'where'				=> $where,
            ];

            ## search result with stats:
            $API_call	= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/exceptions', $postdata, ['auth_token'=>$this->auth_token], true);
            $exc_data		= (isset($API_call->exceptions->result)) ? $API_call->exceptions->result : null;
            $counters		= (isset($API_call->exceptions->counts)) ? $API_call->exceptions->counts : null;

            if (!empty($exc_data)) {
                ## Create pagination
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

                $return_data['exc_data'] = $this->load_exceptions_view($exc_data, $view_type);

                if (!empty($pagination)) {
                    $return_data['exc_data'] .= '<tr><td colspan="9" style="padding: 0;">';
                    $return_data['exc_data'] .= $page_display.$pagination;
                    $return_data['exc_data'] .= '</td></tr>';
                }
            } else {
                $return_data['exc_data'] .= '<tr><td colspan="9">';
                $return_data['exc_data'] .= (isset($search_result->message)) ? $search_result->message : $this->config->item("no_records").'</td></tr>';
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    private function load_exceptions_view($exceptions = false)
    {
        $result = "";

        if (!empty($exceptions)) {
            foreach ($exceptions as $key => $row) {
                $result .= "<tr>";

                $result .= "<td><a href=\"".(base_url("webapp/audit/exception_profile/".$row->id))."\">".(!empty($row->audit_result_status_name) ? ($row->audit_result_status_name) : "-")."</a></td>";

                // if( !empty( $row->record_type ) && ( strtolower( $row->record_type ) == "failed" ) ){
                // $result .= "<td>".( !empty( $row->failure_reasons ) ? ( $row->failure_reasons ) : "-" )."</td>";
                // } else {
                // $result .= "<td>".( !empty( $row->recommendations ) ? mb_strimwidth ( $row->recommendations, 0, 80, " (...)" ) : "-" )."</td>";
                // }
                $result .= "<td>".(!empty($row->recommendations) ? $row->recommendations : "")."</td>";

                /* $result .= "<td class=\"text-center\">".( !empty( $row->site_id ) ? ( ( int ) $row->site_id ) : "-" )."</td>"; */
                /* $result .= "<td class=\"text-center\">".( !empty( $row->asset_id ) ? ( ( int ) $row->asset_id ) : "-" )."</td>"; */
                /* $result .= "<td class=\"text-center\">".( !empty( $row->vehicle_reg ) ? ( $row->vehicle_reg ) : "-" )."</td>"; */
                $result .= "<td>".(!empty($row->action_due_date) ? ($row->action_due_date) : "-")."</td>";
                /* $result .= "<td>".( !empty( $row->next_audit_date ) ? ( $row->next_audit_date ) : "-" )."</td>"; */
                $result .= "<td class='text-center'>".(!empty($row->priority_rating) ? ($row->priority_rating) : "-")."</td>";
                /* $result .= "<td class=\"text-right\">".( !empty( $row->estimated_repair_cost ) ? ( $row->estimated_repair_cost ) : "-" )."</td>"; */
                $result .= "<td>".(!empty($row->date_created) ? ($row->date_created) : "-")."</td>";
                $result .= "<td>".(!empty($row->audit_type) ? ("<a href=\"".(base_url("webapp/audit/profile/".$row->audit_id))."\">".$row->audit_type)."</a>" : "-")."</td>";
                $result .= "<td>".((!empty($row->action_status_name)) ? $row->action_status_name : '')."</td>";
                $result .= "</tr>";
            }
        }
        return $result;
    }


    /*
    *	Function to show the information about exception
    */
    public function exception_profile($exception_id = false, $page = "exception")
    {
        # Check module access
        $section 		= (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access 	= $this->webapp_service->check_access($this->user, $this->module_id, $section);
        /* if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){ */

        if (!$this->user->is_admin && !$item_access) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            if (!empty($exception_id)) {
                $run_admin_check 	  		= false;
                $postdata 					= [];
                $postdata['account_id'] 	= $this->user->account_id;
                $postdata['where']['id']	= $exception_id;

                $API_call					= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/exceptions', $postdata, ['auth_token'=>$this->auth_token], true);
                $data['exception_details']	= (isset($API_call->exceptions->result)) ? $API_call->exceptions->result[0] : null;

                if (!empty($data['exception_details'])) {
                    #Get allowed access for the logged in user
                    $item_access 				= $this->webapp_service->check_access($this->user, $this->module_id, $section = 'details');

                    $data['permissions']		= $item_access;
                    $data['active_tab']			= $page;

                    $module_items 				= $this->webapp_service->api_dispatcher($this->api_end_point.'access/account_modules_items', ['account_id'=>$this->user->account_id, 'module_id'=>$this->module_id ], ['auth_token'=>$this->auth_token], true);
                    $data['module_tabs']		= (isset($module_items->module_items)) ? $module_items->module_items : null;

                    $reordered_tabs 		 	= reorder_tabs($data['module_tabs']);
                    $data['module_tabs'] 	 	= (!empty($reordered_tabs['module_tabs'])) ? $reordered_tabs['module_tabs'] : $data['module_tabs'];
                    $data['more_list_active']	= (!empty($reordered_tabs['more_list']) && in_array($page, $reordered_tabs['more_list'])) ? true : false;

                    switch($page) {
                        case 'details':
                        default:

                            $audit_result_statuses 			= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/result_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true);
                            $data['audit_result_statuses']	= (isset($audit_result_statuses->audit_result_statuses)) ? $audit_result_statuses->audit_result_statuses : null;

                            $action_statuses 				= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/action_statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true);
                            $data['action_statuses']		= (isset($action_statuses->action_statuses)) ? $action_statuses->action_statuses : null;

                            $users		  	  				= $this->webapp_service->api_dispatcher($this->api_end_point.'user/users', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true);
                            $data['users']  	  			= (isset($users->users)) ? $users->users : null;

                            $postdata							= [];
                            $postdata['account_id']				= $this->user->account_id;
                            $postdata['where']['exception_id']	= $exception_id;
                            $postdata['order_by']				= urlencode("audit_exceptions_log.log_id DESC");

                            $API_call							= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/exception_logs', $postdata, ['auth_token'=>$this->auth_token], true);
                            $data['exception_logs']				= (!empty($API_call->exception_logs)) ? ($API_call->exception_logs) : false;

                            $data['include_page'] 	= 'exception_details.php';
                            break;
                    }
                }

                //Run the admin check if tab needs only admin
                if (!empty($run_admin_check)) {
                    if ((!admin_check($this->user->is_admin, false, (!empty($data['permissions']) ? $data['permissions']->is_admin : false)))) {
                        $data['admin_no_access'] = true;
                    }
                }

                $this->_render_webpage('audit/exception_profile', $data, '');
            } else {
                redirect('webapp/audit/exceptions', 'refresh');
            }
        }
    }


    public function create_exception_log($exception_id = false)
    {
        $return_data 	= ["exception_log" => false, "status_msg" => "Request not completed", "status"=> 0 ];
        $data_post 		= $this->input->post();
        $exception_id 	= (!empty($data_post['exception_id'])) ? ( int ) $data_post['exception_id'] : $exception_id ;

        # Check module-item access for the logged in user
        $section 		= (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access 	= $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            if (!empty($exception_id) && !empty($data_post)) {
                $postdata['account_id'] 	= $this->user->account_id;
                $postdata['exception_id']	= $exception_id;
                $postdata['data']			= $data_post;

                $API_call				= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/create_exception_log', $postdata, ['auth_token'=>$this->auth_token]);

                $return_data['exception_log']  	= (isset($API_call->exception_log)) ? $API_call->exception_log : null;
                $return_data['status_msg']	  	= (isset($API_call->message)) ? $API_call->message : (!empty($return_data['exception_log']) ? 'Request completed with result!' : 'Request completed with no result!');
                $return_data['status']	  		= (isset($API_call->status)) ? $API_call->status : (!empty($return_data['exception_log']) ? 1 : 0);
            } else {
                $return_data['status_msg'] = "Insufficient data";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    public function settings()
    {
        $settings_list = (object)[ 'types', 'questions' ];

        // Redirect if user only has access to 1 module
        if (!empty($data['module_count']) && $data['module_count'] == 1) {
            $module = $data['permitted_modules'][0];
            redirect('webapp/'.$module->module_controller, 'refresh');
        } else {
            $data['settings_list'] = $settings_list;
            $this->_render_webpage('audit/settings', $data, false, true);
        }
    }

    //Manage Evidocs types
    public function evidoc_names($audit_type_id = false, $page = 'details')
    {
        $toggled		= (!empty($this->input->get('toggled')) ? urldecode($this->input->get('toggled')) : false);

        $section 		= (!empty($page)) ? $page : (!empty($this->input->get('page')) ? $this->input->get('page') : 'details');
        $audit_type_id  = (!empty($audit_type_id)) ? $audit_type_id : (!empty($this->input->get('audit_type_id')) ? $this->input->get('audit_type_id') : ((!empty($this->input->get('id')) ? $this->input->get('id') : null)));

        if (!empty($audit_type_id)) {
            $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
            if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
                $this->_render_webpage('errors/access-denied', false);
            } else {
                $default_params = $params =[ 'account_id'=>$this->user->account_id, 'where'=>[ 'audit_type_id'=>$audit_type_id ] ];

                $params['where']['apply_limit'] = 1;
                $evidoc_type_details = $this->webapp_service->api_dispatcher($this->api_end_point.'audit/evidoc_types', $params, [ 'auth_token'=>$this->auth_token ], true);

                if (!empty($evidoc_type_details->evidoc_types)) {
                    $data['evidoc_type_details']= $evidoc_type_details->evidoc_types;

                    $evidoc_groups	 			= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/evidoc_groups', [ 'account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true);
                    $data['evidoc_groups']		= (isset($evidoc_groups->evidoc_groups)) ? $evidoc_groups->evidoc_groups : null;

                    $response_types	 			= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/response_types', $default_params, ['auth_token'=>$this->auth_token], true);
                    $data['response_types']		= (isset($response_types->response_types)) ? $response_types->response_types : null;

                    $evidoc_sections	 		= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/evidoc_sections', $default_params, ['auth_token'=>$this->auth_token], true);
                    $data['evidoc_sections']	= (isset($evidoc_sections->evidoc_sections)) ? $evidoc_sections->evidoc_sections : null;

                    $evidoc_type_sections	 			= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/evidoc_type_sections', $default_params, ['auth_token'=>$this->auth_token], true);
                    $data['evidoc_type_sections']		= (isset($evidoc_type_sections->evidoc_type_sections)) ? $evidoc_type_sections->evidoc_type_sections : null;

                    $default_params['where']['grouped'] = 1;

                    $audit_questions	 		= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/audit_questions', [ 'account_id'=>$this->user->account_id, 'audit_type_id'=>$audit_type_id, 'sectioned'=>1 ], ['auth_token'=>$this->auth_token], true);
                    $data['evidoc_questions']	= (isset($audit_questions->audit_questions)) ? $audit_questions->audit_questions : null;

                    $audit_categories	 		= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/audit_categories', [ 'account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true);
                    $data['evidoc_categories']	= (isset($audit_categories->audit_categories)) ? $audit_categories->audit_categories : null;

                    $asset_types	 			= $this->webapp_service->api_dispatcher($this->api_end_point.'asset/asset_types', [ 'account_id'=>$this->user->account_id, 'where'=>['grouped'=>1], 'limit' => -1 ], ['auth_token'=>$this->auth_token], true);
                    $data['asset_types']		= (isset($asset_types->asset_types)) ? $asset_types->asset_types : null;

                    $data['general_file_types'] = generic_file_types();
                    $data['toggled_section']	= $toggled;

                    $evidoc_frequencies	 		= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/evidoc_frequencies', ['account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true);
                    $data['evidoc_frequencies'] = (isset($evidoc_frequencies->evidoc_frequencies)) ? $evidoc_frequencies->evidoc_frequencies : null;

                    $schedule_frequencies 			= $this->webapp_service->api_dispatcher($this->api_end_point.'job/schedule_frequencies', [ 'account_id'=>$this->user->account_id ], [ 'auth_token'=>$this->auth_token ], true);
                    $data['schedule_frequencies'] 	= isset($schedule_frequencies->schedule_frequencies) ? $schedule_frequencies->schedule_frequencies : false;

                    $asset_group 				= $data['evidoc_type_details']->asset_group;
                    $data['asset_sub_categories']= asset_sub_categories();
                    $data['asset_sub_category']	= (!empty($asset_group) && !empty($data['asset_sub_categories'][$asset_group])) ? $data['asset_sub_categories'][$asset_group] : false;

                    $job_types					= $this->webapp_service->api_dispatcher($this->api_end_point.'job/job_types', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true);
                    $data['job_types']			= (isset($job_types->job_types)) ? $job_types->job_types : null;

                    $available_contracts	  	= $this->webapp_service->api_dispatcher($this->api_end_point.'contract/contracts', ['account_id'=>$this->user->account_id, 'limit'=>-1 ], ['auth_token'=>$this->auth_token], true);
                    $data['available_contracts']= (isset($available_contracts->contract)) ? $available_contracts->contract : null;

                    $disciplines	 			= $this->webapp_service->api_dispatcher($this->api_end_point.'discipline/account_disciplines', [ 'account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true);
                    $data['disciplines']		= (isset($disciplines->account_disciplines)) ? $disciplines->account_disciplines : null;


                    $this->_render_webpage('audit/evidocs_types_profile', $data);
                } else {
                    redirect('webapp/audit/evidoc_names', 'refresh');
                }
            }
        } else {
            $this->_render_webpage('audit/evidocs_types', false, false, true);
        }
    }

    /*
    * Create New Evidocs Type
    */
    public function new_type($page = 'details')
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $evidoc_groups	 		= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/evidoc_groups', ['account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true);
            $data['evidoc_groups']	= (isset($evidoc_groups->evidoc_groups)) ? $evidoc_groups->evidoc_groups : null;

            $evidoc_frequencies	 		= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/evidoc_frequencies', ['account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true);
            $data['evidoc_frequencies'] = (isset($evidoc_frequencies->evidoc_frequencies)) ? $evidoc_frequencies->evidoc_frequencies : null;

            $audit_categories	 		= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/audit_categories', [ 'account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true);
            $data['evidoc_categories']	= (isset($audit_categories->audit_categories)) ? $audit_categories->audit_categories : null;

            $asset_types	 			= $this->webapp_service->api_dispatcher($this->api_end_point.'asset/asset_types', [ 'account_id'=>$this->user->account_id, "where[grouped]" => 1, "limit" => -1 ], ['auth_token'=>$this->auth_token], true);
            $data['asset_types']		= (isset($asset_types->asset_types)) ? $asset_types->asset_types : null;

            $available_contracts	  	= $this->webapp_service->api_dispatcher($this->api_end_point.'contract/contracts', ['account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true);
            $data['available_contracts']= (isset($available_contracts->contract)) ? $available_contracts->contract : null;

            $schedule_frequencies 		= $this->webapp_service->api_dispatcher($this->api_end_point.'job/schedule_frequencies', [ 'account_id'=>$this->user->account_id ], [ 'auth_token'=>$this->auth_token ], true);
            $data['schedule_frequencies'] = isset($schedule_frequencies->schedule_frequencies) ? $schedule_frequencies->schedule_frequencies : false;

            $disciplines	 			= $this->webapp_service->api_dispatcher($this->api_end_point.'discipline/account_disciplines', [ 'account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true);
            $data['disciplines']		= (isset($disciplines->account_disciplines)) ? $disciplines->account_disciplines : null;

            $this->_render_webpage('audit/evidocs_types_add_new', $data);
        }
    }

    /** Check Evidoc exists **/
    public function check_evidoc_exists($page = 'details')
    {
        $return_data = [
            'status'=>0
        ];

        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : 'details');

        $evidoc_type = ($this->input->post('evidoc_type')) ? $this->input->post('evidoc_type') : (!empty($evidoc_type) ? $evidoc_type : null);

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postdata 	  = array_merge([ 'account_id'=>$this->user->account_id ], $this->input->post());
            $check_exists = $this->webapp_service->api_dispatcher($this->api_end_point.'audit/evidoc_types', $postdata, [ 'auth_token'=>$this->auth_token ], true);
            $result		  = (isset($check_exists->evidoc_types)) ? $check_exists->evidoc_types : null;
            $message	  = (isset($check_exists->message)) ? $check_exists->message : 'Oops! There was an error processing your request.';

            if (!empty($result)) {
                $return_data['status']		= 1;
                $return_data['evidoc_type'] = $result;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }

    /*
    * Evidocs Types
    */
    public function evidoc_types_list($page = 'audits')
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
            $where['apply_limit'] = 1;
            $postdata = [
                'account_id'	=>$this->user->account_id,
                'search_term'	=>$search_term,
                'where'			=>$where,
                'order_by'		=>$order_by,
                'limit'			=>$limit,
                'offset'		=>$offset
            ];

            $search_result	= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/evidoc_types', $postdata, [ 'auth_token'=>$this->auth_token ], true);

            $evidoc_types		= (isset($search_result->evidoc_types)) ? $search_result->evidoc_types : null;

            if (!empty($evidoc_types)) {
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

                $return_data = $this->load_evidoctypes_view($evidoc_types);
                if (!empty($pagination)) {
                    $return_data .= '<tr><td colspan="7" style="padding: 0;">';
                    $return_data .= $page_display.$pagination;
                    $return_data .= '</td></tr>';
                }
            } else {
                $return_data .= '<tr><td colspan="7" style="padding: 0 8px;"><br/>';
                $return_data .= (isset($search_result->message)) ? $search_result->message : 'No records found';
                $return_data .= '</td></tr>';
            }
        }

        print_r($return_data);
        die();
    }

    /*
    * Prepare Evidocs types views
    */
    private function load_evidoctypes_view($evidoc_types_data)
    {
        $return_data = '';
        if (!empty($evidoc_types_data)) {
            $asset_sub_categories = asset_sub_categories();

            foreach ($evidoc_types_data as $k => $evidoc_type_details) {
                $asset_group 		= $evidoc_type_details->asset_group;
                $asset_sub_category	= (!empty($asset_group) && !empty($asset_sub_categories[$asset_group])) ? $asset_sub_categories[$asset_group] : false;

                $return_data .= '<tr>';
                $return_data .= '<td><a href="'.base_url('/webapp/audit/evidoc_names/'.$evidoc_type_details->audit_type_id).'">'. ucwords($evidoc_type_details->audit_type) .' - '.ucwords($evidoc_type_details->audit_frequency).'</a></td>';
                $return_data .= '<td>'.ucwords($evidoc_type_details->audit_group). (!empty($asset_sub_category) ? ' <small>('.$asset_sub_category.')</small>' : '') .'</td>';
                $return_data .= '<td>'.$evidoc_type_details->audit_type_desc.'</td>';
                $return_data .= '<td>'.(!empty($evidoc_type_details->date_created) ? date('d-m-Y H:i:s', strtotime($evidoc_type_details->date_created)) : '').'</td>';
                $return_data .= '<td>'.(!empty($evidoc_type_details->is_active) ? 'Active' : 'Disabled').'</td>';
                $return_data .= '<td>'.$evidoc_type_details->frequency_name.'</td>';
                $return_data .= '<td><a href="'.base_url('/webapp/audit/evidoc_names/'.$evidoc_type_details->audit_type_id).'" ><i class="far fa-edit"></i> Open</a></td>';
                $return_data .= '</tr>';
            }

            if (!empty($pagination)) {
                $return_data .= '<tr><td colspan="6" style="padding: 0;">';
                $return_data .= $page_display.$pagination;
                $return_data .= '</td></tr>';
            }
        } else {
            $return_data .= '<tr><td colspan="6"><br/>'.$this->config->item("no_records").'</td></tr>';
        }
        return $return_data;
    }

    /**
    * Creat new Evidoc type
    */
    public function create_evidoc_type()
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
            $postdata 	  	 = array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $new_evidoc_type = $this->webapp_service->api_dispatcher($this->api_end_point.'audit/create_evidoc_type', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  	 = (isset($new_evidoc_type->evidoc_type)) ? $new_evidoc_type->evidoc_type : null;
            $message	  	 = (isset($new_evidoc_type->message)) ? $new_evidoc_type->message : 'Oops! There was an error processing your request.';
            $exists	  	 	 = (!empty($new_evidoc_type->exists)) ? $new_evidoc_type->exists : false;

            if (!empty($result)) {
                $return_data['status'] 			= 1;
                $return_data['evidoc_type'] 	= $result;
                $return_data['already_exists']  = $exists;
                $text_color 					= 'auto';
            }
            $return_data['status_msg'] = '<span class="text-'.$text_color.'">'.$message.'</span>';
        }

        print_r(json_encode($return_data));
        die();
    }

    /** Add new Evidoc Section **/
    public function add_new_section()
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
            $postdata 	  	= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $evidoc_section = $this->webapp_service->api_dispatcher($this->api_end_point.'audit/add_new_section', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  	= (isset($evidoc_section->evidoc_section)) ? $evidoc_section->evidoc_section : null;
            $message	  	= (isset($evidoc_section->message)) ? $evidoc_section->message : 'Oops! There was an error processing your request.';

            if (!empty($result)) {
                $return_data['status'] 	= 1;
                $return_data['section'] = $result;
                $text_color 			= 'auto';
            }
            $return_data['status_msg'] = '<span class="text-'.$text_color.'">'.$message.'</span>';
        }

        print_r(json_encode($return_data));
        die();
    }

    /**
    * Create new Evidoc type
    */
    public function create_evidoc_question()
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
            $postdata 	  		 = array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $new_evidoc_question = $this->webapp_service->api_dispatcher($this->api_end_point.'audit/add_evidoc_question', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  	 	 = (isset($new_evidoc_question->evidoc_question)) ? $new_evidoc_question->evidoc_question : null;
            $message	  	 	 = (isset($new_evidoc_question->message)) ? $new_evidoc_question->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] 			= 1;
                $return_data['evidoc_question'] = $result;
                $text_color 					= 'auto';
            }
            $return_data['status_msg'] = '<span class="text-'.$text_color.'">'.$message.'</span>';
        }

        print_r(json_encode($return_data));
        die();
    }

    /** Update Evidoc name Details **/
    public function update_evidoc_name($audit_type_id = false, $page = 'details')
    {
        $color_class  = 'red';
        $return_data = [
            'status'=>0
        ];

        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $audit_id = ($this->input->post('audit_type_id')) ? $this->input->post('audit_type_id') : (!empty($audit_type_id) ? $audit_type_id : null);

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $audit_type_id		 = (!empty($this->input->post('audit_type_id'))) ? $this->input->post('audit_type_id') : $audit_type_id;
            $postdata 	  		 = array_merge(['account_id'=>$this->user->account_id, 'audit_type_id'=>$audit_type_id ], $this->input->post());
            $updates_evidoc_name = $this->webapp_service->api_dispatcher($this->api_end_point.'audit/update_evidoc_name', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  		 = (isset($updates_evidoc_name->evidoc_name)) ? $updates_evidoc_name->evidoc_name : null;
            $message	  		 = (isset($updates_evidoc_name->message)) ? $updates_evidoc_name->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status']		= 1;
                $return_data['evidoc_name'] = $result;
                $color_class				= 'auto';
            }
            $return_data['status_msg'] = '<span class="text-'.$color_class.'">'.$message.'</span>';
        }

        print_r(json_encode($return_data));
        die();
    }

    /*
    * Load a Question record
    */
    public function view_question_data($question_id = false)
    {
        $question_id 	= ($this->input->post('question_id')) ? $this->input->post('question_id') : (!empty($question_id) ? $question_id : null);

        $return_data = [
            'status'=>0,
            'audit_record'=>null,
            'status_msg'=>'Invalid paramaters'
        ];

        if (!empty($question_id)) {
            $postdata 		= array_merge(['account_id'=>$this->user->account_id ], $this->input->post());
            $question_data	= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/audit_questions', $postdata, ['auth_token'=>$this->auth_token], true);
            $result			= (isset($question_data->audit_questions)) ? $question_data->audit_questions : null;
            $message		= (isset($question_data->message)) ? $question_data->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $question_data 					= $this->load_question_data($result);
                $return_data['status'] 	 		= 1;
                $return_data['question_data'] 	= $question_data;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }

    private function load_question_data($question_record = false, $response_types = false)
    {
        $return_data = '<div>Question data not available.</div>';
        if (!empty($question_record)) {
            #$response_options = ( !empty( $question_record->response_options ) ) ? ( ( is_array( $question_record->response_options ) ) ? $question_record->response_options : ( is_object( $question_record->response_options ) ? object_to_array( $question_record->response_options ) ) : false ) : false;
            $selected_options = !empty($question_record->response_options) ? (is_array($question_record->response_options) ? $question_record->response_options : (is_object($question_record->response_options) ? object_to_array($question_record->response_options) : [])) : [];

            $default_params	 = $params =[ 'account_id'=>$this->user->account_id, 'where'=>[ 'audit_type_id'=>$question_record->audit_type_id ] ];

            $response_types	 = $this->webapp_service->api_dispatcher($this->api_end_point.'audit/response_types', $default_params, ['auth_token'=>$this->auth_token], true);
            $response_types	 = (isset($response_types->response_types)) ? $response_types->response_types : null;

            $evidoc_type_sections = $this->webapp_service->api_dispatcher($this->api_end_point.'audit/evidoc_type_sections', $default_params, ['auth_token'=>$this->auth_token], true);
            $evidoc_type_sections = (isset($evidoc_type_sections->evidoc_type_sections)) ? $evidoc_type_sections->evidoc_type_sections : null;

            $general_file_types = generic_file_types();

            $return_data = '';

            $return_data .= '<div><input type="hidden" name="question_id" value="'.$question_record->question_id.'" /></div>';
            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Question label</label>';
            $return_data .= '<input name="question" class="form-control" type="text" placeholder="Question label" value="'.$question_record->question.'" />';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Evidoc Name</label>';
            $return_data .= '<input class="form-control" type="text" placeholder="Evidoc name" value="'. ucwords($question_record->audit_type).'" readonly title="To change this field, you\'ll have to delete this question and add it to the Evidoc name you require"/>';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Response Type</label>';
            $return_data .= '<select id="response_type" name="response_type" class="form-control">';
            $return_data .= '<option value="">Choose response type</option>';
            if (!empty($response_types)) {
                foreach ($response_types as $k => $resp_type) {
                    $return_data .= '<option value="'.$resp_type->response_type.'" '.((strtolower($resp_type->response_type_alt) == strtolower($question_record->response_type)) ? "selected=selected" : "") .' data-resp_type="'. $resp_type->response_type .'" data-resp_type_alt="'.$resp_type->response_type_alt.'"  data-resp_desc="' .$resp_type->response_type. '" >'.$resp_type->response_type_alt.'</option>';
                }
            }
            $return_data .= '</select>';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<div>';
            if (!empty($response_types)) {
                foreach ($response_types as $k => $resp_type) {
                    $display = ((strtolower($resp_type->response_type_alt) == strtolower($question_record->response_type)) ? 'block' : 'none');

                    switch(strtolower($resp_type->response_type)) {
                        case 'input':
                        case 'input_text':
                        case 'input_integer':
                            //Do nothing
                            $return_data .= '<div class="resp_'.$resp_type->response_type.' resp-type-options" style="display:'.$display.'">';
                            $return_data .= '<input type="hidden" name="response_options['.$resp_type->response_type.'][response_type_max_chars]" value="" class="form-control"  >';
                            $return_data .= '</div>';
                            break;

                        case 'radio':
                        case 'checkbox':
                            //Do something
                            $return_data .= '<div class="resp_'.$resp_type->response_type.' resp-type-options" style="display:'.$display.'">';
                            $return_data .= '<label class="text-grey input-group push-10-left">Update Response Options</label>';
                            $return_data .= '<div class="row checkbox-options" data-checkbox_type="'.$resp_type->response_type.'" >';
                            if (!empty($resp_type->response_type_options)) {
                                foreach ($resp_type->response_type_options as $k => $resp_options) {
                                    $return_data .= '<div class="col-md-4 col-sm-4 col-xs-12">';
                                    $return_data .= '<div class="radio">';
                                    $return_data .= '<label><input '.(in_array($resp_options->option_value, $selected_options) ? 'checked' : '').' type="checkbox" name="response_options['.$resp_type->response_type.'][options][]" value="'.$resp_options->option_value.'" style="margin-top:10px;" > &nbsp;'.$resp_options->option_value.'</label>';
                                    $return_data .= '</div>';
                                    $return_data .= '</div>';
                                }
                            }
                            $return_data .= '</div>';

                            //Extra info Trigggers
                            $return_data .= '<div class="input-group form-group">';
                            $return_data .= '<label class="input-group-addon">Extra Info Trigger</label>';
                            $return_data .= '<select id="extra-info-'.$resp_type->response_type.'" name="response_options['.$resp_type->response_type.'][extra_info_trigger]" class="form-control extra_info_trigger" data-response_type="'.$resp_type->response_type.'" style="width:98%" >';
                            $return_data .= '<option value="">Choose response type</option>';
                            if (!empty($resp_type->response_type_options)) {
                                foreach ($resp_type->response_type_options as $k => $select_resp_ops) {
                                    $return_data .= '<option value="'.$select_resp_ops->option_value.'" '.((strtolower($select_resp_ops->option_value) == strtolower($question_record->extra_info_trigger)) ? "selected=selected" : "") .' >'.$select_resp_ops->option_value.'</option>';
                                }
                            }
                            $return_data .= '</select>';
                            $return_data .= '</div>';
                            $return_data .= '<input id="extra-info-selected-'.$resp_type->response_type.'" type="hidden" name="response_options['.$resp_type->response_type.'][extra_info]" value="please provide further info" />';

                            //Default Response
                            $return_data .= '<div class="input-group form-group">';
                            $return_data .= '<label class="input-group-addon">Default Response</label>';
                            $return_data .= '<select id="default-response-'.$resp_type->response_type.'" name="response_options['.$resp_type->response_type.'][default_response]" class="form-control default_response" data-response_type="'.$resp_type->response_type.'" style="width:98%" >';
                            $return_data .= '<option value="">Choose response type</option>';
                            if (!empty($resp_type->response_type_options)) {
                                foreach ($resp_type->response_type_options as $k => $select_resp_ops) {
                                    $return_data .= '<option value="'.$select_resp_ops->option_value.'" '.((strtolower($select_resp_ops->option_value) == strtolower($question_record->default_response)) ? "selected=selected" : "") .' >'.$select_resp_ops->option_value.'</option>';
                                }
                            }
                            $return_data .= '</select>';
                            $return_data .= '</div>';

                            //Defects Response Trigger
                            $return_data .= '<div class="form-group">';
                            $return_data .= '<label class="text-bold">Is a Defects Response Required for this Question?</label>';
                            $return_data .= '<div class="row">';
                            $return_data .= '<div class="col-md-3 col-sm-6 col-xs-12">';
                            $return_data .= '<div class="radio">';
                            $return_data .= '<label><input type="radio" class="defects_response_required" name="response_options['.$resp_type->response_type.'][defects_response_required]" value="1" style="margin-top:6px;" '.((1 == $question_record->defects_response_required) ? "checked=checked" : "").' data-resp_type="'. $resp_type->response_type .'" > Yes</label>';
                            $return_data .= '</div>';
                            $return_data .= '</div>';

                            $return_data .= '<div class="col-md-3 col-sm-6 col-xs-12">';
                            $return_data .= '<div class="radio">';
                            $return_data .= '<label><input type="radio" class="defects_response_required" name="response_options['.$resp_type->response_type.'][defects_response_required]" value="0" style="margin-top:6px;" '.((1 != $question_record->defects_response_required) ? "checked=checked" : "").' data-resp_type="'. $resp_type->response_type .'" > No</label>';
                            $return_data .= '</div>';
                            $return_data .= '</div>';
                            $return_data .= '</div>';

                            $return_data .= '</div>';

                            $return_data .= '<div class="form-group">';
                            $return_data .= '<div class="defects_response_required_container form-group" style="display:block">';
                            $return_data .= '<div class="row">';
                            $return_data .= '<div class="col-md-12 col-sm-12 col-xs-12">';
                            $return_data .= '<label>Which one of these options should trigger a Defects Response?</label>';
                            $return_data .= '<select id="defects-response-'.$resp_type->response_type.'" name="response_options['.$resp_type->response_type.'][defects_response_trigger]" class="form-control defects_response_trigger" >';
                            $return_data .= '<option value="" >Select type</option>';
                            if (!empty($resp_type->response_type_options)) {
                                foreach ($resp_type->response_type_options as $key => $select_resp_ops) {
                                    $return_data .= '<option value="'.$select_resp_ops->option_value.'" '.((strtolower($select_resp_ops->option_value) == strtolower($question_record->defects_response_trigger)) ? "selected=selected" : "") .' >'.$select_resp_ops->option_value.'</option>';
                                }
                            }
                            $return_data .= '</select>';
                            $return_data .= '</div>';
                            $return_data .= '</div>';
                            $return_data .= '</div>';
                            $return_data .= '</div>';

                            $return_data .= '</div>';
                            break;

                        case 'file':
                        case 'signature':
                            //Do something
                            $return_data .= '<div class="resp_'.$resp_type->response_type.' resp-type-options" style="display:'.$display.'">';
                            $return_data .= '<label class="text-grey input-group push-10-left">Update acceptable file options</label>';
                            $return_data .= '<div class="row">';
                            if (!empty($resp_type->response_type_options)) {
                                foreach ($resp_type->response_type_options as $k => $resp_options) {
                                    $return_data .= '<div class="col-md-3 col-sm-4 col-xs-12">';
                                    $return_data .= '<div class="radio">';
                                    $return_data .= '<label><input '.(in_array($resp_options->option_value, $selected_options) ? 'checked' : '').' type="checkbox" name="response_options['.$resp_type->response_type.'][options][]" value="'.$resp_options->option_value.'" style="margin-top:6px;" >  &nbsp;'.$resp_options->option_value.'</label>';
                                    $return_data .= '</div>';
                                    $return_data .= '</div>';
                                }
                            }
                            $return_data .= '</div>';
                            $return_data .= '</div>';
                            break;
                    }
                }
            }

            $return_data .= '</div>';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">File/Photo Upload Req?</label>';
            $return_data .= '<select name="files_required" class="form-control" style="width:95%">';
            $return_data .= '<option value="">Choose section</option>';
            $return_data .= '<option value="1" '.(($question_record->files_required == 1) ? "selected=selected" : "") .' >Yes</option>';
            $return_data .= '<option value="0" '.(($question_record->files_required != 1) ? "selected=selected" : "") .' >No</option>';
            $return_data .= '</select>';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Section Name</label>';
            $return_data .= '<select id="modal_section_id" name="section" class="form-control" style="width:95%">';
            $return_data .= '<option value="">Choose section</option>';
            if (!empty($evidoc_type_sections)) {
                foreach ($evidoc_type_sections as $sec => $section) {
                    $return_data .= '<option value="'.$section->section_name.'" '.((strtolower($section->section_name) == strtolower($question_record->section)) ? "selected=selected" : "") .' >'.$section->section_name.'</option>';
                }
            }
            $return_data .= '</select>';
            $return_data .= '<span>';
            $return_data .= '<div id="evidoc-section-quick-add" style="margin-top:4px" class="pointer" title="Quick Add new section option"><span class="pull-right"><i class="far fa-plus-square fa-2x text-green"></i></span></div>';
            $return_data .= '</span>';
            $return_data .= '</div>';
        }
        return $return_data;
    }

    /** Update Question Record **/
    public function update_question($question_id = false, $page = 'details')
    {
        $color_class  = 'red';
        $return_data = [
            'status'=>0
        ];

        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $question_id = ($this->input->post('question_id')) ? $this->input->post('question_id') : (!empty($question_id) ? $question_id : null);
            $postdata 	  		 = array_merge(['account_id'=>$this->user->account_id, 'question_id'=>$question_id ], $this->input->post());
            $evidoc_question = $this->webapp_service->api_dispatcher($this->api_end_point.'audit/update_question', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  		 = (isset($evidoc_question->evidoc_question)) ? $evidoc_question->evidoc_question : null;
            $message	  		 = (isset($evidoc_question->message)) ? $evidoc_question->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status']			= 1;
                $return_data['evidoc_question']	= $result;
                $color_class			= 'auto';
            }
            $return_data['status_msg'] = '<span class="text-'.$color_class.'">'.$message.'</span>';
        }

        print_r(json_encode($return_data));
        die();
    }

    /** Add new Asset Type **/
    public function add_asset_type()
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
            $postdata 	  	= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $asset_type 	= $this->webapp_service->api_dispatcher($this->api_end_point.'asset/add_asset_type', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  	= (isset($asset_type->asset_type)) ? $asset_type->asset_type : null;
            $message	  	= (isset($asset_type->message)) ? $asset_type->message : 'Oops! There was an error processing your request.';

            if (!empty($result)) {
                $return_data['status'] 		= 1;
                $return_data['asset_type']  = $result;
                $text_color 				= 'auto';
            }
            $return_data['status_msg'] = '<span class="text-'.$text_color.'">'.$message.'</span>';
        }

        print_r(json_encode($return_data));
        die();
    }


    /**
    * Delete Question record
    **/
    public function delete_question($question_id = false)
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
            $postdata 	  	= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $drop_question = $this->webapp_service->api_dispatcher($this->api_end_point.'audit/delete_question', $postdata, ['auth_token'=>$this->auth_token], true);
            $result		    = (isset($drop_question->status)) ? $drop_question->status : null;
            $message	    = (isset($drop_question->message)) ? $drop_question->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] = 1;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }

    /** Delete Document Resource **/
    public function delete_document($document_id = false, $page = 'details')
    {
        $return_data = [
            'status'=>0
        ];

        $section 		= ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $document_id 	= ($this->input->post('document_id')) ? $this->input->post('document_id') : (!empty($document_id) ? $document_id : null);

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postdata 	  		= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $delete_document	= $this->webapp_service->api_dispatcher($this->api_end_point.'document_handler/delete_document', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  		= (isset($delete_document->status)) ? $delete_document->status : null;
            $message	  		= (isset($delete_document->message)) ? $delete_document->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status']		= 1;
                $return_data['document'] 	= $result;
            }
            $return_data['status_msg'] 		= $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    //Manage Categories - Overview page
    public function categories($category_id = false, $page = 'details')
    {
        $toggled	= (!empty($this->input->get('toggled')) ? $this->input->get('toggled') : false);
        $section 	= (!empty($page)) ? $page : (!empty($this->input->get('page')) ? $this->input->get('page') : 'details');
        $category_id  	= (!empty($category_id)) ? $category_id : (!empty($this->input->get('category_id')) ? $this->input->get('category_id') : ((!empty($this->input->get('category_id')) ? $this->input->get('category_id') : null)));

        if (!empty($category_id)) {
            $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
            if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
                $this->_render_webpage('errors/access-denied', false);
            } else {
                $default_params 	= $params = [ 'account_id'=>$this->user->account_id, 'where'=>[ 'category_id'=>$category_id ] ];
                $category_details 	= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/audit_categories', $params, [ 'auth_token'=>$this->auth_token ], true);

                if (!empty($category_details->audit_categories)) {
                    $data['category_details']  		= $category_details->audit_categories;
                    $linked_audit_types  		= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/linked_audit_types', ['account_id'=>$this->user->account_id, 'category_id'=>$category_id ], ['auth_token'=>$this->auth_token], true);
                    $data['linked_audit_types']	= (isset($linked_audit_types->linked_audit_types)) ? $linked_audit_types->linked_audit_types : null;

                    $assigned_operatives  		 	= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/assigned_people', ['account_id'=>$this->user->account_id, 'category_id'=>$category_id ], ['auth_token'=>$this->auth_token], true);
                    $data['assigned_operatives']	= (isset($assigned_operatives->assigned_people)) ? $assigned_operatives->assigned_people : null;

                    $this->_render_webpage('audit/categories/category_details_profile', $data);
                } else {
                    redirect('webapp/audit/categories', 'refresh');
                }
            }
        } else {
            $this->_render_webpage('audit/categories/manage_categories', false, false, true);
        }
    }

    /*
    * Categories List / Search
    */
    public function categories_list($page = 'details')
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

            $search_result	= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/audit_categories', $postdata, [ 'auth_token'=>$this->auth_token ], true);

            $audit_categories		= (isset($search_result->audit_categories)) ? $search_result->audit_categories : null;

            if (!empty($audit_categories)) {
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

                $return_data = $this->load_categories_view($audit_categories);
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
    * Category list views
    */
    private function load_categories_view($categories_data)
    {
        $return_data = '';
        if (!empty($categories_data)) {
            foreach ($categories_data as $k => $category) {
                $return_data .= '<tr>';
                $return_data .= '<td><a href="'.base_url('/webapp/audit/categories/'.$category->category_id).'" >'.$category->category_name.'</a></td>';
                $return_data .= '<td>'.$category->description.'</td>';
                $return_data .= '<td>'.(!empty($category->category_group) ? $category->category_group : '').'</td>';
                $return_data .= '<td>'.(!empty($category->is_active) ? 'Active' : 'Disabled').'</td>';
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
    * Add New Category
    */
    public function new_category($page = 'details')
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $disciplines	 			= $this->webapp_service->api_dispatcher($this->api_end_point.'discipline/account_disciplines', [ 'account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true);
            $data['disciplines']		= (isset($disciplines->account_disciplines)) ? $disciplines->account_disciplines : null;

            $this->_render_webpage('audit/categories/category_add_new', $data);
        }
    }


    /**
    * Add a new Evidoc Category
    **/
    public function add_category()
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
            $postdata 	  	 = array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $evidoc_category = $this->webapp_service->api_dispatcher($this->api_end_point.'audit/add_category', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  	 = (isset($evidoc_category->evidoc_category)) ? $evidoc_category->evidoc_category : null;
            $message	  	 = (isset($evidoc_category->message)) ? $evidoc_category->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] 	 = 1;
                $return_data['category'] = $result;
                $text_color 			 = 'auto';
            }
            $return_data['status_msg'] = '<span class="text-'.$text_color.'">'.$message.'</span>';
        }

        print_r(json_encode($return_data));
        die();
    }


    /** Update Category Profile Details **/
    public function update_category($category_id = false, $page = 'details')
    {
        $return_data = [
            'status'=>0
        ];

        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $category_id = ($this->input->post('category_id')) ? $this->input->post('category_id') : (!empty($category_id) ? $category_id : null);

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
            $update_category= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/update_category', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  = (isset($update_category->evidoc_category)) ? $update_category->evidoc_category : null;
            $message	  = (isset($update_category->message)) ? $update_category->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] 		= 1;
                $return_data['category'] 	= $result;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    /**
    * Delete Category Record
    **/
    public function delete_category($category_id = false)
    {
        $return_data = [
            'status'=>0
        ];

        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $category_id = ($this->input->post('category_id')) ? $this->input->post('category_id') : (!empty($category_id) ? $category_id : null);

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_delete) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postdata 	  		= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $delete_category_item 	= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/delete_category', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  		= (isset($delete_category_item->status)) ? $delete_category_item->status : null;
            $message	  		= (isset($delete_category_item->message)) ? $delete_category_item->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] = 1;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    /**
    * Re-Order Questions
    **/
    public function reorder_questions($page = 'details')
    {
        $return_data = [
            'status'=>0
        ];

        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        } else {
            # Check module-item access
            $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

            if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
                $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            } else {
                $postdata 	  		= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
                $reorder_items 		= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/reorder_questions', $postdata, ['auth_token'=>$this->auth_token]);
                $result		  		= (isset($reorder_items->status)) ? $reorder_items->status : null;
                $message	  		= (isset($reorder_items->message)) ? $reorder_items->message : 'Oops! There was an error processing your request.';
                if (!empty($result)) {
                    $return_data['status'] = 1;
                }
                $return_data['status_msg'] = $message;
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    /**
    * 	Delete Evidoc Type(set as archived )
    **/
    public function delete_evidoc_type($evidoc_type_id = false, $page = 'details')
    {
        $return_data = [
            'status' => 0
        ];

        $section 		= ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
        $evidoc_type_id = ($this->input->post('audit_type_id')) ? $this->input->post('audit_type_id') : (!empty($audit_type_id) ? $audit_type_id : null);

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && !$item_access && empty($item_access->can_delete)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            ;
        } else {
            if ($evidoc_type_id) {
                $postdata 	  	= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
                $api_call 		= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/delete_evidoc_type', $postdata, ['auth_token'=>$this->auth_token]);
                $result		  	= (isset($api_call->status)) ? $api_call->status : null;
                $message	  	= (isset($api_call->message)) ? $api_call->message : 'Something went wrong, please try again!';
                if (!empty($result)) {
                    $return_data['status'] = 1;
                }
                $return_data['status_msg'] = $message;
            } else {
                $return_data['status_msg'] = 'Invalid Evidoc type ID!';
            }
        }

        print_r(json_encode($return_data));
        die();
    }
}
