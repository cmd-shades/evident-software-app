<div class="row" >
	<div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12">
		<div class="modal-body">
			<form id="premises-attribute-creation-form" >
				<input type="hidden" name="override_existing" value="" />
				<input type="hidden" name="page" value="details" />
				<input type="hidden" name="premises_type_id" value="<?php echo null; ?>" />
				<input type="hidden" name="item_type" value="generic" />
				<div class="x_panel tile has-shadow">
					<div class="premises_attribute_creation_panel1">
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<h4>Please enter the attribute name?</h4>
								<div class="form-group" >
									<input id="attribute_name" name="attribute_name"  type="text"  class="form-control required" data-label_text="Attribute name" value="" />
								</div>
								<?php /*<div class="col-md-3 col-sm-3 col-xs-12">
									<label class="pointer" ><input name="option_group" type="checkbox" style="margin-top:6px;" class="btn-select-all"> Create Grouped options</label>
								</div> */ ?>
								<div class="hide form-group">
									<input type="hidden" name="ordering" value="" >
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<h4>What response type would like this to be?</h4>
								<select id="response_type_id" name="response_type" class="form-control required" data-label_text="EviDocs Response type" >
									<option value="" >Select type</option>
									<?php if( !empty( $response_types ) ) { foreach( $response_types as $k => $resp_type ) { ?>
										<option value="<?php echo $resp_type->response_type; ?>" data-resp_type="<?php echo $resp_type->response_type; ?>" data-resp_type_alt="<?php echo $resp_type->response_type_alt; ?>"  data-resp_desc="<?php echo $resp_type->response_type_desc; ?>" ><?php echo $resp_type->response_type_alt; ?></option>
									<?php } } ?>
								</select>
								<div class="hide form-group">
									<input type="hidden" name="response_type_alt" value="" >
								</div>
								
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
																<span><input type="text" plceholder="Type new value..." id="txtOptionName-<?php echo $resp_type->response_type; ?>" />
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
																			<label><input type="checkbox" name="response_options[<?php echo $resp_type->response_type; ?>][options][]" value="<?php echo $resp_options->option_value; ?>" style="margin-top:6px;" > <?php echo $resp_options->option_value; ?></label>
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
						</div>
						<br/>
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button id="check-exists" class="btn btn-block btn-flow btn-success btn-next premises-attribute-creation-steps" data-currentpanel="premises_attribute_creation_panel1" type="button">Next</button>				
							</div>
						</div>

					</div>
					
					<div class="premises_attribute_creation_panel2" style="display:none" >
						<div class="form-group">
							<div class="text-center">
								<p>You are about to submit a request to create a new Premises attribute.</p>
								<p>Click the "Create" to proceed or Back to review your setup.</p>
							</div>
						</div>
						<div class="form-group resp-requirement" style="margin:15px 0">
							<div class="row">
								<div class="col-md-4 col-sm-4 col-xs-12">
									<button class="btn btn-block btn-flow btn-back-attribute" data-currentpanel="premises_attribute_creation_panel2" type="button" >Back</button>					
								</div>
								<div class="col-md-4 col-sm-4 col-xs-12">
									<button id="create-another-premises-attribute" class="btn btn-block btn-flow btn-info" type="button" >Create Another Attribute</button>					
								</div>
								<div class="col-md-4 col-sm-4 col-xs-12">
									<button id="create-premises-attribute-btn" class="btn btn-block btn-flow btn-success btn-next" type="button" >Create and Finish</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<script>

	$('.btn-select-all').change(function(){
		selectAllChecked = $(this).prop('checked')
		$(this).closest('.form-group').find('input[type=checkbox]').prop('checked', selectAllChecked)
	})

	function addNewOption( optionType, optionValue ) {
		
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
		}else{
			alert( 'Missing required options' );
			return false;
		}
	}

	$( document ).ready( function(){
		
		$( '.add-new-option' ).click( function(){
			var respType 		= $( this ).data( 'resp_type' );
			$( '.add-more-options-'+respType ).slideToggle();
		});
		
		$('.add-new-option-btn').click(function() {
			var respType = $( this ).data( 'resp_type' );
			if( respType.length > 0 ){
				var newValue   = $( '#txtOptionName-'+respType ).val();
				var addSuccess = addNewOption( respType, newValue );
				if( addSuccess ){
					$( '#txtOptionName-'+respType ).val( '' )
				}
			}else{
				alert( 'Oops! Something went wrong!' );
			}
		});
		
		$( '.extra_info_trigger' ).change( function(){
			var selectdOpt = $( 'option:selected', this ).val();
			if( selectdOpt.length > 0 ){
				var newText = 'If "' + selectdOpt + '", please provide further info.';
				$( '#extra_info' ).val( newText );
			}			
		} );
		
		//Update column in the system
		$( '[name="update_column"]' ).change( function(){
			updateCol = $("input[name='update_column']:checked").val();
			$( '.update-attribute-name' ).slideUp( 'fast' );
			if( updateCol == 1 ){
				$( '.update-attribute-name' ).slideDown( 'slow' );
			}else{
				$( '#update_attribute_id' ).val( '' );
			}
		} );
		
		//File required
		$( '.photo_required' ).change( function(){
			optChoice = $("input[name='photo_required']:checked").val();
			$( '.acceptable-file-types' ).slideUp( 'fast' );
			if( optChoice == 1 ){
				$( '.acceptable-file-types' ).slideDown( 'slow' );
				
				$( '.file_types_opts' ).each( function(){
					$( this ).prop( 'checked', true );
				});
				
			}else{
				$( '.file_types_opts' ).each( function(){
					$( this ).prop( 'checked', false );
				});
			}
		} );
		
		$( '#attribute_name' ).change( function(){
			var attributeName 		= $( 'option:selected', this ).val();
			var attributeOrdering 	= $( 'option:selected', this ).data( 'ordering' );

			if( attribute_name.length > 0 ){
				$( '[name="ordering"]').val( attributeOrdering );
			} else {
				$( '[name="ordering"]').val( '' );
			}
			
		})
		
		$( '#response_type_id' ).change( function(){
			var respType 	= $( 'option:selected', this ).data( 'resp_type' );
			var respTypeAlt = $( 'option:selected', this ).data( 'resp_type_alt' );
			var respDesc 	= $( 'option:selected', this ).data( 'resp_desc' );
			
			$( '.resp-extra-options' ).hide();
			$( '#selected-option' ).text( '' );
			if( respType.length > 0 ){
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
		
		$(".premises-attribute-creation-steps").click(function(){

			//Clear errors first
			$( '.error_message' ).each(function(){
				$( this ).text( '' );
			});
			
			var currentpanel = $(this).data( "currentpanel" );
			
			var inputs_state = check_inputs( currentpanel );			
			if( inputs_state ){
				//If name attribute returned, auto focus to the field and display error message
				$( '[name="'+inputs_state+'"]' ).focus().css("border","1px solid red");
				var labelText = $( '[name="'+inputs_state+'"]' ).parent().find('label').text();
					labelText = ( labelText !== "" && ( labelText.length > 0 ) ) ? labelText : $( '[name="'+inputs_state+'"]' ).data( 'label_text' )+' is a required';
				swal({
					type: 'error',
					title: labelText
				})
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
		
		//Go back-btn
		$(".btn-back-attribute").click(function(){
			var currentpanel = $(this).data( "currentpanel" );
			go_back("."+currentpanel)	
			return false;
		});
		
		function panelchange( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )+parseInt(1);
			var changeto = ".premises_attribute_creation_panel"+panelnumber;
			$( changefrom ).hide( "slide", {direction : 'left'}, 500);
			$( changeto ).delay(600).show( "slide", {direction : 'right'},500);	
			return false;	
		}
		
		function go_back( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
			var changeto = ".premises_attribute_creation_panel"+panelnumber;
			$( changefrom ).hide( "slide", { direction : 'right' }, 500);
			$( changeto ).delay(600).show( "slide", { direction : 'left' },500);	
			return false;	
		}
		
		
		$( '#create-another-premises-attribute' ).click(function( e ){
			createPremisesAttribute( e, true )
		});
		
		
		/*
		* Submit Evidoc Question form
		*/
		$( '#create-premises-attribute-btn' ).click(function( e ){
			createPremisesAttribute( e )
		});
		
		function createPremisesAttribute( event, restartOnCreate = false){
			event.preventDefault();
			
			var formData = $('#premises-attribute-creation-form').serialize();
			
			swal({
				title: 'Confirm attribute creation?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/premises/add_premises_type_attribute/' ); ?>",
						method:'POST',
						data:formData,
						dataType: 'json',
						success:function( data ){
							if( ( data.status == 1 ) && ( data.premises_attribute !== '' ) ){
								
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2000
								}).then(function() {
									
									if( !restartOnCreate ){
										window.setTimeout(function(){
											window.location.href = "<?php echo base_url( 'webapp/premises/premises_type_attributes' ); ?>";
										} ,2000);			
									} else {
										/* reset premises type attribute form */
										$('#attribute_name').val('')
										$('#response_type_id').val('')
										
										$('#attribute_name').css('border', '1px solid #ccc')
										$('#response_type_id').css('border', '1px solid #ccc')
										
										/* change to first screen of premises attribute creation */
										var changeto = ".premises_attribute_creation_panel1";
										var changefrom = ".premises_attribute_creation_panel2";
										$( changefrom ).hide( "slide", {direction : 'left'}, 500);
										$( changeto ).delay(600).show( "slide", {direction : 'right'},500);	
									}

								});	

							} else {
								swal({
									type: 'error',
									title: data.status_msg
								})
								var changeto = ".premises_attribute_creation_panel1";
								var changefrom = ".premises_attribute_creation_panel2";
								$( changefrom ).hide( "slide", {direction : 'left'}, 500);
								$( changeto ).delay(600).show( "slide", {direction : 'right'},500);	
							}		
						}
					});
				} else {
					$( ".premises_attribute_creation_panel2" ).hide( "slide", { direction : 'left' }, 500 );
					go_back( ".premises_attribute_creation_panel2" );
					return false;
				}
			}).catch( swal.noop )
		}
		

	});
</script>