<?php

namespace App\Controllers\Web;

use App\Extensions\MX\Controller as MX_Controller;
use Domain\AccountDisciplineService;
use Domain\DisciplineService;
use Domain\AccountService;

class AccountController extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('pagination');

        $this->module_id 	   		= $this->webapp_service->_get_module_id($this->router->fetch_class());
        $this->discipline_module_id = 23; //Check in DB;
    }

    public function index()
    {
        redirect('webapp/account/administration', 'refresh');
    }

    /** Signup Account **/
    public function signup()
    {
        $modules			= $this->webapp_service->api_dispatcher($this->api_end_point.'account/modules', ['account_id' => false , 'active_only'=> 0 ], ['auth_token'=>false], true);
        $data['modules'] 	= (!empty($modules->modules)) ? $modules->modules : null;
        $packages			= $this->webapp_service->api_dispatcher($this->api_end_point.'account/packages', ['account_id'=>false], ['auth_token'=>false], true);
        $data['packages'] 	= (!empty($packages->packages)) ? $packages->packages : null;
        $this->_render_webpage('account/signup', $data);
    }

    /** Do account creation **/
    public function create_account()
    {
        $return_data = [
            'status'=>0
        ];

        $new_account	  = $this->webapp_service->api_dispatcher($this->api_end_point.'account/create', $this->input->post(), ['auth_token'=>false]);
        $result		  = (isset($new_account->account)) ? $new_account->account : null;
        $message	  = (isset($new_account->message)) ? $new_account->message : 'Oops! There was an error processing your request.';
        if (!empty($result)) {
            $return_data['status']  = 1;
            $return_data['account'] = $new_account;
        }
        $return_data['status_msg']  = $message;

        print_r(json_encode($return_data));
        die();
    }

    public function activate($activate_str = false)
    {
        if (!empty($activate_str)) {
            $activate 	= $this->webapp_service->api_dispatcher($this->api_end_point.'account/activate_account', ['activation_code'=>$activate_str]);
            $data['activation_data']= (!empty($activate)) ? $activate : null;
            $this->_render_webpage('account/activate', $data);
        } else {
            redirect('webapp/user/login', 'refresh');
        }
    }

    /** Account Administration **/
    public function administration()
    {
        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        if (!$this->user->is_admin && (!in_array($this->user->id, $this->super_admin_list))) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $data['account_statuses'] = ['Active','Closed','Suspended','Trial'];
            $this->_render_webpage('account/administration/index', $data);
        }
    }

    /*
    * Account lookup / search
    */
    public function lookup()
    {
        $return_data = '';
        if (!$this->user->is_admin && (!in_array($this->user->id, $this->super_admin_list))) {
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

            $search_result	= $this->webapp_service->api_dispatcher($this->api_end_point.'account/lookup', $postdata, ['auth_token'=>$this->auth_token], true);

            $account			= (isset($search_result->accounts)) ? $search_result->accounts : null;
            if (!empty($account)) {
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

                $return_data = $this->load_accounts_view($account);
                if (!empty($pagination)) {
                    $return_data .= '<tr><td colspan="5" style="padding: 0;">';
                    $return_data .= $page_display.$pagination;
                    $return_data .= '</td></tr>';
                }
            } else {
                $return_data .= '<tr><td colspan="5" style="padding: 0;"><br/>';
                $return_data .= (isset($search_result->message)) ? $search_result->message : 'No records found';
                $return_data .= '</td></tr>';
            }
        }

        print_r($return_data);
        die();
    }

    /*
    * Prepare Account views
    */
    private function load_accounts_view($accounts_data)
    {
        $return_data = '';
        if (!empty($accounts_data)) {
            foreach ($accounts_data as $k => $account_details) {
                $return_data .= '<tr>';
                $return_data .= '<td><a href="'.base_url('/webapp/account/profile/'.$account_details->account_id).'" >'.$account_details->account_id.'</a></td>';
                $return_data .= '<td><a href="'.base_url('/webapp/account/profile/'.$account_details->account_id).'" >'.$account_details->account_name.'</a></td>';
                $return_data .= '<td>'.$account_details->account_first_name.' '.$account_details->account_last_name.'</td>';
                $return_data .= '<td>'.$account_details->account_membership_number.'</td>';
                $return_data .= '<td>'.$account_details->account_status.'</td>';
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

    //View Account profile
    public function profile($account_id = false, $page = 'details')
    {
        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        if (!$this->user->is_admin && (!in_array($this->user->id, $this->super_admin_list))) {
            $this->_render_webpage('errors/access-denied', false);
        } elseif ($account_id) {
            $account_details			= $this->webapp_service->api_dispatcher($this->api_end_point.'account/lookup', ['account_id'=>$this->user->account_id,'where'=>['account_id'=>$account_id]], ['auth_token'=>$this->auth_token], true);
            $data['account_details']	= (isset($account_details->accounts)) ? $account_details->accounts : null;

            $data['super_admin_list'] 	= $this->super_admin_list;

            $data['tab_base'] = base_url('webapp/account/profile/' . $account_id . '/');

            $data['account_admin_tabs'] = [
                [ "text" => "Details", "link_tab" =>  'details' ],
                [ "text" => "Account Modules", "link_tab" =>  'account_modules' ],
                [ "text" => "Disciplines", "link_tab" =>  'disciplines'],
                /* [ "text" => "Account Configs", "link_tab" =>  'config' ],
                [ "text" => "Addresses", "link_tab" =>  'addresses'],
                [ "text" => "Settings", "link_tab" =>   'settings' ],
                [ "text" => "System Modules", "link_tab" =>  'system_modules' ],*/
            ];

            $data['account_profile_id'] = $account_id;

            if (!empty($data['account_details'])) {
                $data['page'] = $page;
                switch(strtolower($page)) {
                    /* case 'account_modules':
                        $data['include_page'] 	= 'account_modules.php';
                        break; */
                    case 'addresses':
                        $data['include_page'] 	= 'account_addresses.php';
                        break;
                    case 'settings':

                        $postdata 				=  [ 'user_id' =>  $this->user->id, 'account_id' => $this->user->account_id, 'admin_account_id'=>$this->user->account_id ];
                        $system_modules 		= $this->webapp_service->api_dispatcher($this->api_end_point.'account/system_modules', $postdata, [ 'auth_token'=>$this->auth_token ], true);
                        $data['system_modules']	= (isset($system_modules->modules)) ? $system_modules->modules : null;

                        $postdata = [ 'user_id' =>  $this->user->id, 'account_id' => $this->user->account_id, 'admin_account_id'=>$this->user->account_id ];
                        $all_tables = $this->webapp_service->api_dispatcher($this->api_end_point.'account/all_tables', $postdata, [ 'auth_token'=>$this->auth_token ], true);
                        $data['available_tables']	= (!empty($all_tables->available_tables)) ? $all_tables->available_tables : null;

                        $configurable_tables  	= $this->webapp_service->api_dispatcher($this->api_end_point.'account/configurable_tables', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true);
                        $data['config_tables']	= (!empty($configurable_tables->configurable_tables)) ? $configurable_tables->configurable_tables : null;
                        $data['include_page'] 	= 'admin_settings.php';

                        break;
                    case 'config':

                        $postdata 	  		= array_merge(['account_id'=>$this->user->account_id, 'id'=>$this->user->id ], $this->input->post());
                        $profile_image	= $this->webapp_service->api_dispatcher($this->api_end_point.'user/users', $postdata, ['auth_token'=>$this->auth_token], true);
                        $profile_path = '_account_assets/accounts/' .  $this->user->account_id .  '/users/' .  $this->user->id . "/";

                        if (!empty($profile_image->user->profile_image)) {
                            $profile_image_status['status'] = true;
                            $profile_image_status['image_link'] = base_url($profile_path . $profile_image->user->profile_image);
                        }

                        $data['user'] 			= $this->user;
                        $data['profile_image'] 	= !empty($profile_image_status) ? $profile_image_status : false;

                        $data['include_page'] 	= 'admin_config.php';
                        break;
                    case 'account_modules':
                    case 'system_modules':

                        $postdata 				=  [ 'user_id' =>  $this->user->id, 'account_id' => $this->user->account_id, 'admin_account_id'=>$this->user->account_id ];
                        $all_modules_list 		= $this->webapp_service->api_dispatcher($this->api_end_point.'account/system_modules', $postdata, [ 'auth_token'=>$this->auth_token ], true);
                        $data['system_modules']	= (isset($all_modules_list->modules)) ? $all_modules_list->modules : null;

                        $data['include_page'] 	= 'system_modules.php';
                        break;
                    case 'disciplines':

                        $postdata 				=  [ 'user_id' =>  $this->user->id, 'account_id' => $this->user->account_id, 'admin_account_id'=>$this->user->account_id ];
                        $system_modules 		= $this->webapp_service->api_dispatcher($this->api_end_point.'account/system_modules', $postdata, [ 'auth_token'=>$this->auth_token ], true);
                        $data['system_modules']	= (isset($system_modules->modules)) ? $system_modules->modules : null;

                        $postdata 				=  [ 'account_id' => $account_id, 'admin_account_id' => $this->user->account_id ];
                        $disciplines 			= $this->webapp_service->api_dispatcher($this->api_end_point.'discipline/disciplines', $postdata, [ 'auth_token'=>$this->auth_token ], true);
                        $data['disciplines']	= (isset($disciplines->disciplines)) ? $disciplines->disciplines : null;

                        $account_disciplines 			= $this->webapp_service->api_dispatcher($this->api_end_point.'discipline/account_disciplines', $postdata, [ 'auth_token'=>$this->auth_token ], true);
                        $data['account_disciplines']	= (isset($account_disciplines->account_disciplines)) ? $account_disciplines->account_disciplines : null;

                        $data['account_disciplines_people']    = [];

                        $postdata['account_id']             = $this->user->account_id;
                        $API_call                            = $this->webapp_service->api_dispatcher($this->api_end_point.'people/people', $postdata, ['auth_token'=>$this->auth_token], true);
                        //						 debug( $API_call, "print", true );
                        $data['account_disciplines_people']    = (isset($API_call->people)) ? $API_call->people : null;

                        $data['active_acc_disciplines']	= [];
                        if (!empty($data['account_disciplines'])) {
                            foreach ($data['account_disciplines'] as $k => $discp) {
                                $data['active_acc_disciplines'][$discp->discipline_id] = $discp;
                            }
                        }

                        $data['include_page'] 	= 'account_disciplines.php';
                        break;

                    case 'details':
                    default:
                        $data['account_types'] 	= (isset($account_types->account_types)) ? $account_types->account_types : null;
                        $account_statuses		= $this->webapp_service->api_dispatcher($this->api_end_point.'account/account_statuses', ['account_id'=>false], ['auth_token'=>$this->auth_token], true);
                        $data['include_page'] 	= 'account_details.php';
                        break;
                }
            }
            $this->_render_webpage('account/administration/profile', $data, '');
        } else {
            redirect('webapp/home', 'refresh');
        }
    }


    /** Update account details **/
    public function update_account($account_id = false)
    {
        $return_data = [
            'status'=>0
        ];
        $user_id = ($this->input->post('id')) ? $this->input->post('id') : (!empty($id) ? $id : null);
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : 'details');

        if (!$this->user->is_admin && (!in_array($this->user->id, $this->super_admin_list))) {
            $return_data['message'] = 'Access denied! Please login';
        }

        if (!$this->user->is_admin && (!in_array($this->user->id, $this->super_admin_list))) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            ;
        } else {
            $postdata 	  	= array_merge(['admin_account_id'=>$this->user->account_id, 'admin_user_id'=>$this->user->id], $this->input->post());
            $update_account = $this->webapp_service->api_dispatcher($this->api_end_point.'account/update', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  	= (isset($update_account->account)) ? $update_account->account : null;
            $message	  	= (isset($update_account->message)) ? $update_account->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] = 1;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }

    public function get_system_module($module_id = false)
    {
        $module_id = ($this->input->post('module_id')) ? $this->input->post('module_id') : (!empty($module_id) ? $module_id : null);

        $return_data = [
            'status'=>0,
        ];

        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        if (!$this->user->is_admin && (!in_array($this->user->id, $this->super_admin_list))) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $postdata = [ 'module_id' => $module_id, 'user_id' =>  $this->user->id, 'account_id' => $this->user->account_id, 'admin_account_id'=>$this->user->account_id ];
            $all_modules_list = $this->webapp_service->api_dispatcher($this->api_end_point.'account/system_modules', $postdata, [ 'auth_token'=>$this->auth_token ], true);

            $result		  	= (isset($all_modules_list->modules)) ? $all_modules_list->modules : null;
            $message	  	= (isset($all_modules_list->message)) ? $all_modules_list->message : 'Oops! There was an error processing your request.';

            if (!empty($result)) {
                $return_data['status'] = 1;
                $return_data['module_data'] =  $result;
                $return_data['message'] = $message;
            }
        }

        print_r(json_encode($return_data));
        die();
    }

    public function update_system_module()
    {
        $return_data = [
            'status'=>0
        ];

        if (!$this->user->is_admin && (!in_array($this->user->id, $this->super_admin_list))) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postdata 	  	= array_merge([ 'user_id' => $this->user->id, 'account_id'=>$this->user->account_id, 'admin_account_id'=>$this->user->id ], $this->input->post());
            $update_base_module = $this->webapp_service->api_dispatcher($this->api_end_point.'account/update_base_module', $postdata, [ 'auth_token'=>$this->auth_token ]);

            $result		  	= (isset($update_base_module->updated_module)) ? $update_base_module->updated_module : null;
            $message	  	= (isset($update_base_module->message)) ? $update_base_module->message : 'Oops! There was an error processing your request.';

            if (!empty($result)) {
                $return_data[ 'status' ] = 1;
                $return_data[ 'updated_module' ] = $result;
            }

            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }

    public function get_all_tables()
    {
        $return_data = [
            'status'=>0,
        ];

        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        if (!$this->user->is_admin && (!in_array($this->user->id, $this->super_admin_list))) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $postdata = [ 'user_id' =>  $this->user->id, 'account_id' => $this->user->account_id, 'admin_account_id'=>$this->user->account_id ];
            $all_modules_list = $this->webapp_service->api_dispatcher($this->api_end_point.'account/all_tables', $postdata, [ 'auth_token'=>$this->auth_token ], true);

            $result		  	= (isset($all_modules_list->available_tables)) ? $all_modules_list->available_tables : null;
            $message	  	= (isset($all_modules_list->message)) ? $all_modules_list->message : 'Oops! There was an error processing your request.';

            if (!empty($result)) {
                $return_data['status'] = 1;
                $return_data['tables'] =  $result;
                $return_data['message'] = $message;
            }
        }

        print_r(json_encode($return_data));
        die();
    }

    public function get_table_columns()
    {
        $return_data = [
            'status'=>0
        ];

        if (!$this->user->is_admin && (!in_array($this->user->id, $this->super_admin_list))) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $table_name = $this->input->get('table_name');

            if (!empty($table_name)) {
                $postdata 	  	= array_merge([ 'table_name'=> $table_name, 'user_id' => $this->user->id, 'account_id'=>$this->user->account_id, 'admin_account_id'=>$this->user->id ]);
                $table_columns = $this->webapp_service->api_dispatcher($this->api_end_point.'account/table_columns', $postdata, [ 'auth_token'=>$this->auth_token ], true);

                $result		  	= (isset($table_columns->available_columns)) ? $table_columns->available_columns : null;
                $message	  	= (isset($table_columns->message)) ? $table_columns->message : 'Oops! There was an error processing your request.';

                if (!empty($result)) {
                    $return_data[ 'status' ] = 1;
                    $return_data[ 'status_msg' ] = $message;
                    $return_data[ 'table_columns' ] = $result;
                }
            } else {
                $return_data['status_msg'] = 'You did not enter a table name!';
            }
        }

        print_r(json_encode($return_data));
        die();
    }

    public function update_config_table()
    {
        $return_data = [
            'status'=>0
        ];

        if (!$this->user->is_admin && (!in_array($this->user->id, $this->super_admin_list))) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $config_data  	   = ($this->input->post('config_data')) ? $this->input->post('config_data') : false;

            $postdata = [
                'account_id'		=>	$this->user->account_id,
                'account_id'		=>	$this->user->account_id,
                'admin_account_id'	=>	$this->user->id,
                'config_data' => $config_data
            ];

            $update_config_table = $this->webapp_service->api_dispatcher($this->api_end_point.'account/update_config_table', $postdata, [ 'auth_token'=>$this->auth_token ]);

            $return_data = $update_config_table;
        }

        print_r(json_encode($return_data));
        die();
    }

    public function delete_config_entry()
    {
        $return_data = [
            'status'=>0
        ];

        if (!$this->user->is_admin && (!in_array($this->user->id, $this->super_admin_list))) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $config_entry_id  	   = ($this->input->post('settings_id')) ? $this->input->post('settings_id') : false;

            if ($config_entry_id) {
                $postdata = [ 'config_entry_id' => $config_entry_id, 'user_id' => $this->user->id, 'account_id'=>$this->user->account_id, 'admin_account_id'=>$this->user->id ];
                $update_config_table = $this->webapp_service->api_dispatcher($this->api_end_point.'account/delete_config_entry', $postdata, [ 'auth_token'=>$this->auth_token ]);

                $return_data['status'] = ($update_config_table->status) ? $update_config_table->status : false;
                $return_data['status_msg'] = ($update_config_table->message) ? $update_config_table->message : null;
            } else {
                $return_data['status_msg'] = 'You did not provide a config entry id!';
            }
        }

        print_r(json_encode($return_data));
        die();
    }

    public function get_related_config_table()
    {
        $return_data = [
            'status'=>0
        ];

        if (!$this->user->is_admin && (!in_array($this->user->id, $this->super_admin_list))) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $table_name = $this->input->post('table_name');

            $postdata = [ 'user_id' => $this->user->id, 'account_id'=>$this->user->account_id, 'admin_account_id'=>$this->user->id, 'table_name' => $table_name ];
            $table_entries = $this->webapp_service->api_dispatcher($this->api_end_point.'account/config_table_data', $postdata, [ 'auth_token'=>$this->auth_token ], true);
            $return_data['table_data'] = (!empty($table_entries->table_data)) ? $table_entries->table_data : false;

            $postdata 	  	= array_merge([ 'table_name'=> $table_name, 'user_id' => $this->user->id, 'account_id'=>$this->user->account_id, 'admin_account_id'=>$this->user->id ]);
            $table_columns = $this->webapp_service->api_dispatcher($this->api_end_point.'account/table_columns', $postdata, [ 'auth_token'=>$this->auth_token ], true);
            $return_data['table_columns'] = (!empty($table_columns->available_columns)) ? $table_columns->available_columns : false;

            if (!empty($table_entries) && !empty($table_columns)) {
                $return_data['status'] = 1;
                $return_data['status_msg'] = 'Found table data!';
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    //View Discipline profile
    public function discipline_profile($discipline_id = false, $page = 'details')
    {
        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module-discipline access
        $discipline_access = $this->webapp_service->check_access($this->user, $this->discipline_module_id, $section);

        if (!$this->user->is_admin && (!in_array($this->user->id, $this->super_admin_list))) {
            $this->_render_webpage('errors/access-denied', false);
        } elseif ($discipline_id) {
            $discipline_details = $this->webapp_service->api_dispatcher($this->api_end_point.'discipline/disciplines', [ 'account_id'=>$this->user->account_id, 'where'=>[ 'discipline_id'=>$discipline_id ] ], [ 'auth_token'=>$this->auth_token ], true);
            $data['discipline_details'] = (isset($discipline_details->disciplines)) ? $discipline_details->disciplines : null;
            if (!empty($data['discipline_details'])) {
                $run_admin_check 	= false;
                #Get allowed access for the logged in user
                $data['permissions']= $discipline_access;
                $tab_permissions	= !empty($discipline_access->tab_permissions) ? $discipline_access->tab_permissions : false;
                $data['active_tab']	= $page;

                $module_items 		= $this->webapp_service->api_dispatcher($this->api_end_point.'access/account_modules_items', ['account_id'=>$this->user->account_id, 'module_id'=>$this->discipline_module_id ], ['auth_token'=>$this->auth_token], true);
                $data['module_tabs']= (isset($module_items->module_items)) ? $module_items->module_items : null;
                $data['more_list_active']= (!empty($reordered_tabs['more_list']) && in_array($page, $reordered_tabs['more_list'])) ? true : false;

                $data['tab_permissions'] = !empty($tab_permissions->{$page}) ? $tab_permissions->{$page} : false;
                $data['profile_link']    = __FUNCTION__;

                switch($page) {
                    case 'details':
                    default:
                        $data['include_page'] = 'discipline_details.php';
                        break;
                }
            }

            //Run the admin check if tab needs only admin
            if (!empty($run_admin_check)) {
                if ((!admin_check($this->user->is_admin, false, (!empty($data['permissions']) ? $data['permissions']->is_admin : false)))) {
                    $data['admin_no_access'] = true;
                }
            }
            $this->_render_webpage('account/disciplines/profile', $data);
        } else {
            redirect('webapp/account/disciplines', 'refresh');
        }
    }

    //Manage Disciplines - Overview page
    public function disciplines($discipline_id = false, $page = 'details')
    {
        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        $toggled		= (!empty($this->input->get('toggled')) ? $this->input->get('toggled') : false);
        $section 		= (!empty($page)) ? $page : (!empty($this->input->get('page')) ? $this->input->get('page') : 'details');
        $discipline_id  = (!empty($discipline_id)) ? $discipline_id : (!empty($this->input->get('discipline_id')) ? $this->input->get('discipline_id') : ((!empty($this->input->get('discipline_id')) ? $this->input->get('discipline_id') : null)));

        if (!$this->user->is_admin && (!in_array($this->user->id, $this->super_admin_list))) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            if (!empty($discipline_id)) {
                $default_params = $params =[ 'account_id'=>$this->user->account_id, 'where'=>[ 'discipline_id'=>$discipline_id ] ];
                $discipline_details = $this->webapp_service->api_dispatcher($this->api_end_point.'discipline/disciplines', $params, [ 'auth_token'=>$this->auth_token ], true);

                if (!empty($discipline_details->disciplines)) {
                    $data['discipline_details']  	= $discipline_details->disciplines;
                    $associated_job_types  		 	= $this->webapp_service->api_dispatcher($this->api_end_point.'discipline/associated_job_types', ['account_id'=>$this->user->account_id, 'discipline_id'=>$discipline_id ], ['auth_token'=>$this->auth_token], true);
                    $data['associated_job_types']	= (isset($associated_job_types->associated_job_types)) ? $associated_job_types->associated_job_types : null;
                    $this->_render_webpage('account/disciplines/discipline_details', $data);
                } else {
                    redirect('webapp/account/disciplines', 'refresh');
                }
            } else {
                $this->_render_webpage('account/disciplines/discipline_overview', false, false, true);
            }
        }
    }


    /**
    *	Create Discipline
    **/
    public function new_discipline($page = 'details')
    {
        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        $section 	= (!empty($page)) ? $page : (!empty($this->input->get('page')) ? $this->input->get('page') : 'details');
        if (!$this->user->is_admin && (!in_array($this->user->id, $this->super_admin_list))) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $this->_render_webpage('account/disciplines/create_discipline', $data = false);
        }
    }


    /**
    *	Create Discipline
    **/
    public function create_discipline($page = 'details')
    {
        $return_data = [
            'status'=>0
        ];

        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        if (!$this->user->is_admin && (!in_array($this->user->id, $this->super_admin_list))) {
            $return_data['status_msg'] = $this->config->discipline('ajax_access_denied');
        } else {
            $postdata 	= array_merge(['account_id'=>$this->user->account_id], $this->input->post());

            $discipline	= $this->webapp_service->api_dispatcher($this->api_end_point.'discipline/create_discipline', $postdata, ['auth_token'=>$this->auth_token]);
            $result		= (isset($discipline->discipline)) ? $discipline->discipline : null;
            $message	= (isset($discipline->message)) ? $discipline->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] 		= 1;
                $return_data['discipline'] 	= $result;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }



    /*
    * Disciplines List / Search
    */
    public function disciplines_list($page = 'details')
    {
        $return_data = '';

        $section 	 = (!empty($page)) ? $page : $this->router->fetch_method();

        if (!$this->user->is_admin && (!in_array($this->user->id, $this->super_admin_list))) {
            $return_data .= $this->config->discipline('ajax_access_denied');
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

            //	$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'discipline/disciplines', $postdata, [ 'auth_token'=>$this->auth_token ], true );


            $d = (new Discipline_model())->get_disciplines(
                $postdata['account_id'],
                $postdata['search_term'],
                $postdata['where'],
                $postdata['order_by'],
                $postdata['offset']
            );

            $disciplines	= (isset($search_result->disciplines)) ? $search_result->disciplines : null;

            if (!empty($disciplines)) {
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

                $return_data = $this->load_disciplines_view($disciplines);
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
    * Disciplines bank views
    */
    private function load_disciplines_view($disciplines_data)
    {
        $return_data = '';
        if (!empty($disciplines_data)) {
            foreach ($disciplines_data as $k => $discipline) {
                $return_data .= '<tr>';
                $return_data .= '<td><a href="'.base_url('/webapp/account/discipline_profile/'.$discipline->discipline_id).'" >'.$discipline->discipline_name.'</a></td>';
                $return_data .= '<td>'.$discipline->discipline_desc.'</td>';
                $return_data .= '<td>'.$discipline->discipline_ref.'</td>';
                $return_data .= '<td>'.$discipline->discipline_icon.'</td>';
                $return_data .= '<td><span class="pull-right" >'.(!empty($discipline->is_active) ? 'Active' : 'Disabled').'</span></td>';
                $return_data .= '</tr>';
            }

            if (!empty($pagination)) {
                $return_data .= '<tr><td colspan="5" style="padding: 0;">';
                $return_data .= $page_display.$pagination;
                $return_data .= '</td></tr>';
            }
        } else {
            $return_data .= '<tr><td colspan="5"><br/>'.$this->config->discipline("no_records").'</td></tr>';
        }
        return $return_data;
    }

    /** Update Discipline Profile Details **/
    public function update_discipline($discipline_id = false, $page = 'details')
    {
        $return_data = [
            'status'=>0
        ];

        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $discipline_id = ($this->input->post('discipline_id')) ? $this->input->post('discipline_id') : (!empty($discipline_id) ? $discipline_id : null);

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        if (!$this->user->is_admin && (!in_array($this->user->id, $this->super_admin_list))) {
            $return_data['status_msg'] = $this->config->discipline('ajax_access_denied');
        } else {
            $postdata 	  = array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $update_discipline= $this->webapp_service->api_dispatcher($this->api_end_point.'discipline/update_discipline', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  = (isset($update_discipline->discipline)) ? $update_discipline->discipline : null;
            $message	  = (isset($update_discipline->message)) ? $update_discipline->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] 		= 1;
                $return_data['discipline'] 	= $result;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    /**
    * Delete Discipline
    **/
    public function delete_discipline($discipline_id = false)
    {
        $return_data = [
            'status'=>0
        ];

        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $discipline_id = ($this->input->post('discipline_id')) ? $this->input->post('discipline_id') : (!empty($discipline_id) ? $discipline_id : null);

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        if (!$this->user->is_admin && (!in_array($this->user->id, $this->super_admin_list))) {
            $return_data['status_msg'] = $this->config->discipline('ajax_access_denied');
        } else {
            $postdata 	  = array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $delete_discipline = $this->webapp_service->api_dispatcher($this->api_end_point.'discipline/delete_discipline', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  = (isset($delete_discipline->status)) ? $delete_discipline->status : null;
            $message	  = (isset($delete_discipline->message)) ? $delete_discipline->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] = 1;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    /**
     * Get Account Discipline
     */
    public function get_account_discipline($discipline_id = false)
    {
        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
            exit();
        }

        if (!$this->user->is_admin && (!in_array($this->user->id, $this->super_admin_list))) {
            $this->_render_webpage('errors/access-denied', false);
        }

        // @todo: we can use this later and pass in to the service for validation and usage
        $postdata = array_merge(
            ['admin_account_id' => $this->user->account_id],
            $this->input->post()
        );

        $account_discipline = new AccountService($this->user->account_id);

        $data = $account_discipline->getAccountDisciplineData();

        print_r(json_encode($data));
        die();
    }


    /**
     * Update account discipline
     */
    public function update_account_discipline()
    {
        $return_data = [
            'status' => 0
        ];

        if (!$this->user->is_admin && (!in_array($this->user->id, $this->super_admin_list))) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        }

        $raw_post = $this->input->post();

        $discipline_data = $raw_post['discipline_data'];

        unset($raw_post['discipline_data']);

        $postdata = array_merge(
            ['admin_account_id' => $this->user->account_id],
            $raw_post,
            $discipline_data
        );

        $uri = 'discipline/update_account_discipline';

        $api_endpoint = sprintf("%s%s", $this->api_end_point, $uri);

        $update_base_discipline = $this->webapp_service->api_dispatcher(
            $api_endpoint,
            $postdata,
            ['auth_token' => $this->auth_token]
        );

        $account = new AccountService($this->user->account_id);

        $account->updateAccountDisplineContacts($postdata);

        $result = (isset($update_base_discipline->account_discipline))
            ? $update_base_discipline->account_discipline
            : null;

        $message = (isset($update_base_discipline->message))
            ? $update_base_discipline->message
            : 'Oops! There was an error processing your request.';

        if (!empty($result)) {
            $return_data['status'] = 1;
            $return_data['account_discipline'] = $result;
        }

        $return_data['status_msg'] = $message;

        print_r(json_encode($return_data));
        die();
    }


    public function activate_account_disciplines()
    {
        $return_data = [
            'status'=>0
        ];

        if (!$this->user->is_admin && (!in_array($this->user->id, $this->super_admin_list))) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postdata 	  			= array_merge([ 'account_id'=>$this->user->account_id ], $this->input->post());
            $activate_discipline 	= $this->webapp_service->api_dispatcher($this->api_end_point.'discipline/activate_account_disciplines', $postdata, [ 'auth_token'=>$this->auth_token ]);

            $result		  	= (isset($activate_discipline->account_disciplines[0])) ? $activate_discipline->account_disciplines[0] : null;
            $message	  	= (isset($activate_discipline->message)) ? $activate_discipline->message : 'Oops! There was an error processing your request.';

            if (!empty($result)) {
                $return_data[ 'status' ] 			 = 1;
                $return_data[ 'account_discipline' ] = $result;
            }

            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    public function deactivate_account_disciplines()
    {
        $return_data = [
            'status'=>0
        ];

        if (!$this->user->is_admin && (!in_array($this->user->id, $this->super_admin_list))) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postdata 	  			= array_merge([ 'account_id'=>$this->user->account_id ], $this->input->post());
            ;
            $deactivate_discipline = $this->webapp_service->api_dispatcher($this->api_end_point.'discipline/deactivate_account_disciplines', $postdata, [ 'auth_token'=>$this->auth_token ]);

            $result		  	= (isset($deactivate_discipline->account_disciplines)) ? $deactivate_discipline->account_disciplines : null;
            $message	  	= (isset($deactivate_discipline->message)) ? $deactivate_discipline->message : 'Oops! There was an error processing your request.';

            if (!empty($result)) {
                $return_data[ 'status' ] 			 = 1;
                $return_data[ 'account_discipline' ] = $result;
            }

            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    ############ EVIDOC TEMPLATES MANAGER #############

    //Manage Evidocs types
    public function evidoc_templates($audit_type_id = false, $src_account_id = false, $page = 'details')
    {
        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        $toggled		= (!empty($this->input->get('toggled')) ? $this->input->get('toggled') : false);
        $section 		= (!empty($page)) ? $page : (!empty($this->input->get('page')) ? $this->input->get('page') : 'details');
        $audit_type_id  = (!empty($audit_type_id)) ? $audit_type_id : (!empty($this->input->get('audit_type_id')) ? $this->input->get('audit_type_id') : ((!empty($this->input->get('id')) ? $this->input->get('id') : null)));

        if (!empty($audit_type_id)) {
            $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
            if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
                $this->_render_webpage('errors/access-denied', false);
            } else {
                #$default_params = $params =[ 'account_id'=>$this->user->account_id, 'where'=>[ 'audit_type_id'=>$audit_type_id ] ];
                $default_params = $params =[ 'account_id'=> $src_account_id, 'where'=>[ 'audit_type_id'=>$audit_type_id ] ];

                $params['where']['apply_limit']  = 1;
                $params['where']['all_accounts'] = 1;
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

                    $audit_questions	 		= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/audit_questions', [ 'account_id'=> $src_account_id, 'audit_type_id'=>$audit_type_id, 'sectioned'=>1 ], ['auth_token'=>$this->auth_token], true);
                    $data['evidoc_questions']	= (isset($audit_questions->audit_questions)) ? $audit_questions->audit_questions : null;

                    $audit_categories	 		= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/audit_categories', [ 'account_id'=> $src_account_id ], ['auth_token'=>$this->auth_token], true);
                    $data['evidoc_categories']	= (isset($audit_categories->audit_categories)) ? $audit_categories->audit_categories : null;

                    $asset_types	 			= $this->webapp_service->api_dispatcher($this->api_end_point.'asset/asset_types', [ 'account_id'=> $src_account_id, 'where'=>['grouped'=>1] ], ['auth_token'=>$this->auth_token], true);
                    $data['asset_types']		= (isset($asset_types->asset_types)) ? $asset_types->asset_types : null;

                    $data['general_file_types'] = generic_file_types();
                    $data['toggled_section']	= $toggled;

                    $evidoc_frequencies	 		= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/evidoc_frequencies', ['account_id'=> $src_account_id ], ['auth_token'=>$this->auth_token], true);
                    $data['evidoc_frequencies'] = (isset($evidoc_frequencies->evidoc_frequencies)) ? $evidoc_frequencies->evidoc_frequencies : null;

                    $schedule_frequencies 			= $this->webapp_service->api_dispatcher($this->api_end_point.'job/schedule_frequencies', [ 'account_id'=> $src_account_id ], [ 'auth_token'=>$this->auth_token ], true);
                    $data['schedule_frequencies'] 	= isset($schedule_frequencies->schedule_frequencies) ? $schedule_frequencies->schedule_frequencies : false;

                    $asset_group 				= $data['evidoc_type_details']->asset_group;
                    $data['asset_sub_categories']= asset_sub_categories();
                    $data['asset_sub_category']	= (!empty($asset_group) && !empty($data['asset_sub_categories'][$asset_group])) ? $data['asset_sub_categories'][$asset_group] : false;

                    $job_types					= $this->webapp_service->api_dispatcher($this->api_end_point.'job/job_types', ['account_id'=> $src_account_id ], ['auth_token'=>$this->auth_token], true);
                    $data['job_types']			= (isset($job_types->job_types)) ? $job_types->job_types : null;

                    $available_contracts	  	= $this->webapp_service->api_dispatcher($this->api_end_point.'contract/contracts', ['account_id'=> $src_account_id, 'limit'=>-1 ], ['auth_token'=>$this->auth_token], true);
                    $data['available_contracts']= (isset($available_contracts->contract)) ? $available_contracts->contract : null;

                    $disciplines	 			= $this->webapp_service->api_dispatcher($this->api_end_point.'discipline/account_disciplines', [ 'account_id'=> $src_account_id ], ['auth_token'=>$this->auth_token], true);
                    $data['disciplines']		= (isset($disciplines->account_disciplines)) ? $disciplines->account_disciplines : null;

                    $this->_render_webpage('account/templates/evidocs/evidoc_type_template_profile', $data);
                } else {
                    redirect('webapp/account/evidoc_names', 'refresh');
                }
            }
        } else {
            $evidoc_frequencies	 		= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/evidoc_frequencies', ['account_id'=>$src_account_id ], ['auth_token'=>$this->auth_token], true);
            $data['evidoc_frequencies'] = (isset($evidoc_frequencies->evidoc_frequencies)) ? $evidoc_frequencies->evidoc_frequencies : null;
            $active_accounts			= $this->webapp_service->api_dispatcher($this->api_end_point.'account/lookup', [ 'account_id'=> $this->user->account_id, 'where' => [ 'active_only' => 1 ], 'limit'=> -1 ], ['auth_token'=>$this->auth_token], true);
            $data['active_accounts'] 	= !empty($active_accounts->accounts) ? $active_accounts->accounts : null;

            $this->_render_webpage('account/templates/evidocs/evidoc_types', $data);
        }
    }

    /*
    * Create New Evidocs Type
    */
    public function new_evidoc_template($page = 'details')
    {
        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $evidoc_groups	 			= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/evidoc_groups', ['account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true);
            $data['evidoc_groups']		= (isset($evidoc_groups->evidoc_groups)) ? $evidoc_groups->evidoc_groups : null;

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

            $this->_render_webpage('account/templates/evidocs/evidoc_type_template_new', $data);
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
            $where['apply_limit']  = 1;
            $where['all_accounts'] = 1;
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
                $asset_long_desc	= (!empty($asset_group) && !empty($asset_sub_categories[$asset_group])) ? $asset_sub_categories[$asset_group] : false;

                $return_data .= '<tr>';
                $return_data .= '<td>'.(!empty($evidoc_type_details->account_name) ? $evidoc_type_details->account_name : '').'</td>';
                $return_data .= '<td><a title="'.$evidoc_type_details->audit_type_desc.'" href="'.base_url('/webapp/account/evidoc_templates/'.$evidoc_type_details->audit_type_id.'/'.$evidoc_type_details->account_id).'">'. ucwords($evidoc_type_details->audit_type) .' - '.ucwords($evidoc_type_details->audit_frequency).'</a></td>';
                $return_data .= '<td>'.ucwords($evidoc_type_details->audit_group). (!empty($asset_sub_category) ? ' <small>('.(strlen($asset_sub_category) > 18 ? substr($asset_sub_category, 0, 18)."<span title=".$asset_long_desc." >...</span>" : $asset_sub_category).')</small>' : '') .'</td>';
                #$return_data .= '<td>'.$evidoc_type_details->audit_type_desc.'</td>';
                $return_data .= '<td>'.(!empty($evidoc_type_details->is_active) ? 'Active' : 'Disabled').'</td>';
                $return_data .= '<td>'.$evidoc_type_details->frequency_name.'</td>';
                $return_data .= '<td><span data-toggle="modal" data-target="#audit-type-clone-modal-md" class="clone-audit-type pointer pull-right" data-source_account_name="'.$evidoc_type_details->account_name.'" data-source_account_id="'.$evidoc_type_details->account_id.'" data-audit_type="'.$evidoc_type_details->audit_type.'"  data-frequency="'.$evidoc_type_details->audit_frequency.'" data-audit_type_id="'.$evidoc_type_details->audit_type_id.'" ><span class="clone-evidoc-type"><i class="far fa-copy"></i> &nbsp;&nbsp;</span><a href="'.base_url('/webapp/account/evidoc_templates/'.$evidoc_type_details->audit_type_id).'" ><i class="fas fa-eye"></i></a></span></td>';
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


    /**
    * Clone Evidoc Type Record
    **/
    public function clone_evidoc_type($evidoc_type_id = false, $page = 'details')
    {
        $section 	 = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $demo = $this->input->post();

            $postdata 	  	  = array_merge([ 'account_id'=>$this->user->account_id ], $this->input->post());
            $clone_evidoc_type= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/clone_evidoc_type', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  	  = (isset($clone_evidoc_type->evidoc_type)) ? $clone_evidoc_type->evidoc_type : null;
            $message	  	  = (isset($clone_evidoc_type->message)) ? $clone_evidoc_type->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] 	 	= 1;
                $return_data['evidoc_type'] = $result;
            }
            $return_data['status_msg'] = $message;
        }
        print_r(json_encode($return_data));
        die();
    }
}
