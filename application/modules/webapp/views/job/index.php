<style>
	body {
		background-color: #F7F7F7;
	}
	.table>thead>tr>th {
		cursor:pointer;
	}
	
	div.xdsoft_datetimepicker{
		left: 1064.98px
	}
</style>

<div class="row">
	<div class="x_panel no-border">
		<div class="row">
			<div class="x_content">
				<!-- Module statistics and info -->
				<div class="module-statistics table-responsive alert alert-ssid" role="alert" style="overflow-y: hidden;display:block" >
					<legend>Job Manager <span class="pull-right" style="font-size: 14px">Today's stats <span class="<?php echo $module_identier; ?>" ><?php echo date( 'd-m-Y' ); ?></span></span></legend>
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="col-md-3 col-sm-3 col-xs-12" style="margin:0">
							<div class="row">
								<h5 class="text-bold text-center" >Assigned</h5>
								<h3 class="text-center"><?php echo ( !empty( $job_stats->TotalJobs ) && ( !empty( $job_stats->Assigned ) ) ) ? number_format( ( $job_stats->Assigned / $job_stats->TotalJobs )*100, 1 ).'%' : '0'; ?></h3>
							</div>
						</div>
						<div class="col-md-3 col-sm-3 col-xs-12" style="margin:0">
							<div class="row">
								<h5 class="text-bold text-center" >Successfull</h5>
								<h3 class="text-center"><?php echo ( !empty( $job_stats->TotalJobs ) && ( !empty( $job_stats->Successful ) ) ) ? number_format( ( $job_stats->Successful / $job_stats->TotalJobs )*100, 1 ).'%' : '0'; ?></h3>
							</div>
						</div>
						<div class="col-md-3 col-sm-3 col-xs-12" style="margin:0">
							<div class="row">
								<h5 class="text-bold text-center" >Failed </h5>
								<h3 class="text-center"><?php echo ( !empty( $job_stats->TotalJobs ) && ( !empty( $job_stats->Failed ) ) ) ? number_format( ( $job_stats->Failed / $job_stats->TotalJobs )*100, 1 ).'%' : '0'; ?></h3>
							</div>
						</div>
						<div class="col-md-3 col-sm-3 col-xs-12" style="margin:0">
							<div class="row">
								<h5 class="text-bold text-center" >In Progress</h5>
								<h3 class="text-center"><?php echo ( !empty( $job_stats->TotalJobs ) && ( !empty( $job_stats->InProgress ) ) ) ? number_format( ( $job_stats->InProgress / $job_stats->TotalJobs )*100, 1 ).'%' : '0'; ?></h3>
							</div>
						</div>
						
					</div>
					<div class="clearfix"></div>
				</div>
				
				<!-- Filter toggle + search bar -->
				<div class="row">
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
						<?php 
						$this->load->view('webapp/_partials/filters'); ?>
						<?php 
						$this->load->view('webapp/_partials/center_options'); ?>
					</div>
					<div class="col-lg-4 col-lg-offset-4 col-md-4 col-md-offset-4 col-sm-6 col-xs-12">
						<?php 
						$this->load->view('webapp/_partials/search_bar'); ?>
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
													<label><input type="checkbox" class="job-statuses" name="job_statuses[]" value="<?php echo $job_status->status_id; ?>" > <?php echo ucwords( $job_status->job_status ); ?></label>
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
							
							<div class="pull-right col-md-6 col-sm-6 col-xs-12" style="margin:0; display:none">
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
				<div class="table-responsive alert alert-ssid" role="alert" style="overflow-y: hidden;" >
					<table id="datatable" class="table table-responsive" style="margin-bottom:0px;" >
						<thead>
							<tr>
								<th width="10%">Job ID</th>
								<th width="25%">Job Date</th>
								<th width="25%">Job Type</th>
								<th width="25%">Assignee</th>
								<th width="15%">Status</th>
							</tr>
						</thead>
						<tbody id="table-results">
							
						</tbody>
					</table>
				</div>
				
				<div class="clearfix"></div>
				<div class="row">
					<div class="col-md-2 col-sm-3 col-xs-12">
						<a href="<?php echo base_url('/webapp/job/create' ); ?>" class="btn btn-block btn-success shadow-success">Create new</a>
					</div>
				</div>
				
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){

		var search_str   		= null;
		var job_statuses_arr	= [];
		var job_types_arr		= [];
		var job_date_from       = $('[name="date_from"]').val();
		var job_date_to         = $('[name="date_to"]').val();
		var start_index	 		= 0;
		
		//Load default brag-statuses
		$('.job-statuses').each(function(){
			if( $(this).is(':checked') ){
				job_statuses_arr.push( $(this).val() );
			}
		});
		
		//Load default brag-statuses
		$('.job-types').each(function(){
			if( $(this).is(':checked') ){
				job_types_arr.push( $(this).val() );
			}			
		});
		
		//Load default brag-statuses
		$('.job-dates').each(function(){
			if( $(this).is(':checked') ){
				job_types_arr.push( $(this).val() );
			}			
		});
		
		load_data( search_str, job_statuses_arr, job_types_arr, job_date_from, job_date_to );
		
		//Do Search when filters are changed
		$('.job-statuses').change(function(){
			job_statuses_arr =  get_statuses( '.job-statuses' );
			job_types_arr 	 =  get_statuses( '.job-types' );
			load_data( search_str, job_statuses_arr, job_types_arr, job_date_from, job_date_to );
		});
		
		//Do Search when job types are changed
		$('.job-types').change(function(){
			job_statuses_arr =  get_statuses( '.job-statuses' );
			job_types_arr 	 =  get_statuses( '.job-types' );
			load_data( search_str, job_statuses_arr, job_types_arr, job_date_from, job_date_to );
		});
	
	
		//Do search when the Dates change
		$('.date-range').change( function(){
			search_str    	 = $('#search_term').val();
			job_statuses_arr =  get_statuses( '.job-statuses' );
			job_types_arr 	 =  get_statuses( '.job-types' );
			job_date_from 	 = $('[name="date_from"]').val();
			job_date_to   	 = $('[name="date_to"]').val();
			
			load_data( search_str, job_statuses_arr, job_types_arr, job_date_from, job_date_to );
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
				
				job_statuses_arr  =  get_statuses( '.job-statuses' );
				
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

				job_types_arr 	=  get_statuses( '.job-types' );
			}
			load_data( search_str, job_statuses_arr, job_types_arr, job_date_from, job_date_to );
		});

		//Pagination links
		$("#table-results").on("click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $(this).find('a').data('ciPaginationPage');
			load_data( search_str, job_statuses_arr, job_types_arr, job_date_from, job_date_to, start_index );
		});
		
		function load_data( search_str, job_statuses_arr, job_types_arr, job_date_from, job_date_to, start_index ){
			$.ajax({
				url:"<?php echo base_url('webapp/job/lookup'); ?>",
				method:"POST",
				data:{search_term:search_str,job_statuses:job_statuses_arr,job_types:job_types_arr, date_from:job_date_from, date_to:job_date_to, start_index:start_index},
				success:function(data){
					$('#table-results').html(data);
				}
			});
		}
		
		$('#search_term').keyup(function(){
			var search = encodeURIComponent( $(this).val() );
			job_date_from = $('[name="date_from"]').val();
			job_date_to   = $('[name="date_to"]').val();
			
			if( search.length > 0 ){
				load_data( search , job_statuses_arr, job_types_arr, job_date_from, job_date_to, );
			}else{
				load_data( search_str, job_statuses_arr, job_types_arr, job_date_from, job_date_to, );
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

