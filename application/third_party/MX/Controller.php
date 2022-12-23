<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/** load the CI class for Modular Extensions **/
require dirname(__FILE__).'/Base.php';

/**
 * Modular Extensions - HMVC
 *
 * Adapted from the CodeIgniter Core Classes
 * @link	http://codeigniter.com
 *
 * Description:
 * This library replaces the CodeIgniter Controller class
 * and adds features allowing use of modules and the HMVC design pattern.
 *
 * Install this file as application/third_party/MX/Controller.php
 *
 * @copyright	Copyright (c) 2015 Wiredesignz
 * @version 	5.5
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 **/
require_once APPPATH . '/libraries/JWT.php';
use \Firebase\JWT\JWT;
 
class MX_Controller 
{
	public $autoload = array();
	
	public function __construct() 
	{
		
		$class = str_replace(CI::$APP->config->item('controller_suffix'), '', get_class($this));
		log_message('debug', $class." MX_Controller Initialized");
		Modules::$registry[strtolower($class)] = $this;	
		
		/* copy a loader instance and initialize */
		$this->load = clone load_class('Loader');
		
		$this->load->initialize($this);	
		
		/* autoload module items */
		$this->load->_autoloader($this->autoload);
					
		$this->api_end_point	= api_end_point();
		$this->app_module 		= $this->router->fetch_class();
		$this->app_method 		= $this->router->fetch_method();
		
		$auth_data 				= $this->session->userdata('auth_data');
		$this->auth_token		= ( !empty($auth_data) ) ? $auth_data->auth_token : null;
		$this->user 			= ( !empty($auth_data) ) ? $auth_data->user : null;
		if( !empty( $this->user ) ){
			$this->user->auth_token = ( !empty($this->auth_token) ) ? $this->auth_token : null;
		}
		$this->logged_in 		= ( !empty($auth_data) ) ? $auth_data->user->id : 0;		
		$this->is_admin 		= ( !empty($auth_data->user->is_admin) ) ? 1 : 0;		
		$this->user_id 			= ( !empty($auth_data->user->id) ) ? $auth_data->user->id : null;		
		$this->account_id 		= ( !empty($auth_data->user->account_id) ) ? $auth_data->user->account_id : null;		
		
		$this->account_id 		= ( !empty($auth_data->user->account_id) ) ? $auth_data->user->account_id : null;		
		
		$this->load->model( 'webapp/Webapp_model','webapp_service' );
		$this->load->model( 'serviceapp/Document_Handler_model','document_service' );
		
		$this->exempt_methods = ['login','activate'];
	}
	
	public function __get($class){
		return CI::$APP->$class;
	}
	
	public function _render_webpage($template_name, $vars = [], $return = FALSE, $hide_sidebar = false ) {

		if( !in_array( strtolower($this->app_method), $this->exempt_methods ) ){
			
			# Set active class
			$vars['active_class'] = preg_replace( '/[^A-Za-z0-9]/', '', $this->app_method );
			$vars['hide_sidebar'] = ( $hide_sidebar ) ? true : false;	

			# Set module style-custom files
			$vars['module_identier']= $this->app_module;
			$vars['module_style'] 	= $this->app_module.'_styles.css';
			
			# Breakcrumber
			if( !in_array( $this->app_module, ['home'] ) ){
				$vars['breadcrumb'] 	= Modules::run('webapp/breadcrumb/get');
			}

			if ($return){
				$content = $this->load->view('webapp/_partials/main_header', $vars, $return);
				$content .= $this->load->view('webapp/'.$template_name, $vars, $return);			
				$content .= $this->load->view('webapp/_partials/main_footer', $vars, $return);
				return $content;
			}else{
				$this->load->view('webapp/_partials/main_header', $vars);
				$this->load->view('webapp/'.$template_name, $vars);
				$this->load->view('webapp/_partials/main_footer', $vars);
			}
		}else{
			$this->load->view('webapp/'.$template_name, $vars);
		}
    }
	
	public function identity(){
		
		if( $this->logged_in ){
			return true;
		}		
		return false;
	}
}