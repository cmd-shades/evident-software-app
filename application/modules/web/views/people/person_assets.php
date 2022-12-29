<div class="row">
	<?php if ($this->user->is_admin || !empty($permissions->can_add) || !empty($permissions->can_view) || !empty($permissions->is_admin)) { ?>
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Assigned Assets</legend>
			<table id="datatable" class="table-responsive" style="margin-bottom:0px;width:100%" >
				<thead>
					<tr>
						<th width="40%">Unique ID</th>
						<th width="20%">Type</th>
						<th width="20%">Primary Attribute</th>
						<th width="20%">Status</th>
					</tr>
				</thead>
				
				<tbody>
					<?php if (!empty($assigned_assets)) {
					    foreach ($assigned_assets as $asset) { ?>
						<tr>
							<td><a href="<?php echo base_url('webapp/asset/profile/'.$asset->asset_id); ?>" ><?php echo (!empty($asset->asset_unique_id)) ? ucwords($asset->asset_unique_id) : '' ; ?></a></td>
							<td><?php echo (!empty($asset->asset_type)) ? $asset->asset_type : '' ; ?></td>
							<td><?php echo (!empty($asset->primary_attribute)) ? strtoupper($asset->primary_attribute) : '' ; ?></td>
							<td><?php echo (!empty($asset->asset_status)) ? $asset->asset_status : '' ; ?></td>
						</tr>
					<?php }
					    } else { ?>
						<tr>
							<td colspan="7"><?php echo $this->config->item('no_records'); ?></td>
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