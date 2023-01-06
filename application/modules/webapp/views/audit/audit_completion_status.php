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
						
						<div id="audit-completion-status" class='filter-container'>
							<div class='filter-clear pointer' title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
							<div class='filter-heading'><i class="fas fa-filter filter-icon"></i><span style="font-size:14px;margin-left:10px;">Audit Completion Status<span class='filter-count'></span></span></div>
							<div class='filter-dropdown' style="display:none;">
								<div class='filter-item'>
								  <input type="checkbox" class='filter-checkbox filter-select-all results_statuses' id="check-all-result" checked>
								  <label class='filter-label' for="check-all-result">All</label>
								</div>
								<section class='active-filters'>
									<?php if( !empty( $progress_statuses ) ) { foreach( $progress_statuses as $k => $status ){ ?>
										<div class='filter-item'>
											<input id="fil-ar-<?php echo $k; ?>" type="checkbox" class='filter-checkbox audit_statuses' value="<?php echo $status; ?>" <?php echo ( ( strtolower( $status ) == strtolower( $selected_group ) ) || ( in_array( strtolower( $selected_group ), ['all', 'total'] ) ) ) ? 'checked' : '' ?>>
											<label for = "fil-ar-<?php echo $k; ?>" class='filter-label'><?php echo ucwords( $status ); ?></label>
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
								<th width="20%">EviDoc Type</th>
								<th width="15%">Asset Unique ID</th>
								<!-- <th width="15%">Building Name</th>
								<th width="10%">Next Audit Date</th> -->
								<th width="15%">Result Status</th>
								<!-- <th width="10%">Assets EOL Date</th> -->
								<th width="15%">Audited By</th>
								<th width="10%">Status</th>
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
		var audit_statuses_arr	= <?php echo ( !empty( $selected_group ) ) ? '["'.( urldecode( $selected_group ) ).'"]' : '[]';  ?>;
		var start_index	 		= 0;
		var result_status_id	= $( '#audit_result_status_id option:selected').val(); //Default is 1 year if it's not set
		
		console.log( audit_statuses_arr );
		
		//Load default brag-statuses
		$('.audit_statuses').each(function(){
			if( $(this).is(':checked') ){
				audit_statuses_arr.push( $(this).val() );
			}
		});
		
		$( "#progress_statuses" ).on( "change", function(){
			var search_str  = $('#search_term').val();
			audit_types_arr =  get_statuses( '.audit-types' );
			audit_statuses_arr = encodeURIComponent( $(this).val() );
			load_data( search_str, audit_statuses_arr, audit_types_arr, start_index, result_status_id );
		});
		
		$('.audit-types').each(function(){
			if( $(this).is(':checked') ){
				audit_types_arr.push( $(this).val() );
			}
		});



			
			console.log( 'first audit_statuses_arr' );
			console.log( audit_statuses_arr );
			
		
		load_data( search_str, audit_statuses_arr, audit_types_arr, start_index, result_status_id );
		
		
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
		
	
		
		auditCompletionStatus = new setupResultFilter($("#audit-completion-status"), true)

		auditCompletionStatus.update = function(){
			
			
			
			audit_statuses_arr = (auditCompletionStatus.getFilters() == 0) ? '' : auditCompletionStatus.getFilters()
			
					console.log( 'Again:: audit_statuses_arr' );
		console.log( audit_statuses_arr );
			
			load_data( search_str, audit_statuses_arr, audit_types_arr, start_index, result_status_id  );
		}
	});
</script>

