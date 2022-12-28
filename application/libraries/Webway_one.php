<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  SSIDCommon
* Author: Simpyl SID
* Created:  10.02.2018
* Description:  This is library for all system interactions with Web Way One.
*/

class Webway_one{

	function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->database();
	}

		
	public function doCurl( $url=false, $postdata=false, $options=array() ){
		$result = false;
		if( $url && $postdata ){
			
 			if( !empty( $options['auth_token'] ) ){
				$http_headers = array(
					"authorization: Bearer ".$options['auth_token'],
					"cache-control: no-cache"
				);
			} else {
				$http_headers = array(
					"Cache-control: no-store, no-cache, must-revalidate",
				);
			}
			$postdata = urldecode($postdata);
			
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_HEADER, 0);
			curl_setopt ($ch, CURLOPT_HTTPHEADER, $http_headers);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
			curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt ($ch, CURLOPT_REFERER, $url);   
			if( isset($options['method']) && strtolower($options['method']) == 'get' ){
				curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, 'GET');
				curl_setopt ($ch, CURLOPT_URL, "$url?$postdata" );
				curl_setopt ($ch, CURLOPT_HTTPGET, 1);
				curl_setopt ($ch, CURLOPT_POST, 0);
			}else{
				curl_setopt ($ch, CURLOPT_URL, $url);
				curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata);
				curl_setopt ($ch, CURLOPT_POST, 1);
			}
			
			$executed = curl_exec($ch);

			if ( 0 === strpos(bin2hex($executed), 'efbbbf') ) {
				$executed = substr($executed, 3);
			}
			
			if( strpos( $executed, "</pre>" ) !== false ){
				$executed = explode("</pre>",$executed );
				$result   = ( !empty($executed[1]) ) ? json_decode( $executed[1] ) : false;
			}else{
				$result   = json_decode( $executed );
			}
			
			curl_close ($ch);
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
		if( !empty($post_data) ){			
			reset($post_data);
			$first_key = key($post_data);
			
			foreach( $post_data as $column=>$value ){
				$value = ( is_array($value) ) ? json_encode($value) : $value;
				if( $first_key == $column ){
					$result .= $column."=".$value;
				}else{
					$result .= "&".$column."=".$value;
				}				
			}
			$result = urlencode($result);
		}
		return $result;
	}
	
	/* Filter table data */
	public function _filter_data($table, $data){
		$filtered_data = array();
		$columns = $this->CI->db->list_fields($table);

		if( is_array( $data ) ){
			foreach( $columns as $column ){
				if (array_key_exists($column, $data))
					$filtered_data[$column] = $data[$column];
			}
		} elseif ( is_object( $data ) ){
			foreach ( $columns as $column ){
				if( array_key_exists( $column, $data ) ){
					$filtered_data[$column] = $data->$column;
				}
			}
		}
		return $filtered_data;
	}
	
	public function get_site_compliance( $site_count = 0, $statuses = false ){
		$result = [
			'total_sites'=> $site_count,
			'sites_ok'=> 0,
			'sites_not_ok'=> 0,
			'compliance'=> 0
		];
		if( ( $site_count > 0 ) && $statuses ){
			$okay_statuses = site_ok_statuses();
			$sites_okay 	   = [];
			
			foreach( $statuses as $status ){
				if( in_array( strtolower($status), array_map( 'strtolower', $okay_statuses ) ) ){
					$sites_okay[] = $status;
				}
			}
			
			if( count($sites_okay) == $site_count ){
				$result['sites_ok'] 	= count($sites_okay);
				$result['sites_not_ok'] = 0;
				$result['compliance']   = ( count($sites_okay) / $site_count )*100;
			}else{
				$result['sites_ok'] 	= count($sites_okay);
				$result['sites_not_ok'] = $site_count - count($sites_okay);
				$result['compliance']   = ( count($sites_okay) / $site_count )*100;
			}
		}
		return $result;
	}
}