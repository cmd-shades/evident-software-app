<?php

namespace Application\Modules\Service\Models;

use System\Core\CI_Model;

class Easel_Api_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->easel_endpoint = EASEL_TV_API_BASE_URL;
    }

    private $price_band_type    = ['rental', 'rental-midday', 'subscription'];

    /** Check Server Connection to the Easel TV Server **/
    public function check_connection($account_id = false)
    {
        $result = false;
        if (!empty($account_id)) {
            $result = $this->easel_endpoint;
        }
        return $result;
    }

    /** Get Audio Track **/
    public function get_audio_track($account_id = false)
    {
        $result = false;
        if (!empty($account_id)) {
            $result = $this->easel_endpoint;
        }
        return $result;
    }

    /** Get Audio Track **/
    public function get_media_file_format($account_id = false)
    {
        $result = false;
        if (!empty($account_id)) {
            $result = $this->easel_tv_common->api_dispatcher('media-file-format', false, false, true);
            if (!empty($result)) {
                $this->session->set_flashdata('message', 'Media File Format data found');
            } else {
                $this->session->set_flashdata('message', 'No data found');
            }
        }
        return $result;
    }

    //**========= START PRODUCT API CALLS COLLECTION =========**//

    /** Create a new Product / Film Content **/
    public function create_product($account_id = false, $postdata = false)
    {
        $result = (object)[
            'data'   => false,
            'success' => false,
            'message' => ''
        ];

        if (!empty($account_id) && !empty($postdata)) {
            $url_endpoint   = 'product';
            $method_type    = 'POST';
            $data           = [
                'id'                => (!empty($postdata['external_content_ref'])) ? $postdata['external_content_ref'] : $this->ssid_common->create_guid(),
                'reference'         => (!empty($postdata['asset_code'])) ? $postdata['asset_code'] : '',
                'type'              => (!empty($postdata['type']) && in_array($postdata['type'], ['episode', 'channel', 'live-event'])) ? $postdata['type'] : 'generic', //[ generic, episode, channel
                'name'              => (!empty($postdata['title'])) ? $postdata['title'] : '',
                'state'             => (!empty($postdata['airtime_state'])) ? strtolower($postdata['airtime_state']) : 'offline',
                'shortDescription'  => (!empty($postdata['tagline'])) ? html_entity_decode($postdata['tagline']) : '',
                'description'       => (!empty($postdata['plot'])) ? html_entity_decode($postdata['plot']) : '',
                'duration'          => (!empty($postdata['running_time'])) ? minutes_to_iso8601_duration($postdata['running_time']) : '',
                'country'           => (!empty($postdata['country'])) ? $postdata['country'] : 'GBR',
                'released'          => (!empty($postdata['release_date'])) ? convert_date_to_iso8601($postdata['release_date']) : '',
                'parentalAdvisory'  => '',
                'categories'        => (!empty($postdata['categories'])) ? $postdata['categories'] : [] ,
                'ageRatings'        => (!empty($postdata['ageRatings'])) ? $postdata['ageRatings'] : [] ,
            ];

            $easel_post = $this->easel_tv_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);
            if (empty($easel_post->data->elements) && (!(isset($easel_post->key) && (strpos($easel_post->key, "error") !== false)))) {
                $result->data    = $easel_post;
                $result->success = true;
                $result->message = 'Product created successfully';
                $this->session->set_flashdata('message', 'Product created successfully');
            } else {
                $error_message = "EASEL:";
                // $error_message = !empty( $easel_post->name )         ? $error_message.' '.$easel_post->name : $error_message;
                $error_message = !empty($easel_post->description) ? $error_message . ' ' . $easel_post->description : '';
                // $error_message = !empty( $easel_post->key )          ? $error_message.' | '.$easel_post->key : '';
                $error_message = !empty($error_message) ? $error_message : 'Error: Product create to EASEL API failed!';

                $result->data    = $easel_post->data;
                $result->success = false;
                $result->message = $error_message;

                $this->session->set_flashdata('message', $error_message);
            }
        }
        return $result;
    }


    /** GET Product / Film Content **/
    public function fetch_product($account_id = false, $product_id = false)
    {
        $result = false;
        if (!empty($account_id)) {
            $url_endpoint = 'product/';
            if (!empty($product_id)) {
                $url_endpoint .= $product_id;
            }

            $easel_post = $this->easel_tv_common->api_dispatcher($url_endpoint, false, ['method' => 'GET']);

            if (!empty($easel_post->id) && (!(isset($easel_post->key) && (strpos($easel_post->key, "error") !== false)))) {
                $result = $easel_post;
                $this->session->set_flashdata('message', 'Product(s) data found');
            } else {
                $error_message = !empty($easel_post->name) ? $easel_post->name : '';
                $error_message = !empty($easel_post->description) ? $error_message . ' | ' . $easel_post->description : '';
                // $error_message = !empty( $easel_post->key )          ? $error_message.' | '.$easel_post->key : '';
                $error_message = !empty($error_message) ? $error_message : 'Error: No data found';
                $this->session->set_flashdata('message', $error_message);
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information!');
        }

        return $result;
    }

    /** Update Product Details / Film Content **/
    public function update_product($account_id = false, $product_id = false, $postdata = false)
    {
        $result = (object)[
            'data'   => false,
            'success' => false,
            'message' => ''
        ];


        if (!empty($account_id) && !empty($product_id) && !empty($postdata)) {
            $url_endpoint   = 'product/' . $product_id;
            $data           = [
                // 'id'             => $product_id,
                'reference'         => (!empty($postdata['asset_code'])) ? $postdata['asset_code'] : ((!empty($postdata['reference'])) ? $postdata['reference'] : ''),
                'type'              => (!empty($postdata['type']) && in_array($postdata['type'], ['episode', 'channel', 'live-event'])) ? $postdata['type'] : 'generic', //[ generic, episode, channel
                'name'              => (!empty($postdata['name'])) ? $postdata['name'] : ((!empty($postdata['title'])) ? $postdata['title'] : '') ,
                'state'             => (!empty($postdata['state'])) ? strtolower($postdata['state']) : 'offline',

                // 'shortDescription'   => ( !empty( $postdata['tagline'] ) )           ? htmlspecialchars_decode( str_replace( "&pound;", "£", $postdata['tagline'] ) )    : ( ( !empty( $postdata['shortDescription'] ) ) ? htmlspecialchars_decode( str_replace( "&pound;", "£", $postdata['shortDescription'] ) ) : '' ),
                'shortDescription'  => (!empty($postdata['tagline'])) ? html_entity_decode($postdata['tagline']) : ((!empty($postdata['shortDescription'])) ? html_entity_decode($postdata['shortDescription']) : ''),

                ## Easel want the entities - decoded - £ instead of &pound;
                'description'       => (!empty($postdata['plot'])) ? html_entity_decode($postdata['plot']) : ((!empty($postdata['description'])) ? html_entity_decode($postdata['description']) : ''),
                'duration'          => (!empty($postdata['running_time'])) ? minutes_to_iso8601_duration($postdata['running_time']) : ((!empty($postdata['duration'])) ? $postdata['duration'] : ''),
                // 'country'        => ( !empty( $postdata['country'] ) )   ? $postdata['country'] : 'GBR', ## found by accident Easel don't take it no more  (12/07/2022)
                'released'          => (!empty($postdata['release_date'])) ? convert_date_to_iso8601($postdata['release_date']) : ((!empty($postdata['released'])) ? $postdata['released'] : ''),
                'parentalAdvisory'  => (!empty($postdata['parentalAdvisory'])) ? $postdata['parentalAdvisory'] : '' ,
                'categories'        => (!empty($postdata['categories'])) ? $postdata['categories'] : [] ,
                'ageRatings'        => (!empty($postdata['ageRatings'])) ? $postdata['ageRatings'] : [] ,
                'indexable'         => (!empty($postdata['indexable'])) ? $postdata['indexable'] : false ,
                'episodeNumber'     => (!empty($postdata['episodeNumber'])) ? $postdata['episodeNumber'] : false
            ];

            if (isset($postdata['published']) && !empty($postdata['published'])) {
                $data["published"] = $postdata['published'];
            }

            if (isset($postdata['trailer']) && !empty($postdata['trailer'])) {
                $data["trailer"] = $postdata['trailer'];
            }

            if (isset($postdata['feature']) && !empty($postdata['feature'])) {
                $data["feature"] = $postdata['feature'];
            }

            if ((isset($postdata['image']['master']['imageId']) && !empty($postdata['image']['master']['imageId']))) {
                $data['image']['master']['imageId'] = $postdata['image']['master']['imageId'];
            }

            if ((isset($postdata['image']['hero']['master']['imageId']) && !empty($postdata['image']['hero']['master']['imageId']))) {
                $data['image']['hero']['master']['imageId'] = $postdata['image']['hero']['master']['imageId'];
            }

            ## intentionally intended for the clarity of the code as a subtype of hero
            if ((isset($postdata['image']['hero']['5:4']['imageId']) && !empty($postdata['image']['hero']['5:4']['imageId']))) {
                $data['image']['hero']['5:4']['imageId'] = $postdata['image']['hero']['5:4']['imageId'];
            }

            if ((isset($postdata['image']['hero']['16:9']['imageId']) && !empty($postdata['image']['hero']['16:9']['imageId']))) {
                $data['image']['hero']['16:9']['imageId'] = $postdata['image']['hero']['16:9']['imageId'];
            }

            if ((isset($postdata['image']['hero']['16:7']['imageId']) && !empty($postdata['image']['hero']['16:7']['imageId']))) {
                $data['image']['hero']['16:7']['imageId'] = $postdata['image']['hero']['16:7']['imageId'];
            }

            if ((isset($postdata['image']['hero']['2:3']['imageId']) && !empty($postdata['image']['hero']['2:3']['imageId']))) {
                $data['image']['hero']['2:3']['imageId'] = $postdata['image']['hero']['2:3']['imageId'];
            }

            if ((isset($postdata['image']['poster']['master']['imageId']) && !empty($postdata['image']['poster']['master']['imageId']))) {
                $data['image']['poster']['master']['imageId'] = $postdata['image']['poster']['master']['imageId'];
            }

            if ((isset($postdata['image']['thumb']['master']['imageId']) && !empty($postdata['image']['thumb']['master']['imageId']))) {
                $data['image']['thumb']['master']['imageId'] = $postdata['image']['thumb']['master']['imageId'];
            }

            ## intentionally intended for the clarity of the code as a subtype of thumb
            if ((isset($postdata['image']['thumb']['4:3']['imageId']) && !empty($postdata['image']['thumb']['4:3']['imageId']))) {
                $data['image']['thumb']['4:3']['imageId'] = $postdata['image']['thumb']['4:3']['imageId'];
            }

            if ((isset($postdata['image']['thumb']['16:9']['imageId']) && !empty($postdata['image']['thumb']['16:9']['imageId']))) {
                $data['image']['thumb']['16:9']['imageId'] = $postdata['image']['thumb']['16:9']['imageId'];
            }

            if ((isset($postdata['image']['thumb']['2:3']['imageId']) && !empty($postdata['image']['thumb']['2:3']['imageId']))) {
                $data['image']['thumb']['2:3']['imageId'] = $postdata['image']['thumb']['2:3']['imageId'];
            }

            if (EASEL_UPDATE_DEBUG) {
                $debug_data = [
                    "product_id"    => $product_id,
                    "request_type"  => "easel product update - input",
                    "request_data"  => json_encode($data),
                ];
                $this->db->insert("tmp_easel_update_debugging", $debug_data);
            }

            $easel_post = false;
            $easel_post = $this->easel_tv_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => 'PUT']);

            if (EASEL_UPDATE_DEBUG) {
                $debug_data = [
                    "product_id"    => $product_id,
                    "request_type"  => "easel product update - output",
                    "request_data"  => json_encode($easel_post),
                ];
                $this->db->insert("tmp_easel_update_debugging", $debug_data);
            }
            if (!empty($easel_post->id) && empty($easel_post->data->elements) && (!(isset($easel_post->key) && (strpos($easel_post->key, "error") !== false)))) {
                $result->data    = $easel_post;
                $result->success = true;
                $result->message = 'Product updated successfully';

                $this->session->set_flashdata('message', 'Product updated successfully');
            } else {
                $error_message = !empty($easel_post->name) ? $easel_post->name : '';
                $error_message = !empty($easel_post->description) ? $error_message . ' | ' . $easel_post->description : '';
                $error_message = !empty($easel_post->key) ? $error_message . ' | ' . $easel_post->key : '';
                $error_message = !empty($error_message) ? $error_message : 'Error: Product update to EASEL API failed';

                $result->data    = (!empty($easel_post->data)) ? $easel_post->data : '' ;
                $result->success = false;
                $result->message = $error_message;

                $this->session->set_flashdata('message', $error_message);
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information!');
        }
        return $result;
    }


    //**========= START PRICE BANDS =========**//

    /**
    *   Create a new Price Band
    */
    public function create_price_band($account_id = false, $postdata = false)
    {
        $result = (object)[
            'data'   => false,
            'success' => false,
            'message' => ''
        ];

        if (!empty($account_id) && !empty($postdata)) {
            $url_endpoint   = 'price-band';
            $method_type    = 'POST';
            $data           = [
                'id'        => (!empty($postdata['id'])) ? $postdata['id'] : $this->ssid_common->create_guid(),
                'type'      => (!empty($postdata['kind'])) ? $postdata['kind'] : 'rental-midday', ## possible options: $this->price_band_type
                'name'      => (!empty($postdata['title'])) ? str_replace(' ', '_', $postdata['title']) : 'title_not_set',
                'value'     => (!empty($postdata['price'])) ? intval(number_format($postdata['price'] * 100, 0, null, '')) : ((!empty($postdata['value'])) ? intval(number_format($postdata['value'] * 100, 0, null, '')) : 0),       // now is mandatory (02/02/2021)
                'currency'  => (!empty($postdata['currency'])) ? $postdata['currency'] : 'GBP',  // now is mandatory (02/02/2021)
            ];

            $easel_post = $this->easel_tv_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);

            if (empty($easel_post->data->elements) && (!(isset($easel_post->key) && (strpos($easel_post->key, "error") !== false)))) {
                $result->data    = $easel_post;
                $result->success = true;
                $result->message = 'Price Band created successfully';
                $this->session->set_flashdata('message', 'Price Band created successfully');
            } else {
                $error_message = !empty($easel_post->name) ? $easel_post->name : '';
                $error_message = !empty($easel_post->description) ? $error_message . ' | ' . $easel_post->description : '';
                $error_message = !empty($easel_post->key) ? $error_message . ' | ' . $easel_post->key : '';
                $error_message = !empty($error_message) ? $error_message : 'Error: Price Band creation to EASEL API failed!';

                $result->data    = $easel_post->data;
                $result->success = false;
                $result->message = $error_message;

                $this->session->set_flashdata('message', $error_message);
            }
        }
        return $result;
    }

    /** GET Price Bands **/
    public function fetch_price_band($account_id = false, $price_band_id = false)
    {
        $result = false;
        if (!empty($account_id)) {
            $url_endpoint = 'price-band/';
            if (!empty($price_band_id)) {
                $url_endpoint .= $price_band_id;
            }

            $result = $this->easel_tv_common->api_dispatcher($url_endpoint, false, ['method' => 'GET']);

            if (!empty($result)) {
                $this->session->set_flashdata('message', 'Price Band(s) data found');
            } else {
                $this->session->set_flashdata('message', 'No data found');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information!');
        }
        return $result;
    }

    /** Update Price Band **/
    public function update_price_band($account_id = false, $price_band_id = false, $postdata = false)
    {
        $result = (object)[
            'data'   => false,
            'success' => false,
            'message' => ''
        ];

        if (!empty($account_id) && !empty($price_band_id) && !empty($postdata)) {
            ## get the price band b4 update data
            $price_band_b4_update = $this->fetch_price_band($account_id, $price_band_id);

            if (!empty($price_band_b4_update->kind) && !empty($price_band_b4_update->title)) {
                // do the update otherwise (if kind or title are empty) it will fail
                $url_endpoint   = 'price-band/' . $price_band_id;

                $data = [];
                $data['id']             = $price_band_id;
                if (!empty($postdata['kind'])) {
                    if (in_array($postdata['kind'], $price_band_type)) {
                        $data['type']   = $postdata['kind'];
                    }
                } else {
                    $data['type']       = $price_band_b4_update->kind;
                }

                if (!empty($postdata['title'])) {
                    $data['name']       = str_replace(' ', '_', $postdata['title']) ;
                } else {
                    $data['name']       = $price_band_b4_update->title;
                }

                if (!empty($postdata['price'])) {
                    $data['value']      = (int) (number_format($postdata['price'], 2, ".", " ") * 100);
                } elseif ($postdata['value']) {
                    $data['value']      = (int) (number_format($postdata['value'], 2, ".", " ") * 100);
                } else {
                    $data['value']      = 0;        ## this is now mandatory field
                }

                if (!empty($postdata['currency'])) {
                    $data['currency']   = $postdata['currency'];
                } else {
                    $data['currency']   = "GBP";    ## this is now mandatory field
                }

                $easel_post = $this->easel_tv_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => 'PUT']);

                if (empty($easel_post->data->elements) && (!(isset($easel_post->key) && (strpos($easel_post->key, "error") !== false)))) {
                    $result->data    = $easel_post;
                    $result->success = true;
                    $result->message = 'Price Band details updated successfully';

                    $this->session->set_flashdata('message', 'Price Band details updated successfully');
                } else {
                    $error_message = !empty($easel_post->name) ? $easel_post->name : '';
                    $error_message = !empty($easel_post->description) ? $error_message . ' | ' . $easel_post->description : '';
                    $error_message = !empty($easel_post->key) ? $error_message . ' | ' . $easel_post->key : '';
                    $error_message = !empty($error_message) ? $error_message : 'Error: Price Band update to EASEL API failed!';

                    $result->data    = $easel_post->data;
                    $result->success = false;
                    $result->message = $error_message;

                    $this->session->set_flashdata('message', $error_message);
                }
            } else {
                $error_message = 'Error: Missing Kind or Title from the current Price Band!';

                $result->data    = false;
                $result->success = false;
                $result->message = $error_message;

                $this->session->set_flashdata('message', $error_message);
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information!');
        }
        return $result;
    }


    /**
    *   Delete Price Band
    **/
    public function delete_price_band($account_id = false, $price_band_id = false, $force = false)
    {
        $result = (object)[
            'data'      => false,
            'success'   => false,
            'message'   => ''
        ];

        if (!empty($account_id) && !empty($price_band_id)) {
            ## get the price band b4 deleting
            $price_band_b4_deleting = false;
            $price_band_b4_deleting = $this->fetch_price_band($account_id, $price_band_id);

            if (isset($price_band_b4_deleting) && (isset($price_band_b4_deleting->id) && !empty($price_band_b4_deleting->id))) {
                $url_endpoint   = 'price-band/' . $price_band_id;

                if (!empty($force) && ($force != false)) {
                    $url_endpoint   .= "?force=true";
                }

                if (DELETE_PRICE_BAND_DEBUGGING !== false) {
                    $debug_data = [
                        "product_price_plan_id" => null,
                        "request_type"          => "deleting easel price band - Easel endpoint",
                        "request_data"          => json_encode($url_endpoint),
                    ];
                    $this->db->insert("tmp_delete_price_band_debugging", $debug_data);
                }

                $easel_post = $this->easel_tv_common->api_dispatcher($url_endpoint, false, ['method' => 'DELETE']);

                if (DELETE_PRICE_BAND_DEBUGGING !== false) {
                    $debug_data = [
                        "product_price_plan_id" => null,
                        "request_type"          => "deleting easel price band - Easel response",
                        "request_data"          => json_encode($easel_post),
                    ];
                    $this->db->insert("tmp_delete_price_band_debugging", $debug_data);
                }

                if (empty($easel_post->data->elements) && (!(isset($easel_post->key) && (strpos($easel_post->key, "error") !== false)))) {
                    // $result->data     = $easel_post; // there is no data - it is just 'true'
                    $result->success = true;
                    $result->message = 'Price Band details deleted successfully';
                    $this->session->set_flashdata('message', 'Price Band details deleted successfully');
                } else {
                    $error_message = !empty($easel_post->name) ? $easel_post->name : '';
                    $error_message = !empty($easel_post->description) ? $error_message . ' | ' . $easel_post->description : '';
                    $error_message = !empty($easel_post->key) ? $error_message . ' | ' . $easel_post->key : '';
                    $error_message = !empty($error_message) ? $error_message : 'Error: Price Band delete to EASEL API failed!';

                    $result->data    = (!empty($easel_post->data)) ? $easel_post->data : '' ;
                    $result->message = $error_message;

                    $this->session->set_flashdata('message', $error_message);
                }
            } else {
                $error_message      = "Price Band doesn't exists on CaCTI!";
                $result->message    = $error_message;
                $this->session->set_flashdata('message', $error_message);
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information!');
        }
        return $result;
    }



    //**========= END PRICE BANDS =========**//



    //**========= START MARKETS =========**//
    /**
    *   Create a new Market
    **/
    public function create_market($account_id = false, $postdata = false)
    {
        $result = (object)[
            'data'   => false,
            'success' => false,
            'message' => ''
        ];

        if (!empty($account_id) && !empty($postdata)) {
            $url_endpoint   = 'market';
            $method_type    = 'POST';
            $data           = [
                'id'                => (!empty($postdata['external_site_ref'])) ? (string) $postdata['external_site_ref'] : (string) $this->ssid_common->create_guid(),
                'name'              => (!empty($postdata['name'])) ? trim($postdata['name']) : '',
                'description'       => (!empty($postdata['description'])) ? trim($postdata['description']) : '',
                'ordering'          => (!empty($postdata['ordering'])) ? ((int) $postdata['ordering']) : '99',
            ];





            if (!empty($postdata['expression'])) {
                $data['expression'] = $postdata['expression'];
            }

            // Easel introduced a new way of adding the 'expressions'. This code below has been commented out on 2/9/2022. Left for the future reference.
            // Delete after a few months with no issues.
            // if( !empty( $postdata['expression']['segmentId'] ) ){
            // $data['expression']  = array(
            // "operator" => 'and'
            // );
            // if( is_array( $postdata['expression']['segmentId'] ) ){
            // foreach( $postdata['expression']['segmentId'] as $segment_id ){
            // $data['expression']['operands'][] = ( object ) ["segmentId" => $segment_id];
            // }
            // } else {
            // $data['expression']['operands'] = [( object ) ['segmentId' => $postdata['expression']['segmentId']]];
            // }
            // }

            log_message("error", json_encode(["create market data" => $data]));
            $easel_post = $this->easel_tv_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);
            log_message("error", json_encode(["create market easel post" => $easel_post]));

            if (empty($easel_post->data->elements) && (!(isset($easel_post->key) && (strpos($easel_post->key, "error") !== false)))) {
                $result->data       = $easel_post;
                $result->success    = true;
                $result->message    = 'Market created successfully';
                $this->session->set_flashdata('message', 'Market created successfully');
            } else {
                $error_message      = '';
                $error_message      = !empty($easel_post->name) ? $easel_post->name : '';
                $error_message      = !empty($easel_post->description) ? $error_message . ' | ' . $easel_post->description : '';
                $error_message      = !empty($easel_post->key) ? $error_message . ' | ' . $easel_post->key : '';
                $error_message      = !empty($error_message) ? $error_message : 'Error: Market creation on EASEL API failed!';

                $result->data       = (!empty($easel_post->data)) ? $easel_post->data : '' ;
                $result->success    = false;
                $result->message    = $error_message;

                $this->session->set_flashdata('message', $error_message);
            }
        }
        return $result;
    }


    /**
    *   Update a Site / Market
    **/
    public function update_market($account_id = false, $market_id = false, $market_data = false)
    {
        $result = (object)[
            'data'   => false,
            'success' => false,
            'message' => ''
        ];

        $market_data = convert_to_array($market_data);

        if (!empty($account_id) && !empty($market_id) && !empty($market_data)) {
            $url_endpoint   = 'market/' . ((string) $market_id);
            $method_type    = 'PUT';
            $data           = [
                'id'                => (string) $market_id,
                'name'              => (!empty($market_data['name'])) ? trim($market_data['name']) : '',
                'description'       => (!empty($market_data['description'])) ? trim($market_data['description']) : '',
                'ordering'          => (isset($market_data['ordering'])) ? ((int) $market_data['ordering']) : 99,
            ];

            if (!empty($market_data['expression'])) {
                $data['expression'] = ($market_data['expression']);
            }

            $easel_post = $this->easel_tv_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);

            if (empty($easel_post->data->elements) && (!(isset($easel_post->key) && (strpos($easel_post->key, "error") !== false)))) {
                $result->data       = $easel_post;
                $result->success    = true;
                $result->message    = 'Market updated successfully. ';
                $this->session->set_flashdata('update_market_message', 'Market updated successfully. ');
            } else {
                $error_message      = '';
                $error_message      = !empty($easel_post->name) ? $easel_post->name : '';
                $error_message      = !empty($easel_post->description) ? $error_message . ' | ' . $easel_post->description : '';
                $error_message      = !empty($easel_post->key) ? $error_message . ' | ' . $easel_post->key : '';
                $error_message      = !empty($error_message) ? $error_message : 'Error: Market update on EASEL API failed!';

                $result->data       = (!empty($easel_post->data)) ? $easel_post->data : '' ;
                $result->success    = false;
                $result->message    = $error_message;

                $this->session->set_flashdata('update_market_message', $error_message);
            }
        }
        return $result;
    }



    /**
    *   GET Market Data
    **/
    public function fetch_market($account_id = false, $id = false)
    {
        $result = false;
        if (!empty($account_id)) {
            $url_endpoint = 'market/';
            if (!empty($id)) {
                $url_endpoint .= $id;
            }

            $market_easel = $this->easel_tv_common->api_dispatcher($url_endpoint, false, ['method' => 'GET']);

            if (empty($market_easel->data) && (!(isset($market_easel->key) && (strpos($market_easel->key, "error") !== false))) && !empty($market_easel->id)) {
                $result = $market_easel;
                $this->session->set_flashdata('message', 'Market data found');
            } else {
                $this->session->set_flashdata('message', 'No data found');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information!');
        }

        return $result;
    }
    //**========= END MARKETS =========**//



    //**========= START AVAILABILITY WINDOWS =========**//

    /**
    *   Create an Availability Window
    **/
    public function create_availability_window($account_id = false, $postdata = false)
    {
        $result = (object)[
            'data'   => false,
            'success' => false,
            'message' => ''
        ];

        if (!empty($account_id) && !empty($postdata)) {
            $url_endpoint   = 'availability-window';
            $method_type    = 'POST';
            $data           = [
                'id'                => (!empty($postdata['id'])) ? $postdata['id'] : $this->ssid_common->create_guid(),
                'productId'         => (!empty($postdata['productId'])) ? $postdata['productId'] : '',
                'visibleFrom'       => (!empty($postdata['visibleFrom'])) ? $postdata['visibleFrom'] : '',
                'visibleTo'         => (!empty($postdata['visibleTo'])) ? $postdata['visibleTo'] : '',
                'priceBandId'       => (!empty($postdata['priceBandId'])) ? $postdata['priceBandId'] : '',
                'marketId'          => (!empty($postdata['marketId'])) ? $postdata['marketId'] : '',
                'billing'           => [
                    'category'          => (!empty($postdata['billing']['category'])) ? $postdata['billing']['category'] : "Library",
                    'revenueShare'      => (!empty($postdata['billing']['revenueShare'])) ? $postdata['billing']['revenueShare'] : 0,
                    'wholesalePrice'    => (!empty($postdata['billing']['wholesalePrice'])) ? $postdata['billing']['wholesalePrice'] : 0,
                ],
            ];

            $easel_post = $this->easel_tv_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);

            if (empty($easel_post->data->elements) && (!(isset($easel_post->key) && (strpos($easel_post->key, "error") !== false)))) {
                $result->data    = $easel_post;
                $result->success = true;
                $result->message = 'Availability Window created successfully';
                $this->session->set_flashdata('message', 'Availability Window created successfully');
            } else {
                $error_message = !empty($easel_post->name) ? $easel_post->name : '';
                $error_message = !empty($easel_post->description) ? $error_message . ' | ' . $easel_post->description : '';
                $error_message = !empty($easel_post->key) ? $error_message . ' | ' . $easel_post->key : '';
                $error_message = !empty($error_message) ? $error_message : 'Error: Availability Window create to EASEL API failed!';

                $result->data    = $easel_post->data;
                $result->success = false;
                $result->message = $error_message;

                $this->session->set_flashdata('message', $error_message);
            }
        }
        return $result;
    }


    /**
    *   GET Availability Window Data
    **/
    public function fetch_availability_window($account_id = false, $id = false)
    {
        $result = false;
        if (!empty($account_id)) {
            $url_endpoint = 'availability-window/';
            if (!empty($id)) {
                $url_endpoint .= $id;
            }

            $aw_easel = $this->easel_tv_common->api_dispatcher($url_endpoint, false, ['method' => 'GET']);

            if (empty($aw_easel->data) && (!(isset($aw_easel->key) && (strpos($aw_easel->key, "error") !== false))) && !empty($aw_easel->id)) {
                $result = $aw_easel;
                $this->session->set_flashdata('message', 'Availability Window(s) data found');
            } else {
                $this->session->set_flashdata('message', 'No data found');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information!');
        }
        return $result;
    }


    /**
    *   GET Availability Window(s) using a product (movie) ID
    **/
    public function fetch_availability_window_by_product($account_id = false, $product_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($product_id)) {
            $url_endpoint = 'product/' . $product_id . '/availability-window';

            $aw_easel = $this->easel_tv_common->api_dispatcher($url_endpoint, false, ['method' => 'GET']);

            if ((!(isset($aw_easel->key) && (strpos($aw_easel->key, "error") !== false)) && !empty($aw_easel->data))) {
                $result = $aw_easel;
                $this->session->set_flashdata('message', 'Availability Window(s) data found');
            } else {
                $this->session->set_flashdata('message', 'No data found');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information!');
        }
        return $result;
    }


    /**
    *   GET Availability Window(s) from DB (CaCTi) by the Content ID
    **/
    public function fetch_availability_window_from_cacti($account_id = false, $content_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($content_id)) {
            $this->db->select("*", false);
            $this->db->where("content_id", $content_id);
            $this->db->where("active", 1);
            $query = $this->db->get("availability_window");

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Availability Window(s) data found');
            } else {
                $this->session->set_flashdata('message', 'No data found');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information!');
        }
        return $result;
    }



    /**
    *   DELETE Availability Window
    **/
    public function delete_availability_window($account_id = false, $window_id = false)
    {
        $result = (object)[
            'data'   => false,
            'success' => false,
            'message' => ''
        ];
        if (!empty($account_id) && !empty($window_id)) {
            $url_endpoint = 'availability-window/';
            if (!empty($window_id)) {
                $url_endpoint .= $window_id;
            }

            $delete_availability_window_easel = $this->easel_tv_common->api_dispatcher($url_endpoint, false, ['method' => 'DELETE']);

            if (empty($delete_availability_window_easel->data) && (!(isset($delete_availability_window_easel->key) && (strpos($delete_availability_window_easel->key, "error") !== false)))) {
                $result->data       = $delete_availability_window_easel;
                $result->success    = true;
                $result->message    = 'Availability Windows has been deleted';
                $this->session->set_flashdata('message', 'Availability Windows has been deleted');
            } else {
                $error_message      = !empty($delete_availability_window_easel->name) ? $delete_availability_window_easel->name : '';
                $error_message      = !empty($delete_availability_window_easel->description) ? $error_message . ' | ' . $delete_availability_window_easel->description : '';
                $error_message      = !empty($delete_availability_window_easel->key) ? $error_message . ' | ' . $delete_availability_window_easel->key : '';
                $error_message      = !empty($error_message) ? $error_message : 'Error: Availability Window delete to EASEL API failed!';

                $result->data       = $delete_availability_window_easel->data;
                $result->success    = false;
                $result->message    = $error_message;
                $this->session->set_flashdata('message', $error_message);
            }
        } else {
            $result->message    = 'Your request is missing required information!';
            $this->session->set_flashdata('message', 'Your request is missing required information!');
        }

        return $result;
    }




    //**========= END AVAILABILITY WINDOWS =========**//


    //**========= START SEGMENT =========**//
    /**
    *   Create a new Segment
    */
    public function create_segment($account_id = false, $postdata = false)
    {
        $result = (object)[
            'data'   => false,
            'success' => false,
            'message' => ''
        ];

        if (!empty($account_id) && !empty($postdata) && !empty($postdata['id'])) {
            $url_endpoint   = 'segment';
            $method_type    = 'POST';
            $data           = [
                ## ID should be a Product PIN. If ID is not provided we shouldn't attempt to create a segment ( 2/7/2021 )
                'id'                => (!empty($postdata['id'])) ? (string) $postdata['id'] : false,
                'name'              => (!empty($postdata['name'])) ? $postdata['name'] : '',
                'description'       => (!empty($postdata['description'])) ? $postdata['description'] : '',

                ## 'type' possible options: [ device, device-list, region, region-list, time-watched ]
                'type'              => (!empty($postdata['type'])) ? $postdata['type'] : 'device-list',

                ## This is required part to create a segment. It is empty for now. It will be updated when adding the devices
                'data'          => [
                    "deviceList"    => [],
                    "operator"      => "contains"
                ],

                // No needed according to the recent changes to Easel API ( 2/7/2021 )
                // 'pin'            => ( !empty( $postdata['pin'] ) )           ? ( int ) $postdata['pin']  : '',
                // 'region'         => ( !empty( $postdata['region'] ) )        ? $postdata['region']       : 'UK',
            ];

            $easel_post = $this->easel_tv_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);

            if (empty($easel_post->data->elements) && (!(isset($easel_post->key) && (strpos($easel_post->key, "error") !== false)))) {
                $result->data    = $easel_post;
                $result->success = true;
                $result->message = 'Segment created successfully';
                $this->session->set_flashdata('message', 'Segment created successfully');
            } else {
                $error_message = !empty($easel_post->name) ? $easel_post->name : '';
                $error_message = !empty($easel_post->description) ? $error_message . ' | ' . $easel_post->description : '';
                $error_message = !empty($easel_post->key) ? $error_message . ' | ' . $easel_post->key : '';
                $error_message = !empty($error_message) ? $error_message : 'Error: Segment create to EASEL API failed!';

                $result->data    = $easel_post->data;
                $result->success = false;
                $result->message = $error_message;

                $this->session->set_flashdata('message', $error_message);
            }
        }
        return $result;
    }



    /*
    *   Update segment call, used also for the device adding (not much difference in requirements from Easel side)
    *   - 'airtime_segment_ref' as Easel: 'segment ID' is required
    */
    public function update_segment($account_id = false, $postdata = false)
    {
        $result = (object)[
            'data'   => false,
            'success' => false,
            'message' => ''
        ];


        $device_debugging_data = [
        "device_id"     => "",
        "product_id"    => "",
        "string_name"   => "Easel UPDATE SEGMENT data - before the check",
        "query_string"  => json_encode($postdata),
        ];
        $this->db->insert("tmp_device_debugging", $device_debugging_data);

        if (!empty($account_id) && !empty($postdata) && !empty($postdata['airtime_segment_ref']) && !empty($postdata['type']) && !empty($postdata['name'])) {
            $url_endpoint   = 'segment/' . $postdata['airtime_segment_ref'];
            $method_type    = 'PUT';
            $data           = [
                'id'                => (string) $postdata['airtime_segment_ref'],
                'type'              => (string) $postdata['type'],
                'name'              => (string) $postdata['name'],
                'description'       => (!empty($postdata['description'])) ? $postdata['description'] : '' ,
                'data'          => [
                    "deviceList"    => !empty($postdata['deviceList']) ? (array) $postdata['deviceList'] : [],
                    "operator"      => "contains"
                ],
            ];

            $easel_post = $this->easel_tv_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);

            $device_debugging_data = [
            "device_id"     => "",
            "product_id"    => "",
            "string_name"   => "easel UPDATE SEGMENT response",
            "query_string"  => json_encode($easel_post),
            ];
            $this->db->insert("tmp_device_debugging", $device_debugging_data);

            if (!empty($easel_post->id) && (!(isset($easel_post->key) && (strpos($easel_post->key, "error") !== false)))) {
                $result->data    = $easel_post;
                $result->success = true;
                $result->message = 'Segment data updated successfully';
                $this->session->set_flashdata('message', 'Segment data updated successfully');
            } else {
                $error_message = !empty($easel_post->name) ? $easel_post->name : '';
                $error_message = !empty($easel_post->description) ? $error_message . ' | ' . $easel_post->description : '';
                $error_message = !empty($easel_post->key) ? $error_message . ' | ' . $easel_post->key : '';
                $error_message = !empty($error_message) ? $error_message : 'Error: Segment data update to EASEL API failed!';

                $result->data    = $easel_post->data;
                $result->success = false;
                $result->message = $error_message;

                $this->session->set_flashdata('message', $error_message);
            }
        } else {
            $result = (object)[
                'data'   => false,
                'success' => false,
                'message' => 'No required data provided'
            ];
        }
        return $result;
    }


    /**
    *   GET Segment Data
    **/
    public function fetch_segment($account_id = false, $id = false)
    {
        $result = false;
        if (!empty($account_id)) {
            $url_endpoint = 'segment/';
            if (!empty($id)) {
                $url_endpoint .= $id;
            }

            $segment_easel = $this->easel_tv_common->api_dispatcher($url_endpoint, false, ['method' => 'GET']);

            if (!empty($segment_easel->id) && (!(isset($segment_easel->key) && (strpos($segment_easel->key, "error") !== false)))) {
                $result = $segment_easel;
                $this->session->set_flashdata('message', 'Segment data found');
            } else {
                $this->session->set_flashdata('message', 'No data found');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information!');
        }
        return $result;
    }

    /**
    *   DELETE Segment Data
    **/
    public function delete_segment($account_id = false, $id = false)
    {
        $result = false;
        if (!empty($account_id)) {
            $url_endpoint = 'segment/';
            if (!empty($id)) {
                $url_endpoint .= $id;
            }

            $segment_easel = $this->easel_tv_common->api_dispatcher($url_endpoint, false, ['method' => 'DELETE']);

            if (empty($segment_easel->data) && (!(isset($segment_easel->key) && (strpos($segment_easel->key, "error") !== false))) && !empty($segment_easel->id)) {
                $result = $segment_easel;
                $this->session->set_flashdata('message', 'Segment data found');
            } else {
                $this->session->set_flashdata('message', 'No data found');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information!');
        }
        return $result;
    }


    //**========= END SEGMENT =========**//




    //**========= START DEVICES =========**//
    /**
    *   Create a new Device
    **/
    public function create_device($account_id = false, $postdata = false)
    {
        $result = (object)[
            'data'   => false,
            'success' => false,
            'message' => ''
        ];

        if (!empty($account_id) && !empty($postdata)) {
            $url_endpoint   = 'device';
            $method_type    = 'POST';
            $data           = [
                'id'                => (!empty($postdata['id'])) ? $postdata['id'] : (string) $this->ssid_common->create_guid(),
                'platform'          => (!empty($postdata['platform'])) ? (string) trim($postdata['platform']) : '',
            ];

            $easel_post = $this->easel_tv_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);

            if (empty($easel_post->data->elements) && (!(isset($easel_post->key) && (strpos($easel_post->key, "error") !== false)))) {
                $result->data       = $easel_post;
                $result->success    = true;
                $result->message    = 'Device created successfully';
                $this->session->set_flashdata('message', 'Device created successfully');
            } else {
                $error_message      = '';
                $error_message      = !empty($easel_post->name) ? $easel_post->name : '';
                $error_message      = !empty($easel_post->description) ? $error_message . ' | ' . $easel_post->description : '';
                $error_message      = !empty($easel_post->key) ? $error_message . ' | ' . $easel_post->key : '';
                $error_message      = !empty($error_message) ? $error_message : 'Error: Device creation on EASEL API failed!';

                $result->data       = (!empty($easel_post->data)) ? $easel_post->data : '' ;
                $result->success    = false;
                $result->message    = $error_message;

                $this->session->set_flashdata('message', $error_message);
            }
        }
        return $result;
    }



    /**
    *   Add a device to a segment - not used (16/07/2021)
    **/
    public function add_device_to_segment($account_id = false, $device_external_id = false, $segment_external_id = false)
    {
        $result = (object)[
            'data'   => false,
            'success' => false,
            'message' => ''
        ];

        if (!empty($account_id) && !empty($segment_external_id)) {
            $url_endpoint   = 'segment/' . $segment_external_id . '/device';
            $method_type    = 'POST';
            $data           = [
                'ids'   => [$device_external_id],
            ];

            $easel_post = $this->easel_tv_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);

            if (!empty($easel_post->data) && ($easel_post->data[0] == $device_external_id)) {
                $result->data       = $easel_post;
                $result->success    = true;
                $result->message    = 'Device added successfully';
                $this->session->set_flashdata('message', 'Device added successfully');
            } else {
                $error_message      = '';
                $error_message      = !empty($easel_post->name) ? $easel_post->name : '';
                $error_message      = !empty($easel_post->description) ? $error_message . ' | ' . $easel_post->description : '';
                $error_message      = !empty($easel_post->key) ? $error_message . ' | ' . $easel_post->key : '';
                $error_message      = !empty($error_message) ? $error_message : 'Error: Adding Device to a segment on EASEL API failed!';

                $result->data       = (!empty($easel_post->data)) ? $easel_post->data : '' ;
                $result->success    = false;
                $result->message    = $error_message;

                $this->session->set_flashdata('message', $error_message);
            }
        }
        return $result;
    }



    /**
    *   GET Device Data
    **/
    public function fetch_device($account_id = false, $id = false)
    {
        $result = false;
        if (!empty($account_id)) {
            $url_endpoint = 'device/';
            if (!empty($id)) {
                $url_endpoint .= $id;
            }

            $device_easel = $this->easel_tv_common->api_dispatcher($url_endpoint, false, ['method' => 'GET']);

            if (!empty($device_easel->id) && (!(isset($device_easel->key) && (strpos($device_easel->key, "error") !== false)))) {
                $result = $device_easel;
                $this->session->set_flashdata('message', 'Device data obtained');
            } else {
                $this->session->set_flashdata('message', 'No device data found');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information!');
        }
        return $result;
    }


    //**========= END DEVICES =========**//





    /**
    *   Create a Category Type (Genre Type)
    **/
    public function create_genre_type($account_id = false, $postdata = false)
    {
        $result = (object)[
            'data'   => false,
            'success' => false,
            'message' => ''
        ];

        if (!empty($account_id) && !empty($postdata)) {
            $url_endpoint   = 'category-type';
            $method_type    = 'POST';
            $data           = [
                'id'            => (!empty($postdata['id'])) ? $postdata['id'] : (string) $this->ssid_common->create_guid(),
                'name'          => (!empty($postdata['name'])) ? (string) trim($postdata['name']) : '',
                'exclusive'     => (!empty($postdata['exclusive'])) ? (bool) ($postdata['exclusive']) : false ,
            ];

            $easel_post = $this->easel_tv_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);

            if (((!isset($easel_post->data)) || empty($easel_post->data->elements)) && (!(isset($easel_post->key) && (strpos($easel_post->key, "error") !== false))) && (!empty($easel_post->id))) {
                $result->data       = $easel_post;
                $result->success    = true;
                $result->message    = 'Genre Type created successfully';
                $this->session->set_flashdata('message', 'Genre Type created successfully');
            } else {
                $error_message      = '';
                $error_message      = !empty($easel_post->name) ? $easel_post->name : '';
                $error_message      = !empty($easel_post->description) ? $error_message . ' | ' . $easel_post->description : '';
                $error_message      = !empty($easel_post->key) ? $error_message . ' | ' . $easel_post->key : '';
                $error_message      = !empty($error_message) ? $error_message : 'Error: Genre Type creation on EASEL API failed!';

                $result->data       = (!empty($easel_post->data)) ? $easel_post->data : '' ;
                $result->success    = false;
                $result->message    = $error_message;

                $this->session->set_flashdata('message', $error_message);
            }
        }

        return $result;
    }


    /**
    *   Create a Category (Genre)
    **/
    public function create_genre($account_id = false, $postdata = false)
    {
        $result = (object)[
            'data'      => false,
            'success'   => false,
            'message'   => ''
        ];

        if (!empty($account_id) && !empty($postdata)) {
            $url_endpoint   = 'category';
            $method_type    = 'POST';
            $data           = [
                'id'                => (!empty($postdata['id'])) ? $postdata['id'] : (string) $this->ssid_common->create_guid(),
                'name'              => (!empty($postdata['name'])) ? (string) trim($postdata['name']) : '',
                'categoryTypeId'    => (!empty($postdata['categoryTypeId'])) ? (string) trim($postdata['categoryTypeId']) : '',
            ];

            $easel_post = $this->easel_tv_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);

            if (((!isset($easel_post->data)) || empty($easel_post->data->elements)) && (!(isset($easel_post->key) && (strpos($easel_post->key, "error") !== false))) && (!empty($easel_post->id))) {
                $result->data       = $easel_post;
                $result->success    = true;
                $result->message    = 'Genre created successfully';
                $this->session->set_flashdata('message', 'Genre created successfully');
            } else {
                $error_message      = '';
                $error_message      = !empty($easel_post->name) ? $easel_post->name : '';
                $error_message      = !empty($easel_post->description) ? $error_message . ' | ' . $easel_post->description : '';
                $error_message      = !empty($easel_post->key) ? $error_message . ' | ' . $easel_post->key : '';
                $error_message      = !empty($error_message) ? $error_message : 'Error: Genre creation on EASEL API failed!';

                $result->data       = (!empty($easel_post->data)) ? $easel_post->data : '' ;
                $result->success    = false;
                $result->message    = $error_message;

                $this->session->set_flashdata('message', $error_message);
            }
        }

        return $result;
    }


    //**========= START IMAGES =========**//
    /**
    *   Create a new Image
    */
    public function create_image($account_id = false, $postdata = false)
    {
        $result = (object)[
            'data'      => false,
            'success'   => false,
            'message'   => ''
        ];

        if (!empty($account_id)) {
            // URl verification
            $verified_url = false;
            $verified_url = check_link_is_image($postdata['url']);

            if (!empty($verified_url)) {
                $url_endpoint   = 'image/binary';
                $method_type    = 'POST';
                $data           = [
                    'id'        => (!empty($postdata['id'])) ? $postdata['id'] : $this->ssid_common->create_guid(),
                    'url'       => $verified_url
                ];

                $easel_post = $this->easel_tv_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);

                if (empty($easel_post->data->elements) && (!(isset($easel_post->key) && (strpos($easel_post->key, "error") !== false)))) {
                    $result->data    = $easel_post;
                    $result->success = true;
                    $result->message = 'Image created successfully';
                    $this->session->set_flashdata('message', 'Image created successfully');
                } else {
                    $error_message = !empty($easel_post->name) ? $easel_post->name : '';
                    $error_message = !empty($easel_post->description) ? $error_message . ' | ' . $easel_post->description : '';
                    $error_message = !empty($easel_post->key) ? $error_message . ' | ' . $easel_post->key : '';
                    $error_message = !empty($error_message) ? $error_message : 'Image creation to EASEL API failed!';

                    // $result->data     = $easel_post->data;
                    $result->success = false;
                    $result->message = $error_message;

                    $this->session->set_flashdata('message', $error_message);
                }
            } else {
                $result->message = 'Invalid URL';
            }
        } else {
            $result->message = 'Missing required data';
        }
        return $result;
    }

    // not live yet - it may not be needed - just prepared for debugginh if will be needed
    // $image_debugging_data = [
        // "image_id"       => "",
        // "product_id"     => "",
        // "string_name"    => "Easel CREATE IMAGE data - before the check",
        // "query_string"   => json_encode( $data ),
    // ];
    // $this->db->insert( "tmp_image_debugging", $image_debugging_data );


    /**
    *   Functionality to delete an Image
    */
    public function delete_image($account_id = false, $image_id = false)
    {
        $result = (object)[
            'data'      => false,
            'success'   => false,
            'message'   => ''
        ];

        if (!empty($account_id) && !empty($image_id)) {
            $url_endpoint   = 'image/' . $image_id;
            $method_type    = 'DELETE';
            $data           = [];

            $easel_post = $this->easel_tv_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);

            if ((!(isset($easel_post->key) && (strpos($easel_post->key, "error") !== false)))) {
                $result->success = true;
                $result->message = 'The image has been deleted';
                $this->session->set_flashdata('message', 'The image has been deleted');
            } else {
                $result->message = (!empty($easel_post->description)) ? $easel_post->description : 'There was an error deleting the image' ;
                $this->session->set_flashdata('message', $result->message);
            }
        } else {
            $result->message = 'Missing required data: Account ID or Image ID';
        }
        return $result;
    }

    //**========= END IMAGES =========**//



    //**========= START SUBTITLES =========**//

    /**
    *   Create a new Subtitle and link with media VoD
    */
    public function create_subtitle($account_id = false, $postdata = false)
    {
        $result = (object)[
            'data'      => false,
            'success'   => false,
            'message'   => ''
        ];

        if (!empty($account_id)) {
            $verified_url = false;
            $verified_url = does_url_exists($postdata['url']);

            if (!empty($verified_url)) {
                if (!empty($postdata['vodMediaId']) && !empty($postdata['language'])) {
                    $query_string = "?";
                    $query_string .= "id=" . (!empty($postdata['id']) ? $postdata['id'] : $this->ssid_common->create_guid());
                    $query_string .= "&vodMediaId=" . $postdata['vodMediaId'];
                    $query_string .= "&language=" . $postdata['language'];

                    $url_endpoint   = 'subtitle/binary' . $query_string;
                    $method_type    = 'POST';
                    $data           = [
                        'url'           => $verified_url,
                    ];

                    $easel_post = $this->easel_tv_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);

                    if (empty($easel_post->data->elements) && (!(isset($easel_post->key) && (strpos($easel_post->key, "error") !== false)))) {
                        $result->data       = $easel_post;
                        $result->success    = true;
                        $result->message    = 'Subtitle created successfully';
                        $this->session->set_flashdata('message', 'Subtitle created successfully');
                    } else {
                        $error_message      = !empty($easel_post->name) ? $easel_post->name : '';
                        $error_message      = !empty($easel_post->description) ? $error_message . ' | ' . $easel_post->description : '';
                        $error_message      = !empty($easel_post->key) ? $error_message . ' | ' . $easel_post->key : '';
                        $error_message      = !empty($error_message) ? $error_message : 'Subtitle creation to EASEL API failed!';
                        $result->message    = $error_message;
                        $result->success    = false;

                        $this->session->set_flashdata('message', $error_message);
                    }
                } else {
                    $result->message = 'Missing required data: VoD Media ID or Language';
                }
            } else {
                $result->message = 'Invalid URL';
            }
        } else {
            $result->message = 'Missing required data: Account ID';
        }
        return $result;
    }
    // $subtitle_debugging_data = [
        // "subtitle_id"    => "",
        // "content_id"     => "",
        // "string_name"    => "Easel CREATE SUBTITLE data - before the check",
        // "query_string"   => json_encode( $data ),
    // ];
    // $this->db->insert( "tmp_subtitle_debugging", $subtitle_debugging_data );


    /**
    *   Functionality to delete a Subtitle
    */
    public function delete_subtitle($account_id = false, $subtitle_id = false)
    {
        $result = (object)[
            'data'      => false,
            'success'   => false,
            'message'   => ''
        ];

        if (!empty($account_id) && !empty($subtitle_id)) {
            $url_endpoint   = 'subtitle/' . $subtitle_id;
            $method_type    = 'DELETE';
            $data           = [];

            $easel_post = $this->easel_tv_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);

            if ((!(isset($easel_post->key) && (strpos($easel_post->key, "error") !== false)))) {
                $result->success = true;
                $result->message = 'The subtitle has been deleted';
                $this->session->set_flashdata('message', 'The subtitle has been deleted');
            } else {
                $result->message = (!empty($easel_post->description)) ? $easel_post->description : 'There was an error deleting the subtitle' ;
                $this->session->set_flashdata('message', $result->message);
            }
        } else {
            $result->message = 'Missing required data: Account ID or Subtitle ID';
        }
        return $result;
    }

