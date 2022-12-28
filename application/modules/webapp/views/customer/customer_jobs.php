<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Existing Customer Jobs <span id="add-new-job" data-toggle="modal" data-target="#new-job-modal-md" class="pull-right pointer" title="Book a new Job for this customer" ><i class="fas fa-plus text-green"></i></span></legend>
			<table class="no-border-top table table-responsive" style="width:100%">
				<tr>
					<th width="10%">Job Id</th>
					<th width="20%">Job Type</th>
					<th width="15%">Job Date</th>
					<th width="15%">Status</th>
					<th width="15%">Priority</th>
					<th width="15%">Assignee</span></th>
					<th width="10%"><span class="pull-right">Action</span></th>
				</tr>
				<?php if( !empty( $customer_jobs ) ){ ?>
					<?php foreach( $customer_jobs as $job_record ) { ?>
					<tr>
						<td><a href="<?php echo base_url( 'webapp/job/profile/'.$job_record->job_id ); ?>"><?php echo $job_record->job_id; ?></a></td>
						<td><?php echo $job_record->job_type; ?></td>
						<td><?php echo ( valid_date( $job_record->job_date ) ) ? date('d/m/Y', strtotime( $job_record->job_date ) ) : ''; ?></td>
						<td><?php echo !empty( $job_record->job_status ) ? $job_record->job_status : ''; ?></td>
						<td><?php echo !empty( $job_record->job_priority_rating ) ? $job_record->job_priority_rating : ''; ?></td>
						<td><?php echo $job_record->assignee; ?></span></td>
						<td>
							<div class="row">
								<div class="col-md-6"></div>
								<?php if( $this->user->is_admin || !empty( $permissions->can_delete ) || !empty( $permissions->is_admin ) ){ ?>
									<div class="col-md-2">
										<span class="hide text-red text-bold"><i title="Click here to delete Job" class="delete-item fas fa-trash-alt text-red pointer"></i></span>
									</div>									
								<?php } ?>
								<div class="col-md-2"><span class="hide pull-right text-blue text-bold"><a style="display:<?php echo ( empty( $job_record->assigned_to ) || !valid_date( $job_record->job_date ) ) ? 'block' : 'none'; ?>" href="<?php echo base_url( 'webapp/diary/routing?job='.$job_record->job_id ); ?>" class="pull-right pointer" title="Click to route this Job"><i class="fas fa-road"></i></a> &nbsp;&nbsp;</span></div>
								<div class="col-md-2"><span class="pull-right text-green text-bold"><a href="<?php echo base_url( 'webapp/job/profile/'.$job_record->job_id ); ?>"><i title="Click here to view this Job record" class="fas fa-edit text-blue pointer"></i></a> &nbsp;&nbsp;</span></div>
							</div>
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
	
	<div id="new-job-modal-md" class="modal fade" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title" id="myModalLabel">Create New Job</h4>
				</div>
				<div class="modal-body">
					<div class="">
						<form id="job-creation-form" >
							<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
							<input type="hidden"  name="customer_id" value="<?php echo $customer_details->customer_id; ?>"/>
							<input type="hidden"  name="address_id" value="<?php echo $customer_details->address_id; ?>"/>
							<input type="hidden"  name="page" value="jobs"/>
							
							<div class="input-group form-group">
								<label class="input-group-addon">Client Reference</label>
								<input name="client_reference" class="form-control" type="text" placeholder="Client Reference" value="" />
							</div>
							
							<div class="input-group form-group">
								<label class="input-group-addon">Job type &nbsp;&nbsp;&nbsp;<small><i title="This list is filtered by Contract if the customer has been linked to one" class="fas fa-info pointer"></i></small></label>
								<select id="job_type_id" name="job_type_id" class="form-control" style="width:100%" >
									<option value="" >Please select</option>
									<?php if( !empty( $job_types ) ) { foreach( $job_types as $k => $job_type ) { ?>
										<option value="<?php echo $job_type->job_type_id; ?>" data-job_duration="<?php echo $job_type->job_base_duration; ?>" ><?php echo $job_type->job_type; ?></option>
									<?php } } ?>
								</select>	
							</div>
							
							<?php if( $this->user->is_admin || !in_array( $this->user->id, $restricted_people ) ){ ?>
							
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
										<input id="job_date" name="job_date" class="form-control job-datepicker" type="text" placeholder="Job date" value="" />
									</div>
									
									<div class="input-group form-group">
										<label class="input-group-addon">Job assignee</label>
										<select id="assigned_to" name="assigned_to" class="form-control">
											<option value="" >Please select</option>
											<?php if( !empty( $operatives ) ) { foreach( $operatives as $k => $operative ) { ?>
												<option value="<?php echo $operative->id; ?>" ><?php echo $operative->first_name." ".$operative->last_name; ?></option>
											<?php } } ?>
										</select>	
									</div>
								</div>
							<?php } ?>
							
							<div class="hide input-group form-group">
								<label class="input-group-addon">Job status</label>
								<select class="form-control">
									<option value="">Please select</option>
									<?php if( !empty( $job_statuses ) ) { foreach( $job_statuses as $k => $job_status ) { ?>
										<option value="<?php echo $job_status->status_id; ?>" ><?php echo $job_status->job_status; ?></option>
									<?php } } ?>
								</select>	
							</div>
							
							<div class="input-group form-group">
								<label class="input-group-addon">Priority Rating</label>
								<select id="job_priority_rating" name="job_priority_rating" class="form-control">
									<option value="">Please select</option>
									<?php if( !empty( $priority_ratings ) ) { foreach( $priority_ratings as $rating ) { ?>
										<option value="<?php echo $rating; ?>" ><?php echo ucwords( strtolower( $rating ) ); ?></option>
									<?php } } ?>
								</select>
							</div>
							
							<div class="input-group form-group">
								<label class="input-group-addon">Job Duration (Slots)</label>
								<select id="job_duration" name="job_duration" class="form-control" >
									<option>Please select</option>
									<?php if( !empty( $job_durations ) ) { foreach( $job_durations as $k => $duration ) { ?>
										<option value="<?php echo $k; ?>" <?php echo ( $k == '1.0' ) ? 'selected="selected"' : '' ?> ><?php echo $duration; ?></option>
									<?php } } ?>
								</select>
							</div>
							
							<div class="hide input-group form-group" >
								<label class="input-group-addon">Is this Job Chargeable</label>
								<select name="is_chargeable" class="form-control">
									<option>Please select</option>
									<option value="1" selected="selected" >Yes</option>
									<option value="0" >No</option>
								</select>
							</div>

							<div class="form-group">
								<label class="strong">Works Required / Job Notes</label>
								<textarea name="works_required" rows="2" class="form-control" type="text" value="" style="width:100%;" placeholder="Description of works required / Job notes..."></textarea>
							</div>
							
							<div class="form-group">
								<label class="strong">Access Requirements</label>
								<textarea name="access_requirements" rows="2" class="form-control" type="text" value="" style="width:100%;" placeholder="Please provide any access requirements..."></textarea>
							</div>
							
							<div class="hide form-group">
								<label class="strong">Job notes</label>
								<textarea name="job_notes" rows="3" class="form-control" type="text" value="" style="width:100%;" placeholder=""></textarea>
							</div>
							
							<div class="form-group">
								<label class="strong">Parking Requirements</label>
								<textarea name="parking_requirements" rows="3" class="form-control" type="text" value="" style="width:100%;" placeholder="Parking Requirements "></textarea>
							</div>
							
							<?php if( $this->user->is_admin || !empty( $permissions->can_add ) || !empty( $permissions->is_admin ) ){ ?>
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

	function matchCustom( params, data ) {
		// If there are no search terms, return all of the data
		if ( $.trim( params.term ) === '' ) {
			return data;
		}

		// Do not display the item if there is no 'text' property
		if ( typeof data.text === 'undefined' ) {
			return null;
		}

		// `params.term` should be the term that is used for searching
		// `data.text` is the text that is displayed for the data object
		if ( data.text.indexOf(params.term) > -1 ) {
			var modifiedData = $.extend({}, data, true);
			modifiedData.text += ' (matched)';

			// You can return modified objects from here
			// This includes matching the `children` how you want in nested data sets
			return modifiedData;
		}

		// Return `null` if the term should not be displayed
		return null;
	}

	function matchStart( params, data ) {
		
		params.term = params.term || '';
		if ( data.text.toUpperCase().indexOf( params.term.toUpperCase() ) == 0 ) {
			return data;
		}
		return null;
		
		
		/*// If there are no search terms, return all of the data
		if ( $.trim(params.term) === '' ) {
			return data;
		}

		// Skip if there is no 'children' property
		if (typeof data.children === 'undefined') {
			return null;
		}

		// `data.children` contains the actual options that we are matching against
		var filteredChildren = [];
		$.each(data.children, function (idx, child) {
			if ( child.text.toUpperCase().indexOf(params.term.toUpperCase()) == 0) {
				filteredChildren.push( child );
			}
		});

		// If we matched any of the timezone group's children, then set the matched children on the group
		// and return the group object
		if ( filteredChildren.length ) {
			var modifiedData = $.extend({}, data, true);
			modifiedData.children = filteredChildren;

			// You can return modified objects from here
			// This includes matching the `children` how you want in nested data sets
			return modifiedData;
		}

		// Return `null` if the term should not be displayed
		return null;*/
	}
	

	$( document ).ready(function(){
		
		$( '#job_type_id' ).change( function(){
			var jobDuration = parseFloat( $( 'option:selected',this ).data( 'job_duration' ) ).toFixed(1);
			$('#job_duration option[value="'+jobDuration+'"]').prop( 'selected', true );
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
		
		$('.job-datepicker').datetimepicker({
			timepicker:false,
			format:'d-m-Y'
		});

		$( "#job_type_id" ).select2({
			matcher: matchCustom,
		});
	
		$('.address-lookup-results').hide();
		$('.address-postcode').focus(function(){
			var postCode = $(this).val();
			if( postCode.length > 0 ){
				$.post( "<?php echo base_url("lms/get_addresses_by_postcode"); ?>",{postcodes:postCode},function(result){
					$( "#address-select" ).html( result["addresses_list"] );				
				},"json" );
				
				$('.address-lookup-results').show( 'slow' );
			}
		});

		$("#address-select").change(function(){
			var addresId = $("option:selected", this).val();
			var leadId   = $("[name='customer_id']").val();
			var postCode = $("#post-code").val();
		
			if( addresId.length > 0 ){
				$.post("<?php echo base_url("lms/ajax_lms_update"); ?>",
					{
						customer_id:leadId,
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
			
			var jobType = $( '#job_type_id option:selected' ).val();
			
			if( jobType.length == 0 ){
				swal({
					type: 'error',
					text: 'Job type field is required!'
				});
				return false;
			}
			
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
		
	});
</script>