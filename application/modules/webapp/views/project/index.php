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

<div class="row">
	<div class="x_panel no-border">
		<div class="row">
			<div class="x_content">
				<div class="row" role="alert">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="alert alert-ssid" role="alert">
							<legend>Project Tracker</legend>
							<div class="row">
								<?php if( !empty( $stats_to_dashboard ) ){ foreach( $stats_to_dashboard as $stats_key ){ ?>
									<div class="col-sm-3">
										<div class="text-center">
											<span class="indic_line_1"><?php echo $contract_statuses->$stats_key->status_name; ?></span>
											<br />
											<span class="indic_line_2"><?php echo ( !empty( $quick_stats->$stats_key ) ) ? $quick_stats->$stats_key : '0' ; ?></span>
										</div>
									</div>
								<?php } } else { ?>
									<div class="col-sm-12">
										<span>There currently no project stats available</span>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>

				<!-- Filter toggle + search bar -->
				<div class="row">
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
						<div class="row">
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
								<a href="<?php echo base_url('/webapp/project/new_project' ); ?>" class="btn btn-block btn-success success-shadow" title="Click to add a new Project"><i class="fas fa-plus-circle" title="" style="font-size: 18px;"></i></a><br/>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center zindex_99">
						<div class="row hide">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<?php $this->load->view( 'webapp/_partials/filters' ); ?>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
						<div class="form-group top_search" style="margin-bottom:-13px">
							  <!-- search bar -->
							  <div class="input-group" style="width: 100%;">
								  <i class="fas fa-search"></i><input type="text" class="form-control <?php echo $module_identier; ?>-search_input" id="search_term" value="" placeholder="Search Projects">
							  </div>
						  </div>
					</div>
				</div>

				<div class="clearfix"></div>
				<div class="table-responsive alert alert-ssid alert-results" role="alert" style="overflow-y: hidden;" >
					<table id="datatable" class="table table-responsive" style="margin-bottom:0px;width:100%" >
						<thead>
							<tr>
								<th width="12.5%" >Project Name</th>
								<th width="12.5%" >Project Type</th>
								<th width="12.5%" >Start Date</th>
								<th width="12.5%" >End Date</th>
								<th width="12.5%" >Reference</th>
								<th width="10%" >Status</th>
								<th width="15%" >Leader</th>
								<th width="12.5%" >Ownership</th>
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

		var search_str   	= null;
		var project_status_arr	= [];
		var start_index	 	= 0;
		var where 			= {
			'project_status_id':project_status_arr
		};

		load_data( search_str, where, start_index );

		//Pagination links
		$( "#table-results" ).on("click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $(this).find('a').data('ciPaginationPage');
			var search_str 	= encodeURIComponent( $( '#search_term' ).val() );
			load_data( search_str, where, start_index);
		});

		function load_data( search_str, where, start_index ){
			$.ajax({
				url:"<?php echo base_url('webapp/project/projects_lookup'); ?>",
				method:"POST",
				data:{ search_term:search_str, where:where, start_index:start_index },
				success:function(data){
					$( '#table-results' ).html(data);
				}
			});
		}

		$( '#search_term' ).keyup(function(){
			var search = encodeURIComponent( $(this).val() );
			if( search.length > 0 ){
				load_data( search , where );
			}else{
				load_data( search_str, where );
			}
		});

		function get_statuses( identifier ){

			var chkCount  = 0;
			var totalChekd= 0;
			var unChekd   = 0;

			var idClass	  = '';

			if( identifier == '.project-statuses' ){

				project_status_arr  = [];

				$( identifier ).each(function(){
					chkCount++;
					if( $(this).is(':checked') ){
						totalChekd++;
						project_status_arr.push( $(this).val() );
					}else{
						unChekd++;
					}
				});

				if( chkCount > 0 && ( chkCount == totalChekd ) ){
					$( '#check-all-statuses' ).prop( 'checked', true );
				}else{
					$( '#check-all-statuses' ).prop( 'checked', false );
				}

				return project_status_arr;

			}

		}
	});
</script>
