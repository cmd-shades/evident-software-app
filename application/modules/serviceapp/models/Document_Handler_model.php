<?php if (!defined('BASEPATH'))exit('No direct script access allowed');

class Document_Handler_model extends CI_Model {

	function __construct(){
		parent::__construct();
		$section 	   = explode("/", $_SERVER["SCRIPT_NAME"]);

		$this->app_root= $_SERVER["DOCUMENT_ROOT"]."/".( !empty( $section[1] ) ? $section[1] : 'techlive' )."/";
		$this->app_root= str_replace('/index.php','',$this->app_root);
		$this->load->library('upload');
	}

	private $numerical_fields = ['account_id','site_id','job_id','customer_id','assessment_id'];


	/**
	*	Process files
	**/
	public function upload_files( $account_id = false, $postdata = false, $document_group = false , $folder = false ){

		$result 	= false;

		if( !empty( $account_id ) && !empty( $_FILES['upload_files']['name'] ) ){
			$document_data = [];
			$doc_reference = 'techlive'.$account_id.'_';
			foreach( $postdata as $col=>$val ){
				$val				 = ( !is_array($val) ) ? trim($val) : $val;
				$document_data[$col] = ( in_array( $col, $this->numerical_fields ) ) ? (int)$val : $val;
			}

			if( !empty( $document_data ) ){

				switch( strtolower( $document_group ) ){
					case 'site':
						$identifier		= ( !empty( $document_data['site_id'] ) ) ? $document_data['site_id'] : 'ref-not-set';
						$folder			= 'site';
						$target_table 	= 'site_document_uploads';
						$doc_reference .= ( !empty( $document_data['site_id'] ) ) ? 'site'.$document_data['site_id'] 	: '';
						break;

					case 'content':
						$identifier		= ( !empty( $document_data['content_id'] ) ) ? $document_data['content_id'] : 'ref-not-set';
						$folder			= 'content';
						$target_table 	= 'content_document_uploads';
						$doc_reference .= ( !empty( $document_data['content_id'] ) ) ? 'cont'.$document_data['content_id'] 	: '';

						$content_details 	= $this->db->select( 'content_film.content_id, content_film.title, content_film.asset_code' )
							->get_where( 'content_film', [ 'content_film.content_id'=>$identifier ] )
							->row();
						$file_prefix	= !empty( $content_details->asset_code ) ? $content_details->asset_code : false;
						break;

					case 'provider':
						$identifier		= ( !empty( $document_data['provider_id'] ) ) ? $document_data['provider_id'] : 'ref-not-set';
						$folder			= 'provider';
						$target_table 	= 'provider_document_uploads';
						$doc_reference .= ( !empty($document_data['provider_id']) ) ? 'prov'.$document_data['provider_id'] 	: '';
						break;

					case 'integrator':
						$identifier		= ( !empty( $document_data['system_integrator_id'] ) ) ? $document_data['system_integrator_id'] : 'ref-not-set';
						$folder			= 'integrator';
						$target_table 	= 'integrator_document_uploads';
						$doc_reference .= ( !empty($document_data['system_integrator_id']) ) ? 'intg'.$document_data['system_integrator_id'] : '';
						break;

					case 'systems':
						$identifier		= ( !empty( $document_data['system_type_id'] ) ) ? $document_data['system_type_id'] : 'ref-not-set';
						$folder			= 'systems';
						$target_table 	= 'system_document_uploads';
						$doc_reference .= ( !empty($document_data['system_type_id']) ) ? 'syst'.$document_data['system_type_id'] : '';
						break;

					case 'channel':
						$identifier		= ( !empty( $document_data['channel_id'] ) ) ? $document_data['channel_id'] : 'ref-not-set';
						$folder			= 'channel';
						$target_table 	= 'channel_document_uploads';
						$doc_reference .= ( !empty($document_data['channel_id']) ) ? 'chan'.$document_data['channel_id'] : '';
						break;

					case 'report_viewing_stats':
						$identifier		= ( !empty( $document_data['provider_id'] ) ) ? $document_data['provider_id'] : 'ref-not-set';
						$folder			= 'report_viewing_stats';
						$target_table 	= 'report_viewing_stats_uploads';
						$doc_reference .= ( !empty($document_data['provider_id']) ) ? 'rvs'.$document_data['provider_id'] : '';
						break;

					default:
						$identifier		= 'ref-not-set';
						$target_table 	= 'document_uploads';
						$folder			= ( !empty( $folder ) ) ? $folder : 'others';
						break;
				}

				## Process the files
				if( !empty( $_FILES['upload_files']['name'] ) ){

					## Process the files
					foreach( $_FILES['upload_files']['name'] as $i => $imgname ){
						if( !empty( $_FILES['upload_files']['name'][$i] ) ) {
							#$reversed_file_parts = explode( '.', strrev( $_FILES['upload_files']['name'][$i] ), 1 );
							$reversed_file_parts = explode( '.', strrev( $_FILES['upload_files']['name'][$i] ), 2 );
							$file_extension 	 = strrev( $reversed_file_parts[0] );
							$reversed_file_name	 = ( !empty( $reversed_file_parts[1] ) ) ? strrev( $reversed_file_parts[1] ) : '';

							#$file_upload_type	 = ( in_array( $i, ['hero','thumbnail','standard'] ) ) ? 'images' : ( in_array( $i, ['subtitles', 'vtt'] ) ? 'subtitles' : 'meta-data' );
							if( !empty( $file_prefix ) ){

								switch( strtolower( $i ) ){
									case ( in_array( $i, [ 'hero','thumbnail','standard' ] ) ) :
										$file_upload_type = 'image';
										$file_uuid				 		= $file_prefix.'-'.$i.'.'.$file_extension;
										$prefixed_file_name				= ucwords($file_prefix).'-'.ucwords($i).'.'.$file_extension;

										break;

									case ( in_array( $i, [ 'subtitles', 'vtt' ] ) ) :
										$file_upload_type 	= 'subtitles';
										#$file_uuid			= $file_prefix.'-'.strtolower( $reversed_file_name ).'-'.$file_upload_type.'.'.$file_extension;
										#$prefixed_file_name	= ucwords( $file_prefix ).'-'.ucwords( $reversed_file_name ).'-'.ucwords( $file_upload_type ).'.'.$file_extension;

										$file_uuid			= $file_prefix.'_'.strtolower( $reversed_file_name ).'.'.$file_extension;
										$prefixed_file_name	= ucwords( $file_prefix ).'_'.ucwords( $reversed_file_name ).'.'.$file_extension;

										break;

									default:
										$file_upload_type 	= 'meta-data';
										$file_uuid			= ucwords( $i ).'-'.preg_replace( '/\s+/', '', $_FILES['upload_files']['name'][$i] );
										$prefixed_file_name	= false;
										break;
								}

							} else {
								#$file_uuid			= ucwords( $i ).'-'.preg_replace( '/\s+/', '', $_FILES['upload_files']['name'][$i] );
								#$prefixed_file_name	= ucwords( $i ).'-'.preg_replace( '/\s+/', '', $_FILES['upload_files']['name'][$i] );

								$file_uuid			= ucwords( $i ).'-'.preg_replace( '/[^\w-]/', '', $_FILES['upload_files']['name'][$i] );
								$prefixed_file_name	= false;
							}

							$document_data['doc_type'] 		= ( !empty( $document_data['doc_type']) ) ? ucwords( strtolower( $document_data['doc_type'] ) ) : $document_group;
							$document_data['doc_file_type'] = $i;
							$document_data['document_name'] = !empty( $prefixed_file_name ) ? $prefixed_file_name : ( ( !empty( $_FILES['upload_files']['name'][$i]) ) ? $_FILES['upload_files']['name'][$i] : null );




							// This is to remove extra dots in the file name
							//  - upload library will do this with the file when moved to the server
							if( ( $ext_pos = strrpos( $file_uuid, '.') ) === FALSE ){

							} else {
								$ext 		= substr( $file_uuid, $ext_pos );
								$file_uuid 	= substr( $file_uuid, 0, $ext_pos );
								$file_uuid =  str_replace( '.', '_', $file_uuid ).$ext;
							}


							if( strtolower( $document_group ) == "report_viewing_stats" ){
								$document_data['doc_reference'] = ( date( 'YmdHis' ) )."-".$file_uuid;
							} else {
								$document_data['doc_reference'] = $file_uuid;
							}

							$temp_document_id  = $this->_create_document_placeholder( $account_id, $document_data, $target_table );

							if( !empty( $temp_document_id ) ){
								$_FILES['doc_file']['name'] 	= !empty( $prefixed_file_name ) ? $prefixed_file_name : $_FILES['upload_files']['name'][$i];
								$_FILES['doc_file']['type'] 	= $_FILES['upload_files']['type'][$i];
								$_FILES['doc_file']['tmp_name'] = $_FILES['upload_files']['tmp_name'][$i];
								$_FILES['doc_file']['error'] 	= $_FILES['upload_files']['error'][$i];
								$_FILES['doc_file']['size'] 	= $_FILES['upload_files']['size'][$i];


								if( strtolower( $document_group ) == "report_viewing_stats" ){
									$document_path 			 		= '_account_assets/accounts/'.$account_id.'/'.$folder.'/'.$identifier.'/'.( date( 'YmdHis' ) ).'/';
								} else {
									$document_path 			 		= '_account_assets/accounts/'.$account_id.'/'.$folder.'/'.$identifier.'/';
								}

								$upload_path 			 		= $document_path; //Sub folder, resolve path at time of rendering

								if( !is_dir( $upload_path ) ){
									if( !mkdir( $upload_path, 0755, true ) ){
										$this->db->where( 'account_id', $account_id )
											->where( 'document_id', $temp_document_id )
											->delete( $target_table );
										$this->session->set_flashdata('message', 'Error: Unable to create upload location');
										return false;
									}
								}

								$file_name				 = !empty( $prefixed_file_name ) ? $prefixed_file_name : $_FILES['doc_file']['name'];
								$file_type				 = $_FILES['doc_file']['type'];
								$file_location			 = $upload_path.$file_uuid;

								$config['upload_path'] 	 = $upload_path;
								$config['allowed_types'] = 'pdf|csv|xls|xlsx|doc|docx|gif|jpg|JPG|jpeg|JPEG|png|ods|txt|vtt|vtt';
								$config['max_size']      = 24384; //Approx 24MB
								$config['file_name'] 	 = strtolower( $file_uuid );
								$config['overwrite']     = TRUE;
								$config['remove_spaces'] = TRUE;

								$this->upload->initialize( $config );

								if( $this->upload->do_upload( 'doc_file' ) ){
									$update_document_data = [
										'document_id'			=>$temp_document_id,
										//'doc_reference'=>$file_uuid,
										'document_name'			=>$file_name,
										'document_link'			=>base_url( $document_path.$file_uuid ),
										'document_location'		=>$file_location,
										'document_extension'	=>$file_type,
										'created_by'			=>!empty( $this->ion_auth->_current_user->id ) ? $this->ion_auth->_current_user->id : null
									];

									# Update temp file
									$upload_complete = $this->_update_document( $account_id, $temp_document_id, $update_document_data, $target_table );
									if( $upload_complete ){
										$uploaded_data['documents'][$i] = array_merge( $document_data, $update_document_data );
									}
								} else {
									## delete the temp file
									$this->db->where( 'account_id', $account_id )
										->where( 'document_id', $temp_document_id )
										->delete( $target_table );

									$uploaded_data['errors'][$i] = [
										'file'		=>$file_name,
										'error'		=>$this->upload->display_errors()
									];
								}
							} else {
								$this->session->set_flashdata('message', 'Error: Unable to created a file in the DB ' );
								return false;
							}
						}
					}
				}
			}

			if( !empty( $uploaded_data ) ){
				$errors = !empty($uploaded_data['errors']) ? ', with some Errors!' : '';
				$this->session->set_flashdata( 'message', 'Documents uploaded successfully'.$errors );
				$result = $uploaded_data;
			}
		} else {
			$this->session->set_flashdata('message', 'No files were selected');
		}
		return $result;
	}


