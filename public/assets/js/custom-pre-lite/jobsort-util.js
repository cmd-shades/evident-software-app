function updateUserStats(user_jobs){

	user_element = $(user_jobs).closest(".user-element")
	home_address = user_element.attr("user_address")

	updateMapFromUser(user_element)

	user_job_elements 	= $(user_jobs).children(".job-element")
	user_job_count 		= user_job_elements.length

	user_jobs.children('.travel-information-element').remove();

	user_id = user_element.attr("user_id")

	if(user_job_count > 0){

		previous_location = home_address

		travel_id = 0

		user_job_elements.each( function( i, user_job_element ){
			if( travel_id == 0 ){
				addTravelInfo($(user_job_element), true, previous_location, $(user_job_element).attr("job_location"), travel_id, user_job_count, user_id, "home-first-job")
			} else {
				addTravelInfo($(user_job_element), true, previous_location, $(user_job_element).attr("job_location"), travel_id, user_job_count, user_id, "job-job")
			}

			previous_location = $(user_job_element).attr("job_location")
			travel_id += 1
		});

		// addTravelInfo(user_job_elements.last(), false, user_job_elements.last().attr("job_location"), home_address, travel_id, user_job_count, user_id, "job-home")
	}
}

function hideTravelInfo(user_jobs){
	user_jobs.children('.travel-information-element').css("display", "none")
}

function getTravelTypeFormatted(unformatted){
	switch(unformatted) {
	  case "home-first-job":
			return "Home to first Job"
		break;
	  case "job-job":
			return "Job to Job"
		break;
	  case "job-home":
			return "Job to Home"
		break;
	  default:
			return ""
	}
}

function addTravelInfo(position, before, location_from, location_to, travel_id, travel_id_max, user_id, travel_type){

	travel_unique_id = "travel-info-" + travel_id + "-" + user_id

	travelInfoDiv = $( "<div style='display: inline-block;color: black;margin: 10px 0;width:100%;' class='travel-information-element info-slide static-sortable "+ travel_type +"'  id='" + travel_unique_id + "' travel_distance='1' travel_time='1'>");

	travelInfoDiv.append("<br>")

	travelInfoDiv.append("<div class='travel-info-additional'>" + getTravelTypeFormatted(travel_type) + "</div>")
	travelInfoDiv.append("<div class='travel-info-content'>" + "An error has occurred" + "</div>")

	travelInfoDiv.append("<br>")
	travelInfoDiv.append("</div>")

	if( before ){
		travelInfoDiv.insertBefore($(position));
	} else {
		travelInfoDiv.insertAfter($(position));
	}
}

function setAllTravelStats(user_id, content, value){
	$( "#user-" + user_id+"-travel-info").find(".travel-info-content").each(function(i, obj) {
		$(obj).text(content)
		$(obj).attr("value", value)
	});
}

function secondsToHmsString(d) {
    d = Number(d);
    var h = Math.floor(d / 3600);
    var m = Math.floor(d % 3600 / 60);
    var s = Math.floor(d % 3600 % 60);

    var hDisplay = h > 0 ? h + (h == 1 ? " hr " : " hrs ") : "";
    var mDisplay = m >=/*<<  add a = to the smallest value type */ 0 ? m + (m == 1 ? " min " : " mins ") : "";
	/*
    var sDisplay = s > 0 ? s + (s == 1 ? " sec" : " secs") : "";
	*/
    return hDisplay + mDisplay /*+ sDisplay*/;
}

function metresToMilesString(d){
	return (d * 0.000621371).toFixed(2) + " mi"
}

function setTravelStat(stat_id, stat_type, value){

	switch(stat_type) {
	  case "distance":
			$(stat_id).text(metresToMilesString(value))
			$(stat_id).attr("value", value)
		break;
	  case "time":
			$(stat_id).text(secondsToHmsString(value))
			$(stat_id).attr("value", value)
		break;
	  default:
			alert("Could not find travel stat type, type: "+stat_type)
			$(stat_id).text(value)
	}
}


