<?php

namespace Application\Modules\Service\Models;

class Diary_Date_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /*
    * Get Diary-Dates single records or multiple records
    */
    public function get_diary_dates($account_id=false, $diary_date_id = false, $diary_date=false, $where=false, $offset=0, $limit=20)
    {
        $result = false;

        if ($account_id) {
            $this->db->where('diary_dates.account_id', $account_id);
            if ($diary_date_id) {
                $row = $this->db->get_where('diary_dates', ['diary_date_id'=>$diary_date_id])->row();
                if (!empty($row)) {
                    $this->session->set_flashdata('message', 'Diary-Date record found');
                    $result = $row;
                } else {
                    $this->session->set_flashdata('message', 'Diary-Date record not found');
                }
                return $result;
            }

            if ($diary_date) {
                $diary_date = format_datetime_db($diary_date);
                $this->db->where('diary_date', $diary_date);
            }

            if (isset($where['date_from']) && !empty($where['date_from'])) {
                $date_from 	= date('Y-m-d', strtotime($where['date_from']));
                $date_to 	= (!empty($where['date_to'])) ? date('Y-m-d', strtotime($where['date_to'])) : date('Y-m-d');
                $this->db->where('diary_date >=', $date_from);
                $this->db->where('diary_date <=', $date_to);
                unset($where['date_from'],$where['date_to']);
            }

            $query = $this->db->order_by('diary_date')
                ->offset($offset)
                ->limit($limit)
                ->get('diary_dates');

            if ($query->num_rows() > 0) {
                $this->session->set_flashdata('message', 'Diary-Date records found');
                $result = $query->result();
            } else {
                $this->session->set_flashdata('message', 'Diary-Date record(s) not found');
            }
        }

        return $result;
    }

    /*
    * Create new Diary date resource
    */
    public function create_diary_date($account_id=false, $diary_date_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($diary_date_data)) {
            $data 	 = [];
            $options = (isset($diary_date_data['options'])) ? $diary_date_data['options'] : null;
            unset($diary_date_data['options']);
            foreach ($diary_date_data as $key=>$value) {
                if (in_array($key, format_date_columns())) {
                    $value = format_datetime_db($value);
                } else {
                    $value  = trim($value);
                }
                $data[$key] = $value;
            }

            if (!empty($data)) {
                #Check if it's bulk insert
                if (!empty($options)) {
                    $from_date = !empty($options['from_date']) ? date('Y-m-d', strtotime($options['from_date'])) : date('Y-m-d');
                    $to_date   = !empty($options['to_date']) ? date('Y-m-d', strtotime($options['to_date'])) : $from_date;
                    $exclusions= !empty($options['exclude']) ? $options['exclude'] : [];

                    ## Prepare the dates correct dates for the Diary Slots
                    $new_dates = [];
                    while ($from_date <= $to_date) {
                        if (!in_array(strtolower(date('D', strtotime($from_date))), array_map('strtolower', $exclusions))) {
                            $data['diary_date'] = date('Y-m-d', strtotime($from_date));
                            $new_dates[] = $this->insert_diary_dates($account_id, $data);
                        }
                        $from_date = date("Y-m-d", strtotime($from_date. "+1 day"));
                    }
                    $result = $new_dates;
                } else {
                    $result = $this->insert_diary_dates($account_id, $data);
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Diary-Date data supplied.');
        }
        return $result;
    }

    /*
    * Update Diary-Date record
    */
    public function update_job($account_id=false, $diary_date_id = false, $diary_date_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($diary_date_id) && !empty($diary_date_data)) {
            $data = [];
            foreach ($diary_date_data as $key=>$value) {
                if (in_array($key, format_date_columns())) {
                    $value = format_datetime_db($value);
                } else {
                    $value = trim($value);
                }
                $data[$key] = $value;
            }

            if (!empty($data)) {
                $data['last_modified'] 	 		= date('Y-m-d H:i:s');
                $this->db->where('diary_date_id', $diary_date_id)->update('customer_jobs', $data);
                if ($this->db->trans_status() !== false) {
                    $result = $this->get_diary_dates($account_id, false, $diary_date_id);
                    $this->session->set_flashdata('message', 'Diary-Date record updated successfully.');
                } else {
                    $this->session->set_flashdata('message', 'There was an Error while trying to upate the Diary-Date record.');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Diary-Date data supplied.');
        }
        return $result;
    }

    /*
    * Delete Diary-Date record
    */
    public function delete_job($account_id = false, $diary_date_id = false)
    {
        $result = false;
        if ($account_id && $diary_date_id) {
            $diary_date_exists = $this->db->get_where('customer_jobs', ['diary_date_id'=>$diary_date_id])->row();

            if (!empty($diary_date_exists)) {
                $data = ['archived'=>1];
                $this->db->where('diary_date_id', $diary_date_id)
                    ->update('customer_jobs', $data);
                if ($this->db->trans_status() !== false) {
                    $this->session->set_flashdata('message', 'Record deleted successfully.');
                    $result = true;
                }
            } else {
                $this->session->set_flashdata('message', 'Invalid Diary-Date ID.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Diary-Date ID supplied.');
        }
        return $result;
    }

    /**
    * Update Diary slots
    */
    public function update_slots($account_id = false, $diary_date = false, $slots = 0, $type = 'add')
    {
        $result = false;

        if ($account_id && $diary_date && ($slots > 0)) {
            $current_date = $this->db->get_where('diary_dates', ['account_id'=>$account_id,'diary_date'=>$diary_date])->row();
            if (!empty($current_date)) {
                $data = [
                    'used_slots'=>($type == 'add') ? ($current_date->used_slots + $slots) : ($current_date->used_slots - $slots),
                    'last_modified_by'=>$this->ion_auth->_current_user->id
                ];
                $this->db->where(['account_id'=>$account_id,'diary_date'=>$diary_date]);
                $query = $this->db->update('diary_dates', $data);
                $this->session->set_flashdata('message', 'Diary slots updated successfully!');
                $result = true;
            } else {
                //Create a new Diary date then
                $new_diary_date = [
                    'account_id'=>$account_id,
                    'diary_date'=>$diary_date,
                    'created_by'=>$this->ion_auth->_current_user->id
                ];
                $new_date = $this->create_diary_date($account_id, $new_diary_date);
                if ($new_date) {
                    self::update_slots($account_id, $diary_date, $slots, 'add');
                    $this->session->set_flashdata('message', 'Diary slots updated successfully!');
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
    * Add dates the DB table
    **/
    private function insert_diary_dates($account_id = false, $diary_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($diary_data)) {
            $declared_slots 	= (!empty($diary_data['declared_slots'])) ? $diary_data['declared_slots'] : 0;
            $diary_date_exists 	= $this->db->get_where('diary_dates', ['account_id'=>$account_id,'diary_date'=>$diary_data['diary_date']])->row();

            if (!$diary_date_exists) {
                $diary_data['declared_slots'] = $declared_slots;
                $diary_data['created_by'] 	= $this->ion_auth->_current_user->id;
                $this->db->insert('diary_dates', $diary_data);
                if ($this->db->trans_status() !== false) {
                    $diary_data['diary_date_id'] = $this->db->insert_id();
                    $result = $this->get_diary_dates($account_id, $diary_data['diary_date_id']);
                    $this->session->set_flashdata('message', 'Diary-Date record created successfully.');
                }
            } else {
                $diary_data['declared_slots']  = ($declared_slots > 0) ? ($diary_date_exists->declared_slots + $declared_slots) : $diary_date_exists->declared_slots;
                $diary_data['last_modified'] 	 = date('Y-m-d H:i:s');
                $diary_data['last_modified_by']= $this->ion_auth->_current_user->id;
                $this->db->where('diary_date_id', $diary_date_exists->diary_date_id);
                $this->db->update('diary_dates', $diary_data);
                if ($this->db->trans_status() !== false) {
                    $result = $this->get_diary_dates($account_id, $diary_date_exists->diary_date_id);
                    $this->session->set_flashdata('message', 'Diary-Date record updated successfully.');
                }
            }
        }
        return $result;
    }
}
