<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="profile-details-container">
				<div class="row">
					<div class="row">
						<div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12">
							<div class="x_panel tile has-shadow">
								<form id="create-stock-form" class="form-horizontal">
									<legend>Stock Details</legend>
									<input type="hidden" name="page" value="details" />
									
									<div class="input-group form-group">
										<label class="input-group-addon">Item Code</label>
										<input id="item_code" name="item_code" class="form-control" type="text" placeholder="Item Code" value="" />
									</div>
									
									<div class="input-group form-group">
										<label class="input-group-addon">Item Name&nbsp;*</label>
										<input name="item_name" class="form-control required" type="text" placeholder="Item Name" value="" required />
									</div>
									
									<div class="input-group form-group">
										<label class="input-group-addon">Item Type</label>
										<select name="item_type" class="form-control">
											<option>Please select</option>
											<option value="Labour">Labour</option>
											<option value="Materials">Materials</option>
										</select>
									</div>
									
									<div class="input-group form-group">
										<label class="input-group-addon">Item Category</label>
										<input name="item_category" class="form-control" type="text" placeholder="Item Category" value="" />
									</div>

									<div class="input-group form-group">
										<label class="input-group-addon">Buy Price</label>
										<input name="buy_price" class="form-control numbers-only" type="text" placeholder="Buy Price" value="" />
									</div>
									
									<div class="input-group form-group">
										<label class="input-group-addon">Sell Price</label>
										<input name="sell_price" class="form-control numbers-only" type="text" placeholder="Sell price" value="" />
									</div>

									<div class="input-group form-group">
										<label class="input-group-addon">Item Supplier</label>
										<input name="item_supplier" class="form-control" type="text" placeholder="Item Supplier" value="" />
									</div>
									
									<div class="input-group form-group hide">
										<label class="input-group-addon">Item Quantity</label>
										<input name="item_qty" class="form-control" type="number" min="1" placeholder="Item Quantity" value="" />
									</div>

									<div class="input-group form-group hide">
										<label class="input-group-addon">Item Status</label>
										<select name="item_status" class="form-control">
											<option>Please select</option>
											<option value="Active" selected="selected">Active</option>
										</select>
									</div>

									<div class="row">
										<div class="col-md-6">
											<button class="btn btn-sm btn-block btn-flow btn-success btn-next" type="submit">Create Stock Item</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$( "form#create-stock-form" ).submit( function( e ){
	e.preventDefault();
	
	var formData = $( this ).serialize();

	swal({
		title: 'Confirm Stock Item creation?',
		showCancelButton: true,
		confirmButtonColor: '#5CB85C',
		cancelButtonColor: '#9D1919',
		confirmButtonText: 'Yes'
	}).then( function( result ){
		if( result.value ) {
			$.ajax({
				url:"<?php echo base_url('webapp/job/create_stock_item/'); ?>",
				method: "POST",
				data: formData,
				dataType: 'json',
				success:function( data ){
					if( ( data.status == 1 ) && ( data.stock_item.item_id !== '' ) ){
						var newStockID = data.stock_item.item_id;
						swal({
							type: 'success',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 2000
						})
						window.setTimeout( function(){
							location.href = "<?php echo base_url('webapp/job/stock/') ?>" + newStockID;
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
</script>
