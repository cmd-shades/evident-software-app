<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

/*
|---------- API CONFIGS ---------- |*/
defined('SERVICE_END_POINT')	OR define('SERVICE_END_POINT', 'serviceapp/api/'); // no errors
defined('COOKIE_DOMAIN')		OR define('COOKIE_DOMAIN', ''); // no errors
defined('API_JWT_ALGORITHM')   	OR define('API_JWT_ALGORITHM', 'HS512'); // JWT Default Algorithm
defined('API_SECRET_KEY')   	OR define('API_SECRET_KEY', 'fa973f03622b7b02bd1b8ef4d9525273edbdf6a6'); // JWT Key production version
defined('API_SECRET_KEY_TEST')  OR define('API_SECRET_KEY_TEST', '237a2edf4bb55407f086267ec28c949350533a1a'); // JWT Key production version TEST

/**------ ACCOUNT TRIAL PERIOD ------ **/
defined('API_ACCOUNT_TRIAL_PERIOD')  OR define('API_ACCOUNT_TRIAL_PERIOD', '3 Months'); //Trial period in months


## ORIG:: if( in_array( $_SERVER['REMOTE_ADDR'], array( "127.0.0.1", "::1" ) ) ){	

if( !isset( $_SERVER['REMOTE_ADDR'] ) || ( isset( $_SERVER['REMOTE_ADDR'] ) && in_array( $_SERVER['REMOTE_ADDR'], array( "127.0.0.1", "::1" ) ) ) ){	
	define('API_SEARCH_KEY',		'PCWK9-K775Q-XJKZF-YRPL8'); //Test Account
}else{
	define('API_SEARCH_KEY',		'PCW7Z-P5CKZ-CJVJ5-ZCPP3'); //Live Account
}

/* ---------- CaCTI SETTINGS -----------*/
defined('APP_NAME') 					OR define('APP_NAME', 'TechLive');
defined('APP_VERSION') 					OR define('APP_VERSION', '1.0');
defined('APP_TAG_LINE')					OR define('APP_TAG_LINE', 'TechLive CMS');
defined('APNS_CERTIFICATE')				OR define('APNS_CERTIFICATE', 'WolfAlertCertificate.pem'); //APNS Certificate from Apple
defined('APNS_CERTIFICATES_PATH')		OR define('APNS_CERTIFICATES_PATH', 'assets/apns-certificates/'); // no errors	

/** SYSTEM DEFAULT LIMIT / OFFSET **/
defined('DEFAULT_LIMIT')				OR define('DEFAULT_LIMIT', 15);
defined('DEFAULT_MAX_LIMIT')			OR define('DEFAULT_MAX_LIMIT', 99999);
defined('DEFAULT_OFFSET')				OR define('DEFAULT_OFFSET', 0);
defined('DEFAULT_PASSWORD')				OR define('DEFAULT_PASSWORD', 'W3lc0m3!' );
defined('DEFAULT_USER_TYPE')			OR define('DEFAULT_USER_TYPE', 2 );

/*------------ COMPANY DETAILS --------------*/
################################## DEFINE COMPANY DETAILS #########################################
######################### Ideally these should come from the DB per company ########################

defined('COMPANY_NAME')  				OR define('COMPANY_NAME', 'TechLive' );
defined('COMPANY_LOGO')  				OR define('COMPANY_LOGO', '' );
defined('COMPANY_SLOGAN')  				OR define('COMPANY_SLOGAN', '');
defined('COMPANY_REGISTRATION_NO')  	OR define('COMPANY_REGISTRATION_NO', '');
defined('COMPANY_VAT_REGISTRATION_NO')  OR define('COMPANY_VAT_REGISTRATION_NO', '');
defined('COMPANY_FAX')  				OR define('COMPANY_FAX', '' );
defined('COMPANY_TELEPHONE')  			OR define('COMPANY_TELEPHONE', '' );
defined('ADDRESS_LINE1')  				OR define('ADDRESS_LINE1', '' );
defined('ADDRESS_LINE2')  				OR define('ADDRESS_LINE2', '' );
defined('ADDRESS_LINE3')  				OR define('ADDRESS_LINE3', '' );
defined('ADDRESS_TOWN')  				OR define('ADDRESS_TOWN', '' );
defined('ADDRESS_COUNTY')  				OR define('ADDRESS_COUNTY', '' );
defined('ADDRESS_POSTCODE')  			OR define('ADDRESS_POSTCODE', '' );
defined('COMPANY_ADDRESS_SUMMARYLNE')  	OR define('COMPANY_ADDRESS_SUMMARYLNE', '');

