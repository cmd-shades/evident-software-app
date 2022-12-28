<div class="row">
	<div class="col-md-8 col-sm-8 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Audit Responses <span><a target="_blank" style="text-decoration:none" href="<?php echo base_url('/webapp/audit/download/'.$audit_details->audit_id ); ?>" class="pull-right text-red">PDF <i class="fas fa-file-pdf text-red"></i></a></span></legend>
			
			
			<div class="accordion" id="accordion" role="tablist" aria-multiselectable="true">
				<?php if( !empty( $audit_details->audit_responses )){ $counter = 1; ?>
					<?php foreach( $audit_details->audit_responses as $section => $section_data ){ $counter++; ?>
						<div class="panel">
							<div class="section-container-bar panel-heading collapsed bg-grey pointer no-radius" role="tab" id="heading<?php echo number_to_words( $counter ); ?>" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo number_to_words( $counter ); ?>" aria-expanded="true" aria-controls="collapse<?php echo number_to_words( $counter ); ?>">
								<h4 class="panel-title"><i class="caret-icon fas fa-caret-down text-yellow"></i> <?php echo ucwords( $section ); ?> (<?php echo ( is_object( $section_data ) ) ? count( object_to_array( $section_data ) ) : count( $section_data ) ; ?>)</h4>
							</div>
							<div id="collapse<?php echo number_to_words( $counter ); ?>" class="panel-collapse collapse no-bg no-background <?php echo ( !empty( $toggled_section ) && ( strtolower( lean_string( $toggled_section ) ) == strtolower( lean_string( $section ) ) ) ) ? 'show_toggled' : ''?>" role="tabpanel" aria-labelledby="heading<?php echo number_to_words( $counter ); ?>" >
								<div class="panel-body">
									<div class="table-responsive">
										<div class="col-md-12 col-sm-12 col-xs-12">
											<table class="table">
												<thead>
													<tr>
														<th width="10%">ID</th>
														<th width="40%">Question</th>
														<th width="30%">Response</th>
														<th width="20%">Extra Info</th>	
													</tr>
												</thead>
												<?php foreach( $section_data as $k => $response ){ $k++; ?>
													<tr>
														<td><?php echo ( !empty( $k ) ) ? $k.'.' : $response->ordering.'.'; ?></td>
														<td><?php echo $response->question; ?></td>
														<td>
															<?php if( $response->segment == 'documents' ){ ?>
																<!-- Show ticks / crosses -->
																<i class="far <?php echo ( ( $audit_details->documents_uploaded == 1 ) || ( !empty( $response->response ) ) ) ? ' fa-check-circle text-green ' : ' fa-times-circle text-red'; ?>"></i>
																
															<?php }else if( $response->segment == 'signature' ){ ?>
																<?php if( strtolower( $response->response ) == 'signature' ){ ?>
																	<i class="far <?php echo ( !empty( $response->response ) ) ? ' fa-check-circle text-green ' : ' fa-times-circle text-red'; ?>"></i>
																<?php }else{ ?>
																	<span><?php echo ( !empty( $response->response ) ) ? html_escape($response->response) : ''; ?></span>
																<?php } ?>
															<?php }else{ ?>
															
																<!-- Render list responses -->
																<?php if( is_object( $response->response  ) ) { ?>
																	<table width="100%">
																		<?php foreach( $response->response->list  as $zone => $resp ) { ?>
																			<tr>
																				<td width="30%"><?php echo html_escape($zone); ?>:</td><td><span class="pull-left"><?php echo html_escape($resp); ?></span></td>
																			</tr>
																		<?php } ?>
																	</table>
																<?php }else{ ?>
																	<?php echo ( ( strtolower( $response->segment ) == 'signature' ) && ( strtolower( $response->response ) == 'n/a' )  ) ? '<i class="far fa-check-circle text-green"></i>' : html_escape($response->response); ?>									
																<?php } ?>
															<?php } ?>
														</td>
														<td><?php echo ( !empty( $response->response_extra ) ) ? html_escape($response->response_extra) : ''; ?></td>
													</tr>
												<?php } ?>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<br/>
					<?php } ?>
				<?php } else{ ?>
					<div class="no-results">
						<div><?php echo $this->config->item('no_records'); ?></div>							
					</div>
				<?php } ?>	
			</div>
		</div>
	</div>
	
	<div class="col-md-4 col-sm-4 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Audit Completion Information</legend>
			<table style="width:100%" class="responsive">
				<tr>
					<th width="30%">Date audited</th><td width="70%"><?php echo date('d-m-Y H:i:s', strtotime( $audit_details->evidoc_completion_date ) ); ?></td>
				</tr>
				<tr>
					<th width="30%">Audited by</th><td width="70%"><?php echo ucwords( $audit_details->created_by ); ?></td>
				</tr>
				<tr>
					<th width="30%">GPS Location (start)</th><td width="70%"><?php echo ( !empty( $audit_details->gps_latitude ) ) ? $audit_details->gps_latitude : ' - - -'; ?> <?php echo ( !empty( $audit_details->gps_longitude ) ) ? $audit_details->gps_longitude : ' - - - '; ?></td>
				</tr>
				<!-- <tr>
					<th width="50%">GPS Location (finish)</th><td width="50%"><?php echo ( !empty( $audit_details->finish_gps_latitude ) ) ? 'Lat: '.$audit_details->finish_gps_latitude : ' - - -'; ?> <?php echo ( !empty( $audit_details->finish_gps_longitude ) ) ? '/ Long:'.$audit_details->finish_gps_longitude : ' - - - '; ?></td>
				</tr> -->
				<tr>
					<td colspan="2">
						<div class="full-width">
							<iframe width="100%" height="220" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q=<?php echo $audit_details->gps_latitude; ?>,<?php echo $audit_details->gps_longitude; ?>&hl=es;zoom=1&amp;output=embed" ></iframe>
							<!-- <iframe width="210" height="100" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed/v1/view?key=AIzaSyBo10O1NYI8Ppkcluanpr51rec5MpX8MDM&center=51.324034,-0.172468&zoom=18&maptype=roadmap" ></iframe> -->
						</div>				
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		
		$('#new-assignee').change(function(){
			$('#assignee').val( $(this).val() );			
			$('#audit_status option[value="2"]').prop('selected','selected');
			$('#location_id option[value="2"]').prop('selected','selected');
		});
		
		//Re-assign the location based on selected status
		$('#audit_status').change(function(){
			var auditStatus = $(this).val();
			if( auditStatus == 2 ){
				$('#location_id option[value="2"]').prop('selected','selected');
			}else if( auditStatus == 4 ){
				$('#location_id option[value="4"]').prop('selected','selected');
			}else if( auditStatus == 5 ){
				$('#location_id option[value="5"]').prop('selected','selected');
			}
		});
		
		//Submit form for processing
		$( '.update-audit-btn' ).click( function( event ){
					
			event.preventDefault();
			//var formData = $('#update-audit-form').serialize();
			var formData = $(this).closest('form').serialize();
			swal({
				title: 'Confirm audit update?',
				// type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/audit/update_audit/'.$audit_details->audit_id ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){ 
									location.reload();
								} ,3000);							
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}		
						}
					});
				}
			}).catch(swal.noop)
		});
	});
</script>

