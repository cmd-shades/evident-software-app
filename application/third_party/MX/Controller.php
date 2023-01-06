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
		$this->auth_token		= ( !empty( $auth_data ) ) ? $auth_data->auth_token : null;
		$this->xsrf_token		= ( !empty( $auth_data ) ) ? $auth_data->xsrf_token : null;
		$this->user 			= ( !empty( $auth_data ) ) ? $auth_data->user : null;
		if( !empty( $this->user ) ){
			$this->user->auth_token = ( !empty($this->auth_token) ) ? $this->auth_token : null;
			$this->session->set_userdata( 'xsrf_token', $this->xsrf_token );
		}
		$this->logged_in 			= ( !empty( $auth_data ) ) ? $auth_data->user->id : 0;		
		$this->is_admin 			= ( !empty( $auth_data->user->is_admin ) ) ? 1 : 0;		
		$this->is_primaary_user 	= ( !empty( $auth_data->user->is_primaary_user ) ) ? 1 : 0;		
		$this->associated_user_id 	= ( !empty( $auth_data->user->associated_user_id ) ) ? $auth_data->user->associated_user_id : null;	
		$this->user_id 				= ( !empty( $auth_data->user->id ) ) ? $auth_data->user->id : null;		
		$this->account_id 			= ( !empty( $auth_data->user->account_id ) ) ? $auth_data->user->account_id : null;	
		$this->super_admin_list 	= SUPER_ADMIN_ACCESS;		
		
		$this->load->model( 'webapp/Webapp_model','webapp_service' );
		$this->load->model( 'serviceapp/Document_Handler_model','document_service' );
		$this->load->model( 'serviceapp/Account_model','account_service' );
		
		$this->exempt_methods 	= ['login','activate','change_password', 'signup' ];
		$this->options 			= ['auth_token'=>$this->auth_token];
		
		$section 				= explode("/", $_SERVER["SCRIPT_NAME"]);
		$appDir  				= $_SERVER["DOCUMENT_ROOT"]."/".$section[1]."/";
		$this->appDir  			= str_replace( 'index.php/', '', $appDir );
	}
	
	public function __get($class){
		return CI::$APP->$class;
	}
	
	public function _render_webpage( $template_name, $vars = [], $return = FALSE, $hide_sidebar = false ) {

		if( !in_array( strtolower($this->app_method), $this->exempt_methods ) ){		
			
			#Get permitted modules
			$allowed_modules			= $this->webapp_service->api_dispatcher( $this->api_end_point.'access/check_module_access', ['account_id'=>$this->account_id, 'user_id'=>$this->user_id], $this->options, true );
			$vars['permitted_modules'] 	= ( !empty( $allowed_modules->module_access ) ) ? $allowed_modules->module_access : null;
			$vars['module_count'] 		= ( !empty( $allowed_modules->module_access ) ) ? count($allowed_modules->module_access) : 0;
			
			# Set active class
			$vars['active_class'] = preg_replace( '/[^A-Za-z0-9]/', '', $this->app_method );
			
			# Set module style-custom files
			$vars['module_identier']= $this->app_module;
			$vars['module_style'] 	= $this->app_module.'_styles.css';

			# Breakcrumber
			if( !in_array( $this->app_module, ['home'] ) ){
				$vars['breadcrumb'] = Modules::run('webapp/breadcrumb/get');
			}

			# Get list of core modules
			$vars['core_modules'] 	= Modules::run('webapp/breadcrumb/get_core_modules');
			$output = $this->output->get_output();

			# Set xsrf token
			$vars['xsrf_token']		= $this->xsrf_token;
			
			# Separate Dashboard Header & Footter from original version
			$partials_splitter = '';
			if( strtolower( $this->app_module ) == 'dashboard' ){
				$partials_splitter = 'discipline_';
			}
			
			if ( $return ){
				$content = $this->load->view('webapp/_partials/'.$partials_splitter.'main_header', $vars, $return);
				$content .= $this->load->view('webapp/'.$template_name, $vars, $return);			
				$content .= $this->load->view('webapp/_partials/'.$partials_splitter.'main_footer', $vars, $return);
				return $content;
			} else {
				$this->load->view('webapp/_partials/'.$partials_splitter.'main_header', $vars);
				$this->load->view('webapp/'.$template_name, $vars);
				$this->load->view('webapp/_partials/'.$partials_splitter.'main_footer', $vars);
			}
		}else{
			$this->load->view('webapp/'.$template_name, $vars);
		}
    }
	
	public function identity(){
		
		if( $this->logged_in ){
			return true;
		} else {	
			
			if (!empty($_SERVER['QUERY_STRING'])) {
				$uri = uri_string() . '?' . $_SERVER['QUERY_STRING'];
			} else {
				$uri = uri_string();
			}
			
			$this->session->set_userdata( 'referrer_uri', $uri );
			redirect( 'webapp/user/login', 'refresh' );
			#return false;
		}
	}
}