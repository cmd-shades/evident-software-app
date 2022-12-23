<?php

namespace Application\Service\Models;

defined('BASEPATH') || exit('No direct script access allowed');

use System\Core\CI_Model;

class Modules_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /*
    * Create a user's access right to modules
    */
    public function create_user_module_access($user = false, $modules = array())
    {
        $result = false;
        if ($user && $modules) {
            $modules                = ( is_object($modules) ) ? object_to_array($modules) : $modules;

            $module_access          = [];
            //Check user's current module access rights before update
            $current_module_access  = $this->get_module_access($user->account_id, $user->id);
            $current_module_access  = ( !empty($current_module_access) ) ? object_to_array($current_module_access) : null;
            $modules                = ( array_column($modules, 'module_id') ) ? array_column($modules, 'module_id') : $modules;

            if (!empty($current_module_access)) {
                //Prevent overriding main account holders permissions
                if (( $user->id == $this->ion_auth->_current_user()->id ) || ( $user->is_account_holder != 1 )) {
                    $existing_perms         = array_column($current_module_access, 'module_id');
                    $dropped_access_rights  = ( !empty($existing_perms) ) ? array_diff($existing_perms, $modules) : [];
                } else {
                    $warning = 'Warning! You can not update the account holder\'s module access rights.';
                }
            }

            //Drop any permissions that have been removed
            if (isset($dropped_access_rights) && !empty($dropped_access_rights)) {
                $this->db->where('user_id', $user->id)
                    ->where_in('module_id', $dropped_access_rights)
                    ->delete('user_module_access');
            }

            foreach ($modules as $k => $module) {
                $conditions = ['user_id' => $user->id,'module_id' => $module];

                $module_permissions = [
                    'user_id'   => $user->id,
                    'module_id' => $module,
                    'account_id' => $user->account_id,
                    'has_access' => 1
                ];

                if ($this->ion_auth->is_admin($user->id)) {
                    $module_permissions['is_module_admin'] = 1;
                }

                $query = $this->db->get_where('user_module_access', $conditions)->row();
                if (!empty($query)) {
                    $this->db->where($conditions);
                    $this->db->update('user_module_access', $module_permissions);
                    $this->session->set_flashdata('message', 'User permissions updated successfully');
                } else {
                    $this->db->insert('user_module_access', $module_permissions);
                    $this->session->set_flashdata('message', 'User permissions created successfully');
                }

                //Set module-items access permissions
                $module_items = $this->module_service->get_module_items(false, $module);

                if (!empty($module_items)) {
                    foreach ($module_items as $k => $module_item) {
                        $mod_item_perms = [
                            'module_item_id' => $module_item->module_item_id,
                            'item_permissions' => json_encode([
                                $module_item->module_item_id . '_add',
                                $module_item->module_item_id . '_view',
                                $module_item->module_item_id . '_edit',
                                $module_item->module_item_id . '_delete',
                                $module_item->module_item_id . '_admin'
                            ])
                        ];
                        $this->module_service->set_module_item_permissions($user, $module, $mod_item_perms);
                    }
                }

                if (isset($warning) && !empty($warning)) {
                    $this->session->set_flashdata('warning', $warning);
                }
            }

            $result = true;
        }
        return $result;
    }

    /**
    * Get users module access
    **/
    public function get_module_access($account_id = false, $user_id = false, $module_id = false, $app_uuid = false)
    {
        $result = false;
        if ($account_id && $user_id) {
            $this->db->select('uma.user_id `user_id`, uma.module_id, uma.has_access, uma.is_module_admin, um.module_name, um.module_ranking, mc.category_id, mc.category_name')
            ->join('user_module_categories mc', 'mc.category_id = um.category_id', 'left')
            ->join('user_module_access uma', 'uma.module_id = um.module_id')
            ->where("uma.user_id", $user_id)
            ->where("uma.account_id", $account_id)
            ->where("um.is_active", 1)
            ->where("mc.is_active", 1)
            ->where("uma.has_access", 1)
            ->order_by("module_ranking", "asc");

            if ($module_id) {
                $this->db->where('uma.module_id', $module_id);
            }

            if (!empty($app_uuid)) {
                $this->db->where('um.app_uuid', $app_uuid);
            }

            $query = $this->db->get('user_modules um');

            if ($query->num_rows() > 0) {
                $result = $query->result();
            }
        }
        return $result;
    }

    /**
    * Create Update user-module-item-permissions
    **/
    public function set_module_item_permissions($user = false, $module_id = false, $item_permissions = array())
    {
        $result = false;
        $user   = ( is_object($user) ) ? object_to_array($user) : $user;
        if (!empty($user['id']) && !empty($module_id)) {
            //Drop any permissions that have been removed
            $this->drop_item_permissions($user['account_id'], $user['id'], $module_id, $item_permissions['module_item_id']);

            $insert_data = array_merge(['user_id' => $user['id'],'account_id' => $user['account_id'],'module_id' => $module_id], $item_permissions);
            if (!empty($insert_data)) {
                $this->db->insert('user_module_item_permissions', $insert_data);
            }
            $result = ( $this->db->trans_status() !== false ) ? true : false;
        }
        return $result;
    }

    /** Drop any previously set item permissions **/
    private function drop_item_permissions($account_id = false, $user_id = false, $module_id = false, $module_item_id = false)
    {
        $result = false;
        if ($account_id && $user_id && $module_id) {
            $this->db->where('user_id', $user_id)
                ->where('account_id', $account_id)
                ->where('module_id', $module_id);
            if ($module_item_id) {
                $this->db->where('module_item_id', $module_item_id);
            }
                $query = $this->db->delete('user_module_item_permissions');
            if ($this->db->trans_status() !== false) {
                $result = true;
                $this->ssid_common->_reset_auto_increment('user_module_item_permissions', 'id');
            }
        }

        return $result;
    }

    /**
    * Get a user's access permissions to a module item
    **/
    public function get_module_item_permissions($account_id = false, $user_id = false, $module_id = false, $module_item_id = false, $module_item = false, $as_list = false)
    {
        $result = false;
        if ($account_id && $user_id) {
            if ($module_item) {
                $module_item_record = $this->get_module_items(false, $module_id, $module_item);
                $mod_item_id        = ( !empty($module_item_record) ) ? $module_item_record[0] : false;
                $mod_id             = ( !empty($mod_item_id) ) ? $mod_item_id->module_item_id : false;
                $this->db->where('mp.module_item_id', $mod_id);
                $this->db->group_by('mp.module_item_id');
            }

            $this->db->select('mp.*,mi.module_item_name,m.module_name')
                ->join('user_module_access ma', 'ma.module_id = mp.module_id')
                ->join('user_modules m', 'm.module_id = ma.module_id')
                ->join('user_module_items mi', 'mi.module_item_id = mp.module_item_id')
                ->where('mp.user_id', $user_id)
                ->where('ma.account_id', $account_id)
                ->where('m.is_active', 1)
                ->where('ma.has_access', 1);

            if ($module_id) {
                $this->db->where('mp.module_id', $module_id);
            }

            if ($module_item_id) {
                $this->db->where('mp.module_item_id', $module_item_id);
            }

                $query = $this->db->get('user_module_item_permissions mp');

            if ($query->num_rows() > 0) {
                if ($module_item_id || $module_item) {
                    $row    = $query->result_array();

                    if (!empty($row[0])) {
                        if (!empty($row[0]['is_admin']) || !empty($row[0]['can_add']) || !empty($row[0]['can_edit']) || !empty($row[0]['can_view'])) {
                            $result = (array)$row;
                            $this->session->set_flashdata('message', 'Access Granted. You have access to this module item');
                        }
                    }
                } else {
                    $data = [];
                    if ($as_list) {
                        foreach ($query->result() as $k => $row) {
                            $data[$row->module_id][$row->module_item_id] = (array)$row;
                        }
                    } else {
                        foreach ($query->result() as $k => $row) {
                            $data[$k] = (array)$row;
                        }
                    }
                    $result = $data;
                    $this->session->set_flashdata('message', 'Access Granted. You have access to these modules items');
                }
            } else {
                if ($module_item_id) {
                    $this->session->set_flashdata('message', 'Access Denied. You do not currently have permission for this module item');
                } else {
                    $this->session->set_flashdata('message', 'You do not currently have permissions for this module item');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'Access Denied. Missing required data');
        }

        return $result;
    }

    /**
    * Get module items
    **/
    public function get_module_items($item_id = false, $module_id = false, $module_item_name = false)
    {
        $result = false;
        $this->db->select('mod_item.*, m.module_name')
        ->join('user_modules m', 'm.module_id = mod_item.module_id', 'left')
        ->where('mod_item.is_active', 1)
        ->where('m.is_active', 1)
        ->order_by('mod_item.module_item_name', 'asc');

        if ($item_id) {
            $this->db->where('mod_item.module_item_id', $item_id);
        }

        if ($module_id) {
            $this->db->where('mod_item.module_id', $module_id);
        }

        if ($module_item_name) {
            $this->db->where('mod_item.module_item_tab', $module_item_name);
        }

        $query = $this->db->get('user_module_items mod_item');

        if ($query->num_rows() > 0) {
            $result = $query->result();
        }

        return $result;
    }

    /*
    * Check what modules a user is allowed access to
    */
    public function get_allowed_modules($account_id = false, $user_id = false, $module_id = false, $app_uuid = false, $as_list = false)
    {
        $result = false;
        if ($account_id && $user_id) {
            $this->db->select('uma.user_id `user_id`, uma.module_id, uma.has_access,mc.category_name, um.*')
            ->join('user_module_categories mc', 'mc.category_id = um.category_id', 'left')
            ->join('user_module_access uma', 'uma.module_id = um.module_id')
            ->where("uma.user_id", $user_id)
            ->where("uma.account_id", $account_id)
            ->where("um.is_active", 1)
            ->where("mc.is_active", 1)
            ->where('uma.has_access', 1)
            ->order_by("module_ranking", "asc");

            if ($module_id) {
                $this->db->where('uma.module_id', $module_id);
            }

            if ($app_uuid) {
                $this->db->where('um.app_uuid', $app_uuid);
            }

            $query = $this->db->get('user_modules um');

            if ($query->num_rows() > 0) {
                if ($module_id) {
                    $this->session->set_flashdata('message', 'Access Granted. You have access to this module / app');
                    $result = $query->result_array();
                } else {
                    $this->session->set_flashdata('message', 'Access Granted. You have access to these modules/ apps');
                    if ($as_list) {
                        foreach ($query->result() as $module_details) {
                            $result[] = $module_details->module_id;
                        }
                    } else {
                        $result = $query->result_array();
                    }
                }
            } else {
                if ($module_id) {
                    $this->session->set_flashdata('message', 'Access Denied. You do not currently have permission to this module');
                } else {
                    $this->session->set_flashdata('message', 'You do not currently have access to any modules / application');
                }
            }
        }
        return $result;
    }


    /*
    * Get list of available modules / applications
    */
    public function get_modules($module_id = false, $active_only = false)
    {
        $result = [];
        if ($module_id) {
            $this->db->where('module_id', $module_id);
        }
        if ($active_only) {
            $this->db->where("um.is_active", 1);
        }

        $query = $this->db->select('um.*, mc.category_id, mc.category_name')
            ->join('user_module_categories mc', 'mc.category_id = um.category_id')
            ->order_by("um.module_ranking, category_name, um.module_name,module_id")
            ->get('user_modules um');

        $this->session->set_flashdata('message', 'Module record(s) not found');
        if ($query->num_rows() > 0) {
            $result = ( $module_id ) ? $query->result()[0] : $query->result();
            $this->session->set_flashdata('message', 'Module record(s) found');
        }
        return $result;
    }

    public function get_module_categories($category_id = false)
    {
        $result = [];
        if ($category_id) {
            $this->db->where('category_id', $category_id);
        }
        $this->db->order_by("category_name");
        $query = $this->db->get('user_module_categories');
        if ($query->num_rows() > 0) {
            $result = ( $category_id ) ? $query->result()[0] : $query->result();
        }
        return $result;
    }

    public function add_module()
    {
        $result = false;
        $postdata = $this->input->post();
        if (!empty($postdata)) {
            foreach ($postdata as $col => $value) {
                if ($col != 'request_source') {
                    $result[$col] = $value;
                }
            }
            if (!empty($result)) {
                $this->db->insert('user_modules', $result);
                if ($this->db->trans_status() !== false) {
                    $result['module_id'] = $this->db->insert_id();
                }
            }
        }
        return $result;
    }

    public function update_module($module_id = false)
    {
        $result = false;
        $postdata = $this->input->post();
        if (!empty($module_id) && !empty($postdata)) {
            foreach ($postdata as $col => $value) {
                if ($col != 'request_source') {
                    $result[$col] = $value;
                }
            }
            if (!empty($result)) {
                $this->db->where('module_id', $module_id)
                    ->update('user_modules', $result);
                    $result = ( $this->db->trans_status() !== false ) ? $result : true;
            }
        }
        return $result;
    }

    public function delete_module($module_id = false)
    {
        if (!empty($module_id)) {
            #Delete all attached items
            $this->db->where('module_id', $module_id)->delete('user_modules_items');

            $this->db->where('module_id', $module_id)->delete('user_modules');
            return true;
        }
        return $module_id;
    }

    public function add_module_category()
    {
        $result = false;
        $postdata = $this->input->post();
        if (!empty($postdata)) {
            foreach ($postdata as $col => $value) {
                if ($col != 'request_source') {
                    $result[$col] = $value;
                }
            }
            if (!empty($result)) {
                $this->db->insert('user_module_categories', $result);
                if ($this->db->trans_status() !== false) {
                    $result->category_id = $this->db->insert_id;
                }
            }
        }
        return $result;
    }

    public function delete_module_category($category_id = false)
    {
        if (!empty($category_id)) {
            #Delete all attached items
            $this->db->where('category_id', $category_id)->update('user_modules', ['category_id' => null]);

            $this->db->where('category_id', $category_id)->delete('user_module_categories');
            return true;
        }
        return $category_id;
    }

    public function update_module_category($category_id = false)
    {
        $result = false;
        $postdata = $this->input->post();
        if (!empty($category_id) && !empty($postdata)) {
            foreach ($postdata as $col => $value) {
                if ($col != 'request_source') {
                    $result[$col] = $value;
                }
            }
            if (!empty($result)) {
                $this->db->where('category_id', $category_id)
                    ->update('user_module_categories', $result);
                    $result = ( $this->db->trans_status() !== false ) ? $result : true;
            }
        }
        return $result;
    }

    public function add_module_item()
    {
        $result = false;
        $postdata = $this->input->post();
        if (!empty($postdata)) {
            foreach ($postdata as $col => $value) {
                if ($col != 'request_source') {
                    $result[$col] = $value;
                }
            }
            if (!empty($result)) {
                $this->db->insert('user_modules_items', $result);
                if ($this->db->trans_status() !== false) {
                    $result['item_id'] = $this->db->insert_id;
                }
            }
        }
        return $result;
    }

    public function update_module_item($item_id = false)
    {
        $result = false;
        $postdata = $this->input->post();
        if (!empty($item_id) && !empty($postdata)) {
            foreach ($postdata as $col => $value) {
                if ($col != 'request_source') {
                    $result[$col] = $value;
                }
            }
            if (!empty($result)) {
                $this->db->where('item_id', $item_id)
                    ->update('user_modules_items', $result);
                    $result = ( $this->db->trans_status() !== false ) ? $result : true;
            }
        }
        return $result;
    }

    public function delete_module_item($item_id = alse)
    {
        if ($item_id) {
            $this->db->where('item_id', $item_id)->delete('user_modules_items');
            return ( $this->db->trans_status() !== false ) ? true : false;
        }
        return false;
    }

    /** Set / update user permissions **/
    public function set_permissions($account_id = false, $user_id = false, $data = false)
    {
        $result = false;
        $message = $error_message = '';
        if (!empty($account_id) && !empty($user_id)) {
            if (!empty($data['permissions'])) {
                $data['permissions'] = ( !is_array($data['permissions']) ) ? object_to_array(json_decode($data['permissions'], true)) : $data['permissions'];

                foreach ($data['permissions'] as $module_id => $mod_permissions) {
                    if (!empty($mod_permissions['module_items'])) {
                        //Access given to atleast 1 item/tab
                        $grant_access = $this->update_module_access($account_id, $user_id, ['module_id' => $module_id, 'has_access' => 1 ]);
                        if ($grant_access) {
                            $message = 'User permissions updated successfully';
                            foreach ($mod_permissions['module_items'] as $mod_item_id => $perms) {
                                $item_actions = [];
                                foreach ($perms as $k => $action) {
                                    $item_actions[] = $mod_item_id . '_' . $action;
                                }

                                ## compile specific item permissions
                                if (!empty($item_actions)) {
                                    $item_permissions = [
                                        'module_item_id' => $mod_item_id,
                                        'item_permissions' => json_encode($item_actions)
                                    ];

                                    ## Now set the item permissions
                                    $set_permissions = $this->set_module_item_permissions(['id' => $user_id,'account_id' => $account_id], $module_id, $item_permissions);
                                    if ($set_permissions) {
                                        $message = 'User permissions updated successfully';
                                        $result  = true;
                                    } else {
                                        $error_message = 'Permission update request failed';
                                    }
                                }
                            }
                            $result = true;
                        } else {
                            $this->session->set_flashdata('message', 'Permission update request failed');
                        }
                    } elseif ($mod_permissions['full_access'] == 'no' || $mod_permissions['full_access'] == 0 || empty($mod_permissions['module_items'])) {
                        $revoke_access = $this->update_module_access($account_id, $user_id, ['module_id' => $module_id, 'has_access' => 0 ]);
                        if ($revoke_access) {
                            ##Drop any previously set permissions when the access to the module is revoked
                            $drop   = $this->drop_item_permissions($account_id, $user_id, $module_id);
                            $message = 'Module access updated successfully';
                            $result = true;
                        } else {
                            $error_message = 'Module access update request failed';
                        }
                    } elseif ($mod_permissions['full_access'] == 'yes' || $mod_permissions['full_access'] == 1) {
                        $grant_access = $this->update_module_access($account_id, $user_id, ['module_id' => $module_id, 'has_access' => 1 ]);
                        if ($grant_access) {
                            $message = 'Module access updated successfully';
                            $result = true;
                        } else {
                            $error_message = 'Module access update request failed';
                        }
                    }
                }

                if ($result) {
                    $this->session->set_flashdata('message', $message);
                } else {
                    $this->session->set_flashdata('message', $error_message);
                }
            }
        }
        return $result;
    }

    /** Update user module access **/
    public function update_module_access($account_id = false, $user_id = false, $module_access = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($user_id) && !empty($module_access)) {
            $where = ['account_id' => $account_id,  'user_id' => $user_id, 'module_id' => $module_access['module_id']];

            $check_exists = $this->db->get_where('user_module_access', $where)->row();

            if (!empty($check_exists)) {
                $this->db->where($where)
                ->update('user_module_access', $module_access);
            } else {
                $module_access = array_merge($where, $module_access);
                $this->db->insert('user_module_access', $module_access);
            }

            $result = ( $this->db->trans_status() !== false ) ? true : false;
        }
        return $result;
    }
}
