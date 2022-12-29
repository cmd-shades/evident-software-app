<style>
	body {
		background-color: #FFFFFF;
	}

	.table>thead>tr>th {
		cursor:pointer;
	}

	.nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover{
		color: #0092CD !important;
	}

	.fas.fa-caret-down.fa-2x.pull-right{
		margin-top: -6px;
		margin-bottom: 0;
		padding-bottom: 0;
	}

	.checked{
		background-color: #01c7df;
		color: #fff;
	}

	.unchecked{
		background-color: #fff;
		color: #01c7df;
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
				<div class="module-statistics table-responsive alert alert-ssid alert-results hide" role="alert" style="overflow-y: hidden;display:block" >
					<legend>People Manager</legend>
					<div class="col-md-12 col-sm-12 col-xs-12" id="stats-results"></div>
					<div class="clearfix"></div>
				</div>
				<div class="module-topbar-container">
				  <div style="width:250px">
				      <!-- create button -->
					  <a href="<?php echo base_url('/webapp/people/create'); ?>" class="btn btn-block btn-success success-shadow"><i class="fas fa-plus-circle" style="font-size: 18px;"></i></a>
				  </div>
				  <div style="width:25%;"></div>
			         <!-- filters -->
					 <div id="people-status" class='filter-container'>
						 <div class='filter-clear pointer' title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
						 <div class='filter-heading'><i class="fas fa-filter filter-icon"></i><span style="font-size:14px;margin-left:10px;">Status <span class='filter-count'></span></span></div>
						 <div class='filter-dropdown' style="display:none;">
							 <div class='filter-item'>
							   <input type="checkbox" class='filter-checkbox filter-select-all results_statuses' id="check-all-result">
							   <label class='filter-label' for="check-all-result">All</label>
							 </div>
							 <section class='active-filters'>
								 <?php if (!empty($user_statuses)) {
								     foreach ($user_statuses as $k =>$status) { ?>
									 <div class='filter-item'>
										 <input id="fil-p-<?php echo $k; ?>" type="checkbox" class='filter-checkbox results_statuses' name="results_statuses[]" value="<?php echo $status->status_id; ?>">
										 <label for = "fil-p-<?php echo $k; ?>" class='filter-label'><?php echo ucwords($status->status); ?></label>
									 </div>
								 <?php }
								     } ?>
							 </section>
						 </div>
					 </div>
			      <div style="width:25%;"></div>
				  <div style="width:400px">
				      <div class="form-group top_search" style="margin-bottom:-13px">
				          <!-- search bar -->
				          <div class="input-group" style="width: 100%;">
				              <i class="fas fa-search"></i><input type="text" class="form-control <?php echo $module_identier; ?>-search_input" id="search_term" value="" placeholder="Search <?php echo ($module_identier != "people") ? (!empty($rename_search_word) ? $rename_search_word : ucwords($module_identier)."s") : ucwords($module_identier) ; ?>">
				          </div>
				      </div>
				  </div>
				</div>
				<div class="clearfix"></div>
				<div class="table-responsive alert alert-ssid alert-results" role="alert" style="overflow-y: hidden;" >
					<table id="datatable" class="table table-responsive" style="margin-bottom:0px;width:100%" >
						<thead>
							<tr>
								<!-- <th width="5%">ID</th> -->
								<th width="30%">Full Name</th>
								<th width="10%">Preferred Name</th>
								<th width="15%">Email</th>
								<th width="15%">Department</th>
								<th width="20%">Job Title</th>
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

	$(document).ready(function(){

		
		var search_str   			= null;
		var people_departments_arr	= [];
		var user_statuses_arr		= [];
		var start_index	 			= 0;
		var user_statuses_arr		= [];
		var where = {
			'status_id'	 	:user_statuses_arr,
			'department_id' :people_departments_arr
		};
		

		//Pagination links
		$("#table-results").on("click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $(this).find('a').data('ciPaginationPage');
			var search_str 	= encodeURIComponent( $( '#search_term' ).val() );
			load_data( search_str, where, start_index);
		});

		function load_data( search_str, where, start_index ){
			$.ajax({
				url:"<?php echo base_url('webapp/people/lookup'); ?>",
				method:"POST",
				data:{ search_term:search_str, where:where, start_index:start_index},
				success:function(data){
					$('#table-results').html(data);
				}
			});
		}
		
		personStatusFilter = new setupResultFilter($("#people-status"))

		personStatusFilter.update = function(){
			user_statuses_arr = personStatusFilter.getFilters()
			load_data( search_str, user_statuses_arr );
		}

		$('#search_term').keyup(function(){
			var search = encodeURIComponent( $(this).val() );
			if( search.length > 0 ){
				load_data( search , where );
			}else{
				load_data( search_str, where );
			}
		});
		
		load_data( search_str, where, start_index);

	});
</script>
