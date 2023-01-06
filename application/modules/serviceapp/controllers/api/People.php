<?php defined('BASEPATH' ) OR exit('No direct script access allowed' );
 
class People extends REST_Controller {

    function __construct(){
        // Construct the parent class
        parent::__construct();
		$this->load->model('People_model','people_service' );		
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth' ), $this->config->item('error_end_delimiter', 'ion_auth' ));
		$this->lang->load('auth' );
    }
	
	/**
	* Create new Person resource 
	*/
	public function create_post(){
		
		$people_data = $this->post();
		$account_id	 = (int)$this->post( 'account_id' );
		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'personal_email', 'Personal Email Address', 'required|valid_email|is_unique[people.personal_email]' );		
		#$this->form_validation->set_rules('department_id', 'Department ID', 'required' );
		
		if ($this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? ucfirst( validation_errors() ) : '';
		}

		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			## One of the required fields is invalid
			$message = [
				'status' => FALSE,
				'message' => 'Invalid data: ',
				'person' => NULL
			];
			
			$message['message'] = ( !$account_id)? $message['message'].'account_id, ': $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: ucfirst( $message['message'] );
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'person' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$new_person = $this->people_service->create_person( $account_id, $people_data );
		
		if( !empty( $new_person ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message' ),
				'person' => ( !empty( $new_person->records ) ) ? $new_person->records : $new_person
			];
			$this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message' ),
				'person' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
    }

