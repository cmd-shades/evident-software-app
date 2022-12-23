<?php

defined('BASEPATH') or exit('No direct script access allowed');

    /*
    * Return the API Endpoint
    */
if (! function_exists('api_end_point')) {
    function api_end_point()
    {
        return base_url() . SERVICE_END_POINT;
    }
}

function format_name($var = false)
{
    if ($var) {
        $var = ucwords(strtolower(trim($var)));
    }
    return $var;
}

function format_email($var = false)
{
    if ($var) {
        $var = strtolower(trim(filter_var($var, FILTER_SANITIZE_EMAIL)));
    }
    return $var;
}

function format_number($var = false)
{
    if ($var) {
        $var = preg_replace('/\s+/', '', $var);
    }
    return $var;
}

function format_datetime_db($var = false)
{
    if ($var) {
        $var = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $var)));
    }
    return $var;
}

function format_datetime_client($var = false)
{
    if ($var) {
        $var = date('d/m/Y H:i:s', strtotime($var));
    }
    return $var;
}

function format_email_columns()
{
    return [
        'email',
        'client_email',
        'account_email',
    ];
}

function format_name_columns()
{
    return [
        'client_name',
        'first_name',
        'last_name',
        'contact_first_name',
        'contact_last_name',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'address_town',
        'address_county',
        'address_contact_first_name',
        'address_contact_last_name',
        'account_first_name',
        'account_last_name',
    ];
}

function format_number_columns()
{
    return [
        'mobile',
        'telephone',
        'contact_mobile',
        'contact_telephone',
        'account_mobile',
        'account_telephone',
        'start_period',
        'end_period',
    ];
}

function format_date_columns()
{
    return [
        'date_from',
        'date_to',
        'date_created',
        'date_actioned',
        'date_modified',
        'last_modified',
        'last_modified_date',
        'diary_date',
        'start_date',
        'end_date',
        'mot_expiry',
        'tax_expiry',
        'last_audit_date',
        'next_audit_date',
        'end_of_life_date',
        'date_of_birth',
        'leave_date',
        'service_due',
        'warranty_start_date',
        'warranty_end_date',
        'date_added_to_insurance',
        'first_registration_date',
        'valid_from',
        'expiry_date',
        'reminder_date',
        'fine_date',
        'tracker_install_date',
        'review_date',
        'medical_qnaire_date',
        'release_date',
        'order_date',
        'delivered_date',
        'last_ingestion_date',
        'approval_date',
        'license_start_date',
        'removal_date',
        'distribution_start_date',
        'distribution_end_date',
        'schedule_date_time',
    ];
}

function object_to_array($obj)
{
    $arr = json_decode(json_encode($obj), true);
    return $arr;
}

function array_to_object($arr)
{
    $obj = json_decode(json_encode($arr));
    return $obj;
}

function format_date_db($var = false)
{
    if ($var) {
        $var = date('Y-m-d', strtotime(str_replace('/', '-', $var)));
    }
    return $var;
}

    /**
    * Format date for use by clients
    */
function format_date_client($var = false)
{
    if ($var) {
        $var = date('d/m/Y', strtotime($var));
    }
    return $var;
}

    /**
    * Get a list of availalable address types
    */
function address_types()
{

    return [
        'Billing',
        'Business',
        'Delivery',
        'Invoice',
        'Residential',
        'Site'
    ];
}

    /** Format likes into a where condition **/
function format_like_to_where($where)
{
    $result = false;
    if ($where) {
        $sql = '( ';
        foreach ($where as $column => $value) {
            $sql .= $column . ' LIKE "%' . ( html_escape($value) ) . '%" OR ';
        }
        $sql .= ' ) ';
        if (strrpos($sql, 'OR') !== false) {
            $sql = substr_replace($sql, '', strrpos($sql, 'OR'), strlen('OR'));
        }
        $result = $sql;
    }
    return $result;
}

    /** Parse an Array to CSV string, ready to be written to file as a CSV **/
