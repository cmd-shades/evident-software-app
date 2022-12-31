<?php

namespace Application\Modules\Service\Controllers\Api;

use App\Adapter\RESTController;
use Application\Modules\Service\Models\CogginsApiModel;

class Coggins extends RESTController
{
	/**
	 * @var \Application\Modules\Service\Models\CogginsApiModel
	 */
	private $coggins_service;

	public function __construct()
    {
        parent::__construct();
        $this->coggins_service = new CogginsApiModel();
    }


    /**
    *   Show the status of running distributions along with their progress and the number of errors encountered.
    */
    public function running_get()
    {
        $account_id     = (int) $this->get('account_id');
        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_BAD_REQUEST,
                'message'       => 'Validation errors: ' . $validation_errors,
                'running'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if ((!$account_id) || (!$this->account_service->check_account_status($account_id))) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'       => 'Invalid main Account ID.',
                'running'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $running = $this->coggins_service->get_running($account_id);

        if (!empty($running->success)) {
            $message = [
                'status'    => true,
                'http_code' => REST_Controller::HTTP_OK,
                'message'   => $this->session->flashdata('message'),
                'running'   => !empty($running->data) ? $running->data : $running
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'    => false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message'   => (!empty($running->message)) ? $running->message : $this->session->flashdata('message'),
                'running'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Show the status of selected CDS servers along with their details of when they were last active.
    */
    public function servers_get()
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
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_BAD_REQUEST,
                'message'       => 'Validation errors: ' . $validation_errors,
                'servers'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if ((!$account_id) || (!$this->account_service->check_account_status($account_id))) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'       => 'Invalid main Account ID.',
                'servers'       => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $servers = $this->coggins_service->get_servers($account_id, $where, $limit, $offset);

        if (!empty($servers->success)) {
            $message = [
                'status'    => true,
                'http_code' => REST_Controller::HTTP_OK,
                'message'   => $this->session->flashdata('message'),
                'servers'   => !empty($servers->data) ? $servers->data : $servers
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'    => false,
                'http_code' => REST_Controller::HTTP_NO_CONTENT,
                'message'   => (!empty($servers->message)) ? $servers->message : $this->session->flashdata('message'),
                'servers'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    /**
    *   Submit a content package containing multiple files for distribution to servers at one or more sites.
    */
    public function queue_add_post()
    {
        $post           = $this->post();
        $account_id     = (!empty($post['account_id'])) ? (int) $post['account_id'] : false ;
        $films_data     = (!empty($post['films_data'])) ? $post['films_data'] : false ;
        $server_ids     = (!empty($post['server_ids'])) ? (array) $post['server_ids'] : false ;
        $scheduled      = (!empty($post['scheduled'])) ? $post['scheduled'] : false ;
        $priority       = (!empty($post['priority'])) ? $post['priority'] : false ;


        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('films_data', 'Films Data', 'required');
        $this->form_validation->set_rules('server_ids[]', 'List of Servers', 'required|trim|numeric|min_length[1]');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_BAD_REQUEST,
                'message'       => 'Validation errors: ' . $validation_errors,
                'queue_add'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if ((!$account_id) || (!$this->account_service->check_account_status($account_id))) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'       => 'Invalid main Account ID.',
                'queue_add'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $queue_add = $this->coggins_service->add_to_queue($account_id, $films_data, $server_ids, $scheduled, $priority);

        if (!empty($queue_add->success)) {
            $message = [
                'status'        => true,
                'http_code'     => REST_Controller::HTTP_OK,
                'message'       => $this->session->flashdata('message'),
                'queue_add'     => !empty($queue_add->data) ? $queue_add->data : $queue_add
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_NO_CONTENT,
                'message'       => (!empty($queue_add->message)) ? $queue_add->message : $this->session->flashdata('message'),
                'queue_add'     => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Delete a bundle (distribution) from the queue
    */
    public function queue_delete_post()
    {
        $post           = $this->post();
        $account_id     = (!empty($post['account_id'])) ? (int) $post['account_id'] : false ;
        $queue_id       = (!empty($post['queue_id'])) ? $post['queue_id'] : false ;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('queue_id', 'Queue ID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_BAD_REQUEST,
                'message'       => 'Validation errors: ' . $validation_errors,
                'queue_delete'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if ((!$account_id) || (!$this->account_service->check_account_status($account_id))) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'       => 'Invalid main Account ID.',
                'queue_delete'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $queue_delete = $this->coggins_service->queue_delete($account_id, $queue_id);

        if (!empty($queue_delete->success)) {
            $message = [
                'status'        => true,
                'http_code'     => REST_Controller::HTTP_OK,
                'message'       => $this->session->flashdata('message'),
                'queue_delete'  => !empty($queue_delete->data) ? $queue_delete->data : $queue_delete
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_NO_CONTENT,
                'message'       => (!empty($queue_delete->message)) ? $queue_delete->message : $this->session->flashdata('message'),
                'queue_delete'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Delete a bundle (distribution) from the queue
    */
    public function queue_cancel_post()
    {
        $post           = $this->post();
        $account_id     = (!empty($post['account_id'])) ? (int) $post['account_id'] : false ;
        $uid            = (!empty($post['uid'])) ? $post['uid'] : false ;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('uid', 'UID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_BAD_REQUEST,
                'message'       => 'Validation errors: ' . $validation_errors,
                'queue_cancel'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if ((!$account_id) || (!$this->account_service->check_account_status($account_id))) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'       => 'Invalid main Account ID.',
                'queue_cancel'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $queue_cancel = $this->coggins_service->queue_cancel($account_id, $uid);

        if (!empty($queue_cancel->success)) {
            $message = [
                'status'        => true,
                'http_code'     => REST_Controller::HTTP_OK,
                'message'       => $this->session->flashdata('message'),
                'queue_cancel'  => !empty($queue_cancel->data) ? $queue_cancel->data : $queue_cancel
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_NO_CONTENT,
                'message'       => (!empty($queue_cancel->message)) ? $queue_cancel->message : $this->session->flashdata('message'),
                'queue_cancel'  => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Cancel a bundle (distribution) from the running distribution (state: running, not in:[error|queued|prepared|catalogued] )
    */
    public function cancel_post()
    {
        $post           = $this->post();
        $account_id     = (!empty($post['account_id'])) ? (int) $post['account_id'] : false ;
        $bundle_uid     = (!empty($post['bundle_uid'])) ? $post['bundle_uid'] : false ;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('bundle_uid', 'Bundle UID', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_BAD_REQUEST,
                'message'       => 'Validation errors: ' . $validation_errors,
                'cancel'        => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if ((!$account_id) || (!$this->account_service->check_account_status($account_id))) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'       => 'Invalid main Account ID.',
                'cancel'        => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $cancel = $this->coggins_service->cancel($account_id, $bundle_uid);

        if (!empty($cancel->success)) {
            $message = [
                'status'        => true,
                'http_code'     => REST_Controller::HTTP_OK,
                'message'       => $this->session->flashdata('message'),
                'cancel'        => !empty($cancel->data) ? $cancel->data : $cancel
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_NO_CONTENT,
                'message'       => (!empty($cancel->message)) ? $cancel->message : $this->session->flashdata('message'),
                'cancel'        => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }



    /**
    *   Send a bundle of the movie, images and subtitle files into Coggins which will start the process for uploading them into AWS
    */
    public function aws_upload_post()
    {
        $post           = $this->post();

        $account_id     = (!empty($post['account_id'])) ? (int) $post['account_id'] : false ;
        $bucket_name    = (!empty($post['bucket'])) ? $post['bucket'] : 'basilica' ;
        $films          = (!empty($post['films'])) ? $post['films'] : false ;
        $cacti_id       = (!empty($post['cacti_id'])) ? (int) $post['cacti_id'] : false ;

        $this->form_validation->set_rules('account_id', 'Main Account ID', 'required');
        $this->form_validation->set_rules('bucket', 'Bucket Name', 'required'); ## not required by Coggins
        $this->form_validation->set_rules('films[]', 'Films Data', 'required');
        $this->form_validation->set_rules('cacti_id', 'CaCTi ID', 'required'); ## not required by Coggins

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_BAD_REQUEST,
                'message'       => 'Validation errors: ' . $validation_errors,
                'aws_upload'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if ((!$account_id) || (!$this->account_service->check_account_status($account_id))) {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'       => 'Invalid main Account ID.',
                'aws_upload'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $aws_upload = $this->coggins_service->aws_upload($account_id, $bucket_name, $films, $cacti_id);

        if (!empty($aws_upload->success)) {
            $message = [
                'status'        => true,
                'http_code'     => REST_Controller::HTTP_OK,
                'message'       => $this->session->flashdata('message'),
                'aws_upload'    => !empty($aws_upload->data) ? $aws_upload->data : $cancel
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'        => false,
                'http_code'     => REST_Controller::HTTP_NO_CONTENT,
                'message'       => (!empty($aws_upload->message)) ? $aws_upload->message : $this->session->flashdata('message'),
                'aws_upload'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }


    public function token_check($str = false, $token = false)
    {
        $this->form_validation->set_message('token_check', 'Invalid Token');

        $token  = (!empty($token)) ? $token : AWS_TOKEN ;
        $result = ($str === $token) ? true : false ;

        return $result;
    }


    /**
    *   AWS Coggins Webhook
    */
    public function aws_upload_webhook_post()
    {
        $post               = $this->post();
        $request_data       = false;

        $account_id = 1;
        $url_endpoint = "aws_upload_webhook_post";
        $data = json_encode($post);


        $debugging_data = [
        "full_response" => json_encode($post),
        "api-status"    => '' ,
        "api-code"      => '' ,
        "api-message"   => '' ,
        "date"          =>  date('Y-m-d H:i:s'),
        ];

        $debug = $this->coggins_service->save_debugging_data($account_id, $url_endpoint, $data, $debugging_data);

        $account_id = false;

        if (is_array($post)) {
            ## If request object is an array, we're checking if the first key is the request data (as per Basil's example). If not, not proceeding further - unknown structure.
            if (isset($post[0]) && is_object(json_decode($post[0]))) {
                $request_data = convert_to_array($post[0]);
            } else {
                $request_data = $post;
            }
        } elseif (is_object(json_decode($post))) {
            ## If request object is an object - trying to decode it
            $request_data = convert_to_array($post);
        } else {
            ## Post request object wasn't either an array or an object
        }

        if (!$request_data || empty($request_data)) {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_OK,
                'message'               => "Unknown structure of the request/Bad request",
                'aws_upload_webhook'    => false
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $this->form_validation->set_data($request_data);
        $this->form_validation->set_rules('credentials[accountId]', 'Account ID', 'required');
        $this->form_validation->set_rules('credentials[token]', 'Token', 'required|callback_token_check');
        $this->form_validation->set_rules('ids[cactiFileId]', 'CaCTi File ID', 'required');
        $this->form_validation->set_rules('state[status]', 'Status', 'required');
        $this->form_validation->set_rules('state[destination]', 'Destination', 'required');

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_BAD_REQUEST,
                'message'               => 'Validation errors: ' . $validation_errors,
                'aws_upload_webhook'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $account_id = (!empty($request_data['credentials']['accountId'])) ? (int) $request_data['credentials']['accountId'] : false ;
        if ((!$account_id) || ((int) $account_id < 1)) {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'               => 'Invalid main Account ID.',
                'aws_upload_webhook'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $aws_upload_webhook = $this->coggins_service->aws_upload_webhook($account_id, $request_data);

        if (!empty($aws_upload_webhook->success)) {
            $message = [
                'status'                => true,
                'http_code'             => REST_Controller::HTTP_OK,
                'message'               => (!empty($this->session->flashdata('message'))) ? $this->session->flashdata('message') : "Operation Successful" ,
                'aws_upload_webhook'    => !empty($aws_upload_webhook->data) ? $aws_upload_webhook->data : (object) [],
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_NO_CONTENT,
                'message'               => (!empty($this->session->flashdata('message'))) ? $this->session->flashdata('message') : "Operation Failed" ,
                'aws_upload_webhook'    => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
    *   This is a Webhook received from Coggins after the file (feature or trailer) has been moved by Lambda from Techlive AWS bucket into the Easel's AWS bucket, processed and added into Easel.
    *   The expected value is an Easel's ID of the file.
    */
    public function lambda_file_process_post()
    {
        $post               = $this->post();
        $request_data       = false;

        if (is_array($post)) {
            ## If request object is an array, we're checking if the first key is the request data (as per Basil's example). If not, not proceeding further - unknown structure.
            if (isset($post[0]) && is_object(json_decode($post[0]))) {
                $request_data = convert_to_array($post[0]);
            } else {
                $request_data = $post;
            }
        } elseif (is_object(json_decode($post))) {
            ## If request object is an object - trying to decode it
            $request_data = convert_to_array($post);
        } else {
            ## Post request object wasn't either an array or an object
        }

        if (!$request_data || empty($request_data)) {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_OK,
                'message'               => "Unknown structure of the request/Bad request",
                'lambda_file_process'   => false
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $this->form_validation->set_data($request_data);
        $this->form_validation->set_rules('credentials[accountId]', 'Account ID', 'required');
        $this->form_validation->set_rules('credentials[token]', 'Token', 'required|callback_token_check');
        $this->form_validation->set_rules('ids[cactiAwsBundleId]', 'CaCTi Bundle ID', 'required');
        $this->form_validation->set_rules('ids[cactiFileId]', 'CaCTi File ID', 'required');
        $this->form_validation->set_rules('ids[airtimeId]', 'VOD Media File ID', 'required');
        $this->form_validation->set_rules('state[status]', 'Status', 'required');

        $account_id = (!empty($request_data['credentials']['accountId'])) ? (int) $request_data['credentials']['accountId'] : false ;

        if ($this->form_validation->run() == false) {
            $validation_errors = (validation_errors()) ? validation_errors() : '';
        }

        if (isset($validation_errors) && !empty($validation_errors)) {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_BAD_REQUEST,
                'message'               => 'Validation errors: ' . $validation_errors,
                'lambda_file_process'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        if ((!$account_id) || ((int) $account_id < 1)) {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
                'message'               => 'Invalid main Account ID.',
                'lambda_file_process'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }

        $lambda_file_process = $this->coggins_service->lambda_file_process($account_id, $request_data);

        if (!empty($lambda_file_process->success)) {
            $message = [
                'status'                => true,
                'http_code'             => REST_Controller::HTTP_OK,
                'message'               => (!empty($lambda_file_process->message)) ? $lambda_file_process->message : ((!empty($this->session->flashdata('message'))) ? $this->session->flashdata('message') : "Operation Successful") ,
                'lambda_file_process'   => !empty($lambda_file_process->data) ? $lambda_file_process->data : (object) [],
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        } else {
            $message = [
                'status'                => false,
                'http_code'             => REST_Controller::HTTP_NO_CONTENT,
                'message'               => (!empty($lambda_file_process->message)) ? $lambda_file_process->message : ((!empty($this->session->flashdata('message'))) ? $this->session->flashdata('message') : "Operation Failed") ,
                'lambda_file_process'   => null
            ];
            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}
