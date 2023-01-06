<?php 
	$system_id 	= ( !empty( $system_id ) ) 	? $system_id 	: ( ( $this->input->get( 'system_id' ) )	? $this->input->get( 'system_id' ) 	: false ); 
	$group 		= ( !empty( $group ) ) 		? $group 		: ( ( $this->input->get( 'group' ) ) 		? $this->input->get( 'group' ) 		: false ); 
?>

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
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 hide">&nbsp;\</div>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
						<h5 class="text-bold">Filter by Audit Result Status</h5>
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
				
				<!-- Search by Filters -->
				<div id="filters-container" class="hide table-responsive filters-container <?php echo $module_identier; ?>-color <?php echo 'border-'.$module_identier; ?>" role="alert" style="overflow-y: hidden; display:none" >
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="filters">
							<div class="col-md-3 col-sm-3 col-xs-12" style="margin:0">
								<div class="row">
									<h5 class="text-bold text-auto">Site statuses (regular)</h5>
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-6">
											<span class="checkbox" style="margin:0">
												<label><input type="checkbox" id="check-all" value="all" > All</label>
											</span>
										</div>
										<?php if( !empty($site_statuses) ) { foreach( $site_statuses as $k =>$status ){ ?>
											<div class="col-md-6 col-sm-6 col-xs-6">
												<span class="checkbox" style="margin:0">
													<label><input type="checkbox" class="user-types" name="site_statuses[]" value="<?php echo $status->status_id; ?>" > <?php echo ucwords( $status->status_name ); ?></label>
												</span>
											</div>
										<?php } } ?>							
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
				<div class="table-responsive alert alert-ssid alert-results" role="alert" style="overflow-y: hidden;" >
					<table id="datatable" class="table table-responsive" style="margin-bottom:0px;width:100%" >
						<thead>
							<tr>
								<!-- <th width="5%">ID</th> -->
								<th width="20%">Building Name </th>
								<th width="35%">Address</th>
								<th width="15%">Estate Name</th>
								<th width="15%">Postcode</th>
								<th width="15%">Result Status</th>								
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
		var result_status_id	= $( '#audit_result_status_id option:selected').val();
		var group          		= "<?php echo $group; ?>";
		var systemId          	= "<?php echo $system_id; ?>";
		var wheRe 				= {
			'site_statuses'		:site_statuses_arr,
			'result_status_id'	:result_status_id,
			'site_statuses'		:site_statuses_arr,
			'system_id'			:systemId,
			'group'				:group,
		};
		
		//Load default brag-statuses
		$('.user-types').each(function(){
			if( $(this).is(':checked') ){
				wheRe.site_statuses.push( $(this).val() );
			}
		});
		
		load_data( search_str, wheRe, start_index );
		
		//Do Search when filters are changed
		$('.user-types').change(function(){
			wheRe.site_statuses =  get_statuses( '.user-types' );
			load_data( search_str, wheRe, start_index );
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
			wheRe.site_statuses =  get_statuses( '.user-types' );
			load_data( search_str, wheRe, start_index );
		});

		
		//Get result when EOL selectors are changed
		$( '#audit_result_status_id' ).change( function(){
			var result_status_id = $('option:selected', this ).val();
			wheRe.result_status_id = result_status_id;
			load_data( search_str, wheRe, start_index );
		} );
		
		//Pagination links
		$("#table-results").on("click", "li.page", function( event ){
			event.preventDefault();
			//var off_set = $(this).data('ciPaginationPage');
			var start_index = $(this).find('a').data('ciPaginationPage');
			load_data( search_str, wheRe, start_index );
		});
		
		function load_data( search_str, wheRe, start_index ){
			$.ajax({
				url:"<?php echo base_url('webapp/site/lookup'); ?>",
				method:"POST",
				dataType: "json",
				data:{search_term:search_str, where:wheRe, start_index:start_index },
				success:function(data){
					$( '#table-results' ).html( data.sites );
				}
			});
		}
		
		$('#search_term').keyup(function(){
			var search = encodeURIComponent( $(this).val() );
			if( search.length > 0 ){
				load_data( search , wheRe, start_index );
			}else{
				load_data( search_str, wheRe, start_index );
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