function array_to_csv($masterArray, $fileHeaders = false)
{
    $delimiter = ",";
    $newline = "\r\n";

    $result = "";
    if (!empty($masterArray)) {
        if ($fileHeaders) {
            ## Apply headers to the main array
            array_unshift($masterArray, $fileHeaders);
        }

        foreach ($masterArray as $fields) {
            if ($fields) {
                foreach ($fields as $field) {
                    $field = str_replace(',', '.', $field); //Replace all commas with  periods (.)
                    $field = str_replace(array("\n", "\r"), '', $field);
                    $result .= $field . $delimiter;
                }
            }
            $result .= $newline;
        }
    }
    return $result;
}

function site_ok_statuses()
{
    return [
        'OK',
        'No Fault'
    ];
}

function valid_date($date = false)
{

    if (!empty($date)) {
        $date = str_replace("/", "-", $date);
        $invalid_dates = [ '0000-00-00', '0000-00-00 00:00:00', '1970-01-01', '1970-01-01 00:00:00' ];
        $valid_date = date('Y-m-d H:i:s', strtotime($date));
        if (!in_array($valid_date, $invalid_dates)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function account_terms_and_conditions()
{
    return $terms = '<ul>
		   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>
		   <li>Aliquam tincidunt mauris eu risus.</li>
		   <li>Vestibulum auctor dapibus neque.</li>
		</ul><br/>
		<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>';
}

function format_boolean_columns()
{
    return [
        'is_active',
        'active',
        'is_supervisor',
        'is_insured',
        'has_road_assistance',
        'ordered',
        'medical_disability',
        'medical_conditions',
        'medical_hs_assessment_req',
        'status_change',
        'has_camera',
        'reemployment',
        'exit_interview',
        'is_review_required',
        'executive_acceptance_of_risk',
        'ftg',
        'is_airtime_ftg',
        'is_content_ftg',
        'organized',
        'is_content_active',
        'territory_clearance',
        'is_uip_nominated',
        'is_approved_by_provider',
        'is_local_server',
        'is_airtime_active',
        'is_hybrid',
        'add_packages',
        'is_package_active',
        'is_adult_active',
        'is_channel_ott',
        'is_provider_a_channel',
        'is_verified_for_airtime',
        'is_signed',
    ];
}

function format_boolean($var = false)
{
    if (( strtolower($var) == 'yes' ) || ( $var == 1 )) {
        $var = true;
    } else {
        $var = false;
    }
    return $var;
}


function format_long_date_columns()
{
    return [
        'wf_start_date',
        'wf_end_date',
        'reminder_date',
        'wf_date_created',
        'wf_date_modified',
        'return_date',
        'supply_date',
        'camera_install_date',
        'date_created',
        'event_date',
        'contract_start_date',
        'contract_end_date',
        'event_review_date',
        'sent_date',
    ];
}

function _pagination_config($base_url = false, $module_name = false, $method_name = false)
{

    if (!empty($base_url)) {
        $config['base_url']     = base_url() . $base_url;
    } else {
        $config['base_url']     = base_url() . 'webapp/user/users';
    }

    $config["uri_segment"]          = 3;
    $config['num_links']            = 10;
    $config['use_page_numbers']     = true;
    $config['reuse_query_string']   = true;

    $config['full_tag_open']        = '<ul class="pagination pull-right">';
    $config['full_tag_close']       = '</ul>';

    $config['first_link']           = '&laquo; First';
    $config['first_tag_open']       = '<li class="prev page">';
    $config['first_tag_close']      = '</li>';

    $config['last_link']            = 'Last &raquo;';
    $config['last_tag_open']        = '<li class="next page">';
    $config['last_tag_close']       = '</li>';

    $config['next_link']            = 'Next';
    $config['next_tag_open']        = '<li class="next page">';
    $config['next_tag_close']       = '</li>';

    /* $config['prev_link']             = '&larr; Previous'; */
    $config['prev_link']            = 'Previous';
    $config['prev_tag_open']        = '<li class="prev page">';
    $config['prev_tag_close']       = '</li>';

    $config['cur_tag_open']         = '<li><a class="pgn-link" href="">';
    $config['cur_tag_close']        = '</a></li>';

    $config['num_tag_open']         = '<li class="page">';
    $config['num_tag_close']        = '</li>';
    return $config;
}

    /**
    * Get a list of emergency contact relationsips list
    */
function contact_relationships()
{

    return [
        'Self'                      => 'Self',
        'Sibling'                   => 'Sibling',
        'Partner / Spouse'          => 'Partner / Spouse',
        'Parent'                    => 'Parent',
        'Grand Parent'              => 'Grand Parent',
        'Aunt / Uncle'              => 'Aunt / Uncle',
        'Nephew / Niece'            => 'Nephew / Niece',
        'Son / Daughter'            => 'Son / Daughter',
        'Son / Daughter (In-law)'   => 'Son / Daughter (In-law)',
        'Father / Mother (In-law)'  => 'Father / Mother (In-law)',
        'Other'                     => 'Other',
    ];
}

    /**
    * Convert an image into base64
    * @img_path has to be localised (./images/your-image.png ) and not remote (http://your-domain.com/images/your-image.png)
    */
function encode_img_base64($img_path = false, $img_type = 'png')
{
    if ($img_path) {
        //convert image into Binary data
        $img_data = fopen($img_path, 'rb');
        $img_size = filesize($img_path);
        $binary_image = fread($image_data, $img_size);
        fclose($img_data);

        //Build the src string to place inside your img tag
        $img_src = "data:image/" . $img_type . ";base64," . str_replace("\n", "", base64_encode($binary_image));
        return $img_src;
    }
    return false;
}

    /** Convert int to number in words **/
function number_to_words($number)
{

    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}

    /** Check if string is valid Json **/
function is_json($json_str = false)
{
    if (!empty($json_str)) {
        $is_json = json_decode($json_str);
        if (json_last_error() === JSON_ERROR_NONE) {
            return true;
        } else {
            return false;
        }
    }
    return false;
}

    /** array verify **/
function verify_array($data = false)
{
    if (!empty($data)) {
        $data   = ( !is_array($data) ) ? json_decode($data) : $data;
        $data   = ( is_object($data) ) ? object_to_array($data) : $data;
    }
    return $data;
}

    /**
    * Convert CSV File to Array
    */
function csv_file_to_array($uploadfile)
{

    $result = false;

    if (!empty($uploadfile)) {
        $rawcsv         = array();  //This is a temp array used for parsing.
        $parsedCsvArr   = array(); //This is what we send to update the DB with the input records

        //Process CSV file aka successful records
        $row = 1;
        if (( $handle = fopen($uploadfile, 'r') ) !== false) {
            while (( $data = fgetcsv($handle, 1000, ',') ) !== false) {
                $rawcsv[] = $data;
            }
            fclose($handle);
        }

        //Loop through the raw records and assign the fields to keys from the first records NOTE.
        foreach ($rawcsv as $k => $csvrows) {
            $rebuiltcsv = array();
            if ($k !== 0) {
                foreach ($csvrows as $r => $datarec) {
                    $key = trim(array_search($r, array_flip($rawcsv[0])));
                    $key = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $key); // to remove the BOM from Microsoft's files
                    $rebuiltcsv[$key] = $datarec;
                }
                $parsedCsvArr[] = $rebuiltcsv; //Load each rebuilt CSV record as a key=value pair
            }
        }

        if (!empty($parsedCsvArr)) {
            $result = $parsedCsvArr;
        }
    }

    return $result;
}

    /** Check if user is System admin or module admin or **/
function admin_check($system_admin = false, $module_admin = false, $module_tab_admin = false)
{
    $result = false;
    if (!empty($system_admin)) {
        return true;
    }

    if (!empty($module_admin) && !empty($module_admin)) {
        return true;
    }

    if (!empty($module_tab_admin)) {
        return true;
    }
    return false;
}

    /** Get time ago from timestamp **/
function timeago($time, $tense = 'ago')
{
    // declaring periods as static function var for future use
    static $periods = array('year', 'month', 'day', 'hour', 'minute', 'second');

    // checking time format
    if (!( strtotime($time) > 0 )) {
        return trigger_error("Wrong time format: '$time'", E_USER_ERROR);
    }

    // getting diff between now and time
    $now  = new DateTime('now');
    $time = new DateTime($time);
    $diff = $now->diff($time)->format('%y %m %d %h %i %s');
    // combining diff with periods
    $diff = explode(' ', $diff);
    $diff = array_combine($periods, $diff);
    // filtering zero periods from diff
    $diff = array_filter($diff);
    // getting first period and value
    $period = key($diff);
    $value  = current($diff);

    // if input time was equal now, value will be 0, so checking it
    if (!$value) {
        $period = 'seconds';
        $value  = 0;
    } else {
        // converting days to weeks
        if ($period == 'day' && $value >= 7) {
            $period = 'week';
            $value  = floor($value / 7);
        }
        // adding 's' to period for human readability
        if ($value > 1) {
            $period .= 's';
        }
    }

    // returning timeago
    return "$value $period $tense";
}


    /* Function to check if the date isn't empty */
function validate_date($date = false)
{
    if (isset($date) && !empty($date)) {
        $invalid_dates = [ '0000-00-00', '0000-00-00 00:00:00', '1970-01-01', '1970-01-01 00:00:00' ];
        if (!in_array($date, $invalid_dates)) {
            return true;
        }
    }
    return false;
}



    /** Strip white spaces from a string **/
function strip_all_whitespace($str = false)
{
    if (!empty($str)) {
        return preg_replace('/\s+/', '', trim(urldecode($str)));
    }
    return false;
}

    /** Re-order tabs for display in profile **/
function reorder_tabs($module_tabs = false)
{
    $data = false;
    if (!empty($module_tabs)) {
        $module_total   = ( is_object($module_tabs) ) ? count(object_to_array($module_tabs)) : count($module_tabs);
        $reordered_tabs = $more_list = [];
        if ($module_total > 6) { //6 is the max per layer in a 12-column grid system
            $counter = 1;
            foreach ($module_tabs as $k => $module) {
                if ($counter <= 5) {
                    $reordered_tabs[$counter] = $module;
                } else {
                    $reordered_tabs['more'][] = $module;
                    $more_list[] = $module->module_item_tab;
                }
                $counter++;
            }
            $data['module_tabs'] = $reordered_tabs;
            $data['more_list']   = $more_list;
        }
    }
    return $data;
}

function format_boolean_client($var = false)
{
    if (!empty($var) || $var == 0) {
        if ($var == true) {
            $var = 'Yes';
        } else {
            $var = 'No';
        }
    } else {
        $var = '';
    }
    return $var;
}

function convert_to_array($data = false)
{

    if (!empty($data)) {
        $data = ( !is_array($data) ) ? json_decode($data) : $data;
        $data = ( is_string($data) ) ? explode(",", $data) : $data;
        $data = ( is_object($data) ) ? object_to_array($data) : $data;
    }
    return $data;
}


function string_to_json_columns()
{
    return [
        'genre',
        'actors',
        'director',
        'writer',
    ];
}

function string_to_json($var)
{
    $array = [];

    if (is_string($var) && !empty($var)) {
        if (strpos($var, "|") > 0) {
            $array = array_map('trim', explode('|', $var));
        } elseif (strpos($var, ",") > 0) {
            $array = array_map('trim', explode(',', $var));
        } else {
            $array = [$var];
        }
    } elseif (is_array($var)) {
        $array = $var;
    }

    return json_encode($array);
}

function json_to_string($var)
{
    if (!empty(json_decode($var))) {
        return implode(",", ( json_decode($var) ));
    }
}


function prepare_poster_link($var, $res)
{
    if (!empty($var)) {
        $resolution = "SX" . $res . ".jpg";
        return str_replace("SX300.jpg", $resolution, $var);
    }
}


function time_after($date, $period)
{
    $resultDate = false;
    if (!empty($date) && !empty($period)) {
        $date = format_datetime_db($date);
        $resultDate = date('Y-m-d', strtotime($period, strtotime($date)));
    }
    return $resultDate;
}


function excerpt($string, $length)
{
    $str_len    = strlen($string);
    $string     = strip_tags($string);

    if ($str_len > $length) {
        $stringCut  = substr($string, 0, $length);
        $string     = $stringCut . '(...)';
        // $string  = $stringCut.'(...)'.substr($string, $str_len-10, $str_len-1);
    }
    return $string;
}


function strWordCut($string, $length, $end = ' (...)')
{
    $string = strip_tags($string);

    if (strlen($string) > $length) {
        $stringCut = substr($string, 0, $length);

        // make sure it ends in a word so assassinate doesn't become ass...
        $string = substr($stringCut, 0, strrpos($stringCut, ' ')) . $end;
    }
    return $string;
}


function formatBytes($bytes, $precision = 2)
{
    $units = array( 'B', 'KB', 'MB', 'GB', 'TB' );

    $bytes = max($bytes, 0);
    $pow = floor(( $bytes ? log($bytes) : 0 ) / log(1024));
    $pow = min($pow, count($units) - 1);

    // Uncomment one of the following alternatives
    $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow));

    return round($bytes, $precision) . ' ' . $units[$pow];
}


    /**
    *   Multi-array search
    *
    *   @param array $array
    *   @param array $search
    *   @return array
    */
