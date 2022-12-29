<?php

namespace Application\Modules\Service\Models;

class Address_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    private $unverified_addresses_search_fields	= [ 'addressline1', 'addressline2', 'street', 'posttown', 'county', 'postcode', 'postcode_nospaces' ];

    /** GET ADDRESSES BY SINGLE/MULTIPLE POSTCODES **/
    public function get_addresses($searchTerm, $limit=false)
    {
        $result = false;
        if (!empty($searchTerm)) {
            $searchTerm = urldecode($searchTerm);
            $searchTerm = str_replace('.', ',', $searchTerm); //Replace all periods (.) with commas

            if (strpos($searchTerm, ',') !== false) {
                $searchTerm = explode(',', $searchTerm);
            }


            if (!is_array($searchTerm)) {
                $localaddresses = $this->localPostcodeLookup($searchTerm, false, false, $limit);

                #return $localaddresses; //Un-comment to prevent making a chargeable API-call to postcode

                if (!empty($localaddresses)) {
                    $result = $localaddresses;
                } else {
                    ## Make API Call only if no records are found in the Local DB
                    $result = $this->apiPostcodeLookup($searchTerm);
                }
            } else {
                ## Multiple Postcodes Request
                $returnedaddresses = array();
                foreach ($searchTerm as $postcode) {
                    $localaddresses = $this->localPostcodeLookup($postcode, false, false, $limit);
                    if (!empty($localaddresses)) {
                        $returnedaddresses += $localaddresses;
                    } else {
                        ## Make API Call only if no records are found in the Local DB
                        //$returnedaddresses += $this->apiPostcodeLookup($postcode);
                        array_push($returnedaddresses, $this->apiPostcodeLookup($postcode));
                    }
                }
                $result = $returnedaddresses;
            }
        }

        if (!empty($result)) {
            $this->session->set_flashdata('message', 'Addresses found');
        } else {
            $this->session->set_flashdata('message', 'No Addresses found');
        }

        return $result;
    }

    /** LOCAL POSTCODE LOOKUP: Make a call to the Local Database to get any existing Address for the given postcode. **/
    public function localPostcodeLookup($searchTerm=false, $addresseslist=false, $where=false, $limit=false)
    {
        $result = false;

        if (!empty($searchTerm)) {
            $searchTermNoSpaces = preg_replace('/\s+/', '', $searchTerm);
            $this->db->where('postcode', $searchTerm);
            $this->db->or_where('postcode_nospaces', $searchTermNoSpaces);
        }

        if ($addresseslist) {
            $this->db->where_in('main_address_id', $addresseslist);
        }

        if ($where) {
            $this->db->where($where);
        }

        if ($limit) {
            $this->db->limit($limit);
        }

        $this->db->order_by('main_address_id', 'asc');
        $this->db->order_by('premise', 'asc');
        $query = $this->db->get('addresses');

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                if ($limit == 1) {
                    $result = (array)$row;
                } else {
                    $result[$row->main_address_id] = (array)$row;
                }
            }
        }

        return $result;
    }

    /** EXTERNAL API CALL: Make an Address Postcode Lookup API Call. Inputs a Postcode or part of an address **/
    public function apiPostcodeLookup($searchTerm)
    {
        $result = false;

        $searchKey = API_SEARCH_KEY;   // replace with your search key, stored as Config Contant

        ## we have a string to use for an address search
        $searchTerm     = rawurlencode($searchTerm);
        $identifier     = rawurlencode('v3 LDTV Dev');  // something to identify this lookup script in your PostCoder Web stats page (optional)
        $addresslines   = 3;                            // number of address lines required in your form / database
        //$exclude        = 'organisation';               // if organisation has it's own field in your form / database, exclude it from the address lines
        $include        = 'posttown,postcode';          // you could include posttown, county, postcode in the address lines if they don't have thier own fields.

        // build the URL, using the 'address' search method:
        $RestURL = 'http://ws.postcoder.com/pcw/' . $searchKey .'/address/UK/' . $searchTerm . '?identifier=' . $identifier . '&lines=' . $addresslines; ## Without Geo-coordinates
        //$RestURL = 'http://ws.postcoder.com/pcw/' . $searchKey .'/addressgeo/UK/' . $searchTerm . '?identifier=' . $identifier . '&lines=' . $addresslines; ## With Geo-coordinates. This Method Costs MORE CREDITS

        if (isset($include)) {
            $RestURL .= '&include=' . $include;
        }

        if (isset($exclude)) {
            $RestURL .= '&exclude=' . $exclude;
        }

        ## use cURL to send the request and get the response
        $session = curl_init($RestURL);
        ## Tell cURL to return the request data
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        ## Set the HTTP request headers, if we use application/json, then json will be returned from the PostCoder Web service, the default is XML.
        $headers = array(
            'Content-Type: application/json'
        );
        curl_setopt($session, CURLOPT_HTTPHEADER, $headers);
        ## Execute cURL on the session handle
        $response = curl_exec($session);
        // Close the cURL session
        curl_close($session);

        ########## DEBUG SECTION #############

        /*if($this->session->userdata('id') ==135){
            // get the header and body of the response
            $header_size = curl_getinfo($session, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $header_size);
            $body = substr($response, $header_size);

            // Close the cURL session
            curl_close($session);

            // send the response back to the client with the same header received from the service.
            $header_lines = explode("\n",$header);
            foreach($header_lines as $header_line){
                header($header_line);
            }
            debug($body);
        }*/

        ########## DEBUG SECTION #############

        $addresses = json_decode($response, true);

        ## SAVE FOR QUICKER SEARCH NEXT TIME ROUND
        if (!empty($addresses)) {
            $result = $this->save_addresses($addresses);
        }

        return $result;
    }

    /** Save All Addresses Received from Postcode Lookup **/
    public function save_addresses($addresses)
    {
        $result = false;
        if (!empty($addresses)) {
            $postCode 	 	 = $addresses[0]['postcode'];
            $splitPostCode 	 = explode(" ", $postCode);
            $firstPartNumber = filter_var($splitPostCode[0], FILTER_SANITIZE_NUMBER_INT); //E.g. CR0 = 0
            $secondPartNumber= filter_var($splitPostCode[1], FILTER_SANITIZE_NUMBER_INT); //E.g. 4GE = 4

            $postcode_sector  = $splitPostCode[0].' '.$secondPartNumber;					//E.g. CR0 4
            $postcode_district= $splitPostCode[0];											//E.g. CR0
            $postcode_area    = preg_replace('/[0-9]+/', '', $splitPostCode[0]);			//E.g. CR
            $postcode_qi      = $firstPartNumber;

            ## Do some pre-sets
            $addressesData = array();

            ## Add the pre-set data to each
            foreach ($addresses as $address) {
                $appendix = preg_replace('/\s+/', '', $address['addressline1']).'.'.preg_replace('/[^A-Za-z0-9\-]/', '', $address['addressline2']);
                $uniquereference = preg_replace('/\s+/', '', $address['postcode']).'.'.preg_replace('/[^A-Za-z0-9\-]/', '', $address['addressline1']).'.'.$appendix;

                $addressesData[] = array(
                    'addressline1'=>(isset($address['addressline1'])) ? $address['addressline1'] : '',
                    'addressline2'=>(isset($address['addressline2'])) ? $address['addressline2'] : '',
                    'addressline3'=>(isset($address['addressline3'])) ? $address['addressline3'] : '',
                    'summaryline'=>(isset($address['summaryline'])) ? $address['summaryline'] : '',
                    'number'=>(isset($address['number'])) ? $address['number'] : '',
                    'premise'=>(isset($address['premise'])) ? $address['premise'] : '',
                    'street'=>(isset($address['street'])) ? $address['street'] : '',
                    'posttown'=>(isset($address['posttown'])) ? $address['posttown'] : '',
                    'county'=>(isset($address['county'])) ? $address['county'] : '',
                    'postcode'=>(isset($address['postcode'])) ? trim($address['postcode']) : '',
                    'postcode_nospaces'=>(isset($address['postcode'])) ? (preg_replace('/\s+/', '', $address['postcode'])) : '',
                    'postcode_sector'=>$postcode_sector,
                    'postcode_district'=>$postcode_district,
                    'postcode_area'=>$postcode_area,
                    'postcode_qi'=>$postcode_qi,
                    'xcoords'=>(isset($address['xcoords'])) ? $address['xcoords'] : '',
                    'ycoords'=>(isset($address['ycoords'])) ? $address['ycoords'] : '',
                    'organisation'=>(isset($address['organisation'])) ? $address['organisation'] : '',
                    'buildingname'=>(isset($address['buildingname'])) ? $address['buildingname'] : '',
                    'subbuildingname'=>(isset($address['subbuildingname'])) ? $address['subbuildingname'] : '',
                    'dependentlocality'=>(isset($address['dependentlocality'])) ? $address['dependentlocality'] : '',
                    'uniquereference'=>$uniquereference
                );
            }

            ## Run DB Operations on the Data
            if (!empty($addressesData)) {
                $newaddresses     = array();
                $existingaddresses= array();
                $newrecords       = array();
                $updatedrecords   = array();
                foreach ($addressesData as $addressRecord) {
                    ## Check if record exists.
                    $this->db->where(array('postcode'=>$addressRecord['postcode'], 'uniquereference'=>$addressRecord['uniquereference']));
                    $query = $this->db->get("addresses");
                    if ($query->num_rows() > 0) {
                        $row = $query->result_array()[0];
                        $addressRecord['main_address_id']	  = (string) $row['main_address_id'];
                        $addressRecord['lastmodified']= date('Y-m-d H:i:s');
                        $existingaddresses[] = $addressRecord;
                    } else {
                        $addressRecord['datecreated']= date('Y-m-d H:i:s');
                        $this->db->insert('addresses', $addressRecord);

                        $addressRecord['main_address_id'] = (string) (($this->db->affected_rows() > 0) ? $this->db->insert_id() : '');
                        $newaddresses[] = $addressRecord;
                    }
                }

                ## Insert New records
                if (count($newaddresses) > 0) {
                    $newrecords = ($this->db->affected_rows() > 0) ? $newaddresses : false;
                }

                ## Batch Update Existing records
                if (count($existingaddresses) > 0) {
                    $this->db->update_batch("addresses", $existingaddresses, "main_address_id");
                    $updatedrecords = ($this->db->affected_rows() > 0) ? $existingaddresses : false;
                }
            }
            $result = array_merge($newrecords, $updatedrecords);
        }
        return $result;
    }

    /*
    * Get Addresses single records or multiple records
    */
    public function get_customer_address($address_id = false, $customer_id=false, $postcode=false, $archived=false, $offset=DEFAULT_OFFSET, $limit=DEFAULT_LIMIT)
    {
        $result = false;

        $this->db->select('customer_addresses.*,addrs.main_address_id,addrs.addressline1 `address_line_1`,addrs.addressline2 `address_line_2`,addrs.addressline3 `address_line_3`,addrs.posttown `address_city`,addrs.county `address_county`,addrs.postcode `address_postcode`,addrs.summaryline `address_summaryline`,addrs.organisation `address_business_name`', false)
            ->join('addresses addrs', 'addrs.main_address_id = customer_addresses.main_address_id', 'left')
            ->where('customer_addresses.archived !=', 1);
        if ($address_id) {
            $row = $this->db->get_where('customer_addresses', ['address_id'=>$address_id])->row();
            if (!empty($row)) {
                $this->session->set_flashdata('message', 'Address found');
                $result = $row;
            } else {
                $this->session->set_flashdata('message', 'Address not found');
            }
            return $result;
        }

        if (!$customer_id && !$postcode) {
            $this->session->set_flashdata('message', 'Address record(s) not found. Missing search creteria');
            return false;
        }

        if ($customer_id) {
            $this->db->where('customer_id', $customer_id);
        }

        if ($postcode) {
            $postcode_where = '( addrs.postcode = "'.$postcode.'" OR postcode_nospaces = "'.preg_replace('/\s+/', '', $postcode).'" )';
            $this->db->where($postcode_where);
        }

        if ($archived) {
            $this->db->where('archived', $archived);
        }

        $address = $this->db->order_by('address_id')
            ->limit($limit, $offset)
            ->get('customer_addresses');

        if ($address->num_rows() > 0) {
            $this->session->set_flashdata('message', 'Address records found');
            $result = $address->result();
        } else {
            $this->session->set_flashdata('message', 'Address record(s) not found');
        }
        return $result;
    }

    /*
    * Create new Address
    */
    public function create_address($address_data = false)
    {
        $result = false;
        if (!empty($address_data)) {
            $data = [];
            foreach ($address_data as $key=>$value) {
                if (in_array($key, format_name_columns())) {
                    $value = format_name($value);
                } else {
                    $value = trim($value);
                }
                $data[$key] = $value;
            }

            if (!empty($data)) {
                $data['unique_reference'] 			= $this->unique_address_reference($data);

                $address_exists = $this->db->get_where('customer_addresses', ['unique_reference'=>$data['unique_reference'],'archived'=>0])->row();
                if (!$address_exists) {
                    $this->db->insert('customer_addresses', $data);
                    if ($this->db->trans_status() !== false) {
                        $data['address_id'] = $this->db->insert_id();
                        $result = $this->get_address($data['address_id']);
                        $this->session->set_flashdata('message', 'Address record created successfully.');
                    }
                } else {
                    $data['last_modified'] = date('Y-m-d H:i:s');
                    $this->db->where('unique_reference', $data['unique_reference']);
                    $this->db->update('customer_addresses', $data);
                    if ($this->db->trans_status() !== false) {
                        $result = $this->get_address($address_exists->address_id);
                        $this->session->set_flashdata('message', 'Address record updated successfully.');
                    }
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Address data supplied.');
        }
        return $result;
    }

    /*
    * Update Address record
    */
    public function update_address($address_id = false, $address_data = false)
    {
        $result = false;
        if (!empty($address_id) && !empty($address_data)) {
            $data = [];
            foreach ($address_data as $key=>$value) {
                if (in_array($key, format_name_columns())) {
                    $value = format_name($value);
                } else {
                    $value = trim($value);
                }
                $data[$key] = $value;
            }

            if (!empty($data)) {
                $data['last_modified'] 	 			= date('Y-m-d H:i:s');
                $data['unique_reference'] 			= $this->unique_address_reference($data);

                #Check if other addresses exist with same Referrence
                $this->db->where('unique_reference', $data['unique_reference']);
                $this->db->where('address_id !=', $address_id);
                $query = $this->db->get('customer_addresses');
                if ($query->num_rows() > 0) {
                    $archive_data = [
                        'archived'		=> 1,
                        'last_modified'	=> date('Y-m-d H:i:s')
                    ];
                    $this->db->where('unique_reference', $data['unique_reference']);
                    $this->db->where('address_id !=', $address_id);
                    $this->db->update('customer_addresses', $archive_data);
                }

                $this->db->where('address_id', $address_id)->update('customer_addresses', $data);
                if ($this->db->trans_status() !== false) {
                    $result = $this->get_address($address_id);
                    $this->session->set_flashdata('message', 'Address record updated successfully.');
                } else {
                    $this->session->set_flashdata('message', 'There was an Error while trying to upate the Address record.');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Address data supplied.');
        }
        return $result;
    }

    /*
    * Delete Address record
    */
    public function delete_address($customer_id = false, $address_id = false)
    {
        $result = false;
        if (!empty($customer_id) && !empty($address_id)) {
            $address_exists = $this->db->get_where('customer_addresses', ['address_id'=>$address_id,'customer_id'=>$customer_id])->row();
            if (!empty($address_exists)) {
                $data = ['archived'=>1];
                $this->db->where(['address_id'=>$address_id,'customer_id'=>$customer_id])
                    ->update('customer_addresses', $data);
                if ($this->db->trans_status() !== false) {
                    $this->session->set_flashdata('message', 'Address record deleted successfully.');
                    $result = true;
                }
            } else {
                $this->session->set_flashdata('message', 'Address record not found. delete request failed');
            }
        } else {
            $this->session->set_flashdata('message', 'No Address ID supplied.');
        }
        return $result;
    }

    /*
    * Generate a unique address reference
    */
    public function unique_address_reference($address_data = false)
    {
        if (!empty($address_data)) {
            $address_appendix = preg_replace('/\s+/', '', $address_data['address_contact_first_name']).'.'.preg_replace('/\s+/', '', $address_data['address_contact_last_name']).'.'.preg_replace('/\s+/', '', $address_data['address_type']);
            $unique_reference = preg_replace('/\s+/', '', $address_data['main_address_id']).'.'.$address_appendix;
            return strtoupper($unique_reference);
        }
        return false;
    }

    /** Get Address types **/
    public function get_address_types($account_id = false, $address_type_id = false, $address_type_group = false, $grouped = false, $strict_mode = false)
    {
        $result = null;
        if ($account_id) {
            $this->db->where('address_types.account_id', $account_id);

        // if( $address_type_group ){
        // $this->db->where( 'address_types.address_type_group', $address_type_group );
        // }
        } else {
            $this->db->where('( address_types.account_id IS NULL OR address_types.account_id = "" )');
        }

        if ($address_type_group) {
            $this->db->where('address_types.address_type_group', $address_type_group);
        }

        if (!empty($address_type_id)) {
            $address_type_id = (!is_array($address_type_id)) ? json_decode($address_type_id) : $address_type_id;
            $address_type_id = (is_object($address_type_id)) ? object_to_array($address_type_id) : $address_type_id;
            if (is_array($address_type_id)) {
                $this->db->where_in('address_types.address_type_id', $address_type_id);
            } else {
                $this->db->where('address_types.address_type_id', $address_type_id);
            }
        }

        $query = $this->db->select('address_types.*', false)
            ->order_by('address_type')
            ->where('address_types.is_active', 1)
            ->get('address_types');

        if ($query->num_rows() > 0) {
            $result = $query->result();
        } elseif ($strict_mode) {
            $result = null;
        } else {
            $result = $this->get_address_types();
        }

        #Grouped result
        if (!empty($grouped)) {
            $data = [];
            foreach ($result as $k => $row) {
                $data[$row->address_type_group][] = $row;
            }
            $result = $data;
        }

        return $result;
    }


    /*
    *	Get list of Unverified Addresses and search through them
    */
    public function get_unverified_addresses($account_id = false, $main_address_id = false, $search_term = false, $where = false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select('addresses.*', false)
                ->where('addresses.verified !=', 1);

            $where = $raw_where = convert_to_array($where);

            if (!empty($main_address_id) || isset($where['main_address_id'])) {
                $main_address_id	= (!empty($main_address_id)) ? $main_address_id : $where['main_address_id'];
                if (!empty($main_address_id)) {
                    $row = $this->db->get_where('addresses', ['addresses.main_address_id'=>$main_address_id ])->row();

                    if (!empty($row)) {
                        $result  				= $row;
                        $this->session->set_flashdata('message', 'Unverified Addresses data found');
                        return $result;
                    } else {
                        $this->session->set_flashdata('message', 'Unverified Addresses data not found');
                        return false;
                    }
                }
                unset($where['main_address_id']);
            }

            if (!empty($search_term)) {
                //Check for spaces in the search term
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->unverified_addresses_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->unverified_addresses_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            if (!empty($order_by)) {
                $this->db->order_by($order_by);
            } else {
                $this->db->order_by('main_address_id DESC, addressline1');
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $query = $this->db->get('addresses');

            if ($query->num_rows() > 0) {
                $result_data = $query->result();

                $result 					= ( object )[ 'records' =>( object )[], 'counters'=>( object )[] ];
                $result->records 			= $result_data;
                $counters 					= $this->get_unverified_addresses_totals($account_id, $search_term, $raw_where);
                $result->counters->total 	= (!empty($counters->total)) ? $counters->total : null;
                $result->counters->pages 	= (!empty($counters->pages)) ? $counters->pages : null;
                $result->counters->limit  	= (!empty($apply_limit)) ? $limit : $result->counters->total;
                $result->counters->offset 	= $offset;

                $this->session->set_flashdata('message', 'Unverified Addresses data found');
            } else {
                $this->session->set_flashdata('message', 'There\'s currently no Unverified Addresses');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing required information');
        }

        return $result;
    }


    /** Get Category lookup counts **/
    public function get_unverified_addresses_totals($account_id = false, $search_term = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;
        if (!empty($account_id)) {
            $this->db->select('addresses.main_address_id', false)
                ->where('addresses.verified !=', 1);

            $where = $raw_where = convert_to_array($where);

            if (!empty($search_term)) {
                $search_term  = trim(urldecode($search_term));
                $search_where = [];
                if (strpos($search_term, ' ') !== false) {
                    $multiple_terms = explode(' ', $search_term);
                    foreach ($multiple_terms as $term) {
                        foreach ($this->unverified_addresses_search_fields as $k=>$field) {
                            $search_where[$field] = trim($term);
                        }

                        $where_combo = format_like_to_where($search_where);
                        $this->db->where($where_combo);
                    }
                } else {
                    foreach ($this->unverified_addresses_search_fields as $k=>$field) {
                        $search_where[$field] = $search_term;
                    }

                    $where_combo = format_like_to_where($search_where);
                    $this->db->where($where_combo);
                }
            }

            $query 			  = $this->db->from('addresses')->count_all_results();
            $results['total'] = !empty($query) ? $query : 0;
            $limit 				= ($limit > 0) ? $limit : $results['total'];
            $results['pages'] = !empty($query) ? ceil($query / $limit) : 0;
            return json_decode(json_encode($results));
        }
        return $result;
    }


    /** Add a NEW Unverified Address **/
    public function add_unverified_address($account_id = false, $unverified_address_data = false)
    {
        $result = null;

        if (!empty($account_id) && !empty($unverified_address_data)) {
            foreach ($unverified_address_data as $col => $value) {
                $data[$col] = trim($value);
            }

            $data['uniquereference'] 	= $this->_generate_address_unique_ref($account_id, $data);
            $data['summaryline'] 		= $this->_generate_summaryline($account_id, $data);
            $data['postcode'] 			= strtoupper($data['postcode']);
            $data['postcode_nospaces'] 	= strip_all_whitespace($data['postcode']);
            $data['verified'] 			= 0;

            $check_exists = $this->db->where('uniquereference', $data['uniquereference'])
                ->limit(1)
                ->get('addresses')
                ->row();

            $data = $this->ssid_common->_filter_data('addresses', $data);

            if (!empty($check_exists)) {
                /* 	$this->db->where( 'main_address_id', $check_exists->main_address_id )
                    ->update( 'addresses', $data );
                    $result = $check_exists; */

                $this->session->set_flashdata('message', 'This Address already exists (Address ID: '.$check_exists->main_address_id.'), request aborted!');
                $result = false;
            } else {
                $this->db->insert('addresses', $data);
                $this->session->set_flashdata('message', 'New Unverified Address added successfully.');
                $data['main_address_id'] = (string) $this->db->insert_id();
                $result = $data;
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }

        return $result;
    }


    /** Update Unverified Address **/
    public function update_unverified_address($account_id = false, $unverified_address_data = false)
    {
        $result = null;

        if (!empty($account_id) && !empty($unverified_address_data['main_address_id']) && !empty($unverified_address_data)) {
            foreach ($unverified_address_data as $col => $value) {
                $data[$col] = trim($value);
            }

            $data['uniquereference'] 	= $this->_generate_address_unique_ref($account_id, $data);
            $data['summaryline'] 		= $this->_generate_summaryline($account_id, $data);
            $data['postcode'] 			= strtoupper($data['postcode']);
            $data['postcode_nospaces'] 	= strip_all_whitespace($data['postcode']);
            $data['verified'] 			= 0;

            if (!empty($data['main_address_id'])) {
                $check_conflict = $this->db->select('main_address_id')
                    ->where('uniquereference', $data['uniquereference'])
                    ->where('addresses.main_address_id !=', $data['main_address_id'])
                    ->get('addresses')->row();

                $data = $this->ssid_common->_filter_data('addresses', $data);

                if (!$check_conflict) {
                    $data['lastmodifiedby'] = $this->ion_auth->_current_user->id;
                    $this->db->where([ 'main_address_id'=>$data['main_address_id'] ])
                        ->update('addresses', $data);
                    if ($this->db->trans_status() !== false) {
                        $result = $this->get_unverified_addresses($account_id, $data['main_address_id']);
                        $this->session->set_flashdata('message', 'Uverified Address updated successfully.');
                    }
                } else {
                    $this->session->set_flashdata('message', 'This Address does not exists or does not belong to you.');
                    $result = false;
                }
            } else {
                $this->session->set_flashdata('message', 'Error! Missing required information.');
            }
        } else {
            $this->session->set_flashdata('message', 'Error! Missing required information.');
        }

        return $result;
    }


    /*
    * Delete Unverified Address record
    */
    public function delete_unverified_address($account_id = false, $main_address_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($main_address_id)) {
            $conditions 		= ['verified'=>0,'main_address_id'=>$main_address_id];
            $category_item_exists 	= $this->db->get_where('addresses', $conditions)->row();
            if (!empty($category_item_exists)) {
                $this->db->where($conditions)
                    ->delete('addresses');

                if ($this->db->trans_status() !== false) {
                    $this->ssid_common->_reset_auto_increment('addresses', 'main_address_id');
                    $this->session->set_flashdata('message', 'Unverified Address deleted successfully.');
                    $result = true;
                }
            } else {
                $this->session->set_flashdata('message', 'Invalid Unverified Address ID');
            }
        } else {
            $this->session->set_flashdata('message', 'No Address record found.');
        }
        return $result;
    }


    /** Generate Address Unique Reference **/
    private function _generate_address_unique_ref($account_id = false, $address = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($address)) {
            $address_ref	= '';
            $address_ref 	.= (!empty($address['postcode'])) ? preg_replace('/\s+/', '', $address['postcode']) : '';
            $address_ref 	.= (!empty($address['addressline1'])) ? '.'.preg_replace('/\s+/', '', $address['addressline1']) : '';
            $address_ref 	.= (!empty($address['addressline2'])) ? preg_replace('/\s+/', '', $address['addressline2']) : '';
            $address_ref 	.= (!empty($address['addressline3'])) ? preg_replace('/\s+/', '', $address['addressline3']) : '';
            $address_ref 	.= (!empty($address['posttown'])) ? '.'.preg_replace('/\s+/', '', $address['posttown']) : '';
        } else {
            $address_ref = $this->ssid_common->generate_random_password();
        }
        return strtoupper($address_ref);
    }


        /** Generate Address Summary Line **/
    private function _generate_summaryline($account_id = false, $address = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($address)) {
            $address_ref	= '';
            $address_ref 	.= (!empty($address['addressline1'])) ? $address['addressline1'] : '';
            $address_ref 	.= (!empty($address['addressline2'])) ? ', '.$address['addressline2'] : '';
            $address_ref 	.= (!empty($address['addressline3'])) ? ', '.$address['addressline3'] : '';
            $address_ref 	.= (!empty($address['posttown'])) ? ', '.$address['posttown'] : '';
            $address_ref 	.= (!empty($address['county'])) ? ', '.$address['county'] : '';
            $address_ref 	.= (!empty($address['postcode'])) ? ', '.strtoupper($address['postcode']) : '';

            return $address_ref;
        } else {
            return false;
        }
    }
}
