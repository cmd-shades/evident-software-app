<div style="font-size:92%">
	<table class="generals_table" cellpadding="4" cellspacing="0" border="0" style="margin-top:0px; font-size:92%">
		<tr>
			<td width="30%" align="left"><b>Audit ID</b></td>
			<td width="70%" align="left"><?php echo ( !empty( $document_setup['document_content']->audit_id ) ) ? ( $document_setup['document_content']->audit_id ) : '' ; ?></td>
		</tr>
		<tr>
			<td width="30%" align="left"><b>Audit Reference</b></td>
			<td width="70%" align="left"><?php echo ( !empty( $document_setup['document_content']->asset_unique_id ) ) ? ( $document_setup['document_content']->asset_unique_id ) : '' ; ?> - <?php echo ( !empty( $document_setup['document_content']->asset_name ) ) ? ( $document_setup['document_content']->asset_name ) : '' ; ?> </td>
		</tr>
		<!-- <tr>
			<td width="30%" align="left"><b>Audit Type</b></td>
			<td width="70%" align="left"><?php //echo ( ( !empty( $document_setup['document_content']->alt_audit_type ) ) ? ( $document_setup['document_content']->alt_audit_type ) : ( ( !empty( $document_setup['document_content']->audit_type ) ) ? ( $document_setup['document_content']->audit_type ) : '' ) ); ?></td>
		</tr> -->
		<tr>
			<td width="30%" align="left"><b>Audit Date</b></td>
			<td width="70%" align="left"><?php echo ( !empty( $document_setup['document_content']->date_created ) ) ? ( $document_setup['document_content']->date_created ) : '' ; ?></td>
		</tr>
		<tr>
			<td width="30%" align="left"><b>Audited By</b></td>
			<td width="70%" align="left"><?php echo ( !empty( $document_setup['document_content']->created_by ) ) ? ( $document_setup['document_content']->created_by ) : '' ; ?></td>
		</tr>
		<tr>
			<td width="30%" align="left"><b>Questions Completed</b></td>
			<td width="70%" align="left"><?php echo ( !empty( $document_setup['document_content']->audit_responses ) || ( !empty( $document_setup['document_content']->questions_completed ) && ( $document_setup['document_content']->questions_completed == true ) ) ) ? 'Yes' : ''; ?></td>
		</tr>
		<tr>
			<td width="30%" align="left"><b>Documents Uploaded</b></td>
			<td width="70%" align="left"><?php echo ( !empty( $document_setup['document_content']->documents_uploaded ) && ( $document_setup['document_content']->documents_uploaded == true )  ) ? 'Yes' : '' ; ?></td>
		</tr>
		<tr>
			<td width="30%" align="left"><b>Signature Uploaded</b></td>
			<td width="70%" align="left"><?php echo ( !empty( $document_setup['document_content']->signature_uploaded ) && ( $document_setup['document_content']->signature_uploaded == true )  ) ? 'Yes' : '' ; ?></td>
		</tr>
	</table>
	<br>
	<h3 style="font-size:90%" class="title">AUDIT RESPONSES</h3>
	<hr>
	<h3 style="font-size:90%" class="title">&nbsp;</h3>
	<table cellpadding="5" style="border:1px solid #f2f2f2; font-size:92%" >
		<tr>
			<td width="10%" style="border:1px solid #f2f2f2"><strong>ID</strong></td>
			<td width="40%" style="border:1px solid #f2f2f2"><b>Question</b></td>
			<td width="30%" style="border:1px solid #f2f2f2; text-align:left"><b><span style="text-align:left">Response</span></b></td>
			<td width="20%" style="border:1px solid #f2f2f2; text-align:center"><b><span style="text-align:center">Comments</span></b></td>
		</tr>
		<?php foreach( $document_setup['document_content']->audit_responses as $key => $row ){ $key++; ?>
			<tr>
				<td width="10%" style="border:1px solid #f2f2f2"><?php echo 'Q'.$key.'.'; ?></td>
				<td width="40%" style="border:1px solid #f2f2f2"><?php echo $row->question; ?></td>
				<td width="30%" style="border:1px solid #f2f2f2">
					<?php if( is_object( $row->response  ) ) { ?>
						<table width="100%">
							<?php foreach( $row->response->list  as $zone => $resp ) { ?>
								<tr>
									<th width="30%"><strong><?php echo $zone; ?>:</strong></th><td><span class="pull-left"><?php echo $resp; ?></span></td>
								</tr>
							<?php } ?>
						</table>
					<?php }else{ ?>
						<span style=""><?php echo ( in_array( strtolower( $row->response ), ['files','signature'] ) ) ? '<em>See image(s) below</em>' : $row->response; ?></span>
					<?php } ?>
				</td>
				<td width="20%" style="border:1px solid #f2f2f2"><span style="text-align:center"><?php echo $row->response_extra; ?></span></td>
			</tr>
			<?php if( isset( $document_setup['document_content']->uploaded_docs->{$row->question_id} ) ) { ?>
				<tr>
					<td width="100%" colspan="4"><br/><strong style="text-align:center">UPLOADED IMAGES</strong></td>
				</tr>
				<tr>
					<td width="100%" colspan="4">
						<div style="width:100%; margin:0 auto; margin-top:-10px; text-align:center">
							<?php foreach( $document_setup['document_content']->uploaded_docs->{$row->question_id} as $k => $image ) { ?>
								<div style="width:45%; float:left">
									<img width="320px"  src="<?php echo base_url( $image->document_location ); ?>" /><br/>
									<em><strong>Image name:</strong> <?php echo ( !empty( $image->upload_segment ) ) ? ucwords( $image->upload_segment ) : '' ; ?> <?php echo ( !empty( $image->document_name ) ) ? ' - '.$image->document_name : '' ; ?></em>
								</div>
							<?php } ?>
						</div>
					</td>
				</tr>
			<?php } ?>
		<?php } ?>
		
		<?php if ( !empty( $document_setup['document_content']->gps_latitude ) ) { ?>
			<tr>
				<td width="100%" colspan="4">
					<iframe width="400" height="220" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q=<?php echo $document_setup['document_content']->gps_latitude; ?>,<?php echo $document_setup['document_content']->gps_longitude; ?>&hl=es;zoom=1&amp;output=embed" ></iframe>			
				</td>
			</tr>
		<?php } ?>
		<!-- <br>
		<h3 style="font-size:90%" class="title">CONSIDERATIONS</h3>
		<hr>
		<tr>
			<td width="100%" colspan="4"></td>
		</tr> -->
	</table>
</div>