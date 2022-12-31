<?php

namespace Application\Modules\Service\Models;

use App\Adapter\Model;

class PremisesModel extends Model
{
	/**
	 * @var \Application\Modules\Service\Models\DocumentHandlerModel $document_service
	 */
	private $document_service;

	public function __construct()
    {
        parent::__construct();
        $section 	   = explode("/", $_SERVER["SCRIPT_NAME"]);
        $this->app_root= $_SERVER["DOCUMENT_ROOT"]."/".$section[1]."/";
        $this->app_root= str_replace('/index.php', '', $this->app_root);
        $this->document_service = new DocumentHandlerModel();
    }

    /** Searchable fields **/
    private $searchable_fields  			= ['premises.premises_id', 'premises.premises_ref','premises.premises_desc', 'premises.premises_type_id'];
    private $premises_type_search_fields  	= ['premises_type'];
    private $premises_type_attribs_search  	= ['attribute_name', 'response_type'];
    private $file_response_types			= ['file','signature'];

    /** Primary table name **/
    private $primary_tbl = 'premises';

    /*
    * Create new Premises v2
    */
    public function create_premises($account_id = false, $premises_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($premises_data)) {
            $data = $this->ssid_common->_data_prepare($premises_data);

            if (!empty($data)) {
                $data['premises_ref'] = !empty($data['premises_ref']) ? $data['premises_ref'] : $this->_auto_genearte_premises_ref($account_id, $data);
                if (!empty($premises_data['premises_type_id'])) {
                    $verify_premises_type = $this->_verify_premises_type($account_id, $premises_data['premises_type_id']);
                    if (!$verify_premises_type) {
                        $this->session->set_flashdata('message', 'This Premises Type record does not exist or does not belong to you.');
                        return false;
                    }
                } else {
                    $this->session->set_flashdata('message', 'This Premises Type record does not exist or does not belong to you.');
                    return false;
                }

                $premises_attributes 	= !empty($data['premises_attributes']) ? $data['premises_attributes'] : false;
                $parent_premises_id 	= !empty($data['parent_premises_id']) ? $data['parent_premises_id'] : false;
                unset($data['premises_attributes']);
                $premises_exists = $this->db->where('premises_ref', $data['premises_ref'])
                    ->where('premises.account_id', $account_id)
                    ->get('premises')->row();

                if (!$premises_exists) {
                    $new_premises = $this->ssid_common->_filter_data('premises', $data);

                    $new_premises['created_by']= $this->ion_auth->_current_user->id;
                    $this->db->insert('premises', $new_premises);
                    if ($this->db->trans_status() !== false) {
                        $premises_id 			= $this->db->insert_id();
                        $data['premises_id'] 	= (string)$premises_id;

                        if (!empty($premises_attributes)) {
                            $save_attribs = $this->_save_premises_attributes($account_id, $premises_id, $premises_attributes);
                        }

                        $result = $this->get_premises($account_id, $premises_id);
                        $this->session->set_flashdata('message', 'Premises record created successfully.');
                    }
                } else {
                    $this->session->set_flashdata('message', 'Premises Reference already exists.');
                    return false;
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Premises data supplied.');
        }
        return $result;
    }


    /*
    * Update Premises record
    */
    public function update_premises($account_id = false, $premises_id = false, $premises_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($premises_id) && !empty($premises_data)) {
            $data 			= $this->ssid_common->_data_prepare($premises_data);

            if (isset($data['premises_attributes'])) {
                $premises_attributes 	= !empty($data['premises_attributes']) ? $data['premises_attributes'] : false;
                unset($data['premises_attributes']);
            }

            if (!empty($data)) {
                $data['premises_ref'] = !empty($data['premises_ref']) ? $data['premises_ref'] : $this->_auto_genearte_premises_ref($account_id, $data);

                ## Auto Generate Unique Premises iD
                if (!empty($data['premises_type_id'])) {
                    $verify_premises_type = $this->_verify_premises_type($account_id, $data['premises_type_id']);
                    if (!$verify_premises_type) {
                        $this->session->set_flashdata('message', 'This Premises Type record does not exist or does not belong to you.');
                        return false;
                    }
                } else {
                    $this->session->set_flashdata('message', 'This Premises Type record does not exist or does not belong to you.');
                    return false;
                }

                $conditions = [ 'account_id'=>$account_id, 'premises_ref'=>$data['premises_ref'] ];

                #Check if other premises exist with same Reference
                $check_conflict = $this->db->where('premises.premises_id !=', $premises_id)
                    ->limit(1)
                    ->get_where('premises', $conditions)
                    ->row();

                if (!$check_conflict) {
                    $premises_b4_update = $this->db->get_where('premises', [ 'account_id'=>$account_id, 'premises_id'=>$premises_id ])->row();

                    if (!empty($premises_b4_update)) {
                        $update_data	 = $this->ssid_common->_filter_data('premises', $data);
                        if (empty($update_data['premises_notes'])) {
                            unset($update_data['premises_notes']);
                        }

                        $update_data['last_modified_by'] = $this->ion_auth->_current_user->id;
                        $this->db->where('premises_id', $premises_id);
                        $this->db->update('premises', $update_data);

                        if ($this->db->affected_rows() > 0 || ($this->db->trans_status() !== false)) {
                            if (!empty($premises_attributes)) {
                                $this->_save_premises_attributes($account_id, $premises_id, $premises_attributes);
                            }
                        }

                        $result = $this->get_premises($account_id, $premises_id);

                        if (!empty($data['archived']) && $data['archived'] == 1) {
                            $this->session->set_flashdata('message', 'Premises record deleted successfully.');
                            $result = true;
                        } else {
                            $this->session->set_flashdata('message', 'Premises record updated successfully.');
                        }
                    } else {
                        $this->session->set_flashdata('message', 'This Premises record does not exist or does not belong to you.');
                    }
                } else {
                    $this->session->set_flashdata('message', 'This Premises Reference already exists for your account, please use a different one.');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Premises data supplied.');
        }
        return $result;
    }

    /*
    * Delete Premises record
    */
    public function delete_premises($account_id = false, $premises_id = false)
    {
        $result = false;
        if ($this->account_service->check_account_status($account_id) && !empty($premises_id)) {
            $conditions 	= ['account_id'=>$account_id,'premises_id'=>$premises_id];
            $premises_exists 	= $this->db->get_where('premises', $conditions)->row();
            if (!empty($premises_exists)) {
                $data = ['premises_ref'=>strtoupper($premises_exists->premises_ref.'_ARC'), 'archived'=>1];
                $this->db->where($conditions)->update('premises', $data);
                if ($this->db->trans_status() !== false) {
                    $this->session->set_flashdata('message', 'Record deleted successfully.');
                    $result = true;
                }
            } else {
                $this->session->set_flashdata('message', 'Invalid Premises ID.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Premises record found.');
        }
        return $result;
    }


    /*
    *	Get list of Premises types / searchable
    */
    public function get_premises_types($account_id = false, $premises_type_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select('premises_types.*, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`, account_discipline.account_discipline_name, account_discipline.account_discipline_image_url `discipline_image_url`, premises_type_attributes.attribute_name', false)
                ->join('user creater', 'creater.id = premises_types.created_by', 'left')
                ->join('user modifier', 'modifier.id = premises_types.last_modified_by', 'left')
                ->join('account_discipline', 'account_discipline.discipline_id = premises_types.discipline_id', 'left')
                ->join('premises_type_attributes', 'premises_type_attributes.attribute_id = premises_types.primary_attribute_id', 'left')
                ->where('premises_types.is_active', 1)		## Shall we show non-active too?
                ->where('premises_types.account_id', $account_id);

            $where = $raw_where = convert_to_array($where);

            if (!empty($premises_type_id) || isset($where['premises_type_id'])) {
                $premises_type_id	= (!empty($premises_type_id)) ? $premises_type_id : $where['premises_type_id'];
                if (!empty($premises_type_id)) {
                    $row = $this->db->get_where('premises_types', ['premises_types.premises_type_id'=>$premises_type_id ])->row();

                    if (!empty($row)) {
                        $result 				= ( object ) ['records'=>$row];
                        $this->session->set_flashdata('message', 'Premises Type data found');
                        return $result;
                    } else {
                        $this->session->set_flashdata('message', 'Premises Type data not found');
                        return false;
                    }
                }
                unset($where['premises_type_id'], $where['premises_type_ref']);
            }

            if (!empty($search_term)) {
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->premises_type_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->premises_type_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                if (isset($where['premises_type'])) {
                    if (!empty($where['premises_type'])) {
                        $this->db->where('premises_types.premises_type ', $where['premises_type']);
                    }
                    unset($where['premises_type']);
                }

                if (isset($where['contract_id'])) {
                    if (!empty($where['contract_id'])) {
                        $this->db->where('premises_types.contract_id', $where['contract_id']);
                    }
                    unset($where['contract_id']);
                }

                if (isset($where['site_id'])) {
                    if (!empty($where['site_id'])) {
                        $this->db->where('premises_types.site_id', $where['site_id']);
                    }
                    unset($where['site_id']);
                }
            }

            if (!empty($order_by)) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('premises_type, premises_type_id DESC');
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->group_by('premises_types.premises_type_id')
                ->get('premises_types');

            if ($query->num_rows() > 0) {
                $result_data = $query->result();

                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $result->records 			= $result_data;
                $counters 					= $this->premises_types_totals($account_id, $search_term, $raw_where, $limit);
                $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
                $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
                $result->counters->limit  	= (!empty($limit > 0)) ? $limit : $result->counters->total;
                $result->counters->offset 	= $offset;

                $this->session->set_flashdata('message', 'Premises Types data found');
            } else {
                $this->session->set_flashdata('message', 'There\'s currently no Premises types data matching your criteria');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }

    /** Get Premises Types lookup counts **/
    public function premises_types_totals($account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('premises_types.premises_type_id', false)
                ->where('premises_types.is_active', 1)
                ->where('premises_types.account_id', $account_id)
                ->group_by('premises_types.premises_type_id', $account_id);

            $where = convert_to_array($where);

            if (!empty($search_term)) {
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->premises_type_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->premises_type_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                if (isset($where['premises_type'])) {
                    if (!empty($where['premises_type'])) {
                        $this->db->where('premises_types.premises_type', $where['premises_type']);
                    }
                    unset($where['premises_type']);
                }

                if (isset($where['contract_id'])) {
                    if (!empty($where['contract_id'])) {
                        $this->db->where('premises_types.contract_id', $where['contract_id']);
                    }
                    unset($where['contract_id']);
                }

                if (isset($where['site_id'])) {
                    if (!empty($where['site_id'])) {
                        $this->db->where('premises_types.site_id', $where['site_id']);
                    }
                    unset($where['site_id']);
                }
            }

            $query 			  = $this->db->from('premises_types')->count_all_results();
            $results['total'] = !empty($query) ? $query : 0;
            $limit 			  = ($limit > 0) ? $limit : $results['total'];
            $results['pages'] = !empty($query) ? ceil($query / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }

    /*
    * Search through premises
    */
    public function premises_lookup($account_id = false, $search_term = false, $premises_types = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            $where = $raw_where = convert_to_array($where);

            $this->db->select('premises.*', false)
                ->select('ata.attribute_name `primary_attribute`, atr.attribute_value, ata.is_mobile_visible', false)
                ->select('site.site_name, site.site_postcodes, site.site_address_id `address_id`, site_zones.zone_name, site_locations.location_name, concat(modifier.first_name," ",modifier.last_name) `last_modified_by`, premises_types.premises_type', false)
                ->join('premises_types', 'premises_types.premises_type_id = premises.premises_type_id', 'left')
                ->join('premises_type_attributes ata', 'ata.attribute_id = premises_types.primary_attribute_id', 'left')
                ->join('premises_attributes atr', 'atr.attribute_id = ata.attribute_id', 'left')
                ->join('user modifier', 'modifier.id = premises.last_modified_by', 'left')
                ->join('site', 'site.site_id = premises.site_id', 'left')
                ->join('site_locations', 'site_locations.location_id = premises.location_id', 'left')
                ->join('site_zones', 'site_zones.zone_id = premises.zone_id', 'left')
                ->where('premises.account_id', $account_id)
                ->where('premises.archived !=', 1)
                ->group_by('premises.premises_id');

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

                        if (!empty($search_where['premises.premises_type_id'])) {
                            $search_where['premises_types.premises_type'] =  trim($term);
                            unset($search_where['premises.premises_type_id']);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->searchable_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    if (!empty($search_where['premises.premises_type_id'])) {
                        $search_where['premises_types.premises_type'] =  $search_term;
                        unset($search_where['premises.premises_type_id']);
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($premises_types)) {
                $premises_types = convert_to_array($premises_types);
                $this->db->where_in('premises.premises_type_id', $premises_types);
            }

            if ($where) {
                $where = convert_to_array($where);

                if (isset($where['premises_type_id'])) {
                    if (!empty($where['premises_type_id'])) {
                        $this->db->where('premises.premises_type_id', $where['premises_type_id']);
                    }
                    unset($where['premises_type_id']);
                }

                if (isset($where['site_id'])) {
                    if (!empty($where['site_id'])) {
                        $this->db->where('premises.site_id', $where['site_id']);
                    }
                    unset($where['site_id']);
                }
            }

            if ($order_by) {
                $order = $this->ssid_common->_clean_order_by($order_by, $this->primary_tbl);
                if (!empty($order)) {
                    $this->db->order_by($order);
                }
            } else {
                $this->db->order_by('premises.premises_id desc');
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->get('premises');

            if ($query->num_rows() > 0) {
                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $result->records 			= $query->result();
                $counters 					= $this->get_total_premises($account_id, $search_term, $premises_types, $raw_where, $limit);
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
    * Get total premises count for the search
    */
    public function get_total_premises($account_id = false, $search_term = false, $premises_types = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;
        if (!empty($account_id)) {
            $where = $raw_where = convert_to_array($where);

            $this->db->select('premises.premises_id', false)
                ->join('premises_types', 'premises_types.premises_type_id = premises.premises_type_id', 'left')
                ->where('premises.account_id', $account_id)
                ->where('premises.archived !=', 1)
                ->group_by('premises.premises_id');

            if (!empty($search_term)) {
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->searchable_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        if (!empty($search_where['premises.premises_type_id'])) {
                            $search_where['premises_types.premises_type'] =  trim($term);
                            unset($search_where['premises.premises_type_id']);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->searchable_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    if (!empty($search_where['premises.premises_type_id'])) {
                        $search_where['premises_types.premises_type'] =  $search_term;
                        unset($search_where['premises.premises_type_id']);
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($premises_types)) {
                $premises_types = convert_to_array($premises_types);
                $this->db->where_in('premises.premises_type_id', $premises_types);
            }

            if ($where) {
                $where = convert_to_array($where);

                if (isset($where['premises_type_id'])) {
                    if (!empty($where['premises_type_id'])) {
                        $this->db->where('premises.premises_type_id', $where['premises_type_id']);
                    }
                    unset($where['premises_type_id']);
                }

                if (isset($where['site_id'])) {
                    if (!empty($where['site_id'])) {
                        $this->db->where('premises.site_id', $where['site_id']);
                    }
                    unset($where['site_id']);
                }
            }

            $query 			  = $this->db->from('premises')->count_all_results();
            $results['total'] = !empty($query) ? $query : 0;
            $limit 			  = (!empty($limit > 0)) ? $limit : $results['total'];
            $results['pages'] = !empty($query) ? ceil($query / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }


    /** Create A New Premises Type **/
    public function add_premises_type($account_id = false, $postdata = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($postdata)) {
            $data = [];
            foreach ($postdata as $col => $value) {
                if ($col == 'premises_type') {
                    $data['premises_type_ref'] = $this->generate_premises_type_ref($account_id, $postdata);
                }
                $data[$col] = (is_string($value)) ? trim($value) : $value;
            }

            if (!empty($data['site_id'])) {
                $this->db->where('premises_types.account_id', $data['site_id']);
            }

            $data['premises_type_desc'] = !empty($data['premises_type_desc']) ? $data['premises_type_desc'] : $data['premises_type'];

            $check_where  = '( premises_types.premises_type_ref = "'.$data['premises_type_ref'].'" OR premises_types.premises_type = "'.$data['premises_type'].'" )';
            $check_exists = $this->db->where($check_where)
                ->where('premises_types.account_id', $account_id)
                ->where('premises_types.is_active', 1)
                ->limit(1)
                ->get('premises_types')->row();

            if (!empty($check_exists)) {
                $this->session->set_flashdata('message', 'This Premises Type already exists, request aborted');
                $result = false;
            } else {
                $data					= $this->ssid_common->_filter_data('premises_types', $data);
                $data['created_by'] 	= $this->ion_auth->_current_user->id;
                $this->db->insert('premises_types', $data);
                $data['premises_type_id'] 	= $this->db->insert_id();
                $result 			 		= $this->db->select('premises_types.*, account_discipline.account_discipline_name `discipline_name`', false)
                    ->join('account_discipline', 'account_discipline.discipline_id = premises_types.discipline_id', 'left')
                    ->get_where('premises_types', [ 'premises_types.account_id' => $account_id, 'premises_types.premises_type_id' => $data['premises_type_id'] ])
                    ->row();
                $this->session->set_flashdata('message', 'New Premises Type added successfully.');
            }
        }

        return $result;
    }

    /** Edit / Update Premises Type **/
    public function update_premises_type($account_id = false, $postdata = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($postdata['premises_type_id'])) {
            $data = [];
            foreach ($postdata as $col => $value) {
                if ($col == 'premises_type') {
                    $data['premises_type_ref'] = $this->generate_premises_type_ref($account_id, $postdata);
                }
                $data[$col] = (is_string($value)) ? trim($value) : $value;
            }

            $check_exists = $this->db->where('premises_type_id', $data['premises_type_id'])
                ->where('premises_types.account_id', $account_id)
                ->where('premises_types.is_active', 1)
                ->limit(1)
                ->get('premises_types')->row();

            if (!empty($check_exists)) {
                $data					  = $this->ssid_common->_filter_data('premises_types', $data);
                $data['last_modified_by'] = $this->ion_auth->_current_user->id;

                $this->db->where('premises_type_id', $check_exists->premises_type_id)
                    ->update('premises_types', $data);

                $result = $this->get_premises_types($account_id, $check_exists->premises_type_id);
                $this->session->set_flashdata('message', 'Premises Type updated successfully.');
            } else {
                $this->session->set_flashdata('message', 'This Premises Type does not exist or does not belong to you.');
                $result = false;
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }

        return $result;
    }


    /** Get Premises Response Types **/
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
                            $this->session->set_flashdata('message', 'Premises Attribute Response types data found');
                            return $result;
                        } else {
                            $this->session->set_flashdata('message', 'Premises Attribute Response types data not found');
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
                $this->session->set_flashdata('message', 'Premises Response types data found.');
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
                    $this->session->set_flashdata('message', 'Premises Response types data found.');
                } else {
                    $this->session->set_flashdata('message', 'Premises Response types data not found.');
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
                $this->session->set_flashdata('message', 'Premises Response types data found.');
            } else {
                $this->session->set_flashdata('message', 'Premises Response types data not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Response type ID is a mandatory field.');
        }

        return $result;
    }


    /** Create new Premises Type Attribute **/
    public function add_premises_type_attribute($account_id = false, $premises_type_attribute_data = false)
    {
        $result = null;

        if (!empty($account_id) && !empty($premises_type_attribute_data)) {
            foreach ($premises_type_attribute_data as $col => $value) {
                if ($col == 'attribute_name') {
                    $data['attribute_ref'] = strtolower(strip_all_whitespace($value));
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
                    ->get('premises_type_attributes')->row();
            } else {
                unset($data['attribute_id']);
                $check_exists = $this->db->where('account_id', $account_id)
                    ->where('premises_type_attributes.premises_type_id', $data['premises_type_id'])
                    ->where('premises_type_attributes.attribute_name', $data['attribute_name'])
                    ->limit(1)
                    ->get('premises_type_attributes')
                    ->row();
            }

            $data = $this->ssid_common->_filter_data('premises_type_attributes', $data);

            if (!empty($check_exists)) {
                $this->session->set_flashdata('message', 'This Premises Type attribute already exists, record has been updated successfully.');
                $result = false;
            } else {
                $data['created_by'] 	= $this->ion_auth->_current_user->id;
                $this->db->insert('premises_type_attributes', $data);
                $this->session->set_flashdata('message', 'New Premises Type attribute added successfully.');
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

    /** Update an existing Premises Type attribute **/
    public function update_premises_type_attribute($account_id = false, $attribute_id = false, $postdata = false)
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
            $update_data   = $this->ssid_common->_filter_data('premises_type_attributes', $data);
            $record_pre_update = $this->db->get_where('premises_type_attributes', [ 'account_id'=>$account_id, 'attribute_id'=>$attribute_id ])->row();

            if (!empty($record_pre_update)) {
                $premises_type_id  = (!empty($data['premises_type_id'])) ? $data['premises_type_id'] : 0;

                $check_conflict = $this->db->select('attribute_id', false)
                    ->where('account_id', $account_id)
                    ->where('attribute_id !=', $attribute_id)
                    ->where('premises_type_id', $premises_type_id)
                    ->where('attribute_name', $update_data['attribute_name'])
                    ->limit(1)
                    ->get('premises_type_attributes')
                    ->row();

                if (!$check_conflict) {
                    $update_data['last_modified_by'] = $this->ion_auth->_current_user->id;
                    $this->db->where($ref_condition)
                        ->update('premises_type_attributes', $update_data);

                    $updated_record = $this->get_premises_type_attributes($account_id, false, $attribute_id);
                    $result = (!empty($updated_record)) ? $updated_record : false;
                    $this->session->set_flashdata('message', 'Premises Type Attribute updated successfully');
                    return $result;
                } else {
                    $this->session->set_flashdata('message', 'This Premises Type Attribute already exists under the specified section. Update request aborted');
                    return false;
                }
            } else {
                $this->session->set_flashdata('message', 'This Premises Type Attribute record does not exist or does not belong to you.');
                return false;
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }
        return $result;
    }


    /*
    *	Get list of Premises Types attributes for a specific Premises Type
    */
    public function get_premises_type_attributes($account_id = false, $premises_type_id = false, $attribute_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET, $order_by = false)
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
                    ->get('premises_type_attributes ata');

                if ($query->num_rows() > 0) {
                    $result = $query->result();
                    $this->session->set_flashdata('message', 'Premises Type attributes found');
                } else {
                    $this->session->set_flashdata('message', 'No data found');
                }
            } else {
                $this->db->select('ata.*, premises_types.premises_type', false)
                    ->where('ata.account_id', $account_id)
                    ->join('premises_types', 'premises_types.premises_type_id = ata.premises_type_id', 'left');

                $premises_type_id = !empty($premises_type_id) ? $premises_type_id : (!empty($where['premises_type_id']) ? $where['premises_type_id'] : false);
                $attribute_id  = !empty($attribute_id) ? $attribute_id : (!empty($where['attribute_id']) ? $where['attribute_id'] : false);

                if (!empty($premises_type_id)) {
                    $this->db->where('ata.account_id', $account_id)
                        ->where('ata.premises_type_id', $premises_type_id);
                } else {
                    if (!$attribute_id) {
                        $this->db->where('( ata.premises_type_id IS NULL OR ata.premises_type_id = 0 )');
                    }
                }

                if (!empty($search_term)) {
                    //Check for spaces in the search term
                    $search_term  = trim(urldecode($search_term));
                    $search_where = [];
                    if (strpos($search_term, ' ') !== false) {
                        $multiple_terms = explode(' ', $search_term);
                        foreach ($multiple_terms as $term) {
                            foreach ($this->premises_type_attribs_search as $k=>$field) {
                                $search_where[$field] = trim($term);
                            }

                            $where_combo = format_like_to_where($search_where);
                            $this->db->where($where_combo);
                        }
                    } else {
                        foreach ($this->premises_type_attribs_search as $k=>$field) {
                            $search_where[$field] = $search_term;
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                }

                if (!empty($attribute_id)) {
                    $row = $this->db->where('ata.attribute_id', $attribute_id)
                        ->get('premises_type_attributes ata')
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

                    if (isset($order_by) && !empty($order_by)) {
                        $this->db->order_by($order_by);
                    } else {
                        $this->db->order_by('LENGTH( ata.ordering ) asc, ata.ordering asc');
                    }

                    $query = $this->db->where('ata.is_active', 1)
                        ->get('premises_type_attributes ata');

                    if ($query->num_rows() > 0) {
                        foreach ($query->result() as $k => $row) {
                            $row->response_options = (!empty($row->response_options)) ? json_decode($row->response_options) : null;
                            $data[$k] = $row;
                        }
                        $this->session->set_flashdata('message', 'Premises Type attributes found');
                    } else {
                        // $data = [];
                        // $default = $this->db->where( '( ata.account_id IS NULL OR ata.account_id = 0 )' )
                        // ->get( 'premises_type_attributes `ata`' );
                        // foreach( $default->result() as $k => $row ){
                        // $row->response_options = ( !empty( $row->response_options ) ) ? json_decode( $row->response_options ) : null;
                        // $data[$k] = $row;
                        // }
                        // $this->session->set_flashdata('message','Premises Type attributes found (default list)');

                        $this->session->set_flashdata('message', 'No data found');
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
    * Get Premises single records or multiple records - Version 2.0
    */
    public function get_premises($account_id = false, $premises_id = false, $premises_ref = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            if (!empty($where)) {
                $where = convert_to_array($where);
            }

            if (isset($where['premises_id'])) {
                $site_id = !empty($where['premises_id']) ? $where['premises_id'] : false;
                $this->db->where('premises.premises_id', $site_id);
                unset($where['premises_id']);
            }

            if (isset($where['site_id'])) {
                $site_id = !empty($where['site_id']) ? $where['site_id'] : false;

                if (is_array($site_id)) {
                    $this->db->where_in('premises.site_id', $site_id);
                } else {
                    $this->db->where('premises.site_id', $site_id);
                }
                unset($where['site_id']);
            }

            $this->db->select('premises.*', false)
                ->select('ata.attribute_name `primary_attribute`, atr.attribute_value, ata.is_mobile_visible', false)
                ->select('site.site_name, site.site_postcodes, site.site_address_id `address_id`, site_zones.zone_name, site_locations.location_name, concat(modifier.first_name," ",modifier.last_name) `last_modified_by`, premises_types.premises_type', false)
                ->join('premises_types', 'premises_types.premises_type_id = premises.premises_type_id', 'left')
                ->join('premises_type_attributes ata', 'ata.attribute_id = premises_types.primary_attribute_id', 'left')
                ->join('premises_attributes atr', 'atr.attribute_id = ata.attribute_id', 'left')
                ->join('user modifier', 'modifier.id = premises.last_modified_by', 'left')
                ->join('site', 'site.site_id = premises.site_id', 'left')
                ->join('site_locations', 'site_locations.location_id = premises.location_id', 'left')
                ->join('site_zones', 'site_zones.zone_id = premises.zone_id', 'left')
                ->where('premises.account_id', $account_id)
                ->where('premises.archived !=', 1)
                ->group_by('premises.premises_id');

            if (isset($where['premises_type_id'])) {
                if (!empty($where['premises_type_id'])) {
                    $this->db->where('premises.premises_type_id', $where['premises_type_id']);
                }
                unset($where['premises_type_id']);
            }

            if (!empty($where)) {
                $this->db->where($where);
            }

            if ($premises_id || $premises_ref) {
                $uniq_where = (!empty($premises_id)) ? ['premises.premises_id'=>$premises_id] : ((!empty($premises_ref)) ? ['premises.premises_ref'=>$premises_ref] : false);

                $row 		= $this->db->get_where('premises', $uniq_where)->row();

                if (!empty($row)) {
                    $profile_images 			= $this->document_service->get_document_list($account_id, $document_group = 'premises', ['premises_id'=>$premises_id], ['doc_type'=>'Profile Images']);
                    $row->profile_images 		= (!empty($profile_images[$account_id]['Profile Images'])) ? $profile_images[$account_id]['Profile Images'] : null;
                    $row->attribute_value 		= (is_json($row->attribute_value)) ? json_decode($row->attribute_value) : $row->attribute_value;
                    $row->attribute_value 		= (is_array($row->attribute_value) ? ( string ) implode(" | ", $row->attribute_value) : (string)$row->attribute_value);
                    $row->premises_attributes 		= $this->get_premises_attribute_values($account_id, $row->premises_type_id, $premises_id);
                    $this->session->set_flashdata('message', 'Premises found');
                    $result 					= $row;
                } else {
                    $this->session->set_flashdata('message', 'Premises not found');
                }
                return $result;
            }


            if ($order_by) {
                $order = $this->ssid_common->_clean_order_by($order_by, $this->primary_tbl);
                if (!empty($order)) {
                    $this->db->order_by($order);
                }
            } else {
                $this->db->order_by('premises_types.premises_type, premises.premises_id');
            }

            if ($limit > 0) {
                $premises = $this->db->limit($limit, $offset);
            }

            $premises = $this->db->get('premises');

            if ($premises->num_rows() > 0) {
                $this->session->set_flashdata('message', 'Premises records found');
                $result = $premises->result();
            } else {
                $this->session->set_flashdata('message', 'Premises record(s) not found');
            }
        }

        return $result;
    }

    /* Process Premises Attributes */
    private function _save_premises_attributes($account_id = false, $premises_id = false, $responses = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($premises_id) && !empty($responses)) {
            $responses		= convert_to_array($responses);
            $responses		= (is_json($responses)) ? json_decode($responses) : $responses;
            $target_table 	= 'premises_attributes';
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
                            $postdata['premises_id']  		= (string) $premises_id;
                            $postdata['account_id']  	= $account_id;
                            $postdata['doc_type']  		= 'Profile Images';
                            $postdata['document_name']  = $row['attribute_name'];
                            #$uploaded_docs 	= $this->document_service->upload_files( $account_id, $postdata, $doc_group = 'premises' );
                            $uploaded_docs 	= $this->document_service->upload_profile_documents($account_id, $postdata, $doc_group = 'premises');
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
                $new_row['premises_id']	 = $premises_id;
                $resp_data[$k] 		 	 = $new_row;
                //}
            }

            ## Insert responses
            if (!empty($resp_data)) {
                $conditions = ['premises_id'=>$premises_id];
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
    * Get Premises attribute attribute_values
    */
    private function get_premises_attribute_values($account_id = false, $premises_type_id = false, $premises_id = false)
    {
        $result = null;
        if (!empty($account_id) && !empty($premises_id) && !empty($premises_type_id)) {
            $premises_type_attributes = $this->get_premises_type_attributes($account_id, $premises_type_id, false, false, false, $limit = -1);

            if (!empty($premises_type_attributes)) {
                foreach ($premises_type_attributes as $k => $value) {
                    $row = $this->db->select('premises_attributes.*, ata.attribute_id , ata.attribute_name, ata.response_type, ata.response_options, ata.accepted_file_types, ata.is_mandatory, ata.is_mobile_visible, ata.photo_required, ata.ordering', false)
                        ->join('premises_attributes', 'ata.attribute_id = premises_attributes.attribute_id', 'left')
                        ->where('ata.is_active', 1)
                        ->where('premises_attributes.premises_id', $premises_id)
                        ->where('premises_attributes.attribute_id', $value->attribute_id)
                        ->order_by('LENGTH( premises_attributes.ordering ) asc, premises_attributes.ordering asc')
                        ->order_by('premises_attributes.attribute_id')
                        ->get('premises_type_attributes `ata`')
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
                            'premises_id' 			=> ( string ) $premises_id,
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
                $this->session->set_flashdata('message', 'Premises attribute values found');
            } else {
                $this->session->set_flashdata('message', 'No records found');
            }
        }
        return $result;
    }


    /*
    * 	Delete Premises Type
    */
    public function delete_premises_type($account_id = false, $premises_type_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($premises_type_id)) {
            $check_exists = $this->db->select('premises_type_id, premises_type, premises_type_ref', false)
                ->get('premises_types', [ 'account_id'	=> $account_id,	'premises_type_id' => $premises_type_id ])->row();

            if ($check_exists) {
                $data = [
                    'is_active' 		=> 0,
                    'premises_type_ref' => 'ARCH_'.$check_exists->premises_type_ref,
                    'last_modified_by' 	=> $this->ion_auth->_current_user->id
                ];

                $this->db->where([ 'account_id' => $account_id, 'premises_type_id' => $premises_type_id ])
                    ->update('premises_types', $data);

                if ($this->db->affected_rows() > 0) {
                    $this->session->set_flashdata('message', 'Record deleted successfully');
                    $result = true;

                    $attributes_exists = $this->db->get_where('premises_type_attributes', ['account_id' => $account_id, 'premises_type_id' => $premises_type_id ])->result();

                    if (!empty($attributes_exists)) {
                        foreach ($attributes_exists as $attr) {
                            $this->db->delete('premises_type_attributes', ['account_id' => $account_id, 'attribute_id' => $attr->attribute_id]);
                        }
                    }
                }
            } else {
                $this->session->set_flashdata('message', 'This record does not exist or does not belong to you.');
            }
        } else {
            $this->session->set_flashdata('message', 'Invalid Premises Type ID or Account ID');
        }

        return $result;
    }

    /**
    /* 	Delete an Premises Type Attribute resource
    */
    public function delete_premises_type_attribute($account_id = false, $premises_type_id = false, $attribute_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($attribute_id)) {
            if (!empty($premises_type_id)) {
                $this->db->where('premises_type_id', $premises_type_id); #?? not sure why I need this, but leave it here for now!
            }

            $conditions 	= [ 'account_id'=>$account_id,'attribute_id'=>$attribute_id ];
            $record_exists 	= $this->db->get_where('premises_type_attributes', $conditions)->row();

            if (!empty($record_exists)) {
                ## Drop preexisting attributes, children!
                $this->db->where('attribute_id', $attribute_id)
                    ->delete('premises_attributes');

                if ($this->db->trans_status() !== false) {
                    $this->ssid_common->_reset_auto_increment('premises_attributes', 'attribute_id');
                }

                ## Then the parent
                $this->db->where('attribute_id', $attribute_id)
                    ->delete('premises_type_attributes');

                if ($this->db->trans_status() !== false) {
                    $this->ssid_common->_reset_auto_increment('premises_type_attributes', 'attribute_id');
                    $this->session->set_flashdata('message', 'Premises Type Attribute removed successfully.');
                    $result = true;
                }
            } else {
                $this->session->set_flashdata('message', 'Invalid Premises Type Attribute ID.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Premises Type Attribute record found.');
        }
        return $result;
    }


    /*
    * 	Get Premises attribute attribute_values - newest approach, left the old one for the legacy reason
    */
    public function get_premises_attribute_values2($account_id = false, $premises_type_id = false, $premises_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = null;
        if (!empty($account_id)) {
            if (!empty($premises_id) && !empty($premises_type_id)) {
                $premises_type_attributes = $this->get_premises_type_attributes($account_id, $premises_type_id);

                if (!empty($premises_type_attributes)) {
                    foreach ($premises_type_attributes as $k => $value) {
                        $row = $this->db->select('premises_attributes.*, ata.attribute_id , ata.attribute_name, ata.response_type, ata.response_options, ata.accepted_file_types, ata.is_mandatory, ata.is_mobile_visible, ata.photo_required, ata.ordering', false)
                            ->join('premises_attributes', 'ata.attribute_id = premises_attributes.attribute_id', 'left')
                            ->where('ata.is_active', 1)
                            ->where('premises_attributes.premises_id', $premises_id)
                            ->where('premises_attributes.attribute_id', $value->attribute_id)
                            ->order_by('LENGTH( premises_attributes.ordering ) asc, premises_attributes.ordering asc')
                            ->order_by('premises_attributes.attribute_id')
                            ->get('premises_type_attributes `ata`')
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
                                'premises_id' 			=> ( string ) $premises_id,
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
                $premises_sql = 'SELECT premises_id FROM premises WHERE premises.`account_id` = '.$account_id.' AND archived != 1 ';

                $main_sql  = 'SELECT * FROM `premises_attributes` WHERE premises_id IN ( '.$premises_sql.' )';

                if ($limit > 0) {
                    $main_sql	.= ' LIMIT '.$limit;
                }

                $query = $this->db->query($main_sql);
                if ($query->num_rows() > 0) {
                    $result = $query->result();
                    $this->session->set_flashdata('message', 'Premises attribute values found');
                } else {
                    $this->session->set_flashdata('message', 'No records found');
                }
            }

            if (!empty($result)) {
                $this->session->set_flashdata('message', 'Premises attribute values found');
            } else {
                $this->session->set_flashdata('message', 'No records found');
            }
        }
        return $result;
    }


    /** Verify Premises Type **/
    public function _verify_premises_type($account_id = false, $premises_type_id = false, $premises_type = false)
    {
        $result = false;
        if (!empty($account_id) && (!empty($premises_type_id) || !empty($premises_type))) {
            $this->db->select('account_id, premises_type_id, premises_type', false);
            if (!empty($premises_type_id)) {
                $verify_premises_type = $this->db->get_where('premises_types', [ 'account_id'=>$account_id, 'premises_type_id'=>$premises_type_id ])->row();
            } elseif (!empty($premises_type)) {
                $verify_premises_type = $this->db->where('account_id', $account_id)
                    ->where('premises_type', $premises_type)
                    ->or_where('premises_type_ref', strip_all_whitespace($premises_type))
                    ->limit(1)
                    ->get('premises_types')->row();
            }

            if (!empty($verify_premises_type)) {
                $this->session->set_flashdata('message', 'Premises Type verified.');
                $result = $verify_premises_type;
            } else {
                $this->session->set_flashdata('message', 'Invalid Premises Type.');
            }
        }
        return $result;
    }


    /** Generate Premises Type Ref **/
    private function generate_premises_type_ref($account_id = false, $data = false)
    {
        if (!empty($account_id) && !empty($data)) {
            $premises_type_ref = $account_id;
            $premises_type_ref .= (!empty($data['premises_type'])) ? lean_string($data['premises_type']) : '';
            $premises_type_ref .= (!empty($data['site_id'])) ? $data['site_id'] : '';
            $premises_type_ref .= (!empty($data['contract_id'])) ? $data['contract_id'] : '';
            $premises_type_ref .= (!empty($data['premises_type_id'])) ? $data['premises_type_id'] : '';
            $premises_type_ref .= (!empty($data['discipline_id'])) ? $data['discipline_id'] : '';
        } else {
            $premises_type_ref = $account_id.$this->ssid_common->generate_random_password();
        }
        return strtoupper($premises_type_ref);
    }

    /** Auto Generate the Premises Reference**/
    private function _auto_genearte_premises_ref($account_id = false, $data = false)
    {
        if (!empty($account_id) && !empty($data)) {
            $premises_ref 			= '';
            if (!empty($data['site_id'])) {
                $site_details = $this->db->select('site_id, site_name, site_reference', false)->get_where('site', [ 'account_id'=> $account_id, 'site_id'=> $data['site_id']  ])->row();
                if (!empty($site_details->site_name)) {
                    $site_snips = preg_split('/\s+/', $site_details->site_name);
                    $site_abbrev = '';
                    foreach ($site_snips as $w) {
                        $site_abbrev .= trim($w[0]);
                    }
                    $premises_ref .= trim($site_abbrev).'-'.trim($data['site_id']).'-';
                } else {
                    $premises_ref = $account_id;
                    $premises_ref = strtoupper(trim($premises_ref));
                }
            } else {
                $premises_ref = $account_id;
                $premises_ref = strtoupper(trim($premises_ref));
            }

            $premises_ref .= (!empty($data['premises_desc'])) ? lean_string($data['premises_desc']) : '';
            $premises_ref .= (!empty($data['site_id'])) ? $data['site_id'] : '';
            $premises_ref .= (!empty($data['contract_id'])) ? $data['contract_id'] : '';
            $premises_ref .= (!empty($data['premises_type_id'])) ? $data['premises_type_id'] : '';
            $premises_ref .= (!empty($data['discipline_id'])) ? $data['discipline_id'] : '';
        } else {
            $premises_ref = json_encode($data);
        }
        return strtoupper($premises_ref);
    }
}
