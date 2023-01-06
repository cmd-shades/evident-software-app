<?php
    /************************************************
    Function to get the buildings within a discipline
    ************************************************/

    function getBuildingDisciplinesFeed($disciplineid, $account_id = false, $api_end_point = false ) {
        //Get the feed URL
        $date_range = !empty( $_COOKIE['daterange'] ) ? $_COOKIE['daterange'] : '7';
        $json 		= file_get_contents( $api_end_point.'/discipline/buildings_by_discipline?account_id='.$account_id.'&discipline_id='.$disciplineid.'&where[date_range]='.$date_range.'&limit=15&offset='.$_GET['offset'] );
        
        //Decode the JSON
        $builddisciplines = json_decode($json);
        
        //Return feed from data level
        return !empty( $builddisciplines->buildings->data ) ? $builddisciplines->buildings->data : false;
    }

    function getNumberofPages($disciplineid, $account_id = false, $api_end_point = false ) {
        //Get the feed URL
        $date_range = !empty( $_COOKIE['daterange'] ) ? $_COOKIE['daterange'] : '7';
        $json 		= file_get_contents( $api_end_point.'/discipline/buildings_by_discipline?account_id='.$account_id.'&discipline_id='.$disciplineid.'&where[date_range]='.$date_range.'&limit=15&offset=0' );
        
        //Decode the JSON
        $builddisciplines = json_decode($json);
        
        //Return feed from data level
        return !empty( $builddisciplines->counters ) ? $builddisciplines->counters->pages : false;
    }