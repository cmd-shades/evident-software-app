

<div class="row">
	<div class="x_panel" style="border:none">
		<div class="row panel-info">
			<div class="x_content">
				<div class="row">
					<div class="col-md-6">
						<div class="x_panel no-border has-shadow">
							<div class="col-md-12">
							<legend>Create New User</legend>
							<div class="row">
							
								<form id="user-creation-form" method="post" >
									<input type="hidden" name="page" value="details" />
									<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
										
										<div class="input-group form-group">
											<label class="input-group-addon">User Type</label>
											<select name="user_type_id" class="form-control">
												<option>Please select</option>
												<?php if( !empty( $user_types ) ) { foreach( $user_types as $k => $user_type ) { ?>
													<option value="<?php echo $user_type->user_type_id; ?>" <?php echo ( $user_type->user_type_id == 2 ) ? 'selected=selected' : ''; ?> ><?php echo $user_type->user_type_name; ?> <?php echo ( $user_type->user_type_id == 1 ) ? '(System)' : ''; ?></option>
												<?php } } ?>
											</select>	
										</div>

										<div class="input-group form-group info-fields" data-info_tag="primary_user" >
											<label class="input-group-addon">Primary User &nbsp;<i title="This is used for managing External Users who can be assigned Bulk Jobs." class="fas fa-info-circle"></i></label>
											<select name="is_primary_user" class="form-control">
												<option value="0" >Please select</option>
												<option value="1" >Yes</option>
												<option value="0" selected >No</option>
											</select>	
										</div>

										<div class="input-group form-group" >
											<label class="input-group-addon" title="This allows this user to appear in the list of Field Operatives" >Can Be Assigned Jobs</label>
											<select name="can_be_assigned_jobs" class="form-control">
												<option value="0" >Please select</option>
												<option value="1" >Yes</option>
												<option value="0" selected >No</option>
											</select>	
										</div>

										<div class="input-group form-group">
											<label class="input-group-addon">First Name</label>
											<input name="first_name" class="form-control" type="text" placeholder="First Name" required />
										</div>
										
										<div class="input-group form-group">
											<label class="input-group-addon">Last Name</label>
											<input name="last_name" class="form-control" type="text" placeholder="Last Name" required />
										</div>
										
										<div class="input-group form-group info-fields" data-info_tag="email" >
											<label class="input-group-addon">Contact Email</label>
											<input name="email" class="form-control" type="email" placeholder="Email" required />
										</div>
										
										<div class="input-group form-group info-fields" data-info_tag="username">
											<label class="input-group-addon">Username</label>
											<input name="username" class="form-control" type="text" placeholder="Username" required />
										</div>

										<div class="input-group form-group info-fields" data-info_tag="External User Ref">
											<label class="input-group-addon">3rd Party User Ref</label>
											<input name="external_user_ref" class="form-control" type="text" placeholder="3rd Party/External User Ref" />
										</div>
										
										<div class="input-group form-group info-fields" data-info_tag="External User Ref">
											<label class="input-group-addon">3rd Party Username</label>
											<input name="external_username" class="form-control" type="text" placeholder="3rd Party/External Username" />
										</div>
										
										<div class="input-group form-group info-fields" data-info_tag="External User Ref">
											<label class="input-group-addon">3rd Party Password</label>
											<input name="external_password" class="form-control" type="password" placeholder="3rd Party/External Password" />
										</div>

										<div class="input-group form-group">
											<label class="input-group-addon">Mobile</label>
											<input name="mobile_number" class="form-control" type="text" placeholder="Mobile number"  />
										</div>
										
										<div class="input-group form-group info-fields" data-info_tag="password">
											<label class="input-group-addon">Password</label>
											<input name="password" class="form-control" type="password" placeholder="Password"  />
										</div>
										<div class="input-group form-group info-fields" data-info_tag="password">
											<label class="input-group-addon">Password Confirm</label>
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
					
					<div class="col-md-6 col-sm-6 col-xs-12">
						<div class="username-criteria info-containers" style="display:none">
							<div class="alert alert-info-bars alert-dismissible">
								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
								<h4><i class="fas fa-user"></i> &nbsp;Username</h4>
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
								<p>&#8226; Password must contain at least 1 special character</p>
								<br/>
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
		
		$( '.input-fields' ).blur( function(){
			var infoTag = $( this ).parent().data( 'info_tag' );
			$( '.'+infoTag+'-criteria' ).slideUp( 'fast' );
		});	
		
		$( '.info-fields' ).click( function(){
			var infoTag = $( this ).data( 'info_tag' );
			$( '.info-containers' ).hide();
			$( '.'+infoTag+'-criteria' ).show();
		});
		
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