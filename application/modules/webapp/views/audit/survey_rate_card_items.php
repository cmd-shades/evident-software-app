<?php if( !empty( $category_sections ) ) { $counter = count( $category_sections ); foreach( $category_sections as $k => $category_section ) { $k++; ?>

	<div class="survey_creation_panel<?php echo $k; ?> col-md-4 col-md-offset-4 col-sm-12 col-xs-12" style="display:<?php echo ( $k == 1 ) ? 'block' : 'none' ?>">
		<div class="x_panel tile has-shadow">
			<legend><?php echo $category_section;?></legend>
			
			<?php  $sectGrp = preg_replace('/[^A-Za-z0-9\-]/','', $category_section ); ?>

			<!-- Loop over all the items within the section -->
			<?php if( !empty( $category_questions->{$category_section} ) ) { foreach(  $category_questions->{$category_section} as $key => $item ){ $key++; ?>

				<div class="common-fields sectGrp<?php echo $sectGrp; ?>" style="display:block" >
					<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
					<input type="hidden" name="page" value="details"/>
					<input type="hidden" name="request_source" value="<?php echo $request_source; ?>"/>
					<input type="hidden" name="survey_id" value="<?php echo ( !empty( $survey_id ) ) ? $survey_id : null; ?>" class="survey_id"  />
					<input type="hidden" name="survey_type_id" value="1" />
					<input type="hidden" name="site_id" value="1" />
					<div class="form-group">
						<div class="row">
							<div class="col-md-9">
								<!-- Item description -->
								<label id="unique-id-label"><?php echo $key.'. '; ?><span style="font-weight:400"><?php echo $item->item; ?></span></label>
							</div>
							<div class="col-md-3">
								<!-- Item Qty -->
								<input type="hidden" name="survey_items[<?php echo $item->item_code; ?>][item_code]" 	value="<?php echo $item->item_code; ?>" class="sectGrp<?php echo $sectGrp; ?>" >
								<input type="number" min="1" name="survey_items[<?php echo $item->item_code; ?>][item_qty]" class="form-control sectGrp<?php echo $sectGrp; ?>" value="" placeholder="Qty"  />
							</div>
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