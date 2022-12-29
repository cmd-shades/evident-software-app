<link rel="stylesheet" type="text/css" href="<?php echo base_url("assets/css/responsive-card-table.css"); ?>" />
<div class="row">
	<div class="x_panel no-border">
		<div class="x_content">
			<div class="profile-details-container">
				<div class="row alert alert-ssid" role="alert">
					<div class="col-md-12 col-sm-12 col-xs-12 alert-container">
						<div class="row">
							<div class="rows">
								<?php foreach ($stats_to_dashboard as $stats_key) { ?>
									<div class="row col-sm-3">
										<div class="rows text-center">
											<span class="indic_line_1"><?php echo $contract_statuses->$stats_key->status_name; ?></span>
											<br />
											<span class="indic_line_2"><?php echo (!empty($quick_stats->$stats_key)) ? $quick_stats->$stats_key : '0' ; ?></span>
										</div>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
						<div class="row">
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
								<a href="<?php echo base_url('/webapp/contract/add_contract'); ?>" class="btn btn-block btn-success success-shadow" title="Click to Book Jobs"><i class="fas fa-plus-circle" title="" style="font-size: 18px;"></i></a><br/>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 text-center zindex_99 hide">
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<?php $this->load->view('webapp/_partials/center_options'); ?>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 pull-right">
						<?php $this->load->view('webapp/_partials/search_bar'); ?>
					</div>
				</div>

				<div class="row row_filters">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="row">
							<div id="filters-container" class="table-responsive filters-container <?php echo $module_identier; ?>-color <?php echo 'border-'.$module_identier; ?>" role="alert" style="display:none;">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="filters">
										<div class="col-md-12 col-sm-12 col-xs-12" style="margin:0">
											<div class="row">
												<?php
                                                if (!empty($contract_statuses)) { ?>
													<div class="col-md-6 col-sm-6 col-xs-12">
														<div class="row">
															<h5 class="text-bold text-auto">Contract Statuses</h5>
															<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 no_left_padding">
																<label>
																	<input type="checkbox" id="statuses_check-all" value="all" > <span></span> All
																</label>
															</div>
														</div>
														<div class="row">
														<?php
                                                        foreach ($contract_statuses as $key =>$value) { ?>
															<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 no_left_padding">
																<label>
																	<input type="checkbox" class="contract-statuses" name="contract_statuses[]" value="<?php echo $value->status_id; ?>" > <span></span> <?php echo ucwords($value->status_name); ?>
																</label>
															</div>
														<?php
                                                        } ?>
														</div>
													</div>
												<?php
                                                }
                                                if (!empty($contract_types)) { ?>
													<div class="col-md-6 col-sm-6 col-xs-12">
														<div class="row">
															<h5 class="text-bold text-auto">Contract Types</h6>
															<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 no_left_padding">
																<label>
																	<input type="checkbox" id="types_check-all" value="all"> <span></span> All
																</label>
															</div>
														</div>
														<div class="row">
														<?php
                                                        foreach ($contract_types as $key =>$value) { ?>
															<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 no_left_padding">
																<label>
																	<input type="checkbox" class="contract-types" name="contract_types[]" value="<?php echo $value->type_id; ?>" > <span></span> <?php echo ucwords($value->type_name); ?>
																</label>
															</div>
														<?php
                                                        } ?>
														</div>
													</div>
												<?php
                                                } ?>
											</div>
										</div>
										<!-- Clear Filter -->
										<?php $this->load->view('webapp/_partials/clear_filters.php') ?>
									</div>
								</div>
								<div class="clearfix"></div>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>

				
				<div class="row alert alert-ssid records-bar" role="alert">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="row">
							<div class="rows">
								<div class="row profile_view">
									<div class="row col-sm-12">
										<div class="right col-xs-12">
											<?php
                                            if (!empty($contract_data)) { ?>
												<table class="table sortable" id="">
													<thead>
														<tr>
															<th>Contract Name</th>
															<th>Contract Reference</th>
															<th>Contract Type</th>
															<th>Contract Status</th>
															<th>Contract Lead Name</th>
															<th>Contract Start Date</th>
															<th>Contract End Date</th>
															<th>Created On</th>
														</tr>
													</thead>
													<tbody id="table-results">
													</tbody>
												</table>
											<?php
                                            } else { ?>
												<table class="table sortable" id="">
													<thead>
														<tr>
															<th>Contract Name</th>
															<th>Contract Reference</th>
															<th>Contract Type</th>
															<th>Contract Status</th>
															<th>Contract Lead Name</th>
															<th>Contract Start Date</th>
															<th>Contract End Date</th>
															<th>Created On</th>
														</tr>
													</thead>
													<tbody id="table-results">
													</tbody>
												</table>
											<?php
                                            } ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>

			
		</div>
	</div>
</div>

<script>
	$( document ).ready( function(){
		<?php
        if (!empty($feedback)) { ?>
			swal({
				type: 'info',
				title: "<?php echo $feedback; ?>",
				showConfirmButton: false,
				timer: 3000
			})
		<?php
        } ?>

		var search_str   			= null;
		var contract_statuses_arr	= [];
		var contract_types_arr		= [];
		var start_index	 			= 0;

		$( '.contract-statuses' ).each( function(){
			if( $( this ).is( ':checked' ) ){
				contract_statuses_arr.push( $( this ).val() );
			}
		});

		$( '.contract-types' ).each( function(){
			if( $( this ).is( ':checked' ) ){
				contract_types_arr.push( $( this ).val() );
			}
		});

		load_data( search_str, contract_statuses_arr, contract_types_arr, start_index );

		// Do Search when filters are changed
		$( '.contract-statuses' ).change( function(){
			var search_str  = $( '#search_term' ).val();
			var contract_statuses_arr = [];
			$( '.contract-statuses' ).each( function(){
				if( $( this ).is( ':checked' ) ){
					contract_statuses_arr.push( $( this ).val() );
				}
			});
			load_data( search_str, contract_statuses_arr, contract_types_arr, start_index );
		});


		// Do Search when filters are changed
		$( '.contract-types' ).change( function(){
			var search_str  = $( '#search_term' ).val();
			var contract_types_arr = [];
			$( '.contract-types' ).each( function(){
				if( $( this ).is( ':checked' ) ){
					contract_types_arr.push( $( this ).val() );
				}
			});
			load_data( search_str, contract_statuses_arr, contract_types_arr, start_index );
		});


		// Do search when All is selected
		$( '#statuses_check-all' ).change( function(){
			var search_str  = $( '#search_term' ).val();
			if( $( this ).is( ':checked' ) ){
				$('.contract-statuses').each( function(){
					$( this ).prop( 'checked', true );
					contract_statuses_arr.push( $( this ).val() );
				});
			} else {
				$( '.contract-statuses' ).each(function(){
					$( this ).prop( 'checked', false );
				});
				contract_statuses_arr = [];
			}
			load_data( search_str, contract_statuses_arr, contract_types_arr, start_index );
		});

		// Do search when All is selected
		$( '#types_check-all' ).change( function(){
			var search_str  = $( '#search_term' ).val();
			if( $( this ).is( ':checked' ) ){
				$('.contract-types').each( function(){
					$( this ).prop( 'checked', true );
					contract_types_arr.push( $( this ).val() );
				});
			} else {
				$( '.contract-types' ).each(function(){
					$( this ).prop( 'checked', false );
				});
				contract_types_arr = [];
			}
			load_data( search_str, contract_statuses_arr, contract_types_arr, start_index );
		});

		//Do search when GO! is clicked
		$( '#search_term' ).on( 'input', function(){
			var search_str  = $( '#search_term' ).val();
			load_data( search_str, contract_statuses_arr, contract_types_arr, start_index );
		});


		// Pagination links
		$( "#table-results" ).on( "click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $(this).find( 'a' ).data( 'ciPaginationPage' );
			load_data( search_str, contract_statuses_arr, contract_types_arr, start_index );
		});

		function load_data( search_str, contract_statuses_arr, contract_types_arr, start_index ){
			$.ajax({
				url:"<?php echo base_url('webapp/contract/lookup'); ?>",
				method:"POST",
				data:{ search_term:search_str, contract_statuses:contract_statuses_arr, contract_types:contract_types_arr, start_index:start_index },
				success:function( data ){
					$( '#table-results').html( data );
				}
			});
		}

	});
</script>