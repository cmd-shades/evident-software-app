<?php

namespace Application\Modules\Service\Models;

use App\Adapter\Model;

class WorkforcemanagerModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /*
    * Create new Engineer Profile
    */
    public function add_profile($account_id = false, $user_id = false, $post_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($user_id) && !empty($post_data)) {
            $operative_exists = $this->db->get_where('operative_profile', ['user_id' => $user_id, 'archived !=' => 1  ])->row();
            if (!$operative_exists) {
                $data = [];
                foreach ($post_data as $key => $value) {
                    if (in_array($key, format_boolean_columns())) {
                        $value = format_boolean($value);
                    } elseif (in_array($key, format_number_columns())) {
                        $value = format_number($value);
                    } else {
                        $value = trim($value);
                    }
                    $data[$key] = $value;
                }

                $data['created_datetime'] 	= date('Y-m-d H:i:s');
                $data['created_by'] 		= $this->ion_auth->_current_user()->id;

                $this->db->insert('operative_profile', $data);

                if (($this->db->trans_status() !== false) && ($this->db->affected_rows() > 0)) {
                    $data['profile_id'] = $this->db->insert_id();
                    $this->session->set_flashdata('message', 'Operative profile has been created successfully.');
                    $result = $data;
                }
            } else {
                $this->session->set_flashdata('message', 'This User already exists as an Operative.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Operative data supplied.');
        }
        return $result;
    }


    /*
    *	Get Operative record(s)
    */
    public function get_profile($account_id = false, $profile_id = false, $where = false, $limit = 50, $offset = false)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select("op.*, CONCAT( u.first_name, ' ', u.last_name ) `full_name`");
            $this->db->select("u.email, u.phone");

            if (!empty($profile_id)) {
                $this->db->where("op.profile_id", $profile_id);
            }

            if (!empty($where)) {
                if (is_object($where)) {
                    $where = get_object_vars($where);
                }

                $this->db->where($where);
            }

            $this->db->join("user u", "u.id = op.user_id", "left");

            $arch_where = "( op.archived != 1 or op.archived is NULL )";
            $this->db->where($arch_where);

            $this->db->where("op.account_id", $account_id);

            $query = $this->db->get("operative_profile `op`", $limit, $offset);

            if (!empty($query->num_rows()) && ($query->num_rows() > 0)) {
                $result 	= $query->result();
                $this->session->set_flashdata('message', 'Profile(s) data found.');
            } else {
                $this->session->set_flashdata('message', 'Profile(s) data not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account details provided.');
        }

        return $result;
    }



    /*
    *	Update profile
    */
    public function update_profile($account_id = false, $profile_id = false, $profile_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($profile_id) && !empty($profile_data)) {
            $data = [];
            foreach ($profile_data as $key=>$value) {
                if (in_array($key, format_name_columns())) {
                    $value = format_name($value);
                } elseif (in_array($key, format_email_columns())) {
                    $value = format_email($value);
                } elseif (in_array($key, format_number_columns())) {
                    $value = format_number($value);
                } elseif (in_array($key, format_boolean_columns())) {
                    $value = format_boolean($value);
                } else {
                    $value = trim($value);
                }
                $data[$key] = $value;
            }

            if (!empty($data)) {
                $this->db->where('profile_id', $profile_id)->update('operative_profile', $data);
                if (($this->db->trans_status() !== false) && ($this->db->affected_rows() > 0)) {
                    $this->session->set_flashdata('message', 'Operative Profile updated successfully.');
                    $result = $data;
                } else {
                    $this->session->set_flashdata('message', 'The profile hasn\'t been changed.');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Account ID, no Profile Id or no new data supplied.');
        }
        return $result;
    }


    /*
    *	Delete Operative Profile
    */
    public function delete_profile($account_id = false, $profile_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($profile_id)) {
            $data = ['archived'=>1];
            $this->db->where('profile_id', $profile_id)
                ->update('operative_profile', $data);
            if (($this->db->trans_status() !== false) && ($this->db->affected_rows() > 0)) {
                $this->session->set_flashdata('message', 'Operative Profile deleted successfully.');
                $result = true;
            } else {
                $this->session->set_flashdata('message', 'No Profile has been deleted.');
                $result = false;
            }
        } else {
            $this->session->set_flashdata('message', 'No Operative ID supplied.');
            $result = true;
        }
        return $result;
    }
}
