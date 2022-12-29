<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="profile-details-container">
				<div class="row alert alert-ssid bg-blue" role="alert">
					<div class="profile-overview">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<legend>Risk Profile <span class="pull-right"><span class="edit-risk pointer hide" title="Click to edit this Risk Item profile"><i class="fas fa-pencil-alt"></i></span> &nbsp; <span data-risk_id="<?php echo $risk_details->risk_id; ?>" class="delete-risk-item-btn pointer" title="Click to delete this Job Type profile" ><i class="far fa-trash-alt"></i></span></span></legend>
							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Date Created</label></td>
											<td width="85%"><?php echo (valid_date($risk_details->date_created)) ? date('d-m-Y H:i:s', strtotime($risk_details->date_created)) : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Created By</label></td>
											<td width="85%"><?php echo (!empty($risk_details->record_created_by)) ? ucwords($risk_details->record_created_by) : 'Data not available'; ?></td>
										</tr>
									</table>							
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label><strong>(#) Associated Job Types</strong></label></td>
											<td width="85%"><?php echo !empty($associated_job_types) ? count($associated_job_types, 1) : 0; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Status</label></td>
											<td width="85%"><?php echo ($risk_details->is_active == 1) ? 'Active <i class="far fa-check-circle"></i>' : 'Disabled <i class="far fa-times-circle text-red"></i>'; ?></td>
										</tr>
									</table>							
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<div class="x_panel tile has-shadow">
								<form id="update-job-form" class="form-horizontal">
									<input type="hidden" name="page" value="details" />
									<input type="hidden" name="risk_id" value="<?php echo $risk_details->risk_id; ?>" />
									<legend>Risk Details</legend>
									<div class="input-group form-group">
										<label class="input-group-addon">Risk Code</label>
										<input id="risk_code" name="risk_code" class="form-control" type="text" placeholder="Risk Code" readonly value="<?php echo $risk_details->risk_code; ?>" />
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Risk Rating</label>
										<select name="risk_rating" class="form-control">
											<option>Please select</option>
											<option value="Low" <?php echo (strtolower($risk_details->risk_rating) == 'low') ? 'selected=selected' : ''; ?> >Low</option>
											<option value="Medium" <?php echo (strtolower($risk_details->risk_rating) == 'medium') ? 'selected=selected' : ''; ?> >Medium</option>
											<option value="High" <?php echo (strtolower($risk_details->risk_rating) == 'high') ? 'selected=selected' : ''; ?> >High</option>
										</select>	
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Risk Score</label>
										<input id="risk_score" name="risk_score" class="form-control numbers-only" type="number" placeholder="Risk Score" value="<?php echo $risk_details->risk_score; ?>" />
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Risk Text</label>
										<input name="risk_text" class="form-control" type="text" placeholder="Risk text" value="<?php echo $risk_details->risk_text; ?>" />
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Risk Harm</label>
										<textarea id="risk_harm" name="risk_harm" type="text" class="form-control" rows="3"><?php echo (!empty($risk_details->risk_harm)) ? $risk_details->risk_harm : '' ?></textarea>     
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Persons At Risk</label>
										<input name="persons_at_risk" class="form-control" type="text" placeholder="Persons At Risk" value="<?php echo $risk_details->persons_at_risk; ?>" />
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Residual Risk</label>
										<select name="residual_risk" class="form-control">
											<option>Please select</option>
											<option value="Low" <?php echo (strtolower($risk_details->residual_risk) == 'low') ? 'selected=selected' : ''; ?> >Low</option>
											<option value="Medium" <?php echo (strtolower($risk_details->residual_risk) == 'medium') ? 'selected=selected' : ''; ?> >Medium</option>
											<option value="High" <?php echo (strtolower($risk_details->residual_risk) == 'high') ? 'selected=selected' : ''; ?> >High</option>
										</select>	
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Control Measures</label>
										<textarea id="control_measures" name="control_measures" type="text" class="form-control" rows="3" placeholder="Control Measures" ><?php echo (!empty($risk_details->control_measures)) ? $risk_details->control_measures : '' ?></textarea>     
									</div>
									<div class="input-group form-group">
										<button type="button" class="update-risk-item-btn btn btn-sm btn-success" >Save Changes</button>
									</div>
								</form>
							</div>
						</div>

						<div class="col-md-6 col-sm-6 col-xs-12">
							<div class="x_panel tile has-shadow">
								<legend>Associated Job Types (<?php echo !empty($associated_job_types) ? count($associated_job_types, 1) : 0; ?>)</legend>
								<?php if (!empty($associated_job_types)) { ?>
									<div class="row">
										<?php foreach ($associated_job_types as $job_type) { ?>
											<div class="col-md-3 col-sm-3 col-xs-12">
												<ul class="to_do">
													<li><p><a href="<?php echo base_url('webapp/job/job_types/'.$job_type->job_type_id); ?>" ><?php echo $job_type->job_type; ?></a> <span class="pull-right"><span class="remove-risk pointer" data-job_type_id="<?php echo $job_type->job_type_id; ?>" data-risk_id="<?php echo $job_type->risk_id; ?>" title="Click to remove this Risk from this Job type" ><i class="far fa-trash-alt text-red"></i></span></span></p></li>
												</ul>
											</div>
										<?php } ?>								
									</div>
								<?php } ?>								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){

		$( '.update-risk-item-btn' ).click( function( event ){

			var formID = $( this ).closest( 'form' ).attr( 'id' );
			event.preventDefault();
			var formData = $('#'+formID ).serialize();
			swal({
				title: 'Confirm Risk Item update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/update_risk_item/'.$risk_details->risk_id); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2000
								})
								window.setTimeout(function(){
									location.reload();
								} ,1000);
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


		//Delete Risk Item from
		$('.delete-risk-item-btn').click(function(){

			var riskId = $(this).data( 'risk_id' );
			swal({
				title: 'Confirm delete Risk Item?',
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/delete_risk_item/'.$risk_details->risk_id); ?>",
						method:"POST",
						data:{'page':'details', risk_id:riskId},
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 1500
								})
								window.setTimeout(function(){
									window.location.href = "<?php echo base_url('webapp/job/risks'); ?>";
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
			}).catch( swal.noop )
		});
	});
</script>


