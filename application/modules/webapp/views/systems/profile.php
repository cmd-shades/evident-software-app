<div id="systems-profile" class="row">
	<div class="x_panel">
		<div class="x_content">
			<div class="profile-details-container">
				<?php
				if( !empty( $systems_details ) ){ ?>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<h2 class="profile-header">System Profile <?php echo ( !empty( $systems_details->system_type_id ) ) ? "(" . $systems_details->name . ")"  : '' ; ?></h2><div class="delete_container"><a href="#"><i class="fas fa-trash-alt"></i></a></div>
						</div>
					</div>
					<div class="row records-bar panel-primary" role="alert">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<div class="row">
								<!-- <legend>System Details</legend> -->
								<div class="row profile_view">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<table style="width:100%;">
											<tr>
												<td width="30%"><i class="hide fa fa-user"></i> <label>System Name</label></td>
												<td width="60%"><?php echo ( !empty( $systems_details->name ) ) ? ucwords( $systems_details->name ) : '' ; ?></td>
											</tr>
											<tr>
												<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Delivery Mechanism</label></td>
												<td width="60%" title="<?php echo ( !empty( $systems_details->delivery_mechanism_name ) ) ? ucwords( $systems_details->delivery_mechanism_name ) : '' ; ?>"><?php echo ( !empty( $systems_details->delivery_mechanism_name ) ) ? ucwords( $systems_details->delivery_mechanism_name ) : '' ; ?></td>
											</tr>
										</table>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<div class="row">
								<!-- <legend>System Overview</legend> -->
								<table style="width:100%;">
									<tr>
										<td width="30%"><i class="hide fa fa-user"></i> <label>DRM Type</label></td>
										<td width="60%"><?php echo ( !empty( $systems_details->drm_type_name ) ) ? ucwords( $systems_details->drm_type_name ) : '' ; ?></td>
									</tr>
									<tr>
										<td width="30%"><i class="hide fa fa-briefcase"></i> <label>Reference Code</label></td>
										<td width="60%"><?php echo ( $systems_details->system_reference_code ) ? html_escape( $systems_details->system_reference_code ) : '' ; ?></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div class="row">
						<?php include $include_page; ?>
					</div>
				<?php
				} else { ?>
					<div class="row">
						<span><?php echo $this->config->item( 'no_records' ); ?></span>
					</div>
				<?php
				} ?>
			</div>
		</div>
	</div>
</div>

