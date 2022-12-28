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
				<?php if( !empty( $premises_details ) ) { ?>
				<div class="row alert alert-ssid" role="alert">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<legend>Premise ID: <?php echo $premises_details->premises_id; ?></legend>
							<div class="rows">
								<div class="row profile_view">
									<div class="row col-sm-12">
										<div class="right col-xs-12">
											<table style="width:100%;">
												<tr>
													<td width="30%"><label><?php echo ( !empty( $premises_details->primary_attribute ) ) ? ucwords( $premises_details->primary_attribute ) : false; ?></label></td>
													<td width="60%"><?php echo ( !empty( $premises_details->attribute_value ) ) ? ucwords( $premises_details->attribute_value ) : false; ?></td>
												</tr>
												<tr>
													<td width="30%"><label>Premises Ref</label></td>
													<td width="60%"><?php echo strtoupper( $premises_details->premises_ref ); ?></td>
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
							<legend>Premises state</legend>
							<table style="width:100%;">
								<tr>
									<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Premises Type</label></td>
									<td width="60%"><a href="<?php echo base_url( '/webapp/premises/premises_types/'.$premises_details->premises_type_id ); ?>" ><?php echo ucwords( $premises_details->premises_type ); ?></a></td>
								</tr>
								<tr>
									<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Assigned to</label></td>
									<td width="60%"><?php echo ( !empty( $premises_details->assigned_to ) ) ? ucwords( $premises_details->assigned_to ) : '-  -  -  -  -  -  -  -  -'; ?></td>
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
			<?php } else { ?>
				<div class="row">
					<span><?php echo $this->config->item('no_records'); ?></span>
				</div>
			<?php } ?>
		</div>
	</div>
</div>

