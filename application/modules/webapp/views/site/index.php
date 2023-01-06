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
				<?php /* ?><div class="module-statistics table-responsive alert alert-ssid alert-results" role="alert" style="overflow-y: hidden;display:block" >
					<legend>Sites Manager</legend>
					<div class="col-md-12 col-sm-12 col-xs-12" id="stats-results">

					</div>
					<div class="clearfix"></div>
				</div>
				<?php */ ?>

				<!-- Filter toggle + search bar -->
				<div class="module-topbar-container">
				  <div style="width:250px">
						<div class="row" >
							<div class="col-md-6" >
								<!-- create button -->
								<a href="<?php echo base_url( '/webapp/site/create' ); ?>" class="btn btn-block btn-success success-shadow"><i class="fas fa-plus-circle" style="font-size: 18px;"></i></a>
							
							</div>
							<?php if( in_array( $this->user->account_id, [8] ) ) {?>
								<div class="col-md-6" >
									<button id="pull-tess-sites" type="button" class="btn btn btn-info success-shadow" >Pull Tesseract Sites</button>
								</div>
							<?php } ?>
						</div>
				  </div>
				  <div style="width:26%;"></div>
				  <div id="site-status" class='filter-container'>
					 <div class='filter-clear pointer' title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
					 <div class='filter-heading'><i class="fas fa-filter filter-icon"></i><span style="font-size:14px;margin-left:10px;">Status <span class='filter-count'></span></span></div>
					 <div class='filter-dropdown' style="display:none;">
							 <div class='filter-item'>
							   <input type="checkbox" class='filter-checkbox filter-select-all results_statuses' id="check-all-result">
							   <label class='filter-label' for="check-all-result">All</label>
							 </div>
							 <section class='active-filters'>
								 <?php if( !empty( $site_statuses ) ) { foreach( $site_statuses as $k =>$status ){ ?>
									 <div class='filter-item'>
										 <input id="fil-rs-<?php echo $k; ?>" type="checkbox" class='filter-checkbox results_statuses' name="results_statuses[]" value="<?php echo $status->status_id; ?>">
										 <label for = "fil-rs-<?php echo $k; ?>" class='filter-label'><?php echo ucwords( $status->status_name ); ?></label>
									 </div>
								 <?php } } ?>
							 </section>
						 </div>
			       </div>
				   <div style="width:26%;"></div>
				  <div style="width:400px">
				      <div class="form-group top_search" style="margin-bottom:-13px">
				          <!-- search bar -->
				          <div class="input-group" style="width: 100%;">
				              <i class="fas fa-search"></i><input type="text" class="form-control <?php echo $module_identier; ?>-search_input" id="search_term" value="" placeholder="Search buildings">
				          </div>
				      </div>
				  </div>
				</div>
				<div class="clearfix"></div>
				</div>
				<div class="clearfix"></div>
				<div class="table-responsive alert alert-ssid alert-results" role="alert" style="overflow-y: hidden;" >
					<table id="datatable" class="table table-responsive" style="margin-bottom:0px;" >
						<thead>
							<tr>
								<!-- <th width="10%">Site ID</th> -->
								<th width="20%">Building Name </th>
								<th width="40%">Address</th>
								<th width="15%">Estate Name</th>
								<th width="10%">Postcode</th>
								<th width="15">Building Status</th>
								<!-- <th width="15">Result Status</th> -->
							</tr>
						</thead>
						<tbody id="table-results">

						</tbody>
					</table>
				</div>
				<div class="clearfix"></div>
				<!-- Modal for pulling Sites By Tesseract Site Number -->
				<div style="z-index:10000" id="pull-tess-sites-modal" class="modal fade pull-site-jobs-modal" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog modal-md">
						<form id="pull-tess-sites-form" >
							<input type="hidden" name="page" value="details" />
							<input type="hidden" name="account_id" value="<?php echo $this->user->account_id; ?>" />
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
									<h4 class="modal-title" id="myModalLabel">Pull Tesseract Sites By Site Number</h4>
									<small id="feedback-message"></small>
								</div>

								<div class="modal-body">
									<div class="input-group form-group">
										<label class="input-group-addon">Site Numbers <small><em>(Comma seperated)</em></small></label>
										<input id="site_numbers" name="site_numbers" class="form-control" type="text" placeholder="Site Numbers" value="" />
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">&nbsp;&nbsp;&nbsp;&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;</button>
									<button id="fetch-tesseract-sites-btn" type="button" class="update-job-btn btn btn-sm btn-success">Fetch Sites</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){

		var search_str   		= null;
		var site_statuses_arr	= [];
		var start_index	 		= 0;


		siteStatusFilter = new setupResultFilter($("#site-status"))

		siteStatusFilter.update = function(){
			site_statuses_arr = siteStatusFilter.getFilters()
			load_data( search_str, site_statuses_arr );
		}
		
		
		function load_data( search_str, site_statuses_arr, start_index ){
			$.ajax({
				url:"<?php echo base_url( 'webapp/site/lookup' ); ?>",
				method:"POST",
				dataType: 'json',
				data:{search_term:search_str,site_statuses:site_statuses_arr,start_index:start_index},
				success:function( data ){
					$( '#table-results' ).html( data.sites );
					$( '#stats-results' ).html( data.stats );
				}
			});
		}

		$('#search_term').keyup(function(){
			var search = encodeURIComponent( $(this).val() );
			if( search.length > 0 ){
				load_data( search , site_statuses_arr );
			}else{
				load_data( search_str, site_statuses_arr );
			}
		});
		
		$("#table-results").on("click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $(this).find('a').data('ciPaginationPage');
			var search_str 	= encodeURIComponent( $( '#search_term' ).val() );
			load_data( search_str, site_statuses_arr, start_index );
		});


		load_data( search_str, site_statuses_arr );

		$( '#pull-tess-sites' ).click(function(){
			$("#pull-tess-sites-modal").modal( "show" );
		});
		
		$( '#fetch-tesseract-sites-btn' ).click( function(){
			
			var siteNums = $( '#site_numbers' ).val();
			
			if( siteNums.length == 0 || siteNums.length === null ){
				swal({
					type: 'error',
					title: 'Please provide at least 1 Site Number',
					confirmButtonColor: '#5CB85C',
					cancelButtonColor: '#9D1919',
					confirmButtonText: 'Yes'
				});
				return false;
			}
			
			$.ajax({
				url:"<?php echo base_url('webapp/site/fetch_tesseract_sites_by_site_number' ); ?>",
				method:"POST",
				data:{ page:"details", site_number:siteNums },
				dataType: 'json',
				success:function(data){
					if( data.status == 1 ){
						
						$("#pull-site-jobs-modal").modal( "hide" );
						
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

