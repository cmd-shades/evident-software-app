<?php

namespace Application\Modules\Service\Models;

class Notification_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->detect = new Mobile_Detect();
    }

    // (Android)API access key from Google API's Console.
    private static $API_ACCESS_KEY = 'AIzaSyDG3fYAj1uW7VB-wejaMJyJXiO5JagAsYI';
    // (iOS) Private key's passphrase.
    private static $passphrase = '';
    // (Windows Phone 8) The name of our push channel.
    private static $channelName = "s1mplys1d";

    /*
    * Store iOS APNS Device token
    */
    public function add_apns_token($apns_data = false)
    {
        $result = false;
        if (!empty($apns_data['account_id']) && !empty($apns_data['user_id']) && !empty($apns_data['device_token'])) {
            $data = $this->_filter_data('apns_tokens', $apns_data);
            if (!empty($data)) {
                $conditons = ['user_id'=>$apns_data['user_id'],'account_id'=>$apns_data['account_id'],'device_token'=>$apns_data['device_token']];
                $get_existing_token = $this->db->get_where('apns_tokens', $conditons)->row();
                if (empty($get_existing_token)) {
                    $this->db->insert('apns_tokens', $data);
                    $this->session->set_flashdata('message', 'Device token added successfully');
                    $result = ($this->db->trans_status() !== false) ? true : false;
                } else {
                    $this->session->set_flashdata('message', 'Device token already exists');
                    $result = false;
                }
            }
        }
        return $result;
    }

    protected function _filter_data($table, $data)
    {
        $filtered_data = array();
        $columns = $this->db->list_fields($table);

        if (is_array($data)) {
            foreach ($columns as $column) {
                if (array_key_exists($column, $data)) {
                    $filtered_data[$column] = $data[$column];
                }
            }
        } elseif (is_object($data)) {
            foreach ($columns as $column) {
                if (array_key_exists($column, $data)) {
                    $filtered_data[$column] = $data->$column;
                }
            }
        }

        return $filtered_data;
    }

    // Sends Push notification for iOS users
    public function iOS($account_id = false, $user_id = false, $data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($data)) {
            $this->db->select('device_token')
                ->where('account_id', $account_id)
                ->where('device_status', 1);

            if ($user_id) {
                $this->db->where('user_id', $user_id);
            }

            $tokens = $this->db->order_by('apns_tokens.id')
                    ->get('apns_tokens');

            if ($tokens->num_rows() > 0) {
                foreach ($tokens->result() as $row) {
                    $data['user_id'] 	= $user_id;
                    $data['account_id'] = $account_id;
                    $pushed = $this->_push_to_ios($data, $row->device_token);
                    $result['push_status'] = $pushed;
                    $result['device_count'] = (isset($result['device_count'])) ? $result['device_count']+1 : 1;
                }

                $this->session->set_flashdata('message', $this->session->flashdata('message'));
            } else {
                $this->session->set_flashdata('message', 'No device tokens found!');
            }
        }
        return $result;
    }

    /*
    * Push Notification to iOS Devices
    */
    private function _push_to_ios($data, $deviceToken)
    {
        $feedbackMsg= 'Push notification failed!';
        if ($deviceToken) {
            $capath  	= FCPATH.APNS_CERTIFICATES_PATH;
            $cert_file  = $capath.APNS_CERTIFICATE;
            $host 		= 'gateway.sandbox.push.apple.com';
            $port 		= 2195;
            $tTimeout 	= 30;
            $tContext = stream_context_create(
                [ 'ssl' => [
                'local_cert'        => $cert_file,
                'cafile'=>$cert_file,
                'capath'=>$capath,
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
                'verify_depth'      => 0 ]]
            );

            // Create the payload body
            $body['aps'] = array(
                'alert' => array(
                    'title' => $data['mtitle'],
                    'body' => $data['mdesc'],
                 ),
                'badge' => 1,
                'sound' => 'Wolf-howl-sound.aiff',
                'content-available' => '1'
            );

            ## Log the notification
            $attached_user = $data['user_id'];
            if (empty($attached_user)) {
                $get_device_details = $this->db->get_where('apns_tokens', ['device_token'=>$deviceToken])->row();
                $attached_user = (!empty($get_device_details->user_id)) ? $get_device_details->user_id : false;
            }

            $log_data = ['user_id'=>$attached_user,'account_id'=>$data['account_id'],'device_token'=>$deviceToken,'msg_title'=>$data['mtitle'],'msg_contents'=>$data['mdesc']];
            $notification_id = $this->_log_apns_notice($log_data);
            if ($notification_id) {
                $body['aps']['notification_id'] = $notification_id;
            }

            // Encode the payload as JSON
            $tBody = json_encode($body);

            $tSocket = stream_socket_client('ssl://'.$host.':'.$port, $error, $errstr, $tTimeout, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $tContext);
            ## Check if we were able to open a socket.
            if (!$tSocket) {
                $feedbackMsg = "APNS Connection Failed: $error $errstr";
                $this->session->set_flashdata('message', $feedbackMsg);
                fclose($tSocket);
                return false;
            }

            ## Build the Binary Notification.
            $tMsg = chr(0) . chr(0) . chr(32) . pack('H*', $deviceToken) . pack('n', strlen($tBody)) . $tBody;

            ## Send the Notification to the Server.
            $tResult = fwrite($tSocket, $tMsg, strlen($tMsg));
            if ($tResult) {
                $log_update['delivery_status'] = 1;
                $feedbackMsg= 'Success: Message delivered to APNS';
                $result 	= true;
            } else {
                $feedbackMsg= 'Failure: Could not deliver message to APNS';
                $result 	= false;
            }

            ## Update Log notice
            if ($notification_id) {
                $log_update['delivery_msg'] = $feedbackMsg;
                $this->_update_apns_log_notice($notification_id, $log_update);
            }

            ## Close the Connection to the Server.
            fclose($tSocket);
        }
        $this->session->set_flashdata('message', $feedbackMsg);
        return $result;
    }

    // Sends Push notification for Android users
    public function android($data, $reg_id)
    {
        $url = 'https://android.googleapis.com/gcm/send';
        $message = array(
            'title' => $data['mtitle'],
            'message' => $data['mdesc'],
            'subtitle' => '',
            'tickerText' => '',
            'msgcnt' => 1,
            'vibrate' => 1
        );

        $headers = array(
            'Authorization: key=' .self::$API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $fields = array(
            'registration_ids' => array($reg_id),
            'data' => $message,
        );

        return $this->useCurl($url, $headers, json_encode($fields));
    }

    // Sends Push's toast notification for Windows Phone 8 users
    public function WP($data, $uri)
    {
        $delay = 2;
        $msg =  "<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
                "<wp:Notification xmlns:wp=\"WPNotification\">" .
                    "<wp:Toast>" .
                        "<wp:Text1>".htmlspecialchars($data['mtitle'])."</wp:Text1>" .
                        "<wp:Text2>".htmlspecialchars($data['mdesc'])."</wp:Text2>" .
                    "</wp:Toast>" .
                "</wp:Notification>";

        $sendedheaders =  array(
            'Content-Type: text/xml',
            'Accept: application/*',
            'X-WindowsPhone-Target: toast',
            "X-NotificationClass: $delay"
        );

        $response = $this->useCurl($uri, $sendedheaders, $msg);

        $result = array();
        foreach (explode("\n", $response) as $line) {
            $tab = explode(":", $line, 2);
            if (count($tab) == 2) {
                $result[$tab[0]] = trim($tab[1]);
            }
        }

        return $result;
    }

    // Curl
    private function useCurl(&$model, $url, $headers, $fields = null)
    {
        // Open connection
        $ch = curl_init();
        if ($url) {
            // Set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'SSLv3');

            // Disabling SSL Certificate support temporarly
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            if ($fields) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            }

            // Execute post
            $result = curl_exec($ch);
            if ($result === false) {
                die('Curl failed: ' . curl_error($ch));
            }

            // Close connection
            curl_close($ch);

            return $result;
        }
    }

    private function _log_apns_notice($notice_data = false)
    {
        if (!empty($notice_data['device_token'])) {
            $data = $this->_filter_data('apns_notifications', $notice_data);
            if (!empty($data)) {
                $this->db->insert('apns_notifications', $data);
                $id = $this->db->insert_id();
            }
        }
        return (!empty($id)) ? $id : false;
    }

    private function _update_apns_log_notice($log_id = false, $data = false)
    {
        if (!empty($log_id) && !empty($data)) {
            $data = $this->_filter_data('apns_notifications', $data);
            $this->db->where('log_id', $log_id)->update('apns_notifications', $data);
        }
        return true;
    }

    public function get_device_notifications($device_token = false, $account_id = false, $user_id = false, $limit = 10, $offset = 0)
    {
        $result = false;

        if (!$device_token && $account_id && !$user_id) {
            $this->session->set_flashdata('message', 'No records found');
            return $result;
        }

        $this->db->select('log_id,user_id,account_id,device_token,sent_timestamp,delivery_status,receipt,receipt_timestamp,msg_title,msg_contents');
        if ($device_token) {
            $this->db->where('device_token', $device_token);
        }
        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }
        if ($account_id) {
            $this->db->where('account_id', $account_id);
        }

        $this->db->where('receipt', 0);
        $this->db->limit($limit);
        $this->db->offset($offset);

        $query = $this->db->order_by('log_id desc')
            ->get('apns_notifications');

        if ($query->num_rows() > 0) {
            $this->session->set_flashdata('message', 'Device notifications found');
            $result = $query->result();
        }
        return $result;
    }

    /**
    * Send an email notification to alert someone
    **/
    public function send_email_notification($account_id = false, $alert_data = false, $user_id = false, $email_address = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($alert_data)) {
            $destination = 'simplysidteam@lovedigitaltv.co.uk';
            if (!empty($email_address)) {
                $destination = $email_address;
            } elseif (!empty($user_id)) {
                $user = $this->ion_auth->get_user_by_id($account_id, $user_id);
                $destination = (!empty($user->email)) ? $user->email : $destination;
            }

            $msg_content = [
                'alert_css_style' =>'background:'.$alert_data['bg_hex_color'].'; background-color:'.$alert_data['bg_hex_color'].'; color:#fff; font-style: italicl; padding:20px; line-height:1.8',
                'salutation' => 'Hello, ',
                'alert_mesage' => $alert_data['html_msg']
            ];

            $email_body = $this->load->view('email_templates/alert/alert_status_notification', $msg_content, true);

            $email_data = [
                'to'=>$destination,
                'from'=>['developmentteam@lovedigitaltv.co.uk','Simply SID Admin'],
                'subject'=>$alert_data['mtitle'],
                'message'=>$email_body
            ];
            $result = $this->mail->send_mail($email_data);
        }
        return $result;
    }
}
