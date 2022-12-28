<head>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url( '/assets/js/slick/slick.css' ) ?>" >
    <link rel="stylesheet" type="text/css" href="<?php echo base_url( '/assets/js/slick/slick-theme.css' ) ?>" >
    
</head>
<body>
<style>
	.connectedSortable {
		min-height: 100%;
	}
	
	.v-aligned{
		vertical-align: middle;
		display:table-cell;    
	}
	
	table tr td.resource-td-cell{
		width: 14%;
		text-align: left;		
	}
	
	.item-container{
		margin-bottom: 15px;
	}
	
	.resource-th-cell{
		width: 14%;
		text-align: center;
		text-transform: uppercase;
	}
	
	.accordion .panel {
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

	div.panel-body-overflow{
		overflow-y: auto;
		height: 30vh;
		padding-top: 0;
	}
	
	/** EXTRA RE-STLES**/	
	.user-element {
		padding: 0px 10px 5px;
		/*margin-bottom: 15px !important;*/
	}
</style>

<style type="text/css">

    .slider {
        width: 98%;
        /*margin: 0px auto;*/
    }

    .slick-slide {
		margin: 0px 15px;
		min-height: 250px;;
    }

    .slick-slide img {
		width: 100%;
    }

    .slick-prev:before,
    .slick-next:before {
		color: black;
    }


    .slick-slide {
		transition: all ease-in-out .3s;
		opacity: .2;
    }
    
    .slick-active {
		opacity: .5;
    }

    .slick-current {
		opacity: 1;
    }
	
	.resource-container{
		color: #5c5c5c;
        /*padding: 10px;*/
        overflow-y: auto;
	}
	
	.panel-group
	{
		height:60vh;
		padding:10px;		
	}
	
	.panel-group .panel-heading+.panel-collapse>.list-group, 
	.panel-group .panel-heading+.panel-collapse>.panel-body {
		border: none;		
	}
	
</style>


<?php $week_days = [ 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' ]; ?>

<style>

#myModal {

    font-weight: 0;
}

#map {

    width: 100%;
    height: 500px;
}

</style>





<!-- Modal -->
<div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="exampleModalLabel">Job Route</h2>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

      <div id="map"></div>

      <script>
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





