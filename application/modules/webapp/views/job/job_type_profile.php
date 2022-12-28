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
</style>

<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<?php if( !empty( $job_type_details ) ) { ?>
			<div class="profile-details-container">
				<div class="row alert alert-ssid bg-blue" role="alert">
					<div class="profile-overview">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<legend>Job Type (<?php echo $job_type_details->job_type_id; ?>) <span class="pull-right"><span class="edit-job-type pointer" title="Click to edit this Job Type profile"><i class="fas fa-pencil-alt"></i></span> &nbsp; <span data-job_type_id="<?php echo $job_type_details->job_type_id; ?>" class="archive-job-type-btn pointer" title="Click to Archive this Job Type" ><i class="far fa-trash-alt"></i></span></span></legend>
						</div>
						<div class="col-md-5 col-sm-5 col-xs-12">
							<table style="width:100%;">
								<tr>
									<td width="25%"><i class="hide fa fa-at text-bold"></i> <label>Job Type</label></td>
									<td width="75%"><?php echo ucwords( $job_type_details->job_type ); ?></td>
								</tr>
								<tr>
									<td width="25%"><i class="hide fa fa-at text-bold"></i> <label>Job Group</label></td>
									<td width="75%"><?php echo ucwords( $job_type_details->job_group ); ?></td>
								</tr>

								<!-- <tr>
									<td width="25%"><i class="hide fa fa-at text-bold"></i> <label>Job Type Category</label></td>
									<td width="75%"><?php echo ucwords( $job_type_details->category_name ); ?></td>
								</tr> -->
								<tr>
									<td width="25%"><label>Status</label></td>
									<td width="75%"><?php echo ( $job_type_details->is_active == 1 ) ? 'Active <i class="far fa-check-circle"></i>' : 'Disabled <i class="far fa-times-circle text-red"></i>'; ?></td>
								</tr>
								<tr>
									<td width="25%"><i class="hide fa fa-at text-bold"></i> <label>Description</label></td>
									<td width="75%"><?php echo ucwords( $job_type_details->job_type_desc ); ?></td>
								</tr>
							</table>
						</div>
						<div class="col-md-5 col-sm-5 col-xs-12">
							<table style="width:100%;">
								<tr>
									<td width="25%"><i class="hide fa fa-at text-bold"></i> <label>Base SLA</label></td>
									<td width="75%"><?php echo ucwords( $job_type_details->base_sla ); ?></td>
								</tr>
								<tr>
									<td width="25%"><i class="hide fa fa-at text-bold"></i> <label>Base Priority Rating</label></td>
									<td width="75%"><?php echo ucwords( $job_type_details->base_priority_rating ); ?></td>
								</tr>
								<tr>
									<td width="25%"><label>Date Created</label></td>
									<td width="75%"><?php echo ( !empty( $job_type_details->record_created_by ) ) ? ucwords( $job_type_details->record_created_by ) : 'Data not available'; ?> <?php echo ( valid_date( $job_type_details->date_created ) ) ? '@ '.date( 'd-m-Y H:i:s', strtotime( $job_type_details->date_created ) ) : ''; ?></td>
								</tr>
								<tr>
									<td width="25%"><label>Date Last Modified</label></td>
									<td width="75%"><?php echo ( !empty( $job_type_details->record_modified_by ) ) ? ucwords( $job_type_details->record_modified_by ) : 'No updates yet'; ?> <?php echo ( valid_date( $job_type_details->last_modified ) ) ? '@ '.date( 'd-m-Y H:i:s', strtotime( $job_type_details->last_modified ) ) : ''; ?></td>
								</tr>
							</table>							
						</div>
						<div class="col-md-2 col-sm-2 col-xs-12">
							<table style="width:100%;">
								<tr>
									<td width="25%"><i class="hide fa fa-at text-bold"></i> <label><!-- Discipline --></label></td>
									<td width="75%" class="center text-center"><?php echo !empty( $job_type_details->account_discipline_name ) ? $job_type_details->account_discipline_name : ''; ?></td>
								</tr>
								<tr>
									<td width="25%">&nbsp;</td>
									<td width="75%" class="center text-center"><img width="60px;" src="<?php echo !empty( $job_type_details->discipline_image_url ) ? $job_type_details->discipline_image_url : ''; ?>" /></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
			
			<div class="clearfix"></div>
			
			<div class="row">
			<div class="row">
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<div class="col-md-12">
							<div class="x_panel tile has-shadow">
								<legend>Associated Risks <span class="pull-right pointer add-new-associated-risk"><i class="fas fa-plus text-green" title="Add new associated Risk to this Job type" ></i></span></legend>
								<div class="accordion" id="accordion-risks" role="tablist" aria-multiselectable="true">
									<?php if( !empty( $job_type_details->ra_required ) ){ ?>
										<div class="row">
											<?php if( !empty( $job_type_details->associated_risks ) ){ foreach( $job_type_details->associated_risks as $risk_details ){ ?>	
												<div class="col-md-6 col-sm-6 col-xs-12">
													<ul class="to_do">
														<li>
															<div class="container">
																<div class="rows">
																	<?php $risk_rating = strtolower( $risk_details->risk_rating ); ?>
																	<div class="col-md-12 col-sm-12 col-xs-12"><strong>Risk Rating | <span class="text-<?php echo ( $risk_rating == 'high' ) ? 'red' : ( $risk_rating == 'medium' ? 'orange' : 'brown' ); ?>"><?php echo $risk_details->risk_rating; ?></span></strong></div>
																	<div class="col-md-12 col-sm-12 col-xs-12"><p><a href="<?php echo base_url( 'webapp/job/risks/'.$risk_details->risk_id ); ?>" ><?php echo $risk_details->risk_text; ?></a> <span class="pull-right"><span class="remove-risk pointer" data-job_type_id="<?php echo $job_type_details->job_type_id; ?>" data-risk_id="<?php echo $risk_details->risk_id; ?>" title="Click to remove this Risk from this Job type" ><i class="far fa-trash-alt text-red"></i></span></span></p></div>
																	<div class="col-md-12 col-sm-12 col-xs-12"><small><?php echo $risk_details->risk_harm; ?></small></div>
																</div>
															</div>
														</li>
													</ul>
												</div>
											<?php } } else { ?>
												<div class="col-md-12">
													<?php echo $this->config->item( 'no_records' ); ?>
												</div>
											<?php } ?>
										</div>
									<?php } else { ?>
										<div class="row">
											<div class="col-md-12">
												<span>This Job Type is not set to expect Risks. You can change this by <a href="#" class="edit-job-type">Editing the Profile</a></span>
											</div>
										</div>
									<?php } ?>
								</div>
								
							</div>
						</div>

						<div class="col-md-12">
							<div class="x_panel tile has-shadow">
								<legend>Associated Evidoc <span class="pull-right pointer add-associated-evidoc"><i class="far fa-edit evi-blue" title="Change or remove the associated Evidoc for this Job type" ></i></span></legend>
								<div class="accordion" id="accordion-evidoc" role="tablist" aria-multiselectable="true">
									<?php if( !empty( $job_type_details->evidoc_required ) ){ ?>
										<div class="row">
											<?php if( !empty( $job_type_details->evidoc_details ) ){ ?>	
												<div class="col-md-12 col-sm-12 col-xs-12">
													<ul class="to_do">
														<li>
															<div class="container">
																<div class="col-md-12 col-sm-12 col-xs-12"><a href="<?php echo base_url( 'webapp/audit/evidoc_names/'.$job_type_details->evidoc_details->audit_type_id ); ?>" ><?php echo $job_type_details->evidoc_details->audit_type; ?></a> <span class="pull-right"><span class="remove-evidoc pointer" data-job_type_id="<?php echo $job_type_details->job_type_id; ?>" data-evidoc_type_id="<?php echo $job_type_details->evidoc_type_id; ?>" title="Click to unlink this EviDoc from this Job type" ><i class="far fa-trash-alt text-red"></i></span></span></div>
																<div class="col-md-12 col-sm-12 col-xs-12"><p><strong>Category: </strong><?php echo $job_type_details->evidoc_details->category_name; ?></p></div>
																<div class="col-md-12 col-sm-12 col-xs-12"><p><strong>Complete: &nbsp;</strong><?php echo $job_type_details->evidoc_details->audit_group; ?> - <?php echo $job_type_details->evidoc_details->audit_frequency; ?></p></div>
															</div>
														</li>
													</ul>
												</div>
											<?php } else { ?>
												<div class="col-md-12">
													<?php echo $this->config->item( 'no_records' ); ?>
												</div>
											<?php } ?>
										</div>
									<?php } else { ?>
										<div class="row">
											<div class="col-md-12">
												<span>This Job Type is not set to expect an Evidoc. You can change this by <a href="#" class="edit-job-type">Editing the Profile</a></span>
											</div>
										</div>
									<?php } ?>
								</div>
							</div>
						</div>
						
						<?php if( !empty( $job_type_details->checklists_required ) ){ ?>
							<div class="col-md-12">
								<div class="x_panel tile has-shadow">
									<legend>Required Checklists <span class="pull-right pointer add-required-checklists"><i class="fas fa-plus text-green" title="Add new Checklists to this Job type" ></i></span></legend>
									<div class="accordion" id="accordion-checklists" role="tablist" aria-multiselectable="true">
										<?php if( !empty( $job_type_details->required_checklists ) ){ ?>
											<div class="row">
												<?php if( !empty( $job_type_details->required_checklists ) ){ foreach( $job_type_details->required_checklists as $checklist_details ){ ?>	
													<div class="col-md-6 col-sm-6 col-xs-12">
														<ul class="to_do">
															<li>
																<div class="container">
																	<div class="rows">
																		<div class="col-md-12 col-sm-12 col-xs-12"><strong><?php echo $checklist_details->checklist_id; ?> <span> - <?php echo ( !empty( $checklist_details->checklist_desc ) ) ? $checklist_details->checklist_desc : ''; ?></span> <span class="pull-right"><span class="remove-checklist pointer" data-job_type_id="<?php echo $job_type_details->job_type_id; ?>" data-checklist_id="<?php echo $checklist_details->checklist_id; ?>" title="Click to remove this Checklist from this Job type" ><i class="far fa-trash-alt text-red"></i></span></span></strong></div>
																	</div>
																</div>
															</li>
														</ul>
													</div>
												<?php } } else { ?>
													<div class="col-md-12">
														<?php echo $this->config->item( 'no_records' ); ?>
													</div>
												<?php } ?>
											</div>
										<?php } else { ?>
											<div class="row">
												<div class="col-md-12">
													<span>This Job Type is not set to expect any Checklists. You can change this by <a href="#" class="edit-job-type">Editing the Profile</a></span>
												</div>
											</div>
										<?php } ?>
									</div>
									
								</div>
							</div>
						<?php } ?>
						
						<?php if( !empty( $job_type_details->nps_required ) ){ ?>
							<div class="col-md-12">
								<div class="x_panel tile has-shadow">
									<legend>NSP Questions <span class="pull-right pointer add-new-nps-question"><i class="fas fa-plus text-green" title="Add new NPS Question to this Job type" ></i></span></legend>
									<div class="accordion" id="accordion-nps" role="tablist" aria-multiselectable="true">
											
									</div>
								</div>
							</div>
						<?php } ?>
						
					</div>
					
					<div class="col-md-6 col-sm-6 col-xs-12">
						
						<div class="col-md-12">
							<div class="x_panel tile has-shadow">
								<legend>Required BOMs <span class="pull-right pointer add-new-required-bom"><i class="fas fa-plus text-green" title="Add new Required BOM items to this Job type" ></i></span></legend>
								<div class="accordion" id="accordion-boms" role="tablist" aria-multiselectable="true">
									<div class="row">
										<?php if( !empty( $job_type_details->required_boms ) ){ foreach( $job_type_details->required_boms as $item_details ){ ?>	
											<div class="col-md-12 col-sm-12 col-xs-12">
												<ul class="to_do">
													<li>
														<div class="container">
															<div class="rows">
																<div class="col-md-12 col-sm-12 col-xs-12"><p><a href="<?php echo base_url( 'webapp/job/boms/'.$item_details->item_id ); ?>" ><strong><?php echo $item_details->item_code; ?></strong> - <?php echo $item_details->item_name; ?></a> <span class="pull-right"><span class="remove-bom pointer" data-job_type_id="<?php echo $job_type_details->job_type_id; ?>" data-item_id="<?php echo $item_details->item_id; ?>" title="Click to remove this Item from this Job type" ><i class="far fa-trash-alt text-red"></i></span></span></p></div>
															</div>
														</div>
													</li>
												</ul>
											</div>
										<?php } } else { ?>
											<div class="col-md-6 col-sm-6 col-xs-12">
												<?php echo $this->config->item( 'no_records' ); ?>
											</div>
										<?php } ?>
									</div>
								</div>
								
							</div>
						</div>
					
						<div class="col-md-12">
							<div class="x_panel tile has-shadow">
								<legend>Required Skill-set <span class="pull-right pointer add-required-skills"><i class="fas fa-plus text-green" title="Add new Skill requirement to this Job type" ></i></span></legend>
								<div class="accordion" id="accordion-skills" role="tablist" aria-multiselectable="true">
									<div class="row">
										<?php if( !empty( $job_type_details->required_skills ) ){ ?>	
											<div class="col-md-12 col-sm-12 col-xs-12">
												<p>Any operative doing this Job Type <strong>MUST</strong> have at least one of the Skills listed below.</p>
											</div>
											<?php foreach( $job_type_details->required_skills as $skill ){ ?>	
											<div class="col-md-12 col-sm-12 col-xs-12">
												<ul class="to_do">
													<li>
														<div class="container">
															<div class="rows">
																<div class="col-md-12 col-sm-12 col-xs-12"><strong><?php echo $skill->skill_name; ?> <span><?php echo ( !empty( $skill->skill_level ) ) ? ' | '.$skill->skill_level : ''; ?></span> <span class="pull-right"><span class="remove-skill pointer" data-job_type_id="<?php echo $job_type_details->job_type_id; ?>" data-skill_id="<?php echo $skill->skill_id; ?>" title="Click to remove this Skill from this Job type" ><i class="far fa-trash-alt text-red"></i></span></span></strong></div>
																<div class="col-md-12 col-sm-12 col-xs-12"><small><?php echo $skill->skill_description; ?></small></div>
															</div>
														</div>
													</li>
												</ul>
											</div>
										<?php } } else { ?>
											<div class="col-md-6 col-sm-6 col-xs-12">
												<?php echo $this->config->item( 'no_records' ); ?>
											</div>
										<?php } ?>	
									</div>
								</div>
							</div>
						</div>
					
						<?php if( !empty( $job_type_details->nps_required ) ){ ?>
							<div class="row">
								<div class="x_panel tile has-shadow">
									<legend>CSAT Questions <span class="pull-right pointer add-new-csat-question"><i class="fas fa-plus text-green" title="Add new CSAT Question to this Job type" ></i></span></legend>
									<div class="accordion" id="accordion-csat" role="tablist" aria-multiselectable="true">
											
									</div>
								</div>
							</div>
						<?php } ?>
						
					</div>
				</div>
			</div>
			
			<!-- Modal for VIewing and Editing an existing Job Type -->
			<div id="edit-job-type-modal" class="modal fade edit-job-type-modal " tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-lg" style="width:70%">
					<form id="edit-job-type-form" >					
						<input type="hidden" name="page" value="details" />
						<input type="hidden" name="job_type_id" value="<?php echo $job_type_details->job_type_id; ?>" />
						<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
								<h4 class="modal-title" id="myModalLabel">Edit Job Type (<?php echo $job_type_details->job_type_id; ?>)</h4>
								<small id="feedback-message"></small>
							</div>

							<div class="modal-body">
							
								<div class="row">
									<!-- LEFT PANEL -->
									<div class="col-md-6 col-sm-6 col-xs-12">
										<div class="input-group form-group">
											<label class="input-group-addon">Job Type</label>
											<input id="job_type" name="job_type" class="form-control" type="text" placeholder="Job Type" value="<?php echo $job_type_details->job_type; ?>" />
										</div>
										<div class="input-group form-group">
											<label class="input-group-addon">Job Base Rate (&pound;)</label>
											<input name="job_base_rate" class="form-control" type="text" placeholder="Job base Rate" value="<?php echo number_format( $job_type_details->job_base_rate, 2 ); ?>" <?php echo ( $job_type_details->job_base_rate_adjustable == 1 ) ? '' : 'readonly'; ?> />
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">3rd Party Job Type Ref</label>
											<input <?php echo ( $this->user->is_admin ) ? 'name="external_job_type_ref"' : ''; ?> class="form-control" type="text" placeholder="3rd Party Job Type Ref" value="<?php echo !empty( $job_type_details->external_job_type_ref ) ? $job_type_details->external_job_type_ref : ''; ?>" data-label_text="3rd Party Job Type Ref" <?php echo ( $this->user->is_admin ) ? '' : 'readonly'; ?> />
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Base Rate Adjustable?</label>
											<select id="job_base_rate_adjustable" name="job_base_rate_adjustable" class="form-control" data-label_text="Adjustable Base Rate" >
												<option >Please select</option>
												<option value="1" <?php echo ( $job_type_details->job_base_rate_adjustable == 1 ) ? 'selected=selected' : ''; ?> >Yes</option>
												<option value="0" <?php echo ( $job_type_details->job_base_rate_adjustable != 1 ) ? 'selected=selected' : ''; ?> >No</option>
											</select>
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Job Duration (Slots)</label>
											<select name="job_base_duration" class="form-control" >
												<option>Please select</option>
												<?php if( !empty( $job_durations ) ) { foreach( $job_durations as $k => $duration ) { ?>
													<option value="<?php echo $k; ?>" <?php echo ( $k == $job_type_details->job_base_duration ) ? 'selected="selected"' : '' ?> ><?php echo $duration; ?></option>
												<?php } } ?>
											</select>
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Job Type Description</label>
											<textarea id="job_type_desc" name="job_type_desc" type="text" class="form-control" style="height:83px;"><?php echo ( !empty( $job_type_details->job_type_desc ) ) ? $job_type_details->job_type_desc : '' ?></textarea>     
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Linked Evidoc</label>
											<select id="evidoc_type_id" name="evidoc_type_id" class="form-control" style="width:100%; margin-bottom:10px; background-color:none" data-label_text="Linked Evidoc" >
												<option value="">Search / Select the linked Evidoc</option>
												<!-- <option value="">-- Remove ( Disassociate Evidoc from Job type )</option> -->
												<?php if( !empty( $available_evidocs ) ) { foreach( $available_evidocs as $k => $evidoc_type ) { ?>
													<option value="<?php echo $evidoc_type->audit_type_id; ?>" <?php echo ( $job_type_details->evidoc_type_id == $evidoc_type->audit_type_id ) ? 'selected=selected' : ''; ?> ><?php echo ucwords( $evidoc_type->audit_type.' - '.$evidoc_type->audit_frequency ); ?> <?php echo !empty( $evidoc_type->audit_type ) ? '('.$evidoc_type->audit_group.')' : ''; ?></option>
												<?php } } ?>
											</select>
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Linked Contract</label>
											<select id="contract_id" name="contract_id" class="form-control" style="width:100%; display:none; margin-bottom:10px;" data-label_text="Linked contract"  >	
												<option value="" >Select linked contract</option>
												<?php if( !empty( $available_contracts ) ) { foreach( $available_contracts as $k => $contract ) { ?>
													<option value="<?php echo $contract->contract_id; ?>" <?php echo ( $job_type_details->contract_id == $contract->contract_id ) ? 'selected=selected' : ''; ?> ><?php echo $contract->contract_name; ?></option>
												<?php } } ?>
											</select>
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Base SLA <small>(in hours)</small></label>
											<input name="base_sla" class="form-control" type="text" placeholder="Job base SLA" value="<?php echo $job_type_details->base_sla; ?>" />
										</div>

										<div class="input-group form-group">
											<label class="input-group-addon">Base Priority Rating</label>
											<input name="base_priority_rating" class="form-control" type="text" placeholder="Base Priority Rating" value="<?php echo $job_type_details->base_priority_rating; ?>" />
										</div>
										
									</div>
									
									<!-- RIGHT PANEL -->
									<div class="col-md-6 col-sm-6 col-xs-12">
										
										<div class="input-group form-group">
											<label class="input-group-addon">Damage Details Req?</label>
											<select id="damage_details_req" name="damage_details_req" class="form-control" data-label_text="Damage Details Req" >
												<option value="" >Please select</option>
												<option value="1" <?php echo ( $job_type_details->damage_details_req == 1 ) ? 'selected=selected' : ''; ?> >Yes</option>
												<option value="0" <?php echo ( $job_type_details->damage_details_req != 1 ) ? 'selected=selected' : ''; ?> >No</option>
											</select>
										</div>
										<div class="input-group form-group">
											<label class="input-group-addon">Risk Assessment Req?</label>
											<select id="ra_required" name="ra_required" class="form-control" data-label_text="RA Required" >
												<option >Please select</option>
												<option value="1" <?php echo ( $job_type_details->ra_required == 1 ) ? 'selected=selected' : ''; ?> >Yes</option>
												<option value="0" <?php echo ( $job_type_details->ra_required != 1 ) ? 'selected=selected' : ''; ?> >No</option>
											</select>
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Checklist(s) Required?</label>
											<select id="checklists_required" name="checklists_required" class="form-control" data-label_text="Checklist(s) Required" >
												<option value="" >Please select</option>
												<option value="1" <?php echo ( $job_type_details->checklists_required == 1 ) ? 'selected=selected' : ''; ?> >Yes</option>
												<option value="0" <?php echo ( $job_type_details->checklists_required != 1 ) ? 'selected=selected' : ''; ?> >No</option>
											</select>
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Stock Required?</label>
											<select id="stock_required" name="stock_required" class="form-control" data-label_text="Stock Required" >
												<option value="" >Please select</option>
												<option value="1" <?php echo ( $job_type_details->stock_required == 1 ) ? 'selected=selected' : ''; ?> >Yes</option>
												<option value="0" <?php echo ( $job_type_details->stock_required != 1 ) ? 'selected=selected' : ''; ?> >No</option>
											</select>
										</div>
										<div class="input-group form-group">
											<label class="input-group-addon">BOMs Required?</label>
											<select id="boms_required" name="boms_required" class="form-control" data-label_text="BOMs Required" >
												<option value="" >Please select</option>
												<option value="1" <?php echo ( $job_type_details->boms_required == 1 ) ? 'selected=selected' : ''; ?> >Yes</option>
												<option value="0" <?php echo ( $job_type_details->boms_required != 1 ) ? 'selected=selected' : ''; ?> >No</option>
											</select>
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">BOM Category</label>
											<select id="bom_category_id" name="bom_category_id" class="form-control" data-label_text="BOM Category" >
												<option value="" >Please Select</option>
												<?php if( !empty( $bom_categories ) ) { foreach( $bom_categories as $key => $bom_cat ) { ?>
													<option value="<?php echo $bom_cat->bom_category_id; ?>" <?php echo ( $job_type_details->bom_category_id == $bom_cat->bom_category_id ) ? "selected=selected" : "" ?> ><?php echo $bom_cat->bom_category_name; ?></option>
												<?php } } ?>
											</select>
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Discipline</label>
											<select id="discipline_id" name="discipline_id" class="form-control required" data-label_text="Discipline" >
												<option value="" >Please Select</option>
												<?php if( !empty( $disciplines ) ) { foreach( $disciplines as $disp_key => $discipline ) { ?>
													<option value="<?php echo $discipline->discipline_id; ?>" <?php echo ( $job_type_details->discipline_id == $discipline->discipline_id ) ? "selected=selected" : "" ?> ><?php echo $discipline->account_discipline_name; ?></option>
												<?php } } ?>
											</select>
										</div>
										
										
										<div class="input-group form-group">
											<label class="input-group-addon">Email Notification Req?</label>
											<select id="notification_required" name="notification_required" class="form-control" data-label_text="Email Notification Required" >
												<option value="">Please select</option>
												<option value="1" <?php echo ( $job_type_details->notification_required == 1 ) ? 'selected=selected' : ''; ?> >Yes</option>
												<option value="0" <?php echo ( $job_type_details->notification_required != 1 ) ? 'selected=selected' : ''; ?> >No</option>
											</select>
										</div>
										
										<div class="notification_required_container" style="display:none">
											<div class="input-group form-group">
												<label class="input-group-addon">Emails List</label>
												<textarea name="notification_emails" class="form-control" type="text" style="height:88px;" data-label_text="Notification Emails" placeholder="List of emails addresses e.g. support@yourcompany.com,  customercare@yourcompany.com" value=""><?php echo !empty( $job_type_details->notification_emails ) ? $job_type_details->notification_emails : ''; ?></textarea>
											</div>
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Notify Engineer?</label>
											<select id="notify_engineer" name="notify_engineer" class="form-control" data-label_text="Notify Engineer?" >
												<option value="">Please select</option>
												<option value="1" <?php echo ( $job_type_details->notify_engineer == 1 ) ? 'selected=selected' : ''; ?> >Yes</option>
												<option value="0" <?php echo ( $job_type_details->notify_engineer != 1 ) ? 'selected=selected' : ''; ?> >No</option>
											</select>
										</div>
										
									</div>									
								</div>								
							</div>
							<div class="clearfix"></div>
							<div class="modal-footer">
								<div class="row">
									<div class="col-md-3 col-sm-3 col-xs-12 pull-left">
										<button type="button" class="btn btn-block btn-sm btn-danger" data-dismiss="modal">&nbsp;&nbsp;&nbsp;&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;</button>
									</div>
									<div class="col-md-3 col-sm-3 col-xs-12 pull-right">
										<button id="update-job-type-btn" type="button" class="update-job-btn btn-block  btn btn-sm btn-success">Save Changes</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
			
			<!-- Modal for adding a new Associated Risk. Should this be a modal? -->
			<div class="modal fade add-associated-riskmodal" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title" id="myAddQuestModalLabel">Add a new Job Type Associated Risk</h4>						
						</div>
						<?php include( 'job_type_add_new_risk.php' ); ?>
					</div>
				</div>
			</div>
			
			<!-- Modal for Editing a Associated Risk -->
			<div class="modal fade edit-job-type-risk-modal" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<form id="edit-job-type-risk-form" >
							<input type="hidden" name="risk_id" value="" />
							<input type="hidden" name="page" value="details" />
							<input type="hidden" name="job_type_id" value="<?php echo $job_type_details->job_type_id; ?>" />
					
							<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
								<h4 class="modal-title" id="myEditQuestModalLabel">Edit Job Type Associated Risk ID: <span id="risk_id">Data loading....</span></h4>
								<small id="associated-risk-feedback-message"></small>								
							</div>

							<div class="modal-body job-type-risk-body">
								
							</div>

							<div class="modal-footer">
								<button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">&nbsp;&nbsp;&nbsp;&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;</button>
								<button id="edit-job-type-risk-btn" type="button" class="btn btn-sm btn-success">Save Changes</button>
							</div>
						</form>
					</div>
				</div>
			</div>

			<?php }else{ ?>
				<div class="row">
					<div><?php echo $this->config->item('no_records'); ?> </div>
					<div><span class="pull-right pointer"><i class="fas fa-plus" title="Add new Associated Risk to this EviDoc type" ></i></span></div>
				</div>
			<?php } ?>	
		</div>
	</div>
