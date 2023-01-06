// converts a number of seconds to x hrs, y mins
function secondsToHmsString( d ) {
    d = Number( d );

    var h = Math.floor( d / 3600 );
    var m = Math.floor( d % 3600 / 60 );

    var hDisplay = h > 0 ? h + ( h == 1 ? " h " : " hrs " ) : "";
    var mDisplay = m > 0 ? m + ( m == 1 ? " m " : " mins " ) : "";

    return hDisplay + mDisplay
}

// converts meteres to miles
function metresToMilesString( d, extension = " miles"){
	return ( d * 0.000621371 ).toFixed( 2 ) + extension
}

// tells you how far through the month a date is.
// endBefore == false will set the last day of month to 1
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

// returns the amount of days in a given month
function getDaysInMonth(month, year) {
    return new Date(year, month, 0).getDate();
}

// turns a date object into DD-MM-YYYY
function dateToText(input_date) {
    function pad(s) { return (s < 10) ? '0' + s : s; }
    var d = new Date(input_date)
    return [pad(d.getDate()), pad(d.getMonth()+1), d.getFullYear()].join('/')
}
