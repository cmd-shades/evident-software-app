<style>
	.panel-body{
		background-color:#F7F7F7; 
		height:140px; 
		min-height:140px;
	}
</style>


<div>
	<br/>
	<div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12">
		<!-- <span><small id="feedback-message" class="text-red">Error!</small></span> -->
		<div class="x_panel tile has-shadow">
			<legend class="evidocs-legend text-bold">NEW STANDARD JOB TYPE</legend>
			<form id="job-type-creation-form" >
				<input type="hidden" name="override_existing" value="" />
				<input type="hidden" name="page" value="details" />
				<input type="hidden" name="job_type_id" value="" />
				<input type="hidden" name="is_reactive" value="0" />
				<div class="rows">
					<div class="job_type_creation_panel1 col-md-12 col-sm-12 col-xs-12">
					
						<h4>What is the name of this Job Type?</h4>
						<div class="">
							<div class="form-group">
								<input name="job_type" class="form-control required" type="text" data-label_text="Job Type" placeholder="Enter a short name here max 50 chars" value="" />
							</div>							
						</div>
						
						<div class="margin-top-15 margin-bottom-15">
							<div class="form-group" >
								<h4>Does this have a Sub-type <small>(where applicable)</small>?</h4>
								<select id="job_type_subtype" name="job_type_subtype" class="form-control" data-label_text="Job Type Sub-type" >
									<option value="" >Please Select a sub-type</option>
									<?php if (!empty($job_type_sub_types)) {
									    foreach ($job_type_sub_types as $sub_type => $sub_type_name) { ?>
										<option value="<?php echo $sub_type; ?>" ><?php echo $sub_type_name; ?></option>
									<?php }
									    } ?>
								</select>
							</div>
						</div>
						
						<div>
							<h4>Please provide a detailed description</h4>
							<div class="">
								<div class="form-group">
									<textarea name="job_type_desc" class="form-control required" type="text" data-label_text="Job Type Description" placeholder="Give a detailed description of what this Job Type does..." value=""></textarea>
								</div>							
							</div>
						</div>
						
						<div class="">
							<h4>Which Discipline does this Job Type belong to?</h4>
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="form-group has-shadow" >
										<select id="discipline_id" name="discipline_id" class="form-control required" data-label_text="Discipline" >
											<option value="" >Please Select a Discipline</option>
											<?php if (!empty($disciplines)) {
											    foreach ($disciplines as $k => $discipline) { ?>
												<option value="<?php echo $discipline->discipline_id; ?>" ><?php echo $discipline->account_discipline_name; ?></option>
											<?php }
											    } ?>
										</select>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-success btn-next job-type-creation-steps fetch-discipline-evidoc-types-btn" data-currentpanel="job_type_creation_panel1" type="button">Next</button>				
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">&nbsp;</div>
						</div>
					</div>
					
					<div class="job_type_creation_panel2 col-md-12 col-sm-12 col-xs-12" style="display:none" >

						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<!-- <h4>Please set the base Job Duration for this Job Type</h4>
								<div class="form-group" >
									<select name="job_base_duration" class="form-control" >
										<option>Please select</option>
										<?php if (!empty($job_durations)) {
										    foreach ($job_durations as $k => $duration) { ?>
											<option value="<?php echo $k; ?>" <?php echo ($k == '1.0') ? 'selected="selected"' : '' ?> ><?php echo $duration; ?></option>
										<?php }
										    } ?>
									</select>
								</div> -->
								<h4>What is the Job base rate for this Job Type (&pound;)?</h4>
								<div class="form-group row" >
									<div class="col-md-12 col-sm-12 col-xs-12">
										<input name="job_base_rate" class="numbers-only form-control" type="text" data-label_text="Job base Rate" placeholder="e.g. 20.00" value="0.00" />
									</div>
								</div>
								<h4>Is this rate adjustable?</h4>
								<div class="form-group row" >
									<div class="col-md-3 col-sm-6 col-xs-12 ">
										<div class="radio">
											<label><input type="radio" name="job_base_rate_adjustable" value="1" style="margin-top:6px;"> Yes</label>
										</div>
									</div>
									<div class="col-md-3 col-sm-6 col-xs-12">
										<div class="radio">
											<label><input type="radio" name="job_base_rate_adjustable" value="0" style="margin-top:6px;" checked > No</label>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-back" data-currentpanel="job_type_creation_panel2" type="button" >Back</button>					
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-success btn-next job-type-creation-steps" data-currentpanel="job_type_creation_panel2" type="button" >Next</button>					
							</div>
						</div>
					</div>
					
					<div class="job_type_creation_panel3 col-md-12 col-sm-12 col-xs-12" style="display:none" >
						<div class="hide">
							<h4>What category does this Job Type this belong to?</h4>
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-11">
									<div class="form-group has-shadow" >
										<select id="job_type_category_id" name="category_id" class="form-control" data-label_text="Job Type Category" >
											<option value="" >Please Select a category</option>
											<?php if (!empty($evidoc_categories)) {
											    foreach ($evidoc_categories as $k => $category) { ?>
												<option value="<?php echo $category->category_id; ?>" ><?php echo $category->category_name_alt; ?> - <small><?php echo $category->description; ?></small></option>
											<?php }
											    } ?>
										</select>
									</div>
								</div>
								<div class="col-md-1 col-sm-1 col-xs-1 hide">
									<div id="job-type-category-quick-add" style="margin-top:4px" class="job-type-category-quick-add pointer" title="Quick Add new category option"><span class="pull-right"><i class="fas fa-plus-circle fa-2x text-green"></i></span></div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<h4>Is a Risk Assessment required for this Job Type?</h4>
							</div>
							<div class="col-md-3 col-sm-6 col-xs-12">
								<div class="radio">
									<label><input type="radio" class="ra_required" name="ra_required" value="1" style="margin-top:6px;"> Yes</label>
								</div>
							</div>
							<div class="col-md-3 col-sm-6 col-xs-12">
								<div class="radio">
									<label><input type="radio" class="ra_required" name="ra_required" value="0" style="margin-top:6px;" checked > No</label>
								</div>
							</div>
						</div>
						
						<div class="ra_required_container" style="display:none">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="form-group has-shadow margin-bottom-15" >
										<select id="associated_risks_id" name="associated_risks[]" multiple="multiple" class="form-control" style="width:100%; display:none; margin-bottom:10px;" data-label_text="Associated Risks" >
											<option value="" >Search / Select the Risks</option>
											<?php if (!empty($available_risks)) {
											    foreach ($available_risks as $k => $risk) { ?>
												<option value="<?php echo $risk->risk_id; ?>" ><?php echo ucwords($risk->risk_text.' - '.$risk->risk_rating); ?></option>
											<?php }
											    } ?>
										</select>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-back" data-currentpanel="job_type_creation_panel3" type="button" >Back</button>					
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-success btn-next job-type-creation-steps" data-currentpanel="job_type_creation_panel3" type="button" >Next</button>					
							</div>
						</div>
					</div>
					
					<div class="job_type_creation_panel4 col-md-12 col-sm-12 col-xs-12" style="display:none" >
						
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<h4>Is/are Evidoc(s) required for this Job Type? <small></small></h4>
							</div>
							<div class="col-md-3 col-sm-6 col-xs-12">
								<div class="radio">
									<label><input type="radio" class="evidoc_required" name="evidoc_required" value="1" style="margin-top:6px;"> Yes</label>
								</div>
							</div>
							<div class="col-md-3 col-sm-6 col-xs-12">
								<div class="radio">
									<label><input type="radio" class="evidoc_required" name="evidoc_required" value="0" style="margin-top:6px;" checked > No</label>
								</div>
							</div>
						</div>
						
						<div class="evidoc_required_container" style="display:none">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<h4>Please select required Evidoc?</h4>
									<div class="row">
										<div class="col-md-12 col-sm-12 col-xs-12">
											<div class="form-group has-shadow margin-bottom-15" id="filtered_evidoc_types" >
												<select id="evidoc_type_id" name="evidoc_type_id" class="form-control" style="width:100%; display:none; margin-bottom:10px;" data-label_text="Linked Evidoc" >
													<option value="" >Search / Select the Evidoc</option>
													<?php if (!empty($available_evidocs)) {
													    foreach ($available_evidocs as $k => $evidoc_type) { ?>
														<option value="<?php echo $evidoc_type->audit_type_id; ?>" ><?php echo ucwords($evidoc_type->audit_type.' - '.$evidoc_type->audit_frequency); ?> <?php echo !empty($evidoc_type->audit_type) ? '('.$evidoc_type->audit_group.')' : ''; ?></option>
													<?php }
													    } ?>
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<h4>Are Checklists Required for this Job Type?</h4>
							</div>
							<div class="col-md-3 col-sm-6 col-xs-12">
								<div class="radio">
									<label><input type="radio" class="checklists_required" name="checklists_required" value="1" style="margin-top:6px;"> Yes</label>
								</div>
							</div>
							<div class="col-md-3 col-sm-6 col-xs-12">
								<div class="radio">
									<label><input type="radio" class="checklists_required" name="checklists_required" value="0" style="margin-top:6px;" checked > No</label>
								</div>
							</div>
						</div>

						<div class="checklists_required_container" style="display:none">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="form-group has-shadow margin-bottom-15" >
										<select id="required_checklists_id" name="required_checklists[]" multiple="multiple" class="form-control" style="width:100%; display:none; margin-bottom:10px;" data-label_text="Associated Checklists" >
											<option value="" >Search / Select the required Checklists</option>
											<?php if (!empty($available_checklists)) {
											    foreach ($available_checklists as $k => $checklist) { ?>
												<option value="<?php echo $checklist->checklist_id; ?>" ><?php echo ucwords($checklist->checklist_desc); ?></option>
											<?php }
											    } ?>
										</select>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<h4>Does this Job Type require an <strong>Email Notification</strong> to be sent out when the Job is created?</h4>
							</div>
							<div class="col-md-3 col-sm-6 col-xs-12">
								<div class="radio">
									<label><input type="radio" class="notification_required" name="notification_required" value="1" style="margin-top:6px;"> Yes</label>
								</div>
							</div>
							<div class="col-md-3 col-sm-6 col-xs-12">
								<div class="radio">
									<label><input type="radio" class="notification_required" name="notification_required" value="0" style="margin-top:6px;" checked > No</label>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<h4>Does this Job Type require Engineers to be Notified when a Job is assigned to them?</h4>
							</div>
							<div class="col-md-3 col-sm-6 col-xs-12">
								<div class="radio">
									<label><input type="radio" class="notify_engineer" name="notify_engineer" value="1" style="margin-top:6px;"> Yes</label>
								</div>
							</div>
							<div class="col-md-3 col-sm-6 col-xs-12">
								<div class="radio">
									<label><input type="radio" class="notify_engineer" name="notify_engineer" value="0" style="margin-top:6px;" checked > No</label>
								</div>
							</div>
						</div>

						<div class="notification_required_container" style="display:none">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<h4>List of emails or email group (comma separated)</h4>
									<div class="form-group">
										<textarea name="notification_emails" class="form-control" type="text" data-label_text="Notification Emails" placeholder="List of emails addresses e.g. support@yourcompany.com,  customercare@yourcompany.com" value=""></textarea>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-back" data-currentpanel="job_type_creation_panel4" type="button" >Back</button>					
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-success btn-next job-type-creation-steps" data-currentpanel="job_type_creation_panel4" type="button" >Next</button>
							</div>
						</div>
					</div>
					
					<?php /* Comment out until functionality is ready
                    <div class="job_type_creation_panel5 col-md-12 col-sm-12 col-xs-12" style="display:none" >

                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <h4>Does this Job Type require an NPS rating? <small></small></h4>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="radio">
                                    <label><input type="radio" class="nps_required" name="nps_required" value="1" style="margin-top:6px;"> Yes</label>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="radio">
                                    <label><input type="radio" class="nps_required" name="nps_required" value="0" style="margin-top:6px;" checked > No</label>
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12 nps_required_container" style="display:none">
                                <div>
                                    <h6>NPS setup here... coming soon!</h6>
                                    <h6>NPS setup here... coming soon!</h6>
                                    <h6>NPS setup here... coming soon!</h6>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <button class="btn btn-block btn-flow btn-back" data-currentpanel="job_type_creation_panel5" type="button" >Back</button>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <button class="btn btn-block btn-flow btn-success btn-next job-type-creation-steps" data-currentpanel="job_type_creation_panel5" type="button" >Next</button>
                            </div>
                        </div>
                    </div>

                    <div class="job_type_creation_panel6 col-md-12 col-sm-12 col-xs-12" style="display:none" >

                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <h4>Does this Job Type require a CSAT score? <small></small></h4>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="radio">
                                    <label><input type="radio" class="csat_required" name="csat_required" value="1" style="margin-top:6px;"> Yes</label>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="radio">
                                    <label><input type="radio" class="csat_required" name="csat_required" value="0" style="margin-top:6px;" checked > No</label>
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="csat_required_container" style="display:none">
                                    <h6>CSAT setup here... coming soon!</h6>
                                    <h6>CSAT setup here... coming soon!</h6>
                                    <h6>CSAT setup here... coming soon!</h6>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <button class="btn btn-block btn-flow btn-back" data-currentpanel="job_type_creation_panel6" type="button" >Back</button>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <button class="btn btn-block btn-flow btn-success btn-next job-type-creation-steps" data-currentpanel="job_type_creation_panel6" type="button" >Next</button>
                            </div>
                        </div>
                    </div>
                    */?>
					
					<?php /* When the above NPS/CSAT are uncommented, please change the panel numbers below e.g. job_type_creation_panel7, job_type_creation_panel8 */ ?>
					<div class="job_type_creation_panel5 col-md-12 col-sm-12 col-xs-12" style="display:none" >
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<h4>Does this Job Type require Stock to be added? <small></small></h4>
							</div>
							<div class="col-md-3 col-sm-6 col-xs-12">
								<div class="radio">
									<label><input type="radio" class="stock_required" name="stock_required" value="1" style="margin-top:6px;"> Yes</label>
								</div>
							</div>
							<div class="col-md-3 col-sm-6 col-xs-12">
								<div class="radio">
									<label><input type="radio" class="stock_required" name="stock_required" value="0" style="margin-top:6px;" checked > No</label>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<h4>Does this Job Type require BOMs/SORs to be added? <small></small></h4>
							</div>
							<div class="col-md-3 col-sm-6 col-xs-12">
								<div class="radio">
									<label><input type="radio" class="required_boms" name="boms_required" value="1" style="margin-top:6px;"> Yes</label>
								</div>
							</div>
							<div class="col-md-3 col-sm-6 col-xs-12">
								<div class="radio">
									<label><input type="radio" class="required_boms" name="boms_required" value="0" style="margin-top:6px;" checked > No</label>
								</div>
							</div>
						</div>
						
						<div class="required_boms_container" style="display:none">

							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="form-group" >
										<h4>Select the BOM Category<small>(where applicable)</small></h4>
										<div class="row" >
											<div class="col-md-11 col-sm-11 col-xs-11">
												<select id="bom_category_id" name="bom_category_id" class="form-control" data-label_text="BOM Category" >
													<option value="" >Please Select a sub-type</option>
													<?php if (!empty($bom_categories)) {
													    foreach ($bom_categories as $key => $bom_cat) { ?>
														<option value="<?php echo $bom_cat->bom_category_id; ?>" ><?php echo $bom_cat->bom_category_name; ?> <?php echo !empty($bom_cat->bom_category_name) ? ' - '.$bom_cat->bom_category_name : ''; ?></option>
													<?php }
													    } ?>
												</select>
											</div>
											<div class="col-md-1 col-sm-1 col-xs-1 hide">
												<div id="bom-category-quick-add" style="margin-top:4px" class="pointer" title="Quick Add new BOM category"><span class="pull-right"><i class="fas fa-plus-circle fa-2x text-green"></i></span></div>
											</div>
										</div>
									</div>
								</div>

								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="form-groupmargin-bottom-15" >
										<h4>Select default BOMs</h4>
										<select id="required_boms_id" name="required_boms[]" multiple="multiple" class="form-control" style="width:100%; display:none; margin-bottom:10px;" data-label_text="Required BOM items" >
											<option value="" >Search / Select the BOMs</option>
											<?php if (!empty($available_boms)) {
											    foreach ($available_boms as $key => $bom) { ?>
												<option value="<?php echo $bom->item_id; ?>" ><?php echo ucwords($bom->value.' - '.$bom->label); ?></option>
											<?php }
											    } ?>
										</select>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-back" data-currentpanel="job_type_creation_panel5" type="button" >Back</button>					
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-success btn-next job-type-creation-steps" data-currentpanel="job_type_creation_panel5" type="button" >Next</button>
							</div>
						</div>
					</div>
					
					<div class="job_type_creation_panel6 col-md-12 col-sm-12 col-xs-12" style="display:none" >
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<h4>What is the base SLA for this Job type? <small>Time in hours e.g. 2 hours</small></h4>
								<div class="form-group row" >
									<div class="col-md-12 col-sm-12 col-xs-12">
										<input name="base_sla" class="numbers-only form-control" type="text" data-label_text="Base SLA" placeholder="Base SLA e.g. 2 hours" value="" />
									</div>
									<?php /*<select id="base_sla" name="base_sla" class="form-control" style="width:100%; margin-bottom:10px;" data-label_text="Base SLA" >
                                        <option value="" >Select the base SLA</option>
                                        <?php if( !empty( $defined_slas ) ) { foreach( $defined_slas as $k => $sla ) { ?>
                                            <option value="<?php echo $sla->value; ?>" ><?php echo $sla->description; ?></option>
                                        <?php } } ?>
                                    </select> */ ?>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<h4>What is the base Priority rating for this Job type?</h4>
								<div class="form-group row" >
									<div class="col-md-12 col-sm-12 col-xs-12">
										<input name="base_priority_rating" class="numbers-only form-control" type="text" data-label_text="Base Priority Rating" placeholder="50" value="" />
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<h4>Details of Damage to Property required?</h4>
								<div class="form-group row" >
									<div class="col-md-3 col-sm-6 col-xs-12">
										<div class="radio">
											<label><input type="radio" class="damage_details_req" name="damage_details_req" value="1" style="margin-top:6px;"> Yes</label>
										</div>
									</div>
									<div class="col-md-3 col-sm-6 col-xs-12">
										<div class="radio">
											<label><input type="radio" class="damage_details_req" name="damage_details_req" value="0" style="margin-top:6px;" checked > No</label>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-back" data-currentpanel="job_type_creation_panel6" type="button" >Back</button>					
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-success btn-next job-type-creation-steps" data-currentpanel="job_type_creation_panel6" type="button" >Next</button>
							</div>
						</div>
					</div>
					
					<div class="job_type_creation_panel7 col-md-12 col-sm-12 col-xs-12" style="display:none" >
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<h4>Which contract is this Job Type for?</h4>
								<div class="row">
									<div class="col-md-12 col-sm-12 col-xs-12">
										<div class="form-group  has-shadow" >
											<select id="contract_id" name="contract_id" class="form-control" style="width:100%" data-label_text="Linked contract" >
												<option value="" >Select linked contract</option>
												<?php if (!empty($available_contracts)) {
												    foreach ($available_contracts as $k => $contract) { ?>
													<option value="<?php echo $contract->contract_id; ?>" ><?php echo $contract->contract_name; ?></option>
												<?php }
												    } ?>
											</select>
										</div>
									</div>
								</div>
							</div>
						
							<div class="col-md-12 col-sm-12 col-xs-12">
								<h4>What Skills are required to completed this Job Type?</h4>
								<div class="form-group has-shadow margin-bottom-15" >
									<select id="required_skills" name="required_skills[]" multiple="multiple" class="form-control" style="width:100%; margin-bottom:10px;" data-label_text="Associated Risks" >
										<option value="" >Search / Select Skill set list</option>
										<?php if (!empty($available_skills)) {
										    foreach ($available_skills as $k => $skill) { ?>
											<option value="<?php echo $skill->skill_id; ?>" ><?php echo ucwords($skill->skill_name); ?></option>
										<?php }
										    } ?>
									</select>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-back" data-currentpanel="job_type_creation_panel7" type="button" >Back</button>					
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-success btn-next job-type-creation-steps" data-currentpanel="job_type_creation_panel7" type="button" >Next</button>					
							</div>
						</div>
					</div>
					
					<div class="job_type_creation_panel8 col-md-12 col-sm-12 col-xs-12" style="display:none" >
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<div class="form-group">
									<div class="text-center">
										<br/>
										<p>You are about to submit a request to create a new Job Type.</p>
										<p>Click the "Create Job Type" to proceed or Back to review your Job Type setup.</p>
										<br/>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button class="btn btn-block btn-flow btn-back" data-currentpanel="job_type_creation_panel8" type="button" >Back</button>					
							</div>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<button id="create-job-type-btn" class="btn btn-block btn-flow btn-success btn-next" type="button" >Create Standard Job Type</button>
							</div>
						</div>
					</div>
				</div>
			</form>
			
			<!-- Modal for adding a new category -->
			<div class="modal fade add-category-modal" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-md">
					<form id="add-category-form-container" >
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
								<h4 class="modal-title" id="myCategoryModalLabel">Add an Job Type Category</h4>
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

	$( document ).ready( function() {
		
		$( '.fetch-discipline-evidoc-types-btn' ).click( function(){
			
			var disciplineId 	= $( '#discipline_id option:selected' ).val();

			var wheRe = {
				discipline_id: disciplineId
			};

			$.ajax({
				url:"<?php echo base_url('webapp/job/evidoc_types_by_discipline/'); ?>",
				method:"POST",
				data:{ page:'details', where:wheRe},
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 && ( data.evidoc_types_data !== '' ) ){
						$( '#filtered_evidoc_types' ).html( data.evidoc_types_data );						
						return false;
					}		
				}
			});
			
			return false;
			
		});

		$( '#evidoc_type_id, #associated_risks_id, #required_skills, #required_boms_id, #required_checklists_id' ).select2({
			allowClear: true,
			minimumResultsForSearch: -1,
		});
		
		//Risk Assessment requirements
		$('.ra_required').on('change', function() {
			var raReq = $("input[name='ra_required']:checked").val();
			if( raReq == 1 ){
				$( '.ra_required_container' ).slideDown();
			} else {
				$( '.ra_required_container' ).slideUp( 'slow' );
			}
		});
		
		//Required STOCK Items
		$('.required_stock').on('change', function() {
			var raReq = $("input[name='stock_required']:checked").val();
			if( raReq == 1 ){
				$( '.required_stock_container' ).slideDown();
			} else {
				$( '.required_stock_container' ).slideUp( 'slow' );
			}
		});
		
		
		//Required BOMS Items
		$('.required_boms').on('change', function() {
			var raReq = $("input[name='boms_required']:checked").val();
			if( raReq == 1 ){
				$( '.required_boms_container' ).slideDown();
			} else {
				$( '.required_boms_container' ).slideUp( 'slow' );
			}
		});
		
		//Evidoc Requirements 
		$('.evidoc_required').on('change', function() {
			var eviReq = $("input[name='evidoc_required']:checked").val();
			if( eviReq == 1 ){
				$( '.evidoc_required_container' ).slideDown();
			} else {
				$( '.evidoc_required_container' ).slideUp( 'slow' );
			}
		});
		
		//Checklists requirements
		$('.checklists_required').on('change', function() {
			var checkListReq = $("input[name='checklists_required']:checked").val();
			if( checkListReq == 1 ){
				$( '.checklists_required_container' ).slideDown();
			} else {
				$( '.checklists_required_container' ).slideUp( 'slow' );
			}
		});
		
		//NPS Requirements 
		$('.nps_required').on('change', function() {
			var npsReq = $("input[name='nps_required']:checked").val();
			if( npsReq == 1 ){
				$( '.nps_required_container' ).slideDown();
			} else {
				$( '.nps_required_container' ).slideUp( 'slow' );
			}
		});	
		
		//CSAT Requirements 
		$('.csat_required').on('change', function() {
			var csatReq = $("input[name='csat_required']:checked").val();
			if( csatReq == 1 ){
				$( '.csat_required_container' ).slideDown();
			} else {
				$( '.csat_required_container' ).slideUp( 'slow' );
			}
		});
		
		
		//Email Notification Required
		$('.notification_required').on('change', function() {
			var emailReq = $("input[name='notification_required']:checked").val();
			if( emailReq == 1 ){
				$( '.notification_required_container' ).slideDown();
			} else {
				$( '.notification_required_container' ).slideUp( 'slow' );
			}
		});
		
		//Clear red-bordered elements
		$( '.required' ).change( function(){
			$( this ).css("border","1px solid #ccc");
		});
		
		//Trigger search field
		$('[name="job_type"]').keyup(function(){
			$( this ).focus().css("border","1px solid #ccc");
			$('[name="job_type"]').val();
		});
		
		$(".job-type-creation-steps").click(function(){
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
			var changeto = ".job_type_creation_panel"+panelnumber;
			$( changefrom ).hide( "slide", {direction : 'left'}, 500);
			$( changeto ).delay(600).show( "slide", {direction : 'right'},500);	
			return false;	
		}
		
		function go_back( changefrom ){
			var panelnumber = parseInt( changefrom.match(/\d+/) )-parseInt(1);
			var changeto = ".job_type_creation_panel"+panelnumber;
			$( changefrom ).hide( "slide", {direction : 'right'}, 500);
			$( changeto ).delay(600).show( "slide", {direction : 'left'},500);	
			return false;	
		}
		
		//Submit Evidoc form
		$( '#create-job-type-btn' ).click(function( e ){
		
			e.preventDefault();
			
			submitJobTypeForm();
			
		});
		
		function submitJobTypeForm(){
			
			var formData = $('#job-type-creation-form').serialize();
			
			swal({
				title: 'Confirm Job Type creation?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function ( result ) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/create_job_type/'); ?>",
						method:'POST',
						data:formData,
						dataType: 'json',
						success:function( data ){
							if( data.status == 1 && ( data.job_type !== '' ) ){
								console.log(data);
								var alreadyExists= data.already_exists;
								var newJobTypeId = data.job_type.job_type_id;
								console.log(alreadyExists);
								if( alreadyExists ){
									var existUrl = "<?php echo base_url('webapp/job/job_types/'); ?>"+data.job_type.job_type_id;
									swal({
										type: 'warning',
										showCancelButton: true,
										confirmButtonColor: '#5CB85C',
										cancelButtonColor: '#9D1919',
										confirmButtonText: 'Override',
										title: 'This Job Type already exists!',
										html:
											'<b>Job Type: </b>' + ucwords( data.job_type.job_type ) + '<br/>' +
											//'<b>Category: </b>' + ucwords( data.job_type.category_name ) + '<br/>' +
											'<b>Description: </b><br/>' +
											'<em>' + data.job_type.job_type_desc + '</em>' + '<br/><br/>' +
											'Click <a href="'+existUrl+'" target="_blank">here</a> to view it or Cancel to go back and change name'
									}).then( function (result) {
										if ( result.value ) {
											//Do this if user accepts to Override
											$( '[name="job_type_id"]' ).val( data.job_type.job_type_id );
											$( '[name="override_existing"]' ).val( 1 );
											$( '[name="job_type_desc"]' ).val( data.job_type.job_type_desc );	
											
											//Do something here
											submitJobTypeForm();											
										}else{
											//Do this if user cancels to change the name
										}
									})
									
								} else {
									swal({
										type: 'success',
										title: data.status_msg,
										showConfirmButton: false,
										timer: 2000
									})
									window.setTimeout(function(){ 
										location.href = "<?php echo base_url('webapp/job/job_types/'); ?>"+newJobTypeId;
									} ,1000);
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
					$( ".asset_creation_panel8" ).hide( "slide", { direction : 'left' }, 500 );
					go_back( ".asset_creation_panel2" );
					return false;
				}
			}).catch( swal.noop )
		}
		
		//Trigger Category modal
		$( '.evidoc-category-quick-add' ).click( function(){
			$( '.add-category-modal' ).modal( 'show' );
		} );
		
		// New Category Quick add
		$( '#category-quick-add-btn' ).click(function(){

			var formData = $( "#add-category-form-container :input").serialize();
		
			$.ajax({
				url:"<?php echo base_url('webapp/audit/add_category/'); ?>",
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
						
						var optionExists  = ( $('#job_category_id option[value="' + categoryId + '"]').length > 0 );
						var optionExists2 = ( $('#category_id option[value="' + categoryId + '"]').length > 0 );

						if( !optionExists ){
							//Only add the new option if it doesn't already exist
							$('#job_category_id').append( $('<option >').val( categoryId ).text( categoryName + ' - ' + categoryDesc ) );
						}
						
						if( !optionExists2 ){
							//Only add the new option if it doesn't already exist
							$('#category_id').append( $('<option >').val( categoryId ).text( categoryName + ' - ' + categoryDesc ) );
						}
						
						//Set selected
						$('#job_category_id option[value="'+categoryId+'"]').prop( 'selected', true );
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