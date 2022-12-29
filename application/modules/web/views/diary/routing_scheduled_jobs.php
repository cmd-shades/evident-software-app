<div class="user-element" id="user-<?php echo $user_id ?>-profile" user_id ="<?php echo $user_id ?>" user_fullname="<?php echo $user_fullname; ?>" user_address="<?php echo $user_address; ?>" <?php /* user_slots="<?php echo $user_slots; ?>" */ ?>>
	<div class="user-header">
		<h5 class="user_name"><?php echo $user_fullname; ?><span class="pull-right"><h6><?php /* <?php echo $user_slots; echo " ".( ( !empty( $job_duration ) ) && ( $job_duration > 1 ) ? "hours" : "hour" ); ?> */ ?></h6></span></h5>
		<div class="user_address"><?php echo $user_address; ?></div>
	</div>
	<br>
	<div class="user-interact">
		<i class="fas fa-plus-square user-info-toggle"></i>
		<i class="fas fa-reply-all reset-all-jobs" title="Unassign All Jobs"></i>
	</div>

	<div class="user-more-info">
		<div id="user-<?php echo $user_id; ?>-jobs" user_id = "<?php echo $user_id; ?>" class="sortable-list connectedSortable scheduled-jobs">
			<br>
			<?php foreach ($assigned_jobs as $key => $row) { ?>
				<div class="job-element" id="job-<?php echo $row->job_id ?>" job_id = "<?php echo $row->job_id; ?>" job_slots="<?php echo $row->job_duration; ?>" job_name="<?php echo $row->job_type; ?>" job_description="<?php echo $row->job_type_desc; ?>" job_location="<?php echo $row->summaryline; ?>">
					 <div class="job-header">
						<h2 class="job-title job_id_<?php echo(!empty($row->job_id) ? $row->job_id : false); ?>">
							<span class="job-el-type"><?php echo(!empty($row->job_type) ? $row->job_type : false); ?></span><span class="job-el-duration pull-right" style="font-size: 10px;"><?php echo(!empty($row->job_duration) ? ( int ) $row->job_duration : false);
							echo " ".((!empty($row->job_duration)) && ($row->job_duration > 1) ? "hours" : "hour"); ?></span>
						</h2>
						<div>
							<span class="job-el-postcode pull-right" style="padding-right:10px;"><?php echo $row->summaryline ?></span>
						</div>
					 </div>
				</div>
			<?php } ?>
		</div>
		<?php /*
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
        */ ?>
	</div>
</div>


