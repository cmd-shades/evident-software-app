<!-- Run the admin check if a Tab requires that you're admin to view -->
<?php if (!empty($admin_no_access)) {
    $this->load->view('errors/access-denied', false, false, true);
} else { ?>

<div class="row">
	<?php if ($this->user->is_admin || !empty($permissions->can_add) || !empty($permissions->can_edit) || !empty($permissions->is_admin)) { ?>
	<div class="col-md-5 col-sm-5 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Current Address / Contacts</legend>
			<form id="address-contact-add-form" class="form-horizontal" >
				<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
				<input type="hidden" name="person_id" value="<?php echo $person_details->person_id; ?>" />
				<input type="hidden" name="page" value="contacts"/>
				<div class="row">
					<div class="asset_creation_panel1 col-md-12 col-sm-12 col-xs-12">
						<legend class="hide legend-header">Please confirm addressee details</legend>
						<div class="input-group form-group">
							<label class="input-group-addon" >Address type</label>
							<select id="address_type_id" name="address_type_id" class="form-control" >
								<option>Please select</option>
								<?php if (!empty($address_types)) {
								    foreach ($address_types as $k => $address_type) { ?>
									<option value="<?php echo $address_type->address_type_id; ?>" data-address_type_group="<?php echo (!empty($address_type->address_type_group)) ? $address_type->address_type_group : "" ; ?>" ><?php echo $address_type->address_type; ?></option>
								<?php }
								    } ?>
							</select>	
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Addressee First name</label>
							<input name="contact_first_name" class="form-control" type="text" placeholder="Addressee First name" value="" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Addressee Last name</label>
							<input name="contact_last_name" class="form-control" type="text" placeholder="Addressee Last name" value="" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Mobile</label>
							<input name="contact_mobile" class="form-control" type="text" placeholder="Mobile" value="" />
						</div>
						<div class="hide input-group form-group">
							<label class="input-group-addon">Telephone</label>
							<input name="contact_number" class="form-control" type="text" placeholder="Telephone" value="" />
						</div>
						<div class="input-group form-group">
							<label class="input-group-addon">Email</label>
							<input name="contact_email" class="form-control" type="email" placeholder="Email address" value="" />
						</div>
						<div class="input-group form-group">						
							<label class="input-group-addon">Relationship to you</label>
							<select id="relationship" name="relationship" class="form-control" >
								<option>Please select</option>
								<?php if (!empty($relationships)) {
								    foreach ($relationships as $k => $relationship) { ?>
									<option value="<?php echo urlencode($k); ?>" ><?php echo $relationship; ?></option>
								<?php }
								    } ?>
							</select>
						</div>
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12 pull-right">
								<button class="btn btn-block btn-flow btn-success btn-next asset-creation-steps" data-currentpanel="asset_creation_panel1" type="button">Next</button>					
							</div>
						</div>
					</div>
					<div class="clear"></div>
					<div class="asset_creation_panel2 col-md-12 col-sm-12 col-xs-12" style="display:none">
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
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-warning btn-back" data-currentpanel="asset_creation_panel2" type="button" >Back</button>					
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button id="create-contact-btn" class="btn btn-block btn-flow btn-success btn-next" type="button" >Add Address</button>					
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
		<div class="clear"></div>
	</div>
	<?php } ?>
	
	<?php if ($this->user->is_admin || !empty($permissions->can_view) || !empty($permissions->is_admin)) { ?>
	<div class="col-md-7 col-sm-7 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Current Address / Contacts</legend>
			<?php if (!empty($address_contacts)) { ?>
				<table style="width:100%">
					<tr>
						<th width="20%">Contact Name</th>
						<th width="14%">Relationship</th>
						<th width="16%">Address Type</th>
						<th width="32%">Address Summary</th>
						<th width="11%">Mobile</th>
						<!-- <th width="15%">Email</th>-->
						<th width="7%"><span class="pull-right">Action</span></th>
					</tr>
					<?php if (!empty($address_contacts)) {
					    foreach ($address_contacts as $contact) { ?>
						<tr>
							<td><?php echo ucwords(strtolower($contact->contact_first_name.' '.$contact->contact_last_name)); ?></td>
							<td><?php echo ($contact->relationship) ? $contact->relationship : ''; ?></td>
							<td><?php echo ($contact->address_type) ? $contact->address_type : ''; ?></td>
							<td><?php echo ucwords(strtolower($contact->address_line1)).', '.strtoupper($contact->address_postcode); ?></td>
							<td><a style="color:#324D6B; font-weight:500" href="tel:<?php echo ($contact->contact_mobile) ? $contact->contact_mobile : $contact->contact_number; ?>"><?php echo ($contact->contact_mobile) ? $contact->contact_mobile : $contact->contact_number; ?></a></td>
							<!-- <td><?php //echo ( $contact->contact_email ) ? $contact->contact_email : '';?></td> -->
							<td><span class="pull-right" ><a class="edit pointer" data-contact_id="<?php echo $contact->contact_id; ?>" data-toggle="modal" data-target="#contact-record-modal-md" ><i class="fas fa-edit text-green"></i></a> &nbsp;|&nbsp; <a class="delete pointer" data-contact_id="<?php echo $contact->contact_id; ?>" ><i class="fas fa-trash-alt text-red"></i></a></span></td>
						</tr>
					<?php }
					    } ?>
				</table>
				
				<div class="modal fade contact-record-modal-md" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog modal-md">
						<div class="modal-content">
							<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
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
				
			<?php } else { ?>
				<?php echo $this->config->item('no_records'); ?>
			<?php } ?>
		</div>		
	</div>
	<?php } ?>
</div>

<?php } ?>



