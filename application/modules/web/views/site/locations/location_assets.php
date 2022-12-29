<div class="row">
	<?php if ($this->user->is_admin || !empty($permissions->can_add) || !empty($permissions->can_view) || !empty($permissions->is_admin)) { ?>
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Assets assigned to this Building</legend>
			<table id="datatable" class="table-responsive" style="margin-bottom:0px;width:100%" >
				<thead>
					<tr>
						<th width="25%">Asset Name</th>
						<th width="15%">Make &amp; Model</th>
						<th width="10%">Type</th>
						<th width="10%">Sub Type</th>
						<th width="15%">Unique ID</th>
						<th width="15%">IMEI #</th>
						<th width="10%">Status</th>
					</tr>
				</thead>
				
				<tbody>
					<?php if (!empty($assigned_assets)) {
					    foreach ($assigned_assets as $asset) { ?>
						<tr>
							<td><a href="<?php echo base_url('webapp/asset/profile/'.$asset->asset_id); ?>" ><?php echo ucwords($asset->asset_name); ?></a></td>
							<td><?php echo $asset->asset_make; ?> <?php echo $asset->asset_model; ?></span></td>
							<td><?php echo $asset->asset_type; ?></td>
							<td><?php echo $asset->asset_sub_type; ?></td>
							<td><?php echo strtoupper($asset->asset_unique_id); ?></td>
							<td><?php echo strtoupper($asset->asset_imei_number); ?></td>
							<td><?php echo (!empty($asset->site_id)) ? 'Assigned' : 'Un-assigned'; ?></td>
						</tr>
					<?php }
					    } else { ?>
						<tr>
							<td colspan="7"><br/><?php echo $this->config->item('no_records'); ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>		
	</div>
	<?php } ?>
</div>

<script>
	$(document).ready(function(){

	});
</script>