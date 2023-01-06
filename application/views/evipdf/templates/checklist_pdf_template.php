<?php 
	ini_set( 'memory_limit', '512M' );
	$image_preview 		 = !empty( $document_setup['generic_details']['image_preview'] ) ? true : false;
    $collected_responses = [];
	$mpdf->SetHTMLHeader( $this->load->view('evipdf/templates/_partials/checklists/checklist-header', ['document_setup' => $document_setup['document_setup'], 'checklist_data' => $document_setup['document_setup']['document_content']->checklists_data], true));
	$mpdf->WriteHTML( $this->load->view('evipdf/templates/_partials/checklists/checklist-response-form', ['responses' => $document_setup['document_setup']['document_content']->checklists_data], true ) );  
 ?>