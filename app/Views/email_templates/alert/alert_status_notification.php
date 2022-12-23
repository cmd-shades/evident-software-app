<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>WOLFALERT - Notification System</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <style>
            body {
                font-family: 'Droid Sans', Arial;
            }
        </style>
    </head>

    <body style="margin: 0; padding: 0; font-family: 'Droid Sans', Arial;">
        <table align="center" border="0" cellpadding="5" cellspacing="0" width="80%">
            <tr>
                <td colspan="4"><?php echo ( !empty($salutation) ) ? $salutation : 'Hello,'?></td>
            </tr>
            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="4" style="<?php echo ( !empty($alert_css_style) ) ? $alert_css_style : ''; ?>" ><?php echo ( !empty($alert_mesage) ) ? $alert_mesage : 'If you\'re seeing this message, then something has gone wrong with the system. Please contact your system administrator or email <a href="mailto:support@simplysid.co.uk">support@simplysid.co.uk</a>!'; ?></td>
            </tr>
            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr><td colspan="4"><br/>This is an auto-generated email, please do not reply to it.<br/><br/>Kind regards.</td></tr>
            <tr><td colspan="4"><br/><br/><strong>Simply SiD System Admin</strong><br/><a href="mailto:alerts@simplysid.co.uk">alerts@simplysid.co.uk</a></td></tr>
        </table>
    </body>
</html>
