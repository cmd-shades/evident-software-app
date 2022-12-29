<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="profile-details-container">
				<div class="row alert alert-ssid bg-blue" role="alert">
					<div class="profile-overview">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<legend>Unverified Address Profile <span class="pull-right hide"><span class="edit-stock-item pointer hide" title="Click to edit this Item profile"><i class="fas fa-pencil-alt"></i></span> &nbsp; <span class="---delete-stock-item pointer" title="Click to delete this Item profile" ><i class="far fa-trash-alt"></i></span></span></legend>
							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Date Created</label></td>
											<td width="85%"><?php echo (valid_date($unverified_address_details->datecreated)) ? date('d-m-Y H:i:s', strtotime($unverified_address_details->datecreated)) : ''; ?></td>
										</tr>
									</table>
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Status</label></td>
											<td width="85%"><?php echo ($unverified_address_details->verified == 1) ? 'Verified Address <i class="far fa-check-circle"></i>' : 'Un Verified Address <i class="far fa-times-circle text-red"></i>'; ?></td>
										</tr>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<div class="x_panel tile has-shadow">
								<form id="update-job-form" class="form-horizontal">
									<input type="hidden" name="page" value="details" />
									<input type="hidden" name="main_address_id" value="<?php echo $unverified_address_details->main_address_id; ?>" />
									<legend>Unverified Address Details</legend>
									
									<input type="hidden" name="page" value="details" />
									
									<div class="input-group form-group">
										<label class="input-group-addon">Unique Reference</label>
										<input readonly class="form-control required" type="text" placeholder="Address Unique Reference" value="<?php echo $unverified_address_details->uniquereference; ?>" />
									</div>
									
									<div class="input-group form-group">
										<label class="input-group-addon">Address Line 1</label>
										<input id="addressline1" name="addressline1" class="form-control" type="text" placeholder="Address Line 1" value="<?php echo $unverified_address_details->addressline1; ?>" />
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Address Line 2</label>
										<input id="addressline2" name="addressline2" class="form-control" type="text" placeholder="Address Line 2" value="<?php echo $unverified_address_details->addressline2; ?>" />
									</div>
									
									<div class="input-group form-group">
										<label class="input-group-addon">Address Line 3</label>
										<input id="addressline3" name="addressline3" class="form-control" type="text" placeholder="Address Line 3" value="<?php echo $unverified_address_details->addressline3; ?>" />
									</div>

									<div class="hide input-group form-group">
										<label class="input-group-addon">Street</label>
										<input id="street" name="street" class="form-control" type="text" placeholder="Street" value="<?php echo $unverified_address_details->street; ?>" />
									</div>
			
									<div class="input-group form-group">
										<label class="input-group-addon">Address Town</label>
										<input name="posttown" class="form-control" type="text" placeholder="Address Town" value="<?php echo $unverified_address_details->posttown; ?>" />
									</div>

									<div class="input-group form-group">
										<label class="input-group-addon">Address County</label>
										<input name="county" class="form-control" type="text" placeholder="Address County" value="<?php echo $unverified_address_details->county; ?>" />
									</div>
									
									<div class="input-group form-group">
										<label class="input-group-addon">Postcode</label>
										<input id="postcode" name="postcode" class="form-control required" type="text" placeholder="Address Postcode" value="<?php echo $unverified_address_details->postcode; ?>" />
									</div>

									<div class="input-group form-group">
										<label class="input-group-addon">Organisation</label>
										<input name="organisation" class="form-control" type="text" placeholder="Organisation" value="<?php echo $unverified_address_details->organisation; ?>" />
									</div>

									<div class="input-group form-group">
										<label class="input-group-addon">Building Name</label>
										<input name="buildingname" class="form-control" type="text" placeholder="buildingname" value="<?php echo $unverified_address_details->buildingname; ?>" />
									</div>

									<div class="input-group form-group">
										<label class="input-group-addon">Sub Building Name</label>
										<input name="subbuildingname" class="form-control" type="text" placeholder="Sub Building Name" value="<?php echo $unverified_address_details->subbuildingname; ?>" />
									</div>

									
									<div class="form-group">
										<hr>
										<div class="row" >
											<div class="col-md-6 col-sm-6 col-xs-12">
												<button type="button" class="update-stock-item-btn btn btn-sm btn-success" >Update Unverified Address</button>
												<?php if ($this->user->is_admin || !empty($permissions->can_delete) || !empty($permissions->is_admin)) { ?>
													<button class="btn btn-sm btn-danger has-shadow delete-stock-item-btn" type="button" data-main_address_id="<?php echo $unverified_address_details->main_address_id; ?>">Delete Unverified Address</button>
												<?php } ?>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){

		$( '.update-stock-item-btn' ).click( function( event ){

			var formID = $( this ).closest( 'form' ).attr( 'id' );
			event.preventDefault();
			var formData = $('#'+formID ).serialize();
			swal({
				title: 'Confirm Unverified Address update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/config/update_unverified_address/'.$unverified_address_details->main_address_id); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2000
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


		//Delete Unverified Address from
		$('.delete-stock-item-btn').click(function(){

			var mainAddressId = $(this).data( 'main_address_id' );
			swal({
				title: 'Confirm delete Unverified Address?',
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/config/delete_unverified_address/'.$unverified_address_details->main_address_id); ?>",
						method:"POST",
						data:{'page':'details', main_address_id:mainAddressId},
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 1500
								})
								window.setTimeout(function(){
									window.location.href = "<?php echo base_url('webapp/config/unverified_addresses'); ?>";
								} ,1500);
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}
						}
					});
				}
			}).catch( swal.noop )
		});
	});
</script>