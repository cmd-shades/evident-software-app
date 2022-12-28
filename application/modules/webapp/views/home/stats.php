<!DOCTYPE html>
<title>Example</title>

<!-- Styles -->
<style>
	.moving-banner {
		height: 50px;
		overflow: hidden;
		position: relative;
	}

	.moving-banner h3, .moving-banner table {
		/*	 font-size: 3em; */
		color: blue;
		position: absolute;
		/* width: 100%;
		height: 100%; */
		margin: 0;
		/* line-height: 50px; */
		text-align: center;
		/* Starting position */
		-moz-transform:translateX(100%);
		-webkit-transform:translateX(100%);
		transform:translateX(100%);
		/* Apply animation to this element */
		-moz-animation: moving-banner 20s linear infinite;
		-webkit-animation: moving-banner 20s linear infinite;
		animation: moving-banner 20s linear infinite;
	}
	/* Move it (define the animation) */
	@-moz-keyframes moving-banner {
		0%   { -moz-transform: translateX(100%); }
		100% { -moz-transform: translateX(-100%); }
	}
	@-webkit-keyframes moving-banner {
		0%   { -webkit-transform: translateX(100%); }
		100% { -webkit-transform: translateX(-100%); }
	}
	@keyframes moving-banner {
		0%   {
			-moz-transform: translateX(100%); /* Firefox bug fix */
			-webkit-transform: translateX(100%); /* Firefox bug fix */
			transform: translateX(100%);
		}
		100% {
			-moz-transform: translateX(-100%); /* Firefox bug fix */
			-webkit-transform: translateX(-100%); /* Firefox bug fix */
			transform: translateX(-100%);
		}
	}

	.moving-banner:hover, .moving-banner:focus {
		animation-play-state: paused;
		-webkit-animation-play-state: paused;
	}
</style>

<style>
	.data-bars{
		/*margin-top:15px;*/
	}

	.x_title h1, .x_title h2, .x_title h3, .x_title h4, .x_title h5, .x_title h6 {
		float:none;
		color: #0092CD;
		text-transform: uppercase;
	}

	.x_title{
		margin-bottom: 0;
		background: #0092CD;
		background-image: linear-gradient(-180deg, #0092cd, #029ddb);
		color: #fff;
	}

	.x_title h2 > small{
		color: #fff;
		font-weight: 0;
		letter-spacing: 3px;
	}

	.x_panel {
		min-height:550px;
		margin-bottom:20px;
		padding-top: 35px;
	}

    .stat-error {
        text-align: center;
        color: #bfbfbf;
    }

    .x_title h2 {
        width: 100% !important;
    }

	.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
		vertical-align: middle;
	}

	.default-anchor{

	}

</style>

<script type="text/javascript">
	var accountID = "<?php echo $this->user->account_id; ?>";
</script>

<div class="row" style="margin-left:-15px; margin-right:-15px;">
	<div class="x_content">
		<div class="col-md-12 col-sm-12 col-xs-12 module-header">
			<h3><strong>Stats</strong><span style="font-size: 12px;display: inline; float: right;color: black;"><a href="<?php echo base_url( "webapp/home/index?dashboard2=2" ); ?>">Dashboard 2</a></span></h3>
		</div>
	</div>
</div>
<div class="rows" style="margin-left:-15px; margin-right:-15px;">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12 module-header">
			<div id="moving-banner">
				<table class="table floating-bar" >
					<tr class="bg-light-red" >
						<td width="25%" class="bg-red" style="padding-left:20px;" >Asset Tagging Summary</td>
						<td width="25%" >
							<div class="col-md-2" ></div>
							<div class="col-md-2" ><img style="width:99%" src="<?php echo base_url( '/assets/images/dashboard-icons/assets-48x48.png' ) ?>" /></div>
							<div class="col-md-8" >
								<a href="<?php echo base_url( 'webapp/asset/assets' ) ?>" class="default-anchor" >
									<div id="totalAssets" >0</div>
									<div>Total Assets</div>
								<a>
							</div>
						</td>
						<td width="25%" >
							<div class="col-md-2" ></div>
							<div class="col-md-2" ><img style="width:99%" src="<?php echo base_url( '/assets/images/dashboard-icons/buildings-48x48.png' ) ?>" /></div>
							<div class="col-md-8" >
								<a href="<?php echo base_url( 'webapp/site/sites' ) ?>" class="default-anchor" >
									<div id="totalBuildings" >0</div>
									<div>Total Buildings</div>
								</a>
							</div>
						</td>
						<td width="25%" >
							<div class="col-md-2" ></div>
							<div class="col-md-2" ><img style="width:99%" src="<?php echo base_url( '/assets/images/dashboard-icons/flats-48x48.png' ) ?>" /></div>
							<div class="col-md-8" >
								<div id="totalFlats" >0</div>
								<div>Total Flats</div>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>


