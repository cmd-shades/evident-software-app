<style>
	table tr th,table tr td{
		padding: 5px 0; 
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
			<div class="profile-details-container">
				<?php if (!empty($site_details)) { ?>
				<div class="row alert alert-ssid records-bar" role="alert">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<legend>Site Details</legend>
							<div class="rows">
								<div class="row profile_view">
									<div class="row col-sm-12">
										<div class="col-xs-12">
											<table style="width:100%;">
												<tr>
													<td width="30%"><i class="hide fa fa-user"></i> <label>Site Name</label></td>
													<td><?php echo ucwords($site_details->site_name); ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Address</label></td>
													<td width="60%"><?php echo $site_details->summaryline; ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Total Dwellings</label></td>
													<td width="60%"><?php echo ($site_details->total_dwellings > 0) ? strtoupper($site_details->total_dwellings) : (!empty($existing_dwellings) ? count($existing_dwellings) : 0); ?></td>
												</tr> 
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Number of Floors</label></td>
													<td width="60%"><?php echo strtoupper($site_details->number_of_floors); ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Site Reference</label></td>
													<td width="60%"><?php echo strtoupper($site_details->site_reference); ?></td>
												</tr>
											</table>
										</div>		
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<legend>Site Overview</legend>
							<table style="width:100%;">
								<tr>
									<td width="40%"><i class="hide fa fa-user"></i> <label>Site Status</label></td>
									<td width="60%"><?php echo ucwords($site_details->status_name); ?></td>
								</tr>
								<tr>
									<td width="40%"><i class="hide fa fa-user"></i> <label>Connected Panels</label></td>
									<td width="60%"><?php echo (!empty($site_panels)) ? count(object_to_array($site_panels)) : 0; ?></td>
								</tr>
								<tr>
									<td width="40%"><i class="hide fa fa-user"></i> <label>Event Tracking Status</label></td>
									<td width="60%"><?php echo ucwords($site_details->event_tracking_status); ?></td>
								</tr>
								<tr>
									<td width="40%"><i class="hide fa fa-user"></i> <label>Last Audit Date</label></td>
									<td width="60%"><?php echo (valid_date($site_details->last_audit_date)) ? date('d-m-Y H:i:s', strtotime($site_details->last_audit_date)) : ''; ?></td>
								</tr>
								<tr>
									<td width="40%"><i class="hide fa fa-user"></i> <label>Next Audit Date</label></td>
									<td width="60%"><?php echo (valid_date($site_details->next_audit_date)) ? date('d-m-Y H:i:s', strtotime($site_details->next_audit_date)) : ''; ?></td>
								</tr>
								<!-- <tr>
									<td width="30%"><i class="hide fa fa-user"></i> <label>Address line 1</label></td>
									<td><?php echo ucwords($site_details->site_name); ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Address line 2</label></td>
									<td width="60%"><?php echo $site_details->summaryline; ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Address Town</label></td>
									<td width="60%"><?php echo strtoupper($site_details->site_reference); ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Postcode(s)</label></td>
									<td width="60%"><?php echo strtoupper($site_details->site_reference); ?></td>
								</tr> -->
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="row">
				<?php $this->load->view('webapp/_partials/tabs_loader') ?>
				<?php include $include_page; ?>
			</div>
			<?php } else { ?>
				<div class="row">
					<span><?php echo $this->config->item('no_records'); ?></span>
				</div>
			<?php } ?>			
		</div>
	</div>
</div>

