<?php

namespace Application\Modules\Service\Models;

use System\Core\CI_Model;

class IntegratorGateway_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

        $section            = explode("/", $_SERVER["SCRIPT_NAME"]);
        if (!isset($section[1]) || empty($section[1]) || (!(is_array($section)))) {
            $this->app_root = substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), "application"));
        } else {
            if (!isset($_SERVER["DOCUMENT_ROOT"]) || (empty($_SERVER["DOCUMENT_ROOT"]))) {
                $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . '/../');
            }

            $this->section      = $section;
            $this->app_root     = $_SERVER["DOCUMENT_ROOT"] . "/" . $section[1] . "/";
            $this->app_root     = str_replace('/index.php', '', $this->app_root);
        }
    }


    /*
    *
    */
    public function get_active_sites($integrator_id = false, $site_ids = false, $limit = DEFAULT_MAX_LIMIT)
    {
        $result = [
            "message"   => "",
            "status"    => false,
            "data"      => null
        ];

        if (!empty($integrator_id)) {
            $this->db->select("site.site_id, site.site_name", false);
            // $this->db->select( "integrator.integrator_name", false );
            $this->db->select("site_address.fulladdress `address`", false);
            $this->db->select("site_contact.contact_full_name `contact_name`, site_contact.telephone_number `contact_number`, site_contact.email `contact_email`", false);
            $this->db->select("content_territory.country `content_territory`", false);

            $this->db->where("site.system_integrator_id", $integrator_id);
            $arch_where = "( ( site.archived != 1 ) OR ( site.archived IS NULL ) )";
            $this->db->where($arch_where);

            if (!empty($site_ids)) {
                $this->db->where_in("site.site_id", $site_ids);
            }

            $this->db->join("site_address", "site.site_address_id = site_address.address_id", "left");
            $this->db->join("site_contact", "site.site_id = site_contact.site_id", "left");
            $this->db->join("content_territory", "site.content_territory_id = content_territory.territory_id", "left");
            $this->db->join("integrator", "integrator.system_integrator_id = site.system_integrator_id", "left");

            if (!empty($limit) && ($limit <= DEFAULT_MAX_LIMIT)) {
                $this->db->limit($limit);
            } else {
                $this->db->limit(DEFAULT_MAX_LIMIT);
            }

            $this->db->order_by("site.site_name ASC");

            $query = $this->db->get("site");

            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $result['data'][$row->site_id]              = $row;
                    $result['data'][$row->site_id]->product     = [];

                    // $this->db->select( "product.product_id", false );
                    $this->db->select("product.product_name", false);
                    $this->db->select("product_type_tab.setting_value `product_type`", false);
                    $this->db->select("content_provider.provider_name `provider`", false);
                    $this->db->select("no_of_titles.setting_value `number_of_titles`, product.no_of_rooms `number_of_rooms`", false);

                    $this->db->where("product.site_id", $row->site_id);
                    $arch_where_prod = "( ( product.archived != 1 ) OR ( product.archived IS NULL ) )";
                    $this->db->where($arch_where_prod);

                    $this->db->join("setting `product_type_tab`", "product_type_tab.setting_id = product.product_type_id", "left");
                    $this->db->join("content_provider", "content_provider.provider_id = product.content_provider_id", "left");
                    $this->db->join("setting `no_of_titles`", "no_of_titles.setting_id = product.no_of_titles_id", "left");

                    $pro_query = $this->db->get("product");

                    if ($pro_query->num_rows() > 0) {
                        $result['data'][$row->site_id]->product = $pro_query->result();
                    }
                }

                $result['message']  = "Site data has been found";
                $result['status']   = true;
            } else {
                $result['data']     = false;
                $result['message']  = "No Site data has been found";
                $result['status']   = false;
            }
        } else {
            $result['message']  = "Missing required data: Integrator ID";
        }

        return $result;
    }


    /*
    *
    */
    public function get_installed_films($integrator_id = false, $limit = DEFAULT_MAX_LIMIT, $site_ids = false, $order_by = false)
    {
        $result = [
            "message"   => "",
            "status"    => false,
            "data"      => null
        ];

        if (!empty($integrator_id)) {
            $this->db->select("distribution_groups.distribution_group_id", false);

            $this->db->where("distribution_groups.system_integrator_id", $integrator_id);
            $a1_archived =  "( ( distribution_groups.archived != 1 ) OR ( distribution_groups.archived IS NULL ) )";
            $this->db->where($a1_archived);

            if (!empty($site_ids)) {
                $this->db->join("distribution_group_sites", "distribution_group_sites.distribution_group_id = distribution_groups.distribution_group_id", "left");

                $this->db->where_in("distribution_group_sites.site_id", $site_ids) ;
            }

            $q1 = $this->db->get("distribution_groups");

            if ($q1->num_rows() > 0) {
                $dg = single_array_from_arrays($q1->result_array(), "distribution_group_id");

                if (!empty($dg)) {
                    $this->db->select("content_film.asset_code", false);
                    $this->db->select("content_film.title `content_title`", false);
                    $this->db->select("content_provider.provider_name", false);
                    $this->db->select("age_rating.age_rating_name `certificate`", false);
                    $this->db->select("distribution_bundle_content.removal_date, distribution_bundle_content.license_start_date, distribution_bundle_content.content_in_use", false);
                    // $this->db->select( "distribution_bundle_content.distribution_group_id", false ); ## for testing only
                    $this->db->select("content_film.content_id", false);

                    $this->db->join("content_film", "content_film.content_id = distribution_bundle_content.content_id", "left");
                    $this->db->join("age_rating", "age_rating.age_rating_id = content_film.age_rating_id", "left");
                    $this->db->join("content", "content.content_id = content_film.film_id", "left");
                    $this->db->join("content_provider", "content_provider.provider_id = content.content_provider_id", "left");

                    $this->db->where_in("distribution_bundle_content.distribution_group_id", $dg);

                    if (!empty($limit) && ($limit <= DEFAULT_MAX_LIMIT)) {
                        $this->db->limit($limit);
                    } else {
                        $this->db->limit(DEFAULT_MAX_LIMIT);
                    }

                    $this->db->order_by("content_film.title ASC");

                    $this->db->group_by("distribution_bundle_content.content_id");

                    $a1_archived =  "( ( distribution_bundle_content.archived != 1 ) OR ( distribution_bundle_content.archived IS NULL ) )";
                    $this->db->where($a1_archived);

                    $q2 = $this->db->get("distribution_bundle_content");

                    if ($q2->num_rows() > 0) {
                        foreach ($q2->result() as $key => $q2_row) {
                            if (
                                ($q2_row->content_in_use == true) ||
                                ($q2_row->removal_date == "" || $q2_row->removal_date == null) ||
                                ($q2_row->license_start_date > $q2_row->removal_date)
                            ) {
                                $output[$key]               = $q2_row;
                                $output[$key]->languages    = null;

                                if (!empty($q2_row->content_id)) {
                                    $this->db->select("content_decoded_file.file_id, content_decoded_file.content_id", false);
                                    $this->db->select("content_decoded_stream.language", false);
                                    $this->db->select("content_decoded_stream.stream_id, content_decoded_stream.language, content_decoded_stream.decoded_file_id, content_decoded_stream.codec_type, content_decoded_stream.codec_type_id ", false);

                                    $this->db->join("content_decoded_file", "content_decoded_file.file_id = content_decoded_stream.decoded_file_id", "left");

                                    $where_sql = "( content_decoded_stream.decoded_file_id IN ( 
										SELECT content_decoded_file.file_id
										FROM  content_decoded_file
										WHERE content_decoded_file.decoded_file_type_id = 1 AND
										content_decoded_file.main_record = 1 AND
										content_decoded_file.is_verified = 1 AND
										( content_decoded_file.archived != 1 OR content_decoded_file.archived IS NULL ) AND
										content_decoded_file.content_id = " . $q2_row->content_id . " )
									)";

                                    $this->db->where($where_sql);
                                    $this->db->where("content_decoded_stream.codec_type_id", 1);

                                    $codec_query = $this->db->get("content_decoded_stream");

                                    if ($codec_query->num_rows() > 0) {
                                        $codec_set                  = single_array_from_arrays($codec_query->result_array(), "language");
                                        $output[$key]->languages    = $codec_set;
                                    }
                                }
                            }

                            unset($q2_row->content_id);
                            unset($q2_row->removal_date);
                            unset($q2_row->content_in_use);
                        }

                        $result['data']     = array_values($output);
                        $result['message']  = "Installed Film data has been found";
                        $result['status']   = true;
                    } else {
                        $result['message']  = "No installed Film data has been found";
                        $result['status']   = false;
                    }
                }
            }
        } else {
            $result['message']  = "Missing required data: Integrator ID";
        }

        return $result;
    }
}
