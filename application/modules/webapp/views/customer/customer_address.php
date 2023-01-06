<div class="row customer-addresses-container">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<div class="rows">
				<a class="btn btn-sm btn-flow btn-success btn-next pull-right no_right_margin" id="addNewlog">Add Address &nbsp;<i class="fas fa-chevron-down"></i></a>
				<legend>Customer Addresses</legend>
			</div>
			<div class="control-group form-group full_block">
			<?php if( !empty( $address_contacts ) ){ ?>
					<table style="width:100%; table-layout: fixed;">
						<tr>
							<th width="20%">Contact Name</th>
							<th width="16%">Address Type</th>
							<th width="32%">Address Summary</th>
							<th width="11%">Mobile</th>
							<th width="7%"><span class="pull-right">Action</span></th>
						</tr>
					</table>
				</div>
				<div class="control-group form-group table_body full_block">
					<table style="width:100%; table-layout: fixed;">
						<?php if( !empty( $address_contacts ) ){ foreach( $address_contacts as $contact ) { ?>
							<tr>
								<td width="20%"><?php echo ucwords( strtolower( $contact->address_contact_first_name.' '.$contact->address_contact_last_name ) ); ?></td>
								<td width="16%"><?php echo ( $contact->address_type ) ? $contact->address_type : ''; ?></td>
								<td width="32%"><?php echo ucwords( strtolower( $contact->address_line1) ).', '.strtoupper( $contact->address_postcode ); ?></td>
								<td width="11%"><a style="color:#324D6B; font-weight:500" href="tel:<?php echo ( $contact->address_contact_number ) ? $contact->address_contact_number : '' ; ?>"><?php echo ( $contact->address_contact_number ) ? $contact->address_contact_number : '' ; ?></a></td>
								<td width="7%">
									<span class="pull-right">
										<a class="edit pointer" data-customer_address_id="<?php echo $contact->customer_address_id; ?>" data-toggle="modal" data-target="#contact-record-modal-md" >
											<i class="fas fa-edit text-green"></i>
										</a> &nbsp;|&nbsp; 
										<a class="delete pointer" data-customer_address_id="<?php echo $contact->customer_address_id; ?>">
											<i class="fas fa-trash-alt text-red"></i>
										</a>
									</span>
								</td>
							</tr>
						<?php } } ?>
					</table>



			<?php } else { ?>
				<?php echo $this->config->item('no_records'); ?>
			<?php } ?>
			</div>
		</div>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="create-contact" style="display: none;">
			<form id="address-contact-add-form" class="form-horizontal address-contact-add-form">
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<input type="hidden" name="customer_id" value="<?php echo $customer_details->customer_id; ?>" />
						<div class="x_panel tile has-shadow">
							<legend>Add Address</legend>
							<!-- <legend class="hide legend-header">Please confirm addressee details</legend> -->
							<div class="input-group form-group">
								<label class="input-group-addon">Address type</label>
								<select id="address_type_id" name="address_type_id" class="form-control required" required>
									<option value="">Please select</option>
									<?php if( !empty( $address_types ) ) { foreach( $address_types as $k => $address_type ) { ?>
										<option value="<?php echo $address_type->address_type_id; ?>" ><?php echo $address_type->address_type; ?></option>
									<?php } } ?>
								</select>
							</div>
							
							<div class="input-group form-group">
								<label class="input-group-addon">Contact First Name&nbsp*</label>
								<input name="address_contact_first_name" class="form-control required" type="text" placeholder="Contact First name" value=""  required />
							</div>
							
							<div class="input-group form-group">
								<label class="input-group-addon">Contact Last Name&nbsp*</label>
								<input name="address_contact_last_name" class="form-control required" type="text" placeholder="Contact Last name" value=""  required />
							</div>
							
							<div class="input-group form-group">
								<label class="input-group-addon">Contact Phone Number</label>
								<input name="address_contact_number" class="form-control" type="text" placeholder="Contact Phone number" value="" />
							</div>
							
							<div class="input-group form-group">
								<label class="input-group-addon">Contact Note</label>
								<textarea name="contact_note" class="form-control" placeholder="Contact Note" rows="3"></textarea>
							</div>
						</div>
					</div>
				

					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="x_panel tile has-shadow create_address_right_box">
							<legend class="hide legend-header">Please confirm the address</legend>
							<div class="hide form-group">
								<input class="form-control" type="text" placeholder="Enter the address postcode..." value="" />
							</div>
							<div class="form-group top_search">
								<div class="input-group">
									<input type="text" id="postcode-search" class="form-control address-lookup <?php echo $module_identier; ?>-search_input"  placeholder="Enter the address postcode..." >
									<span class="input-group-btn"><button id="find-address" class="btn btn-default <?php echo $module_identier; ?>-bg" type="button" >Find address</button></span>
								</div>
							</div>
							<select id="address-lookup-result" name="address_id" class="form-control" ></select>
							<br/>
							<div class="confirm-address" style="display:none">
								<div class="input-group form-group">
									<label class="input-group-addon">Address Line 1</label>
									<input name="address_line1" class="form-control" type="text" placeholder="Address Line 1" value="" />
								</div>
								<div class="input-group form-group">
									<label class="input-group-addon">Address Line 2</label>
									<input name="address_line2" class="form-control" type="text" placeholder="Address Line 2" value="" />
								</div>
								<div class="hide input-group form-group">
									<label class="input-group-addon">Address Line 3</label>
									<input name="address_line3" class="form-control" type="text" placeholder="Address Line 3" value="" />
								</div>
								<div class="input-group form-group">
									<label class="input-group-addon">Address Town</label>
									<input name="address_town" class="form-control" type="text" placeholder="Address Town" value="" />
								</div>
								<div class="input-group form-group">
									<label class="input-group-addon">Address County</label>
									<input name="address_county" class="form-control" type="text" placeholder="Address County" value="" />
								</div>
								<div class="input-group form-group">
									<label class="input-group-addon">Postcode</label>
									<input name="address_postcode" class="form-control" type="text" placeholder="Address Postcode" value="" />
								</div>
							</div>
							<div class="add_address_row">
								<?php if( $this->user->is_admin || !empty( $permissions->can_add ) || !empty( $permissions->is_admin ) ){ ?>
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<button id="create-contact-btn" class="btn btn-block btn-flow btn-success btn-next" type="button">Add Contact / Address</button>
									</div>
								<?php } else { ?>
										<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
											<button class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled>No permissions</button>
										</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
		<div class="clear"></div>
	</div>
</div>


<div class="modal fade contact-record-modal-md" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title" id="myModalLabel">Update Address Contact Details</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-12" id="ajax_contact">
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
		
		$( "#ajax_contact" ).on( "click", "#updateAddressBtn", function( e ){
			e.preventDefault();
			var formData = $( "#contact_update_in_modal" ).serialize();
			swal({
				title: 'Confirm Address Update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function ( result ) {
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
								}, 3000);
							} else {
								swal({
									type: 'error',
									title: data.status_msg
								})
							}
						}
					});
				} else {
					return false;
				}
			}).catch( swal.noop )
		})

		$( "#addNewlog" ).on( "click", function(){
			$( ".table_body" ).slideToggle( 1000 );
			$( ".create-contact" ).slideToggle( 1000 );
			$( this ).children( '.fas' ).toggleClass( 'fa-chevron-down fa-chevron-up' );
		});

		//Trigger address search on btn click
		$( '#find-address' ).click(function(){
			var postCode = encodeURIComponent( $( '#postcode-search' ).val() );
			if( postCode.length > 0 ){
				$.post("<?php echo base_url("webapp/people/get_addresses_by_postcode"); ?>",{postcodes:postCode},function(result){
					$("#address-lookup-result").html(result["addresses_list"]);
				},"json");
			}
		});

		//Trigger address search on pressing-enter key
		$( '#postcode-search' ).keypress( function( e ){
			if( e.which == 13 ){
				$('#find-address').click();
			}
		});

		//If user clears the postcode filed, clear the previously selected address
		$( '#postcode-search' ).change( function(){

			var searchTerm = $( this ).val();
			console.log( searchTerm );
			if( searchTerm == null || searchTerm.length == 0 ||  searchTerm == '' ){
				$( '.confirm-address' ).slideUp( 'fast' );
				$( '[name="address_line1"]' ).val( '' );
				$( '[name="address_line2"]' ).val( '' );
				$( '[name="address_line3"]' ).val( '' );
				$( '[name="address_town"]' ).val( '' );
				$( '[name="address_county"]' ).val( '' );
				$( '[name="address_postcode"]' ).val( '' );
				$("#address-lookup-result").html( '' );
			}
		});

		//Show and populate the address fields when an address is selected
		$( '#address-lookup-result' ).change( function(){

			var addrLine1 	= $('option:selected', this).data( 'addressline1' ),
				addrLine2 	= $('option:selected', this).data( 'addressline2' ),
				addrLine3 	= $('option:selected', this).data( 'addressline3' ),
				addrTown 	= $('option:selected', this).data( 'posttown' ),
				addrCounty 	= $('option:selected', this).data( 'county' );
				addrPostcode= $('option:selected', this).data( 'postcode' ).toUpperCase();

			$( '[name="address_line1"]' ).val( addrLine1 );
			$( '[name="address_line2"]' ).val( addrLine2 );
			//$( '[name="address_line3"]' ).val( addrLine3 );
			$( '[name="address_town"]' ).val( addrTown );
			$( '[name="address_county"]' ).val( addrCounty );
			$( '[name="address_postcode"]' ).val( addrPostcode );

			$( '.confirm-address' ).slideDown( 'fast' );

		});

		
		$( '#address_type_id' ).change( function(){
			var addrTypeId = $('option:selected', this).val();
			if( addrTypeId == '2' ){
				$( '[name="contact_first_name"]' ).val( '<?php echo ( !empty( $person_details->first_name ) ) ? $person_details->first_name : '' ; ?>' );
				$( '[name="contact_last_name"]' ).val( '<?php echo ( !empty( $person_details->last_name ) ) ? $person_details->last_name : '' ; ?>' );
				$( '[name="contact_email"]' ).val( '<?php echo ( !empty( $person_details->personal_email ) ) ? $person_details->personal_email : '' ; ?>' );
			}else{
				$( '[name="contact_first_name"]' ).val( '' );
				$( '[name="contact_last_name"]' ).val( '' );
				$( '[name="contact_email"]' ).val( '' );
			}
		});


		$(".btn-back").click(function(){
			var currentpanel = $(this).data("currentpanel");
			go_back("."+currentpanel)
			return false;
		});
		
		//** Validate any inputs that have the required class, if empty return the name attribute **/
		function check_inputs2( containerClass ){
			var result = [];
			var panel  = "." + containerClass;
			
			$( panel + " .required" ).each( function(){
				var fieldName  = '';
				var inputValue = $( this ).val();
				fieldName = $( this ).attr( 'name' );

				$( '[name="' + fieldName + '"]' ).css( "border", "1px solid #ccc" );
				if( ( inputValue == false ) || ( inputValue == '' ) || ( inputValue.length == 0 ) ){
					$( '[name="' + fieldName + '"]' ).css( "border", "2px solid red" );
					result.push( fieldName );
				}
			});
			
			if( result.length > 0 ){
				swal({
					type: 'error',
					title: "Required value(s) are missing:",
					confirmButtonText: 'Ok',
					confirmButtonColor: '#5CB85C',
				});
			}
			
			return result;
		}
		
		
		//Submit form for address creation
		$( '#create-contact-btn' ).click(function( e ){
			
			var inputs_state = check_inputs2( 'address-contact-add-form' );
		
			if( inputs_state.length > 0 ){
				return false;
			}
			
			e.preventDefault();
			var formData = $('#address-contact-add-form').serialize();

			swal({
				title: 'Confirm create new contact?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/customer/create_address/' ); ?>",
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
				} else {
					return false;
				}
			}).catch(swal.noop)
		});

		$( '.edit' ).click( function(){

			//Trigger modal
			$( ".contact-record-modal-md" ).modal( "show" );
			
			var customer_address_id = $( this ).data( "customer_address_id" );
			
			$.ajax({
				url: "<?php echo base_url( 'webapp/customer/get_address_details/' ); ?>",
				method:"POST",
				data:{ 
					customer_address_id: customer_address_id,
					customer_id: <?php echo ( !empty( $customer_details->customer_id ) ) ? $customer_details->customer_id : '""' ; ?>,
				},
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 ){
						$( "#ajax_contact" ).html( data.address_contacts );
					} else {

					}
				}
			});			
		});

		$( '.delete' ).click( function(){
			var customer_address_id = $( this ).data( "customer_address_id" );
			
			swal({
				title: 'Confirm Delete Address?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function( result ) {
				if ( result.value ) {
					$.ajax({
						url: "<?php echo base_url( 'webapp/customer/delete_address/' ); ?>",
						method: "POST",
						data:{
							customer_address_id: customer_address_id,
							customer_id: <?php echo ( !empty( $customer_details->customer_id ) ) ? $customer_details->customer_id : '""' ; ?>
						},
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
								}, 3000 );
							} else {
								swal({
									type: 'error',
									title: data.status_msg
								})
							}
						}
					});
				} else {
					return false;
				}
			}).catch( swal.noop )
		});
	});
</script>



	<div class="col-md-6 col-sm-6 col-xs-12" style="display: none;">
		<div class="x_panel tile has-shadow">
			<legend>Customer Location</legend>
			<table style="width:100%">
				<tr>
					<th width="50%">Customer Postcode</th><td width="50%"><?php echo ( !empty( $customer_details->site_postcodes ) ) ? $customer_details->site_postcodes : "" ; ?></td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="full-width">
							<iframe width="100%" height="280" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%&height=280&hl=en&q=<?php echo ( !empty( $customer_details->site_postcodes ) ) ? str_replace( " ", "+", $customer_details->site_postcodes ) : "" ; ?>&ie=UTF8&t=&z=16&iwloc=B&output=embed"></iframe>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>