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

</style>
<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<?php if( !empty( $workflows ) ){ ?>
		<div class="x_panel tile shadow">
			<div class="rows">
				<legend>Action Items</legend>
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12 createWFButtonDiv">
						<a class="btn btn-sm btn-flow btn-success btn-next pull-left createWFButton">Create New Action &nbsp;<i class="fas fa-chevron-down"></i></a>
					</div>
				</div>
				<form action="<?php echo base_url( "webapp/contract/batch_update_workflow" ) ?>" method="post" name="batchUpdateWFForm" id="batchUpdateWFForm">
					<input type="hidden" name="postdata[contract_id]" value="<?php echo $profile_data[0]->contract_id; ?>" />
					<div class="control-group form-group">					
						<table class="table" style="border-top:0;" >
							<tr class="text-blue">
								<th class="width_60">ID</th>
								<th class="width_160">Action Name</th>
								<th class="width_200">Action Subname</th>
								<th class="width_80">Start Date</th>
								<th class="width_80">End Date</th>
								<th class="width_80">Reminder</th>
								<th class="width_120">Action Status</th>
								<th class="width_120">Assignee</th>
								<th class="width_40">Update</th>
								<th class="width_60">Delete</th>
							</tr>
						</table>
						<hr>
					</div>
					<div class="control-group form-group table_body">
						<table class="table">
							<tbody>
								<?php foreach( $workflows as $row ){ ?>
									<tr>
										<input type="hidden" name="wf_id" value="<?php echo $row->wf_id; ?>" />
										<td class="width_60">
											<input type="text" class="form-control" value="#<?php echo str_pad( $row->wf_id, 4, '0', STR_PAD_LEFT ); ?>" disabled="disabled" /></td>
										<td class="width_160">
											<select name="postdata[batch_update][<?php echo $row->wf_id; ?>][wf_name_id]" type="text" class="form-control" id="wf_name_id_update"><option value="">Please select</option><?php
											if( !empty( $wf_task_names ) ){
												foreach( $wf_task_names as $task ){ ?>
													<option value="<?php echo $task->wf_name_id; ?>" <?php echo ( !empty( $row->wf_name_id ) && ( $row->wf_name_id == $task->wf_name_id ) ) ? 'selected="selected"' : '' ; ?> ><?php echo ucwords( $task->wf_name ); ?></option>
												<?php
												}
											} else { ?>
												<option value="7">Meeting Required</option>
											<?php } ?>
											</select>
										</td>
										<td class="width_200">
											<input type="text" class="form-control" name="postdata[batch_update][<?php echo $row->wf_id; ?>][wf_subname]" value="<?php echo $row->wf_subname; ?>" /></td>
										<td class="width_80">
											<input type="text" class="form-control datetimepicker" name="postdata[batch_update][<?php echo $row->wf_id; ?>][wf_start_date]" value="<?php echo date( 'd/m/Y', strtotime( $row->wf_start_date ) ); ?>" /></td>
										<td class="width_80">
											<input type="text" class="form-control datetimepicker" name="postdata[batch_update][<?php echo $row->wf_id; ?>][wf_end_date]" value="<?php  echo date( 'd/m/Y', strtotime( $row->wf_end_date ) ); ?>" /></td>
										<td class="width_80">
											<input type="text" class="form-control datetimepicker" name="postdata[batch_update][<?php echo $row->wf_id; ?>][reminder_date]" value="<?php  echo date( 'd/m/Y', strtotime( $row->reminder_date ) ); ?>" /></td>
										<td class="width_120">
											<select name="postdata[batch_update][<?php echo $row->wf_id; ?>][wf_status]" type="text" class="form-control" id="wf_status_update">
												<option value="">Please select</option>
												<option value="Awaiting Action" <?php echo ( !empty( $row->wf_status ) && ( strtolower( $row->wf_status ) == 'awaiting action' ) ) ? ( 'selected = "selected"' ) : '' ; ?>>Awaiting Action</option>
												<option value="Completed" <?php echo ( !empty( $row->wf_status ) && ( strtolower( $row->wf_status ) == 'completed' ) ) ? ( 'selected = "selected"' ) : '' ; ?>>Completed</option>
												<option value="Suspended" <?php echo ( !empty( $row->wf_status ) && ( strtolower( $row->wf_status ) == 'suspended' ) ) ? ( 'selected = "selected"' ) : '' ; ?>>Suspended</option>
												<option value="In Progress" <?php echo ( !empty( $row->wf_status ) && (  strtolower( $row->wf_status ) == 'in progress' ) ) ? ( 'selected = "selected"' ) : '' ; ?>>In Progress</option>
											</select>
										</td>
										<td class="width_120"><select name="postdata[batch_update][<?php echo $row->wf_id; ?>][assignee]" type="text" class="form-control" id="assignee_update"><option value="">Please select</option><?php echo $row->assignee.'  _|'; ?><?php
											if( !empty( $contract_leaders ) ){
												foreach( $contract_leaders as $user ){ ?>
													<option value="<?php echo $user->id; ?>" <?php echo ( !empty( $row->assignee ) && ( $user->id == $row->assignee ) ) ? 'selected="selected"' : '' ; ?> ><?php echo ucwords( $user->first_name.' '.$user->last_name ); ?></option>
												<?php } } else { ?>
												<option value="">Please, add users</option>
											<?php } ?>
											</select>
											</td>
										<!-- <td class="width_20_checkbox"><input type="checkbox" class="form-control" name="postdata[batch_update][<?php echo $row->wf_id; ?>][check]" value="yes" /></td> -->
										<td><input style="margin-left: 20px;" type="checkbox" name="postdata[batch_update][<?php echo $row->wf_id; ?>][check]" value="yes" /></td>
										<td class="width_60" >
											<?php if( $this->user->is_admin || !empty( $permissions->can_delete ) || !empty( $permissions->is_admin ) ){ ?>
												<a style="color: red;" onclick="return confirm( 'Are you sure you want to delete this item?' );" href="<?php echo base_url( "webapp/contract/delete_wf/".$row->wf_id."/".$profile_data[0]->contract_id."/workflow_items" ); ?>">Delete</a>
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
									<button type="submit" class="btn btn-sm btn-flow btn-success btn-next" id="updateWFButton" style="width: 100%;">Update Actions</button>
								</div>
						<?php } else { ?>
								<div class="col-md-3 col-sm-3 col-xs-12">
									<button class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled>Insufficient permissions</button>
								</div>
						<?php } ?>
					</div>
				</form>
			</div>
		</div>
		<?php } else { ?>
			<div class="x_panel tile shadow">
				<div class="rows">
					<legend>Actions</legend>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<h6>No Actions under this Contract</h6>
						</div>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12 createWFButtonDiv">
						<a class="btn btn-sm btn-flow btn-success btn-next pull-left createWFButton">Create New Action &nbsp;<i class="fas fa-chevron-down"></i></a>
					</div>
				</div>
			</div>
		<?php } ?>
		<div class="row create_wf_form" style="display: none;">
			<form action="<?php echo base_url( "webapp/contract/add_workflow" ) ?>" method="post" name="add_workflow" id="workflowForm" novalidate>
				<input type="hidden" name="postdata[contract_id]" value="<?php echo $profile_data[0]->contract_id; ?>" />
				<input type="hidden" name="postdata[account_id]" value="<?php echo $profile_data[0]->account_id; ?>" />
				
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="x_panel tile fixed_height_300">
						<legend>Create Action</legend>
						<div class="input-group form-group">
							<label class="input-group-addon">Action - Task Name *:</label>
							<select name="postdata[wf_name_id]" type="text" class="form-control" id="wf_name_id" required />
								<option value="">Please select</option>
								<?php if( !empty( $wf_task_names ) ){
									foreach( $wf_task_names as $row ){ ?>
										<option value="<?php echo $row->wf_name_id; ?>"><?php echo ucwords( $row->wf_name ); ?></option>
									<?php
									} } else { ?>
									<option value="7">Meeting Required</option>
								<?php } ?>
							</select>
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Action - Sub name:</label>
							<input name="postdata[wf_subname]" type="text" class="form-control" id="wf_subname" required />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Action - Reference *:</label>
							<input name="postdata[wf_reference]" type="text" class="form-control" id="wf_reference" required />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Assignee:</label>
							<select name="postdata[assignee]" type="text" class="form-control" id="assignee" required>
								<option value="">Please select</option>
								<?php if( !empty( $contract_leaders ) ){
									foreach( $contract_leaders as $row ){ ?>
										<option value="<?php echo $row->id; ?>"><?php echo ucwords( $row->first_name.' '.$row->last_name ); ?></option>
									<?php
									} } else { ?>
									<option value="">Please, add users</option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>

				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="x_panel tile fixed_height_300">
						<div class="input-group form-group">
							<label class="input-group-addon">Start Date:</label>
							<input type="text" name="postdata[wf_start_date]" value="<?php echo date( 'd/m/Y' ); ?>" class="form-control datetimepicker" placeholder="<?php echo date( 'd/m/Y' ); ?>" data-date-format="DD/MM/Y" required />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Reminder Date:</label>
							<input type="text" name="postdata[reminder_date]" value="<?php echo date( 'd/m/Y' ); ?>" class="form-control datetimepicker" placeholder="<?php echo date( 'd/m/Y' ); ?>" data-date-format="DD/MM/Y" required />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Finish Date:</label>
							<input type="text" name="postdata[wf_end_date]" value="<?php echo date( 'd/m/Y' ); ?>" class="form-control datetimepicker" placeholder="<?php echo date( 'd/m/Y' ); ?>" data-date-format="DD/MM/Y" required />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Description:</label>
							<textarea name="postdata[wf_description]" type="text" cols="50" rows="4" class="form-control" id="wf_description"></textarea>
						</div>

						<?php if( $this->user->is_admin || !empty( $permissions->can_add ) || !empty( $permissions->is_admin ) ){ ?>
							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12 pull-left">
									<button id="addWorkflowItem" class="btn btn-sm btn-block btn-flow btn-success btn-next" type="submit">Add Action</button>
								</div>
							</div>
						<?php } else { ?>
							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12 pull-left">
									<button class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled>Insufficient permissions</button>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
$( "#workflowForm" ).submit( function(){
	$( this ).find( 'button#addWorkflowItem' ).prop( "disabled", true );
});
</script>