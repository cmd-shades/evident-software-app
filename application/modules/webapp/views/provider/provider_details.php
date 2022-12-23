<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<form id="update-provider-form" method="post" >
		<input type="hidden" name="page" value="details" />
		<input type="hidden" name="provider_id" value="<?php echo $provider_details->provider_id; ?>" />
		<div class="x_panel tile group-container">
			<h4 class="legend"><i class="fas fa-caret-down"></i>Provider Details</h4>
			<?php include( "includes/provider_details_inc.php" ); ?>
		</div>
	</form>

	<div class="x_panel tile group-container territories">
		<span class="add-territory"><a type="button" data-toggle="modal" data-target="#addTerritory"><i class="fas fa-plus-circle"></i></a></span>
		<h4 class="legend"><i class="fas fa-caret-down"></i>Territories</h4>
		<?php include( "includes/territories.php" ); ?>
		
	</div>

	<div class="x_panel tile group-container provider-documents">
		<h4 class="legend"><i class="fas fa-caret-down"></i>Provider Documents</h4>
		<?php include( "includes/provider_documents.php" ); ?>
	</div>

	<div class="x_panel tile group-container airtime-plan">
		<span class="add-price-plan"><a type="button" data-toggle="modal" data-target="#addAirtimePlan"><i class="fas fa-plus-circle"></i></a></span>
		<h4 class="legend"><i class="fas fa-caret-down"></i>Airtime Plans</h4>
		<?php include( "includes/airtime_plans.php" ); ?>
	</div>

	<div class="x_panel tile group-container packet-identifiers">
		<span class="add-identifier"><a type="button" data-toggle="modal" data-target="#addIdentifier"><i class="fas fa-plus-circle"></i></a></span>
		<input type="hidden" name="provider_id" value="<?php echo $provider_details->provider_id; ?>" />
		<h4 class="legend"><i class="fas fa-caret-down"></i>Technical Specification</h4>
		<?php include( "includes/technical_specification.php" ); ?>
	</div>
	
<?php 
if( !empty( $royalty_settings ) ){ ?>
	<div class="x_panel tile group-container report-settings">
		<h4 class="legend"><i class="fas fa-caret-down"></i>Report Settings</h4>
		<div class="row group-content el-hidden">
			<?php 
			if( !empty( $royalty_settings ) ){ ?>
				<form id="update-report-settings-form" method="post" >
					<!-- <span class="add-setting"><a type="button" data-toggle="modal" data-target="#addSetting"><i class="fas fa-plus-circle"></i></a></span> -->
					<input type="hidden" name="provider_id" value="<?php echo $provider_details->provider_id; ?>" />
					<input type="hidden" name="report_category_id" value="<?php echo ( !empty( $report_category_id ) ) ? $report_category_id : '' ; ?>" />
					<input type="hidden" name="report_type_id" value="<?php echo ( !empty( $report_type_id ) ) ? $report_type_id : '' ; ?>" />
					<?php include( "includes/report_settings.php" ); ?>
				</form>
			<?php 
			} else { ?>
				<div class="row group-content">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 input-container">
						<legend class="default-legend">No setting(s) available</legend>
					</div>
				</div>
			<?php 
			} ?>
		</div>
	</div>
	<?php 
}	?>
</div>


<!-- Modal To Add Airtime Plan -->
<div class="modal fade" id="addAirtimePlan" tabindex="-1" role="dialog" aria-labelledby="addAirtimePlan" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<?php $this->view( 'provider/includes/add_airtime_plan.php' ); ?>
		</div>
	</div>
</div>


<!-- Modal To Add Provider modal Manually -->
<div class="modal fade" id="addTerritory" tabindex="-1" role="dialog" aria-labelledby="addTerritory" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<?php $this->view( 'provider/includes/add_territory.php' ); ?>
		</div>
	</div>
</div>

<!-- Modal To Add Language Code modal Manually -->
<div class="modal fade" id="addIdentifier" tabindex="-1" role="dialog" aria-labelledby="addIdentifier" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<?php $this->view( 'provider/includes/add_identifier.php' ); ?>
		</div>
	</div>
