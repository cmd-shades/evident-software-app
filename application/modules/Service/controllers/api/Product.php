<?php

namespace Application\Modules\Service\Controllers\Api;

use App\Libraries\REST_Controller;

class Product extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library("Ssid_common");
        $this->load->library("form_validation");
        $this->load->library("email");
        $this->load->model("Product_model", "product_service");
        $this->load->model("Site_model", "site_service");
    }


    /*
    *   Creation of the product
    */
    public function create_post()
    {
        $post_set       = $this->post();

        $account_id     = (!empty($post_set['account_id'])) ? $post_set['account_id'] : false;
        $product_data   = (!empty($post_set['product_data'])) ? $post_set['product_data'] : false;

        if (is_array($product_data)) {
            $this->form_validation->set_rules('product_data[]', 'Product Data', 'required');
        } else {
            $this->form_validation->set_rules('product_data', 'Product Data', 'required');
        }
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'new_product'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid main Account ID',
                'new_product'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_product = $this->product_service->create_product($account_id, $product_data);

        if (!empty($new_product)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'new_product'   => $new_product
            ];
            $this->response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'new_product'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get Product(s)
    */
    public function product_get()
    {
        $get_set        = $this->get();

        $account_id     = (!empty($get_set['account_id'])) ? $get_set['account_id'] : false;
        $product_id     = (!empty($get_set['product_id'])) ? $get_set['product_id'] : false;
        $where          = (!empty($get_set['where'])) ? $get_set['where'] : false;
        $limit          = (!empty($get_set['limit'])) ? $get_set['limit'] : false;
        $offset         = (!empty($get_set['offset'])) ? $get_set['offset'] : false;

        $expected_data = [
            'account_id'    => $account_id ,
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Invalid or missing Field(s)',
                'product'           => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'product'       => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $product = $this->product_service->get_product($account_id, $product_id, $where, $limit, $offset);

        if (!empty($product)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'product'       => $product
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'product'       => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Add System (with packages) as a part of the Product
    */
    public function add_product_system_post()
    {
        $product_exists     = false;
        $post_set           = $this->post();

        $account_id         = (!empty($post_set['account_id'])) ? $post_set['account_id'] : false;
        $product_id         = (!empty($post_set['product_id'])) ? $post_set['product_id'] : false;
        $system_details     = (!empty($post_set['system_details'])) ? $post_set['system_details'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('product_id', 'Product ID', 'required');
        $this->form_validation->set_rules('system_details', 'System Details', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Invalid or missing Field(s)',
                'product_system'    => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'product_system'    => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $product_exists = $this->product_service->get_product($account_id, $product_id);
        if (!$product_exists) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'product_system'    => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $product_system = $this->product_service->add_system_to_product($account_id, $product_id, $system_details);

        if (!empty($product_system)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'product_system'    => $product_system
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'product_system'    => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Add Package to the Product
    */
    public function add_product_package_post()
    {
        $product_exists     = false;
        $post_set           = $this->post();

        $account_id         = (!empty($post_set['account_id'])) ? $post_set['account_id'] : false;
        $product_id         = (!empty($post_set['product_id'])) ? $post_set['product_id'] : false;
        $package_data       = (!empty($post_set['package_data'])) ? $post_set['package_data'] : false;


        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('product_id', 'Product ID', 'required');
        $this->form_validation->set_rules('package_data', 'Package Data', 'required');


        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Invalid or missing Field(s)',
                'product_package'   => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'product_package'   => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $product_exists = $this->product_service->get_product($account_id, $product_id);
        if (!$product_exists) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'product_package'   => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $product_package = $this->product_service->add_package_to_product($account_id, $product_id, $package_data);

        if (!empty($product_package)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'product_package'   => $product_package
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'product_package'   => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   Delete the product
    */
    public function delete_post()
    {
        $post_set       = $this->post();

        $account_id     = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $product_id     = (!empty($post_set['product_id'])) ? (int) $post_set['product_id'] : false;

        $expected_data = [
            'account_id'    => $account_id ,
            'product_id'    => $product_id ,
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('product_id', 'Product ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'd_product'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'd_product'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## check the product exists
        $product_exists = $this->product_service->get_product($account_id, $product_id);
        if ((!$product_exists) || empty($product_id) || ((int) $product_id <= 0)) {
            $message = [
                'status'        => false,
                'message'       => "Invalid Product ID",
                'd_product'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $d_product = $this->product_service->delete_product($account_id, $product_id);

        if (!empty($d_product)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'd_product'     => $d_product
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'd_product'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   Delete the package from the product
    */
    public function delete_product_package_post()
    {
        $post_set               = $this->post();

        $account_id             = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $product_package_id     = (!empty($post_set['product_package_id'])) ? (int) $post_set['product_package_id'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('product_package_id', 'Product Package ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'd_p_package'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'd_p_package'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## check the product package exists
        $product_package_exists = $this->product_service->get_product_package($account_id, $product_package_id);
        if ((!$product_package_exists) || empty($product_package_id) || ((int) $product_package_id <= 0)) {
            $message = [
                'status'        => false,
                'message'       => "Invalid Product Package ID",
                'd_p_package'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $d_p_package = $this->product_service->delete_product_package($account_id, $product_package_id);

        if (!empty($d_p_package)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'd_p_package'   => $d_p_package
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'd_p_package'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   Update package data within the product
    */
    public function update_product_package_post()
    {
        $post_set               = $this->post();

        $account_id             = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $package_id             = (!empty($post_set['package_id'])) ? (int) $post_set['package_id'] : false;
        $package_data           = (!empty($post_set['package_data'])) ? $post_set['package_data'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('package_id', 'Package ID', 'required');
        $this->form_validation->set_rules('package_data', 'Package Data', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'u_package'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'u_package'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## check the package exists
        $package_exists = $this->product_service->get_package($account_id, $package_id);

        if ((!$package_exists) || empty($package_id) || ((int) $package_id <= 0)) {
            $message = [
                'status'        => false,
                'message'       => "Invalid Package ID",
                'u_package'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $u_package = $this->product_service->update_package($account_id, $package_id, $package_data);

        if (!empty($u_package)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'u_package'     => $u_package
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'u_package'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   Update product data
    */
    public function update_post()
    {
        $post_set               = $this->post();

        $account_id             = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $product_id             = (!empty($post_set['product_id'])) ? (int) $post_set['product_id'] : false;
        $product_data           = (!empty($post_set['product_data'])) ? $post_set['product_data'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('product_id', 'Product ID', 'required');
        $this->form_validation->set_rules('product_data', 'Product Data', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'u_product'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'u_product'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## check the product exists
        $product_exists = $this->product_service->get_product($account_id, $product_id);

        if ((!$product_exists) || empty($product_id) || ((int) $product_id <= 0)) {
            $message = [
                'status'        => false,
                'message'       => "Invalid Product ID",
                'u_product'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $u_product = $this->product_service->update($account_id, $product_id, $product_data);

        if (!empty($u_product)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'u_product'     => $u_product
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'u_product'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Adding Price Plan(s) to the existing Product
    *   Assuming there will be multiple plans in the price_plans variable
    */
    public function add_price_plan_to_product_post()
    {
        $site_and_product_exists = false;
        $post_set           = $this->post();

        $account_id         = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $site_id            = (!empty($post_set['site_id'])) ? (int) $post_set['site_id'] : false;
        $product_id         = (!empty($post_set['product_id'])) ? (int) $post_set['product_id'] : false;
        $price_plans        = (!empty($post_set['price_plans'])) ? $post_set['price_plans'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('site_id', 'Site ID', 'required');
        $this->form_validation->set_rules('product_id', 'Product ID', 'required');
        $this->form_validation->set_rules('price_plans', 'Price Plan(s) Data', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid or missing Field(s)',
                'product'       => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'product'       => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $site_and_product_exists = $this->product_service->get_site_and_product($account_id, $site_id, $product_id);

        if (!$site_and_product_exists) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'product'       => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $product = $this->product_service->add_price_plan_to_product($account_id, $site_id, $product_id, $price_plans);

        if (!empty($product)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'product'       => $product
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'product'       => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Activate (invite) the Market on Easel
    */
    public function activate_market_post()
    {
        $post_set           = $this->post();

        $account_id         = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $product_id         = (!empty($post_set['product_id'])) ? (int) $post_set['product_id'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('product_id', 'Product ID', 'required|is_natural_no_zero');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid or missing Field(s)',
                'market'        => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'market'        => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $product_exists = $this->product_service->get_product($account_id, $product_id);

        if (!$product_exists) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'market'        => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $activated_market = $this->product_service->activate_market_on_airtime($account_id, $product_id);

        if (!empty($activated_market->status) && ($activated_market->status != false) && (!empty($activated_market->data))) {
            $message = [
                'status'        => (!empty($activated_market->status)) ? $activated_market->status : true,
                'message'       => (!empty($activated_market->message)) ? $activated_market->message : ((!empty($this->session->flashdata('message'))) ? $this->session->flashdata('message') : ''),
                'market'        => (!empty($activated_market->data)) ? $activated_market->data : $activated_market
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => (!empty($activated_market->message)) ? $activated_market->message : ((!empty($this->session->flashdata('message'))) ? $this->session->flashdata('message') : ''),
                'market'        => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }




    /**
    *   De-activate (un-invite) the Market on Easel
    */
    public function deactivate_market_post()
    {
        $post_set           = $this->post();

        $account_id         = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $product_id         = (!empty($post_set['product_id'])) ? (int) $post_set['product_id'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('product_id', 'Product ID', 'required|is_natural_no_zero');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid or missing Field(s)',
                'market'        => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'market'        => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $product_exists = $this->product_service->get_product($account_id, $product_id);

        if (!$product_exists) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'market'        => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $deactivated_market = $this->product_service->deactivate_market_on_airtime($account_id, $product_id);

        if (!empty($deactivated_market->status) && ($deactivated_market->status != false) && (!empty($deactivated_market->data))) {
            $message = [
                'status'        => (!empty($deactivated_market->status)) ? $deactivated_market->status : true,
                'message'       => (!empty($deactivated_market->message)) ? $deactivated_market->message : ((!empty($this->session->flashdata('message'))) ? $this->session->flashdata('message') : 'Market is de-activated'),
                'market'        => (!empty($deactivated_market->data)) ? $deactivated_market->data : $deactivated_market
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => (!empty($deactivated_market->message)) ? $deactivated_market->message : ((!empty($this->session->flashdata('message'))) ? $this->session->flashdata('message') : ''),
                'market'        => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   Function to delete Price Band(Easel), Product Price Plan (CaCTI) and archive subsequent Availability Windows (Easel, CaCTI)
    */
    public function delete_product_price_plan_post()
    {
        $post_set                   = $this->post();

        $account_id                 = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $product_price_plan_id      = (!empty($post_set['product_price_plan_id'])) ? (int) $post_set['product_price_plan_id'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('product_price_plan_id', 'Product Price Plan', 'required|is_natural_no_zero');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'                => false,
                'message'               => 'Invalid or missing Field(s)',
                'product_price_plan'    => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'product_price_plan'    => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $product_price_plan_exists = $this->product_service->get_product_price_plan($account_id, ["product_price_plan_id" => $product_price_plan_id]);

        if (!$product_price_plan_exists) {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'product_price_plan'    => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $deleted_product_price_plan = $this->product_service->delete_product_price_plan($account_id, $product_price_plan_id);

        log_message("error", json_encode(["deleted_product_price_plan" => $deleted_product_price_plan]));

        if (!empty($deleted_product_price_plan->status) && ($deleted_product_price_plan->status != false)) {
            $message = [
                'status'                => (!empty($deleted_product_price_plan->status)) ? $deleted_product_price_plan->status : true,
                'message'               => (!empty($deleted_product_price_plan->message)) ? $deleted_product_price_plan->message : ((!empty($this->session->flashdata('message'))) ? $this->session->flashdata('message') : 'Price Plan has been deleted'),
                'product_price_plan'    => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                => false,
                'message'               => (!empty($deleted_product_price_plan->message)) ? $deleted_product_price_plan->message : ((!empty($this->session->flashdata('message'))) ? $this->session->flashdata('message') : ''),
                'product_price_plan'    => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
