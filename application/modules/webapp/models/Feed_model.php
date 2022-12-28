<?php if ( !defined( 'BASEPATH' ))exit( 'No direct script access allowed' );

class Feed_model extends CI_Model {

	function __construct(){
		parent::__construct();
    }

	## hardcoded values:
	public $alarms_array = [
		"LB"	=> [
			"text" 		=> "engineer mode",
			"zone"		=> "8008",
			"opposite"	=> "LX",
			"positive"	=> "no"
		],
		"LX"	=>[
			"text" 		=> "engineer exit",
			"zone"		=> "8008",
			"opposite"	=> "LB",
			"positive"	=> "yes"
		],
		"FR"	=>[
			"text" 		=> "fire alarm restore",
			"zone"		=> "8001",
			"opposite"	=> "FA",
			"positive"	=> "yes"
		],
		"FA"	=>[
			"text" 		=> "fire alarm",
			"zone"		=> "8001",
			"opposite"	=> "FR",
			"positive"	=> "no"
		],
		"YK"	=>[
			"text" 		=> "gprs communications trouble cleared",
			"zone"		=> "9015",
			"opposite"	=> "YC",
			"positive"	=> "yes"
		],
		"YC"	=>[
			"text" 		=> "gprs communications trouble",
			"zone"		=> "9015",
			"opposite"	=> "YK",
			"positive"	=> "no"
		],
		"YK"	=>[
			"text" 		=> "communications restored",
			"zone"		=> "9021",
			"opposite"	=> "YC",
			"positive"	=> "yes"
		],
		"YC"	=>[
			"text" 		=> "communications failed",
			"zone"		=> "9021",
			"opposite"	=> "YK",
			"positive"	=> "no"
		],
		"YC"	=>[
			"text" 		=> "ip communications trouble",
			"zone"		=> "9010",
			"opposite"	=> "YK",
			"positive"	=> "no"
		],
		"FV"	=>[
			"text" 		=> "fire fault restore",
			"zone"		=> "8002",
			"opposite"	=> "FT",
			"positive"	=> "yes"
		],
		"FT"	=>[
			"text" 		=> "fire fault",
			"zone"		=> "8002",
			"opposite"	=> "FV",
			"positive"	=> "no"
		],
		"XX"	=>[
			"text" 		=> "no status change",
			"zone"		=> "9999",
			"opposite"	=> "XX",
			"positive"	=> "yes"
		],
	];

	public $negative_codes_array = ["LB", "FA", "YC"];


	/*
	*	Open socket with WebWayOne using provided details
	*/
	public function opensocket(){
		if( fsockopen( WWO_SOCKET, 50572, $errno, $errstr, 30 ) == true ){
			return true;
		} else {
			return false;
		}
	}

	/*
	*	Check the latest status from the DataBase
	*/
	public function check_latest_status( $packet_id = false ){
		$result = false;

		$this->db->select( "response_id, packet_id, response_datetime, event_type, site_status, site_status_details, event, event_sia_code, event_sia_zone_no, event_site_account_no", false );
		$where = "(`packet_id`, `response_datetime`) IN ( SELECT `packet_id`, MAX(`response_datetime`) as `time` FROM `response` WHERE event_type != 'Heartbeat'";
		if( !empty( $packet_id ) ){
			$where .= " AND packet_id = '".$packet_id."'";
		}
		$where .=" GROUP BY `packet_id` )";

		$this->db->where( $where );

		$query = $this->db->get( "response" );

		if( $query->num_rows() > 0 ){
			$dataset = $query->result_array();
			foreach( $dataset as $key => $row  ){
				$result[$row['event_site_account_no']][$row['packet_id']]['site_status'] 				= ( !empty( $row['site_status'] ) ) ? $row['site_status'] : 'unknown' ;
				$result[$row['event_site_account_no']][$row['packet_id']]['last_response_id'] 			= $row['response_id'];
				$result[$row['event_site_account_no']][$row['packet_id']]['last_response_datetime'] 	= $row['response_datetime'];
				$result[$row['event_site_account_no']][$row['packet_id']]['event_type'] 				= $row['event_type'];
				$result[$row['event_site_account_no']][$row['packet_id']]['site_status_details'] 		= $row['site_status_details'];
				$result[$row['event_site_account_no']][$row['packet_id']]['event'] 						= $row['event'];
				$result[$row['event_site_account_no']][$row['packet_id']]['event_sia_code'] 			= $row['event_sia_code'];
				$result[$row['event_site_account_no']][$row['packet_id']]['event_sia_zone_no'] 			= $row['event_sia_zone_no'];
			}
		}

		return $result;
	}


