<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="profile-details-container">
				<div class="row alert alert-ssid bg-blue" role="alert">
					<div class="profile-overview">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<legend>Stock Item Profile <span class="pull-right hide"><span class="edit-stock-item pointer hide" title="Click to edit this Item profile"><i class="fas fa-pencil-alt"></i></span> &nbsp; <span class="---delete-stock-item pointer" title="Click to delete this Item profile" ><i class="far fa-trash-alt"></i></span></span></legend>
							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Date Created</label></td>
											<td width="85%"><?php echo (valid_date($stock_item_details->date_created)) ? date('d-m-Y H:i:s', strtotime($stock_item_details->date_created)) : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Created By</label></td>
											<td width="85%"><?php echo (!empty($stock_item_details->record_created_by)) ? ucwords($stock_item_details->record_created_by) : 'Data not available'; ?></td>
										</tr>
									</table>
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label><strong>(#) Associated Job Types</strong></label></td>
											<td width="85%"><?php echo !empty($associated_job_types) ? count($associated_job_types, 1) : 0; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Status</label></td>
											<td width="85%"><?php echo ($stock_item_details->is_active == 1) ? 'Active <i class="far fa-check-circle"></i>' : 'Disabled <i class="far fa-times-circle text-red"></i>'; ?></td>
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
								<form id="update-job-form" class="form-horizontal">
									<input type="hidden" name="page" value="details" />
									<input type="hidden" name="item_id" value="<?php echo $stock_item_details->item_id; ?>" />
									<legend>Stock Item Details</legend>
									<div class="input-group form-group">
										<label class="input-group-addon">Item Code</label>
										<input id="item_code" name="item_code" class="form-control" type="text" placeholder="Stock Item Code" readonly value="<?php echo $stock_item_details->item_code; ?>" />
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Item Name</label>
										<input id="item_name" name="item_name" class="form-control" type="text" placeholder="Stock Item Name" value="<?php echo $stock_item_details->item_name; ?>" />
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Item Type</label>
										<select name="item_type" class="form-control">
											<option>Please select</option>
											<option value="Labour" <?php echo (strtolower($stock_item_details->item_type) == 'labour') ? 'selected=selected' : ''; ?> >Labour</option>
											<option value="Materials" <?php echo (strtolower($stock_item_details->item_type) == 'materials') ? 'selected=selected' : ''; ?> >Materials</option>
										</select>
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Item Category</label>
										<input id="item_category" name="item_category" class="form-control" type="text" placeholder="Item Category" value="<?php echo $stock_item_details->item_category; ?>" />
									</div>
									<?php if ($this->user->is_admin || !empty($permissions->can_delete) || !empty($permissions->is_admin)) { ?>
										<div class="input-group form-group">
											<label class="input-group-addon">Buy Price</label>
											<input id="buy_price" name="buy_price" class="form-control" type="text" placeholder="Item Buy Price" placeholder="E.g. £9.99" value="<?php echo number_format($stock_item_details->buy_price, 2); ?>" />
										</div>
										<div class="input-group form-group">
											<label class="input-group-addon">Sell Price</label>
											<input id="sell_price" name="sell_price" class="form-control" type="text" placeholder="Item Sell Price" value="<?php echo number_format($stock_item_details->sell_price, 2); ?>" />
										</div>
										<div class="input-group form-group">
											<label class="input-group-addon">Item Supplier</label>
											<input id="item_supplier" name="item_supplier" class="form-control" type="text" placeholder="Item Supplier" value="<?php echo $stock_item_details->item_supplier; ?>" />
										</div>
									<?php } ?>
									<div class="row" >
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button type="button" class="update-stock-item-btn btn btn-sm btn-success" >Update Stock Item</button>
											<?php if ($this->user->is_admin || !empty($permissions->can_delete) || !empty($permissions->is_admin)) { ?>
												<button class="btn btn-sm btn-danger has-shadow delete-stock-item-btn" type="button" data-item_id="<?php echo $stock_item_details->item_id; ?>">Delete Stock Item</button>
											<?php } ?>
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
													<li><p><a href="<?php echo base_url('webapp/job/job_types/'.$job_type->job_type_id); ?>" ><?php echo $job_type->job_type; ?></a> <span class="pull-right"><span class="remove-stock-item pointer" data-job_type_id="<?php echo $job_type->job_type_id; ?>" data-item_id="<?php echo $job_type->item_id; ?>" title="Click to remove this Stock Item from this Job type" ><i class="far fa-trash-alt text-red"></i></span></span></p></li>
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

<script>
	$( document ).ready( function(){

		$( '.update-stock-item-btn' ).click( function( event ){

			var formID = $( this ).closest( 'form' ).attr( 'id' );
			event.preventDefault();
			var formData = $('#'+formID ).serialize();
			swal({
				title: 'Confirm Stock Item update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/update_stock_item/'.$stock_item_details->item_id); ?>",
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
			}).catch(swal.noop)
		});


		//Delete Stock Item from
		$('.delete-stock-item-btn').click(function(){

			var itemId = $(this).data( 'item_id' );
			swal({
				title: 'Confirm delete Stock Item?',
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/delete_stock_item/'.$stock_item_details->item_id); ?>",
						method:"POST",
						data:{'page':'details', item_id:itemId},
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 1500
								})
								window.setTimeout(function(){
									window.location.href = "<?php echo base_url('webapp/job/stock'); ?>";
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
			}).catch( swal.noop )
		});
	});
</script>