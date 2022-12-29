<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="profile-details-container">
				<div class="row alert alert-ssid bg-blue" role="alert">
					<div class="profile-overview">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<legend>Tracking Status Profile <span class="pull-right"><span class="edit-job_tracking pointer hide" title="Click to edit thie Job Type profile"><i class="fas fa-pencil-alt"></i></span> &nbsp; <span data-job_tracking_id="<?php echo $job_tracking_details->job_tracking_id; ?>" class="delete-job-tracking-btn pointer" title="Click to delete this Job Type profile" ><i class="far fa-trash-alt"></i></span></span></legend>
							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Status</label></td>
											<td width="85%"><?php echo ($job_tracking_details->is_active == 1) ? 'Active <i class="far fa-check-circle"></i>' : 'Disabled <i class="far fa-times-circle text-red"></i>'; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Date Created</label></td>
											<td width="85%"><?php echo (valid_date($job_tracking_details->date_created)) ? date('d-m-Y H:i:s', strtotime($job_tracking_details->date_created)) : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Created By</label></td>
											<td width="85%"><?php echo (!empty($job_tracking_details->record_created_by)) ? ucwords($job_tracking_details->record_created_by) : 'Data not available'; ?></td>
										</tr>
									</table>							
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">

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
								<form id="update-job_tracking-profile-form" class="form-horizontal">
									<input type="hidden" name="page" value="details" />
									<input type="hidden" name="job_tracking_id" value="<?php echo $job_tracking_details->job_tracking_id; ?>" />
									<legend>Tracking Status Details</legend>
									<div class="input-group form-group">
										<label class="input-group-addon">Tracking Status</label>
										<input id="job_tracking_status" name="job_tracking_status" class="form-control" type="text" placeholder="Tracking Status" value="<?php echo $job_tracking_details->job_tracking_status; ?>" />
									</div>									

									<div class="input-group">
										<label class="input-group-addon">Status Description</label>
										<textarea id="job_tracking_desc" name="job_tracking_desc" type="text" class="form-control" rows="3"><?php echo (!empty($job_tracking_details->job_tracking_desc)) ? $job_tracking_details->job_tracking_desc : '' ?></textarea>     
									</div>
									<br/>
									<div class="input-group form-group">
										<button type="button" class="update-job-tracking-btn btn btn-sm btn-success">Save Changes</button>
									</div>
								</form>
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

		$( '.update-job-tracking-btn' ).click( function( event ){

			var formID = $( this ).closest( 'form' ).attr( 'id' );
			event.preventDefault();
			var formData = $('#'+formID ).serialize();
			swal({
				title: 'Confirm Tracking Status Profile update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/update_job_tracking_status/'.$job_tracking_details->job_tracking_id); ?>",
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
			}).catch( swal.noop )
		});
		
		
		//Delete Risk Item from
		$( '.delete-job-tracking-btn' ).click( function(){

			var jobTrackingStatusId = $( this ).data( 'job_tracking_id' );
			swal({
				title: 'Confirm delete Tracking Status?',
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/delete_job_tracking_status/'.$job_tracking_details->job_tracking_id); ?>",
						method:"POST",
						data:{'page':'details', job_tracking_id:jobTrackingStatusId},
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 1500
								})
								window.setTimeout( function(){
									window.location.href = "<?php echo base_url('webapp/job/tracking_statuses'); ?>";
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