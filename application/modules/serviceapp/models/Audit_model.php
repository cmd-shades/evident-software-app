<?php

namespace Application\Modules\Service\Models;

class Audit_model extends CI_Model {

	function __construct(){
		parent::__construct();
		$this->load->model('serviceapp/Document_Handler_model','document_service');
		$this->load->model('serviceapp/Asset_model','asset_service');
		$this->audit_group = false;

		#$this->mobileDetect = new Mobile_Detect;
    }

	/** Searchable fields **/
	private $searchable_fields  			= ['audit.audit_id', 'audit.audit_type_id', 'audit.asset_id', 'audit.site_id', 'audit.vehicle_reg', 'asset.asset_unique_id', 'audit.created_by', 'audit.site_id' ];
	private $audit_stats_group	  			= ['asset','site',/*'fleet'*/];
	private $exceptions_search_fields  		= ['audit_exceptions.record_type', 'audit_exceptions.recommendations', 'audit_exceptions.failure_reasons', 'audit_exceptions.audit_id', 'audit_exceptions.site_id', 'audit_exceptions.asset_id', 'audit_exceptions.vehicle_reg','audit_exceptions.priority_rating', 'audit_types.audit_type'];
	private $evidoc_types_search_fields  	= ['audit_types.audit_type', 'audit_types.alt_audit_type', 'audit_types.audit_frequency', 'audit_types.audit_type_desc' ];
	private $evidoc_questions_search_fields = [];
	private $file_response_types			= ['file','signature'];
	private $audit_categories_search_fields	= ['category_name','description', 'category_group'];

	## Fixed number of audits needs to be done for the month. At the later stage, the calculations need to be done dynamically.
	private $audits2do_number	= 250;

	/** Primary table name **/
	private $primary_tbl = 'audit';

	/** Get Evidoc Categories **/
	public function get_evidoc_categories( $account_id = false, $where = false ){

		$result = false;

		if( $account_id ){
			#$this->db->where( 'evidoc_categories.account_id', $account_id );
			$this->db->where( '( evidoc_categories.account_id IS NULL OR evidoc_categories.account_id = "" )' );
		}else{
			$this->db->where( '( evidoc_categories.account_id IS NULL OR evidoc_categories.account_id = "" )' );
		}

		if( !empty( $where ) ){

			$where = convert_to_array( $where );

			if( isset( $where['category_id'] ) ){
				if( !empty( $where['category_id'] ) ){
					$row = $this->db->get_where( 'evidoc_response_types', [ 'category_id'=>$where['category_id'] ] )->row();
					if( !empty( $row ) ){
						$resp_options 				= $this->_get_response_type_options( $row->category_id );
						$row->response_type_options = ( !empty( $resp_options ) ) ? $resp_options : null;
						$result = $row;
						$this->session->set_flashdata( 'message','Evidoc Category data found' );
						return $result;
					} else {
						$this->session->set_flashdata( 'message','Evidoc Category data data not found' );
						return false;
					}
				}
				unset( $where['category_id'] );
			}

		}

		$query = $this->db->order_by( 'category_name' )
			->get( 'evidoc_categories' );

		if( $query->num_rows() > 0 ){
			$result = ( !empty( $single_record ) ) ? $query->result()[0] : $query->result();
			$this->session->set_flashdata( 'message','Evidoc Categories data found.' );
		}else{
			$this->session->set_flashdata( 'message','Evidoc Categories data not found.' );
		}
		return $result;
	}