	/*
	*	Check the last transaction ID
	*/
	public function check_last_transaction_id( $packet_id = false ){
		$result = false;

		$this->db->select( "max( transaction_id ) `max_transaction_id`", false );
		$this->db->order_by( "`transaction_id` DESC" );
		$max_transaction_id = $this->db->get( "response" )->row()->max_transaction_id;

		if( !empty( $max_transaction_id ) ){
			$result = (int) $max_transaction_id;
		} else {
			$result = false;
		}

		return $result;
	}

	/*
	*	Get the string fragment
	*/
	public function get_string_between( $string = false, $start = '[', $end= ']' ){
		if( !empty( $string ) ){
			$string 	= ' ' . $string;
			$ini 		= strpos( $string, $start );
			if( $ini == 0 ) return '';
			$ini += strlen( $start );
			$len 		= strpos( $string, $end, $ini ) - $ini;
			return substr( $string, $ini, $len );
		} else {
			return false;
		}
	}


	/*
	*	Unpack the string fragment into array
	*/
	public function unpack_event_string( $string = false ){
		$result = $error = false;

		if( empty( $string ) ){
			return false;
		}

		## Extracting from brackets
		$extracted = $this->get_string_between( $string );
		if( !empty( $extracted ) ){

			## Strip into chunks
			$exploded = explode( '|',$extracted );

			if( is_array( $exploded ) ){
				## processing first element
				$first_char_1 = $exploded[0][0];
				if( !empty( $first_char_1 ) && ( $first_char_1 == '#' ) ){
					$result['site_account'] = substr( $exploded[0], 1 );
				} else {
					$error['status'] = true;
					$error['message']	= 'There is a problem with extracting Site Account ID.';
				}

				## processing first letter from the second element
				$first_char_2 = $exploded[1][0];
				if( !empty( $first_char_2 ) && ( $first_char_2 == 'N' ) ){
					$result['new_alarm'] = 'Yes';
				} else {
					$result['new_alarm'] = 'No';
				}

				## processing an array in the middle of the string
				$middle_array = substr( $exploded[1], 1 );
				if( strpos( $middle_array, '/' ) !== false ){
					## We've got an array - date hasn't been processed
					$middle_array_exploded = explode( '/',$middle_array );
					$result['alarm_sia_code'] = substr( $middle_array_exploded[2], 0, 2 );
					$result['alarm_sia_zone'] = substr( $middle_array_exploded[2], 2 );

				} else {
					$result['alarm_sia_code'] = substr( $middle_array, 0, 2 );
					$result['alarm_sia_zone'] = substr( $middle_array, 2 );
				}

				## processing the last element
				## Alarm string - first letter - always A
				$result['alarm_string']	=  strtolower( substr( $exploded[2], 1 ) );
			} else {
				$error['status'] 	= true;
				$error['message']	= 'There is no items in extracted string.';
			}

		} else {
			$error['status'] 	= true;
			$error['message']	= 'The string between brackets cannot be extracted or there is no string.';
		}

		return $result;
	}


	/*
	*	Prepare the timestamp for the database using microtime
	*/
	function get_timestamp_db(){
		$now = DateTime::createFromFormat( 'U.u', microtime( true ) );
		return $now->format( "Y-m-d H:i:s.u" );
	}


