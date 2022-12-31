<?php

namespace Application\Modules\Service\Models;

use App\Adapter\Model;
use Firebase\JWT\JWT;

class AccountModel extends Model
{
    private $blacklisted_table_columns = [ 'created_by', 'archived', 'is_active', 'last_modified', 'last_modified_by', 'date_created', 'assignee', 'account_id', 'user_id' ];
    private $whitelisted_config_tables = [ 'asset_types', 'address_types', 'attribute_response_types', 'audit_types', 'cost_item_types', 'evidoc_response_types', 'job_types', 'location_types', 'people_event_types', 'project_action_types', 'project_types', 'user_types', 'user_group_types'];


    public function __construct()
    {
        parent::__construct();
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->lang->load('auth');
    }

    //THis is a list of table options that must be copied at the time of Account Activation, when list gets over 15, remind me to move this to DB
    private $req_defaults_opts = [
        /*[ 'table_name'=>'asset_types', 'primary_key'=>'asset_type_id' ],*/
        /*[ 'table_name'=>'audit_categories', 'primary_key'=>'category_id' ],*/
        [ 'table_name'=>'cost_item_types', 'primary_key'=>'cost_item_type_id' ],
        [ 'table_name'=>'evidoc_response_types', 'primary_key'=>'response_type_id' ],
        [ 'table_name'=>'address_types', 'primary_key'=>'address_type_id' ],
        [ 'table_name'=>'asset_eol_statuses', 'primary_key'=>'eol_group_id' ],
        [ 'table_name'=>'asset_statuses', 'primary_key'=>'status_id' ],
        [ 'table_name'=>'user_statuses', 'primary_key'=>'status_id' ],
        [ 'table_name'=>'audit_result_statuses', 'primary_key'=>'audit_result_status_id' ],
        [ 'table_name'=>'audit_action_statuses', 'primary_key'=>'action_status_id' ],
        [ 'table_name'=>'people_categories', 'primary_key'=>'category_id' ],
        [ 'table_name'=>'job_fail_codes', 'primary_key'=>'fail_code_id' ],
        [ 'table_name'=>'schedule_frequencies', 'primary_key'=>'frequency_id' ],
        [ 'table_name'=>'job_tracking_statuses', 'primary_key'=>'job_tracking_id' ],
    ];

    private $account_searchable_fields  = ['account_name', 'account_first_name', 'account_last_name', 'account_email', 'account_membership_number'];

    /*
    * Get Accounts single records or multiple records
    */
    public function get_accounts($account_id = false, $offset = 0, $limit = 20)
    {
        $result = false;
        $this->db->where('archived !=', 1);
        if ($account_id) {
            $row = $this->db->get_where('account', ['account_id'=>$account_id])->row();
            if (!empty($row)) {
                $this->session->set_flashdata('message', 'Account found');
                $result = $row;
            } else {
                $this->session->set_flashdata('message', 'Account not found');
            }
            return $result;
        }

        $accounts = $this->db->order_by('account_name')
            ->offset($offset)
            ->limit($limit)
            ->get('account');

        if ($accounts->num_rows() > 0) {
            $this->session->set_flashdata('message', 'Account records found');
            $result = $accounts->result();
        } else {
            $this->session->set_flashdata('message', 'Account record(s) not found');
        }
        return $result;
    }

