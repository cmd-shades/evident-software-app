<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  AlertManager
* Author: Simpyl SID Team
* Created:  10.02.2018
* Description:  This is library for all system interactions with external services like Web Way One.
*/

class AlertManager extends MX_Controller{

	function __construct(){
		$this->load->model( 'serviceapp/Alert_Manager_model', 'alert_manager_service' );
		$timestamp 		 = DateTime::createFromFormat( 'U.u', microtime( true ) );
		$this->timestamp = $timestamp->format( 'Y-m-d H:i:s.u' );
		$this->datetime  = $timestamp->format( 'Y-m-d H:i:s' );
	}

	/**  Open WebWayOne Connection **/
	public function _open_connection( $account_id = false ){
		
		if( $account_id ){
			$response 			= false;
			$unique_sequence 	= 'SEQ_';
			$xml_request_header	= '<?xml version="1.0" encoding="UTF-8"?>';
			$heartbeat_ack_str  = $xml_request_header.'<Ack/>';
			$msg				= '';
			
			## Open a socket connection
			$fp = @fsockopen( WWO_SOCKET, 50572, $errno, $errstr, 30 ); 
			if( !$fp ){
				$msg .= '<p style="color:green">'.date( 'Y-m-d H:i:s' ).' | Connection using Socket #1 ('.WWO_SOCKET.') failed, attempting connection using Socket #2 ('.WWO_SOCKET2.') </p>';
				$fp   = @fsockopen( WWO_SOCKET2, 50572, $errno, $errstr, 30 ); 
				$open_socket = '#2 ('.WWO_SOCKET2.')';
			}else{
				$open_socket = '#1 ('.WWO_SOCKET.')';
			}

			if( $fp ){
				$msg 	.= '<p style="color:green">'.date( 'Y-m-d H:i:s' ).' | Connection to WebWayOne Servers opened successfully on Socket '.$open_socket.' ...</p>';
	
				$feedback['msg'] = $msg;
				return $feedback;
				
				## Wait >= 30 seconds, this allows the server to populate the response with a response (if any
				sleep( 31 );
				
				$msg 	.= '<p style="color:green">'.date( 'Y-m-d H:i:s' ).' | The system waited 32 seconds, then read for a heartbeat...</p>';
				
				## Check for and get any responses from the open connection
				$response = fread( $fp, 4096 );

				if( strpos( $response, '<Heartbeat/>' ) !== false ){
					## Heartbeat received, we need to look inside the response
					$msg 	.= '<p style="color:green">'.date( 'Y-m-d H:i:s' ).' | Heartbeat received successfully...</p>';
					
					## Save opening heartbeat
					$save_hb = $this->alert_manager_service->save_heartbeat( $response );
					
					if( !empty( $save_hb ) ){
						$msg 	.= '<p style="color:green">'.date( 'Y-m-d H:i:s' ).' | Heartbeat saved successfully...</p>';
					}else{
						$msg 	.= '<p style="color:red">'.date( 'Y-m-d H:i:s' ).' | There was a problem with saving Opening Hartbeat into the database...</p>';
					}
					
					## Ack the heartbeat
					fputs( $fp, $heartbeat_ack_str );
					
					##Read the response after acking the heartbeat
					$ack_heartbeat_response = fread( $fp, 4096 );
					
					while( strpos( $ack_heartbeat_response, '<Heartbeat/>' ) !== false ){
						$msg 	.= '<p style="color:green">'.date( 'Y-m-d H:i:s' ).' | Alarm Response received successfully after Heartbeat Acknowledgement...</p>';
						$save_alarm = $this->alert_manager_service->save_alarm( $account_id, $ack_heartbeat_response, $ack_action  );
						if( !empty( $save_alarm ) ){
							//Save and update site status and panel status accordingly
							
							//Ack the alarm e.g. fputs( $fp, $ack );
						}else{
							//Log and report any errors or continue
						}
					}
					
					##
					
				}else{
					## No Heartbeat received
					$msg 	.= '<p style="color:orange">'.date( 'Y-m-d H:i:s' ).' | No heartbeat, response is empty...</p>';			
				}
				
			}else{
				$msg 	= '<p style="color:red">'.date( 'Y-m-d H:i:s' ).' | Connection to WebWayOne Servers failed...</p>';
			}

			##Somewhere here, log the feedback message for help with debugging
			$this->alert_manager_service->save_feedback_log( $account_id, $packet_id = false ,$d = false  );
			
			$feedback['msg'] = $msg;
		}else{
			$feedback['msg'] ='<p style="color:red">'.date( 'Y-m-d H:i:s' ).' | Invalid or missing Account ID...</p>';
		}
		
		return $feedback;
	}
	
	/**  Close Webway One Connection **/
	public function _close_connection( $account_id = false ){
		
	}
	
	/** While the connection is open, check for any new alerts **/
	public function _check_for_alerts( $account_id = false, $packet_id = false ){
		
	}
	
	/** Save an Alarm Alert **/
	private function _save_alarm( $account_id = false, $alarm_data = false ){
		$this->alert_manager_service->save_alarm( $account_id, $alarm_data = false );
	}
	
	/** Save a hearbeat **/
	private function _save_heartbeat( $account_id = false ){
		
	}
}