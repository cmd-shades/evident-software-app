/*

    infiniScroll v1.0,
        written by Jake Nelson
        
                                  */

 function infiScroll( buttons , button_count , infi_scroll_container , animation_length = 500 ) { 
  
 	this.buttons = buttons; 
 	this.button_count = button_count; 
 	this.static_menu = false; 
 	this.current_page = 0; 
  
 	$( infi_scroll_container ).append(  "<div class='infini-content' id='ev-infin-menu'></div>"  ) 
  
 	// get the main menu container 
 	this.menu_buttons = $( infi_scroll_container ).find(  ".infini-content"  ); 
  
 	// set a local variable to the current object 
 	var infiThis = this; 
  
 	// a function to get button at position 
 	this.getButton = function ( x ) { 
  
 		// if the button goes past max ,  return false 
 		if ( x < this.buttons.length ) { 
  
 			return this.buttons[ x % ( this.buttons.length ) ] 
  
 		} 
 		return false; 
 	} 
  
 	// hides the previous buttons while scrolling next 
 	this.hideButtonsNext = function ( button_remove_count ) { 
  
 		// recursive function condition 
 		if ( button_remove_count > 0 ) { 
  
 			// get the menu buttons 
 			menu_buttons = $( this.menu_buttons ).children(  ) 
  
 			// animate the first button to 0% 
 			$( menu_buttons ).first(  ).animate( { 
  
 				 "width" : "0%"  
  
 			} ,  animation_length / this.button_count ,   "linear"  ,  function (  ) { 
  
 				// once animation is complete remove the button 
 				$( menu_buttons ).first(  ).remove(  ) 
  
 				// call the function again ,  and take one off the remove counter 
 				infiThis.hideButtonsNext( button_remove_count - 1 ) 
 			} ) 
 		} 
 	} 
  
 	// shows the new buttons while scrolling next 
 	this.showButtonsNext = function ( page ,  buttons_to_add ,  offset = 0 ) { 
  
 		// recursive function condition 
 		if ( offset < buttons_to_add ) { 
  
 			// calculates the index of the button that should be added 
 			btn_index = ( ( page * buttons_to_add ) + offset ) 
  
 			// gets the button at index 
 			button_to_add = this.getButton( btn_index ) 
  
 			// if the button exists 
 			if ( button_to_add ) { 
 				// add the button 
 				this.menu_buttons.append(  "<button class='scroll-el infi-button " + ( button_to_add.selected_tab ? 'infini-btn-active' : '' ) + "' style='width:0px' data-link='" + button_to_add.link + "'>" + button_to_add.text + "</button>"  ); 
 			} else { 
 				// add a hidden button for spacing 
 				this.menu_buttons.append(  "<button class='scroll-el infi-button' style='width:0px;visibility: hidden'></button>"  ); 
 			} 
  
 			// animate the added button into view 
 			$( this.menu_buttons ).children(  ).last(  ).animate( { 
 				 "width" : "100%"  
 			} ,  animation_length / this.button_count ,   "linear"  ,  function (  ) { 
 				// once the animation is complete add the next button 
 				infiThis.showButtonsNext( page ,  buttons_to_add ,  offset + 1 ) 
 			} ) 
  
  
 		} 
 	} 
  
 	// hides the old buttons while scrolling previous 
 	this.hideButtonsPrev = function ( button_remove_count ) { 
  
 		// recursive function condition 
 		if ( button_remove_count > 0 ) { 
  
 			// get the menu buttons 
 			menu_buttons = $( this.menu_buttons ).children(  ) 
  
 			// animate their width to 0% 
 			$( menu_buttons ).last(  ).animate( { 
 				 "width" : "0%"  
 			} ,  animation_length / this.button_count ,   "linear"  ,  function (  ) { 
  
 				// once the animation is complete remove the button 
 				$( menu_buttons ).last(  ).remove(  ) 
  
 				// remove the next button 
 				infiThis.hideButtonsPrev( button_remove_count - 1 ) 
 			} ) 
 		} 
 	} 
  
 	// shows the new buttons while scrolling previous 
 	this.showButtonsPrev = function ( page ,  buttons_to_add ,  offset = 0 ) { 
  
 		// recursive function condition 
 		if ( offset < buttons_to_add ) { 
  
 			// get the index of the new button 
 			btn_index = ( ( ( page + 1 ) * buttons_to_add ) - offset ) - 1 
  
 			// get button at index 
 			button_to_add = this.getButton( btn_index ) 
  
 			// if the button exists at the position 
 			if ( button_to_add ) { 
  
 				// add the button 
 				this.menu_buttons.prepend(  "<button class='scroll-el infi-button " + ( button_to_add.selected_tab ? 'infini-btn-active' : '' ) + "' style='width:0px' data-link='" + button_to_add.link + "'>" + button_to_add.text + "</button>"  ); 
  
 			} else { 
  
 				// add a hidden button for spacing 
 				this.menu_buttons.prepend(  "<button class='scroll-el infi-button' style='width:0px;visibility: hidden;'></button>"  ); 
  
 			} 
  
 			$( this.menu_buttons ).children(  ).first(  ).animate( { 
 				 "width" : "100%"  
 			} ,  animation_length / this.button_count ,   "linear"  ,  function (  ) { 
 				infiThis.showButtonsPrev( page ,  buttons_to_add ,  offset + 1 ) 
 			} ) 
 		} 
 	} 
  
 	// next page of buttons 
 	this.next = function (  ) { 
 		// if at the end of the buttons ,  prevent next 
 		if ( ( ( this.current_page + 1 ) * this.button_count ) < this.buttons.length ) { 
 			this.current_page += 1 
 			this.hideButtonsNext( this.button_count ) 
 			this.showButtonsNext( this.current_page ,  this.button_count ) 
 		} 
 		this.updateNav(  ) 
  
 	} 
  
 	// previous page of buttons 
 	this.prev = function (  ) { 
 		if ( this.current_page > 0 ) { 
 			// if at the start of the buttons ,  prevent previous 
 			this.current_page -= 1 
 			this.hideButtonsPrev( this.button_count ) 
 			this.showButtonsPrev( this.current_page ,  this.button_count ) 
 		} 
 		this.updateNav(  ) 
 	} 
  
 	// a function to update the navigation buttons 
 	this.updateNav = function (  ) { 
 		// if the current position is the start of the list 
 		if ( this.current_page == 0 ) { 
 			// add a disabled colour to the back button 
 			$( infi_scroll_container ).find(  ".infini-btn-prev"  ).addClass(  "infini-nav-disabled"  ) 
 		} else { 
 			// make the back button normal coloured 
 			$( infi_scroll_container ).find(  ".infini-btn-prev"  ).removeClass(  "infini-nav-disabled"  ) 
 		} 
  
 		// if we are at the end of the list 
 		if ( ( ( this.current_page + 1 ) * this.button_count ) < this.buttons.length ) { 
 			//disable the next button 
 			$( infi_scroll_container ).find(  ".infini-btn-next"  ).removeClass(  "infini-nav-disabled"  ) 
 		} else { 
 			// enable the next button 
 			$( infi_scroll_container ).find(  ".infini-btn-next"  ).addClass(  "infini-nav-disabled"  ) 
 		} 
 	} 
  
  
 	/* Only show navigation arrows if required */ 
 	if ( this.button_count < this.buttons.length ) { 
 		$(  "<button class='infi-button ev-shadow infini-btn-prev'><i class='fas fa-caret-left'></i></button>"  ).insertBefore( $( this.menu_buttons ) ); 
 		$(  "<button class='infi-button ev-shadow infini-btn-next'><i class='fas fa-caret-right'></i></button>"  ).insertAfter( $( this.menu_buttons ) ); 
 	} else { 
 		this.static_menu = true 
 		this.button_count = this.buttons.length 
 	} 
  
 	// calculate the page that the selected tab is on and set it to the current position 
 	for ( x = 0; x < this.buttons.length; x++ ) { 
 		if ( this.buttons[ x ][  "selected_tab"  ] == true ) { 
 			this.current_page = Math.floor( ( x ) / this.button_count ); 
 		} 
 	} 
  
 	/* add the buttons into the menu */ 
 	for ( i = ( this.current_page * this.button_count ); i < this.button_count + ( this.current_page * this.button_count ); i++ ) { 
 		button_to_add = this.getButton( i ); 
  
 		// add  each of the buttons to the menu 
 		if ( button_to_add ) { 
 			if ( !this.static_menu ) { 
 				this.menu_buttons.append(  "<button class='scroll-el infi-button " + ( button_to_add.selected_tab ? 'infini-btn-active' : '' ) + "' data-link='" + button_to_add.link + "'>" + button_to_add.text + "</button>"  ); 
 			} else { 
 				this.menu_buttons.append(  "<button class='scroll-el infi-button " + ( button_to_add.selected_tab ? 'infini-btn-active' : '' ) + " infini-btn-static " + ( this.buttons.length == 5 ? 'infi-btn-five' : '' ) + "' data-link='" + button_to_add.link + "'>" + button_to_add.text + "</button>"  ); 
 			} 
 		} else { 
 			this.menu_buttons.append(  "<button class='scroll-el infi-button' style='visibility: hidden;'></button>"  ); 
 		} 
 	} 
  
 	this.updateNav(  ); 
  
 	/* connect the previous and next buttons to the nav buttons */ 
 	$( infi_scroll_container ).on( 'click' ,  '.infini-btn-prev' ,  function (  ) { 
 		infiThis.prev(  ); 
 	} ); 
  
 	$( infi_scroll_container ).on( 'click' ,  '.infini-btn-next' ,  function (  ) { 
 		infiThis.next(  ); 
 	} ) 
  
 	// when a button is clicked and it has a data link ,  redirect the page 
 	$( infi_scroll_container ).on( 'click' ,  '.scroll-el' ,  function (  ) { 
 		if ( this.hasAttribute(  "data-link"  ) ) { 
 			window.location.href = $( this ).attr(  "data-link"  ); 
 		} 
 	} ) 
  
 } 