</div>

<!-- Modal for adding Associated Risks -->
<div class="modal fade add-associated-risks-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title" id="myChecklistsModalLabel">Add Associated Risks</h4>						
			</div>
			<div class="modal-body" id="risk-items-modal-container" >
				<input type="hidden" name="page" value="details" />
				<input type="hidden" name="job_type_id" value="<?php echo $job_type_details->job_type_id; ?>" />
				<label class="strong">Search available Risks</label>
				<div class="form-group">
					<select id="associated_risks_id" name="associated_risks[]" multiple="multiple" class="form-control" style="width:100%; display:none; margin-bottom:10px;" data-label_text="Associated Risks" >
						<option value="" disabled >Search / Select Risks</option>
						<?php if( !empty( $available_risks ) ) { foreach( $available_risks as $k => $risk ) { ?>
							<?php if( !in_array( $risk->risk_id, $linked_risks ) ){ ?>
								<option value="<?php echo $risk->risk_id; ?>" ><?php echo ucwords( $risk->label ).( !empty( $risk->risk_rating ) ? ' | '.$risk->risk_rating : '' ); ?> </option>
							<?php } ?>
						<?php } } ?>
					</select>
				</div>
			
				<?php /* <div class="col-md-12 col-sm-12 col-xs-12 form-group top_search right">
					<!-- Search bar -->
					<div class="input-group" style="width: 100%;">
						<input type="text" id="risks_search" class="form-control <?php echo $module_identier; ?>-search_input" id="search_term" value="" placeholder="Search Risks">
					</div>
					<div id="no-risks-warning" class="text-red" style="display:none;">You do not currently have searchable Rick items on the system!</div>
				</div>
				<div id="risks-container" style="display:none" >
					<input type="hidden" name="page" value="details" />
					<input type="hidden" name="job_type_id" value="<?php echo $job_type_details->job_type_id; ?>" />					
					<div class="row" id="append-risks"></div>						
				</div> */?>
			</div>
			
			<div class="modal-footer">
				<button id="add-associated-risks-btn" class="btn btn-success btn-sm">Add Risks</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal for adding Requiored BOMs -->
