<?php
$system_id 		= (!empty($system_id)) ? $system_id : (($this->input->get('system_id')) ? $this->input->get('system_id') : false);
$range_index 	= (!empty($range_index)) ? $range_index : (($this->input->get('range_index')) ? $this->input->get('range_index') : false);
?>
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
				<!-- Filter toggle + search bar -->
				<div class="row" >
					<div class="col-md-2 col-sm-2 col-xs-12" >
						<!-- create button -->
						<a href="<?php echo base_url('/webapp/site/create'); ?>" class="btn btn-block btn-success success-shadow"><i class="fas fa-plus-circle" style="font-size: 18px;"></i></a>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12" >
						&nbsp;
					</div>
					<div class="col-md-4 col-sm-4 col-xs-12" >
						<div class="form-group top_search" style="margin-bottom:-13px">
							<!-- search bar -->
							<div class="input-group" style="width: 100%;">
								<i class="fas fa-search"></i><input type="text" class="form-control <?php echo $module_identier; ?>-search_input" id="search_term" value="" placeholder="Search buildings">
							</div>
						</div>
					</div>
				</div>
				<br/>
				<div class="clearfix"></div>
				<div class="table-responsive alert alert-ssid alert-results" role="alert" style="overflow-y: hidden;" >
					<table id="datatable" class="table table-responsive" style="margin-bottom:0px; font-size:95%;" >
						<thead>
							<tr>
								<th width="16%">Building Name </th>
								<th width="18%">Estate Name</th>
								<th width="20%">Installed Systems</th>
								<th width="15%">Compliance Status</th>
								<th width="13%">Last Status Updated</th>
								<th width="12%">Days Elaspsed</th>
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

		var search_str   		= null;
		var site_statuses_arr	= [];
		var start_index	 		= 0;
		var rangeIndex          = "<?php echo $range_index; ?>";
		var systemId          	= "<?php echo $system_id; ?>";
		var wheRe 				= {
			'site_statuses'	:site_statuses_arr,
			'range_index'	:rangeIndex,
			'system_id'		:systemId,
		};

		siteStatusFilter = new setupResultFilter($("#site-status"))

		siteStatusFilter.update = function(){
			wheRe.site_statuses = siteStatusFilter.getFilters();
			load_data( search_str, site_statuses_arr );
		}

		function load_data( search_str, site_statuses_arr, start_index ){
			$.ajax({
				url:"<?php echo base_url('webapp/site/non_compliant_buildings_lookup'); ?>",
				method:"POST",
				data:{search_term:search_str,where:wheRe,start_index:start_index},
				success:function( data ){
					$( '#table-results' ).html( data );
				}
			});
		}
		
		$( '#search_term' ).keyup( function(){
			var search = encodeURIComponent( $( this ).val() );
			if( search.length > 0 ){
				load_data( search , wheRe );
			}else{
				load_data( search_str, wheRe );
			}
		});
		
		$("#table-results").on("click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $(this).find('a').data('ciPaginationPage');
			load_data( search_str, wheRe, start_index );
		});


		load_data( search_str, wheRe );

	});
</script>