function updateTravelStats(user_id){

	total_work_time = getWorkTime(user_id)
	total_mileage = getTotalDistance(user_id)
	total_travel_time = getTravelTime(user_id)

	total_work_travel_time =  getWorkTravelTime(user_id)

	total_time = total_work_time + total_travel_time

	setTravelStat("#user-"+user_id+"-total-mileage", "distance", total_mileage)
	setTravelStat("#user-"+user_id+"-total-travel-time", "time", total_travel_time)
	setTravelStat("#user-"+user_id+"-total-work-time", "time", total_work_time)
	setTravelStat("#user-"+user_id+"-total-work-travel-time", "time", total_work_travel_time)
	setTravelStat("#user-"+user_id+"-total-time", "time", total_time)
}


function getWorkTime(user_id){

	JOB_SLOT_DURATION = 3600

	user_jobs = $("#user-" + user_id +"-jobs")

	job_elements = user_jobs.children(".job-element")

	total_time = 0

	if(job_elements.length > 0){

		job_elements.each(function(i, job_element) {
			total_time += ($(job_element).attr("job_slots"))*JOB_SLOT_DURATION
		});
	}

	return total_time;
}

function getTravelTime(){

	user_jobs = $("#user-" + user_id +"-jobs")

	travel_info_elements = user_jobs.children(".travel-information-element")

	var total_travel_time = 0

	travel_info_elements.each(function(i, obj) {
		travel_time = parseInt($(obj).attr("travel_time"))

		total_travel_time += travel_time
	});

	return total_travel_time
}

function getTotalDistance(){

	user_jobs = $("#user-" + user_id +"-jobs")

	travel_info_elements = user_jobs.children(".travel-information-element")

	var total_travel_distance = 0

	travel_info_elements.each(function(i, obj) {
		travel_distance = parseInt($(obj).attr("travel_distance"))

		total_travel_distance += travel_distance
	});

	return total_travel_distance
}

function getWorkTravelTime(user_id){

	user_jobs = $("#user-" + user_id +"-jobs")

	travel_info_elements = user_jobs.children(".travel-information-element")

	var total_travel_time = 0

	travel_info_elements.each(function(i, obj) {

		travel_element = $(obj)

		if(travel_element.hasClass("job-job")){
			travel_time = parseInt(travel_element.attr("travel_time"))
			total_travel_time += travel_time
		}
	});

	return total_travel_time
}

function getUserName(user_id){
	return $("#user-" + user_id + "-profile").attr("user_fullname")
}

function updateMapFromUser(user_element) {

	user_id = user_element.attr("user_id")

	user_jobs = user_element.find(".user-jobs")

	job_elements = user_jobs.children(".job-element")

	if(job_elements.length > 0){

		home_address = user_element.attr("user_address")

		job_locations = []

		job_elements.each(function (i) {
			job_locations.push($(this).attr("job_location"))
		});

		optimize_enabled = user_element.find(".user-optimize-waypoints").hasClass("fa-fast-forward")

		updateMap(user_id, home_address, home_address, job_locations, optimize_enabled)
	}
}

function getUserJobLocations(user_id) {

	job_locations = []

	user_element = $("#user-" + user_id + "-profile")

	user_id = user_element.attr("user_id")

	user_jobs = user_element.find("#user-" + user_id + "-jobs")

	job_elements = user_jobs.children(".job-element")

	if(job_elements.length > 0){

		home_address = user_element.attr("user_address")

		job_locations.push(home_address)

		job_elements.each(function (i) {
			job_locations.push($(this).attr("job_location"))
		});

		job_locations.push(home_address)
	}

	return job_locations
}
	

function unScheduleJob( job_id ){
	var url      = window.location.href;     // Returns full URL (https://example.com/path/example.html)
	var f_url = url + "/unschedule_job";

	$.ajax({
		url: f_url,
		method: "POST",
		data:{ job_id: job_id },
		success: function( result ){
			var resultData = JSON.parse( result );
			if( resultData.status == true || resultData.status == 1 ){
				swal({
					type: 'success',
					title: resultData.status_msg,
					showConfirmButton: false,
					timer: 3000
				})
				window.setTimeout(function(){
					location.reload();
				}, 3000);
			} else {
				swal({
					type: 'error',
					title: resultData.status_msg
				})
			}
		}
	});
}

	$('.user-info-toggle').click(function(event) {
        user_element = event.target.closest(".user-element")
		$(user_element).find(".user-more-info").slideToggle("slow");
		$(event.target).toggleClass('fa-plus-square fa-minus-square');
    });
