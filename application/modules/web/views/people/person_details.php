<div class="row">
	<div class="col-md-6 col-sm-6 col-xs-12">
		<form id="update-person-form-left" class="form-horizontal">
			<input type="hidden" name="page" value="details" />
			<input type="hidden" name="user_id" value="<?php echo $person_details->user_id; ?>" />
			<input type="hidden" name="person_id" value="<?php echo $person_details->person_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<div class="x_panel tile has-shadow">
				<legend>Update Personal Info</legend>
				<div class="input-group form-group">
					<label class="input-group-addon">Status</label>
					<select id="user_status" name="status_id" class="form-control">
						<option>Please select</option>
						<?php if (!empty($user_statuses)) {
						    foreach ($user_statuses as $k => $status) { ?>
							<option value="<?php echo $status->status_id; ?>" <?php echo ($person_details->status_id == $status->status_id) ? 'selected=selected' : ''; ?> ><?php echo $status->status; ?></option>
						<?php }
						    } ?>
					</select>
				</div>

				<div class="leave-date" style="display:<?php echo (in_array($person_details->status_id, [2])) ? 'block' : 'none'; ?>">
					<div class="input-group form-group">
						<label class="input-group-addon">Leave date</label>
						<input name="leave_date" class="form-control datepicker" type="text" placeholder="Leave date" value="<?php echo (valid_date($person_details->leave_date)) ? date('d-m-Y', strtotime($person_details->leave_date)) : ''; ?>" />
					</div>
				</div>

				<div class="input-group form-group">
					<label class="input-group-addon">First name</label>
					<input name="first_name" class="form-control readonly-data" type="text" placeholder="First name" value="<?php echo $person_details->first_name; ?>" readonly />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Last name</label>
					<input name="last_name" class="form-control readonly-data" type="text" placeholder="Last name" value="<?php echo $person_details->last_name; ?>" readonly />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Preferred name</label>
					<input name="preferred_name" class="form-control" type="text" placeholder="Preferred name" value="<?php echo $person_details->preferred_name; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Person Category</label>
					<select name="category_id" class="form-control">
						<option>Please select</option>
						<?php if (!empty($people_categories)) {
						    foreach ($people_categories as $k => $row) { ?>
							<option value="<?php echo $row->category_id; ?>" <?php echo ($person_details->category_id == $row->category_id) ? 'selected=selected' : ''; ?> ><?php echo $row->category_name_alt; ?></option>
						<?php }
						    } ?>
					</select>
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Email</label>
					<input name="personal_email" class="form-control" type="text" placeholder="Email" value="<?php echo $person_details->personal_email; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Mobile number</label>
					<input name="mobile_number" class="form-control" type="text" placeholder="Mobile Number" value="<?php echo $person_details->mobile_number; ?>" />
				</div>

				<?php if ($this->user->is_admin || !empty($permissions->can_edit) || !empty($permissions->is_admin)) { ?>
					<div class="row">
						<div class="col-md-6">
							<button id="update-person-btn-1" class="btn btn-sm btn-block btn-flow btn-success btn-next update-person-btn" type="button">Update Person Info</button>
						</div>

						<?php if ($this->user->is_admin || !empty($permissions->can_delete) || !empty($permissions->is_admin)) { ?>
							<div class="col-md-6">
								<button id="delete-person-btn" class="btn btn-sm btn-block btn-flow btn-danger has-shadow" type="button" data-person_id="<?php echo $person_details->person_id; ?>">Archive Person</button>
							</div>
						<?php } ?>
					</div>
				<?php } else { ?>
					<div class="row col-md-6">
						<span id="no-permissions-1" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</span>
					</div>
				<?php } ?>

				<div class="row hide">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="row col-md-6">
							<span id="no-permissions-2" class="btn btn-sm btn-block btn-flow btn-success btn-next" type="button" >Insufficient permissions</span>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>

	<?php if ($this->user->is_admin || !empty($permissions->is_admin)) { ?>
	<div class="col-md-6 col-sm-6 col-xs-12">
		<form id="update-person-form-right" class="form-horizontal">
			<input type="hidden" name="page" value="details" />
			<input type="hidden" name="user_id" value="<?php echo $person_details->user_id; ?>" />
			<input type="hidden" name="person_id" value="<?php echo $person_details->person_id; ?>" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			<div class="x_panel tile has-shadow">
				<legend>Confidential Data</legend>
				<div class="input-group form-group">
					<label class="input-group-addon">Date of Birth</label>
					<input name="date_of_birth" class="form-control datepicker" placeholder="Date of Birth" value="<?php echo (valid_date($person_details->date_of_birth)) ? date('d-m-Y', strtotime($person_details->date_of_birth)) : ''; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Nationality</label>
					<select name="nationality_id" class="form-control">
						<option>Please select</option>
						<?php if (!empty($countries)) {
						    foreach ($countries as $k => $country) { ?>
							<option value="<?php echo $country->country_id; ?>" <?php echo ($person_details->nationality_id == $country->country_id) ? 'selected=selected' : ''; ?> ><?php echo $country->country_name; ?></option>
						<?php }
						    } ?>
					</select>
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Right to work</label>
					<select name="right_to_work" class="form-control">
						<option>Please select</option>
						<option value="Yes" <?php echo (strtolower($person_details->right_to_work) == 'yes') ? 'selected=selected' : ''; ?> >Yes</option>
						<option value="No" <?php echo (strtolower($person_details->right_to_work) != 'yes') ? 'selected=selected' : ''; ?> >No</option>
					</select>
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Right to work type</label>
					<input name="right_to_work_type" class="form-control" type="text" placeholder="Right to work type" value="<?php echo $person_details->right_to_work_type; ?>" />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">National Insurance</label>
					<input name="national_insurance_number" class="form-control" placeholder="National Insurance number" value="<?php echo $person_details->national_insurance_number; ?>" />
				</div>
				<?php if ($this->user->is_admin || !empty($permissions->is_admin)) { ?>
					<div class="row col-md-6">
						<button id="update-person-btn-2" class="btn btn-sm btn-block btn-flow btn-success btn-next update-person-btn" type="button" >Update Confidential Data</button>
					</div>
				<?php } else { ?>
					<div class="row col-md-6">
						<button id="no-permissions" class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>
					</div>
				<?php } ?>
			</div>
		</form>
	</div>
	<?php } ?>
</div>

<script>
	$(document).ready(function(){

		$( '#user_status' ).change( function(){
			var statusId = $('option:selected', this).val();
			if( statusId == '2' ){
				$( '.leave-date' ).slideDown();
			}else{
				$( '.leave-date' ).slideUp();
				$( '[name="leave_date"]' ).val( '' );
			}
		});

		$( '.readonly-data' ).click( function(){
			swal({
				title: 'Ops! Readonly field',
				text: 'You can change this field in User Manager',
			})
		});

		//Submit form for processing
		$( '.update-person-btn' ).click( function( event ){

			event.preventDefault();
			var formData = $(this).closest('form').serialize();
			swal({
				title: 'Confirm person update?',
				// type: 'question',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/people/update_person/'.$person_details->person_id); ?>",
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){
									location.reload();
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

		$('#delete-person-btn').click(function(){

			var personId = $(this).data( 'person_id' );

			swal({
				title: 'Confirm person delete?',
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/people/delete_person/'.$person_details->person_id); ?>",
						method:"POST",
						data:{person_id:personId},
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									title: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){
									window.location.href = "<?php echo base_url('webapp/people'); ?>";
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
