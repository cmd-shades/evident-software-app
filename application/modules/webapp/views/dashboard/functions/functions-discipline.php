<?php

    /**********************************************************
    Function to get a single discipline
    **********************************************************/
    function getDisciplineFeed( $disciplineid = false, $account_id = false, $api_end_point = false  ) {

		//Get the date range from AJAX url
		$daterange  = $_GET['daterange'];

		//Default blank date range to 7 days
		if($daterange == '') {
			$daterange = 7;
		}

		//Get the discipline ID from AJAX url
		$disciplineid = $_GET['id'];
		//Get the feed URL
		$json = file_get_contents( $api_end_point.'/discipline/discipline_stats?account_id='.$account_id.'&discipline_id='.$disciplineid.'&where[date_range]='.$daterange );

		//Decode the JSON
		$disciplines = json_decode($json);

		//Return feed from stats level
		return $disciplines->discipline_stats;
    }

    /**********************************************************
    Function to get a single discipline with custom date range
    **********************************************************/
    function getDisciplineDateRangeFeed( $disciplineid = false, $account_id = false, $api_end_point = false ) {
      //Get the start date from AJAX url
      $start = $_GET['start'];

      //Get the start date from AJAX url
      $end = $_GET['end'];
      
      //Get the discipline ID from AJAX url
      $disciplineid = $_GET['id'];
      
      //Get the feed URL
      $json = file_get_contents( $api_end_point.'/discipline/discipline_stats?account_id='.$account_id.'&discipline_id='.$disciplineid.'&date_from='.$start.'&date_to='.$end.'');
      
      //Decode the JSON
      $disciplines = json_decode($json);
      
      //Return feed from stats level
      return $disciplines->discipline_stats;
    }

    /**********************************************************
    Function to get discipline ID
    **********************************************************/
    function getDisciplineID( $root_folder = false ) {
		
		//Get the current URI
		#$uri = $_SERVER['REQUEST_URI'];
		$uri = str_replace( '//', '/', $_SERVER['REQUEST_URI'] );

		$uri_parts = explode( '/', $uri );
		$uri_parts = array_filter( $uri_parts );
	  
		if( !empty( $uri_parts ) && ( count( $uri_parts ) == 4 ) ){
			$uri = '/'.$uri_parts[2].'/'.$uri_parts[3].'/'.$uri_parts[4];
		}

		$siteid = '';

		//Is there a site ID available?
		if(isset($_GET["siteid"])) {
			$siteid = $_GET["siteid"];
		}
		
		//Return discipline ID depending on current page
		switch($uri) {

			//If fire
			case '/webapp/dashboard/fire':
				global $disciplinename, $disciplineid; 
				$disciplineid = 1;
				break;

			//If electricity
			case '/webapp/dashboard/electricity':
				$disciplineid = 3;
				break;

			//If security
			case '/webapp/dashboard/security':
				global $disciplinename, $disciplineid;
				$disciplineid = 2;
				break;

			//If water
			case '/webapp/dashboard/water':
				global $disciplinename, $disciplineid;
				$disciplineid = 4;
				break;

			//If gas
			case '/webapp/dashboard/gas':
				global $disciplinename, $disciplineid;
				$disciplineid = 5;
				break;

			//If specialit
			case '/webapp/dashboard/specialist':
				global $disciplinename, $disciplineid;
				$disciplineid = 6;
				break;

			//If overview
			case '/webapp/dashboard/index':
				global $disciplinename;
				break;

			//Default is overview
			default :
				$disciplinename = 'overview';
		}

      //Return result
	    return $disciplineid;
    }

    /**********************************************************
    Function to get discipline name
    **********************************************************/
    function getDisciplineName( $root_folder = false ) {

		//Get the current URI
		$uri 		= str_replace( '//', '/', $_SERVER['REQUEST_URI'] );
		$uri_parts 	= explode( '/', $uri );
		$uri_parts 	= array_filter( $uri_parts );
	  
		if( !empty( $uri_parts ) && ( count( $uri_parts ) == 4 ) ){
			$uri = '/'.$uri_parts[2].'/'.$uri_parts[3].'/'.$uri_parts[4];
		}

		$siteid = '';

		//Is there a site ID available?
		if(isset($_GET["siteid"])) {
			$siteid = $_GET["siteid"];
		}

		//Return discipline ID depending on current page
		switch($uri) {

			//If fire
			case '/webapp/dashboard/fire':
				global $disciplinename, $disciplineid; 
				$disciplinename = 'fire';
				$disciplineid = 1;
				break;

			//If electricity
			case '/webapp/dashboard/electricity':
				$disciplinename = 'electricity';
				$disciplineid = 3;
				break;

			//If security
			case '/webapp/dashboard/security':
				global $disciplinename, $disciplineid;
				$disciplinename = 'security';
				$disciplineid = 2;
				break;

			//If water
			case '/webapp/dashboard/water':
				global $disciplinename, $disciplineid;
				$disciplinename = 'water';
				$disciplineid = 4;
				break;

			//If gas
			case '/webapp/dashboard/gas':
				global $disciplinename, $disciplineid;
				$disciplinename = 'gas';
				$disciplineid = 5;
				break;

			//If specialist
			case '/webapp/dashboard/specialist':
				global $disciplinename, $disciplineid;
				$disciplinename = 'specialist';
				$disciplineid = 6;
				break;

			//If overview
			case '/webapp/dashboard/index':
				global $disciplinename;
				$disciplinename = 'overview';
				break;

			//If building
			case '/webapp/dashboard/building?siteid='.$siteid.'':
				$disciplinename = "building";
				break;

			//Default is overview
			default :
				$disciplinename = 'overview';
		}

		//Return result
		return $disciplinename;
  }

    /**********************************************************
    Function to get discipline URI
    **********************************************************/
  function getDisciplineNameURI() {
    $siteid = '';

    if(isset($_GET["name"])) {
      $disciplinename = $_GET["name"];
    }
    elseif(isset($_GET["site"])) {
      $siteid = $_GET["siteid"];
    }
    else {
      $disciplinename = 'overview';
    }
    
    return $disciplinename;
  }