<?php

/*
 | --------------------------------------------------------------------
 | App Namespace
 | --------------------------------------------------------------------
 |
 | This defines the default Namespace that is used throughout
 | CodeIgniter to refer to the Application directory. Change
 | this constant to change the namespace that all application
 | classes should use.
 |
 | NOTE: changing this will require manually modifying the
 | existing namespaces of App\* namespaced-classes.
 */
defined('APP_NAMESPACE') || define('APP_NAMESPACE', 'App');

/*
 | --------------------------------------------------------------------------
 | Composer Path
 | --------------------------------------------------------------------------
 |
 | The path that Composer's autoload file is expected to live. By default,
 | the vendor folder is in the Root directory, but you can customize that here.
 */
defined('COMPOSER_PATH') || define('COMPOSER_PATH', ROOTPATH . 'vendor/autoload.php');

/*
 |--------------------------------------------------------------------------
 | Timing Constants
 |--------------------------------------------------------------------------
 |
 | Provide simple ways to work with the myriad of PHP functions that
 | require information to be in seconds.
 */
defined('SECOND') || define('SECOND', 1);
defined('MINUTE') || define('MINUTE', 60);
defined('HOUR')   || define('HOUR', 3600);
defined('DAY')    || define('DAY', 86400);
defined('WEEK')   || define('WEEK', 604800);
defined('MONTH')  || define('MONTH', 2_592_000);
defined('YEAR')   || define('YEAR', 31_536_000);
defined('DECADE') || define('DECADE', 315_360_000);

/*
 | --------------------------------------------------------------------------
 | Exit Status Codes
 | --------------------------------------------------------------------------
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
defined('EXIT_SUCCESS')        || define('EXIT_SUCCESS', 0);        // no errors
defined('EXIT_ERROR')          || define('EXIT_ERROR', 1);          // generic error
defined('EXIT_CONFIG')         || define('EXIT_CONFIG', 3);         // configuration error
defined('EXIT_UNKNOWN_FILE')   || define('EXIT_UNKNOWN_FILE', 4);   // file not found
defined('EXIT_UNKNOWN_CLASS')  || define('EXIT_UNKNOWN_CLASS', 5);  // unknown class
defined('EXIT_UNKNOWN_METHOD') || define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     || define('EXIT_USER_INPUT', 7);     // invalid user input
defined('EXIT_DATABASE')       || define('EXIT_DATABASE', 8);       // database error
defined('EXIT__AUTO_MIN')      || define('EXIT__AUTO_MIN', 9);      // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      || define('EXIT__AUTO_MAX', 125);    // highest automatically-assigned error code

/**
 * @deprecated Use \CodeIgniter\Events\Events::PRIORITY_LOW instead.
 */
define('EVENT_PRIORITY_LOW', 200);

/**
 * @deprecated Use \CodeIgniter\Events\Events::PRIORITY_NORMAL instead.
 */
define('EVENT_PRIORITY_NORMAL', 100);

/**
 * @deprecated Use \CodeIgniter\Events\Events::PRIORITY_HIGH instead.
 */
define('EVENT_PRIORITY_HIGH', 10);

define('VIEWPATH', APPPATH . 'Views' . DIRECTORY_SEPARATOR);

// Path to the system directory
define('BASEPATH', dirname(APPPATH) . DIRECTORY_SEPARATOR);

define('SYSPATH', dirname(APPPATH) . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR);

// defined('BASEPATH') OR exit('No direct script access allowed');

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
defined('SHOW_DEBUG_BACKTRACE') or define('SHOW_DEBUG_BACKTRACE', true);

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
defined('FILE_READ_MODE') or define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') or define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE') or define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE') or define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ') or define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE') or define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE') or define(
	'FOPEN_WRITE_CREATE_DESTRUCTIVE',
	'wb'
); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE') or define(
	'FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',
	'w+b'
); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE') or define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE') or define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT') or define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT') or define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

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
defined('EXIT_SUCCESS') or define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR') or define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG') or define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE') or define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS') or define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') or define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT') or define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE') or define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN') or define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX') or define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

