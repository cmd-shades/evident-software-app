<style>
	div.panel-min-height{
		height:452px;
		min-height:452px;
	}
	
	label {
		line-height: 24px;

	}
</style>

<div class="row">
	<div class="col-md-6 col-sm-6 col-xs-12">
		<div class="x_panel tile has-shadow">
			<form id="update-job-form" class="form-horizontal">
				<input type="hidden" name="page" value="details" />
				<input type="hidden" name="discipline_id" value="<?php echo $account_discipline_details->discipline_id; ?>" />
				<input type="hidden" name="activation_account_id" value="<?php echo $account_discipline_details->account_id; ?>" />
				<input type="hidden" name="account_discipline_id" value="<?php echo $account_discipline_details->account_discipline_id; ?>" />
				<legend>Discipline Details</legend>
				<div class="input-group form-group">
					<label class="input-group-addon">Discipline Ref</label>
					<input id="account_discipline_ref" name="account_discipline_ref" class="form-control" type="text" placeholder="Discipline Code" readonly value="<?php echo $account_discipline_details->account_discipline_ref; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Discipline Name</label>
					<input id="account_discipline_name" name="account_discipline_name" class="form-control" type="text" placeholder="Discipline Name" value="<?php echo $account_discipline_details->account_discipline_name; ?>" />
				</div>

				<div class="input-group form-group">
					<label class="input-group-addon">Discipline Desc</label>
					<input id="account_discipline_desc" name="account_discipline_desc" class="form-control" type="text" placeholder="Discipline Category" value="<?php echo $account_discipline_details->account_discipline_desc; ?>" />
				</div>

				<div class="input-group form-group">
					<label class="input-group-addon">Discipline Status</label>
					<select name="account_discipline_status" class="form-control">
						<option>Please select</option>
						<option value="Active" <?php echo (strtolower($account_discipline_details->account_discipline_status) == 'active') ? 'selected=selected' : ''; ?> >Active</option>
						<option value="Deactivated" <?php echo (strtolower($account_discipline_details->account_discipline_status) == 'deactivated') ? 'selected=selected' : ''; ?> >Deactivated</option>
						<option value="Unavailable" <?php echo (strtolower($account_discipline_details->account_discipline_status) == 'unavailable') ? 'selected=selected' : ''; ?> disabled >Unavailable</option>
					</select>
				</div>

				<div class="input-group form-group">
					<label class="input-group-addon">Image url</label>
					<input id="account_discipline_image_url" name="account_discipline_image_url" class="form-control" type="text" placeholder="Image url" value="<?php echo $account_discipline_details->account_discipline_image_url; ?>" />
				</div>

				<div class="row" >
					<?php if ($this->user->is_admin || !empty($permissions->can_delete) || !empty($permissions->is_admin)) { ?>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<button type="button" class="btn-block update-account-discipline-btn btn btn-sm btn-success" >Update Discipline</button>
						</div>
					<?php } ?>
				</div>
			</form>
		</div>
	</div>
 
	<div class="col-md-6 col-sm-6 col-xs-12">
		<div class="x_panel tile has-shadow">
			<legend>Change Log</legend>
			
		</div>
	</div>
</div>
	
<script>
	$( document ).ready( function(){
		
		$( '.update-account-discipline-btn' ).click( function( event ){

			var formID = $( this ).closest( 'form' ).attr( 'id' );
			event.preventDefault();
			var formData = $('#'+formID ).serialize();
			swal({
				title: 'Confirm Discipline update?',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/discipline/update_account_discipline/'.$account_discipline_details->account_discipline_id); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 2000
								})
								window.setTimeout(function(){
									location.reload();
								} ,1000);
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