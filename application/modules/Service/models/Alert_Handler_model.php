<?php

namespace Application\Modules\Service\Models;

class Alert_Handler_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('serviceapp/Notification_model', 'notification_service');
    }

    /*
    * Get Alert records by account ID or Site ID or both
    */
    public function get_alerts($account_id = false, $event_tracking_status_id = false, $packet_id = false, $site_id = false, $panel_id = false, $filter='all', $limit=30, $offset=0)
    {
        $result = false;
        if (!empty($account_id)) {
            if (!empty($site_id) || !empty($panel_id)) {
                $packets_data = $this->get_site_packets($account_id, $site_id, $panel_id);

                $packets	  = (!empty($packets_data)) ? array_keys($packets_data) : false;

                if (!empty($packets)) {
                    $this->db->select('response.account_id,response.response_id,response.site_id,transaction_id,response_datetime,packet_id,alarmno,event_type,event,event_site_account_no,event_is_alarm_new,event_alarm_time_stamp,event_sia_code,event_sia_zone_no,site_status,site_status_details,asset.asset_id,asset.asset_name,asset.asset_unique_id', false);
                    $this->db->where('event_type != "heartbeat"');
                    $this->db->where_in('packet_id', $packets);
                    $this->db->order_by('response_id DESC');
                } else {
                    $this->session->set_flashdata('message', 'No alerts found matching your creteria');
                    return false;
                }
            }

            $data = [];
            $this->db->where('response.account_id', $account_id)
                ->join('asset', 'asset.alarm_panel_code = response.packet_id', 'left')
                ->order_by('response_datetime DESC');

            if ($event_tracking_status_id) {
                $this->db->select('response.account_id,response.response_id,response.site_id,transaction_id,response_datetime,packet_id,event_type,asset.*', false);
                $this->db->where('event_site_account_no', $event_tracking_status_id);
            }

            if ($packet_id) {
                $this->db->select('response.account_id,response.response_id,response.site_id,transaction_id,response_datetime,packet_id,alarmno,event_type,event,event_site_account_no,event_is_alarm_new,event_alarm_time_stamp,event_sia_code,event_sia_zone_no,site_status,site_status_details,asset.asset_id,asset.asset_name,asset.asset_unique_id', false);
                $this->db->where('event_type != "heartbeat"');
                $this->db->where('packet_id', $packet_id);
            }

            if ($filter != 'all') {
                $this->db->where('event_type != "heartbeat"');
                if ($filter == 'bad') {
                    $this->db->where_in('event_sia_code', $this->bad_alarm_codes);
                    $this->db->where_in('event_sia_zone_no', $this->sia_zones);
                } elseif ($filter == 'good') {
                    $this->db->where_not_in('event_sia_code', $this->bad_alarm_codes);
                    $this->db->where_in('event_sia_zone_no', $this->sia_zones);
                }
            }

            $limit  = (!empty($limit)) ? (int)$limit : 30;
            $offset = (!empty($offset)) ? (int)$offset : 0;

            $this->db->limit($limit, $offset);

            $query = $this->db->get('response');

            if ($query->num_rows() > 0) {
                if ($event_tracking_status_id) {
                    foreach ($query->result() as $k=>$row) {
                        if (!empty($row->packet_id)) {
                            $data[$row->packet_id] = [
                                'packet_id'=>$row->packet_id,
                                #'events_count'=>( isset($data[$row->packet_id]['events_count']) ) ? ( $data[$row->packet_id]['events_count'] + 1 ) : 1,
                                'events_count'=>$this->_get_packet_event_count($row->packet_id),
                                'asset_name'=>$row->asset_name,
                                #'location'=>$row->panel_location
                            ];
                        }
                    }
                } elseif ($packet_id || $panel_id) {
                    $data = $query->result_array();
                } else {
                    foreach ($query->result() as $k=>$row) {
                        if (!empty($row->packet_id)) {
                            if (strtolower($row->event_type) != 'heartbeat') {
                                $data[$row->event_site_account_no][$row->packet_id][] = (array)$row;
                            } else {
                                //Ignore all records without a packet_id aka Heartbeats
                                //$data['heartbeat'][] = (array)$row;
                            }
                        }
                    }
                }
                $this->session->set_flashdata('message', 'Site alert(s) found');
                $result = $data;
            } else {
                $this->session->set_flashdata('message', 'Site alert(s) not found');
            }
        } else {
            $this->session->set_flashdata('message', 'Main Account ID is required');
        }
        return $result;
    }

    /**Get all packets contained on a particular site **/
    public function get_site_packets($account_id = false, $site_id = false, $alarm_panel_code = false)
    {
        $result = false;
        if ($account_id) {
            $this->db->select('asset.site_id, asset.asset_id, asset.alarm_panel_code', false)
                ->join('site', 'asset.site_id = site.site_id')
                ->join('asset_types', 'asset.asset_type_id = asset_types.asset_type_id')
                ->where_in('asset_types.asset_group', [ 'panel' ]) //Only look for assets of type Panel
                ->where('asset.alarm_panel_code >', 0)
                ->where('asset.account_id', $account_id)
                ->order_by('alarm_panel_code');

            if ($site_id) {
                $this->db->where('asset.site_id', $site_id);
            }

            if ($alarm_panel_code) {
                $this->db->where('asset.alarm_panel_code', $alarm_panel_code);
            }

            $query = $this->db->get('asset');

            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $result[$row->alarm_panel_code] = (array) $row;
                }
                $this->session->set_flashdata('message', 'Site packets found');
            } else {
                $this->session->set_flashdata('message', 'Site packets not found');
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
            $query = $this->db->select('site_id, event_tracking_status_id, site_status ')
                ->where('account_id', $account_id)
                ->where('event_tracking_status_id > 0')
                ->get('site');

            if ($query->num_rows() > 0) {
                $saved_heartbeats = [];
                foreach ($query->result()  as $site) {
                    $conditions = [ 'event_type'=>'sia', 'event_site_account_no'=>$site->event_tracking_status_id, 'event_type'=>'sia',  ];
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
                                    'event'=>'#'.$site->event_tracking_status_id.' | '.$panel_details->packet_id.' | Site still ['.$panel_details->panel_status.'] since last change at '.date('d-m-Y H:i:s', strtotime($panel_details->response_datetime)),
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

    /** Get Site compliance for an account or an individual site **/
    public function get_site_compliance($account_id = false, $site_id = false, $event_tracking_status_id = false, $event_tracking_status_id = false)
    {
        #incompleted return 'Function incomplete'
        $result = null;

        if (!empty($account_id)) {
            $this->db->select('site.site_id, site_event_statuses.*', false)
                ->join('site_statuses', 'site_statuses.status_id = site.status_id', 'left')
                ->join('site_event_statuses', 'site_event_statuses.event_tracking_status_id = site.event_tracking_status_id', 'left')
                ->where('site.account_id', $account_id)
                ->where('site.event_tracking_status_id > 0')
                ->where('site.archived !=', 1)
                ->get('site');

            $result = true;
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

    /** Trigger an alarm action **/
    public function trigger_alert($account_id = false, $packet_id = false, $trigger_type = false)
    {
        $result = false;
        if ($account_id && $packet_id && $trigger_type) {
            $query = $this->db->where('account_id', $account_id)
                ->where('packet_id', $packet_id)
                ->where('site_status', $trigger_type)
                ->where('event_type', 'SIA')
                ->order_by('response_id desc')
                ->limit(1)
                ->get('response');
            if ($query->num_rows() > 0) {
                $copied_row   = $query->result_array()[0];
                $copied_row['alarmno'] 			= (int)$copied_row['alarmno'] + 1;
                $copied_row['timestamp'] 		= date('Y-m-d H:i:s');
                $copied_row['response_datetime']= date('Y-m-d H:i:s').'.'.microtime();
                unset($copied_row['response_id']);
                $this->db->insert('response', $copied_row);

                $qry = $this->db->select('asset.*, site.site_name')
                    ->join('site', 'site.site_id = asset.site_id')
                    ->where('alarm_panel_code', $packet_id)
                    ->get('site_panels');

                if ($qry->num_rows() > 0) {
                    $panel_details = $qry->result()[0];
                    $this->db->where('site_id', $panel_details->site_id)
                        ->update('site', ['site_status'=>$trigger_type ]);


                    $update_time = date('d-m-Y H:i:s A');

                    ## Push a Notification Message
                    if ((strtolower($trigger_type) == 'ok')) {
                        $mtitle = 'Status Restored for Site ID: '.$panel_details->site_id;
                        $message 	= 'Your '.$panel_details->site_name.' site status has been restored! The Site Status is '.strtoupper($trigger_type).' as at '.$update_time;
                        $html_msg 	= 'Your <strong>'.$panel_details->site_name.'</strong> site status has been restored! The Site Status is <strong>'.strtoupper($trigger_type).'</strong> as at <strong>'.$update_time.'</strong>';
                        $hex_color 	= '#5fb760';
                    } else {
                        $mtitle 	= 'ATTENTION! Site ID '.$panel_details->site_id.' has a Fault';
                        $message 	= 'Your '.$panel_details->site_name.' site has gone into '.strtoupper($trigger_type).' as at '.$update_time.', please follow your standard procedure to rectify this.';
                        $html_msg 	= 'Your <strong>'.$panel_details->site_name.'</strong> site has gone into <strong>'.strtoupper($trigger_type).'</strong> as at <strong>'.$update_time.'</strong>, please follow your standard procedure to rectify this.';
                        $hex_color 	= '#C9302C';
                    }

                    $apns_data = [
                        'account_id'=>$account_id,
                        'mtitle'=>$mtitle,
                        'mdesc'=>$message,
                        'html_msg'=>$html_msg,
                        'site_status'=>strtolower($trigger_type),
                        'bg_hex_color'=>$hex_color
                    ];

                    #$this->notification_service->iOS( $account_id, false, $apns_data );
                    $this->notification_service->send_email_notification($account_id, $apns_data);
                }
                $result = $apns_data;
                $this->session->set_flashdata('message', 'Alert triggered successfully!');
            } else {
                $this->session->set_flashdata('message', 'Alert triggered failed!');
            }
        }
        return $result;
    }

    /* Get total count of all alerts on current panel */
    private function _get_packet_event_count($packet_id = false)
    {
        $result = false;
        if (!empty($packet_id)) {
            $result = $this->db->where('packet_id', $packet_id)
                ->from("response")
                ->count_all_results();
        }
        return $result;
    }
}
