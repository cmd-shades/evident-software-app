<?php /*

	SECURITY WARNING:
	
	Please remember to use html_escape() around ALL user input fields.
		- Without this check, users can specify LOCAL IMAGES on the server
		to pull, as MPDF reads URLS as well as LOCAL FILES.
		
		- By Adding html_escape we remove ALL included HTML in user entered data.

	Jake.

*/ ?>

<style>
    tr {
		
		
        page-break-inside: avoid;
    }
</style>

<div style="font-size:92%">
	<table class="generals_table" cellpadding="4" cellspacing="0" border="0" style="margin-top:0px; font-size:92%">
		<tr>
			<td width="30%" align="left"><b>EviDoc ID</b></td>
			<td width="50px"></td>
			<td width="60%" align="left"><?php echo ( !empty( $document_setup['document_content']->audit_id ) ) ? ( html_escape($document_setup['document_content']->audit_id) ) : '' ; ?></td>
		</tr>
		<tr>
			<td width="30%" align="left"><b>EviDoc Reference</b></td>
			<td width="50px"></td>
			<td width="60%" align="left"><?php echo ( !empty( $document_setup['document_content']->asset_unique_id ) ) ? ( html_escape($document_setup['document_content']->asset_unique_id ) ) : '' ; ?> - <?php echo ( !empty( $document_setup['document_content']->asset_name ) ) ? ( $document_setup['document_content']->asset_name ) : '' ; ?> </td>
		</tr>
		<!-- <tr>
			<td width="30%" align="left"><b>Audit Type</b></td>
			<td width="70%" align="left"><?php //echo ( ( !empty( $document_setup['document_content']->alt_audit_type ) ) ? ( $document_setup['document_content']->alt_audit_type ) : ( ( !empty( $document_setup['document_content']->audit_type ) ) ? ( $document_setup['document_content']->audit_type ) : '' ) ); ?></td>
		</tr> -->
		<tr>
			<td width="30%" align="left"><b>EviDoc Date</b></td>
			<td width="50px"></td>
			<td width="60%" align="left"><?php echo ( !empty( $document_setup['document_content']->date_created ) ) ? ( html_escape($document_setup['document_content']->date_created) ) : '' ; ?></td>
		</tr>
        <tr>
			<td width="30%" align="left"><b>EviDoc Completion Date</b></td>
			<td width="50px"></td>
			<td width="60%" align="left"><?php echo ( !empty( $document_setup['document_content']->finish_time ) ) ? ( html_escape($document_setup['document_content']->created_by) ) : '-' ; ?></td>
		</tr>
		<tr>
			<td width="30%" align="left"><b>EviDoc By</b></td>
			<td width="50px"></td>
			<td width="60%" align="left"><?php echo ( !empty( $document_setup['document_content']->created_by ) ) ? ( html_escape($document_setup['document_content']->created_by) ) : '' ; ?></td>
		</tr>
		<?php /* ?> <tr>
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
		<?php */?>
	</table>
	<br>
	<h3 style="font-size:90%;display: inline;" class="title">EVIDOC RESPONSES</h3>
	<hr>
	<h3 style="font-size:90%" class="title">&nbsp;</h3>
	<table style="font-size:92%;width: 100%" >
		<tr>
			<td width="10%" style="border:1px solid #f2f2f2"><strong>ID</strong></td>
			<td width="40%" style="border:1px solid #f2f2f2"><b>Question</b></td>
			<td width="30%" style="border:1px solid #f2f2f2; text-align:left"><b><span style="text-align:left">Response</span></b></td>
			<td width="20%" style="border:1px solid #f2f2f2; text-align:center"><b><span style="text-align:center">Comments</span></b></td>
		</tr>
		<?php foreach( $document_setup['document_content']->audit_responses as $key => $row ){ $key++; ?>
			<tr>
				<td width="10%" style="border:1px solid #f2f2f2"><?php echo html_escape('Q'.$key.'.'); ?></td>
				<td width="40%" style="border:1px solid #f2f2f2"><?php echo html_escape($row->question); ?></td>
				<td width="30%" style="border:1px solid #f2f2f2">
					<?php if( is_object( $row->response  ) ) { ?>
						<table width="100%">
							<?php foreach( $row->response->list  as $zone => $resp ) { ?>
								<tr>
									<th width="30%"><strong><?php echo html_escape($zone); ?>:</strong></th><td><span class="pull-left"><?php echo html_escape($resp); ?></span></td>
								</tr>
							<?php } ?>
						</table>
					<?php }else{ ?>
						<span style=""><?php echo ( in_array( strtolower( $row->response ), ['files','signature'] ) ) ? '<em>See image(s) below</em>' : html_escape($row->response); ?></span>
					<?php } ?>
				</td>
				<td width="20%" style="border:1px solid #f2f2f2"><span style="text-align:center"><?php echo html_escape($row->response_extra); ?></span></td>
			</tr>
			<?php if( isset( $document_setup['document_content']->uploaded_docs->{$row->question_id} ) ) { ?>
				<tr>
					<td width="100%" colspan="4" style="text-align:center"><br/><strong>UPLOADED IMAGES</strong></td>
				</tr>
					<?php foreach( $document_setup['document_content']->uploaded_docs->{$row->question_id} as $k => $image ) { ?>
					<tr>
	`					<td style="text-align:center;" width="100%" colspan="4">
								<div style="width:45%;">
																<?php 

                                    if(@getimagesize($image->document_location)){?>
                                        <img width="300px" src="<?php echo ( !empty( $image->document_location ) ) ? base_url( $image->document_location ) : '' ; ?>" style="text-align:center;"/>
                                        <?php
                                    } else {
                                            $string = base_url($image->document_location);
                                            $segments = explode('/', $string);
                                            $filename = array_pop($segments);
                                            $filename_ne = explode('.', $filename);
                                            $filename_extension = array_pop($filename_ne);
                                            $filenameComplete = implode('_', $filename_ne) . '.' .  $filename_extension;
                                            $modifiedUrl = implode('/', $segments) . '/' . $filenameComplete;

                                            if(@getimagesize($modifiedUrl)){
                                                ?>
                                                <img width="300px"  src="<?php echo $modifiedUrl ?>" style="text-align:center;"/>
                                                <?php
                                            } else {
                                                echo "<br><small>Missing image file, URL: " . $modifiedUrl . "</small>";
                                            }
                                    } ?>
									<br/><br>
									<em><strong>Image name:</strong> <?php echo ( !empty( html_escape($image->upload_segment) ) ) ? html_escape(ucwords( $image->upload_segment )) : '' ; ?> <?php echo ( !empty( $image->document_name ) ) ? ' - '.$image->document_name : '' ; ?></em>
									
									
									
								</div>
							<br>
						</td>
					</tr>
					<?php } ?>
			<?php } ?>
		<?php } ?>
		<?php if ( !empty( $document_setup['document_content']->gps_latitude ) ) { ?>
			<tr>
				<td width="100%" colspan="4">
					<iframe width="400" height="220" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q=<?php echo $document_setup['document_content']->gps_latitude; ?>,<?php echo html_escape($document_setup['document_content']->gps_longitude); ?>&hl=es;zoom=1&amp;output=embed" ></iframe>
				</td>
			</tr>
		<?php } ?>
	</table>
</div>
