<?php

namespace App\Controllers\Web;

use App\Extensions\MX\Controller as MX_Controller;
class Stats extends MX_Controller
{
    public $enabled_tables = array(
        "Jobs Table" => "jobs_day_view",
        //"Full Table" => "job"
    );

    public $conn_host 	= "localhost";
    public $conn_user 	= "ssid_admin";
    public $conn_pass 	= "q7ybSs3Td42XxMK1";
    public $conn_db 	= "evident_myevident";

    public function index()
    {
        $select_context['enabled_tables'] = $this->enabled_tables;

        $this->_render_webpage('stats/pivot_select', $select_context);
    }

    public function displaypivot()
    {
        $param = $_GET;

        $pivot_context["table_name"] = (empty($param["table_name"])) ? die("No Table was selected!") : (in_array($param["table_name"], $this->enabled_tables) ? $param["table_name"] : die("You do not have access to this table"));

        $columns = array();

        foreach ($_GET as $key => $value) {
            if (!($key == "table_name") && (strpos($key, 'col_') !== false)) {
                array_push($columns, explode("col_", $key)[1]);
            }
        }




        $pivot_context["columns"] 	= $columns;

        $pivot_context["conn_host"] = $this->conn_host;
        $pivot_context["conn_user"] = $this->conn_user;
        $pivot_context["conn_pass"] = $this->conn_pass;
        $pivot_context["conn_db"]   = $this->conn_db;

        $this->load->view('stats/pivot_table', $pivot_context);
    }


    public function fetch($getType = false)
    {
        $this->load->model('Stats_model');

        $this->Stats_model->fetchAPI($_GET, $getType);
    }
}
