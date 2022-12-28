<div class="row">
	<div class="row">
		<div class="x_panel no-border">
			<div class="row">
				<div class="x_content">
					<div class="row" style="margin-bottom:10px;">
						<div class="col-lg-12 col-md-12 col-sm-12 zindex_99">
							<div class="row">
								<div class="col-lg-4 col-md-4 col-sm-4 zindex_99 pull-right">
									<?php $this->load->view( 'webapp/_partials/search_bar' ); ?>
								</div>
							</div>
						</div>
					</div>

					<div class="clearfix"></div>
					<div class="table-responsive alert alert-ssid alert-results" role="alert" style="overflow-y: hidden;" >
						<table id="datatable-standard" class="table table-responsive" style="margin-bottom:0px; font-size:90%; font-weight:300" >
							<thead>
								<tr>
									<th width="20%">CHECKLIST TYPE</th>
									<th width="25%">SITE NAME</th>
									<th width="15%">SITE REF</th>
									<th width="15%">COMPLETED BY</th>
									<th width="15%">COMPLETION DATE</th>
									<th width="10%">STATUS</th>
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
</div>

<script type="text/javascript">
	$( document ).ready(function(){

		var search_str   		= null;
		var start_index	 		= 0;
		var where 				= false;

		//Pagination links
		$( "#table-results" ).on( "click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $( this ).find('a').data('ciPaginationPage');
			var search_str 	= encodeURIComponent( $( '#search_term' ).val() );
			load_data( search_str, where, start_index );
		});
		
		load_data( search_str, where, start_index );

		function load_data( search_str, where, start_index ){
			$.ajax({
				url:"<?php echo base_url( 'webapp/job/checklist_search' ); ?>",
				method:"POST",
				data:{ search_term:search_str, where:where, start_index:start_index },
				success:function(data){
					$( '#table-results' ).html(data);
				}
			});
		}

		$( '#search_term' ).keyup(function(){

			var search 		= encodeURIComponent( $(this).val() );
			if( search.length > 0 ){
				load_data( search, where, start_index );
			}else{
				load_data( search_str, where, start_index );
			}
		});

	});
</script>