<script>
	$(document).ready(function(){
		
		$( "#ajax_contact" ).on( "click", "#updateContactBtn", function( e ){
			e.preventDefault();
			var formData = $( "#contact_update_in_modal" ).serialize();
			swal({
				title: 'Confirm Contact Update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function ( result ) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/people/update_contact/'); ?>",
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
		
		
		$( '.edit' ).click( function(){

			//Trigger modal
			$( ".contact-record-modal-md" ).modal( "show" );
			
			var contact_id = $( this ).data( "contact_id" );
			
			$.ajax({
				url: "<?php echo base_url('webapp/people/get_contact_details/'); ?>",
				method:"POST",
				data:{ 
					contact_id: contact_id,
					person_id: <?php echo $person_details->person_id; ?>,
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
			var contact_id = $( this ).data( "contact_id" );
			
			swal({
				title: 'Confirm Delete Address?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function( result ) {
				if ( result.value ) {
					$.ajax({
						url: "<?php echo base_url('webapp/people/delete_contact/'); ?>",
						method: "POST",
						data:{
							contact_id: contact_id,
							person_id: <?php echo $person_details->person_id; ?>
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
		
		//Trigger address search on btn click
		$('#find-address').click(function(){
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
		
		//If the address type selected is Residential i.e. for the current person's profile, pre-populate with person's details
		$( '#address_type_id' ).change( function(){
			var addrTypeId = $('option:selected', this).val();
			var address_type_group = $('option:selected', this).data( 'address_type_group' );
			if( address_type_group == 'residential' ){
				$( '[name="contact_first_name"]' ).val( '<?php echo $person_details->first_name; ?>' );
				$( '[name="contact_last_name"]' ).val( '<?php echo $person_details->last_name; ?>' );
				$( '[name="contact_email"]' ).val( '<?php echo $person_details->personal_email; ?>' );
			}else{
				$( '[name="contact_first_name"]' ).val( '' );
				$( '[name="contact_last_name"]' ).val( '' );
				$( '[name="contact_email"]' ).val( '' );
			}
		});
		
		$(".asset-creation-steps").click(function(){
			
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
			var changeto = ".asset_creation_panel"+panelnumber;
			$(changefrom).hide( "slide", {direction : 'left'}, 500);
			$(changeto).delay(600).show( "slide", {direction : 'right'},500);	
			return false;	
		}
		
		function go_back( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
			var changeto = ".asset_creation_panel"+panelnumber;
			$(changefrom).hide( "slide", {direction : 'right'}, 500);
			$(changeto).delay(600).show( "slide", {direction : 'left'},500);	
			return false;	
		}
		
		//Submit form for address creation-steps
				//Submit asset form
		$( '#create-contact-btn' ).click(function( e ){
			
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
						url:"<?php echo base_url('webapp/people/create_contact/'); ?>",
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
				}else{
					$( ".asset_creation_panel4" ).hide( "slide", { direction : 'left' }, 500 );
					go_back( ".asset_creation_panel2" );
					return false;
				}
			}).catch(swal.noop)
		});
		

	});
</script>