	/** Create a Document placeholder in the respective table **/
	function _create_document_placeholder( $account_id = false, $doc_data = false, $target_table = false ){

		$result = false;
		if( !empty( $account_id ) && !empty( $doc_data ) && !empty( $target_table ) ){

			$doc_data = $this->ssid_common->_filter_data( $target_table, $doc_data );

			$where = ['account_id'=>$account_id, 'doc_type'=>$doc_data['doc_type'], 'doc_reference'=>$doc_data['doc_reference'] ];
			$query = $this->db->order_by('document_id desc')->limit(1)->get_where( $target_table, $where );

			if( $query->num_rows() > 0 ){
				$row = $query->result()[0];
				$this->db->where( $where );
				$this->db->where( 'document_id', $row->document_id );
				$this->db->update( $target_table, $doc_data );
				$result = ( $this->db->trans_status() !== false ) ? $row->document_id : false;
			} else {
				$this->db->insert( $target_table, $doc_data );
				$result = ( $this->db->trans_status() !== false ) ? $this->db->insert_id() : false;
			}
		}
		return $result;
	}

	/** Update document record **/
	private function _update_document( $account_id = false, $document_id = false, $doc_data = false, $target_table = 'document_uploads' ){
		$result = false;
		if( !empty($account_id) && !empty($document_id) && !empty($doc_data) ){
			$doc_data = $this->ssid_common->_filter_data( $target_table, $doc_data );
			$this->db->where( 'account_id ', $account_id )
				->where( 'document_id', $document_id )
				->update( $target_table, $doc_data );

			$result = ( $this->db->trans_status() !== false ) ? $document_id : false;
		}
		return $result;
	}

