<div class="row">
	<div class="col-md-6 col-sm-6 col-xs-12">
		<div class="x_panel tile has-shadow">
			<form id="update-location-profile-form" class="form-horizontal">
				<input type="hidden" name="page" value="location" />
				<input type="hidden" name="site_id" value="<?php echo $location_details->site_id; ?>" />
				<input type="hidden" name="location_id" value="<?php echo $location_details->location_id; ?>" />
				<legend>Location Details</legend>
				<div class="input-group form-group">
					<label class="input-group-addon">Location Reference</label>
					<input id="location_ref" name="location_ref" class="form-control" type="text" placeholder="Location Reference" readonly value="<?php echo $location_details->location_ref; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Location Name</label>
					<input id="location_name" name="location_name" class="form-control" type="text" placeholder="Location Name" value="<?php echo $location_details->location_name; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Location Type</label>
					<select name="location_type_id" class="form-control">
						<option>Please select</option>
						<?php if( !empty( $location_types ) ){ foreach( $location_types as $location_type_id => $location_type ) { ?>
							<option value="<?php echo $location_type_id; ?>" <?php echo ( strtolower( $location_details->location_type_id ) == $location_type_id ) ? 'selected=selected' : ''; ?> ><?php echo $location_type; ?></option>
						<?php } } ?>
					</select>	
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Salutation</label>
					<input id="resident_salutation" name="resident_salutation" class="form-control" type="text" placeholder="Salutation e.g. Mr/ Mrs/ Miss /Ms /Dr /Prof. " value="<?php echo ( !empty( $location_details->resident_salutation ) ) ? $location_details->resident_salutation : ''; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Resident First Name</label>
					<input id="resident_first_name" name="resident_first_name" class="form-control" type="text" placeholder="Resident First Name" value="<?php echo ( !empty( $location_details->resident_first_name ) ) ? $location_details->resident_first_name : ''; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Resident Last Name</label>
					<input id="resident_last_name" name="resident_last_name" class="form-control" type="text" placeholder="Resident Last Name" value="<?php echo ( !empty( $location_details->resident_last_name ) ) ? $location_details->resident_last_name : ''; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Resident Email</label>
					<input id="resident_email_address" name="resident_email_address" class="form-control" type="email" placeholder="Email address " value="<?php echo ( !empty( $location_details->resident_email_address ) ) ? $location_details->resident_email_address : ''; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Contact Number 1</label>
					<input id="resident_contact_number1" name="resident_contact_number1" class="form-control" type="text" placeholder="Resident Contact Number 1" value="<?php echo ( !empty( $location_details->resident_contact_number1 ) ) ? $location_details->resident_contact_number1 : ''; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Contact Number 2</label>
					<input id="resident_contact_number2" name="resident_contact_number2" class="form-control" type="text" placeholder="Resident Contact Number 2" value="<?php echo ( !empty( $location_details->resident_contact_number2 ) ) ? $location_details->resident_contact_number2 : ''; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Zone</label>
					<select name="zone_id" class="form-control">
						<option>Please select</option>
						<?php if( !empty( $site_zones ) ){ foreach( $site_zones as $k => $site_zone ) { ?>
							<option value="<?php echo $site_zone->zone_id; ?>" <?php echo ( strtolower( $location_details->zone_id ) == $site_zone->zone_id ) ? 'selected=selected' : ''; ?> ><?php echo !empty( $site_zone->sub_block_name ) ? $site_zone->sub_block_name.' - ' : '' ; ?> <?php echo $site_zone->zone_name; ?> <?php //echo ( !empty( $site_zone->zone_description ) ) ? ' - '.$site_zone->zone_n : ''; ?></option>
						<?php } } ?>
					</select>	
				</div>
				<div class="input-group">
					<label class="input-group-addon">Location Notes</label>
					<textarea id="location_notes" name="location_notes" type="text" class="form-control" rows="3"><?php echo ( !empty( $location_details->location_notes ) ) ? $location_details->location_notes : '' ?></textarea>     
				</div>
				<br/>
				<div class="input-group">
					<button type="button" class="update-location-btn btn btn-sm btn-success">Save Changes</button>
					<button type="button" class="delete-location-btn btn btn-sm btn-danger" data-location_id="<?php echo $location_details->location_id; ?>" >Delete Location</button>
				</div>
			</form>
		</div>
	</div>
</div>


<script>

	$( document ).ready( function(){
		

	
	} );

</script>