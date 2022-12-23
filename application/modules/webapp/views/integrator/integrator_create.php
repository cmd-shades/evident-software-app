<div id="add-integrator" class="row">
	<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
		<div class="left-container"> <!-- // Left container -->
			<div class="row">
				<h1>Add System Integrator</h1>
			</div>
			<div class="row">

				<div class="step-name-wrapper current" data-group-name="Integrator Name">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<div class="step-name">Integrator Name</div>
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<div class="step-status"><span class="tick_box el-hidden"><i class="fas fa-check"></i></span><span class="x-cross el-hidden"><i class="fas fa-times"></i></span></div>
						</div>
					</div>
				</div>

				<div class="step-name-wrapper" data-group-name="Integrator Address">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<div class="step-name">Integrator Address</div>
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<div class="step-status"><span class="tick_box el-hidden"><i class="fas fa-check"></i></span><span class="x-cross el-hidden"><i class="fas fa-times"></i></span></div>
						</div>
					</div>
				</div>

				<div class="step-name-wrapper" data-group-name="Integrator Contact Details">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<div class="step-name">Integrator Contact Details</div>
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<div class="step-status"><span class="tick_box el-hidden"><i class="fas fa-check"></i></span><span class="x-cross el-hidden"><i class="fas fa-times"></i></span></div>
						</div>
					</div>
				</div>

				<div class="step-name-wrapper" data-group-name="System Types">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<div class="step-name">System Types</div>
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<div class="step-status"><span class="tick_box el-hidden"><i class="fas fa-check"></i></span><span class="x-cross el-hidden"><i class="fas fa-times"></i></span></div>
						</div>
					</div>
				</div>

				<div class="step-name-wrapper" data-group-name="Territory">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<div class="step-name">Content Territory</div>
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<div class="step-status"><span class="tick_box el-hidden"><i class="fas fa-check"></i></span><span class="x-cross el-hidden"><i class="fas fa-times"></i></span></div>
						</div>
					</div>
				</div>

				<div class="step-name-wrapper" data-group-name="Start Date and Invoice Currency">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<div class="step-name">Start Date and Invoice Currency</div>
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<div class="step-status"><span class="tick_box el-hidden"><i class="fas fa-check"></i></span><span class="x-cross el-hidden"><i class="fas fa-times"></i></span></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div> <!-- // Left container - END -->


	<div class="col-lg-9 col-md-9 col-sm-6 col-xs-12"> <!-- // Right container -->
		<div class="right-container">
			<div class="row">
				<div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12 col-xs-offset-0">
					<form id="integrator-creation-form" >
						<div class="row">
							<div class="integrator_creation_panel1 col-md-6 col-sm-12 col-xs-12" data-panel-index = "0">
								<div class="slide-group">
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<legend class="legend-header">What's the integrator name?</legend>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<h6 class="error_message pull-right" id="integrator_creation_panel1-errors"></h6>
										</div>
									</div>

									<div class="input-group form-group container-full">
										<label class="input-group-addon el-hidden">Integrator name</label>
										<input name="integrator_details[integrator_name]" class="form-control required" type="text" value="" placeholder="Integrator name..."  />
									</div>

									<div class="row">
										<div class="col-lg-6 col-lg-offset-6 col-md-6 col-md-offset-6 col-sm-6 col-sm-offset-6 col-xs-12">
											<button class="btn-block btn-next integrator-creation-steps" data-currentpanel="integrator_creation_panel1" type="button">Next</button>
										</div>
									</div>
								</div>
							</div>

							<div class="integrator_creation_panel2 col-md-6 col-sm-12 col-xs-12 el-hidden" data-panel-index = "1">
								<div class="slide-group">
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<legend class="legend-header">What is the integrator address?</legend>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<h6 class="error_message pull-right" id="integrator_creation_panel2-errors"></h6>
										</div>
									</div>

									<div class="input-group form-group container-full">
										<label class="input-group-addon el-hidden">Address</label>
										<input name="integrator_address[fulladdress]" class="form-control required" type="text" value="" placeholder="Address *" />
									</div>

									<?php /* Hidden for now - 1.05.2020
									<div class="input-group form-group container-full">
										<label class="input-group-addon el-hidden">Address</label>
										<input name="integrator_address[addressline]" class="form-control required" type="text" value="" placeholder="Address *" />
									</div>

									<div class="input-group form-group container-full">
										<label class="input-group-addon el-hidden">Address Line 1</label>
										<input name="integrator_address[addressline1]" class="form-control required" type="text" value="" placeholder="Address Line 1 *" />
									</div>

									<div class="input-group form-group container-full">
										<label class="input-group-addon el-hidden">Address Line 2</label>
										<input name="integrator_address[addressline2]" class="form-control required" type="text" value="" placeholder="Address Line 2 *" />
									</div>

									<div class="input-group form-group container-full">
										<label class="input-group-addon el-hidden">Postcode</label>
										<input name="integrator_address[postcode]" class="form-control required" type="text" value="" placeholder="Postcode *" />
									</div>

									<div class="input-group form-group container-full">
										<label class="input-group-addon el-hidden">Town</label>
										<input name="integrator_address[posttown]" class="form-control required" type="text" value="" placeholder="Town *" />
									</div> */ ?>

									<div class="input-group form-group container-full">
										<label class="input-group-addon el-hidden">Country</label>
										<?php
										if( !empty( $territories ) ){ ?>
											<select name="integrator_address[integrator_territory_id]" class="form-control">
												<option value="">Please select</option>
												<?php
												foreach( $territories as $territory_id => $row ){ ?>
													<option value="<?php echo $row->territory_id; ?>"><?php echo ( !empty ( $row->country ) ) ? ucwords( $row->country ) : '' ; ?></option>
												<?php
												} ?>
											</select>
										<?php
										} else { ?>
											<input name="integrator_address[integrator_territory_id]" class="form-control required" type="text" value="" placeholder="integrator Country *" />
										<?php
										}?>
									</div>

									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn-block btn-back" data-currentpanel="integrator_creation_panel2" type="button">Back</button>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn-block btn-next integrator-creation-steps" data-currentpanel="integrator_creation_panel2" type="button">Next</button>
										</div>
									</div>
								</div>
							</div>

							<div class="integrator_creation_panel3 col-md-6 col-sm-12 col-xs-12 el-hidden" data-panel-index = "2">
								<div class="slide-group">
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<legend class="legend-header">What are the contact details?</legend>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<h6 class="error_message pull-right" id="integrator_creation_panel3-errors"></h6>
										</div>
									</div>

									<div class="input-group form-group container-full">
										<label class="input-group-addon el-hidden">Contact Name</label>
										<input name="integrator_details[contact_name]" class="form-control" type="text" value="" placeholder="Contact Name" />
									</div>

									<div class="input-group form-group container-full">
										<label class="input-group-addon el-hidden">Telephone Number</label>
										<input name="integrator_details[integrator_phone]" class="form-control" type="text" value="" placeholder="Telephone Number of Contact Person" />
									</div>

									<div class="input-group form-group container-full">
										<label class="input-group-addon el-hidden">Email</label>
										<input name="integrator_details[integrator_email]" class="form-control" type="text" value="" placeholder="Email" />
									</div>

									<div class="input-group form-group container-full el-hidden">
										<label class="input-group-addon el-hidden">Skype</label>
										<input name="" class="form-control" type="text" value="" placeholder="Skype Details" />
									</div>

									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn-block btn-back" data-currentpanel="integrator_creation_panel3" type="button">Back</button>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn-block btn-next integrator-creation-steps" data-currentpanel="integrator_creation_panel3" type="button">Next</button>
										</div>
									</div>
								</div>
							</div>


							<div class="integrator_creation_panel4 col-md-6 col-sm-12 col-xs-12 el-hidden" data-panel-index = "3">
								<div class="slide-group">
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<legend class="legend-header">What is the System Type?</legend>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<h6 class="error_message pull-right" id="integrator_creation_panel4-errors"></h6>
										</div>
									</div>

									<div class="input-group form-group container-full">
									<label class="input-group-addon el-hidden">System Type</label>
										<?php
										if( !empty( $systems ) ){ ?>
											<select name="integrator_systems[0]" class="form-control">
												<option value="">Select System Type</option>
												<?php foreach( $systems as $row ){?>
													<option value="<?php echo ( !empty( $row->system_type_id ) ) ? $row->system_type_id : ''; ?>" title="<?php echo ( !empty( $row->name ) ) ? $row->name : '' ?>"><?php echo ( !empty( $row->name ) ) ? $row->name : '' ?></option>
												<?php } ?>
											</select>
										<?php
										} else { ?>
											<input name="integrator_systems[0]" class="form-control" type="text" value="" placeholder="System Type ID" />
										<?php
										} ?>
									</div>
									<div id="outputArea"></div>
									<div class="add_another_attribute"><a class=""><i class="fas fa-plus-circle"></i> Add System</a></div>

									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn-block btn-back" data-currentpanel="integrator_creation_panel4" type="button">Back</button>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn-block btn-next integrator-creation-steps" data-currentpanel="integrator_creation_panel4" type="button">Next</button>
										</div>
									</div>
								</div>
							</div>

							<div class="integrator_creation_panel5 col-md-6 col-sm-12 col-xs-12 el-hidden" data-panel-index = "4">
								<div class="slide-group">
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<legend class="legend-header">What are the Territories?</legend>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<h6 class="error_message pull-right" id="integrator_creation_panel5-errors"></h6>
										</div>
									</div>

									<div class="input-group form-group container-full">
										<label class="input-group-addon el-hidden">What is the territory?</label>
										<?php if( !empty( $territories ) ){ ?>
											<ul class="territory_list" title="Territory Name">
												<?php
												$terr_numb 	= count( ( array ) $territories );
												$col11_num 	= ( $terr_numb % 2 == 1 ) ? floor( $terr_numb / 2 ) + 1 : ( $terr_numb / 2 ) ;

												$i = 1;
												$coll1 = $coll2 = false;

												foreach( $territories as $ter_row ){
													if( $i <= $col11_num ){
														if( !$coll1 ){ ?>
														<div class="col_1">
															<li>
																<label for="all_territories">
																	<input type="checkbox" id="all_territories" value="" />
																	<span class="territory_name">All Territories</span>
																</label>
															</li>
														<?php
														$coll1 = true; } ?>

														<li>
															<label for="<?php echo ( strtolower( $ter_row->country ) ); ?>">
																<input type="checkbox" name="integrator_territories[]" id="<?php echo ( strtolower( $ter_row->country ) ); ?>" value="<?php echo ( !empty( $ter_row->territory_id ) ) ? $ter_row->territory_id : '' ; ?>" /> <span class="territory_name weak"><?php echo ucwords( strtolower( $ter_row->country ) ); ?></span>
															</label>
														</li>
														<?php
														if( $i ==  $col11_num ){ echo '</div>';	}
													} else {
														if( !$coll2 ){ echo '<div class="col_2">'; $coll2 = true; } ?>
														<li>
															<label for="<?php echo ( strtolower( $ter_row->country ) ); ?>">
																<input type="checkbox" name="integrator_territories[]" id="<?php echo ( strtolower( $ter_row->country ) ); ?>" value="<?php echo ( !empty( $ter_row->territory_id ) ) ? $ter_row->territory_id : '' ; ?>" /> <span class="territory_name weak"><?php echo ucwords( strtolower( $ter_row->country ) ); ?></span>
															</label>
														</li>
													<?php
													} ?>
												<?php
												$i++; } ?>
												</div>
											</ul>
										<?php
										} else { ?>
											<p>No territories have been set or all territories are already added.</p>
										<?php
										} ?>
									</div>

									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn-block btn-back" data-currentpanel="integrator_creation_panel5" type="button">Back</button>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn-block btn-next integrator-creation-steps" data-currentpanel="integrator_creation_panel5" type="button">Next</button>
										</div>
									</div>
								</div>
							</div>


							<div class="integrator_creation_panel6 col-md-6 col-sm-12 col-xs-12 el-hidden" data-panel-index = "5">
								<div class="slide-group">
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<legend class="legend-header">What is the Start Date and Invoice Currency?</legend>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<h6 class="error_message pull-right" id="integrator_creation_panel6-errors"></h6>
										</div>
									</div>

									<div class="input-group form-group container-full">
										<label class="input-group-addon el-hidden">Start Date</label>
										<input name="integrator_details[start_date]" class="form-control datetimepicker required" type="text" value="" placeholder="Start Date" />
									</div>

									<div class="input-group form-group container-full">
										<?php
										if( !empty( $si_currencies ) ){ ?>
											<select name="integrator_details[invoice_currency_id]" class="form-control">
												<option value="">Invoice Currency</option>
												<?php foreach( $si_currencies as $row ){?>
													<option value="<?php echo ( !empty( $row->setting_id ) ) ? $row->setting_id : ''; ?>" title="<?php echo ( !empty( $row->value_desc ) ) ? $row->value_desc : '' ?>"><?php echo ( !empty( $row->setting_value ) ) ? $row->setting_value : '' ?></option>
												<?php } ?>
											</select>
										<?php
										} else { ?>
											<input name="integrator_details[invoice_currency_id]" class="form-control" type="text" value="" placeholder="Invoice Currency ID" />
										<?php
										} ?>
									</div>

									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn-block btn-back" data-currentpanel="integrator_creation_panel6" type="button">Back</button>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn-block btn-next" type="submit">Submit</button>
										</div>
									</div>
								</div>
							</div>

						</div>
					</form>
				</div>
			</div>
		</div>
	</div> <!-- // Right container - END -->
