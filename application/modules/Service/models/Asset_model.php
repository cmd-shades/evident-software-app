<?php

namespace Application\Modules\Service\Models;

class Asset_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $section 	   = explode("/", $_SERVER["SCRIPT_NAME"]);
        $this->app_root= $_SERVER["DOCUMENT_ROOT"]."/".$section[1]."/";
        $this->app_root= str_replace('/index.php', '', $this->app_root);
        $this->load->model('serviceapp/Document_Handler_model', 'document_service');
    }

    /** Searchable fields **/
    private $searchable_fields  		= ['asset.asset_id', 'asset.asset_unique_id', /*'asset_name', 'asset_make', 'asset_make', 'asset_model', 'asset_imei_number', */'asset.assignee', 'asset.status_id', 'asset.asset_type_id'];
    private $asset_types_search_fields  = ['asset_type', 'asset_group'];
    private $asset_type_attribs_search  = ['attribute_name', 'response_type'];
    private $file_response_types		= ['file','signature'];

    /** Primary table name **/
    private $primary_tbl = 'asset';

    /*
    * Delete Asset record
    */
    public function delete_asset($account_id = false, $asset_id = false)
    {
        $result = false;
        if ($this->account_service->check_account_status($account_id) && !empty($asset_id)) {
            $conditions 	= ['account_id'=>$account_id,'asset_id'=>$asset_id];
            $asset_exists 	= $this->db->get_where('asset', $conditions)->row();
            if (!empty($asset_exists)) {
                $data = ['asset_unique_id'=>strtoupper($asset_exists->asset_unique_id.'_ARC'), 'archived'=>1];
                $this->db->where($conditions)->update('asset', $data);
                if ($this->db->trans_status() !== false) {
                    $this->session->set_flashdata('message', 'Record deleted successfully.');
                    $result = true;
                }
            } else {
                $this->session->set_flashdata('message', 'Invalid Asset ID.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Asset record found.');
        }
        return $result;
    }

    /** Get Asset statuses **/
    public function get_asset_statuses($account_id = false, $status_id = false, $status_group = false)
    {
        $result = null;

        if ($account_id) {
            if (!empty($status_id)) {
                $this->db->where("status_id", $status_id);
            }

            if (!empty($status_group)) {
                $this->db->where("status_group", $status_group);
                $this->db->group_by("status_name");
            }

            $this->db->where('asset_statuses.account_id', $account_id);
            $query = $this->db->where('is_active', 1)->get('asset_statuses');
            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Status Data found.');
            } else {
                $this->session->set_flashdata('message', 'No Data found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }

        return $result;
    }


    /*
    *	Get list of Asset types / searchable
    */
    public function get_asset_types($account_id = false, $asset_type_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select('asset_types.*, audit_categories.category_name, audit_categories.category_group, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`, account_discipline.account_discipline_name, account_discipline.account_discipline_image_url `discipline_image_url`', false)
                ->join('user creater', 'creater.id = asset_types.created_by', 'left')
                ->join('user modifier', 'modifier.id = asset_types.last_modified_by', 'left')
                ->join('audit_categories', 'asset_types.category_id = audit_categories.category_id', 'left')
                ->join('account_discipline', 'account_discipline.discipline_id = asset_types.discipline_id', 'left')
                ->where('asset_types.is_active', 1)
                ->where('asset_types.account_id', $account_id);

            $where = $raw_where = convert_to_array($where);

            if (!empty($asset_type_id) || isset($where['asset_type_id'])) {
                $asset_type_id	= (!empty($asset_type_id)) ? $asset_type_id : $where['asset_type_id'];
                if (!empty($asset_type_id)) {
                    $row = $this->db->get_where('asset_types', ['asset_type_id'=>$asset_type_id ])->row();

                    if (!empty($row)) {
                        $row->linked_asset 	= [];
                        $result = ( object ) ['records'=>$row];
                        $this->session->set_flashdata('message', 'Asset Type data found');
                        return $result;
                    } else {
                        $this->session->set_flashdata('message', 'Asset Type data not found');
                        return false;
                    }
                }
                unset($where['asset_type_id'], $where['asset_type_ref']);
            }

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->asset_type_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->asset_type_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                if (isset($where['grouped'])) {
                    if (!empty($where['grouped'])) {
                        $grouped = 1;
                    }
                    unset($where['grouped'], $raw_where['grouped']);
                }

                if (isset($where['asset_type'])) {
                    if (!empty($where['asset_type'])) {
                        $asset_type_ref = strtoupper(strip_all_whitespace($where['asset_type']));
                        $this->db->where('( asset_types.asset_type = "'.$where['asset_type'].'" OR asset_types.asset_type_ref = "'.$asset_type_ref.'" )');
                    }
                    unset($where['asset_type']);
                }

                if (isset($where['contract_id'])) {
                    if (!empty($where['contract_id'])) {
                        $this->db->where('asset_types.contract_id', $where['contract_id']);
                    }
                    unset($where['contract_id']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            if (!empty($order_by)) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('asset_type, asset_type_id DESC');
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->group_by('asset_types.asset_type_id')
                ->get('asset_types');

            if ($query->num_rows() > 0) {
                #Grouped result
                if (!empty($grouped)) {
                    $data = [];
                    foreach ($query->result() as $k => $row) {
                        $data[$row->category_name][] = $row;
                    }
                    $result_data = $data;
                } else {
                    $result_data = $query->result();
                }

                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $result->records 			= $result_data;
                $counters 					= $this->asset_types_totals($account_id, $search_term, $raw_where, $limit);
                $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
                $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
                $result->counters->limit  	= (!empty($limit > 0)) ? $limit : $result->counters->total;
                $result->counters->offset 	= $offset;

                $this->session->set_flashdata('message', 'Asset Types data found');
            } else {
                $this->session->set_flashdata('message', 'There\'s currently no Asset types data matching your criteria');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }

    /** Get Asset Types lookup counts **/
    public function asset_types_totals($account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('asset_types.asset_type_id', false)
                ->where('asset_types.is_active', 1)
                ->where('asset_types.account_id', $account_id)
                ->group_by('asset_types.asset_type_id', $account_id);

            $where = $raw_where = convert_to_array($where);

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->asset_type_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->asset_type_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                if (isset($where['asset_type'])) {
                    if (!empty($where['asset_type'])) {
                        $asset_type_ref = strtoupper(strip_all_whitespace($where['asset_type']));
                        $this->db->where('( asset_types.asset_type = "'.$where['asset_type'].'" OR asset_types.asset_type_ref = "'.$asset_type_ref.'" )');
                    }
                    unset($where['asset_type']);
                }

                if (isset($where['contract_id'])) {
                    if (!empty($where['contract_id'])) {
                        $this->db->where('asset_types.contract_id', $where['contract_id']);
                    }
                    unset($where['contract_id']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $query 			  = $this->db->from('asset_types')->count_all_results();
            $results['total'] = !empty($query) ? $query : 0;
            $limit 			  = ($limit > 0) ? $limit : $results['total'];
            $results['pages'] = !empty($query) ? ceil($query / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }

    /** Create A New Asset Status **/
    public function add_asset_status($account_id = false, $postdata = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($postdata)) {
            $data = [];
            foreach ($postdata as $col => $value) {
                if ($col == 'status_name') {
                    $data['status_name_ref'] = strtolower(lean_string($value));
                }
                $data[$col] = trim($value);
            }

            $check_exists = $this->db->where('( asset_statuses.status_name = "'.$data['status_name'].'" OR asset_statuses.status_name_ref = "'.$data['status_name_ref'].'" )')
                ->where('asset_statuses.status_group', $data['status_group'])
                ->where('asset_statuses.account_id', $account_id)
                ->where('asset_statuses.is_active', 1)
                ->limit(1)
                ->get('asset_statuses')->row();

            if (!empty($check_exists)) {
                $this->session->set_flashdata('message', 'This Asset Status name already exists, request aborted');
                $result = false;
            } else {
                $data					= $this->ssid_common->_filter_data('asset_statuses', $data);
                $data['created_by'] 	= $this->ion_auth->_current_user->id;
                $this->db->insert('asset_statuses', $data);
                $data['status_id'] 		= $this->db->insert_id();

                $result = $this->db->select('asset_statuses.*', false)
                    ->get_where('asset_statuses', [ 'asset_statuses.account_id' => $account_id, 'asset_statuses.status_id' => $data['status_id'] ])
                    ->row();

                $this->session->set_flashdata('message', 'New Asset Status added successfully.');
            }
        }

        return $result;
    }

    public function create_asset_change_log($account_id=false, $asset_id=false, $log_data = false)
    {
        if ($account_id && $asset_id && $log_data) {
            $data   = $this->ssid_common->_filter_data('asset_change_log', $log_data);
            $data['asset_id']     = $asset_id;
            $data['account_id']   = $account_id;
            $data['created_by']   = $this->ion_auth->_current_user->id;
            $data['log_notes']  = (!empty($log_data['asset_notes'])) ? $log_data['asset_notes'] : null;
            $data['updated_data'] = json_encode($log_data);
            $this->db->insert('asset_change_log', $data);
        }
        return true;
    }

    /** This only returns the most recent 10 logs by default **/
    public function get_asset_change_logs($account_id=false, $asset_id=false, $limit = 10, $offset = 0)
    {
        $result = false;
        $this->db->select('acl.id, acl.current_assignee, acl.previous_assignee, acl.asset_id, acl.account_id, acl.date_created, concat(user.first_name," ",user.last_name) `created_by`,
				concat(current_a.first_name," ",current_a.last_name) `assigned_to`,
				concat(previous_a.first_name," ",previous_a.last_name) `previously_assigned_to`,
				current_s.status_name `current_status`,
				previous_s.status_name `previous_status`,
				current_l.location_name `current_location`,
				previous_l.location_name `previous_location`, acl.updated_data', false)
            ->order_by('acl.id desc')
            ->where('acl.account_id', $account_id)
            ->join('user', 'user.id = acl.created_by', 'left')
            ->join('user current_a', 'current_a.id = acl.current_assignee', 'left')
            ->join('user previous_a', 'previous_a.id = acl.previous_assignee', 'left')
            ->join('asset_statuses current_s', 'current_s.status_id = acl.current_status', 'left')
            ->join('asset_statuses previous_s', 'previous_s.status_id = acl.previous_status', 'left')
            ->join('asset_locations current_l', 'current_l.location_id = acl.current_location', 'left')
            ->join('asset_locations previous_l', 'previous_l.location_id = acl.previous_location', 'left');

        if ($asset_id) {
            $this->db->where('acl.asset_id', $asset_id);
        }

        $this->db->limit($limit, $offset);

        $query = $this->db->order_by('acl.id desc')
            ->get('asset_change_log acl');
        if ($query->num_rows() > 0) {
            $result = $query->result();
        }
        return $result;
    }

    /*
    * Search through asset
    */
    public function asset_lookup($account_id = false, $search_term = false, $asset_statuses = false, $asset_types = false, $asset_categories = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            $where = $raw_where = convert_to_array($where);

            if (isset($where['audit_type_id'])) {
                if ($where['audit_type_id'] > 0) {
                    $qry = $this->db->select('qb.asset_type_id', false)
                        ->where('qb.asset_type_id > 0')
                        ->where('qb.account_id', $account_id)
                        ->where('qb.audit_type_id', $where['audit_type_id'])
                        ->where('qb.is_active', 1)
                        ->group_by('qb.asset_type_id')
                        ->get('audit_question_bank qb');
                    if ($qry->num_rows() > 0) {
                        $asset_type_ids = array_column($qry->result_array(), 'asset_type_id');
                        if (!empty($asset_types) && is_array($asset_types)) {
                            $asset_types = array_merge($asset_types, $asset_type_ids);
                        } else {
                            $asset_types = $asset_type_ids;
                        }
                    }
                }
                unset($where['audit_type_id']);
            }

            #$this->db->select('asset.*, audit_result_statuses.result_status, site.site_name, concat(user.first_name," ",user.last_name) `assigned_to`,asset_statuses.status_name `asset_status`, asset_statuses.status_group `asset_status_group`, asset_types.asset_type, asset_types.asset_group',false)
            $this->db->select('asset.*, ata.attribute_name `primary_attribute`, atr.attribute_value, ata.is_mobile_visible, audit_result_statuses.result_status, site.site_name, site.site_address_id `address_id`, audit_categories.category_id, audit_categories.category_name, concat(user.first_name," ",user.last_name) `assigned_to`, concat(modifier.first_name," ",modifier.last_name) `last_modified_by`, asset_statuses.status_name `asset_status`, asset_statuses.status_group `asset_status_group`, asset_types.asset_type, asset_types.asset_group, site_zones.zone_name, site_locations.location_name', false)
                ->join('asset_types', 'asset_types.asset_type_id = asset.asset_type_id', 'left')
                ->join('asset_type_attributes ata', 'ata.attribute_id = asset_types.primary_attribute_id', 'left')
                ->join('asset_attributes atr', 'atr.attribute_id = ata.attribute_id AND `atr`.`asset_id` = `asset`.`asset_id`', 'left')
                ->join('audit_categories', 'audit_categories.category_id = asset_types.category_id', 'left')
                ->join('user', 'user.id = asset.assignee', 'left')
                ->join('user modifier', 'modifier.id = asset.last_modified_by', 'left')
                ->join('asset_statuses', 'asset_statuses.status_id = asset.status_id', 'left')
                ->join('site', 'site.site_id = asset.site_id', 'left')
                ->join('audit_result_statuses', 'audit_result_statuses.audit_result_status_id = asset.audit_result_status_id', 'left')
                ->join('site_zones', 'site_zones.zone_id = asset.zone_id', 'left')
                ->join('site_locations', 'site_locations.location_id = asset.location_id', 'left')
                ->where('asset.account_id', $account_id)
                ->where('asset.archived !=', 1)
                ->group_by('asset.asset_id');

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->searchable_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        if (!empty($search_where['asset.status_id'])) {
                            $search_where['asset_statuses.status_name'] =  trim($term);
                            unset($search_where['asset.status_id']);
                        }

                        if (!empty($search_where['asset.asset_type_id'])) {
                            $search_where['asset_types.asset_type'] =  trim($term);
                            unset($search_where['asset.asset_type_id']);
                        }

                        if (!empty($search_where['asset.assignee'])) {
                            $search_where['user.first_name'] =  trim($term);
                            $search_where['user.last_name']  =  trim($term);
                            unset($search_where['asset.assignee']);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->searchable_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    if (!empty($search_where['asset.status_id'])) {
                        $search_where['asset_statuses.status_name'] =  $search_term;
                        unset($search_where['asset.status_id']);
                    }

                    if (!empty($search_where['asset.asset_type_id'])) {
                        $search_where['asset_types.asset_type'] =  $search_term;
                        unset($search_where['asset.asset_type_id']);
                    }

                    if (!empty($search_where['asset.assignee'])) {
                        $search_where['user.first_name'] =  $search_term;
                        $search_where['user.last_name']  =  $search_term;
                        unset($search_where['asset.assignee']);
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($asset_types)) {
                $asset_types = convert_to_array($asset_types);
                $this->db->where_in('asset.asset_type_id', $asset_types);
            }

            if (!empty($asset_categories)) {
                $asset_categories = convert_to_array($asset_categories);
                $this->db->where_in('asset_types.category_id', $asset_categories);
            }

            if (!empty($asset_statuses)) {
                $asset_statuses = convert_to_array($asset_statuses);
                $this->db->where_in('asset.status_id', $asset_statuses);
            }

            if ($where) {
                $where = convert_to_array($where);

                if (isset($where['period_days'])) {
                    $group_min = 0;
                    $group_max = 365;
                    switch($where['period_days']) {
                        case 0:
                        case '0':
                            $this->db->where('( DATEDIFF( asset.end_of_life_date, CURDATE() ) < 0 )');
                            break;
                        case ($where['period_days'] > 0 && $where['period_days'] <= 30):
                            $group_min = 0;
                            $group_max = 30;
                            $this->db->where('( ( DATEDIFF( asset.end_of_life_date, CURDATE() ) > '.$group_min.' ) AND ( DATEDIFF( asset.end_of_life_date, CURDATE() ) <= '.$group_max.' ) )');
                            break;
                        case ($where['period_days'] > 30 && $where['period_days'] <= 60):
                            $group_min = 30;
                            $group_max = 60;
                            $this->db->where('( ( DATEDIFF( asset.end_of_life_date, CURDATE() ) > '.$group_min.' ) AND ( DATEDIFF( asset.end_of_life_date, CURDATE() ) <= '.$group_max.' ) )');
                            break;
                        case ($where['period_days'] > 60 && $where['period_days'] <= 90):
                            $group_min = 60;
                            $group_max = 90;
                            $this->db->where('( ( DATEDIFF( asset.end_of_life_date, CURDATE() ) > '.$group_min.' ) AND ( DATEDIFF( asset.end_of_life_date, CURDATE() ) <= '.$group_max.' ) )');
                            break;
                        case ($where['period_days'] > 90 && $where['period_days'] <= 180):
                            $group_min = 90;
                            $group_max = 180;
                            $this->db->where('( ( DATEDIFF( asset.end_of_life_date, CURDATE() ) > '.$group_min.' ) AND ( DATEDIFF( asset.end_of_life_date, CURDATE() ) <= '.$group_max.' ) )');
                            break;
                        case ($where['period_days'] > 0 && $where['period_days'] <= 365):
                            $group_min = 0;
                            $group_max = 365;
                            $this->db->where('( ( ( DATEDIFF( asset.end_of_life_date, CURDATE() ) >= '.$group_min.' ) AND ( DATEDIFF( asset.end_of_life_date, CURDATE() ) <= '.$group_max.' ) ) OR ( DATEDIFF( asset.end_of_life_date, CURDATE() ) < 0 ) )');
                            break;
                        case ($where['period_days'] < 0):
                            $this->db->where('( ( asset.end_of_life_date IS NULL ) OR ( asset.end_of_life_date = "0000-00-00" ) )');
                            break;
                    }
                    unset($where['period_days']);
                }

                if (isset($where['asset_type_id'])) {
                    if (!empty($where['asset_type_id'])) {
                        $this->db->where('asset.asset_type_id', $where['asset_type_id']);
                    }
                    unset($where['asset_type_id']);
                }

                if (isset($where['location_id'])) {
                    if (!empty($where['location_id'])) {
                        $this->db->where('asset.location_id', $where['location_id']);
                    }
                    unset($where['location_id']);
                }

                if (isset($where['assignee'])) {
                    if (!empty($where['assignee'])) {
                        $this->db->where('asset.assignee', $where['assignee']);
                    }
                    unset($where['assignee']);
                }

                if (isset($where['audit_result_status_id'])) {
                    if (!empty($where['audit_result_status_id'])) {
                        $this->db->where('asset.audit_result_status_id', $where['audit_result_status_id']);
                    }
                    unset($where['audit_result_status_id']);
                }

                if (isset($where['discipline_id'])) {
                    if (!empty($where['discipline_id'])) {
                        $this->db->where('asset_types.discipline_id', $where['discipline_id']);
                    }
                    unset($where['discipline_id']);
                }

                if (isset($where['site_id'])) {
                    if (!empty($where['site_id'])) {
                        $this->db->where('asset.site_id', $where['site_id']);
                    }
                    unset($where['site_id']);
                }

                if (!empty($where)) {
                    //$this->db->where( $where );
                }
            }

            if ($order_by) {
                $order = $this->ssid_common->_clean_order_by($order_by, $this->primary_tbl);
                if (!empty($order)) {
                    $this->db->order_by($order);
                }
            } else {
                $this->db->order_by('asset.asset_id desc');
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->get('asset');

            if ($query->num_rows() > 0) {
                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $result->records 			= $query->result();
                $counters 					= $this->get_total_asset($account_id, $search_term, $asset_statuses, $asset_types, false, $raw_where, $limit);
                $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
                $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
                $result->counters->limit  	= (!empty($limit > 0)) ? $limit : $result->counters->total;
                $result->counters->offset 	= $offset;

                $this->session->set_flashdata('message', 'Records found.');
            } else {
                $this->session->set_flashdata('message', 'No records found matching your criteria.');
            }
        }

        return $result;
    }

    /*
    * Get total asset count for the search
    */
    public function get_total_asset($account_id = false, $search_term = false, $asset_statuses = false, $asset_types = false, $asset_categories = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;
        if (!empty($account_id)) {
            $where = $raw_where = convert_to_array($where);
            if (isset($where['audit_type_id'])) {
                if ($where['audit_type_id'] > 0) {
                    $qry = $this->db->select('qb.asset_type_id', false)
                        ->where('qb.asset_type_id > 0')
                        ->where('qb.account_id', $account_id)
                        ->where('qb.audit_type_id', $where['audit_type_id'])
                        ->where('qb.is_active', 1)
                        ->group_by('qb.asset_type_id')
                        ->get('audit_question_bank qb');
                    if ($qry->num_rows() > 0) {
                        $asset_type_ids = array_column($qry->result_array(), 'asset_type_id');
                        if (!empty($asset_types) && is_array($asset_types)) {
                            $asset_types = array_merge($asset_types, $asset_type_ids);
                        } else {
                            $asset_types = $asset_type_ids;
                        }
                    }
                }
                unset($where['audit_type_id']);
            }

            #$this->db->select('asset.*, audit_result_statuses.result_status, site.site_name, concat(user.first_name," ",user.last_name) `assigned_to`,asset_statuses.status_name `asset_status`, asset_statuses.status_group `asset_status_group`, asset_types.asset_type, asset_types.asset_group',false)
            $this->db->select('asset.*, ata.attribute_name `primary_attribute`, atr.attribute_value, audit_result_statuses.result_status, site.site_name, site.site_address_id `address_id`, audit_categories.category_id, audit_categories.category_name, concat(user.first_name," ",user.last_name) `assigned_to`, concat(modifier.first_name," ",modifier.last_name) `last_modified_by`, asset_statuses.status_name `asset_status`, asset_statuses.status_group `asset_status_group`, asset_types.asset_type, asset_types.asset_group', false)
                ->join('asset_types', 'asset_types.asset_type_id = asset.asset_type_id', 'left')
                ->join('asset_type_attributes ata', 'ata.attribute_id = asset_types.primary_attribute_id', 'left')
                ->join('asset_attributes atr', 'atr.attribute_id = ata.attribute_id AND `atr`.`asset_id` = `asset`.`asset_id`', 'left')
                ->join('audit_categories', 'audit_categories.category_id = asset_types.category_id', 'left')
                ->join('user', 'user.id = asset.assignee', 'left')
                ->join('user modifier', 'modifier.id = asset.last_modified_by', 'left')
                ->join('asset_statuses', 'asset_statuses.status_id = asset.status_id', 'left')
                ->join('site', 'site.site_id = asset.site_id', 'left')
                ->join('audit_result_statuses', 'audit_result_statuses.audit_result_status_id = asset.audit_result_status_id', 'left')
                ->where('asset.account_id', $account_id)
                ->where('asset.archived !=', 1);

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->searchable_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        if (!empty($search_where['asset.status_id'])) {
                            $search_where['asset_statuses.status_name'] =  trim($term);
                            unset($search_where['asset.status_id']);
                        }

                        if (!empty($search_where['asset.asset_type_id'])) {
                            $search_where['asset_types.asset_type'] =  trim($term);
                            unset($search_where['asset.asset_type_id']);
                        }

                        if (!empty($search_where['asset.assignee'])) {
                            $search_where['user.first_name'] =  trim($term);
                            $search_where['user.last_name']  =  trim($term);
                            unset($search_where['asset.assignee']);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->searchable_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    if (!empty($search_where['asset.status_id'])) {
                        $search_where['asset_statuses.status_name'] =  $search_term;
                        unset($search_where['asset.status_id']);
                    }

                    if (!empty($search_where['asset.asset_type_id'])) {
                        $search_where['asset_types.asset_type'] =  $search_term;
                        unset($search_where['asset.asset_type_id']);
                    }

                    if (!empty($search_where['asset.assignee'])) {
                        $search_where['user.first_name'] =  $search_term;
                        $search_where['user.last_name']  =  $search_term;
                        unset($search_where['asset.assignee']);
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($asset_types)) {
                $asset_types = convert_to_array($asset_types);
                $this->db->where_in('asset.asset_type_id', $asset_types);
            }

            if (!empty($asset_categories)) {
                $asset_categories = convert_to_array($asset_categories);
                $this->db->where_in('asset_types.category_id', $asset_categories);
            }

            if (!empty($asset_statuses)) {
                $asset_statuses = convert_to_array($asset_statuses);
                $this->db->where_in('asset.status_id', $asset_statuses);
            }

            if ($where) {
                $where = convert_to_array($where);

                if (isset($where['period_days'])) {
                    $group_min = 0;
                    $group_max = 365;
                    switch($where['period_days']) {
                        case 0:
                        case '0':
                            $this->db->where('( DATEDIFF( asset.end_of_life_date, CURDATE() ) < 0 )');
                            break;
                        case ($where['period_days'] > 0 && $where['period_days'] <= 30):
                            $group_min = 0;
                            $group_max = 30;
                            $this->db->where('( ( DATEDIFF( asset.end_of_life_date, CURDATE() ) > '.$group_min.' ) AND ( DATEDIFF( asset.end_of_life_date, CURDATE() ) <= '.$group_max.' ) )');
                            break;
                        case ($where['period_days'] > 30 && $where['period_days'] <= 60):
                            $group_min = 30;
                            $group_max = 60;
                            $this->db->where('( ( DATEDIFF( asset.end_of_life_date, CURDATE() ) > '.$group_min.' ) AND ( DATEDIFF( asset.end_of_life_date, CURDATE() ) <= '.$group_max.' ) )');
                            break;
                        case ($where['period_days'] > 60 && $where['period_days'] <= 90):
                            $group_min = 60;
                            $group_max = 90;
                            $this->db->where('( ( DATEDIFF( asset.end_of_life_date, CURDATE() ) > '.$group_min.' ) AND ( DATEDIFF( asset.end_of_life_date, CURDATE() ) <= '.$group_max.' ) )');
                            break;
                        case ($where['period_days'] > 90 && $where['period_days'] <= 180):
                            $group_min = 90;
                            $group_max = 180;
                            $this->db->where('( ( DATEDIFF( asset.end_of_life_date, CURDATE() ) > '.$group_min.' ) AND ( DATEDIFF( asset.end_of_life_date, CURDATE() ) <= '.$group_max.' ) )');
                            break;
                        case ($where['period_days'] > 0 && $where['period_days'] <= 365):
                            $group_min = 0;
                            $group_max = 365;
                            $this->db->where('( ( ( DATEDIFF( asset.end_of_life_date, CURDATE() ) >= '.$group_min.' ) AND ( DATEDIFF( asset.end_of_life_date, CURDATE() ) <= '.$group_max.' ) ) OR ( DATEDIFF( asset.end_of_life_date, CURDATE() ) < 0 ) )');
                            break;
                        case ($where['period_days'] < 0):
                            $this->db->where('( ( asset.end_of_life_date IS NULL ) OR ( asset.end_of_life_date = "0000-00-00" ) )');
                            break;
                    }
                    unset($where['period_days']);
                }

                if (isset($where['asset_type_id'])) {
                    if (!empty($where['asset_type_id'])) {
                        $this->db->where('asset.asset_type_id', $where['asset_type_id']);
                    }
                    unset($where['asset_type_id']);
                }

                if (isset($where['location_id'])) {
                    if (!empty($where['location_id'])) {
                        $this->db->where('asset.location_id', $where['location_id']);
                    }
                    unset($where['location_id']);
                }

                if (isset($where['assignee'])) {
                    if (!empty($where['assignee'])) {
                        $this->db->where('asset.assignee', $where['assignee']);
                    }
                    unset($where['assignee']);
                }

                if (isset($where['audit_result_status_id'])) {
                    if (!empty($where['audit_result_status_id'])) {
                        $this->db->where('asset.audit_result_status_id', $where['audit_result_status_id']);
                    }
                    unset($where['audit_result_status_id']);
                }

                if (isset($where['discipline_id'])) {
                    if (!empty($where['discipline_id'])) {
                        $this->db->where('asset_types.discipline_id', $where['discipline_id']);
                    }
                    unset($where['discipline_id']);
                }

                if (isset($where['site_id'])) {
                    if (!empty($where['site_id'])) {
                        $this->db->where('asset.site_id', $where['site_id']);
                    }
                    unset($where['site_id']);
                }

                if (!empty($where)) {
                    #$this->db->where( $where );
                }
            }

            $query = $this->db->from('asset')->count_all_results();
            $results['total'] = !empty($query) ? $query : 0;
            $limit 			  = (!empty($limit > 0)) ? $limit : $results['total'];
            $results['pages'] = !empty($query) ? ceil($query / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }

    /** Process Asset Upload **/
    public function upload_asset($account_id = false)
    {
        $result = null;
        if (!empty($account_id)) {
            $uploaddir  = $this->app_root. 'asset' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;

            if (!file_exists($uploaddir)) {
                mkdir($uploaddir);
            }

            for ($i=0; $i < count($_FILES['upload_file']['name']); $i++) {
                //Get the temp file path
                $tmpFilePath = $_FILES['upload_file']['tmp_name'][$i];
                if ($tmpFilePath != '') {
                    $uploadfile = $uploaddir . basename($_FILES['upload_file']['name'][$i]); //Setup our new file path
                    if (move_uploaded_file($tmpFilePath, $uploadfile)) {
                        //If FILE is CSV process differently
                        $ext = pathinfo($uploadfile, PATHINFO_EXTENSION);
                        if ($ext == 'csv') {
                            $processed = csv_file_to_array($uploadfile);
                            if (!empty($processed)) {
                                $data = $this->_save_temp_data($account_id, $processed);
                                if ($data) {
                                    unlink($uploadfile);
                                    $result = true;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    /** Process uploaded array **/
    private function _save_temp_data($account_id = false, $raw_data = false)
    {
        $result = null;
        if (!empty($account_id) && !empty($raw_data)) {
            $exists = $new = [];
            foreach ($raw_data as $k => $record) {
                $check_exists = $this->db->where(['account_id'=>$account_id, 'asset_unique_id'=>$record['asset_unique_id'] ])
                    ->limit(1)
                    ->get('asset_tmp_upload')
                    ->row();
                if (!empty($check_exists)) {
                    $exists[] 	= $this->ssid_common->_filter_data('asset_tmp_upload', $record);
                } else {
                    $new[]  	= $this->ssid_common->_filter_data('asset_tmp_upload', $record);
                }
            }

            //Updated existing
            if (!empty($exists)) {
                $this->db->update_batch('asset_tmp_upload', $exists, 'asset_unique_id');
            }

            //Insert new records
            if (!empty($new)) {
                $this->db->insert_batch('asset_tmp_upload', $new);
            }

            $result = ($this->db->trans_status() !== false) ? true : false;
        }
        return $result;
    }

    /** Get records penging from upload **/
    public function get_pending_upload_records($account_id = false)
    {
        $result = null;
        if (!empty($account_id)) {
            $query = $this->db->where('account_id', $account_id)
                ->order_by('asset_unique_id')
                ->get('asset_tmp_upload');

            if ($query->num_rows() > 0) {
                $data = [];
                foreach ($query->result() as $k => $row) {
                    $check = $this->db->select('asset.asset_id')
                        ->where('asset.account_id', $account_id)
                        ->where('asset.asset_unique_id', $row->asset_unique_id)
                        ->limit(1)
                        ->get('asset')
                        ->row();

                    if (!empty($check->asset_id)) {
                        $data['existing-records'][] = ( array ) $row;
                    } else {
                        $data['new-records'][] = ( array ) $row;
                    }
                }
                $result = $data;
            }
        }
        return $result;
    }

    /*
    * Update Asset record
    */
    public function update_temp_data($account_id = false, $temp_asset_id = false, $temp_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($temp_asset_id) && !empty($temp_data)) {
            $data  = [];
            $where = [
                'account_id'=>$account_id,
                'temp_asset_id'=>$temp_asset_id
            ];

            foreach ($temp_data as $key => $value) {
                $data[$key] = trim($value);
            }

            $update_data = array_merge($data, $where);
            $update_data = $this->ssid_common->_filter_data('asset_tmp_upload', $update_data);
            $this->db->where($where)
                ->update('asset_tmp_upload', $update_data);

            $result = ($this->db->trans_status() !== 'false') ? true : false;
        }
        return $result;
    }

    /** Create Assets in Bulk **/
    public function create_bulk_asset($account_id = false, $postdata = false)
    {
        $result = null;
        if (!empty($account_id) && !empty($postdata['asset'])) {
            $to_delete = $processed = [];
            foreach ($postdata['asset'] as $temp_asset_id => $update_record) {
                #get temp data
                if (!empty($update_record['checked'])) {
                    $get_temp_record = (array) $this->db->get_where('asset_tmp_upload', [ 'temp_asset_id'=>$temp_asset_id ])->row();
                    $new_asset = $this->create_asset($account_id, $get_temp_record);
                    if (!empty($new_asset)) {
                        $processed[] = $new_asset;
                        $to_delete[$temp_asset_id] = $temp_asset_id;
                    } else {
                        $asset_failed[] = $get_temp_record;
                    }
                }
            }

            if (!empty($processed)) {
                $result = $processed;
                //Delete processed records
                if (!empty($to_delete)) {
                    $this->db->where_in('temp_asset_id', $to_delete)
                        ->delete('asset_tmp_upload');

                    $this->ssid_common->_reset_auto_increment('asset_tmp_upload', 'temp_asset_id'); //House keeping
                }
                $this->session->set_flashdata('message', 'Asset records added successfully.');
            }
        }
        return $result;
    }

    /*
    * Get Asset statistics
    */
    public function get_asset_stats($account_id=false, $stat_type = false, $period_days = '30', $date_from=false, $date_to = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($stat_type)) {
            switch(strtolower($stat_type)) {
                case 'asset_status':
                    $asset_data = $this->db->select('asset_statuses.*, COUNT( asset.asset_id ) status_total', false)
                        ->join('asset', 'asset_statuses.status_id = asset.status_id')
                        ->where('asset_statuses.account_id', $account_id)
                        ->where('asset_statuses.is_active', 1)
                        ->order_by('status_group')
                        ->group_by('asset_statuses.status_id')
                        ->get('asset_statuses');
                    $num_rows = ($asset_data->num_rows() > 0) ? $asset_data->num_rows() : false;
                    break;

                case 'eol':

                    $eol_statuses = $this->get_eol_statuses($account_id);

                    $sql_select = '';

                    if (!empty($eol_statuses)) {
                        foreach ($eol_statuses as $k => $eol_group) {
                            $group_min = (!empty($eol_group->eol_group_min)) ? $eol_group->eol_group_min : 0;
                            $group_max = (!empty($eol_group->eol_group_max)) ? $eol_group->eol_group_max : 0;

                            if (strtolower($eol_group->eol_group) == 'eol_expired') {
                                $sql_select .= 'SUM( CASE WHEN DATEDIFF( asset.end_of_life_date, CURDATE() ) < 0 THEN 1 ELSE 0 END ) `eol_expired`, ';
                            } elseif (strtolower($eol_group->eol_group) == 'eol_not_set') {
                                $sql_select .= 'SUM( CASE WHEN ( ( asset.end_of_life_date IS NULL ) OR ( asset.end_of_life_date = "0000-00-00" ) ) THEN 1 ELSE 0 END ) `eol_not_set`, ';
                            } else {
                                $sql_select .= 'SUM( CASE WHEN ( ( DATEDIFF( asset.end_of_life_date, CURDATE() ) > '.$group_min.' ) AND ( DATEDIFF( asset.end_of_life_date, CURDATE() ) <= '.$group_max.' ) ) THEN 1 ELSE 0 END ) `'.$eol_group->eol_group.'`, ';
                            }
                        }
                    }

                    $asset_data = $this->db->select($sql_select, false)
                        ->where('asset.account_id', $account_id)
                        ->where('asset.archived !=', 1)
                        ->get('asset');
                    $num_rows = ($asset_data->num_rows() > 0) ? $asset_data->num_rows() : false;

                    //Prepare for bar-graph
                    break;

                case 'replace_cost':

                    ## Select expired asset firs then a list based on the date supplied date range eg. 30 / 60 / 90 / 180 / 365
                    $where = ' ( ( DATEDIFF( a.end_of_life_date, CURDATE() ) < 0 ) OR ( ( DATEDIFF( a.end_of_life_date, CURDATE() ) > 0 ) AND ( DATEDIFF( a.end_of_life_date, CURDATE() ) <= '.$period_days.' ) ) ) ';

                    $asset_data = $this->db->select('
							SUM( CASE WHEN DATEDIFF( a.end_of_life_date, CURDATE() ) < 0 THEN 1 ELSE 0 END ) `expired`,
							SUM( CASE WHEN ( ( DATEDIFF( a.end_of_life_date, CURDATE() ) < 0 ) AND ( a.purchase_price > 0 ) ) THEN a.purchase_price ELSE 0 END ) `expired_cost`,
							SUM( CASE WHEN ( ( DATEDIFF( a.end_of_life_date, CURDATE() ) > 0 ) AND ( DATEDIFF( a.end_of_life_date, CURDATE() ) <= '.$period_days.' ) ) THEN 1 ELSE 0 END ) `due_to_expire`,
							SUM( CASE WHEN ( ( DATEDIFF( a.end_of_life_date, CURDATE() ) > 0 ) AND ( DATEDIFF( a.end_of_life_date, CURDATE() ) <= '.$period_days.' ) AND ( a.purchase_price > 0 ) ) THEN a.purchase_price ELSE 0 END ) `due_to_expire_cost`,
							COUNT( a.asset_id ) `counted_asset`, SUM( CASE WHEN a.purchase_price > 0 THEN a.purchase_price ELSE 0 END ) `replacement_cost`', false)
                        ->where($where)
                        ->where('a.account_id', $account_id)
                        ->where('a.archived !=', 1)
                        ->get('asset a');

                    $num_rows = ($asset_data->num_rows() > 0) ? $asset_data->num_rows() : false;
                    break;


                case 'tagging_summary':
                    $stats_data = [
                        'total_assets'   =>0,
                        'total_buildings'=>0,
                        'number_of_flats'=>0,
                    ];
                    $buildings_data  = $this->db->select('s.site_id, s.site_name, s.number_of_flats, s.archived')
                        ->where('s.account_id', $account_id)
                        ->where('s.archived !=', 1)
                        ->get('site s');

                    if ($buildings_data->num_rows() > 0) {
                        foreach ($buildings_data->result() as $row) {
                            $stats_data['total_buildings'] 	+= 1;
                            $stats_data['number_of_flats'] 	+= $row->number_of_flats;

                            $assets = $this->db->select('SUM( CASE WHEN asset.asset_id > 0 THEN 1 ELSE 0 END ) `total_assets`', false)
                                ->where('asset.account_id', $account_id)
                                ->where('asset.site_id', $row->site_id)
                                ->where('asset.archived !=', 1)
                                ->get('asset');

                            if ($assets->num_rows() > 0) {
                                if ($assets->result()[0]->total_assets > 0) {
                                    $stats_data['total_assets'] += $assets->result()[0]->total_assets;
                                }
                            }
                        }
                    }

                    if (!empty($stats_data)) {
                        foreach ($stats_data as $col => $val) {
                            $num_rows['stats'][] 	= [ 'column_key'=>$col, 'column_header'=> ucwords(str_replace("_", " ", $col)), 'column_value'=> (string) $val, 'hex_color'=> '#6CD167' ];
                            $num_rows['totals'][] 	= [ 'column_key'=>$col, 'column_header'=> ucwords(str_replace("_", " ", $col)), 'column_value'=> (string) $val, 'hex_color'=> '#6CD167' ];
                        }
                    }

                    $num_rows 	= (!empty($num_rows)) ? $num_rows : false;
                    $stats_data = (!empty($num_rows)) ? $num_rows : false;

                    break;
            }

            if ($num_rows) {
                if ($stat_type == 'eol') {
                    $data 		= [];
                    foreach ($asset_data->result_array()[0] as $stat_grp => $eol_data) {
                        if (!empty($eol_statuses)) {
                            foreach ($eol_statuses as $k => $value) {
                                if (strtolower($stat_grp) == ($value->eol_group)) {
                                    $data[] = array_merge((array)$value, ['eol_group_total'=>$eol_data ]);
                                }
                            }
                        }
                    }
                    $result = $data;
                } elseif ($stat_type == 'replace_cost') {
                    $result = $asset_data->result()[0];
                    $result->period_days = ( string ) $period_days;
                } else {
                    $result = (!empty($stats_data)) ? $stats_data : $asset_data->result();
                }

                $this->session->set_flashdata('message', 'Asset stats found');
            } else {
                $this->session->set_flashdata('message', 'Asset stats not available');
            }
        } else {
            $this->session->set_flashdata('message', 'Missing required information');
        }
        return $result;
    }

    /** Get Asset EOL Group statuses **/
    public function get_eol_statuses($account_id = false, $eol_group = false)
    {
        $result = null;

        if ($account_id) {
            if (!empty($eol_group)) {
                $this->db->where('asset_eol_statuses.eol_group', $eol_group);
            }

            $query = $this->db->where('asset_eol_statuses.account_id', $account_id)
                ->where('is_active', 1)
                ->order_by('eol_group_ordering')
                ->get('asset_eol_statuses');

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'EOL Status data found.');
            } else {
                $this->session->set_flashdata('message', 'No data found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }

        return $result;
    }

    /** Create A New Asset Type **/
    public function add_asset_type($account_id = false, $postdata = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($postdata)) {
            $data = [];
            foreach ($postdata as $col => $value) {
                if ($col == 'asset_type') {
                    $data['asset_type_ref'] = strtolower(lean_string($value));
                }
                $data[$col] = trim($value);
            }

            $check_where  = '( asset_types.asset_type = "'.$data['asset_type_ref'].'" OR asset_types.asset_type = "'.$data['asset_type'].'" )';
            $check_exists = $this->db->where($check_where)
                ->where('asset_types.account_id', $account_id)
                ->where('asset_types.is_active', 1)
                ->limit(1)
                ->get('asset_types')->row();

            if (!empty($check_exists)) {
                $this->session->set_flashdata('message', 'This Asset Type already exists, request aborted');
                $result = false;
            } else {
                $data					= $this->ssid_common->_filter_data('asset_types', $data);
                $data['created_by'] 	= $this->ion_auth->_current_user->id;
                $this->db->insert('asset_types', $data);
                $data['asset_type_id'] 	= $this->db->insert_id();
                $result 			 	= $this->db->select('asset_types.*, audit_categories.category_name', false)
                    ->join('audit_categories', 'audit_categories.category_id = asset_types.category_id', 'left')
                    ->get_where('asset_types', [ 'asset_types.account_id' => $account_id, 'asset_types.asset_type_id' => $data['asset_type_id'] ])
                    ->row();
                $this->session->set_flashdata('message', 'New Asset Type added successfully.');
            }
        }

        return $result;
    }

    /** Edit / Update Asset Type **/
    public function update_asset_type($account_id = false, $postdata = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($postdata['asset_type_id'])) {
            $data = [];
            foreach ($postdata as $col => $value) {
                if ($col == 'asset_type') {
                    $data['asset_type_ref'] = strtolower(lean_string($value));
                }
                $data[$col] = trim($value);
            }

            $check_exists = $this->db->where('asset_type_id', $data['asset_type_id'])
                ->where('asset_types.account_id', $account_id)
                ->where('asset_types.is_active', 1)
                ->limit(1)
                ->get('asset_types')->row();

            if (!empty($check_exists)) {
                $data					  = $this->ssid_common->_filter_data('asset_types', $data);
                $data['last_modified_by'] = $this->ion_auth->_current_user->id;

                $this->db->where('asset_type_id', $check_exists->asset_type_id)
                    ->update('asset_types', $data);

                $result = $this->get_asset_types($account_id, $check_exists->asset_type_id);
                $this->session->set_flashdata('message', 'Asset Type updated successfully.');
            } else {
                $this->session->set_flashdata('message', 'This Asset Type does not exist or does not belong to you.');
                $result = false;
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }

        return $result;
    }

    /** Get Default Asset status **/
    public function get_default_asset_status($account_id = false)
    {
        $result = false;
        if (!empty($account_id)) {
            $query = $this->db->where('account_id', $account_id)
                ->where('status_group', 'unassigned')
                ->get('asset_statuses');

            if ($query->num_rows() > 0) {
                $result = $query->result()[0];
            } else {
                //Create new List here and use it
                $tbl_data = [ 'table_name'=>'asset_statuses', 'primary_key'=>'status_id' ];
                $new_list = $this->account_service->copy_account_options($account_id, $tbl_data);
                if (!empty($new_list)) {
                    foreach ($new_list as $k => $row) {
                        if (strtolower($row->status_group) == 'unassigned') {
                            return $row;
                        }
                    }
                }
            }
        }
        return $result;
    }


    /**
    * Assign asset(s) to an asset
    */
    public function link_assets($account_id = false, $parent_asset_id = false, $asset_data = false)
    {
        $result = false;


        if (!empty($account_id) && !empty($parent_asset_id) && !empty($asset_data)) {
            $asset_data 	= convert_to_array($asset_data);
            $asset_ids		= !empty($asset_data['linked_assets']) ? $asset_data['linked_assets'] : false;
            $asset_ids		= (is_json($asset_ids)) ? json_decode($asset_ids) : $asset_ids;

            if (!empty($asset_ids)) {
                $asset_ids 	= array_diff($asset_ids, [ $parent_asset_id ]);
                foreach ($asset_ids as $asset_id) {
                    $condition = $data = [
                        'parent_asset_id'	=> $parent_asset_id,
                        'asset_id'			=> $asset_id,
                        'account_id'		=> $account_id
                    ];

                    $check_exists = $this->db->get_where('asset_connectivity', $data)->row();
                    if (!empty($check_exists)) {
                        $data['last_modified_by'] = $this->ion_auth->_current_user->id;
                        $this->db->where('asset_connectivity.id', $check_exists->id)
                            ->update('asset_connectivity', $data);
                    } else {
                        $data['linked_by'] = $this->ion_auth->_current_user->id;
                        $this->db->insert('asset_connectivity', $data);
                    }
                }

                if ($this->db->affected_rows() > 0 || ($this->db->trans_status() !== false)) {
                    $result = $this->get_linked_assets($account_id, $parent_asset_id);
                    $this->session->set_flashdata('message', 'Asset(s) linked successfully.');
                }
            } else {
                $this->session->set_flashdata('message', 'There was a problem problem processing your request.');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }
        return $result;
    }


    /**
    * Unlink asset(s) from it's parent asset
    */
    public function unlink_asset($account_id = false, $parent_asset_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($parent_asset_id) && !empty($postdata)) {
            $postdata 		= convert_to_array($postdata);
            $linked_asset	= !empty($postdata['linked_assets']) ? $postdata['linked_assets'] : false;
            $linked_asset	= (is_json($linked_asset)) ? json_decode($linked_asset) : $linked_asset;
            $deleted		= [];

            if (!empty($linked_asset)) {
                foreach ($linked_asset as $k => $val) {
                    $data = [
                        'parent_asset_id'	=> $parent_asset_id,
                        'asset_id'			=> $val
                    ];

                    $check_exists = $this->db->limit(1)->get_where('asset_connectivity', $data)->row();
                    if (!empty($check_exists)) {
                        $this->db->where($data);
                        $this->db->delete('asset_connectivity');
                        $this->ssid_common->_reset_auto_increment('asset_connectivity', 'id');
                    }
                    $deleted[] = $data;
                }
            } elseif (!empty($postdata['asset_id'])) {
                $data = [
                    'parent_asset_id'=> $parent_asset_id,
                    'asset_id'		 => $postdata['asset_id']
                ];

                $check_exists = $this->db->limit(1)->get_where('asset_connectivity', $data)->row();
                if (!empty($check_exists)) {
                    $this->db->where($data);
                    $this->db->delete('asset_connectivity');
                    $deleted[] = $data;
                    $this->ssid_common->_reset_auto_increment('asset_connectivity', 'id');
                }
            }

            if (!empty($deleted)) {
                $result = $deleted;
                $this->session->set_flashdata('message', 'Asset(s) unlinked successfully');
            } else {
                $this->session->set_flashdata('message', 'No asset(s) were unlinked');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }
        return $result;
    }

    /*
    *	Get all contracts to which this Asset record is attached
    */
    public function get_linked_contracts($account_id = false, $asset_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = 0)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select('contract.contract_id, contract.contract_name, ca.*, asset.*, asset_types.asset_type, categories.category_name, CONCAT( user.first_name, " ", user.last_name ) `created_by`')
                ->join('contract', 'contract.contract_id = ca.contract_id', 'left')
                ->join('asset', 'asset.asset_id = ca.asset_id', 'left')
                ->join('asset_types', 'asset_types.asset_type_id = asset.asset_type_id', 'left')
                ->join('audit_categories `categories`', 'categories.category_id = asset_types.category_id', 'left')
                ->join('user', 'ca.created_by = user.id', 'left')
                ->where('ca.account_id', $account_id);

            if (!empty($contract_id)) {
                $this->db->where('ca.contract_id', $contract_id);
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->get('contract_asset ca');
            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Linked asset data found.');
            } else {
                $this->session->set_flashdata('message', 'Linked asset data not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }

        return $result;
    }


    /*
    * Get a list of all linked asset based on contract / site / location
    */
    public function get_linked_assets($account_id = false, $parent_asset_id = false, $asset_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = 0)
    {
        $result = null;
        if (!empty($account_id)) {
            $where		 	= convert_to_array($where);

            $contract_id 	= !empty($where['contract_id']) ? $where['contract_id'] : false;
            $site_id 	 	= !empty($where['site_id']) ? $where['site_id'] : false;
            $location_id 	= !empty($where['location_id']) ? $where['location_id'] : false;
            $parent_asset_id= !empty($where['parent_asset_id']) ? $where['parent_asset_id'] : (!empty($parent_asset_id) ? $parent_asset_id : false);
            $asset_id 	 	= !empty($where['asset_id']) ? $where['asset_id'] : (!empty($asset_id) ? $asset_id : $parent_asset_id);
            $inverse 	 	= !empty($where['inverse']) ? true : false;
            $grouped 	 	= !empty($where['grouped']) ? true : false;

            if (!empty($asset_id)) {
                $this->db->select('asset.*, ata.attribute_name `primary_attribute`, atr.attribute_value, ac.parent_asset_id `parent_asset_id`, ac.date_linked, concat(creator.first_name," ",creator.last_name) `linked_by`, audit_categories.category_id, audit_categories.category_name, concat(user.first_name," ",user.last_name) `assigned_to`,  concat(modifier.first_name," ",modifier.last_name) `last_modified_by`, asset_types.asset_type, asset_types.asset_group', false);

                if ($inverse) {
                    $this->db->join('asset', 'asset.asset_id = ac.parent_asset_id', 'left')
                        ->where('ac.asset_id', $asset_id)
                        ->group_by('ac.asset_id');
                } else {
                    $this->db->join('asset', 'asset.asset_id = ac.asset_id', 'left')
                        ->where('ac.parent_asset_id', $asset_id)
                        ->group_by('ac.asset_id');
                }

                $query = $this->db->join('asset_types', 'asset_types.asset_type_id = asset.asset_type_id', 'left')
                    ->join('asset_type_attributes ata', 'ata.attribute_id = asset_types.primary_attribute_id', 'left')
                    ->join('asset_attributes atr', 'atr.asset_id = asset.asset_id', 'left')
                    ->join('audit_categories', 'audit_categories.category_id = asset_types.category_id', 'left')
                    ->join('user', 'user.id = asset.assignee', 'left')
                    ->join('user creator', 'creator.id = ac.linked_by', 'left')
                    ->join('user modifier', 'modifier.id = ac.last_modified_by', 'left')
                    ->where('asset.archived !=', 1)
                    ->where('asset.account_id', $account_id)
                    ->get('asset_connectivity `ac`');

                if ($query->num_rows() > 0) {
                    $this->session->set_flashdata('message', 'Linked asset data found.');
                    $result = $query->result();
                }
            } else {
                if (!empty($contract_id)) {
                    $site_query = $this->db->select('site_id')
                        ->where('sites_contracts.contract_id', $contract_id)
                        ->where('sites_contracts.account_id', $account_id)
                        ->get('sites_contracts');
                    unset($where['contract_id']);
                    if ($site_query->num_rows() > 0) {
                        $site_id = array_column($site_query->result_array(), 'site_id');
                    }
                }

                if (!empty($site_id)) {
                    if (is_array($site_id)) {
                        $this->db->where_in('asset.site_id', $site_id);
                    } else {
                        $this->db->where('asset.site_id', $site_id);
                    }
                    unset($where['site_id']);
                }

                if (!empty($location_id)) {
                    if (is_array($location_id)) {
                        $this->db->where_in('asset.location_id', $location_id);
                    } else {
                        $this->db->where('asset.location_id', $location_id);
                    }
                    unset($where['location_id']);
                }

                $query = $this->db->select('asset.*, ata.attribute_name `primary_attribute`, atr.attribute_value, audit_categories.category_id, audit_categories.category_name, concat(user.first_name," ",user.last_name) `assigned_to`, concat(modifier.first_name," ",modifier.last_name) `last_modified_by`, asset_statuses.status_name `asset_status`, asset_statuses.status_group `asset_status_group`, asset_types.asset_type, asset_types.asset_group', false)
                    ->join('asset_types', 'asset_types.asset_type_id = asset.asset_type_id', 'left')
                    ->join('asset_type_attributes ata', 'ata.attribute_id = asset_types.primary_attribute_id', 'left')
                    ->join('asset_attributes atr', 'atr.asset_id = asset.asset_id', 'left')
                    ->join('audit_categories', 'audit_categories.category_id = asset_types.category_id', 'left')
                    ->join('user', 'user.id = asset.assignee', 'left')
                    ->join('user modifier', 'modifier.id = asset.last_modified_by', 'left')
                    ->join('asset_statuses', 'asset_statuses.status_id = asset.status_id', 'left')
                    ->join('site', 'site.site_id = asset.site_id', 'left')
                    ->group_by('asset.asset_id')
                    ->where('asset.account_id', $account_id)
                    ->where('asset.archived !=', 1)
                    ->get('asset');

                if ($query->num_rows() > 0) {
                    if ($grouped) {
                        $data = [];
                        foreach ($query->result() as $k => $row) {
                            $data[$row->asset_type][] = $row;
                        }
                        $result = $data;
                    } else {
                        $result = $query->result();
                    }
                } else {
                    $this->session->set_flashdata('message', 'No linked data found.');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }
        return $result;
    }


    /** Get Asset Response Types **/
    public function get_response_types($account_id = false, $where = false)
    {
        $result = null;

        if ($account_id) {
            $this->db->where('attribute_response_types.account_id', $account_id);

            if (!empty($where)) {
                $where = convert_to_array($where);

                if (isset($where['response_type_id']) ||  isset($where['response_type'])) {
                    $ref_condition = (!empty($where['response_type_id'])) ? [ 'response_type_id'=>$where['response_type_id'] ] : (!empty($where['response_type']) ? [ 'response_type'=>$where['response_type'] ] : false);

                    if (!empty($ref_condition)) {
                        $row = $this->db->get_where('attribute_response_types', $ref_condition)->row();
                        if (!empty($row)) {
                            $resp_options 				= $this->_get_response_type_options($row->response_type_id);
                            $row->response_type_options = (!empty($resp_options)) ? $resp_options : null;
                            $result = $row;
                            $this->session->set_flashdata('message', 'Asset Attribute Response types data found');
                            return $result;
                        } else {
                            $this->session->set_flashdata('message', 'Asset Attribute Response types data not found');
                            return false;
                        }
                    }
                    unset($where['response_type_id']);
                }

                if (isset($where['un_grouped'])) {
                    if (!empty($where['un_grouped'])) {
                        $un_grouped = true;
                    }
                    unset($where['un_grouped']);
                }
            }

            $query = $this->db->select('attribute_response_types.*', false)
                ->order_by('attribute_response_types.response_type_ordering, attribute_response_types.response_type_alt')
                ->where('attribute_response_types.is_active', 1)
                ->get('attribute_response_types');

            if ($query->num_rows() > 0) {
                $data = [];
                foreach ($query->result() as $k => $row) {
                    $resp_options = $this->_get_response_type_options($row->response_type_id);
                    $row->response_type_options = (!empty($resp_options)) ? $resp_options : null;
                    $data[] = $row;
                }
                $result = $data;
                $this->session->set_flashdata('message', 'Asset Response types data found.');
            } else {
                $query = $this->db->select('attribute_response_types.*', false)
                    ->order_by('attribute_response_types.response_type_ordering, attribute_response_types.response_type_alt')
                    ->where('( attribute_response_types.account_id IS NULL OR attribute_response_types.account_id = "" )')
                    ->where('attribute_response_types.is_active', 1)
                    ->get('attribute_response_types');

                if ($query->num_rows() > 0) {
                    $data = [];
                    foreach ($query->result() as $k => $row) {
                        $resp_options = $this->_get_response_type_options($row->response_type_id);
                        $row->response_type_options = (!empty($resp_options)) ? $resp_options : null;
                        if (!empty($un_grouped)) {
                            $data[] = $row;
                        } else {
                            $data[$row->response_type_id] = $row;
                        }
                    }
                    $result = $data;
                    $this->session->set_flashdata('message', 'Asset Response types data found.');
                } else {
                    $this->session->set_flashdata('message', 'Asset Response types data not found.');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }
        return $result;
    }

    /** Get list of all options attached to a Response type **/
    private function _get_response_type_options($response_type_id = false)
    {
        $result = false;

        if (!empty($response_type_id)) {
            $query = $this->db->select('opts.*', false)
                ->where('opts.response_type_id', $response_type_id)
                ->order_by('opts.option_ordering')
                ->get('attribute_response_type_options opts');

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Asset Response types data found.');
            } else {
                $this->session->set_flashdata('message', 'Asset Response types data not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Response type ID is a mandatory field.');
        }

        return $result;
    }


    /** Create new Asset Type Attribute **/
    public function add_asset_type_attribute($account_id = false, $asset_type_attribute_data = false)
    {
        $result = null;

        if (!empty($account_id) && !empty($asset_type_attribute_data)) {
            foreach ($asset_type_attribute_data as $col => $value) {
                if ($col == 'attribute_name') {
                    $data['attribute_ref'] = strtolower(lean_string($value));
                }
                $data[$col] = (is_array($value)) ? json_encode($value) : $value;
            }

            if (!empty($data['response_options'])) {
                $file_types 	  = '';
                $response_options = convert_to_array($data['response_options']);
                unset($data['response_options']);
                if (!empty($response_options[ $data['response_type'] ]['options'])) {
                    $options = $response_options[ $data['response_type'] ]['options'];
                    if (!empty($options)) {
                        $update_opts = $this->update_response_options($account_id, $data['response_type'], $options);
                        $data['response_options'] = json_encode($options);
                    }

                    if (in_array(strtolower($data['response_type']), $this->file_response_types)) {
                        $file_types = $data['response_options'];
                    }
                }
            }

            $response_type = $this->get_response_types($account_id, ['response_type'=>$data['response_type'] ]);
            $data['response_type'] 		= (!empty($response_type->response_type)) ? $response_type->response_type : ucwords($data['response_type']);
            $data['response_options'] 	= (!empty($data['response_options'])) ? $data['response_options'] : null;
            $data['file_types']			= (!empty($data['file_types'])) ? $data['file_types'] : (!empty($file_types) ? $file_types : null);

            if (!empty($data['override_existing']) && !empty($data['attribute_id'])) {
                $check_exists = $this->db->where('account_id', $account_id)
                    ->where('attribute_id', $data['attribute_id'])
                    ->get('asset_type_attributes')->row();
            } else {
                unset($data['attribute_id']);
                $check_exists = $this->db->where('account_id', $account_id)
                    ->where('asset_type_attributes.asset_type_id', $data['asset_type_id'])
                    ->where('asset_type_attributes.attribute_name', $data['attribute_name'])
                    ->limit(1)
                    ->get('asset_type_attributes')
                    ->row();
            }

            $data = $this->ssid_common->_filter_data('asset_type_attributes', $data);

            if (!empty($check_exists)) {
                #$data['last_modified_by'] = $this->ion_auth->_current_user->id;
                #$this->db->where( 'attribute_id', $check_exists->attribute_id )
                #->update( 'asset_type_attributes', $data );
                $this->session->set_flashdata('message', 'This Asset Type attribute already exists, record has been updated successfully.');
                $result = false;
            } else {
                $data['created_by'] 	= $this->ion_auth->_current_user->id;
                $this->db->insert('asset_type_attributes', $data);
                $this->session->set_flashdata('message', 'New Asset Type attribute added successfully.');
                $data['attribute_id'] 		= $this->db->insert_id();
                if (!empty($data['response_options'])) {
                    $data['response_options'] 	= json_decode($data['response_options']);
                }
                $result = $data;
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }

        return $result;
    }

    /** Update an existing Asset Type attribute **/
    public function update_asset_type_attribute($account_id = false, $attribute_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($attribute_id)  && !empty($postdata)) {
            foreach ($postdata as $col => $value) {
                if ($col == 'attribute_name') {
                    $data['attribute_ref'] = strtoupper(lean_string($value));
                }
                $data[$col] = (is_array($value)) ? json_encode($value) : $value;
            }

            if (!empty($data['response_options'])) {
                $response_options = convert_to_array($data['response_options']);
                unset($data['response_options']);
                if (!empty($response_options[ $data['response_type'] ]['options'])) {
                    $options = $response_options[ $data['response_type'] ]['options'];
                    if (!empty($options)) {
                        $update_opts = $this->update_response_options($account_id, $data['response_type'], $options);
                        $data['response_options'] = json_encode($options);
                    }

                    if (in_array(strtolower($data['response_type']), $this->file_response_types)) {
                        $file_types = $data['response_options'];
                    }
                } else {
                    $data['response_options'] = '';
                }
            }

            $response_type = $this->get_response_types($account_id, ['response_type'=>$data['response_type'] ]);
            $data['response_type_alt'] 	= (!empty($response_type->response_type_alt)) ? $response_type->response_type_alt : ucwords($data['response_type']);
            $data['response_options'] 	= (!empty($data['response_options'])) ? $data['response_options'] : null;
            $data['file_types']			= (!empty($data['file_types'])) ? $data['file_types'] : (!empty($file_types) ? $file_types : null);

            $ref_condition = [ 'account_id'=>$account_id, 'attribute_id'=>$attribute_id ];
            $update_data   = $this->ssid_common->_filter_data('asset_type_attributes', $data);
            $record_pre_update = $this->db->get_where('asset_type_attributes', [ 'account_id'=>$account_id, 'attribute_id'=>$attribute_id ])->row();

            if (!empty($record_pre_update)) {
                $asset_type_id  = (!empty($data['asset_type_id'])) ? $data['asset_type_id'] : 0;

                $check_conflict = $this->db->select('attribute_id', false)
                    ->where('account_id', $account_id)
                    ->where('attribute_id !=', $attribute_id)
                    ->where('asset_type_id', $asset_type_id)
                    ->where('attribute_name', $update_data['attribute_name'])
                    ->limit(1)
                    ->get('asset_type_attributes')
                    ->row();

                if (!$check_conflict) {
                    $update_data['last_modified_by'] = $this->ion_auth->_current_user->id;
                    $this->db->where($ref_condition)
                        ->update('asset_type_attributes', $update_data);

                    $updated_record = $this->get_asset_type_attributes($account_id, false, $attribute_id);
                    $result = (!empty($updated_record)) ? $updated_record : false;
                    $this->session->set_flashdata('message', 'Asset Type Attribute updated successfully');
                    return $result;
                } else {
                    $this->session->set_flashdata('message', 'This Asset Type Attribute already exists under the specified section. Update request aborted');
                    return false;
                }
            } else {
                $this->session->set_flashdata('message', 'This Asset Type Attribute record does not exist or does not belong to you.');
                return false;
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }
        return $result;
    }


    /*
    * Get list of Asset Types attributes for a specific Asset Type
    */
    public function get_asset_type_attributes($account_id = false, $asset_type_id = false, $attribute_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            $where = convert_to_array($where);

            if (!empty($where['offline_mode'])) {
                if ($limit > 0) {
                    $this->db->limit($limit, $offset);
                }

                $query = $this->db->select('ata.*', false)
                    ->where('ata.account_id', $account_id)
                    ->get('asset_type_attributes ata');

                if ($query->num_rows() > 0) {
                    $result = $query->result();
                    $this->session->set_flashdata('message', 'Asset Type attributes found');
                } else {
                    $this->session->set_flashdata('message', 'No data found');
                }
            } else {
                $this->db->select('ata.*, asset_types.asset_type', false)
                    ->where('ata.account_id', $account_id)
                    ->join('asset_types', 'asset_types.asset_type_id = ata.asset_type_id', 'left');

                $asset_type_id = !empty($asset_type_id) ? $asset_type_id : (!empty($where['asset_type_id']) ? $where['asset_type_id'] : false);
                $attribute_id  = !empty($attribute_id) ? $attribute_id : (!empty($where['attribute_id']) ? $where['attribute_id'] : false);

                if (!empty($asset_type_id)) {
                    $this->db->where('ata.account_id', $account_id)
                        ->where('ata.asset_type_id', $asset_type_id);
                } else {
                    if (!$attribute_id) {
                        $this->db->where('( ata.asset_type_id IS NULL OR ata.asset_type_id = 0 )');
                    }
                    #$this->db->where( '( ata.account_id IS NULL OR ata.account_id = 0 )' );
                }

                if (!empty($search_term)) {
                    //Check for spaces in the search term
                    $search_term  = trim(urldecode($search_term));
                    $search_where = [];
                    if (strpos($search_term, ' ') !== false) {
                        $multiple_terms = explode(' ', $search_term);
                        foreach ($multiple_terms as $term) {
                            foreach ($this->asset_type_attribs_search as $k=>$field) {
                                $search_where[$field] = trim($term);
                            }

                            $where_combo = format_like_to_where($search_where);
                            $this->db->where($where_combo);
                        }
                    } else {
                        foreach ($this->asset_type_attribs_search as $k=>$field) {
                            $search_where[$field] = $search_term;
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                }

                if (!empty($attribute_id)) {
                    $row = $this->db->where('ata.attribute_id', $attribute_id)
                        ->get('asset_type_attributes ata')
                        ->row();

                    if (!empty($row)) {
                        $row->file_types 	   = (!empty($row->file_types)) ? json_decode($row->file_types) : null;
                        return $row;
                    }
                    return $row;
                } else {
                    $data = [];

                    if ($limit > 0) {
                        $this->db->limit($limit, $offset);
                    }

                    $query = $this->db->order_by('LENGTH(ata.ordering) asc, ata.ordering asc')
                        ->where('ata.is_active', 1)
                        ->get('asset_type_attributes ata');

                    if ($query->num_rows() > 0) {
                        foreach ($query->result() as $k => $row) {
                            $row->response_options = (!empty($row->response_options)) ? json_decode($row->response_options) : null;
                            $data[$k] = $row;
                        }
                        $this->session->set_flashdata('message', 'Asset Type attributes found');
                    } else {
                        // $data = [];
                        // $default = $this->db->where( '( ata.account_id IS NULL OR ata.account_id = 0 )' )
                        // ->get( 'asset_type_attributes `ata`' );
                        // foreach( $default->result() as $k => $row ){
                        // $row->response_options = ( !empty( $row->response_options ) ) ? json_decode( $row->response_options ) : null;
                        // $data[$k] = $row;
                        // }
                        // $this->session->set_flashdata('message','Asset Type attributes found (default list)');
                    }
                    $result = $data;
                }
            }
        }
        return $result;
    }


    /**
    * Update Response type
    */
    public function update_response_options($account_id = false, $response_type = false, $options = false, $action = 'add')
    {
        $result = false;
        if (!empty($account_id) && !empty($response_type) && !empty($options)) {
            $query = $this->db->select('rt.response_type_id, ro.option_value')
                ->join('attribute_response_type_options `ro`', 'ro.response_type_id = rt.response_type_id', 'left')
                ->where([ 'rt.account_id'=>$account_id, 'rt.response_type'=>$response_type ])
                ->group_by('ro.option_value')
                ->get('attribute_response_types rt');

            if ($query->num_rows() > 0) {
                $response_type_id = array_column($query->result_array(), 'response_type_id');
                $response_type_id = !empty($response_type_id[0]) ? $response_type_id[0] : false;
                $current_list 	  = array_map('strtolower', array_column($query->result_array(), 'option_value'));
                if (!empty($response_type_id) && $action == 'add') {
                    $add_opts 		= [];
                    if (!empty($current_list)) {
                        $new_options    = array_diff(array_map('strtolower', $options), $current_list);
                        foreach ($new_options as $opt) {
                            $add_opts[] = [
                                'response_type_id'=>$response_type_id,
                                'option_value'=>ucwords($opt),
                                'option_pass_value'=>strtolower($opt)
                            ];
                        }
                    } else {
                        foreach ($options as $opt) {
                            $add_opts[] = [
                                'response_type_id'=>$response_type_id,
                                'option_value'=>ucwords($opt),
                                'option_pass_value'=>strtolower($opt)
                            ];
                        }
                    }

                    if (!empty($new_options)) {
                        $this->db->insert_batch('attribute_response_type_options', $add_opts);
                        $this->session->set_flashdata('message', 'Response type options added successfully.');
                        $result = true;
                    }
                } else {
                    //Use this if you're deleting from the options
                    //$new_options  = array_diff( $current_list, array_map( 'strtolower', $options ) );
                }
            } else {
                //Copy options and recall this function
                $table_optons = [
                    'table_name'	=>'attribute_response_types',
                    'primary_key'	=>'response_type_id'
                ];
                $new_options = $this->account_service->copy_account_options($account_id, $table_optons);
                if (!empty($new_options)) {
                    $this->update_response_options($account_id, $response_type, $options);
                    $result = true;
                }
                $this->session->set_flashdata('message', 'This Response type does not exists or does not belong to you.');
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }
        return $result;
    }

    /*
    * Get Asset single records or multiple records - Version 2.0
    */
    public function get_assets($account_id = false, $asset_id = false, $asset_unique_id = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            if (!empty($where)) {
                $where = convert_to_array($where);
            }

            if (isset($where['asset_id'])) {
                $site_id = !empty($where['asset_id']) ? $where['asset_id'] : false;
                $this->db->where('asset.asset_id', $site_id);
                unset($where['asset_id']);
            }

            if (isset($where['grouped'])) {
                $grouped = !empty($where['grouped']) ? true : false;
                unset($where['grouped']);
            }

            if (isset($where['grouped_by'])) {
                switch(strtolower($where['grouped_by'])) {
                    case 'category':
                        $grouped_by = 'category';
                        break;

                    case 'floor':
                    case 'zone':
                        $grouped_by = 'floor';
                        $this->db->order_by('site_zones.sub_block_id, site_zones.zone_name');
                        break;
                }
                unset($where['grouped_by']);
            }

            if (isset($where['site_id'])) {
                $site_id = !empty($where['site_id']) ? $where['site_id'] : false;

                if (is_array($site_id)) {
                    $this->db->where_in('asset.site_id', $site_id);
                } else {
                    $this->db->where('asset.site_id', $site_id);
                }
                unset($where['site_id']);
            }

            if (isset($where['sub_block_id'])) {
                $sub_block_id = !empty($where['sub_block_id']) ? $where['sub_block_id'] : false;
                $this->db->where('asset.sub_block_id', $sub_block_id);
                unset($where['sub_block_id']);
            }

            if (isset($where['audit_type_id'])) {
                if ($where['audit_type_id'] > 0) {
                    $qry = $this->db->select('qb.asset_type_id', false)
                        ->where('qb.asset_type_id > 0')
                        ->where('qb.account_id', $account_id)
                        ->where('qb.audit_type_id', $where['audit_type_id'])
                        ->where('qb.is_active', 1)
                        ->group_by('qb.asset_type_id')
                        ->get('audit_question_bank qb');

                    if ($qry->num_rows() > 0) {
                        $asset_type_ids = array_column($qry->result_array(), 'asset_type_id');
                        $this->db->where_in('asset.asset_type_id', $asset_type_ids);
                    }
                }
                unset($where['audit_type_id']);
            }

            $this->db->select('asset.*, ata.attribute_name `primary_attribute`, atr.attribute_value, ata.is_mobile_visible, audit_result_statuses.result_status, site.site_name, site.site_postcodes, site.site_address_id `address_id`, site_sub_blocks.sub_block_name, site_zones.zone_name, site_locations.location_name, audit_categories.category_id, audit_categories.category_name, concat(user.first_name," ",user.last_name) `assigned_to`, concat(modifier.first_name," ",modifier.last_name) `last_modified_by`, asset_statuses.status_name `asset_status`, asset_statuses.status_group `asset_status_group`, asset_types.asset_type, asset_types.asset_group', false)
                ->join('asset_types', 'asset_types.asset_type_id = asset.asset_type_id', 'left')
                ->join('asset_type_attributes ata', 'ata.attribute_id = asset_types.primary_attribute_id', 'left')
                ->join('asset_attributes atr', 'atr.attribute_id = ata.attribute_id AND `atr`.`asset_id` = `asset`.`asset_id`', 'left')
                ->join('audit_categories', 'audit_categories.category_id = asset_types.category_id', 'left')
                ->join('user', 'user.id = asset.assignee', 'left')
                ->join('user modifier', 'modifier.id = asset.last_modified_by', 'left')
                ->join('asset_statuses', 'asset_statuses.status_id = asset.status_id', 'left')
                ->join('site', 'site.site_id = asset.site_id', 'left')
                ->join('audit_result_statuses', 'audit_result_statuses.audit_result_status_id = asset.audit_result_status_id', 'left')
                ->join('site_locations', 'site_locations.location_id = asset.location_id', 'left')
                ->join('site_zones', 'site_zones.zone_id = asset.zone_id', 'left')
                ->join('site_sub_blocks', 'site_sub_blocks.sub_block_id = site_zones.sub_block_id', 'left')
                ->where('asset.account_id', $account_id)
                ->where('asset.archived !=', 1)
                ->group_by('asset.asset_id');

            if (isset($where['asset_type_id'])) {
                if (!empty($where['asset_type_id'])) {
                    $this->db->where('asset.asset_type_id', $where['asset_type_id']);
                }
                unset($where['asset_type_id']);
            }

            if (isset($where['assignee'])) {
                if (!empty($where['assignee'])) {
                    $this->db->where('asset.assignee', $where['assignee']);
                }
                unset($where['assignee']);
            }

            if (isset($where['discipline_id'])) {
                if (!empty($where['discipline_id'])) {
                    $this->db->where('asset_types.discipline_id', $where['discipline_id']);
                }
                unset($where['discipline_id']);
            }

            if (!empty($where)) {
                //$this->db->where( $where );
            }

            if ($asset_id || $asset_unique_id) {
                $uniq_where = (!empty($asset_id)) ? ['asset.asset_id'=>$asset_id] : ((!empty($asset_unique_id)) ? ['asset.asset_unique_id'=>$asset_unique_id] : false);

                $row 		= $this->db->get_where('asset', $uniq_where)->row();

                if (!empty($row)) {
                    $profile_images 			= $this->document_service->get_document_list($account_id, $document_group = 'asset', ['asset_id'=>$row->asset_id], ['doc_type'=>'Profile Images']);
                    $row->profile_images 		= (!empty($profile_images[$account_id]['Profile Images'])) ? $profile_images[$account_id]['Profile Images'] : null;
                    $row->attribute_value 		= (is_json($row->attribute_value)) ? json_decode($row->attribute_value) : $row->attribute_value;
                    ## $row->attribute_value 		= ( is_array( $row->attribute_value ) 	? implode( " | ", $row->attribute_value  ) :  $row->attribute_value );
                    $row->attribute_value 		= (is_array($row->attribute_value) ? ( string ) implode(" | ", $row->attribute_value) : (string)$row->attribute_value);

                    $tracking_log	    		= $this->get_asset_change_logs($account_id, $row->asset_id);
                    $row->parent_assets 		= $this->get_linked_assets($account_id, false, $row->asset_id, ['inverse'=>1]);
                    $row->child_assets 			= $this->get_linked_assets($account_id, $row->asset_id);
                    $row->asset_attributes 		= $this->get_asset_attribute_values($account_id, $row->asset_type_id, $row->asset_id);
                    $row->tracking_log  		= (!empty($tracking_log)) ? $tracking_log : null;
                    $this->session->set_flashdata('message', 'Asset found');
                    $result 					= $row;
                } else {
                    $this->session->set_flashdata('message', 'Asset not found');
                }
                return $result;
            }


            if ($order_by) {
                $order = $this->ssid_common->_clean_order_by($order_by, $this->primary_tbl);
                if (!empty($order)) {
                    $this->db->order_by($order);
                }
            } else {
                $this->db->order_by('audit_categories.category_name, asset_types.asset_type, asset.asset_id');
            }

            if ($limit > 0) {
                $asset = $this->db->limit($limit, $offset);
            }

            $asset = $this->db->get('asset');

            if ($asset->num_rows() > 0) {
                $this->session->set_flashdata('message', 'Asset records found');
                if (!empty($grouped)) {
                    $data = [];
                    foreach ($asset->result() as $k => $row) {
                        $data[$row->asset_type][] = $row;
                    }
                    $result = $data;
                } elseif (!empty($grouped_by)) {
                    $data = [];
                    switch(strtolower($grouped_by)) {
                        case 'category':
                            foreach ($asset->result() as $k => $row) {
                                $data[$row->category_name][$row->asset_type][] = $row;
                            }
                            break;

                        case 'floor':
                        case 'zone':
                            foreach ($asset->result() as $k => $row) {
                                $floor_location = $row->zone_name;
                                $data[$row->zone_id]['floor_id'] 		= (isset($data[$floor_location]['total_assets'])) ? ($data[$floor_location]['total_assets'] + 1) : 1;
                                $data[$row->zone_id]['floor_name'] 		= $floor_location;
                                $data[$row->zone_id]['sub_block_name'] 	= $row->sub_block_name;
                                $data[$row->zone_id]['total_assets'] 	= (isset($data[$row->zone_id]['total_assets'])) ? ($data[$row->zone_id]['total_assets'] + 1) : 1;
                                $data[$row->zone_id]['floor_assets'][$row->category_name][$row->asset_type][] = $row;
                            }
                            break;
                    }
                    $result = $data;
                } else {
                    $result = $asset->result();
                }
            } else {
                $this->session->set_flashdata('message', 'Asset record(s) not found');
            }
        }

        return $result;
    }

    /*
    * Create new Asset v2
    */
    public function create_asset($account_id = false, $asset_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($asset_data)) {
            $data = $this->ssid_common->_data_prepare($asset_data);

            if (!empty($data)) {
                if (!empty($data['asset_unique_id'])) {
                    ## Auto Generate Unique Asset iD
                    if (!empty($asset_data['asset_type_id'])) {
                        $verify_asset_type = $this->_verify_asset_type($account_id, $asset_data['asset_type_id']);
                        if (!$verify_asset_type) {
                            $this->session->set_flashdata('message', 'This Asset Type record does not exist or does not belong to you.');
                            return false;
                        }

                        if (!empty($asset_data['site_id']) && !empty($verify_asset_type->auto_generate_unique_ids)) {
                            $data['asset_unique_id'] = $this->_auto_genearte_asset_unique_id($account_id, array_merge($asset_data, (array) $verify_asset_type));
                        }
                    } else {
                        #$this->session->set_flashdata( 'message', 'This Asset Type record does not exist or does not belong to you.' );
                        #return false;
                    }

                    $asset_attributes 	= !empty($data['asset_attributes']) ? $data['asset_attributes'] : false;
                    $parent_asset_id 	= !empty($data['parent_asset_id']) ? $data['parent_asset_id'] : false;
                    unset($data['asset_attributes']);
                    $asset_exists = $this->db->where('asset_unique_id', $data['asset_unique_id'])
                        ->where('asset.account_id', $account_id)
                        ->get('asset')->row();

                    if (!$asset_exists) {
                        $new_asset = $this->ssid_common->_filter_data('asset', $data);

                        if (empty($data['status_id'])) {
                            $default_asset_status = $this->get_default_asset_status($account_id);
                            if (!empty($default_asset_status->status_id)) {
                                $new_asset['status_id'] = $default_asset_status->status_id;
                            }
                        }

                        $new_asset['created_by']= $this->ion_auth->_current_user->id;
                        $this->db->insert('asset', $new_asset);
                        if ($this->db->trans_status() !== false) {
                            $asset_id 			= $this->db->insert_id();
                            $data['asset_id'] 	= (string)$asset_id;

                            if (!empty($asset_attributes)) {
                                $save_attribs = $this->_save_asset_attributes($account_id, $asset_id, $asset_attributes);
                            }

                            if (!empty($parent_asset_id)) {
                                $linked_assets = $this->link_assets($account_id, $parent_asset_id, ['linked_assets'=>[$data['asset_id']]]);
                            }

                            $result = $this->get_assets($account_id, $asset_id);
                            $this->session->set_flashdata('message', 'Asset record created successfully.');
                        }
                    } else {
                        $this->session->set_flashdata('message', 'Asset Unique ID already exists.');
                        return false;
                    }
                } else {
                    $this->session->set_flashdata('message', 'Missing Unique ID and/or IMEI number!');
                    return false;
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Asset data supplied.');
        }
        return $result;
    }


    /*
    * Update Asset record - v2
    */
    public function update_asset($account_id = false, $asset_id = false, $asset_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($asset_id) && !empty($asset_data)) {
            $data 			= $this->ssid_common->_data_prepare($asset_data);

            if (isset($data['linked_assets'])) {
                $linked_asset 	= !empty($data['linked_assets']) ? $data['linked_assets'] : [];
                unset($data['linked_assets']);
            }

            if (isset($data['asset_attributes'])) {
                $asset_attributes 	= !empty($data['asset_attributes']) ? $data['asset_attributes'] : false;
                unset($data['asset_attributes']);
            }

            if (!empty($data)) {
                ## Auto Generate Unique Asset iD
                if (!empty($data['asset_type_id'])) {
                    $verify_asset_type = $this->_verify_asset_type($account_id, $data['asset_type_id']);
                    if (!$verify_asset_type) {
                        $this->session->set_flashdata('message', 'This Asset Type record does not exist or does not belong to you.');
                        return false;
                    }

                    if (!empty($data['site_id']) && !empty($verify_asset_type->auto_generate_unique_ids)) {
                        $data['asset_unique_id'] = $this->_auto_genearte_asset_unique_id($account_id, array_merge($data, (array) $verify_asset_type));
                    }
                } else {
                    #$this->session->set_flashdata( 'message', 'This Asset Type record does not exist or does not belong to you.' );
                    #return false;
                }

                $conditions = [ 'account_id'=>$account_id, 'asset_unique_id'=>$data['asset_unique_id'] ];

                #Check if other asset exist with same Reference
                $check_conflict = $this->db->where('asset.asset_id !=', $asset_id)
                    ->limit(1)
                    ->get_where('asset', $conditions)
                    ->row();

                if (!$check_conflict) {
                    $asset_b4_update = $this->db->get_where('asset', [ 'account_id'=>$account_id, 'asset_id'=>$asset_id ])->row();

                    if (!empty($asset_b4_update)) {
                        //Check for location update
                        if (!empty($data['location_id'])) {
                            $this->ssid_common->update_location_record($account_id, $data['location_id'], $asset_id);
                        }

                        if (!empty($asset_b4_update->status_id)) {
                            ## Asset Status Group before update
                            $status_by_id = $this->get_asset_statuses($account_id, $asset_b4_update->status_id, false);

                            ## change status if is the first-assigning
                            if (($status_by_id[0]->status_group == 'unassigned') && (!empty($data['assignee']) || !empty($data['site_id']))) {
                                $status_by_group   = $this->get_asset_statuses($account_id, false, 'assigned');
                                $data['status_id'] = $status_by_group[0]->status_id;
                            }

                            ## Unset status only if both assignee and site_id are empty
                            if (empty($data['assignee']) && empty($data['site_id']) && in_array($status_by_id[0]->status_group, ['assigned'])) {
                                $status_by_group   = $this->get_asset_statuses($account_id, false, 'unassigned');
                                $data['status_id'] = $status_by_group[0]->status_id;
                            }
                        } else {
                            $status_by_group   = $this->get_asset_statuses($account_id, false, 'assigned');
                            if (!empty($data['assignee']) || !empty($data['site_id'])) {
                                $data['status_id'] = (!empty($data['status_id'])) ? $data['status_id'] : $status_by_group[0]->status_id;
                            }

                            ## Unset status only if both assignee and site_id are empty
                            if (empty($data['assignee']) && empty($data['site_id']) && ($data['status_id'] == $status_by_group[0]->status_id)) {
                                $status_by_group   = $this->get_asset_statuses($account_id, false, 'unassigned');
                                $data['status_id'] = $status_by_group[0]->status_id;
                            }
                        }

                        $update_data	 = $this->ssid_common->_filter_data('asset', $data);
                        if (empty($update_data['asset_notes'])) {
                            unset($update_data['asset_notes']);
                        }

                        $update_data['last_modified_by'] = $this->ion_auth->_current_user->id;
                        $this->db->where('asset_id', $asset_id);
                        $this->db->update('asset', $update_data);

                        if ($this->db->affected_rows() > 0 || ($this->db->trans_status() !== false)) {
                            if (!empty($linked_asset)) {
                                $this->link_assets($account_id, $asset_id, ['linked_assets'=>$linked_asset]);
                            }

                            if (!empty($asset_attributes)) {
                                $this->_save_asset_attributes($account_id, $asset_id, $asset_attributes);
                            }
                        }

                        ## Prepare log data
                        $new_status		 = !empty($data['status_id']) ? $data['status_id'] : $asset_b4_update->status_id;
                        $new_assignee	 = !empty($data['assignee']) ? $data['assignee'] : null;
                        $new_location	 = !empty($data['location_id']) ? $data['location_id'] : (!empty($asset_b4_update->location_id) ? $asset_b4_update->location_id : null);
                        $up_log_data = [
                            'previous_assignee'	=> $asset_b4_update->assignee,
                            'current_assignee'	=> $new_assignee,
                            'previous_status'	=> $asset_b4_update->status_id,
                            'current_status'	=> $new_status,
                            'previous_location'	=> (!empty($asset_b4_update->location_id) ? $asset_b4_update->location_id : null),
                            'current_location'	=> $new_location
                        ];

                        $log_data = array_merge($up_log_data, $data);
                        $this->create_asset_change_log($account_id, $asset_id, $log_data);

                        $result = $this->get_assets($account_id, $asset_id);

                        if (!empty($data['archived']) && $data['archived'] == 1) {
                            $this->session->set_flashdata('message', 'Asset record deleted successfully.');
                            $result = true;
                        } else {
                            $this->session->set_flashdata('message', 'Asset record updated successfully.');
                        }
                    } else {
                        $this->session->set_flashdata('message', 'This Asset record does not exist or does not belong to you.');
                    }
                } else {
                    $this->session->set_flashdata('message', 'This Asset Unique ID already exists for your account, please use a different one.');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Asset data supplied.');
        }
        return $result;
    }


    /* Process Asset Attributes */
    private function _save_asset_attributes($account_id = false, $asset_id = false, $responses = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($asset_id) && !empty($responses)) {
            $responses		= convert_to_array($responses);
            $responses		= (is_json($responses)) ? json_decode($responses) : $responses;
            $target_table 	= 'asset_attributes';
            $resp_data		= [];

            foreach ($responses as $k=>$row) {
                $attribute_id = !empty($row['attribute_id']) ? $row['attribute_id'] : '';
                $file_attr_id = !empty($row['attribute_id']) ? $row['attribute_id'] : '';

                switch (strtolower($row['response_type'])) {
                    case 'file':
                    case 'photo':
                    case 'image':
                        # Upload image
                        if (!empty($_FILES['user_files']['name'])) {
                            $postdata['asset_id']  		= (string) $asset_id;
                            $postdata['account_id']  	= $account_id;
                            $postdata['doc_type']  		= 'Profile Images';
                            $postdata['document_name']  = $row['attribute_name'];
                            #$uploaded_docs 	= $this->document_service->upload_files( $account_id, $postdata, $doc_group = 'asset' );
                            $uploaded_docs 	= $this->document_service->upload_profile_documents($account_id, $postdata, $doc_group = 'asset');
                            $image_url		= !empty($uploaded_docs['documents'][0]['document_link']) ? $uploaded_docs['documents'][0]['document_link'] : false;
                            $row['attribute_value'] 	= $image_url;
                        }
                        break;
                }

                //if( !empty( $row['attribute_value'] ) ){

                //Check for pipped string from Android
                // if( stripos( $row['attribute_value'], '|' ) !== false ){
                // $row['attribute_value'] = array_map( "trim", explode( "|", $row['attribute_value'] ) );
                // }

                if (!empty($row['attribute_value'])) {
                    if (is_array($row['attribute_value'])) {
                        $row['attribute_value'] = json_encode($row['attribute_value']);
                    } elseif (is_scalar($row['attribute_value'])) {
                        $row['attribute_value'] = $row['attribute_value'];
                    } elseif (is_object($row['attribute_value'])) {
                        $row['attribute_value'] = ( array ) $row['attribute_value'];
                    }
                } else {
                    $row['attribute_value'] = null;
                }

                $new_row 			 	 = $this->ssid_common->_filter_data($target_table, $row);
                $new_row['created_by']	 = $this->ion_auth->_current_user->id;
                $new_row['asset_id']	 = $asset_id;
                $resp_data[$k] 		 	 = $new_row;
                //}
            }

            ## Insert responses
            if (!empty($resp_data)) {
                $test_tracking_data = [];
                $test_tracking_data = [
                    'account_id'	=> $account_id,
                    'asset_id' 		=> $asset_id,
                    'responses_json'=> json_encode($responses),
                    'resp_data'		=> json_encode($resp_data),
                    'created_by'  	=> $this->ion_auth->_current_user->id,
                ];

                if (!empty($test_tracking_data)) {
                    $this->db->insert("test_requests_table", $test_tracking_data);
                }


                $conditions = ['asset_id'=>$asset_id];
                $this->db->where_in('attribute_id', array_column($resp_data, 'attribute_id'))
                    ->where($conditions)->delete($target_table);

                $this->ssid_common->_reset_auto_increment($target_table, 'id');

                $this->db->insert_batch($target_table, $resp_data);
            }
            $result = ($this->db->trans_status() !== false) ? true : false;
        }
        return $result;
    }


    /*
    * Get Asset attribute attribute_values
    */
    public function get_asset_attribute_values($account_id = false, $asset_type_id = false, $asset_id = false)
    {
        $result = null;
        if (!empty($account_id) && !empty($asset_id) && !empty($asset_type_id)) {
            $asset_type_attributes = $this->get_asset_type_attributes($account_id, $asset_type_id, false, false, false, $limit = -1);

            if (!empty($asset_type_attributes)) {
                foreach ($asset_type_attributes as $k => $value) {
                    $row = $this->db->select('asset_attributes.*, ata.attribute_id , ata.attribute_name, ata.response_type, ata.response_options, ata.accepted_file_types, ata.is_mandatory, ata.is_mobile_visible, ata.photo_required, ata.ordering', false)
                        ->join('asset_attributes', 'ata.attribute_id = asset_attributes.attribute_id', 'left')
                        ->where('ata.is_active', 1)
                        ->where('asset_attributes.asset_id', $asset_id)
                        ->where('asset_attributes.attribute_id', $value->attribute_id)
                        ->order_by('LENGTH( asset_attributes.ordering ) asc, asset_attributes.ordering asc')
                        ->order_by('asset_attributes.attribute_id')
                        ->get('asset_type_attributes `ata`')
                        ->row();

                    if (!empty($row)) {
                        $row->response_options 	= (is_json($row->response_options)) ? json_decode($row->response_options) : $row->response_options;
                        $row->attribute_value 	= (is_json($row->attribute_value)) ? json_decode($row->attribute_value) : $row->attribute_value;
                        #$row->attribute_value 	= ( is_array( $row->attribute_value ) 	? implode( " | ", $row->attribute_value  ) : $row->attribute_value );
                        $row->attribute_value 	= (is_scalar($row->attribute_value) ? ( string ) $row->attribute_value : $row->attribute_value);
                        $result[$k] = $row;
                    } else {
                        $response_options 		= (is_json($value->response_options)) ? json_decode($value->response_options) : $value->response_options;
                        $result[$k] = (object)[
                            'id'				=> null,
                            'asset_id' 			=> ( string ) $asset_id,
                            'attribute_id' 		=> $value->attribute_id,
                            'attribute_name' 	=> $value->attribute_name,
                            'attribute_value' 	=> null,
                            'ordering' 			=> $value->ordering,
                            'date_created' 		=> null,
                            'created_by' 		=> null,
                            'response_type' 	=> $value->response_type,
                            'response_options' 	=> $response_options,
                            'accepted_file_types'=> $value->accepted_file_types ,
                            'is_mandatory' 		=> $value->is_mandatory,
                            'is_mandatory' 		=> $value->is_mobile_visible,
                            'photo_required' 	=> $value->photo_required,
                        ];
                    }
                }
            }

            if (!empty($result)) {
                $this->session->set_flashdata('message', 'Asset attribute values found');
            } else {
                $this->session->set_flashdata('message', 'No records found');
            }
        }
        return $result;
    }


    /*
    * 	Delete Asset Type
    */
    public function delete_asset_type($account_id = false, $asset_type_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($asset_type_id)) {
            $where = [
                'account_id'	=>$account_id,
                'asset_type_id'	=>$asset_type_id
            ];

            $data = [
                'is_active' 		=> 0,
                'last_modified_by' 	=> $this->ion_auth->_current_user->id
            ];
            $this->db->where($where)->update('asset_types', $data);

            if ($this->db->affected_rows() > 0) {
                $this->session->set_flashdata('message', 'Record deleted successfully');
                $result = true;

                $attributes_exists = $this->db->get_where('asset_type_attributes', ['account_id' => $account_id, 'asset_type_id' => $asset_type_id ])->result();

                if (!empty($attributes_exists)) {
                    foreach ($attributes_exists as $attr) {
                        $this->db->delete('asset_type_attributes', ['account_id' => $account_id, "attribute_id" => $attr->attribute_id]);
                    }
                }
            }
        } else {
            $this->session->set_flashdata('message', 'Invalid Asset Type ID or Account ID');
        }

        return $result;
    }


    public function get_asset_categories($account_id = false, $category_id = false, $where = false)
    {
        $result = false;
        if (!empty($account_id)) {
            if (!empty($category_id)) {
                $this->db->where("category_id", $category_id);
            }
            $this->db->select("audit_categories.category_id, audit_categories.category_name, audit_categories.account_id, audit_categories.is_active", false);
            $this->db->where("account_id", $account_id);
            $this->db->where("is_active", 1);

            $this->db->order_by("audit_categories.category_name ASC");

            $query = $this->db->get("audit_categories");

            if (($query->num_rows() !== null) && ($query->num_rows())) {
                $data_set = $query->result();


                if (!empty($category_id)) {
                    $result = $data_set[0];
                } else {
                    $result = $data_set;
                }
                $this->session->set_flashdata('message', 'Category(ies) found');
            } else {
                $this->session->set_flashdata('message', 'Category(ies) not found');
            }
        } else {
            $this->session->set_flashdata('message', 'Invalid or missing Account ID');
        }
        return $result;
    }

    /**
    /* Delete an Asset Type Attribute resource
    */
    public function delete_asset_type_attribute($account_id = false, $asset_type_id = false, $attribute_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($attribute_id)) {
            if (!empty($asset_type_id)) {
                $this->db->where('asset_type_id', $asset_type_id); #?? not sure why I need this, but leave it here for now!
            }

            $conditions 	= [ 'account_id'=>$account_id,'attribute_id'=>$attribute_id ];
            $record_exists 	= $this->db->get_where('asset_type_attributes', $conditions)->row();

            if (!empty($record_exists)) {
                ## Drop preexisting attributes, children!
                $this->db->where('attribute_id', $attribute_id)
                    ->delete('asset_attributes');

                if ($this->db->trans_status() !== false) {
                    $this->ssid_common->_reset_auto_increment('asset_attributes', 'id');
                }

                ## Then the parent
                $this->db->where('attribute_id', $attribute_id)
                    ->delete('asset_type_attributes');

                if ($this->db->trans_status() !== false) {
                    $this->ssid_common->_reset_auto_increment('asset_type_attributes', 'attribute_id');
                    $this->session->set_flashdata('message', 'Asset Type Attribute removed successfully.');
                    $result = true;
                }
            } else {
                $this->session->set_flashdata('message', 'Invalid Asset Type Attribute ID.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Asset Type Attribute record found.');
        }
        return $result;
    }

    /** Get Asset Types By Category **/
    public function get_asset_types_by_category($account_id = false, $where = false)
    {
        $result = false;

        if (!empty($account_id)) {
            $where = convert_to_array($where);

            if (isset($where['grouped'])) {
                if (!empty($where['grouped'])) {
                    $grouped = 1;
                }
                unset($where['grouped']);
            }

            if (isset($where['site_id'])) {
                if (!empty($where['site_id'])) {
                    $asset_types  = $this->db->select('asset.asset_type_id', false)
                        ->where('asset.site_id', $where['site_id'])
                        ->where('( asset.archived != 1 OR asset.archived IS NULL )')
                        ->where('asset.account_id', $account_id)
                        ->group_by('asset.asset_type_id')
                        ->get('asset');
                    if ($asset_types->num_rows() > 0) {
                        $asset_type_ids = $asset_types->result_array();
                        $asset_type_ids = array_column($asset_type_ids, 'asset_type_id');
                        if (!empty($asset_type_ids)) {
                            $this->db->where_in('asset_types.asset_type_id', $asset_type_ids);
                        }
                    }
                }
                unset($where['site_id']);
            }

            $query  = $this->db->select('asset_types.*, audit_categories.category_name, audit_categories.category_group, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false)
                ->join('user creater', 'creater.id = asset_types.created_by', 'left')
                ->join('user modifier', 'modifier.id = asset_types.last_modified_by', 'left')
                ->join('audit_categories', 'asset_types.category_id = audit_categories.category_id', 'left')
                ->where('asset_types.is_active', 1)
                ->where('asset_types.account_id', $account_id)
                ->get('asset_types');

            if ($query->num_rows() > 0) {
                if (!empty($grouped)) {
                    $data = [];
                    foreach ($query->result() as $k => $row) {
                        $data[$row->category_name]['category'] 		= [ 'category_id'=>$row->category_id, 'category_name'=>$row->category_name];
                        $data[$row->category_name]['asset_types'][] = $row;
                    }
                    $result = $data;
                } else {
                    $result = $query->result();
                }

                $this->session->set_flashdata('message', 'Asset type data found.');
            } else {
                $this->session->set_flashdata('message', 'No asset type data found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }
        return $result;
    }


    /** Get Building Asset Types By Category **/
    public function get_assets_by_asset_type($account_id = false, $where = false)
    {
        $result = false;

        if (!empty($account_id)) {
            $where = convert_to_array($where);

            if (isset($where['grouped'])) {
                if (!empty($where['grouped'])) {
                    $grouped = 1;
                }
                unset($where['grouped']);
            }

            if (isset($where['category_id'])) {
                if (!empty($where['category_id'])) {
                    $category_id = (is_array($where['category_id'])) ? $where['category_id'] : [$where['category_id']];
                    $asset_types  = $this->db->select('asset_types.asset_type_id', false)
                        ->where_in('asset_types.category_id', $category_id)
                        ->where('asset_types.account_id', $account_id)
                        ->get('asset_types');

                    if ($asset_types->num_rows() > 0) {
                        $asset_type_ids = $asset_types->result_array();
                        $asset_type_ids = array_column($asset_type_ids, 'asset_type_id');
                        if (!empty($asset_type_ids)) {
                            $this->db->where_in('asset.asset_type_id', $asset_type_ids);
                        }
                    }
                }
                unset($where['category_id']);
            }

            if (isset($where['site_id'])) {
                if (!empty($where['site_id'])) {
                    $this->db->where('asset.site_id', $where['site_id']);
                }
                unset($where['site_id']);
            }

            if (isset($where['asset_type_id'])) {
                if (!empty($where['asset_type_id'])) {
                    $asset_type_id = (is_array($where['asset_type_id'])) ? $where['asset_type_id'] : [$where['asset_type_id']];
                    $this->db->where_in('asset.asset_type_id', $asset_type_ids);
                }
                unset($where['asset_type_id']);
            }

            $query = $this->db->select('asset.asset_id, asset.asset_unique_id, ata.attribute_name `primary_attribute`, atr.attribute_value, ata.is_mobile_visible, asset.account_id, asset.site_id, asset.asset_type_id, asset_types.asset_type, asset_types.category_id', false)
                ->join('asset_types', 'asset_types.asset_type_id = asset.asset_type_id', 'left')
                ->join('asset_type_attributes ata', 'ata.attribute_id = asset_types.primary_attribute_id', 'left')
                ->join('asset_attributes atr', 'atr.attribute_id = ata.attribute_id AND `atr`.`asset_id` = `asset`.`asset_id`', 'left')
                ->where('asset.account_id', $account_id)
                ->where('( asset.archived != 1 OR asset.archived IS NULL )')
                ->group_by('asset.asset_id')
                ->get('asset');

            if ($query->num_rows() > 0) {
                if (!empty($grouped)) {
                    $data = [];
                    foreach ($query->result() as $k => $row) {
                        $data[$row->asset_type_id]['asset_type'] = [ 'asset_type_id'=>$row->asset_type_id, 'asset_type'=>$row->asset_type];
                        $data[$row->asset_type_id]['assets'][] 	 = $row;
                    }
                    $result = $data;
                } else {
                    $result = $query->result();
                }

                $this->session->set_flashdata('message', 'Assets data found.');
            } else {
                $this->session->set_flashdata('message', 'No asset data found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }
        return $result;
    }


    /*
    * 	Get Asset attribute attribute_values - newest approach, left the old one for the legacy reason
    */
    public function get_asset_attribute_values2($account_id = false, $asset_type_id = false, $asset_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = null;
        if (!empty($account_id)) {
            if (!empty($asset_id) && !empty($asset_type_id)) {
                $asset_type_attributes = $this->get_asset_type_attributes($account_id, $asset_type_id);

                if (!empty($asset_type_attributes)) {
                    foreach ($asset_type_attributes as $k => $value) {
                        $row = $this->db->select('asset_attributes.*, ata.attribute_id , ata.attribute_name, ata.response_type, ata.response_options, ata.accepted_file_types, ata.is_mandatory, ata.is_mobile_visible, ata.photo_required, ata.ordering', false)
                            ->join('asset_attributes', 'ata.attribute_id = asset_attributes.attribute_id', 'left')
                            ->where('ata.is_active', 1)
                            ->where('asset_attributes.asset_id', $asset_id)
                            ->where('asset_attributes.attribute_id', $value->attribute_id)
                            ->order_by('LENGTH( asset_attributes.ordering ) asc, asset_attributes.ordering asc')
                            ->order_by('asset_attributes.attribute_id')
                            ->get('asset_type_attributes `ata`')
                            ->row();

                        if (!empty($row)) {
                            $row->response_options 	= (is_json($row->response_options)) ? json_decode($row->response_options) : $row->response_options;
                            ## $row->attribute_value 	= ( is_json( $row->attribute_value ) ) 	? json_decode( $row->attribute_value )  : $row->attribute_value;
                            #$row->attribute_value 	= ( is_array( $row->attribute_value ) 	? implode( " | ", $row->attribute_value  ) : $row->attribute_value );
                            $row->attribute_value 	= (is_scalar($row->attribute_value) ? ( string ) $row->attribute_value : $row->attribute_value);
                            $result[$k] = $row;
                        } else {
                            $response_options 		= (is_json($value->response_options)) ? json_decode($value->response_options) : $value->response_options;
                            $result[$k] = (object)[
                                'id'				=> null,
                                'asset_id' 			=> ( string ) $asset_id,
                                'attribute_id' 		=> $value->attribute_id,
                                'attribute_name' 	=> $value->attribute_name,
                                'attribute_value' 	=> null,
                                'ordering' 			=> $value->ordering,
                                'date_created' 		=> null,
                                'created_by' 		=> null,
                                'response_type' 	=> $value->response_type,
                                'response_options' 	=> $response_options,
                                'accepted_file_types'=> $value->accepted_file_types ,
                                'is_mandatory' 		=> $value->is_mandatory,
                                'is_mandatory' 		=> $value->is_mobile_visible,
                                'photo_required' 	=> $value->photo_required,
                            ];
                        }
                    }
                }
            } else {
                $asset_sql = 'SELECT asset_id FROM asset WHERE asset.`account_id` = '.$account_id.' AND archived != 1 ';

                $main_sql  = 'SELECT * FROM `asset_attributes` WHERE asset_id IN ( '.$asset_sql.' )';

                if ($limit > 0) {
                    $main_sql	.= ' LIMIT '.$limit;
                }

                $query = $this->db->query($main_sql);
                if ($query->num_rows() > 0) {
                    foreach ($query->result() as $key => $row) {
                        $row->attribute_value1 = is_json($row->attribute_value) ? json_decode($row->attribute_value) : $row->attribute_value ;
                        $result[$key] = $row;
                    }

                    $this->session->set_flashdata('message', 'Asset attribute values found');
                } else {
                    $this->session->set_flashdata('message', 'No records found');
                }
            }

            if (!empty($result)) {
                $this->session->set_flashdata('message', 'Asset attribute values found');
            } else {
                $this->session->set_flashdata('message', 'No records found');
            }
        }
        return $result;
    }


    /** Verify Asset Type **/
    public function _verify_asset_type($account_id = false, $asset_type_id = false, $asset_type = false)
    {
        $result = false;
        if (!empty($account_id) && (!empty($asset_type_id) || !empty($asset_type))) {
            $this->db->select('account_id, asset_type_id, asset_type, discipline_id, primary_attribute_id, auto_generate_unique_ids', false);
            if (!empty($asset_type_id)) {
                $verify_asset_type = $this->db->get_where('asset_types', [ 'account_id'=>$account_id, 'asset_type_id'=>$asset_type_id ])->row();
            } elseif (!empty($asset_type)) {
                $verify_asset_type = $this->db->where('account_id', $account_id)
                    ->where('asset_type', $asset_type)
                    ->or_where('asset_type_ref', strip_all_whitespace($asset_type))
                    ->or_where('job_group', strip_all_whitespace($asset_type))
                    ->limit(1)
                    ->get('asset_types')->row();
            }

            if (!empty($verify_asset_type)) {
                $this->session->set_flashdata('message', 'Asset Type verified.');
                $result = $verify_asset_type;
            } else {
                $this->session->set_flashdata('message', 'Invalid Asset Type.');
            }
        }
        return $result;
    }

    /** Auto Generate the Asset Unique ID **/
    private function _auto_genearte_asset_unique_id($account_id = false, $data = false)
    {
        if (!empty($account_id) && !empty($data)) {
            $asset_unique_id 			= '';
            $uniq_id_parts 			 	= explode('_', $data['asset_unique_id']);
            $data['asset_unique_id'] 	= (is_array($uniq_id_parts)) ? end($uniq_id_parts) : $data['asset_unique_id'];

            if (!empty($data['site_id'])) {
                $site_details = $this->db->select('site_id, site_name, site_reference', false)->get_where('site', [ 'account_id'=> $account_id, 'site_id'=> $data['site_id']  ])->row();
                if (!empty($site_details->site_name)) {
                    $site_snips = preg_split('/\s+/', $site_details->site_name);
                    $site_abbrev = '';
                    foreach ($site_snips as $w) {
                        $site_abbrev .= trim($w[0]);
                    }
                    $asset_unique_id .= trim($site_abbrev).'_'.trim($data['site_id']).'_'.trim($data['asset_unique_id']);
                } else {
                    $asset_unique_id = trim($data['asset_unique_id']);
                }
            } else {
                if (!empty($data['asset_type'])) {
                    $asset_type_snips = preg_split('/\s+/', $data['asset_type']);
                    $asset_type_abbrev = '';
                    foreach ($asset_type_snips as $a) {
                        $asset_type_abbrev .= $a[0];
                    }
                    $asset_unique_id .= $account_id.$asset_type_abbrev.'_'.$data['asset_type_id'].'_'.$data['asset_unique_id'];
                } else {
                    $asset_unique_id = $data['asset_unique_id'];
                }
            }
        } else {
            $asset_unique_id = $data['asset_unique_id'];
        }
        return strtoupper($asset_unique_id);
    }
}