	/*
	*	Prepare the timestamp for the database using microtime
	*/
	function set_current_panel_status( $packet_id = false, $event_string = false ){

		if( empty( $packet_id ) || ( empty( $event_string ) ) ) {
			return false;
		}

		$result = false;

		$current_alarm = $this->unpack_event_string( $event_string );
		
		## I have to check the status for a specific site - from current alarm array data
		$latest_status	= $this->check_latest_status( $packet_id ); ## get the data for last status for all

		if( strtolower( $latest_status[$current_alarm['site_account']][$packet_id]['site_status'] ) != 'fault'  ){
			$site_status = "OK";

			$current_site_status_details[$packet_id][$current_alarm['alarm_sia_code'].$current_alarm['alarm_sia_zone']][$current_alarm['alarm_string']]['status'] = true ;
			$current_site_status_details[$packet_id][$current_alarm['alarm_sia_code'].$current_alarm['alarm_sia_zone']][$current_alarm['alarm_string']]['change_status_timestamp'] = $this->get_timestamp_db() ;

			$result['curr_site_status_details_db'] = serialize( $current_site_status_details );

			$positive = strtolower( $this->alarms_array[$current_alarm['alarm_sia_code']]['positive'] );
			if( !empty( $positive ) && $positive != 'yes' ){
				$site_status = "Fault";
			}
			$result['site_status'] 	= $site_status;

		} elseif ( strtolower( $latest_status[$packet_id]['site_status'] ) == 'fault' ){

			$site_status = "OK";

			## Current alarm full code
			$current_alarm_full_code = $current_alarm['alarm_sia_code'].$current_alarm['alarm_sia_zone'];

			## building temporary opposite variable to the current alarm code
			$opposite_to_the_current = $alarms_array[$current_alarm['alarm_sia_code']]['opposite'].$current_alarm['alarm_sia_zone'];

			## Take site_status_details from the DB after last alarm
			$latest_site_status_details = ( unserialize( $latest_status[$current_alarm['site_account']][$packet_id]['site_status_details'] ) );

			$current_site_status_details = $latest_site_status_details;

			## if the current alarm is opposite to what we had last time and if is 'positive'...
			if( in_array( $opposite_to_the_current, array_keys( $latest_site_status_details[$packet_id] ) ) ){

				foreach ( $latest_site_status_details[$packet_id][$opposite_to_the_current] as $firstKey => $firstValue ) {
					break;
				}

				$positive = strtolower( $this->alarms_array[$current_alarm['alarm_sia_code']]['positive'] );

				if( $positive == 'yes' ){
					## ... we reset the specific alarm ...
					$current_site_status_details[$packet_id][$opposite_to_the_current][$firstKey]['status'] = false;
					$current_site_status_details[$packet_id][$opposite_to_the_current][$firstKey]['change_status_timestamp'] = $this->get_timestamp_db();

					## ... add add new one
					$current_site_status_details[$packet_id][$current_alarm_full_code][$firstKey]['status'] = true;
					$current_site_status_details[$packet_id][$current_alarm_full_code][$firstKey]['change_status_timestamp'] = $this->get_timestamp_db();
				} else {
					## means another 'negative' alarm - adding to the list
					$current_site_status_details[$packet_id][$current_alarm_full_code][$firstKey]['status'] = true;
					$current_site_status_details[$packet_id][$current_alarm_full_code][$firstKey]['change_status_timestamp'] = $this->get_timestamp_db();
				}

			} else {

				## means - we're receiving a new alarm  - just add to the detail list
				$current_site_status_details[$packet_id][$current_alarm_full_code][$current_alarm['alarm_string']]['status'] = true;
				$current_site_status_details[$packet_id][$current_alarm_full_code][$current_alarm['alarm_string']]['change_status_timestamp'] = $this->get_timestamp_db();
			}

			foreach( $current_site_status_details[$packet_id] as $alarm_code => $alarm_details ){
				$array_values = array_values( $alarm_details );
				if( ( in_array( substr( $alarm_code, 0, 2 ), $this->negative_codes_array ) ) && ( $array_values[0]['status'] == true ) ){
					$site_status = "Fault";
				}
			}

			$result['curr_site_status_details_db'] = serialize( $current_site_status_details );
			$result['site_status'] 	= $site_status;

		}

		return $result;
	}



