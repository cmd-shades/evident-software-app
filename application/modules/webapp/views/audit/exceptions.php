
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
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12" style='margin-bottom: 10px;'>
					
						<div id="audit-result-status" class='filter-container'>
							<div class='filter-clear pointer' title = "Clear Filter" style="display:none;"><i class="fas fa-times"></i></div>
							<div class='filter-heading'><i class="fas fa-filter filter-icon"></i><span style="font-size:14px;margin-left:10px;">Exception Status <span class='filter-count'></span></span></div>
							<div class='filter-dropdown' style="display:none;">
								<section class='active-filters'>
									<?php if( !empty($exception_statuses) ) { foreach( $exception_statuses as $k => $status ){ ?>
										<div class='filter-item'>
											<input id="fil-ar-<?php echo $k; ?>" type="checkbox" class='filter-checkbox audit_statuses' value="<?php echo $status->action_status_id; ?>" <?php echo ( $status->action_status_group == $selected_group ) ? 'checked="checked"' : '' ?>>
											<label for = "fil-ar-<?php echo $k; ?>" class='filter-label'><?php echo ucwords( $status->action_status_alt ); ?></label>
										</div>
									<?php } } ?>
								</section>
							</div>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="table-responsive alert alert-ssid alert-results" role="alert" style="overflow-y: hidden;" >
					<table id="datatable" class="table table-responsive" style="margin-bottom:0px;width:100%" >
						<thead>
							<tr>
								<th width="10%">Audit Result Status</th>
								<!-- <th width="">Recommendations</th> -->
								<th width="35%">Recommendation / Failure Reasons</th>
								<!-- <th width="">Additonal<br />Notes</th> -->
								<!-- <th class="text-center" width="">Site<br />Id</th> -->
								<!-- <th class="text-center" width="">Asset<br />Id</th> -->
								<!-- <th class="text-center"width="">Vehicle<br />Reg</th> -->
								<th width="10%">Action Due Date</th>
								<!-- <th width="">Next Audit<br />Date</th> -->
								<th width="10%" class="text-center" width="">Priority Rating</th>
								<!-- <th class="text-right" width="">Estimated<br />Repair cost</th> -->
								<th width="10%">Date Created</th>
								<th width="15%">Audit Type</th>
								<th width="10%">Exception Status</th>
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
		var audit_types_arr		= [];
		var audit_statuses_arr	= [];
		var start_index	 		= 0;
		// var action_status_id	= $( '#action_status_id option:selected' ).val();
		var action_status_id	= $( '#audit-result-status input:checked' ).val();

		load_data( search_str, audit_statuses_arr, audit_types_arr, start_index, action_status_id );

		//Pagination links
		$("#table-results").on("click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $(this).find('a').data('ciPaginationPage');
			load_data( search_str, audit_statuses_arr, audit_types_arr, start_index, action_status_id  );
		});
		
		function load_data( search_str, audit_statuses_arr, audit_types_arr, start_index, action_status_id, ){
			$.ajax({
				url:"<?php echo base_url( 'webapp/audit/exceptions_lookup' ); ?>",
				method: "POST",
				dataType: "json",
				data:{search_term:search_str,audit_statuses:audit_statuses_arr,audit_types:audit_types_arr,start_index:start_index, action_status_id:action_status_id },
				success:function(data){
					$( '#table-results' ).html( data.exc_data );
				}
			});
		}
		
		auditResultStatus = new setupResultFilter($("#audit-result-status"), false)

		auditResultStatus.update = function(){
			action_status_id = auditResultStatus.getFilters()
			load_data( search_str, audit_statuses_arr, audit_types_arr, start_index, action_status_id  );
		}
		
		$('#search_term').keyup(function(){
			var search = encodeURIComponent( $(this).val() );
			if( search.length > 0 ){
				load_data( search , audit_statuses_arr, audit_types_arr, start_index, action_status_id );
			}else{
				load_data( search_str, audit_statuses_arr, audit_types_arr, start_index, action_status_id );
			}
		});
		
	});
</script>

