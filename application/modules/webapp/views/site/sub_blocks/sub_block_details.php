<div class="row">
	<div class="col-md-6 col-sm-6 col-xs-12">
		<div class="x_panel tile has-shadow">
			<form id="update-sub_block-profile-form" class="form-horizontal">
				<input type="hidden" name="page" value="sub_block" />
				<input type="hidden" name="site_id" value="<?php echo $sub_block_details->site_id; ?>" />
				<input type="hidden" name="sub_block_id" value="<?php echo $sub_block_details->sub_block_id; ?>" />
				<legend>Sub Block Details</legend>
				<div class="input-group form-group">
					<label class="input-group-addon">Sub Block Name</label>
					<input name="sub_block_name" class="form-control" type="text" placeholder="Sub Block Name" value="<?php echo $sub_block_details->sub_block_name; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Sub Block Description</label>
					<textarea name="sub_block_desc" type="text" class="form-control" rows="2"><?php echo $sub_block_details->sub_block_desc; ?></textarea>     
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Sub Block Postcode</label>
					<input name="sub_block_postcode" class="form-control" type="text" placeholder="Sub Block Postcode" value="<?php echo $sub_block_details->sub_block_postcode; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Sub Block Address</label>
					<textarea name="sub_block_address" type="text" class="form-control" rows="2"><?php echo $sub_block_details->sub_block_address; ?></textarea>     
				</div>
				<br/>
				<div class="input-group">
					<button type="button" class="update-sub_block-btn btn btn-sm btn-success">Save Changes</button>
					<button type="button" class="delete-sub_block-btn btn btn-sm btn-danger" data-sub_block_id="<?php echo $sub_block_details->sub_block_id; ?>" >Delete Sub Block</button>
				</div>
			</form>
		</div>
	</div>
</div>