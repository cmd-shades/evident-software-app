<?php

namespace Application\Service\Models;

defined('BASEPATH') || exit('No direct script access allowed');

use System\Core\CI_Model;

class Integrator_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $section            = explode("/", $_SERVER["SCRIPT_NAME"]);
        if (!isset($section[1]) || empty($section[1]) || ( !( is_array($section) ) )) {
            $this->app_root = substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), "application"));
        } else {
            if (!isset($_SERVER["DOCUMENT_ROOT"]) || ( empty($_SERVER["DOCUMENT_ROOT"]) )) {
                $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . '/../');
            }

            $this->section      = $section;
            $this->app_root     = $_SERVER["DOCUMENT_ROOT"] . "/" . $section[1] . "/";
            $this->app_root     = str_replace('/index.php', '', $this->app_root);
        }

        $this->load->library('upload');
        $this->load->model('serviceapp/site_model', 'site_service');

        $this->searchable_fields = ["i.integrator_name", "i.integrator_email"];
    }

    /*
    *   To create an integrator from the post data
    */
    public function create_integrator($account_id, $integrator_name, $integrator_data)
    {
        $result = false;
        if (!empty($account_id)) {
            if (!empty($integrator_name)) {
                if (!empty($integrator_data)) {
                    $integrator_details = convert_to_array($integrator_data['integrator_details']);

                    if (!empty($integrator_details)) {
                        $data = [];
                        foreach ($integrator_details as $key => $value) {
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

                        $data['account_id']     = $account_id;
                        $data['created_by']     = $this->ion_auth->_current_user->id;

                        $filtered_data = $this->ssid_common->_filter_data('integrator', $data);
                        $this->db->insert('integrator', $filtered_data);

                        if ($this->db->affected_rows() > 0) {
                            $integrator_id  = !empty($this->db->insert_id()) ? $this->db->insert_id() : false ;

                            ## save address as a business address
                            $integrator_address_data = convert_to_array($integrator_data['integrator_address']);
                            if (( !empty($integrator_address_data) )) {
                                $address_type_id = 3;   ## business address type id
                                $save_integrator_address = $this->save_integrator_address($account_id, $integrator_id, $integrator_address_data, $address_type_id);
                            }

                            ## save systems
                            $integrator_systems_data = convert_to_array($integrator_data['integrator_systems']);

                            if (( !empty($integrator_systems_data) )) {
                                $save_integrator_systems = $this->save_integrator_systems($account_id, $integrator_id, $integrator_systems_data);
                            }

                            ## save territories
                            $integrator_territories_data = convert_to_array($integrator_data['integrator_territories']);
                            if (( !empty($integrator_territories_data) )) {
                                $save_integrator_territories = $this->save_integrator_territories($account_id, $integrator_id, $integrator_territories_data);
                            }

                            $result         = ( !empty($integrator_id) ) ? $this->db->get_where("integrator", ["account_id" => $account_id, "system_integrator_id" => $integrator_id ])->row() : false ;

                            if (!empty($result)) {
                                $this->session->set_flashdata('message', 'The System integrator has been created');
                            } else {
                                $this->session->set_flashdata('message', 'The System integrator hasn\'t been created');
                            }
                        } else {
                            $this->session->set_flashdata('message', 'There was an error adding an Integrator');
                        }
                    } else {
                        $this->session->set_flashdata('message', 'There was an error processing the Integrator Details');
                    }
                } else {
                    $this->session->set_flashdata('message', 'Integrator Data is required');
                }
            } else {
                $this->session->set_flashdata('message', 'Integrator name is required');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id supplied.');
        }
        return $result;
    }


    /*
    *    Save the Integrator Address
    */
    public function save_integrator_address($account_id = false, $integrator_id = false, $integrator_address_data = false, $address_type_id = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($integrator_address_data)) {
            $integrator_address_data = convert_to_array($integrator_address_data);

            foreach ($integrator_address_data as $key => $value) {
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
                $data['account_id']         = $account_id;
                $data['created_by']         = $this->ion_auth->_current_user->id;
                $data['integrator_id']      = $integrator_id;
                $data['address_type_id']    = $address_type_id;
                $address_data               = $this->ssid_common->_filter_data('integrator_address', $data);
                $this->db->insert('integrator_address', $address_data);
                if ($this->db->affected_rows() > 0) {
                    $address_id = $this->db->insert_id();
                    $result     = $this->db->get_where('integrator_address', ["address_id" => $address_id ])->row();
                }
            }
        }

        return $result;
    }


    /*
    *    Save the Integrator Systems
    */
    public function save_integrator_systems($account_id = false, $integrator_id = false, $systems = [])
    {
        $result = false;

        if (!empty($account_id) && !empty($integrator_id) && !empty($systems)) {
            $systems        = ( !( is_array($systems) ) ) ? convert_to_array($systems) : $systems ;
            $batch_data     = [];

            ## cleaning and sanitizing data
            $systems = array_unique(array_filter($systems), SORT_NUMERIC);

            $i = 0;
            foreach ($systems as $system_id) {
                if (!empty($system_id)) {
                    $batch_data[$i]['account_id']   = $account_id;
                    $batch_data[$i]['integrator_id']    = $integrator_id;
                    $batch_data[$i]['system_id']        = $system_id;
                    $batch_data[$i]['created_by']   = $this->ion_auth->_current_user->id;
                }
                $i++;
            }

            if (!empty($batch_data)) {
                $this->db->insert_batch('integrator_systems', $batch_data);

                if ($this->db->affected_rows() > 0) {
                    $integrator_system_id   = $this->db->insert_id();
                    $result                 = $this->db->get_where("integrator_systems", ["integrator_system_id" => $integrator_system_id ])->row();
                    $batch_message          = "Total inserts: " . ( $this->db->affected_rows() );
                    $this->session->set_flashdata('message', $batch_message);
                } else {
                    $this->session->set_flashdata('message', "No Data inserted");
                }
            }
        } else {
            $this->session->set_flashdata('message', 'Required value is missing: Account ID, Integrator ID or Systems data.');
        }

        return $result;
    }


    /*
    *    Save the Integrator Systems
    */
    public function save_integrator_territories($account_id = false, $integrator_id = false, $territories = [])
    {
        $result = false;

        if (!empty($account_id) && !empty($integrator_id) && !empty($territories)) {
            $territories        = ( !( is_array($territories) ) ) ? convert_to_array($territories) : $territories ;
            $batch_data     = [];

            ## cleaning and sanitizing data
            $territories = array_unique(array_filter($territories), SORT_NUMERIC);

            $i = 0;
            foreach ($territories as $territory_id) {
                if (!empty($territory_id)) {
                    $batch_data[$i]['account_id']       = $account_id;
                    $batch_data[$i]['integrator_id']    = $integrator_id;
                    $batch_data[$i]['territory_id']     = $territory_id;
                    $batch_data[$i]['created_by']       = $this->ion_auth->_current_user->id;
                }
                $i++;
            }

            if (!empty($batch_data)) {
                $this->db->insert_batch('integrator_territories', $batch_data);

                if ($this->db->affected_rows() > 0) {
                    $integrator_territory_id    = $this->db->insert_id();
                    $result                 = $this->db->get_where("integrator_territories", ["integrator_territory_id" => $integrator_territory_id ])->row();
                    $batch_message          = "Total inserts: " . ( $this->db->affected_rows() );
                    $this->session->set_flashdata('message', $batch_message);
                } else {
                    $this->session->set_flashdata('message', "No Data inserted");
                }
            }
        } else {
            $this->session->set_flashdata('message', 'Required value is missing: Account ID, Integrator ID or Territories data.');
        }

        return $result;
    }


    public function get_integrator($account_id = false, $integrator_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select("i.*", false);
            $this->db->select("invoice_currency.setting_value `invoice_currency_name`", false);
            $this->db->select("CONCAT( u1.first_name, ' ',u1.last_name ) `created_by_full_name`", false);
            $this->db->select("CONCAT( u2.first_name, ' ',u2.last_name ) `modified_by_full_name`", false);

            $this->db->join("setting `invoice_currency`", "invoice_currency.setting_id = i.invoice_currency_id", "left");
            $this->db->join("user `u1`", "u1.id = i.created_by", "left");
            $this->db->join("user `u2`", "u2.id = i.modified_by", "left");

            $this->db->where("i.active", 1);

            $arch_where = "( i.archived != 1 or i.archived is NULL )";
            $this->db->where($arch_where);

            $this->db->where("i.account_id", $account_id);

            $this->db->order_by("i.integrator_name ASC");

            if (!empty($integrator_id)) {
                $this->db->where("i.system_integrator_id", $integrator_id);
            }

            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where['integrator_status'])) {
                    $integrator_status = $where['integrator_status'];
                    $this->db->where("integrator_status", $integrator_status);
                    unset($where['integrator_status']);
                }


                $this->db->where($where);
            }

            $query = $this->db->get("integrator `i`");

            if (!empty($query->num_rows() && $query->num_rows() > 0)) {
                $dataset = $query->result();
                ## get additional informations
                if (!empty($integrator_id)) {
                    $result = $dataset[0];
                    $result->addresses      = $this->get_integrator_addresses($account_id, $integrator_id);
                    $result->systems        = $this->get_integrator_systems($account_id, $integrator_id);
                    $result->territories    = $this->get_integrator_territories($account_id, $integrator_id);
                } else {
                    foreach ($dataset as $row) {
                        $result[$row->system_integrator_id] = $row;
                        $result[$row->system_integrator_id]->addresses  = $this->get_integrator_addresses($account_id, $integrator_id);
                        $result[$row->system_integrator_id]->systems        = $this->get_integrator_systems($account_id, $integrator_id);
                        $result[$row->system_integrator_id]->territories    = $this->get_integrator_territories($account_id, $integrator_id);
                    }
                }

                $this->session->set_flashdata('message', 'Integrator(s) data found.');
            } else {
                $this->session->set_flashdata('message', 'Integrator(s) data not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID not supplied.');
        }

        return $result;
    }

    public function get_integrator_addresses($account_id = false, $integrator_id = false, $address_id = false, $where = false)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select("ia.*", false);
            $this->db->select("ct.country", false);
            $this->db->select("at.address_type", false);

            if (!empty($address_id)) {
                $this->db->where("ia.address_id", $address_id);
            }

            if (!empty($integrator_id)) {
                $this->db->where("ia.integrator_id", $integrator_id);
            }

            $this->db->where("ia.active", 1);
            $arch_where = "( ia.archived != 1 or ia.archived is NULL )";
            $this->db->where($arch_where);


            $this->db->where("ia.account_id", $account_id);

            $where = ( !is_array($where) ) ? convert_to_array($where) : $where ;

            $this->db->join("content_territory `ct`", "ct.territory_id = ia.integrator_territory_id", "left");
            $this->db->join("address_types `at`", "at.address_type_id = ia.address_type_id", "left");

            if (!empty($where)) {
                $this->db->where($where);
            }

            $query = $this->db->get("integrator_address `ia`");

            if ($query->num_rows() > 0) {
                $dataset = $query->result();
                if (!empty($dataset)) {
                    foreach ($dataset as $row) {
                        $result[$row->address_type_id][] = $row;
                    }
                }
                $this->session->set_flashdata('message', 'Integrator addresses data found.');
            } else {
                $this->session->set_flashdata('message', 'Integrator addresses data not found.');
            }
        }

        return $result;
    }


    public function get_integrator_systems($account_id = false, $integrator_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($integrator_id)) {
            $this->db->select("isys.*", false);
            $this->db->select("pst.name `product_system_type_name`", false);

            $this->db->join("product_system_type `pst`", "pst.system_type_id = isys.system_id", "left");

            $this->db->where("isys.active", 1);
            $arch_where = "( isys.archived != 1 or isys.archived is NULL )";
            $this->db->where($arch_where);

            $this->db->where("isys.integrator_id", $integrator_id);
            $this->db->where("isys.account_id", $account_id);

            $query = $this->db->get("integrator_systems `isys`");

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Integrator systems data found.');
            } else {
                $this->session->set_flashdata('message', 'Integrator systems data not found.');
            }
        }
        return $result;
    }

    public function get_integrator_territories($account_id = false, $integrator_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        $where          = convert_to_array($where);
        $integrator_id  = !empty($integrator_id) ? $integrator_id : ( !empty($where['integrator_id']) ? $where['integrator_id'] : false );

        if (!empty($account_id) && !empty($integrator_id)) {
            $this->db->select("it.*", false);
            $this->db->select("ct.country, ct.country `territory_name`", false);

            $this->db->join("content_territory `ct`", "ct.territory_id = it.territory_id", "left");

            $this->db->where("it.active", 1);
            $arch_where = "( it.archived != 1 or it.archived is NULL )";
            $this->db->where($arch_where);

            $this->db->where("it.integrator_id", $integrator_id);
            $this->db->where("it.account_id", $account_id);

            $this->db->order_by("ct.country ASC");

            $query = $this->db->get("integrator_territories `it`");

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Integrator Territories data found.');
            } else {
                $this->session->set_flashdata('message', 'Integrator Territories data not found.');
            }
        }
        return $result;
    }


    /*
    *   Integrator Lookup
    */
    public function integrator_lookup($account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select("i.*", false);
            $this->db->select("invoice_currency.setting_value `invoice_currency_name`", false);
            $this->db->select("CONCAT( u1.first_name, ' ',u1.last_name ) `created_by_full_name`", false);
            $this->db->select("CONCAT( u2.first_name, ' ',u2.last_name ) `modified_by_full_name`", false);

            $this->db->join("setting `invoice_currency`", "invoice_currency.setting_id = i.invoice_currency_id", "left");
            $this->db->join("user `u1`", "u1.id = i.created_by", "left");
            $this->db->join("user `u2`", "u2.id = i.modified_by", "left");

            $this->db->where('i.account_id', $account_id);

            $arch_where = "( i.archived != 1 or i.archived is NULL )";
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
                $this->db->where($where);
            }

            if ($order_by) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('i.integrator_name ASC');
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->get('integrator i');

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Records found.');
            } else {
                $this->session->set_flashdata('message', 'No records found matching your criteria.');
            }
        }

        return $result;
    }


    public function get_total_integrator($account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select("i.*", false);
            $this->db->select("invoice_currency.setting_value `invoice_currency_name`", false);
            $this->db->select("CONCAT( u1.first_name, ' ',u1.last_name ) `created_by_full_name`", false);
            $this->db->select("CONCAT( u2.first_name, ' ',u2.last_name ) `modified_by_full_name`", false);

            $this->db->join("setting `invoice_currency`", "invoice_currency.setting_id = i.invoice_currency_id", "left");
            $this->db->join("user `u1`", "u1.id = i.created_by", "left");
            $this->db->join("user `u2`", "u2.id = i.modified_by", "left");

            $this->db->where('i.account_id', $account_id);

            $arch_where = "( i.archived != 1 or i.archived is NULL )";
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
                $this->db->where($where);
            }

            $query = $this->db->from('integrator i')->count_all_results();

            $results['total'] = !empty($query) ? $query : 0;
            $results['pages'] = !empty($query) ? ceil($query / ( ( $limit > 0 ) ? $limit : DEFAULT_LIMIT  )) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }


    /*
    *   Update integrator
    */
    public function update_integrator($account_id = false, $integrator_id = false, $integrator_data = false)
    {
        $result = false;
        if (!empty($account_id)  && !empty($integrator_id) && ( !empty($integrator_data) )) {
            $data = [];
            $integrator_data = json_decode($integrator_data);

            if (!empty($integrator_data)) {
                foreach ($integrator_data as $key => $value) {
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
                    $data['modified_by']            = $this->ion_auth->_current_user->id;
                    $u_integrator_data              = $this->ssid_common->_filter_data('integrator', $data);

                    $this->db->update('integrator', $u_integrator_data, ["system_integrator_id" => $integrator_id, "account_id" => $account_id]);

                    if ($this->db->affected_rows() > 0) {
                        $result = $this->get_integrator($account_id, $integrator_id);
                        $this->session->set_flashdata('message', 'Integrator record updated successfully.');
                    } else {
                        $this->session->set_flashdata('message', 'Integrator record hasn\'t been updated.');
                    }
                }
            } else {
                $this->session->set_flashdata('message', 'There was an error processing the Integrator Data');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id or Integrator Data supplied.');
        }
        return $result;
    }


    /*
    *   Delete integrator
    */
    public function delete_integrator($account_id = false, $integrator_id = false)
    {
        $result = false;
        if (!empty($account_id)  && !empty($integrator_id)) {
            $data = [
                "archived"      => 1,
                "active"        => 0,
                "modified_by"   => $this->ion_auth->_current_user->id,
            ];

            $d_integrator_data  = $this->ssid_common->_filter_data('integrator', $data);
            $this->db->update('integrator', $d_integrator_data, ["system_integrator_id" => $integrator_id, "account_id" => $account_id]);

            if ($this->db->affected_rows()) {
                $result = true;
                $this->session->set_flashdata('message', 'Integrator record has been deleted.');
            } else {
                $this->session->set_flashdata('message', 'Integrator record hasn\'t been deleted.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id or Integrator ID supplied.');
        }
        return $result;
    }



    /*
    *   Update integrator address
    */
    public function update_address($account_id = false, $address_id = false, $address_data = false)
    {
        $result = false;
        if (!empty($account_id)  && !empty($address_id) && ( !empty($address_data) )) {
            $data = [];
            $address_data = json_decode($address_data);

            if (!empty($address_data)) {
                foreach ($address_data as $key => $value) {
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
                    $data['last_modified_by']       = $this->ion_auth->_current_user->id;
                    $u_address_data                 = $this->ssid_common->_filter_data('integrator_address', $data);

                    $this->db->update('integrator_address', $u_address_data, ["address_id" => $address_id, "account_id" => $account_id]);

                    if ($this->db->affected_rows() > 0) {
                        $result = $this->get_integrator_addresses($account_id, false, $address_id);
                        $this->session->set_flashdata('message', 'Address record updated successfully.');
                    } else {
                        $this->session->set_flashdata('message', 'Address record hasn\'t been updated.');
                    }
                }
            } else {
                $this->session->set_flashdata('message', 'There was an error processing the Address Data');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id or Address Data supplied.');
        }
        return $result;
    }


    public function get_territories($account_id = false, $territory_id = false, $where = false, $unorganized = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where['integrator_id']) && !empty($where['not_added']) && ( $where['not_added'] == 'yes' )) {
                    $integrator_id = $where['integrator_id'];

                    ## already added clearance territory ids
                    $this->db->select("territory_id");
                    $this->db->where("account_id", $account_id);
                    $this->db->where("integrator_id", $integrator_id);
                    $this->db->where("active", 1);
                    $arch_where = "( archived != 1 OR archived is NULL )";
                    $this->db->where($arch_where);

                    $added_territories = $this->db->get("integrator_territories")->result_array();

                    $added_territories_array = array_column($added_territories, "territory_id");

                    if (!empty($added_territories_array)) {
                        $this->db->where_not_in("territory_id", $added_territories_array);
                    }

                    unset($where['integrator_id']);
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



    public function add_territory($account_id = false, $integrator_id = false, $territories = false)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($integrator_id)) {
                if (!empty($territories)) {
                    $territories    = json_decode($territories);

                    $i = 0;
                    if (is_array($territories)) {
                        foreach ($territories as $key => $territory_id) {
                            $batch_data[$i]['account_id']               = $account_id;
                            $batch_data[$i]['territory_id']             = $territory_id;
                            $batch_data[$i]['integrator_id']            = $integrator_id;
                            $batch_data[$i]['created_by']               = $this->ion_auth->_current_user->id;
                            $i++;
                        }
                    } else {
                        $batch_data[0]['account_id']                    = $account_id;
                        $batch_data[0]['territory_id']                  = $territories;
                        $batch_data[0]['integrator_id']                 = $integrator_id;
                        $batch_data[0]['created_by']                    = $this->ion_auth->_current_user->id;
                    }

                    $this->db->insert_batch("integrator_territories", $batch_data);

                    if ($this->db->affected_rows() > 0) {
                        $insert_id  = $this->db->insert_id();
                        $result     = $this->db->get_where("integrator_territories", ["account_id" => $account_id, "integrator_territory_id" => $insert_id])->row();
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

            $d_territory    = $this->ssid_common->_filter_data('integrator_territories', $data);
            $this->db->update('integrator_territories', $d_territory, ["territory_id" => $territory_id, "account_id" => $account_id]);

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


    /*
    *   Add System(s) to the integrator
    */
    public function add_system($account_id = false, $integrator_id = false, $integrator_systems = false)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($integrator_id)) {
                if (!empty($integrator_systems)) {
                    $integrator_systems     = json_decode($integrator_systems);

                    $i = 0;
                    if (is_array($integrator_systems)) {
                        foreach ($integrator_systems as $key => $system_id) {
                            $batch_data[$i]['account_id']               = $account_id;
                            $batch_data[$i]['system_id']                = $system_id;
                            $batch_data[$i]['integrator_id']            = $integrator_id;
                            $batch_data[$i]['created_by']               = $this->ion_auth->_current_user->id;
                            $i++;
                        }
                    } else {
                        $batch_data[0]['account_id']                    = $account_id;
                        $batch_data[0]['system_id']                     = $integrator_systems;
                        $batch_data[0]['integrator_id']                 = $integrator_id;
                        $batch_data[0]['created_by']                    = $this->ion_auth->_current_user->id;
                    }

                    $this->db->insert_batch("integrator_systems", $batch_data);

                    if ($this->db->affected_rows() > 0) {
                        $insert_id  = $this->db->insert_id();
                        $result     = $this->db->get_where("integrator_systems", ["account_id" => $account_id, "integrator_system_id" => $insert_id])->row();
                        $this->session->set_flashdata('message', 'System(s) added');
                    } else {
                        $this->session->set_flashdata('message', 'There was an error processing your request');
                    }
                } else {
                    $this->session->set_flashdata('message', 'No System(s) supplied.');
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
    *   Delete System
    */
    public function delete_system($account_id = false, $system_id = false)
    {
        $result = false;
        if (!empty($account_id)  && !empty($system_id)) {
            $data = [
                "archived"      => 1,
                "active"        => 0,
                "modified_by"   => $this->ion_auth->_current_user->id,
            ];

            $d_system   = $this->ssid_common->_filter_data('integrator_systems', $data);
            $this->db->update('integrator_systems', $d_system, ["system_id" => $system_id, "account_id" => $account_id]);

            if ($this->db->affected_rows() > 0) {
                $result = true;
                $this->session->set_flashdata('message', 'System has been deleted.');
            } else {
                $this->session->set_flashdata('message', 'System hasn\'t been deleted.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id or Integrator ID supplied.');
        }
        return $result;
    }



    public function get_systems($account_id = false, $system_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where['integrator_id']) && !empty($where['not_added']) && ( $where['not_added'] == 'yes' )) {
                    $integrator_id = $where['integrator_id'];

                    ## already added system ids
                    $this->db->select("system_id");
                    $this->db->where("account_id", $account_id);
                    $this->db->where("integrator_id", $integrator_id);
                    $this->db->where("active", 1);
                    $arch_where     = "( archived != 1 OR archived is NULL )";
                    $this->db->where($arch_where);

                    $added_systems  = $this->db->get("integrator_systems")->result_array();

                    $added_systems_array = array_column($added_systems, "system_id");

                    if (!empty($added_systems_array)) {
                        $this->db->where_not_in("system_type_id", $added_systems_array);
                    }

                    unset($where['integrator_id']);
                    unset($where['not_added']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $this->db->select("product_system_type.*", false);

            if (!empty($system_id)) {
                $this->db->where("system_type_id", $system_id);
            }

            $arch_where = "( product_system_type.archived != 1 or product_system_type.archived is NULL )";
            $this->db->where($arch_where);
            $this->db->where("product_system_type.active", 1);
            $this->db->order_by("product_system_type.name ASC");

            $query = $this->db->get("product_system_type");

            if (!empty($query->num_rows() && $query->num_rows() > 0)) {
                $dataset = $query->result();

                foreach ($dataset as $row) {
                    $result[$row->system_type_id] = $row;
                }
                $this->session->set_flashdata('message', 'System(s) found.');
            } else {
                $this->session->set_flashdata('message', 'System(s) not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID not supplied.');
        }

        return $result;
    }


    /*
    *   Function to disable the integrator with given date
    */
    public function disable_integrator($account_id = false, $integrator_id = false, $disable_date = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($integrator_id) && !empty($disable_date)) {
            $todays_date        = date('Y-m-d');
            $disable_date       = date('Y-m-d', strtotime(format_date_db($disable_date)));
            $change_status_now  = false;
            $message            = "";

            if (!empty($disable_date)) {
                if ($disable_date <= $todays_date) {
                    $change_status_now = true;
                }

                ## Add a disable date into the integrator table
                $upd_data = [
                    "modified_by"       => ( isset($this->ion_auth->_current_user->id) && !empty($this->ion_auth->_current_user->id) ) ? $this->ion_auth->_current_user->id : 1 ,
                    "disable_date"      => $disable_date,
                ];

                ## Change the status if needed
                if ($change_status_now) {
                    $upd_data['integrator_status'] = 'disabled'; ## - integrator is disabled
                }
                $this->db->update("integrator", $upd_data, ["account_id" => $account_id, "system_integrator_id" => $integrator_id]);

                if ($this->db->trans_status() !== false) {
                    $result = $this->get_integrator($account_id, $integrator_id);
                }

                $this->db->select("site_id");
                $linked_sites       = $this->db->get_where("site", ["account_id" => $account_id, "system_integrator_id" => $integrator_id])->result_array();
                $linked_sites_arr   = array_column($linked_sites, "site_id");

                if (!empty($linked_sites_arr)) {
                    foreach ($linked_sites_arr as $site_id) {
                        $disabled_site  = false;
                        $disabled_site  = $this->site_service->disable_site($account_id, $site_id, $disable_date);
                        if (!empty($disabled_site)) {
                            $sites_disabled[]       = $disabled_site->site_id;
                        } else {
                            $sites_not_disabled[]   = $site_id;
                        }
                    }

                    if (count($sites_disabled) == count($linked_sites)) {
                        $message    = "All linked sites have been disabled.";
                    } else {
                        $message    = "Some sites hasn't been changed: " . ( implode(",", $sites_not_disabled) );
                    }
                }

                if (!empty($result)) {
                    $this->session->set_flashdata('message', 'The Integrator has been disabled. ' . $message);
                } else {
                    $this->session->set_flashdata('message', 'There was an error processing your request');
                }
            } else {
                $this->session->set_flashdata('message', 'Disable date hasn\'t been provided or is incorrect');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id or Site ID or disable Date supplied');
        }

        return $result;
    }


    /*
    *   Function to disable the integrator automatically based on the disable date
    */
    public function automated_integrator_disabling()
    {

        $todays_date        = date('Y-m-d');
        $account_id         = 1;
        $integrators_2b_disabled = $this->db->get_where("integrator", ["disable_date<=" => $todays_date, "integrator_status" => "active"  ])->result();
        $disabled_integrators = [];

        if (!empty($integrators_2b_disabled)) {
            foreach ($integrators_2b_disabled as $integrator_row) {
                if ($this->disable_integrator($account_id, $integrator_row->system_integrator_id, $todays_date)) {
                    $disabled_integrators[] = $integrator_row->system_integrator_id;
                }
            }
        }

        return true;
    }
}
