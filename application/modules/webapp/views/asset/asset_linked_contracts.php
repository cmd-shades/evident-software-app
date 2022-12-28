<div class="row asset_details">
	<?php /*<div class="col-md-6 col-sm-6 col-xs-12">
		<form class="form-horizontal">
			<input type="hidden" name="page" value="details" />
			<input type="hidden" name="asset_id" value="<?php echo $asset_details->asset_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<div class="x_panel tile has-shadow">
				<legend>Update Asset Details</legend>
				<div class="input-group form-group">
					<label class="input-group-addon">Linked Contracts</label>
					<select id="linked-contracts" class="form-control" name="linked_contrcats[]" multiple="multiple" style="width: 100%">
						<?php if( !empty( $available_contracts ) ){ foreach( $available_contracts as $contract ){  ?>
							<?php if( !in_array( $contract->contract_id, array_column( $linked_contracts, 'contract_id' ) ) ){  ?>
								<option value="<?php echo $contract->contract_id; ?>"><?php echo $contract->contract_name; ?></option>
							<?php } ?>							
						<?php } } ?>
					</select>
				</div>

				<?php if( $this->user->is_admin || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
					<div class="row">
						<div class="col-md-6">
							<button id="update-asset-btn-1" class="btn btn-sm btn-block btn-flow btn-success btn-next update-asset-btn" type="button" >Update </button>
						</div>
					</div>
				<?php }else{ ?>
					<div class="row col-md-6">
						<span id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</span>
					</div>
				<?php } ?>
			</div>
		</form>
	</div> <?php */ ?>
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Linked Contracts <span class="hide pull-right pointer attach-risk"><i class="fas fa-plus text-green" title="Attach assets to this Contract" ></i></span></legend>
			<div class="rows">
				<?php if( !empty( $linked_contracts ) ){ ?>
					<table class="table table-responsive">
						<thead>
							<tr>
								<th>Contract Name</th>
								<th>Contract Type</th>
								<th>Status</th>
								<th><span class="pull-right">Action</span></th>									
							</tr>
						</thead>
						<tbody>
							<?php foreach( $linked_contracts as $k => $contract ){ ?>
								<tr>
									<td><?php echo $contract->contract_name; ?></td>
									<td><?php echo $contract->type_name; ?></td>
									<td><?php echo $contract->status_name; ?></td>
									<td><span class="pull-right"><a href="<?php echo base_url('webapp/contract/profile/'.$contract->contract_id ); ?>" title="Click to view the Asset profile" ><i class="fas fa-external-link-alt"></i> View</a> | <span class="unlink-asset pointer text-red" data-contract_id="<?php echo $contract->contract_id; ?>" title="Click to unlink this Asset from this contract"><i class="far fa-trash-alt"></i> Unlink</span></span></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				<?php } else { ?>
					<div>
						<span><?php echo $this->config->item('no_records'); ?></span>
					</div>
				<?php } ?>
				
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		$('#linked-contracts').select2({
			"language": {
				"noResults": function(){
					return "There\'s currently no contracts avaiable to link this Asset to";
				}
			},
			escapeMarkup: function ( markup ) {
				return markup;
			}
		});
	});
</script>

