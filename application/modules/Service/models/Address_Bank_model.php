<?php

namespace Application\Modules\Service\Models;

class Address_Bank_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->dbutil();
        $this->load->helper('download');
        $this->load->helper('file');
    }

    private $dateformat = "d-m-Y";
    private $delimiter = ",";
    private $newline = "\r\n";

    /** GET ADDRESSES BY SINGLE/MULTIPLE POSTCODES **/
    public function get_addresses($search_term, $limit=false)
    {
        $result = false;
        if (!empty($search_term)) {
            $search_term = str_replace('.', ',', $search_term); //Replace all periods (.) with commas
            if (strpos($search_term, ',') !== false) {
                $search_term = explode(',', $search_term);
            }

            if (!is_array($search_term)) {
                $localaddresses = $this->local_postcode_lookup($search_term, false, false, $limit);
                if (!empty($localaddresses)) {
                    $result = $localaddresses;
                } else {
                    ## Make API Call only if no records are found in the Local DB
                    $result = $this->api_postcode_lookup($search_term);
                }
            } else {
                ## MUltiple Postcodes Request
                $returnedaddresses = array();
                foreach ($search_term as $postcode) {
                    $localaddresses = $this->local_postcode_lookup($postcode, false, false, $limit);
                    if (!empty($localaddresses)) {
                        $returnedaddresses += $localaddresses;
                    } else {
                        ## Make API Call only if no records are found in the Local DB
                        array_push($returnedaddresses, $this->api_postcode_lookup($postcode));
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
    public function local_postcode_lookup($search_term=false, $addresseslist=false, $where=false, $limit=false)
    {
        $result = false;
        if (!empty($search_term)) {
            $search_termNoSpaces = preg_replace('/\s+/', '', $search_term);
            $this->db->where('postcode', $search_term);
            $this->db->or_where('postcode_nospaces', $search_termNoSpaces);
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
            if ($limit == 1) {
                $result = $query->result_array()[0];
            } else {
                $result = $query->result_array();
            }
        }
        return $result;
    }

    /** EXTERNAL API CALL: Make an Address Postcode Lookup API Call. Inputs a Postcode or part of an address **/
    public function api_postcode_lookup($search_term)
    {
        $result = false;

        $api_search_key = API_SEARCH_KEY;   // replace with your search key, stored as Config Contant

        ## we have a string to use for an address search
        $search_term    = rawurlencode($search_term);
        $identifier     = rawurlencode('v3 LDTV Dev');  // something to identify this lookup script in your PostCoder Web stats page (optional)
        $addresslines   = 3;                            // number of address lines required in your form / database
        //$exclude      = 'organisation';               // if organisation has it's own field in your form / database, exclude it from the address lines
        $include        = 'posttown,postcode';          // you could include posttown, county, postcode in the address lines if they don't have thier own fields.

        // build the URL, using the 'address' search method:
        $RestURL = 'http://ws.postcoder.com/pcw/' . $api_search_key .'/address/UK/' . $search_term . '?identifier=' . $identifier . '&lines=' . $addresslines; ## Without Geo-coordinates
        //$RestURL = 'http://ws.postcoder.com/pcw/' . $api_search_key .'/addressgeo/UK/' . $search_term . '?identifier=' . $identifier . '&lines=' . $addresslines; ## With Geo-coordinates. This Method Costs MORE CREDITS

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

    /* Get address saved locally */
    public function get_local_addresses($main_address_id=false, $addresses_list=array(), $postcode=false)
    {
        $result = false;

        if ($main_address_id) {
            $this->db->where('main_address_id', $address_id);
        }

        if ($addresses_list) {
            $this->db->where_in('main_address_id', $addresses_list);
        }

        if ($postcode) {
            $this->db->where('main_address_id', $postcode);
        }

        $this->db->order_by('main_address_id', 'asc');
        $this->db->order_by('premise', 'asc');
        $query = $this->db->get('addresses');
        if ($query->num_rows() > 0) {
            if ($main_address_id) {
                $result = $query->result()[0];
            } else {
                $result = $query->result();
            }
        }
        return $result;
    }
}
