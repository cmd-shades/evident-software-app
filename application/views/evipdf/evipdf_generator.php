<?php if ( !defined('BASEPATH') ) exit('No direct script access allowed');

	$section 		= explode("/", $_SERVER["SCRIPT_NAME"]);
	$appDir  		= $_SERVER["DOCUMENT_ROOT"]."/".$section[1]."/";
	$appDir  		= str_replace( 'index.php/', '', $appDir );
	$account_id 	= !empty( $document_setup['document_setup']['document_content']->account_id ) 		? $document_setup['document_setup']['document_content']->account_id 	: false;
	$custom_footer  = !empty( $document_setup['document_setup']['generic_details']['custom_footer'] ) 	? $document_setup['document_setup']['generic_details']['custom_footer'] : COMPANY_ADDRESS_SUMMARYLNE;

	/************ SETUP PDF SETTINGS **********/
	$mpdf = new \Mpdf\Mpdf( 
	[
		'mode' => 'utf-8', 
		'format' => 'A4-P', 
		'setAutoTopMargin' => 'stretch', 
		'setAutoBottomMargin' => 'stretch',
		'fontDir' => [
       		 __DIR__ . '/fonts',
		],
		'fontdata' => [
			'frutiger' => [
				'R' => 'roboto-v20-latin-300.ttf'
			]
		],
		'default_font' => 'frutiger'
	] );

	$mpdf->curlAllowUnsafeSslRequests 	= true;
	$mpdf->shrink_tables_to_fit 		= 1;
	$mpdf->keep_table_proportions 		= true;
	$mpdf->SetHTMLFooter( Footer( $account_id, $custom_footer ) );
	
	function Footer( $account_id = false, $custom_footer = false ) {

		if( !empty( $account_id ) && in_array( $account_id, [2] ) ){
			
			$footertext 	 = '<p style="font-size:10px;text-align:right">Page {PAGENO}</p><hr><div style="text-align: center; font-size: 11px; margin-top: 5px; color:#696969;">'.( !empty( $custom_footer ) ? $custom_footer : COMPANY_ADDRESS_SUMMARYLNE ).'';

		} else {
			
			$footertext 	 = '<p style="font-size:10px;text-align:right">Page {PAGENO}</p><hr><div style="text-align: center; font-size: 11px; margin-top: 5px; color:#696969;">'.COMPANY_ADDRESS_SUMMARYLNE.'';
			$footertext 	.= '<br><span style="text-align: center; margin-top: 10px; font-size: 11px; color:#696969;"><strong>Tel</strong> '.COMPANY_TELEPHONE.', <strong>Fax</strong> '.COMPANY_FAX.', Registered in England No. '.COMPANY_REGISTRATION_NO.', <strong>VAT registered No.</strong> '.COMPANY_VAT_REGISTRATION_NO.'</span></div>';
		}

		return $footertext;
	}
	
	/************  END PDF SETTINGS  **********/
	
	
	
	/* CHOOSE THE PDF TEMPLATE NAME */
	if( !empty( $template_name ) ){
		$this->load->view( $template_name, ['mpdf' => $mpdf, 'document_setup' => $document_setup]);
	} else {
		$this->load->view( 'evipdf/templates/audit_pdf_template', ['mpdf' => $mpdf, 'document_setup' => $document_setup]);
	}
	
	/* OUTPUT THE DOCUMENT */
	
	$document_name = !empty( $document_setup['generic_details']['document_name'] ) ? "EVIDOC - " . $document_setup['generic_details']['document_name'] : 'EVIDOC';
	$mpdf->Output( $document_name ." " . date("Y-m-d") . ".pdf","I");
	
	

?>