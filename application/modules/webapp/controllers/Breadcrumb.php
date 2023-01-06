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
		$breadcrumb 	= '<span style="color:#0092CD !important; margin-left:0px;"><i class="pointer fas fa-arrow-circle-left" onclick="goBack()" title="Go back"></i>  &nbsp;<a style="text-decoration:none; color:#0092CD !important; font-weight:600" href="'.base_url( "/webapp/home" ).'">HOME</a> &nbsp; <i class="fas fa-angle-double-right"></i> &nbsp;</span>';
		
		
		$last_segment 	= "";
		$foreach_count 	= 1;
		$module_parent	= '';
		$display_name	= '';

		foreach ( $segments as $k => $segment ) {
			switch( $k ){
				case '1':
					if( $count <= 3 ){

						if( $this->app_method == 'audits' ){
							$display_name = 'evidocs';
						} elseif( $this->app_method == 'sites' ){
							$display_name = 'buildings';
						} else {
							$display_name = $this->app_method;
						}

						$breadcrumb 	.= '<strong><span>'.strtoupper( str_replace( '_', ' ', $display_name ) ).'</a></span></strong>&nbsp; &nbsp; ';
					}

					$module_parent 	= $segment;
					break;
				case '2':
					$segment_home	= ( $this->str_ends_with( strtolower( $segment ), 's') || in_array( strtolower( $segment ), $pluralised ) ) ? $segment : $segment.'s';

					if( $segment_home == 'audits' ){
						$display_name = 'evidocs';
						
					} elseif( $segment_home == 'sites' ){
						$display_name = 'buildings';
					}else{
						$display_name = $segment_home;
					}


					if( $count > 3 ){
						$breadcrumb 	.= '<span><a style="text-decoration:none; color:#0092CD !important;font-weight:600" href="'.base_url( $module_parent.'/'.$segment ).'">'.strtoupper( $display_name ).'</a></span>';
					}else if( !empty( $segments[3] ) && in_array( $segments[3], ['create','create_new']) ){
						$breadcrumb 	.= '<span><a style="text-decoration:none; color:#0092CD !important;font-weight:600" href="'.base_url( $module_parent.'/'.$segment ).'">'.strtoupper( $display_name ).'</a></span>';
					}

					$module_parent 	= $module_parent.'/'.$segment;
					break;
				case '3':
					$module_parent 	= $module_parent.'/'.$segment;
					break;
				case '4':
					if( $foreach_count >= $count ){
						$breadcrumb 	.= '<span class="text-grey">&nbsp; &nbsp; <i style="text-decoration:none; color:#0092CD !important;font-weight:600" class="fas fa-angle-double-right"></i> &nbsp;'.strtoupper( str_replace( '_', ' ', $this->app_method ) ).'</span>';
					}else{
						$breadcrumb 	.= '<span>&nbsp; &nbsp; <i style="text-decoration:none; color:#0092CD !important;font-weight:600" class="fas fa-angle-double-right"></i> &nbsp;'.'<a style="text-decoration:none; color:#0092CD; font-weight:600" href="'.base_url( $module_parent.'/'.$segment ).'">'.strtoupper( str_replace( '_', ' ', $this->app_method ) ).'</a></span>';
					}
					$module_parent 	= $module_parent.'/'.$segment;
					break;
				case '5':
					if( $foreach_count >= $count ){
						$breadcrumb 	.= '<span><strong>&nbsp; <i style="text-decoration:none; color:#0092CD !important;font-weight:600" class="fas fa-angle-double-right"></i> &nbsp;'.strtoupper( str_replace( '_', ' ', $segment ) ).'</strong></span>';
					}else{
						$breadcrumb 	.= '<span>&nbsp; <i style="text-decoration:none; color:#0092CD !important;font-weight:600" class="fas fa-angle-double-right"></i> &nbsp;'.'<a style="text-decoration:none; color:#0092CD; font-weight:600" href="'.base_url( $module_parent.'/'.$segment ).'">'.strtoupper( $segment ).'</a></span>';
					}
					$module_parent 	= $module_parent.'/'.$segment;
					break;
			}
			$foreach_count += 1;
		}
		$breadcrumb .= '</span>';
		return $breadcrumb;
	}

	/** Get list of core modules per account **/
	public function get_core_modules(){
		//This should really be from the Db, remind me to complete this snippet if I will have a spare 2 mins :)
		$default_modules_links = '&nbsp;<span><a style="text-decoration:none; color:#0092CD !important;" href="'.base_url( '/webapp/site/sites' ).'">BUILDINGS</a></span>&nbsp;&nbsp |';
		$default_modules_links .= '&nbsp;<span><a style="text-decoration:none; color:#0092CD  !important;" href="'.base_url( '/webapp/asset/assets' ).'">ASSET</a></span>&nbsp;&nbsp; |';
		$default_modules_links .= '&nbsp;<span><a style="text-decoration:none; color:#0092CD  !important;" href="'.base_url( '/webapp/audit/audits' ).'">EVIDOCS</a></span>&nbsp;&nbsp; |';
		$default_modules_links .= '&nbsp;<span><a style="text-decoration:none; color:#0092CD  !important;" href="'.base_url( '/webapp/report/reports' ).'">REPORTS</a></span>&nbsp;&nbsp;';
		return $default_modules_links;
	}

	/** Check if String ends with S **/
	function str_ends_with( $haystack, $needle ){
		$length = strlen($needle);
		return $length === 0 || ( substr($haystack, -$length) === $needle );
	}
}