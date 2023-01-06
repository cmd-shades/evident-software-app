<style>
	.swal2-container {
	  z-index: 10000;
	}

	table tr th,table tr td{
		padding: 5px 0; 
	}
	
	.accordion .panel:hover {
		background: transparent;
	}
	
	.show_toggled{
		display: none;
	}
	
	@media (max-width: 480px) {
		.btn-info{
			margin-bottom:10px;
		}
	}
	
	.move {cursor: move;}
</style>

<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<?php if( !empty( $evidoc_type_details ) ) { ?>
			<div class="profile-details-container">
				<div class="row alert alert-ssid bg-blue" role="alert">
					<div class="profile-overview">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<legend>EviDoc Type ID (<?php echo $evidoc_type_details->audit_type_id; ?>) <span class="pull-right"><span class="edit-evidoc-name pointer" title="Click to edit thie Evidoc Name profile"><i class="fas fa-pencil-alt"></i></span> &nbsp; <span class="delete-evidoc-type pointer" title="Click to delete this Evidoc Name profile" ><i class="far fa-trash-alt"></i></span></span></legend>
						</div>
						<div class="col-md-5 col-sm-5 col-xs-12">
							<table style="width:100%;">
								<tr>
									<td width="25%"><i class="hide fa fa-at text-bold"></i> <label>EviDoc Type</label></td>
									<td width="75%"><?php echo ucwords( $evidoc_type_details->audit_type ); ?> - <?php echo ucwords( $evidoc_type_details->audit_frequency ); ?></td>
								</tr>
								<tr>
									<td width="25%"><i class="hide fa fa-at text-bold"></i> <label>EviDoc Group</label></td>
									<td width="75%"><?php echo ucwords( $evidoc_type_details->audit_group ); ?> <?php echo ( !empty( $asset_sub_category ) ) ? '<small>('.$asset_sub_category.')</small>' : ''; ?></td>
								</tr>
								<tr>
									<td width="25%"><i class="hide fa fa-at text-bold"></i> <label>EviDoc Category</label></td>
									<td width="75%"><?php echo ucwords( $evidoc_type_details->category_name ); ?> &nbsp;&nbsp;<?php echo !empty( $evidoc_type_details->job_contract_name ) ? '|&nbsp;&nbsp; <a href="'.base_url( "webapp/contract/profile/" ).$evidoc_type_details->job_type_contract_id.'">'.$evidoc_type_details->job_contract_name.'</a>' : ( !empty( $evidoc_type_details->evidoc_contract_name ) ? '<a href="'.base_url( "webapp/contract/profile/" ).$evidoc_type_details->contract_id.'">'.$evidoc_type_details->evidoc_contract_name.'</a>' : '' ); ?></td>
								</tr>
								<tr>
									<td width="25%"><i class="hide fa fa-at text-bold"></i> <label>Description</label></td>
									<td width="75%"><?php echo ucwords( $evidoc_type_details->audit_type_desc ); ?></td>
								</tr>
							</table>
						</div>
						<div class="col-md-5 col-sm-5 col-xs-12">
							<table style="width:100%;">
								<tr>
									<td width="25%"><label>Status</label></td>
									<td width="75%"><?php echo ( $evidoc_type_details->is_active == 1 ) ? 'Active <i class="far fa-check-circle"></i>' : 'Disabled <i class="far fa-times-circle text-red"></i>'; ?></td>
								</tr>
								<tr>
									<td width="25%"><label>Schedule Frequency</label></td>
									<td width="75%"><?php echo !empty( $evidoc_type_details->frequency_name ) ? $evidoc_type_details->frequency_name : ''; ?></td>
								</tr>												
								<tr>
									<td width="25%"><label>Date Created</label></td>
									<td width="75%"><?php echo ( !empty( $evidoc_type_details->record_created_by ) ) ? ucwords( $evidoc_type_details->record_created_by ) : 'Data not available'; ?> <?php echo ( valid_date( $evidoc_type_details->date_created ) ) ? '@ '.date( 'd-m-Y H:i:s', strtotime( $evidoc_type_details->date_created ) ) : ''; ?></td>
								</tr>
								<tr>
									<td width="25%"><label>Date Last Modified</label></td>
									<td width="75%"><?php echo ( !empty( $evidoc_type_details->record_modified_by ) ) ? ucwords( $evidoc_type_details->record_modified_by ) : 'No updates yet'; ?> <?php echo ( valid_date( $evidoc_type_details->last_modified ) ) ? '@ '.date( 'd-m-Y H:i:s', strtotime( $evidoc_type_details->last_modified ) ) : ''; ?></td>
								</tr>
							</table>							
						</div>
						<div class="col-md-2 col-sm-2 col-xs-12 pull-right">
							<table style="width:100%;">
								<tr>
									<td width="25%"><i class="hide fa fa-at text-bold"></i> <label><!-- Discipline --></label></td>
									<td width="75%" class="center text-center"><?php echo !empty( $evidoc_type_details->account_discipline_name ) ? $evidoc_type_details->account_discipline_name : ''; ?></td>
								</tr>
								<tr>
									<td width="25%">&nbsp;</td>
									<td width="75%" class="center text-center"><img width="60px;" src="<?php echo !empty( $evidoc_type_details->discipline_image_url ) ? $evidoc_type_details->discipline_image_url : ''; ?>" /></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			
			<div class="row">
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						
						<div class="x_panel tile has-shadow">
							<legend>Question Bank <span class="pull-right pointer add-new-question"><i class="fas fa-plus" title="Add new Question to this EviDoc type" ></i></span></legend>
							<div class="accordion" id="accordion" role="tablist" aria-multiselectable="true" >
								<?php if( !empty( $evidoc_questions )){ $counter = 1; ?>
									<?php foreach( $evidoc_questions as $section => $section_questions ){ $counter++; ?>
										<div class="panel">
											<div class="section-container-bar panel-heading collapsed bg-grey pointer no-radius" role="tab" id="heading<?php echo number_to_words( $counter ); ?>" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo number_to_words( $counter ); ?>" aria-expanded="true" aria-controls="collapse<?php echo number_to_words( $counter ); ?>">
												<h4 class="panel-title"><i class="caret-icon fas fa-caret-down text-yellow"></i> <?php echo ucwords( html_entity_decode( $section ) ); ?> (<?php echo ( is_object( $section_questions ) ) ? count( object_to_array( $section_questions ) ) : count( $section_questions ) ; ?>) <small class="reorder-feedback-msg pull-right" id="sec-<?php echo strtolower( lean_string( $section ) ); ?>"></small></h4>
											</div>
											<div id="collapse<?php echo number_to_words( $counter ); ?>" class="panel-collapse collapse no-bg no-background <?php echo ( !empty( $toggled_section ) && ( strtolower( lean_string( $toggled_section ) ) == strtolower( lean_string( $section ) ) ) ) ? 'show-toggled' : ''?>" role="tabpanel" aria-labelledby="heading<?php echo number_to_words( $counter ); ?>" >
												<div class="panel-body">
													<div class="table-responsive">
														<div class="col-md-12 col-sm-12 col-xs-12">
															<table class="table">
																<thead>
																	<tr>
																		<th width="35%">Question / Label Name</th>
																		<th width="15%">Response Type</th>
																		<th width="15%">Response Options</th>
																		<th width="10%" class="text-center" >Status</th>
																		<th width="15%" class="text-center" >Ordering</th>
																		<th width="10%"><span class="pull-right">Action</span></th>
																	</tr>
																</thead>
																<tbody id="sortable-<?php echo strtolower( lean_string( $section ) ); ?>" class="sortabel-items" >
																	<?php foreach( $section_questions as $k => $question ){ ?>
																		<tr class="move sort-item" data-question_id="<?php echo $question->question_id; ?>" data-account_id="<?php echo $question->account_id; ?>"  data-audit_type_id="<?php echo $question->audit_type_id; ?>" data-section_name="<?php echo $section; ?>" data-section_ref="<?php echo strtolower( lean_string( $section ) ); ?>" >
																			<td width="35%"><?php echo $question->question; ?></td>
																			<td width="15%"><span title="<?php echo $question->response_type; ?>" ><?php echo $question->response_type; ?></span></td>
																			<td width="15%"><span title="" ><?php echo ( is_array( $question->response_options ) ) ? implode( " | ", $question->response_options ) : ( is_object( $question->response_options ) ? json_encode( $question->response_options ) : $question->response_options ) ; ?></span></td>
																			<td width="10%" class="text-center" ><?php echo ( $question->is_active == 1 ) ? '<i class="far fa-check-circle text-green" title="This Question is active" ></i>' : '<i class="far fa-times-circle text-red" title="This Question is currently disabled" ></i>'; ?></td></td>
																			<td width="15%" class="text-center" ><span title="<?php echo $question->ordering; ?>" ><?php echo $question->ordering; ?></span></td>
																			<td width="10%"><span class="pull-right"><span class="edit-evidoc-question pointer" data-question_id="<?php echo $question->question_id; ?>" data-section_name="<?php echo $section; ?>" title="Click to Edit this record"><i class="far fa-edit"></i> Edit</span> &nbsp; &nbsp; <span class="delete-evidoc-question pointer text-red" data-question_id="<?php echo $question->question_id; ?>" data-section_name="<?php echo $section; ?>" title="Click to Delete this record" ><i class="far fa-trash-alt"></i> Delete</span></span></td>
																		</tr>
																	<?php } ?>
																</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>
										</div>
										<br/>
									<?php } ?>
								<?php } else{ ?>
									<div class="no-results">
										<div><?php echo $this->config->item('no_records'); ?></span> &nbsp;Click the button to add a new Question <span><button title="Add new Question to this EviDoc type" style="width:8%" class="add-new-question btn btn-sm btn-success success-shadow"><i class="fas fa-plus-circle" style="font-size: 18px;"></i></button></span></div>							
									</div>
								<?php } ?>	
							</div>
							
						</div>
					</div>
				</div>
			</div>
			
			<!-- Modal for adding a new Section -->
			<div class="modal fade profile-add-section-modal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index:9999" >
				<div class="modal-dialog modal-md">
					<div class="modal-content">
						<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title" id="myModalLabel">Section <small><em>Quick Add</em></small></h4>
						</div>
						<div id="profile-add-section-form-container" class="modal-body">
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
									<button id="profile-quick-add-section-btn" class="btn btn-block btn-success" type="button" >Add Section</button>					
								</div>
							</div>										
						</div>
					</div>
				</div>
			</div>
			
			<!-- Modal for VIewing and Editing an existing Evidoc Name -->
			<div id="edit-evidoc-name-modal" class="modal fade edit-evidoc-name-modal" tabindex="-1" role="dialog" aria-hidden="true">
				<form id="edit-evidoc-name-form" >
					<div class="modal-dialog modal-lg" style="width:70%">
					
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
								<h4 class="modal-title" id="myModalLabel">Edit EviDoc Type (<?php echo $evidoc_type_details->audit_type_id; ?>)</h4>
								<small id="feedback-message"></small>
							</div>

							<div class="modal-body">
								
								<div class="row">
									<div class="col-md-6 col-sm-6 col-xs-12">
										<legend>EviDoc Details</legend>
										<div class="input-group form-group">
											<label class="input-group-addon">EviDoc Type</label>
											<input id="evidoc_name" name="audit_type" class="form-control" type="text" placeholder="EviDoc name" value="<?php echo $evidoc_type_details->audit_type; ?>" />
										</div>
										<div class="input-group form-group">
											<label class="input-group-addon">Evidoc Group</label>
											<select id="evidoc_group" name="audit_group" class="form-control">
												<option value="">Please select group</option>
												<?php if( !empty( $evidoc_groups ) ) { foreach( $evidoc_groups as $k => $group ) { ?>
													<option value="<?php echo $group->evidoc_group_name; ?>" <?php echo ( strtolower( $group->evidoc_group_name ) == strtolower( $evidoc_type_details->audit_group ) ) ? 'selected=selected' : ''; ?> ><?php echo $group->evidoc_group_name; ?></option>
												<?php } } ?>
											</select>
										</div>
										<div class="input-group form-group">
											<label class="input-group-addon">Discipline</label>
											<select id="discipline_id" name="discipline_id" class="form-control required" data-label_text="Discipline" >
												<option value="" >Please Select</option>
												<?php if( !empty( $disciplines ) ) { foreach( $disciplines as $disp_key => $discipline ) { ?>
													<option value="<?php echo $discipline->discipline_id; ?>" <?php echo ( $evidoc_type_details->discipline_id == $discipline->discipline_id ) ? "selected=selected" : "" ?> ><?php echo $discipline->account_discipline_name; ?></option>
												<?php } } ?>
											</select>
										</div>
										<div class="input-group form-group">
											<label class="input-group-addon">Evidoc Category</label>
											<select id="category_id" name="category_id" class="form-control">
												<option value="">Please select group</option>
												<?php if( !empty( $evidoc_categories ) ) { foreach( $evidoc_categories as $k => $category ) { ?>
													<option value="<?php echo $category->category_id; ?>" <?php echo ( strtolower( $category->category_id ) == strtolower( $evidoc_type_details->category_id ) ) ? 'selected=selected' : ''; ?> ><?php echo $category->category_name; ?></option>
												<?php } } ?>
											</select>
										</div>
										<div class="input-group form-group asset-types" style="display:none" >
											<label class="input-group-addon">Asset Type</label>
											<select id="asset_type_id" name="asset_type_id" class="form-control required" data-label_text="EviDocs Asset type" >
												<option value="" >Please Select a type</option>
													<?php if( !empty( $asset_types ) ) { foreach( $asset_types as $category => $asset_categories ) { ?>
														<optgroup label="<?php echo ucwords( $category ); ?>">
															<?php foreach( $asset_categories as $k => $asset_type ) { ?>
																<option value="<?php echo $asset_type->asset_type_id; ?>" <?php echo ( $asset_type->asset_type_id == $evidoc_type_details->asset_type_id ) ? 'selected=selected' : ''; ?> data-category_id="<?php echo $asset_type->category_id; ?>" ><?php echo $asset_type->asset_type; ?></option>
															<?php } ?>
														</optgroup>
													<?php } } ?>
											</select>
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Evidoc Frequency</label>
											<select id="audit_frequency" name="audit_frequency" class="form-control required" data-label_text="EviDocs Frequency" >
												<option value="" >Please Select a frequency</option>
												<?php if( !empty( $evidoc_frequencies ) ) { foreach( $evidoc_frequencies as $k => $frequency ) { ?>
													<option value="<?php echo $frequency->frequency; ?>" <?php echo ( strtolower( $frequency->frequency ) == strtolower( $evidoc_type_details->audit_frequency ) ) ? 'selected=selected' : ''; ?> ><?php echo $frequency->frequency_alt; ?></option>
												<?php } } ?>
											</select>
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Schedule Frequency</label>
											<select id="frequency_id" name="frequency_id" class="form-control required" data-label_text="Schedule Frequency" >
												<option value="" >Please Select schedule frequency</option>
												<?php if( !empty( $schedule_frequencies ) ) { foreach( $schedule_frequencies as $k => $shed_frequency ) { ?>
													<option value="<?php echo $shed_frequency->frequency_id; ?>" <?php echo ( strtolower( $shed_frequency->frequency_id ) == strtolower( $evidoc_type_details->frequency_id ) ) ? 'selected=selected' : ''; ?> ><?php echo $shed_frequency->frequency_name; ?></option>
												<?php } } ?>
											</select>
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Outcome Required?</label>
											<select id="outcome_required" name="outcome_required" class="form-control">
												<option value="0">Please select group</option>
												<option value="1" <?php echo ( $evidoc_type_details->outcome_required == 1 ) ? 'selected=selected' : ''; ?> >Yes</option>
												<option value="0" <?php echo ( empty( $evidoc_type_details->outcome_required ) || ( $evidoc_type_details->outcome_required == 0 ) ) ? 'selected=selected' : ''; ?> >No</option>
											</select>
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Approval Required?</label>
											<select id="approval_required" name="approval_required" class="form-control">
												<option value="0">Please select group</option>
												<option value="1" <?php echo ( $evidoc_type_details->approval_required == 1 ) ? 'selected=selected' : ''; ?> >Yes</option>
												<option value="0" <?php echo ( empty( $evidoc_type_details->approval_required ) || ( $evidoc_type_details->outcome_required == 0 ) ) ? 'selected=selected' : ''; ?> >No</option>
											</select>
										</div>
										
										<?php /*
										<div class="input-group form-group">
											<label class="input-group-addon">Evidoc Group</label>
											<select id="evidoc_group" name="audit_group" class="form-control">
												<option value="">Please select group</option>
												<?php if( !empty( $evidoc_groups ) ) { foreach( $evidoc_groups as $k => $group ) { ?>
													<option value="<?php echo $group->evidoc_group_name; ?>" <?php echo ( strtolower( $group->evidoc_group_name ) == strtolower( $evidoc_type_details->audit_group ) ) ? 'selected=selected' : ''; ?> ><?php echo $group->evidoc_group_name; ?></option>
												<?php } } ?>
											</select>
										</div>
										*/ ?>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Linked Contract</label>
											<select id="contract_id" name="contract_id" class="form-control" style="width:100%; display:none; margin-bottom:10px;" data-label_text="Linked contract"  >	
												<option value="" >Select linked contract</option>
												<?php if( !empty( $available_contracts ) ) { foreach( $available_contracts as $k => $contract ) { ?>
													<option value="<?php echo $contract->contract_id; ?>" <?php echo ( $evidoc_type_details->contract_id == $contract->contract_id ) ? 'selected=selected' : ''; ?> ><?php echo $contract->contract_name; ?></option>
												<?php } } ?>
											</select>
										</div>
										<input type="hidden" name="job_type_id" value="<?php echo $evidoc_type_details->job_type_id; ?>" />
										
										<?php /* ?><div class="input-group form-group">
											<label class="input-group-addon">Status</label>
											<select id="is_active" name="is_active" class="form-control">
												<option value="">Please select group</option>
												<option value="1" <?php echo ( $evidoc_type_details->outcome_required == 1 ) ? 'selected=selected' : ''; ?> >Yes</option>
												<option value="1" <?php echo ( empty( $evidoc_type_details->outcome_required ) || ( $evidoc_type_details->outcome_required == 0 ) ) ? 'selected=selected' : ''; ?> >No</option>
											</select>
										</div>
										<?php */ ?>
										<div class="input-group form-group">
											<label class="input-group-addon">Evidoc Description</label>
											<textarea name="audit_type_desc" class="form-control" type="text" value="" style="width:100%; height:78px" ><?php echo $evidoc_type_details->audit_type_desc; ?></textarea>
										</div>

									</div>
									
									<div class="col-md-6 col-sm-6 col-xs-12">
										<legend>Read-only Information on the Evidoc</legend>
										<div class="input-group form-group">
											<label class="input-group-addon">Introduction Header</label>
											<input id="info_intro_header" name="info_intro_header" class="form-control" type="text" placeholder="Info intro Header" value="<?php echo $evidoc_type_details->info_intro_header; ?>" />
										</div>
									
										<div class="input-group form-group">
											<label class="input-group-addon">Introduction Text</label>
											<textarea id="info_intro_text" name="info_intro_text" class="form-control" style="height:82px;" type="text" placeholder="Info intro Text" value="" ><?php echo $evidoc_type_details->info_intro_text; ?></textarea>
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Body Header</label>
											<input id="info_main_header" name="info_main_header" class="form-control" type="text" placeholder="Body Header" value="<?php echo $evidoc_type_details->info_main_header; ?>" />
										</div>
									
										<div class="input-group form-group">
											<label class="input-group-addon">Body Text</label>
											<textarea id="info_main_text" name="info_main_text" class="form-control" style="height:132px;" type="text" placeholder="Body Text" value="" ><?php echo $evidoc_type_details->info_main_text; ?></textarea>
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Summary Header</label>
											<input id="info_summary_header" name="info_summary_header" class="form-control" type="text" placeholder="Summary Header" value="<?php echo $evidoc_type_details->info_summary_header; ?>" />
										</div>
									
										<div class="input-group form-group">
											<label class="input-group-addon">Summary Text</label>
											<textarea id="info_summary_text" name="info_summary_text" class="form-control"  style="height:82px;" type="text" placeholder="Summary Text" value="" ><?php echo $evidoc_type_details->info_summary_text; ?></textarea>
										</div>
									</div>
								</div>								
							</div>
							
							<div class="modal-footer">
								<button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">&nbsp;&nbsp;&nbsp;&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;</button>
								<button id="update-evidoc-name-btn" type="button" class="btn btn-sm btn-success">Save Changes</button>
							</div>
						</div>
					</div>
				</form>
			</div>
			
			<!-- Modal for adding a new Question. Should this be a modal? -->
			<div class="modal fade add-question-modal" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title" id="myAddQuestModalLabel">Add a new EviDoc Question</h4>						
						</div>
						<?php include( 'evidoc_question_add_new.php' ); ?>
					</div>
				</div>
			</div>
			
			<!-- Modal for Editing a Question -->
			<div class="modal fade edit-evidoc-question-modal" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<form id="edit-evidoc-question-form" >
							<input type="hidden" name="question_id" value="" />
							<input type="hidden" name="page" value="details" />
							<input type="hidden" name="audit_type_id" value="<?php echo $evidoc_type_details->audit_type_id; ?>" />
					
							<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
								<h4 class="modal-title" id="myEditQuestModalLabel">Edit EviDoc Question ID: <span id="question_id">Data loading....</span></h4>
								<small id="question-feedback-message"></small>								
							</div>

							<div class="modal-body evidoc-question-body">
								
							</div>

							<div class="modal-footer">
								<button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">&nbsp;&nbsp;&nbsp;&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;</button>
								<button id="edit-evidoc-question-btn" type="button" class="btn btn-sm btn-success">Save Changes</button>
							</div>
						</form>
					</div>
				</div>
			</div>

			<?php }else{ ?>
				<div class="row">
					<div><?php echo $this->config->item('no_records'); ?> </div>
					<div><span class="pull-right pointer"><i class="fas fa-plus" title="Add new Question to this EviDoc type" ></i></span></div>
				</div>
			<?php } ?>	
		</div>
	</div>
