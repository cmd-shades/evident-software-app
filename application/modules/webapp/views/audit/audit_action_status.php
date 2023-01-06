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
				<div class="row">
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 hide">&nbsp;\</div>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<h5 class="text-bold">Filter by Audit Action Status</h5>
						<select name="audit_result_status_id" id="audit_result_status_id" class="form-control" required>
							<option><i class="fas fa-filter"></i>Results filter</option>
							<option value="">All (no filter)</option>
							<?php if( !empty( $result_statuses ) ){ foreach( $result_statuses as $result_group ){ ?>
								<option value="<?php echo $result_group->audit_result_status_id; ?>" <?php echo ( $result_group->result_status_group == $selected_group ) ? 'selected="selected"' : '' ?> ><?php echo ucwords( $result_group->result_status ); ?></option>
							<?php } } ?>
						</select>
						<br/>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 hide">
						<?php //$this->load->view('webapp/_partials/search_bar'); ?>
					</div>
				</div>
				
				<!-- Search Filters -->
				<div id="filters-container" class="table-responsive filters-container <?php echo $module_identier; ?>-color <?php echo 'border-'.$module_identier; ?>" role="alert" style="overflow-y: hidden; display:none" >
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="filters">
							<div class="col-md-3 col-sm-3 col-xs-12" style="margin:0">
								<div class="row">
									<h5 class="text-bold text-auto">Audit Action statuses</h5>
									<div class="row">
										<div class="col-md-12 col-sm-12 col-xs-12">
											<span class="checkbox" style="margin:0">
												<label><input type="checkbox" id="check-all-statuses" value="all" > All</label>
											</span>
										</div>
										<?php if( !empty($audit_statuses) ) { foreach( $audit_statuses as $k =>$status ){ ?>
											<div class="col-md-12 col-sm-12 col-xs-12">
												<span class="checkbox" style="margin:0">
													<label><input type="checkbox" class="audit-statuses" name="audit_statuses[]" value="<?php echo urlencode($status); ?>" > <?php echo ucwords( $status ); ?></label>
												</span>
											</div>
										<?php } } ?>							
									</div>							
								</div>
							</div>
							
							<div class="col-md-9 col-sm-4 col-xs-12" style="margin:0">
								<div class="row">
									<h5 class="text-bold text-auto">Audit type</h5>
									<div class="row">
										<div class="col-md-3 col-sm-6 col-xs-12">
											<span class="checkbox" style="margin:0">
												<label><input type="checkbox" id="check-all-types" > All types</label>
											</span>
										</div>
										<?php if( !empty( $audit_types ) ) { foreach( $audit_types as $k =>$audit_type ){ ?>
											<div class="col-md-3 col-sm-6 col-xs-12">
												<span class="checkbox" style="margin:0">
													<label><input type="checkbox" class="audit-types" name="audit_types[]" value="<?php echo $audit_type->audit_type_id; ?>" > <?php echo ucwords( $audit_type->audit_type ); ?></label>
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
								<!-- <th width="5%">ID</th> -->
								<th width="25%">Audit Type</th>
								<th width="15%">Asset Name</th>
								<th width="15%">Site Name</th>
								<th width="15%">Vehicle Reg</th>
								<th width="20%">Audited By</th>
								<th width="10%">Action Status</th>
							</tr>
						</thead>
						<tbody id="table-results">
							
						</tbody>
					</table>
				</div>
				<div class="clearfix"></div>
				<div class="row">
					<div class="col-md-2 col-sm-3 col-xs-12">
						<a href="<?php echo base_url('/webapp/audit/create' ); ?>" class="btn btn-block btn-success success-shadow">Add new</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
	
		var search_str   		= null;
		var audit_types_arr		= [];
		var audit_statuses_arr	= [];
		var start_index	 		= 0;
		var result_status_id	= $( '#audit_result_status_id option:selected').val(); //Default is 1 year if it's not set
		
		//Load default brag-statuses
		$('.audit-statuses').each(function(){
			if( $(this).is(':checked') ){
				audit_statuses_arr.push( $(this).val() );
			}
		});
		
		$('.audit-types').each(function(){
			if( $(this).is(':checked') ){
				audit_types_arr.push( $(this).val() );
			}
		});
		
		load_data( search_str, audit_statuses_arr, audit_types_arr, start_index, result_status_id );
		
		//Do Search when filters are changed
		$('.audit-statuses, .audit-types').change(function(){
			audit_types_arr =  get_statuses( '.audit-types' );
			audit_statuses_arr =  get_statuses( '.audit-statuses' );
			load_data( search_str, audit_statuses_arr, audit_types_arr, start_index, result_status_id );
		});
	
		//Do search when All is selected
		$('#check-all-statuses, #check-all-types').change(function(){
			var search_str  = $('#search_term').val();
			
			var identifier = $(this).attr('id');
			
			if( identifier == 'check-all-statuses' ){
				if( $(this).is(':checked') ){
					$('.audit-statuses').each(function(){
						$(this).prop( 'checked', true );
					});
				}else{
					$('.audit-statuses').each(function(){
						$(this).prop( 'checked', false );
					});
				}
				
				audit_statuses_arr  =  get_statuses( '.audit-statuses' );
				
			}else if( identifier == 'check-all-types' ){
				if( $(this).is(':checked') ){
					$('.audit-types').each(function(){
						$(this).prop( 'checked', true );
					});
				}else{
					$('.audit-types').each(function(){
						$(this).prop( 'checked', false );
					});
				}
					
				audit_types_arr 	=  get_statuses( '.audit-types' );
			}
			load_data( search_str, audit_statuses_arr, audit_types_arr, start_index, result_status_id  );
		});

		//Get result when EOL selectors are changed
		$( '#audit_result_status_id' ).change( function(){
			var result_status_id = $('option:selected', this ).val();
			load_data( search_str, audit_statuses_arr, audit_types_arr, start_index, result_status_id  );
		} );
		
		//Pagination links
		$("#table-results").on("click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $(this).find('a').data('ciPaginationPage');
			load_data( search_str, audit_statuses_arr, audit_types_arr, start_index, result_status_id  );
		});
		
		function load_data( search_str, audit_statuses_arr, audit_types_arr, start_index, result_status_id  ){
			$.ajax({
				url:"<?php echo base_url('webapp/audit/lookup'); ?>",
				method: "POST",
				dataType: "json",
				data:{search_term:search_str,audit_statuses:audit_statuses_arr,audit_types:audit_types_arr,start_index:start_index, audit_result_status_id:result_status_id },
				success:function(data){
					$( '#table-results' ).html( data.audits );
				}
			});
		}
		
		$('#search_term').keyup(function(){
			var search = encodeURIComponent( $(this).val() );
			if( search.length > 0 ){
				load_data( search , audit_statuses_arr, audit_types_arr, start_index, result_status_id );
			}else{
				load_data( search_str, audit_statuses_arr, audit_types_arr, start_index, result_status_id );
			}
		});
		
		function get_statuses( identifier ){

			var chkCount  = 0;
			var totalChekd= 0;
			var unChekd   = 0;
			
			var idClass	  = '';
			
			if( identifier == '.audit-statuses' ){
				
				audit_statuses_arr  = [];
				
				$( identifier ).each(function(){
					chkCount++;
					if( $(this).is(':checked') ){
						totalChekd++;
						audit_statuses_arr.push( $(this).val() );
					}else{
						unChekd++;
					}
				});
				
				if( chkCount > 0 && ( chkCount == totalChekd ) ){
					$( '#check-all-statuses' ).prop( 'checked', true );
				}else{
					$( '#check-all-statuses' ).prop( 'checked', false );
				}
				
				return audit_statuses_arr;
				
			}else if( identifier == '.audit-types' ){
				
				audit_types_arr 	= [];
				
				$( identifier ).each(function(){
					chkCount++;
					if( $(this).is(':checked') ){
						totalChekd++;
						audit_types_arr.push( $(this).val() );
					}else{
						unChekd++;
					}
				});
				
				if( chkCount > 0 && ( chkCount == totalChekd ) ){
					$( '#check-all-types' ).prop( 'checked', true );
				}else{
					$( '#check-all-types' ).prop( 'checked', false );
				}
				
				return audit_types_arr;
			}

		}
	});
</script>

