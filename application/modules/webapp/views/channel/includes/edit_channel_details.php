<div class="modal-header">
	<div class="row">
		<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
			<h4 class="modal-title"><span class="channel_name_in_modal"><?php echo ( !empty( $channel_details->channel_name ) ) ? $channel_details->channel_name : '' ; ?></span> (ID: <span class="channel_name_in_modal"><?php echo ( !empty( $channel_details->channel_id ) ) ? $channel_details->channel_id : '' ; ?>)</span></h4>
		</div>
	</div>
</div>

<div class="modal-body">
	<div class="rows group-content">
		<form id="update-channel-form">
			<input type="hidden" name="channel_id" value="<?php echo ( !empty( $channel_details->channel_id ) ) ? $channel_details->channel_id : '' ; ?>" />
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<label class="input-label-40">Channel Name</label>
					<input class="input-field-60" name="channel_details[channel_name]" type="text" placeholder="Channel Name" value="<?php echo ( !empty( $channel_details->channel_name ) ) ? $channel_details->channel_name : '' ; ?>" />
				</div>
				
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 el-hidden">
					<label class="input-label-40">Channel Reference Code</label>
					<input class="input-field-60" name="channel_details[channel_reference_code]" type="text" placeholder="Channel Reference Code" value="<?php echo ( !empty( $channel_details->channel_reference_code ) ) ? $channel_details->channel_reference_code : '' ; ?>" />
				</div>

				<?php /*
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<label class="input-label-40">Channel Status</label>
					<select class="input-field-60" name="channel_details[channel_status]">
						<option value="active" <?php echo ( !empty( $channel_details->channel_status ) && ( strtolower( $channel_details->channel_status ) == "active" ) ) ? 'selected="selected"' : '' ; ?>>Active</option>
						<option value="disabled" <?php echo ( empty( $channel_details->channel_status ) || ( strtolower( $channel_details->channel_status ) == "disabled" ) ) ? 'selected="selected"' : '' ; ?>>Disabled</option>
					</select>
				</div> */ ?>
				
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<label class="input-label-40">Channel Provider</label>
					<?php
					if( !empty( $providers ) ){ ?>
						<select name="channel_details[provider_id]" class="input-field-60">
							<option value="">Select Provider</option>
							<?php foreach( $providers as $p_row ){?>
								<option value="<?php echo ( !empty( $p_row->provider_id ) ) ? $p_row->provider_id : ''; ?>" title="<?php echo ( !empty( $p_row->provider_name ) ) ? $p_row->provider_name : '' ?>" <?php echo ( ( !empty( $channel_details->provider_id ) && ( $channel_details->provider_id == $p_row->provider_id ) ) ? 'selected="selected"' : '' ); ?>><?php echo ( !empty( $p_row->provider_name ) ) ? $p_row->provider_name : '' ?></option>
							<?php } ?>
						</select>
					<?php } ?>
				</div>
				
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<label class="input-label-40">Channel Description</label>
					<textarea rows="3" class="input-field-60" name="channel_details[description]" type="text" placeholder="Channel Description"><?php echo ( !empty( $channel_details->description ) ) ? $channel_details->description : '' ; ?></textarea>
				</div>
				
				
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<label class="input-label-40">Distribution Start Date</label>
					<input class="input-field-60 datetimepicker" name="channel_details[distribution_start_date]" type="text" placeholder="Distribution Start Date" value="<?php echo ( validate_date( $channel_details->distribution_start_date ) ) ? format_date_client( $channel_details->distribution_start_date ) : '' ; ?>" />
				</div>
				
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<label class="input-label-40">Distribution End Date</label>
					<input class="input-field-60 datetimepicker" name="channel_details[distribution_end_date]" type="text" placeholder="Distribution End Date" value="<?php echo ( validate_date( $channel_details->distribution_end_date ) ) ? format_date_client( $channel_details->distribution_end_date ) : '' ; ?>" />
				</div>
				
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<label class="input-label-40">Is Channel OTT?</label>
					<select name="channel_details[is_channel_ott]" class="input-field-60">
						<option value="">Please select</option>
						<option value="yes" <?php echo ( ( !empty( $channel_details->is_channel_ott ) && ( $channel_details->is_channel_ott == true ) ) ? 'selected="selected"' : '' ); ?>>Yes</option>
						<option value="no" <?php echo ( ( empty( $channel_details->is_channel_ott ) || ( $channel_details->is_channel_ott != true ) ) ? 'selected="selected"' : '' ); ?>>No</option>
					</select>
				</div>

				<div class="source_url col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php echo ( !empty( $channel_details->is_channel_ott ) ) ? "el-shown" : "el-hidden"; ?>">
					<label class="input-label-40">Channel Source URL</label>
					<textarea rows="3" class="input-field-60" name="channel_details[source_url]" placeholder="Channel Source URL"><?php echo ( !empty( $channel_details->source_url ) ) ? $channel_details->source_url : '' ; ?></textarea>
				</div>
				
				<div class="technical_encoded_url col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php echo ( !empty( $channel_details->is_channel_ott ) ) ? "el-shown" : "el-hidden"; ?>">
					<label class="input-label-40">Technical Encoded URL</label>
					<textarea class="input-field-60" name="channel_details[technical_encoded_url]" placeholder="Technical Encoded URL"><?php echo ( !empty( $channel_details->technical_encoded_url ) ) ? $channel_details->technical_encoded_url : '' ; ?></textarea>
				</div>
				
				<div class="satelite_sources col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php echo ( !empty( $channel_details->is_channel_ott ) ) ? "el-hidden" : "el-shown"; ?>">
					<label class="input-label-40">Satellite Sources</label>
					<input class="input-field-60" name="channel_details[satelite_sources]" type="text" placeholder="Satellite Sources" value="<?php echo ( !empty( $channel_details->satelite_sources ) ) ? $channel_details->satelite_sources : '' ; ?>" />
				</div>
				
				<div class="regions col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php echo ( !empty( $channel_details->is_channel_ott ) ) ? "el-hidden" : "el-shown"; ?>">
					<label class="input-label-40">Regions</label>
					<input class="input-field-60" name="channel_details[regions]" type="text" placeholder="Regions" value="<?php echo ( !empty( $channel_details->regions ) ) ? $channel_details->regions : '' ; ?>" />
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<button class="btn btn-block btn-update btn-primary" type="submit" data-content_section="content">Update</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>