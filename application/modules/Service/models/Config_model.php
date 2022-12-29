<?php

namespace Application\Modules\Service\Models;

class Config_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    private $system_config_data_search_fields = [ 'entry_name', 'entry_group', 'entry_description' ];

    /*
    *	Get list of Config Data Entries
    */
    public function get_config_data($entry_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        $this->db->select('system_config_data.*', false)
            ->where('system_config_data.is_active', 1);

        $where = $raw_where = convert_to_array($where);

        if (!empty($entry_id) || isset($where['entry_id'])) {
            $entry_id	= (!empty($entry_id)) ? $entry_id : $where['entry_id'];
            if (!empty($entry_id)) {
                $row = $this->db->get_where('system_config_data', ['system_config_data.entry_id'=>$entry_id ])->row();

                if (!empty($row)) {
                    $this->session->set_flashdata('message', 'Config Data data found');
                    return $row;
                } else {
                    $this->session->set_flashdata('message', 'Config Data data not found');
                    return false;
                }
            }
            unset($where['entry_id']);
        }

        if (!empty($search_term)) {
            //Check for spaces in the search term
            $search_term  = trim(urldecode($search_term));
            $search_where = [];
            if (strpos($search_term, ' ') !== false) {
                $multiple_terms = explode(' ', $search_term);
                foreach ($multiple_terms as $term) {
                    foreach ($this->system_config_data_search_fields as $k=>$field) {
                        $search_where[$field] = trim($term);
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            } else {
                foreach ($this->system_config_data_search_fields as $k=>$field) {
                    $search_where[$field] = $search_term;
                }

                $where_combo = format_like_to_where($search_where);
                $this->db->where($where_combo);
            }
        }

        if (!empty($order_by)) {
            $this->db->order_by($order_by);
        } else {
            $this->db->order_by('entry_name');
        }

        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get('system_config_data');


        if ($query->num_rows() > 0) {
            $result_data = $query->result();

            $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
            $result->records 			= $result_data;
            $counters 					= $this->get_config_data_totals($search_term, $raw_where, $limit);
            $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
            $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
            $result->counters->limit  	= (!empty($apply_limit)) ? $limit : $result->counters->total;
            $result->counters->offset 	= $offset;

            $this->session->set_flashdata('message', 'Config Data entries found');
        } else {
            $this->session->set_flashdata('message', 'There\'s currently no Config Data entries');
        }

        return $result;
    }


    /** Get list of Config Data Entries **/
    public function get_config_data_totals($search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;

        $this->db->select('system_config_data.*', false)
            ->where('system_config_data.is_active', 1);

        $where = $raw_where = convert_to_array($where);

        if (!empty($search_term)) {
            //Check for spaces in the search term
            $search_term  = trim(urldecode($search_term));
            $search_where = [];
            if (strpos($search_term, ' ') !== false) {
                $multiple_terms = explode(' ', $search_term);
                foreach ($multiple_terms as $term) {
                    foreach ($this->system_config_data_search_fields as $k=>$field) {
                        $search_where[$field] = trim($term);
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            } else {
                foreach ($this->system_config_data_search_fields as $k=>$field) {
                    $search_where[$field] = $search_term;
                }

                $where_combo = format_like_to_where($search_where);
                $this->db->where($where_combo);
            }
        }

        $query 			  = $this->db->from('system_config_data')->count_all_results();
        $results['total'] = !empty($query) ? $query : 0;
        $limit 				= ($limit > 0) ? $limit : $results['total'];
        $results['pages'] = !empty($query) ? ceil($query / $limit) : 0;
        return json_decode(json_encode($results));

        return $result;
    }


    /** Create a new Config Entry record **/
    public function add_config_entry($config_entry_data = false)
    {
        $result = null;

        if (!empty($config_entry_data)) {
            foreach ($config_entry_data as $col => $value) {
                if ($col == 'entry_url_link') {
                    $value = ltrim($value, '\\');
                }
                $data[$col] = $value;
            }

            $check_exists = $this->db->select('system_config_data.*', false)
                ->where('system_config_data.entry_name', $data['entry_name'])
                ->where('system_config_data.entry_url_link', $data['entry_url_link'])
                ->limit(1)
                ->get('system_config_data')
                ->row();

            $data = $this->ssid_common->_filter_data('system_config_data', $data);
            $data = array_map('trim', $data);

            if (!empty($check_exists)) {
                $this->db->where('entry_id', $check_exists->entry_id)
                    ->update('system_config_data', $data);

                $this->session->set_flashdata('message', 'This Config Entry already exists, record has been updated successfully.');
                $result = $check_exists;
            } else {
                $this->db->insert('system_config_data', $data);
                $data['entry_id']	= $this->db->insert_id();
                $data = $this->get_config_data($data['entry_id']);
                $this->session->set_flashdata('message', 'New Config Entry created successfully.');
                $result = $data;
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }

        return $result;
    }


    /** Update an existing Config Entry record **/
    public function update_config_entry($entry_id = false, $update_data = false)
    {
        $result = false;
        if (!empty($entry_id)  && !empty($update_data)) {
            $ref_condition = [ 'account_id'=>$account_id, 'entry_id'=>$entry_id ];
            $update_data   = $this->ssid_common->_data_prepare($update_data);
            $update_data   = $this->ssid_common->_filter_data('system_config_data', $update_data);
            $record_pre_update = $this->db->get_where('system_config_data', [ 'account_id'=>$account_id, 'entry_id'=>$entry_id ])->row();

            if (!empty($record_pre_update)) {
                $check_conflict = $this->db->select('entry_id', false)
                    ->where('system_config_data.account_id', $account_id)
                    ->where('system_config_data.entry_id !=', $entry_id)
                    ->limit(1)
                    ->get('system_config_data')
                    ->row();

                if (!$check_conflict) {
                    $this->db->where($ref_condition)
                        ->update('system_config_data', $update_data);

                    $updated_record = $this->get_config_data($entry_id);
                    $result 		= (!empty($updated_record->records)) ? $updated_record->records : (!empty($updated_record) ? $updated_record : false);

                    $this->session->set_flashdata('message', 'Config Entry profile record updated successfully');
                    return $result;
                } else {
                    $this->session->set_flashdata('message', 'Config Entry profile record updated successfully');
                    return false;
                }
            } else {
                $this->session->set_flashdata('message', 'This Config Entry profile record does not exist or does not belong to you.');
                return false;
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }
        return $result;
    }
}
