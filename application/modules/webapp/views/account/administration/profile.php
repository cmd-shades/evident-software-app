<script src="<?php echo base_url( "assets/js/custom/infiscroll.js" ); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url("assets/css/custom/infiscroll.css"); ?>">
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
				<?php if( !empty( $account_details ) ) { ?>
				<div class="row alert alert-ssid bg-blue" role="alert">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<legend>Account Details</legend>
							<div class="rows">
								<div class="row profile_view">
									<div class="row col-sm-12">
										<div class="col-xs-12">
											<table style="width:100%;">
												<tr>
													<td width="30%"><i class="hide fa fa-user"></i> <label>Account Name</label></td>
													<td><?php echo ucwords($account_details->account_name); ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Account Email</label></td>
													<td width="60%"><?php echo $account_details->account_email; ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-user"></i> <label>Account Mobile</label></td>
													<td><?php echo ucwords( $account_details->account_mobile ); ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Account Telephone</label></td>
													<td width="60%"><?php echo $account_details->account_telephone; ?></td>
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
							<legend>&nbsp;</legend>
							<table style="width:100%;">
								<tr>
									<td width="30%"><i class="hide fa fa-user"></i> <label>Membership Number</label></td>
									<td><?php echo strtoupper( $account_details->account_membership_number ); ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Account Status</label></td>
									<td width="60%"><?php echo $account_details->account_status; ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-user"></i> <label>Business Size</label></td>
									<td><?php echo ucwords( $account_details->organisation_size ); ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Business Area</label></td>
									<td width="60%"><?php echo $account_details->organisation_area; ?></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			
			<?php include 'administration_tabs.php';  ?>

				<div class="row">
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

