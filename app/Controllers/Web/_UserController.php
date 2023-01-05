<?php

namespace App\Controllers\Web;

use App\Extensions\MX\Controller as MX_Controller;
use App\Models\Service\ModulesModel;

class _UserController extends MX_Controller
{
	private $module_service;

    public function __construct()
    {
        parent::__construct();
        $this->module_id 	   = $this->_get_module_id();
        $this->load->library('pagination');
		$this->module_service = new ModulesModel();
    }

    //redirect if needed, otherwise display the user list
    public function index()
    {
        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access->is_module_admin) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            #$this->users();
            redirect('webapp/user/users', 'refresh');
        }
    }

    //log the user in
    public function login()
    {
        if ($this->input->post()) {
            $remember = (bool) $this->input->post('remember');
            $url	  = $this->api_end_point.'user/login';
            $postdata = $this->ssid_common->_prepare_curl_post_data($this->input->post());
            $response = $this->ssid_common->doCurl($url, $postdata);
            if (isset($response->auth_token) && !empty($response->auth_token)) {
                //Everything is good, please proceed to home
                $this->session->set_userdata('auth_data', $response);

                if (!empty($response->user->change_password) && ($response->user->change_password == 1)) {
                    //Password change is required
                    $this->session->set_flashdata('username', $this->input->post('username'));
                    $this->session->set_flashdata('message', $response->message);
                    redirect('webapp/user/change_password', 'refresh');
                } else {
                    //Prevent Web-access from restricted user-types
                    if (in_array($response->user->user_type_id, $this->ion_auth_model->no_web_access_allowed)) {
                        $message = 'Access denied! You do not have access to the Web portal';
                        $this->session->set_flashdata('message', $message);
                        unset($response->user->auth_token);
                        redirect('webapp/user/login', 'refresh');
                    } else {
                        $previous_url = !empty($this->input->post('previous_url')) ? $this->input->post('previous_url') : false;

                        if ($previous_url && (preg_match('(login|logout|change_password|forgot_password)', $previous_url) != 1)) {
                            redirect($previous_url, 'refresh');
                        } else {
                            if (in_array($response->user->user_type_id, EXTERNAL_USER_TYPES)) {
                                redirect('webapp/job/jobs', 'refresh');
                            } else {
                                redirect('/webapp/home', 'refresh');
                            }
                        }
                    }
                }
            } elseif (isset($response->message) && !empty($response->message)) {
                // $this->session->set_flashdata('message', $this->ion_auth->errors());
                $this->session->set_flashdata('username', $this->input->post('username'));
                $this->session->set_flashdata('message', $response->message);
                redirect('webapp/user/login', 'refresh');
            } else {
                $this->session->set_flashdata('username', $this->input->post('username'));
                redirect('webapp/user/login', 'refresh');
            }
        } else {
            $data['message']  		= $this->session->flashdata('message');
            $data['username'] 		= ($this->session->flashdata('username')) ? $this->session->flashdata('username') : null;
            $data['previous_url'] 	= ($this->session->userdata('referrer_uri')) ? $this->session->userdata('referrer_uri') : null;

            ## Log out current session
            $this->session->sess_destroy();

            $this->_render_webpage('user/login', $data);
        }
    }

    /** Get list of users **/
    public function users($user_id = false)
    {
        if ($user_id) {
            redirect('webapp/user/profile/'.$user_id, 'refresh');
        }

        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        #Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access->is_module_admin) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $user_types 			= $this->webapp_service->api_dispatcher($this->api_end_point.'user/user_types', ['account_id'=>$this->user->account_id], false, true);
            $data['user_types']		= (isset($user_types->user_types)) ? $user_types->user_types : null;
            $data['current_user']	= $this->user;

            $user_statuses	 		= $this->webapp_service->api_dispatcher($this->api_end_point.'user/statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true);
            $data['user_statuses'] 	= (isset($user_statuses->user_statuses)) ? $user_statuses->user_statuses : null;

            $this->_render_webpage('user/index', $data);
        }
    }

    //View user profile
    public function profile($user_id = false, $page = 'details')
    {
        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } elseif ($user_id) {
            $account_modules	 	= $this->webapp_service->api_dispatcher($this->api_end_point.'user/account_modules', ['account_id'=>$this->user->account_id,'inc_mod_item'=>0,'categorized'=>1], ['auth_token'=>$this->auth_token], true);
            $user_details		 	= $this->webapp_service->api_dispatcher($this->api_end_point.'user/users', ['account_id'=>$this->user->account_id,'id'=>$user_id], ['auth_token'=>$this->auth_token], true);
            $data['user_details']	= (isset($user_details->user)) ? $user_details->user : null;
            if (!empty($data['user_details'])) {
                $run_admin_check 	= false;
                #Get allowed access for the logged in user
                $data['permissions']= $item_access;
                $data['active_tab']	= $page;

                $module_items 		= $this->webapp_service->api_dispatcher($this->api_end_point.'access/account_modules_items', ['account_id'=>$this->user->account_id, 'module_id'=>$this->module_id ], ['auth_token'=>$this->auth_token], true);
                $data['module_tabs']= (isset($module_items->module_items)) ? $module_items->module_items : null;
                $reordered_tabs 		 = reorder_tabs($data['module_tabs']);
                $data['module_tabs'] 	 = (!empty($reordered_tabs['module_tabs'])) ? $reordered_tabs['module_tabs'] : $data['module_tabs'];
                $data['more_list_active']= (!empty($reordered_tabs['more_list']) && in_array($page, $reordered_tabs['more_list'])) ? true : false;
            }

            switch($page) {
                case 'permissions':
                    $run_admin_check 			= true;
                    $data['account_modules'] = (isset($account_modules->account_modules)) ? $account_modules->account_modules : null;
                    $data['include_page'] 	 = 'user_permissions.php';
                    break;
                case 'addresses':
                    $run_admin_check 	  = true;
                    $data['include_page'] = 'user_addresses.php';
                    break;
                case 'confidential':
                    $run_admin_check 	  = true;
                    $data['include_page'] = 'user_confidential.php';
                    break;
                case 'associated_users':
                    $run_admin_check 	  = true;

                    $associated_users				= $this->webapp_service->api_dispatcher($this->api_end_point.'user/associated_users', ['account_id'=>$this->user->account_id, 'where'=>['primary_user_id'=>$user_id] ], ['auth_token'=>$this->auth_token], true);
                    $data['associated_users'] 		= (isset($associated_users->associated_users)) ? $associated_users->associated_users : [];

                    $data['associated_users_ids'] 	= (!empty($data['associated_users'])) ? array_column($data['associated_users'], 'user_id') : [];

                    $available_users	 	  	= $this->webapp_service->api_dispatcher($this->api_end_point.'user/users', ['account_id'=>$this->user->account_id, 'limit'=>-1], ['auth_token'=>$this->auth_token], true);
                    $data['available_users'] 	= (isset($available_users->users)) ? $available_users->users : null;

                    $users		  	  			= $this->webapp_service->api_dispatcher($this->api_end_point.'user/users', ['account_id'=>$this->user->account_id, 'limit'=>-1], ['auth_token'=>$this->auth_token], true);
                    $data['users']   			= (isset($users->users)) ? $users->users : null;

                    $data['include_page'] = 'associated_users.php';
                    break;
                case 'buildings_access':
                case 'buildings_visibility':
                case 'associated_buildings':

                    $run_admin_check 	  				= true;

                    $associated_buildings				= $this->webapp_service->api_dispatcher($this->api_end_point.'site/associated_buildings', ['account_id'=>$this->user->account_id, 'where'=>['user_id'=>$user_id] ], ['auth_token'=>$this->auth_token], true);
                    $data['associated_buildings'] 		= (isset($associated_buildings->associated_buildings)) ? $associated_buildings->associated_buildings : [];

                    $data['associated_buildings_ids']	= (!empty($data['associated_buildings'])) ? array_column($data['associated_buildings'], 'site_id') : [];

                    $where = [
                        'exclude_sites' => $data['associated_buildings_ids']
                    ];

                    $available_buildings	 	  	= $this->webapp_service->api_dispatcher($this->api_end_point.'site/lookup', [ 'account_id'=>$this->user->account_id, 'where' => $where, 'limit'=> -1 ], ['auth_token'=>$this->auth_token], true);
                    $data['available_buildings'] 	= (isset($available_buildings->sites)) ? $available_buildings->sites : null;

                    $postcode_regions     		= $this->webapp_service->api_dispatcher($this->api_end_point.'diary/regions', ['account_id'=>$this->user->account_id, 'limit'=>-1], [ 'auth_token'=>$this->auth_token ], true);
                    $data['postcode_regions']   = (isset($postcode_regions->regions)) ? $postcode_regions->regions : null;

                    $data['include_page'] = 'associated_buildings.php';
                    break;

                case 'details':
                default:
                    $user_types	 				= $this->webapp_service->api_dispatcher($this->api_end_point.'user/user_types', ['account_id'=>$this->user->account_id], false, true);
                    $data['user_types']			= (isset($user_types->user_types)) ? $user_types->user_types : null;

                    $active_accounts			= $this->webapp_service->api_dispatcher($this->api_end_point.'account/lookup', [ 'account_id'=> $this->user->account_id, 'where' => [ 'active_only' => 1 ], 'limit'=> -1 ], ['auth_token'=>$this->auth_token], true);
                    $data['active_accounts'] 	= !empty($active_accounts->accounts) ? $active_accounts->accounts : null;

                    $data['include_page'] = 'user_details.php';
                    break;
            }

            //Run the admin check if tab needs only admin
            if (!empty($run_admin_check)) {
                if ((!admin_check($this->user->is_admin, false, (!empty($data['permissions']) ? $data['permissions']->is_admin : false)))) {
                    $data['admin_no_access'] = true;
                }
            }

            $this->_render_webpage('user/profile', $data);
        } else {
            redirect('webapp/user', 'refresh');
        }
    }

    /** Update user permissions **/
    public function update_user_permissions($id = false, $page = 'permissions')
    {
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $return_data = [
            'status'=>0
        ];
        $user_id = ($this->input->post('id')) ? $this->input->post('id') : (!empty($id) ? $id : null);

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
            $updates_perms= $this->webapp_service->api_dispatcher($this->api_end_point.'user/permissions', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  = (isset($updates_perms->permissions)) ? $updates_perms->permissions : null;
            $message	  = (isset($updates_perms->message)) ? $updates_perms->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] = 1;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }

    /** Update user details **/
    public function update_user($id = false)
    {
        $return_data = [
            'status'=>0
        ];
        $user_id = ($this->input->post('id')) ? $this->input->post('id') : (!empty($id) ? $id : null);
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : 'details');

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

            $updates_user = $this->webapp_service->api_dispatcher($this->api_end_point.'user/update', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  = (isset($updates_user->user)) ? $updates_user->user : null;
            $message	  = (isset($updates_user->message)) ? $updates_user->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] = 1;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    public function update_mypassword()
    {
        if ($this->identity()) {
            $postdata 	  		= array_merge(['account_id'=>$this->user->account_id, 'id'=>$this->user->id ], $this->input->post());
            $password_change	= $this->webapp_service->api_dispatcher($this->api_end_point.'user/change_password', $postdata, ['auth_token'=>$this->auth_token]);
            echo json_encode($password_change);
            die();
        }
    }

    public function my_details()
    {
        $profile_image_status = [
            'status'=>0
        ];

        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        $postdata 	  		= array_merge(['account_id'=>$this->user->account_id, 'id'=>$this->user->id ], $this->input->post());
        $profile_image	= $this->webapp_service->api_dispatcher($this->api_end_point.'user/users', $postdata, ['auth_token'=>$this->auth_token], true);
        $profile_path = '_account_assets/accounts/' .  $this->user->account_id .  '/users/' .  $this->user->id . "/";

        if (!empty($profile_image->user->profile_image)) {
            $profile_image_status['status'] = true;
            $profile_image_status['image_link'] = base_url($profile_path . $profile_image->user->profile_image);
        }

        $data['user'] = $this->user;
        $data['profile_image'] = $profile_image_status;
        $this->load->view('user/user_mydetails', $data);
    }

    public function reorder_taskbar()
    {
        $this->load->model('Modules_model', 'module_service');
        $data['max_quickbar_modules'] = 5;
        $data['permitted_modules'] = $this->module_service->get_allowed_modules($this->user->account_id, $this->user->id);
        $this->load->view('user/reorder_taskbar', $data);
    }


    public function save_taskbar()
    {
        $result = [
                    'status'=>0
        ];
        if (!$this->identity()) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $quickbar_modules = $this->input->post('user_quickbar_modules');
            if ($quickbar_modules) {
                $postdata 	  		= array_merge(['account_id'=>$this->user->account_id, 'id'=>$this->user->id ]);
                $quickbar_result = $this->webapp_service->save_quickbar_modules($this->user, $quickbar_modules);
                $message = $this->session->flashdata('message');
                if ($quickbar_result) {
                    $result['status'] = 1;
                    $result['status_msg'] = 'Saved account quickbar.';
                } else {
                    $result['status_msg'] = 'Failed to save account quickbar!';
                }
            } else {
                $result['status_msg'] = "No quickbar modules selected!";
            }
        }
        print_r(json_encode($result));
        die();
    }


    public function crop_profileimage()
    {
        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }
        if ($this->input->post("profile_image")) {
            $postdata['profile_image'] = $this->input->post('profile_image');
            $postdata['user'] = $this->user;
            $this->load->view('user/user_crop_profileimage', $postdata);
        }
    }

    public function get_quickbar_modules()
    {
        $return_data = [
            'status'=>0,
            'message'=>'An unknown error has occured!'
        ];

        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        $quickbar_modules = $this->webapp_service->get_quickbar_modules($this->user->account_id);

        if ($quickbar_modules) {
            $return_data['quickbar_modules'] = $quickbar_modules;
            $return_data['status'] = 1;
            $return_data['message'] = 'Quickbar modules have been found!';
        }

        print_r(json_encode($return_data));
        die();
    }

    public function delete_profile_picture()
    {
        $return_data = [
            'status'=>0,
            'message'=>'An unknown error has occured!'
        ];
        if (!$this->identity()) {
            $return_data['message'] = "User is not authenticated!";
        } else {
            $postdata 	  		= array_merge(['account_id'=>$this->user->account_id, 'id' => $this->user->id, 'profile_image' => ''], $this->input->post());
            $update_profile_status	= $this->webapp_service->api_dispatcher($this->api_end_point . 'user/update', $postdata, $this->options);
            if ($update_profile_status->status) {
                $return_data['message'] = 'Successfully deleted profile picture!';
                $return_data['status'] = 1;
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    public function update_profile_picture()
    {
        $return_data = [
            'status'=>0,
            'message'=>'An unknown error has occured!'
        ];

        if (!$this->identity()) {
            $return_data['message'] = "User is not authenticated!";
            die();
        }

        if ($this->input->post("base64Image")) {
            $upload_path = '_account_assets/accounts/' .  $this->user->account_id .  '/users/' .  $this->user->id . "/";
            $filename   = 'profilepicture_' . $this->user->id . '_' . uniqid() . '.jpg';

            $valid = true;

            if (!is_dir($upload_path)) {
                if (mkdir($upload_path, 0755, true)) {
                    $return_data['message'] = 'Failed to create profile picture directory!';
                    $return_data['status'] = 0;
                }
            }

            if ($valid) {
                file_put_contents($upload_path . $filename, file_get_contents($this->input->post("base64Image")));
                $return_data['message'] = 'Profile picture was successfully updated!';
                $return_data['status'] = 1;
            }

            $postdata 	  		= array_merge(['account_id'=>$this->user->account_id, 'id' => $this->user->id, 'profile_image' => $filename], $this->input->post());
            $update_profile_status	= $this->webapp_service->api_dispatcher($this->api_end_point . 'user/update', $postdata, $this->options);

            if (empty($update_profile_status)) {
                $return_data['message'] = "Unable to update user profile picture!";
                $return_data['status'] = 0;
            } else {
                if ($update_profile_status->status == 0) {
                    $return_data['message'] = "Unable to update user profile picture!";
                    $return_data['status'] = 0;
                }
            }
        }

        print_r(json_encode($return_data));
        die();
    }

    public function prepare_profile_picture()
    {
        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        $return_data = [
            'status'=>0,
            'message'=>'An unknown error has occured!'
        ];

        $upload_path = '_tmp_processing/profile_pictures/';

        $valid = true;

        if (!is_dir($upload_path)) {
            if (mkdir($upload_path, 0755, true)) {
                $return_data['message'] = 'Failed to create profile picture directory!';
                $valid = false;
            }
        }

        if ($valid) {
            $config['upload_path']          = $upload_path;
            $config['allowed_types']        = 'gif|jpg|png';
            $config['max_size']             = 5000;
            $config['file_name'] = 'tmp_profilepicture_' . $this->user->id . '_' . uniqid();

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if (! $this->upload->do_upload('image_file')) {
                $error = array('error' => $this->upload->display_errors());
                $return_data['message'] = $error;
            } else {
                $data = array('upload_data' => $this->upload->data());

                $return_data['status'] = 1;
                $return_data['message'] = "Picture was successfully uploaded!";

                $return_data['image_filename'] =  base_url($upload_path . '/' . $this->upload->data('file_name'));
            }
        }

        echo json_encode($return_data);
        die();
    }

    //log the user out
    public function logout()
    {
        if ($this->user) {
            $logout		= $this->webapp_service->api_dispatcher($this->api_end_point.'user/logout', ['account_id'=>$this->user->account_id, 'user_id'=>$this->user->id ], ['auth_token'=>$this->auth_token]);
        }
        $this->session->sess_destroy();
        redirect('/webapp/user/login', 'refresh');
    }

    //change password
    public function change_password()
    {
        if ($this->identity()) {
            if ($this->input->post()) {
                $postdata 	  		= array_merge(['account_id'=>$this->user->account_id, 'id'=>$this->user->id ], $this->input->post());
                $password_change	= $this->webapp_service->api_dispatcher($this->api_end_point.'user/change_password', $postdata, ['auth_token'=>$this->auth_token]);

                if (!empty($password_change->user) && ($password_change->status == true)) {
                    $this->session->set_flashdata('username', $this->input->post('username'));
                    $this->session->set_flashdata('message', $password_change->message);

                    if (!empty($this->user->last_login)) {
                        redirect('webapp/home/index', 'refresh');
                    } else {
                        ## Redirect to the tutorial page
                        redirect('webapp/home/index', 'refresh');
                        #redirect( 'webapp/user/tutorial', 'refresh' );
                    }
                } else {
                    //Something went wrong show error message
                    $data['message']  = (!empty($password_change->message)) ? $password_change->message : 'Oops! Something went wrong. Please try again';
                    $data['username'] = ($this->input->post('username')) ? $this->input->post('username') : null;
                    $this->_render_webpage('user/change_password', $data);
                }
            } else {
                $data['message']  = ($this->session->flashdata('message')) ? $this->session->flashdata('message') : 'Please change your password';
                $data['username'] = ($this->session->flashdata('username')) ? $this->session->flashdata('username') : null;
                $this->_render_webpage('user/change_password', $data);
            }
        } else {
            redirect('webapp/user/login', 'refresh');
        }
    }

    //forgot password
    public function forgot_password()
    {/*
        $data = false;
        $this->_render_webpage('user/forgot_password', $data);*/

        $this->load->view('user/forgot_password');
    }

    //reset password - final step for forgotten password
    public function reset_password($code = null)
    {
        $data = false;
        $this->_render_webpage('user/reset_password', $data);
    }

    //activate the user
    public function activate($id, $code=false)
    {
        $data = false;
        $this->_render_webpage('user/activate', $data);
    }

    //deactivate the user
    public function deactivate($id = null)
    {
        $data = false;
        $this->_render_webpage('user/deactivate', $data);
    }

    //create a new user
    public function create($page = 'details')
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
            $user_types	 			= $this->webapp_service->api_dispatcher($this->api_end_point.'user/user_types', ['account_id'=>$this->user->account_id], false, true);
            $data['user_types']		= (isset($user_types->user_types)) ? $user_types->user_types : null;

            $account_modules	 	= $this->webapp_service->api_dispatcher($this->api_end_point.'user/account_modules', ['account_id'=>$this->user->account_id,'inc_mod_item'=>1], ['auth_token'=>$this->auth_token], true);
            $data['account_modules']= (isset($account_modules->account_modules)) ? $account_modules->account_modules : null;


            $this->_render_webpage('user/create_user', $data);
        }
    }

    //create new user
    public function create_user($page = 'details')
    {
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $return_data = [
            'status'=>0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            ;
        } else {
            $postdata 	  = array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $new_user	  = $this->webapp_service->api_dispatcher($this->api_end_point.'user/register', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  = (isset($new_user->user)) ? $new_user->user : null;
            $message	  = (isset($new_user->message)) ? $new_user->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] = 1;
                $return_data['user']   = $new_user;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }

    public function _get_csrf_nonce()
    {
        $this->load->helper('string');
        $key   = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $this->session->set_flashdata('csrfkey', $key);
        $this->session->set_flashdata('csrfvalue', $value);
        return array($key => $value);
    }

    public function _valid_csrf_nonce()
    {
        if ($this->input->post($this->session->flashdata('csrfkey')) !== false && $this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue')) {
            return true;
        } else {
            return false;
        }
    }

    private function _get_module_id()
    {
        $result 	= false;
        $module_id 	= $this->webapp_service->get_module_id_by_controller($this->router->fetch_class());
        if (!empty($module_id)) {
            $result = (!is_array($module_id)) ? $module_id : $module_id[$this->router->fetch_class()];
        }
        return $result;
    }

    private function _check_module_role($module_id = false)
    {
        $result = false;
        if ($module_id) {
            $permissions = (isset($this->user->permissions->{$module_id})) ? $this->user->permissions->{$module_id}->permissions : false;
            $result  	 = ($permissions && in_array($module_id.'_admin', $permissions)) ? true : false;
        }
        return $result;
    }

    /**
    * Delete user (set as archived )
    **/
    public function delete_user($user_id = false)
    {
        $return_data = [
            'status'=>0
        ];

        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : (!empty($user_id) ? $user_id : null);

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_delete) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            ;
        } else {
            $postdata 	  = array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $delete_people = $this->webapp_service->api_dispatcher($this->api_end_point.'user/delete', $postdata, ['auth_token'=>$this->auth_token], true);
            $result		  = (isset($delete_people->status)) ? $delete_people->status : null;
            $message	  = (isset($delete_people->message)) ? $delete_people->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status']= 1;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }

    /*
    * User lookup / search
    */
    public function lookup()
    {
        $return_data = '';

        if (!$this->identity()) {
            $return_data .= 'Access denied! Please login';
        }

        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            $return_data .= $this->config->item('ajax_access_denied');
        } else {
            # Setup search parameters
            $search_term   = ($this->input->post('search_term')) ? $this->input->post('search_term') : false;
            $user_types    = ($this->input->post('user_types')) ? $this->input->post('user_types') : false;
            $user_statuses = ($this->input->post('user_statuses')) ? $this->input->post('user_statuses') : false;
            $limit		   = ($this->input->post('limit')) ? $this->input->post('limit') : DEFAULT_LIMIT;
            $start_index   = ($this->input->post('start_index')) ? $this->input->post('start_index') : 0;
            $offset		   = (!empty($start_index)) ? (($start_index - 1) * $limit) : 0;
            $order_by	   = false;
            $where		   = false;

            #prepare postdata
            $postdata = [
                'account_id'=>$this->user->account_id,
                'search_term'=>$search_term,
                'user_types'=>$user_types,
                'user_statuses'=>$user_statuses,
                'where'=>$where,
                'order_by'=>$order_by,
                'limit'=>$limit,
                'offset'=>$offset
            ];

            $search_result	= $this->webapp_service->api_dispatcher($this->api_end_point.'user/lookup', $postdata, ['auth_token'=>$this->auth_token], true);
            $users			= (isset($search_result->users)) ? $search_result->users : null;

            if (!empty($users)) {
                ## Create pagination
                $counters 		= $this->ion_auth->get_total_users($this->user->account_id, $search_term, $user_types, $user_statuses, $where, $limit, $offset);//Direct access to count, this should only return a number
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

                $return_data = $this->load_users_view($users);
                if (!empty($pagination)) {
                    $return_data .= '<tr><td colspan="'.(!empty($this->user->is_admin) ? '6' : '5').'" style="padding: 0;">';
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
    * Prepare Users views
    */
    private function load_users_view($users_data)
    {
        $return_data = '';
        if (!empty($users_data)) {
            foreach ($users_data as $k => $user_details) {
                $last_login		= !empty($user_details->last_login) ? date('d-m-Y H:i:s', $user_details->last_login) : false;
                $logged_out		= !empty($user_details->logout_time) ? date('d-m-Y H:i:s', $user_details->logout_time) : false;
                $time_logged_in	= !empty($last_login) ? strtotime(date('d-m-Y H:i:s')) - strtotime($last_login) : false;
                $is_logged_in 	= (!empty($last_login) && ($time_logged_in <= DEFAULT_TOKEN_VALIDITY)) ? ((!empty($logged_out) & ($logged_out > $last_login)) ? false : true) : false;
                $is_away 		= (!empty($last_login) && (($time_logged_in >= DEFAULT_TOKEN_VALIDITY) && ($time_logged_in <= (DEFAULT_TOKEN_VALIDITY*3)))) ? true : false;

                $return_data .= '<tr>';
                //$return_data .= '<td><a href="'.base_url('/webapp/user/profile/'.$user_details->id).'" >'.$user_details->account_user_id.'</a></td>';
                $return_data .= '<td><a href="'.base_url('/webapp/user/profile/'.$user_details->id).'" >'.ucwords(strtolower((!empty($user_details->first_name) ? $user_details->first_name." " : ""). $user_details->last_name)).'</a></td>';
                $return_data .= '<td>'.$user_details->email.'</td>';
                $return_data .= '<td>'.$user_details->username.'</td>';
                $return_data .= '<td>'.$user_details->user_type_name.'</td>';
                $return_data .= '<td>'.(($user_details->active == 1) ? 'Active' : 'In-active').'</td>';

                if (!empty($this->user->is_admin)) {
                    $return_data .= '<td><small>'.(!empty($user_details->last_login) ? ('<i title="'.(!empty($is_logged_in) ? 'Currently logged in' : (!empty($is_away) ? 'Last logged in the last 24 Hours' : 'Offline')).'" class="fas fa-circle text-'.(!empty($is_logged_in) ? 'green' : (!empty($is_away) ? 'orange' : 'red')).'"></i></small>&nbsp;').time_elapsed(date('d-m-Y H:i:s', $user_details->last_login)) : '<i title="Never logged in" class="far fa-times-circle text-red"></i> &nbsp;Never logged in').'<span data-first_name="'.($user_details->first_name).'" data-user_id="'.$user_details->id.'" title="Force '.$user_details->first_name.' to be logged out of the system" class="force-logout pull-right pointer '.(!empty($is_logged_in) ? '' : 'hide').'"><i class="fas fa-sign-out-alt"></i></span></td>';
                }
                $return_data .= '</tr>';
            }

            if (!empty($pagination)) {
                $return_data .= '<tr><td colspan="'.(!empty($this->user->is_admin) ? '6' : '5').'" style="padding: 0;">';
                $return_data .= $page_display.$pagination;
                $return_data .= '</td></tr>';
            }
        } else {
            $return_data .= '<tr><td colspan="'.(!empty($this->user->is_admin) ? '6' : '5').'"><br/>'.$this->config->item("no_records").'</td></tr>';
        }
        return $return_data;
    }

    /*
    * Load a audit record
    */
    public function module_items_list($user_id = false, $module_id = false)
    {
        $user_id 	= ($this->input->post('user_id')) ? $this->input->post('user_id') : (!empty($user_id) ? $user_id : null);
        $module_id 	= ($this->input->post('module_id')) ? $this->input->post('module_id') : (!empty($module_id) ? $module_id : null);

        $return_data = [
            'status'=>0,
            'module_items'=>null,
            'status_msg'=>'Invalid paramaters'
        ];

        if (!empty($module_id)) {
            $module_access 		= $this->module_service->get_module_access($this->user->account_id, $user_id, $module_id);

            $module_permissions = (!empty($module_access[0])) ? (is_array($module_access[0]) ? array_to_object($module_access[0]) : $module_access[0]) : [];

            $module_items = $this->webapp_service->api_dispatcher($this->api_end_point.'access/account_modules_items', ['account_id'=>$this->user->account_id,'module_id'=>$module_id, 'detailed'=>1 ], ['auth_token'=>$this->auth_token], true);
            $result 	= (!empty($module_items->module_items)) ? $module_items->module_items : null;
            $message	= (isset($module_items->message)) ? $module_items->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $allowed_items 		= $this->webapp_service->api_dispatcher($this->api_end_point.'access/module_item_permissions', ['account_id'=>$this->user->account_id,'module_id'=>$module_id, 'user_id'=>$user_id, 'as_list'=>1], ['auth_token'=>$this->auth_token]);
                $permitted_actions	= (!empty($allowed_items->mod_item_access)) ? $allowed_items->mod_item_access : null;
                $module = $this->load_module_permissions($result, $module_permissions, $permitted_actions);
                $return_data['status'] 	  	 = 1;
                $return_data['module_items'] = $module;
            }
            $return_data['status_msg'] = $message;
        }
        print_r(json_encode($return_data));
        die();
    }

    private function load_module_permissions($module_record = false, $module_permissions = false, $permitted_actions = false)
    {
        $module = '';
        if (!empty($module_record)) {
            $moduel_id 	= $module_record->module_details->module_id;
            $module 	= '<div class="module-access-grp" >';
            $module 	.= '<table style="width:100%">';
            $checked_perms = (!empty($permitted_actions->{$moduel_id})) ? object_to_array($permitted_actions->{$moduel_id}) : [];
            $can_view   = $can_view = $can_view = $can_view = $can_view =
            //$item_perms = ( !empty( $permitted_actions[] ) )
                    #$module .= '<tr><th width="30%">Module ID</th><td>'.$module_record->module_details->module_id.'</td></tr>';
                    $module .= '<tr>';
            $module .= '<th colspan="2" width="100%">';
            $module .= '<span id="feedback_message" style="font-weight:400;color:green"></span>';
            $module .= '<table width="100%">';
            $module .= '<tr>';
            $module .= '<td width="50%">'.$module_record->module_details->module_name.'</td>';
            $module .= '<input type="hidden" name="permissions['.$module_record->module_details->module_id.'][module_access][module_id]" value="'.$module_record->module_details->module_id.'" >';
            $module .= '<input type="hidden" name="permissions['.$module_record->module_details->module_id.'][module_access][has_access]" value="0" >';
            $module .= '<td width="30%"><div class="checkbox"><label><input class="module-access grant-access" type="checkbox" name="permissions['.$module_record->module_details->module_id.'][module_access][has_access]" value="1" data-module_id="'.$module_record->module_details->module_id.'" '.(!empty($module_permissions->has_access) ? 'checked' : '').' > <em>Grant/Revoke Access</em></label></div></td>';
            $module .= '<input type="hidden" name="permissions['.$module_record->module_details->module_id.'][module_access][is_module_admin]" value="0" >';
            $module .= '<td width="20%"><div class="checkbox pull-right"><label><input class="module-access grant-admin-access" type="checkbox" name="permissions['.$module_record->module_details->module_id.'][module_access][is_module_admin]" value="1" data-module_id="'.$module_record->module_details->module_id.'" '.(!empty($module_permissions->is_module_admin) ? 'checked' : '').' > <em>Is Admin</em></label></div></td>';
            $module .= '<input type="hidden" name="permissions['.$module_record->module_details->module_id.'][module_access][has_mobile_access]" value="0" >';
            $module .= '<tr><td colspan="3"><div class="checkbox"><label><input class="module-access grant-mobile-access" type="checkbox" name="permissions['.$module_record->module_details->module_id.'][module_access][has_mobile_access]" value="1" data-module_id="'.$module_record->module_details->module_id.'" '.(!empty($module_permissions->has_mobile_access) ? 'checked' : '').' > <em>Mobile Access</em></label></div></td></tr>';
            $module .= '</tr>';
            $module .= '</table>';
            $module .= '</th>';
            $module .= '</tr>';

            $module .= '<tr><th colspan="2" style="text-align:center"><hr></th></tr>';
            $module .= '<tr><th colspan="2">Module tabs Access Permission<hr></th></tr>';
            foreach ($module_record->module_items as $k => $item) {
                $mod_item_id = $item->module_item_id;

                $module .= '<tr>';
                $module .= '<th width="18%"><span title="Access permission to the '.ucwords($item->module_item_name).' tab ">'.ucwords($item->module_item_name).'</span></th>';
                $module .= '<td>';
                $module .= '<div class="row mod-item-grp'.$item->module_item_id.'">';
                $module .= '<input type="hidden" name="[module_id]" value="'.$module_record->module_details->module_id.'" >';

                $module .= '<input type="hidden" name="permissions['.$module_record->module_details->module_id.']['.$item->module_item_id.'][can_view]" value="0" title="Permission to view content under the '.$module_record->module_details->module_name.' '.$item->module_item_name.'" >';

                $module .= '<input type="hidden" name="permissions['.$module_record->module_details->module_id.']['.$item->module_item_id.'][can_view]" value="0" >';
                $module .= '<div class="col-md-2 col-sm-4 col-xs-12 checkbox" title="Permission to view content under the '.$module_record->module_details->module_name.' '.$item->module_item_name.' tab" ><label><input class="view mod-item-chk perms_'.$k.'_'.$item->module_item_id.' " type="checkbox" name="permissions['.$module_record->module_details->module_id.']['.$item->module_item_id.'][can_view]" value="1" data-mod_item_class="perms_'.$k.'_'.$item->module_item_id.'" data-module_id="'.$item->module_id.'" data-module_item_id="'.$item->module_item_id.'" '.(!empty($checked_perms[$mod_item_id]['can_view']) ? 'checked' : '').' > <em>View</em></label></div>';

                if (!in_array(strtolower($module_record->module_details->module_name), ['report','reports'])) {
                    $module .= '<input type="hidden" name="permissions['.$module_record->module_details->module_id.']['.$item->module_item_id.'][can_add]" value="0" >';
                    $module .= '<div class="col-md-2 col-sm-4 col-xs-12 checkbox" title="Permission to add content under the '.$module_record->module_details->module_name.' '.$item->module_item_name.' tab" ><label><input class="mod-item-chk perms_'.$k.'_'.$item->module_item_id.' " type="checkbox" name="permissions['.$module_record->module_details->module_id.']['.$item->module_item_id.'][can_add]" value="1" data-mod_item_class="perms_'.$k.'_'.$item->module_item_id.'" data-module_id="'.$item->module_id.'" data-module_item_id="'.$item->module_item_id.'" '.(!empty($checked_perms[$mod_item_id]['can_add']) ? 'checked' : '').' > <em>Add</em></label></div>';

                    $module .= '<input type="hidden" name="permissions['.$module_record->module_details->module_id.']['.$item->module_item_id.'][can_edit]" value="0" >';
                    $module .= '<div class="col-md-2 col-sm-4 col-xs-12 checkbox" title="Permission to edit content under the '.$module_record->module_details->module_name.' '.$item->module_item_name.' tab" ><label><input class="mod-item-chk perms_'.$k.'_'.$item->module_item_id.' " type="checkbox" name="permissions['.$module_record->module_details->module_id.']['.$item->module_item_id.'][can_edit]" value="1" data-mod_item_class="perms_'.$k.'_'.$item->module_item_id.'" data-module_id="'.$item->module_id.'" data-module_item_id="'.$item->module_item_id.'" '.(!empty($checked_perms[$mod_item_id]['can_edit']) ? 'checked' : '').'  > <em>Edit</em></label></div>';

                    $module .= '<input type="hidden" name="permissions['.$module_record->module_details->module_id.']['.$item->module_item_id.'][can_delete]" value="0" >';
                    $module .= '<div class="col-md-2 col-sm-4 col-xs-12 checkbox" title="Permission to delete content under the '.$module_record->module_details->module_name.' '.$item->module_item_name.' tab" ><label><input class="mod-item-chk perms_'.$k.'_'.$item->module_item_id.' " type="checkbox" name="permissions['.$module_record->module_details->module_id.']['.$item->module_item_id.'][can_delete]" value="1" data-mod_item_class="perms_'.$k.'_'.$item->module_item_id.'" data-module_id="'.$item->module_id.'" data-module_item_id="'.$item->module_item_id.'" '.(!empty($checked_perms[$mod_item_id]['can_delete']) ? 'checked' : '').'  > <em>Delete</em></label></div>';

                    $module .= '<input type="hidden" name="permissions['.$module_record->module_details->module_id.']['.$item->module_item_id.'][is_admin]" value="0" >';
                    $module .= '<div class="col-md-3 col-sm-4 col-xs-12 checkbox" title="Full access to the '.$item->module_item_name.' tab" ><label><input class="mod-item-chk perms_'.$k.'_'.$item->module_item_id.' check-all" id="perms_'.$k.'_'.$item->module_item_id.'" type="checkbox" name="permissions['.$module_record->module_details->module_id.']['.$item->module_item_id.'][is_admin]" value="1" data-mod_item_class="perms_'.$k.'_'.$item->module_item_id.'" data-module_id="'.$item->module_id.'" data-module_item_id="'.$item->module_item_id.'" '.(!empty($checked_perms[$mod_item_id]['is_admin']) ? 'checked' : '').'  > <em>Full Access</em></label></div>';
                }
                $module .= '</td>';
                $module .= '</td>';
                $module .= '</tr>';
            }
            $module .= '</table>';
            $module .= '</div>';
        }
        return $module;
    }

    /** Update Module Permissions **/
    public function update_module_permissions($user_id = false, $module_id = false)
    {
        $data['status'] = 0;
        $data['status_msg'] 	= 'Hello one';

        if (!empty($user_id)) {
            $data 	= ($this->input->post()) ? $this->input->post() : null;

            if (!empty($data)) {
                $postdata 	  = array_merge(['account_id'=>$this->user->account_id, 'user_id'=>$user_id ], $this->input->post());

                $update_perms = $this->webapp_service->api_dispatcher($this->api_end_point.'access/update_module_permissions', $postdata, ['auth_token'=>$this->auth_token]);

                $result 	  = (!empty($update_perms->module_permissions)) ? $update_perms->module_permissions : null;
                $message	  = (isset($update_perms->message)) ? $update_perms->message : 'Oops! There was an error processing your request.';

                if (!empty($result)) {
                    $data['status'] 	= 1;
                    $data['status_msg']	= $message;
                }
            }
        }
        print_r(json_encode($data));
        die();
    }

    public function tutorial()
    {
        if ($this->user->tier_id == 2) {
            $this->load->view('user/tutorial/Manage/manage-tutorial', $data);
        } else {
            $this->load->view('user/tutorial/Info/info-tutorial', $data);
        }
    }

    /** Get Users who are logged In **/
    public function whois_logged_in()
    {
        $return_data = '';

        if (!$this->identity()) {
            $return_data .= 'Access denied! Please login';
        }

        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            $return_data .= $this->config->item('ajax_access_denied');
        } else {
            # Setup search parameters
            $search_term   = ($this->input->post('search_term')) ? $this->input->post('search_term') : false;
            $user_types    = ($this->input->post('user_types')) ? $this->input->post('user_types') : false;
            $user_statuses = ($this->input->post('user_statuses')) ? $this->input->post('user_statuses') : false;
            $limit		   = ($this->input->post('limit')) ? $this->input->post('limit') : 10;
            $start_index   = ($this->input->post('start_index')) ? $this->input->post('start_index') : 0;
            $offset		   = (!empty($start_index)) ? (($start_index - 1) * $limit) : 0;
            $order_by	   = false;
            $where		   = ['logged_in'=>1];

            #prepare postdata
            $postdata = [
                'account_id'=>$this->user->account_id,
                'search_term'=>$search_term,
                'user_types'=>$user_types,
                'user_statuses'=>$user_statuses,
                'where'=>$where,
                'order_by'=>$order_by,
                'limit'=>$limit,
                'offset'=>$offset
            ];

            $search_result	= $this->webapp_service->api_dispatcher($this->api_end_point.'user/lookup', $postdata, ['auth_token'=>$this->auth_token], true);
            $users			= (isset($search_result->users)) ? $search_result->users : null;

            if (!empty($users)) {
                ## Create pagination
                $counters 		= $this->ion_auth->get_total_users($this->user->account_id, $search_term, $user_types, $user_statuses, $where, $limit, $offset);//Direct access to count, this should only return a number
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

                $return_data = $this->load_logged_in_users_view($users);
                if (!empty($pagination)) {
                    $return_data .= '<tr><td colspan="3" style="padding: 0;">';
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
    * Load Logged In Users views
    */
    private function load_logged_in_users_view($users_data)
    {
        $return_data = '';
        if (!empty($users_data)) {
            foreach ($users_data as $k => $user_details) {
                $last_login		= !empty($user_details->last_login) ? date('d-m-Y H:i:s', $user_details->last_login) : false;
                $logged_out		= !empty($user_details->logout_time) ? date('d-m-Y H:i:s', $user_details->logout_time) : false;
                $time_logged_in	= !empty($last_login) ? strtotime(date('d-m-Y H:i:s')) - strtotime($last_login) : false;
                $is_logged_in 	= (!empty($last_login) && ($time_logged_in <= DEFAULT_TOKEN_VALIDITY)) ? ((!empty($logged_out) & ($logged_out > $last_login)) ? false : true) : false;
                $is_away 		= (!empty($last_login) && (($time_logged_in >= DEFAULT_TOKEN_VALIDITY) && ($time_logged_in <= (DEFAULT_TOKEN_VALIDITY*3)))) ? true : false;

                $return_data .= '<tr>';
                $return_data .= '<td><a href="'.base_url('/webapp/user/profile/'.$user_details->id).'" >'.ucwords(strtolower((!empty($user_details->first_name) ? $user_details->first_name." " : ""). $user_details->last_name)).'</a></td>';
                $return_data .= '<td>'.$user_details->username.'</td>';
                $return_data .= '<td><small>'.(!empty($user_details->last_login) ? ('<i title="'.(!empty($is_logged_in) ? 'Currently logged in' : (!empty($is_away) ? 'Last logged in the last 24 Hours' : 'Offline')).'" class="fas fa-circle text-'.(!empty($is_logged_in) ? 'green' : (!empty($is_away) ? 'orange' : 'red')).'"></i></small>&nbsp;').time_elapsed(date('d-m-Y H:i:s', $user_details->last_login)) : '<i title="Never logged in" class="far fa-times-circle text-red"></i> &nbsp;Never logged in').'<span data-first_name="'.($user_details->first_name).'" data-user_id="'.$user_details->id.'" title="Force '.$user_details->first_name.' to be logged out of the system" class="force-logout pull-right pointer '.(!empty($is_logged_in) ? '' : 'hide').'"><i class="fas fa-sign-out-alt"></i></span></td>';
                $return_data .= '</tr>';
            }

            if (!empty($pagination)) {
                $return_data .= '<tr><td colspan="3" style="padding: 0;">';
                $return_data .= $page_display.$pagination;
                $return_data .= '</td></tr>';
            }
        } else {
            $return_data .= '<tr><td colspan="3"><br/>'.$this->config->item("no_records").'</td></tr>';
        }
        return $return_data;
    }


    /**
    * Associate a User to a Contract
    **/
    public function associate_users()
    {
        $section 	 = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
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
            $associated_users 	 = $this->webapp_service->api_dispatcher($this->api_end_point.'user/associate_users', $postdata, ['auth_token'=>$this->auth_token]);

            $result		  	 = (isset($associated_users->associated_users)) ? $associated_users->associated_users : null;
            $message	  	 = (isset($associated_users->message)) ? $associated_users->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] 	 = 1;
                $text_color 			 = 'auto';
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    /**
    * Disassociate Users
    **/
    public function disassociate_users($person_id = false)
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
            $disassociate_users	= $this->webapp_service->api_dispatcher($this->api_end_point.'user/disassociate_users', $postdata, ['auth_token'=>$this->auth_token]);
            $result			= (isset($disassociate_users->status)) ? $disassociate_users->status : null;
            $message		= (isset($disassociate_users->message)) ? $disassociate_users->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] = 1;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    /**
    * Associate a User to a list of Buildings
    **/
    public function associate_buildings()
    {
        $section 	 = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
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
            $postdata 	  	 		= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $associated_buildings 	= $this->webapp_service->api_dispatcher($this->api_end_point.'site/associate_buildings', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  	 		= (isset($associated_buildings->associated_buildings)) ? $associated_buildings->associated_buildings : null;
            $message	  	 		= (isset($associated_buildings->message)) ? $associated_buildings->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] 	 = 1;
                $text_color 			 = 'auto';
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    /**
    * Disassociate Buildings
    **/
    public function disassociate_buildings($person_id = false)
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
            $disassociate_buildings	= $this->webapp_service->api_dispatcher($this->api_end_point.'site/disassociate_buildings', $postdata, ['auth_token'=>$this->auth_token]);
            $result			= (isset($disassociate_buildings->status)) ? $disassociate_buildings->status : null;
            $message		= (isset($disassociate_buildings->message)) ? $disassociate_buildings->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] = 1;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    /** Switch user account **/
    public function switch_user_account($id = false, $page = 'permissions')
    {
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());

        $return_data = [
            'status'=>0
        ];
        $user_id = ($this->input->post('id')) ? $this->input->post('id') : (!empty($id) ? $id : null);

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            ;
        } else {
            $postdata 	  		= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $switched_account	= $this->webapp_service->api_dispatcher($this->api_end_point.'user/switch_user_account', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  		= (isset($switched_account->user_account)) ? $switched_account->user_account : null;
            $message	 		= (isset($switched_account->message)) ? $switched_account->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] = 1;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }


    /**
    * Force user logout
    **/
    public function force_user_logout($user_id = false, $page = 'details')
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

        if (!$this->user->is_admin && empty($item_access->can_delete) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            ;
        } else {
            $postdata 	  = array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $user_logout  = $this->webapp_service->api_dispatcher($this->api_end_point.'user/force_logout', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  = (isset($user_logout->status)) ? $user_logout->status : null;
            $message	  = (isset($user_logout->message)) ? $user_logout->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status']= 1;

                if ($this->user->id == $postdata['user_id']) {
                    $this->session->sess_destroy();
                }
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }
}
