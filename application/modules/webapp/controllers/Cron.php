<?php

namespace Application\Modules\Web\Controllers;

defined('BASEPATH') || exit('No direct script access allowed');

use Application\Extentions\MX_Controller;

class Cron extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('serviceapp/Internal_common_model', 'common_service');
    }

    public function index()
    {
        debug(date("H"), "print", false);

        switch (date("H")) {
            case "01":
                break;

            case "02":
                // $this->automated_site_disabling();
                break;

            case "03":
                // $this->automated_integrator_disabling();
                break;

            case "13":
                $this->create_marketing_pdf();
                break;

                ## default :
                ## $this->automated_site_disabling();
        }
    }

    public function automated_site_disabling()
    {
        $this->load->model('serviceapp/site_model', 'site_service');
        $disabled_sites = $this->site_service->automated_site_disable();

        return true;
    }


    public function automated_integrator_disabling()
    {
        $this->load->model('serviceapp/integrator_model', 'integrator_service');
        $disabled_integrators = $this->integrator_service->automated_integrator_disabling();

        return true;
    }


    public function create_marketing_pdf()
    {
        $this->load->model('serviceapp/content_model', 'content_service');

        $account_id         = 1;
        $territory_id       = 83;
        $provider_ids       = json_encode([1,2]);
        $product_name       = "airtime";

        $pdf_data = $this->content_service->generate_pdf_data($account_id, $territory_id, $provider_ids, $product_name);

        if (!empty($pdf_data)) {
            $document_setup = [
                "pdf_type"          => strtolower($product_name),
                "pdf_data"          => $pdf_data,
                "pdf_category"      => "pdf_marketing",
                "account_id"        => $account_id,
                "pdf_target"        => "cron-stored"
            ];
            $this->load->view('/evipdf/marketing_pdf_generator.php', $document_setup);
        } else {
        }

        return true;
    }
}
