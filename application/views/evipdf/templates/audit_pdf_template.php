<?php 

	ini_set( 'memory_limit', '512M' );

	$image_preview 		 = !empty( $document_setup['generic_details']['image_preview'] ) ? true : false;
    $collected_responses = [];

	if( !empty( $document_setup['document_setup']['document_content']->audit_responses ) ){
		foreach ( $document_setup['document_setup']['document_content']->audit_responses as $response_section => $response_data ) {
			
			foreach ( $response_data as $response ) {
				$mpdf->SetHTMLHeader( $this->load->view('evipdf/templates/_partials/generic-header', ['document_setup' => $document_setup['document_setup'], 'response_section' => $response->section], true));
				$mpdf->WriteHTML( $this->load->view('evipdf/templates/_partials/generic-response-form', ['response' => $response], true ) );

				$collected_responses[$response->question_id] = array( 'question_number' => $response->ordering, 'question_content' => $response->question );

				if( !empty( $document_setup['document_setup']['document_content']->uploaded_docs ) && property_exists( $document_setup['document_setup']['document_content']->uploaded_docs, $response->question_id)){
					//if( !empty( $image_preview ) ){
						$response_uploads = $document_setup['document_setup']['document_content']->uploaded_docs->{$response->question_id};
						$mpdf->WriteHTML( $this->load->view('evipdf/templates/_partials/generic-image-preview', ['response_uploads' => $response_uploads, 'question_number' => $response->ordering], true));
					//}
				}
			}
		}
	}

    if( !empty( $document_setup['document_setup']['document_content']->uploaded_docs ) ){
		
        $mpdf->SetHTMLHeader($this->load->view('evipdf/templates/_partials/generic-header', ['document_setup' => $document_setup['document_setup'], 'response_section' => 'UPLOADED IMAGES'], true));
        $mpdf->AddPage();

        $question_count 	= $response_count = 0;
		$up_files 			= ( !empty( $document_setup['document_setup']['document_content']->uploaded_docs ) ) ? $document_setup['document_setup']['document_content']->uploaded_docs : [];

		$question_count_max = ( !empty( $up_files ) && is_array( $up_files ) ) ? count( $up_files ) : ( count( object_to_array( $up_files ) ) );

        foreach( $document_setup['document_setup']['document_content']->uploaded_docs as $question_id => $question_responses ){
            
            $question_count ++;
            $response_count_max = count($question_responses);
            
            foreach( $question_responses as $question_response ){

                $image_context = ( array_key_exists( $question_response->question_id, $collected_responses  )) ? $collected_responses[$question_response->question_id] : false;

                $mpdf->WriteHTML( $this->load->view( 'evipdf/templates/_partials/generic-image-view', ['question_response' => $question_response, 'image_context' => $image_context ], true ) );
                
                $response_count ++;
                
                if(!($question_count == $question_count_max && $response_count == $response_count_max)){
                    $mpdf->AddPage();
                }
            }
        }
    
    }

   
 ?>