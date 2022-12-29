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
			<?php if (!empty($audit_details)) { ?>
			<div class="profile-details-container">
				<div class="row alert alert-ssid" role="alert">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<legend>EviDoc ID: <?php echo $audit_details->audit_id; ?></legend>
							<div class="rows">
								<div class="row profile_view">
									<div class="row col-sm-12">
										<div class="right col-xs-12">
											<table style="width:100%;">
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>EviDoc Type</label></td>
													<td width="60%"><a href="<?php echo base_url('webapp/audit/evidoc_names/'.$audit_details->audit_type_id); ?>"><?php echo html_escape(ucwords($audit_details->audit_type)); ?> <?php echo !empty($audit_details->audit_frequency) ? ' - '.ucwords($audit_details->audit_frequency) : ''; ?></a></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>EviDoc Status</label></td>
													<td width="60%"><?php echo html_escape(ucwords($audit_details->audit_status)); ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>EviDoc Created By</label></td>
													<td width="60%"><?php echo html_escape(ucwords($audit_details->created_by)); ?></td>
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
							<legend>EviDoc details</legend>
							<table class="table-full-width">
								<?php if (in_array(strtolower($audit_details->audit_group), ['asset'])) { ?>
									<?php /*
                                    <tr>
                                        <td width="30%"><i class="hide fa fa-briefcase"></i> <label>Asset Make and Model</label></td>
                                        <td width="60%"><a href="<?php echo base_url('/webapp/asset/profile/'.$audit_details->asset_id ); ?>"><?php echo ucwords($audit_details->asset_make); ?> <?php echo ucwords( $audit_details->asset_model ); ?></a></td>
                                    </tr>
                                    <tr>
                                        <td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Asset IMEI #</label></td>
                                        <td width="60%"><?php echo strtoupper( $audit_details->asset_imei_number ); ?></td>
                                    </tr> */ ?>
									<tr>
										<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Asset Unique Id</label></td>
										<td width="60%"><a href='<?php echo base_url('/webapp/asset/profile/'.$audit_details->asset_id); ?>'><?php echo html_escape(strtoupper($audit_details->asset_unique_id)); ?></a></td>
									</tr>
									<tr>
										<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Audit Outcome</label></td>
										<td width="60%"><?php echo ucwords($audit_details->result_status); ?></td>
									</tr>
									<?php if (!empty($audit_details->site_id) && !empty($audit_details->site_name)) { ?>
										<tr>
											<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Building Name</label></td>
											<td width="60%"><a href="<?php echo base_url('/webapp/site/profile/'.$audit_details->site_id); ?>"><?php echo html_escape(ucwords($audit_details->site_name)); ?></a></td>
										</tr>
									<?php } ?>
									
									<?php if (!empty($audit_details->job_id)) { ?>
										<tr>
											<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Job Details</label></td>
											<td width="60%"><a href="<?php echo base_url('/webapp/job/profile/'.$audit_details->job_id.'/evidocs'); ?>"><?php echo ucwords($audit_details->job_type); ?> (<?php echo ucwords($audit_details->job_id); ?>)</a></td>
										</tr>
									<?php } ?>
									
									
								<?php } elseif (in_array(strtolower($audit_details->audit_group), ['site'])) { ?>
									<tr>
										<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Building ID</label></td>
										<td width="60%"><a href="<?php echo base_url('/webapp/site/profile/'.$audit_details->site_id); ?>"><?php echo ucwords($audit_details->site_id); ?></a></td>
									</tr>
									<tr>
										<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Building Name</label></td>
										<td width="60%"><a href="<?php echo base_url('/webapp/site/profile/'.$audit_details->site_id); ?>"><?php echo html_escape(ucwords($audit_details->site_name)); ?></a></td>
									</tr>
									<tr>
										<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Building Postcode</label></td>
										<td width="60%"><?php echo ucwords($audit_details->site_postcodes); ?></td>
									</tr>
								<?php } elseif (in_array(strtolower($audit_details->audit_group), ['vehicle'])) { ?>
									<tr>
										<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Vehicle Reg</label></td>
										<td width="60%"><a href="<?php echo base_url('/webapp/fleet/profile/'.$audit_details->vehicle_id); ?>"><?php echo html_escape(ucwords($audit_details->vehicle_reg)); ?></a></td>
									</tr>
									<tr>
										<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Vehicle Make and Model</label></td>
										<td width="60%"><?php echo ucwords($audit_details->vehicle_make); ?> <?php echo html_escape(ucwords($audit_details->vehicle_model)); ?></td>
									</tr>
									<tr>
										<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Year</label></td>
										<td width="60%"><?php echo html_escape($audit_details->year); ?></td>
									</tr>
								<?php } ?>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="row">
				<?php if (!empty($this->user->tier_id) && ($this->user->tier_id > 0)) { ?>
					<?php $this->load->view('webapp/_partials/tabs_loader') ?>
					<?php include $include_page; ?>
				<?php } ?>
			</div>
			<?php } else { ?>
				<div class="row">
					<span><?php echo $this->config->item('no_records'); ?></span>
				</div>
			<?php } ?>
		</div>
	</div>
</div>