/*
|---------- API CONFIGS ---------- |*/
defined('SERVICE_END_POINT') or define('SERVICE_END_POINT', 'serviceapp/api/'); // no errors
defined('COOKIE_DOMAIN') or define('COOKIE_DOMAIN', ''); // no errors
defined('API_JWT_ALGORITHM') or define('API_JWT_ALGORITHM', 'HS512'); // JWT Default Algorithm
defined('API_SECRET_KEY') or define(
	'API_SECRET_KEY',
	'fa973f03622b7b02bd1b8ef4d9525273edbdf6a6'
); // JWT Key production version
defined('API_SECRET_KEY_TEST') or define(
	'API_SECRET_KEY_TEST',
	'237a2edf4bb55407f086267ec28c949350533a1a'
); // JWT Key production version TEST

/**------ ACCOUNT TRIAL PERIOD ------ **/
defined('API_ACCOUNT_TRIAL_PERIOD') or define('API_ACCOUNT_TRIAL_PERIOD', '3 Months'); //Trial period in months


/*
|-------- ADDRESS LOOKUP API ----- | */

if(!defined('STDIN') ){
	if (in_array($_SERVER['REMOTE_ADDR'], ["127.0.0.1", "::1"])) {
		define('API_SEARCH_KEY', 'PCWK9-K775Q-XJKZF-YRPL8'); //Test Account
	} else {
		define('API_SEARCH_KEY', 'PCW7Z-P5CKZ-CJVJ5-ZCPP3'); //Live Account
	}
} else {
	define('API_SEARCH_KEY', NULL);
}


// define( "WWO_SOCKET", "62.133.28.58" ); ## WebWayOne socket for LDTV - original - test environment
define("WWO_SOCKET", "37.152.38.1203"); ## WebWayOne socket for LDTV
define("WWO_SOCKET2", "62.8.115.203"); ## WebWayOne socket for LDTV - alternative, not used currently

/* ---------- APP SETTINGS -----------*/
defined('APP_NAME') or define('APP_NAME', 'Evident Software CMS');
defined('APP_VERSION') or define('APP_VERSION', '2.10.19');
defined('APP_TAG_LINE') or define('APP_TAG_LINE', 'What you need to know, when you need to know it');
defined('APNS_CERTIFICATE') or define('APNS_CERTIFICATE', 'WolfAlertCertificate.pem'); //APNS Certificate from Apple
defined('APNS_CERTIFICATES_PATH') or define('APNS_CERTIFICATES_PATH', 'assets/apns-certificates/'); // no errors

/** SYSTEM DEFAULT LIMIT / OFFSET **/
define('SUPER_ADMIN_ACCESS', [1, 171, 331]);
defined('NO_WEB_ACCESS_USER_TYPES') or define('NO_WEB_ACCESS_USER_TYPES', [3]);
defined('EXTERNAL_USER_TYPES') or define('EXTERNAL_USER_TYPES', [4, 5]);
defined('DEFAULT_LIMIT') or define('DEFAULT_LIMIT', 15);
defined('DEFAULT_MAX_LIMIT') or define('DEFAULT_MAX_LIMIT', 9999);
defined('DEFAULT_OFFSET') or define('DEFAULT_OFFSET', 0);
defined('DEFAULT_PASSWORD') or define('DEFAULT_PASSWORD', 'W3lc0m3!');
defined('DEFAULT_USER_TYPE') or define('DEFAULT_USER_TYPE', 2);
defined('DEFAULT_PERIOD_DAYS') or define('DEFAULT_PERIOD_DAYS', 30); //Days 30 / 60 / 90 / 180 / 365
defined('DEFAULT_AUDIT_REQ_PERCENTAGE') or define('DEFAULT_AUDIT_REQ_PERCENTAGE', 100); //Days 30 / 60 / 90 / 180 / 365
// defined('DEFAULT_TOKEN_VALIDITY') OR define( 'DEFAULT_TOKEN_VALIDITY', 28800 ); //8 Hours in Seconds - Default value, changed 15/06/2020 upon TSG request
defined('DEFAULT_TOKEN_VALIDITY') or define('DEFAULT_TOKEN_VALIDITY', 32400); //9 Hours in Seconds
// defined('DEFAULT_TOKEN_VALIDITY') OR define( 'DEFAULT_TOKEN_VALIDITY', 120 ); //2 minutes in Seconds