	/*
	* Get Evidocs single records or multiple records
	*/
	public function get_audits( $account_id=false, $audit_id = false, $asset_id = false, $site_id = false, $vehicle_reg = false, $person_id = false, $job_id = false, $audit_status = false, $inc_responses = false, $where = false, $order_by = false, $limit=DEFAULT_LIMIT, $offset=DEFAULT_OFFSET  ){

		$result = false;

		$where 	= $raw_where = convert_to_array( $where );

		if( isset( $where['allowed_contracts'] ) ){
			if( !empty( $where['allowed_contracts'] ) ){
				$allowed_contracts = is_array( $where['allowed_contracts'] ) ? $where['allowed_contracts'] : [ $where['allowed_contracts'] ];
				$this->db->where_in( 'audit_types.contract_id', $allowed_contracts );
			}
			unset( $where['allowed_contracts'] );
		}

		if( !empty( $where['asset_evidocs'] ) ){
			if( !empty( $site_id ) ){
				$get_audited_assets = $this->db->select( 'asset_id' )->get_where( 'asset', ['account_id'=>$account_id, 'site_id'=>$site_id] );
				if( $get_audited_assets->num_rows() ){
					$asset_ids = array_column( $get_audited_assets->result_array(), 'asset_id' );
					if( !empty( $asset_ids ) ){
						$this->db->where_in( 'audit.asset_id',$asset_ids );
					}
				}
			}
			unset( $where['asset_evidocs'] );
		} else {
			
			if( !empty( $site_id ) ){
				$this->db->where( 'audit.site_id', $site_id );
				$raw_where['site_id'] = $site_id;
			}
		}
		
		if( isset( $where['grouped'] ) ){
			if( !empty( $where['grouped'] ) ){
				$grouped = true;
			}
			unset( $where['grouped'] );
		}

		$this->db->select( 'audit_types.*, audit.*,
			CASE WHEN audit.contract_id > 0 THEN audit.contract_id 
				WHEN audit_types.contract_id > 0 THEN audit_types.contract_id
				WHEN job_types.contract_id > 0 THEN job_types.contract_id
				ELSE NULL END AS linked_contract_id,
			audit.finish_time `evidoc_completion_date`, audit_categories.category_name, concat(user.first_name," ",user.last_name) `created_by`,concat(modifier.first_name," ",modifier.last_name) `last_modified_by`, asset.asset_type_id, asset.asset_unique_id, fleet_vehicle.vehicle_id, fleet_vehicle.vehicle_make, fleet_vehicle.vehicle_model, fleet_vehicle.year, site.site_name, site.site_postcodes, ars.result_status,  ars.result_status_group, concat(person.first_name," ",person.last_name) `audited_person`, job_types.job_type', false )
			->select( 'account_discipline.account_discipline_name `discipline_name`,account_discipline.account_discipline_image_url `discipline_image_url`', false )
			->join( 'audit_types','audit_types.audit_type_id = audit.audit_type_id','left')
			->join( 'audit_categories','audit_categories.category_id = audit_types.category_id','left')
			->join( 'audit_result_statuses ars','ars.audit_result_status_id = audit.audit_result_status_id','left')
			->join( 'job_types','job_types.evidoc_type_id = audit_types.audit_type_id','left')
			->join('asset', 'asset.asset_id = audit.asset_id', 'left')
			->join('user', 'user.id = audit.created_by', 'left')
			->join('fleet_vehicle', 'fleet_vehicle.vehicle_reg = audit.vehicle_reg', 'left')
			->join('site', 'site.site_id = audit.site_id', 'left')
			->join('user person','person.id = audit.person_id','left')
			->join('user modifier','modifier.id = audit.last_modified_by','left')
			->join( 'account_discipline','account_discipline.discipline_id = audit_types.discipline_id','left')
			->where( 'audit.account_id', $account_id );

		if( !empty( $job_id ) ){
			$this->db->where( 'audit.job_id',$job_id );
		}

		if( !empty( $person_id ) ){
			$this->db->where( 'audit.person_id',$person_id );
		}

		if( !empty( $customer_id ) ){
			$this->db->where( 'audit.customer_id',$customer_id );
		}

		if( $vehicle_reg ){
			$this->db->where( '( REPLACE( " ","", audit.vehicle_reg ) = "'.$vehicle_reg.'" OR audit.vehicle_reg = "'.$vehicle_reg.'" )' );
		}

		if( $audit_status ){
			$this->db->where( 'audit_status', $audit_status );
		}

		$extra_where	= false;

		if( !empty( $where ) ){

			if( isset( $where['asset_id'] ) ){
				if( !empty( $where['asset_id'] ) ){
					$this->db->where( 'audit.asset_id', $where['asset_id'] );
				}
				unset( $where['asset_id'] );
			}

			if( isset( $where['site_id'] ) ){
				if( !empty( $where['site_id'] ) ){
					$this->db->where( 'audit.site_id', $where['site_id'] );
				}
				unset( $where['site_id'] );
			}

			if( isset( $where['person_id'] ) ){
				if( !empty( $where['person_id'] ) ){
					$this->db->where( 'audit.person_id', $where['person_id'] );
				}
				unset( $where['person_id'] );
			}


			if( isset( $where['vehicle_reg'] ) ){
				if( !empty( $where['vehicle_reg'] ) ){
					$this->db->where( 'audit.vehicle_reg', $where['vehicle_reg'] );
				}
				unset( $where['vehicle_reg'] );
			}

			if( isset( $where['sectioned'] ) ){
				if( !empty( $where['sectioned'] ) ){
					$extra_where['sectioned'] = $where['sectioned'];
				}
				unset( $where['sectioned'] );
			}

			if( isset( $where['docs_ungrouped'] ) ){
				if( !empty( $where['docs_ungrouped'] ) ){
					$docs_ungrouped = true;
				}
				unset( $where['docs_ungrouped'] );
			}

			#$this->db->where( $where );
		}

		if( $audit_id ){

			$this->db->select( 'job.works_required', false )
				->join( 'job','job.job_id = audit.job_id','left');

			$row = $this->db->get_where('audit',['audit_id'=>$audit_id])->row();

			if( !empty( $row ) ){

				$source_table = false;
				switch( $row->audit_group ){
					## All asset type audits
					case ( in_array( strtolower( $row->audit_group ), ['asset'] ) ):
						$source_table = 'audit_responses_assets';
						$this->audit_group = 'asset';
						
						##Get Asset Details
						$asset_info = $this->get_additional_info( $account_id, $this->audit_group, ['asset_id'=>$row->asset_id] );
						
						break;

					## All site type audits
					case ( in_array( strtolower( $row->audit_group ), ['site'] ) ):
						$source_table = 'audit_responses_sites';
						$this->audit_group = 'site';
						break;

					## All People type audits
					case ( in_array( strtolower( $row->audit_group ), ['people','person'] ) ):
						$source_table = 'audit_responses_people';
						$this->audit_group = 'people';
						break;

					## All vehicle-type audits
					case ( in_array( strtolower( $row->audit_group ), ['vehicle','fleet'] ) ):
						$source_table = 'audit_responses_fleet';
						$this->audit_group = 'fleet';
						break;

					## All Job type audits
					case ( in_array( strtolower( $row->audit_group ), ['job'] ) ):
						$source_table = 'audit_responses_job';
						$this->audit_group = 'job';
						break;

					## All Customer audits
					case ( in_array( strtolower( $row->audit_group ), ['customer'] ) ):
						$source_table = 'audit_responses_customer';
						$this->audit_group = 'customer';

						##Get customer Details
						##$customer_info = $this->get_additional_info( $account_id, $this->audit_group, ['customer_id'=>$row->customer_id] ); - Method deprecated 03/12/2020
						break;
						
					case ( in_array( strtolower( $row->audit_group ), ['premises'] ) ):
						$source_table = 'audit_responses_premises';
						$this->audit_group = 'premises';
						break;
						
					case ( in_array( strtolower( $row->audit_group ), ['generic'] ) ):
					
						##Get AssetS linked to this Audit
						$generic_assets_list = $this->get_audit_generic_assets( $account_id, $row->audit_id );
						$row->generic_assets = !empty( $generic_assets_list ) ? $generic_assets_list : null;
					
						$source_table = 'audit_responses_generic';
						$this->audit_group = 'generic';
						break;
				}

				#$row->customer_info 	= !empty( $customer_info ) ? $customer_info : null;
				$row->customer_info 	= $this->get_customer_info_by_job_id( $account_id, [ 'customer_id'=>$row->customer_id, 'job_id'=>$row->job_id ] );
				$row->building_info 	= $this->get_building_info_by_job_id( $account_id, [ 'site_id'=>$row->site_id, 'job_id'=>$row->job_id ] );
				$row->audit_responses 	= $this->get_audit_responses( $audit_id, $source_table, false, false, $extra_where );
				$docs_where 			= [ 'audit_id'=>$audit_id, 'attached_to_question'=>1 ];
				if( !empty( $docs_ungrouped ) ){
					$docs_where['docs_ungrouped'] = 1;
				}
				$uploaded_docs 			= $this->document_service->get_document_list( $account_id, $this->audit_group, $docs_where );
				
				if( !empty( $docs_where['docs_ungrouped'] ) ){
					$row->uploaded_docs 	= ( !empty( $uploaded_docs ) ) ? $uploaded_docs : null;					
				} else {
					$row->uploaded_docs 	= ( !empty( $uploaded_docs[$account_id] ) ) ? $uploaded_docs[$account_id] : null;					
				}

				$this->session->set_flashdata('message','Evidocs found');
				$result = $row;

			}else{
				$this->session->set_flashdata('message','Evidocs not found');
			}
			return $result;
		}

		if( $asset_id ){
			$this->db->where('audit.asset_id', $asset_id);
		}

		if( $order_by ){
			$order = $this->ssid_common->_clean_order_by( $order_by, $this->primary_tbl );
			if( !empty( $order ) ){ $this->db->order_by( $order ); }
		}else{
			$this->db->order_by( 'audit.audit_id DESC' );
		}

		$this->db->limit( $limit, $offset );

		$audit = $this->db->group_by('audit.audit_id')
			->order_by('audit_id desc')
			->get('audit');

		if( $audit->num_rows() > 0 ){

			$data = [];

			foreach( $audit->result() as $k=>$row ){

				if( $inc_responses ){
					$source_table = false;
					switch( $row->audit_group ){
						## All asset type audits
						case ( in_array( strtolower( $row->audit_group ), ['asset'] ) ):
							$source_table = 'audit_responses_assets';
							break;

						## All site type audits
						case ( in_array( strtolower( $row->audit_group ), ['site'] ) ):
							$source_table = 'audit_responses_sites';
							break;

						## All People type audits
						case ( in_array( strtolower( $row->audit_group ), ['people','person'] ) ):
							$source_table = 'audit_responses_people';
							break;

						## All vehicle-type audits
						case ( in_array( strtolower( $row->audit_group ), ['vehicle','fleet'] ) ):
							$source_table = 'audit_responses_fleet';
							break;

						## All Job type audits
						case ( in_array( strtolower( $row->audit_group ), ['job'] ) ):
							$source_table = 'audit_responses_job';
							break;

						## All Customer audits
						case ( in_array( strtolower( $row->audit_group ), ['customer'] ) ):
							$source_table = 'audit_responses_customer';
							break;
					}
				}

				$row->audit_responses = ( !empty( $inc_responses ) ) ? $this->get_audit_responses( $row->audit_id, $source_table ) : null;

				if( !empty( $grouped ) ){
					$data[$row->discipline_id]['discipline_id'] 		= $row->discipline_id;
					$data[$row->discipline_id]['discipline_name'] 		= $row->discipline_name;
					$data[$row->discipline_id]['discipline_image_url'] 	= $row->discipline_image_url;
					$data[$row->discipline_id]['evidocs'][$k] 			= $row;
				} else {
					$data[$k] = $row;
				}
				

			}

			$result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
			$result->records 			= $data;
			$counters 					= $this->get_total_audits( $account_id, $search_term = false, $raw_where, false, $limit );

			$result->counters->total 	= ( !empty( $counters->total ) ) ? $counters->total : null;
			$result->counters->pages 	= ( !empty( $counters->pages ) ) ? $counters->pages : null;
			$result->counters->limit  	= ( !empty( $limit ) ) 			 ? $limit : $result->counters->total;
			$result->counters->offset 	= $offset;

			$this->session->set_flashdata('message','Evidocs records found');
		} else {
			$this->session->set_flashdata('message','Evidocs record(s) not found');
		}
		return $result;
	}

	/** Get Evidocs types **/
	public function get_audit_types( $account_id = false, $audit_group = false, $audit_type = false, $audit_type_id = false, $categorized = false, $category = false, $ungrouped = false, $asset_type_id = false, $apply_limit = false, $category_id = false, $audit_frequency = false, $frequency_id = false ){
		//Redireect where to get this
		$where = [
			'audit_type'=>( !empty( $audit_type ) ) ? $audit_type : false,
			'audit_group'=>( !empty( $audit_group ) ) ? $audit_group : false,
			'audit_type_id'=>( !empty( $audit_type_id ) ) ? $audit_type_id : false,
			'categorized'=>( !empty( $categorized ) ) ? $categorized : false,
			'ungrouped'=>( !empty( $ungrouped ) ) ? $ungrouped : false,
			'asset_type_id'=>( !empty( $asset_type_id ) ) ? $asset_type_id : false,
			'apply_limit'=>( !empty( $apply_limit ) ) ? $apply_limit : false,
			'category_id'=>( !empty( $category_id ) ) ? $category_id : false,
			'audit_frequency'=>( !empty( $audit_frequency ) ) ? $audit_frequency : false,
			'frequency_id'=>( !empty( $frequency_id ) ) ? $frequency_id : false,
		];
		$records = $this->get_evidoc_types( $account_id, false, $where );
		// $records = ( !empty( $records->records ) ) ? $records->records : ( !empty( $records ) ? $records : false );
		return $records;

	}

	/*
	*	Get list of Evidocs types list and search though it
	*/
	public function get_evidoc_types( $account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){

		$result = false;

		if( !empty( $account_id ) ){
			$this->db->select( 'audit_types.*, job_types.job_type_id, job_types.contract_id `job_type_contract_id`, schedule_frequencies.frequency_name, asset_types.asset_type, asset_types.asset_group, audit_categories.category_id, audit_categories.category_name, audit_categories.category_group, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`, contract.contract_name `job_contract_name`,  evidoc_contract.contract_name `evidoc_contract_name`, account_discipline.account_discipline_name, account_discipline.account_discipline_image_url `discipline_image_url`', false )
				->join( 'user creater', 'creater.id = audit_types.created_by', 'left' )
				->join( 'user modifier', 'modifier.id = audit_types.last_modified_by', 'left' )
				->join( 'audit_categories', 'audit_types.category_id = audit_categories.category_id', 'left' )
				->join( 'asset_types', 'asset_types.asset_type_id = audit_types.asset_type_id', 'left' )
				->join( 'schedule_frequencies', 'schedule_frequencies.frequency_id = audit_types.frequency_id', 'left' )
				->join( 'job_types', 'job_types.evidoc_type_id = audit_types.audit_type_id', 'left' )
				->join( 'contract', 'job_types.contract_id = contract.contract_id', 'left' )
				->join( 'contract evidoc_contract', 'evidoc_contract.contract_id = audit_types.contract_id', 'left' )
				->join( 'account_discipline','account_discipline.discipline_id = audit_types.discipline_id','left' )
				->where( 'audit_types.is_active', 1 );	

			$where = $raw_where = convert_to_array( $where );

			if( !empty( $account_id ) && ( in_array( $this->ion_auth->_current_user->id, SUPER_ADMIN_ACCESS ) ) && !empty( $where['all_accounts'] ) ){
				$this->db->select( 'account.account_name', false )
					->join( 'account', 'account.account_id = audit_types.account_id', 'left' )
					->order_by( 'account.account_name' )
					->where( 'account.account_status', 'Active' );
				#$this->db->where( 'audit_types.account_id', $account_id );
				unset( $where['all_accounts'] );
			} else {
				$this->db->where( 'audit_types.account_id', $account_id );
			}

			if( isset( $where['audit_type_id'] ) || isset( $where['audit_type_ref'] ) ){

				$ref_condition = ( !empty( $where['audit_type_id'] ) ) ? [ 'audit_type_id'=>$where['audit_type_id'] ] : ( !empty( $where['audit_type_ref'] ) ?  [ 'audit_type_ref'=>$where['audit_type_ref'] ] : false );
				if( !empty( $ref_condition ) ){
					$row = $this->db->get_where( 'audit_types', $ref_condition )->row();
					if( !empty( $row ) ){
						$result = ( object ) ['records'=>$row];
						$this->session->set_flashdata( 'message','Evidoc Type data found' );
						return $result;
					} else {
						$this->session->set_flashdata( 'message','Evidoc Type data not found' );
						return false;
					}
				}
				unset( $where['audit_type_id'], $where['audit_type_ref'] );
			}

			//Trim list if asset type id is supplied
			if( isset( $where['asset_type_id'] ) ){
				if( !empty( $where['asset_type_id'] ) ){
					$sql 	= '( SELECT DISTINCT( qb.audit_type_id ) FROM audit_question_bank `qb` WHERE qb.asset_type_id = "'.$where['asset_type_id'].'" GROUP BY qb.audit_type_id )';
					$this->db->where_in( 'audit_type_id', $sql, false );
				}
				unset( $where['asset_type_id'] );
			}

			if( !empty( $search_term ) ){
				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->evidoc_types_search_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						if( !empty( $search_where['audit_types.evidoc_group_id'] ) ){
							$search_where['evidoc_groups.evidoc_group_name'] =  trim( $term );
							unset($search_where['audit_types.evidoc_group_id']);
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				}else{
					foreach( $this->evidoc_types_search_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					if( !empty( $search_where['audit_types.evidoc_group_id'] ) ){
						$search_where['evidoc_groups.evidoc_group_name'] =  trim( $search_term );
						unset($search_where['audit_types.evidoc_group_id']);
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( !empty( $where ) ){

				if( isset( $where['audit_type'] ) ){
					if( !empty( $where['audit_type'] ) ){
						$audit_type_ref = strtoupper( strip_all_whitespace( $where['audit_type'] ) );
						$this->db->where( '( audit_types.audit_type = "'.$where['audit_type'].'" OR audit_types.audit_type_ref = "'.$audit_type_ref.'" )' );
					}
					unset( $where['audit_type'] );
				}

				if( isset( $where['audit_group'] ) ){
					if( !empty(  $where['audit_group'] ) ){
						$audit_group = ( strtolower( $where['audit_group'] ) == 'fleet' ) ? 'Vehicle' : $where['audit_group'];
						$this->db->where( '( audit_types.audit_group = "'.$where['audit_group'].'" OR audit_types.audit_group = "'.$audit_group.'" )' );
					}
					unset( $where['audit_group'] );
				}

				if( isset( $where['category_id'] ) ){
					if( !empty( $where['category_id'] ) ){
						$category_id = convert_to_array( $where['category_id'] );
						$category_id = is_array( $category_id ) ? $category_id : [ $category_id ];
						$this->db->where_in( 'audit_types.category_id', $category_id );
					}
					unset( $where['category_id'] );
				}

				if( isset( $where['category'] ) ){
					if( !empty( $where['categorized'] ) ){
						$this->db->where( 'audit_categories.category_name', $where['category'] );
					}
					unset( $where['category'] );
				}

				if( isset( $where['audit_frequency'] ) ){
					if( !empty( $where['audit_frequency'] ) ){
						$this->db->where( 'audit_types.audit_frequency', $where['audit_frequency'] );
					}
					unset( $where['audit_frequency'] );
				}

				if( isset( $where['categorized'] ) ){
					if( !empty( $where['categorized'] ) ){
						$categorized = true;
					}
					unset( $where['categorized'] );
				}

				if( isset( $where['frequency_id'] ) ){
					if( !empty( $where['frequency_id'] ) ){
						$this->db->where( 'audit_types.frequency_id', $where['frequency_id'] );
					}
					unset( $where['frequency_id'] );
				}

				if( isset( $where['ungrouped'] ) ){
					//
					unset( $where['ungrouped'] );
				}

				if( isset( $where['apply_limit'] ) ){
					if( !empty( $where['apply_limit'] ) ){
						$this->db->limit( $limit, $offset );
						$apply_limit = true;
					}
					unset( $where['apply_limit'] );
				}
				
				if( isset( $where['discipline_id'] ) ){
					if( !empty( $where['discipline_id'] ) ){
						$this->db->where( 'audit_types.discipline_id', $where['discipline_id'] );
					}
					unset( $where['discipline_id'] );
				}

				if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}

			if( !empty( $order_by ) ){
				$this->db->order_by( $order_by );
			}else{
				$this->db->order_by( 'audit_type_id DESC, audit_type' );
			}

			$query = $this->db->group_by( 'audit_types.audit_type_id' )
					->get( 'audit_types' );

			if( $query->num_rows() > 0 ){

				if( !empty( $categorized ) ){
					$data = [];
					foreach( $query->result() as $k => $row ){
						$data[$row->audit_group][] = $row;
					}
					$result_data = $data;
				}else{
					$result_data = $query->result();
				}

				$result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
				$result->records 			= $result_data;
				$counters 					= $this->evidoc_types_totals( $account_id, $search_term, $raw_where, $limit );
				$result->counters->total 	= ( !empty( $counters->total ) ) ? $counters->total : null;
				$result->counters->pages 	= ( !empty( $counters->pages ) ) ? $counters->pages : null;
				$result->counters->limit  	= ( !empty( $apply_limit ) ) ? $limit : $result->counters->total;
				$result->counters->offset 	= $offset;

				$this->session->set_flashdata( 'message','Evidoc types data found' );
			} else {
				$this->session->set_flashdata( 'message','No data found' );
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}

		return $result;
	}

	/** Get Evidocs lookup counts **/
	public function evidoc_types_totals( $account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT ){
		$result = false;
		if( !empty( $account_id ) ){

			$this->db->select( 'audit_types.audit_type_id', false )
				->join( 'user creater', 'creater.id = audit_types.created_by', 'left' )
				->join( 'user modifier', 'modifier.id = audit_types.last_modified_by', 'left' )
				->join( 'audit_categories', 'audit_types.category_id = audit_categories.category_id', 'left' )
				->join( 'asset_types', 'asset_types.asset_type_id = audit_types.asset_type_id', 'left' )
				->join( 'job_types', 'job_types.evidoc_type_id = audit_types.audit_type_id', 'left' )
				->join( 'contract', 'job_types.contract_id = contract.contract_id', 'left' )
				->join( 'contract evidoc_contract', 'evidoc_contract.contract_id = audit_types.contract_id', 'left' )
				->where( 'audit_types.is_active', 1 );
			
			$where = $raw_where = convert_to_array( $where );

			if( !empty( $account_id ) && ( in_array( $this->ion_auth->_current_user->id, SUPER_ADMIN_ACCESS ) ) && !empty( $where['all_accounts'] ) ){
				$this->db->select( 'account.account_name', false )
					->join( 'account', 'account.account_id = audit_types.account_id', 'left' )
					->order_by( 'account.account_name' )
					->where( 'account.account_status', 'Active' );
				#$this->db->where( 'audit_types.account_id', $account_id );
				unset( $where['all_accounts'] );
			} else {
				$this->db->where( 'audit_types.account_id', $account_id );
			}

			if( isset( $where['audit_type_id'] ) || isset( $where['audit_type_ref'] ) ){

				$ref_condition = ( !empty( $where['audit_type_id'] ) ) ? [ 'audit_type_id'=>$where['audit_type_id'] ] : ( !empty( $where['audit_type_ref'] ) ?  [ 'audit_type_ref'=>$where['audit_type_ref'] ] : false );

				if( !empty( $ref_condition ) ){
					$row = $this->db->get_where( 'audit_types', $ref_condition )->row();
					if( !empty( $row ) ){
						$this->session->set_flashdata( 'message','Evidoc Type data found' );
						$result = ( object ) ['records'=>$row];
						return $result;
					} else {
						$this->session->set_flashdata( 'message','Evidoc Type data not found' );
						return false;
					}
				}
				unset( $where['audit_type_id'], $where['audit_type_ref'] );
			}

			//Trim list if asset type id is supplied
			if( isset( $where['asset_type_id'] ) ){
				if( !empty( $where['asset_type_id'] ) ){
					$sql 	= '( SELECT DISTINCT( qb.audit_type_id ) FROM audit_question_bank `qb` WHERE qb.asset_type_id = "'.$where['asset_type_id'].'" GROUP BY qb.audit_type_id )';
					$this->db->where_in( 'audit_type_id', $sql, false );
				}
				unset( $where['asset_type_id'] );
			}

			if( !empty( $search_term ) ){
				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->evidoc_types_search_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						if( !empty( $search_where['audit_types.evidoc_group_id'] ) ){
							$search_where['evidoc_groups.evidoc_group_name'] =  trim( $term );
							unset($search_where['audit_types.evidoc_group_id']);
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				}else{
					foreach( $this->evidoc_types_search_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					if( !empty( $search_where['audit_types.evidoc_group_id'] ) ){
						$search_where['evidoc_groups.evidoc_group_name'] =  trim( $search_term );
						unset($search_where['audit_types.evidoc_group_id']);
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( !empty( $where ) ){

				if( isset( $where['audit_type'] ) ){
					if( !empty( $where['audit_type'] ) ){
						$audit_type_ref = strtoupper( strip_all_whitespace( $where['audit_type'] ) );
						$this->db->where( '( audit_types.audit_type = "'.$where['audit_type'].'" OR audit_types.audit_type_ref = "'.$audit_type_ref.'" )' );
					}
					unset( $where['audit_type'] );
				}

				if( isset( $where['audit_group'] ) ){
					if( !empty(  $where['audit_group'] ) ){
						$audit_group = ( strtolower( $where['audit_group'] ) == 'fleet' ) ? 'Vehicle' : $where['audit_group'];
						$this->db->where( 'audit_types.audit_group', $audit_group );
					}
					unset( $where['audit_group'] );
				}

				if( isset( $where['category_id'] ) ){
					if( !empty( $where['category_id'] ) ){
						$category_id = convert_to_array( $where['category_id'] );
						$category_id = is_array( $category_id ) ? $category_id : [ $category_id ];
						$this->db->where_in( 'audit_types.category_id', $category_id );
					}
					unset( $where['category_id'] );
				}

				if( isset( $where['category'] ) ){
					if( !empty( $where['categorized'] ) ){
						$this->db->where( 'audit_categories.category_name', $where['category'] );
					}
					unset( $where['category'] );
				}

				if( isset( $where['audit_frequency'] ) ){
					if( !empty( $where['audit_frequency'] ) ){
						$this->db->where( 'audit_types.audit_frequency', $where['audit_frequency'] );
					}
					unset( $where['audit_frequency'] );
				}

				if( isset( $where['categorized'] ) ){
					if( !empty( $where['categorized'] ) ){
						$categorized = true;
					}
					unset( $where['categorized'] );
				}

				if( isset( $where['frequency_id'] ) ){
					if( !empty( $where['frequency_id'] ) ){
						$this->db->where( 'audit_types.frequency_id', $where['frequency_id'] );
					}
					unset( $where['frequency_id'] );
				}

				if( isset( $where['ungrouped'] ) ){
					//
					unset( $where['ungrouped'] );
				}

				if( isset( $where['apply_limit'] ) ){
					if( !empty( $where['apply_limit'] ) ){
						$apply_limit = true;
					}
					unset( $where['apply_limit'] );
				}

				if( isset( $where['discipline_id'] ) ){
					if( !empty( $where['discipline_id'] ) ){
						$this->db->where( 'audit_types.discipline_id', $where['discipline_id'] );
					}
					unset( $where['discipline_id'] );
				}

				if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}

			$query 			  = $this->db->from( 'audit_types' )->count_all_results();
			$results['total'] = !empty( $query ) ? $query : 0;
			$limit 			  = ( !empty( $apply_limit ) ) ? $limit : ( !empty( $limit ) ? $limit : $results['total'] );
			$results['pages'] = !empty( $query ) ? ceil( $query / $limit ) : 0;
			return json_decode( json_encode( $results ) );
		}
		return $result;
	}

	/*
	* Create new Evidocs
	*/
	public function create_audit( $account_id = false, $audit_type_id=false, $audit_data = false ){
		$result = false;

		if( !empty( $account_id ) && !empty( $audit_type_id ) && !empty( $audit_data ) ){
			$data 	  = [];
			$responses= !empty($audit_data['responses']) ? $audit_data['responses'] : null;

			unset( $audit_data['responses'] );

			$data = $this->ssid_common->_data_prepare( $audit_data );

			$site_id  		= ( !empty( $audit_data['site_id'] ) )  		? $audit_data['site_id']  	: null;
			$asset_id 		= ( !empty( $audit_data['asset_id'] ) ) 		? $audit_data['asset_id'] 	: null;
			$vehicle_reg	= ( !empty( $audit_data['vehicle_reg'] ) )  	? $audit_data['vehicle_reg']: null;
			$person_id		= ( !empty( $audit_data['person_id'] ) )  		? $audit_data['person_id']: null;
			$job_id			= ( !empty( $audit_data['job_id'] ) )  			? $audit_data['job_id']: null;
			$customer_id	= ( !empty( $audit_data['customer_id'] ) ) 		? $audit_data['customer_id']: null;
			$generic_assets = ( !empty( $audit_data['generic_assets'] ) ) 	? $audit_data['generic_assets']: null;

			$audit_type = $this->get_audit_types( $account_id, false, false, $audit_type_id );

			$audit_type = ( !empty( $audit_type->records ) ) ? $audit_type->records : ( !empty( $audit_type ) ? $audit_type : false );
			$audit_group= !empty( $audit_type->audit_group ) ? strtolower( $audit_type->audit_group ) : false;
			$data		= ( !empty( $audit_type ) ) ? array_merge( $data, (array) $audit_type ) : $data;

			unset( $data['created_by'], $data['date_created'] );

			switch( $audit_group ){
				## All asset type audits
				case ( in_array( $audit_group, ['asset'] ) ):

					if( !empty( $asset_id ) ){

						##Check that asset exists and it belows to this account_id
						$check_exists = $this->db->get_where( 'asset', ['account_id'=>$account_id,'asset_id'=>$asset_id] )->row();

						if( !empty( $check_exists ) ){
							$result = $this->_process_asset_audit( $account_id, $asset_id, $data, $responses, $audit_group = 'asset' );
						}else{
							$this->session->set_flashdata('message', 'This Asset record does not exist or it does not belong to you.' );
							return false;
						}

					}else{
						$this->session->set_flashdata('message', 'Asset ID is required for this Evidocs type.' );
						return false;
					}

					break;

				## All site type audits
				case ( in_array( $audit_group, ['site', 'building'] ) ):

					if( !empty( $site_id ) ){

						$check_exists = $this->db->get_where( 'site', ['account_id'=>$account_id,'site_id'=>$site_id] )->row();

						if( !empty( $check_exists ) ){
							$result = $this->_process_site_audit( $account_id, $site_id, $data, $responses );
						}else{
							$this->session->set_flashdata('message', 'This Site record does not exist or it does not belong to you.' );
							return false;
						}

					}else{
						$this->session->set_flashdata('message', 'Site ID is required for this Evidocs type.' );
						return false;
					}

					break;


				## All people type audits
				case ( in_array( $audit_group, ['people','person'] ) ):

					if( !empty( $person_id ) ){

						$check_exists = $this->db->get_where( 'people', ['account_id'=>$account_id,'person_id'=>$person_id] )->row();

						if( !empty( $check_exists ) ){
							$result = $this->_process_people_audit( $account_id, $person_id, $data, $responses );
						}else{
							$this->session->set_flashdata('message', 'This Person record does not exist or it does not belong to you.' );
							return false;
						}

					}else{
						$this->session->set_flashdata('message', 'Person ID is required for this Evidocs type.' );
						return false;
					}

					break;

				## All vehicle-type audits
				case ( in_array( $audit_group, ['vehicle','fleet'] ) ):

					if( !empty( $vehicle_reg ) ){

						$check_exists = $this->db->select( 'fleet_vehicle.vehicle_reg' )
							->get_where( 'fleet_vehicle', ['account_id'=>$account_id,'vehicle_reg'=>$vehicle_reg ] )
							->row();
						if( !empty( $check_exists ) ){
							$result = $this->_process_vehicle_audit( $account_id, $vehicle_reg, $data, $responses );
						}else{
							$this->session->set_flashdata('message', 'This Vehicle record does not exist or it does not belong to you.' );
							return false;
						}

					}else{
						$this->session->set_flashdata('message', 'Vehicle Reg is required for this Evidocs type.' );
						return false;
					}

					break;

				## All Job type audits
				case ( in_array( $audit_group, ['job'] ) ):

					if( !empty( $job_id ) ){

						$check_exists = $this->db->get_where( 'job', ['account_id'=>$account_id,'job_id'=>$job_id] )->row();

						if( !empty( $check_exists ) ){
							$result = $this->_process_job_audit( $account_id, $job_id, $data, $responses );
						}else{
							$this->session->set_flashdata('message', 'This Job record does not exist or it does not belong to you.' );
							return false;
						}

					}else{
						$this->session->set_flashdata('message', 'Job ID is required for this Evidocs type.' );
						return false;
					}

					break;

				## All Customer audits
				case ( in_array( $audit_group, ['customer'] ) ):

					if( !empty( $customer_id ) ){

						$check_exists = $this->db->get_where( 'customer', ['account_id'=>$account_id,'customer_id'=>$customer_id] )->row();

						if( !empty( $check_exists ) ){
							$result = $this->_process_customer_audit( $account_id, $customer_id, $data, $responses );
						}else{
							$this->session->set_flashdata('message', 'This Customer record does not exist or it does not belong to you.' );
							return false;
						}

					}else{
						$this->session->set_flashdata('message', 'Customer ID is required for this Evidocs type.' );
						return false;
					}

					break;
					
				## All Premises audits
				case ( in_array( $audit_group, ['premises'] ) ):

					if( !empty( $premises_id ) ){

						$check_exists = $this->db->get_where( 'premises', ['account_id'=>$account_id,'premises_id'=>$premises_id] )->row();

						if( !empty( $check_exists ) ){
							$result = $this->_process_premises_audit( $account_id, $premises_id, $data, $responses );
						}else{
							$this->session->set_flashdata('message', 'This Premises record does not exist or it does not belong to you.' );
							return false;
						}

					}else{
						$this->session->set_flashdata('message', 'Premises ID is required for this Evidocs type.' );
						return false;
					}

					break;

				## All Generic audits
				case ( in_array( $audit_group, ['generic'] ) ) :

					if( !empty( $generic_assets ) ){

						$result = $this->_process_generic_audit( $account_id, $generic_assets, $data, $responses );
						
					}else{
						$this->session->set_flashdata('message', 'At leatst 1 asset is required for this Evidocs type.' );
						return false;
					}

					break;
			}

			if( $result ){
				$this->session->set_flashdata('message', 'Evidocs record created successfully.' );
			}else{
				$message = $this->session->flashdata('message');
				$this->session->set_flashdata('message', ($message) ? $message : 'Evidocs record created successfully.' );
			}
		}else{
			$this->session->set_flashdata('message','No Evidocs data supplied.');
		}
		return $result;
	}

	/* Process audit audit */
	private function _process_asset_audit( $account_id = false, $asset_id = false, $postdata = false, $responses = false, $audit_group = 'asset' ){

		$result = false;

		if( !empty( $account_id ) && !empty( $asset_id ) && !empty( $postdata ) ){

			$job_id   = ( !empty( $postdata['job_id'] ) ) ? $postdata['job_id'] : null;
			$audit_id = ( !empty( $postdata['audit_id'] ) ) ? $postdata['audit_id'] : null;
			$audit_id = $this->_update_audit( $account_id, $audit_id, $postdata );

			if( !empty( $audit_id ) && !empty( $responses ) ){
				foreach( $responses as $segment => $segement_data ){
					$save_responses = $this->_save_audit_responses( $account_id, $audit_id, $postdata, $segement_data, $target_table = 'audit_responses_assets' );
				}
			}

			## Upload any files if they exist
			if(  !empty($_FILES['user_files']['name']) ) {
				$postdata['audit_id']  = $audit_id;
				$uploaded_docs = $this->document_service->upload_files( $account_id, $postdata, $audit_group );
			}

			## Upload any files inside segments if any
			if(  !empty($_FILES['audit_files']['name']) ) {
				$postdata['audit_id']  = $audit_id;
				$audit_uploaded_docs = $this->document_service->upload_audit_files( $account_id, $postdata, $audit_group );
				#return $audit_uploaded_docs;
			}

			## Check if any documents have been uploaded and update the audir record
			if( !empty( $uploaded_docs ) || !empty( $audit_uploaded_docs ) ){
				$postdata['documents_uploaded'] = 1;
			}

			## Check completion status
			$status = $this->check_audit_status( $account_id, $audit_id, $postdata );

			## Do a Quick update to the Evidocs record
			$this->_prep_quick_update( $account_id, $audit_id, $status, $postdata );

			## Get the created record
			$result = $this->get_audits( $account_id, $audit_id );

			if( !empty( $result ) ){

				//Update last audited date
				$this->db->where( [ 'account_id'=>$account_id, 'asset_id'=>$asset_id ] )
					->update( 'asset', [ 'last_audit_date' => date( 'Y-m-d H:i:s' ) ] );

				if( !empty( $job_id ) ){
					$this->_quick_job_update( $account_id, $job_id, [ 'linked_evidoc_id'=>$audit_id, 'asset_id'=>$asset_id ] );
				}

				$this->session->set_flashdata('message','Evidocs  details saved successfully');
			}else{
				$this->session->set_flashdata('message','An error occurred while updating your Evidocs  record');
			}
		}else{
			$this->session->set_flashdata('message','Error! Your request is missing required parameter(s)');
		}
		return $result;
	}

	/* Process site audit */
	private function _process_site_audit( $account_id = false, $site_id = false, $postdata = false, $responses = false, $audit_group = 'site' ){

		$result = false;

		if( !empty( $account_id ) && !empty( $site_id ) && !empty( $postdata ) ){
			$job_id   = ( !empty( $postdata['job_id'] ) ) ? $postdata['job_id'] : null;
			$audit_id = ( !empty( $postdata['audit_id'] ) ) ? $postdata['audit_id'] : null;
			$audit_id = $this->_update_audit( $account_id, $audit_id, $postdata );

			if( !empty( $audit_id ) && !empty( $responses ) ){
				foreach( $responses as $segment => $segement_data ){
					$save_responses = $this->_save_audit_responses( $account_id, $audit_id, $postdata, $segement_data, $target_table = 'audit_responses_sites' );
				}
			}

			## Upload any files if they exist
			if(  !empty( $_FILES['user_files']['name'] ) ) {
				$postdata['audit_id']  = $audit_id;
				$uploaded_docs = $this->document_service->upload_files( $account_id, $postdata, $audit_group );
			}

			## Upload any files inside segments if any
			if(  !empty( $_FILES['audit_files']['name'] ) ) {
				$postdata['audit_id']  = $audit_id;
				$audit_uploaded_docs = $this->document_service->upload_audit_files( $account_id, $postdata, $audit_group );
			}

			## Check if any documents have been uploaded and update the audir record
			if( !empty( $uploaded_docs ) || !empty( $audit_uploaded_docs ) ){
				$postdata['documents_uploaded'] = 1;
			}

			## Check completion status
			$status = $this->check_audit_status( $account_id, $audit_id, $postdata );

			## Do a Quick update to the Evidocs record
			$this->_prep_quick_update( $account_id, $audit_id, $status, $postdata );

			## Get the created record
			$result = $this->get_audits( $account_id, $audit_id );

			if( !empty( $result ) ){

				//Update last audited date
				$this->db->where( [ 'account_id'=>$account_id, 'site_id'=>$site_id ] )
					->update( 'site', ['last_audit_date'=>date( 'Y-m-d H:i:s' ) ] );

				if( !empty( $job_id ) ){
					$this->_quick_job_update( $account_id, $job_id, [ 'linked_evidoc_id'=>$audit_id ] );
				}

				$this->session->set_flashdata('message','Evidocs  details saved successfully');
			}else{
				$this->session->set_flashdata('message','An error occurred while updating your Evidocs  record');
			}
		}

		return $result;

	}

	/* Process person's audit */
	private function _process_people_audit( $account_id = false, $person_id = false, $postdata = false, $responses = false, $audit_group = 'people' ){

		$result = false;

		if( !empty( $account_id ) && !empty( $person_id ) && !empty( $postdata ) ){

			$audit_id = ( !empty( $postdata['audit_id'] ) ) ? $postdata['audit_id'] : null;
			$audit_id = $this->_update_audit( $account_id, $audit_id, $postdata );

			if( !empty( $audit_id ) && !empty( $responses ) ){
				foreach( $responses as $segment => $segement_data ){
					$save_responses = $this->_save_audit_responses( $account_id, $audit_id, $postdata, $segement_data, $target_table = 'audit_responses_people' );
				}
			}

			## Upload any files if they exist
			if(  !empty( $_FILES['user_files']['name'] ) ) {
				$postdata['audit_id']  = $audit_id;
				$uploaded_docs = $this->document_service->upload_files( $account_id, $postdata, $audit_group );
			}

			## Upload any files inside segments if any
			if(  !empty( $_FILES['audit_files']['name'] ) ) {
				$postdata['audit_id']  = $audit_id;
				$audit_uploaded_docs = $this->document_service->upload_audit_files( $account_id, $postdata, $audit_group );
			}

			## Check if any documents have been uploaded and update the audir record
			if( !empty( $uploaded_docs ) || !empty( $audit_uploaded_docs ) ){
				$postdata['documents_uploaded'] = 1;
			}

			## Check completion status
			$status = $this->check_audit_status( $account_id, $audit_id, $postdata );

			## Do a Quick update to the Evidocs record
			$this->_prep_quick_update( $account_id, $audit_id, $status, $postdata );

			## Get the created record
			$result = $this->get_audits( $account_id, $audit_id );

			if( !empty( $result ) ){
				$this->session->set_flashdata('message','Evidocs details saved successfully');
			}else{
				$this->session->set_flashdata('message','An error occurred while updating your Evidocs  record');
			}
		}

		return $result;

	}

	/** Process a Vehicle Evidocs **/
	private function _process_vehicle_audit( $account_id = false, $vehicle_reg = false, $postdata = false, $responses = false, $audit_group = 'fleet' ){
		$result = false;
		if( !empty( $account_id ) && !empty( $vehicle_reg ) && !empty( $postdata ) ){

			$job_id 	= ( !empty( $postdata['job_id'] ) ) ? $postdata['job_id'] : null;
			$audit_id 	= ( !empty( $postdata['audit_id'] ) ) ? $postdata['audit_id'] : null;
			$audit_id 	= $this->_update_audit( $account_id, $audit_id, $postdata );

			if( !empty( $audit_id ) && !empty( $responses ) ){
				foreach( $responses as $segment => $segement_data ){
					$save_responses = $this->_save_audit_responses( $account_id, $audit_id, $postdata, $segement_data, $target_table = 'audit_responses_fleet' );
				}
			}

			## Upload any files if they exist
			if(  !empty($_FILES['user_files']['name']) ) {
				$postdata['audit_id']  = $audit_id;
				$uploaded_docs = $this->document_service->upload_files( $account_id, $postdata, $audit_group );
			}

			## Upload any files inside segments if any
			if(  !empty($_FILES['audit_files']['name']) ) {
				$postdata['audit_id']  = $audit_id;
				$uploaded_docs = $this->document_service->upload_audit_files( $account_id, $postdata, $audit_group );
			}

			## Check if any documents have been uploaded and update the audir record
			if( !empty( $uploaded_docs ) || !empty( $audit_uploaded_docs ) ){
				$postdata['documents_uploaded'] = 1;
			}

			## Check completion status
			$status = $this->check_audit_status( $account_id, $audit_id, $postdata );

			## Do a Quick update to the Evidocs record
			$this->_prep_quick_update( $account_id, $audit_id, $status, $postdata );

			## Get the created record
			$result = $this->get_audits( $account_id, $audit_id );

			if( !empty( $result ) ){

				//Update last audited date
				$this->db->where( [ 'account_id'=>$account_id, 'vehicle_reg'=>$vehicle_reg ] )
					->update( 'fleet_vehicle', [ 'last_audit_date' => date( 'Y-m-d H:i:s' ) ]  );

				if( !empty( $job_id ) ){
					$this->_quick_job_update( $account_id, $job_id, [ 'linked_evidoc_id'=>$audit_id ] );
				}

				$this->session->set_flashdata('message','Evidocs  details saved successfully');
			}else{
				$this->session->set_flashdata('message','An error occurred while updating your Evidocs  record');
			}
		}
		return $result;
	}

	/* Process Job audit */
	private function _process_job_audit( $account_id = false, $job_id = false, $postdata = false, $responses = false, $audit_group = 'job' ){

		$result = false;

		if( !empty( $account_id ) && !empty( $job_id ) && !empty( $postdata ) ){

			$audit_id = ( !empty( $postdata['audit_id'] ) ) ? $postdata['audit_id'] : null;
			$audit_id = $this->_update_audit( $account_id, $audit_id, $postdata );

			if( !empty( $audit_id ) && !empty( $responses ) ){
				foreach( $responses as $segment => $segement_data ){
					$save_responses = $this->_save_audit_responses( $account_id, $audit_id, $postdata, $segement_data, $target_table = 'audit_responses_job' );
				}
			}

			## Upload any files if they exist
			if(  !empty( $_FILES['user_files']['name'] ) ) {
				$postdata['audit_id']  = $audit_id;
				$uploaded_docs = $this->document_service->upload_files( $account_id, $postdata, $audit_group );
			}

			## Upload any files inside segments if any
			if(  !empty( $_FILES['audit_files']['name'] ) ) {
				$postdata['audit_id']  = $audit_id;
				$audit_uploaded_docs = $this->document_service->upload_audit_files( $account_id, $postdata, $audit_group );
			}

			## Check if any documents have been uploaded and update the audir record
			if( !empty( $uploaded_docs ) || !empty( $audit_uploaded_docs ) ){
				$postdata['documents_uploaded'] = 1;
			}

			## Check completion status
			$status = $this->check_audit_status( $account_id, $audit_id, $postdata );

			## Do a Quick update to the Evidocs record
			$this->_prep_quick_update( $account_id, $audit_id, $status, $postdata );

			## Get the created record
			$result = $this->get_audits( $account_id, $audit_id );

			if( !empty( $result ) ){
				$this->_quick_job_update( $account_id, $job_id, [ 'linked_evidoc_id'=>$audit_id ] );
				$this->session->set_flashdata('message','Evidocs details saved successfully');
			}else{
				$this->session->set_flashdata('message','An error occurred while updating your Evidocs  record');
			}
		}

		return $result;

	}

	/* Process customer's audit */
	private function _process_customer_audit( $account_id = false, $customer_id = false, $postdata = false, $responses = false, $audit_group = 'customer' ){

		$result = false;

		if( !empty( $account_id ) && !empty( $customer_id ) && !empty( $postdata ) ){

			$audit_id = ( !empty( $postdata['audit_id'] ) ) ? $postdata['audit_id'] : null;
			$audit_id = $this->_update_audit( $account_id, $audit_id, $postdata );

			if( !empty( $audit_id ) && !empty( $responses ) ){
				foreach( $responses as $segment => $segement_data ){
					$save_responses = $this->_save_audit_responses( $account_id, $audit_id, $postdata, $segement_data, $target_table = 'audit_responses_customer' );
				}
			}

			## Upload any files if they exist
			if(  !empty( $_FILES['user_files']['name'] ) ) {
				$postdata['audit_id']  = $audit_id;
				$uploaded_docs = $this->document_service->upload_files( $account_id, $postdata, $audit_group );
			}

			## Upload any files inside segments if any
			if(  !empty( $_FILES['audit_files']['name'] ) ) {
				$postdata['audit_id']  = $audit_id;
				$audit_uploaded_docs = $this->document_service->upload_audit_files( $account_id, $postdata, $audit_group );
			}

			## Check if any documents have been uploaded and update the audir record
			if( !empty( $uploaded_docs ) || !empty( $audit_uploaded_docs ) ){
				$postdata['documents_uploaded'] = 1;
			}

			## Check completion status
			$status = $this->check_audit_status( $account_id, $audit_id, $postdata );

			## Do a Quick update to the Evidocs record
			$this->_prep_quick_update( $account_id, $audit_id, $status, $postdata );

			## Get the created record
			$result = $this->get_audits( $account_id, $audit_id );

			if( !empty( $result ) ){

				$job_id = !empty( $postdata['job_id'] ) ? $postdata['job_id'] : false;

				if( !empty( $job_id ) ){
					$this->_quick_job_update( $account_id, $job_id, [ 'linked_evidoc_id'=>$audit_id ] );
				}

				$this->session->set_flashdata('message','Evidocs details saved successfully');
			}else{
				$this->session->set_flashdata('message','An error occurred while updating your Evidocs  record');
			}
		}

		return $result;

	}

	/* Process Premises audit */
	private function _process_premises_audit( $account_id = false, $premises_id = false, $postdata = false, $responses = false, $audit_group = 'premises' ){

		$result = false;

		if( !empty( $account_id ) && !empty( $premises_id ) && !empty( $postdata ) ){

			$job_id   = ( !empty( $postdata['job_id'] ) ) ? $postdata['job_id'] : null;
			$audit_id = ( !empty( $postdata['audit_id'] ) ) ? $postdata['audit_id'] : null;
			$audit_id = $this->_update_audit( $account_id, $audit_id, $postdata );

			if( !empty( $audit_id ) && !empty( $responses ) ){
				foreach( $responses as $segment => $segement_data ){
					$save_responses = $this->_save_audit_responses( $account_id, $audit_id, $postdata, $segement_data, $target_table = 'audit_responses_premisess' );
				}
			}

			## Upload any files if they exist
			if(  !empty($_FILES['user_files']['name']) ) {
				$postdata['audit_id']  = $audit_id;
				$uploaded_docs = $this->document_service->upload_files( $account_id, $postdata, $audit_group );
			}

			## Upload any files inside segments if any
			if(  !empty($_FILES['audit_files']['name']) ) {
				$postdata['audit_id']  = $audit_id;
				$audit_uploaded_docs = $this->document_service->upload_audit_files( $account_id, $postdata, $audit_group );
				#return $audit_uploaded_docs;
			}

			## Check if any documents have been uploaded and update the audir record
			if( !empty( $uploaded_docs ) || !empty( $audit_uploaded_docs ) ){
				$postdata['documents_uploaded'] = 1;
			}

			## Check completion status
			$status = $this->check_audit_status( $account_id, $audit_id, $postdata );

			## Do a Quick update to the Evidocs record
			$this->_prep_quick_update( $account_id, $audit_id, $status, $postdata );

			## Get the created record
			$result = $this->get_audits( $account_id, $audit_id );

			if( !empty( $result ) ){

				//Update last audited date
				$this->db->where( [ 'account_id'=>$account_id, 'premises_id'=>$premises_id ] )
					->update( 'premises', [ 'last_audit_date' => date( 'Y-m-d H:i:s' ) ] );

				if( !empty( $job_id ) ){
					$this->_quick_job_update( $account_id, $job_id, [ 'linked_evidoc_id'=>$audit_id, 'premises_id'=>$premises_id ] );
				}

				$this->session->set_flashdata('message','Evidocs  details saved successfully');
			}else{
				$this->session->set_flashdata('message','An error occurred while updating your Evidocs  record');
			}
		}else{
			$this->session->set_flashdata('message','Error! Your request is missing required parameter(s)');
		}
		return $result;
	}
	
	
		/* Process Generic audit */
	private function _process_generic_audit( $account_id = false, $generic_assets = false, $postdata = false, $responses = false, $audit_group = 'generic' ){

		$result = false;

		if( !empty( $account_id ) && !empty( $generic_assets ) && !empty( $postdata ) ){

			$audit_id 		= ( !empty( $postdata['audit_id'] ) ) ? $postdata['audit_id'] : false;
			$generic_assets = ( !empty( $postdata['generic_assets'] ) ) ? $postdata['generic_assets'] : $generic_assets;
			$audit_id 		= $this->_update_audit( $account_id, $audit_id, $postdata );

			## Process linked Assets
			if( !empty( $audit_id ) && !empty( $generic_assets ) ){
				$this->link_audit_generic_assets( $account_id, $audit_id, $generic_assets );
			}

			if( !empty( $audit_id ) && !empty( $responses ) ){
				foreach( $responses as $segment => $segement_data ){
					$save_responses = $this->_save_audit_responses( $account_id, $audit_id, $postdata, $segement_data, $target_table = 'audit_responses_generic' );
				}
			}

			## Upload any files if they exist
			if(  !empty($_FILES['user_files']['name']) ) {
				$postdata['audit_id']  = $audit_id;
				$uploaded_docs = $this->document_service->upload_files( $account_id, $postdata, $audit_group );
			}

			## Upload any files inside segments if any
			if(  !empty($_FILES['audit_files']['name']) ) {
				$postdata['audit_id']  = $audit_id;
				$audit_uploaded_docs = $this->document_service->upload_audit_files( $account_id, $postdata, $audit_group );
				#return $audit_uploaded_docs;
			}

			## Check if any documents have been uploaded and update the audir record
			if( !empty( $uploaded_docs ) || !empty( $audit_uploaded_docs ) ){
				$postdata['documents_uploaded'] = 1;
			}

			## Check completion status
			$status = $this->check_audit_status( $account_id, $audit_id, $postdata );

			## Do a Quick update to the Evidocs record
			$this->_prep_quick_update( $account_id, $audit_id, $status, $postdata );

			## Get the created record
			$result = $this->get_audits( $account_id, $audit_id );

			if( !empty( $result ) ){

				if( !empty( $job_id ) ){
					$this->_quick_job_update( $account_id, $job_id, [ 'linked_evidoc_id'=>$audit_id ] );
				}

				$this->session->set_flashdata('message','Evidocs  details saved successfully');
			}else{
				$this->session->set_flashdata('message','An error occurred while updating your Evidocs  record');
			}
		}else{
			$this->session->set_flashdata('message','Error! Your request is missing required parameter(s)');
		}
		return $result;
	}
	
	/** Update some information against a Job **/
	private function _quick_job_update( $account_id = false, $job_id = false, $data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $job_id ) && !empty( $data ) ){
			$data = $this->ssid_common->_filter_data( 'job', $data );
			$this->db->where( [	'account_id'=>$account_id, 'job_id'=>$job_id ] )
				->update( 'job', $data );
			$result = true;
		}
		return $result;
	}

	/** Update some information against a Job **/
	private function _quick_site_update( $account_id = false, $site_id = false, $data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $site_id ) && !empty( $data ) ){
			$data = $this->ssid_common->_filter_data( 'site', $data );
			$this->db->where( [	'account_id'=>$account_id, 'site_id'=>$site_id ] )
				->update( 'site', $data );
			$result = true;
		}
		return $result;
	}

	/** Create audit  main record **/
	public function _update_audit( $account_id = false, $audit_id = false, $data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $data ) ){

			$audit_id = ( !empty( $audit_id ) ) ? $audit_id : ( !empty( $data['audit_id'] ) ? $data['audit_id'] : null );

			$audit_data 	  = $this->ssid_common->_filter_data( 'audit', $data );

			$client_reference = !empty( $audit_data['audit_reference_client'] ) ? $audit_data['audit_reference_client'] : false;
			
			if( !empty( $audit_id ) ){

				#Check if the Evidocs record actually exists
				$check_exists = $this->db->get_where( 'audit', ['account_id'=>$account_id, 'audit_id'=>$audit_id] )->row();
				if( !empty( $check_exists ) ){
					$update_audit = $audit_data;
					$update_audit['last_modified_by'] 	 = $this->ion_auth->_current_user->id;
					$update_audit['finish_gps_latitude'] = !empty( $data['finish_gps_latitude'] ) ? $data['finish_gps_latitude'] : null;
					$update_audit['finish_gps_longitude']= !empty( $data['finish_gps_longitude'] ) ? $data['finish_gps_longitude'] : null;
					unset( $update_audit['created_by'], $update_audit['date_created'] );
					
					if( isset( $update_audit['audit_status'] ) && ( strtolower( $update_audit['audit_status'] ) == 'completed' ) ){
						$update_audit['finish_time'] = date( 'Y-m-d H:i:s' );
						
						if( empty( $check_exists->completion_time ) ){
							$update_audit['completion_time'] = date( 'Y-m-d H:i:s' );
						} else {
							unset( $update_audit['completion_time'] );
						}
					}
				
					#Update ra record
					$this->db->where( 'account_id', $account_id )
						->where( 'audit_id', $audit_id )
						->update( 'audit', $update_audit );

					$result = ( $this->db->trans_status() ) ? $check_exists->audit_id : false;
				}

			} else {
				
				$audit_reference = $this->_generate_audit_reference( $account_id, $data );
				
				##Client Ref Route
				
				if( !empty( $client_reference ) ){
					
					$check_exists = $this->db->order_by( 'audit_id desc' )
						->limit( 1 )
						->where( ['account_id'=>$account_id, 'audit_reference_client'=>$client_reference] )
						->get( 'audit' )->row();
						
					if( !empty( $check_exists ) ){
						$update_audit = $audit_data;
						unset( $update_audit['created_by'], $update_audit['date_created'] );
						$update_audit['audit_reference'] = !empty( $audit_reference ) ? $audit_reference : $check_exists->audit_reference;

						if( isset( $update_audit['audit_status'] ) && ( strtolower( $update_audit['audit_status'] ) == 'completed' ) ){
							$update_audit['finish_time'] = date( 'Y-m-d H:i:s' );
							
							if( empty( $check_exists->completion_time ) ){
								$update_audit['completion_time'] = date( 'Y-m-d H:i:s' );
							} else {
								unset( $update_audit['completion_time'] );
							}
						}

						$update_audit['audit_id']		  = $check_exists->audit_id;
						$update_audit['last_modified_by'] = $this->ion_auth->_current_user->id;
						$this->db->where( 'account_id', $account_id )
							->where( 'audit_id', $check_exists->audit_id )
							->update( 'audit', $update_audit );

						$result = ( $this->db->trans_status() ) ? $check_exists->audit_id : false;

					} else {
						unset( $audit_data['audit_id'] );
						$audit_data['audit_status']		= 'In Progress';
						$audit_data['audit_reference']	= $audit_reference;
						$audit_data['start_time'] 		= date( 'Y-m-d H:i:s' );
						$audit_data['created_by'] 		= $this->ion_auth->_current_user->id;
						$audit_data['gps_latitude'] 	= !empty( $data['gps_latitude'] ) ? $data['gps_latitude'] : null;
						$audit_data['gps_longitude']	= !empty( $data['gps_longitude'] ) ? $data['gps_longitude'] : null;
						$this->db->insert( 'audit', $audit_data );
						$result = ( $this->db->trans_status() ) ? $this->db->insert_id() : false;
					}
					
				} else {
					
					#Check if this Evidocs record actually exists
					$check_exists = $this->db->order_by( 'audit_id desc' )
						->limit( 1 )
						->like( 'date_created', date('Y-m-d'), 'after' )
						->where( 'audit_status !=', 'Completed' )
						->where( ['account_id'=>$account_id, 'audit_reference'=>$audit_reference] )
						->get( 'audit' )->row();

					if( !empty( $check_exists ) ){
						$update_audit = $audit_data;
						unset( $update_audit['created_by'], $update_audit['date_created'] );

						if( isset( $update_audit['audit_status'] ) && strtolower( $update_audit['audit_status'] ) == 'completed' ){
							$update_audit['finish_time'] = date( 'Y-m-d H:i:s' );
						
							if( empty( $check_exists->completion_time ) ){
								$update_audit['completion_time'] = date( 'Y-m-d H:i:s' );
							} else {
								unset( $update_audit['completion_time'] );
							}
							
						}

						$update_audit['audit_id']		  = $check_exists->audit_id;
						$update_audit['last_modified_by'] = $this->ion_auth->_current_user->id;
						$this->db->where( 'account_id', $account_id )
							->where( 'audit_id', $check_exists->audit_id )
							->update( 'audit', $update_audit );

						$result = ( $this->db->trans_status() ) ? $check_exists->audit_id : false;

					} else {
						unset( $audit_data['audit_id'] );
						$audit_data['audit_status']		= 'In Progress';
						$audit_data['audit_reference']	= $audit_reference;
						$audit_data['start_time'] 		= date( 'Y-m-d H:i:s' );
						$audit_data['created_by'] 		= $this->ion_auth->_current_user->id;
						$audit_data['gps_latitude'] 	= !empty( $data['gps_latitude'] ) ? $data['gps_latitude'] : null;
						$audit_data['gps_longitude']	= !empty( $data['gps_longitude'] ) ? $data['gps_longitude'] : null;

						$this->db->insert( 'audit', $audit_data );
						$result = ( $this->db->trans_status() ) ? $this->db->insert_id() : false;
					}
				}
				
			}

			//Process result status
			if( !empty( $audit_data['audit_result_status_id'] ) ){
				$data['audit_id'] = $result;
				$result_processed = $this->process_result_status( $account_id, $data );
			}

		}else{
			$this->session->set_flashdata( 'message','An error occurred while updating your Evidocs record' );
		}
		return $result;
	}

	/* Process audit  responses */
	private function _save_audit_responses( $account_id = false, $audit_id = false, $data = false, $responses = false, $target_table = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $audit_id ) && !empty( $data ) && !empty( $target_table ) ){
			if( !empty($responses) ){

				$resp_data		= [];
				$defects_data	= [];

				foreach( $responses as $k=>$row ){

					if( !empty( $row['response'] ) ){

						//Check for pipped string from Android
						if( stripos( $row['response'], '|' ) !== false ){
							$row['response'] = array_map( "trim", explode( "|", $row['response'] ) );
						}

						if( is_array( $row['response'] ) ){
							$row['response'] = json_encode( $row['response'] );
						}

						## Check for Defects / 
						/* if( !empty( $row['response_defects_details'] ) && ( strtolower( $row['raise_reactive_job'] ) == 'yes' ) ){
							$defects_data[] = [
								'account_id' 		=> $account_id,
								'discipline_id' 	=> !empty( $data['discipline_id'] ) ? $data['discipline_id'] : null,
								'source_data' 		=> json_encode( $row ),
								'item_has_defects' 	=> $row['response_has_defects'],
								'works_required' 	=> $row['response_defects_details'],
								'source_job_id' 	=> !empty( $data['job_id'] ) 		? $data['job_id'] : null,
								'source_evidoc_id' 	=> $audit_id,
								'site_id' 			=> !empty( $data['site_id'] ) 		? $data['site_id'] : null,
								'asset_id' 			=> !empty( $data['asset_id'] ) 		? $data['asset_id'] : null,
								'customer_id' 		=> !empty( $data['customer_id'] ) 	? $data['customer_id'] : null,
								'address_id' 		=> !empty( $data['address_id'] ) 	? $data['address_id'] : null,
							];
						} */

						$new_row 			 	 = $this->ssid_common->_filter_data( $target_table, $row );
						$new_row['created_by']	 = $this->ion_auth->_current_user->id;
						$new_row['audit_id']	 = $audit_id;
						$resp_data[$k] 		 	 = $new_row;
					}
				}

				## Insert responses
				if( !empty( $resp_data ) ){

					$conditions = ['audit_id'=>$audit_id];
					$this->db->where_in( 'question_id', array_column( $resp_data, 'question_id' ) )
						->where( $conditions )->delete( $target_table );

					#$this->ssid_common->_reset_auto_increment( $target_table, 'id' );

					$this->db->insert_batch( $target_table, $resp_data );
					
					## Raise Tasks / Defects
					#Processs $defects_data - @todo;
					
					
				}
				$result = ( $this->db->trans_status() !== false ) ? true : false;
			}
		}
		return $result;
	}

	/*
	* Get list of Evidocs Questions for a specific Evidocs type
	*/
	public function get_audit_questions( $account_id = false, $audit_type_id = false, $asset_type_id= false, $section_ref = false, $segment = false, $segmented = false, $un_grouped = false, $sectioned = false, $question_id = false ){
		$result = false;
		if( !empty( $account_id ) ){

			$this->db->select('qb.*, audit_types.audit_type',false)
				->join( 'audit_types', 'audit_types.audit_type_id = qb.audit_type_id', 'left' )
				->where( 'qb.account_id',$account_id );

			if( $audit_type_id ){
				$this->db->where( 'qb.audit_type_id', $audit_type_id );
			}

			if( !empty( $question_id ) ){
				$row = $this->db->where( 'qb.question_id', $question_id )
					->get( 'audit_question_bank qb' )
					->row();

				if( !empty( $row ) ){
					$row->response_options = ( !empty( $row->response_options ) ) ? json_decode( $row->response_options ) : null;
					$row->terms_conditions = ( !empty( $row->terms_conditions ) ) ? json_decode( $row->terms_conditions ) : null;
					$row->file_types 	   = ( !empty( $row->file_types ) ) ? json_decode( $row->file_types ) : null;
					return $row;
				}
				return $row;
			} else {

				if( $asset_type_id ){
					$this->db->where( 'qb.asset_type_id',$asset_type_id );
				}

				if( $section_ref ){
					$this->db->where( 'qb.section_ref',$section_ref );
				}

				if( $segment ){
					$this->db->where( 'qb.segment',$segment );
				}

				#$query = $this->db->order_by('qb.section_ref, LENGTH(qb.ordering) asc, qb.ordering asc')
				$query = $this->db->order_by('LENGTH(qb.ordering) asc, qb.ordering asc, qb.section_ref')
					->where('qb.is_active',1)
					->get( 'audit_question_bank qb' );


				if( $query->num_rows() > 0 ){

					if( $section_ref || $un_grouped ){
						foreach( $query->result() as $k=>$row ){
							$row->response_options = ( !empty( $row->response_options ) ) ? json_decode( $row->response_options ) : null;
							$row->terms_conditions = ( !empty( $row->terms_conditions ) ) ? json_decode( $row->terms_conditions ) : null;
							$row->file_types 	   = ( !empty( $row->file_types ) ) ? json_decode( $row->file_types ) : null;
							$result[$k] 		   = $row;
						}
					}else if( !empty( $sectioned ) ) {
						foreach( $query->result() as $k=>$row ){
							$section = ( !empty( $row->section ) ) ? strtolower( trim( $row->section ) ) : '_Unknown Section';
							$row->response_options = ( !empty( $row->response_options ) ) ? json_decode( $row->response_options ) : null;
							$row->terms_conditions = ( !empty( $row->terms_conditions ) ) ? json_decode( $row->terms_conditions ) : null;
							$row->file_types 	   = ( !empty( $row->file_types ) ) ? json_decode( $row->file_types ) : null;
							$result[$section][$k] 	= $row;
						}
					}else{
						foreach( $query->result() as $row ){
							$segment = ( !empty( $row->segment ) ) ? $row->segment : '_Unknown Segment';
							if( $segmented ){
								$result[$segment][] = $row->question_id;
							}else{
								$row->response_options = ( !empty( $row->response_options ) ) ? json_decode( $row->response_options ) : null;
								$row->terms_conditions = ( !empty( $row->terms_conditions ) ) ? json_decode( $row->terms_conditions ) : null;
								$row->file_types 	   = ( !empty( $row->file_types ) ) ? json_decode( $row->file_types ) : null;
								$result[$segment][$row->section][] = $row;
							}
						}
					}
					$this->session->set_flashdata('message','Evidocs questions found');
				}else{
					$this->session->set_flashdata('message','No records found');
				}
			}

		}
		return $result;
	}

	/*
	* Get list of Evidocs responses
	*/
	private function get_audit_responses( $audit_id = false, $source_table = false, $segmented = false, $section = false, $where = false ){
		$result = null;
		if( !empty( $audit_id ) && !empty( $source_table ) ){

			$where = convert_to_array( $where );

			$this->db->select( 'audit_responses.*',false )
				->join( 'audit', 'audit.audit_id = audit_responses.audit_id', 'left' );

			if( $audit_id ){
				$this->db->where( 'audit_responses.audit_id', $audit_id );
			}

			if( $section ){
				$this->db->where( 'audit_responses.section', $section );
			}

			$query = $this->db->order_by( 'LENGTH(audit_responses.ordering) asc, audit_responses.ordering asc' )
				->order_by('audit_responses.segment, audit_responses.section, audit_responses.question_id')
				->get( $source_table.' as audit_responses');

			if( $query->num_rows() > 0 ){
				foreach( $query->result() as $k => $row ){
					$row->response = ( isValidJson( $row->response ) ) ? json_decode( $row->response ) : $row->response;
					$row->response = ( is_array( $row->response ) ? implode( " | ", $row->response  ) : $row->response );
					$row->response = ( is_int( $row->response ) ? ( string ) $row->response : $row->response );
					if( !empty( $where['sectioned'] ) ){
						$result[$row->section][] = $row;
					} else if( $segmented ){
						$result[$row->segment][] = $row->id;
					}else{
						$result[$k] = $row;
					}
				}
				$this->session->set_flashdata( 'message','Evidocs  responses found' );
			}else{
				$this->session->set_flashdata( 'message','No records found' );
			}
		}
		return $result;
	}

	/** Check Evidocs completion status **/
	public function check_audit_status( $account_id = false, $audit_id = false, $postdata = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $audit_id ) ){
			$this->db->select( 'audit.audit_id, audit.audit_type_id, questions_completed, documents_uploaded, signature_uploaded, audit_status, audit_types.audit_group,asset_id,site_id,vehicle_reg', false )
				->join( 'audit_types', 'audit_types.audit_type_id = audit.audit_type_id', 'left' )
				->where( 'audit.account_id', $account_id );

			if( $audit_id ){
				$this->db->where( 'audit_id', $audit_id );
			}

			$query = $this->db->get( 'audit' );

			if( $query->num_rows() > 0 ){
				$row 	= $query->result()[0];
				$result = (object)[
					'audit_id'=>$audit_id,
					'questions_completed'=>$row->questions_completed,
					'documents_uploaded'=>( !empty( $postdata['documents_uploaded'] ) ) ? $postdata['documents_uploaded'] : $row->documents_uploaded,
					'signature_uploaded'=>$row->signature_uploaded,
					'audit_status'=>$row->audit_status
				];
				$audit_group = strtolower( $row->audit_group );
			}else{
				$result = (object)[
					'audit_id'=>$audit_id,
					'questions_completed'=>0,
					'documents_uploaded'=>0,
					'signature_uploaded'=>0,
					'audit_status'=>null
				];

				$audit_type = $this->get_audit_types( $account_id, false, false, $postdata['audit_type_id'] );
				$audit_type = ( !empty( $audit_type->records ) ) ? $audit_type->records : ( !empty( $audit_type ) ? $audit_type : false );
				$audit_group= strtolower( $audit_type->audit_group );
			}

			## Determine source table

			$asset_type_id	= false;

			switch( $audit_group ){
				## All asset type audits
				case ( in_array( $audit_group, ['asset'] ) ):
					$get_asset 		= $this->db->get_where( 'asset', ['asset_id'=>$row->asset_id ] )->row();
					$asset_type_id	= ( !empty( $get_asset->asset_type_id ) ) ? $get_asset->asset_type_id : false;

					$source_table 	= 'audit_responses_assets';
					break;

				## All site type audits
				case ( in_array( $audit_group, ['site'] ) ):
					$source_table = 'audit_responses_sites';
					break;

				## All people type audits
				case ( in_array( $audit_group, ['people','person'] ) ):
					$source_table = 'audit_responses_people';
					break;

				## All vehicle-type audits
				case ( in_array( $audit_group, ['vehicle','fleet'] ) ):
					$source_table = 'audit_responses_fleet';
					break;

				## All Job audits
				case ( in_array( $audit_group, ['job'] ) ):
					$source_table = 'audit_responses_job';
					break;

				## All Customer audits
				case ( in_array( $audit_group, ['customer'] ) ):
					$source_table = 'audit_responses_customer';
					break;
					
				case ( in_array( $audit_group, ['premises'] ) ):
					$source_table = 'audit_responses_premises';
					break;
					
				case ( in_array( $audit_group, ['generic'] ) ):
					$source_table = 'audit_responses_generic';
					break;
			}

			$audit_questions = $this->get_audit_questions( $account_id, $postdata['audit_type_id'], $asset_type_id, false, true );

			$audit_response  = $this->get_audit_responses( $audit_id, $source_table, true );

			$result->expected_questions  = ( !empty( $audit_questions['questions'] ) ) ? count( $audit_questions['questions'] ) : 0;
			$result->expected_documents  = ( !empty( $audit_questions['documents'] ) ) ? count( $audit_questions['documents'] ) : 0;
			$result->expected_signatures = ( !empty( $audit_questions['signature'] ) ) ? count( $audit_questions['signature'] ) : 0;

			$result->completed_questions = ( !empty( $audit_response['questions'] ) ) ? count( $audit_response['questions'] ) : 0;
			$result->completed_documents = ( !empty( $audit_response['documents'] ) ) ? count( $audit_response['documents'] ) : 0;
			$result->completed_signatures= ( !empty( $audit_response['signature'] ) ) ? count( $audit_response['signature'] ) : 0;
		}
		return $result;
	}

