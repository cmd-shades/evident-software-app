<div id="channel-profile" class="row">
	<div class="x_panel">
		<div class="x_content">
			<?php
			if( !empty( $channel_details ) ){ ?>
				<div class="profile-details-container">
					<div class="row">
						<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
							<h2 class="profile-header <?php echo ( !empty( $channel_details->channel_status ) && ( strtolower( $channel_details->channel_status ) != "active" ) ) ? 'inactive' : "" ; ?>"><span class="text-bold"><?php echo ( !empty( $channel_details->channel_name ) ) ? ucwords( $channel_details->channel_name ) : '' ; ?></span><?php echo ( !empty( $channel_details->channel_id ) ) ? ' (ID: <span class="text-bold">'.$channel_details->channel_id.'</span>)' : '' ; ?></h2>
						</div>
						<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
							<div class="pull-right">
								<span class="delete_container" title="Click to archive Channel" data-channel_id="<?php echo ( !empty( $channel_details->channel_id ) ) ? $channel_details->channel_id : -1 ; ?>">
									<a href="#">
										<i class="fas fa-trash-alt"></i>
									</a>
								</span>
								<span class="edit_container" >
									<a class="edit-channel" href="#" data-toggle="modal" data-target="#editChannel" title="Click to edit Channel">
										<i class="fas fa-edit"></i>
									</a>
								</span>
							</div>
						</div>
					</div>
					<div class="row records-bar panel-primary" role="alert">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<div class="row">
								<div class="row profile_view">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<table style="width:100%;">
											<tr>
												<td width="30%"><i class="hide fa fa-user"></i> <label>Channel Name</label></td>
												<td width="60%" title="<?php echo $channel_details->channel_name; ?>"><?php echo ( !empty( $channel_details->channel_name ) ) ? html_escape( ucwords( $channel_details->channel_name ) ) : '' ; ?></td>
											</tr>
											<tr>
												<td width="30%"> <label>Channel Provider</label></td>
												<td width="60%"><?php echo ( !empty( $channel_details->provider_name ) ) ? html_escape( ucwords( $channel_details->provider_name ) ) : '' ; ?></td>
											</tr>
											<tr>
												<td width="30%"> <label>Channel Description</label></td>
												<td width="60%"><?php echo ( !empty( $channel_details->description ) ) ? html_escape( $channel_details->description ) : '' ; ?></td>
											</tr>
											<tr>
												<td width="30%"><i class="hide fa fa-user"></i> <label>Distribution Start Date</label></td>
												<td width="60%"><?php echo ( validate_date( $channel_details->distribution_start_date ) ) ? format_date_client( $channel_details->distribution_start_date ) : '' ; ?></td>
											</tr>
											<tr>
												<td width="30%"><i class="hide fa fa-user"></i> <label>Distribution End Date</label></td>
												<td width="60%"><?php echo ( validate_date( $channel_details->distribution_end_date ) ) ? format_date_client( $channel_details->distribution_end_date ) : '' ; ?></td>
											</tr>
										</table>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<div class="row">
								<table class="fixed">
									<tr>
										<td width="30%"><label>OTT Channel</label></td>
										<td width="60%"><?php echo ( !empty( $channel_details->is_channel_ott ) ) ? "Yes" : "No" ; ?></td>
									</tr>

									<tr style="display: <?php echo ( !empty( $channel_details->is_channel_ott ) ) ? "table-row" : "none"; ?>">
										<td width="30%"><label>Source URL</label></td>
										<td width="60%"><?php echo ( !empty( $channel_details->source_url ) ) ? html_escape( substr( $channel_details->source_url, 0, 100 ).( ( strlen( $channel_details->source_url )  > 100 ) ? " (...)" : "" ) ) : "" ; ?></td>
									</tr>

									<tr style="display: <?php echo ( !empty( $channel_details->is_channel_ott ) ) ? "table-row" : "none"; ?>">
										<td width="30%"><label>Technical Encoded URL</label></td>
										<td width="60%"><?php echo ( !empty( $channel_details->technical_encoded_url ) ) ? html_escape( substr( $channel_details->technical_encoded_url, 0, 100 ).( ( strlen( $channel_details->source_url ) > 100 ) ? " (...)" : "" ) ) : "" ; ?></td>
									</tr>
									
									<tr style="display: <?php echo ( !empty( $channel_details->is_channel_ott ) ) ? "none" : "table-row"; ?>">
										<td width="30%"><label>Satelite Sources</label></td>
										<td width="60%"><?php echo ( !empty( $channel_details->satelite_sources ) ) ? ucwords( $channel_details->satelite_sources ) : "" ; ?></td>
									</tr>
									
									<tr style="display: <?php echo ( !empty( $channel_details->is_channel_ott ) ) ? "none" : "table-row"; ?>">
										<td width="30%"><label>Regions</label></td>
										<td width="60%"><?php echo ( !empty( $channel_details->regions ) ) ? ucwords( $channel_details->regions ) : "" ; ?></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div class="row">
						<?php include $include_page; ?>
					</div>
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