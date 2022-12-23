<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Model
*
* Author:  Ben Edmunds
* 		   ben.edmunds@gmail.com
*	  	   @benedmunds
*
* Added Awesomeness: Phil Sturgeon
*
* Location: http://github.com/benedmunds/CodeIgniter-Ion-Auth
*
* Created:  10.01.2009
*
* Description:  Modified auth system based on redux_auth with extensive customization.  This is basically what Redux Auth 2 should be.
* Original Author name has been kept but that does not mean that the method has not been modified.
*
* Requirements: PHP5 or above
*
*/

require_once APPPATH . '/libraries/JWT.php';
require_once APPPATH . '/libraries/ExpiredException.php';

use \Firebase\JWT\JWT;

class Ion_auth_model extends CI_Model
{
	/**
	 * Holds an array of tables used
	 *
	 * @var array
	 **/
	public $tables = array();

	/**
	 * activation code
	 *
	 * @var string
	 **/
	public $activation_code;

	/**
	 * forgotten password key
	 *
	 * @var string
	 **/
	public $forgotten_password_code;

	/**
	 * new password
	 *
	 * @var string
	 **/
	public $new_password;

	/**
	 * Identity
	 *
	 * @var string
	 **/
	public $identity;

	/**
	 * Where
	 *
	 * @var array
	 **/
	public $_ion_where = array();

	/**
	 * Select
	 *
	 * @var array
	 **/
	public $_ion_select = array();

	/**
	 * Like
	 *
	 * @var array
	 **/
	public $_ion_like = array();

	/**
	 * Limit
	 *
	 * @var string
	 **/
	public $_ion_limit = NULL;

	/**
	 * Offset
	 *
	 * @var string
	 **/
	public $_ion_offset = NULL;

	/**
	 * Order By
	 *
	 * @var string
	 **/
	public $_ion_order_by = NULL;

	/**
	 * Order
	 *
	 * @var string
	 **/
	public $_ion_order = NULL;

	/**
	 * Hooks
	 *
	 * @var object
	 **/
	protected $_ion_hooks;

	/**
	 * Response
	 *
	 * @var string
	 **/
	protected $response = NULL;

	/**
	 * message (uses lang file)
	 *
	 * @var string
	 **/
	protected $messages;

	/**
	 * error message (uses lang file)
	 *
	 * @var string
	 **/
	protected $errors;

	/**
	 * error start delimiter
	 *
	 * @var string
	 **/
	protected $error_start_delimiter;

	/**
	 * error end delimiter
	 *
	 * @var string
	 **/
	protected $error_end_delimiter;

	/**
	 * caching of users and their groups
	 *
	 * @var array
	 **/
	public $_cache_user_in_group = array();

	/**
	 * caching of groups
	 *
	 * @var array
	 **/
	protected $_cache_groups = array();

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->config->load('ion_auth', TRUE);
		$this->load->helper('cookie');
		$this->load->helper('date');
		$this->lang->load('ion_auth');
		

		// initialize db tables data
		$this->tables  = $this->config->item('tables', 'ion_auth');

		//initialize data
		$this->identity_column = $this->config->item('identity', 'ion_auth');
		$this->store_salt      = $this->config->item('store_salt', 'ion_auth');
		$this->salt_length     = $this->config->item('salt_length', 'ion_auth');
		$this->join			   = $this->config->item('join', 'ion_auth');


		// initialize hash method options (Bcrypt)
		$this->hash_method = $this->config->item('hash_method', 'ion_auth');
		$this->default_rounds = $this->config->item('default_rounds', 'ion_auth');
		$this->random_rounds = $this->config->item('random_rounds', 'ion_auth');
		$this->min_rounds = $this->config->item('min_rounds', 'ion_auth');
		$this->max_rounds = $this->config->item('max_rounds', 'ion_auth');


		// initialize messages and error
		$this->messages    = array();
		$this->errors      = array();
		$delimiters_source = $this->config->item('delimiters_source', 'ion_auth');

		// load the error delimeters either from the config file or use what's been supplied to form validation
		if ($delimiters_source === 'form_validation')
		{
			// load in delimiters from form_validation
			// to keep this simple we'll load the value using reflection since these properties are protected
			$this->load->library('form_validation');
			$form_validation_class = new ReflectionClass("CI_Form_validation");

			$error_prefix = $form_validation_class->getProperty("_error_prefix");
			$error_prefix->setAccessible(TRUE);
			$this->error_start_delimiter = $error_prefix->getValue($this->form_validation);
			$this->message_start_delimiter = $this->error_start_delimiter;

			$error_suffix = $form_validation_class->getProperty("_error_suffix");
			$error_suffix->setAccessible(TRUE);
			$this->error_end_delimiter = $error_suffix->getValue($this->form_validation);
			$this->message_end_delimiter = $this->error_end_delimiter;
		}
		else
		{
			// use delimiters from config
			$this->message_start_delimiter = $this->config->item('message_start_delimiter', 'ion_auth');
			$this->message_end_delimiter   = $this->config->item('message_end_delimiter', 'ion_auth');
			$this->error_start_delimiter   = $this->config->item('error_start_delimiter', 'ion_auth');
			$this->error_end_delimiter     = $this->config->item('error_end_delimiter', 'ion_auth');
		}


		// initialize our hooks object
		$this->_ion_hooks = new stdClass;

		// load the bcrypt class if needed
		if ($this->hash_method == 'bcrypt') {
			if ($this->random_rounds)
			{
				$rand = rand($this->min_rounds,$this->max_rounds);
				$params = array('rounds' => $rand);
			}
			else
			{
				$params = array('rounds' => $this->default_rounds);
			}

			$params['salt_prefix'] = $this->config->item('salt_prefix', 'ion_auth');
			$this->load->library('bcrypt',$params);
		}

