<?php

namespace App\Controllers\REST\Api;

use App\Adapter\RESTController;
use App\Models\Service\EaselApiModel;

final class EaseltvController extends RESTController
{
	/**
	 * @var \Application\Modules\Service\Models\EaselApiModel
	 */
	private $easel_service;

	public function __construct()
    {
        parent::__construct();
        $this->easel_service = new EaselApiModel();
    }

    /** Check Easel Server Connection **/
    public function check_connection_get()
    {
        $account_id             = (int) $this->get('account_id');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'       => 'Invalid main Account ID',
                'connection'    => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $connection = $this->easel_service->check_connection($account_id);

        if (!empty($connection)) {
            $message = [
                'status'        => true,
                'http_code'     => REST_Controller::HTTP_OK,
                'message'       => $this->session->flashdata('message'),
                'connection'    => (!empty($connection->records)) ? $connection->records : (!empty($connection) ? $connection : null),
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_NO_CONTENT,
                'message'       => 'No records found',
                'connection'    => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Get Audio Track **/
    public function audio_track_get()
    {
        $account_id             = (int) $this->get('account_id');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'       => 'Invalid main Account ID',
                'audio_track'   => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $audio_track = $this->easel_service->get_audio_track($account_id);

        if (!empty($audio_track)) {
            $message = [
                'status'        => true,
                'http_code'     => REST_Controller::HTTP_OK,
                'message'       => $this->session->flashdata('message'),
                'audio_track'   => (!empty($audio_track->records)) ? $audio_track->records : (!empty($audio_track) ? $audio_track : null),
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_NO_CONTENT,
                'message'       => 'No records found',
                'audio_track'   => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Get Media File Format **/
    public function media_file_format_get()
    {
        $account_id             = (int) $this->get('account_id');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'           => 'Invalid main Account ID',
                'media_file_format' => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $media_file_format = $this->easel_service->get_media_file_format($account_id);

        if (!empty($media_file_format)) {
            $message = [
                'status'            => true,
                'http_code'         => REST_Controller::HTTP_OK,
                'message'           => $this->session->flashdata('message'),
                'media_file_format' => (!empty($media_file_format->records)) ? $media_file_format->records : (!empty($media_file_format) ? $media_file_format : null),
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'http_code'         => REST_Controller::HTTP_NO_CONTENT,
                'message'           => 'No records found',
                'media_file_format' => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Create new Product = Film/Content
    */
    public function create_product_post()
    {
        $product_data = $this->post();
        $account_id     = (int)$this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('title', 'Title', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_BAD_REQUEST,
                'message'       => 'Invalid data: ',
                'product'   => null
            ];

            $message['message'] = (!$account_id) ? $message['message'] . 'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'       => 'Invalid main Account ID.',
                'product'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
        $new_product = $this->easel_service->create_product($account_id, $product_data);

        if (!empty($new_product->success)) {
            $message = [
                'status'    => true,
                'http_code' => REST_Controller::HTTP_CREATED,
                'message'   => $this->session->flashdata('message'),
                'product'   => !empty($new_product->data) ? $new_product->data : $new_product
            ];
            $this->response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'    => false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message'   => (!empty($new_product->message)) ? $new_product->message : $this->session->flashdata('message'),
                'product'   => $new_product
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Update Product / Film Details
    */
    public function update_product_post()
    {
        $product_data   = $this->post();
        $account_id     = (int) $this->post('account_id');
        $product_id     = $this->post('id');

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('id', 'Product ID', 'required');
        $this->form_validation->set_rules('asset_code', 'Asset Code', 'required');
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('state', 'State', 'required');
        $this->form_validation->set_rules('plot', 'Plot', 'required');
        $this->form_validation->set_rules('running_time', 'Running Time', 'required');
        $this->form_validation->set_rules('country', 'Country', 'required');
        $this->form_validation->set_rules('restriction_type', 'Restriction type', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status'    => false,
                'http_code' => REST_Controller::HTTP_BAD_REQUEST,
                'message'   => 'Invalid data: ',
                'product'   => null
            ];

            $message['message'] = (!$account_id) ? $message['message'] . 'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'    => false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'   => 'Invalid main Account ID.',
                'product'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Validate the Product id.
        if ($product_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        ## Run update call
        $updated_product = $this->easel_service->update_product($account_id, $product_id, $product_data);

        if (!empty($updated_product->success)) {
            $message = [
                'status'    => true,
                'http_code' => REST_Controller::HTTP_OK,
                'message'   => $this->session->flashdata('message'),
                'product'   => !empty($updated_product->data) ? $updated_product->data : $updated_product
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'    => false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message'   => (!empty($updated_product->message)) ? $updated_product->message : $this->session->flashdata('message'),
                'product'   => $updated_product
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Get Product / Film / Content **/
    public function fetch_product_get()
    {
        $account_id     = (int) $this->get('account_id');
        $product_id     = $this->get('id');
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'    => false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'   => 'Invalid main Account ID',
                'product'   => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $product = $this->easel_service->fetch_product($account_id, $product_id);

        if (!empty($product)) {
            $message = [
                'status'    => true,
                'http_code' => REST_Controller::HTTP_OK,
                'message'   => $this->session->flashdata('message'),
                'product'   => (!empty($product->records)) ? $product->records : (!empty($product) ? $product : null),
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'    => false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message'   => (!empty($this->session->flashdata('message'))) ? $this->session->flashdata('message') : 'No records found',
                'product'   => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Create new Price Band
    */
    public function create_price_band_post()
    {
        $price_band_data = $this->post();
        $account_id     = (int)$this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('title', 'Title', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_BAD_REQUEST,
                'message'       => 'Invalid data: ',
                'price_band'    => null
            ];

            $message['message'] = (!$account_id) ? $message['message'] . 'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'       => 'Invalid main Account ID.',
                'price_band'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
        $new_price_band = $this->easel_service->create_price_band($account_id, $price_band_data);

        if (!empty($new_price_band->success)) {
            $message = [
                'status'    => true,
                'http_code' => REST_Controller::HTTP_CREATED,
                'message'   => $this->session->flashdata('message'),
                'price_band' => !empty($new_price_band->data) ? $new_price_band->data : $new_price_band
            ];
            $this->response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'    => false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message'   => (!empty($new_price_band->message)) ? $new_price_band->message : $this->session->flashdata('message'),
                'price_band' => $new_price_band
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * Update Price Band
    */
    public function update_price_band_put()
    {
        $price_band_data    = $this->put();
        $account_id     = (int) $this->put('account_id');
        $price_band_id  = $this->put('id');

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('id', 'Price Band ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status'    => false,
                'http_code' => REST_Controller::HTTP_BAD_REQUEST,
                'message'   => 'Invalid data: ',
                'price_band' => null
            ];

            $message['message'] = (!$account_id) ? $message['message'] . 'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'    => false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'   => 'Invalid main Account ID.',
                'price_band' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Validate the Price Band id.
        if ($price_band_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        ## Run update call
        $updated_price_band = $this->easel_service->update_price_band($account_id, $price_band_id, $price_band_data);

        if (!empty($updated_price_band->success)) {
            $message = [
                'status'    => true,
                'http_code' => REST_Controller::HTTP_OK,
                'message'   => $this->session->flashdata('message'),
                'price_band' => !empty($updated_price_band->data) ? $updated_price_band->data : $updated_price_band
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'    => false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message'   => (!empty($updated_price_band->message)) ? $updated_price_band->message : $this->session->flashdata('message'),
                'price_band' => $updated_price_band
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /** Get Price Bands **/
    public function fetch_price_band_get()
    {
        $account_id     = (int) $this->get('account_id');
        $price_band_id      = $this->get('id');
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'    => false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'   => 'Invalid main Account ID',
                'price_band' => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $price_band = $this->easel_service->fetch_price_band($account_id, $price_band_id);

        if (!empty($price_band)) {
            $message = [
                'status'    => true,
                'http_code' => REST_Controller::HTTP_OK,
                'message'   => $this->session->flashdata('message'),
                'price_band' => (!empty($price_band->records)) ? $price_band->records : (!empty($price_band) ? $price_band : null),
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'    => false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message'   => 'No records found',
                'price_band' => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Create new Availability Window
    */
    public function create_availability_window_post()
    {
        $availability_window_data = $this->post();

        $account_id     = (int) $this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('productId', 'Product ID', 'required');
        $this->form_validation->set_rules('visibleFrom', 'Visible From', 'required');
        $this->form_validation->set_rules('visibleTo', 'Visible To', 'required');
        $this->form_validation->set_rules('priceBandId', 'Price Band ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_BAD_REQUEST,
                'message'               => 'Invalid data: ',
                'availability_window'   => null
            ];

            $message['message'] = (!$account_id) ? $message['message'] . 'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'               => 'Invalid main Account ID.',
                'availability_window'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
        $new_availability_window = $this->easel_service->create_availability_window($account_id, $availability_window_data);

        if (!empty($new_availability_window->success)) {
            $message = [
                'status'                => true,
                'http_code'             => REST_Controller::HTTP_CREATED,
                'message'               => $this->session->flashdata('message'),
                'availability_window'   => !empty($new_availability_window->data) ? $new_availability_window->data : $new_availability_window
            ];
            $this->response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_NO_CONTENT,
                'message'               => (!empty($new_availability_window->message)) ? $new_availability_window->message : $this->session->flashdata('message'),
                'availability_window'   => $new_availability_window
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Get Availability Window data from Easel
    **/
    public function fetch_availability_window_get()
    {
        $account_id     = (int) $this->get('account_id');
        $id             = $this->get('id');
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'    => false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'   => 'Invalid main Account ID',
                'availability_window'   => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $availability_window = $this->easel_service->fetch_availability_window($account_id, $id);

        if (!empty($availability_window)) {
            $message = [
                'status'    => true,
                'http_code' => REST_Controller::HTTP_OK,
                'message'   => $this->session->flashdata('message'),
                'availability_window'   => (!empty($availability_window->records)) ? $availability_window->records : (!empty($availability_window) ? $availability_window : null),
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'    => false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message'   => 'No records found',
                'availability_window'   => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get Availability Window(s) data from Easel using the product ID
    **/
    public function fetch_availability_window_by_product_get()
    {
        $account_id     = (int) $this->get('account_id');
        $id             = $this->get('id');

        $expected_data = [
            'account_id'    => $account_id ,
            'id'            => $id
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('id', 'Product ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'                    => false,
                'http_code'                 => REST_Controller::HTTP_BAD_REQUEST,
                'message'                   => 'Invalid or missing Field(s)',
                'availability_windows'      => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'               => 'Invalid main Account ID',
                'availability_windows'  => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $availability_windows = $this->easel_service->fetch_availability_window_by_product($account_id, $id);

        if (!empty($availability_windows)) {
            $message = [
                'status'                => true,
                'http_code'             => REST_Controller::HTTP_OK,
                'message'               => $this->session->flashdata('message'),
                'availability_windows'  => (!empty($availability_windows->data)) ? $availability_windows->data : (!empty($availability_windows) ? $availability_windows : null),
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_NO_CONTENT,
                'message'               => 'No records found',
                'availability_windows'  => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get Availability Window(s) data from CaCTi using the Content ID
    **/
    public function fetch_availability_window_from_cacti_get()
    {
        $account_id     = (int) $this->get('account_id');
        $content_id     = $this->get('content_id');

        $expected_data = [
            'account_id'    => $account_id ,
            'content_id'    => $content_id
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('content_id', 'Content ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'                    => false,
                'http_code'                 => REST_Controller::HTTP_BAD_REQUEST,
                'message'                   => 'Invalid or missing Field(s)',
                'availability_windows'      => null
            ];
            $message['message'] = 'Validation errors: ' . trim($validation_errors) . trim($message['message']);
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'               => 'Invalid main Account ID',
                'availability_windows'  => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $availability_windows = $this->easel_service->fetch_availability_window_from_cacti($account_id, $content_id);

        if (!empty($availability_windows)) {
            $message = [
                'status'                => true,
                'http_code'             => REST_Controller::HTTP_OK,
                'message'               => $this->session->flashdata('message'),
                'availability_windows'  => $availability_windows,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_NO_CONTENT,
                'message'               => 'No records found',
                'availability_windows'  => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Create new Segment
    */
    public function create_segment_post()
    {
        $segment_data   = $this->post();
        $account_id     = (int) $this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('name', 'Segment Name', 'required');
        $this->form_validation->set_rules('type', 'Segment Type', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_BAD_REQUEST,
                'message'       => 'Invalid data: ',
                'segment'       => null
            ];

            $message['message'] = (!$account_id) ? $message['message'] . 'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'       => 'Invalid main Account ID.',
                'segment'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
        $new_segment = $this->easel_service->create_segment($account_id, $segment_data);

        if (!empty($new_segment->success)) {
            $message = [
                'status'    => true,
                'http_code' => REST_Controller::HTTP_CREATED,
                'message'   => $this->session->flashdata('message'),
                'segment'   => !empty($new_segment->data) ? $new_segment->data : $new_segment
            ];
            $this->response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'    => false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message'   => (!empty($new_segment->message)) ? $new_segment->message : $this->session->flashdata('message'),
                'segment'   => $new_segment
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    *   Get Segment data from Easel
    **/
    public function fetch_segment_get()
    {
        $account_id     = (int) $this->get('account_id');
        $id             = $this->get('id');
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'       => 'Invalid main Account ID',
                'segment'       => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $segment = $this->easel_service->fetch_segment($account_id, $id);

        if (!empty($segment)) {
            $message = [
                'status'    => true,
                'http_code' => REST_Controller::HTTP_OK,
                'message'   => $this->session->flashdata('message'),
                'segment'   => (!empty($segment->records)) ? $segment->records : ($segment),
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'    => false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message'   => 'No records found',
                'segment'   => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Delete Segment data from Easel
    **/
    public function delete_segment_post()
    {
        $account_id     = (int) $this->post('account_id');
        $id             = $this->post('id');
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'       => 'Invalid main Account ID',
                'segment'       => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $segment = $this->easel_service->delete_segment($account_id, $id);

        if (!empty($segment)) {
            $message = [
                'status'    => true,
                'http_code' => REST_Controller::HTTP_OK,
                'message'   => $this->session->flashdata('message'),
                'segment'   => (!empty($segment->records)) ? $segment->records : ($segment),
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'    => false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message'   => 'No records found',
                'segment'   => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Create Market
    */
    public function create_market_post()
    {
        $market_data    = $this->post();
        $account_id     = (int) $this->post('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('name', 'Market Name', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_BAD_REQUEST,
                'message'       => 'Invalid data: ',
                'market'        => null
            ];

            $message['message'] = (!$account_id) ? $message['message'] . 'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'       => 'Invalid main Account ID.',
                'market'        => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
        $new_market = $this->easel_service->create_market($account_id, $market_data);

        if (!empty($new_market->success)) {
            $message = [
                'status'    => true,
                'http_code' => REST_Controller::HTTP_CREATED,
                'message'   => $this->session->flashdata('message'),
                'market'    => !empty($new_market->data) ? $new_market->data : $new_market
            ];
            $this->response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'    => false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message'   => (!empty($new_market->message)) ? $new_market->message : $this->session->flashdata('message'),
                'market'    => $new_market
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get Market data from Easel
    **/
    public function fetch_market_get()
    {
        $account_id     = (int) $this->get('account_id');
        $id             = $this->get('id');
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'       => 'Invalid main Account ID',
                'market'        => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $market = $this->easel_service->fetch_market($account_id, $id);

        if (!empty($market)) {
            $message = [
                'status'    => true,
                'http_code' => REST_Controller::HTTP_OK,
                'message'   => $this->session->flashdata('message'),
                'market'    => (!empty($market->records)) ? $market->records : ($market),
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'    => false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message'   => 'No records found',
                'market'    => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Create new Device
    */
    public function create_device_post()
    {
        $device_data    = $this->post();
        $account_id     = (!empty($this->post('account_id'))) ? (int) $this->post('account_id') : false ;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('platform', 'Platform', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_BAD_REQUEST,
                'message'       => (!$account_id) ? "Invalid Account ID" : 'Validation errors: ' . $validation_errors,
                'device'        => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'       => 'Invalid main Account ID.',
                'device'        => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
        $new_device = $this->easel_service->create_device($account_id, $device_data);

        if (!empty($new_device->success)) {
            $message = [
                'status'    => true,
                'http_code' => REST_Controller::HTTP_CREATED,
                'message'   => $this->session->flashdata('message'),
                'device'    => !empty($new_device->data) ? $new_device->data : $new_device
            ];
            $this->response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'    => false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message'   => (!empty($new_device->message)) ? $new_device->message : $this->session->flashdata('message'),
                'device'    => $new_device
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Add a Device to a Segment on Airtime (Easel)
    */
    public function add_device_to_segment_post()
    {
        $account_id             = (!empty($this->post('account_id'))) ? (int) $this->post('account_id') : false ;
        $device_external_id     = (!empty($this->post('device_external_id'))) ? $this->post('device_external_id') : false ;
        $segment_external_id    = (!empty($this->post('segment_external_id'))) ? $this->post('segment_external_id') : false ;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('device_external_id', 'Device External ID', 'required');
        $this->form_validation->set_rules('segment_external_id', 'Segment External ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            ## One of the required fields is invalid
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_BAD_REQUEST,
                'message'       => (!$account_id) ? "Invalid Account ID" : 'Validation errors: ' . $validation_errors,
                'added_device'  => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'       => 'Invalid main Account ID.',
                'added_device'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
        $added_device = $this->easel_service->add_device_to_segment($account_id, $device_external_id, $segment_external_id);

        if (!empty($added_device->success)) {
            $message = [
                'status'        => true,
                'http_code'     => REST_Controller::HTTP_CREATED,
                'message'       => $this->session->flashdata('message'),
                'added_device'  => !empty($added_device->data) ? $added_device->data : $added_device
            ];
            $this->response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_NO_CONTENT,
                'message'       => (!empty($new_device->message)) ? $new_device->message : $this->session->flashdata('message'),
                'added_device'  => $new_device
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Update Segment
    */
    public function update_segment_post()
    {
        $post_data                              = $this->post();
        $account_id                             = (!empty($post_data['account_id'])) ? (int) $post_data['account_id'] : '' ;
        $segment_data                           = [];
        $segment_data['airtime_segment_ref']    = (!empty($post_data['airtime_segment_ref'])) ? (string) $post_data['airtime_segment_ref'] : '' ;
        $segment_data['type']                   = (!empty($post_data['type'])) ? (string) $post_data['type'] : '' ;
        $segment_data['name']                   = (!empty($post_data['name'])) ? (string) $post_data['name'] : '' ;
        $segment_data['description']            = (!empty($post_data['description'])) ? (string) $post_data['description'] : '' ;
        $segment_data['deviceList']             = (!empty($post_data['deviceList'])) ? (array) $post_data['deviceList'] : [] ;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('airtime_segment_ref', 'Airtime Segment Reference', 'required');
        $this->form_validation->set_rules('type', 'Segment Type', 'required');
        $this->form_validation->set_rules('name', 'Name', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (!$account_id || (isset($validation_errors) && !empty($validation_errors))) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_BAD_REQUEST,
                'message'       => 'Invalid data: ',
                'segment'       => null
            ];

            $message['message'] = (!$account_id) ? $message['message'] . 'account_id, ' : $message['message'];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: ' . $validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'       => 'Invalid main Account ID.',
                'segment'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
        $updated_segment = $this->easel_service->update_segment($account_id, $segment_data);

        if (!empty($updated_segment->success)) {
            $message = [
                'status'    => true,
                'http_code' => REST_Controller::HTTP_CREATED,
                'message'   => $this->session->flashdata('message'),
                'segment'   => !empty($updated_segment->data) ? $updated_segment->data : $updated_segment
            ];
            $this->response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'    => false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message'   => (!empty($updated_segment->message)) ? $updated_segment->message : $this->session->flashdata('message'),
                'segment'   => $updated_segment
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Create new Image
    */
    public function create_image_post()
    {
        $image_data     = $this->post();
        $account_id     = (!empty($this->post('account_id'))) ? (int) $this->post('account_id') : false ;
        $url            = (!empty($this->post('url'))) ? $this->post('url') : false ;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('url', 'URL of the Image', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_BAD_REQUEST,
                'message'       => 'Validation errors: ' . $validation_errors,
                'image'         => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if ((!$account_id) || ((int) $account_id < 1) || (!$this->account_service->check_account_status($account_id))) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'       => 'Invalid main Account ID.',
                'image'         => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
        $new_image = $this->easel_service->create_image($account_id, $image_data);

        if (!empty($new_image->success)) {
            $message = [
                'status'        => true,
                'http_code'     => REST_Controller::HTTP_CREATED,
                'message'       => $this->session->flashdata('message'),
                'image'         => !empty($new_image->data) ? $new_image->data : $new_image
            ];
            $this->response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_NO_CONTENT,
                'message'       => (!empty($new_image->message)) ? $new_image->message : $this->session->flashdata('message'),
                'image'         => $new_image
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }




    /**
    *   Create new Subtitle against the VoD media profile
    */
    public function create_subtitle_post()
    {
        $subtitle_data  = $this->post();
        $account_id     = (!empty($this->post('account_id'))) ? (int) $this->post('account_id') : false ;
        $url            = (!empty($this->post('url'))) ? $this->post('url') : false ;
        $vodMediaId     = (!empty($this->post('vodMediaId'))) ? $this->post('vodMediaId') : false ;
        $language       = (!empty($this->post('language'))) ? $this->post('language') : false ;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('url', 'Subtitle URL', 'required');
        $this->form_validation->set_rules('vodMediaId', 'VoD Media ID', 'required');
        $this->form_validation->set_rules('language', 'Language', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_BAD_REQUEST,
                'message'       => 'Validation errors: ' . $validation_errors,
                'subtitle'      => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if ((!$account_id) || ((int) $account_id < 1) || (!$this->account_service->check_account_status($account_id))) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'       => 'Invalid main Account ID.',
                'subtitle'      => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_subtitle = $this->easel_service->create_subtitle($account_id, $subtitle_data);

        if (!empty($new_subtitle->success)) {
            $message = [
                'status'        => true,
                'http_code'     => REST_Controller::HTTP_CREATED,
                'message'       => $this->session->flashdata('message'),
                'subtitle'      => !empty($new_subtitle->data) ? $new_subtitle->data : $new_subtitle
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_NO_CONTENT,
                'message'       => (!empty($new_subtitle->message)) ? $new_subtitle->message : $this->session->flashdata('message'),
                'subtitle'      => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   This Webhook is to inform CaCTi that the specific action has been completed on Easel side
    *   - add a security token
    */
    public function webhook_post()
    {
        $post               = $this->post();
        $request_data       = false;

        if (is_array($post)) {
            $request_data = $post;
        } elseif (is_object(json_decode($post))) {
            $request_data = convert_to_array($post);
        } else {
            ## Post request object wasn't either an array or an object
        }

        if (!$request_data || empty($request_data)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_BAD_REQUEST,
                'message'       => "Unknown structure of the request/Bad request",
                'webhook'       => false
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $this->form_validation->set_data($request_data);
        $this->form_validation->set_rules('event', 'Event', 'required');
        $this->form_validation->set_rules('data[vodMedia][id]', 'Media ID', 'required');
        $this->form_validation->set_rules('data[vodMedia][name]', 'Media Name', 'required');
        $this->form_validation->set_rules('data[vodMedia][encodingStatus]', 'Encoding Status', 'required');

        $feature = false;
        $feature = (isset($request_data['data']['vodMedia']['feature'])) ? true : false ;

        if (!$feature) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_BAD_REQUEST,
                'message'       => 'Validation errors: The Media Feature field is required',
                'webhook'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_BAD_REQUEST,
                'message'       => 'Validation errors: ' . $validation_errors,
                'webhook'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $webhook = $this->easel_service->webhook($request_data);

        if (!empty($webhook->success)) {
            $message = [
                'status'        => true,
                'http_code'     => REST_Controller::HTTP_OK,
                'message'       => (!empty($webhook->message)) ? $webhook->message : "Operation Successful" ,
                'webhook'       => !empty($webhook->data) ? $webhook->data : (object) [],
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_NO_CONTENT,
                'message'       => (!empty($webhook->message)) ? $webhook->message : "Operation Failed" ,
                'webhook'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   To submit the VoD Media file for encoding
    */
    public function start_encoding_post()
    {
        $post_data      = $this->post();
        $account_id     = (!empty($post_data['account_id'])) ? (int) $post_data['account_id'] : false ;
        $vod_media_id   = (!empty($post_data['vod_media_id'])) ? $post_data['vod_media_id'] : false ;
        $quality        = (!empty($post_data['quality'])) ? $post_data['quality'] : 'hd' ;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('vod_media_id', 'VoD Media ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_BAD_REQUEST,
                'message'       => 'Validation errors: ' . $validation_errors,
                'vod_media'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if ((!$account_id) || ((int) $account_id < 1) || (!$this->account_service->check_account_status($account_id))) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'       => 'Invalid main Account ID.',
                'vod_media'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $start_encoding = $this->easel_service->start_encoding($account_id, $post_data);

        if (!empty($start_encoding->success)) {
            $message = [
                'status'        => true,
                'http_code'     => REST_Controller::HTTP_CREATED,
                'message'       => (!empty($start_encoding->message)) ? $start_encoding->message : $this->session->flashdata('message'),
                'vod_media'     => !empty($start_encoding->data) ? $start_encoding->data : $start_encoding
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_NO_CONTENT,
                'message'       => (!empty($start_encoding->message)) ? $start_encoding->message : $this->session->flashdata('message'),
                'vod_media'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
