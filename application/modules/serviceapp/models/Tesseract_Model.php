<?php

namespace Application\Modules\Service\Models;

class Tesseract_model extends CI_Model {

	function __construct(){
		
		parent::__construct();
		
		$this->load->model( 'Modules_model','module_service' );
		$this->load->model( 'Account_model','account_service' );
		$this->load->model( 'Job_model','job_service' );
		
		$this->soap_client 				= new SoapClient( TESSERACT_API_BASE_URL, [ 'trace'=>1, 'exceptions'=>0 ] );
		$this->tesseract_suid			= TESSERACT_API_AUTH_USER;
		$this->tesseract_spwd			= TESSERACT_API_AUTH_PWD;
		$this->attachments_path_name	= TESSERACT_ATTACHMENTS_PATH_NAME;
		
		## Authenticate User
		$auth_params		= [
			'sUID'			=> TESSERACT_API_AUTH_USER,
			'sPWD'			=> TESSERACT_API_AUTH_PWD,
			'sDataSource'	=> 'SCLEGACY', 
			'bSuccess'		=> 'Evident' 
		];
		
		$tess_auth_user			= $this->soap_client->AuthenticateUser( $auth_params );
		$this->tess_auth_token 	= $tess_auth_user->AuthenticateUserResult;
		$this->tess_api_token 	= !empty( $this->ion_auth->_current_user()->external_auth_token ) ? $this->ion_auth->_current_user()->external_auth_token : false;
		
	}

	private $tess_datasource= 'SCLEGACY';
	private $tess_success	= 'Evident';

	/* AuthenticateUser API to Tesseract */
	public function authenticate_user( $account_id = false, $request_data = false ){
		
		$result = false;
		
		if( !empty( $request_data ) ){
			$data		= convert_to_array( $request_data );
			$params		= [
				'sUID'			=> !empty( $data['tesseract_suid'] ) 	? $data['tesseract_suid'] 	: $this->tesseract_suid,
				/* ORIGINAL 'sPWD'			=> !empty( $data['tesseract_spwd'] ) 	? $data['tesseract_spwd'] 	: $this->spwd, */
				'sPWD'			=> !empty( $data['tesseract_spwd'] ) 	? $data['tesseract_spwd'] 	: $this->tesseract_spwd,
				'sDataSource'	=> $this->tess_datasource, 
				'bSuccess'		=> $this->tess_success 
			];
			
			$tess_auth_user					= $this->soap_client->AuthenticateUser( $params );
			$tess_user_obj 					= $this->ion_auth->_current_user();
			$tess_user_obj->tesseract_auth 	= $tess_auth_user;
			
			if( !empty( $tess_auth_user ) && !empty( $tess_user_obj ) ){
				$this->session->set_userdata( 'tesseract_auth', $tess_auth_user );				
				$this->session->set_flashdata('message','User Authentication Successful');
				$result = $tess_user_obj;
			} else {
				$this->session->set_flashdata('message','Unabled to Authenticate User! Invalid username/password');
			}
		}
		
		return $result;
	}
	

