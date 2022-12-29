<style>
	.alert-info-bars {
		color: #fff;
		background: #0092CD;
		background-color: rgba(0, 146, 205, 0.88);
		border-color: rgba(0, 146, 205, 0.88);
	}
	
	.input-group-addon {
		min-width: 240px;
		text-align: left;
	}
</style>

<div class="row">
	<div class="col-md-6 col-sm-6 col-xs-12">
		<form id="user-details-form" class="form-horizontal">
			<input type="hidden" name="id" value="<?php echo $user_details->id; ?>" />
			<input type="hidden" name="page" value="permissions" />
			<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
			
			<div class="x_panel tile has-shadow">
				<legend>User Details</legend>
				<div class="input-group form-group" title="User's first name">
					<label class="input-group-addon">First Name</label>
					<input name="first_name" value="<?php echo !empty($user_details->first_name) ? $user_details->first_name : ''; ?>" class="form-control" type="text" placeholder="First Name"  />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Last Name</label>
					<input name="last_name" value="<?php echo !empty($user_details->last_name) ? $user_details->last_name : ''; ?>" class="form-control" type="text" placeholder="Last Name"  />
				</div>
				<div class="input-group form-group info-fields" data-info_tag="username" >
					<label class="input-group-addon">Username</label>
					<input name="username" value="<?php echo !empty($user_details->username) ? $user_details->username : ''; ?>" class="form-control  input-fields" type="text" placeholder="Username"  />
				</div>
				
				<div class="input-group form-group info-fields" data-info_tag="email" >
					<label class="input-group-addon">Email</label>
					<input name="email" value="<?php echo !empty($user_details->email) ? $user_details->email : ''; ?>" class="form-control  input-fields" type="email" placeholder="Email"  />
				</div>
				<div class="input-group form-group">
					<label class="input-group-addon">Mobile Number</label>
					<input name="mobile_number" value="<?php echo !empty($user_details->mobile_number) ? $user_details->mobile_number : ''; ?>" class="form-control" type="text" placeholder="Mobile Number"  />
				</div>
				<?php /*
                <div class="input-group form-group info-fields" data-info_tag="External User Ref">
                    <label class="input-group-addon">3rd Party User Ref</label>
                    <input name="external_user_ref" class="form-control" type="text" placeholder="3rd Party/External User Ref" value="<?php echo !empty( $user_details->external_user_ref ) ? $user_details->external_user_ref : ''; ?>" />
                </div>

                <div class="input-group form-group info-fields" data-info_tag="External User Ref">
                    <label class="input-group-addon">3rd Party Username</label>
                    <input name="external_username" class="form-control" type="text" placeholder="3rd Party/External Username" value="<?php echo !empty( $user_details->external_username ) ? $user_details->external_username : ''; ?>" />
                </div>

                <div class="input-group form-group info-fields" data-info_tag="External User Ref">
                    <label class="input-group-addon">3rd Party Password</label>
                    <input name="external_password" class="form-control sensitive-data" type="password" placeholder="3rd Party/External Password" value="<?php echo !empty( $user_details->external_password ) ? $user_details->external_password : ''; ?>" />
                </div>
                */ ?>
				
				<?php /*
                <div class="input-group form-group">
                    <label class="input-group-addon">Telephone</label>
                    <input name="phone" value="<?php echo !empty( $user_details->phone ) ? $user_details->phone : ''; ?>" class="form-control" type="text" placeholder="Mobile"  />
                </div>
                */ ?>
				
				<?php if (!empty($this->user->is_admin)) { ?>
				<div class="input-group form-group">
						<label class="input-group-addon">User Status</label>
						<select id="active" name="active" class="form-control">
							<option value="0">Please select group</option>
							<option value="1" <?php echo ($user_details->active == 1) ? 'selected=selected' : ''; ?> >Active</option>
							<option value="0" <?php echo (empty($user_details->active) || ($user_details->active != 1)) ? 'selected=selected' : ''; ?> >In-Active</option>
						</select>	
					</div>
				<?php } ?>
				
				<?php if (!empty($this->user->is_admin)) { ?>
					<div class="input-group form-group">
						<label class="input-group-addon">User Type</label>
						<select name="user_type_id" class="form-control">
							<option>Please select</option>
							<?php if (!empty($user_types)) {
							    foreach ($user_types as $k => $user_type) { ?>
								<option value="<?php echo $user_type->user_type_id; ?>" <?php echo ($user_type->user_type_id == $user_details->user_type_id) ? 'selected=selected' : ''; ?> ><?php echo $user_type->user_type_name; ?> <?php echo ($user_type->user_type_id == 1) ? '(System)' : ''; ?></option>
							<?php }
							    } ?>
						</select>	
					</div>
					
					<div class="input-group form-group info-fields" data-info_tag="primary_user" >
						<label class="input-group-addon">Primary User &nbsp;<i title="This is used for managing External Users who can be assigned Bulk Jobs." class="fas fa-info-circle"></i></label>
						<select name="is_primary_user" class="form-control">
							<option value="0" >Please select</option>
							<option value="1" <?php echo ($user_details->is_primary_user == 1) ? 'selected=selected' : ''; ?> >Yes</option>
							<option value="0" <?php echo ($user_details->is_primary_user != 1) ? 'selected=selected' : ''; ?> >No</option>
						</select>	
					</div>
					
					<div class="input-group form-group" >
						<label class="input-group-addon" title="This allows this user to appear in the list of Field Operatives" >Can Be Assigned Jobs</label>
						<select name="can_be_assigned_jobs" class="form-control">
							<option value="0" >Please select</option>
							<option value="1" <?php echo ($user_details->can_be_assigned_jobs == 1) ? 'selected=selected' : ''; ?> >Yes</option>
							<option value="0" <?php echo ($user_details->can_be_assigned_jobs != 1) ? 'selected=selected' : ''; ?> >No</option>
						</select>	
					</div>
					
					<div class="input-group form-group" >
						<label class="input-group-addon" title="This allows this user to view Site Jobs on the Mobile app">Can View Building Jobs <small>(Mobile App)</small> <i title="This allows this user to view Site Jobs on the Mobile app" class="fas fa-info-circle"></i></label>
						<select name="can_view_site_jobs" class="form-control">
							<option value="0" >Please select</option>
							<option value="1" <?php echo ($user_details->can_view_site_jobs == 1) ? 'selected=selected' : ''; ?> >Yes</option>
							<option value="0" <?php echo ($user_details->can_view_site_jobs != 1) ? 'selected=selected' : ''; ?> >No</option>
						</select>	
					</div>
					
					<div class="input-group form-group" >
						<label class="input-group-addon" title="This allows this user to edit sepcific Job fields on the Mobile App" >Can Edit Jobs <small>(Mobile App)</small> <i title="This allows this user to edit sepcific Job fields on the Mobile App" class="fas fa-info-circle"></i></label>
						<select name="can_edit_jobs" class="form-control">
							<option value="0" >Please select</option>
							<option value="1" <?php echo ($user_details->can_edit_jobs == 1) ? 'selected=selected' : ''; ?> >Yes</option>
							<option value="0" <?php echo ($user_details->can_edit_jobs != 1) ? 'selected=selected' : ''; ?> >No</option>
						</select>	
					</div>
				
					<div class="input-group form-group">
						<label class="input-group-addon">Line Manager/Supervisor</label>
						<select name="supervisor_id" class="form-control">
							<option>Please select</option>
							<?php if (!empty($supervisors)) {
							    foreach ($supervisors as $k => $supervisor) { ?>
								<option value="<?php echo $supervisor->id; ?>" <?php echo ($supervisor->id == $user_details->supervisor_id) ? 'selected=selected' : ''; ?> ><?php echo $supervisor->first_name; ?> <?php echo $supervisor->last_name; ?></option>
							<?php }
							    } ?>
						</select>	
					</div>
				
					<div class="input-group form-group" >
						<label class="input-group-addon" title="This allows this user to be listed as a Supervisor" >Is Supervisor</label>
						<select name="is_supervisor" class="form-control">
							<option value="0" >Please select</option>
							<option value="1" <?php echo ($user_details->is_supervisor == 1) ? 'selected=selected' : ''; ?> >Yes</option>
							<option value="0" <?php echo ($user_details->is_supervisor != 1) ? 'selected=selected' : ''; ?> >No</option>
						</select>	
					</div>

				<?php } ?>
				
				<div style="display:block">
					<div class="input-group form-group info-fields" data-info_tag="password">
						<label class="input-group-addon">Password</label>
						<input name="password" type="password" value="" class="form-control" type="text" placeholder="Password"  />
					</div>
					<div class="input-group form-group info-fields" data-info_tag="password">
						<label class="input-group-addon">Password Confirm</label>
						<input name="password_confirm" type="password" value="" class="form-control" type="text" placeholder="Confirm Password"  />
					</div>
				</div>
				
				<?php if ($user_details->id == 1 && ($this->user->id != $user_details->id)) { ?>
					<!-- <div class="row col-md-12">
						<button class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >You do can not update this record</button>					
					</div> -->
					<em>This record is readonly</em>
				<?php } else { ?>
					<?php if ($this->user->is_admin || !empty($permissions->can_edit) || !empty($permissions->is_admin)) { ?>
						<div class="row">
							<div class="col-md-6">
								<button id="update-user-btn" class="btn btn-sm btn-block btn-flow btn-success btn-next update-user-btn" type="button" >Update User</button>					
							</div>
							
							<?php if ($this->user->is_admin || !empty($permissions->can_delete) || !empty($permissions->is_admin)) { ?>
								<div class="col-md-6">
									<button id="delete-user-btn" class="btn btn-sm btn-block btn-flow btn-danger has-shadow" type="button" data-user_id="<?php echo $user_details->id; ?>">Delete User</button>
								</div>
							<?php } ?>
						</div>
					<?php } else { ?>
						<div class="row col-md-6">
							<button class="btn btn-sm btn-block btn-flow btn-success btn-next no-permissions" type="button" disabled >Insufficient permissions</button>					
						</div>
					<?php } ?>
				<?php } ?>
			</div>
		</form>
	</div>
	
	<?php if (!empty($this->user->is_admin) && !empty($this->user->is_superuser) && (in_array($this->user->id, SUPER_ADMIN_ACCESS))) { ?>
		<div class="col-md-6 col-sm-6 col-xs-12">
			<form id="user-account-switcher-form" class="form-horizontal">
				<input type="hidden" name="user_id" value="<?php echo $user_details->id; ?>" />
				<input type="hidden" name="page" value="permissions" />
				<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
				<input type="hidden" name="source_account_id" value="<?php echo $this->user->account_id; ?>" />
				
				<div class="x_panel tile has-shadow">
					<legend>Switch User Account</legend>
					<p>You may have to re-apply Permissions for the user on the new account.</p>
					<div class="input-group form-group">
						<label class="input-group-addon">Switch To</label>
						<select name="destination_account_id" class="form-control">
							<option>Please select</option>
							<?php if (!empty($active_accounts)) {
							    foreach ($active_accounts as $k => $active_account) { ?>
								<option value="<?php echo $active_account->account_id; ?>" <?php echo ($active_account->account_id == $user_details->account_id) ? 'selected=selected' : ''; ?> ><?php echo $active_account->account_name; ?> <?php echo ($active_account->account_id == $user_details->account_id) ? '(Current)' : ''; ?></option>
							<?php }
							    } ?>
						</select>	
					</div>
					
					<div class="row">
						<div class="col-md-6">
							<button id="switch-user-account-btn" class="btn btn-sm btn-block btn-flow btn-success" type="button" >Switch Account</button>					
						</div>
					</div>
				</div>
			</form>
		</div>
	<?php } ?>
	
	<div class="col-md-6 col-sm-6 col-xs-12">
		<div class="username-criteria info-containers" style="display:none">
			<div class="alert alert-info-bars alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="far fa-user"></i> &nbsp;Username</h4>
				<p>&#8226; Username must be unique within your Business</p>
				<br/>
            </div>
		</div>
	
		<div class="email-criteria info-containers" style="display:none">
			<div class="alert alert-info-bars alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="fas fa-at"></i> &nbsp;Email Criteria</h4>
				<p>&#8226; Email address must be valid</p>
				<p>&#8226; Email address must be unique within your Business</p>
				<br/>
            </div>
		</div>
		
		<div class="password-criteria info-containers" style="display:none">
			<div class="alert alert-info-bars alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="fas fa-key"></i> &nbsp;Password Policy</h4>
                <p>&#8226; Password must be at least 8 characters long</p>
                <p>&#8226; Password must contain at least 1 number</p>
				<p>&#8226; Password must contain at least 1 uppercase letter</p>
				<p>&#8226; Password must contain at least 1 special character</p>
				<br/>
            </div>
		</div>
		
		<div class="primary_user-criteria info-containers" style="display:none">
			<div class="alert alert-info-bars alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="fas fa-info-circle"></i> &nbsp;Primary User <small>(For Contractor Management)</small></h4>
                <p>&nbsp;</p>
				<p>&#8226; When set to <strong>Yes</strong> - Other users can be associated with this user</p>
                <p>&#8226; When set to <strong>Yes</strong> - This User can be assigned Bulk Jobs</p>
				<p>&#8226; When set to <strong>Yes</strong> - This User can re-assign Jobs</p>
                <p>&nbsp;</p>
                <!-- <p>&#8226; When set to <strong>No</strong> - Other users can NOT be associated with this user</p> -->
            </div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		
		$( '.input-fields' ).blur( function(){
			var infoTag = $( this ).parent().data( 'info_tag' );
			$( '.'+infoTag+'-criteria' ).slideUp( 'fast' );
		});	
		
		$( '.info-fields' ).click( function(){
			var infoTag = $( this ).data( 'info_tag' );
			$( '.info-containers' ).hide();
			$( '.'+infoTag+'-criteria' ).show();
		});
		
		//Submit form for processing
		$( '#update-user-btn' ).click( function(){

			var formData = $('#user-details-form').serialize();
			var postUrl  = '<?php echo base_url("webapp/user/update_user/".$user_details->id); ?>';

			swal({
				title: "Confirm user update?",
				showCancelButton: true,
				confirmButtonColor: "#5CB85C",
				cancelButtonColor: "#9D1919",
				confirmButtonText: "Yes"
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:postUrl,
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									text: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){ 
									location.reload();
								} ,3000);							
							}else{
								swal({
									type: 'error',
									text: data.status_msg
								})
							}		
						}
					});
				}
			}).catch(swal.noop)
		});
		
		//Delete a user record (set as archived)
		$('#delete-user-btn').click(function(){
			
			var userId = $(this).data( 'user_id' );
			
			swal({
				title: 'Confirm delete user?',
				type: 'warning',
				text: 'This will also delete the attached person record',
				showCancelButton: true,
				confirmButtonColor: '#5CB85C',
				cancelButtonColor: '#9D1919',
				confirmButtonText: 'Yes'
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:"<?php echo base_url('webapp/user/delete_user/'.$user_details->id); ?>",
						method:"POST",
						data:{user_id:userId},
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
								
									window.location.href = "<?php echo base_url('webapp/user/users'); ?>";
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
		
		
		//Submit Switch Account form for processing
		$( '#switch-user-account-btn' ).click( function(){

			var formData 	= $('#user-account-switcher-form').serialize();
			var postUrl  	= '<?php echo base_url("webapp/user/switch_user_account/".$user_details->id); ?>';
			var userProfId 	= '<?php echo $user_details->id; ?>';
			var adminUserId	= '<?php echo $this->user->id; ?>';

			var warningMsg = ( userProfId == adminUserId ) ? "Re-login is required on Success!" : "You will be redirected to Users list on success!";

			swal({
				title: "Confirm Switch Account?",
				text: warningMsg,
				showCancelButton: true,
				confirmButtonColor: "#5CB85C",
				cancelButtonColor: "#9D1919",
				confirmButtonText: "Yes"
			}).then( function (result) {
				if ( result.value ) {
					$.ajax({
						url:postUrl,
						method:"POST",
						data:formData,
						dataType: 'json',
						success:function(data){
							if( data.status == 1 ){
								swal({
									type: 'success',
									text: data.status_msg,
									showConfirmButton: false,
									timer: 3000
								})
								window.setTimeout(function(){
	
									if( userProfId == adminUserId ){
										var new_url = '<?php echo base_url("webapp/user/logout"); ?>';
									} else {
										var new_url = '<?php echo base_url("webapp/user/users"); ?>';
									}
									window.location.href = new_url;
								} ,3000);							
							}else{
								swal({
									type: 'error',
									text: data.status_msg
								})
							}		
						}
					});
				}
			}).catch(swal.noop)
		});
		
	});
</script>