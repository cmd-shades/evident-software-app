<?php

namespace App\Controllers\Web;

use App\Extensions\MX\Controller as MX_Controller;
use Application\Extentions\MX_Controller;

class Product extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (false === $this->identity()) {
            redirect("webapp/user/login", 'refresh');
        }

        $this->module_id       = $this->webapp_service->_get_module_id($this->router->fetch_class());


        $this->load->model('serviceapp/Provider_model', 'provider_service');
        $this->load->library('pagination');
    }

    public function index()
    {
        redirect('webapp', 'refresh');
    }


    /*
    *   Create product
    */
    public function create_product($page = "details")
    {
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data)) {
                $postdata               = [];
                $post_data              = $this->input->post();

                $postdata['product_data']   = $post_data;
                $postdata['account_id']     = $this->user->account_id;

                $url            = 'product/create';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result)) {
                    $return_data['new_product']     = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->new_product)) ? $API_result->new_product : null;
                    $return_data['status']      = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    public function add_product_system($page = "details")
    {
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = "details");

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data['system_details']) && (!empty($post_data['product_id']))) {
                $postdata                       = [];
                $postdata['system_details']     = $post_data['system_details'];
                $postdata['product_id']         = (!empty($post_data['product_id'])) ? $post_data['product_id'] : false ;
                $postdata['account_id']         = $this->user->account_id;
                $url                            = 'product/add_product_system';
                $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result)) {
                    $return_data['new_product']     = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->product_system)) ? $API_result->product_system : null;
                    $return_data['status']      = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    public function add_product_package($page = "details")
    {
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = "details");

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data) && (!empty($post_data['product_id']))) {
                $postdata                       = [];
                $postdata['account_id']         = $this->user->account_id;
                $postdata['product_id']         = (!empty($post_data['product_id'])) ? $post_data['product_id'] : false ;
                $postdata['package_data']       = $post_data;
                $url                            = 'product/add_product_package';
                $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result)) {
                    $return_data['product_package']     = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->product_package)) ? $API_result->product_package : null;
                    $return_data['status']      = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    /*
    *   Delete product
    */
    public function delete_product($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();
        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_delete) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data)) {
                $postdata               = [];

                $postdata['product_id']     = (!empty($post_data['product_id'])) ? $post_data['product_id'] : false ;
                $postdata['account_id']     = $this->user->account_id;

                $url            = 'product/delete';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result)) {
                    $return_data['d_product']       = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->d_product)) ? $API_result->d_product : null;
                    $return_data['status']      = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    /*
    *   Delete package
    */
    public function delete_package($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();
        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_delete) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data)) {
                $postdata               = [];

                $postdata['product_package_id']     = (!empty($post_data['product_package_id'])) ? $post_data['product_package_id'] : false ;
                $postdata['account_id']     = $this->user->account_id;

                $url            = 'product/delete_product_package';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                ## d_p_package - Deleted Product Package
                if (!empty($API_result)) {
                    $return_data['d_p_package']     = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->d_p_package)) ? $API_result->d_p_package : null;
                    $return_data['status']          = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']      = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']      = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    /**
    *   Update Package Details
    **/
    public function package_update($page = 'details')
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();
        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data)) {
                $postdata               = [];

                if (!empty($post_data['package_id'])) {
                    $postdata['account_id']     = $this->user->account_id;
                    $postdata['package_id']     = $post_data['package_id'];
                    $postdata['package_data']   = $post_data;

                    $url            = 'product/update_product_package';

                    $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                    ## u_package - Updated Package
                    if (!empty($API_result)) {
                        $return_data['u_package']       = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->u_package)) ? $API_result->u_package : null;
                        $return_data['status']          = (isset($API_result->status)) ? $API_result->status : false ;
                        $return_data['status_msg']      = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                    } else {
                        $return_data['status_msg']      = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                    }
                } else {
                    $return_data['status_msg'] = "Package ID, Account ID and Package Data required.";
                }
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    /**
    *   Update Product Data
    **/
    public function product_update($page = 'details')
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();
        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data)) {
                $postdata               = [];

                if (!empty($post_data['product_id'])) {
                    $postdata['account_id']     = $this->user->account_id;
                    $postdata['product_id']     = $post_data['product_id'];
                    $postdata['product_data']   = $post_data;

                    $url            = 'product/update';
                    $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                    ## u_product - Updated Product
                    if (!empty($API_result)) {
                        $return_data['u_product']       = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->u_product)) ? $API_result->u_product : null;
                        $return_data['status']          = (isset($API_result->status)) ? $API_result->status : false ;
                        $return_data['status_msg']      = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                    } else {
                        $return_data['status_msg']      = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                    }
                } else {
                    $return_data['status_msg'] = "Product ID, Account ID and Package Data required.";
                }
            }
        }

        print_r(json_encode($return_data));
        die();
    }



    public function add_price_plan_to_product($page = "details")
    {
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = "details");

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data['product_id']) && (!empty($post_data['price_plans'])) && (!empty($post_data['site_id']))) {
                $postdata                       = [];
                $postdata['account_id']         = $this->user->account_id;
                $postdata['site_id']            = $post_data['site_id'];
                $postdata['product_id']         = $post_data['product_id'];
                $postdata['price_plans']        = $post_data['price_plans'];
                $url                            = 'product/add_price_plan_to_product';
                $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result)) {
                    $return_data['product']     = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->product)) ? $API_result->product : null;
                    $return_data['status']      = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "No required data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    /*
    *   Function to delete Price Band(Easel), Product Price Plan (CaCTI) and subsequent Availability Windows (Easel, CaCTI)
    */
    public function delete_product_price_plan($page = "details")
    {
        $section = ($this->input->post('page')) ? $this->input->post('page') : ((!empty($page)) ? $page : $this->router->fetch_method());
        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = "details");

        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data['product_price_plan_id'])) {
                $postdata                               = [];
                $postdata['account_id']                 = $this->user->account_id;
                $postdata['product_price_plan_id']      = $post_data['product_price_plan_id'];
                $url                                    = 'product/delete_product_price_plan';
                $API_result                             = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (isset($API_result->status) && ($API_result->status == true)) {
                    $return_data['status']                  = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']              = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']              = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "No required data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }
}
