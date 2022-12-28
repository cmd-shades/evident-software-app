$( function(){
    $(".sortable-list").sortable({
        connectWith: ".connectedSortable",
        cancel: ".static-sortable",
        receive: function( event, ui ) {
            current_user_jobs = $(this)

            // update the new list when dropped
/*             if( current_user_jobs.hasClass( "user-jobs" ) ){
                updateUserStats( current_user_jobs )
            } */

			var newPlace = ui.item.closest( '.section' ).attr( 'id' );
			var oldPlace =  ui.item.attr( 'data-source' );
			$( this ).removeAttr( 'data-source' );
			
			if( newPlace.toLowerCase() == "jobs-container" && oldPlace.toLowerCase() == "field-operative-w-jobs" ){
				var jobID = ui.item.attr( "job_id" );
				if( parseInt( jobID, 10 ) > 0 ){
					unScheduleJob( jobID );
				} else {
					alert( "Incorrect Job ID attribute" );
				}
			}

        },
        stop: function( event, ui ){

            // update the old list when dropped
/*             if( $( this ).hasClass( "user-jobs" ) ){
                updateUserStats( $( this ) )
            } */
        },
        start: function( event, ui ) {
            // hide travel info as the user is dragging the job
            hideTravelInfo($(this))
			
			var source = ui.item.closest( '.section' ).attr( 'id' );
			ui.item.attr( 'data-source', source );
			console.log( source );
        }
    }).disableSelection();
});