</div>

<script type="text/javascript">
$( document ).ready(function(){
	function validEmail( email ){
		var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
		return pattern.test( email );
	}


	function validPhone( phone ){
		var pattern = /([0-9]{10})|(\([0-9]{3}\)\s+[0-9]{3}\-[0-9]{4})/;
		// if spaces allowed
		var phone = phone.replace( /\s/g, "" );
		return pattern.test( phone );
	}


	//Submit Integrator form
	$( '#integrator-creation-form' ).on( "submit", function( e ){
		e.preventDefault();
		var formData = $( '#integrator-creation-form' ).serialize();

		swal({
			title: 'Confirm new integrator creation?',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url( 'webapp/integrator/create_integrator/' ); ?>",
					method:"POST",
					data:formData,
					dataType: 'json',
					success:function( data ){
						if( data.status == 1 && ( data.integrator !== '' ) ){

							var newIntegratorId = data.integrator.system_integrator_id;
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 2000
							})
							window.setTimeout( function(){
								location.href = "<?php echo base_url( 'webapp/integrator/profile/' ); ?>" + newIntegratorId;
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
				$( ".integrator_creation_panel6" ).hide( "slide", { direction : 'left' }, 500 );
				go_back( ".integrator_creation_panel2" );
				return false;
			}
		}).catch( swal.noop )
	});

	// Adding dynamic field after click
	var i = 1;
	$( ".add_another_attribute > a" ).on( "click", function(){
		var template = '<div class="value_attributes"><div class="input-group form-group container-full"><label class="input-group-addon el-hidden">Value Order</label>	<select name="integrator_systems[' + i + ']" class="form-control"><option value="">Select System Type</option>';
			<?php
			if( !empty( $systems ) ){
				foreach( $systems as $row ){ ?>
					template += '<option value="<?php echo ( !empty( $row->system_type_id ) ) ? $row->system_type_id : ''; ?>" title="<?php echo ( !empty( $row->name ) ) ? $row->name : '' ?>"><?php echo ( !empty( $row->name ) ) ? $row->name : '' ?></option>';
				<?php
				}
			} ?>
		template += '</select></div></div>';
		i++;
		$( "#outputArea" ).append( template );
	});

	var trigger = $( "#all_territories" );
	$( trigger ).on( "change", function(){
		if( $( this ).prop( "checked" ) != true ){
			$( ".territory_list input[type='checkbox']" ).each(
				function(){ $( this ).prop( "checked", false ) }
			)
		} else {
			$( ".territory_list input[type='checkbox']" ).each(
				function(){ $( this ).prop( "checked", true ) }
			)
		}
	});

	$( ".territory_list input[type='checkbox']" ).not( ":first" ).on( "click", function(){
		if( ( $( trigger ).prop( "checked" ) == true ) && ( $( this ).prop( "checked" ) != true ) ){
			$( trigger ).prop( "checked", false );
		}
	})

 	$( ".integrator-creation-steps" ).click( function(){
		$( '.error_message' ).each( function(){
			$( this ).text( '' );
		});

		var currentpanel = $( this ).data( "currentpanel" );
		var inputs_state = check_inputs( currentpanel );
		if( inputs_state ){
			//If name attribute returned, auto focus to the field and display an error message
			$( '[name="'+inputs_state+'"]' ).focus();
			var labelText = $( '[name="'+inputs_state+'"]' ).parent().find( 'label' ).text();
			$( '#'+currentpanel+'-errors' ).text( ucwords( labelText ) +' is a required' );
			return false;
		}
		panelchange( "." + currentpanel )
		return false;
	});

	//** Validate any inputs that have the 'required' class, if empty return the name attribute **/
	function check_inputs( currentpanel ){

		var result = false;
		var panel = "." + currentpanel;

		$( $( panel + " .required" ).get().reverse() ).each( function(){
			var fieldName = '';
			var inputValue = $( this ).val();
			if( ( inputValue == false ) || ( inputValue == '' ) || ( inputValue.length == 0 ) ){
				fieldName = $( this ).attr( 'name' );
				result = fieldName;
				return result;
			}
		});


		if( currentpanel == "integrator_creation_panel3" ){
			
			// phone validation
			var phone = $( "[name='integrator_details[integrator_phone]']" ).val();
			if( !validPhone( phone ) ) {
				alert( 'Please enter valid phone number (only digits, min 10)' );
				fieldName = $( "[name='integrator_details[integrator_phone]']" ).attr( 'name' );
				result = fieldName;
				return result;
			}
			
			// email validation
			var email = $( "[name='integrator_details[integrator_email]']" ).val();
			if( !validEmail( email ) ) {
				alert( 'Please enter valid email' );
				fieldName = $( "[name='integrator_details[integrator_email]']" ).attr( 'name' );
				result = fieldName;
				return result;
			}
		}


		return result;
	}

	$( ".btn-next" ).click( function(){
		var currentpanel = $( "." + $( this ).data( "currentpanel" ) );
		prev_group_is_valid = true;

		currentpanel.find( "input" ).each( function( i, input_element ){
			if ( $( input_element ).hasClass( "required" ) ){
				if( $( input_element ).val() == "" ){
					prev_group_is_valid = false;
				}
			}
		});

		current_panel_id = $( "." + $( this ).data( "currentpanel" ) ).attr( "data-panel-index" );

		if( prev_group_is_valid ){
			$( $( ".tick_box" )[current_panel_id]).removeClass( "el-hidden" )
			$( $( ".x-cross" )[current_panel_id]).addClass( "el-hidden" )
		} else {
			$( $( ".x-cross" )[current_panel_id]).removeClass( "el-hidden" )
			$( $( ".tick_box" )[current_panel_id]).addClass( "el-hidden" )
		}

	});

	$( ".btn-back" ).click( function(){
		var currentpanel = $( this ).data( "currentpanel" );
		go_back( "." + currentpanel )
		return false;
	});

	function panelchange( changefrom ){
		var panelnumber = parseInt( changefrom.match(/\d+/) )+parseInt(1);
		var changeto = ".integrator_creation_panel" + panelnumber;
		$( changefrom ).hide( "slide", {direction : 'left'}, 500 );
		$( changeto ).delay( 600 ).show( "slide", {direction : 'right'},500 );
		return false;
	}

	function go_back( changefrom ){
		var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
		var changeto = ".integrator_creation_panel" + panelnumber;
		$( changefrom ).hide( "slide", {direction : 'right'}, 500 );
		$( changeto ).delay( 600 ).show( "slide", {direction : 'left'},500 );
		return false;
	}
});
</script>