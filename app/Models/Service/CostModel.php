<?php

namespace App\Models\Service;

use App\Adapter\Model;

class CostModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $section 	   = explode("/", $_SERVER["SCRIPT_NAME"]);
        $this->app_root= $_SERVER["DOCUMENT_ROOT"]."/".$section[1]."/";
        $this->app_root= str_replace('/index.php', '', $this->app_root);
    }

    /** Searchable fields **/
    private $searchable_fields  = [ 'cost_item.cost_item_id', 'cost_item_name', 'cost_item.cost_item_type' ];

    /** Primary table name **/
    private $primary_tbl = 'cost_item';

    /*
    *	Get list of Cost items and search though it
    */
    public function get_cost_items($account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select('cost_item.*, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false)
                ->join('user creater', 'creater.id = cost_item.created_by', 'left')
                ->join('user modifier', 'modifier.id = cost_item.last_modified_by', 'left')
                ->where('cost_item.is_active', 1)
                ->where('cost_item.account_id', $account_id);

            $where = $raw_where = convert_to_array($where);

            if (isset($where['cost_item_id'])) {
                if (!empty($where['cost_item_id'])) {
                    $row = $this->db->get_where('cost_item', [ /*'cost_item.account_id'=>$account_id,*/ 'cost_item_id'=>$where['cost_item_id'] ])->row();
                    if (!empty($row)) {
                        $result = $row;
                        $this->session->set_flashdata('message', 'Cost item data found');
                        return $result;
                    } else {
                        $this->session->set_flashdata('message', 'Cost item data not found');
                        return false;
                    }
                }
                unset($where['cost_item_id']);
            }

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

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->searchable_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                if (isset($where['asset_id'])) {
                    if (!empty($where['asset_id'])) {
                        $this->db->where('cost_item.asset_id', $where['asset_id']);
                    }
                    unset($where['asset_id']);
                }

                if (isset($where['person_id'])) {
                    if (!empty($where['person_id'])) {
                        $this->db->where('cost_item.person_id', $where['person_id']);
                    }
                    unset($where['person_id']);
                }

                if (isset($where['site_id'])) {
                    if (!empty($where['site_id'])) {
                        $this->db->where('cost_item.site_id', $where['site_id']);
                    }
                    unset($where['site_id']);
                }

                if (isset($where['vehicle_reg'])) {
                    if (!empty($where['vehicle_reg'])) {
                        $this->db->where('cost_item.vehicle_reg', $where['vehicle_reg']);
                    }
                    unset($where['vehicle_reg']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            if (!empty($order_by)) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('cost_item_id DESC, cost_item_name');
            }

            $query = $this->db->get('cost_item');

            if ($query->num_rows() > 0) {
                $result_data = $query->result();

                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $result->records 			= $result_data;
                $counters 					= $this->cost_items_totals($account_id, $search_term, $raw_where);
                $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
                $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
                $result->counters->limit  	= (!empty($apply_limit)) ? $limit : $result->counters->total;
                $result->counters->offset 	= $offset;

                $this->session->set_flashdata('message', 'Cost Items data found');
            } else {
                $this->session->set_flashdata('message', 'No data found');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }

    /** Get Cost item counters **/
    public function cost_items_totals($account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('cost_item.cost_item_id', false)
            ->join('user creater', 'creater.id = cost_item.created_by', 'left')
            ->join('user modifier', 'modifier.id = cost_item.last_modified_by', 'left')
            ->where('cost_item.is_active', 1)
            ->where('cost_item.account_id', $account_id);

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

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->searchable_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                if (isset($where['asset_id'])) {
                    if (!empty($where['asset_id'])) {
                        $this->db->where('cost_item.asset_id', $where['asset_id']);
                    }
                    unset($where['asset_id']);
                }

                if (isset($where['person_id'])) {
                    if (!empty($where['person_id'])) {
                        $this->db->where('cost_item.person_id', $where['person_id']);
                    }
                    unset($where['person_id']);
                }

                if (isset($where['site_id'])) {
                    if (!empty($where['site_id'])) {
                        $this->db->where('cost_item.site_id', $where['site_id']);
                    }
                    unset($where['site_id']);
                }

                if (isset($where['vehicle_reg'])) {
                    if (!empty($where['vehicle_reg'])) {
                        $this->db->where('cost_item.vehicle_reg', $where['vehicle_reg']);
                    }
                    unset($where['vehicle_reg']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $query 			  = $this->db->from('cost_item')->count_all_results();
            $results['total'] = !empty($query) ? $query : 0;
            $limit 			  = (!empty($apply_limit)) ? $limit : $results['total'];
            $results['pages'] = !empty($query) ? ceil($query / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }

    /*
    * Create new Asset
    */
    public function create_cost_item($account_id = false, $cost_item_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($cost_item_data)) {
            $data = $this->ssid_common->_data_prepare($cost_item_data);
            if (!empty($data)) {
                $new_cost_item 				= $this->ssid_common->_filter_data('cost_item', $data);
                $new_cost_item['created_by']= $this->ion_auth->_current_user->id;
                $this->db->insert('cost_item', $new_cost_item);
                if ($this->db->trans_status() !== false) {
                    $data['cost_item_id'] = $this->db->insert_id();
                    $result = $this->get_cost_items($account_id, false, [ 'cost_item_id'=>$data['cost_item_id'] ]);
                    $result = $this->db->get_where('cost_item', [ 'account_id' => $account_id, 'cost_item_id' => $data['cost_item_id'] ])->row();
                    $this->session->set_flashdata('message', 'Cost Item created successfully.');
                    return $result;
                }
            }
            $this->session->set_flashdata('message', 'Error parsing your supplied data. Request aborted');
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }
        return $result;
    }

    /*
    * Delete Cost Item record
    */
    public function delete_cost_item($account_id = false, $cost_item_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($cost_item_id)) {
            $conditions 		= ['account_id'=>$account_id,'cost_item_id'=>$cost_item_id];
            $cost_item_exists 	= $this->db->get_where('cost_item', $conditions)->row();
            if (!empty($cost_item_exists)) {
                $this->db->where($conditions)->delete('cost_item');
                if ($this->db->trans_status() !== false) {
                    $this->session->set_flashdata('message', 'Record deleted successfully.');
                    $result = true;
                }
            } else {
                $this->session->set_flashdata('message', 'Invalid Cost Item ID');
            }
        } else {
            $this->session->set_flashdata('message', 'No Cost item record found.');
        }
        return $result;
    }

    /** Get Asset types **/
    public function get_cost_item_types($account_id = false, $category_id = false, $category = false, $grouped = false)
    {
        $result = null;
        if ($account_id) {
            $this->db->where('cost_item_types.account_id', $account_id);
        } else {
            $this->db->where('( cost_item_types.account_id IS NULL OR cost_item_types.account_id = "" )');
        }

        $query = $this->db->select('cost_item_types.*', false)
            ->order_by('cost_item_types.cost_item_type')
            ->where('cost_item_types.is_active', 1)
            ->get('cost_item_types');

        if ($query->num_rows() > 0) {
            $result = $query->result();
        } else {
            $result = $this->get_cost_item_types();
        }

        return $result;
    }
}
