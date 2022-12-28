<style>
	button, .buttons, .btn, .modal-footer .btn+.btn {
		margin-bottom: 5px;
		margin-right: 0px;
	}
</style>


<div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12">
	<form id="job-tracking-creation-form" >
		<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
		<input type="hidden" name="page" value="details"/>
		<div class="row">
			<div class="job_tracking_status_creation_panel1 col-md-12">
				<div class="x_panel tile has-shadow">
					<legend>Create New Tracking Status <span class="pull-right"><a href="<?php echo base_url( 'webapp/job/tracking_statuses/' ); ?>"><i class="fas fa-list"></i> Tracking Statuses List</a></span></legend>
					<div class="input-group form-group">
						<label class="input-group-addon">Tracking Status Name</label>
						<input id="job_tracking_status" name="job_tracking_status" class="form-control" type="text" placeholder="Tracking Status" value="" />
					</div>
					
					<div class="form-group">
						<div class="input-group">
							<label class="input-group-addon">Status Description</label>
							<textarea id="job_tracking_desc" name="job_tracking_desc" type="text" class="form-control" rows="3" placeholder="Job Status Description"></textarea>     
						</div>
					</div>
					<hr>
					<div class="row form-group">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button id="create-job-tracking-status-btn" class="btn btn-sm btn-flow btn-success btn-next" type="button" >Create Job Tracking Status</button>					
						</div>
					</div>
				</div>						
			</div>	

		</div>
	</form>
</div>

<script>
	$( document ).ready( function(){

		//** Validate any inputs that have the required class, if empty return the name attribute **/
		function check_inputs( currentpanel ){
			
			var result = false;
			var panel  = "." + currentpanel;
			
			$( $( panel + " .required" ).get().reverse() ).each( function(){
				var fieldName  = '';
				var inputValue = $( this ).val();
				if( ( inputValue == false ) || ( inputValue == '' ) || ( inputValue.length == 0 ) ){
					fieldName = $(this).attr( 'name' );
					result    = fieldName;
					return result;
				}
			});
			return result;
		}
		
		//Submit Job Tracking Status form
		$( '#create-job-tracking-status-btn' ).click(function( e ){
			
			e.preventDefault();
			var formData = $('#job-tracking-creation-form').serialize();
			
			swal({
				title: 'Confirm new Tracking Status creation?',
				showCancelButton: 	true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: 	'#9D1919',
				confirmButtonText: 	'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url( 'webapp/job/add_job_tracking_status/' ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 && ( data.job_tracking_status !== '' ) ){
								
								var newTrackingStatusId = data.job_tracking_status.job_tracking_id;
								
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){ 
									//location.reload();
									location.href = "<?php echo base_url('webapp/job/tracking_statuses/'); ?>"+newTrackingStatusId;
								} ,3000);							
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}		
						}
					});
				}else{
					return false;
				}
			}).catch( swal.noop )
		});
		
	});
</script>

