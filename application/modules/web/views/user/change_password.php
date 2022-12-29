<?php include('includes/login_form_top.php') ?>
	<form class="login-form" method="post" action="<?php echo base_url('webapp/user/change_password'); ?>" role="form">

		<div class="input-group">
			<?php if (!empty($message)) { ?>
				<div class="feedback-msg-container text-center" sty>
					<em><?php echo $message; ?></em>
				</div>
			<?php } ?>
		</div>
		
		<div class="input-group">
			<span class="input-group-addon" id="addon-username">&nbsp;&nbsp;&nbsp;</span>								
			<input type="text" name="username" id="username" tabindex="1" class="form-control" aria-describedby="addon-username" placeholder="Username" value="<?php echo !empty($username) ? $username : ''; ?>">
		</div>
		<br/>
		<div class="input-group">
			<span class="input-group-addon" id="addon-password">&nbsp;&nbsp;&nbsp;</span>								
			<input type="password" name="old" id="old_password" tabindex="1" class="form-control" aria-describedby="addon-password" placeholder="Current Password" value="">
		</div>
		<br/>
		<div class="input-group">
			<span class="input-group-addon" id="addon-password">&nbsp;&nbsp;&nbsp;</span>								
			<input type="password" name="new" id="new_password" tabindex="1" class="form-control" aria-describedby="addon-password" placeholder="New Password" value="">
		</div>
		<br/>
		<div class="input-group">
			<span class="input-group-addon" id="addon-password_confirm">&nbsp;&nbsp;&nbsp;</span>								
			<input type="password" name="new_confirm" id="confirm_new_password" tabindex="1" class="form-control" aria-describedby="addon-password" placeholder="New Password Confirm" value="">
		</div>
		<br/>
		<div class="form-group">
			<div class="row">
				<div class="col-sm-12">
					<button type="submit" class="btn btn-block btn-orange" >Change Password</button>												
				</div>
			</div>
		</div>
	</form>
<?php include('includes/login_form_bottom.php') ?>
