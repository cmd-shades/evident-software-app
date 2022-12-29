<div>
	<legend>Create Customer Profile</legend>
	<form id="customer-creation-form" >
		<div class="row">
			<div class="customer_creation_panel1 col-md-6 col-sm-12 col-xs-12">
				<div class="x_panel tile has-shadow">
					<div class="row section-header">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<legend class="legend-header">Customer Details</legend>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<h6 class="error_message pull-right" style="display: block; color:red; font-weight:600" id="customer_creation_panel1-errors"></h6>
						</div>
					</div>

					<div class="input-group form-group">
						<label class="input-group-addon">Salutation</label>
						<input name="salutation" class="form-control" type="text" value="" placeholder="Salutation"  />
					</div>

					<div class="input-group form-group">
						<label class="input-group-addon">First Name&nbsp;*</label>
						<input name="customer_first_name" class="form-control required" type="text" value="" placeholder="First Name"  />
					</div>

					<div class="input-group form-group">
						<label class="input-group-addon">Last Name&nbsp;*</label>
						<input name="customer_last_name" class="form-control required" type="text" value="" placeholder="Last Name"  />
					</div>

					<div class="input-group form-group">
						<label class="input-group-addon">Business Name</label>
						<input name="business_name" class="form-control" type="text" value="" placeholder="Business Name"  />
					</div>

					<div class="input-group form-group">
						<label class="input-group-addon">Associated Contract&nbsp;*</label>
						<select id="contract_id" name="contract_id" class="required form-control" style="width:100%; display:none; margin-bottom:10px;" data-label_text="Associated Contract"  >	
							<option value="" >Please select contract</option>
							<?php if (!empty($available_contracts)) {
							    foreach ($available_contracts as $k => $contract) { ?>
								<option value="<?php echo $contract->contract_id; ?>" ><?php echo $contract->contract_name; ?></option>
							<?php }
							    } ?>
						</select>
					</div>

					<div class="row">
						<div class="col-md-offset-6 col-md-6 col-sm-offset-6 col-sm-6 col-xs-offset-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-success btn-next customer-creation-steps" data-currentpanel="customer_creation_panel1" type="button">Next</button>
						</div>
					</div>
				</div>
			</div>

			<div class="customer_creation_panel2 col-md-6 col-sm-12 col-xs-12" style="display:none">
				<div class="x_panel tile has-shadow">
					<div class="row section-header">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<legend class="legend-header">Customer Contact Details?</legend>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<h6 class="error_message pull-right" style="display: block; color:red; font-weight:600" id="customer_creation_panel2-errors"></h6>
						</div>
					</div>

					<div class="input-group form-group">
						<label class="input-group-addon">Customer Type</label>
						<select name="customer_type" class="form-control" title="Customer Type">
							<option value="">Please select</option>
							<option value="commercial">Commercial</option>
							<option value="private">Private</option>
						</select>
					</div>

					<div class="input-group form-group">
						<label class="input-group-addon">Business Email&nbsp;</label>
						<input name="customer_email" class="form-control" type="text" value="" placeholder="Business Email"  />
					</div>

					<div class="input-group form-group">
						<label class="input-group-addon">Main Telephone</label>
						<input name="customer_main_telephone" class="form-control" type="text" value="" placeholder="Main Telephone"  />
					</div>

					<div class="input-group form-group">
						<label class="input-group-addon">Work Telephone</label>
						<input name="customer_work_telephone" class="form-control" type="text" value="" placeholder="Work Telephone"  />
					</div>

					<div class="input-group form-group">
						<label class="input-group-addon">Mobile&nbsp;*</label>
						<input name="customer_mobile" class="form-control required" type="text" value="" placeholder="Mobile"  />
					</div>

					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="customer_creation_panel2" type="button">Back</button>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-success btn-next customer-creation-steps" data-currentpanel="customer_creation_panel2" type="button">Next</button>
						</div>
					</div>
				</div>
			</div>

			<div class="customer_creation_panel3 col-md-6 col-sm-12 col-xs-12" style="display:none">
				<div class="x_panel tile has-shadow">
					<div class="row section-header">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<legend class="legend-header">Customer Address details</legend>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<h6 class="error_message pull-right" style="display: block; color:red; font-weight:600" id="customer_creation_panel3-errors"></h6>
						</div>
					</div>

					<div class="form-group top_search">
						<div class="input-group">
							<input type="text" id="postcode_search" class="form-control address-lookup <?php echo $module_identier; ?>-search_input"  placeholder="Enter the address postcode..." >
							<span class="input-group-btn"><button id="find_address" class="btn btn-default <?php echo $module_identier; ?>-bg" type="button" >Find address</button></span>
						</div>
					</div>

					<div class="form-group address-selection" style="display:none" >
						<select id="address_lookup_result" name="address_id" class="form-control" style="width:100%; margin-bottom:20px;" ></select>
					</div>

					<div class="confirm-address" style="display:none">
						<div class="input-group form-group">
							<label class="input-group-addon">Contact First Name</label>
							<input name="customer_address[address_contact_first_name]" class="form-control" type="text" placeholder="Contact First Name" value="" />
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">Contact Last Name</label>
							<input name="customer_address[address_contact_last_name]" class="form-control" type="text" placeholder="Contact Last Name" value="" />
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">Mobile</label>
							<input name="customer_address[address_contact_number]" class="form-control" type="text" placeholder="Contact Number" value="" />
						</div>

						<div class="input-group form-group hide">
							<label class="input-group-addon">Address Type ID</label>
							<input name="customer_address[address_type_id]" class="form-control" type="text" placeholder="Address type ID" value="<?php echo (!empty($address_types->address_type_id)) ? $address_types->address_type_id : '' ; ?>" />
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">Address Line 1</label>
							<input name="customer_address[address_line1]" class="form-control required" type="text" placeholder="Address Line 1" value="" required />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Address Line 2</label>
							<input name="customer_address[address_line2]" class="form-control" type="text" placeholder="Address Line 2" value="" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Address Line 3</label>
							<input name="customer_address[address_line3]" class="form-control" type="text" placeholder="Address Line 3" value="" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Address Town</label>
							<input name="customer_address[address_town]" class="form-control required" type="text" placeholder="Address Town" value="" required />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Address County</label>
							<input name="customer_address[address_county]" class="form-control" type="text" placeholder="Address County" value="" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Postcode</label>
							<input name="customer_address[address_postcode]" class="form-control required" type="text" placeholder="Address Postcode" value="" required />
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="customer_creation_panel3" type="button">Back</button>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button id="create-customer-btn" class="btn btn-block btn-flow btn-success btn-next" data-currentpanel="customer_creation_panel3"  type="button">Create Customer Record</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<script>
	$(document).ready(function(){
		
		$( '#contract_id' ).select2({
			
		});

		$(".customer-creation-steps").click(function(){

			//Clear errors first
			$( '.error_message' ).each(function(){
				$( this ).text( '' );
			});

			var currentpanel = $(this).data("currentpanel");
			var inputs_state = check_inputs( currentpanel );
			if( inputs_state ){
				//If name attribute returned, auto focus to the field and display arror message
				$( '[name="'+inputs_state+'"]' ).focus();
				var labelText = $( '[name="'+inputs_state+'"]' ).parent().find('label').text();
				$( '#'+currentpanel+'-errors' ).text( ucwords( labelText ) +' is a required' );
				return false;
			}
			panelchange("."+currentpanel)
			return false;
		});

		//** Validate any inputs that have the required class, if empty return the name attribute **/
		function check_inputs( currentpanel ){

			var result = false;
			var panel  = "." + currentpanel;

			$( $( panel + " .required" ).get().reverse() ).each( function(){
				var fieldName  = '';
				var inputValue = $( this ).val();
				if( ( inputValue == false ) || ( inputValue == '' ) || ( inputValue.length == 0 ) ){
					fieldName = $(this).attr( 'name' );
					result    = fieldName;
					return result;
				}
			});
			return result;
		}

		$(".btn-back").click(function(){
			var currentpanel = $(this).data("currentpanel");
			go_back("."+currentpanel)
			return false;
		});

		function panelchange(changefrom){
			var panelnumber = parseInt( changefrom.match(/\d+/) )+parseInt(1);
			var changeto = ".customer_creation_panel"+panelnumber;
			$(changefrom).hide( "slide", {direction : 'left'}, 500);
			$(changeto).delay(600).show( "slide", {direction : 'right'},500);
			return false;
		}

		function go_back( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
			var changeto = ".customer_creation_panel"+panelnumber;
			$(changefrom).hide( "slide", {direction : 'right'}, 500);
			$(changeto).delay(600).show( "slide", {direction : 'left'},500);
			return false;
		}

		//SUBMIT SITE FORM
		$( '#create-customer-btn' ).on( "click", function( e ){
			e.preventDefault();
			
			var currentpanel = $( this ).data( "currentpanel" );
			var inputs_state = check_inputs( currentpanel );
			if( inputs_state ){
				//If name attribute returned, auto focus to the field and display error message
				$( '[name="'+inputs_state+'"]' ).focus();
				var labelText = $( '[name="'+inputs_state+'"]' ).parent().find('label').text();
				$( '#'+currentpanel+'-errors' ).text( ucwords( labelText ) +' is a required' );
				return false;
			}
			var formData = $( '#customer-creation-form' ).serialize();

			swal({
				title: 'Confirm new Customer creation?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function ( result ){
				if ( result.value ){
					$.ajax({
						url: "<?php echo base_url('webapp/customer/create_customer/'); ?>",
						method: "POST",
						data: formData,
						dataType: 'json',
						success: function( data ){
							if( data.status == 1 && ( data.customer !== '' ) ){

								var newCustomerId = data.customer.customer_id;

								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2000
								})
								window.setTimeout( function(){
									location.href = "<?php echo base_url('webapp/customer/profile/'); ?>" + newCustomerId;
								}, 2000 );
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}
						}
					});
				} else {
					$( ".customer_creation_panel4" ).hide( "slide", { direction : 'left' }, 500 );
					go_back( ".customer_creation_panel3" );
					return false;
				}
			}).catch( swal.noop )
		});


		//Trigger address search on btn click
		$( '#find_address' ).click(function(){
			var postCode = encodeURIComponent( $( '#postcode_search' ).val() );
			if( postCode.length > 0 ){
				$.post("<?php echo base_url("webapp/people/get_addresses_by_postcode"); ?>",{postcodes:postCode},function(result){
					$("#address_lookup_result").html( result["addresses_list"] );
					$( '.address-selection' ).slideDown( 'slow' );
				},"json");
			}
		});

		//Trigger address search on pressing-enter key
		$( '#postcode_search' ).keypress( function( e ){
			if( e.which == 13 ){
				$( '#find_address' ).click();
			}
		});

		//If user clears the postcode filed, clear the previously selected address
		$( '#postcode-search' ).change( function(){

			var searchTerm = $( this ).val();
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
		$( '#address_lookup_result' ).change( function(){
			
			var selected =$( 'option:selected', this ).text();
			if( selected != "Please select address..." ){

				var addrLine1 			= $('option:selected', this).data( 'addressline1' ),
					addrLine2 			= $('option:selected', this).data( 'addressline2' ),
					addrLine3 			= $('option:selected', this).data( 'addressline3' ),
					addrTown 			= $('option:selected', this).data( 'posttown' ),
					addrCounty 			= $('option:selected', this).data( 'county' ),
					addrPostcode		= $('option:selected', this).data( 'postcode' ).toUpperCase(),
					contactFirstName	= $('[name="customer_first_name"]').val(),
					contactLastName		= $('[name="customer_last_name"]').val();

				$( '[name="customer_address[address_line1]"]' ).val( addrLine1 );
				$( '[name="customer_address[address_line2]"]' ).val( addrLine2 );
				$( '[name="customer_address[address_line3]"]' ).val( addrLine3 );
				$( '[name="customer_address[address_town]"]' ).val( addrTown );
				$( '[name="customer_address[address_county]"]' ).val( addrCounty );
				$( '[name="customer_address[address_postcode]"]' ).val( addrPostcode );
				$( '[name="customer_address[address_contact_first_name]"]' ).val( contactFirstName );
				$( '[name="customer_address[address_contact_last_name]"]' ).val( contactLastName );
				$( '.confirm-address' ).slideDown( 'fast' );
			}
		});

		//If the address type selected is Residential i.e. for the current person's profile, pre-populate with person's details
		$( '#address_type_id' ).change( function(){
			var addrTypeId = $('option:selected', this).val();
			if( addrTypeId == '2' ){
				$( '[name="contact_first_name"]' ).val( '<?php ##  echo html_escape( $person_details->first_name );?>' );
				$( '[name="contact_last_name"]' ).val( '<?php ## echo html_escape( $person_details->last_name );?>' );
				$( '[name="contact_email"]' ).val( '<?php ## echo html_escape( $person_details->personal_email );?>' );
			}else{
				$( '[name="contact_first_name"]' ).val( '' );
				$( '[name="contact_last_name"]' ).val( '' );
				$( '[name="contact_email"]' ).val( '' );
			}
		});
	});
</script>