<div class="modal fade add-required-boms-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title" id="myBOMItemModalLabel">Add Required BOM Items</h4>						
			</div>
			<div class="modal-body" id="required-boms-modal-container" >
				<input type="hidden" name="page" value="details" />
				<input type="hidden" name="job_type_id" value="<?php echo $job_type_details->job_type_id; ?>" />
				<label class="strong">Search available BOMs</label>
				<div class="form-group">
					<select id="required_boms_id" name="required_boms[]" multiple="multiple" class="form-control" style="width:100%; display:none; margin-bottom:10px;" data-label_text="Required BOM items" >
						<option value="" >Search / Select the BOMs</option>
						<?php if( !empty( $available_boms ) ) { foreach( $available_boms as $key => $bom ) { ?>
							<?php if( !in_array( $bom->item_id, $linked_boms ) ){ ?>
								<option value="<?php echo $bom->item_id; ?>" ><?php echo ucwords( $bom->value.' - '.$bom->label ); ?></option>
							<?php } ?>
						<?php } } ?>
					</select>
				</div>
			</div>
			
			<div class="modal-footer">
				<button id="add-required-boms-btn" class="btn btn-success btn-sm">Add BOMs</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal for Required Skills -->
<div class="modal fade add-required-skills-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title" id="mySkillModalLabel">Add Required Skills</h4>						
			</div>
			<div class="modal-body" id="required-skills-modal-container" >
				<input type="hidden" name="page" value="details" />
				<input type="hidden" name="job_type_id" value="<?php echo $job_type_details->job_type_id; ?>" />
				<label class="strong">Available Skills</label>
				<div class="form-group">
					<select id="required_skills" name="required_skills[]" multiple="multiple" class="form-control" style="width:100%; display:none; margin-bottom:10px;" data-label_text="Associated Risks" >
						<option value="" disabled >Search / Select Skills list</option>
						<?php if( !empty( $available_skills ) ) { foreach( $available_skills as $k => $skill ) { ?>
							<?php if( !in_array( $skill->skill_id, $linked_skills ) ){ ?>
								<option value="<?php echo $skill->skill_id; ?>" ><?php echo ucwords( $skill->skill_name ); ?></option>
							<?php } ?>
						<?php } } ?>
					</select>
				</div>
			</div>
			
			<div class="modal-footer">
				<button id="add-required-skills-btn" class="btn btn-success btn-sm">Add Selected Skills</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal for adding Required Evidoc -->
