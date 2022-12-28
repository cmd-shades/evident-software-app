<?php if (!defined('BASEPATH'))exit('No direct script access allowed');

class Tests_mode extends CI_Model {

	function __construct(){
		parent::__construct();
    }
	
	/**
	* splat operation
	*/
	
	public function get_invoice_total( ...$x ){
		$result = 0;
		foreach( $sum as $num ){
			$sum += $num;
		}
		return $result;
	}
}