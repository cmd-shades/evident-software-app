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
			<?php if (!empty($vehicle_details)) { ?>
			<div class="profile-details-container">
				<div class="row alert alert-ssid" role="alert">
					<div class="col-md-4 col-sm-6 col-xs-12">
						<div class="row">
							<legend>Vehicle Details</legend>
							<div class="rows">
								<div class="row profile_view">
									<div class="row col-sm-12">
										<div class="right col-xs-12">
											<table style="width:100%;">
												<tr>
													<td width="30%"><i class="hide fa fa-user"></i> <label>Vehicle Reg</label></td>
													<td><?php echo strtoupper($vehicle_details->vehicle_reg); ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Make / Model / Year</label></td>
													<td width="60%"><?php echo ucwords($vehicle_details->vehicle_make); ?> &nbsp;<?php echo $vehicle_details->vehicle_model; ?> &nbsp;<?php echo $vehicle_details->year; ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Vehicle Supplier</label></td>
													<td width="60%"><?php echo $vehicle_details->supplier_name; ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Vehicle Barcode</label></td>
													<td width="60%"><?php echo $vehicle_details->vehicle_barcode; ?></td>
												</tr>
											</table>
										</div>		
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4 col-sm-6 col-xs-12">
						<div class="row">
							<legend>MOT & TAX Information</legend>
							<table style="width:100%;display:block" class="">
								<tr>
									<td width="30%"><i class="hide fa fa-user"></i> <label>MOT Valid</label></td>
									<td><?php echo (strtotime($vehicle_details->mot_expiry) > strtotime(date('Y-m-d'))) ? 'Yes' : 'No'; ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-user"></i> <label>MOT Due Date</label></td>
									<td><?php echo ucwords($vehicle_details->mot_expiry); ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Tax due Date</label></td>
									<td width="60%"><?php echo $vehicle_details->tax_expiry; ?></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="col-md-4 col-sm-6 col-xs-12">
						<div class="row">
							<legend>Insurance Information</legend>
							<table style="width:100%;display:block" class="">
								<tr>
									<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Vehicle Insured</label></td>
									<td width="60%"><?php echo ($vehicle_details->is_insured == 1) ? 'Yes' : 'No'; ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Insurance Provider</label></td>
									<td width="60%"><?php echo $vehicle_details->insurance_provider; ?></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Roadside Assistance</label></td>
									<td width="60%"><?php echo ($vehicle_details->has_road_assistance == 1) ? 'Yes' : 'No'; ?></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="row">
				<?php ## if( !empty( $this->user->tier_id ) && ( $this->user->tier_id != 1 ) ){?>
				<?php 	if ((!empty($this->user->tier_id) && ($this->user->tier_id > 1))) {
				    $this->load->view('webapp/_partials/tabs_loader');
				    include $include_page;
				} else {
				    $this->load->view('webapp/_partials/tabs_loader');
				    if ($include_page == "vehicle_details.php") {
				        include $include_page;
				    }
				} ?>
			</div>
			<?php } else { ?>
				<div class="row">
					<span><?php echo $this->config->item('no_records'); ?></span>
				</div>
			<?php } ?>
		</div>
	</div>
</div>

