<div class="job-element"  id="job-<?php echo $job_id ?>" job_id = "<?php echo $job_id; ?>" job_slots="<?php echo $job_duration; ?>" job_name="<?php echo $job_type; ?>" job_description="<?php echo $job_type_desc; ?>" job_location="<?php echo $summaryline; ?>">
	 <div class="job-header">
		<h2 class="job-title job_id_<?php echo ( !empty( $job_id ) ? $job_id : false ); ?>">
			<span class="job-el-type"><?php echo ( !empty( $job_type ) ? $job_type : false ); ?></span>
			<span class="job-el-duration pull-right"><?php echo ( !empty( $job_duration ) ?  ( int ) $job_duration : false ); echo " ".( ( $job_duration > 1 ) ? "hours" : "hour" ); ?></span>
			<span class="job-el-postcode pull-right" style="padding-right:10px;"><?php echo $summaryline ?></span>
		</h2>
	 </div>
</div>