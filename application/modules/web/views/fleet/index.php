<link href="<?php echo base_url('assets/css/custom.filters.css'); ?>" rel="stylesheet" type="text/css" media="all" /> 

<style>
	body {
		background-color: #FFFFFF;
	}
	.table>thead>tr>th {
		cursor:pointer;
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
				<?php /* ?><div class="hide module-statistics table-responsive alert alert-ssid alert-results" role="alert" style="overflow-y: hidden;display:block" >
                    <legend>Fleet Manager</legend>
                    <div class="col-md-12 col-sm-12 col-xs-12" id="stats-results">
                        <?php
                        if( !empty( $simple_stats[0] ) ){
                            $stats_no = count( get_object_vars( $simple_stats[0] ) );

                            foreach( $simple_stats[0] as $key => $value ){ ?>
                                <div class="col-md-<?php echo ceil( 12/$stats_no ); ?> col-sm-<?php echo ceil( 12/$stats_no ); ?> col-xs-12" style="margin:0">
                                    <div class="row">
                                        <h5 class="text-bold text-center"><?php echo ucfirst( str_replace( "_", " ", $key ) ); ?></h5>
                                        <h3 class="text-center"><?php echo $value; ?></h3>
                                    </div>
                                </div>
                            <?php
                            } ?>
                        <?php
                        } else { ?>
                            <div class="col-md-12 col-sm-12 col-xs-12" style="margin:0">
                                <div class="row">
                                    <h5 class="text-bold text-center">No Stats available</h5>
                                    <h3 class="text-center">&nbsp;</h3>
                                </div>
                            </div>
                        <?php
                        } ?>
                    </div>
                    <div class="clearfix"></div>
                </div> <?php */ ?>

				<!-- Filter toggle + search bar -->
				<div class="row">
					<div class="col-lg-4 col-md-4 col-sm-3 col-xs-12">
						<div class="row">
							<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
								<a href="<?php echo base_url('/webapp/fleet/create'); ?>" class="btn btn-block btn-success success-shadow"><i class="fas fa-plus-circle" style="font-size: 18px;"></i></a>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center zindex_99">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<?php
                            $data['filters']['filter_1']['filter_name'] = "Filters";
$data['filters']['filter_1']['filter_data'] = $vehicle_statuses;
$this->load->view('webapp/_partials/fleet_filters.php', $data);
?>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-3 col-xs-12">
						<?php
                        $this->load->view('webapp/_partials/search_bar'); ?>
					</div>
				</div>

				<div class="clearfix"></div>
				<div class="table-responsive alert alert-ssid alert-results" role="alert" style="overflow-y: hidden;" >
					<table id="datatable" class="table table-responsive" style="margin-bottom:0px;" >
						<thead>
							<tr>
								<th width="15%">Vehicle Reg</th>
								<th width="25%">Make</th>
								<th width="15%">Model</th>
								<th width="15%">Year</th>
								<th width="20%">Driver</th>
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

		$( ".single_filter label" ).on( "click", function(){
			$( this ).parent().toggleClass( 'open closed' );
			$( this ).next().slideToggle( "fast", function() {
				// Animation complete.
			});
		});

		var search_str   		= null;
		var vehicle_statuses_arr= [];
		var start_index	 		= 0;

		//Load default brag-statuses
		$('.user-types').each(function(){
			if( $(this).is(':checked') ){
				vehicle_statuses_arr.push( $(this).val() );
			}
		});

		load_data( search_str, vehicle_statuses_arr );

		// Do Search when filters are changed
		$('.user-types').change(function(){
			vehicle_statuses_arr =  get_statuses( '.user-types' );
			load_data( search_str, vehicle_statuses_arr );
		});

		// Do search when All is selected
		$('#check-all').change(function(){
			var search_str  = $('#search_term').val();
			if( $(this).is(':checked') ){
				$('.user-types').each(function(){
					$(this).prop( 'checked', true );
				});
			}else{
				$('.user-types').each(function(){
					$(this).prop( 'checked', false );
				});
			}
			vehicle_statuses_arr =  get_statuses( '.user-types' );
			load_data( search_str, vehicle_statuses_arr );
		});

		// Pagination links
		$("#table-results").on("click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $(this).find('a').data('ciPaginationPage');
			load_data( search_str, vehicle_statuses_arr, start_index );
		});

		function load_data( search_str, vehicle_statuses_arr, start_index ){
			$.ajax({
				url:"<?php echo base_url('webapp/fleet/lookup'); ?>",
				method:"POST",
				data:{search_term:search_str,vehicle_statuses:vehicle_statuses_arr,start_index:start_index},
				success:function( data ){
					$('#table-results').html(data);
				}
			});
		}

		$('#search_term').keyup(function(){
			var search = encodeURIComponent( $(this).val() );
			if( search.length > 0 ){
				load_data( search , vehicle_statuses_arr );
			}else{
				load_data( search_str, vehicle_statuses_arr );
			}
		});

		function get_statuses( identifier ){
			vehicle_statuses_arr = [];
			var chkCount  = 0;
			var totalChekd= 0;
			var unChekd   = 0;
			$( identifier ).each(function(){
				chkCount++;
				if( $(this).is(':checked') ){
					totalChekd++;
					vehicle_statuses_arr.push( $(this).val() );
				}else{
					unChekd++;
				}
			});

			if( chkCount > 0 && ( chkCount == totalChekd ) ){
				$( '#check-all' ).prop( 'checked', true );
			}else{
				$( '#check-all' ).prop( 'checked', false );
			}
			return vehicle_statuses_arr;
		}
	});
</script>

