<?php defined('BASEPATH') OR exit('No direct script access allowed');

	/*
	* Return the API Endpoint
	*/
	if ( ! function_exists('api_end_point')){
		function api_end_point(){
			return base_url() . SERVICE_END_POINT;
		}
	}

	function format_name( $var = false ){
		if( $var ){
			$var = ucwords( strtolower( trim($var) ) );
		}
		return $var;
	}

	function format_email( $var = false ){
		if( $var ){
			$var = strtolower( trim( filter_var( $var, FILTER_SANITIZE_EMAIL ) ) );

		}
		return $var;
	}

	function format_number( $var = false ){
		if( $var ){
			$var = preg_replace('/\s+/', '',$var);
		}
		return $var;
	}

	function format_datetime_db( $var = false ){
		if( $var ){
			$var = date( 'Y-m-d H:i:s', strtotime( str_replace( '/', '-', $var ) ) );
		}
		return $var;
	}

	function format_datetime_client( $var = false ){
		if( $var ){
			$var = date( 'd/m/Y H:i:s', strtotime( $var ) );
		}
		return $var;
	}

	function format_email_columns(){
		return [
			'email',
			'client_email',
			'account_email',
		];
	}

	function format_name_columns(){
		return [
			'client_name',
			'first_name',
			'last_name',
			'contact_first_name',
			'contact_last_name',
			'address_line_1',
			'address_line_2',
			'address_line_3',
			'address_town',
			'address_county',
			'address_contact_first_name',
			'address_contact_last_name',
			'account_first_name',
			'account_last_name',
		];
	}

	function format_number_columns(){
		return [
			'mobile',
			'telephone',
			'contact_mobile',
			'contact_telephone',
			'account_mobile',
			'account_telephone',		
		];
	}

	function format_date_columns(){
		return [
			'date_from',
			'date_to',
			'date_created',
			'date_actioned',
			'date_modified',
			'job_date',
			'diary_date',
			'start_date',
			'end_date',
			'mot_expiry',
			'tax_expiry',
			'last_audit_date',
			'next_audit_date',
			'end_of_life_date',
			'date_of_birth',
			'leave_date',
			'purchase_date',
			'action_due_date',
			'due_date',
			'schedule_date',
			'note_date',
			'project_start_date',
			'project_finish_date',
		];
	}

	function object_to_array( $obj ){
		$arr = json_decode( json_encode($obj ), true);
		return $arr;
	}

	function array_to_object($arr){
		$obj = json_decode( json_encode( $arr ) );
		return $obj;
	}

	function format_date_db( $var = false ){
		if( $var ){
			$var = date( 'Y-m-d', strtotime( str_replace( '/', '-', $var ) ) );
		}
		return $var;
	}

	/**
	* Format date for use by clients
	*/
	function format_date_client( $var = false ){
		if( $var ){
			$var = date( 'd/m/Y', strtotime( $var ) );
		}
		return $var;
	}

	/**
	* Get a list of availalable address types
	*/
	function address_types(){

		return [
			'Billing',
			'Business',
			'Delivery',
			'Invoice',
			'Residential',
			'Site'
		];
	}

	/**
	* Get a list of Job durations */
	function job_durations(){
		return [
			'0.5'=>'30 Minutes',
			'1.0'=>'1 Hour',
			'1.5'=>'1.5 Hours',
			'2.0'=>'2 Hours',
			'2.5'=>'2.5 Hours',
			'3.0'=>'3 Hours',
			'3.5'=>'3.5 Hours',
			'4.0'=>'4 Hours',
			'4.5'=>'4.5 Hours',
			'5.0'=>'5 Hours',
			'5.5'=>'5.5 Hours',
			'6.0'=>'6 Hours',
			'6.5'=>'6.5 Hours',
			'7.0'=>'7 Hours',
			'7.5'=>'7.5 Hours',
			'8.0'=>'8 Hours',
		];
	}

		/**
	* Get a list of Job statuses */
	function job_statuses(){
		return [
			'Assigned',
			'Cancelled',
			'Failed',
			'In Progress',
			'Successful',
			'Un-assigned',
		];
	}

	/** Format likes into a where condition **/
	function format_like_to_where( $where ){
		$result = false;
		if( $where ){
			$sql = '( ';
			foreach( $where as $column=>$value ){
				$sql .= $column.' LIKE "%'.$value.'%" OR ';
			}
			$sql .= ' ) ';
			if( strrpos($sql, 'OR') !== false){
				$sql = substr_replace($sql, '', strrpos($sql, 'OR'), strlen('OR'));
			}
			$result = $sql;
		}
		return $result;
	}

	/** Parse an Array to CSV string, ready to be written to file as a CSV **/
	function array_to_csv( $masterArray, $fileHeaders=false){
		$delimiter = ",";
		$newline = "\r\n";

		$result ="";
		if(!empty($masterArray)){

			if($fileHeaders){
				## Apply headers to the main array
				array_unshift($masterArray, $fileHeaders);
			}

			foreach ($masterArray as $fields) {
				if($fields){
					foreach ($fields as $field) {
						$field = str_replace( ',', '.', $field); //Replace all commas with  periods (.)
						$field = str_replace(array("\n", "\r"), '', $field);
						$result .= $field.$delimiter;
					}
				}
				$result .= $newline;
			}
		}
		return $result;
	}

	function site_ok_statuses(){
		return [
			'OK',
			'No Fault'
		];
	}

	function valid_date( $date = false ){

		if( !empty( $date ) ){
			$date = str_replace( "/", "-", $date );
			$invalid_dates = [ '0000-00-00', '0000-00-00 00:00:00', '1970-01-01', '1970-01-01 00:00:00' ];
			if( !in_array( $date, $invalid_dates ) ){
				$valid_date = date( 'Y-m-d H:i:s', strtotime( $date ) );
				if( !in_array( $valid_date, $invalid_dates ) ){
					return true;
				}else{
					return false;
				}
			} else {
				return false;
			}
		}else{
			return false;
		}
	}

	function account_terms_and_conditions(){
		return $terms = '<ul>
		   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>
		   <li>Aliquam tincidunt mauris eu risus.</li>
		   <li>Vestibulum auctor dapibus neque.</li>
		</ul><br/>
		<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>';
	}

	function format_boolean_columns(){
		return [
			'is_active',
			'is_supervisor',
			'is_insured',
			'has_road_assistance',
			'has_camera_installed',
			'ordered',
		];
	}

	function format_boolean( $var = false ){
		if( ( strtolower( $var ) == 'yes' ) || ( $var == 1 ) ){
			$var = true;
		} else {
			$var = false;
		}
		return $var;
	}

	function job_failcodes(){
		return [
			'001'=>'Fail code 1',
			'002'=>'Fail code 2',
			'003'=>'Fail code 3',
		];
	}

	function format_long_date_columns(){
		return [
			'wf_start_date',
			'wf_end_date',
			'reminder_date',
			'wf_date_created',
			'wf_date_modified',
			'return_date',
			'supply_date',
			'camera_install_date',
			'date_created',
			'event_date',
			'action_start_date',
			'action_end_date',
			'workflow_start_date',
			'workflow_end_date',
			'entry_start_date',
			'entry_end_date',
			'a_tag_start_time',
			'a_tag_end_time',
			'expected_completion_date'
		];
	}

	function _pagination_config( $base_url = false, $module_name = false, $method_name = false ){

		
		if( !empty( $base_url ) ){
			$config['base_url']   	= base_url() . $base_url;
		} else {
			$config['base_url']   	= base_url() . 'webapp/user/users';
		}
		
		$config["uri_segment"] 		= 3;

		$config['num_links'] 		  = 10;
		$config['use_page_numbers']   = TRUE;
		$config['reuse_query_string'] = TRUE;

		$config['full_tag_open'] 	= '<ul class="pagination pull-right">';
		$config['full_tag_close'] 	= '</ul>';

		$config['first_link']     = '&laquo; First';
		$config['first_tag_open'] = '<li class="prev page">';
		$config['first_tag_close']= '</li>';

		$config['last_link'] = 'Last &raquo;';
		$config['last_tag_open'] = '<li class="next page">';
		$config['last_tag_close']= '</li>';

		$config['next_link'] = 'Next';
		$config['next_tag_open'] = '<li class="next page">';
		$config['next_tag_close']= '</li>';

		/* $config['prev_link'] = '&larr; Previous'; */
		$config['prev_link'] = 'Previous';
		$config['prev_tag_open'] = '<li class="prev page">';
		$config['prev_tag_close']= '</li>';

		$config['cur_tag_open']  = '<li><a class="pgn-link" href="">';
		$config['cur_tag_close'] = '</a></li>';

		$config['num_tag_open']  = '<li class="page">';
		$config['num_tag_close'] = '</li>';
		return $config;
	}
	
	/**
	* Get a list of emergency contact relationsips list
	*/
	function contact_relationships(){

		return [
			'Self'=>'Self',
			'Sibling'=>'Sibling',
			'Partner / Spouse'=>'Partner / Spouse',
			'Parent'=>'Parent',
			'Grand Parent'=>'Grand Parent',			
			'Aunt / Uncle'=>'Aunt / Uncle',
			'Nephew / Niece'=>'Nephew / Niece',
			'Son / Daughter'=>'Son / Daughter',
			'Son / Daughter (In-law)'=>'Son / Daughter (In-law)',
			'Father / Mother (In-law)'=>'Father / Mother (In-law)',
			'Other'=>'Other',
		];
	}
	
	/**
	* Convert an image into base64
	* @img_path has to be localised (./images/your-image.png ) and not remote (http://your-domain.com/images/your-image.png)
	*/
	function encode_img_base64( $img_path = false, $img_type = 'png' ){
		if( $img_path ){
			//convert image into Binary data
			$img_data = fopen ( $img_path, 'rb' );
			$img_size = filesize ( $img_path );
			$binary_image = fread ( $image_data, $img_size );
			fclose ( $img_data );
		
			//Build the src string to place inside your img tag
			$img_src = "data:image/".$img_type.";base64,".str_replace ("\n", "", base64_encode ( $binary_image ) );
			return $img_src;
		}
		return false;
	}
	
	/** Convert int to number in words **/
	function number_to_words( $number ) {

		$hyphen      = '-';
		$conjunction = ' and ';
		$separator   = ', ';
		$negative    = 'negative ';
		$decimal     = ' point ';
		$dictionary  = array(
			0                   => 'zero',
			1                   => 'one',
			2                   => 'two',
			3                   => 'three',
			4                   => 'four',
			5                   => 'five',
			6                   => 'six',
			7                   => 'seven',
			8                   => 'eight',
			9                   => 'nine',
			10                  => 'ten',
			11                  => 'eleven',
			12                  => 'twelve',
			13                  => 'thirteen',
			14                  => 'fourteen',
			15                  => 'fifteen',
			16                  => 'sixteen',
			17                  => 'seventeen',
			18                  => 'eighteen',
			19                  => 'nineteen',
			20                  => 'twenty',
			30                  => 'thirty',
			40                  => 'fourty',
			50                  => 'fifty',
			60                  => 'sixty',
			70                  => 'seventy',
			80                  => 'eighty',
			90                  => 'ninety',
			100                 => 'hundred',
			1000                => 'thousand',
			1000000             => 'million',
			1000000000          => 'billion',
			1000000000000       => 'trillion',
			1000000000000000    => 'quadrillion',
			1000000000000000000 => 'quintillion'
		);

		if (!is_numeric($number)) {
			return false;
		}

		if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
			// overflow
			trigger_error(
				'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
				E_USER_WARNING
			);
			return false;
		}

		if ($number < 0) {
			return $negative . convert_number_to_words(abs($number));
		}

		$string = $fraction = null;

		if (strpos($number, '.') !== false) {
			list($number, $fraction) = explode('.', $number);
		}

		switch (true) {
			case $number < 21:
				$string = $dictionary[$number];
				break;
			case $number < 100:
				$tens   = ((int) ($number / 10)) * 10;
				$units  = $number % 10;
				$string = $dictionary[$tens];
				if ($units) {
					$string .= $hyphen . $dictionary[$units];
				}
				break;
			case $number < 1000:
				$hundreds  = $number / 100;
				$remainder = $number % 100;
				$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
				if ($remainder) {
					$string .= $conjunction . convert_number_to_words($remainder);
				}
				break;
			default:
				$baseUnit = pow(1000, floor(log($number, 1000)));
				$numBaseUnits = (int) ($number / $baseUnit);
				$remainder = $number % $baseUnit;
				$string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
				if ($remainder) {
					$string .= $remainder < 100 ? $conjunction : $separator;
					$string .= convert_number_to_words($remainder);
				}
				break;
		}

		if (null !== $fraction && is_numeric($fraction)) {
			$string .= $decimal;
			$words = array();
			foreach (str_split((string) $fraction) as $number) {
				$words[] = $dictionary[$number];
			}
			$string .= implode(' ', $words);
		}

		return $string;
	}
	
	/** Check if string is valid Json **/
	function is_json( $json_str = false ){
		if( !empty( $json_str ) && !is_array( $json_str ) ){
			$is_json = json_decode( $json_str );
			if( json_last_error() === JSON_ERROR_NONE ) {
				return true;
			}else{
				return false;
			}
		}
		return false;		
	}
	
	/** array verify **/
	function verify_array( $data = false ){
		if( !empty( $data ) ){
			$data 	= ( !is_array( $data ) ) ? json_decode( $data ) : $data;
			$data 	= ( is_object( $data ) ) ? object_to_array( $data ) : $data;
		}
		return $data;
	}
	
	/** convert string to array, comman separated **/
	function str_to_array( $data = false ){
		if( !empty( $data ) ){
			if( is_string( $data ) ) {
				$data = str_replace( '.', ',', $data); //Replace all periods (.) with commas
				if( strpos( $data, ',') !== false ){
					$data = explode( ',', $data );
				}
			}
		}
		return $data;
	}
	
	
	/** 
	* Convert CSV File to Array
	*/
	function csv_file_to_array( $uploadfile ){
	
		$result = false;

		if( !empty( $uploadfile ) ){
		
			$rawcsv  		= array();	//This is a temp array used for parsing.
			$parsedCsvArr   = array(); //This is what we send to update the DB with the inpiut records

			//Process CSV file aka successfull records
			$row = 1;
			if ( ( $handle = fopen( $uploadfile , 'r') ) !== FALSE ) {
			
				while ( ( $data = fgetcsv($handle, 1000, ',' ) ) !== FALSE ) {
					$rawcsv[] = $data;				
				}
				fclose( $handle );
			}

			//Loop through the raw records and assign the fields to keys from the first records NOTE.
			foreach( $rawcsv as $k=>$csvrows ){
				$rebuiltcsv = array();
				if( $k !==0 ){
					foreach( $csvrows as $r=>$datarec ){
						$key = trim( array_search( $r, array_flip( $rawcsv[0] ) ) );
						$rebuiltcsv[$key] = $datarec;									
					}
					$parsedCsvArr[] = $rebuiltcsv; //Load each rebuilt csv record as a key=value pair
				}				
			}
			
			if( !empty( $parsedCsvArr ) ){
				$result = $parsedCsvArr;
			}			
		}

		return $result;
	}
	
	/** Check if user is System admin or module admin or **/
	function admin_check( $system_admin = false, $module_admin = false, $module_tab_admin = false ){
		$result = false;
		if( !empty( $system_admin ) ){
			return true;
		}
		
		if( !empty( $module_admin ) && !empty( $module_admin ) ){
			return true;
		}
		
		if( !empty( $module_tab_admin ) ){
			return true;
		}
		return false;
	}
	
	/** Get time ago from timestamp **/
	function timeago( $time, $tense='ago' ) {
		// declaring periods as static function var for future use
		static $periods = array('year', 'month', 'day', 'hour', 'minute', 'second');

		// checking time format
		if( !( strtotime( $time ) > 0 ) ) {
			return trigger_error( "Wrong time format: '$time'", E_USER_ERROR );
		}

		// getting diff between now and time
		$now  = new DateTime('now');
		$time = new DateTime($time);
		$diff = $now->diff($time)->format('%y %m %d %h %i %s');
		// combining diff with periods
		$diff = explode(' ', $diff);
		$diff = array_combine($periods, $diff);
		// filtering zero periods from diff
		$diff = array_filter($diff);
		// getting first period and value
		$period = key($diff);
		$value  = current($diff);

		// if input time was equal now, value will be 0, so checking it
		if( !$value ) {
			$period = 'seconds';
			$value  = 0;
		} else {
			// converting days to weeks
			if($period=='day' && $value>=7) {
				$period = 'week';
				$value  = floor($value/7);
			}
			// adding 's' to period for human readability
			if($value>1) {
				$period .= 's';
			}
		}

		// returning timeago
		return "$value $period $tense";
	}
	
	/** Strip white spaces from a string **/
	function strip_all_whitespace( $str = false ){
		if( !empty( $str ) ){
			return preg_replace( '/\s+/', '', trim( urldecode( $str ) ) );
		}
		return false;
	}
	
	/** Strip all white spaces and special characters, to leave a lean string **/
	function lean_string( $str = false ){
		if( !empty( $str ) ){
			return preg_replace( '/[^a-zA-Z]+/', '', trim( urldecode( $str ) ) );
		}
		return false;
	}
	
	/** Re-order tabs for display in profile **/
	function reorder_tabs( $module_tabs = false ){
		$data = false;
		if( !empty( $module_tabs ) ){
			$module_total 	= ( is_object( $module_tabs ) ) ? count( object_to_array( $module_tabs ) ) : count( $module_tabs );
			$reordered_tabs = $more_list = [];
			if( $module_total > 6 ){ //6 is the max per layer in a 12-column grid system
				$counter = 1;
				foreach( $module_tabs as $k => $module ){
					if( $counter <= 5 ){
						$reordered_tabs[$counter] = $module;
					}else{
						$reordered_tabs['more'][] = $module;
						$more_list[] = $module->module_item_tab;
					}
					$counter++;
				}
				$data['module_tabs'] = $reordered_tabs;
				$data['more_list']   = $more_list;
			}
		}
		return $data;
	}
	
	function format_currency_columns(){
		return [
			'lease_price',
			'purchase_price',
			'estimated_repair_cost',
			'cost_item_value',
			'job_rate',
		];
	}
	
	function convert_to_array( $data = false ){
		
		if( !empty( $data ) ){
			$data = ( is_string( $data ) && !is_json( $data ) ) ? ( ( strpos( $data, ',' ) !== false ) ? ( explode( ",", $data ) ): [$data] ) : $data;
			$data = ( !is_array( $data ) ) ? json_decode( $data ) : $data;
			#$data = ( is_string( $data ) && ( strpos( $data, ',' ) !== false ) ) ? ( explode( ",", $data ) ) : ( is_string( $data ) ? [ $data ] : $data );
			$data = ( is_object( $data ) ) ? object_to_array( $data ) : $data;
		}
		return $data;
	}
	
	function define_order( $order ){
		$result = 'ASC';
		if( !empty( $order ) ){
			switch( strtolower( $order ) ){
				case 'a':
				case 'asc':
				case 'low':
				case 'lowest':
				case 'ascend':
				case 'ascending':
					$result = 'ASC';
					break;
				case 'd':
				case 'z':
				case 'desc':
				case 'high':
				case 'highest':
				case 'descend':
				case 'descending':
					$result = 'DESC';
					break;
			}
		}
		return $result;
	}
	
	function define_sort( $sort = false, $primary_tbl = false ){
		$result = false;
		if( !empty( $sort ) ){
			
			if( strpos( $sort, '.' ) !== false ) {
				$result = $sort;
			}else{
				if( !empty( $primary_tbl ) ){
					$result = trim( $primary_tbl ).'.'.trim( $sort );
				}else{
					$result = $sort; //This is likely to cause ambiguity, need to think of how to prevent this
				}
			}
			
		}
		return $result;
	}
	
	/* Function to check if the date isn't empty */
	function validate_date( $date = false ){
		if( !empty( $date ) ){
			$invalid_dates = [ '0000-00-00', '0000-00-00 00:00:00', '1970-01-01', '1970-01-01 00:00:00' ];
			if( !in_array( $date, $invalid_dates ) ){
				return true;
			}
		}
		return false;
	}
	
	/** Generic list of file types **/
	function generic_file_types(){
		return array_to_object( [
			/*'csv'=>[
				'file_group'=>'Excel',
				'file_type'=>'CSV',
				'file_name'=>'Comma Delimited Value (*.csv)',
				'extention'=>'.csv'
				
			],
			'xls'=>[
				'file_group'=>'Excel',
				'file_type'=>'xls',
				'file_name'=>'Excel Workbook (97 - 2003) (*.xls)',
				'extention'=>'.xls'
				
			],
			'xlsx'=>[
				'file_group'=>'Excel',
				'file_type'=>'xlsx',
				'file_name'=>'Excel Workbook (*.xlsx)',
				'extention'=>'.xlsx'
				
			],
			'doc'=>[
				'file_group'=>'Word',
				'file_type'=>'doc',
				'file_name'=>'Word 97 - 2013 Document (*.doc)',
				'extention'=>'.doc'
				
			],
			'docx'=>[
				'file_group'=>'Word',
				'file_type'=>'docx',
				'file_name'=>'Word Document (*.docx)',
				'extention'=>'.docx',
				
			],
			'pdf'=>[
				'file_group'=>'PDF',
				'file_type'=>'pdf',
				'file_name'=>'PDF (*.pdf)',
				'extention'=>'.pdf',
			],*/
			'png'=>[
				'file_group'=>'Image',
				'file_type'=>'png',
				'file_name'=>'PNG (*.png)',
				'extention'=>'.png'
			],
			'jpg'=>[
				'file_group'=>'Image',
				'file_type'=>'jpg',
				'file_name'=>'JPG (*.jpg)',
				'extention'=>'.png'
			],
			'jpeg'=>[
				'file_group'=>'Image',
				'file_type'=>'jpeg',
				'file_name'=>'JPEG (*.jpeg)',
				'extention'=>'.jpeg'
			]
		] );
	}
	
	function asset_sub_categories(){
		return [
			'comm device'=>'Communication device (Phones, PCs, Laptops)',
			'plant'	=>'Plant (Harnesses, Helmets etc.)',
			'device'=>'Installable assets item',
			'asset'	=>'Any asset item',
			'system'=>'Any System asset'
		];
	}
	
	/*
	* This should move to the DB if we decide that each account should manage their own or if we want to extent the functionality to do more
	*/
	function sub_categories(){
		return array_to_object( [
			[
				'id'				=>'1',
				'sub_category'		=>'comm device',
				'sub_category_desc'	=>'Communication device (Phones, PCs, Laptops)',
				'account_id'		=>null
			],
			[
				'id'				=>'2',
				'sub_category'		=>'plant',
				'sub_category_desc'	=>'Plant (Harnesses, Helmets etc.)',
				'account_id'		=>null
			],
			[
				'id'				=>'3',
				'sub_category'		=>'device',
				'sub_category_desc'	=>'Installable assets item',
				'account_id'		=>null
			],
			[
				'id'				=>'4',
				'sub_category'		=>'asset',
				'sub_category_desc'	=>'Any asset item',
				'account_id'		=>null
			],
			[
				'id'=>'5',
				'sub_category'		=>'system',
				'sub_category_desc'	=>'System Type',
				'account_id'		=>null
			]
		] );
	}
	
	//System static asset-groups. If this get too much usage, consider putting it in the DB
	function status_groups(){
		return array_to_object( [
			'assigned'=>[
				'group_name'=>'assigned',
				'group_desc'=>'Assigned - Item is assigned to something or someone',
				'group_colour'=>'#26B99A',
			],
			'faulty'=>[
				'group_name'=>'faulty',
				'group_desc'=>'Faulty - Item is faulty or broken',
				'group_colour'=>'#F93711',
			],
			'lost'=>[
				'group_name'=>'lost',
				'group_desc'=>'Lost - Item is lost or can\'t be found',
				'group_colour'=>'#19181D',
			],
			'recalled'=>[
				'group_name'=>'recalled',
				'group_desc'=>'Recalled - Item has been returned or recalled to supplier',
				'group_colour'=>'#0D95E8',
			],
			'repair'=>[
				'group_name'=>'repair',
				'group_desc'=>'Repair - Item is in repair or marked as needing repair',
				'group_colour'=>'#f89c1c',
			],
			'retired'=>[
				'group_name'=>'retired',
				'group_desc'=>'Retired - Item has been retired, can no longer be used',
				'group_colour'=>'#5C268D',
			],
			'unassigned'=>[
				'group_name'=>'unassigned',
				'group_desc'=>'Unassigned - Item is not assigned to anything or anyone',
				'group_colour'=>'#F2F2F2',
			]
		] );
	}
	
	function defined_slas(){
		return array_to_object( [
			['value'=>1, 'description'=>'Within 1 hour'],
			['value'=>2, 'description'=>'Within 2 hours'],
			['value'=>3, 'description'=>'Within 3 hours'],
			['value'=>4, 'description'=>'Within 4 hours'],
			['value'=>24, 'description'=>'Within 24 hours']
		] );
	}
	
	function experiece_levels(){
		return array_to_object( [
			'Fundamental Awareness (basic knowledge)',
			'Novice (limited experience)',
			'Intermediate (practical application)',
			'Advanced (applied theory)',
			'Expert (recognized authority)'
		] );
	}
	
	function job_type_sub_types(){
		
		return array_to_object(
			[
				'Service Call'	=> 'Service Call',
				'Inspection'	=> 'Inspection',
				'Installation'	=> 'Installation',
				'Invoice'	=> 'Invoice'
			]
		);
		
	}
	
	function preset_shifts_patterns(){
		return array_to_object( [
				['start_time'=>'06:00', 'finish_time'=>'15:00'],
				['start_time'=>'06:30', 'finish_time'=>'15:30'],
				['start_time'=>'07:00', 'finish_time'=>'16:00'],
				['start_time'=>'07:30', 'finish_time'=>'16:30'],
				['start_time'=>'08:00', 'finish_time'=>'17:00'],
				['start_time'=>'08:30', 'finish_time'=>'17:30'],
				['start_time'=>'09:00', 'finish_time'=>'17:00'],
				['start_time'=>'09:00', 'finish_time'=>'18:00'],
				['start_time'=>'09:30', 'finish_time'=>'18:30'],
				['start_time'=>'10:00', 'finish_time'=>'19:00']
			]
		);
	}
	
	function shift_allowed_times(){
		return [
			'06:00', '06:30', '07:00', '07:30', '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00','14:30', 
			'15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00', '21:30', '22:00'  
		];
	}
	
	function week_days(){
		return array_to_object( [
			'1'=>['day_key'=>'1', 'day_short'=>'Mon', 'day_full'=>'Monday'], 
			'2'=>['day_key'=>'2', 'day_short'=>'Tue', 'day_full'=>'Tuesday'], 
			'3'=>['day_key'=>'3', 'day_short'=>'Wed', 'day_full'=>'Wednesday'], 
			'4'=>['day_key'=>'4', 'day_short'=>'Thu', 'day_full'=>'Thursday'], 
			'5'=>['day_key'=>'5', 'day_short'=>'Fri', 'day_full'=>'Friday'], 
			'6'=>['day_key'=>'6', 'day_short'=>'Sat', 'day_full'=>'Saturday'], 
			'7'=>['day_key'=>'7', 'day_short'=>'Sun', 'day_full'=>'Sunday'],
		] );
	}
	
	/* @param string|\DateTime|null $date
	* @return \DateTime
	*/
	function get_start_of_week_date( $date = null )
	{
		if ( $date instanceof \DateTime ) {
			$date = clone $date;
		} else if ( !$date ) {
			$date = new \DateTime();
		} else {
			$date = new \DateTime( $date );
		}
		
		$date->setTime( 0, 0, 0 );
		
		if ( $date->format('N') == 1 ) {
			// If the date is already a Monday, return it as-is
			return $date->format( 'Y-m-d' );
			#return $date; 
		} else {
			// Otherwise, return the date of the nearest Monday in the past
			// This includes Sunday in the previous week instead of it being the start of a new week
			$last_monday = $date->modify( 'last monday' );
			return $last_monday->format( 'Y-m-d' );
		}
	}
	
	/* Resort a multi-dimensional array upto second level */
	function re_sort_array( $values = false, $column = false, $sort_type = 'asort' ){
		
		$new_array = [];
		
		if( !empty( $values ) && !empty( $column ) ){
			
			$order_columns = array_column( $values, $column );
			
			switch( $sort_type ){
				default:
				case 'asort':
					asort( $order_columns );
					break;
			}

			foreach( $order_columns as $key => $column_name ){
				$new_array[] = $values[$key];
			}
			
		} else {
			$new_array = $values;
		}

		return $new_array;
		
	}
	
	/** Get List of ETA Statuses **/
	function eta_statuses(){
		return array_to_object([
			[
				'id'			=>'1',
				'status_name'	=>'ETA Confirmed',
				'status_ref'	=>'etaconfirmed',
			],
			[
				'id'			=>'2',
				'status_name'	=>'ETA Not Suitable',
				'status_ref'	=>'etanotsuitable',
			],
			[
				'id'			=>'3',
				'status_name'	=>'Left Voice Message',
				'status_ref'	=>'leftvoicemessage',
			],
			[
				'id'			=>'4',
				'status_name'	=>'Incorrect Contact Details',
				'status_ref'	=>'IncorrectContactDetails',
			],
			[
				'id'			=>'5',
				'status_name'	=>'Contact Attempt Failed',
				'status_ref'	=>'ContactAttetmpFailed',
			]
		]);
	}
	
	/** Convert number to Ordinal 1st / 2nd, 3rd etc. **/
	function ordinal( $number ) {
		$ends = array('th','st','nd','rd','th','th','th','th','th','th');
		if ( ( ( $number % 100 ) >= 11) && ( ( $number%100 ) <= 13 ) )
			return $number. 'th';
		else
			return $number. $ends[$number % 10];
	}
	
	/** Get numbers only from String **/
	function numbers_only( $string ){
		return str_replace( ['+', '-'], '', filter_var( $string, FILTER_SANITIZE_NUMBER_INT ) );
	}
	
	function category_icons (){
		return array_to_object([ 
			'fire'=>[
				'category_name'	=> 'Fire',
				'category_icon'	=> '/assets/images/dashboard-icons/schedules-fire.png',
			],
			'gas'=>[
				'category_name'	=> 'Gas',
				'category_icon'	=> '/assets/images/dashboard-icons/schedules-gas.png',
			],
			'water'=>[
				'category_name'	=> 'Water',
				'category_icon'	=> '/assets/images/dashboard-icons/schedules-water.png',
			],
			'electric'=>[
				'category_name'	=> 'Electric',
				'category_icon'	=> '/assets/images/dashboard-icons/schedules-electric.png',
			],
			'lifts'=>[
				'category_name'	=> 'Lifts',
				'category_icon'	=> '/assets/images/dashboard-icons/schedules-lifts.png',
			],
			'lighting'=>[
				'category_name'	=> 'Lighting',
				'category_icon'	=> '/assets/images/dashboard-icons/schedules-lighting.png',
			],
			'security'=>[
				'category_name'	=> 'Lighting',
				'category_icon'	=> '/assets/images/dashboard-icons/schedules-security.png',
			],
			'other'=>[
				'category_name'	=> 'Other',
				'category_icon'	=> '/assets/images/dashboard-icons/schedules-generic.png',
			]
			
		]);
	}
	
	/** Periodic Month Ranges **/
	function overdue_month_ranges (){
		return array_to_object([ 
			[
				'range'			=> '0-3',
				'range_name'	=> '0 - 3 Months Overdue',
				'range_min'		=> '0',
				'range_max'		=> '3',
				'range_rank'	=> '1',
			],
			[
				'range'			=> '3-6',
				'range_name'	=> '3 - 6 Months Overdue',
				'range_min'		=> '3',
				'range_max'		=> '6',
				'range_rank'	=> '2',
			],
			[
				'range'			=> '6+',
				'range_name'	=> '6+ Months Overdue',
				'range_min'		=> '6',
				'range_max'		=> '12',
				'range_rank'	=> '3',
			],
		]);
	}
	
	
	/** Get time Elapsed (Time ago)**/
	function time_elapsed( $datetime = false, $full = false ) {
		$datetime 	= !empty( $datetime ) ? $datetime : new DateTime;
		$now 		= new DateTime;
		$ago 		= new DateTime( $datetime );
		$diff 		= $now->diff($ago );

		$diff->w 	= floor( $diff->d / 7 );
		$diff->d 	-= $diff->w * 7;

		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);
		
		foreach ( $string as $k => &$v ) {
			if ( $diff->$k ) {
				$v = $diff->$k . ' ' . $v . ( $diff->$k > 1 ? 's' : '' );
			} else {
				unset( $string[$k] );
			}
		}

		if ( !$full ) $string = array_slice( $string, 0, 1 );
		return $string ? implode( ', ', $string ) . ' ago' : 'just now';
	}
	
	/** Get Initials from a String **/
	function string_initials( $str = false ) {
		
		if( !empty( $str ) ){
			$result 		= '';
			$exploded_str 	= explode( ' ', $str );
			if( count( $exploded_str ) == 1 ){
				$result = $exploded_str[0];
			} else {
				foreach ( $exploded_str as $key => $word ){
					if( $key == 0 && ( strlen( $word ) <= 2 ) ){
						$result .= strtoupper( $word[0] );
						if( !empty( $word[1] ) ){
							$result .= strtoupper( $word[1] );
						}
					} else {
						$result .= strtoupper( $word[0] );
					}
				}
			}
			return $result;
		}
		return false;
	}
	
	/* 
	*	Function to check if the string is a valid JSON object and will not be converted into NAN or INF
	*/
	function isValidJson( $strJson ){
		$result = false;
		if( !empty( $strJson ) ){
			$decoded	= json_decode( $strJson);
			$result 	= ( (json_last_error() === JSON_ERROR_NONE ) && ( !is_nan( $decoded ) && !is_infinite( $decoded ) ) );
		}
		return $result;
	}
	
	/** Explode a String at the last occurence of a Sub-string **/
	function explode_on_last_occurrence( $explodeAt, $string ) {
		$explodeAt	= strtolower( $explodeAt );
		$string		= strtolower( $string );
		
		$explode 	= explode( $explodeAt, strtolower( $string ) );
		$count 		= count( $explode );
		$counter 	= 0;
		$string 	= null;

		while ( $counter < $count-1 ) {
			if ( $counter < $count-2 ) {
				$string .= $explode[$counter].$explodeAt;
			} else {
				$string .= $explode[$counter];
			}
			$counter++;
		}

		return $string;

	}
	
	function encrypt_str( $q_string ) {
		$enc_iv 		= openssl_random_pseudo_bytes( openssl_cipher_iv_length( STRING_ENCRYPTION_CYPHER_METHOD ) );
		$encrypted_str  = openssl_encrypt( $q_string, STRING_ENCRYPTION_CYPHER_METHOD, STRING_ENCRYPTION_KEY, 0, $enc_iv ) . "::" . bin2hex( $enc_iv );
		unset( $q_string, $cipher_method, $enc_iv );
		return $encrypted_str;
		
		// $qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( STRING_ENCRYPTION_KEY ), $q, MCRYPT_MODE_CBC, md5( md5( STRING_ENCRYPTION_KEY ) ) ) );
		// return( $qEncoded );
	}

	function decrypt_str( $q_string ) {
		list( $q_string, $enc_iv ) = explode( "::", $q_string );
		$decrypted_str 		= openssl_decrypt($q_string, STRING_ENCRYPTION_CYPHER_METHOD, STRING_ENCRYPTION_KEY, 0, hex2bin( $enc_iv ) );
		unset( $q_string, $enc_iv );
		return $decrypted_str;
	}
	
	/** Get Number of Months between 2 dates **/
	function _number_of_months( $date1, $date2 ){
		$d1 			= new DateTime( $date2 ); 
		$d2 			= new DateTime( $date1 );                                  
		$months 		= $d2->diff( $d1 ); 
		$months_since 	= ( ( $months->y ) * 12 ) + ( $months->m );
		return $months_since;
	}
	
	/**
	* Convert Date to ISO8601 format
	*/
	function convert_date_to_iso8601( $date_str = false ){
		if( !empty( $date_str ) ){
			$date_str = date( "Y-m-d\TH:i\Z", strtotime( date( 'Y-m-d', strtotime( $date_str ) ) ) );
		}
		return $date_str;
	}
	
	/**
	* Convert Date to ISO8601 format
	*/
	function datetime_to_iso8601( $date_str = false ){
		$dt = new DateTime( $date_str, new DateTimeZone('Europe/London') );
		#$dt->setTimezone( new DateTimeZone( 'Europe/London' ) );
		return $dt->format('Y-m-d\TH:i:s.v\Z');
	}
	
	/**
	* Convert time from minutes to ISO8601 Duration
	*/
	function minutes_to_iso8601_duration( $time_in_minutes = false ) {
		
		$str = false;
		
		if( !empty( $time_in_minutes ) ){
			$time_in_minutes = preg_replace( "/[^0-9.]/", "", $time_in_minutes );
			$time = strtotime( $time_in_minutes." minutes", 0 );
			
			$units = [
				"Y" => 365*24*3600,
				"D" =>     24*3600, 
				"H" =>        3600,
				"M" =>          60,
				"S" =>           1,
			];

			$str = "P";
			$istime = false;

			foreach ( $units as $unitName => &$unit ) {
				$quot  = intval( $time / $unit );
				$time -= $quot * $unit;
				$unit  = $quot;
				if ( $unit > 0) {
					if ( !$istime && in_array( $unitName, array( "H", "M", "S" ) ) ) { // There may be a better way to do this
						$str .= "T";
						$istime = true;
					}
					$str .= strval( $unit ) . $unitName;
				}
			}
		}

		return $str;
	}
	
	/**
	* Convert ISO 8601 Duration like P2DT15M33S
	* to a total value of seconds/minutes.
	*
	* @param string $iso8601_str
	*/
	function iso8601_duration_to_minutes( $iso8601_str = false, $in_seconds = false ) {
		
		$time = false;
		
		if( !empty( $iso8601_str ) ){

			$interval = new \DateInterval( $iso8601_str );

			$time_in_seconds = ( $interval->d * 24 * 60 * 60 ) +
				( $interval->h * 60 * 60) +
				( $interval->i * 60 ) +
				$interval->s;
				
			$time = ( !empty( $in_seconds ) ) ? $time_in_seconds : ( $time_in_seconds/60 );
		}
		
		return $time;
	}
	
	/**
	* Convert Date to Standard Date time
	*/
	function _datetime( $date_str = false ){
		
		if( $date_str ){
			$dt = new DateTime( $date_str );
		} else {
			$dt = new DateTime();
		}
		
		$dt->setTimezone( new DateTimeZone( 'Europe/London' ) );
		return $dt->format('Y-m-d H:i:s');
	}
	
	/**
	*	Replace underscores with spaces and uppercase first letter
	*/
	function underscore_to_space( $text = false ){
		if( !empty( $text ) ){
			return ucwords( str_replace( "_", " ", $text ) );
		} else {
			return false;
		}
	}