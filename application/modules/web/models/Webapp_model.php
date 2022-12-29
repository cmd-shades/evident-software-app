<?php

namespace Application\Modules\Web\Models;

class Webapp_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }


    /** Get module ID from the controller **/
    public function _get_module_id($module_controller)
    {
        $result 	= false;
        $module_id 	= $this->get_module_id_by_controller($module_controller);
        if (!empty($module_id)) {
            $result = (!is_array($module_id)) ? $module_id : $module_id[$module_controller];
        }
        return $result;
    }

    public function get_module_id_by_controller($controller_name = false)
    {
        $result = [];
        $this->db->select('um.module_id,um.module_controller')
            ->order_by("um.module_id")
            ->where("um.is_active", 1);

        if ($controller_name) {
            $this->db->where('module_controller', $controller_name);
        }

        $query = $this->db->get('user_modules um');

        if ($query->num_rows() > 0) {
            if ($controller_name) {
                $result = $query->result()[0]->module_id;
            } else {
                foreach ($query->result() as $row) {
                    $result[strtolower($row->module_controller)] = $row->module_id;
                }
            }
        }
        return $result;
    }

    /* Dispatch an api request locally */
    public function api_dispatcher($url_endpoint = false, $data = false, $options = false, $is_get_method = false)
    {
        $result = false;
        if (!empty($url_endpoint) && !empty($data)) {
            if ($is_get_method) {
                if ($options) {
                    $options = array_merge($options, ['method'=>'GET']);
                } else {
                    $options = ['method'=>'GET'];
                }
            }
            $postdata = $this->ssid_common->_prepare_curl_post_data($data);
            $result   = $this->ssid_common->doCurl($url_endpoint, $postdata, $options);

            if (!empty($result->message) && ($result->message == 'Expired token')) {
                $this->session->set_flashdata('message', 'Your token has expired. Please login.');
                redirect('webapp/user/login', 'refresh');
            }
        }
        return $result;
    }

    /** Decode an activation string **/
    public function decode_activation_string($decode_string)
    {
        $result = false;
        if (!empty($decode_string)) {
            $account_data = $this->ssid_common->_decode_activation_code($decode_string);
        }
        $result = (!empty($account_data)) ? $account_data : $result;
        return $result;
    }

    /* Check access to module or item */
    public function check_access($user = false, $module_id = false, $module_item = false)
    {
        $result = false;
        if (!empty($module_id) && !empty($user)) {
            $params 	= ['account_id'=>$user->account_id, 'user_id'=>$user->id,'module_id'=>$module_id];
            $pointer    = 'access/check_module_access';

            if ($module_item) {
                $params['module_item']  = $module_item;
                $pointer				= 'access/module_item_permissions';
            }

            $permission = $this->api_dispatcher(api_end_point().$pointer, $params, ['auth_token'=>$user->auth_token]);

            if (!empty($permission->mod_item_access)) {
                $module_access = (is_object($permission->mod_item_access)) ? object_to_array($permission->mod_item_access) : $permission->mod_item_access;
                if (!empty($module_access)) {
                    $result = (!empty($module_access[0])) ? $module_access[0] : false;

                    if ($result) {
                        $permissions = (object)[];
                        foreach ($module_access as $mod => $tab) {
                            $permissions->{$tab->module_item_tab} = $tab;
                        }
                        $result->tab_permissions = $permissions;
                    }
                }
            }

            if (!empty($permission->module_access)) {
                $result = (!empty($permission->module_access[0])) ? $permission->module_access[0] : false;
            }
        }

        return $result;
    }

    public function generateUUID($length)
    {
        $random = '';
        for ($i = 0; $i < $length; $i++) {
            $random .= rand(0, 1) ? rand(0, 9) : chr(rand(ord('a'), ord('z')));
        }
        return $random;
    }

    public function save_quickbar_modules($user = false, $quickbar_modules = false)
    {
        $result = false;

        if ($user && $quickbar_modules) {
            $data = array();

            foreach ($quickbar_modules as $module_index => $module_id) {
                array_push($data, array('account_id' => $user->account_id, 'module_id' => $module_id , 'module_ordering' => $module_index + 1));
            }

            $existing_records = $this->db->get_where('user_module_quickbar', array('account_id' => $user->account_id));

            if ($existing_records->num_rows() > 0) {
                $this->db->where('account_id', $user->account_id);
                $this->db->delete('user_module_quickbar');
                $this->ssid_common->_reset_auto_increment('user_module_quickbar', 'id');
            }

            $this->db->insert_batch('user_module_quickbar', $data);

            $result = ($this->db->trans_status() !== false) ? true : false;
            if ($result) {
                $this->session->set_flashdata('message', 'Success');
            } else {
                $this->session->set_flashdata('message', 'Failed');
            }
        }
        return $result;
    }

    public function get_quickbar_modules($account_id = false)
    {
        $result = false;
        if ($account_id) {
            ## $this->session->set_flashdata('message', 'Found quickbar module data!');

            $this->db->where('account_id', $account_id);

            $query = $this->db->select('module_id')->get('user_module_quickbar');

            if ($query->num_rows() > 0) {
                $result = ($this->db->trans_status() !== false) ? $query->result() : false;
            } else {
                $result = false;
            }
        } else {
            $this->session->set_flashdata('message', 'No account id was supplied!');
        }
        return $result;
    }


    public function get_audit_from_uuid($audit_uuid = false)
    {
        if ($audit_uuid) {
            $row = $this->db->select('audit_id, secure_uuid_download')
                ->where('secure_uuid_download', $audit_uuid)
                ->limit(1)
                ->get('audit')
                ->row();

            if (!empty($row)) {
                return $row->audit_id;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function update_audit_uuid($audit_id)
    {
        $result = false;

        if (!empty($audit_id)) {
            $query = $this->db->select('secure_uuid_download')
                ->where('audit_id', $audit_id)
                ->limit(1)
                ->get('audit')
                ->row();

            if (!empty($query)) {
                if (!empty($query->secure_uuid_download)) {
                    $result = $query->secure_uuid_download;
                } else {
                    $random_audit_uuid = $this->generateUUID(25);
                    $this->db->set('secure_uuid_download', $random_audit_uuid);
                    $this->db->where('audit_id', $audit_id);
                    $this->db->update('audit');
                    $result = $random_audit_uuid;
                }
            } else {
                $result = false;
            }

            return $result;
        } else {
            $result = false;
        }

        return $result;
    }
}
