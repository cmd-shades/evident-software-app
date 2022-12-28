<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Existing Jobs <span id="add-new-job" data-toggle="modal" data-target="#new-job-modal-md" class="pull-right pointer" title="Add new Job" ><i class="fas fa-plus text-green"></i></span></legend>
			<?php if( $this->user->is_admin || !empty( $permissions->can_view ) || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
				<table class="table table-responsive" style="width:100%">
					<tr>
						<th width="15%">Job Id</th>
						<th width="20%">Job Date</th>
						<th width="18%">Job Type</th>
						<th width="15%">Assignee</span></th>
						<th width="27%"><span class="pull-right">Action</span></th>
					</tr>
					<?php if( !empty( $contract_jobs ) ){ foreach( $contract_jobs as $job_record ) { ?>
						<tr>
							<td><a href="<?php echo base_url( 'webapp/job/profile/'.$job_record->job_id ); ?>"><?php echo $job_record->job_id; ?></a></td>
							<td><?php echo date( 'd-m-Y', strtotime( $job_record->job_date ) ); ?></td>
							<td><?php echo ucwords( $job_record->job_type ); ?></td>
							<td><?php echo $job_record->assignee; ?></span></td>
							<td>
								<span class="pull-right">
									<span>
										<span class="text-green text-bold"><a href="<?php echo base_url( 'webapp/job/profile/'.$job_record->job_id ); ?>">View Job</a></span>
									</span>
								</span>
							</td>
						</tr>
					<?php } }else{ ?>
						<tr>
							<td colspan="5"><?php echo $this->config->item('no_records'); ?></td>
						</tr>
					<?php } ?>
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
					<div class="x_panel tile has-shadow">
						<form id="job-creation-form" >
							<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
							<input type="hidden"  name="contract_id" value="<?php echo $contract_details->contract_id; ?>"/>
							<input type="hidden"  name="page" value="details"/>
							<div class="input-group form-group">
								<label class="input-group-addon">Job type</label>
								<select name="job_type_id" class="form-control">
									<option>Please select</option>
									<?php if( !empty( $job_types ) ) { foreach( $job_types as $k => $job_type ) { ?>
										<option value="<?php echo $job_type->job_type_id; ?>" ><?php echo $job_type->job_type; ?></option>
									<?php } } ?>
								</select>	
							</div>
							<div class="input-group form-group">
								<label class="input-group-addon">Job date</label>
								<input name="job_date" class="form-control datepicker" type="text" placeholder="Job date" value="" />
							</div>
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
								<label class="input-group-addon">Job assignee</label>
								<select name="assigned_to" class="form-control">
									<option>Please select</option>
									<?php if( !empty($operatives) ) { foreach( $operatives as $k => $operative ) { ?>
										<option value="<?php echo $operative->id; ?>" ><?php echo $operative->first_name." ".$operative->last_name; ?></option>
									<?php } } ?>
								</select>	
							</div>
							
							<div class="form-group">
								<label class="">Job notes</label>
								<textarea name="job_notes" class="form-control" type="text" value="" style="width:100%; height:85px;" placeholder=""></textarea>
							</div>
							
							<?php if( $this->user->is_admin || !empty( $permissions->can_add ) || !empty( $permissions->is_admin ) ){ ?>
								<div class="row">
									<div class="col-md-12">
										<button id="create-job-btn" class="btn btn-sm btn-block btn-flow btn-success btn-next" type="button" >Book Job</button>					
									</div>
								</div>
							<?php }else{ ?>
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
	$( document ).ready(function(){

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
				$.post("<?php echo base_url("lms/ajax_lms_update"); ?>",
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