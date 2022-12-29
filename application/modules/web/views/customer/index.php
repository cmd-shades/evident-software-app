<div class="row top_<?php echo $module_identier; ?>">
	<div class="x_panel no-border">
		<div class="row">
			<div class="x_content">
				
				<!-- Module statistics and info -->
				<div class="module-statistics table-responsive alert alert-ssid alert-results hide" role="alert" style="overflow-y: hidden;display:block" >
					<legend>Customer Manager</legend>
					<div class="col-md-12 col-sm-12 col-xs-12" id="stats-results">

					</div>
					<div class="clearfix"></div>
				</div>
				
				<!-- Filter toggle + search bar -->
				<div class="row">
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
						<div class="row">
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
								<a href="<?php echo base_url('/webapp/customer/create'); ?>" class="btn btn-block btn-success success-shadow"><i class="fas fa-plus-circle" style="font-size: 18px;"></i></a>
							</div>
						</div>
					</div>
					
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center zindex_99">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<?php
                                ## $this->load->view( 'webapp/_partials/filters' );?>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
						<?php
                        $this->load->view('webapp/_partials/search_bar'); ?>
					</div>
				</div>
				<br/>
				<!-- Search by Filters -->
				<div id="filters-container" class="table-responsive filters-container <?php echo $module_identier; ?>-color <?php echo 'border-'.$module_identier; ?>" role="alert" style="overflow-y: hidden; display:none" >
					<ul class="nav nav-tabs">
						<li class="active">
							<a data-toggle="tab" href="#statuses">Statuses</a><input type="checkbox" id="check-all-statuses" style="position: relative; right: 10px;" />
						</li>
						<li>
							<a data-toggle="tab" href="#departments">Departments</a><input type="checkbox" id="check-all-departments" style="position: relative; right: 10px;" />
						</li>
					</ul>
					
					<div class="tab-content filters">
						<div id="statuses" class="tab-pane fade in active">
							<div class="col-md-12 col-sm-12 col-xs-12" style="margin:0">
								<div class="row padding_top_20">
									<?php if (!empty($user_statuses)) {
									    foreach ($user_statuses as $k =>$status) { ?>
										<div class="col-md-2 col-sm-6 col-xs-6">
											<span class="checkbox" style="margin:0">
												<label><input type="checkbox" class="user-statuses" name="user_statuses[]" value="<?php echo $status->status_id; ?>" > <?php echo ucwords($status->status); ?></label>
											</span>
										</div>
									<?php }
									    } ?>
								</div>
							</div>
						</div>

						<div id="departments" class="tab-pane fade in">
							<div class="col-md-12 col-sm-12 col-xs-12" style="margin:0">
								<div class="row padding_top_20">
									<?php if (!empty($departments)) {
									    foreach ($departments as $k =>$department) { ?>
										<div class="col-md-2 col-sm-6 col-xs-6">
											<span class="checkbox" style="margin:0">
												<label><input type="checkbox" class="people-departments" name="departments[]" value="<?php echo $department->department_id; ?>" > <?php echo ucwords($department->department_name); ?></label>
											</span>
										</div>
									<?php }
									    } ?>
								</div>
							</div>
						</div>
					</div>
				
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="filters">
							<!-- Clear Filter -->
							<?php $this->load->view('webapp/_partials/clear_filters.php') ?>				
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				
				<div class="clearfix"></div>
				<div class="table-responsive alert alert-ssid alert-results" role="alert" style="overflow-y: hidden;" >
					<table id="datatable" class="table table-responsive" style="margin-bottom:0px;width:100%" >
						<thead>
							<tr>
								<th width="20%">Name</th>
								<th width="25%">Business Name</th>
								<th width="15%">Email</th>
								<th width="15%">Main Telephone</th>
								<th width="15%">Customer Type</th>
								<th width="10%">Postcode</th>
							</tr>
						</thead>
						<tbody id="table-results"></tbody>
					</table>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){

	var search_str   		= null;
	var start_index	 		= 0;
	var where 				= false;
	
	// Initial data pull 
	load_data( search_str, where, start_index );
	
	// Pull data on search
	$( '#search_term' ).keyup( function(){
		var search = encodeURIComponent( $( this ).val() );
		if( search.length > 0 ){
			load_data( search, where, start_index );
		} else {
			load_data( search_str, where, start_index );
		}
	});
	
	
	$( '.search-go' ).on( "click", function(){
		var search = encodeURIComponent( $( '#search_term' ).val() );
		if( search.length > 0 ){
			load_data( search, where, start_index );
		} else {
			load_data( search_str, where, start_index );
		}
	});

	
	//Pagination links
	$( "#table-results" ).on( "click", "li.page", function( event ){
		event.preventDefault();
		var start_index = $( this ).find( 'a' ).data( 'ciPaginationPage' );
		var search_str 	= encodeURIComponent( $( '#search_term' ).val() );
		load_data( search_str, where, start_index );
	});
	
	
	// Pull the data
	function load_data( search_str, where, start_index ){
		$.ajax({
			url:"<?php echo base_url('webapp/customer/lookup'); ?>",
			method:"POST",
			data:{ search_term:search_str, where:where, start_index:start_index },
			success:function( data ){
				$( '#table-results' ).html( data );
			}
		});
	}
});
</script>

