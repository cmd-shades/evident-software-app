<?php

namespace Application\Service\Models;

use System\Core\CI_Model;

class Settings_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    private $setting_restricted_columns = ["account_id", "module_id", "setting_name_id", "created_date", "created_by"];

    /*
    *   Function to get settings based on given parameters
    */
    public function get_settings($account_id = false, $setting_id = false, $where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select("setting.*, setting_name.*", false);

            $this->db->join("setting_name", "setting_name.setting_name_id = setting.setting_name_id", "left");

            if (!empty($setting_id)) {
                $this->db->where_in("setting.setting_id", $setting_id);
            }

            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where['module_id'])) {
                    $module_id = $where['module_id'];
                    unset($where['module_id']);
                    $this->db->where("setting.module_id", $module_id);
                }

                if (!empty($where['setting_name_id'])) {
                    $setting_name_id = $where['setting_name_id'];

                    if ($setting_name_id == 26) {
                        $this->db->select("ct.territory_id, ct.country, ct.code", false);
                        $this->db->join("content_territory `ct`", "ct.territory_id = setting.setting_territory_id", "left");
                    }

                    unset($where['setting_name_id']);
                    $this->db->where("setting.setting_name_id", $setting_name_id);
                }

                if (!empty($where['subgroup_id'])) {
                    $subgroup_id = $where['subgroup_id'];
                    unset($where['subgroup_id']);
                    $this->db->where("setting.subgroup_id", $subgroup_id);
                }

                if (!empty($where['setting_name'])) {
                    $setting_name = $where['setting_name'];
                    unset($where['setting_name']);
                    $this->db->like("setting_name.setting_name", $setting_name);
                }


                if (!empty($where['setting_name_group'])) {
                    $setting_name_group = $where['setting_name_group'];
                    $this->db->like("setting_name.setting_name_group", $setting_name_group);
                    unset($where['setting_name_group']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $arch_where = "( setting.archived != 1 or setting.archived is NULL )";
            $this->db->where($arch_where);
            $this->db->where("setting.is_active", 1);

            $this->db->order_by("setting.setting_name_id ASC, setting.setting_order ASC, setting.setting_id ASC");

            $query = $this->db->get("setting");

            if (!empty($query->num_rows() && $query->num_rows() > 0)) {
                $dataset = $query->result();
                if (!empty($setting_id) && ( !( is_array($setting_id) ) )) {
                    $result = $dataset[0];
                } else {
                    foreach ($dataset as $row) {
                        $result[$row->setting_id] = $row;
                    }
                }
                $this->session->set_flashdata('message', 'Setting(s) data found.');
            } else {
                $this->session->set_flashdata('message', 'Setting(s) data not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID not supplied.');
        }

        return $result;
    }


    /*
    *   Function to get settings based on given parameters
    */
    public function get_setting_name($account_id = false, $setting_name_id = false, $where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select("setting_name.*", false);

            if (!empty($setting_name_id)) {
                $this->db->where("setting_name.setting_name_id", $setting_name_id);
            }

            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where['module_id'])) {
                    $module_id = $where['module_id'];
                    unset($where['module_id']);
                    $this->db->where("setting_name.module_id", $module_id);
                }

                if (!empty($where['setting_name'])) {
                    $setting_name = $where['setting_name'];
                    unset($where['setting_name']);
                    $this->db->like("setting_name.setting_name", $setting_name);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $arch_where = "( setting_name.archived != 1 or setting_name.archived is NULL )";
            $this->db->where($arch_where);
            $this->db->where("setting_name.is_active", 1);
            $query = $this->db->get("setting_name");

            if (!empty($query->num_rows() && $query->num_rows() > 0)) {
                $dataset = $query->result();
                if (!empty($setting_name_id)) {
                    $result = $dataset[0];
                } else {
                    foreach ($dataset as $row) {
                        $result[$row->setting_name_id] = $row;
                    }
                }
                $this->session->set_flashdata('message', 'Setting Group(s) data found.');
            } else {
                $this->session->set_flashdata('message', 'Setting Group(s) data not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID not supplied.');
        }

        return $result;
    }


    public function update_setting($account_id = false, $setting_id = false, $setting_data = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($setting_id) && !empty($setting_data)) {
            $setting_b4 = $this->get_settings($account_id, $setting_id);

            if (!empty($setting_b4)) {
                $details_updated    = false;
                $setting_data       = object_to_array(json_decode($setting_data));

                $data = [];
                if (!empty($setting_data)) {
                    foreach ($setting_data as $key => $value) {
                        if (!( in_array($key, $this->setting_restricted_columns) )) {
                            if (in_array($key, format_name_columns())) {
                                $value = format_name($value);
                            } elseif (in_array($key, format_email_columns())) {
                                $value = format_email($value);
                            } elseif (in_array($key, format_number_columns())) {
                                $value = format_number($value);
                            } elseif (in_array($key, format_boolean_columns())) {
                                $value = format_boolean($value);
                            } elseif (in_array($key, format_date_columns())) {
                                $value = format_date_db($value);
                            } elseif (in_array($key, format_long_date_columns())) {
                                $value = format_datetime_db($value);
                            } elseif (in_array($key, string_to_json_columns())) {
                                $value = string_to_json($value);
                            } else {
                                $value = trim($value);
                            }
                            $data[$key] = $value;
                        }
                    }

                    if (!empty($data)) {
                        $data['last_modified_by']   = $this->ion_auth->_current_user->id;
                        $update_data                = $this->ssid_common->_filter_data('setting', $data);

                        $this->db->update("setting", $update_data, ['account_id' => $account_id, 'setting_id' => $setting_id]);

                        if ($this->db->trans_status() !== false) {
                            $result = $this->get_settings($account_id, $setting_id);
                            $this->session->set_flashdata('message', 'Setting record updated successfully.');
                        } else {
                            $this->session->set_flashdata('message', 'Setting update request failed.');
                        }
                    }
                }
            } else {
                $this->session->set_flashdata('message', 'Foreign content record. Access denied.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Setting ID, Setting data or Account ID supplied.');
        }
        return $result;
    }



    public function delete_setting($account_id = false, $setting_id = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($setting_id)) {
            $setting_b4 = $this->get_settings($account_id, $setting_id);
            if (!empty($setting_b4)) {
                $delete_data['last_modified_by']        = $this->ion_auth->_current_user->id;
                $delete_data['is_active']           = null;
                $delete_data['archived']            = 1;

                $this->db->update("setting", $delete_data, ['account_id' => $account_id, 'setting_id' => $setting_id]);

                if ($this->db->trans_status() !== false) {
                    $result     = true;
                    $this->session->set_flashdata('message', 'Setting deleted successfully.');
                } else {
                    $this->session->set_flashdata('message', 'Setting Delete request failed.');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Setting ID or Account ID supplied.');
        }
        return $result;
    }


    public function create_setting($account_id = false, $module_id = false, $setting_name_data = false, $setting_data = false)
    {
        $result = $data = $setting_name = $setting_name_id =  false;
        if (!empty($account_id) && ( !empty($module_id) ) && ( !empty($setting_data) )) {
            $setting_name_data  = convert_to_array($setting_name_data);
            $setting_data       = convert_to_array($setting_data);
            $setting_name_id    = ( !empty($setting_data['setting_name_id']) ) ? $setting_data['setting_name_id'] : false ;

            if (!empty($setting_name_data['setting_name']) && !( $setting_name_id )) {
                ## save the setting name and return the setting_name_id

                $setting_name = $this->create_setting_name($account_id, $module_id, $setting_name_data);

                if (!empty($setting_name)) {
                    $setting_name_id = $setting_name->setting_name_id;
                } else {
                    $setting_name_id = false;
                }
            }

            ## at any other case just use the setting_name_id

            ## if setting name ID is present - proceed
            if (!empty($setting_name_id) && !empty($setting_data['values'])) {
                $i = 0;
                foreach ($setting_data['values'] as $row) {
                    $data[$i]['account_id']         = $account_id;
                    $data[$i]['module_id']          = $module_id;
                    $data[$i]['setting_name_id']    = $setting_name_id;
                    $data[$i]['setting_value']      = $row['item_value'];
                    $data[$i]['value_desc']         = $row['value_desc'];
                    $data[$i]['setting_order']      = $row['setting_order'];
                    $data[$i]['created_by']         = $this->ion_auth->_current_user->id;
                    $i++;
                }

                if (!empty($data)) {
                    $query = $this->db->insert_batch('setting', $data);

                    if ($this->db->affected_rows() > 0) {
                        $setting_insert_id  = !empty($this->db->insert_id()) ? $this->db->insert_id() : false ;
                        $result             = ( !empty($setting_insert_id) ) ? $this->get_settings($account_id, $setting_insert_id) : false ;

                        $this->session->set_flashdata('message', 'Setting records created successfully.');
                    } else {
                        $this->session->set_flashdata('message', 'Setting record hasn\'t been created');
                    }
                } else {
                    $this->session->set_flashdata('message', 'There was an error processing the Setting Data');
                }
            } else {
                if (!empty($this->session->flashdata('message'))) {
                    $this->session->set_flashdata('message', $this->session->flashdata('message'));
                } else {
                    $this->session->set_flashdata('message', 'Setting Name or Setting Data are missing');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id or Setting Data supplied.');
        }
        return $result;
    }


    public function create_setting_name($account_id = false, $module_id = false, $setting_name_data = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($module_id) && !empty($setting_name_data['setting_name'])) {
            ## create a reference and check if exists
            $group_reference            = false;
            $group_reference            = $module_id . "_" . ( create_reference_string($setting_name_data['setting_name']) );
            $reference_exists = $this->db->get_where("setting_name", ["account_id" => $account_id, "setting_name_group" => $group_reference ])->row();

            if (!empty($reference_exists)) {
                $this->session->set_flashdata('message', 'The Setting reference already exists');
            } else {
                $data['account_id']         = $account_id;
                $data['module_id']          = $module_id;
                $data['setting_name']       = $setting_name_data['setting_name'];
                $data['setting_name_group'] = $group_reference;
                if (!empty($setting_name_data['setting_name_desc'])) {
                    $data['setting_name_desc']  = $setting_name_data['setting_name_desc'];
                }
                $data['created_by']         = $this->ion_auth->_current_user->id;

                $query = $this->db->insert("setting_name", $data);
                if ($this->db->affected_rows() > 0) {
                    $inserted_id = $this->db->insert_id();
                    $result = $this->db->get_where("setting_name", ["setting_name_id" => $inserted_id ])->row();
                    $this->session->set_flashdata('message', 'Setting Name has been created.');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id or Setting Name supplied.');
        }

        return $result;
    }
}
