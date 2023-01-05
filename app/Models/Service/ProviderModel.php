<?php

namespace App\Models\Service;

use App\Adapter\Model;

class ProviderModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    private $searchable_fields = ["content_provider.provider_name", "content_provider.provider_id"];

    public function get_provider($account_id = false, $provider_id = false, $where = false, $unorganized = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $where = convert_to_array($where);

            ## Filter Content by System
            if (isset($where['system_type_id'])) {
                if (!empty($where['system_type_id'])) {
                    $get_providers = $this->db->select('provider_id')
                        ->get_where('system_providers', [ 'account_id' => $account_id, 'system_type_id' => $where['system_type_id'] ]);

                    if ($get_providers->num_rows() > 0) {
                        $system_providers = array_column($get_providers->result_array(), 'provider_id');
                        $this->db->where_in('content_provider.provider_id', $system_providers);
                    }
                }
                unset($where['system_type_id']);
            }

            $integrator_territory_providers = [];

            ## Filter Providers By Integrator
            if (isset($where['integrator_id'])) {
                if (!empty($where['integrator_id'])) {
                    $get_integrator_systems = $this->db->select('system_id')
                        ->where('integrator_systems.active', 1)
                        ->get_where('integrator_systems', [ 'account_id' => $account_id, 'integrator_id' => $where['integrator_id'] ]);

                    if ($get_integrator_systems->num_rows() > 0) {
                        $integrator_systems = array_column($get_integrator_systems->result_array(), 'system_id');
                        if (!empty($integrator_systems)) {
                            $get_integrator_providers = $this->db->select('provider_id')
                                ->where('system_providers.active', 1)
                                ->where_in('system_providers.system_type_id', $integrator_systems)
                                ->group_by('system_providers.provider_id')
                                ->get_where('system_providers', [ 'account_id' => $account_id ]);

                            if ($get_integrator_providers->num_rows() > 0) {
                                $integrator_providers = array_column($get_integrator_providers->result_array(), 'provider_id');

                                ## Filter further By Territory
                                if (isset($where['territory_id'])) {
                                    if (!empty($integrator_providers) && !empty($where['territory_id'])) {
                                        $get_territory_providers = $this->db->select('provider_id')
                                            ->where('provider_territories.active', 1)
                                            ->group_by('provider_territories.provider_id')
                                            ->get_where('provider_territories', [ 'account_id' => $account_id, 'territory_id' => $where['territory_id'] ]);

                                        if ($get_territory_providers->num_rows() > 0) {
                                            $territory_providers = array_column($get_territory_providers->result_array(), 'provider_id');

                                            $integrator_territory_providers = array_intersect($integrator_providers, $territory_providers);

                                            if (!empty($integrator_territory_providers)) {
                                                $this->db->where_in('content_provider.provider_id', $integrator_territory_providers);
                                            }
                                        } else {
                                            $this->session->set_flashdata('message', 'No data matching your criteria.');
                                            return false;
                                        }
                                    } else {
                                        $this->session->set_flashdata('message', 'No data matching your criteria.');
                                        return false;
                                    }
                                    unset($where['territory_id']);
                                } else {
                                    $this->session->set_flashdata('message', 'Please provider a Territory to filter by.');
                                    return false;
                                }
                            } else {
                                $this->session->set_flashdata('message', 'No data matching your criteria.');
                                return false;
                            }
                        } else {
                            $this->session->set_flashdata('message', 'No data matching your criteria.');
                            return false;
                        }
                    } else {
                        $this->session->set_flashdata('message', 'No data matching your criteria.');
                        return false;
                    }
                }
                unset($where['integrator_id']);
            }


            if (!empty($where['setting_group_name'])) {               // channel
                $setting_group_name = $where['setting_group_name'];

                $this->db->where("setting.setting_group_name", $setting_group_name);
                unset($where['setting_group_name']);
            }

            $this->db->select("content_provider.*", false);
            $this->db->select("setting.setting_value `provider_category_name`, setting.setting_group_name `provider_group_name`", false);
            $this->db->join("setting", "setting.setting_id = content_provider.content_provider_category_id", "left");

            if (!empty($provider_id)) {
                $this->db->where("provider_id", $provider_id);
            }

            if (!empty($where)) {
                $this->db->where($where);
            }

            $arch_where = "( content_provider.archived != 1 or content_provider.archived is NULL )";
            $this->db->where($arch_where);
            $this->db->where("content_provider.active", 1);
            $this->db->order_by("content_provider.provider_id ASC");
            $query = $this->db->get("content_provider");

            if (!empty($query->num_rows() && $query->num_rows() > 0)) {
                if ($unorganized) {
                    $result = $query->result();
                } else {
                    if ($unorganized) {
                        $result = $query->result();
                    } else {
                        $dataset = $query->result();
                        if (!empty($provider_id)) {
                            $result = $dataset[0];
                            $result->territories    = $this->get_provider_territories($account_id, $provider_id);
                        } else {
                            foreach ($dataset as $row) {
                                $result[$row->provider_id] = $row;
                            }
                        }
                    }
                }
                $this->session->set_flashdata('message', 'Provider(s) data found.');
            } else {
                $this->session->set_flashdata('message', 'Provider(s) data not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID not supplied.');
        }

        return $result;
    }

    public function get_provider_territories($account_id = false, $provider_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($provider_id)) {
            $this->db->select("it.*", false);
            $this->db->select("ct.country", false);

            $this->db->join("content_territory `ct`", "ct.territory_id = it.territory_id", "left");

            $this->db->where("it.active", 1);
            $arch_where = "( it.archived != 1 or it.archived is NULL )";
            $this->db->where($arch_where);

            $this->db->where("it.provider_id", $provider_id);
            $this->db->where("it.account_id", $account_id);

            $this->db->order_by("ct.country ASC");

            $query = $this->db->get("provider_territories `it`");

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Integrator territories data found.');
            } else {
                $this->session->set_flashdata('message', 'Integrator territories data not found.');
            }
        }
        return $result;
    }

    public function get_provider_category($account_id = false, $category_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select('cpc.setting_id, cpc.setting_value `provider_category_name`, cpc.value_desc `provider_category_description`, cpc.setting_group_name `provider_category_group`, cpc.setting_order `provider_category_order`', false);

            if (!empty($category_id)) {
                $this->db->where("cpc.setting_id", $category_id);
            }

            if (!empty($where)) {
                $where = convert_to_array($where);
                $this->db->where($where);
            }

            $arch_where = "( cpc.archived != 1 or cpc.archived is NULL )";
            $this->db->where($arch_where);
            $this->db->where("cpc.setting_name_id", 33);
            $this->db->where("cpc.is_active", 1);
            $query = $this->db->get("setting `cpc`");

            if (!empty($query->num_rows() && $query->num_rows() > 0)) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Provider Category(ies) data found.');
            } else {
                $this->session->set_flashdata('message', 'Provider Category(ies) data not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID not supplied.');
        }

        return $result;
    }


    /*
    *   Create new provider
    */
    public function create_provider($account_id = false, $provider_data = false)
    {
        $result = false;
        if (!empty($account_id) && (!empty($provider_data))) {
            $data = [];
            $provider_data = json_decode($provider_data);

            if (!empty($provider_data)) {
                foreach ($provider_data as $key => $value) {
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

                if (!empty($data)) {
                    $data['account_id']     = $account_id;
                    $data['created_by']     = $this->ion_auth->_current_user->id;

                    $new_provider_data      = $this->ssid_common->_filter_data('content_provider', $data);
                    $new_provider_data['provider_group'] = $new_provider_data['provider_description'];

                    ## check the uniqueness of the provider reference code
                    $provider_ref_code_exists = false;
                    $this->db->where('provider_reference_code', $new_provider_data['provider_reference_code']);
                    $this->db->where('account_id', $account_id);
                    $provider_ref_code_exists = $this->db->get("content_provider")->row();

                    if (!$provider_ref_code_exists) {
                        $this->db->insert('content_provider', $new_provider_data);

                        $provider_insert_id     = !empty($this->db->insert_id()) ? $this->db->insert_id() : false ;
                        $result                 = (!empty($provider_insert_id)) ? $this->get_provider($account_id, $provider_insert_id) : false ;
                        $this->session->set_flashdata('message', 'Provider record created successfully.');
                    } else {
                        $this->session->set_flashdata('message', 'Provider Reference Code already exists');
                    }
                }
            } else {
                $this->session->set_flashdata('message', 'There was an error processing the Provider Data');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id or Provider Data supplied.');
        }
        return $result;
    }


    /*
    *   Update provider
    */
    public function update_provider($account_id = false, $provider_id = false, $provider_data = false)
    {
        $result = false;
        if (!empty($account_id)  && !empty($provider_id) && (!empty($provider_data))) {
            $data = [];
            $provider_data = json_decode($provider_data);

            if (!empty($provider_data)) {
                foreach ($provider_data as $key => $value) {
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

                if (!empty($data)) {
                    $data['modified_by']                = $this->ion_auth->_current_user->id;

                    $exempt_columns                     = ["account_id", "provider_id", "created_by", "created_date"];
                    $u_provider_data                    = $this->ssid_common->_filter_data('content_provider', $data, $exempt_columns);
                    $u_provider_data['provider_group']  = str_replace(' ', '_', html_escape($u_provider_data['provider_description']));

                    ## check the uniqueness of the provider reference code
                    $provider_ref_code_exists = false;

                    if (!empty($u_provider_data['provider_reference_code'])) {
                        $this->db->where('provider_reference_code', $u_provider_data['provider_reference_code']);
                        $this->db->where('account_id', $account_id);
                        $this->db->where_not_in('provider_id', $provider_id); ## to omit the self one
                        $provider_ref_code_exists = $this->db->get("content_provider")->row();
                    }

                    if (!$provider_ref_code_exists) {
                        $this->db->update('content_provider', $u_provider_data, ["provider_id" => $provider_id, "account_id" => $account_id]);

                        if ($this->db->trans_status() !== false) {
                            $result = $this->get_provider($account_id, $provider_id);
                            $this->session->set_flashdata('message', 'Provider record updated successfully.');
                        } else {
                            $this->session->set_flashdata('message', 'Provider record hasn\'t been updated.');
                        }
                    } else {
                        $this->session->set_flashdata('message', 'Provider Reference Code already exists');
                    }
                }
            } else {
                $this->session->set_flashdata('message', 'There was an error processing the Provider Data');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id or Provider Data supplied.');
        }
        return $result;
    }


    /*
    *   Provider Lookup
    */
    public function provider_lookup($account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('content_provider.*', false);
            $this->db->select('cpc.setting_value `provider_category_name`, cpc.value_desc `provider_category_description`, cpc.setting_group_name `provider_category_group`', false);
            $this->db->select('CONCAT( c.first_name, " ", c.last_name ) created_by_full_name', false);
            $this->db->select('CONCAT( m.first_name, " ", m.last_name ) modified_by_full_name', false);

            $this->db->join('setting cpc', 'cpc.setting_id = content_provider.content_provider_category_id', 'left');
            $this->db->join('user c', 'c.id = content_provider.created_by', 'left');
            $this->db->join('user m', 'm.id = content_provider.modified_by', 'left');

            $this->db->where('content_provider.account_id', $account_id);

            $arch_where = "( content_provider.archived != 1 or content_provider.archived is NULL )";
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

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->searchable_fields as $k => $field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where['content_provider'])) {
                    $content_provider = $where['content_provider'];
                    unset($where['content_provider']);
                    $this->db->where("content_provider.provider_id", $content_provider);
                }

                if (!empty($where['category_id'])) {
                    $category_id = $where['category_id'];
                    unset($where['category_id']);
                    $this->db->where("cpc.setting_id", $category_id);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            if ($order_by) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('content_provider.provider_id ASC');
            }

            $query = $this->db->limit($limit, $offset)
                ->get('content_provider');

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Records found.');
            } else {
                $this->session->set_flashdata('message', 'No records found matching your criteria.');
            }
        }

        return $result;
    }


    public function get_total_provider($account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('content_provider.*', false);

            $this->db->select('cpc.setting_value `provider_category_name`, cpc.value_desc `provider_category_description`, cpc.setting_group_name `provider_category_group`', false);
            $this->db->select('CONCAT( c.first_name, " ", c.last_name ) created_by_full_name', false);
            $this->db->select('CONCAT( m.first_name, " ", m.last_name ) modified_by_full_name', false);

            $this->db->join('setting cpc', 'cpc.setting_id = content_provider.content_provider_category_id', 'left');
            $this->db->join('user c', 'c.id = content_provider.created_by', 'left');
            $this->db->join('user m', 'm.id = content_provider.modified_by', 'left');

            $this->db->where('content_provider.account_id', $account_id);

            $arch_where = "( content_provider.archived != 1 or content_provider.archived is NULL )";
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

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->searchable_fields as $k => $field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where['content_provider'])) {
                    $content_provider = $where['content_provider'];
                    unset($where['content_provider']);
                    $this->db->where("content_provider.provider_id", $content_provider);
                }

                if (!empty($where['category_id'])) {
                    $category_id = $where['category_id'];
                    unset($where['category_id']);
                    $this->db->where("cpc.setting_id", $category_id);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $query = $this->db->from('content_provider')->count_all_results();

            $results['total'] = !empty($query) ? $query : 0;
            $results['pages'] = !empty($query) ? ceil($query / (($limit > 0) ? $limit : DEFAULT_LIMIT)) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }



    /*
    *   Delete provider
    */
    public function delete_provider($account_id = false, $provider_id = false)
    {
        $result = false;
        if (!empty($account_id)  && !empty($provider_id)) {
            $provider_b4 = $this->get_provider($account_id, $provider_id);

            if (!empty($provider_b4)) {
                $data = [
                    "archived"      => 1,
                    "active"        => 0,
                    "modified_by"   => $this->ion_auth->_current_user->id,
                ];

                ## archive the Provider Reference Code to be available
                if (!empty($provider_b4->provider_reference_code)) {
                    $data['provider_reference_code']            = $provider_b4->provider_reference_code . '_arch_' . microtime(true) ;
                    ## To keep the column unique and avoid duplicates as a closest possible action tested on two submissions at once: inserted new row at the same time when update. Micro time( true) picked the difference, time() didn't.
                }

                $d_provider_data    = $this->ssid_common->_filter_data('content_provider', $data);
                $this->db->update('content_provider', $d_provider_data, ["provider_id" => $provider_id, "account_id" => $account_id]);

                if ($this->db->affected_rows() > 0) {
                    $result = true;
                    $this->session->set_flashdata('message', 'Provider record has been deleted.');
                } else {
                    $this->session->set_flashdata('message', 'Provider record hasn\'t been deleted.');
                }
            } else {
                $this->session->set_flashdata('message', 'Incorrect Provider ID supplied.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id or Provider ID supplied.');
        }
        return $result;
    }


    public function get_territories($account_id = false, $territory_id = false, $where = false, $unorganized = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($where)) {
                $where = convert_to_array($where);


                if (!empty($where['provider_id']) && !empty($where['not_added']) && ($where['not_added'] == 'yes')) {
                    $provider_id = $where['provider_id'];

                    ## already added clearance territory ids
                    $this->db->select("territory_id");
                    $this->db->where("account_id", $account_id);
                    $this->db->where("provider_id", $provider_id);
                    $this->db->where("active", 1);
                    $arch_where = "( archived != 1 OR archived is NULL )";
                    $this->db->where($arch_where);

                    $added_territories = $this->db->get("provider_territories")->result_array();

                    $added_territories_array = array_column($added_territories, "territory_id");

                    if (!empty($added_territories_array)) {
                        $this->db->where_not_in("territory_id", $added_territories_array);
                    }

                    unset($where['provider_id']);
                    unset($where['not_added']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $this->db->select("content_territory.*", false);

            if (!empty($territory_id)) {
                $this->db->where("territory_id", $territory_id);
            }

            $arch_where = "( content_territory.archived != 1 or content_territory.archived is NULL )";
            $this->db->where($arch_where);
            $this->db->where("content_territory.active", 1);
            $this->db->order_by("content_territory.country ASC");

            $query = $this->db->get("content_territory");

            if (!empty($query->num_rows() && $query->num_rows() > 0)) {
                if ($unorganized) {
                    $result = $query->result();
                } else {
                    $dataset = $query->result();

                    foreach ($dataset as $row) {
                        $result[$row->territory_id]                     = $row;
                        $result[$row->territory_id]->country_n_code     = ucfirst(strtolower($row->country)) . ' ' . strtoupper($row->code);
                    }
                }
                $this->session->set_flashdata('message', 'Territory(ies) found.');
            } else {
                $this->session->set_flashdata('message', 'Territory(ies) not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID not supplied.');
        }

        return $result;
    }

    /*
    *   Delete Territory
    */
    public function delete_territory($account_id = false, $territory_id = false)
    {
        $result = false;
        if (!empty($account_id)  && !empty($territory_id)) {
            $data = [
                "archived"      => 1,
                "active"        => 0,
                "modified_by"   => $this->ion_auth->_current_user->id,
            ];

            $d_territory    = $this->ssid_common->_filter_data('provider_territories', $data);
            $this->db->update('provider_territories', $d_territory, ["territory_id" => $territory_id, "account_id" => $account_id]);

            if ($this->db->affected_rows() > 0) {
                $result = true;
                $this->session->set_flashdata('message', 'Territory has been deleted.');
            } else {
                $this->session->set_flashdata('message', 'Territory hasn\'t been deleted.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id or Integrator ID supplied.');
        }
        return $result;
    }


    public function add_territory($account_id = false, $provider_id = false, $territories = false)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($provider_id)) {
                if (!empty($territories)) {
                    $territories    = json_decode($territories);

                    $i = 0;
                    if (is_array($territories)) {
                        foreach ($territories as $key => $territory_id) {
                            $batch_data[$i]['account_id']               = $account_id;
                            $batch_data[$i]['territory_id']             = $territory_id;
                            $batch_data[$i]['provider_id']          = $provider_id;
                            $batch_data[$i]['created_by']               = $this->ion_auth->_current_user->id;
                            $i++;
                        }
                    } else {
                        $batch_data[0]['account_id']                    = $account_id;
                        $batch_data[0]['territory_id']                  = $territories;
                        $batch_data[0]['provider_id']               = $provider_id;
                        $batch_data[0]['created_by']                    = $this->ion_auth->_current_user->id;
                    }

                    $this->db->insert_batch("provider_territories", $batch_data);

                    if ($this->db->affected_rows() > 0) {
                        $insert_id  = $this->db->insert_id();
                        $result     = $this->db->get_where("provider_territories", ["account_id" => $account_id, "provider_territory_id" => $insert_id])->row();
                        $this->session->set_flashdata('message', 'Territory(ies) been added');
                    } else {
                        $this->session->set_flashdata('message', 'There was an error processing your request');
                    }
                } else {
                    $this->session->set_flashdata('message', 'No Territory(ies) supplied.');
                }
            } else {
                $this->session->set_flashdata('message', 'No Integrator ID supplied.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account ID supplied.');
        }

        return $result;
    }


    /*
    *   Get Packet Identifier(s)
    */
    public function get_packet_identifiers($account_id = false, $where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where['identifier_id'])) {
                    $identifier_id = false;
                    if (is_scalar($where['identifier_id']) && ((int)$where['identifier_id'] > 0)) {
                        $identifier_id = (int) $where['identifier_id'];
                    } elseif (is_array($where['identifier_id'])) {
                        $identifier_id = $where['identifier_id'];
                    }

                    if (!empty($identifier_id)) {
                        $this->db->where_in("pi.identifier_id", $identifier_id);
                    }
                    unset($where['identifier_id']);
                }

                if (!empty($where['codec_type_id'])) {
                    $codec_type_id = false;
                    if (is_scalar($where['codec_type_id']) && ((int)$where['codec_type_id'] > 0)) {
                        $codec_type_id = (int) $where['codec_type_id'];
                    } elseif (is_array($where['codec_type_id'])) {
                        $codec_type_id = $where['codec_type_id'];
                    }

                    if (!empty($codec_type_id)) {
                        $this->db->where_in("pi.codec_type_id", $codec_type_id);
                    }
                    unset($where['codec_type_id']);
                }

                if (!empty($where['codec_name_id'])) {
                    $codec_name_id = false;
                    if (is_scalar($where['codec_name_id']) && ((int)$where['codec_name_id'] > 0)) {
                        $codec_name_id = (int) $where['codec_name_id'];
                    } elseif (is_array($where['codec_name_id'])) {
                        $codec_name_id = $where['codec_name_id'];
                    }

                    if (!empty($codec_name_id)) {
                        $this->db->where_in("pi.codec_name_id", $codec_name_id);
                    }
                    unset($where['codec_name_id']);
                }

                if (!empty($where['language_id'])) {
                    $language_id = false;
                    if (is_scalar($where['language_id']) && ((int)$where['language_id'] > 0)) {
                        $language_id = (int) $where['language_id'];
                    } elseif (is_array($where['language_id'])) {
                        $language_id = $where['language_id'];
                    }

                    if (!empty($language_id)) {
                        $this->db->where_in("pi.language_id", $language_id);
                    }
                    unset($where['language_id']);
                }

                if (!empty($where['definition_id'])) {
                    $definition_id = false;
                    if (is_scalar($where['definition_id']) && ((int)$where['definition_id'] > 0)) {
                        $definition_id = (int) $where['definition_id'];
                    } elseif (is_array($where['definition_id'])) {
                        $definition_id = $where['definition_id'];
                    }

                    if (!empty($definition_id)) {
                        $this->db->where_in("cfcd.definition_id", $definition_id);
                    }
                    unset($where['definition_id']);
                }

                if (!empty($where['pid'])) {
                    $pid = false;
                    if (is_scalar($where['pid']) && ((int)$where['pid'] > 0)) {
                        $pid = (int) $where['pid'];
                    } elseif (is_array($where['pid'])) {
                        $pid = $where['pid'];
                    }

                    if (!empty($pid)) {
                        $this->db->where_in("pi.pid", $pid);
                    }
                    unset($where['pid']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $this->db->select("pi.*", false);
            $this->db->select("cfct.type_name, cfct.type_group, cfct.type_alt_name", false);
            $this->db->select("cfcn.short_name, cfcn.long_name, cfcn.name_group, cfcn.definition_id", false);
            $this->db->select("cfcd.definition_name, cfcd.definition_group", false);

            ## languages
            $this->db->select("clpl.language_name, clpl.language_symbol, clpl.subtitle_code", false);

            $this->db->join("content_format_codec_type `cfct`", "cfct.type_id=pi.codec_type_id", "left");
            $this->db->join("content_format_codec_name `cfcn`", "cfcn.name_id=pi.codec_name_id", "left");
            $this->db->join("content_format_codec_definition `cfcd`", "cfcd.definition_id=cfcn.definition_id", "left");

            ## languages
            $this->db->join("content_language_phrase_language `clpl`", "clpl.language_id=pi.language_id", "left");

            $arch_where = "( pi.archived != 1 or pi.archived is NULL )";
            $this->db->where($arch_where);
            $this->db->where("pi.active", 1);
            $this->db->order_by("cfcd.definition_name ASC, cfct.type_name ASC, pi.identifier_name ASC");

            if (!empty($limit)) {
                if (!empty($offset)) {
                    $this->db->limit($limit, $offset);
                } else {
                    $this->db->limit($limit);
                }
            }

            $query = $this->db->get("packet_identifiers `pi`");


            if (!empty($query->num_rows() && $query->num_rows() > 0)) {
                $result = $query->result_array();
                $this->session->set_flashdata('message', 'Identifier(s) found.');
            } else {
                $this->session->set_flashdata('message', 'Identifier(s) not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID not supplied.');
        }

        return $result;
    }


    /*
    *   Get Packet Identifier(s) assigned to the Provider
    */
    public function get_provider_packet_identifiers($account_id = false, $where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where['identifier_id'])) {
                    $identifier_id = false;
                    if (is_scalar($where['identifier_id']) && ((int)$where['identifier_id'] > 0)) {
                        $identifier_id = (int) $where['identifier_id'];
                    } elseif (is_array($where['identifier_id'])) {
                        $identifier_id = $where['identifier_id'];
                    }

                    if (!empty($identifier_id)) {
                        $this->db->where_in("ppi.identifier_id", $identifier_id);
                    }
                    unset($where['identifier_id']);
                }

                if (!empty($where['provider_id'])) {
                    $provider_id = false;
                    if (is_scalar($where['provider_id']) && ((int)$where['provider_id'] > 0)) {
                        $provider_id = (int) $where['provider_id'];
                    } elseif (is_array($where['provider_id'])) {
                        $provider_id = $where['provider_id'];
                    }

                    if (!empty($provider_id)) {
                        $this->db->where_in("ppi.provider_id", $provider_id);
                    }
                    unset($where['provider_id']);
                }

                if (!empty($where['definition_id'])) {
                    $definition_id = false;
                    $definition_id = $where['definition_id'];
                    $this->db->where_in("cfcn.definition_id", $definition_id);
                    unset($where['definition_id']);
                }

                /*              if( !empty( $where['packet_identifier_id'] ) ){

                                    $packet_identifier_id = false;
                                    if( is_scalar( $where['packet_identifier_id'] ) && ( ( int )$where['packet_identifier_id'] > 0 ) ){
                                        $packet_identifier_id = ( int ) $where['packet_identifier_id'];
                                    } elseif( is_array( $where['packet_identifier_id'] ) ){
                                        $packet_identifier_id = $where['packet_identifier_id'];
                                    }

                                    if( !empty( $packet_identifier_id ) ){
                                        $this->db->where_in( "ppi.identifier_id", $packet_identifier_id );
                                    }
                                    unset( $where['packet_identifier_id'] );
                                } */

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $this->db->select("ppi.*", false);
            $this->db->select("pi.codec_type_id, pi.codec_name_id, pi.is_adult, pi.key_name, pi.identifier_name, pi.language_id, pi.pid, pi.hex_string, pi.hex_id", false);
            $this->db->select("cfct.type_name, cfct.type_group, cfct.type_alt_name", false);
            $this->db->select("cfcn.short_name, cfcn.long_name, cfcn.name_group, cfcn.definition_id", false);
            $this->db->select("cfcd.definition_name, cfcd.definition_group", false);

            ## languages
            $this->db->select("clpl.language_name, clpl.language_desc, clpl.language_symbol, clpl.audio_code, clpl.subtitle_code, clpl.abbreviation, clpl.abbreviation_2", false);

            $this->db->join("packet_identifiers `pi`", "pi.identifier_id=ppi.packet_identifier_id", "left");
            $this->db->join("content_format_codec_type `cfct`", "cfct.type_id=pi.codec_type_id", "left");
            $this->db->join("content_format_codec_name `cfcn`", "cfcn.name_id=pi.codec_name_id", "left");
            $this->db->join("content_format_codec_definition `cfcd`", "cfcd.definition_id=cfcn.definition_id", "left");

            ## languages
            $this->db->join("content_language_phrase_language `clpl`", "clpl.language_id=pi.language_id", "left");

            $arch_where = "( ppi.archived != 1 or ppi.archived is NULL )";
            $this->db->where($arch_where);
            $this->db->where("ppi.active", 1);
            $this->db->order_by("cfct.type_id ASC, cfcn.short_name ASC");

            if (!empty($limit)) {
                if (!empty($offset)) {
                    $this->db->limit($limit, $offset);
                } else {
                    $this->db->limit($limit);
                }
            }

            $query = $this->db->get("provider_packet_identifiers `ppi`");

            if (!empty($query->num_rows() && $query->num_rows() > 0)) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Identifier(s) found.');
            } else {
                $this->session->set_flashdata('message', 'Identifier(s) not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID not supplied.');
        }

        return $result;
    }


    /*
    *   Definition(s)
    */
    public function get_definition($account_id = false, $where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where['definition_id'])) {
                    $definition_id = $where['definition_id'];
                    $this->db->where_in("cfcd.definition_id", $definition_id);
                    unset($where['definition_id']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $this->db->select("cfcd.*", false);

            $arch_where = "( cfcd.archived != 1 or cfcd.archived is NULL )";
            $this->db->where($arch_where);
            $this->db->where("cfcd.active", 1);
            $this->db->order_by("cfcd.definition_name ASC");

            if (!empty($limit)) {
                if (!empty($offset)) {
                    $this->db->limit($limit, $offset);
                } else {
                    $this->db->limit($limit);
                }
            }

            $query = $this->db->get("content_format_codec_definition `cfcd`");

            if (!empty($query->num_rows() && $query->num_rows() > 0)) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Definition(s) found.');
            } else {
                $this->session->set_flashdata('message', 'Definition(s) not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID not supplied.');
        }

        return $result;
    }


    /*
    *   Codec Type(s)
    */
    public function get_codec_type($account_id = false, $where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where['type_id'])) {
                    $type_id = $where['type_id'];
                    $this->db->where_in("cfct.type_id", $type_id);
                    unset($where['type_id']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $this->db->select("cfct.*", false);

            $arch_where = "( cfct.archived != 1 or cfct.archived is NULL )";
            $this->db->where($arch_where);
            $this->db->where("cfct.active", 1);
            $this->db->order_by("cfct.type_name ASC");

            if (!empty($limit)) {
                if (!empty($offset)) {
                    $this->db->limit($limit, $offset);
                } else {
                    $this->db->limit($limit);
                }
            }

            $query = $this->db->get("content_format_codec_type `cfct`");

            if (!empty($query->num_rows() && $query->num_rows() > 0)) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Codec Type(s) found.');
            } else {
                $this->session->set_flashdata('message', 'Codec Type(s) not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID not supplied.');
        }

        return $result;
    }


    /*
    *   Codec Name(s)
    */
    public function get_codec_name($account_id = false, $where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where['name_id'])) {
                    $name_id = $where['name_id'];
                    $this->db->where_in("cfcn.name_id", $name_id);
                    unset($where['name_id']);
                }

                if (!empty($where['definition_id'])) {
                    $definition_id = $where['definition_id'];
                    $this->db->where_in("cfcn.definition_id", $definition_id);
                    unset($where['definition_id']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $this->db->select("cfcn.*", false);

            $arch_where = "( cfcn.archived != 1 or cfcn.archived is NULL )";
            $this->db->where($arch_where);
            $this->db->where("cfcn.active", 1);
            $this->db->order_by("cfcn.short_name ASC");

            if (!empty($limit)) {
                if (!empty($offset)) {
                    $this->db->limit($limit, $offset);
                } else {
                    $this->db->limit($limit);
                }
            }

            $query = $this->db->get("content_format_codec_name `cfcn`");

            if (!empty($query->num_rows() && $query->num_rows() > 0)) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Codec Name(s) found.');
            } else {
                $this->session->set_flashdata('message', 'Codec Name(s) not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID not supplied.');
        }

        return $result;
    }


    /*
    *   Add Packet Identifier to the Provider
    */
    public function add_identifier_to_provider($account_id = false, $provider_id = false, $post_set = false)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($provider_id)) {
                if (!empty($post_set)) {
                    $post_set = convert_to_array($post_set);

                    if (!empty($post_set)) {
                        $packet_exists = false;

                        if (!empty($post_set['packet_identifier_id'])) {
                            $this->db->where("packet_identifier_id", $post_set['packet_identifier_id']);
                        }
                        $arch_where = "( provider_packet_identifiers.archived != 1 or provider_packet_identifiers.archived is NULL )";
                        $this->db->where($arch_where);
                        $this->db->where("account_id", $account_id);
                        $this->db->where("provider_id", $provider_id);
                        $this->db->where("active", 1);
                        $packet_exists = $this->db->get("provider_packet_identifiers")->row();

                        if (!empty($packet_exists)) {
                            $this->session->set_flashdata('message', 'This packet already exists');
                            return $result;
                        } else {
                            $insert_data = [
                                "account_id"            => $account_id,
                                "provider_id"           => $provider_id,
                                "packet_identifier_id"  => (!empty($post_set['packet_identifier_id'])) ? $post_set['packet_identifier_id'] : null,
                                "description"           => (!empty($post_set['description'])) ? $post_set['description'] : null,
                                "created_by"            => $this->ion_auth->_current_user->id
                            ];

                            $query = $this->db->insert("provider_packet_identifiers", $insert_data);

                            if ($this->db->affected_rows() > 0) {
                                $insert_id      = $this->db->insert_id();
                                $result         = $this->get_provider_packet_identifiers($account_id, ["identifier_id" => $insert_id]);
                                $this->session->set_flashdata('message', 'Packet successfully added');
                            } else {
                                $this->session->set_flashdata('message', 'There was an error processing the packet');
                            }
                        }
                    } else {
                        $this->session->set_flashdata('message', 'There was an error processing your data');
                    }
                } else {
                    $this->session->set_flashdata('message', 'No Data supplied.');
                }
            } else {
                $this->session->set_flashdata('message', 'No Integrator ID supplied.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account ID supplied.');
        }

        return $result;
    }



    /*
    *   Update provider PID
    */
    public function update_provider_pid($account_id = false, $identifier_id = false, $identifier_data = false)
    {
        $result = false;
        if (!empty($account_id)  && !empty($identifier_id) && (!empty($identifier_data))) {
            $data = convert_to_array($identifier_data);

            if (!empty($data)) {
                $data['modified_by']                = $this->ion_auth->_current_user->id;

                $u_identifier_data                  = $this->ssid_common->_filter_data('provider_packet_identifiers', $data);

                $this->db->update('provider_packet_identifiers', $u_identifier_data, ["identifier_id" => $identifier_id, "account_id" => $account_id]);

                if ($this->db->trans_status() !== false) {
                    $result = $this->get_provider_packet_identifiers($account_id, ["identifier_id" => $identifier_id]);
                    $this->session->set_flashdata('message', 'Record updated successfully.');
                } else {
                    $this->session->set_flashdata('message', 'Record hasn\'t been updated.');
                }
            } else {
                $this->session->set_flashdata('message', 'There was an error processing the Identifier Data');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account ID, Identifier ID or Identifier Data supplied.');
        }
        return $result;
    }




    /*
    *   Delete provider PID
    */
    public function delete_provider_pid($account_id = false, $identifier_id = false)
    {
        $result = false;
        if (!empty($account_id)  && !empty($identifier_id)) {
            $data['modified_by']        = $this->ion_auth->_current_user->id;
            $data['active']             = null;
            $data['archived']           = 1;

            $this->db->update('provider_packet_identifiers', $data, ["identifier_id" => $identifier_id, "account_id" => $account_id]);

            if ($this->db->trans_status() !== false) {
                $result = true;
                $this->session->set_flashdata('message', 'Record deleted successfully.');
            } else {
                $this->session->set_flashdata('message', 'Record hasn\'t been deleted.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account ID or Identifier ID supplied.');
        }
        return $result;
    }



    /*
    *   Create and Add Price Plan to the Provider
    */
    public function add_provider_price_plan($account_id = false, $provider_id = false, $price_plan_details = false)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($provider_id)) {
                if (!empty($price_plan_details)) {
                    $price_plan_details = convert_to_array($price_plan_details);

                    if (!empty($price_plan_details)) {
                        $price_plan = $this->create_price_plan($account_id, $price_plan_details['price_plan_name'], $price_plan_details);

                        if (!empty($price_plan)) {
                            $insert_data = [
                                "account_id"            => $account_id,
                                "provider_id"           => $provider_id,
                                "plan_id"               => (!empty($price_plan->plan_id)) ? $price_plan->plan_id : null,
                                "created_by"            => $this->ion_auth->_current_user->id
                            ];

                            $query = $this->db->insert("provider_price_plan", $insert_data);

                            if ($this->db->affected_rows() > 0) {
                                $ppp_insert_id  = $this->db->insert_id();
                                ## $result      = $this->get_provider_price_plan( $account_id, ["provider_plan_id" => $insert_id] );
                                $result         = $this->db->get_where("provider_price_plan", ["account_id" => $account_id, "provider_plan_id" => $ppp_insert_id])->row();
                                $this->session->set_flashdata('message', 'Plan successfully added');
                            } else {
                                $this->session->set_flashdata('message', 'There was an error processing the Price Plan');
                            }
                        } else {
                            if (!empty($this->session->flashdata('message'))) {
                                $this->session->set_flashdata('message', $this->session->flashdata('message'));
                            } else {
                                $this->session->set_flashdata('message', 'There was an error saving the Price Plan');
                            }
                        }
                    } else {
                        $this->session->set_flashdata('message', 'Error processing the data.');
                    }
                } else {
                    $this->session->set_flashdata('message', 'No Data supplied.');
                }
            } else {
                $this->session->set_flashdata('message', 'No Integrator ID supplied.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account ID supplied.');
        }

        return $result;
    }


    /*
    *   Function to create a price plan
    */
    public function create_price_plan($account_id = false, $price_plan_name = false, $price_plan_details = false)
    {
        $result = false;
        if (!empty($account_id)) {
            if (!empty($price_plan_name)) {
                if (!empty($price_plan_details)) {
                    foreach ($price_plan_details as $key => $value) {
                        if (in_array($key, format_name_columns())) {
                            $value = format_name($value);
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

                    $plan_data                  = $this->ssid_common->_filter_data('price_plan', $data);
                    $plan_data['account_id']    = $account_id;
                    $plan_data['created_by']    = $this->ion_auth->_current_user->id;

                    if (!empty($plan_data)) {
                        $this->db->insert('price_plan', $plan_data);
                        if ($this->db->affected_rows() > 0) {
                            $insert_id      = $this->db->insert_id();
                            $insert_result  = $this->db->get_where("price_plan", ["account_id" => $account_id, "plan_id" => $insert_id])->row();

                            if (!empty($insert_result)) {
                                $result = $insert_result;
                                $this->session->set_flashdata('message', 'Price Plan has been created successfully.');
                            } else {
                                $this->session->set_flashdata('message', 'Error retrieving the data.');
                            }
                        }
                    } else {
                        $this->session->set_flashdata('message', 'Error processing the data.');
                    }
                } else {
                    $this->session->set_flashdata('message', 'No Price Plan Details supplied.');
                }
            } else {
                $this->session->set_flashdata('message', 'No Price Plan Name supplied.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account ID supplied.');
        }

        return $result;
    }



    /*
    *   Get Provider Plan(s)
    */
    public function get_provider_price_plan($account_id = false, $where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where['provider_plan_id'])) {
                    $provider_plan_id = $where['provider_plan_id'];
                    $this->db->where_in("ppp.provider_plan_id", $provider_plan_id);
                    unset($where['provider_plan_id']);
                }

                if (!empty($where['provider_id'])) {
                    $provider_id = $where['provider_id'];
                    $this->db->where_in("ppp.provider_id", $provider_id);
                    unset($where['provider_id']);
                }

                if (!empty($where['plan_id'])) {
                    $plan_id = $where['plan_id'];
                    $this->db->where_in("ppp.plan_id", $plan_id);
                    unset($where['plan_id']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $this->db->select("ppp.*", false);
            $this->db->select("pp.*", false);

            $this->db->join("price_plan `pp`", "pp.plan_id = ppp.plan_id", "left");

            $arch_where = "( ppp.archived != 1 or ppp.archived is NULL )";
            $this->db->where($arch_where);
            $this->db->where("ppp.active", 1);
            $this->db->order_by("pp.price_plan_name ASC");

            if (!empty($limit)) {
                if (!empty($offset)) {
                    $this->db->limit($limit, $offset);
                } else {
                    $this->db->limit($limit);
                }
            }

            $query = $this->db->get("provider_price_plan `ppp`");

            if (!empty($query->num_rows() && $query->num_rows() > 0)) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Provider Price Plan(s) found.');
            } else {
                $this->session->set_flashdata('message', 'Provider Price Plan(s) not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID not supplied.');
        }

        return $result;
    }



    /*
    *   Get Price Plan(s)
    */
    public function get_price_plan($account_id = false, $where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where['plan_id'])) {
                    $plan_id = $where['plan_id'];
                    $this->db->where_in("pp.plan_id", $plan_id);
                    unset($where['plan_id']);
                }

                if (!empty($where['price_plan_type'])) {
                    $price_plan_type = $where['price_plan_type'];
                    $this->db->where_in("pp.price_plan_type", $price_plan_type);
                    unset($where['price_plan_type']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $this->db->select("pp.*", false);

            $arch_where = "( pp.archived != 1 or pp.archived is NULL )";
            $this->db->where($arch_where);
            $this->db->where("pp.active", 1);
            $this->db->order_by("pp.price_plan_name ASC");

            if (!empty($limit)) {
                if (!empty($offset)) {
                    $this->db->limit($limit, $offset);
                } else {
                    $this->db->limit($limit);
                }
            }

            $query = $this->db->get("price_plan `pp`");

            if (!empty($query->num_rows() && $query->num_rows() > 0)) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Price Plan(s) found.');
            } else {
                $this->session->set_flashdata('message', 'Price Plan(s) not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID not supplied.');
        }

        return $result;
    }


    /*
    *   Delete Provider Price Plan
    */
    public function delete_provider_price_plan($account_id = false, $provider_plan_id = false)
    {
        $result = false;
        if (!empty($account_id)  && !empty($provider_plan_id)) {
            $data['modified_by']        = $this->ion_auth->_current_user->id;
            $data['active']             = null;
            $data['archived']           = 1;

            $this->db->update('provider_price_plan', $data, ["provider_plan_id" => $provider_plan_id, "account_id" => $account_id]);

            if ($this->db->trans_status() !== false) {
                $result = true;
                $this->session->set_flashdata('message', 'Record deleted successfully.');
            } else {
                $this->session->set_flashdata('message', 'Record hasn\'t been deleted.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account ID or Provider Plan ID supplied.');
        }
        return $result;
    }
}
