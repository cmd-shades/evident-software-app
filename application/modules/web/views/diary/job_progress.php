<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDKbrViiSEEynq_eYpFlOmXXErTCQL8Mqs"></script>

<script src="<?php echo base_url();?>/assets/js/timeline/vis.min.js"></script>
<link href="<?php echo base_url();?>/assets/css/timeline/vis.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url();?>/assets/css/timeline/custom-style.css" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url();?>/assets/js/timeline/engineer-reports.js"></script>

<div class="container-fluid" id="app-body">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12" id="report-content">
			<h1 class="display-3 app-prompt">Please pick the job date for your request</h1>
			<hr/>
			<div class="row">
				<form id="app-prompt-form">
					<div class="form-group">
						<div class="col-sm-2"><input class="form-control" required type="text" id="prompt-date-picker" placeholder="Enter a date here"></div>
						<div class="col-sm-2"><button type="submit" class="btn btn-success" id="prompt-submit">Fetch Jobs</button></div>
					</div>
				</form>
			</div>
			<hr/>
			<script> var u_jobs = (<?php echo $engineer_data; ?>); </script>
			<div id="timeline"><script> var timeline; </script></div>
			<br>
			<div id="timeline-error-message"></div>
			<table id="legend-help">
				<tr>
					<th><span class="legend-dot jobstatus1"> </span> <small> Available </small>&nbsp;&nbsp;</th>
					<th><span class="legend-dot jobstatus2"> </span> <small> Scheduled </small>&nbsp;&nbsp;</th> 
					<th><span class="legend-dot jobstatus3"> </span> <small> Failed </small>&nbsp;&nbsp;</th>
				</tr>
			</table>
		</div>
	</div>
</div>
  