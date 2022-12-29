<?php

namespace Application\Modules\Service\Models;

class Job_model extends CI_Model
{
    public function __construct()
    {
        #parent::__construct();
        //$this->load->model('serviceapp/Quote_model','quote_service');
        $this->load->model('serviceapp/Site_model', 'site_service');
        $this->load->model('serviceapp/Customer_model', 'customer_service');
        $this->load->model('serviceapp/Diary_Date_model', 'diary_date_service');
        $this->load->model('serviceapp/Billable_item_model', 'billable_item_service');
        $this->load->model('serviceapp/Risk_Assessment_model', 'ra_service');
        $this->load->model('serviceapp/Audit_model', 'evidocs_service');
        $this->load->model('serviceapp/Diary_model', 'diary_service');
        #$this->load->model( 'serviceapp/Task_model','task_service' );
    }

    //Searchable fields
    private $searchable_fields  				= [ 'job.job_id', 'job.status_id', 'job.job_type_id', 'job.assigned_to', 'addrs.postcode_nospaces','addrs.postcode', 'customer_addresses.address_postcode', 'job.job_tracking_id', 'job.external_job_ref', 'job.external_job_call_status' ];
    private $job_types_search_fields			= [ 'job_types.job_type', 'job_types.job_type_desc' ];
    private $fail_codes_search_fields			= [ 'fail_code', 'fail_code_text', 'fail_code_desc', 'fail_code_group' ];
    private $schedule_search_fields				= [ 'schedules.schedule_id', 'schedule_name', 'frequency_group'];
    private $schedule_frequencies_search_fields	= [ 'frequency_name', 'frequency_ref', 'frequency_desc' ];
    private $job_tracking_statuses_search_fields= [ 'job_tracking_id', 'job_tracking_status', 'job_tracking_desc' ];
    private $activity_search_fields				= [ 'activity_name', 'status', 'job_type', 'job_status' ];
    private $minimal_searchable_fields  		= [ 'job.job_id', 'job.status_id', 'job.job_type_id', 'job.assigned_to', 'job.contract_id', 'job.region_id', 'job.address_id', 'job.job_tracking_id', 'job.external_job_ref', 'job.external_job_call_status', 'site.site_name' ];
    private $tesseract_linked_statuses 			= [ 'enroute', 'onsite', 'successful', 'onhold' ];
    private $checklist_searchable_fields  		= [ 'job.job_id', 'job_types.job_type', 'job.assigned_to', 'site.site_name', 'site.site_reference', 'user.first_name', 'user.last_name', 'job_statuses.job_status' ];

    /** Check Jobs Access **/
    private function _check_jobs_access($user = false)
    {
        $result = false;
        if (!empty($user)) {
            if (!empty($user->is_primary_user)) {
                $result = false;
            } elseif (!$user->is_primary_user && !empty($user->associated_user_id)) {
                $result = [$user->associated_user_id, $user->id];
            } elseif (!$user->is_primary_user && !$user->associated_user_id) {
                if (in_array($user->user_type_id, EXTERNAL_USER_TYPES)) {
                    $contract_access = $this->contract_service->get_linked_people($user->account_id, false, $user->id, ['as_arraay'=>1]);
                    $lead_assignees  = !empty($contract_access) ? array_filter(array_column($contract_access, 'contract_lead_id')) : [];
                    if (!empty($lead_assignees)) {
                        #$result = $lead_assignees;
                        $result = [];
                    } else {
                        $result = [];
                    }
                } else {
                    $result = [];
                }
            } else {
                $result = [];
            }
        }
        return $result;
    }

    /*
    * Get Jobs single records or multiple records
    */
    public function get_jobs($account_id = false, $job_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result 	= false;
        $where 		= convert_to_array($where);
        $assignees 	= $this->_check_jobs_access($this->ion_auth->_current_user());

        #Limit Jobs List access by Associated Buildings
        if ((!$this->ion_auth->_current_user()->is_admin) && !empty($this->ion_auth->_current_user()->buildings_visibility) && (strtolower($this->ion_auth->_current_user()->buildings_visibility) == 'limited')) {
            $buildings_access 	= $this->site_service->get_user_associated_buildings($account_id, $this->ion_auth->_current_user->id);
            $allowed_buildings  = !empty($buildings_access) ? array_column($buildings_access, 'site_id') : [];

            if (!empty($allowed_buildings)) {
                $site_ids_str 	= implode(',', $allowed_buildings);

                $linked_assets = $this->db->select('asset.asset_id', false)
                    ->where_in('asset.site_id', $allowed_buildings)
                    ->where('asset.account_id', $account_id)
                    ->where('asset.archived !=', 1)
                    ->group_by('asset.asset_id')
                    ->get('asset');

                if ($linked_assets->num_rows() > 0) {
                    $asset_ids 		= array_column($linked_assets->result_array(), 'asset_id');
                    $asset_ids_str 	= implode(',', $asset_ids);
                    $sql_combi 		= '( job.site_id IN ('.$site_ids_str.' ) OR job.asset_id IN ('.$asset_ids_str.' ) )';
                } else {
                    $sql_combi		= '( job.site_id IN ('.$site_ids_str.' ) )';
                }

                $this->db->where($sql_combi);
            } else {
                $this->session->set_flashdata('message', 'No data found matching your criteria.');
                return false;
            }
        }

        #Limit access by contract to External User Types
        if (in_array($this->ion_auth->_current_user()->user_type_id, EXTERNAL_USER_TYPES)) {
            if (!empty($this->ion_auth->_current_user()->is_primary_user)) {
                ## Get associated users
                if (!$job_id) {
                    $group_assignees = $this->ion_auth->get_associated_users($account_id, $this->ion_auth->_current_user()->id, false, ['as_arraay'=>1]);
                    if (!empty($group_assignees)) {
                        $group_assignees = (!empty($group_assignees)) ? array_column($group_assignees, 'user_id') : [$this->ion_auth->_current_user()->id];
                        $group_assignees = (!in_array($this->ion_auth->_current_user()->id, $group_assignees)) ? array_merge($group_assignees, [$this->ion_auth->_current_user()->id]) : $group_assignees;
                        $raw_where['group_assignees']	= $group_assignees;
                        $this->db->where_in('job.assigned_to', $group_assignees);
                    } else {
                        $contract_access = $this->contract_service->get_linked_people($account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1]);
                        $allowed_access  = !empty($contract_access) ? array_column($contract_access, 'contract_id') : [];
                        if (!empty($allowed_access)) {
                            $this->db->where_in('job_types.contract_id', $allowed_access);
                        } else {
                            $this->session->set_flashdata('message', 'No data found matching your criteria');
                            return false;
                        }
                    }
                }
            } else {
                $contract_access = $this->contract_service->get_linked_people($account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1]);
                $allowed_access  = !empty($contract_access) ? array_column($contract_access, 'contract_id') : [];
                if (!empty($allowed_access)) {
                    $this->db->where_in('job_types.contract_id', $allowed_access);
                } else {
                    $this->session->set_flashdata('message', 'No data found matching your criteria');
                    return false;
                }
            }
        }

        $this->db->select('job.*, site.qr_code_location, site.site_notes, job_types.contract_id as contract_id, job_types.*, schedule_activities.activity_name, schedule_activities.status  `activity_status`, job_statuses.job_status, job_statuses.status_group, job_tracking_statuses.job_tracking_status, job_tracking_statuses.job_tracking_group, fc.fail_code, fc.fail_code_text, fc.fail_code_desc, fc.fail_code_group, audit_categories.category_name, CONCAT(user.first_name," ",user.last_name) `assignee`,  CONCAT(user2.first_name," ",user2.last_name) `second_assignee`, CONCAT( creator.first_name," ",creator.last_name ) `created_by_full_name`, addrs.main_address_id,addrs.addressline1 `address_line_1`, addrs.addressline2 `address_line_2`,addrs.addressline3 `address_line_3`,addrs.posttown `address_city`,addrs.county `address_county`, addrs.postcode `address_postcode`, customer_addresses.address_postcode `customer_postcode`, postcode_area, postcode_district, postcode_sector, addrs.summaryline `summaryline`, CONCAT( addrs.addressline1,", ",addrs.addressline2,", ",addrs.posttown, ", ",addrs.posttown,", ",addrs.postcode ) `short_address`, addrs.organisation `address_business_name`, diary_regions.region_name, user.external_user_ref, user.external_username, job.last_modified `job_last_modified_time`, site.site_actual_address, site.site_actual_postcode, site.site_address_verified, CONCAT(modifier.first_name," ",modifier.last_name) `last_modified_by`, account_discipline.account_discipline_name, account_discipline.account_discipline_image_url `discipline_image_url`, site_zones.zone_name, site_zones.zone_description, site_locations.location_name, site_locations.resident_salutation, site_locations.resident_first_name, site_locations.resident_last_name, site_locations.location_notes', false)
            ->join('addresses addrs', 'addrs.main_address_id = job.address_id', 'left')
            ->join('job_types', 'job_types.job_type_id = job.job_type_id', 'left')
            ->join('job_statuses', 'job_statuses.status_id = job.status_id', 'left')
            ->join('job_tracking_statuses', 'job_tracking_statuses.job_tracking_id = job.job_tracking_id', 'left')
            ->join('audit_categories', 'audit_categories.category_id = job.category_id', 'left')
            ->join('job_fail_codes fc', 'fc.fail_code_id = job.fail_code_id', 'left')
            ->join('user', 'user.id = job.assigned_to', 'left')
            ->join('user user2', 'user2.id = job.second_assignee_id', 'left')
            ->join('user creator', 'creator.id = job.created_by', 'left')
            ->join('user modifier', 'modifier.id = job.last_modified_by', 'left')
            ->join('site', 'site.site_id = job.site_id', 'left')
            ->join('customer', 'customer.customer_id = job.customer_id', 'left')
            ->join('customer_addresses', 'customer_addresses.customer_id = customer.customer_id', 'left')
            ->join('schedule_activities', 'schedule_activities.activity_id = job.activity_id', 'left')
            ->join('diary_regions', 'diary_regions.region_id = job.region_id', 'left')
            ->join('account_discipline', 'account_discipline.discipline_id = job_types.discipline_id', 'left')
            ->join('site_zones', 'site_zones.zone_id = job.zone_id', 'left')
            ->join('site_locations', 'site_locations.location_id = job.location_id', 'left')
            ->where('job.archived !=', 1);

        if (!empty($account_id)) {
            $this->db->where('job.account_id', $account_id);
        }

        if (!empty($job_id)) {
            $row = $this->db->get_where('job', ['job_id'=>$job_id])->row();

            if (!empty($row)) {
                $row->site_details  	= null;
                $row->customer_details  = null;

                if (!empty($row->customer_id)) {
                    $customer_details 		= $this->customer_service->get_customers($account_id, $row->customer_id);
                    $row->customer_details  = !empty($customer_details) ? $customer_details : null;
                }

                if (!empty($row->site_id)) {
                    $site_details 			= $this->site_service->get_sites($account_id, $row->site_id);
                    $row->site_details  	= !empty($site_details) ? $site_details : null;
                }

                $this->session->set_flashdata('message', 'Job record found');
                $required_items			= $this->get_required_items($account_id, $job_id);
                $consumed_items			= $this->get_consumed_items($account_id, $job_id);
                $ra_responses			= $this->ra_service->get_ra_responses(false, $job_id);
                $associated_risks		= $this->get_associated_risks($account_id, $row->job_type_id);
                $dynamic_risks			= $this->get_dynamic_risks($account_id, $row->job_id);

                $row->required_items  	= (!empty($required_items)) ? $required_items : null;
                $row->consumed_items 	= (!empty($consumed_items)) ? $consumed_items : null;
                $row->ra_responses 		= (!empty($ra_responses)) ? $ra_responses : null;
                $row->associate_risks	= (!empty($associated_risks)) ? $associated_risks : null;
                $row->dynamic_risks 	= (!empty($dynamic_risks)) ? $dynamic_risks : null;

                $row->comm_logs			= $this->get_communication_logs($account_id, false, false, $job_id);
                $result = $row;
            } else {
                $this->session->set_flashdata('message', 'Job record not found');
            }
            return $result;
        }

        if (!empty($where)) {
            if (isset($where['site_id'])) {
                if (!empty($where['site_id'])) {
                    $this->db->where('job.site_id', $where['site_id']);
                }
            }

            if (isset($where['customer_id'])) {
                if (!empty($where['customer_id'])) {
                    $this->db->where('job.customer_id', $where['customer_id']);
                }
            }

            if (isset($where['contract_id'])) {
                if (!empty($where['contract_id'])) {
                    $this->db->where('job.contract_id', $where['contract_id']);
                }
                unset($where['contract_id']);
            }

            ## Combined assignees
            if (!empty($assignees)) {
                if (!empty($where['assigned_to'])) {
                    $assignees[] = $where['assigned_to'];
                }
                $this->db->where_in('job.assigned_to', $assignees);
            } else {
                if (isset($where['assigned_to'])) {
                    if (!empty($where['assigned_to'])) {
                        if ($where['assigned_to'] < 0) {
                            $where_job = "( ( job.assigned_to is NULL ) || ( job.assigned_to = 0 ) || ( job.assigned_to = '' ) )";
                            $this->db->where($where_job);
                        } else {
                            $this->db->where('( ( job.assigned_to = "'.$where['assigned_to'].'" ) || ( job.second_assignee_id = "'.$where['assigned_to'].'" ) )');
                        }
                    }
                    unset($where['assigned_to']);
                }
            }

            if (isset($where['location_id'])) {
                if (!empty($where['location_id'])) {
                    $this->db->where('job.location_id', $where['location_id']);
                }
            }

            if (isset($where['asset_id'])) {
                if (!empty($where['asset_id'])) {
                    $this->db->where('job.asset_id', $where['asset_id']);
                }
            }

            if (isset($where['job_date'])) {
                if (!empty($where['job_date'])) {
                    $sjob_date = date('Y-m-d', strtotime($where['job_date']));
                    $this->db->where('job.job_date', $sjob_date);
                    unset($where['job_date']);
                }
            } else {
                if (isset($where['date_from']) || isset($where['date_to'])) {
                    if (!empty($where['date_from'])) {
                        $this->db->where('job.job_date >=', date('Y-m-d', strtotime($where['date_from'])));
                    }

                    if (!empty($where['date_to'])) {
                        $this->db->where('job.job_date <=', date('Y-m-d', strtotime($where['date_to'])));
                    }
                    unset($where['date_from'], $where['date_to']);
                }
            }

            if (isset($where['region_id'])) {
                if (!empty($where['region_id'])) {
                    $region_ids = is_array($where['region_id']) ? $where['region_id'] : [ $where['region_id'] ] ;
                    $this->db->where_in('job.region_id', $region_ids);
                }
                unset($where['region_id']);
            }

            if (isset($where['discipline_id'])) {
                if (!empty($where['discipline_id'])) {
                    $disciplines = (!is_array($where['discipline_id']) && ((int) $where['discipline_id'] > 0)) ? [ $where['discipline_id'] ] : ((is_array($where['discipline_id'])) ? $where['discipline_id'] : (is_object($where['discipline_id']) ? object_to_array($where['discipline_id']) : []));
                    $this->db->where_in('job_types.discipline_id', $disciplines);
                }
                unset($where['discipline_id']);
            }

            if (isset($where['exclude_successful_jobs'])) {
                if (!empty($where['exclude_successful_jobs'])) {
                    $this->db->where_not_in('job.status_id', [ 4 ]); //Remove Successful Jobs
                }
                unset($where['exclude_successful_jobs']);
            }


            if (isset($where['open_jobs'])) {
                #if( !empty( $where['open_jobs'] ) ){
                $this->db->where('( job.job_date = "1970-01-01" OR job.job_date = "0000-00-00" OR job.job_date IS NULL )');
                $this->db->where('( job.due_date != "1970-01-01" AND job.due_date != "0000-00-00" AND job.due_date IS NOT NULL )');
                $this->db->where('( job.assigned_to > 0 )');
                #}
                unset($where['open_jobs']);
            }

            if (isset($where['is_reactive'])) {
                #if( !empty( $where['is_reactive'] ) ){
                $this->db->where('job_types.is_reactive', $where['is_reactive']);
                #}
                unset($where['is_reactive']);
            }

            if (isset($where['is_scheduled'])) {
                $this->db->where('job.schedule_id >', 0);
                unset($where['is_scheduled']);
            }
        }

        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }

        $job = $this->db->order_by('job_id desc, job_date desc, job_type')
            ->group_by('job_id')
            ->get('job');

        if ($job->num_rows() > 0) {
            $this->session->set_flashdata('message', 'Job records found');
            $result = $job->result();
        } else {
            $this->session->set_flashdata('message', 'Job record(s) not found');
        }

        return $result;
    }

    /*
    * Create new Job
    */
    public function create_job($account_id=false, $postdata = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($postdata)) {
            $data = [];
            foreach ($postdata as $key=>$value) {
                if (in_array($key, format_date_columns())) {
                    $value = format_datetime_db($value);
                } else {
                    $value = (is_string($value)) ? trim($value) : $value;
                }
                $data[$key]= $value;
            }

            if (!empty($data)) {
                $slots = (!empty($data['job_duration'])) ? $data['job_duration'] : 1;

                if (!empty($where)) {
                    $job_exists = $this->db->where($where)
                        ->where('job.archived !=', 1)
                        ->get_where('job', ['account_id'=>$data['account_id'],'job_type_id'=>$data['job_type_id'],'job_date'=>$data['job_date']])->row();
                } else {
                    $job_exists = false;
                }

                if (!$job_exists) {
                    if (!empty($data['job_type_id'])) {
                        $job_type_check = $this->_validate_job_type($account_id, $data['job_type_id']);
                        if (!$job_type_check) {
                            $this->session->set_flashdata('message', 'This Job Type record does not exist or does not belong to you.');
                            return false;
                        }
                        $data['base_rate'] = $job_type_check->job_base_rate;
                    } else {
                        // $this->session->set_flashdata( 'message','Job Type field is required.' );
                        // return false;
                    }

                    ## Assign Region
                    if (empty($data['region_id'])) {
                        if (!empty($data['address_id'])) {
                            $address_record = $this->db->select('postcode, postcode_district, postcode_area', false)->get_where('addresses', [ 'main_address_id'=>$data['address_id'] ])->row();
                            if (!empty($address_record)) {
                                $region = $this->db->select('region_id, postcode_district')
                                    ->where('diary_region_postcodes.account_id', $account_id)
                                    ->where('diary_region_postcodes.postcode_district', $address_record->postcode_district)
                                    ->group_by('diary_region_postcodes.region_id')
                                    ->limit(1)
                                    ->get('diary_region_postcodes')
                                    ->row();
                                if (!empty($region)) {
                                    $data['region_id'] = $region->region_id;
                                }
                            }
                        }
                    }

                    $unassigned_status 	= $this->db->get_where('job_statuses', [ 'account_id'=>$account_id, 'status_group'=>'unassigned' ])->row();
                    $assigned_status 	= $this->db->get_where('job_statuses', [ 'account_id'=>$account_id, 'status_group'=>'assigned' ])->row();

                    $data['status_id'] 	= (!empty($data['status_id'])) ? $data['status_id'] : (!empty($unassigned_status->status_id) ? $unassigned_status->status_id : 2);
                    $data['created_by'] = $this->ion_auth->_current_user->id;
                    $job_date			= !empty($data['job_date']) ? $data['job_date'] : false;

                    $notify_engineer 	= false;
                    if (!empty($data['assigned_to'])) {
                        $data['status_id'] 	= (!empty($assigned_status->status_id) ? $assigned_status->status_id : 1);
                        $tracking_status 	= $this->fetch_tracking_status($account_id, (object)[ 'job_date'=>$job_date, 'job_status_group'=>'assigned' ]);

                        ## Notify Engineer
                        if ($data['status_id'] == 1) {
                            $notify_engineer = true;
                        }
                    } else {
                        $tracking_status 	= $this->fetch_tracking_status($account_id, (object)[ 'job_date'=>$job_date, 'job_status_group'=>'unassigned' ]);
                    }

                    $data['job_tracking_id'] = !empty($tracking_status['job_tracking_id']) ? $tracking_status['job_tracking_id'] : (!empty($data['job_tracking_id']) ? $data['job_tracking_id'] : false);
                    $job_data = $this->ssid_common->_filter_data('job', $data);

                    $this->db->insert('job', $job_data);

                    if ($this->db->trans_status() !== false) {
                        $job_id = $this->db->insert_id();
                        $result = $this->get_jobs($account_id, $job_id);
                        ## Update diary slots
                        if (!empty($result->assigned_to) && !empty($data['job_date'])) {
                            $this->consume_slots($account_id, $result->assigned_to, [ 'action'=>'add', 'slots'=>$slots, 'job_date'=>$data['job_date'] ]);
                        }

                        ## Create Comms Log
                        $site_id 						= !empty($data['site_id']) ? $data['site_id'] : null;
                        $customer_id 					= !empty($data['customer_id']) ? $data['customer_id'] : null;
                        $data['job_notes'] 				= (!empty($data['job_notes'])) ? $data['job_notes'] : 'System Note: Job created successfully';
                        $data['current_tracking_id']	= $data['job_tracking_id'];
                        $data['previous_tracking_id']	= null;
                        $data['previous_status_id'] 	= null;
                        $data['current_status_id'] 		= $data['status_id'];
                        $this->create_communications_log($account_id, $site_id, $customer_id, $job_id, $data);

                        $job_data['job_id'] = $job_id;
                        $job_data			=  array_merge($job_data, (array) $job_type_check);

                        ## Send email Notification
                        if (!empty($job_type_check->notification_required) && !empty($job_type_check->notification_emails)) {
                            $created_by			= '';
                            $created_by			.= !empty($this->ion_auth->_current_user->first_name) ? $this->ion_auth->_current_user->first_name : '';
                            $created_by			.= !empty($this->ion_auth->_current_user->last_name) ? ' '.$this->ion_auth->_current_user->last_name : '';

                            $job_data['job_status'] 	= ($job_data['status_id'] == 1) ? 'Assigned' : 'Un-assigned';
                            $job_data['created_by'] 	= !empty($created_by) ? ucwords(strtolower($created_by)) : 'Unknown User ('.$job_data['created_by'].')';

                            $site_id  = !empty($job_data['site_id']) ? $job_data['site_id'] : false;
                            if (empty($site_id)) {
                                $asset_id   = !empty($job_data['asset_id']) ? $job_data['asset_id'] : false;
                                $asset_data  = $this->db->select('asset_id, asset_unique_id, site_id', false)
                                    ->group_by('asset.site_id')
                                    ->limit(1)
                                    ->get_where('asset', [ 'account_id'=>$account_id, 'asset_id' => $asset_id ])
                                    ->row();
                                $site_id = !empty($asset_data->site_id) ? $asset_data->site_id : false;
                            }

                            $site_data	= $this->db->select('site_id, site_name, site_reference, UPPER(site_postcodes) `site_postcodes`, site_address_id, CONCAT( TRIM(site_name),", ",TRIM( UPPER(site_postcodes) ) ) `site_summaryline`, site_contact_email', false)
                                ->get_where('site', [ 'account_id'=>$account_id, 'site_id' => $site_id ])
                                ->row();

                            $regional_emails 		= $this->db->get_where('diary_regions', [ 'account_id'=> $account_id, 'region_id'=> $data['region_id'] ])->row();
                            $regional_notify_emails = !empty($regional_emails->notification_emails) ? (is_json($regional_emails->notification_emails) ? json_decode($regional_emails->notification_emails) : $regional_emails->notification_emails) : [];
                            $building_manager_emails= !empty($site_data->site_contact_email) ? [$site_data->site_contact_email] : [];

                            $notification_data['site_data'] 	= !empty($site_data) ? $site_data : null;
                            $notification_data['asset_data'] 	= !empty($asset_data) ? $asset_data : null;
                            $notification_data['jobs_data'] 	= [ (object) $job_data ];
                            $destination 						= (!empty($regional_notify_emails)) ? array_merge($job_type_check->notification_emails, $regional_notify_emails) : $job_type_check->notification_emails;
                            $destination 						= (!empty($building_manager_emails)) ? array_merge($destination, $building_manager_emails) : $destination;
                            $notification_data['destination']	= (!empty($destination) && is_array($destination)) ? array_map('trim', $destination) : $destination;
                            $this->send_new_job_notification($account_id, $notification_data);
                        }

                        ## Notify Engineer
                        if (!empty($notify_engineer) && !empty($job_type_check->notify_engineer)) {
                            $job_data['job_status'] 	= 'Assigned';
                            if (!empty($site_data)) {
                                $job_data 	= array_merge($job_data, (array)$site_data);
                            }
                            $this->send_engineer_notification($account_id, $data['assigned_to'], [ (object)$job_data ]);
                        }

                        ## Upload any attached Files
                        if (!empty($_FILES['user_files']['name'])) {
                            $data['job_id']  = $job_id;
                            $folder			 = !empty($data['doc_type']) ? $data['doc_type'] : 'others';
                            $uploaded_docs 	 = $this->document_service->upload_files($account_id, $job_data, $doc_group = 'job', $folder);
                        }

                        #$this->diary_date_service->update_slots( $account_id, $result->job_date, $slots , 'add' );
                        $this->session->set_flashdata('message', 'Job record created successfully.');
                    }
                } else {
                    ## Here we should reduce the slots then add the new ones if the slots have been updated. provided the date hasn't changed
                    if (($slots != $job_exists->job_duration) && ($date['job_date'] == $job_exists->job_date)) {
                        $this->diary_date_service->update_slots($account_id, $job_exists->job_date, $job_exists->job_duration, 'sub');
                        #$this->diary_date_service->update_slots( $account_id, $job_exists->job_date , $slots , 'add' );
                    }

                    $data['last_modified_by'] = $this->ion_auth->_current_user->id;
                    $update_data = $this->ssid_common->_filter_data('job', $data);

                    $this->db->where('job_id', $job_exists->job_id);
                    $this->db->update('job', $update_data);
                    if ($this->db->trans_status() !== false) {
                        $result = $this->get_jobs($account_id, $job_exists->job_id);
                        $this->session->set_flashdata('message', 'Job record updated successfully.');
                    }
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Job data supplied.');
        }
        return $result;
    }

    /*
    * Update Job record
    */
    public function update_job($account_id = false, $job_id = false, $job_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($job_id) && !empty($job_data)) {
            $data = [];

            $consumed_items = (isset($job_data['consumed_items']) && !empty($job_data['consumed_items'])) ? $job_data['consumed_items'] : null;
            unset($job_data['consumed_items']);

            $data = $this->ssid_common->_data_prepare($job_data);

            if (!empty($data)) {
                if (!empty($data['status_group'])) {
                    $status_grp 		= $this->_resolve_status_group(false, lean_string($data['status_group']));
                    $status_grp 		= $status_grp[ lean_string($data['status_group']) ];
                    $data['status_id'] 	= $status_grp->status_id;
                }

                if (!empty($data['job_type_id'])) {
                    $job_type_details = $this->_validate_job_type($account_id, $data['job_type_id']);
                    if (!$job_type_details) {
                        $this->session->set_flashdata('message', 'This Job Type record does not exist or does not belong to you.');
                        return false;
                    }
                } else {
                    /* Commented out temporarily */
                    #$this->session->set_flashdata( 'message','Job Type field is required.' );
                    #return false;
                }

                ## Process Signatures
                if (!empty($_FILES['signatures']['name'])) {
                    $data['upload_segment'] = 'Signature';
                    $uploaded_docs = $this->document_service->process_signatures($account_id, $data, $doc_group = 'job');
                    unset($_FILES);
                }

                ## Process File Attachments
                if (!empty($_FILES['attachments']['name'])) {
                    $data['upload_segment'] = 'Attachment';
                    $uploaded_docs = $this->document_service->process_attachments($account_id, $data, $doc_group = 'job');
                    unset($_FILES);
                }

                ## Reduce the slots then add the new allocation if the slots have been updated. provided the date hasn't changed
                $slots 			= (!empty($data['job_duration'])) ? $data['job_duration'] : null;
                $job_exists 	= $this->db->select('job.*, job_types.job_type', false)
                    ->join('job_types', 'job_types.job_type_id = job.job_type_id', 'left')
                    ->where('job.archived !=', 1)
                    ->get_where('job', ['job.account_id'=>$data['account_id'],'job_id'=>$data['job_id']])
                    ->row();

                $job_date				= !empty($data['job_date']) ? $data['job_date'] : false;
                $tracking_status_id 	= !empty($data['job_tracking_id']) ? $data['job_tracking_id'] : false;
                $new_job_status 		= $this->db->get_where('job_statuses', [ 'status_id'=>$data['status_id'] ])->row();
                $new_tracking_status 	= $this->db->get_where('job_tracking_statuses', [ 'job_tracking_id'=>$tracking_status_id ])->row();

                if (!empty($slots) && ($slots != $job_exists->job_duration) && (date('Y-m-d', strtotime($data['job_date'])) == date('Y-m-d', strtotime($job_exists->job_date)))) {
                    #$this->diary_date_service->update_slots( $account_id, $job_exists->job_date , $job_exists->job_duration , 'sub' );
                    #$this->diary_date_service->update_slots( $account_id, $job_exists->job_date , $slots , 'add' );
                    ## Update diary slots
                    if (!empty($job_exists->assigned_to)) {
                        $this->consume_slots($account_id, $job_exists->assigned_to, [ 'action'=>'sub', 'slots'=>$job_exists->job_duration, 'job_date'=>$data['job_date'] ]);
                        $this->consume_slots($account_id, $job_exists->assigned_to, [ 'action'=>'add', 'slots'=>$slots, 'job_date'=>$data['job_date'] ]);
                    }
                }

                ## If Failing or Cancelling Job
                if (in_array($data['status_id'], [5,6])) {
                    $tracking_status = $this->fetch_tracking_status($account_id, (object)['job_date'=>$job_date, 'job_status_group'=>$new_job_status->status_group]);
                    $data['job_tracking_id'] = !empty($tracking_status['job_tracking_id']) ? $tracking_status['job_tracking_id'] : $data['job_tracking_id'];
                } else {
                    ## Set Job as UnAssign Job if status not set
                    if (empty($job_exists->status_id) || $job_exists->status_id == 2 || $data['status_id'] == 2) {
                        #$assigned_status 	= $this->db->get_where( 'job_statuses', [ 'status_group'=>'assigned' ] )->row();
                        $data['status_id'] 	= 2;
                    }

                    $notify_engineer = false;
                    ## Assign Job if it wasn't assigned
                    if (!empty($data['assigned_to']) && $job_exists->status_id == 2) {
                        $data['status_id'] 	= 1;
                        $notify_engineer 	= true;
                    }

                    ## Notify Engineer
                    if ($data['status_id'] != $job_exists->status_id) {
                        if (($data['status_id'] == 1) && !empty($data['assigned_to'])) {
                            $notify_engineer = true;
                        }
                    }
                }

                ## Status has likely changed
                $new_job_status = $this->db->get_where('job_statuses', [ 'status_id'=>$data['status_id'] ])->row();

                ## Assign Region
                if (empty($data['region_id'])) {
                    if (!empty($data['address_id'])) {
                        if (!empty($data['region_id']) && ($data['region_id'] != $job_exists->region_id)) {
                            //Use the new one (admin override)
                        } else {
                            $address_record = $this->db->select('postcode, postcode_district, postcode_area', false)->get_where('addresses', [ 'main_address_id'=>$data['address_id'] ])->row();
                            if (!empty($address_record)) {
                                $region = $this->db->select('region_id, postcode_district')
                                    ->where('diary_region_postcodes.account_id', $account_id)
                                    ->where('diary_region_postcodes.postcode_district', $address_record->postcode_district)
                                    ->group_by('diary_region_postcodes.region_id')
                                    ->limit(1)
                                    ->get('diary_region_postcodes')
                                    ->row();
                                if (!empty($region)) {
                                    $data['region_id'] = $region->region_id;
                                }
                            }
                        }
                    }
                }

                ## Check for Job completion
                #if( !empty( $data['status_id'] ) && ( $job_exists->status_id  != $data['status_id'] ) ){
                $completion_data = $this->check_job_completion($account_id, $job_id, $data);
                #}

                ## Prevent overriding the Start Time
                if (!empty($completion_data['start_time']) && !empty($job_exists->start_time)) {
                    unset($completion_data['start_time']);
                }

                if (!empty($completion_data) && is_array($completion_data)) {
                    $data = array_merge($data, $completion_data);
                }

                ## Update Tracking Status

                if (!empty($data['job_tracking_id']) && ($job_exists->job_tracking_id  != $data['job_tracking_id'])) {
                    //Do nothing and give precedence to manually changed Tracking Status
                } else {
                    $tracking_status_id 	= !empty($data['job_tracking_id']) ? $data['job_tracking_id'] : false;
                    #if( !empty( $data['status_id'] ) && ( $job_exists->status_id  != $data['status_id'] ) ){
                    $tracking_status_record = !empty($new_tracking_status->job_tracking_group) ? $new_tracking_status->job_tracking_group : false;
                    $tracking_status = $this->fetch_tracking_status($account_id, (object)['job_date'=>$job_date, 'job_status_group'=>$new_job_status->status_group], $tracking_status_record);
                    $data['job_tracking_id'] = !empty($tracking_status['job_tracking_id']) ? $tracking_status['job_tracking_id'] : $tracking_status_id;
                    #}
                }

                $data['last_modified_by'] = $this->ion_auth->_current_user->id;
                $update_data = $this->ssid_common->_filter_data('job', $data);

                $this->db->where('job_id', $job_id)->update('job', $update_data);

                ## Trigger Tesseract Actions
                $new_job_status_group = !empty($data['status_group']) ? trim($data['status_group']) : $new_job_status->status_group;
                if (in_array(strtolower(trim($new_job_status_group)), $this->tesseract_linked_statuses)) {
                    $data['status_group'] = $new_job_status_group;
                    $this->_trigger_tesseract_actions($account_id, $job_id, $data);
                }

                if ($this->db->trans_status() !== false) {
                    ## Log Job Type Change
                    if (!empty($data['job_type_id']) && ($job_exists->job_type_id  != $data['job_type_id'])) {
                        $notes				= $data['job_notes'];
                        $data['job_notes']	= 'Job Type changed: <br/>From: <strong>'.$job_exists->job_type.'</strong> <br/>To: <strong>'.$job_type_details->job_type.'</strong>. '.$notes;
                    }

                    ## Save notes in the communications log
                    if (!empty($data['job_date']) && ($job_exists->job_date  != date('Y-m-d', strtotime($data['job_date'])))) {
                        $notes				= !empty($data['job_notes']) ? $data['job_notes'] : '';
                        $data['job_notes']	= 'Job Date changed from <strong>'.date("d-m-Y", strtotime($job_exists->job_date)).'</strong> to <strong>'.date("d-m-Y", strtotime($data['job_date'])).'</strong>. '.$notes;
                    }

                    ## Log Region Change
                    if (!empty($data['region_id']) && ($job_exists->region_id  != $data['region_id'])) {
                        $old_region = !empty($job_exists->region_id) ? $this->db->get_where('diary_regions', ['diary_regions.region_id'=>$job_exists->region_id])->row() : false;

                        $new_region = !empty($data['region_id']) ? $this->db->get_where('diary_regions', ['diary_regions.region_id'=>$data['region_id']])->row() : false;

                        $notes				= $data['job_notes'];
                        $data['job_notes']	= 'Region changed from <br/><strong>'.(!empty($old_region->region_name) ? $old_region->region_name : 'NOT-SET').'</strong> to <strong>'.(!empty($new_region->region_name) ? $new_region->region_name : 'NOT-SET').'</strong>. '.$notes;
                    }

                    $old_status 			= $this->db->get_where('job_statuses', [ 'status_id'=>$job_exists->status_id ])->row();
                    $old_tracking_status 	= $this->db->get_where('job_tracking_statuses', [ 'job_tracking_id'=>$job_exists->job_tracking_id ])->row();

                    $data['job_notes'] 		= (!empty($data['job_notes'])) ? $data['job_notes'] : 'System Note: Status changed from <em>'.$old_status->job_status.'</em> to <em>'.$new_job_status->job_status.'</em>';

                    $site_id 	 = (!empty($data['site_id'])) ? $data['site_id'] : null;
                    $customer_id = (!empty($data['customer_id'])) ? $data['customer_id'] : null;

                    if (isset($data['job_tracking_id'])) {
                        $new_tracking_status 			= $this->db->get_where('job_tracking_statuses', [ 'job_tracking_id'=>$data['job_tracking_id'] ])->row();

                        $new_tracking_status 			= $this->db->get_where('job_tracking_statuses', [ 'job_tracking_id'=>$data['job_tracking_id'] ])->row();

                        if (!empty($new_tracking_status)) {
                            if (!empty($new_tracking_status->job_tracking_id) && ($job_exists->job_tracking_id  != $new_tracking_status->job_tracking_id)) {
                                if ($new_tracking_status->job_tracking_group == 'jobinvoiced') {
                                    $this->quick_update($account_id, $job_id, [ 'invoice_date'=>date('Y-m-d H:i:s') ]);
                                }
                            }

                            $data['current_tracking_id']	= !empty($new_tracking_status->job_tracking_id) ? $new_tracking_status->job_tracking_id : $data['job_tracking_id'];
                            $data['previous_tracking_id']	= !empty($old_tracking_status->job_tracking_id) ? $old_tracking_status->job_tracking_id : $data['current_tracking_id'];

                            ## Update confirmation station for consumed_status
                            if (!empty($data['job_tracking_id']) && ($job_exists->job_tracking_id  != $data['job_tracking_id'])) {
                                $this->update_consumed_items_status($account_id, $job_id, $new_tracking_status->job_tracking_status);
                            }
                        }
                    }

                    $data['previous_status_id'] 	= $job_exists->status_id;
                    $data['current_status_id'] 		= $data['status_id'];
                    $this->create_communications_log($account_id, $site_id, $customer_id, $job_id, $data);

                    ## Check and consume stock/labour items
                    if (!empty($consumed_items)) {
                        $this->process_confirmed_job_items($account_id, $job_id, $consumed_items);
                    }

                    ## Update Activities status
                    $this->update_activity_completion_status($account_id, $data);

                    ## Send Engineer Notification
                    $update_data	=  array_merge($update_data, (array) $job_type_details);

                    if (!empty($notify_engineer) && !empty($job_type_details->notify_engineer)) {
                        $update_data['job_id'] 		= $job_id;
                        $update_data['job_status'] 	= 'Assigned';

                        $site_data	= $this->db->select('site_id, site_name, site_reference, UPPER(site_postcodes) `site_postcodes`, site_address_id, CONCAT( TRIM(site_name),", ",TRIM( UPPER(site_postcodes) ) ) `site_summaryline`', false)
                            ->get_where('site', [ 'account_id'=>$account_id, 'site_id' => $job_exists->site_id ])
                            ->row();
                        if (!empty($site_data)) {
                            $update_data = array_merge($update_data, (array)$site_data);
                        }

                        $this->send_engineer_notification($account_id, $update_data['assigned_to'], [ (object)$update_data ]);
                    }

                    $result = $this->get_jobs($account_id, $job_id);
                    $this->session->set_flashdata('message', 'Job record updated successfully.');
                } else {
                    $this->session->set_flashdata('message', 'There was an Error while trying to upate the Job record.');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Job data supplied.');
        }
        return $result;
    }

    /** Check Job Completion **/
    private function check_job_completion($account_id = false, $job_id = false, $data = false)
    {
        $result = null;
        if (!empty($account_id) && !empty($job_id) && !empty($data)) {
            if (!empty($data['status_id'])) {
                $job_statuses 	= $this->get_job_statuses(false, $data['status_id']);
            } else {
                $job_statuses 	= $this->_resolve_status_group(false, lean_string($data['status_group']));
                $job_statuses 	= $job_statuses[ lean_string($data['status_group']) ];
            }

            $completion_status 	= [];

            if (!empty($job_statuses->status_group)) {
                $completion_status['status_id'] = $job_statuses->status_id;
                $status_group 					= strtolower($job_statuses->status_group);
                switch($status_group) {
                    ## Successful Jobs
                    case ($status_group == 'successful'):

                        $completion_status['finish_time'] 		  = _datetime();
                        $completion_status['finish_gps_latitude'] = !empty($data['finish_gps_latitude']) ? $data['finish_gps_latitude'] : null;
                        $completion_status['finish_gps_longitude']= !empty($data['finish_gps_longitude']) ? $data['finish_gps_longitude'] : null;

                        break;

                        ## Jobs In Progress
                    case ($status_group == 'inprogress'):
                        #$completion_status['start_time'] = date( 'Y-m-d H:i:s' );
                        $completion_status['start_time'] = _datetime();
                        break;

                        ## Failed Jobs
                    case ($status_group == 'failed'):
                        $completion_status['finish_time'] 			= _datetime();
                        $completion_status['finish_gps_latitude'] 	= !empty($data['finish_gps_latitude']) ? $data['finish_gps_latitude'] : null;
                        $completion_status['finish_gps_longitude']	= !empty($data['finish_gps_longitude']) ? $data['finish_gps_longitude'] : null;
                        break;

                        ## Enroute Jobs
                    case ($status_group == 'enroute'):
                        $completion_status['dispatch_time'] = _datetime();
                        break;

                        ## On-site Jobs
                    case ($status_group == 'onsite'):

                        $completion_status['on_site_time'] = _datetime();

                        break;

                        ## Onhold Jobs
                    case ($status_group == 'onhold'):
                        if (!empty($data['fsr_start_date'])) {
                            $completion_status['fsr_start_date'] 	= datetime_to_iso8601($data['fsr_start_date']);
                        }
                        if (!empty($data['fsr_complete_date'])) {
                            $completion_status['fsr_complete_date'] = datetime_to_iso8601($data['fsr_complete_date']);
                        }
                        break;

                        ## Assigned
                    case ($status_group == 'assigned'):

                        $completion_status['start_time'] 				=  null;
                        $completion_status['finish_time']				=  null;
                        $completion_status['on_site_time']				=  null;
                        $completion_status['dispatch_time']				=  null;
                        $completion_status['fsr_start_date']			=  null;
                        $completion_status['fsr_complete_date']			=  null;
                        $completion_status['external_job_call_status']	=  null;
                        $completion_status['symptom_code']				=  null;
                        $completion_status['fault_code']				=  null;
                        $completion_status['repair_code']				=  null;

                        break;

                        ## Default
                    default:
                    case ($status_group == 'unassigned'):
                    case ($status_group == 'cancelled'):
                        //Do nothing for now
                        break;
                }
            }

            $result = $completion_status;
        }
        return $result;
    }

    /*
    * Delete Job record
    */
    public function delete_job($account_id = false, $job_id = false)
    {
        $result = false;
        if ($account_id && $job_id) {
            $job_exists = $this->db->get_where('job', ['job_id'=>$job_id])->row();

            if (!empty($job_exists)) {
                $data = [
                    'archived'=>1,
                    'archived_on'=> _datetime(),
                    'archived_by'=>$this->ion_auth->_current_user->id
                ];
                $this->db->where('job_id', $job_id)
                    ->update('job', $data);
                if ($this->db->trans_status() !== false) {
                    $this->session->set_flashdata('message', 'Record deleted successfully.');
                    $result = true;
                }
            } else {
                $this->session->set_flashdata('message', 'Invalid Job ID.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Job ID supplied.');
        }
        return $result;
    }

    /**
    * Consume items used on a Job
    */
    public function process_confirmed_job_items($account_id, $job_id, $items_data)
    {
        $result = false;
        if ($account_id && $job_id && $items_data) {
            foreach ($items_data as $item_code=>$qty) {
                $item_details = $this->billable_item_service->get_billable_items(false, $item_code);
                if (!empty($item_details)) {
                    $item_data = [
                        'item_code' => $item_code,
                        'item_qty' => $qty,
                        'job_id' => $job_id,
                        'account_id' => $account_id
                    ];

                    $where = array('item_code'=>$item_code,'job_id'=>$job_id);
                    $check_exists = $this->db->get_where('job_consumed_items', $where)->row();
                    if (!empty($check_exists)) {
                        #$item_data['item_qty'] += $check_exists->item_qty;
                        $item_data['item_qty'] = (int)$item_data['item_qty'];
                        if (!$item_data['item_qty']) {
                            $this->db->where($where);
                            $this->db->delete('job_consumed_items');
                            $result = true;
                        } else {
                            $this->db->where($where);
                            $this->db->update('job_consumed_items', $item_data);
                            if ($this->db->trans_status() !== false) {
                                $this->session->set_flashdata('message', 'Job item updated successfully.');
                                $result = true;
                            }
                        }
                    } else {
                        if ($item_data['item_qty'] > 0) {
                            $this->db->insert('job_consumed_items', $item_data);
                            if ($this->db->trans_status() !== false) {
                                $this->session->set_flashdata('message', 'Job item created successfully.');
                                $result = true;
                            }
                        }
                    }
                } else {
                    $this->session->set_flashdata('message', 'Item not found in the database.');
                }
            }
        }
        return $result;
    }

    /*
    * Create a communication log, most recent 20 entries
    */
    public function create_communications_log($account_id=false, $site_id = false, $customer_id = false, $job_id=false, $log_data =false, $offset=DEFAULT_OFFSET, $limit = DEFAULT_LIMIT)
    {
        $result = false;
        if (!empty($account_id) && !empty($log_data)) {
            $data = [];
            foreach ($log_data as $col => $value) {
                $data[$col] = $value;
            }

            if (!empty($data)) {
                $data['account_id' ]	= $account_id;
                $data['site_id']		= (!empty($site_id)) ? $site_id : (!empty($data['site_id']) ? $data['site_id'] : null);
                $data['customer_id']	= (!empty($customer_id)) ? $customer_id : (!empty($data['customer_id']) ? $data['customer_id'] : null);
                $data['job_id']			= (!empty($job_id)) ? $job_id : (!empty($data['job_id']) ? $data['job_id'] : null);
                $data['notes']			= (!empty($data['job_notes']) ? $data['job_notes'] : (!empty($data['notes']) ? $data['notes'] : ''));
                $data['logged_by']		= $this->ion_auth->_current_user()->id;

                $data = $this->ssid_common->_filter_data('communication_logs', $data);

                $this->db->insert('communication_logs', $data);
            }

            if ($this->db->trans_status() !== false) {
                $this->session->set_flashdata('message', 'Log created successfully.');
                $result = true;
            }
        }
        return $result;
    }

    /*
    * Get a list of communication logs, most recent 20 entries or by supplied limit
    */
    public function get_communication_logs($account_id=false, $site_id = false, $customer_id = false, $job_id=false, $notes=false, $limit = DEFAULT_LIMIT, $offset=DEFAULT_OFFSET)
    {
        $result = null;
        if (!empty($account_id)) {
            $this->db->select('cl.*, current_status.job_status as current_status, previous_status.job_status as previous_status, current_tracking_status.job_tracking_status as current_tracking_status, previous_tracking_status.job_tracking_status as previous_tracking_status, CONCAT(user.first_name," ",user.last_name) `logged_by`, customer.customer_id, CONCAT(customer.customer_first_name," ",customer.customer_last_name) `customer_name`, site.site_id, site.site_name', false);
            $this->db->join('user', 'user.id = cl.logged_by', 'left');
            $this->db->join('customer', 'customer.customer_id = cl.customer_id', 'left');
            $this->db->join('site', 'site.site_id = cl.site_id', 'left');
            $this->db->join('job_statuses current_status', 'current_status.status_id = cl.current_status_id', 'left');
            $this->db->join('job_statuses previous_status', 'previous_status.status_id = cl.previous_status_id', 'left');
            $this->db->join('job_tracking_statuses current_tracking_status', 'current_tracking_status.job_tracking_id = cl.current_tracking_id', 'left');
            $this->db->join('job_tracking_statuses previous_tracking_status', 'previous_tracking_status.job_tracking_id = cl.previous_tracking_id', 'left');

            $this->db->where('cl.account_id', $account_id);
            if ($site_id) {
                $this->db->where('cl.site_id', $site_id);
            }

            if ($customer_id) {
                $this->db->where('cl.customer_id', $customer_id);
            }

            if ($job_id) {
                $this->db->where('cl.job_id', $job_id);
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            ## $query = $this->db->order_by( 'cl.account_id, cl.customer_id, log_id desc, cl.job_id' )
            $query = $this->db->order_by('cl.logged_date DESC')
                ->get('communication_logs cl');
            if ($query->num_rows() > 0) {
                $this->session->set_flashdata('message', 'Log records found!');
                $result = $query->result();
            }
        }
        return $result;
    }

    /*
    * Get Jobs statistics
    */
    public function get_job_statistics($account_id=false, $where = false)
    {
        $result = false;


        $statuses = $this->db->select('status_id, job_status, status_group', false)
            ->order_by('job_status')
            ->get_where('job_statuses', [ 'is_active'=> 1 ]);

        if ($statuses->num_rows() > 0) {
            $where 	= convert_to_array($where);

            if (!empty($where['include_slots'])) {
                $include_slots = true;
            }

            if (!empty($where['include_details'])) {
                $include_details = true;
            }

            $SQL 	= '';
            foreach ($statuses->result()  as $k => $row) {
                switch(strtolower($row->status_group)) {
                    case 'assigned':
                        $SQL .= ' SUM( CASE WHEN status_group = "assigned"  THEN 1 ELSE 0 END ) AS `assigned`,';
                        break;

                    case 'failed':
                        $SQL .= ' SUM(CASE WHEN status_group = "failed" THEN 1 ELSE 0 END) AS `failed`,';
                        break;

                    case 'cancelled':
                        $SQL .= ' SUM(CASE WHEN status_group = "cancelled" THEN 1 ELSE 0 END) AS `cancelled`,';
                        break;

                    case 'inprogress':
                        $SQL .= ' SUM(CASE WHEN status_group = "inprogress" THEN 1 ELSE 0 END) AS `inprogress`,';
                        break;

                    case 'successful':
                        $SQL .= ' SUM( CASE WHEN status_group = "successful"  THEN 1 ELSE 0 END ) AS `successful`,';
                        break;

                    case 'enroute':
                        $SQL .= ' SUM( CASE WHEN status_group = "enroute"  THEN 1 ELSE 0 END ) AS `enroute`,';
                        break;

                    case 'onsite':
                        $SQL .= ' SUM( CASE WHEN status_group = "onsite"  THEN 1 ELSE 0 END ) AS `onsite`,';
                        break;

                    case 'unassigned':
                        $SQL .= ' SUM( CASE WHEN status_group = "unassigned"  THEN 1 ELSE 0 END ) AS `un_assigned`,';
                        break;
                }
            }

            if (!empty($include_slots)) {
                $SQL .= ' SUM( CASE WHEN job_duration > 0 THEN job_duration ELSE 0 END ) AS `total_slots`, ';
            }

            if (!empty($SQL)) {
                $SQL .= ' SUM( CASE WHEN job_id > 0 THEN 1 ELSE 0 END ) AS `total_jobs`';
            }
        }

        if (!empty($SQL)) {
            $this->db->select($SQL, false);
        }

        if (!empty($where)) {
            $where = convert_to_array($where);

            if (!empty($where['site_id'])) {
                $this->db->where('job.site_id', $where['site_id']);
            }

            if (!empty($where['customer_id'])) {
                $this->db->where('job.customer_id', $where['customer_id']);
            }

            if (!empty($where['assigned_to'])) {
                $this->db->where('job.assigned_to', $where['assigned_to']);
            }

            if (!empty($where['date_from'])) {
                $date_from 	= date('Y-m-d', strtotime($where['date_from']));
                $date_to 	= (!empty($where['date_to'])) ? date('Y-m-d', strtotime($where['date_to'])) : date('Y-m-d');
                $this->db->where('job_date >=', $date_from);
                $this->db->where('job_date <=', $date_to);
            } elseif (!empty($where['job_date'])) {
                $job_date = date('Y-m-d', strtotime($where['job_date']));
                $this->db->where('job_date', $job_date);
            }
        }

        $job = $this->db->join('job_statuses', 'job_statuses.status_id = job.status_id', 'left')
            ->order_by('job_statuses.job_status')
            ->get('job');
        if ($job->num_rows() > 0) {
            $this->session->set_flashdata('message', 'Job stats found');
            $result = $job->result()[0];
        } else {
            $this->session->set_flashdata('message', 'Job stats not available');
        }
        return $result;
    }

    /** Get Job Type Setup **/
    public function get_job_type_setup($account_id = false, $job_type_id = false, $hide_pay_info = false)
    {
        $result = null;
        if (!empty($account_id) && !empty($job_type_id)) {
            $query = $this->db->select('id,account_id,job_type_id,method_statement,pay_rate,nps_required,csat_required,doc_upload_required,doc_upload_details', false)
                ->where('account_id', $account_id)
                ->where('job_type_id', $job_type_id)
                ->where('is_active', 1)
                ->get('job_type_setup');

            if ($query->num_rows() > 0) {
                $result = $query->result()[0];
                if ($hide_pay_info) {
                    unset($result->pay_rate);
                }
            }
        }
        return $result;
    }


    /** Create a new Job Type record **/
    public function create_job_type($account_id = false, $job_type_data = false)
    {
        $result = null;

        if (!empty($account_id) && !empty($job_type_data)) {
            ##Extract out associate risks if they have been passed
            $associated_risks 	= !empty($job_type_data['associated_risks']) ? $job_type_data['associated_risks'] : false;
            $required_boms 		= !empty($job_type_data['required_boms']) ? $job_type_data['required_boms'] : false;
            $required_skills	= !empty($job_type_data['required_skills']) ? $job_type_data['required_skills'] : false;
            $assigned_regions	= !empty($job_type_data['assigned_regions']) ? $job_type_data['assigned_regions'] : false;
            $required_checklists= !empty($job_type_data['required_checklists']) ? $job_type_data['required_checklists'] : false;

            unset($job_type_data['associated_risks'], $job_type_data['required_skills'], $job_type_data['assigned_regions'], $job_type_data['required_boms'], $job_type_data['required_checklists']);
            $data = [];
            foreach ($job_type_data as $col => $value) {
                if ($col == 'job_type') {
                    $data['job_type_ref'] = $this->generate_job_type_ref($account_id, $job_type_data);
                    $data['job_group'] 	  = ucwords(strtolower($value));
                }

                if ($col == 'notification_emails') {
                    $value = json_encode(array_map('trim', array_filter(explode(',', $value))));
                }

                $data[$col] = $value;
            }

            $category_id 	= !empty($data['category_id']) ? $data['category_id'] : false;
            $discipline_id 	= !empty($data['discipline_id']) ? $data['discipline_id'] : false;

            if (!empty($data['override_existing']) && !empty($data['job_type_id'])) {
                $override_existing = true;
                //User said override the current record
                $check_exists = $this->db->select('job_types.*, audit_categories.category_name', false)
                    ->join('audit_categories', 'audit_categories.category_id = job_types.category_id', 'left')
                    ->where('job_types.account_id', $account_id)
                    ->where('job_types.job_type_id', $data['job_type_id'])
                    ->get('job_types')->row();
            } else {
                if (!empty($category_id)) {
                    $this->db->where('job_types.category_id', $category_id);
                }

                if (!empty($discipline_id)) {
                    $this->db->where('job_types.discipline_id', $discipline_id);
                }

                unset($data['job_type_id']);
                $check_exists = $this->db->select('job_types.*, audit_categories.category_name', false)
                    ->join('audit_categories', 'audit_categories.category_id = job_types.category_id', 'left')
                    ->where('job_types.account_id', $account_id)
                    ->where('( job_types.job_type = "'.$data['job_type'].'" OR job_types.job_type_ref = "'.$data['job_type_ref'].'" )')
                    ->limit(1)
                    ->get('job_types')
                    ->row();
            }

            $data = $this->ssid_common->_filter_data('job_types', $data);

            if (!empty($check_exists)) {
                if (!empty($override_existing)) {
                    $data['last_modified_by'] = $this->ion_auth->_current_user->id;
                    $this->db->where('job_type_id', $check_exists->job_type_id)
                        ->update('job_types', $data);

                    ## Add/update associated-risks
                    if (!empty($associated_risks)) {
                        $this->add_associated_risks($account_id, $check_exists->job_type_id, ['associated_risks'=>$associated_risks]);
                    }

                    ## Add Skills required to completed this Job Type
                    if (!empty($required_skills)) {
                        $this->add_required_skills($account_id, $check_exists->job_type_id, ['required_skills'=>$required_skills]);
                    }

                    ## Add/update Required BOMs
                    if (!empty($required_boms)) {
                        $this->add_required_boms($account_id, $check_exists->job_type_id, ['required_boms'=>$required_boms]);
                    }

                    ## Add/update Required Checklists
                    if (!empty($required_checklists)) {
                        $this->add_required_checklists($account_id, $check_exists->job_type_id, ['required_checklists'=>$required_checklists ]);
                    }

                    $this->session->set_flashdata('message', 'This Job Type already exists, record has been updated successfully.');
                    $result = $check_exists;
                } else {
                    $this->session->set_flashdata('message', 'This Job Type already exists, Would you like to override it?');
                    $this->session->set_flashdata('already_exists', 'True');
                    $result = $check_exists;
                }
            } else {
                $data['created_by'] = $this->ion_auth->_current_user->id;
                $this->db->insert('job_types', $data);
                $data['job_type_id'] = $this->db->insert_id();

                ## Add associated-risks
                if (!empty($associated_risks)) {
                    $this->add_associated_risks($account_id, $data['job_type_id'], ['associated_risks'=>$associated_risks]);
                }

                ## Add Skills required to completed this Job Type
                if (!empty($required_skills)) {
                    $this->add_required_skills($account_id, $data['job_type_id'], ['required_skills'=>$required_skills]);
                }

                ## Add/update Required BOMs
                if (!empty($required_boms)) {
                    $this->add_required_boms($account_id, $data['job_type_id'], ['required_boms'=>$required_boms]);
                }

                ## Add/update Required Checklists
                if (!empty($required_checklists)) {
                    $this->add_required_checklists($account_id, $data['job_type_id'], ['required_checklists'=>$required_checklists ]);
                }

                $this->session->set_flashdata('message', 'New Job Type created successfully.');
                $result = $data;
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }

        return $result;
    }

    /** Update an existing Job Type **/
    public function update_job_type($account_id = false, $job_type_id = false, $update_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($job_type_id)  && !empty($update_data)) {
            $ref_condition = [ 'account_id'=>$account_id, 'job_type_id'=>$job_type_id ];
            $update_data   = $this->ssid_common->_data_prepare($update_data);
            $update_data   = $this->ssid_common->_filter_data('job_types', $update_data);

            if (!empty($update_data['notification_emails'])) {
                #$update_data['notification_emails'] = json_encode( array_filter( explode( ',', $update_data['notification_emails'] ) ) );
                $update_data['notification_emails'] = json_encode(array_map('trim', array_filter(explode(',', $update_data['notification_emails']))));
            }

            $record_pre_update = $this->db->get_where('job_types', [ 'account_id'=>$account_id, 'job_type_id'=>$job_type_id ])->row();

            if (!empty($record_pre_update)) {
                $category_id 	= (!empty($update_data['category_id'])) ? $update_data['category_id'] : false;
                $discipline_id 	= (!empty($update_data['discipline_id'])) ? $update_data['discipline_id'] : false;

                if (!empty($category_id)) {
                    $this->db->where('job_types.category_id', $category_id);
                }

                if (!empty($discipline_id)) {
                    $this->db->where('job_types.discipline_id', $discipline_id);
                }

                $update_data['job_type_ref'] = $this->generate_job_type_ref($account_id, $update_data);

                $job_type_where = '( job_types.job_type = "'.$update_data['job_type'].'" OR job_types.job_type_ref = "'. $update_data['job_type_ref'] .'" )';
                ;

                $check_conflict = $this->db->select('job_type_id', false)
                    ->where('job_types.account_id', $account_id)
                    ->where('job_types.job_type_id !=', $job_type_id)
                    ->where($job_type_where)
                    ->limit(1)
                    ->get('job_types')
                    ->row();

                if (!$check_conflict) {
                    $update_data['last_modified_by'] = $this->ion_auth->_current_user->id;
                    $this->db->where($ref_condition)
                        ->update('job_types', $update_data);

                    $updated_record = $this->get_job_types($account_id, $job_type_id);
                    $result 		= (!empty($updated_record->records)) ? $updated_record->records : (!empty($updated_record) ? $updated_record : false);

                    $this->session->set_flashdata('message', 'Job Type updated successfully');
                    return $result;
                } else {
                    $this->session->set_flashdata('message', 'This Job Type already exists for your account. Request aborted');
                    return false;
                }
            } else {
                $this->session->set_flashdata('message', 'This Job Type record does not exist or does not belong to you.');
                return false;
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing requireed information.');
        }
        return $result;
    }

    /*
    *	Get list of Job types list and search though it
    */
    public function get_job_types($account_id = false, $job_type_id = false, $search_term = false, $where = false, $order_by = false, $limit = 100, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            #Limit access by contract to External User Types
            if (in_array($this->ion_auth->_current_user()->user_type_id, EXTERNAL_USER_TYPES)) {
                $contract_access = $this->contract_service->get_linked_people($account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1]);
                $allowed_access  = !empty($contract_access) ? array_column($contract_access, 'contract_id') : [];
                if (!empty($allowed_access)) {
                    $this->db->where_in('job_types.contract_id', $allowed_access);
                } else {
                    $this->session->set_flashdata('message', 'No data found matching your criteria');
                    return false;
                }
            }

            $this->db->select('job_types.*, audit_types.audit_type, audit_types.audit_group, audit_categories.category_name, audit_categories.category_group, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`, account_discipline.account_discipline_name, account_discipline.account_discipline_image_url `discipline_image_url`', false)
                ->join('user creater', 'creater.id = job_types.created_by', 'left')
                ->join('user modifier', 'modifier.id = job_types.last_modified_by', 'left')
                ->join('audit_categories', 'job_types.category_id = audit_categories.category_id', 'left')
                ->join('audit_types', 'job_types.evidoc_type_id = audit_types.audit_type_id', 'left')
                ->join('account_discipline', 'job_types.discipline_id = account_discipline.discipline_id', 'left')
                ->where('job_types.is_active', 1)
                ->where('job_types.archived !=', 1)
                ->where('job_types.account_id', $account_id);

            $where = $raw_where = convert_to_array($where);

            if (!empty($job_type_id) || isset($where['job_type_id'])) {
                $job_type_id	= (!empty($job_type_id)) ? $job_type_id : $where['job_type_id'];
                if (!empty($job_type_id)) {
                    $row = $this->db->get_where('job_types', ['job_type_id'=>$job_type_id ])->row();

                    if (!empty($row)) {
                        #$row->notification_emails	= is_json( $row->notification_emails ) ? json_decode( $row->notification_emails ) : $row->notification_emails;
                        $row->notification_emails	= is_json($row->notification_emails) ? implode(', ', json_decode($row->notification_emails)) : $row->notification_emails;

                        $associated_risks  		= $this->get_associated_risks($account_id, $job_type_id);
                        $row->evidoc_details	= null;
                        $row->associated_risks  = (!empty($associated_risks)) ? $associated_risks : null;
                        if (!empty($row->evidoc_type_id)) {
                            $evidoc_details 	= $this->evidocs_service->get_evidoc_types($account_id, false, ['audit_type_id'=>$row->evidoc_type_id]);
                            $row->evidoc_details= (!empty($evidoc_details->records)) ? $evidoc_details->records : false;
                        }

                        $required_skills  		= $this->get_required_skills($account_id, $job_type_id);
                        $row->required_skills  	= (!empty($required_skills)) ? $required_skills : null;

                        $required_boms  		= $this->get_required_boms($account_id, $job_type_id);
                        $row->required_boms  	= (!empty($required_boms)) ? $required_boms : null;

                        if (!empty($row->checklists_required)) {
                            $required_checklists  		= $this->get_required_checklists($account_id, $job_type_id, $where);
                        }

                        $row->required_checklists  	= (!empty($required_checklists)) ? $required_checklists : null;

                        $result = ( object ) ['records'=>$row];
                        $this->session->set_flashdata('message', 'Job Type data found');
                        return $result;
                    } else {
                        $this->session->set_flashdata('message', 'Job Type data not found');
                        return false;
                    }
                }
                unset($where['job_type_id'], $where['job_type_ref']);
            }

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->job_types_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->job_types_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                if (isset($where['job_type'])) {
                    if (!empty($where['job_type'])) {
                        $job_type_ref = strtoupper(strip_all_whitespace($where['job_type']));
                        $this->db->where('( job_types.job_type = "'.$where['job_type'].'" OR job_types.job_type_ref = "'.$job_type_ref.'" )');
                    }
                    unset($where['job_type']);
                }

                if (isset($where['evidoc_type_id'])) {
                    if (!empty($where['evidoc_type_id'])) {
                        $evidoc_type_ids = (!is_array($where['evidoc_type_id']) && ((int) $where['evidoc_type_id'] > 0)) ? [ $where['evidoc_type_id'] ] : ((is_array($where['evidoc_type_id'])) ? $where['evidoc_type_id'] : (is_object($where['evidoc_type_id']) ? object_to_array($where['evidoc_type_id']) : []));
                        $this->db->where_in('job_types.evidoc_type_id', $evidoc_type_ids);
                    }
                    unset($where['evidoc_type_id']);
                }

                if (isset($where['contract_id'])) {
                    if (!empty($where['contract_id'])) {
                        $this->db->where('job_types.contract_id', $where['contract_id']);
                    }
                    unset($where['contract_id']);
                }

                if (isset($where['is_reactive'])) {
                    #if( !empty( $where['is_reactive'] ) ){
                    $this->db->where('job_types.is_reactive', $where['is_reactive']);
                    #}
                    unset($where['is_reactive']);
                }

                if (isset($where['is_scheduled'])) {
                    $this->db->where('job_types.is_reactive !=', 1);
                    unset($where['is_scheduled']);
                }

                if (!empty($where)) {
                    #$this->db->where( $where );
                }
            }

            if (!empty($order_by)) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('job_type');
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->group_by('job_type_id')
                ->get('job_types');

            if ($query->num_rows() > 0) {
                $result_data = $query->result();

                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $result->records 			= $result_data;
                $counters 					= $this->job_types_totals($account_id, $search_term, $raw_where, $limit);
                $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
                $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
                $result->counters->limit  	= (!empty($apply_limit)) ? $limit : $result->counters->total;
                $result->counters->offset 	= $offset;

                $this->session->set_flashdata('message', 'Job Types data found');
            } else {
                $this->session->set_flashdata('message', 'There\'s currently no Job types data matching your criteria');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }

    /** Get Job Types lookup counts **/
    public function job_types_totals($account_id = false, $search_term = false, $where = false, $limit = 100)
    {
        $result = false;
        if (!empty($account_id)) {
            #Limit access by contract to External User Types
            if (in_array($this->ion_auth->_current_user()->user_type_id, EXTERNAL_USER_TYPES)) {
                $contract_access = $this->contract_service->get_linked_people($account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1]);
                $allowed_access  = !empty($contract_access) ? array_column($contract_access, 'contract_id') : [];
                if (!empty($allowed_access)) {
                    $this->db->where_in('job_types.contract_id', $allowed_access);
                } else {
                    $this->session->set_flashdata('message', 'No data found matching your criteria');
                    return false;
                }
            }

            $this->db->select('job_types.job_type_id', false)
                ->where('job_types.is_active', 1)
                ->where('job_types.archived !=', 1)
                ->where('job_types.account_id', $account_id);

            $where = $raw_where = convert_to_array($where);

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->job_types_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->job_types_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                if (isset($where['job_type'])) {
                    if (!empty($where['job_type'])) {
                        $job_type_ref = strtoupper(strip_all_whitespace($where['job_type']));
                        $this->db->where('( job_types.job_type = "'.$where['job_type'].'" OR job_types.job_type_ref = "'.$job_type_ref.'" )');
                    }
                    unset($where['job_type']);
                }

                if (isset($where['evidoc_type_id'])) {
                    if (!empty($where['evidoc_type_id'])) {
                        $evidoc_type_ids = (!is_array($where['evidoc_type_id']) && ((int) $where['evidoc_type_id'] > 0)) ? [ $where['evidoc_type_id'] ] : ((is_array($where['evidoc_type_id'])) ? $where['evidoc_type_id'] : (is_object($where['evidoc_type_id']) ? object_to_array($where['evidoc_type_id']) : []));
                        $this->db->where_in('job_types.evidoc_type_id', $evidoc_type_ids);
                    }
                    unset($where['evidoc_type_id']);
                }

                if (isset($where['contract_id'])) {
                    if (!empty($where['contract_id'])) {
                        $this->db->where('job_types.contract_id', $where['contract_id']);
                    }
                    unset($where['contract_id']);
                }

                if (isset($where['is_reactive'])) {
                    #if( !empty( $where['is_reactive'] ) ){
                    $this->db->where('job_types.is_reactive', $where['is_reactive']);
                    #}
                    unset($where['is_reactive']);
                }

                if (isset($where['is_scheduled'])) {
                    $this->db->where('job_types.is_reactive !=', 1);
                    unset($where['is_scheduled']);
                }

                if (!empty($where)) {
                    #$this->db->where( $where );
                }
            }

            $query 			  	= $this->db->from('job_types')->count_all_results();
            $results['total'] 	= !empty($query) ? $query : 0; //xyz
            $limit 				= ($limit > 0) ? $limit : $results['total'];
            $results['pages'] 	= !empty($query) ? ceil($results['total'] / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }


    /**
    /* Delete/Archive an Job Type resource
    */
    public function delete_job_type($account_id = false, $job_type_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($job_type_id)) {
            $conditions 	= [ 'account_id'=>$account_id,'job_type_id'=>$job_type_id ];
            $record_exists 	= $this->db->get_where('job_types', $conditions)->row();

            if (!empty($record_exists)) {
                ## Archive preexisting links to this Job type
                $this->db->where($conditions)->update('job', [ 'archived'=>1 ]);

                ## Then the parent
                $this->db->where('job_type_id', $job_type_id)
                    ->update('job_types', ['archived'=>1]);

                if ($this->db->trans_status() !== false) {
                    $this->session->set_flashdata('message', 'Job Type archived successfully.');
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


    /** Get Job statuses **/
    public function get_job_statuses($account_id = false, $status_id = false, $grouped = false, $status_group = false)
    {
        $result = null;

        if ($account_id) {
            #$this->db->where( 'job_statuses.account_id', $account_id );
        } else {
            $this->db->where('( job_statuses.account_id IS NULL OR job_statuses.account_id = "" )');
        }

        if ($status_id) {
            $this->db->where('job_statuses.status_id', $status_id);
        }

        if ($status_group) {
            $this->db->where('job_statuses.status_group', $status_group);
        }

        $query = $this->db->where('is_active', 1)->get('job_statuses');

        if ($query->num_rows() > 0) {
            if ($grouped) {
                $data = [];
                foreach ($query->result() as $row) {
                    $data[$row->status_group] = $row;
                }
                $result = $data;
            } else {
                $result = (!empty($status_id)) ? $query->result()[0] : $query->result();
            }
        } else {
            $result = $this->get_job_statuses();
        }
        return $result;
    }

    /*
    * Search through Jobs
    */
    public function job_lookup($account_id = false, $job_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            $where = $raw_where 	= (!empty($where)) ? convert_to_array($where) : false;
            $assignees 	= $this->_check_jobs_access($this->ion_auth->_current_user());

            #Limit Jobs List access by Associated Buildings
            if ((!$this->ion_auth->_current_user()->is_admin) && !empty($this->ion_auth->_current_user()->buildings_visibility) && (strtolower($this->ion_auth->_current_user()->buildings_visibility) == 'limited')) {
                $buildings_access 	= $this->site_service->get_user_associated_buildings($account_id, $this->ion_auth->_current_user->id);
                $allowed_buildings  = !empty($buildings_access) ? array_column($buildings_access, 'site_id') : [];

                if (!empty($allowed_buildings)) {
                    $site_ids_str 	= implode(',', $allowed_buildings);

                    $linked_assets = $this->db->select('asset.asset_id', false)
                        ->where_in('asset.site_id', $allowed_buildings)
                        ->where('asset.account_id', $account_id)
                        ->where('asset.archived !=', 1)
                        ->group_by('asset.asset_id')
                        ->get('asset');

                    if ($linked_assets->num_rows() > 0) {
                        $asset_ids 		= array_column($linked_assets->result_array(), 'asset_id');
                        $asset_ids_str 	= implode(',', $asset_ids);
                        $sql_combi 		= '( job.site_id IN ('.$site_ids_str.' ) OR job.asset_id IN ('.$asset_ids_str.' ) )';
                    } else {
                        $sql_combi		= '( job.site_id IN ('.$site_ids_str.' ) )';
                    }

                    $this->db->where($sql_combi);
                } else {
                    $this->session->set_flashdata('message', 'No data found matching your criteria.');
                    return false;
                }
            }

            #Limit access by contract to External User Types
            if (in_array($this->ion_auth->_current_user()->user_type_id, EXTERNAL_USER_TYPES)) {
                if (!empty($this->ion_auth->_current_user()->is_primary_user)) {
                    ## Get associated users
                    if (!$job_id) {
                        $group_assignees = $this->ion_auth->get_associated_users($account_id, $this->ion_auth->_current_user()->id, false, ['as_arraay'=>1]);
                        if (!empty($group_assignees)) {
                            $group_assignees = (!empty($group_assignees)) ? array_column($group_assignees, 'user_id') : [$this->ion_auth->_current_user()->id];
                            $group_assignees = (!in_array($this->ion_auth->_current_user()->id, $group_assignees)) ? array_merge($group_assignees, [$this->ion_auth->_current_user()->id]) : $group_assignees;
                            $raw_where['group_assignees']	= $group_assignees;
                            $this->db->where_in('job.assigned_to', $group_assignees);
                        } else {
                            $contract_access = $this->contract_service->get_linked_people($account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1]);
                            $allowed_access  = !empty($contract_access) ? array_column($contract_access, 'contract_id') : [];
                            if (!empty($allowed_access)) {
                                $this->db->where_in('job_types.contract_id', $allowed_access);
                            } else {
                                $this->session->set_flashdata('message', 'No data found matching your criteria');
                                return false;
                            }
                        }
                    }
                } else {
                    $contract_access = $this->contract_service->get_linked_people($account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1]);
                    $allowed_access  = !empty($contract_access) ? array_column($contract_access, 'contract_id') : [];
                    if (!empty($allowed_access)) {
                        $this->db->where_in('job_types.contract_id', $allowed_access);
                    } else {
                        $this->session->set_flashdata('message', 'No data found matching your criteria');
                        return false;
                    }
                }
            }

            $raw_where['assignees']	= $assignees;

            ## Get Asset IDs by Category ID
            if (isset($where['category_id'])) {
                if (!empty($where['category_id'])) {
                    $asset_ids = $this->get_assets_by_category($account_id, $where['category_id'], [ 'ids_only'=>1 ]);
                    if (!empty($asset_ids) && is_array($asset_ids)) {
                        $this->db->where('job.job_date > 0');
                        $this->db->where_in('job.asset_id', $asset_ids);
                    }
                }
                unset($where['category_id']);
            }

            $this->db->select('job.*, site.qr_code_location, job_types.contract_id as contract_id, job_types.*, schedule_activities.activity_name, schedule_activities.status  `activity_status`, job_statuses.job_status, job_statuses.status_group, job_tracking_statuses.job_tracking_status, job_tracking_statuses.job_tracking_group, fc.fail_code, fc.fail_code_text, fc.fail_code_desc, fc.fail_code_group,  CONCAT(user.first_name," ",user.last_name) `assignee`, CONCAT(user2.first_name," ",user2.last_name) `second_assignee`, addrs.main_address_id,addrs.addressline1 `address_line_1`, addrs.addressline2 `address_line_2`,addrs.addressline3 `address_line_3`,addrs.posttown `address_city`,addrs.county `address_county`, addrs.postcode `postcode`, addrs.postcode `address_postcode`, customer_addresses.address_postcode `customer_postcode`, postcode_area, postcode_district, postcode_sector, addrs.summaryline `summaryline`, CONCAT( addrs.addressline1,", ",addrs.addressline2,", ",addrs.posttown, ", ",addrs.posttown,", ",addrs.postcode ) `short_address`, addrs.organisation `address_business_name`, site.site_actual_address, site.site_actual_postcode, site.site_address_verified', false)
                ->join('addresses addrs', 'addrs.main_address_id = job.address_id', 'left')
                ->join('job_types', 'job_types.job_type_id = job.job_type_id', 'left')
                ->join('job_statuses', 'job_statuses.status_id = job.status_id', 'left')
                ->join('job_tracking_statuses', 'job_tracking_statuses.job_tracking_id = job.job_tracking_id', 'left')
                ->join('audit_categories', 'audit_categories.category_id = job.category_id', 'left')
                ->join('job_fail_codes fc', 'fc.fail_code_id = job.fail_code_id', 'left')
                ->join('user', 'user.id = job.assigned_to', 'left')
                ->join('user user2', 'user2.id = job.second_assignee_id', 'left')
                ->join('site', 'site.site_id = job.site_id', 'left')
                ->join('customer', 'customer.customer_id = job.customer_id', 'left')
                ->join('customer_addresses', 'customer_addresses.customer_id = customer.customer_id', 'left')
                ->join('schedule_activities', 'schedule_activities.activity_id = job.activity_id', 'left')
                ->where('job.account_id', $account_id)
                ->where('job.archived !=', 1);

            if (!empty($job_id)) {
                $row = $this->db->get_where('job', ['job_id'=>$job_id])->row();

                if (!empty($row)) {
                    $row->site_details  	= null;
                    $row->customer_details  = null;

                    if (!empty($row->customer_id)) {
                        $customer_details 		= $this->customer_service->get_customers($account_id, $row->customer_id);
                        $row->customer_details  = !empty($customer_details) ? $customer_details : null;
                    }

                    if (!empty($row->site_id)) {
                        $site_details 			= $this->site_service->get_sites($account_id, $row->site_id);
                        $row->site_details  	= !empty($site_details) ? $site_details : null;
                    }

                    $this->session->set_flashdata('message', 'Job record found');
                    $required_items			= $this->get_required_items($account_id, $job_id);
                    $consumed_items			= $this->get_consumed_items($account_id, $job_id);
                    $ra_responses			= $this->ra_service->get_ra_responses(false, $job_id);
                    $associated_risks		= $this->get_associated_risks($account_id, $row->job_type_id);
                    $dynamic_risks			= $this->get_dynamic_risks($account_id, $row->job_id);

                    $row->required_items  	= (!empty($required_items)) ? $required_items : null;
                    $row->consumed_items 	= (!empty($consumed_items)) ? $consumed_items : null;
                    $row->ra_responses 		= (!empty($ra_responses)) ? $ra_responses : null;
                    $row->associate_risks	= (!empty($associated_risks)) ? $associated_risks : null;
                    $row->dynamic_risks 	= (!empty($dynamic_risks)) ? $dynamic_risks : null;
                    $row->comm_logs			= $this->get_communication_logs($account_id, false, false, $job_id);
                    $result 				= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                    $result->records 		= $row;
                } else {
                    $this->session->set_flashdata('message', 'Job record not found');
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

                        if (!empty($search_where['job.status_id'])) {
                            $search_where['job_statuses.job_status'] =  trim($term);
                            unset($search_where['job.status_id']);
                        }

                        if (!empty($search_where['job.job_type_id'])) {
                            $search_where['job_types.job_type'] =  trim($term);
                            unset($search_where['job.job_type_id']);
                        }

                        if (!empty($search_where['job.assigned_to'])) {
                            $search_where['user.first_name'] =  trim($term);
                            $search_where['user.last_name'] =  trim($term);
                            unset($search_where['job.assigned_to']);
                        }

                        if (!empty($search_where['job.job_date'])) {
                            $job_date = date('Y-m-d', strtotime($term));
                            if (valid_date($job_date)) {
                                $search_where['job.job_date'] =  $job_date;
                            }
                            unset($search_where['job.job_date']);
                        }

                        if (!empty($search_where['job.job_tracking_id'])) {
                            $search_where['job_tracking_statuses.job_tracking_status'] =  trim($term);
                            unset($search_where['job.job_tracking_id']);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->searchable_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    if (!empty($search_where['job.status_id'])) {
                        $search_where['job_statuses.job_status'] =  trim($search_term);
                        unset($search_where['job.status_id']);
                    }

                    if (!empty($search_where['job.job_type_id'])) {
                        $search_where['job_types.job_type'] =  trim($search_term);
                        unset($search_where['job.job_type_id']);
                    }

                    if (!empty($search_where['job.assigned_to'])) {
                        $search_where['user.first_name'] =  trim($search_term);
                        $search_where['user.last_name'] =  trim($search_term);
                        unset($search_where['job.assigned_to']);
                    }

                    if (!empty($search_where['job.job_date'])) {
                        $job_date = date('Y-m-d', strtotime($search_term));
                        if (valid_date($job_date)) {
                            $search_where['job.job_date'] =  $job_date;
                        }
                        unset($search_where['job.job_date']);
                    }

                    if (!empty($search_where['job.job_tracking_id'])) {
                        $search_where['job_tracking_statuses.job_tracking_status'] =  trim($search_term);
                        unset($search_where['job.job_tracking_id']);
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (isset($where['status_id'])) {
                if (!empty($where['status_id'])) {
                    $status_ids = (!is_array($where['status_id']) && ((int) $where['status_id'] > 0)) ? [ $where['status_id'] ] : ((is_array($where['status_id'])) ? $where['status_id'] : (is_object($where['status_id']) ? object_to_array($where['status_id']) : []));
                    $this->db->where_in('job.status_id', $status_ids);
                }
                unset($where['job_type_id']);
            }

            if (isset($where['job_type_id'])) {
                if (!empty($where['job_type_id'])) {
                    $job_types = (!is_array($where['job_type_id']) && ((int) $where['job_type_id'] > 0)) ? [ $where['job_type_id'] ] : ((is_array($where['job_type_id'])) ? $where['job_type_id'] : (is_object($where['job_type_id']) ? object_to_array($where['job_type_id']) : []));
                    $this->db->where_in('job.job_type_id', $job_types);
                }
                unset($where['job_type_id']);
            }

            if (isset($where['pool_jobs'])) {
                if (!empty($where['pool_jobs'])) {
                    $this->db->where('( job.job_date = "1970-01-01" OR job.job_date = "0000-00-00" OR job.job_date IS NULL )');
                }
                unset($where['pool_jobs']);
            }

            if (isset($where['un_booked_jobs'])) {
                if (!empty($where['un_booked_jobs'])) {
                    $this->db->where('( job.job_date = "1970-01-01" OR job.job_date = "0000-00-00" OR job.job_date IS NULL )');
                    $this->db->where('( ( job.assigned_to = 0 OR job.assigned_to = "" OR job.assigned_to IS NULL ) )');
                    $this->db->where('( job.activity_id > 0 )');
                }
                unset($where['un_booked_jobs']);
            }

            if (isset($where['location_id'])) {
                if (!empty($where['location_id'])) {
                    $this->db->where('job.location_id', $where['location_id']);
                }
            }

            if (isset($where['asset_id'])) {
                if (!empty($where['asset_id'])) {
                    $this->db->where('job.asset_id', $where['asset_id']);
                }
            }

            if (isset($where['site_id'])) {
                if (!empty($where['site_id'])) {
                    $this->db->where('job.site_id', $where['site_id']);
                }
            }

            if (isset($where['job_date_start']) || isset($where['job_date_end'])) {
                if (!empty($where['job_date_start'])) {
                    $this->db->where('job.job_date >=', format_date_db($where['job_date_start']));
                }
                unset($where['job_date_start']);

                if (!empty($where['job_date_end'])) {
                    $this->db->where('job.job_date <=', format_date_db($where['job_date_end']));
                    unset($where['job_date_end']);
                }
                unset($where['job_date_end']);
            }

            if (isset($where['created_on_start']) || isset($where['created_on_end'])) {
                if (!empty($where['created_on_start'])) {
                    $this->db->where('job.created_on >=', format_date_db($where['created_on_start']).' 00:00:00');
                }
                unset($where['created_on_start']);

                if (!empty($where['created_on_end'])) {
                    $this->db->where('job.created_on <=', format_date_db($where['created_on_end']).' 23:59:59');
                }
                unset($where['created_on_end']);
            }

            if (isset($where['region_id'])) {
                if (!empty($where['region_id'])) {
                    $region_ids = is_array($where['region_id']) ? $where['region_id'] : [ $where['region_id'] ] ;
                    $this->db->where_in('job.region_id', $region_ids);
                }
                unset($where['region_id']);
            }

            if (isset($where['job_date'])) {
                if (!empty($where['job_date'])) {
                    $sjob_date = date('Y-m-d', strtotime($where['job_date']));
                    $this->db->where('job.job_date', $sjob_date);
                    unset($where['job_date']);
                }
            } else {
                if (isset($where['date_from']) || isset($where['date_to'])) {
                    if (!empty($where['date_from'])) {
                        $this->db->where('job.job_date >=', date('Y-m-d', strtotime(format_date_db($where['date_from']))));
                    }

                    if (!empty($where['date_to'])) {
                        $this->db->where('job.job_date <=', date('Y-m-d', strtotime(format_date_db($where['date_to']))));
                    }
                    unset($where['date_from'], $where['date_to']);
                }
            }

            if (isset($where['due_date_from']) || isset($where['due_date_to'])) {
                $due_date_from 	= date('Y-m-d', strtotime($where['due_date_from']));
                $due_date_to 	= (!empty($where['due_date_to'])) ? date('Y-m-d', strtotime($where['due_date_to'])) : date('Y-m-d');
                $this->db->where('job.due_date >=', $due_date_from);
                $this->db->where('job.due_date <=', $due_date_to);
                unset($where['due_date_from'], $where['due_date_to']);
            }

            ## Combined assignees
            if (!empty($assignees)) {
                if (!empty($where['assigned_to'])) {
                    $assignees[] = $where['assigned_to'];
                }
                $this->db->where_in('job.assigned_to', $assignees);
            } else {
                if (isset($where['assigned_to'])) {
                    if (!empty($where['assigned_to'])) {
                        if ($where['assigned_to'] < 0) {
                            $where_job = "( ( job.assigned_to is NULL ) || ( job.assigned_to = 0 ) || ( job.assigned_to = '' ) )";
                            $this->db->where($where_job);
                        } else {
                            $this->db->where('( ( job.assigned_to = "'.$where['assigned_to'].'" ) || ( job.second_assignee_id = "'.$where['assigned_to'].'" ) )');
                        }
                    }
                    unset($where['assigned_to']);
                }
            }

            if (isset($where['exclude_successful_jobs'])) {
                if (!empty($where['exclude_successful_jobs'])) {
                    $this->db->where_not_in('job.status_id', [ 4 ]); //Remove Successful Jobs
                }
                unset($where['exclude_successful_jobs']);
            }

            if (isset($where['open_jobs'])) {
                #if( !empty( $where['open_jobs'] ) ){
                $this->db->where('( job.job_date = "1970-01-01" OR job.job_date = "0000-00-00" OR job.job_date IS NULL )');
                $this->db->where('( job.due_date != "1970-01-01" AND job.due_date != "0000-00-00" AND job.due_date IS NOT NULL )');
                $this->db->where('( job.assigned_to > 0 )');
                #}
                unset($where['open_jobs']);
            }

            if (isset($where['is_reactive'])) {
                #if( !empty( $where['is_reactive'] ) ){
                $this->db->where('job_types.is_reactive', $where['is_reactive']);
                #}
                unset($where['is_reactive']);
            }

            if (isset($where['is_scheduled'])) {
                $this->db->where('job.schedule_id >', 0);
                unset($where['is_scheduled']);
            }

            if (!empty($where)) {
                #$this->db->where( $where );
            }

            if ($order_by) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('job.job_id desc, job.job_date desc');
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->group_by('job.job_id')
                ->get('job');

            if ($query->num_rows() > 0) {
                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $result->records 			= $query->result();
                $counters 					= $this->get_total_jobs($account_id, $search_term, $raw_where, $limit);
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
    public function get_total_jobs($account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;

        if (!empty($account_id)) {
            #Limit Jobs List access by Associated Buildings
            if ((!$this->ion_auth->_current_user()->is_admin) && !empty($this->ion_auth->_current_user()->buildings_visibility) && (strtolower($this->ion_auth->_current_user()->buildings_visibility) == 'limited')) {
                $buildings_access 	= $this->site_service->get_user_associated_buildings($account_id, $this->ion_auth->_current_user->id);
                $allowed_buildings  = !empty($buildings_access) ? array_column($buildings_access, 'site_id') : [];

                if (!empty($allowed_buildings)) {
                    $site_ids_str 	= implode(',', $allowed_buildings);

                    $linked_assets = $this->db->select('asset.asset_id', false)
                        ->where_in('asset.site_id', $allowed_buildings)
                        ->where('asset.account_id', $account_id)
                        ->where('asset.archived !=', 1)
                        ->group_by('asset.asset_id')
                        ->get('asset');

                    if ($linked_assets->num_rows() > 0) {
                        $asset_ids 		= array_column($linked_assets->result_array(), 'asset_id');
                        $asset_ids_str 	= implode(',', $asset_ids);
                        $sql_combi 		= '( job.site_id IN ('.$site_ids_str.' ) OR job.asset_id IN ('.$asset_ids_str.' ) )';
                    } else {
                        $sql_combi		= '( job.site_id IN ('.$site_ids_str.' ) )';
                    }

                    $this->db->where($sql_combi);
                } else {
                    $this->session->set_flashdata('message', 'No data found matching your criteria.');
                    return false;
                }
            }

            #Limit access by contract to External User Types
            if (in_array($this->ion_auth->_current_user()->user_type_id, EXTERNAL_USER_TYPES)) {
                if (!empty($this->ion_auth->_current_user()->is_primary_user)) {
                    $group_assignees = !empty($where['group_assignees']) ? $where['group_assignees'] : false;
                    if ($group_assignees) {
                        $this->db->where_in('job.assigned_to', $group_assignees);
                    } else {
                        $group_assignees = $this->ion_auth->get_associated_users($account_id, $this->ion_auth->_current_user()->id, false, ['as_arraay'=>1]);
                        if (!empty($group_assignees)) {
                            $group_assignees = (!empty($group_assignees)) ? array_column($group_assignees, 'user_id') : [$this->ion_auth->_current_user()->id];
                            $group_assignees = (!in_array($this->ion_auth->_current_user()->id, $group_assignees)) ? array_merge($group_assignees, [$this->ion_auth->_current_user()->id]) : $group_assignees;
                            $raw_where['group_assignees']	= $group_assignees;
                            $this->db->where_in('job.assigned_to', $group_assignees);
                        } else {
                            $contract_access = $this->contract_service->get_linked_people($account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1]);
                            $allowed_access  = !empty($contract_access) ? array_column($contract_access, 'contract_id') : [];
                            if (!empty($allowed_access)) {
                                $this->db->where_in('job_types.contract_id', $allowed_access);
                            } else {
                                $this->session->set_flashdata('message', 'No data found matching your criteria');
                                return false;
                            }
                        }
                    }
                    unset($where['group_assignees']);
                } else {
                    $contract_access = $this->contract_service->get_linked_people($account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1]);
                    $allowed_access  = !empty($contract_access) ? array_column($contract_access, 'contract_id') : [];
                    if (!empty($allowed_access)) {
                        $this->db->where_in('job_types.contract_id', $allowed_access);
                    } else {
                        $this->session->set_flashdata('message', 'No data found matching your criteria');
                        return false;
                    }
                }
            }

            $where 		= $raw_where = (!empty($where)) ? convert_to_array($where) : false;

            ## Extract pre-assigned Assignees
            if (isset($where['assignees'])) {
                $assignees 	= $where['assignees'];
                unset($where['assignees']);
            }

            ## Get Asset IDs by Category ID
            if (isset($where['category_id'])) {
                if (!empty($where['category_id'])) {
                    $asset_ids = $this->get_assets_by_category($account_id, $where['category_id'], [ 'ids_only'=>1 ]);
                    if (!empty($asset_ids) && is_array($asset_ids)) {
                        $this->db->where('job.job_date > 0');
                        $this->db->where_in('job.asset_id', $asset_ids);
                    }
                }
                unset($where['category_id']);
            }

            $this->db->select('job.job_id, job.account_id, job.archived', false)
                ->join('addresses addrs', 'addrs.main_address_id = job.address_id', 'left')
                ->join('job_types', 'job_types.job_type_id = job.job_type_id', 'left')
                ->join('job_statuses', 'job_statuses.status_id = job.status_id', 'left')
                ->join('job_tracking_statuses', 'job_tracking_statuses.job_tracking_id = job.job_tracking_id', 'left')
                #->join( 'audit_categories','audit_categories.category_id = job.category_id','left' )
                #->join( 'job_fail_codes fc','fc.fail_code_id = job.fail_code_id','left' )
                ->join('user', 'user.id = job.assigned_to', 'left')
                #->join( 'customer','customer.customer_id = job.customer_id','left' )
                #->join( 'customer_addresses','customer_addresses.customer_id = customer.customer_id','left' )
                ->join('customer_addresses', 'customer_addresses.customer_id = job.customer_id', 'left')
                ->where('job.account_id', $account_id)
                ->where('job.archived !=', 1);

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

                        if (!empty($search_where['job.status_id'])) {
                            $search_where['job_statuses.job_status'] =  trim($term);
                            unset($search_where['job.status_id']);
                        }

                        if (!empty($search_where['job.job_type_id'])) {
                            $search_where['job_types.job_type'] =  trim($term);
                            unset($search_where['job.job_type_id']);
                        }

                        if (!empty($search_where['job.assigned_to'])) {
                            $search_where['user.first_name'] =  trim($term);
                            $search_where['user.last_name'] =  trim($term);
                            unset($search_where['job.assigned_to']);
                        }

                        if (!empty($search_where['job.job_date'])) {
                            $job_date = date('Y-m-d', strtotime($term));
                            if (valid_date($job_date)) {
                                $search_where['job.job_date'] =  $job_date;
                            }
                            unset($search_where['job.job_date']);
                        }

                        if (!empty($search_where['job.job_tracking_id'])) {
                            $search_where['job_tracking_statuses.job_tracking_status'] =  trim($term);
                            unset($search_where['job.job_tracking_id']);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->searchable_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    if (!empty($search_where['job.status_id'])) {
                        $search_where['job_statuses.job_status'] =  trim($search_term);
                        unset($search_where['job.status_id']);
                    }

                    if (!empty($search_where['job.job_type_id'])) {
                        $search_where['job_types.job_type'] =  trim($search_term);
                        unset($search_where['job.job_type_id']);
                    }

                    if (!empty($search_where['job.assigned_to'])) {
                        $search_where['user.first_name'] =  trim($search_term);
                        $search_where['user.last_name'] =  trim($search_term);
                        unset($search_where['job.assigned_to']);
                    }

                    if (!empty($search_where['job.job_date'])) {
                        $job_date = date('Y-m-d', strtotime($search_term));
                        if (valid_date($job_date)) {
                            $search_where['job.job_date'] =  $job_date;
                        }
                        unset($search_where['job.job_date']);
                    }

                    if (!empty($search_where['job.job_tracking_id'])) {
                        $search_where['job_tracking_statuses.job_tracking_status'] =  trim($search_term);
                        unset($search_where['job.job_tracking_id']);
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (isset($where['status_id'])) {
                if (!empty($where['status_id'])) {
                    $status_ids = (!is_array($where['status_id']) && ((int) $where['status_id'] > 0)) ? [ $where['status_id'] ] : ((is_array($where['status_id'])) ? $where['status_id'] : (is_object($where['status_id']) ? object_to_array($where['status_id']) : []));
                    $this->db->where_in('job.status_id', $status_ids);
                }
                unset($where['job_type_id']);
            }

            if (isset($where['job_type_id'])) {
                if (!empty($where['job_type_id'])) {
                    $job_types = (!is_array($where['job_type_id']) && ((int) $where['job_type_id'] > 0)) ? [ $where['job_type_id'] ] : ((is_array($where['job_type_id'])) ? $where['job_type_id'] : (is_object($where['job_type_id']) ? object_to_array($where['job_type_id']) : []));
                    $this->db->where_in('job.job_type_id', $job_types);
                }
                unset($where['job_type_id']);
            }

            if (isset($where['pool_jobs'])) {
                if (!empty($where['pool_jobs'])) {
                    $this->db->where('( job.job_date = "1970-01-01" OR job.job_date = "0000-00-00" OR job.job_date IS NULL )');
                }
                unset($where['pool_jobs']);
            }

            if (isset($where['un_booked_jobs'])) {
                if (!empty($where['un_booked_jobs'])) {
                    $this->db->where('( job.job_date = "1970-01-01" OR job.job_date = "0000-00-00" OR job.job_date IS NULL )');
                    $this->db->where('( ( job.assigned_to = 0 OR job.assigned_to = "" OR job.assigned_to IS NULL ) )');
                    $this->db->where('( job.activity_id > 0 )');
                }
                unset($where['un_booked_jobs']);
            }

            if (isset($where['location_id'])) {
                if (!empty($where['location_id'])) {
                    $this->db->where('job.location_id', $where['location_id']);
                }
            }

            if (isset($where['asset_id'])) {
                if (!empty($where['asset_id'])) {
                    $this->db->where('job.asset_id', $where['asset_id']);
                }
            }

            if (isset($where['site_id'])) {
                if (!empty($where['site_id'])) {
                    $this->db->where('job.site_id', $where['site_id']);
                }
            }

            if (isset($where['job_date_start']) || isset($where['job_date_end'])) {
                if (!empty($where['job_date_start'])) {
                    $this->db->where('job.job_date >=', format_date_db($where['job_date_start']));
                }
                unset($where['job_date_start']);

                if (!empty($where['job_date_end'])) {
                    $this->db->where('job.job_date <=', format_date_db($where['job_date_end']));
                    unset($where['job_date_end']);
                }
                unset($where['job_date_end']);
            }

            if (isset($where['created_on_start']) || isset($where['created_on_end'])) {
                if (!empty($where['created_on_start'])) {
                    $this->db->where('job.created_on >=', format_date_db($where['created_on_start']).' 00:00:00');
                }
                unset($where['created_on_start']);

                if (!empty($where['created_on_end'])) {
                    $this->db->where('job.created_on <=', format_date_db($where['created_on_end']).' 23:59:59');
                }
                unset($where['created_on_end']);
            }

            if (isset($where['region_id'])) {
                if (!empty($where['region_id'])) {
                    $region_ids = is_array($where['region_id']) ? $where['region_id'] : [ $where['region_id'] ] ;
                    $this->db->where_in('job.region_id', $region_ids);
                }
                unset($where['region_id']);
            }

            if (isset($where['job_date'])) {
                if (!empty($where['job_date'])) {
                    $sjob_date = date('Y-m-d', strtotime($where['job_date']));
                    $this->db->where('job.job_date', $sjob_date);
                    unset($where['job_date']);
                }
            } else {
                if (isset($where['date_from']) || isset($where['date_to'])) {
                    if (!empty($where['date_from'])) {
                        $this->db->where('job.job_date >=', date('Y-m-d', strtotime(format_date_db($where['date_from']))));
                    }

                    if (!empty($where['date_to'])) {
                        $this->db->where('job.job_date <=', date('Y-m-d', strtotime(format_date_db($where['date_to']))));
                    }
                    unset($where['date_from'], $where['date_to']);
                }
            }

            if (isset($where['due_date_from']) || isset($where['due_date_to'])) {
                $due_date_from 	= date('Y-m-d', strtotime($where['due_date_from']));
                $due_date_to 	= (!empty($where['due_date_to'])) ? date('Y-m-d', strtotime($where['due_date_to'])) : date('Y-m-d');
                $this->db->where('job.due_date >=', $due_date_from);
                $this->db->where('job.due_date <=', $due_date_to);
                unset($where['due_date_from'], $where['due_date_to']);
            }

            ## Limit Jobs based on Associated User's Jobs
            if (!empty($assignees)) {
                if (!empty($where['assigned_to'])) {
                    $assignees[] 		= $where['assigned_to'];
                }
                $this->db->where_in('job.assigned_to', $assignees);
            } else {
                if (isset($where['assigned_to'])) {
                    if (!empty($where['assigned_to'])) {
                        if ($where['assigned_to'] < 0) {
                            $where_job = "( ( job.assigned_to is NULL ) || ( job.assigned_to = 0 ) || ( job.assigned_to = '' ) )";
                            $this->db->where($where_job);
                        } else {
                            $this->db->where('( ( job.assigned_to = "'.$where['assigned_to'].'" ) || ( job.second_assignee_id = "'.$where['assigned_to'].'" ) )');
                        }
                    }
                    unset($where['assigned_to']);
                }
            }

            if (isset($where['exclude_successful_jobs'])) {
                if (!empty($where['exclude_successful_jobs'])) {
                    $this->db->where_not_in('job.status_id', [ 4 ]); //Remove Successful Jobs
                }
                unset($where['exclude_successful_jobs']);
            }

            if (isset($where['open_jobs'])) {
                #if( !empty( $where['open_jobs'] ) ){
                $this->db->where('( job.job_date = "1970-01-01" OR job.job_date = "0000-00-00" OR job.job_date IS NULL )');
                $this->db->where('( job.due_date != "1970-01-01" AND job.due_date != "0000-00-00" AND job.due_date IS NOT NULL )');
                $this->db->where('( job.assigned_to > 0 )');
                #}
                unset($where['open_jobs']);
            }

            if (isset($where['is_reactive'])) {
                #if( !empty( $where['is_reactive'] ) ){
                $this->db->where('job_types.is_reactive', $where['is_reactive']);
                #}
                unset($where['is_reactive']);
            }

            if (isset($where['is_scheduled'])) {
                $this->db->where('job.schedule_id >', 0);
                unset($where['is_scheduled']);
            }

            if (!empty($where)) {
                #$this->db->where( $where );
            }

            $query = $this->db->from('job')->count_all_results();
            $results['total'] = !empty($query) ? $query : 0;
            $limit 			  = (!empty($limit > 0)) ? $limit : $results['total'];
            $results['pages'] = !empty($query) ? ceil($query / $limit) : 0;

            return json_decode(json_encode($results));
        }

        return $result;
    }

    /** Add Required Stock Items to a Job Record **/
    public function add_required_items($account_id = false, $job_id = false, $item_type = false, $postdata = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($job_id) && !empty($postdata)) {
            $postdata 		= convert_to_array($postdata);
            $required_items = !empty($postdata['required_items']) ? $postdata['required_items'] : false;
            $required_items = (is_json($required_items)) ? json_decode($required_items) : $required_items;
            unset($postdata['required_items']);

            $new = $exists  = $all = $invalid_codes = [];
            if (!empty($required_items)) {
                foreach ($required_items as $key => $details) {
                    $details				= is_object($details) ? object_to_array($details) : $details;
                    $details['account_id'] 	= $account_id;
                    $requested_type	= (!empty($details['item_type'])) ? $details['item_type'] : $item_type;
                    $item_type 	  	= $this->_get_item_type($requested_type);
                    $raw_post	  	= $details;
                    $details	  	= $this->ssid_common->_filter_data('job_required_items', $details);
                    if (!empty($details['item_code'])) {
                        $check_exists = $this->db->get_where('job_required_items', ['job_id'=>$job_id, 'item_code'=>$details['item_code']])->row();

                        $details['item_type'] = $item_type;

                        if (!empty($check_exists)) {
                            $details['id']  	  		= $check_exists->id;
                            $details['item_qty']  		= ( string ) ((!empty($details['item_qty'])) ? ((int) $details['item_qty'] + (int)$check_exists->item_qty) : 1 + (int)$check_exists->item_qty);
                            $details['price']  			= $check_exists->price;
                            $details['price_adjusted'] 	= $check_exists->price_adjusted;
                            ;
                            ksort($details);
                            $exists[] = $details;
                        } else {
                            ## Verify item exists as part of this account
                            $item_code_exists = $this->_check_item_exists($account_id, $details['item_code'], $item_type);
                            if ($item_code_exists) {
                                $details['item_qty']  		= ( string ) ((!empty($details['item_qty'])) ? (int) $details['item_qty'] : 1);
                                $details['price']  			= ( string ) ((!empty($item_code_exists->sell_price)) ? (int)$item_code_exists->sell_price : '0.00');
                                $details['price_adjusted'] 	= ( string ) ((!empty($item_code_exists->sell_price)) ? (int)$item_code_exists->sell_price : '0.00');
                                ksort($details);
                                $new[] 	  = $details;
                            } else {
                                $invalid_codes[] = $details['item_code'];
                            }
                        }

                        ksort($details);
                        $all[] 				  = $details;
                    }
                }
            } elseif (!empty($postdata['item_code'])) {
                $postdata['account_id'] = $account_id;
                $raw_post 	  = $postdata;
                $postdata	  = $this->ssid_common->_filter_data('job_required_items', $postdata);
                $check_exists = $this->db->get_where('job_required_items', ['job_id'=>$job_id, 'item_code'=>$postdata['item_code']])->row();
                if (!empty($postdata['item_code'])) {
                    $requested_type			= (!empty($postdata['item_type'])) ? $postdata['item_type'] : $item_type;
                    $item_type 	  			= $this->_get_item_type($requested_type);
                    $postdata['item_type'] 	= $item_type;

                    if (!empty($check_exists)) {
                        $postdata['id']  	   = $check_exists->id;
                        $postdata['item_qty']  = ( string ) ((!empty($postdata['item_qty'])) ? (int) ($postdata['item_qty'] + (int) $check_exists->item_qty) : (1 + (int) $check_exists->item_qty));
                        $postdata['price']  		= $check_exists->price;
                        $postdata['price_adjusted'] = $check_exists->price_adjusted;
                        ;

                        ksort($postdata);
                        $exists[] = $postdata;
                    } else {
                        ## Verify item exists as part of this account
                        $item_code_exists = $this->_check_item_exists($account_id, $postdata['item_code'], $item_type);
                        if ($item_code_exists) {
                            $postdata['item_qty']  			= ( string ) ((!empty($postdata['item_qty'])) ? (int)$postdata['item_qty'] : 1);
                            $postdata['price']  			= ( string ) ((!empty($item_code_exists->sell_price)) ? (int)$item_code_exists->sell_price : '0.00');
                            $postdata['price_adjusted'] 	= ( string ) ((!empty($item_code_exists->sell_price)) ? (int)$item_code_exists->sell_price : '0.00');

                            ksort($postdata);
                            $new[] 	  = $postdata;
                        } else {
                            $invalid_codes[] = $postdata['item_code'];
                        }
                    }

                    ksort($postdata);
                    $all[] 		  		   = $postdata;
                }
            }

            if (!empty($new)) {
                $this->db->insert_batch('job_required_items', $new);
            }

            if (!empty($exists)) {
                $this->db->update_batch('job_required_items', $exists, 'id');
            }

            if ($this->db->trans_status() !== false) {
                $result = $all;
                $this->session->set_flashdata('message', 'Job items added successfully.');
                if (!empty($invalid_codes)) {
                    $this->session->set_flashdata('message', 'Some items were not added as they are invalid. Invalid list '.json_encode($invalid_codes));
                }
            } else {
                $this->session->set_flashdata('message', 'Your request is missing required information');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }
        return $result;
    }

    /**
    * Get all items required on a Job
    */
    public function get_required_items($account_id = false, $job_id = false, $item_type = false)
    {
        $result = false;
        if ($job_id) {
            if (!empty($item_type)) {
                $item_type = $this->_get_item_type($item_type);
            }

            $sql_str = "( SELECT job_required_items.id, job_required_items.job_id, job_required_items.item_code, job_required_items.item_qty, job_required_items.price, job_required_items.price_adjusted, job_required_items.item_type, stock_items.item_name
						FROM job_required_items JOIN stock_items ON job_required_items.item_code = stock_items.item_code
						WHERE job_required_items.job_id = '".$job_id."' ";
            if (!empty($account_id)) {
                $sql_str .= "AND stock_items.account_id = '". $account_id."' ";
            }
            if (!empty($item_type)) {
                $sql_str .= "AND job_required_items.item_type = '". $item_type."' ";
            }
            $sql_str .= "ORDER BY stock_items.item_name ) ";
            $sql_str .= "UNION ALL ";
            $sql_str .= "( SELECT job_required_items.id, job_required_items.job_id, job_required_items.item_code, job_required_items.item_qty, job_required_items.price, job_required_items.price_adjusted, job_required_items.item_type, bom_items.item_name
						FROM job_required_items JOIN bom_items ON job_required_items.item_code = bom_items.item_code
						WHERE job_required_items.job_id = '".$job_id."' ";
            if (!empty($account_id)) {
                $sql_str .= "AND bom_items.account_id = '". $account_id."' ";
            }
            if (!empty($item_type)) {
                $sql_str .= "AND job_required_items.item_type = '". $item_type."' ";
            }
            $sql_str .= "ORDER BY bom_items.item_name ) ";

            $query = $this->db->query($sql_str);

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Job Required items found');
            } else {
                $this->session->set_flashdata('message', 'No data found');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }

    /** Add Consumed Stock & BOM Items to a Job Record **/
    public function add_consumed_items($account_id = false, $job_id = false, $item_type = false, $postdata = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($job_id) && !empty($postdata)) {
            $postdata 		= convert_to_array($postdata);
            $consumed_items = !empty($postdata['consumed_items']) ? $postdata['consumed_items'] : false;
            $consumed_items = (is_json($consumed_items)) ? json_decode($consumed_items) : $consumed_items;
            unset($postdata['consumed_items']);

            $new = $exists  = $all = $invalid_codes = [];
            if (!empty($consumed_items)) {
                foreach ($consumed_items as $key => $details) {
                    $details				= is_object($details) ? object_to_array($details) : $details;
                    $details['account_id'] 	= $account_id;
                    $requested_type	= (!empty($details['item_type'])) ? $details['item_type'] : $item_type;
                    $item_type 	  	= $this->_get_item_type($requested_type);
                    $raw_post	  	= $details;
                    $details	  	= $this->ssid_common->_filter_data('job_consumed_items', $details);

                    if (!empty($details['item_code'])) {
                        $check_exists = $this->db->get_where('job_consumed_items', ['job_id'=>$job_id, 'item_code'=>$details['item_code']])->row();

                        $details['item_type'] = $item_type;

                        if (!empty($check_exists)) {
                            $details['id']  	  		= $check_exists->id;
                            $details['item_qty']  		= ( string ) ((!empty($details['item_qty'])) ? ((int) $details['item_qty'] + (int)$check_exists->item_qty) : 1 + (int)$check_exists->item_qty);
                            $details['price']  			= number_format($check_exists->price, 2);
                            $details['price_adjusted'] 	= number_format($check_exists->price_adjusted, 2);
                            $details['last_modified_by']= $this->ion_auth->_current_user->id;
                            ksort($details);
                            $exists[] = $details;
                        } else {
                            ## Verify item exists as part of this account
                            $item_code_exists = $this->_check_item_exists($account_id, $details['item_code'], $item_type);
                            if ($item_code_exists) {
                                $details['item_qty']  		= ( string ) ((!empty($details['item_qty'])) ? (int) $details['item_qty'] : 1);
                                $details['price']  			= ( string ) ((!empty($item_code_exists->sell_price)) ? number_format($item_code_exists->sell_price, 2) : '0.00');
                                $details['price_adjusted'] 	= ( string ) ((!empty($item_code_exists->sell_price)) ? number_format($item_code_exists->sell_price, 2) : '0.00');
                                $details['last_modified_by']= null;
                                ksort($details);
                                $new[] 	  = $details;
                            } else {
                                $invalid_codes[] = $details['item_code'];
                            }
                        }

                        ksort($details);
                        $all[] 				  = $details;
                    }
                }
            } elseif (!empty($postdata['item_code'])) {
                $postdata['account_id'] = $account_id;
                $raw_post 	  = $postdata;
                $postdata	  = $this->ssid_common->_filter_data('job_consumed_items', $postdata);
                $check_exists = $this->db->get_where('job_consumed_items', ['job_id'=>$job_id, 'item_code'=>$postdata['item_code']])->row();
                if (!empty($postdata['item_code'])) {
                    $requested_type			= (!empty($postdata['item_type'])) ? $postdata['item_type'] : $item_type;
                    $item_type 	  			= $this->_get_item_type($requested_type);
                    $postdata['item_type'] 	= $item_type;

                    if (!empty($check_exists)) {
                        $postdata['id']  	   		= $check_exists->id;
                        $postdata['item_qty']  		= ( string ) ((!empty($postdata['item_qty'])) ? (int) ($postdata['item_qty'] + (int) $check_exists->item_qty) : (1 + (int) $check_exists->item_qty));
                        $postdata['price']  		= number_format($check_exists->price, 2);
                        $postdata['price_adjusted'] = number_format($check_exists->price_adjusted, 2);
                        $postdata['last_modified_by']= $this->ion_auth->_current_user->id;
                        ksort($postdata);
                        $exists[] = $postdata;
                    } else {
                        ## Verify item exists as part of this account
                        $item_code_exists = $this->_check_item_exists($account_id, $postdata['item_code'], $item_type);
                        if ($item_code_exists) {
                            $postdata['item_qty']  			= ( string ) ((!empty($postdata['item_qty'])) ? (int)$postdata['item_qty'] : 1);
                            $postdata['price']  			= ( string ) ((!empty($item_code_exists->sell_price)) ? number_format($item_code_exists->sell_price, 2) : '0.00');
                            $postdata['price_adjusted'] 	= ( string ) ((!empty($item_code_exists->sell_price)) ? number_format($item_code_exists->sell_price, 2) : '0.00');
                            $postdata['last_modified_by']	= null;
                            ksort($postdata);
                            $new[] 	  = $postdata;
                        } else {
                            $invalid_codes[] = $postdata['item_code'];
                        }
                    }

                    ksort($postdata);
                    $all[] 		  		   = $postdata;
                }
            }

            if (!empty($new)) {
                $this->db->insert_batch('job_consumed_items', $new);
            }

            if (!empty($exists)) {
                $this->db->update_batch('job_consumed_items', $exists, 'id');
            }

            if ($this->db->trans_status() !== false) {
                $result = $all;
                $this->session->set_flashdata('message', 'Job consumed items added successfully.');
                if (!empty($invalid_codes)) {
                    $this->session->set_flashdata('message', 'Some items were not added as they are invalid. Invalid list '.json_encode($invalid_codes));
                }
            } else {
                $this->session->set_flashdata('message', 'Your request is missing required information');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }
        return $result;
    }

    /**
    * Get all items consumed on a Job
    */
    public function get_consumed_items($account_id = false, $job_id = false, $item_type = false, $grouped = false)
    {
        $result = false;
        if ($job_id) {
            if (!empty($item_type)) {
                $item_type = $this->_get_item_type($item_type);
            }

            $sql_str = "( SELECT stock_items.item_id `item_id`, job_consumed_items.id, job_consumed_items.job_id, job_consumed_items.item_code, job_consumed_items.item_qty, job_consumed_items.price, job_consumed_items.price_adjusted, job_consumed_items.item_type, job_consumed_items.is_confirmed, stock_items.item_name
						FROM job_consumed_items JOIN stock_items ON job_consumed_items.item_code = stock_items.item_code
						WHERE job_consumed_items.job_id = '".$job_id."' ";
            if (!empty($account_id)) {
                $sql_str .= "AND stock_items.account_id = '". $account_id."' AND ( stock_items.archived != 1 OR stock_items.archived IS NULL )";
            }
            if (!empty($item_type)) {
                $sql_str .= "AND job_consumed_items.item_type = '". $item_type."' ";
            }
            $sql_str .= "ORDER BY stock_items.item_name ) ";
            $sql_str .= "UNION ALL ";
            $sql_str .= "( SELECT bom_items.item_id `item_id`, job_consumed_items.id, job_consumed_items.job_id, job_consumed_items.item_code, job_consumed_items.item_qty, job_consumed_items.price, job_consumed_items.price_adjusted, job_consumed_items.item_type, job_consumed_items.is_confirmed, bom_items.item_name
						FROM job_consumed_items JOIN bom_items ON job_consumed_items.item_code = bom_items.item_code
						WHERE job_consumed_items.job_id = '".$job_id."' ";
            if (!empty($account_id)) {
                $sql_str .= "AND bom_items.account_id = '". $account_id."' AND ( bom_items.archived != 1 OR bom_items.archived IS NULL )";
            }
            if (!empty($item_type)) {
                $sql_str .= "AND job_consumed_items.item_type = '". $item_type."' ";
            }

            $sql_str .= "ORDER BY bom_items.item_name ) ";

            $query = $this->db->query($sql_str);

            if ($query->num_rows() > 0) {
                if ($grouped) {
                    $data = [];
                    foreach ($result = $query->result() as $k => $row) {
                        $group 			= (in_array($row->item_type, ['bom','boms'])) ? 'boms' : 'stock';
                        $data[$group][] = $row;
                    }
                    $result = $data;
                } else {
                    $result = $query->result();
                }
                $this->session->set_flashdata('message', 'Job Required items found');
            } else {
                $this->session->set_flashdata('message', 'No data found');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }

    /** Add Associated Risks **/
    public function add_associated_risks($account_id = false, $job_type_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($job_type_id) && !empty($postdata)) {
            $postdata 		 = convert_to_array($postdata);
            $associated_risks= !empty($postdata['associated_risks']) ? $postdata['associated_risks'] : false;
            $associated_risks= (is_json($associated_risks)) ? json_decode($associated_risks) : $associated_risks;
            $total		= [];

            if (!empty($associated_risks)) {
                foreach ($associated_risks as $k => $val) {
                    $data = [
                        'risk_id'		=>$val,
                        'job_type_id'	=>$job_type_id,
                        'created_by'	=> $this->ion_auth->_current_user->id
                    ];

                    $check_exists = $this->db->limit(1)->get_where('job_associated_risks', $data)->row();
                    if (!$check_exists) {
                        $this->db->insert('job_associated_risks', $data);
                    }
                    $total[] = $data;
                }
            } elseif (!empty($postdata['risk_id'])) {
                $data = [
                    'risk_id'		=>$postdata['risk_id'],
                    'job_type_id'	=>$job_type_id,
                    'created_by'	=> $this->ion_auth->_current_user->id
                ];

                $check_exists = $this->db->limit(1)->get_where('job_associated_risks', $data)->row();
                if (!$check_exists) {
                    $this->db->insert('job_associated_risks', $data);
                }
                $total[] = $data;
            }

            if (!empty($total)) {
                $result = $total;
                $this->session->set_flashdata('message', 'Associated risks added successfully');
            } else {
                $this->session->set_flashdata('message', 'No associated risks found');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }
        return $result;
    }

    /** Add Associated Risks **/
    public function remove_associated_risks($account_id = false, $job_type_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($job_type_id) && !empty($postdata)) {
            $postdata 		 = convert_to_array($postdata);
            $associated_risks= !empty($postdata['associated_risks']) ? $postdata['associated_risks'] : false;
            $associated_risks= (is_json($associated_risks)) ? json_decode($associated_risks) : $associated_risks;
            $deleted		= [];

            if (!empty($associated_risks)) {
                foreach ($associated_risks as $k => $val) {
                    $data = [
                        'risk_id'=>$val,
                        'job_type_id'=>$job_type_id
                    ];

                    $check_exists = $this->db->limit(1)->get_where('job_associated_risks', $data)->row();
                    if (!empty($check_exists)) {
                        $this->db->where($data);
                        $this->db->delete('job_associated_risks');
                        $this->ssid_common->_reset_auto_increment('job_associated_risks', 'associate_id');
                    }
                    $deleted[] = $data;
                }
            } elseif (!empty($postdata['risk_id'])) {
                $data = [
                    'risk_id'=>$postdata['risk_id'],
                    'job_type_id'=>$job_type_id
                ];

                $check_exists = $this->db->limit(1)->get_where('job_associated_risks', $data)->row();
                if (!empty($check_exists)) {
                    $this->db->where($data);
                    $this->db->delete('job_associated_risks');
                    $deleted[] = $data;
                    $this->ssid_common->_reset_auto_increment('job_associated_risks', 'associate_id');
                }
            }

            if (!empty($deleted)) {
                $result = $deleted;
                $this->session->set_flashdata('message', 'Associated risks removed successfully');
            } else {
                $this->session->set_flashdata('message', 'No associated risks were removed');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }
        return $result;
    }

    /** Get a list of associated Risks to a Job **/
    public function get_associated_risks($account_id = false, $job_type_id = false, $where = false)
    {
        $result = false;

        if (!empty($job_type_id)) {
            if (!empty($account_id)) {
                #$this->db->where( 'jar.account_id', $account_id );
            }

            if (!empty($where['job_id'])) {
                // if Job id is given, get any attached dynamic risks
                $dynamic_risks = $this->get_dynamic_risks($account_id, $where['job_id'], ['result_as_array'=>1]);
            }

            #$query = $this->db->select( 'raqb.*' )
            $query = $this->db->select('jar.job_id, jar.job_type_id, raqb.*')
                ->join('risk_assessment_question_bank raqb', 'raqb.risk_id = jar.risk_id')
                ->where('jar.job_type_id', $job_type_id)
                ->get('job_associated_risks jar');

            if ($query->num_rows() > 0) {
                if (!empty($dynamic_risks)) {
                    $result = array_merge($query->result_array(), $dynamic_risks); //ensure both are arrays
                } else {
                    if (!empty($where['result_as_array'])) {
                        $result = $query->result_array();
                    } else {
                        $result = $query->result();
                    }
                }
                $this->session->set_flashdata('message', 'Associated risks found');
            } else {
                $this->session->set_flashdata('message', 'No associated risks found');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }

        return $result;
    }

    /** Get Item Type **/
    private function _get_item_type($item_type = false)
    {
        switch(strtolower($item_type)) {
            case 'bom':
            case 'boms':
            case 'sor':
            case 'sors':
                $item_type = 'bom';
                break;
            case 'stock':
            default:
                $item_type = 'stock';
                break;
        }
        return $item_type;
    }

    /** Check and verify if any item exists in the DB for this account **/
    public function _check_item_exists($account_id = false, $item_code = false, $item_type = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($item_code)) {
            switch(strtolower($item_type)) {
                case 'bom':
                    $ref_table = 'bom_items';
                    break;
                case 'stock':
                default:
                    $ref_table = 'stock_items';
                    break;
            }

            $check_exists = $this->db->where($ref_table.'.account_id', $account_id)
                ->where($ref_table.'.item_code', $item_code)
                ->where('( '.$ref_table.'.archived != 1 OR '.$ref_table.'.archived IS NULL )')
                ->limit(1)
                ->get($ref_table)
                ->row();

            if (!empty($check_exists)) {
                $check_exists->buy_price 	= (!empty($check_exists->buy_price)) ? $check_exists->buy_price : (!empty($check_exists->item_revenue) ? $check_exists->item_revenue : '0.00');
                #$check_exists->sell_price 	= ( !empty( $check_exists->sell_price ) ) ? $check_exists->sell_price : ( !empty( $check_exists->item_cost ) ? $check_exists->item_cost : '0.00' );
                $check_exists->sell_price 	= (!empty($check_exists->sell_price)) ? $check_exists->sell_price : (!empty($check_exists->item_revenue) ? $check_exists->item_revenue : '0.00');
                $result = $check_exists;
            }
        }
        return $result;
    }

    /** Resolve Job Status Group **/
    private function _resolve_status_group($status_id = false, $status_group = false)
    {
        $job_status = false;
        if (!empty($status_id)) {
            $job_status 		= $this->get_job_statuses(false, $data['status_id']);
        } elseif (!empty($status_group)) {
            switch(strtolower($status_group)) {
                case 'successful':
                    $status_group = 'successful';
                    break;

                case 'failed':
                    $status_group = 'failed';
                    break;

                case 'cancel':
                case 'cancelled':
                    $status_group = 'cancelled';
                    break;

                case 'inprogress':
                    $status_group = 'inprogress';
                    break;

                case 'assigned':
                    $status_group = 'assigned';
                    break;

                case 'un assigned':
                case 'un-assigned':
                case 'unassigned':
                    $status_group = 'unassigned';
                    break;

                case 'schedule':
                case 'scheduled':
                    $status_group = 'scheduled';
                    break;

                case 'on site':
                case 'onsite':
                    $status_group = 'onsite';
                    break;

                case 'en route':
                case 'enroute':
                    $status_group = 'enroute';
                    break;
            }

            $job_status = $this->get_job_statuses(false, false, true, $status_group);
        }

        return (!empty($job_status)) ? $job_status : false;
    }

    /**
    * Quick Job update
    */
    public function quick_update($account_id = false, $job_id = false, $job_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($job_id) && !empty($job_data)) {
            $job_status_data = [];
            if (!empty($job_data['status_id'])) {
                $job_status_data = $this->get_job_statuses(false, $job_data['status_id']);
            } elseif (!empty($job_data['status_group'])) {
                $job_status_data = $this->_resolve_status_group(false, $job_data['status_group'])[$job_data['status_group']];
            }
            $job_data = array_merge(object_to_array($job_status_data), $job_data);
            $job_data = $this->ssid_common->_filter_data('job', $job_data);
            $job_data['last_modified_by'] = $this->ion_auth->_current_user->id;

            $this->db->where('account_id', $account_id)
                ->where('job_id', $job_id)
                ->update('job', $job_data);

            if ($this->db->trans_status() !== false) {
                $this->session->set_flashdata('message', 'Job updated successfully');
                $result = $job_data;
            } else {
                $this->session->set_flashdata('message', 'Job update request failed!');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }
        return $result;
    }

    /** Commit Jobs from Routing **/
    public function commit_jobs($account_id = false, $postdata = false)
    {
        $result 	= false;
        $postdata 	= convert_to_array($postdata);
        if (!empty($account_id) && !empty($postdata['jobs'])) {
            $job_data = [ 'success'=>null, 'errors'=>null ];
            foreach ($postdata['jobs'] as $user_id => $job_info) {
                $job_info['account_id']  	= $account_id;
                $job_info['status_group']  	= 'assigned';
                $run_update = $this->quick_update($account_id, $job_info['job_id'], $job_info);
                if ($run_update) {
                    $job_data['success'][] = $job_info['job_id'];
                } else {
                    $job_data['errors'][] = $job_info['job_id'];
                }
            }
            $result = $job_data;
            if (!empty($result['success'])) {
                $this->session->set_flashdata('message', 'Job commit submission completed successfully');
            } else {
                $this->session->set_flashdata('message', 'Some or all of the submitted Jobs were not committed!');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }
        return $result;
    }

    /** Add Required Skills **/
    public function add_required_skills($account_id = false, $job_type_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($job_type_id) && !empty($postdata)) {
            $postdata 		 = convert_to_array($postdata);
            $required_skills= !empty($postdata['required_skills']) ? $postdata['required_skills'] : false;
            $required_skills= (is_json($required_skills)) ? json_decode($required_skills) : $required_skills;
            $total		= [];

            if (!empty($required_skills)) {
                foreach ($required_skills as $k => $val) {
                    $data = [
                        'skill_id'=>$val,
                        'job_type_id'=>$job_type_id,
                        'account_id'=>$account_id
                    ];

                    $check_exists = $this->db->limit(1)->get_where('job_type_required_skills', $data)->row();
                    if (!$check_exists) {
                        $this->db->insert('job_type_required_skills', $data);
                    }
                    $total[] = $data;
                }
            } elseif (!empty($postdata['skill_id'])) {
                $data = [
                    'skill_id'=>$postdata['skill_id'],
                    'job_type_id'=>$job_type_id,
                    'account_id'=>$account_id
                ];

                $check_exists = $this->db->limit(1)->get_where('job_type_required_skills', $data)->row();
                if (!$check_exists) {
                    $this->db->insert('job_type_required_skills', $data);
                }
                $total[] = $data;
            }

            if (!empty($total)) {
                $result = $total;
                $this->session->set_flashdata('message', 'Job Type required Skills added successfully');
            } else {
                $this->session->set_flashdata('message', 'Job Type required Skills not found');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }
        return $result;
    }

    /** Remove Required Skills **/
    public function remove_required_skills($account_id = false, $job_type_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($job_type_id) && !empty($postdata)) {
            $postdata 		= convert_to_array($postdata);
            $required_skills= !empty($postdata['required_skills']) ? $postdata['required_skills'] : false;
            $required_skills= (is_json($required_skills)) ? json_decode($required_skills) : $required_skills;
            $deleted		= [];

            if (!empty($required_skills)) {
                foreach ($required_skills as $k => $val) {
                    $data = [
                        'skill_id'=>$val,
                        'job_type_id'=>$job_type_id
                    ];

                    $check_exists = $this->db->limit(1)->get_where('job_type_required_skills', $data)->row();
                    if (!empty($check_exists)) {
                        $this->db->where($data);
                        $this->db->delete('job_type_required_skills');
                        $this->ssid_common->_reset_auto_increment('job_type_required_skills', 'id');
                    }
                    $deleted[] = $data;
                }
            } elseif (!empty($postdata['skill_id'])) {
                $data = [
                    'skill_id'=>$postdata['skill_id'],
                    'job_type_id'=>$job_type_id
                ];

                $check_exists = $this->db->limit(1)->get_where('job_type_required_skills', $data)->row();
                if (!empty($check_exists)) {
                    $this->db->where($data);
                    $this->db->delete('job_type_required_skills');
                    $deleted[] = $data;
                    $this->ssid_common->_reset_auto_increment('job_type_required_skills', 'id');
                }
            }

            if (!empty($deleted)) {
                $result = $deleted;
                $this->session->set_flashdata('message', 'Job Type Required Skills removed successfully');
            } else {
                $this->session->set_flashdata('message', 'No required skills were removed');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }
        return $result;
    }

    /** Get a list of Required Skills to a Job type **/
    public function get_required_skills($account_id = false, $job_type_id = false, $where = false)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($where)) {
                $where = convert_to_array($where);
                $job_type_id = (!empty($job_type_id)) ? $job_type_id : (!empty($where['job_type_id']) ? $where['job_type_id'] : false);
            }

            if (!empty($account_id)) {
                $this->db->where('jrs.account_id', $account_id);
            }

            if (!empty($job_type_id)) {
                $this->db->where('jrs.job_type_id', $job_type_id);
            }

            if (!empty($where['skill_id'])) {
                $skills_arr	= (is_array($where['skill_id'])) ? $where['skill_id'] : (is_string($where['skill_id']) ? [$where['skill_id']] : false);

                if (!empty($skills_arr) && is_array($skills_arr)) {
                    $this->db->where_in('jrs.skill_id', $skills_arr);
                }
            }

            if (isset($where['job_date']) || isset($where['include_personnel']) || (isset($where['people_skills']))) {
                if (!empty($where['people_skills'])) {
                    $people_skills = true;
                }

                if (!empty($where['job_date'])) {
                    $job_date = date('Y-m-d', strtotime($where['job_date']));
                }

                if (!empty($where['include_personnel'])) {
                    $include_personnel = true;
                }
                unset($where['job_date'], $where['include_personnel'], $where['people_skills']);
            }

            $query = $this->db->select('jrs.job_type_id, ss.*')
                ->join('job_types jt', 'jt.job_type_id = jrs.job_type_id')
                ->join('skills_bank ss', 'ss.skill_id = jrs.skill_id')
                ->get('job_type_required_skills jrs');

            if ($query->num_rows() > 0) {
                if (!empty($job_date) || !empty($include_personnel) || !empty($people_skills)) {
                    $people_data = [];
                    $data 		 = [];
                    foreach ($query->result() as $k => $row) {
                        $skilled_people 	 = $this->diary_service->get_skilled_people($account_id, $row->skill_id);
                        $row->skilled_people = (!empty($skilled_people)) ? $skilled_people : ((!empty($skilled_people)) ? $skilled_people : null);
                        if (!empty($people_skills) && !empty($row->skilled_people)) {
                            $people_data = array_merge($people_data, array_column($row->skilled_people, 'person_id'));
                        } else {
                            $data[$k] = $row;
                        }
                    }

                    if (!empty($people_data)) {
                        $data_skill 	= [];
                        foreach (array_unique($people_data) as $key => $person_id) {
                            $personal_data  = $this->people_service->get_personal_skills($account_id, $person_id);
                            $home_address	= '';
                            $home_address	.= (!empty($personal_data[0]->address_line1)) ? $personal_data[0]->address_line1.', ' : '';
                            $home_address	.= (!empty($personal_data[0]->address_town)) ? $personal_data[0]->address_town.', ' : '';
                            $home_address	.= (!empty($personal_data[0]->address_postcode)) ? $personal_data[0]->address_postcode : '';

                            $data_skill[$personal_data[0]->person_id]['person'] 		= !empty($personal_data[0]->full_name) ? $personal_data[0]->full_name : null;
                            $data_skill[$personal_data[0]->person_id]['person_id'] 		= !empty($personal_data[0]->person_id) ? $personal_data[0]->person_id : null;
                            $data_skill[$personal_data[0]->person_id]['home_postcode']	= !empty($personal_data[0]->address_postcode) ? $personal_data[0]->address_postcode : '';
                            $data_skill[$personal_data[0]->person_id]['home_address']	= $home_address;
                            $data_skill[$personal_data[0]->person_id]['personal_skills']= $personal_data;
                            $data_skill[$personal_data[0]->person_id]['availability']	= null;
                        }
                        $result = $data_skill;
                    } else {
                        $result = $data;
                    }
                } else {
                    $result = $query->result();
                }
                $this->session->set_flashdata('message', 'Job Type Required Skills found');
            } else {
                $this->session->set_flashdata('message', 'No required skills found');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }
        return $result;
    }


    /*
    * Get Jobs statistics
    */
    public function get_job_completion_stats($account_id = false, $job_date = false, $date_from = false, $date_to = false, $where = false, $limit = false, $offset = false)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($where)) {
                $where = convert_to_array($where);
            }

            $this->db->where('job.account_id', $account_id);

            $data_target = (!empty($where['data_target'])) ? $where['data_target'] : 'table';

            switch($data_target) {
                case 'bar':
                case 'graph':

                    $this->db->select(
                        'SUM(CASE WHEN status_group = "assigned"  THEN 1 ELSE 0 END) AS `Assigned`,
						SUM( CASE WHEN status_group = "enroute"  THEN 1 ELSE 0 END ) AS `En Route`,
						SUM( CASE WHEN status_group = "onsite"  THEN 1 ELSE 0 END ) AS `On Site`,
						SUM(CASE WHEN status_group = "inprogress" THEN 1 ELSE 0 END) AS `In Progress`,
						SUM(CASE WHEN ( status_group = "cancelled" OR status_group = "failed" ) THEN 1 ELSE 0 END) AS `Failed`,
						SUM(CASE WHEN status_group = "successful" THEN 1 ELSE 0 END) AS `Successful`,
						SUM(CASE WHEN ( status_group = "unassigned" OR job.status_id = "" OR job.status_id IS NULL ) THEN 1 ELSE 0 END) AS `Unassigned`,
						SUM(CASE WHEN job_id > 0 THEN 1 ELSE 0 END) AS `Total Jobs`',
                        false
                    );

                    $this->db->order_by('job_statuses.job_status')
                        ->order_by('job_statuses.job_status');

                    break;

                default:
                case 'table':

                    $this->db->select(
                        'job.account_id, ( CASE WHEN assigned_to > 0 THEN assigned_to ELSE 0 END ) AS `engineer_id`, ( CASE WHEN assigned_to > 0 THEN ( CONCAT(user.first_name, " ", user.last_name ) ) ELSE "not_set" END ) AS `engineer_id`,
						SUM( CASE WHEN status_group = "assigned"  THEN 1 ELSE 0 END ) AS `assigned`,
						SUM( CASE WHEN status_group = "enroute"  THEN 1 ELSE 0 END ) AS `en_route`,
						SUM( CASE WHEN status_group = "onsite"  THEN 1 ELSE 0 END ) AS `on_site`,
						SUM( CASE WHEN status_group = "inprogress" THEN 1 ELSE 0 END ) AS `in_progress`,
						SUM( CASE WHEN ( status_group = "cancelled" OR status_group = "failed" ) THEN 1 ELSE 0 END) AS `failed`,
						SUM( CASE WHEN status_group = "successful" THEN 1 ELSE 0 END ) AS `successful`,
						SUM( CASE WHEN ( status_group = "unassigned" OR job.status_id = "" OR job.status_id IS NULL ) THEN 1 ELSE 0 END) AS `un_assigned`,
						SUM( CASE WHEN job_id > 0 THEN 1 ELSE 0 END ) AS total_jobs',
                        false
                    );

                    $this->db->join('user', 'job.assigned_to = user.id', 'left') //This should really be linked to people but hey :)
                        ->group_by('job.assigned_to')
                        ->order_by('user.first_name, job.job_date');

                    break;
            }

            if (!empty($where['assigned_to'])) {
                $this->db->where('job.assigned_to', $where['assigned_to']);
            }

            if (!empty($where['date_from'])) {
                $date_from 	= date('Y-m-d', strtotime($where['date_from']));
                $date_to 	= (!empty($where['date_to'])) ? date('Y-m-d', strtotime($where['date_to'])) : date('Y-m-d');
                $this->db->where('job_date >=', $date_from);
                $this->db->where('job_date <=', $date_to);
            } elseif (!empty($where['job_date'])) {
                $job_date = date('Y-m-d', strtotime($where['job_date']));
                $this->db->where('job_date', $job_date);
            }

            $job = $this->db->join('job_statuses', 'job_statuses.status_id = job.status_id', 'left')
                //->where( '(job_statuses.status_group NOT IN ( "unassigned" ) )' ) //Exclude Unassigned` Jobs
                ->where('job.assigned_to > 0')
                ->get('job');

            if ($job->num_rows() > 0) {
                $data = [];

                $this->session->set_flashdata('message', 'Job completion stats found');

                switch($data_target) {
                    case 'bar':
                    case 'graph':
                        $bar_graph_data = [];
                        foreach ($job->result()[0] as $status_group => $group_total) {
                            $status_details = $this->get_job_statuses($account_id, false, false, $status_group)[0];

                            $bar_graph_data['labels'][] 	 = $status_group;
                            $bar_graph_data['values'][] 	 = ( string )$group_total;
                            $bar_graph_data['colors'][] 	 = !empty($status_details->status_colour_hex) ? $status_details->status_colour_hex : '';
                            $bar_graph_data['bg_colors'][] 	 = !empty($status_details->status_colour_hex) ? $status_details->status_colour_hex : '';
                            $bar_graph_data['status_names'][]= !empty($status_details->job_status) ? $status_details->job_status : '';
                            $bar_graph_data['descriptions'][]= !empty($status_details->job_status_desc) ? $status_details->job_status_desc : '';
                        }
                        $result = $bar_graph_data;
                        break;

                    case 'table':
                        $result 			= $job->result_array();
                        $not_set_items_key 	= array_search('not_set', array_column($result, 'EngineerName'));

                        if (!empty($not_set_items_key) || $not_set_items_key !== false) {
                            $pop_element = $result[$not_set_items_key];
                            unset($result[$not_set_items_key]);
                            $result['not_set'] = $pop_element;
                        }
                        break;
                }
            } else {
                $this->session->set_flashdata('message', 'Job completion stats not available');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }
        return $result;
    }


    /** Add Dynamic Risks **/
    public function add_dynamic_risks($account_id = false, $job_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($job_id) && !empty($postdata)) {
            $postdata 		= convert_to_array($postdata);
            $dynamic_risks	= !empty($postdata['dynamic_risks']) ? $postdata['dynamic_risks'] : false;
            $dynamic_risks	= (is_json($dynamic_risks)) ? json_decode($dynamic_risks) : $dynamic_risks;
            $total			= [];

            if (!empty($dynamic_risks)) {
                foreach ($dynamic_risks as $k => $val) {
                    $check_risk_exists = $this->db->limit(1)->get_where('risk_assessment_question_bank', [ 'account_id'=>$account_id, 'risk_id'=>$val ])->row();
                    if (!empty($check_risk_exists)) {
                        $data = [
                            'risk_id'	=> $val,
                            'job_id'	=> $job_id,
                            'created_by'=> $this->ion_auth->_current_user->id
                        ];

                        $check_exists = $this->db->limit(1)->get_where('job_dynamic_risks', $data)->row();
                        if (!$check_exists) {
                            $this->db->insert('job_dynamic_risks', $data);
                        }
                        $total[] = $data;
                    }
                }
            } elseif (!empty($postdata['risk_id'])) {
                $check_risk_exists = $this->db->limit(1)->get_where('risk_assessment_question_bank', [ 'account_id'=>$account_id, 'risk_id'=>$postdata['risk_id'] ])->row();
                if (!empty($check_risk_exists)) {
                    $data = [
                        'risk_id'	=>$postdata['risk_id'],
                        'job_id'	=>$job_id,
                        'created_by'=> $this->ion_auth->_current_user->id
                    ];
                    $check_exists = $this->db->limit(1)->get_where('job_dynamic_risks', $data)->row();
                    if (!$check_exists) {
                        $this->db->insert('job_dynamic_risks', $data);
                    }
                    $total[] = $data;
                }
            }

            if (!empty($total)) {
                $result = $total;
                $this->session->set_flashdata('message', 'Dynamic risks added successfully');
            } else {
                $this->session->set_flashdata('message', 'No dynamic risks found');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }
        return $result;
    }

    /** Add Dynamic Risks **/
    public function remove_dynamic_risks($account_id = false, $job_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($job_id) && !empty($postdata)) {
            $postdata 		= convert_to_array($postdata);
            $dynamic_risks	= !empty($postdata['dynamic_risks']) ? $postdata['dynamic_risks'] : false;
            $dynamic_risks	= (is_json($dynamic_risks)) ? json_decode($dynamic_risks) : $dynamic_risks;
            $deleted		= [];

            if (!empty($dynamic_risks)) {
                foreach ($dynamic_risks as $k => $val) {
                    $data = [
                        'risk_id'=>$val,
                        'job_id'=>$job_id
                    ];

                    $check_exists = $this->db->limit(1)->get_where('job_dynamic_risks', $data)->row();
                    if (!empty($check_exists)) {
                        $this->db->where($data);
                        $this->db->delete('job_dynamic_risks');
                        $this->ssid_common->_reset_auto_increment('job_dynamic_risks', 'associate_id');
                    }
                    $deleted[] = $data;
                }
            } elseif (!empty($postdata['risk_id'])) {
                $data = [
                    'risk_id'=>$postdata['risk_id'],
                    'job_id'=>$job_id
                ];

                $check_exists = $this->db->limit(1)->get_where('job_dynamic_risks', $data)->row();
                if (!empty($check_exists)) {
                    $this->db->where($data);
                    $this->db->delete('job_dynamic_risks');
                    $deleted[] = $data;
                    $this->ssid_common->_reset_auto_increment('job_dynamic_risks', 'associate_id');
                }
            }

            if (!empty($deleted)) {
                $result = $deleted;
                $this->session->set_flashdata('message', 'Dynamic risks removed successfully');
            } else {
                $this->session->set_flashdata('message', 'No dynamic risks were removed');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }
        return $result;
    }

    /** Get a list of dynamic Risks to a Job **/
    public function get_dynamic_risks($account_id = false, $job_id = false, $where = false)
    {
        $result = false;

        if (!empty($job_id)) {
            if (!empty($account_id)) {
                #$this->db->where( 'jdr.account_id', $account_id );
            }

            $query = $this->db->select('jdr.job_id, jdr.job_type_id, raqb.*')
                ->join('risk_assessment_question_bank raqb', 'raqb.risk_id = jdr.risk_id')
                ->where('jdr.job_id', $job_id)
                ->get('job_dynamic_risks jdr');

            if ($query->num_rows() > 0) {
                if (!empty($where['result_as_array'])) {
                    $result = $query->result_array();
                } else {
                    $result = $query->result();
                }
                $this->session->set_flashdata('message', 'Dynamic risks found');
            } else {
                $this->session->set_flashdata('message', 'No dynamic risks found');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }

        return $result;
    }

    /** Create a new Fail Code record **/
    public function create_fail_code($account_id = false, $fail_code_data = false)
    {
        $result = null;

        if (!empty($account_id) && !empty($fail_code_data)) {
            foreach ($fail_code_data as $col => $value) {
                if ($col == 'fail_code_text') {
                    $data['fail_code_ref'] 		= strtolower(strip_all_whitespace($value));
                    $data['fail_code_group']	= ucwords(strtolower($value));
                }
                $data[$col] = $value;
            }

            if (!empty($data['override_existing']) && !empty($data['fail_code_id'])) {
                $override_existing = true;
                $check_exists = $this->db->select('job_fail_codes.*', false)
                    ->where('job_fail_codes.account_id', $account_id)
                    ->where('job_fail_codes.fail_code_id', $data['fail_code_id'])
                    ->get('job_fail_codes')->row();
            } else {
                unset($data['fail_code_id']);
                $check_exists = $this->db->select('job_fail_codes.*', false)
                    ->where('job_fail_codes.account_id', $account_id)
                    ->where('( job_fail_codes.fail_code_text = "'.$data['fail_code_text'].'" OR job_fail_codes.fail_code_ref = "'.$data['fail_code_ref'].'" )')
                    ->limit(1)
                    ->get('job_fail_codes')
                    ->row();
            }

            $data = $this->ssid_common->_filter_data('job_fail_codes', $data);

            if (!empty($check_exists)) {
                if (!empty($override_existing)) {
                    $data['last_modified_by'] = $this->ion_auth->_current_user->id;
                    $this->db->where('fail_code_id', $check_exists->fail_code_id)
                        ->update('job_fail_codes', $data);

                    $this->_set_fail_code($account_id, $check_exists->fail_code_id);

                    $this->session->set_flashdata('message', 'This Fail Code already exists, record has been updated successfully.');
                    $result = $check_exists;
                } else {
                    $this->session->set_flashdata('message', 'This Fail Code already exists, Would you like to override it?');
                    $this->session->set_flashdata('already_exists', 'True');
                    $result = $check_exists;
                }
            } else {
                $data['created_by'] 	= $this->ion_auth->_current_user->id;
                $this->db->insert('job_fail_codes', $data);
                $data['fail_code_id'] 	= $this->db->insert_id();

                $this->_set_fail_code($account_id, $data['fail_code_id']);

                $data = $this->get_fail_codes($account_id, $data['fail_code_id']);

                $this->session->set_flashdata('message', 'New Fail Code created successfully.');
                $result = !empty($data->records) ? $data->records : (!empty($data) ? $data : false);
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }

        return $result;
    }

    /** Update an existing Fail Code **/
    public function update_fail_code($account_id = false, $fail_code_id = false, $update_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($fail_code_id)  && !empty($update_data)) {
            $ref_condition = [ 'account_id'=>$account_id, 'fail_code_id'=>$fail_code_id ];
            $update_data   = $this->ssid_common->_data_prepare($update_data);
            $update_data   = $this->ssid_common->_filter_data('job_fail_codes', $update_data);
            $record_pre_update = $this->db->get_where('job_fail_codes', [ 'account_id'=>$account_id, 'fail_code_id'=>$fail_code_id ])->row();

            if (!empty($record_pre_update)) {
                $update_data['fail_code_ref'] 	= strtolower(strip_all_whitespace($update_data['fail_code_text']));
                $fail_code_where 				= '( job_fail_codes.fail_code_text = "'.$update_data['fail_code_text'].'" OR job_fail_codes.fail_code_ref = "'. $update_data['fail_code_ref'] .'" )';
                ;

                $check_conflict = $this->db->select('fail_code_id', false)
                    ->where('job_fail_codes.account_id', $account_id)
                    ->where('job_fail_codes.fail_code_id !=', $fail_code_id)
                    ->where($fail_code_where)
                    ->limit(1)
                    ->get('job_fail_codes')
                    ->row();

                if (!$check_conflict) {
                    $update_data['last_modified_by'] = $this->ion_auth->_current_user->id;
                    $this->db->where($ref_condition)
                        ->update('job_fail_codes', $update_data);

                    $this->_set_fail_code($account_id, $fail_code_id);

                    $updated_record = $this->get_fail_codes($account_id, $fail_code_id);
                    $result 		= (!empty($updated_record->records)) ? $updated_record->records : (!empty($updated_record) ? $updated_record : false);

                    $this->session->set_flashdata('message', 'Fail Code updated successfully');
                    return $result;
                } else {
                    $this->session->set_flashdata('message', 'This Fail Code already exists for your account. Request aborted');
                    return false;
                }
            } else {
                $this->session->set_flashdata('message', 'This Fail Code record does not exist or does not belong to you.');
                return false;
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing requireed information.');
        }
        return $result;
    }

    /*
    *	Get list of Fail codes list and search though it
    */
    public function get_fail_codes($account_id = false, $fail_code_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select('job_fail_codes.*, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false)
                ->join('user creater', 'creater.id = job_fail_codes.created_by', 'left')
                ->join('user modifier', 'modifier.id = job_fail_codes.last_modified_by', 'left')
                ->where('job_fail_codes.is_active', 1)
                ->where('job_fail_codes.account_id', $account_id);

            $where = $raw_where = convert_to_array($where);

            if (!empty($fail_code_id) || isset($where['fail_code_id'])) {
                $fail_code_id	= (!empty($fail_code_id)) ? $fail_code_id : $where['fail_code_id'];
                if (!empty($fail_code_id)) {
                    $row = $this->db->get_where('job_fail_codes', ['fail_code_id'=>$fail_code_id ])->row();

                    if (!empty($row)) {
                        $result = ( object ) ['records'=>$row];
                        $this->session->set_flashdata('message', 'Fail Code data found');
                        return $result;
                    } else {
                        $this->session->set_flashdata('message', 'Fail Code data not found');
                        return false;
                    }
                }
                unset($where['fail_code_id'], $where['fail_code_ref']);
            }

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->fail_codes_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->fail_codes_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                if (isset($where['fail_code'])) {
                    if (!empty($where['fail_code'])) {
                        $fail_code_ref = strtoupper(strip_all_whitespace($where['fail_code']));
                        $this->db->where('( job_fail_codes.fail_code = "'.$where['fail_code'].'" OR job_fail_codes.fail_code_ref = "'.$fail_code_ref.'" )');
                    }
                    unset($where['fail_code']);
                }

                if (isset($where['grouped'])) {
                    if (!empty($where['grouped'])) {
                        $grouped_results = 1;
                    }
                    unset($where['grouped']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            if (!empty($order_by)) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('fail_code_id DESC, fail_code');
            }

            $query = $this->db->get('job_fail_codes');

            if ($query->num_rows() > 0) {
                if (!empty($grouped_results)) {
                    $result_data = [];
                    foreach ($query->result() as $k => $row) {
                        $result_data[$row->fail_code_group][] = $row;
                    }
                } else {
                    $result_data = $query->result();
                }

                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $result->records 			= $result_data;
                $counters 					= $this->fail_codes_totals($account_id, $search_term, $raw_where);
                $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
                $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
                $result->counters->limit  	= (!empty($apply_limit)) ? $limit : $result->counters->total;
                $result->counters->offset 	= $offset;

                $this->session->set_flashdata('message', 'Fail Codes data found');
            } else {
                $this->session->set_flashdata('message', 'There\'s currently no Fail codes setup for your Account');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }

    /** Get Fail Codes lookup counts **/
    public function fail_codes_totals($account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('job_fail_codes.fail_code_id', false)
                ->where('job_fail_codes.is_active', 1)
                ->where('job_fail_codes.account_id', $account_id);

            $where = $raw_where = convert_to_array($where);

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->fail_codes_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->fail_codes_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                if (isset($where['fail_code'])) {
                    if (!empty($where['fail_code'])) {
                        $fail_code_ref = strtoupper(strip_all_whitespace($where['fail_code']));
                        $this->db->where('( job_fail_codes.fail_code = "'.$where['fail_code'].'" OR job_fail_codes.fail_code_ref = "'.$fail_code_ref.'" )');
                    }
                    unset($where['fail_code']);
                }

                if (isset($where['grouped'])) {
                    if (!empty($where['grouped'])) {
                        $grouped_results = 1;
                    }
                    unset($where['grouped']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $query 			  = $this->db->from('job_fail_codes')->count_all_results();
            $results['total'] = !empty($query) ? $query : 0;
            $limit 			  = (!empty($apply_limit)) ? $limit : $results['total'];
            $results['pages'] = !empty($query) ? ceil($query / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }

    /** Set Fail Code **/
    public function _set_fail_code($account_id = false, $fail_code_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($fail_code_id)) {
            $fail_code = 'F'.$account_id.'00'.$fail_code_id;
            $this->db->where('account_id', $account_id)
                ->where('fail_code_id', $fail_code_id)
                ->update('job_fail_codes', [ 'fail_code'=>$fail_code ]);

            $result = ($this->db->trans_status() !== false || ($this->db->affected_rows() > 0)) ? true : false;
        }
        return $result;
    }

    /** Consume Slots against Engineer **/
    public function consume_slots($account_id = false, $person_id = false, $data = false)
    {
        $result 	  = false;
        if (!empty($account_id) && !empty($person_id) && !empty($data)) {
            $job_date   = date('Y-m-d', strtotime($data['job_date']));
            $slots   	= !empty($data['slots']) ? $data['slots'] : 1;
            $params		= [ 'account_id'=>$account_id, 'user_id'=>$person_id, 'ref_date'=>$job_date ];
            $resource   = $this->db->get_where('diary_resource_schedule', $params)->row();
            if ($resource) {
                $update_data = [
                    'consumed_slots'	=>($data['action'] == 'add') ? ($resource->consumed_slots + $slots) : ($resource->consumed_slots - $slots),
                    'last_modified_by'	=>$this->ion_auth->_current_user->id
                ];
                $this->db->where('resource_id', $resource->resource_id);
                $query = $this->db->update('diary_resource_schedule', $update_data);
                $result = true;
            }
        }
        return $result;
    }

    /** Get Jobs By Status **/
    public function get_jobs_by_status($account_id = false, $status_group = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        $this->db->select('job.*, job_types.*, job_statuses.job_status, job_statuses.status_group, fc.fail_code, fc.fail_code_text, fc.fail_code_desc, fc.fail_code_group, audit_categories.category_name, CONCAT(user.first_name," ",user.last_name) `assignee`, addrs.main_address_id,addrs.addressline1 `address_line_1`, addrs.addressline2 `address_line_2`,addrs.addressline3 `address_line_3`,addrs.posttown `address_city`,addrs.county `address_county`, addrs.postcode `address_postcode`, postcode_area, postcode_district, postcode_sector, addrs.summaryline `summaryline`, CONCAT( addrs.addressline1,", ",addrs.addressline2,", ",addrs.posttown, ", ",addrs.posttown,", ",addrs.postcode ) `short_address`, addrs.organisation `address_business_name`', false)
            ->join('addresses addrs', 'addrs.main_address_id = job.address_id', 'left')
            ->join('job_types', 'job_types.job_type_id = job.job_type_id', 'left')
            ->join('job_statuses', 'job_statuses.status_id = job.status_id', 'left')
            ->join('audit_categories', 'audit_categories.category_id = job.category_id', 'left')
            ->join('job_fail_codes fc', 'fc.fail_code_id = job.fail_code_id', 'left')
            ->join('user', 'user.id = job.assigned_to', 'left')
            ->where('job.archived !=', 1);

        if (!empty($account_id)) {
            $this->db->where('job.account_id', $account_id);
        }

        $where = $raw_where = !empty($where) ? convert_to_array($where) : false;
        $status_group 		= ($status_group) ? $status_group : (!empty($where['status_group']) ? $where['status_group'] : false);

        if (!empty($status_group)) {
            if (is_array($status_group)) {
                $this->db->where_in('job_statuses.status_group', $status_group);
            } else {
                $this->db->where('job_statuses.status_group', $status_group);
            }
            unset($where['status_group']);
        }

        if (!empty($where)) {
            if (isset($where['assigned_to'])) {
                if (!empty($where['assigned_to'])) {
                    $this->db->where('job.assigned_to', $where['assigned_to']);
                }
                unset($where['assigned_to']);
            }

            if (!empty($where['job_date'])) {
                $job_date 	= date('Y-m-d', strtotime($where['job_date']));
                $this->db->where('job_date', $job_date);
            } elseif (!empty($where['date_from'])) {
                $date_from 	= date('Y-m-d', strtotime($where['date_from']));
                $date_to 	= (!empty($where['date_to'])) ? date('Y-m-d', strtotime($where['date_to'])) : date('Y-m-d');
                $this->db->where('job_date >=', $date_from);
                $this->db->where('job_date <=', $date_to);
            } elseif (!empty($where['job_date'])) {
                $job_date = date('Y-m-d', strtotime($where['job_date']));
                $this->db->where('job_date', $job_date);
            }

            if (isset($where['grouped'])) {
                if (!empty($where['grouped'])) {
                    $grouped = true;
                }
                unset($where['grouped']);
            }

            if (isset($where['customer_details'])) {
                if (!empty($where['customer_details'])) {
                    $customer_details = $where['customer_details'];
                }
                unset($where['customer_details']);
            }





            unset($where['date_from'], $where['date_to']);
        }

        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }

        $job = $this->db->order_by('job_id desc, job_date desc, job_type')
            ->get('job');

        if ($job->num_rows() > 0) {
            $data = [];
            if (!empty($grouped)) {
                foreach ($job->result() as $key => $row) {
                    $data[$row->status_group][] = $row;
                }
            } else {
                $data = $job->result();
            }

            if ((!empty($customer_details)) && ($customer_details == 'yes')) {
                foreach ($data as $key => $row) {
                    if (!empty($row->customer_id)) {
                        $customer_details 				= $this->customer_service->get_customers($account_id, $row->customer_id);
                        $data[$key]->customer_details  	= !empty($customer_details) ? $customer_details : null;
                    } else {
                        $data[$key]->customer_details  	= null;
                    }
                }
            }

            $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
            $result->records 			= $data;
            $counters 					= $this->get_total_jobs_by_status($account_id, $status_group, $raw_where, $limit);
            $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
            $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
            $result->counters->limit  	= (!empty($limit)) ? $limit : $result->counters->total;
            $result->counters->offset 	= $offset;

            $this->session->set_flashdata('message', 'Job records found');
        } else {
            $this->session->set_flashdata('message', 'Job record(s) not found');
        }

        return $result;
    }


    /*
    * Get total jobs_by_status
    */
    public function get_total_jobs_by_status($account_id = false, $status_group = false, $where = false, $limit = false)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select('job.id', false)
                ->join('job_types', 'job_types.job_type_id = job.job_type_id', 'left')
                ->join('job_statuses', 'job_statuses.status_id = job.status_id', 'left')
                ->join('user', 'user.id = job.assigned_to', 'left')
                ->where('job.archived !=', 1);

            if (!empty($account_id)) {
                $this->db->where('job.account_id', $account_id);
            }

            $where = $raw_where = !empty($where) ? convert_to_array($where) : false;
            $status_group 		= ($status_group) ? $status_group : (!empty($where['status_group']) ? $where['status_group'] : false);

            if (!empty($status_group)) {
                $this->db->where('job_statuses.status_group', $status_group);
                unset($where['status_group']);
            }

            if (!empty($where)) {
                if (isset($where['assigned_to'])) {
                    if (!empty($where['assigned_to'])) {
                        $this->db->where('job.assigned_to', $where['assigned_to']);
                    }
                    unset($where['assigned_to']);
                }

                if (!empty($where['job_date'])) {
                    $job_date 	= date('Y-m-d', strtotime($where['job_date']));
                    $this->db->where('job_date', $job_date);
                } elseif (!empty($where['date_from'])) {
                    $date_from 	= date('Y-m-d', strtotime($where['date_from']));
                    $date_to 	= (!empty($where['date_to'])) ? date('Y-m-d', strtotime($where['date_to'])) : date('Y-m-d');
                    $this->db->where('job_date >=', $date_from);
                    $this->db->where('job_date <=', $date_to);
                } elseif (!empty($where['job_date'])) {
                    $job_date = date('Y-m-d', strtotime($where['job_date']));
                    $this->db->where('job_date', $job_date);
                }

                if (isset($where['grouped'])) {
                    if (!empty($where['grouped'])) {
                        $grouped = true;
                    }
                    unset($where['grouped']);
                }

                unset($where['date_from'], $where['date_to']);
            }

            $query 				= $this->db->from('job')->count_all_results();
            $limit				= ($limit > 0) ? $limit : $query;
            $results['total'] 	= !empty($query) ? $query : 0;
            $results['pages'] 	= !empty($query) ? ceil($query / $limit) : 0;
            return json_decode(json_encode($results));
        }

        return $result;
    }


    /** Update Consumed Stock/BOM Items on a Job **/
    public function update_consumed_items($account_id = false, $job_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($job_id) && !empty($postdata)) {
            $postdata 			= convert_to_array($postdata);
            $consumed_items 	= !empty($postdata['consumed_items']) ? $postdata['consumed_items'] : false;
            $consumed_items 	= (is_json($consumed_items)) ? json_decode($consumed_items) : $consumed_items;

            unset($postdata['consumed_items']);
            $update_items_data  = [];
            if (!empty($consumed_items)) {
                foreach ($consumed_items as $key => $item) {
                    $check_exists = $this->db->get_where('job_consumed_items', ['job_id'=>$job_id, 'id'=>$item['id']])->row();
                    $update_items_data[] = [
                        'id'			=>$item['id'],
                        'job_id'		=>$job_id,
                        'item_code'		=> $check_exists->item_code,
                        #'item_qty'		=> ( string ) ( $check_exists->item_qty + $item['item_qty'] ),
                        'item_qty'		=> ( string ) $item['item_qty'],
                        'price'			=> ( string ) (!empty($postdata['price']) ? $postdata['price'] : $check_exists->price),
                        'price_adjusted'=> ( string ) (!empty($postdata['price']) ? $postdata['price'] : $check_exists->price_adjusted),
                    ];
                }
            } elseif (!empty($postdata['id'])) {
                $postdata	  = $this->ssid_common->_filter_data('job_consumed_items', $postdata);
                $check_exists = $this->db->get_where('job_consumed_items', ['job_id'=>$job_id, 'id'=>$postdata['id']])->row();
                if (!empty($check_exists)) {
                    $update_items_data[] = [
                        'id'			=> $postdata['id'],
                        'job_id'		=> $job_id,
                        'item_code'		=> $check_exists->item_code,
                        #'item_qty'		=> ( string ) ( $check_exists->item_qty + $postdata['item_qty'] ),
                        'item_qty'		=> ( string ) $postdata['item_qty'],
                        'price'			=> ( string ) (!empty($postdata['price']) ? $postdata['price'] : $check_exists->price),
                        'price_adjusted'=> ( string ) (!empty($postdata['price']) ? $postdata['price'] : $check_exists->price_adjusted),

                    ];
                }
            }

            if (!empty($update_items_data)) {
                $this->db->where('job_id', $job_id)
                    ->update_batch('job_consumed_items', $update_items_data, 'id');

                if ($this->db->trans_status() !== false) {
                    $this->session->set_flashdata('message', 'Item(s) updated successfully');
                    $result = $update_items_data;
                }
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }
        return $result;
    }


    /** Delete Stock/BOM Items from a Job **/
    public function delete_consumed_items($account_id = false, $job_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($job_id) && !empty($postdata)) {
            $postdata 		= convert_to_array($postdata);
            $consumed_items = !empty($postdata['consumed_items']) ? $postdata['consumed_items'] : false;
            $consumed_items= (is_json($consumed_items)) ? json_decode($consumed_items) : $consumed_items;
            $deleted		= [];

            if (!empty($consumed_items)) {
                foreach ($consumed_items as $k => $val) {
                    $data = [
                        'id'		=> $val,
                        'account_id'=> $account_id,
                        'job_id'	=> $job_id
                    ];

                    $check_exists = $this->db->limit(1)->get_where('job_consumed_items', $data)->row();
                    if (!empty($check_exists)) {
                        $this->db->where($data);
                        $this->db->delete('job_consumed_items');
                        $this->ssid_common->_reset_auto_increment('job_consumed_items', 'id');
                        $deleted[] = $data;
                    }
                }
            } elseif (!empty($postdata['id'])) {
                $data = [
                    'id'		=> $postdata['id'],
                    'account_id'=> $account_id,
                    'job_id'	=> $job_id
                ];

                $check_exists = $this->db->limit(1)->get_where('job_consumed_items', $data)->row();
                if (!empty($check_exists)) {
                    $this->db->where($data);
                    $this->db->delete('job_consumed_items');
                    $deleted[] = $data;
                    $this->ssid_common->_reset_auto_increment('job_consumed_items', 'id');
                }
            }

            if (!empty($deleted)) {
                $result = $deleted;
                $this->session->set_flashdata('message', 'Consumed item(s) removed successfully');
            } else {
                $this->session->set_flashdata('message', 'No consumed items were removed');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }
        return $result;
    }


    /** Create a new Schedule Frequency record **/
    public function create_schedule_frequency($account_id = false, $frequency_data = false)
    {
        $result = null;

        if (!empty($account_id) && !empty($frequency_data)) {
            foreach ($frequency_data as $col => $value) {
                if ($col == 'frequency_name') {
                    $data['frequency_ref'] 	= strtolower(strip_all_whitespace($value));
                    $data['frequency_group']= ucwords(strtolower($value));
                }
                $data[$col] = $value;
            }

            if (!empty($data['override_existing']) && !empty($data['frequency_id'])) {
                $override_existing = true;
                $check_exists = $this->db->select('schedule_frequencies.*', false)
                    ->where('schedule_frequencies.account_id', $account_id)
                    ->where('schedule_frequencies.frequency_id', $data['frequency_id'])
                    ->get('schedule_frequencies')->row();
            } else {
                unset($data['frequency_id']);
                $check_exists = $this->db->select('schedule_frequencies.*', false)
                    ->where('schedule_frequencies.account_id', $account_id)
                    ->where('( schedule_frequencies.frequency_name = "'.$data['frequency_name'].'" OR schedule_frequencies.frequency_ref = "'.$data['frequency_ref'].'" )')
                    ->limit(1)
                    ->get('schedule_frequencies')
                    ->row();
            }

            $data = $this->ssid_common->_filter_data('schedule_frequencies', $data);

            if (!empty($check_exists)) {
                if (!empty($override_existing)) {
                    $data['last_modified_by'] = $this->ion_auth->_current_user->id;
                    $this->db->where('frequency_id', $check_exists->frequency_id)
                        ->update('schedule_frequencies', $data);

                    $this->session->set_flashdata('message', 'This Schedule Frequency already exists, record has been updated successfully.');
                    $result = $check_exists;
                } else {
                    $this->session->set_flashdata('message', 'This Schedule Frequency already exists, Would you like to override it?');
                    $this->session->set_flashdata('already_exists', 'True');
                    $result = $check_exists;
                }
            } else {
                $data['created_by'] = $this->ion_auth->_current_user->id;
                $this->db->insert('schedule_frequencies', $data);
                $data['frequency_id'] = ( string ) $this->db->insert_id();

                $this->session->set_flashdata('message', 'New Schedule Frequency created successfully.');
                $result = $data;
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }

        return $result;
    }


    /** Update an existing Schedule Frequency **/
    public function update_schedule_frequency($account_id = false, $frequency_id = false, $update_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($frequency_id)  && !empty($update_data)) {
            $ref_condition = [ 'account_id'=>$account_id, 'frequency_id'=>$frequency_id ];
            $update_data   = $this->ssid_common->_data_prepare($update_data);
            $update_data   = $this->ssid_common->_filter_data('schedule_frequencies', $update_data);
            $record_pre_update = $this->db->get_where('schedule_frequencies', [ 'account_id'=>$account_id, 'frequency_id'=>$frequency_id ])->row();

            if (!empty($record_pre_update)) {
                $update_data['frequency_ref'] 	= strtolower(strip_all_whitespace($update_data['frequency_name']));
                $schedule_frequency_where 		= '( schedule_frequencies.frequency_name = "'.$update_data['frequency_name'].'" OR schedule_frequencies.frequency_ref = "'. $update_data['frequency_ref'] .'" )';
                ;

                $check_conflict = $this->db->select('frequency_id', false)
                    ->where('schedule_frequencies.account_id', $account_id)
                    ->where('schedule_frequencies.frequency_id !=', $frequency_id)
                    ->where($schedule_frequency_where)
                    ->limit(1)
                    ->get('schedule_frequencies')
                    ->row();

                if (!$check_conflict) {
                    $update_data['last_modified_by'] = $this->ion_auth->_current_user->id;
                    $this->db->where($ref_condition)
                        ->update('schedule_frequencies', $update_data);

                    $updated_record = $this->get_schedule_frequencies($account_id, $frequency_id);
                    $result 		= (!empty($updated_record->records)) ? $updated_record->records : (!empty($updated_record) ? $updated_record : false);

                    $this->session->set_flashdata('message', 'Schedule Frequency updated successfully');
                    return $result;
                } else {
                    $this->session->set_flashdata('message', 'This Schedule Frequency already exists for your account. Request aborted');
                    return false;
                }
            } else {
                $this->session->set_flashdata('message', 'This Schedule Frequency record does not exist or does not belong to you.');
                return false;
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }
        return $result;
    }


    /*
    *	Get list of  list and search though it
    */
    public function get_schedule_frequencies($account_id = false, $frequency_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select('schedule_frequencies.*, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false)
                ->join('user creater', 'creater.id = schedule_frequencies.created_by', 'left')
                ->join('user modifier', 'modifier.id = schedule_frequencies.last_modified_by', 'left')
                ->where('schedule_frequencies.is_active', 1)
                ->where('schedule_frequencies.account_id', $account_id);

            $where = $raw_where = convert_to_array($where);

            if (!empty($frequency_id) || isset($where['frequency_id'])) {
                $frequency_id	= (!empty($frequency_id)) ? $frequency_id : $where['frequency_id'];
                if (!empty($frequency_id)) {
                    $row = $this->db->get_where('schedule_frequencies', ['frequency_id'=>$frequency_id ])->row();

                    if (!empty($row)) {
                        $result = ( object ) ['records'=>$row];
                        $this->session->set_flashdata('message', 'Schedule Frequency data found');
                        return $result;
                    } else {
                        $this->session->set_flashdata('message', 'Schedule Frequency data not found');
                        return false;
                    }
                }
                unset($where['frequency_id'], $where['frequency_ref']);
            }

            if (!empty($search_term)) {
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->schedule_frequencies_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->schedule_frequencies_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                if (isset($where['frequency_name'])) {
                    if (!empty($where['frequency_name'])) {
                        $frequency_ref = strtoupper(strip_all_whitespace($where['frequency_name']));
                        $this->db->where('( schedule_frequencies.frequency_name = "'.$where['frequency_name'].'" OR schedule_frequencies.frequency_ref = "'.$frequency_ref.'" )');
                    }
                    unset($where['frequency_name']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            if (!empty($order_by)) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('frequency_id, frequency_name');
            }

            $query = $this->db->get('schedule_frequencies');

            if ($query->num_rows() > 0) {
                $result_data = $query->result();

                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $result->records 			= $result_data;
                $counters 					= $this->schedule_frequencies_totals($account_id, $search_term, $raw_where);
                $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
                $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
                $result->counters->limit  	= (!empty($apply_limit)) ? $limit : $result->counters->total;
                $result->counters->offset 	= $offset;

                $this->session->set_flashdata('message', 'Schedule Frequencies data found');
            } else {
                $this->session->set_flashdata('message', 'No schedule frequencies data found!');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }

    /** Get Schedule Frequencies lookup counts **/
    public function schedule_frequencies_totals($account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('schedule_frequencies.frequency_id', false)
                ->where('schedule_frequencies.is_active', 1)
                ->where('schedule_frequencies.account_id', $account_id);

            $where = $raw_where = convert_to_array($where);

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->schedule_frequencies_search_fields as $k => $field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->schedule_frequencies_search_fields as $k => $field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                if (isset($where['frequency_name'])) {
                    if (!empty($where['frequency_name'])) {
                        $frequency_ref = strtoupper(strip_all_whitespace($where['frequency_name']));
                        $this->db->where('( schedule_frequencies.frequency_name = "'.$where['frequency_name'].'" OR schedule_frequencies.frequency_ref = "'.$frequency_ref.'" )');
                    }
                    unset($where['frequency_name']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $query 			  = $this->db->from('schedule_frequencies')->count_all_results();
            $results['total'] = !empty($query) ? $query : 0;
            $limit 			  = ($limit > 0) ? $limit : $results['total'];
            $results['pages'] = !empty($query) ? ceil($query / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }

    /** Generate Schedule Ref **/
    private function generate_schedule_ref($account_id = false, $data = false)
    {
        if (!empty($account_id) && !empty($data)) {
            $schedule_ref = $account_id;
            $schedule_ref .= (!empty($data['schedule_name'])) ? strip_all_whitespace($data['schedule_name']) : '';
            $schedule_ref .= (!empty($data['contract_id'])) ? $data['contract_id'] : '';
            $schedule_ref .= (!empty($data['site_id'])) ? $data['site_id'] : '';
            $schedule_ref .= (!empty($data['location_id'])) ? $data['location_id'] : '';
            $schedule_ref .= (!empty($data['asset_id'])) ? $data['asset_id'] : '';
            $schedule_ref .= (!empty($data['evidoc_type_id'])) ? $data['evidoc_type_id'] : '';
            $schedule_ref .= (!empty($data['job_type_id'])) ? $data['job_type_id'] : '';
            $schedule_ref .= (!empty($data['first_activity_due_date'])) ? date('dmY', strtotime($data['first_activity_due_date'])) : '';
            $schedule_ref .= (!empty($data['frequency_id'])) ? $data['frequency_id'] : '';
            $schedule_ref .= (!empty($data['category_id'])) ? $data['category_id'] : '';
        } else {
            $schedule_ref = $account_id.$this->ssid_common->generate_random_password();
        }
        return strtoupper($schedule_ref);
    }

    /** Create a new Schedule record record **/
    public function create_schedules($account_id = false, $frequency_id = false, $schedules_data = false)
    {
        $result = null;

        if (!empty($account_id) && !empty($frequency_id) && !empty($schedules_data)) {
            ini_set('memory_limit', '384M');

            $frequency_data 		= $this->db->get_where('schedule_frequencies', [ 'account_id'=>$account_id, 'frequency_id'=>$frequency_id ])->row();
            $schedules_data 		= convert_to_array($schedules_data);
            $schedule_activities 	= !empty($schedules_data['schedule_activities']) ? convert_to_array($schedules_data['schedule_activities']) : $schedules_data;
            $schedule_ref			= $this->generate_schedule_ref($account_id, array_merge($schedules_data, ['frequency_id'=>$frequency_id]));

            if (!empty($schedule_activities['multi'])) {
                $multi_asset_types 	= $schedule_activities['multi'];
                $multi_asset_types	= (is_string($multi_asset_types)) ? json_decode($multi_asset_types) : $multi_asset_types;
                $multi_asset_types	= (is_object($multi_asset_types)) ? object_to_array($multi_asset_types) : $multi_asset_types;
            } else {
                $multiple_activities 	= !empty($schedules_data['schedule_activities']) ? $schedules_data['schedule_activities'] : false;
                $multiple_activities	= (is_string($multiple_activities)) ? json_decode($multiple_activities) : $multiple_activities;
                $multiple_activities	= (is_object($multiple_activities)) ? object_to_array($multiple_activities) : $multiple_activities;
                unset($schedules_data['schedule_activities']);
            }

            $data 					 		= $this->ssid_common->_filter_data('schedules', $schedules_data);
            $data['schedule_ref'] 	 		= $schedule_ref;
            $data['schedule_status']		= 'Pending';
            $data['activities_total']		= !empty($frequency_data->activities_required) ? ( string ) $frequency_data->activities_required : '1';
            $data['activities_pending']		= $data['activities_total'];
            $data['first_activity_due_date']= date('Y-m-d', strtotime($data['first_activity_due_date']));
            $expiry_date 							= date('Y-m-d', strtotime($data['first_activity_due_date']. ' + 1 year')). ' 23:59:59';
            $data['expiry_date'] 			= date('Y-m-d H:i:s', strtotime($expiry_date. ' - 1 day'));

            $check_exists = $this->db->select('schedules.schedule_id', false)
                ->where('schedules.account_id', $account_id)
                ->where([ 'frequency_id'=>$frequency_id, 'schedule_ref'=>$schedule_ref ])
                ->limit(1)
                ->get('schedules')
                ->row();

            if (!empty($check_exists)) {
                $data['last_modified_by'] 	= $this->ion_auth->_current_user->id;
                $this->db->where('schedule_id', $check_exists->schedule_id)
                    ->update('schedules', $data);
                $record = $this->get_schedules($account_id, false, [ 'schedule_id'=>$check_exists->schedule_id ]);
            } else {
                $data['created_by'] 		= $this->ion_auth->_current_user->id;
                $this->db->insert('schedules', $data);
                $record = $this->get_schedules($account_id, false, [ 'schedule_id'=>$this->db->insert_id() ]);
            }

            if (!empty($record)) {
                ## Create individual items
                if (!empty($multiple_activities)) {
                    $activities = $this->create_schedule_activities($account_id, $record->schedule_id, [ 'schedule_activities' => $multiple_activities ], $schedules_data);
                }

                ## Create Multiple Activities
                if (!empty($multi_asset_types)) {
                    $activities = $this->create_multiple_schedule_activities($account_id, $record->schedule_id, [ 'schedule_activities' => $multi_asset_types ], $schedules_data);
                    $record->activities_data = $activities;
                }

                $result = $record;
                $this->session->set_flashdata('message', 'Schedule record(s) created successfully.');
            } else {
                $this->session->set_flashdata('message', 'Error! There was a problem completing your request, please check your submitted data.');
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }

        return $result;
    }

    /** Update an existing Schedule record **/
    public function update_site_schedule($account_id = false, $schedule_id = false, $update_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($schedule_id)  && !empty($update_data)) {
            $ref_condition = [ 'account_id'=>$account_id, 'schedule_id'=>$schedule_id ];
            $update_data   = $this->ssid_common->_data_prepare($update_data);
            $update_data   = $this->ssid_common->_filter_data('schedules', $update_data);
            $record_pre_update = $this->db->get_where('schedules', [ 'account_id'=>$account_id, 'schedule_id'=>$schedule_id ])->row();

            if (!empty($record_pre_update)) {
                $address_id		= !empty($update_data['address_id']) ? $update_data['address_id'] : $record_pre_update->address_id;
                $schedule_ref	= !empty($update_data['schedule_ref']) ? $update_data['schedule_ref'] : $account_id.$update_data['frequency_id'].$address_id.$data['frequency_id'];

                $check_conflict = $this->db->select('schedules.*', false)
                    ->where('schedules.account_id', $account_id)
                    ->where('schedules.schedule_ref', $schedule_ref)
                    ->where([ 'frequency_id'=>$update_data['frequency_id'], 'frequency_id'=>$update_data['frequency_id'] ])
                    ->where('schedule_id !=', $schedule_id)
                    ->limit(1)
                    ->get('schedules')
                    ->row();

                if (!$check_conflict) {
                    $update_data['last_modified_by'] = $this->ion_auth->_current_user->id;
                    $this->db->where($ref_condition)
                        ->update('schedules', $update_data);

                    $updated_record = $this->get_schedules($account_id, false, [ 'schedule_id'=>$schedule_id ]);
                    $result 		= (!empty($updated_record->records)) ? $updated_record->records : (!empty($updated_record) ? $updated_record : false);

                    $this->session->set_flashdata('message', 'Schedule record updated successfully');
                    return $result;
                } else {
                    $this->session->set_flashdata('message', 'Schedule record updated successfully');
                    return false;
                }
            } else {
                $this->session->set_flashdata('message', 'This Schedule record does not exist or does not belong to you.');
                return false;
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }
        return $result;
    }

    /*
    *	Get list of Schedule records and search through them
    */
    public function get_schedules($account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $where = $raw_where = convert_to_array($where);
            $site_id = !empty($where['site_id']) ? $where['site_id'] : false;

            ## Get all Schedules related to a Site
            $site_activities = $this->db->select('DISTINCT(schedule_activities.schedule_id) AS `schedule_id`', false)
                ->group_by('schedule_activities.schedule_id')
                ->get_where('schedule_activities', [ 'schedule_activities.account_id' => $account_id, 'schedule_activities.site_id'=>$site_id ]);

            if ($site_activities->num_rows() > 0) {
                $site_linked_schedules = array_column($site_activities->result(), 'schedule_id');
                $raw_where['site_linked_schedules'] = $site_linked_schedules;
            }


            $this->db->select('schedules.*, sf.frequency_name, sf.frequency_group, sf.frequency_desc, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false)
                ->join('schedule_frequencies sf', 'sf.frequency_id = schedules.frequency_id', 'left')
                ->join('user creater', 'creater.id = schedules.created_by', 'left')
                ->join('user modifier', 'modifier.id = schedules.last_modified_by', 'left')
                ->where('schedules.is_active', 1)
                ->where('schedules.account_id', $account_id);

            $where = $raw_where = convert_to_array($where);

            if (isset($where['schedule_id'])) {
                $schedule_id	= (!empty($where['schedule_id'])) ? $where['schedule_id'] : false;
                if (!empty($schedule_id)) {
                    $row = $this->db->get_where('schedules', ['schedule_id'=>$schedule_id ])->row();

                    if (!empty($row)) {
                        $activities_completed		= $this->get_schedule_activities($account_id, false, ['schedule_id'=>$schedule_id, 'status'=>'Completed' ]);
                        $activities_completed		= !empty($activities_completed->records) ? $activities_completed->records : (!empty($activities_completed) ? $activities_completed : 0);
                        $row->activities_completed 	= !empty($activities_completed) ? count($activities_completed) : 0;

                        $result = $row;
                        $this->session->set_flashdata('message', 'Schedule record(s) data found');
                        return $result;
                    } else {
                        $this->session->set_flashdata('message', 'Schedule record(s) data not found');
                        return false;
                    }
                }
                unset($where['schedule_id'], $where['schedule_ref']);
            }

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->schedule_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->schedule_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (isset($where['frequency_id'])) {
                if (!empty($where['frequency_id'])) {
                    $this->db->where('schedules.frequency_id', $where['frequency_id']);
                }
                unset($where['frequency_id']);
            }

            if (isset($where['contract_id'])) {
                if (!empty($where['contract_id'])) {
                    $this->db->where('schedules.contract_id', $where['contract_id']);
                }
                unset($where['contract_id']);
            }

            if (isset($where['site_id'])) {
                if (!empty($where['site_id'])) {
                    if (!empty($site_linked_schedules) && is_array($site_linked_schedules)) {
                        $comma_separated_schedules_list = implode(', ', $site_linked_schedules);
                        $combined_where = '( ( schedules.site_id  = '.$where['site_id'].' ) OR ( schedules.schedule_id IN ( '.$comma_separated_schedules_list.' ) ) )';
                        $this->db->where($combined_where);
                    } else {
                        $this->db->where('schedules.site_id', $where['site_id']);
                    }
                }
                unset($where['site_id']);
            }

            if (isset($where['location_id'])) {
                if (!empty($where['location_id'])) {
                    $this->db->where('schedules.location_id', $where['location_id']);
                }
                unset($where['location_id']);
            }

            if (isset($where['asset_id'])) {
                if (!empty($where['asset_id'])) {
                    $this->db->where('schedules.asset_id', $where['asset_id']);
                }
                unset($where['asset_id']);
            }

            if (!empty($where['schedule_summary'])) {
                $schedule_summary = 1;
                unset($where['schedule_summary']);
            }

            if (!empty($where)) {
                $this->db->where($where);
            }

            if (!empty($order_by)) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('schedule_name, schedule_id DESC');
            }

            $query = $this->db->get('schedules');

            if ($query->num_rows() > 0) {
                if (!empty($schedule_summary)) {
                    $result = [];
                    foreach ($query->result() as $k => $row) {
                        $result[$k] = [
                            'site_id'				=> $row->site_id,
                            'schedule_id'			=> $row->schedule_id,
                            'schedule_name'			=> $row->schedule_name,
                            'schedule_ref'			=> $row->schedule_ref,
                            'schedule_status'		=> $row->schedule_status,
                            'activities_total'		=> $row->activities_total,
                            'first_activity_due_date'=> $row->first_activity_due_date,
                            'date_created'			=> $row->date_created,
                        ];
                    }
                } else {
                    $result_data = [];
                    foreach ($query->result() as $k => $row) {
                        $row->scheduled_job_types 	= false;
                        $row->scheduled_sites 		= false;
                        $row->scheduled_assets 		= false;
                        if (!empty($row->contract_id)) {
                            $query = $this->db->select('schedule_activities.site_id')->get_where('schedule_activities', [ 'account_id'=>$account_id, 'schedule_id'=>$row->schedule_id ]);
                            $row->scheduled_sites 	= ($query->num_rows() > 0) ? (array_unique(array_column($query->result_array(), 'site_id'))) : false;
                            $scheduled_job_types	= $this->get_scheduled_job_types($account_id, $row->schedule_id, [ 'contract_id' => $row->contract_id ]);
                        }

                        if (!empty($row->site_id)) {
                            $query = $this->db->select('schedule_activities.asset_id')->get_where('schedule_activities', [ 'account_id'=>$account_id, 'schedule_id'=>$row->schedule_id ]);
                            $row->scheduled_assets 		= ($query->num_rows() > 0) ? (array_unique(array_column($query->result_array(), 'asset_id'))) : false;
                            $scheduled_job_types		= $this->get_scheduled_job_types($account_id, $row->schedule_id, [ 'site_id' => $row->site_id ]);
                        }

                        $row->scheduled_job_types	= !empty($scheduled_job_types) ? $scheduled_job_types : null;

                        $result_data[$k] = $row;
                    }

                    $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                    $result->records 			= $result_data;
                    $counters 					= $this->get_schedule_totals($account_id, $search_term, $raw_where, $limit);
                    $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
                    $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
                    $result->counters->limit  	= (!empty($apply_limit)) ? $limit : $result->counters->total;
                    $result->counters->offset 	= $offset;
                }

                $this->session->set_flashdata('message', 'Schedule records data found');
            } else {
                $this->session->set_flashdata('message', 'There\'s currently no Schedule records setup for your Account');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }

    /** Get Schedule record lookup counts **/
    public function get_schedule_totals($account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('schedules.schedule_id', false)
                ->join('schedule_frequencies sf', 'sf.frequency_id = schedules.frequency_id', 'left')
                ->where('schedules.is_active', 1)
                ->where('schedules.account_id', $account_id);

            $where = convert_to_array($where);

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->schedule_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->schedule_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (isset($where['frequency_id'])) {
                if (!empty($where['frequency_id'])) {
                    $this->db->where('schedules.frequency_id', $where['frequency_id']);
                }
                unset($where['frequency_id']);
            }

            if (isset($where['contract_id'])) {
                if (!empty($where['contract_id'])) {
                    $this->db->where('schedules.contract_id', $where['contract_id']);
                }
                unset($where['contract_id']);
            }


            if (isset($where['site_id'])) {
                if (!empty($where['site_id'])) {
                    if (!empty($where['site_linked_schedules']) && is_array($where['site_linked_schedules'])) {
                        $comma_separated_schedules_list = implode(', ', $where['site_linked_schedules']);
                        $combined_where = '( ( schedules.site_id  = '.$where['site_id'].' ) OR ( schedules.schedule_id IN ( '.$comma_separated_schedules_list.' ) ) )';

                        $this->db->where($combined_where);
                        unset($where['site_linked_schedules']);
                    } else {
                        $this->db->where('schedules.site_id', $where['site_id']);
                    }
                }
                unset($where['site_id']);
            }

            if (isset($where['location_id'])) {
                if (!empty($where['location_id'])) {
                    $this->db->where('schedules.location_id', $where['location_id']);
                }
                unset($where['location_id']);
            }

            if (isset($where['asset_id'])) {
                if (!empty($where['asset_id'])) {
                    $this->db->where('schedules.asset_id', $where['asset_id']);
                }
                unset($where['asset_id']);
            }

            if (!empty($where)) {
                $this->db->where($where);
            }

            $query 			  = $this->db->from('schedules')->count_all_results();

            $results['total'] = !empty($query) ? $query : 0;
            $limit 			  = (!empty($limit > 0)) ? $limit : $results['total'];
            $results['pages'] = !empty($query) ? ceil($query / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }

    /** Create Multiple Schedule Activities **/
    public function create_multiple_schedule_activities($account_id = false, $schedule_id = false, $activities_data = false, $data = false, $limit = SCHEDULE_CLONE_DEFAULT_LIMIT, $offset = 0)
    {
        if (!empty($account_id) && !empty($schedule_id) && !empty($activities_data)) {
            $schedule = $this->db->select('schedules.frequency_id, schedules.contract_id, site.site_address_id `site_address_id`, site_asset.site_address_id `asset_address_id`', false)
                ->join('site', 'site.site_id = schedules.site_id', 'left')
                ->join('asset', 'asset.asset_id = schedules.asset_id', 'left')
                ->join('site site_asset', 'site_asset.site_id = asset.site_id', 'left')
                ->where('schedules.schedule_id', $schedule_id)
                ->get('schedules')
                ->row();

            $address_id  			= !empty($schedule->asset_address_id) ? $schedule->asset_address_id : (!empty($schedule->site_address_id) ? $schedule->site_address_id : false);
            $contract_id 			= !empty($data['contract_id']) ? $data['contract_id'] : (!empty($schedule->contract_id) ? $schedule->contract_id : false);
            $frequency_id 			= !empty($data['frequency_id']) ? $data['frequency_id'] : (!empty($schedule->frequency_id) ? $schedule->frequency_id : false);
            $asset_site_id 			= !empty($data['site_id']) ? $data['site_id'] : (!empty($schedule->site_id) ? $schedule->site_id : false);
            $total_assets_expected 	= !empty($data['total_assets']) ? $data['total_assets'] : 0;
            $total_sites_expected 	= !empty($data['total_sites']) ? $data['total_sites'] : 0;
            $total_activities_due 	= !empty($data['total_activities_due']) ? $data['total_activities_due'] : 0;

            $activities_data	= convert_to_array($activities_data);

            $new_data = $existing_records 	= $all_activities = $activity_jobs = $site_tracking_data = [];
            $multi_asset_type_activities 	= !empty($activities_data['schedule_activities']) ? $activities_data['schedule_activities'] : false;
            $multi_asset_type_activities	= (is_string($multi_asset_type_activities)) ? json_decode($multi_asset_type_activities) : $multi_asset_type_activities;
            $multi_asset_type_activities	= (is_object($multi_asset_type_activities)) ? object_to_array($multi_asset_type_activities) : $multi_asset_type_activities;
            $processed_activities			= $total_assets = 0;
            $total_processed_assets	= $total_processed_sites = [];

            if (!empty($multi_asset_type_activities)) {
                foreach ($multi_asset_type_activities as $k => $schedule_data) {
                    foreach ($schedule_data as $k => $activity_data) {
                        if (!empty($activity_data['activity_name'])) {
                            $address_id 			= !empty($activity_data['address_id']) ? $activity_data['address_id'] : false;
                            $site_id 				= !empty($activity_data['site_id']) ? $activity_data['site_id'] : false;
                            $data 					= array_merge($activity_data, [ 'account_id' => $account_id, 'schedule_id'=>$schedule_id, 'contract_id' => $contract_id, 'frequency_id' => $frequency_id, 'site_id' => $site_id, 'address_id' => $address_id ]);
                            $job_data 				= $this->ssid_common->_filter_data('job', $data);
                            $schedule_site_tracker 	= $this->ssid_common->_filter_data('schedule_site_tracker', $data);
                            $data 					= $this->ssid_common->_filter_data('schedule_activities', $data);

                            $due_date 				= !empty($data['due_date']) ? date('Y-m-d', strtotime($data['due_date'])) : date('Y-m-d', strtotime(' + 7 days'));
                            $data['due_date'] 		= $due_date;
                            $data['job_due_date'] 	= !empty($data['job_due_date']) ? date('Y-m-d', strtotime($data['job_due_date'])) : $due_date;

                            $job_data['due_date'] 	= $due_date;
                            $job_data['job_due_date'] 	= !empty($data['job_due_date']) ? date('Y-m-d', strtotime($data['job_due_date'])) : $due_date;

                            $this->db->select('schedule_activities.activity_id', false)
                                ->where('schedule_activities.account_id', $account_id)
                                ->where([ 'activity_name'=>$data['activity_name'], 'schedule_id'=>$schedule_id, 'job_type_id'=>$data['job_type_id'], 'due_date'=>$due_date ])
                                ->limit(1);

                            if (!empty($data['site_id'])) {
                                $this->db->where('schedule_activities.site_id', $data['site_id']);
                            }

                            if (!empty($data['asset_id'])) {
                                $this->db->where('schedule_activities.asset_id', $data['asset_id']);
                                $total_assets++;
                            }

                            $check_exists = $this->db->get('schedule_activities')->row();

                            if (!empty($check_exists)) {
                                $data['activity_id'] 		= $check_exists->activity_id;
                                $data['last_modified_by'] 	= $this->ion_auth->_current_user->id;
                                $existing_records[] 	= $data;
                            } else {
                                $data['status'] 		= 'Not due';
                                $data['completion'] 	= 0;
                                $data['created_by'] 	= $this->ion_auth->_current_user->id;
                                $new_records[] 			= $data;
                            }

                            $all_activities[]							= $data;

                            $site_ref_id = !empty($check_exists->site_id) ? $check_exists->site_id : (!empty($data['site_id']) ? $data['site_id'] : (!empty($site_id) ? $site_id : $asset_site_id));

                            if (!empty($data['asset_id'])) {
                                $total_processed_assets[$data['asset_id']] 	= $data['asset_id'];
                            }

                            if (!empty($site_ref_id)) {
                                $total_processed_sites[$site_ref_id] = $site_ref_id;
                                $site_tracking_data[$site_ref_id]	 = $schedule_site_tracker;
                            }
                        }
                    }
                }

                if (!empty($existing_records)) {
                    $this->db->update_batch('schedule_activities', $existing_records, 'activity_id');
                    $processed_activities += count($existing_records);
                    $this->session->set_flashdata('message', 'Activity record(s) updated successfully.');
                }

                if (!empty($new_records)) {
                    $this->db->insert_batch('schedule_activities', $new_records);
                    $processed_activities += count($new_records);
                    $this->session->set_flashdata('message', 'Activity record(s) created successfully.');
                }

                if (!empty($all_activities)) {
                    if (!empty($site_tracking_data)) {
                        $site_tracker = $this->generate_site_schedule_tracking($account_id, $site_tracking_data);
                    }

                    $result = [
                        'site_id' 				=> !empty($asset_site_id) ? (string) $asset_site_id : null,
                        'schedule_id' 			=> (string) $schedule_id,
                        'contract_id' 			=> !empty($contract_id) ? (string) $contract_id : null,
                        'frequency_id' 			=> (string) $frequency_id,
                        'counters' 				=> [
                            'expected_sites' 		=> $total_sites_expected,
                            'processed_sites' 		=> count($total_processed_sites),
                            'expected_assets' 		=> $total_assets_expected,
                            'processed_assets' 		=> count($total_processed_assets),
                            'expected_activities' 	=> (string) $total_activities_due,
                            'processed_activities' 	=>  strval($processed_activities),
                            'limit' 				=> !empty($limit) ? (string) $limit : 0,
                            'offset'				=> !empty($offset) ? (string) $offset : 0,
                            'activity_pages'		=> !empty($activity_pages) ? (string) $activity_pages : 0,
                        ]
                    ];

                    $this->session->set_flashdata('message', 'Activity record(s) processed successfully.');
                } else {
                    $this->session->set_flashdata('message', 'Unable to process your request. Please try again!');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }

        return $result;
    }

    /** Create Schedule Activity  record **/
    public function create_schedule_activities($account_id = false, $schedule_id = false, $activities_data = false)
    {
        $result = null;

        if (!empty($account_id) && !empty($schedule_id) && !empty($activities_data)) {
            ini_set('memory_limit', '384M');

            $address = $this->db->select('site.site_address_id `site_address_id`, customer.address_id `customer_address_id`', false)
                ->join('site', 'site.site_id = schedules.site_id', 'left')
                ->join('customer', 'customer.customer_id = schedules.customer_id', 'left')
                ->where('schedules.schedule_id', $schedule_id)
                ->get('schedules')
                ->row();
            $address_id = !empty($address->customer_address_id) ? $address->customer_address_id : (!empty($address->site_address_id) ? $address->site_address_id : false);

            $activities_data   	= convert_to_array($activities_data);
            $new_data = $existing_records = $activity_jobs = [];
            $multiple_activities 	= !empty($activities_data['schedule_activities']) ? $activities_data['schedule_activities'] : false;
            $multiple_activities	= (is_string($multiple_activities)) ? json_decode($multiple_activities) : $multiple_activities;
            $multiple_activities	= (is_object($multiple_activities)) ? object_to_array($multiple_activities) : $multiple_activities;

            unset($activities_data['schedule_activities']);

            if (!empty($multiple_activities)) {
                foreach ($multiple_activities as $k => $data) {
                    $data 		= array_merge($data, [ 'account_id' => $account_id, 'schedule_id'=>$schedule_id ]);
                    $job_data 	= $this->ssid_common->_filter_data('job', $data);
                    $data 		= $this->ssid_common->_filter_data('schedule_activities', $data);

                    $due_date 				= !empty($data['due_date']) ? date('Y-m-d', strtotime($data['due_date'])) : date('Y-m-d', strtotime(' + 7 days'));
                    $data['due_date'] 		= $due_date;
                    $job_data['due_date'] 	= $due_date;
                    $job_data['job_due_date'] 	= !empty($data['job_due_date']) ? date('Y-m-d', strtotime($data['job_due_date'])) : $due_date;

                    $check_exists = $this->db->select('schedule_activities.activity_id', false)
                        ->where('schedule_activities.account_id', $account_id)
                        ->where([ 'schedule_id'=>$schedule_id, 'job_type_id'=>$data['job_type_id'], 'due_date'=>$due_date ])
                        ->where([ 'activity_name'=>$data['activity_name'], 'schedule_id'=>$schedule_id, 'job_type_id'=>$data['job_type_id'], 'due_date'=>$due_date ])
                        ->limit(1)
                        ->get('schedule_activities')
                        ->row();


                    if (!empty($check_exists)) {
                        $data['last_modified_by'] 	= $this->ion_auth->_current_user->id;
                        $this->db->where('activity_id', $check_exists->activity_id)->update('schedule_activities', $data);
                        $activity_id 			= $check_exists->activity_id;
                        $data['activity_id'] 	= $activity_id;
                        $job_data['activity_id']= $activity_id;
                        $job_data['address_id'] = !empty($address_id) ? $address_id : (!empty($job_data['address_id']) ? $job_data['address_id'] : null);
                        $existing_records[] 	= $data;
                    } else {
                        $data['status'] 		= 'Not due';
                        $data['completion'] 	= 0;
                        $data['created_by'] 	= $this->ion_auth->_current_user->id;
                        $this->db->insert('schedule_activities', $data);
                        $activity_id 			= $this->db->insert_id();
                        $data['activity_id'] 	= $activity_id;
                        $job_data['activity_id']= $activity_id;
                        $job_data['address_id'] = !empty($address_id) ? $address_id : (!empty($job_data['address_id']) ? $job_data['address_id'] : null);
                        $new_records[] 			= $data;
                    }
                    $activity_jobs[]		= $job_data;
                }
            } else {
                foreach ($activities_data as $col => $value) {
                    $data[$col] = $value;
                }

                $data 		= array_merge($data, [ 'account_id' => $account_id, 'schedule_id'=>$schedule_id ]);
                $job_data 	= $this->ssid_common->_filter_data('job', $data);
                $data 		= $this->ssid_common->_filter_data('schedule_activities', $data);

                $due_date 				= date('Y-m-d', strtotime($data['due_date']));
                $data['due_date'] 		= $due_date;
                $job_data['due_date'] 	= $due_date;
                $job_data['job_due_date'] 	= !empty($data['job_due_date']) ? date('Y-m-d', strtotime($data['job_due_date'])) : $due_date;

                $check_exists = $this->db->select('schedule_activities.activity_id', false)
                    ->where('schedule_activities.account_id', $account_id)
                    ->where([ 'activity_name'=>$data['activity_name'], 'schedule_id'=>$schedule_id, 'job_type_id'=>$data['job_type_id'], 'due_date'=>$due_date ])
                    ->limit(1)
                    ->get('schedule_activities')
                    ->row();

                if (!empty($check_exists)) {
                    $data['last_modified_by'] 	= $this->ion_auth->_current_user->id;
                    $this->db->where('activity_id', $check_exists->activity_id)->update('schedule_activities', $data);
                    $activity_id 			= $check_exists->activity_id;
                    $data['activity_id'] 	= $activity_id;
                    $job_data['activity_id']= $activity_id;
                    $job_data['address_id'] = !empty($address_id) ? $address_id : (!empty($job_data['address_id']) ? $job_data['address_id'] : null);
                    $existing_records[] 	= $data;
                } else {
                    $data['status'] 		= 'Not due';
                    $data['completion'] 	= 0;
                    $data['created_by'] 	= $this->ion_auth->_current_user->id;
                    $this->db->insert('schedule_activities', $data);
                    $activity_id 			= $this->db->insert_id();
                    $data['activity_id'] 	= $activity_id;
                    $job_data['activity_id']= $activity_id;
                    $job_data['address_id'] = !empty($address_id) ? $address_id : (!empty($job_data['address_id']) ? $job_data['address_id'] : null);
                    $new_records[] 			= $data;
                }

                $activity_jobs[]			= $job_data;
            }

            if (!empty($activity_jobs)) {
                #$result['activity_jobs'] = $activity_jobs;
                $this->generate_activity_jobs($account_id, $activity_jobs);
            }

            if (!empty($existing_records)) {
                $this->session->set_flashdata('message', 'Activity record(s) updated successfully.');
                $result['updated_records'] = $existing_records;
            }

            if (!empty($new_records)) {
                $this->session->set_flashdata('message', 'Activity record(s) created successfully.');
                $result['new_records'] = $new_records;
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }

        return $result;
    }

    /** Update an existing Activity record **/
    public function update_schedule_activity($account_id = false, $activity_id = false, $update_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($activity_id)  && !empty($update_data)) {
            $ref_condition = [ 'account_id'=>$account_id, 'activity_id'=>$activity_id ];
            $update_data   = $this->ssid_common->_data_prepare($update_data);
            $update_data   = $this->ssid_common->_filter_data('schedule_activities', $update_data);
            $record_pre_update = $this->db->get_where('schedule_activities', [ 'account_id'=>$account_id, 'activity_id'=>$activity_id ])->row();

            if (!empty($record_pre_update)) {
                $address_id		= !empty($update_data['address_id']) ? $update_data['address_id'] : $record_pre_update->address_id;
                $activity_ref	= !empty($update_data['activity_ref']) ? $update_data['activity_ref'] : $account_id.$update_data['schedule_id'].$address_id.$data['activity_type_id'];

                $check_conflict = $this->db->select('schedule_activities.*', false)
                    ->where('schedule_activities.account_id', $account_id)
                    ->where('schedule_activities.activity_ref', $activity_ref)
                    ->where([ 'schedule_id'=>$update_data['schedule_id'], 'activity_type_id'=>$update_data['activity_type_id'] ])
                    ->where('activity_id !=', $activity_id)
                    ->limit(1)
                    ->get('schedule_activities')
                    ->row();

                if (!$check_conflict) {
                    $update_data['last_modified_by'] = $this->ion_auth->_current_user->id;
                    $this->db->where($ref_condition)
                        ->update('schedule_activities', $update_data);

                    $updated_record = $this->get_schedule_activities($account_id, false, ['activity_id'=>$activity_id]);
                    $result 		= (!empty($updated_record->records)) ? $updated_record->records : (!empty($updated_record) ? $updated_record : false);

                    $this->session->set_flashdata('message', 'Activity record updated successfully');
                    return $result;
                } else {
                    $this->session->set_flashdata('message', 'Activity record updated successfully');
                    return false;
                }
            } else {
                $this->session->set_flashdata('message', 'This Activity record does not exist or does not belong to you.');
                return false;
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }
        return $result;
    }

    /*
    *	Get list of Activity records and search through them
    */
    public function get_schedule_activities($account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $where = $raw_where = convert_to_array($where);

            if (isset($where['category_id'])) {
                if (!empty($where['category_id'])) {
                    $asset_ids = $this->get_assets_by_category($account_id, $where['category_id'], ['ids_only'=>1]);
                    if (!empty($asset_ids)) {
                        $this->db->where_in('schedule_activities.asset_id', $asset_ids);
                    }
                }
                unset($where['category_id']);
            }

            $this->db->select('schedule_activities.*, job.job_id, job_types.job_type, job_types.discipline_id, account_discipline.account_discipline_name `discipline_name`, account_discipline.account_discipline_image_url `discipline_image_url`, job_statuses.job_status,CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`, CONCAT( assignee.first_name, " ", assignee.last_name ) `assignee`, site.site_reference, ( CASE WHEN ( site.site_id != "" ) THEN site.site_id ELSE site_asset.site_id END ) `site_id`, ( CASE WHEN ( site.site_id != "" ) THEN site.site_id ELSE site_asset.site_id END ) `site_id`, ( CASE WHEN ( site.site_name != "" ) THEN site.site_name ELSE site_asset.site_name END ) `site_name`, ( CASE WHEN ( site.site_postcodes != "" ) THEN site.site_postcodes ELSE site_asset.site_postcodes END ) `site_postcodes`', false)
                ->join('job', 'job.activity_id = schedule_activities.activity_id', 'left')
                ->join('site', 'site.site_id = schedule_activities.site_id', 'left')
                ->join('asset', 'asset.asset_id = schedule_activities.asset_id', 'left')
                ->join('site site_asset', 'site_asset.site_id = asset.site_id', 'left')
                ->join('job_statuses', 'job_statuses.status_id = job.status_id', 'left')
                ->join('job_types', 'job_types.job_type_id = job.job_type_id', 'left')
                ->join('user creater', 'creater.id = schedule_activities.created_by', 'left')
                ->join('user modifier', 'modifier.id = schedule_activities.last_modified_by', 'left')
                ->join('user assignee', 'assignee.id = job.assigned_to', 'left')
                ->join('account_discipline', 'account_discipline.discipline_id = job_types.discipline_id', 'left')
                ->where('schedule_activities.is_active', 1)
                ->where('job.archived !=', 1)
                ->where('schedule_activities.account_id', $account_id);

            if (isset($where['activity_id'])) {
                $activity_id	= (!empty($where['activity_id'])) ? $where['activity_id'] : false;
                if (!empty($activity_id)) {
                    $row = $this->db->get_where('schedule_activities', ['activity_id'=>$activity_id ])->row();

                    if (!empty($row)) {
                        $result = $row;
                        $this->session->set_flashdata('message', 'Activity records data found');
                        return $result;
                    } else {
                        $this->session->set_flashdata('message', 'Activity records data not found');
                        return false;
                    }
                }
                unset($where['activity_id'], $where['activity_ref']);
            }

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->activity_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        if (!empty($search_where['job_type'])) {
                            $search_where['job_types.job_type'] =   trim($term);
                            unset($search_where['job_types.job_type']);
                        }

                        if (!empty($search_where['job_status'])) {
                            $search_where['job_statuses.job_status'] =   trim($term);
                            unset($search_where['job_statuses.job_status']);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->activity_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    if (!empty($search_where['job_type'])) {
                        $search_where['job_types.job_type'] =   trim($search_term);
                        unset($search_where['job_types.job_type']);
                    }

                    if (!empty($search_where['job_status'])) {
                        $search_where['job_statuses.job_status'] =   trim($search_term);
                        unset($search_where['job_statuses.job_status']);
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (isset($where['schedule_id'])) {
                if (!empty($where['schedule_id'])) {
                    $this->db->where('schedule_activities.schedule_id', $where['schedule_id']);
                    $this->db->where('job.schedule_id', $where['schedule_id']);
                }
                unset($where['schedule_id']);
            }

            if (isset($where['period_range'])) {
                if (!empty($where['period_range'])) {
                    switch($where['period_range']) {
                        case 'weekly':
                        case 'weekly_schedules':
                            $date_from 	= date('Y-m-d', strtotime('next Sunday -1 week', strtotime('this sunday')));
                            $date_to 	= date('Y-m-d', strtotime('next Sunday -1 week + 6 days', strtotime('this sunday')));
                            break;

                        case 'monthly':
                        case 'monthly_schedules':
                            $date_from 	= date('Y-m-01');
                            $date_to 	= date('Y-m-t');
                            break;

                        case 'annual':
                        case 'annual_schedules':
                            $date_from	= !empty($where['year_start']) ? date('Y-m-d', strtotime($where['year_start'])) : date('Y-m-d', strtotime('Jan 01'));
                            $date_to 	= !empty($where['year_end']) ? date('Y-m-d', strtotime($where['year_end'])) : date('Y-m-d', strtotime('Dec 31'));
                            break;
                    }

                    $this->db->where('schedule_activities.due_date >= "'.$date_from.'" ')
                        ->where('schedule_activities.due_date <= "'.$date_to.'" ');
                }
                unset($where['period_range']);
            }

            if (isset($where['status'])) {
                if (!empty($where['status'])) {
                    $this->db->where('schedule_activities.status', $where['status']);
                }
                unset($where['status']);
            }

            if (!empty($where)) {
                $this->db->where($where);
            }

            if (!empty($order_by)) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('site.site_name, site.site_id, LENGTH( schedule_activities.activity_name ), schedule_activities.activity_name');
                $this->db->order_by('schedule_activities.date_created, due_date, activity_name');
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            } else {
                $this->db->limit(5000);
            }

            $query = $this->db->group_by('schedule_activities.activity_id, schedule_activities.site_id')
                ->get('schedule_activities');

            if ($query->num_rows() > 0) {
                $result_data = [];
                foreach ($query->result() as $k => $row) {
                    if (!empty($row->job_id)) {
                        $assets = $this->db->where([ 'account_id'=>$account_id, 'job_assets.job_id' => $row->job_id ])
                            ->from('job_assets')->count_all_results();
                    }

                    $row->total_assets = !empty($assets) ? strval($assets) : null;
                    $result_data[$k] = $row;
                }

                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $result->records 			= $result_data;
                $counters 					= $this->get_activity_totals($account_id, $search_term, $raw_where, $limit);
                $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
                $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
                $result->counters->limit  	= (!empty($apply_limit)) ? $limit : $result->counters->total;
                $result->counters->offset 	= $offset;

                $this->session->set_flashdata('message', 'Activity records data found');
            } else {
                $this->session->set_flashdata('message', 'There\'s currently no Activity records setup for your Account');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }

    /** Get Activity record lookup counts **/
    public function get_activity_totals($account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;
        if (!empty($account_id)) {
            $where = convert_to_array($where);

            if (isset($where['category_id'])) {
                if (!empty($where['category_id'])) {
                    $asset_ids = $this->get_assets_by_category($account_id, $where['category_id'], ['ids_only'=>1]);
                    if (!empty($asset_ids)) {
                        $this->db->where_in('schedule_activities.asset_id', $asset_ids);
                    }
                }
                unset($where['category_id']);
            }

            $this->db->select('schedule_activities.activity_id', false)
                ->where('schedule_activities.is_active', 1)
                ->where('job.archived !=', 1)
                ->where('schedule_activities.account_id', $account_id)
                ->join('job', 'job.activity_id = schedule_activities.activity_id', 'left')
                ->join('job_statuses', 'job_statuses.status_id = job.status_id', 'left')
                ->join('job_types', 'job_types.job_type_id = job.job_type_id', 'left');

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->activity_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        if (!empty($search_where['job_type'])) {
                            $search_where['job_types.job_type'] =   trim($term);
                            unset($search_where['job_types.job_type']);
                        }

                        if (!empty($search_where['job_status'])) {
                            $search_where['job_statuses.job_status'] =   trim($term);
                            unset($search_where['job_statuses.job_status']);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->activity_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    if (!empty($search_where['job_type'])) {
                        $search_where['job_types.job_type'] =   trim($search_term);
                        unset($search_where['job_types.job_type']);
                    }

                    if (!empty($search_where['job_status'])) {
                        $search_where['job_statuses.job_status'] =   trim($search_term);
                        unset($search_where['job_statuses.job_status']);
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (isset($where['schedule_id'])) {
                if (!empty($where['schedule_id'])) {
                    $this->db->where('schedule_activities.schedule_id', $where['schedule_id']);
                }
                unset($where['schedule_id']);
            }

            if (isset($where['period_range'])) {
                if (!empty($where['period_range'])) {
                    switch($where['period_range']) {
                        case 'weekly':
                        case 'weekly_schedules':
                            $date_from 	= date('Y-m-d', strtotime('next Sunday -1 week', strtotime('this sunday')));
                            $date_to 	= date('Y-m-d', strtotime('next Sunday -1 week + 6 days', strtotime('this sunday')));
                            break;

                        case 'monthly':
                        case 'monthly_schedules':
                            $date_from 	= date('Y-m-01');
                            $date_to 	= date('Y-m-t');
                            break;

                        case 'annual':
                        case 'annual_schedules':
                            $date_from	= !empty($where['year_start']) ? date('Y-m-d', strtotime($where['year_start'])) : date('Y-m-d', strtotime('Jan 01'));
                            $date_to 	= !empty($where['year_end']) ? date('Y-m-d', strtotime($where['year_end'])) : date('Y-m-d', strtotime('Dec 31'));
                            break;
                    }

                    $this->db->where('schedule_activities.due_date >= "'.$date_from.'" ')
                        ->where('schedule_activities.due_date <= "'.$date_to.'" ');
                }
                unset($where['period_range']);
            }

            if (isset($where['status'])) {
                if (!empty($where['status'])) {
                    $this->db->where('schedule_activities.status', $where['status']);
                }
                unset($where['status']);
            }

            if (!empty($where)) {
                $this->db->where($where);
            }

            $query 			  = $this->db->from('schedule_activities')->count_all_results();
            $results['total'] = !empty($query) ? $query : 0;
            $limit 			  = (!empty($limit > 0)) ? $limit : $results['total'];
            $results['pages'] = !empty($query) ? ceil($query / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }


    /** Generate Activity Job **/
    public function generate_activity_jobs($account_id = false, $activity_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($activity_data)) {
            $frequency_group = !empty($frequency_group) ? $frequency_group : '';
            ini_set('memory_limit', '480M');
            set_time_limit(180);
            $data = [];
            foreach ($activity_data as $k => $job_data) {
                $job_data['due_date'] 		= date('Y-m-d', strtotime($job_data['due_date']));
                $job_data['job_due_date'] 	= date('Y-m-d', strtotime($job_data['job_due_date']));
                $job_data['status_id']		= 2;
                $job_data['job_duration']	= 1;

                if (empty($frequency_group)) {
                    $freq_data = $this->db->select('schedule_frequencies.frequency_group, schedules.frequency_id', false)
                        ->join('schedules', 'schedule_frequencies.frequency_id = schedules.frequency_id', 'left')
                        ->where('schedules.account_id', $account_id)
                        ->where('schedules.schedule_id', $job_data['schedule_id'])
                        ->where('schedules.is_active', 1)
                        ->where('schedule_frequencies.is_active', 1)
                        ->limit(1)
                        ->get('schedule_frequencies')
                        ->row();

                    $frequency_group = !empty($freq_data->frequency_group) ? $freq_data->frequency_group : $frequency_group;
                }

                if (!empty($frequency_group)) {
                    switch(strtolower($frequency_group)) {
                        case 'weekly':
                        case 'weekly inspection':
                        case 'weekly-inspection':
                            $job_data['job_date'] = null;
                            #$job_data['job_date'] = $job_data['due_date'];
                            #$job_data['job_date'] = !empty( $is_cloned_req ) ? null : $job_data['due_date'];
                            break;

                        case 'monthly':
                        case 'monthly inspection':
                        case 'monthly-inspection':
                            $job_data['job_date'] = null;
                            #$job_data['job_date'] = $job_data['due_date'];
                            #$job_data['job_date'] = !empty( $is_cloned_req ) ? null : $job_data['due_date'];
                            break;
                        default:
                            $job_data['job_date'] = null;
                            break;
                    }
                }

                $condition 	= [
                    'account_id' => $account_id,
                    'due_date'	 =>	$job_data['due_date'],
                    'schedule_id'=>	$job_data['schedule_id'],
                    'job_type_id'=>	$job_data['job_type_id'],
                    'activity_id'=>	$job_data['activity_id']
                ];

                $check_exists = $this->db->select('job.job_id', false)->get_where('job', $condition)->row();

                if (!empty($check_exists)) {
                    $job_data['job_id'] 		  = $check_exists->job_id;
                    $job_data['last_modified_by'] = $this->ion_auth->_current_user->id;
                    #$this->db->where( 'job.job_id', $check_exists->job_id )->update( 'job', $job_data );
                    #$result = true;
                    $data['updated'][] = $job_data;
                } else {
                    $job_data['created_by'] = $this->ion_auth->_current_user->id;
                    $data['new'][] = $job_data;
                    #$this->db->insert( 'job', $job_data );
                    #$result = true;
                }
            }

            if (!empty($data['updated'])) {
                $this->db->update_batch('job', $data['updated'], 'job_id');
            }

            if (!empty($data['new'])) {
                $this->db->insert_batch('job', $data['new']);
            }

            $result = $data;
        }
        return $result;
    }

    /** Update Schedule and Activity Status **/
    public function update_activity_completion_status($account_id = false, $job_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($job_data)) {
            $job_details = $this->db->select('job_id, schedule_id, activity_id, job_statuses.job_status, job_statuses.status_group')
                ->join('job_statuses', 'job_statuses.status_id = job.status_id', 'left')
                ->where('job.account_id', $account_id)
                ->where('job_id', $job_data['job_id'])
                ->get('job')
                ->row();

            if (!empty($job_details)) {
                if (!empty($job_details->activity_id)) {
                    $query = $this->db->select('job_id, schedule_id, activity_id, job_statuses.job_status, job_statuses.status_group')
                        ->join('job_statuses', 'job_statuses.status_id = job.status_id', 'left')
                        ->where('job.account_id', $account_id)
                        ->where('activity_id', $job_details->activity_id)
                        ->get('job');

                    $update_data = [
                        'status'			=> $job_details->job_status,
                        'last_modified_by'	=> $this->ion_auth->_current_user->id
                    ];

                    if (!empty($query->num_rows())) {
                        $activity_jobs  = $query->result_array();
                        $total_statuses = array_column($activity_jobs, 'status_group');
                        $success_jobs   = (count(array_keys($total_statuses, 'successful'))) ? count(array_keys($total_statuses, 'successful')) : 0;
                        $total_jobs		= count($activity_jobs);

                        $completion_perc= ($success_jobs / $total_jobs)*100;

                        if ($success_jobs == $total_jobs) {
                            $update_data['status'] = 'Completed';
                        }
                        $update_data['completion'] = $completion_perc;
                    }

                    $this->db->where('account_id', $account_id)
                        ->where('activity_id', $job_details->activity_id)
                        ->update('schedule_activities', $update_data);
                }

                //Update main Schedule
                $query2 = $this->db->select('schedule_activities.status')
                        ->where('schedule_activities.account_id', $account_id)
                        ->where('schedule_id', $job_details->schedule_id)
                        ->get('schedule_activities');

                if ($query2->num_rows() > 0) {
                    $activities = $query2->result_array();

                    $activity_statuses 		= array_column($activities, 'status');
                    $successful_activities 	= (count(array_keys($activity_statuses, 'Completed'))) ? count(array_keys($activity_statuses, 'Completed')) : 0;
                    $total_activities		= count($activities);

                    if ($successful_activities == $total_activities) {
                        $sched_update_data = ['schedule_status'=>'Works Complete'];
                    } else {
                        $sched_update_data = ['schedule_status'=>'Works In Progress'];
                    }

                    $sched_update_data['last_modified_by']	= $this->ion_auth->_current_user->id;
                    $this->db->where('account_id', $account_id)
                        ->where('schedule_id', $job_details->schedule_id)
                        ->update('schedules', $sched_update_data);
                }
                $result = true;
            }
        }
        return $result;
    }


    /** Create a new Job Tracking Status record **/
    public function create_job_tracking_status($account_id = false, $job_tracking_status_data = false)
    {
        $result = null;

        if (!empty($account_id) && !empty($job_tracking_status_data)) {
            foreach ($job_tracking_status_data as $col => $value) {
                if ($col == 'job_tracking_status') {
                    $data['job_tracking_group'] = strtolower(strip_all_whitespace($value));
                }
                $data[$col] = $value;
            }

            $check_exists = $this->db->select('job_tracking_statuses.*', false)
                ->where('job_tracking_statuses.account_id', $account_id)
                ->where('job_tracking_statuses.is_active', 1)
                ->where('( job_tracking_statuses.job_tracking_status = "'.$data['job_tracking_status'].'" OR job_tracking_statuses.job_tracking_group = "'.$data['job_tracking_group'].'" )')
                ->limit(1)
                ->get('job_tracking_statuses')
                ->row();

            $data = $this->ssid_common->_filter_data('job_tracking_statuses', $data);

            if (!empty($check_exists)) {
                $data['last_modified_by'] = $this->ion_auth->_current_user->id;
                $this->db->where('job_tracking_id', $check_exists->job_tracking_id)
                    ->update('job_tracking_statuses', $data);

                $this->session->set_flashdata('message', 'This Job Tracking Status already exists, record has been updated successfully.');
                $result = $check_exists;
            } else {
                $data['created_by'] = $this->ion_auth->_current_user->id;
                $this->db->insert('job_tracking_statuses', $data);
                $data['job_tracking_id'] = $this->db->insert_id();

                $this->session->set_flashdata('message', 'New Job Tracking Status created successfully.');
                $result = $data;
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }

        return $result;
    }


    /** Update an existing Job Tracking Status **/
    public function update_job_tracking_status($account_id = false, $job_tracking_id = false, $update_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($job_tracking_id)  && !empty($update_data)) {
            $ref_condition = [ 'account_id'=>$account_id, 'job_tracking_id'=>$job_tracking_id ];
            $update_data   = $this->ssid_common->_data_prepare($update_data);
            $update_data   = $this->ssid_common->_filter_data('job_tracking_statuses', $update_data);
            $record_pre_update = $this->db->get_where('job_tracking_statuses', [ 'account_id'=>$account_id, 'job_tracking_id'=>$job_tracking_id ])->row();

            if (!empty($record_pre_update)) {
                $update_data['job_tracking_group'] 	= strtolower(strip_all_whitespace($update_data['job_tracking_status']));
                $job_tracking_status_where 			= '( job_tracking_statuses.job_tracking_status = "'.$update_data['job_tracking_status'].'" OR job_tracking_statuses.job_tracking_group = "'. $update_data['job_tracking_group'] .'" )';
                ;

                $check_conflict = $this->db->select('job_tracking_id', false)
                    ->where('job_tracking_statuses.account_id', $account_id)
                    ->where('job_tracking_statuses.is_active', 1)
                    ->where('job_tracking_statuses.job_tracking_id !=', $job_tracking_id)
                    ->where($job_tracking_status_where)
                    ->limit(1)
                    ->get('job_tracking_statuses')
                    ->row();

                if (!$check_conflict) {
                    $update_data['last_modified_by'] = $this->ion_auth->_current_user->id;
                    $this->db->where($ref_condition)
                        ->update('job_tracking_statuses', $update_data);

                    $updated_record = $this->get_job_tracking_statuses($account_id, $job_tracking_id);
                    $result 		= (!empty($updated_record->records)) ? $updated_record->records : (!empty($updated_record) ? $updated_record : false);

                    $this->session->set_flashdata('message', 'Job Tracking Status updated successfully');
                    return $result;
                } else {
                    $this->session->set_flashdata('message', 'This Job Tracking Status already exists for your account. Request aborted');
                    return false;
                }
            } else {
                $this->session->set_flashdata('message', 'This Job Tracking Status record does not exist or does not belong to you.');
                return false;
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing requireed information.');
        }
        return $result;
    }

    /*
    *	Get list of Job Tracking Statuses and search though it
    */
    public function get_job_tracking_statuses($account_id = false, $job_tracking_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select('job_tracking_statuses.*, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false)
                ->join('user creater', 'creater.id = job_tracking_statuses.created_by', 'left')
                ->join('user modifier', 'modifier.id = job_tracking_statuses.last_modified_by', 'left')
                ->where('job_tracking_statuses.is_active', 1)
                ->where('job_tracking_statuses.account_id', $account_id);

            $where = $raw_where = convert_to_array($where);

            if (!empty($job_tracking_id) || isset($where['job_tracking_id'])) {
                $job_tracking_id	= (!empty($job_tracking_id)) ? $job_tracking_id : $where['job_tracking_id'];
                if (!empty($job_tracking_id)) {
                    $row = $this->db->get_where('job_tracking_statuses', ['job_tracking_id'=>$job_tracking_id ])->row();

                    if (!empty($row)) {
                        $result = ( object ) ['records'=>$row];
                        $this->session->set_flashdata('message', 'Job Tracking Status data found');
                        return $result;
                    } else {
                        $this->session->set_flashdata('message', 'Job Tracking Status data not found');
                        return false;
                    }
                }
                unset($where['job_tracking_id'], $where['job_tracking_group']);
            }

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->job_tracking_statuses_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->job_tracking_statuses_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                if (isset($where['job_tracking_status'])) {
                    if (!empty($where['job_tracking_status'])) {
                        $job_tracking_group = strtoupper(strip_all_whitespace($where['job_tracking_status']));
                        $this->db->where('( job_tracking_statuses.job_tracking_status = "'.$where['job_tracking_status'].'" OR job_tracking_statuses.job_tracking_group = "'.$job_tracking_group.'" )');
                    }
                    unset($where['job_tracking_status']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            if (!empty($order_by)) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('job_tracking_status');
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->get('job_tracking_statuses');

            if ($query->num_rows() > 0) {
                $result_data = $query->result();

                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $result->records 			= $result_data;
                $counters 					= $this->job_tracking_statuses_totals($account_id, $search_term, $raw_where, $limit);
                $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
                $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
                $result->counters->limit  	= (!empty($apply_limit)) ? $limit : $result->counters->total;
                $result->counters->offset 	= $offset;

                $this->session->set_flashdata('message', 'Job Tracking Statuss data found');
            } else {
                $this->session->set_flashdata('message', 'There\'s currently no Job Tracking Statuses data matching your criteria');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }


    /** Get Job Tracking Statuss lookup counts **/
    public function job_tracking_statuses_totals($account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('job_tracking_statuses.job_tracking_id', false)
                ->where('job_tracking_statuses.is_active', 1)
                ->where('job_tracking_statuses.archived !=', 1)
                ->where('job_tracking_statuses.account_id', $account_id);

            $where = $raw_where = convert_to_array($where);

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->job_tracking_statuses_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->job_tracking_statuses_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                if (isset($where['job_tracking_status'])) {
                    if (!empty($where['job_tracking_status'])) {
                        $job_tracking_group = strtoupper(strip_all_whitespace($where['job_tracking_status']));
                        $this->db->where('( job_tracking_statuses.job_tracking_status = "'.$where['job_tracking_status'].'" OR job_tracking_statuses.job_tracking_group = "'.$job_tracking_group.'" )');
                    }
                    unset($where['job_tracking_status']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $query 			  	= $this->db->from('job_tracking_statuses')->count_all_results();
            $results['total'] 	= !empty($query) ? $query : 0; //xyz
            $limit 				= ($limit > 0) ? $limit : $results['total'];
            $results['pages'] 	= !empty($query) ? ceil($results['total'] / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }


    /**
    /* Delete/Archive an Job Tracking Status resource
    */
    public function delete_job_tracking_status($account_id = false, $job_tracking_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($job_tracking_id)) {
            $conditions 	= [ 'account_id'=>$account_id,'job_tracking_id'=>$job_tracking_id ];
            $record_exists 	= $this->db->get_where('job_tracking_statuses', $conditions)->row();

            if (!empty($record_exists)) {
                ## Then the parent
                $this->db->where('job_tracking_id', $job_tracking_id)
                    ->update('job_tracking_statuses', ['is_active'=>0]);

                if ($this->db->trans_status() !== false) {
                    $this->session->set_flashdata('message', 'Job Tracking Status archived successfully.');
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


    /*
    *	Get list of Activity records and associated Evidocs through jobs
    */
    public function get_schedule_activities_w_evidocs($account_id = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select('schedule_activities.*, job.job_id, job_types.job_type, job_statuses.job_status,CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false)
                ->join('job', 'job.activity_id = schedule_activities.activity_id', 'left')
                ->join('job_statuses', 'job_statuses.status_id = job.status_id', 'left')
                ->join('job_types', 'job_types.job_type_id = job.job_type_id', 'left')
                ->join('user creater', 'creater.id = schedule_activities.created_by', 'left')
                ->join('user modifier', 'modifier.id = schedule_activities.last_modified_by', 'left')
                ->where('schedule_activities.is_active', 1)
                ->where('schedule_activities.account_id', $account_id);

            $where = $raw_where = convert_to_array($where);

            /* 			if( isset( $where['activity_id'] ) ){
                            $activity_id	= ( !empty( $where['activity_id'] ) ) ? $where['activity_id'] : false;
                            if( !empty( $activity_id ) ){

                                $row = $this->db->get_where( 'schedule_activities', ['activity_id'=>$activity_id ] )->row();

                                if( !empty( $row ) ){
                                    $result = $row;
                                    $this->session->set_flashdata( 'message','Activity records data found' );
                                    return $result;
                                } else {
                                    $this->session->set_flashdata( 'message','Activity records data not found' );
                                    return false;
                                }
                            }
                            unset( $where['activity_id'], $where['activity_ref'] );
                        } */

            if (isset($where['schedule_id'])) {
                if (!empty($where['schedule_id'])) {
                    $this->db->where('schedule_activities.schedule_id', $where['schedule_id']);
                }
                unset($where['schedule_id']);
            }

            if (!empty($where)) {
                $this->db->where($where);
            }

            if (!empty($order_by)) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('due_date, activity_name');
            }

            $query = $this->db->get('schedule_activities');

            if ($query->num_rows() > 0) {
                $result_data = $query->result();
                foreach ($result_data as $key => $data) {
                    $uploaded_docs = false;
                    $result_data[$key]->evidocs = false;

                    ## alternative version
                    #$this->db->select( "a.audit_id, a.account_id, a.job_id, a.audit_reference `evidoc_reference`, a.date_created, a.created_by", false );
                    $this->db->select("a.audit_id, a.account_id, a.job_id, a.audit_reference `evidoc_reference`, a.date_created, CONCAT( creater.first_name, ' ', creater.last_name ) as record_created_by", false);
                    $this->db->select("ara.*", false);
                    $this->db->select("at.audit_group", false);

                    $this->db->where("a.job_id", $data->job_id);

                    $arch_where = "( a.archived != 1 or a.archived is NULL )";
                    $this->db->where($arch_where);

                    $this->db->join("audit_responses_assets `ara`", "a.audit_id=ara.audit_id", "right");
                    $this->db->join("audit_types `at`", "a.audit_type_id=at.audit_type_id", "left");
                    $this->db->join("user `creater`", "creater.id = a.created_by", "left");

                    $this->db->order_by('ara.audit_id desc, ara.ordering ASC');

                    $query = $this->db->get("audit `a`");

                    if ($query->num_rows() > 0) {
                        $result_data[$key]->evidocs = $query->result();
                    } else {
                        $result_data[$key]->evidocs = false;
                    }



                    if (!empty($result_data[$key]->evidocs)) {
                        foreach ($result_data[$key]->evidocs as $evi_key => $evidoc) {
                            $doc_list 			= $this->document_service->get_document_list($account_id, strtolower($evidoc->audit_group), ['audit_id' => $evidoc->audit_id, 'attached_to_question' => 1]);
                            $uploaded_docs	= (!empty($doc_list[$account_id])) ? $doc_list[$account_id] : $uploaded_docs ;
                        }
                    }
                }
                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[], 'uploaded_docs' =>( object )[] ];
                $result->records 			= $result_data;
                $result->uploaded_docs 		= $uploaded_docs;

                $this->session->set_flashdata('message', 'Activity records data found');
            } else {
                $this->session->set_flashdata('message', 'There\'s currently no Activity records setup for your Account');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }


    /** Get the Status of a Single Job **/
    public function get_job_status($account_id = false, $job_id = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($job_id)) {
            $query = $this->db->select('job.job_id, job.status_id, job_statuses.job_status, job_statuses.status_group', false)
            #$query = $this->db->select('job.job_id, job.status_id, job_statuses.job_status, job_statuses.status_group, job_tracking_statuses.job_tracking_status, job_tracking_statuses.job_tracking_group',false)
                ->join('job_statuses', 'job_statuses.status_id = job.status_id', 'left')
                ->join('job_tracking_statuses', 'job_tracking_statuses.job_tracking_id = job.job_tracking_id', 'left')
                ->where('job.archived !=', 1)
                ->where('job.account_id', $account_id)
                ->where('job.job_id', $job_id)
                ->get('job');

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Activity records data found');
            } else {
                $this->session->set_flashdata('message', 'No data found matching your criteria');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }
        return $result;
    }


        /** Add Required BOMs **/
    public function add_required_boms($account_id = false, $job_type_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($job_type_id) && !empty($postdata)) {
            $postdata 		= convert_to_array($postdata);
            $required_boms	= !empty($postdata['required_boms']) ? $postdata['required_boms'] : false;
            $required_boms	= (is_json($required_boms)) ? json_decode($required_boms) : $required_boms;
            $total			= [];

            if (!empty($required_boms)) {
                foreach ($required_boms as $k => $val) {
                    $data = [
                        'item_id'		=>$val,
                        'job_type_id'	=>$job_type_id,
                        'created_by'	=> $this->ion_auth->_current_user->id
                    ];

                    $check_exists = $this->db->limit(1)->get_where('job_required_bom_items', $data)->row();
                    if (!$check_exists) {
                        $this->db->insert('job_required_bom_items', $data);
                    }
                    $total[] = $data;
                }
            } elseif (!empty($postdata['item_id'])) {
                $data = [
                    'item_id'		=>$postdata['item_id'],
                    'job_type_id'	=>$job_type_id,
                    'created_by'	=> $this->ion_auth->_current_user->id
                ];

                $check_exists = $this->db->limit(1)->get_where('job_required_bom_items', $data)->row();
                if (!$check_exists) {
                    $this->db->insert('job_required_bom_items', $data);
                }
                $total[] = $data;
            }

            if (!empty($total)) {
                $result = $total;
                $this->session->set_flashdata('message', 'Required BOMs added successfully');
            } else {
                $this->session->set_flashdata('message', 'No required BOMs found');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }
        return $result;
    }

    /** Add Required BOMs **/
    public function remove_required_boms($account_id = false, $job_type_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($job_type_id) && !empty($postdata)) {
            $postdata 		 = convert_to_array($postdata);
            $required_boms= !empty($postdata['required_boms']) ? $postdata['required_boms'] : false;
            $required_boms= (is_json($required_boms)) ? json_decode($required_boms) : $required_boms;
            $deleted		= [];

            if (!empty($required_boms)) {
                foreach ($required_boms as $k => $val) {
                    $data = [
                        'item_id'=>$val,
                        'job_type_id'=>$job_type_id
                    ];

                    $check_exists = $this->db->limit(1)->get_where('job_required_bom_items', $data)->row();
                    if (!empty($check_exists)) {
                        $this->db->where($data);
                        $this->db->delete('job_required_bom_items');
                        $this->ssid_common->_reset_auto_increment('job_required_bom_items', 'req_id');
                    }
                    $deleted[] = $data;
                }
            } elseif (!empty($postdata['item_id'])) {
                $data = [
                    'item_id'=>$postdata['item_id'],
                    'job_type_id'=>$job_type_id
                ];

                $check_exists = $this->db->limit(1)->get_where('job_required_bom_items', $data)->row();
                if (!empty($check_exists)) {
                    $this->db->where($data);
                    $this->db->delete('job_required_bom_items');
                    $deleted[] = $data;
                    $this->ssid_common->_reset_auto_increment('job_required_bom_items', 'req_id');
                }
            }

            if (!empty($deleted)) {
                $result = $deleted;
                $this->session->set_flashdata('message', 'Required BOM Item removed successfully');
            } else {
                $this->session->set_flashdata('message', 'No required BOMs were removed');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }
        return $result;
    }

    /** Get a list of required BOMs to a Job **/
    public function get_required_boms($account_id = false, $job_type_id = false, $where = false)
    {
        $result = false;

        if (!empty($job_type_id)) {
            if (!empty($account_id)) {
                #$this->db->where( 'jrb.account_id', $account_id );
            }

            if (!empty($where['job_id'])) {
                // if Job id is given, get any attached dynamic BOMs
                $dynamic_boms = $this->get_dynamic_boms($account_id, $where['job_id'], ['result_as_array'=>1]);
            }

            $query = $this->db->select('jrb.job_id, jrb.job_type_id, bom_items.*, jrb.item_qty')
                ->join('bom_items bom_items', 'bom_items.item_id = jrb.item_id')
                ->where('jrb.job_type_id', $job_type_id)
                ->get('job_required_bom_items jrb');

            if ($query->num_rows() > 0) {
                if (!empty($dynamic_boms)) {
                    $result = array_merge($query->result_array(), $dynamic_boms);
                } else {
                    if (!empty($where['result_as_array'])) {
                        $result = $query->result_array();
                    } else {
                        $result = $query->result();
                    }
                }
                $this->session->set_flashdata('message', 'Required BOMs found');
            } else {
                $this->session->set_flashdata('message', 'No required BOMs found');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }

        return $result;
    }


    /** Add Dynamic BOMs **/
    public function add_dynamic_boms($account_id = false, $job_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($job_id) && !empty($postdata)) {
            $postdata 		= convert_to_array($postdata);
            $dynamic_boms	= !empty($postdata['dynamic_boms']) ? $postdata['dynamic_boms'] : false;
            $dynamic_boms	= (is_json($dynamic_boms)) ? json_decode($dynamic_boms) : $dynamic_boms;
            $total			= [];

            if (!empty($dynamic_boms)) {
                foreach ($dynamic_boms as $k => $val) {
                    $check_item_exists = $this->db->limit(1)->get_where('bom_items', [ 'account_id'=>$account_id, 'item_id'=>$val ])->row();
                    if (!empty($check_item_exists)) {
                        $data = [
                            'item_id'	=> $val,
                            'job_id'	=> $job_id,
                            'created_by'=> $this->ion_auth->_current_user->id
                        ];

                        $check_exists = $this->db->limit(1)->get_where('job_dynamic_boms', $data)->row();
                        if (!$check_exists) {
                            $this->db->insert('job_dynamic_boms', $data);
                        }
                        $total[] = $data;
                    }
                }
            } elseif (!empty($postdata['item_id'])) {
                $check_item_exists = $this->db->limit(1)->get_where('bom_items', [ 'account_id'=>$account_id, 'item_id'=>$postdata['item_id'] ])->row();
                if (!empty($check_item_exists)) {
                    $data = [
                        'item_id'	=>$postdata['item_id'],
                        'job_id'	=>$job_id,
                        'created_by'=> $this->ion_auth->_current_user->id
                    ];
                    $check_exists = $this->db->limit(1)->get_where('job_dynamic_boms', $data)->row();
                    if (!$check_exists) {
                        $this->db->insert('job_dynamic_boms', $data);
                    }
                    $total[] = $data;
                }
            }

            if (!empty($total)) {
                $result = $total;
                $this->session->set_flashdata('message', 'Dynamic boms added successfully');
            } else {
                $this->session->set_flashdata('message', 'No dynamic boms found');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }
        return $result;
    }

    /** Add Dynamic BOMs **/
    public function remove_dynamic_boms($account_id = false, $job_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($job_id) && !empty($postdata)) {
            $postdata 		= convert_to_array($postdata);
            $dynamic_boms	= !empty($postdata['dynamic_boms']) ? $postdata['dynamic_boms'] : false;
            $dynamic_boms	= (is_json($dynamic_boms)) ? json_decode($dynamic_boms) : $dynamic_boms;
            $deleted		= [];

            if (!empty($dynamic_boms)) {
                foreach ($dynamic_boms as $k => $val) {
                    $data = [
                        'item_id'=>$val,
                        'job_id'=>$job_id
                    ];

                    $check_exists = $this->db->limit(1)->get_where('job_dynamic_boms', $data)->row();
                    if (!empty($check_exists)) {
                        $this->db->where($data);
                        $this->db->delete('job_dynamic_boms');
                        $this->ssid_common->_reset_auto_increment('job_dynamic_boms', 'associate_id');
                    }
                    $deleted[] = $data;
                }
            } elseif (!empty($postdata['item_id'])) {
                $data = [
                    'item_id'=>$postdata['item_id'],
                    'job_id'=>$job_id
                ];

                $check_exists = $this->db->limit(1)->get_where('job_dynamic_boms', $data)->row();
                if (!empty($check_exists)) {
                    $this->db->where($data);
                    $this->db->delete('job_dynamic_boms');
                    $deleted[] = $data;
                    $this->ssid_common->_reset_auto_increment('job_dynamic_boms', 'associate_id');
                }
            }

            if (!empty($deleted)) {
                $result = $deleted;
                $this->session->set_flashdata('message', 'Dynamic BOMs removed successfully');
            } else {
                $this->session->set_flashdata('message', 'No dynamic BOMs were removed');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }
        return $result;
    }

    /** Get a list of dynamic BOMs to a Job **/
    public function get_dynamic_boms($account_id = false, $job_id = false, $where = false)
    {
        $result = false;

        if (!empty($job_id)) {
            if (!empty($account_id)) {
                #$this->db->where( 'jdb.account_id', $account_id );
            }

            $query = $this->db->select('jdb.job_id, jdb.job_type_id, bom_items.*')
                ->join('bom_items bom_items', 'bom_items.item_id = jdb.item_id')
                ->where('jdb.job_id', $job_id)
                ->get('job_dynamic_boms jdb');

            if ($query->num_rows() > 0) {
                if (!empty($where['result_as_array'])) {
                    $result = $query->result_array();
                } else {
                    $result = $query->result();
                }
                $this->session->set_flashdata('message', 'Dynamic BOMs found');
            } else {
                $this->session->set_flashdata('message', 'No dynamic BOMs found');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }

        return $result;
    }


    /** Update COnsumed Items Status **/
    public function update_consumed_items_status($account_id = false, $job_id = false, $tracking_status = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($job_id) && !empty($tracking_status)) {
            $this->db->where('account_id', $account_id)
                ->where('job_id', $job_id);
            switch(strtolower($tracking_status)) {
                /* 				case 'callcompleted':
                                case 'call completed': */
                case 'jobinvoice':
                case 'jobinvoiced':
                case 'job invoice':
                case 'job invoiced':
                    $update_data = ['is_confirmed'=>1];
                    break;

                default:
                    $update_data = ['is_confirmed'=>0];
                    break;
            }

            $query 	= $this->db->update('job_consumed_items', $update_data);
            $result = ($this->db->trans_status() !== false) ? true : false;
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }

        return $result;
    }


    /**
    * Get Assets By Category
    **/
    public function get_assets_by_category($account_id = false, $category_id = false, $where = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($category_id)) {
            $category_assets = $this->db->select('asset.asset_id, asset.asset_unique_id, asset.asset_type_id', false)
                ->join('asset_types', 'asset_types.asset_type_id = asset.asset_type_id', 'left')
                ->where('asset_types.category_id', $category_id)
                ->where('asset.account_id', $account_id)
                ->where('asset.archived !=', 1)
                ->order_by('asset_types.asset_type, asset.asset_id')
                ->group_by('asset.asset_id')
                ->get('asset');

            if ($category_assets->num_rows() > 0) {
                if ($where['ids_only']) {
                    $result = array_column($category_assets->result_array(), 'asset_id');
                } else {
                    $result = $category_assets->result_array();
                }
            }
        }
        return $result;
    }

    /** Fetch Tracking Status by Job Status **/
    public function fetch_tracking_status($account_id = false, $job_data = false, $current_tracking_status = false)
    {
        $result = [];
        if (!empty($account_id) && !empty($job_data->job_status_group)) {
            switch(strtolower($job_data->job_status_group)) {
                case 'onsite':
                case 'enroute':
                case 'assigned':
                case 'inprogress':
                    if (empty($job_data->job_date)) {
                        if (!empty($current_tracking_status) && in_array($current_tracking_status, ['contactattempted'])) {
                            $where = ['account_id'=>$account_id, 'job_tracking_group'=>$current_tracking_status];
                        } else {
                            $where = ['account_id'=>$account_id, 'job_tracking_group'=>'callpending'];
                        }
                    } else {
                        $where = ['account_id'=>$account_id, 'job_tracking_group'=>'jobbooked'];
                    }
                    break;

                case 'unassigned':
                    $where = ['account_id'=>$account_id, 'job_tracking_group'=>'callpending'];
                    break;

                case 'successful':
                    /*if( !in_array( $current_tracking_status, ['jobinvoiced', 'invoicepaid'] ) ){
                        $where = ['account_id'=>$account_id, 'job_tracking_group'=>'adminquery'];
                    }*/
                    break;
                case 'failed':
                    $where = ['account_id'=>$account_id, 'job_tracking_group'=>'failed'];
                    break;
                case 'cancelled':
                    $where = ['account_id'=>$account_id, 'job_tracking_group'=>'cancelled'];
                    break;
            }
            if (!empty($where)) {
                $row = $this->db->get_where('job_tracking_statuses', $where)->row();
                if (!empty($row)) {
                    $result['job_tracking_id'] = $row->job_tracking_id;
                }
            }
        }
        return $result;
    }

    /** Validate Job Type **/
    public function _validate_job_type($account_id = false, $job_type_id = false, $job_type = false)
    {
        $result = false;
        if (!empty($account_id) && (!empty($job_type_id) || !empty($job_type))) {
            $this->db->select('job_type_id, job_type, job_base_rate, notification_required, notification_emails, notify_engineer', false);

            if (!empty($job_type_id)) {
                $validdate_job_type = $this->db->get_where('job_types', [ 'account_id'=>$account_id, 'job_type_id'=>$job_type_id ])->row();
            } elseif (!empty($job_type)) {
                $validdate_job_type = $this->db->where('account_id', $account_id)
                    ->where('job_type', $job_type)
                    ->or_where('job_type_ref', strip_all_whitespace($job_type))
                    ->or_where('job_group', strip_all_whitespace($job_type))
                    ->limit(1)
                    ->get('job_types')->row();
            }

            if (!empty($validdate_job_type)) {
                $validdate_job_type->notification_emails	= is_json($validdate_job_type->notification_emails) ? json_decode($validdate_job_type->notification_emails) : $validdate_job_type->notification_emails;
                $this->session->set_flashdata('message', 'Job Type validated.');
                $result = $validdate_job_type;
            } else {
                $this->session->set_flashdata('message', 'Invalid Job Type.');
            }
        }
        return $result;
    }


    /*
    *	Get list of Symptom codes list and search though it
    */
    public function get_symptom_codes($account_id = false, $symptom_code_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select('job_symptom_codes.*, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false)
                ->join('user creater', 'creater.id = job_symptom_codes.created_by', 'left')
                ->join('user modifier', 'modifier.id = job_symptom_codes.last_modified_by', 'left')
                ->where('job_symptom_codes.is_active', 1)
                ->where('job_symptom_codes.account_id', $account_id);

            $where = $raw_where = convert_to_array($where);

            if (!empty($symptom_code_id) || isset($where['symptom_code_id'])) {
                $symptom_code_id	= (!empty($symptom_code_id)) ? $symptom_code_id : $where['symptom_code_id'];
                if (!empty($symptom_code_id)) {
                    $row = $this->db->get_where('job_symptom_codes', ['symptom_code_id'=>$symptom_code_id ])->row();

                    if (!empty($row)) {
                        $result = ( object ) ['records'=>$row];
                        $this->session->set_flashdata('message', 'Symptom Code data found');
                        return $result;
                    } else {
                        $this->session->set_flashdata('message', 'Symptom Code data not found');
                        return false;
                    }
                }
                unset($where['symptom_code_id'], $where['symptom_code_ref']);
            }

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->symptom_codes_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->symptom_codes_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                if (isset($where['symptom_code'])) {
                    if (!empty($where['symptom_code'])) {
                        $symptom_code_ref = strtoupper(strip_all_whitespace($where['symptom_code']));
                        $this->db->where('( job_symptom_codes.symptom_code = "'.$where['symptom_code'].'" OR job_symptom_codes.symptom_code_ref = "'.$symptom_code_ref.'" )');
                    }
                    unset($where['symptom_code']);
                }

                if (isset($where['grouped'])) {
                    if (!empty($where['grouped'])) {
                        $grouped_results = 1;
                    }
                    unset($where['grouped']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            if (!empty($order_by)) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('symptom_code_id DESC, symptom_code');
            }

            $query = $this->db->get('job_symptom_codes');

            if ($query->num_rows() > 0) {
                if (!empty($grouped_results)) {
                    $result_data = [];
                    foreach ($query->result() as $k => $row) {
                        $result_data[$row->symptom_code_group][] = $row;
                    }
                } else {
                    $result_data = $query->result();
                }

                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $result->records 			= $result_data;
                $counters 					= $this->symptom_codes_totals($account_id, $search_term, $raw_where);
                $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
                $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
                $result->counters->limit  	= (!empty($apply_limit)) ? $limit : $result->counters->total;
                $result->counters->offset 	= $offset;

                $this->session->set_flashdata('message', 'Symptom Codes data found');
            } else {
                $this->session->set_flashdata('message', 'There\'s currently no Symptom codes setup for your Account');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }


    /** Get Symptom Codes lookup counts **/
    public function symptom_codes_totals($account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('job_symptom_codes.symptom_code_id', false)
                ->where('job_symptom_codes.is_active', 1)
                ->where('job_symptom_codes.account_id', $account_id);

            $where = $raw_where = convert_to_array($where);

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->symptom_codes_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->symptom_codes_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                if (isset($where['symptom_code'])) {
                    if (!empty($where['symptom_code'])) {
                        $symptom_code_ref = strtoupper(strip_all_whitespace($where['symptom_code']));
                        $this->db->where('( job_symptom_codes.symptom_code = "'.$where['symptom_code'].'" OR job_symptom_codes.symptom_code_ref = "'.$symptom_code_ref.'" )');
                    }
                    unset($where['symptom_code']);
                }

                if (isset($where['grouped'])) {
                    if (!empty($where['grouped'])) {
                        $grouped_results = 1;
                    }
                    unset($where['grouped']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $query 			  = $this->db->from('job_symptom_codes')->count_all_results();
            $results['total'] = !empty($query) ? $query : 0;
            $limit 			  = (!empty($apply_limit)) ? $limit : $results['total'];
            $results['pages'] = !empty($query) ? ceil($query / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }


    /*
    *	Get list of Fault codes list and search though it
    */
    public function get_fault_codes($account_id = false, $fault_code_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select('job_fault_codes.*, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false)
                ->join('user creater', 'creater.id = job_fault_codes.created_by', 'left')
                ->join('user modifier', 'modifier.id = job_fault_codes.last_modified_by', 'left')
                ->where('job_fault_codes.is_active', 1)
                ->where('job_fault_codes.account_id', $account_id);

            $where = $raw_where = convert_to_array($where);

            if (!empty($fault_code_id) || isset($where['fault_code_id'])) {
                $fault_code_id	= (!empty($fault_code_id)) ? $fault_code_id : $where['fault_code_id'];
                if (!empty($fault_code_id)) {
                    $row = $this->db->get_where('job_fault_codes', ['fault_code_id'=>$fault_code_id ])->row();

                    if (!empty($row)) {
                        $result = ( object ) ['records'=>$row];
                        $this->session->set_flashdata('message', 'Fault Code data found');
                        return $result;
                    } else {
                        $this->session->set_flashdata('message', 'Fault Code data not found');
                        return false;
                    }
                }
                unset($where['fault_code_id'], $where['fault_code_ref']);
            }

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->fault_codes_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->fault_codes_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                if (isset($where['fault_code'])) {
                    if (!empty($where['fault_code'])) {
                        $fault_code_ref = strtoupper(strip_all_whitespace($where['fault_code']));
                        $this->db->where('( job_fault_codes.fault_code = "'.$where['fault_code'].'" OR job_fault_codes.fault_code_ref = "'.$fault_code_ref.'" )');
                    }
                    unset($where['fault_code']);
                }

                if (isset($where['grouped'])) {
                    if (!empty($where['grouped'])) {
                        $grouped_results = 1;
                    }
                    unset($where['grouped']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            if (!empty($order_by)) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('fault_code_id DESC, fault_code');
            }

            $query = $this->db->get('job_fault_codes');

            if ($query->num_rows() > 0) {
                if (!empty($grouped_results)) {
                    $result_data = [];
                    foreach ($query->result() as $k => $row) {
                        $result_data[$row->fault_code_group][] = $row;
                    }
                } else {
                    $result_data = $query->result();
                }

                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $result->records 			= $result_data;
                $counters 					= $this->fault_codes_totals($account_id, $search_term, $raw_where);
                $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
                $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
                $result->counters->limit  	= (!empty($apply_limit)) ? $limit : $result->counters->total;
                $result->counters->offset 	= $offset;

                $this->session->set_flashdata('message', 'Fault Codes data found');
            } else {
                $this->session->set_flashdata('message', 'There\'s currently no Fault codes setup for your Account');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }


    /** Get Fault Codes lookup counts **/
    public function fault_codes_totals($account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('job_fault_codes.fault_code_id', false)
                ->where('job_fault_codes.is_active', 1)
                ->where('job_fault_codes.account_id', $account_id);

            $where = $raw_where = convert_to_array($where);

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->fault_codes_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->fault_codes_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                if (isset($where['fault_code'])) {
                    if (!empty($where['fault_code'])) {
                        $fault_code_ref = strtoupper(strip_all_whitespace($where['fault_code']));
                        $this->db->where('( job_fault_codes.fault_code = "'.$where['fault_code'].'" OR job_fault_codes.fault_code_ref = "'.$fault_code_ref.'" )');
                    }
                    unset($where['fault_code']);
                }

                if (isset($where['grouped'])) {
                    if (!empty($where['grouped'])) {
                        $grouped_results = 1;
                    }
                    unset($where['grouped']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $query 			  = $this->db->from('job_fault_codes')->count_all_results();
            $results['total'] = !empty($query) ? $query : 0;
            $limit 			  = (!empty($apply_limit)) ? $limit : $results['total'];
            $results['pages'] = !empty($query) ? ceil($query / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }

        /*
    *	Get list of Repair codes list and search though it
    */
    public function get_repair_codes($account_id = false, $repair_code_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select('job_repair_codes.*, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false)
                ->join('user creater', 'creater.id = job_repair_codes.created_by', 'left')
                ->join('user modifier', 'modifier.id = job_repair_codes.last_modified_by', 'left')
                ->where('job_repair_codes.is_active', 1)
                ->where('job_repair_codes.account_id', $account_id);

            $where = $raw_where = convert_to_array($where);

            if (!empty($repair_code_id) || isset($where['repair_code_id'])) {
                $repair_code_id	= (!empty($repair_code_id)) ? $repair_code_id : $where['repair_code_id'];
                if (!empty($repair_code_id)) {
                    $row = $this->db->get_where('job_repair_codes', ['repair_code_id'=>$repair_code_id ])->row();

                    if (!empty($row)) {
                        $result = ( object ) ['records'=>$row];
                        $this->session->set_flashdata('message', 'Repair Code data found');
                        return $result;
                    } else {
                        $this->session->set_flashdata('message', 'Repair Code data not found');
                        return false;
                    }
                }
                unset($where['repair_code_id'], $where['repair_code_ref']);
            }

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->repair_codes_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->repair_codes_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                if (isset($where['repair_code'])) {
                    if (!empty($where['repair_code'])) {
                        $repair_code_ref = strtoupper(strip_all_whitespace($where['repair_code']));
                        $this->db->where('( job_repair_codes.repair_code = "'.$where['repair_code'].'" OR job_repair_codes.repair_code_ref = "'.$repair_code_ref.'" )');
                    }
                    unset($where['repair_code']);
                }

                if (isset($where['grouped'])) {
                    if (!empty($where['grouped'])) {
                        $grouped_results = 1;
                    }
                    unset($where['grouped']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            if (!empty($order_by)) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('repair_code_id DESC, repair_code');
            }

            $query = $this->db->get('job_repair_codes');

            if ($query->num_rows() > 0) {
                if (!empty($grouped_results)) {
                    $result_data = [];
                    foreach ($query->result() as $k => $row) {
                        $result_data[$row->repair_code_group][] = $row;
                    }
                } else {
                    $result_data = $query->result();
                }

                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $result->records 			= $result_data;
                $counters 					= $this->repair_codes_totals($account_id, $search_term, $raw_where);
                $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
                $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
                $result->counters->limit  	= (!empty($apply_limit)) ? $limit : $result->counters->total;
                $result->counters->offset 	= $offset;

                $this->session->set_flashdata('message', 'Repair Codes data found');
            } else {
                $this->session->set_flashdata('message', 'There\'s currently no Repair codes setup for your Account');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }


    /** Get Repair Codes lookup counts **/
    public function repair_codes_totals($account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('job_repair_codes.repair_code_id', false)
                ->where('job_repair_codes.is_active', 1)
                ->where('job_repair_codes.account_id', $account_id);

            $where = $raw_where = convert_to_array($where);

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->repair_codes_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->repair_codes_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                if (isset($where['repair_code'])) {
                    if (!empty($where['repair_code'])) {
                        $repair_code_ref = strtoupper(strip_all_whitespace($where['repair_code']));
                        $this->db->where('( job_repair_codes.repair_code = "'.$where['repair_code'].'" OR job_repair_codes.repair_code_ref = "'.$repair_code_ref.'" )');
                    }
                    unset($where['repair_code']);
                }

                if (isset($where['grouped'])) {
                    if (!empty($where['grouped'])) {
                        $grouped_results = 1;
                    }
                    unset($where['grouped']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $query 			  = $this->db->from('job_repair_codes')->count_all_results();
            $results['total'] = !empty($query) ? $query : 0;
            $limit 			  = (!empty($apply_limit)) ? $limit : $results['total'];
            $results['pages'] = !empty($query) ? ceil($query / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }


    /** Get Assigned Job By Engineer **/
    public function get_assigned_jobs_by_engineer($account_id = false, $engineer_id = false, $where = false)
    {
        $result = false;

        $where = !empty($where) ? convert_to_array($where) : false;

        if (!empty($account_id) && !empty($where['job_date'])) {
            $associated_user_id = $user_ids = false;

            if (!empty($where['associated_user_id'])) {
                $associated_user_id = $where['associated_user_id'];
                unset($where['associated_user_id']);
                $helper_query = $this->db->get_where("associated_users", ["account_id" => $account_id, "primary_user_id" => $associated_user_id])->result_array();
                if (!empty($helper_query)) {
                    $user_ids = array_column($helper_query, 'user_id');
                    if (!empty($user_ids)) {
                        $user_ids[] = $associated_user_id;
                    }
                }
            }

            $this->db->select('job.job_id, job.site_id, job.asset_id, job.customer_id, job.address_id, job.account_id, job.job_date, job.due_date, job.assigned_to, job.job_type_id, job_types.job_type, job.client_reference, job.status_id, job.fail_code_id, job.fail_notes, job.job_order, job.region_id, diary_regions.region_name, job_statuses.job_status, job_statuses.status_group, fc.fail_code, fc.fail_code_text, fc.fail_code_desc, fc.fail_code_group, CONCAT(user.first_name," ",user.last_name) `assignee`, addrs.main_address_id,addrs.addressline1 `address_line_1`, addrs.addressline2 `address_line_2`,addrs.addressline3 `address_line_3`,addrs.posttown `address_city`,addrs.county `address_county`, addrs.postcode `job_postcode`, postcode_area, postcode_district, postcode_sector, addrs.summaryline `summaryline`, pca.address_line1, pca.address_line2, pca.address_town, pca.address_postcode, site.site_name, site.site_postcodes, site.site_reference', false)
                ->join('addresses addrs', 'addrs.main_address_id = job.address_id', 'left')
                ->join('job_types', 'job_types.job_type_id = job.job_type_id', 'left')
                ->join('job_statuses', 'job_statuses.status_id = job.status_id', 'left')
                ->join('job_fail_codes fc', 'fc.fail_code_id = job.fail_code_id', 'left')
                ->join('user', 'user.id = job.assigned_to', 'left')
                ->join('people_contact_addresses pca', 'pca.person_id = user.id', 'left')
                ->join('diary_regions', 'diary_regions.region_id = job.region_id', 'left')
                ->join('site', 'site.site_id = job.site_id', 'left')
                ->where('job.archived !=', 1)
                ->where('job.assigned_to > 0');

            if (!empty($engineer_id)) {
                $this->db->where('job.assigned_to', $engineer_id);
            }

            if (isset($where['job_type_id'])) {
                if (!empty($where['job_type_id'])) {
                    $job_type_id = convert_to_array($where['job_type_id']);
                    $job_type_id = is_array($job_type_id) ? $job_type_id : [ $job_type_id ];
                    $this->db->where_in('job.job_type_id', $job_type_id);
                }
                unset($where['job_type_id']);
            }

            if (isset($where['region_id'])) {
                if (!empty($where['region_id'])) {
                    $region_id = convert_to_array($where['region_id']);
                    $region_id = is_array($region_id) ? $region_id : [ $region_id ];
                    $this->db->where_in('job.region_id', $region_id);
                }
                unset($where['region_id']);
            }

            if (isset($where['assigned_to'])) {
                if (!empty($where['assigned_to'])) {
                    $this->db->where('job.assigned_to', $where['assigned_to']);
                }
                unset($where['assigned_to']);
            }

            if (!empty($where['job_date'])) {
                $job_date 	= date('Y-m-d', strtotime($where['job_date']));
                $this->db->where('job_date', $job_date);
                unset($where['job_date']);
            } elseif (!empty($where['date_from'])) {
                $date_from 	= date('Y-m-d', strtotime($where['date_from']));
                $date_to 	= (!empty($where['date_to'])) ? date('Y-m-d', strtotime($where['date_to'])) : date('Y-m-d');
                $this->db->where('job_date >=', $date_from);
                $this->db->where('job_date <=', $date_to);
                unset($where['date_from']);
            } elseif (!empty($where['date_to'])) {
                $job_date = date('Y-m-d', strtotime($where['date_to']));
                $this->db->where('job_date', $job_date);
                unset($where['date_to']);
            }

            if (isset($where['exclude_successful_jobs'])) {
                if (!empty($where['exclude_successful_jobs'])) {
                    $this->db->where_not_in('job.status_id', [ 4 ]); //Remove Successful Jobs
                }
                unset($where['exclude_successful_jobs']);
            }

            if (!empty($user_ids)) {
                $this->db->where_in('user.id', $user_ids);
            }

            $job = $this->db->order_by('job_id desc, job_date desc, job_type')
                ->get('job');

            if ($job->num_rows() > 0) {
                $engineer_data = $global_counters = $job_types =  $regions = [];

                foreach ($job->result() as $key => $row) {
                    if (!empty($row->assignee)) {
                        $engineer_data[$row->assigned_to]['engineer_id']	 					= $row->assigned_to;
                        $engineer_data[$row->assigned_to]['engineer_name'] 	 					= $row->assignee;
                        $engineer_data[$row->assigned_to]['home_postcode'] 	 					= strtoupper($row->address_postcode);
                        ;
                        $engineer_data[$row->assigned_to]['home_address'] 	 					= ucwords(strtolower($row->address_line1.' '.$row->address_town)).' '.strtoupper($row->address_postcode);
                        ;
                        $engineer_data[$row->assigned_to]['total_jobs']							= !empty($engineer_data[$row->assigned_to]['total_jobs']) ? ($engineer_data[$row->assigned_to]['total_jobs'] + 1) : 1;
                        $engineer_data[$row->assigned_to]['status_counters'][$row->status_group]= !empty($engineer_data[$row->assigned_to]['status_counters'][$row->status_group]) ? ($engineer_data[$row->assigned_to]['status_counters'][$row->status_group] + 1) : 1;
                        $engineer_data[$row->assigned_to]['status_jobs'][$row->job_status][]	= $row;
                        $global_counters[$row->job_status]  									= !empty($global_counters[$row->job_status]) ? ($global_counters[$row->job_status] + 1) : 1;
                        $global_counters['Total']  												= !empty($global_counters['Total']) ? ($global_counters['Total'] + 1) : 1;
                        $job_types[$row->job_type_id]											= [ 'job_type_id' 	=> $row->job_type_id, 'job_type' => $row->job_type ];
                        $regions[$row->region_id]												= [ 'region_id' 	=> $row->region_id,	'region_name' 	=> $row->region_name ];
                    }
                }

                $result = ( object )[
                    'records' 	=> $engineer_data,
                    'counters'	=> $global_counters,
                    'job_types'	=> $job_types,
                    'regions'	=> $regions
                ];

                $this->session->set_flashdata('message', 'Job records found');
            } else {
                $this->session->set_flashdata('message', 'Job record(s) not found');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }

    /** Bulk Re-assign Jobs **/
    public function bulk_reassign_jobs($account_id = false, $postdata = false)
    {
        $result 	= null;


        if (!empty($account_id) && !empty($postdata)) {
            $postdata			= convert_to_array($postdata);

            $jobs_data 			= [];
            $assign_to 			= !empty($postdata['assigned_to']) ? $postdata['assigned_to'] : false;
            $job_date 			= !empty($postdata['job_date']) ? date('Y-m-d', strtotime($postdata['job_date'])) : false ;
            $booked_jobs 		= !empty($postdata['booked_jobs']) ? true : (!empty($postdata['book_jobs']) ? true : false);
            $rebook_date 		= !empty($postdata['rebook_date']) ? date('Y-m-d', strtotime($postdata['rebook_date'])) : false ;

            $jobs_to_reassign 	= !empty($postdata['jobs_to_reassign']) ? $postdata['jobs_to_reassign'] : false;
            $jobs_to_reassign 	= convert_to_array($jobs_to_reassign);

            if (!empty($jobs_to_reassign)) {
                foreach ($jobs_to_reassign as $key => $job_id) {
                    $check_job_status = $this->db->select('job_id, status_id', false)->get_where('job', [ 'job.account_id' => $account_id, 'job.job_id' => $job_id ])->row();

                    if (!empty($assign_to)) {
                        $job_obj = [
                            'job_id'			=> $job_id,
                            'assigned_to'		=> $assign_to,
                            'status_id'			=> (!empty($check_job_status->status_id) && ($check_job_status->status_id != 2)) ? $check_job_status->status_id : 1,
                            'last_modified_by'	=> $this->ion_auth->_current_user->id
                        ];

                        if (!empty($rebook_date)) {
                            $job_obj['job_date'] =  $rebook_date;
                        }

                        $jobs_data[$key]	= $job_obj;
                    } elseif (!empty($rebook_date)) {
                        $job_obj = [
                            'job_id'			=> $job_id,
                            'job_date'			=> $rebook_date,
                            'last_modified_by'	=> $this->ion_auth->_current_user->id
                        ];

                        if (!empty($assign_to)) {
                            $job_obj['assigned_to'] =  $assign_to;
                            $job_obj['status_id'] 	=  (!empty($check_job_status->status_id) && ($check_job_status->status_id != 2)) ? $check_job_status->status_id : 1;
                        }

                        $jobs_data[$key]	= $job_obj;
                    }

                    if (!empty($job_date) && !empty($booked_jobs)) {
                        $jobs_data[$key]['job_date'] 	= $job_date;
                        $jobs_data[$key]['status_id'] 	= (!empty($check_job_status->status_id) && ($check_job_status->status_id != 2)) ? $check_job_status->status_id : 1;
                    }
                }

                if (!empty($jobs_data)) {
                    $this->db->update_batch('job', $jobs_data, 'job_id');
                }

                if ($this->db->trans_status() !== false || ($this->db->affected_rows() > 0)) {
                    $result = true;

                    if (!empty($rebook_date)) {
                        $this->session->set_flashdata('message', count($jobs_data).' Job(s) were successfully re-booked.');
                    } else {
                        $this->session->set_flashdata('message', count($jobs_data).' Job(s) were successfully re-assigned.');
                    }
                } else {
                    $result = false;
                    $this->session->set_flashdata('message', 'Request Failed: There was an error trying to re-assign Jobs.');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }
        return $result;
    }


    /*
    * Search through Jobs
    */
    public function job_search($account_id = false, $job_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            $today				= date('Y-m-d', strtotime(_datetime()));
            $where 				= $raw_where 	= (!empty($where)) ? convert_to_array($where) : false;

            if (isset($where['region_id'])) {
                if (!empty($where['region_id'])) {
                    $region_id = convert_to_array($where['region_id']);
                    $region_id = is_array($region_id) ? $region_id : [ $region_id ];
                }

                unset($where['region_id']);
                $region_sites = $this->db->select('site.site_id, site.region_id', false)->where_in('site.region_id', $region_id)->get_where('site', ['account_id' => $account_id ])->result_array();
                if (!empty($region_sites)) {
                    $site_ids = array_column($region_sites, 'site_id');
                }
            }


            $supervisor_id 		= !empty($where['supervisor_id']) ? $where['supervisor_id'] : false;
            if (!empty($supervisor_id)) {
                $supervised_staff 	= $this->_get_supervised_staff($account_id, $supervisor_id, true);
                unset($where['supervised_staff']);
            }

            $assignees 			= $this->_check_jobs_access($this->ion_auth->_current_user());

            #Limit Jobs List access by Associated Buildings
            if ((!$this->ion_auth->_current_user()->is_admin) && !empty($this->ion_auth->_current_user()->buildings_visibility) && (strtolower($this->ion_auth->_current_user()->buildings_visibility) == 'limited')) {
                $buildings_access 	= $this->site_service->get_user_associated_buildings($account_id, $this->ion_auth->_current_user->id);
                $allowed_buildings  = !empty($buildings_access) ? array_column($buildings_access, 'site_id') : [];

                if (!empty($allowed_buildings)) {
                    $site_ids_str 	= implode(',', $allowed_buildings);

                    $linked_assets = $this->db->select('asset.asset_id', false)
                        ->where_in('asset.site_id', $allowed_buildings)
                        ->where('asset.account_id', $account_id)
                        ->where('asset.archived !=', 1)
                        ->group_by('asset.asset_id')
                        ->get('asset');

                    if ($linked_assets->num_rows() > 0) {
                        $asset_ids 		= array_column($linked_assets->result_array(), 'asset_id');
                        $asset_ids_str 	= implode(',', $asset_ids);
                        $sql_combi 		= '( job.site_id IN ('.$site_ids_str.' ) OR job.asset_id IN ('.$asset_ids_str.' ) )';
                    } else {
                        $sql_combi		= '( job.site_id IN ('.$site_ids_str.' ) )';
                    }

                    $this->db->where($sql_combi);
                } else {
                    $this->session->set_flashdata('message', 'No data found matching your criteria.');
                    return false;
                }
            }

            #Limit access by contract to External User Types
            if (in_array($this->ion_auth->_current_user()->user_type_id, EXTERNAL_USER_TYPES)) {
                if (!empty($this->ion_auth->_current_user()->is_primary_user)) {
                    ## Get associated users
                    if (!$job_id) {
                        $group_assignees = $this->ion_auth->get_associated_users($account_id, $this->ion_auth->_current_user()->id, false, ['as_arraay'=>1]);
                        if (!empty($group_assignees)) {
                            $group_assignees = (!empty($group_assignees)) ? array_column($group_assignees, 'user_id') : [$this->ion_auth->_current_user()->id];
                            $group_assignees = (!in_array($this->ion_auth->_current_user()->id, $group_assignees)) ? array_merge($group_assignees, [$this->ion_auth->_current_user()->id]) : $group_assignees;
                            $raw_where['group_assignees']	= $group_assignees;
                            $this->db->where_in('job.assigned_to', $group_assignees);
                        } else {
                            $contract_access = $this->contract_service->get_linked_people($account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1]);
                            $allowed_access  = !empty($contract_access) ? array_column($contract_access, 'contract_id') : [];
                            if (!empty($allowed_access)) {
                                $this->db->where_in('job_types.contract_id', $allowed_access);
                            } else {
                                $this->session->set_flashdata('message', 'No data found matching your criteria');
                                return false;
                            }
                        }
                    }
                } else {
                    $contract_access = $this->contract_service->get_linked_people($account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1]);
                    $allowed_access  = !empty($contract_access) ? array_column($contract_access, 'contract_id') : [];
                    if (!empty($allowed_access)) {
                        $this->db->where_in('job_types.contract_id', $allowed_access);
                    } else {
                        $this->session->set_flashdata('message', 'No data found matching your criteria');
                        return false;
                    }
                }
            }

            $raw_where['assignees']	= $assignees;

            $this->db->select('job.*, job_types.contract_id as contract_id, job_types.job_type, job_types.is_reactive, job_types.discipline_id, account_discipline.account_discipline_name `discipline_name`, job_statuses.job_status, job_statuses.status_group, job_tracking_statuses.job_tracking_status, job_tracking_statuses.job_tracking_group, CONCAT(user.first_name," ",user.last_name) `assignee`, diary_regions.region_name, contract.contract_name, job.last_modified `job_last_modified_time`, ( CASE WHEN ( site.site_name != "" ) THEN site.site_name ELSE site_asset.site_name END ) `site_name`, site.site_actual_address, site.site_actual_postcode, site.site_address_verified', false);			#$this->db->select( 'addrs.postcode `postcode`',false );
            #$this->db->join( 'addresses addrs','addrs.main_address_id = job.address_id','left' )
            $this->db->join('job_types', 'job_types.job_type_id = job.job_type_id', 'left')
                ->join('contract', 'job_types.contract_id = contract.contract_id', 'left')
                ->join('diary_regions', 'diary_regions.region_id = job.region_id', 'left')
                ->join('job_statuses', 'job_statuses.status_id = job.status_id', 'left')
                ->join('job_tracking_statuses', 'job_tracking_statuses.job_tracking_id = job.job_tracking_id', 'left')
                ->join('user', 'user.id = job.assigned_to', 'left')
                ->join('site', 'site.site_id = job.site_id', 'left')
                ->join('asset', 'asset.asset_id = job.asset_id', 'left')
                ->join('site site_asset', 'site_asset.site_id = asset.site_id', 'left')
                ->join('account_discipline', 'account_discipline.discipline_id = job_types.discipline_id', 'left')
                ->where('job.account_id', $account_id)
                ->where('job.archived !=', 1);

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->minimal_searchable_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        if (!empty($search_where['job.contract_id'])) {
                            $search_where['contract.contract_name'] 		 =  trim($term);
                            unset($search_where['job.contract_id']);
                        }

                        if (!empty($search_where['job.region_id'])) {
                            $search_where['diary_regions.region_name'] 		 =  trim($term);
                            unset($search_where['job.region_id']);
                        }

                        if (!empty($search_where['job.address_id'])) {
                            // $search_where['addrs.postcode'] 		 =  trim( $term );
                            // $search_where['addrs.postcode_nospaces'] =  trim( $term );
                            unset($search_where['job.address_id']);
                        }

                        if (!empty($search_where['job.status_id'])) {
                            $search_where['job_statuses.job_status'] =  trim($term);
                            unset($search_where['job.status_id']);
                        }

                        if (!empty($search_where['job.job_type_id'])) {
                            $search_where['job_types.job_type'] =  trim($term);
                            unset($search_where['job.job_type_id']);
                        }

                        if (!empty($search_where['job.assigned_to'])) {
                            $search_where['user.first_name'] =  trim($term);
                            $search_where['user.last_name'] =  trim($term);
                            unset($search_where['job.assigned_to']);
                        }

                        if (!empty($search_where['job.job_date'])) {
                            $job_date = date('Y-m-d', strtotime($term));
                            if (valid_date($job_date)) {
                                $search_where['job.job_date'] =  $job_date;
                            }
                            unset($search_where['job.job_date']);
                        }

                        if (!empty($search_where['job.job_tracking_id'])) {
                            $search_where['job_tracking_statuses.job_tracking_status'] =  trim($term);
                            unset($search_where['job.job_tracking_id']);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->minimal_searchable_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    if (!empty($search_where['job.contract_id'])) {
                        $search_where['contract.contract_name'] 		 =  trim($search_term);
                        unset($search_where['job.contract_id']);
                    }

                    if (!empty($search_where['job.region_id'])) {
                        $search_where['diary_regions.region_name'] 		 =  trim($search_term);
                        unset($search_where['job.region_id']);
                    }

                    if (!empty($search_where['job.address_id'])) {
                        // $search_where['addrs.postcode'] 		 =  trim( $search_term );
                        // $search_where['addrs.postcode_nospaces'] =  trim( $search_term );
                        unset($search_where['job.address_id']);
                    }

                    if (!empty($search_where['job.status_id'])) {
                        $search_where['job_statuses.job_status'] =  trim($search_term);
                        unset($search_where['job.status_id']);
                    }

                    if (!empty($search_where['job.job_type_id'])) {
                        $search_where['job_types.job_type'] =  trim($search_term);
                        unset($search_where['job.job_type_id']);
                    }

                    if (!empty($search_where['job.assigned_to'])) {
                        $search_where['user.first_name'] =  trim($search_term);
                        $search_where['user.last_name'] =  trim($search_term);
                        unset($search_where['job.assigned_to']);
                    }

                    if (!empty($search_where['job.job_date'])) {
                        $job_date = date('Y-m-d', strtotime($search_term));
                        if (valid_date($job_date)) {
                            $search_where['job.job_date'] =  $job_date;
                        }
                        unset($search_where['job.job_date']);
                    }

                    if (!empty($search_where['job.job_tracking_id'])) {
                        $search_where['job_tracking_statuses.job_tracking_status'] =  trim($search_term);
                        unset($search_where['job.job_tracking_id']);
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (isset($where['contract_id'])) {
                if (!empty($where['contract_id'])) {
                    $contract_id = $where['contract_id'];
                    $contract_id = convert_to_array($where['contract_id']);
                    $contract_id = is_array($contract_id) ? $contract_id : [ $contract_id ];
                    $this->db->where_in('job_types.contract_id', $contract_id);
                }
                unset($where['contract_id']);
            }

            if (isset($where['site_id'])) {
                if (!empty($where['site_id'])) {
                    $site_id = $where['site_id'];
                    $site_id = convert_to_array($where['site_id']);
                    $site_id = is_array($site_id) ? $site_id : [ $site_id ];
                    $this->db->where_in('site.site_id', $site_id);
                }
                unset($where['site_id']);
            }

            /* if( isset( $where['region_id'] ) ){
                if( !empty( $where['region_id'] ) ){
                    $region_id = $where['region_id'];
                    $region_id = convert_to_array( $where['region_id'] );
                    $region_id = is_array( $region_id ) ? $region_id : [ $region_id ];
                    $this->db->where_in('job.region_id', $region_id );
                }
                unset( $where['region_id'] );
            } */

            if (!empty($where['region_id']) || !empty($region_id)) {
                $region_id = !empty($region_id) ? $region_id : convert_to_array($where['region_id']);
                $region_id = is_array($region_id) ? $region_id : [ $region_id ];

                if (!empty($region_id) && !empty($site_ids)) {
                    $region_sql = '( ( job.region_id IN ('.implode(",", $region_id).') ) OR job.site_id IN ('.implode(",", $site_ids).') )';
                    $this->db->where($region_sql);

                #$this->db->where( '( job.region_id IN ( '.implode( ', ', $region_id ).' ) OR job.site_id IN ( '.implode( ', ', $site_ids ).' ) )' );
                } else {
                    $this->db->where_in('job.region_id', $region_id);
                }
                unset($where['region_id']);
            }

            if (isset($where['job_type_id'])) {
                if (!empty($where['job_type_id'])) {
                    $job_types = (!is_array($where['job_type_id']) && ((int) $where['job_type_id'] > 0)) ? [ $where['job_type_id'] ] : ((is_array($where['job_type_id'])) ? $where['job_type_id'] : (is_object($where['job_type_id']) ? object_to_array($where['job_type_id']) : []));
                    $this->db->where_in('job.job_type_id', $job_types);
                }
                unset($where['job_type_id']);
            }

            if (isset($where['status_id'])) {
                if (!empty($where['status_id'])) {
                    $status_id = $where['status_id'];
                    $status_id = convert_to_array($where['status_id']);
                    $status_id = is_array($status_id) ? $status_id : [ $status_id ];
                    $this->db->where_in('job.status_id', $status_id);
                }
                unset($where['status_id']);
            }

            if (isset($where['job_tracking_id'])) {
                if (!empty($where['job_tracking_id'])) {
                    $job_tracking_id = $where['job_tracking_id'];
                    $job_tracking_id = convert_to_array($where['job_tracking_id']);

                    if (is_array($job_tracking_id)) {
                        if (in_array('_blanks', $job_tracking_id)) {
                            $to_remove 		 = ['_blanks'];
                            $job_tracking_id = array_diff($job_tracking_id, $to_remove);

                            $open_sql = '( ( job.job_tracking_id IS NULL OR job.job_tracking_id = "" ) ';

                            if (!empty($job_tracking_id)) {
                                $open_sql .= ' OR ( job.job_tracking_id IN ('. (implode(",", $job_tracking_id)) .') ) ';
                            }

                            $open_sql .= ' )';
                            $this->db->where($open_sql);
                        } else {
                            if (!empty($job_tracking_id)) {
                                $this->db->where_in('job.job_tracking_id', $job_tracking_id);
                            }
                        }
                    } else {
                        $job_tracking_id = [ $job_tracking_id ];
                        $this->db->where_in('job.job_tracking_id', $job_tracking_id);
                    }
                }
                unset($where['job_tracking_id']);
            }

            if (isset($where['combined_date_range'])) {
                $combi_job_data_sql = '';
                if (isset($where['date_from']) || isset($where['date_to'])) {
                    $date_from 	= date('Y-m-d', strtotime($where['date_from']));
                    $date_to 	= (!empty($where['date_to'])) ? date('Y-m-d', strtotime($where['date_to'])) : date('Y-m-d');
                    $combi_job_data_sql = '( ( job.job_date >= "'.$date_from.'" AND job.job_date <= "'.$date_to.'" ) OR ( job.due_date >= "'.$date_from.'" AND job.due_date <= "'.$date_to.'" AND job.job_date IS NULL ) )';
                    $this->db->where($combi_job_data_sql);
                    unset($where['date_from'], $where['date_to']);
                }
                unset($where['date_from'], $where['date_to']);
            }

            if (isset($where['job_type_id'])) {
                if (!empty($where['job_type_id'])) {
                    $job_types = (!is_array($where['job_type_id']) && ((int) $where['job_type_id'] > 0)) ? [ $where['job_type_id'] ] : ((is_array($where['job_type_id'])) ? $where['job_type_id'] : (is_object($where['job_type_id']) ? object_to_array($where['job_type_id']) : []));
                    $this->db->where_in('job.job_type_id', $job_types);
                }
                unset($where['job_type_id']);
            }

            if (isset($where['job_date_start']) || isset($where['job_date_end'])) {
                if (!empty($where['job_date_start'])) {
                    $this->db->where('job.job_date >=', format_date_db($where['job_date_start']));
                }
                unset($where['job_date_start']);

                if (!empty($where['job_date_end'])) {
                    $this->db->where('job.job_date <=', format_date_db($where['job_date_end']));
                    unset($where['job_date_end']);
                }
                unset($where['job_date_end']);
            }

            if (isset($where['created_on_start']) || isset($where['created_on_end'])) {
                if (!empty($where['created_on_start'])) {
                    $this->db->where('job.created_on >=', format_date_db($where['created_on_start']).' 00:00:00');
                }
                unset($where['created_on_start']);

                if (!empty($where['created_on_end'])) {
                    $this->db->where('job.created_on <=', format_date_db($where['created_on_end']).' 23:59:59');
                }
                unset($where['created_on_end']);
            }

            if (isset($where['job_date'])) {
                if (!empty($where['job_date'])) {
                    $sjob_date = date('Y-m-d', strtotime($where['job_date']));
                    $this->db->where('job.job_date', $sjob_date);
                    unset($where['job_date']);
                }
            } else {
                if (isset($where['date_from']) || isset($where['date_to'])) {
                    if (!empty($where['date_from'])) {
                        $this->db->where('job.job_date >=', date('Y-m-d', strtotime(format_date_db($where['date_from']))));
                    }

                    if (!empty($where['date_to'])) {
                        $this->db->where('job.job_date <=', date('Y-m-d', strtotime(format_date_db($where['date_to']))));
                    }
                    unset($where['date_from'], $where['date_to']);
                }
            }

            ## Combined assignees
            if (!empty($assignees)) {
                if (!empty($where['assigned_to'])) {
                    $assignees[] 		= $where['assigned_to'];
                }
                $this->db->where_in('job.assigned_to', $assignees);
            } else {
                if (isset($where['assigned_to'])) {
                    if (!empty($where['assigned_to'])) {
                        $assigned_to = $where['assigned_to'];
                        $assigned_to = convert_to_array($where['assigned_to']);
                        $assigned_to = is_array($assigned_to) ? $assigned_to : [ $assigned_to ];
                        $this->db->where_in('job.assigned_to', $assigned_to);
                    }
                    unset($where['assigned_to']);
                }
            }

            if (isset($where['discipline_id'])) {
                if (!empty($where['discipline_id'])) {
                    $disciplines = (!is_array($where['discipline_id']) && ((int) $where['discipline_id'] > 0)) ? [ $where['discipline_id'] ] : ((is_array($where['discipline_id'])) ? $where['discipline_id'] : (is_object($where['discipline_id']) ? object_to_array($where['discipline_id']) : []));
                    $this->db->where_in('job_types.discipline_id', $disciplines);
                }
                unset($where['discipline_id']);
            }

            if (isset($where['date_range'])) {
                if (!empty($where['date_range'])) {
                    $range_dates = $this->get_dates_from_date_range($account_id, $where['date_range']);
                    if (!empty($range_dates->date_from)) {
                        $this->db->where('job.job_date >=', date('Y-m-d', strtotime($range_dates->date_from)));
                        $raw_where = $range_dates->date_from;
                    }

                    if (!empty($range_dates->date_to)) {
                        $this->db->where('job.job_date <=', date('Y-m-d', strtotime($range_dates->date_to)));
                        $raw_where = $range_dates->date_to;
                    }
                }
                unset($where['date_range']);
            }

            if (isset($where['overdue_jobs'])) {
                if (!empty($where['overdue_jobs'])) {
                    $this->db->where('job.job_date <', $today);
                    $this->db->where_not_in('job_statuses.status_group', [ 'unassigned', 'successful', 'failed', 'cancelled' ]);
                }
                unset($where['overdue_jobs']);
            }

            if (isset($where['exclude_successful_jobs'])) {
                if (!empty($where['exclude_successful_jobs'])) {
                    $this->db->where_not_in('job.status_id', [ 4 ]); //Remove Successful Jobs
                }
                unset($where['exclude_successful_jobs']);
            }

            if (isset($where['open_jobs'])) {
                #if( !empty( $where['open_jobs'] ) ){
                $this->db->where('( job.job_date = "1970-01-01" OR job.job_date = "0000-00-00" OR job.job_date IS NULL )');
                $this->db->where('( job.due_date != "1970-01-01" AND job.due_date != "0000-00-00" AND job.due_date IS NOT NULL )');
                $this->db->where('( job.assigned_to > 0 )');
                #}
                unset($where['open_jobs']);
            }


            if (isset($where['is_reactive'])) {
                $this->db->where('job_types.is_reactive', $where['is_reactive']);
                unset($where['is_reactive']);
            }

            if (isset($where['is_scheduled'])) {
                $this->db->where('job.schedule_id >', 0);
                unset($where['is_scheduled']);
            }

            if (!empty($supervised_staff) && is_array($supervised_staff)) {
                $raw_where['supervised_staff'] = $supervised_staff;
                $this->db->where_in('job.assigned_to', $supervised_staff);
                $this->db->where('job.status_id', 10); //Awaiting approval
            }

            if (isset($where['job_status'])) {
                if (!empty($where['job_status'])) {
                    $job_status = $where['job_status'];
                    $job_status = convert_to_array($where['job_status']);
                    $job_status = is_array($job_status) ? $job_status : [ $job_status ];
                    $this->db->where_in('job_statuses.job_status', $job_status);
                }
                unset($where['job_status']);
            }

            if (!empty($where)) {
                #$this->db->where( $where );
            }

            if ($order_by) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('job.job_id desc, job.job_date desc');
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $this->db->group_by('job.job_id');
            $query = $this->db->get('job');

            if ($query->num_rows() > 0) {
                $data 						= [];
                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                foreach ($query->result() as $k => $row) {
                    $address_postcode = $this->db->select('postcode', false)
                        ->get_where('addresses', [ 'main_address_id'=>$row->address_id ])
                        ->row();
                    $row->postcode	= !empty($address_postcode->postcode) ? $address_postcode->postcode : null;
                    $data[$k] = $row;
                }

                $result->records 			= $data;
                $counters 					= $this->get_job_search_totals($account_id, $search_term, $raw_where, $limit);
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
    public function get_job_search_totals($account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;

        if (!empty($account_id)) {
            $today		= date('Y-m-d', strtotime(_datetime()));
            $where 		= $raw_where = (!empty($where)) ? convert_to_array($where) : false;

            if (isset($where['region_id'])) {
                if (!empty($where['region_id'])) {
                    $region_id = convert_to_array($where['region_id']);
                    $region_id = is_array($region_id) ? $region_id : [ $region_id ];
                }

                unset($where['region_id']);
                $region_sites = $this->db->select('site.site_id, site.region_id', false)->where_in('site.region_id', $region_id)->get_where('site', ['account_id' => $account_id ])->result_array();
                if (!empty($region_sites)) {
                    $site_ids = array_column($region_sites, 'site_id');
                }
            }

            #Limit Jobs List access by Associated Buildings
            if ((!$this->ion_auth->_current_user()->is_admin) && !empty($this->ion_auth->_current_user()->buildings_visibility) && (strtolower($this->ion_auth->_current_user()->buildings_visibility) == 'limited')) {
                $buildings_access 	= $this->site_service->get_user_associated_buildings($account_id, $this->ion_auth->_current_user->id);
                $allowed_buildings  = !empty($buildings_access) ? array_column($buildings_access, 'site_id') : [];

                if (!empty($allowed_buildings)) {
                    $site_ids_str 	= implode(',', $allowed_buildings);

                    $linked_assets = $this->db->select('asset.asset_id', false)
                        ->where_in('asset.site_id', $allowed_buildings)
                        ->where('asset.account_id', $account_id)
                        ->where('asset.archived !=', 1)
                        ->group_by('asset.asset_id')
                        ->get('asset');

                    if ($linked_assets->num_rows() > 0) {
                        $asset_ids 		= array_column($linked_assets->result_array(), 'asset_id');
                        $asset_ids_str 	= implode(',', $asset_ids);
                        $sql_combi 		= '( job.site_id IN ('.$site_ids_str.' ) OR job.asset_id IN ('.$asset_ids_str.' ) )';
                    } else {
                        $sql_combi		= '( job.site_id IN ('.$site_ids_str.' ) )';
                    }

                    $this->db->where($sql_combi);
                } else {
                    $this->session->set_flashdata('message', 'No data found matching your criteria.');
                    return false;
                }
            }

            #Limit access by contract to External User Types
            if (in_array($this->ion_auth->_current_user()->user_type_id, EXTERNAL_USER_TYPES)) {
                if (!empty($this->ion_auth->_current_user()->is_primary_user)) {
                    $group_assignees = !empty($where['group_assignees']) ? $where['group_assignees'] : false;
                    if ($group_assignees) {
                        $this->db->where_in('job.assigned_to', $group_assignees);
                    } else {
                        $group_assignees = $this->ion_auth->get_associated_users($account_id, $this->ion_auth->_current_user()->id, false, ['as_arraay'=>1]);
                        if (!empty($group_assignees)) {
                            $group_assignees = (!empty($group_assignees)) ? array_column($group_assignees, 'user_id') : [$this->ion_auth->_current_user()->id];
                            $group_assignees = (!in_array($this->ion_auth->_current_user()->id, $group_assignees)) ? array_merge($group_assignees, [$this->ion_auth->_current_user()->id]) : $group_assignees;
                            $raw_where['group_assignees']	= $group_assignees;
                            $this->db->where_in('job.assigned_to', $group_assignees);
                        } else {
                            $contract_access = $this->contract_service->get_linked_people($account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1]);
                            $allowed_access  = !empty($contract_access) ? array_column($contract_access, 'contract_id') : [];
                            if (!empty($allowed_access)) {
                                $this->db->where_in('job_types.contract_id', $allowed_access);
                            } else {
                                $this->session->set_flashdata('message', 'No data found matching your criteria');
                                return false;
                            }
                        }
                    }
                    unset($where['group_assignees']);
                } else {
                    $contract_access = $this->contract_service->get_linked_people($account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1]);
                    $allowed_access  = !empty($contract_access) ? array_column($contract_access, 'contract_id') : [];
                    if (!empty($allowed_access)) {
                        $this->db->where_in('job_types.contract_id', $allowed_access);
                    } else {
                        $this->session->set_flashdata('message', 'No data found matching your criteria');
                        return false;
                    }
                }
            }

            ## Extract pre-assigned Assignees
            if (isset($where['assignees'])) {
                $assignees 	= $where['assignees'];
                unset($where['assignees']);
            }

            $this->db->select('job.job_id', false)
                #->join( 'addresses addrs','addrs.main_address_id = job.address_id','left' )
                ->join('job_types', 'job_types.job_type_id = job.job_type_id', 'left')
                ->join('contract', 'job_types.contract_id = contract.contract_id', 'left')
                ->join('diary_regions', 'diary_regions.region_id = job.region_id', 'left')
                ->join('job_statuses', 'job_statuses.status_id = job.status_id', 'left')
                ->join('job_tracking_statuses', 'job_tracking_statuses.job_tracking_id = job.job_tracking_id', 'left')
                ->join('user', 'user.id = job.assigned_to', 'left')
                ->join('site', 'site.site_id = job.site_id', 'left')
                ->join('asset', 'asset.asset_id = job.asset_id', 'left')
                ->join('site site_asset', 'site_asset.site_id = asset.site_id', 'left')
                ->where('job.account_id', $account_id)
                ->where('job.archived !=', 1);

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->minimal_searchable_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        if (!empty($search_where['job.contract_id'])) {
                            $search_where['contract.contract_name'] 		 =  trim($term);
                            unset($search_where['job.contract_id']);
                        }

                        if (!empty($search_where['job.region_id'])) {
                            $search_where['diary_regions.region_name'] 		 =  trim($term);
                            unset($search_where['job.region_id']);
                        }

                        if (!empty($search_where['job.address_id'])) {
                            // $search_where['addrs.postcode'] 		 =  trim( $term );
                            // $search_where['addrs.postcode_nospaces'] =  trim( $term );
                            unset($search_where['job.address_id']);
                        }

                        if (!empty($search_where['job.status_id'])) {
                            $search_where['job_statuses.job_status'] =  trim($term);
                            unset($search_where['job.status_id']);
                        }

                        if (!empty($search_where['job.job_type_id'])) {
                            $search_where['job_types.job_type'] =  trim($term);
                            unset($search_where['job.job_type_id']);
                        }

                        if (!empty($search_where['job.assigned_to'])) {
                            $search_where['user.first_name'] =  trim($term);
                            $search_where['user.last_name'] =  trim($term);
                            unset($search_where['job.assigned_to']);
                        }

                        if (!empty($search_where['job.job_date'])) {
                            $job_date = date('Y-m-d', strtotime($term));
                            if (valid_date($job_date)) {
                                $search_where['job.job_date'] =  $job_date;
                            }
                            unset($search_where['job.job_date']);
                        }

                        if (!empty($search_where['job.job_tracking_id'])) {
                            $search_where['job_tracking_statuses.job_tracking_status'] =  trim($term);
                            unset($search_where['job.job_tracking_id']);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->minimal_searchable_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    if (!empty($search_where['job.contract_id'])) {
                        $search_where['contract.contract_name'] 		 =  trim($search_term);
                        unset($search_where['job.contract_id']);
                    }

                    if (!empty($search_where['job.region_id'])) {
                        $search_where['diary_regions.region_name'] 		 =  trim($search_term);
                        unset($search_where['job.region_id']);
                    }

                    if (!empty($search_where['job.address_id'])) {
                        // $search_where['addrs.postcode'] 		 =  trim( $search_term );
                        // $search_where['addrs.postcode_nospaces'] =  trim( $search_term );
                        unset($search_where['job.address_id']);
                    }

                    if (!empty($search_where['job.status_id'])) {
                        $search_where['job_statuses.job_status'] =  trim($search_term);
                        unset($search_where['job.status_id']);
                    }

                    if (!empty($search_where['job.job_type_id'])) {
                        $search_where['job_types.job_type'] =  trim($search_term);
                        unset($search_where['job.job_type_id']);
                    }

                    if (!empty($search_where['job.assigned_to'])) {
                        $search_where['user.first_name'] =  trim($search_term);
                        $search_where['user.last_name'] =  trim($search_term);
                        unset($search_where['job.assigned_to']);
                    }

                    if (!empty($search_where['job.job_date'])) {
                        $job_date = date('Y-m-d', strtotime($search_term));
                        if (valid_date($job_date)) {
                            $search_where['job.job_date'] =  $job_date;
                        }
                        unset($search_where['job.job_date']);
                    }

                    if (!empty($search_where['job.job_tracking_id'])) {
                        $search_where['job_tracking_statuses.job_tracking_status'] =  trim($search_term);
                        unset($search_where['job.job_tracking_id']);
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (isset($where['combined_date_range'])) {
                $combi_job_data_sql = '';
                if (isset($where['date_from']) || isset($where['date_to'])) {
                    $date_from 	= date('Y-m-d', strtotime($where['date_from']));
                    $date_to 	= (!empty($where['date_to'])) ? date('Y-m-d', strtotime($where['date_to'])) : date('Y-m-d');
                    $combi_job_data_sql = '( ( job.job_date >= "'.$date_from.'" AND job.job_date <= "'.$date_to.'" ) OR ( job.due_date >= "'.$date_from.'" AND job.due_date <= "'.$date_to.'" AND job.job_date IS NULL ) )';
                    $this->db->where($combi_job_data_sql);
                    unset($where['date_from'], $where['date_to']);
                }
                unset($where['date_from'], $where['date_to']);
            }

            if (isset($where['status_id'])) {
                if (!empty($where['status_id'])) {
                    $status_id = $where['status_id'];
                    $status_id = convert_to_array($where['status_id']);
                    $status_id = is_array($status_id) ? $status_id : [ $status_id ];
                    $this->db->where_in('job.status_id', $status_id);
                }
                unset($where['status_id']);
            }

            if (isset($where['job_tracking_id'])) {
                if (!empty($where['job_tracking_id'])) {
                    $job_tracking_id = $where['job_tracking_id'];
                    $job_tracking_id = convert_to_array($where['job_tracking_id']);

                    if (is_array($job_tracking_id)) {
                        if (in_array('_blanks', $job_tracking_id)) {
                            $to_remove 		 = ['_blanks'];
                            $job_tracking_id = array_diff($job_tracking_id, $to_remove);

                            $open_sql = '( ( job.job_tracking_id IS NULL OR job.job_tracking_id = "" ) ';

                            if (!empty($job_tracking_id)) {
                                $open_sql .= ' OR ( job.job_tracking_id IN ('. (implode(",", $job_tracking_id)) .') ) ';
                            }

                            $open_sql .= ' )';
                            $this->db->where($open_sql);
                        } else {
                            if (!empty($job_tracking_id)) {
                                $this->db->where_in('job.job_tracking_id', $job_tracking_id);
                            }
                        }
                    } else {
                        $job_tracking_id = [ $job_tracking_id ];
                        $this->db->where_in('job.job_tracking_id', $job_tracking_id);
                    }
                }
                unset($where['job_tracking_id']);
            }

            if (isset($where['contract_id'])) {
                if (!empty($where['contract_id'])) {
                    $contract_id = $where['contract_id'];
                    $contract_id = convert_to_array($where['contract_id']);
                    $contract_id = is_array($contract_id) ? $contract_id : [ $contract_id ];
                    $this->db->where_in('job_types.contract_id', $contract_id);
                }
                unset($where['contract_id']);
            }

            if (!empty($where['region_id']) || !empty($region_id)) {
                $region_id = !empty($region_id) ? $region_id : convert_to_array($where['region_id']);
                $region_id = is_array($region_id) ? $region_id : [ $region_id ];

                if (!empty($region_id) && !empty($site_ids)) {
                    $region_sql = '( ( job.region_id IN ('.implode(",", $region_id).') ) OR job.site_id IN ('.implode(",", $site_ids).') )';
                    $this->db->where($region_sql);
                } else {
                    $this->db->where_in('job.region_id', $region_id);
                }
                unset($where['region_id']);
            }

            if (isset($where['job_type_id'])) {
                if (!empty($where['job_type_id'])) {
                    $job_types = (!is_array($where['job_type_id']) && ((int) $where['job_type_id'] > 0)) ? [ $where['job_type_id'] ] : ((is_array($where['job_type_id'])) ? $where['job_type_id'] : (is_object($where['job_type_id']) ? object_to_array($where['job_type_id']) : []));
                    $this->db->where_in('job.job_type_id', $job_types);
                }
                unset($where['job_type_id']);
            }

            if (isset($where['job_date_start']) || isset($where['job_date_end'])) {
                if (!empty($where['job_date_start'])) {
                    $this->db->where('job.job_date >=', format_date_db($where['job_date_start']));
                }
                unset($where['job_date_start']);

                if (!empty($where['job_date_end'])) {
                    $this->db->where('job.job_date <=', format_date_db($where['job_date_end']));
                    unset($where['job_date_end']);
                }
                unset($where['job_date_end']);
            }

            if (isset($where['created_on_start']) || isset($where['created_on_end'])) {
                if (!empty($where['created_on_start'])) {
                    $this->db->where('job.created_on >=', format_date_db($where['created_on_start']).' 00:00:00');
                }
                unset($where['created_on_start']);

                if (!empty($where['created_on_end'])) {
                    $this->db->where('job.created_on <=', format_date_db($where['created_on_end']).' 23:59:59');
                }
                unset($where['created_on_end']);
            }

            if (isset($where['job_date'])) {
                if (!empty($where['job_date'])) {
                    $sjob_date = date('Y-m-d', strtotime($where['job_date']));
                    $this->db->where('job.job_date', $sjob_date);
                    unset($where['job_date']);
                }
            } else {
                if (isset($where['date_from']) || isset($where['date_to'])) {
                    if (!empty($where['date_from'])) {
                        $this->db->where('job.job_date >=', date('Y-m-d', strtotime(format_date_db($where['date_from']))));
                    }

                    if (!empty($where['date_to'])) {
                        $this->db->where('job.job_date <=', date('Y-m-d', strtotime(format_date_db($where['date_to']))));
                    }
                    unset($where['date_from'], $where['date_to']);
                }
            }

            ## Limit Jobs based on Associated User's Jobs
            if (!empty($assignees)) {
                if (!empty($where['assigned_to'])) {
                    $assignees[] 		= $where['assigned_to'];
                }
                $this->db->where_in('job.assigned_to', $assignees);
            } else {
                if (isset($where['assigned_to'])) {
                    if (!empty($where['assigned_to'])) {
                        $assigned_to = $where['assigned_to'];
                        $assigned_to = convert_to_array($where['assigned_to']);
                        $assigned_to = is_array($assigned_to) ? $assigned_to : [ $assigned_to ];
                        $this->db->where_in('job.assigned_to', $assigned_to);
                    }
                    unset($where['assigned_to']);
                }
            }

            if (isset($where['discipline_id'])) {
                if (!empty($where['discipline_id'])) {
                    $disciplines = (!is_array($where['discipline_id']) && ((int) $where['discipline_id'] > 0)) ? [ $where['discipline_id'] ] : ((is_array($where['discipline_id'])) ? $where['discipline_id'] : (is_object($where['discipline_id']) ? object_to_array($where['discipline_id']) : []));
                    $this->db->where_in('job_types.discipline_id', $disciplines);
                }
                unset($where['discipline_id']);
            }

            if (isset($where['overdue_jobs'])) {
                if (!empty($where['overdue_jobs'])) {
                    $this->db->where('job.job_date <', $today);
                    $this->db->where_not_in('job_statuses.status_group', [ 'unassigned', 'successful', 'failed', 'cancelled' ]);
                }
                unset($where['overdue_jobs']);
            }

            if (isset($where['exclude_successful_jobs'])) {
                if (!empty($where['exclude_successful_jobs'])) {
                    $this->db->where_not_in('job.status_id', [ 4 ]); //Remove Successful Jobs
                }
                unset($where['exclude_successful_jobs']);
            }

            if (isset($where['open_jobs'])) {
                #if( !empty( $where['open_jobs'] ) ){
                $this->db->where('( job.job_date = "1970-01-01" OR job.job_date = "0000-00-00" OR job.job_date IS NULL )');
                $this->db->where('( job.due_date != "1970-01-01" AND job.due_date != "0000-00-00" AND job.due_date IS NOT NULL )');
                $this->db->where('( job.assigned_to > 0 )');
                #}
                unset($where['open_jobs']);
            }

            if (isset($where['is_reactive'])) {
                $this->db->where('job_types.is_reactive', $where['is_reactive']);
                unset($where['is_reactive']);
            }

            if (isset($where['is_scheduled'])) {
                $this->db->where('job.schedule_id >', 0);
                unset($where['is_scheduled']);
            }

            if (!empty($where['supervised_staff']) && is_array($where['supervised_staff'])) {
                $this->db->where_in('job.assigned_to', $where['supervised_staff']);
                $this->db->where('job.status_id', 10);
                unset($where['supervised_staff']);
            }

            if (isset($where['job_status'])) {
                if (!empty($where['job_status'])) {
                    $job_status = $where['job_status'];
                    $job_status = convert_to_array($where['job_status']);
                    $job_status = is_array($job_status) ? $job_status : [ $job_status ];
                    $this->db->where_in('job_statuses.job_status', $job_status);
                }
                unset($where['job_status']);
            }

            if (!empty($where)) {
                #$this->db->where( $where );
            }

            $query = $this->db->group_by('job.job_id')
                ->get('job');

            $results['total'] = !empty($query->num_rows()) ? $query->num_rows() : 0;
            $limit 			  = (!empty($limit > 0)) ? $limit : $results['total'];
            $results['pages'] = !empty($query->num_rows()) ? ceil($query->num_rows() / $limit) : 0;

            return json_decode(json_encode($results));
        }

        return $result;
    }


    /*
    * Do an Exact Match Search through list of Jobs
    */
    public function advanced_job_search($account_id = false, $postdata = false, $where = false, $order_by = false)
    {
        $result = false;
        if (!empty($account_id)) {
            $assignees 	= $this->_check_jobs_access($this->ion_auth->_current_user());

            #Limit access by contract to External User Types
            if (in_array($this->ion_auth->_current_user()->user_type_id, EXTERNAL_USER_TYPES)) {
                if (!empty($this->ion_auth->_current_user()->is_primary_user)) {
                    ## Get associated users
                    if (!$job_id) {
                        $group_assignees = $this->ion_auth->get_associated_users($account_id, $this->ion_auth->_current_user()->id, false, ['as_arraay'=>1]);
                        if (!empty($group_assignees)) {
                            $group_assignees = (!empty($group_assignees)) ? array_column($group_assignees, 'user_id') : [$this->ion_auth->_current_user()->id];
                            $group_assignees = (!in_array($this->ion_auth->_current_user()->id, $group_assignees)) ? array_merge($group_assignees, [$this->ion_auth->_current_user()->id]) : $group_assignees;
                            $raw_where['group_assignees']	= $group_assignees;
                            $this->db->where_in('job.assigned_to', $group_assignees);
                        } else {
                            $contract_access = $this->contract_service->get_linked_people($account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1]);
                            $allowed_access  = !empty($contract_access) ? array_column($contract_access, 'contract_id') : [];
                            if (!empty($allowed_access)) {
                                $this->db->where_in('job_types.contract_id', $allowed_access);
                            } else {
                                $this->session->set_flashdata('message', 'No data found matching your criteria');
                                return false;
                            }
                        }
                    }
                } else {
                    $contract_access = $this->contract_service->get_linked_people($account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1]);
                    $allowed_access  = !empty($contract_access) ? array_column($contract_access, 'contract_id') : [];
                    if (!empty($allowed_access)) {
                        $this->db->where_in('job_types.contract_id', $allowed_access);
                    } else {
                        $this->session->set_flashdata('message', 'No data found matching your criteria');
                        return false;
                    }
                }
            }

            $search_params 	= (!empty($postdata['search_params'])) ? convert_to_array($postdata['search_params']) : false;

            if (!empty($search_params)) {
                $fields_check = [];
                $this->db->select('job.*, job_types.contract_id as contract_id, job_types.job_type, job_types.discipline_id, account_discipline.account_discipline_name `discipline_name`, job_statuses.job_status, job_statuses.status_group, job_tracking_statuses.job_tracking_status, job_tracking_statuses.job_tracking_group, CONCAT(user.first_name," ",user.last_name) `assignee`, diary_regions.region_name, contract.contract_name, site.site_name', false);
                $this->db->select('addresses.postcode `postcode`', false)
                    ->join('addresses', 'addresses.main_address_id = job.address_id', 'left')
                    ->join('customer', 'customer.customer_id = job.customer_id', 'left')
                    ->join('job_types', 'job_types.job_type_id = job.job_type_id', 'left')
                    ->join('contract', 'job_types.contract_id = contract.contract_id', 'left')
                    ->join('diary_regions', 'diary_regions.region_id = job.region_id', 'left')
                    ->join('job_statuses', 'job_statuses.status_id = job.status_id', 'left')
                    ->join('job_tracking_statuses', 'job_tracking_statuses.job_tracking_id = job.job_tracking_id', 'left')
                    ->join('user', 'user.id = job.assigned_to', 'left')
                    ->join('site', 'site.site_id = job.site_id', 'left')
                    ->join('account_discipline', 'account_discipline.discipline_id = job_types.discipline_id', 'left')
                    ->where('job.account_id', $account_id)
                    ->where('job.archived !=', 1);

                foreach ($search_params as $col => $value) {
                    $search_term  	= urldecode(trim($value));
                    $column 		= trim(str_replace('-', '.', $col));
                    if (!empty($search_term)) {
                        if ($column == 'addresses.postcode') {
                            $combined_where = '( addresses.postcode = "'.$search_term.'" OR  addresses.postcode_nospaces = "'.$search_term.'" )';
                            $this->db->where($combined_where);
                        } else {
                            $this->db->where($column, $search_term);
                        }

                        $fields_check[] = $search_term;
                    }
                }

                if (!empty($fields_check)) {
                    $query = $this->db->group_by('job.job_id')
                        ->get('job');

                    if ($query->num_rows() > 0) {
                        $result = $query->result();
                        $this->session->set_flashdata('message', 'Records found.');
                    } else {
                        $this->session->set_flashdata('message', 'No records found matching your criteria.');
                    }
                } else {
                    $this->session->set_flashdata('message', 'Missing search parameters');
                }
            } else {
                $this->session->set_flashdata('message', 'Missing search parameters');
            }
        }

        return $result;
    }


    /** Get Un-Assigned Jobs **/
    /** Get Un-Assigned Jobs **/
    public function get_un_assigned_jobs($account_id = false, $where = false, $order_by = false, $limit = 1000)
    {
        $result = false;

        $where = !empty($where) ? convert_to_array($where) : false;

        if (!empty($account_id) && !empty($where['job_date'])) {
            $associated_user_id = $user_ids = false;

            if (isset($where['region_id'])) {
                if (!empty($where['region_id'])) {
                    $region_id = convert_to_array($where['region_id']);
                    $region_id = is_array($region_id) ? $region_id : [ $region_id ];
                }

                unset($where['region_id']);
                $region_sites = $this->db->select('site.site_id, site.region_id', false)->where_in('site.region_id', $region_id)->get_where('site', ['account_id' => $account_id ])->result_array();
                if (!empty($region_sites)) {
                    $site_ids = array_column($region_sites, 'site_id');
                }
            }

            if (!empty($where['associated_user_id'])) {
                $associated_user_id = $where['associated_user_id'];
                unset($where['associated_user_id']);
                $helper_query = $this->db->get_where("associated_users", ["account_id" => $account_id, "primary_user_id" => $associated_user_id])->result_array();
                if (!empty($helper_query)) {
                    $user_ids = array_column($helper_query, 'user_id');
                    if (!empty($user_ids)) {
                        $user_ids[] = $associated_user_id;
                    }
                }
            }

            if (isset($where['contract_id'])) {
                if (!empty($where['contract_id'])) {
                    $contract_id = $where['contract_id'];
                    $job_types = $this->db->select('job_types.job_type_id')
                        ->group_by('job_types.job_type_id')
                        ->get_where('job_types', [ 'job_types.contract_id'=>$contract_id ]);

                    if ($job_types->num_rows() > 0) {
                        $contract_job_types = array_column($job_types->result_array(), 'job_type_id');
                        $this->db->where_in('job.job_type_id', $contract_job_types);
                    }
                }
                unset($where['contract_id']);
            }

            $this->db->select('job.job_id, job.site_id, job.asset_id, job.customer_id, job.address_id, job.account_id, job.due_date, job.job_date, job.job_duration, job.job_type_id, job_types.job_type, job.client_reference, job.status_id, job.fail_code_id, job.fail_notes, job.job_order, job.region_id, diary_regions.region_name, job_statuses.job_status, job_statuses.status_group, fc.fail_code, fc.fail_code_text, fc.fail_code_desc, fc.fail_code_group, addrs.main_address_id,addrs.addressline1 `address_line_1`, addrs.addressline2 `address_line_2`,addrs.addressline3 `address_line_3`,addrs.posttown `address_city`,addrs.county `address_county`, addrs.postcode `job_postcode`, postcode_area, postcode_district, postcode_sector, addrs.summaryline `summaryline`, ( CASE WHEN ( site.site_id != "" ) THEN site.site_id ELSE site_asset.site_id END ) `site_id`, ( CASE WHEN ( site.site_name != "" ) THEN site.site_name ELSE site_asset.site_name END ) `site_name`, ( CASE WHEN ( site.site_postcodes != "" ) THEN site.site_postcodes ELSE site_asset.site_postcodes END ) `site_postcodes`, site.site_reference', false)
                ->join('addresses addrs', 'addrs.main_address_id = job.address_id', 'left')
                ->join('job_types', 'job_types.job_type_id = job.job_type_id', 'left')
                ->join('job_statuses', 'job_statuses.status_id = job.status_id', 'left')
                ->join('job_fail_codes fc', 'fc.fail_code_id = job.fail_code_id', 'left')
                ->join('diary_regions', 'diary_regions.region_id = job.region_id', 'left')
                ->join('asset', 'asset.asset_id = job.asset_id', 'left')
                ->join('site site_asset', 'site_asset.site_id = asset.site_id', 'left')
                ->join('site', 'site.site_id = job.site_id', 'left')
                ->where('job.archived !=', 1)
                ->where('job.account_id', $account_id)
                #->where( '( job.assigned_to IS NULL OR job.assigned_to = "" )' );
                ->where('( ( job.assigned_to IS NULL OR job.assigned_to = "" ) AND ( job.second_assignee_id IS NULL OR job.second_assignee_id = "" ) )');

            if (isset($where['grouped'])) {
                if (!empty($where['grouped'])) {
                    $grouped = true;
                }
                unset($where['grouped']);
            }

            if (isset($where['job_type_id'])) {
                if (!empty($where['job_type_id'])) {
                    $job_type_id = convert_to_array($where['job_type_id']);
                    $job_type_id = is_array($job_type_id) ? $job_type_id : [ $job_type_id ];
                    $this->db->where_in('job.job_type_id', $job_type_id);
                }
                unset($where['job_type_id']);
            }

            if (!empty($where['region_id']) || !empty($region_id)) {
                $region_id = !empty($region_id) ? $region_id : convert_to_array($where['region_id']);
                $region_id = is_array($region_id) ? $region_id : [ $region_id ];

                if (!empty($region_id) && !empty($site_ids)) {
                    $region_sql = '( ( job.region_id IN ('.implode(",", $region_id).') ) OR job.site_id IN ('.implode(",", $site_ids).') )';
                    $this->db->where($region_sql);

                #$this->db->where( '( job.region_id IN ( '.implode( ', ', $region_id ).' ) OR job.site_id IN ( '.implode( ', ', $site_ids ).' ) )' );
                } else {
                    $this->db->where_in('job.region_id', $region_id);
                }
                unset($where['region_id']);
            }

            if (!empty($where['job_date'])) {
                $job_date 	= date('Y-m-d', strtotime($where['job_date']));
                unset($where['job_date']);
            } else {
                $job_date = date('Y-m-d');
            }

            if (isset($where['include_blank_dates'])) {
                if (!empty($where['include_blank_dates'])) {
                    $job_date_sql = '( ( job.job_date = "'.$job_date.'" ) OR ( job.job_date IS NULL ) )';
                } else {
                    $job_date_sql = '( job.job_date = "'.$job_date.'" )';
                }
                unset($where['include_blank_dates']);
            } else {
                $job_date_sql = '( job.job_date = "'.$job_date.'" )';
            }

            $this->db->where($job_date_sql);

            if (isset($where['due_date'])) {
                if (!empty($where['due_date'])) {
                    $due_date 	= date('Y-m-d', strtotime($where['due_date']));
                    $this->db->where('job.due_date >=', $due_date);
                    $this->db->where('job.due_date <=', $due_date);
                }
                unset($where['due_date']);
            }

            if (!empty($user_ids)) {
                $this->db->where_in('user.id', $user_ids);
            }

            $this->db->where_not_in('job.status_id', [5,6]); //Exclude failed and cancelled Jobs

            $this->db->limit($limit);

            $job = $this->db->order_by('job_id desc, job_type')
                ->get('job');

            if ($job->num_rows() > 0) {
                $job_types_data = $global_counters = $job_types =  $regions = [];

                foreach ($job->result() as $key => $row) {
                    if (!empty($grouped)) {
                        $job_types_data[$row->job_type_id]['job_type_id']	= $row->job_type_id;
                        $job_types_data[$row->job_type_id]['job_type'] 	 	= $row->job_type;
                        $job_types_data[$row->job_type_id]['total_jobs']	= !empty($job_types_data[$row->job_type_id]['total_jobs']) ? ($job_types_data[$row->job_type_id]['total_jobs'] + 1) : 1;
                        $job_types_data[$row->job_type_id]['jobs_list'][]	= $row;
                    } else {
                        $job_types_data[] = $row;
                    }

                    $global_counters['Total']  		= !empty($global_counters['Total']) ? ($global_counters['Total'] + 1) : 1;
                    $job_types[$row->job_type_id]	= [ 'job_type_id' 	=> $row->job_type_id, 'job_type' => $row->job_type ];
                    $regions[$row->region_id]		= [ 'region_id' 	=> $row->region_id,	'region_name' 	=> $row->region_name ];
                }

                $result = ( object )[
                    'records' 	=> $job_types_data,
                    'counters'	=> $global_counters,
                    'job_types'	=> $job_types,
                    'regions'	=> $regions
                ];

                $this->session->set_flashdata('message', 'Job records found');
            } else {
                $this->session->set_flashdata('message', 'Job record(s) not found');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }


    /** Generate Site Schedule Tracking **/
    public function generate_site_schedule_tracking($account_id = false, $site_schedule_tracking_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($site_schedule_tracking_data)) {
            foreach ($site_schedule_tracking_data as $k => $tracking_data) {
                $tracking_data['date_created'] 	= date('Y-m-d');

                $condition 	= [
                    'account_id' 	=> $account_id,
                    #'date_created'	=>	date( 'Y-m-d' ),
                    'contract_id'	=>	!empty($tracking_data['contract_id']) ? $tracking_data['contract_id'] : false,
                    'site_id'		=>	!empty($tracking_data['site_id']) ? $tracking_data['site_id'] : false,
                    'frequency_id'	=>	!empty($tracking_data['frequency_id']) ? $tracking_data['frequency_id'] : false,
                    'is_active'		=>	1
                ];

                $check_exists = $this->db->get_where('schedule_site_tracker', $condition)->row();

                if (!empty($check_exists)) {
                    $this->db->where($condition)->update('schedule_site_tracker', $tracking_data);
                    $result = true;
                } else {
                    $this->db->insert('schedule_site_tracker', $tracking_data);
                    $result = true;
                }
            }
        }
        return $result;
    }


        /** Add Required Checklists **/
    public function add_required_checklists($account_id = false, $job_type_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($job_type_id) && !empty($postdata)) {
            $postdata 		 	= convert_to_array($postdata);
            $required_checklists= !empty($postdata['required_checklists']) ? $postdata['required_checklists'] : false;
            $required_checklists= (is_json($required_checklists)) ? json_decode($required_checklists) : $required_checklists;
            $total				= [];

            if (!empty($required_checklists)) {
                foreach ($required_checklists as $k => $val) {
                    $data = [
                        'checklist_id'	=> $val,
                        'job_type_id'	=> $job_type_id,
                        'created_by'	=> $this->ion_auth->_current_user->id
                    ];

                    $check_exists = $this->db->limit(1)->get_where('job_required_checklists', [ 'checklist_id'	=> $val, 'job_type_id' => $job_type_id ])->row();
                    if (!$check_exists) {
                        $this->db->insert('job_required_checklists', $data);
                    }
                    $total[] = $data;
                }
            } elseif (!empty($postdata['checklist_id'])) {
                $data = [
                    'checklist_id'	=>$postdata['checklist_id'],
                    'job_type_id'	=>$job_type_id,
                    'created_by'	=> $this->ion_auth->_current_user->id
                ];

                $check_exists = $this->db->limit(1)->get_where('job_required_checklists', $data)->row();
                if (!$check_exists) {
                    $this->db->insert('job_required_checklists', $data);
                }
                $total[] = $data;
            }

            if (!empty($total)) {
                $result = $total;
                $this->session->set_flashdata('message', 'Required Checklists added successfully');
            } else {
                $this->session->set_flashdata('message', 'No Required Checklists found');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }
        return $result;
    }

    /** Add Required Checklists **/
    public function remove_required_checklists($account_id = false, $job_type_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($job_type_id) && !empty($postdata)) {
            $postdata 		 = convert_to_array($postdata);
            $required_checklists= !empty($postdata['required_checklists']) ? $postdata['required_checklists'] : false;
            $required_checklists= (is_json($required_checklists)) ? json_decode($required_checklists) : $required_checklists;
            $deleted		= [];

            if (!empty($required_checklists)) {
                foreach ($required_checklists as $k => $val) {
                    $data = [
                        'checklist_id'=>$val,
                        'job_type_id'=>$job_type_id
                    ];

                    $check_exists = $this->db->limit(1)->get_where('job_required_checklists', $data)->row();
                    if (!empty($check_exists)) {
                        $this->db->where($data);
                        $this->db->delete('job_required_checklists');
                        $this->ssid_common->_reset_auto_increment('job_required_checklists', 'assoc_id');
                    }
                    $deleted[] = $data;
                }
            } elseif (!empty($postdata['checklist_id'])) {
                $data = [
                    'checklist_id'=>$postdata['checklist_id'],
                    'job_type_id'=>$job_type_id
                ];

                $check_exists = $this->db->limit(1)->get_where('job_required_checklists', $data)->row();
                if (!empty($check_exists)) {
                    $this->db->where($data);
                    $this->db->delete('job_required_checklists');
                    $deleted[] = $data;
                    $this->ssid_common->_reset_auto_increment('job_required_checklists', 'assoc_id');
                }
            }

            if (!empty($deleted)) {
                $result = $deleted;
                $this->session->set_flashdata('message', 'Required Checklists removed successfully');
            } else {
                $this->session->set_flashdata('message', 'No Required Checklists were removed');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }
        return $result;
    }

    /** Get a list of associated Risks to a Job **/
    public function get_required_checklists($account_id = false, $job_type_id = false, $where = false)
    {
        $result = false;

        if (!empty($job_type_id)) {
            if (!empty($account_id)) {
                #$this->db->where( 'jrc.account_id', $account_id );
            }

            if (!empty($where['job_id'])) {
                // if Job id is given, get any attached dynamic checklists
                $dynamic_checklists = $this->get_dynamic_checklists($account_id, $where['job_id'], ['result_as_array'=>1]);
            }

            ## Local records are non-tesseract checklists
            $local_records_only = !empty($where['local_records_only']) ? $where['local_records_only'] : false;

            $this->db->select('jrc.job_id, jrc.job_type_id, chkl.*')
                ->join('tesseract_checklist chkl', 'chkl.checklist_id = jrc.checklist_id', 'left');

            if (!empty($local_records_only)) {
                $this->db->where('chkl.remote_checklist !=', 1);
            } else {
                $this->db->select('jt_ref.criteria_source ,jt_ref.criteria_id `checklist_order_id`, jt_ref.responseset_link_type, jt_ref.task_type')
                    ->join('tesseract_job_type_checklist_ref jt_ref', 'chkl.checklist_id = jt_ref.checklist_id', 'left')
                    ->where('jt_ref.is_active', 1)
                    ->where('chkl.remote_checklist', 1)
                    ->order_by('jt_ref.criteria_id');
            }

            $query = $this->db->where('jrc.job_type_id', $job_type_id)
                ->group_by('jrc.checklist_id')
                ->get('job_required_checklists jrc');

            if ($query->num_rows() > 0) {
                if (!empty($dynamic_checklists)) {
                    $result = array_merge($query->result_array(), $dynamic_checklists);
                } else {
                    if (!empty($where['result_as_array'])) {
                        $result = $query->result_array();
                    } else {
                        $result = $query->result();
                    }
                }
                $this->session->set_flashdata('message', 'Required Checklists found');
            } else {
                $this->session->set_flashdata('message', 'No Required Checklists found');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }

        return $result;
    }


    /** Get a list of all dynamic Checklists to a Job **/
    public function get_dynamic_checklists($account_id = false, $job_id = false, $where = false)
    {
        $result = false;

        if (!empty($job_id)) {
            if (!empty($account_id)) {
                #$this->db->where( 'jdc.account_id', $account_id );
            }

            $query = $this->db->select('jdc.job_id, jdc.job_type_id, chkl.*')
                ->join('tesseract_checklist chkl', 'chkl.checklist_id = jdc.checklist_id')
                ->where('jdc.job_id', $job_id)
                ->get('job_dynamic_checklists jdc');

            if ($query->num_rows() > 0) {
                if (!empty($where['result_as_array'])) {
                    $result = $query->result_array();
                } else {
                    $result = $query->result();
                }
                $this->session->set_flashdata('message', 'Dynamic checklists found');
            } else {
                $this->session->set_flashdata('message', 'No dynamic checklists found');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }

        return $result;
    }


    /** Trigger all Tesseract related Actions **/
    private function _trigger_tesseract_actions($account_id = false, $job_id = false, $data = false)
    {
        $result = null;

        if (!empty($account_id) && !empty($job_id) && !empty($data)) {
            $tess_job 	= $this->db->select('tesseract_jobs.*, job.dispatch_time, job.on_site_time, job.start_time, job.finish_time, job.completed_works, symptom_code, fault_code, repair_code,  job.engineer_signature,  job.customer_signature, job.customer_name')
                ->where('tesseract_jobs.evident_job_id', $job_id)
                ->where('tesseract_jobs.account_id', $account_id)
                ->join('job', 'job.external_job_ref = tesseract_jobs.call_num', 'left')
                ->get('tesseract_jobs')
                ->row();

            if (!empty($tess_job)) {
                $this->load->model('serviceapp/Tesseract_model', 'tesseract_service');

                $status_group = $data['status_group'] ? $data['status_group'] : '';

                $call_update_data= [];
                $tess_call_num 	 = !empty($tess_job->call_num) ? ($tess_job->call_num) : false;
                $tess_employ_num = !empty($tess_job->call_employ_num) ? ($tess_job->call_employ_num) : false;
                $tess_site_status= !empty($tess_job->call_site_num) ? ($tess_job->call_site_num) : false;

                switch($status_group) {
                    case 'inprogress':
                        //
                        break;
                    case 'enroute':

                        $tess_call_status 	= !empty($data['external_job_call_status']) ? strtoupper($data['external_job_call_status']) : 'DISP';
                        $status_data	  	= [ 'job.external_job_call_status'=> $tess_call_status ];
                        $call_update_data['Call_DDate'] = datetime_to_iso8601(_datetime());

                        break;

                    case 'onhold':
                    case 'successful':

                        $tess_call_status 	= !empty($data['external_job_call_status']) ? strtoupper($data['external_job_call_status']) : 'COMP';
                        $status_data	  	= [ 'job.external_job_call_status'=> $tess_call_status ];

                        ## Update FSR
                        // FSR needs a full record in the
                        $call_fsr_records = $this->tesseract_service->get_fsr_by_call_number($account_id, $data['external_job_ref']);

                        if (!empty($call_fsr_records)) {
                            $fsr_nums = array_column($call_fsr_records, 'fsR_Num');
                            $highest_number = !empty($fsr_nums) ? max($fsr_nums) : 1;
                            $new_fsr_number = $highest_number + 1;
                        } else {
                            $new_fsr_number = 1;
                        }

                        $completion_date = !empty($tess_job->finish_time) ? datetime_to_iso8601($tess_job->finish_time) : datetime_to_iso8601(_datetime());
                        if (!empty($tess_job->engineer_signature)) {
                            $engineer_signature_img	 = file_get_contents($tess_job->engineer_signature);
                            $engineer_signature_blob  = base64_encode($engineer_signature_img);
                        }

                        if (!empty($tess_job->customer_signature)) {
                            $customer_signature_img	 = file_get_contents($tess_job->customer_signature);
                            $customer_signature_blob  = base64_encode($customer_signature_img);
                        }

                        $fsr_data 		 = '';
                        $fsr_data		.= !empty($tess_job->completed_works) ? $tess_job->completed_works : 'Notes from works completed...';

                        $dispatch_time 	= !empty($tess_job->dispatch_time) ? datetime_to_iso8601($tess_job->dispatch_time) : _datetime();
                        $on_site_time 	= !empty($tess_job->on_site_time) ? datetime_to_iso8601($tess_job->on_site_time) : _datetime();
                        $start_time 	= !empty($tess_job->start_time) ? datetime_to_iso8601($tess_job->start_time) : _datetime();
                        $complete_time 	= !empty($completion_date) ? datetime_to_iso8601($completion_date) : _datetime();
                        $travel_time 	= number_format(((strtotime($on_site_time) - strtotime($dispatch_time)) / 3600), 2); //Hrs
                        $work_time 		= number_format(((strtotime($complete_time) - strtotime($on_site_time)) / 3600), 2); //Hrs

                        ## Create Customer Signature Blob linked to the Engineer's
                        if (!empty($customer_signature_blob)) {
                            $customer_blob_data = [
                                'job_id' 			=> $job_id,
                                'account_id' 		=> $account_id,
                                'blob_Name' 		=> !empty($tess_job->customer_name) ? ucwords(strtolower($tess_job->customer_name)) : 'Customer Signature',
                                'blob_Data' 		=> $customer_signature_blob,
                            ];
                            $customer_blob 			= $this->tesseract_service->create_blob($account_id, $customer_blob_data);
                            $blob_map_mumber		= !empty($customer_blob->data->blob) ? $customer_blob->data->blob : null;
                        }

                        ## Create Engineer Signature Blob
                        if (!empty($engineer_signature_blob)) {
                            $engineer_blob_data = [
                                'job_id' 			=> $job_id,
                                'account_id' 		=> $account_id,
                                'blobMap_Num' 		=> !empty($blob_map_mumber) ? intval($blob_map_mumber) : null,
                                'blob_Name' 		=> !empty($tess_job->call_user) ? $tess_job->call_user : 'Engineer Signature',
                                'blob_Data' 		=> $engineer_signature_blob,
                            ];
                            $engineer_blob 			= $this->tesseract_service->create_blob($account_id, $engineer_blob_data);
                        }

                        ## Submit any file Attachments
                        $this->load->model('Document_Handler_model', 'document_service');
                        $uploaded_files = $this->document_service->get_document_list($account_id, $document_group = 'job', [ 'job_id' => $job_id ], [ 'upload_segment'=> 'Attachment', 'un_grouped' => 1 ]);
                        $uploaded_files = !empty($uploaded_files['Attachments']) ? $uploaded_files['Attachments'] : false;
                        if (!empty($uploaded_files)) {
                            $fsr_attachment_files = [];
                            $send_attachments = $this->tesseract_service->send_attachment($account_id, [ 'documents'=>$uploaded_files ]);
                            if (!empty($send_attachments->success) && !empty($send_attachments->data)) {
                                foreach ($send_attachments->data as $key => $file_object) {
                                    $file_object = (object) $file_object;
                                    $fsr_attachment_files[] = $file_object->file_name;
                                }
                                $fsr_attachment_files = implode(';', $fsr_attachment_files);
                            }
                        }

                        $new_fsr_data = [
                            'job_id' 			=> $job_id,
                            'account_id' 		=> $account_id,
                            'fsR_Num' 			=> intval($new_fsr_number),
                            'fsR_Call_Num'  	=> $tess_call_num,
                            #'fsR_User'   		=> !empty( $tess_job->call_user ) 	? ( $tess_job->call_user ) : null,
                            'fsR_User'   		=> $this->ion_auth->_current_user()->external_username,
                            #'fsR_Employ_Num'	=> $tess_employ_num,
                            'fsR_Employ_Num'	=> $this->ion_auth->_current_user()->external_user_ref,
                            'fsR_Ser_Num'   	=> !empty($tess_job->call_ser_num) ? ($tess_job->call_ser_num) : null,
                            'fsR_Prod_Num'  	=> !empty($tess_job->call_prod_num) ? ($tess_job->call_prod_num) : false,
                            'fsR_Rep_Code'  	=> !empty($tess_job->repair_code) ? intval($tess_job->repair_code) : intval(51),
                            'fsR_Fault_Code'  	=> !empty($tess_job->fault_code) ? ($tess_job->fault_code) : 'MIS',
                            'fsR_Disp_Date'		=> !empty($tess_job->dispatch_time) ? datetime_to_iso8601($tess_job->dispatch_time) : datetime_to_iso8601(_datetime()),
                            'fsR_Start_Date'	=> !empty($tess_job->on_site_time) ? datetime_to_iso8601($tess_job->on_site_time) : (!empty($tess_job->start_time) ? datetime_to_iso8601($tess_job->start_time) : _datetime()),
                            'fsR_Call_Status' 	=> $tess_call_status,
                            'fsR_CallSubStatus_Code'=> $tess_call_status,
                            'fsR_Site_Num' 		=> $tess_site_status,
                            'fsR_Solution' 		=> $tess_job->completed_works,
                            'fsR_Complete_Date'	=> !empty($completion_date) ? $completion_date : datetime_to_iso8601(_datetime()),
                            //'fsR_Miles'			=> 1,
                            //'fsR_Signature_Data'=> !empty( $engineer_signature_blob ) ? $engineer_signature_blob 	: null,
                            'fsR_BlobMap_Num'	=> !empty($blob_map_mumber) ? intval($blob_map_mumber) : null,
                            'fsR_Area_Code'  	=> !empty($tess_job->call_area_code) ? ($tess_job->call_area_code) : '',
                            'fsR_Cost_Centre'  	=> !empty($tess_job->call_cont_num) ? ($tess_job->call_cont_num) : '',
                            'fsR_Symp_Code'  	=> !empty($tess_job->symptom_code) ? ($tess_job->symptom_code) : '',
                            'fsR_Travel_Time'  	=> $travel_time,
                            'fsR_Work_Time'  	=> $work_time,
                            'fsR_Added_Via'		=> intval(-2),
                            'fsR_Last_Update'	=> datetime_to_iso8601(_datetime()),
                            'call_DDate'		=> !empty($tess_job->dispatch_time) ? datetime_to_iso8601($tess_job->dispatch_time) : datetime_to_iso8601(_datetime()),
                            #'call_CDate'		=> !empty( $completion_date ) ?  $completion_date :  datetime_to_iso8601( date( 'Y-m-d H:i:s' ) ),
                            'fsR_Attachment'	=> !empty($fsr_attachment_files) ? $fsr_attachment_files : null

                        ];

                        if ($tess_call_status == 'COMP') {
                            $new_fsr_data['call_CDate']		= !empty($completion_date) ? $completion_date : datetime_to_iso8601(_datetime());
                            $call_update_data['Call_CDate'] = !empty($completion_date) ? $completion_date : datetime_to_iso8601(_datetime());
                        }

                        #$update_tess_fsr = $this->tesseract_service->update_fsr( $account_id, $new_fsr_data );
                        $create_tess_fsr = $this->tesseract_service->create_fsr($account_id, $new_fsr_data);

                        if (!empty($create_tess_fsr->success) || !empty($create_tess_fsr->data)) {
                            $call_update_data['Call_DDate'] 		= !empty($tess_job->dispatch_time) ? datetime_to_iso8601($tess_job->dispatch_time) : datetime_to_iso8601(_datetime());
                            $call_update_data['Call_Last_FSR_Num']  = intval($new_fsr_number);
                            $call_update_data['Call_FSR_Count'] 	= !empty($tess_job->call_fsr_count) ? intval(($tess_job->call_fsr_count + 1)) : intval(1);
                        }

                        break;

                    case 'onsite':
                    case 'on site':

                        $on_site_time 	= !empty($tess_job->on_site_time) ? datetime_to_iso8601($tess_job->on_site_time) : null;

                        $call_update_data['Call_Last_ADate'] = $on_site_time;
                        $call_update_data['call_Ref3'] 		 = $job_id;

                        if (empty($tess_job->call_adate)) {
                            $call_update_data['Call_ADate'] = $on_site_time;
                        }

                        $tess_call_status 	= 'DISP';
                        $status_data	  	= [ 'job.external_job_call_status'=> $tess_call_status, 'on_site_time' => _datetime() ];
                        break;

                    default:
                        ##
                        break;
                }

                ## Update Local Job
                $this->db->where('job.account_id', $account_id)
                    ->where('job.job_id', $job_id)
                    ->update('job', $status_data);

                ## Update Tesseract Call Status
                $call_update_data['job_id'] 					= $job_id;
                $call_update_data['account_id'] 				= $account_id;
                $call_update_data['call_Num'] 					= $tess_call_num;
                $call_update_data['call_Status'] 				= strtoupper($tess_call_status);
                $call_update_data['Call_CallSubStatus_Code'] 	= strtoupper($tess_call_status);
                $call_update_data['call_CalT_Code'] 			= !empty($tess_job->call_calt_code) ? ($tess_job->call_calt_code) : null;
                $call_update_data['call_Employ_Num'] 			= $tess_employ_num;
                $call_update_data['call_Ref3'] 					= $job_id;

                $update_tess_job = $this->tesseract_service->update_job($account_id, $call_update_data);

                $result = true;
            }
        }
        return $result;
    }


    ## Get Completed Checklists against a Job
    public function get_completed_checklists($account_id = false, $job_id = false, $site_id = false, $where = false, $order_by = false)
    {
        $result = false;
        if (!empty($account_id)) {
            $where = convert_to_array($where);

            if (!empty($site_id)) {
                $site_jobs = $this->db->select('job_id', false)
                    ->where('( job.external_job_ref != "" OR job.external_job_ref IS NOT NULL )')
                    ->where('job.site_id', $site_id)
                    ->where('job.account_id', $account_id)
                    ->get('job');

                if ($site_jobs->num_rows() > 0) {
                    $job_ids = array_column($site_jobs->result_array(), 'job_id');
                }
            } else {
                if (!empty($job_id)) {
                    $job_ids = [$job_id];
                }
            }

            if (!empty($where['un_grouped'])) {
                $un_grouped = true;
            }

            if (!empty($job_ids)) {
                $query = $this->db->select('resps.response_checklist_id, checks.evi_checklist_id, checks.checklist_id, checks.checklist_hashcode, checks.checklist_desc, job.job_id, job.job_date, job_types.job_type, job.external_job_call_status, CONCAT(user.first_name," ",user.last_name) `completed_by`', false)
                    ->join('tesseract_checklist checks', 'checks.checklist_id = resps.response_checklist_id', 'left')
                    ->join('job', 'job.job_id = resps.job_id', 'left')
                    ->join('job_types', 'job.job_type_id = job_types.job_type_id', 'left')
                    ->join('user', 'user.id = resps.created_by', 'left')
                    ->where_in('resps.job_id', $job_ids)
                    ->where('resps.account_id', $account_id)

                    ->order_by('resps.response_responseset_id')
                    ->group_by('resps.response_checklist_id')
                    ->get('tesseract_checklist_response `resps`');

                if ($query->num_rows() > 0) {
                    $data = [];
                    if (!empty($site_id)) {
                        foreach ($query->result() as $k => $row) {
                            $data[$row->job_id]['jobs_data'] = [
                                'job_id' 		=> $row->job_id,
                                'job_type' 		=> $row->job_type,
                                'job_date' 		=> $row->job_date,
                            ];

                            $data[$row->job_id]['checklists_data'][$row->checklist_id] = [
                                'job_id' 			=> $row->job_id,
                                'checklist_id' 		=> $row->checklist_id,
                                'checklist_desc' 	=> $row->checklist_desc,
                                'checklist_hashcode'=> $row->checklist_hashcode,
                                'completed_by'		=> $row->completed_by,
                                'responses_data'	=> null
                            ];

                            $respoonses = $this->db->select('resps.*')
                                ->where('resps.response_checklist_id', $row->checklist_id)
                                ->where('resps.account_id', $account_id)
                                ->where('resps.job_id', $row->job_id)
                                ->order_by('resps.response_question_order')
                                ->get('tesseract_checklist_response `resps`');

                            if ($respoonses->num_rows() > 0) {
                                $data[$row->job_id]['checklists_data'][$row->checklist_id]['responses_data'] = $respoonses->result();
                            }
                        }
                    } else {
                        foreach ($query->result() as $k => $row) {
                            if (!empty($un_grouped)) {
                                $data[$k] = [
                                    'job_id' 			=> $row->job_id,
                                    'checklist_id' 		=> $row->checklist_id,
                                    'checklist_desc' 	=> $row->checklist_desc,
                                    'checklist_hashcode'=> $row->checklist_hashcode,
                                    'completed_by'		=> $row->completed_by,
                                    'responses_data'	=> null
                                ];

                                $respoonses = $this->db->select('resps.*')
                                    ->where('resps.response_checklist_id', $row->checklist_id)
                                    ->where('resps.account_id', $account_id)
                                    ->where('resps.job_id', $job_id)
                                    ->order_by('resps.response_question_order')
                                    ->get('tesseract_checklist_response `resps`');

                                if ($respoonses->num_rows() > 0) {
                                    $data[$k]['responses_data'] = $respoonses->result();
                                }
                            } else {
                                $data[$row->checklist_id] = [
                                    'job_id' 			=> $row->job_id,
                                    'checklist_id' 		=> $row->checklist_id,
                                    'checklist_desc' 	=> $row->checklist_desc,
                                    'checklist_hashcode'=> $row->checklist_hashcode,
                                    'completed_by'		=> $row->completed_by,
                                    'responses_data'	=> null
                                ];

                                $respoonses = $this->db->select('resps.*')
                                    ->where('resps.response_checklist_id', $row->checklist_id)
                                    ->where('resps.account_id', $account_id)
                                    ->where('resps.job_id', $job_id)
                                    ->order_by('resps.response_question_order')
                                    ->get('tesseract_checklist_response `resps`');

                                if ($respoonses->num_rows() > 0) {
                                    $data[$row->checklist_id]['responses_data'] = $respoonses->result();
                                }
                            }
                        }
                    }
                    $result = !empty($data) ? $data : false;
                    $this->session->set_flashdata('message', 'Completed checklists data found');
                } else {
                    $this->session->set_flashdata('message', 'No Completed checklists data found');
                }
            }
        }
        return $result;
    }


    /**
    * Search through list of Checklists
    */
    public function checklist_search($account_id = false, $job_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $where 	= $raw_where = convert_to_array($where);

            $this->db->select('job.*, job_types.job_type, job_statuses.job_status, CONCAT(user.first_name," ",user.last_name) `assignee`, site.site_name, site.site_reference', false)
                ->join('job_types', 'job_types.job_type_id = job.job_type_id', 'left')
                ->join('site', 'site.site_id = job.site_id', 'left')
                ->join('tesseract_checklist_response', 'tesseract_checklist_response.job_id = job.job_id')
                ->join('job_statuses', 'job_statuses.status_id = job.status_id', 'left')
                ->join('user', 'user.id = job.assigned_to', 'left')
                ->where('job.archived !=', 1)
                ->where('job.account_id', $account_id)
                ->where('job.external_job_ref > 0');

            $job_id 	= !empty($job_id) ? $job_id : (!empty($where['job_id']) ? $where['job_id'] : false);

            if (!empty($job_id)) {
                $row = $this->db->get_where('job', ['job.job_id' => $job_id ])->row();

                if (!empty($row)) {
                    $row->checklists_data = $this->get_completed_checklists($account_id, $job_id);
                    $this->session->set_flashdata('message', 'Checklist record found');
                    $result = $row;
                } else {
                    $this->session->set_flashdata('message', 'Checklist record not found');
                }
                return $result;
            }

            if (!empty($search_term)) {
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->checklist_searchable_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->checklist_searchable_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($order_by)) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('job.job_id DESC, job.job_id');
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->group_by('job.job_id')
                ->get('job');

            if ($query->num_rows() > 0) {
                $data 						= [];
                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $result->records 			= $query->result();
                $counters 					= $this->get_checklist_search_totals($account_id, $search_term, $raw_where, $limit);
                $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
                $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
                $result->counters->limit  	= ( int ) $limit;
                $result->counters->offset 	= ( int ) $offset;
                $this->session->set_flashdata('message', 'Records found.');
            } else {
                $this->session->set_flashdata('message', 'No records found matching your criteria.');
            }

            return $result;
        }
    }


    /*
    * Get total Checklists count for the search
    */
    public function get_checklist_search_totals($account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        if (!empty($account_id)) {
            $where 			= $raw_where = convert_to_array($where);

            $this->db->select('job.job_id', false)
                ->join('job_types', 'job_types.job_type_id = job.job_type_id', 'left')
                ->join('site', 'site.site_id = job.site_id', 'left')
                ->join('tesseract_checklist_response', 'tesseract_checklist_response.job_id = job.job_id')
                ->join('job_statuses', 'job_statuses.status_id = job.status_id', 'left')
                ->join('user', 'user.id = job.assigned_to', 'left')
                ->where('job.archived !=', 1)
                ->where('job.account_id', $account_id)
                ->where('job.external_job_ref > 0');

            if (!empty($search_term)) {
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->checklist_searchable_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->checklist_searchable_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            $query = $this->db->group_by('job.job_id')
                ->get('job');

            $results['total'] = !empty($query->num_rows()) ? $query->num_rows() : 0;
            $limit 			  = (!empty($limit > 0)) ? $limit : $results['total'];
            $results['pages'] = !empty($query->num_rows()) ? ceil($query->num_rows() / $limit) : 0;

            return json_decode(json_encode($results));
        }
    }


    public function get_dates_from_date_range($account_id = false, $date_range = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($date_range)) {
            $date_to 	= date('Y-m-d', strtotime(_datetime()));
            switch(strtolower($date_range)) {
                # Last 7 Days to date inclusive
                default:
                case '7':
                case '7days':
                case '7 days':
                case '1week':
                case '1 week':
                    $date_from 	= date('Y-m-d', strtotime(_datetime().' - 7 days '));
                    ;
                    break;

                    # Last 30 Days to date inclusive
                case '30':
                case '30days':
                case '30 days':
                case '1month':
                case '1 month':
                    $date_from 	= date('Y-m-d', strtotime(_datetime().' - 30 days '));
                    ;
                    break;

                    # Last 90 Days to date inclusive
                case '90':
                case '90days':
                case '90 days':
                case '3months':
                case '3 months':
                    $date_from 	= date('Y-m-d', strtotime(_datetime().' - 90 days '));
                    ;
                    break;

                    # Last 180 Days to date inclusive
                case '180':
                case '180days':
                case '180 days':
                case '6months':
                case '6 months':
                    $date_from 	= date('Y-m-d', strtotime(_datetime().' - 180 days '));
                    ;
                    break;

                    # Last 365 Days to date inclusive
                case '365':
                case '365days':
                case '365 days':
                case '12months':
                case '12 months':
                case '1year':
                case '1 year':
                    $date_from 	= date('Y-m-d', strtotime(_datetime().' - 1 year'));
                    ;
                    break;
            }
            $result = (object)[
                'date_from' => $date_from,
                'date_to' 	=> $date_to,
            ];
        }
        return $result;
    }


    /*
    * Send New Job Notification
    */
    public function send_new_job_notification($account_id = false, $data = false, $recipients = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($data)) {
            $destination= !empty($data['destination']) ? $data['destination'] : '';
            $email_body = $this->load->view('email_templates/job/new_job_notification_admin', $data, true);

            $destination= is_array($destination) ? array_unique($destination) : $destination;
            $email_data = [
                'to'		=> $destination,
                #'to'		=> 'enockkabungo@evidentsoftware.co.uk',
                #'from'		=> ['buildteam@evidentsoftware.co.uk','Evident Software Ltd'],
                'from'		=> ['alerts@evidentsoftware.co.uk','Evident Software Alerts'],
                #'bcc'		=> ['enockkabungo@evidentsoftware.co.uk'],
                'subject'	=> 'New Job(s) Created',
                'message'	=> $email_body
            ];

            if (ENVIRONMENT == 'production') {
                $result = $this->mail->send_mail($email_data);
            }
        }
        return $result;
    }


    /** Generate Job Type Ref **/
    private function generate_job_type_ref($account_id = false, $data = false)
    {
        if (!empty($account_id) && !empty($data)) {
            $job_type_ref = $account_id;
            $job_type_ref .= (!empty($data['job_type'])) ? lean_string($data['job_type']) : '';
            $job_type_ref .= (!empty($data['contract_id'])) ? $data['contract_id'] : '';
            $job_type_ref .= (!empty($data['evidoc_type_id']) && (intval($data['evidoc_type_id']) > 0)) ? $data['evidoc_type_id'] : '';
            $job_type_ref .= (!empty($data['job_type_id'])) ? $data['job_type_id'] : '';
            $job_type_ref .= (!empty($data['category_id'])) ? $data['category_id'] : '';
            $job_type_ref .= (!empty($data['discipline_id'])) ? $data['discipline_id'] : '';
            $job_type_ref .= (!empty($data['frequency_id'])) ? $data['frequency_id'] : '';
        } else {
            $job_type_ref = $account_id.$this->ssid_common->generate_random_password();
        }
        return strtoupper($job_type_ref);
    }


    /*
    * Send New Job Notification to Engineer
    */
    public function send_engineer_notification($account_id = false, $engineer_id = false, $data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($engineer_id) && !empty($data)) {
            ## Get user
            $engineer = $this->ion_auth->get_user_by_id($account_id, $engineer_id);

            if (!empty($engineer->email)) {
                $email_content['user'] 		= $engineer;
                $email_content['jobs_data'] = $data;
                $destination= trim($engineer->email);
                $email_body = $this->load->view('email_templates/job/new_job_notification_engineer', $email_content, true);

                $destination= !empty($engineer->email) ? trim($engineer->email) : 'buildteam@evidentsoftware.co.uk';

                $email_data = [
                    'to'		=> $destination,
                    #'to'		=> 'enockkabungo@evidentsoftware.co.uk',
                    'from'		=> ['alerts@evidentsoftware.co.uk','Evident Software Alerts'],
                    #'bcc'		=> ['enockkabungo@evidentsoftware.co.uk'],
                    'subject'	=> 'New Job(s) Assigned To You',
                    'message'	=> $email_body
                ];

                if (ENVIRONMENT == 'production') {
                    $result = $this->mail->send_mail($email_data);
                }
            }
        }
        return $result;
    }


    /**
    * Clone an existing Schedule
    **/
    public function clone_schedule($account_id = false, $schedule_id = false, $data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($schedule_id)) {
            $schedule_exists 	= $this->db->get_where('schedules', [ 'account_id'=>$account_id, 'schedule_id'=>$schedule_id ])->row();

            if (!empty($schedule_exists)) {
                $data			= convert_to_array($data);
                $first_activity_due_date = !empty($data['first_activity_due_date']) ? date('Y-m-d', strtotime($data['first_activity_due_date'])) : false;
                $limit 			= !empty($data['limit']) ? $data['limit'] : SCHEDULE_CLONE_DEFAULT_LIMIT;
                $offset			= !empty($data['offset']) ? $data['offset'] : 0;

                $cloned_data 	= (array) $schedule_exists;

                unset($cloned_data['schedule_id'], $cloned_data['schedule_ref'], $cloned_data['date_created'], $cloned_data['last_modified'], $cloned_data['last_modified_by']);
                $cloned_data['first_activity_due_date'] = !empty($first_activity_due_date) ? $first_activity_due_date : date('Y-m-d', strtotime($cloned_data['first_activity_due_date']. ' + 1 year'));
                $cloned_data['schedule_status'] 		= 'Pending';
                $cloned_data['schedule_ref'] 			= $this->generate_schedule_ref($account_id, $cloned_data);
                $cloned_data['is_cloned'] 				= 1;
                $expiry_date 							= date('Y-m-d', strtotime($cloned_data['first_activity_due_date']. ' + 1 year')). ' 23:59:59';
                $cloned_data['expiry_date'] 			= date('Y-m-d H:i:s', strtotime($expiry_date. ' - 1 day'));
                $cloned_data['cloned_schedule_id'] 		= $schedule_id;

                ## Create New Schedule
                $check_conflict = $this->db->select('schedules.schedule_id', false)
                    ->where('schedules.account_id', $account_id)
                    ->where([ 'frequency_id' => $cloned_data['frequency_id'], 'schedule_ref' => $cloned_data['schedule_ref'] ])
                    ->where('schedules.schedule_id !=', $schedule_id)
                    ->limit(1)
                    ->get('schedules')
                    ->row();

                if (!empty($check_conflict)) {
                    $this->db->where('schedules.schedule_id', $check_conflict->schedule_id)
                        ->update('schedules', $cloned_data);

                    ## Create Activities items
                    $cloned_activities 	= $this->clone_schedule_activities($account_id, $schedule_id, $check_conflict->schedule_id, $cloned_data, $limit, $offset);
                    $new_schedule_id	= $check_conflict->schedule_id;
                    $message 			= 'The resulting Schedule already exist, all Activities/Jobs have been updated successfully.';
                } else {
                    $cloned_data['created_by'] 	= $this->ion_auth->_current_user->id;
                    $this->db->insert('schedules', $cloned_data);
                    $new_schedule_id 			= $this->db->insert_id();
                    $cloned_data['schedule_id']	= $new_schedule_id;

                    if (!empty($new_schedule_id)) {
                        $cloned_activities = $this->clone_schedule_activities($account_id, $schedule_id, $new_schedule_id, $cloned_data, $limit, $offset);
                        $message = 'Schedule cloned successfully.';
                    }
                }

                if (!empty($cloned_activities)) {
                    $cloned_activities['counters'] = $this->get_schedule_counters($account_id, $schedule_id, $new_schedule_id, $limit);
                    $result = $cloned_activities;
                    $this->session->set_flashdata('message', $message);
                } else {
                    $this->session->set_flashdata('message', 'Unable to Clone Schedule due to Sites being unlinked from the Contract or missing Jobs from the Original schedule record. Please create the Schedule normally (do not clone).');
                }
            } else {
                $this->session->set_flashdata('message', 'This Schedule record does not exist or does not belong to you.');
                return false;
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }
        return $result;
    }

    /**
    * Get Schedule Totals
    */
    private function get_schedule_counters($account_id = false, $src_schedule_id = false, $dest_schedule_id = false, $limit = SCHEDULE_CLONE_DEFAULT_LIMIT, $offset = 0)
    {
        $result = false;

        if (!empty($account_id) && !empty($src_schedule_id) && !empty($dest_schedule_id)) {
            $query = $this->db->select('schedule_activities.activity_id, schedule_activities.site_id, schedule_activities.asset_id', false)
                ->where('schedule_activities.account_id', $account_id)
                ->where('schedule_activities.schedule_id', $src_schedule_id)
                ->group_by('schedule_activities.activity_id')
                ->get('schedule_activities');

            if ($query->num_rows() > 0) {
                $res 	= $query->result_array();
                $sites  = array_filter(array_column($res, 'site_id'));
                $assets = array_filter(array_column($res, 'asset_id'));

                ## Already processed
                $processed = $this->db->select('schedule_activities.activity_id, schedule_activities.site_id, schedule_activities.asset_id', false)
                    ->where('schedule_activities.account_id', $account_id)
                    ->where('schedule_activities.schedule_id', $dest_schedule_id)
                    ->group_by('schedule_activities.activity_id')
                    ->get('schedule_activities');

                if ($processed->num_rows() > 0) {
                    $prosc 	= $processed->result_array();
                    $processed_activities 	= (string) $processed->num_rows();
                    $processed_sites  		= array_filter(array_column($prosc, 'site_id'));
                    $processed_assets 		= array_filter(array_column($prosc, 'asset_id'));
                }

                $result = (object)[
                    'expected_activities' 	=> (string) $query->num_rows(),
                    'expected_sites' 		=> !empty($sites) ? strval(count(array_unique($sites))) : '0',
                    'expected_assets' 		=> !empty($assets) ? strval(count(array_unique($assets))) : '0',
                    'processed_activities' 	=> !empty($processed_activities) ? $processed_activities : '0',
                    'processed_sites' 		=> !empty($processed_sites) ? strval(count(array_unique($processed_sites))) : '0',
                    'processed_assets' 		=> !empty($processed_assets) ? strval(count(array_unique($processed_assets))) : '0',
                    'limit' 				=> (string) $limit,
                    'offset'				=> (string) $offset,
                    'activity_pages' 		=> (string) ceil(($query->num_rows()) / $limit)
                ];
            } else {
                $result = (object)[
                    'total_activities' 		=> (string) 0,
                    'total_sites' 			=> (string) 0,
                    'total_assets' 			=> (string) 0,
                    'processed_activities' 	=> '0',
                    'processed_sites' 		=> '0',
                    'processed_assets' 		=> '0',
                    'limit' 				=> (string) $limit,
                    'offset'				=> (string) $offset,
                    'activity_pages' 		=> (string) 0
                ];
            }
        }
        return $result;
    }

    /*
    * Clone Schedule Activities
    **/
    public function clone_schedule_activities($account_id = false, $source_sch_id = false, $destination_sch_id = false, $params = false, $limit = false, $offset = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($source_sch_id) && !empty($destination_sch_id)) {
            $limit 			= !empty($limit) ? $limit : SCHEDULE_CLONE_DEFAULT_LIMIT;
            $offset			= !empty($offset) ? $offset : 0;

            $contract_sites = $this->get_contract_site_ids($account_id, $params['contract_id']);
            if (!empty($contract_sites->site_id)) {
                $sites 			= is_array($contract_sites->site_id) ? $contract_sites->site_id : [ $contract_sites->site_id ];
                $contract_name 	= $contract_sites->contract_name;
                $this->db->where_in('schedule_activities.site_id', $sites);
            }

            $this->db->select('schedule_activities.*, schedules.schedule_name, schedules.frequency_id, job.site_id,  job.asset_id, job.customer_id, job.contract_id, job.address_id, job.location_id, job.job_number, job.client_reference, job.region_id, job.access_requirements, job.permission_requirements', false)
                ->join('job', 'job.activity_id = schedule_activities.activity_id', 'left')
                ->join('schedules', 'schedules.schedule_id = schedule_activities.schedule_id', 'left');

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query 	= $this->db->order_by('schedule_activities.activity_id')
                ->get_where('schedule_activities', [ 'schedule_activities.account_id' => $account_id, 'schedule_activities.schedule_id' => $source_sch_id ]);

            if ($query->num_rows() > 0) {
                ini_set('memory_limit', '480M');
                set_time_limit(320);

                $all_activities 	= $new_activities = $update_activities = [];
                $site_tracking_data = [];

                foreach ($query->result_array() as $k => $row) {
                    $source_activity_id  = $row['activity_id'];

                    unset($row['activity_id'], $row['date_created'], $row['last_modified'], $row['last_modified_by']);
                    $row['schedule_id']  = $destination_sch_id;
                    $row['due_date'] 	 = date('Y-m-01', strtotime($row['due_date']. ' + 1 year'));
                    $row['job_due_date'] = date('Y-m-01', strtotime($row['job_due_date']. ' + 1 year'));
                    $row['status'] 		 = 'Not due';
                    $row['completion'] 	 = 0;

                    $params					= array_merge($params, $row);
                    $site_tracking_data[]	= $this->ssid_common->_filter_data('schedule_site_tracker', $params);

                    $this->db->select('schedule_activities.activity_id', false)
                        ->where('schedule_activities.account_id', $account_id)
                        ->where([ 'activity_name'=>$row['activity_name'], 'schedule_id'=>$destination_sch_id, 'job_type_id'=>$row['job_type_id'], 'due_date'=>$row['due_date'] ])
                        ->limit(1);

                    if (!empty($row['site_id'])) {
                        $this->db->where('schedule_activities.site_id', $row['site_id']);
                    }

                    if (!empty($row['asset_id'])) {
                        $this->db->where('schedule_activities.asset_id', $row['asset_id']);
                    }

                    $check_exists = $this->db->get('schedule_activities')->row();

                    $activity_row = $job = $this->ssid_common->_filter_data('schedule_activities', $row);
                    if (!empty($check_exists)) {
                        $row['last_modified_by'] 	= $this->ion_auth->_current_user->id;
                        $this->db->where('activity_id', $check_exists->activity_id)->update('schedule_activities', $activity_row);
                        $activity_id 				= $check_exists->activity_id;
                        $row['activity_id']			= $activity_id;
                        $update_activities[$k]		= $activity_row;
                    } else {
                        $row['created_by'] 	= $this->ion_auth->_current_user->id;
                        $this->db->insert('schedule_activities', $activity_row);
                        $activity_id 		= $this->db->insert_id();
                        $row['activity_id']	= $activity_id;
                        $new_activities[$k]	= $activity_row;
                    }

                    $all_activities['contract_id'] 			= (string) !empty($params['contract_id']) ? $params['contract_id'] : false;
                    $all_activities['contract_name'] 		= !empty($contract_name) ? $contract_name : false;
                    $all_activities['schedule_id'] 			= strval($destination_sch_id);
                    $all_activities['schedule_name'] 		= !empty($params['schedule_name']) ? $params['schedule_name'] : false;
                    $all_activities['cloned_schedule_id'] 	= strval($source_sch_id);
                    $all_activities['frequency_id'] 		= !empty($params['frequency_id']) ? $params['frequency_id'] : false;
                    if (!empty($params['site_id'])) {
                        $all_activities['sites'][] 		= $params['site_id'];
                    }

                    if (!empty($params['asset_id'])) {
                        $all_activities['assets'][] = $params['asset_id'];
                    }

                    $all_activities['activities'][] = $activity_id;
                    #$all_activities['activities'][] = !empty( $all_activities['activities'] ) ? $all_activities['activities'] + 1 : 1;
                }
            }

            if (!empty($all_activities)) {
                /* if( !empty( $site_tracking_data ) ){
                    $this->generate_site_schedule_tracking( $account_id, $site_tracking_data );
                } */

                $all_activities['sites'] 		= !empty($all_activities['sites']) ? strval(count(array_unique($all_activities['sites']))) : '0';
                $all_activities['assets'] 		= !empty($all_activities['assets']) ? strval(count(array_unique($all_activities['assets']))) : '0';
                $all_activities['activities'] 	= !empty($all_activities['activities']) ? strval(count($all_activities['activities'])) : '0';

                $this->session->set_flashdata('message', 'Schedule Activities/Job have been cloned successfully.');
                $result = $all_activities;
            } else {
                $this->session->set_flashdata('message', 'Unable to clone Activities, please try again.');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }

        return $result;
    }

    private function get_contract_site_ids($account_id = false, $contract_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($contract_id)) {
            $sites = $this->db->select('sites_contracts.site_id, contract.contract_name', false)
                ->join('contract', 'contract.contract_id = sites_contracts.contract_id', 'left')
                ->where('sites_contracts.account_id', $account_id)
                ->where('sites_contracts.contract_id', $contract_id)
                ->group_by('sites_contracts.site_id')
                ->get('sites_contracts');

            if ($sites->num_rows() > 0) {
                $contract_name = array_unique(array_column($sites->result_array(), 'contract_name'))[0];
                $result = (object)[
                    'contract_id' 	=> $contract_id,
                    'contract_name' => $contract_name,
                    'site_id'  		=> array_unique(array_column($sites->result_array(), 'site_id')),
                ];
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }
        return $result;
    }


    /*
    * Clone Activities Jobs
    **/
    public function clone_activity_jobs($account_id = false, $schedule_id = false, $cloned_schedule_id = false, $params = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($schedule_id) && !empty($cloned_schedule_id)) {
            $params = convert_to_array($params);
            $query 	= $this->db->select('schedule_activities.*, schedule_frequencies.frequency_group', false)
                ->join('schedules', 'schedule_activities.schedule_id = schedules.schedule_id', 'left')
                ->join('schedule_frequencies', 'schedules.frequency_id = schedule_frequencies.frequency_id', 'left')
                ->group_by('schedule_activities.activity_id')
                ->get_where('schedule_activities', [ 'schedule_activities.account_id' => $account_id, 'schedule_activities.schedule_id' => $schedule_id ]);

            if ($query->num_rows() > 0) {
                $jobs_data 			= [];
                $frequency_group 	= '';
                $sites 	= $assets 	= $customers 	= [];
                foreach ($query->result() as $k => $row) {
                    $frequency_group = $row->frequency_group;
                    unset($row->date_created, $row->created_by, $row->last_modified, $row->last_modified_by);
                    if (!empty($row->asset_id)) {
                        $this->db->where('job.asset_id', $row->asset_id);
                        $assets[] = $row->asset_id;
                    }

                    if (!empty($row->site_id)) {
                        $this->db->where('job.site_id', $row->site_id);
                        $sites[] = $row->site_id;
                    }

                    if (!empty($row->customer_id)) {
                        $this->db->where('job.customer_id', $row->customer_id);
                        $customers[] = $row->customer_id;
                    }

                    $job = $this->db->select('job.site_id, job.asset_id, job.customer_id, job.contract_id, job.address_id, job.region_id', false)
                        ->where('job.schedule_id', $cloned_schedule_id)
                        ->where('job.job_type_id', $row->job_type_id)
                        ->group_by('job.job_type_id')
                        ->get('job')
                        ->row();

                    if (!empty($job)) {
                        $jobs_data[] = $this->ssid_common->_filter_data('job', array_merge((array)$job, $params, (array)$row));
                    } else {
                        $jobs_data[] = $this->ssid_common->_filter_data('job', array_merge($params, (array)$row));
                    }
                }

                if (!empty($jobs_data)) {
                    $cloned_jobs = $this->generate_activity_jobs($account_id, $jobs_data, $frequency_group, true);
                    if ($cloned_jobs) {
                        $total_jobs	= 0;
                        $total_jobs += !empty($cloned_jobs['new']) ? count($cloned_jobs['new']) : 0;
                        $total_jobs += !empty($cloned_jobs['updated']) ? count($cloned_jobs['updated']) : 0;
                        $result = [
                            'schedule_id' 	=> strval($schedule_id),
                            'jobs' 			=> $total_jobs,
                            'sites' 		=> !empty($sites) ? count(array_unique($sites)) : 0,
                            'assets' 		=> !empty($assets) ? count(array_unique($assets)) : 0,
                            'customers' 	=> !empty($customers) ? count(array_unique($customers)) : 0,
                        ];
                        $this->session->set_flashdata('message', 'Schedule cloning process completed Successfully.');
                    } else {
                        $this->session->set_flashdata('message', 'Unable to clone Jobs, please start the cloning processing again!');
                    }
                } else {
                    $this->session->set_flashdata('message', 'Schedule activities have not been cloned yet. Request aborted!');
                }
            } else {
                $this->session->set_flashdata('message', 'Schedule activities have not been cloned yet. Request aborted!');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }

        return $result;
    }


    /*
    * Complete Scheduling Process
    **/
    public function complete_scheduling_process($account_id = false, $schedule_id = false, $params = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($schedule_id)) {
            $params = convert_to_array($params);
            $query 	= $this->db->select('schedule_activities.*, schedules.contract_id, schedule_frequencies.frequency_id, schedule_frequencies.frequency_group', false)
                ->join('schedules', 'schedule_activities.schedule_id = schedules.schedule_id', 'left')
                ->join('schedule_frequencies', 'schedules.frequency_id = schedule_frequencies.frequency_id', 'left')
                ->group_by('schedule_activities.activity_id')
                ->get_where('schedule_activities', [ 'schedule_activities.account_id' => $account_id, 'schedule_activities.schedule_id' => $schedule_id ]);

            if ($query->num_rows() > 0) {
                $jobs_data 			= [];
                $frequency_group 	= '';
                $frequency_id 		= !empty($params['frequency_id']) ? $params['frequency_id'] : false;
                $contract_id 		= !empty($params['contract_id']) ? $params['contract_id'] : false;
                $sites 	= $assets 	= $customers 	= [];
                foreach ($query->result() as $k => $row) {
                    $frequency_group= $row->frequency_group;
                    $frequency_id 	= (!empty($frequency_id) && ($frequency_id == $row->frequency_id)) ? $frequency_id : $row->frequency_id;
                    $contract_id 	= (!empty($contract_id) && ($contract_id == $row->contract_id)) ? $contract_id : $row->contract_id;
                    unset($row->date_created, $row->created_by, $row->last_modified, $row->last_modified_by);
                    if (!empty($row->asset_id)) {
                        $assets[] = $row->asset_id;
                    }

                    if (!empty($row->site_id)) {
                        $sites[] = $row->site_id;
                    }

                    if (!empty($row->customer_id)) {
                        $customers[] = $row->customer_id;
                    }

                    $jobs_data[] = $this->ssid_common->_filter_data('job', array_merge($params, (array)$row));
                }

                if (!empty($jobs_data)) {
                    $processed_jobs = $this->generate_activity_jobs($account_id, $jobs_data, $frequency_group, true);
                    if ($processed_jobs) {
                        $total_jobs	= 0;
                        $total_jobs += !empty($processed_jobs['new']) ? count($processed_jobs['new']) : 0;
                        $total_jobs += !empty($processed_jobs['updated']) ? count($processed_jobs['updated']) : 0;
                        $result = [
                            'schedule_id' 	=> strval($schedule_id),
                            'contract_id' 	=> strval($contract_id),
                            'frequency_id' 	=> strval($frequency_id),
                            'jobs' 			=> strval($total_jobs),
                            'sites' 		=> !empty($sites) ? strval(count(array_unique($sites))) : '0',
                            'assets' 		=> !empty($assets) ? strval(count(array_unique($assets))) : '0',
                            'customers' 	=> !empty($customers) ? strval(count(array_unique($customers))) : '0',
                        ];
                        $this->session->set_flashdata('message', 'Scheduling process completed Successfully.');
                    } else {
                        $this->session->set_flashdata('message', 'Unable to complete the Scheduling process, please re-start the processing!');
                    }
                } else {
                    $this->session->set_flashdata('message', 'Schedule activities were not created. Request aborted!');
                }
            } else {
                $this->session->set_flashdata('message', 'Schedule activities have not been created yet. Request aborted!');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }

        return $result;
    }

    /**
    * Get Scheduled Job Types
    */
    public function get_scheduled_job_types($account_id = false, $schedule_id = false, $where = false)
    {
        $result = null;
        if (!empty($account_id) && !empty($schedule_id)) {
            $where = convert_to_array($where);

            if (!empty($where['contract_id'])) {
                #$this->db->where( 'job.contract_id', $where['contract_id'] );
            }

            if (!empty($where['site_id'])) {
                #$this->db->where( 'job.site_id', $where['site_id'] );
            }

            $query = $this->db->select('job.job_type_id, job_types.job_type', false)
                ->join('job_types', 'job_types.job_type_id = job.job_type_id')
                ->where('job.account_id', $account_id)
                ->where('job.schedule_id', $schedule_id)
                ->order_by('job_types.job_type')
                ->group_by('job_types.job_type')
                ->get('job');

            if ($query->num_rows()) {
                $result = $query->result();
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }
        return $result;
    }


    /**
    * Create a new revised Schedule record
    **/
    public function create_schedules_revised($account_id = false, $frequency_id = false, $schedule_data = false)
    {
        $result = null;

        if (!empty($account_id) && !empty($frequency_id) && !empty($schedule_data)) {
            $frequency_data 		= $this->db->get_where('schedule_frequencies', [ 'account_id'=>$account_id, 'frequency_id'=>$frequency_id ])->row();
            $schedule_data 			= convert_to_array($schedule_data);
            $schedule_activities 	= !empty($schedule_data['schedule_activities']) ? convert_to_array($schedule_data['schedule_activities']) : $schedule_data;
            $schedule_ref			= $this->generate_schedule_ref($account_id, array_merge($schedule_data, ['frequency_id'=>$frequency_id]));
            if (!empty($schedule_activities)) {
                $multiple_asset_types 	= $schedule_activities;
                $multiple_asset_types	= (is_string($multiple_asset_types)) ? json_decode($multiple_asset_types) : $multiple_asset_types;
                $multiple_asset_types	= (is_object($multiple_asset_types)) ? object_to_array($multiple_asset_types) : $multiple_asset_types;
            }

            $data 					 		= $this->ssid_common->_filter_data('schedules', $schedule_data);
            $data['schedule_ref'] 	 		= $schedule_ref;
            $data['schedule_status']		= 'Pending';
            $data['activities_total']		= !empty($frequency_data->activities_required) ? ( string ) $frequency_data->activities_required : '1';
            $data['activities_pending']		= $data['activities_total'];
            $data['first_activity_due_date']= date('Y-m-d', strtotime($data['first_activity_due_date']));
            $expiry_date 					= date('Y-m-d', strtotime($data['first_activity_due_date']. ' + 1 year')). ' 23:59:59';
            $data['expiry_date'] 			= date('Y-m-d H:i:s', strtotime($expiry_date. ' - 1 day'));

            $check_exists = $this->db->select('schedules.schedule_id', false)
                ->where('schedules.account_id', $account_id)
                ->where([ 'frequency_id'=>$frequency_id, 'schedule_ref'=>$schedule_ref ])
                ->limit(1)
                ->get('schedules')
                ->row();

            if (!empty($check_exists)) {
                $data['last_modified_by'] 	= $this->ion_auth->_current_user->id;
                $this->db->where('schedule_id', $check_exists->schedule_id)
                    ->update('schedules', $data);
                $record = $this->get_schedules($account_id, false, [ 'schedule_id'=>$check_exists->schedule_id ]);
            } else {
                $data['created_by'] 		= $this->ion_auth->_current_user->id;
                $this->db->insert('schedules', $data);
                $record = $this->get_schedules($account_id, false, [ 'schedule_id'=>$this->db->insert_id() ]);
            }

            if (!empty($record)) {
                ## Create Activity Placeholders
                if (!empty($multiple_asset_types)) {
                    $activity_containers = $this->create_activity_containers_revised($account_id, $record->schedule_id, [ 'schedule_activities' => $multiple_asset_types ], $schedule_data);
                    $record->processed_activities 	= $activity_containers->total_activities;
                    $record->processed_assets 		= $activity_containers->total_assets;
                }

                $result = $record;
                $this->session->set_flashdata('message', 'Schedule record(s) created successfully.');
            } else {
                $this->session->set_flashdata('message', 'Error! There was a problem completing your request, please check your submitted data.');
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }

        return $result;
    }

    /** Create Multiple Schedule Activities **/
    public function create_activity_containers($account_id = false, $schedule_id = false, $activities_data = false, $data = false, $limit = SCHEDULE_CLONE_DEFAULT_LIMIT, $offset = 0)
    {
        $result = false;

        if (!empty($account_id) && !empty($schedule_id) && !empty($activities_data)) {
            $schedule = $this->db->select('schedules.frequency_id, schedules.contract_id, site.site_address_id `site_address_id`', false)
                ->join('site', 'site.site_id = schedules.site_id', 'left')
                ->where('schedules.schedule_id', $schedule_id)
                ->get('schedules')
                ->row();

            $address_id  			= !empty($data['address_id']) ? $data['address_id'] : (!empty($schedule->site_address_id) ? $schedule->site_address_id : false);
            $contract_id 			= !empty($data['contract_id']) ? $data['contract_id'] : (!empty($schedule->contract_id) ? $schedule->contract_id : false);
            $frequency_id 			= !empty($data['frequency_id']) ? $data['frequency_id'] : (!empty($schedule->frequency_id) ? $schedule->frequency_id : false);
            $asset_site_id 			= !empty($data['site_id']) ? $data['site_id'] : (!empty($schedule->site_id) ? $schedule->site_id : false);
            $activities_data		= convert_to_array($activities_data);

            $new_data = $existing_records 	= $all_activities = $activity_jobs = [];
            $multi_asset_type_activities 	= !empty($activities_data['schedule_activities']) ? $activities_data['schedule_activities'] : false;
            $multi_asset_type_activities	= (is_string($multi_asset_type_activities)) ? json_decode($multi_asset_type_activities) : $multi_asset_type_activities;
            $multi_asset_type_activities	= (is_object($multi_asset_type_activities)) ? object_to_array($multi_asset_type_activities) : $multi_asset_type_activities;

            if (!empty($multi_asset_type_activities)) {
                ini_set('memory_limit', '428M');

                $activity_info = $job_data = [];
                foreach ($multi_asset_type_activities as $visit_id => $asset_type_activities) {
                    $selected_assets = [];
                    foreach ($asset_type_activities as $asset_id => $activity_data) {
                        $selected_assets[$visit_id][] = [
                            'account_id' 	=> $account_id,
                            'asset_id' 		=> $asset_id
                        ];

                        if (!empty($activity_data['activity_name'])) {
                            $due_date 			= !empty($activity_data['due_date']) ? date('Y-m-d', strtotime($activity_data['due_date'])) : date('Y-m-d', strtotime(' + 7 days'));
                            $job_site_id 		= !empty($activity_data['site_id']) ? $activity_data['site_id'] : $asset_site_id;
                            $site_region		= $this->db->select('site_id, region_id')
                                ->get_where('site', [ 'site_id' => $job_site_id, 'account_id' => $account_id ])
                                ->row();

                            $job_data[$visit_id] = [
                                'account_id' 	=> $account_id,
                                'job_type_id' 	=> !empty($activity_data['job_type_id']) ? $activity_data['job_type_id'] : false,
                                'schedule_id' 	=> $schedule_id,
                                'contract_id' 	=> !empty($contract_id) ? $contract_id : false,
                                'frequency_id' 	=> !empty($frequency_id) ? $frequency_id : false,
                                'address_id' 	=> $address_id,
                                'site_id' 		=> $job_site_id,
                                'due_date' 		=> $due_date,
                                'job_due_date' 	=> !empty($activity_data['job_due_date']) ? date('Y-m-d', strtotime($activity_data['job_due_date'])) : $due_date,
                                'is_multi_asset'=> 1,
                                'region_id'		=> !empty($site_region->region_id) ? $site_region->region_id : null
                            ];

                            $activity_info[$visit_id] = [
                                'account_id' 	=> $account_id,
                                'job_type_id' 	=> !empty($activity_data['job_type_id']) ? $activity_data['job_type_id'] : false,
                                'activity_name' => $activity_data['activity_name'],
                                'schedule_id' 	=> $schedule_id,
                                'contract_id' 	=> !empty($contract_id) ? $contract_id : false,
                                'frequency_id' 	=> !empty($frequency_id) ? $frequency_id : false,
                                'address_id' 	=> $address_id,
                                'site_id' 		=> !empty($activity_data['site_id']) ? $activity_data['site_id'] : $asset_site_id,
                                'due_date' 		=> $due_date,
                                'job_due_date' 	=> !empty($activity_data['job_due_date']) ? date('Y-m-d', strtotime($activity_data['job_due_date'])) : $due_date,
                            ];
                        }
                    }

                    $job_data 			= $this->ssid_common->_filter_data('job', $job_data[$visit_id]);
                    $activity_info 		= $this->ssid_common->_filter_data('schedule_activities', $activity_info[$visit_id]);

                    $this->db->select('schedule_activities.activity_id', false)
                        ->where('schedule_activities.account_id', $account_id)
                        ->where([ 'activity_name'=>$activity_info['activity_name'], 'schedule_id'=>$schedule_id, 'job_type_id'=>$job_data['job_type_id'], 'due_date'=>$activity_info['due_date'] ])
                        ->limit(1);

                    if (!empty($activity_info['site_id'])) {
                        $this->db->where('schedule_activities.site_id', $activity_info['site_id']);
                    }

                    $check_exists = $this->db->get('schedule_activities')->row();

                    if (!empty($check_exists)) {
                        $activity_info['activity_id'] 		= $check_exists->activity_id;
                        $activity_info['last_modified_by'] 	= $this->ion_auth->_current_user->id;
                        $existing_records[] 				= $activity_info;
                    } else {
                        $activity_info['status'] 		= 'Not due';
                        $activity_info['completion'] 	= 0;
                        $activity_info['created_by'] 	= $this->ion_auth->_current_user->id;
                        $this->db->insert('schedule_activities', $activity_info);
                        $activity_info['activity_id'] 	= $this->db->insert_id();
                        $new_records[] 					= $activity_info;
                    }

                    $job_data['activity_id'] 	= $activity_info['activity_id'];

                    ## Create Job Container
                    $check_job_exists = $this->db->where($job_data)
                        ->get('job')->row();

                    if (!empty($check_job_exists)) {
                        $job_data['job_id'] 			= $check_job_exists->job_id;
                        $job_data['last_modified_by'] 	= $this->ion_auth->_current_user->id;
                    } else {
                        $job_data['status_id'] 		= 2;
                        $job_data['created_by'] 	= $this->ion_auth->_current_user->id;
                        $this->db->insert('job', $job_data);
                        $job_data['job_id'] 	= $this->db->insert_id();
                    }

                    $link_assets 		= $this->link_job_assets($account_id, $job_data, $selected_assets[$visit_id]);
                    if (!empty($link_assets)) {
                        $all_activities[] = $job_data['job_id'];
                    }
                }

                if (!empty($all_activities)) {
                    $result = $all_activities;
                    $this->session->set_flashdata('message', 'Activity record(s) processed successfully.');
                } else {
                    $this->session->set_flashdata('message', 'Unable to process your request. Please try again!');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }

        return $result;
    }


    /**
    * Link Job Assets
    */
    public function link_job_assets($account_id = false, $job_record = false, $selected_assets = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($job_record) && !empty($selected_assets)) {
            $job_id 			=!empty($job_record['job_id']) ? $job_record['job_id'] : $job_record;
            $selected_assets 	= !empty($selected_assets['assets_to_check']) ? $selected_assets['assets_to_check'] : $selected_assets;
            $selected_assets 	= convert_to_array($selected_assets);
            $all_records 		= $new_records = $existing_records = [];

            foreach ($selected_assets as $key => $asset_info) {
                $asset_id = !empty($asset_info['asset_id']) ? $asset_info['asset_id'] : $asset_info;
                $conditions   = [ 'job_id'=> $job_id, 'account_id' => $account_id, 'asset_id'=> $asset_id ];
                $check_exists = $this->db->select('id', false)->get_where('job_assets', $conditions)->row();
                if (!empty($check_exists->id)) {
                    $existing_records[] = [
                        'id' 				=> $check_exists->id,
                        'job_id' 			=> $job_id,
                        'asset_id' 			=> $asset_id,
                        'account_id' 		=> $account_id,
                        'last_modified_by' 	=> $this->ion_auth->_current_user->id
                    ];
                } else {
                    $asset_legitimacy = $this->db->select('asset_id', false)->get_where('asset', [ 'account_id' => $account_id, 'asset_id'=> $asset_id ])->row();
                    if (!empty($asset_legitimacy->asset_id)) {
                        $new_records[] = [
                            'job_id' 			=> $job_id,
                            'asset_id' 			=> $asset_id,
                            'account_id' 		=> $account_id,
                            'created_by' 		=> $this->ion_auth->_current_user->id
                        ];
                    } else {
                        $invalid_assets[] = $asset_id;
                    }
                }
                $all_records[] = $asset_id;
            }

            if (!empty($new_records)) {
                $this->db->insert_batch('job_assets', $new_records);
            }

            if (!empty($existing_records)) {
                $this->db->update_batch('job_assets', $existing_records, 'id');
            }

            if (!empty($invalid_assets)) {
                $result = [
                    'successful'=> array_merge($new_records, $existing_records),
                    'invalid-assets' => $invalid_assets
                ];
                $this->session->set_flashdata('message', 'Some or all of your assets were not linked.');
            } elseif (!empty($all_records)) {
                $this->session->set_flashdata('message', 'Job assets processed successfully.');
                $result = $all_records;
            } else {
                $this->session->set_flashdata('message', 'There was a problem processing your Job assets. Please try again!');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }
        return $result;
    }


    /*
    * Get Job Assets
    **/
    public function get_job_assets($account_id = false, $job_id = false, $where = false, $order_by = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($job_id)) {
            if (!empty($account_id) && !empty($job_id)) {
                $where   = convert_to_array($where);

                $this->db->select('job_assets.id, job_assets.job_id, job_assets.asset_id, job_assets.account_id, job_assets.archived, job_assets.is_active', false)
                    ->select('asset.asset_type_id, asset.site_id, asset.asset_unique_id, asset_types.asset_type, asset_types.discipline_id', false)
                    ->select('account_discipline.account_discipline_name `discipline_name`,account_discipline.account_discipline_image_url `discipline_image_url`, site_zones.zone_name, site_locations.location_name, job.job_date, job.due_date, job.job_due_date', false)
                    ->join('job', 'job.job_id = job_assets.job_id', 'left')
                    ->join('asset', 'asset.asset_id = job_assets.asset_id', 'left')
                    ->join('asset_types', 'asset_types.asset_type_id = asset.asset_type_id', 'left')
                    ->join('account_discipline', 'account_discipline.discipline_id = asset_types.discipline_id', 'left')
                    ->join('site_zones', 'site_zones.zone_id = asset.zone_id', 'left')
                    ->join('site_locations', 'site_locations.location_id = asset.location_id', 'left')
                    ->where('job_assets.is_active', 1)
                    ->where('job_assets.account_id', $account_id);

                if (isset($where['asset_id'])) {
                    if (!empty($where['asset_id'])) {
                        $this->db->where('job_assets.asset_id', $where['asset_id']);
                    }
                    unset($where['asset_id']);
                }

                if (isset($where['grouped'])) {
                    if (!empty($where['grouped'])) {
                        $grouped = true;
                    }
                    unset($where['grouped']);
                }

                if (!empty($job_id)) {
                    $this->db->where('job_assets.job_id', $job_id);
                }

                $query = $this->db->group_by('job_assets.id')
                    ->get('job_assets');

                if ($query->num_rows() > 0) {
                    $data = [];
                    foreach ($query->result() as $k => $row) {
                        $audit_details = $this->db->where([ 'audit.account_id' => $account_id, 'audit.job_id'=>$row->job_id, 'audit.asset_id'=>$row->asset_id ])
                            ->select('audit.audit_id, audit.start_time, audit.finish_time, audit.audit_status `completion_status`, audit_types.audit_type_id, audit_types.audit_type, audit.audit_result_status_id, audit_result_statuses.result_status', false)
                            ->join('audit_types', 'audit_types.audit_type_id = audit.audit_type_id', 'left')
                            ->join('audit_result_statuses', 'audit_result_statuses.audit_result_status_id = audit.audit_result_status_id', 'left')
                            ->get('audit')
                            ->row();

                        if (date('Y-m-d H:i:s') > date('Y-m-d H:i:s', strtotime($row->job_due_date))) {
                            $completion_status = 'Overdue';
                        }

                        $completion_status 		= !empty($audit_details->completion_status) ? $audit_details->completion_status : (!empty($completion_status) ? $completion_status : null);

                        $row->audit_id 			= !empty($audit_details->audit_id) ? $audit_details->audit_id : null;
                        $row->audit_type_id 	= !empty($audit_details->audit_type_id) ? $audit_details->audit_type_id : null;
                        $row->audit_type 		= !empty($audit_details->audit_type) ? $audit_details->audit_type : null;
                        $row->start_time 		= !empty($audit_details->start_time) ? $audit_details->start_time : null;
                        $row->finish_time 		= !empty($audit_details->finish_time) ? $audit_details->finish_time : null;
                        $row->completion_status = $completion_status;
                        $row->result_status 	= !empty($audit_details->result_status) ? $audit_details->result_status : null;

                        unset($completion_status);

                        if (!empty($grouped)) {
                            $data[$row->asset_type_id]['asset_type_id'] 		= $row->asset_type_id;
                            $data[$row->asset_type_id]['asset_type'] 			= $row->asset_type;
                            $data[$row->asset_type_id]['discipline_image_url'] 	= $row->discipline_image_url;
                            $data[$row->asset_type_id]['assets'][$k] 			= $row;
                        } else {
                            $data[$k] = $row;
                        }
                    }
                    $result = $data;
                    $this->session->set_flashdata('message', 'Job Assets data retrieved.');
                } else {
                    $this->session->set_flashdata('message', 'No data found.');
                    $result = false;
                }
            } else {
                $this->session->set_flashdata('message', 'Error! Missing required information.');
            }
        }

        return $result;
    }

    /**
    * Get Supervised Staff
    */
    public function _get_supervised_staff($account_id = false, $supervisor_id = false, $ids_only = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($supervisor_id)) {
            $query = $this->db->select('id, first_name, last_name, email, supervisor_id, is_supervisor', false)
                ->where('user.account_id', $account_id)
                ->where('user.supervisor_id', $supervisor_id)
                ->where('user.active', 1)
                ->order_by('user.first_name')
                ->get('user');

            if ($query->num_rows() > 0) {
                $result = !empty($ids_only) ? array_column($query->result_array(), 'id') : $query->result_array();
            }
        }
        return $result;
    }


    /** Unlink Job Assets **/
    public function unlink_job_assets($account_id = false, $job_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($job_id) && !empty($postdata)) {
            $postdata 	= convert_to_array($postdata);
            $job_assets	= !empty($postdata['job_assets']) ? $postdata['job_assets'] : $postdata;
            $job_assets	= (is_json($job_assets)) ? json_decode($job_assets) : $job_assets;
            $deleted	= [];

            if (!empty($job_assets)) {
                foreach ($job_assets as $k => $val) {
                    $data = [
                        'id'	=> $val,
                        'job_id'=> $job_id
                    ];

                    $check_exists = $this->db->limit(1)->get_where('job_assets', $data)->row();
                    if (!empty($check_exists)) {
                        $this->db->where($data);
                        $this->db->delete('job_assets');
                        $this->ssid_common->_reset_auto_increment('job_assets', 'id');
                    }
                    $deleted[] = $data;
                }
            } elseif (!empty($postdata['id'])) {
                $data = [
                    'id'	=> $postdata['id'],
                    'job_id'=> $job_id
                ];

                $check_exists = $this->db->limit(1)->get_where('job_assets', $data)->row();
                if (!empty($check_exists)) {
                    $this->db->where($data);
                    $this->db->delete('job_assets');
                    $deleted[] = $data;
                    $this->ssid_common->_reset_auto_increment('job_assets', 'id');
                }
            }

            if (!empty($deleted)) {
                $result = $deleted;
                $this->session->set_flashdata('message', 'Job Asset(s) unlinked successfully');
            } else {
                $this->session->set_flashdata('message', 'No required Job Assets were unlinked');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }
        return $result;
    }


    /**
    /* Delete/Archive a SCHEDULE resource
    */
    public function delete_schedule($account_id = false, $schedule_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($schedule_id)) {
            $conditions 	= [ 'account_id'=>$account_id,'schedule_id'=>$schedule_id ];
            $record_exists 	= $this->db->get_where('schedules', $conditions)->row();

            if (!empty($record_exists)) {
                ## Archive preexisting Jobs linked to this Schedule
                $this->db->where($conditions)->update('job', [
                    'archived' 		=> 1,
                    'archived_on'	=> date('Y-m-d H:i:s'),
                    'archived_by' 	=> $this->ion_auth->_current_user->id
                ]);

                ## Archive preexisting activities linked to this Schedule
                $this->db->where($conditions)->update('schedule_activities', [
                    'is_active' => 0
                ]);

                ## Then the parent
                $this->db->where($conditions)
                    ->update('schedules', [ 'schedule_ref'=> $record_exists->schedule_ref.'_ARCHIVED', 'is_active'=> 0 ]);

                if ($this->db->trans_status() !== false) {
                    $this->session->set_flashdata('message', 'Schedule profile deleted successfully.');
                    $result = true;
                }
            } else {
                $this->session->set_flashdata('message', 'Invalid Schedule ID.');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing the required information.');
        }
        return $result;
    }


    /** Create Multiple Schedule Activities -version 2 **/
    public function create_activity_containers_revised($account_id = false, $schedule_id = false, $activities_data = false, $data = false, $limit = SCHEDULE_CLONE_DEFAULT_LIMIT, $offset = 0)
    {
        $result = [];

        if (!empty($account_id) && !empty($schedule_id) && !empty($activities_data)) {
            $schedule = $this->db->select('schedules.frequency_id, schedules.contract_id, site.site_address_id `site_address_id`', false)
                ->join('site', 'site.site_id = schedules.site_id', 'left')
                ->where('schedules.schedule_id', $schedule_id)
                ->get('schedules')
                ->row();

            $address_id  			= !empty($data['address_id']) ? $data['address_id'] : (!empty($schedule->site_address_id) ? $schedule->site_address_id : false);
            $contract_id 			= !empty($data['contract_id']) ? $data['contract_id'] : (!empty($schedule->contract_id) ? $schedule->contract_id : false);
            $frequency_id 			= !empty($data['frequency_id']) ? $data['frequency_id'] : (!empty($schedule->frequency_id) ? $schedule->frequency_id : false);
            $asset_site_id 			= !empty($data['site_id']) ? $data['site_id'] : (!empty($schedule->site_id) ? $schedule->site_id : false);
            $activities_data		= convert_to_array($activities_data);

            $new_data = $existing_records 	= $all_activities = $activity_jobs = [];
            $multi_asset_type_activities 	= !empty($activities_data['schedule_activities']) ? $activities_data['schedule_activities'] : false;
            $multi_asset_type_activities	= (is_string($multi_asset_type_activities)) ? json_decode($multi_asset_type_activities) : $multi_asset_type_activities;
            $multi_asset_type_activities	= (is_object($multi_asset_type_activities)) ? object_to_array($multi_asset_type_activities) : $multi_asset_type_activities;

            if (!empty($multi_asset_type_activities)) {
                ini_set('memory_limit', '256');

                $activity_info = $processed_assets = [];
                foreach ($multi_asset_type_activities as $visit_id => $asset_type_activities) {
                    $selected_assets = [];
                    foreach ($asset_type_activities as $asset_id => $activity_data) {
                        $processed_assets[] 			= $asset_id;
                        $selected_assets[$visit_id][] 	= $asset_id;

                        if (!empty($activity_data['activity_name'])) {
                            $due_date 			= !empty($activity_data['due_date']) ? date('Y-m-d', strtotime($activity_data['due_date'])) : date('Y-m-d', strtotime(' + 7 days'));
                            $job_site_id 		= !empty($activity_data['site_id']) ? $activity_data['site_id'] : $asset_site_id;
                            $site_region		= $this->db->select('site_id, region_id')
                                ->get_where('site', [ 'site_id' => $job_site_id, 'account_id' => $account_id ])
                                ->row();

                            $activity_info[$visit_id] = [
                                'account_id' 	=> $account_id,
                                'job_type_id' 	=> !empty($activity_data['job_type_id']) ? $activity_data['job_type_id'] : false,
                                'activity_name' => $activity_data['activity_name'],
                                'schedule_id' 	=> $schedule_id,
                                'contract_id' 	=> !empty($contract_id) ? $contract_id : false,
                                'frequency_id' 	=> !empty($frequency_id) ? $frequency_id : false,
                                'address_id' 	=> $address_id,
                                'site_id' 		=> !empty($activity_data['site_id']) ? $activity_data['site_id'] : $asset_site_id,
                                'due_date' 		=> $due_date,
                                'job_due_date' 	=> !empty($activity_data['job_due_date']) ? date('Y-m-d', strtotime($activity_data['job_due_date'])) : $due_date,
                                'visit_number'	=> $visit_id
                            ];
                        }
                    }

                    $activity_info[$visit_id]['selected_assets'] = json_encode($selected_assets[$visit_id]);
                    $activity_info 		= $this->ssid_common->_filter_data('schedule_activities', $activity_info[$visit_id]);

                    $this->db->select('schedule_activities.activity_id', false)
                        ->where('schedule_activities.account_id', $account_id)
                        ->where([ 'activity_name'=>$activity_info['activity_name'], 'schedule_id'=>$schedule_id, 'job_type_id'=>$data['job_type_id'], 'due_date'=>$activity_info['due_date'] ])
                        ->limit(1);

                    if (!empty($activity_info['site_id'])) {
                        $this->db->where('schedule_activities.site_id', $activity_info['site_id']);
                    }

                    $check_exists = $this->db->get('schedule_activities')->row();

                    if (!empty($check_exists)) {
                        $activity_info['selected_assets']	= json_encode($selected_assets[$visit_id]);
                        $activity_info['activity_id'] 		= $check_exists->activity_id;
                        $activity_info['last_modified_by'] 	= $this->ion_auth->_current_user->id;
                        $this->db->where('activity_id', $check_exists->activity_id)
                            ->update('schedule_activities', $activity_info);
                        $existing_records[] 				= $activity_info;
                    } else {
                        $activity_info['status'] 		= 'Not due';
                        $activity_info['completion'] 	= 0;
                        $activity_info['created_by'] 	= $this->ion_auth->_current_user->id;
                        $this->db->insert('schedule_activities', $activity_info);
                        $activity_info['activity_id'] 	= $this->db->insert_id();
                        $new_records[] 					= $activity_info;
                    }

                    $all_activities[] = $activity_info['activity_id'];
                }

                if (!empty($all_activities)) {
                    $result['total_activities']	= $all_activities;
                    $result['total_assets'] 	= array_unique($processed_assets);
                    $this->session->set_flashdata('message', 'Activity record(s) processed successfully.');
                } else {
                    $this->session->set_flashdata('message', 'Unable to process your request. Please try again!');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }

        return (object)$result;
    }


    /*
    * Complete Schedule Processing (Revised)
    **/
    public function complete_schedule_processing_revised($account_id = false, $schedule_id = false, $params = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($schedule_id)) {
            $params = convert_to_array($params);
            $query 	= $this->db->select('schedule_activities.*, schedules.contract_id, schedule_frequencies.frequency_id, schedule_frequencies.frequency_group', false)
                ->join('schedules', 'schedule_activities.schedule_id = schedules.schedule_id', 'left')
                ->join('schedule_frequencies', 'schedules.frequency_id = schedule_frequencies.frequency_id', 'left')
                ->group_by('schedule_activities.activity_id')
                ->get_where('schedule_activities', [ 'schedule_activities.account_id' => $account_id, 'schedule_activities.schedule_id' => $schedule_id ]);

            if ($query->num_rows() > 0) {
                $frequency_group 	= '';
                $frequency_id 		= !empty($params['frequency_id']) ? $params['frequency_id'] : false;
                $contract_id 		= !empty($params['contract_id']) ? $params['contract_id'] : false;
                $total_assets 		= [];
                $total_jobs	= 0;
                foreach ($query->result() as $k => $row) {
                    $selected_assets= !empty($row->selected_assets) ? json_decode($row->selected_assets) : [];
                    $total_assets	= array_merge($total_assets, $selected_assets);
                    $frequency_group= $row->frequency_group;
                    $frequency_id 	= (!empty($frequency_id) && ($frequency_id == $row->frequency_id)) ? $frequency_id : $row->frequency_id;
                    $contract_id 	= (!empty($contract_id) && ($contract_id == $row->contract_id)) ? $contract_id : $row->contract_id;
                    unset($row->date_created, $row->created_by, $row->last_modified, $row->last_modified_by);
                    $job_data = $this->ssid_common->_filter_data('job', array_merge($params, (array)$row));

                    $job_data['due_date'] 		= date('Y-m-d', strtotime($job_data['job_due_date']));
                    $job_data['job_due_date'] 	= date('Y-m-d', strtotime($job_data['job_due_date']));
                    $job_data['status_id']		= 2;
                    $job_data['job_duration']	= 1;
                    $job_data['is_multi_asset']	= 1;

                    $condition 	= [
                        'account_id' => $account_id,
                        'due_date'	 =>	$job_data['due_date'],
                        'schedule_id'=>	$job_data['schedule_id'],
                        'job_type_id'=>	$job_data['job_type_id'],
                        'activity_id'=>	$job_data['activity_id']
                    ];

                    $check_exists = $this->db->select('job.job_id', false)->get_where('job', $condition)->row();

                    if (!empty($check_exists)) {
                        $job_id 					  = $check_exists->job_id;
                        $job_data['job_id'] 		  = $job_id;
                        $job_data['last_modified_by'] = $this->ion_auth->_current_user->id;
                        $this->db->where('job.job_id', $check_exists->job_id)->update('job', $job_data);
                        $processed_jobs[] = $check_exists->job_id;
                    } else {
                        $job_data['created_by'] = $this->ion_auth->_current_user->id;
                        $this->db->insert('job', $job_data);
                        $job_id					= $this->db->insert_id();
                        $processed_jobs[] 		= $job_id;
                        $job_data['job_id']		= $job_id;
                    }

                    $total_jobs++;
                    ## Link Job Assets
                    if (!empty($selected_assets)) {
                        $link_assets = $this->link_job_assets($account_id, $job_data, $selected_assets);
                    }
                }

                if (!empty($processed_jobs)) {
                    $result = [
                        'schedule_id' 	=> strval($schedule_id),
                        'contract_id' 	=> strval($contract_id),
                        'frequency_id' 	=> strval($frequency_id),
                        'jobs' 			=> strval($total_jobs),
                        'assets' 		=> !empty($total_assets) ? strval(count(array_unique($total_assets))) : '0'
                    ];
                    $this->session->set_flashdata('message', 'Schedule process completed Successfully.');
                } else {
                    $this->session->set_flashdata('message', 'Unable to complete the Schedule process, please re-start the processing!');
                }
            } else {
                $this->session->set_flashdata('message', 'Schedule activities have not been created yet. Request aborted!');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }

        return $result;
    }
}
