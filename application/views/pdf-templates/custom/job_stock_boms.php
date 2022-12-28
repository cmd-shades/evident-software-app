<?php $job_details = $document_setup['document_content']; ?>
<table width="100%" cellpadding="2px">
	<tr>
		<td colspan="2"><span style="color: #0092CD;">JOB DETAILS</span></td>
	</tr>
	<tr>
		<td colspan="2"><span ><hr></span></td>
	</tr>
	<tr>
		<td width="15%">Job ID:</td>
		<td width="85%"><?php echo ( !empty( $document_setup['document_content']->job_id ) ) ? ( $document_setup['document_content']->job_id ) : '' ; ?></td>
	</tr>
	<tr>
		<td width="15%">Job Type:</td>
		<td width="85%"><?php echo ( !empty( $document_setup['document_content']->job_type ) ) ? ( $document_setup['document_content']->job_type ) : '' ; ?></td>
	</tr>
	<tr>
		<td width="15%">Job Date:</td>
		<td width="85%"><?php echo ( !empty( $document_setup['document_content']->job_date ) ) ? ( $document_setup['document_content']->job_date ) : '' ; ?></td>
	</tr>
	<?php if( !empty( $job_details->summaryline ) ) { ?>
		<tr>
			<td width="15%">Job Address:</td>
			<td width="85%"><?php echo ( !empty( $document_setup['document_content']->summaryline ) ) ? ( $document_setup['document_content']->summaryline ) : '' ; ?></td>
		</tr>
	<?php } ?>
	<tr>
		<td colspan="2"><?php echo ( !empty( $document_setup['document_content']->completed_works ) ) ? ( $document_setup['document_content']->completed_works ) : '' ; ?></td>
	</tr>
	<?php if( !empty( $job_details->consumed_items ) ) { ?>
		<tr>
			<td colspan="2"><span style="color: #0092CD;">CONSUMED ITEMS</span></td>
		</tr>
		<tr>
			<td colspan="2"><span ><hr></span></td>
		</tr>
		<tr>
			<td colspan="2">
				<br>
				<table width="100%" cellpadding="2px" style="font-size:80%">
					<?php if( !empty( $job_details->consumed_items->stock ) ) { ?>
						<tr>
							<td colspan="5" style="color: #0092CD;" ><strong>STOCK</strong><br></td>
						</tr>
						<tr style="font-weight:bold" >
							<td>Item Code</td>
							<td>Item Name</td>
							<td style="text-align:right">Unit Price</td>
							<td style="text-align:right">Quantity</td>
							<td style="text-align:right">Total Price</td>
						</tr>
						<?php $stock_total = 0.00; foreach( $job_details->consumed_items->stock as $stock_item ) { ?>
							<tr>
								<td><?php echo $stock_item->item_code; ?></td>
								<td><?php echo $stock_item->item_name; ?></td>
								<td style="text-align:right">&pound;<?php echo number_format( $stock_item->price, 2 );?></td>
								<td style="text-align:right"><?php echo $stock_item->item_qty; ?></td>
								<td style="text-align:right">&pound;<?php echo number_format( ( $stock_item->price*$stock_item->item_qty ), 2 ); ?></td>
							</tr>
						<?php $stock_total += ( $stock_item->price*$stock_item->item_qty ); } ?>
						<tr style="font-size:12px;">
							<td colspan="4"><strong>Stock Total</strong></td>
							<td style="text-align:right"><strong>&pound;<?php echo number_format( ( $stock_total ), 2 ); ?></strong></td>
						</tr>
					<?php } ?>
					<tr>
						<td colspan="5" >&nbsp;</td>
					</tr>
					<?php if( !empty( $job_details->consumed_items->boms ) ) { ?>
						<tr>
							<td colspan="5" style="color: #0092CD;" ><strong>BOM/SORs</strong><br></td>
						</tr>
						<tr style="font-weight:bold" >
							<td>Item Code</td>
							<td>Item Name</td>
							<td style="text-align:right">Unit Price</td>
							<td style="text-align:right">Quantity</td>
							<td style="text-align:right">Total Price</td>
						</tr>
						<?php $boms_total = 0.00; foreach( $job_details->consumed_items->boms as $bom_item ) { ?>
							<tr>
								<td><?php echo $bom_item->item_code; ?></td>
								<td><?php echo $bom_item->item_name; ?></td>
								<td style="text-align:right">&pound;<?php echo number_format( $bom_item->price, 2 );?></td>
								<td style="text-align:right"><?php echo $bom_item->item_qty; ?></td>
								<td style="text-align:right">&pound;<?php echo number_format( ( $bom_item->price*$bom_item->item_qty ), 2 ); ?></td>
							</tr>
						<?php $boms_total += ( $bom_item->price*$bom_item->item_qty ); } ?>
						<tr style="font-size:12px;">
							<td colspan="4"><strong>BOM/SORs Total</strong></td>
							<td style="text-align:right"><strong>&pound;<?php echo number_format( ( $boms_total ), 2 ); ?></strong></td>
						</tr>
						<br>
					<?php } ?>
					<tr>
						<td colspan="5" style="color: #0092CD; border-top: 1px dashed #0092CD" >&nbsp;</td>
					</tr>
					<tr style="font-size:12px; color: #0092CD;">
						<td colspan="4"><strong>GRAND TOTAL</strong></td>
						<td style="text-align:right"><strong>&pound;<?php echo number_format( ( $boms_total + $stock_total ), 2 ); ?></strong></td>
					</tr>
				</table>
			</td>
		</tr>
	<?php } ?>

</table>