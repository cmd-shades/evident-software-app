<style>
	.infini-btn-static {
		width: 16.66666%;
	}

	.infi-button {
		height: 35px;
		border: none;
		background-color: rgb(74, 74, 74);
		color: white;
		font-size: 12px;
	}
	.scroll-el {
		background-color: #f1f1f1;
		width: 100%;
		text-align: center;
		line-height: 30px;
		font-size: 30px;
		margin-right: 5px;
		margin: 10px;
	}
</style>


<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="profile-details-container">
				<div class="row alert alert-ssid bg-blue" role="alert">
					<div class="profile-overview">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<legend><?php echo (!empty($checklist_details->job_type)) ? $checklist_details->job_type : 'Checklist Profile'; ?><span class="pull-right hide"><span class="edit-checklist pointer hide" title="Click to edit thie Job Type profile"><i class="fas fa-pencil-alt"></i></span> &nbsp; <span class="---delete-checklist pointer" title="Click to delete this Job Type profile" ><i class="far fa-trash-alt"></i></span></span></legend>
							<div class="row">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Checklist Type</label></td>
											<td width="85%"><?php echo (!empty($checklist_details->job_type)) ? $checklist_details->job_type : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Completion Status</label></td>
											<td width="85%"><?php echo (!empty($checklist_details->job_status)) ? $checklist_details->job_status : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Completion By</label></td>
											<td width="85%"><?php echo (!empty($checklist_details->assignee)) ? ucwords($checklist_details->assignee) : ''; ?></td>
										</tr>
									</table>							
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<table style="width:100%;">
										<tr>
											<td width="15%"><label>Site Name</label></td>
											<td width="85%"><?php echo (!empty($checklist_details->site_name)) ? $checklist_details->site_name : ''; ?></td>
										</tr>										
										<tr>
											<td width="15%"><label>Site Reference</label></td>
											<td width="85%"><?php echo (!empty($checklist_details->site_reference)) ? strtoupper($checklist_details->site_reference) : ''; ?></td>
										</tr>
										<tr>
											<td width="15%"><label>Completion Date</label></td>
											<td width="85%"><?php echo (valid_date($checklist_details->finish_time)) ? date('d-m-Y H:i:s', strtotime($checklist_details->finish_time)) : ''; ?></td>
										</tr>
									</table>							
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="clearfix"></div>
				<div class="row">
					<div class="dashaboard-tabs">
						<div class="floating-pallet">
							<div class="row">
								<div class="col-md-2 col-sm-2 col-xs-2">
									<a class="btn bg-<?php echo $active_tab == 'result' ? 'blue' : 'grey'; ?> btn-block" href="<?php echo base_url('webapp/job/checklist_profile/'.$this->uri->segment(4).'/result'); ?>" >Result</a>
								</div>
								<div class="col-md-2 col-sm-2 col-xs-2">
									<a class="btn bg-<?php echo $active_tab == 'documents' ? 'blue' : 'grey'; ?> btn-block" href="<?php echo base_url('webapp/job/checklist_profile/'.$this->uri->segment(4).'/documents'); ?>" >Documents</a>
								</div>
							</div>
							<div class="clear"></div>
						</div>
					</div>
					<?php include $include_page; ?>
				</div>
			
			</div>
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){
		
		$( '.view-resps' ).click( function( event ){
			var checkListId = $( this ).data( 'checklist_id' );
			$( '.resp-'+checkListId ).slideToggle( 'slow' );
		});
		
		$( '.section-container-bar' ).click( function(){
			$( this ).closest( 'div' ).find( '.caret-icon' ).toggleClass('fa-caret-up fa-caret-down');			
		});
		
	});
</script>