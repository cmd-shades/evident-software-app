<?php

namespace Application\Service\Models;

use System\Core\CI_Model;

class Site_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('serviceapp/Product_model', 'product_service');
        $this->load->model('serviceapp/Account_model', 'account_service');
        $this->load->model('serviceapp/Distribution_model', 'distribution_service');

        $section            = explode("/", $_SERVER["SCRIPT_NAME"]);
        if (!isset($section[1]) || empty($section[1]) || ( !( is_array($section) ) )) {
            $this->app_root = substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), "application"));
        } else {
            if (!isset($_SERVER["DOCUMENT_ROOT"]) || ( empty($_SERVER["DOCUMENT_ROOT"]) )) {
                $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . '/../');
            }

            $this->section      = $section;
            $this->app_root     = $_SERVER["DOCUMENT_ROOT"] . "/" . $section[1] . "/";
            $this->app_root     = str_replace('/index.php', '', $this->app_root);
        }
    }

    public $okay_statuses               = ['OK','No Fault'];

    /** Searchable fields **/
    private $searchable_fields          = ['site.site_id', 'site_name' ];

    /** Fields used on EASEL for Site profile **/
    private $easel_site_used_fields     = ["site_name", "site_reference_code"];

    /*
    *   Get Site single record or multiple records
    */
    public function get_sites($account_id = false, $site_id = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id) && $this->account_service->check_account_status($account_id)) {
            $where = convert_to_array($where);
            if (isset($where['provider_details'])) {
                if (!empty($where['provider_details'])) {
                    $provider_details   = convert_to_array($where['provider_details']);

                    ## PREPARE COMBO_CONDITION
                    $placeholder_arrs   = $this->_create_empty_arrays(count($provider_details));
                    $provider_counter   = 0;
                    $site_ids           = [];
                    $sites              = [];

                    foreach ($provider_details as $provider) {
                        $product_sites = $this->db->select('product.site_id')
                            ->where('account_id', $account_id)
                            ->where('content_provider_id', $provider['provider_id'])
                            ->where('no_of_titles_id', $provider['no_of_titles_id'])
                            ->group_by('product.site_id')
                            ->get('product');

                        if ($product_sites->num_rows() > 0) {
                            $results                                = $product_sites->result_array();
                            $site_ids                               = array_merge($site_ids, array_column($results, 'site_id'));
                            $placeholder_arrs[$provider_counter]    = array_column($results, 'site_id');

                            if ($provider_counter == 0) {
                                #$placeholder_arrs[$provider_counter][] = 8;
                            }

                            if ($provider_counter == 1) {
                                #$placeholder_arrs[$provider_counter][] = 8;
                            }

                            $provider_counter++;
                        }
                    }

                    if (count($placeholder_arrs) == 1) {
                        $valid_sites = $placeholder_arrs[0];
                    } elseif (count($placeholder_arrs) > 1) {
                        $valid_sites = $this->_multi_intersect($placeholder_arrs);
                    }

                    if (!empty($valid_sites)) {
                        $this->db->where_in('site.site_id', $valid_sites);
                    } else {
                        $this->session->set_flashdata('message', 'No Sites data found matching your criteria');
                        return false;
                    }
                }
                unset($where['provider_details']);
            }

            $this->db->select('site.*, concat( user.first_name," ",user.last_name ) `created_by`', false);
            $this->db->select('concat( modifier.first_name," ",modifier.last_name ) `last_modified_by`', false);
            $this->db->select('invoice_currency.setting_value `invoice_currency_name`', false);
            $this->db->select('distribution_group.setting_value `distribution_group_name`', false);
            $this->db->select('product_system_type.name `system_type_name`', false);
            $this->db->select('content_territory.country `content_territory_name`, content_territory.code `content_territory_code`', false);
            $this->db->select('time_zones.tz_db_name `time_zone_name`', false);
            $this->db->select('integrator.integrator_name `system_integrator_name`', false);
            $this->db->select('operating_company.integrator_name `operating_company_name`', false);
            $this->db->select('site_status.status_name', false);
            $this->db->select('charge_frequency.setting_value `charge_frequency_name`', false);

            $this->db->join('user', 'user.id = site.created_by', 'left');
            $this->db->join('user modifier', 'modifier.id = site.last_modified_by', 'left');
            $this->db->join('setting `invoice_currency`', 'invoice_currency.setting_id = site.invoice_currency_id', 'left');
            $this->db->join('setting `distribution_group`', 'distribution_group.setting_id = site.distribution_group_id', 'left');
            $this->db->join('setting `charge_frequency`', 'charge_frequency.setting_id = site.charge_frequency_id', 'left');
            $this->db->join('product_system_type', 'product_system_type.system_type_id = site.system_type_id', 'left');
            $this->db->join('content_territory', 'content_territory.territory_id = site.content_territory_id', 'left');
            $this->db->join('time_zones', 'time_zones.time_zone_id = site.time_zone_id', 'left');
            $this->db->join('integrator', 'integrator.system_integrator_id = site.system_integrator_id', 'left');
            $this->db->join('integrator `operating_company`', 'operating_company.system_integrator_id = site.operating_company_id', 'left');
            $this->db->join('site_status', 'site_status.status_id = site.status_id', 'left');

            $this->db->where('site.account_id', $account_id);

            $archived = "( `site`.`archived` != 1 OR `site`.`archived` is NULL )";
            $this->db->where($archived);

            if ($site_id) {
                $row = $this->db->get_where('site', ['site.site_id' => $site_id])->row();

                if (!empty($row)) {
                    ## join the site 'active months' - only for the 'Profile' view
                    $active_months = false;

                    $where = [
                        'site_reporting_window_month.account_id'    => $account_id,
                        'site_reporting_window_month.site_id'       => $site_id
                    ];

                    $active_months = $this->db->get_where('site_reporting_window_month', $where)->result();
                    if (!empty($active_months)) {
                        $row->active_months = $active_months;
                    } else {
                        $row->active_months = null ;
                    }

                    ## site devices:
                    $this->load->model('serviceapp/Device_model', 'device_service');
                    $number_of_devices = false;
                    $number_of_devices = $this->device_service->get_total_devices($account_id, ['site_id' => $site_id]);

                    if (!empty($number_of_devices->total)) {
                        $row->number_of_devices = (int) $number_of_devices->total;
                    } else {
                        $row->number_of_devices = false;
                    }

                    ## site contact details
                    $this->db->select('site_contact.*', false);

                    $where = [
                        'site_contact.active'   => 1,
                        'site_contact.site_id'  => $site_id
                    ];

                    $row_contact_details = $this->db->get_where('site_contact', $where)->row();

                    if (!empty($row->restrictions)) {
                        $row->site_restrictions = ( json_decode($row->restrictions) ) ? json_decode($row->restrictions) : false ;
                    } else {
                        $row->site_restrictions = null ;
                    }

                    if (!empty($row_contact_details)) {
                        $result = (object) array_merge((array) $row, (array) $row_contact_details);
                    } else {
                        $result = (object) ( (array) $row );
                    }

                    if (!empty($result->site_address_id)) {
                        $this->db->select('site_address.*, content_territory.country', false);

                        $this->db->join("content_territory", "content_territory.territory_id = site_address.site_territory_id", "left");

                        $where = [
                            "site_address.address_id"   => $result->site_address_id,
                            "site_address.active"       => 1,
                        ];

                        $row_address = $this->db->get_where('site_address', $where)->row();

                        if (!empty($row_address)) {
                            $result = (object) array_merge((array) $result, (array) $row_address);
                        }
                    }




                    $this->session->set_flashdata('message', 'Site record found');
                } else {
                    $this->session->set_flashdata('message', 'Site not found');
                }

                return $result;
            }

            if (isset($where['territory_id'])) {
                if (!empty($where['territory_id'])) {
                    $territories = ( is_array($where['territory_id']) ) ? $where['territory_id'] : [ $where['territory_id'] ];
                    $this->db->where_in('site.content_territory_id', $territories);
                }
                unset($where['territory_id']);
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $sites = $this->db->order_by('site.site_name')
                ->get('site');

            if ($sites->num_rows() > 0) {
                $this->session->set_flashdata('message', 'Site records found');
                $result = $sites->result();
            } else {
                $this->session->set_flashdata('message', 'Site record(s) not found');
            }
        }
        return $result;
    }

    /*
    *   Create new Site
    */
    public function create_site($account_id = false, $site_data = false)
    {
        $result = false;
        if (!empty($account_id)) {
            $data = [];

            $site_details = ( !empty($site_data['site_details']) ) ? json_decode($site_data['site_details']) : false ;

            if (!empty($site_details)) {
                foreach ($site_details as $key => $value) {
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
                        if (is_array($value)) {
                            foreach ($value as $key => $subvalue) {
                                $value[$key] = trim($subvalue);
                            }
                        } else {
                            $value = trim($value);
                        }
                    }
                    $data[$key] = $value;
                }

                if (!empty($data)) {
                    $data['account_id']     = $account_id;
                    $data['status_id']      = 1;
                    $data['created_by']     = $this->ion_auth->_current_user->id;
                    $new_site_data = $this->ssid_common->_filter_data('site', $data);
                    $this->db->insert('site', $new_site_data);
                    if ($this->db->trans_status() !== false) {
                        $site_insert_id = $this->db->insert_id();
                        $result         = $this->get_sites($account_id, $site_insert_id);
                        $this->session->set_flashdata('message', 'Site record created successfully.');
                    }
                }

                if (!empty($site_data['site_address'])) {
                    $saved_address = $this->save_site_address($account_id, $site_insert_id, $site_data['site_address']);

                    if (!empty($saved_address)) {
                        $this->db->update("site", ["site_address_id" => $saved_address->address_id ], ["site_id" => $site_insert_id]);
                    }
                }

                if (!empty($site_data['site_contact'])) {
                    $saved_contact = $this->save_site_contact($account_id, $site_insert_id, $site_data['site_contact']);
                }
            } else {
                $this->session->set_flashdata('message', 'No Site details.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id supplied.');
        }
        return $result;
    }


    public function save_site_address($account_id = false, $site_id = false, $address = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($site_id) && !empty($address)) {
            $address = convert_to_array($address);

            foreach ($address as $key => $value) {
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

            if (!empty($data)) {
                $data['account_id']     = $account_id;
                $data['created_by']     = $this->ion_auth->_current_user->id;
                $data['site_id']        = $site_id;
                $address_data           = $this->ssid_common->_filter_data('site_address', $data);
                $this->db->insert('site_address', $address_data);
                if ($this->db->trans_status() !== false) {
                    $data['address_id'] = $this->db->insert_id();
                    $result = $this->db->get_where("site_address", ["address_id" => $data['address_id'] ])->row();
                }
            }
        }

        return $result;
    }


    public function save_site_contact($account_id = false, $site_id = false, $contact = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($site_id) && !empty($contact)) {
            $contact = ( is_object($contact) ) ? object_to_array($contact) : convert_to_array($contact);

            foreach ($contact as $key => $value) {
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

            if (!empty($data)) {
                $this->db->order_by("site_contact.contact_id DESC");
                $contact_exists = $this->db->get_where("site_contact", [ "account_id" => $account_id, "site_id" => $site_id ], 1, 0)->row();

                if ($contact_exists) {
                    $data['account_id']     = $account_id;
                    $data['modified_by']    = $this->ion_auth->_current_user->id;
                    $contact_data           = $this->ssid_common->_filter_data('site_contact', $data);
                    $this->db->update('site_contact', $contact_data, ["contact_id" => $contact_exists->contact_id]);
                } else {
                    $data['account_id']     = $account_id;
                    $data['site_id']        = $site_id;
                    $data['created_by']     = $this->ion_auth->_current_user->id;
                    $contact_data           = $this->ssid_common->_filter_data('site_contact', $data);
                    $this->db->insert('site_contact', $contact_data);
                }

                if ($this->db->affected_rows() > 0) {
                    $contact_id = ( !empty($contact_exists) ) ? $contact_exists->contact_id : $this->db->insert_id();
                    $result = $this->db->get_where("site_contact", ["account_id" => $account_id, "contact_id" => $contact_id ])->row();
                }
            }
        }

        return $result;
    }


    /*
    *   Update Site record
    */
    public function update_site($account_id = false, $site_id = false, $update_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($site_id) && !empty($update_data)) {
            $check_site = $this->db->get_where('site', ['account_id' => $account_id, 'site_id' => $site_id])->row();
            if (!empty($check_site)) {
                $message        = "";
                $update_data    = convert_to_array($update_data);

                $site_details   = ( !empty($update_data['site_details']) ) ? ( $update_data['site_details'] ) : false ;
                unset($update_data['site_details']);

                if (!empty($site_details)) {
                    $updated_details = $this->update_site_details($account_id, $site_id, $site_details);

                    if (!empty($this->session->flashdata('site_details_message'))) {
                        $message .= $this->session->flashdata('site_details_message');
                    } else {
                        if (!empty($updated_details)) {
                            $message .= "Details have been updated. ";
                        } else {
                            $message .= "No changes to Site Details. ";
                        }
                    }
                }

                $address_details = ( !empty($update_data['address_details']) ) ? ( $update_data['address_details'] ) : false ;
                unset($update_data['address_details']);

                if (!empty($address_details)) {
                    $updated_address = $this->update_address_details($account_id, $site_id, $address_details);

                    if (!empty($updated_address)) {
                        $message .= "Address Details have been updated. ";
                    } else {
                        $message .= "No changes to Address Details. ";
                    }
                }

                $contact_details = ( !empty($update_data['contact_details']) ) ? ( $update_data['contact_details'] ) : false ;
                unset($update_data['contact_details']);

                if (!empty($contact_details)) {
                    $updated_contact = $this->save_site_contact($account_id, $site_id, $contact_details);

                    if (!empty($updated_contact)) {
                        $message .= "Contact Details have been updated. ";
                    } else {
                        $message .= "No changes to Contact Details. ";
                    }
                }

                $result = $this->get_sites($account_id, $site_id);

                $message = ( !empty($message) ) ? $message : "There was some issues when updating the Sites" ; ## It may be reviewed to get the proper message

                $this->session->set_flashdata('message', $message);
            } else {
                $this->session->set_flashdata('message', 'Foreign site record. Access denied.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Site data supplied.');
        }
        return $result;
    }



    /**
    *   Save site details
    **/
    public function update_site_details($account_id = false, $site_id = false, $site_details = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($site_id) && !empty($site_details)) {
            $site_details       = ( is_object($site_details) ) ? object_to_array($site_details) : convert_to_array($site_details);

            ## Check if Site exists
            $site_b4_update     = $this->db->get_where("site", ["account_id" => $account_id, "site_id" => $site_id])->row();

            if (!empty($site_b4_update)) {
                foreach ($site_details as $key => $value) {
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
                        if (is_array($value)) {
                            foreach ($value as $key => $subvalue) {
                                $value[$key] = trim($subvalue);
                            }
                        } else {
                            $value = trim($value);
                        }
                    }
                    $data[$key] = $value;
                }

                $data['account_id']         = $account_id;
                $data['last_modified_by']   = $this->ion_auth->_current_user->id;
                $site_data                  = $this->ssid_common->_filter_data('site', $data);
                $this->db->update('site', $site_data, ["site_id" => $site_id]);

                if ($this->db->affected_rows() > 0) {
                    $result = $this->db->get_where("site", ["account_id" => $account_id, "site_id" => $site_id ])->row();

                    ## Check if site is Easel active and it has the Easel ID - not needed
/*                  if( ( $site_b4_update->is_airtime_active == true ) ){

                        ## IF yes, check if update is needed - does the fields changed?
                        $easel_site_used_fields = $this->easel_site_used_fields;

                        $comparison_array = false;

                        if( !empty( $easel_site_used_fields ) ){
                            foreach( $easel_site_used_fields as $check_field ){
                                if( ( isset( $site_b4_update->{ $check_field } ) || ( $site_b4_update->{ $check_field } == NULL ) ) && isset( $result->{ $check_field } ) && ( $result->{ $check_field } != $site_b4_update->{ $check_field } ) ){
                                    $comparison_array[$check_field]['old'] = $site_b4_update->{ $check_field };
                                    $comparison_array[$check_field]['new'] = $result->{ $check_field };
                                }
                            }
                        }

                        if( !empty( $comparison_array ) ){
                            ## there is something to update

                            $easel_message      = "";
                            $easel_updated_site = $this->easel_service->update_market( $account_id, $result->airtime_site_ref, ( array ) $result );

                            if( !empty( $this->session->flashdata( 'update_market_message' ) ) ){
                                $easel_message .= $this->session->flashdata( 'update_market_message' );
                            } else {
                                if( !empty( $easel_updated_site->data ) && ( $easel_updated_site->success == true ) ){
                                    $easel_message .= "Market updated successfully";
                                } else {
                                    $easel_message .= "Market update on Easel API failed";
                                }
                            }
                        }
                    } */
                    $message = 'Site details have been updated. ';
                    $this->session->set_flashdata('site_details_message', $message . ( ( !empty($easel_message) ) ? $easel_message : '' ));
                } else {
                    $this->session->set_flashdata('site_details_message', 'No changes have been applied. ');
                }
            } else {
                $this->session->set_flashdata('site_details_message', 'Site record not found.');
            }
        }

        return $result;
    }



    public function update_address_details($account_id = false, $site_id = false, $address = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($site_id) && !empty($address)) {
            $address = ( is_object($address) ) ? object_to_array($address) : convert_to_array($address);

            foreach ($address as $key => $value) {
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

            if (!empty($data)) {
                $this->db->select("site.site_address_id", false);
                $this->db->where("site.site_id", $site_id);
                $query = $this->db->get("site")->row();
                $address_id = $query->site_address_id;

                if (!empty($address_id)) {
                    $data['account_id']         = $account_id;
                    $data['last_modified_by']   = $this->ion_auth->_current_user->id;
                    $address_data               = $this->ssid_common->_filter_data('site_address', $data);
                    $this->db->update('site_address', $address_data, ["address_id" => $address_id]);
                } else {
                    $data['account_id']     = $account_id;
                    $data['created_by']     = $this->ion_auth->_current_user->id;
                    $address_data           = $this->ssid_common->_filter_data('site_address', $data);
                    $this->db->insert('site_address', $address_data);
                }

                if ($this->db->affected_rows() > 0) {
                    $address_id = ( !empty($address_id) ) ? $address_id : $this->db->insert_id();

                    $this->db->update('site', ["site_address_id" => $address_id], ["site_id" => $site_id]);

                    $result = $this->db->get_where("site_address", ["account_id" => $account_id, "address_id" => $address_id ])->row();
                }
            }
        }

        return $result;
    }




    /**
    *   Save site details
    **/
    public function update_contact_details($site_id = false, $contact_details = false)
    {
        if (!empty($account_id) && !empty($site_id) && !empty($address)) {
            $address = ( is_object($address) ) ? object_to_array($address) : convert_to_array($address);

            foreach ($address as $key => $value) {
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

            if (!empty($data)) {
                $this->db->select("site.site_address_id", false);
                $this->db->where("site.site_id", $site_id);
                $query = $this->db->get("site")->row();
                $address_id = $query->site_address_id;

                if (!empty($address_id)) {
                    $data['account_id']         = $account_id;
                    $data['last_modified_by']   = $this->ion_auth->_current_user->id;
                    $address_data               = $this->ssid_common->_filter_data('site_address', $data);
                    $this->db->update('site_address', $address_data, ["address_id" => $address_id]);
                } else {
                    $data['account_id']         = $account_id;
                    $data['created_by']         = $this->ion_auth->_current_user->id;
                    $address_data               = $this->ssid_common->_filter_data('site_address', $data);
                    $this->db->insert('site_address', $address_data);
                }

                if ($this->db->affected_rows() > 0) {
                    $address_id = ( !empty($address_id) ) ? $address_id : $this->db->insert_id();

                    $this->db->update('site', ["site_address_id" => $address_id], ["site_id" => $site_id]);

                    $result = $this->db->get_where("site_address", ["account_id" => $account_id, "address_id" => $address_id ])->row();
                }
            }
        }
    }



    /*
    *   Delete Site record
    */
    public function delete_site($account_id = false, $site_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($site_id)) {
            $check_site = $this->db->get_where('site', ['account_id' => $account_id, 'site_id' => $site_id])->row();
            if (!empty($check_site)) {
                $data = [
                    "archived"          => 1,
                    "last_modified_by"  => $this->ion_auth->_current_user->id,
                ];

                $this->db->update("site", $data, [ "account_id" => $account_id, "site_id" => $site_id ]);

                if ($this->db->trans_status() !== false) {
                    //Delete associated contact as well
                    $contact_data = [
                        "archived"      => 1,
                        "modified_by"   => $this->ion_auth->_current_user->id,
                        "active"        => 0,
                    ];
                    $this->db->update('site_contact', $contact_data, ["account_id" => $account_id, "site_id" => $site_id]);

                    //Delete associated address as well
                    $address_data = [
                        "archived"          => 1,
                        "last_modified_by"  => $this->ion_auth->_current_user->id,
                        "active"            => 0,
                    ];
                    $this->db->update('site_address', $address_data, ["account_id" => $account_id, "address_id" => $check_site->site_address_id]);

                    $result = true;
                    $this->session->set_flashdata('message', 'Site record deleted successfully.');
                }
            } else {
                $this->session->set_flashdata('message', 'Foreign site record. Access denied.');
            }
        }
        return $result;
    }

    /*
    * Search through sites
    */
    public function site_lookup($account_id = false, $search_term = false, $block_statuses = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('site.*, site_status.status_name, addr.*', false);
            $this->db->select('product_system_type.name `system_type_name`');
            $this->db->select('integrator.integrator_name `system_integrator_name`', false);

            $this->db->join('site_address addr', 'addr.address_id = site.site_address_id', 'left');
            $this->db->join('site_status', 'site_status.status_id = site.status_id', 'left');
            $this->db->join('product_system_type', 'product_system_type.system_type_id = site.system_type_id', 'left');
            $this->db->join('integrator', 'integrator.system_integrator_id = site.system_integrator_id', 'left');

            $this->db->where('site.account_id', $account_id);

            $arch_where = "( site.archived != 1 or site.archived is NULL )";
            $this->db->where($arch_where);

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->searchable_fields as $k => $field) {
                            $search_where[$field] = trim($term);
                        }

                        if (!empty($search_where['site.status_id'])) {
                            $search_where['site_status.status_name'] =   trim($term);
                            unset($search_where['site.status_id']);
                        }

                        if (!empty($search_where['site.site_address_id'])) {
                            $search_where['addrs.summaryline'] =   trim($term);
                            unset($search_where['site.site_address_id']);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->searchable_fields as $k => $field) {
                        $search_where[$field] = $search_term;
                    }

                    if (!empty($search_where['site.status_id'])) {
                        $search_where['site_status.status_name'] =  $search_term;
                        unset($search_where['site.status_id']);
                    }

                    if (!empty($search_where['site.site_address_id'])) {
                        $search_where['addrs.summaryline'] =  $search_term;
                        unset($search_where['site.site_address_id']);
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if ($block_statuses) {
                $this->db->where_in('site.status_id', json_decode($block_statuses));
            }

            //Check for a setting that specifies whether or not to only get Alarmed sites
            if ($where) {
                $where = ( !is_array($where) ) ? json_decode($where) : $where;
                $where = ( is_object($where) ) ? object_to_array($where) : $where;

                if (!empty($where['alarmed']) && ( $where['alarmed'] == 1 )) {
                    $this->db->where('( site.event_tracking_id > 0 )');
                    unset($where['alarmed']);
                }

                if ($where) {
                    $this->db->where($where);
                }
            }

            if ($order_by) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('site.site_name');
            }

            $query = $this->db->limit($limit, $offset)
                ->get('site');

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
    *   Get total site count for the search
    */
    public function get_total_sites($account_id = false, $search_term = false, $block_statuses = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('site.*, site_status.status_name, addr.*', false);
            $this->db->select('product_system_type.name `system_type_name`');
            $this->db->select('integrator.integrator_name `system_integrator_name`', false);

            $this->db->join('site_address addr', 'addr.address_id = site.site_address_id', 'left');
            $this->db->join('site_status', 'site_status.status_id = site.status_id', 'left');
            $this->db->join('product_system_type', 'product_system_type.system_type_id = site.system_type_id', 'left');
            $this->db->join('integrator', 'integrator.system_integrator_id = site.system_integrator_id', 'left');

            $this->db->where('site.account_id', $account_id);

            $arch_where = "( site.archived != 1 or site.archived is NULL )";
            $this->db->where($arch_where);

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->searchable_fields as $k => $field) {
                            $search_where[$field] = trim($term);
                        }

                        if (!empty($search_where['site.status_id'])) {
                            $search_where['site_status.status_name'] =   trim($term);
                            unset($search_where['site.status_id']);
                        }

                        if (!empty($search_where['site.site_address_id'])) {
                            $search_where['addrs.summaryline'] =   trim($term);
                            unset($search_where['site.site_address_id']);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->searchable_fields as $k => $field) {
                        $search_where[$field] = $search_term;
                    }

                    if (!empty($search_where['site.status_id'])) {
                        $search_where['site_status.status_name'] =  $search_term;
                        unset($search_where['site.status_id']);
                    }

                    if (!empty($search_where['site.site_address_id'])) {
                        $search_where['addrs.summaryline'] =  $search_term;
                        unset($search_where['site.site_address_id']);
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if ($block_statuses) {
                $this->db->where_in('site.status_id', json_decode($block_statuses));
            }

            //Check for a setting that specifies whether or not to only get Alarmed sites
            if ($where) {
                $where = ( !is_array($where) ) ? json_decode($where) : $where;
                $where = ( is_object($where) ) ? object_to_array($where) : $where;

                if (!empty($where['alarmed']) && ( $where['alarmed'] == 1 )) {
                    $this->db->where('( site.event_tracking_id > 0 )');
                    unset($where['alarmed']);
                }

                if ($where) {
                    $this->db->where($where);
                }
            }

            $query = $this->db->from('site')->count_all_results();
            $results['total'] = !empty($query) ? $query : 0;
            $results['pages'] = !empty($query) ? ceil($query / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }

    public function create_site_change_log($account_id = false, $site_id = false, $log_data = false)
    {
        if ($account_id && $site_id && $log_data) {
            $data   = $this->ssid_common->_filter_data('site_change_log', $log_data);
            $data['site_id']      = $site_id;
            $data['account_id']   = $account_id;
            $data['created_by']   = $this->ion_auth->_current_user->id;
            $data['updated_data'] = json_encode($log_data);
            $this->db->insert('site_change_log', $data);
        }
        return true;
    }

    public function get_site_change_logs($account_id = false, $site_id = false)
    {
        $result = false;
        $this->db->select('scl.*,concat(user.first_name," ",user.last_name) `created_by`')
            ->order_by('scl.id desc')
            ->where('scl.account_id', $account_id)
            ->join('user', 'user.id = scl.created_by', 'left');

        if ($site_id) {
            $this->db->where('scl.site_id', $site_id);
        }
        $query = $this->db->get('site_change_log scl');
        if ($query->num_rows() > 0) {
            $result = $query->result();
        }
        return $result;
    }

    /** Get site contracts **/
    public function get_site_contracts($account_id = false, $site_id = false, $grouped = false)
    {
        $result = null;
        if (!empty($account_id) && !empty($site_id)) {
            $this->db->select('sc.link_id, contract.*, ct.type_name, cs.status_name, concat(user.first_name," ",user.last_name) `lead_person`', false)
                ->join('contract', 'contract.contract_id = sc.contract_id', 'left')
                ->join('contract_type ct', 'ct.type_id = contract.contract_type_id', 'left')
                ->join('contract_status cs', 'cs.status_id = contract.contract_status_id', 'left')
                ->join('user', 'user.id = contract.contract_lead_id', 'left')
                ->order_by('sc.link_id desc')
                ->where('sc.account_id', $account_id);

            if ($site_id) {
                $this->db->where('sc.site_id', $site_id);
            }

            $query = $this->db->get('sites_contracts sc');

            if ($query->num_rows() > 0) {
                $data   = [];
                foreach ($query->result() as $row) {
                    $data[$row->type_name][] = $row;
                }
                $result = $data;
            }
        }
        return $result;
    }

    /** Get Site statuses **/
    public function get_site_statuses($account_id = false)
    {

        $result = null;

        if ($account_id) {
            $this->db->where('site_status.account_id', $account_id);
        } else {
            $this->db->where('( site_status.account_id IS NULL OR site_status.account_id = "" )');
        }

        $query = $this->db->where('is_active', 1)->get('site_status');

        if ($query->num_rows() > 0) {
            $result = $query->result();
        } else {
            $result = $this->get_site_statuses();
        }

        return $result;
    }

    /** Get Site Event statuses **/
    public function get_site_event_statuses($account_id = false)
    {

        $result = null;

        if ($account_id) {
            $this->db->where('site_event_statuses.account_id', $account_id);
        } else {
            $this->db->where('( site_event_statuses.account_id IS NULL OR site_event_statuses.account_id = "" )');
        }

        $query = $this->db->where('is_active', 1)->get('site_event_statuses');

        if ($query->num_rows() > 0) {
            $result = $query->result();
        } else {
            $result = $this->get_site_event_statuses();
        }

        return $result;
    }


    /**
    *   Get Charge Frequencies
    **/
    public function get_charge_frequencies($account_id = false, $charge_frequency_id = false, $unorganized = false)
    {

        $result = null;

        if ($account_id) {
            $this->db->where('site_charge_frequency.account_id', $account_id);
        } else {
            $this->db->where('( site_charge_frequency.account_id IS NULL OR site_charge_frequency.account_id = "" )');
        }
        if ($charge_frequency_id) {
            $this->db->where('frequency_id', $charge_frequency_id);
        }

        $this->db->where('active', 1);
        $query = $this->db->get('site_charge_frequency');

        if ($query->num_rows() > 0) {
            if (!empty($unorganized)) {
                $result = $query->result();
            } else {
                foreach ($query->result() as $key => $row) {
                    $result[$row->frequency_id] = $row;
                }
            }
            $this->session->set_flashdata('message', 'Frequency(ies) found');
        } else {
            $result = $this->get_charge_frequencies();
        }

        return $result;
    }


    /**
    *   Get Package Types
    **/
    public function get_package($account_id = false, $package_id = false, $where = false, $unorganized = false)
    {

        $result = null;

        if ($account_id) {
            $this->db->where('content_package.account_id', $account_id);

            if ($package_id) {
                $this->db->where('package_id', $package_id);
            }

            if (!empty($where)) {
                $where = convert_to_array($where);
                $this->db->where($where);
            }

            $this->db->where('active', 1);
            $query = $this->db->get('content_package');

            if ($query->num_rows() > 0) {
                if (!empty($unorganized)) {
                    $result = $query->result();
                } else {
                    foreach ($query->result() as $key => $row) {
                        $result[$row->package_id] = $row;
                    }
                }
                $this->session->set_flashdata('message', 'Package(s) found');
            } else {
                $this->session->set_flashdata('message', 'Package(s) not found');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID is required');
        }
        return $result;
    }



    /**
    *   Get Operating Company list
    **/
    public function get_operating_company($account_id = false, $company_id = false, $where = false, $unorganized = false)
    {

        $result = null;

        if ($account_id) {
            $this->db->where('site_operating_company.account_id', $account_id);

            if ($company_id) {
                $this->db->where('company_id', $company_id);
            }

            if (!empty($where)) {
                $where = convert_to_array($where);
                $this->db->where($where);
            }

            $this->db->where('active', 1);
            $query = $this->db->get('site_operating_company');

            if ($query->num_rows() > 0) {
                if (!empty($unorganized)) {
                    $result = $query->result();
                } else {
                    foreach ($query->result() as $key => $row) {
                        $result[$row->company_id] = $row;
                    }
                }
                $this->session->set_flashdata('message', 'Company(ies) found');
            } else {
                $this->session->set_flashdata('message', 'Company(ies) not found');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID is required');
        }
        return $result;
    }



    /**
    *   Get Time Zone(s)
    **/
    public function get_time_zones($account_id = false, $time_zone_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {

        $result = null;

        if ($account_id) {
            if ($time_zone_id) {
                $this->db->where('time_zone_id', $time_zone_id);
            }

            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where['country_code'])) {
                    $country_code = $where['country_code'];
                    $this->db->where("country_code", $country_code);
                    unset($where['country_code']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $this->db->where('active', 1);

            $archived = "( `time_zones`.`archived` != 1 OR `time_zones`.`archived` is NULL )";
            $this->db->where($archived);

            $query = $this->db->get('time_zones');

            if ($query->num_rows() > 0) {
                $result_set = $query->result();

                if (!empty($time_zone_id)) {
                    $result = $result_set[0];
                } else {
                    foreach ($result_set as $key => $row) {
                        $result[$row->time_zone_id] = $row;
                    }
                }
                $this->session->set_flashdata('message', 'Time Zone(s) found');
            } else {
                $this->session->set_flashdata('message', 'Time Zone(s) not found');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID is required');
        }
        return $result;
    }

    /**
    * Get Room totalizer
    **/
    public function get_room_totalizer($account_id = false, $site_id = false)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('SUM( CASE WHEN site_id > 0 THEN 1 ELSE 0 END ) AS total_sites,
			SUM(CASE WHEN number_of_rooms > 0 THEN number_of_rooms ELSE 0 END) AS total_rooms,', false);

            $this->db->where('site.account_id', $account_id);
            #$this->db->where( 'site.is_active', 1 );
            if (!empty($site_id)) {
                $this->db->where('site.site_id', $site_id);
            }

            $query = $this->db->get('site');

            if ($query->num_rows() > 0) {
                $result = $query->result()[0];
                $this->session->set_flashdata('message', 'Site totalizer data found');
            } else {
                $this->session->set_flashdata('message', 'Account ID is required');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID is required');
        }
        return $result;
    }


    /**
    *   Duplicate site record
    **/
    public function duplicate_site($account_id = false, $site_id = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($site_id)) {
            $site = $this->db->get_where("site", ["account_id" => $account_id, "site_id" => $site_id ])->row();
            ;

            if (!empty($site)) {
                unset($site->site_id, $site->last_modified, $site->date_creation);
                $duplicate_data                         = (array) $site;
                $duplicate_data['date_creation']        = date('Y-m-d H:i:s');

                if (strpos($duplicate_data['site_reference_code'], '-') !== false) {
                    $site_reference_code = explode('-', $duplicate_data['site_reference_code']);
                    $duplicate_data['site_reference_code']  = $site_reference_code[0] . '-' . date('dmyHis');
                } else {
                    $duplicate_data['site_reference_code']  = $duplicate_data['site_reference_code'] . '-' . date('dmyHis');
                }

                $duplicate_data['created_by']   = $this->ion_auth->_current_user->id;
                $this->db->insert('site', $duplicate_data);
                if ($this->db->affected_rows() > 0) {
                    $new_site_id = $this->db->insert_id();
                    ## Copy contacts
                    $this->_copy_site_contacts($account_id, $site_id, $new_site_id);

                    ## Copy Site Address
                    $new_site_address_id = $this->_copy_site_address($account_id, $duplicate_data['site_address_id']);
                    if (!empty($new_site_address_id)) {
                        $this->db->where('site.site_id', $new_site_id)
                            ->update('site', [ 'site_address_id' => $new_site_address_id ]);
                    }


                    ## Copy products
                    #$this->_copy_site_products( $account_id, $site_id, $new_site_id );

                    $result = $this->db->get_where('site', ['account_id' => $account_id, 'site_id' => $new_site_id ])->row();
                    ;
                    $result = $this->db->get_where('site', ['account_id' => $account_id, 'site_id' => $new_site_id ])->row();
                    ;
                    $this->session->set_flashdata('message', 'Site duplicated successfully');
                }
            } else {
                $this->session->set_flashdata('message', 'Invalid primary Site ID');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }

    /** Copy Existing Site contacts to a new Site **/
    private function _copy_site_contacts($account_id = false, $primary_site_id = false, $secondary_site_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($primary_site_id) && !empty($secondary_site_id)) {
            $query = $this->db->select('account_id, first_name, last_name, telephone_number, email, skype, archived, active', false)
                ->where('account_id', $account_id)
                ->where('site_id', $primary_site_id)
                ->get('site_contact');

            if ($query->num_rows() > 0) {
                $new_data = [];
                foreach ($query->result() as $k => $row) {
                    $row->site_id    = $secondary_site_id;
                    $row->created_by = $this->ion_auth->_current_user->id;
                    $new_data[] = (array) $row;
                }

                $this->db->insert_batch('site_contact', $new_data);
            }

            $result = ( $this->db->trans_status() !== false ) ? true : false;
        }
        return $result;
    }

    /** Copy Existing Site Addresses to a new Site **/
    private function _copy_site_address($account_id = false, $primary_address_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($primary_address_id)) {
            $query = $this->db->select('site_address.*', false)
                ->where('account_id', $account_id)
                ->where('address_id', $primary_address_id)
                ->get('site_address')
                ->row();

            if (!empty($query)) {
                $row = $query;
                unset($row->address_id);
                $row->created_by = $this->ion_auth->_current_user->id;
                $this->db->insert('site_address', (array) $row);
                $result = $this->db->insert_id();
            }
        }
        return $result;
    }

    /** Copy Existing Site products to a new Site **/
    private function _copy_site_products($accout_id = false, $primary_site_id = false, $secondary_site_id = false)
    {
        $result = false;
        if (!empty($accout_id) && !empty($primary_site_id) && !empty($secondary_site_id)) {
        }
        return $result;
    }



    /*
    *   Function to calculate the value of the site for the current month
    */
    public function get_site_value($account_id = false, $site_id = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($site_id)) {
            $todays_date        = date('Y-m-d H:i:s');
            $first_month_day    = date('Y-m-' . '01', strtotime($todays_date));
            $last_month_day     = date("Y-m-t", strtotime($todays_date));

            $site_monthly_value = 0;

            $site_products      = $this->product_service->get_product($account_id, false, ["site_id" => $site_id]);

            if (!empty($site_products)) {
                foreach ($site_products as $p_row) {
                    ## 1 - end date in this month, 2 - start day in this month, 3 - start date before this month and end date after this month (we're in the middle), 4 - no end date specified
                    if (( strtolower($p_row->product_status_name) == "active" ) && ( ( ( $p_row->end_date >= $first_month_day ) && ( $p_row->end_date <= $last_month_day ) ) || ( ( $p_row->start_date >= $first_month_day ) && ( $p_row->start_date <= $last_month_day  ) ) || ( ( $p_row->start_date <= $first_month_day ) && ( ( $p_row->end_date >= $last_month_day ) || in_array($p_row->end_date, ["0000-00-00", "1970-01-01"]) ) ) )) {
                        if (!empty($p_row->package_charge) && !empty($p_row->no_of_rooms)) {
                            ## $site_monthly_value += number_format( $p_row->package_charge * $p_row->no_of_rooms * MONTHLY_SITE_VALUE, 4, '.', '' );
                            $site_monthly_value += number_format($p_row->package_charge * $p_row->no_of_rooms, 4, '.', '');
                        }
                    } else {
                        ## product outside the range
                        $this->session->set_flashdata('message', 'No product found for the given range');
                    }
                }
                $this->session->set_flashdata('message', 'Current Site monthly value has been calculated');
                $result = $site_monthly_value;
            } else {
                $this->session->set_flashdata('message', 'No product found for the given Site ID.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id or Site ID supplied.');
        }

        return $result;
    }



    /*
    *   Function to disable the site with given date
    */
    public function disable_site($account_id = false, $site_id = false, $disable_date = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($site_id) && !empty($disable_date)) {
            $todays_date        = date('Y-m-d');
            $disable_date       = date('Y-m-d', strtotime(format_date_db($disable_date)));
            $updated_site_id    = false;
            $change_status_now  = false;
            $upd_data           = false;

            if (!empty($disable_date)) {
                if ($disable_date <= $todays_date) {
                    $change_status_now = true;
                }

                ## A. Add a disable date into the site table
                $upd_data = [
                    "last_modified_by"      => ( isset($this->ion_auth->_current_user->id) && !empty($this->ion_auth->_current_user->id) ) ? $this->ion_auth->_current_user->id : 1 ,
                    "disable_site_date"     => $disable_date,
                ];

                ## C. Change the status if needed
                if ($change_status_now) {
                    $upd_data['status_id'] = 2; ## 2 - site is disabled
                }
                $this->db->update("site", $upd_data, ["account_id" => $account_id, "site_id" => $site_id]);

                if ($this->db->trans_status() !== false) {
                    $updated_site_id = $site_id; ## The site has been disabled
                }


                ## B. loop through the products and change the end date if needed
                $site_products      = $this->product_service->get_product($account_id, false, ["site_id" => $site_id]);

                if (!empty($site_products)) {
                    $product_batch_update = [];

                    foreach ($site_products as $product) {
                        if (!isset($product->end_date) || ( $product->end_date = '0000-00-00' ) || empty($product->end_date) || ( $product->end_date > $disable_date )) {
                            $product_batch_update[ $product->product_id ] = [
                                "product_id"            => $product->product_id,
                                "end_date"              => $disable_date,
                                "last_modified_by"      => ( isset($this->ion_auth->_current_user->id) && !empty($this->ion_auth->_current_user->id) ) ? $this->ion_auth->_current_user->id : 1 ,
                            ];
                        }
                    }

                    if (!empty($product_batch_update)) {
                        $this->db->update_batch('product', $product_batch_update, 'product_id');

                        if ($this->db->affected_rows() > 0) {
                            $this->session->set_flashdata('message', 'Site products are scheduled to expire on chosen date');
                        } else {
                            ## $this->session->set_flashdata( 'message', 'There was an error processing Site Products' );   ## not needed as override the previous message.
                        }
                    } else {
                        ## nothing to update - no product with end date empty or exceeding disable date
                        ## no need to mention this
                    }

                    $this->session->set_flashdata('message', 'The Site has been disabled');
                } else {
                    ## No products to update - no need for the message as the main action is to disable the site
                }


                if (!empty($updated_site_id)) {
                    $result = $this->get_sites($account_id, $updated_site_id);
                    $this->session->set_flashdata('message', 'The Site has been disabled');
                } else {
                    $this->session->set_flashdata('message', 'The Site hasn\t been disabled');
                }
            } else {
                $this->session->set_flashdata('message', 'Incorrect format for the Disable Date');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id or Site ID or disable Date supplied');
        }

        return $result;
    }


    /*
    *   Function to disable the site automatically based on the disable site date
    */
    public function automated_site_disable()
    {

        $todays_date        = date('Y-m-d');
        $account_id         = 1;
        $sites_2b_disabled = $this->db->get_where("site", ["disable_site_date<=" => $todays_date, "status_id" => 1 ])->result();

        $disabled_sites = [];

        if (!empty($sites_2b_disabled)) {
            foreach ($sites_2b_disabled as $site_row) {
                if ($this->disable_site($account_id, $site_row->site_id, $todays_date)) {
                    $disabled_sites[] = $site_row->site_id;
                }
            }
        }

        return true;
    }

    /*
    *   Get Site records for use in Distribution Groups
    */
    public function get_distribution_sites($account_id = false, $site_id = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {

        $result = false;

        if (!empty($account_id) && $this->account_service->check_account_status($account_id)) {
            $where = convert_to_array($where);
            if (isset($where['provider_details'])) {
                if (!empty($where['provider_details'])) {
                    $provider_details   = convert_to_array($where['provider_details']);

                    ## PREPARE COMBO_CONDITION
                    $placeholder_arrs   = $this->_create_empty_arrays(count($provider_details));
                    $provider_counter   = 0;
                    $site_ids           = [];
                    $sites              = [];

                    foreach ($provider_details as $provider) {
                        $product_sites = $this->db->select('product.site_id')
                            ->where('account_id', $account_id)
                            ->where('content_provider_id', $provider['provider_id'])
                            ->where('no_of_titles_id', $provider['no_of_titles_id'])
                            ->group_by('product.site_id')
                            ->get('product');

                        if ($product_sites->num_rows() > 0) {
                            $results                                = $product_sites->result_array();
                            $site_ids                               = array_merge($site_ids, array_column($results, 'site_id'));
                            $placeholder_arrs[$provider_counter]    = array_column($results, 'site_id');

                            if ($provider_counter == 0) {
                                #$placeholder_arrs[$provider_counter][] = 8;
                            }

                            if ($provider_counter == 1) {
                                #$placeholder_arrs[$provider_counter][] = 8;
                            }

                            $provider_counter++;
                        }
                    }

                    if (count($placeholder_arrs) == 1) {
                        $valid_sites = $placeholder_arrs[0];
                    } elseif (count($placeholder_arrs) > 1) {
                        $valid_sites = $this->_multi_intersect($placeholder_arrs);
                    }

                    if (!empty($valid_sites)) {
                        $this->db->where_in('site.site_id', $valid_sites);
                    } else {
                        $this->session->set_flashdata('message', 'No Sites data found matching your criteria');
                        return false;
                    }
                }
                unset($where['provider_details']);
            }

            $this->db->select('site.*, concat( user.first_name," ",user.last_name ) `created_by`', false);
            $this->db->select('concat( modifier.first_name," ",modifier.last_name ) `last_modified_by`', false);
            $this->db->select('invoice_currency.setting_value `invoice_currency_name`', false);

            // $this->db->select( 'distribution_group.setting_value `distribution_group_name`', false );

            $this->db->select('product_system_type.name `system_type_name`', false);
            $this->db->select('content_territory.country `content_territory_name`', false);
            $this->db->select('time_zones.tz_db_name `time_zone_name`', false);
            $this->db->select('integrator.integrator_name `system_integrator_name`', false);
            $this->db->select('operating_company.integrator_name `operating_company_name`', false);
            $this->db->select('site_status.status_name', false);

            $this->db->join('user', 'user.id = site.created_by', 'left');
            $this->db->join('user modifier', 'modifier.id = site.last_modified_by', 'left');
            $this->db->join('setting `invoice_currency`', 'invoice_currency.setting_id = site.invoice_currency_id', 'left');

            // $this->db->join( 'setting `distribution_group`','distribution_group.setting_id = site.distribution_group_id','left' );

            $this->db->join('product_system_type', 'product_system_type.system_type_id = site.system_type_id', 'left');
            $this->db->join('content_territory', 'content_territory.territory_id = site.content_territory_id', 'left');
            $this->db->join('time_zones', 'time_zones.time_zone_id = site.time_zone_id', 'left');
            $this->db->join('integrator', 'integrator.system_integrator_id = site.system_integrator_id', 'left');
            $this->db->join('integrator `operating_company`', 'operating_company.system_integrator_id = site.operating_company_id', 'left');
            $this->db->join('site_status', 'site_status.status_id = site.status_id', 'left');

            $this->db->where('site.account_id', $account_id);

            $archived = "( `site`.`archived` != 1 OR `site`.`archived` is NULL )";
            $this->db->where($archived);


            if (isset($where['distribution_group_id']) && !empty($where['distribution_group_id'])) {
                if (is_array($where['distribution_group_id'])) {
                    $in_array = implode(", ", $where['distribution_group_id']);
                } else {
                    $in_array = (int) $where['distribution_group_id'];
                }

                $integrator_where = "( site.system_integrator_id IN ( SELECT distribution_groups.system_integrator_id FROM distribution_groups WHERE distribution_groups.distribution_group_id IN (" . $in_array . ") ) )";
                $this->db->where($integrator_where);
            }

            if (isset($where['system_integrator_id']) && !empty($where['system_integrator_id'])) {
                $this->db->where_in("site.system_integrator_id", $where['system_integrator_id']);
            }

            if ($site_id) {
                $row = $this->db->get_where('site', ['site.site_id' => $site_id])->row();

                if (!empty($row)) {
                    ## add this as a function
                    $this->db->select('site_contact.*', false);

                    $where = [
                        'site_contact.active'   => 1,
                        'site_contact.site_id'  => $site_id
                    ];

                    $row_contact_details = $this->db->get_where('site_contact', $where)->row();

                    if (!empty($row->restrictions)) {
                        $row->site_restrictions = ( json_decode($row->restrictions) ) ? json_decode($row->restrictions) : false ;
                    } else {
                        $row->site_restrictions = null ;
                    }

                    if (!empty($row_contact_details)) {
                        $result = (object) array_merge((array) $row, (array) $row_contact_details);
                    } else {
                        $result = (object) ( (array) $row );
                    }

                    if (!empty($result->site_address_id)) {
                        ## add this as a function
                        $this->db->select('site_address.*, content_territory.country', false);

                        $this->db->join("content_territory", "content_territory.territory_id = site_address.site_territory_id", "left");

                        $where = [
                            "site_address.address_id"   => $result->site_address_id,
                            "site_address.active"       => 1,
                        ];

                        $row_address = $this->db->get_where('site_address', $where)->row();

                        if (!empty($row_address)) {
                            $result = (object) array_merge((array) $result, (array) $row_address);
                        }
                    }

                    $this->session->set_flashdata('message', 'Site record found');
                } else {
                    $this->session->set_flashdata('message', 'Site not found');
                }

                return $result;
            }

            if (isset($where['territory_id'])) {
                if (!empty($where['territory_id'])) {
                    $territories = ( is_array($where['territory_id']) ) ? $where['territory_id'] : [ $where['territory_id'] ];
                    $this->db->where_in('site.content_territory_id', $territories);
                }
                unset($where['territory_id']);
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $sites = $this->db->order_by('site.site_name')
                ->get('site');

            if ($sites->num_rows() > 0) {
                $this->session->set_flashdata('message', 'Site records found');
                $result = $sites->result();
            } else {
                $this->session->set_flashdata('message', 'Site record(s) not found');
            }
        }
        return $result;
    }

    /** Create Array Placeholder for n **/
    private function _create_empty_arrays($num_elements)
    {
        return array_fill(0, $num_elements, []);
    }

    /** Get only common elements in each child array **/
    private function _multi_intersect($arr = false)
    {
        $return = array();
        foreach ($arr as $a) {
            foreach ($arr as $b) {
                if ($a === $b) {
                    $return = array_merge($return, array_intersect($a, $b));
                    continue;
                } else {
                    $return = array_merge($return, array_intersect($a, $b));
                }
            }
        }
        return array_unique($return);
    }

    /*
    *   To generate test viewing statistics (not real)
    */
    public function generate_viewing_stats($account_id = false, $site_id = false)
    {

        ## things to do:
        ## - stats generated for month back - in August -> for July - DONE
        ## - message back                   - DONE (apart if it is a wrong provider)
        ## - rename variables               - if needed (just cosmetics)

        ## - include a clearance date
        ## - price for a movie should always stay the same
        ## - mix 65:35 current - library
        ## - include logic for amount of rooms
        ## - fix for multiple product

        $result = false;

        if (!empty($account_id) && !empty($site_id)) {
            $site_details = $this->get_sites($account_id, $site_id);

            if (!empty($site_details)) {
                if (!empty($site_details->site_reference_code)) {
                    // $site_name               = $site_details->site_name;
                    $site_reference_code        = $site_details->site_reference_code;

                    ## generating random site type
                    $site_types                 = ["hospital", "oilrig", "school", "shipping", "hotel", "hostel", "campervan", "camping", "motel", "ferry", "apart-hotel"];
                    $random_site_type           = mt_rand(0, count($site_types) - 1);
                    $site_details->site_type    = $site_types[$random_site_type];

                    ## setting the stats counter
                    $stats_counter              = 0;

                    $distribution_content       = $this->distribution_service->get_distribution_bundle_content($account_id, false, false, ['site_id' => $site_id, 'limit_to_uip' => 1 ]);
                    if (!empty($distribution_content)) {
                        ## getting site product(s)
                        $site_products              = $this->product_service->get_product($account_id, false, ["site_id" => $site_id]);

                        if (!empty($site_products)) {
                            ## resetting the array for each product
                            $viewing_stats          = [];

                            ## setting the trigger to give a f... eedback if no active product found
                            $active_product_trigger = false;
                            foreach ($site_products as $product) {
                                ## Added 17/08/2022 HOT FIX to limit to content to just UIP for viewing Stats EK
                                if ($product->content_provider_id == 2) {
                                    $product_data   = [];
                                    $today          = date('Y-m-d');

                                    if (( strtolower($product->product_status_name) == "active" )) {
                                        $active_product_trigger = true;

                                        ## checking if the product dates are within the range - if we're in the middle of products active days
                                        if (( ( $today >= $product->start_date ) && ( !validate_date($product->end_date) || $today <= $product->end_date ) )) {
                                            ## pumping the array
                                            $product_data[$product->product_id]['product_type_name']        = $product->product_type_name;
                                            $product_data[$product->product_id]['product_status_name']      = $product->product_status_name;
                                            $product_data[$product->product_id]['content_provider_id']      = $product->content_provider_id;
                                            $product_data[$product->product_id]['provider_name']            = $product->provider_name;
                                            $product_data[$product->product_id]['provider_reference_code']  = $product->provider_reference_code;
                                            $product_data[$product->product_id]['no_of_titles_value']       = $product->no_of_titles_value;
                                            $product_data[$product->product_id]['price_plan']               = $product->price_plan;
                                            $product_data[$product->product_id]['no_of_rooms']              = $product->no_of_rooms;
                                            $product_data[$product->product_id]['start_date']               = $product->start_date;

                                            if (strtolower($product->product_type_name) == "airtime") {
                                                $product_data[$product->product_id]['is_ftg']               = $product->is_airtime_ftg;
                                            } else {
                                                $product_data[$product->product_id]['is_ftg']               = $product->is_content_ftg;
                                            }

                                            ## condition to check if this is correctly set
                                            if ((int) $product_data[$product->product_id]['no_of_titles_value'] > 0) {
                                                ## a single counter for one product
                                                $counter            = 0;

                                                ## the while loop is to be used when there is not enough movies in the site inventory to loop through a couple of times to reach no_of_titles_value - now it has been switched off (2021-03-12)
                                                // while( $counter <= ( int ) $product_data[$product->product_id]['no_of_titles_value'] ){

                                                foreach ($distribution_content as $content) {
                                                    if ($counter <= (int) $product_data[$product->product_id]['no_of_titles_value']) {
                                                        ## found the correct provider
                                                        if ($content->provider_id == $product_data[$product->product_id]['content_provider_id']) {
                                                            $free_sites = ['hospital', 'oilrig', 'school', 'shipping'];

                                                            ## if( in_array( strtolower( $site_details->site_type ), $free_sites ) ){  // not implemented yet
                                                            if ($product_data[$product->product_id]['is_ftg'] > 0) {
                                                                $number_of_plays    = mt_rand(1, 4); ## [0,7]
                                                                $price              = "0";
                                                            } else {
                                                                $number_of_plays    = mt_rand(1, 4); ## [0,4]

                                                                ## prices
                                                                $Current_films      = [325,350,375,395,400,425,450,475,495,500,525,550,575,600,660,699,700,750,799,850];
                                                                $Library_films      = [150,175,195,200,225,245,250,275,299,300,325,350,375,400,425,450,475,499,500,550];

                                                                    $random_number_1    = mt_rand(0, 19);
                                                                    $random_number_2    = mt_rand(1, 2);

                                                                    $price          = ( $random_number_2 > 1 ) ? (float) $Current_films[$random_number_1] / 100 : (float) $Library_films[$random_number_1] / 100;
                                                            }

                                                            for ($i = 0; $i < $number_of_plays; $i++) {
                                                                $viewing_stats[$stats_counter]['title']     = html_entity_decode($content->title, ENT_QUOTES, 'UTF-8');
                                                                $viewing_stats[$stats_counter]['provider']  = $content->provider_name;

                                                                ## first time of the previous month as a Time stamp
                                                                $first_time                                 = mktime(0, 0, 0, date("m") - 1, 1);
                                                                $previous_month                             = ( date('m') == 1 ) ? 12 : (int) date('m') - 1;
                                                                $previous_year                              = ( date('m') == 1 ) ? (int) date('Y') - 1 : (int) date('Y');
                                                                $days_in_month                              = cal_days_in_month(CAL_GREGORIAN, $previous_month, $previous_year);

                                                                ## last time of the previous month as a Time stamp
                                                                $last_time                                  = mktime(23, 59, 59, $previous_month, $days_in_month);
                                                                $int                                        = mt_rand($first_time, $last_time);
                                                                $string                                                             = date("Y-m-d H:i:s", $int);
                                                                $viewing_stats[$stats_counter]['date']      = $string;
                                                                $viewing_stats[$stats_counter]['price']     = $price;
                                                                $viewing_stats[$stats_counter]['currency']  = $site_details->invoice_currency_name;

                                                                $stats_counter++;
                                                            }
                                                        }
                                                    }

                                                    $counter++;
                                                }
                                                    ## what if the provider is completely different?
                                                ## }
                                            } else {
                                                $this->session->set_flashdata('message', 'Incorrect <i>Number of Titles</i> value for product.');
                                                return false;
                                            }
                                        } else {
                                            // $this->session->set_flashdata( 'message', 'Product dates outside today.' );
                                        }
                                    }

                                    ## Use this variable for the Product ID in the Report Name
                                    $report_product_id = $product->product_id;
                                }
                            }

                            if ($active_product_trigger) {
                                $csv_data = $viewing_stats;

                                if (!empty($csv_data)) {
                                    $document_path  = '_account_assets/accounts/' . $account_id . '/site/' . $site_id . '/';
                                    $upload_path    = $this->app_root . $document_path;

                                    if (!is_dir($upload_path)) {
                                        if (!mkdir($upload_path, 0755, true)) {
                                            $this->session->set_flashdata('message', 'Error: Unable to create location');
                                            return false;
                                        }
                                    }

                                    $headers        = ["Title", "Provider", "Viewing Date", "Price", "Currency"];
                                    $data           = array_to_csv($csv_data, $headers);
                                    // $file_name       = $site_name.'-'.date( 'dmYHi' ).'.csv';

                                    //Added to ensure correct Product is picked @EK 17/08/2022
                                    $report_product_id = !empty($report_product_id) ? $report_product_id : $product->product_id;

                                    $file_name      = $site_reference_code . '_' . $report_product_id . '.csv';
                                    $file_path      = $upload_path . $file_name;

                                    if (write_file($upload_path . $file_name, $data)) {
                                        $result = [
                                            'timestamp'         => date('d.m.Y H:i:s'),
                                            'expires_at'        => date('d.m.Y H:i:s', strtotime('+1 hour')),
                                            'file_name'         => $file_name,
                                            'file_path'         => $file_path,
                                            'file_link'         => base_url($document_path . $file_name),
                                            'document_location' => $document_path
                                        ];
                                    }
                                } else {
                                    $this->session->set_flashdata('message', 'No stats generated.');
                                }
                            } else {
                                $this->session->set_flashdata('message', 'No active product(s).');
                            }
                        } else {
                            $this->session->set_flashdata('message', 'No product available for this content.');
                        }
                    } else {
                        $this->session->set_flashdata('message', 'There is no distribution content.');
                    }
                } else {
                    $this->session->set_flashdata('message', 'Site has no reference code set.');
                }
            } else {
                $this->session->set_flashdata('message', 'Site record not found');
            }
        } else {
            $this->session->set_flashdata('message', 'Required data not provided');
        }

        return $result;
    }

    /*
    *   Function to update (add and remove) the months from the site window
    */
    public function update_window($account_id = false, $site_id = false, $incoming_months = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($site_id)) {
            $where['site_id']   = $site_id;
            $current_months = $this->get_window($account_id, $where);

            $to_be_created = $to_be_deleted = [];

            $incoming_months = convert_to_array($incoming_months);

            if (!empty($current_months)) {
                // we do have some months

                if (!empty($incoming_months)) {
                    // and some incoming_months are incoming

                    foreach ($current_months as $key => $month) {
                        if (in_array($month->month_id, $incoming_months)) {
                            // if current month is in incoming array - remove from the array
                            $incoming_months = array_diff($incoming_months, [$month->month_id]);
                        } else {
                            // current is not in the incoming - so has to be deleted
                            $to_be_deleted[] = $month;
                        }
                    }
                } else {
                    // all to delete
                    $to_be_deleted = $current_months;
                }
            } else {
                // we do not have any months
            }
            $to_be_created = $incoming_months;


            if (!empty($to_be_created)) {
                foreach ($to_be_created as $mta_key => $month_to_add) {
                    $month_to_add_set = [];
                    $month_to_add_set = [
                        "account_id"    => $account_id,
                        "site_id"       => $site_id,
                        "month_id"      => $month_to_add,
                        "created_by"    => $this->ion_auth->_current_user->id
                    ];
                    $this->db->insert("site_reporting_window_month", $month_to_add_set);
                }
            }

            if (!empty($to_be_deleted)) {
                foreach ($to_be_deleted as $mtd_key => $month_to_delete) {
                    $where_month_to_delete_set = [];
                    $where_month_to_delete_set = [
                        "account_id"        => $account_id,
                        "site_id"           => $site_id,
                        "window_month_id"   => $month_to_delete->window_month_id, // more reliable
                    ];
                    $this->db->delete("site_reporting_window_month", $where_month_to_delete_set);
                }
            }

            $result = $this->get_window($account_id, ['site_id' => $site_id]);
            $this->session->set_flashdata('message', 'Site Window has been updated');
        } else {
            $this->session->set_flashdata('message', 'Required data not provided');
        }

        return $result;
    }



    public function get_window($account_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where)) {
                    if (!empty($where['site_id'])) {
                        $site_id = $where['site_id'];
                        $this->db->where("site_id", $site_id);
                        unset($where['site_id']);
                    }

                    if (!empty($where['month_id'])) {
                        $month_id = $where['month_id'];
                        $this->db->where("window_month_id", $month_id);
                        unset($where['month_id']);
                    }

                    if (!empty($where)) {
                        $this->db->where($where);
                    }
                } else {
                    $this->session->set_flashdata('message', 'Error processing the conditions');
                }
            }

            $this->db->select("srwm.*", false);
            $this->db->where("srwm.active", 1);

            $query = $this->db->get("site_reporting_window_month `srwm`");

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Window month(s) found');
            } else {
                $this->session->set_flashdata('message', 'Window month(s) not found');
            }
        } else {
            $this->session->set_flashdata('message', 'Required data not provided');
        }
        return $result;
    }


    /*
    *   Get Site By product type ID
    */
    public function get_site_by_product_type_id($account_id = false, $product_type_id = false, $where = false)
    {
        $result = false;

        if (!empty($account_id) && $this->account_service->check_account_status($account_id)) {
            $where = convert_to_array($where);
            if (!empty($where)) {
            }

            $this->db->select('product.product_id, product.product_name', false);
            $this->db->select('site.site_id, site.site_name, concat( user.first_name," ",user.last_name ) `created_by`', false);

            $this->db->join('site', 'site.site_id = product.site_id', 'left');
            $this->db->join('user', 'user.id = site.created_by', 'left');

            $this->db->where("product.product_type_id", $product_type_id);
            $this->db->where('site.account_id', $account_id);

            $site_archived = "( `site`.`archived` != 1 OR `site`.`archived` is NULL )";
            $this->db->where($site_archived);

            $product_archived = "( `product`.`archived` != 1 OR `product`.`archived` is NULL )";
            $this->db->where($product_archived);

            $this->db->order_by('site.site_name ASC');

            $this->db->group_by('site.site_id');
            $sites = $this->db->get('product');

            if ($sites->num_rows() > 0) {
                $this->session->set_flashdata('message', 'Site records found');
                $result = $sites->result();
            } else {
                $this->session->set_flashdata('message', 'Site record(s) not found');
            }
        }
        return $result;
    }
}
