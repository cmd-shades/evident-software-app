<?php
    $section = explode("/", $_SERVER["SCRIPT_NAME"]);
    $appDir  = $_SERVER["DOCUMENT_ROOT"]."/".$section[1]."/";
    
    require_once $appDir.'application/libraries/tcpdf/tcpdf.php';
    
    
    // Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF
{

    //Page header
    public function Header()
    {
        // Logo
        $this->Image(PDF_HEADER_LOGO, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        // Title
        $this->Cell(0, 15, '<< TCPDF Example 003 >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer()
    {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}
    
    
    // Extend the TCPDF class to create custom Header and Footer
    /*class MYPDF extends TCPDF {
        
        public function __construct(){
            $this->load   = clone load_class('Loader');
        }

        //Page header
        public function Header() {            
            // set background image
            // $section =explode("/", $_SERVER["SCRIPT_NAME"]);
            // $appDir  =$_SERVER["DOCUMENT_ROOT"]."/".$section[1]."/";
            // $img_file = $appDir.'/assets/images/logos/ssid-logo-2.png';    
            //$this->Image( PDF_HEADER_LOGO, 0, 0, 210, 297, '', '', 'C', false, 300, '', false, false, 0);
            $this->Image( PDF_HEADER_LOGO, 120, 0, 50, 40, 'PNG', '', 'C', true, 300, '', false, false, 0, false, false, false);
            //$this->Image( PDF_HEADER_LOGO, 10, 10, PDF_HEADER_LOGO_WIDTH, '', 'PNG', '', 'C', false, 300, 'R', false, false, 0, false, false, false);
        }

        #<img src="' .$appDir. '\assets\img\logo.png" border="0" height="116" width="126" />
        
        // Page footer
        public function Footer() {
            //$image_file = "img/bg_bottom_releve.jpg";
            $image_file = $this->appDir.'/assets/img/logo.jpg';    
            
            // Position at 15 mm from bottom
            $this->SetY(-15);
            // Page number
            $this->SetFont('helvetica', 'I', 6);
            //$this->Cell(0, 3, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
        }
    }*/
    
    
    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Simply SID Ltd');
    $pdf->SetTitle('');
    $pdf->SetSubject('');
    $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

    // set default header data
    // $pdf->SetHeaderData(
        // PDF_HEADER_LOGO,
        // PDF_HEADER_LOGO_WIDTH,
        // PDF_HEADER_TITLE.'',
        // PDF_HEADER_STRING, 
        // array(0,64,255),
        // array(255,255,255)
    // );
    
    //$pdf->setHeaderData($ln='', $lw=0, $ht='', $hs='<table cellspacing="0" cellpadding="1" border="1"><tr><td rowspan="3">test</td><td>test</td></tr></table>', $tc=array(0,0,0), $lc=array(0,0,0));
    
    //Set default footer
    $pdf->setFooterData(array(0,64,0), array(0,64,128));

    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // set auto page breaks
    $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    include_once dirname(__FILE__).'/lang/eng.php';
    $pdf->setLanguageArray($l);
}

    // ---------------------------------------------------------

    // set default font subsetting mode
    $pdf->setFontSubsetting(true);

    // Set font
    // dejavusans is a UTF-8 Unicode font, if you only need to
    // print standard ASCII chars, you can use core fonts like
    // helvetica or times to reduce file size.
    $pdf->SetFont('dejavusans', '', 14, '', true);

    // Add a page
    // This method has several options, check the source code documentation for more information.
    $pdf->AddPage();

    // set text shadow effect
    $pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

    // Set some content to print
    //$html = '<p><br/>Welcome 1234</p>';
    $html = $html_content;

    // Print text using writeHTMLCell()
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

    // ---------------------------------------------------------

    // Close and output PDF document
    // This method has several options, check the source code documentation for more information.
    $pdf->Output('example_001.pdf', 'I');
    
    //echo "Simply SID Limited - Phoenix";

