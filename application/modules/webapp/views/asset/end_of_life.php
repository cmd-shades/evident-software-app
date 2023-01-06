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
				<!-- Filter toggle + search bar -->
				<div class="row">
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 hide">
						<?php //$this->load->view('webapp/_partials/filters'); ?>
						<?php //$this->load->view('webapp/_partials/center_options'); ?>
					</div>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
						
						<div id="eol-group" class='filter-container'>
							<div class='filter-clear pointer' title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
							<div class='filter-heading'><i class="fas fa-filter filter-icon"></i><span style="font-size:14px;margin-left:10px;">Audit Result Status <span class='filter-count'></span></span></div>
							<div class='filter-dropdown' style="display:none;">
								<section class='active-filters'>
									<?php if( !empty( $eol_statuses ) ) { foreach( $eol_statuses as $k => $status ){ ?>
										<div class='filter-item'>
											<input id="fil-ar-<?php echo $k; ?>" type="checkbox" class='filter-checkbox audit_statuses' value="<?php echo $status->eol_group_max; ?>" <?php echo ( $status->eol_group_max == $selected_group ) ? 'checked' : '' ?>>
											<label for = "fil-ar-<?php echo $k; ?>" class='filter-label'><?php echo ucwords( $status->eol_group_text_alt ); ?></label>
										</div>
									<?php } } ?>
								</section>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 hide">
						<?php //$this->load->view('webapp/_partials/search_bar'); ?>
					</div>
				</div>
			
				<div class="clearfix"></div>
				<div class="table-responsive alert alert-ssid alert-results" role="alert" style="overflow-y: hidden;" >
					<table id="datatable" class="table table-responsive" style="margin-bottom:0px;width:100%" >
						<thead>
							<tr>
								<!-- <th width="5%">ID</th> -->
								<th width="25%">Asset Name</th>
								<th width="15%">Type</th>
								<th width="15%">Unique ID</th>
								<th width="15%">End of Life</th>
								<th width="15%">Expires in (days)</th>
								<th width="15%"><span class="pull-right">Replacement Cost</span></th>
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
		var asset_types_arr		= [];
		var asset_statuses_arr	= [];
		var start_index	 		= 0;
		var period_days	 		= $( '#period_days option:selected').val(); //Default is 1 year if it's not set
		
		//Load default brag-statuses
		$('.asset-statuses').each(function(){
			if( $(this).is(':checked') ){
				asset_statuses_arr.push( $(this).val() );
			}
		});
		
		$('.asset-types').each(function(){
			if( $(this).is(':checked') ){
				asset_types_arr.push( $(this).val() );
			}
		});
		
		load_data( search_str, asset_statuses_arr, asset_types_arr, start_index, period_days );
		
		//Do Search when filters are changed
		$('.asset-statuses, .asset-types').change(function(){
			asset_types_arr =  get_statuses( '.asset-types' );
			asset_statuses_arr =  get_statuses( '.asset-statuses' );
			load_data( search_str, asset_statuses_arr, asset_types_arr, start_index, period_days );
		});
	
		//Do search when All is selected
		$('#check-all-statuses, #check-all-types').change(function(){
			var search_str  = $('#search_term').val();
			
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
				
				asset_statuses_arr  =  get_statuses( '.asset-statuses' );
				
			}else if( identifier == 'check-all-types' ){
				if( $(this).is(':checked') ){
					$('.asset-types').each(function(){
						$(this).prop( 'checked', true );
					});
				}else{
					$('.asset-types').each(function(){
						$(this).prop( 'checked', false );
					});
				}
					
				asset_types_arr 	=  get_statuses( '.asset-types' );
			}
			load_data( search_str, asset_statuses_arr, asset_types_arr, start_index, period_days );
		});

		//Get result when EOL selectors are changed
		$( '#period_days' ).change( function(){
			var period_days = $('option:selected', this ).val();
			load_data( search_str, asset_statuses_arr, asset_types_arr, start_index, period_days );
		} );
		
		
		//Pagination links
		$("#table-results").on("click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $(this).find('a').data('ciPaginationPage');
			load_data( search_str, asset_statuses_arr, asset_types_arr, start_index, period_days );
		});
		
		function load_data( search_str, asset_statuses_arr, asset_types_arr, start_index, period_days ){
			$.ajax({
				url:"<?php echo base_url('webapp/asset/lookup'); ?>",
				method:"POST",
				data:{search_term:search_str,asset_statuses:asset_statuses_arr,asset_types:asset_types_arr,start_index:start_index, period_days:period_days },
				success:function(data){
					$('#table-results').html(data);
				}
			});
		}
		
		$('#search_term').keyup(function(){
			var search = encodeURIComponent( $(this).val() );
			if( search.length > 0 ){
				load_data( search , asset_statuses_arr, asset_types_arr, start_index, period_days );
			}else{
				load_data( search_str, asset_statuses_arr, asset_types_arr, start_index, period_days );
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
				
			}else if( identifier == '.asset-types' ){
				
				asset_types_arr 	= [];
				
				$( identifier ).each(function(){
					chkCount++;
					if( $(this).is(':checked') ){
						totalChekd++;
						asset_types_arr.push( $(this).val() );
					}else{
						unChekd++;
					}
				});
				
				if( chkCount > 0 && ( chkCount == totalChekd ) ){
					$( '#check-all-types' ).prop( 'checked', true );
				}else{
					$( '#check-all-types' ).prop( 'checked', false );
				}
				
				return asset_types_arr;
			}

		}
		
		assetEOLGroup = new setupResultFilter($("#eol-group"), false)

		assetEOLGroup.update = function(){
			period_days = (assetEOLGroup.getFilters() == 0) ? -1 : assetEOLGroup.getFilters()
			load_data( search_str, asset_statuses_arr, asset_types_arr, start_index, period_days );
		}
	});
</script>

