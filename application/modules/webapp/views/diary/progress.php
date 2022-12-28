<script src="<?php echo base_url('assets/js/custom/vis.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/custom/timetable-util.js'); ?>" type="text/javascript"></script>

<link href="<?php echo base_url('assets/css/custom/vis.min.css'); ?>" rel="stylesheet" type="text/css" />


<div class="container-fluid">
	<div class="row" style="background-color:rgb(19,133,193);height:100px">
		<h1 class="display-4 timeline-title">June 2019</h1>
	</div>
</div>
<div id="timetable"></div>
<script>
var groups = new vis.DataSet( generateUsers( <?php  echo json_encode( $user_jobs_formatted ) ?> ) );
var items = new vis.DataSet( generateJobs( <?php  echo json_encode( $totalJobs ) ?> ) );


var options = {
	orientation: 'top',
	stack: false,
	start: new Date().setHours(06,0,0,0),
	end: new Date().setHours(24,0,0,0),
	min: new Date().setHours(06,0,0,0),
	max: new Date().setHours(24,0,0,0),
};

var container 		= document.getElementById( 'timetable' );
timetable_timeline 	= new vis.Timeline( container, items, groups, options );

</script>


<?php /*
<div id="timetableModal" class="modal">
	<div class="modal-content">
		<span class="close">&times;</span>
		<p class="modal-inner-content">Some text in the Modal..</p>
	</div>
</div>
*/ ?>