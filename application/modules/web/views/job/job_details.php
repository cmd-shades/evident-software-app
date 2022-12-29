<div class="row">
	<div class="col-md-8 col-sm-8 col-xs-12">
		<form id="update-job-form" class="form-horizontal">
			<input type="hidden" name="page" value="details" />
			<input type="hidden" name="job_id" value="<?php echo $job_details->job_id; ?>" />
			<input type="hidden" name="site_id" value="<?php echo (!empty($job_details->site_id)) ? $job_details->site_id : ''; ?>" />
			<input type="hidden" name="customer_id" value="<?php echo (!empty($job_details->customer_id)) ? $job_details->customer_id : ''; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<input type="hidden" name="address_id" value="<?php echo (!empty($job_details->address_id)) ? $job_details->address_id : null; ?>" />
			<input type="hidden" name="external_job_ref" value="<?php echo (!empty($job_details->external_job_ref)) ? $job_details->external_job_ref : null; ?>" />
			
			<div class="x_panel tile has-shadow">
				<legend>Update Job Details <span class="pointer pull-right"><span class="<?php echo (in_array($this->user->account_id, TESSERACT_LINKED_ACCOUNTS)) ? '' : 'hide'; ?>"><a data-call_number="<?php echo !empty($job_details->external_job_ref) ? $job_details->external_job_ref : ''; ?>" id="refresh-tess-job"><i class="fas fa-sync-alt"></i> Refresh</a></span> &nbsp;<span style="display:<?php echo (empty($job_details->assigned_to) || !valid_date($job_details->job_date) || !$job_details->external_job_ref) ? 'inline-block' : 'none'; ?>" title="Click to route this Job"><a class="route-job pointer" ><i class="fas fa-road"></i></a></span></span></legend>

				<div class="row" >
					<div class="col-md-6 col-sm-6 col-xs-12">
						
						<?php if (!empty($this->user->is_primary_user) && !$this->user->is_admin) { ?>
							<div class="input-group form-group">
								<label class="input-group-addon">Job type</label>
								<input id="job_type_id" name="job_type_id" type="hidden" value="<?php echo (!empty($job_details->job_type_id)) ? $job_details->job_type_id : '' ?>" />
								<input class="form-control" readonly type="text" placeholder="Job Type" value="<?php echo (!empty($job_details->job_type)) ? $job_details->job_type : '' ?>" />
							</div>
						<?php } else { ?>
							<div class="input-group form-group">
								<label class="input-group-addon">Job type</label>
								<select id="job_type_id" name="job_type_id" class="form-control">
									<option value="">Please select</option>
									<?php if (!empty($job_types)) {
									    foreach ($job_types as $k => $job_type) { ?>
										<option value="<?php echo $job_type->job_type_id; ?>" <?php echo ($job_details->job_type_id == $job_type->job_type_id) ? 'selected=selected' : ''; ?> ><?php echo $job_type->job_type; ?></option>
									<?php }
									    } ?>
								</select>
							</div>
						<?php } ?>

						<?php if ($this->user->is_admin || !in_array($this->user->id, $restricted_people) || $this->user->is_primary_user) { ?>
							<div class="input-group form-group">
								<label class="input-group-addon">Job Date</label>
								<input name="job_date" class="form-control datepicker" type="text" placeholder="Job date" value="<?php echo (valid_date($job_details->job_date)) ? date('d-m-Y', strtotime($job_details->job_date)) : ''; ?>" />
							</div>

							<div class="input-group form-group">
								<label class="input-group-addon">Job Assignee</label>
								<select id="assigned_to" name="assigned_to" class="form-control">
									<option value="" >Please select</option>
									<?php if (!empty($operatives)) {
									    foreach ($operatives as $k => $operative) { ?>
										<option value="<?php echo $operative->id; ?>" <?php echo ($operative->id == $job_details->assigned_to) ? 'selected=selected' : ''; ?> ><?php echo $operative->first_name." ".$operative->last_name; ?></option>
									<?php }
									    } ?>
								</select>
							</div>
							
							<?php if ($this->user->is_admin) { ?>
								<div class="input-group form-group">
									<label class="input-group-addon">Second Assignee</label>
									<select id="second_assignee_id" name="second_assignee_id" class="form-control">
										<option value="" >Please select</option>
										<?php if (!empty($operatives)) {
										    foreach ($operatives as $k => $operative) { ?>
											<option value="<?php echo $operative->id; ?>" <?php echo ($operative->id == $job_details->second_assignee_id) ? 'selected=selected' : ''; ?> ><?php echo $operative->first_name." ".$operative->last_name; ?></option>
										<?php }
										    } ?>
									</select>
								</div>
							<?php } ?>
							
						<?php } else { ?>
							<div class="input-group form-group">
								<label class="input-group-addon">Job Date</label>
								<input class="form-control" type="text" placeholder="Job date" readonly value="<?php echo (valid_date($job_details->job_date)) ? date('d-m-Y', strtotime($job_details->job_date)) : 'Call pending'; ?>" />
							</div>
						<?php } ?>

						<div class="input-group form-group">
							<label class="input-group-addon">Job Status</label>
							<select id="status_id" name="status_id" class="form-control">
								<option value="">Please select</option>
								<?php if (!empty($job_statuses)) {
								    foreach ($job_statuses as $k => $job_status) { ?>
									<option value="<?php echo $job_status->status_id; ?>" data-status_group="<?php echo $job_status->status_group; ?>" <?php echo ($job_details->status_id == $job_status->status_id) ? 'selected=selected' : ''; ?> ><?php echo $job_status->job_status; ?></option>
								<?php }
								    } ?>
							</select>
						</div>
						
						<div class="input-group form-group">
							<label class="input-group-addon">Priority Rating</label>
							<select id="job_priority_rating" name="job_priority_rating" class="form-control">
								<option value="">Please select</option>
								<?php if (!empty($priority_ratings)) {
								    foreach ($priority_ratings as $rating) { ?>
									<option value="<?php echo $rating; ?>" <?php echo (strtolower($job_details->job_priority_rating) == strtolower($rating)) ? 'selected=selected' : ''; ?> ><?php echo ucwords(strtolower($rating)); ?></option>
								<?php }
								    } ?>
							</select>
						</div>

						<?php if ($this->user->is_admin || !empty($permissions->is_admin) || !empty($tab_permissions->can_edit)) { ?>
							<div class="input-group form-group">
								<label class="input-group-addon">Tracking Status</label>
								<select id="job_tracking_id" name="job_tracking_id" class="form-control" >
									<option value="">Please select</option>
									<?php if (!empty($tracking_statuses)) {
									    foreach ($tracking_statuses as $k => $tracking_status) { ?>
										<option value="<?php echo $tracking_status->job_tracking_id; ?>" <?php echo ($job_details->job_tracking_id == $tracking_status->job_tracking_id) ? 'selected=selected' : ''; ?> ><?php echo $tracking_status->job_tracking_status; ?></option>
									<?php }
									    } ?>
								</select>
							</div>
						<?php } ?>
						
						<?php if ($this->user->is_admin || !empty($permissions->is_admin)) { ?>
							<div class="input-group form-group">
								<label class="input-group-addon">Assigned Region</label>
								<select id="region_id" name="region_id" class="form-control" >
									<option value="">Please select</option>
									<?php if (!empty($postcode_regions)) {
									    foreach ($postcode_regions as $k => $region) { ?>
										<option value="<?php echo $region->region_id; ?>" <?php echo ($job_details->region_id == $region->region_id) ? 'selected=selected' : ''; ?> ><?php echo $region->region_name; ?></option>
									<?php }
									    } ?>
								</select>
							</div>
							<?php if (empty($job_details->region_name)) { ?>
								<div>
									<p class="text-red text-bold">This Job Postcode is not part of any Region. <br>Please Click <a href="<?php echo base_url('/webapp/diary/manage_regions'); ?>" target="_blank">HERE</a> to assign it to a <a href="<?php echo base_url('/webapp/diary/manage_regions'); ?>" target="_blank">Region</a> and then come update this Job record.</p>
								</div>
							<?php } ?>
						<?php } else { ?>
						
							<input type="hidden" name="region_id" value="<?php echo (!empty($job_details->region_id)) ? $job_details->region_id : '' ; ?>" />
						
							<div class="input-group form-group">
								<label class="input-group-addon">Assigned Region</label>
								<input class="form-control" readonly type="text" placeholder="Assigned Region" value="<?php echo (!empty($job_details->region_name)) ? $job_details->region_name : 'NOT SET' ?>" />
							</div>
						<?php } ?>

						<div class="input-group form-group">
							<label class="input-group-addon">Job Duration (Slots)</label>
							<select name="job_duration" class="form-control" >
								<option>Please select</option>
								<?php if (!empty($job_durations)) {
								    foreach ($job_durations as $k => $duration) { ?>
									<option value="<?php echo $k; ?>" <?php echo ($k == $job_details->job_duration) ? 'selected="selected"' : (($k == $job_details->job_base_duration) ? 'selected="selected"' : '') ?> ><?php echo $duration; ?></option>
								<?php }
								    } ?>
							</select>
						</div>

						<div class="hide input-group form-group">
							<label class="input-group-addon">ETA</label>
							<textarea name="eta" type="text" class="form-control" rows="2"><?php echo (!empty($job_details->eta)) ? $job_details->eta : '' ?></textarea>
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">ETA Status</label>
							<select class="form-control" <?php echo ($this->user->is_admin || !empty($permissions->is_admin) || !empty($tab_permissions->is_admin)) ? 'name="eta_status"' : 'readonly'; ?> >
								<option value="" >Please select</option>
								<?php if (!empty($eta_statuses)) {
								    foreach ($eta_statuses as $k => $eta_status) { ?>
									<option <?php echo ($this->user->is_admin || !empty($permissions->is_admin) || !empty($tab_permissions->is_admin)) ? '' : 'disabled'; ?> value="<?php echo $eta_status->status_name; ?>" <?php echo (strtolower($eta_status->status_name) == strtolower($job_details->eta_status)) ? 'selected="selected"' : ''; ?> ><?php echo $eta_status->status_name; ?></option>
								<?php }
								    } ?>
							</select>
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">ETA Window From</label>
							<input <?php echo ($this->user->is_admin || !empty($permissions->is_admin) || !empty($tab_permissions->is_admin)) ? 'name="eta_window_from"' : 'readonly'; ?>  class="form-control" type="text" placeholder="ETA Window From" value="<?php echo (!empty($job_details->eta_window_from)) ? $job_details->eta_window_from : ''; ?>" />
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">ETA Window To</label>
							<input <?php echo ($this->user->is_admin || !empty($permissions->is_admin) || !empty($tab_permissions->is_admin)) ? 'name="eta_window_to"' : 'readonly'; ?> class="form-control" type="text" placeholder="ETA Window To" value="<?php echo (!empty($job_details->eta_window_to)) ? $job_details->eta_window_to : ''; ?>" />
						</div>

						<?php //if( $job_details->status_group == 'failed' ){?>
							<div class="failure-details" style="display:none" >
								<div class="input-group form-group">
									<label class="input-group-addon">Fail Code</label>
									<select id="fail_code_id" name="fail_code_id" class="form-control">
										<option value="" >Please select</option>
										<?php if (!empty($fail_codes)) {
										    foreach ($fail_codes as $k => $fail_code) { ?>
											<option value="<?php echo $fail_code->fail_code_id; ?>" <?php echo ($fail_code->fail_code_id == $job_details->fail_code_id) ? 'selected=selected' : ''; ?> data-fail_code_desc="<?php echo $fail_code->fail_code_desc; ?>" ><?php echo $fail_code->fail_code_text; ?></option>
										<?php }
										    } ?>
									</select>
								</div>
								<div class="input-group form-group">
									<label class="input-group-addon">Fail Reason</label>
									<textarea id="fail_code_desc" type="text" class="form-control" rows="1" readonly ></textarea>
								</div>
								<div class="input-group form-group">
									<label class="input-group-addon">Fail Notes</label>
									<textarea name="fail_notes" type="text" class="form-control" rows="3"><?php echo (!empty($job_details->fail_notes)) ? $job_details->fail_notes : '' ?></textarea>
								</div>
							</div>
						<?php //}?>
						
						<div id="job_notes" style="display:block" class="form-group" >
							<div class="input-group form-group">
								<label class="input-group-addon">Job Notes</label>
								<textarea name="job_notes" type="text" class="form-control has-error" rows="4" placeholder="Last Note: <?php echo (!empty($job_details->job_notes)) ? $job_details->job_notes : ''; ?>" ></textarea>
							</div>
							<div style="margin-top:-10px;" ><span id="job_notes-errors" class="text-red"></span></div>
						</div>
						
						<?php if (!empty($job_details->activity_name)) { ?>
							<div class="input-group form-group">
								<label class="input-group-addon">Scheduled Activity</label>
								<input readonly class="form-control" type="text" placeholder="Scheduled Activity" value="<?php echo (!empty($job_details->activity_name)) ? $job_details->activity_name : ''; ?>" />
							</div>
							<!-- 
							<div class="input-group form-group">
								<label class="input-group-addon">Activity Due Date</label>
								<input readonly class="form-control" type="text" placeholder="Activity Due Date" value="<?php echo (!empty($job_details->activity_due_date)) ? $job_details->activity_due_date : ''; ?>" />
							</div> -->							
							<div class="input-group form-group">
								<label class="input-group-addon">Activity Status</label>
								<input readonly class="form-control" type="text" placeholder="Activity Status" value="<?php echo (!empty($job_details->activity_status)) ? $job_details->activity_status : ''; ?>" />
							</div>
						<?php } ?>

						<?php if (!empty($job_details->site_id)) { ?>
							<div class="input-group form-group">
								<label class="input-group-addon">Building QR Code Location</label>
								<input name="qr_code_location" class="form-control" type="text" placeholder="QR Code Location" value="<?php echo $job_details->qr_code_location; ?>" />
							</div>
						<?php } ?>
						
					</div>

					<div class="col-md-6 col-sm-6 col-xs-12">

						<div class="input-group form-group">
							<label class="input-group-addon">Job Start Time</label>
							<input class="form-control" type="text" placeholder="Job Start Time" readonly value="<?php echo (!empty($job_details->start_time)) ? date('d-m-Y H:i:s', strtotime($job_details->start_time)) : ''; ?>" />
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">Job Finish Time</label>
							<input class="form-control" type="text" placeholder="Job Finish Time" readonly value="<?php echo (!empty($job_details->finish_time)) ? date('d-m-Y H:i:s', strtotime($job_details->finish_time)) : ''; ?>" />
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">Client Reference</label>
							<input name="client_reference" class="form-control" type="text" placeholder="Client Reference" value="<?php echo !empty($job_details->client_reference) ? $job_details->client_reference : ''; ?>" />
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">Damage To Property?</label>
							<select id="damage_to_property" name="damage_to_property" class="form-control" data-label_text="Damage to Property" >
								<option >Please select</option>
								<option value="1" <?php echo ($job_details->damage_to_property == 1) ? 'selected=selected' : ''; ?> >Yes</option>
								<option value="0" <?php echo ($job_details->damage_to_property != 1) ? 'selected=selected' : ''; ?> >No</option>
							</select>
						</div>
							
						<?php if ($job_details->damage_to_property == 1) { ?>
							<div class="input-group form-group">
								<label class="input-group-addon">Details of Damage</label>
								<textarea name="details_of_damage" type="text" class="form-control" rows="4"><?php echo (!empty($job_details->details_of_damage)) ? $job_details->details_of_damage : '' ?></textarea>
							</div>
						<?php } ?>

						<div class="input-group form-group">
							<label class="input-group-addon">Works Required / Notes</label>
							<textarea name="works_required" type="text" class="form-control" rows="3"><?php echo (!empty($job_details->works_required)) ? $job_details->works_required : '' ?></textarea>
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">Completed Works</label>
							<textarea <?php echo (!empty($this->user->is_admin)) ? 'name="completed_works"' : 'readonly'; ?> type="text" class="form-control" rows="3"><?php echo (!empty($job_details->completed_works)) ? $job_details->completed_works : '' ?></textarea>
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">Access Requirements</label>
							<textarea name="access_requirements" type="text" class="form-control" rows="3"><?php echo (!empty($job_details->access_requirements)) ? $job_details->access_requirements : '' ?></textarea>
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">Parking Requirements</label>
							<textarea name="parking_requirements" type="text" class="form-control" rows="3"><?php echo (!empty($job_details->parking_requirements)) ? $job_details->parking_requirements : '' ?></textarea>
						</div>

						<div class="input-group form-group">
							<label class="input-group-addon">Special Instructions</label>
							<textarea name="special_instructions" type="text" class="form-control" rows="3"><?php echo (!empty($job_details->special_instructions)) ? $job_details->special_instructions : '' ?></textarea>
						</div>
						
						<?php if (in_array($this->user->account_id, TESSERACT_LINKED_ACCOUNTS)) { ?>
						<div class="tesseract-call-details">
							<h4><span style="font-weight:600">TESSERACT INFORMATION</span></h4>
							<div class="input-group form-group">
								<label class="input-group-addon">Tesseract Call Num</label>
								<?php if ($this->user->is_admin || !empty($permissions->can_edit) || !empty($permissions->is_admin)) { ?>
									<input name="external_job_ref" class="form-control" type="text" placeholder="3rd Party/External Job Ref" value="<?php echo !empty($job_details->external_job_ref) ? $job_details->external_job_ref : ''; ?>" />
								<?php } else {?>
									<input readonly class="form-control" type="text" placeholder="3rd Party/External Job Ref" value="<?php echo !empty($job_details->external_job_ref) ? $job_details->external_job_ref : ''; ?>" />
								<?php } ?>
							</div>
							
							<div class="input-group form-group">
								<label class="input-group-addon">Last Refreshed By</label>
								<input class="form-control" type="text" placeholder="Last updated" value="<?php echo (!empty($job_details->last_modified_by) && (valid_date($job_details->external_job_updated_on))) ? ucwords($job_details->last_modified_by) : ''; ?> <?php echo (valid_date($job_details->external_job_updated_on)) ? ' @ '.date('d-m-Y H:i:s', strtotime($job_details->external_job_updated_on)) : ''; ?>" readonly />
							</div>
							
							<div class="input-group form-group">
								<label class="input-group-addon">Call Status</label>
								<input readonly name="external_job_call_status" class="form-control" type="text" placeholder="Tesseract Call Status" value="<?php echo !empty($job_details->external_job_call_status) ? $job_details->external_job_call_status : ''; ?>" />
							</div>
							<!-- 
							<div class="input-group form-group">
								<label class="input-group-addon">Fault Code</label>
								<input readonly name="fault_code" class="form-control" type="text" placeholder="Fault code" value="<?php echo !empty($job_details->fault_code) ? $job_details->fault_code : ''; ?>" />
							</div>
							
							<div class="input-group form-group">
								<label class="input-group-addon">Symptom Code</label>
								<input readonly name="symptom_code" class="form-control" type="text" placeholder="Symptom code" value="<?php echo !empty($job_details->symptom_code) ? $job_details->symptom_code : ''; ?>" />
							</div>
							
							<div class="input-group form-group">
								<label class="input-group-addon">Repair Code</label>
								<input readonly name="repair_code" class="form-control" type="text" placeholder="Repair code" value="<?php echo !empty($job_details->repair_code) ? $job_details->repair_code : ''; ?>" />
							</div>	-->					
						</div>
						<?php } ?>
					</div>
				</div>
				
				<?php if ($this->user->is_admin || !empty($permissions->can_edit) || !empty($permissions->is_admin)) { ?>
					<div class="row">
						<div class="col-md-3">
							<button id="update-job-btn" class="update-job-btn btn btn-sm btn-block btn-flow btn-success btn-next" type="button" >Update Job Details</button>
						</div>
						<?php if ($this->user->is_admin || !empty($permissions->can_delete) || !empty($permissions->is_admin)) { ?>
							<div class="col-md-3">
								<button class="btn btn-sm btn-block btn-flow btn-danger has-shadow delete-job-btn" type="button" data-job_id="<?php echo $job_details->job_id; ?>">Delete Job</button>
							</div>
						<?php } ?>
					</div>
				<?php } else { ?>
					<div class="row col-md-3">
						<button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-warning btn-next no-permissions" type="button" disabled >Insufficient permissions</button>
					</div>
				<?php } ?>
				
			</div>
		</form>
	</div>

	<div class="col-md-4 col-sm-4 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Job GPS Location</legend>
			<table style="width:100%">
				<!-- <tr>
					<th colspan="1">Job Start Time&nbsp;: <?php echo (!empty($job_details->start_time)) ? $job_details->start_time : ' - - -'; ?></th>
					<th colspan="1">Job Finish Time: <?php echo (!empty($job_details->finish_time)) ? $job_details->finish_time : ((!empty($job_details->gps_latitude)) ? $job_details->gps_latitude : ' - - -'); ?></th>
				</tr> -->
				<tr>
					<th colspan="2">GPS Coordinates</th>
				</tr>
				<tr>
					<th colspan="1">START: &nbsp; <?php echo (!empty($job_details->gps_latitude)) ? $job_details->gps_latitude.',' : ' - - -'; ?> <?php echo (!empty($job_details->gps_longitude)) ? $job_details->gps_longitude : ' - - - '; ?></th>
					<th colspan="1">FINISH: &nbsp; <?php echo (!empty($job_details->finish_gps_latitude)) ? $job_details->finish_gps_latitude.',' : ' - - -'; ?> <?php echo (!empty($job_details->finish_gps_longitude)) ? $job_details->finish_gps_longitude : ' - - - '; ?></th>
				</tr>
				<tr>
					<th width="50%"></th><td width="50%"></td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="full-width">
							<?php if (!empty($job_details->gps_latitude) && !empty($job_details->gps_longitude)) { ?>
								<!-- <iframe width="100%" height="283" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src='https://www.google.com/maps/embed/v1/view?key=<?php echo GOOGLE_API_KEY; ?>&center=<?php echo $job_details->gps_latitude; ?>,<?php echo $job_details->gps_longitude; ?>&zoom=17&maptype=roadmap' ></iframe> -->
								<iframe width="100%" height="445" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?q=<?php echo $job_details->gps_latitude; ?>,<?php echo $job_details->gps_longitude; ?>&key=<?php echo GOOGLE_API_KEY; ?>"></iframe>
								<!-- <iframe width="100%" height="283" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.googleapis.com/maps/api/staticmap?zoom=18&size=400x400&markers=color:blue%7Clabel:S%7C51.324034,-0.172468&markers=size:tiny%7Ccolor:green&key=AIzaSyCYdFFWPQ5XLKAv4nDywPT6SXootbK6NIY" ></iframe> -->
						<?php 	} else {
						    ## check if there is a 'main' address added
						    $main_address_found = false;
						    if (!empty($job_details->customer_details->addresses)) {
						        foreach ($job_details->customer_details->addresses as $address) {
						            if (strtolower($address->address_type_group) == "main") { ?>
												<iframe width="100%" height="445" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%&height=280&hl=en&q=<?php echo (!empty($address->address_postcode)) ? str_replace(" ", "+", $address->address_postcode) : "" ; ?>&ie=UTF8&t=&z=16&iwloc=B&output=embed&zoom=1"></iframe>
									<?php   	$main_address_found = true;
						            }
						        }
						    } ?>

							<?php 	if (!$main_address_found) { ?>
										<iframe width="100%" height="445" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%&height=280&hl=en&q=<?php echo (!empty($job_details->address_postcode)) ? str_replace(" ", "+", $job_details->address_postcode) : "" ; ?>&ie=UTF8&t=&z=16&iwloc=B&output=embed&zoom=1"></iframe>
							<?php 	}
						} ?>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>

