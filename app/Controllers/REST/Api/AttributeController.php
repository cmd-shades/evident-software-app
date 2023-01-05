<?php

namespace App\Controllers\REST\Api;

use App\Adapter\RESTController;
use App\Models\Service\AccessModel;
use App\Models\Service\AttributeModel;
use App\Models\Service\ModulesModel;

final class AttributeController extends RESTController
{
	/**
	 * @var \Application\Modules\Service\Controllers\Api\AttributeModel
	 */
	private $attribute_service;
	/**
	 * @var \Application\Modules\Service\Controllers\Api\AccessModel
	 */
	private $access_service;
	/**
	 * @var \Application\Modules\Service\Controllers\Api\ModulesModel
	 */
	private $modules_service;

	public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->attribute_service = new AttributeModel();
        $this->access_service = new AccessModel();
        $this->modules_service = new ModulesModel();
    }



    /**
    *	The function will retrieve attributes for the specific module, section, module item
    */
    public function attributes_get()
    {
        $get_data 			= $this->get();
        $account_id 		= (!empty($get_data['account_id'])) ? ( int ) $get_data['account_id'] : false;
        $where 				= (!empty($get_data['where'])) ? $get_data['where'] : false;
        $limit 				= (!empty($get_data['limit'])) ? $get_data['limit'] : DEFAULT_MAX_LIMIT ;
        $offset 			= (!empty($get_data['offset'])) ? $get_data['offset'] : DEFAULT_OFFSET;

        $expected_data = [
            'account_id' => $account_id
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 		=> false,
                'message' 		=> 'Validation errors: '.$validation_errors,
                'attributes' 	=> null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'message' 		=> 'Invalid main Account ID',
                'attributes' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $module_attributes = $this->attribute_service->get_attributes($account_id, $where, $limit, $offset);

        if (!empty($module_attributes)) {
            $message = [
                'status' 		=> true,
                'message' 		=> $this->session->flashdata('message'),
                'attributes'	=> $module_attributes,
            ];
            $this->response($message, REST_Controller::HTTP_OK); //
        } else {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'attributes' 	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *	The function will update the attribute responses
    */
    public function update_attribute_responses_post()
    {
        $post_data 			= $this->post();

        $account_id 		= (!empty($post_data['account_id'])) ? ( int ) $post_data['account_id'] : false;
        $profile_id 		= (!empty($post_data['profile_id'])) ? ( int ) $post_data['profile_id'] : false;
        $module_id 			= (!empty($post_data['module_id'])) ? ( int ) $post_data['module_id'] : false;
        $module_item_id 	= (!empty($post_data['module_item_id'])) ? ( int ) $post_data['module_item_id'] : false;
        $zone_id 			= (!empty($post_data['zone_id'])) ? ( int ) $post_data['zone_id'] : false;
        $resp 				= (!empty($post_data['resp'])) ? $post_data['resp'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('profile_id', 'Profile ID', 'required');
        $this->form_validation->set_rules('module_id', 'Module ID', 'required');
        $this->form_validation->set_rules('module_item_id', 'Module Item ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            ## One of the required fields is invalid
            $message = [
                'status' 		=> false,
                'message' 		=> 'Validation errors: '.$validation_errors,
                'updated_resp' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'message' 		=> 'Invalid main Account ID',
                'updated_resp' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $updated_resp = $this->attribute_service->update_attribute_responses($account_id, $profile_id, $module_id, $module_item_id, $zone_id, $resp);

        if (!empty($updated_resp)) {
            $message = [
                'status' 		=> true,
                'message' 		=> $this->session->flashdata('message'),
                'updated_resp' 	=> $updated_resp,
            ];
            $this->response($message, REST_Controller::HTTP_OK); //
        } else {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'updated_resp' 	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *	The function will retrieve responses for the specific module, section, module item
    */
    public function responses_get()
    {
        $get_data 			= $this->get();
        $account_id 		= (!empty($get_data['account_id'])) ? ( int ) $get_data['account_id'] : false;
        $where 				= (!empty($get_data['where'])) ? $get_data['where'] : false; ## there is a loophole because module id and module item id is required to get anything
        $limit 				= (!empty($get_data['limit'])) ? $get_data['limit'] : DEFAULT_MAX_LIMIT ;
        $offset 			= (!empty($get_data['offset'])) ? $get_data['offset'] : DEFAULT_OFFSET;

        $expected_data = [
            'account_id' => $account_id
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 		=> false,
                'message' 		=> 'Validation errors: '.$validation_errors,
                'responses' 	=> null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'message' 		=> 'Invalid main Account ID',
                'responses' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $responses = $this->attribute_service->get_responses($account_id, $where, $limit, $offset);

        if (!empty($responses)) {
            $message = [
                'status' 		=> true,
                'message' 		=> $this->session->flashdata('message'),
                'responses'		=> $responses,
            ];
            $this->response($message, REST_Controller::HTTP_OK); //
        } else {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'responses' 	=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * 	Create an Attribute
    */
    public function create_post()
    {
        $post_data 			= $this->post();
        $account_id 		= (!empty($post_data['account_id'])) ? ( int ) $post_data['account_id'] : false ;
        $attr_data 			= (!empty($post_data['attr_data'])) ? $post_data['attr_data'] : false ;
        unset($post_data);

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('attr_data', 'Attribute Data', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Validation errors: '.trim($validation_errors),
                'new_attribute' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'new_attribute'  	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $new_attribute = $this->attribute_service->create_atribute($account_id, $attr_data);

        if (!empty($new_attribute)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'new_attribute' 	=> $new_attribute
            ];

            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'new_attribute' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    * 	Check Label if unique
    */
    public function check_label_post()
    {
        $post_data 		= $this->post();
        $account_id 	= (!empty($post_data['account_id'])) ? ( int ) $post_data['account_id'] : false ;
        $module_id 		= (!empty($post_data['module_id'])) ? ( int ) $post_data['module_id'] : false ;
        $label 			= (!empty($post_data['label'])) ? $post_data['label'] : false ;
        unset($post_data);

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('label', 'The Label', 'required');
        $this->form_validation->set_rules('module_id', 'Module ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Validation errors: '.trim($validation_errors),
                'trimmed_label' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'trimmed_label'  	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $trimmed_label = $this->attribute_service->check_label($account_id, $module_id, $label);

        if (!empty($trimmed_label)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'trimmed_label' 	=> $trimmed_label
            ];

            $this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'trimmed_label' 	=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *	The function will retrieve section(s) for the specific module, module item
    */
    public function sections_get()
    {
        $get_data 			= $this->get();
        $account_id 		= (!empty($get_data['account_id'])) ? ( int ) $get_data['account_id'] : false;
        $where 				= (!empty($get_data['where'])) ? $get_data['where'] : false;
        $limit 				= (!empty($get_data['limit'])) ? $get_data['limit'] : DEFAULT_MAX_LIMIT ;
        $offset 			= (!empty($get_data['offset'])) ? $get_data['offset'] : DEFAULT_OFFSET;

        $expected_data = [
            'account_id' => $account_id
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 		=> false,
                'message' 		=> 'Validation errors: '.$validation_errors,
                'sections' 		=> null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'message' 		=> 'Invalid main Account ID',
                'sections' 		=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $sections = $this->attribute_service->get_sections($account_id, $where, $limit, $offset);

        if (!empty($sections)) {
            $message = [
                'status' 		=> true,
                'message' 		=> $this->session->flashdata('message'),
                'sections'		=> $sections,
            ];
            $this->response($message, REST_Controller::HTTP_OK); //
        } else {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'sections' 		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *	The function will retrieve group(s) for the specific module, module item, section ID
    */
    public function groups_get()
    {
        $get_data 			= $this->get();

        $account_id 		= (!empty($get_data['account_id'])) ? ( int ) $get_data['account_id'] : false;
        $where 				= (!empty($get_data['where'])) ? $get_data['where'] : false;
        $limit 				= (!empty($get_data['limit'])) ? $get_data['limit'] : DEFAULT_MAX_LIMIT ;
        $offset 			= (!empty($get_data['offset'])) ? $get_data['offset'] : DEFAULT_OFFSET;

        $expected_data = [
            'account_id' => $account_id
        ];

        $this->form_validation->set_data($expected_data);
        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 		=> false,
                'message' 		=> 'Validation errors: '.$validation_errors,
                'groups' 		=> null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 		=> false,
                'message' 		=> 'Invalid main Account ID',
                'groups' 		=> null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $groups = $this->attribute_service->get_groups($account_id, $where, $limit, $offset);

        if (!empty($groups)) {
            $message = [
                'status' 		=> true,
                'message' 		=> $this->session->flashdata('message'),
                'groups'		=> $groups,
            ];
            $this->response($message, REST_Controller::HTTP_OK); //
        } else {
            $message = [
                'status' 		=> false,
                'message' 		=> $this->session->flashdata('message'),
                'groups' 		=> null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    *	The function will delete an attribute
    */
    public function delete_post()
    {
        $post_data 			= $this->post();

        $account_id 		= (!empty($post_data['account_id'])) ? ( int ) $post_data['account_id'] : false;
        $attribute_id 		= (!empty($post_data['attribute_id'])) ? $post_data['attribute_id'] : false;

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Validation errors: '.$validation_errors,
                'deleted_attribute' => null
            ];

            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 			=> false,
                'message' 			=> 'Invalid main Account ID',
                'deleted_attribute' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if ($attribute_id <= 0) {
            $this->response(null, REST_Controller::HTTP_BAD_REQUEST);
        }

        $attribute = $this->attribute_service->get_attributes($account_id, [$where['attribute_id'] = $attribute_id]);

        if (!$attribute) {
            $message = [
                'status' 			=> false,
                'message' 			=> $this->session->flashdata('message'),
                'deleted_attribute' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $deleted_attribute = $this->attribute_service->delete_attribute($account_id, $attribute_id);

        if (!empty($deleted_attribute)) {
            $message = [
                'status' 			=> true,
                'message' 			=> $this->session->flashdata('message'),
                'deleted_attribute'	=> $deleted_attribute,
            ];
            $this->response($message, REST_Controller::HTTP_OK); //
        } else {
            $message = [
                'status'	 		=> false,
                'message' 			=> $this->session->flashdata('message'),
                'deleted_attribute' => null,
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    * 	Update attribute
    */
    public function update_post()
    {
        $post_data 		= $this->post();
        $account_id 	= (!empty($post_data['account_id'])) ? (int) $post_data['account_id'] : false ;
        $attribute_id 	= (!empty($post_data['attribute_id'])) ? (int) $post_data['attribute_id'] : false ;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('attribute_id', 'Attribute ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : false ;
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 	=> 	false,
                'message' 	=> 	'Validation errors: '.$validation_errors,
                'attribute' => 	null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message'	=> 'Invalid main Account ID.',
                'attribute' => 	null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Validate the attribute ID.
        $valid_attribute = $this->db->get_where("custom_attribute", ["attribute_id" => $attribute_id, "account_id", $account_id ])->row();

        if (!$valid_attribute || $attribute_id <= 0) {
            $message = [
                'status' 	=> false,
                'message' 	=> "Invalid Attribute ID",
                'attribute' => 	null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $current_account = $this->ion_auth->_current_user()->account_id;

        ## Stop illegal updates
        if ($current_account != $account_id) {
            $message = [
                'status' 	=> false,
                'message' 	=> 'Illegal operation. This is not your resource!',
                'attribute' => 	null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        ## Run person update
        $updated_attribute = $this->attribute_service->update_attribute($account_id, $attribute_id, $post_data);

        if (!empty($updated_attribute)) {
            $message = [
                'status' 	=> true,
                'message' 	=> $this->session->flashdata('message'),
                'attribute' => $updated_attribute
            ];
            $this->response($message, REST_Controller::HTTP_OK); // Resource Updated
        } else {
            $message = [
                'status'	=> false,
                'message' 	=> $this->session->flashdata('message'),
                'attribute' => 	null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
