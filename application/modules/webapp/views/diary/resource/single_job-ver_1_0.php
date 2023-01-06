<head>
    <script src="https://momentjs.com/downloads/moment.min.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url( 'assets/css/custom/site_styles.css' ); ?>" media="screen">
</head>

<body>

<script>
// load available resource into javascript memory
avaliable_resource = <?php echo json_encode($available_resource); ?>
</script>

<style type="text/css">
.connectedSortable{
	min-height: 100%;
}

.v-aligned{
	vertical-align: middle;
	display:table-cell;
}

table tr td.resource-td-cell{
	width: 14%;
	text-align: left;
	vertical-align: top;
}

.item-container{
	margin-bottom: 15px;
}

.resource-th-cell{
	width: 14%;
	text-align: center;
	text-transform: uppercase;
}

.accordion .panel{
	margin-bottom: 15px !important;
}
.booked-job{
	text-align: left;
}

.job-record{
	padding: 10px;
}

.travel-details{
	padding: 0px 0;
	font-size: 80%;
}

.panel-title-extra-info{
	margin-top: 8px;
}

.panel-header-info{
	color: #fff !important;
	font-weight: 500;
}

/*
div.panel-body-overflow{
	overflow-y: auto;
	overflow-x: auto;
	height: 30vh;
	padding-top: 0;
}
*/
.user-element{
	padding: 0px 10px 5px;
}

.slider{
	width: 98%;
	/*margin: 0px auto;*/
}

.resource-container{
	color: #5c5c5c;
}

.panel-group{
	min-height:60vh;
	padding:10px;
}

.panel-group .panel-heading+.panel-collapse>.list-group,
.panel-group .panel-heading+.panel-collapse>.panel-body{
	border: none;
}

.btn-sm{
	text-align: center !important;
	padding: 2px !important;
	width: 100%:
}

#myModal{
	font-weight: 0;
}

#map{
	width: 100%;
	height: 500px;
}

.user-interact{
	display: flex;
	flex-wrap: nowrap;
}

.flex-btn{
	padding: 2px;
	width: 100%;
	text-align: center;
}
</style>

