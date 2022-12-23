<?php //debug( $document_setup['document_content'] ); ?>

<?php if( !empty( $document_setup['document_content'] ) ){ ?>
	<div style="font-family: Arial, Helvetica, sans-serif;font-size:12px;" >

		<div class="audit-details">
			<table width="100%" style="">
				<tr>
					<td width="30%"><strong>Audit ID</strong></td>
					<td width="20%"><?php echo $document_setup['document_content']->audit_id; ?></td>
					<td width="20%"><strong>Audit Type</strong></td>
					<td width="30%"><?php echo $document_setup['document_content']->audit_type; ?></td>
				</tr>
				<tr>
					<td width="30%"><strong>Audit Date</strong></td>
					<td width="20%"><?php echo date( 'd-m-Y H:i:s', strtotime( $document_setup['document_content']->date_created ) ); ?></td>
					<td width="30%"><strong>Audited By</strong></td>
					<td width="20%"><?php echo ucwords( $document_setup['document_content']->created_by ); ?></td>
				</tr>
				<tr>
					<td width="30%"><strong>Questions Completed</strong></td>
					<td width="20%"><span style="color:green">&#10003;</span></td>
					<td width="30%"><strong>Documents Uploaded</strong></td>
					<td width="20%"><span style="color:red">&#10007;</span></td>
				</tr>
			</table>
		</div>
		<br/>
		<hr>
		<div class="audit-responses">
			<table width="100%" >
				<tr style="background-color:#DBDBDB; background:#DBDBDB;">
					<td width="100%" style="padding:10px;" colspan="3"><strong>Audit Responses</strong></td>
				</tr>
				<tr>
					<td width="15%" style="padding:10px;"><strong>ID</strong></td>
					<td width="65%" style="padding:10px;"><strong>Question</strong></td>
					<td width="20%" style="padding:10px;"><strong>Response</strong></td>
				</tr>
				<tr>
					<td width="100%" colspan="3">&nbsp;</td>
				</tr>
				<?php if( $document_setup['document_content']->audit_responses ){ ?>
					<?php foreach( $document_setup['document_content']->audit_responses  as $k => $response ){ $k++; ?>
						<tr>
							<td width="15%" style="padding:2px 10px 0 10px;" ><?php echo 'Q'.$k.'.'; ?></td>
							<td width="65%" style="padding:2px 10px 0 10px;" ><?php echo $response->question; ?></td>
							<td width="20%" style="padding:2px 10px 0 10px;" ><?php echo $response->response; ?></td>
						</tr>
					<?php } ?>
				<?php } ?>
				<tr>
					<td width="100%" colspan="3">&nbsp;</td>
				</tr>
				
				<?php if( $document_setup['document_content']->uploaded_docs ){ ?>
					<tr style="background-color:#DBDBDB; background:#DBDBDB;">
						<td width="100%" cellpadding="10" colspan="3"><strong>Audit Photos</strong></td>
					</tr>
					<?php foreach( $document_setup['document_content']->uploaded_docs  as $segment => $segment_files ){ $k++; ?>
						<tr>
							<td width="100%" cellpadding="10" colspan="3"><strong><?php echo ucwords( $segment ); ?></strong></td>
						</tr>
						<tr>
							<td width="100%" style="padding:10px;" colspan="3">
								<div style="width:100%; float:left">
									<?php foreach( $segment_files  as $q => $file ){ $k++; ?>
										<div style="width:100%; padding:10px; height:200px;">
											<div width="100%">
												<div>
													<img src="<?php echo $file->document_link ?>" width="100%" height="300px" />
												</div>
												<div>
													<em><strong>Doc name:</strong> <?php echo $file->document_name; ?></em>
												</div>
											</div>											
										</div>
									<?php } ?>
								</div>
							</td>
						</tr>
					<?php } ?>
				<?php } ?>
			</table>
		</div>

		<div class="audit-images">

		</div>
	</div>
<?php } ?>