<style>
	table tr th,table tr td{
		padding: 5px 0;
	}

	.profile-details-container label{
		color: #fff;
	}

	@media (max-width: 480px) {
		.btn-info{
			margin-bottom:10px;
		}
	}
</style>

<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<?php if( !empty( $job_details ) ) { ?>
			<div class="profile-details-container">
				<div class="row alert alert-ssid records-bar" role="alert">
					<div class="col-md-4 col-sm-4 col-xs-12">
						<div class="row">
							<legend>Job Details ID: <?php echo $job_details->job_id; ?></legend>
							<div class="rows">
								<div class="row profile_view">
									<div class="row col-sm-12">
										<div class="right col-xs-12">
											<table style="width:100%;">
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i><label>Job Type</label></td>
													<td width="60%"><a href="<?php echo base_url( '/webapp/job/job_types/'.$job_details->job_type_id ); ?>" ><?php echo ucwords($job_details->job_type); ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Job Date</label></td>
													<td width="60%"><?php echo ( valid_date( $job_details->job_date ) ) ? date('D, jS M Y', strtotime( $job_details->job_date ) ) : ''; ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Job Status</label></td>
													<td width="60%"><?php echo ucwords( $job_details->job_status ); ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Job Assignee</label></td>
													<td width="60%"><?php echo ucwords( $job_details->assignee ); ?></td>
												</tr>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-12">
						<div class="row">
							<legend>Job Address</legend>

						<?php 	$main_address_found = false;
								if( !empty( $job_details->customer_details->addresses ) ){
									foreach( $job_details->customer_details->addresses as $address ){
										if( strtolower( $address->address_type_group ) == "main" ){ ?>
											<table class="table-responsive">
												<tr>
													<td width="30%"><i class="hide fa fa-user"></i> <label>Address Line 1</label></td>
													<td><?php echo ( !empty( ucwords( $address->address_line1 ) ) ) ? ucwords( $address->address_line1 ) : '' ; ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Address Line 2</label></td>
													<td width="60%"><?php echo ( !empty( $address->address_line2 ) ) ? $address->address_line2 : '' ; ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Town</label></td>
													<td width="60%"><?php echo ( !empty( ucwords( $address->address_town ) ) ) ? ucwords( $address->address_town ) : '' ; ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Postcode(s)</label></td>
													<td width="60%"><?php echo ( !empty( strtoupper( $address->address_postcode ) ) ) ? strtoupper( $address->address_postcode ) : '' ; ?> <?php echo ( !empty( $job_details->region_name ) ) ? ' <a href="'.base_url( '/webapp/diary/manage_regions/' ).$job_details->region_id.'">('. $job_details->region_name.')</a>' : ' &nbsp;<a target="_blank" href="'.base_url('/webapp/diary/manage_regions' ).'" style="color:brown !important;" title="Please add this POSTCODE to a Region and then update this Job record">(POSTCODE NOT PART OF ANY REGION)</a>' ; ?></td>
												</tr>
											</table>
											
							<?php   		$main_address_found = true;
										}
									}
								} ?>

						<?php 	if( !$main_address_found ){ ?>
									<table class="table-responsive">
										<tr>
											<td width="30%"><i class="hide fa fa-user"></i> <label>Address Line 1</label></td>
											<td><?php echo ( !empty( ucwords( $job_details->address_line_1 ) ) ) ? ucwords( $job_details->address_line_1 ) : '' ; ?></td>
										</tr>
										<tr>
											<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Address Line 2</label></td>
											<td width="60%"><?php echo ( !empty( $job_details->address_line_2 ) ) ? $job_details->address_line_2 : '' ; ?></td>
										</tr>
										<tr>
											<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Town</label></td>
											<td width="60%"><?php echo ( !empty( ucwords( $job_details->address_city ) ) ) ? ucwords( $job_details->address_city ) : '' ; ?></td>
										</tr>
										<tr>
											<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Postcode(s)</label></td>
											<td width="60%"><?php echo ( !empty( strtoupper( $job_details->address_postcode ) ) ) ? strtoupper( $job_details->address_postcode ) : '' ; ?> <?php echo ( !empty( $job_details->region_name ) ) ? ' <a href="'.base_url( '/webapp/diary/manage_regions/' ).$job_details->region_id.'">('. $job_details->region_name.')</a>' : '' ; ?></td>
										</tr>
									</table>
						<?php 	} ?>
						</div>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-12">
						<div class="row">
							<?php if( !empty( $job_details->site_details ) ){
								$link = 'Linked Building Details <a title="Click to go to the Building profile" class="pointer" href="'.base_url( 'webapp/site/profile/'.$job_details->site_details->site_id ).'/jobs">('.$job_details->site_details->site_id.')</a>';

								$link2 = '<a title="Click to go to the Building profile" class="pointer" href="'.base_url( 'webapp/site/profile/'.$job_details->site_details->site_id ).'/jobs">'.$job_details->site_details->site_name.'</a>';

							}else if( !empty( $job_details->customer_details ) ){
								$link = 'Linked Customer Details <a title="Click to go to the Customer profile" class="pointer" href="'.base_url( 'webapp/customer/profile/'.$job_details->customer_details->customer_id ).'/jobs">('.$job_details->customer_details->customer_id.')</a>';

								$link2 = '<a title="Click to go to the Customer profile" class="pointer" href="'.base_url( 'webapp/customer/profile/'.$job_details->customer_details->customer_id ).'/jobs">'.ucwords( $job_details->customer_details->customer_first_name ).' '.ucwords( $job_details->customer_details->customer_last_name ).'</a>';
							} ?>

							<legend><?php echo ( !empty( $link  ) ) ? $link  : 'Job not linked'; ?> <span class="pull-right"><span class="edit-job-sheet pointer hide" title="Click to edit this Job Sheet"><i class="fas fa-pencil-alt"></i></span> &nbsp; <span class="delete-job-btn pointer" title="Click to delete this Job" data-job_id="<?php echo $job_details->job_id; ?>" ><i class="far fa-trash-alt"></i></span></span></legend>
							<table style="width:100%;">

								<?php if( !empty( $job_details->site_details ) ){ ?>
									<tr>
										<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Site Reference</label></td>
										<td width="60%"><a href="<?php echo base_url( 'webapp/site/profile/'.$job_details->site_details->site_id ); ?>"><?php echo strtoupper($job_details->site_details->site_reference); ?></a></td>
									</tr>
									<tr>
										<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Site Name</label></td>
										<td width="60%"><?php echo $link2; ?></td>
									</tr>
									<tr>
										<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Site Address 1</label></td>
										<td width="60%"><?php echo $job_details->site_details->address_line_1; ?> <?php echo $job_details->site_details->address_line_2; ?></td>
									</tr>
									<tr>
										<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Site Address 2</label></td>
										<td width="60%"><?php echo $job_details->site_details->address_city.', '; ?> <?php echo strtoupper($job_details->address_postcode); ?></td>
									</tr>
								<?php }else if( !empty( $job_details->customer_details ) ){ ?>
									<tr>
										<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Customer Name</label></td>
										<td width="60%"><?php echo $link2; ?></td>
									</tr>
									<!-- <tr>
										<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Customer Email</label></td>
										<td width="60%"><?php echo $job_details->customer_details->customer_email; ?></td>
									</tr> -->
									<tr>
										<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Contact Mobile</label></td>
										<td width="60%"><?php echo !empty( $job_details->customer_details->customer_mobile ) ? $job_details->customer_details->customer_mobile : ''; ?></td>
									</tr>
									<tr>
										<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Contact Main</label></td>
										<td width="60%"><?php echo ( !empty( $job_details->customer_details->customer_main_telephone ) ) ? $job_details->customer_details->customer_main_telephone : ''; ?></td>
									</tr>
									<tr>
										<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Contact Work</label></td>
										<td width="60%"><?php echo !empty( $job_details->customer_details->customer_work_telephone ) ? $job_details->customer_details->customer_work_telephone : ''; ?></td>
									</tr>
								<?php } ?>

							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="row">
				<?php $this->load->view('webapp/_partials/tabs_loader'); ?>
				<?php include $include_page; ?>
			</div>
			<?php }else{ ?>
				<div class="row">
					<span><?php echo $this->config->item('no_records'); ?></span>
				</div>
			<?php } ?>
		</div>
	</div>
