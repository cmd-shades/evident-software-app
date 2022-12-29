<?php

namespace Application\Modules\Service\Models;

class Alert_Manager_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('serviceapp/Notification_model', 'notification_service');
    }

    private $bad_alarm_codes= ["LB","FA","YC","FT"];//Get from db
    private $sia_zones 		= ["8008","8001","9015","9021","9010","8002","9999"]; //Get from db

    /** Get Site compliance for an account or an individual site **/
    public function get_site_compliance($account_id = false, $site_id = false, $event_tracking_status_id = false, $event_tracking_status_id = false)
    {
        $result = null;

        if (!empty($account_id)) {
            $this->db->select('site.site_id, site_event_statuses.*', false)
                ->join('site_statuses', 'site_statuses.status_id = site.status_id', 'left')
                ->join('site_event_statuses', 'site_event_statuses.event_tracking_status_id = site.event_tracking_status_id', 'left')
                ->where('site.account_id', $account_id)
                ->where('site.archived !=', 1);

        // if( !empty( $sia_code ) ){
        // $this->db->where( 'sia_code', $sia_code );
        // }

        // if( !empty( $sia_zone ) ){
        // $this->db->where( 'sia_zone', $sia_zone );
        // }

        // if( !empty( $sia_type ) ){
        // $this->db->where( 'sia_type', $sia_type );
        // }

        // $query = $this->db->order_by( 'LENGTH( sia_zone ), sia_zone' )
        // ->where( '( sia_zone IS NOT NULL AND sia_zone != "" )' )
        // ->where( 'is_active', 1 )
        // ->get( 'sia_codes' );

        // if( $query->num_rows() > 0 ){
        // $data = [];
        // if( $grouped ){
        // foreach( $query->result() as $k => $row ){
        // $data[$row->sia_zone][$row->sia_code][] = $row;
        // }
        // $result = $data;
        // }else{
        // $result = $query->result();
        // }
        // }
        } else {
            $this->session->set_flashdata('message', 'Missing or Invalid Account ID!');
        }

        return $result;
    }

    /** Get list of Sia codes **/
    public function get_sia_codes($code_id = false, $sia_code = false, $sia_zone = false, $sia_type = false, $grouped = true)
    {
        $result = null;

        if (!empty($code_id)) {
            $this->db->where('code_id', $code_id);
        }

        if (!empty($sia_code)) {
            $this->db->where('sia_code', $sia_code);
        }

        if (!empty($sia_zone)) {
            $this->db->where('sia_zone', $sia_zone);
        }

        if (!empty($sia_type)) {
            $this->db->where('sia_type', $sia_type);
        }

        $query = $this->db->order_by('LENGTH( sia_zone ), sia_zone')
            ->where('( sia_zone IS NOT NULL AND sia_zone != "" )')
            ->where('is_active', 1)
            ->get('sia_codes');

        if ($query->num_rows() > 0) {
            $data = [];
            if ($grouped) {
                foreach ($query->result() as $k => $row) {
                    $data[$row->sia_zone][$row->sia_code][] = $row;
                }
                $result = $data;
            } else {
                $result = $query->result();
            }
        }
        return $result;
    }

    /** Save Alarm response **/
    public function save_alarm($account_id = false, $response_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($response_data)) {
            $decoded_xml = simplexml_load_string($response_data);
        }
        return $result;
    }

    /*
    * Prepare Heartbeat data required to save a triggered HB
    */
    public function save_heartbeat($account_id)
    {
        $result = false;
        if ($account_id) {
            $transaction_id = 1 + (int)$this->check_last_transaction_id();
            $sites = [];
            $query = $this->db->select('site_id, event_site_id, site_status ')
                ->where('account_id', $account_id)
                ->where('event_site_id > 0')
                ->get('site');

            if ($query->num_rows() > 0) {
                $saved_heartbeats = [];
                foreach ($query->result()  as $site) {
                    $conditions = [ 'event_type'=>'sia', 'event_site_account_no'=>$site->event_site_id, 'event_type'=>'sia',  ];
                    $latest_events = $this->db->select('MAX(response_id) `response_id`', false)
                        ->where('packet_id IS NOT NULL')
                        ->group_by('packet_id')
                        ->get_where('response', $conditions);
                    #debug( $this->db->last_query(), "print", false );
                    if ($latest_events->num_rows() > 0) {
                        foreach ($latest_events->result() as $response_id) {
                            $panel_details = $this->get_sia_event_by_id($response_id->response_id);
                            if ($panel_details) {
                                $heartbeat = [
                                    'account_id'=>$account_id,
                                    'packet_id'=>$panel_details->packet_id,
                                    'event'=>'#'.$site->event_site_id.' | '.$panel_details->packet_id.' | Site still ['.$panel_details->panel_status.'] since last change at '.date('d-m-Y H:i:s', strtotime($panel_details->response_datetime)),
                                    'event_site_account_no'=>$panel_details->event_site_account_no,
                                    'transaction_id'=>$transaction_id,
                                    'response_datetime'=>$this->get_timestamp_db(),
                                    'event_type'=>'SIA-H',
                                    'site_status'=>($panel_details->panel_status) ? (empty($panel_details->panel_status) ? 'Faulty' : $panel_details->panel_status) : $site->site_status,
                                    'site_id'=>$panel_details->event_site_account_no,
                                    'event_site_account_no'=>$panel_details->event_site_account_no
                                ];

                                $this->db->insert('response', $heartbeat);

                                if ($this->db->trans_status() !== false) {
                                    $saved_heartbeats = $this->db->insert_id();
                                } else {
                                    $saved_heartbeats = false;
                                }
                            } else {
                            }
                        }
                    }
                }

                $result = (!empty($saved_heartbeats)) ? true : false;
            }
        }
        return $result;
    }

    /** Save a feedback log, useful for debugging purposes only, turn on/off as needed **/
    public function save_feedback_log($account_id = false, $packet_id = false, $feedback_msg = false)
    {
        if (!empty($account_id) && !empty($feedback_msg)) {
            $data = [
                'account_id'=>$account_id,
                'packet_id'=>$packet_id,
                'feedback_msg'=>json_encode($feedback_msg)
            ];

            $this->db->insert('save_feedback_log', $data);
        }
        return true;
    }
}
