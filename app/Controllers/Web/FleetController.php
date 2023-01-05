<?php

namespace App\Controllers\Web;

use App\Extensions\MX\Controller as MX_Controller;
use App\Models\Service\FleetModel;

class FleetController extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        $this->module_id 	= $this->webapp_service->_get_module_id($this->router->fetch_class());
        $this->load->library('pagination');
		$this->fleet_service = new FleetModel();
    }

    public function index()
    {
        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);
        if (!$this->user->is_admin && !$module_access) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            redirect('webapp/fleet/vehicles', 'refresh');
        }
    }

    /** Get list of vehicles **/
    public function vehicles($vehicle_id = false)
    {
        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);
        if (!$this->user->is_admin && !$module_access) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $postdata = [];
            $postdata["account_id"]		= $this->user->account_id;
            $data['vehicle_statuses']	= ['1'=> (object) array( 'status_name' => 'Please, add statuses' )];
            $url						= 'fleet/vehicle_statuses';
            $API_result					= $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $data['vehicle_statuses']	= (($API_result->status == true) && !empty($API_result->statuses)) ? ($API_result->statuses) : ($data['vehicle_statuses']) ;

            $url						= 'fleet/simple_stats';
            $API_result					= $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $data['simple_stats']		= (($API_result->status == true) && !empty($API_result->simple_stats)) ? ($API_result->simple_stats) : (false) ;

            $this->_render_webpage('fleet/index', $data);
        }
    }

    /** View vehicle profile **/
    public function profile($vehicle_id = false, $page = 'details')
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } elseif ($vehicle_id) {
            $vehicle_details		 = $this->webapp_service->api_dispatcher($this->api_end_point.'fleet/vehicles', ['account_id'=>$this->user->account_id,'vehicle_id'=>$vehicle_id], ['auth_token'=>$this->auth_token], true);
            $data['vehicle_details'] = (isset($vehicle_details->vehicles[0])) ? $vehicle_details->vehicles[0] : null;

            if (!empty($data['vehicle_details'])) {
                ## overview attributes
                $data["overview_attributes"] 	  	= false;
                $postdata["account_id"] = $this->user->account_id;
                $postdata["where"] 		= [
                    "module_id"		=> $this->module_id,
                    "zone_id"		=> "1",
                ];
                $API_call	 	  				= $this->webapp_service->api_dispatcher($this->api_end_point.'attribute/attributes', $postdata, ['auth_token'=>$this->auth_token], true);
                $data['overview_attributes']	= (!empty($API_call->attributes)) ? $API_call->attributes : null;
                ## overview attributes - end

                $run_admin_check	= false;
                #Get allowed access for the logged in user
                $data['permissions']= $item_access;
                $data['active_tab']	= $page;

                $module_items 		= $this->webapp_service->api_dispatcher($this->api_end_point.'access/account_modules_items', ['account_id'=>$this->user->account_id, 'module_id'=>$this->module_id ], ['auth_token'=>$this->auth_token], true);
                $data['module_tabs']= (isset($module_items->module_items)) ? $module_items->module_items : null;
                $reordered_tabs 		 = reorder_tabs($data['module_tabs']);
                $data['module_tabs'] 	 = (!empty($reordered_tabs['module_tabs'])) ? $reordered_tabs['module_tabs'] : $data['module_tabs'];
                $data['more_list_active']= (!empty($reordered_tabs['more_list']) && in_array($page, $reordered_tabs['more_list'])) ? true : false;

                $postdata = [];
                $postdata["account_id"]		= $this->user->account_id;
                $postdata["vehicle_id"]		= $vehicle_id;

                $data["vehicle_owner_types"] = [
                    [
                        "owner_type_id" 	=> 1,
                        "owner_type_name" 	=> "Private"
                    ],
                    [
                        "owner_type_id" 	=> 2,
                        "owner_type_name" 	=> "Company"
                    ],
                ];

                switch($page) {
                    case 'events':
                        $url					= 'fleet/vehicle_events';
                        $API_result				= $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['vehicle_events'] = ((!empty($API_result->vehicle_events)) && ($API_result->status == true)) ? ($API_result->vehicle_events) : (false) ;

                        $url					= 'fleet/event_types';
                        $postdata["ordered"]	= 'yes';
                        $API_result				= $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['event_types']	= ((!empty($API_result->event_types)) && ($API_result->status == true)) ? ($API_result->event_types) : (false) ;

                        $url					= 'fleet/event_categories';
                        $postdata["ordered"]	= 'yes';
                        $API_result				= $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['event_categories']	= ((!empty($API_result->event_categories) && ($API_result->status == true))) ? ($API_result->event_categories) : (false) ;

                        $url					= 'fleet/event_statuses';
                        $API_result				= $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['event_statuses'] = ((!empty($API_result->event_statuses)) && ($API_result->status == true)) ? ($API_result->event_statuses) : (false) ;

                        $url							= 'fleet/event_tracking_logs';
                        $API_result						= $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['event_tracking_logs'] 	= ((!empty($API_result->event_tracking_logs)) && ($API_result->event_tracking_logs == true)) ? ($API_result->event_tracking_logs) : (false) ;

                        $data['include_page'] 	= 'vehicle_events.php';
                        break;

                    case 'evidocs':
                    case 'audits':
                        $vehicle_audits	  	    = $this->webapp_service->api_dispatcher($this->api_end_point.'audit/audits', ['account_id'=>$this->user->account_id, 'vehicle_id'=>$vehicle_id, 'vehicle_reg'=>urlencode($data['vehicle_details']->vehicle_reg) ], ['auth_token'=>$this->auth_token], true);
                        $data['vehicle_audits'] = (isset($vehicle_audits->audits)) ? $vehicle_audits->audits : null;
                        $data['include_page'] 	= 'vehicle_audits.php';
                        break;

                    case 'driver':
                        $url						= 'fleet/vehicle_drivers';
                        $postdata['ordered']		= 'yes';
                        $API_result					= $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['vehicle_drivers'] 	= (($API_result->status == true) && !empty($API_result->vehicle_drivers)) ? ($API_result->vehicle_drivers) : (false) ;
                        $vehicle_audits	  	    = $this->webapp_service->api_dispatcher($this->api_end_point.'audit/audits', ['account_id'=>$this->user->account_id, 'vehicle_id'=>$vehicle_id, 'vehicle_reg'=>urlencode($data['vehicle_details']->vehicle_reg) ], ['auth_token'=>$this->auth_token], true);
                        $data['vehicle_audits'] = (isset($vehicle_audits->audits)) ? $vehicle_audits->audits : null;

                        $data['driver_history'] 	= (!empty($data['vehicle_details']->driver_history)) ? $data['vehicle_details']->driver_history : null;
                        $data['include_page'] 		= 'vehicle_driver_history.php';
                        break;

                    case 'documents':
                        $audit_documents		= $this->webapp_service->api_dispatcher($this->api_end_point.'document_handler/document_list', ['account_id'=>$this->user->account_id, 'vehicle_reg'=>urlencode($data['vehicle_details']->vehicle_reg), 'document_group'=>'fleet' ], ['auth_token'=>$this->auth_token], true);
                        $data['audit_documents']= (isset($audit_documents->documents->{$this->user->account_id})) ? $audit_documents->documents->{$this->user->account_id} : null;

                        $data['include_page'] 	= 'vehicle_documents.php';
                        break;

                    case 'history':
                        $url						= 'fleet/vehicle_change_logs';
                        $API_result					= $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['vehicle_change_logs'] 	= (($API_result->status == true) && !empty($API_result->vehicle_change_logs)) ? ($API_result->vehicle_change_logs) : (false) ;

                        $data['include_page'] 	= 'vehicle_history.php';
                        break;

                    case 'cost':
                        $cost_tracking	  	  	= $this->webapp_service->api_dispatcher($this->api_end_point.'cost/cost_items', ['account_id'=>$this->user->account_id, 'where'=>[ 'vehicle_reg'=>$data['vehicle_details']->vehicle_reg ] ], ['auth_token'=>$this->auth_token], true);
                        $data['cost_tracking'] 	= (isset($cost_tracking->cost_items)) ? $cost_tracking->cost_items : null;

                        $cost_item_types	  	= $this->webapp_service->api_dispatcher($this->api_end_point.'cost/cost_item_types', ['account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true);
                        $data['cost_item_types']= (isset($cost_item_types->cost_item_types)) ? $cost_item_types->cost_item_types : null;

                        $data['include_page'] 	= 'vehicle_cost_tracking.php';
                        break;

                    case 'attributes':
                        $data['show_information']		= 0;

                        $data['excluded_response_types'] = ["file", "signature"];
                        $data['response_types_w_options'] = ["radio","checkbox","select" ];

                        $postdata 						= false;
                        $module_item_id					= false;

                        $postdata["account_id"] 		= $this->user->account_id;
                        $postdata["module_id"] 			= $this->module_id;
                        $postdata["module_item_name"] 	= $page;
                        $API_call	 	  				= $this->webapp_service->api_dispatcher($this->api_end_point.'access/module_items', $postdata, ['auth_token'=>$this->auth_token], true);
                        $module_item					= (!empty($API_call->module_items)) ? $API_call->module_items : null;
                        $module_item_id					= $module_item[0]->module_item_id;
                        $data['module_item_id']			= $module_item_id;
                        $data['module_id']				= $this->module_id;

                        $data['manage_attributes']		= false;
                        $postdata 						= false;
                        $postdata["account_id"] 		= $this->user->account_id;
                        $postdata["where"] 				= [
                            "module_item_id"	=> (!empty($module_item_id)) ? ( int ) $module_item_id : false,
                            "module_id"			=> $this->module_id,
                            "zone_id"			=> "2",
                        ];
                        $API_call	 	  				= $this->webapp_service->api_dispatcher($this->api_end_point.'attribute/attributes', $postdata, ['auth_token'=>$this->auth_token], true);
                        $data['manage_attributes']		= (!empty($API_call->attributes)) ? $API_call->attributes : null;

                        ## responses to the attributes
                        $data['manage_responses']		= false;
                        $postdata 						= false;
                        $postdata["account_id"] 		= $this->user->account_id;
                        $postdata["where"] 				= [
                            "module_id"			=> $this->module_id,
                            "module_item_id"	=> (!empty($module_item_id)) ? ( int ) $module_item_id : false,
                            "profile_id"		=> $vehicle_id,
                        ];
                        $API_call	 	  				= $this->webapp_service->api_dispatcher($this->api_end_point.'attribute/responses', $postdata, ['auth_token'=>$this->auth_token], true);
                        $data['manage_responses']		= (!empty($API_call->responses)) ? $API_call->responses : null;

                        ## get attribute sections
                        $data['attribute_sections']		= false;
                        $postdata 						= false;
                        $postdata["account_id"] 		= $this->user->account_id;
                        $postdata["where"] 				= [
                            "module_id"			=> $this->module_id,
                            "module_item_id"	=> (!empty($module_item_id)) ? ( int ) $module_item_id : false,
                            /* "organized"			=> false */
                        ];
                        $API_call	 	  				= $this->webapp_service->api_dispatcher($this->api_end_point.'attribute/sections', $postdata, ['auth_token'=>$this->auth_token], true);
                        $data['attribute_sections']		= (!empty($API_call->status) && !empty($API_call->sections)) ? $API_call->sections : false ;

                        ## get attribute groups
                        $data['attribute_groups']		= false;
                        $postdata 						= false;
                        $postdata["account_id"] 		= $this->user->account_id;
                        $postdata["where"] 				= [
                            "module_id"			=> $this->module_id,
                            "module_item_id"	=> (!empty($module_item_id)) ? ( int ) $module_item_id : false,
                            "organized"			=> true
                        ];
                        $API_call	 	  				= $this->webapp_service->api_dispatcher($this->api_end_point.'attribute/groups', $postdata, ['auth_token'=>$this->auth_token], true);
                        $data['attribute_groups']		= (!empty($API_call->status) && !empty($API_call->groups)) ? $API_call->groups : false ;

                        ## get response types
                        $data['response_types']			= false;
                        $postdata 						= false;
                        $postdata["account_id"] 		= $this->user->account_id;
                        $postdata["where"] 				= [];
                        $API_call	 	  				= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/response_types', $postdata, ['auth_token'=>$this->auth_token], true);
                        $data['response_types']		= (!empty($API_call->status) && !empty($API_call->response_types)) ? $API_call->response_types : false ;

                        $data['include_page'] 			= 'vehicle_attributes.php';
                        break;

                    case 'details':
                    default:
                        $run_admin_check			= false;
                        $data['vehicle_suppliers']	= ['1'=> (object) array( 'supplier_name' => 'Please, add suppliers' )];
                        $url						= 'fleet/vehicle_suppliers';
                        $API_result					= $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['vehicle_suppliers']	= (($API_result->status == true) && !empty($API_result->suppliers)) ? ($API_result->suppliers) : ($data['vehicle_suppliers']) ;

                        $data['vehicle_statuses']	= ['1'=> (object) array( 'status_name' => 'Please, add statuses' )];
                        $url						= 'fleet/vehicle_statuses';
                        $API_result					= $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['vehicle_statuses']	= (($API_result->status == true) && !empty($API_result->statuses)) ? ($API_result->statuses) : ($data['vehicle_statuses']) ;

                        $users		  	  			= $this->webapp_service->api_dispatcher($this->api_end_point.'user/users', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true);
                        $data['drivers']  	  		= (isset($users->users)) ? $users->users : null;

                        $data['include_page'] 		= 'vehicle_details.php';
                        break;
                }
            }

            //Run the admin check if tab needs only admin
            if (!empty($run_admin_check)) {
                if ((!admin_check($this->user->is_admin, false, (!empty($data['permissions']) ? $data['permissions']->is_admin : false)))) {
                    $data['admin_no_access'] = true;
                }
            }

            $this->_render_webpage('fleet/profile', $data, '');
        } else {
            redirect('webapp/audit', 'refresh');
        }
    }

    /** Create new vehicle **/
    public function create($page = 'details')
    {
        $section = (!empty($page)) ? $page : 'details';

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $postset 					= $this->input->post();
            if (!empty($postset)) {
                $postdata = [];
                $postdata 					= $postset;
                $postdata["account_id"]		= $this->user->account_id;
                $url 						= 'fleet/create';
                $API_result					= $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if ((!empty($API_result->status) && ($API_result->status == true))) {
                    $result = [
                        'status'		=> $API_result->status,
                        'message'		=> $API_result->message,
                        'vehicle_id'	=> $API_result->new_vehicle->vehicle_id,
                    ];
                    print_r(json_encode($result));
                    die();
                } else {
                    if ((!empty($API_result->message))) {
                        $this->session->set_flashdata('feedback', $API_result->message);
                    }
                    redirect('webapp/fleet/create/', 'refresh');
                }
            }

            $postdata['account_id'] 	= $this->user->account_id;

            $data['vehicle_statuses']	= ['1'=>'Returned', '2'=>'Assigned','3'=>'Un-assigned','4'=>'Garage'];
            $postdata['ordered'] 		= 'yes';
            $url 						= 'fleet/vehicle_statuses';
            $API_result					= $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            if ((!empty($API_result->status)) && ($API_result->status == 1)) {
                $data['vehicle_statuses'] = (!empty($API_result->statuses)) ? $API_result->statuses : $data['vehicle_statuses'] ;
            }

            $data['vehicle_suppliers']	= ['1'=>'Please add suppliers'];
            $url 						= 'fleet/vehicle_suppliers';
            $API_result					= $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            if ((!empty($API_result->status)) && ($API_result->status == 1)) {
                $data['vehicle_suppliers'] = (!empty($API_result->suppliers)) ? $API_result->suppliers : $data['vehicle_suppliers'] ;
            }

            $this->_render_webpage('fleet/vehicle_create_new', $data);
        }
    }

    /*
    * 	Audit lookup / search
    */
    public function lookup($page = 'details')
    {
        $return_data = '';

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
            $search_term   = ($this->input->post('search_term')) ? $this->input->post('search_term') : false;

            // temporary disabled - The counters do not match the vehicle stauses.
            //$vehicle_statuses = ( $this->input->post( 'vehicle_statuses' ) ) ? $this->input->post( 'vehicle_statuses' ) : false;

            $vehicle_statuses = false;

            $limit		   = ($this->input->post('limit')) ? $this->input->post('limit') : DEFAULT_LIMIT;
            $start_index   = ($this->input->post('start_index')) ? $this->input->post('start_index') : 0;
            $offset		   = (!empty($start_index)) ? (($start_index - 1) * $limit) : DEFAULT_OFFSET;
            $order_by	   = false;
            $where		   = false;

            #prepare postdata
            $postdata = [
                'account_id'		=> $this->user->account_id,
                'search_term'		=> urlencode($search_term),
                'vehicle_statuses'	=> $vehicle_statuses,
                'where'				=> $where,
                'order_by'			=> $order_by,
                'limit'				=> $limit,
                'offset'			=> $offset
            ];

            $postdata 	  	= array_merge(['account_id'=>$this->user->account_id], $postdata);
            $url 			= 'fleet/lookup';
            $API_result		= $this->ssid_common->api_call($url, $postdata, $method = 'GET');

            $vehicles		= (isset($API_result->vehicles)) ? $API_result->vehicles : null;
            $message	 	= (isset($API_result->message)) ? $API_result->message : 'Oops! There was an error processing your request.';

            if (!empty($vehicles)) {
                ## Create pagination
                $counters 		= $this->fleet_service->get_total_vehicles($this->user->account_id, $search_term, $vehicle_statuses, $where);//Direct access to count, this should only return counters
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

                $return_data = $this->load_vehicles_view($vehicles);

                if (!empty($pagination)) {
                    $return_data .= '<tr><td colspan="6" style="padding: 0;">';
                    $return_data .= $page_display.$pagination;
                    $return_data .= '</td></tr>';
                }
            } else {
                $return_data .= '<br/>';
                $return_data .= (isset($search_result->message)) ? $search_result->message : 'No records found';
            }
        }

        print_r($return_data);
        die();
    }

    /*
    * Prepare vehicles views
    */
    private function load_vehicles_view($vehicles_data)
    {
        $return_data = '';
        if (!empty($vehicles_data)) {
            foreach ($vehicles_data as $k => $vehicle_details) {
                $return_data .= '<tr>';
                $return_data .= '<td><a href="'.base_url('/webapp/fleet/profile/'.$vehicle_details->vehicle_id).'" >'.$vehicle_details->vehicle_reg.'</a></td>';
                $return_data .= '<td>'.$vehicle_details->vehicle_make.'</td>';
                $return_data .= '<td>'.$vehicle_details->vehicle_model.'</td>';
                $return_data .= '<td>'.$vehicle_details->year.'</td>';
                $return_data .= '<td>'.$vehicle_details->driver_full_name.'</td>';
                $return_data .= '<td>'.$vehicle_details->status_name.'</td>';
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
    * 	Update Vehicle Details
    **/
    public function update_vehicle($vehicle_id = false, $page = 'details')
    {
        $return_data = [
            'status' => 0
        ];

        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
        $vehicle_id = ($this->input->post('vehicle_id')) ? $this->input->post('vehicle_id') : $vehicle_id ;
        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postdata 	  	= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $url 			= 'fleet/update';
            $API_result		= $this->ssid_common->api_call($url, $postdata, $method = 'POST');
            $result		  = (isset($API_result->updated_vehicle)) ? $API_result->updated_vehicle : null;
            $message	  = (isset($API_result->message)) ? $API_result->message : 'Oops! There was an error processing your request.';

            if (!empty($result)) {
                $return_data['status']	= 1;
                $return_data['updated_vehicle'] = $result	;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    /*
    * Load an audit record
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

    private function load_audit($audit_record = false)
    {
        $audit = '';
        if (!empty($audit_record)) {
            $audit .= '<table style="width:100%">';
            $audit .= '<tr><th width="30%">Audit ID</th><td>'.$audit_record->audit_id.'</td></tr>';
            $audit .= '<tr><th width="30%">Audit Type</th><td>'.$audit_record->audit_type.'</td></tr>';
            $audit .= '<tr><th>Date Audited</th><td>'.date('d-m-Y', strtotime($audit_record->date_created)).'</td></tr>';
            $audit .= '<tr><th>Audited by</th><td>'.$audit_record->created_by.'</td></tr>';
            $audit .= '<tr><th>Questions Completed</th><td><i class="far  '.(($audit_record->questions_completed == 1) ? " fa-check-circle text-green " : " fa-times-circle text-red").' "></i></td></tr>';
            $audit .= '<tr><th>Documents Uploaded</th><td><i class="far  '.(($audit_record->documents_uploaded == 1) ? " fa-check-circle text-green " : " fa-times-circle text-red").' "></i></td></tr>';
            $audit .= '<tr><th>Signature Uploaded</th><td><i class="far  '.(($audit_record->signature_uploaded == 1) ? " fa-check-circle text-green " : " fa-times-circle text-red").' "></i></td></tr>';
            $audit .= '<tr><th colspan="2">&nbsp;</th></tr>';
            $audit .= '<tr><th colspan="2"><span style="font-weight:400">RESPONSES</span><hr></th></tr>';
            $audit .= '<tr><td colspan="2"><table style="width:100%;display:table;font-size:90%">';
            $audit .= '<tr><th width="10%">ID</th><th width="50%">Audit Question</th><th width="20%">Response</th><th width="20%">Extra Info</th></tr>';
            foreach ($audit_record->audit_responses as $k=>$audit_item) {
                $k++;
                $audit .= '<tr><td>'.$k.'</td><td>'.$audit_item->question.'</td><td>'.$audit_item->response.'</td><td>'.$audit_item->response_extra.'</td></tr>';
            }
            $audit .= '</table></td></tr>';
            $audit .= '</table>';
        }
        return $audit;
    }


    /** Create new audit **/
    public function create_event($page = 'create_event')
    {
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $data['active_class'] 		= 'create_event';
        $data['feedback'] 			= !empty($this->session->flashdata('feedback')) ? ($this->session->flashdata('feedback')) : false ;
        $referring_page				= $this->input->post('referring_page');
        $postset 					= $this->input->post('postdata');

        if (!empty($postset)) {
            $postdata = [];
            $postdata = $postset;
            $postdata["account_id"]		= $postset['account_id'];
            unset($postset['account_id']);
            $postdata["vehicle_id"]	= $postset['vehicle_id'];
            unset($postset['vehicle_id']);

            $url 						= 'fleet/create_vehicle_event';
            $API_result					= $this->ssid_common->api_call($url, $postdata, $method = 'POST');

            if ((!empty($API_result->message))) {
                $this->session->set_flashdata('feedback', $API_result->message);
                $this->session->set_flashdata('referring_page', $referring_page);
            }
        }

        redirect('webapp/fleet/profile/'.$postdata["vehicle_id"].'/events', 'refresh');
    }


    /*
    *	Function to create a new Contract Profile
    */
    public function add_workflow()
    {
        $data['active_class'] 		= 'add_contract';
        $data['feedback'] 			= !empty($this->session->flashdata('feedback')) ? ($this->session->flashdata('feedback')) : false ;
        $referring_page				= $this->input->post('referring_page');
        $postset 					= $this->input->post('postdata');


        if (!empty($postset)) {
            $postdata = [];
            $postdata = $postset;
            $postdata["account_id"]		= $postset['account_id'];
            unset($postset['account_id']);
            $postdata["contract_id"]	= $postset['contract_id'];
            unset($postset['contract_id']);

            $url 						= 'contract/add_workflow';
            $API_result					= $this->ssid_common->api_call($url, $postdata, $method = 'POST');

            if ((!empty($API_result->new_workflow))) {
                if ((!empty($API_result->message))) {
                    $this->session->set_flashdata('feedback', $API_result->message);
                    $this->session->set_flashdata('referring_page', $referring_page);
                }
                redirect('webapp/contract/profile/'.$postdata['contract_id'], 'refresh');
            } else {
                if ((!empty($API_result->message))) {
                    $this->session->set_flashdata('feedback', $API_result->message);
                    $this->session->set_flashdata('referring_page', $referring_page);
                }
                redirect('webapp/Contract/profile/'.$postdata['contract_id'], 'refresh');
            }
        }

        redirect('webapp/contract/dashboard', 'refresh');
    }


    /**
    * 	Delete Vehicle
    **/
    public function delete_vehicle($vehicle_id = false, $page = 'details')
    {
        $return_data = [
            'status' => 0
        ];

        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
        $vehicle_id = ($this->input->post('vehicle_id')) ? $this->input->post('vehicle_id') : $vehicle_id ;
        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_delete) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postdata 	  	= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $url 			= 'fleet/delete';
            $API_result		= $this->ssid_common->api_call($url, $postdata, $method = 'POST');
            $result		  = (isset($API_result->vehicle_deleted)) ? $API_result->vehicle_deleted : null;
            $message	  = (isset($API_result->message)) ? $API_result->message : 'Oops! There was an error processing your request.';

            if (!empty($result)) {
                $return_data['status']	= 1;
                $return_data['vehicle_deleted'] = $result;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }



    /**
    * 	Remove the driver from the Vehicle (unassign)
    **/
    public function remove_driver($vehicle_id = false, $page = 'details')
    {
        $return_data = [
            'status' => 0
        ];

        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
        $vehicle_id = ($this->input->post('vehicle_id')) ? $this->input->post('vehicle_id') : $vehicle_id ;
        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postdata 	  	= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $url 			= 'fleet/remove_driver_from_vehicle';
            $API_result		= $this->ssid_common->api_call($url, $postdata, $method = 'POST');
            $result		  = (isset($API_result->removed_driver)) ? $API_result->removed_driver : null;
            $message	  = (isset($API_result->message)) ? $API_result->message : 'Oops! There was an error processing your request.';

            if (!empty($result)) {
                $return_data['status']	= 1;
                $return_data['removed_driver'] = $result;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }

    /**
    * 	Assign the driver to the Vehicle
    **/
    public function assign_driver($vehicle_id = false, $page = 'details')
    {
        $return_data = [
            'status' => 0
        ];

        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
        $vehicle_id = ($this->input->post('vehicle_id')) ? $this->input->post('vehicle_id') : $vehicle_id ;
        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_can) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postdata 	  	= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $url 			= 'fleet/assign_driver_to_vehicle';
            $API_result		= $this->ssid_common->api_call($url, $postdata, $method = 'POST');
            $result		  = (isset($API_result->assigned_driver)) ? $API_result->assigned_driver : null;
            $message	  = (isset($API_result->message)) ? $API_result->message : 'Oops! There was an error processing your request.';

            if (!empty($result)) {
                $return_data['status']	= 1;
                $return_data['assigned_driver'] = $result;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    public function get_event_tracking_logs($event_id = false, $page = "events")
    {
        $return_data = [
            'status' => 0
        ];

        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
        $event_id = ($this->input->post('event_id')) ? $this->input->post('event_id') : $event_id ;
        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postdata 	  	= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            if (!empty($event_id)) {
                $postdata['event_id']	= $event_id;
            }

            $url 			= 'fleet/event_tracking_logs';
            $API_result		= $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $result		  = (isset($API_result->event_tracking_logs)) ? $API_result->event_tracking_logs : null;
            $message	  = (isset($API_result->message)) ? $API_result->message : 'Oops! There was an error processing your request.';

            if (!empty($result)) {
                $return_data['status']	= 1;
                $return_data['event_tracking_logs'] = $this->load_logs_view($result);
            }
            $return_data['status_msg'] = $message;

            ## $return_data = $this->load_vehicles_view( $vehicles );
        }

        print_r(json_encode($return_data));
        die();
    }


    /*
    * Prepare a view for logs
    */
    private function load_logs_view($data)
    {
        $return_data = '<div class="col-md-12 modal_log_table"><table >';
        if (!empty($data)) {
            foreach ($data as $k => $details) {
                $return_data .= '<tr>';
                /* $return_data .= '<td width="5%" class="center">'.$details->log_id.'</td>'; */
                $return_data .= '<td class="left">'.$details->log_note.'</td>';
                $return_data .= '<td width="15%" class="center">'.$details->date_created.'</td>';
                $return_data .= '<td width="20%" class="center">'.$details->created_by_full_name.'</td>';
                $return_data .= '</tr><tr><td colspan="4">&nbsp;</tr>';
            }
        } else {
            $return_data .= '<tr><td colspan="6"><br/>'.$this->config->item("no_records").'</td></tr>';
        }
        return $return_data .= '</table></div>';
    }


    public function add_event_tracker_log()
    {
        $event_id = (!empty($this->input->post('event_id'))) ? $this->input->post('event_id') : false ;

        if (!empty($event_id)) {
            $postdata 	  	= [
                'account_id'	=>	$this->user->account_id,
                'log_note'		=>	htmlentities($this->input->post('log_note')),
                'event_id'		=> ( int ) $event_id
            ];

            $url 			= 'fleet/add_event_tracking_log';
            $API_result		= $this->ssid_common->api_call($url, $postdata, $method = 'POST');
            $result		 	= (isset($API_result->new_log_note)) ? $API_result->new_log_note : null;
            $message	  	= (isset($API_result->message)) ? $API_result->message : 'Oops! There was an error processing your request.';


            if (!empty($result)) {
                $return_data['status']	= 1;
                $return_data['new_log_note'] = $result;
            }
            $return_data['status_msg'] = $message;

            print_r(json_encode($return_data));
            die();
        } else {
            return false;
        }
    }


    public function get_event_type_by_cat_id($event_category_id = false, $page = 'events')
    {
        $return_data = [
            'status' => 0
        ];
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
        $event_category_id = ($this->input->get('event_category_id')) ? $this->input->get('event_category_id') : $event_category_id ;

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postdata['account_id']			=	$this->user->account_id;
            $postdata['event_category_id']	=	$event_category_id;
            $url 			= 'fleet/event_types';
            $API_result		= $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $result		  = (isset($API_result->event_types)) ? $API_result->event_types : null;
            $message	  = (isset($API_result->message)) ? $API_result->message : 'Oops! There was an error processing your request.';

            if (!empty($result)) {
                $return_data['status']	= 1;
                $return_data['event_types'] = $result;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    /**
    * 	Update Vehicle Event status
    **/
    public function update_vehicle_event_status($event_id = false, $page = 'events')
    {
        $return_data['status'] = 0;

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        $section 	= ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $event_id 						= ($this->input->post('event_id')) ? $this->input->post('event_id') : $event_id ;
        $upddata['event_status_id'] 	= ($this->input->post('event_status_id')) ? $this->input->post('event_status_id') : false ;

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            if (!empty($event_id)) {
                $postdata['data']		= $upddata;
                $postdata['account_id'] = $this->user->account_id;
                $postdata['event_id'] 	= $event_id;

                $url 					= 'fleet/update_vehicle_event';
                $API_result				= $this->ssid_common->api_call($url, $postdata, $method = 'POST');
                $result		  			= (isset($API_result->updated_event)) ? $API_result->updated_event : null;
                $message	  			= (isset($API_result->message)) ? $API_result->message : 'Oops! There was an error processing your request.';

                if (!empty($result)) {
                    $return_data['status']	= 1;
                    $return_data['updated_event'] = $result	;
                }
                $return_data['status_msg'] = $message;
            } else {
                $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    /**
    *	Upload files. This is a Web-client only function
    **/
    public function upload_docs($vehicle_id = false, $page = 'documents')
    {
        $post_data = $vehicle_id = false;
        $post_data 		= $this->input->post();
        $vehicle_id 	= (!empty($post_data['vehicle_id'])) ? ( int )$post_data['vehicle_id'] : $vehicle_id ;

        if (!empty($vehicle_id)) {
            # Check module-item access
            $section 		= (!empty($page)) ? $page : $this->router->fetch_method();
            $item_access 	= $this->webapp_service->check_access($this->user, $this->module_id, $section);

            if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
                $this->_render_webpage('errors/access-denied', false);
            } else {
                $postdata 		= array_merge(['account_id'=>$this->user->account_id], $this->input->post());

                $document_group = 'fleet';
                $folder		 	= 'fleet';

                $doc_upload		= $this->document_service->upload_files($this->user->account_id, $postdata, $document_group = 'fleet', $folder = 'fleet');

                redirect('webapp/fleet/profile/'.$vehicle_id.'/documents');
            }
        } else {
            redirect('webapp/fleet', 'refresh');
        }
    }

    /** Add a new Cost Item **/
    public function add_cost_item()
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
            $cost_item = $this->webapp_service->api_dispatcher($this->api_end_point.'cost/create', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  	= (isset($cost_item->cost_item)) ? $cost_item->cost_item : null;
            $message	  	= (isset($cost_item->message)) ? $cost_item->message : 'Oops! There was an error processing your request.';

            if (!empty($result)) {
                $return_data['status'] 		= 1;
                $return_data['cost_item'] 	= $result;
                $text_color 				= 'green';
            }
            $return_data['status_msg'] = '<span class="text-'.$text_color.'">'.$message.'</span>';
        }

        print_r(json_encode($return_data));
        die();
    }
}
