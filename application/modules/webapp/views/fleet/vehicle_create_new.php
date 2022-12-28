<style type="text/css">
</style>

<div class="create_new_vehicle_container">
	<legend>Create New Vehicle</legend>
	<form id="vehicle-creation-form" method="post">
		<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
		<input type="hidden" name="page" value="create" />
		<div class="row">
			<div class="site_creation_panel1 col-md-6 col-sm-12 col-xs-12">
				<div class="x_panel tile has-shadow">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h5 class="left">What is the Vehicle Make?</h5>
							<h5 class="pull-right error_message" style="display: none;">Please, enter the Vehicle Make</h5>
							<div class="input-group form-group" style="width: 100%;">
								<label class="input-group-addon">Vehicle Make&nbsp;*</label>
								<input name="vehicle_make" type="text" class="form-control" id="vehicle_make" required />
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h5 class="left">What is the Vehicle Model?</h5>
							<h5 class="pull-right error_message" style="display: none;">Please, enter the Vehicle Model</h5>
							<div class="input-group form-group">
								<label class="input-group-addon">Vehicle Model&nbsp;*</label>
								<input name="vehicle_model" type="text" class="form-control" id="vehicle_model" />
							</div>
						</div>
					</div>

					<?php /*
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h5 class="left">What is the Vehicle Year?</h5>
							<h5 class="pull-right error_message" style="display: none;">Please, enter the Vehicle Year</h5>
							<div class="input-group form-group">
								<label class="input-group-addon">Vehicle Year&nbsp;*</label>
								<input name="year" type="text" class="form-control" id="year" />
							</div>
						</div>
					</div>
					*/ ?>

					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h5 class="left">What is the Vehicle Registration Number?</h5>
							<h5 class="pull-right error_message" style="display: none;">Please, enter the Vehicle Registration</h5>
							<div class="input-group form-group">
								<label class="input-group-addon">Vehicle Registration&nbsp;*</label>
								<input name="vehicle_reg" type="text" class="form-control" id="vehicle_reg" required />
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h5 class="left">Does the vehicle have a unicode barcode ?</h5>
							<!--- <h5 class="pull-right error_message" style="display: none;">Vehicle Unique ID / Barcode</h5> -->
							<div class="input-group form-group">
								<label class="input-group-addon">Vehicle Unique ID / Barcode&nbsp;</label>
								<input name="vehicle_barcode" type="text" class="form-control no_check" id="vehicle_barcode" />
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-info btn-back hide" data-currentpanel="site_creation_panel7" type="button">Back</button>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button type="submit" class="btn btn-block btn-flow btn-success" id="createVehicleButton">Create Vehicle</button>
						</div>
					</div>
				</div>
			</div>




			<div class="site_creation_panel3 col-md-6 col-sm-12 col-xs-12" style="display: none;">
				<div class="x_panel tile has-shadow">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h5>Who is the Vehicle Supplier?</h5>
							<h5 class="pull-right error_message" style="display: none;">Please, enter the Vehicle Supplier name</h5>
							<div class="input-group form-group">
								<label class="input-group-addon">Vehicle Supplier</label>
								<select name="supplier_id" type="text" class="form-control no_check" id="supplier_id">
									<option value="">Please, select the supplier</option>
										<?php
										if( !empty( $vehicle_suppliers ) ){
											foreach( $vehicle_suppliers as $row ){ ?>
												<option value="<?php echo $row->supplier_id ?>"><?php echo ucwords( $row->supplier_name ); ?></option>
											<?php
											}
										} else { ?>
											<option value="">Please add Suppliers</option>
										<?php
										} ?>
								</select>
							</div>
						</div>
					</div>
					<div class="row veh_supply_date_wrapper" style="display: none;">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h5>What is the Vehicle Supply Date?</h5>
							<h5 class="pull-right error_message" style="display: none;">Please, enter the Vehicle Supply Date</h5>
							<div class="input-group form-group">
								<label class="input-group-addon">Supply Date:</label>
								<input type="text" name="supply_date" value="<?php echo date( 'd/m/Y' ); ?>" class="form-control datetimepicker" id="datetimepicker1" data-date-format="DD/MM/Y" placeholder="<?php echo date( 'd/m/Y' ); ?>" required />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-info btn-back" data-currentpanel="site_creation_panel3" type="button" >Back</button>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-success btn-next fleet-creation-steps" data-currentpanel="site_creation_panel3" type="button" >Next</button>
						</div>
					</div>
				</div>
			</div>

			<div class="site_creation_panel4 col-md-6 col-sm-12 col-xs-12" style="display: none;">
				<div class="x_panel tile has-shadow">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h5>What is the Vehicle MOT and TAX expiry Date?</h5>
							<div class="input-group form-group">
								<label class="input-group-addon">MOT Expiry Date:</label>
								<input type="text" name="mot_expiry" value="<?php echo date( 'd/m/Y' ); ?>" class="form-control datetimepicker" data-date-format="DD/MM/Y" placeholder="<?php echo date( 'd/m/Y' ); ?>" required />
							</div>
							<div class="input-group form-group">
								<label class="input-group-addon">TAX Expiry Date:</label>
								<input type="text" name="tax_expiry" value="<?php echo date( 'd/m/Y' ); ?>" class="form-control datetimepicker" data-date-format="DD/MM/Y" placeholder="<?php echo date( 'd/m/Y' ); ?>" required />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-info btn-back" data-currentpanel="site_creation_panel4" type="button">Back</button>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-success btn-next fleet-creation-steps" data-currentpanel="site_creation_panel4" type="button">Next</button>
						</div>
					</div>
				</div>
			</div>

			<div class="site_creation_panel5 col-md-6 col-sm-12 col-xs-12" style="display: none;">
				<div class="x_panel tile has-shadow">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h5>Is the vehicle insured?</h5>
							<div class="input-group form-group">
								<label class="input-group-addon">Is Insured:</label>
								<select name="is_insured" type="text" class="form-control" id="is_insured" />
									<option value="yes">Yes</option>
									<option value="no" selected="selected">No</option>
								</select>
							</div>
						</div>
					</div>
					<div class="row" id="insurance_provider_wrapper" style="display: none;">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h5 class="left">What is the name of the Insurance Provider?</h5>
							<h5 class="pull-right error_message" style="display: none;">Please, enter the Insurance Provider name</h5>
							<div class="input-group form-group">
								<label class="input-group-addon">Insurance Provider:</label>
								<input name="insurance_provider" type="text" class="form-control no_check" id="insurance_provider" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-info btn-back" data-currentpanel="site_creation_panel5" type="button">Back</button>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-success btn-next fleet-creation-steps" data-currentpanel="site_creation_panel5" type="button">Next</button>
						</div>
					</div>
				</div>
			</div>


			<div class="site_creation_panel6 col-md-6 col-sm-12 col-xs-12" style="display: none;">
				<div class="x_panel tile has-shadow">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h5>Has the vehicle the Road Assistance?</h5>
							<div class="input-group form-group">
								<label class="input-group-addon">Road Assistance:</label>
								<select name="has_road_assistance" type="text" class="form-control no_check" id="has_road_assistance" />
									<option value="yes">Yes</option>
									<option value="no" selected="selected">No</option>
								</select>
							</div>
						</div>
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h5>What is the camera instal date?</h5>
							<div class="input-group form-group">
								<label class="input-group-addon">Camera Install Date:</label>
								<input type="text" name="camera_install_date" value="" class="form-control datetimepicker no_check" data-date-format="DD/MM/Y" placeholder="<?php echo date( 'd/m/Y' ); ?>" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-info btn-back" data-currentpanel="site_creation_panel6" type="button" >Back</button>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-success btn-next fleet-creation-steps" data-currentpanel="site_creation_panel6" type="button">Next</button>
						</div>
					</div>
				</div>
			</div>


			<div class="site_creation_panel7 col-md-6 col-sm-12 col-xs-12" style="display: none;">
				<div class="x_panel tile has-shadow">
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h5>What is the hire cost?</h5>
							<div class="input-group form-group">
								<label class="input-group-addon">Hire Cost (£/week):</label>
								<input name="hire_cost" type="text" class="form-control" id="hire_cost" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<h5>What is the Insurance Cost?</h5>
							<div class="input-group form-group">
								<label class="input-group-addon">Insurance Cost (£/week):</label>
								<input name="insurance_cost" type="text" class="form-control" id="insurance_cost" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-info btn-back" data-currentpanel="site_creation_panel7" type="button">Back</button>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button type="submit" class="btn btn-block btn-flow btn-success" data-currentpanel="site_creation_panel7" id="createVehicleButton">Create Vehicle</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<script>
	$(document).ready( function(){

		/* https://xdsoft.net/jqplugins/datetimepicker/ */
		$( '.datetimepicker' ).datetimepicker({
			formatDate: 'd/m/Y',
			timepicker:false,
			format:'d/m/Y',
		});
		
		$( "#is_insured" ).on( "change", function(){
			$( "#insurance_provider" ).toggleClass( "no_check", "" );
			$( "#insurance_provider_wrapper" ).toggle();
		});
			
			
		$( "#supplier_id" ).on( "change", function(){
			if( $( this ).val() == '' ){
				$( ".veh_supply_date_wrapper" ).hide( "slow" );
			} else {
				$( ".veh_supply_date_wrapper" ).show( "slow" );
			}
		});

		$( ".fleet-creation-steps" ).click( function(){
			var currentpanel = $( this ).data( "currentpanel" );
			var inputs_state = check_inputs( currentpanel );

			if( inputs_state == true ){
				panelchange( "." + currentpanel )
				return false;
			} else {
				show_warning( currentpanel );
				return false;
			}
		});

		$(".btn-back").click(function(){
			var currentpanel = $(this).data("currentpanel");
			go_back("."+currentpanel)
			return false;
		});

		function panelchange( changefrom ){
			var panelnumber = parseInt( changefrom.slice( 20 ) )+parseInt( 1 );
			var changeto = ".site_creation_panel"+panelnumber;
			$( changefrom ).hide( "slide", { direction : 'left' }, 500 );
			$( changeto ).delay( 600 ).show( "slide", {direction : 'right'},500 );
			return false;
		}

		function go_back( changefrom ){
			var panelnumber = parseInt( changefrom.slice( 20 ) )-parseInt( 1 );
			var changeto = ".site_creation_panel"+panelnumber;
			$(changefrom).hide( "slide", {direction : 'right'}, 500);
			$(changeto).delay(600).show( "slide", {direction : 'left'},500);
			return false;
		}

		function check_inputs( currentpanel ){
			var result = true;
			var panel = "." + currentpanel;

			$( panel + " input" ).each( function(){
				if( $( this ).hasClass( 'no_check' ) ){

				} else {
					var value = $( this ).val();
					if( ( value == false ) || ( value == '' ) ){
						result = false;
					}
				}
			});

			$( panel + " select" ).each( function(){
				if( $( this ).hasClass( 'no_check' ) ){

				} else {
					var value = $( this ).val();
					if( ( value == false ) || ( value == '' ) ){
						result = false;
					}
				}
			});

			return result;
		}

		function show_warning( currentpanel ){
			var panel = "." + currentpanel;
			$( panel ).find( ".error_message" ).show();
		}

		//Submit vehicle form
		$( '#createVehicleButton' ).click( function( e ){
			e.preventDefault();
			
			var inputs_state = check_inputs( "site_creation_panel1" );

			if( inputs_state != true ){
				show_warning( "site_creation_panel1" );
				return false;
			}
			
			var formData = $( '#vehicle-creation-form' ).serialize();
			swal({
				title: 'Confirm new vehicle creation?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function ( result ) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url( 'webapp/fleet/create/' ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 && ( data.vehicle_id !== '' ) ){
								var newVehicleId = data.vehicle_id;
								swal({
									type: 'success',
									title: data.message,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout( function(){
									location.href = "<?php echo base_url( 'webapp/fleet/profile/' ); ?>" + newVehicleId;
								}, 3000 );
							} else {
								swal({
									type: 'error',
									title: data.message
								})
							}
						}
					});
				} else {
					$( ".site_creation_panel7" ).hide( "slide", { direction : 'left' }, 500 );
					go_back( ".site_creation_panel2" );
					return false;
				}
			}).catch(swal.noop)

		});
	});
</script>