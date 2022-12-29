<div class="row">
	<?php if ($this->user->is_admin || !empty($permissions->can_add) || !empty($permissions->can_view) || !empty($permissions->is_admin)) { ?>
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel tile has-shadow">
				<legend>Job EviDocs</legend>
				
				<?php /* if( !empty( $evidoc_details ) ){ ?>
                    <div class="row">
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
                                    <td class="res-toggler pointer" ><?php echo ucwords( $evidoc_details->audit_type );?></td>
                                    <td class="res-toggler pointer" ><?php echo ucwords( $evidoc_details->audit_group.' - '.$evidoc_details->audit_frequency );?></td>
                                    <td class="res-toggler pointer"><?php echo ucwords( $evidoc_details->category_name );?></td>
                                    <td class="res-toggler pointer"><?php echo !empty( $evidoc_details->audit_responses ) ? count( $evidoc_details->audit_responses ) : 0 ;?></td>
                                    <td><a class="res-toggler pointer" title="Click to view the responses">Responses</a> &nbsp;|&nbsp; <a href="<?php echo base_url( '/webapp/audit/profile/'.$evidoc_details->audit_id ) ?>" title="Click to open the main Evidoc record" >Open Evidoc</a></td>
                                </tr>
                            </thead>

                            <tr class="evidoc-results" style="display:table-block">
                                <td colspan="5" >
                                    <h6><strong>Evidoc Responses</strong></h6>
                                    <?php if( !empty( $evidoc_details->audit_responses ) ){ ?>
                                        <table style="width:100%;">
                                            <tr class="strong has-shadow bg-grey">
                                                <td width="10%" style="padding-left:10px;">ID</td>
                                                <td width="40%">Question</td>
                                                <td width="30%">Response</td>
                                                <td width="20%">Extra Info</td>
                                            </tr>
                                            <?php $q_counter = 1; foreach( $evidoc_details->audit_responses  as $k => $response ){ ?>
                                                <tr>
                                                    <td style="padding-left:10px;" ><?php echo ( !empty( $q_counter ) ) ? $q_counter.'.' : $response->ordering.'.'; ?></td>
                                                    <td><?php echo $response->question; ?></td>
                                                    <td>
                                                        <?php if( $response->segment == 'documents' ){ ?>
                                                            <!-- Show ticks / crosses -->
                                                            <i class="far <?php echo ( ( $evidoc_details->documents_uploaded == 1 ) || ( !empty( $response->response ) ) ) ? ' fa-check-circle text-green ' : ' fa-times-circle text-red'; ?>"></i>

                                                        <?php }else if( $response->segment == 'signature' ){ ?>
                                                            <?php if( strtolower( $response->response ) == 'signature' ){ ?>
                                                                <i class="far <?php echo ( !empty( $response->response ) ) ? ' fa-check-circle text-green ' : ' fa-times-circle text-red'; ?>"></i>
                                                            <?php }else{ ?>
                                                                <span><?php echo ( !empty( $response->response ) ) ? $response->response : ''; ?></span>
                                                            <?php } ?>
                                                        <?php }else{ ?>

                                                            <!-- Render list responses -->
                                                            <?php if( is_object( $response->response  ) ) { ?>
                                                                <table width="100%">
                                                                    <?php foreach( $response->response->list  as $zone => $resp ) { ?>
                                                                        <tr>
                                                                            <td width="30%"><?php echo $zone; ?>:</td><td><span class="pull-left"><?php echo $resp; ?></span></td>
                                                                        </tr>
                                                                    <?php } ?>
                                                                </table>
                                                            <?php }else{ ?>
                                                                <?php echo ( ( strtolower( $response->segment ) == 'signature' ) && ( strtolower( $response->response ) == 'n/a' )  ) ? '<i class="far fa-check-circle text-green"></i>' : $response->response; ?>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </td>
                                                    <td><?php echo ( !empty( $response->response_extra ) ) ? $response->response_extra : ''; ?></td>
                                                </tr>
                                            <?php $q_counter++; } ?>
                                        </table>
                                    <?php } ?>

                                </td>
                            </tr>

                        </table>
                    </div>
                <?php } */ ?>
				
				
				<?php if (!empty($job_evidocs)) {
				    $counter = 1; ?>
					<div class="accordion" id="accordion" role="tablist" aria-multiselectable="true">
						<?php foreach ($job_evidocs as $discipline_id => $discipline_data) {
						    $counter++; ?>
							<div class="panel">
								<div class="section-container-bar panel-heading collapsed bg-grey pointer no-radius" role="tab" id="heading<?php echo number_to_words($counter); ?>" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo number_to_words($counter); ?>" aria-expanded="true" aria-controls="collapse<?php echo number_to_words($counter); ?>">
									<h4 class="panel-title"><i class="caret-icon fas fa-caret-down text-yellow"></i> &nbsp;<img src="<?php echo !empty($discipline_data->discipline_image_url) ? $discipline_data->discipline_image_url : ''; ?>" width="18px" >&nbsp;&nbsp;<?php echo !empty($discipline_data->discipline_name) ? ucwords($discipline_data->discipline_name) : '<span class="text-yellow">DISCIPLIN NOT SET</span>'; ?> <span class="pull-right">(<?php echo !is_array($discipline_data->evidocs) ? count(object_to_array($discipline_data->evidocs)) : count($discipline_data->evidocs) ; ?>)<span></h4>
								</div>
								
								<div id="collapse<?php echo number_to_words($counter); ?>" class="panel-collapse collapse no-bg no-background" role="tabpanel" aria-labelledby="heading<?php echo number_to_words($counter); ?>" >
									<div class="panel-body">
										<table style="width:100%">
											<tr>
												<th width="16%">Evidoc ID</th>
												<th width="20%">Asset Unique ID</th>
												<th width="16%">Status</th>
												<th width="16%">Outcome</th>
												<th width="16%">Date Completed</th>
												<th width="16%"><span class="pull-right">Action</span></th>
											</tr>
											<?php if (!empty($discipline_data->evidocs)) {
											    foreach ($discipline_data->evidocs as $audit_record) { ?>
												<tr>
													<td><a href="<?php echo base_url('webapp/audit/profile/'.$audit_record->audit_id); ?>"><?php echo $audit_record->audit_id; ?></a></td>
													<td><?php echo !empty($audit_record->asset_unique_id) ? $audit_record->asset_unique_id : ''; ?></td>
													<td><?php echo !empty($audit_record->audit_status) ? $audit_record->audit_status : ''; ?></td>
													<td><?php echo !empty($audit_record->result_status) ? $audit_record->result_status : ''; ?></td>
													<td><?php echo (valid_date($audit_record->date_created)) ? date('d-m-Y H:i:s', strtotime($audit_record->date_created)) : ''; ?></td>
													<td>
														<span class="pull-right">
															<a class="res-toggler pointer" title="Click to view the responses" data-class_id="<?php echo $audit_record->audit_id; ?>" >View Responses</a> &nbsp;|&nbsp; <a href="<?php echo base_url('/webapp/audit/profile/'.$audit_record->audit_id) ?>" title="Click to open the main Evidoc record" >Open Evidoc</a>
														</span>
													</td>
												</tr>
												
												<tr id="evidoc-results<?php echo $audit_record->audit_id; ?>"  class="evidoc-results<?php echo $audit_record->audit_id; ?>" style="display:none">
													<td colspan="6" >
														<h6><strong>Evidoc Responses</strong></h6>
														<?php if (!empty($audit_record->audit_responses)) { ?>
															<table style="width:100%;">
																<tr class="strong has-shadow bg-blue">
																	<td width="10%" style="padding-left:10px;">ID</td>
																	<td width="40%">Question</td>
																	<td width="30%">Response</td>
																	<td width="20%">Extra Info</td>										
																</tr>
																<?php $q_counter = 1;
																foreach ($audit_record->audit_responses  as $k => $response) { ?>
																	<tr>
																		<td style="padding-left:10px;" ><?php echo (!empty($q_counter)) ? $q_counter.'.' : $response->ordering.'.'; ?></td>
																		<td><?php echo $response->question; ?></td>
																		<td>
																			<?php if ($response->segment == 'documents') { ?>
																				<!-- Show ticks / crosses -->
																				<i class="far <?php echo (($audit_record->documents_uploaded == 1) || (!empty($response->response))) ? ' fa-check-circle text-green ' : ' fa-times-circle text-red'; ?>"></i>
																				
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
												
											<?php }
											    } else { ?>
												<tr>
													<td colspan="6"><?php echo $this->config->item('no_records'); ?></td>
													<td colspan="6"><?php echo $this->config->item('no_records'); ?></td>
												</tr>
											<?php } ?>
										</table>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
				
				<?php if (empty($evidoc_details) && empty($job_evidocs)) { ?>
					<div><p><?php echo $this->config->item('no_records'); ?></p></div>
				<?php } ?>
				
			</div>		
		</div>
	<?php } ?>
	
</div>

<script>

	$(document).ready(function(){
		
		$( '.res-toggler' ).click( function(){
			var classId = $( this ).data( 'class_id' );
			 $( '#evidoc-results'+classId ).slideToggle( 'fast' );
		});
		
		$( '#discipline_audits_id' ).select2({
			
		});
		
		$( '#add-audit-to-check' ).click( function(){
			
			//Fetch available audits
			
			$( '#audit-to-be-checked-modal-md' ).modal( 'show' );
			
		});
		
		$( '.audit-types-toggle' ).click( function(){
			var typeRef = $( this ).data( 'audit_type_ref' );
			$( '.atype'+typeRef ).closest( 'tr' ).find( '.caret-icon' ).toggleClass('fa-caret-up fa-caret-down');
			$( '.'+typeRef ).slideToggle();
		});
		
		$( '.audit-categories-toggle' ).click( function(){
			var typeRef = $( this ).data( 'audit_category_ref' );
			$( '.acategory'+typeRef ).closest( 'tr' ).find( '.caret-icon' ).toggleClass('fa-caret-up fa-caret-down');
			$( '.'+typeRef ).slideToggle();
		});
		
		$( '.section-container-bar' ).click( function(){
			$( this ).closest( 'div' ).find( '.caret-icon' ).toggleClass('fa-caret-up fa-caret-down');			
		});
		
		//Add selected audits to be added for checking
		$( '#add-audits-to-check-btn' ).click(function( e ){
			
			e.preventDefault();
			
			var formData = $( '#audit-addition-form' ).serialize();
			
			swal({
				title: 'Add selected audits?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/link_audits_to_job'); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 && ( data.audits !== '' ) ){
								$( "#new-job-modal-md" ).modal( 'hide' );
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){
									location.reload();
								} ,1500);							
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
		
		
		//Remove audit from Job
		$('.remove-audit-btn').click(function(){

			var linkID  		= $(this).data( 'id' ),
				jobId			= $(this).data( 'job_id' ),
				auditUniqueId	= $(this).data( 'audit_unique_id' ),
				locationDetails	= $(this).data( 'location_details' );
				
			swal({
				title: 'Confirm unlink Evidoc?',
				html: '<strong>' + auditUniqueId + '</strong> / ' + locationDetails,
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/unlink_audit_from_job/'.$job_details->job_id); ?>",
						method:"POST",
						data:{job_id: jobId, id:linkID},
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