</div>


<script>
	$( document ).ready( function(){

		function updatedQuestionOrdering( evidocTypeId = false, questionData = false, sectionName = false, sectionRef = false ){
			if( ( evidocTypeId > 0 ) && ( questionData.length > 0 ) && ( sectionName.length > 0 ) && ( sectionRef.length > 0 ) ){
				$.ajax({
					url:"<?php echo base_url( 'webapp/audit/reorder_questions' ); ?>",
					method: "POST",
					data:{ page:'details', audit_type_id:evidocTypeId, questions_data:questionData },
					dataType: 'json',
					success:function( data ){
						
						$( '#sec-'+sectionRef ).html( data.status_msg );								
						setTimeout(function(){
							$( '#sec-'+sectionRef ).fadeOut( 'slow' );
							var new_url = window.location.href.split('?')[0];
							window.location.href = new_url + "?toggled=" + sectionName;
						},1500 );
					}
				});
			}
			return false;
		}

		//$( "#sortable" ).sortable({
		$( ".sortabel-items" ).sortable({
			stop: function(event, ui) {
				var newItemsOrder  	= [];
				var sectionName 	= '';
				var sectionRef 		= '';
				var evidocTypeId 	= 0;
				$.map( $( this ).find( 'tr' ), function( item ) {
					var newPos 			= parseInt( $( item ).index() ) + 1;
					sectionName 		= $( item ).data( 'section_name' );
					sectionRef 			= $( item ).data( 'section_ref' );
					evidocTypeId 		= $( item ).data( 'audit_type_id' );
					newItemsOrder.push(
						{ 
							'account_id'	: $( item ).data( 'account_id' ), 
							'audit_type_id'	: $( item ).data( 'audit_type_id' ),
							'question_id'	: $( item ).data( 'question_id' ),
							'ordering'		: newPos
						}
					);
					
					/* newItemsOrder.push( [ 
						'account_id' 	+ ' => '+ $( item ).data( 'account_id' ), 
						'audit_type_id' + ' => '+ $( item ).data( 'audit_type_id' ), 
						'question_id' 	+ ' => '+ $( item ).data( 'question_id' ), 
						'ordering' 	  	+ ' => '+ newPos
					] ); */

                });
				
				if( newItemsOrder.length > 0 ){
					var setNewOrdering = updatedQuestionOrdering( evidocTypeId, newItemsOrder, sectionName, sectionRef );
					if( setNewOrdering ){
						//Reload the page
						var new_url = window.location.href.split('?')[0];
						window.location.href = new_url + "?toggled=" + sectionName;
					}
				}
			}
		});
		
		$( ".sortabel-items" ).disableSelection();
		//$( "#sortable" ).disableSelection();

		///////////////////////////
		
		var evidocGrp = $( '#evidoc_group option:selected' ).val().toLowerCase();
		if( evidocGrp == 'asset' ){
			$( '.asset-types' ).show();
		}
		
		$( '#contract_id' ).select2({
			dropdownParent: $( "#edit-evidoc-name-modal" )
		});
		
		/* var currentContractId = $( "#contract_id option:selected" ).val();
		$( '#contract_id' ).on('change', function() {
			var contractId = $( "#contract_id option:selected" ).val();
			swal({
				title: 'Job Types Affected!',
				text: 'This change requires that you update the Job Type field accordingly!',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Proceed'
			}).then( function (result) {
				if ( result.value ) {
					$( '#job_type_id' ).css( "border","1px solid red" );
					$.ajax({
						url:"<?php echo base_url( 'webapp/audit/load_job_types/' ); ?>"+contractId,
						method: "POST",
						data:{ page:'details', contract_id:contractId },
						dataType: 'json',
						success:function( data ){
							$( "select[name='job_type_id']" ).empty();
							$( "select[name='job_type_id']" ).append( data.job_types );
						}
					});
				} else {
					console.log( 'Previous Value'+currentContractId );
				}
			}).catch( swal.noop )
			
		}) */
		
		$( '#job_type_id' ).click( function(){
			$( this ).css( "border","1px solid #ccc" );
		});
		
		//Check for Audit group
		$( '#evidoc_group' ).change( function(){
			
			$( this ).css("border","1px solid #ccc");
			$( '#feedback-message' ).show().text( '' );	
			
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
		
		//Preset Category from Asset type
		$( '#asset_type_id' ).change( function(){
			
			$( this ).css("border","1px solid #ccc");
			$( '#feedback-message' ).show().text( '' );
			
			var evidocGrp = $( '#evidoc_group option:selected' ).val().toLowerCase();
			var assetType = $( 'option:selected', this ).val();
			var categoryId= $( 'option:selected', this ).data( 'category_id' );
			if( assetType.length > 0 ){
				$('#category_id option[value="'+categoryId+'"]').prop( 'selected', true );
			}else{
				if( evidocGrp == 'asset' ){
					swal({
						type: 'warning',
						title: 'Asset Type is required for this EviDoc Group'
					})
				}
			}
		} );
		
		$( '.evidoc-question-body' ).on( '#response_type, change', function(){
			
			var respType 	= $( 'option:selected', this ).data( 'resp_type' );
			var respTypeAlt = $( 'option:selected', this ).data( 'resp_type_alt' );
			var respDesc 	= $( 'option:selected', this ).data( 'resp_desc' );

			$( '.resp-extra-options' ).hide();
			$( '#selected-option' ).text( '' );

			$( '.resp-type-options' ).hide();
			$( '.resp-type-options' ).hide();
			$( '.resp_' + respType ).show();
		});

		
		//Section Quick Add
		//$( '#evidoc-section-quick-add' ).click(function(){
		$( '.evidoc-question-body' ).on( 'click', '#evidoc-section-quick-add', function(){
			$(".profile-add-section-modal").modal( "show" );
		});
		
		// Section Quick add
		$( '#profile-quick-add-section-btn' ).click(function(){
		
			var formData = $( "#profile-add-section-form-container :input").serialize();
		
			$.ajax({
				url:"<?php echo base_url('webapp/audit/add_new_section/' ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){
						
						$(".profile-add-section-modal").modal("hide");
						
						var sectionId 	 = data.section.section_id;
						var sectionName  = data.section.section_name;
						
						$('#feedback_message').html( data.status_msg ).delay( 3000 ).fadeToggle( "slow" );
						
						var optionExists = ( $('#modal_section_id option[value="' + sectionName + '"]').length > 0 );

						if( !optionExists ){
							//Only add the new option if it doesn't already exist
							$('#modal_section_id').append( $('<option >').val( sectionName ).text( sectionName ) );
						}
						
						//Set selected
						$('#modal_section_id option[value="'+sectionName+'"]').prop( 'selected', true );
						
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
		
		
		$('#edit-evidoc-name-modal').on('hidden.bs.modal', function () { 
			location.reload();
		});
		
		$( '.section-container-bar' ).click( function(){
			$( this ).closest( 'div' ).find( '.caret-icon' ).toggleClass('fa-caret-up fa-caret-down');			
		});
		
		$( '.edit-evidoc-name' ).click( function(){
			$(".edit-evidoc-name-modal").modal("show");
		} );
		
		$( '.add-new-question' ).click( function(){
			$(".add-question-modal").modal("show");
		} );
		
		//View and Edit an Evidoc question
		$( '.edit-evidoc-question' ).click( function(){
			
			var questionId  = $( this ).data( 'question_id' );
			var auditTypeId = "<?php echo $evidoc_type_details->audit_type_id; ?>";

			if( questionId.length != 0 ){
				
				$( '[name="question_id"]' ).val( questionId );
				$( '#question_id' ).text( questionId );
				
				$.ajax({
					url:"<?php echo base_url('webapp/audit/view_question_data/' ); ?>" + questionId,
					method:"post",
					data:{ question_id:questionId, audit_type_id:auditTypeId },
					dataType: 'json',
					success:function( data ){
						if( data.status == 1 ){
							$( '.evidoc-question-body' ).html( data.question_data );
							$( '.edit-evidoc-question-modal' ).modal( 'show' );						
						}else{
							swal({
								type: 'error',
								title: data.status_msg
							})
						}		
					}
				});
			}else{
				swal({
					type: 'error',
					title: 'Oops! Something went wrong, please refresh this page.'
				})
			}

		} );
		
		//Submit Edit Question form
		$( '#edit-evidoc-question-btn' ).click( function() {
			$( '#question-feedback-message' ).show();
			$( '#question-feedback-message' ).text( '' );

			event.preventDefault();
			
			var questionId 	 = $( '.evidoc-question-body [name="question_id"] ' ).val();
			var questionName = $( '.evidoc-question-body [name="question"] ' ).val();
			var sectionName  = $( '.evidoc-question-body #section_name option:selected' ).val();
			
			if( questionName.length == 0 ){
				$( '[name="question"]' ).focus().css("border","1px solid red");
				$( '#question-feedback-message' ).html( '<span class="text-red">Question label is required</span>' );
				return false;
			}
			
			var formData = $(this).closest('form').serialize();
			
			swal({
				title: 'Confirm update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/audit/update_question/'.$evidoc_type_details->audit_type_id ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							$( '#question-feedback-message' ).html( data.status_msg );								
							setTimeout(function(){
								$( '#question-feedback-message' ).fadeOut( 'slow' );
								$('.edit-evidoc-question-modal').modal( 'hide' );
								var new_url = window.location.href.split('?')[0];
								window.location.href = new_url + "?toggled=" + sectionName;
							},3000 );
						}
					});
				}
			}).catch( swal.noop )
			
		});
		
		//Delete or archive evidoc question
		$( '.delete-evidoc-question' ).click( function(){
			
			var questionId  	= $( this ).data( 'question_id' );
			var sectionName  	= $( this ).data( 'section_name' );
			
			if( questionId == 0 || questionId == undefined ){
				swal({
					title: 'Oops! Something went wrong',
					type: 'error',
					text: 'Please reload the page and try again!',
				})
			}

			swal({
				title: 'Confirm delete Question?',
				type: 'question',
				text: 'This is an irreversible action',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/audit/delete_question/' ); ?>" + questionId,
						method:"POST",
						data:{ page:"details", question_id:questionId },
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2500
								})
								window.setTimeout(function(){
									var new_url = window.location.href.split('?')[0];
									window.location.href = new_url + "?toggled=" + sectionName;
								} ,1000);
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}
						}
					});
				}
			}).catch( swal.noop )
		} );
	
		//Remove red border when user starts to type again
		$( '[name="audit_type"]' ).keyup( function(){
			$( this ).css("border","1px solid #ccc");
			$( '#feedback-message' ).show().text( '' );			
		} );
	
		//Submit form for processing
		$( '#update-evidoc-name-btn' ).click( function( event ){
			
			$( '#feedback-message' ).show();
			$( '#feedback-message' ).text( '' );

			event.preventDefault();
			
			var evidocName 	= $( '#evidoc_name' ).val();
			var avidocGroup = $( '#evidoc_group option:selected').val();
			var avidocCat   = $( '#category_id option:selected' ).val();
			var assetType   = $( '#asset_type_id option:selected' ).val();
			var auditFreq   = $( '#audit_frequency option:selected' ).val();
			
			if( evidocName.length == 0 ){
				$( '[name="audit_type"]' ).focus().css("border","1px solid red");
				$( '#feedback-message' ).html( '<span class="text-red">EviDoc name is required</span>' );
				return false;
			}
			
			if( avidocGroup.length == 0 ){
				$( '[name="audit_group"]' ).focus().css("border","1px solid red");
				$( '#feedback-message' ).html( '<span class="text-red">EviDoc Group is required</span>' );
				return false;
			}
			
			if( avidocCat.length == 0 ){
				$( '[name="category_id"]' ).focus().css("border","1px solid red");
				$( '#feedback-message' ).html( '<span class="text-red">EviDoc category is required</span>' );
				return false;
			}
			
			if( ( avidocGroup.toLowerCase() == 'asset' ) && ( assetType.length == 0 ) ){
				$( '[name="asset_type_id"]' ).focus().css("border","1px solid red");
				$( '#feedback-message' ).html( '<span class="text-red">Asset Type is required for this EviDoc Group</span>' );
				return false;
			}
			
			if( auditFreq.length == 0 ){
				$( '[name="audit_frequency"]' ).focus().css("border","1px solid red");
				$( '#feedback-message' ).html( '<span class="text-red">EviDoc Frequency is required</span>' );
				return false;
			}

			var formData = $(this).closest('form').serialize();
			
			swal({
				title: 'Confirm update?',
				// type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/audit/update_evidoc_name/'.$evidoc_type_details->audit_type_id ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							$( '#feedback-message' ).html( data.status_msg );								
							setTimeout(function(){
								$( '#feedback-message' ).fadeOut( 'slow' );
							},3000 );
						}
					});
				}
			}).catch( swal.noop )

		});

		$( '.evidoc-question-body' ).on( 'change', '.extra_info_trigger', function(){
			var respType 	= $( this ).data( 'response_type' );
			var selectdOpt  = $( 'option:selected', this ).val();
			if( selectdOpt.length > 0 ){
				var newText = 'If "' + selectdOpt + '", please provide further info.';
				$( '#extra-info-selected-'+respType ).val( newText );
			}
		} );


		//Defects Response required
		/* if( defectResReq ){
			$( '.evidoc-question-body .defects_response_required_container' ).show();
		} else {
			$( '.evidoc-question-body .defects_response_required_container' ).hide();
		}
		
		$( '.evidoc-question-body' ).on( 'click', '.defects_response_required', function(){
			
			var defectResReq = $( this ).val();

			if( defectResReq == 1 ){
				$( '.defects_response_required_container' ).slideDown();
			} else {
				$( '.defects_response_required_container' ).slideUp( 'slow' );
			}
		}); */
		
		$( ".delete-evidoc-type" ).on( "click", function( e ){
			
			e.preventDefault();
			
			var evidocTypeID = "<?php echo ( !empty( $evidoc_type_details->audit_type_id ) ) ? $evidoc_type_details->audit_type_id : '' ; ?>";
			
			swal({
				title: 'Delete Evidoc Type?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function( result ){
				if( result.value ){
					$.ajax({
						url:"<?php echo base_url( 'webapp/audit/delete_evidoc_type/' ); ?>",
						method: "POST",
						data: { page:'details', audit_type_id: evidocTypeID },
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 ){
								swal( 'Success', data.status_msg, 'success' );
								setTimeout( function(){
									window.location.href = "<?php echo base_url( 'webapp/audit/evidoc_names/' ); ?>";
								}, 2000 );
							}
						}
					});
				}
			}).catch( swal.noop )
		});
		
		
	});
</script>

