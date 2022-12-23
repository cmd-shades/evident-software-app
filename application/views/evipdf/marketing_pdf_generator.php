<?php if ( !defined('BASEPATH') ) exit('No direct script access allowed');

## Specify the app path
$section 		= explode( "/", $_SERVER["SCRIPT_NAME"] );

if( !isset( $section[1] ) || empty( $section[1] ) || ( !( is_array( $section ) ) ) ){
	$app_root = substr( dirname( __FILE__ ), 0, strpos( dirname( __FILE__ ), "application" ) );
} else {
	if ( !isset( $_SERVER["DOCUMENT_ROOT"] ) || ( empty( $_SERVER["DOCUMENT_ROOT"] ) ) ){
		$_SERVER["DOCUMENT_ROOT"] = realpath( dirname(__FILE__).'/../' );
	}

	$app_root		= $_SERVER["DOCUMENT_ROOT"]."/".$section[1]."/";
	$app_root		= str_replace( '/index.php','',$app_root );
}

## Debugging:
// $section_1 		= !empty( $section[1] ) ? $section[1] : 'techlive' ;
// log_message( "error", json_encode( ["marketing generation - section" => $section] ) );
// log_message( "error", json_encode( ["marketing generation - app_root" => $app_root] ) );

## Build a path to upload the PDF
$account_id 	= ( isset( $account_id ) && !empty( $account_id ) ) ? $account_id : 1 ;
$pdf_path 		= '_marketing_pdf/'.$account_id.'/';
$save_path  	= $app_root.$pdf_path;
// $save_path  	= $appDir.$pdf_path;

if( !is_dir( $save_path ) ){
	if( !mkdir( $save_path, 0755, true ) ){
		$this->session->set_flashdata('message', 'Error: Unable to create upload location');
		return false;
	}
}

## specify a target where it needs to be stored - for CRON - locally
$target = ( isset( $pdf_target ) && !empty( $pdf_target ) ) ? $pdf_target : "download" ;

## build a file name and finish building the full path
if( strtolower( $target ) == "cron-stored" ){
	$file_name 				= "Marketing_PDF.pdf";
	$full_path 				= $save_path.$file_name;
} else {
	$current_time 			= date( 'Y-m-d_H-i-s' );
	$file_name 				= "Marketing_PDF_".$current_time.'.pdf';
	$full_path 				= $save_path.$file_name;
}

## 'ob_end_clean' is needed in order to produce the PDF!
ob_end_clean();
$mpdf = new \Mpdf\Mpdf([
	'format' 			=> 'A4',
	'margin_left' 		=> 5,
	'margin_right' 		=> 5,
	'margin_top' 		=> 5,
	'margin_bottom' 	=> 5,
	'margin_header' 	=> 5,
	'margin_footer' 	=> 5,
	'mode' 				=> 'utf-8', 
]);


## include CSS and HTMl template
include_once( "templates\marketing_001_css.php" );
include_once( "templates\marketing_001_html.php" );

$mpdf->SetDisplayMode( 'fullpage' );
// $mpdf->WriteHTML( $style, 1 ); // 1 is for style sheet
// $mpdf->WriteHTML( $html, 2 ); // 2 is for html body
$mpdf->showImageErrors = true;

try{
	if( strtolower( $target ) == "cron-stored" ){
		$mpdf->Output( $full_path, \Mpdf\Output\Destination::FILE );  ## If the PDF needs to be stored
	} else {
		$mpdf->Output( $file_name, \Mpdf\Output\Destination::DOWNLOAD );
	}
} catch( Exception $ex ){
    return false;
}