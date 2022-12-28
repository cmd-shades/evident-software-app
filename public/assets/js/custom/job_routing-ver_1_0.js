/*
      Written by Jake Nelson
     Property of Evident 2019

*/

function rearrangeJobs( current_user_panel, new_job_order ){

    job_elements = $( current_user_panel ).find( ".job-element" )

    new_job_elements = []

    new_job_order.forEach( function( new_index, current_index ) {
        new_job_elements[ new_index ] = job_elements[ current_index ]
    });

    $( current_user_panel ).find( ".job-element" ).remove()

    new_job_elements.forEach(function( job ) {
        $( current_user_panel ).find( ".user-jobs").append( job )
    });

}

function updateUserTotals( user_id ){

    // defines the length of one slot
    JOB_SLOT_DURATION = 3600

    user_jobs_container = $( "#" + user_id + "-jobs" )

    total_travel_duration = 0

    total_work_travel_duration = 0

    total_travel_distance = 0

    total_work_time = 0

    travel_information_elements = $( user_jobs_container ).find( ".travel-information-element" )

    // for each travel element
    $( travel_information_elements ).each( function( i, element ) {

        _travel_distance = parseInt($( element ).attr( "distance" ))
        _travel_duration = parseInt($( element ).attr( "duration" ))

        // add the travel element's distance and duration to the totals
       total_travel_distance +=  _travel_distance
       total_travel_duration +=  _travel_duration

       // if the travel element is during work time

        if( !( i == 0 || i == ( travel_information_elements.length - 1 ) ) ){
            total_work_travel_duration += _travel_duration
        }

    });

    // loop through each job element
    $( user_jobs_container ).find( ".job-element" ).each( function( i, element ) {

        // get the amount of slots the job takes up
        job_slots = parseFloat( $( element ).attr( "job_slots" ) )

        // calculate the time in seconds the job takes up
        job_duration = JOB_SLOT_DURATION * job_slots

        // add the time to the total work time
        total_work_time += job_duration
    });

    total_time = total_travel_duration + total_work_time

    // set each of the travel information totals

    setTravelStat( "#" + user_id + "-total-mileage", total_travel_distance, "distance" )
    setTravelStat( "#" + user_id + "-total-travel-time", total_travel_duration, "time" )
    setTravelStat( "#" + user_id + "-total-work-time", total_work_time, "time" )
    setTravelStat( "#" + user_id + "-total-work-travel-time", total_work_travel_duration, "time" )
    setTravelStat( "#" + user_id + "-total-time", total_time, "time" )

    // get the user's amount of avaliable slots
    user_avaliable_time = parseInt( $( user_jobs_container ).closest( ".user-element" ).attr( "user_slots" ) ) * JOB_SLOT_DURATION

    // calculates the difference between the time the engineer has avaliable and total time
    delta_time = user_avaliable_time - total_time

    // rounds the time to the nearest minute
    delta_time = delta_time - ( delta_time % 60 );

    // set the remaining travel time stat
    setTravelStat( "#" + user_id + "-remaining", delta_time, "remaining-time" )

    // make the time red if the delta time is negative
    if( delta_time < 0 ){
        $( "#" + user_id + "-remaining" ).addClass("remaining-overtime")
        // Enable this to hide users with no time remaining $( "#" + user_id ).hide()
    } else {
        $( "#" + user_id + "-remaining" ).removeClass("remaining-overtime")
    }

}

// converts a second in hour minute second
function secondsToHmsString( d ) {
    d = Number( d );

    // patch for calculating negative delta time
    negative_time = false
    if(d < 0 ){
        d *= -1
        negative_time = true
    }

    var h = Math.floor( d / 3600 );
    var m = Math.floor( d % 3600 / 60 );
    var s = Math.floor( d % 3600 % 60 );

    var hDisplay = h > 0 ? h + ( h == 1 ? " hr " : " hrs " ) : "";
    var mDisplay = m >= 0 ? m + ( m == 1 ? " min " : " mins " ) : "";

    return negative_time ? "-" + (hDisplay + mDisplay) : hDisplay + mDisplay
}

//converts meters into miles
function metresToMilesString( d ){
	return ( d * 0.000621371 ).toFixed( 2 ) + " miles"
}