	/** Create New Serialized Product / Asset **/
	public function create_serialized_product( $account_id = false, $request_data = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $request_data ) ){

			$data		= convert_to_array( $request_data );

			$xml_request = new SimpleXMLElement( '<Ser></Ser>' );
			
			
			$xml_request->addChild( 'Ser_Num ', $data['asset_unique_id'] );
			## $xml_request->addChild( 'SER_PROD_NUM ', $data['asset_unique_id'] );
			## $xml_request->addChild( 'Ser_Auto_Gen_Flag ', $data['asset_unique_id'] );
			
			
			## $xml_request->addChild( 'Ser_Site_Num', 'V01AB05' );
			$xml_request->addChild( 'Ser_Site_Num', 'HHG1581' );
			$xml_request->addChild( 'Ser_Prod_Num', 'Emergency Lighting' );
			#$xml_request->addChild( 'Ser_Cont_Num', '0004/01-1' );
			
			$prod_params 		= new stdClass();
			$prod_params->xml 	= $xml_request->asXML();
			$prod_params 		= $prod_params->xml;

			$params		= [
				'sDataIn'		=> $prod_params,
				'sTokenID'		=> $this->tess_auth_token,
				'bSuccess'		=> $this->tess_success 
			];
			
			$create_serialized_product		= $this->soap_client->Create_Ser( $params );
			
			var_dump($create_serialized_product);
			
			if( !empty( $create_serialized_product->Create_SerResult ) && !empty( $create_serialized_product->bSuccess ) ){
				$this->session->set_flashdata( 'message','Product created Successfully' );
				$result = $create_serialized_product;
			} else {
				$this->session->set_flashdata( 'message','Unable to Create Product' );
			}
		}
		
		return $result;
	}
	
	
	/** Get Serialized Product / Asset **/
	public function retrieve_serialized_product( $account_id = false, $request_data = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $request_data ) ){

			$data				= convert_to_array( $request_data );

			$params		= [
				'sSerNum'			=> !empty( $data['serial_number'] ) ? $data['serial_number'] : '',
				'sSiteNum'			=> !empty( $data['site_number'] ) ? $data['site_number'] : '',
				/* ORIG 'sProdNum'			=> $data['product_number'], */
				'sProdNum'			=> !empty( $data['product_number'] ) ? $data['product_number'] : '',
				'bGetExtendedData'	=> !empty( $extended_data ) ? $extended_data : '', //Where
				'sTokenID'			=> $this->tess_auth_token,
				'bSuccess'			=> $this->tess_success 
			];

			$retrieve_serialized_product = $this->soap_client->Retrieve_Ser( $params );
		
			if( !empty( $retrieve_serialized_product->Retrieve_SerResult ) && !empty( $retrieve_serialized_product->bSuccess ) ){
				$serialized_product	= simplexml_load_string( $retrieve_serialized_product->Retrieve_SerResult->any );
				$this->session->set_flashdata( 'message','Serialized Product retrieved Successfully' );
				$result = $serialized_product;
			} else {
				$this->session->set_flashdata( 'message','Unabled to Retrieve Serialized Product' );
			}
		}
		
		return $result;
	}
	
	
	/** Update Serialized Product / Asset **/
	public function update_serialized_product( $account_id = false, $request_data = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $request_data ) ){

			$data				= convert_to_array( $request_data );

			$xml_request = new SimpleXMLElement( '<Ser></Ser>' );
 			## $xml_request->addChild( 'Ser_Auto_Gen_Flag', 'N' );
			$xml_request->addChild( 'Ser_Site_Num', 'HHG1581' );
			$xml_request->addChild( 'Ser_Prod_Num', 'Emergency Lighting' );
			$xml_request->addChild( 'Ser_Num', 'EVIDENT-0003a' );
			$xml_request->addChild( 'Ser_Area_Code', 'CACO' );
			#$xml_request->addChild( 'Ser_Cont_Num', '0004/01-1' );
			
			$prod_params 		= new stdClass();
			$prod_params->xml 	= $xml_request->asXML();
			$prod_params 		= $prod_params->xml;

			$params		= [
				'sDataIn'		=> $prod_params,
				'sTokenID'		=> $this->tess_auth_token, 
				'bSuccess'		=> $this->tess_success 
			];
			
			$update_serialized_product		= $this->soap_client->Update_Ser( $params );
			var_dump( $update_serialized_product, "print", false );
			
			if( !empty( $update_serialized_product->bSuccess ) ){
				$this->session->set_flashdata( 'message','Serialized Product updated Successfully' );
				$result = $update_serialized_product;
			} else {
				$this->session->set_flashdata( 'message','Unable to Update Serialized Product' );
			}
		}
		
		return $result;
	}
	
	
	/** Create New Site Record **/
	public function create_site_record( $account_id = false, $request_data = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $request_data ) ){

			$data		= convert_to_array( $request_data );

			$xml_request = new SimpleXMLElement( '<Site></Site>' );
			$xml_request->addChild( 'Site_Auto_Gen_Flag', 'Y' );
			$xml_request->addChild( 'Site_Name', 'Building Name' );
			$xml_request->addChild( 'Site_Cust_Num', 'Existing Customer' );
			$xml_request->addChild( 'Site_Cust_Flag', 'Y' );
			$xml_request->addChild( 'Site_Int_Flag', 'N' );
			$xml_request->addChild( 'Site_Stock_Flag', 'N' );
			$xml_request->addChild( 'Site_Workshop_Flag', 'N' );
			$xml_request->addChild( 'Site_Area_Code', 'N' );
			
			$prod_params 		= new stdClass();
			$prod_params->xml 	= $xml_request->asXML();
			$prod_params 		= $prod_params->xml;

			$params		= [
				'sDataIn'		=> $prod_params,
				'sTokenID'		=> $this->tess_auth_token,
				'sNewSiteNum'	=> 'sNewSiteNum',
				'bSuccess'		=> $this->tess_success 
			];
			
			$create_site_record	= $this->soap_client->Create_Site( $params );
			
			var_dump($create_site_record);
			
			if( !empty( $create_site_record->Create_SiteResult ) && !empty( $create_site_record->bSuccess ) ){
				$this->session->set_flashdata( 'message','Site Record created Successfully' );
				$result = $create_site_record;
			} else {
				$this->session->set_flashdata( 'message','Unabled to Create Site Record' );
			}
		}
		
		return $result;
	}
	
	
	/** Get Site Record **/
	public function retrieve_site_record( $account_id = false, $request_data = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $request_data ) ){

			$data				= convert_to_array( $request_data );

			$params		= [
				'sSiteNum'			=> !empty( $data['extended_site_ref'] ) ? $data['extended_site_ref'] : '',
				'bGetExtendedData'	=> !empty( $extended_data ) ? $extended_data : '', //Where
				'sTokenID'			=> $this->tess_auth_token,
				'bSuccess'			=> $this->tess_success 
			];

			$retrieve_site_record = $this->soap_client->Retrieve_Site( $params );
			
			if( !empty( $retrieve_site_record->Retrieve_SiteResult ) && !empty( $retrieve_site_record->bSuccess ) ){
				$site_record	= simplexml_load_string( $retrieve_site_record->Retrieve_SiteResult->any );
				$this->session->set_flashdata( 'message','Site Record retrieved Successfully' );
				$result = $site_record;
			} else {
				$this->session->set_flashdata( 'message','Unabled to Retrieve Site Record' );
			}
		}
		
		return $result;
	}
	
	/** Update Site Record **/
	public function update_site_record( $account_id = false, $request_data = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $request_data ) ){

			$data				= convert_to_array( $request_data );

			$xml_request = new SimpleXMLElement( '<Site></Site>' );
			$xml_request->addChild( 'Site_Num', '0001' );
			$xml_request->addChild( 'Site_Name', 'Building Name' );
			$xml_request->addChild( 'Site_Cust_Num', 'Existing Customer' );
			$xml_request->addChild( 'Site_Cust_Flag', 'Y' );
			$xml_request->addChild( 'Site_Int_Flag', 'Y' );
			$xml_request->addChild( 'Site_Stock_Flag', 'Y' );
			$xml_request->addChild( 'Site_Workshop_Flag', 'Y' );
			$xml_request->addChild( 'Site_Area_Code', 'Y' );
			
			$prod_params 		= new stdClass();
			$prod_params->xml 	= $xml_request->asXML();
			$prod_params 		= $prod_params->xml;

			$params		= [
				'sDataIn'		=> $prod_params,
				'sTokenID'		=> $this->tess_auth_token, 
				'bSuccess'		=> $this->tess_success 
			];
			
			$update_site_record		= $this->soap_client->Update_Site( $params );
			
			if( !empty( $update_site_record ) ){
				$this->session->set_flashdata( 'message','Site Record updated Successfully' );
				$result = $update_site_record;
			} else {
				$this->session->set_flashdata( 'message','Unabled to Create Site Record' );
			}
		}
		
		return $result;
	}
	
	
	/** Create New Product / Asset Type **/
	public function create_product( $account_id = false, $request_data = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $request_data ) ){

			$data		= convert_to_array( $request_data );

			$xml_request = new SimpleXMLElement( '<Prod></Prod>' );
			$xml_request->addChild( 'Prod_Num', '0001' );
			$xml_request->addChild( 'Prod_Desc', 'Product Description' );
			$xml_request->addChild( 'Prod_Part_Num', 'Part Number' );
			$xml_request->addChild( 'Prod_Memo', 'Some notes' );
			
			$prod_params 		= new stdClass();
			$prod_params->xml 	= $xml_request->asXML();
			$prod_params 		= $prod_params->xml;

			$params		= [
				'sDataIn'		=> $prod_params,
				'sTokenID'		=> $this->tess_auth_token,
				'sNewSiteNum'	=> 'sNewSiteNum',
				'bSuccess'		=> $this->tess_success 
			];
			
			$create_product	= $this->soap_client->Create_Product( $params );
			
			var_dump($create_product);
			
			if( !empty( $create_product->Create_ProductResult ) && !empty( $create_product->bSuccess ) ){
				$this->session->set_flashdata( 'message','Product / Asset Type created Successfully' );
				$result = $create_product;
			} else {
				$this->session->set_flashdata( 'message','Unabled to Create Product / Asset Type' );
			}
		}
		
		return $result;
	}
	
	
	/** Get Product / Asset Type **/
	public function retrieve_product( $account_id = false, $request_data = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $request_data ) ){

			$data				= convert_to_array( $request_data );

			$params		= [
				'sSiteNum'			=> !empty( $data['extended_site_ref'] ) ? $data['extended_site_ref'] : '',
				'bGetExtendedData'	=> !empty( $extended_data ) ? $extended_data : '', //Where
				'sTokenID'			=> $this->tess_auth_token,
				'bSuccess'			=> $this->tess_success 
			];

			$retrieve_product = $this->soap_client->Retrieve_Product( $params );
			
			if( !empty( $retrieve_product->Retrieve_SiteResult ) && !empty( $retrieve_product->bSuccess ) ){
				$product	= simplexml_load_string( $retrieve_product->Retrieve_SiteResult->any );
				$this->session->set_flashdata( 'message','Product / Asset Type retrieved Successfully' );
				$result = $product;
			} else {
				$this->session->set_flashdata( 'message','Unabled to Retrieve Product / Asset Type' );
			}
		}
		
		return $result;
	}
	
	/** Update Product / Asset Type **/
	public function update_product( $account_id = false, $request_data = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $request_data ) ){

			$data				= convert_to_array( $request_data );

			$xml_request = new SimpleXMLElement( '<Prod></Prod>' );
			$xml_request->addChild( 'Prod_Num', '0001' );
			$xml_request->addChild( 'Prod_Desc', 'Product Description' );
			$xml_request->addChild( 'Prod_Part_Num', 'Part Number' );
			$xml_request->addChild( 'Prod_Memo', 'Some notes' );
			
			$prod_params 		= new stdClass();
			$prod_params->xml 	= $xml_request->asXML();
			$prod_params 		= $prod_params->xml;

			$params		= [
				'sDataIn'		=> $prod_params,
				'sTokenID'		=> $this->tess_auth_token, 
				'bSuccess'		=> $this->tess_success 
			];
			
			$update_product		= $this->soap_client->Update_Product( $params );
			
			if( !empty( $update_product ) ){
				$this->session->set_flashdata( 'message','Product / Asset Type updated Successfully' );
				$result = $update_product;
			} else {
				$this->session->set_flashdata( 'message','Unabled to Create Product / Asset Type' );
			}
		}
		
		return $result;
	}
	
	
	/** Create New Job Call **/
	public function create_job_call( $account_id = false, $request_data = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $request_data ) ){

			$data		= convert_to_array( $request_data );

			$xml_request = new SimpleXMLElement( '<Call></Call>' );
			$xml_request->addChild( 'Call_Status', 'Open' );
			$xml_request->addChild( 'Call_CalT_Code', 'Call_CalT_Code' );
			$xml_request->addChild( 'Call_Site_Num', 'Call_Site_Num' );
			
			$job_call_params 		= new stdClass();
			$job_call_params->xml 	= $xml_request->asXML();
			$job_call_params 		= $job_call_params->xml;

			$params		= [
				'sDataIn'		=> $job_call_params,
				'sTokenID'		=> $this->tess_auth_token,
				'iNewCallNum'	=> 'iNewCallNum',
				'bSuccess'		=> $this->tess_success 
			];
			
			$create_job_call	= $this->soap_client->Create_Call( $params );
			
			var_dump($create_job_call);
			
			if( !empty( $create_job_call->Create_CallResult ) && !empty( $create_job_call->bSuccess ) ){
				$this->session->set_flashdata( 'message','Job Call created Successfully' );
				$result = $create_job_call;
			} else {
				$this->session->set_flashdata( 'message','Unable to Create Job Call' );
			}
		}
		
		return $result;
	}

	
	/** Get Job Call **/
	public function retrieve_job_call( $account_id = false, $request_data = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $request_data ) ){

			$data				= convert_to_array( $request_data );

			$params		= [
				'iCallNum'			=> !empty( $data['external_job_ref'] ) ? $data['external_job_ref'] : '',
				'bGetExtendedData'	=> !empty( $extended_data ) ? $extended_data : '', //Where
				'sTokenID'			=> $this->tess_auth_token,
				'bSuccess'			=> $this->tess_success 
			];

			$retrieve_job_calluct = $this->soap_client->Retrieve_Call( $params );
			
			if( !empty( $retrieve_job_calluct->Retrieve_CallResult ) && !empty( $retrieve_job_calluct->bSuccess ) ){
				$job_calluct	= simplexml_load_string( $retrieve_job_calluct->Retrieve_CallResult->any );
				$this->session->set_flashdata( 'message','Job Call retrieved Successfully' );
				$result = $job_calluct;
			} else {
				$this->session->set_flashdata( 'message','Unable to Retrieve Job Call' );
			}
		}
		
		return $result;
	}
	
	/** Update Job Call **/
	public function update_job_call( $account_id = false, $request_data = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $request_data ) ){

			$data				= convert_to_array( $request_data );

			$xml_request = new SimpleXMLElement( '<Call></Call>' );
			$xml_request->addChild( 'Call_Num', '1211127' );
 			$xml_request->addChild( 'Call_Problem', 'CMLS 
			Michael Holman to replace 6 x emergency lights...' );
			
/* 			$xml_request->addChild( 'Call_Status', 'Open' );
			$xml_request->addChild( 'Call_CalT_Code', 'Call_CalT_Code' );
			$xml_request->addChild( 'Call_Site_Num', 'Call_Site_Num' ); */

			$job_call_params 		= new stdClass();
			$job_call_params->xml 	= $xml_request->asXML();
			$job_call_params 		= $job_call_params->xml;

			$params		= [
				'sDataIn'		=> $job_call_params,
				'sTokenID'		=> $this->tess_auth_token, 
				'bSuccess'		=> $this->tess_success 
			];
			
			$update_job_calluct		= $this->soap_client->Update_Call( $params );
			
			if( !empty( $update_job_calluct ) ){
				$this->session->set_flashdata( 'message','Job Call updated Successfully' );
				$result = $update_job_calluct;
			} else {
				$this->session->set_flashdata( 'message','Unable to Create Job Call' );
			}
		}
		
		return $result;
	}
	
	
	/** Get Responses Call **/
	public function retrieve_responses( $account_id = false, $request_data = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $request_data ) ){

			$data				= convert_to_array( $request_data );

			$params		= [
				'iResponse_Map_ID'	=> !empty( $data['checklist_id'] ) ? $data['checklist_id'] : '',
				/* 'TASK_NUM'		=> !empty( $data['checklist_id'] ) ? $data['checklist_id'] : '', */
				'iCallNum'			=> !empty( $data['external_job_ref'] ) ? $data['external_job_ref'] : '',
				'iResponse_Task_Num'	=> !empty( $data['Response_Task_Num'] ) ? $data['Response_Task_Num'] : '',
				'bGetExtendedData'	=> !empty( $data['extended_data'] ) ? $data['extended_data'] : '', //Where
				'sTokenID'			=> $this->tess_auth_token,
				'bSuccess'			=> $this->tess_success 
			];
			
			var_dump( $params, "print", false );

			$retrieve_responses_calluct = $this->soap_client->Retrieve_Responses( $params );
			
			var_dump( $retrieve_responses_calluct, "print", false );
			
			if( !empty( $retrieve_responses_calluct->Retrieve_CallResult ) && !empty( $retrieve_responses_calluct->bSuccess ) ){
				$responses_calluct	= simplexml_load_string( $retrieve_responses_calluct->Retrieve_CallResult->any );
				$this->session->set_flashdata( 'message','Job Call retrieved Successfully' );
				$result 			= $responses_calluct;
			} else {
				$this->session->set_flashdata( 'message','Unable to Retrieve Responses' );
			}
		}
		
		return $result;
	}
	
	
	/** Update Response to Call/FSR/Task **/
	public function update_response( $account_id = false, $request_data = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $request_data ) ){

			$data				= convert_to_array( $request_data );

			$xml_request 		= new SimpleXMLElement( '<ResponseList></ResponseList>' );

			$ResponseList 		= $xml_request->addChild( 'ResponseList' );
			$ResponseMap_ID 	= $ResponseList->addChild( 'ResponseMap_ID', 15 );
			$Response_Table 	= $ResponseList->addChild( 'Response_Table', 'SCFSR' );
			$Response_Call_Num 	= $ResponseList->addChild( 'Response_Call_Num', 553 );
			$Response_FSR_Num 	= $ResponseList->addChild( 'Response_FSR_Num', 1 );
			$Response 			= $ResponseList->addChild( 'Response' );
			$Response_ID 		= $Response->addChild( 'Response_ID', 31 );
			$Response_Value 	= $Response->addChild( 'Response_Value', 'Yes' );
			
			$Response 			= $ResponseList->addChild( 'Response' );
			$Response_ID 		= $Response->addChild( 'Response_ID', 31 );
			$Response_Value 	= $Response->addChild( 'Response_Value', 'Yes' );
			
			$Response 			= $ResponseList->addChild( 'Response' );
			$Response_ID 		= $Response->addChild( 'Response_ID', 32 );
			$Response_Value 	= $Response->addChild( 'Response_Value', 'No' );

			$prod_params 		= new stdClass();
			$prod_params->xml 	= $xml_request->asXML();
			$prod_params 		= $prod_params->xml;
			
			var_dump( $prod_params, "print", false );

			$params		= [
				'oXMLData'		=> $prod_params,
				'sTokenID'		=> $this->tess_auth_token, 
				'bSuccess'		=> $this->tess_success 
			];
			
			$update_responses		= $this->soap_client->Update_Response( $params );
			
			var_dump( $update_responses, "print", false );
			
			if( !empty( $update_responses ) ){
				$this->session->set_flashdata( 'message','Response updated Successfully' );
				$result = $update_responses;
			} else {
				$this->session->set_flashdata( 'message','Unable to Update Response' );
			}
		}
		
		return $result;
	}
	
	
	/** Save Tess Jobs Locally **/
	public function _save_tesseract_jobs( $account_id = false, $jobs_data = false, $site_options = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $jobs_data ) ){

			$jobs_data	= convert_to_array( $jobs_data );
			$new_jobs	= $existing_jobs = $new_sites = $existing_sites = $processed_successfully = [];

			foreach( $jobs_data as $key => $job_call ){
				
				$check_job_exists = $this->db
					->where( 'tess.account_id', $account_id )
					->where( 'tess.call_num', $job_call['call_num'] )
					->where_not_in( 'tess.process_status', ['Success', 'Failed'] )
					->get( 'tesseract_jobs tess' )
					->row();

				if( !empty( $check_job_exists ) ){
					
					$job_call = $this->ssid_common->_filter_data( 'tesseract_jobs', $job_call );
					$this->db->where( 'tesseract_jobs.account_id', $account_id )
						->where( 'tesseract_jobs.call_num', $job_call['call_num'] )						
						->update( 'tesseract_jobs', $job_call );

					
					$updated_job 	 = (array) $this->db->get_where( 'tesseract_jobs', [ 'tesseract_jobs.account_id' => $account_id, 'tesseract_jobs.call_num' => $job_call['call_num'] ] )->row();
					$refreshed 		 = $this->refresh_evident_jobs( $account_id, $job_call['call_num'], $updated_job );
					$existing_jobs[] = $updated_job;

				} else {

					## Get Task Information linked to this Job
					$call_tasks 	= $this->_get_tasks_locally( $account_id, [ 'task_call_num'=> $job_call['call_num'] ] );
					$linked_task	= (object) [ 'call_prodfamily_code'=> 'AOV', 'task_type' => 'PMI' ];

					## Get Required Checklist?
					/* $params = [
						'calt_code'				=> !empty( $job_call['call_calt_code'] ) 		? $job_call['call_calt_code'] 				: false,
						'call_prodfamily_code'	=> !empty( $linked_task->call_prodfamily_code ) ? $linked_task->call_prodfamily_code 	: false,
						'task_type'				=> !empty( $linked_task->task_type ) 			? $linked_task->task_type 				: false
					];
					
					$req_checklists = $this->lookup_required_checklists_by_job_type( $account_id, $params ); */

					## Job Type Details
					$external_job_type_ref 		= $job_call['call_num'].' '.$job_call['call_prodfamily_code'];
					$external_calt_code 		= strtoupper( $job_call['call_calt_code'] );
					$external_prodfamily_code 	= strtoupper( $job_call['call_prodfamily_code'] );
					#$external_task_type 		= !empty( $linked_task->task_type ) ? $linked_task->task_type ;
					
					$job_type = $this->db->select( 'job_type_id', false )
						->where( 'job_types.account_id', $account_id )
						#->where( 'job_types.external_job_type_ref', $external_job_type_ref )
						->where( 'job_types.external_calt_code', $external_calt_code )
						->where( 'job_types.external_prodfamily_code', $external_prodfamily_code )
						#->where( 'job_types.external_task_type', $external_task_type )
						->get( 'job_types' )
						->row();

					## Contract Details
					$contract = $this->db->select( 'contract_id, contract_name', false )
						->where( 'c.account_id', $account_id )
						->or_where( 'c.contract_name', 'Alpha Track System Limited' )
						->or_where( 'c.contract_name', strip_all_whitespace( 'Alpha Track System Limited' ) )
						->get( 'contract c' )
						->row();

					## Get User Details
					$tess_user_ref 	= $job_call['call_employ_num'];
					$user 			= $this->db->select( 'id, external_user_ref', false )
						->where( 'u.account_id', $account_id )
						->where( 'u.external_user_ref', $tess_user_ref )
						->get( 'user u' )
						->row();

					$user_id 	 	= !empty( $user->id ) ? $user->id : null;
					$job_type_id 	= !empty( $job_type->job_type_id ) ? $job_type->job_type_id : false;
					#$contract_id 	= !empty( $contract->contract_id ) ? $contract->contract_id : null;
					$contract_id 	= !empty( $job_type->contract_id ) ? $job_type->contract_id : null;
					
					if( !empty( $job_type_id ) ){
						## Get Site Details
						$site_data 					= $this->_fetch_tess_site_details( $account_id, $job_call['call_site_num'] );
						$job_call['evident_site_id']= !empty( $site_data->site_id ) ? $site_data->site_id : null;

						$job_call['account_id'] 			= $account_id;
						$job_call['site_id'] 				= $site_data->site_id;
						$job_call['job_date']				= !empty( $job_call['call_rdate'] ) ? date( 'Y-m-d', strtotime( $job_call['call_rdate'] ) ) : null;
						$job_call['due_date']				= !empty( $job_call['call_ddate'] ) ? date( 'Y-m-d', strtotime( $job_call['call_ddate'] ) ) : ( !empty( $job_call['call_rdate'] ) ? date( 'Y-m-d', strtotime( $job_call['call_rdate'] ) ) : null );
						$job_call['address_id']				= $site_data->site_address_id;
						$job_call['job_type_id']			= $job_type_id;
						$job_call['contract_id']			= $contract_id;
						$job_call['assigned_to']			= $user_id;
						$job_call['status_id']				= !empty( $user_id ) ? 1 : 2; //Set status to assigned or un-assigned
						$job_call['external_job_ref']		= $job_call['call_num'];
						$job_call['external_job_call_status']	= $job_call['call_status'];
						$job_call['external_job_created_on']= date( 'Y-m-d H:i:s' );

						## Create Evident Job
						$evident_job 	= $this->ssid_common->_filter_data( 'job', $job_call );
						
						## Check if already exists
						$job_exists = $this->db->select( 'job_id, external_job_ref', false )
							->where( 'job.account_id', $account_id )
							->where( 'job.external_job_ref', $job_call['call_num'] )
							->get( 'job' )
							->row();
						if( !empty( $job_exists ) ){
							$evident_job['job_id']  = $job_exists->job_id;
							$existing_jobs[] 		= $evident_job;
						} else {
							$this->db->insert( 'job', $evident_job );
							$evident_job['job_id'] = $job_call['evident_job_id'] = $this->db->insert_id();
						}

						## Create Temp Tess Job
						$temp_job 					= $this->ssid_common->_filter_data( 'tesseract_jobs', $job_call );
						$temp_job['account_id'] 	= $account_id;
						$temp_job['evident_job_id'] = $evident_job['job_id'];

						$this->db->insert( 'tesseract_jobs', $temp_job );
						$new_jobs[] = $evident_job;
						
						## Update Tesseract
						$call_update_data = [
							'job_id'			=> $evident_job['job_id'],
							'account_id'		=> $account_id,
							'call_Num'			=> $job_call['call_num'],
							'call_Status'		=> !empty( $job_call['call_status'] ) 		? strtoupper( $job_call['call_status'] ) 	: null,
							'call_CalT_Code'	=> !empty( $job_call['call_calt_code'] ) 	? strtoupper( $job_call['call_calt_code'] ) : null,
							'call_Employ_Num'	=> $tess_user_ref,
							'call_Ref3'			=> $evident_job['job_id']
						];						
						$update_tess_job = $this->tesseract_service->update_job( $account_id, $call_update_data );

					} else {
						# Unknown Job Type
						$incomplete_data = $this->ssid_common->_filter_data( 'tesseract_unknown_job_types', $job_call );
						
						$if_exists = $this->db->select( 'call_num', false )
							->where( 'tesseract_unknown_job_types.account_id', $account_id )
							->where( 'tesseract_unknown_job_types.call_num', $incomplete_data['call_num'] )
							->get( 'tesseract_unknown_job_types' )
							->row();
						
						if( empty( $if_exists ) ){
							$this->db->insert( 'tesseract_unknown_job_types', $incomplete_data );
						} else {
							$incomplete_data['updated_at'] = date( 'Y-m-d H:i:s' );
							$this->db->where( 'tesseract_unknown_job_types.account_id', $account_id )
								->where( 'tesseract_unknown_job_types.call_num', $incomplete_data['call_num'] )
								->update( 'tesseract_unknown_job_types', $incomplete_data );
						}
						
						$this->session->set_flashdata( 'message','Warning! The system found an unsupported Product Family Code and Call Calt Code combination ('.$incomplete_data['call_prodfamily_code'].' '.$incomplete_data['call_calt_code'].')' );
					}
				}
				$processed_successfully[] = $job_call['call_num'];
			}
			
			$result['new_jobs']		= $new_jobs;
			$result['existing_jobs']= $existing_jobs;

			## Update Tess List of Jobs
			if( !empty( $processed_successfully ) && !empty( $site_options['site_number_ref'] ) ){
				#$run_tess_update = $this->_update_tess_jobs_list( $account_id, $site_options, $processed_successfully );
			}

		}
		
		return $result;
	}
	
	
	/** Save Tess Jobs Locally Bridge API Version **/
	public function _save_tesseract_api_jobs( $account_id = false, $jobs_data = false, $site_options = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $jobs_data ) ){

			$jobs_data	= convert_to_array( $jobs_data );
			$new_jobs	= $existing_jobs = $new_sites = $existing_sites = $processed_successfully = [];

			foreach( $jobs_data as $key => $job_call ){
				
				$check_job_exists = $this->db
					->where( 'tess.account_id', $account_id )
					->where( 'tess.callnum', $job_call['callnum'] )
					->where_not_in( 'tess.process_status', ['Success', 'Failed'] )
					->get( 'tesseract_api_jobs tess' )
					->row();
					
				if( !empty( $check_job_exists ) ){
					//Do nothing for now
					$job_call['record_id'] 	= $check_job_exists->record_id;
					$existing_jobs[] 		= $job_call;
				} else {

					## Contract Details
					$job_type = $this->db->select( 'job_type_id', false )
						->where( 'job_types.account_id', $account_id )
						->where( 'job_types.external_job_type_ref', 'PMI' )
						->get( 'job_types' )
						->row();

					## Contract Details
					$contract = $this->db->select( 'contract_id, contract_name', false )
						->where( 'c.account_id', $account_id )
						->or_where( 'c.contract_name', 'Alpha Track System Limited' )
						->or_where( 'c.contract_name', strip_all_whitespace( 'Alpha Track System Limited' ) )
						->get( 'contract c' )
						->row();

					## Get User Details
					$tess_user_ref 	= !empty( $job_call['callemploynum'] ) ? $job_call['callemploynum'] : false;
					$user 			= $this->db->select( 'id, external_user_ref', false )
						->where( 'u.account_id', $account_id )
						->where( 'u.external_user_ref', $tess_user_ref )
						->get( 'user u' )
						->row();

					$user_id 	 = !empty( $user->id ) ? $user->id : null;
					$job_type_id = !empty( $job_type->job_type_id ) ? $job_type->job_type_id : 101;
					$contract_id = !empty( $contract->contract_id ) ? $contract->contract_id : null;
					
					## Get Site Details
					$site_data 					= $this->_fetch_tess_site_details( $account_id, $job_call['callsitenum'] );
					$job_call['evident_site_id']= $site_data->site_id;

					$job_call['site_id'] 				= $site_data->site_id;
					$job_call['job_date']				= !empty( $job_call['callrdate'] ) ? date( 'Y-m-d', strtotime( $job_call['callrdate'] ) ) : null;
					$job_call['due_date']				= !empty( $job_call['callddate'] ) ? date( 'Y-m-d', strtotime( $job_call['callddate'] ) ) : null;
					$job_call['address_id']				= $site_data->site_address_id;
					$job_call['job_type_id']			= $job_type_id;
					$job_call['contract_id']			= $contract_id;
					$job_call['assigned_to']			= $user_id;
					$job_call['status_id']				= !empty( $user_id ) ? 1 : 2; //Set status to assigned or un-assigned
					$job_call['external_job_ref']		= !empty( $job_call['callnum'] ) ? $job_call['callnum'] : false;
					$job_call['external_job_created_on']= date( 'Y-m-d H:i:s' );

					## Create Evident Job
					$evident_job 	= $this->ssid_common->_filter_data( 'job', $job_call );
					
					## Check if already exists
					$job_exists = $this->db->select( 'job_id, external_job_ref', false )
						->where( 'job.account_id', $account_id )
						->where( 'job.external_job_ref', $job_call['callnum'] )
						->get( 'job' )
						->row();
					if( !empty( $job_exists ) ){
						$evident_job['job_id']  = $job_exists->job_id;
						$existing_jobs[] 		= $evident_job;
					} else {
						$this->db->insert( 'job', $evident_job );
						$evident_job['job_id'] = $job_call['evident_job_id'] = $this->db->insert_id();
					}

					## Create Temp Tess Job
					$temp_job 					= $this->ssid_common->_filter_data( 'tesseract_api_jobs', $job_call );
					$temp_job['evident_job_id'] = $evident_job['job_id'];

					$this->db->insert( 'tesseract_api_jobs', $temp_job );
					$new_jobs[] = $evident_job;
				}
				$processed_successfully[] = $job_call['callnum'];
			}
			
			$result['new_jobs']		= $new_jobs;
			$result['existing_jobs']= $existing_jobs;

			## Update Tess List of Jobs
			if( !empty( $processed_successfully ) && !empty( $site_options['site_number_ref'] ) ){
				#$run_tess_update = $this->_update_tess_jobs_list( $account_id, $site_options, $processed_successfully );
			}

		}
		
		return $result;
	}
	
	/** 
	* Check and Update Tesseract
	**/
	private function _update_tess_jobs_list( $account_id = false, $site_options = false, $jobs_processed_successfully = false ){
		
		$result = false;
		
		if( !empty( $account_id ) && !empty( $jobs_processed_successfully ) && !empty( $site_options['site_number_ref'] ) ){

			## Get New Site Record first
			$params		= [
				'sSiteNum'			=> $site_options['site_number_ref'],
				'bGetExtendedData'	=> true,
				'sTokenID'			=> $this->tess_auth_token,
				'bSuccess'			=> true 
			];
			
			$retrieve_site_record 	= $this->soap_client->Retrieve_Site( $params );
			
			if( !empty( $retrieve_site_record->Retrieve_SiteResult ) && !empty( $retrieve_site_record->bSuccess ) ){

				$site_record	= simplexml_load_string( $retrieve_site_record->Retrieve_SiteResult->any );

				if( !empty( $site_record )  ){
					
					$site_memo_content 			= array( ( string ) $site_record->Site_Memo );
					
					if( !empty( $site_memo_content[0] ) ){
						
						$incoming_jobs_list 		= explode( ',' , $site_memo_content[0] );
						
						$site_last_update  			= array( ( string ) $site_record->Site_Last_Update )[0];
						$incoming_site_last_update  = strtotime( date( 'd-m-Y H:i:s', strtotime( $site_last_update ) ) );
						$current_site_last_update   = strtotime( date( 'd-m-Y H:i:s', strtotime( $site_options['site_last_modified_time'] ) ) );
						
						$incoming_jobs_list[] 		= '123456789';
						$incoming_jobs_list[] 		= '987654321';
					
						## Pending processing 
						$new_list = array_diff( $incoming_jobs_list, $jobs_processed_successfully );

						if( !empty( $new_list ) && ( $incoming_site_last_update >= $current_site_last_update ) ){
							$jobs_str			= implode( ',', $new_list );
							$site_update_data 	= [
								'Site_Num' 		=> $site_options['site_number_ref'],
								'Site_Memo' 	=> $jobs_str,
								'Site_Cust_Num' => $site_options['site_cust_num'],
							];
							
							## Update Tess with un-processed Jobs
							$updated_site = $this->update_site_record( $account_id, $site_update_data );

						}
					}
				}
			}

		}
		return $result;
	}
	
	
	/** Fetch Tess Site Locally, if not exist.. Create One **/
	public function _fetch_tess_site_details( $account_id = false, $tess_site_number = false ){
		
		$result = false;
		
		if( !empty( $account_id ) && !empty( $tess_site_number ) ){
			
			$site_exists = $this->db
				->where( 'site.account_id', $account_id )
				->where( 'site.external_site_ref', $tess_site_number )
				->where_not_in( 'site.archived != 1' )
				->get( 'site' )
				->row();

			if( !empty( $site_exists ) ){
				$this->session->set_flashdata( 'message','Site already exists, record returned' );
				$result = $site_exists;
			} else {
				
				## Get Site from Tess and save it locally
				$site = $this->get_site_by_site_number( $account_id, $tess_site_number );
				if( !empty( $site ) ){
					$result = $site;
					$this->session->set_flashdata( 'message','Site record found' );
				} else {
					$this->session->set_flashdata( 'message','Site record NOT found' );
				}

			}
		}
		
		return $result;
		
	}
	
	
	/** Create Tess Site on Evident **/
	public function _create_tess_site( $account_id = false, $tess_site_number = false ){
		
		$result = false;
		
		if( !empty( $account_id ) && !empty( $tess_site_number ) ){
			
			$site_exists = $this->db->select( 'account_id, site_id, site_name, site_address_id, estate_name, site_postcodes, external_site_ref, external_site_created_on, external_site_updated_on', false )
				->where( 'site.account_id', $account_id )
				->where( 'site.external_site_ref', $tess_site_number )
				->where_not_in( 'site.archived != 1' )
				->get( 'site' )
				->row();
				
			if( !empty( $site_exists ) ){
				
				$this->session->set_flashdata( 'message','Site already exists, record returned' );
				$result = $site_exists;
				
			} else {
				
				$tess_site = $this->retrieve_site_record( $account_id, [ 'site_number'=>$tess_site_number ] );
			
				if( !empty( $tess_site ) ){

					$site_name		= array( ( string ) $tess_site->Site_Name );
					$site_address	= array( ( string ) $tess_site->Site_Address );
					$site_town		= array( ( string ) $tess_site->Site_Town );
					$site_postcode	= array( ( string ) $tess_site->Site_Post_Code );
					
					$data = [
						'account_id'				=> $account_id,
						'site_name'					=> ucwords( strtolower( $site_name[0] ) ),
						'site_address_id'			=> '00',
						'estate_name'				=> ucwords( strtolower( trim( $site_address[0] ).( !empty( $site_town[0] ) ? ', '.trim( $site_town[0] ) : '' ) ) ),
						'site_postcodes'			=> strtoupper( $site_postcode[0] ),
						'external_site_ref'			=> $tess_site_number,
						'external_site_created_on'	=> date( 'Y-m-d H:i:s' )
					];

					$data = $this->ssid_common->_filter_data( 'site', $data );

					$this->db->insert( 'site', $data );
					$data['site_id'] = $this->db->insert_id();
					$result = (object) $data;
					$this->session->set_flashdata( 'message','Site created successfully' );
				}
			}
			
		}
		
		return $result;
		
	}
	
	
	
	/** Create New FSR Call **/
	public function create_fsr_call( $account_id = false, $request_data = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $request_data ) ){

			$data			= convert_to_array( $request_data );
			$call_number	= $data['FSR_Call_Num'];

			## Check for details locally
			$call_exists = $this->db->select( 'tesseract_jobs.*, job.start_time, job.finish_time, job.completed_works, symptom_code,fault_code, repair_code' )
				->where( 'call_num', $call_number )
				->join( 'job', 'job.external_job_ref = tesseract_jobs.call_num', 'left' )
				->get( 'tesseract_jobs' )
				->row();
				
			if( !empty( $call_exists ) ){
				
				$completion_date = !empty( $call_exists->finish_time ) ? datetime_to_iso8601( $call_exists->finish_time ) : datetime_to_iso8601( date( 'Y-m-d H:i:s' ) );
				
				$evidoc_pdf		= 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf';
				$signature_img	= file_get_contents( 'http://www.myevident.co.uk/_account_assets/accounts/8/assets/ssid8_signature1559ast873aud902_Signature_1559.jpg' );
				$signature_blob = base64_encode( $signature_img );
				
				$fsr_data 		= '';
				$fsr_data		.= $call_exists->completed_works;
				$fsr_data		.= ' | Evidoc PDF: '.$evidoc_pdf;

				$xml_request = new SimpleXMLElement( '<FSR></FSR>' );

				$xml_request->addChild( 'FSR_Call_Num', $call_number );
				$xml_request->addChild( 'FSR_Employ_Num', ( !empty( $call_exists->call_employ_num ) ) ? $call_exists->call_employ_num : NULL ); ## Admin - Admin Person (?)
				$xml_request->addChild( 'FSR_Prod_Num', $call_exists->call_prod_num );
				$xml_request->addChild( 'FSR_Rep_Code', 51 );
				$xml_request->addChild( 'FSR_Fault_Code', 'MIS' );
				$xml_request->addChild( 'FSR_Start_Date', ( ( !empty( $call_exists->start_time ) ) ? datetime_to_iso8601( $call_exists->start_time ) : datetime_to_iso8601( date( 'Y-m-d H:i:s', strtotime( '- 1 hour' ) ) ) ) );
				$xml_request->addChild( 'FSR_Call_Status', 'COMP' );
				$xml_request->addChild( 'FSR_Complete_Date', $completion_date );
				#$xml_request->addChild( 'FSR_Solution', ( !empty( $call_exists->completed_works ) ? $call_exists->completed_works : 'SAMPLE_FSR_SOLUTION' ) );
				$xml_request->addChild( 'FSR_Solution', $fsr_data );
				$xml_request->addChild( 'FSR_Miles', '1' );
				$xml_request->addChild( 'FSR_Signature_Data', $signature_blob );
				
				$fsr_call_params 		= new stdClass();
				$fsr_call_params->xml 	= $xml_request->asXML();
				$fsr_call_params 		= $fsr_call_params->xml;
				$params		= [
					'sDataIn'		=> $fsr_call_params,
					'sTokenID'		=> $this->tess_auth_token,
					'iNewFSRNum'	=> 'iNewFSRNum',
					'bSuccess'		=> $this->tess_success 
				];

				$create_fsr_call	= $this->soap_client->Create_FSR( $params );
				// var_dump( $create_fsr_call, "print", false );

				//if( !empty( $create_fsr_call->Create_FSRResult ) && !empty( $create_fsr_call->bSuccess ) ){
				if( !empty( $create_fsr_call->bSuccess ) && ( $create_fsr_call->bSuccess == 1 ) ){
					$this->session->set_flashdata( 'message','FSR Call created Successfully' );
					$result = $create_fsr_call;
				} else {
					$this->session->set_flashdata( 'message','Unable to Create FSR Call' );
				}
			} else {
				$this->session->set_flashdata( 'message','Your request is missing required parameters' );
			}
		}
		
		return $result;
	}
	
	
	/** 
	*	Create New FSR Call 
	* 	This is from the Bridge API
	**/
	public function create_fsr_api_call( $account_id = false, $request_data = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $request_data ) ){
			
			$data			= convert_to_array( $request_data );
			$call_number	= $data['FSR_Call_Num'];
			
			

			## Check for details locally
			$call_exists = $this->db->select( 'tesseract_api_jobs.*, job.start_time, job.finish_time, job.completed_works, symptom_code,fault_code, repair_code' )
				->where( 'callnum', $call_number )
				->join( 'job', 'job.external_job_ref = tesseract_api_jobs.callnum', 'left' )
				->get( 'tesseract_api_jobs' )
				->row();
					
			if( !empty( $call_exists ) ){
				
				$completion_date = !empty( $call_exists->finish_time ) ? date( 'Y-m-d H:i:s', strtotime( $call_exists->finish_time ) ) : date( 'Y-m-d H:i:s' );
				
				$evidoc_pdf		= 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf';
				$signature_img	= file_get_contents( 'http://www.myevident.co.uk/_account_assets/accounts/8/assets/ssid8_signature1559ast873aud902_Signature_1559.jpg' );
				$signature_blob = base64_encode( $signature_img );
				
				$fsr_data 		= '';
				$fsr_data		.= $call_exists->completed_works;
				$fsr_data		.= ' | Evidoc PDF: '.$evidoc_pdf;

				$xml_request = new SimpleXMLElement( '<FSR></FSR>' );
				
				$xml_request->addChild( 'FSR_Call_Num', $call_number );
				
				//  $xml_request->addChild( 'Call_InDate', ( !empty( $call_exists->callindate ) ) ? $call_exists->callindate :  date( 'Y-m-d H:i:s', strtotime( $call_exists->start_time .' -1 hour' );

				$xml_request->addChild( 'FSR_Employ_Num', ( !empty( $call_exists->callemploynum ) ) ? $call_exists->callemploynum : 'MS03' ); ## Melissa Spillane - Admin Person (?)
				$xml_request->addChild( 'FSR_Prod_Num', $call_exists->callprodnum );
				$xml_request->addChild( 'FSR_Rep_Code', 51 );
				$xml_request->addChild( 'FSR_Fault_Code', 'MIS' );
				$xml_request->addChild( 'FSR_Start_Date', ( ( !empty( $call_exists->start_time ) ) ? date( 'Y-m-d H:i:s', strtotime( $call_exists->start_time ) ) : date( 'Y-m-d H:i:s', strtotime( '- 1 day' ) ) ) );
				$xml_request->addChild( 'FSR_Call_Status', 'COMP' );
				$xml_request->addChild( 'FSR_Complete_Date', date( 'Y-m-d H:i:s', strtotime( $completion_date ) ) );
				#$xml_request->addChild( 'FSR_Solution', ( !empty( $call_exists->completed_works ) ? $call_exists->completed_works : 'SAMPLE_FSR_SOLUTION' ) );
				$xml_request->addChild( 'FSR_Solution', $fsr_data );
				$xml_request->addChild( 'FSR_Miles', '1' );
				$xml_request->addChild( 'FSR_Signature_Data', $signature_blob );

				$fsr_call_params 		= new stdClass();
				$fsr_call_params->xml 	= $xml_request->asXML();
				$fsr_call_params 		= $fsr_call_params->xml;
				$params		= [
					'sDataIn'		=> $fsr_call_params,
					'sTokenID'		=> $this->tess_auth_token,
					'iNewFSRNum'	=> 'iNewFSRNum',
					'bSuccess'		=> $this->tess_success 
				];

				$create_fsr_call	= $this->soap_client->Create_FSR( $params );

				//if( !empty( $create_fsr_call->Create_FSRResult ) && !empty( $create_fsr_call->bSuccess ) ){
				if( !empty( $create_fsr_call->bSuccess ) && ( $create_fsr_call->bSuccess == 1 ) ){
					$this->session->set_flashdata( 'message','FSR Call created Successfully' );
					$result = $create_fsr_call;
				} else {
					$this->session->set_flashdata( 'message','Unable to Create FSR Call' );
				}
			} else {
				$this->session->set_flashdata( 'message','Your request is missing required parameters' );
			}
		}
		
		return $result;
	}
	
	
	/** Tesseract User Authentication via Bridge API **/
	public function user_login( $account_id = false, $postdata = false ){
		
		$result = (object)[
			'data'	 => false,
			'success'=> false,
			'message'=> ''
		];

		if( !empty( $account_id ) && !empty( $postdata ) ){
			$url_endpoint 	= 'Users/authenticate';
			$method_type	= 'POST';
			$data			= [
				'username'	=> ( !empty( $postdata['username'] ) ) 	? $postdata['username'] 	: false,
				'password'	=> ( !empty( $postdata['password'] ) )  ? $postdata['password'] 	: '',
			];

			$tesseract_post = $this->tesseract_common->api_dispatcher( $url_endpoint, json_encode( $data ), [ 'method'=>$method_type, 'auth_type'=>'token' ] );

			if( !empty( $tesseract_post->token ) ){
				$result->data 	 = $tesseract_post;
				$result->success = true;
				$result->message = 'User authenticated successfully';
				$this->session->set_flashdata( 'message','User authenticated successfully' );
			} else {
				$result->data 	 = false;
				$result->success = false;
				$result->message = 'User authencation failed';
				$this->session->set_flashdata( 'message', 'User authencation failed' );
			}
		}
		return $result;
	}
	
	
	/** Get Job/Call by Call Number **/
	public function get_job_by_call_number( $account_id = false, $call_number = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $call_number ) ){
			$url_endpoint 	= 'Jobs/GetJobByCallNumber/'.$call_number;
			$method_type	= 'GET';
			$tesseract_job  = $this->tesseract_common->api_dispatcher( $url_endpoint, false, [ 'method'=>$method_type, 'auth_token'=>$this->tess_api_token, 'auth_type'=>'token' ] );
			if( !empty( $tesseract_job->job ) ){
				$this->session->set_flashdata( 'message','Tesseract Job/Call retrieved Successfully' );
				$result			= is_object( $tesseract_job->job ) ? array_change_key_case( object_to_array( $tesseract_job->job ), CASE_LOWER ) : array_change_key_case( $tesseract_job->job, CASE_LOWER );
				$this->_save_tesseract_jobs( $account_id, [$result] );
				$result = $tesseract_job->job;
			} else {
				$this->session->set_flashdata( 'message','Unabled to Retrieve Tesseract Call. Call Number '.$call_number.' was not found' );
			}
		}
		
		return $result;
	}
	
	
	/** Get Jobs/Calls by Site Number **/
	public function get_jobs_by_site_number( $account_id = false, $site_number = false, $params = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $site_number ) ){
			$url_endpoint 	= 'Jobs/GetJobBySiteNumber/'.$site_number;
			
			if( !empty( $params['call_Status'] ) ) {
				if( strpos( $url_endpoint, '?') !== false ){
					$url_endpoint .= '&call_Status=OPEN';
				} else {
					$url_endpoint .= '?call_Status=OPEN';
				}
			}
			
			if( !empty( $params['call_type'] ) ) {
				if( strpos( $url_endpoint, '?') !== false ){
					$url_endpoint .= '&call_Status=OPEN';
				} else {
					$url_endpoint .= '?call_Status=OPEN';
				}
			}
			
			if( !empty( $params['call_calt_code'] ) || !empty( $params['callCalTCode'] ) || !empty( $params['call_CalT_Code'] ) ){
				$call_type_code		= !empty( $params['callCalTCode'] ) ? $params['callCalTCode'] : ( !empty( $params['call_calt_code'] ) ? $params['call_calt_code'] : ( !empty( $params['call_CalT_Code'] ) ? $params['call_CalT_Code'] : false ) );
				
				if( strpos( $url_endpoint, '?') !== false ){
					$url_endpoint .= '&callCalTCode='.$call_type_code;
				} else {
					$url_endpoint .= '?callCalTCode='.$call_type_code;
				}
			}

			if( !empty( $params['startDate'] ) && !empty( $params['endDate'] ) ){
				$start_date = date( 'Y-m-d 00:00:01', strtotime( $params['startDate'] ) );
				$end_date 	= date( 'Y-m-d 23:59:59', strtotime( $params['endDate'] ) );
				
				if( strpos( $url_endpoint, '?') !== false ){
					#$url_endpoint .= '&startDate='. $start_date;
					$url_endpoint .= '&startDate='.urlencode( $start_date );
				} else {
					#$url_endpoint .= '?startDate='. $start_date;
					$url_endpoint .= '?startDate='.urlencode( $start_date );
				}
				
				#$url_endpoint .= '&endDate='. $end_date;
				$url_endpoint .= '&endDate='.urlencode( $end_date );
			}

			$method_type	= 'GET';
			$tesseract_jobs  = $this->tesseract_common->api_dispatcher( $url_endpoint, false, [ 'method'=>$method_type, 'auth_token'=>$this->tess_api_token, 'auth_type'=>'token' ] );

			if( !empty( $tesseract_jobs ) ){
				$this->session->set_flashdata( 'message','Tesseract Jobs/Calls retrieved Successfully' );
				$result = !empty( $tesseract_jobs->job ) ? $tesseract_jobs->job : false;
			} else {
				$this->session->set_flashdata( 'message','Unabled to Retrieve Tesseract Jobs/Calls' );
			}
		}
		
		return $result;
	}
	
	
	/** Update Tesseract Job/Call **/
	public function update_job( $account_id = false, $postdata = false ){
		
		$result = (object)[
			'data'	 => false,
			'success'=> false,
			'message'=> ''
		];

		if( !empty( $account_id ) && !empty( $postdata ) ){
			$url_endpoint 	= 'Jobs/UpdateJob';
			$method_type	= 'POST';
			$data			= [];
			foreach( $postdata as $col => $value ){
				$data[$col] = ( $col == 'call_Num' ) ? intval( $value ) : trim( $value );
			}
			
			$error_log = $this->_prepare_error_log( $account_id, $url_endpoint, $data );
			
			unset( $postdata['account_id'], $data['account_id'] );

			if( !empty( $data['call_Num'] ) ){
				
				$data['Call_Last_FSR_Num'] 		= !empty( $data['Call_Last_FSR_Num'] ) 	? intval( $data['Call_Last_FSR_Num'] )  : null;
				$data['Call_FSR_Count'] 		= !empty( $data['Call_FSR_Count'] ) 	? intval( $data['Call_FSR_Count'] )  : null;
				
				$tesseract_job = $this->tesseract_common->api_dispatcher( $url_endpoint, json_encode( $data ), [ 'method'=>$method_type, 'auth_token'=>$this->tess_api_token, 'auth_type'=>'token' ] );

				if( !empty( $tesseract_job->scCallUpdate ) && ( $tesseract_job->success == 1 ) ){
					
					$this->_modify_job( $account_id, $tesseract_job->scCallUpdate );
					
					$result->data 	 = $tesseract_job;
					$result->success = true;
					$result->message = 'Tesseract Job updated successfully';
					$this->session->set_flashdata( 'message','Tesseract Job updated successfully' );
				} else {
					
					## Log Error
					$error_log['api_call_desc'] 	= __METHOD__;
					$error_log['api_error_details'] = json_encode( $tesseract_job );
					$this->_log_tesseract_errors( $error_log );
					
					$result->data 	 = false;
					$result->success = false;
					$result->message = 'Tesseract Job update failed';
					$this->session->set_flashdata( 'message', 'Tesseract Job update failed' );
				}
				
			} else {
				
				$result->data 	 = false;
				$result->success = false;
				$result->message = 'Tesseract Job update failed';
				$this->session->set_flashdata( 'message', 'Missing required information' );
			}

		}
		return $result;
	}
	
	
	/** Modify locally saved Tesseract Job **/
	public function _modify_job( $account_id = false, $data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $data ) ){
			
			$job_data = is_object( $data ) ? array_change_key_case( object_to_array( $data ), CASE_LOWER ) : array_change_key_case( $data, CASE_LOWER );
			
			$check_exists = $this->db->select( 'tesseract_jobs.record_id, tesseract_jobs.call_num' )
				->where( 'tesseract_jobs.account_id', $account_id )
				->where( 'tesseract_jobs.call_num', $job_data['call_num'] )
				->get( 'tesseract_jobs' )
				->row();

			$job_data 	= $this->ssid_common->_filter_data( 'tesseract_jobs', $job_data );
			
			if( !empty( $check_exists ) ){
				$job_data['processed_by']		=  $this->ion_auth->_current_user->id;
				$this->db->where( 'tesseract_jobs.call_num', $check_exists->call_num )
					->update( 'tesseract_jobs', $job_data );
					
				$job_data['record_id']=  $check_exists->record_id;
				$result = ( $this->db->trans_status() !== FALSE ) ? $job_data : false;
				
			} else {
				## Create new Job Record 
				$job_data['account_id']		=  $account_id;
				$job_data['processed_by']	=  $this->ion_auth->_current_user->id;
				$this->db->insert( 'tesseract_jobs', $job_data );
			}
			
		}
		return $result;
	}
	
	
	/** Retrieve Site Jobs from Tesseract **/
	public function retrieve_site_jobs( $account_id = false, $site_numbers = false, $where = false, $orderByColumn = false, $sortBy = false, $limit = false, $offset = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $site_numbers ) ){
			
			if( is_string( $site_numbers ) && ( strpos( $site_numbers, ',' ) !== false ) ){
				$site_numbers = explode(',', $site_numbers );
				$site_numbers = array_map( 'trim', $site_numbers );
			}
			
			$site_numbers = convert_to_array( $site_numbers );

			$this->db->select( 'site.site_id, site.site_reference, site.external_site_ref', false )
				->where( 'site.account_id', $account_id )
				->where( 'site.archived !=',1 );
			
			$this->db->where_in( 'site.external_site_ref', $site_numbers );

			$query = $this->db->get( 'site' );

			if( $query->num_rows() > 0 ){
			
				$total_saved_jobs = [];
				foreach( $query->result() as $k => $site ){

					## Get Tess-Site-Jobs matching set conditions
					if( !empty( $site->external_site_ref ) ){
						
						$params 	= [ 
							'call_Status'	=> 'OPEN', 
							'call_CalT_Code'=> 'PM', 
						];
						
						$site_jobs 	= [];
						$tess_jobs 	= $this->get_jobs_by_site_number( $account_id, $site->external_site_ref, $params );

						foreach( $tess_jobs as $key => $tess_job ){
							if( in_array( $tess_job->call_Status, [ 'OPEN' ] ) && in_array(  strtoupper( $tess_job->call_CalT_Code ), [ 'PMB','PMA','PMH','PMQ','PMM','PMF','PMW', /*'PMI'*/ ]) ){
								$job 				= array_change_key_case( object_to_array( $tess_job ), CASE_LOWER );
								$job['account_id'] 	= ( string ) $account_id;
								$job['processed_by']= $this->ion_auth->_current_user->id;
								$site_jobs[]		= array_filter( $job );
							}
						}
					}

					if( !empty( $site_jobs ) ){
						#$saved_data 		= $this->_save_tesseract_api_jobs( $account_id, $site_jobs );
						$saved_data 		= $this->_save_tesseract_jobs( $account_id, $site_jobs );
						if( !empty( $saved_data ) ){
							$total_saved_jobs[]	= array_merge( $total_saved_jobs, $site_jobs );
						}
					}
				}
				
				if( !empty( $saved_data ) ){
					$result =  $saved_data;
					$this->session->set_flashdata( 'message','Site Jobs/Calls retrieved Successfully' );
				} else {
					$result = null;
					$this->session->set_flashdata( 'message','No Jobs/Calls retrieved matching your criteria' );
				}

			} else {
				#create site and then pull jobs
				$tess_sites = $this->get_site_by_site_number( $account_id, $site_numbers );
				if( !empty( $tess_sites ) ){
					$tess_sites		= is_object( $tess_sites ) ? object_to_array( $tess_sites ) : $tess_sites;
					$site_numbers 	= ( !empty( $tess_sites[0] ) ) ? array_column( $tess_sites, 'site_num' ) : $tess_sites['site_num'];
					$result 		= $this->retrieve_site_jobs( $account_id, $site_numbers );
					$this->session->set_flashdata( 'message','Site Jobs/Calls retrieved Successfully' );
				}
			}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing required parameters' );
		}
		
		return $result;
	}
	
		
	/** Get Site Rrecord by Site Number **/
	public function get_site_by_site_number( $account_id = false, $site_number = false, $params = false ){

		$result = false;

		if( !empty( $account_id ) && !empty( $site_number ) ){
			
			$url_endpoint 	= 'Sites/GetSiteBySiteNum/';
			$method_type	= 'GET';
			
			$site_number	= is_array( $site_number ) ? $site_number : ( ( strpos( $site_number, ',') !== false ) ? ( explode(',', $site_number ) ) : $site_number );

			if( is_array( $site_number ) ){
				
				## Process multiples
				
				$site_numbers = array_map( 'trim', $site_number );

				foreach( $site_numbers as $key => $site_number ){
					$url_endpoint 	.= $site_number;
					
					## Single Site Number
					$check_for_new	= !empty( $params['check_for_new'] ) ? $params['check_for_new'] : false;
				
					if( !empty( $check_for_new ) ){
						
						$tesseract_site  = $this->tesseract_common->api_dispatcher( $url_endpoint, false, [ 'method'=>$method_type, 'auth_token'=>$this->tess_api_token, 'auth_type'=>'token' ] );
						
						if( !empty( $tesseract_site ) && !empty( $tesseract_site->success ) ){
							$this->session->set_flashdata( 'message','Tesseract Site data retrieved Successfully' );
							$result = !empty( $tesseract_site->site ) ? $tesseract_site->site[0] : false;

							if( !empty( $result ) ){
								$result = $this->_save_site( $account_id, $result );
							}
							
						} else {
							$this->session->set_flashdata( 'message','Unabled to Retrieve Tesseract Task(s)' );
						}
						
					} else {
						//Get the results locally
						$params['site_num'] = $site_number;			

						$result = $this->_get_sites_locally( $account_id, $params );

						if( empty( $result ) ){
							$params['check_for_new'] = 1;
							$result = $this->get_site_by_site_number( $account_id, $site_number, $params );
						}
						
					}
					
					if( !empty( $result ) ){

						## Verify/Create Evident Site from SCCI Data
						$evident_site = $this->_create_evident_site( $account_id, $result);
						if( !empty( $evident_site['site_id'] ) ){
							
							if( isset( $result->evident_site_id ) && empty( $result->evident_site_id ) ){
								$result->evident_site_id = $evident_site['site_id'];
							}
							
							## Pull Jobs
							$get_jobs = $this->retrieve_site_jobs( $account_id, $site_number );

						}
						
						$this->session->set_flashdata( 'message','Tesseract Site retrieved Successfully' );
						$result = !empty( $result ) ? $result : false;

					} else {
						$this->session->set_flashdata( 'message','Unabled to Retrieve Site' );
					}
					
				}
				
			} else {
	
				$url_endpoint 	.= $site_number;

				## Single Site Number
				$check_for_new	= !empty( $params['check_for_new'] ) ? $params['check_for_new'] : false;
			
				if( !empty( $check_for_new ) ){
					
					$tesseract_site  = $this->tesseract_common->api_dispatcher( $url_endpoint, false, [ 'method'=>$method_type, 'auth_token'=>$this->tess_api_token, 'auth_type'=>'token' ] );
					
					if( !empty( $tesseract_site ) && !empty( $tesseract_site->success ) ){
						$this->session->set_flashdata( 'message','Tesseract Site data retrieved Successfully' );
						$result = !empty( $tesseract_site->site ) ? $tesseract_site->site[0] : false;

						if( !empty( $result ) ){
							$result = $this->_save_site( $account_id, $result );
						}
						
					} else {
						$this->session->set_flashdata( 'message','Unabled to Retrieve Tesseract Task(s)' );
					}
					
				} else {
					//Get the results locally
					$params['site_num'] = $site_number;			

					$result = $this->_get_sites_locally( $account_id, $params );

					if( empty( $result ) ){
						$params['check_for_new'] = 1;
						$result = $this->get_site_by_site_number( $account_id, $site_number, $params );
					}
					
				}
				
				if( !empty( $result ) ){

					## Verify/Create Evident Site from SCCI Data
					$evident_site = $this->_create_evident_site( $account_id, $result);
					if( !empty( $evident_site['site_id'] ) ){
						
						if( isset( $result->evident_site_id ) && empty( $result->evident_site_id ) ){
							$result->evident_site_id = $evident_site['site_id'];
						}
						
						## Pull Jobs
						$get_jobs = $this->retrieve_site_jobs( $account_id, $site_number );

					}
					
					$this->session->set_flashdata( 'message','Tesseract Site retrieved Successfully' );
					$result = !empty( $result ) ? $result : false;

				} else {
					$this->session->set_flashdata( 'message','Unabled to Retrieve Site' );
				}
			}
		}
		
		return $result;
	}
	
	
	/** Save a Site/Building locally **/
	public function _save_site( $account_id = false, $data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $data ) ){
			
			$site_data = is_object( $data ) ? array_change_key_case( object_to_array( $data ), CASE_LOWER ) : array_change_key_case( $data, CASE_LOWER );

			$check_exists = $this->db->select( 'site.site_id, site.external_site_ref, site.site_reference, tesseract_sites.evident_site_id, tesseract_sites.site_num' )
				->join( 'site', 'site.external_site_ref = tesseract_sites.site_num', 'left' )
				->where( 'tesseract_sites.account_id', $account_id )
				->where( 'tesseract_sites.site_num', $site_data['site_num'] )
				->get( 'tesseract_sites' )
				->row();

			$site_data 				= $this->ssid_common->_filter_data( 'tesseract_sites', $site_data );
			$site_data['account_id'] = $account_id;

			if( !empty( $check_exists ) ){

				if( empty( $site_data['evident_site_id'] ) ){
					$site_data['evident_site_id']	=  !empty( $check_exists->evident_site_id ) ? $check_exists->evident_site_id : null;
				}
				
				$site_data['updated_by']	=  $this->ion_auth->_current_user->id;

				$this->db->where( 'tesseract_sites.site_num', $check_exists->site_num )
					->where( 'tesseract_sites.site_num', $check_exists->site_num )
					->update( 'tesseract_sites', $site_data );
					
				$site_data['site_num']=  $check_exists->site_num;
				$result = ( $this->db->trans_status() !== FALSE ) ? $site_data : false;
			} else {
				
				if( empty( $site_data['evident_site_id'] ) ){
					$site = $this->db->select( 'evident_site_id, call_num' )
						->where( 'tesseract_jobs.call_site_num', $site_data['site_num'] )
						->get_where( 'tesseract_jobs', [ 'tesseract_jobs.account_id'=>$account_id ] )
						->row();
					$site_data['evident_site_id']	= !empty( $site->evident_site_id ) ? $site->evident_site_id : null;
				}				
				
				$site_data['created_by']	=  $this->ion_auth->_current_user->id;
				$this->db->insert( 'tesseract_sites', $site_data );
				$site_data['evident_site_id']=  $this->db->insert_id();
				$result = ( $this->db->trans_status() !== FALSE ) ? $site_data : false;

			}

		}
		return $result;
	}
	
	
	/** *Get Locally saved Sites/Buildings */
	public function _get_sites_locally( $account_id = false, $params = false ){
		$result = false;
		if( !empty( $account_id ) ){

			if( !empty( $params['evident_site_id'] ) ){
				$this->db->where( 'tesseract_sites.evident_site_id', $params['evident_site_id'] );
			}

			if( !empty( $params['site_num'] ) ){
				$this->db->where( 'tesseract_sites.site_num', $params['site_num'] );
			}

			$query = $this->db->select( 'tesseract_sites.*, CONCAT(user_creater.first_name," ",user_creater.last_name) `created_by`, CONCAT(user_modifier.first_name," ",user_modifier.last_name) `updated_by`', false )
				->where( 'tesseract_sites.account_id', $account_id )
				->join( 'user `user_creater`', 'user_creater.id = tesseract_sites.created_by', 'left' )
				->join( 'user `user_modifier`', 'user_modifier.id = tesseract_sites.updated_by', 'left' )
				->order_by( 'tesseract_sites.site_num' )
				->group_by( 'tesseract_sites.site_num' )
				->get( 'tesseract_sites' );

			if( $query->num_rows() > 0 ){

				$this->session->set_flashdata( 'message','Site data retrieved successfully.' );
				
				if( !empty( $params['site_num'] ) || !empty( $params['evident_site_id'] ) ){
					$result 	= $query->result()[0];
				} else {
					$result = $query->result();
				}

			} else {
				$this->session->set_flashdata( 'message','No data found matching criteria.' );
			}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing the required information.' );
		}
		return $result;
	}
	
	
	/** Create NEW BLOB **/
	public function create_blob( $account_id = false, $postdata = false ){
		
		$result = (object)[
			'data'	 => false,
			'success'=> false,
			'message'=> ''
		];
		
		if( !empty( $account_id ) && !empty( $postdata ) ){
			$url_endpoint 	= 'Blob/CreateBlob';
			$method_type	= 'POST';
			$data			= [];
			foreach( $postdata as $col => $value ){
				$data[$col] = ( $col == 'blobMap_Num' ) ? intval( trim( $value ) ) : trim( $value );
			}
			
			$error_log = $this->_prepare_error_log( $account_id, $url_endpoint, $data );
			
			unset( $postdata['account_id'], $data['account_id'] );

			$tesseract_blob = $this->tesseract_common->api_dispatcher( $url_endpoint, json_encode( $data ), [ 'method'=>$method_type, 'auth_token'=>$this->tess_api_token, 'auth_type'=>'token' ] );
			
			if( !empty( $tesseract_blob ) && !empty( $tesseract_blob->success ) ){
				$result->data 	 = $tesseract_blob;
				$result->success = true;
				$result->message = 'Tesseract Blob created successfully';
				$this->session->set_flashdata( 'message','Tesseract Blob created successfully' );
			} else {
				
				## Log Error
				$error_log['api_call_desc'] 	= __METHOD__;
				$error_log['api_error_details'] = json_encode( $tesseract_blob );
				$this->_log_tesseract_errors( $error_log );

				$result->data 	 = false;
				$result->success = false;
				$result->message = 'Tesseract create Blob failed';
				$this->session->set_flashdata( 'message', 'Tesseract create Blob failed' );
			}
		}
		return $result;
	}	
	
	/** Update an Existing BLOB **/
	public function update_blob( $account_id = false, $postdata = false ){
		
		$result = (object)[
			'data'	 => false,
			'success'=> false,
			'message'=> ''
		];
		
		if( !empty( $account_id ) && !empty( $postdata ) ){
			$url_endpoint 	= 'Blob/UpdateBlobByBlobMapNumber';
			$method_type	= 'POST';
			$data			= [];
			foreach( $postdata as $col => $value ){
				$data[$col] = ( $col == 'blobMap_Num' ) ? (int) trim( $value ) : trim( $value );
			}
			
			$error_log = $this->_prepare_error_log( $account_id, $url_endpoint, $data );
			
			unset( $postdata['account_id'], $data['account_id'] );

			$tesseract_blob = $this->tesseract_common->api_dispatcher( $url_endpoint, json_encode( $data ), [ 'method'=>$method_type, 'auth_token'=>$this->tess_api_token, 'auth_type'=>'token' ] );

			if( !empty( $tesseract_blob ) && !empty( $tesseract_blob->success ) ){
				$result->data 	 = $tesseract_blob;
				$result->success = true;
				$result->message = 'Tesseract Blob updated successfully';
				$this->session->set_flashdata( 'message','Tesseract Blob updated successfully' );
			} else {
				
				## Log Error
				$error_log['api_call_desc'] 	= __METHOD__;
				$error_log['api_error_details'] = json_encode( $tesseract_blob );
				$this->_log_tesseract_errors( $error_log );
				
				$result->data 	 = false;
				$result->success = false;
				$result->message = 'Tesseract update Blob failed';
				$this->session->set_flashdata( 'message', 'Tesseract update Blob failed' );
			}
		}
		return $result;
	}
	
	
	/** Get BLob by Blob Map Number **/
	public function get_blob_by_blob_map_number( $account_id = false, $blob_map_number = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $blob_map_number ) ){
			$url_endpoint 	= 'Blob/GetBlobByBlobMapNumber/'.$blob_map_number;
			$method_type	= 'GET';
			$tesseract_site  = $this->tesseract_common->api_dispatcher( $url_endpoint, false, [ 'method'=>$method_type, 'auth_token'=>$this->tess_api_token, 'auth_type'=>'token' ] );
			if( !empty( $tesseract_site ) ){
				$this->session->set_flashdata( 'message','Tesseract Blob retrieved Successfully' );
				$result = !empty( $tesseract_site->site ) ? $tesseract_site->site : false;
			} else {
				$this->session->set_flashdata( 'message','Unabled to Retrieve Blob' );
			}
		}
		
		return $result;
	}
	
	
	/** Send Attachment **/
	public function send_attachment( $account_id = false, $params = false ){
		
		$result = (object)[
			'data'	 => false,
			'success'=> false,
			'message'=> ''
		];

		if( !empty( $account_id ) ){
			
			## Process the files locally
			if(  !empty( $_FILES['attachments']['name'] ) ) {
				
				$this->load->model( 'Document_Handler_model','document_service' );
				
				$params['upload_segment'] = 'Attachment';
				$uploaded_docs = $this->document_service->process_attachments( $account_id, $params, $doc_group = 'job' );
				unset( $_FILES );
			}
			
			$method_type	= 'POST';
			$processed_files= $data = [];
			
			$uploaded_files = !empty( $uploaded_docs['documents'] ) ?  $uploaded_docs['documents'] : ( !empty( $params['documents'] ) ? $params['documents'] : false );
			
			if( !empty( $uploaded_files ) ){
			#if( !empty( $uploaded_docs['documents'] ) ){
				
				foreach( $uploaded_files as $col => $file_object ){
					$file_object	= (object) $file_object;
					$url_endpoint 	= 'Attachment/SendFile';
					$file_params 	= '';
			
					if( !empty( $file_object->doc_reference ) ) {
						$file_params .= '/'.urlencode( $file_object->doc_reference );
						$data['fileName'] = $file_object->doc_reference;
					}

					if( !empty( $file_object->document_link ) ) {
						
						$file_params .= '/'.urlencode( $this->attachments_path_name );
						
						$file_params .= '/'.urlencode( $file_object->document_link );
						$data['pathName'] = $this->attachments_path_name;

					}

					$url_endpoint	.= $file_params;

					$tesseract_attachment 	= $this->tesseract_common->api_dispatcher( $url_endpoint, json_encode( $data ), [ 'method'=>$method_type, 'auth_token'=>$this->tess_api_token, 'auth_type'=>'token' ] );

					$already_uploaded 		= ( trim( strtolower( $tesseract_attachment->message ) ) == 'file name already exists.' ) ? 1 : 0;

					#if( !empty( $tesseract_attachment ) && ( !empty( $tesseract_attachment->success ) ) ){
					if( !empty( $tesseract_attachment ) && ( !empty( $tesseract_attachment->success ) || !empty( $already_uploaded ) ) ){
						$processed_files[] = [
							'file_id'	=> strval( $file_object->document_id ),
							'file_name'	=> $file_object->doc_reference,
							'url'		=> $file_object->document_link,
							'api_url'	=> $tesseract_attachment->fileURL,
							'exists'	=> strval( $already_uploaded )
						];
					}
				}
			}

			if( !empty( $processed_files ) ){
				$result->data 	 = $processed_files;
				$result->success = true;
				$result->message = 'Tesseract Attachment(s) sent successfully';
				$this->session->set_flashdata( 'message','Tesseract Attachment(s) sent successfully' );
			} else {
				$result->data 	 = null;
				$result->success = false;
				$result->message = 'Tesseract send Attachment(s) failed';
				$this->session->set_flashdata( 'message', 'Tesseract send Attachment(s) failed' );
			}
		}
		return $result;
	}
	
	
	/** Create Attachment **/
	public function create_attachment( $account_id = false, $postdata = false ){
		
		$result = (object)[
			'data'	 => false,
			'success'=> false,
			'message'=> ''
		];
		
		if( !empty( $account_id ) && !empty( $postdata ) ){
			
			$url_endpoint 	= 'Attachment/CreateAttachment';
			$method_type	= 'POST';
			$data			= [];
			foreach( $postdata as $col => $value ){
				$data[$col] = trim( $value );
			}
			unset( $data['account_id'], $data['job_id'] );
			$data['file']	= null;

			$tesseract_attachment = $this->tesseract_common->api_dispatcher( $url_endpoint, json_encode( $data ), [ 'method'=>$method_type, 'auth_token'=>$this->tess_api_token, 'auth_type'=>'token' ] );

			if( !empty( $tesseract_attachment ) && !empty( $tesseract_attachment->success ) ){
				$result->data 	 = $tesseract_attachment;
				$result->success = true;
				$result->message = 'Tesseract Attachment created successfully';
				$this->session->set_flashdata( 'message','Tesseract Attachment created successfully' );
			} else {
				$result->data 	 = false;
				$result->success = false;
				$result->message = 'Tesseract create Attachment failed';
				$this->session->set_flashdata( 'message', 'Tesseract create Attachment failed' );
			}
		}
		return $result;
	}
	
	
	/** Get Checklist(s) **/
	public function get_checklists( $account_id = false, $checklist_id = false, $params = false ){

		$result = false;
		
		if( !empty( $account_id ) ){
		
			$params				= convert_to_array( $params );
			$checklist_id 		= !empty( $checklist_id ) 						? $checklist_id : ( !empty( $params['id'] ) ? $params['id'] : ( !empty( $params['checklist_id'] ) ? !empty( $params['checklist_id'] ) : false ) );
			$check_for_new		= !empty( $params['check_for_new'] ) 			? $params['check_for_new'] : false;
			$where				= !empty( $params['where'] ) 					? convert_to_array( $params['where'] ) : false;
			$local_records_only	= !empty( $where['local_records_only'] ) 		? $where['local_records_only'] : false;

			if( !empty( $local_records_only ) ){
				$params['local_records_only'] = $local_records_only;
				$result = $this->_get_checklists_locally( $account_id, $params );
			} else {
			
				#If this is set to True, Check the remote server first, then save and retieve
				if( !empty( $check_for_new ) ){
					
					$url_endpoint 	= 'Checklist/GetChecklist';

					if( !empty( $checklist_id ) ){
						$url_endpoint 	.= '?id='.$checklist_id;
					}
					
					if( !empty( $params['order_by'] ) ) {
						if( strpos( $url_endpoint, '?') !== false ){
							$url_endpoint .= '&orderByColumn='.$params['order_by'];
						} else {
							$url_endpoint .= '?orderByColumn='.$params['order_by'];
						}
					}
					
					if( !empty( $params['sort_by'] ) ) {
						if( strpos( $url_endpoint, '?') !== false ){
							$url_endpoint .= '&sortBy='.$params['sort_by'];
						} else {
							$url_endpoint .= '?sortBy='.$params['sort_by'];
						}
					}
					
					if( !empty( $params['limit'] ) ) {
						if( strpos( $url_endpoint, '?') !== false ){
							$url_endpoint .= '&limit='.$params['limit'];
						} else {
							$url_endpoint .= '?limit='.$params['limit'];
						}
					}
					
					if( !empty( $params['offset'] ) ) {
						if( strpos( $url_endpoint, '?') !== false ){
							$url_endpoint .= '&offset='.$params['offset'];
						} else {
							$url_endpoint .= '?offset='.$params['offset'];
						}
					}

					$method_type			= 'GET';
				
					$tesseract_checklists  	= $this->tesseract_common->api_dispatcher( $url_endpoint, false, [ 'method'=>$method_type, 'auth_token'=>$this->tess_api_token, 'auth_type'=>'token' ] );

					if( !empty( $tesseract_checklists ) && !empty( $tesseract_checklists->success ) ){
						$this->session->set_flashdata( 'message','Tesseract Checklist(s) data retrieved Successfully' );
						$result = !empty( $tesseract_checklists->checklist ) ? $tesseract_checklists->checklist : false;
						
						if( !empty( $result ) ){
							
							$result = $this->_save_tesseract_checklists( $account_id, $result );
							if( !empty( $checklist_id ) ){
								$result = $this->_get_checklists_locally( $account_id, [ 'checklist_id' => $checklist_id ] );
							}

						}
						
					} else {
						$this->session->set_flashdata( 'message','Unabled to Retrieve Tesseract Checklist(s)' );
					}
				} else {
					//Get the results locally
					if( !empty( $checklist_id ) ){
						$params['checklist_id'] = $checklist_id;			
					}
					$result = $this->_get_checklists_locally( $account_id, $params );

					if( empty( $result ) ){
						$params['check_for_new'] = 1;
						$result = $this->get_checklists( $account_id, $checklist_id, $params );
					}
					
				}
			}
		}
		
		return $result;
	}
	
	
	/** Save Tess Checklists Locally Bridge API Version **/
	public function _save_tesseract_checklists( $account_id = false, $checklists_data = false, $options = false ){

		$result = [];
		
		if( !empty( $account_id ) && !empty( $checklists_data ) ){

			$checklists_data	= convert_to_array( $checklists_data );
			
			$new_jobs	= $existing_checklists = $new_sites = $existing_sites = $processed_successfully = [];

			foreach( $checklists_data as $key => $checklist ){

				$checklist 		  			= array_change_key_case( object_to_array( $checklist ), CASE_LOWER );
				$checklist['account_id']	= $account_id;	

				$checklist_exists = $this->db->select( 'tesseract_checklist.*, CONCAT(user_creater.first_name," ",user_creater.last_name) `created_by`, CONCAT(user_modifier.first_name," ",user_modifier.last_name) `updated_by`, jt_ref.visibility_to_customer', false )
					->where( 'tesseract_checklist.account_id', $account_id )
					->where( 'tesseract_checklist.checklist_id', $checklist['checklist_id'] )
					->join( 'user `user_creater`', 'user_creater.id = tesseract_checklist.created_by', 'left' )
					->join( 'user `user_modifier`', 'user_modifier.id = tesseract_checklist.created_by', 'left' )
					->join( 'tesseract_job_type_checklist_ref jt_ref', 'tesseract_checklist.checklist_id = jt_ref.checklist_id', 'left' )
					->get( 'tesseract_checklist' )
					->row();
				
				$checklist 	= $this->ssid_common->_filter_data( 'tesseract_checklist', $checklist );
				
				if( !empty( $checklist_exists ) ){
					$checklist['updated_by']	=  $this->ion_auth->_current_user->id;					
					$update = $this->db->where( 'tesseract_checklist.account_id', $account_id )
						->where( 'tesseract_checklist.checklist_id', $checklist_exists->checklist_id )
						->update( 'tesseract_checklist', $checklist );
					
					$existing_checklists[] 		= $checklist_exists;
					$result[] = (array) $checklist_exists;
				} else {

					$checklist['created_by']	=  $this->ion_auth->_current_user->id;

					## Create Tesseract Checklist					
					$this->db->insert( 'tesseract_checklist', $checklist );
					$new_checklist 	= ( array ) $this->db->select( 'tesseract_checklist.*, jt_ref.criteria_source ,jt_ref.criteria_id `checklist_order_id`, jt_ref.responseset_link_type, CONCAT(user_creater.first_name," ",user_creater.last_name) `created_by`, CONCAT(user_modifier.first_name," ",user_modifier.last_name) `updated_by`, jt_ref.visibility_to_customer', false )
						->join( 'user `user_creater`', 'user_creater.id = tesseract_checklist.created_by', 'left' )
						->join( 'user `user_modifier`', 'user_modifier.id = tesseract_checklist.created_by', 'left' )
						->join( 'tesseract_job_type_checklist_ref jt_ref', 'tesseract_checklist.checklist_id = jt_ref.checklist_id', 'left' )
						->get_where( 'tesseract_checklist', [ 'evi_checklist_id'=>$this->db->insert_id() ] )->row();
					$result[] = $new_checklist;
				}
				
			}

		}
		
		return $result;
	}
	
	
	/** *Get Locally saved Checklists */
	public function _get_checklists_locally( $account_id = false, $params = false ){
		$result = false;
		if( !empty( $account_id ) ){

			if( !empty( $params['evi_checklist_id'] ) ){
				$this->db->where( 'tesseract_checklist.evi_checklist_id', $params['evi_checklist_id'] );
			}
			
			if( !empty( $params['checklist_id'] ) ){
				$this->db->where( 'tesseract_checklist.checklist_id', $params['checklist_id'] );
			}

			$this->db->select( 'tesseract_checklist.*, CONCAT(user_creater.first_name," ",user_creater.last_name) `created_by`, CONCAT(user_modifier.first_name," ",user_modifier.last_name) `updated_by`', false )
					->where( 'tesseract_checklist.account_id', $account_id )
					->join( 'user `user_creater`', 'user_creater.id = tesseract_checklist.created_by', 'left' )
					->join( 'user `user_modifier`', 'user_modifier.id = tesseract_checklist.created_by', 'left' );

			if( !empty( $params['local_records_only'] ) ){
				## Non-tesseract Checklists
				$this->db->where( 'tesseract_checklist.remote_checklist !=', 1 );
			} else {
				$query = $this->db->select( 'jt_ref.criteria_source ,jt_ref.criteria_id `checklist_order_id`, jt_ref.responseset_link_type, jt_ref.task_type, jt_ref.visibility_to_customer, job_types.job_type_id, job_types.job_type, job_types.external_job_type_ref', false )
					->where( 'tesseract_checklist.remote_checklist', 1 )
					->join( 'job_required_checklists `jrc`', 'jrc.checklist_id = tesseract_checklist.checklist_id', 'left' )
					->join( 'job_types', 'job_types.job_type_id = jrc.job_type_id', 'left' )
					->join( 'tesseract_job_type_checklist_ref jt_ref', 'tesseract_checklist.checklist_id = jt_ref.checklist_id', 'left' );
			}

			$query = $this->db->order_by( 'tesseract_checklist.checklist_id' )
					->group_by( 'tesseract_checklist.checklist_id' )
					->get( 'tesseract_checklist' );

			if( $query->num_rows() > 0 ){

				$this->session->set_flashdata( 'message','Checklist data retrieved successfully.' );
				
				if( !empty( $params['checklist_id'] ) || !empty( $params['evi_checklist_id'] ) ){
					$result 	= $query->result()[0];
					$questions 	= $this->get_questions_by_checklist_id( $account_id, $result->checklist_id );
					$result->checklist_questions = !empty( $questions ) ? $questions : null;
				} else {
					$result = [];
					foreach( $query->result() as $k => $row ){
						#$questions 	= $this->get_questions_by_checklist_id( $account_id, $row->checklist_id );
						#$row->checklist_questions = !empty( $questions ) ? $questions : null;
						$row->checklist_questions = null;
						$result[$k] = $row;
					}
					$result = $query->result();
				}

			} else {
				$this->session->set_flashdata( 'message','No data found matching criteria.' );
			}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing the required information.' );
		}
		return $result;
	}
	
	
	/** Get Checklist Questions By ID**/
	public function get_questions_by_checklist_id( $account_id = false, $checklist_id = false, $params = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $checklist_id ) ){
			
			$params			= convert_to_array( $params );
			$checklist_id 	= !empty( $checklist_id ) ? $checklist_id : ( !empty( $params['id'] ) ? $params['id'] : ( !empty( $params['checklist_id'] ) ? !empty( $params['checklist_id'] ) : false ) );
			$check_for_new	= !empty( $params['check_for_new'] ) ? $params['check_for_new'] : false;
			
			#If this is set to True, Check the remote server first, then save and retieve
			if( !empty( $check_for_new ) ){
				
				$url_endpoint 	= 'ChecklistQuestion/GetQuestionsByChecklistId';

				if( !empty( $params['checklist_id'] ) ) {
					if( strpos( $url_endpoint, '?') !== false ){
						$url_endpoint .= '&checklistId='.$params['checklist_id'];
					} else {
						$url_endpoint .= '?checklistId='.$params['checklist_id'];
					}
				}
				
				if( !empty( $params['order_by'] ) ) {
					if( strpos( $url_endpoint, '?') !== false ){
						$url_endpoint .= '&orderByColumn='.$params['order_by'];
					} else {
						$url_endpoint .= '?orderByColumn='.$params['order_by'];
					}
				}
				
				if( !empty( $params['sort_by'] ) ) {
					if( strpos( $url_endpoint, '?') !== false ){
						$url_endpoint .= '&sortBy='.$params['sort_by'];
					} else {
						$url_endpoint .= '?sortBy='.$params['sort_by'];
					}
				}
				
				if( !empty( $params['limit'] ) ) {
					if( strpos( $url_endpoint, '?') !== false ){
						$url_endpoint .= '&limit='.$params['limit'];
					} else {
						$url_endpoint .= '?limit='.$params['limit'];
					}
				}
				
				if( !empty( $params['offset'] ) ) {
					if( strpos( $url_endpoint, '?') !== false ){
						$url_endpoint .= '&offset='.$params['offset'];
					} else {
						$url_endpoint .= '?offset='.$params['offset'];
					}
				}
				
				$method_type						= 'GET';
				$tesseract_checklist_questions  	= $this->tesseract_common->api_dispatcher( $url_endpoint, false, [ 'method'=>$method_type, 'auth_token'=>$this->tess_api_token, 'auth_type'=>'token' ] );

				if( !empty( $tesseract_checklist_questions ) && !empty( $tesseract_checklist_questions->success ) ){
					$this->session->set_flashdata( 'message','Tesseract Checklist Questions data retrieved Successfully' );
					$result = !empty( $tesseract_checklist_questions->checklistQuestion ) ? $tesseract_checklist_questions->checklistQuestion : false;
					
					if( !empty( $result ) ){
						$result = $this->_save_tesseract_checklist_questions( $account_id, $result );
					}
					
				} else {
					$this->session->set_flashdata( 'message','Unabled to Retrieve Tesseract Checklist Questions' );
				}

			} else {
				//Get the results locally
				if( !empty( $checklist_id ) ){
					$params['question_checklist_id'] = $checklist_id;			
				}

				$result = $this->_get_checklist_questions_locally( $account_id, $params );

				if( empty( $result ) ){
					$params['check_for_new'] = 1;
					$result = $this->get_questions_by_checklist_id( $account_id, $checklist_id, $params );
				}
				
			}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
			return false;
		}
		
		return $result;
	}
	
	
	/** Save Tess Checklist Questions Locally Bridge API Version **/
	public function _save_tesseract_checklist_questions( $account_id = false, $checklist_questions_data = false, $options = false ){

		$result = [];
		
		if( !empty( $account_id ) && !empty( $checklist_questions_data ) ){
			$checklist_questions_data	= convert_to_array( $checklist_questions_data );
			$existing_checklist_questions = [];

			foreach( $checklist_questions_data as $key => $checklist_question ){
				
				$checklist_question 		  			= array_change_key_case( object_to_array( $checklist_question ), CASE_LOWER );
				$checklist_question['account_id']	=  $account_id;	
				
				$checklist_exists = $this->db->select( 'tesseract_checklist_question.*, CONCAT(user_creater.first_name," ",user_creater.last_name) `created_by`, CONCAT(user_modifier.first_name," ",user_modifier.last_name) `updated_by`', false )
					->where( 'tesseract_checklist_question.account_id', $account_id )
					->where( 'tesseract_checklist_question.question_id', $checklist_question['question_id'] )
					->join( 'user `user_creater`', 'user_creater.id = tesseract_checklist_question.created_by', 'left' )
					->join( 'user `user_modifier`', 'user_modifier.id = tesseract_checklist_question.created_by', 'left' )
					->get( 'tesseract_checklist_question' )
					->row();
				
				$checklist_question 	= $this->ssid_common->_filter_data( 'tesseract_checklist_question', $checklist_question );
				
				if( !empty( $checklist_exists ) ){

					$checklist_question['updated_by']	=  $this->ion_auth->_current_user->id;					
					$update = $this->db->where( 'tesseract_checklist_question.account_id', $account_id )
						->where( 'tesseract_checklist_question.question_id', $checklist_exists->question_id )
						->update( 'tesseract_checklist_question', $checklist_question );
					
					$existing_checklist_questions[] 		= $checklist_exists;
					$result[] = (array) $checklist_exists;
				} else {

					$checklist_question['created_by']	=  $this->ion_auth->_current_user->id;

					## Create Tesseract Checklist					
					$this->db->insert( 'tesseract_checklist_question', $checklist_question );
					$new_checklist_question 	= ( array ) $this->db->select( 'tesseract_checklist_question.*, CONCAT(user_creater.first_name," ",user_creater.last_name) `created_by`, CONCAT(user_modifier.first_name," ",user_modifier.last_name) `updated_by`', false )
						->join( 'user `user_creater`', 'user_creater.id = tesseract_checklist_question.created_by', 'left' )
						->join( 'user `user_modifier`', 'user_modifier.id = tesseract_checklist_question.created_by', 'left' )
						->get_where( 'tesseract_checklist_question', [ 'evi_question_id'=>$this->db->insert_id() ] )->row();
					$result[] = $new_checklist_question;
				}
				
			}

		}
		
		return $result;
	}
	
	
	/** *Get (locally) Saved Checklist Questions */
	public function _get_checklist_questions_locally( $account_id = false, $params = false ){
		$result = false;
		if( !empty( $account_id ) ){

			if( !empty( $params['evi_question_id'] ) ){
				$this->db->where( 'tesseract_checklist_question.evi_question_id', $params['evi_question_id'] );
			}
			
			$checklist_id = !empty( $params['checklist_id'] ) ? $params['checklist_id'] : ( !empty( $params['question_checklist_id'] ) ? $params['question_checklist_id'] : false );
			if(  !empty( $checklist_id ) ){
				$this->db->where( 'tesseract_checklist_question.question_checklist_id', $checklist_id );
			}

			$query = $this->db->select( 'tesseract_checklist_question.*, CONCAT(user_creater.first_name," ",user_creater.last_name) `created_by`, CONCAT(user_modifier.first_name," ",user_modifier.last_name) `updated_by`', false )
				->where( 'tesseract_checklist_question.account_id', $account_id )
				->join( 'user `user_creater`', 'user_creater.id = tesseract_checklist_question.created_by', 'left' )
				->join( 'user `user_modifier`', 'user_modifier.id = tesseract_checklist_question.created_by', 'left' )
				->order_by( 'tesseract_checklist_question.question_order' )
				->group_by( 'tesseract_checklist_question.question_id' )
				->get( 'tesseract_checklist_question' );

			if( $query->num_rows() > 0 ){

				$this->session->set_flashdata( 'message','Checklist Questions data retrieved successfully.' );
				
				if( !empty( $params['question_id'] ) || !empty( $params['evi_question_id'] ) ){
					$result = $query->result()[0];
				} else {
					$result = $query->result();
				}

			} else {
				$this->session->set_flashdata( 'message','No data found matching criteria.' );
			}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing the required information.' );
		}
		return $result;
	}
	
	
	
	/** Get Checklist Criteria **/
	public function get_checklist_criteria( $account_id = false, $checklist_id = false, $params = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $checklist_id ) ){
			
			$params			= convert_to_array( $params );
			$checklist_id 	= !empty( $checklist_id ) ? $checklist_id : ( !empty( $params['id'] ) ? $params['id'] : ( !empty( $params['checklist_id'] ) ? !empty( $params['checklist_id'] ) : false ) );
			$check_for_new	= !empty( $params['check_for_new'] ) ? $params['check_for_new'] : false;
			
			#If this is set to True, Check the remote server first, then save and retieve
			if( !empty( $check_for_new ) ){
				
				$url_endpoint 	= 'ChecklistCriteria/GetChecklistCriteriaByChecklistId';

				if( !empty( $params['checklist_id'] ) ) {
					if( strpos( $url_endpoint, '?') !== false ){
						$url_endpoint .= '&checklistId='.$params['checklist_id'];
					} else {
						$url_endpoint .= '?checklistId='.$params['checklist_id'];
					}
				}
				
				if( !empty( $params['order_by'] ) ) {
					if( strpos( $url_endpoint, '?') !== false ){
						$url_endpoint .= '&orderByColumn='.$params['order_by'];
					} else {
						$url_endpoint .= '?orderByColumn='.$params['order_by'];
					}
				}
				
				if( !empty( $params['sort_by'] ) ) {
					if( strpos( $url_endpoint, '?') !== false ){
						$url_endpoint .= '&sortBy='.$params['sort_by'];
					} else {
						$url_endpoint .= '?sortBy='.$params['sort_by'];
					}
				}
				
				if( !empty( $params['limit'] ) ) {
					if( strpos( $url_endpoint, '?') !== false ){
						$url_endpoint .= '&limit='.$params['limit'];
					} else {
						$url_endpoint .= '?limit='.$params['limit'];
					}
				}
				
				if( !empty( $params['offset'] ) ) {
					if( strpos( $url_endpoint, '?') !== false ){
						$url_endpoint .= '&offset='.$params['offset'];
					} else {
						$url_endpoint .= '?offset='.$params['offset'];
					}
				}
				
				$method_type					= 'GET';
				$tesseract_checklist_criteria  	= $this->tesseract_common->api_dispatcher( $url_endpoint, false, [ 'method'=>$method_type, 'auth_token'=>$this->tess_api_token, 'auth_type'=>'token' ] );
				
				if( !empty( $tesseract_checklist_criteria ) && !empty( $tesseract_checklist_criteria->success ) ){
					$this->session->set_flashdata( 'message','Tesseract Checklist Criteria data retrieved Successfully' );
					$result = !empty( $tesseract_checklist_criteria->checklistCriteria ) ? $tesseract_checklist_criteria->checklistCriteria : false;
					
					if( !empty( $result ) ){
						$result = $this->_save_tesseract_checklist_criteria( $account_id, $result );
					}
					
				} else {
					$this->session->set_flashdata( 'message','Unabled to Retrieve Tesseract Checklist Criteria' );
				}

			} else {
				//Get the results locally
				if( !empty( $checklist_id ) ){
					$params['criteria_checklist_id'] = $checklist_id;			
				}
				$result = $this->_get_checklist_criteria_locally( $account_id, $params );

				if( empty( $result ) ){
					$params['check_for_new'] = 1;
					$result = $this->get_checklist_criteria( $account_id, $checklist_id, $params );
				}
				
			}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
			return false;
		}
		
		return $result;
	}
	
	
	/** Save Tess Checklist Criteria Locally Bridge API Version **/
	public function _save_tesseract_checklist_criteria( $account_id = false, $checklist_criteria_data = false, $options = false ){

		$result = [];
		
		if( !empty( $account_id ) && !empty( $checklist_criteria_data ) ){
			$checklist_criteria_data	= convert_to_array( $checklist_criteria_data );
			$existing_checklist_criteria = [];

			foreach( $checklist_criteria_data as $key => $checklist_criteria ){
				
				$checklist_criteria 		  			= array_change_key_case( object_to_array( $checklist_criteria ), CASE_LOWER );
				$checklist_criteria['account_id']	=  $account_id;	
				
				$checklist_criteria_exists = $this->db->select( 'tesseract_checklist_criteria.*, CONCAT(user_creater.first_name," ",user_creater.last_name) `created_by`, CONCAT(user_modifier.first_name," ",user_modifier.last_name) `updated_by`', false )
					->where( 'tesseract_checklist_criteria.account_id', $account_id )
					->where( 'tesseract_checklist_criteria.criteria_id', $checklist_criteria['criteria_id'] )
					->join( 'user `user_creater`', 'user_creater.id = tesseract_checklist_criteria.created_by', 'left' )
					->join( 'user `user_modifier`', 'user_modifier.id = tesseract_checklist_criteria.created_by', 'left' )
					->get( 'tesseract_checklist_criteria' )
					->row();
				
				$checklist_criteria 	= $this->ssid_common->_filter_data( 'tesseract_checklist_criteria', $checklist_criteria );
				
				if( !empty( $checklist_criteria_exists ) ){

					$checklist_criteria['updated_by']	=  $this->ion_auth->_current_user->id;					
					$update = $this->db->where( 'tesseract_checklist_criteria.account_id', $account_id )
						->where( 'tesseract_checklist_criteria.criteria_id', $checklist_criteria_exists->criteria_id )
						->update( 'tesseract_checklist_criteria', $checklist_criteria );
					
					$existing_checklist_criteria[] 		= $checklist_criteria_exists;
					$result[] = (array) $checklist_criteria_exists;
				} else {

					$checklist_criteria['created_by']	=  $this->ion_auth->_current_user->id;

					## Create Tesseract Checklist Criteria					
					$this->db->insert( 'tesseract_checklist_criteria', $checklist_criteria );
					$new_checklist_criteria 	= ( array ) $this->db->select( 'tesseract_checklist_criteria.*, CONCAT(user_creater.first_name," ",user_creater.last_name) `created_by`, CONCAT(user_modifier.first_name," ",user_modifier.last_name) `updated_by`', false )
						->join( 'user `user_creater`', 'user_creater.id = tesseract_checklist_criteria.created_by', 'left' )
						->join( 'user `user_modifier`', 'user_modifier.id = tesseract_checklist_criteria.created_by', 'left' )
						->get_where( 'tesseract_checklist_criteria', [ 'evi_criteria_id'=>$this->db->insert_id() ] )->row();
					$result[] = $new_checklist_criteria;
				}

			}

		}
		
		return $result;
	}
	
	
	/** *Get (locally) Saved Checklist Criteria */
	public function _get_checklist_criteria_locally( $account_id = false, $params = false ){
		$result = false;
		if( !empty( $account_id ) ){

			if( !empty( $params['evi_criteria_id'] ) ){
				$this->db->where( 'tesseract_checklist_criteria.evi_criteria_id', $params['evi_criteria_id'] );
			}
			
			if( !empty( $params['checklist_id'] ) ){
				$this->db->where( 'tesseract_checklist_criteria.criteria_checklist_id', $params['checklist_id'] );
			}

			$query = $this->db->select( 'tesseract_checklist_criteria.*, CONCAT(user_creater.first_name," ",user_creater.last_name) `created_by`, CONCAT(user_modifier.first_name," ",user_modifier.last_name) `updated_by`', false )
				->where( 'tesseract_checklist_criteria.account_id', $account_id )
				->join( 'user `user_creater`', 'user_creater.id = tesseract_checklist_criteria.created_by', 'left' )
				->join( 'user `user_modifier`', 'user_modifier.id = tesseract_checklist_criteria.created_by', 'left' )
				->get( 'tesseract_checklist_criteria' );

			if( $query->num_rows() > 0 ){

				$this->session->set_flashdata( 'message','Checklist Criteria data retrieved successfully.' );
				
				if( !empty( $params['checklist_id'] ) || !empty( $params['evi_criteria_id'] ) ){
					$result = $query->result()[0];
				} else {
					$result = $query->result();
				}

			} else {
				$this->session->set_flashdata( 'message','No data found matching criteria.' );
			}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing the required information.' );
		}
		return $result;
	}
	
	
	/** Get Checklist Criteria Field by Criteria ID **/
	public function get_checklist_criteria_field( $account_id = false, $criteria_id = false, $params = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $criteria_id ) ){
			
			$params			= convert_to_array( $params );
			$criteria_id 	= !empty( $criteria_id ) ? $criteria_id : ( !empty( $params['id'] ) ? $params['id'] : ( !empty( $params['criteria_id'] ) ? !empty( $params['criteria_id'] ) : false ) );
			$check_for_new	= !empty( $params['check_for_new'] ) ? $params['check_for_new'] : false;
			
			#If this is set to True, Check the remote server first, then save and retieve
			if( !empty( $check_for_new ) ){
				
				$url_endpoint 	= 'ChecklistCriteriaField/GetCriteriaFieldByCriteriaId';

				if( !empty( $params['criteria_id'] ) ) {
					if( strpos( $url_endpoint, '?') !== false ){
						$url_endpoint .= '&criteriaId='.$params['criteria_id'];
					} else {
						$url_endpoint .= '?criteriaId='.$params['criteria_id'];
					}
				}
				
				if( !empty( $params['order_by'] ) ) {
					if( strpos( $url_endpoint, '?') !== false ){
						$url_endpoint .= '&orderByColumn='.$params['order_by'];
					} else {
						$url_endpoint .= '?orderByColumn='.$params['order_by'];
					}
				}
				
				if( !empty( $params['sort_by'] ) ) {
					if( strpos( $url_endpoint, '?') !== false ){
						$url_endpoint .= '&sortBy='.$params['sort_by'];
					} else {
						$url_endpoint .= '?sortBy='.$params['sort_by'];
					}
				}
				
				if( !empty( $params['limit'] ) ) {
					if( strpos( $url_endpoint, '?') !== false ){
						$url_endpoint .= '&limit='.$params['limit'];
					} else {
						$url_endpoint .= '?limit='.$params['limit'];
					}
				}
				
				if( !empty( $params['offset'] ) ) {
					if( strpos( $url_endpoint, '?') !== false ){
						$url_endpoint .= '&offset='.$params['offset'];
					} else {
						$url_endpoint .= '?offset='.$params['offset'];
					}
				}
				
				$method_type					= 'GET';
				$tesseract_checklist_criteria_field  	= $this->tesseract_common->api_dispatcher( $url_endpoint, false, [ 'method'=>$method_type, 'auth_token'=>$this->tess_api_token, 'auth_type'=>'token' ] );
				
				if( !empty( $tesseract_checklist_criteria_field ) && !empty( $tesseract_checklist_criteria_field->success ) ){
					$this->session->set_flashdata( 'message','Tesseract Checklist Criteria Field data retrieved Successfully' );
					$result = !empty( $tesseract_checklist_criteria_field->criteriaField ) ? $tesseract_checklist_criteria_field->criteriaField : false;
					
					if( !empty( $result ) ){
						$result = $this->_save_tesseract_checklist_criteria_field( $account_id, $result );
					}
					
				} else {
					$this->session->set_flashdata( 'message','Unabled to Retrieve Tesseract Checklist Criteria Field' );
				}

			} else {
				//Get the results locally
				if( !empty( $criteria_id ) ){
					$params['criteriafield_criteria_id'] = $criteria_id;			
				}
				$result = $this->_get_checklist_criteria_field_locally( $account_id, $params );

				if( empty( $result ) ){
					$params['check_for_new'] = 1;
					$result = $this->get_checklist_criteria_field( $account_id, $criteria_id, $params );
				}
				
			}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
			return false;
		}
		
		return $result;
	}
	
	
	/** Save Tess Checklist Criteria Field Locally Bridge API Version **/
	public function _save_tesseract_checklist_criteria_field( $account_id = false, $checklist_criteria_field_data = false, $options = false ){

		$result = [];
		
		if( !empty( $account_id ) && !empty( $checklist_criteria_field_data ) ){
			$checklist_criteria_field_data	= convert_to_array( $checklist_criteria_field_data );
			$existing_checklist_criteria_field = [];

			foreach( $checklist_criteria_field_data as $key => $checklist_criteria_field ){
				
				$checklist_criteria_field 		  			= array_change_key_case( object_to_array( $checklist_criteria_field ), CASE_LOWER );
				$checklist_criteria_field['account_id']	=  $account_id;	
				
				$checklist_criteria_field_exists = $this->db->select( 'tesseract_checklist_criteria_field.*, CONCAT(user_creater.first_name," ",user_creater.last_name) `created_by`, CONCAT(user_modifier.first_name," ",user_modifier.last_name) `updated_by`', false )
					->where( 'tesseract_checklist_criteria_field.account_id', $account_id )
					->where( 'tesseract_checklist_criteria_field.criteriafield_criteria_id', $checklist_criteria_field['criteriafield_criteria_id'] )
					->join( 'user `user_creater`', 'user_creater.id = tesseract_checklist_criteria_field.created_by', 'left' )
					->join( 'user `user_modifier`', 'user_modifier.id = tesseract_checklist_criteria_field.created_by', 'left' )
					->get( 'tesseract_checklist_criteria_field' )
					->row();
				
				$checklist_criteria_field 	= $this->ssid_common->_filter_data( 'tesseract_checklist_criteria_field', $checklist_criteria_field );
				
				if( !empty( $checklist_criteria_field_exists ) ){

					$checklist_criteria_field['updated_by']	=  $this->ion_auth->_current_user->id;					
					$update = $this->db->where( 'tesseract_checklist_criteria_field.account_id', $account_id )
						->where( 'tesseract_checklist_criteria_field.criteriafield_criteria_id', $checklist_criteria_field_exists->criteriafield_criteria_id )
						->update( 'tesseract_checklist_criteria_field', $checklist_criteria_field );
					
					$existing_checklist_criteria_field[] 		= $checklist_criteria_field_exists;
					$result[] = (array) $checklist_criteria_field_exists;
				} else {

					$checklist_criteria_field['created_by']	=  $this->ion_auth->_current_user->id;

					## Create Tesseract Checklist Criteria Field					
					$this->db->insert( 'tesseract_checklist_criteria_field', $checklist_criteria_field );
					$new_checklist_criteria_field 	= ( array ) $this->db->select( 'tesseract_checklist_criteria_field.*, CONCAT(user_creater.first_name," ",user_creater.last_name) `created_by`, CONCAT(user_modifier.first_name," ",user_modifier.last_name) `updated_by`', false )
						->join( 'user `user_creater`', 'user_creater.id = tesseract_checklist_criteria_field.created_by', 'left' )
						->join( 'user `user_modifier`', 'user_modifier.id = tesseract_checklist_criteria_field.created_by', 'left' )
						->get_where( 'tesseract_checklist_criteria_field', [ 'evi_criteria_field_id'=>$this->db->insert_id() ] )->row();
					$result[] = $new_checklist_criteria_field;
				}

			}

		}
		
		return $result;
	}
	
	
	/** *Get (locally) Saved Checklist Criteria Field */
	public function _get_checklist_criteria_field_locally( $account_id = false, $params = false ){
		$result = false;
		if( !empty( $account_id ) ){

			if( !empty( $params['evi_criteria_field_id'] ) ){
				$this->db->where( 'tesseract_checklist_criteria_field.evi_criteria_field_id', $params['evi_criteria_field_id'] );
			}
			
			if( !empty( $params['criteria_id'] ) ){
				$this->db->where( 'tesseract_checklist_criteria_field.criteriafield_criteria_id', $params['criteria_id'] );
			}

			$query = $this->db->select( 'tesseract_checklist_criteria_field.*, CONCAT(user_creater.first_name," ",user_creater.last_name) `created_by`, CONCAT(user_modifier.first_name," ",user_modifier.last_name) `updated_by`', false )
				->where( 'tesseract_checklist_criteria_field.account_id', $account_id )
				->join( 'user `user_creater`', 'user_creater.id = tesseract_checklist_criteria_field.created_by', 'left' )
				->join( 'user `user_modifier`', 'user_modifier.id = tesseract_checklist_criteria_field.created_by', 'left' )
				->get( 'tesseract_checklist_criteria_field' );

			if( $query->num_rows() > 0 ){

				$this->session->set_flashdata( 'message','Checklist Criteria Field data retrieved successfully.' );
				
				if( !empty( $params['criteria_id'] ) || !empty( $params['evi_criteria_field_id'] ) ){
					$result = $query->result()[0];
				} else {
					$result = $query->result();
				}

			} else {
				$this->session->set_flashdata( 'message','No data found matching criteria.' );
			}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing the required information.' );
		}
		return $result;
	}
	
	
	/** Create NEW Checklist Response Set **/
	public function create_checklist_response_set( $account_id = false, $postdata = false ){
		
		$result = (object)[
			'data'	 => false,
			'success'=> false,
			'message'=> ''
		];
		
		if( !empty( $account_id ) && !empty( $postdata ) ){
			$url_endpoint 	= 'ChecklistResponseSet/CreateChecklistResponseSet';
			$method_type	= 'POST';
			$data			= [];
			foreach( $postdata as $col => $value ){
				$data[$col] = is_string( $value ) ? trim( $value ) : $value;
			}
			
			#Linked Task Data
			$linked_task_data = !empty( $data['responseSet_TaskData'] ) ? $data['responseSet_TaskData'] : null;
			
			#Save the Data locally
			$save_response_set = $this->_save_checklist_response_set( $account_id, $data );
			
			$error_log = $this->_prepare_error_log( $account_id, $url_endpoint, $data );
			
			unset( $data['account_id'], $data['job_id'] );
			
			if( !empty( $save_response_set['evi_responseset_id'] ) ){

				unset( $data['responseSet_TaskType'], $data['responseSet_TaskData'] );
				
				$data['responseSet_Checklist_ID'] 	= (int) $data['responseSet_Checklist_ID'];
				$data['responseSet_Call_Num'] 		= (int) $data['responseSet_Call_Num'];
				$data['responseSet_FSR_Num'] 		= !empty( $data['responseSet_FSR_Num'] ) ? intval( $data['responseSet_FSR_Num'] )  : null;
				$data['responseSet_LinkType'] 		= !empty( $data['responseSet_LinkType'] ) ? intval(  $data['responseSet_LinkType'] ) : null;
				$data['responseSet_Task_Num'] 		= !empty( $data['responseSet_Task_Num'] ) ? intval(  $data['responseSet_Task_Num'] ) : ( !empty( $save_response_set->responseset_task_num ) ? intval( $save_response_set->responseset_task_num ) : null );
				$tesseract_checklist_response_set   = $this->tesseract_common->api_dispatcher( $url_endpoint, json_encode( $data ), [ 'method'=>$method_type, 'auth_token'=>$this->tess_api_token, 'auth_type'=>'token' ] );

			} else {
				//Call update API?
				$tesseract_checklist_response_set = false;
			}
			
			if( !empty( $tesseract_checklist_response_set ) && !empty( $tesseract_checklist_response_set->success ) ){
				
				## Update Task as Complete
				if( !empty( $data['responseSet_Task_Num'] ) && !empty( $linked_task_data ) ){
					$task_params = [
						'task_Num' 			 => $data['responseSet_Task_Num'],
						'task_Call_Num' 	 => intval( $data['responseSet_Call_Num'] ),
						'task_Type'			 => $linked_task_data->task_type,
						'task_Prod_Num'		 => $linked_task_data->task_prod_num,
						'task_Scheduled_Date'=> datetime_to_iso8601( $linked_task_data->task_scheduled_date ),
						'task_Started_Date'  => datetime_to_iso8601( date( 'Y-m-d H:i:s' ) ),
						'task_Complete_Date' => datetime_to_iso8601( date( 'Y-m-d H:i:s' ) ),
					];
					$update_task = $this->update_task( $account_id, $task_params );
				}
				
				$new_checklist_responseset = $tesseract_checklist_response_set->checklistResponseSet;
				
				## Modify local records
				$this->_modify_checklist_response_set( $account_id, object_to_array( $new_checklist_responseset ) );
				
				$result->data 	 = $new_checklist_responseset;
				$result->success = true;
				$result->message = 'Tesseract Checklist Response Set created successfully';
				$this->session->set_flashdata( 'message','Tesseract Checklist Response Set created successfully' );
			} else {
				
				## Log Error
				$error_log['api_call_desc'] 	= __METHOD__;
				$error_log['api_error_details'] = json_encode( $tesseract_checklist_response_set );
				$this->_log_tesseract_errors( $error_log );
				
				$result->data 	 = false;
				$result->success = false;
				$result->message = 'Tesseract create Checklist Response Set failed';
				$this->session->set_flashdata( 'message', 'Tesseract create Checklist Response Set failed' );
			}
		}
		return $result;
	}	
	
	
	/** Save a Checklist Response Set locally **/
	public function _save_checklist_response_set( $account_id = false, $data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $data ) ){
			
			$resp_set_data 					= is_object( $data ) ? array_change_key_case( object_to_array( $data ), CASE_LOWER ) : array_change_key_case( $data, CASE_LOWER );
			
			if(  !empty( $data['responseset_id'] ) ){
				$this->db->where( 'respset.responseset_checklist_id', $resp_set_data['responseset_checklist_id'] );
			}
			
			$resp_set_data 	= $this->ssid_common->_filter_data( 'tesseract_checklist_response_set', $resp_set_data );
			
			$check_exists = $this->db->select( 'respset.evi_responseset_id, respset.responseset_id' )
				->where( 'respset.account_id', $account_id )
				->where( 'respset.responseset_checklist_id', $resp_set_data['responseset_checklist_id'] )
				->where( 'respset.responseset_call_num', $resp_set_data['responseset_call_num'] )
				->where( 'respset.responseset_linktype', $resp_set_data['responseset_linktype'] )
				->get( 'tesseract_checklist_response_set `respset` ' )
				->row();
				
			if( !empty( $check_exists ) ){
				$resp_set_data['updated_by']			=  $this->ion_auth->_current_user->id;
				$resp_set_data['responseset_fsr_num']	=  !empty( $resp_set_data['responseset_fsr_num'] )  ? intval( $resp_set_data['responseset_fsr_num'] )  : null;
				$resp_set_data['responseset_task_num']	=  !empty( $resp_set_data['responseset_task_num'] ) ? intval( $resp_set_data['responseset_task_num'] ) : null;
				$this->db->where( 'tesseract_checklist_response_set.responseset_id', $check_exists->responseset_id )
					->update( 'tesseract_checklist_response_set', $resp_set_data );
					
				
				$resp_set_data['evi_responseset_id']	=  $check_exists->evi_responseset_id;
				$resp_set_data['responseset_id']=  $check_exists->responseset_id;
				$result = ( $this->db->trans_status() !== FALSE ) ? $resp_set_data : false;
			} else {
				$resp_set_data['created_by']	=  $this->ion_auth->_current_user->id;
				$this->db->insert( 'tesseract_checklist_response_set', $resp_set_data );
				$resp_set_data['evi_responseset_id']=  $this->db->insert_id();
				$result = ( $this->db->trans_status() !== FALSE ) ? $resp_set_data : false;
			}
			
		}
		return $result;
	}
	

	/** Update an Existing Checklist Response Set **/
	public function update_checklist_response_set( $account_id = false, $postdata = false ){
		
		$result = (object)[
			'data'	 => false,
			'success'=> false,
			'message'=> ''
		];

		if( !empty( $account_id ) && !empty( $postdata ) ){
			$url_endpoint 	= 'ChecklistResponseSet/UpdateChecklistResponseSet';
			$method_type	= 'POST';
			$data			= [];
			foreach( $postdata as $col => $value ){
				$data[$col] = trim( $value );
			}
			
			
			#Update the Data locally
			#$modify_response_set = $this->_modify_checklist_response_set( $account_id, $data );

			$error_log = $this->_prepare_error_log( $account_id, $url_endpoint, $data );

			unset( $postdata['account_id'], $data['account_id'] );
			
			if( !empty( $data['responseSet_ID'] ) ){
				$data['responseSet_ID'] 			= (int) $data['responseSet_ID'];
				$data['responseSet_Checklist_ID'] 	= (int) $data['responseSet_Checklist_ID'];
				$data['responseSet_Call_Num'] 		= (int) $data['responseSet_Call_Num'];
				$data['responseSet_FSR_Num'] 		= !empty( $data['responseSet_FSR_Num'] ) ? (int) $data['responseSet_FSR_Num']  : null;
				$data['responseSet_LinkType'] 		= !empty( $data['responseSet_LinkType'] ) ? (int) $data['responseSet_LinkType'] : null;
				$data['responseSet_Task_Num'] 		= !empty( $data['responseSet_Task_Num'] ) ? (int) $data['responseSet_Task_Num'] : null;
				$tesseract_checklist_response_set  = $this->tesseract_common->api_dispatcher( $url_endpoint, json_encode( $data ), [ 'method'=>$method_type, 'auth_token'=>$this->tess_api_token, 'auth_type'=>'token' ] );

				if( !empty( $tesseract_checklist_response_set ) && !empty( $tesseract_checklist_response_set->success ) ){
					
					#Update the Data locally
					$this->_modify_checklist_response_set( $account_id, object_to_array( $tesseract_checklist_response_set->checklistResponseSet ) );
					
					$result->data 	 = $tesseract_checklist_response_set->checklistResponseSet;
					$result->success = true;
					$result->message = 'Tesseract Checklist Response Set updated successfully';
					$this->session->set_flashdata( 'message','Tesseract Checklist Response Set updated successfully' );
				} else {
					$result->data 	 = $tesseract_checklist_response_set;
					$result->success = false;
					$result->message = 'Tesseract update Checklist Response Set failed';
					$this->session->set_flashdata( 'message', 'Tesseract update Checklist Response Set failed' );
				}

			} else {
				
				## Log Error
				$error_log['api_call_desc'] 	= __METHOD__;
				$error_log['api_error_details'] = json_encode( $tesseract_checklist_response_set );
				$this->_log_tesseract_errors( $error_log );
				
				$result->data 	 = false;
				$result->success = false;
				$result->message = 'Tesseract Request missing required parameters';
				$this->session->set_flashdata( 'message', 'Tesseract Request missing required parameters' );
				return $result;
			}

		}
		return $result;
	}
	
	
	/** Update a Checklist Response Set saved locally **/
	public function _modify_checklist_response_set( $account_id = false, $data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $data ) ){
			
			$resp_set_data 					= is_object( $data ) ? array_change_key_case( object_to_array( $data ), CASE_LOWER ) : array_change_key_case( $data, CASE_LOWER );
			
			if(  !empty( $data['responseset_id'] ) ){
				#$this->db->where( 'respset.responseset_id', $resp_set_data['responseset_id'] );
			}
			
			$check_exists = $this->db->select( 'respset.evi_responseset_id, respset.responseset_id' )
				->where( 'respset.account_id', $account_id )
				->where( 'respset.responseset_checklist_id', $resp_set_data['responseset_checklist_id'] )
				->where( 'respset.responseset_call_num', $resp_set_data['responseset_call_num'] )
				->where( 'respset.responseset_linktype', $resp_set_data['responseset_linktype'] )
				->get( 'tesseract_checklist_response_set `respset` ' )
				->row();

			if( !empty( $check_exists ) ){
				$resp_set_data['updated_by']		=  $this->ion_auth->_current_user->id;
				$this->db->where( 'tesseract_checklist_response_set.responseset_id', $check_exists->responseset_id )
					->update( 'tesseract_checklist_response_set', $resp_set_data );
					
				$resp_set_data['evi_responseset_id']=  $check_exists->evi_responseset_id;
				$resp_set_data['responseset_id']	=  $check_exists->responseset_id;
				$result = ( $this->db->trans_status() !== FALSE ) ? $resp_set_data : false;
			}
			
		}
		return $result;
	}

	
	/** Create NEW Call Task **/
	public function create_task( $account_id = false, $postdata = false ){
		
		$result = (object)[
			'data'	 => false,
			'success'=> false,
			'message'=> ''
		];
		
		if( !empty( $account_id ) && !empty( $postdata ) ){
			$url_endpoint 	= 'Task/CreateTask';
			$method_type	= 'POST';
			$data			= [];
			foreach( $postdata as $col => $value ){
				$data[$col] = trim( $value );
			}
			
			#Save the Data locally
			### $this->_save_task( $account_id, $data );
			
			unset( $postdata['account_id'], $data['account_id'] );
			
			if( !empty( $data['task_Num'] ) ){
				
				#$task_Scheduled_Date = DateTime::createFromFormat( 'Y-m-d H:i:s', $data['task_Scheduled_Date'] )->format( 'Y-m-d H:i:s' );
				$data['task_Num'] 			 = (int) $data['task_Num'];
				$data['task_Call_Num'] 		 = !empty( $data['task_Call_Num'] ) ? (int) $data['task_Call_Num']  : null;
				#$data['task_FSR_Num'] 		 = !empty( $data['task_FSR_Num'] )  ? (int) $data['task_FSR_Num']  : null;
				#$data['task_Scheduled_Date'] = !empty( $data['task_Scheduled_Date'] ) ? date( 'Y-m-dd H:i:s', strtotime( $data['task_Scheduled_Date'] ) )  : date( 'd/m/Y' );
				#$data['task_Scheduled_Date'] = !empty( $data['task_Scheduled_Date'] ) ?convert_date_to_iso8601( $data['task_Scheduled_Date'] )  : convert_date_to_iso8601( date( 'd-m-Y H:i:s' ) );
				$data['task_Scheduled_Date'] = $data['task_Scheduled_Date'];
				#$data['task_Scheduled_Date'] = $task_Scheduled_Date;

				$tesseract_task 			 = $this->tesseract_common->api_dispatcher( $url_endpoint, json_encode( $data ), [ 'method'=>$method_type, 'auth_token'=>$this->tess_api_token, 'auth_type'=>'token' ] );

				
			} else {
				$result->data 	 = false;
				$result->success = false;
				$result->message = 'Tesseract Request missing required parameters';
				$this->session->set_flashdata( 'message', 'Tesseract Request missing required parameters' );
				return $result;
			}

			
			if( !empty( $tesseract_task ) && !empty( $tesseract_task->success ) ){
				$result->data 	 = $tesseract_task->task;
				$result->success = true;
				$result->message = 'Tesseract Task created successfully';
				$this->session->set_flashdata( 'message','Tesseract Task created successfully' );
			} else {
				$result->data 	 = false;
				$result->success = false;
				$result->message = 'Tesseract create Task failed';
				$this->session->set_flashdata( 'message', 'Tesseract create Task failed' );
			}
		}
		return $result;
	}
	
	
	/** Update an Existing Task **/
	public function update_task( $account_id = false, $postdata = false ){
		
		$result = (object)[
			'data'	 => false,
			'success'=> false,
			'message'=> ''
		];

		if( !empty( $account_id ) && !empty( $postdata ) ){
			$url_endpoint 	= 'Task/UpdateTask';
			$method_type	= 'POST';
			$data			= [];
			foreach( $postdata as $col => $value ){
				$data[$col] = trim( $value );
			}

			$error_log = $this->_prepare_error_log( $account_id, $url_endpoint, $data );

			unset( $postdata['account_id'], $data['account_id'] );
			
			if( !empty( $data['task_Num'] ) ){
				$data['task_Num'] 					= (int) $data['task_Num'];
				$data['task_Call_Num'] 				= !empty( $data['task_Call_Num'] ) 		? (int) $data['task_Call_Num']  	: null;
				$data['task_FSR_Num'] 				= !empty( $data['task_FSR_Num'] ) 		? (int) $data['task_FSR_Num']  		: null;
				$data['task_Dependency'] 			= !empty( $data['task_Dependency'] ) 	? (int) $data['task_Dependency']  	: null;
				$data['task_BlobMap_Num'] 			= !empty( $data['task_BlobMap_Num'] ) 	? (int) $data['task_BlobMap_Num']  	: null;
				$data['task_Est_Work'] 				= !empty( $data['task_Est_Work'] ) 		? $data['task_Est_Work']  : null;
				$tesseract_task  = $this->tesseract_common->api_dispatcher( $url_endpoint, json_encode( $data ), [ 'method'=>$method_type, 'auth_token'=>$this->tess_api_token, 'auth_type'=>'token' ] );

				if( !empty( $tesseract_task ) && !empty( $tesseract_task->success ) ){
					
					#Update the Data locally
					$this->_modify_task( $account_id, object_to_array( $tesseract_task->task ) );
					
					$result->data 	 = $tesseract_task->task;
					$result->success = true;
					$result->message = 'Tesseract Checklist Task updated successfully';
					$this->session->set_flashdata( 'message','Tesseract Checklist Task updated successfully' );
				} else {
					
					## Log Error
					$error_log['api_call_desc'] 	= __METHOD__;
					$error_log['api_error_details'] = json_encode( $tesseract_task );
					$this->_log_tesseract_errors( $error_log );
					
					$result->data 	 = $tesseract_task;
					$result->success = false;
					$result->message = 'Tesseract update Checklist Task failed';
					$this->session->set_flashdata( 'message', 'Tesseract update Checklist Task failed' );
				}

			} else {
				$result->data 	 = false;
				$result->success = false;
				$result->message = 'Tesseract Request missing required parameters';
				$this->session->set_flashdata( 'message', 'Tesseract Request missing required parameters' );
				return $result;
			}

		}
		return $result;
	}
	
	
	/** Update a Checklist Task saved locally **/
	public function _modify_task( $account_id = false, $data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $data ) ){
			
			$task_data 	= is_object( $data ) ? array_change_key_case( object_to_array( $data ), CASE_LOWER ) : array_change_key_case( $data, CASE_LOWER );
			
			$check_exists = $this->db->select( 'task.evi_task_id, task.task_num' )
				->where( 'task.account_id', $account_id )
				->where( 'task.task_num', $task_data['task_num'] )
				->where( 'task.task_call_num', $task_data['task_call_num'] )
				->get( 'tesseract_task `task` ' )
				->row();

			if( !empty( $check_exists ) ){
				$task_data['updated_by']	=  $this->ion_auth->_current_user->id;
				$this->db->where( 'tesseract_task.evi_task_id', $check_exists->evi_task_id )
					->update( 'tesseract_task', $task_data );
					
				$task_data['task_num']	=  $check_exists->task_num;
				$result = ( $this->db->trans_status() !== FALSE ) ? $task_data : false;
			}
			
		}
		return $result;
	}
	
	
	/** Get Task(s) **/
	public function get_tasks( $account_id = false, $task_num = false, $params = false ){

		$result = false;
		
		if( !empty( $account_id ) ){
		
			$params			= convert_to_array( $params );
			$task_num 		= !empty( $task_num ) ? $task_num : ( !empty( $params['task_id'] ) ? $params['task_id'] : ( !empty( $params['task_num'] ) ? !empty( $params['task_num'] ) : false ) );
			$task_call_num 	= !empty( $params['task_call_num'] ) ? $params['task_call_num'] : false;

			$check_for_new	= !empty( $params['check_for_new'] ) ? $params['check_for_new'] : false;
			
			#If this is set to True, Check the remote server first, then save and retieve
			if( !empty( $check_for_new ) ){
				
				$url_endpoint 	= 'Task/GetTaskByCallNumber';

				if( !empty( $task_num ) ){
					
					$url_endpoint .= '?orderByColumn='.$task_num;
			
					$local_task = $this->db->where( 'tesseract_task.account_id', $account_id )
						->where( 'tesseract_task.task_num', $task_num )
						->get( 'tesseract_task' )
						->row();
						
					if( !empty( $local_task ) ){
						$this->session->set_flashdata( 'message','Tesseract Task(s) retrieved Successfully' );
						$result = $local_task;
						return $result;
					}
					
				} else {
				
					if( !empty( $params['task_call_num'] ) ) {
						if( strpos( $url_endpoint, '?') !== false ){
							$url_endpoint .= '&taskCallNum='.$params['task_call_num'];
						} else {
							$url_endpoint .= '?taskCallNum='.$params['task_call_num'];
						}
					}
				
					if( !empty( $params['order_by'] ) ) {
						if( strpos( $url_endpoint, '?') !== false ){
							$url_endpoint .= '&orderByColumn='.$params['order_by'];
						} else {
							$url_endpoint .= '?orderByColumn='.$params['order_by'];
						}
					}
					
					if( !empty( $params['sort_by'] ) ) {
						if( strpos( $url_endpoint, '?') !== false ){
							$url_endpoint .= '&sortBy='.$params['sort_by'];
						} else {
							$url_endpoint .= '?sortBy='.$params['sort_by'];
						}
					}
					
					if( !empty( $params['limit'] ) ) {
						if( strpos( $url_endpoint, '?') !== false ){
							$url_endpoint .= '&limit='.$params['limit'];
						} else {
							$url_endpoint .= '?limit='.$params['limit'];
						}
					}
					
					if( !empty( $params['offset'] ) ) {
						if( strpos( $url_endpoint, '?') !== false ){
							$url_endpoint .= '&offset='.$params['offset'];
						} else {
							$url_endpoint .= '?offset='.$params['offset'];
						}
					}
				
				}
				
				$method_type		= 'GET';
				$tesseract_tasks  	= $this->tesseract_common->api_dispatcher( $url_endpoint, false, [ 'method'=>$method_type, 'auth_token'=>$this->tess_api_token, 'auth_type'=>'token' ] );
				if( !empty( $tesseract_tasks ) && !empty( $tesseract_tasks->success ) ){
					$this->session->set_flashdata( 'message','Tesseract Task(s) data retrieved Successfully' );
					$result = !empty( $tesseract_tasks->task ) ? $tesseract_tasks->task : false;
					
					if( !empty( $result ) ){
						$result = $this->_save_tesseract_tasks( $account_id, $result );
					}
					
				} else {
					$this->session->set_flashdata( 'message','Unabled to Retrieve Tesseract Task(s)' );
				}
			} else {
				//Get the results locally
				if( !empty( $task_num ) ){
					$params['task_num'] = $task_num;			
				}
				
				if( !empty( $task_call_num ) ){
					$params['task_call_num'] = $task_call_num;			
				}
				
				$result = $this->_get_tasks_locally( $account_id, $params );

				if( empty( $result ) ){
					$params['check_for_new'] = 1;
					$result = $this->get_tasks( $account_id, $task_num, $params );
				}
				
			}

		}
		
		return $result;
	}
	
	
	/** Save Tess Tasks Locally Bridge API Version **/
	public function _save_tesseract_tasks( $account_id = false, $tasks_data = false, $options = false ){

		$result = [];
		
		if( !empty( $account_id ) && !empty( $tasks_data ) ){

			$tasks_data	= convert_to_array( $tasks_data );
			$existing_tasks = $processed_successfully = [];

			foreach( $tasks_data as $key => $task ){
				
				$task 		  		= array_change_key_case( object_to_array( $task ), CASE_LOWER );
				$task['account_id']	=  $account_id;	
				
				$task_exists = $this->db->select( 'tesseract_task.*, CONCAT(user_creater.first_name," ",user_creater.last_name) `created_by`, CONCAT(user_modifier.first_name," ",user_modifier.last_name) `updated_by`', false )
					->where( 'tesseract_task.account_id', $account_id )
					->where( 'tesseract_task.task_num', $task['task_num'] )
					->where( 'tesseract_task.task_call_num', $task['task_call_num'] )
					->join( 'user `user_creater`', 'user_creater.id = tesseract_task.created_by', 'left' )
					->join( 'user `user_modifier`', 'user_modifier.id = tesseract_task.created_by', 'left' )
					->get( 'tesseract_task' )
					->row();

				$task 	= $this->ssid_common->_filter_data( 'tesseract_task', $task );
				
				if( !empty( $task_exists ) ){

					$task['updated_by']	=  $this->ion_auth->_current_user->id;					
					$update = $this->db->where( 'tesseract_task.account_id', $account_id )
						->where( 'tesseract_task.task_call_num', $task_exists->task_call_num )
						->where( 'tesseract_task.task_num', $task_exists->task_num )
						->update( 'tesseract_task', $task );
					
					$existing_tasks[] 		= $task_exists;
					$result[] = (array) $task_exists;
				} else {

					$task['created_by']	=  $this->ion_auth->_current_user->id;

					## Create Tesseract Task					
					$this->db->insert( 'tesseract_task', $task );
					$new_task 	= ( array ) $this->db->select( 'tesseract_task.*, CONCAT(user_creater.first_name," ",user_creater.last_name) `created_by`, CONCAT(user_modifier.first_name," ",user_modifier.last_name) `updated_by`', false )
						->join( 'user `user_creater`', 'user_creater.id = tesseract_task.created_by', 'left' )
						->join( 'user `user_modifier`', 'user_modifier.id = tesseract_task.created_by', 'left' )
						->get_where( 'tesseract_task', [ 'evi_task_id'=>$this->db->insert_id() ] )->row();
					$result[] = $new_task;
				}
				
			}

		}
		
		return $result;
	}
	
	
	/** *Get Locally saved Tasks */
	public function _get_tasks_locally( $account_id = false, $params = false ){
		$result = false;
		if( !empty( $account_id ) ){

			if( !empty( $params['evi_task_num'] ) ){
				$this->db->where( 'tesseract_task.evi_task_num', $params['evi_task_num'] );
			}
			
			if( !empty( $params['task_call_num'] ) ){
				$this->db->where( 'tesseract_task.task_call_num', $params['task_call_num'] );
			}

			if( !empty( $params['task_num'] ) ){
				$this->db->where( 'tesseract_task.task_num', $params['task_num'] );
			}

			$query = $this->db->select( 'tesseract_task.*, CONCAT(user_creater.first_name," ",user_creater.last_name) `created_by`, CONCAT(user_modifier.first_name," ",user_modifier.last_name) `updated_by`', false )
				->where( 'tesseract_task.account_id', $account_id )
				->join( 'user `user_creater`', 'user_creater.id = tesseract_task.created_by', 'left' )
				->join( 'user `user_modifier`', 'user_modifier.id = tesseract_task.created_by', 'left' )
				->order_by( 'tesseract_task.task_num' )
				->group_by( 'tesseract_task.task_num' )
				->get( 'tesseract_task' );

			if( $query->num_rows() > 0 ){

				$this->session->set_flashdata( 'message','Task data retrieved successfully.' );
				
				if( !empty( $params['task_num'] ) || !empty( $params['evi_task_num'] ) ){
					$result 	= $query->result()[0];
				} else {
					$result = $query->result();
				}

			} else {
				$this->session->set_flashdata( 'message','No data found matching criteria.' );
			}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing the required information.' );
		}
		return $result;
	}
	
	/** Lookup Required Checklists for a specific Job Type **/
	public function lookup_required_checklists_by_job_type( $account_id = false, $params = false ){
		
		$result = false;
		
		if( !empty( $account_id ) && !empty( $params ) ){
			
			$params		= convert_to_array( $params );
			
			$required_checklists	= [];
			
			$calt_code				= !empty( $params['calt_code'] ) 			? $params['calt_code'] 				: 'PMI';
			$call_prodfamily_code	= !empty( $params['call_prodfamily_code'] ) ? $params['call_prodfamily_code'] 	: 'All';
			$task_type				= !empty( $params['task_type'] ) 			? $params['task_type'] 				: false;
			
			## Get a list of mandatory Checklists
			$query_mandatory	= $this->db->select( 'jtcr.checklist_id, jtcr.criteria_source, jtcr.criteria_id `checklist_order_id`, jtcr.responseset_link_type, jtcr.visibility_to_customer', false )
				->where( 'jtcr.calt_code', $calt_code )
				->where( 'jtcr.call_prodfamily_code', 'All' )
				->where( 'jtcr.is_active', 1 )
				->order_by( 'jtcr.checklist_id' )
				->group_by( 'jtcr.checklist_id' )
				->get( 'tesseract_job_type_checklist_ref `jtcr`' );

			if( $query_mandatory->num_rows() > 0 ){
				#$required_checklists = array_merge( $required_checklists, $query_mandatory->result_array() );
				$required_checklists = array_merge( $required_checklists, array_column( $query_mandatory->result_array(), 'checklist_id' ) );
			}
			
			## Get a list of all Checklists matching the Criteria
			$query_required	= $this->db->select( 'jtcr.checklist_id, jtcr.criteria_source ,jtcr.criteria_id `checklist_order_id`, jtcr.responseset_link_type, jtcr.visibility_to_customer', false )
				->where( 'jtcr.calt_code', $calt_code )
				->where( 'jtcr.call_prodfamily_code', $call_prodfamily_code )
				->where( 'jtcr.task_type', $task_type )
				->where( 'jtcr.is_active', 1 )
				->order_by( 'jtcr.checklist_id' )
				->group_by( 'jtcr.checklist_id' )
				->get( 'tesseract_job_type_checklist_ref `jtcr`' );
			
			if( $query_required->num_rows() > 0 ){
				$required_checklists = array_merge( $required_checklists, array_column( $query_required->result_array(), 'checklist_id' ) );
			}
			
			return !empty( $required_checklists ) ? array_unique( $required_checklists ) : $required_checklists;
			
		} else {
			$this->session->set_flashdata( 'message','Your request is missing the required information.' );
		}
		return $result;
		
	}
	
	
	/** Create Bulk Evident Job Types from Checklist Ref Table Data **/
	public function create_job_types_from_checklist_ref( $account_id = false, $params = false ){
		
		$result = false;
		
		if( !empty( $account_id ) && !empty( $params ) ){
			
			$params				= convert_to_array( $params );

			$job_types_data		= [];
			
			$contract_id		= !empty( $params['contract_id'] ) 			? $params['contract_id'] 			: false;
			$signature_required	= !empty( $params['signature_required'] ) 	? $params['signature_required'] 	: false;
			$checklists_required= !empty( $params['checklists_required'] ) 	? $params['checklists_required'] 	: false;

			if( !empty( $contract_id ) ){
				
				$contract_exists = $this->db->select( 'contract.contract_id', false )
					->get_where( 'contract', ['account_id' => $account_id, 'contract_id'=> $contract_id ] )
					->row();

				if( !empty( $contract_exists ) ){
					
					## Get Job Types
					$query = $this->db->select( 'jtcr.job_type, jtcr.calt_code, jtcr.call_prodfamily_code, jtcr.task_type, jtcr.visibility_to_customer', false )
						->where( 'jtcr.is_active', 1 )
						->order_by( 'jtcr.job_type' )
						->group_by( 'jtcr.job_type' )
						->get( 'tesseract_job_type_checklist_ref `jtcr`' );
					
					if( $query->num_rows() > 0 ){
						
						foreach( $query->result() as $k => $row ){
							
							if( $row->call_prodfamily_code != 'All' ){

								$job_types_data[$k] = [
									'job_type' 					=> $row->job_type,
									'job_type_ref'				=> strtolower( strip_all_whitespace( $row->job_type ) ),
									'job_group' 				=> ucwords( strtolower( $row->job_type ) ),
									'job_type_desc' 			=> 'Use this Job Type for '.$row->job_type,
									'contract_id' 				=> $contract_id,
									'account_id' 				=> $account_id,
									'ra_required ' 				=> 0,
									'signature_required' 		=> $signature_required,
									'checklists_required' 		=> $checklists_required,
									'external_job_type_ref'		=> strtoupper( $row->calt_code.' - '.$row->call_prodfamily_code ),
									'external_calt_code'		=> strtoupper( $row->calt_code ),
									'external_prodfamily_code'	=> strtoupper( $row->call_prodfamily_code ),
									'external_task_type'		=> $row->task_type
								];
							
							}

						}

						if( !empty( $job_types_data ) ){
							$result = $this->_create_checklist_job_types( $account_id, $job_types_data );
						}
					}
					
				} else {
					$this->session->set_flashdata( 'message','Invalid contract ID.' );
				}
				
			} else {
				$this->session->set_flashdata( 'message','Your request is missing the required information.' );
			}
			
		} else {
			$this->session->set_flashdata( 'message','Your request is missing the required information.' );
		}
		return $result;
		
	}
	
	/** Create Checklist Job Type Records for Evident **/
	private function _create_checklist_job_types( $account_id = false, $data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $data ) ){

			foreach( $data as $k => $job_type ){
			
				## Create Job Type
				$check_exists = $this->db->select( 'job_types.job_type_id, job_types.job_type', false )
					->where( 'job_types.account_id', $account_id )
					->where( '( job_types.job_type = "'.$job_type['job_type'].'" OR job_types.job_type_ref = "'.$job_type['job_type_ref'].'" )' )
					->limit( 1 )
					->get( 'job_types' )
					->row();

				if( !empty( $check_exists  ) ){
					$job_type['last_modified_by'] = $this->ion_auth->_current_user->id;
					$this->db->where( 'job_type_id', $check_exists->job_type_id )
						->update( 'job_types', $job_type );

					$job_type['job_type_id'] = $check_exists->job_type_id;
				} else {
					$job_type['created_by'] = $this->ion_auth->_current_user->id;
					$this->db->insert( 'job_types', $job_type );
					$job_type['job_type_id'] = $this->db->insert_id();
				}
				
				## Get Required Checklists
				$params = [
					'calt_code'				=> !empty( $job_type['external_calt_code'] ) 		? $job_type['external_calt_code'] 		: false,
					'call_prodfamily_code'	=> !empty( $job_type['external_prodfamily_code'] ) 	? $job_type['external_prodfamily_code'] : false,
					'task_type'				=> !empty( $job_type['external_task_type'] ) 		? $job_type['external_task_type'] 		: false,
				];
				
				$required_checklists 		= $this->lookup_required_checklists_by_job_type( $account_id, $params );
				
				## Add Required Checklists
				if( $required_checklists ){
					$this->job_service->add_required_checklists( $account_id, $job_type['job_type_id'], ['required_checklists'=>$required_checklists ] );
				}
				
				$result[] = $job_type;
			}

		}
		return $result;
	}
	
	
	/** Create Checklist Responses **/
	public function create_checklist_responses( $account_id = false, $postdata = false ){
		
		$result = (object)[
			'data'	 => false,
			'success'=> false,
			'message'=> ''
		];
		
		if( !empty( $account_id ) && !empty( $postdata ) ){
			$params					= convert_to_array( $postdata );
			$url_endpoint 			= 'ChecklistResponse/CreateScchecklistResponse';
			$method_type			= 'POST';
			$checklist_id			= !empty( $params['checklist_id'] ) 		? $params['checklist_id'] 		: false;
			$checklist_responses	= !empty( $params['responses'] ) 			? $params['responses'] 			: false;
			$local_records_only		= !empty( $params['local_records_only'] ) 	? $params['local_records_only'] : false;
			
			unset( $params['responses'] );
			
			$error_log = $this->_prepare_error_log( $account_id, $url_endpoint, $params );
			
			if( !empty( $checklist_responses ) ){

				$processed 					= [];
				
				## Local Checklists
				if( !empty( $local_records_only ) ){

					foreach( $checklist_responses as $checklist_id => $responses_data ){
						$params['checklist_id'] 		= $checklist_id;
						$params['local_records_only'] 	= $local_records_only;
						$save_response 					= $this->_save_checklist_responses_by_checklist( $account_id, $params, $responses_data );
						$processed[]					= $save_response;
					}
					
				} else {
					
					foreach( $checklist_responses as $checklist_id => $responses_data ){
			
						$link_type						= array_unique( array_column( $responses_data, 'responseSet_LinkType' ) );
						$responseset_linktype			= !empty( $link_type[0] ) ? $link_type[0] : 1;
						
						$task_type						= array_unique( array_column( $responses_data, 'responseSet_TaskType' ) );
						$responseset_tasktype			= !empty( $task_type[0] ) ? $task_type[0] : null;

						$params['checklist_id'] 		= $checklist_id;
						$params['responseset_linktype'] = $responseset_linktype;
						$params['responseset_tasktype'] = $responseset_tasktype;
						
						#Save the Data locally
						$save_response 				= $this->_save_checklist_responses_by_checklist( $account_id, $params, $responses_data );
						$response_ResponseSet_ID	= !empty( $save_response['responseset_id'] ) ? $save_response['responseset_id'] : ( !empty( $save_response['response_responseset_id'] ) ? $save_response['response_responseset_id'] : false );

						foreach( $responses_data as $question_id => $response ){

							#if( !empty( trim( $response['response_Answer'] ) ) ){
							if( trim( $response['response_Answer'] ) != '' ){
								
								$response	= array_map( 'trim', $response );

								unset( $response['responseSet_LinkType'] );

								$response['response_ResponseSet_ID'] 	= !empty( $response_ResponseSet_ID ) ? (int) $response_ResponseSet_ID : ( !empty( $response['response_ResponseSet_ID'] ) 	? (int) trim( $response['response_ResponseSet_ID'] )  : false );
								$response['response_Question_ID'] 		= !empty( $response['response_Question_ID'] ) 		? (int) $response['response_Question_ID']   : $question_id;
								$response['response_Question_Order'] 	= !empty( $response['response_Question_Order'] ) 	? (int) $response['response_Question_Order']  : null;
								$response['response_Question_DataType'] = !empty( $response['response_Question_DataType'] )	? (int) $response['response_Question_DataType']   : null;
								$response['response_BlobMap_Num'] 		= !empty( $response['response_BlobMap_Num'] ) 		? (int) $response['response_BlobMap_Num'] : null;
								$response['response_Answer'] 			= strval( $response['response_Answer'] );
					
								$tesseract_checklist_response 			= $this->tesseract_common->api_dispatcher( $url_endpoint, json_encode( $response ), [ 'method'=>$method_type, 'auth_token'=>$this->tess_api_token, 'auth_type'=>'token' ] );
								
								if( !empty( $tesseract_checklist_response ) && !empty( $tesseract_checklist_response->success ) ){
									
									$modify_responses 					= $this->_save_checklist_responses_by_checklist( $account_id, $params, [ $question_id => $tesseract_checklist_response->checklistResponse ] );
									$processed[] = $tesseract_checklist_response->checklistResponse;
									
								} else {
									## Collect Errors
									$error_log['api_error_details'][] = $tesseract_checklist_response;
								}
							}
						}
						
						#Set Checklist as completed.
						$set_as_completed = $this->_set_checklist_as_completed( $account_id, $params );
					}
					
				}
			}
			
			if( !empty( $processed ) ){
				$result->data 	 = $processed;
				$result->success = true;
				$result->message = 'Tesseract Checklist Response created successfully';
				$this->session->set_flashdata( 'message','Tesseract Checklist Response created successfully' );
			} else {
				
				## Log Error
				$error_log['api_call_desc'] 	= __METHOD__;
				$error_log['api_error_details'] = !empty( $error_log['api_error_details'] ) ? json_encode( $error_log['api_error_details'] ) : null;
				$this->_log_tesseract_errors( $error_log );
				
				$result->data 	 = false;
				$result->success = false;
				$result->message = 'Tesseract create Checklist Response failed';
				$this->session->set_flashdata( 'message', 'Tesseract create Checklist Response failed!' );
			}

		}
		return $result;
	}	
	
	
	/** Save a Checklist Response locally **/
	public function _save_checklist_responses_by_checklist( $account_id = false, $params = false, $responses_data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $params ) && !empty( $responses_data ) ){

			$job_id 			= !empty( $params['job_id'] ) 				? trim( $params['job_id'] )  	  		: false;
			$call_number 		= !empty( $params['external_job_ref'] ) 	? trim( $params['external_job_ref'] ) 	: false;
			$checklist_id 		= !empty( $params['checklist_id'] ) 		? $params['checklist_id'] 				: false;
			$local_records_only = !empty( $params['local_records_only'] ) 	? $params['local_records_only'] 		: false;
			
			$resp_data		= [];

			if( !empty( $local_records_only ) ){
				
				if( !empty( $job_id ) && !empty( $checklist_id ) ){
					
					foreach( $responses_data as $question_id => $response ){
						$row 	= is_object( $response ) ? array_change_key_case( object_to_array( $response ), CASE_LOWER ) : array_change_key_case( $response, CASE_LOWER );
						$row	= array_map( 'trim', $row );
						$new_row 			 	 			= $this->ssid_common->_filter_data( 'tesseract_checklist_response', $row );
						$new_row['account_id']				= $account_id;
						$new_row['job_id']					= $job_id;
						$new_row['response_checklist_id']	= $checklist_id;
						$new_row['response_responseset_id']	= null;
						$new_row['created_by']	 			= $this->ion_auth->_current_user->id;
						$resp_data[$question_id] 		 	= $new_row;
					}
				}
				
			} else {
				
				if( !empty( $job_id ) && !empty( $checklist_id ) && !empty( $call_number ) ){
				
					$response_set 	= $this->get_checklist_response_set( $account_id, $params );
					$response_set 	= is_object( $response_set ) ? array_change_key_case( object_to_array( $response_set ), CASE_LOWER ) : array_change_key_case( $response_set, CASE_LOWER );
					
					foreach( $responses_data as $question_id => $response ){
						$row 	= is_object( $response ) ? array_change_key_case( object_to_array( $response ), CASE_LOWER ) : array_change_key_case( $response, CASE_LOWER );
						$row	= array_map( 'trim', $row );
						//if( !empty( $row['response_answer'] ) ){
							$new_row 			 	 			= $this->ssid_common->_filter_data( 'tesseract_checklist_response', $row );
							$new_row['account_id']				= $account_id;
							$new_row['job_id']					= $job_id;
							$new_row['response_checklist_id']	= $checklist_id;
							$new_row['response_responseset_id']	= !empty( $response_set['responseset_id'] ) ? $response_set['responseset_id'] : ( !empty( $response_set['response_responseset_id'] ) ? $response_set['response_responseset_id'] : null );
							$new_row['created_by']	 			= $this->ion_auth->_current_user->id;
							$resp_data[$question_id] 		 	= $new_row;
						//}
					}
				}
			}

			## Insert responses
			if( !empty( $resp_data ) ){
				
				#$conditions = [ 'response_checklist_id' => $checklist_id ];
				$conditions = [ 'job_id' => $job_id ];
				#$this->db->where_in( 'response_question_id', array_column( $resp_data, 'response_question_id' ) )
				$this->db->where_in( 'response_checklist_id', array_column( $resp_data, 'response_checklist_id' ) )
					->where( $conditions )->delete( 'tesseract_checklist_response' );

				$this->ssid_common->_reset_auto_increment( 'tesseract_checklist_response', 'evi_response_id' );

				$this->db->insert_batch( 'tesseract_checklist_response', $resp_data );
				
			}
			
			$result = ( ( $this->db->trans_status() !== false ) && !empty( $response_set ) ) ? $response_set : ( !empty( $resp_data ) ? $resp_data : false );

		}
		return $result;
	}
	

	/** Update Existing Checklist Responses **/
	public function update_checklist_responses( $account_id = false, $postdata = false ){
		
		$result = (object)[
			'data'	 => false,
			'success'=> false,
			'message'=> ''
		];

		if( !empty( $account_id ) && !empty( $postdata ) ){
			$url_endpoint 	= 'ChecklistResponseSet/UpdateChecklistResponseSet';
			$method_type	= 'POST';
			$data			= [];
			foreach( $postdata as $col => $value ){
				$data[$col] = trim( $value );
			}
			
			
			#Update the Data locally
			#$modify_response = $this->_modify_checklist_response( $account_id, $data );

			unset( $postdata['account_id'], $data['account_id'] );
			
			if( !empty( $data['responseSet_ID'] ) ){
				$data['responseSet_ID'] 			= (int) $data['responseSet_ID'];
				$data['responseSet_Checklist_ID'] 	= (int) $data['responseSet_Checklist_ID'];
				$data['responseSet_Call_Num'] 		= (int) $data['responseSet_Call_Num'];
				$data['responseSet_FSR_Num'] 		= !empty( $data['responseSet_FSR_Num'] ) ? (int) $data['responseSet_FSR_Num']  : null;
				$data['responseSet_LinkType'] 		= !empty( $data['responseSet_LinkType'] ) ? (int) $data['responseSet_LinkType'] : null;
				$data['responseSet_Task_Num'] 		= !empty( $data['responseSet_Task_Num'] ) ? (int) $data['responseSet_Task_Num'] : null;
				$tesseract_checklist_response  = $this->tesseract_common->api_dispatcher( $url_endpoint, json_encode( $data ), [ 'method'=>$method_type, 'auth_token'=>$this->tess_api_token, 'auth_type'=>'token' ] );

				if( !empty( $tesseract_checklist_response ) && !empty( $tesseract_checklist_response->success ) ){
					
					#Update the Data locally
					$this->_modify_checklist_response( $account_id, object_to_array( $tesseract_checklist_response->checklistResponseSet ) );
					
					$result->data 	 = $tesseract_checklist_response->checklistResponseSet;
					$result->success = true;
					$result->message = 'Tesseract Checklist Response updated successfully';
					$this->session->set_flashdata( 'message','Tesseract Checklist Response updated successfully' );
				} else {
					$result->data 	 = $tesseract_checklist_response;
					$result->success = false;
					$result->message = 'Tesseract update Checklist Response failed';
					$this->session->set_flashdata( 'message', 'Tesseract update Checklist Response failed' );
				}

			} else {
				$result->data 	 = false;
				$result->success = false;
				$result->message = 'Tesseract Request missing required parameters';
				$this->session->set_flashdata( 'message', 'Tesseract Request missing required parameters' );
				return $result;
			}

		}
		return $result;
	}
	
	
	/** Update a Checklist Responses saved locally **/
	public function _modify_checklist_responses( $account_id = false, $data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $data ) ){
			
			$responses_data 					= is_object( $data ) ? array_change_key_case( object_to_array( $data ), CASE_LOWER ) : array_change_key_case( $data, CASE_LOWER );
			
			if(  !empty( $data['response_id'] ) ){
				#$this->db->where( 'cresp.response_id', $responses_data['response_id'] );
			}
			
			$check_exists = $this->db->select( 'cresp.evi_response_id, cresp.response_id' )
				->where( 'cresp.account_id', $account_id )
				->where( 'cresp.response_checklist_id', $responses_data['response_checklist_id'] )
				->where( 'cresp.response_question_id', $responses_data['response_question_id'] )
				->where( 'cresp.response_response_id', $responses_data['response_response_id'] )
				->get( 'tesseract_checklist_response `cresp` ' )
				->row();

			if( !empty( $check_exists ) ){
				$responses_data['updated_by']		=  $this->ion_auth->_current_user->id;
				$responses_data['evi_response_id']=  $check_exists->evi_response_id;
				$this->db->where( 'tesseract_checklist_response.response_id', $check_exists->response_id )
					->update( 'tesseract_checklist_response', $responses_data );
					
				$responses_data['response_id']=  $check_exists->response_id;
				$result = ( $this->db->trans_status() !== FALSE ) ? $responses_data : false;
			}
			
		}
		return $result;
	}
	
	
	/** Get the Saved Response Set Data and none Exists, Create one **/
	public function get_checklist_response_set( $account_id = false, $params = false ){
		
		$result = false;
		
		if( !empty( $account_id ) && !empty( $params ) ){

			$job_id 				= !empty( $params['job_id'] ) 					? $params['job_id']  : false;
			$external_user 			= !empty( $params['external_username'] ) 		? $params['external_username']  : false;
			$responseset_linktype 	= !empty( $params['responseset_linktype'] ) 	? $params['responseset_linktype']  : 1;
			$responseset_tasktype 	= !empty( $params['responseset_tasktype'] ) 	? $params['responseset_tasktype']  : null;
			$call_number 			= !empty( $params['responseSet_Call_Num'] ) 	? $params['responseSet_Call_Num']  : ( !empty( $params['responseset_call_num'] ) 		? $params['responseset_call_num'] : ( !empty( $params['external_job_ref'] ) ? $params['external_job_ref'] : false  ) );

			$checklist_id 	= !empty( $params['responseSet_Checklist_ID'] ) ? $params['responseSet_Checklist_ID']  	: ( !empty( $params['responseset_checklist_id'] ) 	? $params['responseset_checklist_id'] : ( !empty( $params['checklist_id'] ) ? $params['checklist_id'] : false  ) );

			$query = $this->db->select( 'tesseract_checklist_response_set.*', false )
				->where( 'tesseract_checklist_response_set.account_id', $account_id )
				->where( 'tesseract_checklist_response_set.job_id', $job_id )
				->where( 'tesseract_checklist_response_set.responseset_checklist_id', $checklist_id )
				->where( 'tesseract_checklist_response_set.responseset_call_num', $call_number )
				->where( 'tesseract_checklist_response_set.responseset_linktype', $responseset_linktype )
				->where( 'tesseract_checklist_response_set.responseset_tasktype', $responseset_tasktype )
				->order_by( 'tesseract_checklist_response_set.responseset_id' )
				->group_by( 'tesseract_checklist_response_set.responseset_id' )
				->get( 'tesseract_checklist_response_set' );

			if( $query->num_rows() > 0 ){
				$result = $query->result()[0];
			} else {
				
				$checklist_ref = $this->db->select( 'checklist_id, responseset_link_type, task_type, visibility_to_customer' )
					->where( 'checklist_id', $checklist_id )
					->where( 'responseset_link_type', $responseset_linktype )
					->where( 'task_type', $responseset_tasktype )
					->where( 'is_active', 1 )
					->group_by( 'tesseract_job_type_checklist_ref.checklist_id' )
					->limit( 1 )
					->get( 'tesseract_job_type_checklist_ref' )
					->row();


				$fsr_ref = $this->db->select( 'tesseract_fsr.job_id, tesseract_fsr.fsr_num' )
					->where( 'tesseract_fsr.job_id', $job_id )
					->group_by( 'tesseract_fsr.fsr_num' )
					->limit( 1 )
					->get( 'tesseract_fsr' )
					->row();

				$resp_link_type = !empty( $responseset_linktype ) ? $responseset_linktype : ( !empty( $checklist_ref->responseset_link_type ) ? $checklist_ref->responseset_link_type : 1 );
				$resp_task_type = !empty( $responseset_tasktype ) ? $responseset_tasktype : ( !empty( $checklist_ref->task_type ) ? $checklist_ref->task_type : null );

				$fsr_number		= null;
				$task_number	= null;
				$task_data		= null;
				#$resp_link_type = 1;

				switch( trim( $resp_link_type ) ){
					
					## FSR
					case 2:
					
						$call_fsr_records = $this->tesseract_service->get_fsr_by_call_number( $account_id, $call_number );

						if( !empty( $call_fsr_records ) ){
							
							$fsr_nums = array_column( $call_fsr_records, 'fsR_Num' );
							$highest_number = !empty( $fsr_nums ) ? max( $fsr_nums ) : 1;
							$fsr_number = $highest_number;
							
						} else {
							$fsr_number = 1;
						}
					
						break;
						
					## TASK
					case 3:
						$call_tasks = $this->tesseract_service->get_tasks( $account_id, false, [ 'task_call_num'=> $call_number, 'check_for_new'=> 1 ] );
						if( !empty( $call_tasks ) ){
							foreach( $call_tasks as $k => $task ){
								$task = (object) $task;
								if( !empty( $resp_task_type ) && !empty( $task->task_type ) && ( strtolower( $resp_task_type ) == strtolower( $task->task_type ) ) ){
									$task_number = $task->task_num;
									$task_data   = $task;
								}
							}
						} else {
							$task_number = null;
						}
						break;
						
					case 1:
					default:
						$fsr_number		= null;
						$task_number	= null;
						break;
				}

				## Create a New Responseset
				#Generate Response Set ID
				$resp_set_data = [
					'account_id' 					=> $account_id,
					'job_id' 						=> $job_id,
					'responseSet_Checklist_ID' 		=> $checklist_id,
					#'responseSet_Checklist_Desc' 	=> 'A sample Response set',
					#'responseSet_Checklist_HashCode'=> '####sdwrd2331',
					'responseSet_User_ID' 			=> $external_user,
					#'responseSet_LinkType' 		=> !empty( $checklist_ref->responseset_link_type ) ? $checklist_ref->responseset_link_type : $responseset_linktype,
					'responseSet_LinkType' 			=> $resp_link_type,
					'responseSet_TaskType' 			=> $resp_task_type,
					'responseSet_Call_Num' 			=> $call_number,
					'responseSet_FSR_Num' 			=> $fsr_number,
					'responseSet_Task_Num' 			=> $task_number,
					'responseSet_TaskData' 			=> $task_data
				];

				$response_set 	= $this->create_checklist_response_set( $account_id, $resp_set_data );
				$response_set	= ( !empty( $response_set->data ) ? $response_set->data : ( !empty( $response_set['data'] ) ? $response_set['data'] : $response_set ) );
				$result			= is_object( $response_set ) ? array_change_key_case( object_to_array( $response_set ), CASE_LOWER ) : array_change_key_case( $response_set, CASE_LOWER );
			}
		}
		return $result;
	}
	

	/** Create NEW Field Service Report **/
	public function create_fsr( $account_id = false, $postdata = false ){
		
		$result = (object)[
			'data'	 => false,
			'success'=> false,
			'message'=> ''
		];

		if( !empty( $account_id ) && !empty( $postdata ) ){
			$url_endpoint 	= 'FSR/CreateFSR';
			$method_type	= 'POST';
			$data			= [];
			foreach( $postdata as $col => $value ){
				$data[$col] = trim( $value );
			}
			
			#Save the Data locally
			$save_fsr = $this->_save_fsr( $account_id, $data );

			$error_log = $this->_prepare_error_log( $account_id, $url_endpoint, $data );

			unset( $data['account_id'], $data['job_id'] );
			
			if( !empty( $save_fsr['fsr_call_num'] ) ){
				$data['fsR_Num'] 			= !empty( $data['fsR_Num'] ) 		? intval( $data['fsR_Num'] ) 		: null;
				$data['fsR_Call_Num'] 		= !empty( $data['fsR_Call_Num'] ) 	? intval( $data['fsR_Call_Num'] ) 	: null;
				$data['fsR_Rep_Code'] 		= !empty( $data['fsR_Rep_Code'] ) 	? intval( $data['fsR_Rep_Code'] ) 	: intval( 51 );
				$data['fsR_BlobMap_Num'] 	= !empty( $data['fsR_BlobMap_Num'] )? intval( $data['fsR_BlobMap_Num'] ): null;
				#$data['fsR_LinkType'] 		= !empty( $data['fsR_LinkType'] ) ? (int) $data['fsR_LinkType'] : null;
				#$data['fsR_Task_Num'] 		= !empty( $data['fsR_Task_Num'] ) ? (int) $data['fsR_Task_Num'] : null;
				$data['fsR_Start_Date'] 	= !empty( $data['fsR_Start_Date'] ) 	? datetime_to_iso8601( $data['fsR_Start_Date'] ) : null;
				$data['fsR_Complete_Date'] 	= !empty( $data['fsR_Complete_Date'] ) 	? datetime_to_iso8601( $data['fsR_Complete_Date'] ) : null;
				$data['fsR_Added_Via'] 		= !empty( $data['fsR_Added_Via'] ) 	? intval( $data['fsR_Added_Via'] ) : null;
				$data['fsR_Travel_Time'] 	= !empty( $data['fsR_Travel_Time'] ) 	? floatval( $data['fsR_Travel_Time'] ) : null;
				$data['fsR_Work_Time'] 		= !empty( $data['fsR_Work_Time'] ) 	? floatval( $data['fsR_Work_Time'] ) : null;
				$tesseract_fsr = $this->tesseract_common->api_dispatcher( $url_endpoint, json_encode( $data ), [ 'method'=>$method_type, 'auth_token'=>$this->tess_api_token, 'auth_type'=>'token' ] );

			} else {

				//Call update API?
				$tesseract_fsr = false;
			}
			
			if( !empty( $tesseract_fsr ) && !empty( $tesseract_fsr->success ) ){
				
				$new_fsr_record = $tesseract_fsr->fsr;
				
				## Modify local records
				$this->_modify_fsr( $account_id, object_to_array( $new_fsr_record ) );
				
				$result->data 	 = $new_fsr_record;
				$result->success = true;
				$result->message = 'Tesseract Field Service Report created successfully';
				$this->session->set_flashdata( 'message','Tesseract Field Service Report created successfully' );
			} else {
				
				## Log Error
				$error_log['api_call_desc'] 	= __METHOD__;
				$error_log['api_error_details'] = json_encode( $tesseract_fsr );

				$this->_log_tesseract_errors( $error_log );
				
				$result->data 	 = !empty( $tesseract_fsr->fsr ) ? $tesseract_fsr->fsr : false;
				$result->success = false;
				$result->message = 'Tesseract create Field Service Report failed';
				$this->session->set_flashdata( 'message', 'Tesseract create Field Service Report failed' );
			}
		}
		return $result;
	}	
	
	/** Save a Field Service Report locally **/
	public function _save_fsr( $account_id = false, $data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $data ) ){
			
			$fsr_data = is_object( $data ) ? array_change_key_case( object_to_array( $data ), CASE_LOWER ) : array_change_key_case( $data, CASE_LOWER );

			if(  !empty( $fsr_data['fsr_num'] ) ){
				$this->db->where( 'fsr.fsr_num', $fsr_data['fsr_num'] );
			}
			
			$check_exists = $this->db->select( 'job.job_id, fsr.evi_fsr_id, fsr.fsr_num, fsr.fsr_call_num' )
				->join( 'job', 'job.external_job_ref = fsr.fsr_call_num', 'left' )
				->where( 'fsr.account_id', $account_id )
				->where( 'fsr.fsr_call_num', $fsr_data['fsr_call_num'] )
				->get( 'tesseract_fsr `fsr` ' )
				->row();

			$fsr_data 				= $this->ssid_common->_filter_data( 'tesseract_fsr', $fsr_data );
			$fsr_data['account_id'] = $account_id;

			if( !empty( $check_exists ) ){
				
				if( empty( $fsr_data['job_id'] ) ){
					$fsr_data['job_id']	=  !empty( $check_exists->job_id ) ? $check_exists->job_id : null;
				}
				
				$fsr_data['updated_by']	=  $this->ion_auth->_current_user->id;

				$this->db->where( 'tesseract_fsr.fsr_call_num', $check_exists->fsr_call_num )
					->where( 'tesseract_fsr.fsr_num', $check_exists->fsr_num )
					->update( 'tesseract_fsr', $fsr_data );
					
				$fsr_data['fsr_num']=  $check_exists->fsr_num;
				$result = ( $this->db->trans_status() !== FALSE ) ? $fsr_data : false;
			} else {
				
				if( empty( $fsr_data['job_id'] ) ){
					$job = $this->db->select( 'evident_job_id, call_num' )
						->where( 'tesseract_jobs.call_num', $fsr_data['fsr_call_num'] )
						->get_where( 'tesseract_jobs', [ 'tesseract_jobs.account_id'=>$account_id ] )
						->row();
					$fsr_data['job_id']	= !empty( $job->evident_job_id ) ? $job->evident_job_id : null;
				}				
				
				$fsr_data['created_by']	=  $this->ion_auth->_current_user->id;
				$this->db->insert( 'tesseract_fsr', $fsr_data );
				$fsr_data['evi_fsr_id']=  $this->db->insert_id();
				$result = ( $this->db->trans_status() !== FALSE ) ? $fsr_data : false;
			}
			
		}
		return $result;
	}
	

	/** Update an Existing Field Service Report **/
	public function update_fsr( $account_id = false, $postdata = false ){
		
		$result = (object)[
			'data'	 => false,
			'success'=> false,
			'message'=> ''
		];

		if( !empty( $account_id ) && !empty( $postdata ) ){
			$url_endpoint 	= 'FSR/UpdateFSR';
			$method_type	= 'POST';
			$data			= [];
			foreach( $postdata as $col => $value ){
				$data[$col] = trim( $value );
			}
			
			unset( $postdata['account_id'], $data['account_id'] );
			
			$call_number = !empty( $data['fsR_Call_Num'] ) ? $data['fsR_Call_Num'] : ( !empty( $data['external_job_ref'] ) ? $data['external_job_ref'] : null );
			
			$call_exists = $this->db->select( 'tesseract_jobs.*, job.dispatch_time, job.on_site_time, job.start_time, job.finish_time, job.completed_works, symptom_code,fault_code, repair_code,  job.engineer_signature,  job.customer_signature' )
				->where( 'call_num', $call_number )
				->join( 'job', 'job.external_job_ref = tesseract_jobs.call_num', 'left' )
				->get( 'tesseract_jobs' )
				->row();

			if( !empty( $data['fsR_Num'] ) && !empty( $call_exists ) ){
				
				$completion_date = !empty( $call_exists->finish_time ) ? datetime_to_iso8601( $call_exists->finish_time ) : datetime_to_iso8601( date( 'Y-m-d H:i:s' ) );
				
				if( empty( $data['fsR_Signature_Data'] ) ){
					if( !empty( $call_exists->engineer_signature ) ){
						$signature_img	 = file_get_contents(  $call_exists->engineer_signature );
						$signature_blob  = base64_encode( $signature_img );
					}					
				}
				
				$fsr_data 		= '';
				$fsr_data		.= !empty( $call_exists->completed_works ) ? $call_exists->completed_works : 'Notes from works completed...';
				#$fsr_data		.= ' | FSR PDF: '.$evidoc_pdf;
				
				$data['fsR_Num'] 			= !empty( $data['fsR_Num'] ) 		? intval( $data['fsR_Num'] ) 		: null;
				$data['fsR_Call_Num'] 		= !empty( $data['fsR_Call_Num'] ) 	? intval( $data['fsR_Call_Num'] )  	: null;
				$data['fsR_Rep_Code'] 		= !empty( $data['fsR_Rep_Code'] ) 	? intval( $data['fsR_Rep_Code'] ) 	: intval( 51 );
				#$data['fsR_LinkType'] 		= !empty( $data['fsR_LinkType'] ) ? (int) $data['fsR_LinkType'] : null;
				#$data['fsR_Task_Num'] 		= !empty( $data['fsR_Task_Num'] ) ? (int) $data['fsR_Task_Num'] : null;
				$data['fsR_Start_Date'] 	= !empty( $data['fsR_Start_Date'] ) 	? datetime_to_iso8601( $data['fsR_Start_Date'] ) : null;
				$data['fsR_Complete_Date'] 	= !empty( $data['fsR_Complete_Date'] ) 	? datetime_to_iso8601( $data['fsR_Complete_Date'] ) : null;
				$data['fsR_Employ_Num'] 	= !empty( $call_exists->call_employ_num ) 	? $call_exists->call_employ_num : null;
				$data['fsR_Prod_Num'] 		= !empty( $call_exists->call_prod_num ) 	? $call_exists->call_prod_num : null;
				$data['fsR_Fault_Code'] 	= !empty( $data['fsR_Fault_Code'] ) 		? $data['fsR_Fault_Code']  		: 'MIS';
				$data['fsR_Start_Date'] 	= !empty( $call_exists->start_time ) 		? datetime_to_iso8601( $call_exists->start_time ) : datetime_to_iso8601( date( 'Y-m-d H:i:s', strtotime( '- 1 hour' ) ) );
				$data['fsR_Call_Status'] 	= !empty( $data['fsR_Call_Status'] ) 		? $data['fsR_Call_Status'] : 'COMP';
				$data['fsR_Solution'] 		= !empty( $fsr_data ) 						? $fsr_data 		: null;
				$data['fsR_Miles'] 			= intval( 1 );
				$data['fsR_Signature_Data'] = !empty( $signature_blob ) 				? $signature_blob 	: null;
				
				$data['fsR_Added_Via'] 		= !empty( $data['fsR_Added_Via'] ) 	? intval( $data['fsR_Added_Via'] ) : null;
				$data['fsR_Travel_Time'] 	= !empty( $data['fsR_Travel_Time'] ) 	? floatval( $data['fsR_Travel_Time'] ) : null;
				$data['fsR_Work_Time'] 		= !empty( $data['fsR_Work_Time'] ) 		? floatval( $data['fsR_Work_Time'] )   : null;
				$data['Call_CallSubStatus_Code'] = !empty( $data['fsR_Call_Status'] ) 		? $data['fsR_Call_Status'] : 'COMP';

				$tesseract_fsr  			= $this->tesseract_common->api_dispatcher( $url_endpoint, json_encode( $data ), [ 'method'=>$method_type, 'auth_token'=>$this->tess_api_token, 'auth_type'=>'token' ] );
				
				if( !empty( $tesseract_fsr ) && !empty( $tesseract_fsr->success ) ){
					
					#Update the Data locally
					$this->_modify_fsr( $account_id, object_to_array( $tesseract_fsr->fsrUpdate ) );
					
					$result->data 	 = $tesseract_fsr->fsrUpdate;
					$result->success = true;
					$result->message = 'Tesseract Field Service Report updated successfully';
					$this->session->set_flashdata( 'message','Tesseract Field Service Report updated successfully' );
				} else {
					$result->data 	 = $tesseract_fsr;
					$result->success = false;
					$result->message = 'Tesseract update Field Service Report failed';
					$this->session->set_flashdata( 'message', 'Tesseract update Field Service Report failed' );
				}

			} else {
				$result->data 	 = false;
				$result->success = false;
				$result->message = 'Tesseract Request missing required parameters';
				$this->session->set_flashdata( 'message', 'Tesseract Request missing required parameters' );
				return $result;
			}

		}
		return $result;
	}
	
	
	/** Update a Field Service Report saved locally **/
	public function _modify_fsr( $account_id = false, $data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $data ) ){
			
			$fsr_data 					= is_object( $data ) ? array_change_key_case( object_to_array( $data ), CASE_LOWER ) : array_change_key_case( $data, CASE_LOWER );
			
			if(  !empty( $fsr_data['fsr_num'] ) ){
				$this->db->where( 'fsr.fsr_num', $fsr_data['fsr_num'] );
			}
			
			$check_exists = $this->db->select( 'job.job_id, fsr.evi_fsr_id, fsr.fsr_num, fsr.fsr_call_num' )
				->join( 'job', 'job.external_job_ref = fsr.fsr_call_num', 'left' )
				->where( 'fsr.account_id', $account_id )
				->where( 'fsr.fsr_call_num', $fsr_data['fsr_call_num'] )
				->get( 'tesseract_fsr `fsr` ' )
				->row();

			$fsr_data = $this->ssid_common->_filter_data( 'tesseract_fsr', $fsr_data );

			if( !empty( $check_exists ) ){
				if( empty( $fsr_data['job_id'] ) ){
					$fsr_data['job_id']	=  !empty( $check_exists->job_id ) ? $check_exists->job_id : null;
				}
				
				$fsr_data['updated_by']	=  $this->ion_auth->_current_user->id;

				$this->db->where( 'tesseract_fsr.fsr_call_num', $check_exists->fsr_call_num )
					->where( 'tesseract_fsr.fsr_num', $check_exists->fsr_num )
					->update( 'tesseract_fsr', $fsr_data );
					
				$fsr_data['fsr_num']=  $check_exists->fsr_num;
				$result = ( $this->db->trans_status() !== FALSE ) ? $fsr_data : false;
				
			}
			
		}
		return $result;
	}
	
	
	/** Get FSR BY Call Number **/
	public function get_fsr_by_call_number( $account_id = false, $call_number = false, $params = false ){

		$result = false;
		
		if( !empty( $account_id ) && !empty( $call_number ) ){

			$params			= convert_to_array( $params );
			$call_number 	= !empty( $call_number ) ? $call_number : ( !empty( $params['call_number'] ) ? $params['call_number'] : false );
			
			if( !empty( $call_number ) ){
				
				$url_endpoint 	= 'FSR/GetFSRByCallNum/'.$call_number;
				
				$method_type	= 'GET';
				$tesseract_fsr  = $this->tesseract_common->api_dispatcher( $url_endpoint, false, [ 'method'=>$method_type, 'auth_token'=>$this->tess_api_token, 'auth_type'=>'token' ] );
				if( !empty( $tesseract_fsr->fsr ) && !empty( $tesseract_fsr->success ) ){
					$this->session->set_flashdata( 'message','Tesseract FSR Record(s) data retrieved Successfully' );
					$result = !empty( $tesseract_fsr->fsr ) ? $tesseract_fsr->fsr : false;
					
					if( !empty( $result ) ){
						
						foreach( $tesseract_fsr->fsr as $key => $fsr ){
							$save = $this->_save_fsr( $account_id, $fsr );							
						}
						
					}
					
				} else {
					$this->session->set_flashdata( 'message','Unabled to Retrieve Tesseract FSR Record(s)' );
				}

			}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
			return false;
		}
		
		return $result;
	}

	/**
	* Prepare Error Log Data
	*/
	private function _prepare_error_log( $account_id = false, $url_endpoint = false, $data = false ){
		
		$data 	 	 = is_object( $data ) ? array_change_key_case( object_to_array( $data ), CASE_LOWER ) : array_change_key_case( $data, CASE_LOWER );
		$call_number = !empty( $data['call_num'] ) 		? $data['call_num'] : ( !empty( $data['fsr_call_num'] ) ? $data['fsr_call_num'] : ( !empty( $data['task_call_num'] ) ? $data['task_call_num'] : ( !empty( $data['external_job_ref'] ) ? $data['external_job_ref'] : null ) ) );
		$site_number = !empty( $data['call_site_num'] ) ? $data['call_site_num'] : ( !empty( $data['fsr_site_num'] ) ? $data['fsr_site_num'] : ( !empty( $data['task_site_num'] ) ? $data['task_site_num'] : null ) );

		return $error_log = [
			'account_id' 		=> $account_id,
			'evident_job_id' 	=> !empty( $data['job_id'] ) ? $data['job_id'] : null,
			'api_call_endpoint' => !empty( $url_endpoint ) ? $url_endpoint : null,
			'api_call_number' 	=> !empty( $call_number )  ? $call_number : null,
			'api_site_number' 	=> !empty( $site_number )  ? $site_number : null,
		];
	}
	
	/**
	* Log Tesseract Errors
	*/
	private function _log_tesseract_errors( $error_log_data = false ){
		
		if( !empty( $error_log_data ) ){
			$log_data 	= $this->ssid_common->_filter_data( 'tesseract_error_logs', $error_log_data );
			$this->db->insert( 'tesseract_error_logs', $error_log_data );
			return true;
		}
		return false;
	}


	/** Create Evident Site from SCCI Data **/
	public function _create_evident_site( $account_id = false, $site_data = false ){
		$result = false;

		if( !empty( $account_id ) && !empty( $site_data ) ){
			$site_data 	 	 = is_object( $site_data ) ? array_change_key_case( object_to_array( $site_data ), CASE_LOWER ) : array_change_key_case( $site_data, CASE_LOWER );
		
			$site_reference  = !empty( $site_data['site_num'] ) ? $site_data['site_num'] : false;
			if( !empty( $site_reference ) ){
				
				## Check if Site Exists
				$site_exists = $this->db->select( 'account_id, site.site_id, site_name, site_postcodes, site_notes, status_id, site_actual_address, site.site_reference, site.external_site_ref, audit_result_status_id, created_by', false )
					->where( 'site.account_id', $account_id )
					->where( 'site.archived !=',1 )
					->where( '( site.external_site_ref = "'.$site_reference.'" OR site.site_reference = "'.$site_reference.'" )' )
					->limit( 1 )
					->get( 'site' )
					->row();
				
				if( !empty( $site_exists ) ){
					$result = ( array ) $site_exists;
					$this->session->set_flashdata( 'message','Site Data retrieved successfully' );
				} else {
					
					$this->load->model( 'Address_model','address_service' );
					
					$site_post_code = $site_data['site_post_code'];

					$check_address		= $this->address_service->get_addresses( $site_post_code, 10 );
					$address_ids_list   = !empty( $check_address ) ? array_keys( $check_address ) : false;
					
					if( !empty(  $address_ids_list) ){
						shuffle( $address_ids_list );
						$address_id = $address_ids_list[0];
					} else {
						$address_id = 3;
					}

					$compliance_status = $this->db->select( 'audit_result_status_id' )
						->get_where( 'audit_result_statuses', ['account_id'=>$account_id, 'result_status_group'=>'not_set'] )
						->row();

					#Create one
					$new_site_data = [
						'account_id' 	    	=> $account_id,
						'site_name' 	    	=> $site_data['site_address'],
						'site_address_id' 	    => $address_id,
						'estate_name' 	    	=> $site_data['site_name'],
						'site_actual_address'	=> $site_data['site_address'],
						'site_actual_postcode'	=> $site_data['site_post_code'],
						'site_postcodes' 	    => $site_data['site_post_code'],
						'site_notes' 	    	=> $site_data['site_memo'],
						'status_id' 	    	=> 1,
						'site_reference' 		=> $site_reference,
						'external_site_ref' 	=> $site_reference,
						'audit_result_status_id'=> !empty( $compliance_status ) ? $compliance_status->audit_result_status_id : null,
						'created_by' 			=> $this->ion_auth->_current_user->id,
						'site_address_verified' => 0,
					];

					$this->db->insert( 'site',$new_site_data );
					if( $this->db->trans_status() !== FALSE ){
						$new_site_data['site_id'] = $this->db->insert_id();
						$result = $new_site_data;
						$this->session->set_flashdata( 'message','Site Data added and retrieved successfully' );
					
						$this->db->where( 'account_id', $account_id )
							->where( 'site_num', $site_reference )
							->update( 'tesseract_sites', [ 'evident_site_id' => $new_site_data['site_id'] ] );
					}
					
				}
				
			} else {
				$this->session->set_flashdata( 'message','Your request is missing required information' );
				return false;
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
			return false;
		}
		return $result;

	}

	
	/** Set Checklist as Complete **/
	public function _set_checklist_as_completed( $account_id = false, $data = false ){
		
		$result = false;
		if( !empty( $account_id ) && !empty( $data ) ){
			
			$checklist_counter_data = is_object( $data ) ? array_change_key_case( object_to_array( $data ), CASE_LOWER ) : array_change_key_case( $data, CASE_LOWER );

			$check_exists = $this->db->select( 'job.job_id, checklist_counter.checklist_id, checklist_counter.call_number, checklist_counter.track_id' )
				->join( 'job', 'job.job_id = checklist_counter.job_id', 'left' )
				->where( 'checklist_counter.account_id', $account_id )
				->where( 'checklist_counter.job_id', $checklist_counter_data['job_id'] )
				->where( 'checklist_counter.call_number', $checklist_counter_data['external_job_ref'] )
				->where( 'checklist_counter.checklist_id', $checklist_counter_data['checklist_id'] )
				->get( 'tesseract_checklist_counter `checklist_counter` ' )
				->row();

			$checklist_counter_data 			  = $this->ssid_common->_filter_data( 'tesseract_checklist_counter', $checklist_counter_data );
			$checklist_counter_data['account_id'] = $account_id;

			if( !empty( $check_exists ) ){

				$checklist_counter_data['updated_by']	=  $this->ion_auth->_current_user->id;

				$this->db->where( 'tesseract_checklist_counter.call_number', $check_exists->call_number )
					->where( 'tesseract_checklist_counter.account_id', $account_id )
					->where( 'tesseract_checklist_counter.job_id', $check_exists->job_id )
					->where( 'tesseract_checklist_counter.checklist_id', $check_exists->checklist_id )
					->update( 'tesseract_checklist_counter', $checklist_counter_data );

				$checklist_counter_data['track_id']=  $check_exists->track_id;
				$result = ( $this->db->trans_status() !== FALSE ) ? $checklist_counter_data : false;

			} else {
				
				$checklist_counter_data['call_number'] 	= $data['external_job_ref'];
				$checklist_counter_data['status'] 		= 'complete';
				$checklist_counter_data['created_by']	= $this->ion_auth->_current_user->id;
				$this->db->insert( 'tesseract_checklist_counter', $checklist_counter_data );
				$checklist_counter_data['track_id']=  $this->db->insert_id();
				$result = ( $this->db->trans_status() !== FALSE ) ? $checklist_counter_data : false;
				
			}
			
		}
		return $result;
	}
	
	

	public function get_checklist_counter( $account_id = false, $where = false, $view_type = false ){
		$result = false;

		if( !empty( $account_id ) ){

			if( !empty( $where ) ){
				$where = convert_to_array( $where );

				if( !empty( $where ) ){
					if( !empty( $where['job_id'] ) ){
						$job_id = $where['job_id'];
						$this->db->where_in( 'tesseract_checklist_counter.job_id', $where['job_id'] );
						unset( $where['job_id'] );
					}

					if( !empty( $where['checklist_id'] ) ){
						$checklist_id = $where['checklist_id'];
						$this->db->where_in( 'tesseract_checklist_counter.checklist_id', $where['checklist_id'] );
						unset( $where['checklist_id'] );
					}

					if( !empty( $where ) ){
						$this->db->where( $where );
					}
				}
			}

			$this->db->select( '
				tesseract_checklist_counter.checklist_id, tesseract_checklist.checklist_desc `checklist_name`,
				sum( CASE WHEN tesseract_checklist_counter.job_id > 0 AND tesseract_checklist_counter.status = "complete"  THEN 1 ELSE 0 END ) as `completed`,
				sum( CASE WHEN ( tesseract_checklist_counter.job_id > 0 AND tesseract_checklist_counter.status = "pending" ) THEN 1 ELSE 0 END ) as `pending`,
				sum( CASE WHEN ( tesseract_checklist_counter.job_id >0 AND tesseract_checklist_counter.status = "in progress" ) THEN 1 ELSE 0 END ) as `in_progress`,
				sum( CASE WHEN tesseract_checklist_counter.job_id > 0 AND tesseract_checklist_counter.status in ("complete", "pending", "in progress" ) THEN 1 ELSE 0 END ) as `total`
			', false );
			$this->db->where( 'tesseract_checklist_counter.is_active', 1 );
			$this->db->group_by( 'tesseract_checklist_counter.checklist_id' );
			$query = $this->db
				->join( 'tesseract_checklist', 'tesseract_checklist.checklist_id = tesseract_checklist_counter.checklist_id' )
				->get( 'tesseract_checklist_counter' );

			if( $query->num_rows() > 0 ){
				switch( $view_type ){
					case 'by_status' :
					default:
					$result = $query->result();
				}
				$this->session->set_flashdata( 'message','Checklist counts generated' );
			} else {
				$this->session->set_flashdata( 'message','No checklist' );
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}

		return $result;
	}
	
	
	/** Create Bulk Evident Job Types from Checklist Ref Table Data **/
	public function get_checklist_frequencies( $account_id = false ){
		$result = false;
		
		if( !empty( $account_id ) ){

			$query = $this->db->select( 'jt_ref.frequency,  jt_ref.frequency_count,  jt_ref.frequency_desc', false )
				->join( 'tesseract_job_type_checklist_ref jt_ref', 'job_types.external_calt_code = jt_ref.calt_code' )
				->where( 'jt_ref.is_active', 1 )
				->where( 'job_types.is_active', 1 )
				->where( 'job_types.archived !=', 1 )
				->where( 'job_types.account_id', $account_id )
				->order_by( 'jt_ref.frequency' )
				->group_by( 'jt_ref.frequency' )
				->get( 'job_types' );
				
				if( $query->num_rows() > 0 ){
					$result = $query->result();
					$this->session->set_flashdata( 'message','Checklist Frequencies found' );
				} else {
					$this->session->set_flashdata( 'message','No data found' );
				}
				
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}

		return $result;
	}
	
	/** Create Bulk Evident Job Types from Checklist Ref Table Data **/
	public function get_checklist_job_types( $account_id = false, $job_type_id = false, $where = false ){
		
		$result = false;
		
		if( !empty( $account_id ) ){
			
			$where	= convert_to_array( $where );
			
			if( !empty( $job_type_id ) ){
				$this->db->where( 'job_types.job_type_id', $job_type_id );
			}
			
			if( !empty( $where['frequency'] ) ){
				$this->db->where( 'jt_ref.frequency', $where['frequency'] );
			}
			
			$query = $this->db->select( 'job_types.*, jt_ref.frequency,  jt_ref.frequency_count,  jt_ref.frequency_desc', false )
				->join( 'tesseract_job_type_checklist_ref jt_ref', 'job_types.external_calt_code = jt_ref.calt_code', 'left' )
				->where( 'jt_ref.is_active', 1 )
				->where( 'job_types.is_active', 1 )
				->where( 'job_types.archived !=', 1 )
				->where( 'job_types.account_id', $account_id )
				->order_by( 'jt_ref.frequency, job_types.job_type' )
				->group_by( 'job_types.job_type' )
				->get( 'job_types' );
				
				if( $query->num_rows() > 0 ){
					$result = (object)[];
					foreach( $query->result() as $k => $row ){
						$result->{$row->frequency}[] = $row;
					}
					$this->session->set_flashdata( 'message','Checklist Job Types found' );
				} else {
					$this->session->set_flashdata( 'message','No data found' );
				}
				
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}

		return $result;
		
	}
	
	/** Refresh Evident Job with new Tesseract Details **/
	public function refresh_evident_jobs( $account_id = false, $call_numbers = false, $data = false ){
		
		$result = false;
		if( !empty( $account_id ) && !empty( $call_numbers ) ){
			
			if( !$data ){
				$data = (array) $this->get_job_by_call_number( $account_id, $call_numbers );
				$data = is_object( $data ) ? array_change_key_case( object_to_array( $data ), CASE_LOWER ) : array_change_key_case( $data, CASE_LOWER );
			}

			if( !empty( $data ) ){

				$refreshed_jobs	= [];
				$call_numbers 	= !is_array( $call_numbers ) ? [$call_numbers] : convert_to_array( $call_numbers );
				$data 			= convert_to_array( $data );
				
				foreach( $call_numbers as $k => $tess_call_number ){
				
					$job_exists = $this->db->where( 'job.account_id', $account_id )
						->where( 'job.external_job_ref', $tess_call_number )
						->get( 'job' )
						->row();
					
					if( !empty( $job_exists ) ){
						$tess_user_ref 	= !empty( $data['call_employ_num'] ) ? $data['call_employ_num'] : false;
						$user 			= $this->db->select( 'id, external_user_ref, first_name, last_name', false )
							->where( 'u.account_id', $account_id )
							->where( 'u.external_user_ref', $tess_user_ref )
							->get( 'user u' )
							->row();
						
						$status_id 	= $job_exists->status_id;
						$user_id 	= !empty( $user->id ) ? $user->id : null;
						if( !$user_id ){
							$status_id = 2;//Un-assigned
						}
						
						switch( strtoupper( $data['call_status'] ) ){
							case 'COMP':
								$status_id = 4;//Treat as successful
								break;
								
							case 'WAIT':
							case 'DOWN':
								$status_id = 9;//On-hold
								break;
						}
						
						$refresh_data = [
							'job_date' 					=> !empty( $data['call_rdate'] ) ? date( 'Y-m-d', strtotime( $data['call_rdate'] ) ) : $job_exists->job_date,
							'due_date' 					=> !empty( $data['call_ddate'] ) ? date( 'Y-m-d', strtotime( $data['call_ddate'] ) ) : ( !empty( $data['call_rdate'] ) ? date( 'Y-m-d', strtotime( $data['call_rdate'] ) ) : $job_exists->due_date ),
							'assigned_to' 				=> $user_id,
							'status_id' 				=> $status_id,
							'external_job_ref' 			=> $tess_call_number,
							'external_job_call_status' 	=> !empty( $data['call_status'] ) ? $data['call_status'] : $job_exists->external_job_call_status,
							'external_job_updated_on' 	=> _datetime(),
							'last_modified' 			=> _datetime(),
							'last_modified_by' 			=> $this->ion_auth->_current_user->id,
							'symptom_code' 				=> !empty( $data['symptom_code'] ) ? $data['symptom_code'] : $job_exists->symptom_code,
							'fault_code' 				=> !empty( $data['fault_code'] ) ? $data['fault_code'] : $job_exists->fault_code,
							'repair_code' 				=> !empty( $data['repair_code'] ) ? $data['repair_code'] : $job_exists->repair_code,
						];

						$this->db->where( 'job.account_id', $account_id )
							->where( 'job.external_job_ref', $tess_call_number )
							->update( 'job', $refresh_data );
							
						## 
						$refreshed_jobs[] = $this->db->where( 'job.account_id', $account_id )
							->where( 'job.external_job_ref', $tess_call_number )
							->get( 'job' )
							->row();
						
						$call_update_data = [
							'job_id'			=> $job_exists->job_id,
							'account_id'		=> $account_id,
							'call_Num'			=> $tess_call_number,
							'call_Status'		=> !empty( $data['call_status'] ) ? strtoupper( $data['call_status'] ) : strtoupper( $job_exists->external_job_call_status ),
							'call_CalT_Code'	=> !empty( $data['call_calt_code'] ) ? strtoupper( $data['call_calt_code'] ) : null,
							'call_Employ_Num'	=> $tess_user_ref,
							'call_Ref3'			=> $job_exists->job_id
						];						
						$update_tess_job = $this->tesseract_service->update_job( $account_id, $call_update_data );

					} else {
						$this->session->set_flashdata( 'message','Invalid Tesseract call number or this record has been deleted!' );
					}
				}
				
				if( !empty( $refreshed_jobs ) ){
					$result = ( count( $refreshed_jobs ) == 1 ) ? $refreshed_jobs[0] : $refreshed_jobs;
					$this->session->set_flashdata( 'message','Tesseract Call details refreshed Successfully' );
				}

			} else {
				$this->session->set_flashdata( 'message','Your request is missing required information' );
			}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}
		return $result;
		
	}
}