<div class="col-md-6 col-sm-6 col-xs-12 hide">
	<div class="row">
		<div class="col-md-7 col-sm-7 col-xs-12">
			<h4 class="text-bold"><strong>Non Compliant Summary</strong></h4>
			<div id="firealarmpanelsiteComplianceBarsRight" class="data-bars">
				<a href="http://localhost/evident-tess/webapp/site/non_compliant_buildings?system_id=46&amp;range_index=1">
					<div class="row">
						<div class="col-md-10 box-header status-bar shadow-orange" style="background-color:orange; opacity:0.9;color:#fff">
							<h3 class="status-bar-header">0-3 Months overdue <span class="pull-right">13</span></h3>
						</div>
						<div class="col-md-2">
							<h4 class="text-bold" style="color:orange"><strong>100.00%</strong></h4>
						</div>
					</div>
				</a>
				<a href="http://localhost/evident-tess/webapp/site/non_compliant_buildings?system_id=46&amp;range_index=2">
					<div class="row">
						<div class="col-md-10 box-header status-bar shadow-red" style="background-color:#FC5B5B; opacity:0.9;color:#fff">
							<h3 class="status-bar-header">3-6 Months overdue <span class="pull-right">0</span>
							</h3>
						</div>
						<div class="col-md-2"> <h4 class="text-bold" style="color:#FC5B5B"><strong>0%</strong></h4></div>
					</div>
				</a>
				<a href="http://localhost/evident-tess/webapp/site/non_compliant_buildings?system_id=46&amp;range_index=3">
					<div class="row"><div class="col-md-10 box-header status-bar shadow-dark-red" style="background-color:#C03636; opacity:0.9;color:#fff"><h3 class="status-bar-header">Over 6 Months overdue <span class="pull-right">0</span></h3></div><div class="col-md-2"> <h4 class="text-bold" style="color:#C03636"><strong>0%</strong></h4></div>
					</div>
				</a>
			</div>
			<div class="stat-error">
			</div>
		</div>
	</div>
</div>


<div class="col-md-4 col-sm-4 col-xs-12 hide">
	<div class="x_title no-border text-center">
		<h2><small>Checklist Counter</small></h2>
		<div class="clearfix"></div>
	</div>
	<div class="evi_panel has-shadow">
		<div class="x_content">
			<table class="table" style="margin-top:-6px">
				<thead>
					<tr>
						<td width="40%" class="text-center text-bold">Checklist Name</td>
						<td width="20%" class="text-center text-bold">Completed</td>
						<td width="20%" class="text-center text-bold">Pending</td>
						<!-- <td width="20%">In Progress</td> -->
						<td width="20%" class="text-center text-bold">Total</td>
					</tr>
				</thead>
				<tbody>
					<?php
					if( !empty( $checklist_counter ) ){
						foreach( $checklist_counter as $single_row ){ ?>
							<tr>
								<td class="text-center"><?php echo ( !empty( $single_row->checklist_name ) ) ? $single_row->checklist_name : '' ; ?></td>
								<td class="text-center"><?php echo ( !empty( $single_row->completed ) ) ? $single_row->completed : '' ; ?></td>
								<td class="text-center"><?php echo ( !empty( $single_row->pending ) ) ? $single_row->pending : '' ; ?></td>
								<!-- <td><?php echo $single_row->in_progress; ?></td> -->
								<td class="text-center"><?php echo ( !empty( $single_row->total ) ) ? $single_row->total : '' ; ?></td>
							</tr>
						<?php
						}
					} else { ?>
						<tr><td colspan="4">No data available</td></tr>
					<?php 
					} ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="dashaboard-tabs">
	<?php include( 'inc/dashboard_tabs.php' ); ?>
</div>
<div class="evidoc-exceptions-data">
	<?php #include( 'inc/evidoc_exceptions_data.php' ); ?>
