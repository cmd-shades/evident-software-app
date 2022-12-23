<!--   Invoice Start	 -->
<!--- 1st page start -->
<div style="font-family: Arial, Helvetica, sans-serif; margin-top: 60px;">
	<table style="width: 100%; display:none">
		<tr>
			<td style="text-align: center;">
			<form action="upload_image.php" method="post" enctype="multipart/form-data">
			  <label for="file-input">
				<img src="<?php echo $document_setup['company_details']['company_logo']; ?>" width="150px" height="150px"/><br>
				<center><text style="font-size: 12px;"><?php echo $document_setup['company_details']['company_slogan']; ?></text></center>
			  </label>
			  <input id="file-input" type="file" name="company_logo" style="visibility:hidden;" onchange="javascript:this.form.submit();"/>
			</form>
			<input type="hidden" id="message" value="<?php echo  isset( $_SESSION['message'] ) ? $_SESSION['message'] : ''; ?>"><br>
			</td>
		</tr>
	</table>
	<p style="text-align: right; margin-top: -20px; color: 	#696969;"><?php echo ( !empty( $document_setup['generic_details']['document_date'] ) ) ? $document_setup['generic_details']['document_date'] : date( 'd jS Y' ); ?></p><br>
	<div style="color: 	#696969; margin-top: -20px;">
		<text><?php echo ( !empty( $document_setup['recipient_details']['address_line1'] ) ) ? ucwords( strtolower( $document_setup['recipient_details']['address_line1'] ) ).'<br>' : ''; ?></text>
		<text><?php echo ( !empty( $document_setup['recipient_details']['address_line2'] ) ) ? ucwords( strtolower( $document_setup['recipient_details']['address_line2'] ) ).'<br>' : ''; ?></text>
		<text><?php echo ( !empty( $document_setup['recipient_details']['address_line3'] ) ) ? ucwords( strtolower( $document_setup['recipient_details']['address_line3'] ) ).'<br>' : ''; ?></text>
		<text><?php echo ( !empty( $document_setup['recipient_details']['address_town'] ) ) ? ucwords( strtolower( $document_setup['recipient_details']['address_town'] ) ).'<br>' : ''; ?></text>
		<text><?php echo ( !empty( $document_setup['recipient_details']['address_postcode'] ) ) ? strtoupper( $document_setup['recipient_details']['address_postcode'] ).'<br>' : ''; ?></text>
	</div>
	<p style="text-align: center; font-size: 20px; color: #9ACD32;">Thank you for your business</p>
	<div style="color: 	#696969;">
		<p style="text-align: left;">Dear Fiona Gardner</p>
		<p style="text-align: left;">Please find enclosed the invoice for the enhancement works completed on your buildings.</p>
		<p style="text-align: left;">Now that the works are complete we will write to your residents to advise them of the Sky Q services that are now available in their properties.</p>
		<p>If you have any residents that contact you directly enquiring about Sky Q please direct them to our <text style="color: #9ACD32;">Dedicated Resident Sales Team on 020 8760 5278</text> who will be on hand to assist them with setting up Sky Q and on hand throughout the process.</p>
		<p>If you would like us to send marketing material to notify your residents that Sky Q services are now available in their properties, please email ldtvmarketing@lovedigitaltv.co.uk. Also feel free to use the PDF attached to this email in any newsletters or communications you are issuing to your residents.</p>
		<p>If you do have any questions or queries around the invoice please call 020 8760 7668.</p>
		<p style="margin-top: 50px;">Kind Regards</p>
		<p>Love Digital TV</p>
	</div>
	<p style="text-align: center; margin-top: 40px; font-size: 12px; font-weight:bold; color: #696969;"><?php echo $document_setup['company_details']['company_slogan']; ?></p>
	<p style="text-align: center; font-size: 12px;"><?php echo $document_setup['company_details']['address_summaryline']; ?></p>
	<p style="text-align: center; margin-top: -10px; font-size: 12px;"><strong>Tel</strong> <?php echo $document_setup['company_details']['telephone']; ?>, <strong>Fax</strong> <?php echo $document_setup['company_details']['fax']; ?>, Registered in England No. <?php echo $document_setup['company_details']['registration_no']; ?>, VAT registered No. <?php echo $document_setup['company_details']['vat_registration_no']; ?></p>
	<p style="text-align: center;">(1)</p>
	<hr style="margin-top: -10px;">
</div>