function multi_array_search($array, $search)
{
    $result = array();

    foreach ($array as $key => $value) {
        // Iterate over each search condition
        foreach ($search as $k => $v) {
            // If the array element does not meet the search condition then continue to the next element
            if (!isset($value[$k]) || $value[$k] != $v) {
                continue 2;
            }
        }
        $result[] = $array[$key];
    }

    return $result;
}


    /**
    *   Create a reference string - only letters and digits
    *   @param string $string
    */
function create_reference_string($string)
{
    if (!empty($string)) {
        return strtolower(str_replace(" ", "_", preg_replace("/[^A-Za-z0-9\s]/", "", $string)));
    }
    return false;
}



    /**
    *   Show a list of files within the directory
    *   @param dir string
    */
function list_folder_files($dir = PREP_FOLDER)
{
    $files = @scandir($dir);

    if (!empty($files)) {
        unset($files[array_search('.', $files, true)]);
        unset($files[array_search('..', $files, true)]);

        // prevent empty ordered elements
        if (count($files) < 1) {
            return;
        }

        echo '<div class="col-md-12">';
        foreach ($files as $file) {
            echo '<div class="row">';
            echo '<a href="#" class="file-picker" data-file_name="' . $file . '">' . $file;
            if (is_dir($dir . '/' . $file)) {
                list_folder_files($dir . '/' . $file);
            }
            echo '</a>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<div class="row">';
        echo '<div class="col-md-12 red">Files not found in the Prep folder</div>';
        echo '</div>';
    }
}

    /** Get Number of Months between 2 dates **/
function _number_of_months($date1, $date2)
{
    $d1             = new DateTime($date2);
    $d2             = new DateTime($date1);
    $months         = $d2->diff($d1);
    $months_since   = ( ( $months->y ) * 12 ) + ( $months->m );
    return $months_since;
}

    /**
    * Convert Date to ISO8601 format
    */
function convert_date_to_iso8601($date_str = false)
{
    if (!empty($date_str)) {
        $format     = "Y-m-d\TH:i\Z";
        $date_str   = new DateTime($date_str);
        $date_str   = $date_str->format($format);
    }
    return $date_str;
}

    /**
    * Convert time from minutes to ISO8601 Duration
    */
function minutes_to_iso8601_duration($time_in_minutes = false)
{

    $str = false;

    if (!empty($time_in_minutes)) {
        $time_in_minutes = preg_replace("/[^0-9.]/", "", $time_in_minutes);
        $time = strtotime($time_in_minutes . " minutes", 0);

        $units = [
            "Y" => 365 * 24 * 3600,
            "D" =>     24 * 3600,
            "H" =>        3600,
            "M" =>          60,
            "S" =>           1,
        ];

        $str = "P";
        $istime = false;

        foreach ($units as $unitName => &$unit) {
            $quot  = intval($time / $unit);
            $time -= $quot * $unit;
            $unit  = $quot;
            if ($unit > 0) {
                if (!$istime && in_array($unitName, array( "H", "M", "S" ))) { // There may be a better way to do this
                    $str .= "T";
                    $istime = true;
                }
                $str .= strval($unit) . $unitName;
            }
        }
    }

    return $str;
}

    /**
    * Convert ISO 8601 Duration like P2DT15M33S
    * to a total value of seconds/minutes.
    *
    * @param string $iso8601_str
    */
function iso8601_duration_to_minutes($iso8601_str = false, $in_seconds = false)
{

    $time = false;

    if (!empty($iso8601_str)) {
        $interval = new \DateInterval($iso8601_str);

        $time_in_seconds = ( $interval->d * 24 * 60 * 60 ) +
            ( $interval->h * 60 * 60) +
            ( $interval->i * 60 ) +
            $interval->s;

        $time = ( !empty($in_seconds) ) ? $time_in_seconds : ( $time_in_seconds / 60 );
    }

    return $time;
}


    /*
    *   Translate special characters (i.e. spaces) in the url addresses to something safe
    */
function escapefile_url($url)
{
    $escaped_url = false;
    if (!empty($url)) {
        $parts              = parse_url($url);
        if (count($parts) > 1) {
            $path_parts     = array_map('rawurldecode', explode('/', $parts['path']));
            $escaped_url    = $parts['scheme'] . '://' . $parts['host'] . implode('/', array_map('rawurlencode', $path_parts));
        }
    }

    return $escaped_url;
}

    /*
    *   Translate ISO-8859-1 entities into Characters
    */
function entities_to_chars($string = false)
{
    $result = false;

    if (!empty($string)) {
        $searchVal = ["&Agrave;","&Aacute;","&Acirc;","&Atilde;","&Auml;","&Aring;","&AElig;","&Ccedil;","&Egrave;","&Eacute;","&Ecirc;","&Euml;","&Igrave;","&Iacute;","&Icirc;","&Iuml;","&ETH;","&Ntilde;","&Ograve;","&Oacute;","&Ocirc;","&Otilde;","&Ouml;","&Oslash;","&Ugrave;","&Uacute;","&Ucirc;","&Uuml;","&Yacute;","&THORN;","&szlig;","&agrave;","&aacute;","&acirc;","&atilde;","&auml;","&aring;","&aelig;","&ccedil;","&egrave;","&eacute;","&ecirc;","&euml;","&igrave;","&iacute;","&icirc;","&iuml;","&eth;","&ntilde;","&ograve;","&oacute;","&ocirc;","&otilde;","&ouml;","&oslash;","&ugrave;","&uacute;","&ucirc;","&uuml;","&yacute;","&thorn;","&yuml;"];

        $replaceVal = ["À","Á","Â","Ã","Ä","Å","Æ","Ç","È","É","Ê","Ë","Ì","Í","Î","Ï","Ð","Ñ","Ò","Ó","Ô","Õ","Ö","Ø","Ù","Ú","Û","Ü","Ý","Þ","ß","à","á","â","ã","ä","å","æ","ç","è","é","ê","ë","ì","í","î","ï","ð","ñ","ò","ó","ô","õ","ö","ø","ù","ú","û","ü","ý","þ","ÿ"];

        $result = str_replace($searchVal, $replaceVal, $string);
    }

    return $result;
}


    /*
    *   Returns true if the $key exists in the haystack and its value is $value.
    *   Otherwise, returns false.
    */
function key_value_pair_exists(array $haystack, $key, $value)
{
    return array_key_exists($key, $haystack) && $haystack[$key] == $value;
}


function decode_for_csv($string)
{
    if (!empty($string) && is_string($string)) {
        return html_entity_decode(( $string ), ENT_QUOTES, 'cp1252');
    } else {
        return false;
    }
}

function map_coggins_status($coggins_status = false)
{
    $result = false;
    if (!empty($coggins_status)) {
        switch (strtolower($coggins_status)) {
            case "queued":
                $result = "processing";
                break;

            case "error":
                $result = "error";
                break;

            case "prepared":
                $result = "scheduled";
                break;

            case "catalogued":
                $result = "scheduled";
                break;

            case "running":
                $result = "sending";
                break;

            case "deleted":
                $result = "cancelled";
                break;

            case "finished":
                $result = "sent";
                break;

            default:
                $result = "unknown";
        }
    }
    return $result;
}


    /*
    *   To check if image URL is valid and contains an Image
    *   @TODO: extended tests and sanitation
    */
function check_link_is_image($url)
{
    $result = false;

    if (!empty($url)) {
        $url = str_replace("\\", "", $url);
        $headers    = get_headers($url, 1);
        if (isset($headers['Content-Type'])) {
            $result     = ( strpos(strtolower($headers['Content-Type']), 'image/') !== false ) ? $url : false;
        }
    }
    return $result;
}


    /*
    *   To check if image URL is valid and contains a file
    */
function check_link_is_file($url)
{
    $result = false;

    if (!empty($url)) {
        $headers    = get_headers($url, 1);
        if (isset($headers[0])) {
            $result     = ( strpos(strtolower($headers[0]), '200 ok') !== false ) ? $url : false;
        }
    }

    return $result;
}

    /*
    *   An alternative version to check if image URL is valid and contains a file
    */
function does_url_exists($url)
{
    $result = false;
    if (!empty($url)) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result = ( $code == 200 ) ? $url : false;
        curl_close($ch);
    }
    return $result;
}

    ## fallback for versions prior to PHP 7.3.0.
