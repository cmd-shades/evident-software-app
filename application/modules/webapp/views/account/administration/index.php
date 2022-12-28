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
</style>

<div class="row">
	<div class="x_panel no-border">
		<div class="row">
			<div class="x_content">
				<!-- Module statistics and info -->
				<div class="module-statistics table-responsive alert alert-ssid alert-results" role="alert" style="overflow-y: hidden;display:block" >
					<!-- <legend>Account Manager <span class="pull-right" style="font-size: 14px">Today's stats <span class="<?php echo $module_identier; ?>" ><?php echo date( 'd-m-Y' ); ?></span></span></legend> -->
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="col-md-3 col-sm-3 col-xs-12" style="margin:0">
							<div class="row">
								<h5 class="text-bold text-center" >Active</h5>
								<h3 class="text-center"><?php echo ( !empty( $account_stats->TotalAccounts ) && ( !empty( $account_stats->Active ) ) ) ? ( number_format( ( $account_stats->Active / $account_stats->TotalAccounts )*100, 1 ) + 0 ).'%' : '0'; ?></h3>
							</div>
						</div>
						<div class="col-md-3 col-sm-3 col-xs-12" style="margin:0">
							<div class="row">
								<h5 class="text-bold text-center" >Closed</h5>
								<h3 class="text-center"><?php echo ( !empty( $account_stats->TotalAccounts ) && ( !empty( $account_stats->Closed ) ) ) ? ( number_format( ( $account_stats->Closed / $account_stats->TotalAccounts )*100, 1 ) + 0 ).'%' : '0'; ?></h3>
							</div>
						</div>
						<div class="col-md-3 col-sm-3 col-xs-12" style="margin:0">
							<div class="row">
								<h5 class="text-bold text-center" >Suspended </h5>
								<h3 class="text-center"><?php echo ( !empty( $account_stats->TotalAccounts ) && ( !empty( $account_stats->Suspended ) ) ) ? ( number_format( ( $account_stats->Suspended / $account_stats->TotalAccounts )*100, 1 ) + 0 ).'%' : '0'; ?></h3>
							</div>
						</div>
						<div class="col-md-3 col-sm-3 col-xs-12" style="margin:0">
							<div class="row">
								<h5 class="text-bold text-center" >Trial</h5>
								<h3 class="text-center"><?php echo ( !empty( $account_stats->TotalAccounts ) && ( !empty( $account_stats->Trial ) ) ) ? ( number_format( ( $account_stats->Trial / $account_stats->TotalAccounts )*100, 1 ) + 0 ).'%' : '0'; ?></h3>
							</div>
						</div>
						
					</div>
					<div class="clearfix"></div>
				</div>
				
				<!-- Filter toggle + search bar -->			
				<div class="row">
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 hide">
						<div class="row">
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
								<a href="<?php echo base_url('/webapp/account/create' ); ?>" class="btn btn-block btn-success success-shadow" title="Click to add a New EviDoc name"><i class="fas fa-plus-circle" title="" style="font-size: 18px;"></i></a><br/>
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
						<?php $this->load->view( 'webapp/_partials/search_bar' ); ?>
					</div>
				</div>

				<div id="filters-container" class="table-responsive filters-container <?php echo $module_identier; ?>-color <?php echo 'border-'.$module_identier; ?>" role="alert" style="overflow-y: hidden; display:none" >
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="filters">
							<div class="col-md-6 col-sm-6 col-xs-12" style="margin:0">
								<div class="row">
									<h5 class="text-bold text-auto">Statuses</h5>
									<div class="row">
										<div class="col-md-6 col-sm-6 col-xs-6">
											<span class="checkbox" style="margin:0">
												<label><input type="checkbox" id="check-all-statuses" value="all" > All</label>
											</span>
										</div>
										<?php if( !empty($account_statuses) ) { foreach( $account_statuses as $k =>$account_status ){ ?>
											<div class="col-md-6 col-sm-6 col-xs-6">
												<span class="checkbox" style="margin:0">
													<label><input type="checkbox" class="account-statuses" name="account_statuses[]" value="<?php echo !empty( $account_status->account_status ) ? $account_status->account_status : ''; ?>" > <?php echo !empty( $account_status->account_status ) ? ucwords( $account_status->account_status ) : ''; ?></label>
												</span>
											</div>
										<?php } } ?>							
									</div>							
								</div>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="clearfix"></div>
				<div class="table-responsive alert alert-ssid alert-results" role="alert" style="overflow-y: hidden;" >
					<table id="datatable" class="table table-responsive" style="margin-bottom:0px;" >
						<thead>
							<tr>
								<th width="10%">Account ID</th>
								<th width="25%">Account Name</th>
								<th width="25%">Account Holder</th>
								<th width="25%">Membership Number</th>
								<th width="15%">Status</th>
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
		var account_statuses_arr= [];
		var start_index	 		= 0;
		var where = { 	
			'account_status'	:account_statuses_arr
		};
		
		//Load default brag-statuses
		$('.account-statuses').each(function(){
			if( $(this).is(':checked') ){
				where.account_status = account_statuses_arr.push( $(this).val() );
			}
		});
		
		load_data( search_str, where, start_index );
		
		//Do Search when filters are changed
		$('.account-statuses').change(function(){
			where.account_status	 = get_statuses( '.account-statuses' );
			load_data( search_str, where, start_index );
		});
	
		//Do search when All is selected
		$('#check-all-statuses').change(function(){
			
			var search_str = $('#search_term').val();
			
			var identifier = $(this).attr('id');
			
			if( identifier == 'check-all-statuses' ){
				if( $(this).is(':checked') ){
					$('.account-statuses').each(function(){
						$(this).prop( 'checked', true );
					});
				}else{
					$('.account-statuses').each(function(){
						$(this).prop( 'checked', false );
					});
				}
				
				where.account_status  =  get_statuses( '.account-statuses' );
				
			}
			load_data( search_str, where, start_index );
		});

		//Pagination links
		$("#table-results").on("click", "li.page", function( event ){
			event.preventDefault();
			var start_index = $(this).find('a').data('ciPaginationPage');
			load_data( search_str, where );
		});
		
		function load_data( search_str, where, start_index ){
			$.ajax({
				url:"<?php echo base_url('webapp/account/lookup'); ?>",
				method:"POST",
				data:{ search_term:search_str, where:where, start_index:start_index },
				success:function(data){
					$('#table-results').html(data);
				}
			});
		}
		
		$('#search_term').keyup( function(){
			var search = encodeURIComponent( $(this).val() );
			if( search.length > 0 ){
				load_data( search , where );
			}else{
				load_data( search_str, where );
			}
		});
		
		function get_statuses( identifier ){

			var chkCount  = 0;
			var totalChekd= 0;
			var unChekd   = 0;
			
			var idClass	  = '';
			
			if( identifier == '.account-statuses' ){
				
				account_statuses_arr  = [];
				
				$( identifier ).each(function(){
					chkCount++;
					if( $(this).is(':checked') ){
						totalChekd++;
						account_statuses_arr.push( $(this).val() );
					}else{
						unChekd++;
					}
				});
				
				if( chkCount > 0 && ( chkCount == totalChekd ) ){
					$( '#check-all-statuses' ).prop( 'checked', true );
				}else{
					$( '#check-all-statuses' ).prop( 'checked', false );
				}
				
				return account_statuses_arr;
				
			}

		}
	});
</script>

