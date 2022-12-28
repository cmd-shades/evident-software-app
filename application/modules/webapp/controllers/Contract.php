<?php

namespace Application\Modules\Web\Controllers;

class Contract extends MX_Controller {

	function __construct(){
		parent::__construct();
		$this->module_id 	   = $this->webapp_service->_get_module_id( $this->router->fetch_class() );
		$this->load->library( 'pagination' );
		$this->load->model( 'serviceapp/contract_model','contract_service' );
        $this->load->model( 'serviceapp/site_model','site_service' );
        $this->load->model( 'serviceapp/asset_model','asset_service' );

		if( !$this->identity() ){
			redirect( 'webapp/user/login', 'refresh' );
		}
	}

	/**
	* Index file
	**/
	public function index(){
		$this->contracts( 'details' );
	}

	/*
	*	The main Index page
	*/
	public function stats( $contract_id = false, $page = "details" ){


        if(!empty($contract_id)){

            $section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

            # Check module-item access
            $item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
            if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
                $this->_render_webpage( 'errors/access-denied', false );
            } else {

					$postdata 	  		= array('account_id'=>$this->user->account_id, 'contract_id' => $contract_id);
					$data["contract_information"] = $this->webapp_service->api_dispatcher( $this->api_end_point.'contract/contracts', $postdata, $this->options, true );

					if( $data["contract_information"] ){
						 $postdata 	  		= array('account_id'=>$this->user->account_id, "stat_type" => "audit_result_status");
					$data["building_compliance"]	= $this->webapp_service->api_dispatcher( $this->api_end_point.'site/site_stats', $postdata, $this->options, true );


					$postdata 	  	= array( 'account_id'=>$this->user->account_id, 'stat_type'=>'periodic_audits' );
					$data["audit_stats"]	= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/audit_stats', $postdata, $this->options, true );


					$postdata 	  		= array( 'account_id'=>$this->user->account_id, "stat_type" =>"replace_cost"  );
					$eol_replacement	= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/asset_stats', $postdata, $this->options, true );
					$data["replacement_cost"] = ($eol_replacement->status) ? array("replacement_cost" => $eol_replacement->asset_stats->replacement_cost, "status" => $eol_replacement->status) : array(null, "status" => "false");


					$postdata 	  		= array( 'account_id'=>$this->user->account_id, "stat_type" =>"eol"  );
					$data['eol_full_replacement']	= $this->webapp_service->api_dispatcher( $this->api_end_point.'asset/asset_stats', $postdata, $this->options, true );


					$this->_render_webpage( 'contract/contract_stats', $data );
				} else {
					redirect( 'webapp/contract/contracts', 'refresh' );
				}
            }

        } else {
            redirect( 'webapp/contract/contracts', 'refresh' );
        }

	}


	/*
	*	Function to show the contracts - get most recent Contract Profiles
	*/
	public function contracts( $page = "details" ){
		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			$data['feedback'] 		= !empty( $this->session->flashdata( 'feedback' ) ) ? ( $this->session->flashdata( 'feedback' ) ) : false ;
			$data['active_class'] 	= 'contract_dashboard';

			$data['contract_data'] 	= false;
			$postdata 				= ['account_id' => $this->user->account_id];
			$url 					= 'contract/contracts';
			$API_result				= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );

			if( ( !empty( $API_result->status ) ) && ( $API_result->status == 1 ) ){
				if( !empty( $API_result->contract ) ){
					$data['contract_data'] = $API_result->contract;
				}
			}

			$data['contract_statuses'] 	= false;
			$postdata['ordered']		= "yes";
			$url 						= 'contract/contract_statuses';
			$contract_statuses			= $this->ssid_common->api_call( $url, $postdata, 'GET' );
			$data['contract_statuses']	= ( !empty( $contract_statuses->statuses ) ) ? $contract_statuses->statuses :  false ;

			$data['contract_types'] 	= false;
			$url 						= 'contract/contract_types';
			$contract_types				= $this->ssid_common->api_call( $url, $postdata, 'GET' );
			$data['contract_types']		= ( !empty( $contract_types->types ) ) ? $contract_types->types :  false ;

			$data['stats_to_dashboard']	= [1, 2, 3, 4];
			$data['quick_stats'] 		= false;
			$postdata['where']			= ( "contract_status_id in ( ".implode( ", ", $data['stats_to_dashboard'] )." )" );
			$url 						= 'contract/quick_stats';
			$request					= $this->ssid_common->api_call( $url, $postdata, 'GET' );

			if( !empty( $request->status ) && ( $request->status == 1 ) ){
				$data['quick_stats']	=  ( !empty( $request->quick_stats ) ) ? $request->quick_stats : false ;
			}

			$this->_render_webpage( 'contract/dashboard', $data );
		}
	}


	/*
	*	Function to create a new Contract Profile
	*/
	public function add_contract(){

 		$data['feedback'] 			= !empty( $this->session->flashdata( 'feedback' ) ) ? ( $this->session->flashdata( 'feedback' ) ) : false ;
		$data['active_class'] 		= 'add_contract';

		$postset 					= $this->input->post();
		if( !empty( $postset ) ){
			$postdata = [];
			$postdata 					= $postset;
			$postdata["account_id"]		= $this->user->account_id;
			$url 						= 'contract/add';

			$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

			if( ( !empty( $API_result->status ) && ( $API_result->status == true ) ) ){

				$result = [
					'status'		=> $API_result->status,
					'message'		=> $API_result->message,
					'contract_id'	=> $API_result->new_contract->contract_id,
				];
				print_r( json_encode( $result ) );
				die();

			} else {
				redirect( 'webapp/contract/add_contract/', 'refresh' );
			}
		}

		$postdata['account_id'] = $this->user->account_id;
		$url 					= 'user/users';
		$contract_leaders		= $this->ssid_common->api_call( $url, $postdata, 'GET' );
		$data['contract_leaders']= ( !empty( $contract_leaders->users ) ) ? $contract_leaders->users :  false ;

		$url 					= 'contract/contract_statuses';
		$contract_statuses		= $this->ssid_common->api_call( $url, $postdata, 'GET' );
		$data['contract_statuses']	= ( !empty( $contract_statuses->statuses ) ) ? $contract_statuses->statuses :  false ;

		$url 					= 'contract/contract_types';
		$contract_types			= $this->ssid_common->api_call( $url, $postdata, 'GET' );
		$data['contract_types']	= ( !empty( $contract_types->types ) ) ? $contract_types->types :  false ;

		$this->_render_webpage( 'contract/add_contract', $data );
	}


	/*
	*	To show the Contract Profile data.
	*/
	public function profile( $contract_id = false, $page = "details" ){

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else if( !empty( $contract_id ) ){

			#Get allowed access for the logged in user
			$data['permissions']= $item_access;
			$data['active_tab']	= $page;

			$module_items 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'access/account_modules_items', ['account_id'=>$this->user->account_id, 'module_id'=>$this->module_id ], ['auth_token'=>$this->auth_token], true );
			$data['module_tabs']= ( isset( $module_items->module_items ) ) ? $module_items->module_items : null;

			$data['feedback'] 			= !empty( $this->session->flashdata( 'feedback' ) ) ? ( $this->session->flashdata( 'feedback' ) ) : false ;

			$data['active_class'] 		= 'profile';

			$postdata['account_id']		= $this->user->account_id;
			$postdata['contract_id'] 	= $contract_id;

			$url 						= 'contract/contract_statuses';
			$contract_statuses			= $this->ssid_common->api_call( $url, $postdata, 'GET' );
			$data['contract_statuses']	= ( !empty( $contract_statuses->statuses ) ) ? $contract_statuses->statuses :  false ;

			$url 						= 'user/users';
			$contract_leaders			= $this->ssid_common->api_call( $url, $postdata, 'GET' );
			$data['contract_leaders']	= ( !empty( $contract_leaders->users ) ) ? $contract_leaders->users :  false ;

			$url 						= 'contract/contract_types';
			$contract_types				= $this->ssid_common->api_call( $url, $postdata, 'GET' );
			$data['contract_types']		= ( !empty( $contract_types->types ) ) ? $contract_types->types :  false ;

			## profile data
			$url 						= 'contract/contracts';
			$profile_data				= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
			$data['profile_data']		= ( !empty( $profile_data->contract ) ) ? $profile_data->contract :  false ;

			$url 						= 'contract/wf_task_names';
			$API_result					= $this->ssid_common->api_call( $url, $postdata, 'GET' );
			$data['wf_task_names']		= ( !empty( $API_result->wf_task_names ) ) ? $API_result->wf_task_names :  false ;

			$url 						= 'contract/workflows';
			$API_result					= $this->ssid_common->api_call( $url, $postdata, 'GET' );
			$data['workflows']			= ( !empty( $API_result->workflows ) ) ? $API_result->workflows :  false ;

			$data['contract_details'] 	= $data['profile_data'][0];

			switch( $page ){
				case 'contract_details':
					$data['include_page'] = 'contract_details.php';
					break;
				case 'action_items':
					$data['include_page'] = 'workflow_items.php';
					break;
				case 'linked_sites':
					$url 						= 'contract/linked_sites';
					$postdata['limit']			= 999;
					$API_result					= $this->ssid_common->api_call( $url, $postdata, 'GET' );
					$data['linked_sites']		= ( !empty( $API_result->linked_sites_data ) ) ? $API_result->linked_sites_data :  false ;

					#$data['include_page'] 					= 'linked_sites.php';*/
					$data['include_page'] 					= 'contract_sites.php';
					break;
				case 'assets':
					$API_result				= $this->ssid_common->api_call( 'contract/linked_assets', $postdata, 'GET' );
					$data['linked_assets']	= ( !empty( $API_result->linked_assets ) ) ? $API_result->linked_assets :  false ;
					$data['include_page'] 	= 'linked_assets.php';
					break;
				case 'jobs':

					$contract_jobs 		  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/jobs', ['account_id'=>$this->user->account_id,'where'=>['contract_id'=>$contract_id]], ['auth_token'=>$this->auth_token], true );
					$data['contract_jobs']= ( isset( $contract_jobs->jobs ) ) ? $contract_jobs->jobs : null;

					$job_types		 	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_types', ['account_id'=>$this->user->account_id], ['auth_token'=>$this->auth_token], true );
					$data['job_types'] 	  = ( isset($job_types->job_types) ) ? $job_types->job_types : null;

					$job_statuses		  = $this->webapp_service->api_dispatcher( $this->api_end_point.'job/job_statuses', ['account_id'=>false], ['auth_token'=>$this->auth_token], true );
					$data['job_statuses'] = ( isset($job_statuses->job_statuses) ) ? $job_statuses->job_statuses : null;

					$operatives		  	  = $this->webapp_service->api_dispatcher( $this->api_end_point.'user/field_operatives', ['account_id'=>$this->user->account_id, 'where'=>['include_admins'=>1], 'limit'=>-1], ['auth_token'=>$this->auth_token], true );
					$data['operatives']   = ( isset( $operatives->field_operatives ) ) ? $operatives->field_operatives : null;

					$data['include_page'] = 'contract_jobs.php';
					break;

				case 'stock':

					$stock_and_boms	 				= $this->webapp_service->api_dispatcher( $this->api_end_point.'contract/consumed_items', ['account_id'=>$this->user->account_id, 'contract_id'=>$contract_id, 'grouped'=>1 ], ['auth_token'=>$this->auth_token], true );
					$consumed_items 				= ( isset( $stock_and_boms->consumed_items ) ) ? $stock_and_boms->consumed_items : null;
					$data['contract_consumed_items']= $consumed_items;
					$data['include_page'] = 'contract_stock.php';
					break;

				case 'schedules':
						#$contract_schedules		= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/schedules', ['account_id'=>$this->user->account_id, 'where'=>['contract_id'=>$contract_id] ], ['auth_token'=>$this->auth_token], true );
						#$data['contract_schedules']= ( isset( $contract_schedules->schedules ) ) ? $contract_schedules->schedules : null;

						$data['include_page'] 	= 'contract_schedules.php';
						break;
				case 'people':
					$linked_people				= $this->webapp_service->api_dispatcher( $this->api_end_point.'contract/linked_people', ['account_id'=>$this->user->account_id, 'where'=>['contract_id'=>$contract_id] ], ['auth_token'=>$this->auth_token], true );
					$data['linked_people'] 		= ( isset( $linked_people->people ) ) ? $linked_people->people : [];

					$data['linked_people_ids'] 	= ( !empty( $data['linked_people'] ) ) ? array_column( $data['linked_people'], 'person_id' ) : [];

					$available_people	 	  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'people/people', ['account_id'=>$this->user->account_id, 'limit'=>-1], ['auth_token'=>$this->auth_token], true );
					$data['available_people'] 	= ( isset( $available_people->people ) ) ? $available_people->people : null;

					$available_users	 	  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'user/users', ['account_id'=>$this->user->account_id, 'limit'=>-1], ['auth_token'=>$this->auth_token], true );

					$data['available_users'] 	= ( isset( $available_users->users ) ) ? $available_users->users : null;

					$data['include_page'] 	  	= 'linked_people.php';
					break;

				default:
					$data['include_page'] = 'contract_details.php';
					break;
			}

			$this->_render_webpage( 'contract/profile', $data );
		} else {
			redirect( 'webapp/contract/dashboard', 'refresh' );
		}
	}


	/*
	*	To update the Contract Profile data.
	*/
	public function update(){
		$return_data = [
			'status' => 0
		];

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'details' );
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 			= $this->input->post( 'postdata' );

			if( !empty( $postdata['account_id'] ) && !empty( $postdata['contract_id'] ) ){

				$url 						= 'contract/update';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );
				$return_data['status'] 		= ( !empty( $API_result->status ) && ( $API_result->status == true ) ) ? $API_result->status : 0 ;
			}

			$return_data['status_msg'] 		= ( !empty( $API_result->message ) ) ? $API_result->message : "Action finished" ;
			$return_data['contract'] 		= ( !empty( $API_result->contract ) ) ? $API_result->contract : NULL ;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/*
	*	To delete the Contract Profile
	*/
	public function delete_contract(){
		$return_data = [
			'status' => 0
		];

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'details' );
		if( !$this->user->is_admin && empty( $item_access->can_delete ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {

			$postdata 			= $this->input->post( 'postdata' );

			if( !empty( $postdata['account_id'] ) && !empty( $postdata['contract_id'] ) ){

				$url 						= 'contract/delete';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );
				$return_data['status'] 		= ( !empty( $API_result->status ) && ( $API_result->status == true ) ) ? $API_result->status : 0 ;
			}

			$return_data['status_msg'] 		= ( !empty( $API_result->message ) ) ? $API_result->message : "Action finished" ;
			$return_data['contract'] 		= ( !empty( $API_result->contract ) ) ? $API_result->contract : NULL ;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/*
	*	Function to create a new Contract Worflow (Action)
	*/
	public function add_workflow(){

		$data['active_class'] 		= 'add_contract';
 		$data['feedback'] 			= !empty( $this->session->flashdata( 'feedback' ) ) ? ( $this->session->flashdata( 'feedback' ) ) : false ;
		$postset 					= $this->input->post( 'postdata' );

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'workflow_items' );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {

			if( !empty( $postset ) ){
				$postdata = [];
				$postdata = $postset;
				$postdata["account_id"]		= $postset['account_id'];
				unset( $postset['account_id'] );
				$postdata["contract_id"]	= $postset['contract_id'];
				unset( $postset['contract_id'] );

				$url 						= 'contract/add_workflow';
				$API_result					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( ( !empty( $API_result->new_workflow ) ) ){
					if( ( !empty( $API_result->message ) ) ){
						$this->session->set_flashdata( 'feedback', $API_result->message );
					}
					redirect( 'webapp/contract/profile/'.$postdata['contract_id'].'/action_items', 'refresh' );
				} else {
					if( ( !empty( $API_result->message ) ) ){
						$this->session->set_flashdata( 'feedback', $API_result->message );
					}
					redirect( 'webapp/contract/profile/'.$postdata['contract_id'].'/action_items', 'refresh' );
				}
			}

			redirect( 'webapp/contract/dashboard', 'refresh' );
		}
	}


	/*
	*	To update the batch of Contract Profile data.
	*/
	public function batch_update_workflow(){

 		$post 				= $this->input->post( 'postdata' );

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'workflow_items' );
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			if( !empty( $post['batch_update'] ) ){

				$post_data['batch_update_data'] 	= false;

				foreach( $post['batch_update'] as $key => $row ){
					if( !empty( $row['check'] ) && ( strtolower( $row['check'] )  == 'yes' ) ){
						$post_data['batch_update_data'][$key] = $row;
					}
				}

				if( !empty( $post_data['batch_update_data'] ) ){
					$postdata['account_id']		= $this->user->account_id;
					$postdata['batch_update']	= $post_data['batch_update_data'];
					$url 						= 'contract/batch_workflow_update';
					$updated_workflows			= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

					if( ( !empty( $updated_workflows->status ) ) && ( $updated_workflows->status == 1 ) ){
						$this->session->set_flashdata( 'feedback', ( !empty( $updated_workflows->message ) ) ? ( $updated_workflows->message ) : "The Action Items have been updated successfully." );
					} else {
						$this->session->set_flashdata( 'feedback', ( !empty( $updated_workflows->message ) ) ? ( $updated_workflows->message ) : "The Action Items hasn't been updated." );
					}

				} else {
					$this->session->set_flashdata( 'feedback', ( !empty( $updated_workflows->message ) ) ? ( $updated_workflows->message ) : "You need to pick Action Items to do an update." );
				}

				redirect( 'webapp/contract/profile/'.$post['contract_id'].'/action_items', 'refresh' );
			} else {
				redirect( 'webapp/contract/dashboard/', 'refresh' );
			}
		}
	}


	/*
	*	To delete the Contract Profile
	*/
	public function delete_wf( $wf_id = false ){

 		$account_id 			= $this->user->account_id;
		$contract_id			= $this->uri->segment( 5, 0 );
		$referring_page			= $this->uri->segment( 6, 0 );

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'workflow_items' );
		if( !$this->user->is_admin && empty( $item_access->can_delete ) && empty( $item_access->is_admin ) ){

			$this->_render_webpage( 'errors/access-denied', false );

		}else{

			if( !empty( $wf_id ) && !empty( $account_id ) ){

				$postdata['workflow_id']	= $wf_id;
				$postdata['account_id']		= $account_id;
				$url 						= 'contract/delete_workflow';

				$deleted_wf					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( ( !empty( $deleted_wf->status ) ) && ( $deleted_wf->status == 1 ) ){
					$this->session->set_flashdata( 'feedback', ( !empty( $deleted_wf->message ) ) ? ( $deleted_wf->message ) : "The Workflow Item has been deleted successfully." );
				} else {
					$this->session->set_flashdata( 'feedback', ( !empty( $deleted_wf->message ) ) ? ( $deleted_wf->message ) : "The Workflow Item has NOT been deleted." );
				}
			}

			redirect( 'webapp/contract/profile/'.$contract_id.'/'.$referring_page, 'refresh' );

		}
	}


	/*
	*	To link Sites to the specific Contract
	*/
	public function link_sites(){
		$account_id 			= $this->user->account_id;
		$post_data 				= $this->input->post( 'postdata' );

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section = 'linked_sites' );
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){

			$this->_render_webpage( 'errors/access-denied', false );

		} else {

			if( !empty( $account_id ) && !empty( $post_data['contract_id'] ) && !empty( $post_data['sites'] ) ){
				$postdata['account_id']		= $account_id;
				$postdata['contract_id']	= $post_data['contract_id'];

				$postdata['sites']			= ( is_array( $post_data['sites'] ) ) ? urlencode( trim( implode( ", ", $post_data['sites'] ) ) ) : $post_data['sites'] ;
				$url 						= 'contract/link_sites';
				$linked_sites				= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( ( !empty( $linked_sites->status ) ) && ( $linked_sites->status == 1 ) ){
					$this->session->set_flashdata( 'feedback', ( !empty( $linked_sites->message ) ) ? ( $linked_sites->message ) : "The Site(Sites) has (have) been linked successfuly." );
				} else {
					$this->session->set_flashdata( 'feedback', ( !empty( $linked_sites->message ) ) ? ( $linked_sites->message ) : "The Site(s) has NOT been linked." );
				}
			}

			redirect( 'webapp/contract/profile/'.$post_data['contract_id'].'/linked_sites', 'refresh' );
		}
	}


	/*
	*	Contract lookup / search
	*/
	public function lookup( $page = 'details' ){

		$return_data = '';

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data .= $this->config->item( 'ajax_access_denied' );
		} else {

			$post = $this->input->post();

			# Setup search parameters
			$search_term   		= ( !empty( $post['search_term'] ) ) ? ( $post['search_term'] ) : false;
			$contract_statuses	= ( !empty( $post['contract_statuses'] ) ) ? $post['contract_statuses'] : false;
			$contract_types		= ( !empty( $post['contract_types'] ) ) ? $post['contract_types'] : false;
			$limit		   		= ( !empty( $post['limit'] ) )  ? $post['limit'] : DEFAULT_LIMIT;
			$start_index   		= ( !empty( $post['start_index'] ) ) ? $post['start_index'] : 0;
			$offset		   		= ( !empty( $start_index ) ) ? ( ( $start_index - 1 ) * $limit ) : 0;
			$where		   		= false;
			$order_by			= ( 'c.contract_id DESC' );

			# Prepare postdata
			$postdata = [
				'account_id'		=> $this->user->account_id,
				'search_term'		=> $search_term,
				'contract_statuses'	=> $contract_statuses,
				'contract_types'	=> $contract_types,
				'where'				=> $where,
				'order_by'			=> $order_by,
				'limit'				=> $limit,
				'offset'			=> $offset
			];

			$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'contract/lookup', $postdata, [ 'auth_token'=>$this->auth_token ], true );
			$contracts		= ( isset( $search_result->contracts ) ) ? $search_result->contracts 	: null;
			$counters		= ( isset( $search_result->counters ) )  ? $search_result->counters 	: null;

			if( !empty( $contracts ) ){

				## Create pagination
				## $counters 		= $this->contract_service->get_total_contracts( $this->user->account_id, $search_term, $contract_statuses, $contract_types, $where, $limit, $offset );

				$page_number	= ( $start_index > 0 ) ? $start_index : 1;

				if( !empty( $counters->pages ) && ( $counters->pages > 1 ) ){
					$page_display = '<span class="pull-left no_page_of">Page <strong>'.$page_number.'</strong> of <strong>'.$counters->pages.'</strong></span>';
				} else {
					$page_display = '';
				}

				if( !empty( $counters->total ) && ( $counters->total > 0 ) ){
					$config['total_rows'] 	= $counters->total;
					$config['per_page'] 	= $limit;
					$config['current_page'] = $page_number;
					$pagination_setup 		= _pagination_config();
					$config					= array_merge( $config, $pagination_setup );
					$this->pagination->initialize( $config );
					$pagination 			= $this->pagination->create_links();
				}

				$return_data = $this->load_contract_view( $contracts );

				if( !empty( $pagination ) ){
					$return_data .= '<tr><td colspan="8" style="padding: 0;">';
						$return_data .= $page_display.$pagination;
					$return_data .= '</td></tr>';
				}
			} else {
				$return_data .= '<tr><td colspan="8" class="end">';
				$return_data .= ( isset($search_result->message) ) ? $search_result->message : 'No records found';
				$return_data .= '</td></tr>';
			}
		}

		print_r( $return_data );
		die();
	}


	/*
	* 	Prepare contract views
	*/
	private function load_contract_view( $contract_data ){
		$return_data = '';
		if( !empty( $contract_data ) ){
			foreach( $contract_data as $k => $contract_details ){
				$return_data .= '<tr data-id="'.$contract_details->contract_id.'" >';
					$return_data .= '<td data-label="Contract Name"><a href="'.base_url( '/webapp/contract/profile/'.$contract_details->contract_id.'/dashboard' ).'" >'.$contract_details->contract_name.'</a></td>';

					$return_data .= '<td data-label="Contract Reference">'.$contract_details->contract_ref.'</td>';
					$return_data .= '<td data-label="Contract Type">'.$contract_details->type_name.'</td>';
					$return_data .= '<td data-label="Contract Status">'.$contract_details->status_name.'</td>';
					$return_data .= '<td data-label="Contract Lead Name">'.$contract_details->contract_lead_name.'</td>';
					$return_data .= '<td data-label="Contract Start Date">'.$contract_details->start_date.'</td>';
					$return_data .= '<td data-label="Contract End Date">'.$contract_details->end_date.'</td>';
					$return_data .= '<td data-label="Created On">'.( $contract_details->date_created ).'</td>';
				$return_data .= '</tr>';
			}

			if( !empty( $pagination ) ){
				$return_data .= '<tr><td colspan="8" style="padding: 0;">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
		} else {
			$return_data .= '<tr><td colspan="8"><p style="width: 100%;">'.$this->config->item( "no_records" ).'</p></td></tr>';
		}
		return $return_data;
	}



	/*
	*	Sites lookup / search
	*/
	public function sites_lookup( $page = 'linked_sites' ){

		$return_data = '';

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();
		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );

		if( !$this->user->is_admin && empty( $item_access->can_view ) && empty( $item_access->is_admin ) ){
			$return_data .= $this->config->item( 'ajax_access_denied' );
		} else {

			$post = $this->input->post();

			# Setup search parameters
			$search_term   		= ( !empty( $post['search_term'] ) ) ? urlencode( $post['search_term'] ) : false;
			$contract_id   		= ( !empty( $post['contract_id'] ) ) ? urlencode( $post['contract_id'] ) : false;
			$limit		   		= ( !empty( $post['limit'] ) )  ? $post['limit'] : 20;
			$start_index   		= ( !empty( $post['start_index'] ) ) ? $post['start_index'] : 0;
			$offset		   		= ( !empty( $start_index ) ) ? ( ( $start_index - 1 ) * $limit ) : 0;
			$where		   		= false;
			$order_by			= false;

			# prepare postdata
			$postdata = [
				'account_id'		=> $this->user->account_id,
				'search_term'		=> $search_term,
				'where'				=> $where,
				'order_by'			=> $order_by,
				'limit'				=> $limit,
				'offset'			=> $offset
			];

			$url 					= 'site/lookup';
			$search_result			= $this->ssid_common->api_call( $url, $postdata, $method = 'GET' );
			$sites				= ( isset( $search_result->sites ) ) ? $search_result->sites : null;

			if( !empty( $sites ) ){

				## Create pagination
				$counters 		= $this->site_service->get_total_sites( $this->user->account_id, $search_term, $where );

				$page_number	= ( $start_index > 0 ) ? $start_index : 1;
				$page_display	= '<span style="margin:15px 0px;" class="pull-left">Page <strong>'.$page_number.'</strong> of <strong>'.$counters->pages.'</strong></span>';

				$return_data = $this->load_sites_view( $sites );

			} else {
				$return_data .= '<tr><td colspan="8" class="end">';
				$return_data .= ( isset($search_result->message) ) ? $search_result->message : 'No records found';
				$return_data .= '</td></tr>';
			}
		}

		$dataset['sites'] 		= $return_data;
		$dataset['status_msg'] 	= $search_result->message;
		$dataset['type_msg'] 	= $search_result->status;

		print_r( json_encode( $dataset ) );
		die();
	}


	/*
	*	To unlink the Site from the Contract
	*/
	public function unlink_site( $site_id = false, $contract_id = false, $page = "linked_sites" ){
 		$account_id 			= $this->user->account_id;
		$contract_id			= $this->uri->segment( 5, 0 );
		$referring_page			= $this->uri->segment( 6, 0 );

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_edit ) && empty( $item_access->is_admin ) ){
			$this->_render_webpage( 'errors/access-denied', false );
		} else {
			if( !empty( $site_id ) && !empty( $account_id ) ){
				$postdata['site_id']		= $site_id;
				$postdata['account_id']		= $account_id;
				$postdata['contract_id']	= $contract_id;
				$url 						= 'contract/unlink_site';

				$unlinked_site					= $this->ssid_common->api_call( $url, $postdata, $method = 'POST' );

				if( ( !empty( $unlinked_site->status ) ) && ( $unlinked_site->status == 1 ) ){
					$this->session->set_flashdata( 'feedback', ( !empty( $unlinked_site->message ) ) ? ( $unlinked_site->message ) : "The Site  has been unlinked successfully." );
				} else {
					$this->session->set_flashdata( 'feedback', ( !empty( $unlinked_site->message ) ) ? ( $unlinked_site->message ) : "The Site has NOT been unlinked." );
				}
			}
			redirect( 'webapp/contract/profile/'.$contract_id.'/'.$referring_page, 'refresh' );
		}
	}

	/*
	* 	Prepare Sites view
	*/
	private function load_sites_view( $sites_data ){
		$return_data = '';
		if( !empty( $sites_data ) ){
			foreach( $sites_data as $k => $sites_details ){
				$return_data .= '<tr data-id="'.$sites_details->site_id.'">';
					$return_data .= '<td data-label="Site ID" class="width_80"><a href="'.base_url( '/webapp/site/profile/'.$sites_details->site_id ).'">'.str_pad( $sites_details->site_id, 4, '0', STR_PAD_LEFT ).'</a></td>';
					$return_data .= '<td data-label="Site Name" class="width_240">'.$sites_details->site_name.'</td>';
					$return_data .= '<td data-label="Summary Line" class="width_240">'.$sites_details->summaryline.'</td>';
					$return_data .= '<td data-label="Site Reference" class="width_120">'.$sites_details->site_reference.'</td>';
					$return_data .= '<td data-label="Site Postcodes" class="width_120">'.$sites_details->site_postcodes.'</td>';
					$return_data .= '<td data-label="Date Created" class="width_120">'.$sites_details->date_created.'</td>';
					$return_data .= '<td data-label="Link Site" class="width_80"><input type="checkbox" name="postdata[sites][]" value="'.$sites_details->site_id.'" /></td>';
				$return_data .= '</tr>';
			}

		} else {
			$return_data .= '<tr><td colspan="8"><p style="width: 100%;">'.$this->config->item( "no_records" ).'</p></td></tr>';
		}
		return $return_data;
    }

	/*
	* Contract Schedules lookup / search
	*/
	public function schedules_lookup( $asset_id = false, $page = 'details' ){

		$return_data = '';

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){

			$return_data .= $this->config->item( 'ajax_access_denied' );

		}else{

			# Setup search parameters
			$search_term   	= ( $this->input->post( 'search_term' ) ) 	? $this->input->post( 'search_term' ) : false;
			$where   	   	= ( $this->input->post( 'where' ) ) 		? $this->input->post( 'where' ) : false;
			$limit		   	= ( !empty( $where['limit'] ) )  			? $where['limit']  : DEFAULT_LIMIT;
			$start_index   	= ( $this->input->post( 'start_index' ) ) 	? $this->input->post( 'start_index' ) : DEFAULT_OFFSET;
			$offset		   	= ( !empty( $start_index ) ) 				? ( ( $start_index - 1 ) * $limit ) : 0;
			$order_by	   	= ( $this->input->post( 'order_by' ) ) 		? $this->input->post( 'order_by' ) : false;

			#prepare postdata
			$postdata = [
				'account_id'	=> $this->user->account_id,
				'search_term'	=> $search_term,
				'where'			=> $where,
				'order_by'		=> $order_by,
				'limit'			=> $limit,
				'offset'		=> $offset
			];

			$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'job/schedules', $postdata, ['auth_token'=>$this->auth_token], true );

			$schedules			= ( isset( $search_result->schedules ) ) ? $search_result->schedules : null;

			if( !empty( $schedules ) ){

				## Create pagination
				$counters 		= ( isset( $search_result->counters ) ) ? $search_result->counters : null;
				$page_number	= ( $start_index > 0 ) ? $start_index : 1;
				$page_display	= '<span style="margin:15px 0px;" class="pull-left">Page <strong>'.$page_number.'</strong> of <strong>'.( !empty( $counters->pages ) ? $counters->pages : "" ).'</strong></span>';
				if( !empty( $counters->total ) && ( $counters->total > 0 ) ){
					$config['total_rows'] 	= $counters->total;
					$config['per_page'] 	= $limit;
					$config['current_page'] = $page_number;
					$pagination_setup 		= _pagination_config();
					$config					= array_merge( $config, $pagination_setup );
					$this->pagination->initialize($config);
					$pagination 			= $this->pagination->create_links();
				}

				$return_data = $this->load_schedules_view( $schedules );
				if( !empty($pagination) ){
					$return_data .= '<tr style="border-bottom:1px solid #red" ><td colspan="10" style="padding: 0; border-bottom:#f4f4f4">';
						$return_data .= $page_display.$pagination;
					$return_data .= '</td></tr>';
				}
			}else{
				$return_data .= '<tr><td colspan="10" style="padding: 0;"><br/>';
					$return_data .= $this->config->item("no_records");
				$return_data .= '</td></tr>';
			}
		}

		print_r( $return_data );
		die();
	}

	/*
	* Prepare schedules view
	*/
	private function load_schedules_view( $schedules_data ){
		$return_data = '';
		if( !empty( $schedules_data ) ){
			foreach( $schedules_data as $k => $schedule_details ){
				## For cloning purposes
				$first_activity_due_date = date( 'd-m-Y', strtotime( $schedule_details->first_activity_due_date. ' + 1 year' ) ); 
				$return_data .= '<tr>';
					$return_data .= '<td width="5%" ><a href="'.base_url( '/webapp/job/schedule_profile/'.$schedule_details->schedule_id).'" >'.$schedule_details->schedule_id.'</a></td>';
					$return_data .= '<td width="20%"><a href="'.base_url( '/webapp/job/schedule_profile/'.$schedule_details->schedule_id).'" >'.$schedule_details->schedule_name.'</a></td>';
					$return_data .= '<td width="10%">'.( date( 'd-m-Y H:i:s', strtotime( $schedule_details->date_created ) ) ).'</td>';
					$return_data .= '<td width="10%">'.( !empty( $schedule_details->scheduled_sites ) ? ( !is_array( $schedule_details->scheduled_sites ) ? count( object_to_array( $schedule_details->scheduled_sites ) ) : count( $schedule_details->scheduled_sites ) ) : '' ).'</td>';
					$return_data .= '<td width="10%">'.$schedule_details->frequency_name.'</td>';
					$return_data .= '<td width="10%">'.$schedule_details->activities_total.'</td>';
					$return_data .= '<td width="10%">'.( ( !empty( $schedule_details->expiry_date ) && valid_date( $schedule_details->expiry_date ) ) ? date( 'd-m-Y H:i:s', strtotime( $schedule_details->expiry_date ) ) : '' ).'</td>';
					$return_data .= '<td width="10%">'.$schedule_details->schedule_status.'</td>';
					$return_data .= '<td width="5%" class="text-center"><span>'.( !empty( $schedule_details->is_cloned ) && ( $schedule_details->is_cloned == 1 ) ? 'Y' : '' ).'</span></td>';
					$return_data .= '<td width="10%">';
						$return_data .= '<class class="row pull-right">';
							$return_data .= '<div class="col-md-4" ><a class="clone-schedule-btn" data-schedule_id="'.$schedule_details->schedule_id.'" data-first_activity_due_date="'.$first_activity_due_date.'" ><i title="Click here to clone this schedule record" class="fas fa-copy text-blue pointer"></i></a></div>';
							$return_data .= '<div class="col-md-4" ><a href="'.base_url( '/webapp/job/schedule_profile/'.$schedule_details->schedule_id ).'" ><i title="Click here to view this schedule record" class="fas fa-edit text-blue pointer"></i></a></div>';
							$return_data .= '<div class="col-md-4 delete-item" ><i title="Click here to delete this Schedule" class="delete-item fas fa-trash-alt text-red pointer"></i></div>';
						$return_data .= '</span>';
					$return_data .= '</td>';
				$return_data .= '</tr>';
			}

			if( !empty( $pagination ) ){
				$return_data .= '<tr><td colspan="10" style="padding: 0;">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
		}else{
			$return_data .= '<tr><td colspan="9"><br/>'.$this->config->item("no_records").'</td></tr>';
		}
		return $return_data;
	}


	/**
	* Link a person to a Contract
	**/
	public function link_people(){

		$section 	 = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
		$return_data = [
			'status'=>0
		];

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access for the logged in user
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && empty( $item_access->can_add ) && empty( $item_access->is_admin ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );
		} else {
			$postdata 	  	 = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$linked_people 	 = $this->webapp_service->api_dispatcher( $this->api_end_point.'contract/link_people' , $postdata, ['auth_token'=>$this->auth_token] );

			$result		  	 = ( isset( $linked_people->people ) ) 	? $linked_people->people : null;
			$message	  	 = ( isset( $linked_people->message ) )  		? $linked_people->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] 	 = 1;
				$text_color 			 = 'auto';
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/**
	* Un-link People
	**/
	public function unlink_people( $person_id = false ){
		$return_data = [
			'status'=>0
		];

		$section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && !$item_access && empty( $item_access->can_delete ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		}else{
			$postdata 		= array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
			$unlink_person	= $this->webapp_service->api_dispatcher( $this->api_end_point.'contract/unlink_people', $postdata, ['auth_token'=>$this->auth_token] );
			$result			= ( isset( $unlink_person->status ) )  ? $unlink_person->status  : null;
			$message		= ( isset( $unlink_person->message ) ) ? $unlink_person->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$return_data['status'] = 1;
			}
			$return_data['status_msg'] = $message;
		}

		print_r( json_encode( $return_data ) );
		die();
	}


	/**
	*	Download Stock / BOM data
	**/
	public function download_consumed_items( $contract_id = false, $page = "stock" ){
		$return_data = [
			'status'=>0
		];

		$post_data 		= $this->input->post();
		$section 		= ( !empty( $post_data['page'] ) ) ? $post_data['page'] : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$item_access = $this->webapp_service->check_access( $this->user, $this->module_id, $section );
		if( !$this->user->is_admin && !$item_access && empty( $item_access->can_view ) ){
			$return_data['status_msg'] = $this->config->item( 'ajax_access_denied' );;
		} else {

			if( !empty( $post_data ) ){

				$contract_id = ( !empty( $post_data['contract_id'] ) ) ? $post_data['contract_id'] : ( ( !empty( $contract_id ) ) ? $contract_id : false );

				$consumed_items = false;
				$postdata = [
					"account_id"	=> $this->user->account_id,
					"where" => [
						"date_from" 	=> ( !empty( $post_data['date_from'] ) ) ? $post_data['date_from'] : false,
						"date_to" 		=> ( !empty( $post_data['date_to'] ) ) ? $post_data['date_to'] : false,
					],
					"item_type"		=> ( !empty( $post_data['item_type'] ) ) ? $post_data['item_type'] : false ,
					"contract_id"	=> $contract_id,
					"grouped"		=>	1
				];


				$api_call	 				= $this->webapp_service->api_dispatcher( $this->api_end_point.'contract/consumed_items_export', $postdata, ['auth_token'=>$this->auth_token], true );

				$contract_consumed_items 	= ( isset( $api_call->consumed_items ) ) ? $api_call->consumed_items : null;
				$status_msg					= ( isset( $consumed_items->message ) ) ? $consumed_items->message : 'No data matching your criteria.';

				if( !empty( $contract_consumed_items->file_link ) ){
					$return_data['status'] = 1;
					$this->session->set_flashdata( 'feedback' , $status_msg );
					force_download( $contract_consumed_items->file_name, file_get_contents( $contract_consumed_items->file_path ) );
				} else {
					$this->session->set_flashdata( 'feedback' , $status_msg );
					redirect( "webapp/contract/profile/".$contract_id."/stock", 'refresh' );
				}
			} else {
				redirect( "webapp/contract/profile/".$contract_id."/stock", 'refresh' );
			}
		}
	}


	/*
	* Contract Buildings lookup / search
	*/
	public function contract_buildings_lookup( $contract_id = false, $page = 'details' ){

		$return_data = '';

		$section = ( !empty( $page ) ) ? $page : $this->router->fetch_method();

		# Check module access
		$module_access = $this->webapp_service->check_access( $this->user, $this->module_id );

		if( !$this->user->is_admin && !$module_access ){

			$return_data .= $this->config->item( 'ajax_access_denied' );

		}else{

			# Setup search parameters
			$contract_id   	= ( $this->input->post( 'contract_id' ) ) 	? $this->input->post( 'contract_id' ) : false;
			$search_term   	= ( $this->input->post( 'search_term' ) ) 	? $this->input->post( 'search_term' ) : false;
			$where   	   	= ( $this->input->post( 'where' ) ) 		? $this->input->post( 'where' ) : false;
			$limit		   	= ( !empty( $where['limit'] ) )  			? $where['limit']  : DEFAULT_LIMIT;
			$start_index   	= ( $this->input->post( 'start_index' ) ) 	? $this->input->post( 'start_index' ) : DEFAULT_OFFSET;
			$offset		   	= ( !empty( $start_index ) ) 				? ( ( $start_index - 1 ) * $limit ) : 0;
			$order_by	   	= ( $this->input->post( 'order_by' ) ) 		? $this->input->post( 'order_by' ) : false;

			#prepare postdata
			$postdata = [
				'account_id'	=> $this->user->account_id,
				'contract_id'	=> !empty( $where['contract_id'] ) ? $where['contract_id'] : $contract_id,
				'search_term'	=> $search_term,
				'where'			=> $where,
				'order_by'		=> $order_by,
				'limit'			=> $limit,
				'offset'		=> $offset
			];

			$search_result	= $this->webapp_service->api_dispatcher( $this->api_end_point.'contract/contract_buildings', $postdata, ['auth_token'=>$this->auth_token], true );

			$buildings			= ( isset( $search_result->buildings ) ) ? $search_result->buildings : null;

			if( !empty( $buildings ) ){

				## Create pagination
				$counters 		= ( isset( $search_result->counters ) ) ? $search_result->counters : null;
				$page_number	= ( $start_index > 0 ) ? $start_index : 1;
				$page_display	= '<span style="margin:15px 0px;" class="pull-left">Page <strong>'.$page_number.'</strong> of <strong>'.( !empty( $counters->pages ) ? $counters->pages : "" ).'</strong></span>';
				if( !empty( $counters->total ) && ( $counters->total > 0 ) ){
					$config['total_rows'] 	= $counters->total;
					$config['per_page'] 	= $limit;
					$config['current_page'] = $page_number;
					$pagination_setup 		= _pagination_config();
					$config					= array_merge( $config, $pagination_setup );
					$this->pagination->initialize($config);
					$pagination 			= $this->pagination->create_links();
				}

				$return_data = $this->load_contract_buildings_view( $buildings );
				if( !empty($pagination) ){
					$return_data .= '<tr style="border-bottom:1px solid #red" ><td colspan="7" style="padding: 0; border-bottom:#f4f4f4">';
						$return_data .= $page_display.$pagination;
					$return_data .= '</td></tr>';
				}
			}else{
				$return_data .= '<tr><td colspan="7" style="padding: 0;"><br/>';
					$return_data .= $this->config->item("no_records");
				$return_data .= '</td></tr>';
			}
		}

		print_r( $return_data );
		die();
	}

	/*
	* Prepare Contract Buildings view
	*/
	private function load_contract_buildings_view( $schedules_data ){
		$return_data = '';
		if( !empty( $schedules_data ) ){
			foreach( $schedules_data as $k => $building_details ){
				$return_data .= '<tr>';
					$return_data .= '<td><a href="'.base_url( '/webapp/site/profile/'.$building_details->site_id).'" >'.$building_details->site_id.'</a></td>';
					$return_data .= '<td>'.$building_details->site_name.'</td>';
					$return_data .= '<td>'.$building_details->site_reference.'</td>';
					$return_data .= '<td>'.( ucwords( strtolower( $building_details->address_line_1 ) ) ).( !empty( $building_details->address_line_2 ) ? ', '.ucwords( strtolower( $building_details->address_line_2 ) ) : '' ).', '.( strtoupper( $building_details->postcode )  ).'</td>';
					$return_data .= '<td>'.$building_details->site_postcodes.'</td>';
					$return_data .= '<td>'.$building_details->status_name.'</td>';
					$return_data .= '<td>';
						$return_data .= '<class class="row pull-right">';
							$return_data .= '<div class="col-md-12 delete-item text-red pointer" data-site_id="'.$building_details->site_id.'" data-contract_id="'.$building_details->contract_id.'"><i title="Click here to unlink this Building" class="delete-item fas fa-trash-alt text-red pointer"></i> Unlink Building</div>';
						$return_data .= '</span>';
					$return_data .= '</td>';
				$return_data .= '</tr>';
			}

			if( !empty( $pagination ) ){
				$return_data .= '<tr><td colspan="7" style="padding: 0;">';
					$return_data .= $page_display.$pagination;
				$return_data .= '</td></tr>';
			}
		}else{
			$return_data .= '<tr><td colspan="7"><br/>'.$this->config->item("no_records").'</td></tr>';
		}
		return $return_data;
	}

}