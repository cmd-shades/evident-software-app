function generateUsers(user_data){

    formatted_users = []

    user_data.forEach(function(entry, i) {
        name_title = entry["user_id"] + " " + entry["user_first_name"] + " "+ entry["user_last_name"] + "<br><p class='user_title'>" + entry["user_postcode"] +"<br>"+"</p>"

        formatted_users.push({id : entry["user_id"], content:  name_title})
    });
    return formatted_users
}

function generateJobs(job_data){

    formatted_jobs = []
    console.log( JSON.stringify( job_data ) )

    job_data.forEach(function(entry, i) {
        formatted_jobs.push({id: i, content: entry["job_postcode"], start: entry["job_start"], end: entry["job_end"], group: entry["user_id"], className: "jobstatus"+entry["job_status_id"]})
    });
   
    return formatted_jobs
}