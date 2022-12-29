<div>
	<div class="modal-body">
		<form id="evidoc-question-creation-form" >
			<input type="hidden" name="override_existing" value="" />
			<input type="hidden" name="page" value="details" />
			<input type="hidden" name="audit_type_id" value="<?php echo $evidoc_type_details->audit_type_id; ?>" />
			<input type="hidden" name="asset_type_id" value="<?php echo $evidoc_type_details->asset_type_id; ?>" />
			<div class="evidoc_question_creation_panel1">
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<h4>What would you like to ask your users?</h4>
						<div class="form-group">
							<input type="text" name="question" class="form-control required" placeholder="Enter what you'd like to ask your users here..." data-label_text="Question name" >
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button id="check-exists" class="btn btn-block btn-flow btn-success btn-next evidoc-question-creation-steps" data-currentpanel="evidoc_question_creation_panel1" type="button">Next</button>				
					</div>
				</div>					
			</div>
			
			<div class="evidoc_question_creation_panel2" style="display:none" >
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<h4>What response type would like this to be?</h4>
						<select id="response_type_id" name="response_type" class="form-control required" data-label_text="EviDocs Response type" >
							<option value="" >Select type</option>
							<?php if (!empty($response_types)) {
							    foreach ($response_types as $k => $resp_type) { ?>
								<option value="<?php echo $resp_type->response_type; ?>" data-resp_type="<?php echo $resp_type->response_type; ?>" data-resp_type_alt="<?php echo $resp_type->response_type_alt; ?>"  data-resp_desc="<?php echo $resp_type->response_type; ?>" ><?php echo $resp_type->response_type_alt; ?></option>
							<?php }
							    } ?>
						</select>
						
						<!-- Response type Options -->
						<div class="resp-extra-options" style="display:none; margin:15px 0">
							<div>
								<label><strong>You have chosen:</strong> <span id="selected-option" ></span></label>
							</div>
							<div class="form-group resp-requirement" style="margin:15px 0">
								<?php if (!empty($response_types)) {
								    foreach ($response_types as $k => $resp_type) { ?>
									<?php if ($resp_type->response_type == 'input') { ?>
										<div class="resp_<?php echo $resp_type->response_type; ?> resp-type-options" style="display:none; margin:15px 0">
											<div class="form-group" >
												<input type="hidden" name="response_options[<?php echo $resp_type->response_type; ?>][response_type_max_chars]" value="" class="form-control"  >
											</div>
										</div>

									<?php } elseif ($resp_type->response_type == 'input_integer') { ?>
										<div class="resp_<?php echo $resp_type->response_type; ?> resp-type-options" style="display:none; margin:15px 0">
											<div class="form-group">
												<label>Set the maximum number <small>(optional)</small></label>
												<input type="number" name="response_options[<?php echo $resp_type->response_type; ?>][response_type_max_chars]" value="" class="form-control" maxlength="125" placeholder="" >
											</div>
										</div>
									<?php } elseif ($resp_type->response_type == 'input_text') { ?>
										<div class="resp_<?php echo $resp_type->response_type; ?> resp-type-options" style="display:none; margin:15px 0">
											<div class="form-group">
												<label>Set the maximum number of characters <small>(optional)</small></label>
												<input type="number" name="response_options[<?php echo $resp_type->response_type; ?>][response_type_max_chars]" value="" class="form-control" maxlength="125" placeholder="Default is 125 characters" >
											</div>
										</div>
									<?php } elseif ($resp_type->response_type == 'textarea') { ?>
										<div class="resp_<?php echo $resp_type->response_type; ?> resp-type-options" style="display:none">
											<div class="form-group">
												<label>Set the maximum number of characters <small>(leave blank if not sure)</small></label>
												<input type="number" name="response_options[<?php echo $resp_type->response_type; ?>][response_type_max_chars]" value="" class="form-control" maxlength="125" placeholder="Default is 2500 characters" >
											</div>
										</div>
									<?php } elseif (($resp_type->response_type == 'radio') || ($resp_type->response_type == 'checkbox')) { ?>

										<div class="resp_<?php echo $resp_type->response_type; ?> resp-type-options form-group" style="display:none">
											<label class="text-red no-options-set-<?php echo $resp_type->response_type; ?> "><?php echo(!empty($resp_type->response_type_options) ? '' : 'You have not created any options yet.'); ?></label>
											<div class="row">
												<div class="col-xs-12">
													<label class="pull-left">Please add options for your users to choose from. Click the plus button to add a new one &nbsp;<span style="margin-top:6px" class="add-new-option pull-right pointer" data-resp_type="<?php echo $resp_type->response_type; ?>" title="Click this to add a new option" > <i class="pull-right far fa-plus-square text-green"></i></span>&nbsp;</label>
													<div class="add-more-options-<?php echo $resp_type->response_type; ?>" style="display:<?php echo(!empty($resp_type->response_type_options) ? 'none' : 'block'); ?>">
														<div><em><small>Please note that the options you add are not saved until you complete this process.</small></em></div>
														<span><input type="text" plceholder="Type new value..." id="txtOptionName-<?php echo $resp_type->response_type; ?>" />
														<input type="button" value="Add" class="add-new-option-btn" data-resp_type="<?php echo $resp_type->response_type; ?>" /></span>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="checkbox-options opts-<?php echo $resp_type->response_type; ?>" data-checkbox_type="<?php echo $resp_type->response_type; ?>" >
													
													<?php if (!empty($resp_type->response_type_options)) { ?>
														<div class="col-md-3 col-sm-3 col-xs-12">
															<div class="radio">
																<label><input type="checkbox" style="margin-top:6px;" class="btn-select-all"> Check all</label>
															</div>
														</div>																
														<?php foreach ($resp_type->response_type_options as $k => $resp_options) { ?>
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
											
											<div>
												<h4>Which one of these options should trigger additional information?</h4>
												<select id="extra-info-<?php echo $resp_type->response_type; ?>" name="response_options[<?php echo $resp_type->response_type; ?>][extra_info_trigger]" class="form-control extra_info_trigger" >
													<option value="" >Select type</option>
													<?php if (!empty($resp_type->response_type_options)) {
													    foreach ($resp_type->response_type_options as $key => $select_resp_ops) { ?>
														<option value="<?php echo $select_resp_ops->option_value; ?>" ><?php echo $select_resp_ops->option_value; ?></option>
													<?php }
													    } ?>
												</select>
												<input id="extra_info" type="hidden" name="response_options[<?php echo $resp_type->response_type; ?>][extra_info]" value="please provide further info" />
											</div>
										</div>
									
										<?php /* <div class="resp_<?php echo $resp_type->response_type; ?> resp-type-options form-group" style="display:none">
                                            <label class="text-red no-options-set-<?php echo $resp_type->response_type; ?> "><?php echo ( !empty( $resp_type->response_type_options ) ? '' : 'You have not created any options yet.' ); ?></label>
                                            <label>Please add options for your users to choose from. Click the plus button to add a new one &nbsp;<span style="margin-top:6px" class="add-new-option pull-right pointer" data-resp_type="<?php echo $resp_type->response_type; ?>" title="Click this to add a new option" > <i class="pull-right far fa-plus-square text-green"></i></span>&nbsp;</label>
                                            <span style="margin-left:10px;display:inline-block;float:right;">Check all &nbsp;<input type="checkbox" name="" class='btn-select-all'></span>
                                            <div class="add-more-options-<?php echo $resp_type->response_type; ?>" style="display:<?php echo ( !empty( $resp_type->response_type_options ) ? 'none' : 'block' ); ?>">
                                                <div><em><small>Please note that the options you add are not saved until you complete this process.</small></em></div>
                                                <span><input type="text" plceholder="Type new value..." id="txtOptionName-<?php echo $resp_type->response_type; ?>" />
                                                <input type="button" value="Add" class="add-new-option-btn" data-resp_type="<?php echo $resp_type->response_type; ?>" /></span>
                                            </div>
                                            <div class="row checkbox-options opts-<?php echo $resp_type->response_type; ?>" data-checkbox_type="<?php echo $resp_type->response_type; ?>" >
                                                <?php if( !empty( $resp_type->response_type_options ) ) { foreach( $resp_type->response_type_options as $k => $resp_options ) { ?>
                                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                                        <div class="radio">
                                                            <label><input type="checkbox" name="response_options[<?php echo $resp_type->response_type; ?>][options][]" value="<?php echo $resp_options->option_value; ?>" style="margin-top:6px;" > <?php echo $resp_options->option_value; ?></label>
                                                        </div>
                                                    </div>
                                                <?php } } ?>
                                            </div>

                                            <h4>Which one of these options should trigger additional information?</h4>
                                            <select id="extra-info-<?php echo $resp_type->response_type; ?>" name="response_options[<?php echo $resp_type->response_type; ?>][extra_info_trigger]" class="form-control extra_info_trigger" >
                                                <option value="" >Select type</option>
                                                <?php if( !empty( $resp_type->response_type_options ) ) { foreach( $resp_type->response_type_options as $key => $select_resp_ops ) { ?>
                                                    <option value="<?php echo $select_resp_ops->option_value; ?>" ><?php echo $select_resp_ops->option_value; ?></option>
                                                <?php } } ?>
                                            </select>
                                            <input id="extra_info" type="hidden" name="response_options[<?php echo $resp_type->response_type; ?>][extra_info]" value="please provide further info" />
                                        </div> */?>
										
									<?php } elseif (($resp_type->response_type == 'file') || ($resp_type->response_type == 'signature')) { ?>
									
										<div class="resp_<?php echo $resp_type->response_type; ?> resp-type-options form-group" style="display:none">
											<label>Please add your accepted file types</label>
											<div class="row">
												<?php if (!empty($resp_type->response_type_options)) {
												    foreach ($resp_type->response_type_options as $k => $resp_options) { ?>
													<div class="col-md-3 col-sm-4 col-xs-12">
														<div class="radio">
															<label><input checked type="checkbox" name="response_options[<?php echo $resp_type->response_type; ?>][options][]" value="<?php echo $resp_options->option_value; ?>" style="margin-top:6px;" > <?php echo $resp_options->option_value; ?></label>
														</div>
													</div>
												<?php }
												    } ?>
											</div>
										</div>
									<?php } else { ?>
										<div class="resp_<?php echo $resp_type->response_type; ?> resp-type-options form-group resp-type-unknown" style="display:none">
											
										</div>
									<?php } ?>
									
								<?php }
								    } ?>

							</div>
						</div>
					</div>
				</div>
				<br/>
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-back" data-currentpanel="evidoc_question_creation_panel2" type="button" >Back</button>					
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-success btn-next evidoc-question-creation-steps" data-currentpanel="evidoc_question_creation_panel2" type="button" >Next</button>					
					</div>
				</div>
			</div>
			
			<div class="evidoc_question_creation_panel3" style="display:none" >
				<div class="form-group">
					<label>Is this a mandatory or an optional response?</label>
					<div class="form-group resp-requirement" style="margin:15px 0">
						<div class="row">
							<div class="col-md-3 col-sm-6 col-xs-12">
								<div class="radio">
									<label><input type="radio" name="is_required" value="1" style="margin-top:6px;" > Mandatory</label>
								</div>
							</div>
							<div class="col-md-3 col-sm-6 col-xs-12">
								<div class="radio">
									<label><input checked type="radio" name="is_required" value="0" style="margin-top:6px;" > Optional</label>
								</div>
							</div>
						</div>
					</div>
				</div>
			
				<div>
					<div class="form-group">
						<label>Does this question require file/photo upload?</label>
						<div class="resp-requirement" style="margin:15px 0">
							<div class="row">
								<div class="col-md-3 col-sm-6 col-xs-12">
									<div class="radio">
										<label><input class="files_required" type="radio" name="files_required" value="1" style="margin-top:6px;" > Yes</label>
									</div>
								</div>
								<div class="col-md-3 col-sm-6 col-xs-12">
									<div class="radio">
										<label><input checked class="files_required" type="radio" name="files_required" value="0" style="margin-top:6px;" > No</label>
									</div>
								</div>
							</div>
						</div>
					</div>
				
					<div class="acceptable-file-types" style="display:none">
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<h4>Please select the acceptable file types for this question.</h4>							
							</div>
							<?php if (!empty($general_file_types)) {
							    foreach ($general_file_types as $k => $file_type) { ?>
								<div class="col-md-4 col-sm-4 col-xs-12">
									<div class="radio">
										<label><input class="file_types_opts" type="checkbox" name="file_types[]" value="<?php echo $k; ?>" style="margin-top:6px;" > <?php echo $file_type->file_name; ?></label>
									</div>
								</div>
							<?php }
							    } ?>
						</div>
						
						<div class="row hide">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<h4>Whats the maximumn number of file you'd expect.</h4>							
							</div>
							<div class="form-group">
								<select name="files_max_total" class="form-control" >
									<option value="" >Select</option>
									<option value="1" >1</option>
									<option value="2" >2</option>
									<option value="3" >3</option>
								</select>
							</div>						
						</div>						
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-back" data-currentpanel="evidoc_question_creation_panel3" type="button" >Back</button>					
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-success btn-next evidoc-question-creation-steps" data-currentpanel="evidoc_question_creation_panel3" type="button" >Next</button>					
					</div>
				</div>
			</div>
			
			<div class="evidoc_question_creation_panel4" style="display:none" >
			
				<div class="form-group">
					<label>What section is this linked to?</label>
				</div>
			
				<div class="form-group resp-requirement" style="margin:15px 0">
					<div class="row">
						<div class="col-md-11 col-sm-11 col-xs-11">
							<div class="form-group has-shadow" >
								<select id="section_id" name="section" class="form-control required" data-label_text="EviDocs Section" >
									<option value="" >Select section</option>
									<?php if (!empty($evidoc_type_sections)) {
									    foreach ($evidoc_type_sections as $k => $section) { ?>
										<option value="<?php echo $section->section_name; ?>" ><?php echo $section->section_name; ?></option>
									<?php }
									    } ?>
									
									<?php /*if( !empty( $evidoc_sections ) ) { foreach( $evidoc_sections as $k => $section ) { ?>
                                        <option value="<?php echo $section->section_name; ?>" ><?php echo $section->section_name; ?></option>
                                    <?php } }*/ ?>
								</select>
							</div>
						</div>
						<div class="col-md-1 col-sm-1 col-xs-1">
							<div id="evidoc-section-quick-add" style="margin-top:4px" class="pointer" title="Quick Add new section option"><span class="pull-right"><i class="far fa-plus-square fa-2x text-green"></i></span></div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-back" data-currentpanel="evidoc_question_creation_panel4" type="button" >Back</button>					
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-success btn-next evidoc-question-creation-steps" data-currentpanel="evidoc_question_creation_panel4" type="button" >Next</button>					
					</div>
				</div>
			</div>

			<?php /*<div class="evidoc_question_creation_panel5" style="display:none" >

                <div class="form-group">
                    <label>Does this question need to update a field in the system?</label>
                    <div class="resp-requirement" style="margin:15px 0">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="radio">
                                    <label><input type="radio" name="update_column" value="1" style="margin-top:6px;" > Yes</label>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="radio">
                                    <label><input type="radio" name="update_column" value="0" style="margin-top:6px;" > No</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="update-attribute-name" style="display:none">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <h4>Please select the module column / attribute to update</h4>
                                <div class="form-group has-shadow" >
                                    <select id="update_attribute_id" name="update_attribute_id" class="form-control" data-label_text="EviDocs Attribute" >
                                        <option value="" >Select the attribute</option>
                                        <?php if( !empty( $evidoc_groups ) ) { foreach( $evidoc_groups as $k => $evidoc_group ) { ?>
                                            <option value="<?php echo $evidoc_group->evidoc_group_id; ?>" ><?php echo $evidoc_group->evidoc_group_name; ?> attribute</option>
                                        <?php } } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <button class="btn btn-block btn-flow btn-back" data-currentpanel="evidoc_question_creation_panel5" type="button" >Back</button>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <button class="btn btn-block btn-flow btn-success btn-next evidoc-question-creation-steps" data-currentpanel="evidoc_question_creation_panel5" type="button" >Next</button>
                    </div>
                </div>
            </div> */ ?>
			
			<div class="evidoc_question_creation_panel5" style="display:none" >
				<div class="form-group">
					<div class="text-center">
						<p>You are about to submit a request to create a new Evidoc Question.</p>
						<p>Click the "Create EviDoc&trade; Question" to proceed or Back to review your setup.</p>
					</div>
				</div>
				<div class="form-group resp-requirement" style="margin:15px 0">
					<div class="row">
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button class="btn btn-block btn-flow btn-back" data-currentpanel="evidoc_question_creation_panel5" type="button" >Back</button>					
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button id="create-evidoc-question-btn" class="btn btn-block btn-flow btn-success btn-next" type="button" >Create EviDoc&trade; Question</button>
						</div>
					</div>
				</div>
			</div>
		</form>
		
		<!-- Modal for adding a new Section -->
		<div class="modal fade add-section-modal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
						<h4 class="modal-title" id="myModalLabel">Section <small><em>Quick Add</em></small></h4>
					</div>
					<div id="add-section-form-container" class="modal-body">
						<div class="row">
							<input type="hidden" name="page" value="details" />
							<input type="hidden" name="audit_type_id" value="<?php echo $evidoc_type_details->audit_type_id; ?>" />
							<div class="col-md-12 col-sm-12 col-xs-12">
								<h4>What is the Section name? <small><i class="fas fa-info text-blue pull-right help" title="You can only add a section that does not already exist"></i></small></h4>
								<div class="form-group">
									<input name="section_name" class="form-control" type="text" value="" placeholder="Add a missing section to your list" required=required />
								</div>
							</div>
							<div class="col-md-12 col-sm-12 col-xs-12">
								<button id="question-quick-add-section-btn" class="btn btn-block btn-success" type="button" >Add Section</button>					
							</div>
						</div>										
					</div>
				</div>
			</div>
		</div>
		
	</div>
</div>
	
<script>

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
		
		$('.btn-select-all').change(function(){
			selectAllChecked = $(this).prop('checked')
			$(this).closest('.form-group').find('input[type=checkbox]').prop('checked', selectAllChecked)
		})
		
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
		$( '.files_required' ).change( function(){
			optChoice = $("input[name='files_required']:checked").val();
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
		
		//Section Quick Add
		$( '#evidoc-section-quick-add' ).click(function(){
			$(".add-section-modal").modal("show");
		});
		
		// Section Quick add
		$( '#question-quick-add-section-btn' ).click(function(){
		
			var formData = $( "#add-section-form-container :input").serialize();
		
			$.ajax({
				url:"<?php echo base_url('webapp/audit/add_new_section/'); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){
						
						$(".add-section-modal").modal("hide");
						
						var sectionId 	 = data.section.section_id;
						var sectionName  = data.section.section_name;
						
						$('#feedback_message').html( data.status_msg ).delay( 3000 ).fadeToggle( "slow" );
						
						var profileOptionExists 	= ( $('#section_id option[value="' + sectionName + '"]').length > 0 );
						
						if( !profileOptionExists ){
							//Only add the new option if it doesn't already exist
							$('#section_id').append( $('<option >').val( sectionName ).text( sectionName ) );
						}
						
						//Set selected
						$('#section_id option[value="'+sectionName+'"]').prop( 'selected', true );
						
					}else{
						swal({
							type: 'error',
							title: data.status_msg
						})
					}		
				}
			});
			return false;
			
		});
		
		$( '#response_type_id' ).change( function(){
			var respType 	= $( 'option:selected', this ).data( 'resp_type' );
			var respTypeAlt = $( 'option:selected', this ).data( 'resp_type_alt' );
			var respDesc 	= $( 'option:selected', this ).data( 'resp_desc' );

			$( '.resp-extra-options' ).hide();
			$( '#selected-option' ).text( '' );
			if( respType.length > 0 ){
				$( '.resp-extra-options' ).show();
				$( '#selected-option' ).html( '<span>' + respTypeAlt + ' - ' + '' + respDesc +'</span>' );
			}
			
			$( '.resp-type-options' ).hide();
			$( '.resp-type-options' ).hide();
			$( '.resp_' + respType ).show();
			
		})
		
		$(".evidoc-question-creation-steps").click(function(){

			//Clear errors first
			$( '.error_message' ).each(function(){
				$( this ).text( '' );
			});
			
			var currentpanel = $(this).data( "currentpanel" );
			
			var inputs_state = check_inputs( currentpanel );			
			if( inputs_state ){
				//If name attribute returned, auto focus to the field and display arror message
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
		$(".btn-back").click(function(){
			var currentpanel = $(this).data( "currentpanel" );
			go_back("."+currentpanel)	
			return false;
		});
		
		function panelchange( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )+parseInt(1);
			var changeto = ".evidoc_question_creation_panel"+panelnumber;
			$( changefrom ).hide( "slide", {direction : 'left'}, 500);
			$( changeto ).delay(600).show( "slide", {direction : 'right'},500);	
			return false;	
		}
		
		function go_back( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
			var changeto = ".evidoc_question_creation_panel"+panelnumber;
			$( changefrom ).hide( "slide", { direction : 'right' }, 500);
			$( changeto ).delay(600).show( "slide", { direction : 'left' },500);	
			return false;	
		}
		
		/*
		* Submit Evidoc Question form
		*/
		$( '#create-evidoc-question-btn' ).click(function( e ){
		
			e.preventDefault();
			
			var formData = $('#evidoc-question-creation-form').serialize();
			
			swal({
				title: 'Confirm Evidoc question submission?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/audit/create_evidoc_question/'); ?>",
						method:'POST',
						data:formData,
						dataType: 'json',
						success:function( data ){
							
							if( ( data.status == 1 ) && ( data.evidoc_question !== '' ) ){
								
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){
									location.reload();
								} ,2000);							
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}		
						}
					});
				}else{
					$( ".evidoc_question_creation_panel5" ).hide( "slide", { direction : 'left' }, 500 );
					go_back( ".evidoc_question_creation_panel2" );
					return false;
				}
			}).catch( swal.noop )
		});

		
		$('.btn-select-all').change(function(){
			selectAllChecked = $(this).prop('checked')
			$(this).closest('.form-group').find('input[type=checkbox]').prop('checked', selectAllChecked)
		})
		
	});
</script>