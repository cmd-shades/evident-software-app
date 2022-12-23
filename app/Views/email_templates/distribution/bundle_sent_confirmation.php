<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>CACTI CMS - Bundle Sent Successfully</title>
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
                <td colspan="4" >
                    <p><?php echo ( !empty($introduction) ) ? $introduction : '...'?></p>
                </td>
            </tr>
            <tr>
                <td colspan="4" >
                    <p><strong><?php echo ( !empty($bundle_ref) ) ? $bundle_ref : '' ; ?></strong></p>
                </td>
            </tr>
            <tr>
                <td colspan="4" >
                    <p><?php echo ( !empty($films_introduction) ) ? $films_introduction : '' ; ?></p>
                </td>
            </tr>
            <?php 
            if(!empty($movie_titles) ) { 
                foreach( $movie_titles as $title ){ ?>
                    <tr>
                        <td colspan="4"><b>&nbsp;-&nbsp;</b><?php echo ( !empty($title) ) ? $title : '' ; ?></td>
                    </tr>
                    <?php 
                }
            } ?>
                    
            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="4" >
                    <p><?php echo ( !empty($ending) ) ? $ending : ''?></p>
                </td>
            </tr>
            <tr><td colspan="4"><br/><br/><strong>CaCTi CMS System Admin</strong><br/><a href="mailto:content@techlive.tv ">content@techlive.tv</a></td></tr>
        </table>
    </body>
</html>