	/** Get a list of all uploaded documents **/
	public function get_document_list( $account_id = false, $doc_group = 'document_uploads', $postdata = false ){
		$result = false;

		if( !empty( $account_id ) && !empty( $doc_group ) ){

			$attached_to_question = ( !empty( $postdata['attached_to_question'] ) ) ? $postdata['attached_to_question'] : false;

			switch( $doc_group ){
				case 'site':
					$target_table 	= 'site_document_uploads';
					break;
				case 'content':
					$target_table 	= 'content_document_uploads';
					break;
				case 'provider':
					$target_table 	= 'provider_document_uploads';
					break;
				case 'integrator':
					$target_table 	= 'integrator_document_uploads';
					break;
				case 'systems':
					$target_table 	= 'system_document_uploads';
					break;
				case 'channel':
					$target_table 	= 'channel_document_uploads';
					break;
				case 'report_viewing_stats':
					$target_table 	= 'report_viewing_stats';
					break;
				default:
					$target_table = 'document_uploads';
					break;
			}


			$this->db->select( 'du.*, CONCAT( user.first_name," ",user.last_name ) `uploaded_by`', false );

			$this->db->join( 'user', 'user.id = du.created_by', 'left' );

			$postdata = $this->ssid_common->_filter_data( $target_table, $postdata );

			if( !empty( $postdata['site_id'] ) ){
				$this->db->where( 'du.site_id', $postdata['site_id'] );
			}

			if( !empty( $postdata['content_id'] ) ){
				$this->db->where( 'du.content_id', $postdata['content_id'] );
			}

			if( !empty( $postdata['provider_id'] ) ){
				$this->db->where( 'du.provider_id', $postdata['provider_id'] );
			}

			if( !empty( $postdata['system_integrator_id'] ) ){
				$this->db->where( 'du.system_integrator_id', $postdata['system_integrator_id'] );
			}

			if( !empty( $postdata['system_type_id'] ) ){
				$this->db->where( 'du.system_type_id', $postdata['system_type_id'] );
			}

			if( !empty( $postdata['channel_id'] ) ){
				$this->db->where( 'du.channel_id', $postdata['channel_id'] );
			}


			$arch_where = "( du.archived != 1 or du.archived is NULL )";
			$this->db->where( $arch_where );

			$this->db->order_by( 'doc_type, date_created desc' );
			$query = $this->db->get( $target_table.' du' );

			if( $query->num_rows() > 0 ){
				$data 	  = [];
				foreach( $query->result() as $doc ){
					if( $attached_to_question ){
						$question_id = ( !empty( $doc->question_id ) ) ? $doc->question_id : '000';
						$data[$doc->account_id][$question_id][] = $doc;
					}else{
						$doc_type = ( !empty( $doc->doc_type ) ) ? $doc->doc_type : 'Doc-type-not-set';
						$data[$doc->account_id][$doc_type][] = $doc;
					}

				}
				$this->session->set_flashdata('message', 'Documents found.' );
				$result = $data;
			}else{
				$this->session->set_flashdata('message', 'No documents found matching you criteria.' );
			}
		}else{
			$this->session->set_flashdata('message', 'No documents found.' );
		}
		return $result;
	}


