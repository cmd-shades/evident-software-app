<?php 
	/* --------------------------------------------------------------------------
	SECURITY WARNING:
	Please remember to use html_escape() around ALL user input fields.
		- Without this check, users can specify LOCAL IMAGES on the server
		to pull, as MPDF reads URLS as well as LOCAL FILES.
		
		- By Adding html_escape we remove ALL included HTML in user entered data.
	----------------------------------------------------------------------------*/ 

	$custom_log 			= !empty( $document_setup['generic_details']['custom_logo'] ) 			 ? $document_setup['generic_details']['custom_logo'] 			: base_url('/assets/images/logos/main-logo-small.png' ) ;
	$custom_log_dimensions  = !empty( $document_setup['generic_details']['custom_log_dimensions'] )  ? $document_setup['generic_details']['custom_log_dimensions'] 	: 'width="50px"';
?>

<table class='logo-table' style='width:100%; border: none;'> <tr> <td align='center'> <img src='<?php echo $custom_log; ?>' <?php echo $custom_log_dimensions; ?>/> </td> </tr> </table>

<div style="font-family: Arial, Helvetica, sans-serif;">
	<p style="text-align: right;; color:#5C5C5C; font-size:85%;">Generated on <?php echo ( !empty( $document_setup['generic_details']['document_date'] ) ) ? $document_setup['generic_details']['document_date'] : date( 'd jS Y' ); ?></p><br>
	<?php if( $document_setup['recipient_details']['show_recipient'] == true ){ ?>
		<div style="color: 	#5C5C5C; margin-top: -10px;">
			<text><?php echo ( !empty( $document_setup['recipient_details']['address_line1'] ) ) ? html_escape( ucwords( strtolower( $document_setup['recipient_details']['address_line1'] )) ).'<br>' : ''; ?></text>
			<text><?php echo ( !empty( $document_setup['recipient_details']['address_line2'] ) ) ? html_escape( ucwords( strtolower( $document_setup['recipient_details']['address_line2'] )) ).'<br>' : ''; ?></text>
			<text><?php echo ( !empty( $document_setup['recipient_details']['address_line3'] ) ) ? html_escape( ucwords( strtolower( $document_setup['recipient_details']['address_line3'] )) ).'<br>' : ''; ?></text>
			<text><?php echo ( !empty( $document_setup['recipient_details']['address_town'] ) ) ? html_escape( ucwords( strtolower( $document_setup['recipient_details']['address_town'] )) ).'<br>' : ''; ?></text>
			<text><?php echo ( !empty( $document_setup['recipient_details']['address_postcode'] ) ) ? html_escape( strtoupper( $document_setup['recipient_details']['address_postcode'] )).'<br>' : ''; ?></text>
		</div>
	<?php } ?>
	<?php if ( !empty( $document_setup['generic_details']['document_name'] ) ){ ?>
		<div style="width:100%;text-align:center;font-size: 20px; color: #5C5C5C; margin-top: -10px;"><strong><?php echo html_escape($document_setup['generic_details']['document_name']); ?><span style="font-size:90%"></span></strong></div>
	<?php } ?>
	<br>
</div>
<!-- <div style="font-size:92%;margin-bottom:10px; border-top: 0.5 dotted #f39c12; border-bottom: 0.5 dotted #f39c12;"> -->
<div style="font-size:92%;margin-bottom:10px; border-top: 0.5 dotted #0092CD; border-bottom: 0.5 dotted #0092CD;">
	<table class="generals_table" cellpadding="4" cellspacing="0" border="0" style="margin-top:10px; padding-bottom: 10px; font-size:92%;">
		<?php if(!empty($document_setup['document_content']->job_id)){ ?>
			<tr>
				<td width="30%" align="left" style="color: #5c5c5c;"><b>Checklist Ref / Job ID</b></td>
				<td width="2%"></td>
				<td width="68%" align="left"><?php echo html_escape($document_setup['document_content']->job_id); ?></td>
			</tr>
		<?php } ?>
	
		<?php if(!empty( $document_setup['document_content']->site_name )){ ?>
			<tr>
				<td width="30%" align="left" style="color: #5c5c5c;"><b>Site ID</b></td>
				<td width="2%"></td>
				<td width="68%" align="left"><?php echo html_escape( $document_setup['document_content']->site_id ); ?> </td>
			</tr>
			<tr>
				<td width="30%" align="left" style="color: #5c5c5c;"><b>Site Name</b></td>
				<td width="2%"></td>
				<td width="68%" align="left"><?php echo $document_setup['document_content']->site_name; ?> (<?php echo !empty( $document_setup['document_content']->site_reference ) ? $document_setup['document_content']->site_reference : ( !empty( $document_setup['document_content']->site_reference ) ? $document_setup['document_content']->site_reference : ''); ?>)</td>
			</tr>
		<?php } ?>

		<?php if(!empty($document_setup['document_content']->finish_time)){ ?>
			<tr>
				<td width="30%" align="left" style="color: #5c5c5c;"><b>Completion Date</b></td>
				<td width="2%"></td>
				<td width="68%" align="left"><?php echo ( valid_date( $document_setup['document_content']->finish_time ) ) ? date( 'd-m-Y H:i:s', strtotime( $document_setup['document_content']->finish_time ) ) : ''; ?></td>
			</tr>
		<?php } ?>
		
		<?php if(!empty($document_setup['document_content']->assignee)){ ?>
			<!-- <tr>
				<td width="30%" align="left" style="color: #5c5c5c;"><b>Completed By</b></td>
				<td width="2%"></td>
				<td width="68%" align="left">
					<table width="100%" >
						<tr>
							<td width="40%" align="left"><?php echo trim( $document_setup['document_content']->assignee ); ?></td>
							<td width="60%" align="left"><?php if( !empty( $document_setup['document_content']->engineer_signature ) ){ /* ?><img src="<?php echo $document_setup['document_content']->engineer_signature; ?>" width="160px" height="60px" /><?php */ } ?></td>
						</tr>
					</table>
				</td>
			</tr> -->
		<?php } ?>
		
		<?php if(!empty($document_setup['document_content']->customer_name)){ ?>
			<!-- <tr>
				<td width="30%" align="left"><b>Customer Name</b></td>
				<td width="2%"></td>
				<td width="68%" align="left">
					<table width="100%">
						<tr>
							<td width="40%" align="left"><?php echo trim( $document_setup['document_content']->customer_name ); ?></td>
							<td width="60%" align="left"><?php if( !empty( $document_setup['document_content']->customer_signature ) ){ ?><img src="<?php echo $document_setup['document_content']->customer_signature; ?>" width="160px" height="60px" /><?php } ?></td>
						</tr>
					</table>
				</td>
			</tr> -->
		<?php } ?>
		
	</table>
</div>