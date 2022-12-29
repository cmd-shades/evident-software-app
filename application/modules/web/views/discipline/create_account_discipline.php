<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="profile-details-container">
				<div class="row">
					<div class="row">
						<div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-12">
							<div class="x_panel tile has-shadow">
								<form id="create-account-discipline-form" class="form-horizontal">
									<input type="hidden" name="page" value="details" />
									<div class="col-md-6 col-xs-12">
										<legend>Discipline Details</legend>
										<div class="input-group form-group">
											<label class="input-group-addon">Default Discipline</label>
											<select id="default_discipline_id" name="discipline_id" class="form-control" required >
												<option>Please select</option>
												<option value="Active" >Active</option>
											</select>
										</div>
										<div class="input-group form-group">
											<label class="input-group-addon">Discipline Name</label>
											<input id="account_discipline_name" name="account_discipline_name" class="form-control" type="text" placeholder="Discipline Name" value="" />
										</div>

										<div class="input-group form-group">
											<label class="input-group-addon">Discipline Desc</label>
											<input id="account_discipline_desc" name="account_discipline_desc" class="form-control" type="text" placeholder="Discipline Category" value="" />
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Image url</label>
											<input id="account_discipline_image_url" name="account_discipline_image_url" class="form-control" type="text" placeholder="Image url" value="" />
										</div>
									</div>
								
									<div class="col-md-12 col-xs-12">
										<div class="row">
											<div class="col-md-6">
												<?php if (!empty($default_disciplines)) { ?>
													<button class="btn btn-sm btn-block btn-flow btn-success btn-next" type="submit">Create Discipline</button>
												<?php } else { ?>
													<button class="btn btn-sm btn-block btn-flow btn-success btn-next" type="button" disabled>Create Discipline</button>
												<?php } ?>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

$( "form#create-account-discipline-form" ).submit( function( e ){
	
	e.preventDefault();
	
	var defaultDisciplineId = $( '#default_discipline_id option:selected' ).val();

	if( !defaultDisciplineId ){
		swal({
			type: 'warning',
			title: 'Please select the Default Discipline'
		});
		return false;
	}

	var formData = $( this ).serialize();

	swal({
		title: 'Confirm Discipline creation?',
		showCancelButton: true,
		confirmButtonColor: '#5CB85C',
		cancelButtonColor: '#9D1919',
		confirmButtonText: 'Yes'
	}).then( function( result ){
		if( result.value ) {
			$.ajax({
				url:"<?php echo base_url('webapp/discipline/create_account_discipline/'); ?>",
				method: "POST",
				data: formData,
				dataType: 'json',
				success:function( data ){
					if( ( data.status == 1 ) && ( data.account_discipline.account_discipline_id !== '' ) ){
						var newDisciplineID = data.account_discipline.account_discipline_id;
						swal({
							type: 'success',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 2000
						})
						window.setTimeout( function(){
							location.href = "<?php echo base_url('webapp/discipline/profile/') ?>" + newDisciplineID;
						}, 1000);
					} else {
						swal({
							type: 'error',
							title: data.status_msg
						})
					}
				}
			});
		}
	}).catch( swal.noop )
});
</script>
