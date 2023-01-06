<style>
	.panel-body{
		background-color:#F7F7F7; 
		height:140px; 
		min-height:140px;
	}
	
	.full-width{
		width:100%;
	}
</style>


<div>
	<div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12">
		<legend class="evidocs-legend">Add New EviDoc</legend>		
		<div class="x_panel tile has-shadow">
			<form id="evidoc-creation-form" >
				<input type="hidden" name="override_existing" value="" />
				<input type="hidden" name="page" value="details" />
				<input type="hidden" name="audit_type_id" value="" />
				<div class="rows">
				
					<div class="evidoc_type_creation_panel1 col-md-12 col-sm-12 col-xs-12">
						<div>
							<h4>What would you like to name this Evidoc?</h4>
							<div class="has-shadow">
								<div class="form-group">
									<input name="audit_type" class="form-control required" type="text" data-label_text="EviDocs name" placeholder="Enter a short name here max 30 chars" value="" />
								</div>							
							</div>
							<div>
								<h4>Please provide a detailed description</h4>
								<div class="has-shadow">
									<div class="form-group">
										<textarea name="audit_type_desc" class="form-control" type="text" placeholder="Give a detailed description of what this Evidoc does..." value=""></textarea>
									</div>							
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button id="check-exists" class="btn btn-block btn-flow btn-success btn-next evidoc-type-creation-steps" data-currentpanel="evidoc_type_creation_panel1" type="button">Next</button>				
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">&nbsp;</div>
						</div>
					</div>

					<div class="evidoc_type_creation_panel2 col-md-12 col-sm-12 col-xs-12" style="display:none" >
					
						<div>
							<h4>What group does this Evidoc belong to?</h4>
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="form-group  has-shadow" >
										<select id="audit_group" name="audit_group" class="form-control required" data-label_text="EviDocs Group" >
											<option value="" >Select group</option>
											<?php if( !empty( $evidoc_groups ) ) { foreach( $evidoc_groups as $k => $group ) { ?>
												<option value="<?php echo ucwords( strtolower( $group->evidoc_group ) ); ?>" ><?php echo $group->evidoc_group_name; ?></option>
											<?php } } ?>
										</select>
									</div>
								</div>
							</div>
						</div>
						
						<div class="asset-types" style="display:none" >
							<h4>What asset type is it?</h4>
							<div class="row">
								<div class="col-md-11 col-sm-11 col-xs-12">
									<div class="form-group  has-shadow" >
										<select id="asset_type_id" name="asset_type_id" class="form-control required" data-label_text="EviDocs Asset type" >
											<option value="" >Please Select a type</option>
												<?php if( !empty( $asset_types ) ) { foreach( $asset_types as $category => $asset_categories ) { ?>
													<optgroup label="<?php echo ucwords( $category ); ?>">
														<?php foreach( $asset_categories as $k => $asset_type ) { ?>
															<option value="<?php echo $asset_type->asset_type_id; ?>" ><?php echo $asset_type->asset_type; ?></option>
														<?php } ?>
													</optgroup>
												<?php } } ?>
										</select>
									</div>
								</div>
								<div class="col-md-1 col-sm-1 col-xs-1">
									<div id="asset-type-quick-add" style="margin-top:4px" class="pointer" title="Quick Add new Asset type option"><span class="pull-right"><i class="fas fa-plus-circle fa-2x text-green"></i></span></div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-back" data-currentpanel="evidoc_type_creation_panel2" type="button" >Back</button>					
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-success btn-next evidoc-type-creation-steps" data-currentpanel="evidoc_type_creation_panel2" type="button" >Next</button>					
							</div>
						</div>
					</div>
					
					<div class="evidoc_type_creation_panel3 col-md-12 col-sm-12 col-xs-12" style="display:none" >
						<div class="">
							<h4>Which Discipline does this Evidoc belong to?</h4>
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="form-group has-shadow" >
										<select id="discipline_id" name="discipline_id" class="form-control required" data-label_text="Discipline" >
											<option value="" >Please Select a Discipline</option>
											<?php if( !empty( $disciplines ) ) { foreach( $disciplines as $k => $discipline ) { ?>
												<option value="<?php echo $discipline->discipline_id; ?>" ><?php echo $discipline->account_discipline_name; ?></option>
											<?php } } ?>
										</select>
									</div>
								</div>
							</div>
						</div>
						
						<div>
							<h4>Please confirm the Evidoc category this belong to?</h4>
							<div class="row">
								<div class="col-md-11 col-sm-11 col-xs-11">
									<div class="form-group has-shadow" >
										<select id="evidoc_category_id" name="category_id" class="form-control required" data-label_text="EviDocs Category" >
											<option value="" >Please Select a category</option>
											<?php if( !empty( $evidoc_categories ) ) { foreach( $evidoc_categories as $k => $category ) { ?>
												<option value="<?php echo $category->category_id; ?>" ><?php echo $category->category_name_alt; ?> <?php echo ( !empty( $category->description ) ) ? ' - '.$category->description : ''; ?></option>
											<?php } } ?>
										</select>
									</div>
								</div>
								<div class="col-md-1 col-sm-1 col-xs-1">
									<div id="evidoc-category-quick-add" style="margin-top:4px" class="evidoc-category-quick-add pointer" title="Quick Add new category option"><span class="pull-right"><i class="fas fa-plus-circle fa-2x text-green"></i></span></div>
								</div>
							</div>
						</div>

						<div>
							<h4>When should this Evidoc be completed?</h4>
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-11">
									<div class="form-group has-shadow" >
										<select id="audit_frequency" name="audit_frequency" class="form-control required" data-label_text="EviDocs Frequency" >
											<option value="" >Please Select a frequency</option>
											<?php if( !empty( $evidoc_frequencies ) ) { foreach( $evidoc_frequencies as $k => $frequency ) { ?>
												<option value="<?php echo $frequency->frequency; ?>" data-schedule_required="<?php echo $frequency->schedule_required; ?>" <?php echo ( $frequency->frequency == 'Ad Hoc' ) ? 'selected=selected' : ''; ?> ><?php echo $frequency->frequency_alt; ?></option>
											<?php } } ?>
										</select>
									</div>
								</div>
								
								<div class="schedule-frequency" style="display:none" >
									<div class="col-md-12 col-sm-12 col-xs-12">
										<h4>How often should this done?</h4>
										<div class="form-group  has-shadow" >
											<select id="frequency_id" name="frequency_id" class="form-control" style="width:100%" data-label_text="Schedule Frequency" >
												<option value="" >Select schedule frequency</option>
												<?php if( !empty( $schedule_frequencies ) ) { foreach( $schedule_frequencies as $k => $sch_frequency ) { ?>
													<option value="<?php echo $sch_frequency->frequency_id; ?>" ><?php echo $sch_frequency->frequency_desc; ?></option>
												<?php } } ?>
											</select>
										</div>
									</div>
								</div>
								
								<div class="col-md-1 col-sm-1 col-xs-1 hide">
									<div id="evidoc-frequency-quick-add" style="margin-top:4px" class="pointer" title="Quick Add new frequency option"><span class="pull-right"><i class="fas fa-plus-circle fa-2x text-green"></i></span></div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-back" data-currentpanel="evidoc_type_creation_panel3" type="button" >Back</button>					
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-success btn-next evidoc-type-creation-steps" data-currentpanel="evidoc_type_creation_panel3" type="button" >Next</button>					
							</div>
						</div>
					</div>
					
					<div class="evidoc_type_creation_panel4 col-md-12 col-sm-12 col-xs-12" style="display:none" >
						<div>
							<h4>Does this Evidoc require an outcome on completion? <small><i class="fas fa-info text-blue pull-right help" title="E.g. if you required the Evidoc to be marked as Passed, Failed etc, select 'Yes'"></i></small></h4>
							<div class="row">
								<div class="col-md-3 col-sm-6 col-xs-12">
									<div class="radio">
										<label><input type="radio" name="outcome_required" value="1" style="margin-top:6px;" checked > Yes</label>
									</div>
								</div>
								<div class="col-md-3 col-sm-6 col-xs-12">
									<div class="radio">
										<label><input type="radio" name="outcome_required" value="0" style="margin-top:6px;" > No</label>
									</div>
								</div>
							</div>
						</div>
						<div>
							<h4>Does this Evidoc require Supervisor approval before completion? <small><i class="fas fa-info text-blue pull-right help" title="Do you require a Supervisor to approve the Evidoc photos before the Evidoc is completed?"></i></small></h4>
							<div class="row">
								<div class="col-md-3 col-sm-6 col-xs-12">
									<div class="radio">
										<label><input type="radio" name="approval_required" value="1" style="margin-top:6px;" checked > Yes</label>
									</div>
								</div>
								<div class="col-md-3 col-sm-6 col-xs-12">
									<div class="radio">
										<label><input type="radio" name="approval_required" value="0" style="margin-top:6px;" > No</label>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-back" data-currentpanel="evidoc_type_creation_panel4" type="button" >Back</button>					
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-success btn-next evidoc-type-creation-steps" data-currentpanel="evidoc_type_creation_panel4" type="button" >Next</button>
							</div>
						</div>
					</div>
					
					<div class="evidoc_type_creation_panel5 col-md-12 col-sm-12 col-xs-12" style="display:none" >
						<div>
							<h4>Which contract is this Evidoc for?</h4>
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="form-group  has-shadow" >
										<select id="contract_id" name="contract_id" class="form-control" style="width:100%" data-label_text="Linked contract" >
											<option value="" >Select linked contract</option>
											<?php if( !empty( $available_contracts ) ) { foreach( $available_contracts as $k => $contract ) { ?>
												<option value="<?php echo $contract->contract_id; ?>" ><?php echo $contract->contract_name; ?></option>
											<?php } } ?>
										</select>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-back" data-currentpanel="evidoc_type_creation_panel5" type="button" >Back</button>					
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-success btn-next evidoc-type-creation-steps" data-currentpanel="evidoc_type_creation_panel5" type="button" >Next</button>					
							</div>
						</div>
					</div>
					
					<div class="evidoc_type_creation_panel6 col-md-12 col-sm-12 col-xs-12" style="display:none" >
						<legend>Read Only Information (Optional)</legend>
						<p>Use this section to provide readonly information that you would like the EviDoc users to be aware of before completing the EviDoc record.</p>
						<div>
							<div class="form-group">
								<h5 class="text-bold">Introduction Header</h5>
								<input name="info_intro_header" class="form-control" type="text" placeholder="Introduction Main Text" />
							</div>
							<div class="form-group">
								<h5 class="text-bold">Introduction Text</h5>
								<textarea name="info_intro_text" class="form-control" type="text" value="" style="width:100%;" placeholder="Introduction Text"></textarea>
							</div>								
						</div>

						<div>
							<div class="form-group">
								<h5 class="text-bold">Body Header</h5>
								<input name="info_main_header" class="form-control" type="text" placeholder="Body Header Text" />
							</div>
							<div class="form-group">
								<h5 class="text-bold">Body Text</h5>
								<textarea name="info_main_text" class="form-control" type="text" value="" style="width:100%;" placeholder="Body Text"></textarea>
							</div>								
						</div>
						
						<div>
							<div class="form-group">
								<h5 class="text-bold">Summary Header</h5>
								<input name="info_summary_header" class="form-control" type="text" placeholder="Summary Header Text" />
							</div>
							<div class="form-group">
								<h5 class="text-bold">Summary Text</h5>
								<textarea name="info_summary_text" class="form-control" type="text" value="" style="width:100%;" placeholder="Summary Text"></textarea>
							</div>								
						</div>

						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-back" data-currentpanel="evidoc_type_creation_panel6" type="button" >Back</button>					
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-success btn-next evidoc-type-creation-steps" data-currentpanel="evidoc_type_creation_panel6" type="button" >Next</button>					
							</div>
						</div>
					</div>
					
					<div class="evidoc_type_creation_panel7 col-md-12 col-sm-12 col-xs-12" style="display:none" >
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<div class="form-group">
									<div class="text-center">
										<p>You are about to submit a request to create a new EviDoc Name.</p>
										<p>Click the "Create EviDoc" to proceed or Back to review your setup.</p>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-back" data-currentpanel="evidoc_type_creation_panel7" type="button" >Back</button>					
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button id="create-evidoc-type-btn" class="btn btn-block btn-flow btn-success btn-next" type="button" >Create EviDoc</button>
							</div>
						</div>
					</div>
				</div>
			</form>

			<!-- Modal for adding a new Frequency -->
			<div class="modal fade add-frequency-modal" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-md">
					<div class="modal-content">
						<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title" id="myModalLabel">Frequency <small><em>Quick Add</em></small></h4>
						</div>
						<div id="add-frequency-form-container" class="modal-body">
							<div class="row">
								<input type="hidden" name="page" value="details" />
								<div class="col-md-12 col-sm-12 col-xs-12">
									<h4>What frequency would you like to add? <small><i class="fas fa-info text-blue pull-right help" title="You can only add a frequency that does not already exist"></i></small></h4>
									<div class="form-group">
										<input name="frequency_alt" class="form-control" type="text" value="" placeholder="Add a missing frequency to your list" required=required />
									</div>
								</div>
								<div class="col-md-12 col-sm-12 col-xs-12">
									<button id="quick-add-btn" class="btn btn-block btn-success" type="button" >Add Frequency</button>					
								</div>
							</div>										
						</div>
					</div>
				</div>
			</div>
			
			<!-- Modal for adding a new asset type -->
			<div class="modal fade add-asset-type-modal" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-md">
					<form id="add-asset-type-form">
						<input type="hidden" name="page" value="details" />
						<div class="modal-content">
							<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
								<h4 class="modal-title" id="myAssetTypeModalLabel">Add a New Asset Type</h4>
							</div>
							<div class="modal-body">
								<div class="row">
									<div class="col-md-12 col-sm-12 col-xs-12">
										<div class="form-group">
											<div class="col-md-12 col-sm-12 col-xs-12">
												<h4>What type of Asset is it?</h4>
												<div class="form-group">
													<input name="asset_type" class="form-control" type="text" value="" placeholder="Asset type" required=required />
												</div>
											</div>
											<div class="col-md-12 col-sm-12 col-xs-12">
												<h4 title="You can only add a type that does not already exist" >What category does this belong to?</h4>
											</div>
											<div class="col-md-11 col-sm-11 col-xs-12">
												<div class="form-group has-shadow" >
													<select id="category_id" name="category_id" class="form-control required">
														<option value="">Please select category</option>
														<?php if( !empty( $evidoc_categories ) ) { foreach( $evidoc_categories as $k => $category ) { ?>
															<option value="<?php echo $category->category_id; ?>" ><?php echo $category->category_name_alt; ?></option>
														<?php } } ?>
													</select>
												</div>
											</div>
											<div class="col-md-1 col-sm-1 col-xs-1">
												<div style="margin-top:4px" class="evidoc-category-quick-add pointer" title="Quick Add new category option"><span class="pull-right"><i class="fas fa-plus-circle fa-2x text-green"></i></span></div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-md-12 col-sm-12 col-xs-12">
												<h4>Please set the sub-group</h4>
												<select id="asset_group" name="asset_group" class="form-control required">
													<option value="">Please select category</option>
													<option value="comm device" >Communication device (Phones, PCs, Laptops)</option>
													<option value="plant" >Plant (Harnesses, Helmets etc.)</option>
													<option value="device" selected="selected" >Installable assets item</option>
													<option value="asset" >Any asset item</option>
												</select>
											</div>
										</div>
									</div>
									<div class="col-md-12 col-sm-12 col-xs-12" style="margin-top:10px">
										<div class="form-group">
											<div class="col-md-12 col-sm-12 col-xs-12">
												<button id="add-asset-type-btn" class="btn btn-block btn-success" type="button" >Add Asset Type</button>					
											</div>
										</div>
									</div>
								</div>										
							</div>
						</div>
					</form>
				</div>
			</div>
			
			<!-- Modal for adding a new category -->
			<div class="modal fade add-category-modal" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-md">
					<form id="add-category-form-container" >
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
								<h4 class="modal-title" id="myCategoryModalLabel">Add an EviDoc Category</h4>
								<span id="category-feedback-message"></span>
							</div>
							<div class="modal-body">
								<div class="row">
									<input type="hidden" name="page" value="details" />
									<div class="col-md-12 col-sm-12 col-xs-12">
										<h4>What is the name of this Category?</h4>
										<div class="form-group">
											<input name="category_name" class="form-control" type="text" value="" placeholder="Category name" required=required />
										</div>
										<h4>Please provide a description</h4>
										<div class="form-group">
											<textarea rows="4" name="description" class="form-control" type="text" value="" placeholder="Please provide a description of your Category" required=required /></textarea>
										</div>
									</div>
								</div>										
							</div>
							<div class="modal-footer">
								<div class="row">
									<div class="col-md-12 col-sm-12 col-xs-12">
										<button id="category-quick-add-btn" class="btn btn-success btn-block" type="button" >Add New Category</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script>

	$(document).ready( function() {
		
		$( '#contract_id' ).select2();
		
		//Check for Audit group
		$( '#audit_group' ).change( function(){
			var selectedGroup = $( 'option:selected', this ).val().toLowerCase();
			$( '.asset-types' ).slideUp( 'slow' );
			$( '#asset_type_id' ).removeClass( 'required' );
			if( selectedGroup == 'asset' ){
				$( '#asset_type_id' ).addClass( 'required' );
				$( '.asset-types' ).slideDown( 'slow' );
			}else{
				$( '[name="asset_type_id"]' ).val( '' );
				$( '#asset_type_id' ).removeClass( 'required' );
			}

			return false;
		});
		
		$( '#audit_frequency' ).change( function(){
			var scheduleFreq = $( 'option:selected', this ).data( 'schedule_required' );
			if( scheduleFreq == 1 ){
				$( '.schedule-frequency' ).slideDown( 'fast' );
			} else {
				$( '.schedule-frequency' ).slideUp( 'fast' );
			}			
		});
		
		//Clear red-bordered elements
		$( '.required' ).change( function(){
			$( this ).css("border","1px solid #ccc");
		});
		
		//Trigger search field
		$('[name="evidoc_type"]').keyup(function(){
			$( this ).focus().css("border","1px solid #ccc");
			$('[name="evidoc_type"]').val();
		});
		
		$( '#check-exists' ).click( function(){
			
			return false;
			
			var currentpanel = $( this ).data( "currentpanel" );
			var evidocName	 = encodeURIComponent( $( '[name="evidoc_type"]').val() );
			var wheRe 		 = { 	
				'evidoc_type':evidocName
			};
			
			if( evidocName.length == 0 ){
				$( '[name="evidoc_type"]' ).focus().css("border","1px solid red");
				swal({
					type: 'error',
					text: 'EviDoc name is required'
				})
				return false;
			}
			
			$.ajax({
				url:"<?php echo base_url('webapp/audit/check_evidoc_exists/' ); ?>",
				method:"POST",
				data:{ page:'details', where:wheRe },
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 ){
						//Handle the event confirm
						swal({
							type: 'warning',
							showCancelButton: true,
							confirmButtonColor: '#5CB85C',
							cancelButtonColor: '#9D1919',
							confirmButtonText: 'Override',
							title: 'This Evidoc already exists!',
							html:
								'<b>Evidoc Name</b>' + data.evidoc_type.evidoc_type + '<br/>' +
								'<b>Description</b><br/>' +
								'<em>' + data.evidoc_type.audit_type_desc + '</em>' + '<br/><br/>' +
								'Click <a href="#">here</a> to edit it or Cancel to change the name'
						}).then( function (result) {
							if ( result.value ) {
								//Do this if user accepts to Override
								$( '[name="audit_type_id"]' ).val( data.evidoc_type.audit_type_id );
								$( '[name="override_existing"]' ).val( 1 );
								$( '[name="audit_type_desc"]' ).val( data.evidoc_type.audit_type_desc );								
								$( '[name="audit_frequency"]' ).val( data.evidoc_type.audit_frequency );
								
								//Proceed with next screen
								panelchange("."+currentpanel);								
							}else{
								//Do this if user cancels to change the name
								$( '[name="audit_type_id"]' ).val( '' );
								$( '[name="override_existing"]' ).val( '' );
								$( '[name="audit_frequency"]' ).val( '' );
								$( '[name="audit_type_desc"]' ).val( '' );
							}
						})
						
						return false;
						
					}else{
						//Proceed with next screen
						panelchange("."+currentpanel);
					}			
				}
			});
			
		});
		
		$(".evidoc-type-creation-steps").click(function(){
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
					labelText = ( labelText !== "" && ( labelText.length > 0 ) ) ? labelText : $( '[name="'+inputs_state+'"]' ).data( 'label_text' )+' field is required';
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
			var currentpanel = $(this).data("currentpanel");
			go_back("."+currentpanel)	
			return false;
		});
		
		function panelchange(changefrom){
			var panelnumber = parseInt( changefrom.match(/\d+/) )+parseInt(1);
			var changeto = ".evidoc_type_creation_panel"+panelnumber;
			$( changefrom ).hide( "slide", {direction : 'left'}, 500);
			$( changeto ).delay(600).show( "slide", {direction : 'right'},500);	
			return false;	
		}
		
		function go_back( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
			var changeto = ".evidoc_type_creation_panel"+panelnumber;
			$( changefrom ).hide( "slide", {direction : 'right'}, 500);
			$( changeto ).delay(600).show( "slide", {direction : 'left'},500);	
			return false;	
		}

		//FRequency Quick Add
		$( '#evidoc-frequency-quick-add' ).click(function(){
			$(".add-frequency-modal").modal("show");
			
		});
		
		// Frequency Quick add
		$( '#quick-add-btn' ).click(function(){

			var formData = $( "#add-frequency-form-container :input").serialize();
		
			$.ajax({
				url:"<?php echo base_url('webapp/audit/add_new_frequency/' ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){
						
						$(".add-frequency-modal").modal("hide");
						
						var frequencyId   = data.frequency.frequency_id;
						var frequencyName = data.frequency.frequency_alt;

						$('#feedback_message').html( data.status_msg ).delay(3000).fadeToggle("slow");
						
						var optionExists = ( $('#audit_frequency option[value=' + frequencyId + ']').length > 0 );

						if( !optionExists ){
							//Only add the new option if it doesn't already exist
							$('#audit_frequency').append( $('<option >').val( frequencyId ).text( frequencyName ) );
						}
						
						//Set selected
						$('#audit_frequency option[value="'+frequencyId+'"]').prop( 'selected', true );
						
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
		
		//Submit Evidoc form
		$( '#create-evidoc-type-btn' ).click(function( e ){
		
			e.preventDefault();
			
			submitEvitdocTypeForm();
			
		});
		
		function submitEvitdocTypeForm(){
			
			var formData = $('#evidoc-creation-form').serialize();
			
			swal({
				title: 'Confirm Evidoc creation?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function ( result ) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/audit/create_evidoc_type/' ); ?>",
						method:'POST',
						data:formData,
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 && ( data.evidoc_type !== '' ) ){
								
								var alreadyExists   = data.already_exists;
								var newEvidocTypeId = data.evidoc_type.audit_type_id;
								
								if( alreadyExists ){
									var existUrl = "<?php echo base_url('webapp/audit/evidoc_names/' ); ?>"+data.evidoc_type.audit_type_id;
									swal({
										type: 'warning',
										showCancelButton: true,
										confirmButtonColor: '#5CB85C',
										cancelButtonColor: '#9D1919',
										confirmButtonText: 'Override',
										title: 'This Evidoc already exists!',
										html:
											'<b>Evidoc Name: </b>' + ucwords( data.evidoc_type.audit_type ) + ' - ' + ucwords( data.evidoc_type.audit_frequency ) + '<br/>' +
											'<b>Category: </b>' + ucwords( data.evidoc_type.category_name ) + '<br/>' +
											'<b>Description: </b><br/>' +
											'<em>' + data.evidoc_type.audit_type_desc + '</em>' + '<br/><br/>' +
											'Click <a href="'+existUrl+'" target="_blank">here</a> to view it or Cancel to go back and change the group, category or frequency'
									}).then( function (result) {
										if ( result.value ) {
											//Do this if user accepts to Override
											$( '[name="audit_type_id"]' ).val( data.evidoc_type.audit_type_id );
											$( '[name="override_existing"]' ).val( 1 );
											$( '[name="audit_type_desc"]' ).val( data.evidoc_type.audit_type_desc );								
											$( '[name="audit_frequency"]' ).val( data.evidoc_type.audit_frequency );
											
											//Do something here
											submitEvitdocTypeForm();											
										}else{
											//Do this if user cancels to change the name
										}
									})
									
								} else {
									swal({
										type: 'success',
										title: data.status_msg,
										showConfirmButton: false,
										timer: 3000
									})
									window.setTimeout(function(){ 
										location.href = "<?php echo base_url('webapp/audit/evidoc_names/'); ?>"+newEvidocTypeId;
									} ,3000);
								}	
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}		
						}
					});
				}else{
					$( ".asset_creation_panel4" ).hide( "slide", { direction : 'left' }, 500 );
					go_back( ".asset_creation_panel2" );
					return false;
				}
			}).catch( swal.noop )
		}
		
		//Quick add a New Asset Type
		$( '#asset-type-quick-add' ).click(function(){
			$( '.add-asset-type-modal' ).modal( 'show' );
		});
			
			
		$( '#add-asset-type-btn' ).click(function(){

			var formData = $( "#add-asset-type-form :input").serialize();
		
			$.ajax({
				url:"<?php echo base_url('webapp/audit/add_asset_type/' ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){
						
						$(".add-asset-type-modal").modal("hide");
						
						var assetTypeId     	= data.asset_type.asset_type_id;
						var assetTypeName   	= data.asset_type.asset_type;
						var assetCategoryId 	= data.asset_type.category_id;
						var assetCategoryName 	= data.asset_type.category_name;

						$('#feedback_message').html( data.status_msg ).delay(3000).fadeToggle("slow");
						
						var optionExists = ( $('#asset_type_id option[value=' + assetTypeId + ']').length > 0 );

						if( !optionExists ){
							//Only add the new option if it doesn't already exist
							$('#asset_type_id').append( $('<option >').val( assetTypeId ).text( assetTypeName ) );
						}
						
						//Set selected
						$('#asset_type_id option[value="'+assetTypeId+'"]').prop( 'selected', true );
						
						//Set the Category if it already exists
						var categoryExists = ( $('#evidoc_category_id option[value=' + assetCategoryId + ']').length > 0 );

						if( !categoryExists ){
							$('#evidoc_category_id').append( $('<option >').val( assetCategoryId ).text( assetCategoryName ) );
						}
						
						//Set selected
						$('#evidoc_category_id option[value="'+assetCategoryId+'"]').prop( 'selected', true );
						
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
		
		//Trigger Category modal
		$( '.evidoc-category-quick-add' ).click( function(){
			$( '.add-category-modal' ).modal( 'show' );
		} );
		
		// New Category Quick add
		$( '#category-quick-add-btn' ).click(function(){

			var formData = $( "#add-category-form-container :input").serialize();
		
			$.ajax({
				url:"<?php echo base_url('webapp/audit/add_category/' ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){
						
						$(".add-category-modal").modal("hide");
						
						var categoryId 	 = data.category.category_id;
						var categoryName = data.category.category_name;
						var categoryDesc = data.category.description;
						
						$('#category-feedback-message').html( data.status_msg ).delay( 3000 ).fadeToggle( "slow" );
						
						var optionExists  = ( $('#evidoc_category_id option[value="' + categoryId + '"]').length > 0 );
						var optionExists2 = ( $('#category_id option[value="' + categoryId + '"]').length > 0 );

						if( !optionExists ){
							//Only add the new option if it doesn't already exist
							$('#evidoc_category_id').append( $('<option >').val( categoryId ).text( categoryName + ' - ' + categoryDesc ) );
						}
						
						if( !optionExists2 ){
							//Only add the new option if it doesn't already exist
							$('#category_id').append( $('<option >').val( categoryId ).text( categoryName + ' - ' + categoryDesc ) );
						}
						
						//Set selected
						$('#evidoc_category_id option[value="'+categoryId+'"]').prop( 'selected', true );
						$('#category_id option[value="'+categoryId+'"]').prop( 'selected', true );
						
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
	});
</script>