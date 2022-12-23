<?php

namespace App\Libraries;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

class Mail
{
    public function __construct()
    {
        $this->mail = new PHPMailer();
        $this->mail->isSMTP();
        $this->mail->SMTPDebug  = false;
        $this->mail->Host       = 'smtp.office365.com';
        $this->mail->Username   = 'notifications@techlive.tv';
        $this->mail->Password   = 'Suk13922';
        $this->mail->Port       = '587';
        $this->mail->SMTPAuth   = true;
        $this->mail->SMTPSecure = 'tls';
        $this->mail->isHTML(true);
    }

    public function send_mail($data, $files = null)
    {

        ob_start();

        $this->mail->clearAllRecipients();
        $this->mail->clearAttachments();
        $this->mail->SetFrom((isset($data['from'][0]) ? $data['from'][0] : 'notifications@Techlive.TV'), ( isset($data['from'][1]) ? $data['from'][1] : 'Cacti CMS Admin'));

        if (is_array($data['to'])) {
            foreach ($data['to'] as $address) {
                $this->mail->AddAddress($address);
            }
        } else {
            $this->mail->AddAddress($data['to']);
        }

        if (!empty($data['cc']) && is_array($data['cc'])) {
            foreach ($data['cc'] as $address) {
                $this->mail->AddCC($address);
            }
        } elseif (isset($data['cc'])) {
            $this->mail->AddCC($data['cc']);
        }

        if (!empty($data['bcc']) && is_array($data['bcc'])) {
            foreach ($data['bcc'] as $address) {
                $this->mail->AddBCC($address);
            }
        } elseif (isset($data['bcc'])) {
            $this->mail->AddBCC($data['bcc']);
        }

        $this->mail->Subject = $data['subject'];
        $this->mail->Body    = $data['message'];

        if (isset($files)) {
            if (is_array($files[0])) {
                foreach ($files as $file) {
                    $this->mail->AddAttachment($file[0], $file[1]);
                }
            } elseif (!empty($files)) {
                $this->mail->AddAttachment($files[0], $files[1]);
            }
        }

        return ($this->mail->send()) ? true : $this->mail->ErrorInfo;
    }
}
