<?php if ( !defined('BASEPATH') ) exit('No direct script access allowed');

	$section = explode("/", $_SERVER["SCRIPT_NAME"]);
	$appDir  = $_SERVER["DOCUMENT_ROOT"]."/".$section[1]."/";
	$appDir  = str_replace( 'index.php/', '', $appDir );
	// Page footer
	function Footer() {
		$footertext 	 = '<p style="font-size:10px;text-align:right">Page {PAGENO}</p><hr><div style="text-align: center; font-size: 11px; margin-top: 5px; color:#696969;">'.COMPANY_ADDRESS_SUMMARYLNE.'';
		$footertext 	.= '<br><span style="text-align: center; margin-top: 10px; font-size: 11px; color:#696969;"><strong>Tel</strong> '.COMPANY_TELEPHONE.', <strong>Fax</strong> '.COMPANY_FAX.', Registered in England No. '.COMPANY_REGISTRATION_NO.', <strong>VAT registered No.</strong> '.COMPANY_VAT_REGISTRATION_NO.'</span></div>';
		return $footertext;
	}

	$mpdf = new \Mpdf\Mpdf( ['mode' => 'utf-8', 'format' => 'A4-P', 'setAutoTopMargin' => 'stretch', 'setAutoBottomMargin' => 'stretch', 'default_font' => 'sans-serif'] );

	$mpdf->useSubstitutions 			= false;
	$mpdf->simpleTables 				= true;
	$mpdf->curlAllowUnsafeSslRequests 	= true;

	$document_name = !empty( $document_setup['generic_details']['document_name'] ) ? "EVIDOC - " . $document_setup['generic_details']['document_name'] : 'EVIDOC';
	
	$logo_img_file = $appDir.'/assets/images/logos/main-logo-small.png';

	$mpdf->SetTitle( $document_name );

	$mpdf->SetHTMLHeader("<table style='width:100%;'> <tr> <td align='center'> <img src='" . $logo_img_file . "' width='80px'/> </td> </tr> </table>");

	$mpdf->SetHTMLFooter( Footer() );

	$mpdf->WriteHTML( $html_content );
	
	$mpdf->Output( $document_name ." " . date("Y-m-d") . ".pdf","I");
