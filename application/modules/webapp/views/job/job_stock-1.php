<style>
	i.child {
		display:table-cell;
		vertical-align:middle;
		text-align:center;
	}
</style>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Consumed Items <?php if( !empty( $job_details->consumed_items ) ) { ?><span class="pull-right pointer download-stock-boms"><a href="<?php echo base_url('webapp/job/profile/'.$job_details->job_id.'/stock?action=download' ); ?>" target="_blank"><i class="far fa-file-pdf text-red" title="Click to download this as a PDF" ></i></a></span> <?php } ?></legend>
			<div class="consumed_items">
				<?php $grand_total = $stock_total = $boms_total = 0.00; if( !empty( $job_details->consumed_items ) ) { ?>
					
					<table style="width:100%">
						<tr class="stock-header pointer item-container-bar">
							<td colspan="5" width="15%"><h5><strong>STOCK <i class="caret-icon fas fa-caret-down text-yellow"></i></h5></td>
							<td><span class="pull-right"><span class="pull-right"><strong><span id="stock_total" >0.00</span></strong></span></strong></span></td>
						</tr>
						<tr class="stock-table" style="display:none">
							<td colspan="6" width="15%">
								<div style="width:100%; height:220px; overflow:auto">
									<?php if( !empty( $job_details->consumed_items->stock ) ) { ?>
										<table class="table" style="width:100%; overflow:scroll">
											<tr>
												<th width="15%">Item Code</th>
												<th width="20%">Item Name</th>
												<th width="15%">Item Type</th>
												<th width="15%"><span class="pull-right">Unit Price</span></th>
												<th width="15%"><span class="pull-right">Qty</span></th>
												<th width="15%"><span class="pull-right">Total Price</span></th>
											</tr>
											<?php foreach( $job_details->consumed_items->stock as $stock_item ) { ?>
												<tr>
													<td><?php echo $stock_item->item_code; ?></td>
													<td><?php echo $stock_item->item_name; ?></td>
													<td><?php echo $stock_item->item_type; ?></td>
													<td><span class="pull-right"> &pound;<?php echo number_format( $stock_item->price, 2 );  ?></span></td>
													<td><span class="pull-right"> <?php echo $stock_item->item_qty; ?></span></td>
													<td><span class="pull-right"> &pound;<?php echo number_format( ( $stock_item->price*$stock_item->item_qty ), 2 ); ?></span></td>
												</tr>
											<?php $stock_total += ( $stock_item->price*$stock_item->item_qty ); } ?>
											<tr style="border-top: 1px solid #e5e5e5">
												<th colspan="5">&nbsp;</th>
												<th class="pull-right" id="stock_total_tbl" >&pound;<?php echo number_format( ( $stock_total ), 2 ); ?></th>
											</tr>
										</table>
									<?php } ?>
								</div>
							</td>
						</tr>
						<tr class="bom-header pointer item-container-bar">
							<td colspan="5"><h5><strong>BOM/SORs <i class="caret-icon fas fa-caret-down text-yellow"></i></h5></td>
							<td><span class="pull-right"><span class="pull-right"><strong><span id="bom_total" >&pound;0.00</span></strong></span></strong></span></td>
						</tr>
						<tr class="bom-table" style="display:none">
							<td colspan="6" width="15%">
								<div style="width:100%; height:220px; overflow:auto">
									<?php if( !empty( $job_details->consumed_items->boms ) ) { ?>
										<table class="table" style="width:100%">
											<tr>
												<th width="15%">Item Code</th>
												<th width="20%">Item Name</th>
												<th width="15%">Item Type</th>
												<th width="15%"><span class="pull-right">Unit Price</span></th>
												<th width="15%"><span class="pull-right">Qty</span></th>
												<th width="15%"><span class="pull-right">Total Price</span></th>
											</tr>
											<?php foreach( $job_details->consumed_items->boms as $bom_item ) { ?>
												<tr>
													<td><?php echo $bom_item->item_code; ?></td>
													<td><?php echo $bom_item->item_name; ?></td>
													<td><?php echo $bom_item->item_type; ?></td>
													<td><span class="pull-right"> &pound;<?php echo number_format( $bom_item->price, 2 );  ?></span></td>
													<td><span class="pull-right"> <?php echo $bom_item->item_qty; ?></span></td>
													<td><span class="pull-right"> &pound;<?php echo number_format( ( $bom_item->price*$bom_item->item_qty ), 2 ); ?></span></td>
												</tr>
											<?php $boms_total += ( $bom_item->price*$bom_item->item_qty ); } ?>
											<tr style="border-top: 1px solid #e5e5e5">
												<th colspan="5">&nbsp;</th>
												<th class="pull-right" id="bom_total_tbl" >&pound;<?php echo ( !empty( $boms_total ) ) ? number_format( ( $boms_total ), 2 ) : 0.00; ?></th>
											</tr>
										</table>
									<?php } ?>
								</div>
							</td>
						</tr>
						
						<tr class="bom-header pointer">
							<td colspan="5"><h5><strong>GRAND TOTAL</h5></td>
							<th class="pull-right">&pound;<?php echo number_format( ( $stock_total + $boms_total ), 2 ); ?></th>
						</tr>
					</table>
				<?php } else { ?>
					<div>There's currently no consumed items on this Job</div>
				<?php } ?>
			</div>
		</div>
	</div>
	
	<div class="col-md-6 col-sm-6 col-xs-12 hide">
		<div class="x_panel tile has-shadow">
			<legend>Consumed Stock</legend>
			<div class="required_items">
				<?php if( !empty( $job_details->required_items ) ) { ?>
				<table class="table table-responsive" style="width:100%">
					<tr>
						<th width="20%">Item Code</th>
						<th width="53%">Item Name</th>
						<th width="12%" class="text-center">Quantity</th>
						<th width="15%"><span class="pull-right">Action</span></th>
					</tr>
					<?php if( !empty( $job_details->required_items ) ){ foreach( $job_details->required_items as $req_item ) { ?>
						<tr>
							<td><?php echo $req_item->item_code; ?></td>
							<td><?php echo $req_item->item_name; ?></td>
							<td class="text-center" data-record_id="<?php echo $req_item->id; ?>" >
								<span class="form-group"><input type="number" value="<?php echo $req_item->item_qty; ?>" id="quntity<?php echo $req_item->id ?>" title="Change this number to update Quantity instantly" data-prev_qty="<?php echo $req_item->item_qty; ?>" class="form-control change-qty"  /></span>
								<small class ="tiny-fdbck-msg fdback-msg<?php echo $req_item->id ?>"></small>
							</td>
							<td class="text-center" data-record_id="<?php echo $req_item->id; ?>" ><span class="pull-right"><span class="edit-item-qty pointer hide" title="Click to edit this Qty for this item"><i class="fas fa-pencil-alt text-blue"></i></span> &nbsp;  &nbsp;  &nbsp; <span class="delete-stock-item pointer" title="Click to delete this item from the list" ><i class="far fa-trash-alt text-red"></i></span></span></td>
						</tr>
					<?php } } ?>
				</table>
			<?php }else{ ?>
				<?php echo $this->config->item('no_records'); ?>
			<?php } ?>
			</div>
		</div>		
	</div>
	
	
	<!-- Modal for adding Stock Items -->
	<div class="modal fade add-required-stock-modal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title" id="myAddStockItemModalLabel">Add Required Stock</h4>						
				</div>
				<div class="modal-body" id="stock-items-modal-container" >
					<div class="col-md-12 col-sm-12 col-xs-12 form-group top_search right">
						<!-- Search bar -->
						<div class="input-group" style="width: 100%;">
							<input type="text" id="stock_search" class="form-control <?php echo $module_identier; ?>-search_input" id="search_term" value="" placeholder="Search stock items">
						</div>
						<div id="no-stock-warning" class="text-red" style="display:none;">You do not currently have searchable Stock items on the system!</div>
					</div>
					<div id="stock-items-container" style="display:none" >
						<input type="hidden" name="page" value="details" />
						<input type="hidden" name="job_id" value="<?php echo $job_details->job_id; ?>" />
						<input type="hidden" name="add_type" value="consumed_items" />
						<div class="row">
							<div class="col-md-8 col-sm-8 col-xs-8 form-group"><strong>Item Name</strong></div>
							<div class="col-md-2 col-sm-2 col-xs-2 form-group"><strong>Qty</strong></div>
							<div class="col-md-2 col-sm-2 col-xs-2 form-group text-center"><strong>Remove</strong></div>
							<hr/>
						</div>
						<div id="append-items"></div>						
					</div>
				</div>
				
				<div class="modal-footer">
					<button id="add-stock-items-btn" class="btn btn-success btn-sm">Add Items</button>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$( document ).ready(function(){

	$( '.item-container-bar' ).click( function(){
		$( this ).closest( 'i' ).find( '.caret-icon' ).toggleClass('fa-caret-up fa-caret-down');			
	});
	
	if( $( '#stock_total_tbl' ).text().length > 0 ){
		$( '#stock_total' ).text( $( '#stock_total_tbl' ).text() );
	}
	
	if( $( '#bom_total_tbl' ).text().length > 0 ){
		$( '#bom_total' ).text( $( '#bom_total_tbl' ).text() );
	}
	
	$( '.stock-header' ).click( function(){
		$( '.stock-table' ).slideToggle( 'slow' );
	} );
	
	$( '.bom-header' ).click( function(){
		$( '.bom-table' ).slideToggle( 'slow' );
	} );
		
	$( '.download-stock-boms' ).click( function(){
		$( '#download_consumed_items' ).submit();
	});
		
		var jobId = "<?php echo $job_details->job_id; ?>";
		
		//Update Item Qty
		$( '.change-qty' ).change( function(){
			$( '.tiny-fdbck-msg' ).text( '' )
			var prevQty  = $( this ).data( 'prev_qty' );
			var recordId = $( this ).closest( 'td' ).data( 'record_id' ),
				qty		 = $( this ).val();
				if( qty < 1 ){
					swal({
						type: 'warning',
						title: 'Quantity must be atleast 1 unit',
						text: 'If you want to remove the item entirely, please use the delete feature'
					});
					$( '#quntity' + recordId ).val( prevQty );
					return false;
				}

			//Fire update Qty api
			$.ajax({
				url:"<?php echo base_url('webapp/job/update_required_items/' ); ?>",
				method:"post",
				data:{job_id: jobId, id: recordId },
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){
						$( '.fdback-msg'+recordId ).html( '<span class="text-green">Qty updated</span>' ).delay(3000).fadeOut();
					}else{
						swal({
							type: 'error',
							title: data.status_msg
						})
					}		
				}
			});
			
		});
		
		$('#stock_search').blur( function(){
			$( '#no-stock-warning' ).hide();
		});
		
		$( '#add-stock-items-btn' ).click( function(){
			var formData = $( "#stock-items-container :input").serialize();
			$.ajax({
				url:"<?php echo base_url('webapp/job/add_job_items/' ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){
						
						$( '.add-required-stock-modal' ).modal( 'hide' );
						$( '.modal-backdrop' ).remove();
						
						swal({
							type: 'success',
							html: data.status_msg,
							showConfirmButton: false,
							timer: 3000
						})
						
						window.setTimeout(function(){ 
							location.reload();
						} ,1000);
					} else {
						swal({
							type: 'error',
							title: data.status_msg
						})
					}		
				}
			});
			return false;
		});
		
		var i   	  = 1,
			maxFields = 10;
		
		var stockItems = <?php echo $stock_items; ?>;
		
		if( stockItems.length > 0 ){
			
			var jobId = <?php echo $job_details->job_id; ?>;
			
			$( '#no-stock-warning' ).hide();
			$('#stock_search').each( function( i, e ) {
				var dataList = $(e);
				dataList.autocomplete({
					//source: stockItems,
					source: function(request, response){
						var matcher = new RegExp( $.ui.autocomplete.escapeRegex( request.term ), "i" );
						response( $.grep( stockItems, function( value ) {
							return matcher.test( value.label ) || matcher.test( value.value ) || matcher.test( value.item_category ) || matcher.test( value.item_name );
						}));
					},
					select: function( event , data ) {
						$( '#stock-items-container' ).show();
						var itemCode 	  = escapeHtml( data.item.value ),  
							itemClassName = escapeHtml( data.item.value ),
							selectedItem  = escapeHtml( data.item.label );
							buyPrice  	  = escapeHtml( data.item.buy_price );
							sellPrice  	  = escapeHtml( data.item.sell_price );
							
						if( i < maxFields ){							
							var numItems = parseInt( $('div .'+itemClassName).length );
							if( numItems > 0 ){
								swal({
									text: 'This Stock Item is already selected! Please update Quantity.'
								})
								return false;
							}else{
								$( this ).val(''); 
								i++;
								var appendItem = '<div class="row new-item '+itemClassName+'" id="'+itemClassName+'">';
										appendItem += '<div class="col-md-8 col-sm-8 col-xs-8 form-group">';
											appendItem += '<input type="hidden" name="consumed_items['+itemCode+'][job_id]" value="'+jobId+'" />';
											appendItem += '<input type="hidden" name="consumed_items['+itemCode+'][item_code]" value="'+itemCode+'" />';
											appendItem += '<input type="hidden" name="consumed_items['+itemCode+'][price]" value="'+buyPrice+'" />';
											appendItem += '<input type="hidden" name="consumed_items['+itemCode+'][price_adjusted]" value="'+buyPrice+'" />';
											appendItem += '<input type="text"   value="'+selectedItem+'" readonly class="form-control" />';
										appendItem += '</div>';
										appendItem += '<div class="col-md-2 col-sm-2 col-xs-2 form-group">';
											appendItem += '<input type="number" name="consumed_items['+itemCode+'][item_qty]" value="1" min="1" class="form-control" title="You can reduce existing quantities by setting this to a minus (-) value" />';
										appendItem += '</div>';
										appendItem += '<div class="col-md-2 col-sm-2 col-xs-2 text-center pointer removeme" data-content_wrapper="'+itemClassName+'" title="Click to remove this item">';
											appendItem += '<i class="fas fa-times fa-2x text-red"></i>';
										appendItem += '</div>';
									appendItem += '</div>';
								
								$( '#append-items' ).prepend( appendItem );
							}						
						}
						return false;
					}
				});
			});
		} else {
			$('#stock_search').focus( function(){
				$( '#no-stock-warning' ).show( );
			});
		}

		//Remove item from list
		$( '#append-items' ).on( 'click', '.removeme',function(){
			var classId = $( this ).data( 'content_wrapper' );
			$( '#'+classId ).remove();			
			var numItems = parseInt( $( 'div .new-item' ).length );
			if( numItems == 0 ){
				$( '#stock-items-container' ).hide();
			}
		});
		
		//Ecape HTML special chars
		function escapeHtml( text ) {
			var map = {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#039;'
			};

			return text.replace(/[&<>"']/g, function(m) { return map[m]; });
		}
		
		$( '.add-required-stock' ).click( function(){
			$( ".add-required-stock-modal" ).modal("show");
			$( "#stock_search" ).autocomplete( "option", "appendTo", "#stock-items-modal-container" );
		} );
		
	});
</script>