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
							<legend>Bulk Job Assign
								<span class="pull-right">
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-6">
											Filter By Contract
										</div>
										<div class="col-md-6 col-sm-6 col-xs-6">
											<select id="contract_id" name="contract_id" class="form-control" style="width:100%; height: 30px; font-size:13px;" >
												<option value="">All (no filter)</option>
												<?php if (!empty($contracts)) {
												    foreach ($contracts as $key => $contract) { ?>
													<option value="<?php echo $contract->contract_id; ?>" <?php echo (!empty($contract_id) && ($contract_id == $contract->contract_id)) ? "selected='selected'" : "" ?> ><?php echo $contract->contract_name; ?></option>
												<?php }
												    } ?>
											</select>
										</div>
									</div>
								</span>
							</legend>
						</div>
					</div>
					<div class="row filters">
						<div class="col-md-4 col-sm-3 col-xs-12">
							
							<div class="input-group form-group">
								<label class="input-group-addon">Due Date</label>
								<input type="text" id="due_date" name="due_date" class="form-control" value="<?php echo date('d-m-Y', strtotime($due_date)); ?>" style="width:68%" placeholder="Due date" />
							</div>
						
							<div class="input-group form-group">
								<label class="input-group-addon">Job Date</label>
								<input type="text" id="job_date" name="job_date" class="form-control" value="<?php echo date('d-m-Y', strtotime($job_date)); ?>" style="width:68%" />
							</div>
							<div class="row">
								<div class="col-md-6 col-xs-12">
									<div class="checkboxs">
										<label class="pointer" for="check-book-jobs" ><input id="check-book-jobs" type="checkbox" checked /> <small>Book selected Jobs for this date</small></label>
									</div>
								</div>
								
								<div class="col-md-6 col-xs-12">
									<div class="checkboxs">
										<label class="pointer" for="include_blank_dates" ><input id="include_blank_dates" type="checkbox" <?php echo (!empty($include_blank_dates)) ? 'checked' : '' ?> checked value="1" /> <small>Show/Hide Blank Dates</small></label>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-4 col-sm-6 col-xs-12">
							<div id="job-types" class="filter-container">
								<div class="filter-clear pointer" title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
								<div class="filter-heading pointer"><span style="font-size:14px;margin-left:0px;">Job Types <span class="filter-count"></span></span></div>
								<div class="filter-dropdown" style="display:none;">
									<section id="job-type-options" class="active-filters row">
										<?php if (!empty($job_types)) {
										    foreach ($job_types as $k => $job_type) { ?>
											<div class="<?php echo (!empty($contract_id)) ? '' : 'col-lg-12' ?> filter-item">
												<input id="fil-jobtype-<?php echo $k; ?>" type="checkbox" class="filter-checkbox job-type-filters" value="<?php echo $job_type->job_type_id; ?>" <?php echo (!empty($contract_id)) ? 'checked' : '' ?> >
												<label for = "fil-jobtype-<?php echo $k; ?>" class="filter-label"><?php echo ucwords($job_type->job_type); ?></label>
											</div>
										<?php }
										    } else { ?>
											<div class="filter-item">
												No records to display
											</div>
										<?php } ?>
									</section>
								</div>
							</div>
						</div>
						
						<div class="col-md-4 col-sm-3 col-xs-12">
							<div id="regions" class="filter-container">
								<div class="filter-clear pointer" title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
								<div class="filter-heading pointer"><span style="font-size:14px;margin-left:0px;">Regions <span class='filter-count'></span></span></div>
								<div class="filter-dropdown" style="display:none;">
									<section id="region-options"  class="active-filters row">
										<?php if (!empty($regions)) {
										    foreach ($regions as $key => $region) { ?>
											<div class="filter-item">
												<input id="fil-region-<?php echo $key; ?>" type="checkbox" class="filter-checkbox regions" value="<?php echo $region->region_id; ?>" >
												<label for = "fil-region-<?php echo $key; ?>" class="filter-label"><?php echo ucwords($region->region_name); ?></label>
											</div>
										<?php }
										    } ?>
									</section>
								</div>
							</div>
						</div>

					</div>
					<hr/>
					<div class="row">
						<form id="un-assigned-jobs-list-form" >
							<input type="hidden" name="page" value="details" />
							<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
							<input type="hidden" name="job_date" id="hidden_job_date" value="<?php echo date('d-m-Y', strtotime($job_date)); ?>" />
							<input type="hidden" name="due_date" id="hidden_due_date" value="<?php echo date('d-m-Y', strtotime($due_date)); ?>" />
							<input type="hidden" name="book_jobs" id="book_jobs" value="1" />
							<input type="hidden" name="include_blank_dates" id="include_blank_dates" value="0" />
							<div class="col-md-8 col-sm-8 col-xs-8">
								<legend>Un Assigned Jobs <small><i class="far fa-question-circle text-blue" title="For optimal results, this page only loads a maximum of 300 Jobs each time"></i></small></legend>
								<div id="un-assigned-jobs-list" style="max-height:400px; height:400px; overflow-y: auto; font-size:90%" >
									
								</div>
							</div>
							<div class="col-md-4 col-sm-4 col-xs-4">
								<legend>Available Engineers <span class="pull-right"><input id="search_available_engineers" class="form-control hide" type="text" value="" placeholder="Search Engineer" style="margin-top:-8px; border-radius:4px;  width:90%; float:right" /></span></legend>
								<div id="available-engineers" style="max-height:400px; height:400px; overflow-y: auto; font-size:90%" >
									
								</div>
								<div class="clearfix"></div>
								<div class="row">
									<br/>
									<div class="col-md-12">
										<button id="assign-jobs-btn" type="button" class="assign-jobs-btn btn btn-sm btn-success btn-block">Assign Selected Jobs</button>
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
		
		var contractID		= $( '#contract_id option:selected' ).val();
		var jobTypesArr		= {};
		var regionsArr		= {};
		var jobDate			= $( '#job_date' ).val();
		var dueDate			= $( '#due_date' ).val();
		var showBlankDates	= $( '#include_blank_dates' ).val();
		
		load_jobs_data( contractID, jobDate, jobTypesArr, regionsArr, showBlankDates,  dueDate );
		
		function load_jobs_data( contractID, jobDate, jobTypesArr, regionsArr, showBlankDates, dueDate ){
			$( '#un-assigned-jobs-list' ).html( '<p>Loading Jobs data... please wait...</p>' );
			$( '#available-engineers' ).html( '<p>Loading data... please wait...</p>' );
			$.ajax({
				url:"<?php echo base_url('webapp/job/fetch_un_assigned_jobs_data'); ?>",
				method:"POST",
				data:{ contract_id:contractID, job_date:jobDate, job_type_id:jobTypesArr, region_id:regionsArr, include_blank_dates:showBlankDates, due_date:dueDate },
				dataType: 'json',
				success:function( data ){
					
					if( data.un_assigned_jobs ){
						$( '#un-assigned-jobs-list' ).html( data.un_assigned_jobs );
					} else {
						$( '#un-assigned-jobs-list' ).html( 'No data available matching your Criteria' );
					}
					
					if( data.available_resource ){
						$( '#available-engineers' ).html( data.available_resource );					
					} else {
						$( '#available-engineers' ).html( 'No Engineer Resource available matching your Criteria' );
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
		
		regionFilter  = new setupResultFilter( $( "#regions" ) );
		jobTypeFilter = new setupResultFilter( $( "#job-types" ) );

		regionFilter.update  = function(){
			jobDate			 = $( '#job_date' ).val();
			dueDate			 = $( '#due_date' ).val();
			jobTypesArr 	 = jobTypeFilter.getFilters();
			regionsArr 	 	 = regionFilter.getFilters();
			load_jobs_data( contractID, jobDate, jobTypesArr, regionsArr, showBlankDates,  dueDate );
		}
		
		jobTypeFilter.update = function(){
			jobDate			 = $( '#job_date' ).val();
			dueDate			 = $( '#due_date' ).val();
			jobTypesArr 	 = jobTypeFilter.getFilters();
			regionsArr 	 	 = regionFilter.getFilters();
			load_jobs_data( contractID, jobDate, jobTypesArr, regionsArr, showBlankDates,  dueDate );
		}
		
		$( function() {
			$( "#job_date" ).datepicker({
				dateFormat: 'dd-mm-yy',
				onSelect: function() {
					var dueDate			 = $( '#job_date' ).val();
					var dateObject = $( this ).datepicker( 'getDate' );
					var pickedDate = $.datepicker.formatDate( 'dd-mm-yy', dateObject );
					jobTypesArr 	 = jobTypeFilter.getFilters();
					regionsArr 	 	 = regionFilter.getFilters();
					$( '#hidden_job_date' ).val( pickedDate );
					
					load_jobs_data( contractID, pickedDate, jobTypesArr, regionsArr, showBlankDates, dueDate );
				}
			});
		});
		
		$( function() {
			$( "#due_date" ).datepicker({
				dateFormat: 'dd-mm-yy',
				onSelect: function() {
					var jobDate			 = $( '#job_date' ).val();
					var dateObject = $( this ).datepicker( 'getDate' );
					var dueDate = $.datepicker.formatDate( 'dd-mm-yy', dateObject );
					jobTypesArr 	 = jobTypeFilter.getFilters();
					regionsArr 	 	 = regionFilter.getFilters();
					$( '#hidden_due_date' ).val( dueDate );
					
					load_jobs_data( contractID, jobDate, jobTypesArr, regionsArr, showBlankDates, dueDate );
				}
			});
		});
		
		$( '#un-assigned-jobs-list' ).on( 'click', '.un-assigned-row', function(){
			var jobTypeId = $( this ).data( 'job_type_id' );
			$( '#un-assigned-jobs-list .job-type-jobs-'+jobTypeId ).slideToggle( 'slow' );
		});
		
		
		$( '#un-assigned-jobs-list' ).on( 'click', '.check-all-jobs', function(){
		
			var jobTypeId = $( this ).data( 'job_type_ref' );
			
			if( $( this ).is(':checked') ){
				$( '.grouped-jobs-'+jobTypeId ).each( function(){
					$( this ).prop( 'checked', true );
				} );
			} else {
				$( '.grouped-jobs-'+jobTypeId ).each( function(){
					$( this ).prop( 'checked', false );
				} );
			}
		});
		
		var jobDate  	= '';
		var jobTypes 	= {};
		var Regions  	= {};
		var Postcodes  	= {};
		
		$( '#assign-jobs-btn' ).click( function(){
			
			var checkedEng 		= $( "#available-engineers input[type='radio']:checked" ).length;
			var checkedJobs 	= $( "#un-assigned-jobs-list  input[type='checkbox']:checked" ).length;
		
			if ( checkedJobs < 1 ){
				swal({
					type: 'error',
					text: 'Please select at least one Job to re-assign!'
				})
				return false;
			}
			
			if ( checkedEng < 1 ){
				swal({
					type: 'error',
					text: 'Please select an Engineer to assign the Jobs to!'
				})
				return false;
			}
			
			var formData = $( "form#un-assigned-jobs-list-form" ).serialize();
			
			$.ajax({
				url:"<?php echo base_url('webapp/job/bulk_reassign_jobs'); ?>",
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
		
		$( '#check-book-jobs' ).click( function(){
			if( $( this ).is(':checked') ){
				$( '#book_jobs' ).val( 1 );
			} else {
				$( '#book_jobs' ).val( 0 );
			}
		});
		
		$( '#include_blank_dates' ).click( function(){
			if( $( this ).is(':checked') ){
				$( '#include_blank_dates' ).val( 1 );
			} else {
				$( '#include_blank_dates' ).val( 0 );
			}
			
			
			jobDate			 = $( '#job_date' ).val();
			dueDate			 = $( '#due_date' ).val();
			jobTypesArr 	 = jobTypeFilter.getFilters();
			regionsArr 	 	 = regionFilter.getFilters();
			showBlankDates 	 = $( '#include_blank_dates' ).val();
			load_jobs_data( contractID, jobDate, jobTypesArr, regionsArr, showBlankDates,  dueDate );

		});

		$( '#contract_id' ).change( function(){
			
			if( $( '#include_blank_dates' ).is(':checked') ){
				$( '#include_blank_dates' ).val( 1 );
			} else {
				$( '#include_blank_dates' ).val( 0 );
			}
			
			var contractID  	= $( 'option:selected', this ).val();
			var	showBlankDates 	= $( '#include_blank_dates' ).val();
			var url 			= location.protocol + '//' + location.host + location.pathname;    
			
			if( contractID.length !== 0 ){
				if( url.indexOf( '?' ) > -1 ){
				   url += '&contract_id='+contractID;			   
				} else {
				   url += '?contract_id='+contractID;
				}
				
				if( showBlankDates ){
					url += '&include_blank_dates='+showBlankDates;
				}
				
			} else {
				if( showBlankDates ){
					url += '?include_blank_dates='+showBlankDates;
				}
			}

			window.location.href = url;
		});		
	});
</script>