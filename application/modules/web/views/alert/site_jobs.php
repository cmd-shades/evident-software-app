<div class="row">
	<?php if ($this->user->is_admin || !empty($permissions->can_add) || !empty($permissions->is_admin)) { ?>
		<div class="col-md-6 col-sm-6 col-xs-12">
			<form action="<?php echo base_url('/site/update_update/'.$site_details->site_id); ?>" method="post" class="form-horizontal">
				<div class="x_panel tile has-shadow">
					<legend>Create New Site Jobs</legend>
					<div class="hide input-group form-group">
						<label class="input-group-addon" >Site Name</label>
						<input name="site_name" value="<?php echo !empty($site_details->site_name) ? $site_details->site_name : ''; ?>" class="form-control" type="text" placeholder="Site Name"  />
					</div>
					<div class="hide input-group form-group">
						<label class="input-group-addon" >Site Reference</label>
						<input name="site_name" value="<?php echo !empty($site_details->site_reference) ? $site_details->site_reference : ''; ?>" class="form-control" type="text" placeholder="Site Reference"  />
					</div>
						<div class="row col-md-6">
							<button id="create-lead-btn" class="btn btn-sm btn-block btn-flow btn-success btn-next" type="submit" >Add New Job</button>					
						</div>
				</div>
			</form>
		</div>
	<?php } ?>
	
	<div class="col-md-6 col-sm-6 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Existing Jobs</legend>
			<?php if ($this->user->is_admin || !empty($permissions->can_view) || !empty($permissions->can_edit) || !empty($permissions->is_admin)) { ?>
				<table style="width:100%">
					<tr>
						<th width="15%">Job Id</th>
						<th width="20%">Job Date</th>
						<th width="18%">Job Type</th>
						<th width="15%">Assignee</span></th>
						<th width="27%"><span class="pull-right">Action</span></th>
					</tr>
					<?php if (!empty($site_jobs)) {
					    foreach ($site_jobs as $job_record) { ?>
						<tr>
							<td><a href="<?php echo base_url('webapp/job/profile/'.$job_record->job_id); ?>"><?php echo $job_record->job_id; ?></a></td>
							<td><?php echo date('d-m-Y', strtotime($job_record->job_date)); ?></td>
							<td><?php echo $job_record->job_type; ?></td>
							<td><?php echo $job_record->assignee; ?></span></td>
							<td>
								<span class="pull-right">
									<span>
										<span class="text-green text-bold"><a href="<?php echo base_url('webapp/job/profile/'.$job_record->job_id); ?>">View</a></span>
										<?php if ($this->user->is_admin || !empty($permissions->can_delete) || !empty($permissions->is_admin)) { ?>
											<span class="text-green text-bold"><a href="#"> | Delete</a></span>
										<?php } ?>
									</span>
								</span>
							</td>
						</tr>
					<?php }
					    } else { ?>
						<tr>
							<td colspan="5"><?php echo $this->config->item('no_records'); ?></td>
						</tr>
					<?php } ?>
				</table>
			<?php } ?>
		</div>		
	</div>
</div>

<script>
	$(document).ready(function(){

		$('.address-lookup-results').hide();
		$('.address-postcode').focus(function(){
			var postCode = $(this).val();
			if( postCode.length > 0 ){
				$.post("<?php echo base_url("lms/get_addresses_by_postcode"); ?>",{postcodes:postCode},function(result){
					$("#address-select").html(result["addresses_list"]);				
				},"json");
				
				$('.address-lookup-results').show( 'slow' );
			}
		});

		$("#address-select").change(function(){
			var addresId = $("option:selected", this).val();
			var leadId   = $("[name='site_id']").val();
			var postCode = $("#post-code").val();
		
			if( addresId.length > 0 ){
				$.post("<?php echo base_url("lms/ajax_lms_update"); ?>",
					{
						site_id:leadId,
						linked_address_id:addresId,
						post_code:postCode
					
					},function(result){
						$("#ajax-feedback").html(result["feedback"]).delay(4000).fadeOut(1500);
						// Wait for the feedback message to fade out then reload the page
						setTimeout(function() {
							location.reload();
						}, 5000);
					},"json"
				);
			}
		});

		$('#delete-lead').click(function(){
			return confirm('Are you sure you want to delete this lead?');
		});
	});
</script>