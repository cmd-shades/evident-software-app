<?php

namespace App\Libraries;

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

// Server file
class PushNotifications
{
    // (Android)API access key from Google API's Console.
    private static $API_ACCESS_KEY = 'AIzaSyDG3fYAj1uW7VB-wejaMJyJXiO5JagAsYI';
    // (iOS) Private key's passphrase.
    private static $passphrase = '';
    // (Windows Phone 8) The name of our push channel.
    private static $channelName = "s1mplys1d";

    // Change the above three vriables as per your app.

    public function __construct()
    {
        #exit('Init function is not allowed');
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
                'Authorization: key=' . self::$API_ACCESS_KEY,
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
                        "<wp:Text1>" . htmlspecialchars($data['mtitle']) . "</wp:Text1>" .
                        "<wp:Text2>" . htmlspecialchars($data['mdesc']) . "</wp:Text2>" .
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

        // Sends Push notification for iOS users
    public function iOS($data, $devicetoken)
    {

        $deviceToken = $devicetoken;

        $ctx = stream_context_create();

        $cert_file = 'C:\wamp\www\wolfalert\assets\device-certificates\WolfAlertCertificate.pem';

        // $new_cert = $this->format_cert_file($cert_file);

        // debug($new_cert);

        $host       = 'gateway.sandbox.push.apple.com';
        $port       = 2195;
        $timeout    = 30;
        // $context     = stream_context_create(
            // array('ssl'=>array('local_cert'=> $cert_file))
        // );

        $tContext = stream_context_create([ 'ssl' => [
            'local_cert'        => 'C:\wamp\www\wolfalert\assets\device-certificates\WolfAlertCertificate.pem',
            'cafile' => 'C:/wamp/www/wolfalert/assets/device-certificates/WolfAlertCertificate.pem',
            'capath' => 'C:/wamp/www/wolfalert/assets/device-certificates/',
            #'peer_fingerprint'  => openssl_x509_fingerprint(file_get_contents('C:\wamp\www\wolfalert\assets\device-certificates/WolfAlertCertificate.pem')),
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true,
            'verify_depth'      => 0 ]]);

        // Create the payload body
        $body['aps'] = array(
            'alert' => array(
                'title' => $data['mtitle'],
                'body' => $data['mdesc'],
             ),
            'sound' => 'default'
        );

        // Encode the payload as JSON
        $tBody = json_encode($body);

        $tSocket = stream_socket_client('ssl://' . $host . ':' . $port, $error, $errstr, $timeout, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $tContext);
        // Check if we were able to open a socket.
        if (!$tSocket) {
            exit("APNS Connection Failed: $error $errstr" . PHP_EOL);
        }

        // Build the Binary Notification.
        $tMsg = chr(0) . chr(0) . chr(32) . pack('H*', $devicetoken) . pack('n', strlen($tBody)) . $tBody;
        // Send the Notification to the Server.
        $tResult = fwrite($tSocket, $tMsg, strlen($tMsg));
        if ($tResult) {
            echo 'Delivered Message to APNS' . PHP_EOL;
        } else {
            echo 'Could not Deliver Message to APNS' . PHP_EOL;
        }
        // Close the Connection to the Server.
        fclose($tSocket);
        die();
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

    private function format_cert_file($cert_file = false)
    {

        $formatted_cert = '';

        if ($cert_file) {
        }

        return $formatted_cert;
    }
}
