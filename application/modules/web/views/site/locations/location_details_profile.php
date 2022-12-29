<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="profile-details-container">
				<div class="row alert alert-ssid bg-blue" role="alert">
					<div class="profile-overview">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<legend>Location Profile <span class="pull-right"><span class="edit-location pointer hide" title="Click to edit this Location profile"><i class="fas fa-pencil-alt"></i></span> &nbsp; <span class="delete-location-btn pointer" data-location_id="<?php echo $location_details->location_id; ?>" title="Click to delete this Location profile" ><i class="far fa-trash-alt"></i></span></span></legend>
							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<?php
                                        $resident_name = '';
							$resident_name .= (!empty($location_details->resident_salutation)) ? $location_details->resident_salutation : '';
							$resident_name .= (!empty($location_details->resident_first_name)) ? ' '.$location_details->resident_first_name : '';
							$resident_name .= (!empty($location_details->resident_last_name)) ? ' '.$location_details->resident_last_name : '';
							?>
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Location Type</label></td>
											<td width="85%"><?php echo (!empty($location_details->location_type)) ? $location_details->location_type : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Resident Name</label></td>
											<td width="85%"><?php echo ucwords(strtolower($resident_name)); ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Location Zone</label></td>
											<td width="85%"><?php echo (!empty($location_details->location_zone)) ? $location_details->location_zone : ''; ?></td>
										</tr>
									</table>							
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Status</label></td>
											<td width="85%"><?php echo ($location_details->is_active == 1) ? 'Active <i class="far fa-check-circle"></i>' : 'Disabled <i class="far fa-times-circle text-red"></i>'; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Date Created</label></td>
											<td width="85%"><?php echo (valid_date($location_details->date_created)) ? date('d-m-Y H:i:s', strtotime($location_details->date_created)) : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Created By</label></td>
											<td width="85%"><?php echo (!empty($location_details->record_created_by)) ? ucwords($location_details->record_created_by) : 'Data not available'; ?></td>
										</tr>
									</table>							
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<?php include $include_page; ?>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- CREATE NEW JOB MODAL -->
<div id="new-job-modal-md" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title" id="myModalLabel">Create New Job</h4>
			</div>
			<div class="modal-body">
				<div class="">
					<form id="job-creation-form" >
						<input type="hidden"  name = "account_id" 	value="<?php echo $this->user->account_id; ?>" />
						<input type="hidden"  name = "site_id" 		value="<?php echo $location_details->site_id; ?>"/>
						<input type="hidden"  name = "address_id" 	value="<?php echo $location_details->address_id; ?>"/>
						<input type="hidden"  name = "location_id" 	value="<?php echo $location_details->location_id; ?>" />
						<input type="hidden"  name = "page" 		value="locations" />
						<div class="input-group form-group">
							<label class="input-group-addon">Job type</label>
							<select name="job_type_id" class="form-control">
								<option>Please select</option>
								<?php if (!empty($job_types)) {
								    foreach ($job_types as $k => $job_type) { ?>
									<option value="<?php echo $job_type->job_type_id; ?>" ><?php echo $job_type->job_type; ?></option>
								<?php }
								    } ?>
							</select>	
						</div>
						<!-- <div class="input-group form-group">
							<label class="input-group-addon">Job date</label>
							<input name="job_date" class="form-control datepicker" type="text" placeholder="Job date" value="" />
						</div> -->
						<div class="hide input-group form-group">
							<label class="input-group-addon">Job status</label>
							<select class="form-control">
								<option value="">Please select</option>
								<?php if (!empty($job_statuses)) {
								    foreach ($job_statuses as $k => $job_status) { ?>
									<option value="<?php echo $job_status->status_id; ?>" ><?php echo $job_status->job_status; ?></option>
								<?php }
								    } ?>
							</select>	
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Job Duration (Slots)</label>
							<select name="job_duration" class="form-control" >
								<option>Please select</option>
								<?php if (!empty($job_durations)) {
								    foreach ($job_durations as $k => $duration) { ?>
									<option value="<?php echo $k; ?>" <?php echo ($k == '1.0') ? 'selected="selected"' : '' ?> ><?php echo $duration; ?></option>
								<?php }
								    } ?>
							</select>
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Is this Job Chargeable</label>
							<select name="assigned_to" class="form-control">
								<option>Please select</option>
								<option value="1" >Yes</option>
								<option value="0" >No</option>
							</select>
						</div>
						
						<!-- <div class="input-group form-group">
							<label class="input-group-addon">Job assignee</label>
							<select name="assigned_to" class="form-control">
								<option>Please select</option>
								<?php if (!empty($operatives)) {
								    foreach ($operatives as $k => $operative) { ?>
									<option value="<?php echo $operative->id; ?>" ><?php echo $operative->first_name." ".$operative->last_name; ?></option>
								<?php }
								    } ?>
							</select>	
						</div> -->
						<div class="form-group">
							<label class="strong">Works Required</label>
							<textarea name="works_required" rows="2" class="form-control" type="text" value="" style="width:100%;" placeholder="Description of works required..."></textarea>
						</div>
						<div class="form-group">
							<label class="strong">Access Requirements</label>
							<textarea name="access_requirements" rows="2" class="form-control" type="text" value="" style="width:100%;" placeholder="Please provide any access requirements..."></textarea>
						</div>
						
						<div class="form-group">
							<label class="strong">Job notes</label>
							<textarea name="job_notes" rows="3" class="form-control" type="text" value="" style="width:100%;" placeholder=""></textarea>
						</div>
						
						<?php if ($this->user->is_admin || !empty($permissions->can_add) || !empty($permissions->is_admin)) { ?>
							<div class="row">
								<div class="col-md-12">
									<button id="create-job-btn" class="btn btn-sm btn-block btn-flow btn-success btn-next" type="button" >Create Job</button>					
								</div>
							</div>
						<?php } else { ?>
							<div class="row col-md-6">
								<button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>					
							</div>
						<?php } ?>
						
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){

		$( '.update-location-btn' ).click( function( event ){

			var formID = $( this ).closest( 'form' ).attr( 'id' );
			event.preventDefault();
			var formData = $('#'+formID ).serialize();
			swal({
				title: 'Confirm Location update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/site/update_site_location/'.$location_details->location_id); ?>",
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
		
		$( '.book-job' ).click( function(){
			$( "#new-job-modal-md" ).modal( "show" );
		} );
		
		//Submit job form
		$( '#create-job-btn' ).click(function( e ){
			
			e.preventDefault();
			
			var formData = $('#job-creation-form').serialize();
			
			swal({
				title: 'Confirm new job creation?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/create_job/'); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 && ( data.job !== '' ) ){
								$("#new-job-modal-md").modal( 'hide' );
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
		
		
		//DELETE LOCATION RESOURCE
		$( '.delete-location-btn' ).click( function( event ){
			
			var siteId = "<?php echo $location_details->site_id; ?>";
			var locationId = $( this ).data( 'location_id' );

			if( locationId.length == 0 ){
				swal({
					type: 'error',
					html: 'Something went wrong, please refresh the page and try again!',
					showCancelButton: true,
					confirmButtonColor: '#5CB85C',
					cancelButtonColor: '#9D1919',
					confirmButtonText: 'Yes'
				});
				return false;
			}

			event.preventDefault();

			swal({
				type: 'warning',
				title: 'Confirm delete Location?',
				html: 'This is an irreversible action and will affect associated Assets, Jobs, Schedules etc.!',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/site/delete_site_location/'.$location_details->site_id.'/'); ?>"+locationId,
						method:"POST",
						data:{ page:'details' , xsrf_token: xsrfToken, site_id:siteId, location_id:locationId },
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2100
								})
								window.setTimeout(function(){
									window.location.href = "<?php echo base_url('webapp/site/profile/'); ?>"+siteId+"/locations";
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
	});
</script>