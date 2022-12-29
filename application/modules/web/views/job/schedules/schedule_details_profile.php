<style>
	#pleaseWaitDialog{
		margin-top:12%;
	}
</style>

<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<?php if (empty(!$schedule_details)) { ?>
			<div class="profile-details-container">
				<div class="row alert alert-ssid bg-blue" role="alert">
					<div class="profile-overview">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<legend>Schedule Profile
								<?php if ($this->user->is_admin || !empty($this->user->is_supervisor)) { ?>
									<span class="pull-right"><span id="clone-schedule-btn" class="<?php echo empty($this->user->is_admin) ? 'hide' : ''; ?> clone-schedule-btn pointer" title="Clone this Schedule" data-schedule_id="<?php echo $schedule_details->schedule_id; ?>" ><i class="fa fa-copy"></i></span> <span class="edit-schedule pointer hide" title="Click to edit this Schedule profile"><i class="fas fa-pencil-alt"></i></span> <span class="delete-schedule-btn pointer <?php echo (in_array(strtolower($schedule_details->schedule_status), ['works complete'])) ? 'hide' : ''; ?>" title="Click to delete this Job Type profile" data-schedule_id="<?php echo $schedule_details->schedule_id; ?>" data-site_id="<?php echo !empty($schedule_details->site_id) ? $schedule_details->site_id : ''; ?>">&nbsp; <i class="far fa-trash-alt"></i></span></span>
								<?php } ?>
							</legend>
							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Schedule Name</label></td>
											<td width="85%"><?php echo (!empty($schedule_details->schedule_name)) ? $schedule_details->schedule_name : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Schedule Ref</label></td>
											<td width="85%"><?php echo (!empty($schedule_details->schedule_ref)) ? strtoupper($schedule_details->schedule_ref) : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label title="Activities per linked asset or building" >Activities Required <small class="text-italic" ><i title="Activities per linked asset or building" class="text-yellow pointer"></i></small></label></td>
											<td width="85%" title="Activities per linked asset or building" ><?php echo (!empty($schedule_details->activities_total)) ? $schedule_details->activities_total : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Activities Completed</label></td>
											<?php
                                                $activities_total 		= (!empty($schedule_details->activities_total)) ? $schedule_details->activities_total : 0;
			    $activities_completed 	= (!empty($schedule_details->activities_completed)) ? $schedule_details->activities_completed : 0;
			    ?>
											<td width="85%"><?php echo ($activities_total > 0) ? $activities_completed.' of '.$activities_total : 0; ?> (<?php echo (($activities_total > 0) && ($activities_completed > 0)) ? (number_format((($activities_completed / $activities_total)*100), 2) + 0) : 0 ; ?>%)</td>
										</tr>										
									</table>							
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Status</label></td>
											<td width="85%"><?php echo ($schedule_details->is_active == 1) ? 'Active <i class="far fa-check-circle"></i>' : 'Disabled <i class="far fa-times-circle text-red"></i>'; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Schedule Status</label></td>
											<td width="85%"><?php echo (!empty($schedule_details->schedule_status)) ? $schedule_details->schedule_status : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Date Created</label></td>
											<td width="85%"><?php echo (valid_date($schedule_details->date_created)) ? date('d-m-Y H:i:s', strtotime($schedule_details->date_created)) : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Created By</label></td>
											<td width="85%"><?php echo (!empty($schedule_details->record_created_by)) ? ucwords($schedule_details->record_created_by) : 'Data not available'; ?></td>
										</tr>
									</table>							
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="row">
						<!-- <div class="col-md-4 col-sm-4 col-xs-12">
							<div class="x_panel tile has-shadow">
								<form id="update-schedule-profile-form" class="form-horizontal">
									<input type="hidden" name="page" value="schedule" />
									<input type="hidden" name="schedule_id" value="<?php echo $schedule_details->schedule_id; ?>" />
									<legend>Schedule Details</legend>
									<div class="input-group form-group">
										<label class="input-group-addon">Schedule Reference</label>
										<input id="schedule_ref" name="schedule_ref" class="form-control" type="text" placeholder="Schedule Reference" readonly value="<?php echo $schedule_details->schedule_ref; ?>" />
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Schedule Name</label>
										<input id="schedule_zone" name="schedule_name" class="form-control" type="text" placeholder="Schedule name" value="<?php echo $schedule_details->schedule_name; ?>" />
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Activities Required</label>
										<input id="activities_total" name="activities_total" class="form-control" type="text" placeholder="Activities Required" value="<?php echo $schedule_details->activities_total; ?>" />
									</div>
									<div class="input-group">
										<label class="input-group-addon">Schedule Description</label>
										<textarea id="schedule_desc" name="schedule_desc" type="text" class="form-control" rows="3"><?php echo (!empty($schedule_details->schedule_desc)) ? $schedule_details->schedule_desc : '' ?></textarea>     
									</div>
									<br/>
									<div class="form-group">
										<div class="">
											<button type="button" class="update-schedule-btn btn btn-sm btn-success">Save Changes</button>
										</div>
									</div>
								</form>
							</div>
						</div> -->

						<div class="col-md-12 col-sm-12 col-xs-12">
							<div class="x_panel tile has-shadow">
								<legend>Schedule Activities (<?php echo !empty($schedule_activities) ? count($schedule_activities, 1) : 0; ?>) <span class="pull-right pointer book-job hide"><i class="fas fa-plus text-green" title="Create a Job for this schedule" ></i></span>
								<span class="pull-right pointer download-pdf" style="margin-right: 10px;"><a target="_blank" style="text-decoration:none" href="<?php echo base_url("/webapp/job/download_schedule/".$schedule_details->schedule_id."/"); ?>" class="pull-right text-red"><i class="fas fa-file-pdf text-red"></i></a></span></legend>
								<table class="sortable table table-responsive" style="width:100%; font-size:90%">
									<tr>
										<th width="17%">ACTIVITY</th>
										<th width="18%">SITE REF / BUILDING NAME</th>
										<th width="10%">STATUS</th>
										<th width="10%">DUE DATE</th>
										<th width="5%">JOB ID</th>
										<th width="6%" class="center text-center">ASSETS</th>
										<th width="20%">JOB TYPE</th>
										<th width="8%">DISCIPLINE</th>
										<th width="6%"><span class="pull-right">ACTION</span></th>
									</tr>
									<?php if (!empty($schedule_activities)) {
									    foreach ($schedule_activities as $activity_record) { ?>
										<tr>
											<td><?php echo $activity_record->activity_name; ?></td>
											<td><a href="<?php echo base_url('webapp/site/profile/'.$activity_record->site_id.'/schedules'); ?>"><?php echo ($activity_record->site_reference) ? $activity_record->site_reference.' - ' : $activity_record->site_id.' - '; ?><?php echo $activity_record->site_name; ?>, <?php echo $activity_record->site_postcodes; ?></a></td>
											<td><?php echo $activity_record->status; ?></td>
											<!-- <td><?php echo ($activity_record->completion > 0) ? $activity_record->completion : '0'; ?></td> -->
											<td><?php echo (valid_date($activity_record->due_date)) ? date('d-m-Y', strtotime($activity_record->due_date)) : ''; ?></td>
											<td><a href="<?php echo base_url('webapp/job/profile/'.$activity_record->job_id); ?>"><?php echo !empty($activity_record->job_id) ? $activity_record->job_id : ''; ?></a></td>
											<td class="center text-center"><?php echo !empty($activity_record->total_assets) ? $activity_record->total_assets : ''; ?></td>
											<td><?php echo $activity_record->job_type; ?></td>
											<td><img src="<?php echo !empty($activity_record->discipline_image_url) ? $activity_record->discipline_image_url : ''; ?>" width="18px" > <?php echo !empty($activity_record->discipline_name) ? $activity_record->discipline_name : ''; ?></span></td>
											<td>
												<span class="pull-right">
													<span>
														<span class="text-green text-bold"><a href="<?php echo base_url('webapp/job/profile/'.$activity_record->job_id); ?>"><i title="Click here to view this Activity record" class="fas fa-edit text-blue pointer"></i></a> &nbsp;&nbsp;</span>
														<?php if ($this->user->is_admin || !empty($permissions->can_delete) || !empty($permissions->is_admin)) { ?>
															<span class="hide text-red text-bold"><i title="Click here to delete Activity" class="delete-item fas fa-trash-alt text-red pointer" data-job_id = "<?php echo $activity_record->job_id; ?>"></i></span>
														<?php } ?>
													</span>
												</span>
											</td>
										</tr>
									<?php }
									    } else { ?>
										<tr>
											<td colspan="9"><?php echo $this->config->item('no_records'); ?></td>
										</tr>
									<?php } ?>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } else { ?>
				<div class="row">
					<span><?php echo $this->config->item('no_records'); ?></span>
				</div>
			<?php } ?>
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){
		
		//DELETE SCHEDULE PROFILE
		$( '.delete-schedule-btn' ).click( function( event ){
			
			var ScheduleId = $( this ).data( 'schedule_id' ),
					siteId = $( this ).data( 'site_id' );
			
			event.preventDefault();

			swal({
				type: 'warning',
				title: 'Confirm Delete Schedule Profile?',
				html: 'This will also delete any associated Jobs and Activities!',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/delete_schedule/'.$schedule_details->schedule_id); ?>",
						method:"POST",
						data:{ page:'details', schedule_id:ScheduleId },
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
									if( siteId ){
										window.location.href = "<?php echo base_url('webapp/site/profile/'); ?>"+siteId+"/schedules";
									} else {
										window.location.href = "<?php echo base_url('webapp/job/schedules'); ?>";
									}
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
		
		
		$('.delete-item').click(function(){
			var jobId = $(this).attr('data-job_id')
			var postUrl = "<?php echo base_url('webapp/job/delete_job') ?>" + "/" +  $(this).attr('data-job_id')
			swal({
				title: 'Confirm delete Job?',
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					console.log()
					$.ajax({
						url: postUrl,
						method:"POST",
						data:{job_id:jobId},
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
									window.location.reload()
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

		$( '.update-schedule-btn' ).click( function( event ){

			var formID = $( this ).closest( 'form' ).attr( 'id' );
			event.preventDefault();
			var formData = $('#'+formID ).serialize();
			swal({
				title: 'Confirm Schedule update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/site/update_site_schedule/'.$schedule_details->schedule_id); ?>",
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
									schedule.reload();
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
									schedule.reload();
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
		
		
		//Clone Schedule
		$( '.clone-schedule-btn' ).click(function( e ){
			
			e.preventDefault();
			
			var scheduleId = $( this ).data( 'schedule_id' );
			
			submitCloneScheduleForm(scheduleId);
			
		});
		
		
		function submitCloneScheduleForm( scheduleId, liMit = "<?php echo SCHEDULE_CLONE_DEFAULT_LIMIT; ?>", offSet = 0 ){

			if( !scheduleId ){
				swal({
					type: 'error',
					title: 'Invalid Schedule ID',
				})
			}

			if( offSet > 0 ){
				var confirmationMessage = 'Continue Schedule Cloning process?',
					warningMessage 		= '';
			} else {
				var confirmationMessage = 'Confirm Clone Schedule?',
				warningMessage 			= 'This will also generate new Activitites & Jobs';
			}

			swal({
				title: confirmationMessage,
				html: warningMessage,
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/clone_schedule'); ?>",
						method:"POST",
						data:{ page:'details', schedule_id:scheduleId, limit:liMit, offset: offSet },
						dataType: 'json',
						beforeSend: function(){
							showPleaseWait();
						},
						success:function(data){
							hidePleaseWait();
							if( data.status == 1 && ( data.schedule.schedule_id !== '' ) ){
								var newScheduleId 	= data.schedule.schedule_id,
									scheduleRef 	= data.schedule.schedule_ref,
									scheduleName 	= data.schedule.schedule_name,
									clonedSchID 	= data.schedule.cloned_schedule_id,
									contractID 		= data.schedule.contract_id,
									contractName 	= data.schedule.contract_name,
									frequencyID 	= data.schedule.frequency_id,
									totalActivities	= data.schedule.activities,
									totalSites 		= data.schedule.sites,
									totalAssets 	= data.schedule.assets,
									dataCounters 	= data.schedule.counters,
									liMit			= data.schedule.counters.limit,
									offSet			= ( Math.floor( ( dataCounters.processed_activities / dataCounters.expected_activities )*dataCounters.activity_pages ) ) * data.schedule.counters.limit;
								swal({
									type: 'success',
									showCancelButton: true,
									confirmButtonColor: '#5CB85C',
									cancelButtonColor: '#9D1919',
									confirmButtonText: 'Proceed',
									title: 'Cloning Process started!',
									html:
										'<p>Please check and confirm that the details below are correct, then click proceed to complete the cloning process.</p>' +
										'<table class="table table-responsive pull-left">' +
											'<tr>' +
												'<th>Schedule Name:</th><td>' + scheduleName+ '</td>' +
											'</tr>' +
											'<tr>' +
												'<th>Contract:</th><td>' + contractName+ '</td>' +
											'</tr>' +
											'<tr>' +
												'<th>Sites Processed:</th><td>' + dataCounters.processed_sites + ' of ' + dataCounters.expected_sites + '</td>' +
											'</tr>' +
											'<tr>' +
												'<th>Activities Processed:</th><td>' + dataCounters.processed_activities + ' of ' + dataCounters.expected_activities + '</td>' +
											'</tr>' +
											/*'<tr>' +
												'<td>Total Assets:</td><td>' + totalCustomers+ '</td>' +
											'</tr>' +*/
										'</table>'
								}).then( function (result) {
									if ( result.value ) {
										
										if( dataCounters.processed_activities === dataCounters.expected_activities ){
											//Do something here
											submitCloneJobsForm({
												page: 'details',
												schedule_id: newScheduleId,
												cloned_schedule_id: clonedSchID,
												contract_id: contractID,
												frequency_id: frequencyID,
											});	
										} else {
											submitCloneScheduleForm( scheduleId, liMit, offSet );
										}
							
									} else {
										//Do this if user cancels to change the name
									}
								});
								
							} else {
								swal({
									type: 'error',
									title: data.status_msg
								})
							}		
						}
					});
				}
			}).catch(swal.noop)
			
		}

		
		/*
		* Clone Schedule Jobs
		**/
		function submitCloneJobsForm( formData ){
		
			var formData = formData;

			if( ( formData.schedule_id.length > 0 ) && ( formData.cloned_schedule_id.length > 0 ) ){
				$.ajax({
					url:"<?php echo base_url('webapp/job/clone_jobs'); ?>",
					method:'POST',
					data:formData,
					dataType: 'json',
					beforeSend: function(){
						showPleaseWait();
					},
					success:function( data ){
						hidePleaseWait();
						if( data.status == 1 && ( data.jobs.schedule_id !== '' ) ){
							var newScheduleId = data.jobs.schedule_id;
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 3000
							})
							window.setTimeout(function(){
								location.href = "<?php echo base_url('webapp/job/schedule_profile/'); ?>"+newScheduleId;
							} ,2000);
							
						} else {
							swal({
								type: 'error',
								title: data.status_msg
							})
						}
					}
				});
			
			} else {
				swal({
					type: 'error',
					title: 'Invalid Data'
				});
				return false;
			}
		}
		
	});
	
	
	/**
	 * Displays overlay with "Please wait" text. Based on bootstrap modal. Contains animated progress bar.
	 */
	function showPleaseWait() {
		
		if ( document.querySelector( "#pleaseWaitDialog") == null ) {
			var modalLoading = '<div class="modal" id="pleaseWaitDialog" data-backdrop="static" data-keyboard="false" role="dialog">\
				<div class="modal-dialog modal-vertical-centered">\
					<div class="modal-content">\
						<div class="modal-body" style="min-height: 40px;">\
							<h4 class="modal-title">Processing your request. Please wait...</h4>\
							<div class="progress">\
							  <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%; height: 40px"></div>\
							</div>\
							<p class="modal-title"><small>This may take several minutes...</small></p>\
						</div>\
					</div>\
				</div>\
			</div>';
			$(document.body).append(modalLoading);
		}
	  
		$( "#pleaseWaitDialog" ).modal( "show" );
	}

	/**
	 * Hides "Please wait" overlay. See function showPleaseWait().
	 */
	function hidePleaseWait() {
		$( "#pleaseWaitDialog" ).modal( "hide" );
		$( '.modal-backdrop' ).remove();
	}
	
</script>