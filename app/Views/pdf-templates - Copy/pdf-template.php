<!-- PDF will generate of this page. -->

<!DOCTYPE html>
<html>
    <head>
        <title>Convert HTML to PDF</title>
    </head>
    <body>
    <!--   Invoice Start     -->
        <!--- 1st page start -->
        <div class="header" style="width: 100%; text-align:center; margin:0 auto;">
            <table style="width: 100%;">
                <tr>
                    <td style="text-align: center;">
                    <form action="upload_image.php" method="post" enctype="multipart/form-data">
                      <label for="file-input">
                        <img src="<?php echo 'uploads/'.$documentSetup['company_logo']; ?>" width="150px" height="150px"/><br>
                        <center><text style="font-size: 12px;">Bringing Sky TV to London and the South</text></center>
                      </label>
                      <input id="file-input" type="file" name="company_logo" style="visibility:hidden;" onchange="javascript:this.form.submit();"/>
                    </form>
                    <input type="hidden" id="message" value="<?php echo  isset($_SESSION['message']) ? $_SESSION['message'] : ''; ?>"><br>
                    </td>
                </tr>
            </table>
        </div>
        
        <div style="width: 100%;">
            <p style="text-align: right; margin-top: -20px; color:     #696969;">13 April 2017</p><br>
            <div style="color:     #696969; margin-top: -20px;">
                <text>The Reef</text><br>
                <text>C/O Initiative Property Management</text><br>
                <text>Suite 4, Lansdowne Place</text><br>
                <text>Bournemouth</text><br>
                <text>BH8 8EW</text><br>
            </div>
            <p style="text-align: center; font-size: 20px; color: #9ACD32;">Thank you for your business</p>
            <div style="color:     #696969;">
                <p style="text-align: left;">Dear Fiona Gardner</p>
                <p style="text-align: left;">Please find enclosed the invoice for the enhancement works completed on your buildings.</p>
                <p style="text-align: left;">Now that the works are complete we will write to your residents to advise them of the Sky Q services that are now available in their properties.</p>
                <p>If you have any residents that contact you directly enquiring about Sky Q please direct them to our <text style="color: #9ACD32;">Dedicated Resident Sales Team on 020 8760 5278</text> who will be on hand to assist them with setting up Sky Q and on hand throughout the process.</p>
                <p>If you would like us to send marketing material to notify your residents that Sky Q services are now available in their properties, please email ldtvmarketing@lovedigitaltv.co.uk. Also feel free to use the PDF attached to this email in any newsletters or communications you are issuing to your residents.</p>
                <p>If you do have any questions or queries around the invoice please call 020 8760 7668.</p>
                <p style="margin-top: 50px;">Kind Regards</p>
                <p>Love Digital TV</p>
            </div>
            <p style="text-align: center; margin-top: 40px; font-size: 12px; font-weight:bold; color: #696969;">Bringing Sky TV to London and the South</p>
            <p style="text-align: center; font-size: 12px;">Love Digital TV Ltd, 4th Floor, Grosvenor House, 125 High Street, Croydon, CR0 9XP</p>
            <p style="text-align: center; margin-top: -10px; font-size: 12px;"><strong>Tel</strong> 0845 291 2012, <strong>Fax</strong> 020 8760 5271, Registered in England No. 4442127, VAT registered No. 820 6105 72</p>
            <p style="text-align: center;">(1)</p>
            <hr style="margin-top: -10px;">
        </div>
        <!--- 1st page end -->
        <!--- 2nd page start -->
        <div style="font-family: Arial, Helvetica, sans-serif; margin-top: -20px;">
            <table style="width: 100%;">
                <tr>
                    <td>
                    <form action="upload_image.php" method="post" enctype="multipart/form-data">
                      <label for="file-input">
                        <img src="<?php echo 'uploads/'.$documentSetup['company_logo']; ?>" width="150px" height="150px"/><br>
                        <text style="font-size: 12px;">Bringing Sky TV to London and the South</text>
                      </label>
                      <input id="file-input" type="file" name="company_logo" style="visibility:hidden;" onchange="javascript:this.form.submit();"/>
                    </form>
                    <input type="hidden" id="message" value="<?php echo  isset($_SESSION['message']) ? $_SESSION['message'] : ''; ?>">
                    </td>
                    <td style="text-align: right;">
                        <p align="right" style="margin: 0em;"><?php echo $documentSetup['company_name']; ?></p><br>
                        <p align="right" style=" margin-top: -15px; margin-left: -10px;"><?php echo $documentSetup['address']; ?></p><br>
                        <p align="right" style=" margin-top: -30px;">
                            <?php
                                echo 'Tel: '.$documentSetup['phone'].' Fax: '.$documentSetup['fax'];
                            ?>
                        </p><br>
                        <p align="right" style=" margin-top: -20px;"><?php echo 'VAT Reg. No. '.$documentSetup['vat_registration_no']; ?></p><br>
                        <p align="right" style=" margin-top: -30px;"><?php echo 'Registered in England No. '.$documentSetup['registration_no']; ?></p>
                    </td>
                </tr>
            </table>
            <div style="margin-top: 10px; color:     #696969;">
                <text>The Reef</text><br>
                <text>C/O Initiative Property Management</text><br>
                <text>Suite 4, Lansdowne Place</text><br>
                <text>Bournemouth</text><br>
                <text>BH8 8EW</text><br>
            </div>
            <table style=" margin-top: 15px;">
              <tr style="text-align: left;">
                <th>Invoice Date:</th>
                <td style="padding-left: 150px;">13-04-2017</td> 
              </tr>
              <tr style="text-align: left;">
                <th>Invoice Number:</th>
                <td style="padding-left: 150px;">SID000142</td> 
              </tr>
              <tr style="text-align: left;">
                <th>Block ID:</th>
                <td style="padding-left: 150px;">5241</td> 
              </tr>
              <tr style="text-align: left;">
                <th>Quote Reference:</th>
                <td style="padding-left: 150px;">5241_LDTV5241-160418.1709-Enhancement_MA</td> 
              </tr>
              <tr style="text-align: left;">
                <th>Quote Type:</th>
                <td style="padding-left: 150px;">Enhancement</td> 
              </tr>
              <tr style="text-align: left;">
                <th>Site Namee:</th>
                <td style="padding-left: 150px;">The Reef</td> 
              </tr>
              <tr style="text-align: left;">
                <th>Site Address:</th>
                <td style="padding-left: 150px;">The Reef.16 Boscombe Spa Road</td> 
              </tr>
              <tr style="text-align: left;">
                <th>Site Postcode:</th>
                <td style="padding-left: 150px;">BH5 1AY</td> 
              </tr>
              <tr style="text-align: left;">
                <th>Sage Reference:</th>
                <td style="padding-left: 150px;">INITIATI</td> 
              </tr>
            </table>
            <p style="text-align: center; font-size: 25px; color: #9ACD32;">Your invoice at a glance</p>
                <table style=" margin-top: 20px; width: 60%; " align="center">
                  <tr>
                    <th style="text-align: left; border: 1px solid #000; padding-left: 10px;">Total for Materials:</th>
                    <td style="text-align: right; border: 1px solid #000; padding-right: 10px; width: 30%;">£1.00</td> 
                  </tr>
                  <tr>
                    <th style="text-align: left; border: 1px solid #000; padding-left: 10px;">Total for Labour:</th>
                    <td style="text-align: right; border: 1px solid #000; padding-right: 10px;">£1.00</td> 
                  </tr>
                  <tr>
                    <th style="text-align: left; border: 1px solid #000; padding-left: 10px;">Sky Subsidy:</th>
                    <td style="text-align: right; border: 1px solid #000; padding-right: 10px;">£2.00</td> 
                  </tr>
                  <tr>
                    <th style="text-align: left; border: 1px solid #000; padding-left: 10px;">LDTV Subsidy:</th>
                    <td style="text-align: right; border: 1px solid #000; padding-right: 10px;">£0.00</td> 
                  </tr>
                  <tr>
                    <th style="text-align: left; border: 1px solid #000; padding-left: 10px;">Total charges before VAT:</th>
                    <td style="text-align: right; border: 1px solid #000; padding-right: 10px;">£480.00</td> 
                  </tr>
                  <tr>
                    <th style="text-align: left; border: 1px solid #000; padding-left: 10px;">VAT Payable (@20%):</th>
                    <td style="text-align: right; border: 1px solid #000; padding-right: 10px;">£96.00</td> 
                  </tr>
                  <tr>
                    <th style="text-align: left; border: 1px solid #000; padding-left: 10px;">Grand Total:</th>
                    <td style="text-align: right; border: 1px solid #000; padding-right: 10px;">£576.00</td> 
                  </tr>
                </table>
            <div style="margin-top: 25px;">
                <text>Barclays Bank, Beckenham, Kent</text><br>
                <text>Sort Code: 20 05 57</text><br>
                <text>Account No: 80773743</text><br>
                <text>Account Name: Love Digital TV Limited</text><br>
            </div>
            <p style="text-align: center; font-size: 15px; margin-top: 30px; color: #9ACD32;"><b>Please email remittance to accounts@lovedigitaltv.co.uk</b></p>
            <p style="text-align: center; margin-bottom: -15px;">(2)</p>
            <hr style="margin-top: 20px; margin-bottom: -25px;">
        </div>
        <!--- 2nd page end -->
    <!--   Invoice End     -->
    </body>
</html>