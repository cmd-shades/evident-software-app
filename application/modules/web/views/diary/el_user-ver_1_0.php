<div class="user-element" id="user-<?php echo $user_id ?>-profile" user_id ="<?php echo $user_id ?>" user_fullname="<?php echo $user_fullname; ?>" user_address="<?php echo (!empty($user_address)) ? $user_address : ((!empty($home_user_address)) ? $home_user_address : '') ; ?>" user_slots="<?php echo $user_slots; ?>">
	<div class="user-header">
		<h5 class="user_name"><?php echo $user_fullname; ?><span class="pull-right"><h6><?php echo ( int ) $user_slots;
		echo " ".(!empty($job_duration) && ($job_duration > 1) ? "hours" : "hour"); ?></h6></span></h5>
		<div class="user_address"><?php echo (!empty($user_address)) ? $user_address : ((!empty($home_user_address)) ? $home_user_address : '') ; ?></div>
	</div>
	<br />
	<div class="user-interact">
		<i class="fas fa-plus-square user-info-toggle"></i>
		<i class="fas fa-undo user-reset"></i>
		<i class="fas fa-play user-optimize-waypoints"></i>
	</div>
	 
	<div class="user-more-info">
		<div id="user-<?php echo $user_id; ?>-jobs" user_id = "<?php echo $user_id; ?>" class="sortable-list connectedSortable user-jobs">
			<br />
		</div>

		<hr>
		<table style="width:100%">
			<tbody id="user-<?php echo $user_id ?>-travel-info" class="total-info-content">
				<tr>
					<td>Total Mileage</td>
					<td align="right"><span class = "travel-info-content" id="user-<?php echo $user_id ?>-total-mileage" value="0">0 miles</span></td>
				</tr>
				<tr>
					<td>Total Travel Time</td>
					<td align="right"><span class = "travel-info-content" id="user-<?php echo $user_id ?>-total-travel-time" value="0">0 minutes</span></td>
				</tr>
				<tr>
					<td>Total Work Time</td>
					<td align="right"><span class = "travel-info-content" id="user-<?php echo $user_id ?>-total-work-time" value="0">0 minute</span></td>
				</tr>
				<tr>
					<td>Total Work Travel Time</td>
					<td align="right"><span class = "travel-info-content" id="user-<?php echo $user_id ?>-total-work-travel-time" value="0">0 minute</span></td>
				</tr>
				<tr>
					<td>Total Time</td>
					<td align="right"><span class = "travel-info-content" id="user-<?php echo $user_id ?>-total-time" value="0">0 minute</span></td>
				</tr>
		   </tbody>
		</table>
		<div class="button_wrapper"><button class="commit btn-success" type="submit">Commit</button></div>
	</div>
</div>
  
  
 