<div class="modal fade add-associated-evidoc-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title" id="myEvidocModalLabel">Add Associated Evidoc</h4>						
			</div>
			<div class="modal-body" id="associated-evidoc-modal-container" >
				<div class="input-group form-group">
					<label class="input-group-addon">Linked Evidoc</label>
					<select id="evidoc_type_id_modal" name="evidoc_type_id_modal" class="form-control" style="width:100%; display:none; margin-bottom:10px; background-color:none" data-label_text="Linked Evidoc" >
						<!-- <option >Search / Select the linked Evidoc</option> -->
						<option value=" ">Disassociate Evidoc from Job type</option>
						<?php if( !empty( $available_evidocs ) ) { foreach( $available_evidocs as $k => $evidoc_type ) { ?>
							<option value="<?php echo $evidoc_type->audit_type_id; ?>" <?php echo ( $job_type_details->evidoc_type_id == $evidoc_type->audit_type_id ) ? 'selected=selected' : ''; ?> ><?php echo ucwords( $evidoc_type->audit_type.' - '.$evidoc_type->audit_frequency ); ?> <?php echo !empty( $evidoc_type->audit_type ) ? '('.$evidoc_type->audit_group.')' : ''; ?></option>
						<?php } } ?>
					</select>
				</div>
			</div>
			
			<div class="modal-footer">
				<button id="add-associated-evidoc-btn" class="btn btn-success btn-sm">Attach Evidoc</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal for adding Required Checklists -->
