<?php

namespace App\Models\Service;

use App\Adapter\Model;

class SiteModel extends Model
{
	/**
	 * @var \Application\Modules\Service\Models\AssetModel $asset_service
	 */
	private $asset_service;

	/**
	 * @var \Application\Modules\Service\Models\AuditModel $audit_service
	 */
	private AuditModel $audit_service;

	public function __construct()
    {
        parent::__construct();

        $this->asset_service = new AssetModel();
        $this->audit_service = new AuditModel();
    }

    public $okay_statuses = ['OK','No Fault'];

    /** Searchable fields **/
    private $searchable_fields  	= ['site.site_id', 'site_name', 'site.status_id', 'site_reference', 'site_address_id', 'summaryline', 'site_postcodes', 'estate_name'];
    private $location_search_fields = ['site_locations.site_id', 'site_zones.zone_name', 'location_name', 'resident_first_name', 'resident_last_name', 'resident_email_address', 'location_type' /*,'address_line1', 'address_line2', 'address_postcode'*/ ];

    /** Primary table name **/
    private $primary_tbl = 'site';

    /*
    * Get Sites single records or multiple records
    */
    public function get_sites($account_id = false, $site_id = false, $site_unique_id = false, $where = false, $order_by = false, $limit=DEFAULT_LIMIT, $offset=DEFAULT_OFFSET)
    {
        $result = $organized = false;

        if (!empty($account_id) && $this->account_service->check_account_status($account_id)) {
            #Limit access by Associated Buildings
            if ((!$this->ion_auth->_current_user()->is_admin) && !empty($this->ion_auth->_current_user()->buildings_visibility) && (strtolower($this->ion_auth->_current_user()->buildings_visibility) == 'limited')) {
                $buildings_access 	= $this->get_user_associated_buildings($account_id, $this->ion_auth->_current_user->id);
                $allowed_buildings  = !empty($buildings_access) ? array_column($buildings_access, 'site_id') : [];
                if (!empty($allowed_buildings)) {
                    $this->db->where_in('site.site_id', $allowed_buildings);
                } else {
                    $this->session->set_flashdata('message', 'No Buildings data found matching your criteria.');
                    return false;
                }
            }

            #Limit access by contract to External User Types
            if (in_array($this->ion_auth->_current_user()->user_type_id, EXTERNAL_USER_TYPES)) {
                $contract_access 	= $this->contract_service->get_linked_people($account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1]);
                $allowed_contracts  = !empty($contract_access) ? array_column($contract_access, 'contract_id') : [];
                if (!empty($allowed_contracts)) {
                    $contract_access 	= $this->contract_service->get_linked_sites($account_id, $allowed_contracts, [ 'ids_only'=>1 ]);
                    $allowed_sites  = !empty($contract_access) ? $contract_access : [];
                    $this->db->where_in('site.site_id', $allowed_sites);
                } else {
                    $this->session->set_flashdata('message', 'No data found matching your criteria');
                    return false;
                }
            }

            #Limit access by Assigned Reqion
            if (!$this->ion_auth->_current_user()->is_admin) {
                if (!empty($this->ion_auth->_current_user()->region_id)) {
                    $this->db->where('site.region_id', $this->ion_auth->_current_user()->region_id);
                }
            }

            $this->db->select('site.*, diary_regions.region_name, site_event_statuses.event_tracking_status_id, site_statuses.status_name, site_event_statuses.event_tracking_status, site_event_statuses.status_group, site_event_statuses.hex_color, site_event_statuses.icon_class, ars.result_status `audit_result_status`, ars.result_status_alt, ars.result_status_group `audit_result_status_group`, addrs.main_address_id,addrs.addressline1 `address_line_1`,addrs.addressline2 `address_line_2`,addrs.addressline3 `address_line_3`,addrs.posttown `address_city`,addrs.county `address_county`,addrs.postcode `address_postcode`,addrs.summaryline, CONCAT( addrs.addressline1,", ",addrs.addressline2,", ",addrs.posttown, ", ",addrs.posttown,", ",addrs.postcode ) `short_address`, addrs.organisation `address_business_name`,addrs.xcoords `gps_latitude`,addrs.ycoords `gps_longitude`, concat(user.first_name," ",user.last_name) `created_by`,concat(modifier.first_name," ",modifier.last_name) `last_modified_by`', false)
                ->join('site_statuses', 'site_statuses.status_id = site.status_id', 'left')
                ->join('site_event_statuses', 'site_event_statuses.event_tracking_status_id = site.event_tracking_status_id', 'left')
                ->join('addresses addrs', 'addrs.main_address_id = site.site_address_id', 'left')
                ->join('user', 'user.id = site.created_by', 'left')
                ->join('user modifier', 'modifier.id = site.last_modified_by', 'left')
                ->join('audit_result_statuses ars', 'ars.audit_result_status_id = site.audit_result_status_id', 'left')
                ->join('diary_regions', 'diary_regions.region_id = site.region_id', 'left')
                ->where('site.account_id', $account_id)
                ->where('site.archived !=', 1);

            if ($site_id || $site_unique_id) {
                $where = !empty($site_id) ? ['site_id'=>$site_id] : (!empty($site_unique_id) ? ['site_unique_id'=>$site_unique_id] : []);

                $row = $this->db->get_where('site', $where)->row();

                if (!empty($row)) {
                    $this->session->set_flashdata('message', 'Building record found');
                    $row->total_zones 		= "2";
                    $row->total_locations 	= "10";
                    $result = $row;
                } else {
                    $this->session->set_flashdata('message', 'Building not found');
                }
                return $result;
            }

            //Check for a setting that specifies whether or not to only get monitored sites
            if ($where) {
                $where = (!is_array($where)) ? json_decode($where) : $where;
                $where = (is_object($where)) ? object_to_array($where) : $where;

                if (!empty($where['monitored']) && ($where['monitored'] == 1)) {
                    $this->db->where('( site.event_tracking_status_id > 0 )');
                    unset($where['monitored']);
                }

                if (!empty($where['organized'])) {
                    $organized = true;
                    unset($where['organized']);
                }

                if (isset($where['region_id'])) {
                    if (!empty($where['region_id'])) {
                        $this->db->where('site.region_id', $where['region_id']);
                        unset($where['region_id']);
                    }
                }
            }

            if ($order_by) {
                $order = $this->ssid_common->_clean_order_by($order_by, $this->primary_tbl);
                if (!empty($order)) {
                    $this->db->order_by($order);
                }
            } else {
                $this->db->order_by('site.site_name');
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $sites = $this->db->get('site');

            if ($sites->num_rows() > 0) {
                $this->session->set_flashdata('message', 'Building records found');

                if (!empty($organized)) {
                    foreach ($sites->result() as $row) {
                        $result[$row->site_id] = $row;
                    }
                } else {
                    $result = $sites->result();
                }
            } else {
                $this->session->set_flashdata('message', 'Building record(s) not found');
            }
        }
        return $result;
    }

    /*
    * Create new Site
    */
    public function create_site($site_data = false)
    {
        $result = false;
        if (!empty($site_data['account_id']) && !empty($site_data)) {
            $data = [];

            $site_locations = (!empty($site_data['site_locations'])) ? $site_data['site_locations'] : [];

            unset($site_data['site_locations']);

            foreach ($site_data as $key=>$value) {
                if (in_array($key, format_name_columns())) {
                    $value = format_name($value);
                } elseif (in_array($key, format_email_columns())) {
                    $value = format_email($value);
                } elseif (in_array($key, format_number_columns())) {
                    $value = format_number($value);
                } else {
                    $value = (is_object($value)) ? json_encode($value) : trim($value);
                }
                $data[$key] = $value;
            }

            if (!empty($data)) {
                $compliance_status = $this->db->select('audit_result_status_id')
                    ->get_where('audit_result_statuses', ['account_id'=>$site_data['account_id'], 'result_status_group'=>'not_set'])
                    ->row();


                if (empty($data['site_reference']) && !empty($data['external_site_ref'])) {
                    $data['site_reference'] = $data['external_site_ref'];
                }

                $data['status_id']  			= 1;
                $data['audit_result_status_id'] = !empty($compliance_status) ? $compliance_status->audit_result_status_id : null ;
                $data['created_by'] 			= $this->ion_auth->_current_user->id;
                $new_site_data 					= $this->ssid_common->_filter_data('site', $data);

                $this->db->insert('site', $new_site_data);
                if ($this->db->trans_status() !== false) {
                    $data['site_id'] = $this->db->insert_id();

                    ## Save the locations
                    if (!empty($site_locations)) {
                        #$locations = $this->create_site_locations( $data['account_id'], $data['site_id'], [ 'site_locations'=>$site_locations ] );
                    }

                    $result = $this->get_sites($site_data['account_id'], $data['site_id']);
                    $this->session->set_flashdata('message', 'Building record created successfully.');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Building data supplied.');
        }
        return $result;
    }

    /** Save site locations **/
    private function save_site_locations($account_id = false, $site_id = false, $locations_data = array())
    {
        $result = false;
        if (!empty($account_id) && !empty($site_id) && !empty($locations_data)) {
            $locations_data = (is_array($locations_data)) ? $locations_data : (is_object($locations_data) ? (array)$locations_data : json_decode($locations_data));

            $current_list   = $update_locations = $new_locations = [];

            $query = $this->db->select('address_id')
                ->where(['account_id'=>$account_id, 'site_id'=>$site_id ])
                ->get('site_locations');

            if ($query->num_rows() > 0) {
                $current_list 		= array_column($query->result_array(), 'address_id');
                $deleted_locations	= (!empty($current_list)) ? array_diff($current_list, $locations_data) : [];
            }

            ##Dropped locations
            if (!empty($deleted_locations)) {
                $this->db->where('account_id', $account_id)
                    ->where('site_id', $site_id)
                    ->where_in('address_id', $deleted_locations)
                    ->delete('site_locations');

                $this->ssid_common->_reset_auto_increment('site_locations', 'location_id');
            }

            foreach ($locations_data as $k=>$address_id) {
                $check_exists = $this->db->get_where('site_locations', ['account_id'=>$account_id, 'site_id'=>$site_id, 'address_id'=>$address_id ])->row();
                if (empty($check_exists)) {
                    $new_locations[] = [
                        'account_id'=>$account_id,
                        'site_id'=>$site_id,
                        'address_id'=>$address_id,
                        'created_by'=>$this->ion_auth->_current_user->id
                    ];
                } else {
                    //Already exists
                    $update_locations[] = [
                        'location_id'=>$check_exists->location_id,
                        'last_modified_by'=>$this->ion_auth->_current_user->id
                    ];
                }
                $result[$k] = $address_id;
            }

            ##Do bacth insert / update
            if (!empty($new_locations)) {
                $this->db->insert_batch('site_locations', $new_locations);
            }

            if (!empty($update_locations)) {
                $this->db->update_batch('site_locations', $update_locations, 'location_id');
            }
        }
        return $result;
    }

    /** Save site locations **/
    public function get_locations($account_id = false, $site_id = false, $where = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($site_id)) {
            $this->db->select('site_locations.*, addresses.*', false);
            $this->db->join('addresses', 'addresses.main_address_id = site_locations.address_id', 'left');

            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where['zone_id'])) {
                    $zone_id = $where['zone_id'];
                    $this->db->where("zone_id", $zone_id);
                    unset($where['zone_id']);
                }

                if (!empty($where['location_id'])) {
                    $location_id = $where['location_id'];
                    $this->db->where("location_id", $location_id);
                    unset($where['location_id']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $this->db->where('site_locations.account_id', $account_id);
            $this->db->where('site_locations.site_id', $site_id);
            $this->db->order_by('addresses.addressline1, addresses.main_address_id');

            $query = $this->db->get('site_locations');

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Building locations found');
            } else {
                $this->session->set_flashdata('message', 'No locations found for this Site');
            }
        }
        return $result;
    }

