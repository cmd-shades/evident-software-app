<style type="text/css">
.input-group.form-group{
    width: 100%;
}

.clickable{
	border-top: 1px solid #eee;
    margin-top: 10px;
    background: #eee;
}

.table > tbody > tr.washer > td {
    line-height: 8px;
    padding: 0;
}

.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
    border-top: none;
}
</style>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Person Fines and Penalties</legend>

			
	<?php 	if( $this->user->is_admin || !empty( $permissions->can_view ) || !empty( $permissions->is_admin ) ){ ?>	
				<div class="control-group form-group" style="width: 100%; display: block; float: left;">		
		<?php		if( !empty( $person_fines ) ){ ?>
						<table class="table table-responsive">
							<tr>
								<th width="5%">Fine ID</th>
								<th width="10%">Fine Date</th>
								<th width="15%">Vehicle REG</th>
								<th width="40%">Fine Note</th>
								<th  width="15%">Created By</th>
								<th  width="10%">Created Date</th>
							</tr>
					<?php	$ctr = 1;
							foreach( $person_fines as $row ){ ?>
							<tr class="clickable" data-row_id="row_<?php echo $ctr; ?>">
								<td width="5%"><?php echo $row->fine_id; ?></td>
								<td width="10%"><?php echo validate_date( $row->fine_date ) ? format_date_client( $row->fine_date ) : '' ; ?></td>
								<td width="15%"><a href="<?php echo base_url( "webapp/fleet/profile/".$row->vehicle_id."/fines/" ); ?>"><?php echo $row->vehicle_reg; ?></a></td>
								<td width="40%"><?php echo $row->fine_note; ?></td>
								<td width="15%"><?php echo $row->created_by_full_name; ?></td>
								<td width="10%"><?php echo validate_date( $row->created_date ) ? format_date_client( $row->created_date ) : '' ; ?></td>
							</tr>
							<tr class="washer"><td colspan="6">&nbsp;</td></tr>
					<?php	$ctr ++;
							} ?>
						</table>
		<?php		} else { ?>
						<table class="table table-responsive">
							<tr>
								<td colspan="6">
									<span><?php echo $this->config->item( 'no_records' ); ?></span>
								</td>
							</tr>
						</table>
		<?php		} ?>
				</div>
	<?php	} ?>
		</div>	
	</div>
</div>

<script>
$( document ).ready( function(){
	
	/* https://xdsoft.net/jqplugins/datetimepicker/ */
	$( '.datetimepicker' ).datetimepicker({
		formatDate: 'd/m/Y',
		timepicker:false,
		format:'d/m/Y',
	});
});
</script>