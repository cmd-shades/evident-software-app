<div id="content-dashboard" class="row">
	<div class="x_panel no-border">
		<div class="row">
			<div class="x_content">
				<div class="row">
					<div class="col-lg-5 col-md-6 col-sm-12 col-xs-12">
						<h2>Content</h2>
					</div>
					<div class="col-lg-7 col-md-6 col-sm-12 col-xs-12">
						<div class="row">
							<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
								<a href="<?php echo base_url( '/webapp/content/create' ); ?>" class="btn btn-block btn-new">New Content</a>
							</div>
							<?php 
							if( !empty( $content_providers ) ){ ?>
								<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
									<select class="btn-block btn-primary" name="filter[provider]">
										<option value="">Please select</option>
										<?php 
										foreach( $content_providers as $key => $row ){ ?>
											<option value="<?php echo $key; ?>"><?php echo $row->provider_name; ?></option>
										<?php 
										} ?>
									</select>
								</div>
							<?php 
							} ?>
							<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
								<?php
								$this->load->view( 'webapp/_partials/search_bar' ); ?>
							</div>

							<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
								<a href="<?php echo base_url( '/webapp/settings/module/'.$module_id ); ?>" class="btn btn-block btn-secondary">Settings</a>
							</div>
						</div>
					</div>
				</div>

				<!-- Search by Filters -->
				<div id="filters-container" class="table-responsive filters-container <?php echo $module_identier; ?>-color <?php echo 'border-'.$module_identier; ?>" role="alert">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="filters">
							<div class="col-md-3 col-sm-3 col-xs-12" style="margin:0">
								<div class="row">
									<h5 class="text-bold text-auto">Content statuses</h5>
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-6">
											<span class="checkbox" style="margin:0">
												<label><input type="checkbox" id="check-all" value="all" > All</label>
											</span>
										</div>
										<?php if( !empty( $site_statuses ) ) { foreach( $site_statuses as $k =>$status ){ ?>
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
				<div class="table-responsive alert alert-ssid" role="alert" style="overflow-y: hidden;" >
					<table id="datatable" class="table table-responsive" style="margin-bottom:0px;" >
						<thead>
							<tr>
								<th width="5%">ID</th>
								<th width="20%">Content Title</th>
								<th width="15%">Provider Name</th>
								<th width="15%">Provider Reference Code</th>
								<th width="10%">Release Year</th>
								<th width="10%">Certificates/Age Rating</th>
								<th width="10%">Is Active</th>
								<th width="15%">IMDb link</th>
							</tr>
						</thead>
						<tbody id="table-results">

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){

		var search_str   		= null;
		var content_provider	= null;
		var start_index	 		= 0;

		if( $( "*[name='filter[provider]']" ).val() != '' ){
			$(content_provider).val( $(this).val() );
		}

		load_data( search_str, content_provider );

		//Do Search when filters are changed
		$( "*[name='filter[provider]']" ).change( function(){
			var content_provider 	= $( this ).val();
			var search_str 			= encodeURIComponent( $( '#search_term' ).val() );
			load_data( search_str, content_provider );
		});

		//Pagination links
		$( "#table-results" ).on( "click", "li.page", function( event ){
			event.preventDefault();
			//var off_set = $(this).data('ciPaginationPage');
			var search_str 			= encodeURIComponent( $( '#search_term' ).val() );
			var start_index 		= $( this ).find( 'a' ).data( 'ciPaginationPage' );
			var content_provider 	= $( "[name='filter[provider]']" ).val();
			load_data( search_str, content_provider, start_index );
		});

		function load_data( search_str, content_provider, start_index ){
			$.ajax({
				url:"<?php echo base_url( 'webapp/content/lookup' ); ?>",
				method:"POST",
				data:{ 
					search_term:search_str,
					content_provider:content_provider,
					start_index:start_index
				},
				success:function( data ){
					$( '#table-results' ).html( data );
				}
			});
		}

 		$( '#search_term' ).keyup( function(){
			var content_provider 	= $( "[name='filter[provider]']" ).val();
			var search 				= encodeURIComponent( $(this).val() );
			if( search.length > 0 ){
				load_data( search , content_provider );
			} else {
				load_data( search_str, content_provider );
			}
		});
	});
</script>