</div>

<?php if( !empty( $job_details ) ) { ?>
	<!-- Modal for VIewing and Editing an existing Job Sheet -->
	<div id="edit-job-sheet-modal" class="modal fade edit-job-sheet-modal" tabindex="-1" role="dialog" aria-hidden="true">
		<form id="edit-job-sheet-form" >
			<input type="hidden" name="page" value="details" />
			<input type="hidden" name="job_id" value="<?php echo $job_details->job_id; ?>" />
			<input type="hidden" name="site_id" value="<?php echo ( !empty( $job_details->site_id ) ) ? $job_details->site_id : ''; ?>" />
			<input type="hidden" name="customer_id" value="<?php echo ( !empty( $job_details->customer_id ) ) ? $job_details->customer_id : ''; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<input type="hidden" name="address_id" value="<?php echo ( !empty( $job_details->address_id ) ) ? $job_details->address_id : null; ?>" />
			<input type="hidden" name="external_job_ref" value="<?php echo ( !empty( $job_details->external_job_ref ) ) ? $job_details->external_job_ref : null; ?>" />
			<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
						<h4 class="modal-title" id="myModalLabel">Edit Job Sheet (<?php echo $job_details->job_id; ?>)</h4>
						<small id="feedback-message"></small>
					</div>

					<div class="modal-body">
						<div class="input-group form-group">
							<label class="input-group-addon">Job Type</label>
							<select id="job_type_id" name="job_type_id" class="form-control required">
								<option value="">Please select</option>
								<?php if( !empty($job_types) ) { foreach( $job_types as $k => $job_type ) { ?>
									<option value="<?php echo $job_type->job_type_id; ?>" <?php echo ( $job_details->job_type_id == $job_type->job_type_id ) ? 'selected=selected' : ''; ?> ><?php echo $job_type->job_type; ?></option>
								<?php } } ?>
							</select>
						</div>

						<?php $restricted_people = !empty( $restricted_people ) ? $restricted_people : []; ?>
						<?php if( $this->user->is_admin || !in_array( $this->user->id, $restricted_people ) ){ ?>
							<div class="input-group form-group">
								<label class="input-group-addon">Job Date</label>
								<input name="job_date" class="form-control datepicker" type="text" placeholder="Job date" value="<?php echo ( valid_date( $job_details->job_date ) ) ? date('d-m-Y', strtotime( $job_details->job_date ) ) : ''; ?>" />
							</div>

							<div class="input-group form-group">
								<label class="input-group-addon">Job Assignee</label>
								<select name="assigned_to" class="form-control">
									<option>Please select</option>
									<?php if( !empty($operatives) ) { foreach( $operatives as $k => $operative ) { ?>
										<option value="<?php echo $operative->id; ?>" <?php echo ( $operative->id == $job_details->assigned_to ) ? 'selected=selected' : ''; ?> ><?php echo $operative->first_name." ".$operative->last_name; ?></option>
									<?php } } ?>
								</select>
							</div>
						<?php } else { ?>
							<div class="input-group form-group">
								<label class="input-group-addon">Job date</label>
								<input class="form-control" type="text" placeholder="Job date" readonly value="<?php echo ( valid_date( $job_details->job_date ) ) ? date('d-m-Y', strtotime( $job_details->job_date ) ) : 'Call pending'; ?>" />
							</div>
						<?php } ?>

						<div class="input-group form-group">
							<label class="input-group-addon">Job Status</label>
							<select name="status_id" class="form-control">
								<option>Please select</option>
								<?php if( !empty($job_statuses) ) { foreach( $job_statuses as $k => $job_status ) { ?>
									<option value="<?php echo $job_status->status_id; ?>" <?php echo ( $job_details->status_id == $job_status->status_id ) ? 'selected=selected' : ''; ?> ><?php echo $job_status->job_status; ?></option>
								<?php } } ?>
							</select>
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">Works Required / Notes</label>
							<textarea name="works_required" type="text" class="form-control" rows="3"><?php echo ( !empty( $job_details->works_required ) ) ? $job_details->works_required : '' ?></textarea>
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">Access Requirements</label>
							<textarea name="access_requirements" type="text" class="form-control" rows="3"><?php echo ( !empty( $job_details->access_requirements ) ) ? $job_details->access_requirements : '' ?></textarea>
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">Parking Requirements</label>
							<textarea name="parking_requirements" type="text" class="form-control" rows="3"><?php echo ( !empty( $job_details->parking_requirements ) ) ? $job_details->parking_requirements : '' ?></textarea>
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">Special Instructions</label>
							<textarea name="special_instructions" type="text" class="form-control" rows="3"><?php echo ( !empty( $job_details->special_instructions ) ) ? $job_details->special_instructions : '' ?></textarea>
						</div>

					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">&nbsp;&nbsp;&nbsp;&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;</button>
						<button id="update-evidoc-name-btn" type="button" class="update-job-btn btn btn-sm btn-success">Save Changes</button>
					</div>
				</div>
			</div>
		</form>
	</div>
<?php } ?>

