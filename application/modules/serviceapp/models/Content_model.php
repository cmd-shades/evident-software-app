<?php if( !defined( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

class Content_model extends CI_Model {

	function __construct(){
		parent::__construct();

		$this->load->model( "serviceapp/settings_model", "settings_service" );
		$this->load->model( "serviceapp/provider_model", "provider_service" );
		$this->load->model( "serviceapp/Easel_Api_model", "easel_service" );
		$this->load->model( "serviceapp/Coggins_Api_model","coggins_service" );
		$this->load->library( 'upload' );
		$this->load->dbutil();
		$this->load->helper( 'xml' );

		$section 	   		= explode("/", $_SERVER["SCRIPT_NAME"]);
		if( !isset( $section[1] ) || empty( $section[1] ) || ( !( is_array( $section ) ) ) ){
			$this->app_root = substr( dirname( __FILE__ ), 0, strpos( dirname( __FILE__ ), "application" ) );
		} else {
			if ( !isset( $_SERVER["DOCUMENT_ROOT"] ) || ( empty( $_SERVER["DOCUMENT_ROOT"] ) ) ){
				$_SERVER["DOCUMENT_ROOT"] = realpath( dirname(__FILE__).'/../' );
			}

			$this->section		= $section;
			$this->app_root		= $_SERVER["DOCUMENT_ROOT"]."/".$section[1]."/";
			$this->app_root		= str_replace('/index.php','',$this->app_root);
		}
    }

	## possible airtime_encoded_status values from Easel API = ["not-encoded", "encoding", "encoded", "encode-cancelled", "encode-failed", "unknown"];
	private $airtime_encoded_statuses 	= ["not-encoded", "pending-encoding", "pending-encoding-error"];

	## AWS aws_status possible values
	private $aws_statuses 				= ["transfer_initiated", "transfer_initiating_error"];


	private $searchable_fields  		= ['content_film.title', 'content_film.asset_code' ];

	public function get_restriction_types( $account_id = false, $type_id = false, $where = false, $unorganized = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;

		if( !empty( $account_id ) ){

			$this->db->select( "content_restriction_type.*", false );

			if( !empty( $type_id ) ){
				$this->db->where( "type_id", $type_id );
			}

			if( !empty( $where ) ){

				$where = convert_to_array( $where );
				$this->db->where( $where );
			}

			$arch_where = "( content_restriction_type.archived != 1 or content_restriction_type.archived is NULL )";
			$this->db->where( $arch_where );
			$this->db->where( "content_restriction_type.active", 1 );
			$query = $this->db->get( "content_restriction_type" );

			if( !empty( $query->num_rows() && $query->num_rows() > 0 ) ){
				if( $unorganized ){
					$result = $query->result();
				} else {
					$dataset = $query->result();

					foreach( $dataset as $row ){
						$result[$row->type_id] = $row;
					}
				}
				$this->session->set_flashdata( 'message','Restriction(s) data found.' );
			} else {
				$this->session->set_flashdata( 'message','Restriction(s) data not found.' );
			}

		} else {
			$this->session->set_flashdata( 'message','Account ID not supplied.' );
		}

		return $result;
	}

	public function get_territories( $account_id = false, $territory_id = false, $where = false, $unorganized = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;

		if( !empty( $account_id ) ){

			if( !empty( $where ) ){

				$where = convert_to_array( $where );

				if( !empty( $where['content_id'] ) && !empty( $where['not_added'] ) && ( $where['not_added'] == 'yes' ) ){
					$content_id = $where['content_id'];

					## already added clearance territory ids
					$this->db->select( "territory_id" );
					$this->db->where( "account_id", $account_id );
					$this->db->where( "content_id", $content_id );
					$this->db->where( "active" , 1 );
					$arch_where = "( archived != 1 OR archived is NULL )";
					$this->db->where( $arch_where );

					$added_territories = $this->db->get( "content_clearance" )->result_array();

					$added_territories_array = array_column( $added_territories, "territory_id" );

					if( !empty( $added_territories_array ) ){
						$this->db->where_not_in( "territory_id", $added_territories_array );
					}

					unset( $where['content_id'] );
					unset( $where['not_added'] );
				}

				if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}

			$this->db->select( "content_territory.*", false );

			if( !empty( $territory_id ) ){
				$this->db->where( "territory_id", $territory_id );
			}

			$arch_where = "( content_territory.archived != 1 or content_territory.archived is NULL )";
			$this->db->where( $arch_where );
			$this->db->where( "content_territory.active", 1 );
			$this->db->order_by( "content_territory.country ASC" );

			$query = $this->db->get( "content_territory" );

			if( !empty( $query->num_rows() && $query->num_rows() > 0 ) ){
				if( $unorganized ){
					$result = $query->result();
				} else {
					$dataset = $query->result();

					foreach( $dataset as $row ){
						$result[$row->territory_id] 					= $row;
						$result[$row->territory_id]->country_n_code 	= ucfirst( strtolower( $row->country ) ).' '.strtoupper( $row->code );
					}
				}
				$this->session->set_flashdata( 'message','Territory(ies) data found.' );
			} else {
				$this->session->set_flashdata( 'message','Territory(ies) data not found.' );
			}

		} else {
			$this->session->set_flashdata( 'message','Account ID not supplied.' );
		}

		return $result;
	}


	/*
	* 	Create new Content. Can be a film, a game etc.
	*/
	public function create_content( $account_id = false, $content_data = false ){
		$result = false;
		if( !empty( $account_id ) && ( !empty( $content_data ) ) ){


			$data = [];
			$content_data = json_decode( $content_data );

			## section to create a film. Other content not requested yet (game, music)
			$content_film = ( !empty( $content_data->content_film ) ) ? ( $content_data->content_film ) : false ;
			unset( $content_data->content_film );

			if( !empty( $content_data ) ){
				foreach( $content_data as $key => $value ){
					if( in_array( $key, format_name_columns() ) ){
						$value = format_name( $value );
					} elseif( in_array( $key, format_email_columns() ) ){
						$value = format_email( $value );
					}elseif( in_array( $key, format_number_columns() ) ){
						$value = format_number( $value );
					} elseif ( in_array( $key, format_boolean_columns() ) ){
						$value = format_boolean( $value );
					} elseif ( in_array( $key, format_date_columns() ) ){
						$value = format_date_db( $value );
					} elseif( in_array( $key, format_long_date_columns() ) ){
						$value = format_datetime_db( $value );
					} elseif( in_array( $key, string_to_json_columns() ) ){
						$value = string_to_json( $value );
					} else {
						$value = trim( $value );
					}
					$data[$key] = $value;
				}

				if( !empty( $data ) ){
					$data['account_id']  	= $account_id;
					$data['created_by'] 	= $this->ion_auth->_current_user->id;
					$new_content_data 		= $this->ssid_common->_filter_data( 'content', $data );

					## check uniqueness of the provider reference code for content
					$provider_ref_code_exists = false;

					if( !empty( $new_content_data['content_provider_reference_code'] ) ){
						$this->db->where( 'content_provider_reference_code', $new_content_data['content_provider_reference_code'] );
						$this->db->where( 'account_id', $account_id );
						$provider_ref_code_exists = $this->db->get( "content" )->row();
					}

					if( !$provider_ref_code_exists ){
						$this->db->insert( 'content', $new_content_data );
						if( $this->db->trans_status() !== FALSE ){
							$content_insert_id 	= !empty( $this->db->insert_id() ) ? $this->db->insert_id() : false ;

							if( !empty( $content_film ) ){
								$film_data = $this->_save_content_film_data( $account_id, $content_insert_id, $content_film );
							}

							$result = ( !empty( $content_insert_id ) ) ? $this->get_content( $account_id, $content_insert_id ) : false ;
							$this->session->set_flashdata( 'message', 'Content record created successfully.' );
						}
					} else {
						$this->session->set_flashdata( 'message', 'Provider Reference Code for Asset already exists' );
					}
				}

			} else {
				$this->session->set_flashdata( 'message','There was an error processing the Content Data' );
			}
		} else {
			$this->session->set_flashdata( 'message','No Account Id or Content Data supplied.' );
		}
		return $result;
	}


	public function _save_content_film_data( $account_id = false, $content_id = false, $content_film_data = false ){
		$result = false;
		if( !empty( $account_id ) && ( !empty( $content_id ) ) && !empty( $content_film_data ) ){
			$easel_message	= "";
			$film_data 		= ( !array( $content_film_data ) ) ? convert_to_array( $content_film_data ) : $content_film_data;

			foreach( $film_data as $key => $value ){
				if( in_array( $key, format_name_columns() ) ){
					$value = format_name( $value );
				} elseif( in_array( $key, format_email_columns() ) ){
					$value = format_email( $value );
				}elseif( in_array( $key, format_number_columns() ) ){
					$value = format_number( $value );
				} elseif ( in_array( $key, format_boolean_columns() ) ){
					$value = format_boolean( $value );
				} elseif ( in_array( $key, format_date_columns() ) ){
					$value = format_date_db( $value );
				} elseif( in_array( $key, format_long_date_columns() ) ){
					$value = format_datetime_db( $value );
				} elseif( in_array( $key, string_to_json_columns() ) ){
					$value = string_to_json( $value );
				} else {
					$value = trim( $value );
				}
				$data[$key] = $value;
			}

			$data['account_id']  	= $account_id;
			$data['created_by'] 	= $this->ion_auth->_current_user->id;
			$data['content_id'] 	= $content_id;

			$film_exists 			= $this->db->get_where( "content_film", [ "account_id" => $account_id, "content_id" => $content_id ] )->row();

			$film_data 				= $this->ssid_common->_filter_data( 'content_film', $data );

			$is_airtime_asset 		= ( !empty( $data['is_airtime_asset'] ) && ( strtolower( $data['is_airtime_asset'] ) == 'yes' ) ) ? true : false;
			## $is_airtime_asset 		= false;

			if( $film_exists ){
				$film_data['modified_by'] 	= $this->ion_auth->_current_user->id;

				## translate CaCTi's genres into Easel's categories
				$categories = false;
				if( !empty( $film_data['genre'] ) ){
					$where							= [];
					$genres = json_decode( $film_data['genre'] );
					$where['genre_id'] 				= $genres;
					$where['return_plain_array']	= 'yes';
					$categories 					= $this->get_genres( $account_id, $where );
				}

				## Updating CaCTI with all the information, what's come
				$this->db->update( 'content_film', $film_data, ["content_id" => $film_exists->content_id] );

				## If there is incoming age rating:
				## 1. Update CaCTI's movie profile - already done
				## 2. Find appropriate Easel ID for the rating

				$age_rating = false;
				if( !empty( $film_data['age_rating_id'] ) ){
					$where							= [];
					$where['age_rating_id'] 		= $film_data['age_rating_id'];
					$where['return_plain_array']	= 'yes';
					$age_rating 					= $this->get_age_rating( $account_id, $where );
				}

				## 3. Update Easel's movie/product profile - see below

				## Update Product / Film to EASEL TV via API
				if( !empty( trim( $film_exists->external_content_ref ) ) && ( !empty( $is_airtime_asset ) ) ){
					$film_data['state']			= ( !empty( $film_data['airtime_state'] ) ) ? $film_data['airtime_state'] : false ;
					$film_data['name']			= $film_data['title'];
					$film_data['categories']	= $categories;
					if( !empty( $age_rating ) ){
						$film_data['ageRatings']	= $age_rating;
					}

					unset( $film_data['genre'] );
					unset( $film_data['age_rating_id'] );

					## Get missing items from the Product object:
					if( !empty( $film_exists->external_content_ref ) ){

						$airtime_product_data = $this->easel_service->fetch_product( $account_id, $film_exists->external_content_ref );

						if( !empty( $airtime_product_data ) && !empty( $airtime_product_data->id ) ){
							if( isset( $airtime_product_data->image->master->imageId ) && !empty( $airtime_product_data->image->master->imageId ) ){
								$film_data['image']['master']['imageId'] = $airtime_product_data->image->master->imageId;
							}

							if( isset( $airtime_product_data->image->hero->master->imageId ) && !empty( $airtime_product_data->image->hero->master->imageId ) ){
								$film_data['image']['hero']['master']['imageId'] = $airtime_product_data->image->hero->master->imageId;
							}

							if( isset( $airtime_product_data->image->thumb->master->imageId ) && !empty( $airtime_product_data->image->thumb->master->imageId ) ){
								$film_data['image']['thumb']['master']['imageId'] = $airtime_product_data->image->thumb->master->imageId;
							}

							if( isset( $airtime_product_data->image->thumb->{'2:3'}->imageId ) && !empty( $airtime_product_data->image->thumb->{'2:3'}->imageId ) ){
								$film_data['image']['thumb']['2:3']['imageId'] = $airtime_product_data->image->thumb->{'2:3'}->imageId;
							}

							if( isset( $airtime_product_data->image->poster->master->imageId ) && !empty( $airtime_product_data->image->poster->master->imageId ) ){
								$film_data['image']['poster']['master']['imageId'] = $airtime_product_data->image->poster->master->imageId;
							}

							if( isset( $airtime_product_data->trailer ) && !empty( $airtime_product_data->trailer ) ){
								$film_data["trailer"] = $airtime_product_data->trailer;
							}

							if( isset( $airtime_product_data->feature ) && !empty( $airtime_product_data->feature ) ){
								$film_data["feature"] = $airtime_product_data->feature;
							}

							if( isset( $airtime_product_data->published ) && !empty( $airtime_product_data->published ) ){
								$film_data["published"] = $airtime_product_data->published;
							}
						}

						$film_data["indexable"] 	= ( !empty( $airtime_product_data->indexable ) ) ? $airtime_product_data->indexable : true ;
						$film_data["episodeNumber"] = ( !empty( $airtime_product_data->episodeNumber ) ) ? $airtime_product_data->episodeNumber : false ;
					}

					$easel_api_push 			= $this->easel_service->update_product( $account_id, $film_exists->external_content_ref, ( array ) $film_data );

					if( !empty( $easel_api_push->data->id ) && ( isset( $easel_api_push->success ) && $easel_api_push->success != false ) ){

						$ext_ref_data = [
							'airtime_state'					=> $easel_api_push->data->state,
							'external_content_updated_on'	=> date( 'Y-m-d H:i:s' )
						];

						$this->db->where( 'content_film.content_id', $film_exists->content_id )->update( 'content_film', $ext_ref_data );

						$easel_message .= 'Product updated on Easel API Successfully';
					} else {
						$easel_message .= '<span class="red">Product Update Failed on Easel API. </span>';
					}
				} else {

					if( !empty( $is_airtime_asset ) ){
						## Publish on EaselTV (Never been sent before)
						$film_data['categories'] 			= false;
						if( !empty( $film_data['genre'] ) ){
							$where							= [];
							$genres = json_decode( $film_data['genre'] );
							$where['genre_id'] 				= $genres;
							$where['return_plain_array']	= 'yes';
							$film_data['categories']		= $this->get_genres( $account_id, $where );
						}

						$age_rating = false;
						if( !empty( $film_data['age_rating_id'] ) ){
							$where							= [];
							$where['age_rating_id'] 		= $film_data['age_rating_id'];
							$where['return_plain_array']	= 'yes';
							$age_rating 					= $this->get_age_rating( $account_id, $where );
							$film_data['ageRatings']		= $age_rating;
						}

						$easel_api_post = $this->easel_service->create_product( $account_id, ( array ) $film_data );

						if( !empty( $easel_api_post->success ) ){

							$ext_ref_data = [
								'external_content_ref'			=> $easel_api_post->data->id,
								'external_content_created_on'	=> date( 'Y-m-d H:i:s' ),
								'airtime_state'					=> $easel_api_post->data->state,
							];

							$this->db->where( 'content_film.content_id', $content_id )->update( 'content_film', $ext_ref_data );
							$easel_message .= ( !empty( $easel_api_post->message ) ) ? $easel_api_post->message : 'Product Created on Easel API Successfully';

						} else {
							$easel_message .= $easel_api_post->message;
						}
					}
				}

			} else {
				$film_data['account_id']  	= $account_id;
				$film_data['content_id'] 	= $content_id;
				$film_data['created_by'] 	= $this->ion_auth->_current_user->id;
				$this->db->insert( 'content_film', $film_data );
				$insert_id					= $this->db->insert_id();

				## Add Product / Film to EASEL TV via API
				/*if( !empty( $insert_id ) && !empty( $is_airtime_asset ) ){
					$easel_api_post = $this->easel_service->create_product( $account_id, $film_data );
					if( !empty( $easel_api_post->success ) ){

						$ext_ref_data = [
							'external_content_ref'			=> $easel_api_post->data->id,
							'external_content_created_on'	=> date( 'Y-m-d H:i:s' )
						];

						$this->db->where( 'content_film.content_id', $content_id )->update( 'content_film', $ext_ref_data );
						$easel_message = ( !empty( $easel_api_post->message ) ) ? $easel_api_post->message : 'Product Created on Easel API Successfully';

					} else {
						$easel_message = $easel_api_post->message;
					}
				}*/

			}

			if( $this->db->affected_rows() > 0 ){
				$content_id = ( !empty( $film_exists ) ) ? $film_exists->content_id : $insert_id;
				$result = $this->db->get_where( "content_film", ["account_id" => $account_id, "content_id" => $content_id ] )->row();
			}

			if( $this->db->affected_rows() > 0 ){

				if( $film_exists ){
					$film_id = $film_exists->film_id;
				} else {
					$film_id = $insert_id;
				}

				$result 	= $this->db->get_where( "content_film", ["account_id" => $account_id, "film_id" => $film_id] )->row();
				$message 	= 'Content Film data saved in CaCTi. ';
			} else {
				$message = 'The system couldn\'t save the Content Film data.';
			}

			$message = ( !empty( $easel_message ) ) ? $message.' <br/>'.$easel_message : $message;

		} else {
			$message = 'No Account Id or Content Film data supplied.';
		}

		$this->session->set_flashdata( 'message', $message );
		return $result;
	}


	public function get_content( $account_id = false, $content_id = false, $where = false, $unorganized = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;

		if( !empty( $account_id ) ){

			$this->db->select( "content.*", false );
			$this->db->select( "content_film.*", false );
			$this->db->select( "content.content_id", false );
			$this->db->select( "content_provider.*", false );
			$this->db->select( "age_rating.age_rating_name, age_rating.age_rating_desc", false );
			$this->db->select( "CONCAT( u1.first_name, ' ', u1.last_name ) `created_by_full_name`", false );
			$this->db->select( "content_decoded_file.file_id `at_feature_file_id`, content_decoded_file.file_new_name `at_file_new_name`, content_decoded_file.airtime_reference `at_vod_media_reference`, content_decoded_file.is_linked_with_airtime `at_vod_media_is_linked_with_airtime`", false );

			$this->db->join( "content_provider", "content_provider.provider_id = content.content_provider_id", "left" );
			$this->db->join( "content_film", "content_film.content_id = content.content_id", "left" );
			$this->db->join( 'age_rating', 'age_rating.age_rating_id = content_film.age_rating_id', 'left' );
			$this->db->join( 'user `u1`', 'u1.id = content_film.created_by', 'left' );
			$this->db->join( 'content_decoded_file', 'content_decoded_file.file_id = content_film.airtime_feature_file_id', 'left' );

			if( !empty( $content_id ) ){
				$this->db->where( "content.content_id", $content_id );
			}

			if( !empty( $where ) ){
				$where = convert_to_array( $where );

				if( !empty( $where['content_provider'] ) ){
					$content_provider = $where['content_provider'];
					unset( $where['content_provider'] );
					$this->db->where( "content_provider.provider_id", $content_provider );
				} else {
					$this->db->where( $where );
				}
			}

			$arch_where = "( content.archived != 1 or content.archived is NULL )";
			$this->db->where( $arch_where );
			$this->db->where( "content.active", 1 );
			$query = $this->db->get( "content" );

			if( !empty( $query->num_rows() && $query->num_rows() > 0 ) ){

				$dataset = $query->result();

				if( $unorganized ){
					$result = $dataset;
				} else {
					if( !empty( $content_id ) ){
						$result = $dataset[0];
					} else {
						foreach( $dataset as $row ){
							$result[$row->content_id] = $row;
						}
					}
				}
				$this->session->set_flashdata( 'message','Content data found.' );
			} else {
				$this->session->set_flashdata( 'message','Content data not found.' );
			}

		} else {
			$this->session->set_flashdata( 'message','Account ID not supplied.' );
		}

		return $result;
	}


	/*
	*	Content Lookup
	*/
	public function content_lookup( $account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;
		if( !empty( $account_id ) ){

			$this->db->select( 'content.*, content_film.*, content_provider.*', false );
			$this->db->select( 'age_rating.age_rating_name, age_rating.age_rating_desc', false );

			$this->db->join( 'content_film','content_film.content_id = content.content_id','left' );
			$this->db->join( 'content_provider','content_provider.provider_id = content.content_provider_id','left' );
			$this->db->join( 'age_rating', 'age_rating.age_rating_id = content_film.age_rating_id', 'left' );

			$this->db->where( 'content.account_id', $account_id );

			$arch_where = "( content.archived != 1 or content.archived is NULL )";
			$this->db->where( $arch_where );

			if( !empty( $search_term ) ){

				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {

					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){

						foreach( $this->searchable_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}

				} else {

					foreach( $this->searchable_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( !empty( $where ) ){

				$where = convert_to_array( $where );

				if( !empty( $where['content_provider'] ) ){
					$content_provider = $where['content_provider'];
					unset( $where['content_provider'] );
					$this->db->where( "content.content_provider_id", $content_provider );
				} else {
					$this->db->where( $where );
				}
			}

			if( $order_by ){
				$this->db->order_by( $order_by );
			} else {
				$this->db->order_by( 'content_film.film_id DESC' );
			}

			$query = $this->db->limit( $limit, $offset )
				->get( 'content' );

			if( $query->num_rows() > 0 ){
				$result = $query->result();
				$this->session->set_flashdata('message','Records found.');
			} else {
				$this->session->set_flashdata('message','No records found matching your criteria.');
			}
		}

		return $result;
	}


	public function get_total_content( $account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;
		if( !empty( $account_id ) ){

			$this->db->select( 'content.*, content_film.*, content_provider.*', false );
			$this->db->select( 'age_rating.age_rating_name, age_rating.age_rating_desc', false );

			$this->db->join( 'content_film','content_film.content_id = content.content_id','left' );
			$this->db->join( 'content_provider','content_provider.provider_id = content.content_provider_id','left' );
			$this->db->join( 'age_rating', 'age_rating.age_rating_id = content_film.age_rating_id', 'left' );

			$this->db->where( 'content.account_id', $account_id );

			$arch_where = "( content.archived != 1 or content.archived is NULL )";
			$this->db->where( $arch_where );

			if( !empty( $search_term ) ){

				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {

					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){

						foreach( $this->searchable_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}

				} else {

					foreach( $this->searchable_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );

				}
			}

			if( !empty( $where ) ){
				$where = convert_to_array( $where );

				if( !empty( $where['content_provider'] ) ){
					$content_provider = $where['content_provider'];
					unset( $where['content_provider'] );
					$this->db->where( "content.content_provider_id", $content_provider );
				} else {
					$this->db->where( $where );
				}
			}


			if( $order_by ){
				$this->db->order_by( $order_by );
			} else {
				$this->db->order_by( 'content_film.film_id DESC' );
			}

			$query = $this->db->from( 'content' )->count_all_results();

			$results['total'] = !empty( $query ) ? $query : 0;
			$results['pages'] = !empty( $query ) ? ceil( $query / ( ( $limit > 0 ) ? $limit : DEFAULT_LIMIT  )  ) : 0;
			return json_decode( json_encode( $results ) );
		}
		return $result;
	}



	/*
	* 	Delete Content
	*/
	public function delete_content( $account_id = false, $content_id = false ){
		$result = false;
		if( !empty( $account_id )  && !empty( $content_id ) ){

			$content_b4 = $this->get_content( $account_id, $content_id );

			$data = [
				"archived" 		=> 1,
				"active"		=> 0,
				"modified_by"	=> $this->ion_auth->_current_user->id,
			];

			## archive the Provider Reference Code to be available
			if( !empty( $content_b4->content_provider_reference_code ) ){
				$data['content_provider_reference_code']			= $content_b4->content_provider_reference_code.'_arch_'.microtime( TRUE ) ;
				## To keep the column unique and avoid duplicates as a closest possible action tested on two submissions at once: inserted new row at the same time when update. Micro time( true) picked the difference, time() didn't.
			}

			$d_content_data 	= $this->ssid_common->_filter_data( 'content', $data );
			$this->db->update( 'content', $d_content_data, ["content_id" => $content_id, "account_id" => $account_id] );

			if( $this->db->trans_status() !== FALSE ){
				$result = true;

				// deleting the movie content if the content film exists
				$content_film_exists = $this->db->get_where( "content_film", ["content_id" => $content_id, "account_id"=>$account_id] )->row();
				if( !empty( $content_film_exists ) ){
					$data_film_data = [
						"archived" 		=> 1,
						"active"		=> 0,
						"modified_by"	=> $this->ion_auth->_current_user->id,
					];

					$this->db->update( 'content_film', $data_film_data, ["content_id" => $content_id, "account_id"=>$account_id] );
				}

				$this->session->set_flashdata( 'message', 'Content record has been deleted.' );
			} else {
				$this->session->set_flashdata( 'message', 'Content record hasn\'t been deleted.' );
			}

		} else {
			$this->session->set_flashdata( 'message', 'No Account Id or Content ID supplied.' );
		}
		return $result;
	}


	/*
	* 	Update Content record
	*/
	public function update_content( $account_id = false, $content_id = false, $content_data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $content_id ) && !empty( $content_data ) ){

			$check_content = $this->db->get_where( 'content', ['account_id'=>$account_id, 'content_id'=>$content_id] )->row();

			if( !empty( $check_content ) ){

				$details_updated = $imdb_updated = false;

				$content_data = object_to_array( json_decode( $content_data ) );

				if( !empty( $content_data['imdb_details'] ) ){
					$imdb_details = $content_data['imdb_details'];
					unset( $content_data['imdb_details'] );
				}
				$data = [];
				if( !empty( $content_data['content_details'] ) ){

					foreach( $content_data['content_details'] as $key => $value ){
						if( in_array( $key, format_name_columns() ) ){
							$value = format_name( $value );
						} elseif( in_array( $key, format_email_columns() ) ){
							$value = format_email( $value );
						} elseif( in_array( $key, format_number_columns() ) ){
							$value = format_number( $value );
						} elseif ( in_array( $key, format_boolean_columns() ) ){
							$value = format_boolean( $value );
						} elseif ( in_array( $key, format_date_columns() ) ){
							$value = format_date_db( $value );
						} elseif( in_array( $key, format_long_date_columns() ) ){
							$value = format_datetime_db( $value );
						} elseif( in_array( $key, string_to_json_columns() ) ){
							$value = string_to_json( $value );
						} else {
							$value = trim( $value );
						}
						$data[$key] = $value;
					}

					if( !empty( $data ) ){

						## check the uniqueness of the provider reference code
						$provider_ref_code_exists = false;

						if( !empty( $data['content_provider_reference_code'] ) ){
							$this->db->where( 'content_provider_reference_code', $data['content_provider_reference_code'] );
							$this->db->where( 'account_id', $account_id );
							$this->db->where_not_in( 'content_id', $content_id ); ## to omit the self one
							$provider_ref_code_exists = $this->db->get( "content" )->row();
						}

						if( !$provider_ref_code_exists ){

							## get movie details
							$this->db->select( "content.is_airtime_asset", false );
							$this->db->select( "content_film.*", false );
							$this->db->select( "age_rating.age_rating_name, age_rating.age_rating_desc", false );

							$this->db->join( 'age_rating', 'age_rating.age_rating_id = content_film.age_rating_id', 'left' );
							$this->db->join( "content", "content.content_id = content_film.content_id", "left" );
							$movie_query = false;
							$movie_query = $this->db->get_where( "content_film", ["content_film.content_id"=>$content_id, "content_film.account_id"=>$account_id] )->row();

							// Doing the update 'product' on Easel first - if this will fail, I shouldn't do it on CaCTi
							// Considerations:
							// - general thought - take first from incoming data from the update form, then from the database
							// - I shouldn't be able to change the asset code using the web client, so pulling it from the DB
							// - We have to deal with the state separately... :
							// - If the specific ('state': published) comes and we're still airtime product ('is_airtime_asset':yes), we then will trigger change state to 'published'

							if( isset( $movie_query ) && !empty( $movie_query->is_airtime_asset ) && ( strtolower( $movie_query->is_airtime_asset ) == "yes" ) && !empty( $movie_query->external_content_ref ) ){

								$Easel_update_data = [];
								$Easel_update_data = [
									'id' 				=> ( !empty( $movie_query->external_content_ref ) ) ? $movie_query->external_content_ref : '' ,
									'asset_code' 		=> ( !empty( $movie_query->asset_code ) ) ? $movie_query->asset_code : '' ,
									'title' 			=> ( !empty( $data['title'] ) ) ? html_escape( $data['title'] ) : ( ( !empty( $movie_query->title ) ) ? $movie_query->title : '' ) ,
									'name' 				=> ( !empty( $data['title'] ) ) ? html_escape( $data['title'] ) : ( ( !empty( $movie_query->title ) ) ? $movie_query->title : '' ) ,
									'tagline' 			=> ( !empty( $data['tagline'] ) ) ? html_escape( $data['tagline'] ) : ( ( !empty( $movie_query->tagline ) ) ? html_escape( $movie_query->tagline ) : '' ) ,
									'plot' 				=> ( !empty( $data['plot'] ) ) ? html_escape( $data['plot'] ) : ( ( !empty( $movie_query->plot ) ) ? html_escape( $movie_query->plot ) : '' ) ,
									'running_time' 		=> ( !empty( $data['running_time'] ) ) ? ( int )( $data['running_time'] ) : ( ( !empty( $movie_query->running_time ) ) ? ( int )( $movie_query->running_time ) : '' ) ,
									'country' 			=> ( !empty( $data['country'] ) ) ? html_escape( $data['country'] ) : ( ( !empty( $movie_query->country ) ) ? html_escape( $movie_query->country ) : 'GBR' ) ,
									'release_date' 		=> ( isset( $data['release_date'] ) && validate_date( $data['release_date'] ) ) ? format_date_db( $data['release_date'] ) : ( ( validate_date( $movie_query->release_date ) ) ? format_date_db( $movie_query->release_date ) : '' ) ,
								];

								$Easel_update_data['categories'] 			= false;
								if( !empty( $data['genre'] ) ){
									$where									= [];
									$genres = json_decode( $data['genre'] );
									$where['genre_id'] 						= $genres;
									$where['return_plain_array']			= 'yes';
									$Easel_update_data['categories']		= $this->get_genres( $account_id, $where );
								}

								$Easel_update_data['ageRatings'] 			= false;
								if( !empty( $data['age_rating_id'] ) ){
									$where									= [];
									$where['age_rating_id'] 				= $data['age_rating_id'];
									$where['return_plain_array']			= 'yes';
									$age_rating 							= $this->get_age_rating( $account_id, $where );
									$Easel_update_data['ageRatings']		= $age_rating;
								}

								if( !empty( $data['is_airtime_asset'] ) && strtolower( $data['is_airtime_asset'] ) == "no" ){
									$Easel_update_data['state'] = "offline";
								}

								if( ( !empty( $data['state'] ) && strtolower( $data['state'] ) == "published" ) && ( !empty( $data['is_airtime_asset'] ) && strtolower( $data['is_airtime_asset'] ) == "yes" ) ){
									$Easel_update_data['state'] = "published";
								}

								## Get missing items from the Product object
								if( !empty( $movie_query->external_content_ref ) ){

									$airtime_product_data = $this->easel_service->fetch_product( $account_id, $movie_query->external_content_ref );
									log_message( "error", json_encode( ["easel product data" =>$airtime_product_data ] ) );

									if( !empty( $airtime_product_data ) && !empty( $airtime_product_data->id ) ){
										if( isset( $airtime_product_data->image->master->imageId ) && !empty( $airtime_product_data->image->master->imageId ) ){
											$Easel_update_data['image']['master']['imageId'] = $airtime_product_data->image->master->imageId;
										}

										if( isset( $airtime_product_data->image->hero->master->imageId ) && !empty( $airtime_product_data->image->hero->master->imageId ) ){
											$Easel_update_data['image']['hero']['master']['imageId'] = $airtime_product_data->image->hero->master->imageId;
										}

										if( isset( $airtime_product_data->image->thumb->master->imageId ) && !empty( $airtime_product_data->image->thumb->master->imageId ) ){
											$Easel_update_data['image']['thumb']['master']['imageId'] = $airtime_product_data->image->thumb->master->imageId;
										}

										if( isset( $airtime_product_data->image->poster->master->imageId ) && !empty( $airtime_product_data->image->poster->master->imageId ) ){
											$Easel_update_data['image']['poster']['master']['imageId'] = $airtime_product_data->image->poster->master->imageId;
										}


										if( isset( $airtime_product_data->image->thumb->{'2:3'}->imageId ) && !empty( $airtime_product_data->image->thumb->{'2:3'}->imageId ) ){
											$Easel_update_data['image']['thumb']['2:3']['imageId'] = $airtime_product_data->image->thumb->{'2:3'}->imageId;
										}

										## If there are new categories incoming:
										if( !isset( $Easel_update_data['categories'] ) || empty( $Easel_update_data['categories'] ) ){
											if( isset( $airtime_product_data->categories ) && !empty( $airtime_product_data->categories ) ){
												$Easel_update_data['categories'] = $airtime_product_data->categories;
											}
										}

										## If there is new Age Rating incoming:
										if( !isset( $Easel_update_data['ageRatings'] ) || empty( $Easel_update_data['ageRatings'] ) ){
											if( isset( $airtime_product_data->ageRatings ) && !empty( $airtime_product_data->ageRatings ) ){
												$Easel_update_data['ageRatings'] = $airtime_product_data->ageRatings;
											}
										}

										$Easel_update_data["reference"] 		= ( !empty( $airtime_product_data->reference ) ) ? $airtime_product_data->reference : '' ;
										$Easel_update_data["type"] 				= ( !empty( $airtime_product_data->type ) ) ? $airtime_product_data->type : '' ;
										$Easel_update_data["parentalAdvisory"] 	= ( !empty( $airtime_product_data->parentalAdvisory ) ) ? $airtime_product_data->parentalAdvisory : '' ;

										if( isset( $airtime_product_data->published ) &&  !empty( $airtime_product_data->published ) ){
											$Easel_update_data["published"] 			= $airtime_product_data->published;
										}

										if( isset( $airtime_product_data->trailer ) &&  !empty( $airtime_product_data->trailer ) ){
											$Easel_update_data["trailer"] 			= $airtime_product_data->trailer;
										}

										if( isset( $airtime_product_data->feature ) &&  !empty( $airtime_product_data->feature ) ){
											$Easel_update_data["feature"] 			= $airtime_product_data->feature;
										}

										$Easel_update_data["indexable"] 		= ( !empty( $airtime_product_data->indexable ) ) ? $airtime_product_data->indexable : true ;
										$Easel_update_data["episodeNumber"] 	= ( !empty( $airtime_product_data->episodeNumber ) ) ? $airtime_product_data->episodeNumber : false ;
									}
								}
log_message( "error", json_encode( ["easel Easel_update_data data" =>$Easel_update_data ] ) );
								$easel_api_push = $this->easel_service->update_product( $account_id, $movie_query->external_content_ref, ( array ) $Easel_update_data );

								if( !empty( $easel_api_push->data->id ) ){
									// This is an EASEl asset, now we're ready to do CaCTi update
									$easel_message = 'Product updated on Easel API Successfully';
								} else {
									// This is an EASEL asset, the update failed - in this case we should go back
									$easel_message = 'Product Update Failed on Easel API';
									return $result;
								}
							}

							// if an EASEL asset and EASEL update successful we should get here and do CaCTi update as well as it is not an EASEL asset
							$data['modified_by']		= $this->ion_auth->_current_user->id;
							$update_data 				= $this->ssid_common->_filter_data( 'content', $data );

							$this->db->update( "content", $update_data, ['account_id' => $account_id, 'content_id' => $content_id] );

							if( $this->db->affected_rows() > 0 ){
								$details_updated = true;
							}

						} else {
							$this->session->set_flashdata( 'message', 'Provider Reference Code already exists' );
							return $result;
						}
					} else {
						$this->session->set_flashdata( 'message', 'There was an error processing your data' );
						return $result;
					}
				}

				## Update / Save the IMDb details
				if( !empty( $imdb_details ) ){
					$imdb_updated 	= $this->_save_content_film_data( $account_id, $content_id, $imdb_details );
					$imdb_message	= ( !empty( $this->session->flashdata( 'message' ) ) ) ? $this->session->flashdata( 'message' ) : '' ;
				}

				if( $details_updated || $imdb_updated ){
					$result = $this->get_content( $account_id, $content_id );
					$this->session->set_flashdata( 'message', ( ( !empty( $imdb_message ) ) ? $imdb_message : '' ).' <br />Content record updated successfully in CaCTi' );
				} else {
					$this->session->set_flashdata( 'message', 'No changes were requested' );
				}
			} else {
				$this->session->set_flashdata( 'message','Foreign content record. Access denied.' );
			}
		} else {
			$this->session->set_flashdata( 'message','No Content data supplied' );
		}
		return $result;
	}


	public function add_clearance( $account_id = false, $content_id = false, $clearance_start_date = false, $territories = false ){
		$result = false;

		if( !empty( $account_id ) ){
			if( !empty( $content_id  ) ){
				if( !empty( $clearance_start_date ) ){
					if( !empty( $territories ) ){
						$territories 	= json_decode( $territories );
						$content_id 	= json_decode( $content_id );

						$i = 0;
						if( is_array( $content_id ) ){
							foreach( $content_id as $key => $cont_id ){
								if( is_array( $territories ) ){
									foreach( $territories as $territory_id ){
										$batch_data[$i]['account_id'] 			= $account_id;
										$batch_data[$i]['content_id'] 			= $cont_id;
										$batch_data[$i]['clearance_start_date'] = format_date_db( $clearance_start_date );
										$batch_data[$i]['territory_id']			= $territory_id;
										$batch_data[$i]['created_by']			= $this->ion_auth->_current_user->id;
										$i++;
									}
								} else {
									$batch_data[$i]['account_id'] 				= $account_id;
									$batch_data[$i]['content_id'] 				= $cont_id;
									$batch_data[$i]['clearance_start_date'] 	= format_date_db( $clearance_start_date );
									$batch_data[$i]['created_by'] 				= $this->ion_auth->_current_user->id;
									$i++;
								}
							}
						} else {
							if( is_array( $territories ) ){
								foreach( $territories as $key => $territory_id ){
									$batch_data[$i]['account_id'] 				= $account_id;
									$batch_data[$i]['territory_id']				= $territory_id;
									$batch_data[$i]['content_id'] 				= $content_id;
									$batch_data[$i]['clearance_start_date'] 	= format_date_db( $clearance_start_date );
									$batch_data[$i]['created_by'] 				= $this->ion_auth->_current_user->id;
									$i++;
								}
							} else {
								$batch_data[0]['account_id'] 					= $account_id;
								$batch_data[0]['territory_id'] 					= $territories;
								$batch_data[0]['content_id'] 					= $content_id;
								$batch_data[0]['clearance_start_date'] 			= format_date_db( $clearance_start_date );
								$batch_data[0]['created_by'] 					= $this->ion_auth->_current_user->id;
							}
						}

						$this->db->insert_batch( "content_clearance", $batch_data );

						if( $this->db->affected_rows() > 0 ){
							$insert_id 	= $this->db->insert_id();
							$result		= $this->db->get_where( "content_clearance", ["account_id"=>$account_id, "clearance_id"=>$insert_id] )->row();
							$this->session->set_flashdata( 'message','Clearance Date(s) been added' );
						} else {
							$this->session->set_flashdata( 'message','There was an error processing your request' );
						}
					} else {
						$this->session->set_flashdata( 'message','No Territory(ies) supplied.' );
					}
				} else{
					$this->session->set_flashdata( 'message','No Clearance Date supplied.' );
				}
			} else {
				$this->session->set_flashdata( 'message','No Content ID supplied.' );
			}
		} else {
			$this->session->set_flashdata( 'message','No Account ID supplied.' );
		}

		return $result;

	}


	/*
	*	Get Clearance for the specific content
	*/
	public function get_clearance( $account_id = false, $clearance_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;

		if( !empty( $account_id ) ){

			$this->db->select( "content_clearance.*", false );
			$this->db->select( "content_territory.territory_id, content_territory.country, content_territory.code", false );
			$this->db->select( "concat( user.first_name, ' ', user.last_name ) created_by_full_name", false );

			$this->db->join( "content_territory", "content_territory.territory_id = content_clearance.territory_id", "left" );
			$this->db->join( "user", "user.id = content_clearance.created_by", "left" );

			if( !empty( $clearance_id ) ){
				$this->db->where( "content_clearance.clearance_id", $clearance_id );
			}

			if( !empty( $where ) ){
				$where = convert_to_array( $where );

				if( !empty( $where['content_id'] ) ){
					$content_id = $where['content_id'];
					unset( $where['content_id'] );
					$this->db->where( "content_clearance.content_id", $content_id );

				} else if( !empty( $where['territory_id'] ) ){
					$territory_id = $where['territory_id'];
					unset( $where['territory_id'] );
					$this->db->where( "content_clearance.territory_id", $territory_id );

				} else if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}

			$arch_where = "( content_clearance.archived != 1 or content_clearance.archived is NULL )";
			$this->db->where( $arch_where );
			$this->db->where( "content_clearance.active", 1 );
			$query = $this->db->get( "content_clearance" );

			if( !empty( $query->num_rows() && $query->num_rows() > 0 ) ){

				$dataset = $query->result();
				if( !empty( $clearance_id ) ){
					$result = $dataset[0];
				} else {
					foreach( $dataset as $row ){
						$result[$row->clearance_id] = $row;
					}
				}
				$this->session->set_flashdata( 'message','Clearance data found.' );
			} else {
				$this->session->set_flashdata( 'message','Clearance data not found.' );
			}

		} else {
			$this->session->set_flashdata( 'message','Account ID not supplied.' );
		}

		return $result;
	}


	/** Process Clearance Upload **/
	public function upload_content( $account_id = false ){
		$result = null;
		if( !empty( $account_id ) ){
			$uploaddir  = $this->app_root. 'assets' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;

			if( !file_exists( $uploaddir) ){
				mkdir( $uploaddir );
			}

			$this->db->truncate( 'content_clearance_tmp_upload' );

			for( $i=0; $i < count( $_FILES['upload_file']['name'] ); $i++ ) {
				//Get the temp file path
				$tmpFilePath = $_FILES['upload_file']['tmp_name'][$i];
				if ( $tmpFilePath != '' ){
					$uploadfile = $uploaddir . basename( $_FILES['upload_file']['name'][$i] ); //Setup our new file path
					if ( move_uploaded_file( $tmpFilePath, $uploadfile) ) {
						//If FILE is CSV process differently
						$ext = pathinfo( $uploadfile, PATHINFO_EXTENSION );
						if ( $ext == 'csv' ){
							$processed = csv_file_to_array( $uploadfile );
							if( !empty( $processed ) ){
								$data = $this->_save_temp_data( $account_id, $processed );
								if( $data ){
									unlink( $uploadfile );
									$result = true;
								}
							}
						}
					}
				}
			}
		}
		return $result;
	}


	/** Process uploaded array **/
	private function _save_temp_data( $account_id = false, $raw_data = false ){
		$result = null;
		if( !empty( $account_id ) && !empty( $raw_data ) ){
			$exists = $new = [];
			foreach( $raw_data as $k => $record ){ ## it is to check if in the table aren't duplicates looking at which column - territory_name

				## we do try to redefine the clearance_date field
				if( !empty( $record['clearance_date'] ) ){
					$date 			= strtotime( $record['clearance_date'] );
					$new_date 		= date( 'Y-m-d', $date );
					## $datetime = DateTime::createFromFormat( "d.n.y", $record['clearance_date'] );

					if( !empty( $new_date ) ){
						## $new_date = $datetime->format( 'Y-m-d' );
						$raw_data[$k]['clearance_date'] = $record['clearance_date'] = $new_date;
					} else {
						## $datetime = DateTime::createFromFormat( "d.n.y", $record['clearance_date'] );
					}

				}

				## the table is freshly cleaned
				## the territory is a leading column - checking against it
				## we do hope nothing is there, so everything will go to the 'new'
				$check_exists = $this->db->where( ['territory_name' => $record['territory_name'] ] )
					->limit( 1 )
					->get( 'content_clearance_tmp_upload' )
					->row();

				if( !empty( $check_exists ) ){
					$exists[] 	= $this->ssid_common->_filter_data( 'content_clearance_tmp_upload', $record );
				} else {
					$new[]  	= $this->ssid_common->_filter_data( 'content_clearance_tmp_upload', $record );
				}
			}

			//Updated existing
			if( !empty( $exists ) ){
				$this->db->update_batch( 'content_clearance_tmp_upload', $exists, 'territory_name' );
			}

			//Insert new records
			if( !empty( $new ) ){
				$this->db->insert_batch( 'content_clearance_tmp_upload', $new );
			}

			$result = ( $this->db->affected_rows() > 0 ) ? true : false;
		}
		return $result;
	}


	/** Get records pending from upload **/
	public function get_pending_upload_records( $account_id = false ){
		$result = null;
		if( !empty( $account_id ) ){

			$this->db->select( "content_clearance_tmp_upload.*", false );
			$this->db->select( "content_film.title", false );
			$this->db->select( "content_territory.country, content_territory.territory_id `db_territory_id`", false );

			$this->db->order_by( "content_clearance_tmp_upload.territory_name" );

			$this->db->join( 'content_film', 'content_film.content_id = content_clearance_tmp_upload.content_id', 'left' );
			$this->db->join( 'content_territory', 'content_territory.country = content_clearance_tmp_upload.territory_name', 'left' );

			$arch_where = "( content_territory.archived != 1 or content_territory.archived is NULL )";
			$this->db->where( $arch_where );

			$query = $this->db->get( 'content_clearance_tmp_upload' );

			if( $query->num_rows() > 0 ){
				$data = [];
				foreach( $query->result() as $k => $row ){

					$this->db->select( 'content_clearance.clearance_id, content_clearance.territory_id, content_clearance.content_id' );

					$this->db->join( 'content_territory', 'content_territory.territory_id = content_clearance.territory_id', 'left' );

					$this->db->where( 'content_territory.country', $row->territory_name );
					$this->db->where( 'content_clearance.content_id', $row->content_id );
					$this->db->limit( 1 );
					$check = $this->db->get( 'content_clearance' )->row();

					if( !empty( $check->territory_id ) ){
						$data['existing-records'][] = ( array ) $row;
					} else {
						$data['new-records'][] = ( array ) $row;
					}
				}
				$result = $data;
			}
		}
		return $result;
	}


	/*
	*	Add Batch of the clearance entries
	*/
	public function add_clearance_batch( $account_id = false, $clearance_batch = false ){


		$result = $formatted_batch = $batch = $data = false;

		if( !empty( $account_id ) && !empty( $clearance_batch ) ){
			$formatted_batch 	= object_to_array( json_decode( $clearance_batch ) );
			$batch 				= $this->security->xss_clean( $formatted_batch );
			if( !empty( $batch ) ){
				foreach( $batch as $key => $row ){
					if( $row['checked'] == 1 ){
						if( !empty( $row['clearance_date'] ) ){
							$row['clearance_start_date']	= format_date_db( $row['clearance_date'] );
							unset( $row['clearance_date'] );
						} else if( !empty( $row['clearance_start_date'] ) ){
							$row['clearance_start_date']	= format_date_db( $row['clearance_start_date'] );
						} else {
							$row['clearance_start_date']	= NULL;
						}

						$row['account_id']	= $account_id;
						$row['created_by']	= $this->ion_auth->_current_user->id;
						unset( $row['checked'] );
						unset( $row['territory_name'] );
						unset( $row['title'] );

						$data[$key] 		= $row;
					}
				}

				if( !empty( $data ) ){
					$this->db->insert_batch( "content_clearance", $data );

					if( $this->db->affected_rows() > 0 ){
						$result = $this->get_clearance( $account_id );
						$this->session->set_flashdata( 'message', 'Clearance Batch processed successfully' );
					} else {
						$this->session->set_flashdata( 'message', 'There was an error saving the Clearance Batch' );
					}
				} else{
					$this->session->set_flashdata( 'message', 'There was an error processing the Clearance Batch' );
				}
			}

		} else {
			$this->session->set_flashdata( 'message', 'Account ID and Clearance Batch are required' );
		}

		return $result;
	}


	/*
	*	Remove Batch from the clearance temporary table
	*/
	public function remove_clearance_from_tmp( $account_id = false, $clearance_batch = false ){


		$result = $formatted_batch = $batch = $data = false;

		if( !empty( $account_id ) && !empty( $clearance_batch ) ){

			$formatted_batch 	= object_to_array( json_decode( $clearance_batch ) );
			$batch 				= $this->security->xss_clean( $formatted_batch );

			if( !empty( $batch ) ){
				foreach( $batch as $key => $row ){
					if( $row['checked'] == 1 ){
						$ids_2_delete[] = $key;
					}
				}

				if( !empty( $ids_2_delete ) ){
					$this->db->where_in( "tmp_clearance_id", $ids_2_delete );
					$delete_query = $this->db->delete( "content_clearance_tmp_upload" );

					$process_entries = $this->db->affected_rows();

					if( $process_entries == count( $ids_2_delete ) ){
						$this->session->set_flashdata( 'message', 'All entries have been deleted' );
						$result = true;
					} else if( ( $process_entries > 0 ) && ( $process_entries < count( $ids_2_delete ) ) ){
						$this->session->set_flashdata( 'message', 'There was an error saving the Clearance Batch' );
						$result = true;
					} else {
						$this->session->set_flashdata( 'message', 'Entries hasn\'t been deleted' );
					}
				} else{
					$this->session->set_flashdata( 'message', 'There was an error processing the Clearance Batch' );
				}
			}

		} else {
			$this->session->set_flashdata( 'message', 'Account ID and Clearance Batch are required' );
		}

		return $result;
	}



	/*
	* 	Delete Clearance
	*/
	public function delete_clearance( $account_id = false, $clearance_id = false ){
		$result = false;
		if( !empty( $account_id )  && !empty( $clearance_id ) ){
			
			$prevent_from_deleting  = false;
			$message				= "Clearance hasn\'t been deleted.";

			/*
			## Delete entry introduced 14/07/2020
			$data = [
				"archived" 		=> 1,
				"active"		=> 0,
				"modified_by"	=> $this->ion_auth->_current_user->id,
			];

			$d_clearance 	= $this->ssid_common->_filter_data( 'content_clearance', $data );
			$this->db->update( 'content_clearance', $d_clearance, ["clearance_id" => $clearance_id, "account_id" => $account_id] ); */

			// check if this is an airtime content!!!
			$this->db->select( "content_film.content_id, content_film.title, content_film.external_content_ref", false );
			$this->db->select( "content_clearance.clearance_id, content_clearance.territory_id", false );

			$this->db->join( "content_clearance", "content_clearance.content_id = content_film.content_id", "left" );

			$this->db->where( "content_clearance.clearance_id", $clearance_id );

			$content_query = $this->db->get( "content_film" )->row();

			## Content may not exists, so checking if can get any result
			if( !empty( $content_query ) ){
				## Checking if the airtime reference !empty
				
				if( !empty( $content_query->external_content_ref ) ){
					## This is an airtime content - need to delete availability windows
					
					## Get all the availability windows by the content and the territory
					$this->db->select( "*", false );
					
					$this->db->where( "availability_window.content_id", $content_query->content_id );
					$this->db->where( "availability_window.territory_id", $content_query->territory_id );
					
					$wind_arch_where 	= "( ( availability_window.archived IS NULL ) || ( availability_window.archived != 1 ) )";
					$this->db->where( $wind_arch_where );
					
					$aw_query 			= $this->db->get( "availability_window" );

					if( $aw_query->num_rows() > 0 ){
						
						$i 				= 0;
						$aw_result 	= $aw_query->result();
						foreach( $aw_result as $key => $row ){
							
							if( !empty( $row->easel_id ) ){
								
								## This check should sieve all the non-existing IDs
								$fetch_availability_window	= false;
								$fetch_availability_window 	= $this->easel_service->fetch_availability_window( $account_id, $row->easel_id );
								
								## If AW doesn't exists on Easel, we will allow deleting,
								## so DO NOT PREVENT from deleting: prevent_from_deleting = false
								if( ( $fetch_availability_window != false ) && ( !empty( $fetch_availability_window->id ) ) ){
									## Assuming only existing items goes there (the check has been performed above) 

									$delete_availability_window	= false;
									$delete_availability_window = $this->easel_service->delete_availability_window( $account_id, $row->easel_id ); 
log_message( "error", json_encode( ["Deleting Availability Window" => $delete_availability_window, "Easel_ID" => $row->easel_id, "CaCTI_ID" =>$row->window_id] ) );
									
									if( $delete_availability_window->success != true ){
										## so, as non existing items have been excluded above, only in a case of the issue/error we will get the status 'false' from the deleting the item
										$prevent_from_deleting = true;
										
										## I should preserve the message from Easel
										$message = ( !empty( $delete_availability_window->message ) ) ? $delete_availability_window->message : 'Error deleting an Availability Window on Easel ( ID:'.$fetch_availability_window->id.')' ;
										log_message( "error", json_encode( ["Error deleting Availability Window" => $delete_availability_window, "window_id" => $fetch_availability_window->id ] ) );
									} else {
										## It means we've deleted from Easel, now it is time to delete from availability_window table
										$this->db->delete( 'availability_window', ["window_id" => $row->window_id, "account_id" => $account_id] );
									}
								}
							}
						}
					} else {
						## No Availability Windows in the system - go to the next action - we can safely remove the clearance
					}
				} else {
					## Not an airtime content - go to the next action - we can safely remove the clearance
				}
			} else {
				## Content to this clearance date has not been found - go to the next action - we can safely remove the clearance
			}

			if( !$prevent_from_deleting ){

				## Deleting the Clearance date
				$this->db->delete( 'content_clearance', ["clearance_id" => $clearance_id, "account_id" => $account_id] );
				if( $this->db->affected_rows() > 0 ){

					$result = true;
					$this->session->set_flashdata( 'message', 'Clearance has been deleted.' );
				} else {
					$this->session->set_flashdata( 'message', $message );
				}
			} else {
				$this->session->set_flashdata( 'message', $message );
			}
		} else {
			$this->session->set_flashdata( 'message', 'No Account Id or Clearance ID supplied.' );
		}
		return $result;
	}


	/*
	*	Get the requested language text (title, description, tag line...)
	*/
	public function get_language_phrase( $account_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;

		if( !empty( $account_id ) ){

			$phrase_languages 	= $this->get_phrase_languages( $account_id );
			$phrase_types 		= $this->get_phrase_types( $account_id );

			if( !empty( $phrase_languages ) && !empty( $phrase_types ) ){
				## build an empty array:
				foreach( $phrase_languages as $pl_row ){
					$result[$pl_row->language_id]["language_name"] = $pl_row->language_name;
					$result[$pl_row->language_id]["language_symbol"] = $pl_row->language_symbol;
					foreach( $phrase_types as $pt_row ){
						$result[$pl_row->language_id]["language_content"][$pt_row->type_id]["type_name"] 	= $pt_row->type_name;
						$result[$pl_row->language_id]["language_content"][$pt_row->type_id]["type_content"] = false;
					}
				}

				$this->db->select( "clf.*", false );
				$this->db->select( "clfl.language_name, clfl.language_symbol", false );
				$this->db->select( "clft.type_name", false );
				$this->db->select( "concat( u1.first_name, ' ', u1.last_name ) created_by_full_name", false );

				$this->db->join( "content_language_phrase_language `clfl`", "clfl.language_id = clf.text_language_id", "left" );
				$this->db->join( "content_language_phrase_type `clft`", "clft.type_id = clf.text_type_id", "left" );
				$this->db->join( "user u1", "u1.id = clf.created_by", "left" );

				if( !empty( $where ) ){

    					$where = ( !is_array( $where ) ) ?  convert_to_array( $where ) : $where ;

					if( !empty( $where['content_id'] ) ){
						$content_id = $where['content_id'];
						$this->db->where( "clf.content_id", $content_id );
						unset( $where['content_id'] );

					} else if( !empty( $where['text_id'] ) ){
						$text_id = $where['text_id'];
						$this->db->where( "clf.text_id", $text_id );
						unset( $where['text_id'] );

					} else if( !empty( $where ) ){
						$this->db->where( $where );
					}
				}

				$arch_where = "( clf.archived != 1 or clf.archived is NULL )";
				$this->db->where( $arch_where );
				$this->db->where( "clf.active", 1 );

				$this->db->order_by( "clf.text_language_id ASC, clf.text_type_id ASC, clf.phrase ASC" );
				$query = $this->db->get( "content_language_phrase `clf`" );

				if( !empty( $query->num_rows() && $query->num_rows() > 0 ) ){

					$dataset = $query->result();

					if( !empty( $text_id ) ){
						$result = [];
						$result = $dataset[0];
					} else {
						## fill it with values
						foreach( $dataset as $row ){
							$result[$row->text_language_id]["language_content"][$row->text_type_id]["type_content"][] 	= $row;
						}
						$this->session->set_flashdata( 'message','Language text(s) found.' );
					}
				} else {
					$this->session->set_flashdata( 'message','Language text(s) not found.' );
				}

			} else {
				$this->session->set_flashdata( 'message','Languages or types not set.' );
			}

		} else {
			$this->session->set_flashdata( 'message','Account ID not supplied.' );
		}

		return $result;
	}


	/*
	*	Get the list of available languages for the phrases
	*/
	public function get_phrase_languages( $account_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;

		if( !empty( $account_id ) ){

			$this->db->select( "clfl.*", false );

			if( !empty( $where ) ){
				$where = convert_to_array( $where );

				if( !empty( $where['language_id'] ) ){
					$language_id = $where['language_id'];
					unset( $where['language_id'] );
					$this->db->where( "clfl.language_id", $language_id );

				} else if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}

			$arch_where = "( clfl.archived != 1 or clfl.archived is NULL )";
			$this->db->where( $arch_where );
			$this->db->where( "clfl.active", 1 );

			$this->db->order_by( "clfl.language_name ASC" );
			$query = $this->db->get( "content_language_phrase_language `clfl`" );

			if( !empty( $query->num_rows() && $query->num_rows() > 0 ) ){

				$dataset = $query->result();

				if( !empty( $language_id ) ){
					$result = $dataset[0];
				} else {
					$result	= $dataset;
				}
				$this->session->set_flashdata( 'message','Language(s) found.' );
			} else {
				$this->session->set_flashdata( 'message','Language(s) not found.' );
			}

		} else {
			$this->session->set_flashdata( 'message','Account ID not supplied.' );
		}

		return $result;
	}



	/*
	*	Get the list of the type of phrases
	*/
	public function get_phrase_types( $account_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;

		if( !empty( $account_id ) ){

			$this->db->select( "clpt.*", false );

			if( !empty( $where ) ){
				$where = convert_to_array( $where );

				if( !empty( $where['type_id'] ) ){
					$type_id = $where['type_id'];
					unset( $where['type_id'] );
					$this->db->where( "clpt.type_id", $type_id );

				} else if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}

			$arch_where = "( clpt.archived != 1 or clpt.archived is NULL )";
			$this->db->where( $arch_where );
			$this->db->where( "clpt.active", 1 );

			$this->db->order_by( "clpt.type_name DESC" );
			$query = $this->db->get( "content_language_phrase_type `clpt`" );

			if( !empty( $query->num_rows() && $query->num_rows() > 0 ) ){

				$dataset = $query->result();

				if( !empty( $type_id ) ){
					$result = $dataset[0];
				} else {
					$result	= $dataset;
				}
				$this->session->set_flashdata( 'message','Phrase type(s) found.' );
			} else {
				$this->session->set_flashdata( 'message','Phrase type(s) not found.' );
			}

		} else {
			$this->session->set_flashdata( 'message','Account ID not supplied.' );
		}

		return $result;
	}


	/*
	*	Function to process language phrase(s) - it could be update or create
	*/
	public function update_language_phrase( $account_id = false, $content_id = false, $phrases_data = false ){
		$result = false;
		if( !empty( $account_id ) ){
			if( !empty( $content_id ) ){
				if( !empty( $phrases_data ) ){

					$update_processed_rows = $insert_processed_rows = 0;

					$phrases_data = json_decode( $phrases_data );

					$i = 0;

					if( !empty( $phrases_data ) ){
						foreach( $phrases_data as $ph_row ){
							## if( !empty( $ph_row->phrase ) ){
								if( !empty( $ph_row->text_id ) ){
									$update_batch[$i] 						= $this->ssid_common->_filter_data( 'content_language_phrase', $ph_row );
									$update_batch[$i]['account_id']			= $account_id;
									$update_batch[$i]['last_modified_by']	= $this->ion_auth->_current_user->id;
								} else {
									$insert_batch[$i] 						= $this->ssid_common->_filter_data( 'content_language_phrase', $ph_row );
									$insert_batch[$i]['account_id']			= $account_id;
									$insert_batch[$i]['created_by']			= $this->ion_auth->_current_user->id;
								}
							## }
							$i++;
						}
					} else {
						$this->session->set_flashdata( 'message','Phrase(s) data not supplied or incorrect.' );
						return $result;
					}

					$database_failed = false;

					if( !empty( $update_batch ) ){
						$update_batch_counter 				= count( $update_batch );
						$this->db->update_batch( "content_language_phrase", $update_batch, "text_id" );
						if( $this->db->trans_status() === FALSE ){
							$database_failed = true;
						}
						$update_processed_rows = $this->db->affected_rows();
					}

					if( !empty( $insert_batch ) ){
						$insert_batch_counter = count( $insert_batch );
						$this->db->insert_batch( "content_language_phrase", $insert_batch );
						if( $this->db->trans_status() === FALSE ){
							$database_failed = true;
						}
						$insert_processed_rows = $this->db->affected_rows();
					}

					$result 	= $this->get_language_phrase( $account_id, ["content_id" => $content_id] );

					if( $database_failed ){
						$message 		= "Language unable to update.";
					} else {
						$message 		= "Language updated.";
					}

					$this->session->set_flashdata( 'message', $message );
				} else {
					$this->session->set_flashdata( 'message','Phrase(s) data not supplied.' );
				}
			} else {
				$this->session->set_flashdata( 'message','Content ID not supplied.' );
			}
		} else {
			$this->session->set_flashdata( 'message','Account ID not supplied.' );
		}

		return $result;
	}



	/*
	*	Function to generate the data export for one content item
	*/
	public function generate_file_export( $account_id = false, $content_id = false, $file_type = false ){
		$result = false;
		if( !empty( $account_id ) ){
			if( !empty( $content_id ) ){

				$content 	= $this->get_content( $account_id, $content_id );
				$sxe   		= new SimpleXMLElement( '<?xml version="1.0" encoding="UTF-8"?><metadata></metadata>', null, false );
				$sxe->addAttribute( 'id', $content->asset_code );
				$sxe->addAttribute( 'type', $content->type );
				$sxe->addAttribute( 'creator', $content->created_by_full_name );
				$sxe->addAttribute( 'created', $content->date_created );

				$release 		= $sxe->addChild( 'release', $content->release_date );

				$encoded_tagl	= false;
				$encoded_tagl 	= iconv( "UTF-8", "Windows-1252", utf8_encode( html_entity_decode( $content->tagline ) ) );
				$tagline 		= $sxe->addChild( 'tagline', $encoded_tagl );

				$year 			= $sxe->addChild( 'year', $content->release_year );
				$certificate 	= $sxe->addChild( 'certificate', $content->age_rating_name );
				$duration 		= $sxe->addChild( 'duration', $content->running_time );
				$imdb 			= $sxe->addChild( 'imdb', $content->imdb_link );
				$provider 		= $sxe->addChild( 'provider' );
				$provider->addChild( 'name', $content->provider_name );
				$provider->addChild( 'reference', $content->content_provider_reference_code );
				$images 		= $sxe->addChild( 'images' );

				## Fetch Movie Images
				$movie_images = $this->_fetch_movie_images( $account_id, $content_id );
				if( !empty( $movie_images ) ){
					foreach( $movie_images as $image_row ){
						#$image = $images->addChild( 'image', $image_row->image_url );
						$image = $images->addChild( 'image', $image_row->image_ref );
						$image->addAttribute( 'type', strtolower( $image_row->type ) );
						$image->addAttribute( 'height', $image_row->image_height );
						$image->addAttribute( 'width', $image_row->image_width );
						$image->addAttribute( 'mimetype', strtolower( $image_row->mimetype ) );
					}
				}

				$content_territories = $this->get_clearance( $account_id, false, ["content_id" => $content_id] );
				if( !empty( $content_territories ) ){
					$territories 	= $sxe->addChild( 'territories' );
					foreach( $content_territories as $c_ter_row ){
						$territory 	= $territories->addChild( 'territory' );
						$territory->addAttribute( 'id', strtolower( $c_ter_row->code ) );
						$territory->addAttribute( 'name', $c_ter_row->country );
						$encrypted 	= $territory->addChild( 'encrypted', '' );
						$encrypted 	= $territory->addChild( 'clear', $c_ter_row->clearance_start_date );
					}
				} else {
					$territories 	= $sxe->addChild( 'territories', '' );
				}

				if( !empty( $content->genre ) ){
					$genre_id 		= json_decode( $content->genre );
					$genres_set 	= $this->get_genres( $account_id, ["genre_id" => $genre_id] );

					if( !empty( $genres_set ) ){
						$genres 	= $sxe->addChild( 'genres' );
						foreach( $genres_set as $genre ){
							$genres->addChild( 'genre', ucfirst( $genre->genre_name ) );
						}
					} else {
						$genres 	= $sxe->addChild( 'genres', '' );
					}
				} else {
					$genres 	= $sxe->addChild( 'genres', '' );
				}

				$languages_set = $this->get_language_phrase( $account_id, ["content_id" => $content_id] );

				if( !empty( $languages_set ) ){

					$languages = $sxe->addChild( 'languages' );

					foreach( $languages_set as $l_key => $l_row ){

						$language 	= $languages->addChild( 'language' );
						$language->addAttribute( 'name', $l_row['language_name'] );
						$language->addAttribute( 'id', $l_row['language_symbol']  );

						if( !empty( $l_row['language_content'] ) ){ 					## I do have a language, just checking if there is a content for it
							foreach( $l_row['language_content'] as $l_type_row ){

								if( !empty( $l_type_row['type_content'] ) ){ 			## I do have a language, now checking any of phrase type for the language
									foreach( $l_type_row['type_content'] as $item ){ 	## looping through the types
										switch( $item->type_name ){						## for different types of the language
											case "title" :
												$encoded		= false;
												// $encoded 	= htmlspecialchars( $item->phrase, ENT_XML1, 'UTF-8', true );
												// $encoded 	= htmlentities( $director, ENT_XML1 );
												// $encoded 	= utf8_encode( html_entity_decode( $item->phrase ) );

												$encoded		= false;
												$encoded 		= @iconv( "UTF-8", "Windows-1252", utf8_encode( html_entity_decode( $item->phrase ) ) );

												$title 			= $language->addChild( "title", $encoded );
												break;

											case "tagline" :
												$encoded		= false;
												// $encoded 	= htmlspecialchars( $item->phrase, ENT_XML1, 'UTF-8', true );
												// $encoded 	= utf8_encode( html_entity_decode( $item->phrase ) );
												// $encoded 	= htmlentities( $director, ENT_XML1 );

												$encoded		= false;
												$encoded 		= @iconv( "UTF-8", "Windows-1252", utf8_encode( html_entity_decode( $item->phrase ) ) );
												$short 			= $language->addChild( "short", $encoded );
												break;

											case "synopsis" :
												$encoded		= false;
												// $encoded 	= htmlspecialchars( $item->phrase, ENT_XML1, 'UTF-8', true );
												// $encoded 	= utf8_encode( html_entity_decode( $item->phrase ) );
												// $encoded 	= htmlentities( $director, ENT_XML1 );

												$encoded		= false;
												$encoded 		= @iconv( "UTF-8", "Windows-1252", utf8_encode( html_entity_decode( $item->phrase ) ) );
												$full 			= $language->addChild( "full", $encoded );
												break;

											default:
												break;
										}
									}
								} else {

								}
							}

						## I don NOT have anything for this language!
						} else {
							$language->addChild( "title", '' );
							$language->addChild( "short", '' );
							$language->addChild( "full", '' );
						}

						$credits 	= $language->addChild( 'credits' );
						if( !empty( $content->director ) ){
							$directors 	= $credits->addChild( 'directors' );
							$directors_set = json_decode( $content->director );

							if( !empty( $directors_set ) ){
								foreach( $directors_set as $director ){
									$encoded	= false;
									// $encoded 	= htmlspecialchars( $director, ENT_XML1, 'UTF-8', true );
									// $encoded 	= utf8_encode( html_entity_decode( $director ) );
									// $encoded 	= htmlentities( $director, ENT_XML1 );

									$encoded	= false;
									$encoded 	= iconv( "UTF-8", "Windows-1252", utf8_encode( html_entity_decode( $director ) ) );
									$director 	= $directors->addChild( 'director', $encoded );
								}
							}
						} else {
							$directors 	= $credits->addChild( 'directors', '' );
						}

						if( !empty( $content->actors ) ){
							$actors 	= $credits->addChild( 'actors' );
							$actors_set = json_decode( $content->actors );

							if( !empty( $actors_set ) ){
								foreach( $actors_set as $actor ){
									$encoded	= false;
									// $encoded 	= htmlspecialchars( $actor, ENT_XML1, 'UTF-8', true );
									// $encoded 	= utf8_encode( html_entity_decode( $actor ) );
									// $encoded 	= htmlentities( $actor, ENT_XML1 );

									$encoded	= false;
									$encoded 	= iconv( "UTF-8", "Windows-1252", utf8_encode( html_entity_decode( $actor ) ) );
									$actor 	= $actors->addChild( 'actor', $encoded );
								}
							}
						} else {
							$actors 	= $credits->addChild( 'actors', '' );
						}
					}
				} else {
					$languages = $sxe->addChild( 'languages', '' );
				}

				$subtitles 		 = $sxe->addChild( 'subtitles' );
				## Fetch and Add Subtitle
				$movie_subtitles = $this->_fetch_movie_subtitles( $account_id, $content_id );
				if( !empty( $movie_subtitles ) ){
					foreach( $movie_subtitles as $subtitle_row ){
						$subtitle = $subtitles->addChild( 'subtitle', $subtitle_row->file_ref );
						$subtitle->addAttribute( 'language', strtolower( $subtitle_row->language ) );
					}
				}

				$assets 		= $sxe->addChild( 'assets', '' );
				## Fetch and Add Movie Files
				$movie_assets = $this->_fetch_movie_assets( $account_id, $content_id );
				if( !empty( $movie_assets ) ){
					foreach( $movie_assets as $movie_asset ){
						$asset = $assets->addChild( 'asset', '' );
							$asset->addAttribute( 'name', strtolower( $movie_asset->asset_name ) );
							$asset->addAttribute( 'class', strtolower( $movie_asset->asset_class ) );
							$asset->addAttribute( 'coding', strtolower( $movie_asset->asset_coding ) );

						if( !empty( $movie_asset->asset_streams ) ){
							foreach( $movie_asset->asset_streams as $asset_stream ){
								$stream = $asset->addChild( 'stream', '' );
								$stream->addAttribute( 'pid', strtolower( $asset_stream->pid ) );
								$stream->addAttribute( 'coding', strtolower( $asset_stream->stream_name ) );
								$stream->addAttribute( 'type', strtolower( $asset_stream->stream_type ) );

								##Switch over the types
								switch( strtolower( $asset_stream->stream_type ) ){
									case 'video':
										$stream->addChild( 'frame_size', $asset_stream->frame_size );
										$stream->addChild( 'aspect_ratio', $asset_stream->aspect_ratio );
										$stream->addChild( 'frame_rate', $asset_stream->frame_rate );
										$stream->addChild( 'encode_rate', $asset_stream->encode_rate );
										break;

									case 'audio':
										$stream->addChild( 'sample_rate', $asset_stream->sample_rate );
										$stream->addChild( 'channels', $asset_stream->channels );
										$stream->addChild( 'encode_rate', $asset_stream->encode_rate );
										$stream->addChild( 'language', $asset_stream->language );

										break;

									case 'subtitle':
										$stream->addChild( 'language', $asset_stream->language );
										break;
								}

							}
						}
					}
				}


				$xmlOutput = $sxe->saveXML();
				## End of the XML string creation


				## Output the files
				$xml = simplexml_load_string( $xmlOutput );
				## $xml2 = simplexml_load_string( $xmlOutput,'SimpleXMLElement', LIBXML_NOCDATA );

				if( $xml === false ){
					$error_string = "Failed loading XML\n";
					foreach( libxml_get_errors() as $error ){
						$error_string .= "\t" . $error->message;
					}
					$this->session->set_flashdata( 'message', $error_string );
					return false;
				}

				$jsonOutput = json_encode( $xml );
				## $array = json_decode( $json, TRUE );

				$document_path = '_account_assets/accounts/'.$account_id.'/content/'.$content_id.'/';
				$upload_path   = $this->app_root.$document_path;

				$result['document_location'] = $document_path;

				if( !is_dir( $upload_path ) ){
					if( !mkdir( $upload_path, 0755, true ) ){
						$this->session->set_flashdata( 'message', 'Error: Unable to create upload location' );
						return false;
					}
				}

				$export_name = "export_".$content_id;
				## Original name by Evident
				## $xml_file_name 			= 'XML_' . $export_name.'_'.date('Y-m-d').'.xml';
				## $xml_file_name 			= 'asset.xml';
				$xml_file_name 				= $content->asset_code.'.xml';

				## Original name by Evident
				## $json_file_name 		= 'JSON_' . $export_name.'_'.date('Y-m-d').'.json';
				## $json_file_name 		= 'asset.json';
				$json_file_name 		= $content->asset_code.'.json';

				$xml_file_path 			= $upload_path.$xml_file_name;
				$json_file_path 		= $upload_path.$json_file_name;

				$result['timestamp']	= date('d.m.Y H:i:s');

				if( $file_type == 'xml' && write_file( $upload_path.$xml_file_name, $xmlOutput ) ){
					$result['xml_file_name']		= $xml_file_name;
					$result['xml_file_path']		= $xml_file_path;
					$result['xml_file_link']		= base_url( $document_path.$xml_file_name );
				} else {
					$result['xml_file_name']		= false;
					$result['xml_file_path']		= false;
					$result['xml_file_link']		= false;
				}

				if(  $file_type == 'json' &&  write_file( $upload_path.$json_file_name, $jsonOutput ) ){
					$result['json_file_name']		= $json_file_name;
					$result['json_file_path']		= $json_file_path;
					$result['json_file_link']		= base_url( $document_path.$json_file_name );
				} else {
					$result['json_file_name']		= false;
					$result['json_file_path']		= false;
					$result['json_file_link']		= false;
				}

			} else {
				$this->session->set_flashdata( 'message','Content ID not supplied.' );
			}
		} else {
			$this->session->set_flashdata( 'message','Account ID not supplied.' );
		}

		return $result;
	}


	/*
	*	Decode streams information from the provided file path
	* 	(function name probably needs to be changed as the required action is to a) decode b) save against the content id
	*/
	public function decode_file_streams( $account_id = false, $content_id = false, $file_location = false){
		$result = $streams_verified = false;
		if( !empty( $account_id ) && !empty( $content_id ) && !empty( $file_location ) ){

			## 1. Check if file exists, read only allowed path, sanitize the file location  				// what if we want a couple locations?
			$ini_path 			= str_replace( '\\', '/', getcwd() );
			$file_path 			= PREP_FOLDER.( str_replace( '|_', ' ', filter_var( str_replace( ' ', '|_', $file_location ), FILTER_SANITIZE_URL ) ) );

			## 2. File exists, try to read
			if( file_exists( $file_path ) ){
				$streams_output = $this->get_video_info( $file_path, "array" );

				if( !empty( $streams_output ) ){


					## 2.5 An additional check the definition to pass it to stream verification
					$SD_max_bit_rate				= SD_MAX_BIT_RATE;
					## Determining the definition of the file: SD or HD
					##  - getting the 'sd' definition type id from the DB
					$file_def_id 		= $this->db->get_where( "content_format_codec_definition", ["account_id" => $account_id, "definition_group" => 'sd'], 1 )->row();
					$file_definition_id = ( !empty( $file_def_id->definition_id ) ) ? $file_def_id->definition_id : 2 ; ## SD

					if( $streams_output['format']['bit_rate'] > $SD_max_bit_rate ){
						##  - getting the 'hd' definition type id from the DB
						$file_def_id 		= $this->db->get_where( "content_format_codec_definition", ["account_id" => $account_id, "definition_group" => 'hd'], 1 )->row();
						$file_definition_id = ( !empty( $file_def_id->definition_id ) ) ? $file_def_id->definition_id : 1 ; ## HD
					}

					## 3. We do have a file, we do have some streams. Let's verified them against Provider technical specification first
					$streams_verified = $this->verify_streams( $account_id, $content_id, $streams_output['streams'], $file_definition_id );

					## I need a very good message from here
					if( !empty( $streams_verified ) ){

						## 4. Output exists, save the file into the database first
						$file_data 						= $streams_output['format'];
						$file_data['account_id'] 		= $account_id;
						$file_data['is_verified'] 		= 1;
						$file_data['content_id'] 		= $content_id;
						$file_data['file_short_name'] 	= basename( $file_data['filename'] );
						$file_data['created_by'] 		= $this->ion_auth->_current_user->id;
						$file_data['tags'] 				= ( !empty( $file_data['tags'] ) ) ? json_encode( $file_data['tags'] ) : NULL ;
						$file_data['file_definition_id']=$file_definition_id;

						$ext 							= pathinfo( $file_data['filename'], PATHINFO_EXTENSION );
						$new_file_name 					= $streams_verified['new_file_name'];

						$trailer_max_file_size 			= TRAILER_MAX_FILE_SIZE;
						$SD_max_bit_rate				= SD_MAX_BIT_RATE;

						## Determining the type of the file: movie or trailer
						##  - getting the 'movie' type id from the DB
						$movie_type_id = $this->db->get_where( "content_decoded_file_type", ["account_id" => $account_id, "type_group" => 'movie'], 1 )->row();
						$file_data['decoded_file_type_id'] = ( !empty( $movie_type_id->type_id ) ) ? $movie_type_id->type_id : 1 ; ## Film

						if( $file_data['size'] < $trailer_max_file_size ){
							if( strpos( strtolower( $file_data['file_short_name'] ), "trailer" ) !== false ){
								## - getting the 'trailer' type id from the DB
								$movie_type_id = $this->db->get_where( "content_decoded_file_type", ["account_id" => $account_id, "type_group" => 'trailer'], 1 )->row();
								$file_data['decoded_file_type_id'] = ( !empty( $movie_type_id->type_id ) ) ? $movie_type_id->type_id : 2 ; ## Trailer

								$new_file_name 	.= "_trailer";
							}
						}

						$file_data['file_new_name']		 = $new_file_name.".".$ext;


						## to clear all 'main_file' triggers for this content_id - 11, file_definition_id - 1 (sd/hd), decoded_file_type_id - 1 (main/trailer)
						$update_values = [
							"main_record" => 0
						];

						$update_conditions = [
							"account_id" 			=> $account_id,
							"content_id"			=> $content_id,
							"file_definition_id"	=> $file_data['file_definition_id'],
							"decoded_file_type_id"	=> $file_data['decoded_file_type_id'],
						];

						$this->db->update( 'content_decoded_file', $update_values, $update_conditions );


						$filtered_file_data = $this->ssid_common->_filter_data( 'content_decoded_file', $file_data );

						$this->db->insert( "content_decoded_file", $filtered_file_data );

						if( $this->db->trans_status() !== FALSE ){

							$decoded_file_id = $this->db->insert_id();

							## update the last ingestion date
							$conditions = [
								"account_id" => $account_id,
								"content_id" => $content_id,
							];

							$upd_data = [
								"last_ingestion_date" 	=> date( 'y-m-d' ),
								"modified_by"			=> $this->ion_auth->_current_user->id,
							];
							$this->db->update( "content", $upd_data, $conditions );
							## update the last ingestion date - end

							if( !empty( $decoded_file_id ) ){

								## 5. Save streams against files
								$saved_output = $this->save_decoded_output( $account_id, $decoded_file_id, $streams_output );

								## 6. If successful -> all streams decoded and saved
								if( !empty( $saved_output ) ){
									$temp_message 	= "All streams decoded from the movie file and saved.";
									$result 		= $saved_output;
								} else {
									$temp_message 	= $this->session->flashdata( 'message' );
								}
							} else {
								$temp_message 		= 'Error saving the file';
							}

							$content_details = $this->get_content( $account_id, $content_id );

							if( !empty( $content_details ) ){
								$asset_code 				= false;
								$provider_reference_code 	= false;
								$provider_reference_code 	= ( !empty( $content_details->provider_reference_code ) ) ? strtolower( $content_details->provider_reference_code ) : false ;
								$asset_code 				= ( !empty( $content_details->asset_code ) ) ? $content_details->asset_code : false ;

								$source_folder = PREP_FOLDER;

								if( !empty( $provider_reference_code ) && !empty( $asset_code )  ){
									$destination_folder = PROCESSED_FOLDER.$provider_reference_code.'/'.$asset_code.'/';
								} else {
									$temp_message 	.= " Error retrieving the Provider or Asset Reference Code. Attempting to saved the file in the main folder.";
									$destination_folder = PROCESSED_FOLDER;
								}

								# Check to see if "DestinationRoot" exists
								if( !is_dir( $destination_folder ) ){
									mkdir( $destination_folder, 0777, true );
								}

								## check the whole operation
								if( file_exists( $source_folder.$file_data['file_short_name'] ) && ( ( !file_exists( $destination_folder.$file_data['file_new_name'] ) ) || is_writable( $destination_folder.$file_data['file_new_name'] ) ) ){
									## transfer the file to the new destination
									rename( $source_folder.$file_data['file_short_name'], $destination_folder.$file_data['file_new_name'] );
									$temp_message 	.= " The file moved to the destination";

									$companion_file_data = [];
									$companion_file_data = [
										"assetcode"		=> $content_details->asset_code,
										"cactiAssetId"	=> $content_details->content_id,
										"cactiFileId"	=> $decoded_file_id,
										"fileName"		=> $file_data['file_new_name'],
										"provider"		=> $content_details->provider_reference_code,
									];

									$companion_file_name = $content_details->asset_code."-".$movie_type_id->type_group;
									$companion_file_data = json_encode( $companion_file_data );

									if( file_put_contents( $destination_folder.$companion_file_name.".json", $companion_file_data ) )
										$temp_message 	.= " A manifest file created successfully.";
									else
										$temp_message 	.= " Error creating a manifest file.";

								} else {
									$temp_message 	.= " Problem moving the file to the destination";
								}
							} else {
								$temp_message 	.= " Error retrieving the Content data";
							}

							$this->session->set_flashdata( 'message', $temp_message );

						} else {
							$this->session->set_flashdata( 'message', 'The movie file hasn\'t been saved' );
						}
					} else {
						$verification_message = ( !empty( $this->session->flashdata( 'ver_message' ) ) ) ? $this->session->flashdata( 'ver_message' ) : 'Streams verifications against provider specs failed' ;

						$this->session->set_flashdata( 'message', $verification_message );
					}
				} else {
					$this->session->set_flashdata( 'message', 'Error decoding the file' );
				}
			} else {
				$this->session->set_flashdata( 'message', 'File doesn\'t exist in allowed location'.$file_path );
			}
		} else {
			$this->session->set_flashdata( 'message', 'Required data not provided' );
		}

		return $result;
	}


	/*
	*	Function to decode streams from the movie file
	*/
	private function get_video_info( $video_file_location = false, $output_type = "json" ){
		$result 		= false;
		$ffmpeg_output 	= false;
		if( !empty( $video_file_location ) ){

			## set local variables
			setlocale( LC_CTYPE, "en_GB.UTF-8" );

			$path 				= getcwd();
			$ffprobe_path 		= $path.'\assets\ffmpeg\ffprobe.exe';
			$ffmpeg_path 		= $path.'\assets\ffmpeg\ffmpeg.exe';

			## validate user input
			$video 	= escapeshellcmd( escapeshellarg( $video_file_location ) );

			## prepare the ffprobe command
			$ffprobe_cmd 	=  $ffprobe_path . " -v quiet -print_format json -show_format -show_streams " . $video . " 2>&1";

			## start processing
			ob_start();
				passthru( $ffprobe_cmd );
				$ffmpeg_output = ob_get_contents();
			ob_end_clean();

			## if file found just return values
			if( !empty( $ffmpeg_output ) ){
				switch( $output_type ){
					case "array" :
						$result = json_decode( $ffmpeg_output, true );
						break;
					case "object" :
						$result = ( object ) json_decode( $ffmpeg_output );
						break;
					default:
						$result = $ffmpeg_output;
				}
			}
		}
		return $result;
	}

	/*
	*	Function to save ONLY streams information against the file. The file itself should be already saved against the content_id
	*/
	public function save_decoded_output( $account_id = false, $decoded_file_id = false, $streams_output = false ){
		$result = false;

		if( !empty( $account_id ) && !empty( $decoded_file_id ) && !empty( $streams_output ) ){

			if( !empty( $streams_output['streams'] ) ){

				$codec_types = $this->provider_service->get_codec_type( $account_id );

				foreach( $streams_output['streams'] as $stream ){
					$new_output_data 			= [];
					$stream['account_id']		= $account_id;
					$stream['decoded_file_id']	= $decoded_file_id;
					$stream['created_by']		= $this->ion_auth->_current_user->id;

					if( !empty( $stream['disposition'] ) ){
						foreach( $stream['disposition'] as $disp_key => $disp ){
							if( $disp_key == "default" ){
								$stream['disposition_default'] = $disp;
							} else {
								$stream[$disp_key] = $disp;
							}
						}
					}

					if( !empty( $stream['tags'] ) ){
						foreach( $stream['tags'] as $tags_key => $tag ){
							$stream[$tags_key] = $tag;
						}
					}

					$stream['codec_type_id'] = NULL;

					foreach( $codec_types as $c_row ){
						if( strtolower( $c_row->type_name ) == strtolower( $stream['codec_type'] ) ){
							$stream['codec_type_id'] = $c_row->type_id;
						}
					}

					$stream['language_id'] 		= NULL;

					if( !empty( $stream['language'] ) ){
						$this->db->select( "clpl.*", false );
						$this->db->where( "clpl.language_symbol", $stream['language'] );
						$this->db->or_where( "clpl.audio_code", $stream['language'] );
						$this->db->or_where( "clpl.subtitle_code", $stream['language'] );
						$this->db->or_where( "clpl.abbreviation", $stream['language'] );
						$this->db->or_where( "clpl.abbreviation_2", $stream['language'] );
						$language_row = $this->db->get( "content_language_phrase_language `clpl`", 1, 0 )->row();
						if( !empty( $language_row ) ){
							$stream['language_id'] = $language_row->language_id;
						}
					}

					$new_output_data 		= $this->ssid_common->_filter_data( 'content_decoded_stream', $stream );
					$this->db->insert( "content_decoded_stream", $new_output_data );

					if( $this->db->affected_rows() > 0 ){
						$successfull[] = $this->db->get_where( "content_decoded_stream", ["account_id" => $account_id, "decoded_file_id" => $decoded_file_id ] )->row();
					} else {
						$this->session->set_flashdata( 'message', 'Saving streams information process failed' );
					}
				}

				if( ( count( $streams_output['streams'] ) > 0 ) && ( count( $streams_output['streams'] ) == count( $successfull ) ) ){
					$result = $successfull;
					$this->session->set_flashdata( 'message', 'Streams successfully saved' );
				} else {
					$this->session->set_flashdata( 'message', 'At least on of the streams hasn\'t been saved' );
				}
			} else {
				$this->session->set_flashdata( 'message', 'Streams data not available' );
			}
		} else {
			$this->session->set_flashdata( 'message', 'Required data not provided' );
		}
		return $result;
	}



	/*
	*	The functions implied stream(s) to be linked to the file, so it always relays on file id which is linked to the content_id
	*/
	public function get_decoded_file_streams( $account_id = false, $where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;

		if( !empty( $account_id ) ){

			if( !empty( $where ) ){
				## deal with 'where' first

				$where = convert_to_array( $where );
				if( !empty( $where ) ){
					## we do expect as possible options to be mentioned:
					## - by the file id
					## - or by the content id

					## get file id(s) for this specific content/file
					$this->db->select( "cdf.*", false );
					## $this->db->select( "cdf.file_id, cdf.content_id, cdf.filename, cdf.file_short_name, cdf.format_name, cdf.format_long_name, cdf.size, cdf.created_by, cdf.created_date, cdf.modified_by, cdf.last_modified_date", false );
					$this->db->select( "cfcd.definition_name, cfcd.definition_group", false );
					$this->db->select( "cdft.type_name, cdft.type_group, cdft.type_alt_name", false );

					if( !empty( $where['content_id'] ) ){
						$content_id = $where['content_id'];
						$this->db->where( "content_id", $content_id );
						unset( $where['content_id'] );
					}

					if( !empty( $where['file_id'] ) ){
						$file_id = $where['file_id'];
						$this->db->where( "file_id", $file_id );
						unset( $where['file_id'] );
					}

					if( !empty( $where ) ){
						$this->db->where( $where );
						unset( $where );
					}

					$this->db->join( "content_format_codec_definition `cfcd`", "cfcd.definition_id=cdf.file_definition_id", "left" );
					$this->db->join( "content_decoded_file_type `cdft`", "cdft.type_id=cdf.decoded_file_type_id", "left" );

					$arch_where = "( cdf.archived != 1 or cdf.archived is NULL )";
					$this->db->where( $arch_where );
					$this->db->where( "cdf.active", 1 );
					$this->db->where( "cdf.is_verified", 1 );

					$this->db->order_by( "cdf.file_id DESC" );

					$query = $this->db->get( "content_decoded_file `cdf`", $limit, $offset );

					if( $query->num_rows() > 0 ){
						foreach( $query->result() as $row ){
							$streams = $this->get_decoded_stream( $account_id, ["decoded_file_id" => $row->file_id] );
							if( !empty( $streams ) ){
								$row->streams = $streams;
							} else {
								$row->streams = NULL;
							}
							$all_streams[] = $row;
						}

						if( !empty( $all_streams ) ){
							$result = $all_streams;
							$this->session->set_flashdata( 'message', 'Stream(s) found' );
						} else {
							$this->session->set_flashdata( 'message', 'Stream(s) not found' );
						}
					} else {
						$this->session->set_flashdata( 'message', 'File(s) for this Content not found' );
					}
				} else {
					$this->session->set_flashdata( 'message', 'Error processing additional conditions' );
				}

			} else {
				## give all streams
				$all_streams = $this->get_decoded_stream( $account_id );
				if( !empty( $all_streams ) ){
					$result = $all_streams;
					$this->session->set_flashdata( 'message', 'Stream(s) found' );
				} else {
					$this->session->set_flashdata( 'message', 'Stream(s) not found' );
				}
			}
		} else {
			$this->session->set_flashdata( 'message', 'Account ID not supplied.' );
		}

		return $result;
	}


	/*
	*	The functions output decoded streams
	*/
	public function get_decoded_stream( $account_id = false, $where = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;

		if( !empty( $account_id ) ){
			$this->db->select( "cds.stream_id, cds.account_id, cds.decoded_file_id, cds.codec_name, cds.codec_long_name, cds.profile, cds.codec_type_id, cds.codec_type `original_codec_type`, cds.codec_time_base, cds.codec_tag_string, cds.codec_tag, cds.width, cds.height, cds.sample_aspect_ratio, cds.display_aspect_ratio, cds.bit_rate, cds.profile, cds.level, cds.r_frame_rate, cds.avg_frame_rate, cds.id, cds.language, cds.created_by, cds.created_date, cds.modified_by, cds.last_modified_date, cds.channels, cds.channel_layout, cds.sample_rate", false );
			$this->db->select( "cfct.type_name `codec_type_name`, cfct.type_group `codec_type_group`, cfct.type_alt_name `codec_type_alt_name`", false );
			$this->db->select( "clpl.language_name, clpl.language_desc, clpl.language_symbol, clpl.audio_code, clpl.subtitle_code, clpl.abbreviation, clpl.abbreviation_2,", false );

			$this->db->join( "content_format_codec_type `cfct`", "cfct.type_id=cds.codec_type_id", "left" );
			$this->db->join( "content_language_phrase_language `clpl`", "clpl.language_id=cds.language_id", "left" );

			$arch_where = "( cds.archived != 1 or cds.archived is NULL )";
			$this->db->where( $arch_where );
			$this->db->where( "cds.active", 1 );

			$this->db->where( "cds.account_id", $account_id );

			if( !empty( $where ) ){

				$where = convert_to_array( $where );

				if( !empty( $where ) ){
					if( !empty( $where['decoded_file_id'] ) ){
						$decoded_file_id = $where['decoded_file_id'];
						$this->db->where_in( "cds.decoded_file_id", $decoded_file_id );
						unset( $where['decoded_file_id'] );
					}

					if( !empty( $where['stream_id'] ) ){
						$stream_id = $where['stream_id'];
						$this->db->where_in( "cds.stream_id", $stream_id );
						unset( $where['stream_id'] );
					}

					if( !empty( $where ) ){
						$this->db->where( $where );
					}
				}
			}

			$query = $this->db->get( "content_decoded_stream `cds`", $limit, $offset );

			if( !empty( $query->num_rows() && $query->num_rows() > 0 ) ){
				$dataset = $query->result();

				if( !empty( $type_id ) ){
					$result = $dataset[0];
				} else {
					$result	= $dataset;
				}
				$this->session->set_flashdata( 'message','Stream(s) found.' );
			} else {
				$this->session->set_flashdata( 'message','Stream(s) not found.' );
			}

		} else {
			$this->session->set_flashdata( 'message','Account ID not supplied.' );
		}

		return $result;
	}



	public function add_territory( $account_id = false, $territory_data = false  ){
		$result = false;

		if( !empty( $account_id ) ){
			if( !empty( $territory_data ) ){

				$territory_data = convert_to_array( $territory_data );

				if( !empty( $territory_data ) ){

					if( !empty( $territory_data['country'] ) ){

						$data_set = [
							"account_id	" 	=> $account_id,
							"created_by"	=> $this->ion_auth->_current_user->id,
							"country"		=> $territory_data['country'],
							"code"			=> $territory_data['code'],
						];

						$this->db->insert( "content_territory", $data_set );

						if( $this->db->affected_rows() > 0 ){
							$insert_id 	= $this->db->insert_id();
							$result		= $this->db->get_where( "content_territory", ["account_id"=>$account_id, "territory_id"=>$insert_id] )->row();
							$this->session->set_flashdata( 'message','The new Territory has been added' );
						} else {
							$this->session->set_flashdata( 'message','There was an error processing your request' );
						}
					} else {
						$this->session->set_flashdata( 'message','No Territory name supplied' );
					}

				} else {
					$this->session->set_flashdata( 'message','There was an error processing the data' );
				}

			} else {
				$this->session->set_flashdata( 'message','No Territory Data supplied.' );
			}
		} else {
			$this->session->set_flashdata( 'message','No Account ID supplied.' );
		}

		return $result;

	}



	/*
	* 	Delete Territory
	*/
	public function delete_territory( $account_id = false, $territory_id = false ){
		$result = false;
		if( !empty( $account_id )  && !empty( $territory_id ) ){

			$data = [
				"archived" 		=> 1,
				"active"		=> 0,
				"modified_by"	=> $this->ion_auth->_current_user->id,
			];

			$d_content_data 	= $this->ssid_common->_filter_data( 'content_territory', $data );
			$this->db->update( 'content_territory', $d_content_data, ["territory_id" => $territory_id, "account_id" => $account_id] );

			if( $this->db->affected_rows() > 0 ){
				$result = true;
				$this->session->set_flashdata( 'message', 'Territory record has been deleted.' );
			} else {
				$this->session->set_flashdata( 'message', 'Territory record hasn\'t been deleted.' );
			}

		} else {
			$this->session->set_flashdata( 'message', 'No Account ID or Territory ID supplied.' );
		}
		return $result;
	}




	/*
	* 	Update Territory record
	*/
	public function update_territory( $account_id = false, $territory_id = false, $territory_data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $territory_id ) && !empty( $territory_data ) ){
			$check_territory = $this->db->get_where( 'content_territory', ['account_id'=>$account_id, 'territory_id'=>$territory_id] )->row();
			if( !empty( $check_territory ) ){

				$territory_data = convert_to_array( $territory_data );

				$data = [];
				if( !empty( $territory_data ) ){

					foreach( $territory_data as $key => $value ){
						if( in_array( $key, format_name_columns() ) ){
							$value = format_name( $value );
						} elseif( in_array( $key, format_email_columns() ) ){
							$value = format_email( $value );
						} elseif( in_array( $key, format_number_columns() ) ){
							$value = format_number( $value );
						} elseif ( in_array( $key, format_boolean_columns() ) ){
							$value = format_boolean( $value );
						} elseif ( in_array( $key, format_date_columns() ) ){
							$value = format_date_db( $value );
						} elseif( in_array( $key, format_long_date_columns() ) ){
							$value = format_datetime_db( $value );
						} elseif( in_array( $key, string_to_json_columns() ) ){
							$value = string_to_json( $value );
						} else {
							$value = trim( $value );
						}
						$data[$key] = $value;
					}

					if( !empty( $data ) ){
						$data['modified_by']		= $this->ion_auth->_current_user->id;
						$update_data 				= $this->ssid_common->_filter_data( 'content_territory', $data );

						$this->db->update( "content_territory", $update_data, ['account_id' => $account_id, 'territory_id' => $territory_id] );

						if( $this->db->affected_rows() > 0 ){
							$result = $this->get_territories( $account_id, $territory_id );
							$this->session->set_flashdata( 'message', 'Territory record updated successfully.' );
						} else {
							$this->session->set_flashdata( 'message', 'No changes been made to the Territory.' );
						}
					}  else {
						$this->session->set_flashdata( 'message', 'There was an error processing your data.' );
					}
				} else {
					$this->session->set_flashdata( 'message', 'There was an error processing the data.' );
				}

			} else {
				$this->session->set_flashdata( 'message','Foreign territory record. Access denied.' );
			}
		} else {
			$this->session->set_flashdata( 'message','No territory data supplied.' );
		}
		return $result;
	}

	/*
	*	The function to verify the movie file streams against the provider technical specifications
	*	Taken the file info, streams and desired content_id
	*/
	public function verify_streams( $account_id = false, $content_id = false, $streams_output = false, $file_definition_id = false ){
		$result = false;
		if( !empty( $account_id ) ){
			if( !empty( $content_id ) ){
				if( !empty( $streams_output ) ){

					$ts 			= "ts";
					$separator 		= "_";
					$file_name		= "";

					## I've got everything
					## get the provider specifications
					$this->db->select( "content.content_provider_id, content_film.asset_code" );
					$this->db->join( "content_provider", "content_provider.provider_id = content.content_provider_id", "left" );
					$this->db->join( "content_film", "content_film.content_id = content.content_id", "left" );
					$this->db->where( "content.account_id", $account_id );
					$this->db->where( "content.content_id", $content_id );
					$arch_where = "( content_provider.archived != 1 or content_provider.archived is NULL )";
					$this->db->where( $arch_where );
					$content_query = $this->db->get( "content" )->row();

					## need the asset code to rename the file
					if( !empty( $content_query->asset_code ) ){
						## preparing the name without extension
						$file_name		= $content_query->asset_code;
					} else {
						$this->session->set_flashdata( 'ver_message','No Asset Code has been specified for the Asset' );
						return $result;
					}

					if( !empty( $content_query->content_provider_id ) ){
						$packet_identifiers_backup = $packet_identifiers = [];


						if( !empty( $file_definition_id ) ){
							$definition_id = $file_definition_id;
						} else {
							$file_def_id 		= $this->db->get_where( "content_format_codec_definition", ["account_id" => $account_id, "definition_group" => 'sd'], 1 )->row();
							$definition_id 		= ( !empty( $file_def_id->definition_id ) ) ? $file_def_id->definition_id : 2 ; ## SD
						}

						## provider technical specifications:
						$packet_identifiers_backup = $packet_identifiers = $this->provider_service->get_provider_packet_identifiers( $account_id, ["provider_id" =>$content_query->content_provider_id, "definition_id" => $definition_id ] );

						if( !empty( $packet_identifiers ) ){

							## Initial values for each stream:
							$message 					= "";
							$correct_identifiers 		= [];
							$incorrect_stream		= [];
							$additional_streams			= [];

							foreach( $streams_output as $str_key => $stream ){

								if( $stream['codec_type'] == "video" ){

									## Check if exists the correct identifier with the same codec name and codec id
									foreach( $packet_identifiers as $key => $packet ){
										$is_correct_identifier = false;

										## For the video type of the codec
										if( !empty( $packet->type_name ) && ( $packet->type_name == "video" ) ){
											if( !empty( $stream['codec_name'] ) && !empty( $packet->short_name ) ){
												if ( $stream['codec_name'] == $packet->short_name ){
													if( !empty( $stream['id'] ) && !empty( $packet->hex_id ) && ( $stream['id'] == $packet->hex_id ) ){
														$is_correct_identifier = true;
														$correct_identifiers[$packet->identifier_id] = $packet;
														$message .= "Stream verified - Codec Name and Hex PID consistent. ";
														unset( $packet_identifiers[$key] );
														unset( $streams_output[$str_key] );

													} else {
														$message .= "Error. Inconsistent Codec Name with Hex PID. Expected PID: $packet->hex_id, received: ".$stream['id'].". ";
														$incorrect_stream[$str_key] = $stream;
														unset( $streams_output[$str_key] );
														## $incorrect_stream[$packet->identifier_id] = $packet;
													}
													$message .= "Details: codec type(".$stream['codec_type']."), codec name( ".$stream['codec_name']."), ID(".$stream['id']."), <br />";
												} else {
													## I do have the video type of the codec, I do have the name of the codec and it isn't the same as the one from the packet identifiers
													## it is just in a different order - not checked now
													## $additional_streams[] = $stream;
												}
											} else {
												## one of guys is missing, can't compare. Usually it will be missing from the stream.
											}
										}
									}

								} else if( $stream['codec_type'] == "audio" ){
									## Check if exists the correct identifier with the same codec name and codec id
									foreach( $packet_identifiers as $key => $packet ){
										$is_correct_identifier = false;

										## For the audio type of the codec
										if( !empty( $packet->type_name ) && ( $packet->type_name == "audio" ) ){
											if( !empty( $stream['codec_name'] ) && !empty( $packet->short_name ) ){
												if( $stream['codec_name'] == $packet->short_name ){
													## codec name match the stream and the packet identifier

													if( !empty( $stream['id'] ) && !empty( $packet->hex_id ) ){
														if( $stream['id'] == $packet->hex_id ){

															## Build languages (abbreviations) table and check if the ['tags']['language'] information is within it
															$language_table = [];
															$language_table = array_filter( [$packet->language_symbol, $packet->audio_code, $packet->subtitle_code, $packet->abbreviation, $packet->abbreviation_2] );

															if( !empty( $stream['tags']['language'] ) && !empty( $language_table ) && ( in_array( $stream['tags']['language'],$language_table ) ) ){
																$is_correct_identifier = true;
																$correct_identifiers[$packet->identifier_id] = $packet;
																$file_name .= $separator.$packet->audio_code;
																$message .= "The language info and the Hex PID is correct. ";
																unset( $packet_identifiers[$key] );
																unset( $streams_output[$str_key] );
															} else {

																$message .= "Missing or Invalid Language Abbreviation: '".$stream['tags']['language']."'";
																$incorrect_stream[$str_key] = $stream;
																unset( $streams_output[$str_key] );
																## $incorrect_stream[$packet->identifier_id] = $packet;
															}
															$message .= "Details: codec type(".$stream['codec_type']."), codec name( ".$stream['codec_name']."), ID(".$stream['id']."), <br />";
														} else {
															## I've got the type: audio, the codec name match the stream codec name, but hex id from the stream does not match the one from the packets
														}
													} else {
														## I've got the type: audio, the codec name match the stream codec name, but hex id is missing from the stream or from the packets
													}
												} else {
													## I've got an audio stream which name does not match the one from the packet identifiers
													## $additional_streams[] = $stream;
												}
											} else {
												## one is missing: from the stream or from the packet identifiers - can't compare
											}
										}
									}
								} else if( $stream['codec_type'] == "subtitle" ){

									## Check if exists the correct identifier with the same codec name and codec id
									foreach( $packet_identifiers as $key => $packet ){
										$is_correct_identifier = false;


										if( !empty( $packet->type_name ) && ( $packet->type_name == "subtitle" ) ){
											if( !empty( $stream['codec_name'] ) && !empty( $packet->short_name ) ){
												if( $stream['codec_name'] == $packet->short_name ){



													if( !empty( $stream['id'] ) && !empty( $packet->hex_id ) && ( $stream['id'] == $packet->hex_id ) ){
														## Build languages (abbreviations) table and check if the ['tags']['language'] information is within it
														$language_table = [];
														$language_table = array_filter( [$packet->language_symbol, $packet->audio_code, $packet->subtitle_code, $packet->abbreviation, $packet->abbreviation_2] );

														if( !empty( $stream['tags']['language'] ) && !empty( $language_table ) && ( in_array( $stream['tags']['language'],$language_table ) ) ){
															$is_correct_identifier = true;
															$correct_identifiers[$packet->identifier_id] = $packet;
															$file_name 	.= $separator.$packet->subtitle_code;
															$message 	.= "The language info and the Hex PID is correct. ";
															unset( $packet_identifiers[$key] );
															unset( $streams_output[$str_key] );
														} else {
															$message 	.= "Missing or Invalid Language Abbreviation: '".$stream['tags']['language']."'";
															## $message 	.= "Invalid Hex PID for this language. Expected PID: $packet->hex_id, received: ".$stream['id'].". ";
															$incorrect_stream[$str_key] = $stream;
															unset( $streams_output[$str_key] );
															## $incorrect_stream[$packet->identifier_id] = $packet;
														}
														$message .= "Details: codec type(".$stream['codec_type']."), codec name( ".$stream['codec_name']."), ID(".$stream['id']."), <br />";
													} else {
														## I've got the type: audio, the codec name match the stream codec name, but hex id from the stream does not match the one from the packets
													}
												} else {
													## I've got the type: audio, the codec name match the stream codec name, but hex id is missing from the stream or from the packets
												}
											} else {
												## I've got an audio stream which name does not match the one from the packet identifiers
												## $additional_streams[] = $stream;
											}
										} else {
											## one is missing: from the stream or from the packet identifiers - can't compare
										}
									}
								} else {
									$message .= "Unknown Codec Type";
								}
							}

							## Missing Packet Identifiers - just info needed
							if( !empty( $packet_identifiers ) ){
								$message .= "<br />Missing Languages: ";
								foreach( $packet_identifiers as $packet ){
									$message .= " <br />PID: ".$packet->pid."; <br />Codec Name: ".$packet->short_name.";  <br />Codec Type: ".$packet->type_name.";  <br />Language: ".$packet->language_name."<br />";
									## $message .= "Details: codec type(".$stream['codec_type']."), codec name( ".$stream['codec_name']."), ID(".$stream['id']."), <br />";
								}
							}


							## Whats left from streams - additional streams
							if( !empty( $streams_output ) ){
								foreach( $streams_output as $str_OUT_key => $ad_stream ){
									if( !empty( $ad_stream['tags']['language'] ) && !empty( $ad_stream['id'] ) ){

										## to verify the additional stream
										$lang_query = false;

										$this->db->select( "packet_identifiers.*, clpl.*", false );
										$this->db->where( "packet_identifiers.hex_id", $ad_stream['id'] );

										$this->db->join( "content_language_phrase_language `clpl`", "clpl.language_id=packet_identifiers.language_id", "left" );

										$where = '( ( clpl.audio_code = "'.$ad_stream['tags']['language'].'" ) OR ( clpl.subtitle_code = "'.$ad_stream['tags']['language'].'" ) OR ( clpl.abbreviation = "'.$ad_stream['tags']['language'].'" ) OR ( clpl.abbreviation_2 = "'.$ad_stream['tags']['language'].'" ) )';
										$this->db->where( $where );

										$lang_query = $this->db->get( "packet_identifiers" )->row();

										if( !empty( $lang_query ) ){
											switch( $ad_stream['codec_type'] ){
												case 'audio' :
													$file_name .= $separator.$lang_query->audio_code;
												break;

												case 'subtitle' :
													$file_name .= $separator.$lang_query->subtitle_code;
												break;

												default:
													$file_name .= $separator.$lang_query->language_symbol;
											}

											$message .= "<br />Additional stream: ";
											$message .= " <br />PID: ".$ad_stream['id']."; <br />Codec Name: ".$ad_stream['codec_name'].";  <br />Codec Type: ".$ad_stream['codec_type']."; ";
											if( !empty( $ad_stream['tags']['language'] ) ){
												$message .= " <br />Language: ".$ad_stream['tags']['language']."<br />";
											}
										} else {
											$incorrect_stream[$str_OUT_key] = $ad_stream;
											unset( $streams_output[$str_OUT_key] );

											$message .= "<br />Incorrect additional stream: ";
											$message .= " <br />PID: ".$ad_stream['id']."; <br />Codec Name: ".$ad_stream['codec_name'].";  <br />Codec Type: ".$ad_stream['codec_type']."; ";
											if( !empty( $ad_stream['tags']['language'] ) ){
												$message .= " <br />Language: ".$ad_stream['tags']['language']."<br />";
											}
										}
									}
								}
							}

							if( !empty( $incorrect_stream ) ){
								## There was an error - returning an empty object with some message

							} else {
								## No errors
								if( !empty( $correct_identifiers ) ){
									$result['correct_identifiers'] 	= $correct_identifiers;
								}

								if( !empty( $packet_identifiers ) ){
									$result['missing_identifiers'] 	= $packet_identifiers; ## missing languages
								}

								if( !empty( $streams_output ) ){
									$result['streams_output'] 		= $streams_output;
								}

								if( !empty( $file_name ) ){
									$result['new_file_name'] 		= $file_name.$separator.$ts;
								}
							}

							$this->session->set_flashdata( 'ver_message', $message );

						} else {
							$this->session->set_flashdata( 'ver_message','No Technical Specification for this Provider' );
						}

					} else {
						$this->session->set_flashdata( 'ver_message','No Provider specified for this content' );
					}
				} else {
					$this->session->set_flashdata( 'ver_message','No Streams data provided' );
				}
			} else {
				$this->session->set_flashdata( 'ver_message','No Content ID provided' );
			}
		} else {
			$this->session->set_flashdata( 'ver_message','No Account ID supplied' );
		}
		return $result;
	}


	/*
	*	Distribution Content
	*/
	public function distribution_content( $account_id = false, $content_id = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;
		if( !empty( $account_id ) ){

			$where = $raw_where = convert_to_array( $where );

			$this->db->select( 'content.*, content_film.*, content_provider.*, content_clearance.clearance_start_date `clearance_date`', false );
			$this->db->select( 'age_rating.age_rating_name, age_rating.age_rating_desc', false );

			$this->db->join( 'content_film','content_film.content_id = content.content_id','left' );
			$this->db->join( 'content_provider','content_provider.provider_id = content.content_provider_id','left' );
			$this->db->join( 'age_rating', 'age_rating.age_rating_id = content_film.age_rating_id', 'left' );
			$this->db->join( 'content_clearance', 'content_clearance.content_id = content_film.content_id', 'left' );

			$this->db->where( 'content.account_id', $account_id );
			$this->db->where( 'content.is_content_active', 1 );

			$arch_where = "( content.archived != 1 or content.archived is NULL )";
			$this->db->where( $arch_where );

			if( !empty( $content_id ) ){
				$row = $this->db->get_where( 'content',['content.content_id'=>$content_id] )->row();

				if( !empty( $row ) ){
					$film_attributes 			= $this->_fetch_content_attributes( $account_id, $row->film_id, $row, $raw_where );
					$row->film_attributes		= !empty( $film_attributes ) ? $film_attributes : null;
					$this->session->set_flashdata('message','Content data found');
					return $row;
				} else {
					$this->session->set_flashdata('message','No data found');
					return false;
				}

				return $row;
			}

			if( isset( $where['territory_id'] ) ){
				if( !empty( $where['territory_id'] ) ){
					$this->db->where_in( 'content_clearance.territory_id', $where['territory_id'] );
				}
				unset( $where['territory_id'] );
			}

			if( isset( $where['content_provider'] ) || isset( $where['provider_id'] ) ){

				$content_provider 	= !empty( $where['content_provider'] ) ? $where['content_provider'] : ( !empty( $where['provider_id'] ) ? $where['provider_id'] : '' );
				$provider_ids		= is_array( $content_provider ) ? $content_provider : [ $content_provider ];

				if( !empty( $provider_ids ) ){
					$this->db->where_in( 'content_provider.provider_id', $provider_ids );
				}
				unset( $where['content_provider'], $where['provider_id'] );
			}

			if( isset( $where['include_languages'] ) ){
				if( !empty( $where['include_languages'] ) ){
					$include_languages = true;
				}
				unset( $where['include_languages'] );
			}

			if( isset( $where['distribution_group_id'] ) ){
				if( !empty( $where['distribution_group_id'] ) ){
					//Do nothing for now
				}
				unset( $where['distribution_group_id'] );
			}

			if( isset( $where['distribution_bundle_id'] ) ){
				if( !empty( $where['distribution_bundle_id'] ) ){
					//Do nothing for now
				}
				unset( $where['distribution_bundle_id'] );
			}

			if( isset( $where['content_in_use'] ) ){
				if( !empty( $where['content_in_use'] ) ){
					//Do nothing for now
				}
				unset( $where['content_in_use'] );
			}

			if( !empty( $where ) ){
				$this->db->where( $where );
			}

			if( $order_by ){
				$this->db->order_by( $order_by );
			} else {
				$this->db->order_by( 'content_provider.provider_name, content_clearance.clearance_start_date DESC' );
			}

			if( $limit > 0 ){
				$this->db->limit( $limit, $offset );
			}

			$query = $this->db->group_by( 'content.content_id' )
				->get( 'content' );

			if( $query->num_rows() > 0 ){
				$data = $data_grouping = [];
				foreach( $query->result() as $key => $row ){
					$film_attributes 			= $this->_fetch_content_attributes( $account_id, $row->film_id, $row, $raw_where );
					$row->film_attributes		= !empty( $film_attributes ) ? $film_attributes : null;
					$group_color 				= !empty( $row->film_attributes->content_group_color ) ? $row->film_attributes->content_group_color : 'green';
					$data_grouping[$group_color][$key] = $row;
					#$data[$key] = $row;
				}

				## Re-order Results
				$data += !empty( $data_grouping['green'] ) 	? $data_grouping['green'] 	: $data;
				$data += !empty( $data_grouping['blue'] ) 	? $data_grouping['blue'] 	: $data;
				$data += !empty( $data_grouping['red'] ) 	? $data_grouping['red'] 	: $data;
				$data += !empty( $data_grouping['orange'] ) ? $data_grouping['orange'] 	: $data;

				$result = $data;
				$this->session->set_flashdata( 'message','Content data found.' );
			} else {
				$this->session->set_flashdata( 'message','No records found matching your criteria.' );
			}
		}

		return $result;
	}


	/** Fetch the Code Definition of a Film **/
	private function _fetch_codec_meta_data( $account_id = false, $content_id = false, $film_name = false, $type = 'film' ){
		$data = (object)[
			'codec_definition'	 => '',
			'content_languages'	 => null
		];

		if( !empty( $account_id ) && !empty( $content_id ) ){

			if( $type == 'trailer' ){
				//This needs to be reviewed to check if a Content Item can be identified as being a Trailer or May Film
				$this->db->where( 'cdf.decoded_file_type_id', 2 );
			} else {
				$this->db->where( 'cdf.decoded_file_type_id', 1 );
			}

			$film_file = $this->db->select( 'cdf.file_short_name, cdf.file_new_name, cdf.file_definition_id, cdft.definition_name, cdf.decoded_file_type_id', false )
				->where( 'cdf.content_id', $content_id )
				->join( 'content_format_codec_definition cdft', 'cdft.definition_id = cdf.file_definition_id', 'left' )
				->order_by( 'cdf.file_id DESC' )
				->limit(1)
				->get( 'content_decoded_file cdf' )
				->row();

			if( !empty( $film_file ) ){

				if( !empty( $film_file->file_new_name ) ){
					$data->codec_definition = !empty( $film_file->definition_name ) ? strtoupper( $film_file->definition_name ) : '';
					$file_name_contents = explode( strtolower( $film_name ), $film_file->file_new_name );
					if( !empty( $file_name_contents[1] ) ){
						$languages_list = explode( '.', $file_name_contents[1] );
						if( !empty( $languages_list[0] ) ){
							$languages 					= explode( '_', $languages_list[0] );
							$data->content_languages 	= !empty( $languages ) ? array_filter( array_map( 'strtoupper', $languages ) ) : [];;
						}
					}
				}

				$result = $data;

			}

		}
		return $data;
	}


	/** Fetch the Code Definition of a Film **/
	public function _fetch_content_attributes( $account_id = false, $content_id = false, $film_obj = false, $where = false ){

		if( !empty( $account_id ) ){

			$data = (object)[
				'content_id'	 	 => null,
				'codec_definition'	 => null,
				'content_languages'	 => null,
				'content_group'		 => 'Latest',
				'content_group_class'=> 'latest-film',
				'content_group_color'=> '#99cc99',
				'content_in_use'	 => 0,
				'license_start_date' => null,
				'removal_date'	 	 => null,
			];

			if( !empty( $content_id ) && !empty( $film_obj ) ){
				$film_obj	 = is_array( $film_obj ) ? array_to_object( $film_obj ) : $film_obj;
			} else if( !empty( $content_id ) ){
				$film_obj	= $this->db->get_where( 'content_film', [ 'content_id'=>$content_id ] )->row();
			}

			if( !empty( $film_obj ) ){

				$data->content_id			= $film_obj->content_id;
				## Code Data
				$codec_meta_data 			= $this->_fetch_codec_meta_data( $account_id, $film_obj->content_id, $film_obj->asset_code );
				$data->codec_definition		= !empty( $codec_meta_data->codec_definition ) ? $codec_meta_data->codec_definition : null;
				$data->content_languages	= !empty( $codec_meta_data->content_languages ) ? $codec_meta_data->content_languages : null;

				## Film distribution data
				$content_usage_data 		= $this->_fetch_content_usage_data( $account_id, $film_obj, $where );
				$data->content_group		= !empty( $content_usage_data->content_group ) ? $content_usage_data->content_group : '';
				$data->content_group_class	= !empty( $content_usage_data->content_group_class ) ? $content_usage_data->content_group_class : '';
				$data->content_group_color	= !empty( $content_usage_data->content_group_color ) ? $content_usage_data->content_group_color : '';
				$data->content_in_use		= !empty( $content_usage_data->content_in_use ) ? $content_usage_data->content_in_use : 0;
				$data->license_start_date	= !empty( $content_usage_data->license_start_date ) ? $content_usage_data->license_start_date : null;
				$data->removal_date			= !empty( $content_usage_data->removal_date ) ? $content_usage_data->removal_date : null;

			}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}

		return $data;
	}



	/** Content Usage Data **/
	private function _fetch_content_usage_data( $account_id = false, $film_obj = false, $where = false ){
		$result = (object)[
			'content_id'	 	 => $film_obj->content_id,
			'content_group'		 => 'Current',
			'content_group_class'=> 'current-film',
			'content_group_color'=> 'green',
			'content_in_use'	 => '0',
			'license_start_date' => null,
			'removal_date'	 	 => null
		];
		if( !empty( $account_id ) && !empty( $film_obj->content_id ) ){

			$where 					= convert_to_array( $where );

			$distribution_group_id	= !empty( $where['distribution_group_id'] ) ? $where['distribution_group_id'] : false;

			if( !empty( $distribution_group_id ) ){
				$this->db->where( 'db.distribution_group_id', $distribution_group_id );
			}

			$aging_period		= '18'; //18 Months
			#$aging_period_days	= '547'; //18 Months in days

			$query = $this->db->select( 'db.distribution_bundle, dbc.*', false )
				->join( 'distribution_bundles db', 'db.distribution_bundle_id = dbc.distribution_bundle_id ', 'left' )
				->where( 'db.account_id', $account_id )
				->where( 'dbc.content_id', $film_obj->content_id )
				->order_by( 'dbc.bundle_content_id DESC' )
				->get( 'distribution_bundle_content dbc' );

			if( $query->num_rows() ){

				foreach( $query->result() as $k => $row ){

					if( !empty( $row->license_start_date ) ){

						## CONTENT SENT | IN USE
						if( ( $row->content_in_use == 1 ) ){
							$result->content_group 			= 'Latest';
							$result->content_group_class 	= 'latest-film';
							$result->content_group_color 	= 'blue';

						} else {

							$date_today 		= date( 'Y-m-d' );
							#$license_date 		= date( 'Y-m-d', strtotime( $row->license_start_date ) );
							$license_check_date = valid_date( $row->removal_date ) ? date( 'Y-m-d', strtotime( $row->removal_date ) ) : date( 'Y-m-d', strtotime( $row->license_start_date ) );
							$elaspsed_months	= $this->_number_of_months( $license_check_date, $date_today );

							if( ( $row->content_in_use != 1 ) && ( $elaspsed_months >= 0 && $elaspsed_months <= $aging_period ) ){
								$result->content_group 			= 'Library';
								$result->content_group_class 	= 'library-film-red';
								$result->content_group_color 	= 'red';

							} else if( ( $row->content_in_use != 1 ) && ( $elaspsed_months > $aging_period ) ){
								$result->content_group 			= 'Library';
								$result->content_group_class 	= 'library-film-orange';
								$result->content_group_color 	= 'orange';
							}

						}

						$result->content_in_use 		= $row->content_in_use;
						$result->license_start_date 	= $row->license_start_date;
						$result->removal_date 			= $row->removal_date;

					}

				}
			}

		}
		return $result;
	}

	/** Get Number of Months between 2 dates **/
	public function _number_of_months( $date1, $date2 ){
		$d1 			= new DateTime( $date2 );
		$d2 			= new DateTime( $date1 );
		$months 		= $d2->diff( $d1 );
		$months_since 	= ( ( $months->y ) * 12 ) + ( $months->m );
		return $months_since;
	}

	/**
	* Fetch Movie Images (
	**/
	public function _fetch_movie_images( $account_id = false, $content_id = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $content_id ) ){
			$content_file_types = [ 'hero', 'standard', 'thumbnail', 'landscape' ];
			$query = $this->db
				->where( 'account_id', $account_id )
				->where( 'content_id', $content_id )
				->where( '( archived != 1 OR archived is NULL )' )
				->where_in( 'doc_file_type', $content_file_types )
				->get_where( 'content_document_uploads' );
			if( $query->num_rows() > 0 ){
				$data = [];
				foreach( $query->result() as $k => $row ){

					$file_name 			 = $this->app_root.$row->document_location;
					$reversed_file_parts = explode( '.', strrev( $file_name ), 2 );
					$file_ext 	 		 = strrev( $reversed_file_parts[0] );

					if( is_file( $file_name ) ){

						$imagesize = getimagesize( $file_name );
						$image_width	= !empty( $imagesize[0] ) ? $imagesize[0] : '600';
						$image_height	= !empty( $imagesize[1] ) ? $imagesize[1] : '400';
					} else {
						$image_width	= '600';
						$image_height	= '400';
					}

					if( !empty( $row->doc_file_type ) ){
						$data[$row->doc_file_type] = (object)[
							'type'			=> strtolower( $row->doc_file_type ),
							'mimetype'		=> 'image/'.$file_ext,
							'image_name'	=> $row->document_name,
							'image_ref'		=> strtolower( $row->doc_reference ),
							'image_path'	=> $row->document_location,
							'image_url'		=> $row->document_link,
							'image_width'	=> $image_width,
							'image_height'	=> $image_height,
						];
					} else {
						$data['other'][$row->document_id] = (object)[
							'type'			=> 	'other',
							'mimetype'		=> 'image/'.$file_ext,
							'image_name'	=> $row->document_name,
							'image_ref'		=> strtolower( $row->doc_reference ),
							'image_path'	=> $row->document_location,
							'image_url'		=> $row->document_link,
							'image_width'	=> $image_width,
							'image_height'	=> $image_height,
						];
					}
				}
				$result = $data;
			}

		}
		return $result;
	}


	/**
	* Fetch Movie Subtitles (
	**/
	public function _fetch_movie_subtitles( $account_id = false, $content_id = false, $where = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $content_id ) ){

			if( !empty( $where ) ){
				$where = convert_to_array( $where );

				if( !empty( $where ) ){

					if( !empty( $where['document_id'] ) ){
						$document_id = $where['document_id'];
						$this->db->where( "document_id", $document_id );
						unset( $where['document_id'] );
					}

					if( !empty( $where ) ){
						$where = $this->db->where( $where );
					}
				}
			}

			$content_file_types = [ 'vtt', 'subtitles' ];
			$query = $this->db
				->where( 'account_id', $account_id )
				->where( 'content_id', $content_id )
				->where( '( archived != 1 OR archived is NULL )' )
				->where_in( 'doc_file_type', $content_file_types )
				->get_where( 'content_document_uploads' );

			if( $query->num_rows() > 0 ){
				$data = [];
				foreach( $query->result() as $k => $row ){

					$file_name 		= $this->app_root.$row->document_location;
					$reversed_ext 	= explode( '.', strrev( $file_name ), 2 );
					$file_ext 	 	= strrev( $reversed_ext[0] );

					$reversed_lang 	= explode( '_', strrev( $file_name ), 2 );
					$file_language 	= explode( '.', strrev( $reversed_lang[0] ) );
					$file_language 	= !empty( $file_language[0] ) ? $file_language[0] : 'unknown';

					if( !empty( $row->doc_file_type ) ){
						$data[$file_language] = (object)[
							'language'		=> strtolower( $file_language ),
							'language_short'=> strtolower( $file_language ),
							'mimetype'		=> 'image/'.$file_ext,
							'file_name'		=> $row->document_name,
							'file_ref'		=> strtolower( $row->doc_reference ),
							'file_path'		=> $row->document_location,
							'file_url'		=> $row->document_link
						];
					} else {
						$data[$file_language][$row->document_id] = (object)[
							'language'		=> strtolower( $file_language ),
							'language_short'=> strtolower( $file_language ),
							'mimetype'		=> 'image/'.$file_ext,
							'file_name'		=> $row->document_name,
							'file_ref'		=> strtolower( $row->doc_reference ),
							'file_path'		=> $row->document_location,
							'file_url'		=> $row->document_link
						];
					}
				}
				$result = $data;
			}

		}
		return $result;
	}


	/**
	* Fetch Movie Assets (main file and/or trailer) (
	**/
	public function _fetch_movie_assets( $account_id = false, $content_id = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $content_id ) ){
			$query = $this->db->select( 'cp.provider_name, cp.provider_reference_code, cf.asset_code, cf.title, cdft.type_name,  cdft.type_group, cfcd.definition_name, cdf.*', false )
				->join( 'content_decoded_file_type cdft', 'cdf.decoded_file_type_id = cdft.type_id', 'left' )
				->join( 'content_format_codec_definition cfcd', 'cdf.file_definition_id = cfcd.definition_id', 'left' )
				->join( 'content_film cf', 'cf.content_id = cdf.content_id', 'left' )
				->join( 'content', 'content.content_id = cdf.content_id', 'left' )
				->join( 'content_provider cp', 'cp.provider_id = content.content_provider_id', 'left' )
				->where( 'cdf.account_id', $account_id )
				->where( 'cdf.content_id', $content_id )
				->where( '( cdf.archived != 1 OR cdf.archived is NULL )' )
				->where( 'cdf.main_record', 1 )
				->get_where( 'content_decoded_file cdf' );

			if( $query->num_rows() > 0 ){
				$data = [];
				foreach( $query->result() as $k => $row ){

					// $cds_pickup_location = CDS_PICKUP_LOCATION . strtolower( $row->provider_reference_code ).'/'. strtolower( $row->asset_code ).'/';
					$cds_pickup_location = CDS_PICKUP_LOCATION .'/'. strtolower( $row->provider_reference_code ).'/'. strtolower( $row->asset_code ).'/';
					$file_name			 = $row->file_new_name;

					// if( !is_dir( $cds_pickup_location ) ){
						// $this->session->set_flashdata( 'message', 'CDS Pickup location "'.$cds_pickup_location.'" is not available!' );
						// #$data['errors']['invalid-pickup-location'][] = $cds_pickup_location;
					// }

					$file_url	= $cds_pickup_location.$file_name;

					// if( is_file( $file_url ) ){
						$class 			= ( strtolower( $row->type_group ) == 'trailer' ) ? 'trailer' : 'film';
						$asset_name 	= !empty( $row->file_new_name ) ? strtolower( $row->file_new_name ) : ( !empty( $row->file_short_name ) ? strtolower( $row->file_short_name ) : 'ERROR_FILE_NAME_NOT_SET_OR_MISSING' );
						$query_streams	= $this->db->select( 'cdf.*, pids.pid, identifier_name, clp.language_name, clp.language_symbol, cfdt.type_name `codec_type`', false )
							->join( 'packet_identifiers pids', 'pids.hex_id = cdf.id', 'left' )
							->join( 'content_language_phrase_language clp', 'clp.language_id = pids.language_id', 'left' )
							->join( 'content_format_codec_type cfdt', 'cfdt.type_id = pids.codec_type_id', 'left' )
							->get_where( 'content_decoded_stream cdf', [ 'cdf.account_id'=> $account_id, 'cdf.decoded_file_id'=>$row->file_id ] );

						$asset_Streams 	= [];
						if( $query_streams->num_rows() > 0 ){
							foreach( $query_streams->result() as $stream ){

								$codec_name = !empty( $stream->codec_long_name ) ? ( explode( ' ', $stream->codec_long_name ) ) : '';
								$codec_name = !empty( $codec_name[0] ) ?  $codec_name[0] : '';

								$frame_rate = !empty( $stream->avg_frame_rate ) ? $stream->avg_frame_rate : ( !empty( $stream->r_frame_rate ) ? $stream->r_frame_rate: '' );
								$frame_rate = !empty( $frame_rate ) ? ( explode( '/', $frame_rate ) ) : '';
								$frame_rate = !empty( $frame_rate[0] ) ?  $frame_rate[0] : '';

								$asset_Streams[] = (object) [
									'pid'			=> $stream->pid,
									'stream_name'	=> !empty( $codec_name ) ? strtolower( str_replace( '-', '', $codec_name ) ) : '',
									'stream_type'	=> $stream->codec_type,
									'language'		=> $stream->language_symbol,
									'encode_rate'	=> !empty( $stream->bit_rate ) ? ( $stream->bit_rate/1000 ) : '',

									## For Video PIDS
									'frame_size'	=> ( !empty( $stream->width ) && !empty( $stream->height ) ) ? ( $stream->width.'x'.$stream->height ) : '',
									'frame_rate'	=> $frame_rate,
									'aspect_ratio'	=> !empty( $stream->display_aspect_ratio ) ? $stream->display_aspect_ratio : '',

									## For Audio PIDS
									'channels'		=> $stream->channels,
									'sample_rate'	=> $stream->sample_rate,
								];

							}
						}

						$data[$row->type_group] = (object)[
							'asset_group'		=> strtolower( $row->type_group ),
							'asset_class'		=> $class,
							'asset_type'		=> 'transport',
							'asset_size'		=> $row->size,
							'asset_coding'		=> 'mpeg2',
							'asset_name'		=> $asset_name,
							'asset_quality'		=> strtolower( $row->definition_name ),
							'asset_location'	=> strtolower( $row->filename ),
							'asset_name_md5'	=> md5( $asset_name ),
							'asset_program_id'	=> $row->nb_programs,
							'no_of_streams'		=> $row->nb_streams,
							'asset_streams'		=> $asset_Streams,
						];
					// } else {
						$this->session->set_flashdata( 'message', 'Some files were not found!' );
						#$data['errors']['files-not-found'][] = $file_url;
					// }
				}
				$result = $data;
			}

		}
		return $result;
	}


	/**
	* 	Get list of Availability Windows based on clearance territory for given content id.
	*	This is used to synchronize Availability Windows after adding the new territory(ies)
	**/
	public function synchronize_availability_windows( $account_id = false, $content_id = false, $where = false ){

		$result = false;
		if( !empty( $account_id ) && !empty( $content_id ) ){

			$movie_data = false;
			$movie_data = $this->get_content( $account_id, $content_id );
			if( !empty( $movie_data ) ){
				if( !empty( $movie_data->is_airtime_asset ) && $movie_data->is_airtime_asset == true && !empty( $movie_data->external_content_ref ) ){

					// Start with the provider
					if( !empty( $movie_data->content_provider_id ) ){
						$provider_id = $movie_data->content_provider_id;

						// I need to know what clearances / territories are for this content
						$content_clearances = $this->get_clearance( $account_id, false, ["content_id" => $content_id] );

						if( !empty( $content_clearances ) ){

							// Build a list of territories
							$territory_ids = array_values( array_column( $content_clearances, "territory_id"  ) );

							// Search site id's by given territories
							$this->db->select( "site.site_id", false );
							$this->db->where_in( "content_territory_id", $territory_ids );
							$query_1 = $this->db->get( "site" );

							if( $query_1->num_rows() > 0 ){

								$result_1 = $query_1->result();
								$site_ids = array_values( array_column( $result_1, "site_id"  ) );

								// Give me the products / Price plans
								$this->db->select( "*", false );
								$this->db->select( "site.site_name", false );

								$this->db->join( "site", "site.site_id = product.site_id", "left" );
								$this->db->join( "setting", "setting.setting_id = product.product_type_id", "left" );

								$query_where = 'LOWER( setting.setting_value ) = "airtime" '; //  $this->db->where( "product_type_id", 71 );

								$this->db->where( $query_where );
								$this->db->where( "product.active", 1 );
								$this->db->where_in( "product.site_id", $site_ids );
								$query_2 = $this->db->get( "product" );
								if( $query_2->num_rows() > 0 ){

									$to_be_created = [];

									foreach( $query_2->result() as $product ){
										$product->external_content_ref = $movie_data->external_content_ref;

										if( !( $product->airtime_segment_ref ) || empty( $product->airtime_segment_ref ) ){

											// create segment and update product object
											$segment_data 			= [
												'name'			=> ( !empty( $product->site_name ) ) ? ( trim( $product->site_name ).( ( isset( $product->is_airtime_ftg ) && !empty( $product->is_airtime_ftg ) ) ? '_VIP' : '' ).'-segment' ) : ( ( !empty( $product->product_name ) ) ? $product->product_name.'-segment' : 'Segment Default Name' ),
												'description'	=> ( !empty( $product->product_description ) ) ? html_escape( trim( $product->product_description ) ) : '',
												'type'			=> 'device',
												'pin' 			=> ( !empty( $product->airtime_pin ) ) ? ( int ) trim( $product->airtime_pin ) : '',
											];

											$easel_created_segment 	= $this->easel_service->create_segment( $account_id, $segment_data ); ## what if pin already exists / taken?

											 // create segment in CaCTi
											if( $easel_created_segment->success == true && !empty( $easel_created_segment->data->id ) ){
												$segment_cacti_data_message 				= '';
												$segment_cacti_data 						= json_decode( json_encode( $segment_data ) );
												$segment_cacti_data->product_id				= $product->product_id;
												$segment_cacti_data->airtime_segment_ref	= $easel_created_segment->data->id;
												$segment_cacti_data->created_by				= $this->ion_auth->_current_user->id;
												$query = $this->db->insert( "segment", $segment_cacti_data );

												if( $this->db->trans_status() !== FALSE ){
													$segment_cacti_data_message .= "Created Segment in CaCTi";
												} else {
													$segment_cacti_data_message .= "Cannot create a Segment in CaCTi";
												}
												// create segment in cacti - END


												// Update product with the segment ID
												$update_message 		= '';
												$product_update_data 	= [
													"airtime_segment_ref" 	=> $easel_created_segment->data->id,
												];

												$where_upd = [];
												$where_upd = [
													"account_id" 		=> $account_id,
													"product_id"		=> $product->product_id,
												];

												$upd_query = $this->db->update( "product", $product_update_data, $where_upd );

												if( $this->db->affected_rows() >0 ){
													$product->airtime_segment_ref = $easel_created_segment->data->id;
													$update_message .= 'The product profile has been updated with Easel Segment ID. ';
												} else {
													$update_message .= 'No new changes have been applied to the Product profile. ';
												}
											}

										}

										if( !( $product->airtime_market_ref ) || empty( $product->airtime_market_ref ) ){
											// create market and update object
											$market_data 				= [
												'name'			=> ( !empty( $product->site_name ) ) ? trim( $product->site_name ).( ( isset( $product->is_airtime_ftg ) && !empty( $product->is_airtime_ftg ) ) ? '_VIP' : '' ).'-market' : 'Market Default Name' ,
												'description'	=> ( !empty( $product->product_description ) ) ? trim( $product->product_description ) : '',
												'ordering'		=> ( !empty( $product->ordering ) ) ? ( ( int ) $product->ordering ) : '99',
											];

											if( !empty( $easel_created_segment->data->id ) ){
												$market_data['expression']['segmentId'] = $easel_created_segment->data->id;
											}

											$easel_created_market 		= $this->easel_service->create_market( $account_id, $market_data );

											$update_message = '';
											if( $easel_created_market->success == true && !empty( $easel_created_market->data->id ) ){

												// Update Product
												$product_update_data = [
													"airtime_market_ref" 	=> $easel_created_market->data->id,
												];

												$where_upd = [];
												$where_upd = [
													"account_id" 		=> $account_id,
													"product_id"		=> $product->product_id,
												];

												$upd_query = $this->db->update( "product", $product_update_data, $where_upd );

												if( $this->db->affected_rows() >0 ){
													$product->airtime_market_ref = $easel_created_market->data->id;
													$update_message .= 'The product profile has been updated with Easel Market ID. ';
												} else {
													$update_message .= 'No new changes have been applied to the Product profile. ';
												}


												// check if site needs to be updated
												if( !( $product->is_airtime_active ) ){

													## Update Site
													$site_update_data = [
														"is_airtime_active" 	=> 1,
														"last_modified_by" 		=> $this->ion_auth->_current_user->id,
													];

													$where_upd = [];
													$where_upd = [
														"account_id" 		=> $account_id,
														"site_id"			=> $product->site_id,
													];

													$site_upd_query = $this->db->update( "site", $site_update_data, $where_upd );

													if( $this->db->affected_rows() > 0 ){
														$update_message .= 'The site profile has been updated to Easel Active. ';
													} else {
														$update_message .= 'No new changes have been applied to the Site profile. ';
													}
												}
											}
										}

										$this->db->select( "product_price_plan.*", false );
										$this->db->select( "site.content_territory_id, site.site_reference_code", false );
										$this->db->select( "price_plan.price_plan_name, price_plan.start_period, price_plan.end_period, price_plan.price_plan_type", false );
										$this->db->select( "content_provider.provider_name", false );
										$this->db->select( "product_currency.setting_value `product_currency`", false );

										$this->db->join( "product", "product_price_plan.product_id = product.product_id", "left" );
										$this->db->join( "site", "site.site_id=product.site_id", "left" );
										$this->db->join( "price_plan", "product_price_plan.price_plan_id=price_plan.plan_id", "left" );
										$this->db->join( "content_provider", "product_price_plan.provider_id = content_provider.provider_id", "left" );
										$this->db->join( "setting `product_currency`", "product_currency.setting_id = product.sale_currency_id", "left" );

										## freshly added - to restrict the amount of AW - create only for the same content provider as the product price plan provider
										$this->db->where( 'product_price_plan.provider_id', $provider_id );

										$this->db->where( "product_price_plan.active", 1 );
										$this->db->where_in( "product_price_plan.product_id ", $product->product_id );
										$query_3 = $this->db->get( "product_price_plan" );
										if( $query_3->num_rows() > 0 ){
											$product->price_plans = $query_3->result();

											foreach( $product->price_plans as $plan ){

												// I do have segment, market
												if( !empty( $plan->easel_price_band_ref ) ){
													// ...and the price band

													// I need to check if the AW with this configuration exists in local table - less important - more important is EASEL
													// yes, but... if it IS in the local table it means it it definitely on Easel, isn't?
													$check_data = [
														"easel_productId" 	=> $movie_data->external_content_ref,
														"easel_priceBandId" => $plan->easel_price_band_ref,
														"active"			=> 1
													];
													$query_4 = $this->db->get_where( "availability_window", $check_data );

													if( $query_4->num_rows() > 0 ){
														// if exists then what?
														// it means we've got this window, we do not have to do anything apart from checking
													} else {
														// it means that we do not have the windows with this configuration. We need to create it

														$this->db->select( "clearance_start_date", false );

														$clearance_where = [
															"content_id"	=> $movie_data->content_id,
															"territory_id"	=> $plan->content_territory_id
														];

														$query_5 = $this->db->get_where( "content_clearance", $clearance_where );

														if( $query_5->num_rows() > 0 ){
															$clearance_date = false;
															$clearance_date = $query_5->result()[0]->clearance_start_date;

															$visible_From 	= date( 'Y-m-d', strtotime( $clearance_date. " +$plan->start_period months" ) );
															$visible_To 	= date( 'Y-m-d', strtotime( $clearance_date. " +$plan->end_period months -1 day" ) );

															$aw_data_set 	= false;
															$aw_data_set 	= [
																"account_id" 			=> $account_id,
																"territory_id" 			=> $plan->content_territory_id,
																"content_id" 			=> $movie_data->content_id,
																"product_id" 			=> $product->product_id,
																"product_price_plan_id" => $plan->product_price_plan_id,
																"site_id" 				=> $product->site_id,
																"productId" 			=> $movie_data->external_content_ref,
																"visibleFrom" 			=> convert_date_to_iso8601( $visible_From ),
																"visibleTo" 			=> convert_date_to_iso8601( $visible_To ),
																"priceBandId" 			=> $plan->easel_price_band_ref,
																"marketId" 				=> $product->airtime_market_ref,
																"billing"				=> [
																	"category"		=> ( strpos( strtolower( $plan->price_plan_name ), "premium") !== false ) ? "Premium" : ( ( strpos( strtolower( $plan->price_plan_name ), "current") !== false ) ? "Current" : "Library" ),
																],
															];

															$to_be_created[] = $aw_data_set;
														}
													}

												} else {
													// for this particular plan I do not have the price-band created
													// create a price band
													$price_band_name 	= "";
													$separator			= "_";
													$price_band_name 	.= ( !empty( $plan->site_reference_code ) ) ? html_escape( $plan->site_reference_code ): 'site_ref' ;
													$price_band_name 	.= $separator;
													$price_band_name 	.= ( !empty( $plan->provider_name ) ) ? html_escape( $plan->provider_name ) : 'provider_ref' ;
													$price_band_name 	.= $separator;
													$price_band_name 	.= ( !empty( $plan->price_plan_name ) ) ? html_escape( $plan->price_plan_name ) : 'price_plan' ;

													$price_band_name 	.= "(".( ( !empty( $plan->plan_price ) ) ? intval( number_format( $plan->plan_price * 100, 0, null, '' ) ) : '0' ).")";

													$price_band_data 	= [
														'title'		=> ( !empty( $price_band_name ) ) ? ( html_escape( $price_band_name ) ) : '',
														'value'		=> ( !empty( $plan->plan_price ) ) ? ( $plan->plan_price ) : '0',
														'currency'	=> ( !empty( $plan->product_currency ) ) ? ( $plan->product_currency ) : 'GBP',
													];
													$easel_price_band 	= $this->easel_service->create_price_band( $account_id, $price_band_data );

													if( !empty( $easel_price_band->data->id ) ){
														## update plan on cacti
														$price_band_upd_data = [
															"easel_price_band_ref" 	=> $easel_price_band->data->id,
															"modified_by" 			=> $this->ion_auth->_current_user->id,
														];

														$where_upd = [
															"account_id" 			=> $account_id,
															"product_price_plan_id"	=> $plan->product_price_plan_id
														];
														$this->db->update( "product_price_plan", $price_band_upd_data, $where_upd );
														$plan->easel_price_band_ref = $easel_price_band->data->id;
													}

													$check_data = [
														"easel_productId" 	=> $movie_data->external_content_ref,
														"easel_priceBandId" => $plan->easel_price_band_ref,
														"active"			=> 1
													];
													$query_4 = $this->db->get_where( "availability_window", $check_data );

													if( $query_4->num_rows() > 0 ){
														// if exists then what?
														// it means we've got this window, we do not have to do anything apart from checking

													} else {
														// it means that we do not have the windows with this configuration. We need to create it
														// we have to build a data set and send to create function

														$this->db->select( "clearance_start_date", false );

														$clearance_where = [
															"content_id"	=> $movie_data->content_id,
															"territory_id"	=> $plan->content_territory_id
														];

														$query_5 = $this->db->get_where( "content_clearance", $clearance_where );

														if( $query_5->num_rows() > 0 ){
															$clearance_date = false;
															$clearance_date = $query_5->result()[0]->clearance_start_date;

															$visible_From 	= date( 'Y-m-d', strtotime( $clearance_date. " +$plan->start_period months" ) );
															$visible_To 	= date( 'Y-m-d', strtotime( $clearance_date. " +$plan->end_period months -1 day" ) );

															$aw_data_set = false;
															$aw_data_set = [
																"account_id" 			=> $account_id,
																"territory_id" 			=> $plan->content_territory_id,
																"content_id" 			=> $movie_data->content_id,
																"product_id" 			=> $product->product_id,
																"product_price_plan_id" => $plan->product_price_plan_id,
																"site_id" 				=> $product->site_id,
																"productId" 			=> $movie_data->external_content_ref,
																"visibleFrom" 			=> convert_date_to_iso8601( $visible_From ),
																"visibleTo" 			=> convert_date_to_iso8601( $visible_To ),
																"priceBandId" 			=> $plan->easel_price_band_ref,
																"marketId" 				=> $product->airtime_market_ref,
																"billing"				=> [
																	"category"		=> ( strpos( strtolower( $plan->price_plan_name ), "premium") !== false ) ? "Premium" : ( ( strpos( strtolower( $plan->price_plan_name ), "current") !== false ) ? "Current" : "Library" ),
																],
															];

															$to_be_created[] = $aw_data_set;
														}
													}
												}
											}
										}
									}

									if( !empty( $to_be_created ) ){
										$count_windows 	= count( $to_be_created );
										$counter 		= 0;

										if( !empty( $where ) ){
											$where = convert_to_array( $where );

											if( !empty( $where ) ){

												if( !empty( $where['synchronize'] ) && ( $where['synchronize'] == true ) ){

													foreach( $to_be_created as $aw ){

														$aw_data = [];
														$aw_data = [
															"account_id" 	=> $account_id,
															"productId" 	=> ( !empty( $aw['productId'] ) ) ? $aw['productId'] : '' ,
															"visibleFrom" 	=> ( !empty( $aw['visibleFrom'] ) ) ? $aw['visibleFrom'] : '' ,
															"visibleTo" 	=> ( !empty( $aw['visibleTo'] ) ) ? $aw['visibleTo'] : '' ,
															"priceBandId" 	=> ( !empty( $aw['priceBandId'] ) ) ? $aw['priceBandId'] : '' ,
															"marketId" 		=> ( !empty( $aw['marketId'] ) ) ? $aw['marketId'] : '' ,
															"billing"		=> [
																"category"			=> ( !empty( $aw['billing']['category'] ) ) ? $aw['billing']['category'] : '' ,
															],
														];

														$easel_message 			= '';
														$easel_availability_window 		= $this->easel_service->create_availability_window( $account_id, $aw_data );

														## save availability window data in the CaCTi
														if( !empty( $easel_availability_window->data->id ) ){
															$counter++;
															$result[] = $easel_availability_window->data;

															$aw_insert_data = [
																"account_id" 					=> $account_id,
																"content_id" 					=> ( !empty( $aw['content_id'] ) ) ? ( int ) $aw['content_id'] : '' ,
																"territory_id" 					=> ( !empty( $aw['territory_id'] ) ) ? ( int ) $aw['territory_id'] : '' ,
																"site_id" 						=> ( !empty( $aw['site_id'] ) ) ? ( int ) $aw['site_id'] : '' ,
																"product_id" 					=> ( !empty( $aw['product_id'] ) ) ? ( int ) $aw['product_id'] : '' ,
																"product_price_plan_id" 		=> ( !empty( $aw['product_price_plan_id'] ) ) ? ( int ) $aw['product_price_plan_id'] : '' ,
																"easel_id" 						=> ( !empty( $easel_availability_window->data->id ) ) ? $easel_availability_window->data->id : '' ,
																"easel_productId" 				=> ( !empty( $easel_availability_window->data->productId ) ) ? $easel_availability_window->data->productId : '' ,
																"easel_visibleFrom" 			=> ( !empty( $easel_availability_window->data->visibleFrom ) ) ? $easel_availability_window->data->visibleFrom : '' ,
																"easel_visibleTo" 				=> ( !empty( $easel_availability_window->data->visibleTo ) ) ? $easel_availability_window->data->visibleTo : '' ,
																"easel_priceBandId" 			=> ( !empty( $easel_availability_window->data->priceBandId ) ) ? $easel_availability_window->data->priceBandId : '' ,
																"easel_marketId" 				=> ( !empty( $easel_availability_window->data->marketId ) ) ? $easel_availability_window->data->marketId : '' ,
																"easel_billing_category" 		=> ( !empty( $easel_availability_window->data->billing->category ) ) ? $easel_availability_window->data->billing->category : '' ,
																"easel_billing_revenueShare" 	=> ( !empty( $easel_availability_window->data->billing->revenueShare ) ) ? $easel_availability_window->data->billing->revenueShare : '' ,
																"easel_billing_wholesalePrice" 	=> ( !empty( $easel_availability_window->data->billing->wholesalePrice ) ) ? $easel_availability_window->data->billing->wholesalePrice : '' ,
																"created_by" 					=> $this->ion_auth->_current_user->id,
															];

															$this->db->insert( "availability_window", $aw_insert_data );
														}
													}
												}
											}
										}
										$summary_message = "Created $counter Availability Windows from total $count_windows required.";
										$this->session->set_flashdata( 'message', $summary_message );
									} else {
										$this->session->set_flashdata( 'message', 'No Availability Windows to be created.' );
									}
								} else {
									$this->session->set_flashdata( 'message', 'No active Airtime product for given sites.' );
								}
							} else {
								$this->session->set_flashdata( 'message', 'No sites from this territory for given Provider.' );
							}
						} else {
							$this->session->set_flashdata( 'message', 'No Clearance dates for this content.' );
						}
					} else {
						$this->session->set_flashdata( 'message', 'The Provider is not set for this Content.' );
					}
				} else {
					$this->session->set_flashdata( 'message', 'This Movie is not an Airtime Asset.' ); ##  or the External Reference is empty
				}
			} else {
				$this->session->set_flashdata( 'message', 'The incorrect Movie ID.' );
			}
		}
		return $result;
	}


	/*
	*	Create genre type on EASEL (Category type) and save in CaCTi
	*	Function is successful only if both action are true
	*/
	public function create_genre_type( $account_id = false, $genre_type_name = false, $genre_type_data = false ){
		$result = false;

		if( !empty( $account_id ) && !empty( $genre_type_name ) ){
			$data = [
				"id"			=> false,
				"name"			=> $genre_type_name,
				"exclusive"		=> ( !empty( $genre_type_data['exclusive'] ) ) ? $genre_type_data['exclusive'] : false ,
			];

			$genre_type 		= $this->easel_service->create_genre_type( $account_id, $data );


			if( ( $genre_type->success ) && !empty( $genre_type->data->id ) ){
				$reference_string = create_reference_string( $genre_type_name );
				$genre_type_dataset = [
					"account_id"		=> $account_id,
					"type_name"			=> $genre_type_name,
					"type_group"		=> ( !empty( $reference_string ) ) ? $reference_string : '' ,
					"alt_text"			=> ( !empty( $genre_type_data['alt_text'] ) ) ? $genre_type_data['alt_text'] : '' ,
					"easel_id"			=> $genre_type->data->id,
					"easel_name"		=> $genre_type->data->name,
					"easel_exclusive"	=> ( !empty( $genre_type->data->exclusive ) ) ? $genre_type->data->exclusive : false ,
					"created_by"		=> $this->ion_auth->_current_user->id,
				];

				$query = $this->db->insert( "genre_type", $genre_type_dataset );

				if( $this->db->affected_rows() > 0 ){
					$insert_id 	= $this->db->insert_id();
					$result 	= $this->db->get_where( "genre_type", ["account_id" => $account_id, "type_id" => $insert_id] )->result();
					$this->session->set_flashdata( 'message', 'Genre type created successfully' );
				} else {
					$this->session->set_flashdata( 'message', 'Error saving Genre Type into CaCTi' );
				}

			} else {
				if( !empty( $genre_type->message ) ){
					$this->session->set_flashdata( 'message', $genre_type->message );
				} else {
					$this->session->set_flashdata( 'message', 'Category Type (Genre Type) creation on Easel failed' );
				}
			}

		} else {
			$this->session->set_flashdata( 'message', 'Missing required data' );
		}

		return $result;
	}



	/*
	*	Create genre on EASEL ( Category ) and save in CaCTi
	*	Function, as the case above, is successful only if both action are true
	*/
	public function create_genre( $account_id = false, $genre_name = false, $genre_type_id = false ){
		$result = false;

		if( !empty( $account_id ) && !empty( $genre_type_id ) ){

			## get the Easel version of the genre type id
			$genre_type = $this->db->get_where( "genre_type", [$account_id => $account_id, "type_id" => $genre_type_id] )->row();

			if( !( $genre_type->easel_id ) || ( empty( $genre_type->easel_id ) ) ){
				$this->session->set_flashdata( 'message', 'Genre Type not registered on Airtime' );
				return result;
			}

			$data = [
				"id"				=> false,
				"name"				=> $genre_name,
				"categoryTypeId"	=> $genre_type->easel_id
			];

			$genre = $this->easel_service->create_genre( $account_id, $data );

			if( ( $genre->success ) && !empty( $genre->data->id ) ){
				$genre_dataset = [
					"account_id"			=> $account_id,
					"genre_name"			=> $genre_name,
					"genre_type_id"			=> $genre_type_id,
					"easel_id"				=> $genre->data->id,
					"easel_name"			=> $genre->data->name,
					"easel_categoryTypeId"	=> ( !empty( $genre->data->categoryTypeId ) ) ? $genre->data->categoryTypeId : false ,
					"created_by"			=> $this->ion_auth->_current_user->id,
				];

				$query = $this->db->insert( "genre", $genre_dataset );

				if( $this->db->affected_rows() > 0 ){
					$insert_id 	= $this->db->insert_id();
					$result 	= $this->db->get_where( "genre", ["account_id" => $account_id, "genre_id " => $insert_id] )->result();
					$this->session->set_flashdata( 'message', 'Genre created successfully' );
				} else {
					$this->session->set_flashdata( 'message', 'Error saving Genre into CaCTi' );
				}

			} else {
				if( !empty( $genre->message ) ){
					$this->session->set_flashdata( 'message', $genre->message );
				} else {
					$this->session->set_flashdata( 'message', 'Genre creation on Easel failed' );
				}
			}

		} else {
			$this->session->set_flashdata( 'message', 'Missing required data' );
		}

		return $result;
	}


	/*
	*	To get Genre type(s) from DB.
	*	Considering only Genre Type(s) with reference to Easel as valid
	*/
	public function get_genre_types( $account_id = false, $where = false ){
		$result = false;

		if( !empty( $account_id ) ){
			if( !empty( $where ) ){
				$where = convert_to_array( $where );

				if( !empty( $where ) ){

					if( !empty( $where['type_name'] ) ){
						$type_name = $where['type_name'];
						$this->db->where( "type_name", $type_name );
						unset( $where['type_name'] );
					}

					if( !empty( $where ) ){
						$this->db->where( $where );
					}
				}
			}

			$this->db->where( "genre_type.active", 1 );
			$this->db->where( "genre_type.archived !=", 1 );

			$where_not_empty = '( genre_type.easel_id !="" AND genre_type.easel_id IS NOT NULL )';
			$this->db->where( $where_not_empty );

			$this->db->select( "*", false );

			$query = $this->db->get( "genre_type" );

			if( $query->num_rows() > 0 ){
				$result = $query->result();
				$this->session->set_flashdata( 'message', 'Genre Type(s) found' );
			} else {
				$this->session->set_flashdata( 'message', 'No results' );
			}
		} else {
			$this->session->set_flashdata( 'message', 'Missing required data' );
		}

		return $result;
	}



	/*
	*	To get Genre types from DB.
	*	Considering only Genre(s) with reference to Easel as valid
	*/
	public function get_genres( $account_id = false, $where = false ){
		$result = false;

		if( !empty( $account_id ) ){

			$return_plain_array = $genre_type_id = $genre_id = $content_type = false;

			if( !empty( $where ) ){
				$where = convert_to_array( $where );

				if( !empty( $where ) ){

					if( !empty( $where['return_plain_array'] ) && ( $where['return_plain_array'] == 'yes' ) ){
						$return_plain_array = 'yes';
						unset( $where['return_plain_array'] );
					}

					if( !empty( $where['genre_id'] ) ){
						$genre_id = $where['genre_id'];
						$this->db->where_in( "genre_id", $genre_id );
						unset( $where['genre_id'] );
					}

					if( !empty( $where['genre_type_id'] ) ){
						$genre_type_id = $where['genre_type_id'];
						$this->db->where( "genre_type_id", $genre_type_id );
						unset( $where['genre_type_id'] );
					}

					if( !empty( $where['content_type'] ) ){
						$content_type = $where['content_type'];

						switch( $content_type ){
							case "episode" :
							case "series" :
								$genre_type_id		= 2;
								break;

							case "adult" :
								$genre_type_id		= 3;
								break;

							case "movie" :
							default:
								$genre_type_id		= 1;
						}
						$this->db->where_in( "genre_type_id", [$genre_type_id, 5] );

						unset( $where['content_type'] );
					}

					if( !empty( $where ) ){
						$this->db->where( $where );
					}
				}
			}

			$this->db->select( "genre.*", false );
			$this->db->select( "genre_type.type_name", false );

			$this->db->join( "genre_type", "genre_type.type_id = genre.genre_type_id", "false" );

			$this->db->where( "genre.active", 1 );
			$this->db->where( "genre.archived !=", 1 );

			$where_not_empty = '( genre.easel_id !="" AND genre.easel_id IS NOT NULL )';
			$this->db->where( $where_not_empty );

			$this->db->order_by( "genre.genre_type_id ASC, genre.genre_name ASC" );

			$query = $this->db->get( "genre" );

			if( $query->num_rows() > 0 ){

				if( $return_plain_array && $return_plain_array == 'yes' ){
					foreach( $query->result() as $row ){
						$result[] = $row->easel_id;
					}
				} else {
					$result = $query->result();
				}
				$this->session->set_flashdata( 'message', 'Genre(s) found' );
			} else {
				$this->session->set_flashdata( 'message', 'No results' );
			}
		} else {
			$this->session->set_flashdata( 'message', 'Missing required data' );
		}

		return $result;
	}



	public function media_to_airtime( $account_id = false, $content_id = false, $action = false, $mediadata = false ){
		$result = false;

		$mediadata = json_decode( $mediadata );

		if( !empty( $account_id ) && !empty( $content_id ) && !empty( $action ) && !empty( $mediadata ) ){

			$add_media_to_airtime = false;

			switch( $action ){
				case "image" :
					$add_media_to_airtime = $this->add_images_to_airtime( $account_id, $content_id, $mediadata );
					break;

				case "subtitle" :
					$add_media_to_airtime = $this->add_subtitle_to_airtime( $account_id, $content_id, $mediadata );
					break;

				case "film_trailer" :
					$add_media_to_airtime = $this->add_film_to_airtime( $account_id, $content_id, $mediadata );
				break;

				default:
					$this->session->set_flashdata( 'message', 'Unrecognized action' );
			}


			if( !empty( $add_media_to_airtime ) ){
				$result = $add_media_to_airtime;
				$msg 	= $this->session->flashdata( 'message' );

				$this->session->set_flashdata( 'message', $msg );
			}
		}

		return $result;
	}



	public function add_images_to_airtime( $account_id = false, $content_id = false, $mediadata = false ){
		$result = false;

		if( !empty( $account_id ) && !empty( $content_id ) && !empty( $mediadata ) ){

			## we must start from the hero image!!!
			$hero_image		= [];
			$other_images	= [];

			foreach( $mediadata as $file ){

				## prepare the where
				$where = [
					"account_id" 	=> $account_id,
					"content_id" 	=> $content_id,
					"document_id"	=> $file->value
				];

				## find if the file exists and get his data
				$document_exists = false;
				$document_exists = $this->db->get_where( "content_document_uploads", $where, 1, 0 )->row();  ## limit and offset

				## check if the document has the path (required by Easel).
				if( isset( $document_exists ) && !empty( $document_exists->document_link ) ){

					## if the document is hero type - throw to hero's bag, otherwise to others
					if( !empty( $document_exists->doc_file_type ) && ( strtolower( $document_exists->doc_file_type ) == "hero" ) ){ 	## 'master' image for Easel, should be processed as a first
						$hero_image 	= $document_exists;
					} else {
						## it should be just one, but to make if extend-able for the future we're using the array for other images
						$other_images[] = $document_exists;
					}

				## If it hasn't skip the round - there are potentially more files 2 b processed
				} else {
					continue;
				}
			}

			## If 'hero' image exists ('Hero' type should be included in any request, even if it is just an update), add it into the Airtime product
			if( !empty( $hero_image ) ){

				$whole_message 	= "";
				$hero_message	= "";
				$image_message	= "";

				$hero_submitted = ( object ) ["success"=>false];

				## Create the Hero image on Easel
				$hero_submitted = $this->easel_service->create_image( $account_id, ["url" =>$hero_image->document_link ] );

				## Easel safety checks
				if( ( $hero_submitted->success !== false ) && ( !empty( $hero_submitted->data->id ) ) ){
					## I can continue only if I do have the Easel image reference. I will store it in the DB a bit later
					## I decided to store the reference now and add the status field with the date

					$upd_data = [];
					$upd_data = [
						"airtime_reference"				=> $hero_submitted->data->id,
						"airtime_status" 				=> "image_created",
						"airtime_status_update_date" 	=> date( 'Y-m-d H:i:s' )
					];

					$upd_where = [];
					$upd_where = [
						"document_id" => $hero_image->document_id
					];

					$this->db->update( "content_document_uploads", $upd_data, $upd_where );


					// in theory I should pull the image object from Easel and confirm hero image exists before updating CaCTi - let's skip it now as I do have the hero_submitted->status = true, which means in theory Easel confirmed the adding

					## Get the product data: I can get it from Easel, reassign, do update or get it from CaCTi to ensure what we do have here will be on Easel.
					## For now, I will use Easel object.

					$content_data = $this->get_content( $account_id, $content_id );

					if( !empty( $content_data ) && !empty( $content_data->external_content_ref ) ){

						$airtime_product_data = $this->easel_service->fetch_product( $account_id, $content_data->external_content_ref );

						if( !empty( $airtime_product_data ) && !empty( $airtime_product_data->id ) ){

							## update the product with hero as a master
							## newest (X.2021): update product with:
							## - hero (flat one) - as a general master && as a thumb master
							## - poster (vertical one) as a thumb 2:3

							$airtime_hero_upd_dataset = [
								"id"				=> $airtime_product_data->id,
								"reference"			=> ( !empty( $airtime_product_data->reference ) ) ? $airtime_product_data->reference : '' ,
								"type"				=> ( !empty( $airtime_product_data->type ) ) ? $airtime_product_data->type : '' ,
								"name"				=> ( !empty( $airtime_product_data->name ) ) ? $airtime_product_data->name : '' ,
								"state"				=> ( !empty( $airtime_product_data->state ) ) ? $airtime_product_data->state : '' ,
								"tagline"			=> ( !empty( $airtime_product_data->shortDescription ) ) ? $airtime_product_data->shortDescription : '' ,
								"plot"				=> ( !empty( $airtime_product_data->description ) ) ? $airtime_product_data->description : '' ,
								"running_time"		=> ( !empty( $content_data->running_time ) ) ? $content_data->running_time : '' ,
								"country"			=> ( !empty( $airtime_product_data->country ) ) ? $airtime_product_data->country : '' ,
								"release_date"		=> ( !empty( $content_data->release_date ) ) ? $content_data->release_date : '' ,
								"parentalAdvisory"	=> ( !empty( $airtime_product_data->parentalAdvisory ) ) ? $airtime_product_data->parentalAdvisory : '' ,
								"categories"		=> ( !empty( $airtime_product_data->categories ) ) ? $airtime_product_data->categories : '' ,
								"ageRatings"		=> ( !empty( $airtime_product_data->ageRatings ) ) ? $airtime_product_data->ageRatings : '' ,
								"indexable"			=> ( !empty( $airtime_product_data->indexable ) ) ? $airtime_product_data->indexable : true ,
								"episodeNumber"		=> ( !empty( $airtime_product_data->episodeNumber ) ) ? $airtime_product_data->episodeNumber : false ,
								"image"				=> [
									"master" 	=> [
										"imageId"	=> $hero_submitted->data->id,
									],
									"thumb" 	=> [
										"master" 	=> [
											"imageId"	=> $hero_submitted->data->id ,
										]
									]
								]
							];

							if( isset( $airtime_product_data->published ) && !empty( $airtime_product_data->published ) ){
								$airtime_hero_upd_dataset["published"] = $airtime_product_data->published;
							}

							if( isset( $airtime_product_data->trailer ) && !empty( $airtime_product_data->trailer ) ){
								$airtime_hero_upd_dataset["trailer"] = $airtime_product_data->trailer;
							}

							if( isset( $airtime_product_data->feature ) && !empty( $airtime_product_data->feature ) ){
								$airtime_hero_upd_dataset["feature"] = $airtime_product_data->feature;
							}

							if( isset( $airtime_product_data->image->thumb->{'2:3'}->imageId ) && !empty( $airtime_product_data->image->thumb->{'2:3'}->imageId ) ){
								$airtime_hero_upd_dataset['image']['thumb']['2:3']['imageId'] = $airtime_product_data->image->thumb->{'2:3'}->imageId;
							}


							## I need to have all the existing product data here before doing the update
							$airtime_updated_product = $this->easel_service->update_product( $account_id, $airtime_product_data->id, $airtime_hero_upd_dataset );

							if( ( $airtime_updated_product->success !== false ) && ( $airtime_updated_product->data->id ) ){
								## hero image added to the product

								// in theory I should pull the product object and check if the hero image exists. For now, I will just rely on Easel as the success !== false

								## update CaCTi -> I SAID I'M GOING TO UDATE THE IMAGE ONLY WHEN SUCCESSFULLY ADDED TO THE PRODUCT!!!!
								## - please ignore uppercase, this is just developer discussing stuff with himself

								## Just the explanation: the process has been created this way that if the image has the reference it means it has been also successfully added to product - movie

								$hero_upd_data = [
									// "airtime_reference"				=> ( !empty( $hero_submitted->data->id ) ) ? $hero_submitted->data->id : '' ,
									"airtime_name"					=> ( !empty( $hero_submitted->data->name ) ) ? $hero_submitted->data->name : '' ,
									"airtime_format"				=> ( !empty( $hero_submitted->data->format ) ) ? $hero_submitted->data->format : '' ,
									"airtime_md5"					=> ( !empty( $hero_submitted->data->md5 ) ) ? $hero_submitted->data->md5 : '' ,
									"airtime_status" 				=> "image_linked",
									"airtime_status_update_date" 	=> date( 'Y-m-d H:i:s' )
								];

								$query = $this->db->update( "content_document_uploads", $hero_upd_data, ["account_id" => $account_id, "document_id" =>$hero_image->document_id ] );
								## this step means I get/confirmed the image reference from/with Easel and now saving it in the Database

								if( $this->db->affected_rows() > 0 ){
									## DB update went OK - what should I return?

									$hero_message = 'Hero image type has been sent to Airtime and added to the product. ';
									// $this->session->set_flashdata( 'message', 'Hero image sent to Airtime and added to the product' );


									## so, ONLY if hero image has been submitted successfully I can progress with other images

									## are there any other images?
									if( !empty( $other_images ) ){

										foreach( $other_images as $o_image ){
											## So far we do have only two types of the images and it should be always just one image 'other' type - 'standard'.
											## It will be send into Airtime as poster and thumb
											## Scenario when just adding another image to the existing ones - doesn't exists - we will always submit a full set or Hero only

											if( !empty( $o_image->document_link ) ){
												$o_image_submitted = $this->easel_service->create_image( $account_id, ["url" =>$o_image->document_link ] );

												if( ( $o_image_submitted->success !== false ) && ( !empty( $o_image_submitted->data->id ) ) ){

													$upd_data = [];
													$upd_data = [
														"airtime_reference"				=> $o_image_submitted->data->id,
														"airtime_status" 				=> "image_created",
														"airtime_status_update_date" 	=> date( 'Y-m-d H:i:s' )
													];

													$upd_where = [];
													$upd_where = [
														"document_id" => $o_image->document_id
													];

													$this->db->update( "content_document_uploads", $upd_data, $upd_where );

													$airtime_product_data = false;
													$airtime_product_data = $this->easel_service->fetch_product( $account_id, $content_data->external_content_ref );

													if( !empty( $airtime_product_data ) && !empty( $airtime_product_data->id ) ){

														## update the product with hero as a master
														$airtime_upd_dataset = [
															"id"				=> $airtime_product_data->id,
															"reference"			=> ( !empty( $airtime_product_data->reference ) ) ? $airtime_product_data->reference : '' ,
															"type"				=> ( !empty( $airtime_product_data->type ) ) ? $airtime_product_data->type : '' ,
															"name"				=> ( !empty( $airtime_product_data->name ) ) ? $airtime_product_data->name : '' ,
															"state"				=> ( !empty( $airtime_product_data->state ) ) ? $airtime_product_data->state : '' ,
															"tagline"			=> ( !empty( $airtime_product_data->shortDescription ) ) ? $airtime_product_data->shortDescription : '' ,
															"plot"				=> ( !empty( $airtime_product_data->description ) ) ? $airtime_product_data->description : '' ,
															"running_time"		=> ( !empty( $content_data->running_time ) ) ? $content_data->running_time : '' ,
															"country"			=> ( !empty( $airtime_product_data->country ) ) ? $airtime_product_data->country : '' ,
															"release_date"		=> ( !empty( $content_data->release_date ) ) ? $content_data->release_date : '' ,
															"parentalAdvisory"	=> ( !empty( $airtime_product_data->parentalAdvisory ) ) ? $airtime_product_data->parentalAdvisory : '' ,
															"categories"		=> ( !empty( $airtime_product_data->categories ) ) ? $airtime_product_data->categories : '' ,
															"ageRatings"		=> ( !empty( $airtime_product_data->ageRatings ) ) ? $airtime_product_data->ageRatings : '' ,
															"indexable"			=> ( !empty( $airtime_product_data->indexable ) ) ? $airtime_product_data->indexable : true ,
															"episodeNumber"		=> ( !empty( $airtime_product_data->episodeNumber ) ) ? $airtime_product_data->episodeNumber : false ,

															"image"				=> [
																"master" 	=> [
																	"imageId"	=> ( !empty( $airtime_product_data->image->master->imageId ) ) ? $airtime_product_data->image->master->imageId : ''
																],
																"thumb" 	=> [
																	"master" 	=> [
																		"imageId"	=> ( !empty( $airtime_product_data->image->thumb->master->imageId ) ) ? $airtime_product_data->image->thumb->master->imageId : ''
																	],
																	"2:3" 	=> [
																		"imageId"	=> ( !empty( $airtime_product_data->image->thumb->{ '2:3' }->imageId ) ) ? $airtime_product_data->image->thumb->{ '2:3' }->imageId : ''
																	],
																],
																"hero" 	=> [
																	"master" 	=> [
																		"imageId"	=> ( !empty( $airtime_product_data->image->hero->master->imageId ) ) ? $airtime_product_data->image->hero->master->imageId : ''
																	]
																],
																"carousel" 	=> [
																	"master" 	=> [
																		"imageId"	=> ( !empty( $airtime_product_data->image->carousel->master->imageId ) ) ? $airtime_product_data->image->carousel->master->imageId : ''
																	]
																],
															]
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

														## to make the functionality open for the future changes - if more image types will be accessible
														if( !empty( $o_image->doc_file_type ) ){
															switch( strtolower( $o_image->doc_file_type ) ){

																## for now all will be using the
																case "thumb" 	:
																	$airtime_upd_dataset['image']['thumb']['master']['imageId'] 	= $o_image_submitted->data->id;
																	// no break yet, as we not having the 'thumb' image type in CaCTi
																	// break;

																case "poster"	:
																case "standard" :
																default :
																	$airtime_upd_dataset['image']['thumb']['2:3']['imageId'] 	= $o_image_submitted->data->id;
																	// $airtime_upd_dataset['image']['poster']['master']['imageId']	= $o_image_submitted->data->id;
																	break;
															}
														}

														$airtime_updated_product = false;
														$airtime_updated_product = $this->easel_service->update_product( $account_id, $airtime_product_data->id, $airtime_upd_dataset );

														if( ( $airtime_updated_product->success !== false ) && ( $airtime_updated_product->data->id ) ){
															## all is good - image submitted and added to Airtime
															## message

															$image_upd_data = [];
															$image_upd_data = [
																"airtime_reference"				=> ( !empty( $o_image_submitted->data->id ) ) ? $o_image_submitted->data->id : '' ,
																"airtime_name"					=> ( !empty( $o_image_submitted->data->name ) ) ? $o_image_submitted->data->name : '' ,
																"airtime_format"				=> ( !empty( $o_image_submitted->data->format ) ) ? $o_image_submitted->data->format : '' ,
																"airtime_md5"					=> ( !empty( $o_image_submitted->data->md5 ) ) ? $o_image_submitted->data->md5 : '' ,
																"airtime_status" 				=> "image_linked",
																"airtime_status_update_date" 	=> date( 'Y-m-d H:i:s' )
															];

															$query = $this->db->update( "content_document_uploads", $image_upd_data, ["account_id" => $account_id, "document_id" =>$o_image->document_id ] );
															## this step means I get/confirmed the image reference from/with Easel and now saving it in the Database

															if( $this->db->affected_rows() > 0 ){
																## DB update went OK - what should I return?

																$image_message .= ucfirst( $o_image->doc_file_type ).' image type has been sent to Airtime and added to the product. ';
																// $this->session->set_flashdata( 'message', 'Hero image sent to Airtime and added to the product' );
															} else {
																## CaCTi DB not updated
															}
														} else {
															## image not added to the product

															$upd_data = [];
															$upd_data = [
																"airtime_status" 				=> "image_linking_error",
																"airtime_status_update_date" 	=> date( 'Y-m-d H:i:s' )
															];

															$upd_where = [];
															$upd_where = [
																"document_id" => $o_image->document_id
															];

															$this->db->update( "content_document_uploads", $upd_data, $upd_where );

														}

													} else {
														## We couldn't pull the product data - issue with Airtime
													}
												} else  {
													## The image hasn't been created on Airtime

													$upd_data = [];
													$upd_data = [
														"airtime_status" 				=> "image_creation_error",
														"airtime_status_update_date" 	=> date( 'Y-m-d H:i:s' )
													];

													$upd_where = [];
													$upd_where = [
														"document_id" => $o_image->document_id
													];

													$this->db->update( "content_document_uploads", $upd_data, $upd_where );
												}
											} else {
												## the image has no URL link, so cannot be created on Easel
												$upd_data = [];
												$upd_data = [
													"airtime_status" 				=> "image_creation_error",
													"airtime_status_update_date" 	=> date( 'Y-m-d H:i:s' )
												];

												$upd_where = [];
												$upd_where = [
													"document_id" => $o_image->document_id
												];

												$this->db->update( "content_document_uploads", $upd_data, $upd_where );
											}
										}
									} else {
										## no more images just return true
									}

									## probably the returning should be done here, outside the 'other images' loop
									$result = $this->get_content( $account_id, $content_id );

									$whole_message = $hero_message.' '.$image_message;
									$this->session->set_flashdata( 'message', $whole_message );

								} else {
									## CaCTi DB update failed
									$this->session->set_flashdata( 'message', 'CaCTi DB update failed' );

								}
							} else {
								$hero_upd_data = [
									"airtime_status" 				=> "image_linking_error",
									"airtime_status_update_date" 	=> date( 'Y-m-d H:i:s' )
								];

								$query = $this->db->update( "content_document_uploads", $hero_upd_data, ["account_id" => $account_id, "document_id" =>$hero_image->document_id ] );

								## Easel's product update failed
								$this->session->set_flashdata( 'message', 'Airtime product update failed' );
							}
						} else {
							## Failed to fetch the data from Easel
							$this->session->set_flashdata( 'message', 'Failed to fetch the data from Airtime' );
						}
					} else {
						## Content has no Airtime reference
						$this->session->set_flashdata( 'message', 'Content has no Airtime reference' );
					}

				} else {

					$message = 'Hero type image creation on Airtime failed';
					if( !empty( $hero_submitted->message ) ){
						$message .= ": ".$hero_submitted->message;
					}


					$upd_data = [];
					$upd_data = [
						"airtime_status" 				=> "image_creation_error",
						"airtime_status_update_date" 	=> date( 'Y-m-d H:i:s' )
					];

					$upd_where = [];
					$upd_where = [
						"document_id" => $hero_image->document_id
					];

					$this->db->update( "content_document_uploads", $upd_data, $upd_where );

					$this->session->set_flashdata( 'message', $message );
				}

			} else {
				$this->session->set_flashdata( 'message', 'No Hero type image provided' );
			}
		}
		return $result;
	}

	public function add_subtitle_to_airtime( $account_id = false, $content_id = false, $mediadata = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $content_id ) && !empty( $mediadata ) ){

			## validate the content has the movie file attached and get the AT ID of the movie file
			$content_details	= false;
			$content_details 	= $this->get_content( $account_id, $content_id );

			if( !empty( $content_details ) ){

				## validate if we do have the VoD media file reference
				if( !empty( $content_details->at_vod_media_reference ) ){

					$files_to_process 				= 0;
					$files_to_process 				= count( $mediadata );
					$files_processed_successfully	= [];
					$files_failed_to_process		= 0;

					foreach( $mediadata as $sub ){

						## get the details
						$subtitles_exists 	= false;
						$subtitles_exists 	= $this->_fetch_movie_subtitles( $account_id, $content_id, ["document_id"=>$sub->value] );

						if( is_array( $subtitles_exists ) && !empty( $subtitles_exists[array_key_first( $subtitles_exists )]->language_short ) && !empty( $subtitles_exists[array_key_first( $subtitles_exists )]->file_url ) ){

							$sub_data 			= false;
							$sub_data 			= [
								"vodMediaId" 	=> $content_details->at_vod_media_reference,
								"language" 		=> $subtitles_exists[array_key_first( $subtitles_exists )]->language_short,
								"url" 			=> $subtitles_exists[array_key_first( $subtitles_exists )]->file_url,
							];

							$easel_subtitles = false;
							$easel_subtitles = $this->easel_service->create_subtitle( $account_id, $sub_data );

							if( isset( $easel_subtitles ) && ( !empty( $easel_subtitles->data->id ) ) ){
								## update subtitles in the CaCTi's DB

								$sub_update_data = [];
								$sub_update_data = [
									"airtime_reference" 			=> $easel_subtitles->data->id,
									"airtime_vodMediaId" 			=> $easel_subtitles->data->vodMediaId,
									"last_modified_by"				=> $this->ion_auth->_current_user->id,
									"airtime_status" 				=> "subtitle_created",
									"airtime_status_update_date" 	=> date( 'Y-m-d H:i:s' )
								];

								$where_upd 	= [];
								$where_upd = [
									"account_id"	=> $account_id,
									"document_id" 	=> $sub->value,
								];

								$sub_updated = false;
								$sub_updated = $this->db->update( "content_document_uploads", $sub_update_data, $where_upd );

								if( $this->db->affected_rows() > 0 ){

									$file_processed 						= [];
									$file_processed 						= $subtitles_exists;
									$file_processed['airtime_reference'] 	= $easel_subtitles->data->id;
									$file_processed['airtime_vodMediaId'] 	= $easel_subtitles->data->vodMediaId;

									// all is good, count as 'DONE'
									$files_processed_successfully[] 		= $file_processed;

								} else {
									// updating CaCTi failed - no message as it is a one from many
								}

							} else {
								// adding to Easel failed - no message as it is a one from many

								$sub_update_data = [];
								$sub_update_data = [
									"last_modified_by"				=> $this->ion_auth->_current_user->id,
									"airtime_status" 				=> "subtitle_creation_error",
									"airtime_status_update_date" 	=> date( 'Y-m-d H:i:s' )
								];

								$where_upd 	= [];
								$where_upd = [
									"account_id"	=> $account_id,
									"document_id" 	=> $sub->value,
								];

								$sub_updated = false;
								$sub_updated = $this->db->update( "content_document_uploads", $sub_update_data, $where_upd );
							}

						} else  {
							// this subtitle doesn't exists or it has incomplete data - no message as it is a one from many
						}
					}

					if( !empty( $files_processed_successfully ) ){
						$number_of_files_processed_successfully = ( int ) count( $files_processed_successfully );
						$result 								= $files_processed_successfully;
						$message 								= '<span style="font-weight:800;">'.$number_of_files_processed_successfully.'</span> of <span style="font-weight:800;">'.$files_to_process.'</span><br />Subtitles have been successfully created and linked';
						$this->session->set_flashdata( 'message', $message );
					} else {
						$this->session->set_flashdata( 'message', 'File(s) processing error' );
					}

				} else {
					$this->session->set_flashdata( 'message', 'The Content has no VoD media file linked' );
				}
			} else {
				$this->session->set_flashdata( 'message', 'Wrong Content ID' );
			}
		} else {
			$this->session->set_flashdata( 'message', 'No required data provided' );
		}
		return $result;
	}


	/*
	*	This function will submit the movie (trailer) file into Easel for encoding
	*/
	public function add_film_to_airtime( $account_id = false, $content_id = false, $mediadata = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $content_id ) && !empty( $mediadata ) ){
			## assuming we will always have the movie file, which is related to the content_decoded_file table

			$number_files_2b_processed 	= count( ( array ) $mediadata );
			$files_processed 			= [];
			$easel_message				= "";

			## assuming there will be always a multiple of files:
			foreach( $mediadata as $file ){

				if( !empty( $file->value ) ){
					$movie_file_details = false;
					$movie_file_details = $this->db->get_where( "content_decoded_file", ["file_id" => $file->value], 1, 0 )->row();

					if( !empty( $movie_file_details ) ){

						if( !empty( $movie_file_details->airtime_reference ) ){
							## submit for encoding, save it in the database

							$start_encoding_data = [];
							$start_encoding_data = [
								"account_id" 	=> $account_id,
								"vod_media_id" 	=> $movie_file_details->airtime_reference,
								"quality" 		=> "hd",
							];

							$start_encoding = false;
							$start_encoding = $this->easel_service->start_encoding( $account_id, $start_encoding_data );

							if( $start_encoding->success != false ){

								$files_processed[] = $movie_file_details;

								$upd_data = [];
								$upd_data = [
									"airtime_encoded_status" 		=> "pending-encoding",
									"airtime_encoded_update_date" 	=> date( 'Y-m-d H:i:s' )
								];

							} else {
								$upd_data = [];
								$upd_data = [
									"airtime_encoded_status" 		=> "pending-encoding-error",
									"airtime_encoded_update_date" 	=> date( 'Y-m-d H:i:s' )
								];
							}

							// Update the DB status
							$upd_where = [];
							$upd_where = [
								"file_id" 			=> $file->value,
								"airtime_reference" => $movie_file_details->airtime_reference
							];
							$this->db->update( "content_decoded_file", $upd_data, $upd_where );

							if( !empty( $start_encoding->message ) ){
								if( strlen( $easel_message > 2 ) ){
									$easel_message .= " | ";
								}
								$easel_message .= $start_encoding->message;
							}

						} else {
							// missing AT reference
						}
					} else {
						// file not found in the database
					}
				} else {
					// no file reference from the Web Client
					// $this->session->set_flashdata( 'message', 'Missing required values: Account ID, Content ID or Media IDs' );
				}
			} ## End of foreach

			$message 		= "";
			if( !empty( $files_processed ) ){
				$message 	= '<span style="font-weight:800;">'.( count( $files_processed ) ).'</span> of <span style="font-weight:800;">'.$number_files_2b_processed.'</span> files have been triggered for encoding';
				$result 	= $files_processed;
			} else {
				$message 	= ( strlen( $easel_message ) > 2 ) ? $easel_message : "No file has been triggered for encoding";
			}
			$this->session->set_flashdata( 'message', $message );

		} else {
			// missing required values
			$this->session->set_flashdata( 'message', 'Missing required values: Account ID, Content ID or Media IDs' );
		}
		return $result;
	}


	/*
	*	Adding the metadata files and the movie files into AWS
	*/
	public function media_to_aws( $account_id = false, $content_id = false, $movie_data = false ){
		$result = ( object ) [
			"success" 	=> false,
			"message"	=> "",
			"data"		=> false
		];

		$movie_data 	= convert_to_array( $movie_data );

		if( !empty( $account_id ) && !empty( $content_id ) && !empty( $movie_data ) ){

			## get the film data (need assetcode and provider):
			$content_details = false;
			$content_details = $this->get_content( $account_id, $content_id );

			## If i do have the all necessary data:
			if( isset( $content_details ) && !empty( $content_details->asset_code ) && !empty( $content_details->provider_name ) ){

				$assetcode						= false;
				$assetcode						= $content_details->asset_code;

				$provider						= false;
				$provider						= strtolower( $content_details->provider_reference_code );

				$file_processed_movie 			= [];
				$file_processed_movie['success']= [];
				$file_processed_movie['error']	= [];

				$number_movie_files_to_process 	= count( $movie_data );

				## Submitting movie files. Assuming we do have multiple movies - still valid (trailer + feature)
				foreach( $movie_data as $file ){

					if( !empty( $file->value ) ){

						$filename 			= false;

						## getting the file name
						$this->db->select( "cdf.file_id, cdf.file_new_name, cdf.airtime_reference, cdf.is_on_aws, cdf.main_record ", false );
						$this->db->select( "cdft.type_group `file_type`", false );

						$this->db->join( "content_decoded_file_type `cdft`", "cdft.type_id = cdf.decoded_file_type_id", "left" );

						$arch_where 		= "( cdf.archived IS NULL or cdf.archived != 1 )";
						$this->db->where( $arch_where );
						$this->db->where( "cdf.file_id", $file->value );

						$film_file_details 	= false;
						$film_file_details 	= $this->db->get( "content_decoded_file `cdf`" )->row();

						if( !empty( $film_file_details ) && !empty( $film_file_details->file_new_name ) ){
							$filename = $film_file_details->file_new_name;
						}

						$submit_aws = false;
						$submit_aws = $this->coggins_service->aws_transfer1( $account_id, $assetcode, $provider, $filename );

						$upd_data 		= [];
						$upd_data 		= [
							"aws_status" 			=> ( isset( $submit_aws->success ) && ( $submit_aws->success != false ) && ( $submit_aws->data != false ) ) ? "transfer_initiated" : "transfer_initiating_error" ,
							"aws_uploading_date"	=> date( 'Y-m-d H:i:s' ),
						];

						$upd_where 		= [];
						$upd_where 		= [
							"file_id" => $file->value,
						];

						$upd_film_file 	= false;
						$upd_film_file 	= $this->db->update( "content_decoded_file", $upd_data, $upd_where );

						if( $this->db->trans_status() != false ){
							## this possibly could be a counter how many files were updated - so far, not required
						}

						if( ( $submit_aws->success ) && ( $submit_aws->success != false ) ){
							$file_processed_movie['success'][] = ["file_id"=>$file->value, "file_data"=>$upd_data, "coggins_msg" =>$submit_aws->message ];
						} else {
							$file_processed_movie['error'][] = ["file_id"=>$file->value, "file_data"=>$upd_data, "coggins_msg" =>$submit_aws->message ];
						}

					}

					$msg 	= "";
					if( !empty( $file_processed_movie['success'] ) ){
						$msg 	.= '<span class="bold" style="font-weight: 800;">'.count( $file_processed_movie['success'] ).'</span> of <span class="bold" style="font-weight: 800;">'.$number_movie_files_to_process.'</span> file(s) have been processed successfully. ';
					}

					if( !empty( $file_processed_movie['error'] ) ){
						$msg 	.= '<span class="bold" style="font-weight: 800;">'.count( $file_processed_movie['error'] ).'</span> of <span class="bold" style="font-weight: 800;">'.$number_movie_files_to_process.'</span> file(s) have been processed with error. ';
					}

					if( !empty( $file_processed_movie['success'] ) && ( count( $file_processed_movie['success'] ) > 0 ) ){
						$result->success 	= true;
					} else {
						$result->success 	= false;
					}
					$result->data			= $file_processed_movie;
					$result->message 		= $msg;
				}

			} else {
				$result->message 			= "Error retrieving the Asset Code or the Provider";
			}
		}

		return $result;
	}



	/**
	*	Process Single-Use Content Upload
	**/
	public function su_upload_content( $account_id = false ){
		$result = null;
		if( !empty( $account_id ) ){
			$uploaddir  = $this->app_root. 'assets' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;

			if( !file_exists( $uploaddir) ){
				mkdir( $uploaddir );
			}

			$this->db->truncate( 'su_content_tmp_upload' );

			for( $i=0; $i < count( $_FILES['upload_file']['name'] ); $i++ ) {
				//Get the temp file path
				$tmpFilePath = $_FILES['upload_file']['tmp_name'][$i];
				if ( $tmpFilePath != '' ){
					$uploadfile = $uploaddir . basename( $_FILES['upload_file']['name'][$i] ); //Setup our new file path
					if ( move_uploaded_file( $tmpFilePath, $uploadfile) ) {
						//If FILE is CSV process differently
						$ext = pathinfo( $uploadfile, PATHINFO_EXTENSION );
						if ( $ext == 'csv' ){
							$processed = csv_file_to_array( $uploadfile );

							if( !empty( $processed ) ){
								$data = $this->_su_save_temp_data( $account_id, $processed );
								if( $data ){
									unlink( $uploadfile );
									$result = true;
								}
							}
						}
					}
				}
			}
		}
		return $result;
	}


	/** Process uploaded array **/
	private function _su_save_temp_data( $account_id = false, $raw_data = false ){
		$result = null;
		if( !empty( $account_id ) && !empty( $raw_data ) ){
			$exists = $new = [];
			foreach( $raw_data as $k => $record ){ ## it is to check if in the table aren't duplicates looking at which column - reference

				## the table is freshly cleaned
				## the reference is a leading column - checking against it
				## we do hope nothing is there, so everything will go to the 'new'
				$check_exists = false;
				$check_exists = $this->db->where( ['reference' => $record['reference'] ] )
					->limit( 1 )
					->get( 'su_content_tmp_upload' )
					->row();

				if( !empty( $check_exists ) ){
					$exists[] 	= $this->ssid_common->_filter_data( 'su_content_tmp_upload', $record );
				} else {
					$new[]  	= $this->ssid_common->_filter_data( 'su_content_tmp_upload', $record );
				}
			}

			//Updated existing
			if( !empty( $exists ) ){
				$this->db->update_batch( 'su_content_tmp_upload', $exists, 'reference' );
			}

			//Insert new records
			if( !empty( $new ) ){
				$this->db->insert_batch( 'su_content_tmp_upload', $new );
			}

			$result = ( $this->db->affected_rows() > 0 ) ? true : false;
		}
		return $result;
	}



	/**
	*	SU (Single Use) - Get content records pending from upload
	**/
	public function su_get_pending_upload_records( $account_id = false ){
		$result = null;
		if( !empty( $account_id ) ){

			$this->db->select( "su_content_tmp_upload.*", false );

			$query = $this->db->get( 'su_content_tmp_upload' );

			if( $query->num_rows() > 0 ){
				$data = [];
				foreach( $query->result() as $k => $row ){

					$check = false;
					$this->db->select( '
					content_film.content_id `cacti_id`,
					content_film.external_content_ref `existing_easel_id`,
					content_film.asset_code `cacti_reference`,
					content_film.type `cacti_type`,
					content_film.title `cacti_name`,
					content_film.airtime_state `cacti_airtime_state`,
					content_film.plot `cacti_plot`' );

					$select_1 = "( SELECT content_decoded_file.airtime_reference FROM content_decoded_file WHERE content_decoded_file.decoded_file_type_id = 2 AND content_decoded_file.content_id = content_film.content_id AND content_decoded_file.main_record = 1 AND ( content_decoded_file.archived != 1 OR content_decoded_file.archived IS NULL ) ) as `trailer_name` ";
					$this->db->select( $select_1, false );

					$select_2 = "( SELECT content_decoded_file.airtime_reference FROM content_decoded_file WHERE content_decoded_file.decoded_file_type_id = 1 AND content_decoded_file.content_id = content_film.content_id AND content_decoded_file.main_record = 1 AND ( content_decoded_file.archived != 1 OR content_decoded_file.archived IS NULL ) ) as `feature_name` ";
					$this->db->select( $select_2, false );

					$this->db->where( 'content_film.asset_code', $row->reference );

					$check = $this->db->get( 'content_film' )->row_array();

					if( !empty( $check ) ){
						$data['existing-records'][$check['cacti_reference']]['upload'] = ( array ) $row;
						$data['existing-records'][$check['cacti_reference']]['cacti'] = ( array ) $check;
					} else {
						## Temporarily switched off as we do process only those existing in the system
						// $data['new-records'][$row->reference]['upload'] = ( array ) $row;
						// $data['new-records'][$row->reference]['cacti'] = false;
					}
				}
				$result = $data;
			}
		}
		return $result;
	}



	/**
	*	Process Single-Use Decoded Files Upload
	**/
	public function su_upload_decoded_files( $account_id = false ){
		$result = null;
		if( !empty( $account_id ) ){
			$uploaddir  = $this->app_root. 'assets' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;

			if( !file_exists( $uploaddir) ){
				mkdir( $uploaddir );
			}

			$this->db->truncate( 'su_decoded_files_tmp_upload' );

			for( $i=0; $i < count( $_FILES['upload_file']['name'] ); $i++ ) {
				//Get the temp file path
				$tmpFilePath = $_FILES['upload_file']['tmp_name'][$i];
				if ( $tmpFilePath != '' ){
					$uploadfile = $uploaddir . basename( $_FILES['upload_file']['name'][$i] ); //Setup our new file path
					if ( move_uploaded_file( $tmpFilePath, $uploadfile) ) {
						//If FILE is CSV process differently
						$ext = pathinfo( $uploadfile, PATHINFO_EXTENSION );
						if ( $ext == 'csv' ){
							$processed = csv_file_to_array( $uploadfile );

							if( !empty( $processed ) ){
								$data = $this->_su_save_temp_decoded_files_data( $account_id, $processed );
								if( $data ){
									unlink( $uploadfile );
									$result = true;
								}
							}
						}
					}
				}
			}
		}
		return $result;
	}



	/** Process uploaded array **/
	private function _su_save_temp_decoded_files_data( $account_id = false, $raw_data = false ){
		$result = null;
		if( !empty( $account_id ) && !empty( $raw_data ) ){
			$exists = $new = [];
			foreach( $raw_data as $k => $record ){ ## it is to check if in the table aren't duplicates looking at which column - reference

				## the table is freshly cleaned
				## the reference is a leading column - checking against it
				## we do hope nothing is there, so everything will go to the 'new'
				$check_exists = false;
				$check_exists = $this->db->where( ['file_new_name' => $record['file_new_name'] ] )
					->limit( 1 )
					->get( 'su_decoded_files_tmp_upload' )
					->row();

				if( !empty( $check_exists ) ){
					$exists[] 	= $this->ssid_common->_filter_data( 'su_decoded_files_tmp_upload', $record );
				} else {
					$new[]  	= $this->ssid_common->_filter_data( 'su_decoded_files_tmp_upload', $record );
				}
			}

			//Updated existing
			if( !empty( $exists ) ){
				$this->db->update_batch( 'su_decoded_files_tmp_upload', $exists, 'file_new_name' );
			}

			//Insert new records
			if( !empty( $new ) ){
				$this->db->insert_batch( 'su_decoded_files_tmp_upload', $new );
			}

			$result = ( $this->db->affected_rows() > 0 ) ? true : false;
		}
		return $result;
	}



	/**
	*	SU (Single Use) - Get content records pending from upload
	**/
	public function su_get_pending_decoded_files_upload( $account_id = false, $number_of_records = false ){
		$result = null;
		if( !empty( $account_id ) ){

			$this->db->select( "su_decoded_files_tmp_upload.*", false );
			// $this->db->where_in( "su_decoded_files_tmp_upload.content_id", [7,937] );

			if( !empty( $number_of_records ) ){
				$this->db->limit( ( int ) $number_of_records );
			}

			$query = $this->db->get( 'su_decoded_files_tmp_upload' );

			if( $query->num_rows() > 0 ){

				$data = [];
				foreach( $query->result() as $k => $row ){

					$check = false;
					$this->db->select( '
						content_decoded_file.content_id `existing_content_id`,
						content_decoded_file.file_id `existing_file_id`,
						content_decoded_file.file_new_name `existing_file_new_name`,
						content_decoded_file_type.type_group `existing_file_type`,
						content_decoded_file.airtime_reference `existing_airtime_reference`,
						content_decoded_file.airtime_product_reference `existing_airtime_product_reference`,
						content_film.airtime_state `existing_airtime_state`
					', false );

					$this->db->join( "content_decoded_file_type", "content_decoded_file_type.type_id = content_decoded_file.decoded_file_type_id", "left" );
					$this->db->join( "content_film", "content_film.content_id = content_decoded_file.content_id", "left" );

					$this->db->where( 'content_decoded_file.file_id', $row->file_id );

					$check = $this->db->get( 'content_decoded_file' )->row_array();

					if( !empty( $check ) ){
						$data['existing-records'][$row->file_id]['upload'] = ( array ) $row;
						$data['existing-records'][$row->file_id]['cacti'] = ( array ) $check;
					} else {
						$data['new-records'][$row->file_id]['upload'] = ( array ) $row;
						$data['new-records'][$row->file_id]['cacti'] = false;
					}
				}
				$result = $data;
			}
		}
		return $result;
	}


	public function su_process_decoded_files( $account_id = false, $post_data = false ){
		$result = ( object ) [
			"success" 	=> false,
			"data" 		=> false,
			"message" 	=> false
		];

		if( !empty( $account_id ) && !empty( $post_data ) ){
			if( !empty( $post_data['decoded_action'] ) && !empty( $post_data['decoded_files_upload'] ) ){
				$actioned 			= false;

				switch( strtolower( html_escape( $post_data['decoded_action'] ) ) ){
					case "update" :
						// search and update
						$actioned = $this->_su_update_existing( $account_id, $post_data['decoded_files_upload'] );
						break;

					// case "add" :
					default :
						// add new ones
						$actioned = $this->_su_create_new( $account_id, $post_data['decoded_files_upload'] );
				}

				## at least one entry fully actioned
				if( !empty( $actioned['fully_actioned'] ) && ( count( $actioned['fully_actioned'] ) > 0 ) ){

					$result->success 	= true;
					$result->message 	= "<strong>".( count( $actioned['fully_actioned'] ) )."</strong> entries out of <strong>".( $actioned['entries_to_process'] )."</strong> have been fully processed";

				} else {
					$result->success 	= false;
					$result->message 	= "No entries have been fully processed";
				}
				$result->data			= $actioned;

			} else {
				$result->message 			= "Action and Files data are required";
			}
		}

		return $result;
	}


	private function _su_update_existing( $account_id, $post_data ){
		$result = false;

		if( !empty( $account_id ) && !empty( $post_data ) ){

			$counter 						= 0;
			$actioned 						= [];
			$actioned['entries_to_process'] = 0;
			foreach( $post_data as $key => $row ){
				if( $row['checked'] == 1 ){

					$upload_row_data = false;
					$upload_row_data = $this->db->get_where( "su_decoded_files_tmp_upload", ["upload_id" => $row['upload_id']] )->row();

					if( !empty( $upload_row_data ) ){

						## If we do have the basic data (File ID and Content ID), and it is not a test profile...
						## Test profiles: Avengers Endgame (ID:1), BodyCam (ID:880), Mulan (ID:919), iRobot (ID:963), The Wedding Smashers (ID:978), Sanctum (ID:545)
						if( !empty( $upload_row_data->file_id ) && !empty( ( int ) $upload_row_data->content_id ) && !in_array( ( int ) $upload_row_data->content_id, [1, 880, 919, 963, 978, 545] ) ){

							$actioned['entries_to_process']++;

							## If we do have more specific data (airtime_reference, easel_product_ref)...
							if( !empty( $upload_row_data->airtime_reference ) && !empty( $upload_row_data->easel_product_ref ) ){

								## Possible values for encoding status taken from Easel API: [ not-encoded, encoding, encoded, encode-cancelled, encode-failed, unknown ]
								## So, we can use:	"airtime_encoded_status" => "success", OR  "airtime_encoded_status" => "encoded" . We're going for the latter.

								## ...we're going to update the decoded file data first:
								$file_upd_data = [];
								$file_upd_data = [
									"is_linked_with_airtime" 			=> 1,
									"airtime_product_reference" 		=> $upload_row_data->easel_product_ref,
									"airtime_product_linking_date" 		=> date( 'Y-m-d H:i:s' ),
									"airtime_product_linking_status" 	=> "success",
									"is_airtime_encoded" 				=> 1,
									"airtime_encoded_status" 			=> "encoded",
									"airtime_encoded_update_date" 		=> date( 'Y-m-d H:i:s' ),
									"airtime_reference" 				=> $upload_row_data->airtime_reference,
									"airtime_reference_updating_date"	=> date( 'Y-m-d H:i:s' ),
								];

								$file_upd_where = [];
								$file_upd_where = [
									"account_id"						=> $account_id,
									"file_id"							=> ( int ) $upload_row_data->file_id,
									"content_id"						=> ( int ) $upload_row_data->content_id,
								];

								$this->db->update( "content_decoded_file", $file_upd_data, $file_upd_where );

								if( $this->db->affected_rows() > 0 ){
									$actioned['file_actioned'][$counter] 		= array_merge( $file_upd_data, $file_upd_where );
									$decoded_file_actioned						= true;
								} else {
									$actioned['file_NOT_actioned'][$counter] 	= array_merge( $file_upd_data, $file_upd_where );
									$decoded_file_actioned						= false;
								}

								## next, going to update the content_film profile:
								$content_film_upd_data = [];
								$content_film_upd_data = [
									"airtime_state"								=> ( !empty( $upload_row_data->airtime_state ) && in_array( strtolower( $upload_row_data->airtime_state ), ["published"] ) ) ? "published" : "offline" ,
									"external_content_ref"						=> $upload_row_data->easel_product_ref,
									"external_content_updated_on"				=> date( 'Y-m-d H:i:s' ),
									"is_verified_for_airtime"					=> 1
								];

								if( !empty( $upload_row_data->file_type ) && in_array( strtolower( $upload_row_data->file_type ), ["movie", "film", "feature"] ) ){
									$content_film_upd_data['airtime_feature_file_id'] = $upload_row_data->file_id;
								} else {
									$content_film_upd_data['airtime_trailer_file_id'] = $upload_row_data->file_id;
								}

								$content_film_upd_where = [];
								$content_film_upd_where = [
									"account_id"						=> $account_id,
									"content_id"						=> ( int ) $upload_row_data->content_id,
								];

								$this->db->update( "content_film", $content_film_upd_data, $content_film_upd_where );

								if( $this->db->affected_rows() > 0 ){
									$actioned['content_film_actioned'][$counter] 		= array_merge( $content_film_upd_data, $content_film_upd_where );
									$content_film_actioned								= true;
								} else {
									$actioned['content_film_NOT_actioned'][$counter] 	= array_merge( $content_film_upd_data, $content_film_upd_where );
									$content_film_actioned								= false;
								}

								## next, going to update the content profile:
								$content_upd_data = [];
								$content_upd_data = [
									"is_airtime_asset" 		=> 1
								];

								$content_upd_where = [];
								$content_upd_where = [
									"account_id"						=> $account_id,
									"content_id"						=> ( int ) $upload_row_data->content_id,
								];

								$this->db->update( "content", $content_upd_data, $content_upd_where );

								if( $this->db->trans_status() !== false ){
									$actioned['content_actioned'][$counter] 		= array_merge( $content_upd_data, $content_upd_where );
									$content_actioned								= true;
								} else {
									$actioned['content_NOT_actioned'][$counter] 	= array_merge( $content_upd_data, $content_upd_where );
									$content_actioned								= false;
								}

								## at the end - remove the entry from the DB if processed successfully
								if( $decoded_file_actioned && $content_film_actioned && $content_actioned ){
									$delete_where = [];
									$delete_where = [
										"upload_id"							=> $upload_row_data->upload_id,
										// "account_id"						=> $account_id,
										// "content_id"						=> ( int ) $upload_row_data->content_id,
									];

									// https://www.codeigniter.com/userguide3/database/transactions.html
									$this->db->trans_begin();
									$this->db->delete( "su_decoded_files_tmp_upload", $delete_where );

									if( $this->db->trans_status() !== FALSE ){
										$this->db->trans_commit();
										$actioned['removed_from_tmp'][$counter] 	= $delete_where;
										$removed_from_tmp							= true;
									} else {
										$this->db->trans_rollback();
										$actioned['NOT_removed_from_tmp'][$counter] = $delete_where;
										$removed_from_tmp							= false;
									}
								} else {
									// something was missing - leaving the entry in the DB
								}

								if( $decoded_file_actioned && $content_film_actioned && $content_actioned && $removed_from_tmp ){
									$actioned['fully_actioned'][$counter] = $upload_row_data;
								}
							}
						}
					}
				}
				$counter++;
			}

			$result = $actioned;
		}

		return $result;
	}

	private function _su_create_new( $account_id, $post_data ){
		$result = false;

		if( !empty( $account_id ) && !empty( $post_data ) ){
			// foreach( $post_data as $key => $row ){
			// }
		}

		return $result;
	}



	/*
	*	A single use function to update movie profiles in CaCTI with Easel ID's obtain from the uploaded spreadsheet

	*/
	public function su_update_movies( $account_id = false, $post_data = false ){
		$result = ( object ) [
			"success" 	=> false,
			"data" 		=> false,
			"message" 	=> false
		];

		if( !empty( $account_id ) && !empty( $post_data ) && !empty( $post_data['upload_id'] ) ){
			$number_of_items_to_process 	= 0;
			$number_of_items_processed		= 0;
			$items_processed				= [];
			$items_unprocessed				= [];

			$number_of_items_to_process = ( is_countable( $post_data['upload_id'] ) ) ? count( $post_data['upload_id'] ) : 0 ;

			if( $number_of_items_to_process > 0 ){

				$this->db->where_in( "su_content_tmp_upload.upload_id", $post_data['upload_id'] );
				$res_query = $this->db->get( "su_content_tmp_upload" );

				if( $res_query->num_rows() > 0 ){

					foreach( $res_query->result() as $key => $row ){

						if( !empty( $row->id ) ){

							$this->db->reset_query();

							$sql_query = '';
							$sql_query = 'UPDATE content LEFT JOIN content_film ON content_film.content_id = content.content_id ';
							$sql_query .= ' SET content_film.external_content_ref = "'.$row->id;
							$sql_query .= '", content_film.modified_by = '.$this->ion_auth->_current_user->id;
							$sql_query .= ', content.modified_by = '.$this->ion_auth->_current_user->id;
							$sql_query .= ', is_airtime_asset = "Yes" ';
							$sql_query .= ' WHERE content_film.asset_code = "'.$row->reference;
							$sql_query .= '" AND content_film.account_id = '.$account_id;

							$this->db->query( $sql_query );
							$query_db = $this->db->last_query();

							if( $this->db->trans_status() !== false ){
								// if( $this->db->affected_rows() > 0 ){ ## alternative approach to track verified updates
								$number_of_items_processed++;
								$items_processed[$key]['reason'] 	= "processed successfully";
								$items_processed[$key]['data'] 		= $row;
							} else {
								$items_unprocessed[$key]['reason'] 	= "db update failed";
								$items_unprocessed[$key]['data'] 	= $row;
							}
						} else {
							$items_unprocessed[$key]['reason'] 	= "missing Easel id in spreadsheet";
							$items_unprocessed[$key]['data'] 	= $row;
						}
					}


					$items = [];

					$items['processed'] 	= $items_processed;
					$items['unprocessed'] 	= $items_unprocessed;
					$result->data			= $items;

					$message 				= '<span class="bold">'.$number_of_items_processed.'</span> out of <span class="bold">'.$number_of_items_to_process.'</span> has been processed';
					$result->message 		= $message;

					if( $number_of_items_processed > 0 ){
						$result->success = true;
					}

					log_message('error', json_encode( ["SU-su_update_movies-RESULT_object" => $result] ) );

				} else {
					$result->message = "No items from the upload table";
				}

			} else {
				$result->message = "Incorrect number of items to process";
			}

		} else {
			$result->message = "Missing required data";
		}

		return $result;
	}
	## the function logic:
		// we've received the data from the form - a bunch of accepted / ticket upload ID's from the su_content_tmp_upload table
		// the goal is:
		// - count how many items is to process
		// - for each one: read the upload line from the su_content_tmp_upload table
		// - take the product reference, i. e. theoryofeverything
		// - find if in CaCTI
		// - check if there is no conflict, if not - update the CaCTI with the new Easel reference,
		// - count processed++

	## do we need any additional functionalities? any helper function?



	/*
	*	To get Age Classification(s) from DB.
	*	Considering only Age Classification(s) with the reference to Easel as valid
	*/
	public function get_age_classifications( $account_id, $where ){
		$result = false;

		if( !empty( $account_id ) ){
			if( !empty( $where ) ){
				$where = convert_to_array( $where );

				if( !empty( $where ) ){

					if( !empty( $where['classification_name'] ) ){
						$classification_name = $where['classification_name'];
						$this->db->where( "classification_name", $classification_name );
						unset( $where['classification_name'] );
					}

					if( !empty( $where ) ){
						$this->db->where( $where );
					}
				}
			}

			$this->db->where( "age_classification.active", 1 );
			$this->db->where( "age_classification.archived !=", 1 );

			$where_not_empty = '( age_classification.easel_id !="" AND age_classification.easel_id IS NOT NULL )';
			$this->db->where( $where_not_empty );

			$this->db->select( "*", false );

			$query = $this->db->get( "age_classification" );

			if( $query->num_rows() > 0 ){
				$result = $query->result();
				$this->session->set_flashdata( 'message', 'Age Classification(s) found' );
			} else {
				$this->session->set_flashdata( 'message', 'No results' );
			}
		} else {
			$this->session->set_flashdata( 'message', 'Missing required data' );
		}

		return $result;
	}



	/*
	*	To get Age Rating(s) from DB.
	*	Considering only Age Rating(s) with reference to Easel as valid
	*/
	public function get_age_rating( $account_id, $where ){
		$result = false;

		if( !empty( $account_id ) ){

			$return_plain_array = $age_classification_id = $age_rating_id = false;

			if( !empty( $where ) ){
				$where = convert_to_array( $where );

				if( !empty( $where ) ){

					if( !empty( $where['return_plain_array'] ) && ( $where['return_plain_array'] == 'yes' ) ){
						$return_plain_array = 'yes';
						unset( $where['return_plain_array'] );
					}

					if( !empty( $where['age_rating_id'] ) ){
						$age_rating_id = $where['age_rating_id'];
						$this->db->where_in( "age_rating.age_rating_id", $age_rating_id );
						unset( $where['age_rating_id'] );
					}

					if( !empty( $where['age_classification_id'] ) ){
						$age_classification_id = $where['age_classification_id'];
						$this->db->where( "age_rating.age_classification_id", $age_classification_id );
						unset( $where['age_classification_id'] );
					}

					if( !empty( $where ) ){
						$this->db->where( $where );
					}
				}
			}

			$this->db->select( "age_rating.*", false );
			$this->db->select( "age_classification.age_classification_name", false );

			$this->db->join( "age_classification", "age_classification.age_classification_id = age_rating.age_classification_id", "false" );

			$this->db->where( "age_rating.active", 1 );
			$this->db->where( "age_rating.archived !=", 1 );

			$where_not_empty = '( age_rating.easel_id !="" AND age_rating.easel_id IS NOT NULL )';
			$this->db->where( $where_not_empty );

			$this->db->order_by( "age_rating.custom_order", 'ASC' );

			$query = $this->db->get( "age_rating" );

			if( $query->num_rows() > 0 ){
				if( $return_plain_array && $return_plain_array == 'yes' ){
					foreach( $query->result() as $row ){
						$result[] = $row->easel_id;
					}
				} else if( $age_rating_id ) {
					$result = $query->row();
				} else {
					$result = $query->result();
				}
				$this->session->set_flashdata( 'message', 'Age Rating(s) found' );
			} else {
				$this->session->set_flashdata( 'message', 'No results' );
			}
		} else {
			$this->session->set_flashdata( 'message', 'Missing required data' );
		}

		return $result;
	}



	/*
	*	Generate PDF data base on provided:
	*	Considering only Age Rating(s) with reference to Easel as valid
	*/
	public function generate_pdf_data( $account_id = false, $territory_id = false, $provider_ids = false, $product_name = false, $limit = false ){
		$result = false;

		if( !empty( $account_id ) ){
			if( !empty( $territory_id ) && !empty( $provider_ids ) && !empty( $product_name ) ){
				$product_type_ids 	= [];
				$pdf_type 			= false;

				$provider_ids 		= json_decode( $provider_ids );

				if( !isset( $provider_ids ) || !is_array( $provider_ids ) || empty( $provider_ids ) ){
					$this->session->set_flashdata( 'message', 'Error processing the Provider IDs' );
					return $result;
				}

				## product name validation
				$this->db->select( "LOWER( setting.setting_value ) `setting_value`", false );

				$this->db->join( "setting_name", "setting_name.setting_name_id = setting.setting_name_id", "left" );

				$this->db->where( "setting_name.setting_name_group", "2_product_type" );
				$this->db->where( "setting.is_active", 1 );

				$arch_setting = "( ( setting.archived != 1 ) || ( setting.archived is NULL ) )";
				$this->db->where( $arch_setting );

				$arch_setting_name = "( ( setting_name.archived != 1 ) || ( setting_name.archived is NULL ) )";
				$this->db->where( $arch_setting_name );

				$setting_query = $this->db->get( "setting" );

				if( $setting_query->num_rows() > 0 ){
					$product_types = array_unique( single_array_from_arrays( $setting_query->result_array(), 'setting_value' ) );
				} else {
					$this->session->set_flashdata( 'message', 'No active Product Type found' );
					return $result;
				}

				if( !empty( $product_types ) ){
					if( in_array( strtolower( $product_name ), $product_types ) ){

						switch( strtolower( $product_name ) ){
							case "airtime":
								$pdf_type = strtolower( $product_name );
								break;

							case "vod":
								$pdf_type = strtolower( $product_name );
								break;

							default:
								$this->session->set_flashdata( 'message', 'There is no PDF generation for this product type' );
							return $result;
						}

					} else {
						$this->session->set_flashdata( 'message', 'Product ID not registered in the system' );
						return $result;
					}

				} else {
					$this->session->set_flashdata( 'message', 'Error processing Product list' );
					return $result;
				}

				if( !empty( $pdf_type ) ){

					$this->db->select( "content_film.content_id, content_film.is_verified_for_airtime, content_film.external_content_ref, content_film.airtime_state", false );
					$this->db->select( "content_film.title, content_film.genre `unpacked_genres`, content_film.age_rating_id `unpacked_rating`, content_film.tagline, content_film.actors, CONCAT( content_film.running_time, ' mins' ) `running_time`", false );

					$this->db->select( "content.is_content_active, content.is_airtime_asset", false );
					$this->db->select( "content_clearance.clearance_start_date, content_clearance.territory_id", false );
					$this->db->select( "age_rating.age_rating_image_url", false );


					$this->db->join( "content", "content_film.content_id = content.content_id", "left" );
					$this->db->join( "content_clearance", "content_film.content_id = content_clearance.content_id", "left" );
					$this->db->join( "age_rating", "content_film.age_rating_id = age_rating.age_rating_id", "left" );

					$this->db->where( "content.is_content_active", 1 );

					## territory limit
					$this->db->where( "content_clearance.territory_id", $territory_id );

					## provider limit
					$this->db->where_in( "content.content_provider_id", $provider_ids );

					## clearance date limit
					$this->db->where( "content_clearance.clearance_start_date <", date( 'Y-m-d H:i:s' ) );

					## not archived
					$content_archived = "( ( content_film.archived != 1 ) || ( content_film.archived IS NULL ) )";
					$this->db->where( $content_archived );

					if( !empty( $limit ) ){
						$this->db->limit( $limit );
					}

					## if it is an 'AIRTIME' type of the PDF
					if( strtolower( $pdf_type ) == "airtime" ){

						## if it is 'published' on Airtime
						$this->db->where( "content_film.airtime_state", "published" );

						## if it has an Easel reference
						$this->db->where( "content_film.external_content_ref !=", "" );
					}

					$query = $this->db->get( "content_film" );

					if( $query->num_rows() > 0 ){
						foreach( $query->result() as $key => $row ){
							$output[$key] 			= $row;

							## Adding Genres to the object
							$output[$key]->genre = false;
							if( !empty( $row->unpacked_genres ) ){

								$unpacked_genres = false;
								$unpacked_genres = json_decode( $row->unpacked_genres );

								if( is_array( $unpacked_genres ) ){
									$this->db->select( "genre.genre_name", false );
									$this->db->where_in( "genre.genre_id", $unpacked_genres );

									$where_arch = "( ( genre.archived != 1 ) || ( genre.archived IS NULL ) )";
									$this->db->where( $where_arch );
									$genre_query = $this->db->get( "genre" );

									if( $genre_query->num_rows() > 0 ){
										$output[$key]->genre = single_array_from_arrays( $genre_query->result_array(), 'genre_name' );
									}
								}
							}
							unset( $row->unpacked_genres );

							## Adding Age Rating Image to the object
							if( !empty( $row->age_rating_image_url ) ){
								$output[$key]->age_rating_image = AGE_RATING_IMAGE_PATH_PDF.$row->age_rating_image_url;
							} else {
								$output[$key]->age_rating_image = false;
							}
							unset( $row->unpacked_rating );
							unset( $row->age_rating_image_url );

							## Standard Image
							## - possible separate function	- get_image_location( content_id = false, image_type = "standard" )
							$this->db->select( "document_location", false );

							$this->db->where( "content_document_uploads.content_id", $row->content_id );
							$this->db->where( "content_document_uploads.doc_file_type", "standard" );

							$where_doc_arch = "( ( content_document_uploads.archived != 1 ) || ( content_document_uploads.archived IS NULL ) )";
							$this->db->where( $where_doc_arch );

							$this->db->limit( 1 );

							$this->db->order_by( "content_document_uploads.document_id DESC" );

							$st_image_query = $this->db->get( "content_document_uploads" );

							if( $st_image_query->num_rows() > 0 ){
								$output[$key]->standard_image_url = $st_image_query->row()->document_location;
							} else {
								$output[$key]->standard_image_url = false;
							}


							## Languages - logic - TBC
							$this->db->select( "stream_id, decoded_file_id", false );
							$this->db->select( "language_symbol", false );

							$this->db->join( "content_language_phrase_language", "content_language_phrase_language.language_id = content_decoded_stream.language_id", "left" );

							$this->db->where( "codec_type", "audio" );

							$where_stream_arch = "( ( content_decoded_stream.archived != 1 ) || ( content_decoded_stream.archived IS NULL ) )";
							$this->db->where( $where_stream_arch );

							$where_in_string = "( decoded_file_id IN ( SELECT file_id FROM `content_decoded_file` WHERE `decoded_file_type_id` = 1 AND `main_record` = 1 AND `content_id` = $row->content_id AND ( ( content_decoded_file.archived != 1 ) || ( content_decoded_file.archived IS NULL ) ) ) )";
							$this->db->where( $where_in_string );

							$lang_query = $this->db->get( "content_decoded_stream" );


							if( $lang_query->num_rows() > 0 ){
								$output[$key]->languages 	= array_unique( single_array_from_arrays( $lang_query->result_array(), 'language_symbol' ) );
							} else {
								$output[$key]->languages 	= false;
							}
						}

						// $result['counters']	= count( $output );
						// $result['data'] 	= $output;
						$result 	= $output;

						$this->session->set_flashdata( 'message', 'PDF data generated' );
					} else {
						$this->session->set_flashdata( 'message', 'No result found for given criteria' );
					}
				} else {
					$this->session->set_flashdata( 'message', 'Error processing the PDF type' );
				}
			} else {
				$this->session->set_flashdata( 'message', 'Missing required data' );
			}
		} else {
			$this->session->set_flashdata( 'message', 'Account ID required' );
		}

		return $result;
	}
}