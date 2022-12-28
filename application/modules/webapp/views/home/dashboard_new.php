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
</style>

<div class="row" style="">
	<div class="x_content">
		<div class="col-md-12 col-sm-12 col-xs-12 module-header">
			<h3><strong>Home</strong><span style="font-size: 12px;display: inline; float: right;color: black;"><a href="<?php echo base_url( "webapp/dashboard/index" ); ?>">Disciplines Dashboard</a></span></h3>
		</div>

		<!-- Assets Tagging Summary -->
        <div class="col-md-<?php echo ( in_array( $this->user->account_id, [15, 17] ) ) ? '3' : '4';  ?> col-sm-<?php echo ( in_array( $this->user->account_id, [15, 17] ) ) ? '3' : '4';  ?> col-xs-12">
			<div class="x_title no-border text-center">
				<h2 id="assetTaggingSummaryLabel"><small>Asset Tagging Summary</small></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_panel border-radius-10 has-shadow stats-container">
				<div class="x_content">
					<canvas id="assetTaggingSummary">Total tagged Assets</canvas>
                    <div class="stat-error"></div>
					<div id="AssetTaggingSummaryBars" class="data-bars" style="margin-top:0px;" ></div>
				</div>
			</div>
        </div>

		
		
		<!-- Asset Compliance -->
		<?php if( in_array( $this->user->account_id, [15, 17] ) ){ ?>
		   <div class="col-md-3 col-sm-6 col-xs-12 hide">
				<div class="x_title no-border text-center">
					<h2 id="exceptionStatusesLabel"><small>Asset Compliance</small></h2>
					<div class="clearfix"></div>
				</div>
				<div class="x_panel border-radius-10 has-shadow stats-container">
					<div class="x_content">
						<div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<!-- <canvas id="assetCompliance"></canvas> -->
								<canvas id="placeholder" style="display: inline-block;height: 150px;"></canvas>
							</div>
						</div>
						<div class="row">
							<div class="stat-error"></div>
							<div class="col-md-12 col-sm-12 col-xs-12">
								<div id="assetComplianceBars" class="data-bars"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>

		
		<!-- INSPECTION RESULTS -->
		<?php if( in_array( $this->user->account_id, [15, 17] ) ){ ?>
		<div class="col-md-3 col-sm-6 col-xs-12">
			<div class="x_title no-border text-center">
				<h2 id="auditResultsLabel"><small>Inspection Results</small></h2>
				<!-- <div><small id="auditResultsLabelSml"></small></div> -->
				<div class="clearfix"></div>
			</div>
			<div class="x_panel border-radius-10 has-shadow stats-container">
				<div class="x_content">
					<canvas id="auditResults"></canvas>
                    <div class="stat-error"></div>
					<br/>
					<div id="auditResultsBars" class="data-bars"></div>
				</div>
			</div>
        </div>
		<?php } ?>


		
		<!-- Exception Statuses -->
		<?php if( in_array( $this->user->account_id, [15, 17] ) ){ ?>
		<div class="col-md-3 col-sm-6 col-xs-12">
			<div class="x_title no-border text-center">
				<h2 ><small>Exception Statuses</small></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_panel border-radius-10 has-shadow stats-container">
				<div class="x_content">
					<canvas id="eviExceptionStatus"></canvas>
                    <br/>
                    <div class="stat-error"></div>
					<div id="eviExceptionStatusBars" class="data-bars"></div>
				</div>
			</div>
		</div>
		<?php } ?>

         <!-- Evidoc Statuses -->
		 <?php if( in_array( $this->user->account_id, [15, 17] ) ){ ?>
		<div class="col-md-3 col-sm-3 col-xs-12">
			<div class="x_title no-border text-center">
				<h2 id="evidocResultsLabel"><small>EVIDOC COMPLETION</small></h2>
				<!-- <div><small id="auditResultsLabelSml"></small></div> -->
				<div class="clearfix"></div>
			</div>
			<div class="x_panel border-radius-10 has-shadow stats-container">
				<div class="x_content">
                    <canvas id="evidocResults"></canvas>
                    <div class="stat-error"></div>
                    <br/>
                    <div id="evidocBars" class="data-bars"></div>
				</div>
			</div>
		</div>
		<?php } ?>

		 <?php if( !in_array( $this->user->account_id, [15, 17] ) ){ ?>
		<div class="col-md-4 col-sm-4 col-xs-12">
			<div class="x_title no-border text-center">
				<h2 id="atitle2021"><small>Schedules (Evidocs) <?php echo date( 'Y' ); ?></small></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_panel border-radius-10 has-shadow stats-container">
				<div class="x_content" style="height:500px">
                    <canvas id="barchart-13" style="margin-top: 0;height:500px;width:100%; margin: 0 auto;"></canvas>
                    <div class="stat-error"></div>
				</div>
			</div>
        </div>
		<?php } ?>

		<?php if( !in_array( $this->user->account_id, [15, 17] ) ){ ?>
		<div class="col-md-4 col-sm-4 col-xs-12">
			<div class="x_title no-border text-center">
				<h2 id="atitle2021"><small>Schedules (Checklists) <?php echo date( 'Y' ); ?></small></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_panel border-radius-10 has-shadow stats-container">
				<div class="x_content" style="height:500px">
					<div id="merchantLegend" class="chart-legend"></div>
                    <canvas id="barchart-13-checklists" style="margin-top: 0;height:500px;width:100%; margin: 0 auto;"></canvas>
                    <div class="stat-error"></div>
				</div>
			</div>
        </div>
		<?php } ?>

        <?php /* <div class="col-md-4 col-sm-4 col-xs-12">
			<div class="x_title no-border text-center">
				<h2 id="atitle"><small>Schedules <?php echo date( 'Y', strtotime( '+ 1 year' ) ); ?></small></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_panel border-radius-10 has-shadow stats-container">
				<div class="x_content" style="height:500px">
                    <canvas id="barchart-12" style="margin-top: 0;height:500px;width:100%; margin: 0 auto;"></canvas>
                    <div class="stat-error"></div>
				</div>
			</div>
        </div> */ ?>

	</div>
</div>

<script type="text/javascript">

	const SHOW_EMPTY_FIELDS = true;


	$( document ).ready(function () {

		function loadPieInspecton( json_data, canvas_id ) {

			if ( json_data.hasOwnProperty( "totals" ) ) {

				if (json_data[ "totals" ] != null) {
					datalabels = [ "Failed", "Recomendations", "Passed" ]

					dataset = [{
						label: [],
						backgroundColor: [ "#FF8C00", "#c03636", "#ffdd63" ],
						data: [
                            json_data[ "totals" ][ "failed" ],
                            json_data[ "totals" ]["recommendations"],
                            json_data[ "totals" ][ "passed" ]
                        ]
					}]

					var data = {
						labels: datalabels,
						datasets: dataset
					};

					options = {
						legend: {
							display: true,
							 onClick: null
						}
					}

					displayChart( canvas_id, data, options, 'doughnut' )
				}
			}
		}



		function loadExpiredChart( json_data, canvas_id ) {
			datalabels = []
			dataset = []
			if ( json_data.hasOwnProperty( "stats" ) ) {
				if ( json_data[ "stats" ] != null ) {
					json_data[ "stats" ].forEach(function( data_set ) {
						data_set_title = data_set[ "eol_group_text" ]
						data_set_amount = data_set[ "eol_group_total" ]
						data_set_is_active = data_set[ "is_active" ]
						if ( data_set_amount > 0 && (data_set_is_active == 1) ) {
							datalabels.push( data_set_title )
							dataset.push( data_set_amount )
						}
					});
					dataset = [{
						"label": "Assets",
						"data": dataset,
						"fill": false,
						"backgroundColor": "#5c5c5c",
						"borderWidth": 1
					}]

					options = {
						legend: {
							display: false,
							onClick: null
						},
						scales: {
							xAxes: [{
								ticks: {
									beginAtZero: true,
								},
								gridLines: {
									display: false
								},
							}],
							yAxes: [{
								ticks: {
									beginAtZero: true,
								},
								gridLines: {
									display: false
								},
							}]
						}
					}

					displayChart( canvas_id, { "labels": datalabels, "datasets": dataset }, options, "horizontalBar" )
				} else {
					alert("Loaded data has a null stats key!")
				}
			} else {
				alert("Loaded data does not have stats key!")
			}
		}


		function loadStatusChart(json_data, canvas_id) {

			datalabels = []
			dataset = []

            dataavailable = false

			if ( json_data.hasOwnProperty( "stats" ) ) {
				if ( json_data[ "stats" ] != null ) {
					var userJobStats = {
						"assigned": [],
						"en_route": [],
						"on_site": [],
						"in_progress": [],
						"failed": [],
						"successful": []
					};

                    json_data[ "stats" ][ "job_completion_stats" ].forEach(function( user ) {
						if (user != null) {
							datalabels.push( user["engineer_id"] )
							Object.keys( user ).forEach( function( user_stat ) {
								if ( userJobStats.hasOwnProperty( user_stat ) ) {
                                    if( user_stat != "un_assigned" ){
                                        userJobStats[ user_stat ].push( user[ user_stat ] )
                                        dataavailable = true
                                    }
								}
							});
						}
					});

					dataset.push({
						label: "Assigned",
						backgroundColor: "#9966cc ",
						data: userJobStats[ "assigned" ]
					})

					dataset.push({
						label: "En Route",
						backgroundColor: "#ff9933",
						data: userJobStats[ "en_route" ]
					})

					dataset.push({
						label: "On Site",
						backgroundColor: "#73522a",
						data: userJobStats[ "on_site" ]
					})

					dataset.push({
						label: "In Progress",
						backgroundColor: "#ffcc33",
						data: userJobStats[ "in_progress" ]
					})

					dataset.push({
						label: "Failed",
						backgroundColor: "#c74a2d",
						data: userJobStats[ "failed" ]
					})

					dataset.push({
						label: "Successful",
						backgroundColor: "#99cc99",
						data: userJobStats[ "successful" ]
					})


					var data = {
						labels: datalabels,
						datasets: dataset
					};

					options = {
						legend: {
							onClick: null
						},
						scales: {

							xAxes: [{
								ticks: {
									beginAtZero: true
								},
								gridLines: {
									display: false
								},
								stacked: true
							}],
							yAxes: [{
								ticks: {
									beginAtZero: true
								},
								gridLines: {
									display:false
								},
								stacked: true
							}]
						}
					}


				}
			}


            if(dataavailable){
                displayChart( canvas_id, data, options, "bar" )
            } else {
                $( "#" + canvas_id ).hide();
                $( "#" + canvas_id ).parent().find( ".data-bars" ).hide();
                $( "#" + canvas_id ).parent().find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
            }
		}

        function loadAssetGroupChart( json_data, canvas_id ){

            dataAvailable = false

            dataset = []
            datalabels = []

            completed_data = []
            uncompleted_data = []

            if ( json_data.hasOwnProperty( "stats" ) ) {
                if (json_data[ "stats" ] != null) {
                    json_data[ "stats" ].forEach(function( asset_group ) {
                        if ( asset_group != null ) {
                            datalabels.push( asset_group[ "category_name_alt" ] )
                            completed_data.push( asset_group[ "total_assets" ] )
                            dataAvailable = true
                        }
                    });

                    dataset.push({
                        label: 'Completed',
                        backgroundColor: "#0092CD",
                        borderColor: "#0092CD",
                        borderWidth: 1,
                        data: completed_data
                    })

                    var data = {
                        labels: datalabels,
                        datasets: dataset
                    };

                    options = {
                        legend: {
                            display: false,
                            onClick: null
                        },
                        scales: {
                            xAxes: [{
                                ticks: {
                                    beginAtZero: true,
									autoSkip: false
                                },
                                gridLines: {
                                    display: false
                                }
                            }],
                            yAxes: [{
                                gridLines: {
                                    display:false
                                }
                            }]
                        }
                    }


                }
            }

            if( dataAvailable ){
                displayChart( canvas_id, data, options, "bar" )
            } else {
                $( "#" + canvas_id ).hide();
                $( "#" + canvas_id ).parent().find( ".data-bars" ).hide();
                $( "#" + canvas_id ).parent().find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
            }
        }

		function loadScheduleChart( json_data, canvas_id ){

                dataAvailable = false

                dataset = []
                datalabels = []

                completed_data = {
					"total_activities": [],
					"total_completed": [],
					"total_in_progress": [],
					"total_not_started": []
				}

                uncompleted_data = []

                if ( json_data.hasOwnProperty( "stats" ) ) {
                    if ( json_data[ "stats" ] != null) {
                        $.each(json_data[ "stats" ], function( index, value ) {
                            if (value != null) {
                                datalabels.push( value[ "month_name_full" ] )
								completed_data[ "total_completed" ].push( value[ "total_completed" ] )
								completed_data[ "total_in_progress" ].push( value[ "total_in_progress" ] )
								completed_data[ "total_not_started" ].push (value[ "total_not_started" ] )
                                dataAvailable = true
                            }
                        });

						dataset.push({
                            label: 'Completed',
                            backgroundColor: "#99cc99",
                            borderColor: "#99cc99",
                            borderWidth: 1,
                            data: completed_data[ "total_completed" ]
                        })

						dataset.push({
                            label: 'In Progress',
                            backgroundColor: "#ffcc33",
                            borderColor: "#ffcc33",
                            borderWidth: 1,
                            data: completed_data[ "total_in_progress" ]
                        })

						dataset.push({
                            label: 'Not Due',
                            backgroundColor: "#a5a4a4",
                            borderColor: "#a5a4a4",
                            borderWidth: 1,
                            data: completed_data[ "total_not_started" ]
                        })

                        var data = {
                            labels: datalabels,
                            datasets: dataset
                        };

                        options = {
							responsive: false,
							legend: {
								onClick: null
							},
							scales: {
								xAxes: [{
									ticks: {
										beginAtZero: true
									},
									gridLines: {
										display: false
									},
									stacked: true
								}],
								yAxes: [{
									ticks: {
										beginAtZero: true
									},
									gridLines: {
										display:false
									},
									stacked: true
								}]
							}
                        }


                    }
                }

                if(dataAvailable){
                    displayChart( canvas_id, data, options, "bar" )
                } else {
                    $( "#" + canvas_id ).hide();
                    $( "#" + canvas_id ).parent().find(".data-bars").hide();
                    $( "#" + canvas_id ).parent().find(".stat-error").html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
                }
		}


		function loadCompletionChart( json_data, canvas_id ){

            dataset = []
			datalabels = []

			completed_data = []
			uncompleted_data = []

            dataavailable = false

			if ( json_data.hasOwnProperty( "stats" ) ) {
				if (json_data[ "stats" ] != null) {
					json_data[ "stats" ][ "job_completion_stats" ].forEach( function( user ) {
						if ( user != null ) {
							completed_percentage = Math.floor( user[ "successful" ] / user[ "total_jobs" ] * 100 )
							datalabels.push(user["engineer_id"])
							completed_data.push( completed_percentage )
                            dataavailable = true
						}
					});

					dataset.push({
						label: 'Completed',
						backgroundColor: "#0092CD",
						borderColor: "#0092CD",
						borderWidth: 1,
						data: completed_data
					})

					var data = {
						labels: datalabels,
						datasets: dataset
					};

					options = {
                        responsive: true,
						legend: {
							display: false,
							onClick: null
						},
						scales: {
							xAxes: [{
								ticks: {
									beginAtZero: true,
								},
								gridLines: {
									display: false
								}
							}],
							yAxes: [{
								ticks: {
									beginAtZero: true,
									callback: function ( value ) {
										return ( value ) + ( '%' );
									}
								},
								gridLines: {
									display:false
								}
							}]
						}
					}


				}
			}


            if(dataavailable){
                displayChart(canvas_id, data, options, "bar")
            } else {
                $( "#" + canvas_id ).hide();
                $( "#" + canvas_id ).parent().find( ".data-bars" ).hide();
                $( "#" + canvas_id ).parent().find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
            }
		}

		function displayChart( canvas_id, data, options, graphtype ) {

            Chart.defaults.global.defaultFontFamily = "'evi-font', sans-serif";
			var ctx = document.getElementById( canvas_id ).getContext( '2d' );
			window.myHorizontalBar = new Chart( ctx, {
				"options": options,
				"type": graphtype,
				"data": data
			});
		}


		var accountID = "<?php echo $this->user->account_id; ?>";


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

		/*-- AUDIT RESULTS --*/
		$.ajax({
			url:"<?php echo base_url( 'webapp/home/audit_stats' ); ?>",
			method:"POST",
			data:{ account_id:accountID, stat_type:"audit_results" },
			dataType: 'json',
			success:function( result ){
				if( result.status == 1 ){
					setChart( result, 'auditResults' );
				}else{
					console.log( result.status_msg );
					$( '#auditResults' ).closest('.stats-container').find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
				}
			}
		});

        /* EVIDOC Statuses */
        $.ajax({
			url:"<?php echo base_url( 'webapp/home/evidoc_stats' ); ?>",
			method:"POST",
			data:{account_id:accountID,stat_type:"completion"},
			dataType: 'json',
			success:function(result){
				if( result.status == 1 ){
					setChart( result, 'evidocResults' );
					console.log('\n\n**')
					console.log(result)
				}else{
					console.log( result.status_msg );
					$( '#evidocResults' ).closest('.stats-container').find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
				}
			}
		});

        /* EXCEPTION STATUSES */
        $.ajax({
			url:"<?php echo base_url( 'webapp/home/evidoc_exception_stats' ); ?>",
			method:"POST",
			data:{account_id:accountID,stat_type:"action_status"},
			dataType: 'json',
			success:function( result ){
				if( result.status == 1 ){
					setChart( result, 'eviExceptionStatus' );
				}else{
					console.log( result.status_msg );
					$( '#eviExceptionStatus' ).closest('.stats-container').find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
				}
			}
		});

    	/*-- Assets by Evidoc Type Category --*/
		$.ajax({
			url:"<?php echo base_url( 'webapp/home/assets_by_category_stats' ); ?>",
			method: "GET",
			data:{account_id:accountID},
			dataType: 'json',
			success:function( result ){
				if( result.status == 1 ){
					loadAssetGroupChart( result, "barchart-9");
				}else{
					console.log( result.status_msg );
					$( '#barchart-9' ).hide()
					$( '#barchart-9' ).closest('.stats-container').find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
				}
			}
		});
		
		
		/*-- assets by Asset Types --*/
		$.ajax({
			url:"<?php echo base_url( 'webapp/home/asset_by_group_stats' ); ?>",
			method: "GET",
			data:{account_id:accountID},
			dataType: 'json',
			success:function( result ){
				if( result.status == 1 ){
					loadAssetGroupChart( result, "barchart-8");
				}else{
					console.log( result.status_msg );
					$( '#barchart-8' ).hide()
					$( '#barchart-8' ).closest('.stats-container').find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
				}
			}
		});


		/*-- GET SITE COMPLIANCE STATS --*/
		$.ajax({
			url:"<?php echo base_url( 'webapp/home/job_completition_stats' ); ?>",
			method: "GET",
			data:{ account_id:accountID,where:{ data_target:'bar' } },
			dataType: 'json',
			success:function(result){
				if( result.status == 1 ){
					loadCompletionChart( result, "barchart-3" );
                    loadStatusChart( result, "barchart-2" );
				}else{
					console.log( result.status_msg );
					$( '#barchart-3' ).closest('.stats-container').find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
					$( '#barchart-2' ).closest('.stats-container').find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
				}
			}
        });


		/*-- GET SITE COMPLIANCE STATS --*/
		$.ajax({
			url:"<?php echo base_url( 'webapp/home/site_stats' ); ?>",
			method:"POST",
			data:{ account_id:accountID,stat_type:"audit_result_status" },
			dataType: 'json',
			success:function( result ){
				if( result.status == 1 ){
					setChart( result, 'SiteCompliance' );
				}else{
					$( '#siteCompliance' ).closest('.stats-container').find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
				}
			}
		});

        /*-- GET ASSET COMPLIANCE --*/
		/* $.ajax({
			url:"<?php echo base_url( 'webapp/home/asset_compliance_stats' ); ?>",
			method:"POST",
			data:{ account_id:accountID,stat_type:"outcome_result" },
			dataType: 'json',
			success:function( result ){
				if( result.status == 1 ){
					setChart( result, 'assetCompliance' );
				}else{
					$( '#assetCompliance' ).closest('.stats-container').find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
				}
			}
		}); */

		/*--- GET ASSET EOL STATS --*/
		/* $.ajax({
			url:"<?php echo base_url( 'webapp/home/asset_stats' ); ?>",
			method:"POST",
			data:{ account_id:accountID, stat_type:"eol" },
			dataType: 'json',
			success:function( result ){
				if( result.status == 1 ){
					setChart( result, 'EolStats' );
				}else{
					$( '#assetEOLstatsHeader' ).hide()
					$( '#assetEOLstats' ).hide()
					$( '#assetEOLstats' ).closest('.stats-container').find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
					
				}
			}
		}); */

		/*--- GET ASSET COST OF REPAIR STATS --*/
		/* $.ajax({
			url:"<?php echo base_url( 'webapp/home/asset_stats' ); ?>",
			method:"POST",
			data:{ account_id:accountID, stat_type:"replace_cost" },
			dataType: 'json',
			success:function( result ){
				if( result.status == 1 ){
					var repairCost = '0.00';
					if( result.stats.replacement_cost ){
						repairCost = result.stats.replacement_cost;
					}
					$( '#assetEOLstatsHeader' ).html( 'Estimated Replacement Cost' );
					$( '#assetEOLstats' ).html( '<small><h2>&pound;'+numberWithCommas( parseFloat( repairCost ).toFixed( 2 ) )+'</h2></small>' );
				}else{
					console.log( result.status_msg );
					$( '#assetEOLstats' ).closest('.stats-container').find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
				}
			}
		}); */


		var yearStart 		= "<?php echo date( 'Y-m-d', strtotime( 'Jan 01' ) ); ?>",
			yearEnd 		= "<?php echo date( 'Y-m-d', strtotime( 'Dec 31' ) ); ?>",
			nextYearStart 	= "<?php echo date( 'Y-m-d', strtotime( 'Jan 01 +1 year' ) ); ?>",
			nextYyearEnd 	= "<?php echo date( 'Y-m-d', strtotime( 'Dec 31 +1 year' ) ); ?>";
			
        $.ajax({
			url:"<?php echo base_url( 'webapp/home/schedule_stats' ); ?>",
			method: "GET",
			data:{ account_id:accountID, stat_type:"schedules", where:{year_start: nextYearStart, year_end: nextYyearEnd } },
			dataType: 'json',
			success:function( result ){
				if( result.status == 1 ){
					loadScheduleChart( result, "barchart-12" );
				} else {
					console.log( result.status_msg );
					$( "barchart-12" ).closest('.stats-container').find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
				}
			}
        });
		
		var prevYearStart 	= "<?php echo date( 'Y-m-d', strtotime( 'Jan 01 -1 year' ) ); ?>",
			prevYyearEnd 	= "<?php echo date( 'Y-m-d', strtotime( 'Dec 31 -1 year' ) ); ?>",
			yearStart 		= "<?php echo date( 'Y-m-d', strtotime( 'Jan 01' ) ); ?>",
			yearEnd 		= "<?php echo date( 'Y-m-d', strtotime( 'Dec 31' ) ); ?>",
			nextYearStart 	= "<?php echo date( 'Y-m-d', strtotime( 'Jan 01 +1 year' ) ); ?>",
			nextYyearEnd 	= "<?php echo date( 'Y-m-d', strtotime( 'Dec 31 +1 year' ) ); ?>";

		$.ajax({
			url:"<?php echo base_url( 'webapp/home/schedule_stats' ); ?>",
			method: "GET",
			data:{ account_id:accountID, stat_type:"schedules", where:{year_start: yearStart, year_end: yearEnd } },
			dataType: 'json',
			success:function( result ){
				if( result.status == 1 ){
					loadScheduleChart( result, "barchart-13" );
				} else {
					console.log( result.status_msg );
					$( "barchart-13" ).closest('.stats-container').find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
				}
			}
        });
		
		
		//Checklist Schedules
		$.ajax({
			url:"<?php echo base_url( 'webapp/home/schedule_stats' ); ?>",
			method: "GET",
			data:{ account_id:accountID, stat_type:"checklist_schedules", where:{year_start: yearStart, year_end: yearEnd } },
			dataType: 'json',
			success:function( result ){
				if( result.status == 1 ){
					loadChecklistScheduleChart( result, "barchart-13-checklists", yearStart );
				} else {
					$( "barchart-13-checklists" ).closest('.stats-container').find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
				}
			}
        });

		function loadChecklistScheduleChart( json_data, canvas_id, year_start ){
			
				var grandTotals	= ( json_data.totals ) ? json_data.totals : null;
				
                dataAvailable 	= false
                dataset 		= [];
                datalabels 		= [];

                completed_data = {
					"total_activities": [],
					"total_completed": [],
					"total_cancelled": [],
					"total_in_progress": [],
					"total_not_started": []
				}

                uncompleted_data = []

                if ( json_data.hasOwnProperty( "stats" ) ) {
					
					if ( json_data[ "stats" ] != null) {
                        $.each(json_data[ "stats" ], function( index, value ) {
                            if (value != null) {
                                datalabels.push( value[ "month_name_full" ] )
								completed_data[ "total_completed" ].push( value[ "total_completed" ] )
								completed_data[ "total_in_progress" ].push( value[ "total_in_progress" ] )
								completed_data[ "total_cancelled" ].push (value[ "total_cancelled" ] )
								completed_data[ "total_not_started" ].push (value[ "total_not_started" ] )
                                dataAvailable = true
                            }
                        });

						dataset.push({
                            label: 'Completed',
                            backgroundColor: "#99cc99",
                            borderColor: "#99cc99",
                            borderWidth: 1,
                            data: completed_data[ "total_completed" ]
                        });

						dataset.push({
                           label: 'In Progress',
                            backgroundColor: "#ffcc33",
                            borderColor: "#ffcc33",
                            borderWidth: 1,
                            data: completed_data[ "total_in_progress" ]
                        });

						dataset.push({
                            label: 'Cancelled',
                            backgroundColor: "#FC5B5B",
                            borderColor: "#FC5B5B",
                            borderWidth: 1,
                            data: completed_data[ "total_cancelled" ]
                        });
						
						dataset.push({
                            label: 'Not Started',
                            backgroundColor: "#a5a4a4",
                            borderColor: "#a5a4a4",
                            borderWidth: 1,
                            data: completed_data[ "total_not_started" ]
                        });

                        var data = {
                            labels: datalabels,
                            datasets: dataset
                        };

                        options = {
							responsive: false,
							legend: {
								onHover: function(e) {
									e.target.style.cursor = 'pointer';
								},
								onClick: function (event, legendItem) {
									var clickFilterUrl	= "<?php echo base_url( '/webapp/checklist/checklist_jobs' ); ?>";
									var datasetStatus 	= legendItem.text;									
									switch( datasetStatus ){										
										case 'Completed':
											clickFilterUrl += '?group_status=completed';
											break;
											
										case 'In Progress':
											clickFilterUrl += '?group_status=in_progress';
											break;
											
										case 'Cancelled':
											clickFilterUrl += '?group_status=cancelled';
											break;
											
										case 'Not Started':
											clickFilterUrl += '?group_status=not_started';
											break;
									}
									
									/* swal({
										html: '<div><p>11 Jobs Completed ( 56% )</p><p>Click <a class="pointer" heref="'+clickFilterUrl+'?group_status=completed'+'" >here</a> to see these Jobs</p></div>',
									}); */
									window.open(clickFilterUrl);
								}
							},
							onClick:function(e){
								/* var clickFilterUrl	= "<?php echo base_url( '/webapp/checklist/checklist_jobs' ); ?>";
								var activeYear 		= new Date( year_start );
								var activePoints 	= this.getElementAtEvent(e);
								var selectedIndex	= activePoints[0]._model;
								var activeStatus 	= selectedIndex.datasetLabel.replace(/ /g, "_").toLowerCase();
								var activeMonth  	= selectedIndex.label.toLowerCase()+'-'+activeYear.getFullYear();
								clickFilterUrl += '?month='+activeMonth+'&group_status='+activeStatus;
								window.open(clickFilterUrl); */
							},
							hover: {
								onHover: function(e) {
									var point = this.getElementAtEvent(e);
									if (point.length) e.target.style.cursor = 'pointer';
									else e.target.style.cursor = 'default';
								}
							},
							scales: {
								xAxes: [{
									ticks: {
										beginAtZero: true
									},
									gridLines: {
										display: false
									},
									stacked: true
								}],
								yAxes: [{
									ticks: {
										beginAtZero: true
									},
									gridLines: {
										display:false
									},
									stacked: true
								}]
							}
                        }
                    }
                }

                if(dataAvailable){
                    displayChart( canvas_id, data, options, "bar" )
                } else {
                    $( "#" + canvas_id ).hide();
                    $( "#" + canvas_id ).parent().find(".data-bars").hide();
                    $( "#" + canvas_id ).parent().find(".stat-error").html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
                }
		
				//Generate Legend
				legend.innerHTML = chart.generateLegend();
		}

	});

	/*--- POPULATE CHART DATA ---*/
	function setChart( data, chartType = 'AssetStatus' ){

		if( chartType.length > 0 ){

			switch( chartType.toLowerCase() ){
                case 'eviexceptionstatus':

                    dataavailable = false

                    dataLabels = []
                    dataValues = []
                    dataColours = []

                    actioned = 0
                    total = 0

                    if(data.stats){
                        $.each( data.stats, function( index, statusItem ) {

							var clickFilterUrl	= "<?php echo base_url( '/webapp/audit/exceptions' ); ?>";

                            var statusBarDiv = '<a href="'+clickFilterUrl+'?group=' + statusItem.action_status_group + '"><div class="box-header status-bar shadow-red" style="background-color:' + statusItem.hex_colour + '; opacity:0.9;color:#fff"><h3 class="status-bar-header">';
                                statusBarDiv = statusBarDiv+( statusItem.action_status_alt )+' <span class="pull-right">' + statusItem.status_total + '</span>';
                                statusBarDiv = statusBarDiv+'</h3></div></a>';

                            $( '#eviExceptionStatusBars' ).append( statusBarDiv );
                            if(statusItem.status_total > 0){
                                dataAvailable = true;
                                dataLabels.push( statusItem.action_status_alt )
                                dataValues.push( statusItem.status_total )
                                dataColours.push( statusItem.hex_colour )
                            }

                            if( statusItem.action_status_group == 'actioned' ){
                                actioned = parseInt( statusItem.status_total )
                                total += parseInt( statusItem.status_total )
                            } else {
                                total += parseInt( statusItem.status_total )
                            }
                        });
                    }

					
					actioned_percentage = parseFloat( ( actioned / total ) * 100 ).toFixed( 2 )
					
					if( actioned_percentage > 0 ){
						var configOptions	= { 'centerText': actioned_percentage + '%', 'centerTextBottom':'Completion', 'centerTextColor': '#6CD167' };
					} else {
						var configOptions	= { 'centerText':'0.00%', 'centerTextBottom':'Complete', 'centerTextColor': '#FC5B5B' };
					}


                    if( dataAvailable ){
                        var dataCofig     = chartDataConfig( dataLabels, dataValues, dataColours, 'Periodic Audits' ); //config the actual chart values
                        var chartConfig   = doughnutConfig( dataCofig, configOptions ); //build the graph before writing to screen
                        var $chart 		  = $('#eviExceptionStatus');
                        var siteCompChart = new Chart( $chart[ 0 ], chartConfig ); //Run the graph and display to screen*/



                    }else{
                        $( '#eviExceptionStatus' ).hide();
						$( '#eviExceptionStatus' ).parent().find( ".data-bars" ).hide();
                        $( '#eviExceptionStatus' ).parent().find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
                    }

                    break;


                case 'assetcompliance':

                    var dataAvailable = false;
                        var dataLabels 		= data.stats.map( e=>e.result_status ),
                            dataValues 		= data.stats.map( e=>e.status_total ),
                            dataBGColor		= data.stats.map( e=>e.result_status_colour ),
                            dataHoverBGColor= data.stats.map( e=>e.result_status_colour );

                        var dataTotals		= data.totals;//Data summary

                        var clickFilterUrl	= "<?php echo base_url( '/webapp/asset/result_status' ); ?>";
                        //var clickFilterUrl = "";

                        var total = 0
                        var compliant = 0

                        //Unpack the stats into coloured divs
                        $.each( data.stats, function( index, statusItem ) {

                            //Only show bars for statuses that have totals over 0
                            if( statusItem.status_total > 0 && ( statusItem.result_status_group != "not_set" ) ){
							//if( statusItem.status_total > 0 ){
                                //var statusBarDiv = '<a href="#"><div class="box-header status-bar" style="background-color:'+statusItem.result_status_colour+'; opacity:0.9;color:#fff"><h3 class="status-bar-header">';
                                var statusBarDiv = '<a href="'+clickFilterUrl+'?group='+statusItem.result_status_group+'"><div class="box-header status-bar shadow-'+statusItem.hex_color+'" style="background-color:'+statusItem.result_status_colour+'; opacity:0.9;color:#fff"><h3 class="status-bar-header">';
                                    statusBarDiv = statusBarDiv+( statusItem.result_status_alt )+' <span class="pull-right">'+statusItem.status_total+'</span>';
                                    statusBarDiv = statusBarDiv+'</h3></div></a>';

                                $( '#assetComplianceBars' ).append( statusBarDiv );

                                dataAvailable = true;
                            }

                            if( statusItem.result_status_group == "passed" ){
                                compliant = parseInt( statusItem.status_total )
                            }
                            total += parseInt( statusItem.status_total )

                        });

                        compliance_text = ( total > 0 ) ? parseFloat( ( compliant / total ) * 100 ).toFixed( 2 ) + '%' : "0%"

                        if( total > 0 ){
                            var configOptions	= { 'centerText': compliance_text, 'centerTextBottom':"Compliant", 'centerTextColor': '#6CD167'  };
                        }else{
                            //This if for 0% percent compliance
                            var configOptions	= { 'centerText':'100%', 'centerTextBottom':'Not compliant', 'centerTextColor': '#FC5B5B' };
                        }

                        if( dataAvailable ){
                            var dataCofig     = chartDataConfig( dataLabels, dataValues, dataBGColor, 'Periodic Audits' ); //config the actual chart values
                            var chartConfig   = doughnutConfig( dataCofig, configOptions ); //build the graph before writing to screen
                            var $chart 		  = $('#assetCompliance');
                            var siteCompChart = new Chart( $chart[ 0 ], chartConfig ); //Run the graph and display to screen*/

                        }else{
                            $( '#assetCompliance' ).hide();
						    $( '#assetCompliance' ).parent().find( ".data-bars" ).hide();
                            $( '#assetCompliance' ).parent().find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
                        }

					break;

                case 'evidocresults':
                    dataavailable = false

                    dataLabels = []
                    dataValues = []
                    dataColours = []

                    total = -1
                    completed = -1
					
					var clickFilterUrl	= "<?php echo base_url( '/webapp/audit/audit_completion_status' ); ?>";

                    if(data.stats){
                        $.each( data.stats, function( index, statusItem ) {
                            if( (statusItem.status_total > 0 && statusItem.status_name != "total") || SHOW_EMPTY_FIELDS){
                                var statusBarDiv = '<a href="'+clickFilterUrl+'?group='+statusItem.status_name_alt+'"><div class="box-header status-bar shadow-red" style="background-color:' + statusItem.hex_colour + '; opacity:0.9;color:#fff"><h3 class="status-bar-header">';
                                    statusBarDiv = statusBarDiv+( statusItem.status_name_alt )+' <span class="pull-right">'+statusItem.status_total+'</span>';
                                    statusBarDiv = statusBarDiv+'</h3></div></a>';

                                $( '#evidocBars' ).append( statusBarDiv );

                                dataAvailable = true;
                            }
                            if( statusItem.status_name !== 'total' ){
                                dataLabels.push( statusItem.status_name_alt )
                                dataValues.push( statusItem.status_total )
                                dataColours.push( statusItem.hex_colour )
                            }

                            if( statusItem.status_name == 'completed' ){
                                completed = statusItem.status_total
                            }

                            if( statusItem.status_name == 'total' ){
                                total = statusItem.status_total

                            }
                        });
                    }
					

                    if( total !== -1 && completed !== -1 && completed !== 0 && completed != '' && total != ''){
                        var configOptions	= { 'centerText': parseFloat( ( completed / total ) * 100 ).toFixed( 2 ) + '%', 'centerTextBottom':'Completion', 'centerTextColor': '#6CD167' };
                    } else {
                        var configOptions	= { 'centerText':'0%', 'centerTextBottom':'Complete', 'centerTextColor': '#FC5B5B' };
                    }


					if( dataAvailable ){
						var dataCofig     = chartDataConfig( dataLabels, dataValues, dataColours, 'Periodic Audits' ); //config the actual chart values
						var chartConfig   = doughnutConfig( dataCofig, configOptions ); //build the graph before writing to screen
						var $chart 		  = $( '#evidocResults' );
						var siteCompChart = new Chart( $chart[ 0 ], chartConfig ); //Run the graph and display to screen*/
					}else{
                        $( '#evidocResults' ).hide();
						$( '#evidocResults' ).parent().find( ".data-bars" ).hide();
                        $( '#evidocResults' ).parent().find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
					}

					break;

				/*--- SITE COMPLIANCE STATISTICS ----*/
				case 'sitecompliance':

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
							//var statusBarDiv = '<a href="#"><div class="box-header status-bar" style="background-color:'+statusItem.result_status_colour+'; opacity:0.9;color:#fff"><h3 class="status-bar-header">';
							var statusBarDiv = '<a href="'+clickFilterUrl+'?group='+statusItem.result_status_group+'"><div class="box-header status-bar shadow-'+statusItem.hex_color+'" style="background-color:'+statusItem.result_status_colour+'; opacity:0.9;color:#fff"><h3 class="status-bar-header">';
								statusBarDiv = statusBarDiv+( statusItem.result_status_alt )+' <span class="pull-right">'+statusItem.status_total+'</span>';
								statusBarDiv = statusBarDiv+'</h3></div></a>';

							$( '#siteComplianceBars' ).append( statusBarDiv );

							dataAvailable = true;
						}

					});


					//Verify that there is some data before attempting to draw the chart
					if( dataAvailable ){
						var dataCofig     = chartDataConfig( dataLabels, dataValues, dataBGColor, 'Periodic Audits' ); //config the actual chart values
						var chartConfig   = doughnutConfig( dataCofig, configOptions ); //build the graph before writing to screen
						var $chart 		  = $( '#siteCompliance' );
						var siteCompChart = new Chart( $chart[ 0 ], chartConfig ); //Run the graph and display to screen*/

					}else{
                        $( '#siteCompliance' ).hide();
						$( '#siteCompliance' ).parent().find( ".data-bars" ).hide();
                        $( '#siteCompliance' ).parent().find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )

					}

					break;

				/*---- Periodic Audits Tracker ----*/
				case 'periodicauditss':

					var configOptions	= { 'centerText':'', 'centerTextBottom':'', 'centerTextColor':'#6CD167' };
					var dataStats		= data.stats,//Data totals
						dataStatsTotals	= data.totals,//Data totals
						dataDates		= data.dates;//Data dates
						$( '#periodicAuditsLabel' ).html( '<small>Monthly Audits</small>' );
						$( '#periodicAuditsLabelSml' ).html( dataDates.date_from + ' - ' + dataDates.date_to );

						var clickFilterUrl	= "<?php echo base_url( '/webapp/audit/periodic_audits' ); ?>";

                        dataStats = false
                        dataStatsTotals = false

						if( dataStatsTotals ){

							var dataAvailable = false;
							var dataLabels 		= dataValues = dataBGColor = dataHoverBGColor = [];
							var grandTotal 		= 0;
							var grandTotalPerc	= 0;

							/*------- SHOW THE COLLECTIVE TOTALS ------ */
							$.each( dataStatsTotals, function( index, auditStatsTotalsGroup ){
								if( parseInt( auditStatsTotalsGroup.group_total ) > 0 ){
									var auditsBarDiv = '<a href="#"><div class="box-header status-bar shadow-'+auditStatsTotalsGroup.hex_color+'" style="background-color:'+auditStatsTotalsGroup.group_colour+'; opacity:0.9;color:#fff"><h3 class="status-bar-header">';
									//var auditsBarDiv = '<a href="'+clickFilterUrl+'?audit_group='+auditStatsTotalsGroup.group_name.toLowerCase()+'"><div class="box-header status-bar" style="background-color:'+auditStatsTotalsGroup.group_colour+'; opacity:0.9;color:#fff"><h3 class="status-bar-header">';
										auditsBarDiv = auditsBarDiv+( auditStatsTotalsGroup.group_name_alt )+' <span class="pull-right">'+auditStatsTotalsGroup.group_total+'</span>';
										auditsBarDiv = auditsBarDiv+'</h3></div></a></div>';
										$( '#periodicAuditsBars' ).append( auditsBarDiv );

									dataAvailable = true;

									if( auditStatsTotalsGroup.group_name.toLowerCase() != 'due' ){
										dataLabels.push( auditStatsTotalsGroup.group_name );
										dataValues.push( auditStatsTotalsGroup.group_colour );
										dataBGColor.push( auditStatsTotalsGroup.group_colour );
										dataHoverBGColor.push( auditStatsTotalsGroup.group_total );
									}else{
										//Do totals for doughnut circle
										grandTotal 					  = auditStatsTotalsGroup.group_total;
										grandTotalPercRaw			  = auditStatsTotalsGroup.group_percentage_raw;
										configOptions.centerText	  = auditStatsTotalsGroup.group_percentage;
										configOptions.centerTextBottom= 'Not Started';
										configOptions.centerTextColor = ( grandTotalPercRaw <= 30 ) ? configOptions.centerTextColor : '#FC5B5B';
									}
								}
							} );

							//Setup data config
							var dataCofig   = chartDataConfig( dataLabels, dataValues, dataBGColor, 'Periodic Audits' );
							var chartConfig = doughnutConfig( dataCofig, configOptions );

							if( dataAvailable ){
								var $chart = $('#periodicAudits');
								var periodicAuditsChart = new Chart( $chart[0], chartConfig );
							}else{
								$( '#periodicAudits' ).hide();
								$( '#periodicAudits' ).parent().find( ".data-bars" ).hide();
								$( '#periodicAudits' ).parent().find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
							}

						}else if( dataStats ){

							/*-------- SHOW THE INDIVIDUAL GROUP ------ */
							var headerCounter 	= 1;

							$.each( dataStats, function( index, auditStatsGroup ){
								var groupHeader = ( auditStatsGroup[0].group_class.length > 0 ) ? ( ucwords( auditStatsGroup[0].group_class ) ) : 'Group Header '+headerCounter;
								if( auditStatsGroup.length > 0 ){
									$( '#periodicAuditsBars' ).append( '<div class="box-header" ><strong>'+groupHeader+'</strong></div>' );
									$.each( auditStatsGroup, function( x, groupData ){
										if( parseInt( groupData.group_total ) > 0 ){
											var auditsBarDiv = '<a href="#"><div class="box-header status-bar shadow-'+groupData.hex_color+'" style="background-color:'+groupData.group_colour+'; opacity:0.9;color:#fff"><h3 class="status-bar-header">';
											//var auditsBarDiv = '<a href="'+clickFilterUrl+'?group='+groupData.group_class+'&type='+groupData.group_name+'"><div class="box-header status-bar" style="background-color:'+groupData.group_colour+'; opacity:0.9;color:#fff"><h3 class="status-bar-header">';
												auditsBarDiv = auditsBarDiv+( groupData.group_name_alt )+' <span class="pull-right">'+groupData.group_total+'</span>';
												auditsBarDiv = auditsBarDiv+'</h3></div></a></div>';
												$( '#periodicAuditsBars' ).append( auditsBarDiv );
										}

									});
								}
								headerCounter++;
							} );

						}else{
                            $( '#periodicAudits' ).hide();
                            $( '#periodicAudits' ).parent().find( ".data-bars" ).hide();
                            $( '#periodicAudits' ).parent().find( ".stat-error" ).html( "<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>" )
						}

					break;

				/*--- AUDIT RESULT STATS ---*/
				case 'auditresults':

					var dataAvailable = false;
					var dataLabels 		= data.stats.map( e=>e.result_status ),
						dataValues 		= data.stats.map( e=>e.status_total ),
						dataBGColor		= data.stats.map( e=>e.result_status_colour ),
						dataHoverBGColor= data.stats.map( e=>e.result_status_colour );

					var dataTotals		= data.totals;//Data summary
					var clickFilterUrl	= "<?php echo base_url( '/webapp/audit/result_status' ); ?>";

					if( dataTotals.compliance_raw > 0 ){
						var configOptions	= { 'type':'doughnut', 'centerText':dataTotals.compliance, 'centerTextBottom':dataTotals.compliance_alt, 'centerTextColor':( ( dataTotals.compliance_raw >= 10 ) ? '#FC5B5B' : '#FC5B5B' ) };
					}else{
						//This if for 0% completion
						var configOptions	= { 'type':'doughnut', 'centerText':'0%', 'centerTextBottom':'No audits', 'centerTextColor': '#FC5B5B' };
					}

					var dataDates		= data.dates;//Data dates

					//$( '#auditResultsLabel' ).html( '<small>Inspection Results</small>' );
					$( '#auditResultsLabelSml' ).html( dataDates.date_from+' - '+dataDates.date_to );

					//Unpack the stats into coloured divs
					$.each( data.stats, function( index, statusItem ) {
						//Only show bars for statuses that have totals over 0
						if( statusItem.status_total > 0 || SHOW_EMPTY_FIELDS){
							//var statusBarDiv = '<a href="#"><div class="box-header status-bar" style="background-color:'+statusItem.result_status_colour+'; opacity:0.9;color:#fff"><h3 class="status-bar-header">';
							var statusBarDiv = '<a href="'+clickFilterUrl+'?group='+statusItem.result_status_group+'&date_from='+dataDates.date_from+'&date_to='+dataDates.date_to+'"><div class="box-header status-bar shadow-'+statusItem.hex_color+'" style="background-color:'+statusItem.result_status_colour+'; opacity:0.9;color:#fff"><h3 class="status-bar-header">';
								statusBarDiv = statusBarDiv+( statusItem.result_status )+' <span class="pull-right">'+statusItem.status_total+'</span>';
								statusBarDiv = statusBarDiv+'</h3></div></a>';

							$( '#auditResultsBars' ).append( statusBarDiv );

							dataAvailable = true;
						}

					});


					//Verify that there is some data before attempting to draw the chart
					if( dataAvailable ){

						var dataCofig     = chartDataConfig( dataLabels, dataValues, dataBGColor, 'Audit Results' ); //config the actual chart values
						var chartConfig   = doughnutConfig( dataCofig, configOptions ); //build the graph before writing to screen
						var $chart 		  = $('#auditResults');
						var siteCompChart = new Chart( $chart[0], chartConfig ); //Run the graph and display to screen

					} else {
                        $( '#auditResults' ).hide();
                        $( '#auditResults' ).parent().find(".data-bars").hide();
                        $( '#auditResults' ).parent().find(".stat-error").html("<i class='fas fa-exclamation-circle fa-6x'></i><br><br><h4>No data is available!</h4>")
					}

					break;

				/*--- ASSET STATUSES STATS ---*/
				case 'assetstatus':

					var dataLabels 		= data.stats.map( e=>e.status_name ),
						dataValue 		= data.stats.map( e=>e.status_total ),
						dataBGColor		= data.stats.map( e=>e.status_colour ),
						dataHoverBGColor= data.stats.map( e=>e.status_colour );

						$( '#assetStatusLabel' ).html( '<small>Assets Statuses</small>' );

						var canvas = document.getElementById("assetStatus");

						var $chart = $('#assetStatus');
						var barChartHome = new Chart( $chart[0], {
							type: 'doughnut',
							options: {
								scales: {
									xAxes: [{ display: false }],
									yAxes: [{ ticks: { max: 100, min: 0, callback: function( value ){ return value+ "%"; } }, display: false }],
								},
								legend: {
									display: false,
									position: 'right'
								},
								tooltips: { enabled: true },
								title: {
									display: true,
									text: 'Assets Statuses',
									position: 'bottom'
								}
							},
							data: {
								labels: dataLabels,
								datasets: [
									{
										label: "Status",
										backgroundColor: dataBGColor,
										hoverBackgroundColor: dataHoverBGColor,
										data: dataValue
									}
								]
							}
						});

						//Attach an event to the click
						canvas.onclick = function( evt ) {
							var activePoints = barChartHome.getElementsAtEvent( evt );
							var chartData = activePoints[ 0 ][ '_chart' ].config.data;
							var idx = activePoints[ 0 ][ '_index' ];

							var label = chartData.labels[ idx ];
							var value = chartData.datasets[ 0 ].data[ idx ];

							var url = "<?php echo base_url( 'asset/assets' ) ?>/?label=" + label + "&value=" + value;

						}
					break;

				/*-- Asset EOL Stats --*/
				case 'eolstats':
						var dataEolStats	= data.stats;//Data summary
						var clickFilterUrl	= "<?php echo base_url( '/webapp/asset/eol' ); ?>";
                        dataavailable = false

						//Unpack the EOL stats into coloured divs
						$.each( dataEolStats, function( index, eolGroupItem ) {
							//Only show bars for statuses that have totals over 0
							if( parseInt( eolGroupItem.eol_group_total ) > 0 ){
								//var eolBarDiv = '<a href="#"><div class="box-header status-bar" style="background-color:'+eolGroupItem.eol_group_colour+'; opacity:0.9;color:#fff"><h3 class="status-bar-header">';
								var eolBarDiv = '<a href="'+clickFilterUrl+'?period_days='+eolGroupItem.eol_group_max+'"><div class="box-header status-bar shadow-'+eolGroupItem.hex_color+'" style="background-color:'+eolGroupItem.eol_group_colour+'; opacity:0.9;color:#fff"><h3 class="status-bar-header">';
									eolBarDiv = eolBarDiv+( eolGroupItem.eol_group_text )+' <span class="pull-right">'+eolGroupItem.eol_group_total+'</span>';
									eolBarDiv = eolBarDiv+'</h3></div></a>';
								$( '#AssetEOLBars' ).append( eolBarDiv );
                                dataavailable = true
							}

						});

						break;

				/*--- Job Stats ---*/
				case 'jobstats':

					$( '#jobStatsLabel' ).html( '<small>Job Completion Stats</small>' );
					var jobStats= data.stats;
					var ctx 	= document.getElementById( "jobStats" );
					var myChart = new Chart(ctx, {
							type: 'bar',
							data: {
								labels: jobStats.labels,
								datasets: [{
									label: 'Number per Status',
									data: jobStats.values,
									backgroundColor: jobStats.colors,
									borderColor: jobStats.colors,
									borderWidth: 1
								}]
						},
						options: {
							responsive: false,
							scales: {
								xAxes: [{
									ticks: {
										maxRotation: 90,
										minRotation: 80
									}
								}],
								yAxes: [{
									ticks: {
										beginAtZero: true
									}
								}]
							}
						}
					});

					break;
					
				/*-- Asset Tagging Summary --*/
				case 'assettaggingsummary':
					var dataAssetTaggingSum	= data.stats.stats;
					var clickFilterUrl	= "";
					dataavailable 		= false

					//Unpack the EOL stats into coloured divs
					$.each( dataAssetTaggingSum, function( index, headerItem ) {
						if( parseInt( headerItem.column_value ) > 0 ){
							//var atsBarDiv = '<a href="'+clickFilterUrl+'?period_days='+headerItem.column_key+'"><div class="box-header status-bar shadow-green" style="background-color:'+headerItem.hex_color+'; opacity:0.9;color:#fff"><h3 class="status-bar-header">';
							var atsBarDiv = '<div class="box-header status-bar shadow-green" style="background-color:'+headerItem.hex_color+'; opacity:0.9;color:#fff"><h3 class="status-bar-header">';
								atsBarDiv = atsBarDiv+( headerItem.column_header )+' <span class="pull-right">'+headerItem.column_value+'</span>';
								//atsBarDiv = atsBarDiv+'</h3></div></a>';
								atsBarDiv = atsBarDiv+'</h3></div>';
							$( '#AssetTaggingSummaryBars' ).append( atsBarDiv );
							dataavailable = true
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
