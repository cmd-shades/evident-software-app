<script src="<?php echo base_url('assets/js/custom/jobsort-events.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/custom/jobsort-ui.js'); ?>" type="text/javascript"></script>

<script src="<?php echo base_url('assets/js/custom/jobsort-util.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/custom/jobsort-map.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/custom/jobsort-modal.js'); ?>" type="text/javascript"></script>

<div class="routing">
	<div id="job-clone-temporary" style="display:none"></div>
	<div id="infoModal" class="modal">
		<div class="modal-content">
			<span class="close" onclick="$('#infoModal').css('display', 'none')">&times;</span>
			<div class="modal-inner-content"></div>
		</div>
	</div>
	<div class="row" style="margin-top: 50px;">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
				<form id="resource-date-form" action="<?php echo base_url( "webapp/diary/planner" ); ?>" method="post">
					<div class="input-group form-group">
						<label class="input-group-addon">Choose a Date</label>
						<input type="text" name="resource_date" value="<?php echo ( !empty( $resource_date ) ) ? date( 'd/m/Y',  strtotime( $resource_date ) ) : date( 'd/m/Y' ); ?>" class="form-control datepicker" data-date-format="DD/MM/YYYY" onchange="this.form.submit()" />
					</div>
				</form>
			</div>
		</div>
	</div>
		
    <div id="content-container" class="container-fluid">
        <div class="row" style="height: 100%;">
            <div class="col-md-4 section" id="jobs-container">
				<div class="col-title">Jobs</div>
				<div id="jobs-sortable" class="sortable-list connectedSortable">
					<?php
					if( !empty( $jobs ) ){
						foreach( $jobs as $job ){
							$this->load->view( 'el_job', $job );
						}
					} else { ?>
						<div style="color: #212121;">No jobs matching the criteria.</div>
					<?php } ?>
				</div>
            </div>
            <div class="col-md-4 section" id="users-container">
				<div class="col-title">Resources with availability</div>
				<input id="user-searchbar" type="text" placeholder="Search.." onkeyup="updateShownUsers()">
				<div id="staff-list">
			 <?php	
					if( !empty( $operatives_without_job ) ){
						foreach( $operatives_without_job as $staff_member ){
							$this->load->view( 'el_user', $staff_member );
						}
					} else { ?>
						<div style="color: #212121;">No staff available to assign the job.</div>
					<?php } ?>
				</div>
            </div>
			<?php /*
            <div class="col-md-3 section" id="routing-container">
               <div class="col-title">Routing</div>
				<div id="map"></div>
				
				<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDKbrViiSEEynq_eYpFlOmXXErTCQL8Mqs&callback=initMap"></script>
            </div>
			*/ ?>
			
            <div class="col-md-4 section" id="field-operative-w-jobs">
				<div class="col-title">Scheduled</div>
					<?php if( !empty( $operatives_with_job ) ){
						foreach( $operatives_with_job as $staff_member ){
							$this->load->view( 'routing_scheduled_jobs', $staff_member );
						} 
					} else { ?>
						<div style="color: #212121;">There are no scheduled jobs in the system yet.</div>
					<?php } ?>
				</div>
			</div>
            <div class="col-md-3 hide" id="directions-contaifghner">
				<div class="col-title">Directions</div>
				<div id="directions-content">(Select some jobs to get started)</div>

				<br>
				<form target="_blank" action="../mpdf/" method="post" id="download-button-form">
					<input id="download-button-html" type="hidden" name="html" value="Failed to load Map Directions!">
					<button type="submit" id="download-directions-button" class="btn btn-info" disabled="true">Download Directions</button>
				</form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$( document ).ready( function(){
	$( ".commit" ).click( function( e ){
		e.preventDefault();
		// $( this ) -> button
		var userElement 	= $( this ).closest( ".user-element" );
		var userID			= userElement.attr( "user_id" );
		var element			= userElement.find( ".job-element" );
		var job_Batch 		= {};
		var i=1;
		element.each( function(){
			var jobID = $( this ).attr( 'job_id' );
			job_Batch[jobID] = {
				'job_id' : jobID,
				'job_order' : i,
				'assigned_to' : userID,
				'job_date' : "<?php echo $resource_date; ?>",
			};
			i++;
		});

		if( job_Batch ){
			$.ajax({
				url:"<?php echo base_url( 'webapp/diary/commit_jobs' ); ?>",
				method: "POST",
				data:{ jobBatch: job_Batch },
				success: function( data ){
					var newData = JSON.parse( data );
					if( newData.status == true || newData.status == 1 ){
						swal({
							type: 'success',
							title: newData.status_msg,
							showConfirmButton: false,
							timer: 3000
						})
						window.setTimeout(function(){
							location.reload();
						}, 3000);
					} else {
						swal({
							type: 'error',
							title: newData.status_msg
						})
					}
				}
			});
		}
	})
})
</script>

<?php /*
<div class="row">
	<div class="col-md-4 col-sm-4 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Jobs</legend>
		   <div id="avaliable-jobs">			   
			   <div class="dragto-sortable" id="avaliable-jobs-drop">				   
					<?php 
						foreach( $avaliable_jobs as $avaliable_job ){
							$this->load->view( 'dragdrop_job', $avaliable_job );
						}				  
					?>					  
			   </div>
		   </div>
		</div>
	</div>
	<div class="col-md-4 col-sm-4 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Available Users / Sub-contractos</legend>
			<div id="searchbar-container">
				<div id="searchbar-container">
					<div class="form-group">
						<input class="form-control <?php echo $module_identier; ?>-search_input" type="text" name="" placeholder="Search user" id="searchbar" />
					</div>
				</div>
				<div id="avaliable-staff">
					<?php 
						foreach( $avaliable_staff as $user_data ){
							$this->load->view( 'dragdrop_user', $user_data );
						}
					?>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4 col-sm-4 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Route</legend>
		</div>
	</div>
</div>
*/ ?>