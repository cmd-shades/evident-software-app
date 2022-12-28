<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="profile-details-container">
				<div class="row">
					<div class="row">
						<div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12">
							<div class="x_panel tile has-shadow">
								<form id="create-address-form" class="form-horizontal">
									<legend>Unverified Address Details</legend>
									<input type="hidden" name="page" value="details" />
									
									<div class="input-group form-group">
										<label class="input-group-addon">Address Line 1</label>
										<input id="addressline1" name="addressline1" class="form-control required" type="text" placeholder="Address Line 1" value="" />
									</div>
									
									<div class="input-group form-group">
										<label class="input-group-addon">Address Line 2</label>
										<input id="addressline2" name="addressline2" class="form-control" type="text" placeholder="Address Line 2" value="" />
									</div>
									
									<div class="input-group form-group">
										<label class="input-group-addon">Address Line 3</label>
										<input id="addressline3" name="addressline3" class="form-control" type="text" placeholder="Address Line 3" value="" />
									</div>

									<div class="hide input-group form-group">
										<label class="input-group-addon">Street</label>
										<input id="street" name="street" class="form-control" type="text" placeholder="Street" value="" />
									</div>
			
									<div class="input-group form-group">
										<label class="input-group-addon">Address Town</label>
										<input name="posttown" class="form-control" type="text" placeholder="Address Town" value="" />
									</div>

									<div class="input-group form-group">
										<label class="input-group-addon">Address County</label>
										<input name="county" class="form-control" type="text" placeholder="Address County" value="" />
									</div>
									
									<div class="input-group form-group">
										<label class="input-group-addon">Postcode</label>
										<input id="postcode" name="postcode" class="form-control required" type="text" placeholder="Address Postcode" value="" />
									</div>

									<div class="input-group form-group">
										<label class="input-group-addon">Organisation</label>
										<input name="organisation" class="form-control" type="text" placeholder="Organisation" value="" />
									</div>

									<div class="input-group form-group">
										<label class="input-group-addon">Building Name</label>
										<input name="buildingname" class="form-control" type="text" placeholder="buildingname" value="" />
									</div>

									<div class="input-group form-group">
										<label class="input-group-addon">Sub Building Name</label>
										<input name="subbuildingname" class="form-control" type="text" placeholder="Sub Building Name" value="" />
									</div>

									<div class="row">
										<div class="col-md-6">
											<button class="btn btn-sm btn-block btn-flow btn-success btn-next" type="submit">Create Unverified Address</button>
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

<script type="text/javascript">

	$( document ).ready( function() {

		$( '.required' ).keyup( function(){
			$( this ).css("border","1px solid #ccc");
		});

		$( "form#create-address-form" ).submit( function( e ){
			e.preventDefault();
			
			var formData = $( this ).serialize();

			var addressLine1 	= $( '#addressline1' ).val();
			var addressPostcode	= $( '#postcode' ).val();

			if( addressLine1.length == 0 || addressLine1 == undefined ){
				$( '#addressline1' ).css( "border","1px solid red" );
				swal({
					type: 'error',
					title: 'Address line 1 is required!',
				});
				return false;
			}
			
			if( addressPostcode.length == 0 || addressPostcode == undefined ){
				$( '#postcode' ).css( "border","1px solid red" );
				swal({
					type: 'error',
					title: 'Address Postcode is required!',
				});
				return false;
			}

			swal({
				title: 'Confirm Unverified Address creation?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function( result ){
				if( result.value ) {
					$.ajax({
						url:"<?php echo base_url( 'webapp/config/add_unverified_address/' ); ?>",
						method: "POST",
						data: formData,
						dataType: 'json',
						success:function( data ){
							if( ( data.status == 1 ) && ( data.unverified_address.main_address_id !== '' ) ){
								var newAddressID = data.unverified_address.main_address_id;
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2000
								})
								window.setTimeout( function(){
									location.href = "<?php echo base_url( 'webapp/config/unverified_addresses/' ) ?>" + newAddressID;
								}, 1000);
							} else {
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
