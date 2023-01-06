<div class="row">
	<div class="row">
		<div class="x_panel no-border">
			<div class="row">
				<div class="x_content">
					<div class="row" style="margin-bottom:10px;">
						<div class="col-lg-6 col-md-6 col-sm-8">
							<div class="row">
							
								<?php /* 
								<div class="col-md-3">
									<div id="job-date" class="filter-container" style="z-index:998">
										<div class="filter-clear pointer" title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
										<div class="filter-heading"><i class="fas fa-filter filter-icon"></i><span style="font-size:14px;margin-left:10px;">Job Date <span class='filter-count'></span></span></div>
										<div class="filter-dropdown" style="display:none; z-index:998">
											<section class="active-filters">
												<div class="filter-item">
													<label for = "fil-ut-1" class="filter-label filter-start-date">From</label>
													<input id="fil-ut-1" type="text" class="datepicker" value=""  placeholder="DD/MM/YY" data-date-format="DD/MM/Y" style="border: none;" />
												</div>
												<div class="filter-item">
													<label for = "fil-ut-2" class="filter-label filter-end-date">To</label>
													<input id="fil-ut-2" type="text" class="datepicker" value="" placeholder="DD/MM/YY" data-date-format="DD/MM/Y" style="border: none;" />
												</div>
											</section>
										</div>
									</div>
								</div> */ ?>								
								
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 filter-upper-container">
									<div id="job-date" class="filter-container" style="z-index:999">
										<div class="filter-clear pointer" title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
										<div class="filter-heading"><i class="fas fa-filter filter-icon"></i><span style="font-size:14px;margin-left:10px;">Job Date <span class='filter-count'></span></span></div>
										<div class="filter-dropdown" style="display:none; z-index:998">
											<section class="active-filters">
												<div class="filter-item">
													<label for = "fil-ut-1" class="filter-label filter-start-date">From</label>
													<input id="fil-ut-1" type="text" class="datepicker-start" value="<?php echo !empty( $date_from ) ? $date_from : ''; ?>"  
			
													data-date-format="DD/MM/Y" style="border: none;" />
												</div>
												<div class="filter-item">
													<label for = "fil-ut-2" class="filter-label filter-end-date">To</label>
													<input id="fil-ut-2" type="text" class="datepicker-end" value="<?php echo !empty( $date_to ) ? $date_to : ''; ?>" 
	
													data-date-format="DD/MM/Y" style="border: none;" />
												</div>
											</section>
										</div>
									</div>
								</div>
								<?php /* <div class="col-md-3">
									<div id="contracts" class="filter-container" style="z-index:999" >
										<div class="filter-clear pointer" title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
										<div class="filter-heading pointer"><span style="font-size:14px;margin-left:0px;">Contract <span class='filter-count'></span></span></div>
										<div class="filter-dropdown" style="display:none; z-index:998">
											<section id="contract-options"  class="active-filters row" style="z-index:998">
												<?php if( !empty( $contracts ) ) { foreach( $contracts as $ckey => $contract ){ ?>
													<div class="filter-item">
														<input id="fil-contract-<?php echo $ckey; ?>" type="checkbox" class="filter-checkbox contract" value="<?php echo $contract->contract_id; ?>" >
														<label for = "fil-contract-<?php echo $ckey; ?>" class="filter-label"><?php echo ucwords( $contract->contract_name ); ?></label>
													</div>
												<?php } } ?>
											</section>
										</div>
									</div>
								</div> */ ?>
								
								<div class="col-md-4">
									<div id="regions" class="filter-container" style="z-index:999" >
										<div class="filter-clear pointer" title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
										<div class="filter-heading pointer"><span style="font-size:14px;margin-left:0px;">Region <span class='filter-count'></span></span></div>
										<div class="filter-dropdown" style="display:none;">
											<section id="region-options"  class="active-filters row">
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
								<div class="col-md-4">
									<div id="job-types" class="filter-container" style="z-index:999" >
										<div class="filter-clear pointer" title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
										<div class="filter-heading pointer"><span style="font-size:14px;margin-left:0px;">Job Types <span class="filter-count"></span></span></div>
										<div class="filter-dropdown" style="display:none;">
											<section id="job-type-options" class="active-filters row">
												<?php if( !empty( $job_types ) ) { foreach( $job_types as $k => $job_type ){ ?>
													<div class="col-md-6 filter-item">
														<input id="fil-jobtype-<?php echo $k; ?>" type="checkbox" class="filter-checkbox job-type-filters" value="<?php echo $job_type->job_type_id; ?>" >
														<label for = "fil-jobtype-<?php echo $k; ?>" class="filter-label"><?php echo ucwords( $job_type->job_type ); ?></label>
													</div>
												<?php } } ?>
											</section>
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<div id="job-assignees" class="filter-container" style="z-index:1" >
										<div class="filter-clear pointer" title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
										<div class="filter-heading pointer"><span style="font-size:14px;margin-left:0px;">Assignee <span class="filter-count"></span></span></div>
										<div class="filter-dropdown" style="display:none;">
											<section id="assignee-options" class="active-filters row">
												<?php if( !empty( $operatives ) ) { foreach( $operatives as $okey => $operative ){ ?>
													<div class="filter-item">
														<input id="fil-assignee-<?php echo $okey; ?>" type="checkbox" class="filter-checkbox assignee-filters" value="<?php echo $operative->id; ?>" >
														<label for = "fil-assignee-<?php echo $okey; ?>" class="filter-label"><?php echo ucwords( $operative->first_name.' '.$operative->last_name ); ?></label>
													</div>
												<?php } } ?>
											</section>
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<div id="job-statuses" class="filter-container" style="z-index:1" >
										<div class="filter-clear pointer" title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
										<div class="filter-heading pointer"><span style="font-size:14px;margin-left:0px;">Job Status <span class="filter-count"></span></span></div>
										<div class="filter-dropdown" style="display:none;">
											<section id="job-status-options" class="active-filters row">
												<?php if( !empty( $job_statuses ) ) { foreach( $job_statuses as $sk => $job_status ) { ?>
													<div class="filter-item">
														<input id="fil-job-status-<?php echo $sk; ?>" type="checkbox" class="filter-checkbox job-status-filters" value="<?php echo $job_status->status_id; ?>" >
														<label for = "fil-job-status-<?php echo $sk; ?>" class="filter-label"><?php echo ucwords( $job_status->job_status ); ?></label>
													</div>
												<?php } } ?>
											</section>
										</div>
									</div>
								</div>
								<?php /* <div class="col-md-4">
									<div id="tracking-statuses" class="filter-container" style="z-index:1" >
										<div class="filter-clear pointer" title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
										<div class="filter-heading pointer"><span style="font-size:14px;margin-left:0px;">Tracking Status <span class="filter-count"></span></span></div>
										<div class="filter-dropdown" style="display:none;">
											<section id="tracking-status-options" class="active-filters row">
												<?php if( !empty( $tracking_statuses ) ) { foreach( $tracking_statuses as $tkey => $tracking_status ){ ?>
													<div class="filter-item">
														<input id="fil-tracking-status-<?php echo $tkey; ?>" type="checkbox" class="filter-checkbox tracking-status-filters" value="<?php echo $tracking_status->job_tracking_id; ?>" >
														<label for = "fil-tracking-status-<?php echo $tkey; ?>" class="filter-label"><?php echo ucwords( $tracking_status->job_tracking_status ); ?></label>
													</div>
												<?php } } ?>
												<div class="filter-item">
													<input id="fil-tracking-status-blnk" type="checkbox" class="filter-checkbox tracking-status-filters" value="_blanks" >
													<label for = "fil-tracking-status-blnk" class="filter-label"><strong>Blanks / Not Set</strong></label>
												</div>
											</section>
										</div>
									</div>
								</div> */ ?>
								
								<div class="col-md-4">
									<div id="disciplines" class="filter-container" style="z-index:998" >
										<div class="filter-clear pointer" title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
										<div class="filter-heading pointer"><span style="font-size:14px;margin-left:0px;">Disciplines <span class="filter-count"></span></span></div>
										<div class="filter-dropdown" style="display:none;">
											<section id="discipline-options" class="active-filters row">
												<?php if( !empty( $disciplines ) ) { foreach( $disciplines as $dk => $discipline ){ ?>
													<div class="col-md-12 filter-item">
														<input id="fil-discipline-<?php echo $dk; ?>" type="checkbox" class="filter-checkbox discipline-filters" value="<?php echo $discipline->discipline_id; ?>" <?php echo ( !empty( $discipline_id ) && ( $discipline_id == $discipline->discipline_id ) ) ? 'checked' : ''; ?> >
														<label for = "fil-discipline-<?php echo $dk; ?>" class="filter-label"><?php echo ucwords( $discipline->account_discipline_name ); ?></label>
													</div>
												<?php } } ?>
											</section>
										</div>
									</div>
								</div>
								
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 zindex_99">
							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12 zindex_99 pull-right">
									<?php $this->load->view( 'webapp/_partials/search_bar' ); ?>
								</div>
							</div>
							
							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12 zindex_99 pull-right">									
									<?php if( in_array( $this->user->account_id, [8] ) ) {?>
										<div style="background-color:#5cb85c; margin-top:10px; height: 32px; width: 14vw;" >
											<button id="pull-site-jobs" type="button" class="btn btn-block btn-success" style="border-color:#5cb85c " >Pull Tesseract Jobs</button>
										</div>
									<?php } ?>									
								</div>
							</div>
						</div>
					</div>

					<div class="clearfix"></div>
					<div class="table-responsive alert alert-ssid alert-results" role="alert" style="overflow-y: hidden;" >
						<table id="datatable-standard" class="table table-responsive" style="margin-bottom:0px; font-size:90%; font-weight:300" >
							<thead>
								<tr>
									<th width="4%">Job</th>
									<th width="18%">Job Type</th>
									<th width="8%">Job Date</th>
									<th width="15%">Works Required</th>
									<th width="15%">Building Name</th>
									<th width="12%">Assignee</th>
									<th width="10%">Status</th>
									<th width="7%">Discipline</th>
									<th width="11%">Region</th>
									<!-- <th width="12%">Contract</th> -->
								</tr>
							</thead>
							<tbody id="table-results">

							</tbody>
						</table>
					</div>

					<div class="clearfix"></div>
					
					<!-- Modal for pulling Jobs By Tesseract Site Number -->
					<div id="pull-tesseract-jobs-modal" class="modal fade pull-tesseract-jobs-modal" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog modal-lg">
							<form id="pull-site-jobs-form" >
								<input type="hidden" name="page" value="details" />
								<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
										<h4 class="modal-title" id="myModalLabel">Pull Tesseract Jobs</h4>
										<small id="feedback-message"></small>
									</div>

									<div class="modal-body">
										<div class="row">
											<div class="col-md-6 col-sm-6">
												<legend>Pull By Site Number</legend>
												<div class="input-group form-group">
													<label class="input-group-addon">Site Numbers <small class="hide"><em>(Comma seperated)</em></small></label>
													<input id="site_numbers" name="site_numbers" class="form-control" type="text" placeholder="Site Numbers" value="" />
												</div>
												<div class="row">
													<div class="modal-footer pull-left">
														<button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">&nbsp;&nbsp;&nbsp;&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;</button>
														<button id="pull-jobs-by-site-number" type="button" class="btn btn-sm btn-success">Fetch Site Jobs</button>
													</div>
												</div>
											</div>
											
											<div class="col-md-6 col-sm-6">
												<legend>Pull By Call Number</legend>
												<div class="input-group form-group">
													<label class="input-group-addon">Call Number <small class="hide"><em>(Comma seperated)</em></small></label>
													<input id="call_numbers" name="call_numbers" class="form-control" type="text" placeholder="Call Numbers" value="" />
												</div>
												<div class="row">
													<div class="modal-footer">
														<button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">&nbsp;&nbsp;&nbsp;&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;</button>
														<button id="pull-jobs-by-call-number" type="button" class="btn btn-sm btn-success">Fetch Single Job</button>
													</div>
												</div>												
											</div>
										</div>
									</div>
									
								</div>
							</form>
						</div>
					</div>
					
				</div>
			</div>
		</div>
	</div>
	

	
