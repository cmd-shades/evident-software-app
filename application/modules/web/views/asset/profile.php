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
				<?php if (!empty($asset_details)) { ?>
				<div class="row alert alert-ssid" role="alert">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<legend>Asset Details ID: <?php echo $asset_details->asset_id; ?></legend>
							<div class="rows">
								<div class="row profile_view">
									<div class="row col-sm-12">
										<div class="right col-xs-12">
											<table style="width:100%;">
												<tr>

													<?php if (strpos(strtolower($asset_details->attribute_value), 'http') !== false) { /*?>
                                                        <td width="30%"><label><?php echo ( !empty( $asset_details->primary_attribute ) ) ? ucwords( $asset_details->primary_attribute ) : false; ?></label></td>
                                                        <td width="60%"><?php echo ( !empty( $asset_details->attribute_value ) ) ? ucwords( $asset_details->attribute_value ) : false; ?></td>
                                                    <?php */
													} else { ?>
														<td width="30%"><label><?php echo (!empty($asset_details->primary_attribute)) ? ucwords($asset_details->primary_attribute) : false; ?></label></td>
														<td width="60%"><?php echo (!empty($asset_details->attribute_value)) ? ucwords($asset_details->attribute_value) : false; ?></td>
													<?php } ?>
												</tr>
												<tr>
													<td width="30%"><label>Asset unique Id</label></td>
													<td width="60%"><?php echo strtoupper($asset_details->asset_unique_id); ?></td>
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
							<legend>Asset state</legend>
							<table style="width:100%;">
								<tr>
									<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Asset Type</label></td>
									<td width="60%"><a href="<?php echo base_url('/webapp/asset/asset_types/'.$asset_details->asset_type_id); ?>" ><?php echo ucwords($asset_details->asset_type); ?></a></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Assigned to</label></td>
									<td width="60%"><?php echo (!empty($asset_details->assigned_to)) ? ucwords($asset_details->assigned_to) : '-  -  -  -  -  -  -  -  -'; ?></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="row">
					<?php ## if( !empty( $this->user->tier_id ) && ( $this->user->tier_id != 1 ) ){?>
					<?php if ((!empty($this->user->tier_id) && ($this->user->tier_id > 0))) {
					    $this->load->view('webapp/_partials/tabs_loader');
					    include $include_page;
					} else {
					    $this->load->view('webapp/_partials/tabs_loader');
					    if ($include_page == "asset_details.php") {
					        include $include_page;
					    } else {
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

