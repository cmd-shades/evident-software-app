
<style>
	body {
		background-color: #FFFFFF;
	}
	.table>thead>tr>th {
		cursor:pointer;
	}

</style>

<div class="row">
	<div class="x_panel no-border">
		<div class="row">
			<div class="x_content">
				<!-- Module statistics and info -->
				<div class="module-statistics table-responsive alert" role="alert" style="overflow-y: hidden;display:block" >
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="col-md-3 col-sm-3 col-xs-12" style="margin:0">
							<div class="rows">
								<legend class="text-center" >Site Compliance</legend>
								<div class="text-center">
									<h1 style="color:<?php echo (!empty($compliance->compliance) && ($compliance->compliance == 100)) ? 'green' : ((empty($compliance->compliance)) ? '' : 'red') ;?>"><?php echo !empty($compliance->compliance) ? (number_format($compliance->compliance, 2) + 0).'%' : '0';?></h1>
									<div><?php echo !empty($compliance->sites_ok) ? $compliance->sites_ok : '0';?> Site<?php echo (!empty($compliance->sites_ok) && ($compliance->sites_ok == 1)) ? ' is' : 's are';?> compliant</div>
									<div><?php echo !empty($compliance->sites_not_ok) ? $compliance->sites_not_ok : '0';?> Site<?php echo (!empty($compliance->sites_ok) && ($compliance->sites_not_ok == 1)) ? ' is' : 's are';?> non-compliant</div>
								</div>
							</div>
						</div>
						<div class="col-md-3 col-sm-3 col-xs-12" style="margin:0">
							<div class="rows">
								<legend class="text-center" >Sites Due for Inspection</legend>
								<div class="x_content text-center">
									<div>You currently have <h1><?php echo (!empty($sites_due_insp)) ? count((array)$sites_due_insp) : 0; ?></h1> Sites due for inspection</div>								
								</div>
							</div>
						</div>
						<div class="col-md-3 col-sm-3 col-xs-12" style="margin:0">
							<div class="rows">
								<legend class="text-center" >Assets Due for Inspection</legend>
								<div class="x_content text-center">
									<div>You currently have <h1><?php echo (!empty($sites_due_insp)) ? count((array)$sites_due_insp) : 0; ?></h1> assets due for inspection</div>								
								</div>
							</div>
						</div>
						<div class="col-md-3 col-sm-3 col-xs-12" style="margin:0">
							<div class="rows">
								<legend class="text-center" >Assets nearing End of Life <span class="hidden-sm hidden-sm">(EOL)</span></legend>
								<div class="x_content text-center">
									<div>You currently have <h1><?php echo (!empty($sites_due_insp)) ? count((array)$sites_due_insp) : 0; ?></h1> assets nearing End of Life</div>								
								</div>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				
				<!-- Filter toggle + search bar -->
				<div class="row hide">				
					<div class="col-md-4 col-sm-4 col-xs-12"><?php //$this->load->view('webapp/_partials/filters')?></div>					
					<div class="col-md-4 col-sm-4 col-xs-12"><?php $this->load->view('webapp/_partials/center_options') ?></div>					
					<div class="col-md-4 col-sm-4 col-xs-12 pull-right"><?php $this->load->view('webapp/_partials/search_bar') ?></div>					
				</div>
				
				<!-- Search by Filters -->
				<div id="filters-container" class="table-responsive filters-container <?php echo $module_identier; ?>-color <?php echo 'border-'.$module_identier; ?>" role="alert" style="overflow-y: hidden; display:none" >
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="filters">
							<div class="col-md-3 col-sm-3 col-xs-12" style="margin:0">
								<div class="row">
									<h5 class="text-bold text-auto">Site statuses</h5>
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-6">
											<span class="checkbox" style="margin:0">
												<label><input type="checkbox" id="check-all" value="all" > All</label>
											</span>
										</div>
										<?php if (!empty($site_statuses)) {
										    foreach ($site_statuses as $k =>$status) { ?>
											<div class="col-md-6 col-sm-6 col-xs-6">
												<span class="checkbox" style="margin:0">
													<label><input type="checkbox" class="user-types" name="site_statuses[]" value="<?php echo $status->status_id; ?>" > <?php echo ucwords($status->status_name); ?></label>
												</span>
											</div>
										<?php }
										    } ?>							
									</div>
								</div>
							</div>
						</div>
						<!-- Clear Filter -->
						<?php $this->load->view('webapp/_partials/clear_filters.php') ?>
					</div>
					<div class="clearfix"></div>
				</div>
				
				<div class="clearfix"></div>
				<hr>
				<div class="row">
					<div class="col-md-12">
						<div class="row" id="table-results">
							
						</div>
					</div>
				</div>

				<div class="hide alert alert-ssid" role="alert" style="overflow: hidden; margin-bottom:0" >
					<div class="">
						<div class="row" id="table-results">
							
						</div>
					</div>						
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
		
		//Load default brag-statuses
		$('.user-types').each(function(){
			if( $(this).is(':checked') ){
				site_statuses_arr.push( $(this).val() );
			}
		});
		
		load_data( search_str, site_statuses_arr );
		
		//Do Search when filters are changed
		$('.user-types').change(function(){
			site_statuses_arr =  get_statuses( '.user-types' );
			load_data( search_str, site_statuses_arr );
		});
	
		//Do search when All is selected
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
			site_statuses_arr =  get_statuses( '.user-types' );
			load_data( search_str, site_statuses_arr );
		});

		//Pagination links
		$("#table-results").on("click", "li.page", function( event ){
			event.preventDefault();
			//var off_set = $(this).data('ciPaginationPage');
			var start_index = $(this).find('a').data('ciPaginationPage');
			load_data( search_str, site_statuses_arr, start_index );
		});
		
		function load_data( search_str, site_statuses_arr, start_index ){
			$.ajax({
				url:"<?php echo base_url('webapp/alert/lookup'); ?>",
				method:"POST",
				data:{search_term:search_str,site_statuses:site_statuses_arr,start_index:start_index},
				success:function(data){
					$('#table-results').html(data);
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
		
		function get_statuses( identifier ){
			site_statuses_arr = [];
			var chkCount  = 0;
			var totalChekd= 0;
			var unChekd   = 0;
			$( identifier ).each(function(){
				chkCount++;
				if( $(this).is(':checked') ){
					totalChekd++;
					site_statuses_arr.push( $(this).val() );
				}else{
					unChekd++;
				}
			});

			if( chkCount > 0 && ( chkCount == totalChekd ) ){
				$( '#check-all' ).prop( 'checked', true );
			}else{
				$( '#check-all' ).prop( 'checked', false );
			}
			return site_statuses_arr;
		}
	});
</script>

