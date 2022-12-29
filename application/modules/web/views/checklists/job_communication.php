<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Communication Logs <small></small></legend>
			<?php if (!empty($job_details->comm_logs)) { ?>
				<table style="width:100%">
					<tr>
						<th width="20%">Status Change Note</th>
						<th width="12%">Current Job Status</th>
						<th width="12%">Previous Job Status</th>
						<th width="12%">Current Tracking Status</th>
						<th width="12%">Previous Tracking Status</th>
						<th width="10%">Logged By</th>
						<th width="10%">Job Created By</th>
						<th width="12%">Timestamp</th>						
					</tr>
					<?php if (!empty($job_details->comm_logs)) {
					    foreach ($job_details->comm_logs as $comm_log) { ?>
						<tr>
							<td><?php echo ($comm_log->notes) ? $comm_log->notes : ''; ?></td>
							<td><?php echo ($comm_log->current_status) ? $comm_log->current_status : ''; ?></td>
							<td><?php echo ($comm_log->previous_status) ? $comm_log->previous_status : ''; ?></td>
							<td><?php echo ($comm_log->current_tracking_status) ? $comm_log->current_tracking_status : ''; ?></td>
							<td><?php echo ($comm_log->previous_tracking_status) ? $comm_log->previous_tracking_status : ''; ?></td>
							<td><?php echo ($comm_log->logged_by) ? $comm_log->logged_by : ''; ?></td>
							<td><?php echo ($job_details->created_by_full_name) ? $job_details->created_by_full_name : ''; ?></td>
							<td><?php echo date('d-m-Y H:i:s', strtotime($comm_log->logged_date)); ?></td>
						</tr>
					<?php }
					    } ?>
				</table>
			<?php } else { ?>
				<?php echo $this->config->item('no_records'); ?>
			<?php } ?>
		</div>		
	</div>
</div>

<script>
	$( document ).ready( function(){

	});
</script>