<style type="text/css">
textarea.height_100{
	height: 100px;
}

.height_500{
	height: 500px;
}

.min_height_300{
	min-height: 300px;
}
</style>

<div class="row">
	<?php if( $this->user->is_admin || !empty( $permissions->is_admin ) ){ ?>
	<div class="col-md-6 col-sm-6 col-xs-12">
		<div class="x_panel tile has-shadow height_500">
			<legend>Exception Details from the EviDoc</legend>
			<?php if( strtolower( $exception_details->record_type ) == "recommendations"  ){ ?>
				<div class="input-group form-group">
					<label class="input-group-addon">Recommendation</label>
					<textarea class="form-control height_100" disabled="disabled"><?php echo ( !empty( $exception_details->recommendations ) ) ? $exception_details->recommendations : "" ; ?></textarea>
				</div>
				
			<?php } elseif( strtolower( $exception_details->record_type ) == "failed" ) { ?>
			
				<div class="input-group form-group">
					<label class="input-group-addon">Failure Reasons</label>
					<textarea class="form-control height_100" disabled="disabled"><?php echo ( !empty( $exception_details->failure_reasons ) ) ? $exception_details->failure_reasons : "" ; ?></textarea>
				</div>
			<?php } ?>

			<div class="input-group form-group">
				<label class="input-group-addon">EviDoc Additonal Notes</label>
				<textarea class="form-control height_100" disabled="disabled"><?php echo ( !empty( $exception_details->additonal_notes ) ) ? $exception_details->additonal_notes : "" ; ?></textarea>
			</div>

			<div class="input-group form-group">
				<label class="input-group-addon">Priority Rating</label>
				<input class="form-control" type="text" placeholder="Priority Rating" value="<?php echo ( !empty( $exception_details->priority_rating ) ) ? ( $exception_details->priority_rating ) : ''; ?>" readonly="readonly" />
			</div>

			<div class="input-group form-group">
				<label class="input-group-addon">Estimated Repair Cost</label>
				<input class="form-control" type="text" placeholder="Estimated Repair Cost" value="<?php echo ( !empty( $exception_details->estimated_repair_cost ) ) ? ( $exception_details->estimated_repair_cost ) : ''; ?>" readonly="readonly" />
			</div>

			<div class="input-group form-group">
				<label class="input-group-addon">Date Created</label>
				<input class="form-control" type="text" placeholder="Date Created" value="<?php echo ( !empty( $exception_details->date_created ) ) ? ( $exception_details->date_created ) : ''; ?>" readonly="readonly" />
			</div>

			<div class="input-group form-group">
				<label class="input-group-addon">Created By</label>
				<input class="form-control" type="text" placeholder="Created By" value="<?php echo ( !empty( $exception_details->created_by_full_name ) ) ? ( $exception_details->created_by_full_name ) : ''; ?>" readonly="readonly" />
			</div>

			<?php /* if( $this->user->is_admin || !empty( $permissions->can_delete ) || !empty( $permissions->is_admin ) ){ ?>
				<div class="col-md-6">
					<button id="delete-exception-btn" class="btn btn-sm btn-block btn-flow btn-danger has-shadow" type="button" data-person_id="<?php echo $exception_details->id; ?>">Archive Exception</button>
				</div>
			<?php }  */ ?>
		</div>
	</div>

	<div class="col-md-6 col-sm-6 col-xs-12">
		<div class="x_panel tile has-shadow height_500">
			<legend>Action the Exception</legend>
			<form id="create-exception-note-form" class="form-horizontal">
				<input type="hidden" name="page" value="details" />
				<input type="hidden" name="exception_id" value="<?php echo $exception_details->id; ?>" />
				<input type="hidden" name="site_id" value="<?php echo $exception_details->site_id; ?>" />
				<input type="hidden" name="asset_id" value="<?php echo $exception_details->asset_id; ?>" />
				<input type="hidden" name="vehicle_reg" value="<?php echo $exception_details->vehicle_reg; ?>" />
				<input type="hidden" name="previous_action_status_id" value="<?php echo $exception_details->action_status_id; ?>" />
				<div class="input-group form-group">
					<label class="input-group-addon">Action Status</label>
					<select name="current_action_status_id" class="form-control">
						<option>Please select</option>
						<?php if( !empty( $action_statuses ) ){ foreach( $action_statuses as $k => $status ) { ?>
							<option value="<?php echo $status->action_status_id; ?>" <?php echo ( $exception_details->action_status_id == $status->action_status_id ) ? 'selected=selected' : ''; ?> ><?php echo $status->action_status; ?></option>
						<?php } } ?>
					</select>
				</div>

				<div class="input-group form-group">
					<label class="input-group-addon">Exception Note</label>
					<textarea name="exception_note" class="form-control min_height_300" placeholder="Please, provide a note"><?php echo ( !empty( $exception_notes ) ) ? $exception_notes : "" ; ?></textarea>
				</div>

				<?php if( $this->user->is_admin || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
					<div class="row">
						<div class="col-md-6">
							<button id="create_exception_note_btn" class="btn btn-sm btn-block btn-flow btn-success btn-next" type="button">Add Exception Note</button>
						</div>

					</div>
				<?php } else { ?>
					<div class="row col-md-6">
						<span class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled>Insufficient permissions</span>
					</div>
				<?php } ?>

				<div class="row hide">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="row col-md-6">
							<span class="btn btn-sm btn-block btn-flow btn-success btn-next" type="button">Insufficient permissions</span>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
	<?php } else { ?>
		<div class="row">
			<span><?php echo $this->config->item( 'no_records' ); ?></span>
		</div>
	<?php } ?>
</div>

<div class="row">
	<?php if( $this->user->is_admin || !empty( $permissions->is_admin ) ){ ?>
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Exception Logs</legend>
				<table style="width: 100%;">
					<thead>
						<tr>
							<th width="10%">Log ID</span></th>
							<th width="50%">Exception Note</th>
							<th width="20%">Date Created</th>
							<th width="20%">Created By</th>
						</tr>
					</thead>
				<?php if( !empty( $exception_logs ) ){?>
					<tbody>
						<?php foreach( $exception_logs as $row ){ ?>
						<tr>
							<td width="20%"><?php echo $row->log_id; ?></td>
							<td width="50%"><?php echo $row->exception_note; ?></td>
							<td width="20%"><?php echo $row->date_created; ?></td>
							<td width="20%"><?php echo $row->created_by_full_name; ?></td>
						</tr>
						<?php } ?>
					</tbody>
				<?php } else { ?>
					<tr>
						<td colspan="4"><?php echo $this->config->item( 'no_records' ); ?></td>
					</tr>
				<?php } ?>
				</table>
		</div>
	</div>
	<?php } ?>
</div>

<script>
$( document ).ready( function(){
	$( "#create_exception_note_btn" ).on( "click", function( e ){
		e.preventDefault();
		
		if( $( "*[name=previous_action_status_id]" ).val() != $( "*[name=current_action_status_id]" ).val() && ( $( "*[name=exception_note]" ).val() == " " || $( "*[name=exception_note]" ).val() == "" ) ){
			alert( "Please, provide the note" );
			return false;
		}
		
		var formData = $(this).closest('form').serialize();

		swal({
			title: 'Confirm exception log creation?',
			// type: 'question',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url( 'webapp/audit/create_exception_log/'.$exception_details->id ); ?>",
						method: "POST",
						data: formData,
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
								}, 3000);
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
	})
});
</script>