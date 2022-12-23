<?php

namespace App\Libraries;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//Composer Autoloader
//require FCPATH . 'vendor/autoload.php';
//require APPPATH.'vendor/autoload.php';
//use Mpdf\Mpdf;

require_once FCPATH. '/vendor/autoload.php';

class SPdf{

	function __construct(){
		//$this->mpdf = new Mpdf();
		
		$mpdf = new mPDF();
		//$mpdf->WriteHTML('<h1>Hello world!</h1>');
		//$mpdf->Output();
	}
	
	public function create_pdf( ){
		$this->this->mpdf->WriteHTML('<h1>Hello world!</h1>');
		$this->mpdf->Output();
	}
	
}
