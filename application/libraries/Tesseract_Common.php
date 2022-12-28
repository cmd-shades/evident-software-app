<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:  Tesseract API Library
* Author: Evident Software
* Created:  06.12.2020
* Description:  This is library for processing all Tesseract API related calls.
*/

class Tesseract_Common{

	function __construct(){
		$this->ci =& get_instance();
		$this->ci->load->database();
		$this->tesseract_api_end_point 	= TESSERACT_BRIDGE_API_BASE_URL;
		$this->load = clone load_class( 'Loader' );
	}


	/* Dispatch an api request to the EASELTV API */
	public function api_dispatcher( $url_endpoint = false, $data = false, $options = false, $is_get_method = false ){
		$result = false;
		if( !empty( $url_endpoint ) ){
			
			$url_endpoint = $this->tesseract_api_end_point.$url_endpoint;

			if( $is_get_method ){
				if( $options ){
					$options = array_merge( $options, ['method'=>'GET'] );
				}else{
					$options = ['method'=>'GET'];
				}				
			} else {
				$options['method'] = !empty( $options['method'] ) ? $options['method'] : 'POST';
			}
			
			$postdata = ( is_array( $data ) ) ? json_encode( $data ) : $data;
			$result   = $this->doCurl( $url_endpoint, $postdata, $options );
			
		}
		
		return $result;
	}
	

	public function doCurl( $url=false, $postdata=false, $options=array() ){
		$result = false;
		if( $url ){
			
			if( !empty( $options ) && $options['auth_type'] == 'token' ){
				if( !empty( $options['auth_token'] ) ){
					$http_headers = array(
						'Content-Type:application/*+json',
						'authorization: Bearer '.$options['auth_token'],
						'cache-control: no-cache'
					);
				} else {
					$http_headers = array(
						'Content-Type:application/*+json',
						'Cache-control: no-store, no-cache, must-revalidate',
					);
				}				
			} else {
				$http_headers = array(
					'Content-Type:application/json',
					'Authorization: Basic '. base64_encode( EASEL_TV_API_AUTH_STRING )
				);
			}

			$ch = curl_init();
			curl_setopt ( $ch, CURLOPT_HEADER, 0 );
			curl_setopt ( $ch, CURLOPT_HTTPHEADER, $http_headers );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
			curl_setopt ( $ch, CURLOPT_TIMEOUT, 60 );
			curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, TRUE );
			curl_setopt ( $ch, CURLOPT_REFERER, $url );
			curl_setopt ( $ch, CURLOPT_URL, $url );
			
			if( !empty( $postdata ) ){
				
				if( !empty( $curl_file ) ){
					$postdata['file'] = $curl_file;
				}
				
				curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postdata );
			}
			
