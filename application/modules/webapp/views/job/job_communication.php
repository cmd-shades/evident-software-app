<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Communication Logs <small><em>(Last 10)</em></small></legend>
			<?php if( !empty( $job_details->comm_logs ) ){ ?>
				<table style="width:100%">
					<tr>
						<th width="5%">ID</th>
						<th width="20%">Site name</th>
						<th width="15%">Customer name</th>
						<th width="30%">Notes</th>
						<th width="15%">Logged By</th>
						<th width="15%">Timestamp</th>						
					</tr>
					<?php if( !empty( $job_details->comm_logs ) ){ foreach( $job_details->comm_logs as $comm_log ) { ?>
						<tr>
							<td><?php echo $comm_log->log_id; ?></td>
							<td><a href="<?php echo base_url('webapp/site/profile/'.$comm_log->site_id ); ?>" ><?php echo ( $comm_log->site_name ) ? ucwords( $comm_log->site_name ) : ''; ?></a></td>
							<td><a href="<?php echo base_url('webapp/customer/profile/'.$comm_log->customer_id ); ?>"><?php echo ( $comm_log->customer_name ) ? ucwords( $comm_log->customer_name ) : ''; ?></a></td>
							<td><?php echo ( $comm_log->notes ) ? $comm_log->notes : ''; ?></td>
							<td><?php echo ( $comm_log->logged_by ) ? $comm_log->logged_by : ''; ?></td>
							<td><?php echo date( 'd-m-Y H:i:s', strtotime( $comm_log->logged_date ) ); ?></td>
						</tr>
					<?php } } ?>
				</table>
				
			<?php }else{ ?>
				<?php echo $this->config->item('no_records'); ?>
			<?php } ?>
		</div>		
	</div>
</div>

<script>
	$(document).ready(function(){

	});
</script>