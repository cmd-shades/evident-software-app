<style>
	body {
		background-color: #F7F7F7;
	}
	.table>thead>tr>th {
		cursor:pointer;
	}

	.nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover{
		color: #0092CD !important;
	}

	.nav-tabs>li>a{
		color: #555;
		width: max-content;
		margin: 0 auto;
		padding: 5px 35px;
	}

	.filters-toggle.open{
		top: 10px;
		position: absolute;
		background-color: rgb( 92, 92, 92 );
	}

	#filters-container{
	    display: block;
		overflow-y: hidden;
		position: relative;
		top: -11px;
		background: #f4f4f4;
	}

	.filters_to_center{
		margin: 0 auto;
		float: initial;
	}

	.top_search .input-group .fas.fa-search{
	    position: absolute;
		top: 8px;
		left: 20px;
		color: #fff;
		width: 28px;
		height: 28px;
		z-index: 99;
		font-size: 18px;
	}

	#search_term{
		text-indent: 32px;
	}

	#search_term::placeholder{
		color: #fff;
	}

	.filters_open{
	    min-height: 45px;
		background-color: #f4f4f4 !important;
		color: #5c5c5c !important;
		border-bottom: none !important;
		z-index: 99;
	    -webkit-box-shadow: none !important;
		-moz-box-shadow: none !important;
		box-shadow: none !important;
	}

	.zindex_99{
		z-index: 99;
	}

	#filters-container .nav>li>a:hover, #filters-container .nav>li>a:active, #filters-container .nav>li>a:focus{
		background: #f4f4f4;
		border: none;
		border-bottom: 1px solid #555;
		width: max-content;
		margin: 0 auto;
	}
	
	#filters-container .nav-tabs > li > a{
		display: inline-block;
	}
	
	#filters-container .nav-tabs > li > a > input{
		display: inline-block;
	}

	.nav-tabs > li.active > a, .nav-tabs > li.active > a:focus, .nav-tabs > li.active > a:hover{
	    cursor: default;
		border: none;
		background-color: #f4f4f4;
		border-bottom: 1px solid #555;
		width: max-content;
		margin: 0 auto;
	}

	.nav.nav-tabs{
		border-bottom: none;
	}

	.nav-tabs > li{
		width: calc(100% / 9 * 2);
		text-align: center;
	}

	.nav-tabs > li:first-child, .nav-tabs > li:last-child{
		width: calc(100% / 9 * 1.5);
	}

	.padding_top_20{
		padding-top: 20px;
	}
	
	
	button.clear-filters{
		background-color: rgba( 92, 92, 92, 1);
		color: #fff;
	}
	
	button.clear-filters:hover, button.clear-filters:active {
		background-color: rgba( 92, 92, 92, 1);
		color: #0092CD;
	}
</style>


