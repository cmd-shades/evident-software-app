<style>
	.ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default, .ui-button, html .ui-button.ui-state-disabled:hover, html .ui-button.ui-state-disabled:active {
		border: 1px solid #c5c5c5;
		background: lightblue;
		font-weight: normal;
		color: #454545;
	} 
	
	.radio label > span {
		display: inline-block;
		vertical-align: middle;
	}
	
	@media (min-width: 992px){
		.modal-lg {
			top:20px;
			width: 92%;
		}
	}
	
	.ui-widget {
		font-size: 1.4em;
	}
	
	table.margin-bottom-0{
		margin-bottom: 0px !important;
	}
	
	table.margin-top-10{
		margin-top:10px;
	}
	
	div.user-element, div.panel-default {
		background-color: #0092CD;
		color: #fff !important;
	}
	
	label.engineer-row, a.view-booked-jobs{
		color: #fff;
	}
	
	div.engineer-postcode-area-match{
		background: #449d44;
		color: #5c5c5c;
	}
	
	.table .table {
		background-color: transparent;
		color: #fff;
	}
	
	.table a{
		color: #fff;
		font-weight: bold;
	}
	
</style>

<!-- Modal for VIewing and Editing an existing Job Type -->
<div id="route-single-job-modal" class="modal fade route-single-job-modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title" id="myEvidocModalLabel">Route Single Job</h4>
				<hr>
				<div class="row">
					<div class="col-md-12">
						<table style="width:100%" class="stable">
							<tr>
								<td width="28%" class="text-bold">JOB TYPE</td>
								<td width="22%" class="text-bold">POSTCODE</td>
								<td width="22%" class="text-bold">REGION</td>
								<td width="28%" class="text-bold">REQUIRED SKILLS</td>
							</tr>
							<tr class="small">
								<td><?php echo $job_details->job_type; ?></td>
								<td><?php echo (!empty(strtoupper($job_details->address_postcode))) ? strtoupper($job_details->address_postcode) : '' ; ?></td>
								<td><?php echo $job_details->region_name; ?></td>
								<td>
									<div class="row">
										<?php if (!empty($job_type_details->required_skills)) {
										    foreach ($job_type_details->required_skills as $k => $skill) { ?>
											<div class="col-md-6"><i class="far fa-check-circle text-green" title="" ></i> &nbsp;<?php echo $skill->skill_name; ?></div>
										<?php }
										    } ?>
									</div>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>

			<div class="modal-body">
				<div class="row" style="min-height:92%; height:92%; margin-top:10px;" >
					<div class="col-md-4">
						<legend>Job Date <span class="pull-right" id="selected-date"></span></legend>
						<div id="date"></div>
					</div>
					
					<div class="col-md-8">
						<legend>Available Engineers <span class="pull-right"><input id="search_available_engineers" class="form-control hide" type="text" value="" placeholder="Search Engineer" style="margin-top:-8px; border-radius:4px;" /></span></legend>
						<input type="hidden" id="selected-input-date" value="" />
						<div class="row postcode-coverage-area" style="display:none; margin-bottom:5px;">
							<div class="col-md-12">
								<h5 class="text-bold">Postcode Area Coverage</h5>
								<div id="postcode_coverage_area"></div>
							</div>
						</div>
						<div id="available-engineers" style="max-height:400px; height:400px; overflow: auto"><span id="loading">Please select a date to book the Job.</span></div>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button id="route-job-btn" type="button" class="route-job-btn btn btn-sm btn-success">Commit Job</button>
			</div>
		</div>
	</div>
</div>

