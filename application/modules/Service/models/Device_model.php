<?php

namespace Application\Modules\Service\Models;

use System\Core\CI_Model;

class Device_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("serviceapp/Easel_Api_model", "easel_service");

        $section            = explode("/", $_SERVER["SCRIPT_NAME"]);
        if (!isset($section[1]) || empty($section[1]) || (!(is_array($section)))) {
            $this->app_root = substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), "application"));
        } else {
            if (!isset($_SERVER["DOCUMENT_ROOT"]) || (empty($_SERVER["DOCUMENT_ROOT"]))) {
                $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . '/../');
            }

            $this->section      = $section;
            $this->app_root     = $_SERVER["DOCUMENT_ROOT"] . "/" . $section[1] . "/";
            $this->app_root     = str_replace('/index.php', '', $this->app_root);
        }
    }


    /**
    *   Searchable fields
    **/
    private $device_searchable_fields       = ['device.device_unique_id', 'product.product_name' ];


    /*
    *   Look for devices
    */
    public function devices_lookup($account_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            $search_term = $device_unique_id = $site_id = $product_id = false;

            $devices_allowed_actions = ['linked_to_unlink', 'unlinked_to_link'];

            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where)) {
                    if (!empty($where['search_term'])) {
                        $search_term = $where['search_term'];
                    }
                    unset($where['search_term']);

                    if (!empty($where['device_unique_id'])) {
                        $device_unique_id = $where['device_unique_id'];
                        $this->db->where_in("device.device_unique_id", $device_unique_id);
                    }
                    unset($where['device_unique_id']);

                    if (!empty($where['device_id'])) {
                        $device_id = $where['device_id'];
                        $this->db->where_in("device.device_id", $device_id);
                    }
                    unset($where['device_id']);

                    if (!empty($where['site_id'])) {
                        $site_id = $where['site_id'];
                        $this->db->where_in("device.site_id", $site_id);
                    }
                    unset($where['site_id']);

                    if (!empty($where['product_id'])) {
                        $product_id = $where['product_id'];
                        $this->db->where_in("device.product_id", $product_id);
                    }
                    unset($where['product_id']);

                    if (!empty($where['status'])) {
                        $status = $where['status'];
                        if ($status == '!connected') {
                            $this->db->where("device.airtime_status !=", 'connected');
                        } else {
                            $this->db->where("device.airtime_status", $status);
                        }
                    }
                    unset($where['status']);

                    if (!empty($where['action']) && in_array(html_escape(strtolower($where['action'])), $devices_allowed_actions)) {
                        $action = html_escape(strtolower($where['action']));

                        switch ($action) {
                            case "linked_to_unlink":
                                $this->db->where_in("device.airtime_status", "connected");
                                $this->db->where("device.easel_segment_id !=", "");
                                $this->db->where("device.external_reference_id !=", "");
                                break;

                            case "unlinked_to_link":
                                $this->db->where("device.external_reference_id !=", "");
                                $this->db->where_in("device.airtime_status", ["disconnected"]);
                                $this->db->where("product.airtime_segment_ref !=", "");
                                break;

                            default:
                                break;
                        }
                    }
                    unset($where['action']);

                    if (!empty($where)) {
                        $this->db->where($where);
                    }
                }
            }

            $this->db->select('device.*', false);
            $this->db->select('platform.setting_value `platform_name`, platform.value_desc `platform_description`', false);
            $this->db->select('product.product_name', false);

            $this->db->join('setting as `platform`', 'device.platform_id = platform.setting_id', 'left');
            $this->db->join('product', 'device.product_id = product.product_id', 'left');

            $this->db->where('device.account_id', $account_id);

            $arch_where = "( device.archived != 1 or device.archived is NULL )";
            $this->db->where($arch_where);

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->device_searchable_fields as $k => $field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->device_searchable_fields as $k => $field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            $this->db->order_by('device.device_unique_id');

            $this->db->limit($limit, $offset);

            $query = $this->db->get('device');

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Records found.');
            } else {
                $this->session->set_flashdata('message', 'No records found matching your criteria.');
            }
        }

        return $result;
    }

    /*
    *   Get total devices count for the search
    */
    public function get_total_devices($account_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            $search_term = $device_unique_id = $site_id = $product_id = false;

            $devices_allowed_actions = ['linked_to_unlink', 'unlinked_to_link'];

            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where)) {
                    if (!empty($where['search_term'])) {
                        $search_term = $where['search_term'];
                    }
                    unset($where['search_term']);

                    if (!empty($where['device_unique_id'])) {
                        $device_unique_id = $where['device_unique_id'];
                        $this->db->where_in("device.device_unique_id", $device_unique_id);
                    }
                    unset($where['device_unique_id']);

                    if (!empty($where['device_id'])) {
                        $device_id = $where['device_id'];
                        $this->db->where_in("device.device_id", $device_id);
                    }
                    unset($where['device_id']);

                    if (!empty($where['site_id'])) {
                        $site_id = $where['site_id'];
                        $this->db->where_in("device.site_id", $site_id);
                    }
                    unset($where['site_id']);

                    if (!empty($where['product_id'])) {
                        $product_id = $where['product_id'];
                        $this->db->where_in("device.product_id", $product_id);
                    }
                    unset($where['product_id']);

                    if (!empty($where['status'])) {
                        $status = $where['status'];
                        if ($status == '!connected') {
                            $this->db->where("device.airtime_status !=", 'connected');
                        } else {
                            $this->db->where("device.airtime_status", $status);
                        }
                    }
                    unset($where['status']);

                    if (!empty($where['action']) && in_array(html_escape(strtolower($where['action'])), $devices_allowed_actions)) {
                        $action = html_escape(strtolower($where['action']));

                        switch ($action) {
                            case "linked_to_unlink":
                                $this->db->where_in("device.airtime_status", "connected");
                                $this->db->where("device.easel_segment_id !=", "");
                                $this->db->where("device.external_reference_id !=", "");
                                break;

                            case "unlinked_to_link":
                                $this->db->where("device.external_reference_id !=", "");
                                $this->db->where_in("device.airtime_status", ["disconnected"]);
                                $this->db->where("product.airtime_segment_ref !=", "");
                                break;

                            default:
                                break;
                        }
                    }
                    unset($where['action']);

                    if (!empty($where)) {
                        $this->db->where($where);
                    }
                }
            }

            $this->db->select('device.*', false);
            $this->db->select('platform.setting_value `platform_name`, platform.value_desc `platform_description`', false);
            $this->db->select('product.product_name', false);

            $this->db->join('setting as `platform`', 'device.platform_id = platform.setting_id', 'left');
            $this->db->join('product', 'device.product_id = product.product_id', 'left');

            $this->db->where('device.account_id', $account_id);

            $arch_where = "( device.archived != 1 or device.archived is NULL )";
            $this->db->where($arch_where);

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->device_searchable_fields as $k => $field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->device_searchable_fields as $k => $field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            $this->db->order_by('device.device_unique_id');

            $query              = $this->db->count_all_results('device');
            $results['total']   = !empty($query) ? $query : 0;
            $results['pages']   = !empty($query) ? ceil($query / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }





    /**
    *   Process Devices Upload
    **/
    public function upload_devices($account_id = false)
    {
        $result = null;
        if (!empty($account_id)) {
            $uploaddir  = $this->app_root . 'assets' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;

            if (!file_exists($uploaddir)) {
                mkdir($uploaddir);
            }

            $this->db->truncate('devices_tmp_upload');

            for ($i = 0; $i < count($_FILES['upload_file']['name']); $i++) {
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


    public function get_platform($account_id = false, $where = false)
    {
        $result = $organized = false;

        if (!empty($account_id)) {
            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where)) {
                    if (!empty($where['setting_name_group'])) {
                        $setting_name_group = $where['setting_name_group'];
                        $this->db->where("setting_name_group", $setting_name_group);
                        unset($where['setting_name_group']);
                    }

                    if (isset($where['organized'])) {
                        if (!empty($where['organized'])) {
                            $organized = $where['organized'];
                        }
                        unset($where['organized']);
                    }

                    if (!empty($where)) {
                        $this->db->where($where);
                    }
                }
            }

            $this->db->select("setting.setting_id, setting.setting_value, setting.value_desc", false);
            $this->db->join("`setting_name`", "setting_name.setting_name_id = setting.setting_name_id", "left");
            $this->db->where("setting.is_active", 1);
            $this->db->where("( setting.archived != 1 or setting.archived IS NULL )");
            $query  = $this->db->get("setting");

            if ($query->num_rows() > 0) {
                if (!empty($organized) && ($organized == "by_platform")) {
                    foreach ($query->result() as $row) {
                        $result[$row->setting_value] = $row;
                    }
                } else {
                    $result = $query->result();
                }

                $this->session->set_flashdata('message', 'Platform record(s) found');
            } else {
                $this->session->set_flashdata('message', 'Platform record not found');
            }
        } else {
            $this->session->set_flashdata('message', 'Required data is missing');
        }

        return $result;
    }


    /** Process uploaded array **/
    private function _save_temp_data($account_id = false, $raw_data = false)
    {
        $result = null;
        if (!empty($account_id) && !empty($raw_data)) {
            $exists = $new = [];

            ## it is to check if in the TMP table aren't duplicates looking at Product and Site column
            foreach ($raw_data as $k => $record) {
                $record['product_id']           = $record['Product ID'];
                $record['site_id']              = $record['Site ID'];
                $record['platform']             = $record['Platform'];
                $record['device_unique_id']     = $record['Device Unique ID'];
                $record['created_by']           = $this->ion_auth->_current_user->id;

                ## the table is freshly cleaned
                ## we do hope nothing is there, so everything will go to the 'new'
                $check_exists = $this->db->where(['site_id' => $record['Site ID'], 'product_id' => $record['Product ID'] ])
                    ->limit(1)
                    ->get('devices_tmp_upload')
                    ->row();

                if (!empty($check_exists)) {
                    $exists[]   = $this->ssid_common->_filter_data('devices_tmp_upload', $record);
                } else {
                    $new[]      = $this->ssid_common->_filter_data('devices_tmp_upload', $record);
                }
            }

            // Updated existing
            if (!empty($exists)) {
                $this->db->update_batch('devices_tmp_upload', $exists, 'territory_name');
            }

            //Insert new records
            if (!empty($new)) {
                $this->db->insert_batch('devices_tmp_upload', $new);
            }

            $result = ($this->db->affected_rows() > 0) ? true : false;
        }
        return $result;
    }


    /**
    *   Get devices records pending from the upload
    **/
    public function get_pending_upload_records($account_id = false)
    {
        $result = null;
        if (!empty($account_id)) {
            $query = $this->db->get('devices_tmp_upload');

            if ($query->num_rows() > 0) {
                $platform_data = $data = $platform_list = [];

                $platform_data      = $this->get_platform($account_id, ["setting_name_group" => "2_device_platform"]);

                if (!empty($platform_data)) {
                    $platform_list = array_column($platform_data, "setting_value");
                } else {
                    $this->session->set_flashdata('message', 'List of platforms not available');
                    return false;
                }

                foreach ($query->result() as $k => $row) {
                    ## check if unique ID already exists
                    $unique_id_exists   = false;
                    $this->db->where('device.device_unique_id', $row->device_unique_id);
                    $arch_where         = "( ( device.archived != 1 ) || ( device.archived IS NULL ) )";
                    $this->db->where($arch_where);
                    $unique_id_exists   = $this->db->get('device')->row();

                    if (!empty($unique_id_exists->device_unique_id)) {
                        $data['existing-records'][] = (array) $row;
                        continue;
                    } else {
                        ## Verify the Site ID and Product ID exists for that Site
                        $product_n_site_exists = false;
                        $product_n_site_exists = $this->db->get_where("product", ["product_id" => $row->product_id, "site_id" => $row->site_id ])->row();
                        if (!empty($product_n_site_exists)) {
                            ## Allow Device Unique ID to be alphanumeric and have hyphens/dashes
                            // ORIG: if( preg_match( '/^[\w-]+$/', $row->device_unique_id ) ){
                            if (preg_match('/^[a-zA-Z0-9.-]+$/', $row->device_unique_id)) {
                                ## Only accept the following platforms (as set by Easel API) in the following format
                                ##  - tivo, freesat, amazonfiretvone, ios, tvos, android, androidtv, lg, samsungtizen, samsungorsay, roku, web
                                if (in_array($row->platform, $platform_list)) {
                                    $data['new-records'][] = (array) $row;
                                } else {
                                    $data['incorrect_platform'][] = (array) $row;
                                }
                            } else {
                                $data['incorrect_unique_id'][] = (array) $row;
                                continue;
                            }
                        } else {
                            $data['product_or_site_non_exists'][] = (array) $row;
                            continue;
                        }
                    }
                }
                $result = $data;
            }
        }
        return $result;
    }


    /*
    *   Add Batch of the devices
    */
    public function add_device_batch($account_id = false, $device_batch = false)
    {
        $result = $formatted_batch = $batch = $data = false;
        $platform_data = $platform_list = $unique_ids = [];

        if (!empty($account_id) && !empty($device_batch)) {
            $formatted_batch    = object_to_array(json_decode($device_batch));
            $batch              = $this->security->xss_clean($formatted_batch);
            if (!empty($batch)) {
                $platform_data      = $this->get_platform($account_id, ["setting_name_group" => "2_device_platform", "organized" => "by_platform"]);

                if (!empty($platform_data)) {
                    $platform_list = array_column((array) $platform_data, "setting_value");
                } else {
                    $this->session->set_flashdata('message', 'List of platforms not available');
                    return false;
                }

                foreach ($batch as $key => $row) {
                    if ($row['checked'] == 1) {
                        if (in_array($row['platform'], $platform_list)) {
                            $row['account_id']  = $account_id;
                            $row['created_by']  = $this->ion_auth->_current_user->id;
                            $row['platform_id'] = $platform_data[$row['platform']]->setting_id;
                            unset($row['created_date']);

                            $unique_ids[] = $row['device_unique_id'];

                            $row = $this->ssid_common->_filter_data('device', $row);
                            $data[$key]         = $row;
                        }
                    }
                }

                if (!empty($data)) {
                    $this->db->insert_batch("device", $data);

                    if ($this->db->affected_rows() > 0) {
                        if (!empty($unique_ids)) {
                            $result = $this->db->where_in("device_unique_id", $unique_ids)->get("device")->result();
                        } else {
                            $result = true;
                        }
                        $this->session->set_flashdata('message', 'Device Batch processed successfully');
                    } else {
                        $this->session->set_flashdata('message', 'There was an error saving the Device Batch');
                    }
                } else {
                    $this->session->set_flashdata('message', 'There was an error processing the Device Batch');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'Required data is missing');
        }

        return $result;
    }


    /*
    *   Remove Batch from the clearance temporary table
    */
    public function remove_devices_from_tmp($account_id = false, $device_batch = false)
    {
        $result = $formatted_batch = $batch = $data = false;

        if (!empty($account_id) && !empty($device_batch)) {
            $formatted_batch    = object_to_array(json_decode($device_batch));
            $batch              = $this->security->xss_clean($formatted_batch);

            if (!empty($batch)) {
                foreach ($batch as $key => $row) {
                    if ($row['checked'] == 1) {
                        $ids_2_delete[] = $key;
                    }
                }

                if (!empty($ids_2_delete)) {
                    $this->db->where_in("tmp_device_id", $ids_2_delete);
                    $delete_query = $this->db->delete("devices_tmp_upload");

                    $process_entries = $this->db->affected_rows();

                    if ($process_entries == count($ids_2_delete)) {
                        $this->session->set_flashdata('message', 'All entries have been deleted');
                        $result = true;
                    } elseif (($process_entries > 0) && ($process_entries < count($ids_2_delete))) {
                        $this->session->set_flashdata('message', 'Some entries have not been deleted');
                        $result = true;
                    } else {
                        $this->session->set_flashdata('message', 'Entries haven\'t been deleted');
                    }
                } else {
                    $this->session->set_flashdata('message', 'There was an error processing the Clearance Batch');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'Required data is missing');
        }

        return $result;
    }


    /*
    *   Get all device(s) details by specific criteria
    */
    public function get_device($account_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = $organized = false;

        if (!empty($account_id)) {
            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where)) {
                    if (!empty($where['site_id'])) {
                        $site_id = $where['site_id'];
                        $this->db->where('device.site_id', $site_id);
                        unset($where['site_id']);
                    }

                    if (!empty($where['product_id'])) {
                        $product_id = $where['product_id'];
                        $this->db->where_in('device.product_id', $product_id);
                        unset($where['product_id']);
                    }

                    if (!empty($where['device_unique_id'])) {
                        $device_unique_id = $where['device_unique_id'];
                        $this->db->where_in('device.device_unique_id', $device_unique_id);
                        unset($where['device_unique_id']);
                    }

                    if (!empty($where['device_id'])) {
                        $device_id = $where['device_id'];
                        $this->db->where_in('device.device_id', $device_id);
                        unset($where['device_id']);
                    }

                    if (isset($where['airtime_status'])) {
                        if (!empty($where['airtime_status'])) {
                            $airtime_status = $where['airtime_status'];
                            $this->db->where('device.airtime_status', $airtime_status);
                        }
                        unset($where['airtime_status']);
                    }

                    if (isset($where['organized'])) {
                        if (!empty($where['organized'])) {
                            $organized = $where['organized'];
                        }
                        unset($where['organized']);
                    }

                    if (!empty($where)) {
                        $this->db->where($where);
                    }
                }
            }

            $this->db->select("device.*, platform.setting_value `platform_name`, platform.value_desc `platform_description`", false);
            $this->db->select("product.product_id, product.product_name, product.airtime_segment_ref, product.airtime_market_ref", false);

            $this->db->join("setting `platform`", "platform.setting_id = device.platform_id", "left");
            $this->db->join("product", "product.product_id = device.product_id", "left");

            $this->db->where("device.account_id", $account_id);
            $this->db->where("device.active", 1);
            $this->db->where("( device.archived != 1 or device.archived IS NULL )");
            $query  = $this->db->get("device");

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Device record(s) found');
            } else {
                $this->session->set_flashdata('message', 'Device record not found');
            }
        } else {
            $this->session->set_flashdata('message', 'Required data is missing');
        }

        return $result;
    }



    public function delete_device($account_id = false, $device_ids = false)
    {
        $result = [];
        if (!empty($account_id) && !empty($device_ids)) {
            ## how many deleted
            $deleted_count          = 0;
            $device_ids             = json_decode($device_ids);
            $how_many_to_delete     = count($device_ids);

            $update_data = [
                "archived"      => 1,
                "modified_by"   => $this->ion_auth->_current_user->id,
                "active"        => 0,
            ];

            $this->db->where_in("device_id", $device_ids);
            $this->db->where("account_id", $account_id);
            $query = $this->db->update("device", $update_data);

            if ($this->db->trans_status() !== false) {
                $deleted_count                  = $this->db->affected_rows();
                $result["delete_status"]        = true;
                $result["stats"]["to_delete"]   = $how_many_to_delete;
                $result["stats"]["deleted"]     = $deleted_count;
                $this->session->set_flashdata('message', 'Device record(s) deleted successfully.');
            }
        } else {
            $this->session->set_flashdata('message', 'Required data is missing');
        }

        return $result;
    }


    /*
    *   Connect devices on Airtime.
    *   Parameter: site_id
    */
    public function connect_on_airtime($account_id = false, $site_id = false)
    {
        $result = [];
        if (!empty($account_id) && !empty($site_id)) {
            $this->db->where("account_id", $account_id);
            $this->db->where("site_id", $site_id);
            $arch_where = "( ( site.archived != 1 ) || ( site.archived IS NULL ) )";
            $this->db->where($arch_where);
            $site_exists = $this->db->get("site")->row();

            if (!empty($site_exists)) {
                ## devices to be reconnect - cleaning the link / connect error
                $this->db->where("device.account_id", $account_id);
                $this->db->where("device.site_id", $site_id);
                $this->db->where("device.airtime_status !=", 'connected');
                $this->db->where("device.external_reference_id !=", '');
                $this->db->where("device.product_id !=", '');
                $this->db->where("product.airtime_segment_ref !=", '');

                $this->db->join("product", "product.product_id = device.product_id", "left");

                $where_error = '( ( connect_error != "" ) OR ( link_error != "" ) )';
                $this->db->where($where_error);

                $where_dev_arch = '( ( device.archived != 1 ) OR ( device.archived IS NULL ) )';
                $this->db->where($where_dev_arch);

                $devices_query      = $this->db->get("device");

                if ($devices_query->num_rows() > 0) {
                    ## 'Try to reconnect' functionality - possible separate function
                    $devices_processed                  = [];
                    $number_devices_processed           = 0;
                    $devices_processeed_status          = true;
                    $devices_to_be_reconnected          = $devices_query->result();
                    $devices_to_be_reconnected_number   = count($devices_to_be_reconnected);

                    foreach ($devices_to_be_reconnected as $dev_row) {
                        $device_reconnect_message = '';

                        ## this could be done as one, collective call for all devices from that segment to Easel but we do want to see | save | wipe out all individual errors against each device

                        ## doing anything only if there is Easel reference for the device and the Easel segment ID
                        if (!empty($dev_row->external_reference_id) && !empty($dev_row->airtime_segment_ref)) {
                            ## get the segment data - assuming this guy exists on Easel
                            $segment_data = false;
                            $segment_data = $this->easel_service->fetch_segment($account_id, $dev_row->airtime_segment_ref);

                            if (!empty($segment_data)) {
                                $segment_upd_data = [];
                                $segment_upd_data = [
                                    "airtime_segment_ref"   => $dev_row->airtime_segment_ref,
                                    "type"                  => (!empty($segment_data->type)) ? $segment_data->type : '' ,
                                    "name"                  => (!empty($segment_data->name)) ? $segment_data->name : '' ,
                                    "description"           => (!empty($segment_data->description)) ? $segment_data->description : '' ,
                                    "deviceList"            => []
                                ];

                                ## the current list of devices taken from Easel
                                $segment_upd_data["deviceList"]     = (!empty($segment_data->data->deviceList)) ? $segment_data->data->deviceList : [] ;

                                if (!in_array($dev_row->external_reference_id, $segment_upd_data["deviceList"])) {
                                    $segment_upd_data["deviceList"][] = $dev_row->external_reference_id;
                                }

                                $updated_segment                = false;
                                $updated_segment                = $this->easel_service->update_segment($account_id, $segment_upd_data);

                                if ($updated_segment->success !== false) {
                                    $cacti_device_upd_data = [
                                        "airtime_status"    => "connected",
                                        "easel_segment_id"  => $dev_row->airtime_segment_ref,
                                        "connect_error"     => null,
                                        "link_error"        => null,
                                        "modified_by"       => $this->ion_auth->_current_user->id
                                    ];

                                    $device_reconnect_message .= "Device ID:" . ($dev_row->airtime_segment_ref) . "connected on Easel";

                                    $devices_processed[]    = $updated_segment;
                                } else {
                                    ## error linking devices
                                    $cacti_device_upd_data = [
                                        // "airtime_status"     => "disconnected",  ## not changed
                                        // "easel_segment_id"   => NULL,            ## not going to change
                                        "connect_error"     => (!empty($updated_segment->message)) ? $updated_segment->message : 'Connect Error - linking' ,
                                        "link_error"        => (!empty($updated_segment->message)) ? $updated_segment->message : 'Link Error - linking' ,
                                        "modified_by"       => $this->ion_auth->_current_user->id
                                    ];

                                    $device_reconnect_message .= "Device ID:" . ($dev_row->airtime_segment_ref) . "NOT connected on Easel";

                                    $devices_processeed_status = false;
                                }

                                ## update the devices data on CaCTI

                                $this->db->where("device.account_id", $account_id);
                                $this->db->where_in("device.device_id", $dev_row->device_id);
                                $update = $this->db->update("device", $cacti_device_upd_data);

                                if ($this->db->affected_rows() > 0) {
                                    $number_devices_processed += 1;
                                } else {
                                }
                            }
                        }
                    } ## end of foreach

                    $result['items'][$dev_row->device_id]['message'] = $device_reconnect_message;
                }

                $result['stats']['devices_to_reconnect']    = (!empty($devices_to_be_reconnected_number)) ? $devices_to_be_reconnected_number : 0 ;
                $result['stats']['devices_reconnected']     = (!empty($number_devices_processed)) ? $number_devices_processed : 0 ;

                ## devices straight from the upload
                $get_site_devices                   = $this->get_device($account_id, ["site_id" => $site_id, "external_reference_id" => '' ]);

                if (!empty($get_site_devices)) {
                    $devices_to_link                = count($get_site_devices);
                    $devices_linked                 = 0;

                    foreach ($get_site_devices as $row) {
                        $device_cacti_data_message      = '';
                        $device_cacti_data              = [];


                        ## check if the device on Easel exists:
                        $device_exists_on_easel         = false;
                        $device_exists_on_easel         = $this->easel_service->fetch_device($account_id, $row->device_unique_id);



                        ## if the device exists on Easel, take the data from them
                        if ($device_exists_on_easel != false && !empty($device_exists_on_easel->id)) {
                            $device_external_id = $device_cacti_data['external_reference_id'] = $device_exists_on_easel->id;
                            $device_cacti_data['external_platform']     = (!empty($device_exists_on_easel->platform)) ? $device_exists_on_easel->platform : '' ;
                            $device_cacti_data['create_error']          = "";
                            $device_cacti_data['modified_by']           = $this->ion_auth->_current_user->id;
                        } else {
                            ## if the device not exists - try to create the device on Easel (which is the primary action)
                            $create_d_data              = [];
                            $create_d_data              = [
                                "platform"  => $row->platform_name,
                                "id"        => $row->device_unique_id,
                            ];
                            $create_device_on_easel     = false;
                            $create_device_on_easel     = $this->easel_service->create_device($account_id, $create_d_data);

                            if ($create_device_on_easel->success == true && !empty($create_device_on_easel->data->id)) {
                                $device_external_id     = $device_cacti_data['external_reference_id'] = $create_device_on_easel->data->id;
                                $device_cacti_data['external_platform']     = $create_device_on_easel->data->platform;
                                $device_cacti_data['create_error']          = "";
                                $device_cacti_data['modified_by']           = $this->ion_auth->_current_user->id;
                            }
                        }

                        if (!empty($device_cacti_data)) {
                            $query1 = $this->db->update("device", $device_cacti_data, ["device_id" => $row->device_id]);

                            if ($this->db->trans_status() !== false) {
                                $device_cacti_data_message .= "Device ID " . $row->device_id . " has been created or already exists on Easel. ";

                                ## checking if the product has a not empty segment in it
                                if (!empty($row->airtime_segment_ref)) { ## this is taken from Product
                                    ## Get the segment data based on the product ID
                                    $airtime_segment_data   = false;
                                    $airtime_segment_data   = $this->db->get_where("segment", ["product_id" => $row->product_id ])->row();

                                    if (!empty($airtime_segment_data)) {
                                        ## get the list of the devices based on the Easel Segment ID
                                        $this->db->select("external_reference_id", false);
                                        $devices_list_query     = $this->db->get_where("device", ["account_id" => $account_id, "easel_segment_id" => $airtime_segment_data->airtime_segment_ref ])->result();

                                        $devices_list           = [];

                                        if (!empty($devices_list_query)) {
                                            $devices_list       = array_column($devices_list_query, "external_reference_id");
                                            $devices_list[]     = $row->device_unique_id;
                                            $devices_list       = array_unique($devices_list);
                                        } else {
                                            $devices_list[]     = $row->device_unique_id;
                                        }

                                        $segment_data = [
                                            'airtime_segment_ref'   => (!empty($airtime_segment_data->airtime_segment_ref)) ? (string) $airtime_segment_data->airtime_segment_ref : '' ,
                                            'type'                  => (!empty($airtime_segment_data->type)) ? (string) $airtime_segment_data->type : '' ,
                                            'name'                  => (!empty($airtime_segment_data->segment_name)) ? (string) $airtime_segment_data->segment_name : '' ,
                                            'description'           => (!empty($airtime_segment_data->description)) ? $airtime_segment_data->description : '' ,
                                            'deviceList'            => !empty($devices_list) ? array_values($devices_list) : [],
                                        ];

                                        $add_device_to_segment = $this->easel_service->update_segment($account_id, $segment_data);

                                        ## Debugging for the device creation
                                        $device_debugging_data = [
                                            "device_id"     => $row->device_id,
                                            "product_id"    => !empty($row->product_id) ? $row->product_id : 1,
                                            "string_name"   => "device_model Add device to segment response",
                                            "query_string"  => json_encode($add_device_to_segment),
                                        ];
                                        $this->db->insert("tmp_device_debugging", $device_debugging_data);
                                        ## Debugging for the device creation - end

                                        if ($add_device_to_segment->success == true && !empty($add_device_to_segment->data) && (in_array($row->device_unique_id, $add_device_to_segment->data->data->deviceList))) {
                                            ## to clean creation (on Easel) errors
                                            $device_cacti_data_message                  .= "Device ID " . $row->device_id . " has been added to a segment on EASEL. ";
                                            $device_cacti_upd_data                      = [];
                                            $device_cacti_upd_data['create_error']      = "";
                                            $device_cacti_upd_data['easel_segment_id']  = $row->airtime_segment_ref;
                                            $device_cacti_upd_data['airtime_status']    = "connected";
                                            $query2 = $this->db->update("device", $device_cacti_upd_data, ["device_id" => $row->device_id]);

                                            ## Debugging for the device creation
                                            $debug_query = $this->db->last_query();
                                            $device_debugging_data = [
                                                "device_id"     => $row->device_id,
                                                "product_id"    => $row->product_id,
                                                "string_name"   => "device_model UPDATE device query CACTI",
                                                "query_string"  => json_encode($debug_query),
                                            ];
                                            $this->db->insert("tmp_device_debugging", $device_debugging_data);
                                            ## Debugging for the device creation - end

                                            if ($this->db->trans_status() !== false) {
                                                $devices_linked++;

                                                $device_cacti_data_message .= "Device created and connected on Easel";

                                                ## clean any connection errors in the DB:
                                                $query3 = $this->db->update("device", ["connect_error" => ""], ["device_id" => $row->device_id]);
                                            } else {
                                                $query3 = $this->db->update("device", ["connect_error" => "Error updating CaCTi - Device connected to Easel"], ["device_id" => $row->device_id]);
                                                $device_cacti_data_message .= 'Error updating CaCTi - Device connected to Easel';
                                            }
                                        } else {
                                            $query2 = $this->db->update("device", ["connect_error" => "Error connecting the device to the segment\n" . json_encode($add_device_to_segment)], ["device_id" => $row->device_id]);
                                            $device_cacti_data_message .= 'Error connecting the device to the segment.';
                                        }
                                    } else {
                                        $query3 = $this->db->update("device", ["connect_error" => "No segment data for this product (" . $row->product_id . ") found"], ["device_id" => $row->device_id]);
                                        $device_cacti_data_message .= 'No segment data for this product (' . $row->product_id . ') found';
                                    }
                                } else {
                                    $query1 = $this->db->update("device", ["connect_error" => "No segment ID for the Product"], ["device_id" => $row->device_id]);
                                    $device_cacti_data_message .= 'No segment ID for the Product';
                                }
                            } else {
                                $query0 = $this->db->update("device", ["create_error" => "Error updating CaCTi - Device created on Easel"], ["device_id" => $row->device_id]);
                                $device_cacti_data_message .= 'Error updating device on CaCTi';
                            }
                        } else {
                            ## Debugging for the device creation
                            $device_debugging_data = [
                                "device_id"     => $row->device_id,
                                "product_id"    => $row->product_id,
                                "string_name"   => "device_model: error creating a device - Easel response",
                                "query_string"  => json_encode($create_device_on_easel),
                            ];
                            $this->db->insert("tmp_device_debugging", $device_debugging_data);
                            ## Debugging for the device creation - end

                            $queryA = $this->db->update("device", ["create_error" => (!empty($create_device_on_easel->message) ? $create_device_on_easel->message : "Error creating or reading Device on Easel")], ["device_id" => $row->device_id]);
                            $device_cacti_data_message .= (!empty($create_device_on_easel->message) ? $create_device_on_easel->message : "Issue creating or reading Device on Easel");
                        }


                        $result['items'][$row->device_id]['message'] = $device_cacti_data_message;
                    } ## End of foreach

                    $result['stats']['devices_to_link'] = $devices_to_link;
                    $result['stats']['devices_linked']  = $devices_linked;
                } else {
                    $this->session->set_flashdata('message', 'No available devices to connect on Airtime');
                }
            } else {
                $this->session->set_flashdata('message', 'Incorrect Site ID');
            }
        } else {
            $this->session->set_flashdata('message', 'Required data is missing');
        }

        return $result;
    }



    /*
    *   Unlink devices from segments on Airtime (Easel).
    *   Parameter: site_id, device ID(s)
    */
    public function unlink_on_airtime($account_id = false, $devices_data = false)
    {
        $result = [
            "data"          => false,
            "status"        => false,
            "status_msg"    => ""
        ];

        $devices_data = json_decode($devices_data);

        if (!empty($account_id) && !empty($devices_data)) {
            $collectdata = [];

            foreach ($devices_data as $key => $device_row) {
                $collectdata[$device_row->easelSegmentId]['devices_to_unlink_easel_id'][]   = $device_row->external_reference_id;
                $collectdata[$device_row->easelSegmentId]['devices_to_unlink_cacti_id'][]   = $device_row->deviceId;
            }

            if (!empty($collectdata)) {
                $number_segments_to_b_updated   = count($collectdata);
                $number_segments_updated        = 0;
                $number_devices_to_unlink       = 0;
                $number_devices_unlinked        = 0;
                $devices_unlinked_status        = true;
                $devices_unlinked               = [];

                foreach ($collectdata as $easel_segment_id => $data) {
                    ## 1. get the segment data - extract the device id's - preserve the segment data
                    ## Does the segment exists on Easel?
                    $segment_data = false;
                    $segment_data = $this->easel_service->fetch_segment($account_id, $easel_segment_id);

                    if (!empty($segment_data)) {
                        $segment_upd_data = [];
                        $segment_upd_data = [
                            "airtime_segment_ref"   => $easel_segment_id,
                            "type"                  => (!empty($segment_data->type)) ? $segment_data->type : '' ,
                            "name"                  => (!empty($segment_data->name)) ? $segment_data->name : '' ,
                            "description"           => (!empty($segment_data->description)) ? $segment_data->description : '' ,
                            "deviceList"            => []
                        ];

                        ## the current list of devices taken from Easel
                        $devices_current_list = (!empty($segment_data->data->deviceList)) ? $segment_data->data->deviceList : '' ;

                        if (!empty($devices_current_list)) {
                            if (!empty($data['devices_to_unlink_easel_id'])) {
                                ## devices list to be unlinked taken from the front-end form
                                $devices_to_unlink          = $data['devices_to_unlink_easel_id'];

                                $count_devices_to_unlink    = 0;
                                $count_devices_to_unlink    = count($devices_to_unlink);
                                $number_devices_to_unlink   += $count_devices_to_unlink;

                                ## final_list_of devices to be send to Easel
                                $segment_upd_data["deviceList"] = array_values(array_diff($devices_current_list, $devices_to_unlink));

                                $updated_segment = false;
                                $updated_segment = $this->easel_service->update_segment($account_id, $segment_upd_data);
                                $cacti_device_upd_data = [];

                                if ($updated_segment->success !== false) {
                                    $cacti_device_upd_data = [
                                        "airtime_status"    => "disconnected",
                                        "easel_segment_id"  => null,
                                        "disconnect_error"  => null,
                                        "unlink_error"      => null,
                                        "modified_by"       => $this->ion_auth->_current_user->id
                                    ];

                                    $devices_unlinked[] = $segment_upd_data;
                                } else {
                                    ## error unlinking devices
                                    $cacti_device_upd_data = [
                                        // "airtime_status"     => "disconnected",  ## not changed
                                        // "easel_segment_id"   => NULL,            ## not going to change
                                        "disconnect_error"  => (!empty($updated_segment->message)) ? $updated_segment->message : 'Disconnect Error - unlinking' ,
                                        "unlink_error"      => (!empty($updated_segment->message)) ? $updated_segment->message : 'Unlink Error - unlinking' ,
                                        "modified_by"       => $this->ion_auth->_current_user->id
                                    ];

                                    $devices_unlinked_status = false;
                                }

                                ## if update successful - update the devices on CaCTI
                                $devices_to_update      = $data['devices_to_unlink_cacti_id'];

                                $this->db->where("device.account_id", $account_id);
                                $this->db->where_in("device.device_id", $devices_to_update);
                                $update = $this->db->update("device", $cacti_device_upd_data);

                                if ($this->db->affected_rows() > 0) {
                                    $number_devices_unlinked += $count_devices_to_unlink;
                                } else {
                                }
                            } else {
                                ## update not needed - No devices to be unlink from the call - this case shouldn't happen
                            }
                        } else {
                            ## update not needed - currently the device's list on Easel is empty
                        }
                    } else {
                        ## segment not found - update not possible
                        $this->session->set_flashdata('message', 'Segment not found on Easel');
                        $result['status_msg']   .= 'Segment not found on Easel';
                        $devices_unlinked_status = false;
                    }
                } ## end of foreach

                $message = "";
                $message .= '<span style="font-weight: 800;">' . ((int) $number_devices_unlinked) . '</span> out of <span style="font-weight: 800;">' . ((int) $number_devices_to_unlink) . '</span> devices have been unlinked';
                if (($devices_unlinked_status !== false) && ((int) $number_devices_to_unlink > 0) && ((int) $number_devices_unlinked > 0) && ((int) $number_devices_to_unlink  == (int) $number_devices_unlinked)) {
                    $result['data']         = $devices_unlinked;
                    $result['success']      = true;
                }
                $result['status_msg']   = $message;
                $this->session->set_flashdata('message', $message);
            } else {
                ## error processing devices data
                $this->session->set_flashdata('message', 'Error processing devices data');
                $result['status_msg']   = 'Error processing devices data';
            }
        } else {
            $this->session->set_flashdata('message', 'Required data is missing');
            $result['status_msg']   = 'Required data is missing';
        }

        return $result;
    }


    /*
    *   Reconnect devices to segment
    *   Required:
    *   - present Easel reference for the device
    *   - device status - disconnected
    *   - Easel segment ID in the product present
    */
    public function reconnect_on_airtime($account_id = false, $devices_data = false)
    {
        $result = [
            "success"       => false,
            "data"          => false,
            "status_msg"    => ""
        ];

        if (!empty($account_id) && !empty($devices_data)) {
            $devices_data = json_decode($devices_data);

            if (!empty($devices_data)) {
                foreach ($devices_data as $row) {
                    ## re-arranging the array by the segment ID
                    $to_processed[$row->productId][] = $row;
                }

                if (!empty($to_processed)) {
                    $number_devices_to_link = 0;
                    $number_devices_linked  = 0;
                    $devices_linked_status  = true;
                    $devices_linked         = [];

                    foreach ($to_processed as $product_id => $d_row) {
                        ## convert into array
                        $d_row              = json_decode(json_encode($d_row), true);

                        $device_easel_ids   = false;
                        $device_easel_ids   = single_array_from_arrays($d_row, 'external_reference_id');

                        if (!empty($device_easel_ids)) {
                            $device_cacti_ids   = false;
                            $device_cacti_ids   = single_array_from_arrays($d_row, 'deviceId');

                            $count_devices_to_link      = 0;
                            $count_devices_to_link      = count($device_easel_ids);
                            $number_devices_to_link     += $count_devices_to_link;

                            if (!empty($device_cacti_ids)) {
                                $product_data   = false;
                                $product_data   = $this->db->get_where("product", ["account_id" => $account_id, "product_id" => $product_id])->row();

                                if (!empty($product_data)) {
                                    if (!empty($product_data->airtime_segment_ref)) {
                                        ## get segment ID and data
                                        $airtime_segment_data = false;
                                        $airtime_segment_data = $this->easel_service->fetch_segment($account_id, $product_data->airtime_segment_ref);

                                        if (!empty($airtime_segment_data)) {
                                            ## build segment object with new devices

                                            $segment_upd_data = [];
                                            $segment_upd_data = [
                                                "airtime_segment_ref"   => $product_data->airtime_segment_ref,
                                                "type"                  => (!empty($airtime_segment_data->type)) ? $airtime_segment_data->type : '' ,
                                                "name"                  => (!empty($airtime_segment_data->name)) ? $airtime_segment_data->name : '' ,
                                                "description"           => (!empty($airtime_segment_data->description)) ? $airtime_segment_data->description : '' ,
                                                "deviceList"            => []
                                            ];

                                            ## the current list of devices taken from Easel
                                            $devices_current_list = (!empty($airtime_segment_data->data->deviceList)) ? $airtime_segment_data->data->deviceList : [] ;

                                            ## final_list_of devices to be send to Easel
                                            $segment_upd_data["deviceList"] = array_unique(array_merge($devices_current_list, $device_easel_ids));

                                            $updated_segment = false;
                                            $updated_segment = $this->easel_service->update_segment($account_id, $segment_upd_data);

                                            if ($updated_segment->success !== false) {
                                                $cacti_device_upd_data  = [
                                                    "airtime_status"    => "connected",
                                                    "easel_segment_id"  => $product_data->airtime_segment_ref,
                                                    "connect_error"     => null,
                                                    "link_error"        => null,
                                                    "modified_by"       => $this->ion_auth->_current_user->id
                                                ];

                                                $devices_linked[]       = $segment_upd_data;
                                            } else {
                                                ## error unlinking devices
                                                $cacti_device_upd_data = [
                                                    "connect_error"     => (!empty($updated_segment->message)) ? $updated_segment->message : 'Connect Error - linking' ,
                                                    "link_error"        => (!empty($updated_segment->message)) ? $updated_segment->message : 'Link Error - linking' ,
                                                    "modified_by"       => $this->ion_auth->_current_user->id
                                                ];

                                                $devices_linked_status = false;
                                            }

                                            ## if update successful - update the devices on CaCTI
                                            $this->db->where("device.account_id", $account_id);
                                            $this->db->where_in("device.device_id", $device_cacti_ids);
                                            $update = $this->db->update("device", $cacti_device_upd_data);

                                            if ($this->db->affected_rows() > 0) {
                                                $number_devices_linked += $count_devices_to_link;
                                            } else {
                                            }
                                        } else {
                                            ## no segment exists on the Easel side
                                        }
                                    } else {
                                        ## no airtime segment reference - as this is in the loop - not stopping here
                                    }
                                } else {
                                    ## no product data for the specified product - as this is in the loop - not stopping here
                                }
                            } else {
                                ## no CaCTI device id's to update
                            }
                        } else {
                            ## no Easel device id's to add
                        }
                    } ## end foreach

                    $message = "";
                    $message .= '<span style="font-weight: 800;">' . ((int) $number_devices_linked) . '</span> out of <span style="font-weight: 800;">' . ((int) $number_devices_to_link) . '</span> devices have been linked';
                    if (($devices_linked_status !== false) && ((int) $number_devices_to_link > 0) && ((int) $number_devices_linked > 0) && ((int) $number_devices_to_link  == (int) $number_devices_linked)) {
                        $result['data']         = $devices_linked;
                        $result['success']      = true;
                    }
                    $result['status_msg']   = $message;
                    $this->session->set_flashdata('message', $message);
                } else {
                    $this->session->set_flashdata('message', 'Error processing segment data');
                    $result['status_msg']   = 'Error processing segment data';
                }
            } else {
                $this->session->set_flashdata('message', 'Error processing devices data');
                $result['status_msg']   = 'Error processing devices data';
            }
        } else {
            $this->session->set_flashdata('message', 'Required data is missing');
            $result['status_msg']   = 'Required data is missing';
        }

        return $result;
    }




    /*
    *   Disconnect devices from Airtime.
    *   Parameter: site_id
    */
    public function disconnect_on_airtime($account_id = false, $site_id = false)
    {
        ## This function could be improved - no time for it now. The 'update cacti with some parameters' should be as a separate function.

        ## Summary of the process:
        ## 1. Check if the device exists on Easel
        ## 2. If isn't - update the device: status - disconnected, easel_reference = '', segment = '', unlink_error = '', disconnect_error = ''

        ## otherwise
        ## 3. Get the segment data
        ## 4. Check if the device is in
        ## 5. Device isn't on the list - update the device: status - disconnected, easel_reference = stays the same, segment = '', unlink_error = '', disconnect_error = ''

        ## otherwise
        ## 6. Prepare the update data - remove the device from the devices list
        ## 7. Do the update - if successful (pull the segment data and check if the device is on the list - verify if the device is part of a segment) - Update the device - status - disconnected, easel_reference = stays the same, segment = '', unlink_error = '', disconnect_error = ''

        ## otherwise
        ## 8. Update the device - status - stays the same, easel_reference = stays the same, segment = stays the same, unlink_error = '', disconnect_error = from Easel or 'Couldn't remove the device'

        $result = [
            "success"       => false,
            "data"          => false,
            "status_msg"    => "",
            "stats"         => [
                "devices_to_disconnect"     => 0,
                "devices_disconnected"      => 0
            ]
        ];

        if (!empty($account_id) && !empty($site_id)) {
            $this->db->where("account_id", $account_id);
            $this->db->where("site_id", $site_id);
            $arch_where = "( ( site.archived != 1 ) || ( site.archived IS NULL ) )";
            $this->db->where($arch_where);
            $site_exists = $this->db->get("site")->row();

            if (!empty($site_exists)) {
                $this->db->join("product", "product.product_id = device.product_id", "left");

                ## devices to be disconnected - cleaning the unlink / disconnect error
                $this->db->where("device.account_id", $account_id);
                $this->db->where("device.site_id", $site_id);
                $this->db->where("device.airtime_status", 'connected');
                $this->db->where("device.external_reference_id !=", '');
                $this->db->where("device.product_id !=", '');
                $this->db->where("product.airtime_segment_ref !=", '');

                $where_error = '( ( disconnect_error != "" ) OR ( unlink_error != "" ) )';
                $this->db->where($where_error);

                $where_dev_arch = '( ( device.archived != 1 ) OR ( device.archived IS NULL ) )';
                $this->db->where($where_dev_arch);

                $devices_query      = $this->db->get("device");

                $devices_processeed_status          = true;
                $number_devices_processed           = 0;
                $devices_to_be_disconnected         = 0;
                $devices_to_be_disconnected_number  = 0;
                $devices_processed                  = [];

                if ($devices_query->num_rows() > 0) {
                    $devices_to_be_disconnected         = $devices_query->result();
                    $devices_to_be_disconnected_number  = count($devices_to_be_disconnected);

                    foreach ($devices_to_be_disconnected as $dev_row) {
                        if (!empty($dev_row->external_reference_id)) {
                            ## we do have the Easel reference - we can proceed

                            $device_data_from_Easel = false;
                            $device_data_from_Easel = $this->easel_service->fetch_device($account_id, $dev_row->external_reference_id);

                            if (!empty($device_data_from_Easel->id)) {
                                ## Device does exist on Easel

                                if (!empty($dev_row->airtime_segment_ref)) {
                                    ## if there is a segment in the device - means the process hasn't been finished yet

                                    ## get the segment data - checking if this guy exists on Easel
                                    $segment_data = false;
                                    $segment_data = $this->easel_service->fetch_segment($account_id, $dev_row->airtime_segment_ref);

                                    if (!empty($segment_data->id)) {
                                        ## the guy exists

                                        if (!empty($segment_data->data->deviceList)) {
                                            ## does it have the devices attached?

                                            if (in_array($dev_row->external_reference_id, $segment_data->data->deviceList)) {
                                                ## the device is on Easel inside the segment structure - need to remove it and update the segment

                                                $segment_upd_data = [];
                                                $segment_upd_data = [
                                                    "airtime_segment_ref"   => $dev_row->airtime_segment_ref,
                                                    "type"                  => (!empty($segment_data->type)) ? $segment_data->type : '' ,
                                                    "name"                  => (!empty($segment_data->name)) ? $segment_data->name : '' ,
                                                    "description"           => (!empty($segment_data->description)) ? $segment_data->description : '' ,
                                                    "deviceList"            => []
                                                ];

                                                ## the current list of devices taken from Easel
                                                $segment_upd_data["deviceList"] = array_values(array_diff($segment_data->data->deviceList, [$dev_row->external_reference_id]));

                                                $updated_segment                = false;
                                                $updated_segment                = $this->easel_service->update_segment($account_id, $segment_upd_data);
                                                $device_update_data             = [];

                                                if ($updated_segment->success !== false) {
                                                    $device_update_data = [
                                                        "airtime_status"    => "disconnected",
                                                        "easel_segment_id"  => '',
                                                        "disconnect_error"  => '',
                                                        "unlink_error"      => '',
                                                        "modified_by"       => $this->ion_auth->_current_user->id
                                                    ];
                                                    $number_devices_processed   += 1;
                                                    $devices_processed[]        = $device_update_data;
                                                } else {
                                                    ## error disconnecting devices
                                                    $device_update_data = [
                                                        // "airtime_status"     => "disconnected",  ## not changed
                                                        // "easel_segment_id"   => NULL,            ## not going to change
                                                        "disconnect_error"  => (!empty($updated_segment->message)) ? $updated_segment->message : 'Disconnect Error - disconnecting' ,
                                                        "unlink_error"      => (!empty($updated_segment->message)) ? $updated_segment->message : 'Unlink Error - linking' ,
                                                        "modified_by"       => $this->ion_auth->_current_user->id
                                                    ];

                                                    $devices_processeed_status = false;
                                                }

                                                ## update the devices data on CaCTI
                                                $this->db->where("device.account_id", $account_id);
                                                $this->db->where_in("device.device_id", $dev_row->device_id);
                                                $update = $this->db->update("device", $device_update_data);
                                            } else {
                                                ## the device isn't on Easel - it is not a part of the segment - we need to update it
                                                $device_update_data         = [];
                                                $device_update_data = [
                                                    "easel_segment_id"      => '',
                                                    "disconnect_error"      => '',
                                                    "unlink_error"          => '',
                                                    "modified_by"           => $this->ion_auth->_current_user->id,
                                                ];

                                                $dev_where = [
                                                    "account_id"            => $account_id,
                                                    "device_id"             => $dev_row->device_id
                                                ];

                                                $update_query = $this->db->update("device", $device_update_data, $dev_where);

                                                if ($this->db->trans_status() > 0) {
                                                    $number_devices_processed += 1;
                                                    $devices_processed[]        = $device_update_data;
                                                }
                                            }
                                        } else {
                                            ## no devices  - no need to disconnect - just: clear the disconnect error, clear the segment ID
                                            $device_update_data         = [];
                                            $device_update_data = [
                                                "easel_segment_id"      => '',
                                                "disconnect_error"      => '',
                                                "unlink_error"          => '',
                                                "airtime_status"        => 'disconnected',
                                                "modified_by"           => $this->ion_auth->_current_user->id,
                                            ];

                                            $dev_where = [
                                                "account_id"            => $account_id,
                                                "device_id"             => $dev_row->device_id
                                            ];

                                            $update_query = $this->db->update("device", $device_update_data, $dev_where);
                                            if ($this->db->trans_status() > 0) {
                                                $devices_processed[]        = $device_update_data;
                                                $number_devices_processed += 1;
                                            }
                                        }
                                    } else {
                                        ## The segment does not exist on easel - no response
                                        $device_update_data         = [];
                                        $device_update_data = [
                                            "disconnect_error"      => "Segment doesn't exist on Easel",
                                            "unlink_error"          => "Segment doesn't exist on Easel",
                                            "modified_by"           => $this->ion_auth->_current_user->id,
                                            "airtime_status"        => 'disconnected',
                                        ];

                                        $dev_where = [
                                            "account_id"            => $account_id,
                                            "device_id"             => $dev_row->device_id
                                        ];

                                        $update_query = $this->db->update("device", $device_update_data, $dev_where);
                                        $devices_processeed_status          = false;
                                    }
                                } else {
                                    ## if there is no segment in the device - means the segment is missing from the product or the device hasn't been properly attached to product. Just need the update the device statuses
                                    $device_update_data         = [];
                                    $device_update_data = [
                                        "disconnect_error"      => '',
                                        "unlink_error"          => '',
                                        "modified_by"           => $this->ion_auth->_current_user->id,
                                    ];

                                    $dev_where = [
                                        "account_id"            => $account_id,
                                        "device_id"             => $dev_row->device_id
                                    ];

                                    $update_query = $this->db->update("device", $device_update_data, $dev_where);
                                    if ($this->db->trans_status() > 0) {
                                        $devices_processed[]        = $device_update_data;
                                        $number_devices_processed += 1;
                                    }
                                }
                            } else {
                                ## Device doesn't exist on Easel despite the reference
                                $device_update_data = [];
                                $device_update_data = [
                                    // "external_reference_id" => '',       ## we're not cleaning this - leave the original values here
                                    // "easel_segment_id"       => '',      ## we're not cleaning this - leave the original values here
                                    // "external_platform"  => '',          ## we're not cleaning this - leave the original values here
                                    "disconnect_error"      => 'Disconnect error: Device not found on Easel',
                                    "unlink_error"          => 'Disconnect error: Device not found on Easel',
                                    "airtime_status"        => "disconnected",
                                    "modified_by"           => $this->ion_auth->_current_user->id,
                                ];

                                $dev_where = [
                                    "account_id"            => $account_id,
                                    "device_id"             => $dev_row->device_id
                                ];

                                $update_query = $this->db->update("device", $device_update_data, $dev_where);

                                $devices_processeed_status          = false;
                            }
                        } else {
                            ## no Easel reference  - device is not linked with Easel at all
                            $device_update_data = [];
                            $device_update_data = [
                                // "easel_segment_id"       => '',      ## we're not cleaning this - leave the original values here
                                // "external_platform"      => '',      ## we're not cleaning this - leave the original values here
                                "disconnect_error"      => 'Disconnect error: No Easel ID for the device',
                                "unlink_error"          => 'Disconnect error: No Easel ID for the device',
                                "airtime_status"        => "disconnected",
                                "modified_by"           => $this->ion_auth->_current_user->id,
                            ];

                            $dev_where = [
                                "account_id"            => $account_id,
                                "device_id"             => $dev_row->device_id
                            ];

                            $update_query = $this->db->update("device", $device_update_data, $dev_where);

                            $devices_processeed_status          = false;
                        }
                    } ## End of foreach for the each device
                }
            }
        }

        $result['status_msg']                       = '<b>' . $number_devices_processed . '</b> of <b>' . $devices_to_be_disconnected_number . '</b> devices successfully disconnected from Airtime';
        $this->session->set_flashdata('message', $result['status_msg']);

        $result['success']                          = $devices_processeed_status;
        $result['data']                             = $devices_processed;
        $result['stats']['devices_to_disconnect']   = $devices_to_be_disconnected_number;
        $result['stats']['devices_disconnected']    = $number_devices_processed;
        $result['items']                            = $devices_processed;

        return $result;
    }


    /*
    *   Reallocate the device (single) from Easel's segment to a different segment
    *   ## A slightly different approach than usually - checking if the required data / arguments are present at first.
    */
    public function reallocate($account_id = false, $device_id = false, $product_id = false)
    {
        $result = (object) [
            "status"    => false,
            "data"      => false,
            "message"   => ""
        ];

        if (!empty($account_id)) {
            if (!empty($device_id)) {
                /* ------- checks for the variables - start ------------------- */
                ## 1a Device:
                $device_data = false;
                $device_data = $this->get_device($account_id, ["device_id" => $device_id]);
                $device_data = is_array($device_data) ? current($device_data) : $device_data;

                if (! ($device_data)) {
                    $result->message = "No Device Data can be found in CaCTI. ";
                    return $result;
                }

                ## 1b Easel Segment ID:
                if ((mb_strtolower($device_data->airtime_status) == "connected") && !(isset($device_data->easel_segment_id) && !empty($device_data->easel_segment_id))) {
                    $result->message = "Easel Segment ID not attached to the device. ";
                    return $result;
                }

                ## 1c Easel Segment ID:
                if ((mb_strtolower($device_data->airtime_status) == "disconnected") && (isset($device_data->easel_segment_id) && !empty($device_data->easel_segment_id))) {
                    $result->message = "This device has an Easel segment ID against it. ";
                    return $result;
                }

                ## 1d Easel Segment ID:
                if (!(isset($device_data->external_reference_id) && !empty($device_data->external_reference_id))) {
                    $result->message = "Easel Device ID is missing. ";
                    return $result;
                }

                ## 1e Easel Segment Data:
                $easel_source_segment_data = false;
                if ((mb_strtolower($device_data->airtime_status) == "connected") && (isset($device_data->easel_segment_id) && !empty($device_data->easel_segment_id))) {
                    $easel_source_segment_data = $this->easel_service->fetch_segment($account_id, $device_data->easel_segment_id);
                    if (!(isset($easel_source_segment_data) && !empty($easel_source_segment_data))) {
                        $result->message = "Issue obtaining Easel Source - Segment data. ";
                        return $result;
                    }
                }

                ## 1f Easel Segment Data - Device list:
                ## This one may not be use as a breaking point - temporarily commented out (2022/11/30)
                // if( !( isset( $easel_source_segment_data->data->deviceList ) && !empty( $easel_source_segment_data->data->deviceList ) ) ){
                // $result->message = "No devices found on Easel Source - Segment";
                // return $result;
                // }


                ## 1g Product ID provided:
                if (!($product_id)) {
                    $result->message = "Target Product ID is missing. ";
                    return $result;
                }

                ## 1h target product exists in CaCTI:
                $target_product_exists = false;
                $target_product_exists = $this->db->get_where("product", ["product_id" => $product_id])->row();
                if (!($target_product_exists)) {
                    $result->message = "Target Product does not exists (CaCTI). ";
                    return $result;
                }

                ## 1i target product EASEL segment reference
                if (!(isset($target_product_exists->airtime_segment_ref) && !empty($target_product_exists->airtime_segment_ref))) {
                    $result->message = "Easel Segment ID missing from the target product profile. ";
                    return $result;
                }

                ## 1j target product site ID
                if (!(isset($target_product_exists->site_id) && !empty($target_product_exists->site_id))) {
                    $result->message = "Site ID missing from the target product profile. ";
                    return $result;
                }

                ## 1k target product EASEL segment data
                $easel_target_segment_data = false;
                $easel_target_segment_data = $this->easel_service->fetch_segment($account_id, $target_product_exists->airtime_segment_ref);
                if (!(isset($easel_target_segment_data) && !empty($easel_target_segment_data))) {
                    $result->message = "Issue obtaining Easel Target - Segment data. ";
                    return $result;
                }
                /* ------- checks for the variables - end ------------------- */

                $segment_updated = true;

                if (isset($easel_source_segment_data->data->deviceList) && !empty($easel_source_segment_data->data->deviceList)) {
                    // there is a devices list attached to the segment

                    if (in_array($device_data->external_reference_id, $easel_source_segment_data->data->deviceList)) {
                        // our device is present in the devices list, the remove 'our' device from the segment action is required

                        ## Remove device from the segment action:
                        $source_segment_upd_data        = [];
                        $source_segment_upd_data        = $this->_prepare_data_for_segment_update($device_data->easel_segment_id, $easel_source_segment_data, $device_data->external_reference_id, "remove");

                        $updated_source_segment = false;
                        $updated_source_segment = $this->easel_service->update_segment($account_id, $source_segment_upd_data);

                        log_message("error", json_encode(["updated_segment" => $updated_source_segment]));

                        if ($updated_source_segment->success !== false) {
                            $result->message = "Device successfully removed from segment (Easel). ";
                        } else {
                            $segment_updated = false;

                            // removing the device from the segment object was unsuccessful
                            $result->message = "Error removing device from segment (Easel). ";
                            log_message("error", json_encode(["issue" => "error_reconecting device - Easel Segment update failed", "easel_response" => $updated_source_segment, "source_data" => $source_segment_upd_data]));

                            ## Debugging for the device reallocation
                            $device_debugging_data = [
                                "device_id"     => $device_id,
                                "product_id"    => $product_id,
                                "string_name"   => "device_model - device reallocation - error_reconecting device - Easel Segment update failed - Easel response",
                                "query_string"  => json_encode($updated_source_segment),
                            ];
                            $this->db->insert("tmp_device_debugging", $device_debugging_data);
                            ## Debugging for the device reallocation - end
                            return $result;
                        }
                    } else {
                        $result->message = "Device hasn't been found on Easel's segment. ";
                    }
                } else {
                    ## No devices in the Easel's segment - no need to do the update on Easel, just the update in CaCTI
                    $result->message = "Device has been found on Easel's segment. ";
                }

                $target_segment_upd_data    = [];
                $target_segment_upd_data    = $this->_prepare_data_for_segment_update($target_product_exists->airtime_segment_ref, $easel_target_segment_data, $device_data->external_reference_id, "add");

                $updated_target_segment     = false;
                $updated_target_segment     = $this->easel_service->update_segment($account_id, $target_segment_upd_data);

                log_message("error", json_encode(["updated_target_segment" => $updated_target_segment]));

                if ($updated_target_segment->success !== false) {
                    $device_update_data     = [];
                    $device_update_data     = [
                        "airtime_status"    => "connected",
                        "product_id"        => $product_id,
                        "site_id"           => $target_product_exists->site_id,
                        "easel_segment_id"  => $target_product_exists->airtime_segment_ref,
                        "modified_by"       => $this->ion_auth->_current_user->id,
                        "modified_date"     => date('Y-m-d H:i:s')
                    ];

                    $device_update_where    = [];
                    $device_update_where    = [
                        "account_id"    => $account_id,
                        "device_id"     => $device_id
                    ];

                    $device_update = $this->db->update("device", $device_update_data, $device_update_where);

                    if ($this->db->trans_status() != false || $this->db->affected_rows() > 0) {
                        $result->message        .= "Device successfully added to the target segment (Easel) and updated on CaCTI. ";
                        $result->status         = true;

                        $updated_device_data    = false;
                        $updated_device_data    = $this->get_device($account_id, ["device_id" => $device_id]);
                        $updated_device_data    = is_array($device_data) ? current($device_data) : $device_data;
                        $result->data           = (!empty($updated_device_data)) ? $updated_device_data : false ;
                    } else {
                        $result->message        .= "Device successfully added to the target segment (Easel) but CaCTI update failed. ";
                        $debug_query            = $this->db->last_query();
                        log_message("error", json_encode(["Device reallocation - CaCTI update failed" => $debug_query]));
                    }
                } else {
                    ## revert the changes back to original

                    $revert_segment_upd_data    = [];
                    $revert_segment_upd_data    = $this->_prepare_data_for_segment_update($device_data->easel_segment_id, $easel_source_segment_data, $device_data->external_reference_id, "add");

                    $revert_segment             = false;
                    $revert_segment             = $this->easel_service->update_segment($account_id, $revert_segment_upd_data);

                    if ($revert_segment->status !== false) {
                        $result->message .= "Device not added to the target segment (Easel). All changes have been reverted. ";
                    } else {
                        $result->message .= "Device not added to the target segment (Easel). Reverting changes failed. ";
                        log_message("error", json_encode(["Device reallocation - Revert failed" => $revert_segment]));
                    }

                    ## Debugging for the device reallocation
                    $device_debugging_data = [
                        "device_id"     => $device_id,
                        "product_id"    => $product_id,
                        "string_name"   => "device_model - device reallocation - Device not added to the target segment (Easel). - Easel response",
                        "query_string"  => json_encode($updated_target_segment),
                    ];
                    $this->db->insert("tmp_device_debugging", $device_debugging_data);
                    ## Debugging for the device reallocation - end
                }
            } else {
                $result->message = "No Device ID provided. ";
            }
        } else {
            $result->message = "No Account ID provided. ";
        }

        return $result;
    }


    /*
    *   A helper function to prepare the data for the segment update.
    *   It includes two options - add the device to the segment or remove it.
    */
    public function _prepare_data_for_segment_update($easel_segment_id = false, $easel_source_segment_data = false, $device_easel_id = false, $action = "add")
    {
        $result = false;
        if (!empty($easel_segment_id) && !empty($easel_source_segment_data) && !empty($device_easel_id)) {
            $segment_upd_data       = [
                "airtime_segment_ref"   => $easel_segment_id,
                "type"                  => (!empty($easel_source_segment_data->type)) ? $easel_source_segment_data->type : '' ,
                "name"                  => (!empty($easel_source_segment_data->name)) ? $easel_source_segment_data->name : '' ,
                "description"           => (!empty($easel_source_segment_data->description)) ? $easel_source_segment_data->description : '' ,
            ];

            if (mb_strtolower($action, 'UTF-8') == "add") {
                $segment_upd_data["deviceList"] = (!empty($easel_source_segment_data->data->deviceList)) ? array_values(array_merge($easel_source_segment_data->data->deviceList, [$device_easel_id])) : [$device_easel_id];
            } elseif (mb_strtolower($action, 'UTF-8') == "remove") {
                $segment_upd_data["deviceList"] = (!empty($easel_source_segment_data->data->deviceList)) ? array_values(array_diff($easel_source_segment_data->data->deviceList, [$device_easel_id])) : [];
            }

            $result = $segment_upd_data;
        }
        return $result;
    }
}
