<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Linked Contract Details</legend>
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12 table-responsive">								
					<table class="table table-responsive" style="width:100%">
						<tr style="font-size:100%; font-weight:700">
							<td width="25%">Contract Name</td>
							<td width="20%">Contract Type</td>
							<td width="10%">Start Date</td>
							<td width="15%">Contract Status</td>
							<td width="20%">Lead Person</td>
							<td width="10%"><span class="pull-right">Action</span></td>
						</tr>
						<?php if( !empty( $contract_details ) ){ ?>
						<tr>
							<td><a href="<?php echo base_url('/webapp/contract/profile/'.$contract_details->contract_id ); ?>" ><?php echo ucwords( $contract_details->contract_name ); ?></a></td>
							<td><?php echo ucwords( $contract_details->type_name ); ?></td>
							<td><?php echo date( 'd-m-Y', strtotime( $contract_details->start_date ) ); ?></td>
							<td><?php echo ucwords( $contract_details->status_name ); ?></td>
							<td><?php echo ( !empty( $contract_details->contract_lead_name ) ) ? $contract_details->contract_lead_name : ''; ?></td>
							<td><span class="pull-right"><a href="<?php echo base_url('/webapp/contract/profile/'.$contract_details->contract_id ); ?>" >Go to Contract</a></span></td>
						</tr>										
						<?php } else{ ?>
							<tr>
								<td colspan="4"><?php echo $this->config->item('no_records'); ?></td>
							</tr>
						<?php } ?>
					</table>						
				</div>
			</div>
		</div>		
	</div>
</div>

<script>
	$(document).ready(function(){
		$('.contract-toggle').click(function(){
			var classGrp = $(this).data( 'class_grp' );
			$( '.'+classGrp ).slideToggle();
		});
	});
</script>