<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Asset tracking log <small>(last 10)</small></legend>
			<?php if( !empty( $asset_details->tracking_log ) ){ ?>
				<table class="table table-responsive">	
					<thead>
						<tr>
							<th>Current Status</th>
							<th>Prev Status</th>
							<th>Current Assignee</th>
							<th>Prev Assignee</th>
							<!-- <th>Current Location</th> -->
							<!-- <th>Prev Location</th> -->
							<th>Date Modified</th>
							<th>Modified By</th>
						</tr>
					</thead>
					<tbody>	
						<?php foreach( $asset_details->tracking_log as $tracking_log ){ ?>
							<tr class="ref-record editmodal">
								<td><?php echo $tracking_log->current_status;?></td>
								<td><?php echo $tracking_log->previous_status;?></td>
								<td><a href="<?php echo base_url("webapp/people/profile/".$tracking_log->current_assignee."/assets"); ?>"><?php echo $tracking_log->assigned_to;?></a></td>
								<td><a href="<?php echo base_url("webapp/people/profile/".$tracking_log->previous_assignee."/assets"); ?>"><?php echo $tracking_log->previously_assigned_to;?></a></td>
								<!-- <td><?php echo $tracking_log->current_location;?></td> -->
								<!-- <td><?php echo $tracking_log->previous_location;?></td> -->
								<td><?php echo date('d-m-Y H:i:s', strtotime($tracking_log->date_created));?></td>
								<td><?php echo $tracking_log->created_by;?></td>
							</tr>
						<?php } ?> 
					</tbody>
				</table>
			<?php }else{ ?> 
				<span><?php echo $this->config->item('no_records'); ?></span>
			<?php } ?>
		</div>		
	</div>
</div>

<script>
	$(document).ready(function(){

	});
</script>