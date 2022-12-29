<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Completed Checklists</legend>
			<?php if (!empty($checklists_data)) { ?>
				<table class="table" style="width:100%">
					<tr>
						<th width="30%">CHECKLIST NAME</th>
						<th width="25%"><span class="text-center">TOTAL RESPONSES</span></th>
						<th width="15%">COMPLETION STATUS</th>
						<th width="15%">COMPLETED BY</th>
						<th width="15%"><span class="pull-right" >ACTION</span></th>
					</tr>
					<?php if (!empty($checklists_data)) {
					    foreach ($checklists_data as $checklist_responses) { ?>
						<tr class>
							<th><?php echo $checklist_responses->checklist_desc; ?></a></th>
							<td><span class="text-center"><?php echo !empty($checklist_responses->responses_data) ? count($checklist_responses->responses_data) : 0; ?></span></td>
							<td><i class="far fa-check-circle text-green"></i> Completed</td>
							<td><?php echo ($checklist_responses->completed_by) ? $checklist_responses->completed_by : ''; ?></td>
							<td><a data-checklist_id="<?php echo $checklist_responses->checklist_id; ?>" class="pull-right pointer view-resps" >View Responses</a></td>
						</tr>
						
						<tr class="resp-<?php echo $checklist_responses->checklist_id; ?>" style="display:none" >
							<td colspan="5" >
								<table class="table" style="width:100%">
									<tr>
										<th width="2%">#</th>
										<th width="58%">Question</th>
										<th width="15%">Response</th>
										<th width="10%">Status</th>
										<th width="15%"><span class="pull-right">Timestamp</span></th>
									</tr>

									<?php if (!empty($checklist_responses->responses_data)) {
									    foreach ($checklist_responses->responses_data as $response) { ?>
										<tr>
											<td width="2%"><?php echo $response->response_question_order; ?></td>
											<td width="58%"><?php echo $response->response_question_prompt; ?></td>
											<td width="15%"><?php echo $response->response_answer; ?></td>
											<td width="10%"><i class="far fa-check-circle text-green"></i> Completed</td>
											<td width="15%"><span class="pull-right"><?php echo (valid_date($response->response_last_update)) ? date('d-m-Y H:i:s', strtotime($response->response_last_update)) : date('d-m-Y H:i:s', strtotime($response->created_on)) ?></span></td>
										</tr>
									<?php }
									    } ?>									
								</table>
							</td>
						</tr>
					<?php }
					    } ?>
				</table>
			<?php } else { ?>
				<?php echo $this->config->item('no_records'); ?>
			<?php } ?>
		</div>		
	</div>
</div>

<script>
	$( document ).ready(function(){
		
		$( '.view-resps' ).click( function( event ){
			var checkListId = $( this ).data( 'checklist_id' );
			$( '.resp-'+checkListId ).slideToggle( 'slow' );
		});
		
	});
</script>