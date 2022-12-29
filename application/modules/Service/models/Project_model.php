<?php

namespace Application\Modules\Service\Models;

class Project_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    private $project_search_fields 					= [ 'project_name', 'project_id', 'project_ref', 'description' ];
    private $project_action_search_fields 			= [ 'project_id', 'project_action', 'action_description', 'action_status', 'assignee' ];
    private $project_workflow_search_fields 		= [ 'project_id', 'workflow_name', 'workflow_description', 'workflow_status', 'assignee' ];
    private $project_workflow_entries_search_fields = [ 'entry_name', 'entry_notes', 'assignee' ];

    /*
    *	Get list of Project records and search through them
    */
    public function get_projects($account_id = false, $project_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select('p.*, pt.project_type, ps.project_status, p.project_lead_id, CONCAT( u1.first_name, " ", u1.last_name ) `project_lead_name`, p.project_start_date, p.project_finish_date, p.date_created, CONCAT( u.first_name, " ", u.last_name ) `created_by_name`, p.last_modified, p.last_modified_by, CONCAT( u2.first_name, " ", u2.last_name ) `last_modified_by_name`, account.account_name `ownership`', false)
                ->join('user u', 'u.id = p.created_by', 'left')
                ->join('user u1', 'u1.id = p.project_lead_id', 'left')
                ->join('user u2', 'u2.id = p.last_modified_by', 'left')
                ->join('project_types pt', 'pt.project_type_id = p.project_type_id', 'left')
                ->join('project_status ps', 'ps.project_status_id = p.project_status_id', 'left')
                ->join('account', 'account.account_id = p.ownership', 'left')
                ->where('p.archived !=', 1)
                ->where('p.account_id', $account_id);

            $where = $raw_where = convert_to_array($where);
            if (!empty($project_id) || isset($where['project_id'])) {
                $project_id	= ($project_id) ? $project_id : (!empty($where['project_id']) ? $where['project_id'] : false);
                if (!empty($project_id)) {
                    $row = $this->db->get_where('project `p`', ['project_id'=>$project_id ])->row();
                    if (!empty($row)) {
                        $result = $row;
                        $this->session->set_flashdata('message', 'Project records data found');
                        return $result;
                    } else {
                        $this->session->set_flashdata('message', 'Project records data not found');
                        return false;
                    }
                }
                unset($where['project_id'], $where['project_ref']);
            }

            if (!empty($search_term)) {
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->project_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->project_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                $this->db->where($where);
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->get('project `p`');

            if ($query->num_rows() > 0) {
                $result_data = $query->result();

                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $result->records 			= $result_data;
                $counters 					= $this->get_project_totals($account_id, $search_term, $raw_where, $limit);
                $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
                $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
                $result->counters->limit  	= ( int ) $limit;
                $result->counters->offset 	= ( int ) $offset;

                $this->session->set_flashdata('message', 'Project records data found');
            } else {
                $this->session->set_flashdata('message', 'No Project data found!');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }

    /** Get Project record lookup counts **/
    public function get_project_totals($account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('p.project_id', false)
                ->join('user u', 'u.id = p.created_by', 'left')
                ->join('user u1', 'u1.id = p.project_lead_id', 'left')
                ->join('user u2', 'u2.id = p.last_modified_by', 'left')
                ->join('project_types pt', 'pt.project_type_id = p.project_type_id', 'left')
                ->join('project_status ps', 'ps.project_status_id = p.project_status_id', 'left')
                ->where('p.archived !=', 1)
                ->where('p.account_id', $account_id);

            $where = convert_to_array($where);

            if (!empty($search_term)) {
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->project_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->project_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($where)) {
                $this->db->where($where);
            }

            $query 			  = $this->db->from('project `p`')->count_all_results();
            $results['total'] = !empty($query) ? $query : 0;
            $limit 			  = (!empty($limit > 0)) ? $limit : $results['total'];
            $results['pages'] = !empty($query) ? ceil($query / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }


    /*
    *	Function to get project statuses for specific account_id. If they aren't exists get the default ones
    */
    public function get_project_statuses($account_id = false, $project_status_id = false, $project_status_group = false, $grouped = false)
    {
        $result = null;

        if (!empty($account_id)) {
            #$this->db->where( 'project_status.account_id', $account_id );
        } else {
            $this->db->where('( project_status.account_id IS NULL OR project_status.account_id = "" )');
        }

        if (!empty($project_status_id)) {
            $this->db->where('project_status.project_status_id', $project_status_id);
        }

        if (!empty($project_status_group)) {
            $this->db->where('project_status.project_status_group', $project_status_group);
        }

        $query = $this->db->where('is_active', 1)->get('project_status');

        if ($query->num_rows() > 0) {
            if ($grouped) {
                $data = [];
                foreach ($query->result() as $row) {
                    $data[$row->project_status_group] = $row;
                }
                $result = $data;
            } else {
                $result = (!empty($project_status_id)) ? $query->result()[0] : $query->result();
            }
        }

        return $result;
    }


    /*
    *	Function to get project types for specific account_id. If they aren't exists get the default ones
    */
    public function get_projects_types($account_id = false, $project_type_id = false, $ordered = false)
    {
        $result = false;

        if (!empty($project_type_id)) {
            $select = "SELECT * FROM project_types WHERE project_type_id = $project_type_id";
        } else {
            $select = "SELECT * FROM project_types WHERE account_id = $account_id
						UNION ALL SELECT * FROM project_types WHERE account_id = 0
					AND ( NOT EXISTS
						( SELECT 1 FROM project_types WHERE account_id = $account_id ) )";
        }

        $query = $this->db->query($select);

        if ($query->num_rows() > 0) {
            $ordered = format_boolean($ordered);
            if ($ordered) {
                foreach ($query->result_array() as $key => $row) {
                    $result[$row['project_type_id']] = $row;
                }
            } else {
                $result = $query->result_array();
            }
        }

        return $result;
    }


    /*
    *	Create a new project Profile with the unique reference
    */
    public function create_project($account_id = false, $post_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($post_data)) {
            ## validate the post-data
            $data = $this->ssid_common->_data_prepare($post_data);

            ## build unique reference
            $unique_ref_code = 'EVIPRO';
            if (!empty($post_data['project_type_id'])) {
                $project_code = $this->get_projects_types($account_id, $post_data['project_type_id']);
                if (!empty($project_code)) {
                    $unique_ref_code 		 = $project_code[0]['type_code'];
                    $data['unique_ref_code'] = $unique_ref_code;
                }
            }

            $last_project_id = 1;
            $this->db->select("max( account_counter ) `last_project_id`");
            $query = $this->db->get_where('project', ['account_id' => $account_id ])->row();

            if (!empty($query->last_project_id) && $query->last_project_id != 0) {
                $unique_ref_number 			= ($query->last_project_id + 1);
                $data['account_counter'] 	= $unique_ref_number;
            } else {
                $unique_ref_number 			= $last_project_id;
                $data['account_counter'] 	= $last_project_id;
            }

            $data['account_id']		= $account_id;
            $data['date_created'] 	= date('Y-m-d H:i:s');
            $data['created_by'] 	= $this->ion_auth->_current_user()->id;
            $data['project_ref']	= $this->generate_project_ref($account_id, $data);

            ## Check conflicts
            $conflict = $this->db->get_where('project', ['project_ref' => $data['project_ref'] ])->row();
            if (!$conflict) {
                $data = $this->ssid_common->_filter_data('project', $data);
                $this->db->insert('project', $data);

                if (($this->db->trans_status() !== false) && ($this->db->affected_rows() > 0)) {
                    $project_record = $this->get_projects($account_id, $this->db->insert_id());
                    $this->session->set_flashdata('message', 'Project created successfully.');
                }
            } else {
                $project_record = $conflict;
                $this->session->set_flashdata('message', 'There is a unique reference conflict. This Project already exists');
            }

            $result = !empty($project_record) ? $project_record : false;
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }
        return $result;
    }


    /*
    *	Update Project
    */
    public function update($account_id = false, $project_id = false, $project_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($project_id) && !empty($project_data)) {
            ## validate the post-data
            $data = $this->ssid_common->_data_prepare($project_data);
            $data = $this->ssid_common->_filter_data('project', $data);

            $data['last_modified_by'] 	= $this->ion_auth->_current_user()->id;
            $data['project_ref']		= $this->generate_project_ref($account_id, $data);

            if (!empty($data)) {
                $this->db->where('project_id', $project_id)->update('project', $data);
                if (($this->db->trans_status() !== false) && ($this->db->affected_rows() > 0)) {
                    $result = $this->get_projects($account_id, $project_id);
                    $this->session->set_flashdata('message', 'Project updated successfully.');
                } else {
                    $this->session->set_flashdata('message', 'The Project data not updated.');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }
        return $result;
    }


    /*
    *	Delete Project Profile
    */
    public function delete_project($account_id = false, $project_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($project_id)) {
            $conditions 	= [ 'account_id'=>$account_id,'project_id'=>$project_id ];
            $record_exists 	= $this->db->get_where('project', $conditions)->row();

            if (!empty($record_exists)) {
                $this->db->where($conditions)->delete('project_actions');
                $this->db->where($conditions)->delete('project_workflow');
                $this->db->where($conditions)->delete('project_workflow_entries');

                $this->db->where('project_id', $project_id)
                        ->delete('project');
                if (($this->db->trans_status() !== false) && ($this->db->affected_rows() > 0)) {
                    $this->session->set_flashdata('message', 'Project Profile deleted successfully.');
                    $result = true;
                } else {
                    $this->session->set_flashdata('message', 'No Project has been deleted.');
                    $result = false;
                }
            } else {
                $this->session->set_flashdata('message', 'Invalid Location ID.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Project ID supplied.');
            $result = true;
        }
        return $result;
    }


    /**
    /* Archive an Project resource
    */
    public function archive_project($account_id = false, $project_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($project_id)) {
            $conditions 	= [ 'account_id'=>$account_id,'project_id'=>$project_id ];
            $record_exists 	= $this->db->get_where('project', $conditions)->row();

            if (!empty($record_exists)) {
                ## Archive preexisting links to this Project
                $this->db->where($conditions)->update('project_actions', [ 'archived'=>1 ]);
                $this->db->where($conditions)->update('project_workflow', [ 'archived'=>1 ]);

                ## Then the parent
                $this->db->where('project_id', $project_id)
                    ->update('project', ['archived'=>1]);

                if ($this->db->trans_status() !== false) {
                    $this->session->set_flashdata('message', 'Project archived successfully.');
                    $result = true;
                }
            } else {
                $this->session->set_flashdata('message', 'Project data not found');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing the required information.');
        }
        return $result;
    }

    /*
    *	Function to get project types for specific account_id. If they aren't exists get the default ones
    */
    public function get_projects_task_names($account_id = false, $action_type_id = false)
    {
        $result = false;

        if (!empty($action_type_id)) {
            $select = "SELECT * FROM project_action_types WHERE action_type_id = $action_type_id";
        } else {
            $select = "SELECT * FROM project_action_types WHERE account_id = $account_id
						UNION ALL SELECT * FROM project_action_types WHERE account_id = 0
					AND ( NOT EXISTS
						( SELECT 1 FROM project_action_types WHERE account_id = $account_id ) )";
        }

        $query = $this->db->query($select);

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $this->session->set_flashdata('message', 'Task Names have been found.');
        } else {
            $this->session->set_flashdata('message', 'Task Names not found.');
        }

        return $result;
    }


    /*
    *	Function to add project id to the Site(s)
    */
    public function link_sites_to_project($account_id = false, $project_id = false, $sites = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($project_id) && !empty($project_id)) {
            $sites = array_map('trim', explode(',', $sites));
        }

        $verify_project = $this->db->get_where("project", ["account_id" => $account_id, "project_id" => $project_id])->row();

        if (!$verify_project) {
            $this->session->set_flashdata('message', 'Provided Project ID doesn\'t exists.');
        } else {
            foreach ($sites as $site_id) {
                if (!empty($site_id)) {
                    ## check if site has been already assigned to the project
                    $row = $this->db->get_where("sites_projects", ["account_id" => $account_id, "project_id" => $project_id, "site_id" => $site_id])->row();

                    $site = $this->db->get_where("site", ["account_id" => $account_id, "site_id" => $site_id])->row();

                    if ((!$row) && (!empty($site))) {
                        $data[] = [
                            "account_id" 	=> $account_id,
                            "project_id" 	=> $project_id,
                            "site_id" 		=> ( int ) $site_id,
                            "created_date"	=> date('Y-m-d H:i:s'),
                            "created_by"	=> $this->ion_auth->_current_user()->id
                        ];
                    }
                }
            }

            if (!empty($data)) {
                $query = $this->db->insert_batch("sites_projects", $data);
                if (!empty($this->db->affected_rows()) && ($this->db->affected_rows() > 0)) {
                    $this->session->set_flashdata('message', 'Sites have been linked.');
                    $result = $query;
                } else {
                    $this->session->set_flashdata('message', 'There is no change to sites.');
                }
            } else {
                $this->session->set_flashdata('message', 'There was a problem with supplied data: <br />Site(s) doesn\'t exists or already assigned to this Project.');
            }
        }
        return $result;
    }


    /*
    *	Get linked Site(s)
    */
    public function get_linked_sites($account_id = false, $project_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = 0)
    {
        $result = false;

        if (!empty($account_id) && !empty($project_id)) {
            $this->db->select("sc.*");
            $this->db->select("s.*");
            $this->db->select("a.summaryline");
            $this->db->select("CONCAT( u.first_name, ' ', u.last_name ) `created_by_fullname`");

            $this->db->join("site s", "sc.site_id = s.site_id", "left");
            $this->db->join("addresses a", "s.site_address_id = a.main_address_id", "left");
            $this->db->join("user u", "sc.created_by = u.id", "left");

            $this->db->where("sc.account_id", $account_id);
            $this->db->where("sc.project_id", $project_id);

            $arch_where = "( s.archived != 1 or s.archived is NULL )";
            $this->db->where($arch_where);

            $query = $this->db->get("sites_projects sc", $limit, $offset);

            if (!empty($query->num_rows()) && ($query->num_rows() > 0)) {
                $result 	= $query->result();
                $this->session->set_flashdata('message', 'Linked Site(s) data found.');
            } else {
                $this->session->set_flashdata('message', 'Linked Site(s) data not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account or Project details provided.');
        }

        return $result;
    }


    /*
    *	Unlink Stie from the Project
    */
    public function unlink_site_from_project($account_id = false, $project_id = false, $site_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($project_id) && !empty($site_id)) {
            $project = $this->get_projects($account_id, $project_id);

            if (!empty($project)) {
                $this->db->select('link_id');
                $where = [
                    "account_id" 	=> $account_id,
                    "project_id" 	=> $project_id,
                    "site_id" 		=> $site_id,
                ];
                $link_id = $this->db->get_where("sites_projects", $where)->row()->link_id;

                if (!empty($link_id)) {
                    $this->db->where("link_id", $link_id);
                    $unlink_site = $this->db->delete("sites_projects", $where);
                    if ($this->db->affected_rows() > 0) {
                        $result = true;
                        $this->session->set_flashdata('message', 'The Building has been unlinked from the project.');
                    } else {
                        $this->session->set_flashdata('message', 'The Building has NOT been unlinked.');
                    }
                } else {
                    $this->session->set_flashdata('message', 'This Building seems to be not linked to this project.');
                }
            } else {
                $this->session->set_flashdata('message', 'No Project has been found.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Project ID, Site ID or Account ID supplied');
        }
        return $result;
    }


    /*
    * 	Get quick stats
    */
    public function get_quick_stats($account_id = false, $where = false, $offset = 0, $limit = 100)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($where)) {
                if (is_object($where)) {
                    $where = get_object_vars($where);
                }
                $this->db->where($where);
            }

            $this->db->select("project_status_id, count( project_id ) `project_number`, project_status.status_name", false);
            $this->db->join("project_status", "project_status.status_id = project.project_status_id", "left");
            $this->db->group_by("project_status_id");
            $this->db->where("project.account_id", $account_id);
            $query = $this->db->get("project");

            if ($query->num_rows() > 0) {
                foreach ($query->result() as $key => $row) {
                    $result[$row->project_status_id] = $row->project_number;
                }
                $this->session->set_flashdata('message', 'Stats found');
            } else {
                $this->session->set_flashdata('message', 'Stats not found');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account ID Provided');
        }
        return $result;
    }

    /*
    *	Get all assets attached to a project
    */
    public function get_linked_assets($account_id = false, $project_id = false, $asset_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = 0)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select('project.project_name, ca.*, asset.*, project.project_id `project_id`, project_status.status_name, project_types.type_name, asset_types.asset_type, categories.category_name, CONCAT( user.first_name, " ", user.last_name ) `created_by`')
                ->join('project', 'project.project_id = ca.project_id', 'left')
                ->join('project_status', 'project_status.status_id = project.project_status_id', 'left')
                ->join('project_types', 'project_types.project_type_id = project.project_type_id', 'left')
                ->join('asset', 'asset.asset_id = ca.asset_id', 'left')
                ->join('asset_types', 'asset_types.asset_type_id = asset.asset_type_id', 'left')
                ->join('audit_categories `categories`', 'categories.category_id = asset_types.category_id', 'left')
                ->join('user', 'ca.created_by = user.id', 'left')
                ->where('ca.account_id', $account_id);

            if (!empty($project_id)) {
                $this->db->where('ca.project_id', $project_id);
            }

            if (!empty($asset_id)) {
                $this->db->where('ca.asset_id', $asset_id);
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->get('project_assets ca');

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Linked assets / projects data found.');
            } else {
                $this->session->set_flashdata('message', 'Linked assets / projects data not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }

        return $result;
    }

    /**
    * Get all Stock and BOMS attached to a Project
    */
    public function get_projects_consumed_items($account_id = false, $project_id = false, $item_type = false, $grouped = false)
    {
        $result = false;
        if ($project_id) {
            if (!empty($item_type)) {
                $item_type = $this->_get_item_type($item_type);
            }

            $job_ids 	= $this->get_projects_jobs($account_id, $project_id, ['ids_only'=>1]);

            if (!empty($job_ids) && is_array($job_ids)) {
                $job_ids = implode(',', $job_ids);
                $sql_str = "( SELECT job_consumed_items.id, job_consumed_items.job_id, job_consumed_items.item_code, job_consumed_items.item_qty, job_consumed_items.price, job_consumed_items.price_adjusted, job_consumed_items.item_type, stock_items.item_name
							FROM job_consumed_items JOIN stock_items ON job_consumed_items.item_code = stock_items.item_code
							WHERE job_consumed_items.job_id IN (".$job_ids.") ";
                if (!empty($account_id)) {
                    $sql_str .= "AND stock_items.account_id = '". $account_id."' ";
                }
                if (!empty($item_type)) {
                    $sql_str .= "AND job_consumed_items.item_type = '". $item_type."' ";
                }
                $sql_str .= "ORDER BY stock_items.item_name ) ";
                $sql_str .= "UNION ALL ";
                $sql_str .= "( SELECT job_consumed_items.id, job_consumed_items.job_id, job_consumed_items.item_code, job_consumed_items.item_qty, job_consumed_items.price, job_consumed_items.price_adjusted, job_consumed_items.item_type, bom_items.item_name
							FROM job_consumed_items JOIN bom_items ON job_consumed_items.item_code = bom_items.item_code
							WHERE job_consumed_items.job_id IN (".$job_ids.") ";
                if (!empty($account_id)) {
                    $sql_str .= "AND bom_items.account_id = '". $account_id."' ";
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
                    $this->session->set_flashdata('message', 'Consumed items found');
                } else {
                    $this->session->set_flashdata('message', 'No data found');
                }
            } else {
                $this->session->set_flashdata('message', 'No data found');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }


    /** Fetch all Job IDs attached to a project **/
    public function get_projects_jobs($account_id = false, $project_id = false, $where = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($project_id)) {
            $check_project_exists = $this->db->get_where('project', [ 'account_id'=>$account_id, 'project_id'=>$project_id ])->row();
            if (!empty($check_project_exists)) {
                $where = convert_to_array($where);
                if (!empty($where['ids_only'])) {
                    $this->db->select('job.job_id', false);
                    $ids_only = true;
                    unset($where['ids_only']);
                } else {
                    $this->db->select('job.*, job.account_id', false);
                }

                $query = $this->db->join('sites_projects', 'job.site_id = sites_projects.site_id', 'left')
                    //->join( 'sites_projects', 'project.project_id = site.project_id', 'left' )
                    ->join('project', 'project.project_id = sites_projects.project_id', 'left')
                    ->where('job.account_id', $account_id)
                    ->where('project.project_id', $project_id)
                    ->or_where('sites_projects.project_id', $project_id)
                    ->or_where('job.project_id', $project_id)
                    ->group_by('job.job_id')
                    ->get('job');
                if ($query->num_rows() > 0) {
                    $result = (!empty($ids_only)) ? array_column($query->result(), 'job_id') : $query->result();
                    $this->session->set_flashdata('message', 'Project Jobs found');
                } else {
                    $this->session->set_flashdata('message', 'No data found');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }
        return $result;
    }

    /** Generate Project Ref **/
    private function generate_project_ref($account_id = false, $data = false)
    {
        if (!empty($account_id) && !empty($data)) {
            $project_ref  = $account_id;
            $project_ref .= !empty($data['project_name']) ? lean_string($data['project_name']) : '';
            $project_ref .= !empty($data['project_type_id']) ? $data['project_type_id'] : '';
            $project_ref .= !empty($data['unique_ref_code']) ? $data['unique_ref_code'] : '';
        } else {
            $project_ref = $account_id.$this->ssid_common->generate_random_password();
        }
        return strtoupper($project_ref);
    }


    /** Generate Project Action Ref **/
    private function generate_project_action_ref($account_id = false, $data = false)
    {
        if (!empty($account_id) && !empty($data)) {
            $project_action_ref = $account_id;
            $project_action_ref .= (!empty($data['project_action'])) ? lean_string($data['project_action']) : '';
            $project_action_ref .= (!empty($data['workflow_name'])) ? lean_string($data['workflow_name']) : '';
            $project_action_ref .= (!empty($data['project_id'])) ? $data['project_id'] : '';
        } else {
            $project_action_ref = $account_id.$this->ssid_common->generate_random_password();
        }
        return strtoupper($project_action_ref);
    }


    /** Create a new Project Action record record **/
    public function create_project_action($account_id = false, $project_id = false, $project_actions_data = false)
    {
        $result = null;

        if (!empty($account_id) && !empty($project_id) && !empty($project_actions_data)) {
            $project_actions_data   = $this->ssid_common->_data_prepare($project_actions_data);
            $project_data 			= $this->db->get_where('project', [ 'account_id'=>$account_id, 'project_id'=>$project_id ])->row();

            if (!empty($project_data)) {
                $project_action_ref			= $this->generate_project_action_ref($account_id, $project_actions_data);
                $data 					 	= $this->ssid_common->_filter_data('project_actions', $project_actions_data);
                $data['project_action_ref'] = $project_action_ref;
                $data['action_status']		= 'Awaiting action';

                $check_exists = $this->db->select('project_actions.project_action_id', false)
                    ->where('project_actions.account_id', $account_id)
                    ->where([ 'project_id'=>$project_id, 'project_action_ref'=>$project_action_ref ])
                    ->limit(1)
                    ->get('project_actions')
                    ->row();

                if (!empty($check_exists)) {
                    $data['last_modified_by'] 	= $this->ion_auth->_current_user->id;
                    $this->db->where('project_action_id', $check_exists->project_action_id)
                        ->update('project_actions', $data);
                    $record = $this->get_project_actions($account_id, false, [ 'project_action_id'=>$check_exists->project_action_id ]);
                    $this->session->set_flashdata('message', 'Project Action already exists, record updated successfully.');
                } else {
                    $data['created_by'] 		= $this->ion_auth->_current_user->id;
                    $this->db->insert('project_actions', $data);
                    $record = $this->get_project_actions($account_id, false, [ 'project_action_id'=>$this->db->insert_id() ]);
                    $this->session->set_flashdata('message', 'Project Action record created successfully.');
                }

                if (!empty($record)) {
                    $result = $record;
                } else {
                    $this->session->set_flashdata('message', 'Error! There was a problem completing your request, please check your submitted data.');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }

        return $result;
    }

    /** Update an existing Project Action record **/
    public function update_project_action($account_id = false, $project_action_id = false, $update_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($project_action_id)  && !empty($update_data)) {
            $ref_condition = [ 'account_id'=>$account_id, 'project_action_id'=>$project_action_id ];
            $update_data   = $this->ssid_common->_data_prepare($update_data);
            $update_data   = $this->ssid_common->_filter_data('project_actions', $update_data);
            $record_pre_update = $this->db->get_where('project_actions', [ 'account_id'=>$account_id, 'project_action_id'=>$project_action_id ])->row();

            if (!empty($record_pre_update)) {
                if (empty($update_data['project_action'])) {
                    $update_data['project_action'] = $record_pre_update->project_action;
                }

                $project_action_ref				 	= !empty($update_data['project_action']) ? $this->generate_project_action_ref($account_id, $update_data) : (!empty($update_data['project_action_ref']) ? $update_data['project_action_ref'] : $this->generate_project_action_ref($account_id, $update_data));
                $update_data['project_action_ref'] 	= $project_action_ref;

                $check_conflict = $this->db->select('project_actions.*', false)
                    ->where('project_actions.account_id', $account_id)
                    ->where('project_actions.project_action_ref', $project_action_ref)
                    ->where([ 'project_id'=>$update_data['project_id'] ])
                    ->where('project_action_id !=', $project_action_id)
                    ->limit(1)
                    ->get('project_actions')
                    ->row();

                if (!$check_conflict) {
                    $update_data['last_modified_by'] = $this->ion_auth->_current_user->id;
                    $this->db->where($ref_condition)
                        ->update('project_actions', $update_data);

                    if ($this->db->trans_status() !== false) {
                        if (isset($update_data['is_active'])&& $update_data['is_active'] == 0) {
                            $this->session->set_flashdata('message', 'Project Action record archived successfully');
                            $result = true;
                        } else {
                            $updated_record = $this->get_project_actions($account_id, false, [ 'project_action_id'=>$project_action_id ]);
                            $result 		= (!empty($updated_record->records)) ? $updated_record->records : (!empty($updated_record) ? $updated_record : false);

                            $this->session->set_flashdata('message', 'Project Action record updated successfully');
                        }
                        return $result;
                    }
                } else {
                    $this->session->set_flashdata('message', 'Project Action conflict, request aborted!');
                    return false;
                }
            } else {
                $this->session->set_flashdata('message', 'This Project Action record does not exist or does not belong to you.');
                return false;
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }
        return $result;
    }

    /*
    *	Get list of Project Action records and search through them
    */
    public function get_project_actions($account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select('project_actions.*, p.project_name, p.project_ref, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false)
                ->join('project p', 'p.project_id = project_actions.project_id', 'left')
                ->join('user creater', 'creater.id = project_actions.created_by', 'left')
                ->join('user modifier', 'modifier.id = project_actions.last_modified_by', 'left')
                ->where('project_actions.is_active', 1)
                ->where('project_actions.account_id', $account_id);

            $where = $raw_where = convert_to_array($where);

            if (isset($where['project_action_id'])) {
                $project_action_id	= (!empty($where['project_action_id'])) ? $where['project_action_id'] : false;
                if (!empty($project_action_id)) {
                    $row = $this->db->get_where('project_actions', ['project_action_id'=>$project_action_id ])->row();

                    if (!empty($row)) {
                        $result = $row;
                        $this->session->set_flashdata('message', 'Project Action records data found');
                        return $result;
                    } else {
                        $this->session->set_flashdata('message', 'Project Action records data not found');
                        return false;
                    }
                }
                unset($where['project_action_id'], $where['project_action_ref']);
            }

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->project_action_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->project_action_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (isset($where['project_id'])) {
                if (!empty($where['project_id'])) {
                    $this->db->where('project_actions.project_id', $where['project_id']);
                }
                unset($where['project_id']);
            }

            if (!empty($where)) {
                $this->db->where($where);
            }

            if (!empty($order_by)) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('project_action');
            }

            $query = $this->db->get('project_actions');

            if ($query->num_rows() > 0) {
                $result_data = $query->result();

                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $result->records 			= $result_data;
                $counters 					= $this->get_project_action_totals($account_id, $search_term, $raw_where, $limit);
                $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
                $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
                $result->counters->limit  	= (!empty($apply_limit)) ? $limit : $result->counters->total;
                $result->counters->offset 	= $offset;

                $this->session->set_flashdata('message', 'Project Action records data found');
            } else {
                $this->session->set_flashdata('message', 'There\'s currently no Project Action records setup for your Account');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }

    /** Get Project Action record lookup counts **/
    public function get_project_action_totals($account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('project_actions.project_action_id', false)
                ->join('project p', 'p.project_id = project_actions.project_id', 'left')
                ->where('project_actions.is_active', 1)
                ->where('project_actions.account_id', $account_id);

            $where = convert_to_array($where);

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->project_action_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->project_action_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (isset($where['project_id'])) {
                if (!empty($where['project_id'])) {
                    $this->db->where('project_actions.project_id', $where['project_id']);
                }
                unset($where['project_id']);
            }

            if (!empty($where)) {
                $this->db->where($where);
            }

            $query 			  = $this->db->from('project_actions')->count_all_results();
            $results['total'] = !empty($query) ? $query : 0;
            $limit 			  = (!empty($limit > 0)) ? $limit : $results['total'];
            $results['pages'] = !empty($query) ? ceil($query / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }


    /*
    *	Delete Project Action Profile
    */
    public function delete_project_action($account_id = false, $project_action_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($project_action_id)) {
            $data = [ 'archived'=>1 ];
            $this->db->where('project_action_id', $project_action_id)
                    ->update('project_actions', $data);
            if (($this->db->trans_status() !== false) && ($this->db->affected_rows() > 0)) {
                $this->session->set_flashdata('message', 'Project Action Profile deleted successfully.');
                $result = true;
            } else {
                $this->session->set_flashdata('message', 'No Project Action has been deleted.');
                $result = false;
            }
        } else {
            $this->session->set_flashdata('message', 'No Project Action ID or Account ID supplied');
            $result = true;
        }
        return $result;
    }


    /**
    * Create a new Project Workflow record record
    **/
    public function create_project_workflow($account_id = false, $project_id = false, $project_workflow_data = false)
    {
        $result = null;

        if (!empty($account_id) && !empty($project_id) && !empty($project_workflow_data)) {
            $project_workflow_data  = $this->ssid_common->_data_prepare($project_workflow_data);
            $project_data 			= $this->db->get_where('project', [ 'account_id'=>$account_id, 'project_id'=>$project_id ])->row();

            if (!empty($project_data)) {
                $workflow_ref			= $this->generate_project_action_ref($account_id, $project_workflow_data);
                $data 					= $this->ssid_common->_filter_data('project_workflow', $project_workflow_data);
                $data['workflow_ref'] 	= $workflow_ref;
                $data['workflow_status']= 'Awaiting action';

                $check_exists = $this->db->select('project_workflow.workflow_id', false)
                    ->where('project_workflow.account_id', $account_id)
                    ->where([ 'project_id'=>$project_id, 'workflow_ref'=>$workflow_ref ])
                    ->limit(1)
                    ->get('project_workflow')
                    ->row();

                if (!empty($check_exists)) {
                    $data['last_modified_by'] 	= $this->ion_auth->_current_user->id;
                    $this->db->where('workflow_id', $check_exists->workflow_id)
                        ->update('project_workflow', $data);
                    $record = $this->get_project_workflows($account_id, false, [ 'workflow_id'=>$check_exists->workflow_id ]);
                    $this->session->set_flashdata('message', 'Project Workflow already exists, record updated successfully.');
                } else {
                    $data['created_by'] 		= $this->ion_auth->_current_user->id;
                    $this->db->insert('project_workflow', $data);
                    $record = $this->get_project_workflows($account_id, false, [ 'workflow_id'=>$this->db->insert_id() ]);
                    $this->session->set_flashdata('message', 'Project Workflow record created successfully.');
                }

                if (!empty($record)) {
                    $result = $record;
                } else {
                    $this->session->set_flashdata('message', 'Error! There was a problem completing your request, please check your submitted data.');
                }
            } else {
                $this->session->set_flashdata('message', 'The supplied Project ID record does not exist or does not belong to you.');
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }

        return $result;
    }


    /** Update an existing Project Workflow record **/
    public function update_project_workflow($account_id = false, $workflow_id = false, $update_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($workflow_id)  && !empty($update_data)) {
            $ref_condition = [ 'account_id'=>$account_id, 'workflow_id'=>$workflow_id ];
            $update_data   = $this->ssid_common->_data_prepare($update_data);
            $update_data   = $this->ssid_common->_filter_data('project_workflow', $update_data);
            $record_pre_update = $this->db->get_where('project_workflow', [ 'account_id'=>$account_id, 'workflow_id'=>$workflow_id ])->row();

            if (!empty($record_pre_update)) {
                $workflow_ref				 = !empty($update_data['workflow_name']) ? $this->generate_project_action_ref($account_id, $update_data) : (!empty($update_data['workflow_ref']) ? $update_data['workflow_ref'] : $this->generate_project_action_ref($account_id, $update_data));
                $update_data['workflow_ref'] = $workflow_ref;

                $check_conflict = $this->db->select('project_workflow.*', false)
                    ->where('project_workflow.account_id', $account_id)
                    ->where('project_workflow.workflow_ref', $workflow_ref)
                    ->where([ 'project_id'=>$update_data['project_id'] ])
                    ->where('workflow_id !=', $workflow_id)
                    ->limit(1)
                    ->get('project_workflow')
                    ->row();

                if (!$check_conflict) {
                    $update_data['last_modified_by'] = $this->ion_auth->_current_user->id;
                    $this->db->where($ref_condition)
                        ->update('project_workflow', $update_data);

                    $updated_record = $this->get_project_workflows($account_id, false, [ 'workflow_id'=>$workflow_id ]);
                    $result 		= (!empty($updated_record->records)) ? $updated_record->records : (!empty($updated_record) ? $updated_record : false);

                    $this->session->set_flashdata('message', 'Project Workflow record updated successfully');
                    return $result;
                } else {
                    $this->session->set_flashdata('message', 'Project Workflow record updated successfully');
                    return false;
                }
            } else {
                $this->session->set_flashdata('message', 'This Project Workflow record does not exist or does not belong to you.');
                return false;
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }
        return $result;
    }


    /*
    *	Get list of Project Workflow records and search through them
    */
    public function get_project_workflows($account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select('project_workflow.*, p.project_name, p.project_ref, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false)
                ->join('project p', 'p.project_id = project_workflow.project_id', 'left')
                ->join('user creater', 'creater.id = project_workflow.created_by', 'left')
                ->join('user modifier', 'modifier.id = project_workflow.last_modified_by', 'left')
                ->where('project_workflow.is_active', 1)
                ->where('project_workflow.account_id', $account_id);

            $where = $raw_where = convert_to_array($where);

            if (isset($where['workflow_id'])) {
                $workflow_id	= (!empty($where['workflow_id'])) ? $where['workflow_id'] : false;
                if (!empty($workflow_id)) {
                    $row = $this->db->get_where('project_workflow', ['workflow_id'=>$workflow_id ])->row();

                    if (!empty($row)) {
                        $result = $row;
                        $this->session->set_flashdata('message', 'Project Workflow records data found');
                        return $result;
                    } else {
                        $this->session->set_flashdata('message', 'Project Workflow records data not found');
                        return false;
                    }
                }
                unset($where['workflow_id'], $where['workflow_ref']);
            }

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->project_workflow_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->project_workflow_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (isset($where['project_id'])) {
                if (!empty($where['project_id'])) {
                    $this->db->where('project_workflow.project_id', $where['project_id']);
                }
                unset($where['project_id']);
            }

            if (!empty($where)) {
                $this->db->where($where);
            }

            if (!empty($order_by)) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('workflow_name');
            }

            $query = $this->db->get('project_workflow');

            if ($query->num_rows() > 0) {
                $result_data = $query->result();

                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $result->records 			= $result_data;
                $counters 					= $this->get_project_workflows_totals($account_id, $search_term, $raw_where, $limit);
                $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
                $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
                $result->counters->limit  	= (!empty($apply_limit)) ? $limit : $result->counters->total;
                $result->counters->offset 	= $offset;

                $this->session->set_flashdata('message', 'Project Workflow records data found');
            } else {
                $this->session->set_flashdata('message', 'There\'s currently no Project Workflow records setup for your Account');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }


    /** Get Project Workflow record lookup counts **/
    public function get_project_workflows_totals($account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('project_workflow.workflow_id', false)
                ->join('project p', 'p.project_id = project_workflow.project_id', 'left')
                ->where('project_workflow.is_active', 1)
                ->where('project_workflow.account_id', $account_id);

            $where = convert_to_array($where);

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->project_workflow_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->project_workflow_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (isset($where['project_id'])) {
                if (!empty($where['project_id'])) {
                    $this->db->where('project_workflow.project_id', $where['project_id']);
                }
                unset($where['project_id']);
            }

            if (!empty($where)) {
                $this->db->where($where);
            }

            $query 			  = $this->db->from('project_workflow')->count_all_results();
            $results['total'] = !empty($query) ? $query : 0;
            $limit 			  = (!empty($limit > 0)) ? $limit : $results['total'];
            $results['pages'] = !empty($query) ? ceil($query / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }


    /*
    *	Delete Project Workflow Profile
    */
    public function delete_project_workflow($account_id = false, $workflow_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($workflow_id)) {
            $data = [ 'archived'=>1 ];
            $this->db->where('workflow_id', $workflow_id)
                    ->update('project_workflow', $data);
            if (($this->db->trans_status() !== false) && ($this->db->affected_rows() > 0)) {
                $this->session->set_flashdata('message', 'Project Workflow Profile deleted successfully.');
                $result = true;
            } else {
                $this->session->set_flashdata('message', 'No Project Workflow has been deleted.');
                $result = false;
            }
        } else {
            $this->session->set_flashdata('message', 'No Project Workflow ID or Account ID supplied');
            $result = true;
        }
        return $result;
    }


    /**
    * Create a new Project Workflow entry record
    **/
    public function create_workflow_entry($account_id = false, $workflow_id = false, $entry_data = false)
    {
        $result = null;

        if (!empty($account_id) && !empty($workflow_id) && !empty($entry_data)) {
            $entry_data  	= $this->ssid_common->_data_prepare($entry_data);
            $workflow_data 	= $this->db->select('project_workflow.workflow_id')->get_where('project_workflow', [ 'account_id'=>$account_id, 'workflow_id'=>$workflow_id ])->row();
            if (!empty($workflow_data)) {
                $data 					= $this->ssid_common->_filter_data('project_workflow_entries', $entry_data);
                $data['entry_status']	= (!empty($data['entry_status'])) ? $data['entry_status'] : 'Not started';

                if (!empty($data['entry_id'])) {
                    $check_exists = $this->db->select('project_workflow_entries.entry_id', false)
                        ->where('project_workflow_entries.account_id', $account_id)
                        ->where('entry_id', $data['entry_id'])
                        ->limit(1)
                        ->get('project_workflow_entries')
                        ->row();
                } else {
                    $check_exists = $this->db->select('project_workflow_entries.entry_id', false)
                        ->where('project_workflow_entries.account_id', $account_id)
                        ->where([ 'workflow_id'=>$workflow_id, 'entry_name'=>$data['entry_name'], 'entry_status'=>$data['entry_status'] ])
                        ->limit(1)
                        ->get('project_workflow_entries')
                        ->row();
                }

                if (!empty($check_exists)) {
                    $data['last_modified_by'] 	= $this->ion_auth->_current_user->id;
                    $this->db->where('entry_id', $check_exists->entry_id)
                        ->update('project_workflow_entries', $data);
                    $record = $this->get_workflow_entries($account_id, false, [ 'entry_id'=>$check_exists->entry_id ]);
                    $this->session->set_flashdata('message', 'Project Workflow already exists, record updated successfully.');
                } else {
                    $data['created_by'] 		= $this->ion_auth->_current_user->id;
                    $this->db->insert('project_workflow_entries', $data);
                    $record = $this->get_workflow_entries($account_id, false, [ 'entry_id'=>$this->db->insert_id() ]);
                    $this->session->set_flashdata('message', 'Project Workflow record created successfully.');
                }

                if (!empty($record)) {
                    $result = $record;
                } else {
                    $this->session->set_flashdata('message', 'Error! There was a problem completing your request, please check your submitted data.');
                }
            } else {
                $this->session->set_flashdata('message', 'The supplied Project ID record does not exist or does not belong to you.');
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }

        return $result;
    }


    /**
    * Update an existing Project Workflow entry record
    **/
    public function update_workflow_entry($account_id = false, $entry_id = false, $update_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($entry_id)  && !empty($update_data)) {
            $ref_condition = [ 'account_id'=>$account_id, 'entry_id'=>$entry_id ];
            $update_data   = $this->ssid_common->_data_prepare($update_data);
            $update_data   = $this->ssid_common->_filter_data('project_workflow_entries', $update_data);
            $record_pre_update = $this->db->get_where('project_workflow_entries', [ 'account_id'=>$account_id, 'entry_id'=>$entry_id ])->row();

            if (!empty($record_pre_update)) {
                $check_conflict = $this->db->select('project_workflow_entries.*', false)
                    ->where('project_workflow_entries.account_id', $account_id)
                    ->where([ 'workflow_id'=>$update_data['workflow_id'] ])
                    ->where('entry_id !=', $entry_id)
                    ->limit(1)
                    ->get('project_workflow_entries')
                    ->row();

                if (!$check_conflict) {
                    $update_data['last_modified_by'] = $this->ion_auth->_current_user->id;
                    $this->db->where($ref_condition)
                        ->update('project_workflow_entries', $update_data);

                    $updated_record = $this->get_workflow_entries($account_id, false, [ 'entry_id'=>$entry_id ]);
                    $result 		= (!empty($updated_record->records)) ? $updated_record->records : (!empty($updated_record) ? $updated_record : false);
                    $this->session->set_flashdata('message', 'Workflow entry updated successfully');
                    return $result;
                } else {
                    $update_data['last_modified_by'] = $this->ion_auth->_current_user->id;
                    $this->db->where($ref_condition)
                        ->update('project_workflow_entries', $update_data);

                    $updated_record = $this->get_workflow_entries($account_id, false, [ 'entry_id'=>$entry_id ]);
                    $result 		= (!empty($updated_record->records)) ? $updated_record->records : (!empty($updated_record) ? $updated_record : false);
                    $this->session->set_flashdata('message', 'Workflow entry updated successfully');
                    return $result;
                }
            } else {
                $this->session->set_flashdata('message', 'This Workflow entry does not exist or does not belong to you.');
                return false;
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }
        return $result;
    }


    /*
    *	Get list of Project Workflow records and search through them
    */
    public function get_workflow_entries($account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select('pwe.*, pw.workflow_name, pw.workflow_ref, p.project_name, p.project_ref, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false)
                ->join('project_workflow pw', 'pw.workflow_id = pwe.project_id', 'left')
                ->join('project p', 'p.project_id = pw.project_id', 'left')
                ->join('user creater', 'creater.id = pwe.created_by', 'left')
                ->join('user modifier', 'modifier.id = pwe.last_modified_by', 'left')
                ->where('pwe.is_active', 1)
                ->where('pwe.account_id', $account_id);

            $where = $raw_where = convert_to_array($where);

            if (isset($where['entry_id'])) {
                $entry_id	= (!empty($where['entry_id'])) ? $where['entry_id'] : false;
                if (!empty($entry_id)) {
                    $row = $this->db->get_where('project_workflow_entries pwe', ['entry_id'=>$entry_id ])->row();

                    if (!empty($row)) {
                        $result = $row;
                        $this->session->set_flashdata('message', 'Project Workflow entries data found');
                        return $result;
                    } else {
                        $this->session->set_flashdata('message', 'Project Workflow entries data not found');
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
                        foreach ($this->project_workflow_entries_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->project_workflow_entries_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (isset($where['project_id'])) {
                if (!empty($where['project_id'])) {
                    $this->db->where('pwe.project_id', $where['project_id']);
                }
                unset($where['project_id']);
            }

            if (isset($where['workflow_id'])) {
                if (!empty($where['workflow_id'])) {
                    $this->db->where('pwe.workflow_id', $where['workflow_id']);
                }
                unset($where['workflow_id']);
            }

            if (!empty($where)) {
                $this->db->where($where);
            }

            if (!empty($order_by)) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('pwe.entry_name');
            }

            $query = $this->db->get('project_workflow_entries `pwe`');

            if ($query->num_rows() > 0) {
                $result_data = $query->result();

                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $result->records 			= $result_data;
                $counters 					= $this->get_workflow_entries_totals($account_id, $search_term, $raw_where, $limit);
                $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
                $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
                $result->counters->limit  	= (!empty($apply_limit)) ? $limit : $result->counters->total;
                $result->counters->offset 	= $offset;

                $this->session->set_flashdata('message', 'Project Workflow entries data found');
            } else {
                $this->session->set_flashdata('message', 'There\'s currently no Project Workflow entries setup for your Account');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }


    /** Get Project Workflow record lookup counts **/
    public function get_workflow_entries_totals($account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('pwe.workflow_id', false)
                ->join('project_workflow pw', 'pw.workflow_id = pwe.project_id', 'left')
                ->join('project p', 'p.project_id = pw.project_id', 'left')
                ->where('pwe.is_active', 1)
                ->where('pwe.account_id', $account_id);

            $where = convert_to_array($where);

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->project_workflow_entries_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->project_workflow_entries_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (isset($where['project_id'])) {
                if (!empty($where['project_id'])) {
                    $this->db->where('pwe.project_id', $where['project_id']);
                }
                unset($where['project_id']);
            }

            if (isset($where['workflow_id'])) {
                if (!empty($where['workflow_id'])) {
                    $this->db->where('pwe.workflow_id', $where['workflow_id']);
                }
                unset($where['workflow_id']);
            }

            if (!empty($where)) {
                $this->db->where($where);
            }

            $query 			  = $this->db->from('project_workflow_entries `pwe`')->count_all_results();
            $results['total'] = !empty($query) ? $query : 0;
            $limit 			  = (!empty($limit > 0)) ? $limit : $results['total'];
            $results['pages'] = !empty($query) ? ceil($query / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }


    /*
    *	Delete Project Workflow Profile
    */
    public function delete_workflow_entry($account_id = false, $workflow_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($workflow_id)) {
            $data = [ 'archived'=>1 ];
            $this->db->where('workflow_id', $workflow_id)
                    ->update('project_workflow_entries', $data);
            if (($this->db->trans_status() !== false) && ($this->db->affected_rows() > 0)) {
                $this->session->set_flashdata('message', 'Project Workflow entry deleted successfully.');
                $result = true;
            } else {
                $this->session->set_flashdata('message', 'No Project Workflow  entry has been deleted.');
                $result = false;
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
            $result = false;
        }
        return $result;
    }
}
