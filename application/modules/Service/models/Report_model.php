<?php

namespace Application\Modules\Service\Models;

class Report_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $section 	   = explode("/", $_SERVER["SCRIPT_NAME"]);
        $this->app_root= $_SERVER["DOCUMENT_ROOT"]."/".$section[1]."/";
        $this->app_root= str_replace('/index.php', '', $this->app_root);
        $this->load->library('upload');
    }

    private $exempt_columns 	= [ 'account_id', 'connected_devices', 'archived', 'is_active', 'event_tracking_status_id', 'external_asset_ref', 'external_asset_created_on', 'external_asset_updated_on', 'status_name', 'asset_status', 'status_id', 'audit_result_status_id' ];
    private $fixed_reports 		= [ 'job_invoice_items', 'job_invoice_report' ];

    /*
    *	Get Report records by account ID
    */
    public function get_reports($account_id = false, $report_type = false, $postdata = false, $limit=30, $offset=0)
    {
        $result = false;

        if (!empty($account_id) && !empty($report_type) && !empty($postdata)) {
            $report_type = strtolower($report_type);

            if (in_array($report_type, ['fleet','vehicle'])) {
                $report_type = 'fleet_vehicle';
            }

            $postdata   = (!is_array($postdata)) ? json_decode($postdata) : $postdata;
            $postdata 	= (is_object($postdata)) ? object_to_array($postdata) : $postdata;

            $contract_id	= (!empty($postdata['contract_id'])) ? $postdata['contract_id'] : false;
            $evidoc_group	= (!empty($postdata['evidoc_group'])) ? $postdata['evidoc_group'] : false;

            $report_data= (!empty($postdata['report'])) ? (is_array($postdata['report']) ? $postdata['report'] : json_decode($postdata['report'], true)) : [];

            $dates 		= (!empty($report_data[$report_type]['dates'])) ? (is_array($report_data[$report_type]['dates']) ? $report_data[$report_type]['dates'] : json_decode($report_data[$report_type]['dates'], true)) : [];

            $statuses 	= (!empty($report_data[$report_type]['statuses'])) ? ((is_array($report_data[$report_type]['statuses'])) ? $report_data[$report_type]['statuses'] : json_decode(urldecode($report_data[$report_type]['statuses']), true)) : [];

            $select   	= (!empty($postdata['report'])) ? (is_array($postdata['report']) ? $postdata['report'] : json_decode($postdata['report'], true)) : $report_type.'*';

            $req_source	= (!empty($postdata['request_source'])) ? $postdata['request_source'] : null;

            $default_cols 	= (in_array($report_type, $this->fixed_reports)) ? implode(', ', array_keys($this->_get_custom_columns($report_type))) : implode(', ', array_keys($this->_get_table_columns($report_type)));

            $columns 		= (!empty($select[$report_type]['columns']) && is_array($select[$report_type]['columns'])) ? implode(', ', $select[$report_type]['columns']) : ((!empty($select[$report_type]['columns']) && is_string($select[$report_type]['columns'])) ? str_replace('"', '', $select[$report_type]['columns']) : $default_cols);

            $date_from 	= !empty($dates['job_date']['date_from']) ? date('Y-m-d', strtotime($dates['job_date']['date_from'])) : false;
            $date_to	= !empty($dates['job_date']['date_to']) ? date('Y-m-d', strtotime($dates['job_date']['date_to'])) : $date_from;

            switch($report_type) {
                case 'asset':

                    $table  = 'asset';

                    if (strpos($columns, 'asset.asset_type_id') !== false) {
                        $columns = str_replace('asset.asset_type_id,', '', $columns);
                        $columns .= ', asset_types.asset_type as `asset_type`';
                        $columns .= ', audit_categories.category_name as `category_name`';
                    }

                    if (strpos($columns, 'asset.created_by') !== false) {
                        $columns = str_replace('asset.created_by,', '', $columns);
                        $columns .= ', concat(user.first_name," ",user.last_name) as `created_by`';
                    }

                    if (strpos($columns, 'asset.status_id') !== false) {
                        $columns = str_replace('asset.status_id,', '', $columns);
                        $columns .= ',asset_statuses.status_name as `asset_status`';
                    }

                    if (strpos($columns, 'asset.audit_result_status_id') !== false) {
                        $columns = str_replace('asset.audit_result_status_id,', '', $columns);
                        $columns .= ',audit_result_statuses.result_status_alt as `compliance_status`';
                    }

                    if (strpos($columns, 'asset.site_id') !== false) {
                        $this->db->join('site', 'site.site_id = asset.site_id', 'left');
                        $columns = str_replace('asset.site_id,', '', $columns);
                        $columns .= ',  site.site_id';
                        $columns .= ',  site.site_name as `site_name`';
                        $columns .= ',  site.site_postcodes as `site_postcode`';
                    }

                    if (strpos($columns, 'asset.sub_block_id') !== false) {
                        $this->db->join('site_sub_blocks', 'site_sub_blocks.sub_block_id = asset.sub_block_id', 'left');
                        $columns = str_replace('asset.sub_block_id,', '', $columns);
                        $columns .= ',  site_sub_blocks.sub_block_name as `sub_block_name`';
                    }

                    if (strpos($columns, 'asset.zone_id') !== false) {
                        $this->db->join('site_zones', 'site_zones.zone_id = asset.zone_id', 'left');
                        $columns = str_replace('asset.zone_id,', '', $columns);
                        $columns .= ',  site_zones.zone_name as `zone_name`';
                    }

                    if (strpos($columns, 'asset.location_id') !== false) {
                        $this->db->join('site_locations', 'site_locations.location_id = asset.location_id', 'left');
                        $columns = str_replace('asset.location_id,', '', $columns);
                        $columns .= ',  site_locations.location_name as `location_name`';
                    }

                    $this->db->select($columns, false);

                    $this->db->join('audit_result_statuses', 'asset.audit_result_status_id = audit_result_statuses.audit_result_status_id', 'left');
                    $this->db->join('asset_statuses', 'asset.status_id = asset_statuses.status_id', 'left');
                    $this->db->join('asset_types', 'asset_types.asset_type_id = asset. asset_type_id', 'left');
                    $this->db->join('audit_categories', 'audit_categories.category_id = asset_types.category_id', 'left');
                    $this->db->join('user', 'user.id = asset.created_by', 'left');

                    $this->db->where('asset.account_id', $account_id);
                    $this->db->where('asset.archived !=', 1);
                    $this->db->order_by('asset.asset_id');
                    $report_name = 'Asset Details';

                    break;

                case 'site':

                    $table  = 'site';

                    if (strpos($columns, 'site.site_address_id') !== false) {
                        $columns = str_replace('site.site_address_id,', '', $columns);
                        $columns .= ', addresses.summaryline as `site_address`';
                    }

                    if (strpos($columns, 'site.created_by') !== false) {
                        $columns = str_replace('site.created_by,', '', $columns);
                        $columns .= ', concat(user.first_name," ",user.last_name) as `created_by`';
                    }

                    if (strpos($columns, 'site.last_modified_by') !== false) {
                        $columns = str_replace('site.last_modified_by,', '', $columns);
                        $columns .= ', concat(user2.first_name," ",user2.last_name) as `last_modified_by`';
                    }

                    if (strpos($columns, 'site.audit_result_status_id') !== false) {
                        $columns = str_replace('site.audit_result_status_id,', '', $columns);
                        $columns .= ', audit_result_statuses.result_status_alt as `compliance_status`';
                    }

                    $this->db->select($columns, false);

                    $this->db->join('audit_result_statuses', 'site.audit_result_status_id = audit_result_statuses.audit_result_status_id', 'left');
                    $this->db->join('addresses', 'addresses.main_address_id = site.site_address_id', 'left');
                    $this->db->join('user', 'user.id = site.created_by', 'left');
                    $this->db->join('user user2', 'user2.id = site.last_modified_by', 'left');
                    $this->db->join('site_statuses', 'site_statuses.status_id = site.status_id', 'left');

                    if (!empty($statuses) && !in_array('all', array_map('strtolower', $statuses))) {
                        //$this->db->where_in( 'site_status',$statuses );
                    }

                    $this->db->where('site.account_id', $account_id);
                    $this->db->where('site.archived !=', 1);
                    $this->db->order_by('site.site_name');
                    $report_name = 'Buildings Details Report';
                    break;

                case 'audit':

                    $table  = 'audit';

                    if (strpos($columns, 'audit.audit_type_id') !== false) {
                        $columns = str_replace('audit.audit_type_id,', '', $columns);
                        $columns .= ', audit_types.audit_type';
                    }

                    if (strpos($columns, 'audit.audit_result_status_id') !== false) {
                        $columns = str_replace('audit.audit_result_status_id,', '', $columns);
                        $columns .= ', audit_result_statuses.result_status';
                    }

                    if (strpos($columns, 'audit.created_by') !== false) {
                        $columns = str_replace('audit.created_by,', '', $columns);
                        $columns .= ', concat(user.first_name," ",user.last_name) as `created_by`';
                    }

                    if (strpos($columns, 'audit.last_modified_by') !== false) {
                        $columns = str_replace('audit.last_modified_by,', '', $columns);
                        $columns .= ', concat(user2.first_name," ",user2.last_name) as `last_modified_by`';
                    }

                    $this->db->select($columns, false);

                    $this->db->join('audit_types', 'audit.audit_type_id = audit_types.audit_type_id', 'left');
                    $this->db->join('user', 'user.id = audit.created_by', 'left');
                    $this->db->join('user user2', 'user2.id = audit.last_modified_by', 'left');
                    $this->db->join('audit_result_statuses', 'audit_result_statuses.audit_result_status_id = audit.audit_result_status_id', 'left');

                    $this->db->where('audit.account_id', $account_id);
                    $this->db->where('( audit.archived != 1 OR audit.archived IS NULL )');
                    $this->db->order_by('audit_types.audit_type_id asc, audit.audit_id');
                    $report_name = 'Audit Overviews Report';

                    break;

                case 'audit_responses':

                    $report_name = !empty($evidoc_group) ? ucwords($evidoc_group).' Evidoc Responses' : 'Evidoc Responses';

                    switch($evidoc_group) {
                        case 'people':
                            $table  = 'audit_responses_people';
                            break;
                        case 'asset':
                        case 'assets':
                            $table  = 'audit_responses_assets';
                            break;
                        case 'job':
                            $table  = 'audit_responses_job';
                            break;
                        case 'customer':
                        case 'customers':
                            $table  = 'audit_responses_customer';
                            break;
                        case 'site':
                        case 'sites':
                        case 'building':
                        case 'buildings':
                            $table  = 'audit_responses_sites';
                            break;
                        case 'fleet':
                        case 'vehicle':
                        case 'vehicles':
                            $table  = 'audit_responses_fleet';
                            break;
                        default:
                            $table  = 'audit_responses';
                            break;
                    }

                    if (strpos($columns, 'audit_responses.') !== false) {
                        $columns = str_replace('audit_responses.', $table.'.', $columns);
                    }

                    if (strpos($columns, $table.'.id') !== false) {
                        $columns = str_replace($table.'.id,', '', $columns);
                        $columns = 'audit_types.audit_type `evidoc_type`,'.$columns;
                    }

                    if (strpos($columns, $table.'.created_by') !== false) {
                        $columns = str_replace($table.'.created_by,', '', $columns);
                        $columns .= ', concat(user.first_name," ",user.last_name) as `created_by`';
                    }

                    $this->db->select($columns, false);

                    $this->db->join('audit', 'audit.audit_id = '.$table.'.audit_id', 'left');
                    $this->db->join('audit_types', 'audit.audit_type_id = audit_types. audit_type_id', 'left');
                    $this->db->join('user', 'user.id = '.$table.'.created_by', 'left');

                    $this->db->where('audit.account_id', $account_id);
                    $this->db->where('( audit.archived != 1 OR audit.archived IS NULL )');
                    $this->db->order_by($table.'.audit_id, '.$table.'.ordering');

                    break;

                case 'fleet':
                case 'vehicle':
                case 'fleet_vehicle':

                    $table  = 'fleet_vehicle';
                    $default_cols = implode(', ', array_keys($this->_get_table_columns($table)));

                    if (strpos($columns, 'fleet_vehicle.supplier_id') !== false) {
                        $columns = str_replace('fleet_vehicle.supplier_id,', '', $columns);
                        $columns .= ', fleet_vehicle_supplier.supplier_name as `supplier_name`';
                    }

                    if (strpos($columns, 'fleet_vehicle.driver_id') !== false) {
                        $columns = str_replace('fleet_vehicle.driver_id,', '', $columns);
                        $columns .= ', concat( user3.first_name," ",user3.last_name ) as `driver_full_name`';
                    }

                    if (strpos($columns, 'fleet_vehicle.veh_status_id') !== false) {
                        $columns = str_replace('fleet_vehicle.veh_status_id,', '', $columns);
                        $columns .= ', fleet_vehicle_status.status_name as `status_name`';
                    }

                    if (strpos($columns, 'fleet_vehicle.created_by') !== false) {
                        $columns = str_replace('fleet_vehicle.created_by,', '', $columns);
                        $columns .= ', concat(user.first_name," ",user.last_name) as `created_by`';
                    }

                    if (strpos($columns, 'fleet_vehicle.last_modified_by') !== false) {
                        $columns = str_replace('fleet_vehicle.last_modified_by,', '', $columns);
                        $columns .= ', concat(user2.first_name," ",user2.last_name) as `last_modified_by`';
                    }

                    if (strpos($columns, 'fleet_vehicle.vehicle_category_id') !== false) {
                        $columns = str_replace('fleet_vehicle.vehicle_category_id,', '', $columns);
                        $columns .= ", CONCAT( fleet_vehicle_category.category_name, ' - ', fleet_vehicle_category.category_symbol ) as `category_name`";
                    }

                    if (strpos($columns, 'fleet_vehicle.tracker_supplier_id') !== false) {
                        $columns = str_replace('fleet_vehicle.tracker_supplier_id,', '', $columns);
                        $columns .= ', vehicle_tracker_supplier.tracker_supplier_name as `tracker_supplier_name`';
                    }

                    if (strpos($columns, 'fleet_vehicle.is_insured') !== false) {
                        $columns = str_replace('fleet_vehicle.is_insured,', ' if ( fleet_vehicle.is_insured = 1, "yes", "no" ) as `is_insured`, ', $columns);
                    }

                    if (strpos($columns, 'fleet_vehicle.has_road_assistance') !== false) {
                        $columns = str_replace('fleet_vehicle.has_road_assistance,', ' if ( fleet_vehicle.has_road_assistance = 1, "yes", "no" ) as `has_road_assistance`, ', $columns);
                    }

                    if (strpos($columns, 'fleet_vehicle.has_camera') !== false) {
                        $columns = str_replace('fleet_vehicle.has_camera,', ' if ( fleet_vehicle.has_camera = 1, "yes", "no" ) as `has_camera`, ', $columns);
                    }

                    $table  = 'fleet_vehicle';
                    $this->db->select($columns, false);

                    $this->db->join('fleet_vehicle_supplier', 'fleet_vehicle_supplier.supplier_id = fleet_vehicle.supplier_id', 'left');
                    $this->db->join('fleet_vehicle_status', 'fleet_vehicle_status.status_id = fleet_vehicle.veh_status_id', 'left');
                    $this->db->join('user', 'user.id = fleet_vehicle.created_by', 'left');
                    $this->db->join('user user2', 'user2.id = fleet_vehicle.last_modified_by', 'left');
                    $this->db->join('user user3', 'user3.id = fleet_vehicle.driver_id', 'left');
                    $this->db->join('fleet_vehicle_category', 'fleet_vehicle_category.category_id = fleet_vehicle.vehicle_category_id', 'left');
                    $this->db->join('vehicle_tracker_supplier', 'vehicle_tracker_supplier.tracker_supplier_id = fleet_vehicle.tracker_supplier_id', 'left');

                    if (!empty($statuses) && !in_array('all', array_map('strtolower', $statuses))) {
                        $this->db->where_in('fleet_vehicle', $statuses);
                    }

                    $arch_where = "( fleet_vehicle.archived != 1 or fleet_vehicle.archived is NULL )";
                    $this->db->where('fleet_vehicle.account_id', $account_id);
                    $this->db->where($arch_where);
                    $this->db->where('fleet_vehicle.account_id', $account_id);
                    $this->db->order_by('fleet_vehicle.vehicle_make');
                    $report_name = 'Vehicle Details Report';
                    break;

                case 'people':

                    $table  = 'people';

                    if (strpos($columns, 'people.user_id') !== false) {
                        $columns = str_replace('people.user_id,', '', $columns);
                        $columns .= ', concat(user3.first_name," ",user3.last_name) as `employee_name`';
                    }

                    if (strpos($columns, 'people.department_id') !== false) {
                        $columns = str_replace('people.department_id,', '', $columns);
                        $columns .= ', people_departments.department_name as `department_name`';
                    }

                    if (strpos($columns, 'people.job_title_id') !== false) {
                        $columns = str_replace('people.job_title_id,', '', $columns);
                        $columns .= ', people_job_titles.job_title as `job_title`';
                    }

                    if (strpos($columns, 'people.created_by') !== false) {
                        $columns = str_replace('people.created_by,', '', $columns);
                        $columns .= ', concat(user.first_name," ",user.last_name) as `created_by`';
                    }

                    if (strpos($columns, 'people.last_modified_by') !== false) {
                        $columns = str_replace('people.last_modified_by,', '', $columns);
                        $columns .= ', concat(user2.first_name," ",user2.last_name) as `last_modified_by`';
                    }

                    if (strpos($columns, 'people.status_id') !== false) {
                        $this->db->join('user_statuses', 'user_statuses.status_id = people.status_id', 'left');
                        $columns = str_replace('people.status_id,', '', $columns);
                        $columns .= ',  user_statuses.status as `employee_status`';
                    }


                    $this->db->select($columns, false);

                    $this->db->join('people_departments', 'people_departments.department_id = people.department_id', 'left');
                    $this->db->join('people_job_titles', 'people_job_titles.job_title_id = people.job_title_id', 'left');
                    $this->db->join('user', 'user.id = people.created_by', 'left');
                    $this->db->join('user user2', 'user2.id = people.last_modified_by', 'left');
                    $this->db->join('user user3', 'user3.id = people.user_id', 'left');

                    if (!empty($statuses) && !in_array('all', array_map('strtolower', $statuses))) {
                        $this->db->where_in('people.status_id', $statuses);
                    }

                    $this->db->where('people.account_id', $account_id);

                    $this->db->where('people.is_active', 1);

                    $this->db->order_by('user3.first_name');
                    $report_name = 'People Details Report';
                    break;

                case 'job':

                    $table  = 'job';

                    if (strpos($columns, 'job.second_assignee_id') !== false) {
                        $columns = str_replace('job.second_assignee_id,', '', $columns);
                        $columns .= ', concat(user4.first_name," ",user4.last_name) as `second_assignee`';
                    }

                    if (strpos($columns, 'job.full_address') !== false) {
                        $columns = str_replace('job.full_address,', '', $columns);
                        $columns = str_replace('full_address,', '', $columns);
                        ## $columns .= ', concat( addresses.addressline1, ", ",addresses.addressline2, ", ", addresses.posttown, ", ", addresses.county, ", ", addresses.postcode ) as `full_address`';
                        $columns .= ', addresses.summaryline as `full_address`';
                    }

                    if (strpos($columns, 'job.contract_id') !== false) {
                        $columns = str_replace('job.contract_id,', '', $columns);
                        $columns .= ',  contract.contract_name as `contract_name`';
                    }

                    if (strpos($columns, 'job.assigned_to') !== false) {
                        $split_cols		= explode('job.assigned_to', $columns);
                        $engineer_name 	= 'concat( user3.first_name," ",user3.last_name ) as `assignee`';
                        $columns		= $split_cols[0].$engineer_name;
                        if (!empty($split_cols[1])) {
                            $columns	.= $split_cols[1];
                        }
                    }

                    if (strpos($columns, 'job.second_assignee_id') !== false) {
                        $columns = str_replace('job.second_assignee_id,', '', $columns);
                        $columns .= ', concat(user4.first_name," ",user4.last_name) as `second_assignee`';
                    }

                    if (strpos($columns, 'job.job_type_id') !== false) {
                        $columns = str_replace('job_types.job_type_id,', '', $columns);
                        $columns .= ', job_types.job_type as `job_type`';
                    }

                    if (strpos($columns, 'job.status_id') !== false) {
                        $this->db->join('job_statuses', 'job_statuses.status_id = job.status_id', 'left');
                        $split_cols		= explode('job.status_id', $columns);
                        $concat_text 	= 'job_statuses.job_status as `job_status`';
                        $columns		= $split_cols[0].$concat_text;
                        if (!empty($split_cols[1])) {
                            $columns	.= $split_cols[1];
                        }
                    }

                    if (strpos($columns, 'job.created_by') !== false) {
                        $columns = str_replace('job.created_by,', '', $columns);
                        $columns .= ', concat(user.first_name," ",user.last_name) as `created_by`';
                    }

                    if (strpos($columns, 'job.last_modified_by') !== false) {
                        $columns = str_replace('job.last_modified_by,', '', $columns);
                        $columns .= ', concat(user2.first_name," ",user2.last_name) as `last_modified_by`';
                    }

                    if (strpos($columns, 'job.job_tracking_id') !== false) {
                        $this->db->join('job_tracking_statuses', 'job_tracking_statuses.job_tracking_id = job.job_tracking_id', 'left');
                        $columns = str_replace('job.job_tracking_id,', '', $columns);
                        $columns .= ',  job_tracking_statuses.job_tracking_status as `job_tracking_status`';
                    }

                    if (strpos($columns, 'job.fail_code_id') !== false) {
                        $this->db->join('job_fail_codes', 'job_fail_codes.fail_code_id = job.fail_code_id', 'left');
                        $split_cols		= explode('job.fail_code_id', $columns);
                        $concat_text 	= 'job_fail_codes.fail_code_text as `fail_code`';
                        $columns		= $split_cols[0].$concat_text;
                        if (!empty($split_cols[1])) {
                            $columns	.= $split_cols[1];
                        }
                    }

                    if (strpos($columns, 'job.boms') !== false) {
                        $this->db->join('job_consumed_items', 'job.job_id = job_consumed_items.job_id', 'left');
                        $this->db->join('bom_items', 'job_consumed_items.item_code = bom_items.item_code', 'left');

                        $split_cols		= explode('job.boms', $columns);
                        $concat_text 	= 'GROUP_CONCAT( bom_items.item_name SEPARATOR " | " ) as `BOMs`';
                        $columns		= $split_cols[0].$concat_text;
                        if (!empty($split_cols[1])) {
                            $columns	.= $split_cols[1];
                        }
                    }

                    if (strpos($columns, 'customer_main_telephone') !== false) {
                        $columns = str_replace('customer_main_telephone,', 'customer.customer_main_telephone, ', $columns);
                    }

                    if (strpos($columns, 'customer_mobile') !== false) {
                        $columns = str_replace('customer_mobile,', 'customer.customer_mobile, ', $columns);
                    }

                    $this->db->select($columns, false);

                    $this->db->join('job_types', 'job_types.job_type_id = job.job_type_id', 'left')
                        ->join('contract', 'contract.contract_id = job_types.contract_id', 'left')
                        ->join('user', 'user.id = job.created_by', 'left')
                        ->join('user user2', 'user2.id = job.last_modified_by', 'left')
                        ->join('user user3', 'user3.id = job.assigned_to', 'left')
                        ->join('user user4', 'user4.id = job.second_assignee_id', 'left')
                        ->join('addresses', 'addresses.main_address_id = job.address_id', 'left')
                        ->join('customer', 'customer.customer_id = job.customer_id', 'left');

                    if (!empty($contract_id)) {
                        $this->db->where('job_types.contract_id', $contract_id);
                    }

                    if (!empty($statuses)) {
                        $this->db->where_in('job.status_id', $statuses);
                    }

                    $this->db->where('job.account_id', $account_id);

                    $arch_where = "( job.archived != 1 or job.archived is NULL )";
                    $this->db->where($arch_where);

                    $this->db->group_by('job.job_id');
                    $this->db->order_by('user3.first_name');

                    if (!empty($date_from)) {
                        $this->db->where('job.job_date >=', $date_from);
                        $this->db->where('job.job_date <=', $date_to);
                    }

                    $report_name = 'Jobs Report';
                    break;

                case 'asset_attributes':

                    $table  = 'asset_attributes';

                    if (strpos($columns, 'asset_attributes.created_by') !== false) {
                        $columns = str_replace('asset_attributes.created_by,', '', $columns);
                        $columns .= ', concat( user.first_name, " ", user.last_name ) as `created_by_full_name`';
                    }

                    if (strpos($columns, 'asset_attributes.attribute_id') !== false) {
                        $columns = str_replace('asset_attributes.attribute_id,', '', $columns);
                        $columns .= ', asset_type_attributes.response_type_alt as `response_type`';
                    }

                    if (strpos($columns, 'asset_attributes.asset_id') !== false) {
                        $columns = str_replace('asset_attributes.asset_id,', '', $columns);
                        $columns .= ', asset.asset_id, asset.asset_unique_id, asset_types.asset_type';
                    }

                    $this->db->select($columns, false);

                    $this->db->join('user', 'user.id = asset_attributes.created_by', 'left');
                    $this->db->join('asset', 'asset.asset_id = asset_attributes.asset_id', 'left');
                    $this->db->join('asset_type_attributes', 'asset_type_attributes.attribute_id = asset_attributes.attribute_id', 'left');
                    $this->db->join('asset_types', 'asset_types.asset_type_id = asset.asset_type_id', 'left');

                    $this->db->where('asset_type_attributes.account_id', $account_id);

                    $this->db->order_by('asset_attributes.attribute_name');
                    $report_name = 'Asset Attributes Report';
                    break;

                case 'job_consumed_items':

                    $table  = 'job_consumed_items';

                    if (strpos($columns, 'job_consumed_items.job_id') !== false) {
                        $columns .= ', job_types.*';
                    }

                    $this->db->select($columns, false);

                    $this->db->join('job', 'job.job_id = job_consumed_items.job_id', 'left');
                    $this->db->join('job_types', 'job_types.job_type_id = job.job_type_id', 'left');
                    $this->db->join('user', 'user.id = job.created_by', 'left');

                    $this->db->where('job_consumed_items.account_id', $account_id);
                    $this->db->where('job_consumed_items.is_confirmed', 1);

                    if (!empty($contract_id)) {
                        $this->db->where('job_types.contract_id', $contract_id);
                    }

                    $this->db->order_by('job_consumed_items.item_type');
                    $report_name = 'Job Stock & BOMs Report';
                    break;

                case 'job_invoice_items':

                    $table  = 'job_consumed_items';

                    $this->db->select('job_consumed_items.job_id, job.job_date, job.client_reference, job.job_notes,
						CASE WHEN customer.contract_id > 0 THEN customer.contract_id ELSE "" END AS contract_id,
						CASE WHEN job.customer_id > 0 THEN job.customer_id ELSE job.site_id END AS po_number,
						SUM( CASE WHEN job_consumed_items.item_type = "bom" THEN ( job_consumed_items.price*job_consumed_items.item_qty ) ELSE 0 END ) AS contract_charge,
						SUM( CASE WHEN job_consumed_items.item_type = "stock" THEN ( job_consumed_items.price*job_consumed_items.item_qty ) ELSE 0 END ) AS materials_total,
						SUM( ( CASE WHEN job_consumed_items.item_type = "bom" THEN ( job_consumed_items.price*job_consumed_items.item_qty ) ELSE 0 END ) + ( CASE WHEN job_consumed_items.item_type = "stock" THEN ( job_consumed_items.price*job_consumed_items.item_qty ) ELSE 0 END ) ) AS `job_total`
						', false)
                            ->join('stock_items', 'job_consumed_items.item_code = stock_items.item_code', 'left')
                            ->join('bom_items', 'job_consumed_items.item_code = bom_items.item_code', 'left')
                            ->join('job', 'job.job_id = job_consumed_items.job_id', 'left')
                            ->join('customer', 'customer.customer_id = job.customer_id', 'left')
                            ->join('site', 'site.site_id = job.site_id', 'left')
                            ->where('job.account_id', $account_id)
                            ->where('( stock_items.account_id = '.$account_id.' OR bom_items.account_id = '.$account_id.' )')
                            ->group_by('job_consumed_items.job_id');

                    $report_name = 'Job Stock & BOMs Report';
                    break;

                case 'job_invoice_report':

                    #$date_from 	= !empty( $dates['job_date']['date_from'] ) ? date( 'Y-m-d', strtotime( $dates['job_date']['date_from'] ) ) : false;
                    #$date_to	= !empty( $dates['job_date']['date_to'] ) 	? date( 'Y-m-d', strtotime( $dates['job_date']['date_to'] ) ) : $date_from;

                    $base_invoice_date = !empty($dates['invoice_date']['date_from']) ? date('Y-m-d', strtotime($dates['invoice_date']['date_from'])) : false;

                    $invoice_date_from 	= !empty($base_invoice_date) ? $base_invoice_date." 00:00:00" : false;
                    $invoice_date_to	= !empty($dates['invoice_date']['date_to']) ? date('Y-m-d', strtotime($dates['invoice_date']['date_to']))." 23:59:59" : (!empty($base_invoice_date) ? $base_invoice_date." 23:59:59" : false) ;

                    $table  = 'job';
                    if (!empty($columns)) {
                        if (strpos($columns, 'job.assigned_to') !== false) {
                            $split_cols		= explode('job.assigned_to', $columns);
                            $engineer_name 	= 'concat( user.first_name," ",user.last_name ) as `engineer_name`';
                            $columns		= $split_cols[0].$engineer_name;
                            if (!empty($split_cols[1])) {
                                $columns	.= $split_cols[1];
                            }
                        }

                        if (strpos($columns, 'bom_items.item_cost') !== false) {
                            $split_cols		= explode('bom_items.item_cost', $columns);
                            $item_combo 	= 'GROUP_CONCAT( bom_items.item_cost SEPARATOR " | " ) as `bom_cost`';
                            $columns		= $split_cols[0].$item_combo;
                            if (!empty($split_cols[1])) {
                                $columns	.= $split_cols[1];
                            }
                        }

                        if (strpos($columns, 'bom_items.item_name') !== false) {
                            $split_cols		= explode('bom_items.item_name', $columns);
                            $engineer_name 	= 'GROUP_CONCAT( bom_items.item_name SEPARATOR " | " ) as `bom_description`';
                            $columns		= $split_cols[0].$engineer_name;
                            if (!empty($split_cols[1])) {
                                $columns	.= $split_cols[1];
                            }
                        }

                        ## Matterials deliberately mis-pelled to prevent conflict with other columns
                        if (strpos($columns, 'matterials') !== false) {
                            $columns = str_replace(', matterials', '', $columns);
                            $columns = str_replace('matterials', '', $columns);
                        }

                        if (strpos($columns, 'job_total') !== false) {
                            $columns = str_replace(', job_total', '', $columns);
                            $columns = str_replace('job_total', '', $columns);
                        }

                        $BASE_SELECT = 'SUM( ( CASE WHEN job_consumed_items.item_type = "bom" THEN ( job_consumed_items.price*job_consumed_items.item_qty ) ELSE 0 END ) + ( CASE WHEN job_consumed_items.item_type = "stock" THEN ( job_consumed_items.price*job_consumed_items.item_qty ) ELSE 0 END ) ) AS `materials_total`,
							SUM( ( CASE WHEN job_consumed_items.item_type = "bom" THEN ( job_consumed_items.price*job_consumed_items.item_qty ) ELSE 0 END ) + ( CASE WHEN job_consumed_items.item_type = "stock" THEN ( job_consumed_items.price*job_consumed_items.item_qty ) ELSE 0 END ) ) + job.base_rate + job.additional_materials_rate AS `job_total_(_Ex._Vat_)`
							';

                        $SQL_SELECT = $columns.', '.$BASE_SELECT;

                        $this->db->select($SQL_SELECT, false);
                    } else {
                        $this->db->select('job.job_id, job_types.job_type, job.client_reference, customer_addresses.address_line1, customer_addresses.address_postcode, job.job_notes, job.invoice_date, job.base_rate,
							SUM( ( CASE WHEN job_consumed_items.item_type = "bom" THEN ( job_consumed_items.price*job_consumed_items.item_qty ) ELSE 0 END ) + ( CASE WHEN job_consumed_items.item_type = "stock" THEN ( job_consumed_items.price*job_consumed_items.item_qty ) ELSE 0 END ) ) AS `materials_total`,
							SUM( ( CASE WHEN job_consumed_items.item_type = "bom" THEN ( job_consumed_items.price*job_consumed_items.item_qty ) ELSE 0 END ) + ( CASE WHEN job_consumed_items.item_type = "stock" THEN ( job_consumed_items.price*job_consumed_items.item_qty ) ELSE 0 END ) ) + job.base_rate` + job.additional_materials_rate
							', false);
                    }

                    if (!empty($contract_id)) {
                        $this->db->where('job_types.contract_id', $contract_id);
                    }

                    $this->db->join('job_consumed_items', 'job.job_id = job_consumed_items.job_id', 'left')
                            ->join('stock_items', 'job_consumed_items.item_code = stock_items.item_code', 'left')
                            ->join('bom_items', 'job_consumed_items.item_code = bom_items.item_code', 'left')
                            ->join('job_types', 'job.job_type_id = job_types.job_type_id', 'left')
                            ->join('customer', 'customer.customer_id = job.customer_id', 'left')
                            ->join('customer_addresses', 'customer_addresses.customer_id = customer.customer_id', 'left')
                            ->join('diary_regions', 'diary_regions.region_id = job.region_id', 'left')
                            ->join('contract', 'contract.contract_id = job_types.contract_id', 'left')
                            ->join('site', 'site.site_id = job.site_id', 'left')
                            ->join('job_statuses', 'job_statuses.status_id = job.status_id', 'left')
                            ->join('job_tracking_statuses', 'job_tracking_statuses.job_tracking_id = job.job_tracking_id', 'left')
                            ->join('user', 'user.id = job.assigned_to', 'left')
                            ->where('job.account_id', $account_id)
                            ->where('( job.archived != 1 OR job.archived IS NULL )')
                            #->where( '( stock_items.account_id = '.$account_id.' OR bom_items.account_id = '.$account_id.' )' )
                            ->group_by('job.job_id');

                    /* if( !empty( $date_from ) ){
                        $this->db->where( 'job.job_date >=', $date_from );
                        $this->db->where( 'job.job_date <=', $date_to );
                    } */

                    if (!empty($invoice_date_from)) {
                        $this->db->where('job.invoice_date >=', $invoice_date_from);
                        $this->db->where('job.invoice_date <=', $invoice_date_to);
                    }

                    ## previous version:: $this->db->where_in( 'job_tracking_statuses.job_tracking_group', ['callcompleted', 'invoicepaid', 'jobinvoiced'] );
                    $this->db->where_in('job_tracking_statuses.job_tracking_group', ['invoicepaid', 'jobinvoiced']);

                    $report_name = 'Job Invoice Report';
                    break;

                case 'asset_report':

                    $table  = 'asset';

                    if (!empty($contract_id)) {
                        $sites = $this->db->select('sites_contracts.site_id', false)
                            ->group_by('sites_contracts.site_id')
                            ->get_where('sites_contracts', [ 'sites_contracts.contract_id'=>$contract_id ]);

                        if ($sites->num_rows() > 0) {
                            $contract_sites = array_column($sites->result_array(), 'site_id');
                            $this->db->where_in('site.site_id', $contract_sites);
                        }
                    }

                    if (strpos($columns, 'asset.asset_type_id') !== false) {
                        $split_cols		= explode('asset.asset_type_id', $columns);
                        $asset_type 	= 'asset_types.asset_type as `asset_type`';
                        $columns		= $split_cols[0].$asset_type;
                        if (!empty($split_cols[1])) {
                            $columns	.= $split_cols[1];
                        }
                    }

                    if (strpos($columns, 'asset.created_by') !== false) {
                        $split_cols		= explode('asset.created_by', $columns);
                        $created_by 	= 'concat( user.first_name," ",user.last_name ) as `created_by`';
                        $columns		= $split_cols[0].$created_by;
                        if (!empty($split_cols[1])) {
                            $columns	.= $split_cols[1];
                        }
                    }

                    if (strpos($columns, 'asset.audit_result_status_id') !== false) {
                        $split_cols		= explode('asset.audit_result_status_id', $columns);
                        $compliance_status 	= 'audit_result_statuses.result_status_alt as `compliance_status`';
                        $columns		= $split_cols[0].$compliance_status;
                        if (!empty($split_cols[1])) {
                            $columns	.= $split_cols[1];
                        }
                    }

                    if (strpos($columns, 'site.site_name') !== false) {
                        $split_cols		= explode('site.site_name', $columns);
                        $site_name 		= 'site.site_name as `site_name`';
                        $columns		= $split_cols[0].$site_name;
                        if (!empty($split_cols[1])) {
                            $columns	.= $split_cols[1];
                        }
                    }

                    if (strpos($columns, 'site_postcode') !== false) {
                        $split_cols		= explode('site_postcode', $columns);
                        $site_postcode 	= 'addresses.postcode as `site_postcode`';
                        $columns		= $split_cols[0].$site_postcode;
                        if (!empty($split_cols[1])) {
                            $columns	.= $split_cols[1];
                        }
                    }

                    if (strpos($columns, 'site_sub_block') !== false) {
                        $split_cols		= explode('site_sub_block', $columns);
                        $site_sub_block = 'site_sub_blocks.sub_block_name as `sub_block_name`';
                        $columns		= $split_cols[0].$site_sub_block;
                        if (!empty($split_cols[1])) {
                            $columns	.= $split_cols[1];
                        }
                    }

                    if (strpos($columns, 'asset_zone') !== false) {
                        $split_cols		= explode('asset_zone', $columns);
                        $asset_zone 	= 'site_zones.zone_name as `zone_name`';
                        $columns		= $split_cols[0].$asset_zone;
                        if (!empty($split_cols[1])) {
                            $columns	.= $split_cols[1];
                        }
                    }

                    if (strpos($columns, 'asset_location') !== false) {
                        $split_cols		= explode('asset_location', $columns);
                        $asset_location = 'site_locations.location_name';
                        $columns		= $split_cols[0].$asset_location;
                        if (!empty($split_cols[1])) {
                            $columns	.= $split_cols[1];
                        }
                    }

                    $this->db->select($columns, false);
                    $this->db->join('asset_types', 'asset_types.asset_type_id = asset. asset_type_id', 'left');
                    $this->db->join('audit_result_statuses', 'asset.audit_result_status_id = audit_result_statuses.audit_result_status_id', 'left');
                    $this->db->join('site', 'site.site_id = asset.site_id', 'left');
                    $this->db->join('site_sub_blocks', 'site_sub_blocks.site_id = site.site_id', 'left');
                    $this->db->join('site_zones', 'site_zones.zone_id = asset.zone_id', 'left');
                    $this->db->join('site_locations', 'site_locations.location_id = asset.location_id', 'left');
                    $this->db->join('addresses', 'addresses.main_address_id = site.site_address_id', 'left');
                    $this->db->join('user', 'user.id = asset.created_by', 'left');
                    $this->db->where('asset.account_id', $account_id);
                    $this->db->where('asset.archived !=', 1);
                    $this->db->order_by('asset.asset_id');
                    $report_name = 'Assets Details Report';

                    break;
            }

            $this->db->limit($limit, $offset);

            $query = $this->db->get($table);

            if ($query->num_rows() > 0) {
                $document_path = '_report_downloads/'.$account_id.'/';
                $upload_path   = $this->app_root.$document_path;

                if (!is_dir($upload_path)) {
                    if (!mkdir($upload_path, 0755, true)) {
                        $this->session->set_flashdata('message', 'Error: Unable to create upload location');
                        return false;
                    }
                }

                $result = $query->result_array();

                $reshuffled_headers = [];
                if ($result[0]) {
                    foreach ($result[0] as $col => $value) {
                        if ($col == 'job_date') {
                            $reshuffled_headers['job_booked_date'] 	= $value;
                        } elseif ($col == 'item_name') {
                            $reshuffled_headers['bom_description'] 	= $value;
                        } else {
                            $reshuffled_headers[$col] 				= $value;
                        }
                    }
                }

                $headers = explode(', ', ucwords(str_replace('_', ' ', implode(', ', array_keys($reshuffled_headers)))));

                $data 	= array_to_csv($result, $headers);

                $file_name 		= $report_name.' - '.date('dmYHi').'.csv';
                $file_path 		= $upload_path.$file_name;

                if (write_file($upload_path.$file_name, $data)) {
                    // if( $req_source == 'web-client' ){
                    // force_download( $report_name, file_get_contents( $file_path ) );
                    // }else{
                    $result = [
                        'timestamp'=>date('d.m.Y H:i:s'),
                        'expires_at'=>date('d.m.Y H:i:s', strtotime('+1 hour')),
                        'file_name'=>$file_name,
                        'file_path'=>$file_path,
                        'file_link'=>base_url($document_path.$file_name)
                    ];
                    //}
                }

                $this->session->set_flashdata('message', 'Report data found');
            } else {
                $this->session->set_flashdata('message', 'There\'s currently no data matching your report criteria');
            }
        } else {
            $this->session->set_flashdata('message', 'Main Account ID is required');
        }
        return $result;
    }

    /**
    * Get Report type setup
    */
    public function get_report_types_setup($account_id  = false, $report_type = false, $source = false)
    {
        $result = false;

        if ($report_type) {
            $report_types = [];

            switch($report_type) {
                case 'site':
                    $report_types['site'] = [
                        'report_type'	=>'Buildings Details',
                        'table_name'	=>$report_type,
                        'table_cols'	=>$this->_get_table_columns($report_type, $source),
                        'date_filters'	=> null,
                        'status_filters'=> null,
                        'group_filters'		=> null,
                        'is_fixed'		=> false,
                    ];
                    break;

                case 'asset':
                    $report_types['asset'] = [
                        'report_type'	=>'Asset Details',
                        'table_name'	=>'asset',
                        'table_cols'	=>$this->_get_table_columns('asset', $source, [ 'last_audit_date', /*'date_created',*/ 'created_by', 'last_modified', 'last_modified_by', 'archived', 'is_active','assignee','gps_latitude', 'gps_longitude', 'end_of_life_date', 'next_audit_date', 'purchase_price', 'purchase_date', 'status_id']),
                        'date_filters'	=>$this->_get_filters('date_filters', ['asset.last_audit_date'=>'Last Audit Date', 'asset.next_audit_date'=>'Next Audit Date'], $source),
                        'status_filters'=>$this->_get_filters('status_filters', false, $source),
                        'group_filters'	=> null,
                        'is_fixed'		=> false,
                    ];
                    break;

                case 'audit':
                    $report_types['audit'] = [
                        'report_type'	=>'Evidoc Overviews',
                        'table_name'	=>'audit',
                        'table_cols'	=>$this->_get_table_columns('audit', $source),
                        #'date_filters'	=>$this->_get_filters( 'date_filters', false, $source ),
                        'date_filters'	=>null,
                        'status_filters'=>$this->_get_filters('status_filters', [ 'Failed'=>'Failed', 'Completed'=>'Completed', 'In Progress'=>'In Progress' ], $source),
                        'group_filters'	=> null,
                        'is_fixed'		=> false,
                    ];
                    break;

                case 'audit_responses':
                    $report_types['audit_responses'] = [
                    'report_type'		=>'Evidoc Responses',
                    'table_name'		=>'audit_responses',
                    'table_cols'		=>$this->_get_table_columns('audit_responses', $source, [ 'segment' ]),
                    'date_filters'		=>$this->_get_filters('date_filters', ['audit_responses.date_created'=>'Date Audited'], $source),
                    'status_filters'	=>$this->_get_filters('status_filters', false, $source),
                    'group_filters'		=> [ 'site'=>'Buildings', 'asset'=>'Assets', /*'fleet'=>'Fleet', 'people'=>'People',*/ 'job'=>'Jobs', 'customer'=>'Customer'], //This is available from the DB
                    'is_fixed'			=> false,
                    ];
                    break;

                case 'job':
                    $report_types['job'] = [
                        'report_type'	=>'Jobs Report',
                        'table_name'	=>'job',
                        /* 'table_cols'	=>$this->_get_table_columns( 'job', $source, ['category_id','archived_on', 'archived_by'] ),   ORIGINAL */
                        'table_cols'		=>array_merge($this->_get_table_columns('job', $source, ['category_id','archived_on', 'archived_by']), ["job.status_id"=> "Job Status", "job.full_address"=> "Full Address", 'customer_main_telephone' => "Customer Main Telephone", 'customer_mobile' => "Customer Mobile", 'job.boms' => 'BOMs']),
                        'date_filters'	=>$this->_get_filters('date_filters', ['job_date'=>'Job Date'], $source),
                        'status_filters'=>$this->_get_filters('status_filters', ['1'=>'Assigned', '2'=>'Un-assigned', '3'=>'In Progress', '4'=>'Successful', '5'=>'Failed', '6'=>'Cancelled'], $source),
                        #'status_filters'=>null,
                        'group_filters'		=> null,
                        'is_fixed'		=> false,
                    ];
                    break;

                case 'job_consumed_items':
                    $report_types['job_consumed_items'] = [
                        'report_type'		=> 'Job Stock and BOMs',
                        'table_name'		=> 'job_consumed_items',
                        'table_cols'		=> $this->_get_table_columns('job_consumed_items', $source, []),
                        'status_filters'	=> null,
                        'date_filters'		=> null,
                        'group_filters'		=> null,
                        'is_fixed'			=> false,
                    ];
                    break;

                case 'job_invoice_items':
                    $report_types['job_invoice_items'] = [
                        'report_type'		=> 'Job Invoice Report',
                        'table_name'		=> 'job_invoice_items',
                        'table_cols'		=> $this->_get_custom_columns('job_invoice_items', $source, []),
                        'status_filters'	=> null,
                        'date_filters'		=> $this->_get_filters('date_filters', ['job.job_date'=>'Job Date'], $source),
                        'group_filters'		=> null,
                        'is_fixed'			=> true,
                    ];
                    break;

                case 'job_invoice_report':
                    $report_types['job_invoice_report'] = [
                        'report_type'		=> 'Job Invoice Report',
                        'table_name'		=> 'job_invoice_report',
                        'table_cols'		=> $this->_get_custom_columns('job_invoice_report', $source, []),
                        'status_filters'	=> null,
                        'date_filters'		=> $this->_get_filters('date_filters', ['invoice_date'=>'Invoice Date'], $source),
                        'group_filters'		=> null,
                        'is_fixed'			=> true,
                    ];
                    break;
            }
        } else {
            $report_types = [
                'asset'=>[
                    'report_type'		=> 'Asset Details',
                    'table_name'		=> 'asset',
                    'table_cols'		=> $this->_get_table_columns('asset', $source, [ 'last_audit_date', /*'date_created',*/ 'created_by', 'last_modified', 'last_modified_by', 'archived', 'is_active','assignee','gps_latitude', 'gps_longitude', 'end_of_life_date', 'next_audit_date', 'purchase_price', 'purchase_date', 'status_id']),
                    'date_filters'		=> $this->_get_filters('date_filters', ['asset.last_audit_date'=>'Last Audit Date', 'asset.next_audit_date'=>'Next Audit Date'], $source),
                    'status_filters'	=> $this->_get_filters('status_filters', false, $source),
                    'group_filters'		=> null,
                    'is_fixed'			=> false,
                ],
                'site'=>[
                    'report_type'=>'Buildings Details',
                    'table_name'=>'site',
                    'table_cols'=>$this->_get_table_columns('site', $source),
                    'date_filters'=>$this->_get_filters('date_filters', [ 'site.last_audit_date'=>'Last Audit Date', 'site.next_audit_date'=>'Next Audit Date' ], $source),
                    //'status_filters'=>$this->_get_filters( 'status_filters', [ 'ok'=>'Ok', 'fault'=>'Fault' ], $source )
                    'group_filters'	=> null,
                    'status_filters'=>null,
                    'is_fixed'		=> false,
                ],
                'audit'=>[
                    'report_type'		=>'Evidoc Overviews',
                    'table_name'		=>'audit',
                    'table_cols'		=>$this->_get_table_columns('audit', $source),
                    #'date_filters'=>$this->_get_filters( 'date_filters', false, $source ),
                    'date_filters'		=>null,
                    'group_filters'		=> null,
                    'is_fixed'			=> false,
                ],
                'audit_responses'=>[
                    'report_type'		=> 'Evidoc Responses',
                    'table_name'		=> 'audit_responses',
                    'table_cols'		=> $this->_get_table_columns('audit_responses', $source, [ 'segment' ]),
                    'date_filters'		=> $this->_get_filters('date_filters', ['audit_responses.date_created'=>'Date Audited'], $source),
                    'status_filters'	=> $this->_get_filters('status_filters', false, $source),
                    'group_filters'		=> [ 'site'=>'Buildings', 'asset'=>'Assets', /*'fleet'=>'Fleet', 'people'=>'People',*/ 'job'=>'Jobs', 'customer'=>'Customer'], //This is available from the DB
                    'is_fixed'			=> false,
                ],
                'fleet'=>[
                    'report_type'=>'Vehicle Details',
                    'table_name'=>'fleet_vehicle',
                    'table_cols'=>$this->_get_table_columns('fleet_vehicle', $source),
                    'date_filters'=>$this->_get_filters('date_filters', false, $source),
                    //'date_filters'=>$this->_get_filters( 'date_filters', false, $source ),
                    //'status_filters'=>$this->_get_filters( 'status_filters', [ '1'=>'Returned', '2'=>'Not Assigned', '3'=>'Garage', '4'=>'Off-Hired' ], $source )
                    'status_filters'=>null,
                    'group_filters'		=> null,
                    'is_fixed'		=> false,
                ],
                'people'=>[
                    'report_type'=>'People Details',
                    'table_name'=>'people',
                    'table_cols'=>$this->_get_table_columns('people', $source),
                    'date_filters'=>$this->_get_filters('date_filters', false, $source),
                    //'status_filters'=>$this->_get_filters( 'status_filters', false, $source )
                    'status_filters'=>null,
                    'group_filters'	=> null,
                    'is_fixed'		=> false,
                ],
                'job'=>[
                    'report_type'		=>'Jobs Report',
                    'table_name'		=>'job',
                    #'table_cols'		=>array_merge( $this->_get_table_columns( 'job', $source, ['category_id','archived_on', 'archived_by'] ), ['job.full_address'=> 'Full Address', 'customer_main_telephone' => 'Customer Main Telephone', 'customer_mobile' => 'Customer Mobile', 'job.boms' => 'BOMs'] ),
                    'table_cols'		=>array_merge($this->_get_table_columns('job', $source, ['category_id','archived_on', 'archived_by']), ["job.status_id"=> "Job Status", "job.full_address"=> "Full Address", 'customer_main_telephone' => "Customer Main Telephone", 'customer_mobile' => "Customer Mobile", 'job.boms' => 'BOMs']),
                    'date_filters'		=>$this->_get_filters('date_filters', ['job_date'=>'Job Date'], $source),
                    'status_filters'	=>$this->_get_filters('status_filters', ['1'=>'Assigned', '2'=>'Un-assigned', '3'=>'In Progress', '4'=>'Successful', '5'=>'Failed', '6'=>'Canceled'], $source),
                    'group_filters'		=> null,
                    'is_fixed'			=> false,
                ],
                'asset_attributes'=>[
                    'report_type'		=> 'Attributes Report',
                    'table_name'		=> 'asset_attributes',
                    'table_cols'		=> $this->_get_table_columns('asset_attributes', $source, []),
                    'status_filters'	=> null,
                    'date_filters'		=> null,
                    'group_filters'		=> null,
                    'is_fixed'			=> false,
                ],
                'job_consumed_items'=>[
                    'report_type'		=> 'Job Stock and BOMs',
                    'table_name'		=> 'job_consumed_items',
                    'table_cols'		=> $this->_get_table_columns('job_consumed_items', $source, []),
                    'status_filters'	=> null,
                    'date_filters'		=> null,
                    'group_filters'		=> null,
                    'is_fixed'			=> false,
                ],
                /* 'job_invoice_items'=>[
                    'report_type'		=> 'Job Invoice Report (Old)',
                    'table_name'		=> 'job_invoice_items',
                    'table_cols'		=> $this->_get_custom_columns( 'job_invoice_items', $source, [] ),
                    'status_filters'	=> NULL,
                    'date_filters'		=> $this->_get_filters( 'date_filters', ['job.job_date'=>'Job Date'], $source ),
                    'group_filters'		=> NULL,
                    'is_fixed'			=> true,
                ], */
                'job_invoice_report'=>[
                    'report_type'		=> 'Job Invoice Report',
                    'table_name'		=> 'job_invoice_report',
                    'table_cols'		=> $this->_get_custom_columns('job_invoice_report', $source, []),
                    'status_filters'	=> null,
                    'date_filters'		=> $this->_get_filters('date_filters', ['invoice_date'=>'Invoice Date'], $source),
                    'group_filters'		=> null,
                    'is_fixed'			=> true,
                ]
            ];
        }
        $result = (!empty($report_types)) ? json_decode(json_encode($report_types)) : $result;

        return $result;
    }

    /*
    * 	Prepare table columns for reporting
    */
    private function _get_table_columns($table_name = false, $source = false, $additional_exclude = false)
    {
        $result = [];
        if (!empty($table_name)) {
            if (!empty($additional_exclude) && is_array($additional_exclude)) {
                $this->exempt_columns = array_merge($this->exempt_columns, $additional_exclude);
            }

            switch($table_name) {
                case 'asset_report':
                    $table_name = 'asset';
                    break;
                default:
                    $table_name = $table_name;
                    break;
            }

            $columns = $this->db->list_fields($table_name);



            $status_key = array_search('status_id', $this->exempt_columns);
            if (!empty($status_key)) {
                unset($this->exempt_columns[$status_key]);
            }

            foreach ($columns as $column) {
                if (!in_array($column, $this->exempt_columns)) {
                    ##Exceptions
                    switch($column) {
                        case 'audit_result_status_id':
                            $friendly_name = 'compliance_status';
                            break;
                        default:
                            $friendly_name = $column;
                            break;
                    }

                    if ((!empty($source)) && in_array($source, ['android', 'and', 'droid'])) {
                        $result[] = [
                            'column_name'=>$table_name.'.'.$column,
                            'column_desc'=>ucwords(str_replace("_", " ", $friendly_name))
                        ];
                    } else {
                        $result[$table_name.'.'.$column] = ucwords(str_replace("_", " ", $friendly_name));
                    }
                }
            }
        }
        return $result;
    }


    /** Get Filters **/
    private function _get_filters($filter_type = false, $values_arr = array(), $source = false)
    {
        $result = null;

        if (!empty($filter_type) && !empty($values_arr)) {
            $default_name = $default_desc = false;
            switch($filter_type) {
                case (in_array($filter_type, [ 'status', 'statuses', 'status_filter', 'status_filters' ])):
                    $default_name = 'status_filter_name';
                    $default_desc = 'status_filter_desc';
                    break;

                case (in_array($filter_type, [ 'date', 'dates', 'date_filter', 'date_filters' ])):
                    $default_name = 'date_filter_name';
                    $default_desc = 'date_filter_desc';
                    break;
            }

            if (!empty($default_name) && !empty($default_desc)) {
                $result = [];
                foreach ($values_arr as $column => $value) {
                    if ((!empty($source)) && in_array($source, ['android', 'and', 'droid'])) {
                        $result[] = [
                            $default_name => ( string ) $column,
                            $default_desc => ucwords(str_replace("_", " ", $value))
                        ];
                    } else {
                        $result[$column] = ucwords(str_replace("_", " ", $value));
                    }
                }
            }
        }
        return $result;
    }


    /** Get Custom Columns for specific Reports **/
    private function _get_custom_columns($table_name = false, $source = false, $additional_exclude = false)
    {
        $result = false;
        if (!empty($table_name)) {
            switch($table_name) {
                case 'job_invoice_items':
                    $columns 	= [ 'recall'=>'Recall', 'job_id'=>'Job ID', 'po_number'=>'PO Number', 'postcode'=>'Post Code', 'region'=>'Region', 'job_notes'=>'Job Notes', 'actual_call'=>'Actual Call', 'invoice_date'=>'Invoice Date', 'materials'=>'Materials', 'contract_charge'=>'Contract Charge', 'job_total'=>'Job Total (Ex. VAT)' ];
                    break;

                case 'job_invoice_report':
                    $columns 	= [ 'contract.contract_name'=>'Contract Name', 'job.job_id'=>'Job ID', 'diary_regions.region_name'=>'Region', 'job.client_reference'=>'PO Number', 'customer.salutation'=>'Title',  'customer.customer_last_name'=>'Customer Surname', 'customer_addresses.address_line1'=>'Address Line 1', 'customer_addresses.address_postcode'=>'Postcode', 'job_types.job_type'=>'Job Type', 'job.works_required'=>'Works Required', 'job.completed_works'=>'Job Notes', 'job.assigned_to'=>'Engineer Name', 'job.created_on'=>'Job Entered Date',  'job.job_date'=>'Job Booking Date', 'job.invoice_date'=>'Job Invoice Date', 'bom_items.item_cost'=>'BOM Cost', 'bom_items.item_name'=>'BOM Descriptoion', 'job.base_rate'=>'Job Base Rate', 'matterials'=>'Materials', 'job.additional_materials'=>'Additional Materials', 'job.additional_materials_rate'=>'Additional Materials Rate', 'job_total'=>'Job Total (Ex. VAT)' ];
                    break;

                case 'asset_report':
                    $columns 	= [ 'asset.asset_id'=>'Asset ID', 'asset.asset_unique_id'=>'Asset Unique ID', 'asset.parent_asset_id'=>'Parent Asset', 'asset.asset_type_id'=>'Asset Type', 'asset.audit_result_status_id'=>'Compliance Status', 'site.site_name'=>'Site Name', 'site_postcode'=>'Site Postcode', 'site_sub_block'=>'Sub Block', 'asset_zone'=>'Site Zone', 'asset_location'=>'Asset Location' ];
                    break;

                default:
                    $columns	= [];
                    break;
            }
        }
        $result = (!empty($columns)) ? $columns : $result;
        return $result;
    }


    /**
    * Get Tailored Reports setup
    */
    public function get_tailored_reports_setup($account_id  = false, $report_type = false, $source = false)
    {
        $result = false;

        $report_types = [
            'asset_report'=>[
                'report_type'		=> 'Assets Details 	Report',
                'table_name'		=> 'asset',
                #'table_cols'		=> $this->_get_table_columns( 'asset', $source, [ 'last_audit_date', 'date_created', 'created_by', 'last_modified', 'last_modified_by', 'archived', 'is_active','assignee','gps_latitude', 'gps_longitude', 'end_of_life_date', 'next_audit_date', 'purchase_price', 'purchase_date', 'status_id'] ),
                'table_cols'		=> $this->_get_custom_columns('asset_report', $source, []),
                'date_filters'		=> $this->_get_filters('date_filters', ['asset.last_audit_date'=>'Last Audit Date', 'asset.next_audit_date'=>'Next Audit Date'], $source),
                'status_filters'	=> $this->_get_filters('status_filters', false, $source),
                'group_filters'		=> null,
                'is_fixed'			=> false,
            ],
            /*'site'=>[
                'report_type'=>'Buildings Report',
                'table_name'=>'site',
                'table_cols'=>$this->_get_table_columns( 'site', $source ),
                'date_filters'=>$this->_get_filters( 'date_filters', [ 'site.last_audit_date'=>'Last Audit Date', 'site.next_audit_date'=>'Next Audit Date' ], $source ),
                //'status_filters'=>$this->_get_filters( 'status_filters', [ 'ok'=>'Ok', 'fault'=>'Fault' ], $source )
                'group_filters'	=> NULL,
                'status_filters'=>null,
                'is_fixed'		=> false,
            ],
            'audit'=>[
                'report_type'		=>'Evidoc Overviews',
                'table_name'		=>'audit',
                'table_cols'		=>$this->_get_table_columns( 'audit', $source ),
                #'date_filters'=>$this->_get_filters( 'date_filters', false, $source ),
                'date_filters'		=>null,
                'group_filters'		=> NULL,
                'is_fixed'			=> false,
            ],
            'audit_responses'=>[
                'report_type'		=> 'Evidoc Responses',
                'table_name'		=> 'audit_responses',
                'table_cols'		=> $this->_get_table_columns( 'audit_responses', $source, [ 'segment' ] ),
                'date_filters'		=> $this->_get_filters( 'date_filters', ['audit_responses.date_created'=>'Date Audited'], $source ),
                'status_filters'	=> $this->_get_filters( 'status_filters', false, $source ),
                'group_filters'		=> [ 'site'=>'Buildings', 'asset'=>'Assets', 'job'=>'Jobs', 'customer'=>'Customer'], //This is available from the DB
                'is_fixed'			=> false,
            ],
            'people'=>[
                'report_type'=>'People Details',
                'table_name'=>'people',
                'table_cols'=>$this->_get_table_columns( 'people', $source ),
                'date_filters'=>$this->_get_filters( 'date_filters', false, $source ),
                #'status_filters'=>$this->_get_filters( 'status_filters', false, $source )
                'status_filters'=>null,
                'group_filters'	=> NULL,
                'is_fixed'		=> false,
            ],
            'job'=>[
                'report_type'		=>'Jobs Report',
                'table_name'		=>'job',
                #'table_cols'		=>array_merge( $this->_get_table_columns( 'job', $source, ['category_id','archived_on', 'archived_by'] ), ['job.full_address'=> 'Full Address', 'customer_main_telephone' => 'Customer Main Telephone', 'customer_mobile' => 'Customer Mobile', 'job.boms' => 'BOMs'] ),
                'table_cols'		=>array_merge( $this->_get_table_columns( 'job', $source, ['category_id','archived_on', 'archived_by'] ), ["job.status_id"=> "Job Status", "job.full_address"=> "Full Address", 'customer_main_telephone' => "Customer Main Telephone", 'customer_mobile' => "Customer Mobile", 'job.boms' => 'BOMs'] ),
                'date_filters'		=>$this->_get_filters( 'date_filters', ['job_date'=>'Job Date'], $source ),
                'status_filters'	=>$this->_get_filters( 'status_filters', ['1'=>'Assigned', '2'=>'Un-assigned', '3'=>'In Progress', '4'=>'Successful', '5'=>'Failed', '6'=>'Canceled'], $source ),
                'group_filters'		=> NULL,
                'is_fixed'			=> false,
            ],
            'asset_attributes'=>[
                'report_type'		=> 'Attributes Report',
                'table_name'		=> 'asset_attributes',
                'table_cols'		=> $this->_get_table_columns( 'asset_attributes', $source, [] ),
                'status_filters'	=> NULL,
                'date_filters'		=> NULL,
                'group_filters'		=> NULL,
                'is_fixed'			=> false,
            ],
            'job_consumed_items'=>[
                'report_type'		=> 'Job Stock and BOMs',
                'table_name'		=> 'job_consumed_items',
                'table_cols'		=> $this->_get_table_columns( 'job_consumed_items', $source, [] ),
                'status_filters'	=> NULL,
                'date_filters'		=> NULL,
                'group_filters'		=> NULL,
                'is_fixed'			=> false,
            ],
            'job_invoice_report'=>[
                'report_type'		=> 'Job Invoice Report',
                'table_name'		=> 'job_invoice_report',
                'table_cols'		=> $this->_get_custom_columns( 'job_invoice_report', $source, [] ),
                'status_filters'	=> NULL,
                'date_filters'		=> $this->_get_filters( 'date_filters', ['invoice_date'=>'Invoice Date'], $source ),
                'group_filters'		=> NULL,
                'is_fixed'			=> true,
            ]*/
        ];

        $result = (!empty($report_types)) ? json_decode(json_encode($report_types)) : $result;

        return $result;
    }
}
