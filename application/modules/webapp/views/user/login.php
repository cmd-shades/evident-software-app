<?php include( 'includes/login_form_top.php' ) ?>
	<form class="login-form" method="post" action="<?php echo base_url('webapp/user/login'); ?>" role="form">
		<input type="hidden" name="previous_url" class="form-control" readonly placeholder="Previous page url" value="<?php echo !empty( $previous_url ) ? $previous_url : ''; ?>">
		<div class="input-group">
			<?php if( !empty( $message ) ){ ?>
				<div class="feedback-msg-container text-center" sty>
					<em><?php echo $message; ?></em>
				</div>
			<?php } ?>
		</div>

		<div class="input-group">
			<span class="input-group-addon" id="addon-username">&nbsp;&nbsp;&nbsp;</span>
			<input type="text" name="username" id="username" tabindex="1" class="form-control" aria-describedby="addon-username" placeholder="Username" value="<?php echo !empty( $username ) ? $username : ''; ?>">
		</div>
		<br/>
		<div class="input-group">
			<span class="input-group-addon" id="addon-password">&nbsp;&nbsp;&nbsp;</span>
			<input type="password" name="password" id="password" tabindex="1" class="form-control" aria-describedby="addon-password" placeholder="Password" value="">
			<input type="hidden" name="app_version" class="hide" value="Evident Webapp - v<?php echo APP_VERSION; ?>">
		</div>
		<br/>
		<?php /*?><div class="input-group">
			<span class="input-group-addon"><input type="checkbox" id="addon-remember" name="remember" value="1" ></span>
			<label class="remember-me-lg" for="addon-remember" > <span> &nbsp;&nbsp;Remember me</span></label>
		</div>
		<br/> <?php */ ?>
		<div class="form-group">
			<div class="row">
				<div class="col-sm-12">
					<button type="submit" class="btn btn-block btn-orange" >Sign In</button>
					<!--<a style="text-decoration:none" href="<?php echo base_url("webapp/user/forgot_password"); ?>"><button type="button" class="btn btn-block" style="text-decoration:none;color: black;">Forgot my password</button></a>-->
				</div>
			</div>
		</div>
		<div>
			<span>Not registered? <a href="<?php echo base_url('webapp/account/signup'); ?>" style="text-decoration:none">Sign up</a> for your <?php echo APP_NAME; ?> account today</span>
		</div>
	</form>
<?php include( 'includes/login_form_bottom.php' ) ?>
