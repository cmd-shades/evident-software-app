<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends MX_Controller {

	function __construct(){
		parent::__construct();
		if( !$this->identity() ){
			redirect('webapp/user/login', 'refresh');
		}

		$this->options = ['auth_token'=>$this->auth_token];		
	}
	
	function index(){
		
		$postdata 				=  [ 'user_id' =>  $this->user->id, 'account_id' => $this->user->account_id, 'admin_account_id'=>$this->user->account_id ];
		$all_modules_list 		= $this->webapp_service->api_dispatcher( $this->api_end_point.'account/system_modules', $postdata, [ 'auth_token'=>$this->auth_token ], true );
		$data['system_modules']	= ( isset( $all_modules_list->modules ) ) ? $all_modules_list->modules : null;
	
		$this->_render_webpage('settings/index', $data, false, true );
	}
	/** View Module config tables **/
	function module( $module_id = false ){
		
		$status_groups = status_groups();
		
		$module_id 	 = ( !empty( $this->input->get( 'module_id' ) ) ) ? $this->input->get( 'module_id' ) : $module_id;		
		
		if( !empty( $module_id ) ){
			# Check module access
			$module_access = $this->webapp_service->check_access( $this->user, $module_id );

			if( !$this->user->is_admin && !$module_access ){
				$this->_render_webpage( 'errors/access-denied', false );
			}else{
				$module_details  		= $this->webapp_service->api_dispatcher( $this->api_end_point.'account/modules', [ 'module_id'=>$module_id ], ['auth_token'=>$this->auth_token], true );
				$data['module_details']	= ( !empty( $module_details->modules) ) ? $module_details->modules : null;
				$configurable_tables  	= $this->webapp_service->api_dispatcher( $this->api_end_point.'account/configurable_tables', ['account_id'=>$this->user->account_id, 'module_id'=>$module_id ], ['auth_token'=>$this->auth_token], true );
				$data['config_tables']	= ( !empty( $configurable_tables->configurable_tables) ) ? $configurable_tables->configurable_tables : null;
				$data['module_id'] 		= $module_id;			
				$this->_render_webpage('settings/modules/index', $data, false, true );
			}
		} else {
			redirect('webapp/settings/index', 'refresh');
		}
	}
	
	/** Get Table Data **/
	function get_list_data( $module_id = false, $table_name = false, $list_name = false ){
		
		$module_id 	 = ( $this->input->post( 'module_id' ) ) ? $this->input->post( 'module_id' ) : ( !empty( $module_id ) ? $module_id : null );
		$table_name	 = ( $this->input->post( 'table_name' ) ) ? $this->input->post( 'table_name' ) : ( !empty( $table_name ) ? $table_name : null );
		$order_column= ( $this->input->post( 'order_column' ) ) ? $this->input->post( 'order_column' ) : ( !empty( $order_column ) ? $order_column : null );
		$list_name	 = ( $this->input->post( 'list_name' ) ) ? $this->input->post( 'list_name' ) : ( !empty( $list_name ) ? $list_name : null );

		$return_data = [
			'status'=>0,
			'audit_record'=>null,
			'status_msg'=>'Invalid parameters'
		];

		if( !empty( $module_id ) && !empty( $table_name ) ){
			$config_table_data	= $this->webapp_service->api_dispatcher( $this->api_end_point.'account/config_table_data', ['account_id'=>$this->user->account_id,'table_name'=>$table_name, 'filters'=>['order_by'=>$order_column ] ], ['auth_token'=>$this->auth_token], true );
			$result				= ( isset( $config_table_data->config_table_data ) ) ? $config_table_data->config_table_data : null;
			$message			= ( isset( $config_table_data->message ) ) ? $config_table_data->message : 'Oops! There was an error processing your request.';
			if( !empty( $result ) ){
				$table_data 				= $this->load_table_data_view( $module_id, $table_name, $list_name, $order_column, $result );
				$return_data['status'] 	    = 1;
				$return_data['table_data']  = $table_data;
				$return_data['add_new_link']= '<a href="#" class="add-new-option" data-table_name="'.$table_name.'" data-list_name="'.$list_name.'" title="Add a new option to '.$list_name.'"><i class="fas fa-plus text-grey"></i></a>';
			}
			$return_data['status_msg'] 		= $message;
		}
		print_r( json_encode( $return_data ) );
		die();
		
	}
	
	/* Load Specific Table Data view */
	function load_table_data_view( $module_id = false, $table_name = false, $list_name = false, $order_column = false, $result = false ){
		
		/** One of those pieces of code I've written and midway thought! Sh*t, this is going to be long... but am too deep into it to change it! #developerwoes **/
		
		$return_data = '';
		if( !empty( $module_id ) && !empty( $table_name ) && !empty( $result ) ){
			
			$add_option_url = base_url( 'settings/add_option/'.$table_name );
			$modal_header 	= !empty( $list_name ) ? 'Add a New '.$list_name.' Option' : 'Add New Option';
			$modal_body		= '';
			
			$return_data .= '<table class="table table-responsive table-full-width" style="width:100%">';
			switch( strtolower( $table_name ) ){
				
				case 'audit_categories':
					
					$return_data .= '<thead><tr>';
						$return_data .= '<th>Option Name</th>';
						$return_data .= '<th>Status</th>';
						$return_data .= '<th title="Date item was last modified">Timestamp</th>';
						$return_data .= '<th><span class="pull-right">Action</span></th>';
					$return_data .= '</tr></thead>';
					foreach( $result as $k => $row ){
						$return_data .= '<tr>';
							$return_data .= '<td>'.ucwords( $row->category_name ).'</td>';
							$return_data .= '<td>'.( ( $row->is_active == 1 ) ? 'Active' : 'Disabled' ).'</td>';
							$return_data .= '<td>'.( !empty( $row->last_modified ) ? $row->last_modified : $row->date_created ).'</td>';
							$return_data .= '<td data-record_id="'.$row->category_id.'" data-table_name="'.$table_name.'" data-list_name="'.$list_name.'" data-module_id="'.$module_id.'" data-order_column="'.$order_column.'" >';
								$return_data .= '<span class="pull-right"><a href="#" class="edit-record pointer" title="Click to Edit this record"><i class="far fa-edit"></i> </a> &nbsp; &nbsp; <a class="delete-record pointer text-red" title="Click to Delete this record" ><i class="far fa-trash-alt"></i> </a></span></td>';
							$return_data .= '</td>';
							
						$return_data .= '</tr>';
					}
					
					##	Prepare modal body
					$modal_body .= '<div>';
						$modal_body .= '<div class="input-group form-group">';
							$modal_body .= '<label class="input-group-addon">Category Name</label><input name="category_name" class="form-control required" type="text" placeholder="Category Name" value="" />';
						$modal_body .= '</div>';
						
						$modal_body .= '<div class="input-group form-group">';
							$modal_body .= '<label class="input-group-addon">Category Description</label>';
							$modal_body .= '<textarea name="description" class="form-control required" type="text" value="" rows="4" ></textarea>';
						$modal_body .= '</div>';
					$modal_body .= '</div>';
					
					break;
				
				case 'asset_types':
					$audit_categories	= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/audit_categories', [ 'account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
					$categories			= ( isset( $audit_categories->audit_categories ) ) ? $audit_categories->audit_categories : null;

					$asset_sub_cats = asset_sub_categories();
					$return_data .= '<thead><tr>';
						$return_data .= '<th>Option Name</th>';
						$return_data .= '<th>Status</th>';
						$return_data .= '<th title="Date item was last modified">Timestamp</th>';
						$return_data .= '<th><span class="pull-right">Action</span></th>';
					$return_data .= '</tr></thead>';
					foreach( $result as $k => $row ){
						$return_data .= '<tr>';
							$return_data .= '<td>'.ucwords( $row->asset_type ).'</td>';
							$return_data .= '<td>'.( ( !empty( $row->is_active ) && ( $row->is_active == 1 ) ) ? 'Active' : 'Disabled' ).'</td>';
							$return_data .= '<td>'.( !empty( $row->last_modified ) ? $row->last_modified : ( ( !empty( $row->date_created ) ) ? $row->date_created : '' ) ).'</td>';
							$return_data .= '<td data-record_id="'.$row->asset_type_id.'" data-table_name="'.$table_name.'" data-list_name="'.$list_name.'" data-module_id="'.$module_id.'" data-order_column="'.$order_column.'" >';
								$return_data .= '<span class="pull-right"><a href="#" class="edit-record pointer" title="Click to Edit this record"><i class="far fa-edit"></i> </a> &nbsp; &nbsp; <a class="delete-record pointer text-red" title="Click to Delete this record" ><i class="far fa-trash-alt"></i> </a></span></td>';
							$return_data .= '</td>';
						$return_data .= '</tr>';
					}

					##	Prepare modal body
					$modal_body .= '<div>';
						$modal_body .= '<div class="input-group form-group">';
							$modal_body .= '<label class="input-group-addon">Asset type</label><input name="asset_type" class="form-control required" type="text" placeholder="Asset type" value="" />';
						$modal_body .= '</div>';
						
						$modal_body .= '<div class="input-group form-group"><label class="input-group-addon">Asset group</label>';
							$modal_body .= '<select id="asset_group" name="asset_group" class="form-control required" data-label_text=""><option value="">Please select</option>';
								foreach( $asset_sub_cats as $asset_group => $asset_group_text ){
									$modal_body .= '<option value="'.$asset_group.'">'.$asset_group_text.'</option>';
								}
							$modal_body .= '</select>';
						$modal_body .= '</div>';
						
						$modal_body .= '<div class="input-group form-group"><label class="input-group-addon">Asset Category</label>';
							$modal_body .= '<select id="category_id" name="category_id" class="form-control required"><option value="">Please select</option>';
								if( !empty( $categories ) ){
									foreach( $categories as $k => $category ){
										$modal_body .= '<option value="'.$category->category_id.'">'.$category->category_name_alt.'</option>';
									}
								}
							$modal_body .= '</select>';
						$modal_body .= '</div>';
						
						$modal_body .= '<div class="input-group form-group">';
							$modal_body .= '<label class="input-group-addon">Description</label>';
							$modal_body .= '<textarea name="description" class="form-control required" type="text" value="" rows="4" ></textarea>';
						$modal_body .= '</div>';
					$modal_body .= '</div>';
					break;
					
				case 'asset_statuses':
				
					$status_groups = status_groups();
					
					$return_data .= '<thead><tr>';
						$return_data .= '<th>Option Name</th>';
						$return_data .= '<th>Status</th>';
						$return_data .= '<th title="Date item was last modified">Timestamp</th>';
						$return_data .= '<th><span class="pull-right">Action</span></th>';
					$return_data .= '</tr></thead>';
					foreach( $result as $k => $row ){
						$return_data .= '<tr>';
							$return_data .= '<td>'.ucwords( $row->status_name ).'</td>';
							$return_data .= '<td>'.( ( $row->is_active == 1 ) ? 'Active' : 'Disabled' ).'</td>';
							$return_data .= '<td>'.( !empty( $row->last_modified ) ? $row->last_modified : $row->date_created ).'</td>';
							$return_data .= '<td data-record_id="'.$row->status_id.'" data-table_name="'.$table_name.'" data-list_name="'.$list_name.'" data-module_id="'.$module_id.'" data-order_column="'.$order_column.'" >';
								$return_data .= '<span class="pull-right"><a href="#" class="edit-record pointer" title="Click to Edit this record"><i class="far fa-edit"></i> </a> &nbsp; &nbsp; <a class="delete-record pointer text-red" title="Click to Delete this record" ><i class="far fa-trash-alt"></i> </a></span></td>';
							$return_data .= '</td>';
						$return_data .= '</tr>';
					}
					
					##	Prepare modal body
					$modal_body .= '<div>';
						$modal_body .= '<div class="input-group form-group">';
							$modal_body .= '<label class="input-group-addon">Status Name</label><input name="status_name" class="form-control required" type="text" placeholder="Asset Status Name" value="" />';
						$modal_body .= '</div>';
						
						$modal_body .= '<div class="input-group form-group"><label class="input-group-addon">Treat this as?</label>';
							$modal_body .= '<select id="status_group" name="status_group" class="form-control required" data-label_text=""><option value="">Please select</option>';
								foreach( $status_groups as $group_name => $group_details ){
									$modal_body .= '<option value="'.$group_details->group_name.'" data-group_colour="'.$group_details->group_colour.'">'.$group_details->group_desc.'</option>';
								}
							$modal_body .= '</select>';
						$modal_body .= '</div>';
						
						$modal_body .= '<div class="input-group form-group">';
							$modal_body .= '<label class="input-group-addon">Status Description</label>';
							$modal_body .= '<textarea name="status_desc" class="form-control required" type="text" value="" rows="4" ></textarea>';
						$modal_body .= '</div>';
					$modal_body .= '</div>';
					
					break;
					
				case 'asset_eol_statuses':
					$return_data .= '<thead><tr>';
						$return_data .= '<th>Option Name</th>';
						$return_data .= '<th>Status</th>';
						$return_data .= '<th title="Date item was created / last modified  created">Timestamp</th>';
						$return_data .= '<th><span class="pull-right">Action</span></th>';
					$return_data .= '</tr></thead>';
					foreach( $result as $k => $row ){
						$return_data .= '<tr>';
							$return_data .= '<td>'.ucwords( $row->eol_group_text ).'</td>';
							$return_data .= '<td>'.( ( $row->is_active == 1 ) ? 'Active' : 'Disabled' ).'</td>';
							$return_data .= '<td>'.( !empty( $row->last_modified ) ? $row->last_modified : $row->date_created ).'</td>';
							$return_data .= '<td data-record_id="'.$row->eol_group_id.'" data-table_name="'.$table_name.'" data-list_name="'.$list_name.'" data-module_id="'.$module_id.'" data-order_column="'.$order_column.'" >';
								$return_data .= '<span class="pull-right"><a href="#" class="edit-record pointer" data-record_id="'.$row->eol_group_id.'" data-table_name="'.$table_name.'"  title="Click to Edit this record"><i class="far fa-edit"></i> </a> &nbsp; &nbsp; <a class="delete-record pointer text-red" title="Click to Delete this record" ><i class="far fa-trash-alt"></i> </a></span></td>';
							$return_data .= '</td>';
						$return_data .= '</tr>';
					}
					break;
					
				case 'user_statuses':
					$return_data .= '<thead><tr>';
						$return_data .= '<th>Option Name</th>';
						$return_data .= '<th>Status</th>';
						$return_data .= '<th title="Date item was last modified">Timestamp</th>';
						$return_data .= '<th><span class="pull-right">Action</span></th>';
					$return_data .= '</tr></thead>';
					foreach( $result as $k => $row ){
						$return_data .= '<tr>';
							$return_data .= '<td>'.ucwords( $row->status ).'</td>';
							$return_data .= '<td>'.( ( $row->is_active == 1 ) ? 'Active' : 'Disabled' ).'</td>';
							$return_data .= '<td>'.( !empty( $row->last_modified ) ? $row->last_modified : $row->date_created ).'</td>';
							$return_data .= '<td data-record_id="'.$row->status_id.'" data-table_name="'.$table_name.'" data-list_name="'.$list_name.'" data-module_id="'.$module_id.'" data-order_column="'.$order_column.'" >';
								$return_data .= '<span class="pull-right"><a href="#" class="edit-record pointer" data-record_id="'.$row->eol_group_id.'" data-table_name="'.$table_name.'"  title="Click to Edit this record"><i class="far fa-edit"></i> </a> &nbsp; &nbsp; <a class="delete-record pointer text-red" title="Click to Delete this record" ><i class="far fa-trash-alt"></i> </a></span></td>';
							$return_data .= '</td>';
						$return_data .= '</tr>';
					}
					break;
			}
			$return_data .= '</table>';
			
			//Prepare Modal
			$modal_template = '<div class="modal fade add-option-'.$table_name.'" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog modal-md">';
				$modal_template .= '<form class="form-'.$table_name.'" id="add-option-form-'.$table_name.'" action="'.$add_option_url.'"><div class="modal-content">';
					$modal_template .= '<input type="hidden" name="table_name" value="'.$table_name.'" />';
					$modal_template .= '<input type="hidden" name="module_id" value="'.$module_id.'" />';
					$modal_template .= '<input type="hidden" name="page" value="details" />';
					##Header
					$modal_template .= '<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>';
						$modal_template .= '<h4 class="modal-title" >'.$modal_header.'</h4>';
						$modal_template .= '<span id="feedback-message"></span>';
					$modal_template .= '</div>';
					
					##Body
					$modal_template .= '<div class="modal-body">';
						$modal_template .= '<div class="row"><div class="col-md-12 col-sm-12 col-xs-12">';
							$modal_template .= $modal_body;
						$modal_template .= '</div></div>';
					$modal_template .= '</div>';
					
					##Footer
					$modal_template .= '<div class="modal-footer"><div class="row"><div class="col-md-12 col-sm-12 col-xs-12">';
						$modal_template .= '<button class="add-option-btn btn btn-success btn-block" data-table_name="'.$table_name.'" data-list_name="'.$list_name.'" data-module_id="'.$module_id.'" data-order_column="'.$order_column.'" type="button" >Add Option</button>';
					$modal_template .= '</div></div></div>';
				$modal_template .= '</div></form>';
			$modal_template .= '</div></div>';
		
			$return_data .= $modal_template;
		}
		return $return_data;
	}
	
	/** Add a new Option item **/
	function add_new_option(){
		
		$text_color  = 'red';
		$return_data = [
			'status'=>0
		];

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
		$module_id	  = !empty( $postdata['module_id'] ) ? $postdata['module_id'] : false;
		$table_name	  = !empty( $postdata['table_name'] ) ? $postdata['table_name'] : false;
		if( !empty( $table_name ) && !empty( $module_id ) ){
			
			$result = $this->dispatch_add_option_request( $module_id, $table_name, $postdata );
			if( !empty( $result ) ){
				$return_data['status']= 1;
				$return_data['result']= $result->result;
				$text_color 		  = 'green';
				$message	  		  = '<span class="text-'.$text_color.'">'.$result->message.'</span>';
			} else {
				$message	  = '<span class="text-red">Your add option request failed.</span>';
			}

		} else {
			$message	  = '<span class="text-red">Your request is missing required information.</span>';
		}
		
		$return_data['status_msg'] = $message;

		print_r( json_encode( $return_data ) );
		die();
	}
	
	/** Edit a new Option item **/
	function edit_option(){
		
		$text_color  = 'red';
		$return_data = [
			'status'=>0
		];

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
		$module_id	  = !empty( $postdata['module_id'] ) ? $postdata['module_id'] : false;
		$table_name	  = !empty( $postdata['table_name'] ) ? $postdata['table_name'] : false;
		if( !empty( $table_name ) && !empty( $module_id ) ){
			$result = $this->dispatch_edit_option_request( $module_id, $table_name, $postdata );
			if( !empty( $result ) ){
				$return_data['status']= 1;
				$return_data['result']= $result->result;
				$text_color 		  = 'green';
				$message	  		  = '<span class="text-'.$text_color.'">'.$result->message.'</span>';
			} else {
				$message	  = '<span class="text-red">Your add option request failed.</span>';
			}

		} else {
			$message	  = '<span class="text-red">Your request is missing required information.</span>';
		}
		
		$return_data['status_msg'] = $message;

		print_r( json_encode( $return_data ) );
		die();
	}
	
	/** Delete an Option item **/
	function delete_option(){
		
		$text_color  = 'red';
		$return_data = [
			'status'=>0
		];

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		# Check module-item access
		$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
		$module_id	  = !empty( $postdata['module_id'] ) ? $postdata['module_id'] : false;
		$table_name	  = !empty( $postdata['table_name'] ) ? $postdata['table_name'] : false;
		
		if( !empty( $table_name ) && !empty( $module_id ) ){
			
			$result = $this->dispatch_delete_option_request( $module_id, $table_name, $postdata );
			if( !empty( $result ) ){
				$return_data['status']= 1;
				$text_color 		  = 'green';
				$message	  		  = '<span class="text-'.$text_color.'">'.$result->message.'</span>';
			} else {
				$message	  = '<span class="text-red">Your delete option request failed.</span>';
			}

		} else {
			$message	  = '<span class="text-red">Your request is missing required information.</span>';
		}
		
		$return_data['status_msg'] = $message;

		print_r( json_encode( $return_data ) );
		die();
	}
	
	/** Dispatch an Add option request **/
	function dispatch_add_option_request( $module_id = false, $table_name = false, $postdata = false ){
		$result = false;
		if( !empty( $module_id ) && !empty( $table_name ) && !empty( $postdata ) ){			
			$data 			= (object) ['result'=>null, 'message'=>'Something went wrong, please try again!' ];
			$url_endpoint 	= $this->api_end_point.'/settings/add_option';			
			$add_result		= $this->webapp_service->api_dispatcher( $url_endpoint, $postdata, ['auth_token'=>$this->auth_token] );
			$result			= ( !empty( $add_result->add_option ) ) ? true : false;
			
			if( !empty( $result ) ){
				$data->result  = $result;
				$data->message = ( isset( $add_result->message ) ) ? $add_result->message : 'Something went wrong, please try again!';
			}
			$result = $data;
		}
		return $result;
	}
	
	/** Dispatch a delete option request **/
	function dispatch_delete_option_request( $module_id = false, $table_name = false, $postdata = false ){
		$result = false;
		if( !empty( $module_id ) && !empty( $table_name ) && !empty( $postdata ) ){
			$data 			= (object) ['result'=>null, 'message'=>'Something went wrong, please try again!' ];
			$url_endpoint 	= $this->api_end_point.'/settings/delete_option';			
			$delete_result	= $this->webapp_service->api_dispatcher( $url_endpoint, $postdata, ['auth_token'=>$this->auth_token] );
			$result			= ( $delete_result->status ) ? true : false;
			
			if( !empty( $result ) ){
				$data->result  = $result;
				$data->message = ( isset( $delete_result->message ) ) ? $delete_result->message : 'Something went wrong, please try again!';
			}
			$result = $data;
		}
		return $result;
	}
	
	/** Dispatch an Edit option request **/
	function dispatch_edit_option_request( $module_id = false, $table_name = false, $postdata = false ){
		$result = false;
		if( !empty( $module_id ) && !empty( $table_name ) && !empty( $postdata ) ){
			$data 			= (object) ['result'=>null, 'message'=>'Something went wrong, please try again!' ];
			$url_endpoint 	= $this->api_end_point.'/settings/edit_option';		
			$edit_option	= $this->webapp_service->api_dispatcher( $url_endpoint, $postdata, ['auth_token'=>$this->auth_token] );
			$result			= ( $edit_option->status ) ? true : false;
			
			if( !empty( $result ) ){
				$data->result  = $result;
				$data->message = ( isset( $edit_option->message ) ) ? $edit_option->message : 'Something went wrong, please try again!';
			}
			$result = $data;
		}
		return $result;
	}
	
	/** Retrieve an Option item **/
	function get_option(){
		
		$text_color  = 'red';
		$return_data = [
			'status'=>0
		];

		if( !$this->identity() ){
			$return_data['message'] = 'Access denied! Please login';
		}

		$postdata 	  = array_merge( ['account_id'=>$this->user->account_id], $this->input->post() );
		$module_id	  = !empty( $postdata['module_id'] ) ? $postdata['module_id'] : false;
		$table_name	  = !empty( $postdata['table_name'] ) ? $postdata['table_name'] : false;
		$list_name	  = !empty( $postdata['list_name'] ) ? $postdata['list_name'] : false;
		$order_column = !empty( $postdata['order_column'] ) ? $postdata['order_column'] : false;
		
		if( !empty( $table_name ) && !empty( $module_id ) ){
			$result = $this->dispatch_get_option_request( $module_id, $table_name, $list_name, $order_column, $postdata );
			if( !empty( $result->result ) ){
				$return_data['status']= 1;
				$return_data['result']= $result->result;
				$text_color 		  = 'green';
				$message	  		  = '<span class="text-'.$text_color.'">'.$result->message.'</span>';
			} else {
				$message	  		  = '<span class="text-red">Option data not found.</span>';
			}

		} else {
			$message	  = '<span class="text-red">Your request is missing required information.</span>';
		}
		
		$return_data['status_msg'] = $message;

		print_r( json_encode( $return_data ) );
		die();
	}
	
	/** Load view option modal **/
	function dispatch_get_option_request( $module_id = false, $table_name = false, $list_name = false, $order_column = false, $postdata = false ){
		$result = false;
		if( !empty( $module_id ) && !empty( $table_name ) && !empty( $postdata ) ){
			$data 			= (object) ['result'=>null, 'message'=>'Something went wrong, please try again!' ];
			$fetch_option	= $this->webapp_service->api_dispatcher( $this->api_end_point.'settings/fetch_option', $postdata, ['auth_token'=>$this->auth_token], true );
			$result			= ( !empty( $fetch_option->fetch_option ) ) ? $fetch_option->fetch_option : false;
			if( !empty( $result ) ){
				$record_data   = $this->load_record_modal( $module_id, $table_name, $list_name, $order_column, $result );
				$data->result  = $record_data;
				$data->message = ( isset( $fetch_option->message ) ) ? $fetch_option->message : 'Something went wrong, please try again!';
			}
			$result = $data;
		}
		return $result;
	}
	
	function load_record_modal( $module_id = false, $table_name = false, $list_name = false, $order_column = false, $result = false ){
		$return_data = '';
		if( !empty( $module_id ) && !empty( $table_name ) && !empty( $result ) ){
			
			$edit_option_url = base_url( 'settings/edit_option/'.$table_name );
			$modal_header 	= !empty( $list_name ) ? 'Edit '.urldecode( $list_name ).' Option' : 'Edit Option';
			$modal_body		= '';

			switch( strtolower( $table_name ) ){
				
				case 'audit_categories':
					##	Prepare modal body
					$modal_body .= '<div>';
						$modal_body .= '<input type="hidden" name="category_id" value="'.$result->category_id.'">';
						$modal_body .= '<div class="input-group form-group">';
							$modal_body .= '<label class="input-group-addon">Category Name</label><input name="category_name" class="form-control required" type="text" placeholder="Category Name" value="'.( !empty( $result->category_name ) ? $result->category_name : '' ).'" />';
						$modal_body .= '</div>';
						
						$modal_body .= '<div class="input-group form-group">';
							$modal_body .= '<label class="input-group-addon">Category Description</label>';
							$modal_body .= '<textarea name="description" class="form-control required" type="text" rows="4" >'.( !empty( $result->description ) ? $result->description : '' ).'</textarea>';
						$modal_body .= '</div>';
					$modal_body .= '</div>';
					
					break;
				
				case 'asset_types':
					$audit_categories	= $this->webapp_service->api_dispatcher( $this->api_end_point.'audit/audit_categories', [ 'account_id'=>$this->user->account_id ], ['auth_token'=>$this->auth_token], true );
					$categories			= ( isset( $audit_categories->audit_categories ) ) ? $audit_categories->audit_categories : null;

					$asset_sub_cats = asset_sub_categories();
					
					##	Prepare modal body
					$modal_body .= '<div>';
						$modal_body .= '<input type="hidden" name="asset_type_id" value="'.$result->asset_type_id.'">';
						$modal_body .= '<div class="input-group form-group">';
							$modal_body .= '<label class="input-group-addon">Asset type</label><input name="asset_type" class="form-control required" type="text" placeholder="Asset type" value="'.( !empty( $result->asset_type ) ? $result->asset_type : '' ).'" />';
						$modal_body .= '</div>';
						
						$modal_body .= '<div class="input-group form-group"><label class="input-group-addon">Asset group</label>';
							$modal_body .= '<select id="asset_group" name="asset_group" class="form-control required" data-label_text=""><option value="">Please select</option>';
								foreach( $asset_sub_cats as $asset_group => $asset_group_text ){
									$modal_body .= '<option value="'.$asset_group.'" '.( $asset_group == $result->asset_group ? 'selected=selected' : '' ).' >'.$asset_group_text.'</option>';
								}
							$modal_body .= '</select>';
						$modal_body .= '</div>';
						
						$modal_body .= '<div class="input-group form-group"><label class="input-group-addon">Asset Category</label>';
							$modal_body .= '<select id="category_id" name="category_id" class="form-control required"><option value="">Please select</option>';
								if( !empty( $categories ) ){
									foreach( $categories as $k => $category ){
										$modal_body .= '<option value="'.$category->category_id.'" '.( $category->category_id == $result->category_id ? 'selected=selected' : '' ).' >'.$category->category_name_alt.'</option>';
									}
								}
							$modal_body .= '</select>';
						$modal_body .= '</div>';
						
						$modal_body .= '<div class="input-group form-group">';
							$modal_body .= '<label class="input-group-addon">Description</label>';
							$modal_body .= '<textarea name="description" class="form-control required" type="text" value="" rows="4" >'.( ( !empty( $result->description ) ) ? $result->description : ''  ).'</textarea>';
						$modal_body .= '</div>';
					$modal_body .= '</div>';
					break;
					
				case 'asset_statuses':
				
					$status_groups = status_groups();
					
					##	Prepare modal body
					$modal_body .= '<div>';
						$modal_body .= '<input type="hidden" name="status_id" value="'.$result->status_id.'">';
						$modal_body .= '<div class="input-group form-group">';
							$modal_body .= '<label class="input-group-addon">Status Name</label><input name="status_name" class="form-control required" type="text" placeholder="Asset Status Name" value="'.$result->status_name.'" />';
						$modal_body .= '</div>';
						
						$modal_body .= '<div class="input-group form-group"><label class="input-group-addon">Treat this as?</label>';
							$modal_body .= '<select id="status_group" name="status_group" class="form-control required" data-label_text=""><option value="">Please select</option>';
								foreach( $status_groups as $group_name => $group_details ){
									$modal_body .= '<option value="'.$group_details->group_name.'" data-group_colour="'.$group_details->group_colour.'" '.( $group_details->group_name == $result->group_name ? 'selected=selected' : '' ).' >'.$group_details->group_desc.'</option>';
								}
							$modal_body .= '</select>';
						$modal_body .= '</div>';
						
						$modal_body .= '<div class="input-group form-group">';
							$modal_body .= '<label class="input-group-addon">Status Description</label>';
							$modal_body .= '<textarea name="status_desc" class="form-control required" type="text" value="" rows="4" >'.$result->status_desc.'</textarea>';
						$modal_body .= '</div>';
					$modal_body .= '</div>';
					
					break;
					
				case 'asset_eol_statuses':
					//
					break;
					
				case 'user_statuses':
					//
					break;
			}
			
			//Prepare Modal
			$modal_template = '<div class="modal fade edit-option-'.$table_name.'" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog modal-md">';
				$modal_template .= '<form class="form-'.$table_name.'" id="edit-option-form-'.$table_name.'" action="'.$edit_option_url.'"><div class="modal-content">';
					$modal_template .= '<input type="hidden" name="table_name" value="'.$table_name.'" />';
					$modal_template .= '<input type="hidden" name="module_id" value="'.$module_id.'" />';
					$modal_template .= '<input type="hidden" name="page" value="details" />';
					##Header
					$modal_template .= '<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>';
						$modal_template .= '<h4 class="modal-title" >'.$modal_header.'</h4>';
						$modal_template .= '<span id="feedback-message"></span>';
					$modal_template .= '</div>';
					
					##Body
					$modal_template .= '<div class="modal-body">';
						$modal_template .= '<div class="row"><div class="col-md-12 col-sm-12 col-xs-12">';
							$modal_template .= $modal_body;
						$modal_template .= '</div></div>';
					$modal_template .= '</div>';
					
					##Footer
					$modal_template .= '<div class="modal-footer"><div class="row"><div class="col-md-12 col-sm-12 col-xs-12">';
						$modal_template .= '<button class="edit-option-btn btn btn-success btn-block" data-table_name="'.$table_name.'" data-list_name="'.urldecode( $list_name ).'" data-module_id="'.$module_id.'" data-order_column="'.$order_column.'" type="button" >Edit Option</button>';
					$modal_template .= '</div></div></div>';
				$modal_template .= '</div></form>';
			$modal_template .= '</div></div>';
		
			$return_data .= $modal_template;
		}
		return $return_data;
	}
}
	