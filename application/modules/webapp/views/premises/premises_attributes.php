<div class="row">
	
	<div class="col-md-6 col-sm-6 col-xs-12">
		<form id="update-asset-form-left" class="form-horizontal">
			<input type="hidden" name="page" value="attributes" />
			<input type="hidden" name="asset_id" value="<?php echo $asset_details->asset_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<div class="x_panel tile has-shadow">
				<legend>Asset Attributes</legend>

				<!-- Common Fields -->
				<div class="input-group form-group">
					<label class="input-group-addon">Last Audit Date</label>
					<input name="last_audit_date" class="form-control" type="text" placeholder="Last Audit Date" value="<?php echo ( !empty( $asset_details->last_audit_date ) ) ? ( ( valid_date( $asset_details->last_audit_date ) ) ? date( 'd/m/Y', strtotime( $asset_details->last_audit_date ) ) : '' ) : ''; ?>" readonly />					
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Next Audit Date</label>
					<input name="next_audit_date" class="form-control datepicker" type="text" placeholder="Next Audit Date" value="<?php echo ( !empty( $asset_details->next_audit_date ) ) ? ( ( valid_date( $asset_details->next_audit_date ) ) ? date( 'd/m/Y', strtotime( $asset_details->next_audit_date ) ) : '' ) : ''; ?>" />
				</div>

				<!-- PPE and All non-mobile Devices Attributes -->
				<div class="input-group form-group">
					<label class="input-group-addon">End of Life Date</label>
					<input name="end_of_life_date" class="form-control datepicker" type="text" placeholder="End of Life Date" value="<?php echo ( !empty( $asset_details->end_of_life_date ) ) ? ( ( valid_date( $asset_details->end_of_life_date ) ) ? date( 'd/m/Y', strtotime( $asset_details->end_of_life_date ) ) : '' ) : ''; ?>" />
				</div>
			
				<div class="input-group form-group">
					<label class="input-group-addon">Purchase Date</label>
					<input name="purchase_date" class="form-control datepicker" type="text" placeholder="Purchase Date" value="<?php echo ( !empty( $asset_details->purchase_date ) ) ? ( ( valid_date( $asset_details->purchase_date ) ) ? date( 'd/m/Y', strtotime( $asset_details->purchase_date ) ) : '' ) : ''; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Purchase Price</label>
					<input name="purchase_price" class="form-control" type="text" placeholder="Purchase Price" value="<?php echo ( !empty( $asset_details->purchase_price ) ) ? number_format( $asset_details->purchase_price, 2 ) : ''; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Lease price <small>(£)</small></label>
					<input name="lease_price" class="form-control" type="text" placeholder="if applicable" value="<?php echo ( !empty( $asset_details->lease_price ) ) ? number_format( $asset_details->lease_price, 2 ) : ''; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Charge frequency</label>
					<select id="charge_frequency" name="charge_frequency" class="form-control">
						<option>Please select</option>
						<option value="One off" <?php echo ( !empty( $asset_details->charge_frequency ) && strtolower( $asset_details->charge_frequency ) == 'one off' ) ? 'selected=selected' : ''; ?> >One off</option>
						<option value="Weekly" <?php echo ( !empty( $asset_details->charge_frequency ) && strtolower( $asset_details->charge_frequency ) == 'weekly' ) ? 'selected=selected' : ''; ?> >Weekly</option>
						<option value="Monthly" <?php echo ( !empty( $asset_details->charge_frequency ) && strtolower( $asset_details->charge_frequency ) == 'monthly' ) ? 'selected=selected' : ''; ?> >Monthly</option>
						<option value="Annually" <?php echo ( !empty( $asset_details->charge_frequency ) && strtolower( $asset_details->charge_frequency == 'annually' ) ) ? 'selected=selected' : ''; ?> >Annually</option>
					</select>
				</div>

				<?php if( !in_array( $asset_details->asset_group, ['comm device'] ) ){  ?>
					<div class="form-group">
						<textarea name="asset_notes" class="form-control" type="text" value="" style="width:100%;height:122px" placeholder="<?php echo ( !empty($asset_details->asset_notes) ) ? 'Last note: '.$asset_details->asset_notes : 'Action notes...' ?>"></textarea>
					</div>
				<?php }  ?>

				<?php if( $this->user->is_admin || !empty( $permissions->can_can ) || !empty( $permissions->is_admin ) ){ ?>
					<div class="row">
						<div class="col-md-6">
							<button id="update-asset-btn-2" class="btn btn-sm btn-block btn-flow btn-success btn-next update-asset-btn" type="button" >Update Asset Attributes</button>
						</div>
					</div>
				<?php }else{ ?>
					<div class="row col-md-6">
						<button id="no-permissions-2" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>
					</div>
				<?php } ?>
			</div>
		</form>
	</div>

	<div class="col-md-6 col-sm-6 col-xs-12" style="display:<?php echo ( !in_array( $asset_details->asset_group, ['comm device'] ) ) ? 'none' : 'block'; ?>" >
		<form id="update-asset-form-right" class="form-horizontal">
			<input type="hidden" name="page" value="attributes" />
			<input type="hidden" name="asset_id" value="<?php echo $asset_details->asset_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<div class="x_panel tile has-shadow">
				<legend>Update Asset Attributes</legend>

				<!-- Mobile Devices Attributes -->
				<div class="input-group form-group">
					<label class="input-group-addon">Network Provider</label>
					<input name="network_provider" class="form-control" type="text" placeholder="Network Provider" value="<?php echo ( !empty( $asset_details->network_provider ) ) ? $asset_details->network_provider : ''; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Phone Number</label>
					<input name="phone_number" class="form-control" type="text" placeholder="Phone Number" value="<?php echo ( !empty( $asset_details->phone_number ) ) ? $asset_details->phone_number : ''; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Call Allowance (minutes)</label>
					<input name="call_allowance" class="form-control" type="text" placeholder="Call Allowance" value="<?php echo ( !empty( $asset_details->call_allowance ) ) ? $asset_details->call_allowance : ''; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Data Allowance (GB)</label>
					<input name="data_allowance" class="form-control" type="text" placeholder="Data Allowance" value="<?php echo ( !empty( $asset_details->data_allowance ) ) ? $asset_details->data_allowance : ''; ?>" />
				</div>
				<?php if( in_array( $asset_details->asset_group, ['comm device'] ) ){  ?>
					<div class="form-group">
						<textarea name="asset_notes" class="form-control" type="text" value="" style="width:100%;height:122px" placeholder="<?php echo ( !empty($asset_details->asset_notes) ) ? 'Last note: '.$asset_details->asset_notes : 'Action notes...' ?>"></textarea>
					</div>
				<?php }  ?>

				<?php if( $this->user->is_admin || !empty( $permissions->can_can ) || !empty( $permissions->is_admin ) ){ ?>
					<div class="row">
						<div class="col-md-6">
							<button id="update-asset-btn-1" class="btn btn-sm btn-block btn-flow btn-success btn-next update-asset-btn" type="button" >Update Asset Attributes</button>
						</div>
					</div>
				<?php }else{ ?>
					<div class="row col-md-6">
						<button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>
					</div>
				<?php } ?>
			</div>
		</form>
	</div>

