


<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Vehicle Audits <small>(last 10 audits)</small></legend>
			<?php if( !empty( $vehicle_audits ) ){ ?>
				<table style="width:100%">
					<thead>
						<tr>
							<th width="12%">Audit Type</th>
							<th width="12%">Timestamp</th>
							<th width="15%">Operative</th>
							<?php /* ?><th width="12%" class="text-center">Questions</th>
							<th width="12%" class="text-center">Documents</th>
							<th width="12%" class="text-center">Signature</th>
							<?php */ ?>
							<th width="8%">Status</th>
							<th width="10%"><span class="pull-right">Action</span></th>
							<th width="10%" class="text-center">PDF</th>
						</tr>
					</thead>
					<?php if( !empty( $vehicle_audits ) ){ foreach( $vehicle_audits as $audit_record ) { ?>
						<tr>
							<td><a href="<?php echo base_url( "webapp/audit/profile/".$audit_record->audit_id ); ?>"><?php echo $audit_record->audit_type; ?></a></td>
							<td><?php echo date( 'd-m-Y H:i:s', strtotime( $audit_record->date_created ) ); ?></td>
							<td><?php echo ( $audit_record->created_by ) ? $audit_record->created_by : ''; ?></td>
							<?php /* ?><td class="text-center" ><i class="far <?php echo ( $audit_record->questions_completed == 1 ) ? ' fa-check-circle text-green ' : ' fa-times-circle text-red'; ?>"></i></td>
							<td class="text-center" ><i class="far <?php echo ( $audit_record->documents_uploaded == 1 ) ? ' fa-check-circle text-green ' : ' fa-times-circle text-red'; ?>"></i></td>
							<td class="text-center" ><i class="far <?php echo ( $audit_record->signature_uploaded == 1 ) ? ' fa-check-circle text-green ' : ' fa-times-circle text-red'; ?>"></i></td>
							<?php */ ?>
							<td><?php echo ( $audit_record->audit_status ) ? $audit_record->audit_status : 'Status unknown'; ?></td>
							<td class="question-toggler pointer" data-audit_id="<?php echo $audit_record->audit_id; ?>" data-toggle="modal" data-target="#questions-modal-md" ><span class="pull-right" >View responses</span></td>
							<td class="text-center pdf_export"><a  style="color: #d01d00; font-size: 18px;" href="<?php echo base_url( "webapp/audit/download/" ).$audit_record->audit_id ?>" target="_blank"><i class="fas fa-file-pdf"></i></a></td>
						</tr>
					<?php } } ?>
					
				</table>
				
				<div class="modal fade questions-modal-md" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
								<h4 class="modal-title" id="myModalLabel">Vehicle Audit Log</h4>
							</div>
							<div class="modal-body"></div>
						</div>
					</div>
				</div>
				
			<?php }else{ ?>
				<?php echo $this->config->item('no_records'); ?>
			<?php } ?>
		</div>		
	</div>
</div>

<script>
	$(document).ready(function(){
		
		$( '.question-toggler' ).click( function( event ){
									
				var auditId = $(this).data('audit_id');
				$.ajax({
					url:"<?php echo base_url('webapp/audit/view_audit_record/' ); ?>",
					method:"POST",
					data:{audit_id:auditId},
					dataType: 'json',
					success:function(data){
						if( data.status == 1 ){
							$(".modal-body").html(data.audit_record);
							$(".questions-modal-md").modal("show");							
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