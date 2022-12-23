<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Job Risk Assessment</legend>
			<?php if( !empty( $ra_records ) ){ ?>
				<table style="width:100%">
					<tr>
						<th width="5%">ID</th>
						<th width="15%">Timestamp</th>
						<th width="15%">Operative</th>
						<th width="13%" class="text-center">Risks</th>
						<th width="12%" class="text-center">Documents</th>
						<th width="12%" class="text-center">Signature</th>
						<th width="13%">Status</th>
						<th width="15%"><span class="pull-right">Action</span></th>
					</tr>
					<?php if( !empty( $ra_records ) ){ foreach( $ra_records as $ra_record ) { ?>
						<tr>
							<td><?php echo $ra_record->assessment_id; ?></td>
							<td><?php echo date( 'd-m-Y H:i:s', strtotime( $ra_record->date_created ) ); ?></td>
							<td><?php echo ( $ra_record->created_by ) ? $ra_record->created_by : ''; ?></td>
							<td class="text-center" ><i class="far <?php echo ( $ra_record->risks_completed == 1 ) ? ' fa-check-circle text-green ' : ' fa-times-circle text-red'; ?>"></i></td>
							<td class="text-center" ><i class="far <?php echo ( $ra_record->documents_uploaded == 1 ) ? ' fa-check-circle text-green ' : ' fa-times-circle text-red'; ?>"></i></td>
							<td class="text-center" ><i class="far <?php echo ( $ra_record->signature_uploaded == 1 ) ? ' fa-check-circle text-green ' : ' fa-times-circle text-red'; ?>"></i></td>
							<td><?php echo ( $ra_record->status ) ? $ra_record->status : 'Status unknown'; ?></td>
							<td class="risk-toggler pointer" data-assessment_id="<?php echo $ra_record->assessment_id; ?>" data-toggle="modal" data-target="#bs-example-modal-md" ><span class="pull-right" >View responses</span></td>
						</tr>
					<?php } } ?>
					
				</table>
				
				<div class="modal fade bs-example-modal-md" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog modal-md">
						<div class="modal-content">
							<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
								<h4 class="modal-title" id="myModalLabel">Risk Assessment Log</h4>
							</div>
							<div class="modal-body"></div>
						</div>
					</div>
				</div>
				
			<?php }else{ ?>
				<div><?php echo $this->config->item('no_records'); ?></div>
			<?php } ?>
		</div>		
	</div>
</div>

<script>
	$(document).ready(function(){
		
		$( '.risk-toggler' ).click( function( event ){
									
				var assessmentId = $(this).data('assessment_id');
				$.ajax({
					url:"<?php echo base_url('webapp/job/view_ra_record/' ); ?>",
					method:"POST",
					data:{assessment_id:assessmentId},
					dataType: 'json',
					success:function(data){
						if( data.status == 1 ){
							$(".modal-body").html(data.ra_record);
							$(".bs-example-modal-md").modal("show");							
						}else{
							swal({
								type: 'error',
								title: data.status_msg
							})
						}		
					}
				});
				
		});
	});
</script>