<?php $week_days = [ 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' ]; ?>

<div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title" id="exampleModalLabel">Job Route</h2>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div id="map"></div>
				<script type="text/javascript">
					var directionsService;
					var directionsDisplay;
				</script>
				<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDKbrViiSEEynq_eYpFlOmXXErTCQL8Mqs&callback=initMap"></script>
			</div>
			<div class="modal-footer">
				<button id="map-commit" type="button" class="btn btn-success" user_commit_id="">Commit</button>
			</div>
		</div>
	</div>
</div>



<?php
$job_location_summaryline = false;

$main_address_found = false;
if( !empty( $job_details->customer_details->addresses ) ){
	foreach( $job_details->customer_details->addresses as $address ){
		if( strtolower( $address->address_type_group ) == "main" && ( ( !empty( $address->address_line1 ) && ( $address->address_town ) ) || !empty( ( $address->address_postcode ) ) ) ){
			$job_location_summaryline .= ( !empty( $address->address_line1 ) ? $address->address_line1.' ' : '' );
			$job_location_summaryline .= ( !empty( $address->address_line2 ) ? $address->address_line2.' ' : '' );
			$job_location_summaryline .= ( !empty( $address->address_town ) ? $address->address_town.' ' : '' );
			$job_location_summaryline .= ( !empty( $address->address_postcode ) ? $address->address_postcode.' ' : '' );
			$main_address_found = true;
		}
	}
}

if( !$main_address_found ){
	$job_location_summaryline .= ( !empty( $job_details->summaryline ) ) ? $job_details->summaryline : 'NO POSTCODE AVAILABLE';
}
?>


<!-- JOB HEADER -->
<div class="row">
	<div class="col-md-4 col-sm-6 col-xs-12" >
		<div class="row">
			<?php if( !empty( $job_details ) ) { ?>
				<div class="col-md-6 col-sm-12 col-xs-12" ><h3>Booking Job <br><a target = "_blank" href="<?php echo base_url( '/webapp/job/profile/'.$job_details->job_id ); ?>" ><?php echo $job_details->job_type; ?></a><br><em style="font-size:55%" >(Drag and drop the box to the person)</em></h3></div>
				<div class="col-md-6 col-sm-12 col-xs-12" >
					<div class="sortable-list connectedSortable" style="border: 0.5px dashed #ccc; min-height:65px;" id="jobs-origin" >
						<div class="job-element job-curr pointer text-white" id="job-<?php echo $job_details->job_id; ?>" job_id = "<?php echo $job_details->job_id; ?>" job_routing_id = "<?php echo ( !empty( $job_details->job_id ) ) ? $job_details->job_id : '' ; ?>" job_slots="<?php echo $job_details->job_duration; ?>" job_name="<?php echo lean_string( $job_details->job_type ); ?>" job_description="<?php echo $job_details->job_type; ?>" job_location="<?php echo ( !empty( $job_location_summaryline ) ) ? $job_location_summaryline : '' ; ?>">
							<div class="job-header strong">
								<h4>
									<span class="job-el-type pull-left"><?php echo ( !empty( $job_details->job_type ) ? $job_details->job_type : false ); ?></span>
									<span class="job-el-duration pull-right"><?php echo ( !empty( $job_details->job_duration ) ? $job_details->job_duration : false ); echo " ".( ( $job_details->job_duration > 1 ) ? "hours" : "hour" ); ?></span>
								</h4>
                            </div>
							<div class="job-details small pull-left" >
								<span class="pull-left job-postcode"><?php echo ( !empty( $job_location_summaryline ) ? $job_location_summaryline : '' ); ?></span>
							</div>
						</div>
					</div>
                </div>

			<?php } else { ?>
				<div class = "col-md-12" >
					<span>There's currently no date matching your criteria</span>
				</div>
            <?php } ?>

		</div>
	</div>

	<div class="col-md-4 col-sm-6 col-xs-12" >
		<div class="row margin-top-15" >


            <script>
                datePickerStart = "<?php echo date( 'd-m-Y' ) ?>"
                datePickerEnd = "<?php echo date( 'd-m-Y', strtotime( 'sunday this week' ) ) ?>"

            </script>
			<form id="date-range-form" method="post">



                <div class="col-md-4 col-sm-5 col-xs-5" >
                    <span class="pull-left" ></span></span><input type="text" name="date_from" class="form-control datepicker-start" value="<?php echo !empty( $range['date_from']) ? $range["date_from"] : '';?>" placeholder="Week of" /></span>
                </div>

                <div class="col-md-4 col-sm-5 col-xs-5" >
                    <input type="text" class="form-control datepicker-end" name="date_to" value="<?php echo !empty( $range['date_to']) ? $range["date_to"] : ''; ?>" placeholder="Week ending" /></span><span></span>
                </div>


            </form>



		</div>
	</div>
    <p style="text-align:right">(Users with no remaining hours left will be hidden from view)</p>

	<div class="col-md-4 col-sm-12 col-xs-12" >
		<div class="row margin-top-15" >
                    <div class="col-md-10 col-sm-12 col-xs-12 form-group top_search right" >
                    <div class="input-group" style="width: 100%;">
                        <i class="fas fa-search" style="position: absolute; top: 8px; left: 20px; color: #fff; width: 28px; height: 28px; z-index: 99; font-size: 18px;"></i>
                        <style>
                            ::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
                                color: white !important;
                            }

                            ::-webkit-input-placeholder { /* Edge */
                                color: white !important;
                            }

                            :-ms-input-placeholder { /* Internet Explorer 10-11 */
                                color: white !important;
                            }

                            ::placeholder {
                                color: white !important;
                            }

                        </style>
                        <input type="text" class="form-control site-search_input" id="search_term" value="" placeholder="Search Engineers" style="text-indent: 32px;color:white;">
                    </div>
			</div>
		</div>
    </div>

	<div class="col-md-12" >
		<legend>&nbsp;</legend>
	</div>
