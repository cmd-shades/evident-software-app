<?php

namespace App\Controllers\REST\Api;

use App\Adapter\RESTController;
use App\Libraries\REST_Controller;
use App\Models\Service\ContentModel;

final class ContentController extends RESTController
{
	/**
	 * @var \Application\Modules\Service\Controllers\Api\ContentModel
	 */
	private $content_service;

	public function __construct()
    {
        parent::__construct();
        $this->load->library("Ssid_common");
        $this->load->library("form_validation");
        $this->load->library("email");
        $this->content_service = new ContentModel();
    }

    /**
    *   Get content restriction type(s)
    */
    public function restriction_types_get()
    {
        $get_set        = $this->get();

        $account_id     = (!empty($get_set['account_id'])) ? $get_set['account_id'] : false;
        $type_id        = (!empty($get_set['type_id'])) ? $get_set['type_id'] : false;
        $where          = (!empty($get_set['where'])) ? $get_set['where'] : false;
        $unorganized    = (!empty($get_set['unorganized'])) ? $get_set['unorganized'] : false;
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
                'message'           => 'Validation errors: ' . $validation_errors,
                'restrictions'      => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'restrictions'      => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $restrictions = $this->content_service->get_restriction_types($account_id, $type_id, $where, $unorganized, $limit, $offset);

        if (!empty($restrictions)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'restrictions'      => $restrictions
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'restrictions'      => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get Territory(ies)
    */
    public function territories_get()
    {
        $get_set        = $this->get();

        $account_id     = (!empty($get_set['account_id'])) ? $get_set['account_id'] : false;
        $territory_id   = (!empty($get_set['territory_id'])) ? $get_set['territory_id'] : false;
        $where          = (!empty($get_set['where'])) ? $get_set['where'] : false;
        $unorganized    = (!empty($get_set['unorganized'])) ? $get_set['unorganized'] : false;
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
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'territories'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'territories'   => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $territories = $this->content_service->get_territories($account_id, $territory_id, $where, $unorganized, $limit, $offset);

        if (!empty($territories)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'territories'   => $territories
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'territories'   => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    *   Create new Content
    */
    public function create_post()
    {
        $post_set       = $this->post();

        $account_id     = (!empty($post_set['account_id'])) ? $post_set['account_id'] : false;
        $content_data   = (!empty($post_set['content_data'])) ? $post_set['content_data'] : false;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('content_data', 'Content Data', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'new_content'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid main Account ID',
                'new_content'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_content = $this->content_service->create_content($account_id, $content_data);

        if (!empty($new_content)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'new_content'   => $new_content
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'new_content'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get Content
    */
    public function content_get()
    {
        $get_set        = $this->get();

        $account_id     = (!empty($get_set['account_id'])) ? $get_set['account_id'] : false;
        $content_id     = (!empty($get_set['content_id'])) ? $get_set['content_id'] : false;
        $where          = (!empty($get_set['where'])) ? $get_set['where'] : false;
        $unorganized    = (!empty($get_set['unorganized'])) ? $get_set['unorganized'] : false;
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
                'status'    => false,
                'message'   => 'Validation errors: ' . $validation_errors,
                'content'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'    => false,
                'message'   => $this->session->flashdata('message'),
                'content'   => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $content = $this->content_service->get_content($account_id, $content_id, $where, $unorganized, $limit, $offset);

        if (!empty($content)) {
            $message = [
                'status'    => true,
                'message'   => $this->session->flashdata('message'),
                'content'   => $content
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'    => false,
                'message'   => $this->session->flashdata('message'),
                'content'   => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Content Lookup
    */
    public function lookup_get()
    {
        $get_data = $this->get();

        $account_id     = (!empty($get_data['account_id'])) ? (int) $get_data['account_id'] : '' ;
        $limit          = (!empty($get_data['limit']) && ($get_data['limit'] > 0)) ? (int) $get_data['limit'] : '' ;
        $offset         = (!empty($get_data['offset'])) ? (int) $get_data['offset'] : '' ;
        $where          = (!empty($get_data['where'])) ? $get_data['where'] : '' ;
        $order_by       = (!empty($get_data['order_by'])) ? $get_data['order_by'] : '' ;
        $search_term    = (!empty($get_data['search_term'])) ? trim(urldecode($get_data['search_term'])) : '' ;


        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'    => false,
                'message'   => 'Invalid main Account ID.',
                'content'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $content_lookup = $this->content_service->content_lookup($account_id, $search_term, $where, $order_by, $limit, $offset);

        if (!empty($content_lookup)) {
            $message = [
                'status'    => true,
                'message'   => $this->session->flashdata('message'),
                'content'   => $content_lookup
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'    => false,
                'message'   => $this->session->flashdata('message'),
                'content'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Delete Content
    */
    public function delete_post()
    {
        $post_data = $this->post();
        $account_id     = (!empty($post_data['account_id'])) ? (int) $post_data['account_id'] : false ;
        $content_id     = (!empty($post_data['content_id'])) ? (int) $post_data['content_id'] : false ;

        if ($content_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'    => false,
                'message'   => 'Invalid main Account ID',
                'd_content' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $content_exists = $this->content_service->get_content($account_id, $content_id);

        if (!$content_exists) {
            $message = [
                'status'        => false,
                'message'       => 'Incorrect Contact ID',
                'd_content'     => false
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $d_content = $this->content_service->delete_content($account_id, $content_id);

        if (!empty($d_content)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'd_content'     => true
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'd_content'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Update Content
    */
    public function update_post()
    {
        $post_set       = $this->post();

        $account_id     = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $content_id     = (!empty($post_set['content_id'])) ? (int) $post_set['content_id'] : false;
        $content_data   = (!empty($post_set['content_data'])) ? $post_set['content_data'] : false;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('content_id', 'Content ID', 'required');
        $this->form_validation->set_rules('content_data', 'Content Data', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'u_content'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid main Account ID',
                'u_content'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $content_exists = $this->content_service->get_content($account_id, $content_id);

        if (!$content_exists) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid Content ID',
                'u_content'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $u_content = $this->content_service->update_content($account_id, $content_id, $content_data);

        if (!empty($u_content)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'u_content'     => $u_content
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'u_content'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    public function imdb_search_post()
    {
        $post_set       = $this->post();

        $account_id     = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $search_title   = (!empty($post_set['search_title'])) ? $post_set['search_title'] : false;
        $where          = (!empty($post_set['where'])) ? $post_set['where'] : false;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('search_title', 'Search Title', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'imdb_title'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid main Account ID',
                'imdb_title'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $imdb_title = $this->content_service->imdb_search($account_id, $search_title, $where);

        if (!empty($imdb_title)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'imdb_title'    => $imdb_title
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'imdb_title'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Create Add new Clearance info
    */
    public function add_clearance_post()
    {
        $post_set               = $this->post();
        $account_id             = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $content_id             = (!empty($post_set['content_id'])) ? $post_set['content_id'] : false;                          ## could be an array
        $clearance_start_date   = (!empty($post_set['clearance_start_date'])) ? $post_set['clearance_start_date'] : false;      ## always single date
        $territories            = (!empty($post_set['territories'])) ? $post_set['territories'] : false;                        ## always as an array - even if only one item

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('content_id', 'Content ID(s)', 'required');
        $this->form_validation->set_rules('clearance_start_date', 'Clearance Date', 'required');
        $this->form_validation->set_rules('territories', 'Territory(ies)', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Validation errors: ' . $validation_errors,
                'new_clearance'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => 'Invalid main Account ID',
                'new_clearance'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_clearance = $this->content_service->add_clearance($account_id, $content_id, $clearance_start_date, $territories);

        if (!empty($new_clearance)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'new_clearance'     => $new_clearance
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'new_clearance'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get Clearance Dates
    */
    public function clearance_get()
    {
        $get_set        = $this->get();

        $account_id     = (!empty($get_set['account_id'])) ? $get_set['account_id'] : false;
        $clearance_id   = (!empty($get_set['clearance_id'])) ? $get_set['clearance_id'] : false;
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
                'status'    => false,
                'message'   => 'Validation errors: ' . $validation_errors,
                'clarance'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'    => false,
                'message'   => $this->session->flashdata('message'),
                'clarance'  => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $clarance = $this->content_service->get_clearance($account_id, $clearance_id, $where, $limit, $offset);

        if (!empty($clarance)) {
            $message = [
                'status'    => true,
                'message'   => $this->session->flashdata('message'),
                'clarance'  => $clarance
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'    => false,
                'message'   => $this->session->flashdata('message'),
                'clarance'  => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Add Clearance Batch
    */
    public function add_batch_clearance_post()
    {
        $post_set       = $this->post();

        $account_id         = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $batch_clearance    = (!empty($post_set['batch_clearance'])) ? $post_set['batch_clearance'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('batch_clearance', 'Clearance Batch', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Validation errors: ' . $validation_errors,
                'clarance_batch'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'clarance_batch'    => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $clarance_batch = $this->content_service->add_clearance_batch($account_id, $batch_clearance);

        if (!empty($clarance_batch)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'clarance_batch'    => $clarance_batch
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'clarance_batch'    => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    public function remove_clearance_from_tmp_post()
    {
        $post_set       = $this->post();

        $account_id         = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $batch_clearance    = (!empty($post_set['batch_clearance'])) ? $post_set['batch_clearance'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('batch_clearance', 'Clearance Batch', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Validation errors: ' . $validation_errors,
                'clarance_removed'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'clarance_removed'  => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $clarance_removed = $this->content_service->remove_clearance_from_tmp($account_id, $batch_clearance);

        if (!empty($clarance_removed)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'clarance_removed'  => $clarance_removed
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'clarance_removed'  => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   Delete clearance from the content
    */
    public function delete_clearance_post()
    {
        $post_set           = $this->post();

        $account_id         = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $clearance_id       = (!empty($post_set['clearance_id'])) ? $post_set['clearance_id'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('clearance_id', 'Clearance ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Validation errors: ' . $validation_errors,
                'd_clearance'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'd_clearance'       => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $d_clearance     = false;
        $d_clearance     = $this->content_service->delete_clearance($account_id, $clearance_id);

        if (($d_clearance != false)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'd_clearance'       => $d_clearance
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'd_clearance'       => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    *   Get Language Text
    */
    public function language_phrase_get()
    {
        $get_set        = $this->get();

        $account_id     = (!empty($get_set['account_id'])) ? $get_set['account_id'] : false;
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
                'message'           => 'Validation errors: ' . $validation_errors,
                'language_phrase'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'language_phrase'   => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $language_phrase = $this->content_service->get_language_phrase($account_id, $where, $limit, $offset);

        if (!empty($language_phrase)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'language_phrase'   => $language_phrase
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'language_phrase'   => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get phrase Languages List
    */
    public function phrase_languages_get()
    {
        $get_set        = $this->get();

        $account_id     = (!empty($get_set['account_id'])) ? $get_set['account_id'] : false;
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
                'message'           => 'Validation errors: ' . $validation_errors,
                'phrase_languages'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'phrase_languages'  => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $phrase_languages = $this->content_service->get_phrase_languages($account_id, $where, $limit, $offset);

        if (!empty($phrase_languages)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'phrase_languages'  => $phrase_languages
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'phrase_languages'  => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Get phrase Type of the Phrase List
    */
    public function phrase_types_get()
    {
        $get_set        = $this->get();

        $account_id     = (!empty($get_set['account_id'])) ? $get_set['account_id'] : false;
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
                'message'           => 'Validation errors: ' . $validation_errors,
                'phrase_types'      => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'phrase_types'      => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $phrase_types = $this->content_service->get_phrase_types($account_id, $where, $limit, $offset);

        if (!empty($phrase_types)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'phrase_types'      => $phrase_types
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'phrase_types'      => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   Function to insert/update language phrases
    */
    public function update_language_phrase_post()
    {
        $post_set       = $this->post();

        $account_id     = (!empty($post_set['account_id'])) ? $post_set['account_id'] : false;
        $phrases_data   = (!empty($post_set['phrases_data'])) ? $post_set['phrases_data'] : false;
        $content_id     = (!empty($post_set['content_id'])) ? $post_set['content_id'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('content_id', 'Content ID', 'required');
        $this->form_validation->set_rules('phrases_data', 'Phrases Data', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Validation errors: ' . $validation_errors,
                'phrases'           => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'phrases'           => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $phrases = $this->content_service->update_language_phrase($account_id, $content_id, $phrases_data);

        if (!empty($phrases)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'phrases'           => $phrases
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'phrases'           => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /*
    *   Function to generate the output file. For now it is only for JSON and XML type.
    */
    public function generate_file_export_post()
    {
        $post_set       = $this->post();

        $account_id     = (!empty($post_set['account_id'])) ? $post_set['account_id'] : false;
        $content_id     = (!empty($post_set['content_id'])) ? $post_set['content_id'] : false;
        $file_type      = (!empty($post_set['file_type'])) ? $post_set['file_type'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('content_id', 'Content ID', 'required');
        $this->form_validation->set_rules('file_type', 'File Type', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Validation errors: ' . $validation_errors,
                'export'            => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'export'            => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $export = $this->content_service->generate_file_export($account_id, $content_id, $file_type);

        if (!empty($export)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'export'            => $export
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'export'            => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /*
    *   Function to decode information from the movie file. Path to the file needs to be provided.
    */
    public function decode_movie_file_post()
    {
        $post_set           = $this->input->post();

        $account_id         = (!empty($post_set['account_id'])) ? $post_set['account_id'] : false;
        $content_id         = (!empty($post_set['content_id'])) ? $post_set['content_id'] : false;
        $file_location      = (!empty($post_set['file_location'])) ? $post_set['file_location'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('content_id', 'Content ID', 'required');
        $this->form_validation->set_rules('file_location', 'File Location', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'            => false,
                'message'           => 'Validation errors: ' . $validation_errors,
                'decoded_file_info' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'decoded_file_info' => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $decoded_file_info = $this->content_service->decode_file_streams($account_id, $content_id, $file_location);

        if (!empty($decoded_file_info)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'decoded_file_info' => $decoded_file_info
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'decoded_file_info'             => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }




    /**
    *   Get Decoded Stream information for the specific content
    */
    public function decoded_file_streams_get()
    {
        $get_set        = $this->get();

        $account_id     = (!empty($get_set['account_id'])) ? $get_set['account_id'] : false;
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
                'status'                => false,
                'message'               => 'Validation errors: ' . $validation_errors,
                'decoded_file_streams'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'decoded_file_streams'  => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $decoded_file_streams = $this->content_service->get_decoded_file_streams($account_id, $where);

        if (!empty($decoded_file_streams)) {
            $message = [
                'status'                => true,
                'message'               => $this->session->flashdata('message'),
                'decoded_file_streams'  => $decoded_file_streams
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'decoded_file_streams'  => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Add new Territory
    */
    public function add_territory_post()
    {
        $post_set           = $this->post();

        $account_id         = (!empty($post_set['account_id'])) ? $post_set['account_id'] : false;
        $territory_data     = (!empty($post_set['territory_data'])) ? $post_set['territory_data'] : false;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('territory_data', 'Territory Data', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'new_territory' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid main Account ID',
                'new_territory' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_territory = $this->content_service->add_territory($account_id, $territory_data);

        if (!empty($new_territory)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'new_territory' => $new_territory
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'new_territory' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }




    /**
    *   Delete Territory
    */
    public function delete_territory_post()
    {
        $post_data = $this->post();
        $account_id     = (!empty($post_data['account_id'])) ? (int) $post_data['account_id'] : false ;
        $territory_id   = (!empty($post_data['territory_id'])) ? (int) $post_data['territory_id'] : false ;

        if ($territory_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid main Account ID',
                'd_territory'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $territory_exists = $this->content_service->get_territories($account_id, $territory_id);

        if (!$territory_exists) {
            $message = [
                'status'        => false,
                'message'       => 'Incorrect Territory ID',
                'd_territory'   => false
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $d_territory = $this->content_service->delete_territory($account_id, $territory_id);

        if (!empty($d_territory)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'd_territory'   => true
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'd_territory'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Update Territory
    */
    public function update_territory_post()
    {
        $post_set       = $this->post();

        $account_id         = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $territory_id       = (!empty($post_set['territory_id'])) ? (int) $post_set['territory_id'] : false;
        $territory_data     = (!empty($post_set['territory_data'])) ? $post_set['territory_data'] : false;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('territory_id', 'Territory ID', 'required');
        $this->form_validation->set_rules('territory_data', 'Territory Data', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'u_territory'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid main Account ID',
                'u_territory'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $territory_exists = $this->content_service->get_territories($account_id, $territory_id);

        if (!$territory_exists) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid Territory ID',
                'u_territory'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $u_territory = $this->content_service->update_territory($account_id, $territory_id, $territory_data);

        if (!empty($u_territory)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'u_territory'   => $u_territory
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'u_territory'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Get Content For Distribution Bundles
    */
    public function distribution_content_get()
    {
        $get_data = $this->get();

        $account_id     = (!empty($get_data['account_id'])) ? (int) $get_data['account_id'] : false ;
        $content_id         = (!empty($get_data['content_id'])) ? (int) $get_data['content_id'] : false ;
        $limit          = (!empty($get_data['limit']) && ($get_data['limit'] > 0)) ? (int) $get_data['limit'] : '' ;
        $offset         = (!empty($get_data['offset'])) ? (int) $get_data['offset'] : '' ;
        $where          = (!empty($get_data['where'])) ? $get_data['where'] : '' ;
        $order_by       = (!empty($get_data['order_by'])) ? $get_data['order_by'] : '' ;
        $search_term    = (!empty($get_data['search_term'])) ? trim(urldecode($get_data['search_term'])) : '' ;


        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'    => false,
                'message'   => 'Invalid main Account ID.',
                'content'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $content_lookup = $this->content_service->distribution_content($account_id, $content_id, $where, $order_by, $limit, $offset);

        if (!empty($content_lookup)) {
            $message = [
                'status'    => true,
                'message'   => $this->session->flashdata('message'),
                'content'   => $content_lookup
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'    => false,
                'message'   => $this->session->flashdata('message'),
                'content'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    *   Get content/film attributes
    */
    public function content_attributes_get()
    {
        $postdata       = $this->get();

        $account_id     = (!empty($postdata['account_id'])) ? $postdata['account_id'] : false;
        $content_id     = (!empty($postdata['content_id'])) ? $postdata['content_id'] : false;
        $where          = (!empty($postdata['where'])) ? $postdata['where'] : false;

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
                'message'           => 'Validation errors: ' . $validation_errors,
                'content_attributes' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'content_attributes' => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $content_attributes = $this->content_service->_fetch_content_attributes($account_id, $content_id, false, $where);

        if (!empty($content_attributes)) {
            $message = [
                'status'            => true,
                'message'           => $this->session->flashdata('message'),
                'content_attributes' => $content_attributes
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'            => false,
                'message'           => $this->session->flashdata('message'),
                'content_attributes' => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   This function will create all missing availability windows for given Content ID
    */
    public function synchronize_availability_windows_get()
    {
        $get_set        = $this->get();

        $account_id     = (!empty($get_set['account_id'])) ? (int) $get_set['account_id'] : false;
        $content_id     = (!empty($get_set['content_id'])) ? (int) $get_set['content_id'] : false;
        $where          = (!empty($get_set['where'])) ? $get_set['where'] : false;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('content_id', 'Content ID', 'required');

        $expected_data = [
            'account_id'    => $account_id ,
            'content_id'    => $content_id ,
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('content_id', 'Content ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'                => false,
                'message'               => 'Validation errors: ' . $validation_errors,
                'availability_windows'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'message'               => 'Invalid main Account ID',
                'availability_windows'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $content_exists = $this->content_service->get_content($account_id, $content_id);

        if (!$content_exists) {
            $message = [
                'status'                => false,
                'message'               => 'Invalid Content ID',
                'availability_windows'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $availability_windows = $this->content_service->synchronize_availability_windows($account_id, $content_id, $where);

        if (!empty($availability_windows)) {
            $message = [
                'status'                => true,
                'message'               => $this->session->flashdata('message'),
                'availability_windows'  => $availability_windows
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'availability_windows'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }





    /**
    *   Create new Genre Type ( Category Type )
    *   - this is successful only if successful on Easel and CaCTi
    */
    public function genre_type_post()
    {
        $post_set       = $this->post();

        $genre_type_data    = $post_set;
        $account_id         = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $genre_type_name    = (!empty($post_set['genre_type_name'])) ? $post_set['genre_type_name'] : false;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('genre_type_name', 'Genre Type name', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'genre_type'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        //Check and verify that main account is valid
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid main Account ID',
                'genre_type'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $genre_type = $this->content_service->create_genre_type($account_id, $genre_type_name, $genre_type_data);

        if (!empty($genre_type)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'genre_type'    => $genre_type
            ];
            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'genre_type'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Create new Genre item ( Category )
    *   - as above: successful only if successful on Easel AND on CaCTi
    */
    public function genre_post()
    {
        $post_set           = $this->post();

        $genre_data         = $post_set;
        $account_id         = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $genre_name         = (!empty($post_set['genre_name'])) ? $post_set['genre_name'] : false;
        $genre_type_id      = (!empty($post_set['genre_type_id'])) ? $post_set['genre_type_id'] : false;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('genre_name', 'Genre name', 'required');
        $this->form_validation->set_rules('genre_type_id', 'Genre Type ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'genre'         => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid main Account ID',
                'genre'         => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $genre = $this->content_service->create_genre($account_id, $genre_name, $genre_type_id);

        if (!empty($genre)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'genre'         => $genre
            ];
            $this->response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'genre'         => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Get Genre Types
    */
    public function genre_types_get()
    {
        $postdata       = $this->get();

        $account_id     = (!empty($postdata['account_id'])) ? (int) $postdata['account_id'] : false;
        $where          = (!empty($postdata['where'])) ? $postdata['where'] : false;

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
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'genre_types'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'genre_types'   => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $genre_types = $this->content_service->get_genre_types($account_id, $where);

        if (!empty($genre_types)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'genre_types'   => $genre_types
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'genre_types'   => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get Genre(s)
    */
    public function genres_get()
    {
        $postdata       = $this->get();

        $account_id     = (!empty($postdata['account_id'])) ? (int) $postdata['account_id'] : false;
        $where          = (!empty($postdata['where'])) ? $postdata['where'] : false;

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
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'genres'        => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'genres'        => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $genres = $this->content_service->get_genres($account_id, $where);

        if (!empty($genres)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'genres'        => $genres
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'genres'        => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    public function media_to_airtime_post()
    {
        $post_set           = $this->post();

        $account_id         = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $content_id         = (!empty($post_set['content_id'])) ? (int) $post_set['content_id'] : false;
        $action             = (!empty($post_set['action'])) ? $post_set['action'] : false;
        $mediadata          = (!empty($post_set['data'])) ? $post_set['data'] : false;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('content_id', 'Content ID', 'required');
        $this->form_validation->set_rules('action', 'Action', 'required');
        $this->form_validation->set_rules('data', 'Media Data', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'media'         => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid main Account ID',
                'media'         => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $media = $this->content_service->media_to_airtime($account_id, $content_id, $action, $mediadata);

        if (!empty($media)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'media'         => $media
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'media'         => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }




    public function media_to_aws_post()
    {
        $post_set           = $this->post();

        $account_id         = (!empty($post_set['account_id'])) ? (int) $post_set['account_id'] : false;
        $content_id         = (!empty($post_set['content_id'])) ? (int) $post_set['content_id'] : false;
        $movie_data         = (!empty($post_set['movie_data'])) ? $post_set['movie_data'] : false;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('content_id', 'Content ID', 'required');
        $this->form_validation->set_rules('movie_data', 'Video Movies', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'media'         => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid main Account ID',
                'media'         => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $media = $this->content_service->media_to_aws($account_id, $content_id, $movie_data);

        if (($media->success != false) && (!empty($media->data))) {
            $message = [
                'status'        => true,
                'message'       => (!empty($media->message)) ? $media->message : ((!empty($this->session->flashdata('message'))) ? $this->session->flashdata('message') : 'Media sent'),
                'media'         => $media->data
            ];
            $this->response($message, REST_Controller::HTTP_CREATED);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'media'         => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Get Age Classifications (only one for now)
    */
    public function age_classifications_get()
    {
        $postdata       = $this->get();

        $account_id     = (!empty($postdata['account_id'])) ? (int) $postdata['account_id'] : false;
        $where          = (!empty($postdata['where'])) ? $postdata['where'] : false;

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
                'status'                => false,
                'message'               => 'Validation errors: ' . $validation_errors,
                'age_classifications'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'age_classifications'   => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $age_classifications = $this->content_service->get_age_classifications($account_id, $where);

        if (!empty($age_classifications)) {
            $message = [
                'status'                => true,
                'message'               => $this->session->flashdata('message'),
                'age_classifications'   => $age_classifications
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                => false,
                'message'               => $this->session->flashdata('message'),
                'age_classifications'   => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Age Rating(s)
    */
    public function age_rating_get()
    {
        $postdata       = $this->get();

        $account_id     = (!empty($postdata['account_id'])) ? (int) $postdata['account_id'] : false;
        $where          = (!empty($postdata['where'])) ? $postdata['where'] : false;

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
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'age_rating'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'age_rating'    => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $age_rating = $this->content_service->get_age_rating($account_id, $where);

        if (!empty($age_rating)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'age_rating'    => $age_rating
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'age_rating'    => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Generate PDF data
    */
    public function generate_pdf_data_post()
    {
        $postdata           = $this->post();

        $account_id         = (!empty($postdata['account_id'])) ? (int) $postdata['account_id'] : false;
        $territory_id       = (!empty($postdata['territory_id'])) ? (int) $postdata['territory_id'] : false;
        $provider_ids       = (!empty($postdata['provider_ids'])) ? $postdata['provider_ids'] : false;
        $product_name       = (!empty($postdata['product_name'])) ? $postdata['product_name'] : false;
        $limit              = (!empty($postdata['limit'])) ? $postdata['limit'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('territory_id', 'Territory ID', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('provider_ids', 'Provider ID', 'required');
        $this->form_validation->set_rules('product_name', 'Product Name', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'message'       => 'Validation errors: ' . $validation_errors,
                'pdf_data'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'pdf_data'  => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        $pdf_data = $this->content_service->generate_pdf_data($account_id, $territory_id, $provider_ids, $product_name, $limit);

        if (!empty($pdf_data)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'pdf_data'      => $pdf_data
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'pdf_data'      => false
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
