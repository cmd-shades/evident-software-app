<?php

namespace Application\Service\Models;

use System\Core\CI_Model;

class Product_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("serviceapp/Easel_Api_model", "easel_service");
    }


    /*
    *   Create new Product
    */
    public function create_product($account_id = false, $product_data = false)
    {
        $result = false;
        if (!empty($account_id)) {
            $data               = [];

            $product_data       = convert_to_array($product_data);
            $product_details    = ( !empty($product_data['product_details']) ) ? ( $product_data['product_details'] ) : false ;

            if (isset($product_details['price_plans'])) {
                if (!empty($product_details['price_plans'])) {
                    $price_plans    = $product_details['price_plans'];
                }
                unset($product_details['price_plans']);
            }

            if (!empty($product_details)) {
                foreach ($product_details as $key => $value) {
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
                    if (!empty($data['product_name'])) {
                        if (!empty($data['product_type_id'])) {
                            $product_type_name = false;
                            $product_type_query = $this->db->get_where("setting", ["account_id" => $account_id, "setting_id" => $data['product_type_id'] ])->row();

                            if (!empty($product_type_query->setting_value)) {
                                $product_type_name = strtolower($product_type_query->setting_value);
                            } else {
                                $this->session->set_flashdata('message', 'Incorrect Product Type.');
                                return $result;
                            }

                            ## check if Airtime PIN already exists for any airtime product type -> also if is not empty
                            if (in_array(strtolower($product_type_name), ["airtime"])) {
                                if (!empty($data['airtime_pin'])) {
                                    $pin_where = [
                                        "account_id"    =>  $account_id,
                                        "airtime_pin"   =>  $data['airtime_pin'],
                                    ];

                                    $airtime_pin_exists     = false;
                                    $airtime_pin_query      = $this->db->get_where("product", $pin_where)->row();

                                    if (!empty($airtime_pin_query)) {
                                        $this->session->set_flashdata('message', 'Airtime PIN already exists.');
                                        return $result;
                                    }
                                } else {
                                    $this->session->set_flashdata('message', 'Airtime PIN cannot be empty for Airtime Product.');
                                    return $result;
                                }
                            }

                            ## add an automatic product status
                            if (!isset($data['product_status_id']) || empty($data['product_status_id'])) {
                                $this->db->select("setting.setting_id", false);

                                $this->db->join("setting_name", "setting_name.setting_name_id = setting.setting_name_id", "left");

                                $this->db->where("setting.setting_value", "active");
                                $this->db->where("setting_name.setting_name_group", "2_product_status");

                                $this->db->where("setting.is_active", "1");
                                $arch_where_1 = "( setting.archived != 1 or setting.archived is NULL )";
                                $this->db->where($arch_where_1);

                                $this->db->where("setting_name.is_active", "1");
                                $arch_where_2 = "( setting_name.archived != 1 or setting_name.archived is NULL )";
                                $this->db->where($arch_where_2);

                                $this->db->limit(1);

                                $status_id_query = $this->db->get("setting");

                                if ($status_id_query->num_rows() > 0) {
                                    $data['product_status_id'] = (int) $status_id_query->row()->setting_id;
                                } else {
                                    ## not adding status - we do not have this value in the settings
                                }
                            }

                            $data['account_id']     = $account_id;
                            $data['status_id']      = 1;
                            $data['created_by']     = $this->ion_auth->_current_user->id;
                            $new_product_data       = $this->ssid_common->_filter_data('product', $data);

                            $this->db->insert('product', $new_product_data);

                            if ($this->db->trans_status() !== false) {
                                $new_product_data['product_id'] = $product_insert_id = $this->db->insert_id();

                                if (!empty($product_insert_id) && in_array($product_type_name, ["airtime"])) {
                                    ## The Product insert was successful and we're dealing with the Airtime product now
                                    // for each product we're going to create a:
                                    // - segment - to store the PIN
                                    // - market
                                    // - price bands
                                    // - a. windows

                                    ## - Get the site data first.
                                    $site_data = $this->site_service->get_sites($account_id, $new_product_data['site_id']);

                                    ## Create a segment - each product should have own segment due to PIN
                                    $segment_message        = '';
                                    $segment_data           = [
                                        // newest changes to Easel API - ID of the segment is a PIN (2/7/2021)
                                        'id'                => ( !empty($new_product_data['airtime_pin']) ) ? (string) trim($new_product_data['airtime_pin']) : '',
                                        'name'              => ( !empty($site_data->site_name) ) ? ( trim($site_data->site_name) . ( ( isset($new_product_data['is_airtime_ftg']) && !empty($new_product_data['is_airtime_ftg']) ) ? '_VIP' : '' ) . '-segment' ) : ( ( !empty($new_product_data['product_name']) ) ? $new_product_data['product_name'] . '-segment' : 'Segment Default Name' ),
                                        'description'       => ( !empty($new_product_data['product_description']) ) ? html_escape(trim($new_product_data['product_description'])) : '',
                                        'type'              => 'device-list',

                                        // This is required part - will be added at the Coggins model
                                        // 'data'           => [
                                            // "deviceList" => [],
                                            // "operator"       => "contains"
                                        // ],
                                    ];

                                    $easel_created_segment  = $this->easel_service->create_segment($account_id, $segment_data);
                                    $segment_message        .= ( !empty($easel_created_segment->message) ) ? $easel_created_segment->message : 'There was a problem on Segment creation on Easel';
                                    ## Segment creation - end

## Debugging for the segment creation
                                    $debug_data = [
                                    "product_id"    => $new_product_data['product_id'],
                                    "price_plan_id" => null,
                                    "string_name"   => "Segment insert _data",
                                    "query_string"  => json_encode($segment_data),
                                    ];
                                    $this->db->insert("tmp_product_debugging", $debug_data);

                                    $debug_data = [
                                    "product_id"    => $new_product_data['product_id'],
                                    "price_plan_id" => null,
                                    "string_name"   => "Segment easel response",
                                    "query_string"  => json_encode($easel_created_segment),
                                    ];
                                    $this->db->insert("tmp_product_debugging", $debug_data);
## Debugging for the segment creation - end


                                    ## create segment in cacti
                                    if ($easel_created_segment->success == true && !empty($easel_created_segment->data->id)) {
                                        $segment_cacti_data_message                 = '';
                                        $segment_cacti_data['segment_name']         = ( !empty($segment_data['name']) ) ? $segment_data['name'] : '' ;
                                        $segment_cacti_data['product_id']           = ( !empty($new_product_data['product_id']) ) ? $new_product_data['product_id'] : '' ;
                                        $segment_cacti_data['airtime_segment_ref']  = $easel_created_segment->data->id;
                                        $segment_cacti_data['description']          = ( !empty($segment_data['description']) ) ? $segment_data['description'] : '' ;
                                        $segment_cacti_data['type']                 = ( !empty($segment_data['type']) ) ? $segment_data['type'] : '' ;
                                        $segment_cacti_data['pin']                  = ( !empty($segment_data['id']) ) ? $segment_data['id'] : '' ;
                                        $segment_cacti_data['created_by']           = $this->ion_auth->_current_user->id;
                                        $segment_cacti_data                         = $this->ssid_common->_filter_data('segment', $segment_cacti_data);
                                        $query                                      = $this->db->insert("segment", $segment_cacti_data);

                                        if ($this->db->trans_status() !== false) {
                                            $segment_cacti_data_message .= "Created Segment in CaCTi";
                                        } else {
                                            $segment_cacti_data_message .= "Cannot create a Segment in CaCTi";
                                        }
                                        ## create segment in cacti - END


                                        ## Update product with the segment ID
                                        $update_message = '';

                                        ## Update Product
                                        $product_update_data = [
                                            "airtime_segment_ref"   => $easel_created_segment->data->id,
                                        ];

                                        $where_upd = [];
                                        $where_upd = [
                                            "account_id"        => $account_id,
                                            "product_id"        => $new_product_data['product_id'],
                                        ];

                                        $upd_query = $this->db->update("product", $product_update_data, $where_upd);

                                        if ($this->db->affected_rows() > 0) {
                                            $new_product_data['airtime_segment_ref'] = $easel_created_segment->data->id;
                                            $update_message .= 'The product profile has been updated with Easel Segment ID. ';
                                        } else {
                                            $update_message .= 'No new changes have been applied to the Product profile. ';
                                        }
                                    }
                                    ## Update product with the segment ID - END



                                    ## Market creation
                                    $market_message             = '';
                                    $market_data                = [
                                        'name'          => ( !empty($site_data->site_name) )  ? trim($site_data->site_name) . ( ( isset($new_product_data['is_airtime_ftg']) && !empty($new_product_data['is_airtime_ftg']) ) ? '_VIP' : '' ) . '-market' : 'Market Default Name' ,
                                        'description'   => ( !empty($new_product_data['product_description']) ) ? trim($new_product_data['product_description']) : '',
                                        'ordering'      => ( !empty($site_data->ordering) ) ? ( (int) $site_data->ordering )    : 99,
                                    ];

                                    if (!empty($easel_created_segment->data->id)) {
                                        $expression                     = "";
                                        $expression                     = "segment_" . $easel_created_segment->data->id;
                                        $market_data['expression']      = [$expression];

                                        $sub_expression                 = [];
                                        $market_data['subExpressions']  = $sub_expression;
                                    }

                                    $easel_created_market       = $this->easel_service->create_market($account_id, $market_data);
                                    $market_message             = ( !empty($easel_created_market->message) ) ? $easel_created_market->message : 'There was a problem on Market creation on Easel';

                                    ## Market creation - end

                                    ## Market creation - debugging
                                    $debug_data = [
                                    "product_id"    => $new_product_data['product_id'],
                                    "price_plan_id" => null,
                                    "string_name"   => "Market insert _data",
                                    "query_string"  => json_encode($market_data),
                                    ];
                                    $this->db->insert("tmp_product_debugging", $debug_data);


                                    $debug_data = [
                                    "product_id"    => $new_product_data['product_id'],
                                    "price_plan_id" => null,
                                    "string_name"   => "Market easel response",
                                    "query_string"  => json_encode($easel_created_market),
                                    ];
                                    $this->db->insert("tmp_product_debugging", $debug_data);

                                    ## Market creation - debugging - end


                                    ## If Market creation on EASEL was successful
                                    $update_message = '';
                                    if ($easel_created_market->success == true && !empty($easel_created_market->data->id)) {
                                        ## Update Product
                                        $product_update_data = [
                                            "airtime_market_ref"    => $easel_created_market->data->id,
                                        ];

                                        $where_upd = [];
                                        $where_upd = [
                                            "account_id"        => $account_id,
                                            "product_id"        => $new_product_data['product_id'],
                                        ];

                                        $upd_query = $this->db->update("product", $product_update_data, $where_upd);
## Debug the product update
                                        $debug_query = $this->db->last_query();
                                        $debug_data = [
                                        "product_id"    => $new_product_data['product_id'],
                                        "price_plan_id" => null,
                                        "string_name"   => "Product update with market ID",
                                        "query_string"  => json_encode($debug_query),
                                        ];
                                        $this->db->insert("tmp_product_debugging", $debug_data);

                                        if ($this->db->affected_rows() > 0) {
                                            $new_product_data['airtime_market_ref'] = $easel_created_market->data->id;
                                            $update_message .= 'The product profile has been updated with Easel Market ID. ';
                                        } else {
                                            $update_message .= 'No new changes have been applied to the Product profile. ';
                                        }


                                        // check if site needs to be updated
                                        if (!( $site_data->is_airtime_active )) {
                                            ## Update Site
                                            $site_update_data = [
                                                "is_airtime_active"     => 1,
                                                "last_modified_by"      => $this->ion_auth->_current_user->id,
                                            ];

                                            $where_upd = [];
                                            $where_upd = [
                                                "account_id"        => $account_id,
                                                "site_id"           => $new_product_data['site_id'],
                                            ];

                                            $site_upd_query = $this->db->update("site", $site_update_data, $where_upd);

                                            if ($this->db->affected_rows() > 0) {
                                                $update_message .= 'The site profile has been updated to Easel Active. ';
                                            } else {
                                                $update_message .= 'No new changes have been applied to the Site profile. ';
                                            }
                                        }
                                    }
                                }

                                if (!empty($price_plans)) {
                                    ## it seems after each price plan/band I have to create an availability window

                                    $i                              = 1;
                                    $where["product_id"]            = $product_insert_id;
                                    $where["site_reference_code"]   = $site_data->site_reference_code;
                                    $where["airtime_market_ref"]    = ( !empty($easel_created_market->data->id) ) ? $easel_created_market->data->id : '' ;
                                    if (in_array($product_type_name, ["airtime"])) {
                                        $where["is_airtime"]    = true;
                                    }

                                    foreach ($price_plans as $price_plan) {
                                        $saved_plan             = false;
                                        $saved_plan_message     = '';
                                        $saved_plan             = $this->save_product_price_plan($account_id, $where, $price_plan);

                                        $saved_plans[]          = $saved_plan;
                                        $saved_plan_message     .= ( !empty($this->session->flashdata('saved_plans')) ) ? $this->session->flashdata('saved_plans') : '' ;
                                    }
                                }

                                $result         = $this->db->get_where("product", ["account_id" => $account_id, "product_id" => $product_insert_id ])->row();
                                $product_message = 'Product record created successfully.';

                                $message = '';
                                $message .= ( !empty($easel_message) ) ? trim($easel_message) : '' ;
                                $message .= ( !empty($update_message) ) ? ' ' . trim($update_message) : '' ;
                                $message .= ( !empty($saved_plans_message) ) ? ' ' . trim($saved_plans_message) : '' ;
                                $message .= ( !empty($product_message) ) ? ' ' . trim($product_message) : '' ;

                                $this->session->set_flashdata('message', $message);
                            }
                        } else {
                            $this->session->set_flashdata('message', 'Missing Product Type.');
                        }
                    } else {
                        $this->session->set_flashdata('message', 'Product Name cannot be empty.');
                    }
                }
            } else {
                $this->session->set_flashdata('message', 'No Product details.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id supplied.');
        }
        return $result;
    }


    /*
    *    Get product(s) data
    */
    public function get_product($account_id = false, $product_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select("product.*", false);
            $this->db->select("product_type_settings.setting_value `product_type_name`, product_status.setting_value `product_status_name`, no_of_titles.setting_value `no_of_titles_value`", false);
            $this->db->select("content_provider.provider_name, content_provider.provider_reference_code", false);

            $this->db->join("content_provider", "content_provider.provider_id = product.content_provider_id", "left");
            $this->db->join("setting `product_type_settings`", "product_type_settings.setting_id = product.product_type_id", "left");
            $this->db->join("setting `product_status`", "product_status.setting_id = product.product_status_id", "left");
            $this->db->join("setting `no_of_titles`", "no_of_titles.setting_id = product.no_of_titles_id", "left");

            if (!empty($product_id)) {
                $this->db->where("product_id", $product_id);
            }

            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where['site_id'])) {
                    $site_id = $where['site_id'];
                    $this->db->where("site_id", $site_id);
                    unset($where['site_id']);
                }

                if (!empty($where['provider_id'])) {
                    $provider_id = $where['provider_id'];
                    $this->db->where("content_provider_id", $provider_id);
                    unset($where['provider_id']);
                }

                if (!empty($where['product_type_id'])) {
                    $product_type_id = $where['product_type_id'];
                    $this->db->where("product.product_type_id", $product_type_id);
                    unset($where['product_type_id']);
                }

                if (!empty($where)) {
                    $this->db->where($where);
                }
            }

            $arch_where = "( product.archived != 1 or product.archived is NULL )";
            $this->db->where($arch_where);
            $this->db->where("product.active", 1);
            $query = $this->db->get("product");

            if (!empty($query->num_rows() && $query->num_rows() > 0)) {
                $dataset = $query->result();
                if (!empty($product_id)) {
                    $result = $dataset[0];
                } else {
                    foreach ($dataset as $row) {
                        $result[$row->product_id] = $row;
                        $result[$row->product_id]->price_plan = $this->get_product_price_plan($account_id, ["product_id" => $row->product_id]);
                    }
                }
                $this->session->set_flashdata('message', 'Product(s) data found.');
            } else {
                $this->session->set_flashdata('message', 'Product(s) data not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID not supplied.');
        }

        return $result;
    }


    public function add_package_to_product($account_id = false, $product_id = false, $package_data = false)
    {
        $result = false;
        if (!empty($account_id)) {
            if (!empty($product_id) && !empty($package_data)) {
                $package_data   = convert_to_array($package_data);
                $package_id     = $data = false;

                if (!empty($package_data['package_id'])) {
                    ## package has been created just needs to be added to the product
                    $package_id = $package_data['package_id'];
                } else {
                    ## create package first and add to the product then
                    $new_package = $this->create_package($account_id, $package_data);

                    if (!empty($new_package->package_id)) {
                        $package_id = $new_package->package_id;
                    }
                }

                $data = [
                    "account_id"        => $account_id,
                    "product_id"        => $product_id,
                    "package_id"        => $package_id,
                    "created_by"        => $this->ion_auth->_current_user->id,
                ];

                $this->db->insert('product_packages', $data);

                if ($this->db->affected_rows() > 0) {
                    $insert_id      = $this->db->insert_id();
                    $result         = $this->db->get_where("product_packages", ["account_id" => $account_id, "product_package_id" => $insert_id ])->row();
                    $this->session->set_flashdata('message', 'Package(s) added successfully.');
                }
            } else {
                $this->session->set_flashdata('message', 'Product ID or Package Data not supplied.');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID not supplied.');
        }

        return $result;
    }

    /*
    *   Delete product function. Airtime PIN needs to stay unique!
    */
    public function delete_product($account_id = false, $product_id = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($product_id)) {
            $product_b4 = $this->db->get_where("product", ["account_id" => $account_id, "product_id" => $product_id])->row();
            if (!empty($product_b4)) {
                $this->db->delete("product", ['account_id' => $account_id, 'product_id' => $product_id]);

                if ($this->db->trans_status() !== false) {
                    $result     = true;

                    ## Delete children of the product: product_price_plan
                    $this->db->delete("product_price_plan", ['account_id' => $account_id, 'product_id' => $product_id]);

                    $this->session->set_flashdata('message', 'Product deleted successfully.');
                } else {
                    $this->session->set_flashdata('message', 'Product Delete request failed.');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Product ID or Account ID supplied.');
        }
        return $result;
    }


    public function delete_product_package($account_id = false, $product_package_id = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($product_package_id)) {
            $product_pckg_b4 = $this->db->get_where("product_packages", ["account_id" => $account_id, "product_package_id" => $product_package_id]);
            if (!empty($product_pckg_b4)) {
                $delete_data['last_modified_by']        = $this->ion_auth->_current_user->id;
                $delete_data['active']                  = null;
                $delete_data['archived']                = 1;

                $this->db->update("product_packages", $delete_data, ['account_id' => $account_id, 'product_package_id' => $product_package_id]);

                if ($this->db->trans_status() !== false) {
                    $result     = true;
                    $this->session->set_flashdata('message', 'Product Package deleted successfully.');
                } else {
                    $this->session->set_flashdata('message', 'Product Package Delete request failed.');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Product Package ID or Account ID supplied.');
        }
        return $result;
    }



    /*
    *    Get product(s) data
    */
    public function get_product_package($account_id = false, $product_package_id = false, $where = false, $unorganized = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select("product_packages.*", false);

            if (!empty($product_package_id)) {
                $this->db->where("product_package_id", $product_package_id);
            }

            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where['product_id'])) {
                    $product_id = $where['product_id'];
                    $this->db->where("product_id", $product_id);
                    unset($where['product_id']);
                }

                if (!empty($where['package_id'])) {
                    $package_id = $where['package_id'];
                    $this->db->where("package_id", $package_id);
                    unset($where['package_id']);
                }

                if (is_array($where) && !empty($where)) {
                    $this->db->where($where);
                }
            }

            $arch_where = "( product_packages.archived != 1 or product_packages.archived is NULL )";
            $this->db->where($arch_where);
            $this->db->where("product_packages.active", 1);
            $query = $this->db->get("product_packages");

            if (!empty($query->num_rows() && $query->num_rows() > 0)) {
                ## get packages
                foreach ($query->result() as $key => $row) {
                    $product_packages   = [];
                    $product_packages = $this->db->get_where("content_package", ["content_package.account_id" => $account_id, "package_id" => $row->package_id ])->result();

                    $query->result()[$key]->product_packages = ( !empty($product_packages) ) ? $product_packages : null ;
                }

                if ($unorganized) {
                    $result = $query->result();
                } else {
                    $dataset = $query->result();
                    if (!empty($product_package_id)) {
                        $result = $dataset[0];
                    } else {
                        foreach ($dataset as $row) {
                            $result[$row->product_packages] = $row;
                        }
                    }
                }
                $this->session->set_flashdata('message', 'Product Package(s) data found.');
            } else {
                $this->session->set_flashdata('message', 'Product Package(s) data not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID not supplied.');
        }

        return $result;
    }


    public function create_package($account_id = false, $package_data = false)
    {
        $result = false;

        ## package name required
        if (!empty($account_id) && !empty($package_data) && !empty($package_data['package_name'])) {
            $data = [];

            foreach ($package_data as $key => $value) {
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
                } elseif (in_array($key, string_to_json_columns())) {
                    $value = string_to_json($value);
                } else {
                    $value = trim($value);
                }
                $data[$key] = $value;
            }

            if (!empty($data)) {
                $data['created_by']     = $this->ion_auth->_current_user->id;
                $data['account_id']     = $account_id;
                $create_data            = $this->ssid_common->_filter_data('content_package', $data);

                $query = $this->db->insert("content_package", $create_data);

                if ($this->db->affected_rows() > 0) {
                    $insert_id = $this->db->insert_id();
                    $result = $this->db->get_where("content_package", ["account_id" => $account_id, "package_id" => $insert_id])->row();
                    $this->session->set_flashdata('message', 'Package created successfully');
                } else {
                    $this->session->set_flashdata('message', 'Package hasn\'t been created');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID or Required Package data missing');
        }

        return $result;
    }

    /*
    *    Get package(s) data
    */
    public function get_package($account_id = false, $package_id = false, $where = false, $unorganized = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select("content_package.*", false);

            if (!empty($package_id)) {
                $this->db->where("package_id", $package_id);
            }

            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where['package_type_id'])) {
                    $package_type_id = $where['package_type_id'];
                    $this->db->where("package_type_id", $package_type_id);
                    unset($where['package_type_id']);
                }

                if (!empty($where['content_provider_id'])) {
                    $content_provider_id = $where['content_provider_id'];
                    $this->db->where("content_provider_id", $content_provider_id);
                    unset($where['content_provider_id']);
                }

                if (is_array($where) && !empty($where)) {
                    $this->db->where($where);
                }
            }

            $arch_where = "( content_package.archived != 1 or content_package.archived is NULL )";
            $this->db->where($arch_where);
            $this->db->where("content_package.active", 1);
            $query = $this->db->get("content_package");

            if (!empty($query->num_rows() && $query->num_rows() > 0)) {
                if ($unorganized) {
                    $result = $query->result();
                } else {
                    $dataset = $query->result();
                    if (!empty($package_id)) {
                        $result = $dataset[0];
                    } else {
                        foreach ($dataset as $row) {
                            $result[$row->package_id] = $row;
                        }
                    }
                }
                $this->session->set_flashdata('message', 'Package(s) data found.');
            } else {
                $this->session->set_flashdata('message', 'Package(s) data not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Account ID not supplied.');
        }
        return $result;
    }


    /*
    *   Update package
    */
    public function update_package($account_id = false, $package_id = false, $package_data = false)
    {
        $result = false;
        if (!empty($account_id)  && !empty($package_id) && ( !empty($package_data) )) {
            $data = [];
            $package_data = json_decode($package_data);

            if (!empty($package_data)) {
                foreach ($package_data as $key => $value) {
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
                    } elseif (in_array($key, string_to_json_columns())) {
                        $value = string_to_json($value);
                    } else {
                        $value = trim($value);
                    }
                    $data[$key] = $value;
                }

                if (!empty($data)) {
                    $data['modified_by']    = $this->ion_auth->_current_user->id;

                    $exempt_columns         = ["account_id", "package_id", "created_by", "date_created"];
                    $u_package_data         = $this->ssid_common->_filter_data('content_package', $data, $exempt_columns);

                    $this->db->update('content_package', $u_package_data, ["package_id" => $package_id, "account_id" => $account_id]);

                    if ($this->db->trans_status() !== false) {
                        $result = $this->get_package($account_id, $package_id);
                        $this->session->set_flashdata('message', 'Package record updated successfully.');
                    } else {
                        $this->session->set_flashdata('message', 'Package record hasn\'t been updated.');
                    }
                }
            } else {
                $this->session->set_flashdata('message', 'There was an error processing the Package Data');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id or Package Data supplied.');
        }
        return $result;
    }


    /*
    *   Update product data
    */
    public function update($account_id = false, $product_id = false, $product_data = false)
    {
        $result = false;
        if (!empty($account_id)  && !empty($product_id) && ( !empty($product_data) )) {
            $data = [];
            $product_data = json_decode($product_data);


            if (!empty($product_data->price_plans)) {
                $price_plans = [];

                $price_plans = json_decode(json_encode($product_data->price_plans), true);
                $saved_plans = $this->update_product_price_plan($account_id, $product_id, $price_plans);

                unset($product_data->price_plans);
            }

            if (!empty($product_data)) {
                foreach ($product_data as $key => $value) {
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
                    } elseif (in_array($key, string_to_json_columns())) {
                        $value = string_to_json($value);
                    } else {
                        $value = trim($value);
                    }
                    $data[$key] = $value;
                }

                if (!empty($data)) {
                    $data['last_modified_by']   = $this->ion_auth->_current_user->id;

                    $exempt_columns         = ["account_id", "product_id", "created_by", "date_created"];
                    $u_product_data         = $this->ssid_common->_filter_data('product', $data, $exempt_columns);
                    $product_b4             = $this->get_product($account_id, $product_id);

                    $airtime_pin_exists = false;
                    ## just an additional check for the airtime product type
                    if (strtolower($product_b4->product_type_name) == "airtime") {
                        $this->db->where('airtime_pin', $u_product_data['airtime_pin']);
                        $this->db->where('account_id', $account_id);
                        $this->db->where_not_in('product_id', $product_id);
                        $airtime_pin_exists = $this->db->get("product")->row();
                    }

                    if (!$airtime_pin_exists) {
                        $this->db->update('product', $u_product_data, ["product_id" => $product_id, "account_id" => $account_id]);

                        if ($this->db->trans_status() !== false) {
                            $result = $this->get_product($account_id, $product_id);

                            $product_message    = 'Product record updated successfully. ';
                            $plan_message       = !empty($this->session->flashdata("plan_message")) ? $this->session->flashdata("plan_message") : '' ;

                            $this->session->set_flashdata('message', $product_message . $plan_message);
                        } else {
                            $this->session->set_flashdata('message', 'Product record hasn\'t been updated.');
                        }
                    } else {
                        $this->session->set_flashdata('message', 'Airtime PIN already exists');
                    }
                }
            } else {
                $this->session->set_flashdata('message', 'There was an error processing the Product Data');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id or Product Data supplied.');
        }
        return $result;
    }



    public function save_product_price_plan($account_id = false, $where = false, $price_plan = false)
    {
        $result = false;
        if (!empty($account_id)) {
            if (!empty($where['product_id'])) {
                if (!empty($price_plan)) {
                    $insert_data = false;
                    $insert_data = [
                        "account_id"    => $account_id,
                        "product_id"    => $where['product_id'],
                        "provider_id"   => ( !empty($price_plan['provider_id']) ) ? $price_plan['provider_id'] : null ,
                        "price_plan_id" => ( !empty($price_plan['plan_id']) ) ? $price_plan['plan_id'] : null ,
                        "plan_price"    => ( !empty($price_plan['plan_price']) ) ? $price_plan['plan_price'] : null ,
                        "created_by"    => $this->ion_auth->_current_user->id,
                    ];


                    if (!empty($insert_data)) {
                        $query = $this->db->insert("product_price_plan", $insert_data);

                        if ($this->db->affected_rows() > 0) {
                            $insert_id = false;
                            $insert_id = $this->db->insert_id();

## Price Plan Insert Debugging
                            $debug_data = [
                            "product_id"    => $where['product_id'],
                            "price_plan_id" => $insert_id,
                            "string_name"   => "product price plan insert_data",
                            "query_string"  => json_encode($insert_data),
                            ];
                            $this->db->insert("tmp_product_debugging", $debug_data);
## Price Plan Insert Debugging - END


                            ## Line below is not needed now if we do not need additional elements like: date_creation, archived, active:
                            ## $added_plan = $this->db->get_where( "product_price_plan", ["account_id" => $account_id, "product_price_plan_id" => $insert_id] )->row();

                            $added_plan                             = $insert_data;
                            $added_plan['product_price_plan_id']    = $insert_id;

                            $this->session->set_flashdata('message', 'Product Price Plan successfully added');

                            if (( $where['is_airtime'] == true )) {
                                ## prepare data to save plan to EASEL
                                $this->db->select("content_provider.provider_name", false);
                                $this->db->select("site.site_id, site.site_reference_code", false);
                                $this->db->select("price_plan.price_plan_name", false);
                                $this->db->select("product_price_plan.plan_price", false);
                                $this->db->select("setting.setting_value `currency`", false);

                                $this->db->join("content_provider", "content_provider.provider_id=product_price_plan.provider_id", "left");
                                $this->db->join("product", "product.product_id=product_price_plan.product_id", "left");
                                $this->db->join("price_plan", "price_plan.plan_id=product_price_plan.price_plan_id", "left");
                                $this->db->join("site", "product.site_id=site.site_id", "left");
                                $this->db->join("setting", "setting.setting_id=product.sale_currency_id", "left");

                                $this->db->where("product_price_plan.product_price_plan_id", $insert_id);

                                $this->db->where("product_price_plan.active", 1);
                                $arch_where = "( product_price_plan.archived != 1 or product_price_plan.archived is NULL )";
                                $this->db->where($arch_where);

                                $query = $this->db->get("product_price_plan");

                                $query_result       = false;

## Get Additional Data Debugging
                                $get_additional_data = $this->db->last_query();
                                $debug_data = [
                                "product_id"    => $where['product_id'],
                                "price_plan_id" => $added_plan['product_price_plan_id'] ,
                                "string_name"   => "data to save plan to Easel",
                                "query_string"  => json_encode($get_additional_data),
                                ];
                                $this->db->insert("tmp_product_debugging", $debug_data);
## Get Additional Data Debugging - END


                                if ($query->num_rows() > 0) {
                                    $query_result = $query->row();

## Additional Data Query Result
                                    $debug_data = [
                                    "product_id"    => $where['product_id'],
                                    "price_plan_id" => $added_plan['product_price_plan_id'] ,
                                    "string_name"   => "Result of the query data to save plan to Easel",
                                    "query_string"  => json_encode($query_result),
                                    ];
                                    $this->db->insert("tmp_product_debugging", $debug_data);
## Additional Data Query Result - END


                                    ## The structure for the Price band name: hotel_provider_plan (price) -> pullmanarcadianaithonbeach_uip_Premium (24500)
                                    $price_band_name    = "";
                                    $separator          = "_";

                                    $price_band_name .= ( !empty($query_result->site_reference_code) ) ? html_escape($query_result->site_reference_code) : 'site_ref' ;
                                    $price_band_name .= $separator;
                                    $price_band_name .= ( !empty($query_result->provider_name) ) ? html_escape($query_result->provider_name) : 'provider_ref' ;
                                    $price_band_name .= $separator;
                                    $price_band_name .= ( !empty($query_result->price_plan_name) ) ? html_escape($query_result->price_plan_name) : 'price_plan' ;
                                    $price_band_name .= "(" . ( ( !empty($query_result->plan_price) ) ? intval(number_format($query_result->plan_price * 100, 0, null, '')) : '0' ) . ")";
                                } else {
                                    ## query to get reference elements failed, I'm still going to submit the price band to EASEL. What name will be the best?
                                    $price_band_name .= $insert_data['product_id'] . $separator . $insert_data['provider_id'] . $separator . $insert_data['price_plan_id'] . '(' . ( intval(number_format($insert_data['plan_price'] * 100, 0, null, '')) ) . ')';
                                }

                                $price_band_data        = [
                                    'title'         => ( !empty($price_band_name) ) ? ( html_escape($price_band_name) ) : '',
                                    'value'         => ( !empty($insert_data['plan_price']) ) ? ( $insert_data['plan_price'] ) : '0',
                                    'currency'      => ( !empty($query_result->currency) ) ? ( $query_result->currency ) : 'GBP',
                                ];
                                $easel_price_band   = $this->easel_service->create_price_band($account_id, $price_band_data);

## Price Band Data
                                $debug_data = [
                                "product_id"    => $where['product_id'],
                                "price_plan_id" => $added_plan['product_price_plan_id'] ,
                                "string_name"   => "price_band_data before save",
                                "query_string"  => json_encode($price_band_data),
                                ];
                                $this->db->insert("tmp_product_debugging", $debug_data);
## Price Band Data - END

## Create Price Band Response from Easel
                                $debug_data = [
                                "product_id"    => $where['product_id'],
                                "price_plan_id" => $added_plan['product_price_plan_id'] ,
                                "string_name"   => "Easel response to create price band",
                                "query_string"  => json_encode($easel_price_band),
                                ];
                                $this->db->insert("tmp_product_debugging", $debug_data);
## Create Price Band Response from Easel - END


                                if (!empty($easel_price_band->data->id)) {
                                    ## update plan on cacti
                                    $price_band_upd_data = [
                                        "easel_price_band_ref"  => $easel_price_band->data->id,
                                        "modified_by"           => $this->ion_auth->_current_user->id,
                                    ];

                                    $where_upd = [
                                        "account_id"            => $account_id,
                                        "product_price_plan_id" => $insert_id
                                    ];
                                    $this->db->update("product_price_plan", $price_band_upd_data, $where_upd);

## Update Price Plan (CaCTi) with Price Band ID
                                    $last_request = $this->db->last_query();
                                    $debug_data = [
                                    "product_id"    => $where['product_id'],
                                    "price_plan_id" => $added_plan['product_price_plan_id'] ,
                                    "string_name"   => "Update product price plan",
                                    "query_string"  => json_encode($last_request),
                                    ];
                                    $this->db->insert("tmp_product_debugging", $debug_data);
                                } else {
                                    ## Easel did not create - nothing to update
                                }

                                ## create an availability window

                                ## I do not know which exactly productId (movie) it is - availability window require a movie ID
                                ## Preparing a movie list to create an availability window against each one:

                                ## consideration:
                                // Movie territory = Hotel territory
                                // Movie has a clearance date older than product date  - what if the clearance date is in the future?
                                // Movie provider = plan price provider
                                // A movie has an Easel reference

                                $this->db->select("product_price_plan.provider_id", false);
                                $this->db->select("product_price_plan.product_id", false);
                                $this->db->select("product.site_id, product.airtime_market_ref", false);
                                $this->db->select("product.start_date", false);                                                               ## this gives me site_id
                                $this->db->select("site.content_territory_id, site.site_reference_code", false);      ## this gives me territory_id
                                $this->db->select("content_territory.country `site_territory_name`", false);                                  ## this gives me territory name
                                $this->db->select("content_clearance.content_id, content_clearance.clearance_start_date", false);             ## this gives me a list of movies
                                $this->db->select("content.content_provider_reference_code, content.content_provider_id", false);             ## this gives me additional info about the movies
                                $this->db->select("content_film.title, content_film.asset_code, content_film.external_content_ref", false);   ## as above
                                $this->db->select("price_plan.price_plan_name, price_plan.start_period, price_plan.end_period", false);       ## as above
                                $this->db->select("DATE_ADD( content_clearance.clearance_start_date, INTERVAL +price_plan.start_period MONTH ) as `aw_start_date`, DATE_ADD( DATE_ADD( content_clearance.clearance_start_date, INTERVAL +price_plan.end_period MONTH ), INTERVAL -1 DAY) as `aw_end_date`", false);

                                $this->db->join("product", "product.product_id = product_price_plan.product_id", "left");
                                $this->db->join("site", "site.site_id = product.site_id", "left");
                                // Movie territory = Hotel territory
                                $this->db->join("content_territory", "content_territory.territory_id = site.content_territory_id", "left");
                                $this->db->join("content_clearance", "content_clearance.territory_id = site.content_territory_id", "left");
                                $this->db->join("content", "content_clearance.content_id = content.content_id", "left");
                                $this->db->join("content_film", "content_film.content_id = content.content_id", "left");
                                $this->db->join("price_plan", "price_plan.plan_id = product_price_plan.price_plan_id", "left");

                                $this->db->where("product_price_plan_id", $insert_id);
                                $this->db->where("content_film.external_content_ref !=", "");

                                // Movie provider = plan price provider
                                $where1 = "content.content_provider_id = product_price_plan.provider_id";
                                $this->db->where($where1);

                                // Clearance date of the movie has to be within the range of the plan
                                // Temporarily switched off
                                // $where2 = 'content_clearance.clearance_start_date > DATE_ADD( CURDATE(), INTERVAL -price_plan.end_period MONTH ) AND content_clearance.clearance_start_date < DATE_ADD( CURDATE(), INTERVAL -price_plan.start_period MONTH )';
                                // $this->db->where( $where2 );


                                // A movie has an Easel reference
                                $this->db->where("content_film.external_content_ref IS NOT NULL", null);

                                $query = $this->db->get("product_price_plan");

                                $aw_preparations = $this->db->last_query();
                                $debug_data = [
                                "product_id"    => $where['product_id'],
                                "price_plan_id" => $added_plan['product_price_plan_id'] ,
                                "string_name"   => "aw_preparations query",
                                "query_string"  => json_encode($aw_preparations),
                                ];
                                $this->db->insert("tmp_product_debugging", $debug_data);

                                if ($query->num_rows() > 0) {
                                    $movies_result = $query->result();

                                    $debug_data = [
                                    "product_id"    => $where['product_id'],
                                    "price_plan_id" => $added_plan['product_price_plan_id'] ,
                                    "string_name"   => "movies_result query",
                                    "query_string"  => json_encode($movies_result),
                                    ];
                                    $this->db->insert("tmp_product_debugging", $debug_data);

                                    // List of movies - I need to create availability windows for all of them:
                                /*  [0] => stdClass Object(
                                            [provider_id] => 2,                                 [product_id] => 49,                     [site_id] => 12,        [start_date] => 2020-01-01
                                            [content_territory_id] => 52,                       [site_territory_name] => New Zealand    [content_id] => 9       [clearance_start_date] => 2019-12-19
                                            [content_provider_reference_code] => 04038658       [content_provider_id] => 2              [title] => Abominable   [asset_code] => abominable
                                            [external_content_ref] => EASEL1                    [price_plan_name] => UIP Current        [start_period] => 5     [end_period] => 12
                                        )
                                    [1] => stdClass Object(
                                            [provider_id] => 2                                  [product_id] => 49                      [site_id] => 12         [start_date] => 2020-01-01
                                            [content_territory_id] => 52                        [site_territory_name] => New Zealand    [content_id] => 19      [clearance_start_date] => 2020-04-16
                                            [content_provider_reference_code] => 04038759       [content_provider_id] => 2              [title] => Dolittle     [asset_code] => dolittle
                                            [external_content_ref] => 4a9291a9-dd79-3056-3667-4c1a65303bdc          [price_plan_name] => UIP Current            [start_period] => 5
                                            [end_period] => 12
                                        ) */

                                    foreach ($movies_result as $movie) {
                                        $availability_window_set = [];
                                        $availability_window_set = [
                                            ## 'id'         => NULL         ## by default GUID will be generated in Easel model
                                            'productId'         => ( !empty($movie->external_content_ref) ) ? $movie->external_content_ref : false,
                                            'visibleFrom'       => ( !empty($movie->aw_start_date) ) ? convert_date_to_iso8601($movie->aw_start_date) : false,
                                            'visibleTo'         => ( !empty($movie->aw_end_date) ) ? convert_date_to_iso8601($movie->aw_end_date) : false,
                                            'priceBandId'       => ( !empty($easel_price_band->data->id) ) ? $easel_price_band->data->id : false ,
                                            'marketId'          => ( !empty($movie->airtime_market_ref) ) ? $movie->airtime_market_ref : false,
                                            'billing'           => [
                                                "category"          => ( strpos(strtolower($movie->price_plan_name), "premium") !== false ) ? "Premium" : ( ( strpos(strtolower($movie->price_plan_name), "current") !== false ) ? "Current" : "Library" ), ## Library, Current, Premium
                                                "revenueShare"      => 0,
                                                "wholesalePrice"    => 0
                                            ],
                                        ];

                                        $debug_data = [
                                            "product_id"    => $where['product_id'],
                                            "price_plan_id" => $added_plan['product_price_plan_id'] ,
                                            "string_name"   => "availability_window_set data",
                                            "query_string"  => json_encode($availability_window_set),
                                        ];
                                        $this->db->insert("tmp_product_debugging", $debug_data);

                                        $easel_message          = '';
                                        $easel_availability_window      = $this->easel_service->create_availability_window($account_id, $availability_window_set);

                                        $debug_data = [
                                            "product_id"    => $where['product_id'],
                                            "price_plan_id" => $added_plan['product_price_plan_id'] ,
                                            "string_name"   => "easel_availability_window easel response",
                                            "query_string"  => json_encode($easel_availability_window),
                                        ];
                                        $this->db->insert("tmp_product_debugging", $debug_data);


                                        ## save availability window data in the CaCTi
                                        if (!empty($easel_availability_window->data->id)) {
                                            $aw_insert_data = [
                                                "account_id"                    => $account_id,
                                                "content_id"                    => ( !empty($movie->content_id) ) ? $movie->content_id : '' ,
                                                "territory_id"                  => ( !empty($movie->content_territory_id) ) ? $movie->content_territory_id : '' ,
                                                "easel_id"                      => ( !empty($easel_availability_window->data->id) ) ? $easel_availability_window->data->id : '' ,
                                                "easel_productId"               => ( !empty($easel_availability_window->data->productId) ) ? $easel_availability_window->data->productId : '' ,
                                                "easel_visibleFrom"             => ( !empty($easel_availability_window->data->visibleFrom) ) ? $easel_availability_window->data->visibleFrom : '' ,
                                                "easel_visibleTo"               => ( !empty($easel_availability_window->data->visibleTo) ) ? $easel_availability_window->data->visibleTo : '' ,
                                                "easel_priceBandId"             => ( !empty($easel_availability_window->data->priceBandId) ) ? $easel_availability_window->data->priceBandId : '' ,
                                                "easel_marketId"                => ( !empty($easel_availability_window->data->marketId) ) ? $easel_availability_window->data->marketId : '' ,
                                                "easel_billing_category"        => ( !empty($easel_availability_window->data->billing->category) ) ? $easel_availability_window->data->billing->category : '' ,
                                                "easel_billing_revenueShare"    => ( !empty($easel_availability_window->data->billing->revenueShare) ) ? $easel_availability_window->data->billing->revenueShare : '' ,
                                                "easel_billing_wholesalePrice"  => ( !empty($easel_availability_window->data->billing->wholesalePrice) ) ? $easel_availability_window->data->billing->wholesalePrice : '' ,
                                                "site_id"                       => ( !empty($query_result->site_id) ) ? (int) $query_result->site_id : '' ,
                                                "product_id"                    => ( !empty($where['product_id']) ) ? $where['product_id'] : '' ,
                                                "product_price_plan_id"         => ( !empty($added_plan['product_price_plan_id']) ) ? $added_plan['product_price_plan_id'] : '' ,
                                                "created_by"                    => $this->ion_auth->_current_user->id,
                                            ];

                                            $this->db->insert("availability_window", $aw_insert_data);

                                            $aw_last_request = $this->db->last_query();
                                            $debug_data = [
                                                "product_id"    => $where['product_id'],
                                                "price_plan_id" => $added_plan['product_price_plan_id'] ,
                                                "string_name"   => "AW to CaCTi DB",
                                                "query_string"  => json_encode($aw_last_request),
                                            ];
                                            $this->db->insert("tmp_product_debugging", $debug_data);
                                        }
                                    }
                                }
                            }
                        } else {
                            $this->session->set_flashdata('message', 'Error in adding Product Price Plans');
                        }
                    } else {
                        $this->session->set_flashdata('message', 'Error processing the data');
                    }
                } else {
                    $this->session->set_flashdata('message', 'No Price Plan data supplied.');
                }
            } else {
                $this->session->set_flashdata('message', 'No Product Id supplied.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id supplied.');
        }

        return $result;
    }


    public function get_product_price_plan($account_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where)) {
                    if (!empty($where['product_price_plan_id'])) {
                        $product_price_plan_id = $where['product_price_plan_id'];
                        $this->db->where("product_price_plan_id", $product_price_plan_id);
                        unset($where['product_price_plan_id']);
                    }

                    if (!empty($where['product_id'])) {
                        $product_id = $where['product_id'];
                        $this->db->where("product_id", $product_id);
                        unset($where['product_id']);
                    }

                    if (!empty($where['provider_id'])) {
                        $provider_id = $where['provider_id'];
                        $this->db->where("provider_id", $provider_id);
                        unset($where['provider_id']);
                    }

                    if (!empty($where['price_plan_id'])) {
                        $price_plan_id = $where['price_plan_id'];
                        $this->db->where("price_plan_id", $price_plan_id);
                        unset($where['price_plan_id']);
                    }

                    if (!empty($where)) {
                        $this->db->where($where);
                    }
                }

                $this->db->select("ppp.*");
                $this->db->select("cp.provider_name");
                $this->db->select("pp.price_plan_name, pp.price_plan_type, pp.is_provider_a_channel, pp.channel_id, pp.start_period, pp.end_period, ");
                $this->db->select("ch.channel_name");

                $this->db->join("content_provider `cp`", "cp.provider_id = ppp.provider_id", "left");
                $this->db->join("price_plan `pp`", "pp.plan_id = ppp.price_plan_id", "left");
                $this->db->join("channel `ch`", "ch.channel_id = pp.channel_id", "left");

                $this->db->where("ppp.active", 1);
                $arch_where = "( ppp.archived != 1 or ppp.archived is NULL )";
                $this->db->where($arch_where);

                $this->db->where("ppp.account_id", $account_id);

                $query = $this->db->get("product_price_plan `ppp`");

                if ($query->num_rows() > 0) {
                    $result = $query->result();
                } else {
                    $this->session->set_flashdata('message', 'No Account Id supplied.');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id supplied.');
        }
        return $result;
    }


    public function update_product_price_plan($account_id = false, $product_id = false, $price_plans = false)
    {
        $result = false;
        if (!empty($account_id)) {
            if (!empty($product_id)) {
                if (!empty($price_plans)) {
                    $batch_update   = false;

                    if (!empty($price_plans)) {
                        $successfuly_updated            = [];
                        $successfuly_easel_updated      = [];

                        foreach ($price_plans as $plan) {
                            $update_set = [];

                            if (!empty($plan['product_price_plan_id']) && !empty($plan['content_provider_id']) &&  !empty($plan['price_plan_id'])) {
                                $plan_b4_update = false;
                                $plan_b4_update = $this->db->get_where("product_price_plan", ["account_id" => $account_id, "product_price_plan_id" => $plan['product_price_plan_id']])->row();

                                $update_set = [
                                    "product_price_plan_id"     => $plan['product_price_plan_id'],
                                    "account_id"                => $account_id,
                                    "product_id"                => $product_id,
                                    "provider_id"               => $plan['content_provider_id'],
                                    "price_plan_id"             => $plan['price_plan_id'],
                                    "plan_price"                => ( !empty($plan['plan_price']) ) ? number_format($plan['plan_price'], 2, ".", " ") : null ,
                                    "modified_by"               => $this->ion_auth->_current_user->id,
                                ];
                            }

                            if (!empty($update_set)) {
                                $query = $this->db->update("product_price_plan", $update_set, ["product_price_plan_id" => $plan['product_price_plan_id'] ]);
                                if ($this->db->trans_status() !== false) {
                                    $successfuly_updated[] =  $plan['product_price_plan_id'];

                                    ## The plan has been updated in CaCTi - next step - update Easel if this is an airtime plan
                                    if (!empty($plan_b4_update->easel_price_band_ref)) {
                                        $postdata = [
                                            "id"        => $plan_b4_update->easel_price_band_ref,
                                            "value"     => $update_set['plan_price'],
                                        ];

                                        $update_easel_price_band        = $this->easel_service->update_price_band($account_id, $price_band_id = $plan_b4_update->easel_price_band_ref, $postdata);

                                        if ($update_easel_price_band->success ==  true) {
                                            $successfuly_easel_updated[] = $plan['product_price_plan_id'];
                                        }
                                    }
                                }
                            }
                        }

                        ## Price plans successfully updated in CaCTi: 55, 57
                        ## Price plans successfully updated in Easel: 55, 57

                        $success_cacti = ( !empty($successfuly_updated) ) ? implode(", ", $successfuly_updated) : 'none' ;
                        $success_easel = ( !empty($successfuly_easel_updated) ) ? implode(", ", $successfuly_easel_updated) : 'none' ;

                        $message = "Price plans successfully updated in CaCTi: " . $success_cacti . '<br />';
                        $message .= "Price plans successfully updated in Easel: " . $success_easel .

                        $this->session->set_flashdata('message', $message);
                        $this->session->set_flashdata('plan_message', $message);

                        $result = $this->db->get_where("product_price_plan", ["account_id" => $account_id, "product_id" => $product_id])->result();
                    } else {
                        $this->session->set_flashdata('message', 'Error decoding the data');
                    }
                } else {
                    $this->session->set_flashdata('message', 'No Price Plan data supplied.');
                }
            } else {
                $this->session->set_flashdata('message', 'No Product Id supplied.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id supplied.');
        }
        return $result;
    }


    public function get_site_and_product($account_id = false, $site_id = false, $product_id = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($site_id) && !empty($product_id)) {
            $this->db->select("product.product_id, site.site_id");
            $this->db->where("`product`.`product_id`", $product_id);
            $this->db->where("`site`.`site_id`", $site_id);
            $query = $this->db->get("product, site");

            $result = ( $query->num_rows() > 0 );
            if ($result) {
                $this->session->set_flashdata('message', 'Site and product has been found.');
            } else {
                $this->session->set_flashdata('message', 'Cannot find the site and the product.');
            }
        } else {
            $this->session->set_flashdata('message', 'Required data is missing.');
        }

        return $result;
    }


    /*
    *   Function to add the price plan(s) to the existing product
    *   Assuming there will be an array of price plans
    */
    public function add_price_plan_to_product($account_id = false, $site_id = false, $product_id = false, $price_plans = false)
    {
        $result = false;
        if (!empty($account_id)) {
            if (!empty($site_id)) {
                if (!empty($product_id)) {
                    if (!empty($price_plans)) {
                        $site_data          = $this->site_service->get_sites($account_id, $site_id);

                        if (!$site_data) {
                            $this->session->set_flashdata('message', 'Site data not available for this Site ID-' . $site_id);
                            return $result;
                        }

                        $product_data       = $this->get_product($account_id, $product_id);
                        if (!$product_data) {
                            $this->session->set_flashdata('message', 'product data not available for this Product ID-' . $product_id);
                            return $result;
                        }

                        $saved_plans                    = [];

                        $i                              = 0;
                        $where["product_id"]            = $product_id;
                        $where["site_reference_code"]   = ( !empty($site_data->site_reference_code) ) ? $site_data->site_reference_code : '' ;
                        ;
                        $where["airtime_market_ref"]    = ( !empty($product_data->airtime_market_ref) ) ? $product_data->airtime_market_ref : '' ;

                        if (in_array(strtolower($product_data->product_type_name), ["airtime"])) {
                            $where["is_airtime"]    = true;
                        }

                        $price_plans = convert_to_array($price_plans);

                        foreach ($price_plans as $price_plan) {
                            if (!empty((int) $price_plan->provider_id) && !empty((int) $price_plan->plan_id) && !empty($price_plan->plan_price)) {
                                $saved_plan             = false;
                                $saved_plan_message     = '';
                                $saved_plan             = $this->save_product_price_plan($account_id, $where, (array) $price_plan);

                                $saved_plans[$i]        = $saved_plan;
                                $saved_plan_message     .= ( !empty($this->session->flashdata('saved_plans')) ) ? $this->session->flashdata('saved_plans') : '' ;

                                $i++;
                            }
                        }

                        $result         = $this->get_product($account_id, $product_id);
                        $this->session->set_flashdata('message', 'Price plan(s) added.');
                    } else {
                        $this->session->set_flashdata('message', 'No Price Plan data supplied.');
                    }
                } else {
                    $this->session->set_flashdata('message', 'No Product Id supplied.');
                }
            } else {
                $this->session->set_flashdata('message', 'No Site Id supplied.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account Id supplied.');
        }

        return $result;
    }



    /**
    *   This is to add the market to invited collection
    */
    public function activate_market_on_airtime($account_id = false, $product_id = false)
    {
        $result = (object)[
            "status"    => false,
            "message"   => "",
            "data"      => false
        ];

        if (!empty($product_id)) {
            $product_data = false;
            $product_data = $this->get_product($account_id, $product_id);

            if (( $product_data != false ) && ( !empty($product_data->airtime_market_ref) ) && ( !empty($product_data->airtime_segment_ref) )) {
                $market_from_product_id             = false;
                $market_from_product_id             = $product_data->airtime_market_ref;

                ## check if this ID is within the 'invited' market
                $invited_market_data                = false;
                $invited_market_data                = $this->easel_service->fetch_market($account_id, EASEL_INVITED_MARKET_ID);

                $market_already_added_to_invited    = false;
                $no_expression_property             = false;
                $ready_to_update_db                 = false;
                $new_market_data                    = false;

                if (( $invited_market_data != false ) && ( !empty($invited_market_data->id) )) {
                    if (!empty($invited_market_data->expression)) {
                        foreach ($invited_market_data->expression as $expr_row) {
                            if (strtolower($expr_row) == "market_" . $market_from_product_id) {
                                $market_already_added_to_invited = true;
                            }
                        }
                    } else {
                        // no 'expression' section - add the expression
                        $no_expression_property     = true;
                        log_message("error", json_encode(["message" => "No expression property in the 'invited' market for the Activate market functionality"]));
                    }

                    if (!$market_already_added_to_invited) {
                        $new_market_data            = [];
                        $new_market_data            = [
                            "id"            => ( !empty($invited_market_data->id) ) ? $invited_market_data->id : EASEL_INVITED_MARKET_ID ,
                            "name"          => ( !empty($invited_market_data->name) ) ? $invited_market_data->name : '' ,
                            "description"   => ( !empty($invited_market_data->description) ) ? $invited_market_data->description : '' ,
                            "ordering"      => ( isset($invited_market_data->ordering) ) ? (int) $invited_market_data->ordering : 99 ,
                            "expression"    => ( !empty($invited_market_data->expression) ) ? $invited_market_data->expression : []
                        ];
                        $invited_market_data        = false;

                        if ($no_expression_property) {
                            $new_market_data['expression'] = ["Market_" . $market_from_product_id];
                        } else {
                            array_push($new_market_data['expression'], "or", "Market_" . $market_from_product_id);
                        }

                        $updated_market = false;
                        $updated_market = $this->easel_service->update_market($account_id, EASEL_INVITED_MARKET_ID, json_encode($new_market_data));
                        log_message("error", json_encode(["message" => "Updating market when adding to invited - data", "market_data" => $new_market_data , "product_id" => $product_id]));
                        log_message("error", json_encode(["message" => "Updating market when adding to invited - Easel response", "market_data" => $updated_market ]));

                        if ($updated_market != false && $updated_market->success != false) {
                            $ready_to_update_db = true;
                            $result->message = "'Invited' Market has been updated on Easel successfully" ;
                        } else {
                            $result->message = ( !empty($updated_market->message) ) ? $updated_market->message : "There was a problem on Easel when updating 'Invited' market" ;
                        }
                    } else {
                        $ready_to_update_db = true;
                    }

                    if ($ready_to_update_db) {
                        $upd_market_data = [];
                        $upd_market_data = [
                            "is_market_active_on_airtime"   => 1,
                            "date_activated_on_airtime"     => date('Y-m-d H:i:s'),
                        ];

                        $upd_market_where = [];
                        $upd_market_where = [
                            "account_id"    => $account_id,
                            "product_id"    => $product_id
                        ];

                        $this->db->update("product", $upd_market_data, $upd_market_where);

                        if ($this->db->affected_rows() > 0 || $this->db->trans_status() != false) {
                            $result->message = ( !empty($result->message) ) ? $result->message . ' Market updated on CaCTI successfully.' : ' Market updated on CaCTI successfully.';

                            $result->status = 1;
                            $result->data   = ( !empty($updated_market) ) ? $updated_market : ( !empty($invited_market_data) ? $invited_market_data : false );
                        } else {
                            $last_query = $this->db->last_query();
                            log_message("error", json_encode(["message" => "Updating CaCTI when adding market to 'invited' - failed", "last_query" => $$last_query ]));
                            unset($last_query);

                            $result->message = ( !empty($result->message) ) ? $result->message . ' Market update on CaCTI failed.' : ' Market update on CaCTI failed.';
                        }
                    } else {
                        $result->message = ( !empty($result->message) ) ? $result->message . '  Market not ready for the update' : " Market not ready for the update";
                    }
                } else {
                    $result->message = "'Invited' market doesn't exists on Easel";
                }
            } else {
                $result->message = "Incomplete Product Data (Segment ID, Market ID)";
            }
        } else {
            $result->message = "Non-Existing Product ID";
        }

        return $result;
    }



    /**
    *   This is to remove the market from the 'invited' collection
    */
    public function deactivate_market_on_airtime($account_id = false, $product_id = false)
    {
        $result = (object)[
            "status"    => false,
            "message"   => "",
            "data"      => false
        ];

        if (!empty($product_id)) {
            $product_data = false;
            $product_data = $this->get_product($account_id, $product_id);

            if (( $product_data != false ) && ( !empty($product_data->airtime_market_ref) ) && ( !empty($product_data->airtime_segment_ref) )) {
                $market_from_product_id             = false;
                $market_from_product_id             = $product_data->airtime_market_ref;

                ## check if this ID is within the 'invited' market
                $invited_market_data                = false;
                $invited_market_data                = $this->easel_service->fetch_market($account_id, EASEL_INVITED_MARKET_ID);
                $ready_to_update_db                 = true;

                if (( $invited_market_data != false ) && ( !empty($invited_market_data->id) )) {
                    if (isset($invited_market_data->expression) && !empty($invited_market_data->expression)) {
                        $expression_length = count($invited_market_data->expression);

                        if ($expression_length > 1) {
                            $key_val = array_search("Market_" . $product_data->airtime_market_ref, $invited_market_data->expression);

                            if (( $key_val !== false )) {
                                if ($key_val === 0) {
                                    array_shift($invited_market_data->expression);
                                    array_shift($invited_market_data->expression);

                                    // unset( $invited_market_data->expression[$key_val+1] );
                                    // unset( $invited_market_data->expression[$key_val] );
                                } else {
                                    unset($invited_market_data->expression[$key_val - 1]);
                                    unset($invited_market_data->expression[$key_val]);
                                    $invited_market_data->expression = array_values($invited_market_data->expression);
                                }

                                log_message("error", json_encode(["message" => "Updating market when removing from invited - data1", "market_data" => $invited_market_data, "product_id" => $product_id ]));

                                $updated_invited_market = false;
                                $updated_invited_market = $this->easel_service->update_market($account_id, EASEL_INVITED_MARKET_ID, json_encode($invited_market_data));

                                log_message("error", json_encode(["message" => "Updating market when removing from invited - Easel response", "market_data" => $updated_invited_market, "product_id" => $product_id ]));

                                if ($updated_invited_market != false && $updated_invited_market->success != false) {
                                    $result->message    = "'Invited' Market has been updated on Easel successfully" ;
                                    $result->data       = $updated_invited_market;
                                } else {
                                    $result->message = ( !empty($updated_invited_market->message) ) ? $updated_invited_market->message : "There was a problem on Easel when updating 'Invited' market" ;
                                    $ready_to_update_db =  false;
                                }
                            } else {
                                $result->message = ( !empty($result->message) ) ? $result->message . " Market not found on Easel." : " Market not found on Easel." ;
                            }
                        } else {
                            $key_val = array_search("Market_" . $product_data->airtime_market_ref, $invited_market_data->expression);
                            if (( $key_val !== false )) {
                                unset($invited_market_data->expression);

                                log_message("error", json_encode(["message" => "Updating market when removing from invited - data2", "market_data" => $invited_market_data, "product_id" => $product_id ]));

                                $updated_invited_market = false;
                                $updated_invited_market = $this->easel_service->update_market($account_id, EASEL_INVITED_MARKET_ID, json_encode($invited_market_data));

                                log_message("error", json_encode(["message" => "Updating market when removing from invited - Easel response2", "market_data" => $updated_invited_market, "product_id" => $product_id ]));

                                if ($updated_invited_market != false && $updated_invited_market->success != false) {
                                    $result->message    = "'Invited' Market has been updated on Easel successfully" ;
                                } else {
                                    $result->message    = ( !empty($updated_invited_market->message) ) ? $updated_invited_market->message : "There was a problem on Easel when updating 'Invited' market" ;
                                    $ready_to_update_db =  false;
                                }
                            } else {
                                $result->message = ( !empty($result->message) ) ? $result->message . " Market not found on Easel." : " Market not found on Easel." ;
                            }
                        }
                    } else {
                        $result->message = "The 'Invited' market has no markets attached. ";
                    }

                    if ($ready_to_update_db != false) {
                        $upd_market_data = [];
                        $upd_market_data = [
                            "is_market_active_on_airtime"   => 0,
                            "date_deactivated_on_airtime"   => date('Y-m-d H:i:s'),
                        ];

                        $upd_market_where = [];
                        $upd_market_where = [
                            "account_id"    => $account_id,
                            "product_id"    => $product_id
                        ];

                        $this->db->update("product", $upd_market_data, $upd_market_where);

                        if ($this->db->affected_rows() > 0 || $this->db->trans_status() != false) {
                            $result->message = ( !empty($result->message) ) ? $result->message . " CaCTI's status of the market has been changed successfully." : "CaCTI status of the market has been changed successfully." ;
                            $result->status     = 1;
                            $result->data       = $updated_invited_market;
                        } else {
                            $result->message    = ( !empty($result->message) ) ? $result->message . " Market update in CaCTI failed." : "Market update in CaCTI failed." ;
                        }
                    } else {
                        $result->message = ( !empty($result->message) ) ? $result->message . " Market update in CaCTI failed." : "Market update in CaCTI failed." ;
                    }
                } else {
                    $result->message = "'Invited' market doesn't exists on Easel";
                }
            } else {
                $result->message = "Incomplete Product Data (Segment ID, Market ID)";
            }
        } else {
            $result->message = "Non-Existing Product ID";
        }

        return $result;
    }


    /**
    *   This is to delete the price plan for the Airtime type of the product.
    *   The call will subsequently go to Easel and also delete Easel's Price band and all the Availability Windows related to.
    */
    public function delete_product_price_plan($account_id = false, $product_price_plan_id = false)
    {
        $result = (object)[
            "status"    => false,
            "message"   => "",
            "data"      => false
        ];

        if (!empty($product_price_plan_id)) {
            ## this has been checked in the controller, but in case of direct access from a different model we need to check it again
            $product_price_plan_exists = false;
            $product_price_plan_exists = $this->product_service->get_product_price_plan($account_id, ["product_price_plan_id" => $product_price_plan_id]);

            if (!$product_price_plan_exists) {
                $result->message = "Invalid Product Price Plan ID";
                return $result;
            } else {
                $product_price_plan_exists = ( is_array($product_price_plan_exists) ) ? array_values($product_price_plan_exists)[0] : $product_price_plan_exists;

                if (!empty($product_price_plan_exists->easel_price_band_ref)) {
                    if (DELETE_PRICE_BAND_DEBUGGING !== false) {
                        $debug_data = [
                            "product_price_plan_id" => $product_price_plan_id,
                            "request_type"          => "fetching easel price band - input data( easel_price_band_ref )",
                            "request_data"          => json_encode($product_price_plan_exists->easel_price_band_ref),
                        ];
                        $this->db->insert("tmp_delete_price_band_debugging", $debug_data);
                    }

                    ## check if Easel price and exists:
                    $price_band_exists = false;
                    $price_band_exists = $this->easel_service->fetch_price_band($account_id, $product_price_plan_exists->easel_price_band_ref);

                    if (DELETE_PRICE_BAND_DEBUGGING !== false) {
                        $debug_data = [
                            "product_price_plan_id" => $product_price_plan_id,
                            "request_type"          => "fetching easel price band - Easel response data",
                            "request_data"          => json_encode($price_band_exists),
                        ];
                        $this->db->insert("tmp_delete_price_band_debugging", $debug_data);
                    }

                    if (( $price_band_exists != false ) && !empty($price_band_exists->id)) {
                        $deleted_price_band     = false;
                        $deleted_price_band     = $this->easel_service->delete_price_band($account_id, $product_price_plan_exists->easel_price_band_ref, true);

                        if (DELETE_PRICE_BAND_DEBUGGING !== false) {
                            $debug_data = [
                                "product_price_plan_id" => $product_price_plan_id,
                                "request_type"          => "deleting easel price band - feedback from Easel response",
                                "request_data"          => json_encode($deleted_price_band),
                            ];
                            $this->db->insert("tmp_delete_price_band_debugging", $debug_data);
                        }

                        if (( isset($deleted_price_band) ) && ( isset($deleted_price_band->success) && ( $deleted_price_band->success != false ) )) {
                            $result->message .= 'Price Band and Availability Windows successfully deleted on Easel. ';

                            ## now we're archiving all the availability windows in CaCTI
                            $availab_windows_upd_data = [];
                            $availab_windows_upd_data = [
                                "archived"      => 1,
                                "active"        => 0,
                                "archived_date" => date('Y-m-d H:i:s'),
                                "archived_by"   => $this->ion_auth->_current_user->id
                            ];

                            $availab_windows_upd_where = [];
                            $availab_windows_upd_where = [
                                "easel_priceBandId" => $product_price_plan_exists->easel_price_band_ref,
                                "account_id"        => $account_id
                            ];

                            if (DELETE_PRICE_BAND_DEBUGGING !== false) {
                                $debug_data = [
                                    "product_price_plan_id" => $product_price_plan_id,
                                    "request_type"          => "archiving availability windows - update data and update where",
                                    "request_data"          => json_encode(["data" => $availab_windows_upd_data, "where" => $availab_windows_upd_where]),
                                ];
                                $this->db->insert("tmp_delete_price_band_debugging", $debug_data);
                            }

                            $aw_update = $this->db->update("availability_window", $availab_windows_upd_data, $availab_windows_upd_where);

                            $last_query = $this->db->last_query();

                            if (DELETE_PRICE_BAND_DEBUGGING !== false) {
                                $debug_data = [
                                    "product_price_plan_id" => $product_price_plan_id,
                                    "request_type"          => "archiving availability windows - last query ",
                                    "request_data"          => json_encode($last_query),
                                ];
                                $this->db->insert("tmp_delete_price_band_debugging", $debug_data);
                            }

                            if ($this->db->affected_rows() > 0 || $this->db->trans_status() !== false) {
                                $result->message .= 'Availability Windows successfully deleted on CaCTI. ';
                            } else {
                                $result->message .= 'There were issues deleting Availability Windows on CaCTI. ';
                            }

                            ## Despite the result, as the Easel call went successfully, we're going to delete the Price Plan (CaCTI)

                            if (DELETE_PRICE_BAND_DEBUGGING !== false) {
                                $debug_data = [
                                    "product_price_plan_id" => $product_price_plan_id,
                                    "request_type"          => "product price plan before deleting",
                                    "request_data"          => json_encode($product_price_plan_exists),
                                ];
                                $this->db->insert("tmp_delete_price_band_debugging", $debug_data);
                            }

                            $this->db->where("product_price_plan_id", $product_price_plan_id);
                            $this->db->delete("product_price_plan");
                            if ($this->db->affected_rows() > 0 || $this->db->trans_status() !== false) {
                                $result->message .= 'Product Price Plan successfully deleted on CaCTI. ';
                                $result->status     = true;
                            } else {
                                $result->message .= 'There were issues deleting Product Price Plan on CaCTI. ';
                            }
                        } else {
                            $result->message = ( !empty($deleted_price_band->message) ) ? $deleted_price_band->message : 'Deleting Price Band on Easel failed' ;
                        }
                    } else {
                        $result->message = "Price Band (Easel) not found";
                    }
                } else {
                    $result->message = "Easel reference for Price Plan (Price Band) is missing";
                }
            }
        } else {
            $result->message = "Product Price Plan ID not provided";
        }

        if (DELETE_PRICE_BAND_DEBUGGING !== false) {
            $debug_data = [
                "product_price_plan_id" => $product_price_plan_id,
                "request_type"          => "final result value",
                "request_data"          => json_encode($result),
            ];
            $this->db->insert("tmp_delete_price_band_debugging", $debug_data);
        }

        return $result;
    }
}
