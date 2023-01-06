<div class="row">
	<div class="col-md-6 col-sm-6 col-xs-12">
		<form id="update-customer-form" method="post" >
			<input type="hidden" name="customer_id" value="<?php echo $customer_details->customer_id; ?>" />
			<div class="x_panel tile has-shadow">
				<legend>Update Customer Details</legend>
				<div class="input-group form-group">
					<label class="input-group-addon">Salutation</label>
					<input name="salutation" class="form-control" type="text" placeholder="Salutation" value="<?php echo ( !empty( $customer_details->salutation ) ) ? $customer_details->salutation : '' ; ?>" />
				</div>

				<div class="input-group form-group">
					<label class="input-group-addon">First Name</label>
					<input name="customer_first_name" class="form-control" type="text" placeholder="First Name" value="<?php echo ( !empty( $customer_details->customer_first_name ) ) ? $customer_details->customer_first_name : '' ; ?>" />
				</div>

				<div class="input-group form-group">
					<label class="input-group-addon">Last Name</label>
					<input name="customer_last_name" class="form-control" type="text" placeholder="Last Name" value="<?php echo ( !empty( $customer_details->customer_last_name ) ) ? $customer_details->customer_last_name : '' ; ?>" />
				</div>

				<div class="input-group form-group">
					<label class="input-group-addon">Email</label>
					<input name="customer_email" class="form-control" type="text" placeholder="Email" value="<?php echo ( !empty( $customer_details->customer_email ) ) ? $customer_details->customer_email : '' ; ?>" />
				</div>

				<div class="input-group form-group">
					<label class="input-group-addon">Mobile Telephone</label>
					<input name="customer_mobile" class="form-control" type="text" placeholder="Mobile Phone" value="<?php echo ( !empty( $customer_details->customer_mobile ) ) ? $customer_details->customer_mobile : '' ; ?>" />
				</div>

				<div class="input-group form-group">
					<label class="input-group-addon">Main Telephone</label>
					<input name="customer_main_telephone" class="form-control" type="text" placeholder="Main Telephone" value="<?php echo ( !empty( $customer_details->customer_main_telephone ) ) ? $customer_details->customer_main_telephone : '' ; ?>" />
				</div>

				<div class="input-group form-group">
					<label class="input-group-addon">Work Telephone</label>
					<input name="customer_work_telephone" class="form-control" type="text" placeholder="Work Telephone" value="<?php echo ( !empty( $customer_details->customer_work_telephone ) ) ? $customer_details->customer_work_telephone : '' ; ?>" />
				</div>

				<div class="input-group form-group">
					<label class="input-group-addon">Customer Type</label>
					<select name="customer_type" class="form-control" title="Customer Type">
						<option value="">Please select</option>
						<option value="Commercial" <?php echo ( isset( $customer_details->customer_type ) && !empty( $customer_details->customer_type ) && strtolower( $customer_details->customer_type ) == 'commercial' ) ? 'selected="selected"' : '' ; ?>>Commercial</option>
						<option value="Private" <?php echo ( isset( $customer_details->customer_type ) && !empty( $customer_details->customer_type ) && strtolower( $customer_details->customer_type ) == 'private' ) ? 'selected="selected"' : '' ; ?>>Private</option>
					</select>
				</div>

				<div class="input-group form-group">
					<label class="input-group-addon">Associated Contract&nbsp;*</label>
					<select id="contract_id" name="contract_id" class="form-control" style="width:100%; display:none; margin-bottom:10px;" data-label_text="Associated Contract"  >	
						<option value="" >Please select contract</option>
						<?php if( !empty( $available_contracts ) ) { foreach( $available_contracts as $k => $contract ) { ?>
							<option value="<?php echo $contract->contract_id; ?>" <?php echo ( $customer_details->contract_id == $contract->contract_id ) ? 'selected=selected' : ''; ?> ><?php echo $contract->contract_name; ?></option>
						<?php } } ?>
					</select>
				</div>

				<?php /*
				<div class="input-group form-group">
					<label class="input-group-addon">Customer Reference</label>
					<input name="customer_reference" class="form-control" type="text" placeholder="Customer Reference" value="<?php echo ( !empty( $customer_details->customer_reference ) ) ? $customer_details->customer_reference : '' ; ?>" />
				</div>

				<div class="input-group form-group">
					<label class="input-group-addon">VAT Number</label>
					<input name="vat_number" class="form-control" type="text" placeholder="VAT Number" value="<?php echo ( !empty( $customer_details->vat_number ) ) ? $customer_details->vat_number : '' ; ?>" />
				</div>

				<div class="input-group form-group">
					<label class="input-group-addon">Company Number</label>
					<input name="company_number" class="form-control" type="text" placeholder="Company Number" value="<?php echo ( !empty( $customer_details->company_number ) ) ? $customer_details->company_number : '' ; ?>" />
				</div>

				<div class="input-group form-group">
					<label class="input-group-addon">Audit Result Status</label>
					<select name="audit_result_status_id" class="form-control">
						<option>Please select</option>
						<?php if( !empty( $audit_result_statuses ) ) { foreach( $audit_result_statuses as $k => $result_status ) { ?>
							<option value="<?php echo $result_status->audit_result_status_id; ?>" <?php echo ( $customer_details->audit_result_status_id == $result_status->audit_result_status_id ) ? 'selected=selected' : ''; ?> ><?php echo $result_status->result_status_alt; ?></option>
						<?php } } ?>
					</select>
				</div> */ ?>
				<br/>

				<?php if( $this->user->is_admin || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
					<div class="row">
						<div class="col-md-6">
							<button id="update-customer-btn" class="btn btn-block btn-flow btn-success btn-next" type="submit">Update Customer</button>
						</div>
						
						<?php /*if( $this->user->is_admin || !empty( $permissions->can_delete ) || !empty( $permissions->is_admin ) ){ ?>
							<div class="col-md-6">
								<button class="btn btn-sm btn-block btn-flow btn-danger has-shadow delete-customer-btn" type="button" data-customer_id="<?php echo $customer_details->customer_id; ?>">Delete Customer</button>
							</div>
						<?php }*/ ?>
						
					</div>
				<?php }else{ ?>
					<div class="row col-md-6">
						<button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled>Insufficient permissions</button>
					</div>
				<?php } ?>
			</div>
		</form>
	</div>

	<div class="col-md-6 col-sm-6 col-xs-12">
		<div class="row">
	<?php 	if( !empty( $customer_details->addresses ) ){ ## the customer address
				$business_address_found = false;
				foreach( $customer_details->addresses as $add_row ){
					if( $add_row->address_type_group == "main" ){ ?>
						<form id="update-customer-address-form" method="post" >
							<input type="hidden" name="customer_address_id" value="<?php echo $add_row->customer_address_id; ?>" />
							<input type="hidden" name="address_type_id" value="<?php echo $add_row->address_type_id; ?>" />
							<div class="x_panel tile has-shadow">
								<legend>Update Main Address Details</legend>
								<div class="input-group form-group">
									<label class="input-group-addon">Addresss Type</label>
									<select name="address_type_id" class="form-control" required><option value="">Please select</option>
							<?php	if( !empty( $address_types ) ){
										foreach( $address_types as $row ){ ?>
											<option value="<?php echo ( $row->address_type_id ); ?>" <?php echo ( !empty( $add_row->address_type_id ) && ( $add_row->address_type_id == $row->address_type_id ) ) ? 'selected="selected"' : '' ;?>><?php echo ( $row->address_type ); ?></option>
										<?php }
									} ?>
									</select>
								</div>

								<div class="input-group form-group">
									<label class="input-group-addon">Address First Name</label>
									<input name="address_contact_first_name" class="form-control" type="text" placeholder="Address First Name" value="<?php echo ( !empty( $add_row->address_contact_first_name ) ) ? html_escape( $add_row->address_contact_first_name ) : '' ; ?>" />
								</div>

								<div class="input-group form-group">
									<label class="input-group-addon">Address Last Name</label>
									<input name="address_contact_last_name" class="form-control" type="text" placeholder="Address Last Name" value="<?php echo ( !empty( $add_row->address_contact_last_name ) ) ? html_escape( $add_row->address_contact_last_name ) : '' ; ?>" />
								</div>

								<div class="input-group form-group">
									<label class="input-group-addon">Mobile</label>
									<input name="address_contact_number" class="form-control" type="text" placeholder="Contact Number" value="<?php echo ( !empty( $add_row->address_contact_number ) ) ? html_escape( $add_row->address_contact_number ) : '' ; ?>" />
								</div>

								<div class="input-group form-group">
									<label class="input-group-addon">Address Line 1</label>
									<input name="address_line1" class="form-control" type="text" placeholder="Address Line 1" value="<?php echo ( !empty( $add_row->address_line1 ) ) ? html_escape( $add_row->address_line1 ) : '' ; ?>" />
								</div>

								<div class="input-group form-group">
									<label class="input-group-addon">Address Line 2</label>
									<input name="address_line2" class="form-control" type="text" placeholder="Address Line 2" value="<?php echo ( !empty( $add_row->address_line2 ) ) ? html_escape( $add_row->address_line2 ) : '' ; ?>" />
								</div>

								<div class="input-group form-group">
									<label class="input-group-addon">Address Line 3</label>
									<input name="address_line3" class="form-control" type="text" placeholder="Address Line 3" value="<?php echo ( !empty( $add_row->address_line3 ) ) ? html_escape( $add_row->address_line3 ) : '' ; ?>" />
								</div>

								<div class="input-group form-group">
									<label class="input-group-addon">Address Town</label>
									<input name="address_town" class="form-control" type="text" placeholder="Address Town" value="<?php echo ( !empty( $add_row->address_town ) ) ? html_escape( $add_row->address_town ) : '' ; ?>" />
								</div>

								<div class="input-group form-group">
									<label class="input-group-addon">Address County</label>
									<input name="address_county" class="form-control" type="text" placeholder="Address County" value="<?php echo ( !empty( $add_row->address_county ) ) ? html_escape( $add_row->address_county ) : '' ; ?>" />
								</div>

								<div class="input-group form-group">
									<label class="input-group-addon">Address Postcode</label>
									<input name="address_postcode" class="form-control" type="text" placeholder="Postcode" value="<?php echo ( !empty( $add_row->address_postcode ) ) ? html_escape( $add_row->address_postcode ) : '' ; ?>" />
								</div>
								
								<div class="input-group form-group">
									<label class="input-group-addon">Address Note:</label>
									<textarea name="contact_note" class="form-control" type="text" placeholder="Address Note"><?php echo ( !empty( $add_row->contact_note ) ) ? html_escape( $add_row->contact_note ) : '' ; ?></textarea>
								</div>

							<?php if( $this->user->is_admin || !empty( $permissions->can_edit ) || !empty( $permissions->is_admin ) ){ ?>
								<div class="row">
									<div class="col-md-6">
										<button id="update-customer-address-btn" class="btn btn-block btn-flow btn-success btn-next" type="submit">Update Address</button>
									</div>
								</div>
							<?php } else { ?>
								<div class="row col-md-6">
									<button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled>Insufficient permissions</button>
								</div>
							<?php } ?>
							</div>
						</form>
			<?php 	}
				}
			} ?>
		</div>
	</div>
<script>
$ (document ).ready(function(){
	
	$( '#contract_id' ).select2({});

	$( '#update-customer-form' ).on( "submit", function( event ){
		event.preventDefault();
		var formData = $( '#update-customer-form' ).serialize();
		swal({
			title: 'Confirm customer update?',
			// type: 'question',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url('webapp/customer/update/' ); ?>",
					method: "POST",
					data:formData,
					dataType: 'json',
					success:function( data ){
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


	$( '#update-customer-address-form' ).on( "submit", function( event ){
		event.preventDefault();
		var formData = $( '#update-customer-address-form' ).serialize();

		swal({
			title: 'Confirm Address update?',
			// type: 'question',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'

		}).then( function (result) {
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url( 'webapp/customer/update_address/' ); ?>",
					method: "POST",
					data:formData,
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
								location.reload();
							}, 2000);
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