    /*
    * Update Site record
    */
    public function update_site($account_id=false, $site_id = false, $site_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($site_id) && !empty($site_data)) {
            $site_b4_update = $this->db->get_where('site', ['site_id'=>$site_id,'account_id'=>$account_id])->row();
            if (!empty($site_b4_update)) {
                $site_locations = (!empty($site_data['site_locations'])) ? $site_data['site_locations'] : [];
                unset($site_data['site_locations']);

                $data = [];
                foreach ($site_data as $key=>$value) {
                    if (in_array($key, format_name_columns())) {
                        $value = format_name($value);
                    } elseif (in_array($key, format_email_columns())) {
                        $value = format_email($value);
                    } elseif (in_array($key, format_number_columns())) {
                        $value = format_number($value);
                    } else {
                        $value = trim($value);
                    }
                    $data[$key] = $value;
                }

                if (!empty($data)) {
                    if (!empty($data['audit_result_status_id']) && ($site_b4_update->audit_result_status_id != $data['audit_result_status_id'])) {
                        $data['audit_result_timestamp'] 	= date('Y-m-d H:i:s');
                    }

                    $update_data = $this->ssid_common->_filter_data('site', $data);

                    $this->db->where('site_id', $site_id)
                        ->where('account_id', $account_id)
                        ->update('site', $update_data);

                    if ($this->db->trans_status() !== false) {
                        ## Save the locations
                        if (!empty($site_locations)) {
                            $this->save_site_locations($account_id, $site_id, $site_locations);
                        }

                        ## Prepare log data
                        $new_status		 = !empty($data['status_id']) ? $data['status_id'] : $site_b4_update->status_id;
                        $log_data   	 = array_merge(['previous_status_id'=>$site_b4_update->status_id,'updated_status_id'=>$new_status], $data);
                        $this->create_site_change_log($account_id, $site_id, $log_data);

                        $result = $result = $this->get_sites($account_id, $site_id);
                        $this->session->set_flashdata('message', 'Building record updated successfully.');
                    }
                }
            } else {
                $this->session->set_flashdata('message', 'Foreign Building record. Access denied.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Building data supplied.');
        }
        return $result;
    }