<script>

	$( document ).ready( function(){
		
		$('input[type="checkbox"]').on('change', function() {
		   $( 'input[type="checkbox"]' ).not(this).prop( 'checked', false );
		});
		
		//Hide/Show Postcode Coverage Areas
		$( '#postcode_coverage_area' ).on( 'click', '.postcode-areas', function(){
			
			$( '#postcode_coverage_area input[type="checkbox"]' ).not( this ).prop( 'checked', false );
			$( '#available-engineers .global-class' ).removeClass( 'currently-showing' );
			
			var postcodeArea = $( this ).data( 'postcode_area' );

			if( $( this ).is(':checked') ){
				
				$( '#available-engineers .global-class' ).each( function(){
					
					var classId = $( this ).data( 'engineer_id' );
					
					if( !$( this ).hasClass( postcodeArea ) ){
						if( !$( this ).hasClass( 'currently-showing' ) ){
							$( this ).addClass( 'hide' );
							$( '#available-engineers .view-booked-jobs-'+classId ).slideUp( 'fast' );
						}
					} else{
						$( this ).removeClass( 'hide' );
						$( this ).addClass( 'currently-showing' );
						$( '#available-engineers .view-booked-jobs-'+classId ).slideToggle( 'slow' );
					}
				});
				
			} else {
				$( '#available-engineers .global-class' ).each( function(){
					
					var classId = $( this ).data( 'engineer_id' );
					$( '#available-engineers .view-booked-jobs-'+classId ).slideUp( 'fast' );
					
					if( !$( this ).hasClass( postcodeArea ) ){
						
						$( this ).removeClass( 'hide' );
						$( this ).removeClass( 'currently-showing' );
						
						
						//$( this ).removeClass( 'hide' );
						/* if( !$( this ).hasClass( 'currently-showing' ) ){
							$( this ).addClass( 'hide' );
						} else{
							
						} */
						
						/* if( !$( this ).hasClass( 'currently-showing' ) ){
							$( this ).addClass( 'hide' );							
						} else {
							$( this ).removeClass( 'hide' );
						} */
						
						
						//$( this ).addClass( 'currently-showing' );
					} else{
						//$( this ).slideUp();
					}
				});
			}
		});
		
		$( '.postcode-coverage-area' ).hide();
		
		var dates 				= ["20/10/2020", "21/10/2020", "22/10/2020", "23/10/2020"];
		var unavailable_dates 	= '<?php echo $dates_with_availability; ?>';
		var dates 				= unavailable_dates;

		function EnabledDates( date ) {
			var string = jQuery.datepicker.formatDate('dd/mm/yy', date);
			return [dates.indexOf(string) != -1];
		}
	
		$( function() {
			$( "#date" ).datepicker({
				changeYear: true,
				changeMonth:true,
				dateFormat: 'dd/mm/yy',
				//minDate: new Date( '01/01/2020' ),
				minDate: 0,
				maxDate: '+2M',
				beforeShowDay: EnabledDates,
				onSelect: function() {
					$( this ).data( 'datepicker' ).inline = true;
					var dateObject = $( this ).datepicker( 'getDate' );
					var pickedDate = $.datepicker.formatDate( 'dd-mm-yy', dateObject );
					$( '#selected-date' ).html( '<strong>'+$.datepicker.formatDate( 'dd-mm-yy', dateObject )+'</strong>' );
					$( '#selected-input-date' ).val( $.datepicker.formatDate( 'yy-mm-dd', dateObject ) );
					loadAvailableResource( pickedDate );
				},
				onClose: function() {
					$( this ).data( 'datepicker' ).inline = false;
				}
			}).datepicker( 'show' );
		});

		//SEARCH CURRENT FILMS
		var $engineerRows = $( '#available-engineers .engineer-row' );
		$( '#search_available_engineers' ).keyup(function() {			
			var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();
			$engineerRows.show().filter(function() {
				var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
				return !~text.indexOf(val);
			}).hide();
		});
		
		$( '#available-engineers' ).on( 'click', '.view-booked-jobs', function(){
			var classId = $( this ).data( 'user_id' );
			$( '#available-engineers .view-booked-jobs-'+classId ).slideToggle( 'slow' );
			
		});
		

		//Get Available Resource
		function loadAvailableResource( refDate ){
			$( '#available-engineers' ).html( 'Loading data... please wait...' );
			$( '#postcode_coverage_area' ).html( '' );
			if( refDate ){
				
				var jobId 		= '<?php echo $job_details->job_id; ?>';
				var jobTypeId 	= '<?php echo $job_details->job_type_id; ?>';
				var regionId  	= '<?php echo !empty($job_details->region_id) ? $job_details->region_id : ""; ?>';
				var jobPostCode	= '<?php echo (!empty(strtoupper($job_details->address_postcode))) ? strtoupper($job_details->address_postcode) : "" ; ?>';
				
				$.ajax({
					url:"<?php echo base_url('webapp/job/fetch_available_resource/'); ?>",
					method:"POST",
					data:{ page:"details", job_id:jobId, ref_date:refDate, job_postcode: encodeURIComponent( jobPostCode ),  job_type_id:jobTypeId, region_id:regionId },
					dataType: 'json',
					beforeSend: function(){
						$( '.postcode-coverage-area' ).hide();
						$( '#loading' ).text( 'Loading data... please wait...' );
					},
					success:function( data ){
						if( data.status == 1 ){
							$( '.postcode-coverage-area' ).show();
							$( '#available-engineers' ).html( data.resource_records );
							$( '#postcode_coverage_area' ).html( data.postcode_coverage );
						} else {
							$( '.postcode-coverage-area' ).hide();
							$( '#available-engineers' ).html( '<div>No resource available for the selected Date</div>' );
							$( '#postcode_coverage_area' ).html( '' );
						}
					}
				});
			} else {
				//
			}

		}
		
		//Route Job
		$( '#route-job-btn' ).click( function(){
			
			var job_Batch 	= {};
			var checkedEng 	= $( "#available-engineers input[type='radio']:checked" ).length;
			
			if ( checkedEng < 1 ){
				swal({
					type: 'error',
					text: 'Please select Engineer to assign the Job to!'
				})
				return false;
			}

			var assignedTo  = $( "#available-engineers input[type='radio']:checked" ).val();
			var jobId 		= '<?php echo $job_details->job_id; ?>';
			var jobDate 	= $( "#selected-input-date" ).val();
			
			job_Batch[ jobId ] = {
				'job_id' : jobId,
				'assigned_to' : assignedTo,
				'job_date' : jobDate,
			};

			$.ajax({
				url:"<?php echo base_url('webapp/diary/commit_jobs'); ?>",
				method: "POST",
				data:{ jobBatch: job_Batch },
				success: function( data ){
					var newData = JSON.parse( data );
					if( newData.status == true || newData.status == 1 || newData.status == 0){
						swal({
							type: 'success',
							title: newData.status_msg,
							showConfirmButton: false,
							timer: 3000
						})

						window.setTimeout( function(){
							location.href = "<?php echo base_url('webapp/job/profile/'); ?>" + jobId;
						}, 3000 );
					} else {
						swal({
							type: 'error',
							title: newData.status_msg
						})
					}
				}
			});

		});	
		
	});
	
</script>