<?php $category_id = ( !empty( $category_id ) ) ? $category_id : ( ( $this->input->get( 'category_id' ) ) ? $this->input->get( 'category_id' ) : false ); ?>


<style>
	body {
		background-color: #FFFFFF;
	}
	.table>thead>tr>th {
		cursor:pointer;
	}

	div.xdsoft_datetimepicker{
		left: 1064.98px
	}

	.min-width-80{
		min-width: 100px;
	}
</style>

<div class="row">
	<div class="x_panel no-border">
		<div class="row">
			<div class="x_content">
				<!-- Module statistics and info -->
				<?php /* <div class="module-statistics table-responsive alert alert-ssid alert-results" role="alert" style="overflow-y: hidden;display:block" >
					<!-- <legend>Job Manager <span class="pull-right" style="font-size: 14px">Today's stats <span class="<?php echo $module_identier; ?>" ><?php echo date( 'd-m-Y' ); ?></span></span></legend> -->
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="col-md-3 col-sm-3 col-xs-12" style="margin:0">
							<div class="row">
								<h5 class="text-bold text-center" >Assigned</h5>
								<h3 class="text-center"><?php echo ( !empty( $job_stats->TotalJobs ) && ( !empty( $job_stats->Assigned ) ) ) ? ( number_format( ( $job_stats->Assigned / $job_stats->TotalJobs )*100, 1 ) + 0 ).'%' : '0'; ?></h3>
							</div>
						</div>
						<div class="col-md-3 col-sm-3 col-xs-12" style="margin:0">
							<div class="row">
								<h5 class="text-bold text-center" >Successful</h5>
								<h3 class="text-center"><?php echo ( !empty( $job_stats->TotalJobs ) && ( !empty( $job_stats->Successful ) ) ) ? ( number_format( ( $job_stats->Successful / $job_stats->TotalJobs )*100, 1 ) + 0 ).'%' : '0'; ?></h3>
							</div>
						</div>
						<div class="col-md-3 col-sm-3 col-xs-12" style="margin:0">
							<div class="row">
								<h5 class="text-bold text-center" >Failed </h5>
								<h3 class="text-center"><?php echo ( !empty( $job_stats->TotalJobs ) && ( !empty( $job_stats->Failed ) ) ) ? ( number_format( ( $job_stats->Failed / $job_stats->TotalJobs )*100, 1 ) + 0 ).'%' : '0'; ?></h3>
							</div>
						</div>
						<div class="col-md-3 col-sm-3 col-xs-12" style="margin:0">
							<div class="row">
								<h5 class="text-bold text-center" >In Progress</h5>
								<h3 class="text-center"><?php echo ( !empty( $job_stats->TotalJobs ) && ( !empty( $job_stats->InProgress ) ) ) ? ( number_format( ( $job_stats->InProgress / $job_stats->TotalJobs )*100, 1 ) + 0 ).'%' : '0'; ?></h3>
							</div>
						</div>

					</div>
					<div class="clearfix"></div>
				</div> */ ?>

				<!-- Filter toggle + search bar -->
				<div class="row">
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<div class="row">
							<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 hide">
								<a href="<?php echo base_url('/webapp/job/upload_jobs' ); ?>" class="btn btn-block btn-success success-shadow" title="Click to Upload Jobs"><i class="fas fa-upload" style="font-size: 18px;"></i></a><br/>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-center zindex_99">
						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-center zindex_99 filter-upper-container">
								<div id="job-date" class='filter-container'>
									<div class='filter-clear pointer' title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
									<div class='filter-heading'><i class="fas fa-filter filter-icon"></i><span style="font-size:14px;margin-left:10px;">Job Date <span class='filter-count'></span></span></div>
									<div class='filter-dropdown' style="display:none;">
										<section class='active-filters'>
											<div class='filter-item'>
												<label for = "fil-ut-1" class='filter-label filter-start-date'>From</label>
												<input id="fil-ut-1" type="text" class='datepicker-start' value=""  placeholder="DD/MM/YY" data-date-format="DD/MM/Y" style="border: none;" />
											</div>
											<div class='filter-item'>
												<label for = "fil-ut-2" class='filter-label filter-end-date'>To</label>
												<input id="fil-ut-2" type="text" class='datepicker-end' value="" placeholder="DD/MM/YY" data-date-format="DD/MM/Y" style="border: none;" />
											</div>
										</section>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-center zindex_99 filter-upper-container">
								<div id="date-created" class='filter-container'>
									<div class='filter-clear pointer' title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
									<div class='filter-heading'><i class="fas fa-filter filter-icon"></i><span style="font-size:14px;margin-left:10px;">Date Created <span class='filter-count'></span></span></div>
									<div class='filter-dropdown' style="display:none;">
										<section class='active-filters'>
											<div class='filter-item'>
												<label for = "fil-ut-3" class='filter-label filter-start-date'>From</label>
												<input id="fil-ut-3" type="text" class='datepicker-start' value=""  placeholder="DD/MM/YY"  data-date-format="DD/MM/Y" style="border: none;" />
											</div>
											<div class='filter-item'>
												<label for = "fil-ut-4" class='filter-label filter-end-date'>To</label>
												<input id="fil-ut-4" type="text" class='datepicker-end' value="" placeholder="DD/MM/YY" data-date-format="DD/MM/Y" style="border: none;" />
											</div>
										</section>
									</div>
								</div>
							</div>
							<?php /*
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="row">
									<div class="col-md-4 col-sm-4 col-xs-12 pull-left">
										Date Created:<br/>
										Job Date:
									</div>
									<div class="col-md-4 col-sm-4 col-xs-12 pull-left">
										<div class="col-md-10">
											<div class="input-prepend input-group form-group">
												<span class="min-width-80 input-group-addon job-bg">From </span>
												<input name="date_from" class="form-control datepicker datepicker-<?php echo $module_identier; ?> date-range" type="text" placeholder="From" value="" />
											</div>
										</div>
									</div>

									<div class="col-md-4 col-sm-4 col-xs-12 pull-right">
										<div class="col-md-10">
											<div class="input-prepend input-group form-group">
												<span class="min-width-80 input-group-addon job-bg">To </span>
												<input name="date_to" class="form-control datepicker datepicker-<?php echo $module_identier; ?> date-range" type="text" placeholder="To" value="" />
											</div>
										</div>
									</div>
								</div>
							</div> */ ?>
						</div>
					</div>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 pull-right">
						<?php $this->load->view( 'webapp/_partials/search_bar' ); ?>
					</div>
				</div>

				<div id="filters-container" class="table-responsive filters-container <?php echo $module_identier; ?>-color <?php echo 'border-'.$module_identier; ?>" role="alert" style="overflow-y: hidden; display:none" >
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="filters">
							<div class="col-md-6 col-sm-6 col-xs-12" style="margin:0">
								<div class="row">
									<h5 class="text-bold text-auto">Statuses</h5>
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-6">
											<span class="checkbox" style="margin:0">
												<label><input type="checkbox" id="check-all-statuses" value="all" > All</label>
											</span>
										</div>
										<?php if( !empty($job_statuses) ) { foreach( $job_statuses as $k =>$job_status ){ ?>
											<div class="col-md-6 col-sm-6 col-xs-6">
												<span class="checkbox" style="margin:0">
													<label><input type="checkbox" class="job-statuses" name="job_statuses[]" value="<?php echo $job_status->job_status; ?>" > <?php echo ucwords( $job_status->job_status ); ?></label>
												</span>
											</div>
										<?php } } ?>
									</div>
								</div>
							</div>

							<div class="col-md-6 col-sm-6 col-xs-12" style="margin:0">
								<div class="row">
									<h5 class="text-bold text-auto">Types</h5>
									<div class="row">
										<div class="col-md-12 col-sm-12 col-xs-6">
											<span class="checkbox" style="margin:0">
												<label><input type="checkbox" id="check-all-types" value="all" > All</label>
											</span>
										</div>
										<?php if( !empty( $job_types ) ) { foreach( $job_types as $k =>$job_type ){ ?>
											<div class="col-md-12 col-sm-12 col-xs-6">
												<span class="checkbox" style="margin:0">
													<label><input type="checkbox" class="job-types" name="job_types[]" value="<?php echo $job_type->job_type_id; ?>" > <?php echo ucwords( $job_type->job_type ); ?></label>
												</span>
											</div>
										<?php } } ?>
									</div>
								</div>
							</div>

							<div class="pull-right col-md-6 col-sm-6 col-xs-12" style="margin:0; display:block">
								<div class="row">
									<h5 class="text-bold">Quick Actions</h5>
									<form>
										<div>
											<select name="active" id="select-action" class="form-control" required>
												<option value="">Select action</option>
												<option value="1">Assigne jobs</option>
												<option value="0">Delete jobs</option>
											</select>
										</div>
										<div class="assignees-list" style="display:none; margin-top:10px;">
											<select id="assign_to" name="assign_to" class="form-control">
												<option value="" >Please select assignee</option>
											</select>
										</div>
										<br/>
										<a id="submit-action" class="btn btn-sm btn-info btn-block" >Submit Action</a>
									</form>
								</div>
							</div>
							<!-- Clear Filter -->
							<?php $this->load->view('webapp/_partials/clear_filters.php') ?>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="clearfix"></div>
				<div class="table-responsive alert alert-ssid alert-results" role="alert" style="overflow-y: hidden;" >
					<table id="datatable" class="table table-responsive" style="margin-bottom:0px;" >
						<thead>
							<tr>
								<th width="5%">Job ID</th>
								<th width="10%">Job Date</th>
								<th width="10%">Date Created</th>
								<th width="22%">Job Type</th>
								<th width="13%">Postcode</th>
								<?php /* <th width="13%">Contract</th> */ ?>
								<th width="15%">Assignee</th>
								<th width="12%">Status</th>
								<th width="13%">Tracking Status</th>
							</tr>
						</thead>
						<tbody id="table-results">

						</tbody>
					</table>
				</div>

				<div class="clearfix"></div>

			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$( document ).ready(function(){

		var search_str   		= null;
		var job_statuses_arr	= [];
		var job_types_arr		= [];
		var job_date_from       = $('[name="date_from"]').val();
		var job_date_to         = $('[name="date_to"]').val();
		var categoryId          = "<?php echo $category_id; ?>";
		var overdueJobs         = "<?php echo !empty( $overdue_jobs ) ? $overdue_jobs : ''; ?>";
		var dateRage            = "<?php echo !empty( $date_range ) ? $date_range : ''; ?>";
		var start_index	 		= 0;
		var where = {
			'status_id'			: job_statuses_arr,
			'job_type_id'		: job_types_arr,
			'category_id'		: categoryId,
			'date_from'			: job_date_from,
			'date_to'			: job_date_to,
			'created_on_start'	: null,
			'created_on_end'	: null,
			'overdue_jobs'		: overdueJobs,
			'date_range'		: dateRage,
		};
		
		jobDateFilter = new setupJobDateFilter( $( "#job-date" ) )
		jobDateFilter.update = function(){
			dates_arr 		= dateCreatedFilter.getDates();
			user_dates_arr 	= jobDateFilter.getDates();
			var all_arrays 	= $.extend( dates_arr, user_dates_arr );
			var search_str 	= encodeURIComponent( $( '#search_term' ).val() );
			load_data( search_str, all_arrays, start_index );
		}
		
		dateCreatedFilter = new setupCreateDateFilter( $( "#date-created" ) );
		dateCreatedFilter.update = function(){
			dates_arr 		= dateCreatedFilter.getDates();
			user_dates_arr 	= jobDateFilter.getDates();
			var all_arrays 	= $.extend( dates_arr, user_dates_arr );
			var search_str 	= encodeURIComponent( $( '#search_term' ).val() );
			load_data( search_str, all_arrays, start_index );
		}

		//Load default brag-statuses
		$( '.job-statuses' ).each(function(){
			if( $(this).is(':checked') ){
				where.status_id = job_statuses_arr.push( $(this).val() );
			}
		});

		//Load default brag-statuses
		$('.job-types').each(function(){
			if( $(this).is(':checked') ){
				where.job_type_id = job_types_arr.push( $(this).val() );
			}
		});

		load_data( search_str, where, start_index );

		//Do Search when filters are changed
		$('.job-statuses').change(function(){
			where.status_id	 = get_statuses( '.job-statuses' );
			where.job_type_id= get_statuses( '.job-types' );
			load_data( search_str, where, start_index );
		});

		//Do Search when job types are changed
		$('.job-types').change(function(){
			where.status_id	 = get_statuses( '.job-statuses' );
			where.job_type_id= get_statuses( '.job-types' );
			load_data( search_str, where, start_index );
		});


		//Do search when the Dates change
		$('.date-range').change( function(){
			search_str    	 	= $('#search_term').val();
			where.status_id	 	= get_statuses( '.job-statuses' );
			where.job_type_id	= get_statuses( '.job-types' );
			where.date_from 	= $('[name="date_from"]').val();
			where.date_to   	= $('[name="date_to"]').val();

			load_data( search_str, where, start_index );
		});

		//Do search when All is selected
		$('#check-all-statuses, #check-all-types').change(function(){

			var search_str = $('#search_term').val();

			var identifier = $(this).attr('id');

			if( identifier == 'check-all-statuses' ){
				if( $(this).is(':checked') ){
					$('.job-statuses').each(function(){
						$(this).prop( 'checked', true );
					});
				}else{
					$('.job-statuses').each(function(){
						$(this).prop( 'checked', false );
					});
				}

				where.status_id  =  get_statuses( '.job-statuses' );

			}else if( identifier == 'check-all-types' ){
				if( $(this).is(':checked') ){
					$('.job-types').each(function(){
						$(this).prop( 'checked', true );
					});
				}else{
					$('.job-types').each(function(){
						$(this).prop( 'checked', false );
					});
				}

				where.job_type_id =  get_statuses( '.job-types' );
			}

			load_data( search_str, where, start_index );
		});

		//Pagination links
		$("#table-results").on( "click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $( this ).find('a').data('ciPaginationPage');
			var search_str 	= encodeURIComponent( $( '#search_term' ).val() );
			
			where.date_from 	= $( '#fil-ut-1' ).val();
			where.date_to   	= $( '#fil-ut-2' ).val();

			where.created_on_start 	= $( '#fil-ut-3' ).val();
			where.created_on_end   	= $( '#fil-ut-4' ).val();
			
			load_data( search_str, where, start_index );
		});

		function load_data( search_str, where, start_index ){
			$.ajax({
				url:"<?php echo base_url('webapp/job/lookup'); ?>",
				method:"POST",
				data:{ search_term:search_str, where:where, start_index:start_index },
				success:function(data){
					$('#table-results').html(data);
				}
			});
		}

		$( '#search_term' ).keyup(function(){
			dates_arr 		= dateCreatedFilter.getDates();
			user_dates_arr 	= jobDateFilter.getDates();
			
			var all_arrays 	= $.extend( dates_arr, user_dates_arr );
				all_arrays.category_id	= categoryId;
			var search 		= encodeURIComponent( $(this).val() );
			if( search.length > 0 ){
				load_data( search, all_arrays );
			}else{
				load_data( search_str, all_arrays );
			}
		});

		function get_statuses( identifier ){

			var chkCount  = 0;
			var totalChekd= 0;
			var unChekd   = 0;

			var idClass	  = '';

			if( identifier == '.job-statuses' ){

				job_statuses_arr  = [];

				$( identifier ).each(function(){
					chkCount++;
					if( $(this).is(':checked') ){
						totalChekd++;
						job_statuses_arr.push( $(this).val() );
					}else{
						unChekd++;
					}
				});

				if( chkCount > 0 && ( chkCount == totalChekd ) ){
					$( '#check-all-statuses' ).prop( 'checked', true );
				}else{
					$( '#check-all-statuses' ).prop( 'checked', false );
				}

				return job_statuses_arr;

			}else if( identifier == '.job-types' ){

				job_types_arr 	= [];

				$( identifier ).each(function(){
					chkCount++;
					if( $(this).is(':checked') ){
						totalChekd++;
						job_types_arr.push( $(this).val() );
					}else{
						unChekd++;
					}
				});

				if( chkCount > 0 && ( chkCount == totalChekd ) ){
					$( '#check-all-types' ).prop( 'checked', true );
				}else{
					$( '#check-all-types' ).prop( 'checked', false );
				}

				return job_types_arr;
			}

		}
	});
</script>
