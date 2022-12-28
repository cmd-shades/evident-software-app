<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Site Alerts <?php echo ( !empty( $event_site_id ) ) ? '( Event Site ID '.$event_site_id.' )' : '';?></legend>
			<div class="row">
				<div class="table-responsive" role="alert" style="overflow-y: hidden;">
					<table class="table">
						<?php if( !empty( $site_alerts->{$event_site_id} ) ){ ?>
							<thead>
								<tr class="headings">
									<th class="column-title no-padding-left" width="30%">Packet ID</th>										
									<th class="column-title" width="70%">&nbsp;</th>										
								</tr>
							</thead>
							<tbody>
								<?php foreach( $site_alerts->{$event_site_id} as $packet_id => $alerts ){ ?>
									<tr class="pointer packets" data-grp_label="pkt<?php echo $packet_id; ?>" >
										<td width="30%" class="no-padding-left"><?php echo $packet_id ; ?> <small>(showing most recent <?php echo count( object_to_array( $alerts ) ) ; ?> alerts)</small> <i class="fa fa-caret-down" style="color:#D66628;"></i></td>
										<td width="70%">&nbsp;</td>
									</tr>
									
									<tr id="pkt<?php echo $packet_id; ?>">
										<td colspan="2" width="100%" class="no-padding-left no-padding-right">
											<table class="table alerts_table" width="100%" >
												<thead>
													<tr>
														<th width="10%">Row ID</th>
														<th width="10%">Packet ID</th>
														<th width="15%">Event Site Id</th>
														<th width="10%">Event Type</th>
														<th width="10%">Sia Code</th>							
														<th width="30%">Event Details</th>							
														<th width="15%">Timestamp</th>
													</tr>
												</thead>
												<tbody>
												<?php foreach( $alerts as $alert ){ ?>
													<tr>
														<tr style="background-color:<?php echo ( in_array( strtolower($alert->site_status), ['ok','no fault'] ) ) ? '#5fb760; color:#fff;' : ( in_array( strtolower($alert->site_status), ['fire','fault'] ) ? '#E74C3C; color:#fff; ' : '' ) ?>">
															<td><?php echo $alert->response_id ; ?></td>
															<td><?php echo $alert->packet_id ; ?></td>
															<td><?php echo $alert->event_site_account_no ; ?></td>								
															<td><?php echo $alert->event_type ; ?></td>
															<td><?php echo $alert->event_sia_code ; ?></td>
															<td><?php echo $alert->event ; ?></td>
															<td><?php echo date('d-m-Y H:i:s',strtotime($alert->response_datetime)); ?></td>									
														</tr>
													</tr>
												<?php } ?>
												</tbody>
											</table>
										</td>									
									</tr>
								<?php } ?>
							</tbody>
						<?php }else{ ?>
							<thead>
								<tr class="headings">
									<td colspan="2" >There's currently no records to display</td>
								</tr>
							</thead>
						<?php } ?>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>