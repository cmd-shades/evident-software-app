<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use Dompdf\Dompdf;

require_once 'dompdf/autoload.inc.php';

class Pdf{

    public function __construct(){
		$this->dompdf = new Dompdf();
		$this->dompdf->set_option('defaultFont', 'Courier');
		$this->dompdf->set_option('isHtml5ParserEnabled', true);
		$this->load   = clone load_class('Loader');
    }

    /** Render parsed HTML to PDF **/
	public function renderHtml( $html_content = false, $output_file = 'Test_' ){
		
		if( !empty( $html_content ) ){

			$timestamp = time();
			
			ob_start();
			
			$this->dompdf->loadHtml( $html_content ); //Load HRML content
			
			ob_end_clean;
			
			$this->dompdf->render(); // Render the HTML as PDF 

			$this->dompdf->stream("$output_file"."_"."$timestamp.pdf"); // Output the generated PDF to Browser
		}
		
		return false;

    }
	
	public function testRender( ){

		$html_data = '<table><tr><th>Name</th></tr><tr><td>Enock Kabungo</td></tr></table>';
		$data['document_setup'] = [
			'company_details'=>[
				'company_logo'=>base_url( '/assets/images/logos/ssid-logo-2.png' ),
				'company_name'=>'Simply SID Limited',
				'company_slogan'=>'Everything you need in one safe and secure place',
				'registration_no'=>'820 6105 72',
				'vat_registration_no'=>'6051241002',
				'telephone'=>'0208333330',
				'fax'=>'0208333330',
				'address_line1'=>'Simply SID Limited',
				'address_line2'=>'125 High Street',
				'address_line3'=>'4th Floor, Grosvenor House',
				'address_town'=>'Croydon',
				'address_country'=>'Surrey',
				'address_postcode'=>'CR0 9XP',
				'address_summaryline'=>'Simply SID Limited, 125 High Street 4th Floor, Grosvenor House, Croydon CR0 9XP',
			],
			'recipient_details'=>[
				'recipient_name'=>'James Bond Esq.',
				'address_line1'=>'The Reef',
				'address_line2'=>'C/O Initiative Property Management',
				'address_line3'=>'Suite 4, Lansdowne Place',
				'address_town'=>'Bournemouth',
				'address_country'=>'Surrey',
				'address_postcode'=>'BH8 8EW',
			],
			'document_content'=>$html_data,
			'generic_details'=>[
				'document_name'=>'Simply SID PDF Document',
				'document_date'=>date('l, jS F Y'),
				'referrence_number'=>'TEST1234PDF'
			]
		];
		
		ob_start();
		$timestamp = time();
		//$this->load->view('webapp/help/faqs', $data );
		$this->load->view('/pdf-templates/generic-template.php', $data );
		$html_content = ob_get_contents();
		ob_end_clean();
		
		$this->dompdf->loadHtml( $html_content );
		
		/* Render the HTML as PDF */
		$this->dompdf->render();

		/* Output the generated PDF to Browser */
		$this->dompdf->stream("test$timestamp.pdf");

		//return $test_data; 
	}
}
