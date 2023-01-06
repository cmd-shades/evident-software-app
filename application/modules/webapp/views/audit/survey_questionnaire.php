<?php if( !empty( $category_sections ) ) { $counter = count( $category_sections ); foreach( $category_sections as $k => $category_section ) { $k++; ?>

	<div class="survey_creation_panel<?php echo $k; ?> col-md-4 col-md-offset-4 col-sm-12 col-xs-12" style="display:<?php echo ( $k == 1 ) ? 'block' : 'none' ?>">
		<div class="x_panel tile has-shadow">
			<legend><?php echo $category_section;?></legend>
			
			<!-- Loop over all the questions within the section -->
			<?php if( !empty( $category_questions->{$category_section} ) ) { foreach(  $category_questions->{$category_section} as $key => $question ){ $key++; ?>
			
				<?php  $sectGrp = preg_replace('/[^A-Za-z0-9\-]/','', $category_section ); ?>
				
				<div class="common-fields sectGrp<?php echo $sectGrp; ?>" style="display:block" >
					<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
					<input type="hidden" name="page" value="details"/>
					<input type="hidden" name="request_source" value="<?php echo $request_source; ?>"/>
					<input type="hidden" name="survey_id" value="" class="survey_id"  />
					<input type="hidden" name="survey_type_id" value="1" />
					<input type="hidden" name="site_id" value="1" />

					<div class="form-group">
						<label id="unique-id-label"><?php echo $key.'. '.$question->question; ?></label>
						<input type="hidden" name="responses[questions][<?php echo $question->question_id; ?>][question_id]" 	value="<?php echo $question->question_id; ?>" class="sectGrp<?php echo $sectGrp; ?>" >
						<input type="hidden" name="responses[questions][<?php echo $question->question_id; ?>][question]" 		value="<?php echo $question->question; ?>"  class="sectGrp<?php echo $sectGrp; ?>">
						<input type="hidden" name="responses[questions][<?php echo $question->question_id; ?>][section]" 		value="<?php echo $question->section; ?>" class="sectGrp<?php echo $sectGrp; ?>">
						<input type="hidden" name="responses[questions][<?php echo $question->question_id; ?>][segment]" 		value="<?php echo $question->segment; ?>" class="sectGrp<?php echo $sectGrp; ?>">
						<input type="hidden" name="responses[questions][<?php echo $question->question_id; ?>][ordering]" 		value="<?php echo $question->ordering; ?>" class="sectGrp<?php echo $sectGrp; ?>">
						<input type="hidden" name="responses[questions][<?php echo $question->question_id; ?>][survey_block]" 	value="<?php echo $question->survey_block; ?>" class="sectGrp<?php echo $sectGrp; ?>">
					
						<?php if( strtolower( $question->response_type ) == 'radio' ){ ?>
							<!-- Render Radio buttons -->
							<div style="margin-left:15px;">
								<?php foreach( $question->response_options as $n => $value ){ ?>
									<label class="radio-inline" data-opt_value="" ><input type="radio" name="responses[questions][<?php echo $question->question_id; ?>][response]" value="<?php echo $value; ?>" class="sectGrp<?php echo $sectGrp; ?> radio-options" data-extra_info_trigger="<?php echo $question->extra_info_trigger; ?>" data-info_key="<?php echo $key; ?>" ><?php echo $value; ?></label>
								<?php } ?>
							</div>
						<?php } else if ( strtolower( $question->response_type ) == 'input' || strtolower( $question->response_type ) == 'datepicker' ) { ?>
							
							<!-- Render input field -->
							<input name="responses[questions][<?php echo $question->question_id; ?>][response]" class="form-control sectGrp<?php echo $sectGrp; ?> <?php echo ( strtolower( $question->response_type ) == 'datepicker' ) ? 'datepicker' : '' ?>" type="text" value="" placeholder=""  />
							
						<?php } else if ( strtolower( $question->response_type ) == 'text' ) { ?>
							
							<!-- Render input field -->
							<textarea name="responses[questions][<?php echo $question->question_id; ?>][response]" class="form-control sectGrp<?php echo $sectGrp; ?>" type="text" value="" ></textarea>
							
						<?php } else if ( strtolower( $question->response_type ) == 'files' ) { ?>
							
							<!-- Render input field for files -->
							<input name="responses[questions][<?php echo $question->question_id; ?>][response]" class="form-control sectGrp<?php echo $sectGrp; ?> " type="file" value="" />
							
						<?php } else if ( strtolower( $question->response_type ) == 'select' ) { ?>
							
							<!-- Render select field -->
							<select name="responses[questions][<?php echo $question->question_id; ?>][response]" class="form-control">
								<option value="">Please select 1</option>
								<?php if( !empty( $question->response_options ) ) { foreach( $question->response_options as $opt => $option ) { ?>
									<option value="<?php echo $option; ?>" ><?php echo $option; ?></option>
								<?php } } ?>
							</select>
							
						<?php } ?>
						
						<div class="extra-info-response<?php echo $key; ?>" style="display:none" >
							<textarea type="text" name="responses[questions][<?php echo $question->question_id; ?>][response_extra]" value="" class="sectGrp<?php echo preg_replace('/[^A-Za-z0-9\-]/','', $category_section ); ?> form-control" placeholder="<?php echo htmlspecialchars( $question->extra_info ); ?>" ></textarea>										
						</div>
					</div>
				</div>
			<?php } } ?>

			<!-- Display the relevant button(s) -->
			<?php if( $k == 1 ) { ?>
					<div class="row">
						<?php if( $k == $counter ) { ?>
							<div class="col-md-12 col-sm-12 col-xs-12">
								<button id="submit-survey-btn" class="btn btn-block btn-flow btn-success btn-next" type="button" data-section_tag="<?php echo $sectGrp; ?>" >Submit Survey</button>					
							</div>
						<?php }else{ ?>
							<div class="col-md-12 col-sm-12 col-xs-12">
								<button class="btn btn-block btn-flow btn-success btn-next survey-creation-steps" data-currentpanel="survey_creation_panel<?php echo $k; ?>" type="button" data-section_tag="<?php echo $sectGrp; ?>" >Next</button>					
							</div>
						<?php } ?>
					</div>
			
			<?php } else if( $k == $counter ) { ?>
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-info btn-back" data-currentpanel="survey_creation_panel<?php echo $k; ?>" type="button" >Back</button>					
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button id="submit-survey-btn" class="btn btn-block btn-flow btn-success btn-next" type="button" data-section_tag="<?php echo $sectGrp; ?>" >Submit Survey</button>					
					</div>
				</div>
			<?php } else { ?>
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-info btn-back" data-currentpanel="survey_creation_panel<?php echo $k; ?>" type="button" >Back</button>					
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<button class="btn btn-block btn-flow btn-success btn-next survey-creation-steps" data-currentpanel="survey_creation_panel<?php echo $k; ?>" type="button" data-section_tag="<?php echo $sectGrp; ?>" >Next</button>					
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
<?php } } ?>