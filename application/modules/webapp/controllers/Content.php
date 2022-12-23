<?php

namespace Application\Modules\Web\Controllers;

defined('BASEPATH') || exit('No direct script access allowed');

use Application\Extentions\MX_Controller;

class Content extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (false === $this->identity()) {
            redirect("webapp/user/login", 'refresh');
        }

        $this->module_id       = $this->webapp_service->_get_module_id($this->router->fetch_class());
        $this->load->model('serviceapp/Content_model', 'content_service');
        $this->load->library('pagination');
    }


    private $airtime_states     = ["offline", "published"];
    private $data_upload_tables = ["su_content_tmp_upload", "devices_tmp_upload", "content_clearance_tmp_upload"];

    //redirect if needed
    public function index()
    {
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            //access denied
            $this->_render_webpage('errors/access-denied', false);
        } else {
            redirect('webapp/content/content', 'refresh');
        }
    }


    public function content($content_id = false)
    {
        if ($content_id) {
            redirect('webapp/content/profile/' . $content_id, 'refresh');
        }

        # Check module access
        $module_access = $this->webapp_service->check_access($this->user, $this->module_id);

        if (!$this->user->is_admin && !$module_access) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $data['current_user']       = $this->user;
            $data['module_id']          = $this->module_id;

            $data['content_providers']  = $postdata = [];
            $postdata['account_id']     = $this->user->account_id;
            $url                        = 'provider/provider';
            $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $data['content_providers']  = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->content_provider)) ? $API_result->content_provider : null;

            $this->_render_webpage('content/index', $data);
        }
    }


    /**
    *   Create new content
    **/
    public function create()
    {
        # Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'details');

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $data = false;

            $postdata['account_id'] = $this->user->account_id;

            $data['content_providers']  = [];
            $url                        = 'provider/provider';
            $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $data['content_providers']  = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->content_provider)) ? $API_result->content_provider : null;

            $data['age_rating']                     = $postdata = [];
            $postdata['account_id']                 = $this->user->account_id;
            $url                                    = 'content/age_rating';
            $postdata['where']['is_visible_on_cacti'] = 1;
            $API_result                             = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $data['age_rating']                     = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->age_rating)) ? $API_result->age_rating : null;

            $data['territories']        = $postdata = [];
            $postdata['account_id']     = $this->user->account_id;
            $url                        = 'content/territories';
            $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $data['territories']        = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->territories)) ? $API_result->territories : null;

            $this->_render_webpage('content/content_create', $data);
        }
    }


    public function create_content($page = "details")
    {
        ## $section = ( $this->input->post( 'page' ) ) ? $this->input->post( 'page' ) : ( ( !empty( $page ) ) ? $page : $this->router->fetch_method() );
        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'details');
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data)) {
                $postdata               = [];
                $post_data              = $this->input->post();

                $postdata['content_data']   = $post_data;
                $postdata['account_id']     = $this->user->account_id;

                $url            = 'content/create';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result)) {
                    $return_data['content']     = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->new_content)) ? $API_result->new_content : null;
                    $return_data['status']      = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    //View Content profile
    public function profile($content_id = false, $page = 'details')
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        ## Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } elseif ($content_id) {
            $run_admin_check            = false;
            $API_call                   = $this->webapp_service->api_dispatcher($this->api_end_point . 'content/content', ['account_id' => $this->user->account_id,'content_id' => $content_id], ['auth_token' => $this->auth_token], true);
            $data['content_details']    = (isset($API_call->content)) ? $API_call->content : null;
            if (!empty($data['content_details'])) {
                ## Get allowed access for the logged in user
                $data['permissions'] = $item_access;
                $data['active_tab'] = $page;
                $data['module_id']  = $this->module_id;

                $module_items       = $this->webapp_service->api_dispatcher($this->api_end_point . 'access/account_modules_items', ['account_id' => $this->user->account_id, 'module_id' => $this->module_id ], ['auth_token' => $this->auth_token], true);
                $data['module_tabs'] = (isset($module_items->module_items)) ? $module_items->module_items : null;

                switch ($page) {
                    case 'details':
                    default:
                        $data['content_providers']  = $postdata = [];
                        $postdata['account_id']     = $this->user->account_id;
                        $url                        = 'provider/provider';
                        $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['content_providers']  = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->content_provider)) ? $API_result->content_provider : null;

                        $data['age_rating']                     = $postdata = [];
                        $postdata['account_id']                 = $this->user->account_id;
                        $postdata['where']['is_visible_on_cacti'] = 1;
                        $url                                    = 'content/age_rating';
                        $API_result                             = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['age_rating']                     = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->age_rating)) ? $API_result->age_rating : null;

                        ## Clearance
                        $data['territories']                    = $postdata = [];
                        $postdata['account_id']                 = $this->user->account_id;
                        $url                                    = 'content/territories';
                        $API_result                             = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['territories']                    = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->territories)) ? $API_result->territories : null;

                        $data['remains_territories']            = $postdata = [];
                        $postdata['account_id']                 = $this->user->account_id;
                        $postdata['where']['not_added']         = 'yes';
                        $postdata['where']['content_id']        = $content_id;
                        $url                                    = 'content/territories';
                        $API_result                             = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['remains_territories']            = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->territories)) ? $API_result->territories : null;

                        $data['clearance']                      = $postdata = [];
                        $postdata['account_id']                 = $this->user->account_id;
                        $postdata['where']['content_id']        = $content_id;
                        $url                                    = 'content/clearance';
                        $API_result                             = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['clearance']                      = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->clarance)) ? $API_result->clarance : null;

                        ## Languages
                        $data['language_phrases']               = $postdata = [];
                        $postdata['account_id']                 = $this->user->account_id;
                        $postdata['where']['content_id']        = $content_id;
                        $url                                    = 'content/language_phrase';
                        $API_result                             = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['language_phrases']               = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->language_phrase)) ? $API_result->language_phrase : null;

                        ## Content Documents
                        $content_documents                      = $this->webapp_service->api_dispatcher($this->api_end_point . 'document_handler/document_list', ['account_id' => $this->user->account_id, 'content_id' => $content_id, 'document_group' => 'content' ], ['auth_token' => $this->auth_token], true);
                        $data['content_documents']              = (isset($content_documents->documents->{$this->user->account_id})) ? $content_documents->documents->{$this->user->account_id} : null;

                        $data['subtitles'] = $data['images'] = [];

                        if (!empty($data['content_documents']->Content)) {
                            foreach ($data['content_documents']->Content as $doc) {
                                if (strtolower($doc->doc_file_type) == "subtitles") {
                                    $data['subtitles'][] = $doc;
                                }

                                if (in_array(strtolower($doc->doc_file_type), [ "hero", "standard" ])) {
                                    $data['images'][] = $doc;
                                }
                            }
                        }

                        ## Content Preparation
                        $data['decoded_file_streams']           = $postdata = [];
                        $postdata['account_id']                 = $this->user->account_id;
                        $postdata['where']['content_id']        = $content_id;
                        $url                                    = 'content/decoded_file_streams';
                        $API_result                             = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
                        $data['decoded_file_streams']           = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->decoded_file_streams)) ? $API_result->decoded_file_streams : null;

                        $data['airtime_states']                 = $this->airtime_states;

                        $data['include_page']                   = 'content_details.php';
                        break;
                }
            }

            ## Run the admin check if tab needs only admin
            if (!empty($run_admin_check)) {
                if ((!admin_check($this->user->is_admin, false, (!empty($data['permissions']) ? $data['permissions']->is_admin : false)))) {
                    $data['admin_no_access'] = true;
                }
            }

            $this->_render_webpage('content/profile', $data);
        } else {
            redirect('webapp/content', 'refresh');
        }
    }

    /*
    *   Content lookup / search
    */
    public function lookup($page = 'details')
    {
        $return_data = '';

        # Check module access
        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access    = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data .= $this->config->item('ajax_access_denied');
        } else {
            # Setup search parameters
            $search_term        = ($this->input->post('search_term')) ? $this->input->post('search_term') : false;
            $limit              = ($this->input->post('limit')) ? $this->input->post('limit') : DEFAULT_LIMIT;
            $start_index        = ($this->input->post('start_index')) ? $this->input->post('start_index') : 0;
            $offset             = (!empty($start_index)) ? (($start_index - 1) * $limit) : 0;
            $order_by           = false;
            $where              = false;

            #prepare postdata
            $postdata = [
                'account_id'        => $this->user->account_id,
                'search_term'       => $search_term,
                'order_by'          => $order_by,
                'limit'             => $limit,
                'offset'            => $offset,
                'where'             => $where,
            ];

            if (!empty($this->input->post('content_provider'))) {
                $postdata['where'] = [
                    'content_provider' => $this->input->post('content_provider')
                ];
            }

            $API_call   = $this->webapp_service->api_dispatcher($this->api_end_point . 'content/lookup', $postdata, ['auth_token' => $this->auth_token], true);
            $content    = (isset($API_call->content)) ? $API_call->content : null;

            if (!empty($content)) {
                ## Create pagination
                $counters       = $this->content_service->get_total_content($this->user->account_id, $search_term, $postdata['where'], $order_by, $limit, $offset);//Direct access to count, this should only return a number
                $page_number    = ($start_index > 0) ? $start_index : 1;
                $page_display   = '<span style="margin:15px 0px;" class="pull-left">Page <strong>' . $page_number . '</strong> of <strong>' . $counters->pages . '</strong></span>';

                if ($counters->total > 0) {
                    $config['total_rows']   = $counters->total;
                    $config['per_page']     = $limit;
                    $config['current_page'] = $page_number;
                    $pagination_setup       = _pagination_config("content");
                    $config                 = array_merge($config, $pagination_setup);
                    $this->pagination->initialize($config);
                    $pagination             = $this->pagination->create_links();
                }

                $return_data = $this->load_content_view($content);
                if (!empty($pagination)) {
                    $return_data .= '<tr><td colspan="8" style="padding: 0;">';
                    $return_data .= $page_display . $pagination;
                    $return_data .= '</td></tr>';
                }
            } else {
                $return_data .= '<tr><td colspan="8">';
                $return_data .= (isset($search_result->message)) ? $search_result->message : 'No records found';
                $return_data .= '</td></tr>';
            }
        }

        print_r($return_data);
        die();
    }

    /*
    *   Prepare content view
    */
    private function load_content_view($content_data = false)
    {
        $return_data = '';

        if (!empty($content_data)) {
            foreach ($content_data as $k => $row) {
                $return_data .= '<tr>';
                $return_data .= '<td>' . ((!empty($row->content_id)) ? $row->content_id : '') . '</td>';
                $return_data .= '<td><a href="' . base_url('/webapp/content/profile/' . $row->content_id) . '" >' . (!empty($row->title) ? $row->title : '') . '</a></td>';
                $return_data .= '<td>' . (!empty($row->provider_name) ? "<a href='" . base_url('/webapp/provider/profile/') . ((int) $row->provider_id) . "'>" . html_escape($row->provider_name) . "</a>" : '') . '</td>';
                $return_data .= '<td>' . (!empty($row->content_provider_reference_code) ? html_escape($row->content_provider_reference_code) : '') . '</td>';
                $return_data .= '<td>' . (!empty($row->release_year) ? $row->release_year : '') . '</td>';
                $return_data .= '<td>' . (!empty($row->age_rating_name) ? $row->age_rating_name : '') . '</td>';
                $return_data .= '<td>' . (!empty($row->is_content_active) ? 'Yes' : 'No') . '</td>';
                $return_data .= '<td>' . (!empty($row->imdb_link) ? '<a target="_blank" href="' . ((strpos($row->imdb_link, 'http') !== false) ? $row->imdb_link : 'https://' . $row->imdb_link) . '">IMDB</a>' : '') . '</td>';
                $return_data .= '</tr>';
            }

            if (!empty($pagination)) {
                $return_data .= '<tr><td colspan="4" style="padding: 0;">';
                $return_data .= $page_display . $pagination;
                $return_data .= '</td></tr>';
            }
        } else {
            $return_data .= '<tr><td colspan="8"><br/>' . $this->config->item("no_records") . '</td></tr>';
        }
        return $return_data;
    }


    public function delete_content($content_id = false, $page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data['status'] = 0;

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data) && !empty($post_data['content_id'])) {
                $postdata                   = [];
                $postdata['account_id']     = $this->user->account_id;
                $postdata['content_id']     = (!empty($post_data['content_id'])) ? $post_data['content_id'] : false ;

                $url            = 'content/delete';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                ## d_content = deleted_content
                if (!empty($API_result)) {
                    $return_data['d_content']   = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->d_content)) ? $API_result->d_content : null;
                    $return_data['status']      = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    public function update($content_id = false, $page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data) && !empty($post_data['content_id'])) {
                $postdata                   = [];
                $postdata['account_id']     = $this->user->account_id;
                $postdata['content_data']   = $post_data;
                $postdata['content_id']     = (!empty($post_data['content_id'])) ? $post_data['content_id'] : false ;

                $url            = 'content/update';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                ## u_content = updated_provider
                if (!empty($API_result)) {
                    $return_data['u_content']   = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->u_content)) ? $API_result->u_content : null;
                    $return_data['status']      = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }

    public function check_reference($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = "details");

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data)) {
                $account_id = $this->user->account_id;
                $reference  = (!empty($post_data['reference'])) ? $post_data['reference'] : false ;
                $module     = (!empty($post_data['module'])) ? $post_data['module'] : false ;

                $reference_exists       = $this->ssid_common->check_reference($account_id, $reference, $module);

                if (!empty($reference_exists)) {
                    $return_data['reference']   = (isset($reference_exists) && !empty($reference_exists)) ? $reference_exists : null;
                    $return_data['status']      = true;
                    $return_data['status_msg']  = "The Reference code already exists";
                } else {
                    $return_data['status_msg']  = "This Reference Code seems to be unique";
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    /*
    *   Function to add manually a clearance entry for the specific content
    */
    public function add_clearance($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = "details");

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data)) {
                $postdata['account_id']             = $this->user->account_id;
                $postdata['content_id']             = (!empty($post_data['content_id'])) ? (int) $post_data['content_id'] : false ;
                $postdata['clearance_start_date']   = (!empty($post_data['clearance_start_date'])) ? $post_data['clearance_start_date'] : false ;
                $postdata['territories']            = (!empty($post_data['territories'])) ? $post_data['territories'] : false ;

                $url            = 'content/add_clearance';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result->status) && ($API_result->status == true)) {
                    $return_data['new_clearance']   = (isset($API_result->new_clearance) && !empty($API_result->new_clearance)) ? $API_result->new_clearance : null ;
                    $return_data['status']          = 1;
                    $return_data['status_msg']      = (isset($API_result->message) && !empty($API_result->message)) ? $API_result->message : null ;
                } else {
                    $return_data['status_msg']      = (!empty($API_result->message)) ? $API_result->message : 'There was an error processing your request';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    public function upload_clearance($page = "documents")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        ## Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $account_id     = $this->user->account_id;

            $post_data = $this->input->post();

            if (!empty($post_data['uploaded'])) {
                $process_file   = $this->content_service->upload_content($account_id);
                if ($process_file) {
                    redirect('/webapp/content/review/' . $account_id);
                }
            }

            $data[] = false;
            $this->_render_webpage('content/content_upload_clearance', $data);
        }
    }

    /**
    *   Review People
    **/
    public function review($account_id = false)
    {
        if (!empty($account_id)) {
            $pending                = $this->content_service->get_pending_upload_records($account_id);
            $data['pending']        = (!empty($pending)) ? $pending : null;

            ## territories

            $data['territories']        = $postdata = [];
            $postdata['account_id']     = $this->user->account_id;
            $url                        = 'content/territories';
            $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'GET');
            $data['territories']        = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->territories)) ? $API_result->territories : null;

            $this->_render_webpage('content/pending_creation', $data);
        }
    }


    public function create_clearance($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data)) {
                ## this will be batch upload
                $postdata['account_id']         = $this->user->account_id;
                $postdata['batch_clearance']    = (!empty($post_data['batch_clearance'])) ? $post_data['batch_clearance'] : false ;

                $url            = 'content/add_batch_clearance';
                $API_result     = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result->status) && ($API_result->status == true)) {
                    $return_data['all_done']        = 1;
                    $return_data['status']          = 1;
                    $return_data['status_msg']      = (isset($API_result->message) && !empty($API_result->message)) ? $API_result->message : null ;
                } else {
                    $return_data['status_msg']      = (!empty($API_result->message)) ? $API_result->message : 'There was an error processing your request';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }

    public function drop_temp_records($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_delete) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data)) {
                ## this will be batch delete
                $postdata['account_id']         = $this->user->account_id;
                $postdata['batch_clearance']    = (!empty($post_data['batch_clearance'])) ? $post_data['batch_clearance'] : false ;

                $url                = 'content/remove_clearance_from_tmp';
                $API_result         = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result->status) && ($API_result->status == true)) {
                    $return_data['all_done']        = 1;
                    $return_data['status']          = 1;
                    $return_data['status_msg']      = (isset($API_result->message) && !empty($API_result->message)) ? $API_result->message : null ;
                } else {
                    $return_data['status_msg']      = (!empty($API_result->message)) ? $API_result->message : 'There was an error processing your request';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }



    /*
    *   Function to delete a clearance entry
    */
    public function delete_clearance($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = "details");

        if (!$this->user->is_admin && empty($item_access->can_delete) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data)) {
                $postdata['account_id']     = $this->user->account_id;
                $postdata['clearance_id']   = (!empty($post_data['clearance_id'])) ? (int) $post_data['clearance_id'] : false ;
                $url                        = 'content/delete_clearance';

                $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                ## d_clearance - deleted clearance
                if (!empty($API_result->status) && ($API_result->status == true)) {
                    $return_data['d_clearance']     = (!empty($API_result->d_clearance)) ? $API_result->d_clearance : null ;
                    $return_data['status']          = 1;
                    $return_data['status_msg']      = (isset($API_result->message) && !empty($API_result->message)) ? $API_result->message : null ;
                } else {
                    $return_data['status_msg']      = (!empty($API_result->message)) ? $API_result->message : 'There was an error processing your request';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    public function fetch_genres()
    {
        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = 'details');
        if (!$this->user->is_admin && empty($item_access->can_view) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data)) {
                $postdata                           = [];
                $post_data                          = $this->input->post();
                $postdata['account_id']             = $this->user->account_id;
                $content_type                       = (!empty($post_data['contentType'])) ? $post_data['contentType'] : false ;
                $destination                        = (!empty($post_data['destination'])) ? $post_data['destination'] : false ;
                $checked                            = (!empty($post_data['checked'])) ? $post_data['checked'] : false ;
                $postdata['account_id']             = $this->user->account_id;
                $postdata['where']['content_type']  = $content_type;
                $url                                = 'content/genres';
                $API_result                         = $this->ssid_common->api_call($url, $postdata, $method = 'GET');

                if (!empty($API_result)) {
                    $return_data['genres']      = (isset($API_result->status) && ($API_result->status == true) && !empty($API_result->genres)) ? $this->prepare_genres_view($API_result->genres, $destination, $checked) : null;
                    $return_data['content_type']    = $content_type ;
                    $return_data['status']      = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }

    private function prepare_genres_view($genres_data = false, $destination = false, $checked = false)
    {
        $return_string = '';
        if (!empty($genres_data)) {
            if (!empty($destination) && ($destination == "profile")) {
                $return_string = '<li class="col-lg-6 col-md-6 col-sm-6 col-xs-12"><label for="all_genres"><input type="checkbox" id="all_genres" value=""><span class="genre_name">All Genres</span></label></li>';

                $separator = false;

                foreach ($genres_data as $g_row) {
                    if ($g_row->genre_type_id == 5 && !$separator) {
                        $return_string .= '<hr style="float: left;;width: calc( 100% - 20px );border: 0;height: 1px;background: #ccc;background-image: linear-gradient(to right, #eee, #ccc, #eee);" />';
                        $separator      = true;
                    }

                    $return_string .= '<li class="col-lg-6 col-md-6 col-sm-6 col-xs-12">';
                    $return_string .= '<label for="' . (strtolower(trim($g_row->genre_name))) . '">';
                    $return_string .= '<input type="checkbox" name="imdb_details[genre][]" id="' . (strtolower(trim($g_row->genre_name))) . '" value="' . ((!empty($g_row->genre_id)) ? $g_row->genre_id : '') . '" ' . ((!empty($checked) && (in_array($g_row->genre_id, $checked))) ? 'checked="checked"' : '') . ' /> <span class="genre_name">' . ucwords(strtolower($g_row->genre_name)) . '</span></label></li>';
                }
            } else {
                $return_string = '<li class="col-lg-6 col-md-6 col-sm-6 col-xs-12"><label for="all_genres"><input type="checkbox" id="all_genres" value=""> <span class="genre_name weak">All Genres</span></label></li>';

                $separator = false;

                foreach ($genres_data as $g_row) {
                    if ($g_row->genre_type_id == 5 && !$separator) {
                        $return_string .= '<hr style="float: left;;width: calc( 100% - 20px );border: 0;height: 1px;background: #ccc;background-image: linear-gradient(to right, #eee, #ccc, #eee);" />';
                        $separator      = true;
                    }

                    $return_string .= '<li class="col-lg-6 col-md-6 col-sm-6 col-xs-12">';
                    $return_string .= '<label for="' . (strtolower(trim($g_row->genre_name))) . '">';
                    $return_string .= '<input type="checkbox" name="content_film[genre][]" id="' . (strtolower(trim($g_row->genre_name))) . '" value="' . ((!empty($g_row->genre_id)) ? $g_row->genre_id : '') . '" /> <span class="genre_name weak">' . ucwords(strtolower($g_row->genre_name)) . '</span></label></li>';
                }
            }
        } else {
        }

        return $return_string;
    }


    /*
    *   Function to update the set of the language phrases (tagline, title, synopsis...)
    */
    public function update_language_phrase($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data)) {
                $postdata['account_id']     = $this->user->account_id;
                $postdata['phrases_data']   = (!empty($post_data['phrases'])) ? $post_data['phrases'] : false ;
                $postdata['content_id']     = (!empty($post_data['content_id'])) ? $post_data['content_id'] : false ;

                $url                        = 'content/update_language_phrase';
                $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result->status) && ($API_result->status == true)) {
                    $return_data['phrases']         = (!empty($API_result->phrases)) ? $API_result->phrases : null ;
                    $return_data['status']          = 1;
                    $return_data['status_msg']      = (isset($API_result->message) && !empty($API_result->message)) ? $API_result->message : null ;
                } else {
                    $return_data['status_msg']      = (!empty($API_result->message)) ? $API_result->message : 'There was an error processing your request';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }



    /*
    *   Function to generate the export file: for the download and to be placed in the content specifically folder
    */
    public function generate_file($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!$this->user->is_admin && empty($item_access->can_edit) && empty($item_access->is_admin)) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data) && (!empty($post_data['content_id'])) && !empty($post_data['file_type'])) {
                $postdata['account_id']     = $this->user->account_id;
                $postdata['file_type']      = (!empty($post_data['file_type'])) ? $post_data['file_type'] : false ;
                $postdata['content_id']     = (!empty($post_data['content_id'])) ? $post_data['content_id'] : false ;

                $url                        = 'content/generate_file_export';
                $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                $export = (!empty($API_result->export)) ? $API_result->export : false ;

                if (isset($export)) {
                    if (isset($postdata['file_type']) && isset($export->xml_file_name) || isset($export->json_file_name)) {
                        ## Evident version $docName =  strtoupper($postdata['file_type']) . ' Export - ' . $postdata['content_id'] . ' ' . date('Y-m-d H:i:s');
                        $docName        =  (!empty($export->xml_file_name)) ? html_escape($export->xml_file_name) : ((!empty($export->json_file_name)) ? html_escape($export->json_file_name) : "asset_" . date('Y-m-d H:i:s') . $postdata['file_type']);
                        $fileName       =  (strtolower($post_data['file_type']) == 'xml') ? $export->xml_file_name : $export->json_file_name;
                        $docReference   = time() . '_' . $fileName;
                        $docLink        = (strtolower($post_data['file_type']) == 'xml') ? $export->xml_file_link : $export->json_file_link;
                        $docLocation    =  $export->document_location . $fileName;

                        $document_data = [
                            "account_id"        => $this->user->account_id,
                            "content_id"        => $postdata['content_id'],
                            "module"            => "content",
                            "doc_type"          => "Content",
                            "doc_file_type"     => $postdata['file_type'],
                            "document_name"     => $docName,
                            "doc_reference"     =>  $docReference,
                            "document_link"     =>  $docLink,
                            'document_location' =>  $docLocation
                        ];
                        $target_table = "content_document_uploads";

                        $this->document_service->_create_document_placeholder($this->user->account_id, $document_data, $target_table);
                    } else {
                        redirect('webapp/content/profile/' . $postdata['content_id'], 'refresh');
                    }
                }

                if (isset($postdata['file_type']) && (strtolower($postdata['file_type']) == 'json')) {
                    if (isset($export) && !empty($export->json_file_link)) {
                        force_download($export->json_file_name, file_get_contents($export->json_file_link));
                    }
                } elseif (isset($postdata['file_type']) && (strtolower($postdata['file_type']) == 'xml')) {
                    if (isset($export) && !empty($export->xml_file_link)) {
                        force_download($export->xml_file_name, file_get_contents($export->xml_file_link));
                    }
                }

                redirect('webapp/content/profile/' . $postdata['content_id'], 'refresh');
            } else {
                redirect('webapp/content/', 'refresh');
            }
        }
    }


    /** Upload Content files. This is a Web-client only function **/
    public function upload_docs($content_id)
    {
        if (!empty($content_id)) {
            $postdata   = array_merge(['account_id' => $this->user->account_id], $this->input->post());
            $doc_upload = $this->document_service->upload_files($this->user->account_id, $postdata, $document_group = 'content', $folder = 'content');
            redirect('webapp/content/profile/' . $content_id);
        } else {
            redirect('webapp/content', 'refresh');
        }
    }



    public function delete_document($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = "details");

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data) && !empty($post_data['document_id'])) {
                $postdata                   = [];
                $postdata['account_id']     = $this->user->account_id;
                $postdata['document_id']    = (!empty($post_data['document_id'])) ? $post_data['document_id'] : false ;
                $postdata['doc_group']      = "content";
                $url                        = 'document_handler/delete_document';
                $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                ## d_document = deleted_document
                if (!empty($API_result)) {
                    $return_data['d_document']  = (isset($API_result->status) && ($API_result->status == true)) ? $API_result->d_document : null;
                    $return_data['status']      = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']  = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }

    /*
    *   To submit the file location to decode information from the movie file
    *   File name needs to be changed as no longer valid
    */
    public function submit_file_location($page = "details")
    {
        $section        = (!empty($page)) ? $page : $this->router->fetch_method();
        $return_data    = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = "details");
        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data) && !empty($post_data['file_location'])) {
                $postdata                   = [];
                $postdata['account_id']     = $this->user->account_id;
                $postdata['content_id']     = (!empty($post_data['content_id'])) ? $post_data['content_id'] : false ;
                $postdata['file_location']  = (!empty($post_data['file_location'])) ? $post_data['file_location'] : false ;
                $url                        = 'content/decode_movie_file';
                $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result)) {
                    $return_data['decoded_streams'] = (isset($API_result->status) && ($API_result->status == true)) ? $API_result->decoded_file_info : null;
                    $return_data['status']          = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']      = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']      = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }


    public function getstreaminfo()
    {
        $get_data = [];
        $get_data['woj'] = '';

        $get_data = $this->input->get();

        if (!empty($get_data['path'] && (isset($get_data['woj']) && $get_data['woj'] == "cup"))) {
            if (!empty($get_data['path'])) {
                $vid_info = ($this->get_video_info($get_data['path']));
            }
        // debug( $vid_info, "print", false );
        } else {
            $return_data['status_msg'] = "No data submitted;";
            echo "not submitted";
        }
    }

    /*
    *   Function to decode streams from the movie file
    */
    private function get_video_info($video_file = false, $output_type = "json")
    {
        $result = false;
        if (!empty($video_file)) {
            ## set local variables
            setlocale(LC_CTYPE, "en_GB.UTF-8");

            $path               = getcwd();
            $ffprobe_path       = $path . '\assets\ffmpeg\ffprobe.exe';
            $ffmpeg_path        = $path . '\assets\ffmpeg\ffmpeg.exe';

            ## validate user input
            $video  = escapeshellcmd(escapeshellarg($video_file));

            ## prepare the ffprobe command
            $ffprobe_cmd    =  $ffprobe_path . " -v quiet -print_format json -show_format -show_streams " . $video . " 2>&1";

            ## start processing
            ob_start();
            passthru($ffprobe_cmd);
            $ffmpeg_output = ob_get_contents();
            ob_end_clean();

            ## if file found just return values
            if (sizeof($ffmpeg_output) != null) {
                switch ($output_type) {
                    case "array":
                        $result = json_decode($ffmpeg_output);
                        break;
                    default:
                        $result = $ffmpeg_output;
                }
            }
        }
        return $result;
    }



    public function synchronize_availability_windows($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = "details");

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data['content_id'])) {
                $data['av_windows_2b_created']  = $postdata = [];
                $postdata['account_id']         = $this->user->account_id;
                $postdata['content_id']         = $post_data['content_id'] ;
                $postdata['where']['synchronize']   = 'yes' ;
                $url                            = 'content/synchronize_availability_windows';
                $API_result                     = $this->ssid_common->api_call($url, $postdata, $method = 'GET');

                if (!empty($API_result)) {
                    $return_data['av_windows']      = (isset($API_result->status) && ($API_result->status == true)) ? $API_result->availability_windows : null;
                    $return_data['status']          = (isset($API_result->status)) ? $API_result->status : false ;
                    $return_data['status_msg']      = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']      = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }



    public function add_media_to_airtime($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = "details");

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data['content_id'] && $post_data['chkdDocument'] && $post_data['action'])) {
                $data['media']  = $postdata = [];
                $postdata['account_id']     = $this->user->account_id;
                $postdata['content_id']     = $post_data['content_id'] ;
                $postdata['action']         = $post_data['action'] ;
                $postdata['data']           = $post_data['chkdDocument'];
                $url                        = 'content/media_to_airtime';
                $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result) && ((isset($API_result->status) && ($API_result->status == true)) && (!empty($API_result->media)))) {
                    $return_data['media']           = $API_result->media;
                    $return_data['status']          = $API_result->status;
                    $return_data['status_msg']      = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']      = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "Required Data missing";
            }
        }

        print_r(json_encode($return_data));
        die();
    }

    public function add_media_to_aws($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        if (!$this->identity()) {
            $return_data['message'] = 'Access denied! Please login';
        }

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = "details");

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();

            if (!empty($post_data['content_id']) && ($post_data['movie_data'] || $post_data['img_sub_data'])) {
                $data['media']  = $postdata = [];
                $postdata['account_id']     = $this->user->account_id;
                $postdata['content_id']     = $post_data['content_id'] ;
                $postdata['movie_data']     = (!empty($post_data['movie_data'])) ? $post_data['movie_data'] : '' ;
                $url                        = 'content/media_to_aws';
                $API_result                 = $this->ssid_common->api_call($url, $postdata, $method = 'POST');

                if (!empty($API_result) && ((isset($API_result->status) && ($API_result->status == true)) && (!empty($API_result->media)))) {
                    $return_data['media']           = $API_result->media;
                    $return_data['status']          = $API_result->status;
                    $return_data['status_msg']      = (isset($API_result->message)) ? $API_result->message : 'Request completed!';
                } else {
                    $return_data['status_msg']      = (isset($API_result->message)) ? $API_result->message : 'Request completed but unsuccessful!';
                }
            } else {
                $return_data['status_msg'] = "Required Data missing";
            }
        }

        print_r(json_encode($return_data));
        die();
    }



    /* Single-Use functions */
    /*
    *   Upload Content and Assets from Live Easel into CaCTI - data migration
    *   Expected a CSV spreadsheet with the columns:
    *   id, reference, type, name, state, description, duration, trailer, feature
    */
    public function su_content_upload($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        ## Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!in_array((int) $this->user->id, [1, 4, 6])) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $account_id     = $this->user->account_id;
            $post_data      = $this->input->post();

            if (!empty($post_data['uploaded'])) {
                $process_file   = $this->content_service->su_upload_content($account_id);
                if ($process_file) {
                    redirect('/webapp/content/su_review/' . $account_id);
                }
            }

            $data[] = false;
            $this->_render_webpage('content/includes/su_content_upload', $data);
        }
    }


    /**
    *   SU Review Content Upload
    **/
    public function su_review($account_id = false)
    {
        if (!empty($account_id)) {
            $su_pending                 = $this->content_service->su_get_pending_upload_records($account_id);
            $data['pending']            = (!empty($su_pending)) ? $su_pending : null;

            $this->_render_webpage('content/includes/su_pending_creation', $data);
        }
    }


    /* Single-Use functions */
    /*
    *   Upload Decoded Files updated by Techlive into CaCTI - data migration
    *   Expected a CSV spreadsheet with the columns:
    *   content_id, file_id, file_new_name, file_type, airtime_reference, easel_product_ref, airtime_state
    */
    public function su_decoded_files_upload($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        ## Check module-item access
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section);
        if (!in_array((int) $this->user->id, [1, 4, 6])) {
            $this->_render_webpage('errors/access-denied', false);
        } else {
            $account_id     = $this->user->account_id;
            $post_data      = $this->input->post();

            if (!empty($post_data['uploaded'])) {
                $process_file   = $this->content_service->su_upload_decoded_files($account_id);
                if ($process_file) {
                    redirect('/webapp/content/su_decoded_files_review/' . $account_id);
                }
            }

            $data[] = false;
            $this->_render_webpage('content/includes/su_decoded_files_upload', $data);
        }
    }


    /**
    *   SU Review Decoded Files Upload
    **/
    public function su_decoded_files_review($account_id = false, $number_of_record = false)
    {
        if (!empty($account_id)) {
            $su_pending                 = $this->content_service->su_get_pending_decoded_files_upload($account_id, (int) $number_of_record);
            $data['pending']            = (!empty($su_pending)) ? $su_pending : null;
            $this->_render_webpage('content/includes/su_decoded_files_pending_creation', $data);
        } else {
            redirect('/webapp/content/');
        }
    }


    /*
    *   Single Use function to process uploaded Decoded Files (from the Live Easel)
    */
    public function su_process_decoded_files($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = "details");

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data)) {
                $account_id         = $this->user->account_id;
                $API_result         = $this->content_service->su_process_decoded_files($account_id, $post_data);

                if (!empty($API_result->success) && ($API_result->success == true)) {
                    $return_data['processed']       = (isset($API_result->data) && !empty($API_result->data)) ? $API_result->data : null ;
                    $return_data['status']          = 1;
                    $return_data['status_msg']      = (isset($API_result->message) && !empty($API_result->message)) ? $API_result->message : null ;
                } else {
                    $return_data['status_msg']      = (!empty($API_result->message)) ? $API_result->message : 'There was an error processing your request';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }



    /*
    *   Single Use function
    *   This takes data from Content_and_assets_from_live_easel spreadsheet and after the confirmation
    *   will update movie (product) profile with Easel id obtained from the spreadsheet.
    */
    public function su_update_movies($page = "details")
    {
        $section = (!empty($page)) ? $page : $this->router->fetch_method();

        $return_data = [
            'status' => 0
        ];

        # Check module-item access for the logged in user
        $item_access = $this->webapp_service->check_access($this->user, $this->module_id, $section = "details");

        if (!$this->user->is_admin && empty($item_access->can_add) && empty($item_access->is_admin)) {
            $return_data['status_msg'] = $this->config->item('ajax_access_denied');
        } else {
            $post_data = $this->input->post();
            if (!empty($post_data)) {
                $account_id         = $this->user->account_id;
                $API_result         = $this->content_service->su_update_movies($account_id, $post_data);

                if (!empty($API_result->success) && ($API_result->success == true)) {
                    $return_data['processed']       = (isset($API_result->data) && !empty($API_result->data)) ? $API_result->data : null ;
                    $return_data['status']          = 1;
                    $return_data['status_msg']      = (isset($API_result->message) && !empty($API_result->message)) ? $API_result->message : null ;
                } else {
                    $return_data['status_msg']      = (!empty($API_result->message)) ? $API_result->message : 'There was an error processing your request';
                }
            } else {
                $return_data['status_msg'] = "No data submitted;";
            }
        }

        print_r(json_encode($return_data));
        die();
    }
}
