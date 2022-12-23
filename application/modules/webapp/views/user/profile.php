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
<!-- class="row records-bar panel-primary" -->
<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<!-- <div class="profile-banner"> -->
			<div class="profile-details-container">
				<?php if( !empty($user_details) ) { ?>
				<!-- <div class="row alert alert-ssid records-bar" role="alert"> -->
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<div class="rows">
							<h2>Personal Info</h2>
						</div>
					</div>
				</div>
				<!-- <div class="row profile_view"> -->
				<div class="row records-bar panel-primary">
					<div class="row">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<div class="row">
								<table style="width:100%;">
									<tr>
										<td width="40%"><i class="hide fa fa-user"></i> <label>Full Name</label></td>
										<td><?php echo ucwords($user_details->first_name); ?> <?php echo ucwords($user_details->last_name); ?></td>
									</tr>
									<tr class="hide">
										<td width="40%"><i class="hide fa fa-briefcase"></i> <label>Employee ID</label></td>
										<td width="60%"><?php echo $user_details->account_user_id; ?></td>
									</tr>
									<tr>
										<td width="40%"><i class="hide fa fa-at text-bold"></i> <label>User Email</label></td>
										<td width="60%"><?php echo strtolower($user_details->email); ?></td>
									</tr>
									<tr>
										<td width="40%"><i class="hide fa fa-phone"></i> <label>User Mobile</label></td>
										<td width="70%"><?php echo ( !empty( $user_details->phone ) ) ? $user_details->phone : '- - -  - - - '; ?></td>
									</tr>
									<tr>
										<td width="30%"><i class="hide fa fa-lock"></i> <label>Username</label></td>
										<td width="60%"><?php echo $user_details->username; ?></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="row">&nbsp;</div>
				</div>
				<div class="clearfix"></div>
				<div class="row">
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<?php $this->load->view('webapp/_partials/tabs_loader') ?>
					</div>
				</div>
				</div>
				<div>&nbsp;</div>
				<?php include $include_page; ?>
			</div>
			<?php }else{ ?>
				<div class="row">
					<span><?php echo $this->config->item('no_records'); ?></span>
				</div>
			<?php } ?>
		</div>
	</div>
</div>

