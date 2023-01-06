<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Personal Cost Tracking <span class="pull-right"><span class="add-cost-item pointer" title="Click to add a new Cost item for this person"><i class="fas fa-plus-circle text-green"></i></span></legend>
			<?php if( !empty( $cost_tracking ) ){ ?>
				<table style="width:100%">
					<tr>
						<th width="25%">Item Name</th>
						<th width="10%" class="text-center">Qty</th>
						<th width="12%"><span class="pull-right">Unit Price</span></th>
						<th width="13%"><span class="pull-right">Total Value</span></th>
						<th width="15%" title="Date when the transaction cost was incurred. This is different to the date it was submitted on the system"><span class="pull-right">Trans. Date</span></th>
						<th width="15%" title="Date when the transaction was submitted on the system i.e. this is system generated"><span class="pull-right">Date Created</span></th>
						<th width="10%"><span class="pull-right">Action</span></th>
					</tr>
					<?php if( !empty( $cost_tracking ) ){ foreach( $cost_tracking as $cost_item ) { ?>
						<tr>
							<td><?php echo $cost_item->cost_item_name; ?></td>
							<td class="text-center" ><?php echo $cost_item->cost_item_qty; ?></td>
							<td><span class="pull-right">&pound;<?php echo number_format( $cost_item->cost_item_value, 2 ); ?></span></td>
							<td><span class="pull-right">&pound;<?php echo number_format( ( $cost_item->cost_item_value * $cost_item->cost_item_qty ), 2 ); ?></span></td>
							<td><span class="pull-right"><?php echo ( valid_date( $cost_item->transaction_date ) ) ? date( 'd-m-Y H:i:s', strtotime( $cost_item->transaction_date ) ) : ''; ?></span></td>
							<td><span class="pull-right"><?php echo ( valid_date( $cost_item->date_created ) ) ? date( 'd-m-Y H:i:s', strtotime( $cost_item->date_created ) ) : ''; ?></span></td>
							<td><span class="pull-right"><span class="edit-cost-item pointer hide" data-cost_item_id="<?php echo $cost_item->cost_item_id; ?>" title="Click to Edit this record"><i class="far fa-edit"></i> </span> &nbsp; &nbsp; <span class="delete-cost-item pointer text-red" title="Click to Delete this record" data-cost_item_id="<?php echo $cost_item->cost_item_id; ?>"><i class="far fa-trash-alt"></i> Delete</span></span></td>
						</tr>
					<?php } } ?>
				</table>
				
			<?php }else{ ?>
				<?php echo $this->config->item('no_records'); ?>
			<?php } ?>
			
			<!-- Add Cost item Modal -->
			<div id="add-cost-item-modal" class="modal fade add-cost-item-modal" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-md">
					<form id="add-cost-item-form" >
						<input type="hidden" name="page" value="details" />
						<input type="hidden" name="person_id" value="<?php echo $person_details->person_id; ?>" />
						<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
						<div class="modal-content">
							<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
								<h4 class="modal-title" id="myAddCostModalLabel">Add Cost Item to Asset</h4>
								<small id="feedback-message"></small>
							</div>
							<div class="modal-body">
								<div class="input-group form-group">
									<label class="input-group-addon">Item Name</label>
									<input id="cost_item_name" name="cost_item_name" class="form-control" type="text" placeholder="Cost Item name" value="" />
								</div>
								<div class="input-group form-group">
									<label class="input-group-addon">Item Type</label>
									<select id="cost_item_type" name="cost_item_type" class="form-control required">
										<option value="">Please select type</option>
										<?php if( !empty( $cost_item_types ) ) { foreach( array_to_object( $cost_item_types ) as $k => $item_type ) { ?>
											<option value="<?php echo $item_type->cost_item_type_alt; ?>" ><?php echo $item_type->cost_item_type_alt; ?></option>
										<?php } } ?>
									</select>
								</div>
								<div class="input-group form-group">
									<label class="input-group-addon">Item Qty</label>
									<input id="cost_item_qty" type="number" min="1" name="cost_item_qty" class="form-control" placeholder="Cost Item qty" value="1" />
								</div>
								<div class="input-group form-group">
									<label class="input-group-addon">Item Value </label>
									<input id="cost_item_value" name="cost_item_value" class="form-control numbers-only" type="text" placeholder="Cost Item value (price per unit)" value="" />
								</div>
								<div class="form-group">
									<label class="input-group push-10-left">Item Description</label>
									<textarea name="cost_item_desc" class="form-control" type="text" value="" style="width:100%; height:78px" ></textarea>
								</div>
							</div>
							
							<div class="modal-footer">
								<button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">&nbsp;&nbsp;&nbsp;&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;</button>
								<button id="add-cost-item-btn" type="button" class="btn btn-sm btn-success">Add Cost Item</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>		
	</div>
</div>

<script>
	$(document).ready(function(){
		
		$( '.add-cost-item' ).click( function(){
			$( '#add-cost-item-modal' ).modal( 'show' );
		});
		
		$( '[name="cost_item_name"]' ).keyup( function(){
			$( this ).css("border","1px solid #ccc");
			$( '#feedback-message' ).show();
			$( '#feedback-message' ).text( '' );
		} );
		
		$( '[name="cost_item_type"]' ).change( function(){
			$( this ).css("border","1px solid #ccc");
			$( '#feedback-message' ).show();
			$( '#feedback-message' ).text( '' );
		} );
		
		// Section Quick add
		$( '#add-cost-item-btn' ).click(function(){
			
			var costItemName = $( '[name="cost_item_name"]' ).val();
			var costItemType = $( '[name="cost_item_type"] option:selected' ).val();

			if( costItemName.length == 0 ){
				$( '[name="cost_item_name"]' ).focus().css("border","1px solid red");
				$( '#feedback-message' ).html( '<span class="text-red">Cost item name is required</span>' );
				return false;
			}
			
			if( costItemType.length == 0 ){
				$( '[name="cost_item_type"]' ).focus().css("border","1px solid red");
				$( '#feedback-message' ).html( '<span class="text-red">Cost item type is required</span>' );
				return false;
			}
			
			var formData = $( "#add-cost-item-form :input").serialize();
		
			$.ajax({
				url:"<?php echo base_url('webapp/person/add_cost_item/' ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 ){
						
						$( '#add-cost-item-modal' ).modal( 'hide' );
						location.reload();
						
					}else{
						swal({
							type: 'error',
							title: data.status_msg
						})
					}		
				}
			});
			return false;
		});

	});
</script>