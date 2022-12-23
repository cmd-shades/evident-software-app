<?php

namespace Modules\Services\Models;

use Firebase\JWT\JWT;
use System\Core\CI_Model;

class Account_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->lang->load('auth');
    }

    /*
    * Get Accounts single records or multiple records
    */
    public function get_accounts($account_id = false, $offset = 0, $limit = 20)
    {
        $result = false;
        $this->db->where('archived !=', 1);
        if ($account_id) {
            $row = $this->db->get_where('account', ['account_id' => $account_id])->row();
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
                unset($account_data['account_modules']);
            }

            //If user admin is not selected, force it in
            if (!in_array(1, $modules)) {
                $modules[] = 1;
            }

            foreach ($account_data as $key => $value) {
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
                $data['is_active']          = 0;
                $data['account_status']     = 'Trial';
                $data['license_type']       = 'Monthly';
                $data['trial_start_date']   = date('Y-m-d H:i:s');
                $data['license_start_date'] = date('Y-m-d H:i:s', strtotime('+ ' . API_ACCOUNT_TRIAL_PERIOD)); //Set account Trial Period
                $data['account_reference']  = 'SSID_' . strtoupper(preg_replace('/\s+/', '', $data['account_name']));

                $account_exists = $this->db->get_where('account', ['account_reference' => $data['account_reference']])->row();
                if (!$account_exists) {
                    $new_account_data = $this->ssid_common->_filter_data('account', $data);
                    $this->db->trans_begin(); //Be ready to rollback if an error occurs with user creation
                    $this->db->insert('account', $new_account_data);
                    if ($this->db->trans_status() !== false) {
                        $data['account_id'] = $this->db->insert_id();
                        if (!empty($data['account_id'])) {
                            $account_uuid = $this->generate_account_uuid($data);

                            $this->db->where('account_id', $data['account_id'])->update('account', ['account_uuid' => $account_uuid]);
                            $result = $this->get_accounts($data['account_id']);

                            ## Create account modules
                            $data['account_id'] = $result->account_id;
                            $account_modules    = $this->create_account_modules($data, $modules);

                            ## Created Admin User account, append username if one was available on sign-up
                            $result->admin_username = $data['admin_username'];
                            $admin_user = $this->create_admin_user_account($result);

                            if (!empty($admin_user)) {
                                $temp_password = $this->session->flashdata('temp_password');

                                $this->db->trans_commit();//We're good to commit this now
                                ## Create Module
                                if (!empty($admin_user) && isset($account_modules) && !empty($account_modules)) {
                                    $this->module_service->create_user_module_access($admin_user, object_to_array($account_modules));
                                    $login_details = ( !empty($admin_user->username) ) ? ' You username is ' . $admin_user->username : null;

                                    //Send an account activation email
                                    $result->temp_password  = ( !empty($temp_password) ) ? $temp_password : 'd3v3l0pm3nt';
                                    $this->send_account_activation_email($result);
                                }
                                $this->session->set_flashdata('message', 'Main account record created successfully.' . ( !empty($login_details) ? $login_details : '' ));
                            } else {
                                $this->db->trans_rollback();//Rollback the account creation transaction
                                $this->ssid_common->_reset_auto_increment('account', 'account_id');

                                $message = $this->session->flashdata('message');
                                $this->session->set_flashdata('message', 'Main account creation failed (User fields). ' . $message);
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
            foreach ($account_data as $key => $value) {
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
                $preserve_account_name = $data['account_name'];
                unset($data['account_name']); //You can't update Account name using this method.
                #$data['account_reference'] = 'SSID_'.strtoupper( preg_replace('/\s+/', '',$data['account_name']) );

                $this->db->where('account_id', $account_id)->update('account', $data);
                if ($this->db->trans_status() !== false) {
                    $data['account_name'] = $preserve_account_name;
                    $result = $this->get_accounts($account_id);
                    $this->session->set_flashdata('message', 'Account record updated successfully.');
                } else {
                    $this->session->set_flashdata('message', 'There was an Error while trying to upate the Account record.');
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
    public function delete_account($account_id = false)
    {
        $result = false;
        if ($account_id) {
            $data = ['archived' => 1];
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

    public function create_admin_user_account($account_data = false)
    {
        $result         = false;
        $account_data   = ( !empty($account_data) ) ? $account_data : null;
        if (!empty($account_data)) {
            $identity   = $this->config->item('identity', 'ion_auth');
            $email      = $account_data->account_email;
            $first_name = ucwords(strtolower($account_data->account_first_name));
            $last_name  = ucwords(strtolower($account_data->account_last_name));
            $username   = ( !empty($account_data->admin_username) ) ? $account_data->admin_username : ( ( $identity == 'email' ) ? trim(strtolower($account_data->account_email)) : strtolower(trim($first_name) . trim($last_name)) );
            #$password  = 'welcome';
            $password   = $this->ssid_common->generate_random_password();//Generate random password

            ## Validate primary user
            $tables     = $this->config->item('tables', 'ion_auth');
            $validate_data = array(
                'account_id' => $account_data->account_id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $account_data->account_email
            );

            $this->form_validation->set_data($validate_data);

            $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
            $this->form_validation->set_rules('first_name', 'Account User First name', 'required');
            $this->form_validation->set_rules('last_name', 'Account User Last name', 'required');
            $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email|is_unique[' . $tables['user'] . '.email]');

            if ($this->form_validation->run() == false) {
                $validation_errors = ( validation_errors() ) ? validation_errors() : $this->session->flashdata('message');
            }

            if (!$email || !$username || ( isset($validation_errors) && !empty($validation_errors) )) {
                ## One of the required fields is invalid
                $message = [
                    'status' => false,
                    'message' => 'Invalid user account data: ',
                    'user' => null
                ];
                $message['message'] = (!$email)     ? $message['message'] . 'email, '     : $message['message'];
                $message['message'] = (!$username)  ? $message['message'] . 'username, '  : $message['message'];
                $message['message'] = ( isset($validation_errors) && !empty($validation_errors) )   ? 'Validation errors: ' . $validation_errors  : $message['message'];
                $this->session->set_flashdata('message', $message['message']);
                return false;
            }

            $additional_data = array(
                'first_name' => $first_name,
                'last_name'  => $last_name,
                'account_id' => $account_data->account_id,
                'user_type_id' => 1, //Account admin type
                'is_account_holder' => 1 //Main account holder
            );

            $user_id = $this->ion_auth->register($username, $password, $email, $additional_data);

            if (!empty($user_id)) {
                $this->session->set_flashdata('temp_password', $password);
                $new_user = $this->ion_auth->get_user_by_id($account_data->account_id, $user_id);
                $this->ion_auth->add_to_group(array(1), $user_id);
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
            $account = $this->db->get_where('account', ['account_id' => $account_id])->row();
            if (!empty($account)) {
                if (( ( !empty($this->ion_auth->_current_user()) && ( (int)$account->account_id == (int)$this->ion_auth->_current_user()->account_id ) ) ) || $ignore_token) {
                    $status          = $account->account_status;
                    $trial_period    = strtotime(date('Y-m-d H:i:s', strtotime($account->trial_start_date . ' + 1 Month')));
                    $date_time_now   = strtotime(date('Y-m-d H:i:s'));
                    if ($account->is_active != 1) {
                        $this->session->set_flashdata('message', 'The main Business account has not been activated. Please contact your system admin.');
                        $result = false;
                    } elseif (( $status == 'Active' ) || ( $status == 'Trial' && ( $trial_period > $date_time_now ) )) {
                        $result = true;
                    } elseif ($status == 'Trial' && ( $trial_period < $date_time_now )) {
                        $this->session->set_flashdata('message', 'The main Business account trial has expired. Please contact customer support to activate your account.');
                        $result = false;
                    } else {
                        $this->session->set_flashdata('message', 'The main Business account is ' . $status . '. Please contact your system admin.');
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
                        $module_items       = $this->get_account_modules_items($row->module_id);
                        $row->module_items  = ( !empty($module_items) ) ? $module_items : null;
                        if ($categorized) {
                            $result[$row->category_name]['category']  = [
                                'category_name' => $row->category_name,
                                'category_color' => ( !empty($row->category_color) ) ? $row->category_color : null ,
                                'category_icon_class' => $row->category_icon_class
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
                                'category_name' => $row->category_name,
                                'category_color' => ( !empty($row->category_color) ) ? $row->category_color : null ,
                                'category_icon_class' => $row->category_icon_class
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
    public function get_account_modules_items($module_id = false, $detailed = false, $user_id = false)
    {
        $result = false;
        if ($module_id) {
            if ($user_id) {
                $this->db->where('user_id', $user_id);
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
                            'module_id' => $row->module_id,
                            'module_name' => $row->module_name,
                            'is_active' => $row->is_active
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
                $module_details = $this->module_service->get_modules($module_id);
                $data = [
                    'module_id' => $module_id,
                    'account_id' => $account_id,
                    'standard_price' => $module_details->module_price,
                    'adjusted_price' => $module_details->module_price,
                    'license_valid_from' => date('Y-m-d H:i:s'),
                    'license_valid_to' => ( !empty($account_data['license_valid_to']) ) ? $account_data['license_valid_to'] : null,
                    'license_type' => ( !empty($account_data['license_type']) ) ? $account_data['license_type'] : 'Monthly'
                ];

                $conditions = ['account_id' => $account_id,'module_id' => $module_id];
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
            $account_data = ( is_object($account_data) ) ? (array)$account_data : $account_data;
            #$result      = base64_encode( base64_encode($account_data['account_id'].'.'.$account_data['account_reference'].'.'.API_JWT_ALGORITHM) );
            $result       = strtoupper(hash('sha256', $account_data['account_id'] . '.' . $account_data['account_reference'] . '.' . API_JWT_ALGORITHM));
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

            $result = ( $this->db->trans_status() !== false ) ? true : $result;
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
            $account_data->activation_code = $this->ssid_common->_encode_activation_code(['account_id' => $account_data->account_id,'account_reference' => $account_data->account_reference ]);
            $data['account_data'] = $account_data;
            $email_body = $this->load->view('email_templates/account/account_activation', $data, true);
            $destination = $account_data->account_email;

            $email_data = [
                'to' => $destination,
                'from' => ['yourfeedback@lovedigitaltv.co.uk','Simply SID'],
                'subject' => 'Activate your account',
                'message' => $email_body
            ];
            $result = $this->mail->send_mail($email_data);
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
            $account_id = ( !empty($account_data) ) ? $account_data->account_id : null;
        }

        if (!empty($account_id)) {
            $check_exists = $this->db->get_where('account', ['account_id' => $account_id])->row();
            if (!empty($check_exists)) {
                if ($check_exists->is_active != 1) {
                    $this->db->where('account_id', $check_exists->account_id)
                        ->update('account', [ 'is_active' => 1, 'first_activated_on' => date('Y-m-d H:i:s') ]);

                    if ($this->db->trans_status() !== false) {
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
}
