<?php

namespace App\Models\Service;

use App\Adapter\Model;

class FleetModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }


    /*
    *	Get Vehicle Profile record(s) data
    */
    public function get_vehicles($account_id = false, $vehicle_id = false, $vehicle_reg = false, $vehicle_barcode = false, $where = false, $limit = DEFAULT_LIMIT, $offset = 0)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select("v.*, concat(u.first_name,' ',u.last_name) as `driver_full_name`");
            $this->db->select("fvs.status_name");
            $this->db->select("fvsup.supplier_name");

            if (!empty($vehicle_id)) {
                $this->db->where("v.vehicle_id", $vehicle_id);
            }

            if (!empty($vehicle_reg)) {
                $this->db->where("v.vehicle_reg", $vehicle_reg);
            }

            if (!empty($vehicle_barcode)) {
                $this->db->where("v.vehicle_barcode", $vehicle_barcode);
            }

            if (!empty($where)) {
                if (is_object($where)) {
                    $where = get_object_vars($where);
                }
                $this->db->where($where);
            }

            $this->db->join("user u", "u.id = v.driver_id", "left");
            $this->db->join("fleet_vehicle_status fvs", "fvs.status_id = v.veh_status_id", "left");
            $this->db->join("fleet_vehicle_supplier fvsup", "fvsup.supplier_id = v.supplier_id", "left");

            $arch_where = "( v.archived != 1 or v.archived is NULL )";
            $this->db->where($arch_where);

            $this->db->where("v.account_id", $account_id);

            $query = $this->db->get("fleet_vehicle `v`", $limit, $offset);

            if (!empty($query->num_rows()) && ($query->num_rows() > 0)) {
                $result 	= $query->result();

                if (!empty($vehicle_id) || !empty($vehicle_reg)) {
                    foreach ($result as $key => $value) {
                        $driver_history = $this->get_fleet_driver_history_log($account_id, $vehicle_id, $vehicle_reg);
                        $result[$key]->driver_history = (!empty($driver_history)) ? $driver_history : null ;
                    }
                }

                $this->session->set_flashdata('message', 'Vehicle(s) data found.');
            } else {
                $this->session->set_flashdata('message', 'Vehicle(s) data not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account details provided.');
        }

        return $result;
    }


    /*
    *	Create a new Vehicle Profile
    */
    public function create_vehicle($account_id = false, $post_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($post_data)) {
            ## validate the postdata
            $data = [];
            foreach ($post_data as $key => $value) {
                if (in_array($key, format_name_columns())) {
                    $value = format_name($value);
                } elseif (in_array($key, format_email_columns())) {
                    $value = format_email($value);
                } elseif (in_array($key, format_number_columns())) {
                    $value = format_number($value);
                } elseif (in_array($key, format_boolean_columns())) {
                    $value = format_boolean($value);
                } elseif (in_array($key, format_date_columns())) {
                    $value = format_date_db($value);
                } elseif (in_array($key, format_long_date_columns())) {
                    $value = format_datetime_db($value);
                } else {
                    $value = trim($value);
                }
                $data[$key] = $value;
            }

            $data['account_id']		= $account_id;
            $data['created_date'] 	= date('Y-m-d H:i:s');
            $data['created_by'] 	= $this->ion_auth->_current_user()->id;

            $status_type 			= "unassigned";
            $unassigned_status_id 	= $this->get_status_by_type($account_id, $status_type);
            $data['veh_status_id']	= $unassigned_status_id;

            ## check conflicts
            $conflict = $this->db->get_where('fleet_vehicle', ['vehicle_reg' => $data['vehicle_reg'] ])->row();

            if (!$conflict) {
                $data = $this->ssid_common->_filter_data('fleet_vehicle', $data);
                $this->db->insert('fleet_vehicle', $data);

                if (($this->db->trans_status() !== false) && ($this->db->affected_rows() > 0)) {
                    $data['vehicle_id'] = $this->db->insert_id();
                    $this->session->set_flashdata('message', 'Vehicle has been created successfully.');
                    $result = $data;
                }
            } else {
                $this->session->set_flashdata('message', 'There is a Registration No conflict.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Vehicle or Account data supplied.');
        }

        return $result;
    }


    /*
    *	Delete Vehicle Profile
    */
    public function delete_vehicle($account_id = false, $vehicle_id = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($vehicle_id)) {
            $data = [ 'archived'=>1 ];
            $this->db->where('vehicle_id', $vehicle_id)
                    ->update('fleet_vehicle', $data);
            if (($this->db->trans_status() !== false) && ($this->db->affected_rows() > 0)) {
                $this->session->set_flashdata('message', 'Vehicle Profile deleted successfully.');
                $result = true;
            } else {
                $this->session->set_flashdata('message', 'No Vehicle has been deleted.');
                $result = false;
            }
        } else {
            $this->session->set_flashdata('message', 'No Vehicle ID supplied.');
            $result = true;
        }
        return $result;
    }


    /*
    *	Update Vehicle profile
    */
    public function update($account_id = false, $vehicle_id = false, $vehicle_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($vehicle_id) && !empty($vehicle_data)) {
            $data = [];


            ## this needs to trigger an unassign action
            if (isset($vehicle_data['driver_id']) && (empty($vehicle_data['driver_id']) || ($vehicle_data['driver_id'] == 0) || ($vehicle_data['driver_id'] == null))) {
                unset($vehicle_data['driver_id']);
            }

            foreach ($vehicle_data as $key => $value) {
                if (in_array($key, format_name_columns())) {
                    $value = format_name($value);
                } elseif (in_array($key, format_email_columns())) {
                    $value = format_email($value);
                } elseif (in_array($key, format_number_columns())) {
                    $value = format_number($value);
                } elseif (in_array($key, format_boolean_columns())) {
                    $value = format_boolean($value);
                } elseif (in_array($key, format_date_columns())) {
                    $value = format_date_db($value);
                } elseif (in_array($key, format_long_date_columns())) {
                    $value = format_datetime_db($value);
                } else {
                    $value = trim($value);
                }
                $data[$key] = $value;
            }

            $data['last_modified'] 		= date('Y-m-d H:i:s');
            $data['last_modified_by'] 	= $this->ion_auth->_current_user()->id;

            if (!empty($data)) {
                $conflict = false;
                $data =  $this->ssid_common->_filter_data('fleet_vehicle', $data);
                $restricted_columns = ['created_by', 'created_date', 'archived'];
                foreach ($data as $key => $value) {
                    if (in_array($key, $restricted_columns)) {
                        unset($data[$key]);
                    }
                }

                $conflict = $this->db->get_where("fleet_vehicle", array( "vehicle_id !=" => $vehicle_id, "vehicle_reg" => $data['vehicle_reg'] ))->row();

                if (!$conflict) {
                    $this->db->where('vehicle_id', $vehicle_id)->update('fleet_vehicle', $data);
                    if (($this->db->trans_status() !== false) && ($this->db->affected_rows() > 0)) {
                        $result = $this->get_vehicles($account_id, $vehicle_id);
                        $this->session->set_flashdata('message', 'Vehicle Profile updated successfully.');
                    } else {
                        $this->session->set_flashdata('message', 'The Vehicle profile hasn\'t been changed.');
                    }
                } else {
                    $this->session->set_flashdata('message', 'There is a Registration No conflict.');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Account ID, no Vehicle Id or no new data supplied.');
        }
        return $result;
    }


    /*
    *	Function to assign the driver to the vehicle and create an entry log
    *	pd_end_date 	-> previous_driver_end_date
    * 	nd_start_date 	-> new_driver_start_date
    */
    public function assign_driver_to_vehicle($account_id = false, $vehicle_id = false, $driver_id = false, $note = false, $pd_end_date = false, $nd_start_date = false, $audit_id = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($vehicle_id) && !empty($driver_id)) {
            $vehicle = false ;
            $vehicle = $this->db->get_where("fleet_vehicle", array( "vehicle_id" => $vehicle_id  ))->row();
            if ($vehicle) {
                ## checking if the car has driver already assign and the current driver is not the same as incoming
                if (!empty($vehicle->driver_id) && ($vehicle->driver_id == $driver_id)) {
                    ## do nothing
                    $this->session->set_flashdata('message', 'This driver is currently assigned to this vehicle.');
                } else {
                    if (!empty($vehicle->driver_id)) {
                        ## remove current driver & create a log
                        $this->remove_driver_from_vehicle($account_id, $vehicle_id, $vehicle->driver_id, $note, $pd_end_date);
                    }

                    $status_type = "assigned";
                    $assigned_status_id = $this->get_status_by_type($account_id, $status_type);

                    $update_data = [
                        "driver_id" 		=> $driver_id,
                        "driver_start_date" => (!empty($nd_start_date)) ? format_datetime_db($nd_start_date) : date('Y-m-d H:i:s'),
                        "last_modified" 	=> date('Y-m-d H:i:s'),
                        "last_modified_by" 	=> $this->ion_auth->_current_user()->id,
                        "veh_status_id" 	=> $assigned_status_id,
                    ];

                    $where = [
                        "account_id" 	=> $account_id,
                        "vehicle_id" 	=> $vehicle_id,
                    ];

                    $this->db->update("fleet_vehicle", $update_data, $where);

                    if ($this->db->affected_rows() > 0) {
                        ## create a driver history log
                        $logdata = [
                            "start_date" 		=> $update_data['driver_start_date'],
                            "audit_id" 			=> (!empty($audit_id)) ? (int) $audit_id : null,
                            "end_date"			=> null,
                            "dc_action_date"	=> date('y-m-d H:i:s'),
                            "note"				=> (!empty($note)) ? trim($note) : null,
                        ];
                        $log = $this->add_fleet_driver_history_log($account_id, $vehicle_id, $driver_id, $vehicle->vehicle_reg, $action = "Assign Driver", $logdata);

                        ## create vehicle history log
                        $vehicle_history_log_data = [
                            "log_type"			=> "driver",
                            "entry_id"			=> $this->db->insert_id(),
                            "vehicle_id"		=> $vehicle_id,
                            "vehicle_reg"		=> $vehicle->vehicle_reg,
                            "action"			=> "assign vehicle driver",
                            "note"				=> (!empty($logdata['note'])) ? trim($logdata['note']) : null,
                            "current_values" 	=> serialize(array( "start_date" => $logdata['start_date'], "driver_id" => $driver_id, "note" => trim($logdata['note']) )),
                        ];
                        $vehicle_history_log = $this->create_vehicle_change_log($account_id, $vehicle_history_log_data);

                        $this->session->set_flashdata('message', 'Driver successfully assigned to the vehicle.');

                        $result = $this->db->get_where('fleet_vehicle', ['vehicle_id' => $vehicle->vehicle_id ])->row();
                    }
                }
            } else {
                $this->session->set_flashdata('message', 'There is No Vehicle with this ID.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Valid Data provided.');
        }

        return $result;
    }



    /*
    *	Function to remove the driver from the vehicle. Will also create a log entry.
    */
    public function remove_driver_from_vehicle($account_id = false, $vehicle_id = false, $driver_id = false, $note = false, $end_date = false, $audit_id = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($vehicle_id) && !empty($driver_id)) {
            $vehicle = false ;
            $vehicle = $this->db->get_where("fleet_vehicle", ["vehicle_id" => $vehicle_id, "driver_id" => $driver_id ])->row();

            if ($vehicle) {
                $status_type = "unassigned";
                $unassigned_status_id = $this->get_status_by_type($account_id, $status_type);

                $update_data = [
                    "veh_status_id" 	=> (!empty($unassigned_status_id)) ? $unassigned_status_id : 3,
                    "driver_id" 		=> null,
                    "driver_start_date"	=> null,
                    "last_modified" 	=> date('Y-m-d H:i:s'),
                    "last_modified_by" 	=> $this->ion_auth->_current_user()->id,
                ];

                $query = $this->db->update("fleet_vehicle", $update_data, ["vehicle_id" => $vehicle->vehicle_id]);

                if ($this->db->affected_rows() > 0) {
                    $result = $this->db->get_where('fleet_vehicle', ['vehicle_id' => $vehicle->vehicle_id ])->row();

                    $logdata = [
                        "start_date" 		=> $vehicle->driver_start_date,
                        "end_date"			=> (!empty($end_date)) ? format_datetime_db($end_date) : date('Y-m-d H:i:s'),
                        "dc_action_date"	=> date('y-m-d H:i:s'),
                        "audit_id" 			=> (!empty($audit_id)) ? (int) $audit_id : null,
                        "note"				=> (!empty($note)) ? trim($note) : null,
                    ];

                    $log = $this->add_fleet_driver_history_log($account_id, $vehicle_id, $driver_id, $vehicle->vehicle_reg, $action = "Unassign Driver", $logdata);

                    ## create vehicle history log
                    $vehicle_history_log_data = [
                        "log_type"			=> "driver",
                        "entry_id"			=> $this->db->insert_id(),
                        "vehicle_id"		=> $vehicle_id,
                        "vehicle_reg"		=> $vehicle->vehicle_reg,
                        "action"			=> "remove vehicle driver",
                        "previous_values" 	=> json_encode(array( "driver_id" => $vehicle->driver_id )),
                        "current_values" 	=> json_encode(array( "end_date" => $logdata['end_date'], "note" => trim($logdata['note']) )),
                        "note"				=> (!empty($logdata['note'])) ? trim($logdata['note']) : null,
                    ];

                    $vehicle_history_log = $this->create_vehicle_change_log($account_id, $vehicle_history_log_data);

                    $this->session->set_flashdata('message', 'The Driver has been Unassigned successfully.');
                } else {
                    $this->session->set_flashdata('message', 'No Vehicle data changed.');
                }
            } else {
                $this->session->set_flashdata('message', 'There is No Vehicle with this Driver assigned.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Valid Data provided.');
        }

        return $result;
    }



    /*
    * 	Search Vehicle by: vehicle_reg, vehicle_make, vehicle_model, year, driver name
    */
    public function vehicle_lookup($account_id = false, $search_term = false, $vehicle_statuses, $where =false, $order_by = false, $limit = DEFAULT_LIMIT, $offset = DEFAULT_OFFSET)
    {
        $result = false;

        ##SELECT vehicle_id, vehicle_reg as veh_reg FROM `fleet_vehicle` where REPLACE( vehicle_reg, " ", "" ) = 'DH01LUD'

        if (!empty($account_id)) {
            $this->db->select("fv.*", false);
            $this->db->select("CONCAT( u.first_name, ' ', u.last_name ) `driver_full_name`", false);
            $this->db->select("CONCAT( u1.first_name, ' ', u1.last_name ) `created_by_full_name`", false);
            $this->db->select("CONCAT( u2.first_name, ' ', u2.last_name ) `last_modified_by_full_name`", false);
            $this->db->select("fvsup.supplier_name, fvsup.supplier_details, fvsup.supplier_details, fvsup.supplier_mobile, fvsup.supplier_telephone", false);
            $this->db->select("fvstat.status_name, fvstat.status_description", false);

            $arch_where = "( fv.archived != 1 or fv.archived is NULL )";
            $this->db->where($arch_where);

            $this->db->where('fv.account_id', $account_id);

            $this->db->join("user u", "u.id = fv.driver_id", "left");
            $this->db->join("user u1", "u1.id = fv.created_by", "left");
            $this->db->join("user u2", "u2.id = fv.last_modified_by", "left");
            $this->db->join("fleet_vehicle_status fvstat", "fvstat.status_id = fv.veh_status_id", "left");
            $this->db->join("fleet_vehicle_supplier fvsup", "fvsup.supplier_id = fv.supplier_id", "left");

            if (!empty($search_term)) {
                $where = "( (";
                $search_fields['fv.vehicle_barcode'] 	= $search_term;
                $search_fields['fv.vehicle_make'] 		= $search_term;
                $search_fields['fv.vehicle_model']		= $search_term;
                $search_fields['fv.year']				= $search_term;
                $where .= format_like_to_where($search_fields);
                $where .= ") OR (";
                $where .= 'REPLACE( fv.vehicle_reg, " ", "" ) LIKE "%'.str_replace(' ', '', $search_term).'%" ';
                $where .= ') OR ( CONCAT( u.first_name, u.last_name ) LIKE "%'.str_replace(' ', '', $search_term).'%" ) )';
                $this->db->where($where);
            }

            if ($vehicle_statuses) {
                $vehicle_statuses = (!is_array($vehicle_statuses)) ? json_decode($vehicle_statuses) : $vehicle_statuses;
                $this->db->where_in('fv.veh_status_id', $vehicle_statuses);
            }

            $this->db->limit($limit, $offset);

            $query = $this->db->get('fleet_vehicle fv');

            if ($query->num_rows() > 0) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'Vehicle(s) data found.');
            } else {
                $this->session->set_flashdata('message', 'No records found matching your criteria.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account or Search Term provided.');
        }
        return $result;
    }

    /*
    * 	Search Vehicle by: vehicle_reg, make, model, year, driver name
    */
    public function get_total_vehicles($account_id = false, $search_term = false, $vehicle_statuses = false, $where = false, $limit = DEFAULT_LIMIT)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select("fv.vehicle_id", false);
            $this->db->select("CONCAT( u.first_name, ' ', u.last_name ) `driver_full_name`", false);
            $this->db->select("CONCAT( u1.first_name, ' ', u1.last_name ) `created_by_full_name`", false);
            $this->db->select("CONCAT( u2.first_name, ' ', u2.last_name ) `last_modified_by_full_name`", false);
            $this->db->select("fvsup.supplier_name, fvsup.supplier_details, fvsup.supplier_details, fvsup.supplier_mobile, fvsup.supplier_telephone", false);
            $this->db->select("fvstat.status_name, fvstat.status_description", false);

            $arch_where = "( fv.archived != 1 or fv.archived is NULL )";
            $this->db->where($arch_where);

            $this->db->where('fv.account_id', $account_id);

            $this->db->join("user u", "u.id = fv.driver_id", "left");
            $this->db->join("user u1", "u1.id = fv.created_by", "left");
            $this->db->join("user u2", "u2.id = fv.last_modified_by", "left");
            $this->db->join("fleet_vehicle_status fvstat", "fvstat.status_id = fv.veh_status_id", "left");
            $this->db->join("fleet_vehicle_supplier fvsup", "fvsup.supplier_id = fv.supplier_id", "left");

            if (!empty($search_term)) {
                $where = "(";
                $search_fields['fv.vehicle_barcode'] 	= $search_term;
                $search_fields['fv.vehicle_make'] 		= $search_term;
                $search_fields['fv.vehicle_model']		= $search_term;
                $search_fields['fv.year']				= $search_term;
                $search_fields['u.first_name']			= $search_term;
                $search_fields['u.last_name']			= $search_term;
                $where .= format_like_to_where($search_fields);
                $where .= ") OR (";
                $where .= 'REPLACE( vehicle_reg, " ", "" ) = "'.str_replace(' ', '', $search_term).'" )';

                $this->db->where($where);
            }
        } else {
            $this->session->set_flashdata('message', 'No Account or Search Term provided.');
        }

        $query = $this->db->from('fleet_vehicle fv')->count_all_results();
        $results['total'] = !empty($query) ? $query : 0;
        $results['pages'] = !empty($query) ? ceil($query / DEFAULT_LIMIT) : 0;
        return json_decode(json_encode($results));
    }


    /*
    *	Function to create an entry log in vehicle driver history
    * 	possible action = ['driver_assign', 'driver_remove']
    */
    public function add_fleet_driver_history_log($account_id = false, $vehicle_id = false, $driver_id = false, $vehicle_reg = false, $action = false, $logdata = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($vehicle_id) && !empty($driver_id) && !empty($action)) {
            $log_data = false;
            $log_data = [
                "account_id" 		=> $account_id,
                "driver_id" 		=> $driver_id,
                "vehicle_id" 		=> $vehicle_id,
                "vehicle_reg" 		=> $vehicle_reg,
                "start_date" 		=> (!empty($logdata['start_date'])) ? format_datetime_db($logdata['start_date']) : null ,
                "end_date" 			=> (!empty($logdata['end_date'])) ? format_datetime_db($logdata['end_date']) : null ,
                "action"			=> $action,
                "dc_action_date"	=> (!empty($logdata['dc_action_date'])) ? $logdata['dc_action_date'] : null ,
                "audit_id"			=> (!empty($logdata['audit_id'])) ? $logdata['audit_id'] : null ,
                "note"				=> (!empty($logdata['note'])) ? $logdata['note'] : null ,
                "created_by"		=> $this->ion_auth->_current_user()->id,
            ];

            $log_data = $this->ssid_common->_filter_data('fleet_driver_change_log', $log_data);
            $this->db->insert("fleet_driver_change_log", $log_data);

            if ($this->db->affected_rows() > 0) {
                if (empty($this->session->flashdata('message'))) {
                    $this->session->set_flashdata('message', 'The Log data added successfully.');
                }
                $result = true;
            }
        } else {
            $this->session->set_flashdata('message', 'No Valid Data provided for an entry log.');
        }

        return $result;
    }


    /*
    *	Function to pull driver history on the vehicle
    */
    public function get_fleet_driver_history_log($account_id = false, $vehicle_id = false, $vehicle_reg = false, $limit = DEFAULT_LIMIT, $offset = 0)
    {
        $result = false;

        if (!empty($account_id) && (!empty($vehicle_id) || !empty($vehicle_reg))) {
            $this->db->select('fdcl.*, CONCAT(drivers.first_name," ",drivers.last_name) `driver_full_name`,  CONCAT(usr.first_name," ",usr.last_name) `logged_by`', false)
                ->join('user drivers', 'drivers.id = fdcl.driver_id', 'left')
                ->join('user usr', 'usr.id = fdcl.created_by', 'left');

            if (!empty($vehicle_id)) {
                $this->db->where("vehicle_id", $vehicle_id);
            }

            if (!empty($vehicle_reg)) {
                $this->db->where("fdcl.vehicle_reg", $vehicle_reg);
            }

            $this->db->order_by("fdcl.created_date DESC, fdcl.dc_log_id DESC");

            $query = $this->db->get("fleet_driver_change_log `fdcl`", $offset, $limit);

            if (!empty($query->num_rows()) && ($query->num_rows() > 0)) {
                $result = $query->result();
                $this->session->set_flashdata('message', 'The Vehicle data found.');
            } else {
                $this->session->set_flashdata('message', 'The Vehicle data not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account or valid Vehicle ID provided.');
        }

        return $result;
    }

    /*
    *	Function to get vehicle statuses for specific account_id. If they aren't exists get the default ones
    */
    public function get_vehicle_statuses($account_id = false, $status_id = false, $ordered = false)
    {
        $result = false;

        if (!empty($status_id)) {
            $select = "SELECT * FROM fleet_vehicle_status WHERE status_id = $status_id";
        } else {
            $select = "SELECT * FROM fleet_vehicle_status WHERE account_id = $account_id
						UNION ALL SELECT * FROM fleet_vehicle_status WHERE account_id = 0
					AND NOT EXISTS
						( SELECT 1 FROM fleet_vehicle_status WHERE account_id = $account_id )";
        }
        $query = $this->db->query($select);

        if ($query->num_rows() > 0) {
            $this->session->set_flashdata('message', 'Vehicle status(es) found.');
            $ordered = format_boolean($ordered);
            if ($ordered) {
                foreach ($query->result_array() as $key => $row) {
                    $result[$row['status_id']] = $row;
                }
            } else {
                $result = $query->result_array();
            }
        } else {
            $this->session->set_flashdata('message', 'Vehicle status(es) not found.');
        }
        return $result;
    }



    /*
    *	Function to get vehicle suppliers for specific account_id. If they aren't exists get the default ones.
    */
    public function get_vehicle_suppliers($account_id = false, $supplier_id = false, $ordered = false)
    {
        $result = false;

        if (!empty($supplier_id)) {
            $select = "SELECT * FROM fleet_vehicle_supplier WHERE supplier_id = $supplier_id";
        } else {
            $select = "SELECT * FROM fleet_vehicle_supplier WHERE account_id = $account_id
						UNION ALL SELECT * FROM fleet_vehicle_supplier WHERE account_id = 0
					AND NOT EXISTS
						( SELECT 1 FROM fleet_vehicle_supplier WHERE account_id = $account_id )";
        }
        $query = $this->db->query($select);

        if ($query->num_rows() > 0) {
            $this->session->set_flashdata('message', 'Vehicle supplier(s) found.');
            $ordered = format_boolean($ordered);
            if ($ordered) {
                foreach ($query->result_array() as $key => $row) {
                    $result[$row['supplier_id']] = $row;
                }
            } else {
                $result = $query->result_array();
            }
        } else {
            $this->session->set_flashdata('message', 'Vehicle supplier(s) not found.');
        }
        return $result;
    }


    /*
    *	Function to create an event log in vehicle events history
    */
    public function create_vehicle_event($account_id = false, $vehicle_id = false, $post_data = false)
    {
        $result = $event_id = false;

        if ((!empty($account_id)) && (!empty($vehicle_id)) && (!empty($post_data))) {
            $data = [];

            foreach ($post_data as $key => $value) {
                if (in_array($key, format_name_columns())) {
                    $value = format_name($value);
                } elseif (in_array($key, format_email_columns())) {
                    $value = format_email($value);
                } elseif (in_array($key, format_number_columns())) {
                    $value = format_number($value);
                } elseif (in_array($key, format_boolean_columns())) {
                    $value = format_boolean($value);
                } elseif (in_array($key, format_date_columns())) {
                    $value = format_date_db($value);
                } elseif (in_array($key, format_long_date_columns())) {
                    $value = format_datetime_db($value);
                } else {
                    $value = trim($value);
                }
                $data[$key] = $value;
            }

            $current_vehicle = $this->get_vehicles($account_id, $vehicle_id);
            if (!empty($current_vehicle)) {
                $data['vehicle_reg']	= $current_vehicle[0]->vehicle_reg;
                $data['account_id']		= $account_id;
                $data['vehicle_id']		= $vehicle_id;
                $data['event_status_id']= 1;
                $data['created_date'] 	= date('Y-m-d H:i:s');
                $data['created_by'] 	= $this->ion_auth->_current_user()->id;

                $data = $this->ssid_common->_filter_data('fleet_vehicle_event_history', $data);
                $this->db->insert("fleet_vehicle_event_history", $data);

                $event_id = $this->db->insert_id();

                if (($this->db->affected_rows() > 0) && (!empty($event_id))) {
                    $reference_no = $data['account_id'].'_'.$data['vehicle_id'].'_'.$event_id.'_'.date('Ymd_His');
                    $this->db->update("fleet_vehicle_event_history", ["reference_no" => $reference_no], ["event_id" => $event_id]);


                    $vehicle_history_log_data = [
                        "log_type"			=> "events",
                        "entry_id"			=> $event_id,
                        "vehicle_id"		=> $vehicle_id,
                        "vehicle_reg"		=> $current_vehicle[0]->vehicle_reg,
                        "action"			=> "create vehicle event",
                        "note"				=> (!empty($data['event_note'])) ? trim($data['event_note']) : null ,
                    ];

                    if (!empty($data['event_type_id'])) {
                        $current_values["event_type_id"] = $data['event_category_id'];
                    }

                    if (!empty($data['event_category_id'])) {
                        $current_values["event_category_id"] = $data['event_category_id'];
                    }

                    if (!empty($data['event_note'])) {
                        $current_values["event_note"] = trim($data['event_note']);
                    }

                    if (!empty($data['event_date'])) {
                        $current_values["event_date"] = $data['event_date'];
                    }

                    if (!empty($data['event_status_id'])) {
                        $current_values["event_status_id"] = $data['event_status_id'];
                    }

                    $vehicle_history_log_data['current_values'] = json_encode($current_values);

                    ## create vehicle history log
                    $vehicle_history_log = $this->create_vehicle_change_log($account_id, $vehicle_history_log_data);

                    $this->session->set_flashdata('message', 'The Vehicle Event data added successfully.');

                    $result = $this->db->get_where("fleet_vehicle_event_history", [ "event_id" => $event_id ])->row();
                } else {
                    $this->session->set_flashdata('message', 'The Vehicle Event data hasn\t been added.');
                }
            } else {
                $this->session->set_flashdata('message', 'Invalid Vehicle ID.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Valid Data provided - missing Account ID, Vehicle ID or Post Data.');
        }
        return $result;
    }



    /*
    *	Get Vehicle Event(s) record(s) data
    */
    public function get_vehicle_events($account_id = false, $event_id = false, $vehicle_id = false, $vehicle_reg = false, $where = false, $limit = DEFAULT_LIMIT, $offset = 0)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select("fveh.*, concat( u.first_name,' ',u.last_name ) as `created_by_full_name`");
            $this->db->select("fves.event_status_name");
            $this->db->select("fvet.event_type_name");
            $this->db->select("fvec.category_name");

            if (!empty($event_id)) {
                $this->db->where("fveh.event_id", $event_id);
            }

            if (!empty($vehicle_id)) {
                $this->db->where("fveh.vehicle_id", $vehicle_id);
            }

            if (!empty($vehicle_reg)) {
                $this->db->where("fveh.vehicle_reg", $vehicle_reg);
            }

            if (!empty($where)) {
                if (is_object($where)) {
                    $where = get_object_vars($where);
                }
                $this->db->where($where);
            }

            $this->db->join("user u", "u.id = fveh.created_by", "left");
            $this->db->join("fleet_vehicle_event_status fves", "fves.event_status_id = fveh.event_status_id", "left");
            $this->db->join("fleet_vehicle_event_type fvet", "fvet.event_type_id = fveh.event_type_id", "left");
            $this->db->join("fleet_vehicle_event_category fvec", "fvec.event_category_id = fveh.event_category_id", "left");

            $arch_where = "( fveh.archived != 1 or fveh.archived is NULL )";
            $this->db->where($arch_where);

            $this->db->where("fveh.account_id", $account_id);

            $this->db->order_by("fveh.event_id DESC");

            $query = $this->db->get("fleet_vehicle_event_history `fveh`", $limit, $offset);

            if (!empty($query->num_rows()) && ($query->num_rows() > 0)) {
                $result 	= $query->result();
                $this->session->set_flashdata('message', 'Vehicle Event(s) data found.');
            } else {
                $this->session->set_flashdata('message', 'Vehicle Event(s) data not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account details provided.');
        }

        return $result;
    }


    /*
    *	Function to get vehicle events stauses for specific account_id. If they aren't exists get the default ones.
    */
    public function get_vehicle_event_statuses($account_id = false, $event_status_id = false, $ordered = false)
    {
        $result = false;

        if (!empty($event_status_id)) {
            $select = "SELECT * FROM fleet_vehicle_event_status WHERE event_status_id = $event_status_id";
        } else {
            $select = "SELECT * FROM fleet_vehicle_event_status WHERE account_id = $account_id
						UNION ALL SELECT * FROM fleet_vehicle_event_status WHERE account_id = 0
					AND NOT EXISTS
						( SELECT 1 FROM fleet_vehicle_event_status WHERE account_id = $account_id )";
        }
        $query = $this->db->query($select);

        if ($query->num_rows() > 0) {
            $this->session->set_flashdata('message', 'Vehicle Event Statuses(s) found.');
            $ordered = format_boolean($ordered);
            if ($ordered) {
                foreach ($query->result_array() as $key => $row) {
                    $result[$row['event_status_id']] = $row;
                }
            } else {
                $result = $query->result_array();
            }
        } else {
            $this->session->set_flashdata('message', 'Vehicle Event Statuses(s) not found.');
        }
        return $result;
    }


    /*
    *	Function to get vehicle event categories for specific account_id. If they aren't exists get the default ones.
    */
    public function get_vehicle_event_categories($account_id = false, $event_category_id = false, $ordered = false)
    {
        $result = false;

        if (!empty($event_category_id)) {
            $select = "SELECT * FROM fleet_vehicle_event_category WHERE event_category_id = $event_category_id";
        } else {
            $select = "SELECT * FROM fleet_vehicle_event_category WHERE account_id = $account_id
						UNION ALL SELECT * FROM fleet_vehicle_event_category WHERE account_id = 0
					AND NOT EXISTS
						( SELECT 1 FROM fleet_vehicle_event_category WHERE account_id = $account_id )";
        }
        $query = $this->db->query($select);

        if ($query->num_rows() > 0) {
            $this->session->set_flashdata('message', 'Vehicle Event Category(ies) found.');
            $ordered = format_boolean($ordered);
            if ($ordered) {
                foreach ($query->result_array() as $key => $row) {
                    $result[$row['event_category_id']] = $row;
                }
            } else {
                $result = $query->result_array();
            }
        } else {
            $this->session->set_flashdata('message', 'Vehicle Event Category(ies) not found.');
        }
        return $result;
    }


    /*
    *	Function to get vehicle event categories for specific account_id. If they aren't exists get the default ones.
    */
    public function get_vehicle_event_types($account_id = false, $event_type_id = false, $event_category_id = false, $ordered = false)
    {
        $result = false;

        if (!empty($event_type_id)) {
            $select = "SELECT * FROM fleet_vehicle_event_type WHERE event_type_id = $event_type_id";
        } elseif (!empty($event_category_id)) {
            $select = "SELECT * FROM fleet_vehicle_event_type WHERE event_category_id = $event_category_id and account_id = $account_id";
        } else {
            $select = "SELECT * FROM fleet_vehicle_event_type WHERE account_id = $account_id
						UNION ALL SELECT * FROM fleet_vehicle_event_type WHERE account_id = 0
					AND NOT EXISTS
						( SELECT 1 FROM fleet_vehicle_event_type WHERE account_id = $account_id )";
        }
        $query = $this->db->query($select);

        if ($query->num_rows() > 0) {
            $this->session->set_flashdata('message', 'Vehicle Event Type(s) found.');
            $ordered = format_boolean($ordered);
            if ($ordered) {
                foreach ($query->result_array() as $key => $row) {
                    $result[$row['event_category_id']][$row['event_type_id']] = $row;
                }
            } else {
                $result = $query->result_array();
            }
        } else {
            $this->session->set_flashdata('message', 'Vehicle Event Type(s) not found.');
        }
        return $result;
    }


    /*
    *	Function to get vehicle driver(s)
    */
    public function get_vehicle_drivers($account_id = false, $driver_id = false, $ordered = false)
    {
        $result = $drivers = false;

        if (!empty($account_id)) {
            if (!empty($driver_id)) {
                $drivers[0] = $this->ion_auth->get_user_by_id($account_id, $driver_id);
            } else {
                $drivers = $this->ion_auth->get_users_by_account_id($account_id);
            }

            if (!empty($drivers)) {
                $this->session->set_flashdata('message', 'Driver(s) found.');
                $ordered = format_boolean($ordered);
                if ($ordered) {
                    foreach ($drivers as $key => $row) {
                        $result[$row->id] = $row;
                    }
                } else {
                    $result = $drivers;
                }
            } else {
                $this->session->set_flashdata('message', 'Driver(s) not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'Missing Account ID.');
        }
        return $result;
    }


    /*
    *	Function to get vehicle log(s)
    */
    public function get_vehicle_change_log($account_id = false, $change_log_id = false, $vehicle_id = false, $vehicle_reg = false, $where = false, $limit = DEFAULT_LIMIT, $offset = false)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select("fvcl.*, concat( u.first_name,' ',u.last_name ) as `created_by_full_name`");

            if (!empty($change_log_id)) {
                $this->db->where("fvcl.change_log_id", $change_log_id);
            }

            if (!empty($vehicle_id)) {
                $this->db->where("fvcl.vehicle_id", $vehicle_id);
            }

            if (!empty($vehicle_reg)) {
                $this->db->where("fvcl.vehicle_reg", $vehicle_reg);
            }

            if (!empty($where)) {
                if (is_object($where)) {
                    $where = get_object_vars($where);
                }
                $this->db->where($where);
            }

            $this->db->join("user u", "u.id = fvcl.created_by", "left");

            $arch_where = "( fvcl.archived != 1 or fvcl.archived is NULL )";
            $this->db->where($arch_where);

            $this->db->where("fvcl.account_id", $account_id);

            $this->db->order_by("change_log_id DESC");

            $query = $this->db->get("fleet_vehicle_change_log `fvcl`", $limit, $offset);

            if (!empty($query->num_rows()) && ($query->num_rows() > 0)) {
                $result 	= $query->result();
                $this->session->set_flashdata('message', 'Vehicle Change Log(s) data found.');
            } else {
                $this->session->set_flashdata('message', 'Vehicle Change Log(s) data not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account details provided.');
        }

        return $result;
    }


    /**
    *	 Function to create a vehicle history log
    **/
    public function create_vehicle_change_log($account_id = false, $vehicle_history_log_data = false)
    {
        if (!empty($account_id) && !empty($vehicle_history_log_data)) {
            $data["account_id"] 	= $account_id;
            $data["created_by"] 	= $this->ion_auth->_current_user()->id;
            $data["date_created"] 	= date('Y-m-d H:i:s');

            $data = array_merge($data, $vehicle_history_log_data);
            $this->db->insert("fleet_vehicle_change_log", $data);

            if ($this->db->affected_rows() > 0) {
                ## $this->session->set_flashdata( 'message','The action has been succesfully Logged in the System.' );
                return $this->db->insert_id();
            } else {
                ## $this->session->set_flashdata( 'message','The action has NOT been Logged in the System.' );
                return false;
            }
        } else {
            $this->session->set_flashdata('message', 'No Account details or no Log Data provided.');
        }
    }



    /*
    *	Function to get event tracking log(s)
    */
    public function get_event_tracking_logs($account_id = false, $event_tracking_log_id = false, $event_id = false, $vehicle_id = false, $vehicle_reg = false, $where = false, $limit = DEFAULT_LIMIT, $offset = false)
    {
        $result = false;

        if (!empty($account_id)) {
            $this->db->select("fvetl.*, concat( u.first_name,' ',u.last_name ) as `created_by_full_name`", false);
            $this->db->select("fveh.vehicle_id, fv.vehicle_reg", false);

            if (!empty($event_tracking_log_id)) {
                $this->db->where("fvetl.log_id", $event_tracking_log_id);
            }

            if (!empty($event_id)) {
                $this->db->where("fvetl.event_id", $event_id);
            }

            if (!empty($vehicle_id)) {
                $this->db->where("fveh.vehicle_id", $vehicle_id);
            }

            if (!empty($vehicle_reg)) {
                $this->db->where("fvetl.vehicle_reg", $vehicle_reg);
            }

            if (!empty($where)) {
                if (is_object($where)) {
                    $where = get_object_vars($where);
                }
                $this->db->where($where);
            }

            $this->db->join("user u", "u.id = fvetl.created_by", "left");
            $this->db->join("fleet_vehicle_event_history fveh", "fveh.event_id = fvetl.event_id", "left");
            $this->db->join("fleet_vehicle fv", "fv.vehicle_id = fveh.vehicle_id", "left");

            $arch_where = "( fvetl.archived != 1 or fvetl.archived is NULL )";
            $this->db->where($arch_where);

            $this->db->where("fvetl.account_id", $account_id);

            $this->db->order_by("log_id DESC");

            $query = $this->db->get("fleet_vehicle_event_tracking_log `fvetl`", $limit, $offset);

            if (!empty($query->num_rows()) && ($query->num_rows() > 0)) {
                $result 	= $query->result();
                $this->session->set_flashdata('message', 'Vehicle Event Tracking Log(s) data found.');
            } else {
                $this->session->set_flashdata('message', 'Vehicle Event Tracking Log(s) data not found.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account details provided.');
        }

        return $result;
    }


    /*
    *	The function to add the tracker log to the specific event
    */
    public function add_event_tracking_log($account_id = false, $event_id = false, $log_note = false)
    {
        $result = false;

        if (!empty($account_id)) {
            if (!empty($event_id)) {
                if (!empty($log_note)) {
                    $dataset = [
                        "account_id" 	=> $account_id,
                         "event_id" 	=> $event_id,
                         "log_note"		=> $log_note,
                         "date_created" => date('Y-m-d H:i:s'),
                         "created_by" 	=> $this->ion_auth->_current_user()->id
                    ];

                    $data = $this->ssid_common->_filter_data('fleet_vehicle_event_tracking_log', $dataset);
                    $this->db->insert("fleet_vehicle_event_tracking_log", $dataset);
                    if ($this->db->affected_rows() > 0) {
                        $data['log_id'] = $this->db->insert_id();
                        $result 		= $data;
                        $this->session->set_flashdata('message', 'The log has been successfuly added.');
                    } else {
                        $this->session->set_flashdata('message', 'The log couldn\'t be saved.');
                    }
                } else {
                    $this->session->set_flashdata('message', 'No Log message provided.');
                }
            } else {
                $this->session->set_flashdata('message', 'No Event ID provided.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account details provided.');
        }
        return $result;
    }


    /*
    *	The function to produce the simple statistics for the fleet manager.
    * 	Account ID required
    */
    public function get_simple_stats($account_id = false)
    {
        $result = false;

        if (!empty($account_id)) {
            $todays_date = date('Y-m-d H:i:s');

            $this->db->select("
				SUM( CASE when fv.mot_expiry < '".$todays_date."' THEN 1 ELSE 0 END ) as `Expired_MOT`,
				SUM( CASE when fv.tax_expiry < '".$todays_date."' THEN 1 ELSE 0 END ) as `Expired_TAX`,
				SUM( CASE when fv.is_insured != 1 THEN 1 ELSE 0 END ) as `Not_Insured`
			", false);

            $this->db->where("fv.account_id", $account_id);

            $where_arch = "( ( fv.archived != 1 ) || ( fv.archived IS NULL ) )";
            $this->db->where($where_arch);

            $this->db->order_by("fv.vehicle_id DESC");

            $query = $this->db->get("fleet_vehicle fv");

            if (!empty($query->num_rows()) && ($query->num_rows() > 0)) {
                $this->session->set_flashdata('message', 'Fleet stats found');
                $result = $query->result();
            } else {
                $this->session->set_flashdata('message', 'No Instant stats at available at the moment.');
            }
        } else {
            $this->session->set_flashdata('message', 'No Account details provided.');
        }
        return $result;
    }


    /*
    *	Update V. Event profile
    */
    public function update_vehicle_event($account_id = false, $event_id = false, $event_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($event_id) && !empty($event_data)) {
            $data = [];

            foreach ($event_data as $key => $value) {
                if (in_array($key, format_name_columns())) {
                    $value = format_name($value);
                } elseif (in_array($key, format_email_columns())) {
                    $value = format_email($value);
                } elseif (in_array($key, format_number_columns())) {
                    $value = format_number($value);
                } elseif (in_array($key, format_boolean_columns())) {
                    $value = format_boolean($value);
                } elseif (in_array($key, format_date_columns())) {
                    $value = format_date_db($value);
                } elseif (in_array($key, format_long_date_columns())) {
                    $value = format_datetime_db($value);
                } else {
                    $value = trim($value);
                }
                $data[$key] = $value;
            }

            $data['modified_date'] 	= date('Y-m-d H:i:s');
            $data['modified_by'] 	= $this->ion_auth->_current_user()->id;

            if (!empty($data)) {
                $data =  $this->ssid_common->_filter_data('fleet_vehicle_event_history', $data);
                $restricted_columns = ['created_by', 'created_date', 'archived'];
                foreach ($data as $key => $value) {
                    if (in_array($key, $restricted_columns)) {
                        unset($data[$key]);
                    }
                }

                $this->db->where('event_id', $event_id)->update('fleet_vehicle_event_history', $data);
                if (($this->db->trans_status() !== false) && ($this->db->affected_rows() > 0)) {
                    $result = $this->get_vehicle_events($account_id, $event_id);


                    /* 					$vehicle_data = $this->get_vehicle( $account_id, $result['vehicle_id'] );

                                        $vehicle_history_log_data = [
                                            "log_type"			=> "events",
                                            "entry_id"			=> $event_id,
                                            "vehicle_id"		=> $result['vehicle_id'],  ##########################
                                            "vehicle_reg"		=> $vehicle_data['vehicle_reg'], ################
                                            "action"			=> "update vehicle event",
                                            "note"				=> ( !empty( $data['event_note'] ) ) ? trim( $data['event_note'] ) : NULL ,
                                        ];

                                        if( !empty( $data['event_type_id'] ) ){
                                            $current_values["event_type_id"] = $data['event_category_id'];
                                        }

                                        if( !empty( $data['event_category_id'] ) ){
                                            $current_values["event_category_id"] = $data['event_category_id'];
                                        }

                                        if( !empty( $data['event_note'] ) ){
                                            $current_values["event_note"] = trim( $data['event_note'] );
                                        }

                                        if( !empty( $data['event_date'] ) ){
                                            $current_values["event_date"] = $data['event_date'];
                                        }

                                        if( !empty( $data['event_status_id'] ) ){
                                            $current_values["event_status_id"] = $data['event_status_id'];
                                        }

                                        $vehicle_history_log_data['current_values'] = json_encode( $current_values );

                                        ## create vehicle history log
                                        $vehicle_history_log = $this->create_vehicle_change_log( $account_id, $vehicle_history_log_data );

                                        $this->session->set_flashdata( 'message', 'The Vehicle Event data added successfully.' ); */

                    $this->session->set_flashdata('message', 'Event Profile updated successfully.');
                } else {
                    $this->session->set_flashdata('message', 'The Event profile hasn\'t been changed.');
                }
            }
        } else {
            $this->session->set_flashdata('message', 'No Account ID, no Event Id or no new data supplied.');
        }
        return $result;
    }


    /*
    *	public function to get a specific status id by the status type for the vehicle
    */
    public function get_status_by_type($account_id = false, $status_type = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($status_type)) {
            $query_string = "
			SELECT ( CASE 
				WHEN EXISTS ( SELECT 1 FROM `fleet_vehicle_status` WHERE `account_id` = $account_id AND `status_type` = '".$status_type."' )
				THEN ( SELECT `status_id` FROM `fleet_vehicle_status` WHERE `account_id` = $account_id AND `status_type` = '".$status_type."' )
				ELSE ( SELECT `status_id` FROM `fleet_vehicle_status` WHERE `account_id` = 0 AND `status_type` = '".$status_type."' )
			END ) as `status_id`
			";
            $query_result = $this->db->query($query_string);

            if ((null !== $query_result->result()) && (!empty($query_result->num_rows()))) {
                $result = $query_result->row()->status_id;
            }
        }
        return $result;
    }
}
