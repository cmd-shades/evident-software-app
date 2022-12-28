<style>
	.accordion .panel-heading {
		background: transparent;
		padding: 13px;
		width: 100%;
		display: block
	}
	.accordion .panel:hover {
		background: transparent
	}
</style>

<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Consumed Stock &amp; <span title="Bill Of Materials" >BOMs</span></legend>
			
			<?php $stock_total = $boms_total = 0.00; ?>
			<div class="accordion" id="accordion1" role="tablist" aria-multiselectable="true">
				<div class="panel" style="border: none;">
					<div class="section-container-bar panel-heading collapsed bg-grey pointer no-radius" role="tab" id="headingOne" data-toggle="collapse" data-parent="#accordion1" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne" style="width: calc( 100% - 60px ); display: block; float: left; margin-bottom: 30px;">
						<h4 class="panel-title"> Stock Items (<?php echo ( !empty( $contract_consumed_items->stock ) ) ? count( $contract_consumed_items->stock ) : 0  ?>) <span class="pull-right"><span class="pull-left"><strong><span id="stock_total" >&pound;0.00</span></strong></span><span class="pull-right"> &nbsp;<i class="caret-icon fas fa-caret-down text-yellow"></i></span></h4>
					</div>
					<div style="width: 40px;float: right;">
						<a data-toggle="modal" data-target="#csv-export-modal" data-item_type="stock" class="export-trigger"><img src="<?php echo base_url( "assets/images/csv-icon.png" ); ?>" style="width: 100%;" alt="Click to download Stock items as a CSV" title="Click to download Stock items as a CSV" /></a>
					</div>

					<div id="collapseOne" class="panel-collapse collapse no-bg no-background" role="tabpanel" aria-labelledby="headingOne" >
						<div class="panel-body">
							<div class="row table-responsive" style="width:100%; height:220px; overflow:auto">
								<table class="table" style="font-size:90%; overflow:hidden">
									<thead>
										<tr>
											<th width="13%">Job ID</th>
											<th width="13%">Item Code</th>
											<th width="25%">Item Name</th>
											<th width="12%">Item Type</th>
											<th width="12%" class="text-center">Qty</th>
											<th width="13%" class="pull-right">Unit Price</th>
											<th width="12%"><span class="pull-right">Total</span></th>
										</tr>
									</thead>
									<tbody>
										<?php if( !empty( $contract_consumed_items->stock ) ){ ?>
											<?php foreach( $contract_consumed_items->stock as $key => $stock_item ){ ?>
												<tr>
													<td><a href="<?php echo base_url( "webapp/job/profile/".$stock_item->job_id.'/stock' ); ?>" ><?php echo ( !empty( $current_job ) && ( $current_job == $stock_item->job_id ) ) ? '' : $stock_item->job_id; ?></a></td>
													<td><?php echo $stock_item->item_code; ?></td>
													<td><?php echo $stock_item->item_name; ?></td>
													<td><?php echo ucwords( $stock_item->item_type ); ?></td>
													<td class="text-center"><?php echo $stock_item->item_qty; ?></td>
													<td><span class="pull-right"><?php echo $stock_item->price; ?></span></td>
													<td><span class="pull-right"><?php echo ( number_format( $stock_item->price*$stock_item->item_qty, 2 ) ); ?></span></td>
												</tr>
											<?php $stock_total += ( $stock_item->price*$stock_item->item_qty ); $current_job = $stock_item->job_id; } ?>
											<tr>
												<th colspan="6">Stock Total</th>
												<th id="stock_total_tbl" ><span class="pull-right">&pound;<?php echo number_format( ( $stock_total ), 2 ); ?></span></th>
											</tr>
										<?php } else { ?>
											<tr>
												<td colspan="7" >There's currently no data to display</td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>

			<br/>
			<div class="accordion" id="accordion2" role="tablist" aria-multiselectable="true">
				<div class="panel" style="border: none;">
					<div class="section-container-bar panel-heading collapsed bg-grey pointer no-radius" role="tab" id="headingTwo" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo"  style="width: calc( 100% - 60px ); display: block; float: left; margin-bottom: 30px;">
						<h4 class="panel-title"> BOMs (<?php echo ( !empty( $contract_consumed_items->boms ) ) ? count( $contract_consumed_items->boms ) : 0  ?>) <span class="pull-right"><span class="pull-left"><strong><span id="bom_total" >&pound;0.00</span></strong></span><span class="pull-right"> &nbsp;<i class="caret-icon fas fa-caret-down text-yellow"></i></span></h4>
					</div>
					<div style="width: 40px;float: right;">
						<a data-toggle="modal" data-target="#csv-export-modal" data-item_type="bom" class="export-trigger"><img src="<?php echo base_url( "assets/images/csv-icon.png" ); ?>" style="width: 100%;" alt="Click to download BOM items as a CSV" title="Click to download BOM items as a CSV" /></a>
					</div>
					<div id="collapseTwo" class="panel-collapse collapse no-bg no-background" role="tabpanel" aria-labelledby="headingTwo" >
						<div class="panel-body">
							<div class="row table-responsive" style="width:100%; height:200px; overflow:auto">
								<table class="table" style="font-size:90%; overflow:hidden">
									<thead>
										<tr>
											<th width="15%">Item Code</th>
											<th width="20%">Item Name</th>
											<th width="15%">Item Type</th>
											<th width="15%"><span class="pull-right">Unit Price</span></th>
											<th width="15%"><span class="pull-right">Qty</span></th>
											<th width="15%"><span class="pull-right">Total Price</span></th>
										</tr>
									</thead>
									<tbody>
										<?php if( !empty( $contract_consumed_items->boms ) ){ ?>
											<?php foreach( $contract_consumed_items->boms as $k => $bom_item ){ ?>
												<tr>
													<td><?php echo $bom_item->item_code; ?></td>
													<td><?php echo $bom_item->item_name; ?></td>
													<td><?php echo strtoupper( $bom_item->item_type ); ?></td>
													<td><span class="pull-right"> &pound;<?php echo number_format( $bom_item->price, 2 );  ?></span></td>
													<td><span class="pull-right"> <?php echo $bom_item->item_qty; ?></span></td>
													<td><span class="pull-right"> &pound;<?php echo number_format( ( $bom_item->price*$bom_item->item_qty ), 2 ); ?></span></td>
												</tr>
											<?php $boms_total += ( $bom_item->price*$bom_item->item_qty ); $curr_job = $bom_item->job_id; } ?>
											<tr>
												<th colspan="5">BOMs Total</th>
												<th id="bom_total_tbl" ><span class="pull-right">&pound;<?php echo number_format( ( $boms_total ), 2 ); ?></span></th>
											</tr>
										<?php } else { ?>
											<tr>
												<td colspan="6" >There's currently no data to 
												</td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<br/>
			<div class="accordion" id="accordion3" role="tablist" aria-multiselectable="true">
				<div class="panel" style="border: none;">
					<div class="section-container-bar panel-heading collapsed bg-grey no-radius" role="tab" id="headingThree" data-toggle="collapse" data-parent="#accordion3" href="#collapseThree" aria-expanded="true" aria-controls="collapseThree"  style="width: calc( 100% - 60px ); display: block; float: left; margin-bottom: 30px;">
						<h4 class="panel-title"> Grand Total <span class="pull-right"><span class="pull-left"><strong><span id="bom_total" >&pound; <?php echo number_format( ( $stock_total + $boms_total ), 2 ); ?></span></strong>&nbsp;&nbsp;&nbsp;&nbsp;</span></h4>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal for CSV Export -->
<div id="csv-export-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<form id="csv-export" action="<?php echo base_url( "webapp/contract/download_consumed_items" ); ?>" method="post">
			<div class="modal-content">
				<div class="modal-header">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title" id="myCategoryModalLabel"><strong>CSV Download (Stock/BOMs)</strong></h4>
							<span id="category-feedback-message"></span>
						</div>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<input type="hidden" name="page" value="details" />
							<input type="hidden" name="item_type" value="" />
							<input type="hidden" name="contract_id" value="<?php echo ( !empty( $contract_details->contract_id ) ) ? $contract_details->contract_id : '' ;  ?>" />
							
							<div class="input-group form-group">
								<label class="input-group-addon">Dates from</label>
								<input type="text" class="form-control datepicker" name="date_from" value="" placeholder="DD/MM/YYYY" />
							</div>
							<div class="input-group form-group">
								<label class="input-group-addon">Dates to</label>
								<input type="text" class="form-control datepicker" name="date_to" value="" placeholder="DD/MM/YYYY" />
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<div class="row">
						<div class="col-md-3 col-sm-6 col-xs-12 pull-left">
							<button class="btn btn-block btn-danger" type="button" data-dismiss="modal">Cancel</button>
						</div>
						<div class="col-md-3 col-sm-6 col-xs-12 pull-right">
							<button id="category-quick-add-btn" class="btn btn-success btn-block" type="submit">Download</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<script>
	$( document ).ready(function(){
		$( ".export-trigger" ).on( "click", function(){
			var item_type = $( this ).data( "item_type" );
			$( '[name="item_type"]' ).val( item_type );
		});
		
		$( '.section-container-bar' ).click( function(){
			$( this ).closest( 'div' ).find( '.caret-icon' ).toggleClass('fa-caret-up fa-caret-down');			
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
	});
</script>