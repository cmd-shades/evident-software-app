<div class="row">
	<div class="col-md-4 col-sm-4 col-xs-12">
		<div class="x_panel tile has-shadow">
			<form id="assignDriver">
				<input type="hidden" name="action" value="<?php echo ( !empty( $vehicle_details->driver_id ) ) ? "unassign" : "assign" ; ?>" />
				<input type="hidden" name="page" value="details" />
				<input type="hidden" name="vehicle_id" value="<?php echo $vehicle_details->vehicle_id; ?>" />
				<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />

				<?php if( !empty( $vehicle_details->driver_id ) ){ ?>
					<legend>Unassign Current Driver</legend>
				<?php } else { ?>
					<legend>Assign New Driver</legend>
				<?php } ?>

				<div class="input-group form-group">
					<label class="input-group-addon">Driver Name</label>
					<?php if( !empty( $vehicle_details->driver_id ) ){ ?>
						<input type="hidden" name="driver_id" value="<?php echo $vehicle_details->driver_id; ?>" />
						<input type="text" value="<?php echo ( !empty( $vehicle_drivers->{$vehicle_details->driver_id} ) ) ? $vehicle_drivers->{ $vehicle_details->driver_id }->first_name.' '.$vehicle_drivers->{ $vehicle_details->driver_id }->last_name : 'Unknown Driver (ID:'.$vehicle_details->driver_id.')' ; ?>"  class="form-control" />
					<?php } else { ?>
						<select name="driver_id" class="form-control">
							<option value="">Please, select a new driver</option>
							<?php if( !empty( $vehicle_drivers ) ){
								foreach( $vehicle_drivers as $key => $row ){ ?>
									<option value="<?php echo $key; ?>" <?php echo ( ( !empty( $vehicle_details->driver_id ) ) && ( $vehicle_details->driver_id == $row->id ) ) ? "selected='selected'" : '' ; ?> ><?php echo ucfirst( $row->first_name ).' '.ucfirst( $row->last_name ); ?></option>
							<?php } } ?>
						</select>
					<?php } ?>
				</div>
				<?php if( !empty( $vehicle_details->driver_id ) ){ ?>
					<div class="input-group form-group">
						<label class="input-group-addon">Driver End Date</label>
						<input name="end_date" value="<?php echo date( 'd/m/Y H:i:s' ); ?>" class="form-control datetimepicker" placeholder="<?php echo date( 'd/m/Y H:i:s' ); ?>" data-date-format="DD/MM/Y" />
					</div>
				<?php } else { ?>
					<div class="input-group form-group">
						<label class="input-group-addon">Driver Start Date</label>
						<input name="nd_start_date" value="<?php echo date( 'd/m/Y H:i:s' ); ?>" class="form-control datetimepicker" placeholder="<?php echo date( 'd/m/Y H:i:s' ); ?>" data-date-format="DD/MM/Y" />
					</div>
				<?php } ?>

				<div class="input-group form-group">
					<label class="input-group-addon">Note:</label>
					<textarea name="note" type="text" cols="50" rows="5" class="form-control" id="note"></textarea>
				</div>

				<div class="input-group form-group">
					<label class="input-group-addon">Vehicle been audited?</label>
					<select class="form-control" name="been_audited" id="been_audited">
						<option value="yes" selected="selected">Yes</option>
						<option value="no">No</option>
					</select>
				</div>

				<div class="input-group form-group" id="been_audited_wrapper">
					<?php if( !empty( $vehicle_audits ) ){ ?>
						<div class="col-md-12">
							<h5>Pick the audit from the 5 most recent records:</h5>
						</div>
						<div class="col-md-12">
							<div class="row">
								<select class="form-control" name="audit_id" style="width: 100%;">
									<?php 
									foreach( array_slice( $vehicle_audits, 0, 5 ) as $row ){ ?>
										<option value="<?php echo $row->audit_id; ?>"><?php echo $row->alt_audit_type; ?> | <?php echo $row->date_created; ?> |  <?php echo $row->audit_status; ?> </option>
									<?php 
									} ?>
								</select>
							</div>
						</div>
					<?php } else { ?>
						<div class="no_audits_alert">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 alert alert-danger">There are no recent audits on this vehicle. Please, perform the audit before assigng the driver.</div>
						</div>
					<?php } ?>
				</div>

				<?php if( $this->user->is_admin || !empty( $permissions->can_add ) || !empty( $permissions->is_admin ) ){ ?>
					<div class="row">
						<div class="col-md-6">
							<?php
							if( !empty( $vehicle_details->driver_id ) ){ ?>
								<button id="unassign-driver-btn" class="btn btn-sm btn-block btn-flow btn-success btn-next" type="button" <?php echo ( empty( $vehicle_audits ) ) ? 'disabled="disabled" ': "" ; ?> >Unassign Driver</button>
							<?php
							} else { ?>
								<button id="assign-driver-btn" class="btn btn-sm btn-block btn-flow btn-success btn-next" type="button" <?php echo ( empty( $vehicle_audits ) ) ? 'disabled="disabled" ': "" ; ?>>Assign Driver</button>
							<?php
							} ?>
						</div>
					</div>
				<?php } else{ ?>
					<div class="row col-md-6">
						<button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>
					</div>
				<?php } ?>
			</form>
		</div>
	</div>
	<div class="col-md-8 col-sm-8 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Vehicle Driver History <small>(last 10 entries)</small></legend>
			<?php if( !empty( $driver_history ) ){ ?>
				<table style="width:100%">
					<thead>
						<tr>
							<th width="15%">Driver</th>
							<th width="14%">Driver Start Date</th>
							<th width="14%">Driver End Date</th>
							<th width="14%">Note</th>
						</tr>
					</thead>
					<?php if( !empty( $driver_history ) ){ foreach( $driver_history as $log_records ) { ?>
						<tr>
							<td><?php echo $log_records->driver_full_name; ?></td>
							<td><?php echo ( valid_date( $log_records->start_date ) ) ? date( 'd-m-Y H:i:s', strtotime( $log_records->start_date ) ) : ''; ?></td>
							<td><?php echo ( valid_date( $log_records->end_date ) ) ? date( 'd-m-Y H:i:s', strtotime( $log_records->end_date ) ) : ''; ?></td>
							<td><?php echo $log_records->note; ?></td>
						</tr>
					<?php } } ?>

				</table>
			<?php }else{ ?>
				<span><?php echo $this->config->item('no_records'); ?></span>
			<?php } ?>
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){
		$( '.datetimepicker' ).datetimepicker({
			formatDate: 'd/m/Y H:i:s',
			timepicker: true,
			format:'d/m/Y H:i:s',
		});


		$( "#been_audited" ).change( function( e ){
			e.preventDefault();
			var vehicle_audits = <?php echo ( !empty( $vehicle_audits ) ) ? "true" : "false" ; ?>;
			if( ( $( this ).val() == 'yes' ) && ( vehicle_audits == true ) ){
				$( "#unassign-driver-btn, #assign-driver-btn" ).prop( "disabled", false );
				$( "#been_audited_wrapper" ).show( "slow" );
			} else {
				alert( "You need to audit vehicle before assigning the driver" );
				$( "#unassign-driver-btn, #assign-driver-btn" ).attr( "disabled", true );
				$( "#been_audited_wrapper" ).hide( "slow" );
			}
		});

		$( '#unassign-driver-btn' ).click( function( event ){
			event.preventDefault();
			var formData = $( '#assignDriver' ).serialize();
			swal({
				title: 'Confirm removing the driver',
				// type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function( result ) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url( 'webapp/fleet/remove_driver/'.$vehicle_details->vehicle_id ); ?>",
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
							} else {
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

		$( '#assign-driver-btn' ).click( function( event ){
			event.preventDefault();

			var driver_id = $( "[name='driver_id']" ).val();
			if( driver_id == '' ){
				alert( 'Please, select the Driver' );
				return false;
			} 
			
			var formData = $( '#assignDriver' ).serialize();
			swal({
				title: 'Confirm assigning a new driver',
				// type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function( result ) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url( 'webapp/fleet/assign_driver/'.$vehicle_details->vehicle_id ); ?>",
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
	});
</script>