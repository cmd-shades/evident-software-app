<style type="text/css">
.feedback{
	color: red;
	font-size: 24px;
	font-weight: 600;
	border: 1px solid red;
	padding: 10px 5px;
}
</style>

<!-- Page Content -->
<div class="content">

	<!-- Page Heading/Breadcrumbs -->
	<h1 class="mt-4 mb-3" style="color:#d66204;">Workforce Manager<br />
		<small>Create a New Operative Profile</small>
	</h1>
	
	<?php 
	if( !empty( $feedback ) ){ ?>
		<div class="row">
			<div class="col-lg-6 mb-4 pull-left">
				<h4 class="feedback"><?php echo $feedback ?></h4>
			</div>
		</div>
	<?php
	} ?>

	<!-- Content Row -->
	<div class="row">
		<div class="col-lg-6 mb-4 pull-left">
			<br />
			<br />
			<h3>Add a new operative profile by finding a user from the users list below:</h3>
			<form action="<?php echo base_url( "webapp/workforcemanager/add_profile" ) ?>" method="post" name="sentMessage" id="contactForm" novalidate>
				<div class="control-group form-group">
					<div class="controls">
						<label>Users List:</label>
							<select name="user_id" class="form-control">
								<?php
								if( !empty( $users_list ) ){ ?>
									<option value="">Please select user</option>
									<?php
									foreach( $users_list as $key => $row ){ ?>
										<option value="<?php echo $row->id; ?>"><?php echo ucfirst( $row->first_name ).' '.ucfirst( $row->last_name ); ?></option>
									<?php
									}
								} else { ?>
									<option value="">Please add users to your account</option>
								<?php
								} ?>
							</select>
						<p class="help-block"></p>
					</div>
				</div>

				<div class="control-group form-group">
					<div class="controls">
						<label>Driving Licence:</label>
						<input name="driving_licence" type="text" class="form-control" id="driving_license" required data-validation-required-message="Please enter your Driving Licence number.">
					</div>
				</div>
				<div class="control-group form-group">
					<div class="controls">
						<label>Area:</label>
						<select name="area" type="text" class="form-control" id="area" required data-validation-required-message="Please enter your area of expertise.">
							<option value="">Please select the Area of expertise</option>
							
							<?php 
							if( !empty( $workforce_areas ) ){
								asort( $workforce_areas );
								foreach( $workforce_areas as $area ){ ?>
									<option value="<?php echo $area ?>"><?php echo ucwords( $area ); ?></option>
								<?php
								}
							} else { ?>
								<option value="Gas">Gas</option>
								<option value="Electricity">Electricity</option>
							<?php
							} ?>
						</select>
					</div>
				</div>
				<div class="control-group form-group">
					<div class="controls">
						<label>Is the Operative Active?</label>
						<select name="is_active" class="form-control" id="is_active" required data-validation-required-message="Please specify if operative is active.">
							<option value="yes" selected="selected">Yes</option>
							<option value="no">No</option>
						</select>
					</div>
				</div>
				<div class="control-group form-group">
					<div class="controls">
						<label>Is the Operative a Supervisor?</label>
						<select name="is_supervisor" class="form-control" id="is_supervisor" required data-validation-required-message="Please specify if operative is a Supervisor.">
							<option value="yes">Yes</option>
							<option value="no" selected="selected">No</option>
						</select>
					</div>
				</div>
				<div id="success"></div>
				<!-- For success/fail messages -->
				<button type="submit" class="btn btn-primary" id="sendMessageButton" style="background-color: #d66204;border-color: #d86b3c;">Create Profile</button>
			</form>
		</div>
	</div>
	<!-- /.row -->

</div>
<!-- /.container / content -->