//**========= END SUBTITLES =========**//


//**========= START WEBHOOKS =========**//

    /**
    *   Webhook (Callback) from Easel
    */
    public function webhook($postdata = false)
    {
        $result = (object)[
            'data'      => false,
            'success'   => false,
            'message'   => ''
        ];

        if (EASEL_WEBHOOK_DEBUG) {
            $debug_data = [
                "request_type"  => "webhook - input",
                "request_data"  => json_encode($postdata),
            ];
            $this->db->insert("tmp_easel_webhook_debugging", $debug_data);
        }

        if (!empty($postdata)) {
            // $version         = ( !empty( $postdata['version'] ) ) ? $postdata['version'] : false ;
            $event_type     = (!empty($postdata['event'])) ? $postdata['event'] : false ;
            $data           = (!empty($postdata['data'])) ? $postdata['data'] : false ;

            if (!empty($event_type)) {
                ## assuming all event type will require the data element
                if (!empty($data)) {
                    $processed_data = (object)[ "data" => false, "message" => false ];
                    ## new event types: vod-media-encode-completed, vod-media-encode-cancelled, vod-media-encode-failed, vod-media-created (discovered by accident, not being informed by Easel)

                    switch (strtolower($event_type)) {
                        case "encode-completed":
                        case "vod-media-encode-completed":
                            $processed_data = $this->_encode_completed_webhook($data);
                            ## update product
                            break;

                        case "vod-media-encode-cancelled":
                        case "vod-media-encode-failed":
                        case "encode-cancelled":
                        case "encode-failed":
                            $processed_data = $this->_encode_cancelled_webhook($data);
                            break;

                        default:
                            $result->message = 'Event Type not recognised';
                    }

                    $result->success    = (!empty($processed_data->data)) ? true : false ;
                    $result->data       = (!empty($processed_data->data)) ? $processed_data->data : $result->data ;
                    $result->message    = (!empty($processed_data->message)) ? $processed_data->message : $result->message ;
                } else {
                    $result->message = 'Data not provided';
                }
            } else {
                $result->message = 'No Event Type provided';
            }
        } else {
            $result->message = 'No Data provided';
        }

        return $result;
    }



    private function _encode_completed_webhook($data = false)
    {
        $result = (object)["data" => false, "message" => ""];

        if (!empty($data)) {
            if (!empty($data['vodMedia'])) {
                $real_file_data = $file_details = $aws_bundle_id = $aws_file_id = $aws_file_name = $result_data = false;

                ## STEP 1: Recognize the file:

                ## VER A: if we do have the Easel ID of the VoD media - simplest and quickest
                if (!empty($data['vodMedia']['id'])) {
                    $this->db->select("content_decoded_file.*", false);
                    $this->db->select("content_decoded_file_type.type_group `file_type`", false);

                    $this->db->join("content_decoded_file_type", "content_decoded_file.decoded_file_type_id=content_decoded_file_type.type_id", "left");

                    $this->db->where("content_decoded_file.airtime_reference", $data['vodMedia']['id']);
                    $this->db->where("content_decoded_file.active", 1);
                    $where_arch = "( ( content_decoded_file.archived IS NULL ) OR ( content_decoded_file.archived != 1 ) )";
                    $this->db->where($where_arch);
                    $this->db->limit(1, 0);
                    ## this is to confirm the file exists to prevent an update on missing file
                    $real_file_data = $this->db->get("content_decoded_file")->row_array();
                }

                ## VER B: if we still have no data - by unpacking the file name
                if ((!$real_file_data) || empty($real_file_data)) {
                    if (!empty($data['vodMedia']['name'])) {
                        $file_details   = explode("-", $data['vodMedia']['name']);

                        $aws_bundle_id  = (!empty($file_details[0])) ? (int) $file_details[0] : false ;
                        $aws_file_id    = (!empty($file_details[1])) ? (int) $file_details[1] : false ;
                        $aws_file_name  = (!empty($file_details[2])) ? $file_details[2] : false ;

                        if (!empty($aws_bundle_id) && !empty($aws_file_id) && !empty($aws_file_name)) {
                            ## find the file
                            $where = [
                                "aws_bundle_id"     => $aws_bundle_id,
                                "file_cacti_id"     => $aws_file_id,
                            ];
                            $this->db->where($where);

                            $where_like = "file_name LIKE '%" . $aws_file_name . "%'";
                            $this->db->where($where_like);

                            ## this is to confirm the file exists to prevent an update on missing file
                            $real_file_data = $this->db->get("aws_bundle_content", 1, 0)->row_array();
                        }
                    }
                }

                if (!empty($real_file_data) && (!empty($real_file_data['file_id']) || !empty($real_file_data['file_cacti_id']))) { ## we do have details from version A or version B
                    ## possible airtime_encoded_status values = ["not-encoded", "encoding", "encoded", "encode-cancelled", "encode-failed", "unknown"];
                    $real_file_upd_data = [];
                    $real_file_upd_data = [
                        "is_airtime_encoded"            => (!empty($data['vodMedia']['encodingStatus']) && in_array(strtolower($data['vodMedia']['encodingStatus']), ["encoded"])) ? true : false,
                        "airtime_encoded_status"        => (!empty($data['vodMedia']['encodingStatus'])) ? $data['vodMedia']['encodingStatus'] : false,
                        "airtime_encoded_update_date"   => date('Y-m-d H:i:s')
                    ];

                    $real_file_upd_where = [];
                    $real_file_upd_where = [
                        "file_id"       => (!empty($real_file_data['file_id'])) ? $real_file_data['file_id'] : ((!empty($real_file_data['file_cacti_id'])) ? $real_file_data['file_cacti_id'] : ''),
                    ];

                    $this->db->update("content_decoded_file", $real_file_upd_data, $real_file_upd_where);
                    $last_query = $this->db->last_query();


                    if (EASEL_WEBHOOK_DEBUG) {
                        $debug_data = [
                            "request_type"  => "encode_completed - before the query",
                            "request_data"  => json_encode($real_file_data),
                        ];
                        $this->db->insert("tmp_easel_webhook_debugging", $debug_data);

                        $debug_data = [
                            "request_type"  => "encode_completed - query",
                            "request_data"  => json_encode($last_query),
                        ];
                        $this->db->insert("tmp_easel_webhook_debugging", $debug_data);
                    }


                    // if( $this->db->affected_rows() > 0 ){
                    if ($this->db->trans_status() !== false) {
                        // $this->db->select( "content_id, airtime_reference, is_airtime_encoded, airtime_encoded_status, airtime_encoded_update_date", false );
                        $result_data = $this->db->get_where("content_decoded_file", $real_file_upd_where, 1, 0)->row();

                        if (!empty($result_data)) {
                            if (!empty($result_data->content_id)) {
                                ## update Easel - product update

                                $this->db->where("content_film.content_id", $result_data->content_id);

                                ## this condition will give us only content which is settled in Easel
                                $this->db->where("content_film.external_content_ref IS NOT NULL");

                                $this->db->limit(1, 0);
                                $content_data = $this->db->get("content_film")->row();

                                if (!empty($content_data)) {
                                    ## just in case checking again
                                    if (!empty($content_data->external_content_ref)) {
                                        ## to pull the object from Easel

                                        $easel_product_data     = json_encode($this->fetch_product(1, $content_data->external_content_ref));
                                        $easel_product_data     = convert_to_array($easel_product_data);

                                        if (!empty($easel_product_data)) {
                                            $product_update_data = [
                                                'asset_code'            => (!empty($easel_product_data['reference'])) ? $easel_product_data['reference'] : '',
                                                'type'              => (!empty($easel_product_data['type']) && in_array($easel_product_data['type'], ['episode', 'channel', 'live-event'])) ? $postdata['type'] : 'generic',
                                                'name'              => (!empty($easel_product_data['name'])) ? $easel_product_data['name'] : ((!empty($easel_product_data['title'])) ? $easel_product_data['title'] : '') ,
                                                'state'             => (!empty($easel_product_data['state'])) ? strtolower($easel_product_data['state']) : 'offline',
                                                'shortDescription'  => (!empty($easel_product_data['shortDescription'])) ? $easel_product_data['shortDescription'] : '',
                                                'description'       => (!empty($easel_product_data['description'])) ? $easel_product_data['description'] : '',
                                                'duration'          => (!empty($easel_product_data['duration'])) ? $easel_product_data['duration'] : '',
                                                'country'           => (!empty($easel_product_data['country'])) ? $easel_product_data['country'] : 'GBR',
                                                'released'          => (!empty($easel_product_data['released'])) ? ($easel_product_data['released']) : '',
                                                'parentalAdvisory'  => (!empty($easel_product_data['parentalAdvisory'])) ? $easel_product_data['parentalAdvisory'] : '',
                                                'categories'        => (!empty($easel_product_data['categories'])) ? $easel_product_data['categories'] : [] ,
                                                'ageRatings'        => (!empty($easel_product_data['ageRatings'])) ? $easel_product_data['ageRatings'] : [] ,
                                                'indexable'         => (!empty($easel_product_data['indexable'])) ? $easel_product_data['indexable'] : false ,
                                                'episodeNumber'     => (!empty($easel_product_data['episodeNumber'])) ? $easel_product_data['episodeNumber'] : false ,
                                            ];

                                            if (isset($easel_product_data['published']) && !empty($easel_product_data['published'])) {
                                                $product_update_data['published'] = $easel_product_data['published'];
                                            }

                                            if ((isset($easel_product_data['image']['master']['imageId']) && !empty($easel_product_data['image']['master']['imageId']))) {
                                                $product_update_data['image']['master']['imageId'] = $easel_product_data['image']['master']['imageId'];
                                            }

                                            if ((isset($easel_product_data['image']['hero']['master']['imageId']) && !empty($easel_product_data['image']['hero']['master']['imageId']))) {
                                                $product_update_data['image']['hero']['master']['imageId'] = $easel_product_data['image']['hero']['master']['imageId'];
                                            }

                                            ## intentionally intended for the clarity of the code as a subtype of hore
                                            if ((isset($easel_product_data['image']['hero']['5:4']['imageId']) && !empty($easel_product_data['image']['hero']['5:4']['imageId']))) {
                                                $product_update_data['image']['hero']['5:4']['imageId'] = $easel_product_data['image']['hero']['5:4']['imageId'];
                                            }

                                            if ((isset($easel_product_data['image']['hero']['16:9']['imageId']) && !empty($easel_product_data['image']['hero']['16:9']['imageId']))) {
                                                $product_update_data['image']['hero']['16:9']['imageId'] = $easel_product_data['image']['hero']['16:9']['imageId'];
                                            }

                                            if ((isset($easel_product_data['image']['hero']['16:7']['imageId']) && !empty($easel_product_data['image']['hero']['16:7']['imageId']))) {
                                                $product_update_data['image']['hero']['16:7']['imageId'] = $easel_product_data['image']['hero']['16:7']['imageId'];
                                            }

                                            if ((isset($easel_product_data['image']['hero']['2:3']['imageId']) && !empty($easel_product_data['image']['hero']['2:3']['imageId']))) {
                                                $product_update_data['image']['hero']['2:3']['imageId'] = $easel_product_data['image']['hero']['2:3']['imageId'];
                                            }


                                            if ((isset($easel_product_data['image']['poster']['master']['imageId']) && !empty($easel_product_data['image']['poster']['master']['imageId']))) {
                                                $product_update_data['image']['poster']['master']['imageId'] = $easel_product_data['image']['poster']['master']['imageId'];
                                            }

                                            if ((isset($easel_product_data['image']['thumb']['master']['imageId']) && !empty($easel_product_data['image']['thumb']['master']['imageId']))) {
                                                $product_update_data['image']['thumb']['master']['imageId'] = $easel_product_data['image']['thumb']['master']['imageId'];
                                            }

                                            ## intentionally intended for the clarity of the code as a subtype of hore
                                            if ((isset($easel_product_data['image']['thumb']['5:4']['imageId']) && !empty($easel_product_data['image']['thumb']['5:4']['imageId']))) {
                                                $product_update_data['image']['thumb']['5:4']['imageId'] = $easel_product_data['image']['thumb']['5:4']['imageId'];
                                            }

                                            if ((isset($easel_product_data['image']['thumb']['16:9']['imageId']) && !empty($easel_product_data['image']['thumb']['16:9']['imageId']))) {
                                                $product_update_data['image']['thumb']['16:9']['imageId'] = $easel_product_data['image']['thumb']['16:9']['imageId'];
                                            }

                                            if ((isset($easel_product_data['image']['thumb']['16:7']['imageId']) && !empty($easel_product_data['image']['thumb']['16:7']['imageId']))) {
                                                $product_update_data['image']['thumb']['16:7']['imageId'] = $easel_product_data['image']['thumb']['16:7']['imageId'];
                                            }

                                            if ((isset($easel_product_data['image']['thumb']['2:3']['imageId']) && !empty($easel_product_data['image']['thumb']['2:3']['imageId']))) {
                                                $product_update_data['image']['thumb']['2:3']['imageId'] = $easel_product_data['image']['thumb']['2:3']['imageId'];
                                            }

                                            ## initially setting data to the default values if exists - if this movie had a feature before, give it back to it
                                            ## this way we preserve original values
                                            if ((isset($easel_product_data['feature'])) && (!empty($easel_product_data['feature']))) {
                                                $product_update_data['feature'] = $easel_product_data['feature'];
                                            }

                                            if ((isset($easel_product_data['trailer'])) && (!empty($easel_product_data['trailer']))) {
                                                $product_update_data['trailer'] = $easel_product_data['trailer'];
                                            }

                                            ## now override with the data from Easel webhook
                                            if (isset($data['vodMedia']['feature']) && ($data['vodMedia']['feature'] != false)) {
                                                ## override feature only
                                                $product_update_data['feature'] = $data['vodMedia']['id'];
                                            } else {
                                                ## override trailer only
                                                $product_update_data['trailer'] = $data['vodMedia']['id'] ;
                                            }

                                            if (EASEL_WEBHOOK_DEBUG) {
                                                $debug_data = [
                                                    // "content_id" => $content_data->content_id,
                                                    "request_type"  => "easel product update data set",
                                                    "request_data"  => json_encode($product_update_data),
                                                ];
                                                $this->db->insert("tmp_easel_webhook_debugging", $debug_data);
                                            }

                                            $upd_product_call = $this->update_product(1, $content_data->external_content_ref, $product_update_data);

                                            if (EASEL_WEBHOOK_DEBUG) {
                                                $debug_data = [
                                                    // "content_id" => $content_data->content_id,
                                                    "request_type"  => "easel response to product update",
                                                    "request_data"  => json_encode($upd_product_call),
                                                ];
                                                $this->db->insert("tmp_easel_webhook_debugging", $debug_data);
                                            }

                                            if (!empty($upd_product_call->success) && !empty($upd_product_call->data)) {
                                                ## easel update successful
                                                $data_upd = [];
                                                if (isset($data['vodMedia']['feature']) && ($data['vodMedia']['feature'] != false)) {
                                                    ## override feature only
                                                    $data_upd['airtime_feature_file_id'] = (!empty($real_file_data['file_id'])) ? $real_file_data['file_id'] : ((!empty($real_file_data['file_cacti_id'])) ? $real_file_data['file_cacti_id'] : '') ;
                                                } else {
                                                    ## override trailer only
                                                    $data_upd['airtime_trailer_file_id'] = (!empty($real_file_data['file_id'])) ? $real_file_data['file_id'] : ((!empty($real_file_data['file_cacti_id'])) ? $real_file_data['file_cacti_id'] : '') ;
                                                }

                                                $this->db->update("content_film", $data_upd, ['content_film.film_id' => $content_data->film_id]);

                                                if (EASEL_WEBHOOK_DEBUG) {
                                                    $last_call = $this->db->last_query();
                                                    $debug_data = [
                                                        "request_type"  => "the final update call on cacti",
                                                        "request_data"  => json_encode($last_call),
                                                    ];
                                                    $this->db->insert("tmp_easel_webhook_debugging", $debug_data);
                                                }

                                                if ($this->db->trans_status() != false) {
                                                    ## anything to add / do / return?
                                                } else {
                                                    ## CaCTi - Customer and Content Tracking interface update failed
                                                }

                                                ## updated file in the content_decoded_file table
                                                $upd_data = [];
                                                $upd_data = [
                                                    "airtime_product_reference"         => $content_data->external_content_ref,
                                                    "airtime_product_linking_status"    => "success",
                                                    "is_linked_with_airtime"            => 1,
                                                    "airtime_product_linking_date"      => date('Y-m-d H:i:s'),
                                                ];

                                                $upd_where = [];
                                                $upd_where = [
                                                    "file_id" => $real_file_upd_where['file_id']
                                                ];

                                                $this->db->update("content_decoded_file", $upd_data, $upd_where);
                                            } else {
                                                ## Easel product update failed
                                                $upd_data = [];
                                                $upd_data = [
                                                    "airtime_product_reference"             => null,
                                                    "airtime_product_linking_status"        => "error",
                                                    "airtime_product_linking_date"          => date('Y-m-d H:i:s'),
                                                ];

                                                $upd_where = [];
                                                $upd_where = [
                                                    "file_id" => $real_file_upd_where['file_id']
                                                ];

                                                $this->db->update("content_decoded_file", $upd_data, $upd_where);
                                            }
                                        } else {
                                            ## couldn't get Easel product data
                                        }
                                    } else {
                                        ## external_content_ref wasn't null but is missing/empty
                                    }
                                } else {
                                    ## No Easel settled (no Easel reference) content to be updated
                                }
                            } else {
                                ## product update cannot be done - no content ID
                                if (EASEL_WEBHOOK_DEBUG) {
                                    $debug_data = [
                                        "request_type"  => "product update problem - no content ID",
                                        "request_data"  => json_encode($result_data),
                                    ];
                                    $this->db->insert("tmp_easel_webhook_debugging", $debug_data);
                                }
                            }

                            ## we're deciding what exactly return to Easel. As a starter these four horses:
                            $result->data       = (object) [
                                "airtime_reference"             => (!empty($result_data->airtime_reference)) ? $result_data->airtime_reference : '' ,
                                "is_airtime_encoded"            => (!empty($result_data->is_airtime_encoded)) ? $result_data->is_airtime_encoded : '' ,
                                "airtime_encoded_status"        => (!empty($result_data->airtime_encoded_status)) ? $result_data->airtime_encoded_status : '' ,
                                "airtime_encoded_update_date"   => (!empty($result_data->airtime_encoded_update_date)) ? $result_data->airtime_encoded_update_date : ''
                            ];
                        } else {
                            // shouldn't happen. It means we failed to get the data from DB. Why? No one knows... ]
                        }
                        $result->message    = "The file has been updated";
                    } else {
                        $result->message    = "The file hasn't been updated";
                    }
                } else {
                    $result->message    = "Cannot determine the File for update";
                }
            } else {
                $result->message    = "The vodMedia section is required";
            }
        } else {
            $result->message    = "The Data section is required";
        }


        if (EASEL_WEBHOOK_DEBUG) {
            $debug_data = [
                "request_type"  => "encode_completed - output",
                "request_data"  => json_encode($result),
            ];
            $this->db->insert("tmp_easel_webhook_debugging", $debug_data);
        }

        return $result;
    }


    private function _encode_cancelled_webhook($data = false)
    {
        $result = (object)["data" => false, "message" => ""];

        if (!empty($data)) {
            if (!empty($data['vodMedia'])) {
                $real_file_data = $file_details = $aws_bundle_id = $aws_file_id = $aws_file_name = $result_data = false;

                ## STEP 1: Recognize the file:

                ## VER A: if we do have the Easel ID of the VoD media - simplest and quickest
                if (!empty($data['vodMedia']['id'])) {
                    $this->db->select("content_decoded_file.*", false);
                    $this->db->select("content_decoded_file_type.type_group `file_type`", false);

                    $this->db->join("content_decoded_file_type", "content_decoded_file.decoded_file_type_id=content_decoded_file_type.type_id", "left");

                    $this->db->where("content_decoded_file.airtime_reference", $data['vodMedia']['id']);
                    $this->db->where("content_decoded_file.active", 1);
                    $where_arch = "( ( content_decoded_file.archived IS NULL ) OR ( content_decoded_file.archived != 1 ) )";
                    $this->db->where($where_arch);
                    $this->db->limit(1, 0);
                    ## this is to confirm the file exists to prevent an update on missing file
                    $real_file_data = $this->db->get("content_decoded_file")->row_array();
                }

                ## VER B: if we still have no data - by unpacking the file name
                if ((!$real_file_data) || empty($real_file_data)) {
                    if (!empty($data['vodMedia']['name'])) {
                        $file_details   = explode("-", $data['vodMedia']['name']);

                        $aws_bundle_id  = (!empty($file_details[0])) ? (int) $file_details[0] : false ;
                        $aws_file_id    = (!empty($file_details[1])) ? (int) $file_details[1] : false ;
                        $aws_file_name  = (!empty($file_details[2])) ? $file_details[2] : false ;

                        if (!empty($aws_bundle_id) && !empty($aws_file_id) && !empty($aws_file_name)) {
                            ## find the file
                            $where = [
                                "aws_bundle_id"     => $aws_bundle_id,
                                "file_cacti_id"     => $aws_file_id,
                            ];
                            $this->db->where($where);

                            $where_like = "file_name LIKE '%" . $aws_file_name . "%'";
                            $this->db->where($where_like);

                            ## this is to confirm the file exists to prevent an update on missing file
                            $real_file_data = $this->db->get("aws_bundle_content", 1, 0)->row_array();
                        }
                    }
                }

                if (!empty($real_file_data) && (!empty($real_file_data['file_id']) || !empty($real_file_data['file_cacti_id']))) { ## we do have details from version A or version B
                    ## possible airtime_encoded_status values = ["not-encoded", "encoding", "encoded", "encode-cancelled", "encode-failed", "unknown"];
                    $real_file_upd_data = [];
                    $real_file_upd_data = [
                        "is_airtime_encoded"            => (!empty($data['vodMedia']['encodingStatus']) && in_array(strtolower($data['vodMedia']['encodingStatus']), ["encoded"])) ? true : false,
                        "airtime_encoded_status"        => (!empty($data['vodMedia']['encodingStatus'])) ? $data['vodMedia']['encodingStatus'] : false,
                        "airtime_encoded_update_date"   => date('Y-m-d H:i:s')
                    ];

                    $real_file_upd_where = [];
                    $real_file_upd_where = [
                        "file_id"       => (!empty($real_file_data['file_id'])) ? $real_file_data['file_id'] : ((!empty($real_file_data['file_cacti_id'])) ? $real_file_data['file_cacti_id'] : '')
                    ];

                    $this->db->update("content_decoded_file", $real_file_upd_data, $real_file_upd_where);

                    // if( $this->db->affected_rows() > 0 ){
                    if ($this->db->trans_status() !== false) {
                        if (EASEL_WEBHOOK_DEBUG) {
                            $debug_data = [
                                "request_type"  => "encode_cancelled - before the query",
                                "request_data"  => json_encode($real_file_data),
                            ];
                            $this->db->insert("tmp_easel_webhook_debugging", $debug_data);

                            $last_query = $this->db->last_query();
                            $debug_data = [
                                "request_type"  => "encode_cancelled - query",
                                "request_data"  => json_encode($last_query),
                            ];
                            $this->db->insert("tmp_easel_webhook_debugging", $debug_data);
                        }

                        // $this->db->select( "content_id, airtime_reference, is_airtime_encoded, airtime_encoded_status, airtime_encoded_update_date", false );
                        $result_data = $this->db->get_where("content_decoded_file", $real_file_upd_where)->result();

                        if (!empty($result_data)) {
                            ## we're deciding what exactly return to Easel. As a starter these four horses:
                            $result->data       = (object) [
                                "airtime_reference"             => (!empty($result_data->airtime_reference)) ? $result_data->airtime_reference : '' ,
                                "is_airtime_encoded"            => (!empty($result_data->is_airtime_encoded)) ? $result_data->is_airtime_encoded : '' ,
                                "airtime_encoded_status"        => (!empty($result_data->airtime_encoded_status)) ? $result_data->airtime_encoded_status : '' ,
                                "airtime_encoded_update_date"   => (!empty($result_data->airtime_encoded_update_date)) ? $result_data->airtime_encoded_update_date : ''
                            ];
                        } else {
                            // shouldn't happen. It means we failed to get the data from DB. Why? No one knows... ]
                        }
                        $result->message    = "The file has been updated";
                    } else {
                        $result->message    = "The file hasn't been updated";
                    }
                } else {
                    $result->message    = "Cannot determine the File for update";
                }
            } else {
                $result->message    = "The vodMedia section is required";
            }
        } else {
            $result->message    = "The Data section is required";
        }


        if (EASEL_WEBHOOK_DEBUG) {
            $debug_data = [
                "request_type"  => "encode_cancelled - output",
                "request_data"  => json_encode($result),
            ];
            $this->db->insert("tmp_easel_webhook_debugging", $debug_data);
        }

        return $result;
    }