</div>

<script>
	$(document).ready(function(){

		$('#new-assignee').change(function(){
			$('#assignee').val( $(this).val() );
			$('#asset_status option[value="2"]').prop('selected','selected');
			$('#location_id option[value="2"]').prop('selected','selected');
		});

		//Re-assign the location based on selected status
		$('#asset_status').change(function(){
			var assetStatus = $(this).val();
			if( assetStatus == 1 ){
				//Put it back in office if unassigned
				$('#location_id option[value="1"]').prop('selected','selected');
				$('#new-assignee option[value=""]').prop('selected','selected');
			}else if( assetStatus == 2 ){
				$('#location_id option[value="2"]').prop('selected','selected');
			}else if( assetStatus == 4 ){
				$('#location_id option[value="4"]').prop('selected','selected');
			}else if( assetStatus == 5 ){
				$('#location_id option[value="5"]').prop('selected','selected');
			}
		});

		//Submit form for processing
		$( '.update-asset-btn' ).click( function( event ){

			event.preventDefault();
			//var formData = $('#update-asset-form').serialize();
			var formData = $(this).closest('form').serialize();
			swal({
				title: 'Confirm asset update?',
				// type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/asset/update_asset/'.$asset_details->asset_id ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2500
								})
								window.setTimeout(function(){
									location.reload();
								} ,1000);
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

		$('#delete-asset-btn').click(function(){

			var assetId = $(this).data( 'asset_id' );

			swal({
				title: 'Confirm clear attributes?',
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/asset/delete_asset_attributes/'.$asset_details->asset_id ); ?>",
						method:"POST",
						data:{asset_id:assetId},
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
