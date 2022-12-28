	<div class="x_panel no-border">
		<div class="x_content">
			<?php if( !empty( $customer_details ) ) { ?>
			<div class="profile-details-container">
				<div class="row alert alert-ssid" role="alert">
					<div class="col-md-4 col-sm-6 col-xs-12">
						<div class="row">
							<legend>Customer Details</legend>
							<div class="rows">
								<div class="row profile_view">
									<div class="row col-sm-12">
										<div class="right col-xs-12">
											<table style="width:100%;">
												<tr>
													<td width="30%"><i class="hide fa fa-user"></i> <label>Salutation</label></td>
													<td><?php echo strtoupper( ( !empty( $customer_details->salutation ) ) ? $customer_details->salutation : ''  ); ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Customer Name</label></td>
													<td width="60%"><?php echo ucwords( ( !empty( $customer_details->customer_first_name ) ) ? $customer_details->customer_first_name : ''  ); ?> &nbsp;<?php echo( !empty(  $customer_details->customer_last_name ) ) ?  $customer_details->customer_last_name : '' ; ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Business Name</label></td>
													<td width="60%"><?php echo ( !empty( $customer_details->business_name ) ) ? $customer_details->business_name : '' ; ?></td>
												</tr>
												<?php /*
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Customer Type</label></td>
													<td width="60%"><?php echo ( ( !empty( $customer_details->customer_type ) ) ? $customer_details->customer_type : '' ); ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>VAT Number</label></td>
													<td width="60%"><?php echo ( !empty( $customer_details->vat_number ) ) ? $customer_details->vat_number : '' ; ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Company Number</label></td>
													<td width="60%"><?php echo ( !empty( $customer_details->company_number ) ) ? $customer_details->company_number : '' ; ?></td>
												</tr> */ ?>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4 col-sm-6 col-xs-12">
						<div class="row">
							<legend>Contact Details</legend>
							<table class="table-responsive">
								<tr>
									<td width="30%"><i class="hide fa fa-user"></i> <label>Customer Main Telephone</label></td>
									<td width="60%"><?php echo ( ( !empty( $customer_details->customer_main_telephone ) ) ? $customer_details->customer_main_telephone : ''  ); ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-user"></i> <label>Customer Mobile</label></td>
									<td width="60%"><?php echo ( !empty( ( $customer_details->customer_mobile ) ) ) ? ( $customer_details->customer_mobile ) : '' ; ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Customer Work Telephone</label></td>
									<td width="60%"><?php echo ( ( !empty( $customer_details->customer_work_telephone ) ) ? $customer_details->customer_work_telephone : ''  ); ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Customer Email</label></td>
									<td width="60%"><?php echo ( !empty( $customer_details->customer_email ) ) ? $customer_details->customer_email : '' ; ?></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="col-md-4 col-sm-6 col-xs-12">
						<div class="row">
					<?php 	if( !empty( $customer_details->addresses ) ){ ## the customer address ?>
								<legend>Main Address Details</legend>
								<table class="table-responsive">
								<?php
									$main_address_found = false;
									foreach( $customer_details->addresses as $add_row ){
										if( strtolower( $add_row->address_type_group ) == "main" ){ ?>
											<tr>
												<td width="30%"><i class="hide fa fa-user"></i> <label>Contact Name</label></td>
												<td width="60%"><?php echo ( ( !empty( $add_row->address_contact_first_name ) ) ? $add_row->address_contact_first_name : '' ); ?> <?php echo ( ( !empty( $add_row->address_contact_last_name ) ) ? $add_row->address_contact_last_name : '' ); ?></td>
											</tr>

											<tr>
												<td width="30%"><i class="hide fa fa-user"></i> <label>Address</label></td>
												<td width="60%"><?php echo ( ( !empty( $add_row->address_line1 ) ) ? $add_row->address_line1 : '' ); echo ( ( !empty( $add_row->address_line2 ) ) ? ', '.$add_row->address_line2 : '' ); echo ( ( !empty( $add_row->address_line3 ) ) ? ', '.$add_row->address_line3 : '' );  echo ( ( !empty( $add_row->address_town ) ) ? ', '.$add_row->address_town : '' );  echo ( ( !empty( $add_row->address_postcode ) ) ? ', '.$add_row->address_postcode : '' );  ?></td>
											</tr>

											<?php
												$main_address_found = true;
												break;
										} } ?>

										<?php if( !$main_address_found ){ ?>
											<?php if( !empty( $customer_details->default_address ) ){ ?>
											
												<?php if( !empty( $customer_details->default_address->address_business_name ) ){ ?>
													<tr>
														<td width="30%"><i class="hide fa fa-user"></i> <label>Address Business Name</label></td>
														<td width="60%"><?php echo $customer_details->default_address->address_business_name; ?></td>
													</tr>
												<?php } ?>
												<tr>
													<td width="30%"><i class="hide fa fa-user"></i> <label>Address</label></td>
													<td width="60%"><?php echo ( ( !empty( $customer_details->default_address->short_address ) ) ? $customer_details->default_address->short_address : ''  ); ?></td>
												</tr>
											<?php } else { ?>
												<tr>
													<td width="30%"><i class="hide fa fa-user"></i> <label>Customer Address</label></td>
													<td width="60%">The main address has not been set yet.</td>
												</tr>
											<?php } ?>
										<?php } ?>


								<?php 	
								/* } else if( !empty( $customer_details->default_address ) ){ ?>
								<legend>Main Address Details</legend>
								<table class="table-responsive">
									<tr>
										<td width="30%"><i class="hide fa fa-user"></i> <label>Address Business Name</label></td>
										<td width="60%"><?php echo ( ( !empty( $customer_details->default_address->address_business_name ) ) ? $customer_details->default_address->address_business_name : ''  ); ?></td>
									</tr>
									<tr>
										<td width="30%"><i class="hide fa fa-user"></i> <label>Address</label></td>
										<td width="60%"><?php echo ( ( !empty( $customer_details->default_address->short_address ) ) ? $customer_details->default_address->short_address : ''  ); ?></td>
									</tr> <?php */
							} else if( !empty( $customer_details->default_address ) ){ ?>
								<legend>Business Address Details</legend>
								<table class="table-responsive">
								<?php
								if( !empty( $customer_details->default_address->address_business_name ) ){ ?>
									<tr>
										<td width="30%"><i class="hide fa fa-user"></i><label>Address Business Name</label></td>
										<td width="60%"><?php echo $customer_details->default_address->address_business_name; ?></td>
									</tr>
								<?php } ?>
								<tr>
									<td width="30%"><i class="hide fa fa-user"></i><label>Address</label></td>
									<td width="60%"><?php echo ( ( !empty( $customer_details->default_address->short_address ) ) ? $customer_details->default_address->short_address : ''  ); ?></td>
								</tr>
							<?php
							} else { ?>
								<legend>Business Address Details</legend>
								<table class="table-responsive">
								<tr>
									<td width="30%"><i class="hide fa fa-user"></i> <label>Customer Address</label></td>
									<td width="60%">The business address has not been set yet.</td>
								</tr>
							<?php 	
							} ?>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="row">
				<?php 	if( ( !empty( $this->user->tier_id ) && ( $this->user->tier_id > 1 ) ) ){
							$this->load->view('webapp/_partials/tabs_loader');
							include $include_page;
						} else {
							$this->load->view('webapp/_partials/tabs_loader');
							if( $include_page == "customer_details.php" ){
								include $include_page;
							} else {
								include $include_page;
							}
						} ?>
			</div>
			<?php }else{ ?>
				<div class="row">
					<span><?php echo $this->config->item('no_records'); ?></span>
				</div>
			<?php } ?>
		</div>
	</div>


