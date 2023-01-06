<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<?php if( !empty( $project_details ) ) { ?>
			<div class="profile-details-container">
				<div class="row alert alert-ssid bg-grey" role="alert">
					<div class="profile-overview">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<legend>Project Profile <span class="pull-right"><span class="edit-project pointer hide" title="Click to edit this Project"><i class="fas fa-pencil-alt"></i></span> &nbsp; <span class="archive-project pointer" data-project_id="<?php echo $project_details->project_id; ?>" title="Click to archive this Project profile" ><i class="far fa-trash-alt"></i></span></span></legend>
							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Project Name</label></td>
											<td width="85%"><?php echo ( !empty( $project_details->project_name ) ) ? $project_details->project_name : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Project Ref</label></td>
											<td width="85%"><?php echo ( !empty( $project_details->project_ref ) ) ? strtoupper( $project_details->project_ref ) : ''; ?></td>
										</tr>
									</table>							
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Status</label></td>
											<td width="85%"><?php echo ( $project_details->is_active == 1 ) ? 'Active <i class="far fa-check-circle"></i>' : 'Disabled <i class="far fa-times-circle text-red"></i>'; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Project Status</label></td>
											<td width="85%"><?php echo ( !empty( $project_details->project_status ) ) ? $project_details->project_status : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Date Created</label></td>
											<td width="85%"><?php echo ( valid_date( $project_details->date_created ) ) ? date( 'd-m-Y H:i:s', strtotime( $project_details->date_created ) ) : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Created By</label></td>
											<td width="85%"><?php echo ( !empty( $project_details->record_created_by ) ) ? ucwords( $project_details->record_created_by ) : 'Data not available'; ?></td>
										</tr>
									</table>							
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
				<div class="row">
					<?php $this->load->view('webapp/_partials/tabs_loader'); ?>				
					<?php include $include_page; ?>
				</div>
			<?php } else { ?>
				<div class="row">
					<span><?php echo $this->config->item( 'no_records' ); ?></span>
				</div>
			<?php } ?>
		</div>
	</div>
</div>

<script>
$( document ).ready( function(){
	<?php 
	if( !empty( $feedback ) ){ ?>
		swal({ 
			<?php
			if( $feedback == "Workflow has been created successfully." ){ ?>
				type: 'success',
			<?php } else { ?>
				type: 'info',
			<?php } ?>
			title: "<?php echo $feedback; ?>",
			showConfirmButton: false,
			timer: 3000
		})
	<?php
	} ?>
	
	$( '.datetimepicker' ).datetimepicker({
		formatDate: 'd/m/Y',
		timepicker:false,
		format:'d/m/Y',
	});
	
	$( ".createWFButton, #linkSitesWFButton" ).on( "click", function(){
		$( ".table_body" ).slideToggle( 300 );
		$( ".create_wf_form, .link_sites" ).slideToggle( 300 );
		$( this ).children( '.fas' ).toggleClass( 'fa-chevron-down fa-chevron-up' );
	});
	
	
	//ARCHIVE Project
	$( '.archive-project' ).click( function( event ){
		
		var projectTypeId = $( this ).data( 'project_id' );
		
		event.preventDefault();

		swal({
			type: 'warning',
			title: 'Confirm Archive Project?',
			html: 'This will affect any all linked Workflows and Actions!',
			showCancelButton: true,
			confirmButtonColor: '#5CB85C',
			cancelButtonColor: '#9D1919',
			confirmButtonText: 'Yes'
		}).then( function (result) {
			if ( result.value ) {
				$.ajax({
					url:"<?php echo base_url('webapp/project/arcive_project/'.$project_details->project_id ); ?>",
					method:"POST",
					data:{ page:'details', xsrf_token: xsrfToken, project_id:projectTypeId },
					dataType: 'json',
					success:function(data){
						if( data.status == 1 ){
							swal({
								type: 'success',
								title: data.status_msg,
								showConfirmButton: false,
								timer: 2100
							})
							window.setTimeout(function(){
								window.location.href = "<?php echo base_url('webapp/project/projects'); ?>";
							} ,3000);
						}else{
							swal({
								type: 'error',
								title: data.status_msg
							})
						}
					}
				});
			}
		}).catch(swal.noop)
	});
	
});
</script>