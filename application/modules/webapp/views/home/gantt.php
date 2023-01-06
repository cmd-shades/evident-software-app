<html lang="en">
	<head>
		<script src="<?php echo base_url("assets/js/custom/evigantt_util.js"); ?>"></script>
        <script src="<?php echo base_url("assets/js/custom/evigantt.js"); ?>"></script>
        <link href="https://fonts.googleapis.com/css?family=Quicksand&display=swap" rel="stylesheet">
        <link href="<?php echo base_url("assets/css/custom/gantt_styles.css"); ?>" rel="stylesheet">
		<script src="<?php echo base_url("assets/js/custom/infiscroll.js"); ?>"></script>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url("assets/css/custom/infiscroll.css"); ?>">
	</head>
	<body>

		<style>

			.cellData {
				background-color:#18B3F1;
				width:50%;
				height:30px;
				float: left;
                margin-top: 10px;
                position: absolute;
                z-index: 700;

			}

			.cellNoData {
				background-color:red;
				width:50%;
				height:30px;
				float: left;
				margin-top: 10px;
                visibility: hidden;
			}

			.monthCell {
				border-right: 1px solid gray;
				width: 25%;
				height:60px;
				margin: 0;
                float: left;
                position: relative;
			}



			.hide {
				display: none;
			}

			.project-months {
				width:100%;
			}

			.gantt-main {
                    padding-right: 0px !important;
                    -webkit-touch-callout: none;
                    -webkit-user-select: none;
                    -khtml-user-select: none;
                    -moz-user-select: none;
                        -ms-user-select: none;
                            user-select: none;
			}

			.evigantt-table tr {
				height: 60px;

			}

			.evigantt-table {
                text-align: center;

			}

			.col-title {
				text-align: center;
			}


			.evigantt-table th {
				border-right: 1px solid gray;
				color: #18B3F1;
			}

			.evigantt-table td {
				border-right: 1px solid gray;
			}


			.evigantt-table th:first-child {
				text-align: left !important;
				padding-left: 20px;
			}

			.evigantt-table td:first-child {
				text-align: left;
				padding-left: 20px;
			}

			.evigantt-table th:last-child {
				border-right: none !important;
			}

			.evigantt-table td:last-child {
				border-right: none !important;
			}

			.gantt-content {
				width: calc(100%);/*
				display: inline-block;*/
				padding-left: 0px !important;
				margin-left: 0px !important;

                height: 100%;
                min-width: 1000px !important;
			}

			.gantt-year-tag {
				font-weight: bold !important;
			}

			.row {
				margin-left: 0px !important;
			}


		</style>

		<div style="margin-left:30px; margin-right: 30px;">
		<div id="ev-menu" class="infini-content"></div>
		</div>
		<script>

    	$( document ).ready(function() {

			infi_scroll = new infiScroll( [ { "text" : "Gantt Chart", "link":"#","selected_tab":true },
											{ "text" : "Details", "link":"#","selected_tab":false },
											{ "text" : "Actions","link":"#","selected_tab":false },
											{ "text": "EviTime","link":"#","selected_tab":false },
											{ "text": "Users","link":"#","selected_tab":false },
											{ "text": "Documents","link":"#","selected_tab":false }
										  ], 6, document.getElementById( "ev-menu" ) )
			});

		</script>


		<div id="evi-chart" class="gantt-container">
			<div class="container-fluid gantt-content" style="margin: 0px !important;padding-right: 0px !important;">
				<div class="row">
					<div class="col-md-4" style="padding-right:0px;padding-left: 0px;background-color: #f7f7f7;padding-left: 20px;padding-top: 20px; padding-bottom: 20px;border-right: 1px solid #e0e0e0;">
					<table width="100%" height="100%" class="evigantt-table">
					  <tr>
						<th style="width:25%"><h2 class="col-title" style="text-align:left">Action Name</h2></th>
						<th style="width:25%"><h2 class="col-title">Start Date</h2></th>
						<th style="width:25%"><h2 class="col-title">End Date</h2></th>
					  </tr>
                      <tbody style="font-size:15px">
                        <?php
                            foreach($project_data['stats'] as $key => $proj){
                                echo "<td>" . $proj->project_name . "</td> <td>" . $proj->project_start_date . "</td> <td>" . $proj->project_finish_date . "</td> </tr>";
                            }
                        ?>
					  </tbody>
					</table>

					</div>
					<div class="col-md-8 gantt-main" style="padding-left:0px;position:relative;padding-top: 20px;padding-right: 20px; padding-bottom: 20px;">
						<div style="height:100%;width:calc(100% - 60px);display: inline-block;min-width:400px;">
							<div class="project-months"></div>
                            <br style="clear:both" />


                            <?php foreach($project_data['stats'] as $key => $proj){ ?>
                                <div class="project-<?php echo $proj->project_id; ?>">
                                    <?php
                                        foreach (range(1, $monthCount) as $monthNum) {
                                            if($monthNum - 1 < 4){
                                                echo "<div class='monthCell month-" . $monthNum . "'></div>";
                                            } else {
                                                echo "<div class='monthCell month-" . $monthNum . "' style='display:none;'></div>";
                                            }
                                        }
                                    ?>
                                </div>
                                <br style="clear:both" />
                            <?php } ?>



							<div class="timebar-container">

							</div>

						</div>
						<div class="gantt-nav-buttons" style="display: inline-block;padding-left: 8px;margin: 0; position: absolute; top: calc(50% - 40px);">
							<i class="fas fa-arrow-circle-right" id="gantt-next"></i><br>
							<i class="fas fa-arrow-circle-left" id="gantt-prev"></i>
						</div>

					</div>

				</div>
			</div>

		</div>




		<script>

		// set the animation speed for better performence.
		jQuery.fx.interval = 20;

		const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

		var current_month = 0

		project_months = []


        $.each( <?php echo json_encode($viewMonths); ?>, function( key, value ) {
            project_months.push(new Date(Date.parse(value)))
        });

        project_data = {}

        $.each( <?php echo json_encode($project_data['stats']); ?> , function( key, value ) {
            project_data["project-" + value.project_id] = { project_start: new Date(Date.parse(value.project_start_date)), project_end: new Date(Date.parse(value.project_finish_date)), project_name: value.project_name }
        });


		$(function() {

			$(window).on('resize', function(){
				updateTimeBar()
			});

			project_months.forEach(function(gantt_month, i) {
				if(i != project_months.length - 1){
					month_name = monthNames[gantt_month.getMonth()]
					year_number = gantt_month.getFullYear()
					if(i < 4){
						$("#evi-chart").find(".project-months").append("<div class='monthCell month-" + (i + 1) + "'><h2 style='text-align:center'>" + month_name + " <br><small class='gantt-year-tag'>" + year_number + "</small></h2></div>")
					} else {
						$("#evi-chart").find(".project-months").append("<div class='monthCell month-" + (i + 1) + "' style='display:none;'><h2 style='text-align:center'>" + month_name + " <br><small class='gantt-year-tag'>" + year_number + "</small></h2></div>")
					}

					today_date = new Date()

					if(today_date >= project_months[i] && today_date < project_months[i + 1]){
						today_month = i + 1
						today_decimal = dayToDecimal(today_date, true)

						addTimeBar(today_month, today_decimal)

					}
				}
            });

            project_decimals = datesToDecimals(project_data, project_months)

            gantt_container = document.getElementById("evi-chart")

            loadEviGantt(gantt_container, project_decimals)

            $( "#gantt-next" ).click(function() {
			 ganttNext()
			});

			$( "#gantt-prev" ).click(function() {
			 ganttPrev()
			});

		});

		function updateTimeBarFor(remainingFrames){
			if(remainingFrames > 0){
				updateTimeBar()
				setTimeout(function(){
					updateTimeBarFor(remainingFrames - 1)
				}, 10); // 100 fps (1/100) x 1000
			}
		}

        animationInProgress = false

        function ganttNext(){
			if((current_month + 4 < (project_months.length - 1)) && !animationInProgress){

                animationInProgress = true

				updateTimeBarFor(200)

				current_month += 1

                $(".month-" + current_month).css("width" , "24%")

				$(".month-" + current_month).animate( {
					 "width" : "0px"
				} , 400 ,   "linear"  ,  function (  ) {
					$(".month-" + current_month).css("display", "none")
				} )

				setTimeout(function(){
					$(".month-"+(current_month+4)).css("width" , "0px")
					$(".month-"+(current_month+4)).css("display", "block")


					$(".month-"+(current_month+4)).animate({
					  width: "25%"
					}, 400, "linear",  function (  ) {
					    animationInProgress = false
				});
				}, 200);
			}
		}

		function ganttPrev(){
			if(current_month > 0 && !animationInProgress){
				updateTimeBarFor(200)

                animationInProgress = true

				current_month -= 1


				$(".month-" + (current_month + 4 + 1)).css("width" , "24%")

				$(".month-" + (current_month + 4 + 1)).animate( {
					 "width" : "0px"
				} , 400 ,   "linear"  ,  function (  ) {
					$(".month-" + (current_month + 4 + 1)).css("display", "none")
				} )


				setTimeout(function(){
					$(".month-"+(current_month + 1)).css("width" , "0%")
					$(".month-"+(current_month + 1)).css("display", "block")


					$(".month-"+(current_month + 1)).animate({
					  width: "25%"
					}, 400, "linear",  function (  ) {
					    animationInProgress = false
				});
				}, 200);


			}

		}



		</script>
	</body>
</html>
