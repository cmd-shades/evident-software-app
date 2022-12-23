<div id="settings-group" class="row">
	<div class="x_panel no-border">
		<div class="row">
			<div class="x_content">
				<div class="row">
					<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
						<h2><?php echo ( !empty( $modules->module_name ) ) ? $modules->module_name : '' ; ?> Settings<span class="add-setting-plus"><a type="button" class="create-setting-modal" data-toggle="modal" data-setting_name_id="" data-target="#create-setting-modal"><i class="fas fa-plus-circle"></i></a></span></h2>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-2 col-md-2 col-sm-3 col-xs-4">
						<!-- <h4>Setting Item</h4> -->
						<div id="settings_within_group">
							<?php if( !empty( $setting_names ) ){
								foreach( $setting_names as $group ){ ?>
									<a class="item_link" data-setting_name_id="<?php echo $group->setting_name_id; ?>" data-setting_name="<?php echo $group->setting_name; ?>"><?php echo ( !empty( $group->setting_name ) ) ? $group->setting_name : '' ;?></a>
								<?php
								}
							} ?>
						</div>
						<?php
						if( $modules->module_id == 5 ){ ?>
							<div id="settings_outside_group">
								<a class="territories_link" data-setting_name="Territories">Territories</a>
							</div>
						<?php
						} else if( $modules->module_id == 2 ){ ?>
							<div id="settings_outside_group">
								<a class="distribution_server_link" data-setting_name="Distribution Servers">Distribution Servers</a>
							</div>
						<?php
						} else if( $modules->module_id == 3 ){ ?>
							<div id="settings_outside_group">
								<p class="genre-paragraph paragraph">Genres</p>
								<?php 
								if( !empty( $genre_types ) ){
									foreach( $genre_types as $type ){ ?>
										<a class="genres_link item subgroup" data-genre_type_id="<?php echo ( !empty( $type->type_id ) ) ? $type->type_id : 0 ; ?>" data-genre_type_name="<?php echo ( !empty( $type->type_name ) ) ? $type->type_name : "" ; ?>" data-easel_id="<?php echo ( !empty( $type->easel_id ) ) ? $type->easel_id : '' ; ?>"><?php echo ( !empty( $type->type_name ) ) ? $type->type_name : '' ; ?></a>
									<?php
									} 
								} else { ?>
									<span class="warning">No genre type set</span>
								<?php 
								} ?>
								
								<p class="age-classifications-paragraph paragraph">Age Classification/Rating</p>
								<?php 
								if( !empty( $age_classifications ) ){
									foreach( $age_classifications as $classification ){ ?>
										<a class="age_classification_link item subgroup" data-age_classification_id="<?php echo ( !empty( $classification->age_classification_id ) ) ? $classification->age_classification_id : 0 ; ?>" data-age_classification_name="<?php echo ( !empty( $classification->age_classification_name ) ) ? $classification->age_classification_name : "" ; ?>" data-easel_id="<?php echo ( !empty( $classification->easel_id ) ) ? $classification->easel_id : '' ; ?>"><?php echo ( !empty( $classification->age_classification_name ) ) ? $classification->age_classification_name : '' ; ?></a>
									<?php
									} 
								} else { ?>
									<span class="warning">No classifications set</span>
								<?php 
								} ?>
							</div>
						<?php
						} ?>
					</div>
					<div class="col-lg-10 col-md-10 col-sm-9 col-xs-8">
						<div id="settings_values"></div>
						<div id="territories_values"></div>
						<div id="distribution_server"></div>
						<div id="genres"></div>
						<div id="age_rating"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div id="server-profile-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="updateServersProfile" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div id="update_server_modal"></div>
            </div>
        </div>
    </div>
</div>


<div id="setting-profile-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="settingProfile" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div id="update_setting_modal" method="post"></div>
            </div>
        </div>
    </div>
</div>


<div id="territory-profile-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="territoryProfile" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div id="update_territory_modal" method="post"></div>
            </div>
        </div>
    </div>
</div>


<div id="create-setting-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="createSettingModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
				<?php include( "includes/setting-creation-form.php" ); ?>
            </div>
        </div>
    </div>
</div>


