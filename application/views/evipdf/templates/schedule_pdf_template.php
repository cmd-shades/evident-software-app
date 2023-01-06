<?php
$image_preview 		 = !empty( $document_setup['generic_details']['image_preview'] ) ? true : false;
$collected_responses = [];

$section 		= explode( "/", $_SERVER["SCRIPT_NAME"] );
$appDir  		= $_SERVER["DOCUMENT_ROOT"]."/".$section[1]."/";
$appDir  		= str_replace( 'index.php/', '', $appDir );
$logo_img_file 	= $appDir.'/assets/images/logos/main-logo-small.png';


$mpdf->SetHTMLHeader( "<table style='width:100%;'> <tr> <td align='center'> <img src='" . $logo_img_file . "' width='80px'/> </td> </tr> </table>" );
$mpdf->WriteHTML( $this->load->view( 'evipdf/templates/_partials/schedules-profile-form', ['document_content' => $document_setup['document_setup']['document_content'] ], true ) );

$activities = ( !empty( $document_setup['document_setup']['document_content']->schedule_activities ) ) ? $document_setup['document_setup']['document_content']->schedule_activities : false ;

if( !empty( $activities ) ){
	
	ini_set( 'memory_limit', '512M' );
	
	foreach ( $activities as $activity_key => $activity_data ){

		// activity profile HTML code:
		$ap_html = '';
		$ap_html = '<br /><br /><hr width="100%">';
		$ap_html .= '<br /><table style="width: 100%;"><tr><td>Activity Name</td><td><strong>'.$activity_data->activity_name.'</strong></td></tr>';
		$ap_html .= '<tr><td>Activity Status</td><td><strong>'.( ( !empty( $activity_data->status ) ) ? $activity_data->status : 'Status Not set' ).'</strong></td></tr>';
		$ap_html .= '<tr><td>Due Date</td><td><strong>'.( ( !empty( $activity_data->due_date ) ) ? $activity_data->due_date : 'Due Date not set' ).'</strong></td></tr>';
		$ap_html .= '<tr><td>Job Type</td><td><strong>'.( ( !empty( $activity_data->job_type ) ) ? $activity_data->job_type : 'Job Type not set' ).'</strong></td></tr>';

		$ap_html .= '<tr><td>Job ID</td><td><strong>'.( ( !empty( $activity_data->job_id ) ) ? $activity_data->job_id : 'Job ID not set' ).'</strong></td></tr>';
		$ap_html .= '</table>';

		$mpdf->WriteHTML( $ap_html, \Mpdf\HTMLParserMode::HTML_BODY );


		if( !empty( $activity_data->evidocs ) ){
			$evidoc_data_displayed = true;
			foreach( $activity_data->evidocs as $evidoc ){
				$e_html = '';

				if( $evidoc_data_displayed ){
					## Evidoc HTML
					$e_html .= '<div style="font-size:92%;margin-bottom:20px;"><table class="generals_table" cellpadding="4" cellspacing="0" border="0" style="margin-top:0px; font-size:92%;">';

					if( !empty( $evidoc->audit_id ) ){
						$e_html .= '<tr>
							<td width="30%" align="left"><b>EviDoc ID</b></td>
							<td width="2%"></td>
							<td width="68%" align="left">'.( html_escape( $evidoc->audit_id ) ).'</td></tr>';
					}

					if( !empty($evidoc->asset_unique_id ) ){
						$e_html .= '<tr>
							<td width="30%" align="left"><b>EviDoc Reference</b></td>
							<td width="2%"></td>
							<td width="68%" align="left">'.( html_escape( $evidoc->asset_unique_id ) ).'</td></tr>';
					}

					if( !empty( $evidoc->job_id ) ){
						$e_html .= '<tr>
							<td width="30%" align="left"><b>Job ID</b></td>
							<td width="2%"></td>
							<td width="68%" align="left">'.html_escape( $evidoc->job_id ).'</td></tr>';
					}

					if( !empty( $evidoc->customer_info->customer_id ) ){
						$e_html .= '<tr>
							<td width="30%" align="left"><b>Customer ID</b></td>
							<td width="2%"></td>
							<td width="68%" align="left">'.html_escape( $evidoc->customer_info->customer_id ).'</td>
						</tr>
						<tr>
							<td width="30%" align="left"><b>Customer Name</b></td>
							<td width="2%"></td>
							<td width="68%" align="left">'.html_escape( $evidoc->customer_info->customer_full_name ).'</td>
						</tr>
						<tr>
							<td width="30%" align="left"><b>Customer Address</b></td>
							<td width="2%"></td>
							<td width="68%" align="left">'.html_escape( $evidoc->customer_info->customer_address ).'</td>
						</tr>';
					}

					if( !empty( $evidoc->date_created ) ){
						$e_html .= '<tr>
							<td width="30%" align="left"><b>EviDoc Date</b></td>
							<td width="2%"></td>
							<td width="68%" align="left">'.html_escape( $evidoc->date_created ).'</td>
						</tr>';
					}

					if( !empty( $evidoc->record_created_by ) ){
						$e_html .= '<tr>
							<td width="30%" align="left"><b>Audited By</b></td>
							<td width="2%"></td>
							<td width="68%" align="left">'.html_escape( $evidoc->record_created_by ).'</td>
						</tr>';
					}

					$e_html .= '</table></div>';

					if ( !empty( $document_setup['document_setup']['generic_details']['document_name'] ) ){
						$e_html .= '<div style="width:100%;text-align:center;font-size: 20px; color: #0092CD;">'.( html_escape( $document_setup['document_setup']['generic_details']['document_name'] ) ).'<span style="font-size:70%">'.( html_escape( $document_setup['document_setup']['generic_details']['schedule_frequency'] ) ).'</span></div>';
					}

					$evidoc_data_displayed = false;
				}
				$mpdf->WriteHTML( $e_html, \Mpdf\HTMLParserMode::HTML_BODY );

				$mpdf->WriteHTML( $this->load->view( 'evipdf/templates/_partials/schedules-response-form', ['response' => $evidoc], true ) );

				if( !empty( $document_setup['document_setup']['document_content']->uploaded_docs ) && property_exists( $document_setup['document_setup']['document_content']->uploaded_docs, $evidoc->question_id)){
					//if( !empty( $image_preview ) ){
						$response_uploads = $document_setup['document_setup']['document_content']->uploaded_docs->{$evidoc->question_id};
						$mpdf->WriteHTML( $this->load->view('evipdf/templates/_partials/generic-image-preview', ['response_uploads' => $response_uploads, 'question_number' => $evidoc->ordering], true));
					//}
				}
			}
		} else {
			## no Evidoc
			$e_html = '';
			$e_html .= '<div style="font-size:92%;margin-bottom:20px;"><table class="generals_table" cellpadding="4" cellspacing="0" border="0" style="margin-top:0px; font-size:92%;">';
			$e_html .= '<tr><td colspan="3">No Evidoc linked to this Activity has been found</td></tr>';
			$e_html .= '</table></div>';
			$mpdf->WriteHTML( $e_html, \Mpdf\HTMLParserMode::HTML_BODY );
		}
	}
} else {
	$no_act_html = '<table style="width:100%;"><tr colspan="2"><th><strong>There are no activities under this schedule. Please create new ones. </strong></th></tr>';
	$mpdf->WriteHTML( $no_act_html, \Mpdf\HTMLParserMode::HTML_BODY );
}

if( !empty( $document_setup['document_setup']['document_content']->uploaded_docs ) ){

	$mpdf->SetHTMLHeader( $this->load->view('evipdf/templates/_partials/generic-header', ['document_setup' => $document_setup['document_setup'], 'response_section' => 'UPLOADED IMAGES'], true));
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
} ?>