<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Breadcrumb extends MX_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
	}

	/** Get the Breadcrumb snippet **/
	public function get(){
		
		$pluralised = ['fleet','people','workforce'];
		$search   	= array('-', '/', '_');
		$replace  	= array(' ', ' ', ' ');
		
		$segments = $this->uri->segment_array();
		$count 	  = count($this->uri->segment_array());
		//$breadcrumb 	= '<span style="color:#fff; margin-left:-20px"><i class="pointer fas fa-undo-alt" onclick="goBack()" title="Go back"></i></span>';
		$breadcrumb 	= '<span style="color:#fff; margin-left:-20px"><i class="pointer fas fa-arrow-circle-left" onclick="goBack()" title="Go back"></i>  &nbsp;&nbsp;</span>';
		//$breadcrumb 	= '<span style="color:#fff; margin-left:-20px"><span style="font-size:12px" class="pointer" onclick="goBack()" title="Go back">&#11178; &nbsp;&nbsp;</span></span>';
		//$breadcrumb 	= '<span style="color:#fff; margin-left:-20px">';
		$last_segment 	= "";
		$foreach_count 	= 1;
		$module_parent	= '';

		foreach ( $segments as $k => $segment ) {
			switch( $k ){
				case '1':
					#$breadcrumb 	.= '<span><a style="text-decoration:none; color:#fff" href="'.base_url( $segment ).'">Home</a></span>&nbsp; &nbsp; ';
					$module_parent 	= $segment;
					break;
				case '2':
					$segment_home	= ( $this->str_ends_with( strtolower( $segment ), 's') || in_array( strtolower( $segment ), $pluralised ) ) ? $segment : $segment.'s';
					
					if( $count > 3 ){
						$breadcrumb 	.= '<span><a style="text-decoration:none; color:#fff;font-weight:400" href="'.base_url( $module_parent.'/'.$segment ).'">'.ucwords( $segment_home ).'</a></span>';
					}else if( !empty( $segments[3] ) && in_array( $segments[3], ['create','create_new']) ){
						$breadcrumb 	.= '<span><a style="text-decoration:none; color:#fff;font-weight:600" href="'.base_url( $module_parent.'/'.$segment ).'">'.ucwords( $segment_home ).'</a></span>';
					}
					
					$module_parent 	= $module_parent.'/'.$segment;
					break;
				case '3':
					$module_parent 	= $module_parent.'/'.$segment;
					
					break;
				case '4':
					if( $foreach_count >= $count ){
						$breadcrumb 	.= '<span><strong>&nbsp; &nbsp; <img style="width:6.5px;" src="'.base_url('assets/images/angle-right.png').'" /> &nbsp; &nbsp;'.ucwords( $this->app_method ).'</strong></span>';
					}else{
						$breadcrumb 	.= '<span>&nbsp; &nbsp; <img style="width:6.5px;" src="'.base_url('assets/images/angle-right.png').'" /> &nbsp; &nbsp;'.'<a style="text-decoration:none; color:#fff; font-weight:400" href="'.base_url( $module_parent.'/'.$segment ).'">'.ucwords( $this->app_method ).'</a></span>';
					}
					$module_parent 	= $module_parent.'/'.$segment;
					break;
				case '5':
					if( $foreach_count >= $count ){
						$breadcrumb 	.= '<span><strong>&nbsp; &nbsp; <img style="width:6.5px;" src="'.base_url('assets/images/angle-right.png').'" /> &nbsp; &nbsp;'.ucwords( $segment ).'</strong></span>';
					}else{
						$breadcrumb 	.= '<span>&nbsp; &nbsp; <img style="width:6.5px;" src="'.base_url('assets/images/angle-right.png').'" /> &nbsp; &nbsp;'.'<a style="text-decoration:none; color:#fff; font-weight:400" href="'.base_url( $module_parent.'/'.$segment ).'">'.ucwords( $segment ).'</a></span>';
					}
					$module_parent 	= $module_parent.'/'.$segment;
					break;
			}
			$foreach_count += 1;
		}
		$breadcrumb .= '</span>';
		return $breadcrumb;
	}
	
	/** Check if String ends with S **/
	function str_ends_with( $haystack, $needle ){
		$length = strlen($needle);
		return $length === 0 || ( substr($haystack, -$length) === $needle );
	}
}