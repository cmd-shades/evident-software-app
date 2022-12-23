<div class="row">
	<div class="col-md-6 col-sm-6 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Stock used on this Job</legend>
			<?php if( !empty( $job_details->confirmed_items ) ) { ?>
				<table style="width:100%">
					<tr>
						<th width="20%">Item Code</th>
						<th width="70%">Item Name</th>
						<th width="10%" class="text-center">Quantity</th>
					</tr>
					<?php if( !empty( $job_details->confirmed_items ) ){ foreach( $job_details->confirmed_items as $stock_item ) { ?>
						<tr>
							<td><?php echo $stock_item->item_code; ?></td>
							<td><?php echo $stock_item->item_name; ?></td>
							<td class="text-center" ><span><?php echo $stock_item->item_qty; ?></span></td>
						</tr>
					<?php } } ?>
				</table>
			<?php }else{ ?>
				<?php echo $this->config->item('no_records'); ?>
			<?php } ?>
			
		</div>		
	</div>
</div>

<script>
	$(document).ready(function(){

	});
</script>