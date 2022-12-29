<div class="row">
	<div class="col-md-6 col-sm-6 col-xs-12">
		<form id="update-project-form" method="post" >
			<input type="hidden" name="page" value="details" />
			<input type="hidden" name="project_id" value="<?php echo $project_details->project_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<div class="x_panel tile has-shadow">
				<legend>Update Project Details</legend>
				<div class="input-group form-group">
					<label class="input-group-addon">Project Name</label>
					<input name="project_name" class="form-control" type="text" placeholder="Project Name" value="<?php echo $project_details->project_name; ?>" />
				</div>
				
				<div class="input-group form-group">
					<label class="input-group-addon">Project Ref</label>
					<input class="form-control" type="text" placeholder="Project Ref" readonly value="<?php echo strtoupper($project_details->project_ref); ?>" />
				</div>

				<div class="input-group form-group">
					<label class="input-group-addon">Project Type</label>
					<select name="project_type_id" class="form-control">
						<option>Please select</option>
						<?php if (!empty($project_types)) {
						    foreach ($project_types as $k => $project_type) { ?>
							<option value="<?php echo $project_type->project_type_id; ?>" <?php echo ($project_details->project_type_id == $project_type->project_type_id) ? 'selected=selected' : ''; ?> ><?php echo $result_status->result_status_alt; ?></option>
						<?php }
						    } ?>
					</select>	
				</div>
				
				<div class="input-group form-group">
					<label class="input-group-addon">Project Status</label>
					<select name="project_status_id" class="form-control">
						<option>Please select</option>
						<?php if (!empty($project_statuses)) {
						    foreach ($project_statuses as $k => $result_status) { ?>
							<option value="<?php echo $result_status->project_status_id; ?>" <?php echo ($project_details->project_status_id == $result_status->project_status_id) ? 'selected=selected' : ''; ?> ><?php echo $result_status->project_status; ?></option>
						<?php }
						    } ?>
					</select>	
				</div>

				<?php if ($this->user->is_admin || !empty($permissions->can_edit) || !empty($permissions->is_admin)) { ?>
					<div class="row">
						<div class="col-md-6">
							<button id="update-project-btn" class="btn btn-block btn-flow btn-success" type="button" >Update Project</button>					
						</div>
						<?php if ($this->user->is_admin || !empty($permissions->can_delete) || !empty($permissions->is_admin)) { ?>
							<div class="col-md-6">
								<button data-action_btn="delete-project-btn" class="archive-project btn btn-block btn-flow btn-danger" type="button" >Archive Project</button>					
							</div>
						<?php } ?>
					</div>
				<?php } else { ?>
					<div class="row col-md-6">
						<button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>					
					</div>
				<?php } ?>
			</div>
		</form>
	</div>
	<div class="col-md-6 col-sm-6 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Project Stats</legend>

		</div>
	</div>
	
</div>

<script>
	$( document ).ready(function(){
		
		//Submit form for processing
		$( '#update-project-btn' ).click( function( event ){
			event.preventDefault();
			var formData = $('#update-project-form').serialize();
			swal({
				title: 'Confirm project update?',
				// type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/project/update_project/'.$project_details->project_id); ?>",
						method:"POST",
						data:formData,
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
									location.reload();
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

