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
								<a href="<?php echo base_url('/webapp/job/new_schedule_frequency' ); ?>" class="btn btn-block btn-success success-shadow" title="Click to add a New EviDoc name"><i class="fas fa-plus-circle" title="" style="font-size: 18px;"></i></a><br/>
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
						<?php $this->load->view( 'webapp/_partials/search_bar' ); ?>
					</div>
				</div>
				
				<div class="clearfix"></div>
				<div class="table-responsive alert alert-ssid alert-results" role="alert" style="overflow-y: hidden;" >
					<table id="datatable" class="table table-responsive" style="margin-bottom:0px;width:100%" >
						<thead>
							<tr>
								<th width="8%">ID</th>
								<th width="15%">Schedule Name</th>
								<th width="20%">Schedule Ref</th>
								<th width="12%" class="text-center center">Activities Required</th>
								<th width="15%">Date Created</th>
								<th width="15%">First Due Date</th>
								<th width="15%">Completion Status</th>
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
		var start_index	 	= 0;
		var where 			= {};

		load_data( search_str, where, start_index );
	
		//Pagination links
		$("#table-results").on( "click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $( this ).find('a').data( 'ciPaginationPage' );
			load_data( search_str, where );
		});
		
		function load_data( search_str, where, start_index ){
			$.ajax({
				url:"<?php echo base_url( 'webapp/job/schedules_list' ); ?>",
				method:"POST",
				data:{ search_term:search_str, where:where, start_index:start_index },
				success:function( data ){
					$('#table-results').html(data);
				}
			});
		}
		
		$('#search_term').keyup( function(){
			var search = encodeURIComponent( $(this).val() );
			if( search.length > 0 ){
				load_data( search , where );
			}else{
				load_data( search_str, where );
			}
		});

	});
</script>

