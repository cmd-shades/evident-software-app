/* Default dropdown filter */
function setupResultFilter( filterContainer, selectMultiple = true){
    
    var resThis = this;
    var update = null;
    
    resThis.selectMultiple = selectMultiple;
    
    this.update = function(){
        alert( 'The filter class has not been properly instantiated!' )
    }
    
    $( document ).ready(function( $ ) {
        $( 'body' ).click(function( e ) {
            if ( !$( e.target ).closest( filterContainer ).length ) {
                $( filterContainer ).find( '.filter-dropdown' ).slideUp()
                $( filterContainer ).removeClass( 'filter-active' )
            }
        });
        
        // set the checkboxes to checked in the case where the user presses back
        $( filterContainer ).find( '.filter-select-all' ).prop( 'checked', false )
        
    });

    this.getFilters = function(){
        active_filters = []
        
        if(resThis.selectMultiple){
            $( filterContainer ).find('.active-filters').find('.filter-checkbox').each(function(index, filter_checkbox) {
                if( $( filter_checkbox ).prop( 'checked' ) ){
                    active_filters.push( $( filter_checkbox ).val() )
                }
            });
            return active_filters
        } else {
            active_filter = 0;
            $( filterContainer ).find('.active-filters').find('.filter-checkbox').each(function(index, filter_checkbox) {
                if( $( filter_checkbox ).prop( 'checked' ) ){
                    active_filter = $( filter_checkbox ).val()
                }
            });
            return active_filter;
        }
    }

    // toggle filter visible
    $( filterContainer ).on( 'click', '.filter-heading', function( event ) {
        if( $( filterContainer ).find( '.filter-dropdown' ).is( ':visible' ) ){
            $( filterContainer ).find( '.filter-dropdown').slideUp().promise().done(function() {
              $( filterContainer ).removeClass( 'filter-active' )
            });
            
        } else {
            $( filterContainer ).find( '.filter-dropdown' ).slideDown()
            $( filterContainer ).addClass( 'filter-active' )
        }
    })
    
    // update the clear button
    $( filterContainer ).find( '.active-filters' ).find( '.filter-checkbox' ).change(function(){
        
        if(resThis.selectMultiple){
        
            tabCount = $( filterContainer ).find( '.active-filters' ).find( '.filter-checkbox' ).filter( ':checked' ).length
            
            if( tabCount > 0 ){
                $( filterContainer ).find( '.filter-count' ).text( '(' + tabCount + ')' )
                $( filterContainer ).find( '.filter-clear' ).show()
            } else {
                $( filterContainer ).find( '.filter-count' ).text( '' )
                $( filterContainer ).find( '.filter-clear' ).hide()
            }
            
            if( !$(this).prop( 'checked' ) ){
                $(filterContainer).find( '.filter-select-all' ).prop( 'checked', false )
            }
            
        } else {
            
            checkbox_checked = $( this ).prop('checked');
            $( filterContainer ).find( '.active-filters' ).find( '.filter-checkbox' ).prop('checked', false)
            $( this ).prop('checked', checkbox_checked)
            
        }
         
        resThis.update()
        
    })
    
    $( filterContainer ).find( '.filter-clear' ).on( 'click', function( event ) {
        $( filterContainer ).find( '.filter-checkbox' ).each( function(i, filterCheckbox) {
            $( filterCheckbox ).prop( 'checked', false )
            $( filterContainer ).find( '.filter-clear' ).hide()
            $( filterContainer ).find( '.filter-count' ).text( '' )
        });
        
        resThis.update()
    })
    
    $( filterContainer ).find( '.filter-select-all' ).on( 'click', function( event ) {
        
        selectAllChecked = $(this).prop('checked')
        
        $( filterContainer ).find( '.filter-checkbox' ).prop('checked', selectAllChecked)
        
        resThis.update()
    })
}



/* Datetime picker */