<div class="modal fade add-required-checklists-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title" id="myChecklistsModalLabel">Add Required Checklists</h4>						
			</div>
			<div class="modal-body" id="required-checklists-modal-container" >
				<input type="hidden" name="page" value="details" />
				<input type="hidden" name="job_type_id" value="<?php echo $job_type_details->job_type_id; ?>" />
				<label class="strong">Search available Checklists</label>
				<div class="form-group">
					<select id="required_checklists_id" name="required_checklists[]" multiple="multiple" class="form-control" style="width:100%; display:none; margin-bottom:10px;" data-label_text="Required Checklists" >
						<option value="" disabled >Search / Select Checklists</option>
						<?php if( !empty( $available_checklists ) ) { foreach( $available_checklists as $k => $checklist ) { ?>
							<?php if( !in_array( $checklist->checklist_id, $linked_checklists ) ){ ?>
								<option value="<?php echo $checklist->checklist_id; ?>" ><?php echo ucwords( $checklist->checklist_id ).( !empty( $checklist->checklist_desc ) ? ' | '.$checklist->checklist_desc : '' ); ?> </option>
							<?php } ?>
						<?php } } ?>
					</select>
				</div>
			</div>			
			<div class="modal-footer">
				<button id="add-required-checklists-btn" class="btn btn-success btn-sm">Add Checklists</button>
			</div>
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){
		
		$( '#contract_id' ).select2({
			dropdownParent: $( "#edit-job-type-modal" )
		});
		
		$( '#evidoc_type_id' ).select2({
			dropdownParent: $( "#edit-job-type-modal" )
		});
		
		$( '#associated_risks_id' ).select2({
			allowClear: true, 
			minimumResultsForSearch: -1,
		});
		
		$( '#required_skills, #required_boms_id, #required_checklists_id' ).select2({
			allowClear: true, 
			minimumResultsForSearch: -1,
		});
		
		$( '.job-type-risk-body' ).on( '#response_type, change', function(){
			
			var respType 	= $( 'option:selected', this ).data( 'resp_type' );
			var respTypeAlt = $( 'option:selected', this ).data( 'resp_type_alt' );
			var respDesc 	= $( 'option:selected', this ).data( 'resp_desc' );

			$( '.resp-extra-options' ).hide();
			$( '#selected-option' ).text( '' );

			$( '.resp-type-options' ).hide();
			$( '.resp-type-options' ).hide();
			$( '.resp_' + respType ).show();
		});

		
		$('#edit-job-type-modal').on('hidden.bs.modal', function () { 
			location.reload();
		});
		
		$( '.section-container-bar' ).click( function(){
			$( this ).closest( 'div' ).find( '.caret-icon' ).toggleClass('fa-caret-up fa-caret-down');			
		});
		
		$( '.edit-job-type, .add-associated-evidoc' ).click( function(){
			$(".edit-job-type-modal").modal("show");
		} );
		
		//Delete or archive associated risk
		$( '.remove-risk' ).click( function(){
			
			var jobTypeId  	= $( this ).data( 'job_type_id' );
			var riskId  	= $( this ).data( 'risk_id' );
			var	sectionName	= 'not-set';
			if( riskId == 0 || riskId == undefined ){
				swal({
					title: 'Oops! Something went wrong',
					type: 'error',
					text: 'Please reload the page and try again!',
				})
			}
			swal({
				title: 'Confirm remove Risk?',
				type: 'warning',
				text: 'This is an irreversible action',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
			
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/remove_associated_risks/' ); ?>" + riskId,
						method:"POST",
						data:{ page:"details", job_type_id:jobTypeId ,risk_id:riskId },
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2100
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
		$( '[name="job_type"]' ).keyup( function(){
			$( this ).css("border","1px solid #ccc");
			$( '#feedback-message' ).show().text( '' );			
		} );
	
		//Submit form for processing
		$( '#update-job-type-btn' ).click( function( event ){
			
			$( '#feedback-message' ).show();
			$( '#feedback-message' ).text( '' );

			event.preventDefault();
			
			var jobTypeName	= $( '#job_type' ).val();
			var jobTypeDesc = $( '#job_type_desc' ).val();
			
			if( jobTypeName.length == 0 ){
				$( '[name="job_type"]' ).focus().css("border","1px solid red");
				$( '#feedback-message' ).html( '<span class="text-red">Job Type is required</span>' );
				return false;
			}
			
			if( jobTypeDesc.length == 0 ){
				$( '[name="job_type_desc"]' ).focus().css("border","1px solid red");
				$( '#feedback-message' ).html( '<span class="text-red">Job Type is required</span>' );
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
						url:"<?php echo base_url('webapp/job/update_job_type/'.$job_type_details->job_type_id ); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function( data ){
							$( '#feedback-message' ).html( data.status_msg );

							if( data.job_type.job_base_rate_adjustable == 1 ){
								$( '[name="job_base_rate"]' ).prop( 'readonly', false );
							} else {
								$( '[name="job_base_rate"]' ).prop( 'readonly', true );
							}
							
							setTimeout(function(){
								$( '#feedback-message' ).fadeOut( 'slow' );
							},3000 );
						}
					});
				}
			}).catch( swal.noop )

		});
		
		$( '#add-associated-risks-btn' ).click( function(){
			var formData = $( "#risk-items-modal-container :input").serialize();
			$.ajax({
				url:"<?php echo base_url('webapp/job/add_associated_risks/' ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){
						
						$( '.add-associated-risks-modal' ).modal( 'hide' );
						$( '.modal-backdrop' ).remove();
						
						swal({
							type: 'success',
							html: data.status_msg,
							showConfirmButton: false,
							timer: 3000
						})
						
						window.setTimeout(function(){ 
							location.reload();
						} ,1000);
					} else {
						swal({
							type: 'error',
							title: data.status_msg
						})
					}		
				}
			});
			return false;
		});

		//Remove item from list
		$( '#append-risks' ).on( 'click', '.removeme',function(){
			var classId = $( this ).data( 'content_wrapper' );
			$( '#'+classId ).remove();			
			var numItems = parseInt( $( 'div .new-item' ).length );
			if( numItems == 0 ){
				$( '#risks-container' ).hide();
			}
		});
		
		$('#risks_search').blur( function(){
			$( '#no-risks-warning' ).hide();
		});
		
		$('#boms_search').blur( function(){
			$( '#no-boms-warning' ).hide();
		});
		
		$( '.add-new-associated-risk' ).click( function(){
			$(".add-associated-risks-modal").modal("show");
			$( "#risks_search" ).autocomplete( "option", "appendTo", "#risk-items-modal-container" );
		} );
		
		//Delete or archive associated risk
		$( '.remove-evidoc' ).click( function(){
			
			var jobTypeId  	 = $( this ).data( 'job_type_id' );
			var evidocTypeId = $( this ).data( 'evidoc_type_id' );
			var	sectionName	 = 'not-set';
			if( evidocTypeId == 0 || evidocTypeId == undefined ){
				swal({
					title: 'Oops! Something went wrong',
					type: 'error',
					text: 'Please reload the page and try again!',
				})
			}
			swal({
				title: 'Confirm disassociate EviDoc?',
				type: 'warning',
				text: 'This is an irreversible action',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
			
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/disassociate/' ); ?>" + evidocTypeId,
						method:"POST",
						data:{ page:"details", job_type_id:jobTypeId ,evidoc_type_id:evidocTypeId },
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2100
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
		
		
		$( '.add-required-skills' ).click( function(){
			$(".add-required-skills-modal").modal( "show" );
		} );
		
		
		$( '#add-required-skills-btn' ).click( function(){
			var formData = $( "#required-skills-modal-container :input").serialize();
			$.ajax({
				url:"<?php echo base_url('webapp/job/add_required_skills/' ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){
						
						$( '.add-required-skills-modal' ).modal( 'hide' );
						$( '.modal-backdrop' ).remove();
						
						swal({
							type: 'success',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 3000
						})
						
						window.setTimeout(function(){ 
							location.reload();
						} ,1000);
					} else {
						swal({
							type: 'error',
							title: data.status_msg
						})
					}		
				}
			});
			return false;
		});
		
		
		//Remove required Skill
		$( '.remove-skill' ).click( function(){
			
			var jobTypeId  	= $( this ).data( 'job_type_id' );
			var skillId  	= $( this ).data( 'skill_id' );
			var	sectionName	= 'not-set';
			if( skillId == 0 || skillId == undefined ){
				swal({
					title: 'Oops! Something went wrong',
					type: 'error',
					text: 'Please reload the page and try again!',
				})
			}
			swal({
				title: 'Confirm remove Skill?',
				type: 'warning',
				text: 'This is an irreversible action',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
			
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/remove_required_skill/' ); ?>" + skillId,
						method:"POST",
						data:{ page:"details", job_type_id:jobTypeId, skill_id:skillId },
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2100
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
		
		
		//ARCHIVE JOB TYPE
		$( '.archive-job-type-btn' ).click( function( event ){
			
			var jobTypeId = $( this ).data( 'job_type_id' );
			
			event.preventDefault();

			swal({
				type: 'warning',
				title: 'Confirm Archive Job Type?',
				html: 'This will affect any associated Jobs, Risks and Evidoc types!',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/delete_job_type/'.$job_type_details->job_type_id ); ?>",
						method:"POST",
						data:{ page:'details', xsrf_token: xsrfToken, job_type_id:jobTypeId },
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2100
								})
								window.setTimeout(function(){
									window.location.href = "<?php echo base_url('webapp/job/job_types'); ?>";
								} ,3000);
							}else{
								swal({
									type: 'error',
									title: data.status_msg
								})
							}
						}
					});
				}
			}).catch(swal.noop)
		});
	});
	
	
	$( '.add-new-required-bom' ).click( function(){
		$(".add-required-boms-modal").modal("show");
		$( "#boms_search" ).autocomplete( "option", "appendTo", "#bom-items-modal-container" );
	} );
	
	
	$( '#add-required-boms-btn' ).click( function(){
		var formData = $( "#required-boms-modal-container :input").serialize();
		$.ajax({
			url:"<?php echo base_url('webapp/job/add_required_boms/' ); ?>",
			method:"POST",
			data:formData,
			dataType: 'json',
			success:function(data){
				if( data.status == 1 ){
					
					$( '.add-required-boms-modal' ).modal( 'hide' );
					$( '.modal-backdrop' ).remove();
					
					swal({
						type: 'success',
						title: data.status_msg,
						showConfirmButton: false,
						timer: 3000
					})
					
					window.setTimeout(function(){ 
						location.reload();
					} ,1000);
				} else {
					swal({
						type: 'error',
						title: data.status_msg
					})
				}		
			}
		});
		return false;
	});
	
	
	//Delete or Archive Required BOM item
	$( '.remove-bom' ).click( function(){
		
		var jobTypeId  	= $( this ).data( 'job_type_id' );
		var itemId  	= $( this ).data( 'item_id' );
		var	sectionName	= 'not-set';
		if( itemId == 0 || itemId == undefined ){
			swal({
				title: 'Oops! Something went wrong',
				type: 'error',
				text: 'Please reload the page and try again!',
			})
		}
		swal({
			title: 'Confirm remove BOM Item?',
			type: 'warning',
			text: 'This is an irreversible action',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
		
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url('webapp/job/remove_required_boms/' ); ?>" + itemId,
					method:"POST",
					data:{ page:"details", job_type_id:jobTypeId ,item_id:itemId },
					dataType: 'json',
					success:function(data){
						if( data.status == 1 ){
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 2100
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
	
	
	$( '.add-required-checklists' ).click( function(){
		$(".add-required-checklists-modal").modal( "show" );
	} );
	
	
	$( '#add-required-checklists-btn' ).click( function(){
		var formData = $( "#required-checklists-modal-container :input").serialize();
		$.ajax({
			url:"<?php echo base_url('webapp/job/add_required_checklists/' ); ?>",
			method:"POST",
			data:formData,
			dataType: 'json',
			success:function(data){
				if( data.status == 1 ){
					
					$( '.add-required-checklists-modal' ).modal( 'hide' );
					$( '.modal-backdrop' ).remove();
					
					swal({
						type: 'success',
						title: data.status_msg,
						showConfirmButton: false,
						timer: 3000
					})
					
					window.setTimeout(function(){ 
						location.reload();
					} ,1000);
				} else {
					swal({
						type: 'error',
						title: data.status_msg
					})
				}		
			}
		});
		return false;
	});
	

	//Remove required Checklist
	$( '.remove-checklist' ).click( function(){
		
		var jobTypeId  	= $( this ).data( 'job_type_id' );
		var checklistId = $( this ).data( 'checklist_id' );
		var	sectionName	= 'not-set';
		if( checklistId == 0 || checklistId == undefined ){
			swal({
				title: 'Oops! Something went wrong',
				type: 'error',
				text: 'Please reload the page and try again!',
			})
		}
		swal({
			title: 'Confirm remove Checklist?',
			type: 'warning',
			text: 'This is an irreversible action',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
		
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url('webapp/job/remove_required_checklist/' ); ?>" + checklistId,
					method:"POST",
					data:{ page:"details", job_type_id:jobTypeId, checklist_id:checklistId },
					dataType: 'json',
					success:function(data){
						if( data.status == 1 ){
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 2100
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
	
	
	var emailReq = $( "#notification_required option:selected" ).val();
	if( emailReq == 1 ){
		$( '.notification_required_container' ).show();
	} else {
		$( '.notification_required_container' ).hide();
	}
	
	//Email Notification Required
	$( '#notification_required' ).on('change', function() {
		var emailReq = $( "#notification_required option:selected" ).val();;
		if( emailReq == 1 ){
			$( '.notification_required_container' ).slideDown();
		} else {
			$( '.notification_required_container' ).slideUp( 'slow' );
		}
	});
	
</script>