</div>

<script type="text/javascript">
	$( document ).ready(function(){

		var search_str   		= null;
		var job_date_from       = function(){
			val = $( '#fil-ut-1' ).val();
			console.log('job_date_from', val)
			return val;
		}
		var job_date_to         = $( '#fil-ut-2' ).val();
		var disciplineId        = "<?php echo !empty( $discipline_id ) ? $discipline_id : '' ?>";
		var overdueJobs         = "<?php echo !empty( $overdue_jobs )  ? $overdue_jobs 	: '' ?>";
		
		var start_index	 		= 0;
		var where = {
			'status_id'			: [],
			'job_type_id'		: [],
			'job_tracking_id'	: [],
			'contract_id'		: [],
			'region_id'			: [],
			'assigned_to'		: [],
			'discipline_id'		: []
		};
		
		//Default parameters
		if( job_date_from ){
			where.date_from 	= job_date_from;
		}
		
		if( job_date_to ){
			where.date_to   	= $( '#fil-ut-2' ).val();
		}	
		
		if( disciplineId ){
			where.discipline_id.push( disciplineId );
		}
		
		if( overdueJobs ){
			where.overdue_jobs = overdueJobs;
		}
		
		var contractFilter  	= new setupResultFilter( $( "#contracts" ) );
		var regionFilter  		= new setupResultFilter( $( "#regions" ) );
		var jobTypeFilter 		= new setupResultFilter( $( "#job-types" ) );
		var jobStatusFilter 	= new setupResultFilter( $( "#job-statuses" ) );
		var jobTrackingFilter 	= new setupResultFilter( $( "#tracking-statuses" ) );
		var jobAssigneeFilter 	= new setupResultFilter( $( "#job-assignees" ) );
		var disciplinesFilter 	= new setupResultFilter( $( "#disciplines" ) );

		//Pagination links
		$( "#table-results" ).on( "click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $( this ).find('a').data('ciPaginationPage');
			var search_str 	= encodeURIComponent( $( '#search_term' ).val() );
			
			where.date_from 		= $( '#fil-ut-1' ).val();
			where.date_to   		= $( '#fil-ut-2' ).val();
			where.status_id 	 	= jobStatusFilter.getFilters();
			where.job_type_id 	 	= jobTypeFilter.getFilters();
			where.job_tracking_id 	= jobTrackingFilter.getFilters();
			where.contract_id 	 	= contractFilter.getFilters();
			where.region_id 	 	= regionFilter.getFilters();
			where.assigned_to 	 	= jobAssigneeFilter.getFilters();
			where.discipline_id 	= disciplinesFilter.getFilters();
			
			load_data( search_str, where, start_index );
		});


		jobDateFilter = new setupJobDateFilter( $( "#job-date" ) )
		jobDateFilter.update = function(){

			where.date_from 		= $( '#fil-ut-1' ).val();
			where.date_to   		= $( '#fil-ut-2' ).val();
			where.status_id 	 	= jobStatusFilter.getFilters();
			where.job_type_id 	 	= jobTypeFilter.getFilters();
			where.job_tracking_id 	= jobTrackingFilter.getFilters();
			where.contract_id 	 	= contractFilter.getFilters();
			where.region_id 	 	= regionFilter.getFilters();
			where.assigned_to 	 	= jobAssigneeFilter.getFilters();
			where.discipline_id 	= disciplinesFilter.getFilters();
			
			var search_str 	= encodeURIComponent( $( '#search_term' ).val() );
			load_data( search_str, where, start_index );
		}

		contractFilter.update  = function(){
			var search_str 	 = encodeURIComponent( $( '#search_term' ).val() );
			//var start_index = $( '#table-results' ).find( 'a' ).data( 'ciPaginationPage' );
			
			where.date_from 		= $( '#fil-ut-1' ).val();
			where.date_to   		= $( '#fil-ut-2' ).val();
			where.status_id 	 	= jobStatusFilter.getFilters();
			where.job_type_id 	 	= jobTypeFilter.getFilters();
			where.job_tracking_id 	= jobTrackingFilter.getFilters();
			where.contract_id 	 	= contractFilter.getFilters();
			where.region_id 	 	= regionFilter.getFilters();
			where.assigned_to 	 	= jobAssigneeFilter.getFilters();
			where.discipline_id 	= disciplinesFilter.getFilters();
			
			load_data( search_str, where, start_index );
		}
		
		regionFilter.update  = function(){
			var search_str 	 = encodeURIComponent( $( '#search_term' ).val() );
			//var start_index = $( '#table-results' ).find( 'a' ).data( 'ciPaginationPage' );
			
			where.date_from 		= $( '#fil-ut-1' ).val();
			where.date_to   		= $( '#fil-ut-2' ).val();
			where.status_id 	 	= jobStatusFilter.getFilters();
			where.job_type_id 	 	= jobTypeFilter.getFilters();
			where.job_tracking_id 	= jobTrackingFilter.getFilters();
			where.contract_id 	 	= contractFilter.getFilters();
			where.region_id 	 	= regionFilter.getFilters();
			where.assigned_to 	 	= jobAssigneeFilter.getFilters();
			where.discipline_id 	= disciplinesFilter.getFilters();

			load_data( search_str, where, start_index );
		}
		
		jobTypeFilter.update = function(){
			var search_str 	= encodeURIComponent( $( '#search_term' ).val() );
			//var start_index = $( '#table-results' ).find( 'a' ).data( 'ciPaginationPage' );
			
			where.date_from 		= $( '#fil-ut-1' ).val();
			where.date_to   		= $( '#fil-ut-2' ).val();
			where.status_id 	 	= jobStatusFilter.getFilters();
			where.job_type_id 	 	= jobTypeFilter.getFilters();
			where.job_tracking_id 	= jobTrackingFilter.getFilters();
			where.contract_id 	 	= contractFilter.getFilters();
			where.region_id 	 	= regionFilter.getFilters();
			where.assigned_to 	 	= jobAssigneeFilter.getFilters();
			where.discipline_id 	= disciplinesFilter.getFilters();
			
			load_data( search_str, where, start_index );
		}

		jobStatusFilter.update  = function(){
			var search_str 	 	= encodeURIComponent( $( '#search_term' ).val() );
			//var start_index  	= $( this ).find('a').data('ciPaginationPage');
			
			where.date_from 		= $( '#fil-ut-1' ).val();
			where.date_to   		= $( '#fil-ut-2' ).val();
			where.status_id 	 	= jobStatusFilter.getFilters();
			where.job_type_id 	 	= jobTypeFilter.getFilters();
			where.job_tracking_id 	= jobTrackingFilter.getFilters();
			where.contract_id 	 	= contractFilter.getFilters();
			where.region_id 	 	= regionFilter.getFilters();
			where.assigned_to 	 	= jobAssigneeFilter.getFilters();
			where.discipline_id 	= disciplinesFilter.getFilters();

			load_data( search_str, where, start_index );
		}		

		jobTrackingFilter.update  = function(){
			var search_str 	 	= encodeURIComponent( $( '#search_term' ).val() );
			//var start_index = $( '#table-results' ).find( 'a' ).data( 'ciPaginationPage' );
			
			where.date_from 		= $( '#fil-ut-1' ).val();
			where.date_to   		= $( '#fil-ut-2' ).val();
			where.status_id 	 	= jobStatusFilter.getFilters();
			where.job_type_id 	 	= jobTypeFilter.getFilters();
			where.job_tracking_id 	= jobTrackingFilter.getFilters();
			where.contract_id 	 	= contractFilter.getFilters();
			where.region_id 	 	= regionFilter.getFilters();
			where.assigned_to 	 	= jobAssigneeFilter.getFilters();
			where.discipline_id 	= disciplinesFilter.getFilters();

			load_data( search_str, where, start_index );
		}
		
		jobAssigneeFilter.update  = function(){
			var search_str 	 	= encodeURIComponent( $( '#search_term' ).val() );
			//var start_index = $( '#table-results' ).find( 'a' ).data( 'ciPaginationPage' );
			
			where.date_from 		= $( '#fil-ut-1' ).val();
			where.date_to   		= $( '#fil-ut-2' ).val();
			where.status_id 	 	= jobStatusFilter.getFilters();
			where.job_type_id 	 	= jobTypeFilter.getFilters();
			where.job_tracking_id 	= jobTrackingFilter.getFilters();
			where.contract_id 	 	= contractFilter.getFilters();
			where.region_id 	 	= regionFilter.getFilters();
			where.assigned_to 	 	= jobAssigneeFilter.getFilters();
			where.discipline_id 	= disciplinesFilter.getFilters();

			load_data( search_str, where, start_index );
		}
		
		disciplinesFilter.update  = function(){
			var search_str 	 	= encodeURIComponent( $( '#search_term' ).val() );
			
			where.date_from 		= $( '#fil-ut-1' ).val();
			where.date_to   		= $( '#fil-ut-2' ).val();
			where.status_id 	 	= jobStatusFilter.getFilters();
			where.job_type_id 	 	= jobTypeFilter.getFilters();
			where.job_tracking_id 	= jobTrackingFilter.getFilters();
			where.contract_id 	 	= contractFilter.getFilters();
			where.region_id 	 	= regionFilter.getFilters();
			where.assigned_to 	 	= jobAssigneeFilter.getFilters();
			where.discipline_id 	= disciplinesFilter.getFilters();

			load_data( search_str, where, start_index );
		}

		load_data( search_str, where, start_index );

		function load_data( search_str, where, start_index ){
			$.ajax({
				url:"<?php echo base_url( 'webapp/job/job_search' ); ?>",
				method:"POST",
				data:{ search_term:search_str, where:where, start_index:start_index },
				success:function(data){
					$('#table-results').html(data);
				}
			});
		}

		$( '#search_term' ).keyup(function(){

			var search 		= encodeURIComponent( $(this).val() );
			
			where.date_from 		= $( '#fil-ut-1' ).val();
			where.date_to   		= $( '#fil-ut-2' ).val();
			where.status_id 	 	= jobStatusFilter.getFilters();
			where.job_type_id 	 	= jobTypeFilter.getFilters();
			where.job_tracking_id 	= jobTrackingFilter.getFilters();
			where.contract_id 	 	= contractFilter.getFilters();
			where.region_id 	 	= regionFilter.getFilters();
			where.discipline_id 	= disciplinesFilter.getFilters();
			
			if( search.length > 0 ){
				load_data( search, where, start_index );
			}else{
				load_data( search_str, where, start_index );
			}
		});

		$( '#pull-site-jobs' ).click(function(){
			$("#pull-tesseract-jobs-modal").modal( "show" );
		});
		
		$( '#pull-jobs-by-site-number' ).click( function(){
			
			var siteNums = $( '#site_numbers' ).val();
			
			if( siteNums.length == 0 || siteNums.length === null ){
				swal({
					type: 'error',
					title: 'Please provide at least 1 Site Number'
				});
				return false;
			}
			
			$.ajax({
				url:"<?php echo base_url('webapp/job/fetch_tesseract_jobs_by_site_number/' ); ?>",
				method:"POST",
				data:{ page:"details", site_number:siteNums },
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){
						
						$("#pull-tesseract-jobs-modal").modal( "hide" );
						
						swal({
							type: 'success',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 2100
						})
						window.setTimeout(function(){
							var new_url = window.location.href.split('?')[0];
							window.location.href = new_url;
						} ,1000);
					} else {
						swal({
							type: 'error',
							title: data.status_msg
						})
					}
				}
			});
			
		});
		
		/*
		* Fetch Calls by Call Number
		*/
		$( '#pull-jobs-by-call-number' ).click( function(){
			
			var callNums = $( '#call_numbers' ).val();
			
			if( callNums.length == 0 || callNums.length === null ){
				swal({
					type: 'error',
					title: 'Please provide at least 1 Call Number',
				});
				return false;
			}
			
			$.ajax({
				url:"<?php echo base_url('webapp/job/fetch_tesseract_jobs_by_call_number/' ); ?>",
				method:"POST",
				data:{ page:"details", call_number:callNums },
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){
						
						$("#pull-tesseract-jobs-modal").modal( "hide" );
						
						swal({
							type: 'success',
							title: data.status_msg,
							showConfirmButton: false,
							timer: 2100
						})
						window.setTimeout(function(){
							var new_url = window.location.href.split('?')[0];
							window.location.href = new_url;
						} ,1000);
					} else {
						swal({
							type: 'error',
							title: data.status_msg
						})
					}
				}
			});
			
		});

	});
</script>