	//Prepare some date for quick update
	private function _prep_quick_update( $account_id = false, $audit_id = false, $status_data = false, $postdata = false ){
		$result = false;

		if( !empty( $account_id ) && !empty( $status_data ) && !empty( $postdata ) ){
			$quick_data = [];
			if( ( $status_data->questions_completed != 1 ) && ( $status_data->expected_questions == $status_data->completed_questions ) ){
				$quick_data['questions_completed'] = 1;
			}

			if( ( $status_data->documents_uploaded != 1 ) && ( $status_data->expected_documents == $status_data->completed_documents ) ){
				$quick_data['documents_uploaded'] = 1;
			}

			if( ( $status_data->signature_uploaded != 1 ) && ( $status_data->expected_signatures == $status_data->completed_signatures ) ){
				$quick_data['signature_uploaded'] = 1;
			}

			if( ( $status_data->questions_completed == 1 ) && ( $status_data->documents_uploaded == 1 ) && ( $status_data->signature_uploaded == 1 ) ){
				$quick_data['finish_time']  		= date( 'Y-m-d H:i:s' );
				#$quick_data['audit_status'] 		= 'Completed';
				$quick_data['finish_gps_latitude']  = !empty( $postdata['gps_latitude'] ) ? $postdata['gps_latitude'] : null;
				$quick_data['finish_gps_longitude'] = !empty( $postdata['gps_logitude'] ) ? $postdata['gps_logitude'] : null;
			}

			## Check if documents have been updated
			if( !empty( $postdata['documents_uploaded'] ) && ( $postdata['documents_uploaded'] == 1 ) ){
				$quick_data['documents_uploaded'] = 1;
			}

			$questions_completed= ( !empty($quick_data['questions_completed']) && $quick_data['questions_completed'] == 1 ) ? 1 : ( ( $status_data->questions_completed == 1 ) ? 1 : 0 );
			$documents_uploaded = ( !empty($quick_data['documents_uploaded']) && $quick_data['documents_uploaded'] == 1 ) ? 1 : ( ( $status_data->documents_uploaded == 1 ) ? 1 : 0 );
			$signature_uploaded = ( !empty($quick_data['signature_uploaded']) && $quick_data['signature_uploaded'] == 1 ) ? 1 : ( ( $status_data->signature_uploaded == 1 ) ? 1 : 0 );

			//Update Evidocs status
			if( $questions_completed && $documents_uploaded && $signature_uploaded ){
				$quick_data['finish_time']  		= date( 'Y-m-d H:i:s' );
				#$quick_data['audit_status'] 		= 'Completed';
				$quick_data['finish_gps_latitude']  = !empty( $postdata['gps_latitude'] ) ? $postdata['gps_latitude'] : null;
				$quick_data['finish_gps_longitude'] = !empty( $postdata['gps_logitude'] ) ? $postdata['gps_logitude'] : null;
			}

			if( !empty( $quick_data ) ){
				$prev_audit_data = $this->get_audits( $account_id, $audit_id );
				unset( $prev_audit_data->audit_responses );
				unset( $prev_audit_data->uploaded_docs );

				if( !empty( $quick_data['audit_status'] ) && ( strtolower( $quick_data['audit_status'] ) == 'completed' ) && ( ( !empty( $prev_audit_data->audit_status ) ) && ( strtolower( $prev_audit_data->audit_status ) != 'completed' ) ) ){

					$vehicle_history_log_data = [
						"log_type"			=> "audits",
						"entry_id"			=> $audit_id,
						"vehicle_id"		=> ( !empty( $prev_audit_data->vehicle_id ) ) ? $prev_audit_data->vehicle_id : NULL,
						"vehicle_reg"		=> ( !empty( $prev_audit_data->vehicle_reg ) ) ? $prev_audit_data->vehicle_reg : NULL,
						"action"			=> "create vehicle audit",
						"note"				=> NULL,
					];
					$vehicle_history_log_data['previous_values'] 	= json_encode( $prev_audit_data ); ##??
					$vehicle_history_log_data['current_values'] 	= json_encode( $quick_data );

					## create vehicle history log
					$this->load->model( "Fleet_model" );
					$vehicle_history_log = $this->Fleet_model->create_vehicle_change_log( $account_id, $vehicle_history_log_data );
				}

				$this->quick_audit_update( $account_id, $audit_id, $quick_data );
			}
		}
		return $result;
	}

	/** Quick udpdate to the risk assessment record **/
	public function quick_audit_update( $account_id, $audit_id, $data ){
		$result = false;
		if( $account_id && $audit_id && $data ){

			$data = $this->ssid_common->_filter_data( 'audit', $data );
			$data['last_modified_by'] = $this->ion_auth->_current_user->id;
			$this->db->where('account_id',$account_id)
				->where('audit_id',$audit_id)
				->update('audit', $data);

			if( $this->db->trans_status() !== false ){
				$result = true;
			}
		}
		return $result;
	}

	/** Generate A unique Evidocs reference using audit-type-id an date + time hours and minutes **/
	private function _generate_audit_reference( $account_id = false, $data = false ){

		if( !empty( $account_id ) && !empty( $data ) ){
			$audit_reference  = 'REF'.$data['audit_type_id'].'.'.date('Ymd.H.i').'.'.$this->ion_auth->_current_user->id;
			$audit_reference .= ( !empty( $data['contract_id'] ) ) 	? $data['contract_id'] 	: '';
			$audit_reference .= ( !empty( $data['site_id'] ) ) 		? $data['site_id'] 		: '';
			$audit_reference .= ( !empty( $data['asset_id'] ) ) 	? $data['asset_id'] 	: '';
			$audit_reference .= ( !empty( $data['job_id'] ) ) 		? $data['job_id'] 		: '';
			return $audit_reference;
		}else{
			return false;
		}
	}

