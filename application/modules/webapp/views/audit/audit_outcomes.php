<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Evidoc Outomes</legend>
			<?php if( !empty( $audit_outcomes ) ){ ?>
				<table class="table" style="width:100%">
					<thead>
						<tr>
							<th width="40%">Recommendation / Failure Reasons</th>
							<th width="15%">Outcome Status</th>
							<th width="15%">Action Status</th>
							<th width="15%">Action Due Date</th>
							<th width="15%">Date Created</th>
						</tr>
					</thead>
					<?php if( !empty( $audit_outcomes ) ){ foreach( $audit_outcomes as $outcomes ) { ?>
						<tr>
							<td><a href="<?php echo base_url( 'webapp/audit/exception_profile/'.$outcomes->id ); ?>"><?php echo !empty( $outcomes->recommendations ) ? ucfirst( $outcomes->recommendations ) : ( !empty( $outcomes->failure_reasons ) ? ucfirst( $outcomes->failure_reasons ) : '' ); ?></a></td>
							<td><?php echo ( $outcomes->audit_result_status_name ) ? $outcomes->audit_result_status_name : 'Status unknown'; ?></td>
							<td><?php echo $outcomes->action_status_name; ?></td>
							<td><?php echo ( valid_date( $outcomes->action_due_date ) ) ? date( 'd-m-Y', strtotime( $outcomes->action_due_date ) ) : ''; ?></td>
							<td><?php echo ( valid_date( $outcomes->date_created ) ) ? date( 'd-m-Y H:i:s', strtotime( $outcomes->date_created ) ) : ''; ?></td>
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
	$( document ).ready(function(){
		
	});
</script>