<script>
	$( document ).ready( function(){

		$( '.edit-job-sheet' ).click( function(){
			$("#edit-job-sheet-modal").modal("show");
		} );

		$( '.x_panel' ).on( 'keyup', '.required', function(){
			var fieldName = $( this ).attr( 'name' );
			$( '#'+fieldName+'-errors' ).text( '' );
		});

		//** Validate any inputs that have the required class, if empty return the name attribute **/
		function check_inputs( container_name ){
			
			var result = false;
			var panel  = "." + container_name;
			
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

		//Submit form for processing
		$( '.update-job-btn' ).click( function( event ){

			var inputs_state = check_inputs( 'x_panel' );

			if( inputs_state ){
				$( '[name="'+inputs_state+'"]' ).focus();
				var labelText = $( '[name="'+inputs_state+'"]' ).parent().find('label').text();
				$( '#'+inputs_state+'-errors' ).text( ucwords( labelText ) +' is a required' );
				return false;
			}

			var jobType 	= $( '[name="job_type_id"]' ).val();
			var jobTypeId 	= $( '#job_type_id option:selected' ).val();
			
			jobTypeId		= ( jobTypeId ) ? jobTypeId : ( ( jobType ) ? jobType : undefined );
			
			if( jobTypeId.length == 0 || jobTypeId === undefined ){
				swal({
					type: 'error',
					title: 'Job Type is required!',
					text: 'Please select a valid Job Type'
				});
				return false;
			}

			/* var reGion 		= $( '[name="region_id"]' ).val();
			var regionId 	= $( '#region_id option:selected' ).val();
			regionId		= ( regionId ) ? regionId : ( ( reGion ) ? reGion : undefined );
			
			if( regionId.length == 0 || regionId === undefined ){
				swal({
					type: 'error',
					title: 'Region is required!',
					text: 'Please select a valid Region name'
				});
				return false;
			} */

			var formID = $( this ).closest( 'form' ).attr( 'id' );
			event.preventDefault();
			var formData = $('#'+formID ).serialize();

			swal({
				title: 'Confirm job update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url( 'webapp/job/update_job/' ).$job_details->job_id; ?>",
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

		//Delete Job from
		$('.delete-job-btn').click(function(){

			var jobId = $(this).data( 'job_id' );
			swal({
				title: 'Confirm delete Job?',
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/delete_job/'.$job_details->job_id ); ?>",
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
									window.location.href = "<?php echo base_url('webapp/job/jobs'); ?>";
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

		$( '.coming-soon' ).click( function(){
			swal({
				title: 'Content coming soon!',
				text: 'This link is not available yet',
			})
		});
	});
</script>


