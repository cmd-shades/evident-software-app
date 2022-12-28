/*

     Written By Jake Nelson 2019,
        Property of Evident

                                    */

// A function that converts dates across a range into decimal numbers
function datesToDecimals(project_data, project_months){
    proj_data = {}

    // for each project
    Object.values(project_data).forEach(function(project, i) {
        
        // get the project key
        project_key = Object.keys(project_data)[i]
        
        // get related project information
        project_title = Object.values(project_data)[i].project_name
        project_start_tx = dateToText((Object.values(project_data)[i].project_start))
        project_end_tx= dateToText((Object.values(project_data)[i].project_end))
        
        // hover over text
        project_hover_tx = "<p>( " + project_title + " )</p>" + "<small>Project Start "+project_start_tx + "<br>Project End "+project_end_tx + "</small>"
        
        // variable to store current project information
        thisData = []
        project_months.forEach(function(month, i) {				
            if( project["project_start"] <= month && (project["project_end"] >= project_months[i + 1])){
                // (block before) month
                // A block that is filled start to finish
                thisData.push({start: 0, end: 1, type : "block_full", "hover_text" : project_hover_tx})
            } else {
                
                if(project["project_start"] > month && project["project_start"] < project_months[i + 1] && project["project_end"] >= project_months[i + 1]){
                    // (start) month
                    // A block that starts part way through and ends at the end of the month
                    thisData.push({start: dayToDecimal(project["project_start"]), end: 1, type : "block_start", "hover_text" : project_hover_tx})
                } else {
                    if(project["project_end"] > month && project["project_end"] < project_months[i + 1] && project["project_start"] <= project_months[i]){
                        // A block that starts at the start of the month, and ends partially through the month	
                        thisData.push({start: 0, end: dayToDecimal(project["project_end"]), type : "block_end", "hover_text" : project_hover_tx})
                    } else {
                        if(project["project_start"] >= month && project["project_start"] < project_months[ i + 1 ] &&
                            project["project_end"] > month && project["project_end"] < project_months[ i + 1] ){
                            // A block that starts after the start of the month and before the end of the month.
                            // (inbetween) month
                            thisData.push({start: dayToDecimal(project["project_start"]), end: dayToDecimal(project["project_end"]), type : "block_partial", "hover_text" : project_hover_tx})
                        } else {
                            if(project["project_start"] <= month && project["project_end"] >= project_months[ project_months.length - 1 ]){
                                // (block before) month
                                // A block that is filled start to finish
                                thisData.push({start: 0, end: 1, type : "block_full", "hover_text" : project_hover_tx})
                            } else {					
                                // for a month that has no data
                                thisData.push({start: 0, end: 0, type : "block_empty", "hover_text" : project_hover_tx})
                            }	
                        }
                    }
                }
            }
        });
        
        proj_data[project_key] = thisData
    });
    return proj_data
}

// takes in a day and returns a decimal number of how far through the month that day is
// (endBefore) - Keep the last day of the month at 1.0 or keep it just before.
function dayToDecimal(date, endBefore){

    day = date.getDate()
    month = date.getMonth() + 1
    year = date.getFullYear()
    days_in_month = getDaysInMonth(month, year)
    
    if(!endBefore){
        return (day - 1)/(days_in_month - 1)
    } else {
        return (day - 1)/(days_in_month)
    }
}

// counts the day in a given month, year
function getDaysInMonth(month, year) {
    return new Date(year, month, 0).getDate();
}

// converts a date object into text
function dateToText(input_date) {
    function pad(s) { return (s < 10) ? '0' + s : s; }
    var d = new Date(input_date)
    return [pad(d.getDate()), pad(d.getMonth()+1), d.getFullYear()].join('/')
}


function addTimeBar(today_month, today_decimal){

    month_bounds = $(".month-"+today_month).get(0).getBoundingClientRect()
    gantt_main = $("#evi-chart").find(".gantt-main").get(0).getBoundingClientRect()
    
    offset = month_bounds.left - gantt_main.left
        
    from_left = offset + ((month_bounds.right - month_bounds.left ) * today_decimal) - 2.3
    
    $("#evi-chart").find(".timebar-container").append("<div class='time-bar' style='position: absolute; top: 0px;z-index:250;opacity:0.7; left: " + from_left + "px; width: 3px;height:100%; background-color: #fcba03;' today-month='" + today_month + "' today-decimal = '" + today_decimal + "'></div>")
    
}

function updateTimeBar(){
    
    current_timebar = $("#evi-chart").find(".timebar-container").find(".time-bar")
    
    // only update the timebar if it exists
    if(current_timebar.length > 0){

        // get the timebar position, stored in the div element
        today_month = current_timebar.attr("today-month")
        today_decimal = current_timebar.attr("today-decimal")
        
        // get the x y pixel bounds of the selected month
        month_bounds = $(".month-"+today_month).get(0).getBoundingClientRect()

        // get the x y pixel bounds of the gantt chart
        gantt_main = $("#evi-chart").find(".gantt-main").get(0).getBoundingClientRect()
        
        // get the offset from the left
        offset = month_bounds.left - gantt_main.left
            
        // find the total distance across the month and times it by the month's decimal value
        from_left = offset + ((month_bounds.right - month_bounds.left ) * today_decimal) - 2.3 // take 1.5 pixels off (half the width of the line)
        
        // update the timebar position.
        $("#evi-chart").find(".timebar-container").find(".time-bar").css("left", from_left + "px")
    
    }
}