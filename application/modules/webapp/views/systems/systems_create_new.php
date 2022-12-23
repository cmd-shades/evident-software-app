<div id="add-new-system" class="row">
	<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
		<div class="left-container"> <!-- // Left container -->
			<div class="row">
				<h1>Add System</h1>
			</div>
			<div class="row">

				<div class="step-name-wrapper current" data-group-name="System Name">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<div class="step-name">System Name</div>
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<div class="step-status"><span class="tick_box el-hidden"><i class="fas fa-check"></i></span><span class="x-cross el-hidden"><i class="fas fa-times"></i></span></div>
						</div>
					</div>
				</div>

				<div class="step-name-wrapper" data-group-name="Local Server">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<div class="step-name">Local Server</div>
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<div class="step-status"><span class="tick_box el-hidden"><i class="fas fa-check"></i></span><span class="x-cross el-hidden"><i class="fas fa-times"></i></span></div>
						</div>
					</div>
				</div>

				<div class="step-name-wrapper" data-group-name="DRM Type">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<div class="step-name">DRM Type</div>
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<div class="step-status"><span class="tick_box el-hidden"><i class="fas fa-check"></i></span><span class="x-cross el-hidden"><i class="fas fa-times"></i></span></div>
						</div>
					</div>
				</div>

				<div class="step-name-wrapper" data-group-name="Delivery Mechanism">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<div class="step-name">Delivery Mechanism</div>
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
					<form id="systems-creation-form" >
						<input type="hidden" name="page" value="details" />
						<div class="row">
							<div class="system_creation_panel1 col-md-6 col-sm-12 col-xs-12" data-panel-index = "0">
								<div class="slide-group">
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<legend class="legend-header">What's the System name?</legend>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<h6 class="error_message pull-right" id="system_creation_panel1-errors"></h6>
										</div>
									</div>

									<div class="input-group form-group container-full">
										<label class="input-group-addon el-hidden">System name</label>
										<input name="system_details[name]" class="form-control required" type="text" value="" placeholder="System name..."  />

										<label class="input-group-addon el-hidden">System Reference Code</label>
										<input name="system_details[system_reference_code]" class="form-control" type="text" value="" placeholder="System Reference Code"  />
									</div>

									<div class="row">
										<div class="col-lg-6 col-lg-offset-6 col-md-6 col-md-offset-6 col-sm-6 col-sm-offset-6 col-xs-12">
											<button class="btn-block btn-next" data-currentpanel="system_creation_panel1" id="check-reference-button" type="button">Next</button>
										</div>
									</div>
								</div>
							</div>

							<div class="system_creation_panel2 col-md-6 col-sm-12 col-xs-12 el-hidden" data-panel-index = "1">
								<div class="slide-group">
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<legend class="legend-header">Is this a Local Server ?</legend>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<h6 class="error_message pull-right" id="system_creation_panel2-errors"></h6>
										</div>
									</div>

									<div class="input-group form-group container-full">
										<label class="input-group-addon el-hidden">Local Server</label>
										<select name="system_details[is_local_server]" class="form-control">
											<option value="">Please select</option>
											<option value="yes">Yes</option>
											<option value="no">No</option>
										</select>
									</div>

									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn-block btn-back" data-currentpanel="system_creation_panel2" type="button">Back</button>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn-block btn-next system-creation-steps" data-currentpanel="system_creation_panel2" type="button">Next</button>
										</div>
									</div>
								</div>
							</div>

							<div class="system_creation_panel3 col-md-6 col-sm-12 col-xs-12 el-hidden" data-panel-index = "2">
								<div class="slide-group">
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<legend class="legend-header">DRM Type</legend>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<h6 class="error_message pull-right" id="system_creation_panel3-errors"></h6>
										</div>
									</div>

									<div class="input-group form-group container-full">
										<label class="input-group-addon el-hidden">What is the DRM type?</label>
										<select name="system_details[drm_type_id]" class="form-control">
											<option value="">Please select</option>
											<?php
											if( !empty( $drm_types ) ){ ?>
													<?php foreach( $drm_types as $row ){?>
														<option value="<?php echo ( !empty( $row->setting_id ) ) ? $row->setting_id : ''; ?>" title="<?php echo ( !empty( $row->setting_value ) ) ? $row->setting_value : '' ?>"><?php echo ( !empty( $row->setting_value ) ) ? $row->setting_value : '' ?></option>
													<?php } ?>
											<?php
											} ?>
										</select>
									</div>

									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn-block btn-back" data-currentpanel="system_creation_panel3" type="button">Back</button>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn-block btn-next system-creation-steps" data-currentpanel="system_creation_panel3" type="button">Next</button>
										</div>
									</div>
								</div>
							</div>

							<div class="system_creation_panel4 col-md-6 col-sm-12 col-xs-12 el-hidden" data-panel-index = "4">
								<div class="slide-group">
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<legend class="legend-header">What is the Delivery Mechanism?</legend>
										</div>

										<div class="col-md-6 col-sm-6 col-xs-12">
											<h6 class="error_message pull-right" id="system_creation_panel4-errors"></h6>
										</div>
									</div>

									<div class="input-group form-group container-full">
										<label class="input-group-addon el-hidden">What is the Delivery Mechanism?</label>
										<?php
										if( !empty( $delivery_mechanism_types ) ){ ?>
											<select name="system_details[delivery_mechanism_id]" class="form-control">
												<option value="">Please select</option>
												<?php foreach( $delivery_mechanism_types as $row ){ ?>
													<option value="<?php echo ( !empty( $row->setting_id ) ) ? $row->setting_id : ''; ?>" title="<?php echo ( !empty( $row->setting_value ) ) ? $row->setting_value : '' ?>"><?php echo ( !empty( $row->setting_value ) ) ? $row->setting_value : '' ?></option>
												<?php } ?>
											</select>
										<?php
										} else { ?>
											<input name="system_details[delivery_mechanism_id]" type="text" value="" placeholder="Delivery Mechanism ID" />
										<?php
										} ?>
									</div>

									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn-block btn-back" data-currentpanel="system_creation_panel4" type="button">Back</button>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button id="create-system-btn" class="btn-block btn-flow btn-next" type="submit">Submit</button>
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