function doesJobExistAtPosition(job_id, week_ref, day_ref, user_id, job_position) {

    week = avaliable_resource[week_ref]
    day = week[day_ref]

    if (Object.keys(day).length > 0) {
        day_data = Object.values(day)[0]


        if (Object.keys(day_data).indexOf(String(user_id)) >= 0) {

            user_data = day_data[String(user_id)]

            if (Object.keys(user_data).indexOf("availability")) {

                user_availability = user_data["availability"]

                if (user_availability.hasOwnProperty("booked_jobs")) {

                    booked_jobs = user_availability["booked_jobs"]

                    if (booked_jobs !== null) {

                        if (Object.keys(booked_jobs).indexOf(String(job_position)) >= 0) {

                            job_at_position = booked_jobs[job_position]

                            if (job_at_position["job_id"] == job_id) {
                                return true;
                            } else {
                                //console.log("The job does not exist in this position")
                                return false;
                            }

                        } else {

                            //console.log("User has no jobs in position " + job_position)
                            return false;
                        }

                    } else {
                        //console.log("User's booked jobs are null!")
                    }

                } else {
                    //console.log("User has no booked jobs!")
                    return false;
                }


            } else {
                //console.log("User has no availability")
                return false;
            }


        } else {

            //console.log("User does not have data on this page.")
            return false;
        }


    } else {
        //console.log("Found not data on this page!")
        return false;
    }
}


$( function( ){
    $(  ".sortable-list" ).sortable( {
        connectWith: ".connectedSortable" ,
        cancel: ".static-sortable" ,
        receive: function( event , ui ) {
            if( $( ui.item ).hasClass( "lock-to-sortable" ) ){

                if( !( $( this ).attr( "id" ) == $( ui.item ).attr( "lock_to_container" ) ) ){
                    $( ui.item ).appendTo( "#" + $( ui.item ).attr( "lock_to_container" ) );
                    Swal.fire(
                        'Info!',
                        'This job is locked to the current engineer!',
                        'info'
                      )
                }

            } else {

                    if(  ($( this ).attr(  "id" ) !== "jobs-origin" ) && ($( this ).attr(  "id" ) !== undefined)){

                        // update the user stats
                        updateUserStats( $( this ) )
                        updateJobColors( $( this ) )

                        updateJobColors( $( ui.sender ) )

                     }
            }

        },
        update : function(e, ui) {
            if (ui.sender == null && ui.item.parent().is($(this))) {
                updateJobColors( $( this ) )
            }
        },
        stop: function( event , ui ) {
            /* Check that the recipient sortable is not the origin */
            if(  ($( this ).attr(  "id" ) !== "jobs-origin" ) && ($( this ).attr(  "id" ) !== undefined)){
                updateUserStats( $( this ) )
            }

            /* enable scrolling after the job element has been dropped */


        } ,
        start: function( event , ui ) {
            /* disable scrolling after the job element has been collected */


        }
    } ).disableSelection( );


    /* handle hide/show triangle /*/
    $( " .user-header" ).click(function() {
        if($(this).hasClass("collapsed")){
            $(this).find(".show-more").switchClass( "fa-caret-down", "fa-caret-up")
        } else {
            $(this).find(".show-more").switchClass( "fa-caret-up", "fa-caret-down")
        }
    });


    $( '#search_term' ).on( 'input', function() {

        search_terms = $( "#search_term" ).val().toLowerCase().split(" ")

        $( ".user-element" ).each(function( i, el) {

           this_user_name = $(this).attr( "user_fullname" ).toLowerCase()

           $( el ).css( "display", "none" )

            search_terms.forEach( function( search_term ) {
                if( this_user_name.indexOf( search_term ) !== - 1 ){
                    $( el ).css( "display", "block" )
                }
            });
          });
    });


    $( ".routing-details" ).on( 'click', '.user-commit-jobs', function( ) {
        promptCommitJobs($( this ).closest( ".user-element" ))

    });

    function promptCommitJobs(user_element){
        user_remaining_delta = parseInt($(user_element).find(".remaining-overtime").attr("value"))

        if(user_remaining_delta < 0){
            Swal.fire({
                title: 'No remaining hours',
                text: "This user has no remaining hours, are you sure you want to continue?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, commit changes!'
            }).then(( result ) => {
                if ( result.value ) {
                    commitJobUserBatch( user_element )
                }
            })
        } else {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to commit?",
                type: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, commit changes!'
            }).then(( result ) => {
                if ( result.value ) {
                    commitJobUserBatch( user_element )
                }
            })
        }

    }


    $( ".routing-details" ).on( 'click', '.user-optimize-waypoints', function( ) {
        updateUserStats( $( this ).closest( ".user-element" ) , true )

    });


    $( ".routing-details" ).on( 'click', '.user-remove-jobs' , function( ) {
        user_element = $( this ).closest( ".user-element" )

        user_element.find( ".job-curr" ).appendTo(" #jobs-origin" );

        updateUserStats( user_element , true )
    });

    $( "#map-commit" ).click(function() {
        user_to_commit =  $( "#map-commit" ).attr( "user_commit_id" )

        //commitJobUserBatch( $( "#" + user_to_commit ) )

        //$('#mapModal').modal('hide')

        promptCommitJobs($( "#" + user_to_commit ))

    });


    $( ".routing-details" ).on( 'click', '.user-view-route' , function( ) {

        user_element = $( this ).closest( ".user-element" )

        home_address = $( user_element ).attr( "user_address" )

        job_elements = $( user_element ).find( ".job-element" )

        if(job_elements.length == 0 ){

            Swal.fire(
                'Error!',
                'Cannot display map as this user has no jobs!',
                'error'
              )

        } else {

            job_locations = []

            job_elements.each( function( index ) {
                job_locations.push( { location : $( this ).attr( "job_location" ) } )
            });

            $( "#map-commit" ).attr( "user_commit_id", user_element.attr( "id" ) )

            showMap( home_address, job_locations )


        }

    });
} );