// sets up a date filter
function setupJobDateFilter( filterContainer ){
    
    //keeps track of current start and end date
	var dateThis 			= this;
    dateThis.dateStart 		= false
    dateThis.dateEnd		= false
    
    // collects the dates 
    this.getDates = function(){
        return { 'job_date_start' : ( dateThis.dateStart != false ) ? dateThis.dateStart : '' , 'job_date_end' : ( dateThis.dateEnd != false ) ? dateThis.dateEnd : '' }
    }
    
    this.update = function(){
        alert( 'The filter has not been properly instantiated!' )
    }

    //sets up the start date picker
    $( filterContainer ).find( '.datepicker-start' ).datetimepicker({
		formatDate: 'd/m/Y',
		timepicker: false,
		format: 'd/m/Y',
	});


	$( filterContainer ).find( '.datepicker-start' ).on( "change", function(){
		dateThis.dateStart 	= $( this ).val();
		$( filterContainer ).find( '.filter-clear' ).show();

		// set the heading input amount
		$( filterContainer ).find( '.filter-count' ).text( ( dateThis.dateStart && dateThis.dateEnd ) ? '(2)' : '(1)');
		
		dateThis.update();
	});
	
    
	    //sets up the end date picker
    $( filterContainer ).find( '.datepicker-end' ).datetimepicker({
		formatDate: 'd/m/Y',
		timepicker: false,
		format: 'd/m/Y',
	});
	
    //sets up the end date picker
	$( filterContainer ).find( '.datepicker-end' ).on( "change", function(){
		dateThis.dateEnd 	= $( this ).val();
		$( filterContainer ).find( '.filter-clear' ).show();

		// set the heading input amount
		$( filterContainer ).find( '.filter-count' ).text( ( dateThis.dateStart && dateThis.dateEnd ) ? '(2)' : '(1)');
		
		dateThis.update();
    });
	
	$( 'html' ).on( "click", function( e ){
		if (e.target.id != filterContainer && $( e.target ).parents( '.filter-upper-container' ).length == 0) {
			$( filterContainer ).find( '.filter-dropdown' ).slideUp().promise().done( function( ) {
				$( filterContainer ).removeClass( 'filter-active' )
			});
		}
	});
	
    
    // toggle date picker
    $( filterContainer ).on( 'click', '.filter-heading', function( event ) {
		event.stopPropagation();
        if($( filterContainer ).find( '.filter-dropdown' ).is( ':visible' )){
            $( filterContainer ).find( '.filter-dropdown' ).slideUp().promise().done( function( ) {
              $( filterContainer ).removeClass( 'filter-active' )
            });
            
        } else {
            $( filterContainer ).find( '.filter-dropdown' ).slideDown()
            $( filterContainer ).addClass( 'filter-active' )
        }
    })
    
    $( filterContainer ).find( '.filter-item' ).click(function() {
      $( this ).find( '.filter-date-dropdown' ).slideToggle()
    });
    
    $( filterContainer ).find( '.toggle-item' ).on('click', function(event) {
        $( $( this ).attr( 'attr-data_toggle' ) ).slideToggle()
    })
    
    $( filterContainer ).find( '.filter-clear' ).on( 'click', function( event ) {
/*         $( filterContainer ).find( '.filter-start-date' ).html( '<span style="color:gray">Start Date</span>' );
        $( filterContainer ).find( '.filter-end-date' ).html( '<span style="color:gray">End Date</span>' ); */
        $( filterContainer ).find( '.filter-count' ).text( '' );
        $( filterContainer ).find( '.filter-clear' ).hide();
		$( filterContainer ).find( '.datepicker-start' ).val( "DD/MM/YY" );
		$( filterContainer ).find( '.datepicker-end' ).val( "DD/MM/YY" );
        dateThis.dateStart 	= false
        dateThis.dateEnd 	= false
        dateThis.update()
    })
}
	
	
// sets up a date filter
function setupCreateDateFilter( filterContainer ){
    
    //keeps track of current start and end date
	var dateThis 			= this;
    dateThis.dateStart 		= false
    dateThis.dateEnd		= false
    
    // collects the dates 
    this.getDates = function(){
        return { 'created_on_start' : ( dateThis.dateStart != false ) ? dateThis.dateStart : '' , 'created_on_end' : ( dateThis.dateEnd != false ) ? dateThis.dateEnd : '' }
    }
    
    this.update = function(){
        alert( 'The filter has not been properly instantiated!' )
    }

    //sets up the start date picker
    $( filterContainer ).find( '.datepicker-start' ).datetimepicker({
		formatDate: 'd/m/Y',
		timepicker: false,
		format: 'd/m/Y',
	});


	$( filterContainer ).find( '.datepicker-start' ).on( "change", function(){
		dateThis.dateStart 	= $( this ).val();
		$( filterContainer ).find( '.filter-clear' ).show();

		// set the heading input amount
		$( filterContainer ).find( '.filter-count' ).text( ( dateThis.dateStart && dateThis.dateEnd ) ? '(2)' : '(1)');
		
		dateThis.update();
	});
	
    
	    //sets up the end date picker
    $( filterContainer ).find( '.datepicker-end' ).datetimepicker({
		formatDate: 'd/m/Y',
		timepicker: false,
		format: 'd/m/Y',
	});
	
    //sets up the end date picker
	$( filterContainer ).find( '.datepicker-end' ).on( "change", function(){
		dateThis.dateEnd 	= $( this ).val();
		$( filterContainer ).find( '.filter-clear' ).show();

		// set the heading input amount
		$( filterContainer ).find( '.filter-count' ).text( ( dateThis.dateStart && dateThis.dateEnd ) ? '(2)' : '(1)');
		
		dateThis.update();
    });
    
	$( 'html' ).on( "click", function( e ){
		if (e.target.id != filterContainer && $( e.target ).parents( '.filter-upper-container' ).length == 0) {
			$( filterContainer ).find( '.filter-dropdown' ).slideUp().promise().done( function( ) {
				$( filterContainer ).removeClass( 'filter-active' )
			});
		}
	});
	
    // toggle date picker
    $( filterContainer ).on( 'click', '.filter-heading', function( event ) {
		event.stopPropagation();
        if($( filterContainer ).find( '.filter-dropdown' ).is( ':visible' )){
            $( filterContainer ).find( '.filter-dropdown' ).slideUp().promise().done( function( ) {
              $( filterContainer ).removeClass( 'filter-active' )
            });
            
        } else {
            $( filterContainer ).find( '.filter-dropdown' ).slideDown()
            $( filterContainer ).addClass( 'filter-active' )
        }
    })
    
    $( filterContainer ).find( '.filter-item' ).click(function() {
      $( this ).find( '.filter-date-dropdown' ).slideToggle()
    });
    
    $( filterContainer ).find( '.toggle-item' ).on('click', function(event) {
        $( $( this ).attr( 'attr-data_toggle' ) ).slideToggle()
    })
    
    $( filterContainer ).find( '.filter-clear' ).on( 'click', function( event ) {
/*         $( filterContainer ).find( '.filter-start-date' ).html( '<span style="color:gray">Start Date</span>' );
        $( filterContainer ).find( '.filter-end-date' ).html( '<span style="color:gray">End Date</span>' ); */
        $( filterContainer ).find( '.filter-count' ).text( '' );
        $( filterContainer ).find( '.filter-clear' ).hide();
		$( filterContainer ).find( '.datepicker-start' ).val( "DD/MM/YY" );
		$( filterContainer ).find( '.datepicker-end' ).val( "DD/MM/YY" );
        dateThis.dateStart 	= false
        dateThis.dateEnd 	= false
        dateThis.update()
    })
    
}