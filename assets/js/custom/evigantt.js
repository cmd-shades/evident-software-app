/*

     Written By Jake Nelson 2019,
        Property of Evident

                                    */


// A function that loops over data and adds gantt bars
function loadEviGantt(gantt_container, gantt_decimals){

	Object.keys(gantt_decimals).forEach(function(gantt_id, x) {
		ganttMonthCells = $( gantt_container ).find("." + gantt_id).find(".monthCell")
		ganttData = gantt_decimals[gantt_id]

		ganttMonthCells.each(function( i, ganttMonth ) {
            month_value = ganttData[i]
			addGanttBar(ganttMonth, month_value)
		});
	});
}

// a function to add a gantt bar.
function addGanttBar(ganttMonthCell, mValue){

	switch(mValue.type) {
	  case "block_full":
		$(ganttMonthCell).append("<div class='cellData' style='width:100%'><span class='tooltiptext'>" + mValue.hover_text + "</span></div>")
		break;
	  case "block_start":
		percentage = Math.round(mValue.start * 100)
		aPerc = ((100-percentage))+"%"
		bPerc = (percentage)+"%"
		$(ganttMonthCell).append("<div class='cellNoData' style='width:" + bPerc + "'></div>")
		$(ganttMonthCell).append("<div class='cellData' style='width:" + aPerc + ";float:right;margin-left:1px;'><span class='tooltiptext'>" + mValue.hover_text + "</span></div>")
		break;
	  case "block_partial":
	  	widthPerc = Math.round(mValue.end * 100) - Math.round(mValue.start * 100)
		startPerc = Math.round(mValue.start * 100)
		remPerc = 100 - widthPerc - startPerc

		$(ganttMonthCell).append("<div class='cellNoData' style='width:" + startPerc + "%'></div>")
		$(ganttMonthCell).append("<div class='cellData' style='width:" + widthPerc + "%'><span class='tooltiptext'>" + mValue.hover_text + "</span></div>")
		$(ganttMonthCell).append("<div class='cellNoData' style='width:" + remPerc + "%'></div>")
		break;
	  case "block_end":
		percentage = Math.round(mValue.end * 100)
		aPerc = (percentage)+"%"
		bPerc = ((100-percentage))+"%"
		$(ganttMonthCell).append("<div class='cellData' style='width:" + aPerc + ";margin-left:-1px;'><span class='tooltiptext'>" + mValue.hover_text + "</span></div>")
		$(ganttMonthCell).append("<div class='cellNoData' style='width:" + bPerc + "'></div>")
		break;
	  case "block_empty":
		$(ganttMonthCell).append("<div class='cellNoData' style='width:100%'></div>")
		break;
	}
}