	/*
	* Search through audits
	*/
	public function audit_lookup( $account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;
		if( !empty( $account_id ) ){
			
			$where = $raw_where = convert_to_array( $where );
			
			#Limit access by contract to External User Types
			if( in_array( $this->ion_auth->_current_user()->user_type_id, EXTERNAL_USER_TYPES ) ){
				$contract_access 	= $this->contract_service->get_linked_people( $account_id, false, $this->ion_auth->_current_user->id, ['as_arraay'=>1] );
				$allowed_contracts  = !empty( $contract_access ) ? array_column( $contract_access, 'contract_id' ) : [];
				if( !empty( $allowed_contracts ) ){					
					$raw_where['allowed_contracts'] = $allowed_contracts;
					$this->db->where_in( 'audit_types.contract_id', $allowed_contracts );
				} else{
					$this->session->set_flashdata( 'message','No data found matching your criteria' );
					return false;
				}
			}			
			
			$this->db->select('audit.*, fleet_vehicle.vehicle_id, fleet_vehicle.next_audit_date `fleet_vehicle_next_audit_date`, concat(user.first_name," ",user.last_name) `created_by`, concat(modifier.first_name," ",modifier.last_name) `last_modified_by`, audit_types.audit_type, audit_types.alt_audit_type, asset.asset_id, asset.asset_unique_id, asset.next_audit_date `asset_next_audit_date`, asset.end_of_life_date, site.site_id, site.site_name, site.next_audit_date `site_next_audit_date`, audit_result_statuses.*, , concat(person.first_name," ",person.last_name) `audited_person`', false)
				->join('audit_types', 'audit_types.audit_type_id = audit.audit_type_id', 'left')
				->join('asset', 'asset.asset_id = audit.asset_id', 'left')
				->join('user', 'user.id = audit.created_by', 'left')
				->join('user modifier','modifier.id = audit.last_modified_by','left')
				->join('user person','person.id = audit.person_id','left')
				->join('fleet_vehicle', 'fleet_vehicle.vehicle_reg = audit.vehicle_reg', 'left')
				->join('site', 'site.site_id = audit.site_id', 'left')
				->join( 'audit_result_statuses','audit_result_statuses.audit_result_status_id = audit.audit_result_status_id','left' )
				->where('audit.account_id',$account_id)
				->where('audit.archived !=',1);

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

						if( !empty($search_where['audit.audit_type_id']) ){
							$search_where['audit_types.audit_type'] =  trim( $term );
							unset($search_where['audit.audit_type_id']);
						}

						if( !empty($search_where['audit.created_by']) ){
							$search_where['user.first_name'] =  trim( $term );
							$search_where['user.last_name']  =  trim( $term );
							unset($search_where['audit.created_by']);
						}

						if( !empty( $search_where['audit.asset_id'] ) || !empty( $search_where['audit.asset_unique_id'] ) ){
							$search_where['asset.asset_id'] 		=  trim( $term );
							$search_where['asset.asset_unique_id']	=  trim( $term );
							unset( $search_where['audit.asset_unique_id'] );
						}
						
						if( !empty( $search_where['audit.site_id'] ) ){
							$search_where['site.site_reference']	=  trim( $term );
							$search_where['site.site_name']			=  trim( $term );
							unset( $search_where['audit.site_id'] );
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				}else{
					foreach( $this->searchable_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					if( !empty($search_where['audit.audit_type_id']) ){
						$search_where['audit_types.audit_type'] =  $search_term;
						unset($search_where['audit.audit_type_id']);
					}

					if( !empty($search_where['audit.created_by']) ){
						$search_where['user.first_name'] =  $search_term;
						$search_where['user.last_name']  =  $search_term;
						unset($search_where['audit.created_by']);
					}

					if( !empty( $search_where['audit.asset_id'] ) || !empty( $search_where['audit.asset_unique_id'] ) ){
						$search_where['asset.asset_id'] 		=  $search_term;
						$search_where['asset.asset_unique_id']	=  $search_term;
						unset( $search_where['audit.asset_unique_id'] );
					}

					if( !empty( $search_where['audit.site_id'] ) ){
						$search_where['site.site_reference']	=  trim( $search_term );
						$search_where['site.site_name']			=  trim( $search_term );
						unset( $search_where['audit.site_id'] );
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( !empty( $where ) ){

				if( isset( $where['audit_types'] ) ){
					if( !empty( $where['audit_types'] ) ){
						$where['audit_types'] = ( is_array( $where['audit_types'] ) ) ? $where['audit_types'] : ( is_string( $where['audit_types'] ) ? str_to_array( $where['audit_types'] ) : $where['audit_types'] );
						$this->db->where_in( 'audit.audit_type_id', $where['audit_types'] );
					}
					unset( $where['audit_types'] );
				}

				if( isset( $where['audit_statuses'] ) ){
					$where['audit_statuses'] = ( is_array( $where['audit_statuses'] ) ) ? $where['audit_statuses'] : ( is_string( $where['audit_statuses'] ) ? str_to_array( $where['audit_statuses'] ) : $where['audit_statuses'] );
					if( !empty( $where['audit_statuses'] ) && ( !in_array( 'all',  $where['audit_statuses'] ) )){
						$this->db->where_in( 'audit.audit_status', $where['audit_statuses'] );
					}
					unset( $where['audit_statuses'] );
				}

				if( isset( $where['result_statuses'] ) ){
					$where['result_statuses'] = ( is_array( $where['result_statuses'] ) ) ? $where['result_statuses'] : ( is_string( $where['result_statuses'] ) ? str_to_array( $where['result_statuses'] ) : $where['result_statuses'] );
					if( !empty( $where['result_statuses'] ) ){
						$this->db->where_in( 'audit.audit_result_status_id', $where['result_statuses'] );
					}
					unset( $where['result_statuses'] );
				}

				if( isset( $where['eol_dates'] ) ){
					if( !empty( $where['eol_dates'] ) ){
						$eol_dates = $where['eol_dates'];

 						if( !empty( $eol_dates['eol_date_from'] ) ){
							$this->db->where( 'asset.end_of_life_date >=', format_date_db( $eol_dates['eol_date_from'] ) );
						}

						if( !empty( $eol_dates['eol_date_to'] ) ){
							$this->db->where( 'asset.end_of_life_date <=', format_date_db( $eol_dates['eol_date_to'] ) );
						}
					}
					unset( $where['eol_dates'] );
				}

				if( isset( $where['next_audit_dates'] ) ){
					if( !empty( $where['next_audit_dates'] ) ){
						$next_audit_dates = $where['next_audit_dates'];
						if( !empty( $next_audit_dates['next_audit_date_from'] ) ){
							$nadf_where = "( ( asset.next_audit_date IS NULL OR asset.next_audit_date >= '".( format_date_db( $next_audit_dates['next_audit_date_from'] ) )."' ) AND ";
							$nadf_where .= "( site.next_audit_date IS NULL OR site.next_audit_date >= '".( format_date_db( $next_audit_dates['next_audit_date_from'] ) )."' ) AND ";
							$nadf_where .= "( fleet_vehicle.next_audit_date IS NULL || fleet_vehicle.next_audit_date >= '".( format_date_db( $next_audit_dates['next_audit_date_from'] ) )."' ) )";
							$this->db->where( $nadf_where );
						}

						if( !empty( $next_audit_dates['next_audit_date_to'] ) ){
							$nadt_where = "( ( asset.next_audit_date IS NULL OR asset.next_audit_date <= '".( format_date_db( $next_audit_dates['next_audit_date_to'] ) )."' ) AND ";
							$nadt_where .= "( site.next_audit_date IS NULL OR site.next_audit_date <= '".( format_date_db( $next_audit_dates['next_audit_date_to'] ) )."' ) AND ";
							$nadt_where .= "( fleet_vehicle.next_audit_date IS NULL || fleet_vehicle.next_audit_date <= '".( format_date_db( $next_audit_dates['next_audit_date_to'] ) )."' ) )";
							$this->db->where( $nadt_where );
						}
					}
					unset( $where['next_audit_dates'] );
				}

				if( isset( $where['asset_id'] ) ){
					if( !empty( $where['asset_id'] ) ){
						$this->db->where( 'audit.asset_id', $where['asset_id'] );
					}
					unset( $where['asset_id'] );
				}

				if( isset( $where['site_id'] ) ){
					if( !empty( $where['site_id'] ) ){
						$this->db->where( 'audit.site_id', $where['site_id'] );
					}
					unset( $where['site_id'] );
				}

				if( isset( $where['person_id'] ) ){
					if( !empty( $where['person_id'] ) ){
						$this->db->where( 'audit.person_id', $where['person_id'] );
					}
					unset( $where['person_id'] );
				}

				if( isset( $where['customer_id'] ) ){
					if( !empty( $where['customer_id'] ) ){
						$this->db->where( 'audit.customer_id', $where['customer_id'] );
					}
					unset( $where['customer_id'] );
				}

				if( isset( $where['vehicle_reg'] ) ){
					if( !empty( $where['vehicle_reg'] ) ){
						$this->db->where( 'audit.vehicle_reg', $where['vehicle_reg'] );
					}
					unset( $where['vehicle_reg'] );
				}
				
				if( isset( $where['contract_id'] ) ){
					if( !empty( $where['contract_id'] ) ){
						$this->db->where( 'audit_types.contract_id', $where['contract_id'] );

						$date_from = !empty( $where['date_from'] )	? date( 'Y-m-d', strtotime( $where['date_from'] ) ) : date( 'Y-m-d', strtotime( 'Jan 01' ) );
						unset( $where['date_from'] );
						$date_to = !empty( $where['date_to'] )		? date( 'Y-m-d', strtotime( $where['date_to'] ) ) 	: date( 'Y-m-d', strtotime( 'Dec 31' ) );
						unset( $where['date_to'] );
						$this->db->where( 'audit.date_created >=', $date_from );
						$this->db->where( 'audit.date_created <=', $date_to );
					}
					unset( $where['contract_id'] );
				}

				if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}

			if( $order_by ){
				$order = $this->ssid_common->_clean_order_by( $order_by, $this->primary_tbl );
			if( !empty( $order ) ){ $this->db->order_by( $order ); }
			}else{
				//$this->db->order_by( 'ISNULL( audit_result_statuses.result_ordering ), audit_result_statuses.result_ordering ASC, audit.audit_result_status_id ASC' );
				$this->db->order_by( 'audit_id DESC' );
			}

			if( $limit > 0 ){
				$this->db->limit( $limit, $offset );
			}

			$query = $this->db->get( 'audit' );

			if( $query->num_rows() > 0 ){
				$result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
				$result->records 			= $query->result();
				$counters 					= $this->get_total_audits( $account_id, $search_term, $raw_where, false, $limit );
				$result->counters->total 	= ( !empty( $counters->total ) ) ? $counters->total : null;
				$result->counters->pages 	= ( !empty( $counters->pages ) ) ? $counters->pages : null;
				$result->counters->limit  	= ( !empty( $limit ) ) 			 ? $limit : $result->counters->total;
				$result->counters->offset 	= $offset;
				$this->session->set_flashdata('message','Records found.');
			} else {
				$this->session->set_flashdata('message','No records found matching your criteria.');
			}
		}

		return $result;
	}

	/*
	* Get total audit count for the search
	*/
	public function get_total_audits( $account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = 0 ){
		$result = false;
		if( !empty( $account_id ) ){

			if( !empty( $where ) ){

				$where = convert_to_array( $where );

			}

			if( !empty( $where['asset_evidocs'] ) ){
				if( !empty( $where['site_id'] ) ){
					$get_audited_assets = $this->db->select( 'asset_id' )->get_where( 'asset', ['account_id'=>$account_id, 'site_id'=>$where['site_id']] );
					if( $get_audited_assets->num_rows() ){
						$asset_ids = array_column( $get_audited_assets->result_array(), 'asset_id' );
						if( !empty( $asset_ids ) ){
							$this->db->where_in( 'audit.asset_id',$asset_ids );
						}
					}
				}
				unset( $where['asset_evidocs'], $where['site_id'] );
			} else {

				if( isset( $where['site_id'] ) ){
					if( !empty( $where['site_id'] ) ){
						$this->db->where( 'audit.site_id', $where['site_id'] );
					}
					unset( $where['site_id'] );
				}

			}
			
			if( isset( $where['allowed_contracts'] ) ){
				if( !empty( $where['allowed_contracts'] ) ){
					$allowed_contracts = is_array( $where['allowed_contracts'] ) ? $where['allowed_contracts'] : [ $where['allowed_contracts'] ];
					$this->db->where_in( 'audit_types.contract_id', $allowed_contracts );
				}
				unset( $where['allowed_contracts'] );
			}

			$this->db->select('audit.audit_id',false)
				->join( 'audit_types', 'audit_types.audit_type_id = audit.audit_type_id', 'left' )
				->join( 'asset', 'asset.asset_id = audit.asset_id', 'left' )
				->join( 'user', 'user.id = audit.created_by', 'left' )
				->join( 'fleet_vehicle', 'fleet_vehicle.vehicle_reg = audit.vehicle_reg', 'left' )
				->join( 'site', 'site.site_id = audit.site_id', 'left' )
				->join( 'audit_result_statuses','audit_result_statuses.audit_result_status_id = audit.audit_result_status_id','left' )
				->where( 'audit.account_id',$account_id )
				->where( 'audit.archived !=',1 );

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

						if( !empty($search_where['audit.audit_type_id']) ){
							$search_where['audit_types.audit_type'] =  trim( $term );
							unset($search_where['audit.audit_type_id']);
						}

						if( !empty($search_where['audit.created_by']) ){
							$search_where['user.first_name'] =  trim( $term );
							$search_where['user.last_name']  =  trim( $term );
							unset($search_where['audit.created_by']);
						}

						if( !empty( $search_where['audit.asset_id'] ) || !empty( $search_where['audit.asset_unique_id'] ) ){
							$search_where['asset.asset_id'] 		=  trim( $term );
							$search_where['asset.asset_unique_id']	=  trim( $term );
							unset( $search_where['audit.asset_unique_id'] );
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				}else{
					foreach( $this->searchable_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					if( !empty($search_where['audit.audit_type_id']) ){
						$search_where['audit_types.audit_type'] =  $search_term;
						unset($search_where['audit.audit_type_id']);
					}

					if( !empty($search_where['audit.created_by']) ){
						$search_where['user.first_name'] =  $search_term;
						$search_where['user.last_name']  =  $search_term;
						unset($search_where['audit.created_by']);
					}

					if( !empty( $search_where['audit.asset_id'] ) || !empty( $search_where['audit.asset_unique_id'] ) ){
						$search_where['asset.asset_id'] 		=  $search_term;
						$search_where['asset.asset_unique_id']	=  $search_term;
						unset( $search_where['audit.asset_unique_id'] );
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}



			if( isset( $where['audit_types'] ) ){
				if( !empty( $where['audit_types'] ) ){
					$where['audit_types'] = ( is_array( $where['audit_types'] ) ) ? $where['audit_types'] : ( is_string( $where['audit_types'] ) ? str_to_array( $where['audit_types'] ) : $where['audit_types'] );
					$this->db->where_in( 'audit.audit_type_id', $where['audit_types'] );
				}
				unset( $where['audit_types'] );
			}

			if( isset( $where['audit_statuses'] ) ){
				$where['audit_statuses'] = ( is_array( $where['audit_statuses'] ) ) ? $where['audit_statuses'] : ( is_string( $where['audit_statuses'] ) ? str_to_array( $where['audit_statuses'] ) : $where['audit_statuses'] );
				if( !empty( $where['audit_statuses'] ) && ( !in_array( 'all',  $where['audit_statuses'] ) )){
					$this->db->where_in( 'audit.audit_status', $where['audit_statuses'] );
				}
				unset( $where['audit_statuses'] );
			}

			if( isset( $where['result_statuses'] ) ){
				$where['result_statuses'] = ( is_array( $where['result_statuses'] ) ) ? $where['result_statuses'] : ( is_string( $where['result_statuses'] ) ? str_to_array( $where['result_statuses'] ) : $where['result_statuses'] );
				if( !empty( $where['result_statuses'] ) ){
					$this->db->where_in( 'audit.audit_result_status_id', $where['result_statuses'] );
				}
				unset( $where['result_statuses'] );
			}

			if( isset( $where['eol_dates'] ) ){
				if( !empty( $where['eol_dates'] ) ){
					$eol_dates = $where['eol_dates'];

					if( !empty( $eol_dates['eol_date_from'] ) ){
						$this->db->where( 'asset.end_of_life_date >=', format_date_db( $eol_dates['eol_date_from'] ) );
					}

					if( !empty( $eol_dates['eol_date_to'] ) ){
						$this->db->where( 'asset.end_of_life_date <=', format_date_db( $eol_dates['eol_date_to'] ) );
					}
				}
				unset( $where['eol_dates'] );
			}

			if( isset( $where['next_audit_dates'] ) ){
				if( !empty( $where['next_audit_dates'] ) ){
					$next_audit_dates = $where['next_audit_dates'];
					if( !empty( $next_audit_dates['next_audit_date_from'] ) ){
						$nadf_where = "( ( asset.next_audit_date IS NULL OR asset.next_audit_date >= '".( format_date_db( $next_audit_dates['next_audit_date_from'] ) )."' ) AND ";
						$nadf_where .= "( site.next_audit_date IS NULL OR site.next_audit_date >= '".( format_date_db( $next_audit_dates['next_audit_date_from'] ) )."' ) AND ";
						$nadf_where .= "( fleet_vehicle.next_audit_date IS NULL || fleet_vehicle.next_audit_date >= '".( format_date_db( $next_audit_dates['next_audit_date_from'] ) )."' ) )";
						$this->db->where( $nadf_where );
					}

					if( !empty( $next_audit_dates['next_audit_date_to'] ) ){
						$nadt_where = "( ( asset.next_audit_date IS NULL OR asset.next_audit_date <= '".( format_date_db( $next_audit_dates['next_audit_date_to'] ) )."' ) AND ";
						$nadt_where .= "( site.next_audit_date IS NULL OR site.next_audit_date <= '".( format_date_db( $next_audit_dates['next_audit_date_to'] ) )."' ) AND ";
						$nadt_where .= "( fleet_vehicle.next_audit_date IS NULL || fleet_vehicle.next_audit_date <= '".( format_date_db( $next_audit_dates['next_audit_date_to'] ) )."' ) )";
						$this->db->where( $nadt_where );
					}
				}
				unset( $where['next_audit_dates'] );
			}

			if( isset( $where['asset_id'] ) ){
				if( !empty( $where['asset_id'] ) ){
					$this->db->where( 'audit.asset_id', $where['asset_id'] );
				}
				unset( $where['asset_id'] );
			}

			if( isset( $where['person_id'] ) ){
				if( !empty( $where['person_id'] ) ){
					$this->db->where( 'audit.person_id', $where['person_id'] );
				}
				unset( $where['person_id'] );
			}

			if( isset( $where['customer_id'] ) ){
				if( !empty( $where['customer_id'] ) ){
					$this->db->where( 'audit.customer_id', $where['customer_id'] );
				}
				unset( $where['customer_id'] );
			}

			if( isset( $where['vehicle_reg'] ) ){
				if( !empty( $where['vehicle_reg'] ) ){
					$this->db->where( 'audit.vehicle_reg', $where['vehicle_reg'] );
				}
				unset( $where['vehicle_reg'] );
			}

			if( isset( $where['contract_id'] ) ){
				if( !empty( $where['contract_id'] ) ){
					$this->db->where( 'audit_types.contract_id', $where['contract_id'] );

					$date_from = !empty( $where['date_from'] )	? date( 'Y-m-d', strtotime( $where['date_from'] ) ) : date( 'Y-m-d', strtotime( 'Jan 01' ) );
					unset( $where['date_from'] );
					$date_to = !empty( $where['date_to'] )		? date( 'Y-m-d', strtotime( $where['date_to'] ) ) 	: date( 'Y-m-d', strtotime( 'Dec 31' ) );
					unset( $where['date_to'] );
					$this->db->where( 'audit.date_created >=', $date_from );
					$this->db->where( 'audit.date_created <=', $date_to );
				}
				unset( $where['contract_id'] );
			}

			if( !empty( $where ) ){
				#$this->db->where( $where );
			}

			if( $order_by ){
				$order = $this->ssid_common->_clean_order_by( $order_by, $this->primary_tbl );
				if( !empty( $order ) ){ $this->db->order_by( $order ); }
			}else{
				$this->db->order_by( 'ISNULL( audit_result_statuses.result_ordering ), audit_result_statuses.result_ordering ASC, audit.audit_result_status_id ASC' );
			}

			$query = $this->db->from('audit')->count_all_results();

			$results['total'] = !empty( $query ) ? $query : 0;
			$limit 			  = ( !empty( $limit > 0 ) ) ? $limit : $results['total'];
			$results['pages'] = !empty( $query ) ? ceil( $query / $limit ) : 0;

			return json_decode( json_encode( $results ) );
		}
		return $result;
	}

	/*
	* Get a list of required sections per asset audit type *
	*/
	public function get_required_sections( $account_id = false , $audit_type_id = false , $asset_type_id = false, $audit_id = false, $un_grouped = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $audit_type_id ) ){

			$get_audit_type = $this->db->get_where( 'audit_types', [ 'account_id'=>$account_id, 'audit_type_id'=>$audit_type_id ] )->row();

			if( !empty( $asset_type_id ) ){
				$this->db->where( 'qb.asset_type_id', $asset_type_id );
			}

			switch( strtolower( $get_audit_type->audit_group ) ){
				case 'site':
					$source_table = 'audit_responses_sites';
					break;
				case 'fleet':
				case 'vehicle':
					$source_table = 'audit_responses_fleet';
					break;

				case 'person':
				case 'people':
					$source_table = 'audit_responses_people';
					break;

				case 'asset':
					$source_table = 'audit_responses_assets';
					break;
				case 'job':
					$source_table = 'audit_responses_job';
					break;
				case 'customer':
					$source_table = 'audit_responses_customer';
					break;
			}

			$this->db->select('qb.section, qb.section_ref, COUNT(*) AS `required_questions`, audit_types.audit_type',false)
				->join( 'audit_types', 'audit_types.audit_type_id = qb.audit_type_id', 'left' )
				->where( 'qb.account_id', $account_id )
				->where( 'qb.audit_type_id', $audit_type_id )
				->group_by( 'qb.section' );

			$query = $this->db->order_by( 'qb.section_ref, LENGTH(qb.ordering) asc, qb.ordering asc' )
				->where( 'qb.is_active',1 )
				->get( 'audit_question_bank qb' );

			if( $query->num_rows() > 0 ){
				foreach( $query->result() as $row ){
					$result[trim($row->section_ref)] = [
						'audit_id'=>( string ) $audit_id,
						'section'=>trim($row->section),
						'section_ref'=>trim($row->section_ref),
						'required_questions'=>$row->required_questions,
						'answered_questions'=>0,
						'pending_questions'=>$row->required_questions,
						'section_status'=>'Not Started'
					];

					#get answered_questions
					if( $audit_id ){
						$responses = $this->get_audit_responses( $audit_id, $source_table, false, trim($row->section) );
						$total_resps = ( is_array( $responses ) ) ? count( $responses ) : ( ( is_object( $responses ) ) ? count( object_to_array(  $responses ) ) : 0 );
						$result[trim($row->section_ref)]['answered_questions'] = (string) $total_resps;
						$result[trim($row->section_ref)]['pending_questions']  = (string) ( $row->required_questions - $total_resps );
					}

					# uddate status
					$result[trim($row->section_ref)]['section_status']  = ( $row->required_questions == $result[trim($row->section_ref)]['answered_questions'] ) ? 'Completed' : (  ( $result[trim($row->section_ref)]['answered_questions'] > 0 ) ? 'In Progress' : 'Not Started' );
				}

				$this->session->set_flashdata('message','Required sections data found');

				$result = ( !empty( $un_grouped ) ) ? array_values( $result ) : $result;

				return $result;

			}else{
				$this->session->set_flashdata('message','No data found');
			}
		}
		return $result;

	}

	/*
	* Get a list of assets for audit
	*/
	public function get_audit_asset_list( $account_id = false , $site_id = false , $period_days = false, $req_percentage = false ){

		$result = false;

		if( !empty( $account_id ) ){

			$date_range_start 	= date( 'Y-m-d' );
			$date_range_end   	= date( 'Y-m-d', strtotime('+ '.$period_days.' days') );
			$where 				= '( ( asset.next_audit_date >="'.$date_range_start.'" AND asset.next_audit_date <="'.$date_range_end.'" ) OR asset.next_audit_date IS NULL OR asset.next_audit_date = "" OR asset.next_audit_date = "0000-00-00" )';
			$where 				= [];


			## Get minimum required assets
			if( !empty( $req_percentage ) && ( $req_percentage != DEFAULT_AUDIT_REQ_PERCENTAGE ) ){
				$query = $this->db->select( 'asset.id' )
					->where( 'asset.account_id', $account_id )
					->where( 'asset.site_id', $site_id )
					->where( $where )
					->from( 'asset' )
					->count_all_results();

				if( $query > 0 ){
					//Get minimum required
					$limit = ( $query > 0 ) ? ceil( ( $req_percentage / 100 ) * $query ) : 0;
				}
			}

			$this->db->select( ' asset.asset_id, asset.asset_unique_id, asset.asset_type_id, asset.last_audit_date, asset.next_audit_date, locations.location_name', false )
				->join( 'locations_shared', 'locations_shared.asset_id = asset.asset_id', 'left' )
				->join( 'locations', 'locations.location_id = locations_shared.location_id', 'left' )
				->where( 'asset.account_id', $account_id )
				->where( 'asset.site_id', $site_id )
				->where( $where )
				->order_by( 'asset.asset_unique_id' );

			if( !empty( $limit ) ){
				$this->db->limit( $limit );
			}

			$query = $this->db->get( 'asset' );

			if( $query->num_rows() > 0 ){
				$result = $query->result();
				$this->session->set_flashdata('message','Evidocs asset list data found');
			}else{
				$this->session->set_flashdata('message','No data found matching your criteria');
			}

		}else{
			$this->session->set_flashdata('message','No data found');
		}
		return $result;
	}

	/** Get Evidocs types **/
	public function audit_types( $account_id = false, $audit_group = false, $audit_type_id = false, $un_grouped = false, $group_by_category = false, $group_by_frequesncy = false ){
		$result = null;

		if( $audit_type_id ){
			$result = $this->db->select( 'audit_types.*, audit_categories.category_id, audit_categories.category_name, audit_categories.category_group', false )
				->join( 'audit_categories', 'audit_types.category_id = audit_categories.category_id', 'left' )
				->get_where( 'audit_types', ['audit_type_id'=>$audit_type_id, 'audit_types.is_active'=>1, 'audit_categories.is_active'=>1 ] )->row();
		}else{

			if( $account_id ){
				$this->db->where( 'audit_types.account_id', $account_id );
			}else{
				$this->db->where( '( audit_types.account_id IS NULL OR audit_types.account_id = "" )' );
			}

			if( $audit_group ){
				$this->db->where( 'audit_types.audit_group', $audit_group );
			}

			$query = $this->db->select( 'audit_types.*, audit_categories.category_id, audit_categories.category_name, audit_categories.category_group', false )
				->join( 'audit_categories', 'audit_types.category_id = audit_categories.category_id', 'left' )
				->order_by( 'category_name, audit_group, audit_type' )
				->group_by( 'audit_types.audit_type_id' )
				->where( 'audit_types.is_active', 1 )
				->where( 'audit_categories.is_active', 1 )
				->order_by( 'audit_types.audit_type ASC, audit_types.audit_frequency ASC' )
				->get( 'audit_types' );

			if( $query->num_rows() > 0 ){

				if( $un_grouped ){

					$result = $query->result();

				}else{
					foreach( $query->result() as $k => $row ){

						if( !empty( $group_by_category ) && !empty( $group_by_frequesncy ) ){
							$data[$row->category_name][$row->audit_frequency][] = $row;
						}else if( !empty( $group_by_category ) ){
							$data[$row->category_name][] 	= $row;
						}else if( !empty( $group_by_frequesncy ) ){
							$data[$row->audit_frequency][] 	= $row;
						}else{
							#$data[$k] 	= $row;
							#$data[$row->category_name][$row->audit_frequency][] = $row;
							$data[$row->audit_frequency][$row->category_name][] = $row;
						}
					}
					$result = $data;
				}
				$this->session->set_flashdata('message','Evidocs type data found');
			}else{
				$this->session->set_flashdata('message','No Evidocs types found');
			}
		}
		return $result;
	}

	/** Get Evidocs result statuses **/
	public function get_audit_result_statuses( $account_id = false, $audit_result_status_id = false, $audit_result_group = false ){
		$result = null;

		if( !empty( $account_id ) ){

			//Do not enforce account_id checking for audit-result statuses
			$this->db->where( 'audit_result_statuses.account_id', $account_id );
			#$this->db->where( '( audit_result_statuses.account_id IS NULL OR audit_result_statuses.account_id = ""  OR audit_result_statuses.account_id = 0 )' );

			if ( !empty( $audit_result_status_id ) ){
				$this->db->where( 'audit_result_status_id', $audit_result_status_id );
			}

			if ( !empty( $audit_result_group ) ){
				$this->db->where( 'audit_result_group', $audit_result_group );
			}

			$query = $this->db->where( 'is_active', 1 )->get( 'audit_result_statuses' );

			if( $query->num_rows() > 0 ){
				$this->session->set_flashdata( 'message','Data found' );
				if( !empty( $audit_result_status_id ) ){
					$result = $query->result()[0];
				}else{
					$result = $query->result();
				}
			}else{
				$this->session->set_flashdata('message','No data found');
			}
		}else{
			$this->session->set_flashdata('message','Your request is missing required information');
		}

		return $result;
	}

	/** Evidocs Statistics **/
	public function get_audit_stats( $account_id = false, $stat_type = false, $period_days = DEFAULT_PERIOD_DAYS, $date_from = false, $date_to = false ){

		$result = false;

		if( !empty( $account_id ) && !empty( $stat_type ) ){
			$current_date 	= date( 'Y-m-d' );
			$date_from 		= !empty( $date_from ) ? date( 'Y-m-d', strtotime( $date_from ) ) : date( 'Y-m-01', strtotime( $current_date ) );
			$date_to  		= !empty( $date_to ) ? date( 'Y-m-d', strtotime( $date_to ) ) : date( 'Y-m-t', strtotime( $current_date ) );
			$date_to		= ( strtotime( $date_to ) > strtotime( $date_from.' + '.$period_days ) ) ? $date_to : date( 'Y-m-d', strtotime( $date_from.' + '.$period_days ) );

			if( !empty( $where ) ){
				$where = convert_to_array( $where );

				if( !empty( $where ) ){
					if( !empty( $where['contract_id'] ) ){
						$contract_id = $where['contract_id'];
						unset( $where['contract_id'] );
					}

					if( !empty( $where['year_to_date'] ) ){
						$year_to_date = $where['year_to_date'];
						unset( $where['year_to_date'] );
					}
					
					if( !empty( $where['month_to_date'] ) ){
						$month_to_date = $where['month_to_date'];
						unset( $where['month_to_date'] );
					}
				}
			}

			switch( strtolower( $stat_type ) ){

				case 'completion':

					// if( !empty( $date_from ) ){
						// $this->db->where( 'audit.date_created >= "'.$date_from.'" AND audit.date_created <= "'.$date_to.'" ' );
					// }

					$query = $this->db->select( 'SUM( CASE WHEN audit_status = "In Progress"  THEN 1 ELSE 0 END ) AS `In Progress`,
						SUM( CASE WHEN audit_status = "Completed"  THEN 1 ELSE 0 END ) AS `Completed`,
						SUM( CASE WHEN audit_id > 0 THEN 1 ELSE 0 END ) AS `Total`', false
					)->where( 'audit.account_id', $account_id )
					->order_by( 'audit.audit_status' )
					->get( 'audit' );

					if( $query->num_rows() > 0 ){
						$result = $query->result();
					}

					break;

				case 'periodic_audits':
					$result = $this->_audits_due( $account_id, $period_days );
					break;

				case 'audit_results':

					// if( !empty( $date_from ) ){
						// $this->db->where( 'audit.date_created >= "'.$date_from.'" AND audit.date_created <= "'.$date_to.'" ' );
					// }
					$this->db->select( 'audit_result_statuses.*,
						SUM( CASE WHEN  audit.audit_result_status_id > 0 THEN 1 ELSE 0 END ) AS status_total', false )
						->join( 'audit', 'audit_result_statuses.audit_result_status_id = audit.audit_result_status_id', 'left' )
						->where( 'audit_result_statuses.account_id', $account_id )
						->where( 'audit_result_statuses.is_active', 1 )
						->where( 'audit.archived !=', 1 )
						->order_by( 'audit_result_statuses.result_ordering' )
						->group_by( 'audit_result_statuses.audit_result_status_id' );

					$audit_stats = 	$this->db->get( 'audit_result_statuses' );

						if( $audit_stats->num_rows() > 0 ){
							$data = [];
							$grand_total 				= (string) array_sum( array_column( $audit_stats->result_array(), 'status_total') );//Get the grand total
							$stats_arr 					= array_combine ( array_map( 'strtolower', array_column( $audit_stats->result_array(), 'result_status_group') ) , array_column( $audit_stats->result_array(), 'status_total') ); //creata a new array if column => value
							$data['stats']				= $audit_stats->result_array();
							$data['totals'] 			= ( !empty( $stats_arr ) && !empty( $grand_total ) ) ? array_merge( ['grand_total'=>$grand_total], $stats_arr ) : [];
							$data['dates'] 				= [
								'date_from'=>date( 'd/m/Y', strtotime( $date_from ) ),
								'date_to'=>date( 'd/m/Y', strtotime( $date_to ) ),
								'period_days'=> ( string ) floor( ( strtotime( $date_to ) - strtotime( $date_from ) ) / 86000 )
							];

							#Calculate Compliance using what passed + recommendations
							if( !empty( $data['totals']['grand_total'] ) && ( !empty( $data['totals']['passed'] ) && ( $data['totals']['passed'] > 0 ) ) ){
								$data['totals']['compliance'] 	  = ( number_format( ( ( ( $data['totals']['passed'] + ( isset( $data['totals']['recommendations'] ) && !empty( $data['totals']['recommendations'] ? $data['totals']['recommendations'] : 0 ) ) ) / $data['totals']['grand_total'] ) * 100 ), 2 ) + 0 ).'%';
								$data['totals']['compliance_raw'] =  ( string ) ( number_format( ( ( ( $data['totals']['passed'] + ( isset( $data['totals']['recommendations'] ) && !empty( $data['totals']['recommendations'] ? $data['totals']['recommendations'] : 0 ) ) ) / $data['totals']['grand_total'] ) * 100 ), 4 ) + 0 );
								$data['totals']['compliance_alt'] = 'Passed';
							}

							# Calculate compliance based on what failed
							if( !empty( $data['totals']['grand_total'] ) && ( !empty( $data['totals']['failed'] ) && ( $data['totals']['failed'] > 0 ) ) ){
								$data['totals']['compliance'] 	  = ( number_format( ( ( ( $data['totals']['failed'] ) / $data['totals']['grand_total'] ) * 100 ), 2 ) + 0 ).'%';
								$data['totals']['compliance_raw'] = ( string ) ( number_format( ( ( ( $data['totals']['failed'] ) / $data['totals']['grand_total'] ) * 100 ), 2 ) + 0 );
								$data['totals']['compliance_alt'] = 'Didn\'t Pass';
							}

							$result = $data;
						}
					break;
			}

			if( !empty( $result ) ){
				$this->session->set_flashdata( 'message', 'Evidocs stats data found!' );
			}else{
				$this->session->set_flashdata( 'message', 'No data matching your criteria!' );
			}

		}else{
			$this->session->set_flashdata('message','Your request is missing required information');
		}

		return $result;
	}

	/** Get Asset / Site / Fleet Evidocss Due **/
	public function _audits_due( $account_id = false, $period_days = DEFAULT_PERIOD_DAYS, $date_from = false, $date_to = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $period_days ) ){
			$data 		= [];
			$total_due 	= $total_in_progress = $total_outstanding = $total_completed = 0;

			$current_date 	= date( 'Y-m-d' );
			$date_from 		= !empty( $date_from ) ? date( 'Y-m-d', strtotime( $date_from ) ) : date( 'Y-m-01', strtotime( $current_date ) );
			$date_to  		= !empty( $date_to ) ? date( 'Y-m-d', strtotime( $date_to ) ) : date( 'Y-m-t', strtotime( $current_date ) );
			$date_to		= ( strtotime( $date_to ) > strtolower( $date_from.' + '.$period_days ) ) ? $date_to : date( 'Y-m-d', strtolower( $date_from.' + '.$period_days ) );

			foreach( $this->audit_stats_group as $k => $audit_group ){

				$res = false;

				switch( strtolower( $audit_group ) ){

					//We now need to switch to using the records from the DB ~ EK
					case 'asset':
						$res = $this->_asset_audits_due( $account_id, DEFAULT_PERIOD_DAYS, $date_from, $date_to );
						break;
					case 'site':
						$res = $this->_site_audits_due( $account_id, DEFAULT_PERIOD_DAYS, $date_from, $date_to );
						break;
					case 'fleet':
						$res = $this->_fleet_audits_due( $account_id, DEFAULT_PERIOD_DAYS, $date_from, $date_to );
						break;
					case 'people':
						$res = $this->_people_audits_due( $account_id, DEFAULT_PERIOD_DAYS, $date_from, $date_to );
						break;
					case 'job':
						$res = $this->_job_audits_due( $account_id, DEFAULT_PERIOD_DAYS, $date_from, $date_to );
						break;

				}

				if( !empty( $res ) ){
					$data['stats'][] = [
						[
							'group_name'=>'due',
							'group_name_alt'=>'Due',
							'group_name_desc'=>'Total due',
							'group_total'=>( !empty( $res->due ) ) ? $res->due : 0,
							'group_colour'=>'#F78A48',
							'group_class'=>$audit_group,
							'hex_color'=>'orange'
						],
						[
							'group_name'=>'in_progress',
							'group_name_alt'=>'In Progress',
							'group_name_desc'=>'Total In Progress',
							'group_total'=>( !empty( $res->in_progress ) ) ? $res->in_progress : 0,
							'group_colour'=>'#F7C848',
							'group_class'=>$audit_group,
							'hex_color'=>'yellow'
						],
						[
							'group_name'=>'outstanding',
							'group_name_alt'=>'Not Started',
							'group_name_desc'=>'Total outstanding',
							'group_total'=>( string ) ( !empty( $res->due ) && ( $res->due > 0 )  ) ? ( $res->due - ( ( ( !empty( $res->in_progress ) ) ? ( !empty( $res->in_progress ) ) : 0 ) + ( ( !empty( $res->completed ) ) ? ( !empty( $res->completed ) ) : 0 ) ) ) : 0,
							'group_colour'=>'#FC5B5B',
							'group_class'=>$audit_group,
							'hex_color'=>'red'
						],
						[
							'group_name'=>'completed',
							'group_name_alt'=>'Completed',
							'group_name_desc'=>'Total Completed',
							'group_total'=>( !empty( $res->completed ) ) ? $res->completed : 0,
							'group_colour'=>'#6CD167',
							'group_class'=>$audit_group,
							'hex_color'=>'green'
						],

					];
					$total_due 			+= ( ( !empty( $res->due ) ) ? $res->due : 0 );
					$total_completed 	+= ( ( !empty( $res->completed ) ) ? $res->completed : 0 );
					$total_in_progress 	+= ( ( !empty( $res->in_progress ) ) ? $res->in_progress : 0 );
				}
			}

			$total_due = 250;
			//This is hack to prevent percentage values above 100
			//if( ( $total_in_progress + $total_completed ) > $total_due ){
				//$total_due = $total_in_progress + $total_completed;
			//}

			//Collate the totals
			$data['totals'] = [
				[
					'group_name'=>'due',
					'group_name_alt'=>'Due',
					'group_name_desc'=>'Total due',
					'group_total'=>( string ) $total_due,
					'group_colour'=>'#F78A48',
					#'group_percentage'=>( ( $total_in_progress + $total_completed ) > 0 && $total_due > 0 ) ? ( number_format( ( ( ( $total_in_progress + $total_completed ) / $total_due )*100 ), 2 ) + 0 ).'%' : 0
					#'group_percentage'=>( ( $total_completed > 0 ) && ( $total_due > 0 ) ) ? ( number_format( ( ( $total_completed / $total_due)*100 ), 2 ) + 0 ).'%' : 0,
					'group_percentage'=>( ( $total_due - ( $total_in_progress + $total_completed ) ) > 0 && $total_due > 0 ) ? ( number_format( ( ( ( $total_due - ( $total_in_progress + $total_completed ) ) / $total_due )*100 ), 2 ) + 0 ).'%' : 0, //Show outstanding audits
					'group_percentage_raw'=>( ( $total_due - ( $total_in_progress + $total_completed ) ) > 0 && $total_due > 0 ) ? ( number_format( ( ( ( $total_due - ( $total_in_progress + $total_completed ) ) / $total_due )*100 ), 2 ) + 0 ) : 0,
					'hex_color'=>'orange'
				],
				[
					'group_name'=>'in_progress',
					'group_name_alt'=>'In Progress',
					'group_name_desc'=>'Total in progress',
					'group_total'=> ( string ) $total_in_progress,
					'group_colour'=>'#F7C848',
					'group_percentage'=>( $total_in_progress > 0 && $total_due > 0 ) ? ( number_format( ( ( $total_in_progress / $total_due )*100 ), 2 ) + 0 ).'%' : 0,
					'group_percentage_raw'=>( $total_in_progress > 0 && $total_due > 0 ) ? ( number_format( ( ( $total_in_progress / $total_due )*100 ), 4 ) + 0 ) : 0,
					'hex_color'=>'yellow'
				],
				[
					'group_name'=>'outstanding',
					'group_name_alt'=>'Not Started',
					'group_name_desc'=>'Total outstanding',
					'group_total'=> ( string ) ( $total_due - ( $total_in_progress + $total_completed ) ),
					'group_colour'=>'#FC5B5B',
					'group_percentage'=>( ( $total_due - ( $total_in_progress + $total_completed ) ) > 0 && $total_due > 0 ) ? ( number_format( ( ( ( $total_due - ( $total_in_progress + $total_completed ) ) / $total_due )*100 ), 2 ) + 0 ).'%' : 0,
					'group_percentage_raw'=>( ( $total_due - ( $total_in_progress + $total_completed ) ) > 0 && $total_due > 0 ) ? ( number_format( ( ( ( $total_due - ( $total_in_progress + $total_completed ) ) / $total_due )*100 ), 4 ) + 0 ) : 0,
					'hex_color'=>'red'
				],
				[
					'group_name'=>'completed',
					'group_name_alt'=>'Completed',
					'group_name_desc'=>'Total completed',
					'group_total'=> ( string ) $total_completed,
					'group_colour'=>'#6CD167',
					'group_percentage'=>( $total_completed > 0 && $total_due > 0 ) ? ( number_format( ( ( $total_completed / $total_due )*100 ), 2 ) + 0 ).'%' : 0,
					'group_percentage_raw'=>( $total_completed > 0 && $total_due > 0 ) ? ( number_format( ( ( $total_completed / $total_due )*100 ), 4 ) + 0 ) : 0,
					'hex_color'=>'green'
				]
			];

			//Preserve the dates from the input
			$from_timestamp = strtotime( $date_from );
			$to_timestamp 	= strtotime( $date_to );
			$data['dates'] = [
				'date_from'=>date( 'd/m/Y', $from_timestamp ),
				'date_to'=>date( 'd/m/Y', $to_timestamp ),
				'period_days'=> (string) floor( ( $to_timestamp - $from_timestamp ) / 86000 ),
				'audit_group'=>'dates'
			];

			$result = $data;
		}else{
			$this->session->set_flashdata('message','Your request is missing required information');
		}
		return $result;
	}

	/** Get Asset Evidocss Due **/
	private function _asset_audits_due( $account_id = false, $period_days = DEFAULT_PERIOD_DAYS, $date_from = false, $date_to = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $period_days ) ){

			$current_date 	= date( 'Y-m-d' );
			$date_from 		= !empty( $date_from ) ? date( 'Y-m-d', strtotime( $date_from ) ) : date( 'Y-m-01', strtotime( $current_date ) );
			$date_to  		= !empty( $date_to ) ? date( 'Y-m-d', strtotime( $date_to ) ) : date( 'Y-m-t', strtotime( $current_date ) );
			$date_to		= ( strtotime( $date_to ) > strtolower( $date_from.' + '.$period_days ) ) ? $date_to : date( 'Y-m-d', strtolower( $date_from.' + '.$period_days ) );

			$result = [
				'due'=>0,
				'in_progress'=>0,
				'completed'=>0
			];

			//Get due audits
			$audits_due = $this->db->select( ' COUNT(a.asset_id) `due`', false )
				->where( 'a.next_audit_date >= "'.$date_from.'" AND a.next_audit_date <= "'.$date_to.'" ' )
				->get( 'asset a' )
				->row();

			# Method 2: Check from the Evidocs Schedule table
			// $audits_due = $this->db->select( ' COUNT(as.asset_id) `due`', false )
				// ->where( 'as.next_audit_date >= "'.$date_from.'" AND as.next_audit_date <= "'.$date_to.'" ' )
				// ->where( 'as.site_id >= 0' )
				// ->get( 'audit_schedule as' )
				// ->row();

			if( !empty( $audits_due ) ){
				$result['due'] = $audits_due->due;
			}

			//Get audits in progress
			$audits_in_progress = $this->db->select( '
					SUM( CASE WHEN audit.audit_status = "in progress" THEN 1 ELSE 0 END ) `in_progress`,
					SUM( CASE WHEN audit.audit_status = "completed" THEN 1 ELSE 0 END ) `completed`',
				false )
				->join( 'asset', 'asset.asset_id = audit.asset_id', 'left' )
				->where_in( 'audit.audit_status', ['in progress','completed'] )
				->where( 'audit.date_created >= "'.$date_from.'" AND audit.date_created <= "'.$date_to.'" ' )
				->get( 'audit' )
				->row();

			if( !empty( $audits_in_progress ) ){
				$result['in_progress'] 	= $audits_in_progress->in_progress;
				$result['completed'] 	= $audits_in_progress->completed;
			}

			$result = (object) $result;
		}
		return $result;
	}

