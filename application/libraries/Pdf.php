<?php

namespace App\Libraries;

require_once('/application/libraries/tcpdf/tcpdf.php');

// Extend the TCPDF class to create custom Header and Footer
class Pdf extends TCPDF
{
    function __construct()
    {
        parent::__construct();
        $this->section = explode("/", $_SERVER["SCRIPT_NAME"]);
        $this->appDir  = $_SERVER["DOCUMENT_ROOT"] . "/" . $this->section[1] . "/";

        // create new PDF document
        $this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    }

    //Page header
    public function Header()
    {

        $img_file = $this->appDir . '/assets/images/logos/ssid-logo.png';

        // Logo
        //$this->setJPEGQuality(90);
        $this->Image($img_file, 94, 10, 20, '', 'PNG', '', 'C', false, 300, '', false, false, 0, false, false, false);
        $this->SetFont('helvetica', 'B', 20);
        $this->Cell(0, 25, '', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer()
    {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

    public function create_pdf($htm_content = false)
    {

        // set document information
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('Nicola Asuni');
        $this->pdf->SetTitle('');
        $this->pdf->SetSubject('');
        $this->pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        // set default header data
        $this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING, [0,64,255], [255,255,255]);

        // set header and footer fonts
        $this->pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        // set default monospaced font
        $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        // set margins
        $this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        // set auto page breaks
        $this->pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        // set image scale factor
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $this->pdf->setLanguageArray($l);
        }

        // ---------------------------------------------------------
        // set font
        $this->pdf->SetFont('times', 'BI', 12);
        // add a page
        $this->pdf->AddPage();
        // set some text to print
        $htm_content = 'TCPDF Example 003
		Custom page header and footer are defined by extending the TCPDF class and overriding the Header() and Footer() methods.';
        // print a block of text using Write()
        $this->pdf->Write(0, $htm_content, '', 0, 'C', true, 0, false, false, 0);
        // ---------------------------------------------------------
        //Close and output PDF document
        $this->pdf->Output('example_003.pdf', 'I');
    }
}