/*------------ COMPANY DETAILS --------------*/
################################## DEFINE COMPANY DETAILS #########################################
######################### Idealy these should come from the DB per company ########################

defined('COMPANY_NAME') or define('COMPANY_NAME', 'Evident Software Limited');
defined('COMPANY_LOGO') or define('COMPANY_LOGO', '/assets/images/logos/WA-logo-final.png');
defined('COMPANY_SLOGAN') or define('COMPANY_SLOGAN', 'Everything you need in one safe and secure place');
defined('COMPANY_REGISTRATION_NO') or define('COMPANY_REGISTRATION_NO', '0208045676');
defined('COMPANY_VAT_REGISTRATION_NO') or define('COMPANY_VAT_REGISTRATION_NO', '000-000-000');
defined('COMPANY_FAX') or define('COMPANY_FAX', '0208045676');
defined('COMPANY_TELEPHONE') or define('COMPANY_TELEPHONE', '0208045676');
defined('ADDRESS_LINE1') or define('ADDRESS_LINE1', 'Unit 1 Mariner Business Center');
defined('ADDRESS_LINE2') or define('ADDRESS_LINE2', 'Kings Way');
defined('ADDRESS_LINE3') or define('ADDRESS_LINE3', 'Evident Software Limited');
defined('ADDRESS_TOWN') or define('ADDRESS_TOWN', 'Croydon');
defined('ADDRESS_COUNTY') or define('ADDRESS_COUNTY', 'Croydon');
defined('ADDRESS_POSTCODE') or define('ADDRESS_POSTCODE', 'CR0 4GE');
defined('COMPANY_ADDRESS_SUMMARYLNE') or define(
	'COMPANY_ADDRESS_SUMMARYLNE',
	'Evident Software Limited, Unit 3, Rooks Nest, Godstone, Surrey, RH9 8BY'
);
defined('DOCUMENT_POWERED_BY') or define('DOCUMENT_POWERED_BY', 'EviDoc&trade; powered by Evident Software');


#####################################################################################################

/* Default module price for the module (Tier 1 and Tier 2) */
defined('MODULE_PRICE') or define('MODULE_PRICE', 10);
defined('MODULE_PRICE_MANAGEMENT') or define('MODULE_PRICE_MANAGEMENT', 100);
defined('MODULE_PRICE_INTELLIGENCE') or define('MODULE_PRICE_INTELLIGENCE', 300);
defined('STRING_ENCRYPTION_KEY') or define('STRING_ENCRYPTION_KEY', 'qJB0rGtIn5UB1xG03efyCp');
defined('STRING_ENCRYPTION_CYPHER_METHOD') or define('STRING_ENCRYPTION_CYPHER_METHOD', 'aes-128-ctr');

defined('GOOGLE_API_KEY') or define('GOOGLE_API_KEY', 'AIzaSyCYdFFWPQ5XLKAv4nDywPT6SXootbK6NIY');

/* ---- TESSERACT Api Configs ------ */
#defined( 'TESSERACT_API_BASE_URL' )  		OR define( 'TESSERACT_API_BASE_URL', 'http://tesseract.co.uk/asmx/ServiceCentreAPI.asmx?wsdl' );
define('TESSERACT_API_BASE_URL', 'https://rea.sccialphatrack.co.uk/SC51/asmx/ServiceCentreAPI.asmx?wsdl');
define('TESSERACT_API_AUTH_USER', 'EKabungo');
define('TESSERACT_API_AUTH_PWD', 'Eno-0807');
define('TESSERACT_BRIDGE_API_BASE_URL', 'http://neweviapi.sccialphatrack.co.uk:8080/service-api/');
define('TESSERACT_ATTACHMENTS_PATH_NAME', 'D:\inetpub\wwwroot\SC51\attachments');
define('TESSERACT_LINKED_ACCOUNTS', [8]);

/* ------- DISCIPLINE DASHBOADS CONFIGS --------- */
define('APP_ROOT_FOLDER', 'evident-core'); //With a leading slash
define('CLIENT_ACCESS_TOKEN', 'AIzaSyCYdFFWPQ5XLKA');
define('SCHEDULE_CLONE_DEFAULT_LIMIT', 800);
