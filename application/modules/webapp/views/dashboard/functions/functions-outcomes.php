<?php
    /*****************************************************
    Function to get the buildings outcomes in a discipline
    *****************************************************/
    function getOutcomesFeed($disciplineid, $site, $account_id, $api_end_point) {
		$date_range = !empty( $_COOKIE['daterange'] ) ? $_COOKIE['daterange'] : '7';
        //Get the feed URL
        $json = file_get_contents( $api_end_point.'/discipline/building_outcomes?account_id='.$account_id.'&where[date_range]='.$date_range.'&discipline_id='.$disciplineid.'&site_id='.$site.'');
        
        //Decode the JSON
        $outcomes = json_decode($json);
		
        //Return feed
        return $outcomes;
    }
