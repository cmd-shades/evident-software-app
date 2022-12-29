<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="profile-details-container">
				<div class="row alert alert-ssid bg-blue" role="alert">
					<div class="profile-overview">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<legend>BOM Profile <span class="pull-right"><span class="edit-bom pointer hide" title="Click to edit this BOM profile"><i class="fas fa-pencil-alt"></i></span> &nbsp; <span class="delete-bom-btn pointer" title="Click to delete this BOM profile" data-item_id="<?php echo (!empty($bom_details->item_id)) ? $bom_details->item_id : '' ; ?>"><i class="far fa-trash-alt"></i></span></span></legend>
							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Date Created</label></td>
											<td width="85%"><?php echo (valid_date($bom_details->date_created)) ? date('d-m-Y H:i:s', strtotime($bom_details->date_created)) : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Created By</label></td>
											<td width="85%"><?php echo (!empty($bom_details->record_created_by)) ? ucwords($bom_details->record_created_by) : 'Data not available'; ?></td>
										</tr>
									</table>
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr class="hide">
											<td width="15%"><label><strong>(#) Associated Job Types</strong></label></td>
											<td width="85%"><?php echo !empty($associated_job_types) ? count($associated_job_types, 1) : 0; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Status</label></td>
											<td width="85%"><?php echo ($bom_details->is_active == 1) ? 'Active <i class="far fa-check-circle"></i>' : 'Disabled <i class="far fa-times-circle text-red"></i>'; ?></td>
										</tr>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<div class="x_panel tile has-shadow">
								<form id="update-bom-form" class="form-horizontal">
									<input type="hidden" name="page" value="details" />
									<input type="hidden" name="item_id" value="<?php echo $bom_details->item_id; ?>" />
									<legend>BOM Details</legend>
									<div class="input-group form-group">
										<label class="input-group-addon">Item Category</label>
										<!-- <input name="item_category" class="form-control" type="text" placeholder="Item Category" value="<?php echo (!empty($bom_details->item_category)) ? html_escape(ucwords($bom_details->item_category)) : '' ; ?>" /> -->
										<select id="bom_category_id" name="bom_category_id" class="form-control" data-label_text="BOM Category" >
											<option value="" >Please Select</option>
											<?php if (!empty($bom_categories)) {
											    foreach ($bom_categories as $key => $bom_cat) { ?>
												<option value="<?php echo $bom_cat->bom_category_id; ?>" <?php echo ($bom_details->bom_category_id == $bom_cat->bom_category_id) ? "selected=selected" : "" ?> ><?php echo $bom_cat->bom_category_name; ?></option>
											<?php }
											    } ?>
										</select>
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Item Type</label>
										<input name="item_type" class="form-control" type="text" placeholder="Item Type" value="<?php echo (!empty($bom_details->item_type)) ? html_escape(ucwords($bom_details->item_type)) : '' ; ?>" />
									</div>

									<div class="input-group form-group">
										<label class="input-group-addon">Item Name</label>
										<input name="item_name" class="form-control" type="text" placeholder="Item Name" value="<?php echo (!empty($bom_details->item_name)) ? html_escape($bom_details->item_name) : '' ; ?>" />
									</div>

									<div class="input-group form-group">
										<label class="input-group-addon">Item Code</label>
										<input id="item_code" name="item_code" class="form-control" type="text" placeholder="Item Code" value="<?php echo (!empty($bom_details->item_code)) ? html_escape($bom_details->item_code) : '' ; ?>" />
									</div>

									<?php /*
                                    <div class="input-group form-group">
                                        <label class="input-group-addon">Item Quantity</label>
                                        <input id="item_code" name="item_qty" class="form-control" type="text" placeholder="Item Quantity" value="<?php echo (!empty( $bom_details->item_qty ) ) ? html_escape( $bom_details->item_qty ) : '' ; ?>" />
                                    </div> */ ?>

									<div class="input-group form-group">
										<label class="input-group-addon">Item Revenue</label>
										<input name="item_revenue" class="form-control" type="text" placeholder="Item Revenue" value="<?php echo (!empty($bom_details->item_revenue)) ? html_escape($bom_details->item_revenue) : '' ; ?>" />
									</div>

									<div class="input-group form-group">
										<label class="input-group-addon">Item Cost</label>
										<input name="item_cost" class="form-control" type="text" placeholder="Item Cost" value="<?php echo (!empty($bom_details->item_cost)) ? html_escape($bom_details->item_cost) : '' ; ?>" />
									</div>

									<?php /*
                                    <div class="input-group form-group">
                                        <label class="input-group-addon">Estimated Slots</label>
                                        <input name="estimated_slots" class="form-control" type="text" placeholder="Estimated Slots" value="<?php echo (!empty( $bom_details->estimated_slots ) ) ? html_escape( $bom_details->estimated_slots ) : '' ; ?>" />
                                    </div> */ ?>

									<div class="row">
										<div class="col-md-6">
											<button id="update-bom-btn" class="btn btn-sm btn-block btn-flow btn-success btn-next update-bom-btn" type="submit">Save Changes</button>
										</div>
										<div class="col-md-6">
											<button class="delete-bom-btn btn btn-sm btn-block btn-flow btn-danger has-shadow" type="button" data-item_id="<?php echo (!empty($bom_details->item_id)) ? $bom_details->item_id : '' ; ?>">Delete</button>
										</div>
									</div>
								</form>
							</div>
						</div>

						<div class="col-md-6 col-sm-6 col-xs-12 hide">
							<div class="x_panel tile has-shadow">
								<legend>Associated Job Types (<?php echo !empty($associated_job_types) ? count($associated_job_types, 1) : 0; ?>)</legend>
								<?php if (!empty($associated_job_types)) { ?>
									<div class="row">
										<?php foreach ($associated_job_types as $job_type) { ?>
											<div class="col-md-3 col-sm-3 col-xs-12">
												<ul class="to_do">
													<li><p><a href="<?php echo base_url('webapp/job/job_types/'.$job_type->job_type_id); ?>" ><?php echo $job_type->job_type; ?></a> <span class="pull-right"><span class="remove-bom pointer" data-job_type_id="<?php echo $job_type->job_type_id; ?>" data-risk_id="<?php echo $job_type->risk_id; ?>" title="Click to remove this BOM from this Job type" ><i class="far fa-trash-alt text-red"></i></span></span></p></li>
												</ul>
											</div>
										<?php } ?>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">
$( "form#update-bom-form" ).submit( function( e ){
	e.preventDefault();
	
	var formData = $( this ).serialize();

	swal({
		title: 'Confirm BOM Item update?',
		showCancelButton: true,
		confirmButtonColor: '#5CB85C',
		cancelButtonColor: '#9D1919',
		confirmButtonText: 'Yes'
	}).then( function (result) {
		if( result.value ) {
			$.ajax({
				url:"<?php echo base_url('webapp/job/update_bom_item/'); ?>",
				method: "POST",
				data: formData,
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 ){
						swal({
							type: 'success',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 2000
						})
						window.setTimeout( function(){
							location.reload();
						}, 1000);
					} else {
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


//Delete BOM Item from
$( '.delete-bom-btn' ).click( function( e ){
	e.preventDefault();
	var itemId = $( this ).data( 'item_id' );
	swal({
		title: 'Confirm delete BOM Item?',
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#5CB85C',
		cancelButtonColor: '#9D1919',
		confirmButtonText: 'Yes'
	}).then( function (result) {
		if ( result.value ) {
			$.ajax({
				url: "<?php echo base_url('webapp/job/delete_bom_item/'); ?>",
				method: "POST",
				data:{ 'page':'details', item_id:itemId },
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 ){
						swal({
							type: 'success',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 1500
						})
						window.setTimeout( function(){
							window.location.href = "<?php echo base_url('webapp/job/boms'); ?>";
						}, 1500 );
					} else {
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
</script>