//**========= END WEBHOOKS =========**//

    /**
    *   Submit a movie file (trailer) and start the encoding process
    */
    public function start_encoding($account_id = false, $postdata = false)
    {
        $result = (object)[
            'data'      => false,
            'success'   => false,
            'message'   => ''
        ];

        if (!empty($account_id)) {
            if (!empty($postdata)) {
                if (!empty($postdata['vod_media_id'])) {
                    if (EASEL_WEBHOOK_DEBUG) {
                        $debug_data = [
                            "request_type"  => "start_encoding - data before Easel",
                            "request_data"  => json_encode($postdata),
                        ];
                        $this->db->insert("tmp_easel_webhook_debugging", $debug_data);
                    }

                    $url_endpoint   = 'vod-media/' . $postdata['vod_media_id'] . '/start-encode';
                    $method_type    = 'POST';
                    $data           = [
                        'quality'       => (!empty($postdata['quality'])) ? strtolower($postdata['quality']) : 'hd'
                    ];

                    $easel_post = $this->easel_tv_common->api_dispatcher($url_endpoint, json_encode($data), ['method' => $method_type]);

                    log_message("error", json_encode(["easel post" => $easel_post]));

                    if (EASEL_WEBHOOK_DEBUG) {
                        $debug_data = [
                            "request_type"  => "start_encoding - data after Easel",
                            "request_data"  => (isset($easel_post) && !empty($easel_post)) ? $easel_post : true ,
                            "request_data"  => json_encode($easel_post),
                        ];
                        $this->db->insert("tmp_easel_webhook_debugging", $debug_data);
                    }

                    if ((!(isset($easel_post->name))) && (!(isset($easel_post->key) && (strpos($easel_post->key, "error") !== false)))) {
                        $result->data       = $easel_post;
                        $result->success    = true;
                        $result->message    = 'Encoding process has been started';
                        $this->session->set_flashdata('message', 'Encoding process has been started');
                    } else {
                        $error_message      = !empty($easel_post->name) ? $easel_post->name : '';
                        $error_message      = !empty($easel_post->description) ? $error_message . ' | ' . $easel_post->description : '';
                        $error_message      = !empty($easel_post->key) ? $error_message . ' | ' . $easel_post->key : '';
                        $error_message      = !empty($error_message) ? $error_message : 'Starting the encoding process failed!';
                        $result->message    = $error_message;
                        $result->success    = false;

                        $this->session->set_flashdata('message', $error_message);
                    }
                } else {
                    $result->message = 'Missing required data: VoD Media ID';
                }
            } else {
                $result->message = 'Data not provided';
            }
        } else {
            $result->message = 'Missing required data: Account ID';
        }

        return $result;
    }
}