<div class="row top_<?php echo $module_identier; ?>">
	<div class="x_panel no-border">
		<div class="row">
			<div class="x_content">
				<!-- Module statistics and info -->
				<div class="hide module-statistics table-responsive alert alert-ssid alert-results" role="alert" style="overflow-y: hidden;display:block" >
					<legend>EviDoc Manager</legend>
					<div class="col-md-12 col-sm-12 col-xs-12" id="stats-results">
					</div>
					<div class="clearfix"></div>
				</div>

				<div class="module-topbar-container">
					
					<div class="col-md-4" >
						<div class="row">
							<div id="result-status" class='filter-container'>
								<div class='filter-clear pointer' title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
								<div class='filter-heading'><i class="fas fa-filter filter-icon"></i><span style="font-size:14px;margin-left:10px;">Result Status <span class='filter-count'></span></span></div>
								<div class='filter-dropdown' style="display:none;">
									 <div class='filter-item'>
									   <input type="checkbox" class='filter-checkbox filter-select-all results_statuses' id="check-all-result">
									   <label class='filter-label' for="check-all-result">All</label>
									 </div>
									 <section class='active-filters'>
										 <?php if (!empty($results_statuses)) {
										     foreach ($results_statuses as $k => $row) { ?>
											 <div class='filter-item'>
												 <input id="fil-rs-<?php echo $k; ?>" type="checkbox" class='filter-checkbox results_statuses' name="results_statuses[]" value="<?php echo $row->audit_result_status_id; ?>">
												 <label for = "fil-rs-<?php echo $k; ?>" class='filter-label'><?php echo ucwords($row->result_status); ?></label>
											 </div>
										 <?php }
										     } ?>
									 </section>
								</div>
							</div>
						</div>
					</div>
					
					<div class="col-md-4" >
						<div id="audit-status" class='filter-container'>
							 <div class='filter-clear pointer' title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
							 <div class='filter-heading'><i class="fas fa-filter filter-icon"></i><span style="font-size:14px;margin-left:10px;">Status <span class='filter-count'></span></span></div>
							 <div class='filter-dropdown' style="display:none;">
								<div class='filter-item'>
									<input type="checkbox" class='filter-checkbox filter-select-all' id="filter-all">
									<label class='filter-label' for="filter-all">All</label>
								</div>
								<section class='active-filters'>
									<?php if (!empty($audit_statuses)) {
									    foreach ($audit_statuses as $k =>$status) { ?>
										<div class='filter-item'>
											<input id="fil-as-<?php echo $k; ?>" type="checkbox" class='filter-checkbox audit_statuses' name="results_statuses[]" value="<?php echo urlencode($status); ?>">
											<label for = "fil-as-<?php echo $k; ?>" class='filter-label'><?php echo ucwords($status); ?></label>
										</div>
									<?php }
									    } ?>
								 </section>
							 </div>
						 </div>
					</div>
					
					<div class="col-md-4" >
						<div class="row">
							<div style="width:100%">
								<div class="form-group top_search" style="margin-bottom:-13px">
									<!-- search bar -->
									<div class="input-group" style="width: 100%;">
										<i class="fas fa-search"></i><input type="text" class="form-control <?php echo $module_identier; ?>-search_input" id="search_term" value="" placeholder="Search EviDocs">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Search Filters -->
				<div id="filters-container" class="table-responsive filters-container <?php echo $module_identier; ?>-color <?php echo 'border-'.$module_identier; ?>" role="alert" style="display: none;">
					<div class="clearfix"></div>
				</div>

				<div class="clearfix"></div>
				<div class="table-responsive alert alert-ssid alert-results" role="alert" style="overflow-y: hidden;" >
					<table id="datatable" class="table table-responsive" style="margin-bottom:0px;width:100%" >
						<thead>
							<tr>
								<!-- <th width="5%">ID</th> -->
								<th width="20%">EviDoc Type</th>
								<th width="15%">Asset Unique ID</th>
								<!-- <th width="15%">Building Name</th>
								<th width="10%">Next Audit Date</th> -->
								<th width="15%">Result Status</th>
								<!-- <th width="10%">Assets EOL Date</th> -->
								<th width="15%">Audited By</th>
								<th width="10%">Status</th>
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

	var search_str   			= null;
	var audit_types_arr			= [];
	var audit_statuses_arr		= [];
	var results_statuses_arr 	= [];
	var next_audit_dates_arr	= [];
	var eol_dates_arr 			= [];
	var start_index	 			= 0;


	function load_data( search_str, audit_statuses_arr, audit_types_arr, results_statuses_arr, next_audit_dates_arr, eol_dates_arr, start_index ){

		$.ajax({
			url:"<?php echo base_url('webapp/audit/lookup'); ?>",
			method: "POST",
			dataType: "json",
			data:{
				search_term: search_str,
				audit_statuses: audit_statuses_arr,
				audit_types: audit_types_arr,
				result_statuses: results_statuses_arr,
				next_audit_dates: next_audit_dates_arr,
				eol_dates: eol_dates_arr,
				start_index: start_index,
			},
			success:function(data){
				$( '#table-results' ).html( data.audits );
				$( '#stats-results' ).html( data.stats );
			}
		});
	}
	
	$('#search_term').keyup(function(){
		var search = encodeURIComponent( $(this).val() );
		if( search.length > 0 ){
			load_data( search, audit_statuses_arr, audit_types_arr, results_statuses_arr, next_audit_dates_arr, eol_dates_arr );
		}else{
			load_data( search_str, audit_statuses_arr, audit_types_arr, results_statuses_arr, next_audit_dates_arr, eol_dates_arr );
		}
	});
	
	//Pagination links
	$("#table-results").on("click", "li.page", function( event ){
		event.preventDefault();
		var start_index = $(this).find('a').data('ciPaginationPage');
		var search_str 	= encodeURIComponent( $( '#search_term' ).val() );
		load_data( search_str, audit_statuses_arr, audit_types_arr, results_statuses_arr, next_audit_dates_arr, eol_dates_arr, start_index);
	});

	resultStatusFilter = new setupResultFilter($("#result-status"))

	resultStatusFilter.update = function(){
		results_statuses_arr = resultStatusFilter.getFilters()
		load_data( search_str, audit_statuses_arr, audit_types_arr, results_statuses_arr, next_audit_dates_arr, eol_dates_arr );
	}

	auditStatusFilter = new setupResultFilter($("#audit-status"))

	auditStatusFilter.update = function(){
		audit_statuses_arr = auditStatusFilter.getFilters()
		load_data( search_str, audit_statuses_arr, audit_types_arr, results_statuses_arr, next_audit_dates_arr, eol_dates_arr );
	}

	load_data( search_str, audit_statuses_arr, audit_types_arr, results_statuses_arr, next_audit_dates_arr, eol_dates_arr );

	
	
</script>

