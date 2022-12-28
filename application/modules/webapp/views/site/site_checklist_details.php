<style>
	.swal2-container {
	  z-index: 10000;
	}

	table tr th,table tr td{
		padding: 5px 0; 
	}
	
	.accordion .panel:hover {
		background: transparent;
	}
	
	.show_toggled{
		display: none;
	}
	
	@media (max-width: 480px) {
		.btn-info{
			margin-bottom:10px;
		}
	}
</style>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Completed Checklist</legend>
			<div class="accordion" id="accordion" role="tablist" aria-multiselectable="true">
				<?php if( !empty( $checklists_data )){ $counter = 1; ?>
					<?php foreach( $checklists_data as $job_id => $job_checklists_data ){ $counter++; ?>
						<div class="panel">
							<div class="section-container-bar panel-heading collapsed bg-grey pointer no-radius" role="tab" id="heading<?php echo number_to_words( $counter ); ?>" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo number_to_words( $counter ); ?>" aria-expanded="true" aria-controls="collapse<?php echo number_to_words( $counter ); ?>">
								<h4 class="panel-title"><i class="caret-icon fas fa-caret-down text-yellow"></i> JOB ID: <?php echo $job_id; ?> - <?php echo $job_checklists_data->jobs_data->job_type; ?> (<?php echo ( is_object( $job_checklists_data->checklists_data ) ) ? count( object_to_array( $job_checklists_data->checklists_data ) ) : count( $job_checklists_data->checklists_data ) ; ?>)</h4>
							</div>
							<div id="collapse<?php echo number_to_words( $counter ); ?>" class="panel-collapse collapse no-bg no-background <?php echo ( !empty( $toggled_section ) && ( strtolower( lean_string( $toggled_section ) ) == strtolower( lean_string( $job_checklists_data->jobs_data->job_type ) ) ) ) ? 'show_toggled' : ''?>" role="tabpanel" aria-labelledby="heading<?php echo number_to_words( $counter ); ?>" >
								<div class="panel-body">
									<div class="table-responsive">
										<div class="col-md-12 col-sm-12 col-xs-12">
											<?php if( !empty( $job_checklists_data->checklists_data ) ){ ?>
												<table class="table" style="width:100%">
													<tr>
														<th width="30%">CHECKLIST NAME</th>
														<th width="25%"><span class="text-center">TOTAL RESPONSES</span></th>
														<th width="15%">COMPLETION STATUS</th>
														<th width="15%">COMPLETED BY</th>
														<th width="15%"><span class="pull-right" >ACTION</span></th>
													</tr>
													<?php if( !empty( $job_checklists_data->checklists_data ) ){ foreach( $job_checklists_data->checklists_data as $check_id => $checklist_responses ) { ?>
														<tr class>
															<th><?php echo $checklist_responses->checklist_desc; ?></a></th>
															<td><span class="text-center"><?php echo !empty( $checklist_responses->responses_data ) ? count( object_to_array( $checklist_responses->responses_data ) ) : 10; ?></span></td>
															<td><i class="far fa-check-circle text-green"></i> Completed</td>
															<td><?php echo ( $checklist_responses->completed_by ) ? $checklist_responses->completed_by : ''; ?></td>
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

																	<?php if( !empty( $checklist_responses->responses_data ) ){ foreach( $checklist_responses->responses_data as $response ) { ?>
																		<tr>
																			<td width="2%"><?php echo $response->response_question_order; ?></td>
																			<td width="58%"><?php echo $response->response_question_prompt; ?></td>
																			<td width="15%"><?php echo $response->response_answer; ?></td>
																			<td width="10%"><i class="far fa-check-circle text-green"></i> Completed</td>
																			<td width="15%"><span class="pull-right"><?php echo ( valid_date( $response->response_last_update ) ) ? date( 'd-m-Y H:i:s', strtotime( $response->response_last_update ) ) :  date( 'd-m-Y H:i:s', strtotime( $response->created_on ) ) ?></span></td>
																		</tr>
																	<?php } } ?>									
																</table>
															</td>
														</tr>
													<?php } } ?>
												</table>
											<?php } else { ?>
												<?php echo $this->config->item( 'no_records' ); ?>
											<?php } ?>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<br/>
					<?php } ?>
				<?php } else{ ?>
					<div class="no-results">
						<div><?php echo $this->config->item('no_records'); ?></span> &nbsp;Click the button to add a new Question <span><button title="Add new Question to this EviDoc type" style="width:8%" class="add-new-question btn btn-sm btn-success success-shadow"><i class="fas fa-plus-circle" style="font-size: 18px;"></i></button></span></div>							
					</div>
				<?php } ?>	
			</div>
			
		</div>
	</div>
</div>

<script>
	$( document ).ready(function(){
		
		$( '.view-resps' ).click( function( event ){
			var checkListId = $( this ).data( 'checklist_id' );
			$( '.resp-'+checkListId ).slideToggle( 'slow' );
		});
		
		$( '.section-container-bar' ).click( function(){
			$( this ).closest( 'div' ).find( '.caret-icon' ).toggleClass('fa-caret-up fa-caret-down');			
		});
		
	});
</script>