<?php

namespace Application\Modules\Web\Controllers;

class Workforcemanager extends MX_Controller {

	function __construct(){
		parent::__construct();
	
		if( !$this->identity() ){
			redirect('webapp/user/login', 'refresh');
		}
		
		$this->workforce_areas = ["gas", "electricity", "installations"];

	}

	/* 
	*	Until will be decided what should go on the landing page, temporarily is pointing at the 'add_profile' section. 
	*/
	public function index(){
		
		redirect( 'webapp/workforcemanager/add_profile', 'refresh' );
	}
	

	/* Function to create a new engineer profile */
	public function add_profile(){

		$data['feedback'] 			= !empty( $this->session->flashdata( 'feedback' ) ) ? ( $this->session->flashdata( 'feedback' ) ) : false ;

		$data['active_class'] 		= 'add_profile';
		$data['workforce_areas'] 	= $this->workforce_areas;
		
		$postset 					= $this->input->post();
		if( !empty( $postset ) ){
			$postdata = [];
			$postdata 					= $postset;
			$postdata["account_id"]		= $this->user->account_id;
			$url 						= 'workforcemanager/add_profile';
 			$method						= 'POST';
			$API_result					= $this->ssid_common->api_call( $url, $postdata, $method );
			
			if( ( !empty( $API_result->new_profile ) ) ){
				if( ( !empty( $API_result->message ) ) ){
					$this->session->set_flashdata( 'feedback', $API_result->message );
				} 
				redirect( 'webapp/workforcemanager/profile/'.$API_result->new_profile->profile_id, 'refresh' );
			} else {
				if( ( !empty( $API_result->message ) ) ){
					$this->session->set_flashdata( 'feedback', $API_result->message );
				}
				redirect( 'webapp/workforcemanager/add_profile/', 'refresh' );
			}
		}
		
		$postdata 				= ['account_id' => $this->user->account_id];
		$url 					= 'user/users';
		$method					= 'GET';
		$users_list				= $this->ssid_common->api_call( $url, $postdata, $method );
		$data['users_list']		= ( !empty( $users_list->users ) ) ? $users_list->users :  false ;
		
		$this->_render_webpage( 'workforcemanager/add_profile', $data );
	}
	
	
	/*
	*	To show the operatives profile data. 
	*	The personal data section needs to be done on the user creation side
	*/
	
	public function profile( $profile_id = false ){
		
		if( !empty( $profile_id ) ){
			
			$data['feedback'] 			= !empty( $this->session->flashdata( 'feedback' ) ) ? ( $this->session->flashdata( 'feedback' ) ) : false ;
			$data['active_class'] 		= 'profile';
			$data['workforce_areas'] 	= $this->workforce_areas;
			
			$postdata['account_id'] 	= $this->user->account_id;
			$postdata['profile_id'] 	= $profile_id;
			$url 						= 'workforcemanager/get_profile';
			
			$profile_data				= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );
			$data['profile_data']		= ( !empty( $profile_data->profile ) ) ? $profile_data->profile :  false ;
			
			$this->_render_webpage( 'workforcemanager/profile', $data );
		} else {
			redirect( 'webapp/workforcemanager/add_profile', 'refresh' );
		}
	}
	
	
	/*
	*	To update the operative profile data. The personal details (email, phone number) needs to be changed from the user management section
	*/
	
	public function update(){
		
		$postdata 		= $this->input->post( 'postdata' );

		if( !empty( $postdata['account_id'] ) && !empty( $postdata['profile_id'] ) ){
			
			$url 				= 'workforcemanager/update';
			$updated_profile	= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );
			
			if( ( !empty( $updated_profile->status ) ) && ( $updated_profile->status == 1 ) ){
				$this->session->set_flashdata( 'feedback', ( !empty( $updated_profile->message ) ) ? ( $updated_profile->message ) : "The Profile has been updated successfully." );
			} else {
				$this->session->set_flashdata( 'feedback', ( !empty( $updated_profile->message ) ) ? ( $updated_profile->message ) : "The Profile has NOT been updated." );
			}
			redirect( 'webapp/workforcemanager/profile/'.$postdata['profile_id'], 'refresh' );
		} else {
			redirect( 'webapp/workforcemanager/add_profile/', 'refresh' );
		}
	}
	
	/*
	*	To update the operative profile data. The personal details (email, phone number) needs to be changed from the user management section
	*/
	
	public function delete_profile(){
		
		$postdata 		= $this->input->post( 'postdata' );

		if( !empty( $postdata['account_id'] ) && !empty( $postdata['profile_id'] ) ){
			
			$url 				= 'workforcemanager/delete';
			$deleted_profile	= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );
			
			if( ( !empty( $deleted_profile->status ) ) && ( $deleted_profile->status == 1 ) ){
				$this->session->set_flashdata( 'feedback', ( !empty( $deleted_profile->message ) ) ? ( $deleted_profile->message ) : "The Profile has been deleted successfully." );
			} else {
				$this->session->set_flashdata( 'feedback', ( !empty( $deleted_profile->message ) ) ? ( $deleted_profile->message ) : "The Profile has NOT been deleted." );
			}
			redirect( 'webapp/workforcemanager/add_profile/', 'refresh' );
		} else {
			redirect( 'webapp/workforcemanager/profile/'.$postdata['profile_id'], 'refresh' );
		}
	}
}
