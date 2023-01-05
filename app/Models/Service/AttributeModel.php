<?php

namespace App\Models\Service;

use App\Adapter\Model;

class AttributeModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $section 	   = explode("/", $_SERVER["SCRIPT_NAME"]);
        $this->app_root= $_SERVER["DOCUMENT_ROOT"]."/".$section[1]."/";
        $this->app_root= str_replace('/index.php', '', $this->app_root);
        $this->response_types_w_options = ["radio","checkbox","select"];
    }


    public function get_attributes($account_id = false, $attr_where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($attr_where)) {
                $attribute_id = false;

                $attr_where = (!is_array($attr_where)) ? convert_to_array($attr_where) : $attr_where ;

                $this->db->select("custom_attribute.*", false);
                $this->db->select("custom_attribute_location.*", false);

                $this->db->join("custom_attribute_location", "custom_attribute_location.attribute_id = custom_attribute.attribute_id", "left");

                if (isset($attr_where['search_term'])) {
                    if (!empty($attr_where['search_term'])) {
                        $where = " ( ( `custom_attribute`.`attribute_description` LIKE '%".trim(urldecode($attr_where['search_term']))."%' )";
                        $where .= " || ( `custom_attribute`.`attribute_alt_text` LIKE '%".trim(urldecode($attr_where['search_term']))."%' ) )";
                        $this->db->where($where);
                    }
                    unset($attr_where['search_term']);
                }

                if (isset($attr_where['attribute_name'])) {
                    if (!empty($attr_where['attribute_name'])) {
                        $attribute_name = true;
                        $this->db->where("custom_attribute.attribute_name", $attr_where['attribute_name']);
                    }
                    unset($attr_where['attribute_name']);
                }

                if (isset($attr_where['attribute_id'])) {
                    if (!empty($attr_where['attribute_id'])) {
                        $attribute_id = true;
                        $this->db->where("custom_attribute.attribute_id", json_decode($attr_where['attribute_id']));
                    }
                    unset($attr_where['attribute_id']);
                }

                if (isset($attr_where['module_id'])) {
                    if (!empty($attr_where['module_id'])) {
                        $this->db->where("custom_attribute.module_id", $attr_where['module_id']);
                    }
                    unset($attr_where['module_id']);
                }

                if (isset($attr_where['module_item_id'])) {
                    if (!empty($attr_where['module_item_id'])) {
                        $this->db->where("custom_attribute.module_item_id", $attr_where['module_item_id']);
                    }
                    unset($attr_where['module_item_id']);
                }

                if (isset($attr_where['show_on_mobile'])) {
                    if (!empty($attr_where['show_on_mobile'])) {
                        if (format_boolean($attr_where['show_on_mobile']) == 0) {
                            $overview_where = "( ( `custom_attribute`.`show_on_mobile` = 0 ) || ( `custom_attribute`.`show_on_mobile` IS NULL) )";
                            $this->db->where($overview_where);
                        } else {
                            $this->db->where("custom_attribute.show_on_mobile", format_boolean($attr_where['show_on_mobile']));
                        }
                    }
                    unset($attr_where['show_on_mobile']);
                }

                if (isset($attr_where['show_on_report'])) {
                    if (!empty($attr_where['show_on_report'])) {
                        if (format_boolean($attr_where['show_on_report']) == 0) {
                            $overview_where = "( ( `custom_attribute`.`show_on_report` = 0 ) || ( `custom_attribute`.`show_on_report` IS NULL) )";
                            $this->db->where($overview_where);
                        } else {
                            $this->db->where("custom_attribute.show_on_report", format_boolean($attr_where['show_on_report']));
                        }
                    }
                    unset($attr_where['show_on_report']);
                }

                if (isset($attr_where['zone_id'])) {
                    if (!empty($attr_where['zone_id'])) {
                        $zone_id = $attr_where['zone_id'];
                        $this->db->where("custom_attribute_location.zone_id", $zone_id);
                    }
                    unset($attr_where['zone_id']);
                }

                $this->db->where("custom_attribute.account_id", $account_id);
                $this->db->where("custom_attribute.active", 1);

                $arch_where = "( custom_attribute.archived != 1 or custom_attribute.archived is NULL )";
                $this->db->where($arch_where);

                $this->db->where("custom_attribute_location.zone_id !=", null);

                $this->db->order_by("custom_attribute.module_id ASC, custom_attribute.module_item_id ASC, custom_attribute_location.zone_id ASC, custom_attribute_location.section_id ASC, custom_attribute_location.group_id ASC, custom_attribute_location.group_order ASC");

                $query = $this->db->get("custom_attribute", $limit, $offset);

                if (!empty($query->num_rows()) && ($query->num_rows() > 0)) {
                    foreach ($query->result() as $key => $row) {
                        if ($attribute_id) {
                            $result[$key] = $row;

                            $result[$key]->options = null ;

                            if (in_array($row->response_type, $this->response_types_w_options)) { ## select, radio, checkbox
                                $this->db->select("custom_dropdowns.*", false);
                                $this->db->where("custom_dropdowns.attribute_id", $row->attribute_id);
                                $this->db->where("custom_dropdowns.active", 1);
                                $o_query = $this->db->get("custom_dropdowns");

                                if (!empty($o_query->num_rows()) && $o_query->num_rows() > 0) {
                                    $result[$key]->options = $o_query->result() ;
                                }
                            }
                        } else {
                            $result[$row->zone_id][$row->section_id][$row->group_id][$row->attribute_id] = $row;

                            $result[$row->zone_id][$row->section_id][$row->group_id][$row->attribute_id]->options = null ;

                            if (in_array($row->response_type, $this->response_types_w_options)) { ## select, radio, checkbox
                                $this->db->select("custom_dropdowns.*", false);
                                $this->db->where("custom_dropdowns.attribute_id", $row->attribute_id);
                                $this->db->where("custom_dropdowns.active", 1);
                                $o_query = $this->db->get("custom_dropdowns");

                                if (!empty($o_query->num_rows()) && $o_query->num_rows() > 0) {
                                    $result[$row->zone_id][$row->section_id][$row->group_id][$row->attribute_id]->options = $o_query->result() ;
                                }
                            }
                        }
                    }

                    $this->session->set_flashdata('message', 'Attributes were found.');
                } else {
                    $this->session->set_flashdata('message', 'No Attributes were found.');
                }
            } else {
                $this->session->set_flashdata('message', 'No search criteria provided.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account details provided.');
        }

        return $result;
    }


    /**
    *	Get responses to the attributes
    */
    public function get_responses($account_id = false, $attr_where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($attr_where)) {
                $attr_where = (!is_array($attr_where)) ? convert_to_array($attr_where) : $attr_where ;

                if (isset($attr_where['response_id'])) {
                    if (!empty($attr_where['response_id'])) {
                        $response_id = $attr_where['response_id'];
                        $this->db->where("custom_attribute_responses.response_id", $response_id);
                    }
                    unset($attr_where['response_id']);
                }

                if (isset($attr_where['module_id'])) {
                    if (!empty($attr_where['module_id'])) {
                        $module_id = $attr_where['module_id'];
                        $this->db->where("custom_attribute_responses.module_id", $module_id);
                    }
                    unset($attr_where['module_id']);
                } else {
                    if (empty($response_id)) {
                        $this->session->set_flashdata('message', 'Module ID is required.');
                    }
                }

                if (isset($attr_where['module_item_id'])) {
                    if (!empty($attr_where['module_item_id'])) {
                        $module_item_id = $attr_where['module_item_id'];
                        $this->db->where("custom_attribute_responses.module_item_id", $module_item_id);
                    }
                    unset($attr_where['module_item_id']);
                } else {
                    if (empty($response_id)) {
                        $this->session->set_flashdata('message', 'Module Item ID is required.');
                    }
                }

                if (isset($attr_where['zone_id'])) {
                    if (!empty($attr_where['zone_id'])) {
                        $zone_id = $attr_where['zone_id'];
                        $this->db->where("custom_attribute_responses.zone_id", $zone_id);
                    }
                    unset($attr_where['zone_id']);
                }

                if (isset($attr_where['section_id'])) {
                    if (!empty($attr_where['section_id'])) {
                        $section_id = $attr_where['section_id'];
                        $this->db->where("custom_attribute_responses.section_id", $section_id);
                    }
                    unset($attr_where['section_id']);
                }

                if (isset($attr_where['group_id'])) {
                    if (!empty($attr_where['group_id'])) {
                        $group_id = $attr_where['group_id'];
                        $this->db->where("custom_attribute_responses.group_id", $group_id);
                    }
                    unset($attr_where['group_id']);
                }

                if (isset($attr_where['profile_id'])) {
                    if (!empty($attr_where['profile_id'])) {
                        $profile_id = $attr_where['profile_id'];
                        $this->db->where("custom_attribute_responses.profile_id", $profile_id);
                    }
                    unset($attr_where['profile_id']);
                }

                if (isset($attr_where['attribute_id'])) {
                    if (!empty($attr_where['attribute_id'])) {
                        $attribute_id = $attr_where['attribute_id'];
                        $this->db->where("custom_attribute_responses.attribute_id", $attribute_id);
                    }
                    unset($attr_where['attribute_id']);
                }

                if (isset($attr_where['attribute_input_type_id'])) {
                    if (!empty($attr_where['attribute_input_type_id'])) {
                        $this->db->where("custom_attribute_responses.attribute_input_type_id", $attr_where['attribute_input_type_id']);
                    }
                    unset($attr_where['attribute_input_type_id']);
                }

                $this->db->where("custom_attribute_responses.account_id", $account_id);

                $arch_where = "( custom_attribute_responses.archived != 1 or custom_attribute_responses.archived is NULL )";
                $this->db->where($arch_where);

                ## update_group_order - ordering
                $query = $this->db->get("custom_attribute_responses", $limit, $offset);

                if (!empty($query->num_rows()) && ($query->num_rows() > 0)) {
                    if (!empty($response_id)) {
                        $result[$response_id] = $query->result();
                    } else {
                        foreach ($query->result() as $key => $row) {
                            $result[$row->attribute_id] = $row;
                        }
                    }
                    $this->session->set_flashdata('message', 'Custom response(s) found.');
                } else {
                    $this->session->set_flashdata('message', 'No result with the search criteria provided.');
                }
            } else {
                $this->session->set_flashdata('message', 'No search criteria provided.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account ID provided.');
        }

        return $result;
    }


    /**
    *	The function to update attribute responses
    */
    public function update_attribute_responses($account_id = false, $profile_id = false, $module_id = false, $module_item_id = false, $zone_id = false, $responses = false)
    {
        $result  = false;

        if (!empty($account_id) && !empty($profile_id) && !empty($module_id) && !empty($module_item_id) && !empty($responses)) {
            $update_batch = [];
            $insert_batch = [];

            $responses = json_decode(urldecode($responses));

            foreach ($responses as $attribute_id => $value) {
                $response_exists = $this->get_responses($account_id, ["attribute_id" => $attribute_id, "module_id" => $module_id, "module_item_id" => $module_item_id ]);
                $var = ($response_exists) ? "update_batch" : "insert_batch";

                $$var[$attribute_id]['account_id'] 				= ( int ) $account_id;
                $$var[$attribute_id]['module_id'] 				= ( int ) $module_id;
                $$var[$attribute_id]['module_item_id'] 			= ( int ) $module_item_id;
                $$var[$attribute_id]['profile_id'] 				= ( int ) $profile_id;
                $$var[$attribute_id]['attribute_id'] 			= ( int ) $attribute_id;

                $attribute 					= $this->get_attributes($account_id, ["attribute_id" => $attribute_id ]);
                $attribute_input_type_id 	= $attribute[0]->attribute_input_type_id;
                $response_type 				= $attribute[0]->response_type;

                if ($var == "insert_batch") {
                    $$var[$attribute_id]['attribute_input_type_id'] = (!empty($attribute_input_type_id)) ? ( int ) $attribute_input_type_id : null ;
                    $$var[$attribute_id]['response_type']			= (!empty($response_type)) ? $response_type : null ;
                    $$var[$attribute_id]['zone_id'] 				= (!empty($attribute[0]->zone_id)) ? ( int ) $attribute[0]->zone_id : null ;
                    /* 					$$var[$attribute_id]['section_id'] 				= ( !empty( $attribute[0]->update_section_id ) ) ? ( int ) $attribute[0]->update_section_id : NULL ;
                                        $$var[$attribute_id]['group_id'] 				= ( !empty( $attribute[0]->update_group_id ) ) ? ( int ) $attribute[0]->update_group_id : NULL ; */
                    $$var[$attribute_id]['created_by'] 				= $this->ion_auth->_current_user()->id;
                } else {
                    $$var[$attribute_id]['last_modified_by'] 		= $this->ion_auth->_current_user()->id;
                }

                if (!empty($response_type)) {
                    switch($response_type) {
                        case "checkbox": ## checkbox
                            $checkboxes = [];
                            foreach ($value as $key => $item) {
                                $checkboxes[$key] = format_boolean($item);
                            }

                            $$var[$attribute_id]['response_value'] 				= json_encode($checkboxes);
                            break;

                        case "datetimepicker": ## datetimepicker
                            $$var[$attribute_id]['response_value'] 				= format_datetime_db($value);
                            break;

                        case "datepicker": ## datepicker
                            $$var[$attribute_id]['response_value'] 				= format_date_db($value);
                            break;

                        case "select": ## select
                        case "radio": ## radio
                        case "textarea": ## textarea
                        case "input": ## input
                        default:
                            $$var[$attribute_id]['response_value'] 				= $value;
                            ## $$var[$attribute_id]['actual_response_value'] 	= $value;
                    }
                }
            }

            $message = "";

            if (!empty($update_batch)) {
                $this->db->update_batch('custom_attribute_responses', $update_batch, 'attribute_id');
                if (($this->db->trans_status() === true) && ($this->db->affected_rows() > 0)) {
                    $message .= "The responses Update was successful";
                    $result['update_batch']['batch'] 		= $update_batch;
                    $result['update_batch']['status']		= true;
                } else {
                    $message .= "No changes were made";
                    $result['update_batch']['batch'] 		= false;
                    $result['update_batch']['status']		= false;
                }
            }

            if (!empty($insert_batch)) {
                $this->db->insert_batch('custom_attribute_responses', $insert_batch);
                if (($this->db->trans_status() === true) && ($this->db->affected_rows() > 0)) {
                    $message .= "The responses Insert was successful";
                    $result['insert_batch']['batch'] 		= $insert_batch;
                    $result['insert_batch']['status']		= true;
                } else {
                    $message .= "The responses Insert wasn't successful";
                    $result['insert_batch']['batch'] 		= false;
                    $result['insert_batch']['status']		= false;
                }
            }

            $this->session->set_flashdata('message', $message);
        ## feedback
        } else {
            $this->session->set_flashdata('message', 'Required Details are missing.');
        }

        return $result;
    }


    /**
    * 	Function to create an atribute.
    */
    public function create_atribute($account_id = false, $attr_data = false)
    {
        $result = false;
        if (!empty($account_id)) {
            if (!empty($attr_data)) {
                $options 		= false;
                $attr_data 		= (!is_array($attr_data)) ? convert_to_array($attr_data) : $attr_data ;
                $data 			= [];

                if (!empty($attr_data)) {
                    foreach ($attr_data as $key => $value) {
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
                        } elseif (is_string($value)) {
                            $value = trim($value);
                        } else {
                            $value = ($value);
                        }
                        $data[$key] = $value;
                    }
                } else {
                    $this->session->set_flashdata('message', 'There was an issue with processing the data.');
                    return $result;
                }

                ## required items section:
                if (!isset($data['attribute_name']) || empty($data['attribute_name'])) {
                    $this->session->set_flashdata('message', 'No Atribute Name provided.');
                    return $result;
                }

                if (!isset($data['attribute_description']) || empty($data['attribute_description'])) {
                    $this->session->set_flashdata('message', 'No Attribute Description provided.');
                    return $result;
                }

                if (!isset($data['module_id']) || empty($data['module_id'])) {
                    $this->session->set_flashdata('message', 'No Module provided.');
                    return $result;
                }

                if (!isset($data['module_item_id']) || empty($data['module_item_id'])) {
                    $this->session->set_flashdata('message', 'No Module Item provided.');
                    return $result;
                }
                ## required items section - end

                ## required but can give defaults
                ## Default Zone
                if (!isset($data['zone_id']) || empty($data['zone_id'])) {
                    $location_data['zone_id'] = 2;
                } else {
                    $location_data['zone_id'] = $data['zone_id'];
                }

                ## Default Section
                if (!isset($data['section_id']) || empty($data['section_id'])) {
                    $location_data['section_id'] = 1;
                } else {
                    $location_data['section_id'] = $data['section_id'];
                }

                ## Default Group
                if (!isset($data['group_id']) || empty($data['group_id'])) {
                    $location_data['group_id'] = 1;
                } else {
                    $location_data['group_id'] = $data['group_id'];
                }

                ## Default Order
                if (!isset($data['group_order']) || empty($data['group_order'])) {
                    $this->db->select_max("custom_attribute_location.group_order");
                    $max_order = $this->db->get_where("custom_attribute_location", [ "account_id" => $account_id, "zone_id" => $location_data['zone_id'], "section_id" => $location_data['section_id'], "group_id" => $location_data['group_id'] ])->row();
                    $location_data['group_order'] = (!empty($max_order->group_order)) ? (( int ) $max_order->group_order) + 1 : 1 ;
                } else {
                    $location_data['group_order'] = $data['group_order'];
                }
                ## required but can give defaults - end

                ## non required items section:
                ## Position on report
                if (!isset($data['position_on_report']) || empty($data['position_on_report'])) {
                    $this->db->select_max("custom_attribute.position_on_report");
                    $max_pos = $this->db->get_where("custom_attribute", [ "account_id" => $account_id ])->row();
                    $data['position_on_report'] = (!empty($max_pos->position_on_report)) ? (( int ) $max_pos->position_on_report) + 1 : 1 ;
                }

                ## Default Attribute type = text
                if (!isset($data['response_type']) || empty($data['response_type'])) {
                    $data['response_type'] = "input";
                }

                ## Default Attribute Alt text = Attribute description
                if (!isset($data['attribute_alt_text']) || empty($data['attribute_alt_text'])) {
                    $data['attribute_alt_text'] = $data['attribute_description'] ;
                }

                ## Default Show on Mobile = yes
                if (!isset($data['show_on_mobile']) || empty($data['show_on_mobile'])) {
                    $data['show_on_mobile'] = 1 ;
                }

                ## Default Show on Report = yes
                if (!isset($data['show_on_report']) || empty($data['show_on_report'])) {
                    $data['show_on_report'] = 1 ;
                }

                if (!empty($data['options'])) {
                    $options = $data['options'] ;
                }

                $data['created_by'] = $this->ion_auth->_current_user()->id;
                $data['account_id'] = $account_id;

                ## check if conflicts. Possible conflicts - by the name only.
                $conflict = $this->get_attributes($account_id, ["attribute_name" => $data['attribute_name'], "module_id" => $data['module_id'], "account_id" => $account_id ]);

                if (!$conflict) {
                    $data = $this->ssid_common->_filter_data('custom_attribute', $data);
                    $this->db->insert('custom_attribute', $data);

                    if ($this->db->affected_rows() > 0) {
                        $attribute_id = $this->db->insert_id();

                        ## saving the attribute location - required
                        $location_data['attribute_id'] 	= $attribute_id;
                        $location_data['account_id'] 	= $this->ion_auth->_current_user()->account_id;
                        $location_data['created_by'] 	= $this->ion_auth->_current_user()->id;

                        $location_data = $this->ssid_common->_filter_data('custom_attribute_location', $location_data);
                        $this->db->insert('custom_attribute_location', $location_data);

                        if ($this->db->affected_rows() > 0) {
                            if (in_array($data['response_type'], $this->response_types_w_options)) { ## wrong condition
                                if (!empty($options)) {
                                    foreach ($options as $option) {
                                        $dropdown_options	= [];
                                        $dropdown_options['option_description'] = $option['option_description'];
                                        $dropdown_options['option_label'] 		= $option['option_label'];
                                        $dropdown_options['option_value'] 		= $option['option_value'];
                                        $dropdown_options['account_id'] 		= $this->ion_auth->_current_user()->account_id;
                                        $dropdown_options['attribute_id'] 		= $attribute_id;
                                        $dropdown_options['created_by'] 		= $this->ion_auth->_current_user()->id;

                                        $this->db->insert("custom_dropdowns", $dropdown_options);

                                        if ($this->db->affected_rows() > 0) {
                                            $this->session->set_flashdata('message', 'Option has been created');
                                        }
                                    }
                                }
                            }

                            $result = $this->get_attributes($account_id, ["attribute_id" => $attribute_id ]);
                            $this->session->set_flashdata('message', 'An attribute has been succesfully created.');
                        } else {
                            $this->session->set_flashdata('message', 'Couldn\'t save the location of the attribute.');
                        }
                    } else {
                        $this->session->set_flashdata('message', 'Couldn\'t create an attribute.');
                    }
                } else {
                    $this->session->set_flashdata('message', 'There is already an attribute with this name.');
                }
            } else {
                $this->session->set_flashdata('message', 'No Atribute data provided.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account ID provided.');
        }
        return $result;
    }


    /*
    *	The function to check if the label is unique.
    *	If the provided label is unique, the function will return status true and trimmed version of the provided label.
    *	Othervise, it will return false.
    */
    public function check_label($account_id = false, $module_id = false, $label = false)
    {
        $result = false;
        if (!empty($account_id) && (!empty($module_id)) && (!empty($label))) {
            $trimmed_label = htmlspecialchars($label);
            $trimmed_label = preg_replace('/\s\s+/', '', $label);  ## strips excess whitespace
            $trimmed_label = preg_replace("/[^a-zA-Z0-9_]/", "", $trimmed_label); 	## allow only alphanumeric with underscores for readability

            $conflict = $this->db->get_where("custom_attribute", ["account_id"=>$account_id, "module_id" => $module_id, "attribute_name"=>$trimmed_label])->row();

            if (!empty($conflict)) {
                $this->session->set_flashdata('message', 'The atribute name already exists.');
            } else {
                $result = $trimmed_label;
                $this->session->set_flashdata('message', 'The attribute name seems to be unique');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account ID or Label provided.');
        }
        return $result;
    }


    public function get_sections($account_id = false, $sections_where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($sections_where)) {
                $section_id = $section_name = $module_id = $module_item_id = $organized = false;

                $sections_where = (!is_array($sections_where)) ? convert_to_array($sections_where) : $sections_where ;

                $this->db->select("custom_attribute_sections.*", false);

                if (isset($sections_where['section_id'])) {
                    if (!empty($sections_where['section_id'])) {
                        $section_id = $sections_where['section_id'];
                        $this->db->where("custom_attribute_sections.section_id", $section_id);
                    }
                    unset($sections_where['section_name']);
                }

                if (isset($sections_where['section_name'])) {
                    if (!empty($sections_where['section_name'])) {
                        $section_name = $sections_where['section_name'];
                        $this->db->where("custom_attribute_sections.section_name", $section_name);
                    }
                    unset($sections_where['section_name']);
                }

                if (isset($sections_where['module_id'])) {
                    if (!empty($sections_where['module_id'])) {
                        $module_id = $sections_where['module_id'];
                        $this->db->where("custom_attribute_sections.module_id", $module_id);
                    }
                    unset($sections_where['module_id']);
                }

                if (isset($sections_where['module_item_id'])) {
                    if (!empty($sections_where['module_item_id'])) {
                        $module_item_id = $sections_where['module_item_id'];
                        $this->db->where("custom_attribute_sections.module_item_id", $module_item_id);
                    }
                    unset($sections_where['module_item_id']);
                }

                if (isset($sections_where['organized'])) {
                    $organized = $sections_where['organized'];
                    unset($sections_where['organized']);
                }

                $this->db->where("custom_attribute_sections.account_id", $account_id);
                $this->db->where("custom_attribute_sections.active", 1);

                $arch_where = "( custom_attribute_sections.archived != 1 or custom_attribute_sections.archived is NULL )";
                $this->db->where($arch_where);

                $this->db->order_by("custom_attribute_sections.module_id ASC, custom_attribute_sections.module_item_id ASC");

                $query = $this->db->get("custom_attribute_sections", $limit, $offset);

                if (!empty($query->num_rows()) && ($query->num_rows() > 0)) {
                    if (!empty($section_id)) {
                        $result = $query->result();
                    } else {
                        if ($organized) {
                            foreach ($query->result() as $key => $row) {
                                $result[$row->module_id][$row->module_item_id][$row->section_id] = $row;
                            }
                        } else {
                            foreach ($query->result() as $key => $row) {
                                $result[$row->section_id] = $row;
                            }
                        }
                    }
                    $this->session->set_flashdata('message', 'Sections were found.');
                } else {
                    $this->session->set_flashdata('message', 'No Sections were found.');
                }
            } else {
                $this->session->set_flashdata('message', 'No search criteria provided.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account details provided.');
        }

        return $result;
    }



    public function get_groups($account_id = false, $groups_where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($groups_where)) {
                $group_id = $group_name = $module_id = $module_item_id = $section_id = $organized = false;

                $groups_where = (!is_array($groups_where)) ? convert_to_array($groups_where) : $groups_where ;

                $this->db->select("custom_attribute_groups.*", false);

                if (isset($groups_where['group_id'])) {
                    if (!empty($groups_where['group_id'])) {
                        $group_id = $groups_where['group_id'];
                        $this->db->where("custom_attribute_groups.group_id", $section_id);
                    }
                    unset($groups_where['group_id']);
                }

                if (isset($groups_where['section_id'])) {
                    if (!empty($groups_where['section_id'])) {
                        $section_id = $groups_where['section_id'];
                        $this->db->where("custom_attribute_groups.section_id", $section_id);
                    }
                    unset($groups_where['section_id']);
                }

                if (isset($groups_where['group_name'])) {
                    if (!empty($groups_where['group_name'])) {
                        $group_name = $groups_where['group_name'];
                        $this->db->where("custom_attribute_groups.group_name", $group_name);
                    }
                    unset($groups_where['group_name']);
                }

                if (isset($groups_where['module_id'])) {
                    if (!empty($groups_where['module_id'])) {
                        $module_id = $groups_where['module_id'];
                        $this->db->where("custom_attribute_groups.module_id", $module_id);
                    }
                    unset($groups_where['module_id']);
                }

                if (isset($groups_where['module_item_id'])) {
                    if (!empty($groups_where['module_item_id'])) {
                        $module_item_id = $groups_where['module_item_id'];
                        $this->db->where("custom_attribute_groups.module_item_id", $module_item_id);
                    }
                    unset($groups_where['module_item_id']);
                }

                if (isset($groups_where['organized'])) {
                    $organized = $groups_where['organized'];
                    unset($groups_where['organized']);
                }

                $this->db->where("custom_attribute_groups.account_id", $account_id);
                $this->db->where("custom_attribute_groups.active", 1);

                $arch_where = "( custom_attribute_groups.archived != 1 or custom_attribute_groups.archived is NULL )";
                $this->db->where($arch_where);

                $this->db->order_by("custom_attribute_groups.module_id ASC, custom_attribute_groups.module_item_id ASC");

                $query = $this->db->get("custom_attribute_groups", $limit, $offset);

                if (!empty($query->num_rows()) && ($query->num_rows() > 0)) {
                    if (!empty($group_id)) {
                        $result = $query->result();
                    } else {
                        if ($organized) {
                            foreach ($query->result() as $key => $row) {
                                $result[$row->module_id][$row->module_item_id][$row->section_id][$row->group_id] = $row;
                            }
                        } else {
                            foreach ($query->result() as $key => $row) {
                                $result[$row->group_id] = $row;
                            }
                        }
                    }
                    $this->session->set_flashdata('message', 'Groups were found.');
                } else {
                    $this->session->set_flashdata('message', 'No Groups were found.');
                }
            } else {
                $this->session->set_flashdata('message', 'No search criteria provided.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account details provided.');
        }

        return $result;
    }


    public function delete_attribute($account_id = false, $attribute_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($attribute_id)) {
            $data = [
                'archived' 			=> 1,
                'active'			=> 0,
                'last_modified_by' 	=> $this->ion_auth->_current_user()->id,
            ];
            $this->db->where('attribute_id', $attribute_id);
            $this->db->where('account_id', $account_id);
            $this->db->update('custom_attribute', $data);

            if (($this->db->trans_status() !== false) && ($this->db->affected_rows() > 0)) {
                $this->session->set_flashdata('message', 'Attribute Profile deleted successfully.');
                $result = true;
            } else {
                $this->session->set_flashdata('message', 'No Attribute has been deleted.');
                $result = false;
            }
        } else {
            $this->session->set_flashdata('message', 'No Account ID or Attribute ID provided.');
        }
        return $result;
    }


    public function update_attribute($account_id = false, $attribute_id = false, $update_data = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($attribute_id)) {
            $update_data 		= convert_to_array($update_data) ;
            $data = $attr_data	= $loc_data = [];
            $message			= '';

            if (!empty($update_data)) {
                foreach ($update_data as $key => $value) {
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
                    } elseif (is_string($value)) {
                        $value = trim($value);
                    } else {
                        $value = ($value);
                    }
                    $data[$key] = $value;
                }

                $excluded_columns = ["attribute_id","account_id", "location_id" ];

                ## attribute - custom_attribute
                $ca_columns = $this->db->list_fields("custom_attribute");
                foreach ($ca_columns as $column) {
                    if (!in_array($column, $excluded_columns)) {
                        if (isset($data[$column])) {
                            $attr_data[$column] = $data[$column];
                            unset($data[$column]);
                        }
                    }
                }
                $attr_data['last_modified_by'] = $this->ion_auth->_current_user()->id;
                $ca_data = $this->ssid_common->_filter_data('custom_attribute', $attr_data);
                if (!empty($ca_data)) {
                    $this->db->update('custom_attribute', $ca_data, ["attribute_id" => $attribute_id, "account_id" => $account_id]);
                    if ($this->db->affected_rows() > 0) {
                        $ca_update = true;
                        $message 	.= " Changes to the Attribute saved.";
                    } else {
                        $ca_update = false;
                        $message 	.= " No changes to the Attribute.";
                    }
                }

                ## location - custom_attribute_location
                $loc_columns = $this->db->list_fields("custom_attribute_location");
                foreach ($loc_columns as $column) {
                    if (!in_array($column, $excluded_columns)) {
                        if (isset($data[$column])) {
                            $loc_data[$column] = $data[$column];
                            unset($data[$column]);
                        }
                    }
                }
                $loc_data['last_modified_by'] = $this->ion_auth->_current_user()->id;
                $cal_data = $this->ssid_common->_filter_data('custom_attribute_location', $loc_data);
                if (!empty($cal_data)) {
                    $this->db->update('custom_attribute_location', $cal_data, ["attribute_id" => $attribute_id, "account_id" => $account_id]);
                    if ($this->db->affected_rows() > 0) {
                        $cal_update = true;
                        $message 	.= " The Attribute Location successfully updated.";
                    } else {
                        $cal_update = false;
                        $message 	.= " The Attribute Location hasn't been updated.";
                    }
                }

                ## options
                if (!empty($attr_data['response_type']) && (in_array($attr_data['response_type'], $this->response_types_w_options)) && (!empty($data['options']))) {
                    $attr_options = convert_to_array(json_decode($data['options']));
                    unset($data['options']);

                    $option_data = [
                        "archived" 			=> 1,
                        "active" 			=> null,
                        "last_modified_by" 	=> $this->ion_auth->_current_user()->id,
                    ];
                    $this->db->update("custom_dropdowns", $option_data, ["attribute_id" => $attribute_id, "account_id" => $account_id]);

                    foreach ($attr_options as $key => $array) {
                        foreach ($array as $key2 => $row) {
                            $opt_data[$key][$key2]					= $row;
                            $opt_data[$key]['option_value'] 		= $row;
                            $opt_data[$key]['option_description'] 	= $row;
                            $opt_data[$key]['account_id'] 			= $account_id;
                            $opt_data[$key]['attribute_id'] 		= $attribute_id;
                            $opt_data[$key]['created_by'] 			= $this->ion_auth->_current_user()->id;
                        }
                    }

                    $this->db->insert_batch("custom_dropdowns", $opt_data);
                    if (($this->db->trans_status() !== false) && ($this->db->affected_rows() > 0)) {
                        $message .= " Options were been saved";
                    } else {
                        $message .= " There was an issue processing options";
                    }
                }

                if ($ca_update || $cal_update) {
                    $updated_attribute = $this->get_attributes($account_id, ["attribute_id" => $attribute_id ]);
                    $result = (!empty($updated_attribute)) ? $updated_attribute : false;
                }

                $this->session->set_flashdata('message', $message);
            } else {
                $this->session->set_flashdata('message', 'There was an issue with processing the data.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account ID or Attribute ID provided.');
        }

        return $result;
    }
}
