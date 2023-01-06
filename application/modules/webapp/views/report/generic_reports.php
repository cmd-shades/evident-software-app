<style type="text/css">
	body {
		background-color: #FFFFFF;
	}
	.table > thead > tr > th {
		cursor:pointer;
	}
</style>

<div class="row">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<br/>
			<div class="x_panel has-shadow" style="overflow:auto; top: 0; left:0; right:0; bottom:0">
				<div class="x_content">
					<legend>System Reports</legend>
					<div class="row">
						<div class="col-md-8 col-sm-12 col-xs-12">
							<span id="feedback_msg" class="text-red" ><?php echo ( !empty( $feedback_msg ) ) ? $feedback_msg : ''; ?></span>
							<form id="fetch-report-form" class="form-horizontal" action="<?php echo base_url('/webapp/report/reports/' ); ?>" method="post" >
								<input type="hidden" name="account_id" value="<?php echo $this->account_id; ?>" />
								<input type="hidden" name="report_type" value="" />
								<h5 class="text-bold">Please select a Contract</h5>
								<div class="row">
									<div class="col-md-6 col-sm-6 col-xs-12">
										<select id="contract_id" name="contract_id" class="form-control" style="width:100%; display:none; margin-bottom:10px;" >
											<option value="">All (no filter)</option>
											<?php if( !empty( $contracts ) ){ foreach( $contracts as $key => $contract ) { ?>
												<option value="<?php echo $contract->contract_id; ?>"><?php echo $contract->contract_name; ?></option>
											<?php } } ?>
										</select>
										</br/>
									</div>
								</div>
								
								<div class="clearfix"></div>
								<?php if( !empty( $setup_data ) ) { ?>
									<h5 class="text-bold">Please select the report type you require</h5>
									<div class="row">
										<div class="col-md-12 col-sm-12 col-xs-12">
											<?php foreach( $setup_data as $report_type=>$report_setup ){ ?>
												<div class="row col-md-4 col-sm-6 col-xs-12 checkbox inline-block" >
													<label><input type="checkbox" class="report-type" data-is_fixed="<?php echo ( $report_setup->is_fixed ) ? true : false; ?>" name="report[<?php echo $report_type; ?>]" id="report-<?php echo $report_type; ?>" value="<?php echo $report_type; ?>" <?php echo ( !empty( $chked_report_type ) && ( $report_type == $chked_report_type ) ) ? 'checked=checked' : ''; ?> > <?php echo $report_setup->report_type; ?></label>
												</div>
											<?php } ?>
											<div class="clearfix"></div>
										</div>
									</div>
									<div class="clearfix"></div>
									<div>
										<?php foreach( $setup_data as $report_type=>$report_setup ){ ?>
											<div class="report-container report-<?php echo $report_type; ?>" style="display:<?php echo ( !empty( $chked_report_type ) && ( $report_type == $chked_report_type ) ) ? 'block' : 'none'; ?>" >
												<?php if( !empty( $report_setup->group_filters ) ){ ?>
													<hr/>
													<h5 class="text-bold">Select the Evidoc Group</h5>
													<div class="row" >
														<div class="row col-md-6 col-sm-6 col-xs-12">
															<select id="evidoc_group" name="evidoc_group" class="form-control" style="width:100%; margin-bottom:10px;" >
																<?php foreach( $report_setup->group_filters as $group => $group_name ) { ?>
																	<option value="<?php echo strtolower( $group ); ?>"><?php echo ucwords( $group_name ); ?></option>
																<?php } ?>
															</select>
														</div>
													</div>
													<div class="clearfix"></div>
												<?php } ?>
												
												<?php if( $report_setup->table_cols ){ ?>
													<hr/>
													<h5 class="text-bold">Select your Report Headers</h5>
													<div class="row" >
														<div class="row col-md-12 col-sm-12 col-xs-12">
															<div class="col-md-3 col-sm-4 col-xs-12 checkbox inline-block" >
																<label><input type="checkbox" class="check-all <?php echo ( $report_setup->is_fixed ) ? 'is_fixed' : ''; ?>" id="column-header-<?php echo $report_type; ?>" value="all" > All</label>
															</div>
															<?php foreach( $report_setup->table_cols as $column=>$column_header ){ ?>
																<div class="col-md-3 col-sm-4 col-xs-12 checkbox inline-block" >
																	<label><input type="checkbox"  data-group_class="column-header-<?php echo $report_type; ?>" class="columns column-header-<?php echo $report_type; ?> column-<?php echo $column; ?> <?php echo ( $report_setup->is_fixed ) ? 'is_fixed' : ''; ?>" name="report[<?php echo $report_type; ?>][columns][]" id="column-<?php echo $column; ?>" value="<?php echo $column; ?>" > <?php echo $column_header; ?></label>
																</div>
															<?php } ?>
														</div>
													</div>
												<?php } ?>
												
												<?php if( !empty( $report_setup->status_filters ) ){ ?>
													<hr/>
													<h5 class="text-bold">Filter by Statuses</h5>
													<div class="row" >
														<div class="row col-md-12 col-sm-12 col-xs-12">
															<div class="col-md-3 col-sm-4 col-xs-12 checkbox" >
																<label><input type="checkbox" class="check-all" id="status-filter-<?php echo $report_type; ?>" value="all" > All</label>
															</div>
															<?php foreach( $report_setup->status_filters as $status=>$status_name ){ ?>
																<div class="col-md-3 col-sm-4 col-xs-12 checkbox inline-block" >
																	<label><input type="checkbox" data-group_class="status-filter-<?php echo $report_type; ?>" class="columns status-filter-<?php echo $report_type; ?> status-<?php echo $status; ?>" name="report[<?php echo $report_type; ?>][statuses][]" id="status-<?php echo $status; ?>" value="<?php echo $status; ?>" > <?php echo $status_name; ?></label>
																</div>
															<?php } ?>
														</div>
													</div>
												<?php } ?>
												<div class="clearfix"></div>
												<?php if( !empty( $report_setup->date_filters ) ){ ?>
													<!-- <div class="date-range-filters <?php echo ( $report_setup->is_fixed ) ? '' : 'hide'; ?>" > -->
													<div class="date-range-filters <?php echo ( in_array( $report_type, [ 'job', 'job_invoice_report' ] ) ) ? '' : 'hide'; ?>" >
														<hr/>
														<h5 class="text-bold">Date Range Filters</h5>
														<div class="row" >
															<div class="col-md-12 col-sm-12 col-xs-12">
																<br/>
																<?php foreach( $report_setup->date_filters as $date_field=>$field_name ){ ?>
																	<label><?php echo $field_name; ?></label>
																	<div class="row">
																		<div class="col-md-5 col-sm-6 col-xs-12" >
																			<div class="form-group">
																				<input name="report[<?php echo $report_type; ?>][dates][<?php echo str_replace( '.', '-', $date_field ); ?>][date_from]" value="" class="form-control datepicker" type="text" placeholder="Date from"  />
																			</div>
																		</div>
																		<div class="col-md-5 col-sm-6 col-xs-12" >
																			<div class="form-group">
																				<input name="report[<?php echo $report_type; ?>][dates][<?php echo str_replace( '.', '-', $date_field ); ?>][date_to]" value="" class="form-control datepicker" type="text" placeholder="Date to"  />
																			</div>
																		</div>																
																	</div>
																<?php } ?>
															</div>
														</div>
													</div>
												<?php } ?>
												<div class="clearfix"></div>
												<hr/>
												<button id="fetch-report-button" class="btn btn-info btn-sm" >Fetch Report Data</button>
											</div>
										<?php } ?>
									</div>
									
								<?php }else{ ?>
									<div>
										<span>Report setup data is not avaiable at the moment. Please try again later</span>
									</div>								
								<?php } ?>
								<div class="clearfix"></div>
								<div class="hide">

									<div class="checkbox">
										<label><input name="report[<?php echo $report_type; ?>]statuses[]" class="flat" type="checkbox" value="All"> All</label><br/>
										<label><input name="report[<?php echo $report_type; ?>]statuses[]" class="flat" type="checkbox" value="Ok"> OK</label><br/>
										<label><input name="report[<?php echo $report_type; ?>]statuses[]" class="flat" type="checkbox" value="Faulty"> Faulty</label><br/>
									</div>
									<!-- <div class="radio">
										<h4>Prefered Delivery</h4>
										<label><input type="radio" checked="" value="option1" id="view" name="optionsRadios"> View</label><br/>
										<label><input type="radio" value="option2" id="download" name="optionsRadios"> Download (csv)</label><br/>
										<label><input type="radio" value="option3" id="emailoption" name="optionsRadios"> Email me</label><br/>
									</div> -->
									
									<div class="email-report-to" style="display:none">
										<br/>
										<div class="form-group">
											<label class="control-label col-md-12 col-sm-12 col-xs-12">Email address</label>
											<div class="row col-md-6 col-sm-6 col-xs-12">
												<br/>
												<input type="text" name="email_to" value="" class="form-control" placeholder="Email report to...">
											</div>
										</div>							
									</div>
								</div>
								
							</form>
						</div>			
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready( function(){
		
		$("#feedback_msg").delay(4000).fadeOut(1500);
		
		$( '#contract_id' ).select2({
			/* allowClear: true */
		});
		
		$('.is_fixed').click( function( e ) {
			// e.preventDefault();
			// swal({
				// type: 'warning',
				// text: 'Fixed Report',
				// text: 'This Is a Fixed Report Type, you can not change the fields',
			// });
			// return false;
		} );
		
		$('.report-type').click( function() {
			
			$('.report-type').not(this).prop('checked', false);
			
			var reportType 		= $(this).val(),
				isFixedReport 	= $(this).data( 'is_fixed' ),
				groupClass		= "column-header-"+reportType;
			
			$('[name="report_type"]').val( reportType );
			
			$( '#'+groupClass ).prop( 'checked', false );
			$('.report-container').not(this).hide();
			$('.report-'+reportType ).show();

			$( '.columns, .check-all' ).each(function(){
				var columnType = $( this ).hasClass( 'is_fixed' );
				if( columnType ){
					$(this).prop( 'checked', true );
					$( '#'+groupClass ).prop( 'checked', true );
				} else {
					$(this).prop( 'checked', true );
					$( '#'+groupClass ).prop( 'checked', false );
				}
			});
		});
		
		$( '.check-all' ).change(function(){
			var grpId = $(this).attr( 'id' );
			if( $(this).is(':checked') ){
				$( '.'+grpId ).each(function(){
					$(this).prop( 'checked', true );
				});				
			}else{
				$( '.'+grpId ).each(function(){
					$(this).prop( 'checked', false );
				});
			}
		});
		
		$('.columns').change(function(){
			
			var groupClass  = $(this).data( 'group_class' );
			var chkCount 	= 0;
			var totalChekd 	= 0;
			var unChekd		= 0;
			
			$( '.'+groupClass ).each(function(){
				chkCount++;
				if( $(this).is(':checked') ){
					totalChekd++;					
				}else{
					unChekd++;
				}
			});

			if( chkCount > 0 && ( chkCount == totalChekd ) ){
				$( '#'+groupClass ).prop( 'checked', true );
			}else{
				$( '#'+groupClass ).prop( 'checked', false );
			};
		});
		
	});
</script>