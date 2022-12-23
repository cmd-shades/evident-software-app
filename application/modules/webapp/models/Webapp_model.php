<?php

namespace Application\Modules\Web\Models;


use System\Core\CI_Model;

class Webapp_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }


    /** Get module ID from the controller **/
    public function _get_module_id($module_controller)
    {
        $result     = false;
        $module_id  = $this->get_module_id_by_controller($module_controller);
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
                    $options = array_merge($options, ['method' => 'GET']);
                } else {
                    $options = ['method' => 'GET'];
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
            $params     = ['account_id' => $user->account_id, 'user_id' => $user->id,'module_id' => $module_id];
            $pointer    = 'access/check_module_access';

            if ($module_item) {
                $params['module_item']  = $module_item;
                $pointer                = 'access/module_item_permissions';
            }
            $permission = $this->api_dispatcher(api_end_point() . $pointer, $params, ['auth_token' => $user->auth_token]);

            if (!empty($permission->mod_item_access)) {
                $module_access = (is_object($permission->mod_item_access)) ? object_to_array($permission->mod_item_access) : $permission->mod_item_access;
                if (!empty($module_access)) {
                    $result = (!empty($module_access[0])) ? $module_access[0] : false;
                }
            }

            if (!empty($permission->module_access)) {
                $result = (!empty($permission->module_access[0])) ? true : false;
            }
        }

        return $result;
    }
}
