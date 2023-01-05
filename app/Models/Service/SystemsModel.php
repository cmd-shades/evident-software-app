<?php

namespace App\Models\Service;

use App\Adapter\Model;

class SystemsModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /** Searchable fields **/
    private $searchable_fields  = ['product_system_type.name', 'content_provider.provider_name' ];


    /**
    *   Get System
    **/
    public function get_system($account_id = false, $system_type_id = false, $unorganized = false, $where = false)
    {
        $result = null;

        if ($account_id) {
            if (!empty($where)) {
                $where = convert_to_array($where);
                if (!empty($where)) {
                    if (!empty($where['integrator_id'])) {
                        $integrator_id = $where['integrator_id'];

                        $this->db->join("integrator_systems", "integrator_systems.system_id = product_system_type.system_type_id", "left");
                        $this->db->where('integrator_systems.integrator_id', $integrator_id);
                    }
                }
            }


            $this->db->select("product_system_type.*", false);
            $this->db->select("drm_type.setting_value `drm_type_name`", false);
            $this->db->select("content_provider.provider_name", false);
            $this->db->select("delivery_mechanism.setting_value `delivery_mechanism_name`", false);  ### 2 b rebuild

            $this->db->join("setting `drm_type`", "drm_type.setting_id = product_system_type.drm_type_id", "left");
            $this->db->join("content_provider", "content_provider.provider_id = product_system_type.provider_id", "left");
            $this->db->join("setting `delivery_mechanism`", "delivery_mechanism.setting_id = product_system_type.delivery_mechanism_id", "left");

            if ($system_type_id) {
                $this->db->where("product_system_type.system_type_id", $system_type_id);
            }

            $this->db->where('product_system_type.account_id', $account_id);
            $this->db->where('product_system_type.active', 1);

            $arch_where = "( ( product_system_type.archived IS NULL ) || ( product_system_type.archived != 1 ) )";
            $this->db->where($arch_where);

            $query = $this->db->get('product_system_type');

            if ($query->num_rows() > 0) {
                if ($system_type_id) {
                    $result             = $query->row();

                    $this->db->select("system_providers.*", false);
                    $this->db->select("content_provider.provider_name, content_provider.provider_description", false);

                    $this->db->where("system_providers.account_id", $account_id);
                    $this->db->where("system_providers.system_type_id", $system_type_id);

                    $this->db->where('system_providers.active', 1);

                    $arch_where = "( ( system_providers.archived IS NULL ) || ( system_providers.archived != 1 ) )";
                    $this->db->where($arch_where);

                    $this->db->join("content_provider", "content_provider.provider_id=system_providers.provider_id", "left");
                    $query2 = $this->db->get_where("system_providers");

                    if ($query2->num_rows() > 0) {
                        $result->providers = $query2->result();
                    } else {
                        $result->providers = null;
                    }
                } elseif (!empty($unorganized)) {
                    $result = $query->result();
                } else {
                    foreach ($query->result() as $key => $row) {
                        $result[$row->system_type_id] = $row;
                    }
                }
                $this->session->set_flashdata('message', 'System Type Data found');
            } else {
                $this->session->set_flashdata('message', 'System Type Data not found');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID is required');
        }

        return $result;
    }


    /*
    *   Search through systems
    */
    public function systems_lookup($account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('product_system_type.*', false);
            $this->db->select('drm_type.setting_value `drm_type_name`', false);
            $this->db->select('content_provider.provider_name', false);
            $this->db->select('delivery_mechanism.setting_value `delivery_mechanism_name`', false);

            $this->db->join('content_provider', 'content_provider.provider_id = product_system_type.provider_id', 'left');
            $this->db->join('setting `drm_type`', 'drm_type.setting_id = product_system_type.drm_type_id', 'left');
            $this->db->join('setting `delivery_mechanism`', 'delivery_mechanism.setting_id = product_system_type.delivery_mechanism_id', 'left');

            $this->db->where('product_system_type.account_id', $account_id);

            $arch_where = "( product_system_type.archived != 1 or product_system_type.archived is NULL )";
            $this->db->where($arch_where);

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->searchable_fields as $k => $field) {
                            $search_where[$field] = trim($term);
                        }

                        if (!empty($search_where['content_provider.provider_name'])) {
                            $search_where['content_provider.provider_name'] =   trim($term);
                            unset($search_where['content_provider.provider_name']);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->searchable_fields as $k => $field) {
                        $search_where[$field] = $search_term;
                    }

                    if (!empty($search_where['content_provider.provider_name'])) {
                        $search_where['content_provider.provider_name'] =   trim($search_term);
                        unset($search_where['content_provider.provider_name']);
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if ($where) {
                $where = (!is_array($where)) ? json_decode($where) : $where;
                $where = (is_object($where)) ? object_to_array($where) : $where;

                if ($where) {
                    $this->db->where($where);
                }
            }

            if ($order_by) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('product_system_type.name');
            }

            $query = $this->db->limit($limit, $offset)
                ->get('product_system_type');

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Records found.');
            } else {
                $this->session->set_flashdata('message', 'No records found matching your criteria.');
            }
        }

        return $result;
    }

    /*
    *   Get total system count for the search
    */
    public function get_total_systems($account_id = false, $search_term = false, $block_statuses = false, $where = false)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('product_system_type.*', false);
            $this->db->select('drm_type.setting_value `drm_type_name`', false);
            $this->db->select('content_provider.provider_name', false);
            $this->db->select('delivery_mechanism.setting_value `delivery_mechanism_name`', false);

            $this->db->join('content_provider', 'content_provider.provider_id = product_system_type.provider_id', 'left');
            $this->db->join('setting `drm_type`', 'drm_type.setting_id = product_system_type.drm_type_id', 'left');
            $this->db->join('setting `delivery_mechanism`', 'delivery_mechanism.setting_id = product_system_type.delivery_mechanism_id', 'left');

            $this->db->where('product_system_type.account_id', $account_id);

            $arch_where = "( product_system_type.archived != 1 or product_system_type.archived is NULL )";
            $this->db->where($arch_where);

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->searchable_fields as $k => $field) {
                            $search_where[$field] = trim($term);
                        }

                        if (!empty($search_where['content_provider.provider_name'])) {
                            $search_where['content_provider.provider_name'] =   trim($term);
                            unset($search_where['content_provider.provider_name']);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->searchable_fields as $k => $field) {
                        $search_where[$field] = $search_term;
                    }

                    if (!empty($search_where['content_provider.provider_name'])) {
                        $search_where['content_provider.provider_name'] =   trim($search_term);
                        unset($search_where['content_provider.provider_name']);
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if ($where) {
                $where = (!is_array($where)) ? json_decode($where) : $where;
                $where = (is_object($where)) ? object_to_array($where) : $where;

                if ($where) {
                    $this->db->where($where);
                }
            }

            $query = $this->db->from('product_system_type')->count_all_results();

            $results['total'] = !empty($query) ? $query : 0;
            $results['pages'] = !empty($query) ? ceil($query / DEFAULT_LIMIT) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }



    /**
    *   DRM Types
    **/
    public function get_drm_types($account_id = false, $drm_type_id = false, $unorganized = false)
    {
        $result = null;

        if ($account_id) {
            $this->db->where('drm_type.account_id', $account_id);

            if ($drm_type_id) {
                $this->db->where("drm_type.drm_type_id", $drm_type_id);
            }

            $this->db->where('active', 1);

            $arch_where = "( ( drm_type.archived IS NULL ) || ( drm_type.archived != 1 ) )";
            $this->db->where($arch_where);

            $query = $this->db->get('drm_type');

            if ($query->num_rows() > 0) {
                if (!empty($unorganized)) {
                    $result = $query->result();
                } else {
                    foreach ($query->result() as $key => $row) {
                        $result[$row->drm_type_id] = $row;
                    }
                }
                $this->session->set_flashdata('message', 'DRM(s) Data found');
            } else {
                $this->session->set_flashdata('message', 'DRM(s) Data not found');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID required');
        }

        return $result;
    }


    /**
    *   DRM Types
    **/
    public function get_mechanism_types($account_id = false, $mechanism_id = false, $unorganized = false)
    {
        $result = null;

        if ($account_id) {
            $this->db->where('delivery_mechanism.account_id', $account_id);

            if ($mechanism_id) {
                $this->db->where("delivery_mechanism.mechanism_id", $mechanism_id);
            }

            $this->db->where('active', 1);

            $arch_where = "( ( delivery_mechanism.archived IS NULL ) || ( delivery_mechanism.archived != 1 ) )";
            $this->db->where($arch_where);

            $query = $this->db->get('delivery_mechanism');

            if ($query->num_rows() > 0) {
                if (!empty($unorganized)) {
                    $result = $query->result();
                } else {
                    foreach ($query->result() as $key => $row) {
                        $result[$row->mechanism_id] = $row;
                    }
                }
                $this->session->set_flashdata('message', 'Delivery Mechanism(s) Data found');
            } else {
                $this->session->set_flashdata('message', 'Delivery Mechanism(s) Data not found');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID required');
        }

        return $result;
    }



    /*
    *   Create new System
    */
    public function create_system($account_id = false, $system_data = false)
    {
        $result = false;
        if (!empty($account_id)) {
            $data = [];

            $system_details = (!empty($system_data['system_details'])) ? json_decode($system_data['system_details']) : false ;

            if (!empty($system_details)) {
                foreach ($system_details as $key => $value) {
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
                    } else {
                        $value = trim($value);
                    }
                    $data[$key] = $value;
                }

                if (!empty($data)) {
                    $data['account_id']     = $account_id;
                    $data['created_by']     = $this->ion_auth->_current_user->id;
                    $new_system_data = $this->ssid_common->_filter_data('product_system_type', $data);
                    $this->db->insert('product_system_type', $new_system_data);
                    if ($this->db->trans_status() !== false) {
                        $system_insert_id   = $this->db->insert_id();
                        ## $result          = $this->get_systems( $account_id, $system_insert_id ); ### not ready yet
                        $result             = $this->db->get_where("product_system_type", ["system_type_id" => $system_insert_id, "account_id" => $account_id])->row();
                        $this->session->set_flashdata('message', 'System record created successfully.');
                    }
                }
            } else {
                $this->session->set_flashdata('message', 'No System details.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id supplied.');
        }
        return $result;
    }



    /*
    *   Update System record
    */
    public function update_system($account_id = false, $system_type_id = false, $system_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($system_type_id) && !empty($system_data)) {
            $check_system = $this->db->get_where('product_system_type', ['account_id' => $account_id, 'system_type_id' => $system_type_id])->row();
            if (!empty($check_system)) {
                $system_data = (convert_to_array($system_data['systems_details']));

                $data = [];
                foreach ($system_data as $key => $value) {
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
                    } else {
                        $value = trim($value);
                    }
                    $data[$key] = $value;
                }

                if (!empty($data)) {
                    $update_data = $this->ssid_common->_filter_data('product_system_type', $data);
                    $this->db->where('system_type_id', $system_type_id)
                        ->where('account_id', $account_id)
                        ->update('product_system_type', $update_data);

                    if ($this->db->trans_status() !== false) {
                        $result = $result = $this->get_system($account_id, $system_type_id);
                        $this->session->set_flashdata('message', 'System record updated successfully.');
                    }
                }
            } else {
                $this->session->set_flashdata('message', 'Foreign System record. Access denied.');
            }
        } else {
            $this->session->set_flashdata('message', 'No System data supplied.');
        }
        return $result;
    }


    /*
    *   Delete System profile
    */
    public function delete_system($account_id = false, $system_type_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($system_type_id)) {
            $check_system = $this->db->get_where('product_system_type', ['account_id' => $account_id, 'system_type_id' => $system_type_id])->row();
            if (!empty($check_system)) {
                $data = [
                    "archived"          => 1,
                    "active"            => 0,
                    "last_modified_by"  => $this->ion_auth->_current_user->id,
                ];

                $this->db->update("product_system_type", $data, [ "account_id" => $account_id, "system_type_id" => $system_type_id ]);

                if ($this->db->trans_status() !== false) {
                    $result = true;
                    $this->session->set_flashdata('message', 'System record deleted successfully.');
                } else {
                    $this->session->set_flashdata('message', 'System record hasn\'t been deleted.');
                }
            } else {
                $this->session->set_flashdata('message', 'Incorrect System ID');
            }
        } else {
            $this->session->set_flashdata('message', 'Required data missing: Account ID or System ID');
        }
        return $result;
    }

    /*
    *   Function to get system providers
    */
    public function get_providers($account_id = false, $provider_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where['system_type_id']) && !empty($where['not_added']) && ($where['not_added'] == 'yes')) {
                    $system_type_id = $where['system_type_id'];

                    ## already added system provider ids
                    $this->db->select("provider_id");
                    $this->db->where("account_id", $account_id);
                    $this->db->where("system_type_id", $system_type_id);
                    $this->db->where("active", 1);
                    $arch_where = "( archived != 1 OR archived is NULL )";
                    $this->db->where($arch_where);

                    $added_providers = $this->db->get("system_providers")->result_array();

                    $added_providers_array = array_column($added_providers, "provider_id");

                    if (!empty($added_providers_array)) {
                        $this->db->where_not_in("provider_id", $added_providers_array);
                    }

                    unset($where['system_type_id']);
                    unset($where['not_added']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $this->db->select("content_provider.*", false);

            if (!empty($provider_id)) {
                $this->db->where("provider_id", $provider_id);
            }

            $arch_where = "( content_provider.archived != 1 or content_provider.archived is NULL )";
            $this->db->where($arch_where);
            $this->db->where("content_provider.active", 1);
            $this->db->order_by("content_provider.provider_name ASC");

            $query = $this->db->get("content_provider");

            if (!empty($query->num_rows() && $query->num_rows() > 0)) {
                $dataset = $query->result();

                if (!empty($provider_id)) {
                    $result = $dataset[0];
                } else {
                    $result = $dataset;
                    $this->session->set_flashdata('message', 'Provider(s) data found.');
                }
            } else {
                $this->session->set_flashdata('message', 'Provider(s) data not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID not supplied.');
        }

        return $result;
    }


    /*
    *   Delete a provider from the system
    */
    public function delete_provider($account_id = false, $system_providers_id = false, $where = false)
    {
        $result = false;

        if (!empty($account_id)) {
            $where = convert_to_array($where);

            if (!empty($system_providers_id) || ((!empty($where['system_id'])) && (!empty($where['provider_id'])))) {
                if (!empty($system_providers_id)) {
                    $this->db->where("system_providers_id", $system_providers_id);
                } elseif ((!empty($where['system_id'])) && (!empty($where['provider_id']))) {
                    if (!empty($where['system_id'])) {
                        $system_type_id = $where['system_id'];
                        $this->db->where("system_type_id", $system_type_id);
                        unset($where['system_id']);
                    }

                    if (!empty($where['provider_id'])) {
                        $provider_id = $where['provider_id'];
                        $this->db->where("provider_id", $provider_id);
                        unset($where['provider_id']);
                    }

                    if (!empty($where)) {
                        $this->db->where($where);
                    }
                }

                $upd_data = [
                    "active"    => null,
                    "archived"  => 1
                ];

                $query = $this->db->update("system_providers", $upd_data);

                if ($this->db->trans_status() !== false) {
                    $this->session->set_flashdata('message', 'Provider has been deleted');
                    $result = true;
                } else {
                    $this->session->set_flashdata('message', 'Provider hasn\'t been deleted');
                }
            } else {
                $this->session->set_flashdata('message', 'Expected data not provided');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID not supplied');
        }

        return $result;
    }



    /*
    *   Add provider with the approval date to the system
    */
    public function add_provider($account_id = false, $system_type_id = false, $approval_date = false, $providers = false)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($system_type_id)) {
                if (!empty($approval_date)) {
                    if (!empty($providers)) {
                        $providers  = json_decode($providers);
                        $system_type_id     = json_decode($system_type_id);

                        $i = 0;
                        if (is_array($system_type_id)) {
                            foreach ($system_type_id as $key => $sys_id) {
                                if (is_array($providers)) {
                                    foreach ($providers as $provider_id) {
                                        $batch_data[$i]['account_id']           = $account_id;
                                        $batch_data[$i]['system_type_id']       = $sys_id;
                                        $batch_data[$i]['approval_date']        = format_date_db($approval_date);
                                        $batch_data[$i]['provider_id']          = $provider_id;
                                        $batch_data[$i]['created_by']           = $this->ion_auth->_current_user->id;
                                        $i++;
                                    }
                                } else {
                                    $batch_data[$i]['account_id']               = $account_id;
                                    $batch_data[$i]['system_type_id']           = $sys_id;
                                    $batch_data[$i]['approval_date']            = format_date_db($approval_date);
                                    $batch_data[$i]['created_by']               = $this->ion_auth->_current_user->id;
                                    $i++;
                                }
                            }
                        } else {
                            if (is_array($providers)) {
                                foreach ($providers as $key => $provider_id) {
                                    $batch_data[$i]['account_id']               = $account_id;
                                    $batch_data[$i]['provider_id']              = $provider_id;
                                    $batch_data[$i]['system_type_id']           = $system_type_id;
                                    $batch_data[$i]['approval_date']            = format_date_db($approval_date);
                                    $batch_data[$i]['created_by']               = $this->ion_auth->_current_user->id;
                                    $i++;
                                }
                            } else {
                                $batch_data[0]['account_id']                    = $account_id;
                                $batch_data[0]['provider_id']                   = $providers;
                                $batch_data[0]['system_type_id']                = $system_type_id;
                                $batch_data[0]['approval_date']                 = format_date_db($approval_date);
                                $batch_data[0]['created_by']                    = $this->ion_auth->_current_user->id;
                            }
                        }

                        $this->db->insert_batch("system_providers", $batch_data);

                        if ($this->db->affected_rows() > 0) {
                            $insert_id  = $this->db->insert_id();
                            $result     = $this->db->get_where("system_providers", ["account_id" => $account_id, "system_providers_id" => $insert_id])->row();
                            $this->session->set_flashdata('message', 'Provider\'s Approval Date(s) been added');
                        } else {
                            $this->session->set_flashdata('message', 'There was an error processing your request');
                        }
                    } else {
                        $this->session->set_flashdata('message', 'No Provider(s) supplied.');
                    }
                } else {
                    $this->session->set_flashdata('message', 'No Provider\'s Approval Date supplied.');
                }
            } else {
                $this->session->set_flashdata('message', 'No System ID supplied.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account ID supplied.');
        }

        return $result;
    }
}
