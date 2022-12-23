<?php

namespace App\Libraries;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
*   Name: Coggins Library
*   Author: Evident Software
*   Created: 27.04.2021
*   Description: This library has been created to work with the COGGINS API
*/
class Coggins_common
{
    function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->database();
        $this->coggins_api_end_point = COGGINS_API_BASE_URL;
        $this->load = clone load_class('Loader');
    }

    /* Dispatch an api request to the EASELTV API */
    public function api_dispatcher($url_endpoint = false, $data = false, $options = false, $is_get_method = false)
    {
        $result = false;
        if (!empty($url_endpoint)) {
            $url_endpoint = $this->coggins_api_end_point . $url_endpoint;

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
                "Content-Type: application/json",
                "Authorization: Bearer " . COGGINS_TEMP_TOKEN,
                "Cache-control: no-cache"
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

            // this function is called by curl for each header received
            curl_setopt(
                $ch,
                CURLOPT_HEADERFUNCTION,
                function ($curl, $header) use (&$headers) {
                    $len        = strlen($header);
                    $header     = explode(':', $header, 2);
                    if (count($header) < 2) { // ignore invalid headers
                        return $len;
                    }
                    $headers[strtolower(trim($header[0]))] = trim($header[1]);
                    return $len;
                }
            );

            $executed = curl_exec($ch);

            if (0 === strpos(bin2hex($executed), 'efbbbf')) {
                $executed = substr($executed, 3);
            }

            if (strpos($executed, "</pre>") !== false) {
                $executed = explode("</pre>", $executed);
                $result_ar   = ( !empty($executed[1]) ) ? json_decode($executed[1]) : false;
            } else {
                $result_ar   = json_decode($executed);
            }

            if (!empty($headers)) {
                $result['headers']  = $headers;
                $result['executed'] = $result_ar;
            } else {
                $result['executed'] = $result_ar;
            }

            curl_close($ch);
        }

        return $result;
    }


    /*
    *   Prepare post data to use for cURL
    */
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

    /*
    *   Filter table data
    */
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
}
