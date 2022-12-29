<?php

namespace Application\Modules\Service\Models;

class People_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $section 	   = explode("/", $_SERVER["SCRIPT_NAME"]);
        $this->app_root= $_SERVER["DOCUMENT_ROOT"]."/".$section[1]."/";
        $this->app_root= str_replace('/index.php', '', $this->app_root);
        $this->load->library('upload');
        #$this->load->model( 'Diary_model','diary_service' );
    }

    /** Searchable fields **/
    private $searchable_fields  = ['people.person_id', 'people.user_id', 'people.status_id', 'people.preferred_name', 'people.department_id', 'people.job_title_id',  'user.first_name',  'user.last_name'];

    /** Primary table name **/
    private $primary_tbl = 'people';

    /*
    * Get Person single records or multiple records
    */
    public function get_people($account_id=false, $user_id = false, $person_id = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset=DEFAULT_OFFSET)
    {
        $result = false;
        $this->db->select('people.*, people_categories.*, user.account_user_id, user.email, user.phone, user.first_name, user.last_name, user_statuses.status, people_job_titles.job_title, people_departments.department_name, countries.country_name `nationality`', false)
            ->join('people_departments', 'people_departments.department_id = people.department_id', 'left')
            ->join('people_job_titles', 'people_job_titles.job_title_id = people.job_title_id', 'left')
            ->join('people_categories', 'people_categories.category_id = people.category_id', 'left')
            ->join('user_statuses', 'user_statuses.status_id = people.status_id', 'left')
            ->join('user', 'user.id = people.user_id')
            ->join('countries', 'countries.country_id = people.nationality_id', 'left')
            ->where('people.account_id', $account_id)
            ->where('people.is_active =', 1);

        if ($user_id || $person_id) {
            $condition = (!empty($person_id)) ? ['people.person_id'=>$person_id] : ((!empty($user_id)) ? ['people.user_id'=>$user_id] : []);

            $row = $this->db->get_where('people', $condition)->row();

            if (!empty($row)) {
                $row->personal_skills 	 = $this->get_personal_skills($account_id, $row->person_id);
                $row->assigned_regions = $this->get_assigned_regions($account_id, $row->person_id);
                $this->session->set_flashdata('message', 'Personal record found');
                $result = ( object )['records' => $row];
            } else {
                $this->session->set_flashdata('message', 'Personal record not found');
            }
            return $result;
        }

        if (!empty($where)) {
            $where = convert_to_array($where);

            if (isset($where['personal_email'])) {
                if (!empty($where['personal_email'])) {
                    $this->db->where('people.personal_email', trim($where['personal_email']));
                }
                unset($where['personal_email']);
            }

            if (isset($where['status_id'])) {
                if (!empty($where['status_id'])) {
                    $departments = (((int)$where['status_id']) > 0) ? [$where['status_id']] : ((!is_array($where['status_id'])) ? json_decode($where['status_id']) : $where['status_id']);
                    $this->db->where_in('people.status_id', $departments);
                }
                unset($where['department_id']);
            }

            if (isset($where['department_id'])) {
                if (!empty($where['department_id'])) {
                    $departments = (((int)$where['department_id']) > 0) ? [$where['department_id']] : ((!is_array($where['department_id'])) ? json_decode($where['department_id']) : $where['department_id']);
                    $this->db->where_in('people.department_id', $departments);
                }
                unset($where['department_id']);
            }

            if (isset($where['job_title_id'])) {
                if (!empty($where['job_title_id'])) {
                    $departments = (((int)$where['job_title_id']) > 0) ? [$where['job_title_id']] : ((!is_array($where['job_title_id'])) ? json_decode($where['job_title_id']) : $where['job_title_id']);
                    $this->db->where_in('people.job_title_id', $departments);
                }
                unset($where['job_title_id']);
            }

            if (!empty($where)) {
                $this->db->where($where);
            }
        }

        if ($order_by) {
            $order = $this->ssid_common->_clean_order_by($order_by, $this->primary_tbl);
            if (!empty($order)) {
                $this->db->order_by($order);
            }
        } else {
            $this->db->order_by('people.person_id');
        }

        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }

        $people = $this->db->get('people');

        if ($people->num_rows() > 0) {
            $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
            $result->records 			= $people->result();
            $counters 					= $this->get_total_people($account_id, false, $where);
            $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
            $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
            $result->counters->limit  	= $limit;
            $result->counters->offset 	= $offset;
            $this->session->set_flashdata('message', 'People records found');
        } else {
            $this->session->set_flashdata('message', 'People record(s) not found');
        }
        return $result;
    }

    /*
    * Create new Person
    */
    public function create_person($account_id=false, $person_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($person_data)) {
            $data = [];
            foreach ($person_data as $key=>$value) {
                if (in_array($key, format_date_columns()) && !empty($value)) {
                    if (!empty($value)) {
                        $data[$key] = format_datetime_db($value);
                    }
                } else {
                    $data[$key] = (!is_array($value)) ? trim($value) : $value;
                }
            }

            if (!empty($data['user_id']) || !empty($data['person_id'])) {
                # Verify that this is a user that already exists and belows to the account of the current user
                $user_id 	 = (!empty($data['user_id'])) ? $data['user_id'] : (!empty($data['person_id']) ? $data['person_id'] : false);
                $verify_user = $this->ion_auth->get_user_by_id($account_id, $user_id);

                if (!empty($verify_user)) {
                    $where = "( people.user_id = '".$user_id."' OR people.person_id = '".$user_id."' )";
                    $person_exists = $this->db->where($where)
                        ->where('people.account_id', $account_id)
                        ->get('people')->row();

                    if (!$person_exists) {
                        $new_person 			 	 = $this->ssid_common->_filter_data('people', $data);
                        $new_person['person_id'] 	 = $user_id;
                        $new_person['personal_email']= (!empty($new_person['personal_email'])) ? $new_person['personal_email'] : $verify_user->email;
                        $new_person['created_by']	 = (!empty($this->ion_auth->_current_user->id)) ? $this->ion_auth->_current_user->id : null;

                        $this->db->insert('people', $new_person);

                        if ($this->db->trans_status() !== false) {
                            ## Create a position log
                            if (!empty($data['job_title_id'])) {
                                $data['person_id'] 		= $user_id;
                                $data['job_start_date'] = !empty($data['start_date']) ? date('Y-m-d', strtotime($data['start_date'])) : date('Y-m-d');
                                $this->create_position_log($account_id, $user_id, $data);
                            }

                            $result = $this->get_people($account_id, $user_id, $user_id);
                            $this->session->set_flashdata('message', 'Person record created successfully.');
                        }
                    } else {
                        $new_person 			 	 	= $this->ssid_common->_filter_data('people', $data);
                        $new_person['last_modified_by'] = $this->ion_auth->_current_user->id;

                        $this->db->where('people.account_id', $account_id)
                            ->where("( people.user_id = '".$user_id."' OR people.person_id = '".$user_id."' )")
                            ->update('people', $new_person);
                        if ($this->db->trans_status() !== false) {
                            $result = $this->get_people($account_id, $user_id, $user_id);
                            $this->session->set_flashdata('message', 'Person record already exists, details updated successfully.');
                        }
                    }
                } else {
                    $this->session->set_flashdata('message', 'Illegal operation. This user resource does not exist or does not below to you!');
                    return false;
                }
            } else {
                $user_id = $this->_create_user_from_person_data($account_id, $data);

                if (!empty($user_id)) {
                    $data['user_id'] = $user_id;
                    //Continue with creating a person record
                    $result = $this->create_person($account_id, $data);
                } else {
                    $this->session->set_flashdata('message', $this->session->flashdata('message'));
                    return false;
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Person data supplied.');
        }
        return $result;
    }

    /*
    * Update Person record
    */
    public function update_person($account_id = false, $person_id = false, $person_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($person_id) && !empty($person_data)) {
            $data = $position_data = [];

            if (!empty($person_data['position'])) {
                $position_data 	= (!is_array($person_data['position'])) ? json_decode($person_data['position']) : $person_data['position'];
                unset($person_data['position']);
            }

            foreach ($person_data as $key=>$value) {
                if (in_array($key, format_date_columns())) {
                    $value = format_datetime_db($value);
                } else {
                    $value = (!is_array($value)) ? trim($value) : $value;
                }
                $data[$key] = $value;
            }

            if (!empty($data)) {
                #Check if this person already before we attempt to update them
                $conditions = [ 'account_id'=>$account_id, 'person_id'=>$person_id ];
                $query = $this->db->get_where('people', $conditions);

                if ($query->num_rows() > 0) {
                    $datab4update = $query->result()[0];

                    $user_record  = $this->ion_auth->get_user_by_id($account_id, $person_id);

                    $update_user_data = [];

                    if (!empty($data['first_name'])  && (strtolower($data['first_name']) != strtolower($user_record->first_name))) {
                        $update_user_data['first_name'] = $data['first_name'];
                    }

                    if (!empty($data['last_name'])  && (strtolower($data['last_name']) != strtolower($user_record->last_name))) {
                        $update_user_data['last_name'] = $data['last_name'];
                    }

                    if (!empty($update_user_data)) {
                        $this->db->where(['account_id'=>$account_id, 'id'=>$person_id])->update('user', $update_user_data);
                    }

                    $update_data = $this->ssid_common->_filter_data('people', $data);
                    $update_data['last_modified_by'] = $this->ion_auth->_current_user->id;
                    $this->db->where($conditions);
                    $this->db->update('people', $update_data);

                    if ($this->db->trans_status() !== false) {
                        ## Add position log, only if the Job title has changed
                        if ((!empty($data['job_title_id'])) && ($data['job_title_id'] != $datab4update->job_title_id)) {
                            $position_data = is_object($position_data) ? object_to_array($position_data) : $position_data;
                            $data = array_merge($data, $position_data);
                            $this->create_position_log($account_id, $person_id, $data);
                        }

                        $result = $this->get_people($account_id, $person_id);
                        $this->session->set_flashdata('message', 'Personal data updated successfully');
                    }
                } else {
                    $this->session->set_flashdata('message', 'Illegal operation. Access denied');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Personal data supplied');
        }
        return $result;
    }

    /*
    * Delete Person record
    */
    public function delete_person($account_id = false, $person_id = false)
    {
        $result = false;
        if ($this->account_service->check_account_status($account_id) && !empty($person_id)) {
            $conditions 	= ['account_id'=>$account_id,'person_id'=>$person_id];
            $person_exists 	= $this->db->get_where('people', $conditions)->row();
            if (!empty($person_exists)) {
                $data = [
                    'archived'=>1,
                    'last_modified_by'=>$this->ion_auth->_current_user()->id
                ];
                $this->db->where($conditions)->update('people', $data);
                if ($this->db->trans_status() !== false) {
                    $this->session->set_flashdata('message', 'Record arhived successfully.');
                    $result = true;
                }
            } else {
                $this->session->set_flashdata('message', 'Invalid Person ID.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Person record found.');
        }
        return $result;
    }

    /** Get Person types **/
    public function get_departments($account_id = false, $department_id = false, $department_group = false, $grouped = false)
    {
        $result = null;
        if ($account_id) {
            $this->db->where('people_departments.account_id', $account_id);

            if ($department_group) {
                $this->db->where('people_departments.department_group', $department_group);
            }
        } else {
            $this->db->where('( people_departments.account_id IS NULL OR people_departments.account_id = "" )');
        }

        if (!empty($department_id)) {
            $this->db->where('people_departments.department_id', $department_id);
        }

        $query = $this->db->select('people_departments.*', false)
            ->where('people_departments.is_active', 1)
            ->get('people_departments');

        if ($query->num_rows() > 0) {
            $result = $query->result();
        } else {
            #$result = $this->get_departments();
        }

        #Grouped result
        if (!empty($grouped)) {
            $data = [];
            foreach ($result as $k => $row) {
                $data[$row->department_group][] = $row;
            }
            $result = $data;
        }

        return $result;
    }


    /** Get Job Titles **/
    public function get_job_titles($account_id = false, $job_title_id = false, $job_area = false, $job_level = false, $group_by = false)
    {
        $result = null;
        if ($account_id) {
            $this->db->where('people_job_titles.account_id', $account_id);

            if ($job_area) {
                $this->db->where('people_job_titles.job_area', $job_area);
            }

            if ($job_level) {
                $this->db->where('people_job_titles.job_level', $job_level);
            }
        } else {
            $this->db->where('( people_job_titles.account_id IS NULL OR people_job_titles.account_id = "" )');
        }

        if (!empty($job_title_id)) {
            $this->db->where('people_job_titles.job_title_id', $job_title_id);
        }

        $query = $this->db->select('people_job_titles.*', false)
            ->where('people_job_titles.is_active', 1)
            ->get('people_job_titles');

        if ($query->num_rows() > 0) {
            $result = $query->result();
        } else {
            #$result = $this->get_job_titles();
        }

        #Grouped result
        if (!empty($group_by)) {
            $data = [];
            foreach ($result as $k => $row) {
                if ((!is_int($group_by)) && (!empty($row->{$group_by}))) {
                    $data[$row->{$group_by}][] = $row;
                } else {
                    $data[$row->job_area][] = $row;
                }
            }
            $result = $data;
        }

        return $result;
    }

    /*
    * Search through people
    */
    public function people_lookup($account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('people.*, people_categories.*, user.account_user_id, user.first_name, user.last_name, user_statuses.status, people_job_titles.job_title, people_departments.department_name', false)
            ->join('people_departments', 'people_departments.department_id = people.department_id', 'left')
            ->join('people_job_titles', 'people_job_titles.job_title_id = people.job_title_id', 'left')
            ->join('people_categories', 'people_categories.category_id = people.category_id', 'left')
            ->join('user_statuses', 'user_statuses.status_id = people.status_id', 'left')
            ->join('user', 'user.id = people.user_id')
            ->where('people.account_id', $account_id)
            ->where('people.is_active =', 1);

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

                        if (!empty($search_where['people.status_id'])) {
                            $search_where['user_statuses.status'] =  trim($term);
                            unset($search_where['people.status_id']);
                        }

                        if (!empty($search_where['people.preferred_email'])) {
                            $search_where['user.email'] =  trim($term);
                            $search_where['people.preferred_email'] =  trim($term);
                        }

                        if (!empty($search_where['people.department_id'])) {
                            $search_where['people_departments.department_name'] =  trim($term);
                            unset($search_where['people.department_id']);
                        }

                        if (!empty($search_where['people.job_title_id'])) {
                            $search_where['people_job_titles.job_title'] 	=  trim($term);
                            $search_where['people_job_titles.job_specialty']=  trim($term);
                            $search_where['people_job_titles.job_level'] 	=  trim($term);
                            unset($search_where['people.job_title_id']);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->searchable_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    if (!empty($search_where['people.status_id'])) {
                        $search_where['user_statuses.status'] =  trim($search_term);
                        unset($search_where['people.status_id']);
                    }

                    if (!empty($search_where['people.preferred_name'])) {
                        $search_where['user.email'] =  $search_term;
                    }

                    if (!empty($search_where['people.department_id'])) {
                        $search_where['people_departments.department_name'] =  $search_term;
                        unset($search_where['people.department_id']);
                    }

                    if (!empty($search_where['people.job_title_id'])) {
                        $search_where['people_job_titles.job_title'] 	=  $search_term;
                        $search_where['people_job_titles.job_specialty']=  $search_term;
                        $search_where['people_job_titles.job_level'] 	=  $search_term;
                        unset($search_where['people.job_title_id']);
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                $where = convert_to_array($where);

                if (isset($where['personal_email'])) {
                    if (!empty($where['personal_email'])) {
                        $this->db->where('people.personal_email', trim($where['personal_email']));
                    }
                    unset($where['personal_email']);
                }

                if (isset($where['status_id'])) {
                    if (!empty($where['status_id'])) {
                        $status_ids = (!is_array($where['status_id']) && ((int) $where['status_id'] > 0)) ? [ $where['status_id'] ] : ((is_array($where['status_id'])) ? $where['status_id'] : (is_object($where['status_id']) ? object_to_array($where['status_id']) : []));
                        $this->db->where_in('people.status_id', $status_ids);
                    }
                    unset($where['status_id']);
                }

                if (isset($where['department_id'])) {
                    if (!empty($where['department_id'])) {
                        $department_ids = (!is_array($where['department_id']) && ((int) $where['department_id'] > 0)) ? [ $where['department_id'] ] : ((is_array($where['department_id'])) ? $where['department_id'] : (is_object($where['department_id']) ? object_to_array($where['department_id']) : []));
                        $this->db->where_in('people.department_id', $department_ids);
                    }
                    unset($where['department_id']);
                }

                if (isset($where['job_title_id'])) {
                    if (!empty($where['job_title_id'])) {
                        $job_title_ids = (!is_array($where['job_title_id']) && ((int) $where['job_title_id'] > 0)) ? [ $where['job_title_id'] ] : ((is_array($where['job_title_id'])) ? $where['job_title_id'] : (is_object($where['job_title_id']) ? object_to_array($where['job_title_id']) : []));
                        $this->db->where_in('people.job_title_id', $job_title_ids);
                    }
                    unset($where['job_title_id']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            if ($order_by) {
                $order = $this->ssid_common->_clean_order_by($order_by, $this->primary_tbl);
                if (!empty($order)) {
                    $this->db->order_by($order);
                }
            } else {
                $this->db->order_by('user.first_name, people.preferred_name');
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->get('people');

            if ($query->num_rows() > 0) {
                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $result->records 			= $query->result();
                $counters 					= $this->get_total_people($account_id, $search_term, $where, $limit);
                $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
                $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
                $result->counters->limit  	= $limit;
                $result->counters->offset 	= $offset;
                $this->session->set_flashdata('message', 'Records found.');
            } else {
                $this->session->set_flashdata('message', 'No records found matching your criteria.');
            }
        }

        return $result;
    }

    /*
    * Get total people count for the search
    */
    public function get_total_people($account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('people.person_id', false)
                ->join('people_departments', 'people_departments.department_id = people.department_id', 'left')
                ->join('people_job_titles', 'people_job_titles.job_title_id = people.job_title_id', 'left')
                ->join('user_statuses', 'user_statuses.status_id = people.status_id', 'left')
                ->join('user', 'user.id = people.user_id')
                ->where('people.account_id', $account_id)
                ->where('people.is_active =', 1);

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

                        if (!empty($search_where['people.status_id'])) {
                            $search_where['user_statuses.status'] =  trim($term);
                            unset($search_where['people.status_id']);
                        }

                        if (!empty($search_where['people.preferred_name'])) {
                            $search_where['user.email'] =  trim($term);
                        }

                        if (!empty($search_where['people.department_id'])) {
                            $search_where['people_departments.department_name'] =  trim($term);
                            unset($search_where['people.department_id']);
                        }

                        if (!empty($search_where['people.job_title_id'])) {
                            $search_where['people_job_titles.job_title'] 	=  trim($term);
                            $search_where['people_job_titles.job_specialty']=  trim($term);
                            $search_where['people_job_titles.job_level'] 	=  trim($term);
                            unset($search_where['people.job_title_id']);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->searchable_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    if (!empty($search_where['people.status_id'])) {
                        $search_where['user_statuses.status'] =  trim($search_term);
                        unset($search_where['people.status_id']);
                    }

                    if (!empty($search_where['people.preferred_name'])) {
                        $search_where['user.email'] =  $search_term;
                    }

                    if (!empty($search_where['people.department_id'])) {
                        $search_where['people_departments.department_name'] =  $search_term;
                        unset($search_where['people.department_id']);
                    }

                    if (!empty($search_where['people.job_title_id'])) {
                        $search_where['people_job_titles.job_title'] 	=  $search_term;
                        $search_where['people_job_titles.job_specialty']=  $search_term;
                        $search_where['people_job_titles.job_level'] 	=  $search_term;
                        unset($search_where['people.job_title_id']);
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                $where = convert_to_array($where);

                if (isset($where['personal_email'])) {
                    if (!empty($where['personal_email'])) {
                        $this->db->where('people.personal_email', trim($where['personal_email']));
                    }
                    unset($where['personal_email']);
                }

                if (isset($where['status_id'])) {
                    if (!empty($where['status_id'])) {
                        $status_ids = (((int)$where['status_id']) > 0) ? [$where['status_id']] : ((!is_array($where['status_id'])) ? json_decode($where['status_id']) : $where['status_id']);
                        $this->db->where_in('people.status_id', $status_ids);
                    }
                    unset($where['department_id']);
                }

                if (isset($where['department_id'])) {
                    if (!empty($where['department_id'])) {
                        $departments = (((int)$where['department_id']) > 0) ? [$where['department_id']] : ((!is_array($where['department_id'])) ? json_decode($where['department_id']) : $where['department_id']);
                        $this->db->where_in('people.department_id', $departments);
                    }
                    unset($where['department_id']);
                }

                if (isset($where['job_title_id'])) {
                    if (!empty($where['job_title_id'])) {
                        $departments = (((int)$where['job_title_id']) > 0) ? [$where['job_title_id']] : ((!is_array($where['job_title_id'])) ? json_decode($where['job_title_id']) : $where['job_title_id']);
                        $this->db->where_in('people.job_title_id', $departments);
                    }
                    unset($where['job_title_id']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $query = $this->db->from('people')->count_all_results();
            $results['total'] = !empty($query) ? $query : 0;
            $limit 			  = (!empty($limit > 0)) ? $limit : $results['total'];
            $results['pages'] = !empty($query) ? ceil($query / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }

    /** Create a new user from the submitted Person / HR data **/
    private function _create_user_from_person_data($account_id = false, $user_data = false)
    {
        $result = null;
        if (!empty($account_id) && !empty($user_data['personal_email']) && !empty($user_data['first_name']) && !empty($user_data['last_name'])) {
            //Check if user exists with this email address
            $user_exists = $this->db->select('user.id')
                ->get_where('user', ['email'=>trim($user_data['personal_email'])])
                ->row();

            if (!empty($user_exists)) {
                $this->session->set_flashdata('message', 'User already exists with this email address! Returning user record.');
                return $user_exists->id;
            }

            //call create user function
            $user_data['email'] = $user_data['personal_email'];
            $user_id 			= $this->ion_auth->register($user_data['email'], DEFAULT_PASSWORD, $user_data['email'], $user_data);
            if (!empty($user_id)) {
                //Create default permissions
                $this->_assign_default_permissions($account_id, $user_id);

                $result = $user_id;
            } else {
                $message = ($this->ion_auth->errors()) ? 'Email address '.$this->ion_auth->errors() : 'Something went wrong while trying to create a user resource!';
                $this->session->set_flashdata('message', $message);
            }
        } else {
            $this->session->set_flashdata('message', 'Email address, first and last names are all required fields to create a new person resource!');
        }
        return $result;
    }

    /** Create Position log **/
    public function create_position_log($account_id=false, $person_id=false, $data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($person_id) && !empty($data)) {
            $data['created_by'] 	= $this->ion_auth->_current_user->id;
            $data['job_start_date'] = (!empty($data['job_start_date'])) ? date('Y-m-d', strtotime($data['job_start_date'])) : date('Y-m-d');
            $data['job_end_date'] 	= (!empty($data['job_end_date'])) ? date('Y-m-d', strtotime($data['job_end_date'])) : null;

            if (!empty($data['position_type']) && (strtolower($data['position_type']) == 'permanent')) {
                //Only do this operation if the new position is permanent
                $last_position = $this->db->limit(1)
                    ->where('person_id', $person_id)
                    ->order_by('position_id desc')
                    ->get('people_job_positions')
                    ->row();

                if (!empty($last_position)) {
                    $this->db->where('position_id', $last_position->position_id)
                        ->update('people_job_positions', ['job_end_date'=>date('Y-m-d'), 'last_modified_by'=>$this->ion_auth->_current_user->id]);
                }
            }

            $position_data = $this->ssid_common->_filter_data('people_job_positions', $data);
            $this->db->insert('people_job_positions', $position_data);
            $result = true;
        }
        return $result;
    }

    /** Get position for a particular person or account **/
    public function get_job_positions($account_id=false, $person_id=false, $position_id=false, $job_title_id=false, $job_start_date=false, $job_end_date=false, $limit = DEFAULT_LIMIT, $offset = 0)
    {
        $result = null;
        if (!empty($account_id)) {
            $this->db->select('positions.*, jt.job_title, jt.job_level, jt.job_area, people_departments.department_name, concat(user.first_name," ",user.last_name) `created_by`, concat(modifier.first_name," ",modifier.last_name) `modified_by`', false)
                ->where('positions.account_id', $account_id);

            if ($person_id) {
                $this->db->where('positions.person_id', $person_id);
            }

            if ($position_id) {
                $this->db->where('positions.position_id', $position_id);
            }

            if ($job_title_id) {
                $this->db->where('positions.job_title_id', $job_title_id);
            }

            if (!empty($job_start_date) && !empty($job_end_date)) {
                $this->db->where('positions.job_start_date >=', date('Y-m-d', strtotime($job_start_date)));
                $this->db->where('positions.job_end_date <=', date('Y-m-d', strtotime($job_end_date)));
            } else {
                if ($job_start_date) {
                    $this->db->where('positions.job_start_date', date('Y-m-d', strtotime($job_start_date)));
                }

                if ($job_end_date) {
                    $this->db->where('positions.job_end_date', date('Y-m-d', strtotime($job_end_date)));
                }
            }

            $this->db->limit($limit, $offset);

            $query = $this->db->join('user', 'user.id = positions.created_by', 'left')
                ->join('people_job_titles jt', 'jt.job_title_id = positions.job_title_id', 'left')
                ->join('people_departments', 'people_departments.department_id = positions.department_id', 'left')
                ->join('user modifier', 'modifier.id = positions.last_modified_by', 'left')
                ->order_by('positions.position_id desc')
                ->get('people_job_positions positions');
            if ($query->num_rows() > 0) {
                $this->session->set_flashdata('message', 'Position data found');
                $result = $query->result();
            } else {
                $this->session->set_flashdata('message', 'Position data not found');
            }
        } else {
            $this->session->set_flashdata('message', 'No parameters supplied for request');
        }
        return $result;
    }

    /** Create a persons contact record **/
    public function create_contact($account_id = false, $person_id = false, $contact_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($person_id)  && !empty($contact_data)) {
            $data = [];
            foreach ($contact_data as $key=>$value) {
                $data[$key] = (!is_array($value)) ? trim($value) : $value;
            }
            if (!empty($data)) {
                $new_contact = $this->ssid_common->_filter_data('people_contact_addresses', $data);
                $new_contact['created_by'] = $this->ion_auth->_current_user->id;
                $this->db->insert('people_contact_addresses', $new_contact);
                if ($this->db->trans_status() !== false) {
                    $contact_id = $this->db->insert_id();
                    $result = $this->get_address_contacts($account_id, false, $contact_id);
                    $this->session->set_flashdata('message', 'Address Contact added successfully');
                }
            } else {
                $this->session->set_flashdata('message', 'An error occurred while adding an address contact!');
            }
        } else {
            $this->session->set_flashdata('message', 'Required parameters not supplied!');
        }
        return $result;
    }

    /** Get list of all contacts attached to a person **/
    public function get_address_contacts($account_id=false, $person_id=false, $contact_id=false, $address_type_id=false, $limit = DEFAULT_LIMIT, $offset = 0)
    {
        $result = null;
        if (!empty($account_id)) {
            $this->db->select('people_contact_addresses.*, address_types.address_type, concat(user.first_name," ",user.last_name) `created_by`, concat(modifier.first_name," ",modifier.last_name) `modified_by`', false)
                ->where('people_contact_addresses.account_id', $account_id)
                ->join('user', 'user.id = people_contact_addresses.created_by', 'left')
                ->join('user modifier', 'modifier.id = people_contact_addresses.last_modified_by', 'left')
                ->join('address_types', 'address_types.address_type_id = people_contact_addresses.address_type_id', 'left');

            $arch_where = "( people_contact_addresses.archived != 1 or people_contact_addresses.archived is NULL )";
            $this->db->where($arch_where);

            if ($contact_id) {
                $row = $this->db->get_where('people_contact_addresses', ['contact_id'=>$contact_id])->row();
                if (!empty($row)) {
                    $this->session->set_flashdata('message', 'Contact details record found');
                    $result = $row;
                } else {
                    $this->session->set_flashdata('message', 'Contact details not found');
                }
                return $result;
            }

            if ($person_id) {
                $this->db->where('people_contact_addresses.person_id', $person_id);
            }

            $query = $this->db->limit($limit, $offset)
                ->order_by('people_contact_addresses.contact_first_name')
                ->get('people_contact_addresses');
            if ($query->num_rows() > 0) {
                $this->session->set_flashdata('message', 'Contacts data found');
                $result = $query->result();
            } else {
                $this->session->set_flashdata('message', 'Contacts data not found');
            }
        } else {
            $this->session->set_flashdata('message', 'No parameters supplied for request');
        }
        return $result;
    }

    /** Search for user pre-creations of people records **/
    public function find_user_records($account_id = false, $search_term = false)
    {
        $result = null;
        if (!empty($account_id) && !empty($search_term)) {
            $found_users = [];
            $users = $this->ion_auth->user_lookup($account_id, $search_term);
            if (!empty($users)) {
                foreach ($users as $user) {
                    $perosn_exists = $this->get_people($account_id, $user->id);
                    if (!empty($perosn_exists)) {
                        $found_users['exists'][] 			= $user;
                    } else {
                        $found_users['person_not_found'][]  = $user;
                    }
                }
                $result = $found_users;
            }
        } else {
            $this->session->set_flashdata('message', 'No parameters supplied for request');
        }
        return $result;
    }

    /** Process People Upload **/
    public function upload_people($account_id = false)
    {
        $result = null;
        if (!empty($account_id)) {
            $uploaddir  = $this->app_root. 'assets' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;

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
                $record['user_type_id'] = (!empty($record['user_type_id'])) ? $record['user_type_id'] : 2;
                $record['is_active'] 	= 1;
                $check_exists = $this->db->where(['account_id'=>$account_id, 'personal_email'=>$record['personal_email'] ])
                    ->limit(1)
                    ->get('people_tmp_upload')
                    ->row();
                if (!empty($check_exists)) {
                    $exists[] 	= $this->ssid_common->_filter_data('people_tmp_upload', $record);
                } else {
                    $new[]  	= $this->ssid_common->_filter_data('people_tmp_upload', $record);
                }
            }

            //Updated existing
            if (!empty($exists)) {
                $this->db->update_batch('people_tmp_upload', $exists, 'personal_email');
            }

            //Insert new records
            if (!empty($new)) {
                $this->db->insert_batch('people_tmp_upload', $new);
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
                ->order_by('personal_email')
                ->get('people_tmp_upload');

            if ($query->num_rows() > 0) {
                $data = [];
                foreach ($query->result() as $k => $row) {
                    $check = $this->db->select('user.id, people.person_id')
                        ->join('people', 'people.user_id = user.id', 'left')
                        ->where('user.account_id', $account_id)
                        ->where('user.email', $row->personal_email)
                        ->limit(1)
                        ->get('user')
                        ->row();
                    if (!empty($check->person_id)) {
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
    * Update Person record
    */
    public function update_temp_data($account_id = false, $temp_user_id = false, $temp_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($temp_user_id) && !empty($temp_data)) {
            $data  = [];
            $where = [
                'account_id'=>$account_id,
                'temp_user_id'=>$temp_user_id
            ];

            foreach ($temp_data as $key => $value) {
                $data[$key] = trim($value);
            }

            $update_data = array_merge($data, $where);
            $this->db->where($where)
                ->update('people_tmp_upload', $update_data);

            $result = ($this->db->trans_status() !== 'false') ? true : false;
        }
        return $result;
    }

    /** Create People **/
    public function create_people($account_id = false, $postdata = false)
    {
        $result = null;
        if (!empty($account_id) && !empty($postdata['people'])) {
            $to_delete = $processed = [];
            foreach ($postdata['people'] as $temp_user_id => $update_record) {
                #get temp data
                if (!empty($update_record['checked'])) {
                    $get_temp_record = (array) $this->db->get_where('people_tmp_upload', [ 'temp_user_id'=>$temp_user_id ])->row();
                    $user_id = $this->_create_user_from_person_data($account_id, $get_temp_record);

                    if (!empty($user_id)) {
                        $get_temp_record['user_id']   = $user_id;
                        $get_temp_record['person_id'] = $user_id;
                        //Continue with creating a person record
                        $new_person = $this->create_person($account_id, $get_temp_record);

                        if (!empty($new_person)) {
                            $processed[] = $new_person;
                            $to_delete[$temp_user_id] = $temp_user_id;
                        } else {
                            $user_failed[] = $get_temp_record;
                        }
                    } else {
                        $user_failed[] = $get_temp_record;
                    }
                }
            }

            if (!empty($processed)) {
                $result = $processed;
                //Delete processed records
                if (!empty($to_delete)) {
                    $this->db->where_in('temp_user_id', $to_delete)
                        ->delete('people_tmp_upload');

                    $this->ssid_common->_reset_auto_increment('people_tmp_upload', 'temp_user_id'); //House keeping
                }
                $this->session->set_flashdata('message', 'People records added successfully.');
            }
        }
        return $result;
    }

    /** Assign default module access and permissions **/
    private function _assign_default_permissions($account_id = false, $user_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($user_id)) {
            //At this time simply copy the main account holder's permission #2
            $module_access = $this->db->select('user_id, module_id, account_id, has_access, is_module_admin', false)
                ->where([ 'account_id'=>$account_id, 'user_id'=> 2 ])
                ->get('user_module_access');

            if ($module_access->num_rows() > 0) {
                $mod_array 		= [];
                $mod_item_perms = [];
                foreach ($module_access->result_array() as $k =>$record) {
                    $record['user_id'] = $user_id;
                    $mod_array[$k] = $record;

                    ## Get module item perms
                    $module_items = $this->db->select('user_id, account_id, module_id, module_item_id, item_permissions', false)
                        ->where([ 'account_id'=>$account_id, 'user_id'=> 2 ])
                        ->get('user_module_item_permissions');

                    foreach ($module_items->result_array() as $key =>$perm) {
                        $perm['user_id'] 	= $user_id;
                        $mod_item_perms[$key] = $perm;
                    }
                }

                //Batch insert
                if (!empty($mod_array)) {
                    $this->db->insert_batch('user_module_access', $mod_array);

                    if (($this->db->trans_status() !== false) && !empty($mod_item_perms)) {
                        $this->db->insert_batch('user_module_item_permissions', $mod_item_perms);
                    }
                }
                $result = ($this->db->trans_status() !== false) ? true : false;
            }
        }
        return $result;
    }


    public function get_people_category($account_id = false, $category_id = false, $ordered = false)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->where("people_categories.account_id", $account_id);
        } else {
            $this->db->where("people_categories.account_id is NULL OR people_categories.account_id = ''");
        }

        if (!empty($category_id)) {
            $this->db->where("category_id", $category_id);
        }

        $this->db->where("is_active", 1);
        $query = $this->db->get("people_categories");

        if (!empty($query->num_rows()) && $query->num_rows() > 0) {
            $result_set = $query->result();
            if (!empty($ordered)) {
                foreach ($result_set as $key => $row) {
                    $result[$row->category_id] = $row;
                }
            } else {
                $result = $result_set;
            }
            $this->session->set_flashdata("message", "People category(ies) found;");
        } else {
            $this->get_people_category(false, $category_id, $ordered);
        }

        if (!empty($result)) {
            $this->session->set_flashdata("message", "People category(ies) found;");
        } else {
            $this->session->set_flashdata("message", "People category(ies) not found;");
        }

        return $result;
    }

        /** Add Personal Skills **/
    public function add_personal_skills($account_id = false, $person_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($person_id) && !empty($postdata)) {
            $postdata 		 = convert_to_array($postdata);
            $personal_skills= !empty($postdata['personal_skills']) ? $postdata['personal_skills'] : false;
            $personal_skills= (is_json($personal_skills)) ? json_decode($personal_skills) : $personal_skills;
            $total		= [];

            if (!empty($personal_skills)) {
                foreach ($personal_skills as $k => $val) {
                    $data = [
                        'skill_id'=>$val,
                        'person_id'=>$person_id,
                        'account_id'=>$account_id
                    ];

                    $check_exists = $this->db->limit(1)->get_where('people_skillset', $data)->row();
                    if (!$check_exists) {
                        $this->db->insert('people_skillset', $data);
                    }
                    $total[] = $data;
                }
            } elseif (!empty($postdata['skill_id'])) {
                $data = [
                    'skill_id'=>$postdata['skill_id'],
                    'person_id'=>$person_id,
                    'account_id'=>$account_id
                ];

                $check_exists = $this->db->limit(1)->get_where('people_skillset', $data)->row();
                if (!$check_exists) {
                    $this->db->insert('people_skillset', $data);
                }
                $total[] = $data;
            }

            if (!empty($total)) {
                $result = $total;
                $this->session->set_flashdata('message', 'Personal Skills added successfully');
            } else {
                $this->session->set_flashdata('message', 'Personal Skills not found');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }
        return $result;
    }


    /** Add Personal Skills **/
    public function remove_personal_skills($account_id = false, $person_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($person_id) && !empty($postdata)) {
            $postdata 		= convert_to_array($postdata);
            $personal_skills= !empty($postdata['personal_skills']) ? $postdata['personal_skills'] : false;
            $personal_skills= (is_json($personal_skills)) ? json_decode($personal_skills) : $personal_skills;
            $deleted		= [];

            if (!empty($personal_skills)) {
                foreach ($personal_skills as $k => $val) {
                    $data = [
                        'skill_id'=>$val,
                        'person_id'=>$person_id
                    ];

                    $check_exists = $this->db->limit(1)->get_where('people_skillset', $data)->row();
                    if (!empty($check_exists)) {
                        $this->db->where($data);
                        $this->db->delete('people_skillset');
                        $this->ssid_common->_reset_auto_increment('people_skillset', 'id');
                    }
                    $deleted[] = $data;
                }
            } elseif (!empty($postdata['skill_id'])) {
                $data = [
                    'skill_id'=>$postdata['skill_id'],
                    'person_id'=>$person_id
                ];

                $check_exists = $this->db->limit(1)->get_where('people_skillset', $data)->row();
                if (!empty($check_exists)) {
                    $this->db->where($data);
                    $this->db->delete('people_skillset');
                    $deleted[] = $data;
                    $this->ssid_common->_reset_auto_increment('people_skillset', 'id');
                }
            }

            if (!empty($deleted)) {
                $result = $deleted;
                $this->session->set_flashdata('message', 'Personal Skills removed successfully');
            } else {
                $this->session->set_flashdata('message', 'No personal skills were removed');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }
        return $result;
    }


    /** Get Personal Skills to a Job type **/
    public function get_personal_skills($account_id = false, $person_id = false, $where = false)
    {
        $result = false;
        if (!empty($account_id)) {
            $where 		= convert_to_array($where);

            $person_id 	= (!empty($person_id)) ? $person_id : (!empty($where['person_id']) ? $where['person_id'] : false);

            if (!empty($person_id)) {
                $this->db->where('ps.person_id', $person_id);
            }

            if (!empty($account_id)) {
                $this->db->where('ps.account_id', $account_id);
            }

            if (!empty($where['skill_id'])) {
                $skills_arr	= (is_array($where['skill_id'])) ? $where['skill_id'] : (is_string($where['skill_id']) ? [$where['skill_id']] : false);

                if (!empty($skills_arr) && is_array($skills_arr)) {
                    $this->db->where_in('ps.skill_id', $skills_arr);
                }
            }

            $query = $this->db->select('ps.person_id, concat( u.first_name," ",u.last_name) `full_name`, ss.*, pca.address_line1, pca.address_line2, pca.address_town, pca.address_postcode')
                ->join('people p', 'p.person_id = ps.person_id')
                ->join('user u', 'u.id = p.person_id')
                ->join('skills_bank ss', 'ss.skill_id = ps.skill_id')
                ->join('people_contact_addresses pca', 'pca.person_id = u.id', 'left')
                ->group_by('ps.skill_id')
                ->get('people_skillset ps');

            if ($query->num_rows() > 0) {
                if (!empty($result_as_array)) {
                    $result = $query->result_array();
                } else {
                    $result = $query->result();
                }
                $this->session->set_flashdata('message', 'Personal Skills data found');
            } else {
                $this->session->set_flashdata('message', 'No required skills found');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }

        return $result;
    }

    /** Add a Person's associated Regions for Job **/
    public function assign_regions($account_id = false, $person_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($person_id) && !empty($postdata)) {
            $postdata 		 	= convert_to_array($postdata);
            $assigned_regions	= !empty($postdata['assigned_regions']) ? $postdata['assigned_regions'] : false;
            $assigned_regions	= (is_json($assigned_regions)) ? json_decode($assigned_regions) : $assigned_regions;
            $total		= [];

            if (!empty($assigned_regions)) {
                foreach ($assigned_regions as $k => $val) {
                    $check_region_exists = $this->db->get_where('diary_regions', [ 'region_id'=>$val ])->row();

                    if (!empty($check_region_exists)) {
                        $data = [
                            'region_id'=>$val,
                            'person_id'=>$person_id,
                            'account_id'=>$account_id
                        ];

                        $check_exists = $this->db->limit(1)->get_where('people_assigned_regions', $data)->row();
                        if (!$check_exists) {
                            $this->db->insert('people_assigned_regions', $data);
                            $rec_id = $this->db->insert_id();
                        } else {
                            $rec_id = $check_exists->id;
                        }

                        $data 	 = $this->get_assigned_regions($account_id, $person_id, [ 'id'=>$rec_id ]);
                        $total[] = $data;
                    }
                }
            } elseif (!empty($postdata['region_id'])) {
                $check_region_exists = $this->db->get_where('diary_regions', [ 'region_id'=>$postdata['region_id'] ])->row();

                if (!empty($check_region_exists)) {
                    $data = [
                        'region_id'=>$postdata['region_id'],
                        'person_id'=>$person_id,
                        'account_id'=>$account_id
                    ];

                    $check_exists = $this->db->limit(1)->get_where('people_assigned_regions', $data)->row();
                    if (!$check_exists) {
                        $this->db->insert('people_assigned_regions', $data);
                        $rec_id = $this->db->insert_id();
                    } else {
                        $rec_id = $check_exists->id;
                    }

                    $data 	 = $this->get_assigned_regions($account_id, $person_id, [ 'id'=>$rec_id ]);
                    $total[] = $data;
                }
            }

            if (!empty($total)) {
                $result = $total;
                $this->session->set_flashdata('message', 'Regions assigned successfully');
            } else {
                $this->session->set_flashdata('message', 'The supplied regions are invalid');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }
        return $result;
    }

    /** Remove Associated Regions from a Person **/
    public function unassign_regions($account_id = false, $person_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($person_id) && !empty($postdata)) {
            $postdata 			= convert_to_array($postdata);
            $assigned_regions	= !empty($postdata['assigned_regions']) ? $postdata['assigned_regions'] : false;
            $assigned_regions	= (is_json($assigned_regions)) ? json_decode($assigned_regions) : $assigned_regions;
            $deleted			= [];

            if (!empty($assigned_regions)) {
                foreach ($assigned_regions as $k => $val) {
                    $data = [
                        'region_id'=>$val,
                        'person_id'=>$person_id
                    ];

                    $check_exists = $this->db->limit(1)->get_where('people_assigned_regions', $data)->row();
                    if (!empty($check_exists)) {
                        $this->db->where($data);
                        $this->db->delete('people_assigned_regions');
                        $this->ssid_common->_reset_auto_increment('people_assigned_regions', 'id');
                    }
                    $deleted[] = $data;
                }
            } elseif (!empty($postdata['region_id'])) {
                $data = [
                    'region_id'=>$postdata['region_id'],
                    'person_id'=>$person_id
                ];

                $check_exists = $this->db->limit(1)->get_where('people_assigned_regions', $data)->row();
                if (!empty($check_exists)) {
                    $this->db->where($data);
                    $this->db->delete('people_assigned_regions');
                    $deleted[] = $data;
                    $this->ssid_common->_reset_auto_increment('people_assigned_regions', 'id');
                }
            }

            if (!empty($deleted)) {
                $result = $deleted;
                $this->session->set_flashdata('message', 'Regions un-assigned successfully');
            } else {
                $this->session->set_flashdata('message', 'No associated regions were removed');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }
        return $result;
    }

    /** Get a list of all regions associated to a Person **/
    public function get_assigned_regions($account_id = false, $person_id = false, $where = false)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select('par.person_id, dr.*, GROUP_CONCAT( drp.`postcode_district` SEPARATOR ", " ) AS `region_postcodes`,')
                ->join('people p', 'p.person_id = par.person_id')
                ->join('diary_regions dr', 'dr.region_id = par.region_id')
                ->join('diary_region_postcodes drp', 'drp.region_id = dr.region_id', 'left')
                ->group_by('dr.region_id');

            if (!empty($where)) {
                $where = convert_to_array($where);
                $person_id = (!empty($person_id)) ? $person_id : (!empty($where['person_id']) ? $where['person_id'] : false);
            }

            if (!empty($account_id)) {
                $this->db->where('par.account_id', $account_id);
            }

            if (!empty($person_id)) {
                $this->db->where('par.person_id', $person_id);
            }

            if (!empty($where['id'])) {
                $row = $this->db->get_where('people_assigned_regions par', [ 'par.account_id'=>$account_id, 'par.id'=>$where['id'] ])->row();
                if (!empty($row)) {
                    return $row;
                }
                return false;
            }

            if (!empty($where['region_id'])) {
                $regions_arr	= (is_array($where['region_id'])) ? $where['region_id'] : (is_string($where['region_id']) ? [$where['region_id']] : false);

                if (!empty($regions_arr) && is_array($regions_arr)) {
                    $this->db->where_in('par.region_id', $regions_arr);
                }
            }

            $query = $this->db->get('people_assigned_regions par');

            if ($query->num_rows() > 0) {
                if (!empty($result_as_array)) {
                    $result = $query->result_array();
                } else {
                    $result = $query->result();
                }
                $this->session->set_flashdata('message', 'Personal associated Regions found');
            } else {
                $this->session->set_flashdata('message', 'No associated regions found');
            }
        } else {
            $this->session->set_flashdata('message', 'You request is missing required information');
        }

        return $result;
    }



    /*
    *	Update Contact Address
    */
    public function update_contact($account_id = false, $contact_id = false, $contact_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($contact_id) && !empty($contact_data)) {
            $data = [];

            foreach ($contact_data as $key => $value) {
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

            $data['last_modified'] 		= date('Y-m-d H:i:s');
            $data['last_modified_by'] 	= $this->ion_auth->_current_user()->id;

            if (!empty($data)) {
                $contactb4update = $this->db->get_where("people_contact_addresses", ["contact_id" => $contact_id ])->row();

                $data =  $this->ssid_common->_filter_data('people_contact_addresses', $data);
                $restricted_columns = ['created_by', 'created_date', 'archived'];
                foreach ($data as $key => $value) {
                    if (in_array($key, $restricted_columns)) {
                        unset($data[$key]);
                    }
                }

                $this->db->where('contact_id', $contact_id)->update('people_contact_addresses', $data);

                if (($this->db->trans_status() !== false) && ($this->db->affected_rows() > 0)) {
                    $result = $this->get_address_contacts($account_id, false, $contact_id);

                    ## create a log
                    $log_history_data = [
                        "log_type" 			=> "contacts",
                        "entry_id" 			=> $contact_id,
                        "person_id" 		=> $contactb4update->person_id,
                        "action" 			=> "update a contact",
                        "previous_values" 	=> json_encode($contactb4update),
                        "current_values" 	=> json_encode($result),
                    ];

                    $this->session->set_flashdata('message', 'The Contact Address has been updated successfully.');
                } else {
                    $this->session->set_flashdata('message', 'The Contact Address hasn\'t been changed.');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Account ID, no Contact Id or no new data supplied.');
        }
        return $result;
    }


    /*
    *	Delete Address Contact
    */
    public function delete_contact($account_id = false, $contact_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($contact_id)) {
            $contactb4delete = $this->db->get_where("people_contact_addresses", ["contact_id" => $contact_id ])->row();

            $data = [
                'archived'			=> 1 ,
                'last_modified_by'	=> $this->ion_auth->_current_user()->id
            ];

            $query = $this->db->update('people_contact_addresses', $data, ["account_id" => $account_id, "contact_id" => $contact_id]);

            if ($this->db->trans_status() !== false && $this->db->affected_rows() > 0) {
                $contact_after_delete = $this->db->get_where("people_contact_addresses", ["contact_id" => $contact_id ])->row();

                ## create a log
                $log_history_data = [
                    "log_type" 			=> "contacts",
                    "entry_id" 			=> $contact_id,
                    "person_id" 		=> $contactb4delete->person_id,
                    "action" 			=> "delete a contact address",
                    "previous_values" 	=> json_encode($contactb4delete),
                    "current_values" 	=> json_encode($contact_after_delete),
                ];

                $this->session->set_flashdata('message', 'The Contact Address has been deleted.');
                $result = true;
            } else {
                $this->session->set_flashdata('message', 'The Contact Address hasn\'t been deleted.');
            }
        } else {
            $this->session->set_flashdata('message', 'Invalid Contact ID or missing Account ID.');
        }
        return $result;
    }
}