#####################################################################################################
/* ---- OMDb Fetch Configuration ---- */
defined( 'OMDb_API_KEY' ) 				OR define( 'OMDb_API_KEY', 'f16f97f8' ); ## DEV Team API key

/* ---- The constants needed for the Site monthly value ---- */
defined( 'MONTHLY_SITE_VALUE' ) 		OR define( 'MONTHLY_SITE_VALUE', '30.4375' ); ## A static value for the site for a month provided by TechLive: 365.25 / 12

/* ---- Movies Location ---- */
defined( 'ALLOWED_MOVIE_PATH' )  		OR define( 'ALLOWED_MOVIE_PATH', '/assets/movies/' );
defined( 'PREP_FOLDER' )  				OR define( 'PREP_FOLDER', 'C:/Web/Ingestion/Prep/' );
defined( 'PROCESSED_FOLDER' )  			OR define( 'PROCESSED_FOLDER', 'C:/Web/Ingestion/Processed/' );

/* ---- Stream Decoding values ---- */
defined( 'TRAILER_MAX_FILE_SIZE' ) 		OR define( 'TRAILER_MAX_FILE_SIZE', 500000000 );
defined( 'SD_MAX_BIT_RATE' )  			OR define( 'SD_MAX_BIT_RATE', 7000000 );

defined( 'LIBRARY_AGING_PERIOD_IN_DAYS' )  		OR define( 'LIBRARY_AGING_PERIOD_IN_DAYS', 18 );
defined( 'LIBRARY_AGING_PERIOD_IN_MONTHS' )  	OR define( 'LIBRARY_AGING_PERIOD_IN_MONTHS', 547 );


/* ---- Easel TV API Configs ------ */
## Test server
// defined( 'EASEL_TV_API_BASE_URL' )  	OR define( 'EASEL_TV_API_BASE_URL', 'https://arw-uat.suggestedtv.com/api/dashboard/v1/' );
## Live server
defined( 'EASEL_TV_API_BASE_URL' )  	OR define( 'EASEL_TV_API_BASE_URL', 'https://arw.suggestedtv.com/api/dashboard/v1/' );

#defined( 'EASEL_TV_API_AUTH_STRING' )  OR define( 'EASEL_TV_API_AUTH_STRING', 'wojciechcupa@evidentsoftware.co.uk:539LZojSFo' );//Username:password
defined( 'EASEL_TV_API_AUTH_STRING' )  	OR define( 'EASEL_TV_API_AUTH_STRING', 'airtime@techlive.co.uk:NR6Nn7tmWf' );//Username:password

define( 'EASEL_WEBHOOK_SIGNINGSECRET'	, 'UgHlovRf1osdY4lF/n1nRD7PXQTEJGG0mAhyM9izoFA=' );
define( 'EASEL_WEBHOOK_DEBUG'			, TRUE );
define( 'EASEL_UAT_API_KEY'				, 'cMAXmalt3C1NpdrEjKib' ); #UAT
define( 'EASEL_PROD_API_KEY'			, 'f15t1eF7kSkt0U0ZqpWW' ); #Production'
define( 'EASEL_UPDATE_DEBUG'			, TRUE );


/* ----- BUNDLE PREPARATION LOCATIONS ----*/
defined( 'CDS_PICKUP_LOCATION' )  		OR define( 'CDS_PICKUP_LOCATION', 'C:/Web/Ingestion/Processed/' );
defined( 'CDS_DESTINATION_LOCATION' )  	OR define( 'CDS_DESTINATION_LOCATION', 'D:/CDS/Distribution/' );//E.g.E:\Distribution\directstreamshq_202007141612\abominable


