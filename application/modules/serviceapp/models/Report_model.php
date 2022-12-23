<?php

namespace Application\Service\Models;

use System\Core\CI_Model;

class Report_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $section       = explode("/", $_SERVER["SCRIPT_NAME"]);
        $this->app_root = $_SERVER["DOCUMENT_ROOT"] . "/" . $section[1] . "/";
        $this->app_root = str_replace('/index.php', '', $this->app_root);
        $this->load->library('upload');
        $this->load->model('serviceapp/Settings_model', 'settings_service');
    }

    private $exempt_columns = [ 'connected_devices', 'archived', 'archived', 'is_active', 'active' ];

    /*
    *   Get Report records by account ID or Site ID or both
    */
    public function get_reports($account_id = false, $report_type = false, $postdata = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id) && !empty($report_type) && !empty($postdata)) {
            $postdata = ( !is_array($postdata) ) ? json_decode($postdata) : $postdata;
            $postdata = ( is_object($postdata) ) ? object_to_array($postdata) : $postdata;

            $statuses   = ( !empty($postdata['statuses']) ) ? ( ( is_array($postdata['statuses']) ) ? $postdata['statuses'] : json_decode(urldecode($postdata['statuses']), true) ) : [];
            $select     = ( !empty($postdata['report']) ) ? ( is_array($postdata['report']) ? $postdata['report'] : json_decode($postdata['report'], true) ) : $report_type . '*';

            $req_source = ( !empty($postdata['request_source']) ) ? $postdata['request_source'] : null;
            switch ($report_type) {
                case 'site':
                    $default_cols = implode(', ', array_keys($this->_get_table_columns('site')));

                    $columns = ( !empty($select[$report_type]['columns']) ) ? implode(', ', $select[$report_type]['columns']) : $default_cols;

                    if (strpos($columns, 'site.site_address_id') !== false) {
                        $columns = str_replace('site.site_address_id,', '', $columns);
                        $columns .= ', site_address.addressline1 as `address_line_1`';
                        $columns .= ', site_address.addressline2 as `address_line_2`';
                        $columns .= ', site_address.addressline3 as `address_line_3`';
                        $columns .= ', site_address.addressline4 as `address_line_4`';
                        $columns .= ', site_address.postcode';
                        $columns .= ', site_address.posttown';
                        $columns .= ', site_territory_name.country `site_territory_name`';
                    }
                    $this->db->join('site_address', 'site_address.address_id = site.site_address_id', 'left');
                    $this->db->join('content_territory `site_territory_name`', 'site_territory_name.territory_id = site_address.site_territory_id', 'left');

                    if (strpos($columns, 'site.time_zone_id') !== false) {
                        $columns = str_replace('site.time_zone_id,', '', $columns);
                        $columns .= ', time_zone.tz_db_name as `time_zone`';
                    }
                    $this->db->join('time_zones `time_zone`', 'site.time_zone_id = time_zone.time_zone_id', 'left');

                    if (strpos($columns, 'site.operating_company_id') !== false) {
                        $columns = str_replace('site.operating_company_id,', '', $columns);
                        $columns .= ', site_operating_company.brand_name `operating_company_brand_name`';
                        $columns .= ', site_operating_company.hotel_group `operating_company_hotel_group`';
                        $columns .= ', site_operating_company.brand_address_line1 `operating_company_address_line1`';
                        $columns .= ', site_operating_company.brand_address_line2 `operating_company_address_line2`';
                        $columns .= ', site_operating_company.brand_address_line3 `operating_company_address_line3`';
                        $columns .= ', site_operating_company.brand_address_line4 `operating_company_address_line4`';
                        $columns .= ', site_operating_company.contact_email `operating_company_contact_email`';
                        $columns .= ', site_operating_company.contact_phone `operating_company_contact_phone`';
                    }

                    $this->db->join('site_operating_company', 'site_operating_company.company_id = site.operating_company_id', 'left');

                    if (strpos($columns, 'site.system_type_id') !== false) {
                        $columns = str_replace('site.system_type_id,', '', $columns);
                        $columns .= ', product_system_type.name as `system_type_name`';
/*                      $columns .= ', product_system_type.system_reference_code';
                        $columns .= ', if ( product_system_type.is_local_server = 1, "yes", "no" ) as `is_local_server`';
                        $columns .= ', drm_type.drm_type_name';
                        $columns .= ', if ( product_system_type.is_approved_by_provider = 1, "yes", "no" ) as `is_approved_by_provider`';
                        $columns .= ', content_provider.provider_name';
                        $columns .= ', product_system_type.approval_date';
                        $columns .= ', product_system_type.tdd_link';
                        $columns .= ', delivery_mechanism.setting_value `delivery_mechanism_name`'; */
                    }

                    $this->db->join('product_system_type', 'site.system_type_id = product_system_type.system_type_id', 'left');
/*                  $this->db->join( 'drm_type', 'product_system_type.drm_type_id = drm_type.drm_type_id', 'left' );
                    $this->db->join( 'content_provider', 'product_system_type.provider_id = content_provider.provider_id', 'left' );
                    $this->db->join( 'setting `delivery_mechanism`', 'product_system_type.delivery_mechanism_id = delivery_mechanism.setting_id', 'left' ); */

                    if (strpos($columns, 'site.status_id') !== false) {
                        $columns = str_replace('site.status_id,', '', $columns);
                        $columns .= ", site_status.status_name `site_status_name`";
                    }

                    $this->db->join('site_status', 'site.status_id = site_status.status_id', 'left');

                    if (strpos($columns, 'site.charge_frequency_id') !== false) {
                        $columns = str_replace('site.charge_frequency_id,', '', $columns);
                        $columns .= ', setting.setting_value as `charge_frequency`';
                    }
                    $this->db->join('setting', 'site.charge_frequency_id = setting.setting_id', 'left');

                    if (strpos($columns, 'site.content_territory_id') !== false) {
                        $columns = str_replace('site.content_territory_id,', '', $columns);
                        $columns .= ", content_territory_name.country `content_territory_name`";
                    }
                    $this->db->join('content_territory `content_territory_name`', 'content_territory_name.territory_id = site_address.site_territory_id', 'left');

                    if (strpos($columns, 'site.invoice_currency_id') !== false) {
                        $columns = str_replace('site.invoice_currency_id,', '', $columns);
                        $columns .= ', invoice_currency.setting_value as `invoice_currency`';
                    }
                    $this->db->join('setting `invoice_currency`', 'site.invoice_currency_id = invoice_currency.setting_id', 'left');

                    if (strpos($columns, 'site.distribution_group_id') !== false) {
                        $columns = str_replace('site.distribution_group_id,', '', $columns);
                        $columns .= ', distribution_group.setting_value as `distribution_group`';
                    }
                    $this->db->join('setting `distribution_group`', 'site.distribution_group_id = distribution_group.setting_id', 'left');

                    if (strpos($columns, 'site.system_integrator_id') !== false) {
                        $columns = str_replace('site.system_integrator_id,', '', $columns);
                        $columns .= ', system_integrator.integrator_name as `system_integrator_name`';
                    }
                    $this->db->join('integrator `system_integrator`', 'site.system_integrator_id = system_integrator.system_integrator_id', 'left');

                    if (strpos($columns, 'site.created_by') !== false) {
                        $columns = str_replace('site.created_by,', '', $columns);
                        $columns .= ', concat( user.first_name," ",user.last_name ) as `created_by_full_name`';
                    }
                    $this->db->join('user', 'user.id = site.created_by', 'left');

                    if (strpos($columns, 'site.last_modified_by') !== false) {
                        $columns = str_replace('site.last_modified_by,', '', $columns);
                        $columns .= ', concat( user2.first_name," ",user2.last_name ) as `last_modified_full_name`';
                    }
                    $this->db->join('user `user2`', 'user2.id = site.last_modified_by', 'left');


                    $table  = 'site';
                    $this->db->select($columns, false);

                    $arch_where = "( site.archived != 1 or site.archived is NULL )";
                    $this->db->where($arch_where);

                    $this->db->where('site.account_id', $account_id);
                    $this->db->order_by('site.site_name ASC');
                    $report_name = 'Site Details Report';
                    break;

                case 'product':
                    $default_cols = implode(', ', array_keys($this->_get_table_columns('product')));

                    $columns = ( !empty($select[$report_type]['columns']) ) ? implode(', ', $select[$report_type]['columns']) : $default_cols;


                    if (strpos($columns, 'product.is_content_ftg') !== false) {
                        $columns .= ', if ( product.is_content_ftg = 1, "yes", "no" ) as `is_content_ftg`';
                    }

                    if (strpos($columns, 'product.is_airtime_ftg') !== false) {
                        $columns .= ', if ( product.is_airtime_ftg = 1, "yes", "no" ) as `is_airtime_ftg`';
                    }

                    if (strpos($columns, 'product.is_adult_active') !== false) {
                        $columns .= ', if ( product.is_adult_active = 1, "yes", "no" ) as `is_adult_active`';
                    }

                    if (strpos($columns, 'product.product_type_id') !== false) {
                        $columns = str_replace('product.product_type_id,', '', $columns);
                        $columns .= ', product_type.setting_value as `product_type`';
                    }
                    $this->db->join('setting `product_type`', 'product.product_type_id = product_type.setting_id', 'left');

                    if (strpos($columns, 'product.content_provider_id') !== false) {
                        $columns = str_replace('product.content_provider_id,', '', $columns);
                        $columns .= ', content_provider.provider_name';
                    }
                    $this->db->join('content_provider', 'product.content_provider_id = content_provider.provider_id', 'left');


                    if (strpos($columns, 'product.delivery_mechanism_id') !== false) {
                        $columns = str_replace('product.delivery_mechanism_id,', '', $columns);
                        $columns .= ', delivery_mechanism.setting_value as `delivery_mechanism`';
                    }
                    $this->db->join('setting `delivery_mechanism`', 'product.delivery_mechanism_id = delivery_mechanism.setting_id', 'left');


                    if (strpos($columns, 'product.sale_currency_id') !== false) {
                        $columns = str_replace('product.sale_currency_id,', '', $columns);
                        $columns .= ', sale_currency.setting_value as `sale_currency`';
                    }
                    $this->db->join('setting `sale_currency`', 'product.sale_currency_id = sale_currency.setting_id', 'left');

                    if (strpos($columns, 'product.airtime_plan_id') !== false) {
                        $columns = str_replace('product.airtime_plan_id,', '', $columns);
                        $columns .= ', airtime_plan.setting_value as `airtime_plan`';
                    }
                    $this->db->join('setting `airtime_plan`', 'product.airtime_plan_id = airtime_plan.setting_id', 'left');

                    if (strpos($columns, 'product.created_by') !== false) {
                        $columns = str_replace('product.created_by,', '', $columns);
                        $columns .= ', concat( user.first_name, " ", user.last_name ) as `created_by_full_name`';
                    }
                    $this->db->join('user', 'user.id = product.created_by', 'left');

                    if (strpos($columns, 'product.last_modified_by') !== false) {
                        $columns = str_replace('product.last_modified_by,', '', $columns);
                        $columns .= ', concat( user2.first_name," ",user2.last_name ) as `last_modified_by_full_name`';
                    }
                    $this->db->join('user user2', 'user2.id = product.last_modified_by', 'left');

                    $table  = 'product';
                    $this->db->select($columns, false);

                    $this->db->where('product.account_id', $account_id);

                    $this->db->where('product.active', 1);


                    $arch_where = "( product.archived != 1 or product.archived is NULL )";
                    $this->db->where($arch_where);

                    $this->db->order_by('product.site_id ASC, product.product_name ASC');
                    $report_name = 'Product Details Report';
                    break;


                case 'content':
                    $default_cols = implode(', ', array_keys($this->_get_table_columns('content')));

                    $columns = ( !empty($select[$report_type]['columns']) ) ? implode(', ', $select[$report_type]['columns']) : $default_cols;

                    if (strpos($columns, 'content.content_provider_id') !== false) {
                        $columns = str_replace('content.content_provider_id,', '', $columns);
                        $columns .= ', content_provider.provider_name';
                    }
                    $this->db->join('content_provider', 'content.content_provider_id = content_provider.provider_id', 'left');

                    if (strpos($columns, 'content.is_uip_nominated') !== false) {
                        $columns .= ', if ( content.is_uip_nominated = 1, "yes", "no" ) as `is_uip_nominated`';
                    }

                    if (strpos($columns, 'content.is_content_active') !== false) {
                        $columns .= ', if ( content.is_content_active = 1, "yes", "no" ) as `is_content_active`';
                    }

                    if (strpos($columns, 'content.created_by') !== false) {
                        $columns = str_replace('content.created_by,', '', $columns);
                        $columns .= ', concat( user.first_name, " ", user.last_name ) as `created_by_full_name`';
                    }
                    $this->db->join('user', 'user.id = content.created_by', 'left');

                    if (strpos($columns, 'content.modified_by') !== false) {
                        $columns = str_replace('content.modified_by,', '', $columns);
                        $columns .= ', concat( user2.first_name," ",user2.last_name ) as `last_modified_by_full_name`';
                    }
                    $this->db->join('user user2', 'user2.id = content.modified_by', 'left');

                    $table  = 'content';
                    $this->db->select($columns, false);

                    $arch_where = "( content.archived != 1 or content.archived is NULL )";
                    $this->db->where($arch_where);

                    $this->db->where('content.account_id', $account_id);

                    $this->db->where('content.active', 1);

                    $this->db->order_by('content.content_provider_id ASC, content.content_id ASC');
                    $report_name = 'Content Details Report';
                    break;

                case 'content_film':
                    $default_cols   = implode(', ', array_keys($this->_get_table_columns('content_film')));

                    $columns        = ( !empty($select[$report_type]['columns']) ) ? implode(', ', $select[$report_type]['columns']) : $default_cols;

                    if (strpos($columns, 'content_film.age_rating_id') !== false) {
                        $columns = str_replace('content_film.age_rating_id,', '', $columns);
                        $columns .= ', age_rating.age_rating_name';
                    }
                    $this->db->join('age_rating', 'age_rating.age_rating_id = content_film.age_rating_id', 'left');

                    if (strpos($columns, 'content_film.created_by') !== false) {
                        $columns = str_replace('content_film.created_by,', '', $columns);
                        $columns .= ', concat( user.first_name, " ", user.last_name ) as `created_by_full_name`';
                    }
                    $this->db->join('user', 'user.id = content_film.created_by', 'left');

                    if (strpos($columns, 'content_film.modified_by') !== false) {
                        $columns = str_replace('content_film.modified_by,', '', $columns);
                        $columns .= ', concat( user2.first_name," ",user2.last_name ) as `last_modified_by_full_name`';
                    }
                    $this->db->join('user user2', 'user2.id = content_film.modified_by', 'left');

                    $table  = 'content_film';
                    $this->db->select($columns, false);

                    $arch_where = "( content_film.archived != 1 or content_film.archived is NULL )";
                    $this->db->where($arch_where);

                    $this->db->where('content_film.account_id', $account_id);

                    $this->db->where('content_film.active', 1);

                    $this->db->order_by('content_film.content_id ASC, content_film.title ASC');
                    $report_name = 'Content Film Details Report';
                    break;


                case 'provider':
                    $default_cols   = implode(', ', array_keys($this->_get_table_columns('content_provider')));

                    $columns        = ( !empty($select[$report_type]['columns']) ) ? implode(', ', $select[$report_type]['columns']) : $default_cols;

                    if (strpos($columns, 'content_provider.content_provider_category_id') !== false) {
                        $columns = str_replace('content_provider.content_provider_category_id,', '', $columns);
                        $columns .= ', content_provider_category.provider_category_name';
                    }
                    $this->db->join('content_provider_category', 'content_provider.content_provider_category_id = content_provider_category.category_id', 'left');

                    if (strpos($columns, 'content_provider.content_provider_territory_id') !== false) {
                        $columns = str_replace('content_provider.content_provider_territory_id,', '', $columns);
                        $columns .= ', content_territory.country';
                    }
                    $this->db->join('content_territory', 'content_provider.content_provider_territory_id = content_territory.territory_id', 'left');

                    if (strpos($columns, 'content_provider.created_by') !== false) {
                        $columns = str_replace('content_provider.created_by,', '', $columns);
                        $columns .= ', concat( user.first_name, " ", user.last_name ) as `created_by_full_name`';
                    }
                    $this->db->join('user', 'user.id = content_provider.created_by', 'left');

                    if (strpos($columns, 'content_provider.modified_by') !== false) {
                        $columns = str_replace('content_provider.modified_by,', '', $columns);
                        $columns .= ', concat( user2.first_name," ",user2.last_name ) as `last_modified_by_full_name`';
                    }
                    $this->db->join('user user2', 'user2.id = content_provider.modified_by', 'left');

                    $table  = 'content_provider';
                    $this->db->select($columns, false);

                    $arch_where = "( content_provider.archived != 1 or content_provider.archived is NULL )";
                    $this->db->where($arch_where);

                    $this->db->where('content_provider.account_id', $account_id);

                    $this->db->where('content_provider.active', 1);

                    $this->db->order_by('content_provider.provider_name ASC');
                    $report_name = 'Provider Details Report';
                    break;

                case 'system':
                    $default_cols   = implode(', ', array_keys($this->_get_table_columns('product_system_type')));

                    $columns        = ( !empty($select[$report_type]['columns']) ) ? implode(', ', $select[$report_type]['columns']) : $default_cols;

                    if (strpos($columns, 'product_system_type.is_local_server') !== false) {
                        $columns .= ', if ( product_system_type.is_local_server = 1, "yes", "no" ) as `is_local_server`';
                    }

                    if (strpos($columns, 'product_system_type.drm_type_id') !== false) {
                        $columns = str_replace('product_system_type.drm_type_id,', '', $columns);
                        $columns .= ', drm_type.drm_type_name';
                    }
                    $this->db->join('drm_type', 'product_system_type.drm_type_id = drm_type.drm_type_id', 'left');

                    if (strpos($columns, 'product_system_type.is_approved_by_provider') !== false) {
                        $columns .= ', if ( product_system_type.is_approved_by_provider = 1, "yes", "no" ) as `is_approved_by_provider`';
                    }

                    if (strpos($columns, 'product_system_type.provider_id') !== false) {
                        $columns = str_replace('product_system_type.provider_id,', '', $columns);
                        $columns .= ', content_provider.provider_name';
                    }
                    $this->db->join('content_provider', 'product_system_type.provider_id = content_provider.provider_id', 'left');

                    if (strpos($columns, 'product_system_type.delivery_mechanism_id') !== false) {
                        $columns = str_replace('product_system_type.delivery_mechanism_id,', '', $columns);
                        $columns .= ', delivery_mechanism.setting_value';
                    }
                    $this->db->join('setting `delivery_mechanism`', 'product_system_type.delivery_mechanism_id = delivery_mechanism.setting_id', 'left');

                    if (strpos($columns, 'product_system_type.created_by') !== false) {
                        $columns = str_replace('product_system_type.created_by,', '', $columns);
                        $columns .= ', concat( user.first_name, " ", user.last_name ) as `created_by_full_name`';
                    }
                    $this->db->join('user', 'user.id = product_system_type.created_by', 'left');

                    if (strpos($columns, 'product_system_type.last_modified_by') !== false) {
                        $columns = str_replace('product_system_type.last_modified_by,', '', $columns);
                        $columns .= ', concat( user2.first_name, " ", user2.last_name ) as `last_modified_by_full_name`';
                    }
                    $this->db->join('user user2', 'user2.id = product_system_type.last_modified_by', 'left');

                    $table  = 'product_system_type';
                    $this->db->select($columns, false);

                    $arch_where = "( product_system_type.archived != 1 or product_system_type.archived is NULL )";
                    $this->db->where($arch_where);

                    $this->db->where('product_system_type.account_id', $account_id);

                    $this->db->where('product_system_type.active', 1);

                    $this->db->order_by('product_system_type.name ASC');
                    $report_name = 'System Type Details Report';
                    break;

                case 'integrator':
                    $default_cols   = implode(', ', array_keys($this->_get_table_columns('integrator')));

                    $columns        = ( !empty($select[$report_type]['columns']) ) ? implode(', ', $select[$report_type]['columns']) : $default_cols;

                    if (strpos($columns, 'integrator.invoice_currency_id') !== false) {
                        $columns = str_replace('integrator.invoice_currency_id,', '', $columns);
                        $columns .= ', invoice_currency.setting_value as `invoice_currency`';
                    }
                    $this->db->join('setting `invoice_currency`', 'integrator.invoice_currency_id = invoice_currency.setting_id', 'left');

                    if (strpos($columns, 'integrator.created_by') !== false) {
                        $columns = str_replace('integrator.created_by,', '', $columns);
                        $columns .= ', concat( user.first_name, " ", user.last_name ) as `created_by_full_name`';
                    }
                    $this->db->join('user', 'user.id = integrator.created_by', 'left');

                    if (strpos($columns, 'integrator.modified_by') !== false) {
                        $columns = str_replace('integrator.modified_by,', '', $columns);
                        $columns .= ', concat( user2.first_name," ",user2.last_name ) as `last_modified_by_full_name`';
                    }
                    $this->db->join('user user2', 'user2.id = integrator.modified_by', 'left');

                    $columns .= ', addressline1 `integrator_addressline1`, addressline2 `integrator_addressline2`, addressline3 `integrator_addressline3`, addressline4 `integrator_addressline4`, postcode `integrator_postcode`, posttown `integrator_posttown`';
                    $this->db->join('integrator_address', 'integrator_address.integrator_id = integrator.system_integrator_id', 'left');

                    $columns .= ',integrator_territory.country';
                    $this->db->join('content_territory `integrator_territory`', 'integrator_territory.territory_id = integrator_address.integrator_territory_id', 'left');

                    $columns .= ',address_types.address_type';
                    $this->db->join('address_types', 'address_types.address_type_id = integrator_address.address_type_id', 'left');

                    $table  = 'integrator';
                    $this->db->select($columns, false);

                    $arch_where = "( integrator.archived != 1 or integrator.archived is NULL )";
                    $this->db->where($arch_where);

                    $this->db->where('integrator.account_id', $account_id);

                    $this->db->where('integrator.active', 1);

                    $this->db->order_by('integrator.integrator_name ASC');
                    $report_name = 'Integrator Details Report';
                    break;
            }

            $this->db->limit($limit, $offset);

            $query = $this->db->get($table);

            if ($query->num_rows() > 0) {
                $document_path = '_report_downloads/' . $account_id . '/';
                $upload_path   = $this->app_root . $document_path;

                if (!is_dir($upload_path)) {
                    if (!mkdir($upload_path, 0755, true)) {
                        $this->session->set_flashdata('message', 'Error: Unable to create upload location');
                        return false;
                    }
                }

                $result = $query->result_array();

                $headers = explode(', ', ucwords(str_replace('_', ' ', implode(', ', array_keys($result[0])))));

                $data   = array_to_csv($result, $headers);

                $file_name      = $report_name . ' - ' . date('dmYHi') . '.csv';
                $file_path      = $upload_path . $file_name;

                if (write_file($upload_path . $file_name, $data)) {
                    // if( $req_source == 'web-client' ){
                        // force_download( $report_name, file_get_contents( $file_path ) );
                    // }else{
                    $result = [
                        'timestamp' => date('d.m.Y H:i:s'),
                        'expires_at' => date('d.m.Y H:i:s', strtotime('+1 hour')),
                        'file_name' => $file_name,
                        'file_path' => $file_path,
                        'file_link' => base_url($document_path . $file_name)
                    ];
                    //}
                }

                $this->session->set_flashdata('message', 'Report data found');
            } else {
                $this->session->set_flashdata('message', 'Report data not found');
            }
        } else {
            $this->session->set_flashdata('message', 'Main Account ID is required');
        }
        return $result;
    }

    /**
    * Get Report type setup
    */
    public function get_report_types_setup($account_id = false, $report_type = false)
    {

        $result = false;

        if ($report_type) {
/*          $report_types = [
                $report_type =>[
                    'report_type'=>'Site Details',
                    'table_name'=>$report_type,
                    'table_cols'=>$this->_get_table_columns( $report_type ),
                ]
            ]; */
        } else {
            $report_types = [
                'site' => [
                    'report_type'       => 'Sites',
                    'table_name'        => 'site',
                    'table_cols'        => $this->_get_table_columns('site'),
                    'date_filters'      => null,
                    'status_filters'    => []
                    /*'1'=>'Returned',
                    '2'=>'Not Assigned',
                    '3'=>'Garage',
                    '4'=>'Off-Hired' */
                ],
                'product' => [
                    'report_type'       => 'Product',
                    'table_name'        => 'product',
                    'table_cols'        => $this->_get_table_columns('product'),
                    'date_filters'      => [],
                    'status_filters'    => []
                ],
                'content' => [
                    'report_type'       => 'Content',
                    'table_name'        => 'content',
                    'table_cols'        => $this->_get_table_columns('content'),
                    'date_filters'      => [],
                    'status_filters'    => []
                ],
                'content_film' => [
                    'report_type'       => 'Content Profiles',
                    'table_name'        => 'content_film',
                    'table_cols'        => $this->_get_table_columns('content_film'),
                    'date_filters'      => [],
                    'status_filters'    => []
                ],
                'provider' => [
                    'report_type'       => 'Provider',
                    'table_name'        => 'content_provider',
                    'table_cols'        => $this->_get_table_columns('content_provider'),
                    'date_filters'      => [],
                    'status_filters'    => []
                ],
                'system' => [
                    'report_type'       => 'System',
                    'table_name'        => 'product_system_type',
                    'table_cols'        => $this->_get_table_columns('product_system_type'),
                    'date_filters'      => [],
                    'status_filters'    => []
                ],
                'integrator' => [
                    'report_type'       => 'Integrators',
                    'table_name'        => 'integrator',
                    'table_cols'        => $this->_get_table_columns('integrator'),
                    'date_filters'      => [],
                    'status_filters'    => []
                ]

            ];
        }
        $result = ( !empty($report_types) ) ? json_decode(json_encode($report_types)) : $result;
        return $result;
    }


    /*
    * Prepare table columns for reporting
    */
    private function _get_table_columns($table_name = false)
    {
        $result = [];
        if (!empty($table_name)) {
            $columns = $this->db->list_fields($table_name);
            foreach ($columns as $column) {
                if (!in_array($column, $this->exempt_columns)) {
                    $result[$table_name . '.' . $column] = ucwords(str_replace("_", " ", $column));
                }
            }
        }
        return $result;
    }


    /*
    *   Get report type(s) for the basic and for the royalty reports
    */
    public function get_report_type($account_id = false, $where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select("rt.*", false);
            $this->db->select("rc.category_name, rc.category_group");

            $this->db->join("report_category rc", "rc.category_id = rt.category_id", "left");

            if (!empty($where)) {
                $where = convert_to_array($where);
                if (!empty($where)) {
                    if (!empty($where['category_id'])) {
                        $category_id = $where['category_id'];
                        $this->db->where_in("rt.category_id", $category_id);
                        unset($where['category_id']);
                    }

                    if (!empty($where)) {
                        $this->db->where($where);
                    }
                }
            }

            $arch_where = "( rt.archived != 1 or rt.archived is NULL )";
            $this->db->where($arch_where);

            $query = $this->db->get("report_type `rt`");

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Report type(s) found');
            } else {
                $this->session->set_flashdata('message', 'Report type(s) not found');
            }
        } else {
            $this->session->set_flashdata('message', 'No required data provided');
        }

        return $result;
    }



    /*
    *   Get report category(s) for the basics for the royalty reports
    */
    public function get_report_category($account_id = false, $where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select("rc.*", false);

            $arch_where = "( rc.archived != 1 or rc.archived is NULL )";
            $this->db->where($arch_where);

            $query = $this->db->get("report_category `rc`");

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Report category(ies) found');
            } else {
                $this->session->set_flashdata('message', 'Report category(ies) not found');
            }
        } else {
            $this->session->set_flashdata('message', 'No required data provided');
        }

        return $result;
    }


    /*
    *   Get setting(s) for the basic and for the royalty reports
    */
    public function get_setting($account_id = false, $where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select("rs.*", false);
            $this->db->select("rc.category_name, rc.category_group", false);
            $this->db->select("rt.type_name, rt.type_group", false);

            $this->db->join("report_category rc", "rc.category_id = rs.category_id", "left");
            $this->db->join("report_type rt", "rt.type_id = rs.type_id", "left");

            if (!empty($where)) {
                $where = convert_to_array($where);
                if (!empty($where)) {
                    if (!empty($where['setting_id'])) {
                        $setting_id = $where['setting_id'];
                        $this->db->where_in("rs.setting_id", $setting_id);
                        unset($where['setting_id']);
                    }

                    if (!empty($where['category_id'])) {
                        $category_id = $where['category_id'];
                        $this->db->where_in("rs.category_id", $category_id);
                        unset($where['category_id']);
                    }

                    if (!empty($where['type_id'])) {
                        $type_id = $where['type_id'];
                        $this->db->where_in("rs.type_id", $type_id);
                        unset($where['type_id']);
                    }

                    if (!empty($where['provider_id'])) {
                        $provider_id = $where['provider_id'];
                        $this->db->where_in("rs.provider_id", $provider_id);
                        unset($where['provider_id']);
                    }

                    if (!empty($where)) {
                        $this->db->where($where);
                    }
                }
            }

            $arch_where = "( rs.archived != 1 or rs.archived is NULL )";
            $this->db->where($arch_where);

            $query = $this->db->get("report_setting `rs`");

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Report setting(s) found');
            } else {
                $this->session->set_flashdata('message', 'Report setting(s) not found');
            }
        } else {
            $this->session->set_flashdata('message', 'No required data provided');
        }

        return $result;
    }


    /*
    *   Get setting(s) for the basic and for the royalty reports
    */
    public function get_expected_files($account_id = false, $where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select("DISTINCT( site.site_id ), site.site_reference_code, product.product_id", false);
            $this->db->select("CONCAT( site.site_reference_code,'_', product.product_id ) as `filename`", false);

            $this->db->join("site", "site.site_id = product.site_id", "left");
            $this->db->join("site_reporting_window_month", "site_reporting_window_month.site_id = site.site_id", "left");
            $this->db->join("product_price_plan", "product_price_plan.product_id = product.product_id", "left");


            if (!empty($where)) {
                $where = convert_to_array($where);
                if (!empty($where)) {
                    if (!empty($where['provider_id'])) {
                        $provider_id = is_array($where['provider_id']) ? implode(", ", $where['provider_id']) : $where['provider_id'] ;
                        $or_where_in = "( `product`.`content_provider_id` IN('" . $provider_id . "') OR `product_price_plan`.`provider_id` IN('" . $provider_id . "') )";
                        $this->db->where($or_where_in);
                        unset($where['provider_id']);
                    }

                    if (!empty($where['month_id'])) {
                        $month_id = $where['month_id'];
                        $this->db->where_in("site_reporting_window_month.month_id", $month_id);
                        unset($where['month_id']);
                    }

                    if (!empty($where)) {
                        $this->db->where($where);
                    }
                }
            }

            $arch_where_1 = "( product.archived != 1 or product.archived is NULL )";
            $this->db->where($arch_where_1);

            $arch_where_2 = "( site.archived != 1 or site.archived is NULL )";
            $this->db->where($arch_where_2);

            $this->db->where("product.account_id", $account_id);
            $this->db->where("product.active", 1);

            $this->db->order_by("site.site_id ASC");

            $query = $this->db->get("product");

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Expected file(s) found');
            } else {
                $this->session->set_flashdata('message', 'Expected file(s) not found');
            }
        } else {
            $this->session->set_flashdata('message', 'No required data provided');
        }

        return $result;
    }



    /*
    *   Function will process provided information about uploaded viewing stats files:
    *    - save them in the separate table in the database (to show if needed*)  // * - not implemented
    *    - process to generate the first part of the report
    */
    public function process_viewing_stats($account_id = false, $provider_id = false, $month_id = false, $year = false, $viewing_stats = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($viewing_stats) && !empty($provider_id) && !empty($month_id)) {
            $viewing_stats = convert_to_array($viewing_stats);

            if (!empty($viewing_stats)) {
                $output                     = [];
                $where                      = [];
                $unrecognized_file_names    = [];
                $movies                     = [];
                $where['provider_id']       = ( !empty($provider_id) ) ? $provider_id : false ;
                $where['month_id']          = ( !empty($month_id) ) ? $month_id : false ;
                $year                       = ( !empty($year) ) ? $year : date("Y") ;
                $expected_files             = $this->get_expected_files($account_id, $where); ## I do need the where here
                $expected_files_arr         = array_column($expected_files, "filename");
                unset($where);

                if (!empty($expected_files_arr)) {
                    $i = 0;

                    ## summary at the end
                    $daily_quarantee_total      = 0;
                    $gross_receipt_total        = 0;
                    $net_receipts_total         = 0;
                    $total_due_total            = 0;
                    $total_schedule_total       = 0;


                    foreach ($viewing_stats as $key => $vs) {
                        // site_reference_from_file_name
                        $expected_document = false;
                        $expected_document = str_replace(".csv", "", $vs->document_name); // document name from the list of uploaded documents

                        // if site reference is empty - we should skip the file without the fuss - maybe remember the file name
                        if (!empty($expected_document)) {
                            // to check if the file is within the expected files array
                            if (in_array($expected_document, $expected_files_arr)) {
                                // check if we do have the site with recognized reference
                                $this->db->select("site.*");
                                $this->db->select("content_territory.country, content_territory.VAT");
                                $this->db->join("content_territory", "content_territory.territory_id = site.content_territory_id", "left");

                                // where site not archived
                                $arch_where_1 = "( site.archived != 1 or site.archived is NULL )";
                                $this->db->where($arch_where_1);

                                // where site not disabled
                                $arch_where_2 = "( site.disable_site_date = '0000-00-00' or site.disable_site_date is NULL )";
                                $this->db->where($arch_where_2);

                                $this->db->where('site.account_id', $account_id);

                                // where site is active
                                // $this->db->where( 'site.active', 1 );

                                $this->db->limit(1);

                                $site_ref           = false;
                                $expected_doc_arr   = explode("_", $expected_document);
                                $site_ref           = $expected_doc_arr[0];
                                $product_id         = $expected_doc_arr[1];

                                $site               = false;

                                if (!empty($site_ref)) {
                                    $site               = $this->db->get_where("site", ["site_reference_code" => $site_ref])->row();
                                } else {
                                    continue;
                                }

                                $this->load->model('serviceapp/Product_model', 'product_service');
                                $product            = false;
                                $product            = $this->product_service->get_product($account_id, $product_id);

                                if (!empty($site)) {
                                    // we've found a site with provided reference
                                    // now processing the file for this site
                                    $csvAsArray = [];
                                    $csvAsArray = array_map('str_getcsv', file($vs->document_link));

                                    $viewings_per_hotel = 0;
                                    $viewings_per_hotel = count(
                                        array_filter($csvAsArray, function ($x) {
                                            $array_counter = count(array_filter($x, function ($x) {
                                                return !empty($x);
                                            }));
                                            return ( $array_counter > 0 );
                                        })
                                    ) - 1;

                                    unset($csvAsArray[0]); // need conditional section for it - if first row is a title

                                    // now we're looking for the UIP Settings Values (Royalty type / Service / Unit) - to get the daily rate value
                                    // it is taken from the report_site_royalty_setting table

                                    $daily_rate_settings_exists = $this->get_site_royalty_setting($account_id, $site->site_id, ["provider_id" => $provider_id]);

                                    $daily_rate                 = 0;
                                    $percentage                 = 0;
                                    $daily_quarantee            = 0;
                                    $net_receipts               = 0;
                                    $total_net                  = 0;
                                    $plays_sum                  = 0;

                                    $total_net_for_site = 0;

                                    $days_in_month              = cal_days_in_month(CAL_GREGORIAN, ( ( !empty($month_id) ) ? $month_id : date('m') ), ( !empty($year) ) ? $year : date('Y'));
                                    $rooms                      = ( !empty($product->no_of_rooms) ) ? $product->no_of_rooms : 0 ;
                                    $territ_VAT_factor          = ( !empty($site->VAT) ) ? $site->VAT : 1 ;

                                    if (!empty($daily_rate_settings_exists)) {
                                        foreach ($daily_rate_settings_exists as $set) {
                                            if (!empty($set->type_group) && ( strtolower($set->type_group) == "minimum_guarantee" )) {
                                                $daily_rate     = ( !empty($set->setting_value) ) ? $set->setting_value : 0 ;
                                            } elseif (!empty($set->type_group) && ( strtolower($set->type_group) == "revenue_share" )) {
                                                $percentage     = ( !empty($set->setting_value) ) ? $set->setting_value : 0 ;
                                            }
                                        }
                                    }

                                    $daily_guarantee            = round($rooms * $daily_rate * $days_in_month / 100, 2);
                                    $j                          = 0;
                                    $new_array                  = [];

                                    foreach ($csvAsArray as $movie_row) {
                                        ## to skip empty rows
                                        $arr_values = count(array_filter($movie_row, function ($x) {
                                            return !empty($x);
                                        }));
                                        if ($arr_values == 0) {
                                            continue;
                                        }

                                        $new_array[$movie_row[0]]['views'][$j]['viewing_date']  = ( !empty($movie_row[2]) ) ? $movie_row[2] : '' ;
                                        $new_array[$movie_row[0]]['views'][$j]['provider']      = ( !empty($movie_row[1]) ) ? $movie_row[1] : '' ;
                                        $new_array[$movie_row[0]]['views'][$j]['title']         = ( !empty($movie_row[0]) ) ? $movie_row[0] : '' ;
                                        $new_array[$movie_row[0]]['views'][$j]['price']         = ( !empty($movie_row[3]) ) ? $movie_row[3] : '' ;
                                        $new_array[$movie_row[0]]['views'][$j]['currency']      = ( !empty($movie_row[4]) ) ? $movie_row[4] : '' ;

                                        $new_array[$movie_row[0]]['count']                      = count($new_array[$movie_row[0]]['views']);

                                        $new_array[$movie_row[0]]['daily_rate']                 = $daily_rate;
                                        $new_array[$movie_row[0]]['daily_guarantee']            = $daily_guarantee;
                                        $new_array[$movie_row[0]]['price']                      = ( !empty($new_array[$movie_row[0]]['price']) ) ? $new_array[$movie_row[0]]['price'] : ( ( !empty($movie_row[3]) ) ? $movie_row[3] : 0 ); ## price 2 guest
                                        $new_array[$movie_row[0]]['gross_receipt']              = $new_array[$movie_row[0]]['count'] * $new_array[$movie_row[0]]['price'];                                  ## plays * price 2 guest
                                        $new_array[$movie_row[0]]['percentage']                 = $percentage;
                                        $new_array[$movie_row[0]]['net_receipts']               = round($new_array[$movie_row[0]]['gross_receipt'] * $percentage / 100 / $territ_VAT_factor, 4);              ## gross * percentage
                                        $new_array[$movie_row[0]]['territ_VAT_factor']          = $territ_VAT_factor;

                                        if (!empty($new_array[$movie_row[0]]['total_net'])) {
                                            $new_array[$movie_row[0]]['total_net']              = round($new_array[$movie_row[0]]['total_net'] + ( $new_array[$movie_row[0]]['net_receipts'] / $new_array[$movie_row[0]]['count'] ), 4);
                                        } else {
                                            $new_array[$movie_row[0]]['total_net']              = $new_array[$movie_row[0]]['net_receipts'];
                                        }

                                        $total_net_for_site                                     = $total_net_for_site + round(( $new_array[$movie_row[0]]['net_receipts'] / $new_array[$movie_row[0]]['count'] ), 4);

                                        $j++;
                                    }

                                    foreach ($new_array as $title => $movie) {
                                        ## film info
                                        $film_info = false;

                                        $this->db->select("if( DATE_ADD( content_clearance.clearance_start_date, INTERVAL 1 YEAR ) > NOW(), 'current', 'library' ) as `class`, content.content_provider_reference_code", false);

                                        $this->db->join("content", "content.content_id=content_film.content_id", "left");
                                        $this->db->join("content_clearance", "content.content_id=content_clearance.content_id", "left");

                                        $this->db->where("content_film.account_id", $account_id);

                                        // temp switched off until the logic confirmed
                                        // - in theory we should get info from the movie even if the movie has been de-activated
                                        // $this->db->where( "content_film.active", 1 );

                                        $where_q = 'content_film.title = "' . ( str_replace('\\', "", $title) ) . '"' ;
                                        $this->db->where($where_q);

                                        $this->db->where("content_clearance.territory_id", $site->content_territory_id);

                                        // temp switched off until the logic confirmed
                                        // - in theory we should get info from the movie even if the movie has been archived
                                        // $arch_where = "( content_film.archived != 1 or content_film.archived is NULL )";
                                        // $this->db->where( $arch_where );

                                        $film_info = $this->db->get("content_film", 1)->row();
                                        ## film info - end

                                        ## values and initial calculations
                                        $total_due              = 0;
                                        if (( strtolower($product->product_type_name) == "airtime" ) && ( $product->is_airtime_ftg != 1 )) {
                                            $total_due = min([$total_net_for_site, $daily_guarantee]);
                                        } else {
                                            $total_due = max([$total_net_for_site, $daily_guarantee]);
                                        }

                                        $total_sched_e          = 0;
                                        $total_sched_e          = ( $movie['net_receipts'] == 0 ) ? round(( $movie['count'] * $total_due / $viewings_per_hotel ), 4) : round(( $movie['net_receipts'] * $total_due / $total_net_for_site ), 4);
                                        ## values and initial calculations - end

                                        ## setting the product type
                                        $product_type = "Guest To Pay";
                                        if (!empty($product->product_type_name) && ( strtolower($product->product_type_name) == "airtime" )) {
                                            if (!empty($product->is_airtime_ftg) && ( $product->is_airtime_ftg == 1 )) {
                                                $product_type = "Airtime Free to Guest";
                                            } else {
                                                $product_type = "Airtime Guest To Pay";
                                            }
                                        } else {
                                            if (!empty($product->is_content_ftg) && ( $product->is_content_ftg == 1 )) {
                                                $product_type = "Free to Guest";
                                            }
                                        }
                                        ## setting the product type - end

                                        ## preparing totals
                                        // $daily_quarantee_total   = 0;
                                        $gross_receipt_total        = $gross_receipt_total + $movie['gross_receipt'];
                                        $net_receipts_total         = $net_receipts_total + $movie['net_receipts'];
                                        // $total_due_total         = 0;
                                        $total_schedule_total       = $total_schedule_total + $total_sched_e;
                                        ## preparing totals - end

                                        $output[$i] = [
                                            "territory"         => '' ,
                                            "site"              => '' ,
                                            "product_name"      => '',
                                            "rooms"             => '',
                                            "plays"             => $movie['count'],
                                            "title"             => $title,
                                            "daily_rate"        => '',
                                            "days"              => '',
                                            "daily_guarantee"   => '',
                                            "price_to_guest"    => ( !empty($movie['price']) ) ? $movie['price'] : 0 ,
                                            "gross_receipt"     => $movie['gross_receipt'],
                                            "percentage"        => $movie['percentage'],
                                            "net_receipts"      => $movie['net_receipts'],
                                            "VAT"               => $territ_VAT_factor,
                                            "total_net"         => '',
                                            "total_due"         => '',
                                            "total_sched_e"     => $total_sched_e,
                                            "title_start_date"  => 0,
                                            "title_end_date"    => 0,
                                            "class"             => ( !empty($film_info->class) ) ? ucwords($film_info->class) : '' ,
                                            "product_type"      => $product_type,
                                        ];

                                        $movies[$title]['total_sched_e']    = ( !empty($movies[$title]['total_sched_e']) ) ? $movies[$title]['total_sched_e'] + $total_sched_e : $total_sched_e ;
                                        $movies[$title]['count']            = ( !empty($movies[$title]['count']) ) ? $movies[$title]['count'] + 1 : 1 ;
                                        $movies[$title]['picture_number']   = ( !empty($movies[$title]['picture_number']) ) ? $movies[$title]['picture_number'] : ( ( !empty($film_info) ? ( !empty($film_info->content_provider_reference_code) ? $film_info->content_provider_reference_code : '' ) : '' ) ) ;
                                        $i++;
                                    }

                                    ## preparing totals
                                    $daily_quarantee_total      = $daily_quarantee_total + $daily_guarantee;
                                    // $gross_receipt_total     += $movie['gross_receipt'];
                                    // $net_receipts_total      += $movie['net_receipts'];
                                    $total_due_total            = $total_due_total + $total_due;
                                    // $total_schedule_total    += $total_sched_e;

                                    $output[$i] = [
                                        "territory"         => ( !empty($site->country) ) ? $site->country : '' ,
                                        "site"              => ( !empty($site->site_name) ) ? html_entity_decode(( $site->site_name ), ENT_QUOTES, 'cp1252') : '' ,
                                        "product_name"      => ( !empty($product->product_name) ) ? $product->product_name : 0 ,
                                        "rooms"             => ( !empty($product->no_of_rooms) ) ? $product->no_of_rooms : 0 ,
                                        // "plays"              => $viewings_per_hotel,
                                        "plays"             => '',
                                        "title"             => '',
                                        "daily_rate"        => $daily_rate,
                                        "days"              => $days_in_month,
                                        "daily_guarantee"   => $daily_guarantee,
                                        "price_to_guest"    => '',
                                        "gross_receipt"     => '',
                                        "percentage"        => '',
                                        "net_receipts"      => '',
                                        "VAT"               => '',
                                        "total_net"         => $total_net_for_site,
                                        "total_due"         => $total_due,
                                        "total_sched_e"     => '',
                                        "title_start_date"  => '',
                                        "title_end_date"    => '',
                                        "class"             => '',
                                        "product_type"      => '', // Airtime Guest to Pay note
                                    ];

                                    $i++;
                                } else {
                                    // site with the provided reference does not exists or is inactive
                                    // we're going to skip this guy too
                                }
                            } else {
                                // site reference code is not within the range (array)
                                $unrecognized_file_names[] = $vs->document_name;    // so, just remember
                            }
                        } else {
                            // processing of the file name wasn't successful - just skipped
                            $unrecognized_file_names[] = $vs->document_name;    // so, just remember
                        }
                        $i++;
                    }

                    ## add total sums
                    $output[$i] = [
                        "territory"         => '' ,
                        "site"              => '' ,
                        "product_name"      => '',
                        "rooms"             => '',
                        "plays"             => '',
                        "title"             => '',
                        "daily_rate"        => '',
                        "days"              => '',
                        "daily_guarantee"   => '',
                        "price_to_guest"    => '',
                        "gross_receipt"     => '',
                        "percentage"        => '',
                        "net_receipts"      => '',
                        "VAT"               => '',
                        "total_net"         => '',
                        "total_due"         => '',
                        "total_sched_e"     => '',
                        "title_start_date"  => '',
                        "title_end_date"    => '',
                        "class"             => '',
                        "product_type"      => '',
                    ];

                    ## add total sums
                    $output[++$i] = [
                        "territory"         => '' ,
                        "site"              => '' ,
                        "product_name"      => '',
                        "rooms"             => '',
                        "plays"             => '',
                        "title"             => '',
                        "daily_rate"        => '',
                        "days"              => '',
                        "daily_guarantee"   => $daily_quarantee_total,
                        "price_to_guest"    => '',
                        "gross_receipt"     => $gross_receipt_total,
                        "percentage"        => '',
                        "net_receipts"      => $net_receipts_total,
                        "VAT"               => '',
                        "total_net"         => '',
                        "total_due"         => $total_due_total,
                        "total_sched_e"     => $total_schedule_total,
                        "title_start_date"  => '',
                        "title_end_date"    => '',
                        "class"             => '',
                        "product_type"      => '',
                    ];


                    if (!empty($movies)) {
                        $schedule_E = $this->generate_schedule_E($account_id, $movies, $year, $month_id, $provider_id);
                    }

                    if (!empty($output)) {
                        $document_path = '_account_assets/accounts/' . $account_id . '/report/';
                        $upload_path   = $this->app_root . $document_path;

                        if (!is_dir($upload_path)) {
                            if (!mkdir($upload_path, 0755, true)) {
                                $this->session->set_flashdata('message', 'Error: Unable to create upload location');
                                return false;
                            }
                        }

                        $headers        = explode(', ', ucwords(str_replace('_', ' ', implode(', ', array_keys(reset($output))))));
                        $data           = array_to_csv($output, $headers);
                        $report_name    = "UIP - ScheduleA";
                        $file_name      = $report_name . ' - ' . date('YmdHis') . '.csv';
                        $file_path      = $upload_path . $file_name;

                        $output_data    = false;

                        if (write_file($upload_path . $file_name, $data)) {
                            $output_data = [
                                'account_id'            => $account_id,
                                'timestamp'             => date('d.m.Y H:i:s'),
                                'expires_at'            => date('d.m.Y H:i:s', strtotime('+1 hour')),
                                'report_category_id'    => 1, ## Royalty Report
                                'report_type_id'        => 1, ## Royalty Report by the provider
                                'provider_id'           => $provider_id,
                                'month'                 => $month_id,
                                'year'                  => $year,
                                'document_name'         => $file_name,
                                'document_location'     => $file_path,
                                'document_link'         => base_url($document_path . $file_name),
                                'document_extension'    => "application/vnd.ms-excel",
                                'file_name'             => $file_name,
                                'file_path'             => $file_path,
                                'file_link'             => base_url($document_path . $file_name),
                                'created_by'            => $this->ion_auth->_current_user->id,
                            ];

                            if (!empty($output_data)) {
                                $insert_data = $this->ssid_common->_filter_data("report", $output_data);

                                if (!empty($insert_data)) {
                                    $this->db->insert("report", $insert_data);

                                    if ($this->db->affected_rows() > 0) {
                                        $result = $output_data;
                                        $this->session->set_flashdata('message', 'Report generated');
                                    } else {
                                        $this->session->set_flashdata('message', 'Error inserting data');
                                    }
                                } else {
                                    $this->session->set_flashdata('message', 'Error filtering DB data');
                                }
                            } else {
                                $this->session->set_flashdata('message', 'Error generating the DB data');
                            }
                        } else {
                            $this->session->set_flashdata('message', 'Error writing a file');
                        }
                    } else {
                        $this->session->set_flashdata('message', 'Stats not generated');
                    }
                } else {
                    $this->session->set_flashdata('message', 'No expected files in the system');
                }
            } else {
                $this->session->set_flashdata('message', 'Error processing provided data');
            }
        } else {
            $this->session->set_flashdata('message', 'No required data provided');
        }
        return $result;
    }


    /*
    *
    */
    public function generate_schedule_E($account_id = false, $movies = false, $year = false, $month_id = false, $provider_id = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($movies)) {
            $document_path = '_account_assets/accounts/' . $account_id . '/report/';
            $upload_path   = $this->app_root . $document_path;

            if (!is_dir($upload_path)) {
                if (!mkdir($upload_path, 0755, true)) {
                    $this->session->set_flashdata('message', 'Error: Unable to create upload location');
                    return false;
                }
            }

            $period     = ( !empty($month_id) ) ? ( date('m', mktime(0, 0, 0, $month_id, 10)) ) : date('m') ;
            $month_name = ( !empty($month_id) ) ? ( date('F', mktime(0, 0, 0, $month_id, 10)) ) : date('F') ;
            $year       = ( !empty($year) ) ? $year : date('Y') ;

            $spr_title  = "Schedule E for " . $month_name . " " . $year;

            $s_output   = [];
            $s_output[] = [$spr_title, "", "", ""];
            $s_output[] = ["", "", "", ""];
            $s_output[] = ["Film Size", "7", "", ""];
            $s_output[] = ["Customer", "Techlive", "Currency", "GBP"];
            $s_output[] = ["Territory", "UK", "Year", $year];
            $s_output[] = ["Territory No.", "588", "Period", $period];
            $s_output[] = ["", "", "", ""];
            $s_output[] = ["Film Title", "", "Picture Number", "Rental"];

            foreach ((array) $movies as $title => $movie) {
                $s_output[] = [$title, "", $movie['picture_number'], $movie['total_sched_e']];
            }
            $s_output[] = ["", "", "", ""];
            $sum = array_sum(array_column($movies, "total_sched_e"));
            $s_output[] = ["", "", "Total Rental:", $sum];

            $headers        = ["", "", "", ""];
            $data           = array_to_csv($s_output, $headers);

            $report_name    = "UIP - ScheduleE";
            $file_name      = $report_name . ' - ' . date('YmdHis') . '.csv';
            $file_path      = $upload_path . $file_name;

            $output_data    = false;

            if (write_file($upload_path . $file_name, $data)) {
                $output_data = [
                    'account_id'            => $account_id,
                    'timestamp'             => date('d.m.Y H:i:s'),
                    'expires_at'            => date('d.m.Y H:i:s', strtotime('+1 hour')),
                    'report_category_id'    => 1, ## Royalty Report
                    'report_type_id'        => 1, ## Royalty Report by the provider
                    'provider_id'           => ( !empty($provider_id) ) ? $provider_id : null ,
                    'month'                 => $month_id,
                    'year'                  => $year,
                    'document_name'         => $file_name,
                    'document_location'     => $file_path,
                    'document_link'         => base_url($document_path . $file_name),
                    'document_extension'    => "application/vnd.ms-excel",
                    'file_name'             => $file_name,
                    'file_path'             => $file_path,
                    'file_link'             => base_url($document_path . $file_name),
                    'created_by'            => $this->ion_auth->_current_user->id,
                ];

                if (!empty($output_data)) {
                    $insert_data = $this->ssid_common->_filter_data("report", $output_data);

                    if (!empty($insert_data)) {
                        $this->db->insert("report", $insert_data);

                        if ($this->db->affected_rows() > 0) {
                            $result = $output_data;
                            $this->session->set_flashdata('message', 'Schedule E Report generated');
                        } else {
                            $this->session->set_flashdata('message', 'Error inserting data');
                        }
                    } else {
                        $this->session->set_flashdata('message', 'Error filtering DB data');
                    }
                } else {
                    $this->session->set_flashdata('message', 'Error generating the DB data');
                }
            } else {
                $this->session->set_flashdata('message', 'File not written');
            }
        } else {
            $this->session->set_flashdata('message', 'No required data provided');
        }
        return $result;
    }


    /*
    *   Update (Royalty) report setting(s)
    */
    public function update_report_settings($account_id = false, $dataset = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($dataset)) {
            $dataset = convert_to_array($dataset);

            if (!empty($dataset['settings'])) {
                $batch_data = $setting_ids = [];
                foreach ($dataset['settings'] as $key => $s_row) {
                    if (!empty($s_row['setting_id'])) {
                        $batch_data[$key]["setting_id"]             = $s_row['setting_id'];
                        $setting_ids[]                              = $s_row['setting_id'];

                        if (!empty($s_row['setting_name'])) {
                            $batch_data[$key]["setting_name"]       = $s_row['setting_name'];
                        };

                        // For now it is  possible to reset the value by passing an empty row.
                        // If this needs to be switched off - uncomment conditions
                        // if( !empty( $s_row['setting_value'] ) ){
                        $batch_data[$key]["setting_value"]      = $s_row['setting_value'];
                        // };

                        if (!empty($s_row['setting_name_group'])) {
                            $batch_data[$key]["setting_name_group"] = $s_row['setting_name_group'];
                        };

                        if (!empty($s_row['currency'])) {
                            $batch_data[$key]["currency"]           = $s_row['currency'];
                        };

                        if (!empty($s_row['unit'])) {
                            $batch_data[$key]["unit"]               = $s_row['unit'];
                        };

                        if (!empty($s_row['other_info'])) {
                            $batch_data[$key]["other_info"]         = $s_row['other_info'];
                        };

                        if (!empty($s_row['modified_by'])) {
                            $batch_data[$key]["modified_by"]        = $s_row['modified_by'];
                        };
                    };
                }

                if (!empty($batch_data)) {
                    $this->db->update_batch('report_setting_royalty', $batch_data, 'setting_id');
                    if ($this->db->trans_status() !== false) {
                        $arch_where = "( report_setting_royalty.archived != 1 or report_setting_royalty.archived is NULL )";
                        $this->db->where($arch_where);
                        $this->db->where("report_setting_royalty.active", 1);
                        $this->db->where_in("setting_id", $setting_ids);
                        $result = $this->db->get("report_setting_royalty");
                        $this->session->set_flashdata('message', 'Setting(s) record updated successfully');
                    } else {
                        $this->session->set_flashdata('message', 'There is an issue updating the settings');
                    }
                } else {
                    $this->session->set_flashdata('message', 'Error processing settings data');
                }
            } else {
                $this->session->set_flashdata('message', 'No setting provided');
            }
        } else {
            $this->session->set_flashdata('message', 'No required data provided');
        }

        return $result;
    }




    /*
    *   Get Royalty type(s)
    */
    public function get_royalty_type($account_id = false, $where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select("rt.*", false);

            if (!empty($where)) {
                $where = convert_to_array($where);
                if (!empty($where)) {
                    if (!empty($where['type_group'])) {
                        $type_group = $where['type_group'];
                        $this->db->where_in("rt.type_group", $type_group);
                        unset($where['type_group']);
                    }

                    if (!empty($where)) {
                        $this->db->where($where);
                    }
                }
            }

            $this->db->where("rt.active", 1);

            $arch_where = "( rt.archived != 1 or rt.archived is NULL )";
            $this->db->where($arch_where);
            $this->db->order_by("rt.type_name ASC");

            $query = $this->db->get("royalty_type `rt`");

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Royalty type(s) found');
            } else {
                $this->session->set_flashdata('message', 'Report type(s) not found');
            }
        } else {
            $this->session->set_flashdata('message', 'No required data provided');
        }

        return $result;
    }


    /*
    *   Get Royalty service(s)
    */
    public function get_royalty_service($account_id = false, $where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select("rs.*", false);

            if (!empty($where)) {
                $where = convert_to_array($where);
                if (!empty($where)) {
                    if (!empty($where['service_group'])) {
                        $service_group = $where['service_group'];
                        $this->db->where_in("rs.service_group", $service_group);
                        unset($where['service_group']);
                    }

                    if (!empty($where)) {
                        $this->db->where($where);
                    }
                }
            }

            $this->db->where("rs.active", 1);

            $arch_where = "( rs.archived != 1 or rs.archived is NULL )";
            $this->db->where($arch_where);
            $this->db->order_by("rs.service_name ASC");

            $query = $this->db->get("royalty_service `rs`");

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Royalty service(s) found');
            } else {
                $this->session->set_flashdata('message', 'Report service(s) not found');
            }
        } else {
            $this->session->set_flashdata('message', 'No required data provided');
        }

        return $result;
    }



    /*
    *   Get Royalty unit(s)
    */
    public function get_royalty_unit($account_id = false, $where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select("ru.*", false);

            if (!empty($where)) {
                $where = convert_to_array($where);
                if (!empty($where)) {
                    if (!empty($where['unit_group'])) {
                        $unit_group = $where['unit_group'];
                        $this->db->where_in("ru.unit_group", $unit_group);
                        unset($where['unit_group']);
                    }

                    if (!empty($where['unit_type'])) {
                        $unit_type = $where['unit_type'];
                        $this->db->where_in("ru.unit_type", $unit_type);
                        unset($where['unit_type']);
                    }

                    if (!empty($where)) {
                        $this->db->where($where);
                    }
                }
            }

            $this->db->where("ru.active", 1);

            $arch_where = "( ru.archived != 1 or ru.archived is NULL )";
            $this->db->where($arch_where);
            $this->db->order_by("ru.unit_name ASC");

            $query = $this->db->get("royalty_unit `ru`");

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Royalty unit(s) found');
            } else {
                $this->session->set_flashdata('message', 'Report unit(s) not found');
            }
        } else {
            $this->session->set_flashdata('message', 'No required data provided');
        }

        return $result;
    }



    /*
    *   Get Royalty Setting(s) linked with Site
    */
    public function get_site_royalty_setting($account_id = false, $site_id = false, $where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id) && !empty($site_id)) {
            $this->db->select("rsrs.*", false);
            $this->db->select("royalty_type.type_name, royalty_type.type_group", false);
            $this->db->select("royalty_service.service_name, royalty_service.service_group", false);
            // $this->db->select( "royalty_unit.unit_name, royalty_unit.unit_group, royalty_unit.unit_type", false );
            $this->db->select("report_setting_royalty.setting_value", false);

            if (!empty($where)) {
                $where = convert_to_array($where);
                if (!empty($where)) {
                    if (!empty($where['provider_id'])) {
                        $provider_id = $where['provider_id'];
                        $this->db->where_in("rsrs.provider_id", $provider_id);
                        unset($where['provider_id']);
                    }

                    if (!empty($where['royalty_type_id'])) {
                        $royalty_type_id = $where['royalty_type_id'];
                        $this->db->where_in("rsrs.royalty_type_id", $royalty_type_id);
                        unset($where['royalty_type_id']);
                    }

                    if (!empty($where['royalty_service_id'])) {
                        $royalty_service_id = $where['royalty_service_id'];
                        $this->db->where_in("rsrs.royalty_service_id", $royalty_service_id);
                        unset($where['royalty_service_id']);
                    }

                    // if( !empty( $where['royalty_unit_id'] ) ){
                        // $royalty_unit_id = $where['royalty_unit_id'];
                        // $this->db->where_in( "rsrs.royalty_unit_id", $royalty_unit_id );
                        // unset( $where['royalty_unit_id'] );
                    // }

                    if (!empty($where['report_setting_id'])) {
                        $report_setting_id = $where['report_setting_id'];
                        $this->db->where_in("rsrs.report_setting_id", $report_setting_id);
                        unset($where['report_setting_id']);
                    }

                    if (!empty($where)) {
                        $this->db->where($where);
                    }
                }
            }

            $this->db->join("royalty_type", "royalty_type.type_id=rsrs.royalty_type_id", "left");
            $this->db->join("royalty_service", "royalty_service.service_id=rsrs.royalty_service_id", "left");
            // $this->db->join( "royalty_unit", "royalty_unit.unit_id=rsrs.royalty_unit_id", "left" );
            $this->db->join("report_setting_royalty", "report_setting_royalty.setting_id=rsrs.report_setting_id", "left");

            $this->db->where_in("rsrs.site_id", $site_id);
            $this->db->where("rsrs.active", 1);

            $arch_where = "( rsrs.archived != 1 or rsrs.archived is NULL )";
            $this->db->where($arch_where);
            $this->db->order_by("rsrs.setting_id ASC");

            $query = $this->db->get("report_site_royalty_setting `rsrs`");

            if ($query->num_rows() > 0) {
                if ($query->num_rows() > 1) {
                    $result = $query->result();
                } else {
                    $result = $query->row();
                }
                $this->session->set_flashdata('message', 'Royalty unit(s) found');
            } else {
                $this->session->set_flashdata('message', 'Report unit(s) not found');
            }
        } else {
            $this->session->set_flashdata('message', 'No required data provided');
        }

        return $result;
    }



    /*
    *   Get Royalty Settings Value(s)
    */
    public function get_royalty_setting_value($account_id = false, $where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select("rsc.*", false);
            $this->db->select("rsr.setting_name, rsr.setting_name_group, rsr.setting_value, rsr.currency", false);

            if (!empty($where)) {
                $where = convert_to_array($where);
                if (!empty($where)) {
                    if (!empty($where['provider_id'])) {
                        $provider_id = $where['provider_id'];
                        $this->db->where_in("rsc.provider_id", $provider_id);
                        unset($where['provider_id']);
                    }

                    if (!empty($where['royalty_type_id'])) {
                        $royalty_type_id = $where['royalty_type_id'];
                        $this->db->where_in("rsc.royalty_type_id", $royalty_type_id);
                        unset($where['royalty_type_id']);
                    }

                    if (!empty($where['royalty_service_id'])) {
                        $royalty_service_id = $where['royalty_service_id'];
                        $this->db->where_in("rsc.royalty_service_id", $royalty_service_id);
                        unset($where['royalty_service_id']);
                    }

                    if (!empty($where['royalty_unit_id'])) {
                        $royalty_unit_id = $where['royalty_unit_id'];
                        $this->db->where_in("rsc.royalty_unit_id", $royalty_unit_id);
                        unset($where['royalty_unit_id']);
                    }

                    if (!empty($where)) {
                        $this->db->where($where);
                    }
                }
            }

            $this->db->join("report_setting_royalty `rsr`", "rsr.setting_id = rsc.setting_royalty_id", "left");

            $this->db->where("rsc.active", 1);

            $arch_where = "( rsc.archived != 1 or rsc.archived is NULL )";
            $this->db->where($arch_where);
            $this->db->order_by("rsc.combination_id ASC");

            $query = $this->db->get("royalty_setting_combined `rsc`");

            if ($query->num_rows() > 0) {
                $result = ( $query->num_rows() > 1 ) ? $query->result() : $query->row();
                $this->session->set_flashdata('message', 'Royalty setting value(s) found');
            } else {
                $this->session->set_flashdata('message', 'Report setting value(s) not found');
            }
        } else {
            $this->session->set_flashdata('message', 'No required data provided');
        }

        return $result;
    }



    /*
    *   Update (Royalty) report setting(s) against the site
    */
    public function update_site_royalty_setting($account_id = false, $dataset = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($dataset)) {
            $dataset = convert_to_array($dataset);

            if (!empty($dataset['data'])) {
                $dataset        = $dataset['data'];
                $creation_error = false;
                $update_error   = false;

                $to_create = [];
                $to_update = [];
                $get_where = [];

                if (count($dataset) == count($dataset, COUNT_RECURSIVE)) {              ## simple array
                    if (!empty($dataset['site_id']) && !empty($dataset['provider_id']) && !empty($dataset['royalty_type_id']) && !empty($dataset['royalty_service_id']) && !empty($dataset['report_setting_id'])) {
                        $get_where = [
                            "account_id"            => $account_id,
                            "site_id"               => $dataset['site_id'],
                            "provider_id"           => $dataset['provider_id'],
                        ];

                        $setting_exists = false;
                        $setting_exists = $this->db->get_where("report_site_royalty_setting", $get_where, 1, DEFAULT_OFFSET)->row();

                        if ($setting_exists) {
                            $to_update[0]                           = $get_where;
                            $to_update[0]['setting_id']             = $setting_exists->setting_id;
                            $to_update[0]['report_setting_id']      = $dataset['report_setting_id'];
                            $to_update[0]['royalty_type_id']        = $dataset['royalty_type_id'];
                            $to_update[0]['royalty_service_id']     = $dataset['royalty_service_id'];
                            $to_update[0]['last_modified_by']       = $this->ion_auth->_current_user->id;
                            if (!empty($dataset['royalty_unit_id'])) {
                                $to_update[0]['royalty_unit_id']    = $dataset['royalty_unit_id'];
                            }
                            $setting_ids[]                          = $setting_exists->setting_id;
                        } else {
                            $to_create[0]                           = $get_where;
                            $to_create[0]['report_setting_id']      = $dataset['report_setting_id'];
                            $to_create[0]['royalty_type_id']        = $dataset['royalty_type_id'];
                            $to_create[0]['royalty_service_id']     = $dataset['royalty_service_id'];
                            $to_create[0]['created_by']             = $this->ion_auth->_current_user->id;
                            if (!empty($dataset['royalty_unit_id'])) {
                                $to_create[0]['royalty_unit_id']    = $dataset['royalty_unit_id'];
                            }
                        }
                    }
                } else { ## multidimensional
                    foreach ($dataset as $key => $s_row) {
                        if (!empty($s_row['site_id']) && !empty($s_row['provider_id']) && !empty($s_row['royalty_type_id']) && !empty($s_row['royalty_service_id']) && !empty($s_row['report_setting_id'])) {
                            $get_where = [
                                "account_id"            => $account_id,
                                "site_id"               => $s_row['site_id'],
                                "provider_id"           => $s_row['provider_id'],
                                "royalty_type_id"       => $s_row['royalty_type_id'],
                            ];

                            $setting_exists = false;
                            $setting_exists = $this->db->get_where("report_site_royalty_setting", $get_where, 1, DEFAULT_OFFSET)->row();

                            if ($setting_exists) {
                                $to_update[$key]                            = $get_where;
                                $to_update[$key]['setting_id']              = $setting_exists->setting_id;
                                $to_update[$key]['report_setting_id']       = $s_row['report_setting_id'];
                                $to_update[$key]['royalty_type_id']         = $s_row['royalty_type_id'];
                                $to_update[$key]['royalty_service_id']      = $s_row['royalty_service_id'];
                                $to_update[$key]['last_modified_by']        = $this->ion_auth->_current_user->id;
                                if (!empty($s_row['royalty_unit_id'])) {
                                    $to_update[$key]['royalty_unit_id']     = $s_row['royalty_unit_id'];
                                }
                                $setting_ids[]                              = $setting_exists->setting_id;
                            } else {
                                $to_create[$key]                            = $get_where;
                                $to_create[$key]['report_setting_id']       = $s_row['report_setting_id'];
                                $to_create[$key]['royalty_type_id']         = $s_row['royalty_type_id'];
                                $to_create[$key]['royalty_service_id']      = $s_row['royalty_service_id'];
                                $to_create[$key]['created_by']              = $this->ion_auth->_current_user->id;
                                if (!empty($s_row['royalty_unit_id'])) {
                                    $to_create[$key]['royalty_unit_id']     = $s_row['royalty_unit_id'];
                                }
                            }
                        }
                    }
                }

                if (!empty($to_create)) {
                    $this->db->insert_batch('report_site_royalty_setting', $to_create);
                    if ($this->db->trans_status() !== false) {
                    } else {
                        $creation_error = true;
                    }
                }

                if (!empty($to_update)) {
                    $this->db->update_batch('report_site_royalty_setting', $to_update, 'setting_id');
                    if ($this->db->trans_status() !== false) {
                    } else {
                        $update_error = true;
                    }
                }

                if (!( $creation_error ) && !( $update_error )) {
                    $arch_where = "( report_site_royalty_setting.archived != 1 or report_site_royalty_setting.archived is NULL )";
                    $this->db->where($arch_where);
                    $this->db->where("report_site_royalty_setting.active", 1);
                    if (!empty($setting_ids)) {
                        $this->db->where_in("setting_id", $setting_ids);
                    } else {
                        $this->db->where($get_where);
                    }

                    $result = $this->db->get("report_site_royalty_setting")->result();

                    $this->session->set_flashdata('message', 'Settings updated successfully');
                } else {
                    $this->session->set_flashdata('message', 'There is an issue updating the settings');
                }
            } else {
                $this->session->set_flashdata('message', 'No setting provided');
            }
        } else {
            $this->session->set_flashdata('message', 'No required data provided');
        }

        return $result;
    }


    public function get_report($account_id = false, $where = false)
    {
        $result = false;
        if (!empty($account_id)) {
            if (!empty($where)) {
                $where = convert_to_array($where);

                if (!empty($where)) {
                    if (!empty($where['category_id'])) {
                        $report_category_id = $where['category_id'];
                        $this->db->where("report.report_category_id", $report_category_id);
                        unset($where['category_id']);
                    }

                    if (!empty($where['type_id'])) {
                        $report_type_id = $where['type_id'];
                        $this->db->where("report.report_type_id", $report_type_id);
                        unset($where['type_id']);
                    }

                    if (!empty($where['provider_id'])) {
                        $provider_id = $where['provider_id'];
                        $this->db->where("report.provider_id", $provider_id);
                        unset($where['provider_id']);
                    }

                    if (!empty($where['month'])) {
                        $month = $where['month'];
                        $this->db->where("report.month", $month);
                        unset($where['month']);
                    }

                    if (!empty($where)) {
                        $this->db->where($where);
                    }
                }

                $this->db->select("report.*", false);
                $this->db->select("report_category.category_name, report_category.category_group", false);
                $this->db->select("report_type.type_name, report_type.type_alt_title, report_type.type_group", false);
                $this->db->select("content_provider.provider_name, content_provider.provider_group", false);

                $this->db->join("report_category", "report_category.category_id = report.report_category_id", "left");
                $this->db->join("report_type", "report_type.type_id = report.report_type_id", "left");
                $this->db->join("content_provider", "content_provider.provider_id = report.provider_id", "left");

                $this->db->where("report.archived !=", 1);
                $this->db->where("report.account_id", $account_id);

                $this->db->order_by("report.date_created DESC");

                $query = $this->db->get("report");

                if ($query->num_rows() > 0) {
                    foreach ($query->result() as $row) {
                        $hour_minute = "_";
                        $hour_minute = date('Y-m-d H:i', strtotime($row->date_created));
                        $result[$hour_minute][$row->report_type_id][] = $row;
                    }

                    $this->session->set_flashdata('message', 'Report(s) found');
                } else {
                    $this->session->set_flashdata('message', 'Report(s) not found');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No required data provided');
        }

        return $result;
    }


    public function delete_report($account_id = false, $report_id = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($report_id)) {
            $arch_data = [
                "archived"          => 1,
                "last_modified_by"  => $this->ion_auth->_current_user->id,
            ];

            $query = $this->db->update("report", $arch_data, ["report_id" => $report_id]);

            if ($this->db->affected_rows() > 0) {
                $result = true;
                $this->session->set_flashdata('message', 'The Report has been archived');
            } else {
                $this->session->set_flashdata('message', 'The Report hasn\'t been archived');
            }
        } else {
            $this->session->set_flashdata('message', 'No required data provided');
        }

        return $result;
    }


    /*
    *   Get Simple Report
    *   @params:
    *    - category_group ( varchar ),
    *    - type_id ( int ),
    *    - where ( array )
    **/
    public function get_simple_report($account_id = false, $category_group = false, $type_id = false, $where = false, $limit = DEFAULT_MAX_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id) && !empty($category_group) && !empty($type_id)) {
            if (strtolower($category_group) == "basic_report") {
                $provider_id = $add_where = false;

                if (!empty($where)) {
                    $where = convert_to_array($where);

                    if (!empty($where)) {
                        if (!empty($where['provider_id'])) {
                            $provider_id = $where['provider_id'];
                            unset($where['provider_id']);
                        }

                        if (!empty($where)) {
                            $add_where = $where;
                            unset($where);
                        }
                    }
                }

                $report_name    = '';
                $category       = false;

                $restricted_columns = ["archived"];

                switch ((int) $type_id) {
                    case 11: // Invoicing
                        $table              = "site";
                        $report_name        = 'Invoicing';
                        $table_columns      = "site.site_id, site.site_name `site`, site.number_of_rooms `no_rooms`, site.disable_site_date, site.invoice_to, site.site_notes";
                        $order_by_colums    = "site.site_name";

                        $additional_select = [
                            "0" => [
                                "select"    => "i.integrator_name `integrator`",
                                "join"      => ['integrator `i`', 'i.system_integrator_id = site.system_integrator_id', 'left']
                            ],

                            "1" => [
                                "select"    => "currency.setting_value `currency`",
                                "join"      => ['setting `currency`', 'currency.setting_id = site.invoice_currency_id', 'left'],
                            ],

                            "2" => [
                                "select"    => "frequency.setting_value `frequency`",
                                "join"      => ['setting `frequency`', 'frequency.setting_id = site.charge_frequency_id', 'left'],
                            ],

                            "3" => [
                                "select"    => "site_status.status_name `site_status`",
                                "join"      => ['site_status', 'site_status.status_id = site.status_id', 'left'],
                            ],
                        ];

                        break;

                    case 9: // Live products
                        $table              = "product";
                        $table_columns      = "product.product_id, product.product_name `product`, product.no_of_rooms ` product_no._rooms`, product.start_date";
                        $report_name        = 'Live Products';

                        $todays_date        = date('Y-m-d H:i:s');
                        $first_month_day    = date('Y-m-' . '01', strtotime($todays_date));
                        $last_month_day     = date("Y-m-t", strtotime($todays_date));
                        $where_dates = "(
							( ( product.end_date >= '" . $first_month_day . "' ) && ( product.end_date <= '" . $last_month_day . "' ) ) ||
							( ( product.start_date >= '" . $first_month_day . "' ) && ( product.start_date <= '" . $last_month_day . "' ) ) ||
							( ( product.start_date <= '" . $first_month_day . "' ) && ( ( product.end_date >= '" . $last_month_day . "' ) || ( product.end_date IN ('0000-00-00', '1970-01-01') ) ) )
						)";

                        $additional_select = [
                            "0" => [
                                "select"    => "site.site_name `site`, site.site_id",
                                "join"      => ['site', 'site.site_id = product.site_id', 'left'],
                                "where"     => $where_dates,
                            ],

                            "1" => [
                                "join"      => ['site_address `sa`', 'sa.address_id = site.site_address_id', 'left'],
                                "where"     => "( ( site.archived != 1 ) || ( site.archived IS NULL ) )",
                            ],

                            "2" => [
                                "select"    => "country.country",
                                "join"      => ['content_territory `country`', 'country.territory_id = sa.site_territory_id', 'left'],
                            ],

                            "3" => [
                                "select"    => "ct.country `content_territory`",
                                "join"      => ['content_territory `ct`', 'ct.territory_id = site.content_territory_id', 'left'],
                            ],

                            "4" => [
                                "select"    => "i.integrator_name `integrator`",
                                "join"      => ['integrator `i`', 'i.system_integrator_id = site.system_integrator_id', 'left'],
                            ],

                            "5" => [
                                "select"    => "op.integrator_name `operating_company`",
                                "join"      => ['integrator `op`', 'op.system_integrator_id = site.operating_company_id', 'left'],
                            ],

                            "6" => [
                                "select"    => "product_type.setting_value `product_type`",
                                "join"      => ['setting `product_type`', 'product_type.setting_id = product.product_type_id', 'left'],
                            ],

                            "7" => [
                                "select"    => "sale_currency.setting_value `sale_currency`",
                                "join"      => ['setting `sale_currency`', 'sale_currency.setting_id = product.sale_currency_id', 'left'],
                            ],

                            "8" => [
                                "select"    => "site_currency.setting_value `site_currency`",
                                "join"      => ['setting `site_currency`', 'site_currency.setting_id = site.invoice_currency_id', 'left'],
                            ],
                        ];

                        break;


                    case 4: // All Releases
                        $table          = 'content_film';
                        $table_columns  = 'content_film.title';
                        $report_name    = "All Releases";
                        $limit          = 99999;

                        $additional_select = [
                            "0" => [
                                "join"      => ['content `co`', 'co.content_id = content_film.content_id', 'left']
                            ],

                            "1" => [
                                "select"    => "p.provider_name `provider`",
                                "join"      => ['content_provider `p`', 'p.provider_id = co.content_provider_id', 'left']
                            ],

                            "2" => [
                                "select"    => "ar.age_rating_name",
                                "join"      => ['age_rating ar', 'ar.age_rating_id = content_film.age_rating_id', 'left']
                            ],

                            "3" => [
                                "select"    => "cc.clearance_start_date `clearance_date`",
                                "join"      => ['content_clearance `cc`', 'cc.content_id = content_film.content_id', 'left']
                            ],

                            "4" => [
                                "select"    => "ct.country `clearance_territory`",
                                "join"      => ['content_territory `ct`', 'ct.territory_id = cc.territory_id', 'left']
                            ]
                        ];

                        break;



                    case 5: // Asset Currently In Use
                        $table          = "distribution_bundle_content";
                        $table_columns  = '';
                        $report_name    = "Asset in Use";
                        $order_by_colums = "cf.content_id ASC";

                        $additional_select = [
                            "0" => [
                                "select"    => "p.provider_name `provider`",
                                "join"      => ['content_provider `p`', 'p.provider_id = distribution_bundle_content.provider_id', 'left'],
                                "where"     => ["distribution_bundle_content.content_in_use", 1],
                            ],

                            "1" => [
                                "select"    => "cf.content_id, cf.title",
                                "join"      => ['content_film `cf`', 'cf.content_id = distribution_bundle_content.content_id', 'left'],
                            ],

                            "2" => [
                                "join"      => ['distribution_bundle_sites `dbs`', 'dbs.distribution_bundle_id = distribution_bundle_content.distribution_bundle_id', 'left'],
                            ],

                            "3" => [
                                // "select"     => "CONCAT( '\'',distribution_bundles.distribution_bundle ) `distribution_bundle`, distribution_bundles.distribution_bundle_id",
                                "select"    => "distribution_bundles.distribution_bundle, distribution_bundles.distribution_bundle_id",
                                "join"      => ['distribution_bundles', 'distribution_bundles.distribution_bundle_id = dbs.distribution_bundle_id', 'left'],
                            ],

                            "4" => [
                                "select"    => "site.site_name `site`",
                                "join"      => ['site', 'site.site_id = dbs.site_id', 'left'],
                            ],

                            "5" => [
                                "join"      => ['site_address `sa`', 'sa.address_id = site.site_address_id', 'left'],
                            ],

                            "6" => [
                                "select"    => "content_territory.territory_id, content_territory.country `site_territory`",
                                "join"      => ['content_territory', 'content_territory.territory_id = sa.site_territory_id', 'left'],
                            ],

                            "7" => [
                                "select"    => "content_clearance.clearance_start_date",
                                "join"      => ['content_clearance', 'content_clearance.content_id = cf.content_id', 'left'],
                                "where"     => "`content_clearance`.`territory_id` = sa.site_territory_id",
                            ],
                        ];

                        break;

                    case 6: // Asset Not In Use
                        $table          = 'content_film';
                        $table_columns  = 'content_film.content_id, content_film.title, content_film.release_date';
                        $report_name    = "Asset Not In Use";

                        $additional_select = [
                            "0" => [
                                "join"      => ['content `con`', 'con.content_id = content_film.content_id', 'left']
                            ],

                            "1" => [
                                "select"    => "p.provider_name `provider`",
                                "join"      => ['content_provider `p`', 'p.provider_id = con.content_provider_id', 'left'],
                                // "where"      => "content_film.content_id NOT IN ( SELECT content_id FROM `distribution_bundle_content` )",
                                "where"     => "( 
									( content_film.content_id NOT IN (SELECT distribution_bundle_content.content_id FROM `distribution_bundle_content` WHERE ( ( removal_date IS NULL ) OR ( removal_date IN ( '0000-00-00', '1970-01-01' ) ) ) ) ) OR 
									( content_film.content_id IN ( SELECT distribution_bundle_content.content_id FROM `distribution_bundle_content` WHERE removal_date NOT IN ( '0000-00-00', '1970-01-01' ) ) )
								)",
                            ],

                            "2" => [
                                "select"    => "ar.age_rating_name",
                                "join"      => ['age_rating ar', 'ar.age_rating_id = content_film.age_rating_id', 'left']
                            ],
                        ];

                        break;


                    case 8: // Contract Status Site
                        $table          = 'site';
                        $table_columns  = 'site.site_name `site`, CASE WHEN site.is_signed = 1 THEN "Yes" ELSE "No" END as `signed`';
                        $report_name    = "Contract Status Site";

                        $additional_select = [
                            "0" => [
                                "select"    => "site_contact.contact_full_name `contact_name`, site_contact.email `contact_email`",
                                "join"      => ['site_contact', 'site_contact.site_id = site.site_id', 'left'],
                            ],

                            "1" => [
                                "select"    => "ss.status_name `site_status`",
                                "join"      => ['site_status `ss`', 'ss.status_id = site.status_id', 'left'],
                            ],

                            "2" => [
                                "select"    => "i.integrator_name `integrator`",
                                "join"      => ['integrator `i`', 'i.system_integrator_id = site.system_integrator_id', 'left'],
                            ],
                        ];

                        break;


                    case 7: // Contract Status Integrator
                        $table          = 'integrator';
                        $table_columns  = 'integrator.integrator_name `integrator`, CASE WHEN integrator.is_signed = 1 THEN "Yes" ELSE "No" END as `signed`, integrator.contact_name, integrator.integrator_email `contact_email`, integrator.integrator_status `status`';
                        $report_name    = "Contract Status Integrator";

                        $additional_select = [];

                        break;

                    case 10: // No or Expire Products
                        $table          = 'site';
                        $table_columns  = 'site.site_name `site`';
                        $report_name    = "No or Expire Products";

                        $additional_select = [
                            "0" => [
                                "join"      => ['site_address `sa`', 'sa.address_id = site.site_address_id', 'left'],
                                "where"     => "( ( product.start_date IS NULL && product.end_date IS NULL ) || ( product.start_date = '' && product.end_date = '' ) || ( product.start_date < CURDATE() && product.end_date < CURDATE() && ( product.end_date NOT IN( '1970-01-01', '0000-00-00' ) ) ) )",
                            ],

                            "1" => [
                                "select"    => "c.country",
                                "join"      => ['content_territory `c`', 'c.territory_id = sa.site_territory_id', 'left'],
                            ],

                            "2" => [
                                "select"    => "content_territory.country `content_territory`",
                                "join"      => ['content_territory', 'content_territory.territory_id = site.content_territory_id', 'left'],
                            ],

                            "3" => [
                                "select"    => "currency.setting_value `currency`",
                                "join"      => ['setting `currency`', 'currency.setting_id = site.invoice_currency_id', 'left'],
                            ],

                            "4" => [
                                "select"    => "site_status.status_name `site_status`",
                                "join"      => ['site_status', 'site_status.status_id = site.status_id', 'left'],
                            ],

                            "5" => [
                                "select"    => "i.integrator_name `integrator`",
                                "join"      => ['integrator `i`', 'i.system_integrator_id = site.system_integrator_id', 'left'],
                            ],

                            "6" => [
                                "select"    => "product.product_name `product`, product.no_of_rooms `no_rooms`, product.start_date, product.end_date",
                                "join"      => ['product', 'product.site_id = site.site_id', 'left'],
                                "where"     => "( ( product.archived != 1 ) || ( product.archived IS NULL ) )"
                            ],
                        ];
                        break;

                    default:
                        return false;
                }

                $this->db->select($table_columns, false);

                $arch_where = "( " . $table . ".archived != 1 or " . $table . ".archived is NULL )";
                $this->db->where($arch_where);

                if (!empty($additional_select)) {
                    foreach ($additional_select as $row) {
                        if (isset($row['join']) && !empty($row['join'])) {
                            $this->db->join($row['join'][0], $row['join'][1], $row['join'][2]);
                        }
                        if (isset($row['select']) && !empty($row['select'])) {
                            $this->db->select($row['select']);
                        }
                        if (isset($row['where']) && !empty($row['where'])) {
                            if (is_array($row['where'])) {
                                $this->db->where($row['where'][0], $row['where'][1]);
                            } else {
                                $this->db->where($row['where']);
                            }
                        }
                    }
                }

                $this->db->limit($limit, $offset);

                if (!empty($order_by_colums)) {
                    $this->db->order_by($order_by_colums);
                } else {
                    // $this->db->order_by( $order );
                }

                $query = $this->db->get($table);

                if ($query->num_rows() > 0) {
                    $document_path = '_report_downloads/' . $account_id . '/';
                    $upload_path   = $this->app_root . $document_path;

                    if (!is_dir($upload_path)) {
                        if (!mkdir($upload_path, 0755, true)) {
                            $this->session->set_flashdata('message', 'Error: Unable to create upload location');
                            return false;
                        }
                    }

                    $result = $query->result_array();

                    if ($type_id == 11) {
                        foreach ($result as $key => $row) {
                            $result[$key]['integrator']         = ( !empty($row['integrator']) ) ? decode_for_csv($row['integrator']) : '' ;
                            $result[$key]['invoice_to']         = ( !empty($row['invoice_to']) ) ? ucwords($row['invoice_to']) : '' ;

                            // Encode months
                            $query = false;
                            $this->db->select("month_id", false);
                            $this->db->where("active", 1);
                            $this->db->where("site_id", $row['site_id']);
                            $query = $this->db->get("site_reporting_window_month");

                            if ($query->num_rows() > 0) {
                                $query_result                   = $query->result_array();
                                $ac                             = array_column($query_result, "month_id");
                                $ad                             = array_map(function ($item) {
                                    return str_pad($item, 2, '0', STR_PAD_LEFT);
                                }, $ac);
                                $result[$key]['active_months']  = implode("|", $ad);
                            } else {
                                $result[$key]['active_months']  = "";
                            }

                            // Encode product
                            $result[$key]['products']           = "";
                            $query                              = false;
                            $this->db->select("product.product_name", false);
                            $arch_where = "( product.archived = 0 OR product.archived IS NULL )";
                            $this->db->where($arch_where);
                            $this->db->where("product.site_id", $row['site_id']);
                            $query = $this->db->get("product");

                            if ($query->num_rows() > 0) {
                                $query_result                   = $query->result_array();
                                $ac                             = array_column($query_result, "product_name");
                                $result[$key]['products']       = decode_for_csv(implode("|", $ac));
                            } else {
                                $result[$key]['products']       = "";
                            }

                            // Find the start day
                            $todays_date        = date('Y-m-d H:i:s');
                            $first_month_day    = date('Y-m-' . '01', strtotime($todays_date));
                            $last_month_day     = date("Y-m-t", strtotime($todays_date));

                            $query = false;
                            $this->db->select("start_date", false);

                            $where_dates = "(
								( ( end_date >= '" . $first_month_day . "' ) && ( end_date <= '" . $last_month_day . "' ) ) ||
								( ( start_date >= '" . $first_month_day . "' ) && ( start_date <= '" . $last_month_day . "' ) ) ||
								( ( start_date <= '" . $first_month_day . "' ) && ( ( end_date >= '" . $last_month_day . "' ) || ( end_date IN ('0000-00-00', '1970-01-01') ) ) )
							)";
                            $this->db->where($where_dates);
                            $this->db->where("site_id", $row['site_id']);
                            $this->db->limit(1);
                            $query = $this->db->get("product");

                            if ($query->num_rows() > 0) {
                                $result[$key]['start_date'] = $query->row()->start_date;
                            } else {
                                $result[$key]['start_date'] = "";
                            }

                            // Fee due
                            $this->load->model("serviceapp/site_model", "site_services");
                            $fee_due = false;
                            $fee_due = $this->site_services->get_site_value($account_id, $row['site_id']);
                            if (!empty($fee_due)) {
                                $result[$key]['fee_due'] = $fee_due;
                            } else {
                                $result[$key]['fee_due'] = '';
                            }

                            // Cleaning unwanted columns
                            unset($result[$key]['site_id']);
                        }
                    }

                    if ($type_id == 9) {
                        foreach ($result as $key => $row) {
                            $result[$key]['site']               = ( !empty($row['site']) ) ? decode_for_csv($row['site']) : '' ;
                            $result[$key]['product']            = ( !empty($row['product']) ) ? decode_for_csv($row['product']) : '' ;
                            $result[$key]['integrator']         = ( !empty($row['integrator']) ) ? decode_for_csv($row['integrator']) : '' ;
                            $result[$key]['operating_company']  = ( !empty($row['operating_company']) ) ? decode_for_csv($row['operating_company']) : '' ;

                            // Site Monthly Value - taken from other functionality
                            $this->load->model("serviceapp/site_model", "site_services");
                            $site_monthly_value = false;
                            $site_monthly_value = $this->site_services->get_site_value($account_id, $row['site_id']);
                            if (!empty($site_monthly_value)) {
                                $result[$key]['site_monthly_value'] = $site_monthly_value;
                            } else {
                                $result[$key]['site_monthly_value'] = '';
                            }

                            $result[$key]['product_value'] = false;

                            $todays_date        = date('Y-m-d H:i:s');
                            $first_month_day    = date('Y-m-' . '01', strtotime($todays_date));
                            $last_month_day     = date("Y-m-t", strtotime($todays_date));

                            $product_monthly_value = 0;

                            $product        = $this->product_service->get_product($account_id, false, ["product_id" => $row['product_id']]);

                            if (count($product) == 1) {
                                $p_row = $product[key($product)];

                                // ## 1 - end date in this month, 2 - start day in this month, 3 - start date before this month and end date after this month (we're in the middle), 4 - no end date specified
                                if (( strtolower($p_row->product_status_name) == "active" ) && ( ( ( $p_row->end_date >= $first_month_day ) && ( $p_row->end_date <= $last_month_day ) ) || ( ( $p_row->start_date >= $first_month_day ) && ( $p_row->start_date <= $last_month_day  ) ) || ( ( $p_row->start_date <= $first_month_day ) && ( ( $p_row->end_date >= $last_month_day ) || in_array($p_row->end_date, ["0000-00-00", "1970-01-01"]) ) ) )) {
                                    if (!empty($p_row->package_charge) && !empty($p_row->no_of_rooms)) {
                                        $product_monthly_value += number_format($p_row->package_charge * $p_row->no_of_rooms, 4, '.', '');
                                        $result[$key]['product_value'] = $product_monthly_value;
                                    }
                                }
                            }

                            // Currency - conditional
                            if (!empty($row['product_type'])) {
                                if (strtolower($row['product_type']) == "airtime") {
                                    $result[$key]['product_currency'] = $result[$key]['sale_currency'];
                                } elseif (in_array(strtolower($row['product_type']), ['vod','linear'])) {
                                    $result[$key]['product_currency'] = $result[$key]['site_currency'];
                                } elseif (in_array(strtolower($row['product_type']), ['ad hoc service'])) {
                                    $result[$key]['product_currency'] = $result[$key]['site_currency'];
                                } else {
                                }
                            } else {
                                $result[$key]['product_currency'] = '';
                            }
                            unset($result[$key]['site_id']);
                            unset($result[$key]['sale_currency']);
                            unset($result[$key]['site_currency']);
                            unset($result[$key]['product_id']);
                        }
                    }

                    if ($type_id == 4) {
                        foreach ($result as $key => $row) {
                            $result[$key]['provider']           = ( !empty($row['provider']) ) ? decode_for_csv($row['provider']) : '' ;
                            $result[$key]['title']              = ( !empty($row['title']) ) ? decode_for_csv($row['title']) : '' ;
                        }
                    }

                    if ($type_id == 5) {
                        foreach ($result as $key => $row) {
                            $result[$key]['site']               = ( !empty($row['site']) ) ? decode_for_csv($row['site']) : '' ;
                            $result[$key]['title']              = ( !empty($row['title']) ) ? decode_for_csv($row['title']) : '' ;

                            unset($result[$key]['content_id']);
                            unset($result[$key]['territory_id']);
                            unset($result[$key]['distribution_bundle_id']);
                        }
                    }

                    if ($type_id == 6) {
                        foreach ($result as $key => $row) {
                            $result[$key]['provider']           = ( !empty($row['provider']) ) ? decode_for_csv($row['provider']) : '' ;
                            $result[$key]['title']              = ( !empty($row['title']) ) ? decode_for_csv($row['title']) : '' ;

                            // Clearance Territories
                            $result[$key]['clearance_territories']      = "";
                            $query = false;
                            $this->db->select("content_territory.country", false);

                            $this->db->join("content_territory", "content_territory.territory_id=content_clearance.territory_id", "left");

                            $arch_where = "( content_clearance.archived = 0 OR content_clearance.archived IS NULL )";
                            $this->db->where($arch_where);
                            $this->db->where("content_clearance.content_id", $row['content_id']);
                            $query = $this->db->get("content_clearance");

                            if ($query->num_rows() > 0) {
                                $query_result                           = $query->result_array();
                                $ac                                     = array_column($query_result, "country");
                                $result[$key]['clearance_territories']  = decode_for_csv(implode("|", $ac));
                            } else {
                                $result[$key]['clearance_territories']  = "";
                            }

                            unset($result[$key]['content_id']);
                        }
                    }

                    if ($type_id == 8) {
                        foreach ($result as $key => $row) {
                            $result[$key]['site']               = ( !empty($row['site']) ) ? decode_for_csv($row['site']) : '' ;
                            $result[$key]['integrator']         = ( !empty($row['integrator']) ) ? decode_for_csv($row['integrator']) : '' ;
                            $result[$key]['contact_name']       = ( !empty($row['contact_name']) ) ? decode_for_csv($row['contact_name']) : '' ;
                        }
                    }

                    if ($type_id == 7) {
                        foreach ($result as $key => $row) {
                            $result[$key]['status']             = ( !empty($row['status']) ) ? ucwords($row['status']) : '' ;
                            $result[$key]['integrator']         = ( !empty($row['integrator']) ) ? decode_for_csv($row['integrator']) : '' ;
                            $result[$key]['contact_name']       = ( !empty($row['contact_name']) ) ? decode_for_csv($row['contact_name']) : '' ;
                        }
                    }

                    if ($type_id == 10) {   // No or Expire Products
                        foreach ($result as $key => $row) {
                            $result[$key]['site_status']        = ( !empty($row['site_status']) ) ? ucwords($row['site_status']) : '' ;
                            $result[$key]['integrator']         = ( !empty($row['integrator']) ) ? decode_for_csv($row['integrator']) : '' ;
                            $result[$key]['site']               = ( !empty($row['site']) ) ? decode_for_csv($row['site']) : '' ;
                            $result[$key]['product']            = ( !empty($row['product']) ) ? decode_for_csv($row['product']) : '' ;
                        }
                    }

                    $headers        = explode(', ', ucwords(str_replace('_', ' ', implode(', ', array_keys($result[0])))));
                    $data           = array_to_csv($result, $headers);
                    $file_name      = $report_name . '-' . date('dmYHi') . '.csv';
                    $file_path      = $upload_path . $file_name;

                    if (write_file($upload_path . $file_name, $data)) {
                        // $req_source = 'web-client';

                        // if( $req_source == 'web-client' ){
                            // force_download( $report_name, file_get_contents( $file_path ) );
                        // } else {
                            $result = [
                                'timestamp'     => date('d.m.Y H:i:s'),
                                'expires_at'    => date('d.m.Y H:i:s', strtotime('+1 hour')),
                                'file_name'     => $file_name,
                                'file_path'     => $file_path,
                                'file_link'     => base_url($document_path . $file_name)
                            ];
                        // }
                    }
                } else {
                }
            }
        }

        return $result;
    }
}