function initMap(){

    directionsService = new google.maps.DirectionsService;
    directionsDisplay = new google.maps.DirectionsRenderer;

    window.map = new google.maps.Map( document.getElementById( 'map' ), {
        zoom: 20,
        center: { lat: 41.85, lng: -87.65 }
    });

    directionsDisplay.setMap( map );

    showMap = function( home_address, job_locations ) {

        directionsService.route({
            origin: home_address,
            waypoints: job_locations,
            destination: home_address,
            travelMode: 'DRIVING'
        }, function( response, status ) {

            if ( status === 'OK' ) {
                $("#mapModal").modal()

                directionsDisplay.setDirections( response );

                setTimeout( function(){ window.map.setZoom( 10 ) }, 500 );


            } else {
                console.log( 'Directions request failed due to ' + status );
                Swal.fire(
                    'Cannot show map!',
                    'One or more job locations are invalid!',
                    'warning'
                )
            }
        });


    }
}

function updateJobColors(destination_sortable){

    user_element = $(destination_sortable).closest(".user-element")
    job_elements = $(user_element).find(".job-element")

    user_id = $(user_element).attr("user_id")
    day_ref = $(user_element).attr("day_ref")
    week_num_ref = $(user_element).attr("week_num_ref")


    $.each( job_elements, function( i, job_element ) {

        current_job_position = i
        job_id = $(job_element).attr("job_id")

        job_is_in_original_position = doesJobExistAtPosition(job_id, week_num_ref, day_ref, user_id, current_job_position)

        if(!($(job_element).hasClass("job-curr"))){

            if(job_is_in_original_position){

                $(job_element).removeClass("uncommited-job")

            } else {

                $(job_element).addClass("uncommited-job")

            }

        }

    });
}