/* ----- Coggins API Configs ----*/
defined( 'COGGINS_API_BASE_URL' )  		OR define( 'COGGINS_API_BASE_URL', 'http://coggins.techliveint.co.uk/v1/' );
defined( 'COGGINS_ADD_QUEUE_PATH' )  	OR define( 'COGGINS_ADD_QUEUE_PATH', '/srv/Content/Cacti/' );
defined( 'AWS_BUCKET_NAME' )  			OR define( 'AWS_BUCKET_NAME', 'easeltv' );
defined( 'AWS_BUCKET_NAME_TEST' )  		OR define( 'AWS_BUCKET_NAME_TEST', 'basilica' );

define( 'AWS_TOKEN'						, 'jkhfieuhf8734yriwh493rhioerf98re' );

defined( 'COGGINS_TEMP_TOKEN' )  		OR define( 'COGGINS_TEMP_TOKEN','eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJjYWN0aSIsImlzcyI6InRlY2hsaXZlIiwic3ViIjoiYWRtaW5AdGVjaGxpdmUuY28udWsiLCJzY29wZSI6ImF3c0NyZWF0ZUJ1Y2tldCxhd3NOb3RpZnlUcmFuc2ZlcjEsYXdzTm90aWZ5VHJhbnNmZXIyLGF3c1RyYW5zZmVyLGF3c1RyYW5zZmVyMSxhd3NUcmFuc2ZlcjIsYXdzVXBsb2FkLGNkc0NhbmNlbCxjZHNDb21wbGV0ZWQsY2RzQ29udGVudCxjZHNSdW5uaW5nLGNkc1NlcnZlcnMscXVldWVBZGQscXVldWVDYW5jZWwscXVldWVEZWxldGUscXVldWVGaW5pc2hlZCxxdWV1ZVJlc2V0LHF1ZXVlUnVubmluZyxxdWV1ZVdhaXRpbmciLCJjZHMiOnsidXNyIjoiYmFzaWxAYWlyd2F2ZS50diIsInB3ZCI6IkExcndhdjMifSwiaWF0IjoxNjQ3NTE3OTQzfQ.4Y9e17Y0I-EveAOFKOA8xssOSMl0G8dtg6JXPfG1ZxE' );


/* ----- Age Rating Images Path ----*/
define( 'AGE_RATING_IMAGE_PATH_PDF'			, '\assets\images\age-certificates\\' );

/* ----- Integrator API Configs ----*/
define( 'INTEGRATOR_KEY'					, 'NXyB93kJEXO7e7U7Rj3HRo6pb0R2t07tpEUZskpmWQneY1X96FCc4c8ilORDXip1' );
// define( 'INTEGRATOR_TOKEN'					, 'ZXlKMGVYQWlPaUpLVjFRaUxDSmhiR2NpT2lKSVV6STFOaUo5LmV5SnBjM01pT2lKRmRtbGtaVzUwSUZOdlpuUjNZWEpsSWl3aWFXRjBJam94TmpZeE5EazROemsyTENKbGVIQWlPakU0TVRreU5qVXhPVFlzSW1GMVpDSTZJa05oUTFSSklpd2ljM1ZpSWpvaVNXNTBaV2R5WVhSdmNpQkJVRWtpTENKR2FYSnpkQ0J1WVcxbElqb2lVbWxqYUdGeVpDSXNJbE4xY201aGJXVWlPaUpGZUdObGJHd2lMQ0pGYldGcGJDSTZJbEpwWTJoaGNtUXVSWGhqWld4c1FGUmxZMmhzYVhabExuUjJJaXdpVW05c1pTSTZXeUpOWVc1aFoyVnlJaXdpVUhKdmFtVmpkQ0JCWkcxcGJtbHpkSEpoZEc5eUlsMTkuYk15RjdkdndvdXVXNlhZR19PaXRBX0ZmSnRQQ1cxT0pKX0FKdjZYRXlfWQ==' );
define( 'INTEGRATOR_TOKEN'					, 'simple-test-header' );
define( 'INTEGRATOR_FIXED_ID'				, 32 );
define( 'LOG_INTEGRATOR_REQUEST'			, true );
define( 'EASEL_INVITED_MARKET_ID'			, 'invited' );
define( 'DELETE_PRICE_BAND_DEBUGGING'		, true );