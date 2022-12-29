<div class="row">
	<div class="col-md-18 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Evidoc Details</legend>
			<?php if (!empty($evidoc_details)) { ?>
				<table style="width:100%">
					<thead>
						<tr>
							<th width="35%">Evidoc Name</th>
							<th width="15%">Group</th>					
							<th width="10%">Category</th>			
							<th width="25%">Total Responses</th>		
							<th width="10%">Action</th>		
						</tr>
						<tr>
							<td class="res-toggler pointer" ><?php echo ucwords($evidoc_details->audit_type);?></td>
							<td class="res-toggler pointer" ><?php echo ucwords($evidoc_details->audit_group.' - '.$evidoc_details->audit_frequency);?></td>
							<td class="res-toggler pointer"><?php echo ucwords($evidoc_details->category_name);?></td>
							<td class="res-toggler pointer"><?php echo !empty($evidoc_details->audit_responses) ? count($evidoc_details->audit_responses) : 0 ;?></td>
							<td><a class="res-toggler pointer" title="Click to view the responses">Responses</a> &nbsp;|&nbsp; <a href="<?php echo base_url('/webapp/audit/profile/'.$evidoc_details->audit_id) ?>" title="Click to open the main Evidoc record" >Open Evidoc</a></td>
						</tr>
					</thead>

					<tr class="evidoc-results" style="display:table-block">
						<td colspan="5" >
							<h6><strong>Evidoc Responses</strong></h6>
							<?php if (!empty($evidoc_details->audit_responses)) { ?>
								<table style="width:100%;">
									<tr class="strong has-shadow bg-grey">
										<td width="10%" style="padding-left:10px;">ID</td>
										<td width="40%">Question</td>
										<td width="30%">Response</td>
										<td width="20%">Extra Info</td>										
									</tr>
									<?php $q_counter = 1;
									foreach ($evidoc_details->audit_responses  as $k => $response) { ?>
										<tr>
											<td style="padding-left:10px;" ><?php echo (!empty($q_counter)) ? $q_counter.'.' : $response->ordering.'.'; ?></td>
											<td><?php echo $response->question; ?></td>
											<td>
												<?php if ($response->segment == 'documents') { ?>
													<!-- Show ticks / crosses -->
													<i class="far <?php echo (($evidoc_details->documents_uploaded == 1) || (!empty($response->response))) ? ' fa-check-circle text-green ' : ' fa-times-circle text-red'; ?>"></i>
													
												<?php } elseif ($response->segment == 'signature') { ?>
													<?php if (strtolower($response->response) == 'signature') { ?>
														<i class="far <?php echo (!empty($response->response)) ? ' fa-check-circle text-green ' : ' fa-times-circle text-red'; ?>"></i>
													<?php } else { ?>
														<span><?php echo (!empty($response->response)) ? $response->response : ''; ?></span>
													<?php } ?>
												<?php } else { ?>
												
													<!-- Render list responses -->
													<?php if (is_object($response->response)) { ?>
														<table width="100%">
															<?php foreach ($response->response->list  as $zone => $resp) { ?>
																<tr>
																	<td width="30%"><?php echo $zone; ?>:</td><td><span class="pull-left"><?php echo $resp; ?></span></td>
																</tr>
															<?php } ?>
														</table>
													<?php } else { ?>
														<?php echo ((strtolower($response->segment) == 'signature') && (strtolower($response->response) == 'n/a')) ? '<i class="far fa-check-circle text-green"></i>' : $response->response; ?>									
													<?php } ?>
												<?php } ?>
											</td>
											<td><?php echo (!empty($response->response_extra)) ? $response->response_extra : ''; ?></td>
										</tr>		
									<?php $q_counter++;
									} ?>
								</table>
							<?php } ?>
							
						</td>
					</tr>
					
				</table>
			<?php } else { ?>
				<div><p><?php echo $this->config->item('no_records'); ?></p></div>
			<?php } ?>
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){
		$( '.res-toggler' ).click( function(){
			 $('.evidoc-results').slideToggle( 'fast' );
		});
	});
</script>