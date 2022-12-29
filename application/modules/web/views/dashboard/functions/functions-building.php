<?php

    /************************************************
    Function to get a single building
    ************************************************/
    function getBuildingFeed($account_id, $api_end_point = false)
    {
        //echo $api_end_point.'/discipline/building_stats?account_id='.$account_id.'&where[date_range]='.$_COOKIE['daterange'].'&site_id='.$_GET['siteid'];
        //Get the feed URL
        $json = file_get_contents($api_end_point.'/discipline/building_stats?account_id='.$account_id.'&where[date_range]='.$_COOKIE['daterange'].'&site_id='.$_GET['siteid'].'');

        //Decode the JSON
        $buildings = json_decode($json);

        //Return feed from data level
        return $buildings->building_stats->data;
    }

    /************************************************
    Function to get a single building recommendations
    ************************************************/
    function getBuildingRecommendationsFeed($account_id, $api_end_point = false)
    {
        //Get the feed URL
        //echo $api_end_point.'/discipline/building_recommendations?account_id='.$account_id.'&where[date_range]='.$_COOKIE['daterange'].'&site_id='.$_GET['siteid'];
        $json = file_get_contents($api_end_point.'/discipline/building_recommendations?account_id='.$account_id.'&where[date_range]='.$_COOKIE['daterange'].'&site_id='.$_GET['siteid'].'');

        //Decode the JSON
        $recommendations = json_decode($json);

        //Return feed
        return $recommendations;
    }

    /************************************************
    Function to get a single building's overdue jobs
    ************************************************/
    function getOverdueJobsFeed($account_id = false, $api_end_point = false)
    {
        //Get the feed URL
        //echo $api_end_point.'/discipline/overdue_jobs?account_id='.$account_id.'&where[date_range]='.$_COOKIE['daterange'].'&site_id='.$_GET['siteid'];
        $json = file_get_contents($api_end_point.'/discipline/overdue_jobs?account_id='.$account_id.'&where[date_range]='.$_COOKIE['daterange'].'&site_id='.$_GET['siteid'].'');
        //Decode the JSON
        $overdues = json_decode($json);

        //Return feed
        return !empty($overdues) ? $overdues : false;
    }
