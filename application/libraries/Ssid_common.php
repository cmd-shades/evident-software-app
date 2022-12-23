<?php

namespace App\Libraries;

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
* Name:  SSIDCommon
* Author: Love Digital TV
* Created:  10.10.2017
* Description:  This is library for commonly used functionality in the system.
*/
class Ssid_common
{
    function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->database();
        $this->api_end_point = api_end_point();
        $this->load = clone load_class('Loader');
    }

    public function doCurl($url = false, $postdata = false, $options = array())
    {
        $result = false;
        if ($url && $postdata) {
            if (!empty($options['auth_token'])) {
                $http_headers = array(
                    "authorization: Bearer " . $options['auth_token'],
                    "cache-control: no-cache"
                );
            } else {
                $http_headers = array(
                    "Cache-control: no-store, no-cache, must-revalidate",
                );
            }
            $postdata = urldecode($postdata);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $http_headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_REFERER, $url);
            if (isset($options['method']) && strtolower($options['method']) == 'get') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($ch, CURLOPT_URL, "$url?$postdata");
                curl_setopt($ch, CURLOPT_HTTPGET, 1);
                curl_setopt($ch, CURLOPT_POST, 0);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
                curl_setopt($ch, CURLOPT_POST, 1);
            }

            $executed = curl_exec($ch);

            if (0 === strpos(bin2hex($executed), 'efbbbf')) {
                $executed = substr($executed, 3);
            }

            if (strpos($executed, "</pre>") !== false) {
                $executed = explode("</pre>", $executed);
                $result   = ( !empty($executed[1]) ) ? json_decode($executed[1]) : false;
            } else {
                $result   = json_decode($executed);
            }

            curl_close($ch);
        }

        return $result;
    }


    public function check_dates($date_from = false, $date_to = false)
    {

        $today      = date('Y-m-d');
        $date_from  = date('Y-m-d', strtotime(str_replace('/', '-', $date_from)));
        $date_to    = date('Y-m-d', strtotime(str_replace('/', '-', $date_to)));
        if (!empty($date_from) && !empty($date_to)) {
            if (( ( $date_from < $today ) || ( $date_to < $today ) ) || ( $date_to < $date_from )) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }


    public function date_difference($date1 = false, $date2 = false)
    {
        if (!empty($date1) && !empty($date2)) {
            $datetime1 = new DateTime($date1);
            $datetime2 = new DateTime($date2);

            $difference = $datetime1->diff($datetime2);
            $days_total = $difference->d;
            return (string)( $days_total + 1 );
        } else {
            return false;
        }
    }

    /* Prepare post data to use for cURL */
    public function _prepare_curl_post_data($post_data = false)
    {

        $result = '';
        if (!empty($post_data)) {
            reset($post_data);
            $first_key = key($post_data);

            foreach ($post_data as $column => $value) {
                $value = ( in_array($column, ['password', 'password_confirm']) ) ? $value : $this->clean_htmlentities($value);

                if ($first_key == $column) {
                    $value = ( is_array($value) ) ? json_encode($value) : $value;
                    $result .= $column . "=" . $value;
                } else {
                    $value = ( is_array($value) ) ? json_encode($value) : $value;
                    $result .= "&" . $column . "=" . $value;
                }
            }
            $result = urlencode(( $result ));
        }

        return $result;
    }

    /*
    *   This is to clean the incoming data from the forms.
    *   It should successfully clean nested arrays with many levels
    */
    public function clean_htmlentities($value = false)
    {

        if (empty($value)) {
            return $value;
        } else {
            if (is_array($value)) {
                foreach (array_keys($value) as $key) {
                    $value[$key] = $this->clean_htmlentities($value[$key]);
                }
            } elseif (is_scalar($value)) {
                $json = ( json_decode($value) !== null ) ? json_decode($value) : $value ;

                if (is_array($json)) {
                    $value = json_encode($this->clean_htmlentities($json));
                } else {
                    $value = urlencode(htmlentities($value));
                }
            } else {
                $value = $value;
            }
            return $value;
        }
    }

    /* Filter table data */
    public function _filter_data($table, $data, $exempt_columns = false)
    {
        $filtered_data = array();
        $columns = $this->ci->db->list_fields($table);

        if (is_array($data)) {
            foreach ($columns as $column) {
                if (!empty($exempt_columns) && is_array($exempt_columns)) {
                    if (array_key_exists($column, $data) && ( !in_array($column, $exempt_columns) )) {
                        $filtered_data[$column] = $data[$column];
                    }
                } else {
                    if (array_key_exists($column, $data)) {
                        $filtered_data[$column] = $data[$column];
                    }
                }
            }
        } elseif (is_object($data)) {
            foreach ($columns as $column) {
                if (!empty($exempt_columns) && is_array($exempt_columns)) {
                    if (array_key_exists($column, $data) && ( !in_array($column, $exempt_columns) )) {
                        $filtered_data[$column] = $data->$column;
                    }
                } else {
                    if (array_key_exists($column, $data)) {
                        $filtered_data[$column] = $data->$column;
                    }
                }
            }
        }
        return $filtered_data;
    }

    public function get_site_compliance($site_count = 0, $statuses = false)
    {
        $result = [
            'total_sites' => $site_count,
            'sites_ok' => 0,
            'sites_not_ok' => 0,
            'compliance' => 0
        ];
        if (( $site_count > 0 ) && $statuses) {
            $okay_statuses = site_ok_statuses();
            $sites_okay        = [];

            foreach ($statuses as $status) {
                if (in_array(strtolower($status), array_map('strtolower', $okay_statuses))) {
                    $sites_okay[] = $status;
                }
            }

            if (count($sites_okay) == $site_count) {
                $result['sites_ok']     = count($sites_okay);
                $result['sites_not_ok'] = 0;
                $result['compliance']   = ( count($sites_okay) / $site_count ) * 100;
            } else {
                $result['sites_ok']     = count($sites_okay);
                $result['sites_not_ok'] = $site_count - count($sites_okay);
                $result['compliance']   = ( count($sites_okay) / $site_count ) * 100;
            }
        }
        return $result;
    }

    /*
    * Reset the auto increment of a table after deleting some rows
    */
    public function _reset_auto_increment($table = false, $ai_column = false)
    {
        if (!empty($table) && !empty($ai_column)) {
            $ai     = 1;
            $query  = $this->ci->db->select($ai_column)
                ->order_by($ai_column, 'desc')
                ->limit(1)
                ->get($table);
            if ($query->num_rows() > 0) {
                $highest_record = $query->result()[0];
                $ai             = ( $highest_record->$ai_column ) + 1 ;
            }

            $sql = "ALTER table " . $table . " AUTO_INCREMENT = " . $ai;
            $this->ci->db->query($sql);
        }
        return true;
    }

    /**
    * Generate random password for signups
    **/
    public function generate_random_password()
    {
        $allowed_str  = '!*abcdefghijklmnopqrstuvwxyz@ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890#$_';
        $password     = array();
        $alpha_length = strlen($allowed_str) - 1;
        ;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alpha_length);
            $password[] = $allowed_str[$n];
        }
        return implode($password);
    }

    /*
    * Encode activation code
    */
    public function _encode_activation_code($data = false)
    {
        $activation_code = false;
        if (!empty($data)) {
            $activation_code = urlencode(base64_encode(base64_encode(json_encode($data))));
        }
        return $activation_code;
    }

    /*
    * Decode activation code
    */
    public function _decode_activation_code($activation_code = false)
    {
        $data = false;
        if (!empty($activation_code)) {
            $data = json_decode(base64_decode(base64_decode(urldecode($activation_code))));
        }
        return $data;
    }

    /*
    *   Common function to do an API Call
    */
    public function api_call($url = false, $postdata = false, $method = false)
    {
        $result = false;
        if (!empty($url) && !empty($postdata)) {
            $options['method']      = ( !empty($method) ) ? ( $method ) : 'POST' ;
            $options['auth_token']  = ( !empty($this->ci->session->userdata['auth_data']->auth_token) ) ? $this->ci->session->userdata['auth_data']->auth_token : false ;
            $postdata               = $this->_prepare_curl_post_data($postdata);
            $full_url               = $this->api_end_point . $url;

            $result                 = $this->doCurl($full_url, $postdata, $options);
        }

        return $result;
    }


    /* Get permitted actions */
    public function permitted_actions($permissions = false)
    {
        $result = [];
        if (!empty($permissions)) {
            $permissions = is_object($permissions) ? (array)$permissions : $permissions;
            foreach ($permissions as $k => $action) {
                $role     = explode('_', $action);
                $result[] = $role[1];
            }
        }
        return $result;
    }

    /** Get list of class methods **/
    public function get_controller_methods($controller_name = false)
    {
        $class_methods = null;
        if (!empty($controller_name)) {
            $class_methods = get_class_methods($controller_name);
        }
        return $class_methods;
    }

    /*
    * Get list of countries (or single by ID or code )
    */
    public function get_countries($country_id = false, $country_code = false, $iso3 = false)
    {
        $result = null;
        if ($country_id) {
            $this->ci->db->where('country_id', $country_id);
        }

        if ($country_code) {
            $this->ci->db->where('country_code', $country_code);
        }

        if ($iso3) {
            $this->ci->db->where('iso3', $iso3);
        }

        $query  = $this->ci->db
            ->order_by('country_name')
            ->get('countries');

        if ($query->num_rows() > 0) {
            if ($country_id) {
                $result = $query->result()[0];
            } else {
                $result = $query->result();
            }
        }
        return $result;
    }

    /** Create PDF Document
    /* @html_content can be a path to a view to render or an html string
    /* @$setup this array contains data need for setting up some common info Recipient details, company details, reference numbers etc.
    /* @is)template if @html_content is a file path, this value needs to be set to true, otherwise leave as false
    **/
    public function create_pdf($html_content = false, $setup = false, $is_template = false)
    {

        if (!empty($html_content) && !empty($setup)) {
            if ($is_template) {
                ob_start();
                $this->load->view($html_content, $setup);
                $setup['document_setup']['document_content'] = ob_get_contents();
                ob_end_clean();
            } else {
                $setup['document_setup']['document_content'] = $html_content;
            }

            ob_start();
            $this->load->view('/pdf-templates/generic-template', $setup);
            $html_content = ob_get_contents();
            ob_end_clean();

            $data = [
                'html_content'      => $html_content,
                'document_setup'    => $setup['document_setup']
            ];

            $this->load->view('/pdf-templates/pdf_creater.php', $data);
        }
    }


    public function check_reference($account_id = false, $reference = false, $module = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($reference) && !empty($module)) {
            $table = $column = [];

            switch ($module) {
                case "content_provider_reference_code":
                    $table      = "content";
                    $column     = "content_provider_reference_code";
                    break;

                case "content":
                    $table      = "content_film";
                    $column     = "asset_code";
                    break;

                case "provider":
                    $table      = "content_provider";
                    $column     = "provider_reference_code";
                    break;

                case "site":
                    $table      = "site";
                    $column     = "site_reference_code";
                    break;

                case "systems":
                    $table      = "product_system_type";
                    $column     = "system_reference_code";
                    break;

                case "product":
                    $table      = "product";
                    $column     = "product_reference_code";
                    break;

                case "airtime_product":
                    $table      = "product";
                    $column     = "airtime_pin";
                    break;
            }

            if (!empty($table) && !empty($column)) {
                $this->ci->db->select("*", false);
                // $this->ci->db->like( $column, $reference, 'both', false );
                $this->ci->db->where($column, $reference);
                $query = $this->ci->db->get($table);

                if ($query->num_rows() > 0) {
                    $result = $query->result();
                }
            }
        }
        return $result;
    }


    public function doc_upload_options($account_id = false, $module_name = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($module_name)) {
            $query = $this->ci->db->where('account_id', $account_id)
                ->where('module_name', $module_name)
                ->where('is_active', 1)
                ->get('document_upload_configs');

            if ($query->num_rows() > 0) {
                $result = $query->result();
            }
        }
        return $result;
    }

    /** Generate a Random String / Number **/
    function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces [] = $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    // Create GUID (Globally Unique Identifier)
    function create_guid()
    {
        $guid = '';
        $namespace = rand(11111, 99999);
        $uid = uniqid('', true);
        $data = $namespace;
        $data .= $_SERVER['REQUEST_TIME'];
        $data .= !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown-User-Agent';
        $data .= $_SERVER['REMOTE_ADDR'];
        $data .= $_SERVER['REMOTE_PORT'];
        $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
        $guid = substr($hash, 0, 8) . '-' .
                substr($hash, 8, 4) . '-' .
                substr($hash, 12, 4) . '-' .
                substr($hash, 16, 4) . '-' .
                substr($hash, 20, 12);
        return strtolower($guid);
    }
}
