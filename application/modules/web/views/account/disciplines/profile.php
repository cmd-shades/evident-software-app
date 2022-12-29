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
				<?php if (!empty($discipline_details)) { ?>
				<div class="row alert alert-ssid records-bar" role="alert">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="row">
							<legend>Discipline Profile</legend>
							<div class="row">
								<div class="col-md-4 col-sm-4 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Date Created</label></td>
											<td width="85%"><?php echo (valid_date($discipline_details->created_on)) ? date('d-m-Y H:i:s', strtotime($discipline_details->created_on)) : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Created By</label></td>
											<td width="85%"><?php echo (!empty($discipline_details->created_by)) ? ucwords($discipline_details->created_by) : 'Data not available'; ?></td>
										</tr>
									</table>
								</div>
								<div class="col-md-4 col-sm-4 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Linked Accounts</label></td>
											<td width="85%"><?php echo !empty($associated_job_types) ? count($associated_job_types, 1) : 0; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Status</label></td>
											<td width="85%"><?php echo ($discipline_details->is_active == 1) ? 'Active <i class="far fa-check-circle"></i>' : 'Disabled <i class="far fa-times-circle text-red"></i>'; ?></td>
										</tr>
									</table>
								</div>
								
								<div class="col-md-4 col-sm-4 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label><strong>&nbsp;</strong></label></td>
											<td width="85%">
												<span class="pull-right" ><img width="100px;" src="<?php echo $discipline_details->discipline_image_url; ?>" /></span>
											</td>
										</tr>
									</table>
								</div>
								
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="row">
				<div class="row">
					<?php $this->load->view('webapp/_partials/tabs_loader') ?>
				</div>
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