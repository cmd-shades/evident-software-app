<?php

    /*****************************************************
    Function to get the overview
    *****************************************************/
    function getOverviewFeed( $account_id, $api_end_point ) {
		
        //Get the date range from AJAX url
        $daterange = !empty( $_COOKIE['daterange'] ) ? $_COOKIE['daterange'] : '7';

        //echo $api_end_point.'/discipline/discipline_stats?account_id='.$account_id.'&where[date_range]='.$daterange;
        //Get the feed URL
        $json = file_get_contents( $api_end_point.'/discipline/discipline_stats?account_id='.$account_id.'&where[date_range]='.$daterange.'');
        //Decode the JSON
        $overviews = json_decode($json);

        //Return feed from data level
        return !empty( $overviews->discipline_stats ) ? $overviews->discipline_stats : false;
        #return $overviews->discipline_stats->data;
    }

    /*****************************************************
    Function to order the disciplines
    *****************************************************/
    function orderDisciplineIDs($a, $b) {
        //Order by discipline ID and return result
        return strcmp($a->profile_info->discipline_id, $b->profile_info->discipline_id);
    }

    /*****************************************************
    Function to get the overview from custom date range
    *****************************************************/
    function getOverviewDateRangeFeed( $start = false, $end = false, $account_id, $api_end_point ) {
        //Get the start date from AJAX url
        $start = $_GET['start'];

        //Get the end date from AJAX url
        $end = $_GET['end'];

        //Get the feed URL
        $json = file_get_contents( $api_end_point.'/discipline/discipline_stats?account_id='.$account_id.'&date_from='.$start.'&date_to='.$end.'');
        
        //Decode the JSON
        $overviews = json_decode($json);
        
        //Return feed from data level
        return $overviews->discipline_stats->data;
    }

    
