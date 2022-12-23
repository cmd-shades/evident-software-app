<?php

namespace App\Libraries;

/**
* Name:  EASEL TV Library
* Author: Evident Software
* Created:  18.06.2020
* Description:  This is library for processing all EASEL TV Api related calls.
*/
class Easel_tv_common
{
    function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->database();
        $this->easel_api_end_point = EASEL_TV_API_BASE_URL;
        $this->load = clone load_class('Loader');
    }

    /* Dispatch an api request to the EASELTV API */
    public function api_dispatcher($url_endpoint = false, $data = false, $options = false, $is_get_method = false)
    {
        $result = false;
        if (!empty($url_endpoint)) {
            $url_endpoint = $this->easel_api_end_point . $url_endpoint;

            if ($is_get_method) {
                if ($options) {
                    $options = array_merge($options, ['method' => 'GET']);
                } else {
                    $options = ['method' => 'GET'];
                }
            } else {
                $options['method'] = !empty($options['method']) ? $options['method'] : 'POST';
            }

            #$postdata = ( !empty( $data ) ) ? $this->_prepare_curl_post_data( $data ) : false;
            $postdata = ( is_array($data) ) ? json_encode($data) : $data;
            $result   = $this->doCurl($url_endpoint, $postdata, $options);
        }

        return $result;
    }


    public function doCurl($url = false, $postdata = false, $options = array())
    {
        $result = false;
        if ($url) {
            $http_headers = array(
                ## 'Authorization: Basic '. base64_encode( EASEL_TV_API_AUTH_STRING ) //- this is the previous version. Left for the reference (25/08/2022)
                'Content-Type:application/json',
                'api-key:' . EASEL_UAT_API_KEY       // UAT
                // 'api-key:'. EASEL_PROD_API_KEY       // Production

            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $http_headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_REFERER, $url);
            curl_setopt($ch, CURLOPT_URL, $url);

            if (!empty($postdata)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
            }

            ## Switch over the Method
            switch (strtolower($options['method'])) {
                case 'get':
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                    curl_setopt($ch, CURLOPT_POST, 0);
                    break;
                case 'post':
                    curl_setopt($ch, CURLOPT_POST, 1);
                    break;
                case 'put':
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                    break;
                case 'delete':
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                    break;
                default:
                    curl_setopt($ch, CURLOPT_POST, 1);
                    break;
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
    /* @$setup this array contains data need for setting up some common info Recipient details, company details, referrence numbers etc.
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
                $this->ci->db->like($column, $reference, 'both', false);
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
}
