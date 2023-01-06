<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="profile-details-container">
				<div class="row">
					<div class="row">
						<div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12">
							<div class="x_panel tile has-shadow">
								<form id="create-bom-form" class="form-horizontal">
									<input type="hidden" name="page" value="details" />
									<legend>BOM Details</legend>
									<div class="input-group form-group">
										<label class="input-group-addon">Item Category</label>
										<select id="bom_category_id" name="bom_category_id" class="form-control" data-label_text="BOM Category" >
											<option value="" >Please Select</option>
											<?php if( !empty( $bom_categories ) ) { foreach( $bom_categories as $key => $bom_cat ) { ?>
												<option value="<?php echo $bom_cat->bom_category_id; ?>" ><?php echo $bom_cat->bom_category_name; ?> <?php echo !empty( $bom_cat->bom_category_name ) ? ' - '.$bom_cat->bom_category_name : ''; ?></option>
											<?php } } ?>
										</select>
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
										<label class="input-group-addon">Item Name&nbsp;*</label>
										<input name="item_name" class="form-control required" type="text" placeholder="Item Name" value="" required />
									</div>

									<div class="input-group form-group">
										<label class="input-group-addon">Item Code&nbsp;*</label>
										<input id="item_code" name="item_code" class="form-control required" type="text" placeholder="Item Code" value="" required />
									</div>
									<?php if( $this->user->is_admin || !empty( $permissions->can_delete ) || !empty( $permissions->is_admin ) ){ ?>
										<div class="input-group form-group">
											<label class="input-group-addon">Item Revenue &nbsp;*</label>
											<input id="item_revenue" name="item_revenue" class="form-control required numbers-only" type="text" placeholder="Item Revenue" value="" />
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Item Cost&nbsp;*</label>
											<input id="item_cost" name="item_cost" class="form-control required numbers-only" type="text" placeholder="Item Cost" value="" />
										</div>
									<?php } ?>

									<div class="input-group form-group hide">
										<label class="input-group-addon">Item Quantity</label>
										<input name="item_qty" class="form-control" type="number" min="1" placeholder="Item Quantity" value="" />
									</div>

									<div class="input-group form-group hide">
										<label class="input-group-addon">Estimated Slots</label>
										<input name="estimated_slots" class="form-control" type="text" placeholder="Estimated Slots" value="" />
									</div>

									<div class="row">
										<div class="col-md-6">
											<button class="btn btn-sm btn-block btn-flow btn-success btn-next" type="submit">Add BOM Item</button>
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

	$( "form#create-bom-form" ).submit( function( e ){
		e.preventDefault();
		
		var formData = $( this ).serialize();

		swal({
			title: 'Confirm BOM Item creation?',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
			if( result.value ) {
				$.ajax({
					url:"<?php echo base_url( 'webapp/job/create_bom_item/' ); ?>",
					method: "POST",
					data: formData,
					dataType: 'json',
					success:function( data ){
						if( ( data.status == 1 ) && ( data.bom_item.item_id !== '' ) ){
							var newBomID = data.bom_item.item_id;
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 2000
							})
							window.setTimeout( function(){
								location.href = "<?php echo base_url( 'webapp/job/boms/' ) ?>" + newBomID;
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