	/*
	*	Save the Heartbeat into the db
	*/
	public function save_heartbeat_into_db( $response = false, $transaction_id = false, $timestamp_db = false, $ack = false, $type = 'opening hartbeat' ){

		$dataset = [
			"raw_response"		=> $response,
			"transaction_id"	=> $transaction_id,
			"response_datetime"	=> $timestamp_db,
			"request"			=> $ack,
			"packet_id"			=> 'N/A',
			"alarmno"			=> 'N/A',
			"event_type"		=> 'Heartbeat',
			"event"				=> $type,
			"timestamp"			=> '9999-99-99 23:59:59'
		];

		$this->db->insert( "response", $dataset );
		return $this->db->insert_id();
	}


	/*
	*	Save the Alarm into the db
	*/
	public function save_alarm_into_db( $packet_id = false, $account_id = 1, $dataset = false ){
	
		if( !empty( $dataset ) ){
			$dataset['packet_id'] 	= $packet_id;
			$dataset['account_id'] 	= $account_id;
		
			$this->db->insert( "response", $dataset );
			
			$this->db->update( "site", array( "site_status" => $dataset['site_status'] ), array( "account_id"=>$account_id, "event_site_id"=>$dataset['event_site_account_no'] ) );
		
			return $this->db->insert_id();
			
		} else {
			return false;
		}
	}


	/*
	*	Set the status for the whole site despite how many panels they've got
	*	The starting point is - I've got an alarm which can 
	*/
	function set_site_status( $site = false, $curr_panel_data = false ){

		if( empty( $site ) || empty( $curr_panel_data ) ){
			return false;
		} else {
			
		}
	}	
	
	public function get_panels_list(){
		$result = false;
		$this->db->select( "*" );
		/* $this->db->where( "site_id",  ); */
		$query=$this->db->get( "site_panels" );

		if( $query->num_rows() > 0 ){
			$result = $query->result_array();
		}
	
		return $result;
	}
	
	public function save_nautral_alarm_into_db( $response = false, $transaction_id = false, $ack = false, $panel_ref_id = false, $site_id = false, $account_id = false ){
		
		## [#1234|NLB8008|AEngineer Mode]
		
		$event 			= '[#'.$site_id.'|NXX9999ANo status change]';
		$timestamp_db 	= $this->get_timestamp_db();
		
		## check site status and detail 
		
		$dataset = [
			"raw_response"			=> $response,
			"transaction_id"		=> $transaction_id,
			"response_datetime"		=> $timestamp_db,
			"request"				=> $ack,
			"packet_id"				=> $panel_ref_id,
			"alarmno"				=> '0',
			"event_type"			=> 'SIA',
			"event"					=> $event,
			"timestamp"				=> NULL,
			"account_id"			=> $account_id,
			"site_status"			=> NULL,
			"site_status_details" 	=> NULL
		];
		
		$this->db->insert( "response", $dataset );
		return $this->db->insert_id();
	}
	

	public function test(){

		$panels = $this->get_panels_list();
		debug( $panels, "print", true );
		
		
		
		$test = 'test';

		$packet_id1 = '00066830';
		$packet_id2 = '00068313';
		$packet_id3 = '00068541';
		$packet_id4 = '00068542';
		
		$latest_status = $this->check_latest_status( $packet_id3 );
		debug( $latest_status, "print", false );
		foreach( $latest_status as $site => $dataset ){
			foreach( $dataset as $panel_id => $row ){
				debug( unserialize( $row['site_status_details'] ), "print", false );
			}
		}
		
		$event_string = '[#1234|NFR8001|Afire alarm restore]';
		$current_alarm = $this->unpack_event_string( $event_string );

		$set_current_panel_status2 = $this->set_current_panel_status2( $packet_id3, $event_string );
	
		debug( unserialize( $set_current_panel_status2['curr_site_status_details_db'] ), "print", true );
		
		foreach( $set_current_panel_status2 as $site => $dataset ){
			foreach( $dataset as $panel_id => $row ){
				$site_details = $row['site_status_details'];
			}
		}
		
		debug( $set_current_panel_status2['1234']['00068541'], "print", true );
		
	}
	