<div id="create-territory-modal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
				<!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> -->
                <form id="territory-creation-form">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" data-panel-index="1">
							<div class="slide-group">
								<div class="row">
									<div class="col-md-6 col-sm-6 col-xs-12">
										<legend class="legend-header">What are the Values?</legend>
									</div>
									<div class="col-md-6 col-sm-6 col-xs-12">
										<h6 class="error_message pull-right" id="setting_creation_panel2-errors"></h6>
									</div>
								</div>

								<div class="value_attributes">
									<div class="input-group full-width" style="float: left;">
										<label class="input-group-addon el-hidden">Country</label>
										<input name="country" class="input-field-full container-full" type="text" value="" placeholder="Country" title="Country" />
									</div>

									<div class="input-group full-width" style="float: left;">
										<label class="input-group-addon el-hidden">Code</label>
										<input name="code" class="input-field-full container-full" type="text" value="" placeholder="Code" title="Code" />
									</div>
								</div>

								<div class="row">
									<div class="col-md-6 col-sm-6 col-xs-12">
										<button class="btn-block btn-back" data-currentpanel="setting_creation_panel2" type="button">Back</button>
									</div>
									<div class="col-md-6 col-sm-6 col-xs-12">
										<button class="btn-block btn-flow btn-next" type="submit">Submit</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
            </div>
        </div>
    </div>
</div>

<div id="create-genre-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="create-genre-modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
				<?php include( "includes/genre-creation-form.php" ); ?>
            </div>
        </div>
    </div>
</div>

<div id="create-distribution-server-modal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
				<!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> -->
                <form id="distribution-server-creation-form">
					<?php include( "includes/distribution-server-creation-form.php" ); ?>
				</form>
            </div>
        </div>
    </div>
</div>

<!-- This has been prepared for the functionality which will be added in the future -->
<div class="el-hidden" id="create-age-rating-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="create-genre-modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
				<?php ## include( "includes/genre-creation-form.php" ); ?>
            </div>
        </div>
    </div>
</div>

