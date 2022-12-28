<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Stats_model extends CI_Model {

	function fetchAPI($getData, $getType = false){

		$getType = ( !empty( $getData['getType'] ) ) ? $getData['getType'] : ( !empty( $getType ) ? $getType : die("{status: 'failed', message : 'Empty Fetch type!'}") );

		if(	!empty($getType)	){
			switch( $getType ){
				case "getcolumns":
					$table = (!empty( $getData['table'] )) ? $getData['table'] : die("{status: 'failed', message : 'Table does not exist!'}");
					
					$db_con = mysqli_connect( $this->db->hostname, $this->db->username, $this->db->password, $this->db->database );

					if (!$db_con) {
						die("{status: 'failed', message : 'Cannot connect to database!'}");
					}
	
					if ($result = mysqli_query($db_con , "SHOW COLUMNS FROM " . $table . ";")) {
						
						$table_columns = Array();
	
						while($row = $result->fetch_row()){
							array_push($table_columns, $row[0]);
						}
	
						die(json_encode($table_columns));
					} else {
						die("{status: 'failed', message : 'Table is empty!'}");
					}
					
					break;
			}
		}
		
	
	}

}


?>