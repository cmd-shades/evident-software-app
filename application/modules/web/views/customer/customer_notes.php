<div class="row customer-notes-container">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<div class="rows">
				<a class="btn btn-sm btn-flow btn-success btn-next pull-right no_right_margin" id="addNewNote">Add note &nbsp;<i class="fas fa-chevron-down"></i></a>
				<legend>Notes</legend>
			</div>
			<?php if (!empty($customer_notes)) { ?>
			<div class="control-group form-group full_block">
				<table style="width:100%; table-layout: fixed;">
					<tr>
						<!-- <th width="20%">Customer Name</th> -->
						<th width="16%">Note Date</th>
						<th width="32%">Note</th>
						<th width="11%">Created Date</th>
						<th width="7%">Created By</th>
					</tr>
				</table>
			</div>
			<div class="control-group form-group table_body full_block">
				<table style="width:100%; table-layout: fixed;">
					<?php if (!empty($customer_notes)) {
					    foreach ($customer_notes as $row) { ?>
						<tr>
							<!-- td width="20%"><?php echo ucwords(strtolower($row->business_name.' '.$row->business_name)); ?></td> -->
							<td width="16%"><?php echo (validate_date($row->note_date)) ? format_date_client($row->note_date) : ''; ?></td>
							<td width="32%"><?php echo (!empty($row->customer_note)) ? $row->customer_note : ''; ?></td>
							<td width="11%"><?php echo (validate_date($row->created_date)) ? format_date_client($row->created_date) : ''; ?></td>
							<td width="7%"><?php echo (!empty($row->created_by_full_name)) ? $row->created_by_full_name : ''; ?></td>
						</tr>
					<?php }
					    } ?>
				</table>
			<?php } else { ?>
				<div class="control-group form-group full_block">
					<p><?php echo $this->config->item('no_records'); ?></p>
				</div>
			<?php } ?>
			</div>
		</div>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<form id="createNewNoteForm" novalidate>
			<div class="row create-note" style="display: none;">
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<div class="x_panel tile has-shadow">
						<input type="hidden" name="customer_id" value="<?php echo $customer_details->customer_id; ?>" />

						<legend>Create a Note</legend>

						<div class="input-group form-group">
							<label class="input-group-addon">Date of Note *</label>
							<input type="text" name="note_date" value="" class="form-control datepicker" placeholder="<?php echo date('d/m/Y'); ?>" data-date-format="DD/MM/YY" required />
						</div>

						<div class="input-group form-group full-width">
							<textarea name="customer_note" rows="5" class="form-control" id="" placeholder="Note"></textarea>
						</div>
						
			<?php 		if ($this->user->is_admin || !empty($permissions->can_add) || !empty($permissions->is_admin)) { ?>
							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12 pull-left">
									<button id="createNote" class="btn btn-sm btn-block btn-flow btn-success btn-next" type="submit">Create Note</button>
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
			</div>
		</form>
	</div>
</div>


<script>

$( "#addNewNote" ).on( "click", function(){
	$( ".table_body" ).slideToggle( 1000 );
	$( ".create-note" ).slideToggle( 1000 );
	$( this ).children( '.fas' ).toggleClass( 'fa-chevron-down fa-chevron-up' );
});

$( document ).ready( function(){
	$( "#createNewNoteForm" ).on( "submit", function( e ){
		e.preventDefault();
		var formData = $( this ).serialize();
		
		$.ajax({
			url:"<?php echo base_url('webapp/customer/create_note/'); ?>",
			method: "POST",
			data: formData,
			dataType: 'json',
			success: function( data ){
				if( data.status == 1 ){
					swal({
						type: 'success',
						title: data.status_msg,
						showConfirmButton: false,
						timer: 2000
					})

					window.setTimeout( function(){
						location.reload();
					}, 2000 );
				} else {
					swal({
						type: 'error',
						title: data.status_msg
					})
				}
			}
		});
		
	});
});
</script>