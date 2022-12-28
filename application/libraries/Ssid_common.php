<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  SSIDCommon
* Author: Love Digital TV
* Created:  10.10.2017
* Description:  This is library for commonly used functionality in the system.
*/

class Ssid_common{

	function __construct(){
		$this->ci =& get_instance();
		$this->ci->load->database();
		$this->api_end_point = api_end_point();
		$this->load = clone load_class('Loader');
	}

	public function doCurl( $url=false, $postdata=false, $options=array() ){
		$result = false;
		if( $url && $postdata ){

			$user_agent = $_SERVER['HTTP_USER_AGENT'];

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
			$postdata = urldecode( $postdata );

			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_HEADER, 0);
			curl_setopt ($ch, CURLOPT_HTTPHEADER, $http_headers);
			curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);
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

				$value = ( in_array( $column, ['password','password_confirm'] ) ) ? $value : $this->clean_htmlentities( $value );

				if( $first_key == $column ){
					$value   = ( is_array( $value ) ) ? json_encode( $value ) : $value;
					$result .= $column."=".$value;
				} else {
					$value   = ( is_array( $value ) ) ? json_encode( $value ) : $value;
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
	public function _filter_data($table, $data){
		$filtered_data = array();
		$columns = $this->ci->db->list_fields($table);

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
	* Encode activation code
	*/
	public function _encode_activation_code( $data = false ){
		$activation_code = false;
		if( !empty( $data ) ){
			$activation_code = urlencode( base64_encode( base64_encode( json_encode( $data ) ) ) );
		}
		return $activation_code;
	}

	/*
	* Decode activation code
	*/
	public function _decode_activation_code( $activation_code = false ){
		$data = false;
		if( !empty( $activation_code ) ){
			$data = json_decode( base64_decode( base64_decode( urldecode( $activation_code ) ) ) );
		}
		return $data;
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


	/* Get permitted actions */
	public function permitted_actions( $permissions = false ){
		$result = [];
		if( !empty( $permissions ) ){
			$permissions = is_object($permissions) ? (array)$permissions : $permissions;
			foreach( $permissions as $k=> $action ){
				$role 	  = explode( '_', $action );
				$result[] = $role[1];
			}
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

	/*
	* Get list of countries (or single by ID or code )
	*/
	public function get_countries( $country_id = false, $country_code = false, $iso3 = false ){
		$result = null;
		if( $country_id ){
			$this->ci->db->where( 'country_id', $country_id );
		}

		if( $country_code ){
			$this->ci->db->where( 'country_code', $country_code );
		}

		if( $iso3 ){
			$this->ci->db->where( 'iso3', $iso3 );
		}

		$query  = $this->ci->db
			->order_by( 'country_name' )
			->get( 'countries' );

		if( $query->num_rows() > 0 ){
			if( $country_id ){
				$result = $query->result()[0];
			}else{
				$result = $query->result();
			}
		}
		return $result;
	}

	/** Create PDF Document
	/* @html_content can be a path to a view to render or an html string
	/* @$setup this array contains data need for setting up some common info Recipient details, company details, referrence numbers etc.
	/* @is)template if @html_content is a file path, this value needs to be set to true, otherwise leave as false
	**/
	public function create_pdf( $html_content = false, $setup = false, $is_template = false ){

		if( !empty( $html_content ) && !empty( $setup ) ){

			if( $is_template ){
				ob_start();
				$this->load->view( $html_content, $setup );
				$setup['document_setup']['document_content'] = ob_get_contents();
				ob_end_clean();
			}else{
				$setup['document_setup']['document_content'] = $html_content;
			}

			ob_start();
			$this->load->view( '/pdf-templates/generic-template', $setup );
			$html_content = ob_get_contents();
			ob_end_clean();

			$data = [
				'html_content' 		=> $html_content,
				'document_setup' 	=> $setup['document_setup']
			];

			$this->load->view( '/pdf-templates/pdf_creater_mpdf.php', $data );
		}
	}
	
	public function create_pdf_from_template(  $template_name = false, $document_setup = false ){
		
		if( !empty($template_name) && !empty( $document_setup )){
			
			$document_setup = [
				'template_name' 		=> $template_name,
				'document_setup'		=> $document_setup
			];
			$this->load->view( '/evipdf/evipdf_generator.php', $document_setup );
		}
	}

	/** Update location record **/
	public function update_location_record( $account_id = false, $location_id = false, $asset_id = false, $site_id = false, $vehicle_id = false, $vehicle_reg = false ){

		$result = false;

		if( !empty( $account_id ) &&  !empty( $location_id ) ){

			$location_exists = $this->ci->db->get_where( 'locations', [ 'locations.account_id'=>$account_id, 'locations.location_id'=>$location_id ] )->row();

			if( !empty( $location_exists ) ){

				$where = [];

				if( !empty( $asset_id ) ){
					$where['locations_shared.asset_id'] = $asset_id;
				}

				if( !empty( $site_id ) ){
					$where['locations_shared.site_id'] = $site_id;
				}

				if( !empty( $vehicle_id ) ){
					$where['locations_shared.vehicle_id'] = $vehicle_id;
				}

				if( !empty( $vehicle_reg ) ){
					$where['locations_shared.vehicle_reg'] = $vehicle_reg;
				}

				$check_exists = $this->ci->db->get_where( 'locations_shared', $where )->row();

				$data = array_merge( [ 'locations_shared.location_id'=>$location_id ], $where );

				if( !empty( $check_exists ) ){
					$this->ci->db->where( $where )
						->update( 'locations_shared', $data );

				}else{
					$this->ci->db->insert( 'locations_shared', $data );
				}

				$result = ( $this->ci->db->trans_status() !== FALSE ) ? true : false;

			}else{
				$this->ci->session->set_flashdata('message','No data found matching the supplied Location ID.');
			}

		}else{
			$this->ci->session->set_flashdata('message','Missing required information.');
		}
		return $result;
	}

	/** Add New Location **/
	public function add_new_location( $account_id = false, $location_data = false ){

		$result = null;

		if( !empty( $account_id ) && !empty( $location_data  ) ){

			$data = ['account_id'=>$account_id];
			foreach( $location_data as $col => $value ){
				$data[$col] = $value;
			}

			if( !empty( $data['building_name'] ) ){
				$data['location_name'] = $data['building_name'].' - '.$data['location_name'];
			}

			$where = [ 'account_id'=>$account_id, 'location_group'=>$data['location_group'], 'location_name'=>$data['location_name'] ];
			$check_exists = $this->ci->db->get_where( 'locations', $where )->row();

			$data = $this->_filter_data( 'locations', $data );

			if( !empty( $check_exists  ) ){
				$this->ci->db->where( $where )
					->update( 'locations', $data );
					$this->ci->session->set_flashdata('message','This location already exists, record has been updated successfully.');
					$result = $check_exists;
			}else{
				$this->ci->db->insert( 'locations', $data );
				$this->ci->session->set_flashdata('message','New location added successfully.');
				$data['location_id'] = $this->ci->db->insert_id();
				$result = $data;
			}

		}else{
			$this->ci->session->set_flashdata('message','Error! Missin required information.');
		}

		return $result;
	}

	/** Get locations **/
	public function get_locations( $account_id = false, $location_group = false, $asset_id = false, $site_id = false, $vehicle_id = false, $vehicle_reg = false, $grouped = false ){

		$result = null;

		if( $account_id ){
			$this->ci->db->select( 'locations.*' )
				->join( 'locations_shared', 'locations.location_id = locations_shared.location_id' , 'left' )
				->where( 'account_id', $account_id )
				->where( 'is_active', 1 );

			if( $location_group ){
				$this->ci->db->where( 'locations.location_group', $location_group );
			}

			if( $asset_id ){
				$this->ci->db->where( 'locations_shared.asset_id', $asset_id );
			}

			if( $site_id ){
				$this->ci->db->where( 'locations_shared.site_id', $site_id );
			}

			if( $vehicle_id ){
				$this->ci->db->where( 'locations_shared.vehicle_id', $asset_id );
			}

			if( $vehicle_reg ){
				$this->ci->db->where( 'locations_shared.vehicle_reg', $vehicle_reg );
			}

			$query = $this->ci->db->get( 'locations' );

			if( $query->num_rows() > 0 ){

				if( $grouped ){

					foreach( $query->result() as $row ){
						$result[$row->location_group][] = $row;
					}

				}else{
					$result = $query->result();
				}

				$this->ci->session->set_flashdata('message','Location records found.');

			}

		}else{
			$this->ci->session->set_flashdata('message','No records found matching your creteria.');
		}

		return $result;
	}

	/** Get location groups **/
	public function get_location_groups( $account_id = false ){

		$result = false;

		if( $account_id ){

			$result = [
				'asset',
				'site',
				'vehicle'
			];

			$this->ci->session->set_flashdata('message','Location groups found.');

		}else{

			$this->ci->session->set_flashdata('message','Main Account ID is required.');

		}
		return $result;
	}

	/** Prepare Data for DB Processing **/
	public function _data_prepare( $data = false ){
		$result = false;
		if( !empty( $data ) ){
			$data = ( !is_array( $data ) ) ? json_decode( urldecode( $data ) ) : $data;
			$data = ( is_object( $data ) ) ? object_to_array( $data ) : $data;

			$prepared = [];
			foreach( $data as $key => $value ){
				switch( $key ){
					case ( in_array( $key, format_name_columns() ) ):
						$value = format_name( $value );
						break;
					case ( in_array( $key, format_email_columns() )):
						$value = format_email( $value );
						break;
					case ( in_array( $key, format_number_columns() ) ):
						$value = format_number( $value );
						break;
					case ( in_array( $key, format_currency_columns() ) ):
						$value = str_replace( ',', '', $value );
						break;
					case ( in_array( $key, format_boolean_columns() ) ):
						$value = format_boolean( $value );
						break;
					case ( in_array( $key, format_date_columns() ) ):
						$value = valid_date( $value ) ? format_datetime_db( $value ) : null;
						break;
					case ( in_array($key, format_long_date_columns() ) ):
						$value = format_datetime_db( $value );
						break;
					default:
						$value = ( !is_array( $value ) ) ? trim( $value ) : $value;
						break;
				}
				$prepared[$key] = ( !is_array( $value ) ) ? trim( $value ) : $value;;
			}

			$result = $prepared;
			$this->ci->session->set_flashdata('message','Data prepared successfully.');
		}else{
			$this->ci->session->set_flashdata('message','Error: Missing required data!');
		}
		return $result;
	}


	/** Sanitize an order_by **/
	public function _clean_order_by( $order_by = false, $table = false  ){
		$result = false;
		if( !empty( $order_by ) && !empty( $table ) ){
			$sort 	= ( !empty( $order_by['sort'] )  )  ? define_sort( $order_by['sort'], $table ) : false;
			if( !empty( $sort ) && ( $this->_column_exists( $sort, $table ) ) ){
				$order 	= ( !empty( $order_by['order'] )  ) ? define_order( $order_by['order'] ) : 'ASC';
				#$result = $sort.' '.$order;

				$result = 'ISNULL( '.$sort.' ), '.$sort.' '.$order;
			}
		}
		return $result;
	}

	/** Check if a Column exists in a particular table **/
	public function _column_exists( $column = false, $table = false  ){
		$result = false;
		if( !empty( $column ) && !empty( $table ) ){
			if( strpos( $column, '.' ) !== false ) {
				$split  = explode( '.', $column );
				$column = ( !empty( $split[1] ) ) ? $split[1] : false;
			}
			$result = ( !empty( $column ) && ( $this->ci->db->field_exists( $column, $table ) ) ) ? true : false;

		}
		return $result;

	}

	/** Generate a Random String / Number **/
	function random_str( $length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' )
	{
		$pieces = [];
		$max = mb_strlen($keyspace, '8bit') - 1;
		for ($i = 0; $i < $length; ++$i) {
			$pieces []= $keyspace[random_int(0, $max)];
		}
		return implode('', $pieces);
	}
	
	// Create GUID (Globally Unique Identifier)
	function create_guid() { 
        $guid = '';
        $namespace = rand(11111, 99999);
        $uid = uniqid('', true);
        $data = $namespace;
        $data .= $_SERVER['REQUEST_TIME'];
        $data .= $_SERVER['HTTP_USER_AGENT'];
        $data .= $_SERVER['REMOTE_ADDR'];
        $data .= $_SERVER['REMOTE_PORT'];
        $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
        $guid = substr($hash,  0,  8) . '-' .
                substr($hash,  8,  4) . '-' .
                substr($hash, 12,  4) . '-' .
                substr($hash, 16,  4) . '-' .
                substr($hash, 20, 12);
        return strtolower( $guid );
    }
}