	/*
	* Prepare Heartbeat data required to save a triggered HB
	*/
	public function store_heartbeat_event( $account_id = 1 ){
		$result = false;
		if( $account_id ){
			$transaction_id = 1 + (int)$this->check_last_transaction_id();
			$sites = [];
			$query = $this->db->select( 'site_id, event_site_id, site_status ' )
				->where( 'account_id', $account_id )
				->where( 'event_site_id > 0' )
				->get( 'site' );
			
			if( $query->num_rows() > 0 ){
				$saved_heartbeats = [];
				foreach( $query->result()  as $site ){
					$conditions = [ 'event_type'=>'sia', 'event_site_account_no'=>$site->event_site_id, 'event_type'=>'sia',  ];
					$latest_events = $this->db->select( 'MAX(response_id) `response_id`', false )
						->where( 'packet_id IS NOT NULL' )
						->group_by( 'packet_id' )
						->get_where( 'response', $conditions );
					debug( $this->db->last_query(), "print", false );
					if( $latest_events->num_rows() > 0 ){
						foreach( $latest_events->result() as $response_id ){
							$panel_details = $this->get_sia_event_by_id( $response_id->response_id );
							if($panel_details){
								$heartbeat = [
									'account_id'=>$account_id,
									'packet_id'=>$panel_details->packet_id,
									'event'=>'#'.$site->event_site_id.' | '.$panel_details->packet_id.' | Site still ['.$panel_details->panel_status.'] since last change at '.date('d-m-Y H:i:s', strtotime($panel_details->response_datetime)),
									'event_site_account_no'=>$panel_details->event_site_account_no,
									'transaction_id'=>$transaction_id,
									'response_datetime'=>$this->get_timestamp_db(),									
									'event_type'=>'SIA-H',								
									'site_status'=>( $panel_details->panel_status ) ? ( empty( $panel_details->panel_status ) ? 'Faulty' : $panel_details->panel_status ) : $site->site_status,
									'site_id'=>$panel_details->event_site_account_no,
									'event_site_account_no'=>$panel_details->event_site_account_no
								];								
								
								$this->db->insert( "response", $heartbeat );
								
								if( $this->db->trans_status() !== false ){
									$saved_heartbeats = $this->db->insert_id();
								} else {
									$saved_heartbeats = false;
								}
							}else{
								
							}
						}
					}
				}
				
				$result = ( !empty($saved_heartbeats) ) ? true : false;
			}
		}
		return $result;
	}
	
	/*
	* Get Panel details using the last known response ID
	*/
	public function get_sia_event_by_id( $response_id = false ){
		$result = false;
		if( $response_id ){
			$query  = $this->db->select('packet_id, event_site_account_no, response_datetime, site_status `panel_status`', false)
				->get_where( 'response', [ 'response_id'=>$response_id ] )->row();
			$result = ( !empty($query) ) ? $query : null;
		}
		return $result;
	}
	
	/*
	* Save a Hartbeat event
	*/
	public function save_heartbeat_event( $heartbeat = [] ){
		$result = false;
		if( !empty($heartbeat['account_id']) && !empty($heartbeat['event_site_account_no']) && !empty($heartbeat['packet_id']) ){
			$this->db->insert( 'response', $heartbeat );
			if( $this->db->trans_status() !== false ){
				$result = true;
			}
		}
		return $result;
	}
	
}