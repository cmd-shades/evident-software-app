<style type="text/css">
legend{
	padding-bottom: 10px;
}

.createWFButton{
	position: absolute;
	top: 10px;
	right: 5px;
}

.createWFButtonDiv{
	position: absolute; 
	top: 0; 
	right: 0;
}

.table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    line-height: 1.42857143;
    vertical-align: top;
    border-top: 0;
	border-bottom: 0;
	
}

#project-actions-container {
	font-family: 'Quicksand', sans-serif;
	font-weight: normal !important;
}

.actions_panel {
    -webkit-box-shadow: 0px 0px 42px -17px rgba(0,0,0,0.75);
    -moz-box-shadow: 0px 0px 42px -17px rgba(0,0,0,0.75);
    box-shadow: 0px 0px 42px -17px rgba(0,0,0,0.75);
    margin-bottom: 15px;
}

.text-action-headers th {
	color: rgb(74, 74, 74);
	font-size: 15px;
	font-weight: normal !important;
}

.module-title {
	color: rgb(74, 74, 74);
	font-size: 20px;
}

</style>


<div class="row">
	<div id='project-actions-container' class="col-md-12 col-sm-12 col-xs-12">
		<?php if( !empty( $project_actions ) ){ ?>
		<div class="x_panel tile shadow actions_panel">
			<a class="btn btn-sm btn-flow btn-warning btn-next btn-selectall" style="float:right">Select All</i></a>
			<a class="btn btn-sm btn-flow btn-warning btn-success btn-createworkflow-modal" style="float:right">Create Project Action</i></a>
			<div class="rows" style='padding:10px;'>
				<legend class='module-title'>Project Actions (<?php print_r(count($project_actions)); ?>)</legend>
				<div class="control-group form-group table_body">
					<table class="table" id='project-action-table'>
						<tr class="text-action-headers">
							<th class="width_60">ID</th>
							<th class="width_200">Name</th>
							<th class="width_120">Reference</th>
							<th class="width_120">Description</th>
							<th class="width_120">Start Date</th>
							<th class="width_120">End Date</th>
							<th class="width_120">Status</th>
							<th class="width_40" style="text-align:center;">Update</th>
							<th class="width_60" style="text-align:center;">Delete</th>
						</tr>
						<tbody>
							<?php foreach( $project_actions as $row ){ ?>
																
								<tr class='project-action-row' data-action_id = "<?php echo $row->project_action_id; ?>">
									
									<input class='action-col' type = "hidden" data-action_col = 'project_action_id' value='<?php echo $row->project_action_id; ?>'>
									
									<td class="width_60">
										<input type="text" class="form-control" value="#<?php echo str_pad( $row->project_action_id, 4, '0', STR_PAD_LEFT ); ?>" disabled="disabled" /></td>
										
									<td class="width_200">
										<input type="text" class="form-control action-col" data-action_col = 'project_action' value="<?php echo $row->project_action; ?>" /></td>
									
									<td class="width_200">
										<input type="text" class="form-control action-col" data-action_col = 'project_action_ref' value="<?php echo $row->project_action_ref; ?>" /></td>
															
									<td class="width_200">
										<input type="text" class="form-control action-col" data-action_col = 'action_description' value="<?php echo $row->action_description; ?>" /></td>
									
									<td class="width_200">
										<input type="text" class="form-control datepicker action-col" data-action_col = 'action_start_date' value="<?php echo $row->action_start_date; ?>" /></td>
									
									<td class="width_200">
										<input type="text" class="form-control datepicker action-col" data-action_col = 'action_end_date' value="<?php echo $row->action_end_date; ?>" /></td>
										
									<td class="width_200">
										<select class="form-control action-col" data-action_col = 'action_status' type="text" placeholder="Action Status" value="">
											<option>Please select</option>
											<?php if( !empty( $project_statuses ) ) { foreach( $project_statuses as $k => $result_status ) { ?>
												<option value="<?php echo $result_status->project_status; ?>" <?php echo ( strtolower( $row->action_status ) == strtolower( $result_status->project_status ) ) ? 'selected="selected"' : ''; ?> ><?php echo $result_status->project_status; ?></option>
											<?php } } ?>
										</select>
									</td>
											
									<td class="width_40"  style="text-align:center;">
										<?php if( $this->user->is_admin || !empty( $permissions->can_delete ) || !empty( $permissions->is_admin ) ){ ?>
											<input type="checkbox" class="project-update">
										<?php } ?>
									</td>
									<td class="width_60" style="text-align:center;">
										<?php if( $this->user->is_admin || !empty( $permissions->can_delete ) || !empty( $permissions->is_admin ) ){ ?>
											<i class="fas fa-trash-alt btn-deleteworkflow" style="color:red;font-size:18px;font-weight: normal !important;"></i>
										<?php } ?>
									</td>
								</tr>
							<?php
							} ?>
						</tbody>
					</table>
				</div>
				<div class="row" style="margin-top: 10px;">
					<?php if( $this->user->is_admin || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
							<div class="col-md-3 col-sm-3 col-xs-12">
								<button type="button" class="btn btn-sm btn-flow btn-success btn-next hidden" id="updateActionsButton" style="width: 100%;">Update Project Actions</button>
							</div>
					<?php } else { ?>
							<div class="col-md-3 col-sm-3 col-xs-12">
								<button class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled>Insufficient permissions</button>
							</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php } else { ?>
			<div class="x_panel tile shadow actions_panel">
				<div class="rows">
					<legend class='module-title'>Project Actions</legend>
					<a class="btn btn-sm btn-flow btn-warning btn-success btn-createworkflow-modal" style="float:right">Create Project Action</i></a>
					<div class="col-md-6 col-sm-6 col-xs-12">
						
						<div class="row">
							<h6>No Project Actions found!</h6>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>

	</div>
</div>

<script>

all_checked = false

$(".btn-selectall").on('click', function(event) {
	all_checked = !all_checked;
	$(".project-update").each(function(index, checkbox) {
		$(checkbox).prop('checked', all_checked);
	});
})

$(".btn-createworkflow-modal").on('click', function(event) {
	$("#create-wf").modal()
})


</script>

<div id="create-wf" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
	
    <div class="modal-content">
  		<div class="x_panel tile has-shadow">
			<div class="row modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<legend>Create Project Action</legend>
			</div>			
			<div class="input-group form-group">
				<label class="input-group-addon">Action Name</label>
				<input class="form-control action-col" data-action_col = 'project_action' type="text" placeholder="Action Name" value="" required>
			</div>
			<div class="input-group form-group">
				<label class="input-group-addon">Action Reference</label>
				<input class="form-control action-col" data-action_col = 'project_action_ref' type="text" placeholder="Action Reference" value="" required>
			</div>
			<div class="input-group form-group">
				<label class="input-group-addon">Assignee</label>
				<select class="form-control action-col" data-action_col = 'assignee' type="text" placeholder="Assignee">
					<option>Please select</option>
					<?php if( !empty( $operatives ) ) { foreach( $operatives as $k => $operative ) { ?>
						<option value="<?php echo $operative->id; ?>" ><?php echo $operative->first_name." ".$operative->last_name; ?></option>
					<?php } } ?>
				</select>	
			</div>
			
			<div class="input-group form-group">
				<label class="input-group-addon">Action Description</label>
				<input class="form-control action-col" data-action_col = 'action_description' type="text" placeholder="Action Description" value="" required>
			</div>
			<div class="input-group form-group">
				<label class="input-group-addon">Action Start Date</label>
				<input class="form-control datepicker action-col" data-action_col = 'action_start_date' type="text" placeholder="Action Start Date" value="" required>
			</div>
			<div class="input-group form-group">
				<label class="input-group-addon">Action End Date</label>
				<input class="form-control datepicker action-col" data-action_col = 'action_end_date' type="text" placeholder="Action End Date" value="" required>
			</div>

			<div class="input-group form-group">
				<label class="input-group-addon">Project Status</label>
				<select class="form-control action-col" data-action_col = 'action_status' type="text" placeholder="Action Status" value="">
					<option>Please select</option>
					<?php if( !empty( $project_statuses ) ) { foreach( $project_statuses as $k => $result_status ) { ?>
						<option value="<?php echo $result_status->project_status_id; ?>" ><?php echo $result_status->project_status; ?></option>
					<?php } } ?>
				</select>	
			</div>
			
			<a class="btn btn-sm btn-flow btn-warning btn-success btn-createworkflow" style="float:right">Create Project Action</i></a>
		</div>
		</div>
    </div>
  </div>
</div>
<script>

$( function() {
	
	$(".btn-createworkflow").on('click' ,function(event) {
		
		data = { 'project_id' : '<?php echo $project_id ?>' }
		
		valid = true;
		
		
		$("#create-wf").find(".action-col").each(function(index, element) {
			data[$(element).attr("data-action_col")] = $(element).val()
			if($(element).val() == ""){
				valid = false
			}
		});
		
		if(!valid){
			Swal.fire(
			  'Error!',
			  'Please fill out all action details!',
			  'error'
			)
			return false;
			
		}

		$.ajax({
  	      url:"<?php echo base_url('webapp/project/create_project_action'); ?>",
  	      method:"POST",
  	      data: data,
  	      dataType: "json",
  	      success:function( result ){
  	          if( result.status == 1 ){
				Swal.fire(
					'Success!',
					result.message,
					'success'
				).then(function (result) {
					location.reload();
				});
  	          }else{
				  Swal.fire(
				  'Error!',
				  'Unable to create project action!',
				  'error'
				).then(function (result) {
					location.reload();
				});
  	          }
  	      }
  	  });
    })
	
	$(".btn-deleteworkflow").on('click' ,function(event) {
		project_action_id = $(this).closest('.project-action-row').attr('data-action_id')
		$.ajax({
		    url:"<?php echo base_url('webapp/project/archive_project_action'); ?>",
		    method:"POST",
		    data:{ project_action_id : project_action_id, project_id : <?php echo $project_id ?>},
		    dataType: "json",
		    success:function( result ){
				console.log(result)
				if( result.status == 1 ){
					Swal.fire(
						'Success!',
						result.message,
						'success'
					).then(function (result) {
	  					location.reload();
	  				});
    	          } else {
  				  	Swal.fire(
	  				  'Error!',
	  				  'Unable to archive project action!',
	  				  'error'
	  				).then(function (result) {
	  					location.reload();
	  				});
    	          }
		    }
		});
	});
	
	// temporarily disable project mass update
	$(".project-update").on('click' ,function(event) {
		$(".project-update").prop('checked', false)
	});
	
	$("#updateActionsButton").on('click' ,function(event) {
		
		rows_to_update = []
		
		$("#project-action-table").find(".project-action-row").each(function(index, elem) {
			update_row = $(elem).find(".project-update").prop('checked')
			
			if(update_row){
				data = { 'project_id' : '<?php echo $project_id ?>' }
				
				$(elem).find(".action-col").each(function(index, element) {
					data[$(element).attr("data-action_col")] = $(element).val()
				});
				
				rows_to_update.push(data)
			}
				
		});
		
		console.log(rows_to_update)
		
	});

});
  


</script>