<div class="row">
	<?php if ($this->user->is_admin || !empty($permissions->can_add) || !empty($permissions->is_admin)) { ?>
		<div class="hide col-md-6 col-sm-6 col-xs-12">
			<form action="<?php echo base_url('/site/update_update/'.$site_details->site_id); ?>" method="post" class="form-horizontal">
				<div class="x_panel tile has-shadow">
					<legend>Create New Site Jobs</legend>
					<div class="hide input-group form-group">
						<label class="input-group-addon" >Site Name</label>
						<input name="site_name" value="<?php echo !empty($site_details->site_name) ? $site_details->site_name : ''; ?>" class="form-control" type="text" placeholder="Site Name"  />
					</div>
					<div class="hide input-group form-group">
						<label class="input-group-addon" >Site Reference</label>
						<input name="site_name" value="<?php echo !empty($site_details->site_reference) ? $site_details->site_reference : ''; ?>" class="form-control" type="text" placeholder="Site Reference"  />
					</div>
						<div class="row col-md-6">
							<button id="create-lead-btn" class="btn btn-sm btn-block btn-flow btn-success btn-next" type="submit" >Add New Job</button>					
						</div>
				</div>
			</form>
		</div>
	<?php } ?>
	
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Existing Jobs <span id="add-new-job" data-toggle="modal" data-target="#new-job-modal-md" class="pull-right pointer" title="Add new Job" ><i class="fas fa-plus text-green"></i></span></legend>
			<?php if ($this->user->is_admin || !empty($permissions->can_view) || !empty($permissions->can_edit) || !empty($permissions->is_admin)) { ?>
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12 pull-right" style="margin-bottom:10px;color:#5c5c5c">
						<div class="input-group">
							<input type="text" class="form-control <?php echo $module_identier; ?>-search_input" id="searchInput" style="color:#5c5c5c" value="" placeholder="Search Site Jobs...">
						</div>
					</div>					
				</div>
				<table class="sorttable sortable datatable" style="width:100%; font-size:90%">
					<tr>
						<th width="7%">JOB ID</th>
						<th width="20%">JOB TYPE</th>
						<th width="8%">JOB DATE</th>
						<th width="30%">WORKS REQUIRED</th>
						<th width="10%">STATUS</th>
						<th width="5%">PRIORITY</th>
						<th width="10%">ASSIGNEE</span></th>
						<th width="5%"><span class="pull-right">ACTION</span></th>
					</tr>
					<tbody id="fbody">
					<?php if (!empty($site_jobs)) {
					    foreach ($site_jobs as $job_record) { ?>
						<tr>
							<td><a href="<?php echo base_url('webapp/job/profile/'.$job_record->job_id); ?>"><?php echo $job_record->job_id; ?></a></td>
							<td><?php echo $job_record->job_type; ?></td>
							<td><?php echo (valid_date($job_record->job_date)) ? date('d-m-Y', strtotime($job_record->job_date)) : ''; ?></td>
							<td><?php echo !empty($job_record->works_required) ? $job_record->works_required : ''; ?></td>
							<td><?php echo !empty($job_record->job_status) ? $job_record->job_status : ''; ?></td>
							<td><?php echo !empty($job_record->job_priority_rating) ? $job_record->job_priority_rating : ''; ?></td>
							<td><?php echo $job_record->assignee; ?></span></td>
							<td>
								<span class="pull-right">
									<span>
										<span class="text-green text-bold"><a href="<?php echo base_url('webapp/job/profile/'.$job_record->job_id); ?>"><i title="Click here to view this Job record" class="fas fa-edit text-blue pointer"></i></a> &nbsp;&nbsp;</span>
										<?php if ($this->user->is_admin || !empty($permissions->can_delete) || !empty($permissions->is_admin)) { ?>
											<span class="text-red text-bold"><i title="Click here to delete Job" class="delete-job-btn fas fa-trash-alt text-red pointer" data-job_id = "<?php echo $job_record->job_id; ?>"></i></span>
										<?php } ?>
									</span>
								</span>
							</td>
						</tr>
					<?php }
					    } else { ?>
						<tr>
							<td colspan="8"><?php echo $this->config->item('no_records'); ?></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			<?php } ?>
		</div>		
	</div>
	
	<div id="new-job-modal-md" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title" id="myModalLabel">Create New Job</h4>
				</div>
				<div class="modal-body">
					<div class="">
						<form id="job-creation-form" enctype="multipart/form-data" >
							<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
							<input type="hidden"  name="site_id" value="<?php echo $site_details->site_id; ?>"/>
							<input type="hidden"  name="address_id" value="<?php echo $site_details->site_address_id; ?>"/>
							<input type="hidden"  name="region_id" value="<?php echo $site_details->region_id; ?>"/>
							<input type="hidden"  name="page" value="details"/>
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
							
							<?php if ($this->user->is_admin || !empty($permissions->is_module_admin)) { ?>
								<div class="input-group form-group">
									<label class="input-group-addon">Route this Job now?</label>
									<select id="routing-opts" class="form-control">
										<option value="">Please select</option>
										<option value="Yes">Yes</option>
										<option value="No" selected >No</option>									
									</select>	
								</div>

								<div class="routing-options" style="display:none" >
									<div class="input-group form-group">
										<label class="input-group-addon">Job date</label>
										<input id="job_date" name="job_date" class="form-control datepicker" type="text" placeholder="Job date" value="" />
									</div>
									
									<div class="input-group form-group">
										<label class="input-group-addon">Job assignee</label>
										<select id="assigned_to" name="assigned_to" class="form-control">
											<option value="" >Please select</option>
											<?php if (!empty($operatives)) {
											    foreach ($operatives as $k => $operative) { ?>
												<option value="<?php echo $operative->id; ?>" ><?php echo $operative->first_name." ".$operative->last_name; ?></option>
											<?php }
											    } ?>
										</select>	
									</div>
								</div>							
							<?php } ?>
							
							<div class="hide input-group form-group">
								<label class="input-group-addon">Job status</label>
								<select id="job_status" class="form-control">
									<option value="">Please select</option>
									<?php if (!empty($job_statuses)) {
									    foreach ($job_statuses as $k => $job_status) { ?>
										<option value="<?php echo $job_status->status_id; ?>" ><?php echo $job_status->job_status; ?></option>
									<?php }
									    } ?>
								</select>	
							</div>
							
							<div class="input-group form-group">
								<label class="input-group-addon">Priority Rating</label>
								<select id="job_priority_rating" name="job_priority_rating" class="form-control">
									<option value="">Please select</option>
									<?php if (!empty($priority_ratings)) {
									    foreach ($priority_ratings as $rating) { ?>
										<option value="<?php echo $rating; ?>" ><?php echo ucwords(strtolower($rating)); ?></option>
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
								<select name="is_chargeable" class="form-control">
									<option>Please select</option>
									<option value="1" >Yes</option>
									<option value="0" >No</option>
								</select>
							</div>
							
							<div class="input-group form-group">
								<label class="input-group-addon">Site Zone</label>
								<select id="site_zone_id" name="zone_id" class="form-control" >
									<option value="" >Please select</option>
									<?php if (!empty($site_zones)) {
									    foreach ($site_zones as $k => $site_zone) { ?>
										<option value="<?php echo $site_zone->zone_id; ?>" data-site_id="<?php echo $site_zone->site_id; ?>" ><?php echo $site_zone->zone_name; ?><?php echo !empty($site_zone->zone_description) ? ' - '.$site_zone->zone_description : ''; ?></option>
									<?php }
									    } ?>
								</select>
							</div>
							
							<div style="display:none" class="site-location-container" >
								<div class="input-group form-group" >
									<label class="input-group-addon">Site Location</label>
									<select id="site_location_id" name="location_id" class="form-control" >
										
									</select>
								</div>
							</div>
							
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
							
							<?php if ($this->user->is_admin || !empty($permissions->can_add) || !empty($permissions->is_admin) || !empty($permissions->is_module_admin)) { ?>
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
</div>

<script>
	$(document).ready(function(){
		
		$("#searchInput").keyup(function () {
			//split the current value of searchInput
			var data = this.value.toUpperCase().split(" ");
			//create a jquery object of the rows
			var jo = $("#fbody").find("tr");
			if (this.value == "") {
				jo.show();
				return;
			}
			//hide all the rows
			jo.hide();

			//Recusively filter the jquery object to get results.
			jo.filter(function (i, v) {
				var $t = $(this);
				for (var d = 0; d < data.length; ++d) {
					if ($t.text().toUpperCase().indexOf(data[d]) > -1) {
						return true;
					}
				}
				return false;
			})
			//show the rows that match.
			.show();
		}).focus(function () {
			this.value = "";
			$(this).css({
				"color": "black"
			});
			$(this).unbind('focus');
		}).css({
			"color": "#C0C0C0"
		});

		$( '#site_zone_id' ).change( function(){
			var selectedZoneId  = $( 'option:selected', this ).val();
			var siteId  		= $( 'option:selected', this ).data( 'site_id' );
			if( selectedZoneId.length > 0 ){				
				$.post( "<?php echo base_url('webapp/site/get_zone_locations'); ?>",{ site_id:siteId, zone_id:selectedZoneId },function(result){
					$( "#site_location_id" ).html( result[ "zone_locations" ] );
					$( '.site-location-container' ).slideDown();
				},"json" );
				
			} else {
				$( '.site-location-container' ).slideUp();
			}
		});
		
		$( '#routing-opts' ).change( function(){
			var selectedOpt = $( 'option:selected', this ).val();
			if( selectedOpt == 'Yes' ){
				$( '#job_date' ).attr( 'name', 'job_date' );
				$( '#assigned_to' ).attr( 'name', 'assigned_to' );
				$( '.routing-options' ).slideDown();
			} else {
				$( '#job_date' ).removeAttr( 'name' );
				$( '#assigned_to' ).removeAttr( 'name' );
				$( '.routing-options' ).slideUp();
			}
		});
	
		$('.address-lookup-results').hide();
		$('.address-postcode').focus(function(){
			var postCode = $(this).val();
			if( postCode.length > 0 ){
				$.post("<?php echo base_url("lms/get_addresses_by_postcode"); ?>",{postcodes:postCode},function(result){
					$("#address-select").html(result["addresses_list"]);				
				},"json");
				
				$('.address-lookup-results').show( 'slow' );
			}
		});

		$("#address-select").change(function(){
			var addresId = $("option:selected", this).val();
			var leadId   = $("[name='site_id']").val();
			var postCode = $("#post-code").val();
		
			if( addresId.length > 0 ){
				$.post("<?php echo base_url('lms/ajax_lms_update'); ?>",
					{
						site_id:leadId,
						linked_address_id:addresId,
						post_code:postCode
					
					},function(result){
						$("#ajax-feedback").html(result["feedback"]).delay(4000).fadeOut(1500);
						// Wait for the feedback message to fade out then reload the page
						setTimeout(function() {
							location.reload();
						}, 5000);
					},"json"
				);
			}
		});

		$('#delete-lead').click(function(){
			return confirm('Are you sure you want to delete this lead?');
		});
		
		
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
						header: {
							'Accept': 'application/json',
							'Content-Type': 'multipart/form-data',
						},
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
						url:"<?php echo base_url('webapp/job/delete_job/'); ?>" + jobId,
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
									window.location.reload();
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