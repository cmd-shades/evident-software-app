<style>

    .response-form {
        border-collapse: collapse;
        width: 100%;
        page-break-inside: avoid;
        margin-top: 20px;
    }
    
    .response-form th, .response-form td{
        border: 1px solid lightgray;
        text-align: left;
		font-weight:400;
    }
	
	th.strong{
		font-weight:600;
	}

	tr.checklist-header{
		background:#5C5C5C;
		background-color:#5C5C5C;
	}
	
	th.checklist-header{
		color: #fff;
		/*color: #f39c12*/ 
	}
</style>
<div>
	<?php foreach ( $responses as $response ) { ?>
		<table class='response-form' cellpadding="5px">
			<tr class="checklist-header">
				<th colspan="3" width="100%" class="checklist-header"><strong><?php echo $response->checklist_desc ?></strong></th>
			</tr>
			<tr>
				<th style="width:5%; font-size:85%;">#</th>
				<th style="width:65%; font-size:85%">Question Prompt</th>
				<th style="width:30% font-size:85%">Response</th>
			</tr>
			<?php foreach( $response->responses_data as $odering => $resp ) { $odering++;?>
				<tr>
					<th style="width:5%; font-size:85%"><?php echo $odering; ?>.</th>
					<th style="width:65%; font-size:85%"><?php echo $resp->response_question_prompt; ?></th>
					<th style="width:30%; font-size:85%"><?php echo $resp->response_answer; ?></th>
				</tr>
			<?php } ?>
		</table>
	<?php } ?>
	
	<!-- ATTACHMENTS -->
	<?php if ( !empty( $document_setup['document_content']->checklist_documents ) )  { ?>
		<div style="font-size:92%;margin-top:20px; border-top: 0.5 dotted #0092CD; ">
			<table class='response-form' cellpadding="5px" cellspacing="0" border="0">
				<tr class="checklist-header">
					<th colspan="3" width="100%" class="checklist-header"><strong>Checklist Attachments</strong></th>
				</tr>
				<?php foreach ( $document_setup['document_content']->checklist_documents as $segment => $documents ) { ?>
					<?php if( in_array( strtolower( $segment ), ['attachments'] ) ){ foreach( $documents as $k => $file ) { ?>
						<tr>
							<td style="width:10%; font-size:85%; border-right:0">&nbsp;</td>
							<td style="width:80%; font-size:85%" >
								<div align="center">
									<img src="<?php echo $file->document_link; ?>" width="100%" />
								</div>
								<div style="float:left; text-align:left;">File name: <?php echo $file->document_name; ?> <?php //echo strtoupper( $file->doc_reference ); ?></div>
							</td>
							<td style="width:10% font-size:85%;  border-left:0">&nbsp;</td>
						</tr>
					<?php } } ?>
				<?php } ?>
			</table>
		</div>
	<?php } ?>
	
	<!-- SIGNATURES -->
	<div style="font-size:92%;margin-top:20px; border-top: 0.5 dotted #0092CD; ">
		<table class="generals_table" cellpadding="4" cellspacing="0" border="0" style="width:100%; margin-top:15px; padding-bottom: 10px; font-size:92%;">
			<tr class="checklist-header">
				<th colspan="3" width="100%" class="checklist-header" cellspacing="1" align="left"><strong>Signatures</strong></th>
			</tr>
			<tr>
				<td width="30%" align="left" style="color: #5c5c5c;"><b>Complete By</b></td>
				<td width="2%"></td>
				<td width="68%" align="left">
					<table width="100%" >
						<tr>
							<td width="40%" align="left"><?php echo trim( $document_setup['document_content']->assignee ); ?></td>
							<td width="60%" align="left"><?php if( !empty( $document_setup['document_content']->engineer_signature ) ){ ?><img src="<?php echo $document_setup['document_content']->engineer_signature; ?>" width="160px" height="60px" /><?php } ?></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td width="30%" align="left" style="color: #5c5c5c;"><b>Customer Name</b></td>
				<td width="2%"></td>
				<td width="68%" align="left">
					<table width="100%">
						<tr>
							<td width="40%" align="left"><?php echo trim( $document_setup['document_content']->customer_name ); ?></td>
							<td width="60%" align="left"><?php if( !empty( $document_setup['document_content']->customer_signature ) ){ ?><img src="<?php echo $document_setup['document_content']->customer_signature; ?>" width="160px" height="60px" /><?php } ?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</div>