if (!function_exists('array_key_first')) {
    function array_key_first(array $arr)
    {
        return array_keys($arr)[0];
    }
}


    ## To check the Coggins object if contains the required values
function check_coggins_postset($required_string = false, $coggins_webhook_object = false)
{
    $result = false;
    if (!empty((string) $required_string) && !empty($coggins_webhook_object)) {
        $decoded_object_keys = array_keys((array) $coggins_webhook_object);
        $result = ( in_array($required_string, $decoded_object_keys) ) ? true : false ;
    }

    return $result;
}

    ## For PHP 7.4+ there is a native is_countable function
if (!function_exists('is_countable')) {
    function is_countable($c)
    {
        return is_array($c) || $c instanceof Countable;
    }
}

    ## Get a simple array of values from the array of arrays using a column name as a parameter
function single_array_from_arrays($array_of_arrays = false, $column = false)
{
    if (!empty($array_of_arrays) && !empty($column)) {
        return array_map(function ($value) use ($column) {
            return $value[$column];
        }, $array_of_arrays);
    }
    return false;
}


    ## For the compatibility with PHP <= 7.3.0 :
if (!function_exists("array_key_last")) {
    function array_key_last($array)
    {
        if (!is_array($array) || empty($array)) {
            return null;
        }

        return array_keys($array)[count($array) - 1];
    }
}


    ## Validate array of integers
function validate_array_of_integers($array = false)
{
    $result = true;

    if (is_array($array)) {
        foreach ($array as $element) {
            if (!is_int($element)) {
                $result = false;
            }
        }
    } else {
        $result = false;
    }
    return $result;
}
