<style>
	div.user-element{
		padding-bottom:0px;
	}
	
	div.user-element, div.panel-default {
		background-color: #0092CD;
		color: #fff !important;
	}
	
	label.engineer-row, a.view-booked-jobs{
		color: #fff;
	}
	
	div.text-lightweight, span.text-lightweight, .text-lightweight{
		font-size:98% !important;
		font-weight: 300  !important;
		margin-bottom: 0px;
	}
	
	.text-normalweight{
		font-weight: 400  !important;
	}
	
	.filter-item {
		padding: 5px 10px;
		padding-left: 15px;
		margin-top: 5px;
		margin-bottom: 5px;
		font-size: 90% !important;
	}
	
	.filter-clear {
		top: 0px;
		width: 20px;
		right: 0px;
		color: #fff;
		background-color: #0191CC;
		height: 20px;
		padding: 2px;
		padding-top: 2px;
		font-size: 14px;
	}
	
	.filter-checkbox + label:last-child {
		margin-bottom: 0;
		font-size: 96%;
	}
</style>

<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="row">
				<div class="has-shadow x_panel">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="row">
							<legend>Bulk Job Reassign</legend>
						</div>
						<div class="row filters">
							
						</div>
					</div>
					<div class="row filters">
						<div class="col-md-4 col-sm-3 col-xs-12">
							<div class="input-group form-group">
								<label class="input-group-addon">Job Date</label>
								<input type="text" id="job_date" name="job_date" class="form-control" value="<?php echo date( 'd-m-Y', strtotime( $job_date ) ); ?>" style="width:68%" />
							</div>
							
							<div class="input-group form-group">
								<label class="input-group-addon">Rebook Date</label>
								<input type="text" id="rebook_date" class="form-control" value="<?php echo !empty( $rebook_date ) ? date( 'd-m-Y', strtotime( $rebook_date ) ) : ''; ?>" style="width:68%" placeholder="New Rebook Date" />
							</div>
							
							<div class="col-md-6 col-xs-12">
								<div class="checkboxs">
									<label class="pointer" for="exclude_successful_jobs" ><input id="exclude_successful_jobs" type="checkbox" <?php echo ( !empty( $exclude_successful_jobs ) ) ? 'checked' : '' ?> checked value="1" /> <small>Exclude Successful Job</small></label>
								</div>
							</div>
						</div>
						
						<div class="col-md-4 col-sm-6 col-xs-12">
							<div id="job-types" class="filter-container">
								<div class="filter-clear pointer" title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
								<div class="filter-heading pointer"><span style="font-size:14px;margin-left:0px;">Job Types <span class="filter-count"></span></span></div>
								<div class="filter-dropdown" style="display:none;">
									<section id="job-type-options" class="active-filters">
										<?php if( !empty( $job_types ) ) { foreach( $job_types as $k => $job_type ){ ?>
											<div class="col-md-12 filter-item">
												<input id="fil-jobtype-<?php echo $k; ?>" type="checkbox" class="filter-checkbox job-type-filters" value="<?php echo $job_type->job_type_id; ?>" >
												<label for = "fil-jobtype-<?php echo $k; ?>" class="filter-label"><?php echo ucwords( $job_type->job_type ); ?></label>
											</div>
										<?php } } ?>
									</section>
								</div>
							</div>
						</div>
						
						<div class="col-md-4 col-sm-3 col-xs-12">
							<div id="regions" class="filter-container">
								<div class="filter-clear pointer" title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
								<div class="filter-heading pointer"><span style="font-size:14px;margin-left:0px;">Regions <span class='filter-count'></span></span></div>
								<div class="filter-dropdown" style="display:none;">
									<section id="region-options"  class="active-filters">
										<?php if( !empty( $regions ) ) { foreach( $regions as $key => $region ){ ?>
											<div class="filter-item">
												<input id="fil-region-<?php echo $key; ?>" type="checkbox" class="filter-checkbox regions" value="<?php echo $region->region_id; ?>" >
												<label for = "fil-region-<?php echo $key; ?>" class="filter-label"><?php echo ucwords( $region->region_name ); ?></label>
											</div>
										<?php } } ?>
									</section>
								</div>
							</div>
						</div>

					</div>
					<hr/>
					<div class="row">
						<form id="assigned-jobs-list-form" >
							<input type="hidden" name="page" value="details" />
							<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
							<input type="hidden" name="rebook_date" value="" />
							<div class="col-md-12 col-sm-12 col-xs-12">
								<legend>Assigned Jobs By Engineer</legend>
								<div id="assigned-jobs-list" style="max-height:400px; height:400px; overflow-y: auto; font-size:90%" >
									
								</div>
								<div class="clearfix"></div>
								<div class="row">
									<hr>
									<div class="col-md-3">
										<button id="rebook-jobs-btn" type="button" class="rebook-jobs-btn btn btn-sm btn-success btn-block">Rebook Selected Jobs</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>