<?php include('inc/route_single_job.php');?>

<script>
	$( document ).ready(function(){
		
		$( '#refresh-tess-job' ).click( function( e ){
			
			e.preventDefault();
			var callNumber = $(this).data( 'call_number' );
			
			if( !callNumber ){
				swal({
					title: 'Invalid Job Type for this operation',
					type: 'warning'
				});
				return false;
			}

			swal({
				title:'Confirm Job Refresh?',
				html: 'Some fields will be overrudeb with details from Tesseract',
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/job/refresh_evident_job/'.$job_details->external_job_ref); ?>",
						method:"POST",
						data:{'page':'details', call_number:callNumber},
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 1500
								})
								window.setTimeout(function(){
									location.reload();
								} ,1500);
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
			
		});
		
		
		//$( '#route-single-job-modal' ).modal( 'show' );
		$( '.route-job' ).click( function(){
			$( '#route-single-job-modal' ).modal( 'show' );
		});
	
		var selectedStatusGrp 	= $( '#status_id option:selected' ).data( 'status_group' );
		if( selectedStatusGrp == 'failed' ||  selectedStatusGrp == 'cancelled'  ){
			$( '.failure-details' ).slideDown();
		} else {
			$( '.failure-details' ).slideUp( 'fast' );
		}
		

		$( '#fail_code_desc' ).val( $( '#fail_code_id option:selected' ).data( 'fail_code_desc' ) );
		
		$( '#fail_code_id' ).change( function() {
			var failDesc = $( 'option:selected', this ).data( 'fail_code_desc' );
			$( '#fail_code_desc' ).val( failDesc );
		});

		var currentStatus 			= $( '#status_id option:selected' ).val();
		var currentAssignee 		= $( '#assigned_to option:selected' ).val();
		$( '#status_id' ).change( function() {
			var selectedStatus 		= $( 'option:selected', this ).val();
			var selectedStatusGrp 	= $( 'option:selected', this ).data( 'status_group' );

			$('#assigned_to option[value="'+currentAssignee+'"]').prop( 'selected', true );
			
			if( selectedStatusGrp == 'failed' ||  selectedStatusGrp == 'cancelled'  ){
				$( '.failure-details' ).slideDown();
			} else {
				$( '.failure-details' ).slideUp( 'fast' );
			}
			
			if( currentStatus !== selectedStatus ){

				if( selectedStatus == "2" && ( currentAssignee.length > 0 ) ){
					$( '#assigned_to option[value=""]' ).prop( 'selected', true );
				}
				
				$( '#job_notes' ).slideDown( 'slow' );
				$( '[name="job_notes"]' ).prop( 'required', true );
				$( '[name="job_notes"]' ).addClass( 'required' );
			} else {
				$( '#job_notes' ).slideUp( 'fast' );
				$( '[name="job_notes"]' ).prop( 'required', false );
				$( '[name="job_notes"]' ).removeClass( 'required' );
			}
		});
		
		var currentTrackingStatus 	= $( '#job_tracking_id option:selected' ).val();		
		$( '#job_tracking_id' ).change( function() {
			var selectedTrackinStatus = $( 'option:selected', this ).val();
			if( currentTrackingStatus !== selectedTrackinStatus ){
				$( '#job_notes' ).slideDown( 'slow' );
				$( '[name="job_notes"]' ).prop( 'required', true );
				$( '[name="job_notes"]' ).addClass( 'required' );
			} else {
				$( '#job_notes' ).slideUp( 'fast' );
				$( '[name="job_notes"]' ).prop( 'required', false );
				$( '[name="job_notes"]' ).removeClass( 'required' );
			}
		});

		$( '.timepicker' ).datetimepicker({
			datepicker : false,
			ampm: true,
			format : 'H:i A'
		});
		
		
	});
</script>