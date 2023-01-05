<?php

namespace App\Models\Service;

use App\Adapter\Model;

class StatisticsModel extends Model
{
	/**
	 * @var \Application\Modules\Service\Models\SiteModel $site_service
	 */
	private $site_service;

	/**
	 * @var \Application\Modules\Service\Models\JobModel $job_service
	 */
	private $job_service;

	public function __construct()
    {
        parent::__construct();

        $this->site_service = new SiteModel();
        $this->job_service = new JobModel();
    }

    /*
    * Get Jobs statistics
    */
    public function get_job_stats($account_id = false, $stat_type = false, $where = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($stat_type)) {
            $where = convert_to_array($where);

            switch(strtolower($stat_type)) {
                case 'maintenance_calls':

                    $category_icons		= category_icons();

                    $first_day_of_month = date('Y-m-01');
                    $last_day_of_month 	= date('Y-m-t');

                    $category  			= $this->db->select('category_id, category_name, category_name_alt, category_group, description')->order_by('category_name')->get_where('audit_categories', [ 'account_id'=>$account_id, 'is_active'=>1 ]);
                    $category_data		= [];
                    $job_stats 			= [];

                    if ($category->num_rows() > 0) {
                        foreach ($category->result() as $k => $row) {
                            if (!empty($site_id)) {
                                if (is_array($site_id)) {
                                    $this->db->where_in('asset.site_id', $site_id);
                                } else {
                                    $this->db->where('asset.site_id', $site_id);
                                }
                            }

                            $category_assets = $this->db->select('asset_id', false)
                                ->join('asset_types', 'asset_types.asset_type_id = asset.asset_type_id', 'left')
                                ->where('asset_types.category_id', $row->category_id)
                                ->where('asset.account_id', $account_id)
                                ->where('asset.archived !=', 1)
                                ->order_by('asset_types.asset_type, asset.asset_id')
                                ->get('asset');

                            if ($category_assets->num_rows() > 0) {
                                if (!empty($where['monthly'])) {
                                    $date_from = date('Y-m-01');
                                    $date_to 	= date('Y-m-t');
                                }

                                if (!empty($where['annual'])) {
                                    $date_from	= date('Y-m-d', strtotime('Jan 01'));
                                    $date_to	= date('Y-m-d', strtotime('Dec 31'));
                                }

                                if (!empty($date_from) && !empty($date_to)) {
                                    $this->db->where('job.job_date >= "'.$date_from.'" ');
                                    $this->db->where('job.job_date <= "'.$date_to.'" ');
                                }

                                $asset_ids = array_column($category_assets->result_array(), 'asset_id');

                                ## Limited Statuses
                                $this->db->select('SUM(CASE WHEN ( status_group = "assigned" OR status_group = "enroute" OR status_group = "onsite" ) THEN 1 ELSE 0 END) AS `assigned`,
									SUM( CASE WHEN status_group = "inprogress" THEN 1 ELSE 0 END) AS `inprogress`,
									SUM( CASE WHEN ( status_group = "cancelled" OR status_group = "failed" ) THEN 1 ELSE 0 END) AS `failed`,
									SUM( CASE WHEN status_group = "successful" THEN 1 ELSE 0 END) AS `successful`,
									SUM( CASE WHEN ( status_group = "unassigned" OR job.status_id = "" OR job.status_id IS NULL ) THEN 1 ELSE 0 END) AS `unassigned`,
									SUM( CASE WHEN job_id > 0 THEN 1 ELSE 0 END) AS `total_jobs`', false);

                                ## All Statuses
                                /*$this->db->select( 'SUM(CASE WHEN status_group = "assigned" THEN 1 ELSE 0 END) AS `assigned`,
                                    SUM( CASE WHEN status_group = "enroute"  THEN 1 ELSE 0 END ) AS `enroute`,
                                    SUM( CASE WHEN status_group = "onsite"  THEN 1 ELSE 0 END ) AS `onsite`,
                                    SUM( CASE WHEN status_group = "inprogress" THEN 1 ELSE 0 END) AS `inprogress`,
                                    SUM( CASE WHEN ( status_group = "cancelled" OR status_group = "failed" ) THEN 1 ELSE 0 END) AS `failed`,
                                    SUM( CASE WHEN status_group = "successful" THEN 1 ELSE 0 END) AS `successful`,
                                    SUM( CASE WHEN ( status_group = "unassigned" OR job.status_id = "" OR job.status_id IS NULL ) THEN 1 ELSE 0 END) AS `unassigned`,
                                    SUM( CASE WHEN job_id > 0 THEN 1 ELSE 0 END) AS `total_jobs`', false ) */

                                $this->db->where('job.account_id', $account_id)
                                ->order_by('job_statuses.job_status')
                                ->order_by('job_statuses.job_status');

                                $jobs = $this->db->join('job_statuses', 'job_statuses.status_id = job.status_id', 'left')
                                    ->where_in('job.asset_id', $asset_ids)
                                    ->get('job');

                                if ($jobs->num_rows() > 0) {
                                    foreach ($jobs->result()[0] as $status_group => $group_total) {
                                        $job_stats[strtolower($row->category_group)]['category_id'] 		= $row->category_id;
                                        $job_stats[strtolower($row->category_group)]['category_totals'] 	= ($status_group == 'total_jobs') ? (!empty($group_total) ? ( string )$group_total : '0') : '0';

                                        $status_details = $this->job_service->get_job_statuses($account_id, false, false, $status_group)[0];
                                        if ($status_group != 'total_jobs') {
                                            $job_stats[strtolower($row->category_group)]['category_group'] 	= $row->category_group;
                                            $job_stats[strtolower($row->category_group)]['category_name'] 	= $row->category_name;
                                            $job_stats[strtolower($row->category_group)]['job_stats'][$status_group] 		= [
                                                'status_id'		=> !empty($status_details->status_id) ? $status_details->status_id : '',
                                                'status_name'	=> !empty($status_details->job_status) ? $status_details->job_status : '',
                                                'status_group'	=> $status_group,
                                                'status_total'	=> !empty($group_total) ? ( string )$group_total : '0',
                                                'status_color'	=> !empty($status_details->status_colour_hex) ? $status_details->status_colour_hex : '',
                                                'status_desc'	=> !empty($status_details->job_status_desc) ? $status_details->job_status_desc : '',
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $result = $job_stats;

                    break;
            }
        }

        if (!empty($result)) {
            $this->session->set_flashdata('message', 'Job stats found');
            $result;
        } else {
            $this->session->set_flashdata('message', 'Job stats not available');
        }
        return $result;
    }



    /*
    * Get Asset statistics
    */
    public function get_asset_stats($account_id=false, $stat_type = false, $period_days = '30', $where = false, $date_from=false, $date_to = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($stat_type)) {
            $where = convert_to_array($where);

            $contract_id	= !empty($where['contract_id']) ? $where['contract_id'] : false;

            if (!empty($contract_id)) {
                $sites = $this->db->select('site_id', false)
                    ->where('sites_contracts.contract_id', $contract_id)
                    ->where('sites_contracts.account_id', $account_id)
                    ->where('sites_contracts.contract_id', $contract_id)
                    ->get('sites_contracts');

                if (!empty($sites->num_rows() > 0)) {
                    $site_id = array_column($sites->result(), 'site_id');
                }
            }

            $site_id	= !empty($site_id) ? $site_id : (!empty($where['site_id']) ? $where['site_id'] : false);

            switch(strtolower($stat_type)) {
                //Compliance status
                case 'compliance':
                case 'outcome_result':
                case 'audit_results':

                    $stats_data = [];
                    $result_statuses  = $this->db->select('audit_result_statuses.*')
                        ->order_by('audit_result_statuses.result_ordering')
                        ->group_by('audit_result_statuses.audit_result_status_id')
                        ->get_where('audit_result_statuses', [ 'account_id'=>$account_id ]);

                    if ($result_statuses->num_rows() > 0) {
                        foreach ($result_statuses->result() as $k => $row) {
                            if (strtolower($row->result_status_group)  == 'not_set') {
                                $group_not_set = true;
                            }

                            $audit_result_status_id = $row->audit_result_status_id;

                            if (!empty($group_not_set)) {
                                $this->db->select('SUM( CASE WHEN ( asset.audit_result_status_id = 0 OR asset.audit_result_status_id IS NULL ) THEN 1 ELSE 0 END ) AS status_not_set', false);
                            } else {
                                $this->db->select('SUM( CASE WHEN asset.audit_result_status_id = "'.$audit_result_status_id.'" THEN 1 ELSE 0 END ) `status_total`', false);
                            }

                            if (!empty($site_id)) {
                                if (is_array($site_id)) {
                                    $this->db->where_in('asset.site_id', $site_id);
                                } else {
                                    $this->db->where('asset.site_id', $site_id);
                                }
                            }

                            $evidoc_statuses = $this->db->where('asset.account_id', $account_id)
                                ->where('asset.archived !=', 1)
                                ->order_by('asset.asset_id')
                                ->get('asset');

                            if ($evidoc_statuses->num_rows() > 0) {
                                $total_records = !empty($evidoc_statuses->result()[0]->status_total) ? $evidoc_statuses->result()[0]->status_total : (!empty($evidoc_statuses->result()[0]->status_not_set) ? $evidoc_statuses->result()[0]->status_not_set : 0);
                                $stats_data[] = array_merge((array) $row, [ 'status_total'=>( string )$total_records ]);
                            }
                        }
                    }

                    if (!empty($stats_data)) {
                        $data = [];
                        $grand_total 				= (string) array_sum(array_column($stats_data, 'status_total'));//Get the grand total
                        $stats_arr 					= array_combine(array_map('strtolower', array_column($stats_data, 'result_status_group')), array_column($stats_data, 'status_total'));
                        $data['stats']				= $stats_data;
                        $data['totals'] 			= (!empty($stats_arr) && !empty($grand_total)) ? array_merge(['grand_total'=>$grand_total], $stats_arr) : [];
                        $data['dates'] 				= [
                            'date_from'=>date('d/m/Y', strtotime($date_from)),
                            'date_to'=>date('d/m/Y', strtotime($date_to)),
                            'period_days'=> ( string ) floor((strtotime($date_to) - strtotime($date_from)) / 86000)
                        ];

                        #Calculate Compliance using what passed + recommendations
                        if (!empty($data['totals']['grand_total']) && (!empty($data['totals']['passed']) && ($data['totals']['passed'] > 0))) {
                            $data['totals']['compliance'] 	  = (number_format(((($data['totals']['passed'] + (!empty($data['totals']['recommendations'] ? $data['totals']['recommendations'] : 0))) / $data['totals']['grand_total']) * 100), 2) + 0).'%';
                            $data['totals']['compliance_raw'] =  ( string ) (number_format(((($data['totals']['passed'] + (!empty($data['totals']['recommendations'] ? $data['totals']['recommendations'] : 0))) / $data['totals']['grand_total']) * 100), 4) + 0);
                            $data['totals']['compliance_alt'] = 'Compliant';
                        }

                        # Calculate compliance based on what failed
                        if (!empty($data['totals']['grand_total']) && (!empty($data['totals']['failed']) && ($data['totals']['failed'] > 0))) {
                            $data['totals']['compliance'] 	  = (number_format(((($data['totals']['failed']) / $data['totals']['grand_total']) * 100), 2) + 0).'%';
                            $data['totals']['compliance_raw'] = ( string ) (number_format(((($data['totals']['failed']) / $data['totals']['grand_total']) * 100), 2) + 0);
                            $data['totals']['compliance_alt'] = 'Not Compliant';
                        }

                        $stats_data = $data;
                        $num_rows 	= true;
                    }

                    break;

                case 'total_number':

                    $stats_data = [];
                    #$cat_query  = $this->db->select( 'category_id, category_name, category_name_alt, category_group, description' )->get_where( 'audit_categories', [ 'account_id'=>$account_id ] );
                    $this->db->where('( asset_types.asset_group NOT IN ( "comm device","plant" ) )');
                    $asset_types = $this->db->select('asset_type_id, asset_type, asset_group, asset_type `category_name_alt`')->get_where('asset_types', [ 'account_id'=>$account_id ]);
                    #if( $cat_query->num_rows() > 0 ){
                    if ($asset_types->num_rows() > 0) {
                        #foreach( $cat_query->result() as $k => $row ){
                        foreach ($asset_types->result() as $k => $row) {
                            if (!empty($site_id)) {
                                if (is_array($site_id)) {
                                    $this->db->where_in('asset.site_id', $site_id);
                                } else {
                                    $this->db->where('asset.site_id', $site_id);
                                }
                            }

                            $asset_type_id 	= $row->asset_type_id;
                            $asset_type 	= $row->asset_type;
                            $asset_types_asset = $this->db->select('SUM( CASE WHEN asset_types.asset_type_id = "'.$asset_type_id.'" THEN 1 ELSE 0 END ) `total_assets`', false)
                                ->join('asset_types', 'asset_types.asset_type_id = asset.asset_type_id', 'left')
                                ->where('asset.account_id', $account_id)
                                ->where('( asset_types.asset_group NOT IN ( "comm device","plant" ) )')
                                ->where('asset.archived !=', 1)
                                ->order_by('asset_types.asset_type, asset.asset_id')
                                ->get('asset');

                            if ($asset_types_asset->num_rows() > 0) {
                                # Only include Categories with at least 1 asset attached to it
                                if ($asset_types_asset->result()[0]->total_assets > 0) {
                                    $stats_data[] = array_merge((array) $row, [ 'total_assets'=>$asset_types_asset->result()[0]->total_assets ]);
                                }
                            }
                        }

                        if (!empty($stats_data)) {
                            $grand_total 					= ( string ) array_sum(array_column($stats_data, 'total_assets'));
                            $stats_arr 						= array_combine(array_map('strtolower', array_map('trim', array_column($stats_data, 'asset_type'))), array_column($stats_data, 'total_assets'));
                            $data['stats']					= $stats_data;
                            $data['totals'] 				= (!empty($stats_arr) && !empty($grand_total)) ? array_merge(['grand_total'=>$grand_total], $stats_arr) : [];

                            $stats_data = $data;
                        }

                        $num_rows = (!empty($stats_data)) ? $stats_data : false;
                    }

                    #$num_rows = ( $asset_data->num_rows() > 0 ) ? $asset_data->num_rows() : false;
                    break;

                case 'assets_by_category':

                    $stats_data = [];
                    $cat_query  = $this->db->select('category_id, category_name, category_name_alt, category_group, description')->get_where('audit_categories', [ 'account_id'=>$account_id ]);
                    $this->db->where('( asset_types.asset_group NOT IN ( "comm device","plant" ) )');
                    $asset_types = $this->db->select('asset_type_id, asset_type, asset_group, asset_type `category_name_alt`')->get_where('asset_types', [ 'account_id'=>$account_id ]);
                    if ($cat_query->num_rows() > 0) {
                        foreach ($cat_query->result() as $k => $row) {
                            if (!empty($site_id)) {
                                if (is_array($site_id)) {
                                    $this->db->where_in('asset.site_id', $site_id);
                                } else {
                                    $this->db->where('asset.site_id', $site_id);
                                }
                            }

                            $category_id 	= $row->category_id;
                            $category_name 	= $row->category_name;
                            $asset_types_asset = $this->db->select('SUM( CASE WHEN asset_types.category_id = "'.$category_id.'" THEN 1 ELSE 0 END ) `total_assets`', false)
                                ->join('asset_types', 'asset_types.asset_type_id = asset.asset_type_id', 'left')
                                ->where('asset.account_id', $account_id)
                                ->where('( asset_types.asset_group NOT IN ( "comm device","plant" ) )')
                                ->where('asset.archived !=', 1)
                                ->order_by('asset_types.asset_type, asset.asset_id')
                                ->get('asset');

                            if ($asset_types_asset->num_rows() > 0) {
                                # Only include Categories with at least 1 asset attached to it
                                if ($asset_types_asset->result()[0]->total_assets > 0) {
                                    $stats_data[] = array_merge((array) $row, [ 'total_assets'=>$asset_types_asset->result()[0]->total_assets ]);
                                }
                            }
                        }

                        if (!empty($stats_data)) {
                            $grand_total 				= ( string ) array_sum(array_column($stats_data, 'total_assets'));
                            $stats_arr 					= array_combine(array_map('strtolower', array_map('trim', array_column($stats_data, 'category_name'))), array_column($stats_data, 'total_assets'));
                            $data['stats']				= $stats_data;
                            $data['totals'] 			= (!empty($stats_arr) && !empty($grand_total)) ? array_merge(['grand_total'=>$grand_total], $stats_arr) : [];

                            $stats_data = $data;
                        }

                        $num_rows = (!empty($stats_data)) ? $stats_data : false;
                    }
                    break;

                case 'asset_status':
                    $asset_data = $this->db->select('asset_statuses.*, COUNT( asset.asset_id ) status_total', false)
                        ->join('asset', 'asset_statuses.status_id = asset.status_id')
                        ->where('asset_statuses.account_id', $account_id)
                        ->where('asset_statuses.is_active', 1)
                        ->order_by('status_group')
                        ->group_by('asset_statuses.status_id')
                        ->get('asset_statuses');
                    $num_rows = ($asset_data->num_rows() > 0) ? $asset_data->num_rows() : false;
                    break;

                case 'eol':

                    $eol_statuses = $this->get_eol_statuses($account_id);

                    $sql_select = '';

                    if (!empty($eol_statuses)) {
                        foreach ($eol_statuses as $k => $eol_group) {
                            $group_min = (!empty($eol_group->eol_group_min)) ? $eol_group->eol_group_min : 0;
                            $group_max = (!empty($eol_group->eol_group_max)) ? $eol_group->eol_group_max : 0;

                            if (strtolower($eol_group->eol_group) == 'eol_expired') {
                                $sql_select .= 'SUM( CASE WHEN DATEDIFF( asset.end_of_life_date, CURDATE() ) < 0 THEN 1 ELSE 0 END ) `eol_expired`, ';
                            } elseif (strtolower($eol_group->eol_group) == 'eol_not_set') {
                                $sql_select .= 'SUM( CASE WHEN ( ( asset.end_of_life_date IS NULL ) OR ( asset.end_of_life_date = "0000-00-00" ) ) THEN 1 ELSE 0 END ) `eol_not_set`, ';
                            } else {
                                $sql_select .= 'SUM( CASE WHEN ( ( DATEDIFF( asset.end_of_life_date, CURDATE() ) > '.$group_min.' ) AND ( DATEDIFF( asset.end_of_life_date, CURDATE() ) <= '.$group_max.' ) ) THEN 1 ELSE 0 END ) `'.$eol_group->eol_group.'`, ';
                            }
                        }

                        $asset_data = $this->db->select($sql_select, false)
                            ->where('asset.account_id', $account_id)
                            ->where('asset.archived !=', 1)
                            ->get('asset');
                        $num_rows = ($asset_data->num_rows() > 0) ? $asset_data->num_rows() : false;
                        //Prepare for bar-graph
                    }
                    break;

                    /* case 'replace_cost':

                        ## Select expired assets firs then a list based on the date supplied date range eg. 30 / 60 / 90 / 180 / 365
                        $where = ' ( ( DATEDIFF( a.end_of_life_date, CURDATE() ) < 0 ) OR ( ( DATEDIFF( a.end_of_life_date, CURDATE() ) > 0 ) AND ( DATEDIFF( a.end_of_life_date, CURDATE() ) <= '.$period_days.' ) ) ) ';

                        $asset_data = $this->db->select('
                                SUM( CASE WHEN DATEDIFF( a.end_of_life_date, CURDATE() ) < 0 THEN 1 ELSE 0 END ) `expired`,
                                SUM( CASE WHEN ( ( DATEDIFF( a.end_of_life_date, CURDATE() ) < 0 ) AND ( a.purchase_price > 0 ) ) THEN a.purchase_price ELSE 0 END ) `expired_cost`,
                                SUM( CASE WHEN ( ( DATEDIFF( a.end_of_life_date, CURDATE() ) > 0 ) AND ( DATEDIFF( a.end_of_life_date, CURDATE() ) <= '.$period_days.' ) ) THEN 1 ELSE 0 END ) `due_to_expire`,
                                SUM( CASE WHEN ( ( DATEDIFF( a.end_of_life_date, CURDATE() ) > 0 ) AND ( DATEDIFF( a.end_of_life_date, CURDATE() ) <= '.$period_days.' ) AND ( a.purchase_price > 0 ) ) THEN a.purchase_price ELSE 0 END ) `due_to_expire_cost`,
                                COUNT( a.asset_id ) `counted_assets`, SUM( CASE WHEN a.purchase_price > 0 THEN a.purchase_price ELSE 0 END ) `replacement_cost`', false )
                            ->where( $where )
                            ->where( 'a.account_id', $account_id )
                            ->where( 'a.archived !=', 1 )
                            ->get( 'asset a' );

                        $num_rows = ( $asset_data->num_rows() > 0 ) ? $asset_data->num_rows() : false;
                        break; */

                case 'tagging_summary':
                    $stats_data = [
                        'total_assets'   =>0,
                        'total_buildings'=>0,
                        'number_of_flats'=>0,
                        'number_of_floors'=>0,
                    ];
                    $buildings_data  = $this->db->select('s.site_id, s.site_name, s.number_of_flats, s.archived')
                        ->where('s.account_id', $account_id)
                        ->where('s.archived !=', 1)
                        ->get('site s');

                    if ($buildings_data->num_rows() > 0) {
                        foreach ($buildings_data->result() as $row) {
                            $stats_data['total_buildings'] 	+= 1;
                            $stats_data['number_of_flats'] 	+= $row->number_of_flats;

                            $assets = $this->db->select('SUM( CASE WHEN asset.asset_id > 0 THEN 1 ELSE 0 END ) `total_assets`', false)
                                ->where('asset.account_id', $account_id)
                                ->where('asset.site_id', $row->site_id)
                                ->where('asset.archived !=', 1)
                                ->get('asset');

                            if ($assets->num_rows() > 0) {
                                if ($assets->result()[0]->total_assets > 0) {
                                    $stats_data['total_assets'] += $assets->result()[0]->total_assets;
                                }
                            }
                        }
                    }

                    if (!empty($stats_data)) {
                        foreach ($stats_data as $col => $val) {
                            $num_rows['stats'][] 	= [ 'column_key'=>$col, 'column_header'=> ucwords(str_replace("_", " ", $col)), 'column_value'=> (string) $val, 'hex_color'=> '#6CD167' ];
                            $num_rows['totals'][] 	= [ 'column_key'=>$col, 'column_header'=> ucwords(str_replace("_", " ", $col)), 'column_value'=> (string) $val, 'hex_color'=> '#6CD167' ];
                        }
                    }

                    $num_rows 	= (!empty($num_rows)) ? $num_rows : false;
                    $stats_data = (!empty($num_rows)) ? $num_rows : false;

                    break;
            }

            if (!empty($num_rows)) {
                if ($stat_type == 'eol') {
                    $data 		= [];
                    foreach ($asset_data->result_array()[0] as $stat_grp => $eol_data) {
                        if (!empty($eol_statuses)) {
                            foreach ($eol_statuses as $k => $value) {
                                if (strtolower($stat_grp) == ($value->eol_group)) {
                                    $data[] = array_merge((array)$value, ['eol_group_total'=>$eol_data ]);
                                }
                            }
                        }
                    }
                    $result = $data;
                } elseif ($stat_type == 'replace_cost') {
                    $result = $asset_data->result()[0];
                    $result->period_days = ( string ) $period_days;
                } else {
                    $result = (!empty($stats_data)) ? $stats_data : $asset_data->result();
                }

                $this->session->set_flashdata('message', 'Asset stats data found');
            } else {
                $this->session->set_flashdata('message', 'Asset stats data not available');
            }
        } else {
            $this->session->set_flashdata('message', 'Missing required information');
        }
        return $result;
    }


    /*
    * Get Asset EOL Group statuses
    **/
    public function get_eol_statuses($account_id = false, $eol_group = false)
    {
        $result = null;

        if ($account_id) {
            if (!empty($eol_group)) {
                $this->db->where('asset_eol_statuses.eol_group', $eol_group);
            }

            $query = $this->db->where('asset_eol_statuses.account_id', $account_id)
                ->where('is_active', 1)
                ->order_by('eol_group_ordering')
                ->get('asset_eol_statuses');

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'EOL Status data found.');
            } else {
                $this->session->set_flashdata('message', 'No data found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information.');
        }

        return $result;
    }


    /*
    * Get System Based Compliance Data
    **/
    private function _fetch_compliance_data($account_id = false, $expected_systems = false, $installed_systems = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($expected_systems)) {
            $data['compliant'] = $data['non_compliant'] = $all_sites = [];
            foreach ($installed_systems as $site_system) {
                $sites = $this->db->select('asset.site_id')
                    ->where('account_id', $account_id)
                    ->where('asset_type_id', $site_system['asset_type_id'])
                    ->group_by('asset.site_id')
                    ->get('asset');

                if ($sites->num_rows() > 0) {
                    foreach ($sites->result() as $row) {
                        $all_sites[$row->site_id] = $row->site_id;
                    }
                }
            }
            ## debug( count( $all_sites ) );
        }

        return $result;
    }


    /*
    * Get Building statistics
    */
    public function get_buildings_stats($account_id = false, $stat_type = false, $where = false)
    {
        $result 	= false;
        $stat_type 	= !empty($stat_type) ? $stat_type : 'total_number';

        if (!empty($account_id)) {
            $where 		= convert_to_array($where);

            $stat_type 	= !empty($stat_type) ? strtolower($stat_type) : (!empty($where['stat_type']) ? $where['stat_type'] : 'total_number');

            $contract_id= !empty($where['contract_id']) ? $where['contract_id'] : false;

            if (!empty($contract_id)) {
                $sites = $this->db->select('site_id', false)
                    ->where('sites_contracts.contract_id', $contract_id)
                    ->where('sites_contracts.account_id', $account_id)
                    ->where('sites_contracts.contract_id', $contract_id)
                    ->get('sites_contracts');

                if (!empty($sites->num_rows() > 0)) {
                    $site_id = array_column($sites->result(), 'site_id');
                }
            }

            $site_id	= !empty($site_id) ? $site_id : (!empty($where['site_id']) ? $where['site_id'] : false);

            switch($stat_type) {
                case 'system_compliance':
                    ## Get Installed Systems
                    $sites = $this->db->select('site_id')
                        ->where('site.account_id', $account_id)
                        ->where('site.archived !=', 1)
                        ->get('site');

                    if ($sites->num_rows() > 0) {
                        $graph_data 		= [];
                        $site_ids 			= array_column($sites->result_array(), 'site_id');
                        $expected_systems 	= $this->site_service->get_expected_systems($account_id);
                        $system_data 		= [];
                        $compliant_data		= $non_compliant_data = [];

                        foreach ($expected_systems as $system) {
                            $query = $this->db->select('asset.site_id, asset_types.asset_type_id,  asset_types.asset_type, asset_types.asset_group, REPLACE( asset_types.asset_type_ref, " ", "" ) `asset_type_ref`, audit_categories.category_group')
                                ->join('asset_types', 'asset_types.asset_type_id = asset.asset_type_id', 'left')
                                ->join('audit_categories', 'audit_categories.category_id = asset_types.category_id', 'left')
                                ->where('asset.account_id', $account_id)
                                ->where('( asset_types.asset_group = "'.$system->system_group.'" OR asset_types.asset_type_ref = "'.$system->system_group.'" )')
                                ->where_in('asset.site_id', $site_ids)
                                ->where('asset_types.asset_group', 'system')
                                ->get('asset');

                            if ($query->num_rows() > 0) {
                                foreach ($query->result() as $row) {
                                    $system_data[$row->asset_type_ref]['system_data'] 			= $row;
                                    $system_data[$row->asset_type_ref]['sites'][$row->site_id] 	= $row->site_id;
                                }

                                $affected_sites 			= $system_data[$system->system_group]['sites'];
                                $system_comp_data			= $this->get_buildings_stats($account_id, $stat_type = 'audit_result_status', ['site_id' => $affected_sites ]);
                                $site_non_compliant_data	= $this->get_buildings_stats($account_id, $stat_type = 'overdue_compliance', ['site_id' => $affected_sites ]);

                                ## Compliant Data
                                $compliant_data[$system->system_group]['system_id'] 		 = $system->system_id;
                                $compliant_data[$system->system_group]['system_group'] 		 = $system->system_group;
                                $compliant_data[$system->system_group]['system_name']  		 = $system->system_name;
                                $compliant_data[$system->system_group]['system_stats'] 		 = !empty($system_comp_data) ? $system_comp_data : false;
                                $compliant_data[$system->system_group]['non_compliant_data'] = !empty($site_non_compliant_data) ? $site_non_compliant_data : false;
                            } else {
                                ## Non Compliant Data
                                $system_comp_data	= false;
                                $non_compliant_data[$system->system_group]['system_id'] 		 = $system->system_id;
                                $non_compliant_data[$system->system_group]['system_group'] 		 = $system->system_group;
                                $non_compliant_data[$system->system_group]['system_name']  		 = $system->system_name;
                                $non_compliant_data[$system->system_group]['system_stats'] 		 = false;
                                $non_compliant_data[$system->system_group]['non_compliant_data'] = false;
                            }
                        }

                        $stats_data = array_merge($compliant_data, $non_compliant_data);

                        $num_rows 	= $stats_data;

                        if (!empty($stats_data)) {
                            $this->session->set_flashdata('message', 'Building Systems data found');
                            return $stats_data;
                        } else {
                            $this->session->set_flashdata('message', 'No Systems Complaiance data found');
                            return false;
                        }
                    }

                    break;

                case 'total_number':

                    $stats_data = (object)[];

                    if (!empty($contract_id)) {
                        $this->db->where('sites_contracts.contract_id', $contract_id);
                    }

                    $building_stats = $this->db->select('SUM( CASE WHEN site.site_id > 0  THEN 1 ELSE 0 END) AS total_buildings', false)
                        ->where('site.account_id', $account_id)
                        ->join('sites_contracts', 'sites_contracts.site_id = site.site_id', 'left')
                        ->join('contract', 'contract.contract_id = sites_contracts.contract_id', 'left')
                        ->get('site');

                    if ($building_stats->num_rows() > 0) {
                        if (!empty($contract_id)) {
                            $this->db->where('contract.contract_id', $contract_id);
                        }

                        $total_contracts = $this->db->select('SUM( CASE WHEN contract.contract_id > 0  THEN 1 ELSE 0 END) AS total_contracts', false)
                            ->where('contract.account_id', $account_id)
                            ->get('contract')->row();

                        $num_rows 	= true;
                        $stats_data->total_buildings = $building_stats->result()[0]->total_buildings;

                        if (!empty($stats_data)) {
                            $stats_data->total_contracts = $total_contracts->total_contracts;
                        }
                    }

                    break;

                case 'inspection':
                        //
                    $num_rows = ($building_stats->num_rows() > 0) ? true : false;
                    break;

                case 'compliance':

                    $stats_data = [];
                    $result_statuses  = $this->db->select('audit_result_statuses.*')
                        ->order_by('result_ordering')
                        ->get_where('audit_result_statuses', [ 'account_id'=>$account_id ]);

                    if ($result_statuses->num_rows() > 0) {
                        foreach ($result_statuses->result() as $k => $row) {
                            if (strtolower($row->result_status_group)  == 'not_set') {
                                $group_not_set = true;
                            }

                            if (!empty($site_id)) {
                                if (is_array($site_id)) {
                                    $this->db->where_in('site.site_id', $site_id);
                                } else {
                                    $this->db->where('site.site_id', $site_id);
                                }
                            }

                            $audit_result_status_id = $row->audit_result_status_id;

                            if (!empty($group_not_set)) {
                                $this->db->select('SUM( CASE WHEN ( site.audit_result_status_id = 0 OR site.audit_result_status_id IS NULL ) THEN 1 ELSE 0 END ) AS status_not_set', false);
                            } else {
                                $this->db->select('SUM( CASE WHEN site.audit_result_status_id = "'.$audit_result_status_id.'" THEN 1 ELSE 0 END ) `status_total`', false);
                            }

                            $building_statuses = $this->db->where('site.account_id', $account_id)
                                ->where('site.archived !=', 1)
                                ->order_by('site.site_name')
                                ->get('site');

                            if ($building_statuses->num_rows() > 0) {
                                $total_records = !empty($building_statuses->result()[0]->status_total) ? $building_statuses->result()[0]->status_total : (!empty($building_statuses->result()[0]->status_not_set) ? $building_statuses->result()[0]->status_not_set : 0);
                                $stats_data[] = array_merge((array) $row, [ 'status_total'=>( string )$total_records ]);
                            }
                        }

                        $num_rows = (!empty($stats_data)) ? $stats_data : false;
                    }

                    break;

                case 'overdue_compliance':

                    $result_statuses  	= $this->db->select('audit_result_statuses.*')
                        ->where_in('audit_result_statuses.result_status_group', ['failed'])
                        ->order_by('result_ordering')
                        ->get_where('audit_result_statuses', [ 'account_id'=>$account_id ]);

                    if ($result_statuses->num_rows() > 0) {
                        foreach (overdue_month_ranges() as $periodic_range) {
                            switch($periodic_range->range_rank) {
                                ## 0 - 3 Months
                                case 1:
                                case '1':

                                    $stats_data = [];

                                    foreach ($result_statuses->result() as $k => $row) {
                                        $group_min1 = 0;
                                        $group_max1 = 90;
                                        $this->db->where('( ( DATEDIFF( CURDATE(), DATE_FORMAT( site.audit_result_timestamp, "%Y-%m-%d" ) ) >= '.$group_min1.' ) AND ( DATEDIFF( CURDATE(), DATE_FORMAT( site.audit_result_timestamp, "%Y-%m-%d" ) ) < '.$group_max1.' ) )');

                                        if (strtolower($row->result_status_group)  == 'not_set') {
                                            $group_not_set = true;
                                        }

                                        if (!empty($site_id)) {
                                            if (is_array($site_id)) {
                                                $this->db->where_in('site.site_id', $site_id);
                                            } else {
                                                $this->db->where('site.site_id', $site_id);
                                            }
                                        }

                                        $audit_result_status_id = $row->audit_result_status_id;

                                        if (!empty($group_not_set)) {
                                            $this->db->select('SUM( CASE WHEN ( site.audit_result_status_id = 0 OR site.audit_result_status_id IS NULL ) THEN 1 ELSE 0 END ) AS status_not_set', false);
                                        } else {
                                            $this->db->select('SUM( CASE WHEN site.audit_result_status_id = "'.$audit_result_status_id.'" THEN 1 ELSE 0 END ) `status_total`', false);
                                        }

                                        $building_statuses = $this->db->where('site.account_id', $account_id)
                                            ->where('site.archived !=', 1)
                                            ->order_by('site.site_name')
                                            ->get('site');

                                        if ($building_statuses->num_rows() > 0) {
                                            $total_records = !empty($building_statuses->result()[0]->status_total) ? $building_statuses->result()[0]->status_total : (!empty($building_statuses->result()[0]->status_not_set) ? $building_statuses->result()[0]->status_not_set : 0);
                                            if ($total_records > 0) {
                                                $stats_data[] = array_merge((array) $row, [ 'status_total'=>( string )$total_records ]);
                                            }
                                        }
                                    }

                                    if (!empty($stats_data)) {
                                        $grand_total 					= ( string ) array_sum(array_column($stats_data, 'status_total'));//Get the grand total
                                        $stats_arr 						= array_combine(array_map('strtolower', array_column($stats_data, 'result_status_group')), array_column($stats_data, 'status_total'));
                                        $data['stats']					= $stats_data;
                                        $data['totals'] 				= (!empty($stats_arr) && !empty($grand_total)) ? array_merge(['grand_total'=>$grand_total], $stats_arr) : [];
                                        $data['hex_color']	  			= 'orange';
                                        $data['group_color']	  		= 'orange';
                                        $data['group_range']	  		= '0-3 Months overdue';
                                        $data['range_index']	  		= 1;

                                        if (!empty($data['totals']['grand_total']) && (!empty($data['totals']['failed']) && ($data['totals']['failed'] > 0))) {
                                            $data['totals']['compliance'] 	  = (number_format(((($data['totals']['failed']) / $data['totals']['grand_total']) * 100), 2) + 0).'%';
                                            $data['totals']['compliance_raw'] = ( string ) (number_format(((($data['totals']['failed']) / $data['totals']['grand_total']) * 100), 2) + 0);
                                            $data['totals']['compliance_alt'] = 'Not Compliant';
                                        } else {
                                            $data['totals']['grand_total'] 	  = '0';
                                            $data['totals']['failed'] 	  	  = '0';
                                            $data['totals']['recommendations']= '0';
                                            $data['totals']['passed']		  = '0';
                                            $data['totals']['not_set']		  = '0';
                                            $data['totals']['compliance'] 	  = '0';
                                            $data['totals']['compliance_raw'] = '0';
                                            $data['totals']['compliance_alt'] = 'Not Compliant';
                                        }
                                        $result['0-3-months-overdue'] 		  = $data;
                                    } else {
                                        $result['0-3-months-overdue']['stats'] 		  = false;
                                        $result['0-3-months-overdue']['totals']		  = 0;
                                        $result['0-3-months-overdue']['hex_color']	  = 'orange';
                                        $result['0-3-months-overdue']['group_color']  = 'orange';
                                        $result['0-3-months-overdue']['group_range']  = '0-3 Months overdue';
                                        $result['0-3-months-overdue']['range_index']  = 1;
                                    }

                                    break;

                                    ## 3 - 6 Months
                                case 2:
                                case '2':
                                    $stats_data = [];
                                    foreach ($result_statuses->result() as $k => $row) {
                                        $group_min2 = 90;
                                        $group_max2 = 180;
                                        $this->db->where('( ( DATEDIFF( CURDATE(), DATE_FORMAT( site.audit_result_timestamp, "%Y-%m-%d" ) ) >= '.$group_min2.' ) AND ( DATEDIFF( CURDATE(), DATE_FORMAT( site.audit_result_timestamp, "%Y-%m-%d" ) ) < '.$group_max2.' ) )');

                                        if (strtolower($row->result_status_group)  == 'not_set') {
                                            $group_not_set = true;
                                        }

                                        if (!empty($site_id)) {
                                            if (is_array($site_id)) {
                                                $this->db->where_in('site.site_id', $site_id);
                                            } else {
                                                $this->db->where('site.site_id', $site_id);
                                            }
                                        }

                                        $audit_result_status_id = $row->audit_result_status_id;

                                        if (!empty($group_not_set)) {
                                            $this->db->select('SUM( CASE WHEN ( site.audit_result_status_id = 0 OR site.audit_result_status_id IS NULL ) THEN 1 ELSE 0 END ) AS status_not_set', false);
                                        } else {
                                            $this->db->select('SUM( CASE WHEN site.audit_result_status_id = "'.$audit_result_status_id.'" THEN 1 ELSE 0 END ) `status_total`', false);
                                        }

                                        $building_statuses = $this->db->where('site.account_id', $account_id)
                                            ->where('site.archived !=', 1)
                                            ->order_by('site.site_name')
                                            ->get('site');

                                        if ($building_statuses->num_rows() > 0) {
                                            $total_records = !empty($building_statuses->result()[0]->status_total) ? $building_statuses->result()[0]->status_total : (!empty($building_statuses->result()[0]->status_not_set) ? $building_statuses->result()[0]->status_not_set : 0);
                                            if ($total_records > 0) {
                                                $stats_data[] = array_merge((array) $row, [ 'status_total'=>( string )$total_records ]);
                                            }
                                        }
                                    }

                                    if (!empty($stats_data)) {
                                        $grand_total 					= ( string ) array_sum(array_column($stats_data, 'status_total'));//Get the grand total
                                        $stats_arr 						= array_combine(array_map('strtolower', array_column($stats_data, 'result_status_group')), array_column($stats_data, 'status_total'));
                                        $data['stats']					= $stats_data;
                                        $data['totals'] 				= (!empty($stats_arr) && !empty($grand_total)) ? array_merge(['grand_total'=>$grand_total], $stats_arr) : [];
                                        $data['hex_color']	  			= '#FC5B5B';
                                        $data['group_color']	  		= 'red';
                                        $data['group_range']	  		= '3-6 Months overdue';
                                        $data['range_index']	  		= 2;

                                        if (!empty($data['totals']['grand_total']) && (!empty($data['totals']['failed']) && ($data['totals']['failed'] > 0))) {
                                            $data['totals']['compliance'] 	  = (number_format(((($data['totals']['failed']) / $data['totals']['grand_total']) * 100), 2) + 0).'%';
                                            $data['totals']['compliance_raw'] = ( string ) (number_format(((($data['totals']['failed']) / $data['totals']['grand_total']) * 100), 2) + 0);
                                            $data['totals']['compliance_alt'] = 'Not Compliant';
                                        } else {
                                            $data['totals']['grand_total'] 	  = '0';
                                            $data['totals']['failed'] 	  	  = '0';
                                            $data['totals']['recommendations']= '0';
                                            $data['totals']['passed']		  = '0';
                                            $data['totals']['not_set']		  = '0';
                                            $data['totals']['compliance'] 	  = '0';
                                            $data['totals']['compliance_raw'] = '0';
                                            $data['totals']['compliance_alt'] = 'Not Compliant';
                                        }
                                        $result['3-6-months-overdue'] 		  = $data;
                                    } else {
                                        $result['3-6-months-overdue']['stats'] 		= false;
                                        $result['3-6-months-overdue']['totals']		= 0;
                                        $result['3-6-months-overdue']['hex_color']	= '#FC5B5B';
                                        $result['3-6-months-overdue']['group_color']= 'red';
                                        $result['3-6-months-overdue']['group_range']= '3-6 Months overdue';
                                        $result['3-6-months-overdue']['range_index']= 2;
                                    }

                                    break;

                                    ## 6+ Months
                                case 3:
                                case '3':
                                    $stats_data = [];
                                    foreach ($result_statuses->result() as $k => $row) {
                                        $group_min3 = 180;
                                        $group_max3 = 365;
                                        $this->db->where('( ( DATEDIFF( CURDATE(), DATE_FORMAT( site.audit_result_timestamp, "%Y-%m-%d" ) ) >= '.$group_min3.' ) AND ( DATEDIFF( CURDATE(), DATE_FORMAT( site.audit_result_timestamp, "%Y-%m-%d" ) ) <= '.$group_max3.' ) )');

                                        if (strtolower($row->result_status_group)  == 'not_set') {
                                            $group_not_set = true;
                                        }

                                        if (!empty($site_id)) {
                                            if (is_array($site_id)) {
                                                $this->db->where_in('site.site_id', $site_id);
                                            } else {
                                                $this->db->where('site.site_id', $site_id);
                                            }
                                        }

                                        $audit_result_status_id = $row->audit_result_status_id;

                                        if (!empty($group_not_set)) {
                                            $this->db->select('SUM( CASE WHEN ( site.audit_result_status_id = 0 OR site.audit_result_status_id IS NULL ) THEN 1 ELSE 0 END ) AS status_not_set', false);
                                        } else {
                                            $this->db->select('SUM( CASE WHEN site.audit_result_status_id = "'.$audit_result_status_id.'" THEN 1 ELSE 0 END ) `status_total`', false);
                                        }

                                        $building_statuses = $this->db->where('site.account_id', $account_id)
                                            ->where('site.archived !=', 1)
                                            ->order_by('site.site_name')
                                            ->get('site');

                                        if ($building_statuses->num_rows() > 0) {
                                            $total_records = !empty($building_statuses->result()[0]->status_total) ? $building_statuses->result()[0]->status_total : (!empty($building_statuses->result()[0]->status_not_set) ? $building_statuses->result()[0]->status_not_set : 0);
                                            if ($total_records > 0) {
                                                $stats_data[] = array_merge((array) $row, [ 'status_total'=>( string )$total_records ]);
                                            }
                                        }
                                    }

                                    if (!empty($stats_data)) {
                                        $grand_total 					= ( string ) array_sum(array_column($stats_data, 'status_total'));//Get the grand total
                                        $stats_arr 						= array_combine(array_map('strtolower', array_column($stats_data, 'result_status_group')), array_column($stats_data, 'status_total'));
                                        $data['stats']					= $stats_data;
                                        $data['totals'] 				= (!empty($stats_arr) && !empty($grand_total)) ? array_merge(['grand_total'=>$grand_total], $stats_arr) : [];
                                        $data['hex_color']	  			= '#C03636';
                                        $data['group_color']	  		= 'dark-red';
                                        $data['group_range']	  		= 'Over 6 Months overdue';
                                        $data['range_index']	  		= 3;

                                        if (!empty($data['totals']['grand_total']) && (!empty($data['totals']['failed']) && ($data['totals']['failed'] > 0))) {
                                            $data['totals']['compliance'] 	  = (number_format(((($data['totals']['failed']) / $data['totals']['grand_total']) * 100), 2) + 0).'%';
                                            $data['totals']['compliance_raw'] = ( string ) (number_format(((($data['totals']['failed']) / $data['totals']['grand_total']) * 100), 2) + 0);
                                            $data['totals']['compliance_alt'] = 'Not Compliant';
                                        } else {
                                            $data['totals']['grand_total'] 	  = '0';
                                            $data['totals']['failed'] 	  	  = '0';
                                            $data['totals']['recommendations']= '0';
                                            $data['totals']['passed']		  = '0';
                                            $data['totals']['not_set']		  = '0';
                                            $data['totals']['compliance'] 	  = '0';
                                            $data['totals']['compliance_raw'] = '0';
                                            $data['totals']['compliance_alt'] = 'Not Compliant';
                                        }
                                        $result['over-6-months-overdue'] 		  = $data;
                                    } else {
                                        $result['over-6-months-overdue']['stats'] 		  = false;
                                        $result['over-6-months-overdue']['totals']		  = 0;
                                        $result['over-6-months-overdue']['hex_color']	  = '#C03636';
                                        ;
                                        $result['over-6-months-overdue']['group_color']	  = 'dark-red';
                                        $result['over-6-months-overdue']['group_range']	  = 'Over 6 Months overdue';
                                        $result['over-6-months-overdue']['range_index']	  = 3;
                                    }

                                    break;
                            }
                        }

                        if (!empty($result)) {
                            $this->session->set_flashdata('message', 'Buildings stats data found');
                            return $result;
                        }
                    }

                    break;

                case 'audit_result_status':

                    if (!empty($where['site_id'])) {
                        $site_id = is_array($where['site_id']) ? $where['site_id'] : [ $where['site_id'] ];
                        $this->db->where_in('site.site_id', $site_id);
                    }

                    $site_data = $this->db->select('audit_result_statuses.*,
							SUM( CASE WHEN ( site.audit_result_status_id = 0 OR site.audit_result_status_id IS NULL OR site.audit_result_status_id != "" ) THEN 1 ELSE 0 END ) AS status_not_set,
							SUM( CASE WHEN  site.audit_result_status_id > 0 THEN 1 ELSE 0 END ) AS status_total', false)
                        ->join('site', 'audit_result_statuses.audit_result_status_id = site.audit_result_status_id', 'left')
                        ->where('audit_result_statuses.account_id', $account_id)
                        ->where('audit_result_statuses.is_active', 1)
                        ->order_by('audit_result_statuses.result_ordering')
                        ->group_by('audit_result_statuses.audit_result_status_id')
                        ->get('audit_result_statuses');

                    $num_rows = ($site_data->num_rows() > 0) ? true : false;

                    break;
            }

            if (!empty($num_rows)) {
                $this->session->set_flashdata('message', 'Buildings stats data found');
                $data = [];
                switch($stat_type) {
                    case 'system_compliance':
                        if (!empty($stats_data)) {
                            $result = $stats_data;
                        }
                        break;
                    case 'total_number':
                        if (!empty($stats_data)) {
                            $result = $stats_data;
                        }
                        break;

                    case 'inspection':
                        $data 	= $building_stats->result();
                        $result = $data;
                        break;

                    case 'compliance':

                        if (!empty($stats_data)) {
                            $grand_total 					= ( string ) array_sum(array_column($stats_data, 'status_total'));//Get the grand total
                            $stats_arr 						= array_combine(array_map('strtolower', array_column($stats_data, 'result_status_group')), array_column($stats_data, 'status_total')); //create a a new array if column => value
                            $data['stats']					= $stats_data;
                            $data['totals'] 				= (!empty($stats_arr) && !empty($grand_total)) ? array_merge(['grand_total'=>$grand_total], $stats_arr) : [];

                            #Calculate Compliance using what has passed + recommendations
                            if (!empty($data['totals']['grand_total']) && (!empty($data['totals']['passed']) && ($data['totals']['passed'] > 0))) {
                                $data['totals']['compliance'] 	  = (number_format(((($data['totals']['passed'] + (!empty($data['totals']['recommendations'] ? $data['totals']['recommendations'] : 0))) / $data['totals']['grand_total']) * 100), 2) + 0).'%';
                                $data['totals']['compliance_raw'] =  ( string ) (number_format(((($data['totals']['passed'] + (!empty($data['totals']['recommendations'] ? $data['totals']['recommendations'] : 0))) / $data['totals']['grand_total']) * 100), 4) + 0);
                                $data['totals']['compliance_alt'] = 'Compliant';
                            }

                            # Calculate compliance based on what has failed
                            if (!empty($data['totals']['grand_total']) && (!empty($data['totals']['failed']) && ($data['totals']['failed'] > 0))) {
                                $data['totals']['compliance'] 	  = (number_format(((($data['totals']['failed']) / $data['totals']['grand_total']) * 100), 2) + 0).'%';
                                $data['totals']['compliance_raw'] = ( string ) (number_format(((($data['totals']['failed']) / $data['totals']['grand_total']) * 100), 2) + 0);
                                $data['totals']['compliance_alt'] = 'Not Compliant';
                            }
                            $result = $data;
                        }

                        break;

                    case 'audit_result_status':

                        $data = [];
                        $grand_total 					= ( string ) array_sum(array_column($site_data->result_array(), 'status_total'));//Get the grand total
                        $stats_arr 						= array_combine(array_map('strtolower', array_column($site_data->result_array(), 'result_status_group')), array_column($site_data->result_array(), 'status_total')); //creata a new array if column => value
                        $data['stats']					= $site_data->result_array();
                        $data['totals'] 				= (!empty($stats_arr) && !empty($grand_total)) ? array_merge(['grand_total'=>$grand_total], $stats_arr) : [];

                        #Calculate Complaiance using what has passed + recommendations
                        if (!empty($data['totals']['grand_total']) && (!empty($data['totals']['passed']) && ($data['totals']['passed'] > 0))) {
                            $data['totals']['compliance'] 	  = (number_format((
                                (
                                    (
                                        $data['totals']['passed'] + ((isset($data['totals']['recommendations']) && !empty($data['totals']['recommendations'])) ? $data['totals']['recommendations'] : 0)
                                    ) / $data['totals']['grand_total']
                                ) * 100
                            ), 2) + 0).'%';
                            $data['totals']['compliance_raw'] =  ( string ) (number_format((
                                (
                                    (
                                        $data['totals']['passed'] + ((isset($data['totals']['recommendations']) && !empty($data['totals']['recommendations'])) ? $data['totals']['recommendations'] : 0)
                                    ) / $data['totals']['grand_total']
                                ) * 100
                            ), 4) + 0);
                            $data['totals']['compliance_alt'] = 'Compliant';
                        }

                        # Calculate compliance based on what has failed
                        if (!empty($data['totals']['grand_total']) && (!empty($data['totals']['failed']) && ($data['totals']['failed'] > 0))) {
                            $data['totals']['compliance'] 	  = (number_format(((($data['totals']['failed']) / $data['totals']['grand_total']) * 100), 2) + 0).'%';
                            $data['totals']['compliance_raw'] = ( string ) (number_format(((($data['totals']['failed']) / $data['totals']['grand_total']) * 100), 2) + 0);
                            $data['totals']['compliance_alt'] = 'Not Compliant';
                        }

                        $result = $data;

                        break;
                }
            } else {
                $this->session->set_flashdata('message', 'Buildings stats data not available');
            }
        } else {
            $this->session->set_flashdata('message', 'Missing required information');
        }
        return $result;
    }


    /*
    * Evidocs Statistics
    **/
    public function get_audit_stats($account_id = false, $stat_type = false, $period_days = DEFAULT_PERIOD_DAYS, $where = false, $date_from = false, $date_to = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($stat_type)) {
            $where 			= convert_to_array($where);
            $current_date 	= date('Y-m-d');
            $date_from 		= !empty($date_from) ? date('Y-m-d', strtotime($date_from)) : date('Y-m-01', strtotime($current_date));
            $date_to  		= !empty($date_to) ? date('Y-m-d', strtotime($date_to)) : date('Y-m-t', strtotime($current_date));
            $date_to		= (strtotime($date_to) > strtotime($date_from.' + '.$period_days)) ? $date_to : date('Y-m-d', strtotime($date_from.' + '.$period_days));

            switch(strtolower($stat_type)) {
                case 'completion':

                    $contract_id = !empty($where['contract_id']) ? $where['contract_id'] : false;

                    if (!empty($contract_id)) {
                        $this->db->where('audit_types.contract_id', $contract_id);
                    }

                    $query = $this->db->select(
                        'SUM( CASE WHEN audit_status = "In Progress"  THEN 1 ELSE 0 END ) AS `in_progress`,
						SUM( CASE WHEN audit_status = "Completed"  THEN 1 ELSE 0 END ) AS `completed`,
						SUM( CASE WHEN audit_id > 0 THEN 1 ELSE 0 END ) AS `Total`',
                        false
                    )->join('audit_types', 'audit.audit_type_id = audit_types.audit_type_id', 'left')
                        ->where('audit.account_id', $account_id)
                        ->where('audit.archived !=', 1)
                        ->order_by('audit.audit_status')
                        ->get('audit');

                    if ($query->num_rows() > 0) {
                        $row = $query->result()[0];
                        $data = [
                            [
                                'status_total'		=> ( string) $row->in_progress,
                                'status_name'		=> 'in_progress',
                                'status_name_alt'	=> 'In Progress',
                                'hex_colour'		=> '#F78A48',
                            ],
                            [
                                'status_total'		=> ( string) $row->completed,
                                'status_name'		=> 'completed',
                                'status_name_alt'	=> 'Completed',
                                'hex_colour'		=> '#6CD167',
                            ],
                            [
                                'status_total'		=> ( string) ($row->in_progress + $row->completed),
                                'status_name'		=> 'total',
                                'status_name_alt'	=> 'Total',
                                'hex_colour'		=> '#C03636',
                            ],
                        ];
                        $result = $data;
                    }

                    break;

                case 'periodic_audits':
                    #$result = $this->_audits_due( $account_id, $period_days );
                    break;

                case 'audit_results':

                    $stats_data = [];
                    $result_statuses  = $this->db->select('audit_result_statuses.*')
                        ->order_by('audit_result_statuses.result_ordering')
                        ->group_by('audit_result_statuses.audit_result_status_id')
                        ->get_where('audit_result_statuses', [ 'account_id'=>$account_id ]);

                    if ($result_statuses->num_rows() > 0) {
                        $contract_id = !empty($where['contract_id']) ? $where['contract_id'] : false;

                        foreach ($result_statuses->result() as $k => $row) {
                            if (strtolower($row->result_status_group)  == 'not_set') {
                                $group_not_set = true;
                            }

                            $audit_result_status_id = $row->audit_result_status_id;

                            if (!empty($group_not_set)) {
                                $this->db->select('SUM( CASE WHEN ( audit.audit_result_status_id = 0 OR audit.audit_result_status_id IS NULL ) THEN 1 ELSE 0 END ) AS status_not_set', false);
                            } else {
                                $this->db->select('SUM( CASE WHEN audit.audit_result_status_id = "'.$audit_result_status_id.'" THEN 1 ELSE 0 END ) `status_total`', false);
                            }

                            if (!empty($contract_id)) {
                                $this->db->where('audit_types.contract_id', $contract_id);
                            }

                            $evidoc_statuses = $this->db->where('audit.account_id', $account_id)
                                ->join('audit_types', 'audit.audit_type_id = audit_types.audit_type_id', 'left')
                                ->where('audit.archived !=', 1)
                                ->order_by('audit.audit_id')
                                ->get('audit');

                            if ($evidoc_statuses->num_rows() > 0) {
                                $total_records = !empty($evidoc_statuses->result()[0]->status_total) ? $evidoc_statuses->result()[0]->status_total : (!empty($evidoc_statuses->result()[0]->status_not_set) ? $evidoc_statuses->result()[0]->status_not_set : 0);
                                $stats_data[] = array_merge((array) $row, [ 'status_total'=>( string )$total_records ]);
                            }
                        }
                    }

                    if (!empty($stats_data)) {
                        $data = [];
                        $grand_total 				= (string) array_sum(array_column($stats_data, 'status_total'));//Get the grand total
                        $stats_arr 					= array_combine(array_map('strtolower', array_column($stats_data, 'result_status_group')), array_column($stats_data, 'status_total'));
                        $data['stats']				= $stats_data;
                        $data['totals'] 			= (!empty($stats_arr) && !empty($grand_total)) ? array_merge(['grand_total'=>$grand_total], $stats_arr) : [];
                        $data['dates'] 				= [
                            'date_from'=>date('d/m/Y', strtotime($date_from)),
                            'date_to'=>date('d/m/Y', strtotime($date_to)),
                            'period_days'=> ( string ) floor((strtotime($date_to) - strtotime($date_from)) / 86000)
                        ];

                        #Calculate Compliance using what passed + recommendations
                        if (!empty($data['totals']['grand_total']) && (!empty($data['totals']['passed']) && ($data['totals']['passed'] > 0))) {
                            $data['totals']['compliance'] 	  = (number_format(((($data['totals']['passed'] + (!empty($data['totals']['recommendations'] ? $data['totals']['recommendations'] : 0))) / $data['totals']['grand_total']) * 100), 2) + 0).'%';
                            $data['totals']['compliance_raw'] =  ( string ) (number_format(((($data['totals']['passed'] + (!empty($data['totals']['recommendations'] ? $data['totals']['recommendations'] : 0))) / $data['totals']['grand_total']) * 100), 4) + 0);
                            $data['totals']['compliance_alt'] = 'Passed';
                        }

                        # Calculate compliance based on what failed
                        if (!empty($data['totals']['grand_total']) && (!empty($data['totals']['failed']) && ($data['totals']['failed'] > 0))) {
                            $data['totals']['compliance'] 	  = (number_format(((($data['totals']['failed']) / $data['totals']['grand_total']) * 100), 2) + 0).'%';
                            $data['totals']['compliance_raw'] = ( string ) (number_format(((($data['totals']['failed']) / $data['totals']['grand_total']) * 100), 2) + 0);
                            $data['totals']['compliance_alt'] = 'Didn\'t Pass';
                        }

                        $result = $data;
                    }
                    break;
            }

            if (!empty($result)) {
                $this->session->set_flashdata('message', 'Evidocs stats data found!');
            } else {
                $this->session->set_flashdata('message', 'No data matching your criteria!');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }

    /*
    * Exception Status
    **/
    public function exceptions_status_stats($account_id = false, $stat_type = false, $where = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($stat_type)) {
            $where = convert_to_array($where);

            $contract_id = !empty($where['contract_id']) ? $where['contract_id'] : false;

            $action_statuses = $this->db->get_where('audit_action_statuses', ['account_id'=>$account_id]);

            if ($action_statuses->num_rows() > 0) {
                foreach ($action_statuses->result() as $row) {
                    $action_status_id = $row->action_status_id;

                    if (!empty($group_not_set)) {
                        $this->db->select('SUM( CASE WHEN ( audit_exceptions.action_status_id = 0 OR audit_exceptions.action_status_id IS NULL ) THEN 1 ELSE 0 END ) AS status_not_set', false);
                    } else {
                        $this->db->select('SUM( CASE WHEN audit_exceptions.action_status_id = "'.$action_status_id.'" THEN 1 ELSE 0 END ) `status_total`', false);
                    }

                    if (!empty($site_id)) {
                        if (is_array($site_id)) {
                            $this->db->where_in('audit_exceptions.site_id', $site_id);
                        } else {
                            $this->db->where('audit_exceptions.site_id', $site_id);
                        }
                    }

                    $exceptions_statues = $this->db->where('audit_exceptions.account_id', $account_id)
                        ->where('audit_exceptions.is_active', 1)
                        ->order_by('audit_exceptions.asset_id')
                        ->get('audit_exceptions');


                    if ($exceptions_statues->num_rows() > 0) {
                        $total_records = !empty($exceptions_statues->result()[0]->status_total) ? $exceptions_statues->result()[0]->status_total : (!empty($exceptions_statues->result()[0]->status_not_set) ? $exceptions_statues->result()[0]->status_not_set : 0);
                        $stats_data[] = array_merge((array) $row, [ 'status_total'=>( string )$total_records ]);
                    }
                }

                if (!empty($stats_data)) {
                    $data = [];
                    $grand_total 	= (string) array_sum(array_column($stats_data, 'status_total'));
                    $stats_arr 		= array_combine(array_map('strtolower', array_column($stats_data, 'action_status_group')), array_column($stats_data, 'status_total'));
                    $data['stats']	= $stats_data;
                    $data['totals'] = (!empty($stats_arr) && !empty($grand_total)) ? array_merge(['grand_total'=>$grand_total], $stats_arr) : [];

                    if (!empty($date_from)) {
                        $data['dates'] 				= [
                            'date_from'=>date('d/m/Y', strtotime($date_from)),
                            'date_to'=>date('d/m/Y', strtotime($date_to)),
                            'period_days'=> ( string ) floor((strtotime($date_to) - strtotime($date_from)) / 86000)
                        ];
                    }

                    #Calculate Resolution using what passed + recommendations
                    if (!empty($data['totals']['grand_total']) && (!empty($data['totals']['actioned']) && ($data['totals']['actioned'] > 0))) {
                        $data['totals']['compliance'] 	  = (number_format(((($data['totals']['actioned'] + (!empty($data['totals']['under_review'] ? $data['totals']['under_review'] : 0))) / $data['totals']['grand_total']) * 100), 2) + 0).'%';
                        $data['totals']['compliance_raw'] =  ( string ) (number_format(((($data['totals']['actioned'] + (!empty($data['totals']['under_review'] ? $data['totals']['under_review'] : 0))) / $data['totals']['grand_total']) * 100), 2) + 0);
                        $data['totals']['compliance_alt'] = 'Actioned';
                    }

                    $stats_data = $data;
                    $num_rows 	= true;
                }
            }

            if (!empty($stats_data)) {
                $result = $stats_data;
                $this->session->set_flashdata('message', 'Exception stats data found');
            } else {
                $this->session->set_flashdata('message', 'Exception stats data not available');
            }

            return $result;
        }
    }


    /*
    * Get Planned works Statistics (Schedules)
    **/
    public function get_schedules_stats($account_id = false, $stat_type = false, $where = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($stat_type)) {
            $where = convert_to_array($where);

            $contract_id 		= !empty($where['contract_id']) ? $where['contract_id'] : false;


            switch(strtolower($stat_type)) {
                case 'checklist_schedules':

                    $current_year_start	= !empty($where['year_start']) ? date('Y-m-d', strtotime($where['year_start'])) : date('Y-m-d', strtotime('Jan 01'));
                    $current_year_end 	= !empty($where['year_end']) ? date('Y-m-d', strtotime($where['year_end'])) : date('Y-m-d', strtotime('Dec 31'));

                    /* $query = $this->db->select( 'SUM( CASE WHEN MONTH( sa.due_date ) = 1 THEN 1 ELSE 0 END ) AS `1.january`,
                        SUM( CASE WHEN MONTH( sa.due_date ) = 2 THEN 1 ELSE 0 END ) AS `2.february`,
                        SUM( CASE WHEN MONTH( sa.due_date ) = 3 THEN 1 ELSE 0 END ) AS `3.march`,
                        SUM( CASE WHEN MONTH( sa.due_date ) = 4 THEN 1 ELSE 0 END ) AS `4.april`,
                        SUM( CASE WHEN MONTH( sa.due_date ) = 5 THEN 1 ELSE 0 END ) AS `5.may`,
                        SUM( CASE WHEN MONTH( sa.due_date ) = 6 THEN 1 ELSE 0 END ) AS `6.june`,
                        SUM( CASE WHEN MONTH( sa.due_date ) = 7 THEN 1 ELSE 0 END ) AS `7.july`,
                        SUM( CASE WHEN MONTH( sa.due_date ) = 8 THEN 1 ELSE 0 END ) AS `8.august`,
                        SUM( CASE WHEN MONTH( sa.due_date ) = 9 THEN 1 ELSE 0 END ) AS `9.september`,
                        SUM( CASE WHEN MONTH( sa.due_date ) = 10 THEN 1 ELSE 0 END ) AS `10.october`,
                        SUM( CASE WHEN MONTH( sa.due_date ) = 11 THEN 1 ELSE 0 END ) AS `11.november`,
                        SUM( CASE WHEN MONTH( sa.due_date ) = 12 THEN 1 ELSE 0 END ) AS `12.december`,
                        SUM( CASE WHEN sa.due_date IS NOT NULL THEN 1 ELSE 0 END ) AS `total`', false )
                            ->where( 'sa.account_id', $account_id )
                            ->where( 'sa.due_date >= "'.$current_year_start.'" ' )
                            ->where( 'sa.due_date <= "'.$current_year_end.'" ' )
                            ->get( 'schedule_activities sa' ); */

                    $query = $this->db->select('SUM( CASE WHEN MONTH( job.due_date ) = 1 THEN 1 ELSE 0 END ) AS `1.january`,
						SUM( CASE WHEN MONTH( job.due_date ) = 2 THEN 1 ELSE 0 END ) AS `2.february`,
						SUM( CASE WHEN MONTH( job.due_date ) = 3 THEN 1 ELSE 0 END ) AS `3.march`,
						SUM( CASE WHEN MONTH( job.due_date ) = 4 THEN 1 ELSE 0 END ) AS `4.april`,
						SUM( CASE WHEN MONTH( job.due_date ) = 5 THEN 1 ELSE 0 END ) AS `5.may`,
						SUM( CASE WHEN MONTH( job.due_date ) = 6 THEN 1 ELSE 0 END ) AS `6.june`,
						SUM( CASE WHEN MONTH( job.due_date ) = 7 THEN 1 ELSE 0 END ) AS `7.july`,
						SUM( CASE WHEN MONTH( job.due_date ) = 8 THEN 1 ELSE 0 END ) AS `8.august`,
						SUM( CASE WHEN MONTH( job.due_date ) = 9 THEN 1 ELSE 0 END ) AS `9.september`,
						SUM( CASE WHEN MONTH( job.due_date ) = 10 THEN 1 ELSE 0 END ) AS `10.october`,
						SUM( CASE WHEN MONTH( job.due_date ) = 11 THEN 1 ELSE 0 END ) AS `11.november`,
						SUM( CASE WHEN MONTH( job.due_date ) = 12 THEN 1 ELSE 0 END ) AS `12.december`,
						SUM( CASE WHEN job.due_date IS NOT NULL THEN 1 ELSE 0 END ) AS `total`', false)
                            ->where('job.account_id', $account_id)
                            ->where('job.due_date >= "'.$current_year_start.'" ')
                            ->where('job.due_date <= "'.$current_year_end.'" ')
                            ->where('job.external_job_ref > 0 ')
                            //->where( 'job.external_job_created_on >= "2021-06-01 00:00:01" ' ) //From June 2021 onwards
                            //->where_in( 'site.external_site_ref', [ 'HHG1425','HHG1581','HHG1582','HHG1583','HHG1592','HHG1593','HHG1594','HHG1595','HHG1596','HHG1597','HHG1601','HHG2989','HHG3180','HHG3204','HHG3221','HHG3222','HHG1601','HHG0871','HHG0872','HHG0878','HHG0879','HHG0881','HHG0881','HHG3019','HHG3181', 'HHG0871','HHG0872','HHG0878','HHG0879','HHG0881','HHG1425','HHG1581','HHG1582','HHG1583','HHG1592','HHG1593','HHG1594','HHG1595','HHG1596','HHG1597','HHG1601','HHG2989','HHG3019','HHG3019','HHG3180','HHG3181','HHG3204','HHG3221','HHG3222','HHG1581','HHG1581','HHG1582','HHG1583','HHG3204' ] )
                            ->where_in('site.external_site_ref', [ 'HHG1425','HHG1581','HHG1582','HHG1583','HHG1592','HHG1593','HHG1594','HHG1595','HHG1596','HHG1597','HHG1601','HHG2989','HHG3180','HHG3204','HHG3221','HHG3222','HHG0871','HHG0872','HHG0878','HHG0879','HHG0881','HHG3019','HHG3181' ])
                            ->join('site', 'site.site_id = job.site_id', 'left')
                            ->group_by('job.job_id')
                            ->get('job');


                    if ($query->num_rows() > 0) {
                        $stats_data = [];
                        $months = $query->result()[0];

                        $total_in_progress = $total_activities = $total_completed = $total_not_started = $total_cancelled = 0;

                        foreach ($months as $month_data => $month_totals) {
                            if ($month_data != 'total') {
                                $month_arr 		= explode('.', $month_data);
                                $month_number 	= !empty($month_arr[0]) ? $month_arr[0] : false;
                                $month_name 	= !empty($month_arr[1]) ? $month_arr[1] : false;

                                if (!empty($month_number)) {
                                    $per_month_stats 	= $this->db->select('SUM( CASE WHEN ( MONTH( job.due_date ) = '.$month_number.' AND job_statuses.job_status IN ( "Assigned" ) ) THEN 1 ELSE 0 END ) AS `not_started`,
									SUM( CASE WHEN ( MONTH( job.due_date ) = '.$month_number.' AND job_statuses.job_status IN ( "In Progress", "On Hold", "En Route", "On Site" ) ) THEN 1 ELSE 0 END ) AS `in_progress`,
									SUM( CASE WHEN ( MONTH( job.due_date ) = '.$month_number.' AND job_statuses.job_status IN ( "Failed", "Cancelled" ) ) THEN 1 ELSE 0 END ) AS `cancelled`,
									SUM( CASE WHEN ( MONTH( job.due_date ) = '.$month_number.' AND ( job_statuses.job_status = "Successful" ) ) THEN 1 ELSE 0 END ) AS `completed`,
									SUM( CASE WHEN ( MONTH( job.due_date ) = '.$month_number.' ) THEN 1 ELSE 0 END ) AS `total`', false)
                                        ->where('job.account_id', $account_id)
                                        ->where('job.due_date >= "'.$current_year_start.'" ')
                                        ->where('job.due_date <= "'.$current_year_end.'" ')
                                        ->where('job.external_job_ref > 0 ')
                                        ->where('job.external_job_created_on >= "2021-06-01 00:00:01" ')
                                        ->join('job_statuses', 'job_statuses.status_id = job.status_id', 'left')
                                        ->get('job');

                                    if ($per_month_stats->num_rows() > 0) {
                                        $result_data 		= $per_month_stats->result()[0];
                                        $month_name_short 	= substr($month_name, 0, 3);
                                        $stats_data['stats'][$month_name_short] = [
                                            'month_name' 		=> $month_name,
                                            'month_number' 		=> $month_number,
                                            'month_name_short' 	=> ucwords($month_name_short),
                                            'month_name_full' 	=> ucwords($month_name),
                                            'total_in_progress' => ( string ) $result_data->in_progress,
                                            'total_completed' 	=> ( string ) $result_data->completed,
                                            'total_cancelled' 	=> ( string ) $result_data->cancelled,
                                            'total_not_started' => ( string ) $result_data->not_started,
                                            'total_activities' 	=> ( string ) $result_data->total,
                                        ];

                                        $total_in_progress 	+= !empty($result_data->in_progress) ? $result_data->in_progress : 0;
                                        $total_completed 	+= !empty($result_data->completed) ? $result_data->completed : 0;
                                        $total_cancelled 	+= !empty($result_data->cancelled) ? $result_data->cancelled : 0;
                                        $total_not_started 	+= !empty($result_data->not_started) ? $result_data->not_started : 0;
                                        $total_activities 	+= !empty($result_data->total) ? $result_data->total : 0;
                                    }
                                }
                            }

                            if (!empty($stats_data)) {
                                $stats_data['totals'] = [
                                    'total_in_progress' => !empty($total_in_progress) ? $total_in_progress : 0,
                                    'total_completed' 	=> !empty($total_completed) ? $total_completed : 0,
                                    'total_cancelled' 	=> !empty($total_cancelled) ? $total_cancelled : 0,
                                    'total_not_started' => !empty($total_not_started) ? $total_not_started : 0,
                                    'total_activities' 	=> !empty($total_activities) ? $total_activities : 0
                                ];
                            }
                        }
                    }

                    break;


                case 'schedules':

                    $current_year_start	= !empty($where['year_start']) ? date('Y-m-d', strtotime($where['year_start'])) : date('Y-m-d', strtotime('Jan 01'));
                    $current_year_end 	= !empty($where['year_end']) ? date('Y-m-d', strtotime($where['year_end'])) : date('Y-m-d', strtotime('Dec 31'));

                    $query = $this->db->select('SUM( CASE WHEN MONTH( sa.due_date ) = 1 THEN 1 ELSE 0 END ) AS `1.january`,
						SUM( CASE WHEN MONTH( sa.due_date ) = 2 THEN 1 ELSE 0 END ) AS `2.february`,
						SUM( CASE WHEN MONTH( sa.due_date ) = 3 THEN 1 ELSE 0 END ) AS `3.march`,
						SUM( CASE WHEN MONTH( sa.due_date ) = 4 THEN 1 ELSE 0 END ) AS `4.april`,
						SUM( CASE WHEN MONTH( sa.due_date ) = 5 THEN 1 ELSE 0 END ) AS `5.may`,
						SUM( CASE WHEN MONTH( sa.due_date ) = 6 THEN 1 ELSE 0 END ) AS `6.june`,
						SUM( CASE WHEN MONTH( sa.due_date ) = 7 THEN 1 ELSE 0 END ) AS `7.july`,
						SUM( CASE WHEN MONTH( sa.due_date ) = 8 THEN 1 ELSE 0 END ) AS `8.august`,
						SUM( CASE WHEN MONTH( sa.due_date ) = 9 THEN 1 ELSE 0 END ) AS `9.september`,
						SUM( CASE WHEN MONTH( sa.due_date ) = 10 THEN 1 ELSE 0 END ) AS `10.october`,
						SUM( CASE WHEN MONTH( sa.due_date ) = 11 THEN 1 ELSE 0 END ) AS `11.november`,
						SUM( CASE WHEN MONTH( sa.due_date ) = 12 THEN 1 ELSE 0 END ) AS `12.december`,
						SUM( CASE WHEN sa.due_date IS NOT NULL THEN 1 ELSE 0 END ) AS `total`', false)
                            ->where('sa.account_id', $account_id)
                            ->where('sa.due_date >= "'.$current_year_start.'" ')
                            ->where('sa.due_date <= "'.$current_year_end.'" ')
                            ->get('schedule_activities sa');

                    if ($query->num_rows() > 0) {
                        $stats_data = [];
                        $months = $query->result()[0];

                        $total_in_progress = $total_activities = $total_completed = $total_not_started = 0;

                        foreach ($months as $month_data => $month_totals) {
                            if ($month_data != 'total') {
                                $month_arr 		= explode('.', $month_data);
                                $month_number 	= !empty($month_arr[0]) ? $month_arr[0] : false;
                                $month_name 	= !empty($month_arr[1]) ? $month_arr[1] : false;


                                if (!empty($month_number)) {
                                    $per_month_stats 	= $this->db->select('SUM( CASE WHEN ( MONTH( sa.due_date ) = '.$month_number.' AND sa.status = "Not due" ) THEN 1 ELSE 0 END ) AS `not_started`,
									SUM( CASE WHEN ( MONTH( sa.due_date ) = '.$month_number.' AND sa.status = "In Progress" ) THEN 1 ELSE 0 END ) AS `in_progress`,
									SUM( CASE WHEN ( MONTH( sa.due_date ) = '.$month_number.' AND sa.status = "Completed" ) THEN 1 ELSE 0 END ) AS `completed`,
									SUM( CASE WHEN ( MONTH( sa.due_date ) = '.$month_number.' ) THEN 1 ELSE 0 END ) AS `total`', false)
                                        ->where('sa.account_id', $account_id)
                                        ->where('sa.due_date >= "'.$current_year_start.'" ')
                                        ->where('sa.due_date <= "'.$current_year_end.'" ')
                                        ->get('schedule_activities sa');



                                    if ($per_month_stats->num_rows() > 0) {
                                        $result_data 		= $per_month_stats->result()[0];
                                        $month_name_short 	= substr($month_name, 0, 3);
                                        $stats_data['stats'][$month_name_short] = [
                                            'month_name' 		=> $month_name,
                                            'month_number' 		=> $month_number,
                                            'month_name_short' 	=> ucwords($month_name_short),
                                            'month_name_full' 	=> ucwords($month_name),
                                            'total_in_progress' => ( string ) $result_data->in_progress,
                                            'total_completed' 	=> ( string ) $result_data->completed,
                                            'total_not_started' => ( string ) $result_data->not_started,
                                            'total_activities' 	=> ( string ) $result_data->total,
                                        ];

                                        $total_in_progress 	+= !empty($result_data->in_progress) ? $result_data->in_progress : 0;
                                        $total_completed 	+= !empty($result_data->completed) ? $result_data->completed : 0;
                                        $total_not_started 	+= !empty($result_data->not_started) ? $result_data->not_started : 0;
                                        $total_activities 	+= !empty($result_data->total) ? $result_data->total : 0;
                                    }
                                }
                            }

                            if (!empty($stats_data)) {
                                $stats_data['totals'] = [
                                    'total_in_progress' => !empty($total_in_progress) ? $total_in_progress : 0,
                                    'total_completed' 	=> !empty($total_completed) ? $total_completed : 0,
                                    'total_not_started' => !empty($total_not_started) ? $total_not_started : 0,
                                    'total_activities' 	=> !empty($total_activities) ? $total_activities : 0
                                ];
                            }
                        }
                    }

                    break;

                case 'weekly_schedules':

                    $category_icons		= category_icons();

                    $first_day_of_week 	= date('Y-m-d', strtotime('next Sunday -1 week', strtotime('this sunday')));
                    $last_day_of_week 	= date('Y-m-d', strtotime('next Sunday -1 week + 6 days', strtotime('this sunday')));

                    $category  		= $this->db->select('category_id, category_name, category_name_alt, category_group, description')->order_by('category_name')->get_where('audit_categories', [ 'account_id'=>$account_id, 'is_active'=>1  ]);
                    $category_data	= [];
                    if ($category->num_rows() > 0) {
                        foreach ($category->result() as $k => $row) {
                            if (!empty($site_id)) {
                                if (is_array($site_id)) {
                                    $this->db->where_in('asset.site_id', $site_id);
                                } else {
                                    $this->db->where('asset.site_id', $site_id);
                                }
                            }

                            $category_assets = $this->db->select('asset_id', false)
                                ->join('asset_types', 'asset_types.asset_type_id = asset.asset_type_id', 'left')
                                ->where('asset_types.category_id', $row->category_id)
                                ->where('asset.account_id', $account_id)
                                ->where('asset.archived !=', 1)
                                ->order_by('asset_types.asset_type, asset.asset_id')
                                ->get('asset');

                            if ($category_assets->num_rows() > 0) {
                                $asset_ids = array_column($category_assets->result_array(), 'asset_id');

                                $weekly_query 	= $this->db->select('SUM( CASE WHEN ( sa.status = "Not due" || sa.status = "Not started" || sa.status = "Assigned" ) THEN 1 ELSE 0 END ) AS `not_due`,
									SUM( CASE WHEN ( sa.status = "In Progress" ) THEN 1 ELSE 0 END ) AS `in_progress`,
									SUM( CASE WHEN ( sa.status = "Completed" ) THEN 1 ELSE 0 END ) AS `completed`,
									SUM( CASE WHEN ( sa.status = "Failed" ) THEN 1 ELSE 0 END ) AS `failed`,
									SUM( CASE WHEN ( sa.activity_id > 0 ) THEN 1 ELSE 0 END ) AS `total`', false)
                                        ->where('sa.account_id', $account_id)
                                        ->where('sa.due_date >= "'.$first_day_of_week.'" ')
                                        ->where('sa.due_date <= "'.$last_day_of_week.'" ')
                                        ->where_in('sa.asset_id', $asset_ids)
                                        ->get('schedule_activities sa');

                                if ($weekly_query->num_rows() > 0) {
                                    $category_group = strtolower($row->category_group);
                                    $weekly_stats 	= $weekly_query->result()[0];
                                    $category_data[$row->category_id] = [
                                        'category_id'		=> $row->category_id,
                                        'category_name'		=> $row->category_name,
                                        'category_icon' 	=> (!empty($category_icons->{$category_group})) ? $category_icons->{$category_group}->category_icon : 'assets/images/dashboard-icons/schedules-generic.png',
                                        'category_group'	=> $row->category_group,
                                        'period_range'		=> 'weekly',
                                        'total_in_progress' => !empty($weekly_stats->in_progress) ? $weekly_stats->in_progress : 0,
                                        'total_completed' 	=> !empty($weekly_stats->completed) ? $weekly_stats->completed : 0,
                                        'total_not_due' 	=> !empty($weekly_stats->not_due) ? $weekly_stats->not_due : 0,
                                        'total_failed' 		=> !empty($weekly_stats->failed) ? $weekly_stats->failed : 0,
                                        'total_activities' 	=> !empty($weekly_stats->total) ? $weekly_stats->total : 0
                                    ];
                                }
                            }
                        }
                    }

                    $stats_data['stats'] = !empty($category_data) ? $category_data : false;

                    break;

                case 'monthly_schedules':

                    $category_icons		= category_icons();

                    $first_day_of_month = date('Y-m-01');
                    $last_day_of_month 	= date('Y-m-t');

                    $category  		= $this->db->select('category_id, category_name, category_name_alt, category_group, description')->order_by('category_name')->get_where('audit_categories', [ 'account_id'=>$account_id, 'is_active'=>1 ]);
                    $category_data	= [];
                    if ($category->num_rows() > 0) {
                        foreach ($category->result() as $k => $row) {
                            if (!empty($site_id)) {
                                if (is_array($site_id)) {
                                    $this->db->where_in('asset.site_id', $site_id);
                                } else {
                                    $this->db->where('asset.site_id', $site_id);
                                }
                            }

                            $category_assets = $this->db->select('asset_id', false)
                                ->join('asset_types', 'asset_types.asset_type_id = asset.asset_type_id', 'left')
                                ->where('asset_types.category_id', $row->category_id)
                                ->where('asset.account_id', $account_id)
                                ->where('asset.archived !=', 1)
                                ->order_by('asset_types.asset_type, asset.asset_id')
                                ->get('asset');

                            if ($category_assets->num_rows() > 0) {
                                $asset_ids = array_column($category_assets->result_array(), 'asset_id');

                                $monthly_query 	= $this->db->select('SUM( CASE WHEN ( sa.status = "Not due" || sa.status = "Not started" || sa.status = "Assigned" ) THEN 1 ELSE 0 END ) AS `not_due`,
									SUM( CASE WHEN ( sa.status = "In Progress" ) THEN 1 ELSE 0 END ) AS `in_progress`,
									SUM( CASE WHEN ( sa.status = "Completed" ) THEN 1 ELSE 0 END ) AS `completed`,
									SUM( CASE WHEN ( sa.status = "Failed" ) THEN 1 ELSE 0 END ) AS `failed`,
									SUM( CASE WHEN ( sa.activity_id > 0 ) THEN 1 ELSE 0 END ) AS `total`', false)
                                        ->where('sa.account_id', $account_id)
                                        ->where('sa.due_date >= "'.$first_day_of_month.'" ')
                                        ->where('sa.due_date <= "'.$last_day_of_month.'" ')
                                        ->where_in('sa.asset_id', $asset_ids)
                                        ->get('schedule_activities sa');

                                if ($monthly_query->num_rows() > 0) {
                                    if ($this->ion_auth->_current_user->id == 1) {
                                        debug($category_icons);
                                    }

                                    $category_group = strtolower($row->category_group);
                                    $monthly_stats 	= $monthly_query->result()[0];
                                    $category_data[$row->category_id] = [
                                        'category_id'		=> $row->category_id,
                                        'category_name'		=> $row->category_name,
                                        'category_icon' 	=> (!empty($category_icons->{$category_group})) ? $category_icons->{$category_group}->category_icon : 'assets/images/dashboard-icons/schedules-generic.png',
                                        'category_group'	=> $row->category_group,
                                        'period_range'		=> 'monthly',
                                        'total_in_progress' => !empty($monthly_stats->in_progress) ? $monthly_stats->in_progress : 0,
                                        'total_completed' 	=> !empty($monthly_stats->completed) ? $monthly_stats->completed : 0,
                                        'total_not_due' 	=> !empty($monthly_stats->not_due) ? $monthly_stats->not_due : 0,
                                        'total_failed' 		=> !empty($monthly_stats->failed) ? $monthly_stats->failed : 0,
                                        'total_activities' 	=> !empty($monthly_stats->total) ? $monthly_stats->total : 0
                                    ];
                                }
                            }
                        }
                    }

                    $stats_data['stats'] = !empty($category_data) ? $category_data : false;

                    break;

                case 'annual_schedules':

                    $category_icons		= category_icons();

                    $current_year_start	= !empty($where['year_start']) ? date('Y-m-d', strtotime($where['year_start'])) : date('Y-m-d', strtotime('Jan 01'));
                    $current_year_end 	= !empty($where['year_end']) ? date('Y-m-d', strtotime($where['year_end'])) : date('Y-m-d', strtotime('Dec 31'));


                    $category  		= $this->db->select('category_id, category_name, category_name_alt, category_group, description')->order_by('category_name')->get_where('audit_categories', [ 'account_id'=>$account_id, 'is_active'=>1  ]);
                    $category_data	= [];
                    if ($category->num_rows() > 0) {
                        foreach ($category->result() as $k => $row) {
                            if (!empty($site_id)) {
                                if (is_array($site_id)) {
                                    $this->db->where_in('asset.site_id', $site_id);
                                } else {
                                    $this->db->where('asset.site_id', $site_id);
                                }
                            }

                            $category_assets = $this->db->select('asset_id', false)
                                ->join('asset_types', 'asset_types.asset_type_id = asset.asset_type_id', 'left')
                                ->where('asset_types.category_id', $row->category_id)
                                ->where('asset.account_id', $account_id)
                                ->where('asset.archived !=', 1)
                                ->order_by('asset_types.asset_type, asset.asset_id')
                                ->get('asset');

                            if ($category_assets->num_rows() > 0) {
                                $asset_ids = array_column($category_assets->result_array(), 'asset_id');

                                $weekly_query 	= $this->db->select('SUM( CASE WHEN ( sa.status = "Not due" || sa.status = "Not started" || sa.status = "Assigned" ) THEN 1 ELSE 0 END ) AS `not_due`,
									SUM( CASE WHEN ( sa.status = "In Progress" ) THEN 1 ELSE 0 END ) AS `in_progress`,
									SUM( CASE WHEN ( sa.status = "Completed" ) THEN 1 ELSE 0 END ) AS `completed`,
									SUM( CASE WHEN ( sa.status = "Failed" ) THEN 1 ELSE 0 END ) AS `failed`,
									SUM( CASE WHEN ( sa.activity_id > 0 ) THEN 1 ELSE 0 END ) AS `total`', false)
                                        ->where('sa.account_id', $account_id)
                                        ->where('sa.due_date >= "'.$current_year_start.'" ')
                                        ->where('sa.due_date <= "'.$current_year_end.'" ')
                                        ->where_in('sa.asset_id', $asset_ids)
                                        ->get('schedule_activities sa');

                                if ($weekly_query->num_rows() > 0) {
                                    $category_group = strtolower($row->category_group);
                                    $weekly_stats 	= $weekly_query->result()[0];
                                    $category_data[$row->category_id] = [
                                        'category_id'		=> $row->category_id,
                                        'category_name'		=> $row->category_name,
                                        'category_icon' 	=> (!empty($category_icons->{$category_group})) ? $category_icons->{$category_group}->category_icon : 'assets/images/dashboard-icons/schedules-generic.png',
                                        'category_group'	=> $row->category_group,
                                        'period_range'		=> 'annual',
                                        'total_in_progress' => !empty($weekly_stats->in_progress) ? $weekly_stats->in_progress : '0',
                                        'total_completed' 	=> !empty($weekly_stats->completed) ? $weekly_stats->completed : '0',
                                        'total_not_due' 	=> !empty($weekly_stats->not_due) ? $weekly_stats->not_due : '0',
                                        'total_failed' 		=> !empty($weekly_stats->failed) ? $weekly_stats->failed : '0',
                                        'total_activities' 	=> !empty($weekly_stats->total) ? $weekly_stats->total : '0'
                                    ];
                                }
                            }
                        }
                    }

                    $stats_data['stats'] = !empty($category_data) ? $category_data : false;

                    break;
            }

            if (!empty($stats_data)) {
                $result = $stats_data;
                $this->session->set_flashdata('message', 'Schedules Activities stats data found');
            } else {
                $this->session->set_flashdata('message', 'Schedules Activities stats data not available');
            }

            return $result;
        }
    }
}