</div>

<style>

	.routing-main-panel {
		width: calc(100% - 100px);
		display: inline-block;
        margin-left: 50px;
	}

	.routing-panel-nav {
		display: inline-block;
		width: 25px !important;
		position: absolute;
	}

	.week-container {
		overflow-x: auto !important;
    overflow-y: auto !important;
	}

    .panel-group {
        min-width: 220px !important;
    }


</style>


<!-- AVAILABLE RESOURCE -->
<div class="row routing-details">
    <div id="routing-next"  class="routing-panel-nav" style="z-index:1000"><i class="fas fa-chevron-circle-left fa-2x"></i></div>
    <div id="routing-prev" class="routing-panel-nav" style="right:15px"><i class="fas fa-chevron-circle-right fa-2x"></i></div>
	<div class="routing-main-panel carousel slide" id="myCarousel" data-ride="carousel" data-interval="false" data-wrap="false">
		<section  class="carousel-inner">
            <!-- <section class="regular slider">-->
			<?php if( !empty( $available_resource ) ) { foreach ( $available_resource as $week_no => $resource_data ){ ?>
				<!-- START DATA LOOP -->

				<div id="diary-results-<?php echo $week_no; ?>" class="resource-container <?php  echo ($week_no == 'week_1') ? 'item active' : 'item' ?>">
                    <div class="week-container carousel-inner" width="width:100%" >
						<table class="table-responsive" style="width:100%;margin-top:10px;" >
							<tr class="resource-tr-cell">
								<th class="resource-th-cell"  ><span class="week-mon" ><?php echo !empty( $resource_data->monday ) 		? date( 'D d', strtotime( array_keys( object_to_array( $resource_data->monday ) )[0] ) )	: 'Mon'; ?></span> <span class="week-mon-no-operatives" ><?php echo !empty($resource_data->monday) ? "(" . count((array)array_values((array)$resource_data->monday)[0]) . ")" : "(0)"; ?></span></th>
								<th class="resource-th-cell"  ><span class="week-tue" ><?php echo !empty( $resource_data->tuesday ) 	? date( 'D d', strtotime( array_keys( object_to_array( $resource_data->tuesday ) )[0] ) )	: 'Tue'; ?></span> <span class="week-mon-no-operatives" ><?php echo !empty($resource_data->tuesday) ? "(" . count((array)array_values((array)$resource_data->tuesday)[0]) . ")" : "(0)"; ?></span></th>
								<th class="resource-th-cell"  ><span class="week-wed" ><?php echo !empty( $resource_data->wednesday )	? date( 'D d', strtotime( array_keys( object_to_array( $resource_data->wednesday ) )[0] ) )	: 'Wed'; ?></span> <span class="week-mon-no-operatives" ><?php echo !empty($resource_data->wednesday) ? "(" . count((array)array_values((array)$resource_data->wednesday)[0]) . ")" : "(0)"; ?></span></th>
								<th class="resource-th-cell"  ><span class="week-thu" ><?php echo !empty( $resource_data->thursday ) 	? date( 'D d', strtotime( array_keys( object_to_array( $resource_data->thursday ) )[0]	) )	: 'Thu'; ?></span> <span class="week-mon-no-operatives" ><?php echo !empty($resource_data->thursday) ? "(" . count((array)array_values((array)$resource_data->thursday)[0]) . ")" : "(0)"; ?></span></th>
								<th class="resource-th-cell"  ><span class="week-fri" ><?php echo !empty( $resource_data->friday ) 		? date( 'D d', strtotime( array_keys( object_to_array( $resource_data->friday )	)[0] ) )	: 'Fri'; ?></span> <span class="week-mon-no-operatives" ><?php echo !empty($resource_data->friday) ? "(" . count((array)array_values((array)$resource_data->friday)[0]) . ")" : "(0)"; ?></span></th>
								<th class="resource-th-cell"  ><span class="week-sat" ><?php echo !empty( $resource_data->saturday ) 	? date( 'D d', strtotime( array_keys( object_to_array( $resource_data->saturday ) )[0]	) )	: 'Sat'; ?></span> <span class="week-mon-no-operatives" ><?php echo !empty($resource_data->saturday) ? "(" . count((array)array_values((array)$resource_data->saturday)[0]) . ")" : "(0)"; ?></span></th>
								<th class="resource-th-cell"  ><span class="week-sun" ><?php echo !empty( $resource_data->sunday ) 		? date( 'D d', strtotime( array_keys( object_to_array( $resource_data->sunday )	)[0] ) )	: 'Sun'; ?></span> <span class="week-mon-no-operatives" ><?php echo !empty($resource_data->sunday) ? "(" . count((array)array_values((array)$resource_data->sunday)[0]) . ")" : "(0)"; ?></span></th>
							</tr>

							<tbody>
								<tr>
									<!-- LOOP THROUGH THE DAYS OF THE WEEK -->
								<?php 	foreach( array_map( 'strtolower', $week_days ) as $day ){ 
											$week_day_ref = $day.'_'.$week_no; ?>

										<td class="resource-td-cell" style="min-width:150px !important;">
											<div class="item-container panel-group no-border" id="accordion_<?php echo $week_day_ref; ?>" >
                                                <?php if( !empty( $resource_data->{$day} ) ) { foreach( $resource_data->{$day} as $date => $personnel_data ){
													foreach( $personnel_data as $person_id => $personnel_data ) { 
														$actual_slots 	= !empty( $personnel_data->availability->actual_slots )   ? $personnel_data->availability->actual_slots : 0;
														$consumed_slots = !empty( $personnel_data->availability->consumed_slots ) ? $personnel_data->availability->consumed_slots : 0;
														$available_slots= $actual_slots - $consumed_slots; ?>

														<div class="user-element panel panel-default no-radius bg-grey" id="user-<?php echo $week_day_ref.'_'.$person_id ?>" user_id ="<?php echo $person_id; ?>" user_fullname="<?php echo $personnel_data->person; ?>" user_address="<?php echo $personnel_data->home_address; ?>" user_slots="<?php echo $available_slots; ?>" user_element_date = "<?php echo $personnel_data->availability->ref_date; ?>" day_ref = "<?php echo $day ?>" week_num_ref = "<?php echo $week_no; ?>">

															<div class="user-header align-left panel-header-info panel-heading bg-grey pointer no-radius accordion-toggle collapsed" style="padding:0;" data-toggle="collapse" data-parent="#accordion_<?php echo $week_day_ref; ?>" href="#accordion_<?php echo $week_day_ref.'_'.$person_id; ?>">
																<h5 class="user_name"><i class="fas fa-caret-down fa-md text-yellow show-more"></i>&nbsp;<?php echo $personnel_data->person; ?><span style="float:right"><?php echo $personnel_data->home_postcode; ?></span></h5>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <h5>Total</h5>
                                                                        <p id="user-<?php echo $week_day_ref.'_'.$person_id ?>-total"><?php echo ( $available_slots ); ?> hour<?php echo ( in_array( ( $available_slots ), ['1','-1'] ) ) ? '' : 's'; ?></p>
                                                                    </div>
                                                                    <div class="col-md-6" style="text-align:right">
                                                                        <h5>Remaining</h5>
                                                                        <p id="user-<?php echo $week_day_ref.'_'.$person_id ?>-remaining"></p>
                                                                    </div>

                                                                </div>
                                                            </div>

															<div id="accordion_<?php echo $week_day_ref.'_'.$person_id; ?>" class="user-more-info panel-collapse collapse no-radius" >
																<hr>
																<div id="user-<?php echo $week_day_ref.'_'.$person_id; ?>-jobs" user_id = "<?php echo $person_id; ?>" style="margin-top:-30px;min-height:40px !important;" class="sortable-list connectedSortable user-jobs"><br />
																<?php 
                                                                    if( !empty( $personnel_data->availability->booked_jobs ) ){ foreach( $personnel_data->availability->booked_jobs as $k => $av_job_details ) { ?>
                                                                        <?php 
																			$hours 		= floor( $av_job_details->job_travel_time / 3600 );
                                                                            $minutes 	= floor( ( $av_job_details->job_travel_time / 60 ) % 60 );
                                                                            if( $hours > 0 ){
                                                                                $pretty_travel_time = $hours . " hours / " . $minutes . " mins" ;
                                                                            } else {
                                                                                $pretty_travel_time = $minutes . " mins";
                                                                            } ?>
																			
																			
																	<?php	$av_job_location_summaryline = false;
																			$main_address_found = false;
																			if( !empty( $av_job_details->customer_details->addresses ) ){
																				foreach( $av_job_details->customer_details->addresses as $address ){
																					if( strtolower( $address->address_type_group ) == "main" && ( ( !empty( $address->address_line1 ) && ( $address->address_town ) ) || !empty( ( $address->address_postcode ) ) ) ){
																						$av_job_location_summaryline .= ( !empty( $address->address_line1 ) ? $address->address_line1.' ' : '' );
																						$av_job_location_summaryline .= ( !empty( $address->address_line2 ) ? $address->address_line2.' ' : '' );
																						$av_job_location_summaryline .= ( !empty( $address->address_town ) ? $address->address_town.' ' : '' );
																						$av_job_location_summaryline .= ( !empty( $address->address_postcode ) ? $address->address_postcode.' ' : '' );
																						$main_address_found = true;
																					}
																				}
																			}

																			if( !$main_address_found ){
																				$av_job_location_summaryline .= ( !empty( $av_job_details->summaryline ) ) ? $av_job_details->summaryline : 'NO POSTCODE AVAILABLE';
																			} ?>
																			
																			
																			
																			
																		<div class="travel-information-element static-sortable" distance="<?php echo $av_job_details->job_travel_distance; ?>" duration="<?php echo $av_job_details->job_travel_time; ?>"><?php echo round((0.000621371 * $av_job_details->job_travel_distance), 2); ?> miles / <?php echo $pretty_travel_time; ?> </div>
																		<div class="job-element lock-to-sortable pointer text-white" id="job-<?php echo $av_job_details->job_id; ?>" job_id = "<?php echo $av_job_details->job_id; ?>" job_slots="<?php echo $av_job_details->job_duration; ?>" job_name="<?php echo lean_string( $av_job_details->job_type ); ?>" job_description="<?php echo $av_job_details->job_type; ?>" job_location="<?php echo $av_job_location_summaryline; ?>" lock_to_container = "user-<?php echo $week_day_ref.'_'.$person_id; ?>-jobs">
																			<div class="job-header strong">
																				<h4>
																					<span class="job-el-type pull-left"><?php echo ( !empty( $av_job_details->job_type ) ? $av_job_details->job_type : false ); ?></span>
																					<span class="job-el-duration pull-right"><?php echo ( !empty( $av_job_details->job_duration ) ? $av_job_details->job_duration : false ); echo " ".( ( $av_job_details->job_duration > 1 ) ? "hours" : "hour" ); ?></span>
																				</h4>
																			</div>
																			
																			
																			<div class="job-details small pull-left">
																				<span class="pull-left"><?php echo $av_job_location_summaryline; ?></span>
																			</div>
                                                                        </div>

                                                                        <?php
                                                                            if( $av_job_details->is_last_job ){

                                                                                $hours = floor($av_job_details->job_travel_time_home / 3600);
                                                                                $minutes = floor(($av_job_details->job_travel_time_home / 60) % 60);

                                                                                if($hours > 0){
                                                                                    $pretty_travel_time = $hours . " hours / " . $minutes . " mins" ;
                                                                                } else {
                                                                                    $pretty_travel_time = $minutes . " mins";
                                                                                }

                                                                                //echo "<div class='travel-information-element'>last travel info here</div>";

                                                                                echo "<div class='travel-information-element static-sortable' distance='" . $av_job_details->job_travel_distance_home . "' duration='" . $av_job_details->job_travel_time_home . "'>" . round((0.000621371 * $av_job_details->job_travel_distance_home), 2) . " miles / " . $pretty_travel_time .  "</div>";
                                                                            }
																			//if($av_job_details->is_last_job){
                                                                            //echo "<div class='travel-information-element'>last travel info here</div>";
                                                                            /*echo "<div class='travel-information-element static-sortable' distance='<?php echo $av_job_details->job_travel_distance; ?>' duration='<?php echo $av_job_details->job_travel_time; ?>'><?php echo round((0.000621371 * $av_job_details->job_travel_distance), 2); ?> miles / <?php echo $pretty_travel_time; ?> </div>";
*/	                                                                        //}
                                                                        ?>
																	<?php } } else { ?>
																		<div>
																		</div>
																	<?php } ?>
                                                                </div>

																<br/>
																<table style="width:100%; font-size:90%">
																	<tbody id="user-<?php echo $week_day_ref.'_'.$person_id ?>-travel-info" class="total-info-content">
																		<tr>
																			<td ><span class="pull-left" style="font-size:10px">Total Mileage</span></td>
																			<td><span class = "pull-right travel-info-content" id="user-<?php echo $week_day_ref.'_'.$person_id ?>-total-mileage" value="0">0 miles</span></td>
																		</tr>
																		<tr>
																			<td ><span class="pull-left" style="font-size:10px">Total Travel Time</span></td>
																			<td><span class = "pull-right travel-info-content" id="user-<?php echo $week_day_ref.'_'.$person_id ?>-total-travel-time" value="0">0 mins</span></td>
																		</tr>
																		<tr>
																			<td ><span class="pull-left" style="font-size:10px">Total Work Time</span></td>
																			<td><span class = "pull-right travel-info-content" id="user-<?php echo $week_day_ref.'_'.$person_id ?>-total-work-time" value="0">0 mins</span></td>
																		</tr>
																		<tr>
																			<td ><span class="pull-left" style="font-size:10px">Total Work Travel Time</span></td>
																			<td><span class = "pull-right travel-info-content" id="user-<?php echo $week_day_ref.'_'.$person_id ?>-total-work-travel-time" value="0">0 mins</span></td>
																		</tr>
																		<tr>
																			<td ><span class="pull-left" style="font-size:10px">Total Time</span></td>
																			<td><span class = "pull-right travel-info-content" id="user-<?php echo $week_day_ref.'_'.$person_id ?>-total-time" value="0">0 mins</span></td>
																		</tr>
																   </tbody>
																</table>
                                                                <hr>
																<div class="user-interact">
																		<div class="user-commit-jobs flex-btn" title="Commit Jobs" ><span class="commit btn-sm btn-block btn-success no-radius no-shadow" ><i class="fas fa-check"></i></span></div>
																		<div class="user-remove-jobs flex-btn"  title="Remove Jobs" ><span class="commit btn-sm btn-block btn-danger no-radius no-shadow" ><i class="fas fa-times"></i></span></div>
                                                                        <div class="user-view-route flex-btn"  title="View Route" ><span class="commit btn-sm btn-block btn-warning no-radius no-shadow" ><i class="fas fa-map-marker-alt"></i></span></div>
                                                                        <div class="user-optimize-waypoints flex-btn" flex-btn  title="Optimize Route" ><span class="commit btn-sm btn-block btn-info no-radius no-shadow" ><i class="fas fa-star"></i></span></div>
                                                                </div>
                                                                <br/>
                                                            </div>
														</div>
													<?php } ?>
												<?php } } ?>
											</div>
										</div>
									<?php } ?>

								</tr>
							</tbody>
						</table>
                    </div>

                </div>

            <?php } } else { ?>
				<div class="week-container" width="width:100%" >
					<table class="table-responsive" style="width:100%" >
						<tr class="resource-tr-cell"  >

                        </tr>
						<tbody id="diary-results-week-00">
							<tr>
								<td colspan="7" ><br/><h4>No data available to display</h4></td>
							</tr>
						</tbody>
					</table>
				</div>
			<?php } ?>
        </section>

	</div>