<script>
	$(document).ready(function(){
		$( ".is_approved_by_provider" ).change( function(){
			if( $( ".is_approved_by_provider" ).val() == 'yes' ){
				$( ".aproval_date" ).css( "display", "block" )
			} else {
				$( ".aproval_date" ).css( "display", "none" )
			}
		});

		$( ".system-creation-steps" ).click( function(){

			//Clear errors first
			$( '.error_message' ).each( function(){
				$( this ).text( '' );
			});

			var currentpanel = $(this).data( "currentpanel" );
			var inputs_state = check_inputs( currentpanel );
			if( inputs_state ){
				//If name attribute returned, auto focus to the field and display arror message
				$( '[name="'+inputs_state+'"]' ).focus();
				var labelText = $( '[name="'+inputs_state+'"]' ).parent().find( 'label' ).text();
				$( '#'+currentpanel+'-errors' ).text( ucwords( labelText ) +' is a required' );
				return false;
			}
			panelchange( "."+currentpanel )
			return false;
		});

		//** Validate any inputs that have the required class, if empty return the name attribute **/
		function check_inputs( currentpanel ){

			var result = false;
			var panel = "." + currentpanel;

			$( $( panel + " .required" ).get().reverse() ).each( function(){
				var fieldName = '';
				var inputValue = $( this ).val();
				if( ( inputValue == false ) || ( inputValue == '' ) || ( inputValue.length == 0 ) ){
					fieldName = $(this).attr( 'name' );
					result = fieldName;
					return result;
				}
			});
			return result;
		}

		$(".btn-next").click(function() {

			var currentpanel = $("."+$(this).data( "currentpanel" ));
			prev_group_is_valid = true;

			currentpanel.find("input").each(function(i, input_element) {
				if ($(input_element).hasClass("required")) {
					if ($(input_element).val() == "") {
						prev_group_is_valid = false;
					}
				}
			});

			current_panel_id = $("."+$(this).data( "currentpanel" )).attr("data-panel-index")

			if(prev_group_is_valid){

				$($(".tick_box")[current_panel_id]).removeClass( "el-hidden" )
				$($(".x-cross")[current_panel_id]).addClass( "el-hidden" )
			} else {
				$($(".x-cross")[current_panel_id]).removeClass( "el-hidden" )
				$($(".tick_box")[current_panel_id]).addClass( "el-hidden" )
			}

		});

		$( ".btn-back" ).click( function(){
			var currentpanel = $( this ).data( "currentpanel" );
			go_back( "."+currentpanel )
			return false;
		});

		function panelchange( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )+parseInt(1);
			var changeto = ".system_creation_panel"+panelnumber;
			$( changefrom ).hide( "slide", {direction : 'left'}, 500 );
			$( changeto ).delay( 600 ).show( "slide", {direction : 'right'},500 );
			return false;
		}

		function go_back( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
			var changeto = ".system_creation_panel"+panelnumber;
			$( changefrom ).hide( "slide", {direction : 'right'}, 500 );
			$( changeto ).delay( 600 ).show( "slide", {direction : 'left'},500 );
			return false;
		}

		//Submit System form
		$( '#systems-creation-form' ).submit( function( e ){
			e.preventDefault();
			var formData = $( '#systems-creation-form' ).serialize();

			swal({
				title: 'Confirm new System creation?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url( 'webapp/systems/create_system/' ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 && ( data.system !== '' ) ){

								var newSystemId = data.system.system_type_id;

								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout( function(){
									location.href = "<?php echo base_url( 'webapp/systems/profile/' ); ?>"+newSystemId;
								}, 3000 );
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}
						}
					});
				}else{
					$( ".system_creation_panel16" ).hide( "slide", { direction : 'left' }, 500 );
					go_back( ".system_creation_panel1" );
					return false;
				}
			}).catch( swal.noop )
		});

	$( "[name = 'system_details[name]' ]" ).on("input", function() {
		title_text = $( "[ name = 'system_details[name]']" ).val();
		if(title_text.length > 255) title_text = title_text.substring(0,254);
		title_text = title_text.replace(/[^a-z0-9]+/gi, "").toLowerCase();
		asset_input = $( "[name = 'system_details[system_reference_code]' ]" ).val(title_text);
	});

	$( "#check-reference-button" ).click( function( e ){
		var ref = $( "[name = 'system_details[system_reference_code]' ]" ).val()

		if(!(ref == "")){
		$.ajax({
				url:"<?php echo base_url( 'webapp/systems/check_reference/' ); ?>",
				method:"POST",
				data:{
					"reference": ref,
					"module": "systems"
				},
				dataType: 'json',
				success:function( data ){
					if( ( data.status == 1 ) ){
						swal({
							type: 'error',
							title: data.status_msg,
							timer: 3000
						})
					} else {
						/* swal({
							type: 'success',
							title: data.status_msg,
							timer: 3000
						}) */

						panelchange( ".system_creation_panel1")

					}
				}
			});

		} else {
				swal({
					type: 'error',
					title: "Reference can not be empty!",
					timer: 3000
				})
		}
	});
});

</script>