<div id="addPoint" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addPoint" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
				<?php include( "includes/notification-point-adding-form.php" ); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$( document ).ready( function(){
	var last_element = "";
	
	
	$( "#server-profile-modal" ).on( "click", "#deleteServerBtn", function( e ){
		e.preventDefault();
		
		var serverId = $( this ).data( "server_id" );
		
		swal({
			title: "Confirm deleting the Server",
			html: '<span style="color:#ff0000;">The server will be archived but notification point(s) will be removed permanently!</span>',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function( result ){
			if( result.value ){
				$.ajax({
					url: "<?php echo base_url( 'webapp/settings/delete_server/' ); ?>",
					method: "POST",
					dataType: "json",
					data: {
						server_id: serverId
					},
					success: function( data ){
						if( data.status != false ){
							swal({
								type: 'success',
								title: data.status_msg
							})
						} else {
							swal({
								type: 'error',
								title: data.status_msg
							})
						}
						window.setTimeout( function(){
							location.reload();
						}, 3000 );
					}
				});
			}
		}).catch( swal.noop )
	});
	
	
	$( "#server-profile-modal" ).on( "submit", "#servers_update_in_modal", function( e ){
		e.preventDefault();
		swal({
			title: "Confirm updating the Server",
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function( result ){
			if( result.value ){
				var formData = $( "#servers_update_in_modal" ).serialize();
				
				$.ajax({
					url:"<?php echo base_url( 'webapp/settings/update_server/' ); ?>",
					method: "POST",
					dataType: "json",
					data: formData,
					success:function( data ){
						if( data.status != false ){
							swal({
								type: 'success',
								title: data.status_msg
							})
						} else {
							swal({
								type: 'error',
								title: data.status_msg
							})
						}
						window.setTimeout( function(){
							location.reload();
						}, 3000 );
					}
				});
			}
		}).catch( swal.noop );
	});
		
	$( "#adding_point-form" ).on( "submit", function( e ){
		e.preventDefault();
	
		swal({
			title: "Confirm adding Notification Point",
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function( result ){
			if ( result.value ){
				
				var formData = $( "#adding_point-form" ).serialize();
				
				$.ajax({
					url:"<?php echo base_url( 'webapp/settings/add_notification_point/' ); ?>",
					method: "POST",
					dataType: "json",
					data: formData,
					success:function( data ){
						if( data.status != false ){
							swal({
								type: 'success',
								title: data.status_msg
							})
						} else {
							swal({
								type: 'error',
								title: data.status_msg
							})
						}
						window.setTimeout( function(){
							location.reload();
						}, 3000 );
					}
				});
			}
		}).catch( swal.noop );
	});
	
	
	$( "#adding_point_back_button" ).on( "click", function( e ){
		$( '#addPoint' ).modal( 'hide' );
		$( ".clickable[data-server_id=" + last_element + "]" ).trigger( 'click' );
		
	});
	
	$( "#update_server_modal" ).on( "click", ".add-point", function( e ){
		var server_idv 	= $( this ).data( 'server_id' );
		last_element 	= server_idv;
		$( '#adding_point-form input[name="server_id"]' ).val( last_element );   // ??????
		$( '#server-profile-modal' ).modal( 'toggle' );
	});
		
	$( "#update_server_modal" ).on( "click", ".delete_point", function(){
		var point_id = $( this ).data( "point_id" );
		
		swal({
			title: "Confirm removing the Notification Point",
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function( result ){
			if ( result.value ){
				$.ajax({
					url:"<?php echo base_url( 'webapp/settings/delete_notification_point/' ); ?>",
					method: "POST",
					dataType: "json",
					data: {
						pointId: point_id
					},
					success:function( data ){
						if( data.status != false ){
							swal({
								type: 'success',
								title: data.status_msg
							})
						} else {
							swal({
								type: 'error',
								title: data.status_msg
							})
						}
						
						window.setTimeout( function(){
							location.reload();
						}, 3000 );
					}
				});
			}
		}).catch( swal.noop );
	});
	
	
	$( "#genre-creation-form" ).on( "submit", function( e ){
		e.preventDefault();
		
		swal({
			title: "Confirm creating the Genre item",
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function( result ){
			if ( result.value ) {
				var formData = $( "#genre-creation-form" ).serialize();
				$.ajax({
					url:"<?php echo base_url( 'webapp/settings/create_genre' ); ?>",
					method: "POST",
					dataType: "json",
					data: formData,
					success:function( data ){
						if( data.status != false ){
							swal({
								type: 'success',
								title: data.status_msg
							})
						} else {
							swal({
								type: 'error',
								title: data.status_msg
							})
						}
						window.setTimeout( function(){
 							location.reload();
						}, 3000 );
					}
				});
			}
		}).catch( swal.noop );
	});
	
	
	$( "#genres" ).on( "click", ".create-genre-modal", function(){
		var genreTypeId = $( this ).data( "genre_type_id" ),
			genreTypeName = $( this ).data( "genre_type_name" );
		
		$( "#genre-creation-form input[name='genre_type_id']" ).val( genreTypeId );
		$( "#genre-creation-form span.genre_type" ).empty().html( genreTypeName );

		$( "#create-genre-modal" ).modal( 'show' );
	});
	
	
	// $( "#age_rating" ).on( "click", ".create-age_rating-modal", function(){
		// var ageClassificationId 	= $( this ).data( "age_classificatione_id" ),
			// ageClassificationName 	= $( this ).data( "age_classificatione_name" );
		
		// $( "#age_rating-creation-form input[name='age_classificatione_id']" ).val( ageClassificationId );
		// $( "#age_rating-creation-form span.age_classificatione_name" ).empty().html( ageClassificationName );

		// $( "#create-age_rating-modal" ).modal( 'show' );
	// });

	$( "#distribution_server" ).on( "click", ".create-distribution-server-modal", function(){

		$.ajax({
			url:"<?php echo base_url( 'webapp/settings/get_available_servers/' ); ?>",
			method: "POST",
			success:function( data ){
				$( "#coggins_server_id" ).html( data );
			}
		});
		
		$( '#coggins_server_id' ).select2({
			dropdownParent: $( "#create-distribution-server-modal" ),
			placeholder: 'Choose the server',
			selectOnClose: true,
			delay: 150,
		});
		
		$( "#create-distribution-server-modal" ).modal( 'show' );
	});


	$( '.setting-trigger' ).on( "change", function(){
		var trigger = $( this ).find( ':selected' ).data( 'trigger' );
		if( ( trigger !== undefined ) && ( trigger == 'yes' ) ){
			$( ".new-setting-name" ).removeClass( "el-hidden" );
			$( ".new-setting-name" ).addClass( "el-shown" );
		} else {
			$( ".new-setting-name" ).removeClass( "el-shown" );
			$( ".new-setting-name" ).addClass( "el-hidden" );
		}
	});

	$( ".modal" ).on( "hidden.bs.modal", function(){
		var inputs = $( this ).find( "input, textarea" );
		$.each( inputs, function(){
			$( this ).val( '' );
		});

		$( '#setting-creation-form select.resetable' ).each( function(){
			$( this ).val( '' );
		});

		var checkboxes = $( this ).find( "input[type=checkbox], input[type=radio]" );
		$.each( checkboxes, function(){
			$( this ).prop( "checked", "" );
		});

		$( "#outputArea" ).empty()
		go_back( ".setting_creation_panel2" );
	})

	$( "#setting-creation-form" ).on( "submit", function( e ){
		e.preventDefault();

		swal({
			title: "Confirm creating the Setting",
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function( result ) {
			if ( result.value ) {
				var formData = $( "#setting-creation-form" ).serialize();
				$.ajax({
					url:"<?php echo base_url( 'webapp/settings/create_setting/' ); ?>",
					method: "POST",
					dataType: "json",
					data: formData,
					success:function( data ){
						if( data.status != false ){
							swal({
								type: 'success',
								title: data.status_msg
							})
						} else {
							swal({
								type: 'error',
								title: data.status_msg
							})
						}
						window.setTimeout( function(){
							location.reload();
						}, 3000 );
					}
				});
			}
		}).catch( swal.noop );
	});


	$( "#territory-creation-form" ).on( "submit", function( e ){
		e.preventDefault();

		swal({
			title: "Confirm adding Territory",
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function( result ) {
			if ( result.value ) {
				var formData = $( "#territory-creation-form" ).serialize();
				$.ajax({
					url:"<?php echo base_url( 'webapp/settings/add_territory/' ); ?>",
					method: "POST",
					dataType: "json",
					data: formData,
					success:function( data ){
						if( data.status != false ){
							swal({
								type: 'success',
								title: data.status_msg
							})
						} else {
							swal({
								type: 'error',
								title: data.status_msg
							})
						}
						window.setTimeout( function(){
							location.reload();
						}, 3000 );
					}
				});
			}
		}).catch( swal.noop );
	});
	
	
	$( "#distribution-server-creation-form" ).on( "submit", function( e ){
		e.preventDefault();

		swal({
			title: "Confirm adding Server",
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function( result ) {
			if ( result.value ) {
				
				// var formData = $( "#distribution-server-creation-form" ).serialize();
				var cogginsServerId 	= $( '[name="coggins_server_id"]' ).val();
				var serverDescription 	= $( '[name="server_description"]' ).val();
				
				var notificationPoints	= [];
				$( '.notifications' ).each( function(){
					if( !this.value.trim() ){
						// an empty string
					} else {
						notificationPoints.push({ name:'', value:this.value });
						// an alternative is to serialize the .notifications class
					}
				});
				
				var serverData 			= $( '[name="coggins_server_id"] option:selected' ).data( "server_data" );
				serverData = decodeURIComponent( escape( window.atob( serverData ) ) );
				
				$.ajax({
					url:"<?php echo base_url( 'webapp/settings/add_server/' ); ?>",
					method: "POST",
					dataType: "JSON",
					data: {
						coggins_server_id: cogginsServerId,
						server_description: serverDescription,
						notification_points: notificationPoints,
						server_data: serverData,
					},
					success:function( data ){
						if( data.status != false ){
							swal({
								type: 'success',
								title: data.status_msg
							})
						} else {
							swal({
								type: 'error',
								title: data.status_msg
							})
						}
						window.setTimeout( function(){
 							location.reload();
						}, 3000 );
					}
				});
			}
		}).catch( swal.noop );
	});
	

	// Appending name ID into the modal
	$( "#settings_values" ).on( "click", ".create-setting-modal", function(){
		var settingNameId = $( this ).data( "setting_name_id" );
		$( "#setting-creation-form *[name='setting_name_id']" ).val( parseInt( settingNameId ) );
	});

	// Adding dynamic field set after click
	var i = 1;
	$( ".add_another_attribute > a" ).on( "click", function(){
		var template = '<div class="value_attributes"><div class="input-group form-setting-value"><input name="value[' + i + '][item_value]" class="input-field-full container-full" type="text" value="" placeholder="Setting Value" title="Setting Value" /></div><div class="input-group form-value-order"><label class="input-group-addon el-hidden">Value Order</label><select name="value[' + i + '][setting_order]" class="input-field-full container-full" title="Value Order"><option value="">Select</option>';
			<?php for( $i = 1; $i < 50; $i++ ){ ?>
				template += '<option value="<?php echo $i; ?>"><?php echo $i; ?></option>';
			<?php }; ?>
		template += '</select></div><div class="input-group form-group container-full"><label class="input-group-addon el-hidden">Value Description</label><input name="value[' + i + '][value_desc]" class="input-field-full container-full required" type="text" value="" placeholder="Value Description" title="Value Description" /></div></div>';
		i++;
		$( "#outputArea" ).append( template );
	});
	
	
	// Adding dynamic email field set after click - server creation
	var j = 1;
	$( ".add_another_email > a" ).on( "click", function(){
		var email_template ='<div class="notification_points"><div class="input-group form-group container-full"><label class="input-group-addon el-hidden">Email address of the person to notify</label><input name="notifications[' + j + '][email]" class="input-field-full container-full notifications required" type="text" value="" placeholder="Email of the person to notify" title="Email of the person to notify" /></div></div>';
		j++;
		$( "#email_outputArea" ).append( email_template );
	});

	// reset the active links ans set the clicked one to 'active'
	$( ".item_link, .territories_link, .distribution_server_link, .item" ).on( "click", function(){
		$( ".item_link, .territories_link, .distribution_server_link, .item" ).each( function(){
			$( this ).removeClass( "active" );
		});

		$( this ).addClass( "active" );
	});
	
	
	
	// Server linking/creation steps
	$( ".server-creation-steps" ).click( function(){
		$( '.error_message' ).each( function(){
			$( this ).text( '' );
		});		
		var currentpanel = $( this ).data( "currentpanel" );
		var inputs_state = check_inputs( currentpanel );
		if( inputs_state ){
			//If name attribute returned, auto focus to the field and display error message
			$( '[name="' + inputs_state + '"]' ).focus();
			var labelText = $( '[name="' + inputs_state + '"]' ).parent().find( 'label' ).text();
			$( '#' + currentpanel + '-errors' ).text( ucwords( labelText ) + ' is a required');
			return false;
		}
		
		panelchange( "." + currentpanel, ".server_creation_panel" );
		
		return false;
	});
	
	// Server linking/creation steps - end
	
	

	$( ".setting-creation-steps" ).click(function() {
		$( '.error_message' ).each( function(){
			$( this ).text( '' );
		});

		var currentpanel = $( this ).data( "currentpanel" );
		var inputs_state = check_inputs( currentpanel );
		if( inputs_state ){
			//If name attribute returned, auto focus to the field and display error message
			$( '[name="' + inputs_state + '"]' ).focus();
			var labelText = $('[name="' + inputs_state + '"]').parent().find('label').text();
			$('#' + currentpanel + '-errors').text(ucwords(labelText) + ' is a required');
			return false;
		}
		panelchange( "." + currentpanel )
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
				fieldName = $( this ).attr('name');
				result = fieldName;
				return result;
			}
		});
		return result;
	}


	$( ".btn-next" ).click( function(){
		current_panel_id = $( "." + $( this ).data( "currentpanel" ) ).attr( "data-panel-index" )
		$( $( ".tick_box" )[current_panel_id] ).removeClass( "el-hidden" )
	});

	$( ".btn-back" ).click( function(){
		var currentpanel = $( this ).data( "currentpanel" );
		go_back( "." + currentpanel )
		return false;
	});
	
	
	
	$( ".btn-server-back" ).click( function(){
		var currentpanel = $( this ).data( "currentpanel" );
		go_back( "." + currentpanel, ".server_creation_panel" )
		return false;
	});
	

	function panelchange( changefrom, defaultClass = ".setting_creation_panel" ){
		var panelnumber = parseInt( changefrom.match( /\d+/ ) ) + parseInt( 1 );
		var changeto = defaultClass + panelnumber;
		$( changefrom ).hide( "slide", {
			direction: 'left'
		}, 500 );
		$( changeto ).delay( 600 ).show( "slide", {
			direction: 'right'
		}, 500 );
		return false;
	}

	function go_back( changefrom, defaultClass = ".setting_creation_panel" ){
		var panelnumber = parseInt( changefrom.match( /\d+/ ) ) - parseInt( 1 );
		var changeto = defaultClass + panelnumber;
		$( changefrom ).hide( "slide", {
			direction: 'right'
		}, 500 );
		$( changeto ).delay( 600 ).show( "slide", {
			direction: 'left'
		}, 500 );
		return false;
	}

	$( "#update_setting_modal" ).on( "submit", "#delete_update_in_modal", function( e ){
		e.preventDefault();

		swal({
			title: "Confirm deleting the Value",
			type: "error",
			showCancelButton: true,
		}).then( function( result ) {
			if ( result.value ) {
				var settingID = $( "#deleteSettingBtn" ).data( "setting_id" );
				$.ajax({
					url:"<?php echo base_url( 'webapp/settings/delete_setting/' ); ?>",
					method: "POST",
					dataType: "json",
					data: {
						setting_id:settingID
					},
					success:function( data ){
						if( data.status != false ){
							swal({
								type: 'success',
								title: data.status_msg
							})
						} else {
							swal({
								type: 'error',
								title: data.status_msg
							})
						}
						window.setTimeout( function(){
							location.reload();
						}, 3000 );
					}
				});
			}
		}).catch( swal.noop );
	});


	$( "#update_territory_modal" ).on( "submit", "#delete_update_in_modal", function( e ){
		e.preventDefault();

		swal({
			title: "Confirm deleting the Territory",
			type: "error",
			showCancelButton: true,
		}).then( function( result ) {
			if ( result.value ) {
				var territoryID = $( "#deleteTerritoryBtn" ).data( "territory_id" );
				$.ajax({
					url:"<?php echo base_url( 'webapp/settings/delete_territory/' ); ?>",
					method: "POST",
					dataType: "json",
					data: {
						territory_id:territoryID
					},
					success:function( data ){
						if( data.status != false ){
							swal({
								type: 'success',
								title: data.status_msg
							})
						} else {
							swal({
								type: 'error',
								title: data.status_msg
							})
						}
						window.setTimeout( function(){
							location.reload();
						}, 3000 );
					}
				});
			}
		}).catch( swal.noop );
	});


	$( "#update_setting_modal" ).on( "submit", "#setting_update_in_modal", function( e ){
		e.preventDefault();
		var formData = $( "form#setting_update_in_modal" ).serialize();
		$.ajax({
			url:"<?php echo base_url( 'webapp/settings/update_setting/' ); ?>",
			method: "POST",
			dataType: "json",
 			data: formData,
			success:function( data ){
				if( data.status != false ){
					swal({
						type: 'success',
						title: data.status_msg
					})
				} else {
					swal({
						type: 'error',
						title: data.status_msg
					})
				}
				window.setTimeout( function(){
					location.reload();
				}, 3000 );
			}
		})
	});

	$( "#update_territory_modal" ).on( "submit", "#territory_update_in_modal", function( e ){
		e.preventDefault();
		var formData = $( "#territory_update_in_modal" ).serialize();
		$.ajax({
			url:"<?php echo base_url( 'webapp/settings/update_territory/' ); ?>",
			method: "POST",
			dataType: "json",
 			data: formData,
			success:function( data ){
				if( data.status != false ){
					swal({
						type: 'success',
						title: data.status_msg
					})
				} else {
					swal({
						type: 'error',
						title: data.status_msg
					})
				}
				window.setTimeout( function(){
 					location.reload();
				}, 3000 );
			}
		})
	});

	$( "#settings_values" ).on( "click", ".clickable", function(){
		var settingID = $( this ).data( "setting_id" );

		$.ajax({
			url:"<?php echo base_url( 'webapp/settings/item_data/' ); ?>",
			method: "POST",
			dataType: "json",
 			data:{
				setting_id : settingID,
			},
			success:function( data ){
				if( data ){
					$( "#update_setting_modal" ).html( data.item_data );
				} else {
					swal({
						type: 'error',
						title: data.status_msg
					})
				}
			}
		})
	});


	$( "#territories_values" ).on( "click", ".clickable", function(){
		var territoryID = $( this ).data( "territory_id" );

		$.ajax({
			url:"<?php echo base_url( 'webapp/settings/territories_data/' ); ?>",
			method: "POST",
			dataType: "json",
 			data:{
				territory_id : territoryID,
			},
			success:function( data ){
				if( data ){
					$( "#update_territory_modal" ).html( data.item_data );
				} else {
					swal({
						type: 'error',
						title: data.status_msg
					})
				}
			}
		})
	});
	
	$( "#distribution_server" ).on( "click", ".clickable", function(){
		var serverID = $( this ).data( "server_id" );
		$.ajax({
			url:"<?php echo base_url( 'webapp/settings/get_server/' ); ?>",
			method: "POST",
			dataType: "json",
 			data:{
				server_id : serverID,
			},
			success:function( output ){
				if( output ){
					$( "#update_server_modal" ).html( output.item_data );
				} else {
					swal({
						type: 'error',
						title: output.status_msg
					})
				}
			}
		})
	});



	$( ".item_link" ).on( "click", function(){
		var groupID = $( this ).data( "setting_name_id" );
		var settingName = $( this ).data( "setting_name" );
		var module_id = <?php echo $module_id; ?>;

		$.ajax({
			url:"<?php echo base_url( 'webapp/settings/setting_items/' ); ?>",
			method: "POST",
			data:{
				setting_name_id : groupID,
				moduleID : module_id,
				setting_name : settingName
			},
			success:function( result ){
				if( result ){
					$( "#territories_values" ).empty();
					$( "#distribution_server" ).empty();
					$( "#genres" ).empty();
					$( "#age_rating" ).empty();
					$( "#settings_values" ).html( result );
				} else {
					swal({
						type: 'error',
						title: result.status_msg
					})
				}
			}
		})
	});


	$( ".territories_link" ).on( "click", function(){
		$.ajax({
			url:"<?php echo base_url( 'webapp/settings/get_territories/' ); ?>",
			method: "POST",
			success:function( result ){
				if( result ){
					$( "#settings_values" ).empty();
					$( "#distribution_server" ).empty();
					$( "#genres" ).empty();
					$( "#age_rating" ).empty();
					$( "#territories_values" ).html( result );
				} else {
					swal({
						type: 'error',
						title: result.status_msg
					})
				}
			}
		})
	});


	$( ".distribution_server_link" ).on( "click", function(){
		$.ajax({
			url:"<?php echo base_url( 'webapp/settings/get_distribution_server/' ); ?>",
			method: "POST",
			success:function( result ){
				if( result ){
					$( "#settings_values" ).empty();
					$( "#territories_values" ).empty();
					$( "#genres" ).empty();
					$( "#age_rating" ).empty();
					$( "#distribution_server" ).html( result );
				} else {
					swal({
						type: 'error',
						title: result.status_msg
					})
				}
			}
		})
	});
	
	$( ".genres_link" ).on( "click", function(){
		
		var genreTypeId 	= $( this ).data( "genre_type_id" );
		var genreTypeName 	= $( this ).data( "genre_type_name" );
		
		$.ajax({
			url:"<?php echo base_url( 'webapp/settings/get_genres/' ); ?>",
			method: "POST",
			data:{
				genre_type_id 	: genreTypeId,
				genre_type_name : genreTypeName,
			},
			success:function( result ){
				if( result ){
					$( "#settings_values" ).empty();
					$( "#territories_values" ).empty();
					$( "#distribution_server" ).empty();
					$( "#age_rating" ).empty();
					$( "#genres" ).html( result );
				} else {
					swal({
						type: 'error',
						title: result.status_msg
					})
				}
			}
		})
	});
	
	
	$( ".age_classification_link" ).on( "click", function(){
		
		var ageClassificationId 	= $( this ).data( "age_classification_id" );
		var ageClassificationName 	= $( this ).data( "age_classification_name" );
		
		$.ajax({
			url:"<?php echo base_url( 'webapp/settings/get_age_rating/' ); ?>",
			method: "POST",
			data:{
				age_classification_id 	: ageClassificationId,
				age_classification_name : ageClassificationName,
			},
			success:function( result ){
				if( result ){
					$( "#settings_values" ).empty();
					$( "#territories_values" ).empty();
					$( "#distribution_server" ).empty();
					$( "#genres" ).empty();
					$( "#age_rating" ).html( result );
				} else {
					swal({
						type: 'error',
						title: result.status_msg
					})
				}
			}
		})
	});
});
</script>