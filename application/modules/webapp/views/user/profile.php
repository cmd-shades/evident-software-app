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
			<div class="profile-banner">
				<?php if( !empty($user_details) ) { ?>
				<div class="row alert alert-ssid records-bar" role="alert">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<legend>Personal Info</legend>
							<div class="rows">
								<div class="row profile_view">
									<div class="row col-sm-12">
										<div class="col-xs-12">
											<table style="width:100%;">
												<tr>
													<td width="40%"><i class="hide fa fa-user"></i> <label>Full Name</label></td>
													<td width="60%"><a href="<?php echo base_url('webapp/people/profile/'.$user_details->id ); ?>" title="Click to view the person profile for <?php echo ucwords( $user_details->first_name.' '.$user_details->last_name ); ?>" ><?php echo ucwords( $user_details->first_name.' '.$user_details->last_name ); ?></a></td>
												</tr>
												<tr class="hide">
													<td width="40%"><i class="hide fa fa-briefcase"></i> <label>Employee ID</label></td>
													<td width="60%"><?php echo $user_details->account_user_id; ?></td>
												</tr>
												<tr>
													<td width="40%"><i class="hide fa fa-at text-bold"></i> <label>Email</label></td>
													<td width="60%"><?php echo strtolower($user_details->email); ?></td>
												</tr>
												<tr>
													<td width="40%"><i class="hide fa fa-phone"></i> <label>Mobile Number</label></td>
													<td width="70%"><?php echo ( !empty( $user_details->mobile_number ) ) ? $user_details->mobile_number : '- - -  - - - '; ?></td>
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
							<legend><!-- Address -->&nbsp;</legend>
							<table style="width:100%;">
								<tr>
									<td width="30%"><i class="hide fa fa-lock"></i> <label>Username</label></td>
									<td width="60%"><?php echo $user_details->username; ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-lock"></i> <label>Account Membership Number</label></td>
									<td width="60%"><?php echo $user_details->account_membership_number; ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-lock"></i> <label>Membership Number</label></td>
									<td width="60%"><?php echo $user_details->membership_number; ?></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="row">			
					<div class="row">			
						<?php $this->load->view('webapp/_partials/tabs_loader'); ?>
					</div>
					<?php include $include_page; ?>
				</div>
			</div>
			<?php }else{ ?>
				<div class="row">
					<span><?php echo $this->config->item('no_records'); ?></span>
				</div>
			<?php } ?>
		</div>
	</div>
</div>

