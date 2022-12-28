<?php

namespace Application\Modules\Service\Models;

use System\Core\CI_Model;

class Channel_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $section            = explode("/", $_SERVER["SCRIPT_NAME"]);

        if (empty($section) || ( !( is_array($section) ) )) {
            $section = getcwd();
        }
        $this->section      = $section;
        $this->app_root     = $_SERVER["DOCUMENT_ROOT"] . "/" . $section[1] . "/";
        $this->app_root     = str_replace('/index.php', '', $this->app_root);
        $this->load->library('upload');

        $this->searchable_fields = ["ch.channel_name", "cp.provider_name"];
    }

    /*
    *   To create an channel from the post data
    */
    public function create($account_id = false, $channel_name = false, $channel_data = false)
    {
        $result = false;
        if (!empty($account_id)) {
            if (!empty($channel_name)) {
                if (!empty($channel_data)) {
                    $channel_details = convert_to_array($channel_data);

                    if (!empty($channel_details['channel_territories'])) {
                        $channel_territories = $channel_details['channel_territories'];
                        unset($channel_details['channel_territories']);
                    }

                    if (!empty($channel_details)) {
                        $data = [];
                        foreach ($channel_details as $key => $value) {
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

                        $filtered_data = $this->ssid_common->_filter_data('channel', $data);
                        $this->db->insert('channel', $filtered_data);

                        if ($this->db->affected_rows() > 0) {
                            $channel_id     = !empty($this->db->insert_id()) ? $this->db->insert_id() : false ;

                            if (!empty($channel_id) && !empty($channel_territories)) {
                                ## save territories
                                $channel_territories_data = convert_to_array($channel_territories);
                                if (( !empty($channel_territories_data) )) {
                                    $save_channel_territories = $this->save_channel_territories($account_id, $channel_id, $channel_territories_data);
                                }
                            }

                            ## $result      = ( !empty( $channel_id ) ) ? $this->db->get_where( "channel", ["account_id" => $account_id, "channel_id" => $channel_id ] )->row() : false ;
                            $result         = ( !empty($channel_id) ) ? $this->get_channel($account_id, ["channel_id" => $channel_id ]) : false ;
                            ## territories

                            if (!empty($result)) {
                                $this->session->set_flashdata('message', 'The Channel has been created');
                            } else {
                                $this->session->set_flashdata('message', 'The Channel hasn\'t been created');
                            }
                        } else {
                            $this->session->set_flashdata('message', 'There was an error adding an Channel');
                        }
                    } else {
                        $this->session->set_flashdata('message', 'There was an error processing the Channel Details');
                    }
                } else {
                    $this->session->set_flashdata('message', 'Channel Data is required');
                }
            } else {
                $this->session->set_flashdata('message', 'Channel name is required');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id supplied.');
        }
        return $result;
    }


    /*
    *    Save the Channel Territories
    */
    public function save_channel_territories($account_id = false, $channel_id = false, $territories = [])
    {
        $result = false;

        if (!empty($account_id) && !empty($channel_id) && !empty($territories)) {
            $territories        = ( !( is_array($territories) ) ) ? convert_to_array($territories) : $territories ;
            $batch_data     = [];

            ## cleaning and sanitizing data
            $territories = array_unique(array_filter($territories), SORT_NUMERIC);

            $i = 0;
            foreach ($territories as $territory_id) {
                if (!empty($territory_id)) {
                    $batch_data[$i]['account_id']       = $account_id;
                    $batch_data[$i]['channel_id']       = $channel_id;
                    $batch_data[$i]['territory_id']     = $territory_id;
                    $batch_data[$i]['created_by']       = $this->ion_auth->_current_user->id;
                }
                $i++;
            }

            if (!empty($batch_data)) {
                $this->db->insert_batch('channel_territories', $batch_data);

                if ($this->db->affected_rows() > 0) {
                    $channel_territory_id   = $this->db->insert_id();
                    $result                 = $this->db->get_where("channel_territories", ["channel_territory_id" => $channel_territory_id ])->row();
                    $batch_message          = "Total inserts: " . ( $this->db->affected_rows() );
                    $this->session->set_flashdata('message', $batch_message);
                } else {
                    $this->session->set_flashdata('message', "No Data inserted");
                }
            }
        } else {
            $this->session->set_flashdata('message', 'Required value is missing: Account ID, Channel ID or Territories data.');
        }

        return $result;
    }


    public function get_channel($account_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = $channel_id = false;

        if (!empty($account_id)) {
            $this->db->select("ch.*", false);
            $this->db->select("cp.provider_name, cp.provider_reference_code", false);
            $this->db->select("CONCAT( u1.first_name, ' ',u1.last_name ) `created_by_full_name`", false);
            $this->db->select("CONCAT( u2.first_name, ' ',u2.last_name ) `modified_by_full_name`", false);

            $this->db->join("content_provider `cp`", "cp.provider_id = ch.provider_id", "left");
            $this->db->join("user `u1`", "u1.id = ch.created_by", "left");
            $this->db->join("user `u2`", "u2.id = ch.modified_by", "left");

            $this->db->where("ch.active", 1);

            $arch_where = "( ch.archived != 1 or ch.archived is NULL )";
            $this->db->where($arch_where);

            $this->db->where("ch.account_id", $account_id);

            $this->db->order_by("ch.channel_name ASC");

            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where)) {
                    if (!empty($where['provider_id'])) {
                        $provider_id = $where['provider_id'];
                        $this->db->where("ch.provider_id", $provider_id);
                        unset($where['provider_id']);
                    }

                    if (!empty($where['channel_id'])) {
                        $channel_id = $where['channel_id'];
                        $this->db->where_in("ch.channel_id", $channel_id);
                        unset($where['channel_id']);
                    }

                    if (!empty($where)) {
                        $this->db->where($where);
                    }
                }
            }

            $query = $this->db->get("channel `ch`");

            if (!empty($query->num_rows() && $query->num_rows() > 0)) {
                $dataset = $query->result();
                ## get additional informations
                if (!empty($channel_id)) {
                    $result = $dataset[0];
                    $result->territories    = $this->get_channel_territories($account_id, ["channel_id" => $channel_id]);
                } else {
                    foreach ($dataset as $row) {
                        $result[$row->channel_id] = $row;
                        $result[$row->channel_id]->territories  = $this->get_channel_territories($account_id, ["channel_id" => $row->channel_id]);
                    }
                }

                $this->session->set_flashdata('message', 'Channel(s) data found.');
            } else {
                $this->session->set_flashdata('message', 'Channel(s) data not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID not supplied.');
        }

        return $result;
    }


    public function get_channel_territories($account_id = false, $where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($where)) {
                $where  = convert_to_array($where);

                if (!empty($where)) {
                    if (!empty($where['channel_territory_id'])) {
                        $channel_territory_id = $where['channel_territory_id'];
                        $this->db->where("cht.channel_territory_id", $channel_territory_id);
                        unset($where['channel_territory_id']);
                    }

                    if (!empty($where['channel_id'])) {
                        $channel_id = $where['channel_id'];
                        $this->db->where("cht.channel_id", $channel_id);
                        unset($where['channel_id']);
                    }

                    if (!empty($where['territory_id'])) {
                        $territory_id = $where['territory_id'];
                        $this->db->where("cht.territory_id", $territory_id);
                        unset($where['territory_id']);
                    }

                    if (!empty($where)) {
                        $this->db->where($where);
                    }
                }
            }

            $this->db->select("cht.*", false);
            $this->db->select("ct.country, ct.country `territory_name`", false);

            $this->db->join("content_territory `ct`", "ct.territory_id = cht.territory_id", "left");

            $this->db->where("cht.active", 1);
            $arch_where = "( cht.archived != 1 or cht.archived is NULL )";
            $this->db->where($arch_where);

            $this->db->order_by("ct.country ASC");

            $query = $this->db->get("channel_territories `cht`");

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Channel Territories data found.');
            } else {
                $this->session->set_flashdata('message', 'Channel Territories data not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required data.');
        }
        return $result;
    }


    /*
    *   Channel Lookup
    */
    public function channel_lookup($account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select("ch.*", false);
            $this->db->select("CONCAT( u1.first_name, ' ',u1.last_name ) `created_by_full_name`", false);
            $this->db->select("CONCAT( u2.first_name, ' ',u2.last_name ) `modified_by_full_name`", false);
            $this->db->select("cp.provider_name, cp.provider_reference_code", false);

            $this->db->join("user `u1`", "u1.id = ch.created_by", "left");
            $this->db->join("user `u2`", "u2.id = ch.modified_by", "left");
            $this->db->join("content_provider `cp`", "cp.provider_id = ch.provider_id", "left");

            $this->db->where('ch.account_id', $account_id);

            $arch_where = "( ch.archived != 1 or ch.archived is NULL )";
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
                $this->db->order_by('ch.channel_name ASC');
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->get('channel `ch`');

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Records found.');
            } else {
                $this->session->set_flashdata('message', 'No records found matching your criteria.');
            }
        }

        return $result;
    }


    public function get_total_channel($account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select("ch.*", false);
            $this->db->select("CONCAT( u1.first_name, ' ',u1.last_name ) `created_by_full_name`", false);
            $this->db->select("CONCAT( u2.first_name, ' ',u2.last_name ) `modified_by_full_name`", false);
            $this->db->select("cp.provider_name, cp.provider_reference_code", false);

            $this->db->join("user `u1`", "u1.id = ch.created_by", "left");
            $this->db->join("user `u2`", "u2.id = ch.modified_by", "left");
            $this->db->join("content_provider `cp`", "cp.provider_id = ch.provider_id", "left");

            $this->db->where('ch.account_id', $account_id);

            $arch_where = "( ch.archived != 1 or ch.archived is NULL )";
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

            $query = $this->db->from('channel `ch`')->count_all_results();

            $results['total'] = !empty($query) ? $query : 0;
            $results['pages'] = !empty($query) ? ceil($query / ( ( $limit > 0 ) ? $limit : DEFAULT_LIMIT  )) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }


    /*
    *   Update channel
    */
    public function update_channel($account_id = false, $channel_id = false, $channel_data = false)
    {
        $result = false;
        if (!empty($account_id)  && !empty($channel_id) && ( !empty($channel_data) )) {
            $data = [];
            $channel_data = json_decode($channel_data);

            if (!empty($channel_data)) {
                foreach ($channel_data as $key => $value) {
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
                    $u_channel_data                 = $this->ssid_common->_filter_data('channel', $data);

                    $this->db->update('channel', $u_channel_data, ["channel_id" => $channel_id, "account_id" => $account_id]);

                    if ($this->db->affected_rows() > 0) {
                        $result = $this->get_channel($account_id, ["channel_id" => $channel_id]);
                        $this->session->set_flashdata('message', 'Channel record updated successfully.');
                    } else {
                        $this->session->set_flashdata('message', 'Channel record hasn\'t been updated.');
                    }
                }
            } else {
                $this->session->set_flashdata('message', 'There was an error processing the Channel Data');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id or Channel Data supplied.');
        }
        return $result;
    }


    /*
    *   Delete channel
    */
    public function delete_channel($account_id = false, $channel_id = false)
    {
        $result = false;
        if (!empty($account_id)  && !empty($channel_id)) {
            $data = [
                "archived"      => 1,
                "active"        => 0,
                "modified_by"   => $this->ion_auth->_current_user->id,
            ];

            $d_channel_data     = $this->ssid_common->_filter_data('channel', $data);
            $this->db->update('channel', $d_channel_data, ["channel_id" => $channel_id, "account_id" => $account_id]);

            if ($this->db->affected_rows()) {
                $result = true;
                $this->session->set_flashdata('message', 'Channel record has been deleted.');
            } else {
                $this->session->set_flashdata('message', 'Channel record hasn\'t been deleted.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id or Channel ID supplied.');
        }
        return $result;
    }



    public function get_territories($account_id = false, $territory_id = false, $where = false, $unorganized = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where['channel_id']) && !empty($where['not_added']) && ( $where['not_added'] == 'yes' )) {
                    $channel_id = $where['channel_id'];

                    ## already added clearance territory ids
                    $this->db->select("territory_id");
                    $this->db->where("account_id", $account_id);
                    $this->db->where("channel_id", $channel_id);
                    $this->db->where("active", 1);
                    $arch_where = "( archived != 1 OR archived is NULL )";
                    $this->db->where($arch_where);

                    $added_territories = $this->db->get("channel_territories")->result_array();

                    $added_territories_array = array_column($added_territories, "territory_id");

                    if (!empty($added_territories_array)) {
                        $this->db->where_not_in("territory_id", $added_territories_array);
                    }

                    unset($where['channel_id']);
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



    public function add_territory($account_id = false, $channel_id = false, $territories = false)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($channel_id)) {
                if (!empty($territories)) {
                    $territories    = json_decode($territories);

                    $i = 0;
                    if (is_array($territories)) {
                        foreach ($territories as $key => $territory_id) {
                            $batch_data[$i]['account_id']               = $account_id;
                            $batch_data[$i]['territory_id']             = $territory_id;
                            $batch_data[$i]['channel_id']               = $channel_id;
                            $batch_data[$i]['created_by']               = $this->ion_auth->_current_user->id;
                            $i++;
                        }
                    } else {
                        $batch_data[0]['account_id']                    = $account_id;
                        $batch_data[0]['territory_id']                  = $territories;
                        $batch_data[0]['channel_id']                    = $channel_id;
                        $batch_data[0]['created_by']                    = $this->ion_auth->_current_user->id;
                    }

                    $this->db->insert_batch("channel_territories", $batch_data);

                    if ($this->db->affected_rows() > 0) {
                        $insert_id  = $this->db->insert_id();
                        $result     = $this->db->get_where("channel_territories", ["account_id" => $account_id, "channel_territory_id" => $insert_id])->row();
                        $this->session->set_flashdata('message', 'Territory(ies) been added');
                    } else {
                        $this->session->set_flashdata('message', 'There was an error processing your request');
                    }
                } else {
                    $this->session->set_flashdata('message', 'No Territory(ies) supplied.');
                }
            } else {
                $this->session->set_flashdata('message', 'No Channel ID supplied.');
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

            $d_territory    = $this->ssid_common->_filter_data('channel_territories', $data);
            $this->db->update('channel_territories', $d_territory, ["territory_id" => $territory_id, "account_id" => $account_id]);

            if ($this->db->affected_rows() > 0) {
                $result = true;
                $this->session->set_flashdata('message', 'Territory has been deleted.');
            } else {
                $this->session->set_flashdata('message', 'Territory hasn\'t been deleted.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id or Channel ID supplied.');
        }
        return $result;
    }
}
