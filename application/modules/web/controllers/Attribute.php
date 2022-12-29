<?php

namespace Application\Modules\Web\Controllers;

class Attribute extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }
    }

    public function index()
    {
        redirect('webapp', 'refresh');
    }

    public function check_label($module_id = false, $module_item_name = false)
    {
        $post = $this->input->post();

        if (empty($post['label'])) {
            $return_data['status_msg'] = "No Label provided";
        } else {
            $postdata['account_id'] = $this->user->account_id;
            $postdata['label']		= $post['label'];
            $postdata['module_id']	= $post['module_id'];

            $API_call	  = $this->webapp_service->api_dispatcher($this->api_end_point.'attribute/check_label', $postdata, ['auth_token'=>$this->auth_token]);

            if (!empty($API_call->trimmed_label) && ($API_call->status)) {
                $return_data['status'] 			= $API_call->status;
                $return_data['trimmed_label']   = $API_call->trimmed_label;
            } else {
                $return_data['status'] 			= false;
            }

            $return_data['status_msg'] 			= (!empty($API_call->message)) ? $API_call->message : 'Oops! There was an error processing your request.' ;
        }

        print_r(json_encode($return_data));
        die();
    }


    public function create_attribute($module_id = false, $module_item_name = false)
    {
        $return_data = [
            'status' => 0
        ];

        $post 			= ($this->input->post()) ? $this->input->post() : false;

        if (!empty($post['attr_data']['module_id']) && !empty($post['attr_data']['module_item_name'])) {
            # Check module-item access for the logged in user
            $item_access = $this->webapp_service->check_access($this->user, $module_id, $module_item_name);
            if (!$this->is_admin && !$item_access) {
                $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            } else {
                if (!empty($post['attr_data']['options'])) {
                    foreach ($post['attr_data']['options'] as $key => $option) {
                        $option_label = "";
                        $option_label = strip_tags($option['option_label']);
                        $option_label = htmlspecialchars($option_label);
                        $post['attr_data']['options'][$key]['option_value'] = urlencode($option_label);
                        $post['attr_data']['options'][$key]['option_description'] = urlencode($option_label);
                        $post['attr_data']['options'][$key]['option_label'] = urlencode($option_label);
                    }
                }

                $postdata['account_id']		= $this->user->account_id;
                $postdata['attr_data']		= (!empty($post['attr_data'])) ? json_encode($post['attr_data']) : null ;

                $API_call		= $this->webapp_service->api_dispatcher($this->api_end_point.'attribute/create', $postdata, ['auth_token'=>$this->auth_token]);

                $result		  	= (isset($API_call->new_attribute)) ? $API_call->new_attribute : null;
                $message	  	= (isset($API_call->message)) ? $API_call->message : 'Oops! There was an error processing your request.';
                if (!empty($result)) {
                    $return_data['status'] = 1;
                }
                $return_data['status_msg'] = $message;
            }
        } else {
            $return_data['status_msg'] = "Module ID and Module Item Name is required";
        }

        print_r(json_encode($return_data));
        die();
    }



    public function update_attribute_responses()
    {
        $return_data = [
            'status' => 0
        ];

        # Check module-item access for the logged in user
        /* 		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, 'attributes' );
                if( !$this->is_admin && !$item_access ){
                    $return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
                } else { */
        $post 			= ($this->input->post()) ? $this->input->post() : false;

        $postdata 	  	= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
        $API_call		= $this->webapp_service->api_dispatcher($this->api_end_point.'attribute/update_attribute_responses', $postdata, ['auth_token'=>$this->auth_token]);
        $result		  	= (isset($API_call->updated_resp)) ? $API_call->updated_resp : null;
        $message	  	= (isset($API_call->message)) ? $API_call->message : 'Oops! There was an error processing your request.';
        if (!empty($result)) {
            $return_data['status'] = 1;
        }
        $return_data['status_msg'] = $message;
        ##}

        print_r(json_encode($return_data));
        die();
    }


    public function attribute_data($attribute_id = false, $module_id = false, $module_item_name = false)
    {
        $return_data = [
            'status' => 0
        ];

        $post 				= ($this->input->post()) ? $this->input->post() : false;
        $attribute_id 		= (!empty($post['attribute_id'])) ? $post['attribute_id'] : (!empty($attribute_id) ? $attribute_id : false) ;
        $module_id 			= (!empty($post['module_id'])) ? $post['module_id'] : (!empty($module_id) ? $module_id : false) ;
        $module_item_name 	= (!empty($post['module_item_name'])) ? $post['module_item_name'] : (!empty($module_item_name) ? $module_item_name : false) ;


        if (!empty($attribute_id) && !empty($module_id) && !empty($module_item_name)) {
            # Check module-item access for the logged in user
            $item_access = $this->webapp_service->check_access($this->user, $module_id, $module_item_name);
            if (!$this->is_admin && !$item_access) {
                $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            } else {
                $postdata['account_id']					= $this->user->account_id;
                $postdata['where']['attribute_id']		= (!empty($post['attribute_id'])) ? json_encode($post['attribute_id']) : null ;
                $API_call								= $this->webapp_service->api_dispatcher($this->api_end_point.'attribute/attributes', $postdata, ['auth_token'=>$this->auth_token], true);

                if (!empty($API_call->attributes[0])) {
                    $return_data['edit_attribute'] = $this->load_edit_attribute_form($API_call->attributes[0]);
                } else {
                    $return_data['edit_attribute'] = '';
                }

                $message	  	= (isset($API_call->message)) ? $API_call->message : 'Oops! There was an error processing your request.';
                if (!empty($return_data)) {
                    $return_data['status'] = 1;
                }
                $return_data['status_msg'] = $message;
            }
        } else {
            $return_data['status_msg'] = "Module ID, Attribute ID and Module Item Name is required";
        }

        print_r(json_encode($return_data));
        die();
    }


    public function load_edit_attribute_form($attribute = false)
    {
        $return_data = '';
        if (!empty($attribute)) {
            ## get attribute sections
            $attribute_sections			= false;
            $postdata 					= false;
            $postdata["account_id"] 	= $this->user->account_id;
            $postdata["where"] 			= [
                "module_id"			=> (!empty($attribute->module_id)) ? ( int ) $attribute->module_id : false,
                "module_item_id"	=> (!empty($attribute->module_item_id)) ? ( int ) $attribute->module_item_id : false,
            ];
            $API_call	 	  			= $this->webapp_service->api_dispatcher($this->api_end_point.'attribute/sections', $postdata, ['auth_token'=>$this->auth_token], true);
            $attribute_sections			= (!empty($API_call->status) && !empty($API_call->sections)) ? $API_call->sections : false ;

            ## get attribute groups
            $data['attribute_groups']	= false;
            $postdata 					= false;
            $postdata["account_id"] 	= $this->user->account_id;
            $postdata["where"] 			= [
                "module_id"			=> (!empty($attribute->module_id)) ? ( int ) $attribute->module_id : false,
                "module_item_id"	=> (!empty($attribute->module_item_id)) ? ( int ) $attribute->module_item_id : false,
                "organized"			=> true
            ];
            $API_call	 	  			= $this->webapp_service->api_dispatcher($this->api_end_point.'attribute/groups', $postdata, ['auth_token'=>$this->auth_token], true);
            $attribute_groups			= (!empty($API_call->status) && !empty($API_call->groups)) ? $API_call->groups : false ;


            ## get response types
            $data['response_types']		= false;
            $postdata 					= false;
            $postdata["account_id"] 	= $this->user->account_id;
            $postdata["where"] 			= [];
            $API_call	 	  			= $this->webapp_service->api_dispatcher($this->api_end_point.'audit/response_types', $postdata, ['auth_token'=>$this->auth_token], true);
            $response_types				= (!empty($API_call->status) && !empty($API_call->response_types)) ? $API_call->response_types : false ;

            $response_types_w_options	= ["radio","checkbox","select" ];
            $excluded_response_types 	= ["file", "signature"];
            $show_information 			= 0;

            $return_data .= '<form id="update_attribute_form">';
            $return_data .= '<input name="attr_data[attribute_id]" class="form-control" type="hidden" value="'.$attribute->attribute_id.'" />';
            $return_data .= '<input name="attr_data[module_id]" class="form-control" type="hidden" value="'.$attribute->module_id.'" />';
            $return_data .= '<input name="attr_data[module_item_id]" class="form-control" type="hidden" value="'.$attribute->module_item_id.'" />';
            $return_data .= '<input name="attr_data[module_item_name]" class="form-control" type="hidden" value="attributes" />';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Attribute Label:</label>';
            $return_data .= '<input id="attribute_description" name="attr_data[attribute_description]" class="form-control required" type="text" placeholder="Attribute Label (i.e. Room Colour)" value="'.((!empty($attribute->attribute_name)) ? ucwords($attribute->attribute_name) : '').'" />';
            $return_data .= '<input name="attr_data[attribute_name]" class="form-control" type="hidden" value="">';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Attribute Description:</label>';
            $return_data .= '<input name="attr_data[attribute_alt_text]" class="form-control" type="text" placeholder="Attribute Description (i. e. What is the colour of the room?)" value="'.((!empty($attribute->attribute_alt_text)) ? ucwords($attribute->attribute_alt_text) : '').'" />';
            $return_data .= '</div>';

            if ($this->user->is_admin && $show_information == 1) {
                $return_data .= '<div class="input-group form-group">';
                $return_data .= '<label class="input-group-addon">Attribute Area</label>';
                $return_data .= '<select name="attr_data[zone_id]" class="form-control">';
                $return_data .= '<option value="">Please select</option>';
                $return_data .= '<option value="1"'.(($attribute->zone_id == 1) ? ' selected="selected"' : '').'>Information</option>';
                $return_data .= '<option value="2"'.(($attribute->zone_id == 2) ? ' selected="selected"' : '').'>Management</option>';
                $return_data .= '</select>';
                $return_data .= '</div>';
            } else {
                $return_data .= '<input name="attr_data[zone_id]" class="form-control" type="hidden" value="2" />';
            }

            if (!empty($attribute_sections)) {
                $return_data .= '<div class="input-group form-group">';
                $return_data .= '<label class="input-group-addon">Section</label>';
                $return_data .= '<select name="attr_data[section_id]" class="form-control">';
                $return_data .= '<option value="">Please select</option>';

                $i = 0;
                foreach ($attribute_sections as $row) {
                    $return_data .= '<option value="'.$row->section_id.'" '.(($i < 1) ? 'selected="selected"' : "").' >'.$row->section_name.'</option>';  #### Pre-populated to push only to section #1
                    $i++;
                }
                $return_data .= '</select>';
                $return_data .= '</div>';
            } else {
                $return_data .= '<input name="attr_data[section_id]" class="form-control" type="hidden" value="1" />';
            }

            if (!empty($attribute_groups) && !empty($attribute_groups->{ $attribute->module_id }->{ $attribute->module_item_id })) {
                $i=0;
                $groups_within_section = $attribute_groups->{ $attribute->module_id }->{ $attribute->module_item_id };
                foreach ($groups_within_section as $section_id => $groups) {
                    $return_data .= '<div class="input-group form-group section_groups" id="section_id_'.$section_id.'" style="'.(($i < 1) ? "display: table;" : "display: none;").'">';
                    $return_data .= '<label class="input-group-addon">Group</label>';
                    $return_data .= '<select class="form-control group_id_selects">';
                    $return_data .= '<option value="">Please select</option>';
                    foreach ($groups as $row) {
                        $return_data .= '<option value="'.$row->group_id.'" '.((!empty($attribute->group_id) && ($attribute->group_id == $row->group_id)) ? 'selected="selected"' : '').'>'.$row->group_name.'</option>';
                    }
                    $return_data .= '</select>';
                    $return_data .= '</div>';
                    $i++;
                }
                $return_data .= '<input type="hidden" name="attr_data[group_id]" value="" />';
            } else {
                $return_data .= '<input name="attr_data[group_id]" class="form-control" type="hidden" value="1" />';
            }

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Attribute type:</label>';
            if (!empty($response_types)) {
                $return_data .= '<select name="attr_data[attribute_input_type_id]" class="form-control">';
                $return_data .= '<option value="">Please select</option>';
                foreach ($response_types as $row) {
                    if (!in_array($row->response_type, $excluded_response_types)) {
                        $return_data .= '<option value="'.$row->response_type_id.'" data-response_type="'.$row->response_type.'" '.((!empty($row->response_type) && ($row->response_type == $attribute->response_type)) ? 'selected="selected"' : "").'>'.$row->response_type_alt.'</option>';
                    }
                }

                $return_data .= '</select>';
            } else {
                $return_data .= '<input name="attr_data[attribute_input_type_id]" class="form-control" value="'.(!empty($attribute->attribute_input_type_id) ? ( int ) $attribute->attribute_input_type_id : '').'" />';
            }

            $return_data .= '<input type="hidden" name="attr_data[response_type]" value="" />';
            $return_data .= '</div>';

            $return_data .= '<div class="attribute_options" style="display: '.(!empty($attribute->response_type) && (in_array($attribute->response_type, $response_types_w_options)) ? 'block' : 'none').';">';
            $return_data .= '<div class="col-md-12 col-sm-12 col-xs-12">';
            $return_data .= '<div class="row">';
            $return_data .= '<legend class="legend-header">Add max 10 options by using the \'+\'. Remove a single option using the \' - \'. </legend>';
            $return_data .= '<div style="display: block; position: absolute;right: 0;z-index: 999;top: 35px;"><a href="javascript:void(0);" class="add_button" title="Add field" style="font-size: 20px; font-weight: 800;"> + </a></div>';

            if (!empty($attribute->options)) {
                $i = 0;
                foreach ($attribute->options as $key => $option) {
                    /* <!-- One single input row --> */
                    $return_data .= '<div class="field_wrapper" style="width: 100%;">';
                    $return_data .= '<div class="input-group form-group" style="">';
                    $return_data .= '<label class="input-group-addon">Option Value</label>';
                    $return_data .= '<input type="text" name="attr_data[options]['.$option->option_id.']['.$option->option_label.']" class="form-control" value="'.$option->option_value.'">';

                    if ($i > 0) {
                        $return_data .= '<a href="javascript:void(0);" class="remove_button" style="font-size: 20px; font-weight: 800;display: block; position: absolute;right: -27.5px;z-index: 999;top: 0px;"> - </a>';
                    }

                    $return_data .= '</div>';
                    $return_data .= '</div>';
                    $i++;
                }
            } else {
                /* <!-- One single input row --> */
                $return_data .= '<div class="field_wrapper" style="width: 100%;">';
                $return_data .= '<div class="input-group form-group" style="">';
                $return_data .= '<label class="input-group-addon">Option Value</label>';
                $return_data .= '<input type="text" name="attr_data[options][][option_label]" class="form-control" value="" />';
                $return_data .= '</div>';
                $return_data .= '</div>';
            }

            $return_data .= '</div>';
            $return_data .= '</div>';
            $return_data .= '</div>';

            $return_data .= '</form>';

            ?>
<?php 	}
        return $return_data;
    }

    public function delete_attribute($attribute_id = false, $module_id = false, $module_item_name = false)
    {
        $return_data = [
            'status' => 0
        ];

        $post 				= ($this->input->post('attr_data')) ? $this->input->post('attr_data') : false;

        $attribute_id 		= (!empty($post['attribute_id'])) ? $post['attribute_id'] : (!empty($attribute_id) ? $attribute_id : false) ;
        $module_id 			= (!empty($post['module_id'])) ? $post['module_id'] : (!empty($module_id) ? $module_id : false) ;
        $module_item_name 	= (!empty($post['module_item_name'])) ? $post['module_item_name'] : (!empty($module_item_name) ? $module_item_name : false) ;

        if (!empty($attribute_id) && !empty($module_id) && !empty($module_item_name)) {
            # Check module-item access for the logged in user
            $item_access = $this->webapp_service->check_access($this->user, $module_id, $module_item_name);
            if (!$this->is_admin && !$item_access) {
                $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            } else {
                $postdata['account_id']		= $this->user->account_id;
                $postdata['attribute_id']	= $attribute_id;
                $API_call		= $this->webapp_service->api_dispatcher($this->api_end_point.'attribute/delete', $postdata, ['auth_token'=>$this->auth_token]);

                $return_data['status'] 		= (isset($API_call->status) && ($API_call->status == true)) ? 1 : false ;
                $return_data['status_msg']	= (isset($API_call->message)) ? $API_call->message : 'Oops! There was an error processing your request.';
                $return_data['deleted_attribute'] = (isset($API_call->deleted_attribute)) ? $API_call->deleted_attribute : false ;
            }
        } else {
            $return_data['status_msg'] = "Module ID, Attribute ID and Module Item Name is required";
        }

        print_r(json_encode($return_data));
        die();
    }


    public function update_attribute($attribute_id = false, $module_id = false, $module_item_name = false)
    {
        $return_data = [
            'status' => 0
        ];

        $post 				= ($this->input->post('attr_data')) ? $this->input->post('attr_data') : false;

        $attribute_id 		= (!empty($post['attribute_id'])) ? $post['attribute_id'] : (!empty($attribute_id) ? $attribute_id : false) ;
        $module_id 			= (!empty($post['module_id'])) ? $post['module_id'] : (!empty($module_id) ? $module_id : false) ;
        $module_item_name 	= (!empty($post['module_item_name'])) ? $post['module_item_name'] : (!empty($module_item_name) ? $module_item_name : false) ;

        if (!empty($attribute_id) && !empty($module_id) && !empty($module_item_name)) {
            # Check module-item access for the logged in user
            $item_access = $this->webapp_service->check_access($this->user, $module_id, $module_item_name);
            if (!$this->is_admin && !$item_access) {
                $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            } else {
                $postdata 	  	= array_merge(['account_id'=>$this->user->account_id], $post);
                $API_call		= $this->webapp_service->api_dispatcher($this->api_end_point.'attribute/update', $postdata, ['auth_token'=>$this->auth_token]);
                $return_data['status'] 		= (isset($API_call->status) && ($API_call->status == true)) ? 1 : false ;
                $return_data['status_msg']	= (isset($API_call->message)) ? $API_call->message : 'Oops! There was an error processing your request.';
                $return_data['updated_attribute'] = (isset($API_call->updated_attribute)) ? $API_call->updated_attribute : false ;
            }
        } else {
            $return_data['status_msg'] = "Module ID, Attribute ID and Module Item Name is required";
        }

        print_r(json_encode($return_data));
        die();
    }
}
