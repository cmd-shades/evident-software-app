<div id="add-channel" class="row">
	<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
		<div class="left-container"> <!-- // Left container -->
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<h1>Add Channel</h1>
				</div>
			</div>
			<div class="row">
				<?php
				$elements = ["Provider", "Channel Name and Description", "Distribution Territories", "Distribution Start Date", "OTT Channel" ];

				foreach( $elements as $key => $el ){ ?>
					<div class="step-name-wrapper current" data-group-name="<?php echo $el; ?>">
						<div class="col-lg-9 col-md-9 col-sm-6 col-xs-9">
							<div class="step-name"><?php echo $el; ?></div>
						</div>
						<div class="col-lg-3 col-md-6 col-sm-6 col-xs-3">
							<div class="step-status"><span class="tick_box el-hidden"><i class="fas fa-check"></i></span><span class="x-cross el-hidden"><i class="fas fa-times"></i></span></div>
						</div>
					</div>
				<?php
				} ?>

			</div>
		</div>
	</div> <!-- // Left container - END -->

	<div class="col-lg-9 col-md-9 col-sm-6 col-xs-12"> <!-- // Right container -->
		<div class="right-container">
			<div class="row">
				<div class="col-lg-9 col-lg-offset-3 col-md-9 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12 col-xs-offset-0">
					<form id="channel-creation-form" >
						<div class="row">
							<div class="channel_creation_panel1 col-md-6 col-sm-12 col-xs-12" data-panel-index = "0">
								<div class="slide-group">
									<div class="row">
										<div class="col-md-12 col-sm-12 col-xs-12">
											<legend class="legend-header">What is the Channel Provider?</legend>
										</div>
										<div class="col-md-12 col-sm-12 col-xs-12">
											<h6 class="error_message pull-right" id="channel_creation_panel1-errors"></h6>
										</div>
									</div>

									<div class="input-group form-group container-full">
										<label class="input-group-addon el-hidden">Channel Provider</label>
										<?php
										if( !empty( $providers ) ){ ?>
											<select name="provider_id" class="form-control required" required="required">
												<option value="">Select Provider</option>
												<?php foreach( $providers as $pr_row ){?>
													<option value="<?php echo ( !empty( $pr_row->provider_id ) ) ? $pr_row->provider_id : ''; ?>" title="<?php echo ( !empty( $pr_row->provider_name ) ) ? $pr_row->provider_name : '' ?>"><?php echo ( !empty( $pr_row->provider_name ) ) ? $pr_row->provider_name : '' ?></option>
												<?php } ?>
											</select>
										<?php
										} ?>
									</div>

									<div class="row">
										<div class="col-lg-6 col-lg-offset-6 col-md-6 col-md-offset-6 col-sm-6 col-sm-offset-6 col-xs-12">
											<button class="btn-block btn-next channel-creation-steps" data-currentpanel="channel_creation_panel1" type="button">Next</button>
										</div>
									</div>
								</div>
							</div>

							<div class="channel_creation_panel2 col-md-6 col-sm-12 col-xs-12 el-hidden" data-panel-index = "1">
								<div class="slide-group">
									<div class="row">
										<div class="col-md-12 col-sm-12 col-xs-12">
											<legend class="legend-header">What is the Channel Name and Description?</legend>
										</div>
										<div class="col-md-12 col-sm-12 col-xs-12">
											<h6 class="error_message pull-right" id="channel_creation_panel2-errors"></h6>
										</div>
									</div>

									<div class="input-group form-group container-full">
										<label class="input-group-addon el-hidden">Channel Name</label>
										<input name="channel_name" class="form-control required" type="text" value="" placeholder="Channel Name" title="Channel Name" required="required" />
									</div>

									<div class="input-group form-group container-full">
										<label class="input-group-addon el-hidden">Channel Reference Code</label>
										<input name="channel_reference_code" class="form-control required" type="text" value="" placeholder="Channel Reference Code" title="Channel Reference Code" required="required" />
									</div>

									<div class="input-group form-group container-full">
										<label class="input-group-addon el-hidden">Channel Description</label>
										<textarea rows="3" class="form-control" name="description" placeholder="Description"></textarea>
									</div>

									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn-block btn-back" data-currentpanel="channel_creation_panel2" type="button">Back</button>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn-block btn-next channel-creation-steps" data-currentpanel="channel_creation_panel2" type="button">Next</button>
										</div>
									</div>
								</div>
							</div>

							<div class="channel_creation_panel3 col-md-6 col-sm-12 col-xs-12 el-hidden" data-panel-index = "2">
								<div class="slide-group">
									<div class="row">
										<div class="col-md-12 col-sm-12 col-xs-12">
											<legend class="legend-header">What are the Territories?</legend>
										</div>
										<div class="col-md-12 col-sm-12 col-xs-12">
											<h6 class="error_message pull-right" id="channel_creation_panel3-errors"></h6>
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
																<input type="checkbox" class="required" name="channel_territories[]" id="<?php echo ( strtolower( $ter_row->country ) ); ?>" value="<?php echo ( !empty( $ter_row->territory_id ) ) ? $ter_row->territory_id : '' ; ?>" /> <span class="territory_name weak"><?php echo ucwords( strtolower( $ter_row->country ) ); ?></span>
															</label>
														</li>
														<?php
														if( $i ==  $col11_num ){ echo '</div>';	}
													} else {
														if( !$coll2 ){ echo '<div class="col_2">'; $coll2 = true; } ?>
														<li>
															<label for="<?php echo ( strtolower( $ter_row->country ) ); ?>">
																<input type="checkbox" class="required" name="channel_territories[]" id="<?php echo ( strtolower( $ter_row->country ) ); ?>" value="<?php echo ( !empty( $ter_row->territory_id ) ) ? $ter_row->territory_id : '' ; ?>"  /> <span class="territory_name weak"><?php echo ucwords( strtolower( $ter_row->country ) ); ?></span>
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
											<button class="btn-block btn-back" data-currentpanel="channel_creation_panel3" type="button">Back</button>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn-block btn-next channel-creation-steps" data-currentpanel="channel_creation_panel3" type="button">Next</button>
										</div>
									</div>
								</div>
							</div>

							<div class="channel_creation_panel4 col-md-6 col-sm-12 col-xs-12 el-hidden" data-panel-index = "3">
								<div class="slide-group">
									<div class="row">
										<div class="col-md-12 col-sm-12 col-xs-12">
											<legend class="legend-header">What is the Distribution Start Date?</legend>
										</div>
										<div class="col-md-12 col-sm-12 col-xs-12">
											<h6 class="error_message pull-right" id="channel_creation_panel4-errors"></h6>
										</div>
									</div>

									<div class="input-group form-group container-full">
										<label class="input-group-addon el-hidden">Distribution Start Date</label>
										<input name="distribution_start_date" class="form-control datetimepicker required" type="text" value="" placeholder="Start Date" required />
									</div>

									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn-block btn-back" data-currentpanel="channel_creation_panel4" type="button">Back</button>
										</div>
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn-block btn-next channel-creation-steps" data-currentpanel="channel_creation_panel4" type="button">Next</button>
										</div>
									</div>
								</div>
							</div>

							<div class="channel_creation_panel5 col-md-6 col-sm-12 col-xs-12 el-hidden" data-panel-index = "4">
								<div class="slide-group">
									<div class="row">
										<div class="col-md-12 col-sm-6 col-xs-12">
											<legend class="legend-header">Is the channel OTT?</legend>
										</div>
										<div class="col-md-12 col-sm-6 col-xs-12">
											<h6 class="error_message pull-right" id="channel_creation_panel5-errors"></h6>
										</div>
									</div>

									<div class="input-group form-group container-full">
										<label class="input-group-addon el-hidden">Is the Channel OTT?</label>
										<select name="is_channel_ott" id="is_channel_ott" class="form-control required" required="required">
											<option value="">Is the Channel OTT? Please select</option>
											<option value="yes">Yes</option>
											<option value="no">No</option>
										</select>
									</div>

									<div class="ott-yes el-hidden">
										<div class="input-group form-group container-full">
											<label class="input-group-addon el-hidden">Source URL</label>
											<input name="source_url" class="form-control" type="text" value="" placeholder="Source URL" />
										</div>

										<div class="input-group form-group container-full">
											<label class="input-group-addon el-hidden">Technical Encoded URL</label>
											<input name="technical_encoded_url" class="form-control" type="text" value="" placeholder="Technical Encoded URL" />
										</div>
									</div>

									<div class="ott-no el-hidden">
										<div class="input-group form-group container-full">
											<label class="input-group-addon el-hidden">Satellite Sources</label>
											<input name="satelite_sources" class="form-control" type="text" value="" placeholder="Satellite Sources" />
										</div>

										<div class="input-group form-group container-full">
											<label class="input-group-addon el-hidden">Regions</label>
											<input name="regions" class="form-control" type="text" value="" placeholder="Regions" />
										</div>
									</div>

									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button class="btn-block btn-back" data-currentpanel="channel_creation_panel5" type="button">Back</button>
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
$( document ).ready( function(){
	$( "#is_channel_ott" ).on( "change", function(){
		if( $( this ).val() == "yes" ){
			$( ".ott-yes" ).show();
			$( ".ott-no" ).hide();
			$( ".ott-no" ).each( function( p ){
				$(  )
			})

		} else {
			$( ".ott-yes" ).hide();
			$( ".ott-no" ).show();
		}
	});

	$( "input[name='channel_name']" ).on( "input", function(){
		title_text = $( "*[name = 'channel_name' ]" ).val().replace( /[^a-z0-9]+/gi, "" ).toLowerCase();
		$("*[name = 'channel_reference_code' ]").val( title_text );
	});


	//Submit channel form
	$( '#channel-creation-form' ).on( "submit", function( e ){
		e.preventDefault();
		var formData = $( '#channel-creation-form' ).serialize();

		swal({
			title: 'Confirm new channel creation?',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url( 'webapp/channel/create_channel/' ); ?>",
					method: "POST",
					data: formData,
 					dataType: "JSON",
					success:function( data ){

						console.log( data );
						if( data.status == 1 && ( data.channel !== '' ) ){
							var newchannelId = data.channel.channel_id;
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 2000
							})
							window.setTimeout( function(){
								location.href = "<?php echo base_url( 'webapp/channel/profile/' ); ?>" + newchannelId;
							}, 2000 );
						} else {
							swal({
								type: 'error',
								title: data.status_msg
							})
						}
					}
				});
			} else {
				$( ".channel_creation_panel5" ).hide( "slide", { direction : 'left' }, 500 );
				go_back( ".channel_creation_panel1" );
				return false;
			}
		}).catch( swal.noop )
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

 	$( ".channel-creation-steps" ).click( function(){
		$( '.error_message' ).each( function(){
			$( this ).text( '' );
		});

		var currentpanel = $( this ).data( "currentpanel" );
		var inputs_state = check_inputs( currentpanel );
		if( inputs_state ){
			//If name attribute returned, auto focus to the field and display an error message
			$( '[name="'+inputs_state+'"]' ).focus();
			var labelText = $( '[name="'+inputs_state+'"]' ).parent().find( 'label' ).text();
			console.log( labelText );
			labelText = ( labelText.length > 0 ) ? labelText : "Field ";
			console.log( labelText );
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
			var fieldName 	= '';
			var inputValue 	= $( this ).val();
			var input 		= $( this );

			if( ( inputValue == false ) || ( inputValue == '' ) || ( inputValue.length == 0 ) ){
				fieldName 	= $( this ).attr( 'name' );
				result 		= fieldName;
				return result;
			}

			if( $( input ).is( ':checkbox' ) ){
				fieldName 	= $( this ).attr( 'name' );

				if( !( $( "input:checked" ).length > 0 ) ){
					result 		= fieldName;
					return result;
				}
			}
		});

		return result;
	}

	$( ".btn-next" ).click( function(){
		
		var currentPanelLabel = $( this ).data( "currentpanel" );
		
		var currentpanel = $( "." + currentPanelLabel );
		prev_group_is_valid = true;

		if( currentPanelLabel == "channel_creation_panel3" ){
			var territoriesList = $( 'input[name="channel_territories[]"]:checked' );
			if( !( territoriesList.length > 0 ) ){
				prev_group_is_valid = false;
			}
		} else {
			currentpanel.find( "input, select" ).each( function( i, input_element ){
				if ( $( input_element ).hasClass( "required" ) ){

					if( $( input_element ).is( ':checkbox' ) ){
						if( !( $( "input_element:checked" ).length > 0 ) ){
							prev_group_is_valid = false;
						}
					} else {
						if( $( input_element ).val() == "" || $( input_element ).val() == false || $( input_element ).val().length == 0 ){
							prev_group_is_valid = false;
						}
					}
				}
			});
		}

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
		var changeto = ".channel_creation_panel" + panelnumber;
		$( changefrom ).hide( "slide", {direction : 'left'}, 500 );
		$( changeto ).delay( 600 ).show( "slide", {direction : 'right'},500 );
		return false;
	}

	function go_back( changefrom ){
		var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
		var changeto = ".channel_creation_panel" + panelnumber;
		$( changefrom ).hide( "slide", {direction : 'right'}, 500 );
		$( changeto ).delay( 600 ).show( "slide", {direction : 'left'},500 );
		return false;
	}
});
</script>