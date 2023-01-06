function initMap() {

	var markerArray = [];

	   var map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 54.030993, lng: -0.319281},
        zoom: 4,
/* 		mapTypeId: google.maps.MapTypeId.HYBRID */
      });
	  
	  
	
	var stepDisplay = new google.maps.InfoWindow;
	
	var directionsService = new google.maps.DirectionsService;

	var directionsDisplay = new google.maps.DirectionsRenderer({map: map});

	updateMap = function(user_id, location_start, location_end, waypoints_list, optimize_waypoints) {
		
		route_waypoints = []
		
		for(var i = 0; i < waypoints_list.length; i++) {
			route_waypoints.push({
				location: waypoints_list[i],
				stopover: true
			})
		}
		
        calDisplayFast(location_start, location_end, route_waypoints, directionsDisplay, directionsService, markerArray, stepDisplay, map, user_id, optimize_waypoints);
    };
	


}

  function calDisplayFast(location_start, location_end, route_waypoints, directionsDisplay, directionsService, markerArray, stepDisplay, map, user_id, optimize_waypoints) {
	for (var i = 0; i < markerArray.length; i++) {
	  markerArray[i].setMap(null);
	}
	directionsService.route({
	  origin: location_start,
	  waypoints: route_waypoints,
	  destination: location_end,
	  optimizeWaypoints: optimize_waypoints,
	  travelMode: 'DRIVING',
	}, function(response, status) {
		if (status == google.maps.DirectionsStatus.OK) {
			console.log(response)
			
			directionsDisplay.setDirections(response);
			updateSteps(response, markerArray, stepDisplay, map, user_id);
			updateTravelSeperators(response)
			if(optimize_waypoints){
				reorderJobs(response, user_id)
			}

        } else {
            getErrorType(status)
			$("#directions-content").text("(Select some jobs to get started)")
		}
	});
  }

  function updateTravelSeperators(response){
	  
	  		var route = response.routes[0];
			
			user_jobs_element = ("#"+"user-" +user_id + "-jobs")
			
			$(user_jobs_element).children(".travel-information-element").each(function(i) {
				
				distance_value = route.legs[i].distance.value
				time_value = route.legs[i].duration.value
				
				distance = metresToMilesString(distance_value)
				time = secondsToHmsString(time_value)
				
				travel_info_text = ("(Time: " +time +  "| Distance: " + distance + ")")
				
				travel_info_content_element = $(this).find(".travel-info-content").text(travel_info_text)
				
				$(this).attr("travel_distance", distance_value)
				$(this).attr("travel_time", time_value)
				
				$(this).slideDown("slow")
			  
			});
	 
			updateTravelStats(user_id)
  
  }

  function reorderJobs(response, user_id){

		$("#job-clone-temporary").html("")

		user_jobs = $("#user-"+user_id+"-jobs").children(".job-element")

		for (i = 0; i < user_jobs.length; i++) { 
			$(user_jobs[i]).clone().appendTo( "#job-clone-temporary" );
		}



		
  }

  function getErrorType(status){
	  
	  if (status == 'ZERO_RESULTS') {
                alert('No route could be found between the origin and destination.');
            } else if (status == 'UNKNOWN_ERROR') {
                alert('A directions request could not be processed due to a server error. The request may succeed if you try again.');
            } else if (status == 'REQUEST_DENIED') {
                alert('This webpage is not allowed to use the directions service.');
            } else if (status == 'OVER_QUERY_LIMIT') {
                alert('The webpage has gone over the requests limit in too short a period of time.');
            } else if (status == 'NOT_FOUND') {
                alert('At least one origin, destination or waypoint has an invalid address!');
            } else if (status == 'INVALID_REQUEST') {
                alert('The DirectionsRequest provided was invalid.');                   
            } else {
                alert("There was an unknown error in your request. Requeststatus: \n\n"+status);
            }
  }

  function updateSteps(directionResult, markerArray, stepDisplay, map, user_id) {
	 $("#directions-content").text("")
	  
	var myRoute = directionResult.routes[0];
	var job_locations = getUserJobLocations(user_id);
	//var marker = markerArray[step_index] = markerArray[step_index] || new google.maps.Marker;
	
	
	$("#directions-content").append("<p>User: " + getUserName(user_id) + "</p>")
	
    for (var leg_index = 0; leg_index < myRoute.legs.length; leg_index++) {
	
		$("#directions-content").append("<h4 class='directions-journey-title'>Journey " + (leg_index+1) + "</h4>")
		
		$("#directions-content").append("<p class='directions-journey-locations'><b>From</b><br>" + job_locations[leg_index] + "<br><b>To</b><br>" + job_locations[leg_index + 1] + "</p>")
	
		for (var step_index = 0; step_index < myRoute.legs[leg_index].steps.length; step_index++) {
			
		  //var marker = markerArray[step_index] = markerArray[step_index] || new google.maps.Marker;

		  step_instruction = myRoute.legs[leg_index].steps[step_index].instructions
		 
		  $("#directions-content").append(step_instruction + "<br>")
		}
	}
	$("#download-directions-button").attr("disabled", false)
	
	
  }

  function attachInstructionText(stepDisplay, marker, text, map) {
	google.maps.event.addListener(marker, 'click', function() {
	  stepDisplay.setContent(text);
	  stepDisplay.open(map, marker);
	});
  }