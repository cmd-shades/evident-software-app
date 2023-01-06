$(function() {

	$('.user-info-toggle').click(function(event) {
        user_element = event.target.closest(".user-element")
		$(user_element).find(".user-more-info").slideToggle("slow");
		$(event.target).toggleClass('fa-plus-square fa-minus-square');
    });

	$('.user-reset').click(function(event) {
		user_element = $(event.target.closest(".user-element"))
		user_jobs = user_element.find(".job-element")

		user_jobs.each(function (i) {
			var job_to_move = $(this);
            job_to_move.remove();
            $("#jobs-sortable").prepend(job_to_move);
        });

		user_element.find(".travel-information-element").remove()

		$("#download-directions-button").attr("disabled", "true")

		$("#directions-content").text("(Select some jobs to get started)")

		user_id = user_element.attr("user_id")

		setTravelStat("#user-"+user_id+"-total-mileage", "distance", 0)
		setTravelStat("#user-"+user_id+"-total-travel-time", "time", 0)
		setTravelStat("#user-"+user_id+"-total-work-time", "time", 0)
		setTravelStat("#user-"+user_id+"-total-work-travel-time", "time", 0)
		setTravelStat("#user-"+user_id+"-total-time", "time", 0)
    });


	$('#download-directions-button').click(function(event) {
		$("#download-button-html").attr("value", $("#directions-content").html())

    });

	$('.user-optimize-waypoints').click(function(event) {
		$(event.target).toggleClass('fa-play fa-fast-forward');
		user_section = $(event.target).closest(".user-element")
		updateMapFromUser(user_section)
    });
});

function updateShownUsers(){
	search_text = document.getElementById("user-searchbar").value
	$(".user-element").each(function(i, user_section) {
		user_section.style.display = "block";
			if (user_section.getAttribute("user_fullname").toLowerCase().indexOf(search_text) == -1) {
				user_section.style.display = "none";
			}
	});
}