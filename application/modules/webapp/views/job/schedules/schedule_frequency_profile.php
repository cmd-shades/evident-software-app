<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="profile-details-container">
				<div class="row alert alert-ssid bg-blue" role="alert">
					<div class="profile-overview">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<legend>Frequency Profile <span class="pull-right"><span class="edit-frequency pointer hide" title="Click to edit thie Job Type profile"><i class="fas fa-pencil-alt"></i></span> &nbsp; <span class="---delete-frequency pointer" title="Click to delete this Job Type profile" ><i class="far fa-trash-alt"></i></span></span></legend>
							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Frequency Name</label></td>
											<td width="85%"><?php echo ( !empty( $frequency_details->frequency_name ) ) ? $frequency_details->frequency_name : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Frequency Ref</label></td>
											<td width="85%"><?php echo ( !empty( $frequency_details->frequency_ref ) ) ? strtoupper( $frequency_details->frequency_ref ) : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Activities Required</label></td>
											<td width="85%"><?php echo ( !empty( $frequency_details->activities_required ) ) ? $frequency_details->activities_required : ''; ?></td>
										</tr>
									</table>							
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Status</label></td>
											<td width="85%"><?php echo ( $frequency_details->is_active == 1 ) ? 'Active <i class="far fa-check-circle"></i>' : 'Disabled <i class="far fa-times-circle text-red"></i>'; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Date Created</label></td>
											<td width="85%"><?php echo ( valid_date( $frequency_details->date_created ) ) ? date( 'd-m-Y H:i:s', strtotime( $frequency_details->date_created ) ) : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Created By</label></td>
											<td width="85%"><?php echo ( !empty( $frequency_details->record_created_by ) ) ? ucwords( $frequency_details->record_created_by ) : 'Data not available'; ?></td>
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
								<form id="update-frequency-profile-form" class="form-horizontal">
									<input type="hidden" name="page" value="frequency" />
									<input type="hidden" name="frequency_id" value="<?php echo $frequency_details->frequency_id; ?>" />
									<legend>Frequency Details</legend>
									<div class="input-group form-group">
										<label class="input-group-addon">Frequency Reference</label>
										<input id="frequency_ref" name="frequency_ref" class="form-control" type="text" placeholder="Frequency Reference" readonly value="<?php echo $frequency_details->frequency_ref; ?>" />
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Frequency Name</label>
										<input id="frequency_zone" name="frequency_name" class="form-control" type="text" placeholder="Frequency name" value="<?php echo $frequency_details->frequency_name; ?>" />
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Activities Required</label>
										<input id="activities_required" name="activities_required" class="form-control" type="text" placeholder="Activities Required" value="<?php echo $frequency_details->activities_required; ?>" />
									</div>
									<div class="input-group">
										<label class="input-group-addon">Frequency Description</label>
										<textarea id="frequency_desc" name="frequency_desc" type="text" class="form-control" rows="3"><?php echo ( !empty( $frequency_details->frequency_desc ) ) ? $frequency_details->frequency_desc : '' ?></textarea>     
									</div>
									<div class="input-group">
										<button type="button" class="update-frequency-btn btn btn-sm btn-success">Save Changes</button>
									</div>
								</form>
							</div>
						</div>

						<div class="col-md-6 col-sm-6 col-xs-12">
							<div class="x_panel tile has-shadow">
								<legend>Frequency Activities (<?php echo !empty( $activities_activities ) ? count( $activities_activities, 1 ) : 0; ?>) <span class="pull-right pointer book-job"><i class="fas fa-plus text-green" title="Create a Job for this frequency" ></i></span></legend>
								<table class="table table-responsive" style="width:100%">
									<tr>
										<th width="15%">Activity Id</th>
										<th width="20%">Total 1</th>
										<th width="20%">Total 2</th>
										<th width="15%">Total 3</th>
										<th width="15%">Status</th>
										<th width="15%"><span class="pull-right">Action</span></th>
									</tr>
									<?php if( !empty( $activities_activities ) ){ foreach( $activities_activities as $job_record ) { ?>
										<tr>
											<td><a href="<?php echo base_url( 'webapp/job/profile/'.$job_record->job_id ); ?>"><?php echo $job_record->job_id; ?></a></td>
											<td><?php echo ( valid_date( $job_record->job_date ) ) ? date('d-m-Y', strtotime( $job_record->job_date ) ) : ''; ?></td>
											<td><?php echo $job_record->job_type; ?></td>
											<td><?php echo !empty( $job_record->status ) ? $job_record->status : ''; ?></td>
											<td><?php echo $job_record->assignee; ?></span></td>
											<td>
												<span class="pull-right">
													<span>
														<span class="text-green text-bold"><a href="<?php echo base_url( 'webapp/job/profile/'.$job_record->job_id ); ?>"><i title="Click here to view this Job record" class="fas fa-edit text-blue pointer"></i></a> &nbsp;&nbsp;</span>
														<?php if( $this->user->is_admin || !empty( $permissions->can_delete ) || !empty( $permissions->is_admin ) ){ ?>
															<span class="text-red text-bold"><i title="Click here to delete Job" class="delete-item fas fa-trash-alt text-red pointer"></i></span>
														<?php } ?>
													</span>
												</span>
											</td>
										</tr>
									<?php } } else{ ?>
										<tr>
											<td colspan="6"><?php echo $this->config->item('no_records'); ?></td>
										</tr>
									<?php } ?>
								</table>
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

		$( '.update-frequency-btn' ).click( function( event ){

			var formID = $( this ).closest( 'form' ).attr( 'id' );
			event.preventDefault();
			var formData = $('#'+formID ).serialize();
			swal({
				title: 'Confirm Frequency update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/site/update_site_frequency/'.$frequency_details->frequency_id ); ?>",
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
									frequency.reload();
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
						url:"<?php echo base_url('webapp/job/create_job/' ); ?>",
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
									frequency.reload();
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
	});
</script>