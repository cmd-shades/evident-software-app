<div class="row">
	
	<div id="system_compliance_data" ></div>

	<!-- <div class="x_content">
		
		<div class="col-md-12">
			<div class="x_title no-border text-center">
				<h2><small>FIRE DETECTION 1</small></h2>
				<div class="clearfix"></div>
			</div>
			<div class="evi_panel has-shadow" >
				
			</div>
		</div>
	</div>

	<div class="x_content">
		<div class="col-md-12">
			<div class="x_title no-border text-center">
				<h2><small>FIRE DETECTION 2</small></h2>
				<div class="clearfix"></div>
			</div>
			<div class="evi_panel has-shadow" >
				
			</div>
		</div>
	</div> -->
</div>


<script type="text/javascript">

	$( document ).ready( function () {

		/*-- GET SITE COMPLIANCE STATS --*/
		$.ajax({
			url:"<?php echo base_url( 'webapp/home/buildings_stats' ); ?>",
			method:"POST",
			data:{ account_id:accountID,stat_type:"system_compliance" },
			dataType: 'json',
			success:function( result ){
				if( result.status == 1 ){
					
					if ( result.hasOwnProperty( "stats" ) ) { 
					
						var systemDataStats = result.stats;
						
						$.each( systemDataStats, function( index, systemData ) {
							
							var statsData 		= ( systemData.system_stats ) 		? systemData.system_stats 		: undefined;
							var nonCompData 	= ( systemData.non_compliant_data ) ? systemData.non_compliant_data : undefined;
							var systemName 		= ( systemData.system_name ) 		? systemData.system_name 		: undefined;

							var systemsData = '<div class="x_content">';
									systemsData += '<div class="col-md-12">';
										systemsData += '<div class="x_title no-border text-center">';
											systemsData += '<h2><small>'+systemData.system_name+'</small></h2>';
											systemsData += '<div class="clearfix"></div>';
										systemsData += '</div>';
										
										systemsData += '<div class="evi_panel has-shadow" >';
										
											//if( statsData.stats && statsData.stats !== undefined ){
											if( statsData !== undefined ){

												systemsData += '<div class="x_content" >';
													systemsData += '<div class="row" >';
														systemsData += '<div class="col-md-6 col-sm-6 col-xs-12">';
															systemsData += '<div class="row">';
																
																systemsData += '<div class="col-md-5 col-sm-5 col-xs-12">';
																	systemsData += '<h4 class="text-bold"><strong>&nbsp;</strong></h4>';
																	systemsData += '<canvas id="'+systemData.system_group+'siteComplianceLeft"></canvas>';
																	systemsData += '<div class="stat-error"></div>';
																systemsData += '</div>';
																
																systemsData += '<div class="col-md-7 col-sm-7 col-xs-12">';
																	systemsData += '<h4 class="text-bold"><strong>All Data Summary</strong></h4>';
																	systemsData += '<div id="'+systemData.system_group+'siteComplianceBarsLeft" class="data-bars"></div>';
																	systemsData += '<div class="stat-error"></div>';
																systemsData += '</div>';
																
															systemsData += '</div>';
														systemsData += '</div>';
														
														systemsData += '<div class="col-md-6 col-sm-6 col-xs-12">';
															systemsData += '<div class="row">';
																
																systemsData += '<div class="col-md-5 col-sm-5 col-xs-12">';
																	systemsData += '<h4 class="text-bold"><strong>&nbsp;</strong></h4>';
																	systemsData += '<canvas id="'+systemData.system_group+'siteComplianceRight"></canvas>';
																	systemsData += '<div class="stat-error"></div>';
																systemsData += '</div>';
																
																systemsData += '<div class="col-md-7 col-sm-7 col-xs-12">';
																	systemsData += '<h4 class="text-bold"><strong>Non Compliant Summary</strong></h4>';
																	systemsData += '<div id="'+systemData.system_group+'siteComplianceBarsRight" class="data-bars"></div>';
																	systemsData += '<div class="stat-error"></div>';
																systemsData += '</div>';
																
															systemsData += '</div>';
														systemsData += '</div>';

													systemsData += '</div>';
												systemsData += '</div>';

											} else {
												systemsData += '<div class="x_content" >';
													systemsData += '<h2><small>System not Installed in any Building</small></h2>';
												systemsData += '</div>';
											}
										
										systemsData += '</div>';
									systemsData += '</div>';
								systemsData += '</div>';

							$( '#system_compliance_data' ).append( systemsData );

							//if( statsData.stats && statsData.stats !== undefined ){
							if( statsData !== undefined ){
								setChart( statsData, 'SiteCompliance', systemData );
							}
							
							if( nonCompData !== undefined ){
								
								var grandTotal = 0;
								$.each( nonCompData, function( index, value ) {
									var grpTotal		= ( value.totals.grand_total ) ? value.totals.grand_total : 0;
									grandTotal			= parseInt( grandTotal) + parseInt( grpTotal );
								});
								
								$.each( nonCompData, function( periodRange, periodicData ) {
								
									var groupTotal		= ( periodicData.totals.grand_total ) ? periodicData.totals.grand_total : 0;
									var clickFilterUrl	= "<?php echo base_url( '/webapp/site/non_compliant_buildings' ); ?>";
									var statusPercentage = ( groupTotal > 0 ) ? ( parseFloat( ( parseInt( groupTotal ) / parseInt( grandTotal ) )*100 ).toFixed( 2 ) ) : 0;

									//if( groupTotal > 0 ){
										var nonCompliantDiv = '<a href="'+clickFilterUrl+'?system_id='+systemData.system_id+'&range_index='+periodicData.range_index+'"><div class="row" ><div class="col-md-10 box-header status-bar shadow-'+periodicData.group_color+'" style="background-color:'+periodicData.hex_color+'; opacity:0.9;color:#fff"><h3 class="status-bar-header">';
											nonCompliantDiv = nonCompliantDiv+( periodicData.group_range )+' <span class="pull-right">'+groupTotal+'</span>';
											nonCompliantDiv = nonCompliantDiv+'</h3></div><div class="col-md-2"> <h4 class="text-bold" style="color:'+periodicData.hex_color+'"><strong>'+statusPercentage+'%</strong></h4></div> </div></a>';

										$( '#'+systemData.system_group+'siteComplianceBarsRight' ).append( nonCompliantDiv );
									//}
								});
								
							}
							
						});
					
					}
					
					
				}else{
					console.log( 'No Data Received!' );
					
				}
			}
		});

	});
	
</script>