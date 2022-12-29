<?php $category_id = (!empty($category_id)) ? $category_id : (($this->input->get('category_id')) ? $this->input->get('category_id') : false); ?>


<style>
	body {
		background-color: #FFFFFF;
	}
	.table>thead>tr>th {
		cursor:pointer;
	}

	div.xdsoft_datetimepicker{
		/* left: 1064.98px */
	}

	.min-width-80{
		min-width: 100px;
	}
</style>

<div class="row">
	<div class="x_panel no-border">
		<div class="row">
			<div class="x_content">

				<!-- Filter toggle + search bar -->
				<div class="row">
					<div class="col-md-2 col-sm-3 col-xs-12">
						<div class="row col-md-12 col-sm-12 col-xs-2"><strong>Job Date</strong></div>
						<div id="job-date" class="filter-container">
							<div class='filter-clear pointer' title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
							<div class='filter-heading'><i class="fas fa-filter filter-icon"></i><span style="font-size:14px;margin-left:10px;">Range <span class='filter-count'></span></span></div>
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
					<div class="col-md-7 col-sm-7 col-xs-12">
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-2"><strong>Checklist Job Statuses</strong></div>
							<div class="col-md-2 col-sm-2 col-xs-2">
								<div class="checkbox">
									<label><input type="checkbox" class="checklist-group-statuses" id="check-all-group-statuses" name="group_status[]" value="all" > All</label>
								</div>
							</div>
							<?php if (!empty($group_statuses)) {
							    foreach ($group_statuses as $check_group =>$check_status) { ?>
								<div class="col-md-2 col-sm-2 col-xs-2">
									<div class="checkbox">
										<label><input type="checkbox" class="checklist-group-statuses" <?php echo (!empty($selected_groups) && (in_array($check_group, $selected_groups))) ? 'checked=checked' : ''; ?> name="group_statuses[]" value="<?php echo $check_group; ?>" ><?php echo $check_status; ?></label>
									</div>
								</div>
							<?php }
							    } ?>
						</div>
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12 pull-right">
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-2"><strong>&nbsp;</strong></div>
							<div class="col-md-12 col-sm-12 col-xs-2"><?php $this->load->view('webapp/_partials/search_bar'); ?></div>
						</div>
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
										<?php if (!empty($job_statuses)) {
										    foreach ($job_statuses as $k =>$job_status) { ?>
											<div class="col-md-6 col-sm-6 col-xs-6">
												<span class="checkbox" style="margin:0">
													<label><input type="checkbox" class="job-statuses" name="job_statuses[]" value="<?php echo $job_status->job_status; ?>" > <?php echo ucwords($job_status->job_status); ?></label>
												</span>
											</div>
										<?php }
										    } ?>
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
										<?php if (!empty($job_types)) {
										    foreach ($job_types as $k =>$job_type) { ?>
											<div class="col-md-12 col-sm-12 col-xs-6">
												<span class="checkbox" style="margin:0">
													<label><input type="checkbox" class="job-types" name="job_types[]" value="<?php echo $job_type->job_type_id; ?>" > <?php echo ucwords($job_type->job_type); ?></label>
												</span>
											</div>
										<?php }
										    } ?>
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
						<table id="datatable-standard" class="table table-responsive" style="margin-bottom:0px; font-size:90%; font-weight:300" >
							<thead>
								<tr>
									<th width="15%">CHECKLIST TYPE</th>
									<th width="24%">SITE NAME</th>
									<th width="5%">POSTCODE</th>
									<th width="8%">SITE REF</th>
									<th width="8%">DUE DATE</th>
									<th width="10%">CHECKLIST STATUS</th>
									<th width="10%">JOB STATUS</th>
									<th width="10%">COMPLETION DATE</th>
									<th width="10%">COMPLETED BY</th>
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
		var job_group_statuses	= [];
		var job_types_arr		= [];
		var job_date_from       = $('[name="date_from"]').val();
		var job_date_to         = $('[name="date_to"]').val();
		var start_index	 		= 0;
		var where = {
			'status_id'			: job_statuses_arr,
			'job_type_id'		: job_types_arr,
			'group_status'	: job_group_statuses,
			'date_from'			: job_date_from,
			'date_to'			: job_date_to,
			'created_on_start'	: null,
			'created_on_end'	: null,
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

		//Load default group-statuses
		$( '.checklist-group-statuses' ).each(function(){
			if( $(this).is(':checked') ){
				where.group_status.push( $(this).val() );
			}
		});
		
		$('.checklist-group-statuses').change(function(){
			job_group_statuses 	= [];
			$( '.checklist-group-statuses' ).each(function(){
				if( $(this).is(':checked') ){
					job_group_statuses.push( $(this).val() );
				}
			});
			where.group_status = job_group_statuses;
			load_data( search_str, where, start_index );
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
		$('#check-all-statuses, #check-all-types, #check-all-group-statuses').change(function(){

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
			}else if( identifier == 'check-all-group-statuses' ){
				if( $(this).is(':checked') ){
					$('.checklist-group-statuses').each(function(){
						$(this).prop( 'checked', true );
					});
				}else{
					$('.checklist-group-statuses').each(function(){
						$(this).prop( 'checked', false );
					});
				}
				where.group_status = get_statuses( '.checklist-group-statuses' );;
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
				url:"<?php echo base_url('webapp/checklist/checklist_jobs_search'); ?>",
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
			} else if( identifier == '.checklist-group-statuses' ){
				
				job_group_statuses 	= [];

				$( identifier ).each(function(){
					chkCount++;
					if( $(this).is(':checked') ){
						totalChekd++;
						job_group_statuses.push( $(this).val() );
					}else{
						unChekd++;
					}
				});

				if( chkCount > 0 && ( chkCount == totalChekd ) ){
					$( '#check-all-group-statuses' ).prop( 'checked', true );
				}else{
					$( '#check-all-group-statuses' ).prop( 'checked', false );
				}

				return job_types_arr;
			}

		}
	});
</script>
