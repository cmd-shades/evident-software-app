<div class="row">
	<div class="x_panel" style="border:none">
		<div class="row panel-info">
			<div class="x_content">
				<div class="row">
					<div class="col-md-6">
						<legend>Create New User</legend>
						<div class="x_panel no-border has-shadow">
							<div class="col-md-12">
							<div class="row">
							
								<form id="user-creation-form" method="post" >
									<input type="hidden" name="page" value="details" />
									<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
										
										<div class="input-group form-group">
											<label class="input-group-addon">User type</label>
											<select name="user_type_id" class="form-control">
												<option>Please select</option>
												<?php if( !empty($user_types) ) { foreach( $user_types as $k => $user_type ) { ?>
													<option value="<?php echo $user_type->user_type_id; ?>" <?php echo ( $user_type->user_type_id == 2 ) ? 'selected=selected' : ''; ?> ><?php echo $user_type->user_type_name; ?> <?php echo ( $user_type->user_type_id == 1 ) ? '(System)' : ''; ?></option>
												<?php } } ?>
											</select>	
										</div>

										<div class="input-group form-group">
											<label class="input-group-addon">First name</label>
											<input name="first_name" class="form-control" type="text" placeholder="First Name" required />
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Last name</label>
											<input name="last_name" class="form-control" type="text" placeholder="Last Name" required />
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Contact email</label>
											<input name="email" class="form-control" type="text" placeholder="Email" required />
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Username</label>
											<input name="username" class="form-control" type="text" placeholder="Username" required />
										</div>

										<div class="input-group form-group">
											<label class="input-group-addon">Mobile</label>
											<input name="mobile_number" class="form-control" type="text" placeholder="Mobile number"  />
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Password</label>
											<input name="password" class="form-control" type="password" placeholder="Password"  />
										</div>
										<div class="input-group form-group">
											<label class="input-group-addon">Password confirm</label>
											<input name="password_confirm" class="form-control" type="password" placeholder="Password confirm"  />
										</div>
										<br/>

										<div class="row">
											<div class="col-md-6 col-sm-6 col-xs-12">
												<button id="create-user-btn" class="btn btn-block btn-flow btn-success btn-next" type="button" >Create User</button>					
											</div>
										</div>
									

									<div class="hide col-md-6">
										<legend>Assign Permissions <i class="fas fa-caret-down"></i></legend>
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
</div>

<script>
	$(document).ready(function(){
		$('#create-user-btn').click(function( e ){
			
			e.preventDefault();
			
			var firstName	= $('[name="first_name"]').val();			
			var lastName	= $('[name="last_name"]').val();			
			var contactEmai	= $('[name="email"]').val();			
			var userName	= $('[name="username"]').val();
			var passWord	= $('[name="password"]').val();
			var passWordConf= $('[name="password_confirm"]').val();
			var fieldName	= '';
			
			if( passWordConf.length == 0 ){ fieldName = 'Password confirm'; }
			if( passWord.length  	== 0 ){ fieldName = 'Password'; }
			if( userName.length  	== 0 ){ fieldName = 'Username'; }
			if( contactEmai.length  == 0 ){ fieldName = 'Contact email'; }
			if( lastName.length  	== 0 ){ fieldName = 'Last name'; }
			if( firstName.length   	== 0 ){ fieldName = 'First name'; }
			
			if( fieldName.length > 0 ){
				swal({
					type: 'error',
					text: fieldName+' field is required'
				})
				return false;
			}
			
			var formData = $('#user-creation-form').serialize();

			$.ajax({
				url:"<?php echo base_url('webapp/user/create_user/' ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function(data){
					if( data.status == 1 && ( data.user !== '' ) ){
						
						var newUserId = data.user.user.id;

						swal({
							type: 'success',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 3000
						})
						window.setTimeout(function(){
							location.href = "<?php echo base_url('webapp/user/profile/'); ?>"+newUserId;
						} ,3000);							
					}else{
						swal({
							type: 'error',
							title: data.status_msg
						})
					}			
				}
			});
			
			return false;		
		});
	});
</script>