</div>
<div class="jobs-data">
	<?php include( 'inc/jobs_data.php' ); ?>
</div>
<div class="periodic-schedules">
	<?php include( 'inc/periodic_schedules.php' ); ?>
</div>
<div class="periodic-schedules">
	<?php include( 'inc/compliance_data.php' ); ?>
</div>
<script>

	$( document ).ready(function () {
		/*--- GET ASSET TAGGING SUMMARY --*/
		$.ajax({
			url:"<?php echo base_url( 'webapp/home/asset_stats' ); ?>",
			method:"POST",
			data:{ account_id:accountID, stat_type:"tagging_summary" },
			dataType: 'json',
			success:function( result ){
				if( result.status == 1 ){
					setChart( result, 'assettaggingsummary' );
				}else{
					$( '#assetTaggingSummaryHeader' ).hide()
					$( '#assetTaggingSummary' ).hide()
					$( '#assetTaggingSummary' ).closest('.stats-container').find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )

				}
			}
		});
	});

	/*--- POPULATE CHART DATA ---*/
	function setChart( data, chartType = 'AssetStatus', systemData = false ){

		if( chartType.length > 0 ){

			switch( chartType.toLowerCase() ){
				/*--- EVIEXCEPTIONS STATUS----*/
                case 'eviexceptionstatus':
					break;

				/*--- ASSET COMPLIANCE ----*/
                case 'assetcompliance':
					break;

				/*--- EVIDOC RESULTS ----*/
                case 'evidocresults':
					break;

				/*--- SITE COMPLIANCE STATISTICS ----*/
				case 'sitecompliance':

					if( data && data !== undefined && ( data.stats.length > 0 ) ){
						var dataAvailable = false;
						var dataLabels 		= data.stats.map( e=>e.result_status ),
							dataValues 		= data.stats.map( e=>e.status_total ),
							dataBGColor		= data.stats.map( e=>e.result_status_colour ),
							dataHoverBGColor= data.stats.map( e=>e.result_status_colour );

						var dataTotals		= data.totals;//Data summary

						var clickFilterUrl	= "<?php echo base_url( '/webapp/site/audit_result_status' ); ?>";

						if( dataTotals.compliance_raw > 0 ){
							var configOptions	= { 'centerText':dataTotals.compliance, 'centerTextBottom':dataTotals.compliance_alt, 'centerTextColor': ( ( dataTotals.compliance_raw >= 90 ) ? '#6CD167' : '#FC5B5B' ) };
						}else{
							//This if for 0% percent compliance
							var configOptions	= { 'centerText':'0%', 'centerTextBottom':'Not compliant', 'centerTextColor': '#FC5B5B' };
						}

						//Unpack the stats into coloured divs
						$.each( data.stats, function( index, statusItem ) {

							//Only show bars for statuses that have totals over 0
							if( statusItem.status_total > 0 ){

								if( statusItem.result_status_alt != '' ){

									var statusPercentage = parseFloat( ( parseInt( statusItem.status_total ) / parseInt( dataTotals.grand_total ) )*100 ).toFixed( 2 );

									var statusBarDiv = '<a href="'+clickFilterUrl+'?system_id='+systemData.system_id+'&group='+statusItem.result_status_group+'"><div class="row" ><div class="col-md-10 box-header status-bar shadow-'+statusItem.hex_color+'" style="background-color:'+statusItem.result_status_colour+'; opacity:0.9;color:#fff"><h3 class="status-bar-header">';
										statusBarDiv = statusBarDiv+( statusItem.result_status_alt )+' <span class="pull-right">'+statusItem.status_total+'</span>';
										statusBarDiv = statusBarDiv+'</h3></div><div class="col-md-2"> <h4 class="text-bold" style="color:'+statusItem.result_status_colour+'"><strong>'+statusPercentage+'%</strong></h4></div> </div></a>';

									$( '#'+systemData.system_group+'siteComplianceBarsLeft' ).append( statusBarDiv );
									//$( '#'+systemGroup+'siteComplianceBarsRight' ).append( statusBarDiv );
									dataAvailable = true;
								}
							}

						});

						//Verify that there is some data before attempting to draw the chart
						if( dataAvailable ){
							var dataCofig     = chartDataConfig( dataLabels, dataValues, dataBGColor, 'Periodic Audits' ); //config the actual chart values
							var chartConfig   = doughnutConfig( dataCofig, configOptions ); //build the graph before writing to screen
							var $chartLeft	  = $( '#'+systemData.system_group+'siteComplianceLeft' );
							//var $chartRight	  = $( '#'+systemData.system_group+'siteComplianceRight' );
							var siteCompChart = new Chart( $chartLeft[ 0 ], chartConfig ); //Run the graph and display to screen*/
							//var siteCompChart = new Chart( $chartRight[ 0 ], chartConfig ); //Run the graph and display to screen*/

						}else{
							$( '#'+systemData.system_group+'siteCompliance' ).hide();
							$( '#'+systemData.system_group+'siteCompliance' ).parent().find( ".data-bars" ).hide();
							$( '#'+systemData.system_group+'siteCompliance' ).parent().find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )

						}
					} else {
						console.log( 'Something went wrong!' );
						return true;
					}

					break;

				/*---- PERIODIC AUDITS TRACKER ----*/
				case 'periodicauditss':

					break;

				/*--- AUDIT RESULT STATS ---*/
				case 'auditresults':

					break;

				/*--- ASSET STATUSES STATS ---*/
				case 'assetstatus':

					break;

				/*-- ASSET EOL STATS --*/
				case 'eolstats':

					break;

				/*--- JOB STATS ---*/
				case 'jobstats':

					break;

				/*-- ASSET TAGGING SUMMARY --*/
				case 'assettaggingsummary':
					var dataAssetTaggingSum	= data.stats.stats;

					//Unpack the Asset TAGGING Stats
					$.each( dataAssetTaggingSum, function( index, headerItem ) {
						if( parseInt( headerItem.column_value ) > 0 ){

							switch( headerItem.column_key ){
								case 'total_assets':
									$( '#totalAssets' ).text( headerItem.column_value );
									break;

								case 'total_buildings':
									$( '#totalBuildings' ).text( headerItem.column_value );
									break;

								case 'number_of_flats':
									$( '#totalFlats' ).text( headerItem.column_value );
									break;
							}
						}

					});

					break;
			}
		}
	}


	/*
	* CHart data config
	*/
	function chartDataConfig( dataLabels, dataValues, bgColor, lableText ){
		return result = {
			labels: dataLabels,
			datasets: [
				{
					label: lableText,
					backgroundColor: bgColor,
					borderWidth:0,
					hoverBackgroundColor: bgColor,
					data: dataValues
				}
			]
		};
	}

	/*
	* Configure a dough-nut Chart
	*/
	function doughnutConfig( dataCofig, configOptions ){
		return result = {
			type: 'doughnut',
			options: {
				aspectRatio: 2.25,
				scales: {
					xAxes: [{ display: false, categoryPercentage: 1.0, barPercentage: 1.0 }],
					yAxes: [{ ticks: { max: 100, min: 0, callback: function( value ){ return value + "%"; } }, display: false }],
				},
				legend: { display: false
				},
				tooltips: { enabled: true },
				cutoutPercentage: 80, //Set the thickness of the doughnut outter border

			},
			data: dataCofig,
			plugins: [{
				beforeDraw: function(chart) {
					var width 	= chart.chart.width,
						height 	= chart.chart.height,
						ctx 	= chart.chart.ctx;

					ctx.restore();
					var fontSize 	= ( height / 114).toFixed( 2 );
					ctx.font 		= fontSize + "em 'evi-font', sans-serif";
					ctx.textBaseline= "middle";
					ctx.fillStyle 	= ( configOptions.centerTextColor.length > 0 ) ? configOptions.centerTextColor : '#6CD167';

					//Set the bottom part of the center text
					var defaultWidth = 2;
					var defaultHeight= 2;

					if( configOptions.centerTextBottom.length > 0 ){
						var textBase = configOptions.centerTextBottom,
						textBaseX 	 = Math.round( ( width - ctx.measureText( textBase ).width ) / defaultWidth ),
						textBaseY 	 = height / 1.8;
						ctx.fillText( textBase, textBaseX, textBaseY );
						defaultHeight= 2.5;
					}

					var text = configOptions.centerText,
					textX = Math.round( ( width - ctx.measureText(text).width ) / defaultWidth ),
					textY = height / defaultHeight;
					ctx.fillText( text, textX, textY );

					ctx.save();
				}
			}]
		}
	}

</script>