</div>


    <script type="text/javascript">
        $( document ).on( 'ready', function() {

            $( "#routing-prev" ).click(function() {
                $("#myCarousel").carousel('next')
            });

            $( "#routing-next" ).click(function() {
                $("#myCarousel").carousel('prev')
            });


            $(".user-element").each(function( index, user_element) {
                updateUserTotals( $(user_element).attr("id") )
            });



            $('.datepicker-start').datetimepicker({
			    timepicker:false,
                format:'Y-m-d',
                minDate: 0,
                onChangeDateTime:function(dp,$input){

                    start = $(".datepicker-start").val()

                    $(".datepicker-end").val(moment(start).add(7, 'days').format('YYYY-MM-DD'))

                }
            });

            $('.datepicker-end').datetimepicker({
			    timepicker:false,
                format:'Y-m-d',
                minDate: 0,
                onChangeDateTime:function(dp,$input){

                    start = moment($(".datepicker-start").val().split("-").reverse().join("-"));
                    end = moment($(".datepicker-end").val().split("-").reverse().join("-"));

                    if(start > end){
                        Swal.fire(
                            'Error!',
                            'The end date cannot be before the start date!',
                            'error'
                        )
                        $(".datepicker-end").val(start.add(7, 'days').format('YYYY-MM-DD'))
                    } else {
                        $("#date-range-form").submit()
                    }
                }
            });
		});



        function commitJobUserBatch(user_element){

            var userID			= user_element.attr( "user_id" );
			var jobRoutingID	= user_element.find( ".job-element" ).attr( "job_routing_id" );
            var job_Batch 		= {};
            var i = 1;

            assigned_to = $( user_element ).attr( "user_id" )
            job_date = $( user_element ).attr( "user_element_date" )

            travel_elements = $(user_element).find( ".travel-information-element" )
            job_elements = $( user_element ).find( ".job-element" )


            $(job_elements).each( function(i, job_element){
                job_id = $( job_element ).attr( "job_id" )

                if(i !== job_elements.length - 1){
                    job_Batch[ job_id ] = {
                        'job_id' : job_id,
                        'job_order' : i,
                        'assigned_to' : assigned_to,
                        'job_date' : job_date,
                        'is_last_job': 0,
                        'job_travel_time' : $( travel_elements[ i ]).attr( "duration" ),
                        'job_travel_distance' : $( travel_elements[ i ] ).attr( "distance" ),
                    };

                } else {
                    job_Batch[ job_id ] = {
                        'job_id' : job_id,
                        'job_order' : i,
                        'assigned_to' : assigned_to,
                        'job_date' : job_date,
                        'is_last_job': 1,
                        'job_travel_time' : $( travel_elements[ i ]).attr( "duration" ),
                        'job_travel_distance' : $( travel_elements[ i ] ).attr( "distance" ),
                        'job_travel_time_home' : $( travel_elements[ travel_elements.length - 1 ]).attr( "duration" ),
                        'job_travel_distance_home' : $( travel_elements[ travel_elements.length - 1 ]).attr( "distance" ),
                    };
                }
            });

            if( job_Batch ){
                $.ajax({
                    url:"<?php echo base_url( 'webapp/diary/commit_jobs' ); ?>",
                    method: "POST",
                    data:{ jobBatch: job_Batch },
                    success: function( data ){
                        var newData = JSON.parse( data );
                        if( newData.status == true || newData.status == 1 || newData.status == 0){
                            swal({
                                type: 'success',
                                title: newData.status_msg,
                                showConfirmButton: false,
                                timer: 3000
                            })

                            window.setTimeout( function(){
                                location.href = "<?php echo base_url( 'webapp/job/profile/' ); ?>" + <?php echo $job_id; ?>;
                            }, 3000 );
                        } else {
                            swal({
                                type: 'error',
                                title: newData.status_msg
                            })
                        }
                    }
                });

            }

        }




    </script>






</body>