	/*
	*	Delete file functionality accessible for any module
	*/
	public function delete_document( $account_id = false, $document_group = false, $document_id = false ){
		$result = false;

		if( !empty( $account_id ) && !empty( $document_group ) && !empty( $document_id ) ){
			
			## a trigger for Airtime/Easel stuff
			$ready_to_delete = true;

			switch( $document_group ){
				case 'site':
					$target_table 	= 'site_document_uploads';
					break;
				case 'content':
					$target_table 	= 'content_document_uploads';
					break;
				case 'provider':
					$target_table 	= 'provider_document_uploads';
					break;
				case 'integrator':
					$target_table 	= 'integrator_document_uploads';
					break;
				case 'systems':
					$target_table 	= 'system_document_uploads';
					break;
				case 'channel':
					$target_table 	= 'channel_document_uploads';
					break;
				case 'report_viewing_stats':
					$target_table 	= 'report_viewing_stats';
					break;
				default:
					$target_table = 'document_uploads';
					break;
			}

			$document_details = $this->db->get_where( $target_table, [ "document_id" => $document_id ] )->row();
			
			$message = false;
			
			## for the 'Content' group documents
			if( !empty( $document_group ) && ( strtolower( $document_group ) == "content" ) ){
				
				## check if document has an Easel/airtime reference
				if( !empty( $document_details->airtime_reference ) ){
					## let's load the Easel model
					$this->load->model( "serviceapp/Easel_Api_model", "easel_service" );
					
					## so, all the documents from this table (content_document_uploads) will have the same structure (fields). 
					## Before doing the delete request to Easel, we need to know what type of the document it is:
					## options are: ['json', 'NULL', 'standard', 'hero', 'subtitles']
					## JSON && NULL won't have the easel reference 
					
					if( in_array( strtolower( $document_details->doc_file_type ), ['subtitles', 'vtt'] ) ){
						$sub_deleted_on_easel = false;
						$sub_deleted_on_easel = $this->easel_service->delete_subtitle( $account_id, $document_details->airtime_reference );

						if( !empty( $sub_deleted_on_easel ) && isset( $sub_deleted_on_easel->success ) && ( $sub_deleted_on_easel->success != false ) ){
							## As we deleting document completely - there is no need for the document update
							
							$sub_upd_data = [];
							$sub_upd_data = [
								"airtime_status"				=> "subtitle_deleting_error",
								"airtime_status_update_date"	=> date( "Y-m-d H:i:s" )
							];
							
							$sub_upd_where = [];
							$sub_upd_where = [
								"account_id" 	=> $account_id,
								"document_id"	=> $document_id
							];
							
							$upd = $this->db->update( "content_document_uploads", $sub_upd_data, $sub_upd_where );

						} else {
							$ready_to_delete 	= false;
							$message 			= ( isset( $sub_deleted_on_easel->message ) && !empty( $sub_deleted_on_easel->message ) ) ? html_escape( $sub_deleted_on_easel->message ) : "There was an error deleting the subtitle on Easel";
							log_message( "error", json_encode( ["Error deleting the subtitle on Easel" => $sub_deleted_on_easel] ) );
						}
					}


					if( in_array( strtolower( $document_details->doc_file_type ), ['standard', 'hero'] ) ){

						## of the document is 'hero' type we have to check if we do have the 'standard' type attached - it will cancel the future operation as we shouldn't have standard image without the hero
						if( strtolower( $document_details->doc_file_type ) == "hero" ){

							## get standard image for this content ID
							$standard_image = false;
							$this->db->where( "account_id", $account_id );
							$this->db->where( "content_id", $document_details->content_id );
							$this->db->where( "doc_file_type", "standard" );
							$arch_doc_where = "( ( ( content_document_uploads.archived != 1 ) || ( content_document_uploads.archived IS NULL ) ) )";
							$this->db->where( $arch_doc_where );
							$standard_image_exists = false;
							$standard_image_exists = $this->db->get( "content_document_uploads" )->row();

							if( !empty( $standard_image_exists ) ){
								$ready_to_delete 	= false;
								$this->session->set_flashdata( 'message', "Standard must be deleted before deleting Hero" );
								return $result;
							}
						}

						## - get the (Easel) product details
						$this->db->where( "content_id", $document_details->content_id );
						$this->db->where( "account_id", $account_id );
						$arch_where = "( ( ( content_film.archived != 1 ) || ( content_film.archived IS NULL ) ) )";
						$this->db->where( $arch_where );
						$movie_details = $this->db->get( "content_film" )->row();

						if( !empty( $movie_details ) ){

							$airtime_product_data = false;
							$airtime_product_data = $this->easel_service->fetch_product( $account_id, $movie_details->external_content_ref );

							if( !empty( $airtime_product_data ) && !empty( $airtime_product_data->image ) ){

								## preparing the basic part of the object for the update
								$airtime_upd_dataset = [
									"reference"			=> ( !empty( $airtime_product_data->reference ) ) ? $airtime_product_data->reference : '' ,
									"type"				=> ( !empty( $airtime_product_data->type ) ) ? $airtime_product_data->type : '' ,
									"name"				=> ( !empty( $airtime_product_data->name ) ) ? $airtime_product_data->name : '' ,
									"state"				=> ( !empty( $airtime_product_data->state ) ) ? $airtime_product_data->state : '' ,
									"tagline"			=> ( !empty( $airtime_product_data->shortDescription ) ) ? $airtime_product_data->shortDescription : '' ,
									"plot"				=> ( !empty( $airtime_product_data->description ) ) ? $airtime_product_data->description : '' ,
									"running_time"		=> ( !empty( $movie_details->running_time ) ) ? $movie_details->running_time : '' ,
									// "country"			=> ( !empty( $airtime_product_data->country ) ) ? $airtime_product_data->country : '' ,
									"release_date"		=> ( !empty( $movie_details->release_date ) ) ? $movie_details->release_date : '' ,
									"parentalAdvisory"	=> ( !empty( $airtime_product_data->parentalAdvisory ) ) ? $airtime_product_data->parentalAdvisory : '' ,
									"categories"		=> ( !empty( $airtime_product_data->categories ) ) ? $airtime_product_data->categories : '' ,
									"ageRatings"		=> ( !empty( $airtime_product_data->ageRatings ) ) ? $airtime_product_data->ageRatings : '' ,
									"indexable"			=> ( !empty( $airtime_product_data->indexable ) ) ? $airtime_product_data->indexable : true ,
									"episodeNumber"		=> ( !empty( $airtime_product_data->episodeNumber ) ) ? $airtime_product_data->episodeNumber : false
								];

								if( isset( $airtime_product_data->published ) && !empty( $airtime_product_data->published ) ){
									$airtime_upd_dataset["published"] = $airtime_product_data->published;
								}

								if( isset( $airtime_product_data->trailer ) && !empty( $airtime_product_data->trailer ) ){
									$airtime_upd_dataset["trailer"] = $airtime_product_data->trailer;
								}

								if( isset( $airtime_product_data->feature ) && !empty( $airtime_product_data->feature ) ){
									$airtime_upd_dataset["feature"] = $airtime_product_data->feature;
								}


								## MASTER ##
								if( isset( $airtime_product_data->image->master->imageId ) && !empty( $airtime_product_data->image->master->imageId ) ){
									if( $document_details->airtime_reference == $airtime_product_data->image->master->imageId ){
										$airtime_upd_dataset['image']['master']['imageId'] = "";
									} else {
										$airtime_upd_dataset['image']['master']['imageId'] = $airtime_product_data->image->master->imageId;
									}
								} else {
									## if not set - I do not change anything
								}
								## MASTER ##


								## THUMB ##
								if( isset( $airtime_product_data->image->thumb->master->imageId ) && !empty( $airtime_product_data->image->thumb->master->imageId ) ){
									if( $document_details->airtime_reference == $airtime_product_data->image->thumb->master->imageId ){
										$airtime_upd_dataset['image']['thumb']['master']['imageId'] = "";
									} else {
										$airtime_upd_dataset['image']['thumb']['master']['imageId'] = $airtime_product_data->image->thumb->master->imageId;
									}
								} else {
									## if not set - I do not change anything
								}

								if( isset( $airtime_product_data->image->thumb->{'2:3'}->imageId ) && !empty( $airtime_product_data->image->thumb->{'2:3'}->imageId ) ){
									if( $document_details->airtime_reference == $airtime_product_data->image->thumb->{'2:3'}->imageId ){
										$airtime_upd_dataset['image']['thumb']['2:3']['imageId'] = "";
									} else {
										$airtime_upd_dataset['image']['thumb']['2:3']['imageId'] = $airtime_product_data->image->thumb->{'2:3'}->imageId;
									}
								} else {
									## if not set - I do not change anything
								}

								if( isset( $airtime_product_data->image->thumb->{'4:3'}->imageId ) && !empty( $airtime_product_data->image->thumb->{'4:3'}->imageId ) ){
									if( $document_details->airtime_reference == $airtime_product_data->image->thumb->{'4:3'}->imageId ){
										$airtime_upd_dataset['image']['thumb']['4:3']['imageId'] = "";
									} else {
										$airtime_upd_dataset['image']['thumb']['4:3']['imageId'] = $airtime_product_data->image->thumb->{'4:3'}->imageId;
									}
								} else {
									## if not set - I do not change anything
								}

								if( isset( $airtime_product_data->image->thumb->{'16:9'}->imageId ) && !empty( $airtime_product_data->image->thumb->{'16:9'}->imageId ) ){
									if( $document_details->airtime_reference == $airtime_product_data->image->thumb->{'18:9'}->imageId ){
										$airtime_upd_dataset['image']['thumb']['16:9']['imageId'] = "";
									} else {
										$airtime_upd_dataset['image']['thumb']['16:9']['imageId'] = $airtime_product_data->image->thumb->{'16:9'}->imageId;
									}
								} else {
									## if not set - I do not change anything
								}
								## THUMB ##


								## HERO ##
								if( isset( $airtime_product_data->image->hero->master->imageId ) && !empty( $airtime_product_data->image->hero->master->imageId ) ){
									if( $document_details->airtime_reference == $airtime_product_data->image->hero->master->imageId ){
										$airtime_upd_dataset['image']['hero']['master']['imageId'] = "";
									} else {
										$airtime_upd_dataset['image']['hero']['master']['imageId'] = $airtime_product_data->image->hero->master->imageId;
									}
								} else {
									## if not set - I do not change anything
								}

								if( isset( $airtime_product_data->image->hero->{'2:3'}->imageId ) && !empty( $airtime_product_data->image->hero->{'2:3'}->imageId ) ){
									if( $document_details->airtime_reference == $airtime_product_data->image->hero->{'2:3'}->imageId ){
										$airtime_upd_dataset['image']['hero']['2:3']['imageId'] = "";
									} else {
										$airtime_upd_dataset['image']['hero']['2:3']['imageId'] = $airtime_product_data->image->hero->{'2:3'}->imageId;
									}
								} else {
									## if not set - I do not change anything
								}

								if( isset( $airtime_product_data->image->hero->{'5:4'}->imageId ) && !empty( $airtime_product_data->image->hero->{'5:4'}->imageId ) ){
									if( $document_details->airtime_reference == $airtime_product_data->image->hero->{'5:4'}->imageId ){
										$airtime_upd_dataset['image']['hero']['5:4']['imageId'] = "";
									} else {
										$airtime_upd_dataset['image']['hero']['5:4']['imageId'] = $airtime_product_data->image->hero->{'5:4'}->imageId;
									}
								} else {
									## if not set - I do not change anything
								}

								if( isset( $airtime_product_data->image->hero->{'16:9'}->imageId ) && !empty( $airtime_product_data->image->hero->{'16:9'}->imageId ) ){
									if( $document_details->airtime_reference == $airtime_product_data->image->hero->{'16:9'}->imageId ){
										$airtime_upd_dataset['image']['hero']['16:9']['imageId'] = "";
									} else {
										$airtime_upd_dataset['image']['hero']['16:9']['imageId'] = $airtime_product_data->image->hero->{'16:9'}->imageId;
									}
								} else {
									## if not set - I do not change anything
								}
								
								if( isset( $airtime_product_data->image->hero->{'16:7'}->imageId ) && !empty( $airtime_product_data->image->hero->{'16:7'}->imageId ) ){
									if( $document_details->airtime_reference == $airtime_product_data->image->hero->{'16:7'}->imageId ){
										$airtime_upd_dataset['image']['hero']['16:7']['imageId'] = "";
									} else {
										$airtime_upd_dataset['image']['hero']['16:7']['imageId'] = $airtime_product_data->image->hero->{'16:7'}->imageId;
									}
								} else {
									## if not set - I do not change anything
								}
								## HERO ##

								## CAROUSEL ##
								if( isset( $airtime_product_data->image->carousel->master->imageId ) && !empty( $airtime_product_data->image->carousel->master->imageId ) ){
									if( $document_details->airtime_reference == $airtime_product_data->image->carousel->master->imageId ){
										$airtime_upd_dataset['image']['carousel']['master']['imageId'] = "";
									} else {
										$airtime_upd_dataset['image']['carousel']['master']['imageId'] = $airtime_product_data->image->carousel->master->imageId;
									}
								} else {
									## if not set - I do not change anything
								}

								if( isset( $airtime_product_data->image->carousel->{'16:9'}->imageId ) && !empty( $airtime_product_data->image->carousel->{'16:9'}->imageId ) ){
									if( $document_details->airtime_reference == $airtime_product_data->image->carousel->{'18:9'}->imageId ){
										$airtime_upd_dataset['image']['carousel']['16:9']['imageId'] = "";
									} else {
										$airtime_upd_dataset['image']['carousel']['16:9']['imageId'] = $airtime_product_data->image->carousel->{'16:9'}->imageId;
									}
								} else {
									## if not set - I do not change anything
								}
								
								if( isset( $airtime_product_data->image->carousel->{'21:9'}->imageId ) && !empty( $airtime_product_data->image->carousel->{'21:9'}->imageId ) ){
									if( $document_details->airtime_reference == $airtime_product_data->image->carousel->{'21:9'}->imageId ){
										$airtime_upd_dataset['image']['carousel']['21:9']['imageId'] = "";
									} else {
										$airtime_upd_dataset['image']['carousel']['21:9']['imageId'] = $airtime_product_data->image->carousel->{'21:9'}->imageId;
									}
								} else {
									## if not set - I do not change anything
								}
								## CAROUSEL ##


								$airtime_updated_product = false;
								$airtime_updated_product = $this->easel_service->update_product( $account_id, $movie_details->external_content_ref, $airtime_upd_dataset );

								if( ( $airtime_updated_product->success !== false ) && ( $airtime_updated_product->data->id ) ){

									## Delete the image from airtime now
									$deleted_image_airtime = false;
									$deleted_image_airtime = $this->easel_service->delete_image( $account_id, $document_details->airtime_reference );

									if( isset( $deleted_image_airtime->success ) &&  $deleted_image_airtime->success != false ){
										$message 			= "Image deleted successfully on Easel";
										$this->session->set_flashdata( "message", "Image deleted successfully on Easel" );
									} else {
										## Easel delete image failed
										$ready_to_delete 	= false;
										$message 			= "Error deleting the image on Easel";
										$this->session->set_flashdata( "message", "Error deleting the image on Easel" );
										return $result;
									}
									
								} else {
									## Easel product update failed
									$ready_to_delete 	= false;
									$message 			= "Error removing the image from the product (movie) on Easel";
									$this->session->set_flashdata( "message", "Error removing the image from the product (movie) on Easel" );
									return $result;
								}

							} else {
								## we do have the Easel product ID but couldn't retrieve the data for the product from Easel
								$ready_to_delete 	= false;
								$message 			= "Error obtaining the product (movie) data from Easel for this image";
								$this->session->set_flashdata( "message", "Error obtaining the product (movie) data from Easel for this image" );
								return $result;
							}

						} else {
							## no movie (product) details for this image
							## For the clarity can't be deleted from the system - needs to be deleted manually 
							$ready_to_delete 	= false;
							$message 			= "Error obtaining the movie(product) data from CaCTI for this image";
							$this->session->set_flashdata( "message", "Error obtaining the movie(product) data from CaCTI for this image" );
							return $result;
						}

					} else {
						## document is not Standard or Hero - no need for action
					}
				}
			}
			
			if( $ready_to_delete != false ){
			
				$this->db->where( 'document_id', $document_id )
					->delete( $target_table );

				if( $this->db->affected_rows() > 0 ){

					$this->ssid_common->_reset_auto_increment( $target_table, 'document_id' );

					##Remove files from the Drive
					if( !empty( $document_details->document_location ) ){
						$file2delete = $this->app_root.$document_details->document_location;
						if( is_file( $file2delete ) && @unlink( $file2delete ) ){
							// delete success
						} else if ( is_file ( $file2delete ) ) {
							// unlink failed.
							// you would have got an error if it wasn't suppressed
						} else {
						  // file doesn't exist
						}
					}

					$result = true;
					
					$status_msg = ( !empty( $message ) ) ? "Easel response: <br />".$message : 'Document has been deleted' ;
					$this->session->set_flashdata( 'message', $status_msg );
				} else {
					$status_msg = 'Document hasn\t been deleted' ;
					$this->session->set_flashdata( 'message', $status_msg );
				}
			} else {
				$status_msg = ( !empty( $message ) ) ? 'Easel response: <br />"<i>'.$message.'</i>"<br /><br />CaCTI unable to proceed.' : 'CaCTI unable to proceed as valid Easel response required';
				$this->session->set_flashdata( 'message', $status_msg );
			}

		} else {
			$this->session->set_flashdata( 'message', 'Required Data is missing: Account_ID, Document Group or Document ID' );
		}

		return $result;
	}

}