</div>

<div class="modal fade" id="editIdentifier" tabindex="-1" role="dialog" aria-labelledby="editIdentifier" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<div class="row">
					<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
						<h4 class="modal-title">Provider Packet Identifier Details (ID: <span class="idenfifier_id_in_modal"></span>)</h4>
					</div>
				</div>
			</div>

			<div class="modal-body">
				<div class="rows group-content">
					<form id="update-identifier-form">

					</form>
				</div>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">
$( document ).ready( function(){
	
	$( "#update-report-settings-btn" ).on( "click", function( e ){
		e.preventDefault();

		var formData = $( "#update-report-settings-form" ).serialize();

		$.ajax({
			url: "<?php echo base_url( 'webapp/provider/update_report_settings/' ); ?>",
			method: "POST",
			data: formData,
			dataType: 'json',
			success: function( data ){
				if( data.status == 1 && ( data.report_settings !== '' ) ){
					swal({
						type: 'success',
						title: data.status_msg,
						showConfirmButton: false,
						timer: 2000
					})
					window.setTimeout( function(){
						location.reload();
					}, 2000 );

				} else {
					swal({
						type: 'error',
						title: data.status_msg
					})
					return false;
				}
			}
		});
	});
	
	
	$( ".delete-price-plan" ).on( "click", function( e ){
		e.preventDefault();
		var providerPlanId = $( this ).data( "provider_plan_id" );

		swal({
			title: 'Confirm deleting Price Plan?',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url( 'webapp/provider/delete_provider_price_plan/' ); ?>",
					method: "POST",
					data: {
						provider_plan_id: providerPlanId
					},
					dataType: 'json',
					success:function( data ){
						if( data.status == 1 ){
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 2000
							})
							window.setTimeout( function(){
								 location.reload();
							}, 2000 );
						} else {
							swal({
								type: 'error',
								title: data.status_msg
							})
							return false;
						}
					}
				});
			} else {
				return false;
			}
		}).catch( swal.noop )
	})



	$( "#adding-airtime-plans-to-provider-form" ).on( "submit", function( e ){
		e.preventDefault();

		var formData = $( this ).serialize();

		$.ajax({
			url: "<?php echo base_url( 'webapp/provider/add_price_plan' ); ?>",
			method: "POST",
			data: formData,
			dataType: 'json',
			success: function( data ){
				if( data.status == 1 && ( data.provider_plan_id !== '' ) ){
					swal({
						type: 'success',
						title: data.status_msg,
						showConfirmButton: false,
						timer: 2000
					})
					window.setTimeout( function(){
						location.reload( true );
					}, 2000 );
				} else {
					swal({
						type: 'error',
						title: data.status_msg
					})
					return false;
				}
			}
		});
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


	$( ".delete-identifier" ).on( "click", function(){
		var identifierId = $( this ).data( "identifier_id" );
		swal({
			title: 'Confirm Packet Identifier delete?',
			// type: 'question',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function( result ){
			if ( result.value ) {
				$.ajax({
					url: "<?php echo base_url( 'webapp/provider/delete_provider_pid/' ); ?>",
					method: "POST",
					data: {
						identifier_id:identifierId
					},
					dataType: 'json',
					success: function( data ){
						if( data.status == 1 ){
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 2000
							})
							window.setTimeout( function(){
								location.href = "<?php echo ( !empty( $provider_details->provider_id ) ) ? base_url( 'webapp/provider/profile/'.$provider_details->provider_id ) : base_url( 'webapp/provider/' ) ; ?>";
							}, 2000 );

						} else {
							swal({
								type: 'error',
								title: data.status_msg
							})
							return false;
						}
					}
				});
			}
		}).catch( swal.noop )
	});

	$( "#update-identifier-form" ).on( "submit", function( e ){
		e.preventDefault();

		var formData = $( this ).serialize();

		$.ajax({
			url: "<?php echo base_url( 'webapp/provider/update_provider_pid/' ); ?>",
			method: "POST",
			data: formData,
			dataType: 'json',
			success: function( data ){
				if( data.status == 1 && ( data.updated_identifier !== '' ) ){
					swal({
						type: 'success',
						title: data.status_msg,
						showConfirmButton: false,
						timer: 2000
					})
					window.setTimeout( function(){
						 location.href = "<?php echo ( !empty( $provider_details->provider_id ) ) ? base_url( 'webapp/provider/profile/'.$provider_details->provider_id ) : base_url( 'webapp/provider/' ) ; ?>";
					}, 2000 );

				} else {
					swal({
						type: 'error',
						title: data.status_msg
					})
					return false;
				}
			}
		});
	});


	$( ".edit-identifier" ).on( "click", function(){
		var identifierId = $( this ).data( "identifier_id" );
		$( ".idenfifier_id_in_modal" ).empty().text( identifierId );

		$.ajax({
			url: "<?php echo base_url( 'webapp/provider/edit_pid_modal/' ); ?>",
			method: "POST",
			data: {
				identifier_id:identifierId
			},
			dataType: 'json',
			success: function( data ){
				if( data.status == 1 && ( data.identifier !== '' ) ){
					$( "#update-identifier-form" ).empty().append( data.identifier );
				} else {
					swal({
						type: 'error',
						title: data.status_msg
					})
					return false;
				}
			}
		});
	});


	$( "#adding-packet-identifier-form" ).on( "submit", function( e ){
		e.preventDefault();

		var formData = $( this ).serialize();

		$.ajax({
			url: "<?php echo base_url( 'webapp/provider/add_pid_to_provider/' ); ?>",
			method: "POST",
			data: formData,
			dataType: 'json',
			success: function( data ){
				if( data.status == 1 && ( data.added_identifier !== '' ) ){
					swal({
						type: 'success',
						title: data.status_msg,
						showConfirmButton: false,
						timer: 2000
					})
					window.setTimeout( function(){
						 location.href = "<?php echo ( !empty( $provider_details->provider_id ) ) ? base_url( 'webapp/provider/profile/'.$provider_details->provider_id ) : base_url( 'webapp/provider/' ) ; ?>";
					}, 2000 );
				} else {
					swal({
						type: 'error',
						title: data.status_msg
					})
					return false;
				}
			}
		});
	});


	$( '*[name="definition_id"], *[name="type_id"], *[name="packet_identifier_id"]' ).on( "change", function(){
		var definition 	= $( '*[name="definition_id"] option:selected' ).text();
		var type 		= $( '*[name="type_id"] option:selected' ).text();
		var packet 		= $( '#adding-packet-identifier-form *[name="packet_identifier_id"] option:selected' ).text();

		if( definition == 'Please select' ){ definition = 'Not specified' };
		if( type == 'Please select' ){ type = 'Not specified' };
		if( packet == 'Please select' ){ packet = 'Not specified' };

		var summary = definition + ' - ' + type + ' - ' + packet.substring( ( packet.indexOf( "|", 4 ) + 2 ) );

		$( ".summary" ).empty().append( summary );
	});


	$( ".packet-adding-steps" ).click( function(){
		$( '.error_message' ).each( function(){
			$( this ).text( '' );
		});

		var currentpanel = $( this ).data( "currentpanel" );
		// If true - there are errors
		var inputs_state = check_inputs( currentpanel );
		if( inputs_state ){
			//If name attribute returned, auto focus to the field and display arror message
			$( '[name="'+inputs_state+'"]' ).focus();
			var labelText = $( '[name="' + inputs_state + '"]' ).parent().find( 'label' ).text();
			$( '#' + currentpanel + '-errors' ).text( ucwords( labelText ) + ' is a required' );
			return false;
		}

		var elementClass = ".packet_identifier_adding_panel";
		panelchange( "." + currentpanel, elementClass )
		return false;
	});


	$( 'input[name="is_provider_a_channel"]' ).on( "click", function(){
		var fieldValue = $( 'input[name="is_provider_a_channel"]:checked' ).val();
		if( fieldValue == 'yes' ){
			if( $( ".channel-container" ).hasClass( 'el-hidden' ) ){
				$( ".channel-container" ).removeClass( 'el-hidden' ).addClass( 'el-shown' );
			}

		} else {
			if( $( ".channel-container" ).hasClass( 'el-shown' ) ){
				$( ".channel-container" ).removeClass( 'el-shown' ).addClass( 'el-hidden' );
			}
		}
	});


	// Airtime creation section
	$( ".airtime-adding-steps" ).click( function(){
		$( '.error_message' ).each( function(){
			$( this ).text( '' );
		});

		var currentpanel = $( this ).data( "currentpanel" );

		if( currentpanel == "adding-airtime-plans-to-provider1" ){
			if( !( $( 'input[name="is_provider_a_channel"]:checked' ).length > 0 ) ){ // none picked
				$( "#adding-airtime-plans-to-provider1-errors" ).text( "Is the Provider a Channel - field is a required" );
				return false;
			} else {  // one picked
				if( $( 'input[name="is_provider_a_channel"]:checked' ).val() == "yes" ){
					if( $( '[name="channel_id"]' ).val() > 0 ){
						var elementClass = ".adding-airtime-plans-to-provider";
						panelchange( "." + currentpanel, elementClass )
						return false;
					} else {
						$( "#adding-airtime-plans-to-provider1-errors" ).text( "Channel Name field is a required" );
					}
				} else {
					var elementClass = ".adding-airtime-plans-to-provider";
					panelchange( "." + currentpanel, elementClass )
					return false;
				}
			}
		} else {

			// If true - there are errors
			var inputs_state = check_inputs( currentpanel );
			if( inputs_state ){
				//If name attribute returned, auto focus to the field and display arror message
				$( '[name="'+inputs_state+'"]' ).focus();
				var labelText = $( '[name="' + inputs_state + '"]' ).parent().find( 'label' ).text();
				$( '#' + currentpanel + '-errors' ).text( ucwords( labelText ) + ' is a required' );
				return false;
			}

			var elementClass = ".adding-airtime-plans-to-provider";
			panelchange( "." + currentpanel, elementClass )
			return false;
		}
	});


	//** Validate any inputs that have the required class, if empty return the name attribute **/
	function check_inputs( currentpanel ){
		var result = false;
		var panel = "." + currentpanel;

		$( $( panel + " .required" ).get().reverse() ).each( function(){
			var fieldName 	= '';
			var inputValue 	= $( this ).val();
			if( ( inputValue === false ) || ( inputValue == '' ) || ( inputValue.length == 0 ) ){
				fieldName 	= $(this).attr( 'name' );
				result 		= fieldName;
			}
		});

		return result;
	}


	$( ".btn-back" ).click( function(){
		var currentpanel = $( this ).data( "currentpanel" );
		go_back( "." + currentpanel )
		return false;
	});


	function panelchange( changefrom, elementClass, changeto ){
		var panelnumber = parseInt( changefrom.match(/\d+/) ) + parseInt( 1 );
		var changeto = elementClass + panelnumber;
		$( changefrom ).hide( "slide", {direction : 'left'}, 500 );
		$( changeto ).delay( 600 ).show( "slide", {direction : 'right'},500 );
		return false;
	}


	function go_back( changefrom ){
		var panelnumber = parseInt( changefrom.match(/\d+/) ) - parseInt( 1 );
		var elementClass = changefrom.substr( 0, parseInt( changefrom.length ) - parseInt( panelnumber.toString().length ) );
		var changeto = elementClass + panelnumber;
		$( changefrom ).hide( "slide", {direction : 'right'}, 500 );
		$( changeto ).delay( 800 ).show( "slide", {direction : 'left'},500 );
		return false;
	}


	// function to load Packet Identifiers which match criteria: definition id and PID category id
	$( '*[name="definition_id"], *[name="type_id"]' ).on( "change", function( e ){
		e.preventDefault();

		var definitionID 	= $( '*[name="definition_id"]' ).val();
		var typeID 		= $( '*[name="type_id"]' ).val();

		$.ajax({
			url: "<?php echo base_url( 'webapp/provider/packet_identifiers/' ); ?>",
			method:"POST",
			data: {
				definition_id: definitionID,
				type_id: typeID,
			},
			dataType: 'json',
			success: function( data ){
				if( data.status == 1 && data.options.length > 0 ){

					var element = $( 'select[name="packet_identifier_id"]' );
					element.empty(); // remove old options
					element.append( data.options );

				} else {
					swal({
						type: 'error',
						title: data.status_msg
					})
				}
			}
		});
	});


	// Trigger to show Provider URL field only if Category is 'channel'
	var providerCategoryTrigger = $( "[name='content_provider_category_id']" );
	$( providerCategoryTrigger ).on( "change", function(){
		if( $( $( "option:selected", this ) ).data("setting_group_name") == "channel" ){
			$( ".provider_url_container" ).css( "display", "block" );
		} else {
			$( ".provider_url_container" ).css( "display", "none" );
		}
	});


	$( ".delete-file" ).click( function( e ){
		e.preventDefault();

		var documentID = $( this ).data( 'document_id' );

		swal({
			title: 'Confirm document delete?',
			// type: 'question',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function( result ){
			if ( result.value ) {
				if( parseInt( documentID ) < 0 ){
					swal({
						title: 'Document ID is required',
						type: 'error',
					})
					return false;
				}

				$.ajax({
					url: "<?php echo base_url( 'webapp/provider/delete_document/' ); ?>",
					method:"POST",
					data: { document_id: documentID },
					dataType: 'json',
					success:function( data ){
						if( data.status == 1 ){
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 2000
							})
							window.setTimeout( function(){
								location.reload( true );
							}, 2000 );
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


	$( ".delete_container" ).click( function(){
		swal({
			title: 'Confirm provider delete?',
			// type: 'question',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
			if ( result.value ) {

				var prov_id = <?php echo $provider_details->provider_id; ?>;
				if( parseInt( prov_id ) < 0 ){
					swal({
						title: 'Provider ID is required',
						type: 'error',
					})
					return false;
				}

				$.ajax({
					url:"<?php echo base_url( 'webapp/provider/delete_provider/'.$provider_details->provider_id ); ?>",
					method:"POST",
					data: {provider_id: prov_id},
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
								location.href ="<?php echo base_url( "webapp/provider" ); ?>";
							}, 3000 );
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


	$( ".legend" ).click( function(){
		$( this ).children( ".fas" ).toggleClass( "fa-caret-down fa-caret-up" );
		$( this ).next( ".group-content" ).slideToggle( 400 );
	});


	$( ".delete-territory" ).on( "click", function( e ){
		e.preventDefault();
		var territoryID = $( this ).data( "territory_id" );

		swal({
			title: 'Confirm deleting Territory?',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url( 'webapp/provider/delete_territory/' ); ?>",
					method: "POST",
					data: {
						territory_id: territoryID
					},
					dataType: 'json',
					success:function( data ){
						if( data.status == 1 ){
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 2000
							})
							window.setTimeout( function(){
								 location.reload();
							}, 2000 );
						} else {
							swal({
								type: 'error',
								title: data.status_msg
							})
							return false;
						}
					}
				});
			} else {
				return false;
			}
		}).catch( swal.noop )
	})
	

	//Submit form for processing
	$( '#update-provider-btn' ).click( function( event ){
		event.preventDefault();
		var formData = $( '#update-provider-form' ).serialize();
		swal({
			title: 'Confirm provider update?',
			// type: 'question',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url( 'webapp/provider/update/'.$provider_details->provider_id ); ?>",
					method:"POST",
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
							}, 3000 );
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



	$( '#adding-territory-to-provider-form' ).on( "submit", function( e ){
		e.preventDefault();
		var formData = $( '#adding-territory-to-provider-form' ).serialize();

		swal({
			title: 'Confirm adding Territory?',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url( 'webapp/provider/add_territory/' ); ?>",
					method: "POST",
					data: formData,
					dataType: 'json',
					success:function( data ){
						if( data.status == 1 && ( data.new_territory !== '' ) ){
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 2000
							})
							window.setTimeout( function(){
								 location.reload();
							}, 2000 );
						} else {
							swal({
								type: 'error',
								title: data.status_msg
							})
							return false;
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