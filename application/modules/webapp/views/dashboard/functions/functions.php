<?php
    $GLOBALS['root'] 		= ( isset( $_SERVER['HTTPS'] ) ? "https://" : "http://" ) . $_SERVER['HTTP_HOST'];
    $GLOBALS['root'] 		.= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
    $GLOBALS['root_parts'] = explode( 'application', $GLOBALS['root'] );	
    $GLOBALS['base_url']	= !empty( $GLOBALS['root_parts'][0] ) ? $GLOBALS['root_parts'][0] : 'http://localhost/evident-core';
    
    //$GLOBALS['api_end_point']	= 'http://77.68.92.77/evident-core/serviceapp/api/';
	if( strpos( $GLOBALS['base_url'], 'myevident.co.uk' ) !== false ){
		$GLOBALS['api_end_point']	= $GLOBALS['base_url'].'serviceapp/api/';
	} else {
		$GLOBALS['api_end_point']	= 'http://77.68.92.77/evident-core/serviceapp/api/';
	}

    $GLOBALS['counter'] = 1;

    include('functions-overview.php');
    include('functions-discipline.php');
    include('functions-building-discipline.php');
    include('functions-building.php');
    include('functions-outcomes.php');
	
?>