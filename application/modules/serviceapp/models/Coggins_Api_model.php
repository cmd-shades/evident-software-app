<?php

namespace Application\Modules\Service\Models;

use System\Core\CI_Model;

class Coggins_Api_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->coggins_api_end_point = COGGINS_API_BASE_URL;
    }



    public function save_debugging_data($account_id = false, $request_name = false, $request_data = false, $debugging_data = false)
    {
        $result     = false;
        if (!empty($account_id) && !empty($request_name) && !empty($request_data) && !empty($debugging_data)) {
            $full_set   = [];

            $data = [
                "account_id"    => $account_id,
                "request_name"  => $request_name,
                "request_data"  => json_encode($request_data),
                "requested_by"  => $this->ion_auth->_current_user->id,
            ];

            $full_set = array_merge($data, $debugging_data);

            if (!empty($full_set)) {
                $this->db->insert("coggins_debugging", $full_set);

                if ($this->db->affected_rows() > 0) {
                    $insert_id = $this->db->insert_id();
                    $result = $this->db->get_where("coggins_debugging", ["request_id" => $insert_id])->row();
                }
            }
        }

        return $result;
    }



    /**
    *   Show the status of running distributions along with their progress and the number of errors encountered.
    */
    public function get_running($account_id = false, $where = false)
    {
        $result = (object)[
            'data'      => false,
            'success'   => false,
            'message'   => ''
        ];

        if (!empty($account_id)) {
            $executed = $headers = false;

            $url_endpoint   = 'queueRunning';
            $method_type    = 'POST';
            $data               = [];
            $data['queueId']    = [];

            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where)) {
                    if (!empty($where['queue_id'])) {
                        $data['queueId'] = $where['queue_id'];
                        unset($where['queue_id']);
                    }
                }
            }
            $coggins_post   = $this->coggins_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);
            $executed       = $coggins_post['executed'];
            $headers        = $coggins_post['headers'];
            unset($coggins_post);

            if (!empty($headers['api-status'])  && !empty($executed)) {
                $result->data    = $executed;
                $result->success = true;
                $result->message = ( !empty($headers['api-message']) ) ? $headers['api-message'] : 'Content found' ;
                $this->session->set_flashdata('message', $result->message);
            } else {
                $result->data    = false;
                $result->success = false;
                $result->message = ( !empty($headers['api-message']) ) ? $headers['api-message'] : 'Content not found' ;
                $this->session->set_flashdata('message', $result->message);
            }
        }
        return $result;
    }



    /**
    *   Show the status of selected CDS servers along with their details of when they were last active.
    *   For POSTMAN: {"filter": {"field": "status","value": "active"},"order": {"field": "serverid","seq": "down"}}
    */
    public function get_servers($account_id = false, $where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {

        $result = (object)[
            'data'      => false,
            'success'   => false,
            'message'   => ''
        ];

        if (!empty($account_id)) {
            $executed = $headers = false;

            $url_endpoint   = 'cdsServers';
            $method_type    = 'POST';
            $data           = [];
            $data['filter'] = [
                'field' => "",
                'value' => "",
            ];
            $data['order']  = [
                'field' => "serverid",
                'seq'   => "up",
            ];

            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where)) {
                    // for now just one filter only
                    if (!empty($where['filter'])) {
                        ## Possible values: [company,running,serverid,servername,status,userid,username]
                        if (!empty($where['filter']['field']) && !empty($where['filter']['value'])) {
                            if (strtolower($where['filter']['field']) == "status") {
                                $data['filter']['field'] = $where['filter']['field'];
                                $data['filter']['value'] = is_string($where['filter']['value']) ? $where['filter']['value'] : (string) html_escape($where['filter']['value']) ;
                            } elseif (strtolower($where['filter']['field']) == "serverid") {
                                if (is_array($where['filter']['value'])) {
                                    foreach ($where['filter']['value'] as $key => $value) {
                                        $data['filter']['field'] = $where['filter']['field'];
                                        $data['filter']['value'][$key] = (int) $value;
                                    }
                                } else {
                                    $data['filter']['field'] = $where['filter']['field'];
                                    $data['filter']['value'] = (int) $where['filter']['value'];
                                }
                            } else {
                                $data['filter']['field'] = $where['filter']['field'];
                                $data['filter']['value'] = $where['filter']['value'];
                            }
                        }
                    }

                    if (!empty($where['order'])) {
                        if (!empty($where['order']['field']) && !empty($where['order']['seq'])) {
                            $data['order']['field'] = $where['order']['field'];
                            $data['order']['seq']   = $where['order']['seq'];
                        }
                    }
                }
            }

            $coggins_post   = $this->coggins_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);

            $executed       = ( !empty($coggins_post['executed']) ) ? $coggins_post['executed'] : false ;
            $headers        = ( !empty($coggins_post['headers']) ) ? $coggins_post['headers'] : false;
            unset($coggins_post);

            if (!empty($headers['api-status'])  && !empty($executed)) {
                $result->data    = $executed;
                $result->success = true;
                $result->message = ( !empty($headers['api-message']) ) ? $headers['api-message'] : 'Content found' ;
                $this->session->set_flashdata('message', $result->message);
            } else {
                $result->data    = false;
                $result->success = false;
                $result->message = ( !empty($headers['api-message']) ) ? $headers['api-message'] : 'Content not found' ;
                $this->session->set_flashdata('message', $result->message);
            }
        }
        return $result;
    }


    public function add_to_queue($account_id = false, $films_data = [], $server_ids = [], $scheduled = false, $priority = false)
    {
        $result = (object)[
            'data'      => false,
            'success'   => false,
            'message'   => ''
        ];

        if (!empty($account_id) && !empty($films_data) && !empty($server_ids)) {
            $executed = $headers = false;
            $url_endpoint   = 'queueAdd';
            $method_type    = 'POST';
            $data           = [
                'films'     => $films_data,
                'servers'   => $server_ids,
                'scheduled' => ( !empty($scheduled) ) ? $scheduled : false ,
                'priority'  => ( !empty($priority) ) ? $priority : false ,  ## ['high' or 'low' (default)]
            ];

            log_message("error", json_encode(["queueadd data" => $data ]));

            $coggins_post   = $this->coggins_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);
            log_message("error", json_encode(["coggins_post data" => $coggins_post ]));

            $debugging_data = [
                "full_response" => json_encode($coggins_post),
                "api-status"    => ( !empty($coggins_post['headers']['api-status']) ) ? $coggins_post['headers']['api-status'] : '' ,
                "api-code"      => ( !empty($coggins_post['headers']['api-code']) ) ? $coggins_post['headers']['api-code'] : '' ,
                "api-message"   => ( !empty($coggins_post['headers']['api-message']) ) ? $coggins_post['headers']['api-message'] : '' ,
                "date"          => ( !empty($coggins_post['headers']['date']) ) ? $coggins_post['headers']['date'] : '' ,
            ];

            $debug = $this->save_debugging_data($account_id, $url_endpoint, $data, $debugging_data);

            $executed       = $coggins_post['executed'];
            $headers        = $coggins_post['headers'];
            unset($coggins_post);

            if (!empty($headers['api-status']) && ( $headers['api-status'] == true ) && !empty($executed)) {
                $result->data    = $executed[0];
                $result->success = true;
                $result->message = ( !empty($headers['api-message']) ) ? $headers['api-message'] : 'Bundle added' ;
                $this->session->set_flashdata('message', $result->message);
            } else {
                $result->data    = false;
                $result->success = false;
                $result->message = ( !empty($headers['api-message']) ) ? $headers['api-message'] : 'Bundle not added' ;
                $this->session->set_flashdata('message', $result->message);
            }
        }

        return $result;
    }


    /**
    *   Show the distribution bundles waiting to be processed
    */
    public function get_queue_waiting($account_id = false, $where = false)
    {
        $result = (object)[
            'data'      => false,
            'success'   => false,
            'message'   => ''
        ];

        if (!empty($account_id)) {
            $executed = $headers = false;

            $url_endpoint       = 'queueWaiting';
            $method_type        = 'POST';

            $data               = [];
            $data['queueId']    = [];

            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where)) {
                    if (!empty($where['queue_id'])) {
                        $data['queueId'] = $where['queue_id'];
                        unset($where['queue_id']);
                    }
                }
            }

            $coggins_post   = $this->coggins_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);

            $executed       = $coggins_post['executed'];
            $headers        = $coggins_post['headers'];
            unset($coggins_post);

            if (!empty($headers['api-status'])  && !empty($executed)) {
                $result->data    = $executed;
                $result->success = true;
                $result->message = ( !empty($headers['api-message']) ) ? $headers['api-message'] : 'Content found' ;
                $this->session->set_flashdata('message', $result->message);
            } else {
                $result->data    = false;
                $result->success = false;
                $result->message = ( !empty($headers['api-message']) ) ? $headers['api-message'] : 'Content not found' ;
                $this->session->set_flashdata('message', $result->message);
            }
        }
        return $result;
    }


    /**
    *   Show the distribution bundles waiting to be processed
    */
    public function get_completed($account_id = false, $where = false)
    {
        $result = (object)[
            'data'      => false,
            'success'   => false,
            'message'   => ''
        ];

        if (!empty($account_id)) {
            $executed = $headers = false;

            $url_endpoint   = 'cdsCompleted';
            $method_type    = 'POST';
            $data           = [];
            $data           = [
                'start' => (int) ( strtotime(date('Y-m-d H:i:s', strtotime("-1 week"))) * 1000 ) ,
                'end'   => (int) ( strtotime(date('Y-m-d H:i:s')) * 1000 ),
            ];

            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where)) {
                    if (!empty($where['start'])) {
                        $data['start'] = (int) ( strtotime(date('Y-m-d H:i:s', strtotime($where['start']))) * 1000 );
                    }

                    if (!empty($where['end'])) {
                        $data['end'] = (int) ( strtotime(date('Y-m-d H:i:s', strtotime($where['end']))) * 1000 ) ;
                    }
                }
            }

            log_message("error", json_encode(["cdsCompleted data" => $data ]));

            $coggins_post   = $this->coggins_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);

            $debugging_data = [
                "full_response" => json_encode($coggins_post),
                "api-status"    => ( !empty($coggins_post['headers']['api-status']) ) ? $coggins_post['headers']['api-status'] : '' ,
                "api-code"      => ( !empty($coggins_post['headers']['api-code']) ) ? $coggins_post['headers']['api-code'] : '' ,
                "api-message"   => ( !empty($coggins_post['headers']['api-message']) ) ? $coggins_post['headers']['api-message'] : '' ,
                "date"          => ( !empty($coggins_post['headers']['date']) ) ? $coggins_post['headers']['date'] : '' ,
            ];

            $debug = $this->save_debugging_data($account_id, $url_endpoint, $data, $debugging_data);

            $executed       = $coggins_post['executed'];
            $headers        = ( !empty($coggins_post['headers']) ) ? $coggins_post['headers'] : false ;
            unset($coggins_post);

            if (isset($headers) && !empty($headers['api-status'])  && !empty($executed)) {
                $result->data    = $executed;
                $result->success = true;
                $result->message = ( !empty($headers['api-message']) ) ? $headers['api-message'] : 'Content found' ;
                $this->session->set_flashdata('message', $result->message);
            } else {
                $result->data    = false;
                $result->success = false;
                $result->message = ( !empty($headers['api-message']) ) ? $headers['api-message'] : 'Content not found' ;
                $this->session->set_flashdata('message', $result->message);
            }
        }
        return $result;
    }


    /**
    *   Return the complete set of distributions that have been finished.
    */
    public function get_queue_finished($account_id = false, $where = false)
    {
        $result = (object)[
            'data'      => false,
            'success'   => false,
            'message'   => ''
        ];

        if (!empty($account_id)) {
            $executed = $headers = false;

            $url_endpoint   = 'queueFinished';
            $method_type    = 'POST';
            $data           = [];
            $data           = [
                'queueId' => [],
            ];

            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where)) {
                    if (!empty($where['queue_id'])) {
                        $data['queueId'] = $where['queue_id'];
                        unset($where['queue_id']);
                    }
                }
            }

