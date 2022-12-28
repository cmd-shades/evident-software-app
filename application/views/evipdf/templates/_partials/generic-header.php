<?php 
	/* --------------------------------------------------------------------------
	SECURITY WARNING:
	Please remember to use html_escape() around ALL user input fields.
		- Without this check, users can specify LOCAL IMAGES on the server
		to pull, as MPDF reads URLS as well as LOCAL FILES.
		
		- By Adding html_escape we remove ALL included HTML in user entered data.
	----------------------------------------------------------------------------*/ 

	$custom_log 			= !empty( $document_setup['generic_details']['custom_logo'] ) 			 ? $document_setup['generic_details']['custom_logo'] 			: base_url('/assets/images/logos/main-logo-small.png' ) ;
	$custom_log_dimensions  = !empty( $document_setup['generic_details']['custom_log_dimensions'] )  ? $document_setup['generic_details']['custom_log_dimensions'] 	: 'width="80px"';
?>


<table class='logo-table' style='width:100%; border: none;'> <tr> <td align='center'> <img src='<?php echo $custom_log; ?>' <?php echo $custom_log_dimensions; ?>/> </td> </tr> </table>

<div style="font-family: Arial, Helvetica, sans-serif; margin-top: 0px;">
	<p style="text-align: right;; color:#5C5C5C; font-size:85%;">Generated on <?php echo ( !empty( $document_setup['generic_details']['document_date'] ) ) ? $document_setup['generic_details']['document_date'] : date( 'd jS Y' ); ?></p><br>
	<?php if( $document_setup['recipient_details']['show_recipient'] == true ){ ?>
		<div style="color: 	#5C5C5C;">
			<text><?php echo ( !empty( $document_setup['recipient_details']['address_line1'] ) ) ? html_escape( ucwords( strtolower( $document_setup['recipient_details']['address_line1'] )) ).'<br>' : ''; ?></text>
			<text><?php echo ( !empty( $document_setup['recipient_details']['address_line2'] ) ) ? html_escape( ucwords( strtolower( $document_setup['recipient_details']['address_line2'] )) ).'<br>' : ''; ?></text>
			<text><?php echo ( !empty( $document_setup['recipient_details']['address_line3'] ) ) ? html_escape( ucwords( strtolower( $document_setup['recipient_details']['address_line3'] )) ).'<br>' : ''; ?></text>
			<text><?php echo ( !empty( $document_setup['recipient_details']['address_town'] ) ) ? html_escape( ucwords( strtolower( $document_setup['recipient_details']['address_town'] )) ).'<br>' : ''; ?></text>
			<text><?php echo ( !empty( $document_setup['recipient_details']['address_postcode'] ) ) ? html_escape( strtoupper( $document_setup['recipient_details']['address_postcode'] )).'<br>' : ''; ?></text>
		</div>
	<?php } ?>
	<?php if ( !empty( $document_setup['generic_details']['document_name'] ) ){ ?>
		<div style="width:100%;text-align:center;font-size: 20px; color: #0092CD;"><?php echo html_escape($document_setup['generic_details']['document_name']); ?><span style="font-size:90%"></span></div>
	<?php } ?>
	<br>
