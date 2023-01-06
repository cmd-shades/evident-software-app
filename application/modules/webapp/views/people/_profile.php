<style>
	table tr th,table tr td{
		padding: 5px 0;
	}
	
	.profile-details-container{
		padding: 0 10px;
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
			<div class="row">
			<?php if( !empty( $overview_attributes ) ) { ?>
				<div class="profile-details-container">
				<?php $this->load->view( 'webapp/_templates/'.( ( !empty( $template_version ) ) ? ( int ) $template_version : 1 ).'/overview' ) ?>
				</div>
			<?php } else { ?>
				<div class="profile-details-container">
					<?php if( !empty( $person_details ) ) { ?>
			
						<div class="row alert alert-ssid" role="alert">
							<div class="col-md-6 col-sm-6 col-xs-12">
								<div class="row">
									<legend>User Details</legend>
									<div class="rows">
										<div class="row profile_view">
											<div class="row col-sm-12">
												<div class="right col-xs-12">
													<table style="width:100%;">
														<tr>
															<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Full name</label></td>
															<td width="60%"><a href="<?php echo base_url('webapp/user/profile/'.$person_details->user_id ); ?>" title="Click to view the user profile for <?php echo ucwords( $person_details->first_name.' '.$person_details->last_name ); ?>" ><?php echo ucwords( $person_details->first_name.' '.$person_details->last_name ); ?></a></td>
														</tr>
														<tr>
															<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Status</label></td>
															<td width="60%"><?php echo ucwords($person_details->status ); ?></td>
														</tr>
														<tr>
															<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Category</label></td>
															<td width="60%"><?php echo ucwords($person_details->category_name_alt ); ?></td>
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
									<legend>Contact</legend>
									<table style="width:100%;">
										<tr>
											<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Email address(es)</label></td>
											<td width="60%"><a href="mailto:<?php echo strtolower( $person_details->email ); ?>"><?php echo strtolower( $person_details->email ); ?></a> | <a href="mailto:<?php echo strtolower( $person_details->personal_email ); ?>"><?php echo ucwords( $person_details->personal_email ); ?></a></td>
										</tr>
										<tr>
											<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Mobile</label></td>
											<td width="60%"><?php echo ucwords( $person_details->mobile_number ); ?></td>
										</tr>
										<tr>
											<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Telephone</label></td>
											<td width="60%"><?php echo ucwords( $person_details->phone ); ?></td>
										</tr>
									</table>
								</div>
							</div>
						</div>
				<?php } ?>
				</div>
			</div>
			<?php } ?>
			<?php if( ( !empty( $this->user->tier_id ) && ( $this->user->tier_id > 1 ) ) ){
				$this->load->view('webapp/_partials/tabs_loader');
				include $include_page;
			} else {
				$this->load->view('webapp/_partials/tabs_loader');
				if( $include_page == "person_details.php" ){ 
					include $include_page; 
				}
			} ?>
		</div>
	</div>
</div>