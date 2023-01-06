<style>
	body {
		background-color: #FFFFFF;
	}
	.table>thead>tr>th {
		cursor:pointer;
	}

	div.xdsoft_datetimepicker{
		left: 1064.98px
	}

	.min-width-80{
		min-width: 100px;
	}
	
	.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
		padding: 8px 6px;
	}
</style>

<div class="row">
	<div class="row">
		<div class="x_panel no-border">
			<div class="row">
				<div class="x_content">
					<div class="row" style="margin-bottom:20px;">
						<div class="col-lg-12 col-md-12 col-sm-12 zindex_99">
							<div class="row">
								<div class="col-md-12 col-sm-12">
									<p><em>**This is an exact match search</em></p>
								</div>
								<form id="advanced-search-form" class="form-horizontal">
									
									<?php if( !empty( $searchable_fields ) ){ foreach( $searchable_fields as $k => $search_field ){ ?>
										<div class="col-md-3">
											<div class="input-group">
												<span class="input-group-addon"><?php echo $search_field->column_label; ?></span>
												<input class="input-field form-control" name="search_params[<?php echo $search_field->table_name.'-'.$search_field->column_name; ?>]" type="text" placeholder="<?php echo $search_field->column_label; ?>" value="" />
											</div>
										</div>
									<?php } } ?>

									<div class="col-md-3">
										<div class="input-group">
											<button id="advanced-search-btn" type="button" class="btn btn-block btn-default job-bg search-go" >Search</button>
										</div>
									</div>

								</form>
							</div>
						</div>
					</div>

					<div class="clearfix"></div>
					<div class="table-responsive alert alert-ssid alert-results" role="alert" style="overflow-y: hidden;" >
						<table id="datatable-standard" class="table sortable table-responsive" style="margin-bottom:0px; font-size:90%; font-weight:300" >
							<thead>
								<tr>
									<th width="4%">Job</th>
									<th width="18%">Job Type</th>
									<th width="8%">Job Date</th>
									<th width="15%">Works Required</th>
									<th width="15%">Building Name</th>
									<th width="12%">Assignee</th>
									<th width="10%">Status</th>
									<th width="10%">Discipline</th>
									<th width="8%">Region</th>
								</tr>
							</thead>
							<tbody id="table-results-advanced" >
								<tr>
									<th colspan="10" width="100%">No data to show</th>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="clearfix"></div>

				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$( document ).ready( function(){
		$( '#advanced-search-btn' ).click( function(){
			
			//var inputFileds	= $( ".input-field" ).length;
			
			/* var elem 	= $( ".input-field input[type='text']" );
			var count 	= elem.filter(function() {				
					return !$( this ).val( );				
				}).length;
				
			if ( count == elem.length ) {
				swal({
					type: 'error',
					text: 'Please provide at least one search term'
				});
				return false;
			} */
			
			var formData = $( 'form#advanced-search-form' ).serialize();
			
			$.ajax({
				url:"<?php echo base_url( 'webapp/job/advanced_job_search' ); ?>",
				method:"POST",
				data:formData,
				dataType: 'json',
				success:function( data ){
					if( data.status == 1 ){
						$( '#table-results-advanced' ).html( data.search_results );
					} else {
						$( '#table-results-advanced' ).html( '<tr><th colspan="10" width="100%">'+data.status_msg+'</th></tr>' );
					}
				}
			});
			
		});
	});
</script>