</div>
<div style="font-size:92%;margin-bottom:20px;">
	<table class="generals_table" cellpadding="4" cellspacing="0" border="0" style="margin-top:0px; font-size:92%;">
		<?php if(!empty($document_setup['document_content']->audit_id)){ ?>
			<tr>
				<td width="30%" align="left"><b>EviDoc ID</b></td>
				<td width="2%"></td>
				<td width="68%" align="left"><?php echo html_escape($document_setup['document_content']->audit_id); ?></td>
			</tr>
		<?php } ?>
		
		<?php if(!empty($document_setup['document_content']->asset_unique_id)){ ?>
			<tr>
				<td width="30%" align="left"><b>EviDoc Reference</b></td>
				<td width="2%"></td>
				<td width="68%" align="left"><?php echo html_escape($document_setup['document_content']->asset_unique_id ); ?> </td>
			</tr>
		<?php } ?>
		
		<?php if(!empty($document_setup['document_content']->job_id )){ ?>
			<tr>
				<td width="30%" align="left"><b>Job ID</b></td>
				<td width="2%"></td>
				<td width="68%" align="left"><?php echo html_escape($document_setup['document_content']->job_id ); ?> </td>
			</tr>
		<?php } ?>
		
		<?php if(!empty( $document_setup['document_content']->asset_info->asset_id )){ ?>
			<tr>
				<td width="30%" align="left"><b>Asset Unique ID</b></td>
				<td width="2%"></td>
				<td width="68%" align="left"><?php echo html_escape( $document_setup['document_content']->asset_info->asset_unique_id ); ?> </td>
			</tr>
			<?php if( !empty( $document_setup['document_content']->asset_info->zone_name ) ){ ?>
			<tr>
				<td width="30%" align="left"><b>Asset Zone</b></td>
				<td width="2%"></td>
				<td width="68%" align="left"><?php echo html_escape( $document_setup['document_content']->asset_info->zone_name ); ?> </td>
			</tr>
			<?php } ?>
			<?php if( !empty( $document_setup['document_content']->asset_info->location_name ) ){ ?>
			<tr>
				<td width="30%" align="left"><b>Asset Location</b></td>
				<td width="2%"></td>
				<td width="68%" align="left"><?php echo html_escape( $document_setup['document_content']->asset_info->location_name ); ?> </td>
			</tr>
			<?php } ?>
			<?php if( !empty( $document_setup['document_content']->asset_info->site_name ) ){ ?>
			<tr>
				<td width="30%" align="left"><b>Asset Linked Site</b></td>
				<td width="2%"></td>
				<td width="68%" align="left"><?php echo html_escape( $document_setup['document_content']->asset_info->site_name ); ?> </td>
			</tr>
			<?php } ?>

		<?php } ?>
		
		<?php if(!empty( $document_setup['document_content']->customer_info->customer_id )){ ?>
			<tr>
				<td width="30%" align="left"><b>Customer ID</b></td>
				<td width="2%"></td>
				<td width="68%" align="left"><?php echo html_escape( $document_setup['document_content']->customer_info->customer_id ); ?> </td>
			</tr>
			<tr>
				<td width="30%" align="left"><b>Customer Name</b></td>
				<td width="2%"></td>
				<td width="68%" align="left"><?php echo html_escape( $document_setup['document_content']->customer_info->customer_full_name ); ?> </td>
			</tr>
			<tr>
				<td width="30%" align="left"><b>Customer Address</b></td>
				<td width="2%"></td>
				<td width="68%" align="left"><?php echo html_escape( $document_setup['document_content']->customer_info->customer_address ); ?> </td>
			</tr>
		<?php } ?>
		
		
		
		<?php if(!empty($document_setup['document_content']->date_created)){ ?>
			<tr>
				<td width="30%" align="left"><b>EviDoc Date</b></td>
				<td width="2%"></td>
				<td width="68%" align="left"><?php echo ( valid_date( $document_setup['document_content']->evidoc_completion_date ) ) ? html_escape( $document_setup['document_content']->evidoc_completion_date ) : html_escape( $document_setup['document_content']->date_created ); ?></td>
			</tr>
		<?php } ?>
		<?php if(!empty($document_setup['document_content']->created_by)){ ?>
			<tr>
				<td width="30%" align="left"><b>Audited By</b></td>
				<td width="2%"></td>
				<td width="68%" align="left"><?php echo html_escape($document_setup['document_content']->created_by); ?></td>
			</tr>
		<?php } ?>
		
	</table>
</div>
<?php if(!empty($response_section)){ ?>
<div class='response-heading' style="margin-top:40px;margin-bottom:20px;">
	<span style='text-transform:uppercase;font-weight:bold;font-size:17px;'><?php echo $response_section; ?></span>
	<div style='margin-top:5px;border-bottom: 2px solid black;'/>
</div>
<?php } ?>