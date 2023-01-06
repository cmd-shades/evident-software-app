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
			<?php if( !empty( $exception_details ) ) { ?>
			<div class="profile-details-container">
				<div class="row alert alert-ssid" role="alert">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<div class="row">
							<legend>Exception ID: <?php echo $exception_details->id; ?></legend>
							<div class="rows">
								<div class="row profile_view">
									<div class="row col-sm-12">
										<div class="right col-xs-12">
											<table style="width:100%;">
												<?php /*
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Record Type</label></td>
													<td width="60%"><?php echo ucwords( $exception_details->record_type ); ?></td>
												</tr> 
												*/ ?>
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i><label>Exception Status</label></td>
													<td width="60%"><?php echo ucwords( $exception_details->action_status_name ); ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Created By</label></td>
													<td width="60%"><?php echo ucwords( $exception_details->created_by_full_name ); ?></td>
												</tr>
												<tr>
													<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Action Due Date</label></td>
													<td width="60%"><?php echo ucwords( $exception_details->action_due_date ); ?></td>
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
							<legend>EviDoc details <?php echo ( !empty( $exception_details->audit_id ) ? "( ID: <a href=\"".base_url( '/webapp/audit/profile/'.$exception_details->audit_id )."\">".( $exception_details->audit_id )."</a>  )" : "" );?></legend>
							<table style="width:100%;">
								<tr>
									<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>EviDoc Type</label></td>
									<td width="60%"><?php echo strtoupper( $exception_details->audit_type ); ?></td>
								</tr>
								
								<tr>
									<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>EviDoc Result Status</label></td>
									<td width="60%"><?php echo strtoupper( $exception_details->audit_result_status_name ); ?></td>
								</tr>								

								<?php if( !empty( $exception_details->asset_id ) ){ ?>
								<tr>
									<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Asset Id</label></td>
									<td width="60%"><?php echo ucfirst( $exception_details->asset_id ); ?></td>
								</tr>
								<?php } ?>

								<?php if( !empty( $exception_details->site_id ) ){ ?>
								<tr>
									<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Building ID</label></td>
									<td width="60%"><a href='<?php echo base_url('webapp/site/profile/' . $exception_details->site_id); ?>'><?php echo strtoupper( $exception_details->site_id ); ?></a></td>
								</tr>
								<?php } ?>
								
								<?php if( !empty( $exception_details->vehicle_reg ) ){ ?>
								<tr>
									<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Vehicle Reg</label></td>
									<td width="60%"><?php  echo ( !empty( $exception_details->vehicle_reg ) ) ? strtoupper( $exception_details->vehicle_reg ) : " --- "; ?></td>
								</tr>
								<?php } ?>

								<tr>
									<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>EviDoc Result Status</label></td>
									<td width="60%"><?php echo ( !empty( $exception_details->audit_result_status_name ) ) ? strtoupper( $exception_details->audit_result_status_name ) : " --- " ; ?></td>
								</tr>
								
								<!-- <tr>
									<td width="30%"><i class="hide fa fa-at text-bold"></i> <label>Next EviDoc Date</label></td>
									<td width="60%"><?php echo ( valid_date( $exception_details->next_audit_date ) ) ? format_date_client( $exception_details->next_audit_date ) : ''; ?></td>
								</tr> -->
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>

			<div class="row">
				<?php ## $this->load->view('webapp/_partials/tabs_loader') ?>
				<?php include $include_page; ?>
			</div>
			<?php } else { ?>
				<div class="row">
					<span><?php echo $this->config->item( 'no_records' ); ?></span>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
