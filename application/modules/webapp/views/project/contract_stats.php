<link href="https://fonts.googleapis.com/css?family=Quicksand&display=swap" rel="stylesheet">
<script src="https://www.chartjs.org/dist/2.8.0/Chart.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url("assets/css/custom/contract_stats_style.css"); ?>">
<script src="<?php echo base_url( "assets/js/custom/infiscroll.js" ); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url("assets/css/custom/infiscroll.css"); ?>">

<body>

	<script>

        $( document ).ready(function() {
            Chart.pluginService.register({
            beforeDraw: function (chart) {
                if (chart.options.textline1) {
                    var width = chart.chart.width,
                    height = chart.chart.height,
                    ctx = chart.chart.ctx;

                    ctx.restore();
                    var fontSize = (height / 190).toFixed(2);
                    ctx.font = fontSize + "em sans-serif";
                    ctx.textBaseline = "middle";

                    var text = chart.options.textline1,
                    textX = Math.round((width - ctx.measureText(text).width) / 2),
                    textY = (height / 2 - (chart.titleBlock.height))-7;
                    ctx.fillStyle = "rgb(255, 102, 102)";
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                }
                if (chart.options.textline2) {
                    var width = chart.chart.width,
                    height = chart.chart.height,
                    ctx = chart.chart.ctx;

                    ctx.restore();
                    var fontSize = (height / 190).toFixed(2);
                    ctx.font = fontSize + "em sans-serif";
                    ctx.textBaseline = "middle";


                    var text = chart.options.textline2,
                    textX = Math.round((width - ctx.measureText(text).width) / 2),
                    textY = (height / 2 - (chart.titleBlock.height))+7;
                    ctx.fillStyle = "rgb(255, 102, 102)";
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                }
            }
        });

        var week_sla_config = {
        type: 'bar',
        data: {
                datasets: [{
                    data: [2, 4, 2, 1, 4, 4, 2],
                    backgroundColor: "rgb(0,178,241)",
                    label: 'Dataset 1'
                }],
                labels: [
                    'Mo',
                    'Tu',
                    'We',
                    'Th',
                    'Fr',
                    'Sa',
                    'Su'
                ]
            },
            options: {
                legend: {
                    display: false
                },
                animation: {
                    animateRotate: true,
                },
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [
                        {
                        ticks: {
                            beginAtZero: true,
                            min: 0,
                            max: this.max,
                            callback: function (value) {
                            return (value / this.max * 100).toFixed(0) + '%';
                            },
                        }
                    }]
                }
            }
        };

        var week_sla_ctx = document.getElementById('week-sla-rate').getContext('2d');
        new Chart(week_sla_ctx , week_sla_config);

        });

        chart_data = {}
        
	</script>

    <div id="main-container">

        <div id="contract-top-details">
            <h4 style="float:left">Contact Profile ID (<?php echo $contract_information->contract[0]->contract_id; ?>)</h4>
            <h4 style="text-align:left; margin: 0 auto !important; display: inline-block;"><?php echo $contract_information->contract[0]->contract_name; ?></h4>
            <h4 style="float:right"><span style="margin-right:20px;"><?php echo $contract_information->contract[0]->type_name; ?></span><i class="fas fa-trash-alt" style="color:rgb(212, 45, 45)"></i></h4>
        </div>

        <hr style="border-bottom: 2px solid black;"/>
        <br>

    <script>

        $(document).ready(function(){
            $("#btn-next").click(function () {
                
                scrollRight($(".scroll-btn").last(), $(".ev-scroll-bar"))
            });

            $("#btn-prev").click(function () {
                
                scrollLeft($(".scroll-btn").first(), $(".ev-scroll-bar"))
                
            });

        });

        function scrollLeft(target_div, scroll_div){

            var targetMargin = (-$(target_div).outerWidth())+"px";


            $(target_div).animate({
                marginLeft: targetMargin
            }, {
                duration: 500,
                complete: function () {
                    $(target_div).appendTo($(".ev-scroll-bar"))
                    $(target_div).css("margin-left", "0px")
                }
            });
        }

        function scrollRight(target_div, scroll_div){
            
            $(target_div).prependTo(scroll_div)
            $(target_div).css("margin-left", (-$(target_div).outerWidth())+"px")

            var targetMargin = "0px";


            $(target_div).animate({
                marginLeft: targetMargin
            }, {
                duration: 500,
                complete: function () {
                }
            });
            
        }

    </script>

        <div class="row chart-row">
            <div class="col-lg-3 col-md-3 col-sm-12 chart-panel">
                <div class="chart-panel-content">
                    <div class="chart-panel-container">
                        <div class="chart-panel-top">
                            <h4 class="chart-panel-title">BUILDING COMPLIANCE</h4>
                        </div>
                            <div class="chart-panel-body">

                            <?php

                                if(!$building_compliance->status){

                                    echo "<p class='nodata-message'>There is no data avaliable!</p>";

                                } else {
                                
                                ?>

                                <div class="chart-container">
                                    <canvas id="build-compliance" height="270px"></canvas>
                                </div>

                                <script>

                                    function notCompliant_bc(data){
                                        total = 0
                                        failed = 0
                                        data.forEach(function(entry) {
                                            total += parseInt(entry.status_total)
                                            if(entry.result_status_group == "failed"){
                                                failed = parseInt(entry.status_total)
                                            }
                                        });
                                        return (total == 0 ? "0%" : Math.round((failed/total)*1000)/10 + "%")

                                    }


                                    $( document ).ready(function() {
                                                var buildCompliance_config = {
                                                    type: 'doughnut',
                                                    data: {
                                                        datasets: [{
                                                            data: <?php echo json_encode(array_column($building_compliance->site_stats->stats, "status_total")); ?>,
                                                            backgroundColor: <?php echo json_encode(array_column($building_compliance->site_stats->stats, "result_status_colour")); ?>,
                                                            label: 'Building Compliance'
                                                        }],
                                                        labels: <?php echo json_encode(array_column($building_compliance->site_stats->stats, "result_status_alt")); ?>
                                                    },
                                                    options: {
                                                        legend: {
                                                            display: false
                                                        },
                                                        animation: {
                                                            animateRotate: true,
                                                        },
                                                        textline1: notCompliant_bc(<?php echo json_encode($building_compliance->site_stats->stats); ?>),
                                                        textline2: "Not compliant"
                                                    }
                                                };

                                                var build_complaince_ctx = document.getElementById('build-compliance').getContext('2d');
                                                new Chart(build_complaince_ctx, buildCompliance_config);
                                        });

                                </script>

                                <div class="chart-panel-totals" style="margin-top:10px;">

                                    <?php foreach($building_compliance->site_stats->stats as $stat){?>
                                    
                                    <div class="total-box ev-shadow" style="background-color:<?php echo $stat->result_status_colour ?>;margin:10px;padding:8px;padding-bottom:15px;display: block;">
                                        <p><span style="float:left"><?php echo $stat->result_status_alt ?></span><span style="float:right"><?php echo $stat->status_total; ?> </span></p>
                                    </div>

                                    <?php } ?>
                                    
                                </div>

                                <?php } ?>

                            </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-3 col-sm-12 chart-panel">
                <div class="chart-panel-content">
                    <div class="chart-panel-container">
                        <div class="chart-panel-top">
                            <h4 class="chart-panel-title">OVERALL INSPECTION RESULTS</h4>
                        </div>
                        
                        <div class="chart-panel-body">


                        <?php

                            if(!$audit_stats->status){

                                echo "<p class='nodata-message'>There is no data avaliable!</p>";

                            } else {

                            ?>

                                <div class="chart-container">
                                    <canvas id="overall-inspection-results" height="270px"></canvas>
                                </div>


                                <script>


                                    function notCompliant_oir(data){
                                        console.log(data)
                                        total = 0
                                        due = 0
                                        data.forEach(function(entry) {
                                            if(entry.group_name == "due"){
                                                due = parseInt(entry.group_total)
                                            } else {
                                                total += parseInt(entry.group_total)
                                            }
                                        });
                                        return (total == 0 ? "0%" : Math.round((due/total) * 1000)/10 + "%")
                                    }

                                    $( document ).ready(function() {                               

                                        var overall_inspection_results_config = {
                                        type: 'doughnut',
                                        data: {
                                                datasets: [{
                                                    data: <?php echo json_encode(array_column($audit_stats->audit_stats->totals, "group_total")); ?>,
                                                    backgroundColor: <?php echo json_encode(array_column($audit_stats->audit_stats->totals, "group_colour")); ?>,
                                                    label: 'Overall Inspection Results'
                                                }],
                                                labels: <?php echo json_encode(array_column($audit_stats->audit_stats->totals, "group_name_alt")); ?>
                                            },
                                            options: {
                                                legend: {
                                                    display: false
                                                },
                                                animation: {
                                                    animateRotate: true,
                                                },
                                                textline1:  notCompliant_oir(<?php echo json_encode($audit_stats->audit_stats->totals); ?>),
                                                textline2: "Not compliant"
                                            }
                                        };
                                    

                                        var overall_inspection_results_ctx = document.getElementById('overall-inspection-results').getContext('2d');
                                        new Chart(overall_inspection_results_ctx , overall_inspection_results_config);

                                    });

                                    </script>

                                <div class="chart-panel-totals" style="margin-top:10px;">
                                    
                                <?php foreach($audit_stats->audit_stats->totals as $stat){?>
                                    
                                    <div class="total-box ev-shadow" style="background-color:<?php echo $stat->group_colour ?>;margin:10px;padding:8px;padding-bottom:15px;display: block;">
                                        <p><span style="float:left"><?php echo $stat->group_name_alt ?></span><span style="float:right"><?php echo $stat->group_total; ?> </span></p>
                                    </div>

                                    <?php } 
                                    
                                }?>









                                
                                </div>
                            </div>
                            
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12 chart-panel">
                <div class="chart-panel-content">
                    <div class="chart-panel-container">
                        <div class="chart-panel-top">
                            <h4 class="chart-panel-title">ASSET INSPECTIONS DUE</h4>
                        </div>

                        <?php


                        if(false){
                            echo "<p class='nodata-message'>There is no data avaliable!</p>";

                        } else { 
                            ?>
                            <div class="chart-panel-body">

                            <?php
                            if($eol_full_replacement->status){ ?>
                                <div class="status-table" style="padding:20px;">
                                    <table class="sortable" style="width:100%;text-align: center;;font-size:18px;">
                                        <tr>
                                            <th class="text-center">WHEN</th>
                                            <th class="text-center">ASSETS</th>
                                            <th class="text-center">BUILDINGS</th>
                                            <th class="text-center">ACTIVITIES</th>
                                            <th class="text-center">REMAINING %</th>
                                        </tr>
                                        <?php
                                                foreach($eol_full_replacement->asset_stats as $asset_stat){
                                                        ?>
                                                    <tr>
                                                        <td style="border-bottom: 2px solid <?php echo $asset_stat->eol_group_colour; ?> !important;"><?php echo $asset_stat->eol_group_text_alt; ?></td>
                                                        <td style="border-bottom: 2px solid <?php echo $asset_stat->eol_group_colour; ?>  !important;"><?php echo $asset_stat->eol_group_total; ?></td>
                                                        <td style="border-bottom: 2px solid <?php echo $asset_stat->eol_group_colour; ?>  !important;">0</td>
                                                        <td style="border-bottom: 2px solid <?php echo $asset_stat->eol_group_colour; ?>  !important;">0</td>
                                                        <td style="border-bottom: 2px solid <?php echo $asset_stat->eol_group_colour; ?>  !important;">42%</td>
                                                    </tr>
                                                    <?php
                                                }
                                        ?>
                                    </table>
                                </div>

                            <?php
                            } else {
                                echo "<p class='nodata-message'>There is no data avaliable!</p>";
                            }

                            ?>


                            </div>


                        <?php } ?>

                        </div>
                            
                    </div>
            </div>
            



    
        </div>

        <div class="container">

            <div class="row">

                <div class="col-lg-6 col-md-6 col-sm-12 chart-panel">
                    <div class="chart-panel-content">
                        <div class="chart-panel-container">
                            <div class="chart-panel-top">
                                <h4 class="chart-panel-title">END OF LIFE AND REPLACEMENT COST</h4>
                            </div>
                            
                            <div class="chart-panel-body">

                                <?php
                                if($eol_full_replacement->status){ ?>
                                    <div class="status-table" style="padding:20px;">
                                        <table class="sortable" style="width:100%;text-align: center;;font-size:18px;">
                                            <tr>
                                                <th class="text-center">WHEN</th>
                                                <th class="text-center">ASSETS</th>
                                                <th class="text-center">BUILDINGS</th>
                                                <th class="text-center">REPLACEMENT COST</th>
                                            </tr>
                                            <?php
                                                    foreach($eol_full_replacement->asset_stats as $asset_stat){
                                                            ?>
                                                        <tr>
                                                            <td style="border-bottom: 2px solid <?php echo $asset_stat->eol_group_colour; ?> !important;"><?php echo $asset_stat->eol_group_text_alt; ?></td>
                                                            <td style="border-bottom: 2px solid <?php echo $asset_stat->eol_group_colour; ?>  !important;"><?php echo $asset_stat->eol_group_total; ?></td>
                                                            <td style="border-bottom: 2px solid <?php echo $asset_stat->eol_group_colour; ?>  !important;">0</td>
                                                            <td style="border-bottom: 2px solid <?php echo $asset_stat->eol_group_colour; ?>  !important;">0</td>
                                                        </tr>
                                                        <?php
                                                    }
                                            ?>
                                        </table>
                                    </div>

                                <?php
                                } else {
                                    echo "<p class='nodata-message'>There is no data avaliable!</p>";
                                }

                                ?>


                            </div>
                                
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-12 chart-panel">
                    <div class="chart-panel-content">
                        <div class="chart-panel-container">
                            <div class="chart-panel-top">
                                <h4 class="chart-panel-title">WEEKS SLA'S RATE MET</h4>
                            </div>
                            
                            <div class="chart-container" style="width:calc(100% - 40px);height: 240px;margin-top:60px;margin-bottom:10px">
                                    <canvas id="week-sla-rate" style="height:180px" height="180px"></canvas>
                            </div>
                                
                        </div>
                    </div>
                </div>


                <div class="col-lg-3 col-md-3 col-sm-12 chart-panel">
                    <div class="chart-panel-content">
                        <div class="chart-panel-container chart-panel-container-half" >
                            <div class="chart-panel-top">
                                <h4 class="chart-panel-title">CONTRACT COST</h4>
                            </div>
                            
                            <h1 style='text-align:center;margin: 43px;'>£100.00</h1>
                                
                        </div>
                    </div>

                    <div class="chart-panel-content"  style="margin-top: 20px !important;">
                        <div class="chart-panel-container chart-panel-container-half">
                            <div class="chart-panel-top">
                                <h4 class="chart-panel-title">REPLACEMENT ASSET COST</h4>
                            </div>
                            
                            <div class="chart-panel-body panel-half">
                                <?php if($replacement_cost['status']){
                                    echo "<h1 style='text-align:center;margin: 43px;'>£" . $replacement_cost["replacement_cost"] . "</h1>";
                                } else {
                                    echo "<p class='nodata-message'>There is no data avaliable!</p>";
                                }
                                ?>
                                
                            </div>
                                
                        </div>
                    </div>
                </div>

            </div>
        </div>


    </div>



    <script>






    </script>

</body>

