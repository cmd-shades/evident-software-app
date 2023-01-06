$(function() {
    $("#prompt-date-picker").datepicker({ dateFormat: 'dd/mm/yy' });
});

$(document).ready(function() {


    default_day_start = new Date().setHours(4, 0, 0, 0)
    default_day_end = new Date().setHours(23, 59, 59, 999);

    var options = {
        orientation: 'top',
        stack: false,
        start: default_day_start,
        end: default_day_end,
        moveable: false,
        format: {
            majorLabels: {
                hour: 'dddd D MMMM', // here i think
            }
        }
    };

    jobsAreValid = loadJobs()

    if (jobsAreValid) {
        tle = document.getElementById('timeline');
        timeline = new vis.Timeline(tle, new vis.DataSet(), new vis.DataSet([]), options);
        timeline.setGroups(visGetEngineers());
        timeline.setItems(visGetEvents())
    } else {
        $("#timeline-error-message").text("There is no data to display!")
    }

    $("#app-prompt-form").submit(function(e) {
        e.preventDefault(e);
        timeline.setItems([])
        selected_date = $("#prompt-date-picker").datepicker('getDate');
        timeline.setWindow(selected_date.setHours(4, 0, 0, 0), selected_date.setHours(23, 59, 59, 999), options);
        reloadCalendar()

    });
});

function reloadCalendar() {

    timeline.setItems([])
    
	$.ajax({
	  url: "http://localhost/evident-dev/serviceapp/api/diary/routed_jobs?account_id=5&where[grouped_by_date]=1",
	  context: document.body
	}).done(function( data ) {
		  console.log(data)
		
	});
		
	/*

        u_jobs = (new_json_data);

        jobsAreValid = loadJobs()

        if (jobsAreValid) {
            timeline.setGroups(visGetEngineers());
            timeline.setItems(visGetEvents())
        } else {
            $("#timeline-error-message").text("There is no data to display!")
        }*/
    



}

function loadJobs() {

    jobs = {}

    var jobsAreValid = false

    if (u_jobs !== null) {
        //returned object is not null"

        if (u_jobs.hasOwnProperty("jobs")) {
            //returned object has jobs property

            engineers = u_jobs["jobs"]

            Object.keys(engineers).forEach(function(engineer_id) {

                if (engineers[engineer_id].hasOwnProperty("engineer_data") && engineers[engineer_id].hasOwnProperty("engineer_jobs")) {

                    if ((engineers[engineer_id]["engineer_data"] != null) && (engineers[engineer_id]["engineer_jobs"] != null)) {

                        if (engineers[engineer_id]["engineer_data"].hasOwnProperty("availability")) {

                            if (engineers[engineer_id]["engineer_data"]["availability"] != null) {

                                if (engineers[engineer_id]["engineer_data"]["availability"]["on_call"]) {

                                    jobs[engineer_id] = engineers[engineer_id]
                                    jobsAreValid = true;

                                } else {
                                    console.log("Omitting user ID " + engineer_id + ", is not on call!")
                                }

                            } else {
                                console.log("Omitting user ID " + engineer_id + ", user has null availability options!")
                            }

                        } else {
                            console.log("Omitting user ID " + engineer_id + ", user has no availability options!")
                        }

                    } else {
                        console.log("Omitting user ID " + engineer_id + ", user has null properties!")
                    }

                } else {
                    console.log("Omitting user ID " + engineer_id + ", incomplete engineer object!")
                }
            });
        }

    }

    return jobsAreValid

}

function visGetEngineers() {

    vis_engineers = []

    Object.keys(jobs).forEach(function(engineer_id) {
        this_engineer = jobs[engineer_id]
        this_engineer_data = this_engineer["engineer_data"]
        this_engineer_jobs = this_engineer["engineer_jobs"]

        full_name = this_engineer_data["first_name"] + " " + this_engineer_data["last_name"]

        vis_engineers.push({ id: engineer_id, content: full_name })
    });



    return vis_engineers;
}

function visGetEvents() {

    vis_events = []

    Object.keys(jobs).forEach(function(engineer_id) {
        this_engineer = jobs[engineer_id]

        this_engineer_data = this_engineer["engineer_data"]
        this_engineer_availability = this_engineer_data["availability"]
        this_engineer_jobs = this_engineer["engineer_jobs"]

        engineer_day_start = this_engineer_availability["start_time"]

        console.log("User ID " + engineer_id + ", starts their day at " + engineer_day_start)

        Object.keys(this_engineer_jobs).forEach(function(job_date) {

            current_start = Date.parse(job_date + "T" + engineer_day_start);

            Object.keys(this_engineer_jobs[job_date]).forEach(function(job_id) {

                this_job = this_engineer_jobs[job_date][job_id]

                job_duration = this_job["job_duration"]

                if (this_job.hasOwnProperty("job_travel_time")) {

                    travel_to_job = this_job["job_travel_time"]

                    current_end = current_start + (travel_to_job * 3600000)
                    vis_events.push({ 'start': current_start, 'end': current_end, group: engineer_id, 'className': 'travel-element' })
                    current_start = current_end
                } else {

                    console.log("User ID " + engineer_id + " Job ID " + job_id + "has no Job Travel time information")
                    current_end = current_start + (1 * 3600000)
                    vis_events.push({ 'start': current_start, 'end': current_end, group: engineer_id, 'className': 'travel-element-missing' })
                    current_start = current_end

                }



                current_end = current_start + (job_duration * 3600000)
                vis_events.push({ 'start': current_start, 'end': current_end, group: engineer_id, 'className': 'jobstatus' + this_job["status_id"] })
                current_start = current_end



            });

        });



    });




    return vis_events
}