			## Switch over the Method
			switch( strtolower( $options['method'] ) ){
				case 'get':
					curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
					curl_setopt ( $ch, CURLOPT_POST, 0 );
					break;
				case 'post':
					curl_setopt ( $ch, CURLOPT_POST, 1 );
					break;
				case 'put':
					curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );
					break;
				case 'delete':
					curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'DELETE' );
					break;
				default:
					curl_setopt ( $ch, CURLOPT_POST, 1 );
					break;
			}

			$executed = curl_exec( $ch );
			
			if ( 0 === strpos( bin2hex( $executed ), 'efbbbf'  ) ){
				$executed = substr( $executed, 3 );
			}

			if( strpos( $executed, "</pre>" ) !== false ){
				$executed = explode("</pre>",$executed );
				$result   = ( !empty( $executed[1] ) ) ? json_decode( $executed[1] ) : false;
			} else {
				$result   = json_decode( $executed );
			}

			curl_close( $ch );
		}

		return $result;
	}


	public function check_dates( $date_from = false, $date_to = false ){

 		$today		= date( 'Y-m-d' );
		$date_from 	= date( 'Y-m-d', strtotime( str_replace( '/', '-', $date_from ) ) );
		$date_to 	= date( 'Y-m-d', strtotime( str_replace( '/', '-', $date_to ) ) );
		if( !empty( $date_from ) && !empty( $date_to ) ){
			if( ( ( $date_from < $today ) || ( $date_to < $today ) ) || ( $date_to < $date_from ) ){
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}


	public function date_difference( $date1 = false, $date2 = false ){
		if( !empty( $date1 ) && !empty( $date2 ) ){
			$datetime1 = new DateTime( $date1 );
			$datetime2 = new DateTime( $date2 );

			$difference = $datetime1->diff( $datetime2 );
			$days_total = $difference->d;
			return ( string )( $days_total+1 );
		} else {
			return false;
		}
	}

	/* Prepare post data to use for cURL */
	public function _prepare_curl_post_data( $post_data = false ){

		$result = '';
		if( !empty( $post_data ) ){
			reset( $post_data );
			$first_key = key($post_data);

			foreach( $post_data as $column=>$value ){

				$value = ( in_array( $column, ['password', 'password_confirm'] ) ) ? $value : $this->clean_htmlentities( $value );

				if( $first_key == $column ){
					$value = ( is_array($value) ) ? json_encode($value) : $value;
					$result .= $column."=".$value;
				} else {
					$value = ( is_array($value) ) ? json_encode($value) : $value;
					$result .= "&".$column."=".$value;
				}
			}
			$result = urlencode( ( $result ) );
		}

		return $result;
	}

	/* 
	*	This is to clean the incoming data from the forms. 
	*	It should successfully clean nested arrays with many levels 
	*/
	public function clean_htmlentities( $value = false ){

		if( empty( $value ) ){
			return $value;
		} else {
			if( is_array( $value ) ){
				foreach( array_keys( $value ) as $key ){
					$value[$key] = $this->clean_htmlentities( $value[$key] );
				}
			} else if( is_scalar( $value ) ){

				$json = ( json_decode( $value ) !== NULL ) ? json_decode( $value ) : $value ;

				if( is_array( $json ) ){
					$value = json_encode( $this->clean_htmlentities( $json ) );
				} else {
					$value = urlencode( htmlentities( $value ) );
				}
			} else {
				$value = $value;
			}
			return $value;
		}
	}

	/* Filter table data */
	public function _filter_data( $table, $data, $exempt_columns = false){
		$filtered_data = array();
		$columns = $this->ci->db->list_fields( $table );

		if( is_array( $data ) ){
			foreach( $columns as $column ){
				if( !empty( $exempt_columns ) && is_array( $exempt_columns ) ){
					if( array_key_exists( $column, $data ) && ( !in_array( $column, $exempt_columns ) ) ){
						$filtered_data[$column] = $data[$column];
					}
				} else {
					if( array_key_exists( $column, $data ) ){
						$filtered_data[$column] = $data[$column];
					}
				}
			}
		} elseif ( is_object( $data ) ){
			foreach ( $columns as $column ){
				if( !empty( $exempt_columns ) && is_array( $exempt_columns ) ){
					if( array_key_exists( $column, $data ) && ( !in_array( $column, $exempt_columns ) ) ){
						$filtered_data[$column] = $data->$column;
					}
				} else {
					if( array_key_exists( $column, $data ) ){
						$filtered_data[$column] = $data->$column;
					}
				}
			}
		}
		return $filtered_data;
	}

	/*
	* Reset the auto increment of a table after deleting some rows
	*/
	public function _reset_auto_increment( $table = false, $ai_column = false ){
		if( !empty( $table ) && !empty( $ai_column ) ){
			$ai		= 1;
			$query  = $this->ci->db->select( $ai_column )
				->order_by( $ai_column, 'desc' )
				->limit( 1 )
				->get( $table );
			if( $query->num_rows() > 0 ){
				$highest_record = $query->result()[0];
				$ai				= ( $highest_record->$ai_column ) + 1 ;
			}

			$sql = "ALTER table ".$table." AUTO_INCREMENT = ".$ai;
			$this->ci->db->query( $sql );
		}
		return true;
	}

	/**
	* Generate random password for signups
	**/
	public function generate_random_password(){
		$allowed_str  = '!*abcdefghijklmnopqrstuvwxyz@ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890#$_';
		$password 	  = array();
		$alpha_length = strlen( $allowed_str ) - 1; ;
		for ( $i = 0; $i < 8; $i++ ){
			$n = rand(0, $alpha_length);
			$password[] = $allowed_str[$n];
		}
		return implode( $password );
	}


	/*
	*	Common function to do an API Call
	*/
	public function api_call( $url = false, $postdata = false, $method = false ){
		$result = false;
		if( !empty( $url ) && !empty( $postdata ) ){
			$options['method']		= ( !empty( $method ) ) ? ( $method ) : 'POST' ;
			$options['auth_token']	= ( !empty( $this->ci->session->userdata['auth_data']->auth_token ) ) ? $this->ci->session->userdata['auth_data']->auth_token : false ;
			$postdata 				= $this->_prepare_curl_post_data( $postdata );
			$full_url 				= $this->api_end_point.$url;

			$result					= $this->doCurl( $full_url, $postdata, $options );
		}

		return $result;
	}

	/** Get list of class methods **/
	public function get_controller_methods( $controller_name = false ){
		$class_methods = null;
		if( !empty( $controller_name ) ){
			$class_methods = get_class_methods( $controller_name );
		}
		return $class_methods;
	}
	
}