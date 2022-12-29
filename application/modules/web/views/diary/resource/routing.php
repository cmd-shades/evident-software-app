<script src="<?php echo base_url('assets/js/custom/job_routing.js'); ?>" type="text/javascript"></script>


<style>
	
	.job-header h1, 
	.job-header h2, 
	.job-header h3, 
	.job-header h4, 
	.job-header h4, 
	.job-header h5, 
	.job-header h5{
		margin-top: 0px;
	}

</style>

<div class="x_panel no-border shaded">
	<div class="x_content">
		<?php
            if (!empty($job_details)) {
                include('single_job.php');
            } elseif ($un_booked_jobs) {
                include('multiple_jobs.php');
            }
?>
	</div>
</div>