    /*
    * Delete Site record
    */
    public function delete_site($account_id = false, $site_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($site_id)) {
            $check_site = $this->db->get_where('site', ['account_id'=>$account_id, 'site_id'=>$site_id])->row();
            if (!empty($check_site)) {
                $this->db->where('account_id', $account_id)
                    ->where('site_id', $site_id)
                    ->update('site', [ 'archived'=> 1, 'last_modified_by' => $this->ion_auth->_current_user->id ]);

                if ($this->db->trans_status() !== false) {
                    //Delete associated addresses as well
                    /* $this->db->where('site_id',$site_id)->delete('site_addresses'); */
                    $result = true;
                    $this->session->set_flashdata('message', 'Building record deleted successfully.');
                }
            } else {
                $this->session->set_flashdata('message', 'Foreign Building record. Access denied.');
            }
        }
        return $result;
    }

    /*
    * Search through sites
    */
    public function site_lookup($account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            #Limit access by Associated Buildings
            if ((!$this->ion_auth->_current_user()->is_admin) && !empty($this->ion_auth->_current_user()->buildings_visibility) && (strtolower($this->ion_auth->_current_user()->buildings_visibility) == 'limited')) {
                $buildings_access 	= $this->get_user_associated_buildings($account_id, $this->ion_auth->_current_user->id);
                $allowed_buildings  = !empty($buildings_access) ? array_column($buildings_access, 'site_id') : [];
                if (!empty($allowed_buildings)) {
                    $this->db->where_in('site.site_id', $allowed_buildings);
                } else {
                    $this->session->set_flashdata('message', 'No Buildings data found matching your criteria.');
                    return false;
                }
            }

            #Limit access by contract to External User Types
            if (in_array($this->ion_auth->_current_user()->user_type_id, EXTERNAL_USER_TYPES)) {
                $contract_access 	= $this->contract_service->get_linked_people($account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1]);
                $allowed_contracts  = !empty($contract_access) ? array_column($contract_access, 'contract_id') : [];
                if (!empty($allowed_contracts)) {
                    $contract_access 	= $this->contract_service->get_linked_sites($account_id, $allowed_contracts, [ 'ids_only'=>1, 'ignore_schedule_check'=> 1 ], -1);
                    $allowed_sites  = !empty($contract_access) ? $contract_access : [];
                    $this->db->where_in('site.site_id', $allowed_sites);
                } else {
                    $this->session->set_flashdata('message', 'No data found matching your criteria');
                    return false;
                }
            }

            #Limit access by Assigned Reqion
            if (!$this->ion_auth->_current_user()->is_admin) {
                if (!empty($this->ion_auth->_current_user()->region_id)) {
                    $this->db->where('site.region_id', $this->ion_auth->_current_user()->region_id);
                }
            }

            $where = convert_to_array($where);

            if (isset($where['system_id'])) {
                if (!empty($where['system_id'])) {
                    $sites_installed_on = $this->get_installed_systems($account_id, false, ['system_id'=>$where[ 'system_id'], 'detailed'=> 1 ]);
                    $site_ids = !empty($sites_installed_on) ? array_column($sites_installed_on, 'site_id') : false;
                    if (!empty($site_ids)) {
                        $this->db->where_in('site.site_id', $site_ids);
                    }
                }
                unset($where['system_id']);
            }

            $this->db->select('site.*, diary_regions.region_name, site_statuses.status_name, site_event_statuses.event_tracking_status_id, site_event_statuses.event_tracking_status, site_event_statuses.status_group, site_event_statuses.hex_color, site_event_statuses.icon_class, addrs.main_address_id,addrs.addressline1 `address_line_1`,addrs.addressline2 `address_line_2`,addrs.postcode `postcode`,addrs.summaryline, addrs.xcoords `gps_latitude`,addrs.ycoords `gps_longitude`, audit_result_statuses.*', false)
                ->join('addresses addrs', 'addrs.main_address_id = site.site_address_id', 'left')
                ->join('site_statuses', 'site_statuses.status_id = site.status_id', 'left')
                ->join('site_event_statuses', 'site_event_statuses.event_tracking_status_id = site.event_tracking_status_id', 'left')
                ->join('audit_result_statuses', 'audit_result_statuses.audit_result_status_id = site.audit_result_status_id', 'left')
                ->join('diary_regions', 'diary_regions.region_id = site.region_id', 'left')
                ->where('site.account_id', $account_id)
                ->where('site.archived !=', 1);

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

                        if (!empty($search_where['site.status_id'])) {
                            $search_where['site_statuses.status_name'] =   trim($term);
                            unset($search_where['site.status_id']);
                        }

                        if (!empty($search_where['site.site_address_id'])) {
                            $search_where['addrs.summaryline'] =   trim($term);
                            unset($search_where['site.site_address_id']);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->searchable_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    if (!empty($search_where['site.status_id'])) {
                        $search_where['site_statuses.status_name'] =  $search_term;
                        unset($search_where['site.status_id']);
                    }

                    if (!empty($search_where['site.site_address_id'])) {
                        $search_where['addrs.summaryline'] =  $search_term;
                        unset($search_where['site.site_address_id']);
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where['monitored']) && ($where['monitored'] == 1)) {
                $this->db->where('( site.event_tracking_status_id > 0 )');
                unset($where['monitored']);
            }

            if (isset($where['group'])) {
                if (!empty($where['group'])) {
                }
                unset($where['group']);
            }

            if (isset($where['system_id'])) {
                if (!empty($where['system_id'])) {
                }
                unset($where['system_id']);
            }

            if (isset($where['result_status_id'])) {
                if (!empty($where['result_status_id'])) {
                    $this->db->where('site.audit_result_status_id', $where['result_status_id']);
                }
                unset($where['result_status_id']);
            }

            if (isset($where['exclude_sites'])) {
                if (!empty($where['exclude_sites'])) {
                    $exclude_sites = is_array($where['exclude_sites']) ? $where['exclude_sites'] : [ $where['exclude_sites'] ];
                    $this->db->where_not_in('site.site_id', $exclude_sites);
                }
                unset($where['exclude_sites']);
            }

            if (isset($where['contract_id'])) {
                if (!empty($where['contract_id'])) {
                    $this->db->join('sites_contracts', 'sites_contracts.site_id = site.site_id', 'left');
                    $this->db->where('sites_contracts.contract_id', $where['contract_id']);
                }
                unset($where['contract_id']);
            }

            if (isset($where['region_id'])) {
                if (!empty($where['region_id'])) {
                    $this->db->where('site.region_id', $where['region_id']);
                    unset($where['region_id']);
                }
            }

            if (isset($where['site_postcodes'])) {
                if (!empty($where['site_postcodes'])) {
                    $this->db->where('( site.site_postcodes = "'.$where['site_postcodes'].' OR site.site_postcodes = "'.strip_all_whitespace($where['site_postcodes']).' ")');
                }
                unset($where['site_postcodes']);
            }

            if ($where) {
                $this->db->where($where);
            }

            if ($order_by) {
                $order = $this->ssid_common->_clean_order_by($order_by, $this->primary_tbl);
                if (!empty($order)) {
                    $this->db->order_by($order);
                }
            } else {
                $this->db->order_by('site.site_name');
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->get($this->primary_tbl);

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
    * Get total site count for the search
    */
    public function get_total_sites($account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            #Limit access by Associated Buildings
            if ((!$this->ion_auth->_current_user()->is_admin) && !empty($this->ion_auth->_current_user()->buildings_visibility) && (strtolower($this->ion_auth->_current_user()->buildings_visibility) == 'limited')) {
                $buildings_access 	= $this->get_user_associated_buildings($account_id, $this->ion_auth->_current_user->id);
                $allowed_buildings  = !empty($buildings_access) ? array_column($buildings_access, 'site_id') : [];
                if (!empty($allowed_buildings)) {
                    $this->db->where_in('site.site_id', $allowed_buildings);
                } else {
                    $this->session->set_flashdata('message', 'No Buildings data found matching your criteria.');
                    return false;
                }
            }

            #Limit access by contract to External User Types
            if (in_array($this->ion_auth->_current_user()->user_type_id, EXTERNAL_USER_TYPES)) {
                $contract_access 	= $this->contract_service->get_linked_people($account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1]);
                $allowed_contracts  = !empty($contract_access) ? array_column($contract_access, 'contract_id') : [];
                if (!empty($allowed_contracts)) {
                    $contract_access 	= $this->contract_service->get_linked_sites($account_id, $allowed_contracts, [ 'ids_only'=>1, 'ignore_schedule_check'=> 1 ], -1);
                    $allowed_sites  = !empty($contract_access) ? $contract_access : [];
                    $this->db->where_in('site.site_id', $allowed_sites);
                } else {
                    $this->session->set_flashdata('message', 'No data found matching your criteria');
                    return false;
                }
            }

            #Limit access by Assigned Reqion
            if (!$this->ion_auth->_current_user()->is_admin) {
                if (!empty($this->ion_auth->_current_user()->region_id)) {
                    $this->db->where('site.region_id', $this->ion_auth->_current_user()->region_id);
                }
            }

            $where = convert_to_array($where);

            if (isset($where['system_id'])) {
                if (!empty($where['system_id'])) {
                    $sites_installed_on = $this->get_installed_systems($account_id, false, ['system_id'=>$where[ 'system_id'], 'detailed'=> 1 ]);
                    $site_ids = !empty($sites_installed_on) ? array_column($sites_installed_on, 'site_id') : false;
                    if (!empty($site_ids)) {
                        $this->db->where_in('site.site_id', $site_ids);
                    }
                }
                unset($where['system_id']);
            }

            $this->db->select('site.id', false)
                ->join('addresses addrs', 'addrs.main_address_id = site.site_address_id', 'left')
                ->join('site_statuses', 'site_statuses.status_id = site.status_id', 'left')
                ->join('site_event_statuses', 'site_event_statuses.event_tracking_status_id = site.event_tracking_status_id', 'left')
                ->join('audit_result_statuses', 'audit_result_statuses.audit_result_status_id = site.audit_result_status_id', 'left')
                ->where('site.account_id', $account_id);

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

                        if (!empty($search_where['site.status_id'])) {
                            $search_where['site_statuses.status_name'] =   trim($term);
                            unset($search_where['site.status_id']);
                        }

                        if (!empty($search_where['site.site_address_id'])) {
                            $search_where['addrs.summaryline'] =   trim($term);
                            unset($search_where['site.site_address_id']);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->searchable_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    if (!empty($search_where['site.status_id'])) {
                        $search_where['site_statuses.status_name'] =  $search_term;
                        unset($search_where['site.status_id']);
                    }

                    if (!empty($search_where['site.site_address_id'])) {
                        $search_where['addrs.summaryline'] =  $search_term;
                        unset($search_where['site.site_address_id']);
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            //Check for a setting that specifies whether or not to only get monitored sites
            if (isset($where['site_statuses'])) {
                $where['site_statuses'] = (is_array($where['site_statuses'])) ? $where['site_statuses'] : (is_string($where['site_statuses']) ? str_to_array($where['site_statuses']) : $where['site_statuses']);
                if (!empty($where['site_statuses'])) {
                    $this->db->where_in('site.status_id', $where['site_statuses']);
                }
                unset($where['site_statuses']);
            }

            //Check for a setting that specifies whether or not to only get monitored sites
            if (!empty($where['monitored']) && ($where['monitored'] == 1)) {
                $this->db->where('( site.event_tracking_status_id > 0 )');
                unset($where['monitored']);
            }

            if (isset($where['group'])) {
                if (!empty($where['group'])) {
                }
                unset($where['group']);
            }

            if (isset($where['system_id'])) {
                if (!empty($where['system_id'])) {
                }
                unset($where['system_id']);
            }

            if (isset($where['result_status_id'])) {
                if (!empty($where['result_status_id'])) {
                    $this->db->where('site.audit_result_status_id', $where['result_status_id']);
                }
                unset($where['result_status_id']);
            }

            if (isset($where['exclude_sites'])) {
                if (!empty($where['exclude_sites'])) {
                    $exclude_sites = is_array($where['exclude_sites']) ? $where['exclude_sites'] : [ $where['exclude_sites'] ];
                    $this->db->where_not_in('site.site_id', $exclude_sites);
                }
                unset($where['exclude_sites']);
            }


            if (isset($where['contract_id'])) {
                if (!empty($where['contract_id'])) {
                    $this->db->join('sites_contracts', 'sites_contracts.site_id = site.site_id', 'left');
                    $this->db->where('sites_contracts.contract_id', $where['contract_id']);
                }
                unset($where['contract_id']);
            }

            if (isset($where['region_id'])) {
                if (!empty($where['region_id'])) {
                    $this->db->where('site.region_id', $where['region_id']);
                    unset($where['region_id']);
                }
            }

            if (isset($where['site_postcodes'])) {
                if (!empty($where['site_postcodes'])) {
                    $this->db->where('( site.site_postcodes = "'.$where['site_postcodes'].' OR site.site_postcodes = "'.strip_all_whitespace($where['site_postcodes']).' ")');
                }
                unset($where['site_postcodes']);
            }

            if ($where) {
                $this->db->where($where);
            }

            if ($order_by) {
                $order = $this->ssid_common->_clean_order_by($order_by, $this->primary_tbl);
                if (!empty($order)) {
                    $this->db->order_by($order);
                }
            } else {
                $this->db->order_by('site.site_name');
            }

            $query = $this->db->from('site')->count_all_results();
            $results['total'] = !empty($query) ? $query : 0;
            $results['pages'] = !empty($query) ? ceil($query / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }


    public function create_site_change_log($account_id=false, $site_id=false, $log_data = false)
    {
        if ($account_id && $site_id && $log_data) {
            $data   = $this->ssid_common->_filter_data('site_change_log', $log_data);
            $data['site_id']      = $site_id;
            $data['account_id']   = $account_id;
            $data['created_by']   = $this->ion_auth->_current_user->id;
            $data['updated_data'] = json_encode($log_data);
            $this->db->insert('site_change_log', $data);
        }
        return true;
    }

    public function get_site_change_logs($account_id=false, $site_id=false)
    {
        $result = false;
        $this->db->select('scl.*,concat(user.first_name," ",user.last_name) `created_by`')
            ->order_by('scl.id desc')
            ->where('scl.account_id', $account_id)
            ->join('user', 'user.id = scl.created_by', 'left');

        if ($site_id) {
            $this->db->where('scl.site_id', $site_id);
        }
        $query = $this->db->get('site_change_log scl');
        if ($query->num_rows() > 0) {
            $result = $query->result();
        }
        return $result;
    }

    /** Get site contracts **/
    public function get_site_contracts($account_id = false, $site_id = false, $grouped = true)
    {
        $result = null;
        if (!empty($account_id) && !empty($site_id)) {
            $this->db->select('sc.link_id, contract.*, ct.type_name, cs.status_name, concat(user.first_name," ",user.last_name) `lead_person`', false)
                ->join('contract', 'contract.contract_id = sc.contract_id', 'left')
                ->join('contract_type ct', 'ct.type_id = contract.contract_type_id', 'left')
                ->join('contract_status cs', 'cs.status_id = contract.contract_status_id', 'left')
                ->join('user', 'user.id = contract.contract_lead_id', 'left')
                ->order_by('sc.link_id desc')
                ->where('sc.account_id', $account_id);

            if ($site_id) {
                $this->db->where('sc.site_id', $site_id);
            }

            $query = $this->db->get('sites_contracts sc');

            if ($query->num_rows() > 0) {
                $data 	= [];
                foreach ($query->result() as $row) {
                    $data[$row->type_name][] = $row;
                }
                $result = $data;
            }
        }
        return $result;
    }

    /** Get Site statuses **/
    public function get_site_statuses($account_id = false)
    {
        $result = null;

        if ($account_id) {
            $this->db->where('site_statuses.account_id', $account_id);
        } else {
            $this->db->where('( site_statuses.account_id IS NULL OR site_statuses.account_id = "" )');
        }

        $query = $this->db->where('is_active', 1)->get('site_statuses');

        if ($query->num_rows() > 0) {
            $result = $query->result();
        } else {
            $result = $this->get_site_statuses();
        }

        return $result;
    }

    /** Get Site Event statuses **/
    public function get_site_event_statuses($account_id = false)
    {
        $result = null;

        if ($account_id) {
            $query = $this->db->where('is_active', 1)->get('site_event_statuses');

            if ($query->num_rows() > 0) {
                $result = $query->result();
            }
        }

        return $result;
    }


    /** Get Site Related Statistics **/
    public function get_site_stats($account_id=false, $stat_type = false, $date_from=false, $date_to=false)
    {
        $result = false;

        if (!empty($account_id) && !empty($stat_type)) {
            switch(strtolower($stat_type)) {
                case 'audit_result_status':
                    $site_data = $this->db->select('audit_result_statuses.*,
							SUM( CASE WHEN ( site.audit_result_status_id = 0 OR site.audit_result_status_id IS NULL OR site.audit_result_status_id != "" ) THEN 1 ELSE 0 END ) AS status_not_set,
							SUM( CASE WHEN  site.audit_result_status_id > 0 THEN 1 ELSE 0 END ) AS status_total', false)
                        ->join('site', 'audit_result_statuses.audit_result_status_id = site.audit_result_status_id', 'left')
                        ->where('audit_result_statuses.account_id', $account_id)
                        ->where('audit_result_statuses.is_active', 1)
                        ->order_by('audit_result_statuses.result_ordering')
                        ->group_by('audit_result_statuses.audit_result_status_id')
                        ->get('audit_result_statuses');
                    $num_rows = ($site_data->num_rows() > 0) ? true : false;
                    break;

                case 'another_case':
                    //
                    break;
            }

            if (!empty($num_rows)) {
                $this->session->set_flashdata('message', 'Building stats found');

                ## For Site Compliance Statistics
                if ($stat_type == 'audit_result_status') {
                    $data = [];
                    $grand_total 					= ( string ) array_sum(array_column($site_data->result_array(), 'status_total'));//Get the grand total
                    $stats_arr 						= array_combine(array_map('strtolower', array_column($site_data->result_array(), 'result_status_group')), array_column($site_data->result_array(), 'status_total')); //creata a new array if column => value
                    $data['stats']					= $site_data->result_array();
                    $data['totals'] 				= (!empty($stats_arr) && !empty($grand_total)) ? array_merge(['grand_total'=>$grand_total], $stats_arr) : [];

                    #Calculate Complaiance using what has passed + recommendations
                    if (!empty($data['totals']['grand_total']) && (!empty($data['totals']['passed']) && ($data['totals']['passed'] > 0))) {
                        $data['totals']['compliance'] 	  = (number_format(((($data['totals']['passed'] + (!empty($data['totals']['recommendations'] ? $data['totals']['recommendations'] : 0))) / $data['totals']['grand_total']) * 100), 2) + 0).'%';
                        $data['totals']['compliance_raw'] =  ( string ) (number_format(((($data['totals']['passed'] + (!empty($data['totals']['recommendations'] ? $data['totals']['recommendations'] : 0))) / $data['totals']['grand_total']) * 100), 4) + 0);
                        $data['totals']['compliance_alt'] = 'Compliant';
                    }

                    # Calculate compliance based on what has failed
                    if (!empty($data['totals']['grand_total']) && (!empty($data['totals']['failed']) && ($data['totals']['failed'] > 0))) {
                        $data['totals']['compliance'] 	  = (number_format(((($data['totals']['failed']) / $data['totals']['grand_total']) * 100), 2) + 0).'%';
                        $data['totals']['compliance_raw'] = ( string ) (number_format(((($data['totals']['failed']) / $data['totals']['grand_total']) * 100), 2) + 0);
                        $data['totals']['compliance_alt'] = 'Not Compliant';
                    }

                    $result = $data;
                } else {
                    $result = $site_datas->result();
                }
            } else {
                $this->session->set_flashdata('message', 'Building stats not available');
            }
        } else {
            $this->session->set_flashdata('message', 'Missing required information');
        }
        return $result;
    }


    public function get_lookup_instant_stats($account_id = false, $search_term = false, $site_statuses = false, $where = false, $order_by = false, $limit = 999, $offset = false)
    {
        $result = $site_lookup = $site_stats = false;

        if (!empty($account_id)) {
            ## $site_lookup	= $this->site_lookup( $account_id, $search_term, $site_statuses, $where, $order_by, 999, $offset );

            $site_stats 	= $this->get_site_stats($account_id, "audit_result_status");

            $audits			= $this->audit_service->get_audits($account_id, false, false, false, false, false, false, ("audit.site_id != ''"));

            if (!empty($audits) && !empty($site_stats)) {
                $result = [
                    "sites_not_compliant" 	=> 0,
                    "failed_audits" 		=> 0,
                    "recommendations_audits"=> 0,
                    "sites_compliant" 		=> 0,
                ];

                $result['sites_not_compliant'] 	= $site_stats['totals']['failed'];
                $result['sites_compliant'] 		= $site_stats['totals']['passed'];

                if (!empty($audits)) {
                    foreach ($audits as $key => $row) {
                        if (in_array($row->result_status_group, ["failed"])) {
                            $result['failed_audits']++;
                        }

                        if (in_array($row->result_status_group, ["recommendations"])) {
                            $result['recommendations_audits']++;
                        }

                        /* if( in_array( $row->result_status_group, ["passed"] ) ){
                            $result['sites_compliant']++;
                        } */
                    }
                }

                $this->session->set_flashdata('message', 'Stats data found');
                return $result;
            } else {
                $this->session->set_flashdata('message', 'Stats data not found');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }


    /** Create a new Location record record **/
    public function create_site_locations($account_id = false, $site_id = false, $locations_data = false)
    {
        $result = null;

        if (!empty($account_id) && !empty($site_id) && !empty($locations_data)) {
            $locations_data   	= convert_to_array($locations_data);

            $new_data = $existing_records = [];
            $multiple_locations = !empty($locations_data['site_locations']) ? $locations_data['site_locations'] : false;
            $multiple_locations	= (is_string($multiple_locations)) ? json_decode($multiple_locations) : $multiple_locations;
            $multiple_locations	= (is_object($multiple_locations)) ? object_to_array($multiple_locations) : $multiple_locations;

            unset($locations_data['site_locations']);

            if (!empty($multiple_locations)) {
                foreach ($multiple_locations as $k => $data) {
                    $address_id      	 		= !empty($data['address_id']) ? $data['address_id'] : '';
                    $location_type_id	 		= (!empty($data['location_type_id'])) ? $data['location_type_id'] : 2;
                    $data['location_type_id'] 	= $location_type_id;

                    $data	 			 		= array_merge(array_map('trim', $data), [ 'account_id'=>$account_id, 'site_id'=>$site_id ]);
                    $location_ref		 		= $this->generate_location_ref($account_id, $data);
                    $data['location_ref']		= strtoupper($location_ref);

                    if (!empty($address_id)) {
                        $address = $this->db->get_where('addresses', [ 'main_address_id'=>$address_id ])->row();
                        $data['address_line1'] 		= (!empty($address->addressline1)) ? $address->addressline1 : '';
                        $data['address_line2'] 		= (!empty($address->addressline2)) ? $address->addressline2 : '';
                        $data['address_line3'] 		= (!empty($address->addressline3)) ? $address->addressline3 : '';
                        $data['address_town']  		= (!empty($address->posttown)) ? $address->posttown : '';
                        $data['address_county']		= (!empty($address->county)) ? $address->county : '';
                        $data['address_postcode']	= (!empty($address->postcode)) ? $address->postcode : '';

                        $this->db->where('site_locations.address_id', $address_id);
                    }

                    $check_exists = $this->db->select('site_locations.location_id', false)
                        ->where('site_locations.account_id', $account_id)
                        ->where([ 'site_id'=>$site_id, 'location_type_id'=>$location_type_id, 'location_ref'=>$location_ref ])
                        ->limit(1)
                        ->get('site_locations')
                        ->row();

                    $data = $this->ssid_common->_filter_data('site_locations', $data);

                    if (!empty($check_exists)) {
                        $data['location_id'] 		= $check_exists->location_id;
                        $data['last_modified_by'] 	= $this->ion_auth->_current_user->id;
                        $existing_records[] 		= array_map('strval', $data);
                    } else {
                        $data['created_by'] 		= $this->ion_auth->_current_user->id;
                        $new_records[] 				= array_map('strval', $data);
                    }
                }
            } else {
                foreach ($locations_data as $col => $value) {
                    $data[$col] = !is_array($value) ? trim($value) : $value;
                }

                $address_id      	 		= !empty($data['address_id']) ? $data['address_id'] : '';
                $location_type_id	 		= (!empty($data['location_type_id'])) ? $data['location_type_id'] : 2;
                $data['location_type_id'] 	= $location_type_id;

                $data	 			 		= array_merge($data, [ 'account_id'=>$account_id, 'site_id'=>$site_id ]);
                $location_ref		 		= $this->generate_location_ref($account_id, $data);
                $data['location_ref']		= strtoupper($location_ref);

                if (!empty($address_id)) {
                    $address = $this->db->get_where('addresses', [ 'main_address_id'=>$address_id ])->row();
                    $data['address_line1'] 		= (!empty($address->addressline1)) ? $address->addressline1 : '';
                    $data['address_line2'] 		= (!empty($address->addressline2)) ? $address->addressline2 : '';
                    $data['address_line3'] 		= (!empty($address->addressline3)) ? $address->addressline3 : '';
                    $data['address_town']  		= (!empty($address->posttown)) ? $address->posttown : '';
                    $data['address_county']		= (!empty($address->county)) ? $address->county : '';
                    $data['address_postcode']	= (!empty($address->postcode)) ? $address->postcode : '';

                    $this->db->where('site_locations.address_id', $address_id);
                }

                $check_exists = $this->db->select('site_locations.location_id', false)
                    ->where('site_locations.account_id', $account_id)
                    ->where([ 'site_id'=>$site_id, 'location_type_id'=>$location_type_id, 'location_ref'=>$location_ref ])
                    ->limit(1)
                    ->get('site_locations')
                    ->row();

                $data = $this->ssid_common->_filter_data('site_locations', $data);

                if (!empty($check_exists)) {
                    $data['location_id'] 		= $check_exists->location_id;
                    $data['last_modified_by'] 	= $this->ion_auth->_current_user->id;
                    $existing_records[] 		= array_map('strval', $data);
                } else {
                    $data['created_by'] 		= $this->ion_auth->_current_user->id;
                    $new_records[] 				= array_map('strval', $data);
                }
            }

            if (!empty($existing_records)) {
                $this->db->update_batch('site_locations', $existing_records, 'location_id');
                $this->session->set_flashdata('message', 'Location record(s) updated successfully.');
                #$result['updated_records'] = $existing_records;
                $result = $existing_records;
            }

            if (!empty($new_records)) {
                $this->db->insert_batch('site_locations', $new_records);
                $this->session->set_flashdata('message', 'Location record(s) created successfully.');
                #$result['new_records'] = $new_records;
                $result = $new_records;
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }

        return (count($result) == 1) ? $result[0] : $result;
    }

    /** Update an existing Location record **/
    public function update_site_location($account_id = false, $location_id = false, $update_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($location_id)  && !empty($update_data)) {
            $ref_condition = [ 'account_id'=>$account_id, 'location_id'=>$location_id ];
            $update_data   = $this->ssid_common->_data_prepare($update_data);
            $update_data   = $this->ssid_common->_filter_data('site_locations', $update_data);
            $record_pre_update = $this->db->get_where('site_locations', [ 'account_id'=>$account_id, 'location_id'=>$location_id ])->row();

            if (!empty($record_pre_update)) {
                $address_id		= !empty($update_data['address_id']) ? $update_data['address_id'] : $record_pre_update->address_id;
                $location_ref	= !empty($update_data['location_ref']) ? $update_data['location_ref'] : $this->generate_location_ref($update_data);

                $check_conflict = $this->db->select('site_locations.*', false)
                    ->where('site_locations.account_id', $account_id)
                    ->where('site_locations.location_ref', $location_ref)
                    ->where([ 'site_id'=>$update_data['site_id'], 'location_type_id'=>$update_data['location_type_id'] ])
                    ->where('location_id !=', $location_id)
                    ->limit(1)
                    ->get('site_locations')
                    ->row();

                if (!$check_conflict) {
                    $update_data['last_modified_by'] = $this->ion_auth->_current_user->id;
                    $this->db->where($ref_condition)
                        ->update('site_locations', $update_data);

                    $updated_record = $this->get_site_locations($account_id, $location_id);
                    $result 		= (!empty($updated_record->records)) ? $updated_record->records : (!empty($updated_record) ? $updated_record : false);

                    $this->session->set_flashdata('message', 'Location record updated successfully');
                    return $result;
                } else {
                    $this->session->set_flashdata('message', 'A Location with these details already exists ('.$ref_condition.')! Update request aborted');
                    return false;
                }
            } else {
                $this->session->set_flashdata('message', 'This Location record does not exist or does not belong to you.');
                return false;
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }
        return $result;
    }

    /*
    *	Get list of Location records and search through them
    */
    public function get_site_locations($account_id = false, $location_id = false, $search_term = false, $where = false, $order_by = false, $limit = 500, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select('site_locations.*, site_sub_blocks.sub_block_name, site_zones.zone_name, site_zones.zone_description, dt.location_type, dt.location_group, dt.location_type_desc, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false)
                ->join('location_types dt', 'dt.location_type_id = site_locations.location_type_id', 'left')
                ->join('site_zones', 'site_zones.zone_id = site_locations.zone_id', 'left')
                ->join('site_sub_blocks', 'site_zones.sub_block_id = site_sub_blocks.sub_block_id', 'left')
                ->join('user creater', 'creater.id = site_locations.created_by', 'left')
                ->join('user modifier', 'modifier.id = site_locations.last_modified_by', 'left')
                ->where('site_locations.is_active', 1)
                ->where('site_locations.account_id', $account_id);

            $where = $raw_where = convert_to_array($where);

            if (isset($where['location_id']) || !empty($location_id)) {
                $location_id	= (!empty($where['location_id'])) ? $where['location_id'] : $location_id;
                if (!empty($location_id)) {
                    if (!empty($where['site_id'])) {
                        $this->db->where('site_locations.site_id', $where['site_id']);
                    }

                    $row = $this->db->get_where('site_locations', ['location_id'=>$location_id ])->row();

                    if (!empty($row)) {
                        $result = $row;
                        $this->session->set_flashdata('message', 'Location records data found');
                        return $result;
                    } else {
                        $this->session->set_flashdata('message', 'Location records data not found');
                        return false;
                    }
                }
                unset($where['location_id'], $where['location_ref']);
            }

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->location_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->location_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where['grouped'])) {
                $grouped = true;
            }

            if (isset($where['zone_id'])) {
                if (!empty($where['zone_id'])) {
                    $this->db->where('site_locations.zone_id', $where['zone_id']);
                }
                unset($where['zone_id']);
            }

            if (isset($where['site_id'])) {
                if (!empty($where['site_id'])) {
                    $this->db->where('site_locations.site_id', $where['site_id']);
                }
                unset($where['site_id']);
            }


            if (isset($where['sub_block_id'])) {
                if (!empty($where['sub_block_id'])) {
                    $this->db->where('site_locations.sub_block_id', $where['sub_block_id']);
                }
                unset($where['sub_block_id']);
            }

            if (!empty($where)) {
                $this->db->where($where);
            }

            if (!empty($order_by)) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('LENGTH(site_zones.zone_name) asc, site_zones.zone_name asc, location_name, resident_first_name');
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->group_by('site_locations.location_id')
                ->get('site_locations');

            if ($query->num_rows() > 0) {
                $result_data = $query->result();

                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $result->records 			= $result_data;
                $counters 					= $this->get_location_totals($account_id, $search_term, $raw_where, $limit);
                $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
                $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
                $result->counters->limit  	= ($limit > 0) ? $limit : $result->counters->total;
                $result->counters->offset 	= $offset;

                $this->session->set_flashdata('message', 'Location records data found');
            } else {
                $this->session->set_flashdata('message', 'There\'s currently no Location records setup for your Account');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }


    /** Get Location record lookup counts **/
    public function get_location_totals($account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('site_locations.location_id', false)
                ->join('location_types dt', 'dt.location_type_id = site_locations.location_type_id', 'left')
                ->join('site_zones', 'site_zones.zone_id = site_locations.zone_id', 'left')
                ->where('site_locations.is_active', 1)
                ->where('site_locations.account_id', $account_id);

            $where = convert_to_array($where);

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->location_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->location_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where['grouped'])) {
                $grouped = true;
            }

            if (isset($where['zone_id'])) {
                if (!empty($where['zone_id'])) {
                    $this->db->where('site_locations.zone_id', $where['zone_id']);
                }
                unset($where['zone_id']);
            }

            if (isset($where['site_id'])) {
                if (!empty($where['site_id'])) {
                    $this->db->where('site_locations.site_id', $where['site_id']);
                }
                unset($where['site_id']);
            }

            if (isset($where['sub_block_id'])) {
                if (!empty($where['sub_block_id'])) {
                    $this->db->where('site_locations.sub_block_id', $where['sub_block_id']);
                }
                unset($where['sub_block_id']);
            }

            if (!empty($where)) {
                $this->db->where($where);
            }

            $query 			  = $this->db->from('site_locations')->count_all_results();
            $results['total'] = !empty($query) ? $query : 0;
            $limit 			  = (!empty($limit > 0)) ? $limit : $results['total'];
            $results['pages'] = !empty($query) ? ceil($query / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }

    /** Generate Sub-Block Ref **/
    private function generate_sub_block_ref($account_id = false, $data = false)
    {
        if (!empty($account_id) && !empty($data)) {
            $zone_ref = $account_id;
            $zone_ref .= !empty($data['sub_block_name']) ? strip_all_whitespace($data['sub_block_name']) : '';
            $zone_ref .= !empty($data['sub_block_postcode']) ? strip_all_whitespace($data['sub_block_postcode']) : '';
            $zone_ref .= !empty($data['site_id']) ? $data['site_id'] : '';
            $zone_ref .= !empty($data['address_id']) ? $data['address_id'] : '';
        } else {
            $zone_ref = $account_id.$this->ssid_common->generate_random_password();
        }
        return strtoupper($zone_ref);
    }


    /** Generate Zone Ref **/
    private function generate_zone_ref($account_id = false, $data = false)
    {
        if (!empty($account_id) && !empty($data)) {
            $zone_ref = $account_id;
            $zone_ref .= !empty($data['zone_name']) ? strip_all_whitespace($data['zone_name']) : '';
            $zone_ref .= !empty($data['site_id']) ? $data['site_id'] : '';
            $zone_ref .= !empty($data['sub_block_id']) ? $data['sub_block_id'] : '';
            $zone_ref .= !empty($data['address_id']) ? $data['address_id'] : '';
        } else {
            $zone_ref = $account_id.$this->ssid_common->generate_random_password();
        }
        return strtoupper($zone_ref);
    }


    /** Generate Schedule Ref **/
    private function generate_location_ref($account_id = false, $data = false)
    {
        if (!empty($account_id) && !empty($data)) {
            $location_ref = $account_id;
            $location_ref .= !empty($data['location_name']) ? strip_all_whitespace($data['location_name']) : '';
            $location_ref .= !empty($data['resident_salutation']) ? $data['resident_salutation'] : '';
            $location_ref .= !empty($data['resident_first_name']) ? $data['resident_first_name'] : '';
            $location_ref .= !empty($data['resident_last_name']) ? $data['resident_last_name'] : '';
            $location_ref .= !empty($data['location_type_id']) ? $data['location_type_id'] : '';
            $location_ref .= !empty($data['contract_id']) ? $data['contract_id'] : '';
            $location_ref .= !empty($data['site_id']) ? $data['site_id'] : '';
            $location_ref .= !empty($data['sub_block_id']) ? $data['sub_block_id'] : '';
            $location_ref .= !empty($data['zone_id']) ? $data['zone_id'] : '';
            $location_ref .= !empty($data['address_id']) ? $data['address_id'] : '';
        } else {
            $location_ref = $account_id.$this->ssid_common->generate_random_password();
        }
        return strtoupper($location_ref);
    }

    /** Get Site Zones **/
    public function get_site_zones($account_id = false, $where = false, $limit = 500, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if ($account_id) {
            $this->db->select('site_zones.*, site_sub_blocks.sub_block_name', false)
                ->where('site_zones.account_id', $account_id);

            if (!empty($where)) {
                $where = convert_to_array($where);

                if (isset($where['zone_id'])) {
                    if (!empty($where['zone_id'])) {
                        $this->db->where('site_zones.zone_id', $where['zone_id']);
                        $single_record = true;
                    }
                    unset($where['zone_id']);
                }

                if (isset($where['site_id'])) {
                    if (!empty($where['site_id'])) {
                        $this->db->where('site_zones.site_id', $where['site_id']);
                    }
                    unset($where['site_id']);
                }
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->order_by('LENGTH(zone_name) asc, zone_name asc')
                ->join('site_sub_blocks', 'site_zones.sub_block_id = site_sub_blocks.sub_block_id', 'left')
                ->get('site_zones');

            if ($query->num_rows() > 0) {
                if (!empty($single_record)) {
                    $row 					= $query->result()[0];
                    $zone_locations			= $this->get_site_locations($account_id, false, false, ['zone_id'=>$row->zone_id]);
                    $row->zone_locations 	= !empty($zone_locations->records) ? $zone_locations->records : null;
                    return $row;
                } else {
                    $data = [];
                    foreach ($query->result() as $k => $row) {
                        $zone_locations			= $this->get_site_locations($account_id, false, false, ['zone_id'=>$row->zone_id]);
                        $row->zone_locations 	= !empty($zone_locations->records) ? $zone_locations->records : null;
                        $data[$k] = $row;
                    }
                    $result = $data;
                }
                $this->session->set_flashdata('message', 'Building Zones data found.');
            } else {
                $this->session->set_flashdata('message', 'Building Zones data not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }

        return $result;
    }

    /** Add a New Site Zone **/
    public function add_site_zone($account_id = false, $site_id = false, $site_zone_data = false)
    {
        $result = null;

        if (!empty($account_id) && !empty($site_id) && !empty($site_zone_data)) {
            if (!empty($site_zone_data['sub_block_id'])) {
                $sub_block = $this->get_site_sub_blocks($account_id, ['sub_block_id'=>$site_zone_data['sub_block_id']]);
            }

            foreach ($site_zone_data as $col => $value) {
                if ($col == 'zone_name') {
                    $value				= !empty($sub_block->sub_block_name) ? $sub_block->sub_block_name.' '.$value : $value;
                    $data['zone_ref'] 	= $this->generate_zone_ref($account_id, $site_zone_data);
                    ;
                }
                $data[$col] = $value;
            }

            if (!empty($data['override_existing']) && !empty($data['zone_id'])) {
                $check_exists = $this->db->where('account_id', $account_id)
                    ->where('zone_id', $data['zone_id'])
                    ->get('site_zones')->row();
            } else {
                unset($data['zone_id']);
                $check_exists = $this->db->where('account_id', $account_id)
                    ->where('( ( site_zones.zone_name = "'.$data['zone_name'].'" && site_zones.zone_name = "'.$site_id.'" ) OR site_zones.zone_ref = "'.$data['zone_ref'].'" )')
                    ->limit(1)
                    ->get('site_zones')
                    ->row();
            }

            $data = $this->ssid_common->_filter_data('site_zones', $data);

            if (!empty($check_exists)) {
                $data['last_modified_by'] = $this->ion_auth->_current_user->id;
                $this->db->where('zone_id', $check_exists->zone_id)
                    ->update('site_zones', $data);
                $this->session->set_flashdata('message', 'This Site Zone already exists, record has been updated successfully.');
                $result = $check_exists;
            } else {
                $data['created_by'] 		= $this->ion_auth->_current_user->id;
                $this->db->insert('site_zones', $data);
                $this->session->set_flashdata('message', 'New Site Zone added successfully.');
                $data['zone_id'] = (string) $this->db->insert_id();
                $result = $data;
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }

        return $result;
    }

    /** Update Site Zone **/
    public function update_site_zone($account_id = false, $zone_id = false, $site_zone_data = false)
    {
        $result = null;

        if (!empty($account_id) && !empty($zone_id) && !empty($site_zone_data)) {
            foreach ($site_zone_data as $col => $value) {
                if ($col == 'zone_name') {
                    $data['zone_ref'] 		= $account_id.strtolower(strip_all_whitespace($value)).$site_id;
                    ;
                }
                $data[$col] = $value;
            }

            if (!empty($data['zone_id'])) {
                $check_exists = $this->db->where('account_id', $account_id)
                    ->where('zone_id', $data['zone_id'])
                    ->get('site_zones')->row();

                $data = $this->ssid_common->_filter_data('site_zones', $data);

                if (!empty($check_exists)) {
                    $data['last_modified_by'] = $this->ion_auth->_current_user->id;
                    $this->db->where('zone_id', $check_exists->zone_id)
                        ->update('site_zones', $data);
                    if ($this->db->trans_status() !== false) {
                        $result = $this->get_site_zones($account_id, ['zone_id'=>$data['zone_id']]);
                        $this->session->set_flashdata('message', 'Building Zone updated successfully.');
                    }
                } else {
                    $this->session->set_flashdata('message', 'This Building Zone does not exists or does not belong to you.');
                    $result = false;
                }
            } else {
                $this->session->set_flashdata('message', 'Error! Missing required information.');
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }

        return $result;
    }


    /** Get Site Systems **/
    public function get_expected_systems($account_id = false, $site_id = false)
    {
        $result = null;
        $this->db->select('site_systems.*', false)
            ->where('( site_systems.account_id IS NULL OR site_systems.account_id = "" )');

        /* --- THIS NEEDS REVIEWING WHEN SYSTEMS ARE DONE CORRECTLY ---
        $this->db->select( 'site_systems.*, site_expected_systems.*', false )
            ->join( 'site_systems', 'site_systems.system_id = site_expected_systems.system_id', 'left' );

        if( $account_id ){
            $this->db->where( 'site_expected_systems.account_id', $account_id );
        }else{
            $this->db->where( '( site_expected_systems.account_id IS NULL OR site_systems.account_id = "" )' );
        }

        if( !empty( $site_id ) ){
            #$this->db->where( 'site_expected_systems.site_id', $site_id ); //Uncomment this when we start managing lists per account and per site
        }
        */

        $query = $this->db->where('site_systems.is_active', 1)->get('site_systems');

        if ($query->num_rows() > 0) {
            $result = $query->result();
        } else {
            #$result = $this->get_expected_systems();
        }

        return $result;
    }


    /** Get Site Systems **/
    public function get_installed_systems($account_id = false, $site_id = false, $where = false)
    {
        $result = null;

        if (!empty($account_id)) {
            $where   = convert_to_array($where);

            $this->db->select('asset_types.asset_type_id, asset_types.asset_group, REPLACE( asset_types.asset_type_ref, " ", "" ) `asset_type_ref`, asset.site_id, audit_categories.category_group, site_systems.system_name', false)
                ->join('asset_types', 'asset_types.asset_type_id = asset.asset_type_id', 'left')
                ->join('audit_categories', 'audit_categories.category_id = asset_types.category_id', 'left')
                ->join('site_systems', 'site_systems.system_group = asset_types.asset_type_ref', 'left')
                ->where('asset.account_id', $account_id);

            if (isset($where['system_id'])) {
                if (!empty($where['system_id'])) {
                    $this->db->where('site_systems.system_id', $where['system_id']);
                    $group_by_site = true;
                }
                unset($where['system_id']);
            }

            if (isset($where['system_group'])) {
                if (!empty($where['system_group'])) {
                    $this->db->where('site_systems.system_group', $where['system_group']);
                    $group_by_site = true;
                }
                unset($where['system_group']);
            }

            if (!empty($group_by_site)) {
                $this->db->group_by('asset.site_id');
            } else {
                $this->db->group_by('asset_types.asset_type_ref');
            }

            if (!empty($site_id)) {
                $site_id = is_array($site_id) ? $site_id : [ $site_id ];
                $this->db->where_in('asset.site_id', $site_id);
            }

            $query = $this->db->where('asset_types.asset_group', 'system')
                ->get('asset');

            if ($query->num_rows() > 0) {
                if (!empty($where['detailed'])) {
                    $result = $query->result_array();
                } else {
                    $result = array_map('strtolower', array_column($query->result_array(), 'asset_type_ref'));
                }

                $this->session->set_flashdata('message', 'Installed system data found.');
            } else {
                $this->session->set_flashdata('message', 'No installed system data found on this building / site.');
                $result = false;
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }
        return $result;
    }


    /** Add a New Site Sub Block **/
    public function add_site_sub_block($account_id = false, $site_id = false, $site_sub_block_data = false)
    {
        $result = null;

        if (!empty($account_id) && !empty($site_id) && !empty($site_sub_block_data)) {
            foreach ($site_sub_block_data as $col => $value) {
                if ($col == 'sub_block_name') {
                    $data['sub_block_ref'] 		= $this->generate_sub_block_ref($account_id, $site_sub_block_data);
                    ;
                }
                $data[$col] = $value;
            }

            if (!empty($data['override_existing']) && !empty($data['sub_block_id'])) {
                $check_exists = $this->db->where('account_id', $account_id)
                    ->where('site_sub_blocks.sub_block_id', $data['sub_block_id'])
                    ->get('site_sub_blocks')->row();
            } else {
                unset($data['sub_block_id']);
                $check_exists = $this->db->where('account_id', $account_id)
                    ->where('( ( site_sub_blocks.sub_block_name = "'.$data['sub_block_name'].'" && site_sub_blocks.sub_block_name = "'.$site_id.'" ) OR site_sub_blocks.sub_block_ref = "'.$data['sub_block_ref'].'" )')
                    ->limit(1)
                    ->get('site_sub_blocks')
                    ->row();
            }

            $data = $this->ssid_common->_filter_data('site_sub_blocks', $data);

            if (!empty($check_exists)) {
                $data['last_modified_by'] = $this->ion_auth->_current_user->id;
                $this->db->where('sub_block_id', $check_exists->sub_block_id)
                    ->update('site_sub_blocks.site_sub_blocks', $data);
                $this->session->set_flashdata('message', 'This Site Sub Block already exists, record has been updated successfully.');
                $result = $check_exists;
            } else {
                $data['created_by'] 		= $this->ion_auth->_current_user->id;
                $this->db->insert('site_sub_blocks', $data);
                $this->session->set_flashdata('message', 'New Site Sub Block added successfully.');
                $data['sub_block_id'] = (string) $this->db->insert_id();
                $result = $data;
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }

        return $result;
    }

    /** Update Site Sub Block **/
    public function update_site_sub_block($account_id = false, $sub_block_id = false, $site_sub_block_data = false)
    {
        $result = null;

        if (!empty($account_id) && !empty($sub_block_id) && !empty($site_sub_block_data)) {
            $site_id = !empty($site_sub_block_data['site_id']) ? $site_sub_block_data['site_id'] : '';
            foreach ($site_sub_block_data as $col => $value) {
                if ($col == 'sub_block_name') {
                    $data['sub_block_ref'] = $this->generate_sub_block_ref($account_id, $site_sub_block_data);
                }
                $data[$col] = $value;
            }

            if (!empty($data['sub_block_id'])) {
                $check_exists = $this->db->where('account_id', $account_id)
                    ->where('site_sub_blocks.sub_block_id', $data['sub_block_id'])
                    ->get('site_sub_blocks')->row();

                $data = $this->ssid_common->_filter_data('site_sub_blocks', $data);

                if (!empty($check_exists)) {
                    $data['last_modified_by'] = $this->ion_auth->_current_user->id;
                    $this->db->where('site_sub_blocks.sub_block_id', $check_exists->sub_block_id)
                        ->update('site_sub_blocks', $data);
                    if ($this->db->trans_status() !== false) {
                        $result = $this->get_site_sub_blocks($account_id, ['sub_block_id'=>$data['sub_block_id']]);
                        $this->session->set_flashdata('message', 'Building Sub Block updated successfully.');
                    }
                } else {
                    $this->session->set_flashdata('message', 'This Building Sub Block does not exists or does not belong to you.');
                    $result = false;
                }
            } else {
                $this->session->set_flashdata('message', 'Error! Missing required information.');
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }

        return $result;
    }


    /** Get Site Sub Blocks **/
    public function get_site_sub_blocks($account_id = false, $where = false)
    {
        $result = false;

        if ($account_id) {
            $this->db->select('site_sub_blocks.*, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false)
                ->join('user creater', 'creater.id = site_sub_blocks.created_by', 'left')
                ->join('user modifier', 'modifier.id = site_sub_blocks.last_modified_by', 'left')
                ->where('site_sub_blocks.account_id', $account_id);

            if (!empty($where)) {
                $where = convert_to_array($where);

                if (isset($where['sub_block_id'])) {
                    if (!empty($where['sub_block_id'])) {
                        $this->db->where('site_sub_blocks.sub_block_id', $where['sub_block_id']);
                        $single_record = true;
                    }
                    unset($where['sub_block_id']);
                }

                if (isset($where['site_id'])) {
                    if (!empty($where['site_id'])) {
                        $this->db->where('site_sub_blocks.site_id', $where['site_id']);
                    }
                    unset($where['site_id']);
                }
            }

            $query = $this->db->order_by('sub_block_name')
                ->get('site_sub_blocks');

            if ($query->num_rows() > 0) {
                if (!empty($single_record)) {
                    $row 						= $query->result()[0];
                    $sub_block_locations		= $this->get_site_locations($account_id, false, false, ['sub_block_id'=>$row->sub_block_id], false, $limit = -1);
                    $row->sub_block_locations 	= !empty($sub_block_locations->records) ? $sub_block_locations->records : null;
                    #$row->sub_block_locations 	= false;

                    $sub_block_asssets			= $this->asset_service->get_assets($account_id, false, false, ['sub_block_id'=>$row->sub_block_id], false, $limit = -1);
                    $row->sub_block_asssets 	= !empty($sub_block_asssets) ? $sub_block_asssets : null;

                    $this->session->set_flashdata('message', 'Building Sub Blocks data found.');
                    return $row;
                } else {
                    $data = [];
                    foreach ($query->result() as $k => $row) {
                        $sub_block_locations		= $this->get_site_locations($account_id, false, false, ['sub_block_id'=>$row->sub_block_id], false, $limit = -1);
                        $row->sub_block_locations 	= !empty($sub_block_locations->records) ? $sub_block_locations->records : null;
                        #$row->sub_block_locations 	= false;

                        $sub_block_asssets			= $this->asset_service->get_assets($account_id, false, false, ['sub_block_id'=>$row->sub_block_id], false, $limit = -1);
                        $row->sub_block_asssets 	= !empty($sub_block_asssets) ? $sub_block_asssets : null;

                        $data[$k] 					= $row;
                    }
                    $result = $data;
                }
                $this->session->set_flashdata('message', 'Building Sub Blocks data found.');
            } else {
                $this->session->set_flashdata('message', 'Building Sub Blocks data not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }

        return $result;
    }


    /**
    /* Delete Site location resource
    */
    public function delete_site_location($account_id = false, $site_id = false, $location_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($site_id) && !empty($location_id)) {
            $conditions 	= [ 'account_id'=>$account_id,'location_id'=>$location_id ];
            $record_exists 	= $this->db->get_where('site_locations', $conditions)->row();

            if (!empty($record_exists)) {
                ## Drop preexisting links to this location
                $this->db->where($conditions)->update('job', [ 'location_id'=>null ]);
                $this->db->where($conditions)->update('asset', [ 'location_id'=>null ]);
                $this->db->where($conditions)->delete('schedules');

                if ($this->db->trans_status() !== false) {
                    $this->ssid_common->_reset_auto_increment('schedules', 'schedule_id');
                }

                ## Then the parent
                $this->db->where('location_id', $location_id)
                    ->delete('site_locations');

                if ($this->db->trans_status() !== false) {
                    $this->ssid_common->_reset_auto_increment('site_locations', 'location_id');
                    $this->session->set_flashdata('message', 'Building location removed successfully.');
                    $result = true;
                }
            } else {
                $this->session->set_flashdata('message', 'Invalid Location ID.');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing the required information.');
        }
        return $result;
    }



    /**
    /* Delete Sub Block resource
    */
    public function delete_site_sub_block($account_id = false, $site_id = false, $sub_block_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($site_id) && !empty($sub_block_id)) {
            $conditions 	= [ 'account_id'=>$account_id,'sub_block_id'=>$sub_block_id ];
            $record_exists 	= $this->db->get_where('site_sub_blocks', $conditions)->row();

            if (!empty($record_exists)) {
                ## Drop preexisting links to this sub_block
                $this->db->where($conditions)->update('site_zones', [ 'sub_block_id'=>null ]);

                ## Then the parent
                $this->db->where('sub_block_id', $sub_block_id)
                    ->delete('site_sub_blocks');

                if ($this->db->trans_status() !== false) {
                    $this->ssid_common->_reset_auto_increment('site_sub_blocks', 'sub_block_id');
                    $this->session->set_flashdata('message', 'Sub Block removed successfully.');
                    $result = true;
                }
            } else {
                $this->session->set_flashdata('message', 'Invalid Sub Block ID.');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing the required information.');
        }
        return $result;
    }


        /*
    * Search through non compliant buildings
    */
    public function get_non_compliant_buildings($account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            $where = $raw_where = convert_to_array($where);

            if (isset($where['system_id'])) {
                if (!empty($where['system_id'])) {
                    $sites_installed_on = $this->get_installed_systems($account_id, false, ['system_id'=>$where[ 'system_id'], 'detailed'=> 1 ]);
                    $site_ids = !empty($sites_installed_on) ? array_column($sites_installed_on, 'site_id') : false;
                    if (!empty($site_ids)) {
                        $this->db->where_in('site.site_id', $site_ids);
                    }
                }
                unset($where['system_id']);
            }

            if (isset($where['range_index'])) {
                if (!empty($where['range_index'])) {
                    switch($where['range_index']) {
                        ## 0-3 Months overdue
                        case 1:
                            $group_min = 0;
                            $group_max = 90;
                            $this->db->where('( ( DATEDIFF( CURDATE(), DATE_FORMAT( site.audit_result_timestamp, "%Y-%m-%d" ) ) >= '.$group_min.' ) AND ( DATEDIFF( CURDATE(), DATE_FORMAT( site.audit_result_timestamp, "%Y-%m-%d" ) ) < '.$group_max.' ) )');

                            break;

                            ## 3-6 Months overdue
                        case 2:
                            $group_min = 90;
                            $group_max = 180;
                            $this->db->where('( ( DATEDIFF( CURDATE(), DATE_FORMAT( site.audit_result_timestamp, "%Y-%m-%d" ) ) >= '.$group_min.' ) AND ( DATEDIFF( CURDATE(), DATE_FORMAT( site.audit_result_timestamp, "%Y-%m-%d" ) ) < '.$group_max.' ) )');

                            break;

                            ## 6+ Months overdue
                        case 3:
                            $group_min = 180;
                            $group_max = 365;
                            $this->db->where('( ( DATEDIFF( CURDATE(), DATE_FORMAT( site.audit_result_timestamp, "%Y-%m-%d" ) ) >= '.$group_min.' ) AND ( DATEDIFF( CURDATE(), DATE_FORMAT( site.audit_result_timestamp, "%Y-%m-%d" ) ) < '.$group_max.' ) )');
                            break;
                    }
                }
                unset($where['range_index']);
            }

            $this->db->select('site.*, site_statuses.status_name, site_event_statuses.event_tracking_status_id, site_event_statuses.event_tracking_status, site_event_statuses.status_group, site_event_statuses.hex_color, site_event_statuses.icon_class, addrs.main_address_id,addrs.addressline1 `address_line_1`,addrs.addressline2 `address_line_2`,addrs.postcode `postcode`,addrs.summaryline, addrs.xcoords `gps_latitude`,addrs.ycoords `gps_longitude`, audit_result_statuses.*', false)
                ->join('addresses addrs', 'addrs.main_address_id = site.site_address_id', 'left')
                ->join('site_statuses', 'site_statuses.status_id = site.status_id', 'left')
                ->join('site_event_statuses', 'site_event_statuses.event_tracking_status_id = site.event_tracking_status_id', 'left')
                ->join('audit_result_statuses', 'audit_result_statuses.audit_result_status_id = site.audit_result_status_id', 'left')
                ->where('site.account_id', $account_id)
                ->where('site.archived !=', 1)
                ->where_in('audit_result_statuses.result_status_group', ['failed']);

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

                        if (!empty($search_where['site.status_id'])) {
                            $search_where['site_statuses.status_name'] =   trim($term);
                            unset($search_where['site.status_id']);
                        }

                        if (!empty($search_where['site.site_address_id'])) {
                            $search_where['addrs.summaryline'] =   trim($term);
                            unset($search_where['site.site_address_id']);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->searchable_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    if (!empty($search_where['site.status_id'])) {
                        $search_where['site_statuses.status_name'] =  $search_term;
                        unset($search_where['site.status_id']);
                    }

                    if (!empty($search_where['site.site_address_id'])) {
                        $search_where['addrs.summaryline'] =  $search_term;
                        unset($search_where['site.site_address_id']);
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                if (isset($where['site_statuses'])) {
                    $where['site_statuses'] = (is_array($where['site_statuses'])) ? $where['site_statuses'] : (is_string($where['site_statuses']) ? str_to_array($where['site_statuses']) : $where['site_statuses']);
                    if (!empty($where['site_statuses'])) {
                        $this->db->where_in('site.status_id', $where['site_statuses']);
                    }
                    unset($where['site_statuses']);
                }

                if ($where) {
                    $this->db->where($where);
                }
            }

            if ($order_by) {
                $order = $this->ssid_common->_clean_order_by($order_by, 'site');
                if (!empty($order)) {
                    $this->db->order_by($order);
                }
            } else {
                $this->db->order_by('site.site_name');
            }

            $query = $this->db->limit($limit, $offset)
                ->get('site');

            if ($query->num_rows() > 0) {
                $data = [];
                foreach ($query->result() as $col => $site) {
                    $installed_systems 		 = $this->get_installed_systems($account_id, $site->site_id, ['detailed'=>1]);
                    $site->installed_systems = (!empty($installed_systems)) ? implode(" | ", array_column($installed_systems, 'system_name')) : '';
                    $data[$col] = $site;
                }

                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $result->records 			= $data;
                $counters 					= $this->total_non_compliant_buildings($account_id, $search_term, $raw_where, $limit);
                $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
                $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
                $result->counters->limit  	= ( int ) $limit;
                $result->counters->offset 	= ( int ) $offset;

                $this->session->set_flashdata('message', 'Records found.');
            } else {
                $this->session->set_flashdata('message', 'No records found matching your criteria.');
            }
        }

        return $result;
    }

    /*
    * Get total site count for the search
    */
    public function total_non_compliant_buildings($account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            $where = convert_to_array($where);

            if (isset($where['system_id'])) {
                if (!empty($where['system_id'])) {
                }
                unset($where['system_id']);
            }

            if (isset($where['range_index'])) {
                if (!empty($where['range_index'])) {
                    switch($where['range_index']) {
                        ## 0-3 Months overdue
                        case 1:
                            $group_min = 0;
                            $group_max = 90;
                            $this->db->where('( ( DATEDIFF( CURDATE(), DATE_FORMAT( site.audit_result_timestamp, "%Y-%m-%d" ) ) > '.$group_min.' ) AND ( DATEDIFF( CURDATE(), DATE_FORMAT( site.audit_result_timestamp, "%Y-%m-%d" ) ) <= '.$group_max.' ) )');

                            break;

                            ## 3-6 Months overdue
                        case 2:
                            $group_min = 90;
                            $group_max = 180;
                            $this->db->where('( ( DATEDIFF( CURDATE(), DATE_FORMAT( site.audit_result_timestamp, "%Y-%m-%d" ) ) > '.$group_min.' ) AND ( DATEDIFF( CURDATE(), DATE_FORMAT( site.audit_result_timestamp, "%Y-%m-%d" ) ) <= '.$group_max.' ) )');

                            break;

                            ## 6+ Months overdue
                        case 3:
                            $group_min = 180;
                            $group_max = 365;
                            $this->db->where('( ( DATEDIFF( CURDATE(), DATE_FORMAT( site.audit_result_timestamp, "%Y-%m-%d" ) ) > '.$group_min.' ) AND ( DATEDIFF( CURDATE(), DATE_FORMAT( site.audit_result_timestamp, "%Y-%m-%d" ) ) <= '.$group_max.' ) )');
                            break;
                    }
                }
                unset($where['range_index']);
            }


            $this->db->select('site.id', false)
                ->join('addresses addrs', 'addrs.main_address_id = site.site_address_id', 'left')
                ->join('site_statuses', 'site_statuses.status_id = site.status_id', 'left')
                ->join('site_event_statuses', 'site_event_statuses.event_tracking_status_id = site.event_tracking_status_id', 'left')
                ->join('audit_result_statuses', 'audit_result_statuses.audit_result_status_id = site.audit_result_status_id', 'left')
                ->where('site.account_id', $account_id)
                ->where('site.archived !=', 1)
                ->where_in('audit_result_statuses.result_status_group', ['failed']);

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

                        if (!empty($search_where['site.status_id'])) {
                            $search_where['site_statuses.status_name'] =   trim($term);
                            unset($search_where['site.status_id']);
                        }

                        if (!empty($search_where['site.site_address_id'])) {
                            $search_where['addrs.summaryline'] =   trim($term);
                            unset($search_where['site.site_address_id']);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->searchable_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    if (!empty($search_where['site.status_id'])) {
                        $search_where['site_statuses.status_name'] =  $search_term;
                        unset($search_where['site.status_id']);
                    }

                    if (!empty($search_where['site.site_address_id'])) {
                        $search_where['addrs.summaryline'] =  $search_term;
                        unset($search_where['site.site_address_id']);
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            //Check for a setting that specifies whether or not to only get monitored sites

            if (!empty($where)) {
                if (isset($where['site_statuses'])) {
                    $where['site_statuses'] = (is_array($where['site_statuses'])) ? $where['site_statuses'] : (is_string($where['site_statuses']) ? str_to_array($where['site_statuses']) : $where['site_statuses']);
                    if (!empty($where['site_statuses'])) {
                        $this->db->where_in('site.status_id', $where['site_statuses']);
                    }
                    unset($where['site_statuses']);
                }

                if ($where) {
                    $this->db->where($where);
                }
            }

            if ($order_by) {
                $order = $this->ssid_common->_clean_order_by($order_by, 'site');
                if (!empty($order)) {
                    $this->db->order_by($order);
                }
            } else {
                $this->db->order_by('site.site_name');
            }

            $query = $this->db->from('site')->count_all_results();
            $results['total'] = !empty($query) ? $query : 0;
            $limit 			  = (!empty($limit > 0)) ? $limit : $results['total'];
            $results['pages'] = !empty($query) ? ceil($query / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }


    /* Associate Buildings to a User */
    public function associate_buildings($account_id = false, $user_id = false, $buildings_data = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($user_id) && !empty($buildings_data)) {
            $buildings_data 	= convert_to_array($buildings_data);
            $site_ids			= !empty($buildings_data['associated_buildings']) ? $buildings_data['associated_buildings'] : false;
            $site_ids			= (is_json($site_ids)) ? json_decode($site_ids) : $site_ids;

            if (!empty($site_ids)) {
                $site_ids 	= array_diff($site_ids, [ $user_id ]);
                foreach ($site_ids as $site_id) {
                    $condition = $data = [
                        'user_id'		=> $user_id,
                        'site_id'		=> $site_id,
                        'account_id'	=> $account_id
                    ];

                    $check_exists = $this->db->get_where('user_associated_buildings', $data)->row();
                    if (!empty($check_exists)) {
                        $data['last_modified_by'] = $this->ion_auth->_current_user->id;
                        $this->db->where('user_associated_buildings.id', $check_exists->id)
                            ->update('user_associated_buildings', $data);
                    } else {
                        $data['linked_by'] = $this->ion_auth->_current_user->id;
                        $this->db->insert('user_associated_buildings', $data);
                    }
                }

                if ($this->db->affected_rows() > 0 || ($this->db->trans_status() !== false)) {
                    $result = $this->get_user_associated_buildings($account_id, $user_id);
                    $this->session->set_flashdata('message', 'Buildings associated successfully.');
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
    * Disassociate Buildings
    */
    public function disassociate_buildings($account_id = false, $user_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($user_id) && !empty($postdata)) {
            $postdata 					= convert_to_array($postdata);
            $user_associated_buildings	= !empty($postdata['associated_buildings']) ? $postdata['associated_buildings'] : false;
            $user_associated_buildings	= (is_json($user_associated_buildings)) ? json_decode($user_associated_buildings) : $user_associated_buildings;
            $deleted					= [];

            if (!empty($user_associated_buildings)) {
                foreach ($user_associated_buildings as $k => $val) {
                    $data = [
                        'user_id'	=> $user_id,
                        'site_id'	=> $val
                    ];

                    $check_exists = $this->db->limit(1)->get_where('user_associated_buildings', $data)->row();
                    if (!empty($check_exists)) {
                        $this->db->where($data);
                        $this->db->delete('user_associated_buildings');
                        $this->ssid_common->_reset_auto_increment('user_associated_buildings', 'id');
                    }
                    $deleted[] = $data;
                }
            } elseif (!empty($postdata['site_id'])) {
                $data = [
                    'user_id'	=> $user_id,
                    'site_id'	=> $postdata['site_id']
                ];

                $check_exists = $this->db->limit(1)->get_where('user_associated_buildings', $data)->row();
                if (!empty($check_exists)) {
                    $this->db->where($data);
                    $this->db->delete('user_associated_buildings');
                    $deleted[] = $data;
                    $this->ssid_common->_reset_auto_increment('user_associated_buildings', 'id');
                }
            }

            if (!empty($deleted)) {
                $result = $deleted;
                $this->session->set_flashdata('message', 'Buildings disassociated successfully');
            } else {
                $this->session->set_flashdata('message', 'No Buildings were disassociated');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }
        return $result;
    }


    /*
    * 	Get a list of all Associated Buildings by User ID
    */
    public function get_user_associated_buildings($account_id = false, $user_id = false, $site_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = 0)
    {
        $result = null;
        if (!empty($account_id)) {
            $where		 	= convert_to_array($where);
            $user_id 		= !empty($user_id) ? $user_id : (!empty($where['user_id']) ? $where['user_id'] : false);
            $site_id 		= !empty($site_id) ? $site_id : (!empty($where['site_id']) ? $where['site_id'] : false);
            $as_arraay		= (!empty($where['as_arraay'])) ? true : false;

            if (!empty($user_id)) {
                $this->db->where('uab.user_id', $user_id);
            }

            if (!empty($site_id)) {
                $this->db->select('site.site_name, site.site_postcodes, uab.id, uab.site_id, uab.user_id, uab.date_linked, concat(creator.first_name," ",creator.last_name) `linked_by`, concat(modifier.first_name," ",modifier.last_name) `last_modified_by`, concat(pu.first_name," ",pu.last_name) `buildings_user`', false)
                    ->join('site', 'site.site_id = uab.site_id', 'left')
                    ->join('user pu', 'pu.id = uab.user_id', 'left')
                    ->join('user creator', 'creator.id = uab.linked_by', 'left')
                    ->join('user modifier', 'modifier.id = uab.last_modified_by', 'left')
                    ->where('uab.site_id', $site_id)
                    ->where('site.archived !=', 1)
                    ->where('site.account_id', $account_id)
                    ->group_by('uab.user_id');
            } else {
                $this->db->select('site.site_name, site.site_postcodes, uab.id, uab.site_id, uab.user_id, uab.date_linked, concat(creator.first_name," ",creator.last_name) `linked_by`, concat(modifier.first_name," ",modifier.last_name) `last_modified_by`, concat(pu.first_name," ",pu.last_name) `buildings_user`', false)
                    ->join('site', 'site.site_id = uab.site_id', 'left')
                    ->join('user pu', 'pu.id = uab.user_id', 'left')
                    ->join('user creator', 'creator.id = uab.linked_by', 'left')
                    ->join('user modifier', 'modifier.id = uab.last_modified_by', 'left')
                    ->where('site.archived !=', 1)
                    ->where('site.account_id', $account_id);
            }

            $query = $this->db->get('user_associated_buildings `uab`');

            if ($query->num_rows() > 0) {
                $this->session->set_flashdata('message', 'Associated buildings data found.');
                $result = !empty($as_arraay) ? $query->result() : $query->result_array();
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }
        return $result;
    }


    /*
    * Search through Sites
    */
    public function site_search($account_id = false, $site_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            #Limit access by contract to External User Types
            if (in_array($this->ion_auth->_current_user()->user_type_id, EXTERNAL_USER_TYPES)) {
                $contract_access 	= $this->contract_service->get_linked_people($account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1]);
                $allowed_contracts  = !empty($contract_access) ? array_column($contract_access, 'contract_id') : [];
                if (!empty($allowed_contracts)) {
                    $contract_access 	= $this->contract_service->get_linked_sites($account_id, $allowed_contracts, [ 'ids_only'=>1, 'ignore_schedule_check'=> 1 ], -1);
                    $allowed_sites  = !empty($contract_access) ? $contract_access : [];
                    $this->db->where_in('site.site_id', $allowed_sites);
                } else {
                    $this->session->set_flashdata('message', 'No data found matching your criteria');
                    return false;
                }
            }

            $where = $raw_where = convert_to_array($where);

            if (isset($where['system_id'])) {
                if (!empty($where['system_id'])) {
                    $sites_installed_on = $this->get_installed_systems($account_id, false, ['system_id'=>$where[ 'system_id'], 'detailed'=> 1 ]);
                    $site_ids = !empty($sites_installed_on) ? array_column($sites_installed_on, 'site_id') : false;
                    if (!empty($site_ids)) {
                        $this->db->where_in('site.site_id', $site_ids);
                    }
                }
                unset($where['system_id']);
            }

            $this->db->select('site.*, site_statuses.status_name, site_event_statuses.event_tracking_status_id, site_event_statuses.event_tracking_status, site_event_statuses.status_group, site_event_statuses.hex_color, site_event_statuses.icon_class, addrs.main_address_id,addrs.addressline1 `address_line_1`,addrs.addressline2 `address_line_2`,addrs.postcode `postcode`,addrs.summaryline, addrs.xcoords `gps_latitude`,addrs.ycoords `gps_longitude`, audit_result_statuses.*', false)
                ->join('addresses addrs', 'addrs.main_address_id = site.site_address_id', 'left')
                ->join('site_statuses', 'site_statuses.status_id = site.status_id', 'left')
                ->join('site_event_statuses', 'site_event_statuses.event_tracking_status_id = site.event_tracking_status_id', 'left')
                ->join('audit_result_statuses', 'audit_result_statuses.audit_result_status_id = site.audit_result_status_id', 'left')
                ->where('site.account_id', $account_id)
                ->where('site.archived !=', 1);

            if (!empty($site_id)) {
                $row = $this->db->get_where('site', ['site_id'=>$site_id])->row();

                if (!empty($row)) {
                    $this->session->set_flashdata('message', 'Site record found');
                    $result = $row;
                } else {
                    $this->session->set_flashdata('message', 'Site record not found');
                }
                return $result;
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

            if (isset($where['contract_id'])) {
                if (!empty($where['contract_id'])) {
                    $this->db->join('sites_contracts', 'sites_contracts.site_id = site.site_id', 'left');
                    $this->db->where('sites_contracts.contract_id', $where['contract_id']);
                }
                unset($where['contract_id']);
            }

            if (isset($where['site_postcodes'])) {
                if (!empty($where['site_postcodes'])) {
                    $postcode = urldecode($where['site_postcodes']);
                    $this->db->where('( site.site_postcodes = "'.trim($postcode).'" OR site.site_postcodes = "'.strip_all_whitespace(trim($postcode)).'" )');
                }
                unset($where['site_postcodes']);
            }

            if ($where) {
                $this->db->where($where);
            }

            if ($order_by) {
                $order = $this->ssid_common->_clean_order_by($order_by, 'site');
                if (!empty($order)) {
                    $this->db->order_by($order);
                }
            } else {
                $this->db->order_by('site.site_name');
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->get('site');

            if ($query->num_rows() > 0) {
                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $counters 					= $this->get_site_search_totals($account_id, $search_term, $raw_where, $limit);
                $result->records 			= $query->result();
                $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
                $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
                $result->counters->limit  	= ( int ) $limit;
                $result->counters->offset 	= ( int ) $offset;
                $this->session->set_flashdata('message', 'Site records found.');
            } else {
                $this->session->set_flashdata('message', 'No records found matching your criteria.');
            }
        }

        return $result;
    }


    /*
    * Get total site count for the search
    */
    public function get_site_search_totals($account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;

        if (!empty($account_id)) {
            #Limit access by contract to External User Types
            if (in_array($this->ion_auth->_current_user()->user_type_id, EXTERNAL_USER_TYPES)) {
                $contract_access 	= $this->contract_service->get_linked_people($account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1]);
                $allowed_contracts  = !empty($contract_access) ? array_column($contract_access, 'contract_id') : [];
                if (!empty($allowed_contracts)) {
                    $contract_access 	= $this->contract_service->get_linked_sites($account_id, $allowed_contracts, [ 'ids_only'=>1, 'ignore_schedule_check'=> 1 ], -1);
                    $allowed_sites  = !empty($contract_access) ? $contract_access : [];
                    $this->db->where_in('site.site_id', $allowed_sites);
                } else {
                    $this->session->set_flashdata('message', 'No data found matching your criteria');
                    return false;
                }
            }

            $where = convert_to_array($where);

            $this->db->select('site.site_id', false)
                ->where('site.account_id', $account_id);

            if (!empty($search_term)) {
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

            if (isset($where['contract_id'])) {
                if (!empty($where['contract_id'])) {
                    $this->db->join('sites_contracts', 'sites_contracts.site_id = site.site_id', 'left');
                    $this->db->where('sites_contracts.contract_id', $where['contract_id']);
                }
                unset($where['contract_id']);
            }

            if (isset($where['site_postcodes'])) {
                if (!empty($where['site_postcodes'])) {
                    $this->db->where('( site.site_postcodes = "'.trim($where['site_postcodes']).'" OR site.site_postcodes = "'.strip_all_whitespace(trim($where['site_postcodes'])).'" )');
                }
                unset($where['site_postcodes']);
            }

            if ($where) {
                $this->db->where($where);
            }

            $query = $this->db->get('site');

            $results['total'] = !empty($query->num_rows()) ? $query->num_rows() : 0;
            $limit 			  = (!empty($limit > 0)) ? $limit : $results['total'];
            $results['pages'] = !empty($query->num_rows()) ? ceil($query->num_rows() / $limit) : 0;

            return json_decode(json_encode($results));
        }

        return $result;
    }

    /**
    * Clone an existing Site
    **/
    public function clone_site($account_id = false, $site_id = false, $data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($site_id)) {
            $site_exists 		= $this->db->get_where('site', [ 'account_id'=>$account_id, 'site_id'=>$site_id ])->row();

            if (!empty($site_exists)) {
                $data			= convert_to_array($data);
                $data			= array_map('trim', $data);
                $cloned_data	= array_merge((array) $site_exists, $data);
                unset($cloned_data['site_id'], $cloned_data['site_reference'], $cloned_data['date_created'], $cloned_data['last_modified'], $cloned_data['last_modified_by'], $cloned_data['approx_max_residents'], $cloned_data['number_of_floors'], $cloned_data['number_of_flats']);
                $cloned_data['is_cloned'] 		= 1;
                $cloned_data['cloned_site_id'] 	= $site_id;

                ## Create New Site
                $check_conflict = $this->db->select('site.site_id', false)
                    ->where('site.account_id', $account_id)
                    ->where('site_name', $cloned_data['site_name'])
                    ->where('site_address_id', $cloned_data['site_address_id'])
                    ->limit(1)
                    ->get('site')
                    ->row();

                if (!empty($check_conflict)) {
                    $this->session->set_flashdata('message', 'The new Site Name already exists. Please change the Site Name and try again.');
                    return false;
                } else {
                    $cloned_data['created_by'] 	= $this->ion_auth->_current_user->id;
                    $new_site_data 				= $this->ssid_common->_filter_data('site', $cloned_data);
                    $this->db->insert('site', $new_site_data);
                    $new_site_id 				= $this->db->insert_id();
                    $new_site_data['site_id']	= $new_site_id;
                    $this->session->set_flashdata('message', 'Site cloned successfully.');
                    $result = $new_site_data;
                }
            } else {
                $this->session->set_flashdata('message', 'This Site record does not exist or does not belong to you.');
                return false;
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }
        return $result;
    }

    /** Generate Site Ref **/
    private function generate_site_ref($account_id = false, $data = false)
    {
        if (!empty($account_id) && !empty($data)) {
            $site_ref = $account_id;
            $site_ref .= (!empty($data['site_name'])) ? lean_string($data['site_name']) : '';
            $site_ref .= (!empty($data['contract_id'])) ? $data['contract_id'] : '';
        } else {
            $site_ref = $account_id.$this->ssid_common->generate_random_password();
        }
        return strtoupper($site_ref);
    }
}