	/** 
	* Update person resource 
	*/
	public function update_post(){

        $people_data= $this->post();
        $user_id 	= (int) $this->post( 'user_id' );
        $person_id 	= (int) $this->post( 'person_id' );
        $account_id = (int) $this->post( 'account_id' );
		
		$this->form_validation->set_rules( 'user_id', 'User ID', 'required' );
		$this->form_validation->set_rules( 'account_id', 'Main Account ID', 'required' );
		$this->form_validation->set_rules( 'person_id', 'Person ID', 'required' );

		if ($this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}
	
		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			## One of the required fields is invalid
			$message = [
				'status' => FALSE,
				'message' => 'Invalid data: ',
				'person' => NULL
			];
			
			$message['message'] = (!$account_id)? $message['message'].'account_id, ': $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'person' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
				
        ## Validate the person id.
        if ( $person_id <= 0 && $user_id <= 0 ){
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }
		
		$person = $this->people_service->get_people( $account_id, $user_id, $person_id );
		
		if( !$person ){
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message' ),
				'person' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	
		$current_account = $this->ion_auth->_current_user()->account_id;
		
		## Stop illegal updates
		if( ( $current_account != $account_id ) || ( $user_id != $person_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Illegal operation. This is not your resource!',
				'person' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		## Run person update
		$updated_person = $this->people_service->update_person( $account_id, $person_id, $people_data);
		
		if( !empty($updated_person) ){		
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message' ),
				'person' => ( !empty( $updated_person->records ) ) ? $updated_person->records : null
			];
			$this->response($message, REST_Controller::HTTP_OK); // Resource Updated
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message' ),
				'person' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
    }

	/** 
	* Get list of all People or a single person / employee
	*/
    public function people_get(){
		$account_id 		= (int) $this->get('account_id' );
		$user_id 			= (int) $this->get('user_id' );
		$person_id 			= (int) $this->get('person_id' );
		$where 				= $this->get('where' );	
		$order_by 			= $this->get('order_by' );	
		$limit		 		= ( $this->get('limit' ) )  ? (int) $this->get('limit' ) : DEFAULT_LIMIT;
		$offset	 			= ( $this->get('offset' ) ) ? (int) $this->get('offset' ) : 0;
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID',
				'people' => null,
				'counters' => null
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}

        $people = $this->people_service->get_people( $account_id, $user_id, $person_id, $where, $order_by, $limit, $offset );
		
		if( !empty( $people ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message' ),
				'people' => ( !empty( $people->records ) ) ? $people->records : null,
				'counters' => ( !empty( $people->counters ) ) ? $people->counters : null
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message' ),
				'people' => null,
				'counters' => null
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
    }
	
	/**
	* Delete Person resource
	*/
    public function delete_get(){
        $account_id = (int) $this->get('account_id' );
        $person_id 	= (int) $this->get('person_id' );
		
		if ( $person_id <= 0 ){
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'person' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$delete_person = $this->people_service->delete_person( $account_id, $person_id );
		
		if( !empty($delete_person) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message' ),
				'person' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message' ),
				'person' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
    }
	
	/**
	* Search through list of Persons
	*/
	public function lookup_get(){
		$account_id 	= (int) $this->get('account_id' );
		$limit 		 	= ( !empty( $this->get('limit' ) ) )  ? (int) $this->get('limit' )  : DEFAULT_LIMIT;
		$offset 	 	= ( !empty( $this->get('offset' ) ) ) ? (int) $this->get('offset' ) : DEFAULT_OFFSET;
		$where 		 	= $this->get('where' );
		$order_by    	= $this->get('order_by' );
		$search_term 	= trim( urldecode( $this->get('search_term' ) ) );

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'people' => NULL,
				'counters' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$people_lookup = $this->people_service->people_lookup( $account_id, $search_term, $where, $order_by, $limit, $offset );
		
		if( !empty( $people_lookup ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message' ),
				'people' => ( !empty( $people_lookup->records ) ) ? $people_lookup->records : null,
				'counters' => ( !empty( $people_lookup->counters ) ) ? $people_lookup->counters : null
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message' ),
				'people' => ( !empty( $people_lookup->records ) ) ? $people_lookup->records : null,
				'counters' => ( !empty( $people_lookup->counters ) ) ? $people_lookup->counters : null
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}
	
	/**
	* Get list of all departments
	*/
	public function departments_get(){
		$account_id   	 = (int) $this->get('account_id' );
		$department_id	 = (int)$this->get('department_id' );		
		$department_group= urldecode( $this->get('department_group' ) );
		$grouped  	  	 = $this->get('grouped' );
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'departments' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$departments = $this->people_service->get_departments( $account_id, $department_id, $department_group, $grouped );
		
		if( !empty($departments) ){
			$message = [
				'status' => TRUE,
				'message' => 'Department records found',
				'departments' => $departments
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message' => 'No records found',
				'departments' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}		
	/**
	* Get list of all Job titles
	*/
	public function job_titles_get(){
		$account_id   	 = (int) $this->get('account_id' );
		$job_title_id	 = (int)$this->get('job_title_id' );		
		$job_area		 = urldecode( $this->get('job_area' ) );
		$job_level		 = urldecode( $this->get('job_level' ) );
		$group_by  	  	 = urldecode( $this->get('group_by' ) );
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'job_titles' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$job_titles = $this->people_service->get_job_titles( $account_id, $job_title_id, $job_area, $job_level, $group_by );
		
		if( !empty( $job_titles ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message' ),
				'job_titles' => $job_titles
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message' => 'No records found',
				'job_titles' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}	
	
	/**
	* Get list of all Person-event logs by person id
	*/
	public function event_logs_get(){
		$person_id   = (int) $this->get('person_id' );
		$account_id  = (int) $this->get('account_id' );
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'event_logs' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$event_logs = $this->people_service->get_event_logs( $account_id, $person_id );
		
		if( !empty($event_logs) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message' ),
				'event_logs' => $event_logs
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message' => 'No records found',
				'event_logs' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}
	
	/**
	* Get list of all Job Poasitions
	*/
	public function job_positions_get(){
		$account_id   	= (int) $this->get('account_id' );
		$person_id	 	= (int)$this->get('person_id' );		
		$position_id	= (int)$this->get('position_id' );		
		$job_title_id	= (int)$this->get('job_title_id' );		
		$job_start_date	= urldecode( $this->get('job_start_date' ) );
		$job_end_date	= urldecode( $this->get('job_end_date' ) );
		$limit		 	= ( $this->get('limit' ) ) ? (int) $this->get('limit' ) : DEFAULT_LIMIT;
		$offset	 		= ( $this->get('offset' ) ) ? (int) $this->get('offset' ) : 0;
		
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'job_positions' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$job_positions = $this->people_service->get_job_positions( $account_id, $person_id, $position_id, $job_title_id,  $job_start_date, $job_end_date, $limit, $offset );
		
		if( !empty( $job_positions ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message' ),
				'job_positions' => $job_positions
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message' =>$this->session->flashdata('message' ),
				'job_positions' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}
	
	/** Create a person's contact address record **/
	public function create_contact_post(){
		
		$contact_data  = $this->post();
		$account_id	 = (int)$this->post( 'account_id' );
		$person_id	 = (int)$this->post( 'person_id' );
		
		$this->form_validation->set_rules('person_id', 'Person\'s ID', 'required' );
		$this->form_validation->set_rules('contact_first_name', 'Contact First Name', 'required' );
		$this->form_validation->set_rules('contact_last_name', 'Contact Last Name', 'required' );
		$this->form_validation->set_rules('relationship', 'Contact Relationship', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}
	
		if( !$account_id || ( isset( $validation_errors ) && !empty( $validation_errors ) ) ){
			## One of the required fields is invalid
			$message = [
				'status' => FALSE,
				'message' => 'Invalid data: ',
				'address_contact' => NULL
			];
			
			$message['message'] = (!$account_id)? $message['message'].'account_id, ': $message['message'];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response($message, REST_Controller::HTTP_OK);
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'address_contact' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$address_contact = $this->people_service->create_contact( $account_id, $person_id, $contact_data );
				
		if( !empty( $address_contact ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message' ),
				'address_contact' => $address_contact
			];
			$this->response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata('message' ),
				'address_contact' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		} 
    }
	
	/* Get list of all Contact Persons */
	public function address_contacts_get(){
		$account_id   	= (int) $this->get('account_id' );
		$person_id	 	= (int) $this->get('person_id' );		
		$contact_id		= (int) $this->get('contact_id' );		
		$address_type_id= $this->get('address_type_id' );		
		$limit		 	= ( $this->get('limit' ) ) ? (int) $this->get('limit' ) : DEFAULT_LIMIT;
		$offset	 		= ( $this->get('offset' ) ) ? (int) $this->get('offset' ) : 0;
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID.',
				'address_contacts' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$address_contacts = $this->people_service->get_address_contacts( $account_id, $person_id, $contact_id, $address_type_id, $limit, $offset );
		
		if( !empty( $address_contacts ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata('message' ),
				'address_contacts' => $address_contacts
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}else{
			$message = [
				'status' => FALSE,
				'message' =>$this->session->flashdata('message' ),
				'address_contacts' => NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
	}
	
	
	/**
	*	Get People categories
	**/
	public function people_category_get(){
		$get_set = $this->get();
		
		$account_id   	= ( !empty( $get_set['account_id'] ) ) ? ( int ) $get_set['account_id'] : false ;
		$category_id   	= ( !empty( $get_set['category_id'] ) ) ? ( int ) $get_set['category_id'] : false ;
		$ordered   		= ( !empty( $get_set['ordered'] ) ) ? $get_set['ordered'] : false ;
		$limit   		= ( !empty( $get_set['limit'] ) ) ? ( int ) $get_set['limit'] : DEFAULT_LIMIT ;
		$offset   		= ( !empty( $get_set['offset'] ) ) ? ( int ) $get_set['offset'] : 0 ;
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 		=> FALSE,
				'message' 		=> 'Invalid main Account ID.',
				'categories' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$categories = $this->people_service->get_people_category( $account_id, $category_id, $ordered, $limit, $offset );
		
		if( !empty( $categories ) ){
			$message = [
				'status' 		=> TRUE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'categories' 	=> $categories
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 		=> FALSE,
				'message' 		=> $this->session->flashdata( 'message' ),
				'categories' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/** Get a list of a Person's skillset **/
	public function personal_skills_get(){
		$account_id	= (int) $this->get( 'account_id' );
		$person_id 	= (int) $this->get( 'person_id' );
		$where 		= $this->get( 'where' );
		
		$this->form_validation->set_data( ['account_id'=>$account_id, 'person_id'=>$person_id ] );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'person_id', 'Person ID', 'required' );
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 			=> ( $this->session->flashdata('message' ) ) ? $this->session->flashdata('message' ) : 'Invalid main Account ID',
				'personal_skills' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$personal_skills 	= $this->people_service->get_personal_skills( $account_id, $person_id, $where );
		
		if( !empty( $personal_skills ) ){
			$message = [
				'status' 			=> TRUE,
				'http_code' 		=> REST_Controller::HTTP_OK,
				'message' 			=> $this->session->flashdata('message' ),
				'personal_skills' => $personal_skills
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
				'message' 			=> 'Personal Skillset not found',
				'personal_skills' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/** Add Personal Skill **/
	public function add_personal_skills_post(){
		$postdata 	 = $this->post();
		$account_id  = $this->post( 'account_id' );
		$job_id  	 = $this->post( 'person_id' );
		
		$this->form_validation->set_rules('account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules('person_id', 'Person ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			## One of the required fields is invalid
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid Job data: ',
				'personal_skills' => NULL	
			];
			$message['message'] 	= ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid main Account ID',
				'personal_skills' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$personal_skills = $this->people_service->add_personal_skills( $account_id, $job_id, $postdata );
		
		if( !empty( $personal_skills ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata('message' ),
				'personal_skills' => $personal_skills
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); // CREATED (201) being the HTTP response code
		}else{
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata('message' ),
				'personal_skills' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	
	/** Remove Personal Skill-set from a Person **/
	public function remove_personal_skills_post(){
		$postdata 	 = $this->post();
		$account_id  = $this->post( 'account_id' );
		$person_id 	 = $this->post( 'person_id' );
		
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'person_id', 'Person ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			## One of the required fields is invalid
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid Job data: ',
				'personal_skills' => NULL	
			];
			$message['message'] 	= ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid main Account ID',
				'personal_skills' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$remove_skills = $this->people_service->remove_personal_skills( $account_id, $person_id, $postdata );
		
		if( !empty( $remove_skills ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata('message' ),
				'personal_skills' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		}else{
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata('message' ),
				'personal_skills' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	
	/** Add a Person's associated Regions for work **/
	public function assign_regions_post(){
		$postdata 	= $this->post();
		$account_id = !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;
		$person_id 	= !empty( $this->post( 'person_id' ) ) 	? ( int ) $this->post( 'person_id' ) 	: false;
		
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'person_id', 'Person ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid Person\'s data: ',
				'assigned_regions'=> NULL	
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 				=> FALSE,
				'message' 				=> 'Invalid main Account ID',
				'assigned_regions' 	=> NULL
			];
			$this->response($message, REST_Controller::HTTP_OK);
		}
		
		$assigned_regions = $this->people_service->assign_regions( $account_id, $person_id, $postdata );
		
		if( !empty( $assigned_regions ) ){
			$message = [
				'status' 				=> TRUE,
				'message' 				=> $this->session->flashdata( 'message' ),
				'assigned_regions' 	=> $assigned_regions
			];
			$this->response( $message, REST_Controller::HTTP_CREATED ); 
		}else{
			$message = [
				'status' 				=> FALSE,
				'message' 				=> $this->session->flashdata( 'message' ),
				'assigned_regions' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	
	/** Get a list of a Person's associated regions for work **/
	public function assigned_regions_get(){
		$account_id	= (int) $this->get( 'account_id' );
		$person_id 	= (int) $this->get( 'person_id' );
		$where 		= $this->get( 'where' );
		
		$this->form_validation->set_data( ['account_id'=>$account_id, 'person_id'=>$person_id ] );
        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'person_id', 'Person ID', 'required' );
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_ACCOUNT_VALIDATION_FAILED,
				'message' 			=> ( $this->session->flashdata('message' ) ) ? $this->session->flashdata('message' ) : 'Invalid main Account ID',
				'assigned_regions'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$assigned_regions 	= $this->people_service->get_assigned_regions( $account_id, $person_id, $where );
		
		if( !empty( $assigned_regions ) ){
			$message = [
				'status' 			=> TRUE,
				'http_code' 		=> REST_Controller::HTTP_OK,
				'message' 			=> $this->session->flashdata('message' ),
				'assigned_regions'=> $assigned_regions
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}else{
			$message = [
				'status' 			=> FALSE,
				'http_code' 		=> REST_Controller::HTTP_NO_CONTENT,
				'message' 			=> 'Personal assigned regions not found',
				'assigned_regions'=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
	
	
	/** Remove associated regions from a Person **/
	public function unassign_regions_post(){
		$postdata 	= $this->post();
		$account_id = !empty( $this->post( 'account_id' ) ) ? ( int ) $this->post( 'account_id' ) 	: false;
		$person_id 	= !empty( $this->post( 'person_id' ) ) 	? ( int ) $this->post( 'person_id' ) 	: false;
		
		$this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
		$this->form_validation->set_rules( 'person_id', 'Person ID', 'required' );

		if ( $this->form_validation->run() == false ){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){		
			$message = [
				'status' => FALSE,
				'message' => 'Invalid Job data: ',
				'assigned_regions' => NULL	
			];
			$message['message'] = ( isset( $validation_errors ) && !empty( $validation_errors ) ) 	? 'Validation errors: '.$validation_errors 	: $message['message'];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' => FALSE,
				'message' => 'Invalid main Account ID',
				'assigned_regions' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
		
		$remove_regions = $this->people_service->unassign_regions( $account_id, $person_id, $postdata );
		
		if( !empty( $remove_regions ) ){
			$message = [
				'status' => TRUE,
				'message' => $this->session->flashdata( 'message' ),
				'assigned_regions' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_CREATED );
		}else{
			$message = [
				'status' => FALSE,
				'message' => $this->session->flashdata( 'message' ),
				'assigned_regions' => NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		} 
	}
	
	
	
	/*
	*	This is to update a contact address
	*/
	public function update_contact_post(){
		$validation_errors = $post_data = $contact_exists = false;

		$post_data 		= $this->post();

		$contact_id 	= ( !empty( $post_data['contact_id'] ) ) ? $post_data['contact_id'] : false ;
		unset( $post_data['contact_id'] );

		$data 			= ( !empty( $post_data['dataset'] ) ) ? json_decode( $post_data['dataset'] ) : false ;
		unset( $post_data['dataset'] );

		$account_id 	= ( !empty( $post_data['account_id'] ) ) ? $post_data['account_id'] : false ;
		unset( $post_data['account_id'] );

        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'contact_id', 'Contact ID', 'required' );
        $this->form_validation->set_rules( 'dataset', 'Update Data', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid or missing Field(s)',
				'updated_contact' 	=> NULL
			];
			$message['message'] = 'Validation errors: '.trim( $validation_errors );
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'updated_contact'	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$contact_exists = $this->people_service->get_address_contacts( $account_id, false, $contact_id );

		if( !$contact_exists ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> "Invalid Contact ID",
				'updated_contact' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$updated_contact = $this->people_service->update_contact( $account_id, $contact_id, $data );

		if( !empty( $updated_contact ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'updated_contact' 	=> $updated_contact
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'updated_contact' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}


	/*
	*	This function will delete Contact Address
	*/
	public function delete_contact_post(){
		$validation_errors = $post_data = $contact_exists = false;

		$post_data 		= $this->post();

		$contact_id 	= ( !empty( $post_data['contact_id'] ) ) ? $post_data['contact_id'] : false ;
		unset( $post_data['contact_id'] );

		$account_id 	= ( !empty( $post_data['account_id'] ) ) ? $post_data['account_id'] : false ;
		unset( $post_data['account_id'] );

        $this->form_validation->set_rules( 'account_id', 'Account ID', 'required' );
        $this->form_validation->set_rules( 'contact_id', 'Contact ID', 'required' );

		if( $this->form_validation->run() == false){
			$validation_errors = ( validation_errors() ) ? validation_errors() : '';
		}

		if( isset( $validation_errors ) && !empty( $validation_errors ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> 'Invalid or missing Field(s)',
				'deleted_contact' 	=> NULL
			];
			$message['message'] = 'Validation errors: '.trim( $validation_errors );
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		if( !$this->account_service->check_account_status( $account_id ) ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'deleted_contact'	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$contact_exists = $this->people_service->get_address_contacts( $account_id, false, $contact_id );

		if( !$contact_exists ){
			$message = [
				'status' 			=> FALSE,
				'message' 			=> "Invalid Contact ID",
				'deleted_contact' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}

		$deleted_contact = $this->people_service->delete_contact( $account_id, $contact_id );

		if( !empty( $deleted_contact ) ){
			$message = [
				'status' 			=> TRUE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'deleted_contact' 	=> $deleted_contact
			];

			$this->response( $message, REST_Controller::HTTP_OK );
		} else {
			$message = [
				'status' 			=> FALSE,
				'message' 			=> $this->session->flashdata( 'message' ),
				'deleted_contact' 	=> NULL
			];
			$this->response( $message, REST_Controller::HTTP_OK );
		}
	}
}