    /*
    * Create new Account
    */
    public function create_account($account_data = false)
    {
        $result = false;
        if (!empty($account_data)) {
            $data = $modules = [];

            if (isset($account_data['account_modules']) && !empty($account_data['account_modules'])) {
                $modules = $account_data['account_modules'];
                $modules = (!is_array($modules)) ? json_decode($modules) : $modules;
                $modules = (is_object($modules)) ? object_to_array($modules) : $modules;
                unset($account_data['account_modules']);
            }

            //If user admin is not selected, force it in
            if (!in_array(1, $modules)) {
                $modules[] = 1;
            }

            foreach ($account_data as $key=>$value) {
                if (in_array($key, format_name_columns())) {
                    $value = format_name($value);
                } elseif (in_array($key, format_email_columns())) {
                    $value = format_email($value);
                } elseif (in_array($key, format_number_columns())) {
                    $value = format_number($value);
                } else {
                    $value = is_string($value) ? trim($value) : $value;
                }
                $data[$key] = $value;
            }

            if (!empty($data)) {
                $data['is_active'] 			= 0;
                $data['account_status'] 	= 'Active';
                $data['license_type'] 		= 'Monthly';
                $data['trial_start_date']	= date('Y-m-d H:i:s');
                $data['license_start_date']	= date('Y-m-d H:i:s', strtotime('+ '.API_ACCOUNT_TRIAL_PERIOD)); //Set account Trial Period
                $data['account_reference'] 	= 'EVDT_'.strtoupper(preg_replace('/\s+/', '', $data['account_name']));

                $account_exists = $this->db->get_where('account', ['account_reference'=>$data['account_reference']])->row();
                if (!$account_exists) {
                    $new_account_data = $this->ssid_common->_filter_data('account', $data);
                    $this->db->trans_begin(); //Be ready to rollback if an error occurs with user creation
                    $this->db->insert('account', $new_account_data);
                    if ($this->db->trans_status() !== false) {
                        $data['account_id'] = $this->db->insert_id();

                        if (!empty($data['account_id'])) {
                            $account_uuid 			= $this->generate_account_uuid($data);
                            ## Generate Account Membership Number
                            $acc_membership_number	= $this->generate_membership_number($data['account_id']);

                            $this->db->where('account_id', $data['account_id'])->update('account', ['account_uuid'=>$account_uuid]);
                            $result = $this->get_accounts($data['account_id']);

                            ## Create account modules
                            $data['account_id'] = $result->account_id;
                            $account_modules 	= $this->create_account_modules($data, $modules);

                            ## Created Admin User account, append username if one was available on sign-up
                            $result->admin_username	= $data['admin_username'];
                            $admin_user = $this->create_admin_user_account($result);

                            if (!empty($admin_user)) {
                                $temp_password = $this->session->flashdata('temp_password');

                                $this->db->trans_commit();//We're good to commit this now

                                ## Generate User Membership Number
                                $user_membership_number	= $this->generate_membership_number($data['account_id'], $admin_user->id);

                                ## Create Module
                                if (!empty($admin_user) && isset($account_modules) && !empty($account_modules)) {
                                    $user_module_access = $this->module_service->create_user_module_access($admin_user, object_to_array($account_modules));
                                    //$login_details 		= ( !empty( $admin_user->username ) ) ? ' You username is '.$admin_user->username : NULL;

                                    //Send an account activation email
                                    $result->temp_password  = (!empty($temp_password)) ? $temp_password : 'W3lc0m3!';
                                    $this->send_account_activation_email($result);
                                }
                                # $this->session->set_flashdata('message','Main account record created successfully.'. ( !empty($login_details) ? $login_details : '' ) );
                                $this->session->set_flashdata('message', 'Main account record created successfully. An email has been sent to '.$data['account_email'].' with activation instructions.');
                            } else {
                                $this->db->trans_rollback();//Rollback the account creation transaction
                                $this->ssid_common->_reset_auto_increment('account', 'account_id');
                                $this->ssid_common->_reset_auto_increment('user', 'id');
                                $this->ssid_common->_reset_auto_increment('user_groups', 'id');

                                $message = $this->session->flashdata('message');
                                $this->session->set_flashdata('message', 'Main account creation failed (User fields). '.$message);
                                $result = false;
                            }
                        } else {
                            $result = false;
                            $this->session->set_flashdata('message', 'Main account creation failed.');
                        }
                    }
                } else {
                    $this->session->set_flashdata('message', 'This Account name already exists.');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Account data supplied.');
        }
        return $result;
    }

    /*
    * Update Account
    */
    public function update_account($account_id = false, $account_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($account_data)) {
            $data = [];
            foreach ($account_data as $key=>$value) {
                if (in_array($key, format_name_columns())) {
                    $value = format_name($value);
                } elseif (in_array($key, format_email_columns())) {
                    $value = format_email($value);
                } elseif (in_array($key, format_number_columns())) {
                    $value = format_number($value);
                } else {
                    $value = trim($value);
                }
                $data[$key] = $value;
            }

            if (!empty($data)) {
                $account_id 			= $data['account_id'];
                #$preserve_account_name 	= $data['account_name'];
                #unset($data['account_name']); //You can't update Account name using this method.
                $data['account_reference'] = 'EVDT_'.strtoupper(preg_replace('/\s+/', '', $data['account_name']));
                $data = $this->ssid_common->_filter_data('account', $data);

                $this->db->where('account_id', $account_id)->update('account', $data);
                if ($this->db->trans_status() !== false) {
                    ##Created a Log of what has changed for the entire account record

                    #$data['account_name'] 		= $preserve_account_name;
                    $result = $this->get_accounts($account_id);
                    $this->session->set_flashdata('message', 'Account record updated successfully.');
                } else {
                    $this->session->set_flashdata('message', 'There was an Error while trying to update the Account record.');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Account data supplied.');
        }
        return $result;
    }

    /*
    * Delete Account record
    */
    public function delete_account($admin_account_id = false, $account_id = false)
    {
        $result = false;
        if ($admin_account_id) {
            $data = ['archived'=>1];
            $this->db->where('account_id', $account_id)
                ->update('account', $data);
            if ($this->db->trans_status() !== false) {
                $this->session->set_flashdata('message', 'Record deleted successfully.');
                $result = true;
            }
        } else {
            $this->session->set_flashdata('message', 'No Account ID supplied.');
        }
        return $result;
    }

    /** Create admin user **/
    public function create_admin_user_account($account_data = false)
    {
        $result 		= false;
        $account_data	= (!empty($account_data)) ? $account_data : null;
        if (!empty($account_data)) {
            $identity   = $this->config->item('identity', 'ion_auth');
            $email 	  	= $account_data->account_email;
            $first_name = ucwords(strtolower($account_data->account_first_name));
            $last_name 	= ucwords(strtolower($account_data->account_last_name));
            $username 	= (!empty($account_data->admin_username)) ? $account_data->admin_username : (($identity == 'email') ? trim(strtolower($account_data->account_email)) : strtolower(trim($first_name).trim($last_name)));
            #$password 	= 'welcome';
            $password 	= $this->ssid_common->generate_random_password();//Generate random password

            ## Validate primary user
            $tables 	= $this->config->item('tables', 'ion_auth');
            $validate_data = array(
                'account_id' => $account_data->account_id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $account_data->account_email,

            );

            $this->form_validation->set_data($validate_data);

            $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
            $this->form_validation->set_rules('first_name', 'Account User First name', 'required');
            $this->form_validation->set_rules('last_name', 'Account User Last name', 'required');
            $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email|is_unique[' . $tables['user'] . '.email]');

            if ($this->form_validation->run() == false) {
                $validation_errors = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            }

            if (!$email || !$username || (isset($validation_errors) && !empty($validation_errors))) {
                ## One of the required fields is invalid
                $message = [
                    'status' => false,
                    'message' => 'Invalid user account data: ',
                    'user' => null
                ];
                $message['message'] = (!$email) ? $message['message'].'email, ' : $message['message'];
                $message['message'] = (!$username) ? $message['message'].'username, ' : $message['message'];
                $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
                $this->session->set_flashdata('message', $message['message']);
                return false;
            }

            $additional_data = array(
                'first_name' => $first_name,
                'last_name'  => $last_name,
                'account_id' => $account_data->account_id,
                'user_type_id' => 1, //Account admin type
                'is_account_holder' => 1, //Main account holder
                'change_password' => 1 //force password change on first login attempt
            );

            $user_id = $this->ion_auth->register($username, $password, $email, $additional_data);

            if (!empty($user_id)) {
                $this->session->set_flashdata('temp_password', $password);
                $new_user = $this->ion_auth->get_user_by_id($account_data->account_id, $user_id);

                #Check user-group perms
                //$this->ion_auth->add_to_group( array(1) ,$user_id );
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                return $new_user;
            } else {
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                return false;
            }
        }
        return $result;
    }

    public function check_account_status($account_id = false, $ignore_token = false)
    {
        $result = false;
        if ($account_id) {
            if (!empty($this->ion_auth->_current_user->is_admin) && in_array($this->ion_auth->_current_user->id, SUPER_ADMIN_ACCESS) && !empty($ignore_token)) {
                $this->session->set_flashdata('message', 'Superadmin Access granted successfully.');
                return true;
            }

            $account = $this->db->get_where('account', ['account_id'=>$account_id])->row();

            if (!empty($account)) {
                if (((!empty($this->ion_auth->_current_user()) && ((int)$account->account_id == (int)$this->ion_auth->_current_user()->account_id))) || $ignore_token) {
                    $status 	 	 = $account->account_status;
                    $trial_period	 = strtotime(date('Y-m-d H:i:s', strtotime($account->trial_start_date.' + 1 Month')));
                    $date_time_now   = strtotime(date('Y-m-d H:i:s'));
                    if ($account->is_active != 1) {
                        $this->session->set_flashdata('message', 'The main Business account has not been activated. Please contact your system admin.');
                        $result = false;
                    } elseif (($status == 'Active') || ($status == 'Trial' && ($trial_period > $date_time_now))) {
                        $result = true;
                        $this->session->set_flashdata('message', 'Account verified successfully.');
                    } elseif ($status == 'Trial' && ($trial_period < $date_time_now)) {
                        $this->session->set_flashdata('message', 'The main Business account trial has expired. Please contact customer support to activate your account.');
                        $result = false;
                    } else {
                        $this->session->set_flashdata('message', 'The main Business account is '.$status.'. Please contact your system admin.');
                        $result = false;
                    }
                } else {
                    $this->session->set_flashdata('message', 'Access denied. This account does not belong to you.');
                }
            } else {
                $this->session->set_flashdata('message', 'This Business account does not exist');
            }
        }
        return $result;
    }

    public function get_account_modules($account_id = false, $inc_module_items = true, $categorized = false)
    {
        $result = false;
        if ($account_id) {
            $query = $this->db->select('am.*,um.module_id,um.app_uuid,um.module_name,um.module_price, mc.*', false)
                ->where('am.account_id', $account_id)
                ->where('um.is_active', 1)
                ->join('user_modules um', 'um.module_id = am.module_id', 'left')
                ->join('user_module_categories mc', 'um.category_id = mc.category_id', 'left')
                ->order_by('um.module_ranking, um.module_name')
                ->get('account_modules am');

            if ($query->num_rows() > 0) {
                if ($inc_module_items) {
                    foreach ($query->result() as $row) {
                        $module_items 		= $this->get_account_modules_items($row->module_id);
                        $row->module_items  = (!empty($module_items)) ? $module_items : null;
                        if ($categorized) {
                            $result[$row->category_name]['category']  = [
                                'category_name'=>$row->category_name,
                                'category_color'=>$row->category_color,
                                'category_icon_class'=>$row->category_icon_class
                            ];
                            $result[$row->category_name]['modules'][] = $row;
                        } else {
                            $result[] = $row;
                        }
                    }
                } else {
                    if ($categorized) {
                        foreach ($query->result() as $row) {
                            $result[$row->category_name]['category']  = [
                                'category_name'=>$row->category_name,
                                'category_color'=>$row->category_color,
                                'category_icon_class'=>$row->category_icon_class
                            ];
                            $result[$row->category_name]['modules'][] = $row;
                        }
                    } else {
                        $result = $query->result();
                    }
                }
                $this->session->set_flashdata('message', 'Account module(s) found');
            } else {
                $this->session->set_flashdata('message', 'This Account has no modules associated with it');
                $result = false;
            }
        }
        return $result;
    }

    /** Get Purchased account module-items**/
    public function get_account_modules_items($module_id = false, $detailed = false, $mobile_visible = false)
    {
        $result = false;
        if ($module_id) {
            if ($mobile_visible) {
                $this->db->where('umi.mobile_visible', 1);
            }

            $query = $this->db->select('umi.*, um.module_id, um.module_name, um.is_active', false)
                ->where('umi.module_id', $module_id)
                ->where('umi.is_active', 1)
                ->join('user_modules um', 'um.module_id = umi.module_id', 'left')
                ->order_by('umi.module_item_sort, umi.module_item_name')
                ->get('user_module_items umi');

            if ($query->num_rows() > 0) {
                if ($detailed) {
                    $data = [];
                    foreach ($query->result() as $k => $row) {
                        $data['module_details'] = [
                            'module_id'=>$row->module_id,
                            'module_name'=>$row->module_name,
                            'is_active'=>$row->is_active
                        ];
                        $data['module_items'][] = $row;
                    }
                    $result = $data;
                } else {
                    $result = $query->result();
                }
                $this->session->set_flashdata('message', 'Module items found');
            } else {
                $this->session->set_flashdata('message', 'Module items not found');
            }
        }
        return $result;
    }

    /**
    * Create a list of modules / app-access that the business has purchased or
    **/
    public function create_account_modules($account_data, $modules = array(1))
    {
        $result = [];
        $account_id = $account_data['account_id'];
        if (!empty($account_id) && !empty($modules)) {
            $data    = [];
            sort($modules);
            foreach ($modules as $module_id) {
                $module_details = $this->module_service->get_modules($module_id, false, false);
                $data = [
                    'module_id'=>$module_id,
                    'account_id'=>$account_id,
                    'standard_price'=>$module_details->module_price,
                    'adjusted_price'=>$module_details->module_price,
                    'license_valid_from'=>date('Y-m-d H:i:s'),
                    'license_valid_to'=>(!empty($account_data['license_valid_to'])) ? $account_data['license_valid_to'] : null,
                    'license_type'=>(!empty($account_data['license_type'])) ? $account_data['license_type'] : 'Monthly'
                ];

                $conditions = ['account_id'=>$account_id,'module_id'=>$module_id];
                $check_exists = $this->db->get_where('account_modules', $conditions)->row();
                if (!empty($check_exists)) {
                    $data['last_modified'] = date('Y-m-d H:i:s');
                    $this->db->where($conditions);
                    $this->db->update('account_modules', $data);
                } else {
                    $this->db->insert('account_modules', $data);
                }
            }
            if ($result) {
                $this->session->set_flashdata('message', 'Account modules added successfully');
            }
            $result = $this->get_account_modules($account_id);
        }
        return $result;
    }

    /*
    * Generate a unique identifier for an account record
    */
    public function generate_account_uuid($account_data = false)
    {
        $result = null;
        if ($account_data) {
            $account_data = (is_object($account_data)) ? (array)$account_data : $account_data;
            #$result 	  = base64_encode( base64_encode($account_data['account_id'].'.'.$account_data['account_reference'].'.'.API_JWT_ALGORITHM) );
            $result 	  = strtoupper(hash('sha256', $account_data['account_id'].'.'.$account_data['account_reference'].'.'.API_JWT_ALGORITHM));
        }
        return $result;
    }

    /**
    * Rollback account creation
    **/
    private function _rollback_account_creation($account_id = false)
    {
        $result = false;
        if (!empty($account_id)) {
            # Drop all modules setup for this account
            $this->db->where('account_id', $account_id)
                ->delete('account_modules');


            # Reset the table auto_increment

            # Drop all modules setup for this account
            $this->db->where('account_id', $account_id)
                ->delete('account');

            $result = ($this->db->trans_status() !== false) ? true : $result;
        }
        return $result;
    }

    /*
    * Send Account Activation Email
    */
    public function send_account_activation_email($account_data = false)
    {
        $result = false;
        if ($account_data) {
            $account_data->activation_code = $this->ssid_common->_encode_activation_code(['account_id'=>$account_data->account_id,'account_reference'=>$account_data->account_reference ]);
            $data['account_data'] = $account_data;
            $email_body = $this->load->view('email_templates/account/account_activation', $data, true);
            $destination= $account_data->account_email;

            $email_data = [
                'to'=>$destination,
                'from'=>['yourfeedback@lovedigitaltv.co.uk','Evident Software Ltd'],
                'subject'=>'Activate your account',
                'message'=>$email_body
            ];

            $result = $this->mail->send_mail($email_data);
            if ($result) {
                $bcc_email_body = $this->load->view('email_templates/account/signup_notification', $data, true);
                ## Blind Copy the Seniors
                $bcc_email_data = [
                    'to'=>['welcome@evidentsoftware.co.uk'],
                    'from'=>['yourfeedback@lovedigitaltv.co.uk','Evident Software Ltd'],
                    'subject'=>'New Account Signup',
                    'message'=>$bcc_email_body
                ];

                $result2 = $this->mail->send_mail($bcc_email_data);
            }
        }
        return $result;
    }

    /*
    * Activate Account from email link
    */
    public function activate_account($account_id = false, $activation_code = false)
    {
        $result = false;
        if (!empty($activation_code)) {
            $account_data = $this->ssid_common->_decode_activation_code($activation_code);
            $account_id = (!empty($account_data)) ? $account_data->account_id : null;
        }

        if (!empty($account_id)) {
            $check_exists = $this->db->get_where('account', ['account_id'=>$account_id])->row();
            if (!empty($check_exists)) {
                if ($check_exists->is_active != 1) {
                    $this->db->where('account_id', $check_exists->account_id)
                        ->update('account', [ 'is_active'=>1, 'first_activated_on'=>date('Y-m-d H:i:s') ]);

                    if ($this->db->trans_status() !== false) {
                        //Copy Default options
                        if (!empty($this->req_defaults_opts)) {
                            foreach ($this->req_defaults_opts  as $k => $table_info) {
                                $this->copy_account_options($account_id, $table_info);
                            }
                        }

                        ## Activate Disciplines
                        $disciplines = $this->db->select('discipline_id', false)->get_where('discipline', [ 'is_active'=> 1 ]);

                        if ($disciplines->num_rows() > 0) {
                            $this->load->model('serviceapp/Discipline_model', 'discipline_service');

                            $discipline_ids  = array_column($disciplines->result_array(), 'discipline_id');

                            $activation_data = [
                                'account_id' 	=> $check_exists->account_id,
                                'discipline_id' => $discipline_ids,
                            ];

                            $activated = $this->discipline_service->activate_account_disciplines($check_exists->account_id, [ 'activation_data' => $activation_data ]);
                        }

                        $this->session->set_flashdata('message', 'Thank you, your account has been activated successfully');
                        $result = true;
                    } else {
                        $this->session->set_flashdata('message', 'Account activation request failed. Please contact your system administrator');
                        $result = false;
                    }
                } else {
                    $this->session->set_flashdata('message', 'This account is already activated. Please login');
                    $result = true;
                }
            }
        }

        return $result;
    }

    /*
    * Get all packages available to select from
    */
    public function get_packages()
    {
        $result = false;
        $query = $this->db->where('is_active', 1)
            ->order_by('package_order')
            ->get('account_package_tiers');

        if ($query->num_rows() > 0) {
            $this->session->set_flashdata('message', 'Package information found');
            $result = $query->result();
        } else {
            $this->session->set_flashdata('message', 'No Package information found');
        }
        return $result;
    }

    /** Copy a list of default Options for an account **/
    public function copy_account_options($account_id = false, $table_options = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($table_options)) {
            $account_id  = (!empty($table_options['account_id'])) ? $table_options['account_id'] : $account_id;
            $table_name  = (!empty($table_options['table_name'])) ? $table_options['table_name'] : false;
            $primary_key = (!empty($table_options['primary_key'])) ? $table_options['primary_key'] : false;

            if (!empty($table_name) && !empty($primary_key)) {
                $query = $this->db->select($table_name.'.*')
                ->where('( '.$table_name.'.account_id IS NULL OR '.$table_name.'.account_id = "" )')
                ->get($table_name);

                if ($query->num_rows()) {
                    $new_list = [];
                    foreach ($query->result() as $k => $row) {
                        unset($row->{$primary_key});
                        $row->account_id = $account_id;
                        $new_list[$k]    = (array) $row;
                    }

                    $already_exists = $this->db->where($table_name.'.account_id', $account_id)
                        ->get($table_name);

                    if ($already_exists->num_rows() > 0) {
                        $result = $already_exists->result();
                        $this->session->set_flashdata('message', 'Account options already exist!');
                    } else {
                        $this->db->insert_batch($table_name, $new_list);
                        if ($this->db->trans_status() !== false) {
                            $query = $this->db->where($table_name.'.account_id', $account_id)
                                ->get($table_name);
                            $result = $query->result();
                        }
                        $this->session->set_flashdata('message', 'Account options created successfully');
                    }
                }
            } else {
                $this->session->set_flashdata('message', 'Your request is missing required information');
            }
        }
        return $result;
    }


    /*
    *	 Public function to save the payments details_id
    */
    public function save_payment_details($account_id = false, $payment_details = false)
    {
        if (!empty($account_id) && (!empty($payment_details))) {
            $result 			= false;
            $payment_details 	= (is_object($payment_details)) ? object_to_array($payment_details) : ( array ) $payment_details;

            if (!empty($payment_details)) {
                $payment_data["preferred_payment_method"] = $payment_details['preferred_payment_method'];
                if (!empty($payment_details['card']) && !empty(array_values($payment_details['card']))) {
                    foreach ($payment_details['card'] as $key => $value) {
                        $payment_data[$key]	= $value;
                    }
                }

                if (!empty($payment_details['bank']) && !empty(array_values($payment_details['bank']))) {
                    foreach ($payment_details['bank'] as $key => $value) {
                        $payment_data[$key]	= $value;
                    }
                }

                $payment_data['date_created'] 	= date('Y-m-d H:i:s');
                $payment_data['account_id'] 	= $account_id;

                $new_payment_data = $this->ssid_common->_filter_data('payment_details', $payment_data);
                $query = $this->db->insert("payment_details", $new_payment_data);

                $details_id = $this->db->insert_id();

                if ((!empty($details_id)) && ($details_id > 0)) {
                    $this->session->set_flashdata('message', 'Payment Details have been recorded');
                    $result = $this->db->get_where("payment_details", ["account_id" => $account_id, "details_id" => $details_id ])->row();
                } else {
                    $this->session->set_flashdata('message', 'Payment Details have NOT been recorded');
                }
            } else {
                $this->session->set_flashdata('message', 'Payment Details are missing');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID or Payment Details are missing');
        }

        return $result;
    }

    /** Get configurable tables **/
    public function get_configurable_tables($account_id = false, $module_id = false, $grouped = false)
    {
        $result = false;

        $this->db->select('sct.*, um.module_name, um.module_controller', false)
            ->join('user_modules um', 'um.module_id = sct.module_id', 'left')
            ->where('sct.is_configurable', 1);

        if (!empty($account_id)) {
            //If we decide to start doing this per account, uncomment this block and append account IDs to the Table
            #$this->db->where( 'sct.account_id', $account_id );
        }

        if (!empty($module_id)) {
            $this->db->where('sct.module_id', $module_id);
        } else {
            $grouped = true;
        }

        $query = $this->db->get('settings_configurable_tables `sct`');

        if ($query->num_rows() > 0) {
            if ($grouped) {
                $data = [];
                foreach ($query->result() as $k => $row) {
                    # $row->table_data 			= $this->get_config_table_data( $account_id, $row->table_name ); //Not rquired for a long list of modules, to reduce on load times
                    $data[$row->module_name][] 	= $row;
                }
                $result = $data;
            } else {
                $result = [];
                foreach ($query->result() as $k => $row) {
                    $row->table_data 	= $this->get_config_table_data($account_id, $row->table_name); //Not rquired for a long list of modules, to reduce on load times
                    $result[] 			= $row;
                }
            }
            $this->session->set_flashdata('message', 'Configurable tables data found');
        } else {
            $this->session->set_flashdata('message', 'There\'s currently no configurable tables setup for your account at the moment.');
        }

        return $result;
    }

    /** Get Table Table Data **/
    public function get_config_table_data($account_id = false, $table_name = false, $options = false, $filters = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($table_name)) {
            $table_columns = $this->get_table_columns(false, $account_id, $account_id, $table_name);

            if (!empty($table_columns)) {
                $this->db->select(array_diff($table_columns, $this->blacklisted_table_columns));

                $options = convert_to_array($options);

                if (!empty($options)) {
                    foreach ($options as $k => $join_table) {
                        $this->db->join($join_table['table_name'], $join_table['table_name'].'.'.$join_table['join_key'].' = '.$table_name.'.'.$join_table['join_key'], 'left');
                    }
                }

                if ($this->db->field_exists('is_active', $table_name)) {
                    $this->db->where($table_name.'.is_active', 1);
                }

                if ($this->db->field_exists('account_id', $table_name)) {
                    $this->db->where($table_name.'.account_id', $account_id);
                }

                if (!empty($filters)) {
                    $filters = convert_to_array($filters);

                    if ($filters['order_by']) {
                        $this->db->order_by($table_name.'.'.$filters['order_by'].' ASC');
                    }
                }

                $query = $this->db->get($table_name);

                if ($query->num_rows() > 0) {
                    $result = $query->result();
                    $this->session->set_flashdata('message', 'Table data found');
                } else {
                    $this->session->set_flashdata('message', 'No data found for this table.');
                }
            } else {
                $this->session->set_flashdata('message', 'Failed to get table columns!');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }

    /** Generate Membership Number **/
    public function generate_membership_number($account_id = false, $user_id = false, $postdata = false)
    {
        $result 	= false;
        $postdata 	= (!is_array($postdata)) ? convert_to_array($postdata) : $postdata;
        $data = false;

        if ($user_id || isset($postdata['user_id'])) {
            $user_id 			= (!empty($user_id)) ? $user_id : $postdata['user_id'];
            $membership_number 	= $this->ssid_common->random_str(5, '0123456789');
            $check_exists 		= $this->db->get_where('user', ['membership_number'=>$membership_number])->row();
            if (!$check_exists) {
                $data['user']['membership_number']  = strtoupper($membership_number);
                $data['user']['membership_pin'] 	= $this->ssid_common->random_str(4, '0123456789');
            } else {
                $data = generate_membership_number($account_id, $user_id, $postdata);
            }

            ## if User exists
            if (!empty($user_id) && !empty($data['user'])) {
                $user_acc_id = (!empty($account_id)) ? $account_id : (!empty($this->ion_auth->_current_user()->account_id) ? $this->ion_auth->_current_user()->account_id : null);
                $this->db->where('id', $user_id)
                    ->where('account_id', $user_acc_id)
                    ->update('user', $data['user']);
                ## Do this after the update
                $data['user']['user_id'] = $user_id;
            }
        }

        if ($account_id || isset($postdata['account_id'])) {
            $account_id 				= (!empty($account_id)) ? $account_id : $postdata['account_id'];
            $account_membership_number 	= $this->ssid_common->random_str(10, '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
            $check_exists 		= $this->db->get_where('account', ['account_membership_number'=>$account_membership_number])->row();
            if (!$check_exists) {
                $data['account']['account_membership_number']  	= strtoupper($account_membership_number);
                $data['account']['account_membership_pin'] 		= $this->ssid_common->random_str(6, '0123456789');
            } else {
                $result = generate_membership_number($account_id, $user_id, $postdata);
            }

            ## if Account ID exists
            if (!empty($account_id) && !empty($data['account'])) {
                $data['account']['account_id'] = $account_id;
                $this->db->where('account_id', $account_id)
                    ->where('( account_membership_number ="" OR account_membership_number IS NULL )')
                    ->update('account', $data['account']);

                if ($this->db->affected_rows() > 0 && ($this->db->trans_status() !== false)) {
                } else {
                    unset($data['account']);
                    $this->session->set_flashdata('message', 'Account membership number(s) already exists for this account');
                }
            }
        }

        if (!empty($data)) {
            $result = array_to_object($data);
            $this->session->set_flashdata('message', 'Account membership number(s) generated successfully');
        } else {
            $this->session->set_flashdata('message', 'Invalid request parameters or the membership number(s) for this account already exists');
        }

        return $result;
    }

    /*
    * Search through Accounts
    */
    public function account_lookup($account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            $where = $raw_where = (!empty($where)) ? convert_to_array($where) : false;

            $this->db->select('account.*', false);

            if (isset($where['active_only'])) {
                if (!empty($where['active_only'])) {
                    $this->db->where('account.is_active', 1);
                    unset($where['active_only']);
                }
            }

            if (!empty($where['account_id'])) {
                $row = $this->db->get_where('account', ['account_id'=>$where['account_id']])->row();
                if (!empty($row)) {
                    $result 		 = ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                    $result->records = $row;
                    return $result;
                }
                return false;
            }

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->account_searchable_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }
                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->account_searchable_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }
                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if ($order_by) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('account.account_name');
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->get('account');

            if ($query->num_rows() > 0) {
                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $result->records 			= $query->result();
                $counters 					= $this->get_total_accounts($account_id, $search_term, $raw_where, $limit);
                $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
                $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
                $result->counters->limit  	= $limit;
                $result->counters->offset 	= $offset;
                $this->session->set_flashdata('message', 'Records found.');
            } else {
                $this->session->set_flashdata('message', 'No records found matching your criteria.');
            }
        }

        return $result;
    }

    /*
    * Get total account count for the search
    */
    public function get_total_accounts($account_id = false, $search_term = false, $where = false, $limit = false)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select('account.account_id', false);

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->account_searchable_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }
                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->account_searchable_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }
                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            $where = $raw_where = (!empty($where)) ? convert_to_array($where) : false;

