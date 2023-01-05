<?php

namespace App\Controllers\REST\Api;


use App\Adapter\RESTController;
use App\Models\Service\AccountModel;
use App\Models\Service\DeviceModel;

final class DeviceController extends RESTController
{
	/**
	 * @var \Application\Modules\Service\Models\DeviceModel
	 */
	private $device_service;
	/**
	 * @var \Application\Modules\Service\Models\AccountModel
	 */
	private $account_service;

	public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->device_service = new DeviceModel();
        $this->account_service = new AccountModel();
        $this->form_validation->set_error_delimiters(
			$this->config->item('error_start_delimiter', 'ion_auth'),
			$this->config->item('error_end_delimiter', 'ion_auth')
		);
        $this->lang->load('auth');
    }



    /**
    *   Lookup for the device(s)
    */
    public function devices_lookup_get()
    {
        $get            = $this->get();
        $account_id     = (!empty($get['account_id'])) ? (int) $get['account_id'] : false ;
        $where          = (!empty($get['where'])) ? $get['where'] : false ;
        $limit          = (!empty($get['limit'])) ? (int) $get['limit'] : false ;
        $offset         = (!empty($get['offset'])) ? (int) $get['offset'] : false ;

        $expected_data = [
            'account_id' => $account_id
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required|is_natural_no_zero');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '' ;
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . trim($validation_errors),
                'system_types'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid main Account ID.',
                'devices'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $devices_lookup = $this->device_service->devices_lookup($account_id, $where, $limit, $offset);

        if (!empty($devices_lookup)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'devices'       => $devices_lookup
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'devices'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }




    /**
    *   Add Batch Devices
    */
    public function add_batch_devices_post()
    {
        $post_set       = $this->post();

        $account_id         = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $batch_devices  = (!empty($post_set['batch_devices'])) ? $post_set['batch_devices'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('batch_devices', 'Devices Batch', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Validation errors: ' . $validation_errors,
                'devices_batch'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'devices_batch'     => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $devices_batch = $this->device_service->add_device_batch($account_id, $batch_devices);

        if (!empty($devices_batch)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'devices_batch'     => $devices_batch
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'devices_batch'     => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    public function remove_clearance_from_tmp_post()
    {
        $post_set           = $this->post();

        $account_id         = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $batch_devices      = (!empty($post_set['batch_devices'])) ? $post_set['batch_devices'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('batch_devices', 'Devices Batch', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Validation errors: ' . $validation_errors,
                'devices_removed'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'devices_removed'   => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $devices_removed = $this->device_service->remove_devices_from_tmp($account_id, $batch_devices);

        if (!empty($devices_removed)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'devices_removed'   => $devices_removed
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'devices_removed'   => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Get device(s) data
    */
    public function device_get()
    {
        $get            = $this->get();
        $account_id     = (!empty($get['account_id'])) ? (int) $get['account_id'] : false ;
        $where          = (!empty($get['where'])) ? $get['where'] : false ;
        $limit          = (!empty($get['limit'])) ? (int) $get['limit'] : false ;
        $offset         = (!empty($get['offset'])) ? (int) $get['offset'] : false ;

        $expected_data = [
            'account_id' => $account_id
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required|is_natural_no_zero');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '' ;
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . trim($validation_errors),
                'devices'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid main Account ID.',
                'devices'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $devices = $this->device_service->get_device($account_id, $where, $limit, $offset);

        if (!empty($devices)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'devices'       => $devices
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'devices'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Delete device(s) data
    */
    public function delete_post()
    {
        $post           = $this->post();
        $account_id     = (!empty($post['account_id'])) ? (int) $post['account_id'] : false ;
        $device_ids     = (!empty($post['devices_ids'])) ? $post['devices_ids'] : false ;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required|is_natural_no_zero');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '' ;
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . trim($validation_errors),
                'devices'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid main Account ID.',
                'devices'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $devices_delete = $this->device_service->delete_device($account_id, $device_ids);

        if (!empty($devices_delete)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'devices'       => $devices_delete['delete_status'],
                'stats'         => $devices_delete['stats'],
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'devices'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Connect Devices on Airtime
    */
    public function airtime_connect_post()
    {
        $post_set       = $this->post();

        $account_id     = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $site_id        = (!empty($post_set['site_id'])) ? (int) $post_set['site_id'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('site_id', 'Site ID', 'required|is_natural_no_zero');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Validation errors: ' . $validation_errors,
                'devices_connected' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'devices_connected' => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $airtime_connected = $this->device_service->connect_on_airtime($account_id, $site_id);

        if (!empty($airtime_connected)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'stats'             => $airtime_connected['stats'],
                'devices_connected' => $airtime_connected['items'],
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'devices_connected' => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Unlink Devices from Segments on Airtime
    */
    public function airtime_unlink_post()
    {
        $post_set       = $this->post();

        $account_id     = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $devices_data   = (!empty($post_set['devices_data'])) ? $post_set['devices_data'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('devices_data', 'Device(s) Data', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'    => false,
                'message'   => 'Validation errors: ' . $validation_errors
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'    => false,
                'message'   => (!empty($this->session->flashdata('message'))) ? $this->session->flashdata('message') : ''
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $airtime_unlinked = $this->device_service->unlink_on_airtime($account_id, $devices_data);

        if (!empty($airtime_unlinked['success']) && ($airtime_unlinked['success'] == true)) {
            $message = [
                'status'    => true,
                'message'   => (!empty($airtime_unlinked['status_msg'])) ? $airtime_unlinked['status_msg'] : ((!empty($this->session->flashdata('message'))) ? $this->session->flashdata('message') : '') ,
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'    => false,
                'message'   => (!empty($airtime_unlinked['status_msg'])) ? $airtime_unlinked['status_msg'] : ((!empty($this->session->flashdata('message'))) ? $this->session->flashdata('message') : '')
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Reconnect Devices on Airtime
    */
    public function airtime_reconnect_post()
    {
        $post_set           = $this->post();

        $account_id         = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $devices_data       = (!empty($post_set['devices_data'])) ? $post_set['devices_data'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('devices_data', 'Devices Data', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Validation errors: ' . $validation_errors,
                'devices_connected' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'devices_connected' => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $airtime_connected = $this->device_service->reconnect_on_airtime($account_id, $devices_data);

        if (isset($airtime_connected['success']) && !empty($airtime_connected['success']) && (!empty($airtime_connected['data']))) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'devices_connected' => $airtime_connected['data'],
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'devices_connected' => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Disconnect Devices from Airtime and clear any unlinking and disconnecting errors
    */
    public function airtime_disconnect_post()
    {
        $post_set       = $this->post();

        $account_id     = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $site_id        = (!empty($post_set['site_id'])) ? (int) $post_set['site_id'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('site_id', 'Site ID', 'required|is_natural_no_zero');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'                => false,
                'message'               => 'Validation errors: ' . $validation_errors,
                'devices_disconnected'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'devices_disconnected'  => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $airtime_disconnected = $this->device_service->disconnect_on_airtime($account_id, $site_id);

        if (!empty($airtime_disconnected)) {
            $message = [
                'status'                => true,
                'message'               => (!empty($airtime_disconnected['status_msg'])) ? $airtime_disconnected['status_msg'] : ((!empty($this->session->flashdata('message'))) ? $this->session->flashdata('message') : '') ,
                'stats'                 => $airtime_disconnected['stats'],
                // 'devices_disconnected'   => $airtime_disconnected['items'],      ## Not using 'items' although they may be needed later
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'devices_disconnected'  => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Reallocate the single device to a different site and product, including respective calls to Easel
    */
    public function reallocate_post()
    {
        $this->load->model('Product_model', 'product_service');

        $post           = $this->post();
        $account_id     = (!empty($post['account_id'])) ? (int) $post['account_id'] : false ;
        $device_id      = (!empty($post['device_id'])) ? (int) $post['device_id'] : false ;
        $product_id     = (!empty($post['product_id'])) ? (int) $post['product_id'] : false ;
        $where          = (!empty($post['where'])) ? $post['where'] : false ;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('device_id', 'Device ID', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('product_id', 'Product ID', 'required|is_natural_no_zero');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '' ;
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . trim($validation_errors),
                'device'        => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid main Account ID.',
                'device'        => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $device_exists          = false;
        $where_dev              = ["device_id" => $device_id];
        $device_exists          = $this->device_service->get_device($account_id, $where_dev);

        if (!($device_exists)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'device'        => false
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $product_exists         = false;
        $product_exists         = $this->product_service->get_product($account_id, $product_id);

        if (!($product_exists)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'device'        => false
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $reallocated = $this->device_service->reallocate($account_id, $device_id, $product_id);

        if (($reallocated->status != false) && (!empty($reallocated->data))) {
            $message = [
                'status'        => $reallocated->status,
                'message'       => (!empty($reallocated->message)) ? $reallocated->message : 'Device reallocated' ,
                'device'        => $reallocated->data
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => (!empty($reallocated->message)) ? $reallocated->message : 'Reallocation unsuccessful',
                'device'        => false
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
