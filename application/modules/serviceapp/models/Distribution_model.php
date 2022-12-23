<?php if( !defined( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

class Distribution_model extends CI_Model {

	function __construct(){
		parent::__construct();
		$this->load->model( 'serviceapp/content_model', 'content_service' );

		$section 	   		= explode("/", $_SERVER["SCRIPT_NAME"]);
		if( !isset( $section[1] ) || empty( $section[1] ) || ( !( is_array( $section ) ) ) ){
			$this->app_root = substr( dirname( __FILE__ ), 0, strpos( dirname( __FILE__ ), "application" ) );
		} else {
			if ( !isset( $_SERVER["DOCUMENT_ROOT"] ) || ( empty( $_SERVER["DOCUMENT_ROOT"] ) ) ){
				$_SERVER["DOCUMENT_ROOT"] = realpath( dirname(__FILE__).'/../' );
			}

			$this->section		= $section;
			$this->app_root		= $_SERVER["DOCUMENT_ROOT"]."/".$section[1]."/";
			$this->app_root		= str_replace('/index.php','',$this->app_root);
		}
	}

	/** Searchable fields **/
	private $distribution_groups_search_fields   = ['distribution_group', 'distribution_group_desc' ];
	private $distribution_bundles_search_fields  = ['distribution_bundle', 'distribution_bundle_desc' ];


	/**	Get list of Distribution Groups and search through them **/
	public function get_distribution_groups( $account_id = false, $distribution_group_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){

		$result = false;

		if( !empty( $account_id ) ){
			$this->db->select( 'distribution_groups.*, content_territory.country,  content_territory.country `territory`, content_territory.code, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false )
				->join( 'user creater', 'creater.id = distribution_groups.created_by', 'left' )
				->join( 'user modifier', 'modifier.id = distribution_groups.last_modified_by', 'left' )
				->join( 'content_territory', 'content_territory.territory_id = distribution_groups.associated_territory_id', 'left' )
				->where( 'distribution_groups.is_active', 1 )
				->where( 'distribution_groups.account_id', $account_id );

				$where = $raw_where = convert_to_array( $where );

			if( !empty( $distribution_group_id ) || isset( $where['distribution_group_id'] ) ){
				$distribution_group_id	= ( !empty( $distribution_group_id ) ) ? $distribution_group_id : $where['distribution_group_id'];
				if( !empty( $distribution_group_id ) ){

					$row = $this->db->get_where( 'distribution_groups', ['distribution_groups.distribution_group_id'=>$distribution_group_id ] )->row();

					if( !empty( $row ) ){
						$row->linked_group_sites 			= $this->get_distribution_group_sites( $account_id, $distribution_group_id );
						$row->distribution_group_providers 	= $this->get_distribution_group_providers( $account_id, $distribution_group_id );
						$result  							= $row;
						$this->session->set_flashdata( 'message','Distribution Groups data found' );
						return $result;
					} else {
						$this->session->set_flashdata( 'message','Distribution Groups data not found' );
						return false;
					}
				}
				unset( $where['distribution_group_id'], $where['distribution_group_ref'] );
			}

			if( !empty( $search_term ) ){
				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->distribution_groups_search_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				} else {
					foreach( $this->distribution_groups_search_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( !empty( $where ) ){

				if( isset( $where['distribution_group'] ) ){
					if( !empty( $where['distribution_group'] ) ){
						$distribution_group = strtolower( strip_all_whitespace( $where['distribution_group'] ) );
						$this->db->where( '( distribution_groups.distribution_group = "'.$where['distribution_group'].'" OR distribution_groups.distribution_group_ref = "'.$distribution_group.'" )' );
					}
					unset( $where['distribution_group'] );
				}

				if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}

			if( !empty( $order_by ) ){
				$this->db->order_by( $order_by );
			} else {
				$this->db->order_by( 'distribution_group ASC' );
			}

			if( $limit > 0 ){
				$this->db->limit( $limit, $offset );
			}

			$query = $this->db->get( 'distribution_groups' );


			if( $query->num_rows() > 0 ){

				$result_data = $query->result();

				$result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
				$result->records 			= $result_data;
				$counters 					= $this->get_distribution_groups_totals( $account_id, $search_term, $raw_where );
				$result->counters->total 	= ( !empty( $counters->total ) ) ? $counters->total : null;
				$result->counters->pages 	= ( !empty( $counters->pages ) ) ? $counters->pages : null;
				$result->counters->limit  	= ( !empty( $apply_limit ) ) ? $limit : $result->counters->total;
				$result->counters->offset 	= $offset;

				$this->session->set_flashdata( 'message','Categories data found' );
			} else {
				$this->session->set_flashdata( 'message','There\'s currently no Distribution Groups for your Account' );
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}

		return $result;
	}


	/** Get Distribution Groups lookup counts **/
	public function get_distribution_groups_totals( $account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT ){
		$result = false;
		if( !empty( $account_id ) ){

			$this->db->select( 'distribution_groups.distribution_group_id', false )
				->where( 'distribution_groups.is_active', 1 )
				->where( 'distribution_groups.account_id', $account_id );

			$where = $raw_where = convert_to_array( $where );

			if( !empty( $search_term ) ){
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->distribution_groups_search_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				} else {
					foreach( $this->distribution_groups_search_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( !empty( $where ) ){

				if( isset( $where['distribution_group'] ) ){
					if( !empty( $where['distribution_group'] ) ){
						$distribution_group_ref = strtoupper( strip_all_whitespace( $where['distribution_group'] ) );
						$this->db->where( '( distribution_groups.distribution_group = "'.$where['distribution_group'].'" OR distribution_groups.distribution_group_ref = "'.$distribution_group_ref.'" )' );
					}
					unset( $where['category_name'] );
				}

				if( !empty( $where ) ){
					$this->db->where( $where );
				}
			}

			$query 			  = $this->db->from( 'distribution_groups' )->count_all_results();
			$results['total'] = !empty( $query ) ? $query : 0;
			$limit 				= ( $limit > 0 ) ? $limit : $results['total'];
			$results['pages'] = !empty( $query ) ? ceil( $query / $limit ) : 0;
			return json_decode( json_encode( $results ) );
		}
		return $result;
	}


	/** Add a NEW Distribution Group **/
	public function add_distribution_group( $account_id = false, $distribution_group_data = false ){

		$result = null;

		if( !empty( $account_id ) && !empty( $distribution_group_data  ) ){

			if( isset( $distribution_group_data['provider_details'] ) ){
				$provider_details = isset( $distribution_group_data['provider_details'] ) ? $distribution_group_data['provider_details'] : [];
				$provider_details = ( is_json( $provider_details ) ) ? json_decode( $provider_details ) : $provider_details;
				unset( $distribution_group_data['provider_details'] );
			}

			if( isset( $distribution_group_data['linked_sites'] ) ){
				$linked_sites = isset( $distribution_group_data['linked_sites'] ) ? $distribution_group_data['linked_sites'] : [];
				$linked_sites = ( is_json( $linked_sites ) ) ? json_decode( $linked_sites ) : $linked_sites;
				unset( $distribution_group_data['linked_sites'] );
			}

			foreach( $distribution_group_data as $col => $value ){
				if( $col == 'distribution_group' ){
					$data['distribution_group_ref'] 	= strtoupper( strip_all_whitespace( $value ) );
				}
				$data[$col] = $value;
			}

			$check_exists = $this->db->where( 'account_id', $account_id )
				->where( '( distribution_groups.distribution_group = "'.$data['distribution_group'].'" OR distribution_groups.distribution_group_ref = "'.$data['distribution_group_ref'].'" )' )
				->limit( 1 )
				->get( 'distribution_groups' )
				->row();

			$data = $this->ssid_common->_filter_data( 'distribution_groups', $data );

			if( !empty( $check_exists  ) ){
				$data['last_modified_by'] = $this->ion_auth->_current_user->id;
				$this->db->where( 'distribution_group_id', $check_exists->distribution_group_id )
					->update( 'distribution_groups', $data );
					$message = 'This Distribution Group already exists, record has been updated successfully.';
					$result  = $check_exists;

				$data['distribution_group_id'] = $check_exists->distribution_group_id;
			}else{
				$data['created_by'] 		= $this->ion_auth->_current_user->id;
				$this->db->insert( 'distribution_groups', $data );
				$message = 'New Distribution Group added successfully.';
				$data['distribution_group_id'] = (string) $this->db->insert_id();
				$result = $data;
			}

			if( isset( $provider_details ) ){
				$this->add_distribution_group_providers( $account_id, $data['distribution_group_id'], ['provider_details'=>$provider_details] );
			}

			if( isset( $linked_sites ) ){
				$this->add_distribution_group_sites( $account_id, $data['distribution_group_id'], ['linked_sites'=>$linked_sites] );
			}

			$this->session->set_flashdata( 'message', $message );

		}else{
			$this->session->set_flashdata( 'message','Error! Missing required information.' );
		}

		return $result;
	}


	/** Update Distribution Group **/
	public function update_distribution_group( $account_id = false, $distribution_group_data = false ){

		$result = null;

		if( !empty( $account_id ) && !empty( $distribution_group_data['distribution_group_id'] ) && !empty( $distribution_group_data ) ){

			$linked_sites = false;
			if( isset( $distribution_group_data['linked_sites'] ) ){
				$linked_sites = isset( $distribution_group_data['linked_sites'] ) ? $distribution_group_data['linked_sites'] : [];
				$linked_sites = ( is_json( $linked_sites ) ) ? json_decode( $linked_sites ) : $linked_sites;
				unset( $distribution_group_data['linked_sites'] );
			}

			foreach( $distribution_group_data as $col => $value ){
				if( $col == 'distribution_group' ){
					$data['distribution_group_ref'] 	= strtoupper( strip_all_whitespace( $value ) );
				}
				$data[$col] = $value;
			}

			if( !empty( $data['distribution_group_id'] ) ){
				$check_conflict = $this->db->where( 'account_id', $account_id )
					->where( '( distribution_groups.distribution_group = "'.$data['distribution_group'].'" OR distribution_groups.distribution_group_ref = "'.$data['distribution_group_ref'].'" )' )
					->where( 'distribution_groups.distribution_group_id !=', $data['distribution_group_id'] )
					->get( 'distribution_groups' )->row();

				$data = $this->ssid_common->_filter_data( 'distribution_groups', $data );

				if( !$check_conflict ){
					$data['last_modified_by'] = $this->ion_auth->_current_user->id;
					$this->db->where( [ 'account_id'=>$account_id, 'distribution_group_id'=>$data['distribution_group_id'] ] )
						->update( 'distribution_groups', $data );
						if( $this->db->trans_status() !== false ){

							if( isset( $linked_sites ) ){
								$this->add_distribution_group_sites( $account_id, $distribution_group_data['distribution_group_id'], ['linked_sites'=>$linked_sites] );
							}

							$result = $this->get_distribution_groups( $account_id, $data['distribution_group_id'] );
							$this->session->set_flashdata( 'message', 'Distribution Group updated successfully.' );
						}

				}else{
					$this->session->set_flashdata( 'message', 'This Distribution Group does not exists or does not belong to you.' );
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


	/** Delete Distribution Group record **/
	public function delete_distribution_group( $account_id = false, $distribution_group_id = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $distribution_group_id ) ){
			$conditions 		= ['account_id'=>$account_id,'distribution_group_id'=>$distribution_group_id];
			$distribution_group_exists 	= $this->db->get_where( 'distribution_groups',$conditions )->row();

			if( !empty( $distribution_group_exists ) ){

				$this->db->where( $conditions )
					->update( 'distribution_groups', [
						'is_active'				=>0,
						'archived'				=>1,
						'distribution_group'	=>$distribution_group_exists->distribution_group.' (Archived)',
						'distribution_group_ref'=> strip_all_whitespace( $distribution_group_exists->distribution_group.'(Archived)' ),
					] );

				if( $this->db->trans_status() !== FALSE ){
					$this->session->set_flashdata('message','Distribution Group deleted successfully.');
					$result = true;
				}
			}else{
				$this->session->set_flashdata( 'message','Invalid Distribution Group ID' );
			}

		}else{
			$this->session->set_flashdata( 'message','No Distribution Group record found.' );
		}
		return $result;
	}


	/** Add Linked Distribution Group Sites **/
	public function add_distribution_group_sites( $account_id = false, $distribution_group_id = false, $postdata = false ){
		$result = false;

		if( !empty( $account_id ) && !empty( $distribution_group_id ) ){

			$postdata 		= convert_to_array( $postdata );
			$linked_sites 	= !empty( $postdata['linked_sites'] ) ? $postdata['linked_sites'] : [];

			unset( $postdata['linked_sites'] );

			$new_sites = $exists  = $incoming_records = $invalid_sites = $pre_existing_sites = [];
			if( isset( $linked_sites ) ){

				$get_existing_sites = $this->db->select( 'site_id' )
					->group_by( 'site_id' )
					->get_where( 'distribution_group_sites', [ 'distribution_group_id'=>$distribution_group_id ] );

				if( $get_existing_sites->num_rows() > 0 ){
					$pre_existing_sites = array_column( $get_existing_sites->result_array(), 'site_id' );
				}

				$deleted_sites = array_diff( $pre_existing_sites, $linked_sites );

				#Drop Deleted Sites
				if( !empty( $deleted_sites ) ){
					$this->db->where( 'distribution_group_id', $distribution_group_id )
						->where_in( 'site_id', $deleted_sites )
						->delete( 'distribution_group_sites' );

					$this->ssid_common->_reset_auto_increment( 'distribution_group_sites', 'group_site_id' );

					$message = 'Distribution Group Site(s) removed successfully.';
				}

				if( !empty( $linked_sites ) ){

					foreach( $linked_sites as $key => $site_id ){
						$check_site_exists 	= $this->db->select( 'site_id' )->get_where( 'site', [ 'account_id'=>$account_id, 'site_id'=>$site_id ] )->row();

						if( !empty( $check_site_exists ) ){

							$check_exists 		= $this->db->get_where( 'distribution_group_sites', [ 'distribution_group_id'=>$distribution_group_id, 'site_id'=>$site_id ] )->row();

							if( !empty( $check_exists ) ){
								$exists[] = [
									'group_site_id'		=> $check_exists->group_site_id,
									'site_id'			=> $site_id,
									'last_modified_by'	=> $this->ion_auth->_current_user->id
								];
							} else {
								$new_sites[] = [
									'site_id'				=> $site_id,
									'account_id'			=> $account_id,
									'distribution_group_id'	=> $distribution_group_id,
									'created_by'			=> $this->ion_auth->_current_user->id
								];
							}

						} else {
							$invalid_sites[] = $site_id;
						}

						$incoming_records[]  = $site_id;

					}
				}
			}

			if( !empty( $new_sites ) ){
				$message = 'Distribution Group Site(s) added successfully.';
				$this->db->insert_batch( 'distribution_group_sites', $new_sites );
			}

			if( !empty( $exists ) ){
				$message = 'Distribution Group Site(s) updated successfully.';
				$this->db->update_batch( 'distribution_group_sites', $exists, 'group_site_id' );
			}

			if( $this->db->trans_status() !== false ){
				$result = !empty( $incoming_records ) ? $incoming_records : $deleted_sites;
				$this->session->set_flashdata( 'message', $message );
				if( !empty( $invalid_sites ) ){
					$this->session->set_flashdata( 'message','Some Sites were not added as they are invalid. Invalid list '.json_encode($invalid_sites) );
				}
			} else {
				$this->session->set_flashdata( 'message','Your request is missing required information' );
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}
		return $result;
	}

	/**
	* Get all Sites Linked to a Distribution Group
	*/
	public function get_distribution_group_sites( $account_id = false, $distribution_group_id = false, $where = false, $order_by = false, $limit = false, $offset = false ){
		$result = false;
		if( !empty( $account_id ) ){

			$this->db->select( 'dgs.*, distribution_groups.distribution_group, site.site_name', false );

			if( !empty( $account_id ) ){
				$this->db->where( 'dgs.account_id', $account_id );
			}

			if( !empty( $distribution_group_id ) ){
				$this->db->where( 'dgs.distribution_group_id', $distribution_group_id );
			}

			$query = $this->db->join( 'distribution_groups', 'distribution_groups.distribution_group_id = dgs.distribution_group_id', 'left' )
				->join( 'site', 'site.site_id = dgs.site_id', 'left' )
				->get( 'distribution_group_sites dgs' );

			if( $query->num_rows() > 0 ){
				$result = $query->result();
				$this->session->set_flashdata( 'message','Distribution Group Sites data found' );
			} else {
				$this->session->set_flashdata( 'message','No data found' );
			}
		}else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}

		return $result;
	}


	/**	Get list of Distribution Bundles and search through them **/
	public function get_distribution_bundles( $account_id = false, $distribution_group_id = false, $distribution_bundle_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){

		$result = false;

		if( !empty( $account_id ) ){
			$this->db->select( 'distribution_bundles.*, CONCAT( creater.first_name, " ", creater.last_name ) `record_created_by`, CONCAT( modifier.first_name, " ", modifier.last_name ) `record_modified_by`', false )
				->join( 'user creater', 'creater.id = distribution_bundles.created_by', 'left' )
				->join( 'user modifier', 'modifier.id = distribution_bundles.last_modified_by', 'left' )
				->join( 'distribution_groups', 'distribution_groups.distribution_group_id = distribution_bundles.distribution_group_id', 'left' )
				->where( 'distribution_bundles.is_active', 1 )
				->where( 'distribution_bundles.account_id', $account_id );

				$where = $raw_where = convert_to_array( $where );

			if( !empty( $distribution_bundle_id ) || isset( $where['distribution_bundle_id'] ) ){
				$distribution_bundle_id	= ( !empty( $distribution_bundle_id ) ) ? $distribution_bundle_id : $where['distribution_bundle_id'];
				if( !empty( $distribution_bundle_id ) ){

					$row = $this->db->get_where( 'distribution_bundles', ['distribution_bundles.distribution_bundle_id'=>$distribution_bundle_id ] )->row();

					if( !empty( $row ) ){
						$row->bundle_sites 		= $this->get_distribution_bundle_sites( $account_id, $distribution_bundle_id );
						$row->bundle_content 	= $this->get_distribution_bundle_content( $account_id, $distribution_bundle_id );
						$result  				= $row;
						$this->session->set_flashdata( 'message','Distribution Bundles data found' );
						return $result;
					} else {
						$this->session->set_flashdata( 'message','Distribution Bundles data not found' );
						return false;
					}
				}
				unset( $where['distribution_bundle_id'], $where['distribution_bundle_ref'] );
			}

			if( !empty( $distribution_group_id ) ){
				$this->db->where( 'distribution_bundles.distribution_group_id', $distribution_group_id );
			}

			if( !empty( $search_term ) ){
				//Check for spaces in the search term
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->distribution_bundles_search_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				} else {
					foreach( $this->distribution_bundles_search_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( !empty( $distribution_group_id ) || isset( $where['distribution_group_id'] ) ){
				$distribution_group_id	= ( !empty( $distribution_group_id ) ) ? $distribution_group_id : $where['distribution_group_id'];
				if( !empty( $distribution_group_id ) ){
					$this->db->where( 'distribution_bundles.distribution_group_id', $distribution_group_id );
				}
				unset( $where['distribution_group_id'] );
			} else {
				$distribution_group_id = false;
			}

			if( isset( $where['distribution_bundle'] ) ){
				if( !empty( $where['distribution_bundle'] ) ){
					$distribution_bundle = strtolower( strip_all_whitespace( $where['distribution_bundle'] ) );
					$this->db->where( '( distribution_bundles.distribution_bundle = "'.$where['distribution_bundle'].'" OR distribution_bundles.distribution_bundle_ref = "'.$distribution_bundle.'" )' );
				}
				unset( $where['distribution_bundle'] );
			}

			if( isset( $where['full_details'] ) ){
				if( !empty( $where['full_details'] ) ){
					$full_details = true;
				}
				unset( $where['full_details'], $raw_where['full_details']  );
			}

			if( !empty( $where ) ){
				$this->db->where( $where );
			}

			if( !empty( $order_by ) ){
				$this->db->order_by( $order_by );
			} else {
				$this->db->order_by( 'distribution_bundle_id DESC, distribution_bundle' );
			}

			if( $limit > 0 ){
				$this->db->limit( $limit, $offset );
			}

			$query = $this->db->get( 'distribution_bundles' );

			if( $query->num_rows() > 0 ){

				if( !empty( $full_details ) ){
					$result_data = [];
					foreach( $query->result() as $key => $row ){
						$row->bundle_sites 		= $this->get_distribution_bundle_sites( $account_id, $row->distribution_bundle_id );
						$row->bundle_content 	= $this->get_distribution_bundle_content( $account_id, $row->distribution_bundle_id );
						$result_data[$key] = $row;
					}
				} else {
					// We do know that this part is for the bundle dashboard view, so we're deciding to sit here with the logic to update the current status of the bundle

					// we do have something from the database
					// we need to have something from Coggins

					// in theory i need to have 3 calls:
						// - Queue Waiting
						// - Queue running
						// - Queue Finished

					// Queue waiting
					$queueWaiting		= false;
					$queueWaiting 		= $this->coggins_service->get_queue_waiting( $account_id );

					// Queue running
					$queueRunning		= false;
					$queueRunning 		= $this->coggins_service->get_running( $account_id );

					// Queue finished
					$queueFinished		= false;
					$queueFinished 		= $this->coggins_service->get_queue_finished( $account_id );

					$statuses_to_update = ["processing", "scheduled", "sending", "error"];

					// reset the output
					$result_data = [];
					foreach( $query->result() as $key => $row ){

						if( in_array( strtolower( $row->send_status ), $statuses_to_update ) ){

							$row->coggins_errors 	= "";
							$row->coggins_progress	= "";

							// this bundle needs to have an updated status
							$cog_id 		= $row->coggins__id;
							// $cog_name 	= $row->coggins_name;
							// $cog_state 	= $row->coggins_state;

							## check if it is in queueWaiting
							$q_waiting_key 	= false;
							$q_running_key 	= false;
							$q_finished_key = false;
							$bundle_found	= false;

							## check the queueRunning dataset for the bundle
							if( !empty( $queueWaiting->data ) ){
								$q_waiting_key 	= array_search( $cog_id, array_column( $queueWaiting->data, '_id' ) );
								if( ( $q_waiting_key !== false ) ){
									$bundle_found = true;

									$row->coggins_state 				= ( !empty( $queueWaiting->data[$q_waiting_key]->state ) ) ? $queueWaiting->data[$q_waiting_key]->state : false ;
									$row->send_status 					= ( !empty( $queueWaiting->data[$q_waiting_key]->state ) ) ? map_coggins_status( $queueWaiting->data[$q_waiting_key]->state ) : $row->send_status ;
									$row->send_status_timestamp 		= ( !empty( $queueWaiting->data[$q_waiting_key]->scheduled ) ) ? date( 'Y-m-d H:i:s', ( $queueWaiting->data[$q_waiting_key]->scheduled/1000 ) ) : $row->send_status_timestamp;
									$row->error_message 				= ( !empty( $queueWaiting->data[$q_waiting_key]->error->text ) ) ? $queueWaiting->data[$q_waiting_key]->error->text : false ;
								}
							}

							if( !$bundle_found ){
								if( !empty( $queueRunning->data ) ){
									$q_running_key 	= array_search( $cog_id, array_column( $queueRunning->data, '_id' ) );

									if( ( $q_running_key !== false ) ){
										$bundle_found = true;

										$row->coggins_state 				= ( !empty( $queueRunning->data[$q_running_key]->state ) ) ? $queueRunning->data[$q_running_key]->state : false ;
										$row->send_status 					= ( !empty( $queueRunning->data[$q_running_key]->state ) ) ? map_coggins_status( $queueRunning->data[$q_running_key]->state ) : $row->send_status ;
										$row->send_status_timestamp 		= ( !empty( $queueRunning->data[$q_running_key]->distribution->details->started ) ) ? date( 'Y-m-d H:i:s', ( $queueRunning->data[$q_running_key]->distribution->details->started/1000 ) ) : $row->send_status_timestamp;

										if( isset( $queueRunning->data[$q_running_key]->distribution->status->progress ) ){
											$row->coggins_progress				= ( $queueRunning->data[$q_running_key]->distribution->status->progress !== false ) ? ( string ) $queueRunning->data[$q_running_key]->distribution->status->progress : NULL ;
										}

										if( isset( $queueRunning->data[$q_running_key]->distribution->status->errors ) ){
											$row->coggins_errors				= ( $queueRunning->data[$q_running_key]->distribution->status->errors !== false ) ? ( string ) $queueRunning->data[$q_running_key]->distribution->status->errors : NULL ;
										}
									}
								}
							}

							if( !$bundle_found ){
								if( !empty( $queueFinished->data ) ){
									$q_finished_key 	= array_search( $cog_id, array_column( $queueFinished->data, '_id' ) );
									if( ( $q_finished_key !== false ) ){
										$bundle_found = true;

										$row->coggins_state 				= ( !empty( $queueFinished->data[$q_finished_key]->state ) ) ? $queueFinished->data[$q_finished_key]->state : false ;
										$row->send_status 					= ( !empty( $queueFinished->data[$q_finished_key]->state ) ) ? map_coggins_status( $queueFinished->data[$q_finished_key]->state ) : $row->send_status ;
										$row->send_status_timestamp 		= ( !empty( $queueFinished->data[$q_finished_key]->scheduled ) ) ? date( 'Y-m-d H:i:s', ( $queueFinished->data[$q_finished_key]->scheduled/1000 ) ) : $row->send_status_timestamp;

										if( isset( $queueFinished->data[$q_finished_key]->distribution->status->progress ) ){
											$row->coggins_progress			= ( $queueFinished->data[$q_finished_key]->distribution->status->progress !== false ) ? ( string ) $queueFinished->data[$q_finished_key]->distribution->status->progress : NULL ;
										}

										if( isset( $queueFinished->data[$q_finished_key]->distribution->status->errors ) ){
											$row->coggins_errors			= ( $queueFinished->data[$q_finished_key]->distribution->status->errors !== false ) ? ( string ) $queueFinished->data[$q_finished_key]->distribution->status->errors : NULL ;
										}
									}
								}
							}

							if( $bundle_found !== false ){
								$upd_data = [
									"coggins_state" 		=> $row->coggins_state,
									"send_status_timestamp" => $row->send_status_timestamp,
									"send_status" 			=> $row->send_status,
									"last_modified_by" 		=> $this->ion_auth->_current_user->id,
									"coggins_progress" 		=> ( !empty( $row->coggins_progress ) ) ? $row->coggins_progress : NULL ,
									"coggins_errors" 		=> ( !empty( $row->coggins_errors ) ) ? $row->coggins_errors : NULL ,
									"error_message" 		=> ( !empty( $row->error_message ) ) ? $row->error_message : '' ,
								];

								$this->db->update( "distribution_bundles", $upd_data, ["distribution_bundle_id"=>$row->distribution_bundle_id] );
							}

						} else {
							// no update
						}
						$result_data[$key] = $row;
					}
				}

				$result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
				$result->records 			= $result_data;
				$counters 					= $this->get_distribution_bundles_totals( $account_id, $distribution_group_id, $search_term, $raw_where );
				$result->counters->total 	= ( !empty( $counters->total ) ) ? $counters->total : null;
				$result->counters->pages 	= ( !empty( $counters->pages ) ) ? $counters->pages : null;
				$result->counters->limit  	= ( !empty( $apply_limit ) ) ? $limit : $result->counters->total;
				$result->counters->offset 	= $offset;

				$this->session->set_flashdata( 'message','Distribution Bundles data found' );
			} else {
				$this->session->set_flashdata( 'message','There\'s currently no Distribution Bundles for your Account' );
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}

		return $result;
	}


	/** Get Distribution Bundles lookup counts **/
	public function get_distribution_bundles_totals( $account_id = false, $distribution_group_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT ){
		$result = false;
		if( !empty( $account_id ) ){

			$this->db->select( 'distribution_bundles.distribution_bundle_id', false )
				->where( 'distribution_bundles.is_active', 1 )
				->where( 'distribution_bundles.account_id', $account_id );

			$where = convert_to_array( $where );

			if( !empty( $search_term ) ){
				$search_term  = trim( urldecode( $search_term ) );
				$search_where = [];
				if( strpos( $search_term, ' ') !== false ) {
					$multiple_terms = explode( ' ', $search_term );
					foreach( $multiple_terms as $term ){
						foreach( $this->distribution_bundles_search_fields as $k=>$field ){
							$search_where[$field] = trim( $term );
						}

						$where_combo = format_like_to_where( $search_where );
						$this->db->where( $where_combo );
					}
				} else {
					foreach( $this->distribution_bundles_search_fields as $k=>$field ){
						$search_where[$field] = $search_term;
					}

					$where_combo = format_like_to_where( $search_where );
					$this->db->where( $where_combo );
				}
			}

			if( !empty( $distribution_group_id ) || isset( $where['distribution_group_id'] ) ){
				$distribution_group_id	= ( !empty( $distribution_group_id ) ) ? $distribution_group_id : $where['distribution_group_id'];
				if( !empty( $distribution_group_id ) ){
					$this->db->where( 'distribution_bundles.distribution_group_id', $distribution_group_id );
				}
				unset( $where['distribution_group_id'] );
			}

			if( isset( $where['distribution_bundle'] ) ){
				if( !empty( $where['distribution_bundle'] ) ){
					$distribution_bundle_ref = strtoupper( strip_all_whitespace( $where['distribution_bundle'] ) );
					$this->db->where( '( distribution_bundles.distribution_bundle = "'.$where['distribution_bundle'].'" OR distribution_bundles.distribution_bundle_ref = "'.$distribution_bundle_ref.'" )' );
				}
				unset( $where['category_name'] );
			}

			if( !empty( $where ) ){
				$this->db->where( $where );
			}


			$query 			  = $this->db->from( 'distribution_bundles' )->count_all_results();
			$results['total'] = !empty( $query ) ? $query : 0;
			$limit 				= ( $limit > 0 ) ? $limit : $results['total'];
			$results['pages'] = !empty( $query ) ? ceil( $query / $limit ) : 0;
			return json_decode( json_encode( $results ) );
		}
		return $result;
	}


	/** Add a NEW Distribution Bundle **/
	public function add_distribution_bundle( $account_id = false, $distribution_bundle_data = false ){

		$result = null;

		if( !empty( $account_id ) && !empty( $distribution_bundle_data  ) ){

			if( isset( $distribution_bundle_data['bundle_sites'] ) ){
				$bundle_sites = isset( $distribution_bundle_data['bundle_sites'] ) ? $distribution_bundle_data['bundle_sites'] : [];
				$bundle_sites = ( is_json( $bundle_sites ) ) ? json_decode( $bundle_sites ) : $bundle_sites;
				unset( $distribution_bundle_data['bundle_sites'] );
			}

			if( isset( $distribution_bundle_data['bundle_content'] ) ){
				$bundle_content = isset( $distribution_bundle_data['bundle_content'] ) ? $distribution_bundle_data['bundle_content'] : [];
				$bundle_content = ( is_json( $bundle_content ) ) ? json_decode( $bundle_content ) : $bundle_content;
				unset( $distribution_bundle_data['bundle_content'] );
			}

			$data = [];
			foreach( $distribution_bundle_data as $col => $value ){
				if( $col == 'distribution_bundle' ){
					$data['distribution_bundle_ref'] 	= strtoupper( strip_all_whitespace( $value ) ).$account_id.$data['distribution_group_id'].date( 'Y' );
				}

				if( in_array($col, format_date_columns() ) ){
					$value = format_datetime_db($value);
				}else{
					$value = ( is_string( $value ) ) ? trim( $value ) : $value;
				}
				$data[$col] = $value;
			}

			$check_exists = $this->db->where( 'account_id', $account_id )
				->where( 'distribution_bundles.distribution_group_id', $data['distribution_group_id'] )
				// ->where( '( distribution_bundles.distribution_bundle = "'.$data['distribution_bundle'].'" OR distribution_bundles.distribution_bundle_ref = "'.$data['distribution_bundle_ref'].'" )' )
				->where( '( distribution_bundles.distribution_bundle_ref = "'.$data['distribution_bundle_ref'].'" )' )
				->limit( 1 )
				->get( 'distribution_bundles' )
				->row();

			$data = $this->ssid_common->_filter_data( 'distribution_bundles', $data );


			if( !empty( $check_exists  ) ){
				$data['last_modified_by'] = $this->ion_auth->_current_user->id;
				$this->db->where( 'distribution_bundle_id', $check_exists->distribution_bundle_id )
					->update( 'distribution_bundles', $data );
					$message = 'This Distribution Bundle already exists, record has been updated successfully.';
					$result = $check_exists;
				$data['distribution_bundle_id'] = $check_exists->distribution_bundle_id;
			} else {

				$data['created_by'] 			= $this->ion_auth->_current_user->id;

				if( !empty( $data['base_line'] ) && ( $data['base_line'] == 1 ) ){
					$data['send_status'] = "complete";
				}

				$data['send_status_timestamp'] 	= date( 'Y-m-d H:i:s' );
				$this->db->insert( 'distribution_bundles', $data );
				$message = 'New Distribution Bundle added successfully.';
				$data['distribution_bundle_id'] = ( string ) $this->db->insert_id();
				$result = $data;
			}

			## Add Linked Sites
			if( isset( $bundle_sites ) ){
				$data['bundle_sites'] = $bundle_sites;
				$this->add_distribution_bundle_sites( $account_id, $data['distribution_bundle_id'], $data );
			}

			## Add Linked Content
			if( isset( $bundle_content ) ){
				$data['bundle_content'] = $bundle_content;
				$this->add_distribution_bundle_content( $account_id, $data['distribution_bundle_id'], $data );
			}

			$this->session->set_flashdata( 'message', $message );

		}else{
			$this->session->set_flashdata( 'message','Error! Missing required information.' );
		}

		return $result;
	}


	/** Update Distribution Bundle **/
	public function update_distribution_bundle( $account_id = false, $distribution_bundle_data = false ){

		$result = null;

		if( !empty( $account_id ) && !empty( $distribution_bundle_data['distribution_bundle_id'] ) && !empty( $distribution_bundle_data ) ){

			if( isset( $distribution_bundle_data['bundle_sites'] ) ){
				$bundle_sites = isset( $distribution_bundle_data['bundle_sites'] ) ? $distribution_bundle_data['bundle_sites'] : [];
				$bundle_sites = ( is_json( $bundle_sites ) ) ? json_decode( $bundle_sites ) : $bundle_sites;
				unset( $distribution_bundle_data['bundle_sites'] );
			}

			if( isset( $distribution_bundle_data['bundle_content'] ) ){
				$bundle_content = isset( $distribution_bundle_data['bundle_content'] ) ? $distribution_bundle_data['bundle_content'] : [];
				$bundle_content = ( is_json( $bundle_content ) ) ? json_decode( $bundle_content ) : $bundle_content;
				unset( $distribution_bundle_data['bundle_content'] );
			}

			$data = [];
			foreach( $distribution_bundle_data as $col => $value ){
				if( $col == 'distribution_bundle' ){
					$data['distribution_bundle_ref'] 	= strtoupper( strip_all_whitespace( $value ) ).$account_id.$data['distribution_group_id'].date( 'Y' );
				}

				if( in_array($col, format_date_columns() ) ){
					$value = format_datetime_db($value);
				}else{
					$value = ( is_string( $value ) ) ? trim( $value ) : $value;
				}
				$data[$col] = $value;
			}

			if( !empty( $data['distribution_bundle_id'] ) ){
				$check_conflict = $this->db->where( 'account_id', $account_id )
					// ->where( '( distribution_bundles.distribution_bundle = "'.$data['distribution_bundle'].'" OR distribution_bundles.distribution_bundle_ref = "'.$data['distribution_bundle_ref'].'" )' )
					->where( '( distribution_bundles.distribution_bundle_ref = "'.$data['distribution_bundle_ref'].'" )' )
					->where( 'distribution_bundles.distribution_bundle_id !=', $data['distribution_bundle_id'] )
					->get( 'distribution_bundles' )->row();

				$data = $this->ssid_common->_filter_data( 'distribution_bundles', $data );

				if( !$check_conflict ){
					$data['last_modified_by'] = $this->ion_auth->_current_user->id;
					$this->db->where( [ 'account_id'=>$account_id, 'distribution_bundle_id'=>$data['distribution_bundle_id'] ] )
						->update( 'distribution_bundles', $data );
						if( $this->db->trans_status() !== false ){

							if( isset( $bundle_sites ) ){
								$data['bundle_sites'] = $bundle_sites;
								$this->add_distribution_bundle_sites( $account_id, $distribution_bundle_data['distribution_bundle_id'], $data );
							}

							if( isset( $bundle_content ) ){
								$data['bundle_content'] = $bundle_content;
								$this->add_distribution_bundle_content( $account_id, $distribution_bundle_data['distribution_bundle_id'], $data );
							}

							$result = $this->get_distribution_bundles( $account_id, false, $data['distribution_bundle_id'] );
							$this->session->set_flashdata( 'message', 'Distribution Bundle updated successfully.' );
						}

				} else {
					$this->session->set_flashdata( 'message', 'This Distribution Bundle does not exists or does not belong to you.' );
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


	/** Delete Distribution Bundle record **/
	public function delete_distribution_bundle( $account_id = false, $distribution_bundle_id = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $distribution_bundle_id ) ){
			$conditions 		= ['account_id'=>$account_id,'distribution_bundle_id'=>$distribution_bundle_id];
			$distribution_bundle_exists 	= $this->db->get_where( 'distribution_bundles',$conditions )->row();

			if( !empty( $distribution_bundle_exists ) ){

				## Drop linked Sites
				$this->db->where( 'distribution_bundle_id', $distribution_bundle_id )
					->where( 'account_id', $account_id )
					->delete( 'distribution_bundle_sites' );
					$this->ssid_common->_reset_auto_increment( 'distribution_bundle_sites', 'bundle_site_id' );

				## Drop linked Assets
				$this->db->where( 'distribution_bundle_id', $distribution_bundle_id )
					->where( 'account_id', $account_id )
					->delete( 'distribution_bundle_content' );
					$this->ssid_common->_reset_auto_increment( 'distribution_bundle_content', 'bundle_content_id' );

				$this->db->where( $conditions )
					->delete( 'distribution_bundles' );
					$this->ssid_common->_reset_auto_increment( 'distribution_bundles', 'distribution_bundle_id' );

				if( $this->db->trans_status() !== FALSE ){
					$this->session->set_flashdata('message','Distribution Bundle deleted successfully.');
					$result = true;
				}
			}else{
				$this->session->set_flashdata( 'message','Invalid Distribution Bundle ID' );
			}

		}else{
			$this->session->set_flashdata( 'message','No Distribution Bundle record found.' );
		}
		return $result;
	}


	/** Add Distribution Group Providers **/
	public function add_distribution_group_providers( $account_id = false, $distribution_group_id = false, $incoming_postdata = false ){

		$result = false;

		if( !empty( $account_id ) && !empty( $distribution_group_id ) && !empty( $incoming_postdata ) ){

			$postdata = [];

			foreach( $incoming_postdata as $col => $value ){
				$postdata[$col] = $value;
			}

			$provider_details 	= isset( $postdata['provider_details'] ) ? $postdata['provider_details'] : [];
			if( is_object( $provider_details ) ){
				$provider_details = object_to_array( $provider_details );
			} else if ( is_json( $provider_details ) && !is_object( $provider_details ) ){
				$provider_details = ( is_object( $provider_details ) ) ? object_to_array( $provider_details ) : $provider_details;
				$provider_details = json_decode( $provider_details );
			} else {
				$provider_details = $provider_details;
			}

			unset( $postdata['provider_details'] );

			$new_providers = $exists  = $incoming_records = $invalid_providers = $pre_existing_providers = [];

			if( isset( $provider_details ) ){

				$this->ssid_common->_reset_auto_increment( 'distribution_group_providers', 'combination_id' );

				foreach( $provider_details as $provider_id => $provider ){

					$provider = ( is_object( $provider ) ) ? object_to_array( $provider ) : $provider;

					$check_provider_exists 	= $this->db->select( 'provider_id' )->get_where( 'content_provider', [ 'account_id'=>$account_id, 'provider_id'=>$provider['provider_id'] ] )->row();

					if( !empty( $check_provider_exists ) ){

						$check_exists 		= $this->db->get_where( 'distribution_group_providers', [ 'distribution_group_id'=>$distribution_group_id, 'provider_id'=>$provider['provider_id'],  'no_of_titles_id'=>$provider['no_of_titles_id'] ] )->row();

						if( !empty( $check_exists ) ){
							$exists[] = [
								'combination_id'		=> $check_exists->combination_id,
								'provider_id'			=> $provider['provider_id'],
								'provider_name'			=> $provider['provider_name'],
								'no_of_titles_id'		=> $provider['no_of_titles_id'],
								'no_of_titles'			=> $provider['no_of_titles'],
								'films_per_month'		=> !empty( $provider['films_per_month'] ) 		? $provider['films_per_month'] : null,
								'films_per_month_id'	=> !empty( $provider['films_per_month_id'] ) 	? $provider['films_per_month_id'] : null,
								'last_modified_by'		=> $this->ion_auth->_current_user->id
							];
						} else {
							$new_providers[] = [
								'provider_id'			=> $provider['provider_id'],
								'provider_name'			=> $provider['provider_name'],
								'no_of_titles_id'		=> $provider['no_of_titles_id'],
								'no_of_titles'			=> $provider['no_of_titles'],
								'films_per_month'		=> !empty( $provider['films_per_month'] ) 		? $provider['films_per_month'] : null,
								'films_per_month_id'	=> !empty( $provider['films_per_month_id'] ) 	? $provider['films_per_month_id'] : null,
								'account_id'			=> $account_id,
								'distribution_group_id'	=> $distribution_group_id,
								'created_by'			=> $this->ion_auth->_current_user->id
							];
						}

					} else {
						$invalid_providers[] = ['provider_id'=>$provider['provider_id'], 'no_of_titles'=>$provider['no_of_titles']];
					}

					$incoming_records[]  = ['provider_id'=>$provider['provider_id'], 'no_of_titles'=>$provider['no_of_titles']];

				}
			}

			if( !empty( $new_providers ) ){
				$this->db->insert_batch( 'distribution_group_providers', $new_providers );
			}

			if( !empty( $exists ) ){
				$this->db->update_batch( 'distribution_group_providers', $exists, 'combination_id' );
			}

			if( $this->db->trans_status() !== false ){
				$result = $incoming_records;
				$this->session->set_flashdata( 'message','Distribution Group Providers added successfully.' );
				if( !empty( $invalid_providers ) ){
					$this->session->set_flashdata( 'message','Some Providers were not added as they are invalid. Invalid list '.json_encode($invalid_providers) );
				}
			} else {
				$this->session->set_flashdata( 'message','Your request is missing required information' );
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}
		return $result;
	}

	/**
	* Get all Providers Linked to a Distribution Group
	*/
	public function get_distribution_group_providers( $account_id = false, $distribution_group_id = false, $where = false, $order_by = false, $limit = false, $offset = false ){
		$result = false;
		if( !empty( $account_id ) ){

			$this->db->select( 'dgp.*, distribution_groups.distribution_group, content_provider.provider_name', false );

			if( !empty( $account_id ) ){
				$this->db->where( 'dgp.account_id', $account_id );
			}

			if( !empty( $distribution_group_id ) ){
				$this->db->where( 'dgp.distribution_group_id', $distribution_group_id );
			}

			$query = $this->db->join( 'distribution_groups', 'distribution_groups.distribution_group_id = dgp.distribution_group_id', 'left' )
				->join( 'content_provider', 'content_provider.provider_id = dgp.provider_id', 'left' )
				->get( 'distribution_group_providers dgp' );

			if( $query->num_rows() > 0 ){
				$result = $query->result();
				$this->session->set_flashdata( 'message','Distribution Providers data found' );
			} else {
				$this->session->set_flashdata( 'message','No data found' );
			}
		}else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}

		return $result;
	}


	/** Delete Distribution Group Provider**/
	public function delete_distribution_group_provider( $account_id = false, $combination_id = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $combination_id ) ){
			$conditions 				= ['account_id'=>$account_id,'combination_id'=>$combination_id];
			$distribution_bundle_exists = $this->db->get_where( 'distribution_group_providers',$conditions )->row();

			if( !empty( $distribution_bundle_exists ) ){

				$this->db->where( $conditions )
					->delete( 'distribution_group_providers' );

				if( $this->db->trans_status() !== FALSE ){
					$this->session->set_flashdata('message','Distribution Group Provider deleted successfully.');
					$result = true;
				}
			}else{
				$this->session->set_flashdata( 'message','Invalid Distribution Group Provider ID' );
			}

		}else{
			$this->session->set_flashdata( 'message','No Distribution Group Provider record found.' );
		}
		return $result;
	}


	/** Add Distribution Bundle Sites **/
	public function add_distribution_bundle_sites( $account_id = false, $distribution_bundle_id = false, $postdata = false ){
		$result = false;

		if( !empty( $account_id ) && !empty( $distribution_bundle_id ) && !empty( $postdata ) ){
			$postdata 		= convert_to_array( $postdata );
			$bundle_sites 	= isset( $postdata['bundle_sites'] ) ? $postdata['bundle_sites'] : [];

			unset( $postdata['bundle_sites'] );

			$new_sites = $exists  = $incoming_records = $invalid_sites = $pre_existing_sites = [];
			if( isset( $bundle_sites ) ){

				$get_existing_sites = $this->db->select( 'site_id' )
					->group_by( 'site_id' )
					->get_where( 'distribution_bundle_sites', [ 'distribution_bundle_id'=>$distribution_bundle_id ] );

				if( $get_existing_sites->num_rows() > 0 ){
					$pre_existing_sites = array_column( $get_existing_sites->result_array(), 'site_id' );
				}

				$deleted_sites = array_diff( $pre_existing_sites, $bundle_sites );

				#Drop Deleted Sites
				if( !empty( $deleted_sites ) ){
					$this->db->where( 'distribution_bundle_id', $distribution_bundle_id )
						->where_in( 'site_id', $deleted_sites )
						->delete( 'distribution_bundle_sites' );

					$this->ssid_common->_reset_auto_increment( 'distribution_bundle_sites', 'bundle_site_id' );
				}

				foreach( $bundle_sites as $key => $site_id ){

					$check_sites_exists 	= $this->db->select( 'site_id' )->get_where( 'site', [ 'account_id'=>$account_id, 'site_id'=>$site_id ] )->row();

					if( !empty( $check_sites_exists ) ){

						$check_exists 		= $this->db->get_where( 'distribution_bundle_sites', [ 'distribution_bundle_id'=>$distribution_bundle_id, 'site_id'=>$site_id ] )->row();

						if( !empty( $check_exists ) ){
							$exists[] = [
								'bundle_site_id'	=> $check_exists->bundle_site_id,
								'site_id'			=> $site_id,
								'last_modified_by'	=> $this->ion_auth->_current_user->id
							];
						} else {
							$new_sites[] = [
								'site_id'				=> $site_id,
								'account_id'			=> $account_id,
								'distribution_bundle_id'=> $distribution_bundle_id,
								'created_by'			=> $this->ion_auth->_current_user->id
							];
						}

					} else {
						$invalid_sites[] = $site_id;
					}

					$incoming_records[]  = $site_id;

				}
			}

			if( !empty( $new_sites ) ){
				$this->db->insert_batch( 'distribution_bundle_sites', $new_sites );
			}

			if( !empty( $exists ) ){
				$this->db->update_batch( 'distribution_bundle_sites', $exists, 'bundle_site_id' );
			}

			if( $this->db->trans_status() !== false ){
				$result = $incoming_records;
				$this->session->set_flashdata( 'message','Distribution Bundle Sites added successfully.' );
				if( !empty( $invalid_sites ) ){
					$this->session->set_flashdata( 'message','Some Sites were not added as they are invalid. Invalid list '.json_encode($invalid_sites) );
				}
			} else {
				$this->session->set_flashdata( 'message','Your request is missing required information' );
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}
		return $result;
	}

	/**
	* Get all Sites linked to a Distribution Bundle
	*/
	public function get_distribution_bundle_sites( $account_id = false, $distribution_bundle_id = false, $where = false, $order_by = false, $limit = false, $offset = false ){
		$result = false;
		if( !empty( $account_id ) ){

			$this->db->select( 'dbs.*, distribution_bundles.distribution_bundle, site.site_name', false );

			if( !empty( $account_id ) ){
				$this->db->where( 'dbs.account_id', $account_id );
			}

			if( !empty( $distribution_bundle_id ) ){
				$this->db->where( 'dbs.distribution_bundle_id', $distribution_bundle_id );
			}

			$query = $this->db->join( 'distribution_bundles', 'distribution_bundles.distribution_bundle_id = dbs.distribution_bundle_id', 'left' )
				->join( 'site', 'site.site_id = dbs.site_id', 'left' )
				->get( 'distribution_bundle_sites dbs' );

			if( $query->num_rows() > 0 ){
				$result = $query->result();
				$this->session->set_flashdata( 'message','Distribution Bundle Sites data found' );
			} else {
				$this->session->set_flashdata( 'message','No data found' );
			}
		}else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}

		return $result;
	}

	/** Add Distribution Bundle Content **/
	public function add_distribution_bundle_content( $account_id = false, $distribution_bundle_id = false, $postdata = false ){
		$result = false;

		if( !empty( $account_id ) && !empty( $distribution_bundle_id ) && !empty( $postdata ) ){
			$postdata 				= convert_to_array( $postdata );
			$bundle_content 		= isset( $postdata['bundle_content'] ) 			? $postdata['bundle_content'] 									: [];
			$license_start_date 	= !empty( $postdata['license_start_date'] ) 	? date( 'Y-m-d', strtotime( $postdata['license_start_date'] ) ) : null;
			$distribution_group_id 	= !empty( $postdata['distribution_group_id'] ) 	? $postdata['distribution_group_id'] 							: null;

			unset( $postdata['bundle_content'] );

			$new_content = $exists  = $incoming_records = $invalid_content = $pre_existing_content = [];
			if( isset( $bundle_content ) ){

				$get_existing_content = $this->db->select( 'content_id' )
					->group_by( 'content_id' )
					->get_where( 'distribution_bundle_content', [ 'distribution_bundle_id'=>$distribution_bundle_id ] );

				if( $get_existing_content->num_rows() > 0 ){
					$pre_existing_content = array_column( $get_existing_content->result_array(), 'content_id' );
				}

				$deleted_content = array_diff( $pre_existing_content, $bundle_content );

				#Drop Deleted Content
				if( !empty( $deleted_content ) ){
					$this->db->where( 'distribution_bundle_id', $distribution_bundle_id )
						->where_in( 'content_id', $deleted_content )
						->delete( 'distribution_bundle_content' );

					$this->ssid_common->_reset_auto_increment( 'distribution_bundle_content', 'bundle_content_id' );
				}

				foreach( $bundle_content as $key => $content_id ){

					$check_content_exists 	= $this->db->select( 'content_id' )->get_where( 'content', [ 'account_id'=>$account_id, 'content_id'=>$content_id ] )->row();

					if( !empty( $check_content_exists ) ){

						$check_exists 		= $this->db->get_where( 'distribution_bundle_content', [ 'distribution_bundle_id'=>$distribution_bundle_id, 'content_id'=>$content_id ] )->row();
						$film_attributes	= $this->content_service->_fetch_content_attributes( $account_id, $content_id );
						$film_profile		= $this->content_service->distribution_content( $account_id, $content_id );

						if( !empty( $check_exists ) ){
							$exists[] = [
								'bundle_content_id'		=> $check_exists->bundle_content_id,
								'content_id'			=> $content_id,
								'last_modified_by'		=> $this->ion_auth->_current_user->id,
								'codec_definition'		=> !empty( $film_attributes->codec_definition ) ? $film_attributes->codec_definition : null,
								'content_languages'		=> !empty( $film_attributes->content_languages ) ? json_encode( $film_attributes->content_languages ) : null,
								'content_group_class'	=> !empty( $film_attributes->content_group_class ) ? $film_attributes->content_group_class : null,
								'content_group_color'	=> !empty( $film_attributes->content_group_color ) ? $film_attributes->content_group_color : null,
								'license_start_date'	=> $license_start_date,
								'provider_id'			=> !empty( $film_profile->provider_id ) 	? $film_profile->provider_id 	: null,
								'provider_name'			=> !empty( $film_profile->provider_name ) 	? $film_profile->provider_name 	: null,
								'latest_entry'			=> 0
							];
						} else {

							$new_content[] = [
								'content_id'			=> $content_id,
								'account_id'			=> $account_id,
								'distribution_group_id' => $distribution_group_id,
								'distribution_bundle_id'=> $distribution_bundle_id,
								'created_by'			=> $this->ion_auth->_current_user->id,
								'codec_definition'		=> !empty( $film_attributes->codec_definition ) ? $film_attributes->codec_definition : null,
								'content_languages'		=> !empty( $film_attributes->content_languages ) ? json_encode( $film_attributes->content_languages ) : null,
								'content_group_class'	=> !empty( $film_attributes->content_group_class ) ? $film_attributes->content_group_class : null,
								'content_group_color'	=> !empty( $film_attributes->content_group_color ) ? $film_attributes->content_group_color : null,
								'license_start_date'	=> $license_start_date,
								'provider_id'			=> !empty( $film_profile->provider_id ) 	? $film_profile->provider_id 	: null,
								'provider_name'			=> !empty( $film_profile->provider_name ) 	? $film_profile->provider_name 	: null,
								'latest_entry'			=> 1
							];
						}

					} else {
						$invalid_content[] = $content_id;
					}

					$incoming_records[]  = $content_id;

				}
			}

			if( !empty( $new_content ) ){

				## Set all previous records as not-latest
				$this->db->where( [ 'dbc.account_id'=>$account_id, 'dbc.distribution_group_id'=>$distribution_group_id ] )
					->update( 'distribution_bundle_content dbc', [ 'dbc.latest_entry'=>0 ] );

				$this->db->insert_batch( 'distribution_bundle_content', $new_content );
			}

			if( !empty( $exists ) ){
				$this->db->update_batch( 'distribution_bundle_content', $exists, 'bundle_content_id' );
			}

			if( $this->db->trans_status() !== false ){
				$result = $incoming_records;
				$this->session->set_flashdata( 'message','Distribution Bundle Content added successfully.' );
				if( !empty( $invalid_content ) ){
					$this->session->set_flashdata( 'message','Some Content were not added as they are invalid. Invalid list '.json_encode($invalid_content) );
				}
			} else {
				$this->session->set_flashdata( 'message','Your request is missing required information' );
			}
		} else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}
		return $result;
	}

	/**
	* Get all Content linked to a Distribution Bundle
	*/
	public function get_distribution_bundle_content( $account_id = false, $distribution_bundle_id = false, $bundle_content_id = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;
		if( !empty( $account_id ) ){

			$where = convert_to_array( $where );

			## Filter By Distribution Group
			if( isset( $where['distribution_group_id'] ) && empty( $where['site_id'] ) ){
				if( !empty( $where['distribution_group_id'] ) ){

						$active_distr_where = '( distribution_bundles.is_active =1 OR distribution_bundles.is_active IS NULL )';
						$this->db->where( $active_distr_where );

						$this->db->where( 'account_id', $account_id );
						$this->db->where( 'distribution_group_id', $where['distribution_group_id'] );
						$this->db->where_not_in( 'send_status', ['cancelled','canceled'] );
						$bundles_query = $this->db->get( 'distribution_bundles' );

					if( $bundles_query->num_rows() > 0 ){
						$bundle_ids = array_column( $bundles_query->result_array(), 'distribution_bundle_id' );
						if( !empty( $bundle_ids ) ){
							$this->db->where_in( 'dbc.distribution_bundle_id', $bundle_ids );
						} else {
							$this->session->set_flashdata( 'message','No data found matching your criteria' );
							return false;
						}
					} else {
						$this->session->set_flashdata( 'message','No data found matching your criteria' );
						return false;
					}
				}
				unset( $where['distribution_group_id'] );
			}

			## Filter By Site
			if( isset( $where['site_id'] ) && empty( $where['distribution_group_id'] ) ){
				if( !empty( $where['site_id'] ) ){
					$sites_query = $this->db->get_where( 'distribution_bundle_sites', ['account_id'=>$account_id, 'site_id'=>$where['site_id']] );
					if( $sites_query->num_rows() > 0 ){
						$site_ids = array_column( $sites_query->result_array(), 'distribution_bundle_id' );
						if( !empty( $site_ids ) ){
							$this->db->where_in( 'dbc.distribution_bundle_id', $site_ids );
						} else {
							$this->session->set_flashdata( 'message','No data found matching your criteria' );
							return false;
						}
					} else {
						$this->session->set_flashdata( 'message','No data found matching your criteria' );
						return false;
					}
				}
				unset( $where['site_id'] );
			}

			$this->db->select( 'dbc.*, distribution_bundles.distribution_bundle, content_film.*, content_clearance.clearance_start_date `clearance_date`, age_rating.age_rating_name, age_rating.age_rating_desc', false )
				->join( 'distribution_bundles', 'distribution_bundles.distribution_bundle_id = dbc.distribution_bundle_id', 'left' )
				->join( 'content_film', 'dbc.content_id = content_film.content_id', 'left' )
				->join( 'age_rating', 'age_rating.age_rating_id = content_film.age_rating_id', 'left' )
				->join( 'content_clearance', 'content_clearance.content_id = content_film.content_id', 'left' )
				->where( 'dbc.account_id', $account_id )
				->order_by( 'dbc.provider_name' )
				->group_by( 'dbc.content_id' );
				
				## Added 17/08/2022 HOT FIX to limit to content to just UIP for viewing Stats EK
				if( isset( $where['limit_to_uip'] ) ){
					if( !empty( $where['limit_to_uip'] ) ){
						$this->db->where( 'dbc.provider_id', 2 );
					}
					unset( $where['limit_to_uip'] );
				}

			if( !empty( $bundle_content_id ) || isset( $where['bundle_content_id'] ) ){
				$bundle_content_id = !empty( $bundle_content_id ) ? $bundle_content_id : ( !empty( $where['bundle_content_id'] ) ? $where['bundle_content_id'] : false );
				if( !empty( $bundle_content_id ) ){
					$row = $this->db->get_where( 'distribution_bundle_content dbc', ['dbc.bundle_content_id'=>$bundle_content_id] )->row();
					if( !empty( $row ) ){
						$row->film_attributes = $this->content_service->_fetch_content_attributes( $account_id, $row->content_id );
						if( $row->content_in_use == 0 ){

							$aging_period		= LIBRARY_AGING_PERIOD_IN_DAYS;
							$date_today 		= date( 'Y-m-d' );
							$license_check_date = valid_date( $row->removal_date ) ? date( 'Y-m-d', strtotime( $row->removal_date ) ) : date( 'Y-m-d', strtotime( $row->license_start_date ) );
							$elaspsed_months	= _number_of_months( $license_check_date, $date_today );

							if( $elaspsed_months >= 0 && ( $elaspsed_months <= $aging_period ) ){
								$row->content_group 		= 'Library';
								$row->content_group_class 	= 'library-film-red';
								$row->content_group_color 	= 'red';
							} else if( $elaspsed_months > $aging_period ){
								$row->content_group 		= 'Library';
								$row->content_group_class 	= 'library-film-orange';
								$row->content_group_color 	= 'orange';
							} else {
								$row->content_group 		= 'Latest';
								$row->content_group_class 	= 'latest-film';
								$row->content_group_color 	= 'blue';
							}

						} else {

							$row->content_group 		= 'Latest';
							$row->content_group_class 	= 'latest-film';
							$row->content_group_color 	= 'blue';

						}

						$this->session->set_flashdata( 'message','Bundle Content data found' );
						return $row;
					} else {
						$this->session->set_flashdata( 'message','No data found matching your criteria' );
						return false;
					}
				} else {
					$this->session->set_flashdata( 'message','No data found matching your criteria' );
					return false;
				}
				unset( $where['distribution_bundle_id'] );
			}

			if( !empty( $distribution_bundle_id ) || isset( $where['distribution_bundle_id'] ) ){
				$distribution_bundle_id = !empty( $distribution_bundle_id ) ? $distribution_bundle_id : ( !empty( $where['distribution_bundle_id'] ) ? $where['distribution_bundle_id'] : false );
				if( !empty( $distribution_bundle_id ) ){
					$this->db->where( 'dbc.distribution_bundle_id', $distribution_bundle_id );
				}
				unset( $where['distribution_bundle_id'] );
			}

			if( isset( $where['content_in_use'] ) ){
				if( !empty( $where['content_in_use'] ) ){
					$this->db->where( 'dbc.content_in_use', $where['content_in_use'] );
				}
				unset( $where['content_in_use'] );
			}

			if( isset( $where['grouped'] ) ){
				if( !empty( $where['grouped'] ) ){
					$grouped = true;
				}
				unset( $where['grouped'] );
			}

			$query = $this->db->get( 'distribution_bundle_content dbc' );

			if( $query->num_rows() > 0 ){
				$data 					= [];

				foreach( $query->result() as $key => $row ){

					$row->film_attributes = $this->content_service->_fetch_content_attributes( $account_id, $row->content_id, false, ['distribution_group_id'=>$row->distribution_group_id] );

					if( !empty( $grouped ) ){

						if( $row->content_in_use == 0 ){

							$aging_period		= LIBRARY_AGING_PERIOD_IN_DAYS;
							$date_today 		= date( 'Y-m-d' );
							$license_check_date = valid_date( $row->removal_date ) ? date( 'Y-m-d', strtotime( $row->removal_date ) ) : date( 'Y-m-d', strtotime( $row->license_start_date ) );
							$elaspsed_months	= _number_of_months( $license_check_date, $date_today );

							if( $elaspsed_months >= 0 && ( $elaspsed_months <= $aging_period ) ){
								$row->content_group 		= 'Library';
								$row->content_group_class 	= 'library-film-red';
								$row->content_group_color 	= 'red';
								// $data['library_films'][$key] = $row;
								$data['library_films'][$row->provider_name][$key] = $row;
							} else if( $elaspsed_months > $aging_period ){
								$row->content_group 		= 'Library';
								$row->content_group_class 	= 'library-film-orange';
								$row->content_group_color 	= 'orange';
								// $data['recylable_films'][$key] = $row;
								$data['recylable_films'][$row->provider_name][$key] = $row;
							} else {
								$row->content_group 		= 'Latest';
								$row->content_group_class 	= 'latest-film';
								$row->content_group_color 	= 'blue';
								// $data['current_films'][$key] = $row;
								$data['current_films'][$row->provider_name][$key] = $row;
							}

						} else {
							#$row->content_group 		= 'Latest';
							#$row->content_group_class 	= 'latest-film';
							#$row->content_group_color 	= 'blue';
							$data['current_films'][$row->provider_name][$key] = $row;

						}
						// $data['all_films'][$key] 		 = $row;
						$data['all_films'][$row->provider_name][$key] 		 = $row;
					} else {
						// $data[$key] = $row;
						$data[$key] = $row;
					}
				}
				$result = $data;
				$this->session->set_flashdata( 'message','Distribution Bundle Content data found' );
			} else {
				$this->session->set_flashdata( 'message','No data found' );
			}
		}else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}

		return $result;
	}


	/** Update Bundle Content record **/
	public function update_bundle_content( $account_id = false, $bundle_content_data = false ){

		$result = null;

		if( !empty( $account_id ) && !empty( $bundle_content_data ) ){

			if( isset( $bundle_content_data['auto_remove_ids'] ) ){
				$auto_remove_ids = !empty( $bundle_content_data['auto_remove_ids'] ) ? $bundle_content_data['auto_remove_ids'] : [];
				$auto_remove_ids = ( is_json( $auto_remove_ids ) ) ? json_decode( $auto_remove_ids ) : $auto_remove_ids;
				unset( $bundle_content_data['auto_remove_ids'] );
			}

			$data = [];
			foreach( $bundle_content_data as $col => $value ){

				if( in_array($col, format_date_columns() ) ){
					$value = format_datetime_db($value);
				} else {
					$value = ( is_string( $value ) ) ? trim( $value ) : $value;
				}
				$data[$col] = $value;
			}

			if( !empty( $auto_remove_ids ) ){
				$group_data = [];
				foreach( $auto_remove_ids as $key => $bundle_content_id ){
					$group_data[$key]['bundle_content_id'] 	= $bundle_content_id;
					$group_data[$key]['content_in_use'] 	= 0;
					$group_data[$key]['latest_entry'] 		= 0;
					$group_data[$key]['removal_date'] 		= date( 'Y-m-d H:i:s' );
					$group_data[$key]['last_modified_by'] 	= $this->ion_auth->_current_user->id;
				}

				if( !empty( $group_data ) ){
					$this->db->update_batch( 'distribution_bundle_content', $group_data, 'bundle_content_id' );
				}

				if( $this->db->trans_status() !== false ){
					$result = $group_data;
					$this->session->set_flashdata( 'message', 'Bundle Content updated successfully.' );
				}

			} else if( !empty( $data['bundle_content_id'] ) ){

				$data = $this->ssid_common->_filter_data( 'distribution_bundle_content', $data );
				$data['latest_entry'] 		= 0;
				$data['last_modified_by'] 	= $this->ion_auth->_current_user->id;

				if( !empty( $data['content_in_use'] ) && ( $data['content_in_use'] == 1 ) ){
					$data['removal_date'] = NULL;
				}

				$this->db->where( 'distribution_bundle_content.bundle_content_id', $data['bundle_content_id'] )
					->where( 'distribution_bundle_content.distribution_bundle_id', $data['distribution_bundle_id'] )
					->update( 'distribution_bundle_content', $data );

					if( $this->db->trans_status() !== false ){
						$result = $this->get_distribution_bundle_content( $account_id, false, $data['bundle_content_id'] );
						$this->session->set_flashdata( 'message', 'Bundle Content updated successfully.' );
					}

			} else {
				$this->session->set_flashdata( 'message','Error! Missing required information.' );
			}

		} else {
			$this->session->set_flashdata( 'message','Error! Missing required information.' );
		}

		return $result;
	}


	/**
	* Get all Content for Auto-removal (oldest items first)
	*/
	public function get_auto_remove_content( $account_id = false, $distribution_group_id = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;
		if( !empty( $account_id ) ){

			$where = convert_to_array( $where );

			if( isset( $where['films_added'] ) ){
				if( !empty( $where['films_added'] ) ){
					$films_added = $where['films_added'];
				}
				unset( $where['films_added'] );
			}

			## Filter By Distribution Group
			if( ( isset( $where['distribution_group_id'] ) || !empty( $distribution_group_id ) ) && !empty( $films_added ) ){
				$distribution_group_id 	= !empty( $distribution_group_id ) ? $distribution_group_id : $where['distribution_group_id'];
				
				$this->db->select( 'dbc.provider_id, dbc.provider_name', false );
				$this->db->select( 'COUNT( provider_id ) `count_prov`', false );
				
				$this->db->where( 'dbc.account_id', $account_id );
				$this->db->where( 'dbc.distribution_group_id', $distribution_group_id );
				$this->db->where( 'dbc.content_in_use', 1 );
				$this->db->where_in( 'dbc.content_id', $films_added );
				
				$this->db->order_by( 'dbc.provider_name' );
				$this->db->group_by( 'dbc.provider_id ' );
				$providers = $this->db->get( 'distribution_bundle_content dbc' );

				if( $providers->num_rows() > 0 ){

					$data = $to_remove = $all = [];

					foreach( $providers->result() as $prow_key => $prow_row ){
						$provider_id = $prow_row->provider_id;
						$query = $this->db->select( 'dbc.*, distribution_bundles.distribution_bundle, content_film.*, content_clearance.clearance_start_date `clearance_date`, age_rating.age_rating_name, age_rating.age_rating_desc', false )
							->join( 'distribution_bundles', 'distribution_bundles.distribution_bundle_id = dbc.distribution_bundle_id', 'left' )
							->join( 'content_film', 'dbc.content_id = content_film.content_id', 'left' )
							->join( 'age_rating', 'age_rating.age_rating_id = content_film.age_rating_id', 'left' )
							->join( 'content_clearance', 'content_clearance.content_id = content_film.content_id', 'left' )
							->where( 'dbc.account_id', $account_id )
							->where( 'dbc.distribution_group_id', $distribution_group_id )
							->where( 'dbc.provider_id', $provider_id )
							->where( 'dbc.content_in_use', 1 )
							->order_by( 'dbc.license_start_date, dbc.bundle_content_id' )
							->group_by( 'dbc.content_id' )
							->get( 'distribution_bundle_content dbc' );

							foreach( $query->result() as $key => $row ){
								$row->film_attributes = $this->content_service->_fetch_content_attributes( $account_id, $row->content_id );
								$data[$provider_id][$key] = $row;
							}

						## Get total Films to remove
						$total_to_remove = !empty( $prow_row->count_prov ) ? $prow_row->count_prov : false;

						if( !empty( $provider_id ) && !$total_to_remove ){
							$remove_query = $this->db->where( 'dbc.account_id', $account_id )
								->where( 'dbc.distribution_group_id', $distribution_group_id )
								->where( 'dbc.provider_id', $provider_id )
								->where( 'dbc.latest_entry', 1 )
								->get( 'distribution_bundle_content dbc' );

							if( $remove_query->num_rows() > 0 ){
								$total_to_remove = $remove_query->num_rows();
							}
						}

						$oldest_films 	= !empty( $total_to_remove ) ? array_slice( $data[$row->provider_id], 0, $total_to_remove ) : false;

						$all 			= array_merge( $all, $data[$provider_id] );
						$provider_remove= !empty( $oldest_films ) ? array_column( $oldest_films, 'bundle_content_id' ) : [];
						$to_remove 		= array_merge( $to_remove, $provider_remove );

					}

					$result['all'] 		= $all;
					$result['to_remove']= $to_remove;
				}

			} else {
				$this->session->set_flashdata( 'message','Your request is missing required information' );
			}

		}else {
			$this->session->set_flashdata( 'message','Your request is missing required information' );
		}

		return $result;
	}

	/** Check Bundle Validity before Dispatch **/
	public function check_bundle_validity( $account_id = false, $distribution_bundle_id = false, $where = false ){
		$result = false;
		if( !empty( $account_id ) ){

			$where = convert_to_array( $where );
			$data  = [];

			if( !empty( $distribution_bundle_id ) ){

				$bundle_content = $this->get_distribution_bundle_content( $account_id, $distribution_bundle_id );

				if( !empty( $bundle_content ) ){

					foreach( $bundle_content as $key => $content ){

						$data_file 				= $this->content_service->generate_file_export( $account_id, $content->content_id, 'json' );
						$data_file 				= $this->content_service->generate_file_export( $account_id, $content->content_id, 'xml' );

						$movie_file_ready 		= false;
						$movie_trailer_ready 	= false;

						$hero_image_ready 		= false;
						$standard_image_ready 	= false;

						$movie_subtitles_ready 	= false;

						$movie_xml_file_ready 	= false;
						$movie_json_file_ready 	= false;

						$file_link_info			= false;

						if( !empty( $data_file['json_file_path'] ) && is_file( $data_file['json_file_path'] ) ){
							$file_link_info = $data_file['json_file_path'];
							$json_data 		= file_get_contents( $data_file['json_file_path'] );
							$ingestion_data = json_decode( $json_data, true );

						} else if( !empty( $data_file['xml_file_path'] ) && is_file( $data_file['xml_file_path'] ) ){
							$file_link_info = $data_file['xml_file_path'];
							$xml_data 		= file_get_contents( $data_file['xml_file_path'] );
							$xml_data		= simplexml_load_string( $xml_data, "SimpleXMLElement", LIBXML_NOCDATA );
							$json 			= json_encode( $xml_data );
							$ingestion_data = json_decode( $json,TRUE );
						}

						$data[$content->content_id] = [
							'content_id'			=> $content->content_id,
							'content_name'			=> $content->title,
							'content_asset_code'	=> $content->asset_code,
							'content_asset_check'	=> 'not-ready',
							'movie_assets'		=>[
								'movie'		=> false,
								'trailer'	=> false
							],
							'alt_movie_assets' 	=> [
								'movie'		=> false,
								'trailer'	=> false
							],
							'movie_images'			=> false,
							'movie_subtitles'		=> false,
							'movie_xml_file'		=> false,
							'movie_json_file'		=> false
						];

						## Check asset movie entry
						$asset_movie = false;
						$this->db->select( "cdf.file_id", false );

						$this->db->where( "cdf.content_id", $content->content_id );
						$where_arch = "( ( cdf.archived is NULL ) or ( cdf.archived != 1 ) )";
						$this->db->where( $where_arch );
						$this->db->where( "cdf.main_record", 1 );
						$this->db->where( "cdft.type_group", "movie" );

						$this->db->join( "content_decoded_file_type `cdft`", "cdft.type_id = cdf.decoded_file_type_id", "left" );

						$asset_movie = $this->db->get( "content_decoded_file `cdf`" )->row();

						if( !empty( $asset_movie ) ){
							$data[$content->content_id]['alt_movie_assets']['movie'] = $asset_movie;
						}

						## Check asset trailer entry
						$asset_trailer = false;
						$this->db->select( "cdf.file_id", false );

						$this->db->where( "cdf.content_id", $content->content_id );
						$where_arch = "( ( cdf.archived is NULL ) or ( cdf.archived != 1 ) )";
						$this->db->where( $where_arch );
						$this->db->where( "cdf.main_record", 1 );
						$this->db->where( "cdft.type_group", "trailer" );

						$this->db->join( "content_decoded_file_type `cdft`", "cdft.type_id = cdf.decoded_file_type_id", "left" );

						$asset_trailer = $this->db->get( "content_decoded_file `cdf`" )->row();

						if( !empty( $asset_trailer ) ){
							$data[$content->content_id]['alt_movie_assets']['trailer'] = $asset_trailer;
						}


						if( !empty( $ingestion_data['assets'] ) ){

							foreach( $ingestion_data['assets'] as $k => $assets ){
								if( is_array( $assets ) && !empty( $assets ) ){

									foreach( $assets as $ast => $asset ){

										if( !empty( $asset['@attributes'] ) ){
											$asset_record = !empty( $asset['@attributes'] ) ? $asset['@attributes'] : false;
											$file_class = $asset_record['class'];
											$file_name 	= $asset_record['name'];
											$file_type 	= in_array( $file_class, [ 'trailer' ] ) ? 'trailer' : 'movie';
											$data[$content->content_id]['movie_assets'][strtolower( $file_type )] = $file_name;

											if( in_array( $file_class, [ 'film', 'movie'] ) ){
												$movie_file_ready = true;
											}

											if( in_array( $file_class, [ 'trailer' ] ) ){
												$movie_trailer_ready = true;
											}
										} else {
											$asset_record 	= !empty( $asset['name'] ) ? $asset : false;
											if( !empty( $asset_record ) ){
												$file_class 	= $asset_record['class'];
												$file_name 		= $asset_record['name'];
												$file_type 		= in_array( $file_class, [ 'trailer' ] ) ? 'trailer' : 'movie';
												$data[$content->content_id]['movie_assets'][strtolower( $file_type )] = $file_name;

												if( in_array( $file_class, [ 'film', 'movie'] ) ){
													$movie_file_ready = true;
												}

												if( in_array( $file_class, [ 'trailer' ] ) ){
													$movie_trailer_ready = true;
												}
											}
										}
									}

								} else {
									$asset 	= $assets;
									if( !empty( $asset['@attributes'] ) ){
										$asset_record 	= !empty( $asset['@attributes'] ) ? $asset['@attributes'] : false;
										if( !empty( $asset_record ) ){
											$file_class 	= $asset_record['class'];
											$file_name 		= $asset_record['name'];
											$file_type 		= in_array( $file_class, [ 'trailer' ] ) ? 'trailer' : 'movie';
											$data[$content->content_id]['movie_assets'][strtolower( $file_type )] = $file_name;

											if( in_array( $file_class, [ 'film', 'movie'] ) ){
												$movie_file_ready = true;
											}

											if( in_array( $file_class, [ 'trailer' ] ) ){
												$movie_trailer_ready = true;
											}
										}
									} else {
										$asset_record 	= !empty( $asset['name'] ) ? $asset : false;
										if( !empty( $asset_record ) ){
											$file_class 	= $asset_record['class'];
											$file_name 		= $asset_record['name'];
											$file_type 		= in_array( $file_class, [ 'trailer' ] ) ? 'trailer' : 'movie';
											$data[$content->content_id]['movie_assets'][strtolower( $file_type )] = $file_name;

											if( in_array( $file_class, [ 'film', 'movie'] ) ){
												$movie_file_ready = true;
											}

											if( in_array( $file_class, [ 'trailer' ] ) ){
												$movie_trailer_ready = true;
											}
										}
									}
								}
							}

						}

						if( !empty( $ingestion_data['images'] ) ){

							foreach( $ingestion_data['images'] as $k => $images ){
								if( is_array( $images ) ){
									foreach( $images as $img => $image ){
										$img_text 	= explode( '-', strrev( $image ), 2 );
										$img_type 	= explode( '.', strrev( $img_text[0] ) );
										$img_type 	= !empty( $img_type[0] ) ? $img_type[0] : 'unknown';
										$data[$content->content_id]['movie_images'][strtolower( $img_type )] = $image;
										if( in_array( $img_type, [ 'hero', 'full'] ) ){
											$hero_image_ready = true;
										}

										if( in_array( $img_type, [ 'standard', 'thumbnail'] ) ){
											$standard_image_ready = true;
										}
									}
								} else {
									$image 		= $images;
									$img_text 	= explode( '-', strrev( $image ), 2 );
									$img_type 	= explode( '.', strrev( $img_text[0] ) );
									$img_type 	= !empty( $img_type[0] ) ? $img_type[0] : 'unknown';
									$data[$content->content_id]['movie_images'][strtolower( $img_type )] = $image;
									if( in_array( $img_type, [ 'hero', 'full'] ) ){
										$hero_image_ready = true;
									}

									if( in_array( $img_type, [ 'standard', 'thumbnail'] ) ){
										$standard_image_ready = true;
									}
								}
							}
						}

						if( !empty( $ingestion_data['subtitles'] ) ){
							if( is_array( $ingestion_data['subtitles'] ) ){
								foreach( $ingestion_data['subtitles'] as $sub => $subtitles ){
									if( is_array( $subtitles ) ){
										foreach( $subtitles as $sub_key => $sub_title ){
											$data[$content->content_id]['movie_subtitles'][] = $sub_title;
										}
									} else {
										$data[$content->content_id]['movie_subtitles'][] = $subtitles;
									}
								}
							} else {
								$data[$content->content_id]['movie_subtitles'][] = $ingestion_data['subtitles'];
							}
							$movie_subtitles_ready = true;
						}

						## Json and XML files
						if( !empty( $file_link_info ) ){
							$pathinfo 	= pathinfo( $file_link_info );
							$xml_file 	= $pathinfo['dirname'].'/'.$pathinfo['filename'].'.xml';
							$json_file 	= $pathinfo['dirname'].'/'.$pathinfo['filename'].'.json';

							if( is_file( $xml_file ) ){
								$files_to_send[] 		= $xml_file;
								$data[$content->content_id]['movie_xml_file'] = $xml_file;
								$movie_xml_file_ready 	= true;
							}

							if( is_file( $json_file ) ){
								$files_to_send[] 		= $json_file;
								$data[$content->content_id]['movie_json_file'] = $json_file;
								$movie_json_file_ready 	= true;
							}
						}

						## Overall check
						if( $movie_file_ready && $movie_trailer_ready && $hero_image_ready && $standard_image_ready && $movie_subtitles_ready ) {
							$data[$content->content_id]['content_asset_check'] = 'ready';
						}

					}

				}
			}

			$result = $data;

		}

		return $result;
	}


	/**
	*	Send Distribution Bundle for CDS to pick up
	*	Currently: send the distribution bundle via Coggins
	**/
	public function send_distribution_bundle( $account_id = false, $distribution_group_id = false, $distribution_bundle_id = false, $postdata = false ){

		$result 			= [];
		$result['status'] 	= false;
		$result['data'] 	= false;
		$result['message'] 	= false;

		if( !empty( $account_id ) && !empty( $distribution_group_id )  && !empty( $distribution_bundle_id ) && !empty( $postdata ) ){

			if( isset( $postdata['checked_content'] ) ){
				$checked_content = isset( $postdata['checked_content'] ) ? $postdata['checked_content'] : [];
				$checked_content = ( is_json( $checked_content ) ) ? json_decode( $checked_content ) : $checked_content;
				unset( $postdata['checked_content'] );
			}

			## Distribution Details
			$distro_details	= $this->db->select( 'dg.*, delivery_nodes.setting_value `delivery_node`, delivery_nodes.value_desc `delivery_desc`, ds.server_name, ds.server_id, ds.coggins_id `server_coggins_id`', false )
				->join( 'setting `delivery_nodes`', 'delivery_nodes.setting_id = dg.delivery_point_id', 'left' )
				->join( 'distribution_server `ds`', 'ds.server_id = dg.delivery_point_id', 'left' )
				->where( 'dg.account_id', $account_id )
				->where( 'dg.distribution_group_id', $distribution_group_id )
				->get( 'distribution_groups dg' )
				->row();

			$delivery_emails = false;

			if( !empty( $distro_details->server_coggins_id ) ){
			## Delivery emails/ a list of notification points

				$this->db->select( "dsnp.email, dsnp.contact_full_name", false );
				$this->db->where( "dsnp.active", 1 );
				$this->db->where( "dsnp.archived", 0 );
				$this->db->where( "dsnp.server_id", $distro_details->server_id );

				$delivery_emails = $this->db->get( "distribution_server_notification_point `dsnp`" )->result();
			}

			## Bundle Details
			$bundle_details	= $this->db->select( 'distribution_bundles.*', false )
				->where( 'distribution_bundles.account_id', $account_id )
				->where( 'distribution_bundles.distribution_bundle_id', $distribution_bundle_id )
				->get( 'distribution_bundles' )
				->row();

			if( !empty( $checked_content ) && !empty( $distro_details ) && !empty( $bundle_details ) && !empty( $distro_details->server_coggins_id ) ){

				$data 						= [];
				$movie_titles				= [];
				$sent_movies	= [
					'sent-successfully' 	=>[],
					'failed-to-send' 		=>[]
				];

				## figuring out the correct schedule date/time for Coggins with using the UTC timestamp
				## Coggins API parameters - scheduled:
				$scheduled 		= false;

				## The code below has been left for the future debugging (2022-04-04)
				## This is an other way to calculate the Epoch / UTC / Zulu timestamp for the bundles - it doesn't seem to be correct due to strtotime and date output
				## Basically strtotime from $bundle_details->schedule_date_time is giving the correct timestamp
				## - this may not be true when DST will take place. Not a clue how to overcome this issue.
				## If ever needed - check the output for each step and compare with desired result, include DST time in your logic.

				if( !empty( $bundle_details->schedule_date_time ) ){

					date_default_timezone_set( 'Europe/London' );

					$now_gmdate 		= false;
					$now_gmdate 		= ( gmdate( "Y-m-d H:i:s" ) );
					$now_datetime 		= date( "Y-m-d H:i:s" );

					$x 					= ( $now_gmdate == $now_datetime ) ? 0 : 3600; 	## DST - Winter and Summer time

					$now_time 			= false;
					$now_time 			= time();

					$event_time 		= false;
					$event_time 		= strtotime( $bundle_details->schedule_date_time );

					$diff1 				= $event_time - $now_time;
					$scheduled			= ( strtotime( $now_gmdate ) + $diff1 + $x ) * 1000 ;

					// $difference 		= 0;
					// $difference 		= $now_time - strtotime( $now_gmdate );

					// $scheduled		= ( $event_time + $difference ) * 1000;
				}

				## Coggins API parameters - priority:
				$priority					= "low";  ## ["high", "low"]

				## RESET THE MAIN DATA CONTAINER
				$whole_bundle_data			= [];

				## GET THE SERVER id
				$server_ids					= [( int ) $distro_details->server_coggins_id];

				## start the loop through the confirmed content (checked)
				foreach( $checked_content as $k => $content_id ){

					$ready 					= false;
					$movie_file_ready 		= false;
					$trailer_file_ready 	= false;
					$hero_image_ready 		= false;
					$standard_image_ready 	= false;
					$movie_subtitles_ready 	= false;
					$movie_xml_file_ready 	= false;
					$movie_json_file_ready 	= false;

					$files_to_send				= [];
					$movie_files_to_send		= [];

					##Check Content Exists
					$content_details	= false;
					$content_details 	= $this->db->select( 'content_film.content_id, title, asset_code, content_provider.provider_name, content_provider.provider_reference_code' )
						->join( 'content', 'content.content_id = content_film.content_id', 'left' )
						->join( 'content_provider', 'content_provider.provider_id = content.content_provider_id', 'left' )
						->where( '( content_film.active = 1 OR content_film.active IS NULL )' )
						->get_where( 'content_film', [ 'content_film.account_id'=>$account_id, 'content_film.content_id'=>$content_id ] )
						->row();

					if( !empty( $content_details->title ) ){

						## Get Movie Assets
						#$assets = $this->content_service->_fetch_movie_assets( $account_id, $content_id );
						$assets = $this->_get_movie_assets( $account_id, $content_id );

						if( !empty( $assets['movie'] ) ){
							$movie_file 		= $assets['movie']->file_url;
							$short_movie_file	= $assets['movie']->file_new_name;

							## The check if the file exists has been suspended for now as Techlive has got files in AWS bucket (12/2021)
							// if( is_file( $movie_file ) ){
								// $movie_files_to_send[] 	= $movie_file;
								$movie_files_to_send[] 	= $short_movie_file;
								$movie_file_ready 		= true;
							// }
						}

						## Temporarily not preventing the bundle to be sent if the trailer is missing (Adult content has no trailer) (05/2022)
						$trailer_file_ready 	= true;
						if( !empty( $assets['trailer'] ) ){
							$trailer_file 		= $assets['trailer']->file_url;
							$short_trailer_file = $assets['trailer']->file_new_name;

							## The check if the file exists has been suspended for now as Techlive has got files in AWS bucket (12/2021)
							// if( is_file( $trailer_file ) ){
								// $movie_files_to_send[] 	= $trailer_file;
								$movie_files_to_send[] 	= $short_trailer_file;
								$trailer_file_ready 	= true;
							// }
						}

						## Get Movie Images
						$images = $this->content_service->_fetch_movie_images( $account_id, $content_id );

						if( !empty( $images['hero'] ) ){
							$hero_image 	= $this->app_root.$images['hero']->image_path;//Local path
							$hero_image_ref = $images['hero']->image_ref;	//Just name
							if( is_file( $hero_image ) ){
								$files_to_send[] 	= $hero_image_ref;
								$hero_image_ready 	= true;
							}

						}

						if( !empty( $images['standard'] ) ){
							$standard_image 	= $this->app_root.$images['standard']->image_path;//Local path
							$standard_image_ref = $images['standard']->image_ref;//Local path
							if( is_file( $standard_image ) ){
								$files_to_send[] 		= $standard_image_ref;
								$standard_image_ready 	= true;
							}
						}

						$data[$content_id]['images'] = $images;

						## Get Movie Subtitles
						$subtitles = $this->content_service->_fetch_movie_subtitles( $account_id, $content_id );
						$data[$content_id]['subtitles'] = $subtitles;

						if( !empty( $subtitles ) ){
							foreach( $subtitles as $f => $subtitle ){
								$subtitle_file 		= $this->app_root.$subtitle->file_path;
								$subtitle_file_ref 	= $subtitle->file_ref;
								if( is_file( $subtitle_file ) ){
									$files_to_send[] 		= $subtitle_file_ref;
									$standard_image_ready 	= true;
								}
							}
						}

						## Get JSON & XML Files
						$movie_xml_file  = $this->app_root.'_account_assets/accounts/'.$account_id.'/content/'.$content_id.'/'.$content_details->asset_code.'.xml';
						if( is_file( $movie_xml_file ) ){
							// $files_to_send[] 		= $movie_xml_file;
							$files_to_send[] 		= $content_details->asset_code.'.xml';
							$movie_xml_file_ready 	= true;
						}

						$movie_json_file = $this->app_root.'_account_assets/accounts/'.$account_id.'/content/'.$content_id.'/'.$content_details->asset_code.'.json';
						if( is_file( $movie_json_file ) ){
							// $files_to_send[] 		= $movie_json_file;
							$files_to_send[] 		= $content_details->asset_code.'.json';
							$movie_json_file_ready 	= true;
						}

						## If everything is good, send the files - previously 'copy'
						if(
							( !empty( $files_to_send ) && !empty( $movie_files_to_send ) ) &&
							( $movie_file_ready && $trailer_file_ready && $hero_image_ready && $standard_image_ready ) &&
							( !empty( $content_details->content_id ) && !empty( $content_details->provider_name ) && !empty( $content_details->asset_code ) )
						){

							$delivery_node	= !empty( $bundle_details->cds_folder_name ) ? $bundle_details->cds_folder_name : strtolower( strip_all_whitespace( $distro_details->delivery_node ).'_'.date( 'YmdHm' ) );
							$delivery_desc	= $distro_details->delivery_desc;

							if( !empty( $delivery_desc ) ){
								$delivery_node_data = explode( '-', $delivery_desc );
								if( !empty( $delivery_node_data[0] ) ){
									// $delivery_emails = explode( ',', $delivery_node_data[0] );
									// $delivery_emails = array_map( 'trim', $delivery_emails );
								}
							}

							$bundle_info['film_name'] 				= $content_details->asset_code;
							$bundle_info['provider_name']			= $content_details->provider_name;
							$bundle_info['delivery_node']			= $delivery_node;

							## preparing the set for a single movie

							$films_data 							= [];
							// $films_data["id"] 					= $content_details->content_id; // The most recent changes for Coggins (12/2021)
							$films_data["assetId"] 					= ( int ) $content_details->content_id;
							$films_data["provider"] 				= strtolower( $content_details->provider_reference_code );
							// $films_data["provider"]["directory"] 	= COGGINS_ADD_QUEUE_PATH.( strtolower( $content_details->provider_reference_code ) );
							$films_data["assetcode"] 				= $content_details->asset_code;
							$films_data["cactiID"] 					= $distribution_bundle_id;

							$films_data["metadata"]					= [];
							foreach( $files_to_send as $file ){
								$films_data["metadata"][] 			= $file;
							}

							$films_data["assets"]					= [];
							foreach( $movie_files_to_send as $mfile ){
								$films_data["assets"][] 			= $mfile;
							}

							## adding data set into the big container
							$whole_bundle_data[] 					= $films_data;
							$movie_titles[]							= $content_details->title;
						}
					}
				} ## end of loop of the checked files

				if( !empty( $whole_bundle_data ) && !empty( $server_ids ) ){

					$queue_added = false;
					$queue_added = $this->coggins_service->add_to_queue( $account_id, $whole_bundle_data, $server_ids, $scheduled, $priority );

					if( isset( $queue_added->success ) && ( $queue_added->success == true ) ){
						$dataset = [
							"account_id" 				=> $account_id,
							"distribution_group_id" 	=> $distribution_group_id,
							"distribution_bundle_id" 	=> $distribution_bundle_id,
							"coggins_output" 			=> json_encode( $queue_added ),
							"server_ids" 				=> ( !empty( $queue_added->data->server ) ) ? ( is_array( $queue_added->data->server ) ? json_encode( $queue_added->data->server ) : $queue_added->data->server->id ): json_encode( $server_ids ) ,
							"coggins__id" 				=> ( !empty( $queue_added->data->_id ) ) ? $queue_added->data->_id : '' ,
							"coggins_queueid" 			=> ( !empty( $queue_added->data->queueId ) ) ? $queue_added->data->queueId : '' ,
							// "coggins_uid" 				=> ( !empty( $queue_added->data->uid ) ) ? $queue_added->data->uid : '' ,
							"coggins_state" 			=> ( !empty( $queue_added->data->state ) ) ? $queue_added->data->state : '' ,
						];

						$this->db->insert( "coggins_distributed_content", $dataset );

						$bundle_dataset = [
							"coggins__id" 				=> ( !empty( $queue_added->data->_id ) ) ? $queue_added->data->_id : '' ,
							"coggins_queueid" 			=> ( !empty( $queue_added->data->queueId ) ) ? $queue_added->data->queueId : '' ,
							// "coggins_uid" 				=> ( !empty( $queue_added->data->uid ) ) ? $queue_added->data->uid : '' ,
							"coggins_state" 			=> ( !empty( $queue_added->data->state ) ) ? $queue_added->data->state : '' ,
							"coggins_name" 				=> ( !empty( $queue_added->data->content->name ) ) ? $queue_added->data->content->name : '' ,
							"send_status"				=> ( !empty( $queue_added->data->state ) ) ? map_coggins_status( $queue_added->data->state ) : '' ,
							"send_status_timestamp"		=> date( 'y-m-d H:i:s' ),
						];

						$this->db->update( "distribution_bundles", $bundle_dataset, ["distribution_bundle_id" => $distribution_bundle_id] );

						if( $this->db->affected_rows() > 0 ){
							$result['data'] 	= $this->get_distribution_bundles( $account_id, $distribution_group_id, $distribution_bundle_id );
							$result['status'] 	= 1;
							$message 			= ( isset( $queue_added->message ) && !empty( $queue_added->message ) ) ? html_escape( $queue_added->message ) : 'Bundle successfully sent' ;
							$result['message'] 	= $message;
							$this->session->set_flashdata( 'message', $message );

							## Send email confirmation
							if( !empty( $delivery_emails ) ){
								foreach( $delivery_emails as $email ){
									$bundle_data['salutation'] 			= 'Dear Customer,';
									$bundle_data['introduction'] 		= 'The following content has been scheduled for download by CDS to node <i>'.( ( !empty( $distro_details->server_name ) ) ? $distro_details->server_name : '' ).'</i> in batch <i>'.( ( !empty( $bundle_dataset['coggins_name'] ) ) ? $bundle_dataset['coggins_name'] : '' ).'</i>.';
									// $bundle_data['bundle_ref']			= ( !empty( $bundle_dataset['coggins_name'] ) ) ? $bundle_dataset['coggins_name'] : '' ;
									$bundle_data['films_introduction']	= 'Here are the films included:';
									$bundle_data['movie_titles'] 		= $movie_titles;
									$bundle_data['ending'] 				= 'This is an auto-generated email, please do not reply to it.<br />Kind regards';

									$subject 							= "Distribution of content to ".( ( !empty( $distro_details->distribution_group ) ) ? $distro_details->distribution_group : '' )." has started";
									$email_body 						= $this->load->view( 'email_templates/distribution/bundle_sent_confirmation', $bundle_data, true );

									$email_data = [
										'to'		=> $email->email,
										'from'		=> ['notifications@techlive.tv','Cacti CMS'],
										// 'bcc'		=> ['wojciechcupa@evidentsoftware.co.uk'],
										// 'subject'	=> 'New Bundle',
										'subject'	=> $subject,
										'message'	=> $email_body
									];

									$mail_result = $this->mail->send_mail( $email_data );
								}
							} else {
								## No delivery points
							}
						} else {
							## Update unsuccessful
						}

					} else {
						$dataset = [
							"account_id" 				=> $account_id,
							"distribution_group_id" 	=> $distribution_group_id,
							"distribution_bundle_id" 	=> $distribution_bundle_id,
							"coggins_output" 			=> json_encode( $queue_added ),
							"server_ids" 				=> ( !empty( $queue_added->data->server ) ) ? ( is_array( $queue_added->data->server ) ? json_encode( $queue_added->data->server ) : $queue_added->data->server->id ): json_encode( $server_ids ) ,
							"coggins__id" 				=> ( !empty( $queue_added->data->_id ) ) ? $queue_added->data->_id : '' ,
							"coggins_queueid" 			=> ( !empty( $queue_added->data->queueId ) ) ? $queue_added->data->queueId : '' ,
							// "coggins_uid" 				=> ( !empty( $queue_added->data->uid ) ) ? $queue_added->data->uid : '' ,
							"coggins_state" 			=> ( !empty( $queue_added->data->state ) ) ? $queue_added->data->state : '' ,
						];

						$this->db->insert( "coggins_distributed_content", $dataset );

						$coggins_message 	= ( isset( $queue_added->message ) && !empty( $queue_added->message ) ) ? html_escape( $queue_added->message ) : 'Something went wrong on Coggins' ;
						$result['message'] 	= $coggins_message;
						$this->session->set_flashdata( 'message', $coggins_message );
					}
				} else {
					$this->session->set_flashdata( 'message','Missing files, Error processing the data or missing Server ID' );
				}

			} else {
				$this->session->set_flashdata( 'message','Some or all of the required Movie assets are not ready!' );
			}

		} else {
			$this->session->set_flashdata( 'message','Error! Missing required information.' );
		}
		return $result;
	}

	/**
	* Copy Files to a CDs Pickup location
	*/
	private function _copy_files_to_cds_pickup_location( $account_id = false, $symlink_files = false, $file_url = false, $bundle_info = false ){

		$result = false;

		if( !empty( $account_id ) && !empty( $symlink_files ) && !empty( $file_url ) && !empty( $bundle_info ) ){

			$data 						= [];
			$delivery_node				= !empty( $bundle_info['delivery_node'] ) 	? $bundle_info['delivery_node'] : '';
			$delivery_node_location		= CDS_DESTINATION_LOCATION.$delivery_node.'/';

			if( !is_dir( $delivery_node_location ) ){
				if( !mkdir( $delivery_node_location, 0755, true ) ){
					$this->session->set_flashdata( 'message', 'Error: Unable to create CDS Delivery Node Pickup folder for'.$destination_folder_name );
					return false;
				}
			}

			## Establish Source Folder
			foreach( $symlink_files as $key => $asset_file ){
				$path_parts = pathinfo( $asset_file );
				break;
			}

			$source_location 	= !empty( $path_parts['dirname'] ) ? $path_parts['dirname'].'/' : false;;

			$film_name					= !empty( $bundle_info['film_name'] ) 	? $bundle_info['film_name'].'/' 	: '';
			$cds_final_pickup_location 	= $delivery_node_location.$film_name;//Destination location

			if( !is_dir( $cds_final_pickup_location ) ){
				$junction_cmd 		=  'mklink /J "'.$cds_final_pickup_location.'" "'.$source_location.'"';
				## start CMD processing
				ob_start();
					exec( $junction_cmd );
				ob_end_clean();
			}

			if( is_array( $file_url ) ){

				foreach( $file_url as $file ){

					$path_parts = pathinfo( $file );
					$file_name  = $path_parts['filename'].'.'.$path_parts['extension'];
					#$newfile 	= $cds_final_pickup_location.$file_name;
					$newfile 	= $source_location.$file_name;

					try {

						if( !copy( $file, $newfile ) ){
							$data['failed-to-send'][] = [
								'file_name'=> $file_name,
								'file_path'=> $newfile
							];
							$this->session->set_flashdata( 'message', 'Files junction-linked successfully' );
						} else {
							$data['sent-successfully'][] = [
								'file_name'=> $file_name,
								'file_path'=> $newfile
							];
						}

					} catch ( Exception $e ){
						$this->session->set_flashdata( 'exception', 'Exception - '.$e->getMessage() );
					}
				}

			} else {
				$file 		= $file_url;
				$path_parts = pathinfo( $file );
				$file_name  = $path_parts['filename'].'.'.$path_parts['extension'];
				#$newfile 	= $cds_final_pickup_location.$file_name;
				$newfile 	= $source_location.$file_name;

				try {

					if( !copy( $file, $newfile ) ){
						$data['failed-to-send'][] = [
							'file_name'=> $file_name,
							'file_path'=> $newfile
						];
					} else {
						$data['sent-successfully'][] = [
							'file_name'=> $file_name,
							'file_path'=> $newfile
						];
					}

					$this->session->set_flashdata( 'message', 'Files junction-linked successfully' );
				} catch ( Exception $e ){
					$this->session->set_flashdata( 'exception', 'Exception - '.$e->getMessage() );
				}
			}
			$result = $data;
		}

		return $result;
	}


	/**
	* Create Symlinks to the CDs Pickup location
	*/
	private function _create_symlinks_to_cds_pickup_location( $account_id = false, $file_url = false, $bundle_info = false ){

		$result = false;

		if( !empty( $account_id ) && !empty( $file_url ) && !empty( $bundle_info ) ){

			$data 						= [];

			$delivery_node				= !empty( $bundle_info['delivery_node'] ) 	? $bundle_info['delivery_node'] : '';
			$delivery_node_location		= CDS_DESTINATION_LOCATION.$delivery_node.'/';

			if( !is_dir( $delivery_node_location ) ){
				if( !mkdir( $delivery_node_location, 0755, true ) ){
					$this->session->set_flashdata( 'message', 'Error: Unable to create CDS Delivery Node Pickup folder for'.$destination_folder_name );
					return false;
				}
			}

			$film_name					= !empty( $bundle_info['film_name'] ) 	? $bundle_info['film_name'].'/' 	: '';
			$cds_final_pickup_location 	= $delivery_node_location.$film_name;

			if( !is_dir( $cds_final_pickup_location ) ){
				if( !mkdir( $cds_final_pickup_location, 0755, true ) ){
					$this->session->set_flashdata( 'message', 'Error: Unable to create CDS Pickup folder for'.$destination_folder_name );
					return false;
				}
			}

			if( is_array( $file_url ) ){
				foreach( $file_url as $file ){

					$path_parts = pathinfo( $file );
					$file_name  = $path_parts['filename'].'.'.$path_parts['extension'];
					$newfile 	= $cds_final_pickup_location.$file_name;

					try {

						if( !is_link( $newfile ) ){
							if( !symlink( $file, $newfile ) ){
								$data['failed-to-send'][] = [
									'file_name'=> $file_name,
									'file_path'=> $newfile
								];
								$this->session->set_flashdata( 'message', 'Symlinks created successfully' );
							} else {
								$data['sent-successfully'][] = [
									'file_name'=> $file_name,
									'file_path'=> $newfile
								];
							}
						} else {
							## Already exists, treat as success.
							$data['sent-successfully'][] = [
								'file_name'=> $file_name,
								'file_path'=> $newfile
							];
							$this->session->set_flashdata( 'message', 'Symlinks created successfully' );
						}

					} catch ( Exception $e ){
						$this->session->set_flashdata( 'exception', 'Exception - '.$e->getMessage() );
					}

				}

			} else {
				$file 		= $file_url;
				$path_parts = pathinfo( $file );
				$file_name  = $path_parts['filename'].'.'.$path_parts['extension'];
				$newfile 	= $cds_final_pickup_location.$file_name;

				try {

					if( !is_link( $newfile ) ){
						if( !symlink( $file, $newfile ) ){
							$data['failed-to-send'][] = [
								'file_name'=> $file_name,
								'file_path'=> $newfile
							];
						} else {
							$data['sent-successfully'][] = [
								'file_name'=> $file_name,
								'file_path'=> $newfile
							];
						}
					} else {
						## Already exists, treat as success.
						$data['sent-successfully'][] = [
							'file_name'=> $file_name,
							'file_path'=> $newfile
						];
					}
					$this->session->set_flashdata( 'message', 'Symlinks created successfully' );
				} catch ( Exception $e ){
					$this->session->set_flashdata( 'exception', 'Exception - '.$e->getMessage() );
				}
			}
			$result = $data;
		}

		return $result;
	}

	/** Get Movie Assets **/
	private function _get_movie_assets( $account_id = false, $content_id = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $content_id ) ){
			$query = $this->db->select( 'cp.provider_name, cp.provider_reference_code, cf.asset_code, cf.title, cdft.type_name,  cdft.type_group, cfcd.definition_name, cdf.*', false )
				->join( 'content_decoded_file_type cdft', 'cdf.decoded_file_type_id = cdft.type_id', 'left' )
				->join( 'content_format_codec_definition cfcd', 'cdf.file_definition_id = cfcd.definition_id', 'left' )
				->join( 'content_film cf', 'cf.content_id = cdf.content_id', 'left' )
				->join( 'content', 'content.content_id = cdf.content_id', 'left' )
				->join( 'content_provider cp', 'cp.provider_id = content.content_provider_id', 'left' )
				->where( 'cdf.account_id', $account_id )
				->where( 'cdf.content_id', $content_id )
				->where( '( cdf.archived != 1 OR cdf.archived is NULL )' )
				->where( 'cdf.main_record', 1 )
				->get_where( 'content_decoded_file cdf' );

			if( $query->num_rows() > 0 ){
				$data = [];
				foreach( $query->result() as $k => $row ){

					$file_type 	= ( strtolower( $row->type_group ) == 'trailer' ) ? 'trailer' : 'movie';
					$file_name	= $row->file_new_name;

					// $cds_pickup_location = CDS_PICKUP_LOCATION . strtolower( $row->provider_reference_code ).'/'. strtolower( $row->asset_code ).'/';
					$cds_pickup_location = CDS_PICKUP_LOCATION .'/'. strtolower( $row->provider_reference_code ).'/'. strtolower( $row->asset_code ).'/';

					// if( !is_dir( $cds_pickup_location ) ){
						// $this->session->set_flashdata( 'message', 'CDS Pickup location'.$cds_pickup_location );
						// return false;
					// }

					$file_url	= $cds_pickup_location.$file_name;

					// if( is_file( $file_url ) ){
						if( $file_type == 'movie' ){
							$data['movie'] = (object)[
								'file_new_name'		=> $row->file_new_name,
								'file_name'			=> $row->file_new_name,
								'file_path'			=> $cds_pickup_location,
								'file_url'			=> $file_url
							];
						} else if( $file_type == 'trailer' ) {
							$data['trailer'] = (object)[
								'file_new_name'		=> $row->file_new_name,
								'file_name'	 		=> $row->file_new_name,
								'file_path'	 		=> $cds_pickup_location,
								'file_url'	 		=> $file_url
							];
						}
					// } else {
						$this->session->set_flashdata( 'message', 'Some files were not found' );
						$data['files-not-found'][] = $file_url;
					// }
				}
				$result = $data;
			}
		}
		return $result;
	}


	public function get_distribution_servers( $account_id = false, $where = false ){
		$result = false;
		if( !empty( $account_id ) ){

			$check_running = false;

			if( !empty( $where ) ){
				$where = convert_to_array( $where );
				if( !empty( $where ) ){
					if( !empty( $where['coggins_id'] ) ){
						$coggins_id = $where['coggins_id'];
						$this->db->where( "distribution_server.coggins_id", $coggins_id );
						unset( $where['coggins_id'] );
					}

					if( !empty( $where['server_id'] ) ){
						$server_id = $where['server_id'];
						$this->db->where( "distribution_server.server_id", $server_id );
						unset( $where['server_id'] );
					}

					if( !empty( $where['check_running'] ) ){
						$check_running = $where['check_running'];
						unset( $where['check_running'] );
					}

					if( !empty( $where ) ){
						$this->db->where( $where );
					}
				}
			}

			$this->db->select( "distribution_server.*", false );
			$this->db->select( "dsnp.email, dsnp.contact_full_name, dsnp.point_id, dsnp.archived `dsnp_archived`", false );

			$this->db->join( "distribution_server_notification_point `dsnp`", "dsnp.server_id = distribution_server.server_id", "left" );
			$this->db->where( "distribution_server.archived !=", 1 );
			$this->db->where( "distribution_server.account_id", $account_id );
			$query = $this->db->get( "distribution_server" );

			if( $query->num_rows() > 0 ){

				foreach( $query->result() as $row ){
					$email 				= false;
					if( isset( $row->dsnp_archived ) && ( $row->dsnp_archived != 1 ) ){
						$email 				= $row->email;
					}
					unset( $row->email );

					$contact_full_name 	= false;
					if( isset( $row->dsnp_archived ) && ( $row->dsnp_archived != 1 ) ){
						$contact_full_name 	= $row->contact_full_name;
					}
					unset( $row->contact_full_name );

					$point_id 			= false;
					if( isset( $row->dsnp_archived ) && ( $row->dsnp_archived != 1 ) ){
						$point_id 			= $row->point_id;
					}
					unset( $row->point_id );

					if( !isset( $result[$row->server_id] ) || empty( $result[$row->server_id] ) ){

						## if required, check the 'running' value for each of the server
						if( isset( $check_running ) && ( strtolower( $check_running ) == "yes" ) ){

							// $row->coggins_userId - this is just for now. Once the Server ID implementation will be ready - this should go
							if( !empty( $row->coggins_id ) && !empty( $row->coggins_userId ) ){

								$where['filter']['field'] = "userid";
								$where['filter']['value'] = [( int ) $row->coggins_userId];

								## not implemented yet but the RIGH ONE
								// $where['filter']['field'] = "serverid";
								// $where['filter']['value'] = $row->coggins_id;

								// get this particular server update from Coggins
								$server = $this->coggins_service->get_servers( $account_id, $where );

								if( !empty( $server->data ) ){
									## we should update each server now
									$server_data = false;
									$server_data = $server->data[0];

									if( !empty( $server_data ) ){

										$this->update_distribution_server( $account_id, $row->server_id, $server_data );

										// we get the newest data from Coggins, we've updated the server in the DB, now we need to update the current $row value
										$row->coggins_status 			= $server_data->server->status;
										$row->coggins_running 			= $server_data->server->running;
										$row->coggins_lastPollSeconds 	= $server_data->server->lastPollSeconds;
										$row->coggins_time 				= $server_data->server->time;
										$row->coggins_units 			= $server_data->server->units;
									}
								}
							}
						}


						## insert a new row for a new server into the results array of object
						$result[$row->server_id] = ( array ) $row;
					}

					if( !empty( $email ) || !empty( $contact_full_name ) ){
						$result[$row->server_id]['notification_points'][] = [
							'email' 			=> ( !empty( $email ) ) ? $email : '' ,
							'contact_full_name' => ( !empty( $contact_full_name ) ) ? $contact_full_name : '' ,
							'point_id' 			=> ( !empty( $point_id ) ) ? ( int ) $point_id : '' ,
						];
					} else {
						if( !isset( $result[$row->server_id]['notification_points'] ) ){
							$result[$row->server_id]['notification_points'] = [];
						}
					}
				}

				if( !empty( $server_id ) ){
					$result = $result[$row->server_id];
				}


				$this->session->set_flashdata( 'message','Record(s) found' );
			} else{
				$this->session->set_flashdata( 'message','Record(s) not found' );
			}
		} else {
			$this->session->set_flashdata( 'message','Error! Missing required information' );
		}
		return $result;
	}


	/**
	*	To get a list of available servers from Coggins
	*/
	public function get_available_servers( $account_id = false, $where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET ){
		$result = false;
		if( !empty( $account_id ) ){

			$servers = ( object )[];
			$servers = $this->coggins_service->get_servers( $account_id, $where );

			if( $servers->success && !empty( $servers->data ) ){
				$msg 	= ( ( !empty( $servers->message ) ) ? $servers->message : 'Record(s) found' );
				$result = $servers->data;
			} else {
				$msg 	= ( ( !empty( $servers->message ) ) ? $servers->message : 'Record(s) not found' );
			}
			$this->session->set_flashdata( 'message', $msg );
		} else {
			$this->session->set_flashdata( 'message','Error! Missing required information' );
		}

		return $result;
	}


	/**
	*	Add server data from Coggins and create a profile in CaCTi
	*/
	public function add_distribution_server( $account_id = false, $coggins_server_id = false, $notification_points = false, $server_data = false, $server_description = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $coggins_server_id ) && !empty( $server_data ) ){

			$server_data = json_decode( $server_data );

			if( !empty( $server_data ) ){
				$insert_data = [
					"account_id"				=> $account_id,
					"server_name"				=> ( !empty( $server_data->server->name ) ) ? $server_data->server->name : '' ,
					"coggins_id"				=> ( !empty( $server_data->server->id ) ) ? $server_data->server->id : '' ,
					"coggins_type"				=> ( !empty( $server_data->server->type ) ) ? $server_data->server->type : '' ,
					"coggins_licence"			=> ( !empty( $server_data->server->licence ) ) ? $server_data->server->licence : '' ,
					"coggins_status"			=> ( !empty( $server_data->server->status ) ) ? $server_data->server->status : '' ,
					"coggins_created"			=> ( !empty( $server_data->server->created ) ) ? $server_data->server->created : '' ,
					"coggins_unlocked"			=> ( !empty( $server_data->server->unlocked ) ) ? $server_data->server->unlocked : '' ,
					"coggins_externalAccess"	=> ( !empty( $server_data->server->externalAccess ) ) ? $server_data->server->externalAccess : '' ,
					"coggins_running"			=> ( !empty( $server_data->server->running ) ) ? $server_data->server->running : '' ,
					"coggins_lastPollSeconds"	=> ( !empty( $server_data->server->lastPollSeconds ) ) ? $server_data->server->lastPollSeconds : '' ,
					"coggins_time"				=> ( !empty( $server_data->server->time ) ) ? $server_data->server->time : '' ,
					"coggins_units"				=> ( !empty( $server_data->server->units ) ) ? $server_data->server->units : '' ,
					"last_refreshed"			=> date( 'Y-m-d H:i:s' ),
					"description"				=> ( !empty( $server_description ) ) ? $server_description : '' ,
					"created_by"				=> $this->ion_auth->_current_user->id,

					## temp added - until the filter for the server id is sorted out - or it may stay forever
					"coggins_companyId"			=> ( !empty( $server_data->company->id ) ) ? $server_data->company->id : '' ,
					"coggins_companyName"		=> ( !empty( $server_data->company->name ) ) ? $server_data->company->name : '' ,
					"coggins_userId"			=> ( !empty( $server_data->user->id ) ) ? $server_data->user->id : '' ,
					"coggins_userName"			=> ( !empty( $server_data->user->name ) ) ? $server_data->user->name : '' ,

				];

				if( !empty( $insert_data ) ){
					$query = $this->db->insert( "distribution_server", $insert_data );

					if( $this->db->affected_rows() > 0 ){

						$server_insert_id = false;
						$server_insert_id = $this->db->insert_id();

						if( !empty( $server_insert_id ) ){
							$result = $this->db->get_where( "distribution_server", ["server_id"=>$server_insert_id] )->result();

							if( !empty( $notification_points ) ){
								$notification_points = $this->add_notification_point( $account_id, $server_insert_id, $notification_points );
							}
							$this->session->set_flashdata( 'message','Server added successfully' );

						} else{
							$this->session->set_flashdata( 'message','Incorrect Insert ID' );
						}

					} else {
						$this->session->set_flashdata( 'message','Error inserting the data' );
					}

				} else {
					$this->session->set_flashdata( 'message','Error processing the data' );
				}

			} else {
				$this->session->set_flashdata( 'message','Error processing the server data' );
				return $result;
			}

		} else {
			$this->session->set_flashdata( 'message','Error! Missing required data' );
		}

		return $result;
	}


	/*
	*	This is to add a notification(s) point onto the server.
	*/
	public function add_notification_point( $account_id = false, $server_id = false, $notification_points = false ){
		$result = false;

		if( !empty( $account_id ) && !empty( $server_id ) && !empty( $notification_points ) ){

			$notification_points = convert_to_array( $notification_points );

			if( !empty( $notification_points ) ){
				$batch_data 	= $emails = [];

				foreach( $notification_points as $np_row ){
					$batch_data[] 	= [
						"account_id" 		=> $account_id,
						"server_id" 		=> $server_id,
						"email" 			=> ( !empty( $np_row->value ) ) ? $np_row->value : '' ,
						"contact_full_name" => ( !empty( $np_row->name ) ) ? $np_row->name : '' ,
						"created_by"		=> $this->ion_auth->_current_user->id,
					];

					if( !empty( $np_row->value ) ){
						$emails[] = $np_row->value;
					}
				}

				if( !empty( $batch_data ) ){
					$this->db->insert_batch( 'distribution_server_notification_point', $batch_data );

					if( $this->db->trans_status() !== FALSE ){
						$this->db->where_in( 'email', $emails );
						$result = $this->db->get( 'distribution_server_notification_point' )->result();
						$this->session->set_flashdata( 'message', 'Notifications successfully added' );
					} else {
						$this->session->set_flashdata( 'message','Error saving data' );
					}
				} else {
					$this->session->set_flashdata( 'message','No data to insert' );
				}
			} else {
				$this->session->set_flashdata( 'message','Error processing notifications data' );
				return $result;
			}
		} else {
			$this->session->set_flashdata( 'message','Error! Missing required data' );
		}
		return $result;
	}


	/**
	*	Update the CaCTi version of the server with the current data
	*/
	public function update_distribution_server( $account_id = false, $server_id = false, $server_data = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $server_id ) && !empty( $server_data ) ){

			// updating only 'fragile' data
			$upd_data = [
				"coggins_type"				=> ( !empty( $server_data->server->type ) ) ? $server_data->server->type : '' ,
				"coggins_status"			=> ( !empty( $server_data->server->status ) ) ? $server_data->server->status : '' ,
				"coggins_unlocked"			=> ( !empty( $server_data->server->unlocked ) ) ? $server_data->server->unlocked : '' ,
				"coggins_externalAccess"	=> ( !empty( $server_data->server->externalAccess ) ) ? $server_data->server->externalAccess : '' ,
				"coggins_running"			=> ( !empty( $server_data->server->running ) ) ? $server_data->server->running : '' ,
				"coggins_lastPollSeconds"	=> ( !empty( $server_data->server->lastPollSeconds ) ) ? $server_data->server->lastPollSeconds : '' ,
				"coggins_time"				=> ( !empty( $server_data->server->time ) ) ? $server_data->server->time : '' ,
				"coggins_units"				=> ( !empty( $server_data->server->units ) ) ? $server_data->server->units : '' ,
				"last_refreshed"			=> date( 'Y-m-d H:i:s' ) ,
				"modified_by"				=> $this->ion_auth->_current_user->id,
			];

			$query = $this->db->update( "distribution_server", $upd_data, ["server_id" => $server_id] );

			if( $this->db->affected_rows() > 0 ){
				$result = $this->db->get_where( "distribution_server", ["server_id"=>$server_id] );
				$this->session->set_flashdata( 'message','Server data has been successfully updated' );
			} else {
				$this->session->set_flashdata( 'message','No server data changed' );
			}
		} else {
			$this->session->set_flashdata( 'message','Error! Missing required data' );
		}
		return $result;
	}


	public function cancel_distribution_bundle( $account_id = false, $distribution_bundle_id = false ){
		$result = false;

		if( !empty( $account_id ) && !empty( $distribution_bundle_id ) ){
			// to 'cancel' the bundle will b only used if the bundle has been already submitted to Coggins
			// - so, it means that we should always have the Coggins ID under this budle.
			// Lets take the details,
			// check if we do have the basic data against the bundle
			// So far, for what we know, the Coggins queueDelete call, which we're going to use for it, expects UID parameter:
			$bundle_details = $this->db->get_where( "distribution_bundles", ["account_id" => $account_id, "distribution_bundle_id" => $distribution_bundle_id ] )->row();

			if( !empty( $bundle_details->coggins_uid ) || !empty( $bundle_details->coggins_queueid ) ){
				$queue_id = false;
				$queue_id = !empty( $bundle_details->coggins_uid ) ? $bundle_details->coggins_uid : $bundle_details->coggins_queueid ;

				// check which state the bundle is now
				// if is running we should use the cancel call

				$cancel_bundle = false;
				if( in_array( strtolower( $bundle_details->send_status ), ['sending'] ) ){
					$cancel_bundle = $this->coggins_service->queue_cancel( $account_id, $queue_id );

					if( !empty( $cancel_bundle->success ) && ( $cancel_bundle->success == true ) ){
						// update bundle on CaCTi
						$upd_data = [
							"send_status" 			=> "sent",
							"coggins_state" 		=> "finished",
							"last_modified_by"		=> $this->ion_auth->_current_user->id,
							"send_status_timestamp"	=> date( 'Y-m-d H:i:s' ),
						];
						$this->db->update( "distribution_bundles", $upd_data, ["distribution_bundle_id" => $distribution_bundle_id] );

						if( $this->db->affected_rows() > 0 ){
							$result = $this->db->get_where( "distribution_bundles", ["account_id" => $account_id, "distribution_bundle_id" => $distribution_bundle_id ] )->row();
							$this->session->set_flashdata( 'message','Distribution has been canceled from the queue' );
						} else {
							$this->session->set_flashdata( 'message','Distribution has been canceled from the queue, but CaCTi is not updated' );
						}
					} else {
						$message = ( !empty( $cancel_bundle->message ) ) ? $cancel_bundle->message : 'Error processing the bundle on Coggins' ;
						$this->session->set_flashdata( 'message', $message );
					}

				} else {
					// we do have the UID, now just need to run the Coggins request and grab the status

					$cancel_bundle = $this->coggins_service->queue_delete( $account_id, $queue_id );

					if( !empty( $cancel_bundle->success ) && ( $cancel_bundle->success == true ) ){
						// update bundle on CaCTi
						$upd_data = [
							"send_status" 			=> "cancelled",
							"coggins_state" 		=> "deleted",
							"last_modified_by"		=> $this->ion_auth->_current_user->id,
							"send_status_timestamp"	=> date( 'Y-m-d H:i:s' ),
						];
						$this->db->update( "distribution_bundles", $upd_data, ["distribution_bundle_id" => $distribution_bundle_id] );

						if( $this->db->affected_rows() > 0 ){
							$result = $this->db->get_where( "distribution_bundles", ["account_id" => $account_id, "distribution_bundle_id" => $distribution_bundle_id ] )->row();
							$this->session->set_flashdata( 'message','Bundle has been deleted from the queue' );

						} else {
							$this->session->set_flashdata( 'message','Bundle has been deleted from the queue, but CaCTi is not updated' );
						}
					} else {
						$message = ( !empty( $cancel_bundle->message ) ) ? $cancel_bundle->message : 'Error processing the bundle on Coggins' ;
						$this->session->set_flashdata( 'message', $message );
					}
				}


			} else {
				$this->session->set_flashdata( 'message','Missing UID of the bundle' );
			}
		}

		return $result;
	}


	public function get_notification_point( $account_id = false, $where = false ){
		$result = false;
		if( !empty( $account_id ) ){

			if( !empty( $where ) ){
				$where = convert_to_array( $where );
				if( !empty( $where ) ){
					if( !empty( $where['server_id'] ) ){
						$server_id = $where['server_id'];
						$this->db->where( "dsnp.server_id", $server_id );
						unset( $where['server_id'] );
					}

					if( !empty( $where['point_id'] ) ){
						$point_id = $where['point_id'];
						$this->db->where( "dsnp.point_id", $point_id );
						unset( $where['point_id'] );
					}

					if( !empty( $where['email'] ) ){
						$email = $where['email'];
						$this->db->where( "dsnp.email", $email );
						unset( $where['email'] );
					}

					if( !empty( $where ) ){
						$this->db->where( $where );
					}
				}
			}

			$this->db->select( "dsnp.*", false );

			$this->db->where( "dsnp.archived !=", 1 );
			$this->db->where( "dsnp.account_id", $account_id );
			$query = $this->db->get( "distribution_server_notification_point `dsnp`" );

			if( $query->num_rows() > 0 ){

				$result = $query->result();

				$this->session->set_flashdata( 'message','Record(s) found' );
			} else {
				$this->session->set_flashdata( 'message','Record(s) not found' );
			}
		} else {
			$this->session->set_flashdata( 'message','Error! Missing required information' );
		}
		return $result;
	}



	/**
	*	Delete server Notification point (email)
	**/
	public function delete_notification_point( $account_id = false, $point_id = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $point_id ) ){
			$conditions 	= ['account_id'=>$account_id, 'point_id'=>$point_id];
			$point_exists 	= $this->db->get_where( 'distribution_server_notification_point', $conditions )->row();

			if( !empty( $point_exists ) ){

				$this->db->where( $conditions );
				$this->db->delete( 'distribution_server_notification_point' );
				// $this->ssid_common->_reset_auto_increment( 'distribution_bundles', 'distribution_bundle_id' );

				if( $this->db->trans_status() !== FALSE ){
					$this->session->set_flashdata( 'message', 'Server Notification Point deleted successfully' );
					$result = true;
				}
			} else {
				$this->session->set_flashdata( 'message','Invalid Notification Point ID' );
			}

		} else {
			$this->session->set_flashdata( 'message','Missing required data' );
		}
		return $result;
	}



	/**
	*	Add single Notification point to the server (email)
	**/
	public function add_single_notification_point( $account_id = false, $postdata = false ){
		$result = false;
		if( !empty( $account_id ) && !empty( $postdata['server_id'] ) && !empty( $postdata['email'] ) ){

			$point_exists 	= false;

			$conditions 	= ['account_id' => $account_id, 'server_id' => ( int ) $postdata['server_id'], 'email' => $postdata['email'] ];
			$point_exists 	= $this->db->get_where( 'distribution_server_notification_point', $conditions )->row();

			if( !empty( $point_exists ) ){
				$this->session->set_flashdata( 'message','Notification Point already exists' );
				return $result;
			} else {
				$point_data = [];
				$point_data = [
					"account_id" 		=> ( int ) $account_id,
					"server_id" 		=> ( int ) $postdata['server_id'],
					"email"				=> $postdata['email'],
					"contact_full_name"	=> ( !empty( $postdata['contact_full_name'] ) ) ? $postdata['contact_full_name'] : '' ,
					"created_by"		=> $this->ion_auth->_current_user->id,
				];

				$query = $this->db->insert( 'distribution_server_notification_point', $point_data );

				if( $this->db->trans_status() !== FALSE ){
					$insert_id 	= $this->db->insert_id();
					$result 	= $this->get_notification_point( $account_id, ["point_id" => $insert_id ] );
					$this->session->set_flashdata( 'message', 'Server Notification Point added successfully' );
				} else {
					$this->session->set_flashdata( 'message', 'There was an issue adding he server notification point' );
				}
			}

		} else {
			$this->session->set_flashdata( 'message','Missing required data' );
		}
		return $result;
	}


	/**
	*	Update server description (only) and Notification point(s)
	**/
	public function update_server( $account_id = false, $server_id = false, $postdata = false ){
		$result = false;

		if( !empty( $account_id ) && !empty( $server_id ) && !empty( $postdata ) ){


			if( !empty( $postdata['description'] ) || !empty( $postdata['email'] ) ){

				if( !empty( $postdata['description'] ) ){
					$server_upd_data = [];
					$server_upd_data = [
						"description" 	=> html_escape( trim( $postdata['description'] ) ),
						"modified_by"	=> $this->ion_auth->_current_user->id
					];

					$server_where = [];
					$server_where = [
						"account_id" 	=> $account_id,
						"server_id"		=> $server_id
					];

					$this->db->update( "distribution_server", $server_upd_data, $server_where );

					if( $this->db->trans_status() !== FALSE ){

						$message = 'The process has started';

						if( !empty( $postdata['email'] ) ){
							$emails = false;
							$emails = get_object_vars( json_decode( $postdata['email'] ) );

							if( !empty( $emails ) && is_array( $emails ) ){
								$batch_update_data = [];
								foreach( $emails as $point_id => $email ){
									$batch_update_data[]=[
										"point_id"	=> $point_id,
										"email" 	=> $email,
									];
								}

								if( !empty( $batch_update_data ) ){
									$query = $this->db->update_batch( 'distribution_server_notification_point', $batch_update_data, 'point_id' );
									$message = 'The server profile and points have been updated successfully';
								} else {
									$message = 'The server profile has been updated successfully';
								}
							} else {
								$message = 'Error processing the notification points';
							}
						} else {
							$message = 'The server profile has been updated successfully';
						}

						$result = $this->get_distribution_servers( $account_id, ["server_id" => $server_id ] );
						$this->session->set_flashdata( 'message', $message );

					} else {
						$this->session->set_flashdata( 'message', 'There was an issue updating the server profile' );
					}
				}
			} else {
				$this->session->set_flashdata( 'message','No update data provided' );
			}
		} else {
			$this->session->set_flashdata( 'message','Missing required data' );
		}
		return $result;
	}
	
	
	/**
	*	Archive the server entry and delete all notification point(s)
	**/
	public function delete_server( $account_id = false, $server_id = false ){
		$result = false;

		if( !empty( $account_id ) && !empty( $server_id ) ){
			
			$datetime = strtotime( "now" );
			
			$this->db->where( "server_id", $server_id );
			$this->db->where( "account_id", $account_id );
			$this->db->set( "server_name", "CONCAT( 'Archived_', `server_name`, '_', $datetime )", false );
			$this->db->set( "modified_by", $this->ion_auth->_current_user->id );
			$this->db->set( "archived", 1 );
			$this->db->set( "is_active", 0 );
			$this->db->update( "distribution_server" );

			if( $this->db->affected_rows() > 0 ){

				$result 	= true;
				
				$this->db->where( 'server_id', $server_id );
				$this->db->delete( 'distribution_server_notification_point' );
				
				if( $this->db->trans_status() !== false ){
					$message 	= 'The server profile and notification points have been deleted successfully';
				} else {
					$message 	= 'The server profile has been deleted successfully';
				}

				$this->session->set_flashdata( 'message', $message );
			} else {
				$this->session->set_flashdata( 'message','The server profile hasn\'t been deleted' );
			}
		} else {
			$this->session->set_flashdata( 'message','Missing required data' );
		}
		return $result;
	}
}