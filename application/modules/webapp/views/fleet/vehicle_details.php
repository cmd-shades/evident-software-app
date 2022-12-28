<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="row">
			<form id="update-vehicle-form" method="post">
				<input type="hidden" name="page" value="details" />
				<input type="hidden" name="vehicle_id" value="<?php echo $vehicle_details->vehicle_id; ?>" />
				<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="x_panel tile has-shadow left_panel">
						<legend>Update Vehicle Details</legend>
						<div class="input-group form-group">
							<label class="input-group-addon">Vehicle Make</label>
							<input name="vehicle_make" class="form-control" type="text" placeholder="Vehicle Make" value="<?php echo $vehicle_details->vehicle_make; ?>" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Vehicle Model</label>
							<input name="vehicle_model" class="form-control" type="text" placeholder="Vehicle Model" value="<?php echo $vehicle_details->vehicle_model; ?>" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Vehicle Year</label>
							<input name="year" class="form-control" type="text" placeholder="Vehicle Year" value="<?php echo $vehicle_details->year; ?>" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Vehicle Reg</label>
							<input <?php echo ( $this->user->is_admin || !empty( $permissions->is_admin ) ) ? 'name="vehicle_reg"' : '' ; ?> class="form-control" type="text" placeholder="Vehicle Reg" value="<?php echo ( !empty( $vehicle_details->vehicle_reg ) ) ? $vehicle_details->vehicle_reg : '' ; ?>" <?php echo ( $this->user->is_admin || !empty( $permissions->is_admin ) ) ? '' :  'readonly = "readonly"'; ?> />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Vehicle Barcode</label>
							<input <?php echo ( $this->user->is_admin || !empty( $permissions->is_admin ) ) ? 'name="vehicle_barcode"' : '' ; ?> class="form-control" type="text" placeholder="Vehicle Barcode" value="<?php echo ( !empty( $vehicle_details->vehicle_barcode ) ) ? $vehicle_details->vehicle_barcode : '' ; ?>" <?php echo ( $this->user->is_admin || !empty( $permissions->is_admin ) ) ? '' :  'readonly = "readonly"'; ?> />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Vehicle Status</label>
							<select name="veh_status_id" class="form-control">
								<?php 
								if( !empty( $vehicle_statuses ) ){ 
									foreach( $vehicle_statuses as $row ){ ?>
										<option value="<?php echo $row->status_id; ?>" <?php echo ( $vehicle_details->veh_status_id == $row->status_id ) ? "selected='selected'" : '' ; ?> ><?php echo $row->status_name; ?></option>
								<?php
									}
								} ?>
							</select>
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Vehicle Supplier</label>
							<select name="supplier_id" class="form-control">
								<?php 
								if( !empty( $vehicle_suppliers ) ){ 
									foreach( $vehicle_suppliers as $row ){ ?>
										<option value="<?php echo $row->supplier_id; ?>" <?php echo ( $vehicle_details->supplier_id == $row->supplier_id ) ? "selected='selected'" : '' ; ?>><?php echo $row->supplier_name; ?></option>
								<?php
									}
								} ?>
							</select>
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Vehicle Supply Date</label>
							<input name="supply_date" value="<?php echo date( 'd/m/Y', strtotime( $vehicle_details->supply_date ) ); ?>" class="form-control datepicker" placeholder="<?php echo date( 'd/m/Y' ); ?>" data-date-format="DD/MM/Y" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Vehicle Owner</label>
							<select name="owner_type_id" class="form-control">
								<option value="">Please select</option>
								<?php 
								if( !empty( $vehicle_owner_types ) ){ 
									foreach( $vehicle_owner_types as $row ){ ?>
										<option value="<?php echo $row['owner_type_id']; ?>" <?php echo ( !empty( $vehicle_details->owner_type_id ) && $vehicle_details->owner_type_id == $row['owner_type_id'] ) ? "selected='selected'" : '' ; ?>><?php echo $row['owner_type_name']; ?></option>
								<?php
									}
								} ?>
							</select>
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">MOT Expiry</label>
							<input name="mot_expiry" value="<?php echo date( 'd/m/Y', strtotime( $vehicle_details->mot_expiry ) ); ?>" class="form-control datepicker" placeholder="<?php echo date( 'd/m/Y' ); ?>" data-date-format="DD/MM/Y" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">TAX Expiry</label>
							<input name="tax_expiry" value="<?php echo date( 'd/m/Y', strtotime( $vehicle_details->tax_expiry ) ); ?>" class="form-control datepicker" placeholder="<?php echo date( 'd/m/Y' ); ?>" data-date-format="DD/MM/Y" />
						</div>
					</div>
				</div>

				<div class="col-md-6 col-sm-6 col-xs-12 pull-left">
					<div class="x_panel tile has-shadow right_panel">
						<div class="input-group form-group">
							<label class="input-group-addon">Vehicle Insured</label>
							<select name="is_insured"  class="form-control">
								<option value="yes" <?php echo ( !empty( $vehicle_details->is_insured ) && ( $vehicle_details->is_insured == true ) ) ? 'selected="selected"' : '' ; ?> >Yes</option>
								<option value="no" <?php echo ( empty( $vehicle_details->is_insured ) || ( $vehicle_details->is_insured != true ) ) ? 'selected="selected"' : '' ; ?> >No</option>
							</select>
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Insurance Provider</label>
							<input name="insurance_provider" class="form-control" type="text" placeholder="Insurance Provider" value="<?php echo $vehicle_details->insurance_provider; ?>" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Has Road Assistance</label>
							<select name="has_road_assistance"  class="form-control">
								<option value="yes" <?php echo ( !empty( $vehicle_details->has_road_assistance ) && ( $vehicle_details->has_road_assistance == true ) ) ? 'selected = "selected"' : '' ; ?> >Yes</option>
								<option value="no" <?php echo ( empty( $vehicle_details->has_road_assistance ) || ( $vehicle_details->has_road_assistance != true ) ) ? 'selected = "selected"' : '' ; ?> >No</option>
							</select>
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">RA Provider Name</label>
							<input name="road_assistance_provider" class="form-control" type="text" placeholder="Road Assistance Provider Name" value="<?php echo ( !empty( $vehicle_details->road_assistance_provider ) ) ? $vehicle_details->road_assistance_provider : "" ; ?>" />
						</div>		
						
						<div class="input-group form-group">
							<label class="input-group-addon">Current Driver</label>
							<input class="form-control" type="text" placeholder="Current Driver" value="<?php echo $vehicle_details->driver_full_name; ?>" readonly="readonly" />
						</div>
						
						<?php /*
						<div class="input-group form-group">
							<label class="input-group-addon">Current Driver Start Date</label>
							<input name="driver_start_date" value="<?php echo ( !in_array( $vehicle_details->driver_start_date, array( '0000-00-00 00:00:00', '', '0000-00-00', '1970-01-01' ) ) ) ? date( 'd/m/Y', strtotime( $vehicle_details->driver_start_date ) ) : '' ; ?>" class="form-control datepicker" placeholder="<?php echo date( 'd/m/Y' ); ?>" data-date-format="DD/MM/Y" readonly="readonly" />
						</div>
						*/ ?>
						
						<div class="input-group form-group">
							<label class="input-group-addon">Has Camera Installed?</label>
							<select name="has_camera_installed"  class="form-control">
								<option value="yes" <?php echo ( !empty( $vehicle_details->has_camera_installed ) && ( $vehicle_details->has_camera_installed == true ) ) ? 'selected = "selected"' : '' ; ?> >Yes</option>
								<option value="no" <?php echo ( empty( $vehicle_details->has_camera_installed ) || ( $vehicle_details->has_camera_installed != true ) ) ? 'selected = "selected"' : '' ; ?> >No</option>
							</select>
						</div>
						
						<div class="input-group form-group">
							<label class="input-group-addon">Camera Install Date</label>
							<input name="camera_install_date" value="<?php echo ( validate_date( $vehicle_details->camera_install_date ) ) ? format_date_client( $vehicle_details->camera_install_date ) : '' ; ?>" class="form-control datepicker" placeholder="<?php echo date( 'd/m/Y' ); ?>" data-date-format="DD/MM/Y" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Hire Cost</label>
							<input name="hire_cost" class="form-control" type="text" placeholder="Hire Cost" value="<?php echo $vehicle_details->hire_cost; ?>" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Insurance Cost</label>
							<input name="insurance_cost" class="form-control" type="text" placeholder="Insurance Cost" value="<?php echo $vehicle_details->insurance_cost; ?>" />
						</div>
						<br/>

						<?php if( $this->user->is_admin || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
							<div class="row col-md-6 veh_details_action_btns">
								<button id="update-vehicle-btn" class="btn btn-sm btn-block btn-flow btn-success btn-next update-asset-btn" type="button">Update Vehicle Details</button>
							</div>
						<?php } else{ ?>
							<div class="row col-md-6 veh_details_action_btns">
								<button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>
							</div>
						<?php } ?>
						</form>

						<?php if( $this->user->is_admin || !empty( $permissions->can_delete ) || !empty( $permissions->is_admin ) ){ ?>
							<form class="col-md-6 pull-right"  method="post" name="deleteVehicleForm" id="deleteVehicleForm">
								<div class="col-md-12 pull-right">
									<input type="hidden" name="vehicle_id" value="<?php echo $vehicle_details->vehicle_id; ?>" />
									<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
									<button type="submit" class="btn btn-sm btn-block btn-flow btn-danger has-shadow red_shadow" id="deleteVehicleButton" data-vehicle_id="<?php echo $vehicle_details->vehicle_id; ?>" onclick="return confirm( 'Are you sure you want to delete this Vehicle?' );" <?php echo ( !empty( $profile_data[0]->archived ) && ( $profile_data[0]->archived == 1 ) ) ? 'disabled = "disabled"' : '' ; ?> >Delete Vehicle</button>
								</div>
							</form>
						<?php } ?>


					</div>
				</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		$( '.datepicker' ).datepicker({
			formatDate: 'd/m/Y',
			timepicker: false,
			format:'d/m/Y',
		});

		//Submit form for processing
		$( '#update-vehicle-btn' ).click( function( event ){
			event.preventDefault();
			var formData = $( '#update-vehicle-form' ).serialize();
			swal({
				title: 'Confirm vehicle update?',
				// type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function( result ) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url( 'webapp/fleet/update_vehicle/'.$vehicle_details->vehicle_id ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 500
								})
								window.setTimeout( function(){
									location.reload();
								},500 );
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}
						}
					});
				} else {
					console.log( 'fail' );
				}
			}).catch( swal.noop )
		});


		// Delete the Vehicle processing
		$( '#deleteVehicleButton' ).click( function( event ){
			event.preventDefault();
			var formData = $( '#deleteVehicleForm' ).serialize();
			swal({
				title: 'Confirm vehicle delete?',
				// type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function( result ) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url( 'webapp/fleet/delete_vehicle/'.$vehicle_details->vehicle_id ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 500
								})
								window.setTimeout( function(){
									location.href = "<?php echo base_url( 'webapp/fleet/' ); ?>";
								},500 );
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}
						}
					});
				} else {
					console.log( 'fail' );
				}
			}).catch( swal.noop )
		});



	});
</script>