		$this->trigger_events('model_constructor');
		
	}
	
	/** Searchable fields **/
	private $searchable_fields  = ['user.id', 'first_name', 'last_name', 'email', 'phone', 'username'];
	

	/**
	 * Misc functions
	 *
	 * Hash password : Hashes the password to be stored in the database.
	 * Hash password db : This function takes a password and validates it
	 * against an entry in the users table.
	 * Salt : Generates a random salt value.
	 *
	 * @author Mathew
	 */

	/**
	 * Hashes the password to be stored in the database.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function hash_password($password, $salt=false, $use_sha1_override=FALSE)
	{
		if (empty($password))
		{
			return FALSE;
		}

		// bcrypt
		if ($use_sha1_override === FALSE && $this->hash_method == 'bcrypt')
		{
			return $this->bcrypt->hash($password);
		}


		if ($this->store_salt && $salt)
		{
			return  sha1($password . $salt);
		}
		else
		{
			$salt = $this->salt();
			return  $salt . substr(sha1($salt . $password), 0, -$this->salt_length);
		}
	}

	/**
	 * This function takes a password and validates it
	 * against an entry in the users table.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function hash_password_db($id, $password, $use_sha1_override=FALSE)
	{
		if (empty($id) || empty($password))
		{
			return FALSE;
		}

		$this->trigger_events('extra_where');

		$query = $this->db->select('password, salt')
		                  ->where('id', $id)
		                  ->limit(1)
		                  ->order_by('id', 'desc')
		                  ->get($this->tables['user']);

		$hash_password_db = $query->row();

		if ($query->num_rows() !== 1)
		{
			return FALSE;
		}

		// bcrypt
		if ($use_sha1_override === FALSE && $this->hash_method == 'bcrypt')
		{
			if ($this->bcrypt->verify($password,$hash_password_db->password))
			{
				return TRUE;
			}

			return FALSE;
		}

		// sha1
		if ($this->store_salt)
		{
			$db_password = sha1($password . $hash_password_db->salt);
		}
		else
		{
			$salt = substr($hash_password_db->password, 0, $this->salt_length);

			$db_password =  $salt . substr(sha1($salt . $password), 0, -$this->salt_length);
		}

		if($db_password == $hash_password_db->password)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Generates a random salt value for forgotten passwords or any other keys. Uses SHA1.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function hash_code($password)
	{
		return $this->hash_password($password, FALSE, TRUE);
	}

	/**
	 * Generates a random salt value.
	 *
	 * Salt generation code taken from https://github.com/ircmaxell/password_compat/blob/master/lib/password.php
	 *
	 * @return void
	 * @author Anthony Ferrera
	 **/
	public function salt()
	{

		$raw_salt_len = 16;

 		$buffer = '';
        $buffer_valid = false;

        if (function_exists('random_bytes')) {
		  $buffer = random_bytes($raw_salt_len);
		  if ($buffer) {
		    $buffer_valid = true;
		  }
		}

		if (!$buffer_valid && function_exists('mcrypt_create_iv') && !defined('PHALANGER')) {
		     $buffer = mcrypt_create_iv($raw_salt_len, MCRYPT_DEV_URANDOM);
		    if ($buffer) {
		        $buffer_valid = true;
		    }
		}

        if (!$buffer_valid && function_exists('openssl_random_pseudo_bytes')) {
            $buffer = openssl_random_pseudo_bytes($raw_salt_len);
            if ($buffer) {
                $buffer_valid = true;
            }
        }

        if (!$buffer_valid && @is_readable('/dev/urandom')) {
            $f = fopen('/dev/urandom', 'r');
            $read = strlen($buffer);
            while ($read < $raw_salt_len) {
                $buffer .= fread($f, $raw_salt_len - $read);
                $read = strlen($buffer);
            }
            fclose($f);
            if ($read >= $raw_salt_len) {
                $buffer_valid = true;
            }
        }

        if (!$buffer_valid || strlen($buffer) < $raw_salt_len) {
            $bl = strlen($buffer);
            for ($i = 0; $i < $raw_salt_len; $i++) {
                if ($i < $bl) {
                    $buffer[$i] = $buffer[$i] ^ chr(mt_rand(0, 255));
                } else {
                    $buffer .= chr(mt_rand(0, 255));
                }
            }
        }

        $salt = $buffer;

        // encode string with the Base64 variant used by crypt
        $base64_digits   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
        $bcrypt64_digits = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $base64_string   = base64_encode($salt);
        $salt = strtr(rtrim($base64_string, '='), $base64_digits, $bcrypt64_digits);

	    $salt = substr($salt, 0, $this->salt_length);


		return $salt;

	}

	/**
	 * Activation functions
	 *
	 * Activate : Validates and removes activation code.
	 * Deactivate : Updates a users row with an activation code.
	 *
	 * @author Mathew
	 */

	/**
	 * activate
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function activate($id, $code = false)
	{
		$this->trigger_events('pre_activate');

		$id = ( isset($id) ) ? ( is_object($id) ? $id->id : $id ) : false;

		if ($code !== FALSE){
			$query = $this->db->select($this->identity_column)
			                  ->where('activation_code', $code)
			                  ->where('id', $id)
			                  ->limit(1)
		    				  ->order_by('id', 'desc')
			                  ->get($this->tables['user']);

			$result = $query->row();

			if ($query->num_rows() !== 1)
			{
				$this->trigger_events(array('post_activate', 'post_activate_unsuccessful'));
				$this->set_error('activate_unsuccessful');
				return FALSE;
			}

			$data = array(
			    'activation_code' => NULL,
			    'active'          => 1
			);

			$this->trigger_events('extra_where');
			$this->db->update($this->tables['user'], $data, array('id' => $id));
		}else{
			$data = array(
			    'activation_code' => NULL,
			    'active'          => 1
			);


			$this->trigger_events('extra_where');
			$this->db->update($this->tables['user'], $data, array('id' => $id));
		}


		$return = $this->db->affected_rows() == 1;
		if ($return)
		{
			$this->trigger_events(array('post_activate', 'post_activate_successful'));
			$this->set_message('activate_successful');
		}
		else
		{
			$this->trigger_events(array('post_activate', 'post_activate_unsuccessful'));
			$this->set_error('activate_unsuccessful');
		}


		return $return;
	}


	/**
	 * Deactivate
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function deactivate($id = NULL)
	{
		$this->trigger_events('deactivate');

		$id = ( isset($id) ) ? ( is_object($id) ? $id->id : $id ) : false;

		if (!isset($id)){
			$this->set_error('deactivate_unsuccessful');
			return FALSE;
		}elseif($this->ion_auth->logged_in() && $this->user()->row()->id == $id){
			$this->set_error('deactivate_current_user_unsuccessful');
			return FALSE;
		}

		$activation_code       = sha1(md5(microtime()));
		$this->activation_code = $activation_code;

		$data = array(
		    'activation_code' => $activation_code,
		    'active'          => 0
		);

		$this->trigger_events('extra_where');

		$this->db->update($this->tables['user'], $data, array('id' => $id));

		$return = $this->db->affected_rows() == 1;
		if ($return)
			$this->set_message('deactivate_successful');
		else
			$this->set_error('deactivate_unsuccessful');

		return $return;
	}

	public function clear_forgotten_password_code($code) {

		if (empty($code))
		{
			return FALSE;
		}

		$this->db->where('forgotten_password_code', $code);

		if ($this->db->count_all_results($this->tables['user']) > 0)
		{
			$data = array(
			    'forgotten_password_code' => NULL,
			    'forgotten_password_time' => NULL
			);

			$this->db->update($this->tables['user'], $data, array('forgotten_password_code' => $code));

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * reset password
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function reset_password($identity, $new) {
		$this->trigger_events('pre_change_password');

		if (!$this->identity_check($identity)) {
			$this->trigger_events(array('post_change_password', 'post_change_password_unsuccessful'));
			return FALSE;
		}

		$this->trigger_events('extra_where');

		$query = $this->db->select('id, password, salt')
		                  ->where($this->identity_column, $identity)
		                  ->limit(1)
		    			  ->order_by('id', 'desc')
		                  ->get($this->tables['user']);

		if ($query->num_rows() !== 1)
		{
			$this->trigger_events(array('post_change_password', 'post_change_password_unsuccessful'));
			$this->set_error('password_change_unsuccessful');
			return FALSE;
		}

		$result = $query->row();

		$new = $this->hash_password($new, $result->salt);

		// store the new password and reset the remember code so all remembered instances have to re-login
		// also clear the forgotten password code
		$data = array(
		    'password' => $new,
		    'remember_code' => NULL,
		    'forgotten_password_code' => NULL,
		    'forgotten_password_time' => NULL,
		);

		$this->trigger_events('extra_where');
		$this->db->update($this->tables['user'], $data, array($this->identity_column => $identity));

		$return = $this->db->affected_rows() == 1;
		if ($return)
		{
			$this->trigger_events(array('post_change_password', 'post_change_password_successful'));
			$this->set_message('password_change_successful');
		}
		else
		{
			$this->trigger_events(array('post_change_password', 'post_change_password_unsuccessful'));
			$this->set_error('password_change_unsuccessful');
		}

		return $return;
	}

	/**
	 * change password
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function change_password($identity, $old, $new)
	{
		$this->trigger_events('pre_change_password');

		$this->trigger_events('extra_where');

		$query = $this->db->select('id, password, salt')
		                  ->where($this->identity_column, $identity)
		                  ->limit(1)
		    			  ->order_by('id', 'desc')
		                  ->get($this->tables['user']);

		if ($query->num_rows() !== 1)
		{
			$this->trigger_events(array('post_change_password', 'post_change_password_unsuccessful'));
			$this->set_error('password_change_unsuccessful');
			return FALSE;
		}

		$user = $query->row();

		$old_password_matches = $this->hash_password_db($user->id, $old);

		if ($old_password_matches === TRUE)
		{
			// store the new password and reset the remember code so all remembered instances have to re-login
			$hashed_new_password  = $this->hash_password($new, $user->salt);
			$data = array(
			    'password' => $hashed_new_password,
			    'remember_code' => NULL,
			);

			$this->trigger_events('extra_where');

			$successfully_changed_password_in_db = $this->db->update($this->tables['user'], $data, array($this->identity_column => $identity));
			if ($successfully_changed_password_in_db)
			{
				$this->trigger_events(array('post_change_password', 'post_change_password_successful'));
				$this->set_message('password_change_successful');
			}
			else
			{
				$this->trigger_events(array('post_change_password', 'post_change_password_unsuccessful'));
				$this->set_error('password_change_unsuccessful');
			}

			return $successfully_changed_password_in_db;
		}

		$this->set_error('password_change_unsuccessful');
		return FALSE;
	}

	/**
	 * Checks username
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function username_check($username = '')
	{
		$this->trigger_events('username_check');

		if (empty($username))
		{
			return FALSE;
		}

		$this->trigger_events('extra_where');

		return $this->db->where('username', $username)
										->group_by("id")
										->order_by("id", "ASC")
										->limit(1)
		                ->count_all_results($this->tables['user']) > 0;
	}

	/**
	 * Checks email
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function email_check($email = '')
	{
		$this->trigger_events('email_check');

		if (empty($email))
		{
			return FALSE;
		}

		$this->trigger_events('extra_where');

		return $this->db->where('email', $email)
										->group_by("id")
										->order_by("id", "ASC")
										->limit(1)
		                ->count_all_results($this->tables['user']) > 0;
	}

	/**
	 * Identity check (check against email address and username fields )
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function identity_check($identity = ''){
		$this->trigger_events('identity_check');

		if (empty($identity)){
			return FALSE;
		}

		return $this->db->where( $this->identity_column, $identity )
			->or_where( 'username', $identity )
			->count_all_results( $this->tables['user'] ) > 0;
	}

	/**
	 * Insert a forgotten password key.
	 *
	 * @return bool
	 * @author Mathew
	 * @updated Ryan
	 * @updated 52aa456eef8b60ad6754b31fbdcc77bb
	 **/
	public function forgotten_password($identity)
	{
		if ( empty($identity) ){
			$this->trigger_events(array('post_forgotten_password', 'post_forgotten_password_unsuccessful'));
			return FALSE;
		}

		// All some more randomness
		$activation_code_part = "";
		if( function_exists("openssl_random_pseudo_bytes" ) ){
			$activation_code_part = openssl_random_pseudo_bytes(128);
		}

		for( $i=0; $i<1024; $i++ ) {
			$activation_code_part = sha1($activation_code_part . mt_rand() . microtime());
		}

		$key = $this->hash_code($activation_code_part.$identity);

		// If enable query strings is set, then we need to replace any unsafe characters so that the code can still work
		if ($key != '' && $this->config->item('permitted_uri_chars') != '' && $this->config->item('enable_query_strings') == FALSE){
			// preg_quote() in PHP 5.3 escapes -, so the str_replace() and addition of - to preg_quote() is to maintain backwards
			// compatibility as many are unaware of how characters in the permitted_uri_chars will be parsed as a regex pattern
			if ( ! preg_match("|^[".str_replace(array('\\-', '\-'), '-', preg_quote($this->config->item('permitted_uri_chars'), '-'))."]+$|i", $key)){
				$key = preg_replace("/[^".$this->config->item('permitted_uri_chars')."]+/i", "-", $key);
			}
		}

		// Limit to 40 characters since that's how our DB field is setup
		$this->forgotten_password_code = substr($key, 0, 40);

		$this->trigger_events('extra_where');

		$update = array(
		    'forgotten_password_code' => $key,
		    'forgotten_password_time' => time()
		);

		$this->db->update($this->tables['user'], $update, array($this->identity_column => $identity));

		$return = $this->db->affected_rows() == 1;

		if ($return)
			$this->trigger_events(array('post_forgotten_password', 'post_forgotten_password_successful'));
		else
			$this->trigger_events(array('post_forgotten_password', 'post_forgotten_password_unsuccessful'));

		return $return;
	}

	/**
	 * Forgotten Password Complete
	 *
	 * @return string
	 * @author Mathew
	 **/
	public function forgotten_password_complete($code, $salt=FALSE)
	{
		$this->trigger_events('pre_forgotten_password_complete');

		if (empty($code))
		{
			$this->trigger_events(array('post_forgotten_password_complete', 'post_forgotten_password_complete_unsuccessful'));
			return FALSE;
		}

		$profile = $this->where('forgotten_password_code', $code)->users()->row(); //pass the code to profile

		if ($profile) {

			if ($this->config->item('forgot_password_expiration', 'ion_auth') > 0) {
				//Make sure it isn't expired
				$expiration = $this->config->item('forgot_password_expiration', 'ion_auth');
				if (time() - $profile->forgotten_password_time > $expiration) {
					//it has expired
					$this->set_error('forgot_password_expired');
					$this->trigger_events(array('post_forgotten_password_complete', 'post_forgotten_password_complete_unsuccessful'));
					return FALSE;
				}
			}

			$password = $this->salt();

			$data = array(
			    'password'                => $this->hash_password($password, $salt),
			    'forgotten_password_code' => NULL,
			    'active'                  => 1,
			 );

			$this->db->update($this->tables['user'], $data, array('forgotten_password_code' => $code));

			$this->trigger_events(array('post_forgotten_password_complete', 'post_forgotten_password_complete_successful'));
			return $password;
		}

		$this->trigger_events(array('post_forgotten_password_complete', 'post_forgotten_password_complete_unsuccessful'));
		return FALSE;
	}

	/**
	 * register
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function register($identity, $password, $email, $additional_data = array(), $groups = array())
	{
		
		if( !empty($additional_data) ){
			$account_modules = ( isset($additional_data['account_modules']) && !empty($additional_data['account_modules']) ) ? $additional_data['account_modules'] : NULL;
			unset($additional_data['account_modules']);
			foreach( $additional_data as $key=>$value ){
				if( !in_array($key,['email','username','password','password_confirm']) ){
					$filter_add_data[$key] = ( !empty($value) ) ? trim( ucwords( strtolower( $value ) ) ) : NULL;
				}
			}
			$additional_data = $filter_add_data;
		}
		
		$this->trigger_events('pre_register');

		$manual_activation = $this->config->item('manual_activation', 'ion_auth');
		
		if ( $this->identity_check($identity) ){
			$this->set_error('account_creation_duplicate_identity');
			return FALSE;
		}elseif ( !$this->config->item('default_group', 'ion_auth') && empty($groups) ){
			$this->set_error('account_creation_missing_default_group');
			return FALSE;
		}

		// check if the default set in config exists in database
		$query = $this->db->get_where($this->tables['groups'],array('name' => $this->config->item('default_group', 'ion_auth')),1)->row();
		if( !isset($query->id) && empty($groups) ){
			$this->set_error('account_creation_invalid_default_group');
			return FALSE;
		}

		// capture default group details
		$default_group = $query;

		// IP Address
		$ip_address = $this->_prepare_ip($this->input->ip_address());
		$salt       = $this->store_salt ? $this->salt() : FALSE;
		$password   = ( empty( $password ) ) ? DEFAULT_PASSWORD : $password;
		$password   = $this->hash_password($password, $salt);

		// Users table.
		$data = array(
		    $this->identity_column   => $identity,
		    'username'   => $identity,
		    'password'   => $password,
		    'email'      => $email,
		    'ip_address' => $ip_address,
		    'created_on' => time(),
		    'active'     => ( $manual_activation === false ? 1 : 1),
			'account_id'=>( isset($additional_data['account_id']) && !empty($additional_data['account_id']) ) ? $additional_data['account_id'] : null,
			'account_user_id'=>$this->generate_account_user_id( $additional_data['account_id'] ),
			'user_type_id'=>( isset($additional_data['user_type_id']) && !empty($additional_data['user_type_id']) ) ? $additional_data['user_type_id'] : DEFAULT_USER_TYPE //Default user type (Standard User)
		);
		
		if ($this->store_salt){
			$data['salt'] = $salt;
		}

		// filter out any data passed that doesnt have a matching column in the users table
		// and merge the set user data and the additional data
		$user_data = array_merge($this->_filter_data($this->tables['user'], $additional_data), $data);

		$this->trigger_events('extra_set');
		$this->db->insert($this->tables['user'], $user_data);
		
		$id 		= $this->db->insert_id($this->tables['user'] . '_id_seq');
		
		// add in groups array if it doesn't exists and stop adding into default group if default group ids are set
		if( isset($default_group->id) && empty($groups) ){
			$groups[] = $default_group->id;
		}

		if (!empty($groups)){
			
			// add to groups
			foreach ($groups as $group){
				$this->add_to_group($group, $id);
			}
		}

		$this->trigger_events('post_register');

		if( !empty($id) && isset($account_modules) &&  !empty($account_modules) ){
			$new_user = $this->ion_auth->get_user_by_id($data['account_id'], $id);
			$this->module_service->create_user_module_access( $new_user, $account_modules );
		}

		return (isset($id) &&  !empty($id)) ? $id : FALSE;
	}

	/**
	 * login
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function login($identity, $password, $remember=FALSE){
		
		$login_identity = $this->config->item('identity','ion_auth');
		
		$this->trigger_events('pre_login');

		if (empty($identity) || empty($password)){
			$this->set_error('login_unsuccessful');
			return FALSE;
		}

		$this->trigger_events('extra_where');

		$this->db->select($this->identity_column . ',id , first_name, last_name, email, username, password, active, last_login,account_id,account_user_id,user_type_id,is_account_holder')
			->where($this->identity_column, $identity);

		if( $login_identity == 'email' ){
			$this->db->or_where('username', $identity);
		}else{
			$this->db->or_where('email', $identity);
		}
		
		$query = $this->db->limit(1)
			->order_by('id', 'desc')
			->get($this->tables['user']);

		if($this->is_max_login_attempts_exceeded($identity)){
			// Hash something anyway, just to take up time
			$this->hash_password($password);

			$this->trigger_events('post_login_unsuccessful');
			$this->set_error('login_timeout');
			return FALSE;
		}

		if ($query->num_rows() === 1)
		{
			$user = $query->row();

			$password = $this->hash_password_db($user->id, $password);

			if ($password === TRUE){
				if ($user->active == 0)
				{
					$this->trigger_events('post_login_unsuccessful');
					$this->set_error('login_unsuccessful_not_active');
					$this->session->set_flashdata('message','Account is inactive');
					return FALSE;
				}

				$this->set_session($user);

				$this->update_last_login($user->id);

				$this->clear_login_attempts($identity);

				if ($remember && $this->config->item('remember_users', 'ion_auth'))
				{
					$this->remember_user($user->id);
				}

				$this->trigger_events(array('post_login', 'post_login_successful'));
				$this->set_message('login_successful');

				//return TRUE;
				//$user = $this->session->userdata();
				unset($user->password);

				## Check if main account is active
				$account_status = $this->account_service->check_account_status( $user->account_id, true );

				if( !$account_status ){
					$message = $this->session->flashdata('message');
					$this->session->set_flashdata('message',$message);
					return FALSE;
				}
				$user->is_admin   = $this->ion_auth->is_admin( $user->id );
				$user->login_time = time();
				return $user;

			}
		}

		// Hash something anyway, just to take up time
		$this->hash_password($password);

		$this->increase_login_attempts($identity);

		$this->trigger_events('post_login_unsuccessful');
		$this->set_error('login_unsuccessful');

		return FALSE;
	}

    /**
     * recheck_session verifies if the session should be rechecked according to
     * the configuration item recheck_timer. If it does, then it will check if the user is still active
     * @return bool
     */
	public function recheck_session()
    {
        $recheck = (null !== $this->config->item('recheck_timer', 'ion_auth')) ? $this->config->item('recheck_timer', 'ion_auth') : 0;

        if($recheck!==0)
        {
            $last_login = $this->session->userdata('last_check');
            if($last_login+$recheck < time())
            {
                $query = $this->db->select('id')
                    ->where(array($this->identity_column=>$this->session->userdata('identity'),'active'=>'1'))
                    ->limit(1)
                    ->order_by('id', 'desc')
                    ->get($this->tables['user']);
                if ($query->num_rows() === 1)
                {
                    $this->session->set_userdata('last_check',time());
                }
                else
                {
                    $this->trigger_events('logout');

                    $identity = $this->config->item('identity', 'ion_auth');

                    if (substr(CI_VERSION, 0, 1) == '2')
                    {
                        $this->session->unset_userdata( array($identity => '', 'id' => '', 'user_id' => '') );
                    }
                    else
                    {
                        $this->session->unset_userdata( array($identity, 'id', 'user_id') );
                    }
                    return false;
                }
            }
        }

        return (bool) $this->session->userdata('identity');
    }

	/**
	 * is_max_login_attempts_exceeded
	 * Based on code from Tank Auth, by Ilya Konyukhov (https://github.com/ilkon/Tank-Auth)
	 *
	 * @param string $identity: user's identity
	 * @param string $ip_address: IP address
	 *                            Only used if track_login_ip_address set to TRUE.
	 *                            If NULL (default value), current IP address is used.
	 *                            Use get_last_attempt_ip($identity) to retrieve user's last IP
	 * @return boolean
	 **/
	public function is_max_login_attempts_exceeded($identity, $ip_address = NULL) {
		if ($this->config->item('track_login_attempts', 'ion_auth')) {
			$max_attempts = $this->config->item('maximum_login_attempts', 'ion_auth');
			if ($max_attempts > 0) {
				$attempts = $this->get_attempts_num($identity, $ip_address);
				return $attempts >= $max_attempts;
			}
		}
		return FALSE;
	}

	/**
	 * Get number of attempts to login occured from given IP-address or identity
	 * Based on code from Tank Auth, by Ilya Konyukhov (https://github.com/ilkon/Tank-Auth)
	 *
	 * @param string $identity: user's identity
	 * @param string $ip_address: IP address
	 *                            Only used if track_login_ip_address set to TRUE.
	 *                            If NULL (default value), current IP address is used.
	 *                            Use get_last_attempt_ip($identity) to retrieve user's last IP
	 * @return int
	 */
	public function get_attempts_num($identity, $ip_address = NULL)
	{
        if ($this->config->item('track_login_attempts', 'ion_auth')) {
            $this->db->select('1', FALSE);
            $this->db->where('login', $identity);
            if ($this->config->item('track_login_ip_address', 'ion_auth')) {
	        if (!isset($ip_address)) {
	            $ip_address = $this->_prepare_ip($this->input->ip_address());
	        }
            	$this->db->where('ip_address', $ip_address);
            }
            $this->db->where('time >', time() - $this->config->item('lockout_time', 'ion_auth'), FALSE);
            $qres = $this->db->get($this->tables['login_attempts']);
            return $qres->num_rows();
        }
        return 0;
	}

	/**
	 * Get a boolean to determine if an account should be locked out due to
	 * exceeded login attempts within a given period
	 *
	 * This function is only a wrapper for is_max_login_attempts_exceeded() since it
	 * only retrieve attempts within the given period.
	 * It is kept for retrocompatibility purpose.
	 *
	 * @param string $identity: user's identity
	 * @param string $ip_address: IP address
	 *                            Only used if track_login_ip_address set to TRUE.
	 *                            If NULL (default value), current IP address is used.
	 *                            Use get_last_attempt_ip($identity) to retrieve user's last IP
	 * @return boolean
	 */
	public function is_time_locked_out($identity, $ip_address = NULL) {
		return $this->is_max_login_attempts_exceeded($identity, $ip_address);
	}

	/**
	 * Get the time of the last time a login attempt occured from given IP-address or identity
	 *
	 * This function is no longer used.
	 * It is kept for retrocompatibility purpose.
	 *
	 * @param string $identity: user's identity
	 * @param string $ip_address: IP address
	 *                            Only used if track_login_ip_address set to TRUE.
	 *                            If NULL (default value), current IP address is used.
	 *                            Use get_last_attempt_ip($identity) to retrieve user's last IP
	 * @return int
	 */
	public function get_last_attempt_time($identity, $ip_address = NULL) {
		if ($this->config->item('track_login_attempts', 'ion_auth')) {
			$this->db->select('time');
			$this->db->where('login', $identity);
			if ($this->config->item('track_login_ip_address', 'ion_auth')) {
				if (!isset($ip_address)) {
					$ip_address = $this->_prepare_ip($this->input->ip_address());
				}
				$this->db->where('ip_address', $ip_address);
			}
			$this->db->order_by('id', 'desc');
			$qres = $this->db->get($this->tables['login_attempts'], 1);

			if($qres->num_rows() > 0) {
				return $qres->row()->time;
			}
		}

		return 0;
	}

	/**
	* Get the IP address of the last time a login attempt occured from given identity
	*
	 * @param string $identity: user's identity
	* @return string
	*/
	public function get_last_attempt_ip($identity) {
		if ($this->config->item('track_login_attempts', 'ion_auth') && $this->config->item('track_login_ip_address', 'ion_auth')) {
			$this->db->select('ip_address');
			$this->db->where('login', $identity);
			$this->db->order_by('id', 'desc');
			$qres = $this->db->get($this->tables['login_attempts'], 1);

			if($qres->num_rows() > 0) {
				return $qres->row()->ip_address;
			}
		}

		return '';
	}

	/**
	 * increase_login_attempts
	 * Based on code from Tank Auth, by Ilya Konyukhov (https://github.com/ilkon/Tank-Auth)
	 *
	 * Note: the current IP address will be used if track_login_ip_address config value is TRUE
	 *
	 * @param string $identity: user's identity
	 **/
	public function increase_login_attempts($identity) {
		if ($this->config->item('track_login_attempts', 'ion_auth')) {
			$data = array('ip_address' => '', 'login' => $identity, 'time' => time());
			if ($this->config->item('track_login_ip_address', 'ion_auth')) {
				$data['ip_address'] = $this->_prepare_ip($this->input->ip_address());
			}
			return $this->db->insert($this->tables['login_attempts'], $data);
		}
		return FALSE;
	}

	/**
	 * clear_login_attempts
	 * Based on code from Tank Auth, by Ilya Konyukhov (https://github.com/ilkon/Tank-Auth)
	 *
	 * @param string $identity: user's identity
	 * @param int $old_attempts_expire_period: in seconds, any attempts older than this value will be removed.
	 *                                         It is used for regularly purging the attempts table.
	 *                                         (for security reason, minimum value is lockout_time config value)
	 * @param string $ip_address: IP address
	 *                            Only used if track_login_ip_address set to TRUE.
	 *                            If NULL (default value), current IP address is used.
	 *                            Use get_last_attempt_ip($identity) to retrieve user's last IP
	 **/
	public function clear_login_attempts($identity, $old_attempts_expire_period = 86400, $ip_address = NULL) {
		if ($this->config->item('track_login_attempts', 'ion_auth')) {
			// Make sure $old_attempts_expire_period is at least equals to lockout_time
			$old_attempts_expire_period = max($old_attempts_expire_period, $this->config->item('lockout_time', 'ion_auth'));

			$this->db->where('login', $identity);
			if ($this->config->item('track_login_ip_address', 'ion_auth')) {
				if (!isset($ip_address)) {
					$ip_address = $this->_prepare_ip($this->input->ip_address());
				}
				$this->db->where('ip_address', $ip_address);
			}
			// Purge obsolete login attempts
			$this->db->or_where('time <', time() - $old_attempts_expire_period, FALSE);

			return $this->db->delete($this->tables['login_attempts']);
		}
		return FALSE;
	}

	public function limit($limit)
	{
		$this->trigger_events('limit');
		$this->_ion_limit = $limit;

		return $this;
	}

	public function offset($offset)
	{
		$this->trigger_events('offset');
		$this->_ion_offset = $offset;

		return $this;
	}

	public function where($where, $value = NULL)
	{
		$this->trigger_events('where');

		if (!is_array($where))
		{
			$where = array($where => $value);
		}

		array_push($this->_ion_where, $where);

		return $this;
	}

	public function like($like, $value = NULL, $position = 'both')
	{
		$this->trigger_events('like');

		array_push($this->_ion_like, array(
			'like'     => $like,
			'value'    => $value,
			'position' => $position
		));

		return $this;
	}

	public function select($select)
	{
		$this->trigger_events('select');

		$this->_ion_select[] = $select;

		return $this;
	}

	public function order_by($by, $order='desc')
	{
		$this->trigger_events('order_by');

		$this->_ion_order_by = $by;
		$this->_ion_order    = $order;

		return $this;
	}

	public function row()
	{
		$this->trigger_events('row');

		$row = $this->response->row();

		return $row;
	}

	public function row_array()
	{
		$this->trigger_events(array('row', 'row_array'));

		$row = $this->response->row_array();

		return $row;
	}

	public function result()
	{
		$this->trigger_events('result');

		$result = $this->response->result();

		return $result;
	}

	public function result_array()
	{
		$this->trigger_events(array('result', 'result_array'));

		$result = $this->response->result_array();

		return $result;
	}

	public function num_rows()
	{
		$this->trigger_events(array('num_rows'));

		$result = $this->response->num_rows();

		return $result;
	}

	/**
	 * users
	 *
	 * @return object Users
	 * @author Ben Edmunds
	 **/
	public function users($groups = NULL)
	{
		$this->trigger_events('users');

		if (isset($this->_ion_select) && !empty($this->_ion_select))
		{
			foreach ($this->_ion_select as $select)
			{
				$this->db->select($select);
			}

			$this->_ion_select = array();
		}
		else
		{
			//default selects
			$this->db->select(array(
			    $this->tables['user'].'.*',
			    $this->tables['user'].'.id as id',
			    $this->tables['user'].'.id as user_id'
			));
		}

		// filter by group id(s) if passed
		if (isset($groups))
		{
			// build an array if only one group was passed
			if (!is_array($groups))
			{
				$groups = Array($groups);
			}

			// join and then run a where_in against the group ids
			if (isset($groups) && !empty($groups))
			{
				$this->db->distinct();
				$this->db->join(
				    $this->tables['user_groups'],
				    $this->tables['user_groups'].'.'.$this->join['user'].'='.$this->tables['user'].'.id',
				    'inner'
				);
			}

			// verify if group name or group id was used and create and put elements in different arrays
			$group_ids = array();
			$group_names = array();
			foreach($groups as $group)
			{
				if(is_numeric($group)) $group_ids[] = $group;
				else $group_names[] = $group;
			}
			$or_where_in = (!empty($group_ids) && !empty($group_names)) ? 'or_where_in' : 'where_in';
			// if group name was used we do one more join with groups
			if(!empty($group_names))
			{
				$this->db->join($this->tables['groups'], $this->tables['user_groups'] . '.' . $this->join['groups'] . ' = ' . $this->tables['groups'] . '.id', 'inner');
				$this->db->where_in($this->tables['groups'] . '.name', $group_names);
			}
			if(!empty($group_ids))
			{
				$this->db->{$or_where_in}($this->tables['user_groups'].'.'.$this->join['groups'], $group_ids);
			}
		}

		$this->trigger_events('extra_where');

		// run each where that was passed
		if (isset($this->_ion_where) && !empty($this->_ion_where))
		{
			foreach ($this->_ion_where as $where)
			{
				$this->db->where($where);

			}

			$this->_ion_where = array();
		}

		if (isset($this->_ion_like) && !empty($this->_ion_like))
		{
			foreach ($this->_ion_like as $like)
			{
				$this->db->or_like($like['like'], $like['value'], $like['position']);
			}

			$this->_ion_like = array();
		}

		if (isset($this->_ion_limit) && isset($this->_ion_offset))
		{
			$this->db->limit($this->_ion_limit, $this->_ion_offset);

			$this->_ion_limit  = NULL;
			$this->_ion_offset = NULL;
		}
		else if (isset($this->_ion_limit))
		{
			$this->db->limit($this->_ion_limit);

			$this->_ion_limit  = NULL;
		}

		// set the order
		if (isset($this->_ion_order_by) && isset($this->_ion_order))
		{
			$this->db->order_by($this->_ion_order_by, $this->_ion_order);

			$this->_ion_order    = NULL;
			$this->_ion_order_by = NULL;
		}

		$this->response = $this->db->get($this->tables['user']);

		return $this;
	}

	/**
	 * user
	 *
	 * @return object
	 * @author Ben Edmunds
	 **/
	public function user($id = NULL)
	{
		$this->trigger_events('user');

		// if no id was passed use the current users id
		$id = isset($id) ? $id : $this->session->userdata('user_id');

		$this->limit(1);
		$this->order_by($this->tables['user'].'.id', 'desc');
		$this->where($this->tables['user'].'.id', $id);

		$this->users();

		return $this;
	}

	/**
	 * get_user_groups
	 *
	 * @return array
	 * @author Ben Edmunds
	 **/
	public function get_user_groups($id=FALSE)
	{
		$this->trigger_events('get_users_group');

		// if no id was passed use the current users id
		$id || $id = $this->session->userdata('user_id');

		return $this->db->select($this->tables['user_groups'].'.'.$this->join['groups'].' as id, '.$this->tables['groups'].'.name, '.$this->tables['groups'].'.description')
		                ->where($this->tables['user_groups'].'.'.$this->join['user'], $id)
		                ->join($this->tables['groups'], $this->tables['user_groups'].'.'.$this->join['groups'].'='.$this->tables['groups'].'.id')
		                ->get($this->tables['user_groups']);
	}

	/**
	 * add_to_group
	 *
	 * @return bool
	 * @author Ben Edmunds
	 **/
	public function add_to_group( $group_ids, $user_id=false )
	{
		$this->trigger_events('add_to_group');

		// if no id was passed use the current users id
		$user_id || $user_id = $this->session->userdata('user_id');

		if(!is_array($group_ids))
		{
			$group_ids = array($group_ids);
		}

		$return = 0;

		// Then insert each into the database
		foreach ( $group_ids as $group_id )
		{
			if ( $this->db->insert($this->tables['user_groups'], array( $this->join['groups'] => (float)$group_id, $this->join['user'] => (float)$user_id) ) )
			{
				if (isset($this->_cache_groups[$group_id])) {
					$group_name = $this->_cache_groups[$group_id];
				}
				else {
					$group = $this->group($group_id)->result();
					$group_name = $group[0]->name;
					$this->_cache_groups[$group_id] = $group_name;
				}
				$this->_cache_user_in_group[$user_id][$group_id] = $group_name;

				// Return the number of groups added
				$return += 1;
			}
		}

		return $return;
	}

	/**
	 * remove_from_group
	 *
	 * @return bool
	 * @author Ben Edmunds
	 **/
	public function remove_from_group( $group_ids=false, $user_id=false )
	{
		$this->trigger_events('remove_from_group');

		// user id is required
		if( empty( $user_id ) )
		{
			return FALSE;
		}

		// if group id(s) are passed remove user from the group(s)
		if( ! empty($group_ids))
		{
			if(!is_array( $group_ids ))
			{
				$group_ids = array($group_ids);
			}

			foreach($group_ids as $group_id)
			{
				$this->db->delete($this->tables['user_groups'], array($this->join['groups'] => (float)$group_id, $this->join['user'] => (float)$user_id));
				if (isset($this->_cache_user_in_group[$user_id]) && isset($this->_cache_user_in_group[$user_id][$group_id]))
				{
					unset($this->_cache_user_in_group[$user_id][$group_id]);
				}
			}
			
			$this->ssid_common->_reset_auto_increment( 'user_groups', 'id' );//housekeeping
			
			$return = TRUE;
		}
		// otherwise remove user from all groups
		else {
			if ( $return = $this->db->delete($this->tables['user_groups'], array( $this->join['user'] => (float)$user_id ) ) ) {
				$this->_cache_user_in_group[$user_id] = array();
				$this->ssid_common->_reset_auto_increment( 'user_groups', 'id' );//housekeeping
			}
		}
		return $return;
	}

	/**
	 * groups
	 *
	 * @return object
	 * @author Ben Edmunds
	 **/
	public function groups()
	{
		$this->trigger_events('groups');

		// run each where that was passed
		if (isset($this->_ion_where) && !empty($this->_ion_where))
		{
			foreach ($this->_ion_where as $where)
			{
				$this->db->where($where);
			}
			$this->_ion_where = array();
		}

		if (isset($this->_ion_limit) && isset($this->_ion_offset))
		{
			$this->db->limit($this->_ion_limit, $this->_ion_offset);

			$this->_ion_limit  = NULL;
			$this->_ion_offset = NULL;
		}
		else if (isset($this->_ion_limit))
		{
			$this->db->limit($this->_ion_limit);

			$this->_ion_limit  = NULL;
		}

		// set the order
		if (isset($this->_ion_order_by) && isset($this->_ion_order))
		{
			$this->db->order_by($this->_ion_order_by, $this->_ion_order);
		}

		$this->response = $this->db->get($this->tables['groups']);

		return $this;
	}

	/**
	 * group
	 *
	 * @return object
	 * @author Ben Edmunds
	 **/
	public function group($id = NULL)
	{
		$this->trigger_events('group');

		if (isset($id))
		{
			$this->where($this->tables['groups'].'.id', $id);
		}

		$this->limit(1);
		$this->order_by('id', 'desc');

		return $this->groups();
	}

	/**
	 * update
	 *
	 * @return bool
	 * @author Phil Sturgeon
	 **/
	public function update( $account_id=false, $id=false, $data=[]){

		if( !empty( $data ) ){
			$account_modules = ( isset( $data['account_modules']) && !empty($data['account_modules'] ) ) ? $data['account_modules'] : NULL;
			unset( $data['account_modules'] );
		
			$user_permissions = ( isset( $data['permissions'] ) && !empty($data['permissions'] ) ) ? $data['permissions'] : NULL;
			unset( $data['permissions'] );
		}

		$this->trigger_events( 'pre_update_user' );

		$user = $this->get_user_by_id( $account_id, $id, true );

		$this->db->trans_begin();

		if ( array_key_exists($this->identity_column, $data) && $this->identity_check($data[$this->identity_column]) && $user->{$this->identity_column} !== $data[$this->identity_column] ){
			$this->db->trans_rollback();
			$this->set_error('account_creation_duplicate_identity');

			$this->trigger_events( array( 'post_update_user', 'post_update_user_unsuccessful' ) );
			$this->set_error('update_unsuccessful');

			return FALSE;
		}
		
		$data = $this->_filter_data( $this->tables['user'], $data );
		
		if (array_key_exists($this->identity_column, $data) || array_key_exists('password', $data) || array_key_exists('email', $data)){
			if (array_key_exists('password', $data)){
				if( ! empty($data['password'])){
					$data['password'] = $this->hash_password($data['password'], $user->salt);
				}else{
					// unset password so it doesn't effect database entry if no password passed
					unset($data['password']);
				}
			}
		}

		$this->trigger_events('extra_where');

		unset( $data['account_id'],$data['is_account_holder'] );//Prevent changing account id and is_account_holder fields

		$this->db->update( $this->tables['user'], $data, array('id' => $user->id ) );

		if ( $this->db->trans_status() === FALSE ){
			$this->db->trans_rollback();

			$this->trigger_events(array('post_update_user', 'post_update_user_unsuccessful'));
			$this->set_error('update_unsuccessful');
			return FALSE;
		}

		$this->db->trans_commit();
		
		if( isset( $account_modules ) && !empty( $account_modules ) && $this->ion_auth->is_admin( $this->ion_auth->_current_user()->id ) ){ //Remember to check the user calling this function is an admin
			//if( isset($account_modules) && !empty($account_modules) ){
			$this->module_service->create_user_module_access( $user, $account_modules );
		}

		##Amend group permissions
		//$this->amend_group_permissions( $id, $data['user_type_id'], $user->user_type_id );
		
		$this->trigger_events( array( 'post_update_user', 'post_update_user_successful' ) );
		$this->set_message( 'update_successful' );
		return TRUE;
	}

	/**
	* delete_user
	*
	* @return bool
	* @author Phil Sturgeon
	**/
	public function delete_user( $account_id = false, $id = false ){
		
		//We're not allowing deletion of user records, user the Archive method
		$this->session->set_flashdata('message','Access denied. You can not delete a user resource');
		return false;
		
		
		$result = false;
		if( $account_id && $id ){
			
			$check_user = $this->db->get_where('user',['account_id'=>$account_id, 'id'=>$id])->row();
			if( !empty($check_user) ){
				$this->trigger_events('pre_delete_user');

				$this->db->trans_begin();

				// remove user from groups
				$this->remove_from_group(NULL, $id);

				// Remove this user's permissions
				$this->db->delete('user_permissions', ['fk_user_id'=> $id,'fk_account_id'=>$account_id] );
				
				// delete user from users table should be placed after remove from group
				$this->db->delete($this->tables['user'], ['id' => $id, 'account_id'=>$account_id]);
				
				if ($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$this->trigger_events(array('post_delete_user', 'post_delete_user_unsuccessful'));
					$this->set_error('delete_unsuccessful');
					$this->session->set_flashdata('message','User delete request failed');
					return $result;
				}

				$this->db->trans_commit();
				
				if ($this->db->trans_status() !== FALSE){
					$result = true;
					$this->trigger_events(array('post_delete_user', 'post_delete_user_successful'));
					$this->set_message('delete_successful');
					$this->session->set_flashdata('message','User record deleted successfully');
				}
			}else{
				$this->session->set_flashdata('message','User record not found. delete request failed');
			}
		}
		
		return $result;
	}

	/**
	 * update_last_login
	 * @return bool
	 * @author Ben Edmunds
	 **/
	public function update_last_login($id)
	{
		$this->trigger_events('update_last_login');

		$this->load->helper('date');

		$this->trigger_events('extra_where');

		$this->db->update($this->tables['user'], array('last_login' => time()), array('id' => $id));

		return $this->db->affected_rows() == 1;
	}

	/**
	 * set_lang
	 *
	 * @return bool
	 * @author Ben Edmunds
	 **/
	public function set_lang($lang = 'en')
	{
		$this->trigger_events('set_lang');

		// if the user_expire is set to zero we'll set the expiration two years from now.
		if($this->config->item('user_expire', 'ion_auth') === 0)
		{
			$expire = (60*60*24*365*2);
		}
		// otherwise use what is set
		else
		{
			$expire = $this->config->item('user_expire', 'ion_auth');
		}

		set_cookie(array(
			'name'   => 'lang_code',
			'value'  => $lang,
			'expire' => $expire
		));

		return TRUE;
	}

	/**
	 * set_session
	 *
	 * @return bool
	 * @author jrmadsen67
	 **/
	public function set_session($user)
	{

		$this->trigger_events('pre_set_session');

		$session_data = array(
		    'identity'             => $user->{$this->identity_column},
		    $this->identity_column             => $user->{$this->identity_column},
		    'email'                => $user->email,
		    'user_id'              => $user->id, //everyone likes to overwrite id so we'll use user_id
		    'old_last_login'       => $user->last_login,
		    'last_check'           => time(),
		    'login_time'           => date( 'H:i:s' ),
		);

		$this->session->set_userdata($session_data);

		$this->trigger_events('post_set_session');

		return TRUE;
	}

	/**
	 * remember_user
	 *
	 * @return bool
	 * @author Ben Edmunds
	 **/
	public function remember_user($id)
	{
		$this->trigger_events('pre_remember_user');

		if (!$id)
		{
			return FALSE;
		}

		$user = $this->user($id)->row();

		$salt = $this->salt();

		$this->db->update($this->tables['user'], array('remember_code' => $salt), array('id' => $id));

		if ($this->db->affected_rows() > -1)
		{
			// if the user_expire is set to zero we'll set the expiration two years from now.
			if($this->config->item('user_expire', 'ion_auth') === 0)
			{
				$expire = (60*60*24*365*2);
			}
			// otherwise use what is set
			else
			{
				$expire = $this->config->item('user_expire', 'ion_auth');
			}

			set_cookie(array(
			    'name'   => $this->config->item('identity_cookie_name', 'ion_auth'),
			    'value'  => $user->{$this->identity_column},
			    'expire' => $expire
			));

			set_cookie(array(
			    'name'   => $this->config->item('remember_cookie_name', 'ion_auth'),
			    'value'  => $salt,
			    'expire' => $expire
			));

			$this->trigger_events(array('post_remember_user', 'remember_user_successful'));
			return TRUE;
		}

		$this->trigger_events(array('post_remember_user', 'remember_user_unsuccessful'));
		return FALSE;
	}

	/**
	 * login_remembed_user
	 *
	 * @return bool
	 * @author Ben Edmunds
	 **/
	public function login_remembered_user()
	{
		$this->trigger_events('pre_login_remembered_user');

		// check for valid data
		if (!get_cookie($this->config->item('identity_cookie_name', 'ion_auth'))
			|| !get_cookie($this->config->item('remember_cookie_name', 'ion_auth'))
			|| !$this->identity_check(get_cookie($this->config->item('identity_cookie_name', 'ion_auth'))))
		{
			$this->trigger_events(array('post_login_remembered_user', 'post_login_remembered_user_unsuccessful'));
			return FALSE;
		}

		// get the user
		$this->trigger_events('extra_where');
		$query = $this->db->select($this->identity_column.', id, email, last_login')
					->where($this->identity_column, urldecode(get_cookie($this->config->item('identity_cookie_name', 'ion_auth'))))
					->where('remember_code', get_cookie($this->config->item('remember_cookie_name', 'ion_auth')))
					->where('active',1)
					->limit(1)
					->order_by('id', 'desc')
					->get($this->tables['user']);

		// if the user was found, sign them in
		if ($query->num_rows() == 1)
		{
			$user = $query->row();

			$this->update_last_login($user->id);

			$this->set_session($user);

			// extend the users cookies if the option is enabled
			if ($this->config->item('user_extend_on_login', 'ion_auth'))
			{
				$this->remember_user($user->id);
			}

			$this->trigger_events(array('post_login_remembered_user', 'post_login_remembered_user_successful'));
			return TRUE;
		}

		$this->trigger_events(array('post_login_remembered_user', 'post_login_remembered_user_unsuccessful'));
		return FALSE;
	}


	/**
	 * create_group
	 *
	 * @author aditya menon
	*/
	public function create_group($group_name = FALSE, $group_description = '', $additional_data = array())
	{
		// bail if the group name was not passed
		if(!$group_name)
		{
			$this->set_error('group_name_required');
			return FALSE;
		}

		// bail if the group name already exists
		$existing_group = $this->db->get_where($this->tables['groups'], array('name' => $group_name))->num_rows();
		if($existing_group !== 0)
		{
			$this->set_error('group_already_exists');
			return FALSE;
		}

		$data = array('name'=>$group_name,'description'=>$group_description);

		// filter out any data passed that doesnt have a matching column in the groups table
		// and merge the set group data and the additional data
		if (!empty($additional_data)) $data = array_merge($this->_filter_data($this->tables['groups'], $additional_data), $data);

		$this->trigger_events('extra_group_set');

		// insert the new group
		$this->db->insert($this->tables['groups'], $data);
		$group_id = $this->db->insert_id($this->tables['groups'] . '_id_seq');

		// report success
		$this->set_message('group_creation_successful');
		// return the brand new group id
		return $group_id;
	}

	/**
	 * update_group
	 *
	 * @return bool
	 * @author aditya menon
	 **/
	public function update_group($group_id = FALSE, $group_name = FALSE, $additional_data = array())
	{
		if (empty($group_id)) return FALSE;

		$data = array();

		if (!empty($group_name))
		{
			// we are changing the name, so do some checks

			// bail if the group name already exists
			$existing_group = $this->db->get_where($this->tables['groups'], array('name' => $group_name))->row();
			if(isset($existing_group->id) && $existing_group->id != $group_id)
			{
				$this->set_error('group_already_exists');
				return FALSE;
			}

			$data['name'] = $group_name;
		}

		// restrict change of name of the admin group
        $group = $this->db->get_where($this->tables['groups'], array('id' => $group_id))->row();
        if($this->config->item('admin_group', 'ion_auth') === $group->name && $group_name !== $group->name)
        {
            $this->set_error('group_name_admin_not_alter');
            return FALSE;
        }


		// IMPORTANT!! Third parameter was string type $description; this following code is to maintain backward compatibility
		// New projects should work with 3rd param as array
		if (is_string($additional_data)) $additional_data = array('description' => $additional_data);


		// filter out any data passed that doesnt have a matching column in the groups table
		// and merge the set group data and the additional data
		if (!empty($additional_data)) $data = array_merge($this->_filter_data($this->tables['groups'], $additional_data), $data);


		$this->db->update($this->tables['groups'], $data, array('id' => $group_id));

		$this->set_message('group_update_successful');

		return TRUE;
	}

	/**
	* delete_group
	*
	* @return bool
	* @author aditya menon
	**/
	public function delete_group($group_id = FALSE)
	{
		// bail if mandatory param not set
		if(!$group_id || empty($group_id))
		{
			return FALSE;
		}
		$group = $this->group($group_id)->row();
		if($group->name == $this->config->item('admin_group', 'ion_auth'))
		{
			$this->trigger_events(array('post_delete_group', 'post_delete_group_notallowed'));
			$this->set_error('group_delete_notallowed');
			return FALSE;
		}

		$this->trigger_events('pre_delete_group');

		$this->db->trans_begin();

		// remove all users from this group
		$this->db->delete($this->tables['user_groups'], array($this->join['groups'] => $group_id));
		// remove the group itself
		$this->db->delete($this->tables['groups'], array('id' => $group_id));

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			$this->trigger_events(array('post_delete_group', 'post_delete_group_unsuccessful'));
			$this->set_error('group_delete_unsuccessful');
			return FALSE;
		}

		$this->db->trans_commit();

		$this->trigger_events(array('post_delete_group', 'post_delete_group_successful'));
		$this->set_message('group_delete_successful');
		return TRUE;
	}

	public function set_hook($event, $name, $class, $method, $arguments)
	{
		$this->_ion_hooks->{$event}[$name] = new stdClass;
		$this->_ion_hooks->{$event}[$name]->class     = $class;
		$this->_ion_hooks->{$event}[$name]->method    = $method;
		$this->_ion_hooks->{$event}[$name]->arguments = $arguments;
	}

	public function remove_hook($event, $name)
	{
		if (isset($this->_ion_hooks->{$event}[$name]))
		{
			unset($this->_ion_hooks->{$event}[$name]);
		}
	}

	public function remove_hooks($event)
	{
		if (isset($this->_ion_hooks->$event))
		{
			unset($this->_ion_hooks->$event);
		}
	}

	protected function _call_hook($event, $name)
	{
		if (isset($this->_ion_hooks->{$event}[$name]) && method_exists($this->_ion_hooks->{$event}[$name]->class, $this->_ion_hooks->{$event}[$name]->method))
		{
			$hook = $this->_ion_hooks->{$event}[$name];

			return call_user_func_array(array($hook->class, $hook->method), $hook->arguments);
		}

		return FALSE;
	}

	public function trigger_events($events)
	{
		if (is_array($events) && !empty($events))
		{
			foreach ($events as $event)
			{
				$this->trigger_events($event);
			}
		}
		else
		{
			if (isset($this->_ion_hooks->$events) && !empty($this->_ion_hooks->$events))
			{
				foreach ($this->_ion_hooks->$events as $name => $hook)
				{
					$this->_call_hook($events, $name);
				}
			}
		}
	}

	/**
	 * set_message_delimiters
	 *
	 * Set the message delimiters
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function set_message_delimiters($start_delimiter, $end_delimiter)
	{
		$this->message_start_delimiter = $start_delimiter;
		$this->message_end_delimiter   = $end_delimiter;

		return TRUE;
	}

	/**
	 * set_error_delimiters
	 *
	 * Set the error delimiters
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function set_error_delimiters($start_delimiter, $end_delimiter)
	{
		$this->error_start_delimiter = $start_delimiter;
		$this->error_end_delimiter   = $end_delimiter;

		return TRUE;
	}

	/**
	 * set_message
	 *
	 * Set a message
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function set_message($message)
	{
		$this->messages[] = $message;

		return $message;
	}



	/**
	 * messages
	 *
	 * Get the messages
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function messages()
	{
		$_output = '';
		foreach ($this->messages as $message)
		{
			$messageLang = $this->lang->line($message) ? $this->lang->line($message) : '##' . $message . '##';
			$_output .= $this->message_start_delimiter . $messageLang . $this->message_end_delimiter;
		}

		return $_output;
	}

	/**
	 * messages as array
	 *
	 * Get the messages as an array
	 *
	 * @return array
	 * @author Raul Baldner Junior
	 **/
	public function messages_array($langify = TRUE)
	{
		if ($langify)
		{
			$_output = array();
			foreach ($this->messages as $message)
			{
				$messageLang = $this->lang->line($message) ? $this->lang->line($message) : '##' . $message . '##';
				$_output[] = $this->message_start_delimiter . $messageLang . $this->message_end_delimiter;
			}
			return $_output;
		}
		else
		{
			return $this->messages;
		}
	}


	/**
	 * clear_messages
	 *
	 * Clear messages
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function clear_messages()
	{
		$this->messages = array();

		return TRUE;
	}


	/**
	 * set_error
	 *
	 * Set an error message
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function set_error($error)
	{
		$this->errors[] = $error;

		return $error;
	}

	/**
	 * errors
	 *
	 * Get the error message
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function errors()
	{
		$_output = '';
		foreach ($this->errors as $error)
		{
			$errorLang = $this->lang->line($error) ? $this->lang->line($error) : '##' . $error . '##';
			$_output .= $this->error_start_delimiter . $errorLang . $this->error_end_delimiter;
		}

		return $_output;
	}

	/**
	 * errors as array
	 *
	 * Get the error messages as an array
	 *
	 * @return array
	 * @author Raul Baldner Junior
	 **/
	public function errors_array($langify = TRUE)
	{
		if ($langify)
		{
			$_output = array();
			foreach ($this->errors as $error)
			{
				$errorLang = $this->lang->line($error) ? $this->lang->line($error) : '##' . $error . '##';
				$_output[] = $this->error_start_delimiter . $errorLang . $this->error_end_delimiter;
			}
			return $_output;
		}
		else
		{
			return $this->errors;
		}
	}


	/**
	 * clear_errors
	 *
	 * Clear Errors
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function clear_errors()
	{
		$this->errors = array();

		return TRUE;
	}



	protected function _filter_data($table, $data)
	{
		$filtered_data = array();
		$columns = $this->db->list_fields($table);

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

	protected function _prepare_ip($ip_address) {
		// just return the string IP address now for better compatibility
		return $ip_address;
	}

	/**
	 * get_users_by_id
	 * @return array
	 **/
	public function get_user_by_id( $account_id=false, $id=false ){
		
		$this->trigger_events('get_user_by_id');

		// if no id was passed use the current users id
		if( !$id && !$account_id ){
			$this->set_error('resource_not_found');
			return false;
		}

		$this->db->select( 'user.id, first_name, last_name, email, username, salt, mobile_number, active, last_login, user.account_id,user.user_type_id, user_types.user_type_name `user_type`,is_account_holder, account_user_id', false );
		$this->db->join( 'user_types','user.user_type_id = user_types.user_type_id','left');

		$query = $this->db->limit(1)
			->order_by($this->tables['user'].'.first_name')
			->where($this->tables['user'].'.account_id', $account_id )
			->where($this->tables['user'].'.id', $id)
			->where($this->tables['user'].'.active', 1)
			->get($this->tables['user']);
		
		if( $query->num_rows() > 0 ){
			$user 			= $query->result()[0];
			$user->is_admin = $this->ion_auth->is_admin( $user->id );
			return $user;
		}else{
			return false;
		}
	}
	
	/**
	 * get_users by account id
	 * @return array
	 **/
	public function get_users_by_account_id( $account_id=FALSE ){
		$users = false;
		if( !empty($account_id) ){
			$this->trigger_events('get_users_by_account_id');
			$query = $this->db->select('user.id, first_name, last_name, email, username, active, last_login,account_id,user_types.user_type_name `user_type`',false)
				->join('user_types','user.user_type_id = user_types.user_type_id','left')
				->order_by($this->tables['user'].'.first_name')
				->where($this->tables['user'].'.account_id', $account_id)
				->get($this->tables['user']);
			if( $query->num_rows() > 0 ){
				$users = $query->result();
			}
		}
		return $users;
	}

	public function get_user_types( $user_type_id = FALSE ){
		$result = FALSE;
		$this->db->where('is_active',1);
		if($user_type_id){
			$row = $this->db->get_where('user_types',['user_type_id'=>$user_type_id])->row();
			if( !empty($row) ){
				$this->session->set_flashdata('message','User type record found');
				$result = $row;
			}else{
				$this->session->set_flashdata('message','User type record not found');
			}
			return $result;
		}

		$user_types = $this->db->order_by('user_type_name')
			->get('user_types');

		if( $user_types->num_rows() > 0 ){
			$this->session->set_flashdata('message','User type records found');
			$result = $user_types->result();
		}else{
			$this->session->set_flashdata('message','User type record(s) not found');
		}
		return $result;
	}
	
	/**
	* Get current logged in user
	*/
	public function _current_user(){
		
		$result 			= false;
		
		//get header vars
        $this->_head_args 	= $this->input->request_headers();
		$auth_header 		= ( !empty($this->_head_args['Authorization']) ) ? $this->_head_args['Authorization'] : ( !empty($this->_head_args['authorization']) ? $this->_head_args['authorization'] : FALSE );
		
		$auth_token 		= false;
		
		if (!empty( $auth_header )) {
			if (preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
				$auth_token = $matches[1];
			}
			$result = $this->_authenticate_token( $auth_token );
		} else {
			$sesh_data 		= $this->session->userdata('auth_data');
			$result 		= ( !empty( $sesh_data->user ) ) ? $sesh_data->user : false;
		}
		return $result;
	}
	
	
	/**
	* Check if user is logged in
	*/
	public function _check_loggin( $user_id = false ){
		$user 	= $this->_current_user();
		$result = ( $user ) ? $user->id : false;
		return $result;
	}
	
	/**
	* Log out current user
	*/
	public function logout(){
		$user 	= $this->_current_user();
		return $result;
	}
	
	/** 
	 * Check if $auth token is valid and signed correctly.
	 * @access protected
	 */
	public function _authenticate_token( $auth_token = false ){

		if( !empty($auth_token) ){
			$jwt_values = explode('.', $auth_token);
			# extracting the signature from the original JWT 
			$recieved_signature = $jwt_values[2];

			# Get the received header and the payload
			$received_header = $jwt_values[0];
			$received_payload= $jwt_values[1];
			
			# creating the Base 64 encoded new signature generated by applying the HMAC method to the concatenated header and payload values
			$resulted_signature = JWT::urlsafeB64Encode( hash_hmac( 'SHA512', $received_header.".".$received_payload, API_SECRET_KEY, true ) );

			// checking if the created signature is equal to the received signature
			if( $resulted_signature == $recieved_signature ) {				
				try {
					$decoded = JWT::decode( $auth_token, API_SECRET_KEY, [API_JWT_ALGORITHM] );
					$this->session->set_flashdata('message','Token authenticated successfully');
					return $decoded->data->user;
				} catch (\Exception $e) { // Also tried JwtException
					$this->session->set_flashdata('message',$e->getMessage());
					return FALSE;
				}
			}else{
				$this->session->set_flashdata('message','Invalid token. Please sign-in again!');
				return FALSE;
			}
		}else{
			$this->session->set_flashdata('message','Invalid request. Please sign-in again!');
			return FALSE;
		}
	}
	
	
	public function holiday_allowance_hours_left( $id = false, $account_id = false, $holiday_allowance_hours = false ){

		if( !$id || !$account_id ){
			return false;
		} else {
			
			$result = false;
			if( !empty($holiday_allowance_hours) ){
				$user = $this->get_user_by_id( $id, $account_id, true );
				$holiday_allowance_hours = ( !empty( $holiday_allowance_hours ) && ( $holiday_allowance_hours > 0 ) ) ? $holiday_allowance_hours : 0 ;
			}

			$this->db->select( "sum( working_hours ) `working_hours_used`", false );
			$this->db->where( "user_id", $id );
			$this->db->where( "account_id", $account_id );
			$this->db->where( "absence_status", "Approved" );
			$query = $this->db->get( "absences" );

			if( $query->num_rows() > 0 ){
				$dataset = $query->result();
				$used_holidays = $dataset[0]->working_hours_used;
			} else {
				$used_holidays = 0;
			}
			$result = ( ( $holiday_allowance_hours - $used_holidays ) > 0 ) ? ( $holiday_allowance_hours - $used_holidays ) : 0 ;
			return $result;
		}
	}
	
	/**
	 * Check if user is_account_holder
	 * @return bool	
	 * @author Simply SID	 
	 **/
	public function is_account_holder( $id=false ){
		if( $id ){
			$query = $this->db->select('id,is_account_holder',false)
				->where('is_account_holder',1)
				->where('id',$id)
				->get( 'users' );
			return ( $query->num_rows() > 0 ) ? true : false;
		}
		return false;
	}
	
	/** 
	* Get the hishest account user / employee ID from the users table
	*/
	public function generate_account_user_id( $account_id  ){
		
		if( $account_id ){
			$maxid  = 0;
			$row 	= $this->db->select( 'MAX(account_user_id) AS `maxid`', false )
				->where( 'user.account_id', $account_id )
				->get( 'user' )
				->row();
				
			if ($row) {
				$maxid = $row->maxid; 
			}
			$maxid++;
		}
		return $maxid;
		
	}
	
	/*
	* Do user lookup
	*/
	public function user_lookup( $account_id = false, $search_term = false, $user_types = false, $user_statuses = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;
		if( !empty( $account_id ) ){
			$this->db->select( 'user.id `id`, account_user_id, first_name, last_name, email, username, active, user.user_type_id, user_types.user_type_name',false )
				->where( 'account_id', $account_id )
				->where( 'active', 1 )
				->join( 'user_types', 'user_types.user_type_id = user.user_type_id', 'left');

			if( !empty( $search_term ) ){
				
				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->searchable_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}
						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				}else{
					foreach( $this->searchable_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}
					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}
			
			if( $user_types ){
				$user_types = ( !is_array( $user_types ) ) ? json_decode( $user_types ) : $user_types;
				$user_types = ( is_object( $user_types ) ) ? object_to_array( $user_types ) : $user_types;
				$this->db->where_in( 'user.user_type_id', $user_types );
			}

			if( $user_statuses ){
				$user_statuses = ( !is_array( $user_statuses ) ) ? json_decode( $user_statuses ) : $user_statuses;
				$user_statuses = ( is_object( $user_statuses ) ) ? object_to_array( $user_statuses ) : $user_statuses;
				$this->db->where_in( 'user.user_status_id', $user_statuses );
			}
			
			if( $where ){
				$this->db->where( $where );
			}
			
			if( $order_by ){
				$this->db->order_by( $order_by );
			}else{
				$this->db->order_by( 'first_name' );
			}
			
			$query = $this->db->limit( $limit, $offset )
				->get('user');
			
			if( $query->num_rows() > 0 ){
				$result = $query->result();
				$this->session->set_flashdata('message','User(s) data found.');
			}else{
				$this->session->set_flashdata('message','No records found matching your creteria.');
			}			
		}
		
		return $result;
	}
	
	/** Get a  **/
	public function get_total_users( $account_id = false, $search_term = false, $user_types = false, $user_statuses = false, $where = false, $limit = DEFAULT_LIMIT, $offset = 0 ){
		$result = false;
		if( !empty( $account_id ) ){
			$this->db->select( 'user.id `id`, account_user_id, first_name, last_name, email, username, active, user.user_type_id, user_types.user_type_name',false )
				->where( 'account_id', $account_id )
				->where( 'active', 1 )
				->join( 'user_types', 'user_types.user_type_id = user.user_type_id', 'left');

			if( !empty( $search_term ) ){
				
				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					
					$multiple_terms = explode( ' ', $search_term );
					
					foreach( $multiple_terms as $term ){
						foreach( $this->searchable_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}
						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				}else{
					foreach( $this->searchable_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}
					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}
			
			if( $user_types ){
				$user_types = ( !is_array( $user_types ) ) ? json_decode( $user_types ) : $user_types;
				$user_types = ( is_object( $user_types ) ) ? object_to_array( $user_types ) : $user_types;
				$this->db->where_in( 'user.user_type_id', $user_types );
			}

			if( $user_statuses ){
				$user_statuses = ( !is_array( $user_statuses ) ) ? json_decode( $user_statuses ) : $user_statuses;
				$user_statuses = ( is_object( $user_statuses ) ) ? object_to_array( $user_statuses ) : $user_statuses;
				$this->db->where_in( 'user.user_status_id', $user_statuses );
			}
			
			if( $where ){
				$this->db->where( $where );
			}
			
			$query = $this->db->from('user')->count_all_results();
			$results['total'] = !empty( $query ) ? $query : 0;
			$results['pages'] = !empty( $query ) ? ceil( $query / $limit ) : 0;
			return json_decode( json_encode( $results ) );		
		}
		return $result;
	}
	
	/** Get user-statuses by account id **/
	public function get_user_statuses( $account_id = false, $status_id = false ){
		$result = null;
		if( $account_id ){
			$this->db->where( 'user_statuses.account_id', $account_id );
			
			if( $status_id ){
				$this->db->where( 'user_statuses.status_id', $status_id );
			}
			
		}else{
			$this->db->where( '( user_statuses.account_id IS NULL OR user_statuses.account_id = "" )' );
		}
		
		$query = $this->db->select( 'user_statuses.*', false )
			->where( 'user_statuses.is_active', 1 )
			->get( 'user_statuses' );

		if( $query->num_rows() > 0 ){
			$result = $query->result();
		}else{
			$result = $this->get_user_statuses();
		}	
		return $result;
	}	
	
	/*
	* Archive a user record
	*/
	public function archive_user( $account_id = false, $user_id = false ){
		$result = false;
		if( $this->account_service->check_account_status( $account_id ) && !empty( $user_id ) ){			
			$conditions 	= [
				'account_id' 	=> $account_id,
				'id'			=> $user_id,
				'active'		=> 1
			];
			$person_exists 	= $this->db->get_where( 'user', $conditions )->row();			
			if( !empty( $person_exists ) ){
				$data = ['active'=>0];
				$this->db->where( $conditions )->update( 'user',$data );
				if( $this->db->trans_status() !== FALSE ){
					## Deactivate the attached user
					/* 
					No People module active yet - 15/04/2020
					$person_data = [
						'is_active'=>0,
						'last_modified_by'=>$this->ion_auth->_current_user()->id						
					];
					$this->db->where( ['account_id'=>$account_id,'person_id'=>$user_id] )->update('people', $person_data ); */
					$this->session->set_flashdata( 'message', 'Record deleted successfully.' );
					$result = true;
				}
			} else {
				$this->session->set_flashdata('message','Invalid user ID.');
			}

		}else{
			$this->session->set_flashdata('message','No user record found.');
		}
		return $result;
	}

	/** Amend group permissions **/
	public function amend_group_permissions( $user_id = false, $update_user_type_id = false, $current_user_type_id = false ){
		
		##Drop permissions just before updating user details
		if( !empty( $user_id ) && !empty( $update_user_type_id ) && ( $update_user_type_id != $current_user_type_id ) ){
			
			//Drop current groups
			$this->ion_auth->remove_from_group( '', $user_id );
			
			$group_ids 			= [2];//refer to user-group-db-types table
			$updated_user_type  = $this->get_user_types( $update_user_type_id ); 
			
			switch( $updated_user_type->user_group ){
				case 'admin':
					$group_ids[] = 1;
					//Add to admin & standard groups
					$this->add_to_group( $group_ids, $user_id );
					return true;
					break;
				case 'standard':
					//Add to standrad group only
					$this->add_to_group( $group_ids, $user_id );
					return true;
					break;
			}
		}
		return false;
	}
}