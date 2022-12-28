<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="profile-details-container">
				<div class="row alert alert-ssid bg-blue" role="alert">
					<div class="profile-overview">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<legend>Attribute Profile <span class="pull-right"><span class="edit-attribute pointer hide" title="Click to edit thie premises Type profile"><i class="fas fa-pencil-alt"></i></span> &nbsp; <span class="delete-attribute-btn pointer" title="Click to delete this Premises Type Attribute profile" ><i class="far fa-trash-alt"></i></span></span></legend>
							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Date Created</label></td>
											<td width="85%"><?php echo ( valid_date( $attribute_details->date_created ) ) ? date( 'd-m-Y H:i:s', strtotime( $attribute_details->date_created ) ) : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Created By</label></td>
											<td width="85%"><?php echo ( !empty( $attribute_details->record_created_by ) ) ? ucwords( $attribute_details->record_created_by ) : 'Data not available'; ?></td>
										</tr>
									</table>							
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label><strong>No. Associated Premises Type</strong></label></td>
											<td width="85%"><?php echo !empty( $associated_premises_types ) ? count( $associated_premises_types, 1 ) : 0; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Status</label></td>
											<td width="85%"><?php echo ( $attribute_details->is_active == 1 ) ? 'Active <i class="far fa-check-circle"></i>' : 'Disabled <i class="far fa-times-circle text-red"></i>'; ?></td>
										</tr>
									</table>							
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<div class="x_panel tile has-shadow">
								<form id="update-premises-form" class="form-horizontal">
									<input type="hidden" name="source" value="type_attribute_profile" />
									<input type="hidden" name="page" value="details" />
									<input type="hidden" name="attribute_id" value="<?php echo $attribute_details->attribute_id; ?>" />
									<input type="hidden" name="premises_type_id" value="<?php echo !empty( $attribute_details->premises_type_id ) ? $attribute_details->premises_type_id : null; ?>" />
									<legend>Attribute Details</legend>
									<div class="input-group form-group">
										<label class="input-group-addon">Attribute Name</label>
										<input id="attribute_name" name="attribute_name" class="form-control readonly-field" type="text" placeholder="Attribute Name" readonly value="<?php echo $attribute_details->attribute_name; ?>" />
									</div>
									<div class="input-group form-group">
										<label class="input-group-addon">Response Type</label>											
										<select id="response_type_id" name="response_type" class="form-control required" data-label_text="EviDocs Response type" >
											<option value="" >Select type</option>
											<?php if( !empty( $response_types ) ) { foreach( $response_types as $k => $resp_type ) { ?>
												<option value="<?php echo $resp_type->response_type; ?>" <?php echo ( $resp_type->response_type == $attribute_details->response_type ) ? 'selected="selected"' : ''; ?> data-resp_type="<?php echo $resp_type->response_type; ?>" data-resp_type_alt="<?php echo $resp_type->response_type_alt; ?>"  data-resp_desc="<?php echo $resp_type->response_type_desc; ?>" ><?php echo $resp_type->response_type_alt; ?></option>
											<?php } } ?>
										</select>
										<div class="hide form-group">
											<input type="hidden" name="response_type_alt" value="" />
										</div>
									</div>
									
									<div class="">
										<!-- Response type Options -->
										<div class="resp-extra-options" style="display:none; margin:15px 0">
											<div>
												<label><strong>You have chosen:</strong> <span id="selected-option" ></span></label>
											</div>
											<div class="form-group resp-requirement" style="margin:15px 0">
												<?php if( !empty( $response_types ) ) { foreach( $response_types as $k => $resp_type ) { ?>
													<?php if( $resp_type->response_type == 'short_text' ){ ?>
														<div class="resp_<?php echo $resp_type->response_type; ?> resp-type-options" style="display:none; margin:15px 0">
															<div class="form-group" >
																<input type="hidden" name="response_options[<?php echo $resp_type->response_type; ?>][response_type_max_chars]" value="" class="form-control"  >
															</div>
														</div>

													<?php } else if( $resp_type->response_type == 'numbers_only' ){ ?>
														<div class="resp_<?php echo $resp_type->response_type; ?> resp-type-options" style="display:none; margin:15px 0">
															<div class="hide form-group">
																<label>Set the maximum number <small>(optional)</small></label>
																<input type="number" name="response_options[<?php echo $resp_type->response_type; ?>][response_type_max_chars]" value="" class="form-control" maxlength="125" placeholder="" >
															</div>
														</div>
													<?php } else if( $resp_type->response_type == 'short_text' ){ ?>
														<div class="resp_<?php echo $resp_type->response_type; ?> resp-type-options" style="display:none; margin:15px 0">
															<div class="hide form-group">
																<label>Set the maximum number of characters <small>(optional)</small></label>
																<input type="number" name="response_options[<?php echo $resp_type->response_type; ?>][response_type_max_chars]" value="" class="form-control" maxlength="125" placeholder="Default is 125 characters" >
															</div>
														</div>
													<?php } else if( $resp_type->response_type == 'long_text' ){ ?>
														<div class="resp_<?php echo $resp_type->response_type; ?> resp-type-options" style="display:none">
															<div class="hide form-group">
																<label>Set the maximum number of characters <small>(leave blank if not sure)</small></label>
																<input type="number" name="response_options[<?php echo $resp_type->response_type; ?>][response_type_max_chars]" value="" class="form-control" maxlength="125" placeholder="Default is 2500 characters" >
															</div>
														</div>
													<?php } else if( ( $resp_type->response_type == 'single_choice' ) || ( $resp_type->response_type == 'multiple_choice' ) ){ ?>

														<div class="resp_<?php echo $resp_type->response_type; ?> resp-type-options form-group" style="display:none">
															<label class="text-red no-options-set-<?php echo $resp_type->response_type; ?> "><?php echo ( !empty( $resp_type->response_type_options ) ? '' : 'You have not created any options yet.' ); ?></label>
															<div class="row">
																<div class="col-xs-12">
																	<label class="pull-left">Please add options for your users to choose from. Click the plus button to add a new one &nbsp;<span style="margin-top:6px" class="add-new-option pull-right pointer" data-resp_type="<?php echo $resp_type->response_type; ?>" title="Click this to add a new option" > <i class="pull-right far fa-plus-square text-green"></i></span>&nbsp;</label>
																	<div class="add-more-options-<?php echo $resp_type->response_type; ?>" style="display:<?php echo ( !empty( $resp_type->response_type_options ) ? 'none' : 'block' ); ?>">
																		<div><em><small>Please note that the options you add are not saved until you complete this process.</small></em></div>
																		<span><input type="text" placeholder="Type new value..." id="txtOptionName-<?php echo $resp_type->response_type; ?>" />
																		<input type="button" value="Add" class="add-new-option-btn" data-resp_type="<?php echo $resp_type->response_type; ?>" /></span>
																	</div>
																</div>
															</div>
															<div class="row">
																<div class="checkbox-options opts-<?php echo $resp_type->response_type; ?>" data-checkbox_type="<?php echo $resp_type->response_type; ?>" >
																	
																	<?php if( !empty( $resp_type->response_type_options ) ) { ?>
																		<div class="col-md-3 col-sm-3 col-xs-12">
																			<div class="radio">
																				<label><input type="checkbox" style="margin-top:6px;" class="btn-select-all"> Check all</label>
																			</div>
																		</div>																
																		<?php foreach( $resp_type->response_type_options as $k => $resp_options ) { ?>
																			<div class="col-md-3 col-sm-3 col-xs-12">
																				<div class="radio">
																					<!-- By default single choice checkboxes should not be checked -->
																					<label><input <?php echo ( is_array( convert_to_array( $attribute_details->response_options ) ) && in_array( strtolower( $resp_options->option_value ), array_map( 'strtolower', convert_to_array( $attribute_details->response_options ) ) ) ) ? 'checked' : '' ?> type="checkbox" name="response_options[<?php echo $resp_type->response_type; ?>][options][]" value="<?php echo $resp_options->option_value; ?>" style="margin-top:6px;" > <?php echo $resp_options->option_value; ?></label>
																				</div>
																			</div>
																		<?php } ?>
																	<?php } ?>
																</div>
															</div>
															
														</div>
													<?php } else if( ( $resp_type->response_type == 'file' ) || ( $resp_type->response_type == 'signature' ) ){ ?>
													
														<div class="resp_<?php echo $resp_type->response_type; ?> resp-type-options form-group" style="display:none">
															<label>Please add your accepted file types</label>
															<div class="row">
																<?php if( !empty( $resp_type->response_type_options ) ) { foreach( $resp_type->response_type_options as $k => $resp_options ) { ?>
																	<div class="col-md-3 col-sm-4 col-xs-12">
																		<div class="radio">
																			<label><input type="checkbox" name="response_options[<?php echo $resp_type->response_type; ?>][options][]" value="<?php echo $resp_options->option_value; ?>" style="margin-top:6px;" > <?php echo $resp_options->option_value; ?></label>
																		</div>
																	</div>
																<?php } } ?>
															</div>
														</div>
													<?php } else { ?>
														<div class="resp_<?php echo $resp_type->response_type; ?> resp-type-options form-group resp-type-unknown" style="display:none">
															
														</div>
													<?php } ?>
													
												<?php } } ?>

											</div>
										</div>
									</div>
								
									<div class="input-group form-group">
										<label class="input-group-addon">Status</label>											
										<select name="is_active" class="form-control" >
											<option >Set status</option>
											<option value="1" <?php echo ( $attribute_details->is_active == 1 ) ? 'selected="selected"' : ''; ?> >Active</option>											
											<option value="0" <?php echo ( $attribute_details->is_active != 1 ) ? 'selected="selected"' : ''; ?> >In-active</option>											
										</select>
									</div>
								
									<div class="input-group form-group">
										<label class="input-group-addon">Is Mobile Visible?</label>											
										<select name="is_mobile_visible" class="form-control" >
											<option >Set visibility</option>
											<option value="1" <?php echo ( $attribute_details->is_mobile_visible == 1 ) ? 'selected="selected"' : ''; ?> >Yes</option>											
											<option value="0" <?php echo ( $attribute_details->is_mobile_visible != 1 ) ? 'selected="selected"' : ''; ?> >No</option>											
										</select>
									</div>
								
									<div class="row" >
										<div class="col-md-6 col-sm-6 col-xs-12">
											<button type="button" class="update-attribute-btn btn btn-sm btn-success">Save Changes</button>
											<button type="button" class="delete-attribute-btn btn btn-sm btn-danger">Delete Attribute</button>
										</div>	
									</div>									
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$( document ).ready( function(){
	
	$( '.readonly-field' ).click( function(){
		swal({
			'type': 'warning',
			'title': 'Readonly field',
			'text': 'This is a Readonly field. If you need to change this, you should archive it and create a new one!',
		});
	})

	$( '.add-new-option' ).click( function(){
		var respType 		= $( this ).data( 'resp_type' );
		$( '.add-more-options-'+respType ).slideToggle();
	});
	
	$( '.add-new-option-btn' ).click( function() {
		var respType = $( this ).data( 'resp_type' );
		if( respType.length > 0 ){
			var newValue   = $( '#txtOptionName-'+respType ).val();
			var addSuccess = addNewOption( respType, newValue );
			if( addSuccess ){
				$( '#txtOptionName-'+respType ).val( '' );
			}
		}else{
			alert( 'Oops! Something went wrong!' );
		}
	});
	
	
	function addNewOption( optionType, optionValue ){
		if( optionType.length > 0 && optionValue.length > 0 ){
			var mainContainer = $( '.opts-' + optionType );
			var elementCloner = '<div class="col-md-3 col-sm-3 col-xs-12"><div class="radio">';
				elementCloner += '<label>';
					elementCloner += '<input checked type="checkbox" name="response_options['+ optionType +'][options][]" value="'+ optionValue +'" style="margin-top:6px;" > ' + ucwords( optionValue );
				elementCloner += '</label>';
			elementCloner += '</div></div>';
			$( mainContainer ).append( elementCloner );

			//Append to Select Options
			var selectIdentifier = '#extra-info-' + optionType;
			var chkOptionExists = ( $( selectIdentifier + ' option[value="' + optionValue + '"]').length > 0 );
			if( !chkOptionExists ){
				//Only add the new option if it doesn't already exist
				$( selectIdentifier ).append( $('<option >').val( optionValue ).text( ucwords( optionValue ) ) );
			}

			//Hide the no-options-set DIV
			$( '.no-options-set-' + optionType ).hide();
			return true;
		} else {
			alert( 'Missing required options' );
			return false;
		}
	}
	
	
	$( '.btn-select-all' ).change( function(){
		selectAllChecked = $( this ).prop( 'checked' );
		$( this ).closest( '.form-group' ).find( 'input[type=checkbox]' ).prop( 'checked', selectAllChecked );
	})

	
	//RENDER RESPONSE OPTIONS ON-LOAD
	var respType 	= $( '#response_type_id option:selected' ).data( 'resp_type' );
	var respTypeAlt = $( '#response_type_id option:selected' ).data( 'resp_type_alt' );
	var respDesc 	= $( '#response_type_id option:selected' ).data( 'resp_desc' );
	
	$( '.resp-extra-options' ).hide();
	$( '#selected-option' ).text( '' );

	if( ( respType ) && respType.length > 0 ){
		$( '[name="response_type_alt"]' ).val( respTypeAlt );
		$( '.resp-extra-options' ).show();
		$( '#selected-option' ).html( '<span>' + respTypeAlt + ' - ' + '' + respDesc +'</span>' );
	} else {
		$( '[name="response_type_alt"]').val( '' );
	}
	
	$( '.resp-type-options' ).hide();
	$( '.resp-type-options' ).hide();
	$( '.resp_' + respType ).show();
		
	//RENDER RESONSE OPTIONS ON-RESPONSE-TYPE-CHANGE
	$( '#response_type_id' ).change( function(){
		var respType 	= $( 'option:selected', this ).data( 'resp_type' );
		var respTypeAlt = $( 'option:selected', this ).data( 'resp_type_alt' );
		var respDesc 	= $( 'option:selected', this ).data( 'resp_desc' );
		
		$( '.resp-extra-options' ).hide();
		$( '#selected-option' ).text( '' );

		if( ( respType ) && respType.length > 0 ){
			$( '[name="response_type_alt"]').val( respTypeAlt );
			$( '.resp-extra-options' ).show();
			$( '#selected-option' ).html( '<span>' + respTypeAlt + ' - ' + '' + respDesc +'</span>' );
		} else {
			$( '[name="response_type_alt"]').val( '' );
		}
		
		$( '.resp-type-options' ).hide();
		$( '.resp-type-options' ).hide();
		$( '.resp_' + respType ).show();
		
	})
	
	
	//Submit form for processing
	$( '.update-attribute-btn' ).click( function( event ){
		event.preventDefault();

		var formData = $( this ).closest( 'form' ).serialize();
		swal({
			title: 'Confirm attribute update?',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url( 'webapp/premises/update_premises_type_attribute/'.$attribute_details->attribute_id ); ?>",
					method:"POST",
					data:formData,
					dataType: 'json',
					success:function( data ){
						if( data.status == 1 ){
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 2500
							})
							window.setTimeout( function(){
								location.reload();
							}, 1000 );
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
	
	//DELETE premises-TYPE ATTRIBUTE
	$( '.delete-attribute-btn' ).click( function( event ){
		event.preventDefault();

		// var formData = $( this ).closest( 'form' ).serialize();
		var formData = $( "form#update-premises-form" ).serialize();
		swal({
			type: 'warning',
			title: 'Confirm Delete attribute?',
			html: 'This is an irreversible action and will affect Premises Types that are linked to it!',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url( 'webapp/premises/delete_premises_type_attribute/'.$attribute_details->attribute_id ); ?>",
					method:"POST",
					data:formData,
					dataType: 'json',
					success:function(data){
						if( data.status == 1 ){
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 2100
							})
							window.setTimeout( function(){
								window.location.href = "<?php echo base_url( 'webapp/premises/type_attributes' ); ?>";
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
});
</script>