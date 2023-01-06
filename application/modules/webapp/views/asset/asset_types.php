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
				<!-- Filter toggle + search bar -->
				<div class="row">
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
						<div class="row">
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
								<a href="<?php echo base_url( '/webapp/asset/create_asset_type' ); ?>" class="btn btn-block btn-success success-shadow" title="Click to add a New asset name"><i class="fas fa-plus-circle" title="" style="font-size: 18px;"></i></a><br/> 
								<!-- <a href="<?php echo base_url( '/webapp/asset/create' ); ?>" class="btn btn-block btn-success success-shadow" title="Click to add a New asset name"><i class="fas fa-plus-circle" title="" style="font-size: 18px;"></i></a><br/>-->
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center zindex_99 hide">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<?php
								$this->load->view( 'webapp/_partials/filters' ); ?>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 pull-right">
						<?php $this->load->view( 'webapp/_partials/search_bar' ); ?>
					</div>
				</div>

				<!-- Search by Filters -->
				<div id="filters-container" class="table-responsive filters-container <?php echo $module_identier; ?>-color <?php echo 'border-'.$module_identier; ?>" role="alert" style="overflow-y: hidden; display:none" >
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="filters">
							<div class="col-md-6 col-sm-4 col-xs-12 hide" style="margin:0">
								<div class="row">
									<h5 class="text-bold text-auto">Asset Statuses</h5>
									<div class="row">
										<div class="col-md-4 col-sm-6 col-xs-6">
											<span class="checkbox" style="margin:0">
												<label><input type="checkbox" id="check-all-statuses" value="all" > All</label>
											</span>
										</div>
										<?php if( !empty( $user_statuses ) ) { foreach( $user_statuses as $k =>$status ){ ?>
											<div class="col-md-4 col-sm-6 col-xs-6">
												<span class="checkbox" style="margin:0">
													<label><input type="checkbox" class="asset-statuses" name="user_statuses[]" value="<?php echo $status->status_id; ?>" > <?php echo ucwords( $status->status ); ?></label>
												</span>
											</div>
										<?php } } ?>
									</div>
								</div>
							</div>

							<div class="col-md-6 col-sm-4 col-xs-12 hide" style="margin:0">
								<div class="row">
									<h5 class="text-bold text-auto">Audit Result Statuses</h5>
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-6">
											<span class="checkbox" style="margin:0">
												<label><input type="checkbox" id="check-all-result-statuses" > All</label>
											</span>
										</div>
										<?php if( !empty( $departments ) ) { foreach( $departments as $k =>$department ){ ?>
											<div class="col-md-6 col-sm-6 col-xs-6">
												<span class="checkbox" style="margin:0">
													<label><input type="checkbox" class="result-result-statuses" name="departments[]" value="<?php echo $department->department_id; ?>" > <?php echo ucwords( $department->department_name ); ?></label>
												</span>
											</div>
										<?php } } ?>
									</div>
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
					<table id="datatable" class="table table-responsive" style="margin-bottom:0px;width:100%" >
						<thead>
							<tr>
								<th width="18%">Asset Name</th>
								<th width="20%">Group</th>
								<th width="20%">Description</th>
								<th width="15%">Date Created</th>
								<th width="13%">Status</th>								
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

		function getParameterByName( name, url ) {
			if (!url) url = window.location.href;
			name = name.replace(/[\[\]]/g, '\\$&');
			var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
				results = regex.exec(url);
			if (!results) return null;
			if (!results[2]) return '';
			return decodeURIComponent(results[2].replace(/\+/g, ' '));
		}

		var search_str   			= null;
		var asset_audit_statuses_arr= [];
		var asset_statuses_arr		= [];
		var start_index	 			= 0;
		
		var where = {
			'status_id'	 	:asset_statuses_arr,
			'department_id' :asset_audit_statuses_arr
		};

		//Load default brag-statuses
		$('.asset-statuses').each(function(){
			if( $(this).is(':checked') ){
				where.status_id = asset_statuses_arr.push( $(this).val() );
			}
		});

		$('.result-result-statuses').each(function(){
			if( $(this).is(':checked') ){
				where.department_id = asset_audit_statuses_arr.push( $(this).val() );
			}
		});

		load_data( search_str, where, start_index );

		//Do search when All is selected
		$('#check-all-statuses, #check-all-result-statuses').change(function(){
			var search_str  = encodeURIComponent( $('#search_term').val() );

			var identifier = $(this).attr('id');

			if( identifier == 'check-all-statuses' ){
				if( $(this).is(':checked') ){
					$('.asset-statuses').each(function(){
						$(this).prop( 'checked', true );
					});
				}else{
					$('.asset-statuses').each(function(){
						$(this).prop( 'checked', false );
					});
				}

				where.status_id	= get_statuses( '.asset-statuses' );

			}else if( identifier == 'check-all-result-statuses' ){
				if( $(this).is(':checked') ){
					$('.result-result-statuses').each(function(){
						$(this).prop( 'checked', true );
					});
				}else{
					$('.result-result-statuses').each(function(){
						$(this).prop( 'checked', false );
					});
				}

				where.department_id	= get_statuses( '.result-result-statuses' );
			}

			load_data( search_str, where, start_index );
		});

		//Pagination links
		$("#table-results").on("click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $(this).find('a').data('ciPaginationPage');
			load_data( search_str, where, start_index);
		});

		function load_data( search_str, where, start_index ){
			$.ajax({
				url:"<?php echo base_url('webapp/asset/asset_types_list'); ?>",
				method:"POST",
				data:{ search_term:search_str, where:where, start_index:start_index },
				success:function(data){
					$('#table-results').html(data);
				}
			});
		}

		$('#search_term').keyup(function(){
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

			if( identifier == '.asset-statuses' ){

				asset_statuses_arr  = [];

				$( identifier ).each(function(){
					chkCount++;
					if( $(this).is(':checked') ){
						totalChekd++;
						asset_statuses_arr.push( $(this).val() );
					}else{
						unChekd++;
					}
				});

				if( chkCount > 0 && ( chkCount == totalChekd ) ){
					$( '#check-all-statuses' ).prop( 'checked', true );
				}else{
					$( '#check-all-statuses' ).prop( 'checked', false );
				}

				return asset_statuses_arr;

			}else if( identifier == '.result-result-statuses' ){

				asset_audit_statuses_arr 	= [];

				$( identifier ).each(function(){
					chkCount++;
					if( $(this).is(':checked') ){
						totalChekd++;
						asset_audit_statuses_arr.push( $(this).val() );
					}else{
						unChekd++;
					}
				});

				if( chkCount > 0 && ( chkCount == totalChekd ) ){
					$( '#check-all-result-statuses' ).prop( 'checked', true );
				}else{
					$( '#check-all-result-statuses' ).prop( 'checked', false );
				}

				return asset_audit_statuses_arr;
			}

		}
	});
</script>
