<div class="user-section" id="user-<?php echo $user_id ?>" name="<?php echo $user_fullname; ?>" address="<?php echo preg_replace('/[^-\w\d .,]/', "", $user_address); ?>">
	<div class="containers">
		<div class="row user-titlebar">
			<div class="col-md-12">
				<div class="user-title"><?php echo $user_fullname; ?> <span class="pull-right"><i class="fas fa-undo user-reset"></i><i class="fas fa-plus-square user-info-toggle"></i></span></div>
			</div>
		</div>
	</div>

  <div><div class="user-address"><?php echo $user_address; ?></div></div>
  
  <div class="more-information" style="display:none">
  
  
	<!-- List of sortable elements inside the user profile-->
	 <div class="user-jobs dragto-sortable" id="user-<?php echo $user_id ?>-jobs" user-id="user-<?php echo $user_id?>">
				
			<!--<div class="static-sortable" id="test-seperator">Non Sortable Element</div>-->
				
		<br>
	 </div>
	 
	 <hr/>
	 
	 <div class="job-totals" >
	 
		<table style="width:100%">

			<tr>
				<td>Total Mileage</td>
				<td align="right"><span id="user-<?php echo $user_id ?>-total-mileage" value=0>0</span>miles</td>
			</tr>

			<tr>
				<td>Total Travel Time</td>
				<td align="right"><span id="user-<?php echo $user_id ?>-total-travel-time" value=0>0</span>minutes</td>
			</tr>

			<tr>
				<td>Total Work Time</td>
				<td align="right"><span id="user-<?php echo $user_id ?>-total-work-time" value=0>0</span>minutes</td>
			</tr>

			<tr>
				<td>Total Time</td>
				<td align="right"><span id="user-<?php echo $user_id ?>-total-time" value=0>0</span>minutes</td>
			</tr>
		</table>
		

	</div>
	</div>
</div>