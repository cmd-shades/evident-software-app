<?php

namespace Application\Modules\Service\Controllers\Api;

use App\Adapter\RESTController;
use Application\Modules\Service\Models\DocumentHandlerModel;

class Document_Handler extends RESTController
{
    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->document_service = new DocumentHandlerModel();
        $this->form_validation->set_error_delimiters(
			$this->config->item('error_start_delimiter', 'ion_auth'),
			$this->config->item('error_end_delimiter', 'ion_auth')
		);
        $this->lang->load('auth');
    }

    /**
    * Upload files
    */
    public function upload_post()
    {
        $postdata		= $this->post();
        $account_id 	= (int) $this->post('account_id');
        $document_group	= $this->post('document_group');
        $doc_type		= $this->post('doc_type');
        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> 'Invalid main Account ID',
                'type' 		=> 'upload',
                'documents' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $uploaded_docs = $this->document_service->upload_files($account_id, $postdata, $document_group, $doc_type);

        if (!empty($uploaded_docs['documents'])) {
            $feedback= $this->session->flashdata('message');

            $message = [
                'status' => true,
                'message' => $feedback. ((isset($uploaded_docs['errors'])) ? '. With some errors' : null),
                'type' => 'upload',
                'documents' =>$uploaded_docs['documents'],
                'errors' =>(!empty($uploaded_docs['errors'])) ? $uploaded_docs['errors'] : null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'type' => 'upload',
                'documents' => null,
                'errors' =>(!empty($uploaded_docs['errors'])) ? $uploaded_docs['errors'] : null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    public function document_list_get()
    {
        $account_id			= (int) $this->get('account_id');
        $document_group 	= $this->get('document_group');
        $postdata 			= $this->get();
        $where 				= !empty($this->get('where')) ? $this->get('where') : null;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID',
                'documents' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $docs_list = $this->document_service->get_document_list($account_id, $document_group, $postdata, $where);

        if (!empty($docs_list)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'documents' =>$docs_list
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' => false,
                'message' => $this->session->flashdata('message'),
                'documents' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    * Delete Document / Resource
    */
    public function delete_document_post()
    {
        $account_id			= (int) $this->post('account_id');
        $document_id		= $this->post('document_id');
        $document_group 	= $this->post('document_group');

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'message' 	=> 'Invalid main Account ID',
                'documents' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $delete_items = $this->document_service->delete_document($account_id, $document_id, $document_group);

        if (!empty($delete_items)) {
            $message = [
                'status' 	=> true,
                'message' 	=> $this->session->flashdata('message'),
                'documents' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status' 	=> false,
                'message' 	=> $this->session->flashdata('message'),
                'documents' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /** Update Document Status **/
    public function update_document_status_post()
    {
        $postdata 		= $this->post();
        $account_id 	= !empty($this->post('account_id')) ? ( int ) $this->post('account_id') : false;
        $document_group	= !empty($this->post('document_group')) ? $this->post('document_group') : false;

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message' 	=> 'Invalid main Account ID',
                'documents' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $this->form_validation->set_rules('account_id', 'Account ID', 'required');
        $this->form_validation->set_rules('document_group', 'Document group', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status' 	=> false,
                'http_code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' 	=> 'Invalid request data: ',
                'documents'	=> null
            ];
            $message['message'] = (isset($validation_errors) && !empty($validation_errors)) ? 'Validation errors: '.$validation_errors : $message['message'];
            $this->response($message, REST_Controller::HTTP_OK);
        }



        $documents = $this->document_service->update_document_status($account_id, $document_group, $postdata);

        if (!empty($documents)) {
            $message = [
                'status' 	=> true,
                'http_code' => REST_Controller::HTTP_CREATED,
                'message' 	=> $this->session->flashdata('message'),
                'documents' => $documents
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'	=> false,
                'http_code' => REST_Controller::HTTP_BAD_REQUEST,
                'message' 	=> $this->session->flashdata('message'),
                'documents' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