<script>
	$( document ).ready(function(){
		
		var exSuccessJobs	= $( '#exclude_successful_jobs' ).val();
		var jobTypesArr		= {};
		var regionsArr		= {};
		var wheRe			= { 'exclude_successful_jobs': exSuccessJobs };
		var jobDate			= $( '#job_date' ).val();
		
		load_jobs_data( jobDate, jobTypesArr, regionsArr, wheRe );
		
		function load_jobs_data( jobDate, jobTypesArr, regionsArr, wheRe ){
			
			if( $( '#exclude_successful_jobs' ).is(':checked') ){
				wheRe.exclude_successful_jobs = 1;
			} else {
				wheRe.exclude_successful_jobs = 0;
			}
			
			$( '#assigned-jobs-list' ).html( '<p>Loading Jobs data... please wait...</p>' );
			$.ajax({
				url:"<?php echo base_url('webapp/job/fetch_assigned_jobs_data'); ?>",
				method:"POST",
				data:{ job_date:jobDate, job_type_id:jobTypesArr, region_id:regionsArr, where:wheRe },
				dataType: 'json',
				success:function( data ){
					
					if( data.assigned_jobs ){
						$( '#assigned-jobs-list' ).html( data.assigned_jobs );
					} else {
						$( '#assigned-jobs-list' ).html( 'No data available matching your Criteria' );
					}
					
					if( data.job_type_options  ){
						//$( '#job-type-options' ).html( data.job_type_options );						
					} else {
						//$( '#job-type-options' ).html( 'No Job Type options available to filter with' );
					}
					
					if( data.region_options  ){
						//$( '#region-options' ).html( data.region_options );						
					} else {
						//$( '#region-options' ).html( 'No Region options available to filter with' );
					}
					
				}
			});
		}
		
		regionFilter  = new setupResultFilter( $( "#regions" ) )
		jobTypeFilter = new setupResultFilter( $( "#job-types" ) )

		regionFilter.update  = function(){
			jobDate			 = $( '#job_date' ).val();
			jobTypesArr 	 = jobTypeFilter.getFilters();
			regionsArr 	 	 = regionFilter.getFilters();
			load_jobs_data( jobDate, jobTypesArr, regionsArr, wheRe );
		}
		
		jobTypeFilter.update = function(){
			jobDate			 = $( '#job_date' ).val();
			jobTypesArr 	 = jobTypeFilter.getFilters();
			regionsArr 	 	 = regionFilter.getFilters();
			load_jobs_data( jobDate, jobTypesArr, regionsArr, wheRe );
		}
		
		$( function() {
			$( "#job_date" ).datepicker({
				dateFormat: 'dd-mm-yy',
				onSelect: function() {
					var dateObject = $( this ).datepicker( 'getDate' );
					var pickedDate = $.datepicker.formatDate( 'dd-mm-yy', dateObject );
					jobTypesArr 	 = jobTypeFilter.getFilters();
					regionsArr 	 	 = regionFilter.getFilters();
					load_jobs_data( pickedDate, jobTypesArr, regionsArr, wheRe );
				}
			});
		});
		
		$( "#rebook_date" ).datepicker({
			dateFormat: 'dd-mm-yy',
			onSelect: function() {
				var dateObject = $( this ).datepicker( 'getDate' );
				var pickedDate = $.datepicker.formatDate( 'dd-mm-yy', dateObject );
				$( '[name="rebook_date"]' ).val( pickedDate );
			}
		});
		
		$( '#exclude_successful_jobs' ).click( function(){
			jobDate			 = $( '#job_date' ).val();
			jobTypesArr 	 = jobTypeFilter.getFilters();
			regionsArr 	 	 = regionFilter.getFilters();
			load_jobs_data( jobDate, jobTypesArr, regionsArr, wheRe );
		});
		
		$( '#assigned-jobs-list' ).on( 'click', '.assigned-row', function(){
			var engID = $( this ).data( 'engineer_id' );
			$( '#assigned-jobs-list .assigned-jobs-'+engID ).slideToggle( 'slow' );
		});
		
		
		$( '#assigned-jobs-list' ).on( 'click', '.check-all-jobs', function(){
			
			var engID = $( this ).data( 'engineer_ref' );
			
			if( $( this ).is(':checked') ){
				$( '.grouped-jobs-'+engID ).each( function(){
					$( this ).prop( 'checked', true );
				} );
			} else {
				$( '.grouped-jobs-'+engID ).each( function(){
					$( this ).prop( 'checked', false );
				} );
			}
		});
		
		var jobDate  	= '';
		var jobTypes 	= {};
		var Regions  	= {};
		var Postcodes  	= {};
		
		$( '#rebook-jobs-btn' ).click( function(){
			
			if( $( '#include_blank_dates' ).is(':checked') ){
				$( '#include_blank_dates' ).val( 1 );
			} else {
				$( '#include_blank_dates' ).val( 0 );
			}
			
			var checkedJobs 	= $( "#assigned-jobs-list  input[type='checkbox']:checked" ).length;
			var rebookDate 		= $( "[name='rebook_date']" ).val();
		
			if ( checkedJobs < 1 ){
				swal({
					type: 'error',
					text: 'Please select at least one Job to re-assign!'
				})
				return false;
			}
			
			if ( rebookDate.length == 0 ){
				swal({
					type: 'error',
					text: 'Please set the Rebook Date!'
				})
				return false;
			}
			
			var formData = $( "form#assigned-jobs-list-form" ).serialize();
			
			$.ajax({
				url:"<?php echo base_url( 'webapp/job/bulk_reassign_jobs' ); ?>",
				method: "POST",
				data:formData,
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
							location.reload();
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