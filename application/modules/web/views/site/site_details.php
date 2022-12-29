<div class="row">
	<div class="col-md-8 col-sm-8 col-xs-12">
		<form id="update-site-form" method="post" >
			<input type="hidden" name="page" value="details" />
			<input type="hidden" name="site_id" value="<?php echo $site_details->site_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<div class="x_panel tile has-shadow">
				<legend>Update Building Details</legend>

				<div class="row" >
					<div class="col-md-6 col-sm-6 col-xs-12">
						<div class="input-group form-group">
							<label class="input-group-addon">Building Name</label>
							<input name="site_name" class="form-control" type="text" placeholder="Site Name" value="<?php echo $site_details->site_name; ?>" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Building QR Code <i title="Associated Barcode" class="fas fa-barcode"></i></label>
							<input name="site_unique_id" class="form-control" type="text" placeholder="Site QR Code" value="<?php echo $site_details->site_unique_id; ?>" />
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">QR Code Location</label>
							<input name="qr_code_location" class="form-control" type="text" placeholder="QR Code Location" value="<?php echo $site_details->qr_code_location; ?>" />
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">3rd Party Site Ref</label>
							<?php if ($this->user->is_admin || !empty($permissions->can_edit) || !empty($permissions->is_admin)) { ?>
								<input name="external_site_ref" class="form-control" type="text" placeholder="3rd Party/External Site Ref" value="<?php echo !empty($site_details->external_site_ref) ? $site_details->external_site_ref : ''; ?>" />
							<?php } else {?>
								<input readonly class="form-control" type="text" placeholder="3rd Party/External Site Ref" value="<?php echo !empty($site_details->external_site_ref) ? $site_details->external_site_ref : ''; ?>" />
							<?php } ?>
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">Building Reference</label>
							<input name="site_reference" class="form-control" type="text" placeholder="Site Reference" value="<?php echo $site_details->site_reference; ?>" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Building Type</label>
							<input name="building_type" class="form-control" type="text" placeholder="Building Type" value="<?php echo $site_details->building_type; ?>" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Estate Name</label>
							<input name="estate_name" class="form-control" type="text" placeholder="Estate Name" value="<?php echo !empty($site_details->estate_name) ? $site_details->estate_name : ''; ?>" />
						</div>
						<!-- <div class="input-group form-group">
							<label class="input-group-addon">Site Postcode</label>
							<input name="site_postcodes" class="form-control" type="text" placeholder="Site Postcodes" value="<?php echo $site_details->site_postcodes; ?>" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Site Dwellings</label>
							<input name="total_dwellings" class="form-control" type="text" placeholder="Site Dwellings" value="<?php echo ($site_details->total_dwellings > 0) ? strtoupper($site_details->total_dwellings) : (!empty($existing_dwellings) ? count($existing_dwellings) : 0); ?>" />
						</div> -->
						<div class="input-group form-group">
							<label class="input-group-addon">Number of Floors</label>
							<input name="number_of_floors" class="form-control" type="text" placeholder="Number of Floors" value="<?php echo $site_details->number_of_floors; ?>" />
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">Number of Flats</label>
							<input name="number_of_flats" class="form-control" type="text" placeholder="Number of Flats" value="<?php echo $site_details->number_of_flats; ?>" />
						</div>
						
						<div class="input-group form-group">
							<label class="input-group-addon">Number of Rooms</label>
							<input name="number_of_rooms" class="form-control" type="text" placeholder="Number of Rooms" value="<?php echo $site_details->number_of_rooms; ?>" />
						</div>
						
						<div class="input-group form-group">
							<label class="input-group-addon">Approx Max Residents</label>
							<input name="approx_max_residents" class="form-control" type="text" placeholder="Approx Max Residents" value="<?php echo $site_details->approx_max_residents; ?>" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Occupancy Type</label>
							<input name="occupancy_type" class="form-control" type="text" placeholder="Occupancy Type" value="<?php echo $site_details->occupancy_type; ?>" />
						</div>
						
						<div class="input-group form-group">
							<label class="input-group-addon">Building Status</label>
							<select name="event_tracking_status_id" class="form-control">
								<option value="">Please select</option>
								<?php if (!empty($site_statuses)) {
								    foreach ($site_statuses as $k => $status) { ?>
									<option value="<?php echo $status->status_id; ?>" <?php echo ($site_details->status_id == $status->status_id) ? 'selected=selected' : ''; ?> ><?php echo $status->status_name; ?></option>
								<?php }
								    } ?>
							</select>
						</div>
						
						<div class="input-group form-group">
							<label class="input-group-addon">Assigned Region</label>
							<select id="region_id" name="region_id" class="form-control" >
								<option value="">Please select</option>
								<?php if (!empty($postcode_regions)) {
								    foreach ($postcode_regions as $k => $region) { ?>
									<option value="<?php echo $region->region_id; ?>" <?php echo ($site_details->region_id == $region->region_id) ? 'selected=selected' : ''; ?> ><?php echo $region->region_name; ?></option>
								<?php }
								    } ?>
							</select>
						</div>
						
						<!--
						<div class="input-group form-group">
							<label class="input-group-addon">Event Tracking ID</label>
							<input name="event_tracking_status_id" class="form-control" type="text" placeholder="Event Tracking ID" value="<?php echo $site_details->event_tracking_status_id; ?>" />
						</div> -->
						<?php /* ?><div class="input-group form-group">
                            <label class="input-group-addon">Event Tracking Status</label>
                            <select name="event_tracking_status_id" class="form-control">
                                <option>Please select</option>
                                <?php if( !empty( $event_statuses ) ) { foreach( $event_statuses as $k => $status ) { ?>
                                    <option value="<?php echo $status->event_tracking_status_id; ?>" <?php echo ( $site_details->event_tracking_status_id == $status->event_tracking_status_id ) ? 'selected=selected' : ''; ?> ><?php echo $status->event_tracking_status; ?></option>
                                <?php } } ?>
                            </select>
                        </div>
                        <?php */ ?>
						<br/>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">

						<div class="input-group form-group">
							<label class="input-group-addon">Audit Result Status</label>
							<select name="audit_result_status_id" class="form-control">
								<option>Please select</option>
								<?php if (!empty($audit_result_statuses)) {
								    foreach ($audit_result_statuses as $k => $result_status) { ?>
									<option value="<?php echo $result_status->audit_result_status_id; ?>" <?php echo ($site_details->audit_result_status_id == $result_status->audit_result_status_id) ? 'selected=selected' : ''; ?> ><?php echo $result_status->result_status_alt; ?></option>
								<?php }
								    } ?>
							</select>
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Building Tenure</label>
							<input name="tenure" class="form-control" type="text" placeholder="Building Tenure" value="<?php echo $site_details->tenure; ?>" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Risk Category</label>
							<select name="risk_category" class="form-control">
								<option>Please select</option>
								<option value="Low" <?php echo (strtolower($site_details->risk_category) == 'low') ? 'selected=selected' : ''; ?> >Low</option>
								<option value="Medium" <?php echo (strtolower($site_details->risk_category) == 'medium') ? 'selected=selected' : ''; ?> >Medium</option>
								<option value="Medium" <?php echo (strtolower($site_details->risk_category) == 'high') ? 'selected=selected' : ''; ?> >High</option>
							</select>
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">Building Year</label>
							<input name="build_year" class="form-control" type="text" placeholder="Building Year" value="<?php echo $site_details->build_year; ?>" />
						</div>
						
						<?php
                        if (in_array($current_account, $demo_accounts)) { ?>
						<div class="input-group form-group">
							<label class="input-group-addon">Building Height</label>
							<input name="build_height" class="form-control" type="text" placeholder="Building Height" value="<?php echo (!empty($site_details->build_height)) ? $site_details->build_height : '' ; ?>" />
						</div>
						<?php
                        } ?>
						
						<div class="input-group form-group">
							<label class="input-group-addon">Evacuation Strategy</label>
							<input name="evacuation_strategy" class="form-control" type="text" placeholder="Evacuation Strategy" value="<?php echo $site_details->evacuation_strategy; ?>" />
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon"><?php echo (in_array($current_account, $demo_accounts)) ? "Safety Manager Name" : "Site Contact Name" ; ?></label>
							<input name="site_contact_name" class="form-control" type="text" placeholder="<?php echo (in_array($current_account, $demo_accounts)) ? "Safety Manager Name" : "Site Contact Name" ; ?>" value="<?php echo !empty($site_details->site_contact_name) ? $site_details->site_contact_name : ''; ?>" />
						</div>

						<?php if (!in_array($current_account, $demo_accounts)) { ?>
							<div class="input-group form-group">
								<label class="input-group-addon">Site Contact Role</label>
								<input name="site_contact_role" class="form-control" type="text" placeholder="Site Contact Role" value="<?php echo !empty($site_details->site_contact_role) ? $site_details->site_contact_role : ''; ?>" />
							</div>
						<?php } ?>

						<div class="input-group form-group">
							<label class="input-group-addon"><?php echo (in_array($current_account, $demo_accounts)) ? "Safety Manager Phone" : "Site Contact Number" ; ?></label>
							<input name="site_contact_number" class="form-control" type="text" placeholder="<?php echo (in_array($current_account, $demo_accounts)) ? "Safety Manager Phone" : "Site Contact Number" ; ?>" value="<?php echo !empty($site_details->site_contact_number) ? $site_details->site_contact_number : ''; ?>" />
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon"><?php echo (in_array($current_account, $demo_accounts)) ? "Safety Manager Mobile" : "Site Contact Mobile" ; ?></label>
							<input name="site_contact_mobile" class="form-control" type="text" placeholder="<?php echo (in_array($current_account, $demo_accounts)) ? "Safety Manager Mobile" : "Site Contact Mobile" ; ?>" value="<?php echo !empty($site_details->site_contact_mobile) ? $site_details->site_contact_mobile : ''; ?>" />
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon"><?php echo (in_array($current_account, $demo_accounts)) ? "Safety Manager Email" : "Site Contact Email" ; ?></label>
							<input name="site_contact_email" class="form-control" type="text" placeholder="<?php echo (in_array($current_account, $demo_accounts)) ? "Safety Manager Email" : "Site Contact Email" ; ?>" value="<?php echo !empty($site_details->site_contact_email) ? $site_details->site_contact_email : ''; ?>" />
						</div>


						<div class="input-group form-group">
							<label class="input-group-addon"><?php echo (in_array($current_account, $demo_accounts)) ? "Square meterage" : "Construction Info" ; ?></label>
							<textarea name="construction_info" type="text" class="form-control" rows="3"><?php echo (!empty($site_details->construction_info)) ? $site_details->construction_info : '' ?></textarea>
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">Building Notes</label>
							<textarea name="site_notes" type="text" class="form-control" rows="3"><?php echo (!empty($site_details->site_notes)) ? $site_details->site_notes : '' ?></textarea>
						</div>
					</div>
				</div>

				<?php if ($this->user->is_admin || !empty($permissions->can_edit) || !empty($permissions->is_admin)) { ?>
					<div class="row">
						<div class="col-md-3">
							<button id="update-site-btn" class="btn btn-block btn-flow btn-success" type="button" >Update Building</button>
						</div>

					<?php if ($this->user->is_admin || !empty($permissions->can_delete) || !empty($permissions->is_admin)) { ?>
							<div class="col-md-3">
								<button id="delete-site-btn" data-site_id = "<?php echo (!empty($site_details->site_id)) ? $site_details->site_id : '' ; ?>" class="btn btn-block btn-flow btn-danger" type="button">Delete Building</button>
							</div>
						</div>
					<?php } ?>

				<?php } else { ?>
					<div class="row col-md-3">
						<button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>
					</div>
				<?php } ?>
			</div>
		</form>
	</div>
	<div class="col-md-4 col-sm-4 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Building Location</legend>

			<table style="width:100%">
				<?php $postcode = false;
			if (!empty($site_details->site_postcodes)) {
			    $postcode = $site_details->site_postcodes;
			} elseif (!empty($site_details->address_postcode)) {
			    $postcode = $site_details->address_postcode;
			} ?>
				<tr>
					<th width="50%">Building Postcode</th><td width="50%"><?php echo (!empty($postcode)) ? $postcode : "" ; ?></td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="full-width">
							<iframe width="100%" height="280" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%&height=280&hl=en&q=<?php echo (!empty($postcode)) ? str_replace(" ", "+", $postcode) : "" ; ?>&ie=UTF8&t=&z=16&iwloc=B&output=embed"></iframe>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){
		$( '#delete-site-btn' ).click( function( event ){
			event.preventDefault();

			var siteID = $( this ).data( "site_id" );
			swal({
				title: 'Confirm Building delete?',
				// type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/site/delete_site/'); ?>",
						method:"POST",
						data: { site_id: siteID },
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout( function(){
									window.location.href = "<?php echo base_url('webapp/site/sites') ?>";
								}, 3000 );
							} else {
								swal({
									type: 'error',
									title: data.status_msg
								})
							}
						}
					});
				}
			}).catch(swal.noop)



		});


		//Submit form for processing
		$( '#update-site-btn' ).click( function( event ){

			event.preventDefault();
			var formData = $('#update-site-form').serialize();
			swal({
				title: 'Confirm Building update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/site/update_site/'.$site_details->site_id); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){
									location.reload();
								} ,3000);
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}
						}
					});
				}
			}).catch(swal.noop)
		});
	});
</script>