// log_message( "error", json_encode( ["get_queue_finished data" =>$data ] ) );

            $coggins_post   = $this->coggins_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);

            $debugging_data = [
                "full_response" => json_encode($coggins_post),
                "api-status"    => ( !empty($coggins_post['headers']['api-status']) ) ? $coggins_post['headers']['api-status'] : '' ,
                "api-code"      => ( !empty($coggins_post['headers']['api-code']) ) ? $coggins_post['headers']['api-code'] : '' ,
                "api-message"   => ( !empty($coggins_post['headers']['api-message']) ) ? $coggins_post['headers']['api-message'] : '' ,
                "date"          => ( !empty($coggins_post['headers']['date']) ) ? $coggins_post['headers']['date'] : '' ,
            ];

            $debug = $this->save_debugging_data($account_id, $url_endpoint, $data, $debugging_data);

            $executed       = $coggins_post['executed'];
            $headers        = ( !empty($coggins_post['headers']) ) ? $coggins_post['headers'] : false ;
            unset($coggins_post);

            if (isset($headers) && !empty($headers['api-status'])  && !empty($executed)) {
                $result->data    = $executed;
                $result->success = true;
                $result->message = ( !empty($headers['api-message']) ) ? $headers['api-message'] : 'Content found' ;
                $this->session->set_flashdata('message', $result->message);
            } else {
                $result->data    = false;
                $result->success = false;
                $result->message = ( !empty($headers['api-message']) ) ? $headers['api-message'] : 'Content not found' ;
                $this->session->set_flashdata('message', $result->message);
            }
        }
        return $result;
    }



    /**
    *   Delete a single distribution from the queue, using the queue ID of the distribution.
    */
    public function queue_delete($account_id = false, $queue_id = false)
    {
        $result = (object)[
            'data'      => false,
            'success'   => false,
            'message'   => ''
        ];

        if (!empty($account_id) && !empty($queue_id)) {
            $executed = $headers = false;

            $url_endpoint       = 'queueDelete';
            $method_type        = 'POST';
            $data               = [];
            $data['queueId']    = [];

            if (!empty($queue_id)) {
                $data['queueId'] = $queue_id;
            }

            $coggins_post   = $this->coggins_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);

            $debugging_data = [
                "full_response" => json_encode($coggins_post),
                "api-status"    => ( !empty($coggins_post['headers']['api-status']) ) ? $coggins_post['headers']['api-status'] : '' ,
                "api-code"      => ( !empty($coggins_post['headers']['api-code']) ) ? $coggins_post['headers']['api-code'] : '' ,
                "api-message"   => ( !empty($coggins_post['headers']['api-message']) ) ? $coggins_post['headers']['api-message'] : '' ,
                "date"          => ( !empty($coggins_post['headers']['date']) ) ? $coggins_post['headers']['date'] : '' ,
            ];

            // $debug = $this->save_debugging_data( $account_id, $url_endpoint, $data, $debugging_data );

            $executed       = $coggins_post['executed'];
            $headers        = ( !empty($coggins_post['headers']) ) ? $coggins_post['headers'] : false ;
            unset($coggins_post);

            if (isset($headers) && !empty($headers['api-status']) && ( $headers['api-status'] == true )) {
                $result->data    = true;
                $result->success = true;
                $result->message = ( !empty($headers['api-message']) ) ? $headers['api-message'] : 'Bundle deleted' ;
                $this->session->set_flashdata('message', $result->message);
            } else {
                $result->data    = false;
                $result->success = false;
                $result->message = ( !empty($headers['api-message']) ) ? $headers['api-message'] : 'Bundle not deleted' ;
                $this->session->set_flashdata('message', $result->message);
            }
        }
        return $result;
    }


    /**
    *   Cancel a single distribution from the RUNNING distribution, in compare to queueDelete - only from the queue
    */
    public function cancel($account_id = false, $bundle_uid = false)
    {
        $result = (object)[
            'data'      => false,
            'success'   => false,
            'message'   => ''
        ];

        if (!empty($account_id) && !empty($bundle_uid)) {
            $executed = $headers = false;

            $url_endpoint   = 'cdsCancel';
            $method_type    = 'POST';
            $data           = [];
            $data           = [
                'distid' => $bundle_uid,
            ];

            $coggins_post   = $this->coggins_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);

            $debugging_data = [
                "full_response" => json_encode($coggins_post),
                "api-status"    => ( !empty($coggins_post['headers']['api-status']) ) ? $coggins_post['headers']['api-status'] : '' ,
                "api-code"      => ( !empty($coggins_post['headers']['api-code']) ) ? $coggins_post['headers']['api-code'] : '' ,
                "api-message"   => ( !empty($coggins_post['headers']['api-message']) ) ? $coggins_post['headers']['api-message'] : '' ,
                "date"          => ( !empty($coggins_post['headers']['date']) ) ? $coggins_post['headers']['date'] : '' ,
            ];

            // $debug = $this->save_debugging_data( $account_id, $url_endpoint, $data, $debugging_data );

            $executed       = $coggins_post['executed'];
            $headers        = ( !empty($coggins_post['headers']) ) ? $coggins_post['headers'] : false ;
            unset($coggins_post);

            if (isset($headers) && !empty($headers['api-status']) && ( $headers['api-status'] == true )) {
                $result->data    = true;
                $result->success = true;
                $result->message = ( !empty($headers['api-message']) ) ? $headers['api-message'] : 'Bundle deleted' ;
                $this->session->set_flashdata('message', $result->message);
            } else {
                $result->data    = false;
                $result->success = false;
                $result->message = ( !empty($headers['api-message']) ) ? $headers['api-message'] : 'Bundle not deleted' ;
                $this->session->set_flashdata('message', $result->message);
            }
        }
        return $result;
    }



    /**
    *   Cancel a running distribution
    */
    public function queue_cancel($account_id = false, $queue_id = false)
    {
        $result = (object)[
            'data'      => false,
            'success'   => false,
            'message'   => ''
        ];

        if (!empty($account_id) && !empty($queue_id)) {
            $executed = $headers = false;

            $url_endpoint   = 'queueCancel';
            $method_type    = 'POST';
            $data           = [];
            $data           = [
                'queueId'   => $queue_id,
            ];

            $coggins_post   = $this->coggins_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);

            $debugging_data = [
                "full_response" => json_encode($coggins_post),
                "api-status"    => ( !empty($coggins_post['headers']['api-status']) ) ? $coggins_post['headers']['api-status'] : '' ,
                "api-code"      => ( !empty($coggins_post['headers']['api-code']) ) ? $coggins_post['headers']['api-code'] : '' ,
                "api-message"   => ( !empty($coggins_post['headers']['api-message']) ) ? $coggins_post['headers']['api-message'] : '' ,
                "date"          => ( !empty($coggins_post['headers']['date']) ) ? $coggins_post['headers']['date'] : '' ,
            ];

            $debug = $this->save_debugging_data($account_id, $url_endpoint, $data, $debugging_data);

            $executed       = $coggins_post['executed'];
            $headers        = ( !empty($coggins_post['headers']) ) ? $coggins_post['headers'] : false ;
            unset($coggins_post);

            if (isset($headers) && !empty($headers['api-status']) && ( $headers['api-status'] == true )) {
                $result->data    = true;
                $result->success = true;
                $result->message = ( !empty($headers['api-message']) ) ? $headers['api-message'] : 'Distribution cancelled' ;
                $this->session->set_flashdata('message', $result->message);
            } else {
                $result->data    = false;
                $result->success = false;
                $result->message = ( !empty($headers['api-message']) ) ? $headers['api-message'] : 'Distribution not cancelled' ;
                $this->session->set_flashdata('message', $result->message);
            }
        }
        return $result;
    }



    /**
    *   Upload Files to AWS using Coggins
    *   // A further verification of the film file set could be added
    */
    public function aws_upload($account_id = false, $bucket_name = false, $films_data = false, $cacti_id = false)
    {
        $result = (object)[
            'data'      => false,
            'success'   => false,
            'message'   => ''
        ];

        if (!empty($account_id) && !empty($bucket_name) && !empty($films_data) && !empty($cacti_id)) {
            $executed = $headers = false;

            $url_endpoint   = 'awsUpload';
            $method_type    = 'POST';
            $data           = [];
            $data           = [
                'bucket'        => $bucket_name,
                'cactiId'       => $cacti_id,
                'films'         => $films_data,
            ];

            $coggins_post   = $this->coggins_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);
            $executed       = $coggins_post['executed'];
            $headers        = ( !empty($coggins_post['headers']) ) ? $coggins_post['headers'] : false ;

// This is debugging - intentionally ill formatted - Coggins won't return 'data' section this time!!!!
            $debugging_data = [
            "full_response" => json_encode($coggins_post),
            "api-status"    => ( !empty($coggins_post['headers']['api-status']) ) ? $coggins_post['headers']['api-status'] : '' ,
            "api-code"      => ( !empty($coggins_post['headers']['api-code']) ) ? $coggins_post['headers']['api-code'] : '' ,
            "api-message"   => ( !empty($coggins_post['headers']['api-message']) ) ? $coggins_post['headers']['api-message'] : '' ,
            "date"          => ( !empty($coggins_post['headers']['date']) ) ? $coggins_post['headers']['date'] : '' ,
            ];

            $debug = $this->save_debugging_data($account_id, $url_endpoint, $data, $debugging_data);

            ## 'Debug' - before this section!
            unset($coggins_post);

            if (isset($headers) && !empty($headers['api-status']) && ( $headers['api-status'] != false )) {
                $result->data    = true;
                $result->success = true;
                $result->message = ( !empty($headers['api-message']) ) ? $headers['api-message'] : 'AWS Upload has been scheduled' ;
                $this->session->set_flashdata('message', $result->message);
            } else {
                $result->data    = false;
                $result->success = false;
                $result->message = ( !empty($headers['api-message']) ) ? $headers['api-message'] : "AWS Upload Hasn't been scheduled" ;
                $this->session->set_flashdata('message', $result->message);
            }
        }
        return $result;
    }


    /**
    *   A Webhook after uploading files to AWS to update the uploaded files statuses
    *   // the core of the method should be moved into the 'content model' for the clarity of the code - no time now (11/10/2021)
    */
    public function aws_upload_webhook($account_id = false, $request_data = false)
    {
        $result = (object)[
            'data'      => false,
            'success'   => false,
            'message'   => ''
        ];

        if (!empty($account_id) && !empty($request_data)) {
            ## nested data
            $credentials    = ( !empty($request_data['credentials']) ) ? $request_data['credentials'] : false ;
            $ids            = ( !empty($request_data['ids']) ) ? $request_data['ids'] : false ;
            $state          = ( !empty($request_data['state']) ) ? $request_data['state'] : false ;

            ## not nested
            $file_name      = ( !empty($request_data['name']) ) ? $request_data['name'] : false ;

            ## top level data:
            $account_id     = ( !empty($credentials['accountId']) ) ? (int) $credentials['accountId'] : false ;
            $cacti_file_id  = ( !empty($ids['cactiFileId']) ) ? (int) $ids['cactiFileId'] : false ;
            $cacti_asset_id = ( !empty($ids['cactiAssetId']) ) ? (int) $ids['cactiAssetId'] : false ;
            $airtime_id     = ( !empty($ids['airtimeId']) ) ? $ids['airtimeId'] : false ;
            $upload_status  = ( !empty($state['status']) ) ? strtolower($state['status']) : false ;
            $destination    = ( !empty($state['destination']) ) ? strtolower($state['destination']) : false ;

            ## Request tracking
            $debug_insert_id        = false;
            $aws_webhook_debug_data = [
                "account_id"        => ( !empty($account_id) ) ? $account_id : false ,
                "cacti_file_id"     => ( !empty($cacti_file_id) ) ? $cacti_file_id : '' ,
                "cacti_asset_id"    => ( !empty($cacti_asset_id) ) ? $cacti_asset_id : '' ,
                "status"            => ( !empty($upload_status) ) ? $upload_status : false ,
                "provided_data"     => json_encode($request_data)
            ];
            $this->db->insert("tmp_aws_debugging", $aws_webhook_debug_data);

            if ($this->db->insert_id() > 0) {
                ## The request saved in the DB
                $debug_insert_id    = $this->db->insert_id();
                $files_processed    = [];

                ## we can recognise the file by the CaCTI File ID or by the name. As the name is not required from the Coggins Controller - we use the File ID
                if (!empty($cacti_file_id) && !empty($destination)) {
                    ## We no longer have bundles on it
                    $film_data      = false;
                    $where_cond     = "";


                    $this->db->select("content_decoded_file.*", false);
                    $this->db->select("content_decoded_file_type.type_group `file_type`", false);

                    $this->db->join("content_decoded_file_type", "content_decoded_file_type.type_id = content_decoded_file.decoded_file_type_id", "left");

                    $where_cond     = '( ( content_decoded_file.account_id = ' . $account_id . ') AND ( content_decoded_file.file_id = "' . $cacti_file_id . '" ) )';
                    $film_data      = $this->db->get_where("content_decoded_file", $where_cond)->row();

                    if (!empty($film_data)) {
                        ## I need to update a real file
                        if (!empty($film_data->file_type) && !empty($film_data->file_id)) {
                            $real_file_updated = false;

                            ## the 2 sections (movies vs images) have been initially implemented but with the time it has been decided we go by the rule: 1 file in 1 Webhook and it will only be movie type files (feature, trailer). For the future options, we're leaving the check below.
                            if (in_array(strtolower($film_data->file_type), ["movie", "trailer", "movie trailer"])) {
                                ## I'm going to update the original file
                                $real_file_upd_data = [];

                                // $real_file_upd_data["aws_status"] = false;

                                if (!empty($upload_status)) {
                                    if (in_array($upload_status, ["complete", "completed"])) {
                                        if (in_array($destination, ["airtime"])) {
                                            $real_file_upd_data["aws_status"]                   = "airtime_reaching_success";
                                            $real_file_upd_data["airtime_encoded_status"]       = "pending-encoding";
                                            $real_file_upd_data["airtime_encoded_update_date"]  = date('Y-m-d H:i:s');
                                        } elseif (in_array($destination, ["holding"])) {
                                            $real_file_upd_data["aws_status"] = "holding_reaching_success";
                                        } else {
                                            ## we do reset to holding
                                            $real_file_upd_data["aws_status"] = "holding_reaching_success";
                                        }
                                    } else {
                                        if (in_array($destination, ["airtime"])) {
                                            $real_file_upd_data["aws_status"] = "airtime_reaching_error";
                                        } elseif (in_array($destination, ["holding"])) {
                                            $real_file_upd_data["aws_status"] = "holding_reaching_error";
                                        } else {
                                            ## we do reset to holding
                                            $real_file_upd_data["aws_status"] = "holding_reaching_error";
                                        }
                                    }
                                } else {
                                    ## shouldn't happened as required in controller. If it will happened - we leave it as 'false'
                                }

                                $real_file_upd_data["aws_destination"]      = ( !empty($destination) ) ? $destination : false ;
                                $real_file_upd_data["airtime_reference"]    = ( !empty($airtime_id) ) ? $airtime_id : false ;
                                $real_file_upd_data["aws_uploading_date"]   = date('Y-m-d H:i:s');

                                ## only if destination is airtime we're going to update airtime reference update date
                                if (in_array($destination, ['airtime'])) {
                                    $real_file_upd_data["airtime_reference_updating_date"] = date('Y-m-d H:i:s');
                                }

                                $real_file_upd_data["is_on_aws"]            = ( !empty($upload_status) && in_array(strtolower($upload_status), ["complete", "completed"]) ) ? 1 : 0;
                                ## This one was tricky. Explanation to this:
                                // if( in_array( $destination, ['holding'] ){
                                    // if( in_array( $upload_status, ["complete"] ) ){
                                        // "is_on_aws"              => 1,
                                    // } else {
                                        // "is_on_aws"              => 0,
                                    // }
                                // } else { ### destination 'airtime'
                                    // if( in_array( $upload_status, ["complete"] ) ){
                                        // "is_on_aws"              => 1,
                                    // } else {
                                        // "is_on_aws"              => 1, ## we do not know specifically from the Webhook, but we do assume if this went up to the sending to Airtime, it has to be on AWS
                                    // }
                                // }

                                $real_file_upd_where = [];
                                $real_file_upd_where = [
                                    "account_id"            => $account_id,
                                    "file_id"               => $film_data->file_id
                                ];

                                $this->db->update("content_decoded_file", $real_file_upd_data, $real_file_upd_where);

                                // if( $this->db->affected_rows() > 0 ){
                                if ($this->db->trans_status() !== false) {
                                    // main file has been updated
                                    $real_file_updated = true;
                                    $updated_file_data = $this->db->get_where("content_decoded_file", $real_file_upd_where)->row();
                                }
                            } elseif (in_array(strtolower($film_data->file_type), ["subtitles", "standard", "hero"])) {
                                $real_file_upd_data = [];
                                $real_file_upd_data = [
                                    "aws_status"            => ( !empty($upload_status) ) ? $upload_status : false ,
                                    "is_on_aws"             => ( !empty($upload_status) && in_array(strtolower($upload_status), ["complete"]) ) ? 1 : 0,
                                    "aws_uploading_date"    => date('Y-m-d H:i:s'),
                                ];

                                $real_file_upd_where = [];
                                $real_file_upd_where = [
                                    "account_id"            => $account_id,
                                    "document_id"           => $film_data->file_cacti_id
                                ];

                                $this->db->update("content_document_uploads", $real_file_upd_data, $real_file_upd_where);

                                // if( $this->db->affected_rows() > 0 ){
                                if ($this->db->trans_status() !== false) {
                                    // main file has been updated
                                    $real_file_updated = true;
                                    $updated_file_data = $this - db > get_where("content_document_uploads", $real_file_upd_where)->row();
                                }
                            } else {
                                // unknown type of the file - we do not know what table needs to be updated  -JSON??
                            }

                            if ($real_file_updated && $debug_insert_id && $updated_file_data) {
                                $files_processed[$cacti_file_id][] = $updated_file_data;
                            } else {
                                ## the file hasn't got the type or hasn't got the 'real' file id
                            }
                        } else {
                            ## file type or file id is missing
                        }
                    } else {
                        ## the file hasn't been submitted by us - not found in the bundle
                    }

                    $aws_webhook_debug_data = [
                        "account_id"        => ( !empty($account_id) ) ? $account_id : false ,
                        "cacti_file_id"     => $cacti_file_id,
                        "cacti_asset_id"    => $cacti_asset_id,
                        "status"            => ( !empty($upload_status) ) ? $upload_status : false ,
                        "provided_data"     => ( !empty(json_encode($files_processed)) ) ? json_encode(["Debugging Output" => $files_processed]) : json_encode(["debugging Output" => "$files_processed is empty"]) ,
                    ];

                    $this->db->insert("tmp_aws_debugging", $aws_webhook_debug_data);

                    if (!empty($files_processed)) {
                        $result->data    = true;
                        // $result->data     = $files_processed; ## we're not giving back anything to Coggins
                        $result->success = true;
                        $result->message = "AWS Webhook has been processed";
                        $this->session->set_flashdata('message', $result->message);
                    } else {
                        $result->data    = false;
                        $result->success = false;
                        $result->message = "AWS Webhook hasn't been processed" ;
                        $this->session->set_flashdata('message', $result->message);
                    }
                } else {
                    $this->session->set_flashdata('message', "Film ID and Destination is required");
                }
            } else {
                $this->session->set_flashdata('message', "Error saving the request");
            }
        } else {
            $this->session->set_flashdata('message', "Missing Account ID or Request Data");
        }
        return $result;
    }



    /*
    *   This is the Webhook from Coggins when the uploaded to Techlive's bucket file has been transferred into Easel's bucket and processed into the Easel system.
    *   The processed file should come with Easel's ID and should be recognizable and updated by and in CaCTI.
    */
    public function lambda_file_process($account_id = false, $data = false)
    {
        $result = (object)[
            'data'      => false,
            'success'   => false,
            'message'   => ''
        ];

        $aws_webhook_debug_data = [
            "account_id"        => ( !empty($account_id) ) ? $account_id : false ,
            "request_name"      => "2nd webhook",
            "request_data"      => ( !empty($data) ) ? json_encode($data) : false ,
            "full_response"     => "CaCTI is going to action the request",
            "api-status"        => ( !empty($data['status']) ) ? $data['status'] : '' ,
            "api-code"          => "",
            "api-message"       => "",
            "date"              => ""
        ];

        $this->db->insert("coggins_debugging", $aws_webhook_debug_data);

        if (!empty($account_id) && !empty($data)) {
            ## nested data
            $credentials    = ( !empty($data['credentials']) ) ? $data['credentials'] : false ;
            $ids            = ( !empty($data['ids']) ) ? $data['ids'] : false ;
            $state          = ( !empty($data['state']) ) ? $data['state'] : false ;

            ## not nested
            $file_name      = ( !empty($data['name']) ) ? $data['name'] : false ;

            ## top level data:
            $account_id     = ( !empty($credentials['accountId']) ) ? (int) $credentials['accountId'] : false ;
            $aws_bundle_id  = ( !empty($ids['cactiAwsBundleId']) ) ? (int) $ids['cactiAwsBundleId'] : false ;
            $cacti_file_id  = ( !empty($ids['cactiFileId']) ) ? (int) $ids['cactiFileId'] : false ;
            $cacti_asset_id = ( !empty($ids['cactiAssetId']) ) ? (int) $ids['cactiAssetId'] : false ;
            ## Airtime VOD Media ID
            $vod_media_id   = ( !empty($ids['airtimeId']) ) ? $ids['airtimeId'] : false ;
            $upload_status  = ( !empty($state['status']) ) ? $state['status'] : false ;

            if (!empty($cacti_file_id)) {
                ## recognize the file
                $this->db->select("content_decoded_file.*", false);

                $arch_where_1 = "( content_decoded_file.archived != 1 or content_decoded_file.archived is NULL )";
                $this->db->where($arch_where_1);
                $this->db->where("content_decoded_file.file_id", $cacti_file_id);

                $query = $this->db->get("content_decoded_file");

                if ($query->num_rows() > 0) {
                    $decoded_file = $query->row_array();

                    if (!empty($vod_media_id)) {
                        $upd_data = [];
                        $upd_data = [
                            "airtime_reference"                 => $vod_media_id,
                            "airtime_reference_updating_date"   => date("Y-m-d H:i:s")
                        ];

                        $upd_where = [];
                        $upd_where = [
                            "file_id"                           => $decoded_file['file_id']
                        ];

                        $this->db->update("content_decoded_file", $upd_data, $upd_where);

                        if ($this->db->affected_rows() > 0) {
                            $result->data = true;
                            $result->success = true;
                            $result->message = "The movie file has been updated with provided data";
                            $this->session->set_flashdata('message', "The movie file has been updated with provided data");
                        } else {
                            $result->message = "The movie file has been updated with provided data";
                            $this->session->set_flashdata('message', "The movie file has been updated with provided data");
                        }
                    } else {
                        $result->message = "There is an issue with updating the file";
                        $this->session->set_flashdata('message', "There is an issue with updating the file");
                    }
                } else {
                    $result->message = "The file with provided ID - " . $cacti_file_id . " not found";
                    $this->session->set_flashdata('message', "The file with provided ID - " . $cacti_file_id . " not found");
                }
            } else {
                $this->session->set_flashdata('message', "Missing CaCTI File ID");
                $result->message = "Missing CaCTI File ID";
            }
        } else {
            $this->session->set_flashdata('message', "Missing Required Data");
            $result->message = "Missing Required Data";
        }

        return $result;
    }


    /**
    *   Transfer Files to Easel AWS using Coggins
    */
    public function aws_transfer1($account_id = false, $assetcode = false, $provider = false, $filename = false)
    {
        $result = (object)[
            'data'      => false,
            'success'   => false,
            'message'   => ''
        ];

        if (!empty($account_id) && !empty($assetcode) && !empty($provider) && !empty($filename)) {
            $executed = $headers = false;

            $url_endpoint   = 'awsTransfer';
            $method_type    = 'POST';
            $data           = [];
            $data           = [
                'assetcode'     => $assetcode,
                'provider'      => $provider,
                'filename'      => $filename,
            ];

            $coggins_post   = $this->coggins_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);

            $executed       = $coggins_post['executed'];
            $headers        = ( !empty($coggins_post['headers']) ) ? $coggins_post['headers'] : false ;

            $debugging_data = [
                "full_response" => json_encode($coggins_post),
                "api-status"    => ( !empty($coggins_post['headers']['api-status']) ) ? $coggins_post['headers']['api-status'] : '' ,
                "api-code"      => ( !empty($coggins_post['headers']['api-code']) ) ? $coggins_post['headers']['api-code'] : '' ,
                "api-message"   => ( !empty($coggins_post['headers']['api-message']) ) ? $coggins_post['headers']['api-message'] : '' ,
                "date"          => ( !empty($coggins_post['headers']['date']) ) ? $coggins_post['headers']['date'] : '' ,
            ];

            $debug = $this->save_debugging_data($account_id, $url_endpoint, $data, $debugging_data);

            ## 'Debug' - before this section!
            unset($coggins_post);

            if (isset($headers) && !empty($headers['api-status']) && ( $headers['api-status'] != false )) {
                $result->data    = true;
                $result->success = true;
                $result->message = ( !empty($headers['api-message']) ) ? $headers['api-message'] : 'AWS Transfer has been initiated' ;
                $this->session->set_flashdata('message', $result->message);
            } else {
                $result->data    = false;
                $result->success = false;
                $result->message = ( !empty($headers['api-message']) ) ? $headers['api-message'] : "AWS Transfer hasn't been initiated" ;
                $this->session->set_flashdata('message', $result->message);
            }
        }
        return $result;
    }
}
