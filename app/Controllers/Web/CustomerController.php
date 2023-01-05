<?php

namespace App\Controllers\Web;

use App\Extensions\MX\Controller as MX_Controller;

class CustomerController extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->identity()) {
            redirect('webapp/user/login', 'refresh');
        }

        $this->module_id 	   = $this->webapp_service->_get_module_id($this->router->fetch_class());
        $this->load->library('pagination');

        $this->priority_ratings 	= [ 'Low', 'Medium', 'High' ];
    }

    public function index()
    {
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            //access denied
            $this->_render_webpage('errors/access-denied', false);
        } else {
            redirect('webapp/customer/customers', 'refresh');
        }
    }

    /**
    *	Get the customer data
    **/
    public function customers($customer_id = false)
    {
        if ($customer_id) {
            redirect('webapp/customer/profile/'.$customer_id, 'refresh');
        }

        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $customer_types	 		= $this->webapp_service->api_dispatcher($this->api_end_point.'customer/types', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true);
            $data['customer_types'] 	= (isset($customer_types->types)) ? $customer_types->types : null;

            $this->_render_webpage('customer/index', $data);
        }
    }


    /*
    *	Customer lookup / search
    */
    public function lookup($page = 'details')
    {
        $return_data = '';

        # Check module access
        $section = (!empty($page)) ? $page : $this->router->fetch_method();
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            $return_data .= $this->config->item('ajax_access_denied');
        } else {
            # Setup search parameters
            $search_term   = ($this->input->post('search_term')) ? $this->input->post('search_term') : false;
            $where   	   = ($this->input->post('where')) ? $this->input->post('where') : false;
            $limit		   = (!empty($where['limit'])) ? $where['limit'] : DEFAULT_LIMIT;
            $start_index   = ($this->input->post('start_index')) ? $this->input->post('start_index') : DEFAULT_OFFSET;
            $offset		   = (!empty($start_index)) ? (($start_index - 1) * $limit) : 0;
            $order_by	   = ($this->input->post('order_by')) ? $this->input->post('order_by') : false;

            #prepare postdata
            $postdata = [
                'account_id'	=>$this->user->account_id,
                'search_term'	=>$search_term,
                'where'			=>$where,
                'order_by'		=>$order_by,
                'limit'			=>$limit,
                'offset'		=>$offset
            ];

            $search_result		= $this->webapp_service->api_dispatcher($this->api_end_point.'customer/lookup', $postdata, ['auth_token'=>$this->auth_token], true);
            $customers			= (isset($search_result->customers)) ? $search_result->customers : null;

            if (!empty($customers)) {
                ## Create pagination
                $counters 		= (isset($search_result->counters)) ? $search_result->counters : null;
                $page_number	= ($start_index > 0) ? $start_index : 1;
                $page_display	= '<span style="margin:15px 0px;" class="pull-left">Page <strong>'.$page_number.'</strong> of <strong>'.(!empty($counters->pages) ? $counters->pages : "").'</strong></span>';

                if (!empty($counters->total) && ($counters->total > 0)) {
                    $config['total_rows'] 	= $counters->total;
                    $config['per_page'] 	= $limit;
                    $config['current_page'] = $page_number;
                    $pagination_setup 		= _pagination_config();
                    $config					= array_merge($config, $pagination_setup);
                    $this->pagination->initialize($config);
                    $pagination 			= $this->pagination->create_links();
                }

                $return_data = $this->load_customers_view($customers);
                if (!empty($pagination)) {
                    $return_data .= '<tr><td colspan="6">';
                    $return_data .= $page_display.$pagination;
                    $return_data .= '</td></tr>';
                }
            } else {
                $return_data .= '<tr><td colspan="6" style="padding: 0;"><br/>';
                $return_data .= (isset($search_result->message)) ? $search_result->message : 'No records found';
                $return_data .= '</td></tr>';
            }
        }

        print_r($return_data);
        die();
    }

    /*
    * 	Prepare customers view
    */
    private function load_customers_view($customers_data)
    {
        $return_data = '';
        if (!empty($customers_data)) {
            foreach ($customers_data as $k => $customer_details) {
                $return_data .= '<tr>';
                $return_data .= '<td><a href="'.base_url('/webapp/customer/profile/'.$customer_details->customer_id).'" >'.ucwords(strtolower($customer_details->customer_first_name.' '.$customer_details->customer_last_name)).'&nbsp;&nbsp;&nbsp;&nbsp;<small title="This is an uploaded Customer record" style="font-size:80%; display:'.(($customer_details->uploaded_record == 1) ? 'inline-block' : 'none').'"><i class="far fa-arrow-alt-circle-up"></i></small></a> </td>';
                $return_data .= '<td>'.$customer_details->business_name.'</td>';
                $return_data .= '<td>'. (!empty($customer_details->customer_email) ? $customer_details->customer_email : '').'</td>';
                $return_data .= '<td>'. (!empty($customer_details->customer_telephone) ? $customer_details->customer_telephone : '').'</td>';
                $return_data .= '<td>'. (!empty($customer_details->customer_type) ? $customer_details->customer_type : '').'</td>';
                $return_data .= '<td>'. (!empty($customer_details->customer_postcodes) && (strtolower(trim($customer_details->customer_postcodes)) != 'null') ? $customer_details->customer_postcodes : (!empty($customer_details->main_postcode) && (strtolower(trim($customer_details->main_postcode)) != 'null') ? $customer_details->main_postcode : '')).'</td>';
                $return_data .= '</tr>';
            }

            if (!empty($pagination)) {
                $return_data .= '<tr><td colspan="6">';
                $return_data .= $page_display.$pagination;
                $return_data .= '</td></tr>';
            }
        } else {
            $return_data .= '<tr><td colspan="6"><br/>'.$this->config->item("no_records").'</td></tr>';
        }
        return $return_data;
    }


    //View user profile
    public function profile($customer_id = false, $page = 'details')
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } elseif ($customer_id) {
            $customer_details		 	= $this->webapp_service->api_dispatcher($this->api_end_point.'customer/customers', ['account_id'=>$this->user->account_id,'customer_id' => $customer_id], ['auth_token' =>$this->auth_token ], true);
            $data['customer_details']	= (isset($customer_details->customers)) ? $customer_details->customers : null;

            if (!empty($data['customer_details'])) {
                $contract_id 		= $data['customer_details']->contract_id;
                $run_admin_check 	= false;

                #Get allowed access for the logged in user
                $data['permissions']= $item_access;
                $data['active_tab']	= $page;

                $module_items 		= $this->webapp_service->api_dispatcher($this->api_end_point.'access/account_modules_items', ['account_id'=>$this->user->account_id, 'module_id'=>$this->module_id ], ['auth_token'=>$this->auth_token], true);
                $data['module_tabs']= (isset($module_items->module_items)) ? $module_items->module_items : null;

                $data['unordered_tabs'] = (isset($module_items->module_items)) ? $module_items->module_items : null;

                $reordered_tabs 		 = reorder_tabs($data['module_tabs']);
                $data['module_tabs'] 	 = (!empty($reordered_tabs['module_tabs'])) ? $reordered_tabs['module_tabs'] : $data['module_tabs'];

                $data['more_list_active']= (!empty($reordered_tabs['more_list']) && in_array($page, $reordered_tabs['more_list'])) ? true : false;

                $data['priority_ratings']= $this->priority_ratings;

                switch($page) {
                    case 'addresses':
                        $run_admin_check 	  = true;
                        $address_types	 	  = $this->webapp_service->api_dispatcher($this->api_end_point.'address/address_types', ['account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true);
                        $data['address_types']= (isset($address_types->address_types)) ? $address_types->address_types : null;

                        $address_contacts	  = $this->webapp_service->api_dispatcher($this->api_end_point.'customer/address_contacts', ['account_id'=>$this->user->account_id, 'customer_id'=>$customer_id ], ['auth_token'=>$this->auth_token], true);
                        $data['address_contacts']= (isset($address_contacts->address_contacts)) ? $address_contacts->address_contacts : null;

                        $data['include_page'] = 'customer_address.php';
                        break;

                    case 'notes':
                        $data['include_page'] = 'customer_notes.php';

                        $data['customer_notes'] 	= [];
                        $customer_notes				= $this->webapp_service->api_dispatcher($this->api_end_point.'customer/notes', ['account_id'=>$this->user->account_id, "customer_id"=>$customer_id], ['auth_token'=>$this->auth_token], true);
                        $data['customer_notes'] 	= (!empty($customer_notes->notes)) ? $customer_notes->notes : null;

                        break;

                    case 'jobs':

                        $params = ['account_id'=>$this->user->account_id];

                        $customer_jobs 		  = $this->webapp_service->api_dispatcher($this->api_end_point.'job/jobs', ['account_id'=>$this->user->account_id,'where'=>['customer_id' => $customer_id ] ], ['auth_token'=>$this->auth_token], true);
                        $data['customer_jobs']= (isset($customer_jobs->jobs)) ? $customer_jobs->jobs : null;

                        //If contract-id is set, use it to filter the available job-types
                        if (!empty($data['customer_details']->contract_id)) {
                            $params['where']['contract_id'] = $data['customer_details']->contract_id;
                        }

                        $params['limit'] 	  = -1;

                        $job_types		 	  = $this->webapp_service->api_dispatcher($this->api_end_point.'job/job_types', $params, ['auth_token'=>$this->auth_token], true);
                        $data['job_types'] 	  = (isset($job_types->job_types)) ? $job_types->job_types : null;

                        $job_statuses		  = $this->webapp_service->api_dispatcher($this->api_end_point.'job/job_statuses', ['account_id'=>false], ['auth_token'=>$this->auth_token], true);
                        $data['job_statuses'] = (isset($job_statuses->job_statuses)) ? $job_statuses->job_statuses : null;

                        $operatives		  	  = $this->webapp_service->api_dispatcher($this->api_end_point.'user/field_operatives', ['account_id'=>$this->user->account_id, 'where'=>['include_admins'=>1], 'limit'=>-1], ['auth_token'=>$this->auth_token], true);
                        $data['operatives']   = (isset($operatives->field_operatives)) ? $operatives->field_operatives : null;

                        $data['job_durations']= job_durations();

                        if (!empty($contract_id)) {
                            $linked_people	 			= $this->webapp_service->api_dispatcher($this->api_end_point.'contract/linked_people', ['account_id'=>$this->user->account_id, 'contract_id'=>$contract_id], ['auth_token'=>$this->auth_token], true);
                            $linked_people				= (isset($linked_people->people)) ? $linked_people->people : null;
                        }

                        $data['restricted_people']		= !empty($linked_people) ? array_column($linked_people, 'user_id') : [];

                        $data['include_page'] = 'customer_jobs.php';
                        break;

                    case 'details':
                    default:
                        $data['countries']		= $this->ssid_common->get_countries();

                        $people_categories		= $this->webapp_service->api_dispatcher($this->api_end_point.'customer/people_category', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true);
                        $data['people_categories'] 	= (!empty($people_categories->categories)) ? $people_categories->categories : null;

                        $departments		 	= $this->webapp_service->api_dispatcher($this->api_end_point.'customer/departments', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true);
                        $data['departments'] 	= (isset($departments->departments)) ? $departments->departments : null;

                        $user_statuses		  	= $this->webapp_service->api_dispatcher($this->api_end_point.'user/statuses', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true);
                        $data['user_statuses'] 	= (isset($user_statuses->user_statuses)) ? $user_statuses->user_statuses : null;

                        $available_contracts	  	= $this->webapp_service->api_dispatcher($this->api_end_point.'contract/contracts', ['account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true);
                        $data['available_contracts']= (isset($available_contracts->contract)) ? $available_contracts->contract : null;

                        $address_types	 	  	= $this->webapp_service->api_dispatcher($this->api_end_point.'address/address_types', ['account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true);
                        $data['address_types']	= (isset($address_types->address_types)) ? $address_types->address_types : null;

                        $data['include_page'] 	= 'customer_details.php';
                        break;
                }

                //Run the admin check if tab needs only admin
                if (!empty($run_admin_check)) {
                    if ((!admin_check($this->user->is_admin, false, (!empty($data['permissions']) ? $data['permissions']->is_admin : false)))) {
                        $data['admin_no_access'] = true;
                    }
                }
            } else {
                $this->_render_webpage('errors/profile-data-not-found', false);
            }

            $this->_render_webpage('customer/profile', $data, '');
        } else {
            redirect('webapp/customers', 'refresh');
        }
    }


    /**
    *	Update Customer Profile
    **/
    public function update($page = 'details')
    {
        $return_data = [
            'status' => 0
        ];

        $section 	= (!empty($page)) ? $page : $this->router->fetch_method();

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
            ;
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data)) {
                $postdata['customer_id']	= (!empty($post_data['customer_id'])) ? $post_data['customer_id'] : false ;
                $postdata['account_id']		= $this->user->account_id;
                $postdata['customer_data']  = $post_data;

                $API_call 	= $this->webapp_service->api_dispatcher($this->api_end_point.'customer/update', $postdata, ['auth_token'=>$this->auth_token]);

                $result		= (isset($API_call->u_customer)) ? $API_call->u_customer : null;
                $message	= (isset($API_call->message)) ? $API_call->message : 'Oops! There was an error processing your request.';
                if (!empty($result)) {
                    $return_data['status']		= 1;
                    $return_data['u_customer'] 	= $result;
                }
                $return_data['status_msg'] 		= $message;
            } else {
                $return_data['status_msg'] 		= "No required data has been submitted";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    /**
    *	Do address-contact creation
    **/
    public function create_address($page = "details")
    {
        $return_data = [
            'status' => 0
        ];

        # Check module-item access for the logged in user
        $section 		= ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
        $item_access	= $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postdata 	  		= array_merge(['account_id'=>$this->user->account_id], $this->input->post());
            $address_contact	= $this->webapp_service->api_dispatcher($this->api_end_point.'customer/create_address', $postdata, ['auth_token'=>$this->auth_token]);
            $result		  		= (isset($address_contact->address_contact)) ? $address_contact->address_contact : null;
            $message	  		= (isset($address_contact->message)) ? $address_contact->message : 'Oops! There was an error processing your request.';
            if (!empty($result)) {
                $return_data['status'] = 1;
            }
            $return_data['status_msg'] = $message;
        }

        print_r(json_encode($return_data));
        die();
    }



    public function get_address_details($page = 'addresses')
    {
        $return_data = [
            'status' => 0
        ];

        # Check module-item access
        $section 		= (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access 	= $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data .= $this->config->item('ajax_access_denied');
        } else {
            $postset 						= $this->input->post();

            if (!empty($postset)) {
                $postdata = [];
                $postdata['account_id']				= $this->user->account_id;
                $postdata['customer_address_id'] 	= (!empty($postset['customer_address_id'])) ? ( int ) $postset['customer_address_id'] : null ;
                $postdata['customer_id']			= (!empty($postset['customer_id'])) ? ( int ) $postset['customer_id'] : null ;

                $url 						= 'customer/address_contacts';
                $API_result					= $this->ssid_common->api_call($url, $postdata, $method = 'GET');

                $return_data = [
                    'status'				=> (!empty($API_result->status)) ? $API_result->status : false ,
                    'message'				=> (!empty($API_result->message)) ? $API_result->message : "No response" ,
                ];

                $return_data['address_contacts'] =	(!empty($API_result->address_contacts)) ? $this->load_contact_details_view($API_result->address_contacts) : false;

                print_r(json_encode($return_data));
                die();
            }
        }
    }


    /*
    * 	Prepare a view for the Contact update
    */
    private function load_contact_details_view($dataset = false)
    {
        $return_data = '';

        if (!empty($dataset)) {
            $postdata 				= [];

            $address_types					= false;
            $postdata["account_id"]			= $this->user->account_id;
            $url 							= 'address/address_types';
            $API_result						= $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $address_types					= (!empty($API_result->address_types)) ? $API_result->address_types : null;

            /* 			$address_contacts 			 	= false;
                        $postdata["account_id"]			= $this->user->account_id;
                        $postdata["customer_id"]		= $customer_id;
                        $url 							= 'people/address_contacts';
                        $API_result						= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
                        $address_contacts				= ( !empty( $API_result->address_contacts ) ) ? $API_result->address_contacts : null; */



            $return_data .= '<form id="contact_update_in_modal">';
            $return_data .= '<input type="hidden" name="customer_address_id" value="'.$dataset->customer_address_id.'" />';

            $return_data .= '<div style="width:100%;">';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Address Type:</label>';
            $return_data .= '<select name="address_type_id" class="form-control" required><option value="">Please select</option>';

            if (!empty($address_types)) {
                foreach ($address_types as $row) {
                    $return_data .= '<option value="'.($row->address_type_id).'" ';
                    if (!empty($dataset->address_type_id) && ($dataset->address_type_id == $row->address_type_id)) {
                        $return_data .= 'selected="selected"';
                    }

                    $return_data .='>'.($row->address_type).'</option>';
                }
            }
            $return_data .= '</select>';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Address First name:</label>';
            $return_data .= '<input class="form-control" placeholder="Address First name" type="text" name="address_contact_first_name" value="'.(!empty($dataset->address_contact_first_name) ? ($dataset->address_contact_first_name) : '').'" required="required" />';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Address Last name:</label>';
            $return_data .= '<input class="form-control" placeholder="Address Last name" type="text" name="address_contact_last_name" value="'.(!empty($dataset->address_contact_last_name) ? ($dataset->address_contact_last_name) : '').'" required="required" />';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Mobile:</label>';
            $return_data .= '<input class="form-control" placeholder="Address Contact number" type="text" name="address_contact_number" value="'.(!empty($dataset->address_contact_number) ? ($dataset->address_contact_number) : '').'" required="required" />';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Address Line 1:</label>';
            $return_data .= '<input class="form-control" placeholder="Address Line 1" type="text" name="address_line1" value="'.(!empty($dataset->address_line1) ? ($dataset->address_line1) : '').'" />';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Address Line 2:</label>';
            $return_data .= '<input class="form-control" placeholder="Address Line 2" type="text" name="address_line2" value="'.(!empty($dataset->address_line2) ? ($dataset->address_line2) : '').'" />';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Address Line 3:</label>';
            $return_data .= '<input class="form-control" placeholder="Address Line 3" type="text" name="address_line3" value="'.(!empty($dataset->address_line3) ? ($dataset->address_line3) : '').'" />';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Address Town:</label>';
            $return_data .= '<input class="form-control" placeholder="Address Town" type="text" name="address_town" value="'.(!empty($dataset->address_town) ? ($dataset->address_town) : '').'" />';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Address County:</label>';
            $return_data .= '<input class="form-control" placeholder="Address County" type="text" name="address_county" value="'.(!empty($dataset->address_county) ? ($dataset->address_county) : '').'" />';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Address Postcode:</label>';
            $return_data .= '<input class="form-control" placeholder="Address Postcode" type="text" name="address_postcode" value="'.(!empty($dataset->address_postcode) ? ($dataset->address_postcode) : '').'" />';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Address Note:</label>';
            $return_data .= '<textarea name="contact_note" class="form-control" placeholder="Contact Note" rows="3">'.(!empty($dataset->contact_note) ? ( string ) $dataset->contact_note : '').'</textarea>';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Date Created:</label>';
            $return_data .= '<input class="form-control" placeholder="'.(date('d/m/Y')).'" data-date-format="DD/MM/Y" type="text" value="'.(!empty($dataset->created_date) && validate_date($dataset->created_date) ? format_datetime_client($dataset->created_date) : '').'" readonly="readonly" />';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Created By:</label>';
            $return_data .= '<input class="form-control" placeholder="" type="text" value="'.(!empty($dataset->created_by) ? ($dataset->created_by) : '').'" readonly="readonly" />';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Last Modified:</label>';
            $return_data .= '<input class="form-control" data-date-format="DD/MM/Y" type="text" value="'.(!empty($dataset->last_modified) &&  validate_date($dataset->last_modified) ? format_datetime_client($dataset->last_modified) : '').'" readonly="readonly" />';
            $return_data .= '</div>';

            $return_data .= '<div class="input-group form-group">';
            $return_data .= '<label class="input-group-addon">Modified By:</label>';
            $return_data .= '<input class="form-control" type="text" value="'.(!empty($dataset->modified_by) ? ($dataset->modified_by) : '').'" readonly="readonly" />';
            $return_data .= '</div>';

            $return_data .= '</div><div class="row"><div class="col-md-4 pull-right">';

            $item_access = $this->webapp_service->check_access($this->user, $this->module_id, "addresses");
            if ($this->user->is_admin || !empty($item_access->can_edit) || !empty($item_access->is_admin)) {
                $return_data .= '<button id="updateAddressBtn" class="btn-success btn-next btn btn-sm btn-block btn-flow margin_top_8">Update Address</button>';
            } else {
                $return_data .= '<button class="btn-success btn btn-sm btn-flow btn-success btn-next submit no-permissions" disabled style="width: 100%;margin-top: 10px;">No Permissions</button>';
            }

            $return_data .= '</div></div>';
            $return_data .= '</form>';
        } else {
            $return_data .= '<div width="100%">';
            $return_data .= '<div><div colspan="2">'.$this->config->item("no_data").'</div></div>';
            $return_data .= '</div>';
        }

        return $return_data;
    }


    /*
    *	Update contact address
    */
    public function update_address($page = "addresses")
    {
        $result['status'] 	= 0;

        $section 			= (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access 		= $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $result['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postset 						= $this->input->post();
            if (!empty($postset)) {
                $postdata = [];

                $postdata["customer_address_id"] 	= (!empty($postset["customer_address_id"])) ? ( int ) $postset["customer_address_id"] : false ;
                $postdata["dataset"] 				= $postset;
                $postdata["account_id"]				= $this->user->account_id;

                $url 						= 'customer/update_address';
                $API_result					= $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if ((!empty($API_result->status) && ($API_result->status == true))) {
                    $result = [
                        'status'			=> $API_result->status,
                        'status_msg'		=> $API_result->message,
                        'updated_address'	=> $API_result->updated_address,
                    ];
                } else {
                    if ((!empty($API_result->message))) {
                        $result['status'] 		= false ;
                        $result['status_msg'] 	= $API_result->message;
                    } else {
                        $result['status'] 		= false ;
                        $result['status_msg'] 	= 'Something went wrong';
                    }
                }
            }
        }
        print_r(json_encode($result));
        die();
    }


    /*
    *	Update contact address
    */
    public function delete_address($page = "addresses")
    {
        $result['status'] 	= 0;

        $section 			= (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access 		= $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $result['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postset 						= $this->input->post();
            if (!empty($postset)) {
                $postdata = [];

                $postdata["customer_address_id"] 	= (!empty($postset["customer_address_id"])) ? ( int ) $postset["customer_address_id"] : false ;
                $postdata["account_id"]				= $this->user->account_id;

                $url 						= 'customer/delete_address';
                $API_result					= $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if ((!empty($API_result->status) && ($API_result->status == true))) {
                    $result = [
                        'status'			=> $API_result->status,
                        'status_msg'		=> $API_result->message,
                        'deleted_address'	=> $API_result->deleted_address,
                    ];
                } else {
                    if ((!empty($API_result->message))) {
                        $result['status'] 		= false ;
                        $result['status_msg'] 	= $API_result->message;
                    } else {
                        $result['status'] 		= false ;
                        $result['status_msg'] 	= 'Something went wrong';
                    }
                }
            }
        }
        print_r(json_encode($result));
        die();
    }


    /*
    *	The function to create a new note
    */
    public function create_note($page = "notes")
    {
        $return_data['status'] 	= 0;
        $postset				= false;

        # Check module-item access for the logged in user
        $section = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postset 					= $this->input->post();

            if (!empty($postset)) {
                $postdata 	  		= array_merge(['account_id'=>$this->user->account_id], $postset);
                $url 				= 'customer/create_note';
                $API_result			= $this->ssid_common->api_call($url, $postdata);

                if (!empty($API_result) && ($API_result->status == true)) {
                    $return_data['new_note']   	= $API_result->new_note;
                }

                $return_data['status_msg'] 	= (!empty($API_result->message)) ? $API_result->message : "New Note hasn't been created" ;
                $return_data['status']		= (!empty($API_result->status)) ? $API_result->status : 0 ;
            } else {
                $return_data['status_msg'] 	= "Missing Post Data";
            }
        }

        print_r(json_encode($return_data));
        die();
    }



    public function create($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->method;

        $module_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($module_access->can_add) && empty($module_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $customer_types	 			= $this->webapp_service->api_dispatcher($this->api_end_point.'customer/types', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true);
            $data['customer_types'] 	= (isset($customer_types->types)) ? $customer_types->types : null;

            $available_contracts	  	= $this->webapp_service->api_dispatcher($this->api_end_point.'contract/contracts', ['account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true);
            $data['available_contracts']= (isset($available_contracts->contract)) ? $available_contracts->contract : null;

            $address_types	 	  = $this->webapp_service->api_dispatcher($this->api_end_point.'address/address_types', ['account_id'=>$this->user->account_id, "address_type_group" => "main", "strict_mode" => true ], ['auth_token'=>$this->auth_token], true);
            $data['address_types']= (isset($address_types->address_types)) ? $address_types->address_types[0] : null;

            $this->_render_webpage('customer/customer_create_new', $data);
        }
    }



    public function create_customer($page = "details")
    {
        $return_data['status'] 	= 0;
        $postset				= false;

        # Check module-item access for the logged in user
        $section = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $postset 					= $this->input->post();

            if (!empty($postset)) {
                $postdata 	  		= array_merge(['account_id'=>$this->user->account_id], $postset);
                $url 				= 'customer/create';
                $API_result			= $this->ssid_common->api_call($url, $postdata);

                if (!empty($API_result) && ($API_result->status == true)) {
                    $return_data['customer']   	= $API_result->customer;
                    $return_data['status_msg'] 	= (!empty($API_result->message)) ? $API_result->message : "New Customer has been created" ;
                } else {
                    $return_data['status_msg'] 	= (!empty($API_result->message)) ? $API_result->message : "New Customer hasn't been created" ;
                }

                $return_data['status']		= (!empty($API_result->status)) ? $API_result->status : 0 ;
            } else {
                $return_data['status_msg'] 	= "Missing Post Data";
            }
        }

        print_r(json_encode($return_data));
        die();
    }
}
