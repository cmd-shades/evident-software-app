<?php if ( !defined('BASEPATH') ) exit('No direct script access allowed');

	require_once( '/application/libraries/tcpdf/tcpdf.php' );

	class MYPDF extends TCPDF {
		
		function __construct(){
			parent::__construct();
			$this->section = explode("/", $_SERVER["SCRIPT_NAME"]);
			$this->appDir  = $_SERVER["DOCUMENT_ROOT"]."/".$this->section[1]."/";
		}
		
		//Page header
		public function Header() {
			// Logo
			$img_file= $this->appDir.'/assets/images/logos/ssid-logo-2.png';
			$this->Image( $img_file, 94, 10, 20, '', 'PNG', '', 'C', false, 300, '', false, false, 0, false, false, false);
			$this->SetFont('helvetica', 'R', 20);
			$this->Cell(0, 25, '', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		}
		
		// Page footer
		public function Footer() {
			$this->SetFont('helvetica', 'I', 8);// Set font	
			$footertext = '<hr><div style="text-align: center; font-size: 11px; margin-top: 5px; color:#696969;">'.COMPANY_ADDRESS_SUMMARYLNE.'</div>';
			$footertext .= '<span style="text-align: center; margin-top: 10px; font-size: 11px; color:#696969;"><strong>Tel</strong> '.COMPANY_TELEPHONE.', <strong>Fax</strong> '.COMPANY_FAX.', Registered in England No. '.COMPANY_REGISTRATION_NO.', <strong>VAT registered No.</strong> '.COMPANY_VAT_REGISTRATION_NO.'</span>';
			$this->writeHTML($footertext, false, true, false, true);			
			$this->SetY(-20);// Position at 15 mm from bottom		
			//$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');// Page number
		}
	}

	// create new PDF document
	$pdf = new MYPDF( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
	
	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('');
	$pdf->SetTitle( '' );
	$pdf->SetSubject('');
	//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
	// set default header data
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING, [0,0,0], [0,0,0]);
	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		require_once(dirname(__FILE__).'/lang/eng.php');
		$pdf->setLanguageArray($l);
	}
	// set font
	$pdf->SetFont('helvetica', 'R', 11);
	
	// add a page
	$pdf->AddPage();
	
	if( !empty( $html_content ) ){
		ob_clean(); // cleaning the buffer before Output()
		ob_end_clean();
		// output the HTML content
		$pdf->writeHTML( $html_content, true, 0, true, 0);
		//Close and output PDF document
		$pdf->Output( $document_setup['generic_details']['document_name'].'.pdf', 'I');
	}
	