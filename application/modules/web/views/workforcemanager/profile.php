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

	<?php
    if (!empty($profile_data)) {
        ?>

	<!-- Page Heading/Breadcrumbs -->
	<h1 class="mt-4 mb-3" style="color:#d66204;">Workforce Manager</h1>

	<?php
        if (!empty($feedback)) { ?>
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
			<legend>Operative Profile: <span style="text-transform:capitalize; font-variant:small-caps; font-weight:bold;font-size: 24px;"><?php echo (!empty($profile_data[0]->full_name)) ? $profile_data[0]->full_name : '' ; ?></span></legend>
			<form action="<?php echo base_url("webapp/workforcemanager/update") ?>" method="post" id="updateProfile" novalidate>
				<input type="hidden" name="postdata[profile_id]" value="<?php echo $profile_data[0]->profile_id; ?>" />
				<input type="hidden" name="postdata[account_id]" value="<?php echo $profile_data[0]->account_id; ?>" />

				<div class="control-group form-group">
					<div class="controls">
						<label>Phone Number:</label>
						<input type="text" class="form-control" id="phone" value="<?php echo (!empty($profile_data[0]->phone)) ? $profile_data[0]->phone : '' ; ?>" readonly="readonly" />
						<p class="help-block"></p>
					</div>
				</div>
				<div class="control-group form-group">
					<div class="controls">
						<label>Email:</label>
						<input type="text" class="form-control" id="email" value="<?php echo (!empty($profile_data[0]->email)) ? $profile_data[0]->email : '' ; ?>" readonly="readonly" />
						<p class="help-block"></p>
					</div>
				</div>
				<div class="control-group form-group">
					<div class="controls">
						<label>Driving Licence:</label>
						<input type="text" name="postdata[driving_licence]" class="form-control" id="driving_licence" value="<?php echo (!empty($profile_data[0]->driving_licence)) ? $profile_data[0]->driving_licence : '' ; ?> " required data-validation-required-message="Please enter your Driving Licence number.">
					</div>
				</div>
				<div class="control-group form-group">
					<div class="controls">
						<label>Area:</label>
						<select name="postdata[area]" type="text" class="form-control" id="area" required data-validation-required-message="Please enter area of expertise.">
							<option value="">Please select the Area of expertise</option>
							<?php
                                if (!empty($workforce_areas)) {
                                    asort($workforce_areas);
                                    foreach ($workforce_areas as $area) { ?>
									<option value="<?php echo $area ?>"  <?php echo (strtolower($area) == strtolower($profile_data[0]->area)) ? ('selected="selected"') : '' ; ?> ><?php echo ucwords($area); ?></option>
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
						<select name="postdata[is_active]" class="form-control" id="is_active" required data-validation-required-message="Please specify if operative is active.">
							<option value="yes" <?php echo ($profile_data[0]->is_active == '1') ? ('selected="selected"') : '' ; ?> >Yes</option>
							<option value="no" <?php echo ($profile_data[0]->is_active != '1') ? ('selected="selected"') : '' ; ?>>No</option>
						</select>
					</div>
				</div>
				<div class="control-group form-group">
					<div class="controls">
						<label>Is the Operative a Supervisor?</label>
						<select name="postdata[is_supervisor]" class="form-control" id="is_supervisor" required data-validation-required-message="Please specify if operative is a Supervisor.">
							<option value="yes" <?php echo ($profile_data[0]->is_supervisor == '1') ? ('selected="selected"') : '' ; ?>>Yes</option>
							<option value="no" <?php echo ($profile_data[0]->is_supervisor != '1') ? ('selected="selected"') : '' ; ?>>No</option>
						</select>
					</div>
				</div>
				<div id="success"></div>
				<!-- For success/fail messages -->
				<button type="submit" class="btn btn-primary" id="updateProfileButton" style="background-color: #d66204;border-color: #d86b3c;">Update Profile</button>
			</form>
			
			<form action="<?php echo base_url("webapp/workforcemanager/delete_profile") ?>" method="post" id="deleteProfile" novalidate>
				<input type="hidden" name="postdata[profile_id]" value="<?php echo $profile_data[0]->profile_id; ?>" />
				<input type="hidden" name="postdata[account_id]" value="<?php echo $profile_data[0]->account_id; ?>" />
				<button type="submit" class="btn btn-primary" id="deleteProfileButton" style="background-color: #ff0000;border-color: #d86b3c;float: right;position: relative;top: -40px;margin-right:0;">Delete Profile</button>
			</form>
		</div>
	</div>
	<!-- /.row -->
	<?php
    } else { ?>
		<h1 class="mt-4 mb-3" style="color:#d66204;">Workforce Manager<br />
			<small>There is no data for this profile in the system</small>
		</h1>
	<?php
    } ?>
</div>
<!-- /.container / content -->