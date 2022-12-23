<?php

use App\Libraries\REST_Controller;

class Document_Handler extends REST_Controller
{
    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('Document_Handler_model', 'document_service');
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->lang->load('auth');
    }

    /**
    * Upload files
    */
    public function upload_post()
    {
        $postdata       = $this->post();
        $account_id     = (int) $this->post('account_id');
        $document_group = $this->post('document_group');
        $doc_folder     = $this->post('doc_type');

        // if( !$this->account_service->check_account_status( $account_id ) ){
        // $message = [
        // 'status'     => FALSE,
        // 'message'    => 'Invalid main Account ID',
        // 'type'       => 'Document upload',
        // 'documents' => NULL
        // ];
        // $this->response( $message, REST_Controller::HTTP_OK );
        // }

        $uploaded_docs = $this->document_service->upload_files($account_id, $postdata, $document_group, $doc_folder);

        if (!empty($uploaded_docs['documents'])) {
            $feedback = $this->session->flashdata('message');

            $message = [
                'status'    => true,
                'message'   => $feedback . ((isset($uploaded_docs['errors'])) ? '. With some errors' : null),
                'type'      => 'upload',
                'documents' => $uploaded_docs['documents'],
                'errors'    => (!empty($uploaded_docs['errors'])) ? $uploaded_docs['errors'] : null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'    => false,
                'message'   => $this->session->flashdata('message'),
                'type'      => 'upload',
                'documents' => null,
                'errors'    => (!empty($uploaded_docs['errors'])) ? $uploaded_docs['errors'] : null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    public function document_list_get()
    {
        $account_id         = (int) $this->get('account_id');
        $document_group     = $this->get('document_group');
        $postdata           = $this->get();

        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status' => false,
                'message' => 'Invalid main Account ID',
                'documents' => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $docs_list = $this->document_service->get_document_list($account_id, $document_group, $postdata);

        if (!empty($docs_list)) {
            $message = [
                'status' => true,
                'message' => $this->session->flashdata('message'),
                'documents' => $docs_list
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


    public function delete_document_post()
    {
        $post_data = $this->post();

        $account_id         = (!empty($post_data['account_id'])) ? (int) $post_data['account_id'] : false ;
        $document_id        = (!empty($post_data['document_id'])) ? (int) $post_data['document_id'] : false ;
        $document_group     = (!empty($post_data['doc_group'])) ? $post_data['doc_group'] : false ;


        if (!$this->account_service->check_account_status($account_id)) {
            $message = [
                'status'        => false,
                'message'       => 'Invalid main Account ID',
                'd_document'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $d_document = $this->document_service->delete_document($account_id, $document_group, $document_id);

        if (!empty($d_document)) {
            $message = [
                'status'        => true,
                'message'       => $this->session->flashdata('message'),
                'd_document'    => $d_document
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'message'       => $this->session->flashdata('message'),
                'd_document'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