function updateUserStats( current_user_panel, optimize_waypoints = false ){

    user_jobs = $( current_user_panel ).find( ".job-element" )

    // remove all previous travel information elements
    $( current_user_panel ).find( ".travel-information-element" ).remove()

    if( user_jobs.length > 0 ){

        $( current_user_panel ).closest( ".user-element" ).find(".no-jobs").css("display", "none")

        // get the user element and get the user_address attribute
        user_home_address = $( current_user_panel ).closest( ".user-element" ).attr( "user_address" )

        job_locations = []

        // create a json object readable by the google maps javascript api

        $(user_jobs).each( function( index , curr_job_element ){
            job_locations.push( {
                location: $( curr_job_element ).attr( "job_location" ),
            });
        });

        updateTravelInformation( current_user_panel, user_home_address, job_locations, optimize_waypoints )

    } else {
        user_id = $( current_user_panel ).closest( ".user-element" ).attr( "id" )

        $( current_user_panel ).closest( ".user-element" ).find(".no-jobs").css("display", "block")

        setTravelStat( "#" + user_id + "-total-mileage", 0, "distance" )
        setTravelStat( "#" + user_id + "-total-travel-time", 0, "time" )
        setTravelStat( "#" + user_id + "-total-work-time", 0, "time")
        setTravelStat( "#" + user_id + "-total-work-travel-time", 0, "time" )
        setTravelStat( "#" + user_id + "-total-time", 0, "time" )

        updateUserTotals( user_id )

    }
}

/*
    setTravelStat(* element id, * raw value * stat type [seconds/meters] )

    Set's a travel element's text in converted units and save's the raw value into an attribute

*/

function setTravelStat( stat_id, value, stat_type ){

    switch( stat_type ) {
        case "distance":
            $( stat_id ).text( metresToMilesString( value ) )
            $( stat_id ).attr( "value", value )
            break;
        case "time":
            $( stat_id ).text( secondsToHmsString( value ) )
            $( stat_id ).attr( "value", value )
            break;
        case "remaining-time":
            if(!isNaN(value)){
                $( stat_id ).text( secondsToHmsString( value ) )
                $( stat_id ).attr( "value", value )
            } else {
                // we have a missing postcode.
                $( stat_id ).html( " - " )
                $( stat_id ).attr( "value", "0" )
            }
            break;
        default:
            alert( "Could not find travel stat type, type: " + stat_type )
            $( stat_id ).text( value )
    }

}

/*
    Builds a travel element object and converts units.
*/

function createTravelElement( distance, duration ){
    return "<div class='travel-information-element static-sortable' distance='" + distance + "' duration='" + duration + "'>" + metresToMilesString( distance ) + " / " + secondsToHmsString(duration)
    + "</div>"
}

/*
    Updates one panel user and calls the Google Api for travel information.

*/
function updateTravelInformation( current_user_panel, user_home_address, job_locations, optimize_waypoints ){

    directionsService.route({

        origin: user_home_address,

        waypoints: job_locations,

        destination: user_home_address,

        optimizeWaypoints: optimize_waypoints,

        travelMode: 'DRIVING',

      }, function( response, status ) {

        if ( status == google.maps.DirectionsStatus.OK ) {

            if( optimize_waypoints ){
                rearrangeJobs( current_user_panel, response[ "routes" ][ 0 ][ "waypoint_order" ] )
            }

            legs = []

            /*
                Unwraps the google response into a simple json object.
            */

            // gets route index 0 as we only asked for one route

            response[ "routes" ][ 0 ][ "legs" ].forEach( function( leg ) {

                distance = leg[ "distance" ][ "value" ]
                duration = leg[ "duration" ][ "value" ]

                legs.push(
                    {
                        "distance" : distance,
                        "duration" : duration
                    })

            });

            // find the jobs section from the user element

            user_jobs = $( current_user_panel ).find( ".job-element" )



            // additional check for while elements are being dragged
            if(user_jobs.length > 0){

                /* append the first (home -> job) travel element before the first job element */

                $( createTravelElement( legs[ 0 ] ["distance" ], legs[ 0 ][ "duration" ] ) ).insertBefore( $( current_user_panel ).find( ".job-element" ).first() )

                /* loop over each other existing job and append the travel information after */

                for (i = 0; i < user_jobs.length; i++) {

                    $( createTravelElement( legs[ i + 1 ][ "distance" ], legs[ i + 1 ][ "duration" ] ) ).insertAfter( $( current_user_panel ).find( ".job-element" )[ i ] )

                }
            }

            user_id = $(current_user_panel).closest(".user-element").attr("id")

            updateUserTotals( user_id )

        } else {
            Swal.fire(
                'No Postcode found!',
                'One or more of the selected jobs have no postcode attached!',
                'warning'
              )


        }

      });

}
