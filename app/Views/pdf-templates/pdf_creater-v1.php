<?php if (!defined('BASEPATH') ) { exit('No direct script access allowed');
}
    
    $section = explode("/", $_SERVER["SCRIPT_NAME"]);
    $appDir  = $_SERVER["DOCUMENT_ROOT"]."/".$section[1]."/";

    require_once $appDir.'/application/libraries/tcpdf/tcpdf.php';

class SIDD_PDF extends TCPDF
{
        
    function __construct()
    {
        parent::__construct();
        $this->section = explode("/", $_SERVER["SCRIPT_NAME"]);
        $this->appDir  = $_SERVER["DOCUMENT_ROOT"]."/".$this->section[1]."/";
    }
        
    //Render images into an HTML strong
    public function renderImages( $doc_type = false, $images = false )
    {
        $html_str = false;
        if(!empty($doc_type) && !empty($images) ) {
            switch( $doc_type ){
            case 'audit':
                $html_str = '<br><br><br><br><div style="margin-top:120px;"><h3>Audit Photos</h3><hr></div>';
                $html_str .= '<table text-align="center" style="text-align:center; font-family: Arial, Helvetica, sans-serif;font-size:12px; width:100%; padding:15px;" >';
                            
                foreach( $images  as $segment => $segment_files ){
                    foreach( $segment_files  as $q => $file ){
                        $html_str .= '<tr>';
                        $html_str .= '<td width="5%" >&nbsp;</td>';
                        $html_str .= '<td width="90%" style="text-align:center;" ><img src="'.$file->document_link.'" /></td>';
                        $html_str .= '<td width="5%" >&nbsp;</td>';
                        $html_str .= '</tr>';
                        $html_str .= '<tr>';
                        $html_str .= '<td width="5%" >&nbsp;</td>';
                        $html_str .= '<td width="90%" ><strong>Document name:</strong> '.$file->document_name.'</td>';
                        $html_str .= '<td width="5%" >&nbsp;</td>';
                        $html_str .= '</tr>';
                    }
                }
                            
                $html_str .= '</table>';

                return $html_str;
             break;
                    
            case 'other':
                break;
            }
        }
        return $html_str;
    }
        
    //Page header
    public function Header()
    {
        // Logo
        $img_file= $this->appDir.'/assets/images/logos/ssid-logo-2.png';
        $this->Image($img_file, 94, 10, 20, '', 'PNG', '', 'C', false, 300, '', false, false, 0, false, false, false);
        $this->SetFont('helvetica', 'R', 20);
        $this->Cell(0, 25, '', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }
        
    // Page footer
    public function Footer()
    {
        $this->SetFont('helvetica', 'I', 8);// Set font    
        $footertext = '<hr><div style="text-align: center; font-size: 11px; margin-top: 5px; color:#696969;">'.COMPANY_ADDRESS_SUMMARYLNE.'</div>';
        $footertext .= '<span style="text-align: center; margin-top: 10px; font-size: 11px; color:#696969;"><strong>Tel</strong> '.COMPANY_TELEPHONE.', <strong>Fax</strong> '.COMPANY_FAX.', Registered in England No. '.COMPANY_REGISTRATION_NO.', <strong>VAT registered No.</strong> '.COMPANY_VAT_REGISTRATION_NO.'</span>';
        $this->writeHTML($footertext, false, true, false, true);            
        $this->SetY(-20);// Position at 15 mm from bottom        
        //$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');// Page number
    }
}

    // create new PDF document
    $page_orientation = ( !empty($page_orientation) ) ? $page_orientation : PDF_PAGE_ORIENTATION;
    $pdf = new SIDD_PDF($page_orientation, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('');
    $pdf->SetTitle('');
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
    $pdf->SetAutoPageBreak(true, 0);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    // set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    include_once dirname(__FILE__).'/lang/eng.php';
    $pdf->setLanguageArray($l);
}
    // set font
    $pdf->SetFont('helvetica', 'R', 11);
    
    // add a page
    $pdf->AddPage();
    
if(!empty($html_content) ) {
        
        
    $section = explode("/", $_SERVER["SCRIPT_NAME"]);
    $appDir  = $_SERVER["DOCUMENT_ROOT"]."/".$this->section[1]."/";
        
    ob_clean(); // cleaning the buffer before Output()
    ob_end_clean();
    // output the HTML content
    $pdf->writeHTML($html_content, true, 0, true, 0);
        
    // create some HTML content
    $photo_img = base_url().'/assets/images/logos/ssid-logo-2.png';
        
    $doc_images  = ( !empty($document_setup['data_details']->uploaded_docs) ) ? $document_setup['data_details']->uploaded_docs : null;
        
    if(!empty($doc_images) ) {
        $pdf->AddPage();
        $images_html = $pdf->renderImages('audit', $doc_images);

        // output the HTML content
        $pdf->writeHTML($images_html, true, false, true, false, '');    
    }
            
        
    //Close and output PDF document
    $pdf->Output($document_setup['generic_details']['document_name'].'.pdf', 'I');
}
    