            if (isset($where['active_only'])) {
                if (!empty($where['active_only'])) {
                    $this->db->where('account.is_active', 1);
                    unset($where['active_only']);
                }
            }

            if (!empty($where)) {
                $this->db->where($where);
            }

            $query = $this->db->group_by('account.account_id')
                ->get('account');

            $results['total'] = !empty($query->num_rows()) ? $query->num_rows() : 0;
            $limit 			  = ($limit > 0) ? $limit : $results['total'];
            $results['pages'] = !empty($query->num_rows()) ? ceil($query->num_rows() / $limit) : 0;
            return json_decode(json_encode($results));
        }

        return $result;
    }

    /** Verify Account Email address uniqueness **/
    public function check_unique_accoount_email($account_id = false, $account_email = false)
    {
        $this->db->where('account_email', $account_email);
        if ($account_id) {
            $this->db->where_not_in('account_id', $account_id);
        }
        return $this->db->get('account')->num_rows();
    }

    public function get_system_modules($user_id = false, $account_id = false, $admin_account_id = false, $module_id = false)
    {
        $result = false;

        if ($this->ion_auth->_current_user->id == $user_id || true) {
            $this->db->select('*');

            if ($module_id) {
                $this->db->where('module_id', $module_id);
            }

            $query = $this->db->get('user_modules');

            if ($query->num_rows() > 0) {
                if (!$module_id) {
                    $this->session->set_flashdata('message', 'System modules found!');
                    $result = $query->result_array();
                } else {
                    $this->session->set_flashdata('message', 'System module found!');
                    $result = $query->row();
                }
            } else {
                $this->session->set_flashdata('message', 'No System modules found!');
            }
        } else {
            $this->session->set_flashdata('message', 'No admin account supplied!');
        }

        return $result;
    }

    public function update_base_module($user_id, $account_id, $admin_account_id, $module_id = false, $module_data = false)
    {
        $result = false;

        if ($this->ion_auth->_current_user->id == $user_id || true) {
            if (!empty($module_id) && !empty($module_data)) {
                $module_data_json = convert_to_array($module_data);
                if (!empty($module_data_json)) {
                    $this->db->where('module_id', $module_id);

                    $updated_status = $this->db->update('user_modules', $module_data_json);

                    if (!empty($updated_status)) {
                        $this->session->set_flashdata('message', 'Updated successfully!');

                        $this->db->where('module_id', $module_id);
                        $updated_row_query = $this->db->get('user_modules');

                        if ($updated_row_query) {
                            return $updated_row_query->row();
                        }
                    }
                } else {
                    $this->session->set_flashdata('message', 'Invalid module data supplied!');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No admin account supplied!');
        }
        return $result;
    }

    public function fetch_all_tables($user_id, $account_id, $admin_account_id)
    {
        $result = false;

        if ($this->ion_auth->_current_user->id == $user_id || true) {
            $all_tables = $this->db->list_tables();
            if (!empty($all_tables)) {
                $this->session->set_flashdata('message', 'Table information found!');
                $result = array_intersect($all_tables, $this->whitelisted_config_tables);
            } else {
                $this->session->set_flashdata('message', 'Table information not found!');
            }
        } else {
            $this->session->set_flashdata('message', 'No admin account supplied!');
        }
        return $result;
    }

    public function get_table_columns($user_id, $account_id, $admin_account_id, $table_name = false)
    {
        $result = false;

        if ($this->ion_auth->_current_user->id == $user_id) {
            if (in_array($table_name, $this->whitelisted_config_tables)) {
                if ($this->db->table_exists($table_name)) {
                    $table_columns = $this->db->list_fields($table_name);
                    if (!empty($table_columns)) {
                        $this->session->set_flashdata('message', 'Table information found!');
                        $result = array_diff($table_columns, $this->blacklisted_table_columns);
                    } else {
                        $this->session->set_flashdata('message', 'Table information not found!');
                    }
                } else {
                    $this->session->set_flashdata('message', 'The table you specified does not exist!');
                }
            } else {
                $this->session->set_flashdata('message', 'Table information not found!');
            }
        } else {
            $this->session->set_flashdata('message', 'No admin account supplied!');
        }

        return $result;
    }

    public function update_config_table($user_id, $account_id, $admin_account_id, $config_data)
    {
        $result = false;

        if ($this->ion_auth->_current_user->id == $user_id) {
            /* check this entry doesn't already exist */
            $this->db->select('id');
            $this->db->where('table_name', $config_data->table_name);
            $this->db->where('module_id', $config_data->module_id);

            $query = $this->db->get('settings_configurable_tables');

            if (!($query->num_rows() > 0)) {
                if (!empty($config_data)) {
                    $this->db->insert('settings_configurable_tables', $config_data);
                    $this->session->set_flashdata('message', 'Update config successfully!');
                    $result = true;
                } else {
                    $this->session->set_flashdata('message', 'No config data was sent!');
                }
            } else {
                $this->session->set_flashdata('message', 'This entry already exists!');
            }
        }
        return $result;
    }

    public function delete_config_entry($user_id, $account_id, $admin_account_id, $config_entry_id)
    {
        $result = false;

        if ($this->ion_auth->_current_user->id == $user_id) {
            $this->db->where('id', $config_entry_id);
            $this->db->delete('settings_configurable_tables');

            if ($this->db->affected_rows() > 0) {
                $result = true;
                $this->session->set_flashdata('message', 'Successfully deleted config entry!');
            } else {
                $this->session->set_flashdata('message', 'Failed to deleted config entry!');
            }
        }

        return $result;
    }


    /**
    * Get account Configs
    **/
    public function get_account_configs($account_id = false, $module_id = false, $where = false)
    {
        $result = false;

        if (!empty($account_id)) {
            $where = convert_to_array($where);
            if (!empty($where['un_grouped'])) {
                $un_grouped = true;
                unset($where['un_grouped']);
            }

            $this->db->select('ac.id, ac.account_id, ac.module_id, ac.config_item, ac.config_type, ac.config_value', false);

            if (!empty($module_id)) {
                $this->db->where('ac.module_id', $module_id)
                    ->order_by('ac.module_id');
            }

            $query = $this->db->where('ac.account_id', $account_id)
                ->order_by('ac.config_item')
                ->get('account_configs `ac`');

            if ($query->num_rows() > 0) {
                if (!empty($un_grouped)) {
                    $result = $query->result();
                } else {
                    $data = [];
                    foreach ($query->result() as $k => $row) {
                        $data[$row->config_item] = $row->config_value;
                    }
                    $result = $data;
                }
                $this->session->set_flashdata('message', 'Account configs data retrieved successfully');
            } else {
                $this->session->set_flashdata('message', 'No account configs found!');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }


    public function get_client_list($access_token = false)
    {
        $result = null;

        if (!empty($access_token) && (strtolower($access_token) == strtolower(CLIENT_ACCESS_TOKEN))) {
            $query  = $this->db->select('account_id, account_reference, account_name, account_membership_number, account_membership_pin, is_active', false)
                ->order_by('account_name')
                ->get('account');

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Data retrieved successfully!');
            } else {
                $this->session->set_flashdata('message', 'No data found!');
            }
        } else {
            $this->session->set_flashdata('message', 'Error: Unauthorized access!');
        }

        return $result;
    }


    /**
    * Get Account Config Items
    **/
    public function get_account_config_items($account_id = false)
    {
        $result = false;

        if (!empty($account_id)) {
            $query = $this->db->select('aci.*', false)
                ->order_by('aci.config_item')
                ->get('account_config_items `aci`');

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Account config items retrieved successfully');
            } else {
                $this->session->set_flashdata('message', 'No data found!');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }

    /**
     * @param $account_id
     *
     * @return array|array[]|false
     * @throws \Exception
     */
    public function get_account_disciplines_with_contacts($account_id = null)
    {
        if (false === $account = $this->get_accounts($account_id)) {
            throw new Exception(sprintf('Cannot determine account identifier %s', $account_id));
        }

        $columns = [
            'account_discipline.account_discipline_name',

            'account_discipline_contact.email',
            'account_discipline_contact.number',

            'user.email',
            'user.phone',
            'user.mobile_number',
        ];

        $queryString = implode(', ', $columns);

        $query = $this->db->select($queryString, false)
            ->join('account_discipline_contact', 'account_discipline.account_id = account_discipline_contact.account_id', 'left')
            ->join('user', 'user.id = account_discipline_contact.user_id', 'left')
            ->where('account_discipline.account_id', $account_id)
            ->where('account_discipline.is_active', 1)
            ->get('account_discipline');

        if ($query->num_rows() < 1) {
            return false;
        }

        return $query->result_array();
    }
}
