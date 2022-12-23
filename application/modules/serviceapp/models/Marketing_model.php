<?php

namespace Application\Service\Models;

defined('BASEPATH') || exit('No direct script access allowed');

use System\Core\CI_Model;

class Marketing_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function get_modules($account_id = false)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select("marketing_modules.*", false);
            $this->db->where("marketing_modules.account_id", $account_id);
            $this->db->where("marketing_modules.archived !=", 1);
            $query = $this->db->get("marketing_modules");

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Module(s) found');
            } else {
                $this->session->set_flashdata('message', 'Module(s) not found');
            }
        } else {
            $this->session->set_flashdata('message', 'Required data is missing');
        }
        return $result;
    }
}