<!-- JOB HEADER -->
<div class="row">

	<div class="col-md-4 col-sm-6 col-xs-12" >
		<div class="row">
			<?php if( !empty( $job_details ) ) { ?>
				<div class="col-md-6 col-sm-12 col-xs-12" ><h3>Booking Job (<a target = "_blank" href="<?php echo base_url( '/webapp/job/profile/'.$job_details->job_id ); ?>" ><?php echo $job_details->job_id; ?></a>)<br><em style="font-size:55%" >(Drag and drop the box to the person)</em></h3></div>
				<div class="col-md-6 col-sm-12 col-xs-12" >
					<div class="sortable-list connectedSortable" style="border: 0.5px dashed #ccc; min-height:65px;" id="jobs-origin" >
						<div class="job-element job-curr pointer text-white" id="job-<?php echo $job_details->job_id; ?>" job_id = "<?php echo $job_details->job_id; ?>" job_slots="<?php echo $job_details->job_duration; ?>" job_name="<?php echo lean_string( $job_details->job_type ); ?>" job_description="<?php echo $job_details->job_type; ?>" job_location="<?php echo $job_details->summaryline; ?>">
							<div class="job-header strong">
								<h4>
									<span class="job-el-type pull-left"><?php echo ( !empty( $job_details->job_type ) ? $job_details->job_type : false ); ?></span>
									<span class="job-el-duration pull-right"><?php echo ( !empty( $job_details->job_duration ) ? $job_details->job_duration : false ); echo " ".( ( $job_details->job_duration > 1 ) ? "hours" : "hour" ); ?></span>
								</h4>
                            </div>
							<div class="job-details small pull-left" >
								<span class="pull-left"><?php echo ( !empty( $job_details->summaryline ) ? $job_details->summaryline : '<span class="text-text">NO POSTCODE AVAILABLE</span>&nbsp;' ); ?></span>
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
			<div class="col-md-1 col-md-offset-1 col-sm-1 col-xs-1" >
				<a class="right carousel-control" href="#myCarousel" data-slide="prev"><i class="fas fa-caret-left text-yellow"></i></a>
			</div>
			
			<div class="col-md-4 col-sm-5 col-xs-5" >
				<span class="pull-left" ></span></span><input type="text" class="form-control datepicker" value="<?php echo date( 'd-m-Y', strtotime( 'monday this week' ) ) ?>" placeholder="Week of" /></span>
			</div>
			
			<div class="col-md-4 col-sm-5 col-xs-5" >
				<input type="text" class="form-control datepicker" value="<?php echo date( 'd-m-Y', strtotime( 'sunday this week' ) ) ?>" placeholder="Week ending" /></span><span></span>
			</div>
			
			<div class="col-md-1 col-sm-1 col-xs-1" >
				<a class="carousel-control" href="#myCarousel" data-slide="prev"><i class="fas fa-caret-right text-yellow"></i></a>
			</div>
		</div>
	</div>
	
	<div class="col-md-4 col-sm-12 col-xs-12" >
		<div class="row margin-top-15" >
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="black-box shaded">
					<input type="text" class="form-control <?php echo $module_identier; ?>-search_input" id="search_term" value="" placeholder="Search Operatives" />
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-12" >
		<legend>&nbsp;</legend>
	</div>
</div>

<!-- AVAILABLE RESOURCE -->
<div class="row routing-details" style="margin-left:10px;">
	<div class="col-md-12 col-sm-12 col-sm-12" >
		<section class="regular slider">
			<?php if( !empty( $available_resource ) ) { foreach ( $available_resource as $week_no => $resource_data ){ ?>
				<!-- START DATA LOOP -->
				
				<div id="diary-results-<?php echo $week_no; ?>" class="resource-container" >
					<div class="week-container" width="width:100%" >
						<table class="table-responsive" style="width:100%" >
							
							<tr class="resource-tr-cell"  >
								<th class="resource-th-cell"  ><span class="week-mon" ><?php echo !empty( $resource_data->monday ) 		? date( 'D d', strtotime( array_keys( object_to_array( $resource_data->monday ) )[0] ) )	: 'Mon'; ?></span> <span class="week-mon-no-operatives" >(2)</span></th>															
								<th class="resource-th-cell"  ><span class="week-tue" ><?php echo !empty( $resource_data->tuesday ) 	? date( 'D d', strtotime( array_keys( object_to_array( $resource_data->tuesday ) )[0] ) )	: 'Tue'; ?></span> <span class="week-mon-no-operatives" >(12)</span></th>															
								<th class="resource-th-cell"  ><span class="week-wed" ><?php echo !empty( $resource_data->wednesday )	? date( 'D d', strtotime( array_keys( object_to_array( $resource_data->wednesday ) )[0] ) )	: 'Wed'; ?></span> <span class="week-mon-no-operatives" >(0)</span></th>															
								<th class="resource-th-cell"  ><span class="week-thu" ><?php echo !empty( $resource_data->thursday ) 	? date( 'D d', strtotime( array_keys( object_to_array( $resource_data->thursday ) )[0]	) )	: 'Thu'; ?></span> <span class="week-mon-no-operatives" >(7)</span></th>															
								<th class="resource-th-cell"  ><span class="week-fri" ><?php echo !empty( $resource_data->friday ) 		? date( 'D d', strtotime( array_keys( object_to_array( $resource_data->friday )	)[0] ) )	: 'Fri'; ?></span> <span class="week-mon-no-operatives" >(12)</span></th>															
								<th class="resource-th-cell"  ><span class="week-sat" ><?php echo !empty( $resource_data->saturday ) 	? date( 'D d', strtotime( array_keys( object_to_array( $resource_data->saturday ) )[0]	) )	: 'Sat'; ?></span> <span class="week-mon-no-operatives" >(0)</span></th>															
								<th class="resource-th-cell"  ><span class="week-sun" ><?php echo !empty( $resource_data->sunday ) 		? date( 'D d', strtotime( array_keys( object_to_array( $resource_data->sunday )	)[0] ) )	: 'Sun'; ?></span> <span class="week-mon-no-operatives" >(0)</span></th>															
							</tr>
							
							<tbody>
								<tr>
									<!-- LOOP THROUGH THE DAYS OF THE WEEK -->
									<?php foreach( array_map( 'strtolower', $week_days ) as $day ){ ?>
										
										<?php 
											$week_day_ref = $day.'_'.$week_no;
										?>
										
										<td class="resource-td-cell" >
											<div class="item-container panel-group no-border" id="accordion_<?php echo $week_day_ref; ?>" >
												<?php if( !empty( $resource_data->{$day} ) ) { foreach( $resource_data->{$day} as $date => $personnel_data ) { ?>
													<?php foreach( $personnel_data as $person_id => $personnel_data ) { ?>

														<?php 
															$actual_slots 	= !empty( $personnel_data->availability->actual_slots )   ? $personnel_data->availability->actual_slots : 0;
															$consumed_slots = !empty( $personnel_data->availability->consumed_slots ) ? $personnel_data->availability->consumed_slots : 0;
															$available_slots= $actual_slots - $consumed_slots;
														?>
                                                        
                                                       
														<div class="user-element panel panel-default no-radius bg-grey" id="user-<?php echo $week_day_ref.'_'.$person_id ?>" user_id ="<?php echo $person_id; ?>" user_fullname="<?php echo $personnel_data->person; ?>" user_address="<?php echo $personnel_data->home_address; ?>" user_slots="<?php echo $available_slots; ?>" user_element_date = "<?php echo $personnel_data->availability->ref_date; ?>">
															
															<div class="user-header align-left panel-header-info panel-heading bg-grey pointer no-radius accordion-toggle" style="padding:0;" data-toggle="collapse" data-parent="#accordion_<?php echo $week_day_ref; ?>" href="#accordion_<?php echo $week_day_ref.'_'.$person_id; ?>">
																<h5 class="user_name"><?php echo $personnel_data->person; ?></h5>
																<div><?php echo $personnel_data->home_postcode; ?> <span class="pull-right" ><?php echo ( $available_slots ); ?> hour<?php echo ( in_array( ( $available_slots ), ['1','-1'] ) ) ? '' : 's'; ?></span></div>
																<div class="user_address hide"><?php echo $personnel_data->home_address; ?></div>
															</div>
															
															<div id="accordion_<?php echo $week_day_ref.'_'.$person_id; ?>" class="user-more-info panel-collapse collapse no-radius" >
																<hr>
																<div id="user-<?php echo $week_day_ref.'_'.$person_id; ?>-jobs" user_id = "<?php echo $person_id; ?>" style="margin-top:-30px;min-height:40px !important;" class="sortable-list connectedSortable user-jobs">
																	&nbsp;
																	<?php if( !empty( $personnel_data->availability->booked_jobs ) ){ foreach( $personnel_data->availability->booked_jobs as $k => $job_details ) { ?>
																		<div class="job-element lock-to-sortable pointer text-white" id="job-<?php echo $job_details->job_id; ?>" job_id = "<?php echo $job_details->job_id; ?>" job_slots="<?php echo $job_details->job_duration; ?>" job_name="<?php echo lean_string( $job_details->job_type ); ?>" job_description="<?php echo $job_details->job_type; ?>" job_location="<?php echo $job_details->summaryline; ?>" lock_to_container = "user-<?php echo $week_day_ref.'_'.$person_id; ?>-jobs">
																			<div class="job-header strong">
																				<h4>
																					<span class="job-el-type pull-left"><?php echo ( !empty( $job_details->job_type ) ? $job_details->job_type : false ); ?></span>
																					<span class="job-el-duration pull-right"><?php echo ( !empty( $job_details->job_duration ) ? $job_details->job_duration : false ); echo " ".( ( $job_details->job_duration > 1 ) ? "hours" : "hour" ); ?></span>
																				</h4>
																			</div>
																			<div class="job-details small pull-left" >
																				<span class="pull-left"><?php echo ( !empty( $job_details->summaryline ) ? $job_details->summaryline : '<span class="text-text">NO POSTCODE AVAILABLE</span>&nbsp;' ); ?></span>
																			</div>
																		</div>
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
																	<div class="row">
																		<div class="col-md-3 user-commit-jobs" title="Commit Jobs" ><span class="commit btn-sm btn-block btn-success no-radius no-shadow" ><i class="fas fa-check"></i></span></div>
																		<div class="col-md-3 user-remove-jobs" ><span class="commit btn-sm btn-block btn-danger no-radius no-shadow" ><i class="fas fa-times"></i></span></div>
                                                                        <div class="col-md-3 user-optimize-waypoints" ><span class="commit btn-sm btn-block btn-warning no-radius no-shadow" ><i class="fas fa-map-marker-alt"></i></span></div>
                                                                        <div class="col-md-3 user-view-route" ><span class="commit btn-sm btn-block btn-info no-radius no-shadow" ><i class="fas fa-star"></i></span></div>

                                                                        
                                                                    </div>
																	<br/>
																</div>
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
							<th class="resource-th-cell" style="paddin-bottom:15px;" ><span class="week-mon" ><?php echo strtoupper( date( 'D d', strtotime( 'monday this week' ) ) ); ?></span> <span class="week-mon-no-operatives" >(0)</span></th>															
							<th class="resource-th-cell" style="paddin-bottom:15px;" ><span class="week-tue" ><?php echo strtoupper( date( 'D d', strtotime( 'tuesday this week' ) ) ); ?></span> <span class="week-mon-no-operatives" >(0)</span></th>															
							<th class="resource-th-cell" style="paddin-bottom:15px;" ><span class="week-wed" ><?php echo strtoupper( date( 'D d', strtotime( 'wednesday this week' ) ) ); ?></span> <span class="week-mon-no-operatives" >(0)</span></th>															
							<th class="resource-th-cell" style="paddin-bottom:15px;" ><span class="week-thu" ><?php echo strtoupper( date( 'D d', strtotime( 'thursday this week' ) ) ); ?></span> <span class="week-mon-no-operatives" >(0)</span></th>															
							<th class="resource-th-cell" style="paddin-bottom:15px;" ><span class="week-fri" ><?php echo strtoupper( date( 'D d', strtotime( 'friday this week' ) ) ); ?></span> <span class="week-mon-no-operatives" >(0)</span></th>															
							<th class="resource-th-cell" style="paddin-bottom:15px;" ><span class="week-sat" ><?php echo strtoupper( date( 'D d', strtotime( 'saturday this week' ) ) ); ?></span> <span class="week-mon-no-operatives" >(0)</span></th>															
							<th class="resource-th-cell" style="paddin-bottom:15px;" ><span class="week-sun" ><?php echo strtoupper( date( 'D d', strtotime( 'sunday this week' ) ) ); ?></span> <span class="week-mon-no-operatives" >(0)</span></th>															
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

<script src="<?php echo base_url( '/assets/js/slick/slick.min.js' ) ?>" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
	$( document ).on( 'ready', function() {
		$( ".regular" ).slick({
			dots: false,
			infinite: false,
			slidesToShow: 1,
			slidesToScroll: 1
		});
    });
    
    function commitJobUserBatch(user_element){

        var userElement 	= $( this ).closest( ".user-element" );
        var userID			= userElement.attr( "user_id" );
        var job_Batch 		= {};
        var i = 1;

        assigned_to = $( user_element ).attr( "user_id" )
        job_date = $( user_element ).attr( "user_element_date" )

        travel_elements = userElement.find( ".travel-information-element" )

        $( user_element ).find( ".job-element" ).each( function(i, job_element){
            job_id = $( job_element ).attr( "job_id" )
            job_Batch[ job_id ] = {
                'job_id' : job_id,
                'job_order' : i,
                'assigned_to' : assigned_to,
                'job_date' : job_date,
                'job_travel_time' : $( travel_elements[ i ]).attr( "duration" ),
                'job_travel_distance' : $( travel_elements[ i ] ).attr( "distance" ),

            };

        });

        if( job_Batch ){
            $.ajax({
                url:"<?php echo base_url( 'webapp/diary/commit_jobs' ); ?>",
                method: "POST",
                data:{ jobBatch: job_Batch },
                success: function( data ){
                    var newData = JSON.parse( data );
                    if( newData.status == true || newData.status == 1 ){
                        swal({
                            type: 'success',
                            title: newData.status_msg,
                            showConfirmButton: false,
                            timer: 3000
                        })
                        window.setTimeout(function(){
                            location.reload();
                        }, 3000);
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