	/** Get Site Evidocs Due **/
	private function _site_audits_due( $account_id = false, $period_days = DEFAULT_PERIOD_DAYS, $date_from = false, $date_to = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $period_days ) ){

			$current_date 	= date( 'Y-m-d' );
			$date_from 		= date( 'Y-m-01', strtotime( $current_date ) );
			$date_to  		= date( 'Y-m-t', strtotime( $current_date ) );
			$date_to		= ( strtotime( $date_to ) > strtolower( $date_from.' + '.$period_days ) ) ? $date_to : date( 'Y-m-d', strtolower( $date_from.' + '.$period_days ) );

			$result = [
				'due'=>0,
				'in_progress'=>0,
				'completed'=>0
			];


			//Get due audits
			# Method 1: Check the site table
			// $audits_due = $this->db->select( ' COUNT(s.site_id) `due`', false )
				// ->where( 's.next_audit_date >= "'.$date_from.'" AND s.next_audit_date <= "'.$date_to.'" ' )
				// ->get( 'site s' )
				// ->row();

			# Method 2: Check from the Evidocs Schedule table
			$audits_due = $this->db->select( ' COUNT(as.site_id) `due`', false )
				->where( 'as.next_audit_date >= "'.$date_from.'" AND as.next_audit_date <= "'.$date_to.'" ' )
				->where( 'as.site_id >= 0' )
				->get( 'audit_schedule as' )
				->row();

			if( !empty( $audits_due ) ){
				$result['due'] = $audits_due->due;
			}

			//Get audits in progress
			$audits_in_progress = $this->db->select( '
					SUM( CASE WHEN a.audit_status = "in progress" THEN 1 ELSE 0 END ) `in_progress`,
					SUM( CASE WHEN a.audit_status = "completed" THEN 1 ELSE 0 END ) `completed`',
				false )
				->join( 'site s', 'a.site_id = s.site_id' )
				->where_in( 'a.audit_status', ['in progress','completed'] )
				->where( 'a.date_created >= "'.$date_from.'" AND a.date_created <= "'.$date_to.'" ' )
				->get( 'audit a' )
				->row();

			if( !empty( $audits_in_progress ) ){
				$result['in_progress'] 	= $audits_in_progress->in_progress;
				$result['completed'] 	= $audits_in_progress->completed;
			}

			$result = (object) $result;

		}
		return $result;
	}

	/** Get Fleet Evidocss Due **/
	private function _fleet_audits_due( $account_id = false, $period_days = DEFAULT_PERIOD_DAYS, $date_from = false, $date_to = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $period_days ) ){
			$current_date 	= date( 'Y-m-d' );
			$date_from 		= date( 'Y-m-01', strtotime( $current_date ) );
			$date_to  		= date( 'Y-m-t', strtotime( $current_date ) );
			$date_to		= ( strtotime( $date_to ) > strtolower( $date_from.' + '.$period_days ) ) ? $date_to : date( 'Y-m-d', strtolower( $date_from.' + '.$period_days ) );

			$result = [
				'due'=>0,
				'in_progress'=>0,
				'completed'=>0
			];

			# Method 1: Get due audits from object table
			$audits_due = $this->db->select( ' COUNT(f.vehicle_reg) `due`', false )
				->where( 'f.next_audit_date >= "'.$date_from.'" AND f.next_audit_date <= "'.$date_to.'" ' )
				->get( 'fleet_vehicle f' )
				->row();

			# Method 2: Check from the Evidocs Schedule table
			// $audits_due = $this->db->select( ' COUNT(as.vehicle_reg) `due`', false )
				// ->where( 'as.next_audit_date >= "'.$date_from.'" AND as.next_audit_date <= "'.$date_to.'" ' )
				// ->where( 'as.vehicle_reg != ""' )
				// ->get( 'audit_schedule as' )
				// ->row();

			if( !empty( $audits_due ) ){
				$result['due'] = $audits_due->due;
			}

			//Get audits in progress
			$audits_in_progress = $this->db->select( '
					SUM( CASE WHEN a.audit_status = "in progress" THEN 1 ELSE 0 END ) `in_progress`,
					SUM( CASE WHEN a.audit_status = "completed" THEN 1 ELSE 0 END ) `completed`',
				false )
				->join( 'audit a', 'a.vehicle_reg = f.vehicle_reg' )
				->where_in( 'a.audit_status', ['in progress','completed'] )
				->where( 'a.date_created >= "'.$date_from.'" AND a.date_created <= "'.$date_to.'" ' )
				->get( 'fleet_vehicle f' )
				->row();

			if( !empty( $audits_in_progress ) ){
				$result['in_progress'] 	= $audits_in_progress->in_progress;
				$result['completed'] 	= $audits_in_progress->completed;
			}

			$result = (object) $result;
		}
		return $result;
	}

	/** Get People Evidocs Due **/
	private function _people_audits_due( $account_id = false, $period_days = DEFAULT_PERIOD_DAYS, $date_from = false, $date_to = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $period_days ) ){

			$current_date 	= date( 'Y-m-d' );
			$date_from 		= date( 'Y-m-01', strtotime( $current_date ) );
			$date_to  		= date( 'Y-m-t', strtotime( $current_date ) );
			$date_to		= ( strtotime( $date_to ) > strtolower( $date_from.' + '.$period_days ) ) ? $date_to : date( 'Y-m-d', strtolower( $date_from.' + '.$period_days ) );

			$result = [
				'due'=>0,
				'in_progress'=>0,
				'completed'=>0
			];

			# Method 2: Check from the Evidocs Schedule table
			$audits_due = $this->db->select( ' COUNT(as.site_id) `due`', false )
				->where( 'as.next_audit_date >= "'.$date_from.'" AND as.next_audit_date <= "'.$date_to.'" ' )
				->where( 'as.person_id >= 0' )
				->get( 'audit_schedule as' )
				->row();

			if( !empty( $audits_due ) ){
				$result['due'] = $audits_due->due;
			}

			//Get audits in progress
			$audits_in_progress = $this->db->select( '
					SUM( CASE WHEN a.audit_status = "in progress" THEN 1 ELSE 0 END ) `in_progress`,
					SUM( CASE WHEN a.audit_status = "completed" THEN 1 ELSE 0 END ) `completed`',
				false )
				->join( 'site s', 'a.site_id = s.site_id' )
				->where_in( 'a.audit_status', ['in progress','completed'] )
				->where( 'a.date_created >= "'.$date_from.'" AND a.date_created <= "'.$date_to.'" ' )
				->get( 'audit a' )
				->row();

			if( !empty( $audits_in_progress ) ){
				$result['in_progress'] 	= $audits_in_progress->in_progress;
				$result['completed'] 	= $audits_in_progress->completed;
			}

			$result = (object) $result;

		}
		return $result;
	}


	public function get_lookup_w_instant_stats( $account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = false ){
		$result = [];

		if( !empty( $account_id ) ){

			$audit_lookup 	= $this->audit_lookup( $account_id, $search_term, $where, $order_by, 9999, $offset );

			if( !empty( $audit_lookup ) ){

				$result['audits'] 	= !empty( $audit_lookup->records ) ? $audit_lookup->records : $audit_lookup;

				$result['stats'] 	= [
					"not_started" 	=> $this->audits2do_number,
					"failed_audits" => 0,
					"recommendations_audits" 	=> 0,
					"in_progress" 	=> 0,
				];

				foreach( $result['audits'] as $key => $row ){
					if( in_array( strtolower( $row->audit_status ), ["in progress", "completed" ] ) ){
						$result['stats']["not_started"]--;
					}

					if( in_array( strtolower( $row->result_status_group ), ["failed"] ) ){
						$result['stats']["failed_audits"]++;
					}

					if( in_array( strtolower( $row->result_status_group ), ["recommendations"] ) ){
						$result['stats']["recommendations_audits"]++;
					}

					if( in_array( strtolower( $row->audit_status ), ["in progress"] ) ){
						$result['stats']["in_progress"]++;
					}
				}

				$this->session->set_flashdata( 'message', 'Evidocss and Stats found' );

			} else {
				$this->session->set_flashdata( 'message', 'Evidocss and Stats not found' );
			}

		} else {
			$this->session->set_flashdata('message','Your request is missing required information');
		}

		return $result;
	}

	/** Process an Evidocs result status **/
	public function process_result_status( $account_id = false, $audit_result_data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $audit_result_data['audit_result_status_id'] ) ){
			$result_statuses = $this->get_audit_result_statuses( $account_id, $audit_result_data['audit_result_status_id'] );

			if( !empty( $result_statuses ) ){
				$audit_result_data['record_type'] 	= $result_statuses->result_status;
				$audit_result_data					= array_merge( $audit_result_data, (array) $result_statuses );
				$save_result_record 				= false;
				$default_status 					= $this->get_action_statuses( $account_id, false, 'awaiting_action' );
				$audit_result_data['action_status_id'] = ( !empty( $default_status[0]->action_status_id ) ) ? $default_status[0]->action_status_id : 1;

				switch( strtolower( $result_statuses->result_status_group  ) ){

					case 'passed':
						//Do the actions here if the EviDocs passed

						//Set asset and site compliance
						$update_asset_status = $this->update_asset_compliance_status( $account_id, $audit_result_data['audit_id'], $audit_result_data );

						break;

					case 'failed':
						//Fails, send an email as an early warning
						$save_result_record = true;
						$save_record 		= $this->create_audit_exceptions( $account_id, $audit_result_data['audit_id'], $audit_result_data );
						$email_sent 		= $this->trigger_email_notice( 'failed-audit', $audit_result_data );

						//Set asset and site compliance
						$update_asset_status = $this->update_asset_compliance_status( $account_id, $audit_result_data['audit_id'], $audit_result_data );

						break;

					case 'recommendations':
						//Recommendations
						$save_result_record = true;
						$save_record 		= $this->create_audit_exceptions( $account_id, $audit_result_data['audit_id'], $audit_result_data );
						$email_sent 		= $this->trigger_email_notice( 'audit-recommendations', $audit_result_data );

						//Set asset and site compliance
						$update_asset_status = $this->update_asset_compliance_status( $account_id, $audit_result_data['audit_id'], $audit_result_data );

						break;

					case 'not_set':
						//This shouldn't really be here but if it's ever needed, we're locked and loaded ready to fire!!!!
						break;
				}

				$update_site_status  = $this->update_site_compliance_status( $account_id, $audit_result_data['audit_id'], $audit_result_data );
			}

			if( !empty( $save_result_record ) ){

				//Update Asset if type is of Asset exists
				if( !empty( $audit_result_data['asset_id'] ) ){
					$this->_quick_asset_update( $account_id, $audit_result_data['asset_id'], ['event_tracking_status_id'=>$audit_result_data['audit_result_status_id'], 'audit_result_status_id'=>$audit_result_data['audit_result_status_id'],  ] );
				}

				//Add audit schedule only after the Result has been recorded
				if( !empty( $audit_result_data['audit_type_id'] ) ){
					$save_schedule = $this->create_audit_schedule( $account_id, $audit_result_data['audit_type_id'], $audit_result_data );
				}

				if( !empty( $save_record ) ){
					$this->session->set_flashdata('message','Evidocs result record saved successfully.');
				}else{
					$this->session->set_flashdata('message','Evidocs result record failed to save.');
				}
			}
			$result = ( !empty( $save_record ) ) ? $save_record : false;
		}else{
			$this->session->set_flashdata('message','Your request is missing required information');
		}
		return $result;
	}

	/** Save an Evidocs Result Record / Log */
	public function create_audit_exceptions( $account_id = false, $audit_id = false, $audit_result_data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $audit_id ) && !empty( $audit_result_data ) ){
			$audit_result_data  = $this->ssid_common->_data_prepare( $audit_result_data );
			$table_name 		= 'audit_exceptions';
			$conditions			= ['account_id'=>$account_id, 'audit_id'=>$audit_id ];
			$record_data 		= $this->ssid_common->_filter_data( $table_name, $audit_result_data );
			$check_exists 		= $this->db->get_where( $table_name , $conditions )->row();

			$record_data['account_id'] = $account_id;
			if( !empty( $check_exists ) ){
				//update
				$record_data['last_modified_by'] = $this->ion_auth->_current_user->id;
				$this->db->where( $conditions )
					->update( $table_name, $record_data );
			}else{
				//Add new entry
				$record_data['audit_id'] = $audit_id;
				$record_data['created_by']= $this->ion_auth->_current_user->id;
				$this->db->insert( $table_name, $record_data );
			}

			if( $this->db->affected_rows() > 0 ){
				//Update Asset with Result status
			}

			$result = ( $this->db->trans_status() !== false ) ? true : false;

		}
		return $result;
	}

	/** Create an audit schedule */
	public function create_audit_schedule( $account_id = false, $audit_type_id = false, $schedule_data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $audit_type_id ) && !empty( $schedule_data ) ){

			$schedule_data = $this->ssid_common->_data_prepare( $schedule_data );

			$schedule_data['next_audit_date'] = ( !empty( $schedule_data['next_audit_date'] ) ) ? $schedule_data['next_audit_date'] : date( 'Y-m-d', strtotime( '+ '.DEFAULT_PERIOD_DAYS.' days' ) );

			$table_name 	= 'audit_schedule';
			$conditions		= ['account_id'=>$account_id, 'audit_type_id'=>$audit_type_id, 'next_audit_date'=>$schedule_data['next_audit_date'] ];
			$record_data 	= $this->ssid_common->_filter_data( $table_name, $schedule_data );
			$check_exists 	= $this->db->get_where( $table_name , $conditions )->row();

			if( !empty( $check_exists ) ){
				//update
				$record_data['last_modified_by'] = $this->ion_auth->_current_user->id;
				$this->db->where( $conditions )
					->update( $table_name, $record_data );
			}else{
				//Add new entry
				$record_data['audit_type_id'] = $audit_type_id;
				$record_data['created_by']	  = $this->ion_auth->_current_user->id;
				$this->db->insert( $table_name, $record_data );
			}

			if( $this->db->trans_status() !== false ){
				$result = ( $this->db->trans_status() !== false ) ? true : false;
				$this->session->set_flashdata('message','Evidocs schedule saved successfully');
			}else{
				$this->session->set_flashdata('message','Evidocs schedule failed to save');
			}
		}else{
			$this->session->set_flashdata('message','Your request is missing required information');
		}
		return $result;
	}

	/** Get audit result records (exceptions) lookup */
	public function get_exceptions( $account_id = false, $audit_type_id = false, $where = false ){
		$result = false;
		if( !empty( $account_id ) ){
			$this->db->where( 'account_id', $account_id );
			if( !empty( $where ) ){

				$where = $this->ssid_common->_data_prepare( $where );

				if( !empty( $where['action_due_date'] ) ){
					$where['action_due_date'] = date( 'Y-m-d', strtotime( $where['action_due_date'] ) );
				}

				if( !empty( $where['date_from'] ) || !empty( $where['date_to'] ) ){
					$date_from 	= !empty( $where['date_from'] ) ? date( 'Y-m-d', strtotime( $where['date_from'] ) ) : date( 'Y-m-d' );
					$date_to 	= !empty( $where['date_to'] ) 	? date( 'Y-m-d', strtotime( $where['date_to'] ) ) 	: $date_from;
					$this->db->where( '( audit_exceptions.action_due_date >= "'.$date_from.'" AND audit_exceptions.action_due_date <= "'.$date_to.'" )' );
					unset( $where['date_from'], $where['date_to'] );
				}

				if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}

			if( !empty( $order_by ) ){
				$order = $this->ssid_common->_clean_order_by( $order_by, $this->primary_tbl );
				if( !empty( $order ) ){ $this->db->order_by( $order ); }
			}else{
				$this->db->order_by( ' action_due_date ' );
			}

			$query = $this->db->limit( $limit, $offset )
				->get( 'audit_exceptions' );
			if( $query->num_rows() > 0 ){
				$result = $query->result();
				$this->session->set_flashdata( 'message', 'Schedule data found' );
			}else{
				$this->session->set_flashdata( 'message', 'No data found matching your criteria' );
			}
		}else{
			$this->session->set_flashdata('message','Your request is missing required information');
		}
		return $result;
	}

	/** Do Evidocs Records (Exceptions) lookup **/
	public function exceptions_lookup( $account_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;
		if( !empty( $account_id ) ){

			$raw_where = false;

			$this->db->select( 'audit_exceptions.*, audit_types.audit_type_id, audit_types.audit_type, audit_result_statuses.result_status `audit_result_status_name`, audit_result_statuses.result_status_alt `audit_result_status_alt`, audit_result_statuses.result_status_group `audit_result_status_group`, audit_action_statuses.action_status `action_status_name`, audit_action_statuses.action_status_alt `action_status_alt`, CONCAT( u1.first_name, " ", u1.last_name ) `created_by_full_name`, CONCAT( u2.first_name, " ", u2.last_name ) `modified_by_full_name`, site.site_reference, site.site_name', false )
				->where( 'audit_exceptions.account_id', $account_id )
				->where( 'audit_exceptions.is_active', 1 )
				->join( 'audit', 'audit.audit_id = audit_exceptions.audit_id', 'left' )
				->join( 'site', 'site.site_id = audit.site_id', 'left' )
				->join( 'audit_types', 'audit_types.audit_type_id = audit.audit_type_id', 'left' )
				->join( 'audit_result_statuses', 'audit_result_statuses.audit_result_status_id = audit_exceptions.audit_result_status_id', 'left' )
				->join( 'audit_action_statuses', 'audit_action_statuses.action_status_id = audit_exceptions.action_status_id', 'left' )
				->join( 'user u1', 'u1.id = audit_exceptions.created_by', 'left' )
				->join( 'user u2', 'u2.id = audit_exceptions.last_modified_by', 'left' );

			if( !empty( $search_term ) ){
				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->exceptions_search_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				}else{
					foreach( $this->exceptions_search_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}
					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( !empty( $where ) ){

				$where = $raw_where = convert_to_array( $where );

				if( !empty( $where['record_type'] ) && $where['record_type'] != 'all' ){
					$this->db->where( 'record_type', $where['record_type'] );
					unset( $where['record_type'] );
				}

				if( !empty( $where['action_due_date'] ) ){
					$where['action_due_date'] = date( 'Y-m-d', strtotime( $where['action_due_date'] ) );
					$this->db->where( 'action_due_date', $where['action_due_date'] );
					unset( $where['action_due_date'] );
				}

				if( !empty( $where['asset_id'] ) ){
					$this->db->where( 'audit_exceptions.asset_id', $where['asset_id'] );
					unset( $where['asset_id'] );
				}

				if( !empty( $where['site_id'] ) ){
					$this->db->where( 'audit_exceptions.site_id', $where['site_id'] );
					unset( $where['site_id'] );
				}

				if( !empty( $where['vehicle_reg'] ) ){
					$this->db->where( 'audit_exceptions.vehicle_reg', $where['vehicle_reg'] );
					unset( $where['vehicle_reg'] );
				}

				if( !empty( $where['action_status_id'] ) ){
					$this->db->where( 'audit_exceptions.action_status_id', $where['action_status_id'] );
					unset( $where['action_status_id'] );
				}

				if( !empty( $where['id'] ) ){
					$this->db->where( 'audit_exceptions.id', $where['id'] );
					unset( $where['id'] );
				}

				if( !empty( $where['audit_id'] ) ){
					$this->db->where( 'audit_exceptions.audit_id', $where['audit_id'] );
					unset( $where['audit_id'] );
				}

				if( !empty( $where['contract_id'] ) ){
					$this->db->where( 'audit_types.contract_id', $where['contract_id'] );
					unset( $where['contract_id'] );
				} elseif( isset( $where['contract_id'] ) ){
					unset( $where['contract_id'] );
				}

				if( !empty( $where['date_from'] ) ){

					$date_from 	= date( 'Y-m-d 00:00:00', strtotime( $where['date_from'] ) );
					unset( $where['date_from'] );
					$date_to 	= ( !empty( $where['date_to'] ) ) ? date( 'Y-m-d 23:59:59', strtotime( $where['date_to'] ) ) : date( 'Y-m-d 23:59:59' ) ;
					if( isset( $where['date_to'] ) ) unset( $where['date_to'] );
					$this->db->where( "( ( audit_exceptions.date_created >= '".$date_from."' ) AND ( audit_exceptions.date_created <= '".$date_to."' ) )" );
				} else if( isset( $where['date_from'] ) ){
					unset( $where['date_from'] );
				}
				
				if( isset( $where['date_to'] ) ) unset( $where['date_to'] );

				if( !empty( $where ) ){
					$this->db->where( $where );
				}

			}

			if( $order_by ){
				$order = $this->ssid_common->_clean_order_by( $order_by, 'audit_exceptions' );
			if( !empty( $order ) ){ $this->db->order_by( $order ); }
			}else{
				$this->db->order_by( 'ISNULL( audit_exceptions.priority_rating ), audit_exceptions.priority_rating ASC, audit.audit_result_status_id ASC' );
			}

			$query = $this->db->limit( $limit, $offset )
				->get( 'audit_exceptions' );

			if( $query->num_rows() > 0 ){
				$data['result'] = $query->result();
				$data['counts'] = $this->exceptions_lookup_totals( $account_id, $search_term, $raw_where );
				$result = $data;

				$this->session->set_flashdata('message','Exceptions data found.');
			} else {
				$this->session->set_flashdata('message','No records found matching your criteria.');
			}
		}
		return $result;
	}

	/** Get exceptions lookup counts **/
	public function exceptions_lookup_totals( $account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT ){
		$result = false;
		if( !empty( $account_id ) ){

			$this->db->select( 'audit_exceptions.id', false )
				->where( 'audit_exceptions.account_id', $account_id )
				->where( 'audit_exceptions.is_active', 1 )
				->join( 'audit', 'audit.audit_id = audit_exceptions.audit_id', 'left' )
				->join( 'site', 'audit.site_id = site.site_id', 'left' )
				->join( 'audit_types', 'audit_types.audit_type_id = audit.audit_type_id', 'left' )
				->join( 'audit_result_statuses', 'audit_result_statuses.audit_result_status_id = audit_exceptions.audit_result_status_id', 'left' )
				->join( 'audit_action_statuses', 'audit_action_statuses.action_status_id = audit_exceptions.action_status_id', 'left' )
				->join( 'user u1', 'u1.id = audit_exceptions.created_by', 'left' )
				->join( 'user u2', 'u2.id = audit_exceptions.last_modified_by', 'left' );

			if( !empty( $search_term ) ){
				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->exceptions_search_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				}else{
					foreach( $this->exceptions_search_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}
					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( !empty( $where ) ){

				$where = $raw_where = convert_to_array( $where );

				if( !empty( $where['record_type'] ) && $where['record_type'] != 'all' ){
					$this->db->where( 'record_type', $where['record_type'] );
					unset( $where['record_type'] );
				}

				if( !empty( $where['action_due_date'] ) ){
					$where['action_due_date'] = date( 'Y-m-d', strtotime( $where['action_due_date'] ) );
					$this->db->where( 'action_due_date', $where['action_due_date'] );
					unset( $where['action_due_date'] );
				}

				if( !empty( $where['asset_id'] ) ){
					$this->db->where( 'audit_exceptions.asset_id', $where['asset_id'] );
					unset( $where['asset_id'] );
				}

				if( !empty( $where['site_id'] ) ){
					$this->db->where( 'audit_exceptions.site_id', $where['site_id'] );
					unset( $where['site_id'] );
				}

				if( !empty( $where['vehicle_reg'] ) ){
					$this->db->where( 'audit_exceptions.vehicle_reg', $where['vehicle_reg'] );
					unset( $where['vehicle_reg'] );
				}

				if( !empty( $where['action_status_id'] ) ){
					$this->db->where( 'audit_exceptions.action_status_id', $where['action_status_id'] );
					unset( $where['action_status_id'] );
				}

				if( !empty( $where['id'] ) ){
					$this->db->where( 'audit_exceptions.id', $where['id'] );
					unset( $where['id'] );
				}

				if( !empty( $where['audit_id'] ) ){
					$this->db->where( 'audit_exceptions.audit_id', $where['audit_id'] );
					unset( $where['audit_id'] );
				}

				if( !empty( $where['contract_id'] ) ){
					$this->db->where( 'audit_types.contract_id', $where['contract_id'] );
					unset( $where['contract_id'] );
				} elseif( isset( $where['contract_id'] ) ){
					unset( $where['contract_id'] );
				}

				if( !empty( $where['date_from'] ) ){

					$date_from 	= date( 'Y-m-d 00:00:00', strtotime( $where['date_from'] ) );
					unset( $where['date_from'] );
					$date_to 	= ( !empty( $where['date_to'] ) ) ? date( 'Y-m-d 23:59:59', strtotime( $where['date_to'] ) ) : date( 'Y-m-d 23:59:59' ) ;
					if( isset( $where['date_to'] ) ) unset( $where['date_to'] );
					$this->db->where( "( ( audit_exceptions.date_created >= '".$date_from."' ) AND ( audit_exceptions.date_created <= '".$date_to."' ) )" );
				} else if( isset( $where['date_from'] ) ){
					unset( $where['date_from'] );
				}
				
				if( isset( $where['date_to'] ) ) unset( $where['date_to'] );

				if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}

			$query 			  = $this->db->from( 'audit_exceptions' )->count_all_results();
			$results['total'] = !empty( $query ) ? $query : 0;
			$results['pages'] = !empty( $query ) ? ceil( $query / $limit ) : 0;
			return json_decode( json_encode( $results ) );
		}
		return $result;
	}

	/** Get audit schedule(s) */
	public function get_audit_schedule( $account_id = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;
		if( !empty( $account_id ) ){

			$this->db->where( 'account_id', $account_id );

			if( !empty( $where ) ){

				$where = $this->ssid_common->_data_prepare( $where );

				if( !empty( $where['next_audit_date'] ) ){
					$where['next_audit_date'] = date( 'Y-m-d', strtotime( $where['next_audit_date'] ) );
				}else if( !empty( $where['date_from'] ) || !empty( $where['date_to'] ) ){
					$date_from 	= !empty( $where['date_from'] ) ? date( 'Y-m-d', strtotime( $where['date_from'] ) ) : date( 'Y-m-d' );
					$date_to 	= !empty( $where['date_to'] ) 	? date( 'Y-m-d', strtotime( $where['date_to'] ) ) 	: $date_from;
					$this->db->where( '( audit_schedule.next_audit_date >= "'.$date_from.'" AND audit_schedule.next_audit_date <= "'.$date_to.'" )' );
				}
				unset( $where['date_from'], $where['date_to'] );

				if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}

			if( !empty( $order_by ) ){
				$order = $this->ssid_common->_clean_order_by( $order_by, $this->primary_tbl );
				if( !empty( $order ) ){ $this->db->order_by( $order ); }
			}else{
				$this->db->order_by( ' next_audit_date ' );
			}

			$query = $this->db->limit( $limit, $offset )
				->get( 'audit_schedule' );
			if( $query->num_rows() > 0 ){
				$result = $query->result();
				$this->session->set_flashdata( 'message', 'Schedule data found' );
			}else{
				$this->session->set_flashdata( 'message', 'No data found matching your criteria' );
			}
		}
		return $result;
	}

	/** Send out emails as an reaction to an Evidocs result status **/
	public function trigger_email_notice( $email_type = false, $content = false, $destination = array() ){
		return true;
		$result = false;
		if( !empty( $email_type ) && !empty( $content ) ){
			$destination = ( !empty( $destination ) ) ? $destination : ['enockkabungo@evidentsoftware.co.uk'];
			$audit_id 	 = ( !empty( $content['audit_id'] ) ) ? ' - #'.$content['audit_id'] : '';

			$content['submitted_by']= ucwords( $this->ion_auth->_current_user->first_name.' '.$this->ion_auth->_current_user->last_name );
			$content['timestamp'] 	= date( 'd-m-Y H:i A' );

			$msg_content = [
				'salutation' => 'Hello, ',
				'content' => $content
			];

			$email_data = [
				'to'=>$destination,
				'from'=>['enockkabungo@evidentsoftware.co.uk','Evident Software Alerts'],
			];

			switch( strtolower( $email_type ) ){
				case 'failed-audit':
					$email_body = $this->load->view( 'email_templates/audit/failed_audit', $msg_content, true );
					$email_data = array_merge( $email_data, [ 'subject'=>'Failed Evidocs Notice'.$audit_id, 'message'=>$email_body ] );
					break;
				case 'audit-recommendations':
					$email_body = $this->load->view( 'email_templates/audit/audit_recommendations', $msg_content, true );
					$email_data = array_merge( $email_data, [ 'subject'=>'Recommendations Notice'.$audit_id, 'message'=>$email_body ] );
					break;
			}

			if( !empty( $email_data ) ){
				$result = $this->mail->send_mail(  $email_data );
			}

		}
		return $result;
	}

	/** Get Action Statuses **/
	public function get_action_statuses( $account_id = false, $action_status_id = false, $action_status_group = false ){
		$result = null;

		if( !empty( $account_id ) ){

			$this->db->where( 'audit_action_statuses.account_id', $account_id );

			if ( !empty( $action_status_id ) ){
				$this->db->where( 'action_status_id', $action_status_id );
			}

			if ( !empty( $action_status_group ) ){
				$this->db->where( 'action_status_group', $action_status_group );
			}

			$query = $this->db->where( 'is_active', 1 )->get( 'audit_action_statuses' );

			if( $query->num_rows() > 0 ){
				$this->session->set_flashdata( 'message','Data found' );
				if( !empty( $action_status_id ) ){
					$result = $query->result()[0];
				}else{
					$result = $query->result();
				}
			}else{
				$this->session->set_flashdata('message','No data found');
			}
		}else{
			$this->session->set_flashdata('message','Your request is missing required information');
		}

		return $result;
	}

	/*
	*	The function to create an exception log
	*/
	public function create_exception_log( $account_id = false, $exception_id = false, $data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $exception_id ) && !empty( $data ) ){

			if( is_string( $data ) ){
				$data = json_decode( $data );
			}

			$data = object_to_array( $data );
			$data['account_id'] = $account_id;
			$data['created_by'] = $this->ion_auth->_current_user->id;

			$data = $this->ssid_common->_filter_data( 'audit_exceptions_log', $data );
			$insert = $this->db->insert( "audit_exceptions_log", $data );

			if( $this->db->affected_rows() > 0 ){
				$result = $this->db->get_where( "audit_exceptions_log", ["log_id" => $this->db->insert_id()] )->row();

				if( $data['previous_action_status_id'] != $data['current_action_status_id'] ){
					$this->db->update( "audit_exceptions", ["action_status_id" => $data['current_action_status_id']], ["id" => $data['exception_id']] );
				}

				$this->session->set_flashdata( 'message','The Exception Log has been created' );
			} else {
				$this->session->set_flashdata( 'message','The Exception Log hasn\'t been created' );
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}

		return $result;
	}

	/** Create new Evidoc Question **/
	public function add_evidoc_question( $account_id = false, $evidoc_question_data = false ){

		$result = null;

		if( !empty( $account_id ) && !empty( $evidoc_question_data  ) ){

			foreach( $evidoc_question_data as $col => $value ){
				if( $col == 'response_type' ){
					$segment 			= $this->get_evidoc_segments( false, [ $col=>$value ] );
					$data['segment'] = ( !empty( $segment  ) ) ? $segment->segment : 'questions';
				}
				$data[$col] = ( is_array( $value ) ) ? json_encode( $value ) : $value;
			}

			//Set Section Ref
			if( !empty( $data['section'] ) ){
				$data['section_ref'] = strtolower( strip_all_whitespace( $data['section'] ) );
			}

			if( !empty( $data['response_options'] ) ){
				$file_types 	  = '';
				$response_options = convert_to_array( $data['response_options'] );
				unset( $data['response_options'] );
				if( !empty( $response_options[ $data['response_type'] ]['options'] ) ){
					$options = $response_options[ $data['response_type'] ]['options'];
					if( !empty( $options ) ){
						$update_opts = $this->update_response_options( $account_id, $data['response_type'], $options );
						$data['response_options'] = json_encode( $options );
					}

					if( in_array( strtolower( $data['response_type'] ), $this->file_response_types ) ){
						$file_types = $data['response_options'];
					}
				}

				##Get extra info and the trigger
				if( !empty( $response_options[ $data['response_type'] ]['extra_info_trigger'] ) ){
					$data['extra_info_trigger'] = $response_options[ $data['response_type'] ]['extra_info_trigger'];
					$data['extra_info'] 		= ( !empty( $response_options[ $data['response_type'] ]['extra_info'] ) ) ? $response_options[ $data['response_type'] ]['extra_info'] : 'Please provide further info';
				}else{
					$data['extra_info_trigger'] = '';
					$data['extra_info'] 		= '';
				}
				
				##Get the Default Response Value
				if( !empty( $response_options[ $data['response_type'] ]['default_response'] ) ){
					$data['default_response'] 	= $response_options[ $data['response_type'] ]['default_response'];
				}else{
					$data['default_response'] 	= NULL;
				}
				
			}

			$response_type = $this->get_response_types( $account_id, ['response_type'=>$data['response_type'] ] );
			$data['response_type'] 		= ( !empty( $response_type->response_type_alt ) ) ? $response_type->response_type_alt : ucwords( $data['response_type'] );
			$data['response_options'] 	= ( !empty( $data['response_options'] ) ) ? $data['response_options'] : null;
			$data['ordering'] 			= ( !empty( $data['ordering'] ) ) ? $data['ordering'] : $this->_get_question_ordering( $account_id, $data['audit_type_id'], $data['section'] );
			$data['file_types']			= ( !empty( $data['file_types'] ) ) ? $data['file_types'] : ( !empty( $file_types ) ? $file_types : null );

			if( !empty( $data['override_existing'] ) && !empty( $data['question_id'] ) ){
				//User said override the current record
				$check_exists = $this->db->where( 'account_id', $account_id )
					->where( 'question_id', $data['question_id'] )
					->get( 'audit_question_bank' )->row();

			} else {
				unset( $data['question_id'] );
				$check_exists = $this->db->where( 'account_id', $account_id )
					->where( 'audit_question_bank.audit_type_id', $data['audit_type_id'] )
					->where( 'audit_question_bank.question', $data['question'] )
					->where( 'audit_question_bank.section_ref', $data['section_ref'] )
					->limit( 1 )
					->get( 'audit_question_bank' )
					->row();
			}

			$data = $this->ssid_common->_filter_data( 'audit_question_bank', $data );

			if( !empty( $check_exists  ) ){
				$data['last_modified_by'] = $this->ion_auth->_current_user->id;
				$this->db->where( 'question_id', $check_exists->question_id )
					->update( 'audit_question_bank', $data );
					$this->session->set_flashdata( 'message', 'This Evidoc question already exists, record has been updated successfully.' );
					$result = $check_exists;
			}else{
				$data['created_by'] 	= $this->ion_auth->_current_user->id;
				$this->db->insert( 'audit_question_bank', $data );
				$this->session->set_flashdata( 'message', 'New Evidoc question added successfully.' );
				$data['question_id'] 	= $this->db->insert_id();
				$result 			 	= $data;
			}

		}else{
			$this->session->set_flashdata( 'message','Error! Missing required information.' );
		}

		return $result;
	}

	/** Update an existing Evidoc question **/
	public function update_question( $account_id = false, $question_id = false, $postdata = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $question_id )  && !empty( $postdata ) ){

			foreach( $postdata as $col => $value ){
				if( $col == 'response_type' ){
					$segment 		= $this->get_evidoc_segments( false, [ $col=>$value ] );
					$data['segment']= ( !empty( $segment  ) ) ? $segment->segment : 'questions';
				}
				$data[$col] = ( is_array( $value ) ) ? json_encode( $value ) : $value;
			}

			//Set Section Ref
			if( !empty( $data['section'] ) ){
				$data['section_ref'] = strtolower( strip_all_whitespace( $data['section'] ) );
			}

			if( !empty( $data['response_options'] ) ){
				$response_options = convert_to_array( $data['response_options'] );
				unset( $data['response_options'] );
				if( !empty( $response_options[ $data['response_type'] ]['options'] ) ){
					$options = $response_options[ $data['response_type'] ]['options'];
					if( !empty( $options ) ){
						$update_opts = $this->update_response_options( $account_id, $data['response_type'], $options );
						$data['response_options'] = json_encode( $options );
					}

					if( in_array( strtolower( $data['response_type'] ), $this->file_response_types ) ){
						$file_types = $data['response_options'];
					}

				}else{
					$data['response_options'] = '';
				}

				##Get extra info and the trigger
				if( !empty( $response_options[ $data['response_type'] ]['extra_info_trigger'] ) ){
					$data['extra_info_trigger'] = $response_options[ $data['response_type'] ]['extra_info_trigger'];
					$data['extra_info'] 		= ( !empty( $response_options[ $data['response_type'] ]['extra_info'] ) ) ? $response_options[ $data['response_type'] ]['extra_info'] : 'Please provide further info';
				}else{
					$data['extra_info_trigger'] = '';
					$data['extra_info'] 		= '';
				}
				
				##Get the Default Response Value
				if( !empty( $response_options[ $data['response_type'] ]['default_response'] ) ){
					$data['default_response'] 	= $response_options[ $data['response_type'] ]['default_response'];
				}else{
					$data['default_response'] 	= NULL;
				}
			}

			$response_type 				= $this->get_response_types( $account_id, ['response_type'=>$data['response_type'] ] );
			$data['response_type'] 		= ( !empty( $response_type->response_type_alt ) ) ? $response_type->response_type_alt : ucwords( $data['response_type'] );
			$data['response_options'] 	= ( !empty( $data['response_options'] ) ) ? $data['response_options'] : null;
			#$data['ordering'] 			= ( !empty( $data['ordering'] ) ) ? $data['ordering'] : $this->_get_question_ordering( $account_id, $data['audit_type_id'], $data['section'] );
			$data['file_types']			= ( !empty( $data['file_types'] ) ) ? $data['file_types'] : ( !empty( $file_types ) ? $file_types : null );

			$ref_condition = [ 'account_id'=>$account_id, 'question_id'=>$question_id ];
			$update_data   = $this->ssid_common->_filter_data( 'audit_question_bank', $data );
			$record_pre_update = $this->db->get_where( 'audit_question_bank', [ 'account_id'=>$account_id, 'question_id'=>$question_id ] )->row();

			if( !empty( $record_pre_update ) ){

				$check_conflict = $this->db->select( 'question_id', false )
					->where( 'account_id', $account_id )
					->where( 'question_id !=', $question_id )
					->where( 'question', $update_data['question'] )
					->where( 'section', $update_data['section'] )
					->limit( 1 )
					->get( 'audit_question_bank' )
					->row();

				if( !$check_conflict ){
					$update_data['last_modified_by'] = $this->ion_auth->_current_user->id;
					$this->db->where( $ref_condition )
						->update( 'audit_question_bank', $update_data );

					$updated_record = $this->get_audit_questions( $account_id, false, false, false, false, false, false, false, $question_id );
					$result = ( !empty( $updated_record ) ) ? $updated_record : false;
					$this->session->set_flashdata( 'message', 'Evidoc Question updated successfully' );
					return $result;
				}else{
					$this->session->set_flashdata( 'message', 'This Evidoc Question already exists under the specified section ['.$update_data['section'].']. Update request aborted' );
					return false;
				}

			} else {
				$this->session->set_flashdata( 'message', 'This Evidoc Question record does not exist or does not belong to you.' );
				return false;
			}

		}else{
			$this->session->set_flashdata( 'message','Your request is missing requireed information.' );
		}
		return $result;
	}

	/**
	* Update Response type
	*/
	public function update_response_options( $account_id = false, $response_type = false, $options = false, $action = 'add' ){
		$result = false;
		if( !empty( $account_id ) && !empty( $response_type ) && !empty( $options ) ){
			$query = $this->db->select( 'rt.response_type_id, ro.option_value' )
				->join( 'evidoc_response_types_options `ro`', 'ro.response_type_id = rt.response_type_id', 'left' )
				->where( [ 'rt.account_id'=>$account_id, 'rt.response_type'=>$response_type ] )
				->group_by( 'ro.option_value' )
				->get( 'evidoc_response_types rt' );

			if( $query->num_rows() > 0 ){
				$response_type_id = array_column( $query->result_array(), 'response_type_id' );
				$response_type_id = !empty( $response_type_id[0] ) ? $response_type_id[0] : false;
				$current_list 	  = array_map( 'strtolower', array_column( $query->result_array(), 'option_value' ) );
				if( !empty( $response_type_id ) && $action == 'add' ){
					$add_opts 		= [];
					if( !empty( $current_list ) ){
						$new_options    = array_diff( array_map( 'strtolower', $options ), $current_list );
						foreach( $new_options as $opt ){
							$add_opts[] = [
								'response_type_id'=>$response_type_id,
								'option_value'=>ucwords( $opt ),
								'option_pass_value'=>strtolower( $opt )
							];
						}
					}else{
						foreach( $options as $opt ){
							$add_opts[] = [
								'response_type_id'=>$response_type_id,
								'option_value'=>ucwords( $opt ),
								'option_pass_value'=>strtolower( $opt )
							];
						}
					}

					if( !empty( $new_options ) ){
						$this->db->insert_batch( 'evidoc_response_types_options', $add_opts );
						$this->session->set_flashdata( 'message','Response type options added successfully.' );
						$result = true;
					}

				} else {
					//Use this if you're deleting from the options
					//$new_options  = array_diff( $current_list, array_map( 'strtolower', $options ) );
				}
			}else{
				//Copy options and recall this function
				$table_optons = [
					'table_name'=>'evidoc_response_types',
					'primary_key'=>'response_type_id'
				];
				$new_options = $this->account_service->copy_account_options( $account_id, $table_optons );
				if( !empty( $new_options ) ){
					$this->update_response_options( $account_id, $response_type, $options );
					$result = true;
				}
				$this->session->set_flashdata( 'message','This Response type does not exists or does not belong to you.' );
			}

		} else {
			$this->session->set_flashdata( 'message','Error! Missing required information.' );
		}
		return $result;
	}

	/*
	* Get list of Evidoc Questions for a specific Evidocs type
	*/
	public function get_evidoc_questions( $account_id = false, $search_term = false, $where = false, $limit = null, $offset = 0 ){
		$result = false;
		if( !empty( $account_id ) ){

			$grouped	= false;

			$this->db->select( 'qb.*, seg.segment, seg.segment_group, trim(es.section_name), es.section_ref, es.section_ordering, rt.*, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false )
				->join( 'evidoc_sections es', 'es.section_id = qb.section_id', 'left' )
				->join( 'evidoc_segments seg', 'seg.segment_id = qb.segment_id', 'left' )
				->join( 'evidoc_response_types rt', 'rt.response_type_id = qb.response_type_id', 'left' )
				->join( 'user creater', 'creater.id = qb.created_by', 'left' )
				->join( 'user modifier', 'modifier.id = qb.last_modified_by', 'left' )
				->where( 'qb.is_active', 1 )
				->where( 'qb.account_id', $account_id );

			$where = $raw_where = convert_to_array( $where );

			if( isset( $where['question_id'] ) ){
				$row = $this->db->get_where( 'evidoc_question_bank', [ 'question_id'=>$where['question_id'] ] )->row();
				if( !empty( $row ) ){
					$result = $row;
					$this->session->set_flashdata( 'message','Evidoc Question data found' );
					return $result;
				} else {
					$this->session->set_flashdata( 'message','Evidoc Question data not found' );
					return false;
				}
			}

			if( !empty( $search_term ) ){
				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->evidoc_types_search_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						if( !empty( $search_where['evidoc_question_bank.evidoc_section_id'] ) ){
							$search_where['evidoc_sections.section_name'] =  trim( $term );
							unset( $search_where['evidoc_question_bank.evidoc_section_id'] );
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				}else{
					foreach( $this->evidoc_types_search_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					if( !empty( $search_where['evidoc_question_bank.evidoc_section_id'] ) ){
							$search_where['evidoc_sections.section_name'] =  trim( $search_term );
							unset( $search_where['evidoc_question_bank.evidoc_section_id'] );
						}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( !empty( $where ) ){

				if( isset( $where['audit_type_id'] ) ){
					if( !empty( $where['audit_type_id'] ) ){
						$this->db->where( 'qb.audit_type_id', $where['audit_type_id'] );
					}
					unset( $where['audit_type_id'] );
				}

				if( isset( $where['grouped'] ) ){
					if( !empty( $where['grouped'] ) ){
						$grouped = true;
					}
					unset( $where['grouped'] );
				}

				if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}

			if( !empty( $order_by ) ){
				$this->db->order_by( $order_by );
			}


			if( !empty( $limit ) ){
				$this->db->limit( $limit, $offset );
			}
			$query = $this->db->get( 'evidoc_question_bank qb' );

			if( $query->num_rows() > 0 ){
				if( $grouped ){
					$data = [];
					foreach( $query->result() as $k => $row ){
						$section_name = !empty( $row->section ) ? $row->section : $row->section_name;
						$section_name = trim( strtolower( $section_name ) );
						$data[$section_name][] = $row;
					}
					$result = $data;
				}else{
					$result = $query->result();
				}
				$this->session->set_flashdata( 'message','Evidoc Questions data found' );
			} else {
				$this->session->set_flashdata( 'message','Evidoc Questions data not found' );
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}

		return $result;
	}

	/** Get Evidocs Groups **/
	public function get_evidoc_groups( $account_id = false, $evidoc_group_id = false, $where = false ){

		$result = null;

		if( $evidoc_group_id ){
			$result = $this->db->select( 'evidoc_groups.*', false )
				->get_where( 'evidoc_groups', ['evidoc_group_id'=>$evidoc_group_id, 'evidoc_groups.is_active'=>1 ] )
				->row();
		}else{

			//Uncomment this line if you want to enforce groups by account_id
			// if( $account_id ){
				// $this->db->where( 'evidoc_groups.account_id', $account_id );
			// }else{
				// $this->db->where( '( evidoc_groups.account_id IS NULL OR evidoc_groupss.account_id = "" )' );
			// }

			$query = $this->db->select( 'evidoc_groups.*', false )
				->where( 'evidoc_groups.is_active', 1 )
				->order_by( 'evidoc_groups.evidoc_group_name' )
				->get( 'evidoc_groups' );

			if( $query->num_rows() > 0 ){
				$result = $query->result();
				$this->session->set_flashdata('message','Evidocs group data found');
			}else{
				$this->session->set_flashdata('message','Evidocs group not found');
			}
		}
		return $result;
	}

	/** Get Evidoc Frequencies **/
	public function get_evidoc_frequencies( $account_id = false, $frequency_id = false ){
		$result = null;
		if( $account_id ){
			$this->db->where( 'evidoc_frequencies.account_id', $account_id );
		}else{
			$this->db->where( '( evidoc_frequencies.account_id IS NULL OR evidoc_frequencies.account_id = "" )' );
		}

		if( !empty( $frequency_id ) ){
			$this->db->where( 'asset_categories.frequency_id', $frequency_id );
		}

		$query = $this->db->select( 'evidoc_frequencies.*', false )
			->order_by( 'evidoc_frequencies.frequency_alt' )
			->where( 'evidoc_frequencies.is_active', 1 )
			->get( 'evidoc_frequencies' );

		if( $query->num_rows() > 0 ){
			$result = $query->result();
		}else{
			$result = $this->get_evidoc_frequencies();
		}

		return $result;
	}

	/** Create a NEW Evidoc Type **/
	public function create_evidoc_type( $account_id = false, $evidoc_type_data = false ){

		$result = null;

		if( !empty( $account_id ) && !empty( $evidoc_type_data  ) ){

			foreach( $evidoc_type_data as $col => $value ){
				if( $col == 'audit_type' ){
					$data['alt_audit_type'] = $value;
					$data['audit_type_ref'] = $this->_generate_audit_type_ref( $account_id, $evidoc_type_data );
				}
				$data[$col] = $value;
			}

			$asset_type_id 	= $data['asset_type_id'];
			$category_id 	= $data['category_id'];
			$audit_group 	= $data['audit_group'];
			$audit_frequency= $data['audit_frequency'];

			if( !empty( $data['override_existing'] ) && !empty( $data['audit_type_id'] ) ){
				$override_existing = true;
				//User said override the current record
				$check_exists = $this->db->select( 'audit_types.*, audit_categories.category_name', false )
					->join( 'audit_categories', 'audit_categories.category_id = audit_types.category_id', 'left' )
					->where( 'audit_types.account_id', $account_id )
					->where( 'audit_types.audit_type_id', $data['audit_type_id'] )
					->get( 'audit_types' )->row();

			} else {

				if( !empty( $asset_type_id ) ){
					$this->db->where( 'audit_types.asset_type_id', $asset_type_id );
				}

				unset( $data['audit_type_id'] );
				$check_exists = $this->db->select( 'audit_types.*, audit_categories.category_name', false )
					->join( 'audit_categories', 'audit_categories.category_id = audit_types.category_id', 'left' )
					->where( 'audit_types.account_id', $account_id )
					->where( 'audit_types.category_id', $category_id )
					->where( 'audit_types.audit_group', $audit_group )
					->where( 'audit_types.audit_frequency', $audit_frequency )
					->where( '( audit_types.audit_type = "'.$data['audit_type'].'" OR audit_types.audit_type_ref = "'.$data['audit_type_ref'].'" )' )
					->limit( 1 )
					->get( 'audit_types' )
					->row();
			}

			$data = $this->ssid_common->_filter_data( 'audit_types', $data );

			if( !empty( $check_exists  ) ){

				if( !empty( $override_existing ) ){
					$data['last_modified_by'] = $this->ion_auth->_current_user->id;
					$this->db->where( 'audit_type_id', $check_exists->audit_type_id )
						->update( 'audit_types', $data );
					$this->session->set_flashdata( 'message', 'This Evidoc Type already exists, record has been updated successfully.' );
					$result = $check_exists;
				} else {
					$this->session->set_flashdata( 'message', 'This Evidoc Type already exists, Would you like to override it?' );
					$this->session->set_flashdata( 'already_exists', 'True' );
					$result = $check_exists;
				}

			} else {

				$data['created_by'] = $this->ion_auth->_current_user->id;
				$this->db->insert( 'audit_types', $data );
				$this->session->set_flashdata( 'message', 'Evidoc Type created successfully.' );
				$data['audit_type_id'] = $this->db->insert_id();
				$result = $data;

			}

		}else{
			$this->session->set_flashdata( 'message','Error! Missing required information.' );
		}

		return $result;
	}

	/** Generate Evidoc Type Reference **/
	private function _generate_audit_type_ref( $account_id = false, $data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $data  ) ){
			$reference = $account_id;
			$reference .= ( !empty( $data['audit_type'] ) ) 	? ( strip_all_whitespace( $data['audit_type'] ) ) : '';
			$reference .= ( !empty( $data['discipline_id'] ) ) 	? ( trim( $data['discipline_id'] ) ) : '';
			$reference .= ( !empty( $data['audit_group'] ) ) 	? ( trim( $data['audit_group'] ) ) : '';
			$result = strtoupper( $reference );
		}
		return $result;
	}

	/** Get Evidoc Segments **/
	public function get_evidoc_segments( $account_id = false, $where = false ){

		$result = false;

		if( !empty( $where ) ){

			$single_record = false;
			$where = convert_to_array( $where );

			if( isset( $where['response_type'] ) ){

				if( !empty( $where['response_type'] ) ){
					$row = $this->db->get_where( 'evidoc_response_types', [ 'response_type'=>$where['response_type'] ] )->row();
					if( !empty( $row->response_type ) ){
						$single_record = true;
						$resp_type = strtolower( $row->response_type );
						switch( $resp_type ){
							default:
							case ( in_array( $resp_type, ['input','input_integer','input_text','textarea','radio','checkbox','select','datepicker'] ) ):
								$this->db->where( 'evidoc_segments.segment_id', 1 );//Questions
								break;
							case ( in_array( $resp_type, ['file'] ) ):
								$this->db->where( 'evidoc_segments.segment_id', 2 );//Questions
								break;
							case ( in_array( $resp_type, ['signature'] ) ):
								$this->db->where( 'evidoc_segments.segment_id', 3 );//Questions
								break;
						}
					}

				}
				unset( $where['response_type'] );
			}

			if( !empty( $where ) ){
				$this->db->where( $where );
			}
		}

		if( $account_id ){
			$this->db->where( 'evidoc_segments.account_id', $account_id );
		}else{
			$this->db->where( '( evidoc_segments.account_id IS NULL OR evidoc_segments.account_id = "" )' );
		}

		$query = $this->db->get( 'evidoc_segments' );

		if( $query->num_rows() > 0 ){
			$result = ( !empty( $single_record ) ) ? $query->result()[0] : $query->result();
			$this->session->set_flashdata( 'message','Evidoc Segment(s) data found.' );
		}else{
			$this->session->set_flashdata( 'message','Evidoc Segment(s) data not found.' );
		}
		return $result;
	}

	/** Add a NEW Evidoc Section **/
	public function add_new_section( $account_id = false, $evidoc_section_data = false ){

		$result = null;

		if( !empty( $account_id ) && !empty( $evidoc_section_data  ) ){

			foreach( $evidoc_section_data as $col => $value ){
				if( $col == 'section_name' ){
					$data['section_name'] = trim( $value );
					$data['section_ref']  = strtolower( strip_all_whitespace( $value ) );
				}
				$data[$col] = trim($value);
			}

			if( !empty( $data['override_existing'] ) && !empty( $data['section_id'] ) ){
				//User said override the current record
				$check_exists = $this->db->where( 'account_id', $account_id )
					->where( 'section_id', $data['section_id'] )
					->get( 'evidoc_sections' )->row();

			} else {
				unset( $data['section_id'] );
				$check_exists = $this->db->where( 'account_id', $account_id )
					->where( '( evidoc_sections.section_name = "'.trim($data['section_name']).'" OR evidoc_sections.section_ref = "'.trim($data['section_ref']).'" )' )
					->limit( 1 )
					->get( 'evidoc_sections' )
					->row();
			}

			$data = $this->ssid_common->_filter_data( 'evidoc_sections', $data );

			if( !empty( $check_exists  ) ){
				$data['last_modified_by'] = $this->ion_auth->_current_user->id;
				$this->db->where( 'section_id', $check_exists->section_id )
					->update( 'evidoc_sections', $data );
					$this->session->set_flashdata( 'message', 'This Evidoc section already exists, record has been updated successfully.' );
					$result = $check_exists;
			}else{

				## Set ordering
				$get_last_order = $this->db->select( 'section_ordering', false )
					->order_by( 'section_ordering DESC' )
					->limit( 1 )
					->get_where( 'evidoc_sections', [ 'account_id' => $account_id ] )
					->row();

				$data['section_ordering'] 	= ( !empty( $data['section_ordering'] ) ) ? ( int ) $data['section_ordering'] : ( !empty( $get_last_order->section_ordering ) ? ( $get_last_order->section_ordering + 1 ) : 1 );
				$data['created_by'] 		= $this->ion_auth->_current_user->id;
				$this->db->insert( 'evidoc_sections', $data );
				$this->session->set_flashdata( 'message', 'New Evidoc section added successfully.' );
				$data['section_id'] = $this->db->insert_id();
				$result = $data;
			}

		}else{
			$this->session->set_flashdata( 'message','Error! Missing required information.' );
		}

		return $result;
	}

	/** Get Evidoc Sections **/
	public function get_evidoc_sections( $account_id = false, $where = false ){
		$result = null;
		if( $account_id ){

			$this->db->where( 'evidoc_sections.account_id', $account_id );

			if( !empty( $where ) ){

				$where = $raw_where = convert_to_array( $where );

				if( isset( $where['section_id'] ) || isset( $where['section_ref'] ) ){

					$ref_condition = ( !empty( $where['section_id'] ) ) ? [ 'section_id'=>$where['section_id'] ] : ( !empty( $where['section_ref'] ) ?  [ 'section_ref'=>$where['section_ref'] ] : false );

					if( !empty( $ref_condition ) ){
						$row = $this->db->get_where( 'evidoc_sections', $ref_condition )->row();
						if( !empty( $row ) ){
							$result = $row;
							$this->session->set_flashdata( 'message','Evidoc Section data found' );
							return $result;
						} else {
							$this->session->set_flashdata( 'message','Evidoc Section data not found' );
							return false;
						}
					}
					unset( $where['section_id'], $where['section_ref'] );
				}

			}

			$query = $this->db->select( 'evidoc_sections.*', false )
				->order_by( 'evidoc_sections.section_ordering, evidoc_sections.section_name' )
				->where( 'evidoc_sections.is_active', 1 )
				->get( 'evidoc_sections' );

			if( $query->num_rows() > 0 ){
				$result = $query->result();
				$this->session->set_flashdata( 'message','Evidoc Sections data found.' );
			}else{
				$this->session->set_flashdata( 'message','Evidoc Sections data not found.' );
			}

		} else {

			$this->session->set_flashdata( 'message','Error! Missing required information.' );

		}
		return $result;
	}

	/** Get Evidoc Response Types **/
	public function get_response_types( $account_id = false, $where = false ){

		$result = null;

		if( $account_id ){

			$this->db->where( 'evidoc_response_types.account_id', $account_id );

			if( !empty( $where ) ){

				$where = convert_to_array( $where );

				if( isset( $where['response_type_id'] ) ||  isset( $where['response_type'] ) ){

					$ref_condition = ( !empty( $where['response_type_id'] ) ) ? [ 'response_type_id'=>$where['response_type_id'] ] : ( !empty( $where['response_type'] ) ?  [ 'response_type'=>$where['response_type'] ] : false );

					if( !empty( $ref_condition ) ){
						$row = $this->db->get_where( 'evidoc_response_types', $ref_condition )->row();
						if( !empty( $row ) ){
							$resp_options 				= $this->_get_response_type_options( $row->response_type_id );
							$row->response_type_options = ( !empty( $resp_options ) ) ? $resp_options : null;
							$result = $row;
							$this->session->set_flashdata( 'message','Evidoc Response types data found' );
							return $result;
						} else {
							$this->session->set_flashdata( 'message','Evidoc Response types data not found' );
							return false;
						}
					}
					unset( $where['response_type_id'] );
				}

			}

			$query = $this->db->select( 'evidoc_response_types.*', false )
				->order_by( 'evidoc_response_types.response_type_ordering, evidoc_response_types.response_type_alt' )
				->where( 'evidoc_response_types.is_active', 1 )
				->get( 'evidoc_response_types' );

			if( $query->num_rows() > 0 ){
				$data = ( object )[];
				foreach( $query->result() as $k => $row ){
					$resp_options = $this->_get_response_type_options( $row->response_type_id );
					$row->response_type_options = ( !empty( $resp_options ) ) ? $resp_options : null;
					$data->{$row->response_type_id} = $row;
				}
				$result = $data;
				$this->session->set_flashdata( 'message','Evidoc Response types data found.' );
			}else{

				$query = $this->db->select( 'evidoc_response_types.*', false )
					->order_by( 'evidoc_response_types.response_type_ordering, evidoc_response_types.response_type_alt' )
					->where( '( evidoc_response_types.account_id IS NULL OR evidoc_response_types.account_id = "" )' )
					->where( 'evidoc_response_types.is_active', 1 )
					->get( 'evidoc_response_types' );

					if( $query->num_rows() > 0 ){
						$data = ( object )[];
						foreach( $query->result() as $k => $row ){
							$resp_options = $this->_get_response_type_options( $row->response_type_id );
							$row->response_type_options = ( !empty( $resp_options ) ) ? $resp_options : null;
							$data->{$row->response_type_id} = $row;
						}
						$result = $data;
						$this->session->set_flashdata( 'message','Evidoc Response types data found.' );
					} else {
						$this->session->set_flashdata( 'message','Evidoc Response types data not found.' );
					}

			}

		} else {

			$this->session->set_flashdata( 'message','Error! Missing required information.' );

		}
		return $result;
	}

	/** Get list of all options attached to a Response type **/
	private function _get_response_type_options( $response_type_id = false ){

		$result = false;

		if( !empty( $response_type_id ) ){
			$query = $this->db->select( 'opts.*', false )
				->where( 'opts.response_type_id', $response_type_id )
				->order_by( 'opts.option_ordering' )
				->get( 'evidoc_response_types_options opts' );

			if( $query->num_rows() > 0 ){
				$result = $query->result();
				$this->session->set_flashdata( 'message','Evidoc Response types data found.' );
			}else{
				$this->session->set_flashdata( 'message','Evidoc Response types data not found.' );
			}
		}else{
			$this->session->set_flashdata( 'message','Response type ID is a mandatory field.' );
		}

		return $result;
	}

	/** Get Question ordering **/
	private function _get_question_ordering( $account_id = false, $audit_type_id = false, $section = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $audit_type_id ) ){

			if( !empty( $section ) ){
				$this->db->where( 'section', $section );
			}

			$row = $this->db->select( 'ordering', false )
				->order_by( 'ordering DESC' )
				->limit( 1 )
				->get_where( 'audit_question_bank', [ 'account_id' => $account_id, 'audit_type_id'=>$audit_type_id ] )
				->row();

			$result 	= ( !empty( $row->ordering ) ) ? ( $row->ordering + 1 ) : 1;

		}
		return $result;
	}

	/** Get list of Sections **/
	public function get_evidoc_type_sections( $account_id = false, $where = false ){
		$result = false;
		if( !empty( $account_id ) ){
			$this->db->select( 'DISTINCT (section) `section_name`, section_ref', false )
				->where( 'qb.account_id', $account_id );

			if( !empty( $where ) ){

				$condition 	= [];
				$where 		= convert_to_array( $where );

				if( isset( $where['audit_type_id'] ) ){
					if( !empty( $where['audit_type_id'] ) ){
						$this->db->where( 'qb.audit_type_id', $where['audit_type_id'] );
						$condition = ['audit_type_id'=>$where['audit_type_id']];
					}
					unset( $where['audit_type_id'] );
				}

				if( isset( $where['asset_type_id'] ) ){
					if( !empty( $where['asset_type_id'] ) ){
						$this->db->where( 'qb.asset_type_id', $where['asset_type_id'] );
					}
					unset( $where['asset_type_id'] );
				}
			}

			$query = $this->db->order_by( 'section' )
				->get( 'audit_question_bank qb' );

			if( $query->num_rows() > 0 ){

				$q_sections = array_column( $query->result_array(), 'section_ref' );

				$this->db->select( 'section_id, section_name, section_ref', false )
					->where_in( 'section_ref', $q_sections );

				if( !empty( $condition ) ){
					$this->db->or_where( $condition );
				}

				$query = $this->db->group_by( 'evidoc_sections.section_ref' )
					->get( 'evidoc_sections' );

				if( $query->num_rows() > 0 ){
				 	$result = $query->result();
					$this->session->set_flashdata( 'message','Evidoc Type Sections data found.' );
				}
			}else{
				$this->session->set_flashdata( 'message','Evidoc Type Sections data not found.' );
			}
		}
		return $result;
	}

	/** Update an existing Evidoc name **/
	public function update_evidoc_name( $account_id = false, $audit_type_id = false, $update_data = false  ){
		$result = false;
		if( !empty( $account_id ) && !empty( $audit_type_id )  && !empty( $update_data ) ){

			$ref_condition 			= [ 'account_id'=>$account_id, 'audit_type_id'=>$audit_type_id ];
			$job_type_id   			= ( !empty( $update_data['job_type_id'] ) )? $update_data['job_type_id'] 	: false;
			$update_data   			= $this->ssid_common->_data_prepare( $update_data );
			$update_data   			= $this->ssid_common->_filter_data( 'audit_types', $update_data );
			$record_pre_update 		= $this->db->get_where( 'audit_types', [ 'account_id'=>$account_id, 'audit_type_id'=>$audit_type_id ] )->row();

			if( !empty( $record_pre_update ) ){

				$category_id 			= ( !empty( $update_data['category_id'] ) ) 		? $update_data['category_id'] 			: false;
				$audit_group 			= ( !empty( $update_data['audit_group'] ) ) 		? $update_data['audit_group'] 			: false;
				$audit_frequency		= ( !empty( $update_data['audit_frequency'] ) ) 	? $update_data['audit_frequency'] 		: false;
				$asset_type_id  		= ( !empty( $update_data['asset_type_id'] ) ) 		? $update_data['asset_type_id'] 		: false;

				if( !empty( $asset_type_id ) ){
					$quick_update_data = [ 'asset_type_id'=>$asset_type_id ];
					$this->db->where( 'audit_types.asset_type_id', $asset_type_id );
				}

				if( strtolower( $audit_group ) != 'asset' ){
					$quick_update_data 				= [ 'asset_type_id'=>null ];
					$update_data['asset_type_id']	= null;
				}

				$check_conflict = $this->db->select( 'audit_type_id', false )
					->where( 'audit_types.account_id', $account_id )
					->where( 'audit_types.category_id', $category_id )
					->where( 'audit_types.audit_group', $audit_group )
					->where( 'audit_types.audit_frequency', $audit_frequency )
					->where( 'audit_types.audit_type_id !=', $audit_type_id )
					->where( 'audit_type', $update_data['audit_type'] )
					->limit( 1 )
					->get( 'audit_types' )
					->row();

				if( !$check_conflict ){

					$update_data['last_modified_by'] = $this->ion_auth->_current_user->id;
					$this->db->where( $ref_condition )
						->update( 'audit_types', $update_data );

					## Update linked Job Type with the correct contract_id
					if( isset( $update_data['contract_id'] ) && !empty( $job_type_id ) ){
						$this->db->where( 'job_type_id', $job_type_id )
							->update( 'job_types', [ 'contract_id'=>$update_data['contract_id'] ] );
					}

					$updated_record = $this->get_evidoc_types( $account_id, false, ['audit_type_id'=>$audit_type_id ] );
					$result = ( !empty( $updated_record->records ) ) ? $updated_record->records : ( !empty( $updated_record ) ? $updated_record : false );

					if( !empty( $result ) ){
						##If the Group has changed, updated the Questions
						if( strtolower( $record_pre_update->audit_group ) != $audit_group ){
							if( strtolower( $audit_group ) == 'asset' ){
								if( !empty( $asset_type_id ) ){
									$quick_update_data = [ 'asset_type_id'=>$asset_type_id ];
								}
							} else {
								$quick_update_data = [ 'asset_type_id'=>null ];
							}
						}

						if( !empty( $quick_update_data )  ){
							$this->_quick_evidoc_name_update( $account_id, $audit_type_id, $quick_update_data );
						}
					}

					$this->session->set_flashdata( 'message', 'Evidoc name updated successfully' );
					return $result;
				}else{
					$this->session->set_flashdata( 'message', 'This Evidoc name already exists for your account. Update request aborted' );
					return false;
				}

			} else {
				$this->session->set_flashdata( 'message', 'This Evidoc name record does not exist or does not belong to you.' );
				return false;
			}

		}else{
			$this->session->set_flashdata( 'message','Your request is missing requireed information.' );
		}
		return $result;
	}

	/*
	*	Get list of Category sets and search through it
	*/
	public function get_audit_categories( $account_id = false, $category_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){

		$result = false;

		if( !empty( $account_id ) ){
			$this->db->select( 'audit_categories.*, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false )
				->join( 'user creater', 'creater.id = audit_categories.created_by', 'left' )
				->join( 'user modifier', 'modifier.id = audit_categories.last_modified_by', 'left' )
				->where( 'audit_categories.is_active', 1 )
				->where( 'audit_categories.account_id', $account_id );

				$where = $raw_where = convert_to_array( $where );

			if( !empty( $category_id ) || isset( $where['category_id'] ) ){
				$category_id	= ( !empty( $category_id ) ) ? $category_id : $where['category_id'];
				if( !empty( $category_id ) ){

					$row = $this->db->get_where( 'audit_categories', ['audit_categories.category_id'=>$category_id ] )->row();

					if( !empty( $row ) ){
						$result  				= $row;
						$this->session->set_flashdata( 'message','Evidoc Categories data found' );
						return $result;
					} else {
						$this->session->set_flashdata( 'message','Evidoc Categories data not found' );
						return false;
					}
				}
				unset( $where['category_id'], $where['category_ref'] );
			}

			if( !empty( $search_term ) ){
				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->audit_categories_search_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				} else {
					foreach( $this->audit_categories_search_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( !empty( $where ) ){

				if( isset( $where['category_name'] ) ){
					if( !empty( $where['category_name'] ) ){
						$category_ref = strtolower( strip_all_whitespace( $where['category_name'] ) );
						$this->db->where( '( audit_categories.category_name = "'.$where['category_name'].'" OR audit_categories.category_ref = "'.$category_ref.'" )' );
					}
					unset( $where['category_name'] );
				}

				if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}

			if( !empty( $order_by ) ){
				$this->db->order_by( $order_by );
			} else {
				$this->db->order_by( 'category_name' );
			}

			if( $limit > 0 ){
				$this->db->limit( $limit, $offset );
			}

			$query = $this->db->get( 'audit_categories' );


			if( $query->num_rows() > 0 ){

				$result_data = $query->result();

				$result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
				$result->records 			= $result_data;
				$counters 					= $this->get_audit_categories_totals( $account_id, $search_term, $raw_where );
				$result->counters->total 	= ( !empty( $counters->total ) ) ? $counters->total : null;
				$result->counters->pages 	= ( !empty( $counters->pages ) ) ? $counters->pages : null;
				$result->counters->limit  	= ( !empty( $apply_limit ) ) ? $limit : $result->counters->total;
				$result->counters->offset 	= $offset;

				$this->session->set_flashdata( 'message','Categories data found' );
			} else {
				$this->session->set_flashdata( 'message','There\'s currently no Categories setup for your Account' );
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}

		return $result;
	}


	/** Get Category lookup counts **/
	public function get_audit_categories_totals( $account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT ){
		$result = false;
		if( !empty( $account_id ) ){

			$this->db->select( 'audit_categories.category_id', false )
				->where( 'audit_categories.is_active', 1 )
				->where( 'audit_categories.account_id', $account_id );

			$where = $raw_where = convert_to_array( $where );

			if( !empty( $search_term ) ){
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->audit_categories_search_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				} else {
					foreach( $this->audit_categories_search_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( !empty( $where ) ){

				if( isset( $where['category_name'] ) ){
					if( !empty( $where['category_name'] ) ){
						$category_ref = strtoupper( strip_all_whitespace( $where['category_name'] ) );
						$this->db->where( '( audit_categories.category_name = "'.$where['category_name'].'" OR audit_categories.category_ref = "'.$category_ref.'" )' );
					}
					unset( $where['category_name'] );
				}

				if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}

			$query 			  = $this->db->from( 'audit_categories' )->count_all_results();
			$results['total'] = !empty( $query ) ? $query : 0;
			$limit 				= ( $limit > 0 ) ? $limit : $results['total'];
			$results['pages'] = !empty( $query ) ? ceil( $query / $limit ) : 0;
			return json_decode( json_encode( $results ) );
		}
		return $result;
	}



	/** Add a NEW Evidoc Category **/
	public function add_category( $account_id = false, $evidoc_category_data = false ){

		$result = null;

		if( !empty( $account_id ) && !empty( $evidoc_category_data  ) ){

			foreach( $evidoc_category_data as $col => $value ){
				if( $col == 'category_name' ){
					$data['category_ref'] 		= strtolower( lean_string( $value ) );
					$data['category_group'] 	= ucwords( strtolower( $value ) );
					$data['category_name_alt'] 	= $value;
				}
				$data[$col] = $value;
			}

			if( !empty( $data['override_existing'] ) && !empty( $data['category_id'] ) ){
				//User said override the current record
				$check_exists = $this->db->where( 'account_id', $account_id )
					->where( 'category_id', $data['category_id'] )
					->get( 'audit_categories' )->row();

			} else {
				unset( $data['category_id'] );
				$check_exists = $this->db->where( 'account_id', $account_id )
					->where( '( audit_categories.category_name = "'.$data['category_name'].'" OR audit_categories.category_ref = "'.$data['category_ref'].'" )' )
					->limit( 1 )
					->get( 'audit_categories' )
					->row();
			}

			$data = $this->ssid_common->_filter_data( 'audit_categories', $data );

			if( !empty( $check_exists  ) ){
				$data['last_modified_by'] = $this->ion_auth->_current_user->id;
				$this->db->where( 'category_id', $check_exists->category_id )
					->update( 'audit_categories', $data );
					$this->session->set_flashdata( 'message', 'This Evidoc category already exists, record has been updated successfully.' );
					$result = $check_exists;
			}else{
				$data['created_by'] 		= $this->ion_auth->_current_user->id;
				$this->db->insert( 'audit_categories', $data );
				$this->session->set_flashdata( 'message', 'New Evidoc category added successfully.' );
				$data['category_id'] = (string) $this->db->insert_id();
				$result = $data;
			}

		}else{
			$this->session->set_flashdata( 'message','Error! Missing required information.' );
		}

		return $result;
	}

	/** Update Evidoc Category **/
	public function update_category( $account_id = false, $evidoc_category_data = false ){

		$result = null;

		if( !empty( $account_id ) && !empty( $evidoc_category_data['category_id'] ) && !empty( $evidoc_category_data ) ){

			foreach( $evidoc_category_data as $col => $value ){
				if( $col == 'category_name' ){
					$data['category_ref'] 		= strtolower( lean_string( $value ) );
					$data['category_group'] 	= ucwords( strtolower( $value ) );
					$data['category_name_alt'] 	= $value;
				}
				$data[$col] = $value;
			}

			if( !empty( $data['category_id'] ) ){
				$check_exists = $this->db->where( 'account_id', $account_id )
					->where( 'category_id', $data['category_id'] )
					->get( 'audit_categories' )->row();

				$data = $this->ssid_common->_filter_data( 'audit_categories', $data );

				if( !empty( $check_exists  ) ){
					$data['last_modified_by'] = $this->ion_auth->_current_user->id;
					$this->db->where( 'category_id', $check_exists->category_id )
						->update( 'audit_categories', $data );
						if( $this->db->trans_status() !== false ){
							$result = $this->get_audit_categories( $account_id, $data['category_id'] );
							$this->session->set_flashdata( 'message', 'Evidoc category updated successfully.' );
						}

				}else{
					$this->session->set_flashdata( 'message', 'This Evidoc category does not exists or does not belong to you.' );
					$result = false;
				}
			} else {
				$this->session->set_flashdata( 'message','Error! Missing required information.' );
			}

		}else{
			$this->session->set_flashdata( 'message','Error! Missing required information.' );
		}

		return $result;
	}


	/*
	* Delete Category record
	*/
	public function delete_audit_category( $account_id = false, $category_id = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $category_id ) ){
			$conditions 		= ['account_id'=>$account_id,'category_id'=>$category_id];
			$category_item_exists 	= $this->db->get_where( 'audit_categories',$conditions )->row();
			if( !empty( $category_item_exists ) ){

				$this->db->where( $conditions )
					->update( 'audit_categories', [
						'is_active'		=>0,
						'category_name'	=>$category_item_exists->category_name.' (Archived)',
						'category_ref'	=> strip_all_whitespace( $category_item_exists->category_name.' (Archived)' ),
					] );

				if( $this->db->trans_status() !== FALSE ){
					$this->session->set_flashdata('message','Evidoc Category deleted successfully.');
					$result = true;
				}
			}else{
				$this->session->set_flashdata('message','Invalid Category ID');
			}

		}else{
			$this->session->set_flashdata('message','No Category record found.');
		}
		return $result;
	}


	/*
	* Delete Question record
	*/
	public function delete_question( $account_id = false, $question_id = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $question_id ) ){
			$conditions 		= [ 'account_id'=>$account_id,'question_id'=>$question_id ];
			$question_exists 	= $this->db->get_where( 'audit_question_bank', $conditions )->row();
			if( !empty( $question_exists ) ){
				$this->db->where( $conditions )->delete( 'audit_question_bank' );
				if( $this->db->trans_status() !== FALSE ){
					$this->ssid_common->_reset_auto_increment( 'audit_question_bank', 'question_id' );
					$this->session->set_flashdata( 'message','Record deleted successfully.' );
					$result = true;
				}
			}else{
				$this->session->set_flashdata( 'message','This question does not exist or does not belong to you' );
			}

		}else{
			$this->session->set_flashdata( 'message','Error! Missing required information.' );
		}
		return $result;
	}

	/** Quick Evidoc update **/
	public function _quick_evidoc_name_update( $account_id = false, $audit_type_id = false, $data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $audit_type_id ) && !empty( $data ) ){

			$evidoc_data 	= $this->ssid_common->_filter_data( 'audit_types', $data );
			$questions_data = $this->ssid_common->_filter_data( 'audit_question_bank', $data );
			$this->db->where( 'audit_types.account_id', $account_id )
				->where( 'audit_types.audit_type_id', $audit_type_id )
				->update( 'audit_types', $evidoc_data );
			if( $this->db->trans_status() !== false ){
				//update questions
				$this->db->where( 'qb.account_id', $account_id )
				->where( 'qb.audit_type_id', $audit_type_id )
				->update( 'audit_question_bank `qb`', $questions_data );
			}
			$result = ( $this->db->trans_status() !== false ) ? true : false;
		}
		return $result;
	}


	/**
	* Do a quick update to the Asset
	**/
	private function _quick_asset_update( $account_id = false, $asset_id = false, $data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $asset_id ) && !empty( $data ) ){
			$asset_data = $this->ssid_common->_filter_data( 'asset', $data );
			$this->db->where( 'account_id', $account_id )
				->where( 'asset_id', $asset_id )
				->update( 'asset', $asset_data );
			if( $this->db->affected_rows() > 0 ){
				$result = true;
			}
		}
		return $result;
	}

	/** Set Asset compliance status **/
	private function update_asset_compliance_status( $account_id = false, $audit_id = false, $data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $audit_id ) ){
			$audit_result_status_id	= $data['audit_result_status_id'];
			$update_asset_data = [
				'audit_result_status_id'=>$audit_result_status_id
			];

			$audit_row = $this->db->select( 'audit.asset_id, audit.site_id, audit.audit_status', false )
				->get_where( 'audit', [ 'account_id'=>$account_id, 'audit_id'=>$audit_id ] )->row();

			if( !empty( $update_asset_data ) && !empty( $audit_row->asset_id ) ){
				$result = $this->_quick_asset_update( $account_id, $audit_row->asset_id , $update_asset_data );
			}
		}
		return $result;
	}

	/**
	* Set Site compliance status
	**/
	private function update_site_compliance_status( $account_id = false, $audit_id = false, $data = false ){

		if( !empty( $account_id ) && !empty( $audit_id ) ){

			$audit_result_status_id	= $data['audit_result_status_id'];
			$update_site_data = [
				'audit_result_status_id'=>$audit_result_status_id
			];

			$audit_row = $this->db->select( 'audit.asset_id, audit.site_id, audit.audit_status', false )
				->get_where( 'audit', [ 'account_id'=>$account_id, 'audit_id'=>$audit_id ] )->row();

			if( !empty( $audit_row->site_id ) ){

				$site_id = $audit_row->site_id;

			} else if ( $audit_row->asset_id ){
				$asset_details = $this->db->select( 'asset.asset_id, asset.site_id', false )
				->get_where( 'asset', [ 'account_id'=>$account_id, 'asset_id'=>$audit_row->asset_id ] )->row();

				$site_id = ( !empty( $asset_details->site_id ) ) ? $asset_details->site_id : false;
			}

			if( !empty( $update_site_data ) && !empty( $site_id ) ){
				$site_data 								= $this->check_asset_compliance( $account_id, $site_id );
				$site_data['audit_result_timestamp'] 	= date( 'Y-m-d H:i:s' );
				$this->_quick_site_update( $account_id, $site_id , $site_data );
			}
		}
		return true;
	}


	private function check_asset_compliance( $account_id = false, $site_id = false ){

		if( !empty( $account_id ) && !empty( $site_id ) ){

			$grouped_statuses 	= [];
			$stats_data 		= [ 'grand_total'=> 0 ];

			$result_statuses  = $this->db->select( 'audit_result_statuses.audit_result_status_id, audit_result_statuses.result_status, audit_result_statuses.result_status_group' )
				->order_by( 'audit_result_statuses.result_ordering' )
				->group_by( 'audit_result_statuses.audit_result_status_id' )
				->get_where( 'audit_result_statuses', [ 'account_id'=>$account_id ] );

			if( $result_statuses->num_rows() > 0 ){

				foreach( $result_statuses->result() as $k => $row ){

					if( strtolower( $row->result_status_group )  == 'not_set' ){
						$group_not_set = true;
					}

					$audit_result_status_id = $row->audit_result_status_id;

					if( !empty( $group_not_set ) ){
						$this->db->select( 'SUM( CASE WHEN ( asset.audit_result_status_id = 0 OR asset.audit_result_status_id IS NULL ) THEN 1 ELSE 0 END ) AS status_not_set', false );
					} else {
						$this->db->select( 'SUM( CASE WHEN asset.audit_result_status_id = "'.$audit_result_status_id.'" THEN 1 ELSE 0 END ) `status_total`', false );
					}

					$this->db->where( 'asset.site_id', $site_id );

					$query = $this->db->where( 'asset.account_id', $account_id )
						->where( 'asset.archived !=', 1 )
						->order_by( 'asset.asset_id' )
						->get( 'asset' );

					if( $query->num_rows() > 0 ){
						$total_records = !empty( $query->result()[0]->status_total ) ? $query->result()[0]->status_total : ( !empty( $query->result()[0]->status_not_set ) ? $query->result()[0]->status_not_set : 0 );

						#$stats_data[$row->result_status_group]  = array_merge( (array) $row, [ 'status_total'=>( string )$total_records ] );
						$stats_data[$row->result_status_group]  = ( string )$total_records;
					}

					$grouped_statuses[$row->result_status_group] = $row;
				}

				$stats_data['grand_total']  = array_sum( $stats_data );
			}

			if( !empty( $stats_data ) ){
				if( $stats_data['passed'] == $stats_data['grand_total']  ){

					$update_site_data = [
						'audit_result_status_id'=>$grouped_statuses['passed']->audit_result_status_id
					];
				} else if ( $stats_data['failed'] >= 1 ){
					$update_site_data = [
						'audit_result_status_id'=>$grouped_statuses['failed']->audit_result_status_id
					];
				} else if ( $stats_data['recommendations'] >= 1 ){
					$update_site_data = [
						'audit_result_status_id'=>$grouped_statuses['recommendations']->audit_result_status_id
					];
				} else if ( $stats_data['not_set'] >= 1 ){
					$update_site_data = [
						'audit_result_status_id'=>$grouped_statuses['not_set']->audit_result_status_id
					];
				}
			}
		}
		$result = !empty( $update_site_data ) ? $update_site_data : false;

		return $result;
	}


	/**
	*	Get Evidocs progress statuses
	**/
	public function get_audit_progress_statuses( $account_id = false, $where = false ){
		$result = null;

		if(!empty($account_id)){

			$where 			= convert_to_array( $where );
			$contract_id	= !empty( $where['contract_id'] ) ? $where['contract_id'] : false;
			
			if( !empty( $contract_id ) ){
				
				$date_from = !empty( $where['date_from'] )	? date( 'Y-m-d', strtotime( $where['date_from'] ) ) : date( 'Y-m-d', strtotime( 'Jan 01' ) );
				$date_to = !empty( $where['date_to'] )		? date( 'Y-m-d', strtotime( $where['date_to'] ) ) 	: date( 'Y-m-d', strtotime( 'Dec 31' ) );
			
				$this->db->where( "audit_types.contract_id", $contract_id );
				$this->db->where( "audit.date_created >=", $date_from );
				$this->db->where( "audit.date_created <=", $date_to );
			}

			$this->db->select( "DISTINCT( audit_status )", false );

			$this->db->where( "audit.account_id", $account_id );

			$arch_where = "( audit.archived is NULL or audit.archived ='' )";
			$this->db->where( $arch_where );

			$query = $this->db->join( 'audit_types', 'audit_types.audit_type_id = audit.audit_type_id' )
				->get("audit");

			if( $query->num_rows() > 0 ){
				$this->session->set_flashdata('message', 'Status(es) found');
				foreach( $query->result() as $key => $value ){
					$result[] = $value->audit_status;
				}
			}else{
				$this->session->set_flashdata('message','Status(es) not found');
			}

		}else{
			$this->session->set_flashdata('message','Your request is missing required information');
		}

		return $result;
	}


	/**
	*	Get Evidocs exception statuses
	**/
	public function get_exception_statuses( $account_id = false, $action_status_id = false, $where = false ){
		$result = null;

		if(!empty( $account_id )){

			if( !empty( $action_status_id ) ){
				$this->db->where( "aas.action_status_id", $action_status_id );
			}

			$this->db->select( "aas.*", false );
			$this->db->where( "aas.account_id", $account_id );
			$this->db->where( "aas.is_active", 1 );

			$query = $this->db->get("audit_action_statuses `aas`");

			if( $query->num_rows() > 0 ){
				$this->session->set_flashdata('message', 'Status(es) found');

				$dataset = $query->result();
				if( !empty( $action_status_id ) ){
					$result = $dataset[0];
				} else {
					$result = $dataset;
				}
				$this->session->set_flashdata('message','Status(es) found');
			}else{
				$this->session->set_flashdata('message','Status(es) not found');
			}

		}else{
			$this->session->set_flashdata('message','Your request is missing required information');
		}

		return $result;
	}


	## $exception_logs = $this->evidocs_service->get_exception_log( $account_id, $exception_log_id, $where, $order_by, $limit, $offset );
	public function get_exception_log( $account_id = false, $exception_log_id = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;
		if( !empty( $account_id ) ){

			$this->db->select( "audit_exceptions_log.*", false);
			$this->db->select( "CONCAT( creator.first_name,' ',creator.last_name ) `created_by_full_name`", false );
			## $this->db->select( "CONCAT( modifier.first_name,' ',modifier.last_name ) `last_modified_by_full_name`", false );

			$this->db->join( 'user creator','creator.id = audit_exceptions_log.created_by','left' );
			## $this->db->join( 'user modifier','modifier.id = audit_exceptions_log.last_modified_by','left' );

			$this->db->where( "audit_exceptions_log.account_id", $account_id );
			$arch_where = "( audit_exceptions_log.archived is NULL or audit_exceptions_log.archived ='' )";
			$this->db->where( $arch_where );

			if( !empty( $exception_log_id ) ){
				$this->db->where( "audit_exceptions_log.log_id", $exception_log_id );
			}

			if( !empty( $where ) ){
				$where = convert_to_array( $where );

				if( !empty( $where['exception_id'] ) ){
					$exception_id = $where['exception_id'];
					$this->db->where( "audit_exceptions_log.exception_id", $exception_id  );
					unset( $where['exception_id'] );
				}

				if( !empty( $where['audit_id'] ) ){
					$audit_id = $where['audit_id'];
					$this->db->where( "audit_exceptions_log.audit_id", $audit_id  );
					unset( $where['audit_id'] );
				}

				if( !empty( $where['site_id'] ) ){
					$site_id = $where['site_id'];
					$this->db->where( "audit_exceptions_log.site_id", $site_id  );
					unset( $where['site_id'] );
				}

				if( !empty( $where['asset_id'] ) ){
					$asset_id = $where['asset_id'];
					$this->db->where( "audit_exceptions_log.asset_id", $asset_id  );
					unset( $where['asset_id'] );
				}

				if( !empty( $where['vehicle_reg'] ) ){
					$vehicle_reg = $where['vehicle_reg'];
					$this->db->where( "audit_exceptions_log.vehicle_reg", $vehicle_reg  );
					unset( $where['vehicle_reg'] );
				}
			}

			$query = $this->db->get( "audit_exceptions_log" );

			if( ( null !== $query->num_rows() ) && $query->num_rows() > 0 ){
				$dataset = $query->result();

				if( !empty( $exception_log_id ) ){
					$result = $dataset[0];
				} else {
					$result = $dataset;
					$this->session->set_flashdata('message','Log(s) found');
				}
			} else {
				$this->session->set_flashdata('message','Log(s) not found');
			}

		} else {
			$this->session->set_flashdata('message','Your request is missing required information');
		}

		return $result;
	}

	/*
	* Get additional information required for Display on an Evidoc
	*/
	private function get_additional_info( $account_id = false, $augit_group = false, $where = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $augit_group ) && !empty( $where ) ){

			switch( $augit_group ){
				case 'customer':
					$customer_id = !empty( $where['customer_id'] ) ? $where['customer_id'] : false;
					if( !empty( $customer_id ) ){
						$row = $this->db->select( 'c.salutation, c.customer_id, c.customer_first_name, c.customer_last_name, trim( concat( c.salutation," ",c.customer_first_name," ",c.customer_last_name ) ) as `customer_full_name`, c.customer_email, c.customer_mobile, c.customer_main_telephone, c.address_id, a.postcode, a.summaryline `customer_address`',false )
							->join ( 'addresses a', 'a.main_address_id = c.address_id', 'left' )
							->where( 'archived !=', 1 )
							->where( 'account_id',$account_id )
							->where( 'customer_id',$customer_id )
							->get( 'customer `c`' )
							->row();

						$result = !empty( $row ) ? $row : false;
					}
					break;

				case 'asset':
					$asset_id = !empty( $where['asset_id'] ) ? $where['asset_id'] : false;
					if( !empty( $asset_id ) ){
						$row = $this->db->select( 'asset.*, ata.attribute_name `primary_attribute`, atr.attribute_value, ata.is_mobile_visible, site.site_name, site.site_postcodes, site.site_address_id `address_id`, site_sub_blocks.sub_block_name, site_zones.zone_name, site_locations.location_name, asset_statuses.status_name `asset_status`, asset_statuses.status_group `asset_status_group`, asset_types.asset_type, asset_types.asset_group', false )
							->join( 'asset_types', 'asset_types.asset_type_id = asset.asset_type_id', 'left' )
							->join( 'asset_type_attributes ata', 'ata.attribute_id = asset_types.primary_attribute_id', 'left' )
							->join( 'asset_attributes atr', 'atr.attribute_id = ata.attribute_id AND `atr`.`asset_id` = `asset`.`asset_id`', 'left' )
							->join( 'asset_statuses', 'asset_statuses.status_id = asset.status_id', 'left' )
							->join( 'site', 'site.site_id = asset.site_id', 'left' )
							->join( 'site_locations', 'site_locations.location_id = asset.location_id', 'left' )
							->join( 'site_zones', 'site_zones.zone_id = asset.zone_id', 'left' )
							->join( 'site_sub_blocks', 'site_sub_blocks.sub_block_id = site_zones.sub_block_id', 'left' )
							->where( 'asset.account_id',$account_id )
							->where( 'asset.asset_id', $asset_id )
							->group_by( 'asset.asset_id' )
							->get( 'asset' )->row();
						$result = !empty( $row ) ? $row : false;
					}
					break;
				
				default:				
				case 'site':
				case 'person':
				case 'people':
				case 'fleet':
				case 'vehicle':
				case 'job':
					//
					break;

			}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}
		return $result;
	}


	/*
	* Get Customer Info By Job ID
	*/
	private function get_customer_info_by_job_id( $account_id = false, $where = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $where ) ){
			$customer_id = !empty( $where['customer_id'] ) 	? $where['customer_id'] : false;
			$job_id 	 = !empty( $where['job_id'] ) 		? $where['job_id'] : false;
			if( !empty( $customer_id ) && !empty( $job_id ) ){
				$row = $this->db->select( 'c.salutation, c.customer_id, c.customer_first_name, c.customer_last_name, trim( concat( c.salutation," ",c.customer_first_name," ",c.customer_last_name ) ) as `customer_full_name`, c.customer_email, c.customer_mobile, c.customer_main_telephone, c.address_id, a.postcode, a.summaryline `customer_address`',false )
					->join ( 'customer `c`', 'c.customer_id = j.customer_id', 'left' )
					->join ( 'addresses a', 'a.main_address_id = c.address_id', 'left' )
					->where( 'j.archived !=', 1 )
					->where( 'j.account_id',$account_id )
					->where( 'j.customer_id',$customer_id )
					->where( 'j.job_id',$job_id )
					->get( 'job j' )
					->row();

				$result = !empty( $row ) ? $row : false;
			}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}
		return $result;
	}

	
	/*
	* Get Building Info By Job ID
	*/
	private function get_building_info_by_job_id( $account_id = false, $where = false ){
		$result = false;		
		if( !empty( $account_id ) && !empty( $where ) ){
			$site_id = !empty( $where['site_id'] ) 	? $where['site_id'] : false;
			$job_id 	 = !empty( $where['job_id'] ) 		? $where['job_id'] : false;
			if( !empty( $site_id ) && !empty( $job_id ) ){
				$row = $this->db->select( 'site.site_id, site.site_name, site.site_reference, site.site_postcodes, site.site_address_id, a.postcode, a.summaryline `site_address`',false )
					->join ( 'site', 'site.site_id = j.site_id', 'left' )
					->join ( 'addresses a', 'a.main_address_id = site.site_address_id', 'left' )
					->where( 'j.archived !=', 1 )
					->where( 'j.account_id',$account_id )
					->where( 'j.site_id',$site_id )
					->where( 'j.job_id',$job_id )
					->get( 'job j' )
					->row();

				$result = !empty( $row ) ? $row : false;
			}
			
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}
		return $result;
	}

	private function _log_audit_data( $account_id = false, $data = false ){
		return true;
		$data = $this->ssid_common->_filter_data( 'audit_logger', $data );
		$data['log_timestamp'] = date( 'Y-m-d H:i:s' );
		$this->db->insert( 'audit_logger', $data );
	}	


	/*
	* Copy Evidoc Type record
	*/
	public function copy_evidoc_type( $account_id = false, $evidoc_type_id = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $evidoc_type_id ) ){
			$conditions 			= ['account_id'=>$account_id,'audit_type_id'=>$evidoc_type_id];
			$evidoc_type_exists 	= (array) $this->db->get_where( 'audit_types',$conditions )->row();
			if( !empty( $evidoc_type_exists ) ){
				unset( $evidoc_type_exists['audit_type_id'], $evidoc_type_exists['date_created'], $evidoc_type_exists['last_modified'] );
				
				## Prepare new Insert
				$time_stamp						= date( 'dmYHi' );
				$new_record 					= $evidoc_type_exists;
				$new_record['audit_type']		= $new_record['alt_audit_type']	= $new_record['audit_type'].' Copy '.$time_stamp;
				$new_record['audit_type_ref']	= $this->_generate_audit_type_ref( $account_id, $new_record );

				$insert_data					= $this->db->insert( 'audit_types', $new_record );
				$new_evidoc_type_id				= ( $this->db->trans_status() ) ? $this->db->insert_id() : false;

				## Copy attached Questions
				$query 		= $this->db->get_where( 'audit_question_bank', $conditions );
				if( $query->num_rows() > 0 ){
					$questions_data = [];
					foreach( $query->result_array() as $k => $row ){
						unset( $row['question_id'], $row['audit_type_id'], $row['date_created'], $row['last_modified'] );
						$row['audit_type_id'] 	= $new_evidoc_type_id;
						$row['created_by'] 		= $this->ion_auth->_current_user->id;
						$questions_data[$k] 	= $row;
					}
					$this->db->insert_batch( 'audit_question_bank', $questions_data );
					$result = $this->get_evidoc_types( $account_id, false, [ 'audit_type_id'=>$new_evidoc_type_id ] );  
					$result	= !empty( $result->records ) ? $result->records : false;
					$this->session->set_flashdata( 'message','Evidoc Type copied successfully' );
				}
				
			} else {
				$this->session->set_flashdata('message','Invalid Evidoc Type ID');
			}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information.' );
		}
		return $result;
	}
	
	/* 
	* Re-Order Evidoc Questions
	*/
	public function reorder_evidoc_questions( $account_id = false, $evidoc_type_id = false, $postdata = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $evidoc_type_id ) ){
			$postdata		= convert_to_array( $postdata );
			$questions_data = !empty( $postdata['questions_data'] ) ? convert_to_array( $postdata['questions_data'] ) : false;
			
			if( !empty( $questions_data ) ){
				$total_questions 	= count( $questions_data );
				$updated_questions 	= 0;
				foreach( $questions_data as $k => $question_data ){
					$update_data = $this->ssid_common->_filter_data( 'audit_question_bank', (array) $question_data );
					$this->db->where( 'question_id', $update_data['question_id'] )
						->update( 'audit_question_bank', $update_data );
					
					if( $this->db->trans_status() !== false ){
						$updated_questions++;
						$result = true;
					}
				}
				
				if( $result && ( $total_questions == $updated_questions ) ){
					
					$this->session->set_flashdata( 'message','Section questions re-ordered successfully!' );
					
				} else if( $result && ( $total_questions != $updated_questions ) ){
					$this->session->set_flashdata( 'message','Some Section questions were not re-ordered. Request partially completed!' );
				} else {
					$this->session->set_flashdata( 'message','Unable to re-Order the Section questions!' );
					$result = false;
				}
				return $questions_data;
				
			} else {
				$this->session->set_flashdata( 'message','Your request is missing required information' );
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}
		return $result;
	}
	
	
	/**
	/* Delete/Archive an Evidoc Type resource
	*/
	public function delete_evidoc_type( $account_id = false, $audit_type_id = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $audit_type_id ) ){

			$conditions 	= [ 'account_id'=>$account_id,'audit_type_id'=>$audit_type_id ];
			$record_exists 	= $this->db->get_where( 'audit_types',$conditions )->row();

			if( !empty( $record_exists ) ){
				## Archive preexisting links to this Audit type
				$this->db->where( $conditions )->update( 'audit', [ 'archived'=>1 ] );

				## Then the parent
				$this->db->where( 'audit_type_id', $audit_type_id )
					->update( 'audit_types', [ 'is_active' => 0 ] );

				if( $this->db->trans_status() !== false ){
					$this->session->set_flashdata( 'message','Evidoc Type archived successfully.' );
					$result = true;
				}
			} else {
				$this->session->set_flashdata( 'message','Invalid Evidoc Type ID' );
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing the required information.' );
		}
		return $result;
	}
	
	
	/** 
	* Clone an existing Evidoc Type 
	**/
	public function clone_evidoc_type( $account_id = false, $evidoc_type_id = false, $data = false  ){
		$result = false;

		if( !empty( $account_id ) && !empty( $evidoc_type_id ) ){

			$data				= convert_to_array( $data );
			$src_account_id 	= !empty( $data['source_account_id'] ) 		? $data['source_account_id'] : false;
			$dest_account_id 	= !empty( $data['destination_account_id'] ) ? $data['destination_account_id'] : false;
			$copy_asset_types 	= !empty( $data['include_asset_types'] ) 	? $data['include_asset_types'] : false;

			$evidoc_type_exists = $this->db->select( 'audit_types.*,schedule_frequencies.frequency_group' )
				->join( 'schedule_frequencies', 'schedule_frequencies.frequency_id = audit_types.frequency_id', 'left' )
				->get_where( 'audit_types', [ 'audit_types.account_id' => $src_account_id, 'audit_types.audit_type_id'=>$evidoc_type_id ] )->row();
			
			if( !empty( $evidoc_type_exists ) ){
				
				$freg = $this->db->select( 'schedule_frequencies.frequency_id' )
					->get_where( 'schedule_frequencies', [ 'schedule_frequencies.account_id' => $dest_account_id, 'schedule_frequencies.frequency_group' => $evidoc_type_exists->frequency_group ] )->row();
				
				$cloned_data = array_merge( (array) $evidoc_type_exists, $data );
				
				unset( $cloned_data['audit_type_id'], $cloned_data['audit_type_ref'], $cloned_data['date_created'], $cloned_data['last_modified'], $cloned_data['last_modified_by'], $cloned_data['asset_type_id'], $cloned_data['category_id'] );
				$cloned_data['frequency_id'] 		= !empty( $freg->frequency_id ) ? $freg->frequency_id : null;
				$cloned_data['account_id'] 			= $dest_account_id;
				$cloned_data['alt_audit_type'] 		= $data['audit_type'];
				$cloned_data['audit_type_ref'] 		= $this->_generate_audit_type_ref( $account_id, $cloned_data );
				$cloned_data['is_cloned'] 			= 1;
				
				## Create New Evidoc Type
				$check_conflict = $this->db->select( 'audit_types.audit_type_id', false )
					->where( 'audit_types.account_id', $dest_account_id )
					->where( [ 'audit_frequency' => $cloned_data['audit_frequency'], 'audit_type_ref' => $cloned_data['audit_type_ref'] ] )
					->where( 'audit_types.audit_type_id !=', $evidoc_type_id )
					->limit( 1 )
					->get( 'audit_types' )
					->row();

				$evidoc_data = $this->ssid_common->_filter_data( 'audit_types', $cloned_data );

				if( !empty( $check_conflict  ) ){

					$this->db->where( 'audit_types.audit_type_id', $check_conflict->audit_type_id )
						->update( 'audit_types', $evidoc_data ); 
					
					## Clone Evidoc Questions
					$cloned_questions 	= $this->clone_evidoc_questions( $account_id, $evidoc_type_id, $check_conflict->audit_type_id, $cloned_data );
					$message 			= 'The resulting Evidoc Type already exists. Record has been updated.';
					$new_audit_type_id	= $check_conflict->audit_type_id;
				} else {
					
					$evidoc_data['created_by'] 	= $this->ion_auth->_current_user->id;
					$this->db->insert( 'audit_types', $evidoc_data );
					$new_audit_type_id 			= $this->db->insert_id();
					$evidoc_data['audit_type_id']	= $new_audit_type_id;
					if( !empty( $new_audit_type_id )){
						$cloned_questions 	= $this->clone_evidoc_questions( $account_id, $evidoc_type_id, $new_audit_type_id, $cloned_data );
						$message 			= 'Evidoc Type cloned successfully.';
					}
				}

				$this->session->set_flashdata( 'message', $message );
				$result	= !empty( $cloned_questions ) ? $cloned_questions : [ 'audit_type_id' => $new_audit_type_id, 'src_account_id' => $src_account_id, 'dest_account_id' => $dest_account_id ];
			} else {
				$this->session->set_flashdata( 'message', 'This Evidoc Type record does not exist or does not belong to you.' );
				return false;
			}
			
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information.' );
		}
		return $result;
	}
	
	
	/*
	* Clone Evidoc Questions
	**/
	public function clone_evidoc_questions( $account_id = false, $src_audit_type_id = false, $dest_audit_type_id = false, $params = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $src_audit_type_id ) && !empty( $dest_audit_type_id ) ){
			
			$src_account_id 	= !empty( $params['source_account_id'] ) 		? $params['source_account_id'] : null;
			$dest_account_id 	= !empty( $params['destination_account_id'] ) 	? $params['destination_account_id'] : null;
			$copy_asset_types 	= !empty( $data['include_asset_types'] ) 	? $data['include_asset_types'] : false;
			
			$query 	= $this->db->order_by( 'audit_question_bank.question_id' )
				->get_where( 'audit_question_bank', [ 'audit_question_bank.account_id' => $src_account_id, 'audit_question_bank.audit_type_id' => $src_audit_type_id ] );
			
			if( $query->num_rows() > 0 ){

				$all_questions 	= $new_questions = $update_questions = [];

				foreach( $query->result_array() as $k => $row ){

					$src_question_id  = $row['question_id'];
					
					unset( $row['question_id'], $row['asset_type_id'], $row['date_created'], $row['last_modified'], $row['last_modified_by'] );
					
					if( !$copy_asset_types ){
						unset( $row['asset_type_id'] );
					}
					
					$row['account_id']  	= $dest_account_id;
					$row['audit_type_id']  	= $dest_audit_type_id;
					$params					= array_merge( $params, $row );

					$this->db->select( 'audit_question_bank.question_id', false )
						->where( 'audit_question_bank.account_id', $dest_account_id )
						->where( [ 'question'=>$row['question'], 'audit_type_id'=>$dest_audit_type_id ] )
						->limit( 1 );

					$check_exists = $this->db->get( 'audit_question_bank' )->row();
					
					$question_row = $this->ssid_common->_filter_data( 'audit_question_bank', $row );
					
					if( !empty( $check_exists  ) ){
						$question_row['last_modified_by'] 	= $this->ion_auth->_current_user->id;
						$this->db->where( 'question_id', $check_exists->question_id )->update( 'audit_question_bank', $question_row );
						$question_id 				= $check_exists->question_id;
						$question_row['question_id']= $question_id;
						$update_questions[$k]		= $question_row;
					} else {
						$question_row['created_by'] = $this->ion_auth->_current_user->id;
						$this->db->insert( 'audit_question_bank', $question_row );
						$question_id 				= $this->db->insert_id();
						$question_row['question_id']= $question_id;
						$new_questions[$k]			= $question_row;
					}
					
					$all_questions['src_account_id'] 		= strval( $src_account_id );
					$all_questions['dest_account_id'] 		= strval( $dest_account_id );
					$all_questions['audit_type_id'] 		= strval( $dest_audit_type_id );
					$all_questions['cloned_audit_type_id'] 	= strval( $src_audit_type_id );
					$all_questions['frequency_id'] 			= !empty( $params['frequency_id'] )? $params['frequency_id'] : false;
					$all_questions['questions'][] 			= $question_id;

				}
			}
			
			if( !empty( $all_questions ) ){
				
				## Clone any Response Types
				$resp_types	= $this->clone_evidoc_response_types( $account_id, $src_account_id, $dest_account_id, $params );
				$all_questions['response_types'] = $resp_types;
				$this->session->set_flashdata( 'message','Evidoc Questions have been cloned successfully.' );
				$result = $all_questions;
			} else {
				$this->session->set_flashdata( 'message','Unable to clone Questions, no data was found.' );
			}

		} else {

			$this->session->set_flashdata( 'message','Your request is missing required information.' );
		}

		return $result;
	}
	
	
	/*
	* Clone Evidoc Response Types
	**/
	public function clone_evidoc_response_types( $account_id = false, $src_audit_type_id = false, $dest_audit_type_id = false, $params = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $src_audit_type_id ) && !empty( $dest_audit_type_id ) ){
			$src_account_id 	= !empty( $params['source_account_id'] ) 		? $params['source_account_id'] : null;
			$dest_account_id 	= !empty( $params['destination_account_id'] ) 	? $params['destination_account_id'] : null;

			$query 	= $this->db->order_by( 'evidoc_response_types.response_type_id' )
				->where( 'is_active', 1 )
				->where( 'evidoc_response_types.account_id', $src_account_id )
				->get( 'evidoc_response_types' );
			
			if( $query->num_rows() > 0 ){

				$all_response_types 	= $new_response_types = $update_response_types = [];

				foreach( $query->result_array() as $k => $row ){
					
					$src_response_type_id  = $row['response_type_id'];
					
					unset( $row['response_type_id'], $row['date_created'], $row['last_modified'], $row['last_modified_by'] );
					
					$row['account_id']  = $dest_account_id;
					$params				= array_merge( $params, $row );

					$this->db->select( 'evidoc_response_types.response_type_id', false )
						->where( 'evidoc_response_types.account_id', $dest_account_id )
						->where( 'response_type', $row['response_type'] )
						->limit( 1 );

					$check_exists = $this->db->get( 'evidoc_response_types' )->row();
					
					$response_type_row = $this->ssid_common->_filter_data( 'evidoc_response_types', $row );
					
					if( !empty( $check_exists  ) ){
						$response_type_row['last_modified_by'] 	= $this->ion_auth->_current_user->id;
						$this->db->where( 'response_type_id', $check_exists->response_type_id )->update( 'evidoc_response_types', $response_type_row );
						$response_type_id 				= $check_exists->response_type_id;
						$response_type_row['response_type_id']= $response_type_id;
						$update_response_types[$k]		= $response_type_row;
					} else {
						$response_type_row['created_by'] = $this->ion_auth->_current_user->id;
						$this->db->insert( 'evidoc_response_types', $response_type_row );
						$response_type_id 				= $this->db->insert_id();
						$response_type_row['response_type_id']= $response_type_id;
						$new_response_types[$k]			= $response_type_row;
					}
					
					$all_response_types[] 				= $response_type_id;
				}
			}
			
			if( !empty( $all_response_types ) ){
				$this->session->set_flashdata( 'message','Evidoc Response Types have been cloned successfully.' );
				$result = $all_response_types;
			} else {
				$this->session->set_flashdata( 'message','Unable to clone Response Types, please try again.' );
			}

		} else {

			$this->session->set_flashdata( 'message','Your request is missing required information.' );
		}

		return $result;
	}
	
	
	/**
	* Link Evidoc Assets
	*/
	public function link_audit_generic_assets( $account_id = false, $audit_id = false, $selected_assets = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $audit_id ) && !empty( $selected_assets ) ){
				
				$selected_assets 	= convert_to_array( $selected_assets );
				$all_records 		= $new_records = $existing_records = [];
				
				foreach( $selected_assets as $key => $asset_id ){
					$conditions   = [ 'audit_id'=> $audit_id, 'account_id' => $account_id, 'asset_id'=> $asset_id ];
					$check_exists = $this->db->select( 'id', false )->get_where( 'audit_generic_assets', $conditions )->row();
					if( !empty( $check_exists->id ) ){
						$existing_records[] = [
							'id' 				=> $check_exists->id,
							'audit_id' 			=> $audit_id,
							'asset_id' 			=> $asset_id,
							'account_id' 		=> $account_id,
							'last_modified_by' 	=> $this->ion_auth->_current_user->id
						];
					} else {
						$new_records[] = [
							'audit_id' 			=> $audit_id,
							'asset_id' 			=> $asset_id,
							'account_id' 		=> $account_id,
							'created_by' 		=> $this->ion_auth->_current_user->id
						];
					}
					$all_records[] = $asset_id;
				}

				if( !empty( $new_records ) ){
					$this->db->insert_batch( 'audit_generic_assets', $new_records );
				}
				
				if( !empty( $existing_records ) ){
					$this->db->update_batch( 'audit_generic_assets', $existing_records, 'id' );
				}
				
				if( !empty( $all_records )){
					$this->session->set_flashdata( 'message','Evidoc assets processed successfully.' );
					$result = $all_records;
				} else {
					$this->session->set_flashdata( 'message','There was a problem processing your Evidoc assets. Please try again!' );
				}

		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information.' );
		}
		return $result;
	}
	
	
	/*
	* Get Evidoc Assets 
	**/
	public function get_audit_generic_assets( $account_id = false, $audit_id = false, $where = false, $order_by = false ){

		$result = false;

		if( !empty( $account_id ) && !empty( $audit_id ) ){
		
			if( !empty( $account_id ) && !empty( $audit_id ) ){
				
				$where   = convert_to_array( $where );
				
				$this->db->select( 'audit_generic_assets.id, audit_generic_assets.audit_id, audit_generic_assets.asset_id, audit_generic_assets.account_id, audit_generic_assets.archived, audit_generic_assets.is_active', false )
					->select( 'asset.asset_type_id, asset.site_id, asset.asset_unique_id, asset_types.asset_type, asset_types.discipline_id', false )
					->select( 'account_discipline.account_discipline_name `discipline_name`,account_discipline.account_discipline_image_url `discipline_image_url`, site_zones.zone_name, site_locations.location_name', false )
					->join(	'audit', 'audit.audit_id = audit_generic_assets.audit_id', 'left')
					->join(	'asset', 'asset.asset_id = audit_generic_assets.asset_id', 'left')
					->join(	'asset_types', 'asset_types.asset_type_id = asset.asset_type_id', 'left')
					->join( 'account_discipline','account_discipline.discipline_id = asset_types.discipline_id','left' )
					->join( 'site_zones','site_zones.zone_id = asset.zone_id','left' )
					->join( 'site_locations','site_locations.location_id = asset.location_id','left' )
					->where( 'audit_generic_assets.is_active', 1 )
					->where( 'audit_generic_assets.account_id', $account_id );

				if( isset( $where['asset_id'] ) ){
					if( !empty( $where['asset_id'] ) ){
						$this->db->where( 'audit_generic_assets.asset_id', $where['asset_id'] );
					}
					unset( $where['asset_id'] );
				}
				
				if( !empty( $audit_id ) ){
					$this->db->where( 'audit_generic_assets.audit_id', $audit_id );
				}
				
				$query = $this->db->group_by( 'audit_generic_assets.id' )
					->get( 'audit_generic_assets' );

				if( $query->num_rows() > 0 ){
					$data = [];
					foreach( $query->result() as $k => $row ){
						$audit_details = $this->db->where( [ 'account_id' => $account_id, 'audit_id'=>$row->audit_id ] )
							->select( 'audit.audit_id, audit.start_time, audit.finish_time, audit.audit_status `completion_status`', false )
							->get( 'audit' )
							->row();
						
						$row->audit_id 			= !empty( $audit_details->audit_id ) ? $audit_details->audit_id : null;
						$row->start_time 		= !empty( $audit_details->start_time ) ? $audit_details->start_time : null;
						$row->finish_time 		= !empty( $audit_details->finish_time ) ? $audit_details->finish_time : null;
						$row->completion_status = !empty( $audit_details->completion_status ) ? $audit_details->completion_status : null;
						$data[$k] = $row;
					}
					$result = $data;
					$this->session->set_flashdata( 'message', 'Evidoc Assets data retrieved.' );
				} else {
					$this->session->set_flashdata( 'message', 'No data found.' );
					$result = false;
				}
			} else{
				$this->session->set_flashdata( 'message','Error! Missing required information.' );
			}
			
		}
	
		return $result;
	}
	
}