<?php

namespace Application\Modules\Service\Models;

use App\Adapter\Model;
use System\Libraries\CI_Upload;

class DocumentHandlerModel extends Model
{
	/**
	 * @var \System\Libraries\CI_Upload $upload
	 */
	private $upload;

	private $customer_service;

	public function __construct()
    {
        parent::__construct();
        $section 	   = explode("/", $_SERVER["SCRIPT_NAME"]);
        $this->app_root= $_SERVER["DOCUMENT_ROOT"]."/".$section[1]."/";
        $this->app_root= str_replace('/index.php', '', $this->app_root);
        $this->upload = new CI_Upload();
        $this->customer_service = new CustomerModel();
        #$this->load->model( 'serviceapp/Job_model','job_service' );
    }

    private $numerical_fields = [ 'account_id','site_id','job_id','customer_id','assessment_id' ];
    private $image_file_types = [ 'jpeg','jpg','jpe','png','bmp','gif','tiff','tif', 'heif', 'heifs', 'heic', 'heics' ];
    private $doc_approval_statuses = [ 'Approved','Pending','Declined' ];

    /** Process Response files **/
    public function upload_audit_files($account_id = false, $document_data = false, $document_group = false)
    {
        $result 	= false;
        //if( !empty($account_id) && !empty($_FILES['audit_files']['name']) && !empty( $postdata['audit_files'] ) ){
        if (!empty($account_id) && !empty($_FILES['audit_files']['name']) && !empty($document_data)) {
            $upload_files  = [];

            foreach ($_FILES['audit_files'] as $attr => $segment_data) {
                if (strtolower($attr) == 'name') {
                    foreach ($segment_data as $segment => $question_data) {
                        if (!is_array($question_data)) {
                            $this->session->set_flashdata('message', 'Error: Invalid structure for questions data.');
                            return false;
                        }

                        foreach ($question_data as $question_id => $loc_files) {
                            foreach ($loc_files as $k => $file_name) {
                                $doc_reference = 'ssid'.$account_id.'_'.$segment.$question_id;

                                ## Prepare temp table
                                switch($document_group) {
                                    case 'site':
                                        $folder			= 'sites';
                                        $target_table 	= 'site_document_uploads';
                                        $doc_reference .= (!empty($document_data['site_id'])) ? 'vreg'.$document_data['site_id'] : '';
                                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                                        $record_path_id = (!empty($document_data['site_id'])) ? $document_data['site_id'] : 'not-specified';
                                        break;
                                    case 'asset':
                                        $folder			= 'assets';
                                        $target_table 	= 'asset_document_uploads';
                                        $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                                        $doc_reference .= (!empty($document_data['asset_id'])) ? 'ast'.$document_data['asset_id'] : '';
                                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                                        $record_path_id = (!empty($document_data['asset_id'])) ? $document_data['asset_id'] : 'not-specified';
                                        break;
                                    case 'fleet':
                                        $folder			= 'fleet';
                                        $target_table 	= 'fleet_document_uploads';
                                        $doc_reference .= (!empty($document_data['vehicle_reg'])) ? 'vreg'.preg_replace('/\s+/', '', $document_data['vehicle_reg']) : '';
                                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                                        $record_path_id = (!empty($document_data['vehicle_reg'])) ? $document_data['vehicle_reg'] : 'not-specified';
                                        break;
                                    case 'person':
                                    case 'people':
                                        $folder			= 'people';
                                        $target_table 	= 'people_document_uploads';
                                        $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                                        $doc_reference .= (!empty($document_data['asset_id'])) ? 'ast'.$document_data['asset_id'] : '';
                                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                                        $doc_reference .= (!empty($document_data['person_id'])) ? 'per'.$document_data['person_id'] : '';
                                        $record_path_id = (!empty($document_data['person_id'])) ? $document_data['person_id'] : 'not-specified';
                                        break;
                                    case 'risk_assessment':
                                        $folder			= 'risk-assessments';
                                        $target_table 	= 'ra_document_uploads';
                                        $doc_reference .= (!empty($document_data['job_id'])) ? 'j'.$document_data['job_id'] : '';
                                        $doc_reference .= (!empty($document_data['site_id'])) ? 's'.$document_data['site_id'] : '';
                                        $doc_reference .= (!empty($document_data['customer_id'])) ? 'c'.$document_data['customer_id'] : '';
                                        $doc_reference .= (!empty($document_data['assessment_id'])) ? 'ra'.$document_data['assessment_id'] : '';
                                        $record_path_id = (!empty($document_data['assessment_id'])) ? $document_data['assessment_id'] : 'not-specified';
                                        break;
                                    case 'job':
                                        $folder			= 'job';
                                        $target_table 	= 'job_document_uploads';

                                        $task_id 	    = (!empty($document_data['task_id'])) ? $document_data['task_id'] : false;

                                        $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                                        $doc_reference .= (!empty($document_data['asset_id'])) ? 'ast'.$document_data['asset_id'] : '';
                                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                                        $doc_reference .= (!empty($document_data['job_id'])) ? 'job'.$document_data['job_id'] : '';
                                        $doc_reference .= (!empty($document_data['task_id'])) ? 'task'.$document_data['task_id'] : '';

                                        $record_path_id = (!empty($document_data['job_id'])) ? $document_data['job_id'] : 'not-specified';

                                        if (!empty($task_id)) {
                                            #$target_table 	= 'job_task_document_uploads';
                                            $record_path_id .= '/tasks/'.$task_id;
                                        }

                                        break;
                                    case 'customer':
                                        $folder			= 'customer';
                                        $target_table 	= 'customer_document_uploads';
                                        $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                                        $doc_reference .= (!empty($document_data['asset_id'])) ? 'ast'.$document_data['asset_id'] : '';
                                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                                        $doc_reference .= (!empty($document_data['customer_id'])) ? 'customer'.$document_data['customer_id'] : '';
                                        $record_path_id = (!empty($document_data['customer_id'])) ? $document_data['customer_id'] : 'not-specified';
                                        break;
                                    case 'premises':
                                        $folder			= 'premises';
                                        $target_table 	= 'premises_document_uploads';
                                        $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                                        $doc_reference .= (!empty($document_data['premises_id'])) ? 'ast'.$document_data['premises_id'] : '';
                                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                                        $record_path_id = (!empty($document_data['premises_id'])) ? $document_data['premises_id'] : 'not-specified';
                                        break;

                                    default:
                                        $target_table 	= 'document_uploads';
                                        $folder			= 'others';
                                        $record_path_id = 'not-specified';
                                        break;
                                }

                                ## Proces individual files
                                $document_data['doc_type'] 		= 'Segment Files';
                                $document_data['document_name'] = $file_name;
                                $file_uuid				 		= $doc_reference.'_'.preg_replace('/\s+/', '', $file_name);

                                $parse_file_name				= $this->_parse_file_name($file_uuid);
                                $file_uuid 						= ($parse_file_name) ? $parse_file_name : $file_uuid;

                                $document_data['doc_reference'] = $file_uuid;
                                $document_data['upload_segment']= $segment;
                                $document_data['question_id']   = $question_id;

                                $temp_document_id  = $this->_create_document_placeholder($account_id, $document_data, $target_table);

                                if (!empty($temp_document_id)) {
                                    $_FILES['doc_file']['name'] 	= preg_replace('/\s+/', '', $file_name);
                                    $_FILES['doc_file']['type'] 	= $_FILES['audit_files']['type'][$segment][$question_id][$k];
                                    $_FILES['doc_file']['tmp_name'] = preg_replace('/\s+/', '', $_FILES['audit_files']['tmp_name'][$segment][$question_id][$k]);
                                    $_FILES['doc_file']['error'] 	= $_FILES['audit_files']['error'][$segment][$question_id][$k];
                                    $_FILES['doc_file']['size'] 	= $_FILES['audit_files']['size'][$segment][$question_id][$k];

                                    $document_path 			 		= '_account_assets/accounts/'.$account_id.'/'.$folder.'/'.$record_path_id.'/';
                                    #$upload_path 			 		= $this->app_root.$document_path;//uncomment if you want to use the absolute path
                                    $upload_path 			 		= $document_path; //Subfolder, resolve path at time of rendering

                                    if (!is_dir($upload_path)) {
                                        if (!mkdir($upload_path, 0755, true)) {
                                            $this->db->where('account_id', $account_id)
                                                ->where('document_id', $temp_document_id)
                                                ->delete($target_table);
                                            $this->session->set_flashdata('message', 'Error: Unable to create upload location');
                                            return false;
                                        }
                                    }

                                    //$file_name			 = $_FILES['doc_file']['name'];
                                    $file_type				 = $_FILES['doc_file']['type'];
                                    $file_location			 = $upload_path.$file_uuid;

                                    $config['upload_path'] 	 = $upload_path;
                                    $config['allowed_types'] = 'pdf|csv|xls|xlsx|doc|docx|gif|jpg|JPG|jpeg|JPEG|png|ods|txt|heif|heifs|heic|heics|bmp|wmpb';
                                    $config['max_size']      = 25000;
                                    $config['file_name'] 	 = strtolower($file_uuid);
                                    $config['overwrite']     = true;
                                    $config['remove_spaces'] = true;

                                    $this->upload->initialize($config);

                                    if ($this->upload->do_upload('doc_file')) {
                                        $ext = pathinfo(base_url($upload_path.$file_uuid), PATHINFO_EXTENSION);
                                        $update_document_data = [
                                            'document_id'=>$temp_document_id,
                                            //'doc_reference'=>$file_uuid,
                                            'document_name'=>$file_name,
                                            'document_link'=>base_url($document_path.$file_uuid),
                                            'document_location'=>$file_location,
                                            'document_extension'=> $ext,
                                            'created_by'=>$this->ion_auth->_current_user->id
                                        ];

                                        ## Resize File if it's an Image and maintain Orientation
                                        $uploaded_image = $this->upload->data();
                                        //if( in_array( $uploaded_image['image_type'], $this->image_file_types ) ){
                                        //$resized_img	= $this->resize_image( $uploaded_image['file_name'], $uploaded_image['file_path'] );
                                        //}

                                        # Update temp file
                                        $upload_complete = $this->_update_document($account_id, $temp_document_id, $update_document_data, $target_table);
                                        if ($upload_complete) {
                                            $uploaded_data['documents'][$k] = array_merge($document_data, $update_document_data);
                                        }
                                    } else {
                                        ## delete the temp file
                                        $this->db->where('account_id', $account_id)
                                            ->where('document_id', $temp_document_id)
                                            ->delete($target_table);

                                        $uploaded_data['errors'][$k] = [
                                            'file'=>$file_name,
                                            'error'=>$this->upload->display_errors()
                                        ];
                                    }
                                } else {
                                    $this->session->set_flashdata('message', 'Error: Unable to created a file in the DB ');
                                    return false;
                                }
                            }
                        }
                    }
                }
            }
            if (!empty($uploaded_data)) {
                $errors = !empty($uploaded_data['errors']) ? ', with some Errors!' : '';
                $this->session->set_flashdata('message', 'Documents uploaded successfully'.$errors);
                $result = $uploaded_data;
            }
        } else {
            $this->session->set_flashdata('message', 'No files were selected');
        }
        return $result;
    }


    /** Process files **/
    public function upload_files($account_id = false, $postdata = false, $document_group = false, $folder = false)
    {
        $result 	= false;

        if (!empty($account_id) && !empty($_FILES['user_files']['name'])) {
            $document_data = [];
            $doc_reference = 'ssid'.$account_id.'_';

            foreach ($postdata as $col=>$val) {
                $val				 = (!is_array($val)) ? trim($val) : $val;
                $document_data[$col] = (in_array($col, $this->numerical_fields)) ? (int)$val : $val;
            }

            if (!empty($document_data)) {
                switch($document_group) {
                    case 'site':
                        $folder			= 'sites';
                        $target_table 	= 'site_document_uploads';
                        $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                        $record_path_id = (!empty($document_data['site_id'])) ? $document_data['site_id'] : 'not-specified';
                        break;
                    case 'asset':
                        $folder			= 'assets';
                        $asset_id		= (!empty($document_data['asset_id'])) ? $document_data['asset_id'] : '';
                        $target_table 	= 'asset_document_uploads';
                        $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                        $doc_reference .= (!empty($document_data['asset_id'])) ? 'ast'.$document_data['asset_id'] : '';
                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                        $record_path_id = (!empty($document_data['asset_id'])) ? $document_data['asset_id'] : 'not-specified';
                        break;
                    case 'vehicle':
                    case 'fleet':
                        $folder			= 'fleet';
                        $target_table 	= 'fleet_document_uploads';
                        $doc_reference .= (!empty($document_data['vehicle_reg'])) ? 'vreg'.$document_data['vehicle_reg'] : '';
                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                        $record_path_id = (!empty($document_data['vehicle_reg'])) ? $document_data['vehicle_reg'] : 'not-specified';
                        break;
                    case 'risk_assessment':
                        $folder			= 'risk-assessments';
                        $target_table 	= 'ra_document_uploads';
                        $doc_reference .= (!empty($document_data['job_id'])) ? 'j'.$document_data['job_id'] : '';
                        $doc_reference .= (!empty($document_data['site_id'])) ? 's'.$document_data['site_id'] : '';
                        $doc_reference .= (!empty($document_data['customer_id'])) ? 'c'.$document_data['customer_id'] : '';
                        $doc_reference .= (!empty($document_data['assessment_id'])) ? 'ra'.$document_data['assessment_id'] : '';
                        $record_path_id = (!empty($document_data['assessment_id'])) ? $document_data['assessment_id'] : 'not-specified';
                        break;
                    case 'person':
                    case 'people':
                        $folder			= 'people';
                        $target_table 	= 'people_document_uploads';
                        $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                        $doc_reference .= (!empty($document_data['asset_id'])) ? 'ast'.$document_data['asset_id'] : '';
                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                        $doc_reference .= (!empty($document_data['person_id'])) ? 'per'.$document_data['person_id'] : '';
                        $record_path_id = (!empty($document_data['person_id'])) ? $document_data['person_id'] : 'not-specified';
                        break;

                    case 'job':
                        $folder			= 'job';
                        $target_table 	= 'job_document_uploads';

                        $task_id 	    = (!empty($document_data['task_id'])) ? $document_data['task_id'] : false;

                        $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                        $doc_reference .= (!empty($document_data['asset_id'])) ? 'ast'.$document_data['asset_id'] : '';
                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                        $doc_reference .= (!empty($document_data['job_id'])) ? 'job'.$document_data['job_id'] : '';
                        $record_path_id = (!empty($document_data['job_id'])) ? $document_data['job_id'] : 'not-specified';

                        if (!empty($task_id)) {
                            #$target_table 	= 'job_task_document_uploads';
                            $record_path_id .= '/tasks/'.$task_id;
                        }

                        break;

                    case 'customer':
                        $folder			= 'customer';
                        $target_table 	= 'customer_document_uploads';
                        $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                        $doc_reference .= (!empty($document_data['asset_id'])) ? 'ast'.$document_data['asset_id'] : '';
                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                        $doc_reference .= (!empty($document_data['customer_id'])) ? 'customer'.$document_data['customer_id'] : '';
                        $record_path_id = (!empty($document_data['customer_id'])) ? $document_data['customer_id'] : 'not-specified';
                        break;
                    case 'premises':
                        $folder			= 'premises';
                        $premises_id	= (!empty($document_data['premises_id'])) ? $document_data['premises_id'] : '';
                        $target_table 	= 'premises_document_uploads';
                        $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                        $doc_reference .= (!empty($document_data['premises_id'])) ? 'ast'.$document_data['premises_id'] : '';
                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                        $record_path_id = (!empty($document_data['premises_id'])) ? $document_data['premises_id'] : 'not-specified';
                        break;

                    default:
                        $target_table 	= 'document_uploads';
                        $folder			= (!empty($folder)) ? $folder : 'others';
                        $record_path_id = 'not-specified';
                        break;
                }

                $filesCount = count($_FILES['user_files']['name']);

                for ($i = 0; $i < $filesCount; $i++) {
                    $document_data['doc_type'] 		= (!empty($document_data['doc_type'])) ? ucwords(strtolower($document_data['doc_type'])) : 'Others';
                    $document_data['document_name'] = (!empty($_FILES['user_files']['name'][$i])) ? $_FILES['user_files']['name'][$i] : null;
                    $file_uuid				 		= $doc_reference.'_'.preg_replace('/\s+/', '', $_FILES['user_files']['name'][$i]);

                    $parse_file_name				= $this->_parse_file_name($file_uuid);
                    $file_uuid 						= ($parse_file_name) ? $parse_file_name : $file_uuid;

                    $document_data['doc_reference'] = $file_uuid;
                    $sub_folder 					= (!empty($document_data['doc_type'])) ? $document_data['doc_type'].'/' : false;

                    $temp_document_id  = $this->_create_document_placeholder($account_id, $document_data, $target_table);


                    if (!empty($temp_document_id)) {
                        $_FILES['doc_file']['name'] 	= $_FILES['user_files']['name'][$i];
                        $_FILES['doc_file']['type'] 	= $_FILES['user_files']['type'][$i];
                        $_FILES['doc_file']['tmp_name'] = $_FILES['user_files']['tmp_name'][$i];
                        $_FILES['doc_file']['error'] 	= $_FILES['user_files']['error'][$i];
                        $_FILES['doc_file']['size'] 	= $_FILES['user_files']['size'][$i];

                        $document_path 			 		= '_account_assets/accounts/'.$account_id.'/'.$folder.'/';
                        ##$upload_path 			 		= $this->app_root.$document_path; //uncomment if you want to use the absolute path
                        $upload_path 			 		= $document_path; //Subfolder, resolve path at time of rendering
                        $upload_path 					.= (!empty($record_path_id)) ? $record_path_id.'/' : '';
                        $upload_path 					.= (strtolower($document_data['doc_type']) == 'profile images') ? 'profile_images/' : (!empty($sub_folder) ? $sub_folder : '');


                        if (!is_dir($upload_path)) {
                            if (!mkdir($upload_path, 0755, true)) {
                                $this->db->where('account_id', $account_id)
                                    ->where('document_id', $temp_document_id)
                                    ->delete($target_table);
                                $this->session->set_flashdata('message', 'Error: Unable to create upload location');
                                return false;
                            }
                        }

                        $file_name				 = $_FILES['doc_file']['name'];
                        $file_type				 = $_FILES['doc_file']['type'];
                        $file_location			 = $upload_path.$file_uuid;

                        $config['upload_path'] 	 = $upload_path;
                        $config['allowed_types'] = 'pdf|csv|xls|xlsx|doc|docx|gif|jpg|JPG|jpeg|JPEG|png|ods|txt|heif|heifs|heic|heics|bmp|wmpb';
                        $config['max_size']      = 25000;
                        $config['file_name'] 	 = strtolower($file_uuid);
                        $config['overwrite']     = true;
                        $config['remove_spaces'] = true;
                        $this->upload->initialize($config);

                        if ($this->upload->do_upload('doc_file')) {
                            $ext = pathinfo(base_url($upload_path.$file_uuid), PATHINFO_EXTENSION);

                            $update_document_data = [
                                'document_id'		=>$temp_document_id,
                                //'doc_reference'	=>$file_uuid,
                                'document_name'		=>$file_name,
                                'document_link'		=>base_url($upload_path.$file_uuid),
                                'document_location'	=>$file_location,
                                'document_extension'=> $ext,
                                'created_by'=>$this->ion_auth->_current_user->id
                            ];

                            ## Resize File if it's an Image and maintain Orientation
                            $uploaded_image = $this->upload->data();
                            /* if( in_array( $uploaded_image['image_type'], $this->image_file_types ) ){
                                $resized_img	= $this->resize_image( $uploaded_image['file_name'], $uploaded_image['file_path'] );
                            } */

                            # Update temp file
                            $upload_complete = $this->_update_document($account_id, $temp_document_id, $update_document_data, $target_table);

                            if ($upload_complete) {
                                $uploaded_data['documents'][$i] = array_merge($document_data, $update_document_data);
                                $this->session->set_flashdata('message', 'Success: Document has been uploaded.');
                            }
                        } else {
                            ## delete the temp file
                            $this->db->where('account_id', $account_id)
                                ->where('document_id', $temp_document_id)
                                ->delete($target_table);

                            $uploaded_data['errors'][$i] = [
                                'file'	=> $file_name,
                                'error'	=> $this->upload->display_errors()
                            ];
                        }
                    } else {
                        $this->session->set_flashdata('message', 'Error: Unable to created a file in the DB ');
                        return false;
                    }
                }
            }

            if (!empty($uploaded_data)) {
                $errors = !empty($uploaded_data['errors']) ? ', with some Errors!' : '';
                $this->session->set_flashdata('message', 'Documents uploaded successfully'.$errors);
                $result = $uploaded_data;
            }
        } else {
            $this->session->set_flashdata('message', 'No files were selected');
        }
        return $result;
    }

    /** Create a Document placeholder in the respective table **/
    private function _create_document_placeholder($account_id = false, $doc_data = false, $target_table = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($doc_data) && !empty($target_table)) {
            $doc_data = $this->ssid_common->_filter_data($target_table, $doc_data);

            $where = ['account_id'=>$account_id, 'doc_type'=>$doc_data['doc_type'], 'doc_reference'=>$doc_data['doc_reference'] ];
            $query = $this->db->order_by('document_id desc')->limit(1)->get_where($target_table, $where);

            if ($query->num_rows() > 0) {
                $row = $query->result()[0];
                $this->db->where($where);
                $this->db->where('document_id', $row->document_id);
                $this->db->update($target_table, $doc_data);
                $result = ($this->db->trans_status() !== false) ? $row->document_id : false;
            } else {
                $this->db->insert($target_table, $doc_data);
                $result = ($this->db->trans_status() !== false) ? $this->db->insert_id() : false;
            }
        }
        return $result;
    }

    /** Update document record **/
    private function _update_document($account_id = false, $document_id = false, $doc_data = false, $target_table = 'document_uploads')
    {
        $result = false;
        if (!empty($account_id) && !empty($document_id) && !empty($doc_data)) {
            $doc_data = $this->ssid_common->_filter_data($target_table, $doc_data);
            $this->db->where('account_id ', $account_id)
                ->where('document_id', $document_id)
                ->update($target_table, $doc_data);

            $result = ($this->db->trans_status() !== false) ? $document_id : false;
        }
        return $result;
    }

    /** Get a list of all uploaded documents **/
    public function get_document_list($account_id = false, $document_group = 'document_uploads', $postdata = false, $where = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($document_group)) {
            $attached_to_question = (!empty($postdata['attached_to_question'])) ? $postdata['attached_to_question'] : false;

            $where = (!empty($where)) ? convert_to_array($where) : false;

            switch($document_group) {
                case 'site':
                    $target_table 	= 'site_document_uploads';
                    break;
                case 'asset':
                    $target_table 	= 'asset_document_uploads';
                    break;
                case 'fleet':
                    $target_table 	= 'fleet_document_uploads';
                    break;
                case 'risk_assessment':
                    $target_table 	= 'ra_document_uploads';
                    break;
                case 'person':
                case 'people':
                    $target_table 	= 'people_document_uploads';
                    break;
                case 'job':
                case 'task':
                    $target_table 	= 'job_document_uploads';
                    break;
                case 'customer':
                    $target_table 	= 'customer_document_uploads';
                    break;
                case 'premises':
                    $target_table 	= 'premises_document_uploads';
                    break;
                case 'generic':
                    $target_table 	= 'generic_document_uploads';
                    break;
                default:
                    #$target_table = 'document_uploads';
                    break;
            }

            $this->db->select('du.*, CONCAT( user.first_name," ",user.last_name ) `uploaded_by`', false);

            $this->db->join('user', 'user.id = du.created_by', 'left');

            $docs_ungrouped = !empty($postdata['docs_ungrouped']) ? true : false;

            $postdata = $this->ssid_common->_filter_data($target_table, $postdata);

            if (!empty($postdata['site_id'])) {
                $this->db->where('du.site_id', $postdata['site_id']);
            }

            if (!empty($postdata['customer_id'])) {
                $this->db->where('du.customer_id', $postdata['customer_id']);
            }

            if (!empty($postdata['job_id'])) {
                $this->db->where('du.job_id', $postdata['job_id']);
            }

            if (!empty($postdata['audit_id'])) {
                $this->db->where('du.audit_id', $postdata['audit_id']);
            }

            if (!empty($postdata['asset_id'])) {
                $this->db->where('du.asset_id', $postdata['asset_id']);
            }

            if (!empty($postdata['person_id'])) {
                $this->db->where('du.person_id', $postdata['person_id']);
            }

            if (!empty($postdata['customer_id'])) {
                $this->db->where('du.customer_id', $postdata['customer_id']);
            }

            if (!empty($postdata['task_id'])) {
                $this->db->where('du.task_id', $postdata['task_id']);
            }

            if (!empty($postdata['premises_id'])) {
                $this->db->where('du.premises_id', $postdata['premises_id']);
            }

            if (!empty($postdata['vehicle_reg'])) {
                $this->db->where('( REPLACE( " ","", du.vehicle_reg ) = "'.$postdata['vehicle_reg'].'" OR du.vehicle_reg = "'.$postdata['vehicle_reg'].'" )');
            }

            if (!empty($where['doc_type'])) {
                $this->db->where('du.doc_type', $where['doc_type']);
            }

            if (!empty($where['un_grouped']) || !empty($docs_ungrouped)) {
                $un_grouped = true;
            }

            if (!empty($where['approval_status'])) {
                $this->db->where('du.approval_status', $where['approval_status']);
            }

            $this->db->order_by('doc_type, date_created desc');
            $query = $this->db->get($target_table.' du');

            if ($query->num_rows() > 0) {
                $data 	  = [];
                foreach ($query->result() as $doc) {
                    $doc_type = (!empty($doc->doc_type)) ? $doc->doc_type : 'Doc-type-not-set';
                    if (!empty($un_grouped)) {
                        $data[$doc_type][] = $doc;
                    } else {
                        if ($attached_to_question) {
                            $question_id = (!empty($doc->question_id)) ? $doc->question_id : '000';
                            $data[$doc->account_id][$question_id][] = $doc;
                        } else {
                            $data[$doc->account_id][$doc_type][] = $doc;
                        }
                    }
                }
                $this->session->set_flashdata('message', 'Documents found.');
                $result = $data;
            } else {
                $this->session->set_flashdata('message', 'No documents found matching you criteria.');
            }
        } else {
            $this->session->set_flashdata('message', 'No documents found.');
        }
        return $result;
    }


    /** Get a list of all uploaded documents **/
    public function delete_document($account_id = false, $document_id = false, $document_group = 'document_uploads')
    {
        $result = false;

        if (!empty($account_id) && !empty($document_id)  && !empty($document_group)) {
            switch($document_group) {
                case 'site':
                case 'building':
                    $target_table 	= 'site_document_uploads';
                    break;
                case 'asset':
                    $target_table 	= 'asset_document_uploads';
                    break;
                case 'fleet':
                    $target_table 	= 'fleet_document_uploads';
                    break;
                case 'risk_assessment':
                    $target_table 	= 'ra_document_uploads';
                    break;
                case 'person':
                case 'people':
                    $target_table 	= 'people_document_uploads';
                    break;
                case 'job':
                case 'task':
                    $target_table 	= 'job_document_uploads';
                    break;
                case 'customer':
                    $target_table 	= 'customer_document_uploads';
                    break;
                case 'audit':
                case 'evidoc':
                    $target_table 	= 'customer_document_uploads';
                    break;
                case 'premises':
                    $target_table 	= 'premises_document_uploads';
                    break;
                case 'generic':
                    $target_table 	= 'generic_document_uploads';
                    break;
                default:
                    $target_table = 'document_uploads';
                    break;
            }

            if (is_array($document_id)) {
                $this->db->where_in('document_id', $document_id);
            } else {
                $this->db->where('document_id', $document_id);
            }

            $query = $this->db->where('account_id', $account_id)
                ->get($target_table.' du');

            if ($query->num_rows() > 0) {
                $data = [];

                foreach ($query->result() as $row) {
                    if (!empty($row->document_location) && file_exists($this->app_root.$row->document_location)) {
                        unlink($this->app_root.$row->document_location);
                    }

                    $this->db->where('account_id', $account_id)
                        ->where('document_id', $row->document_id)
                        ->delete($target_table);

                    if ($this->db->trans_status() !== false) {
                        $data[] = $row->document_id;
                    }
                }

                if (!empty($data)) {
                    $this->session->set_flashdata('message', 'Documents deleted successfully.');
                    $result = true;
                }
            } else {
                $this->session->set_flashdata('message', 'No documents found matching you criteria.');
            }
        } else {
            $this->session->set_flashdata('message', 'No documents found.');
        }
        return $result;
    }


    /** Process Asset Related Images **/
    public function upload_profile_documents($account_id = false, $document_data = false, $document_group = false, $folder = false)
    {
        $result 	= false;

        if (!empty($account_id) && !empty($_FILES['user_files']['name']) && !empty($document_data)) {
            foreach ($_FILES['user_files'] as $files_data) {
                foreach ($files_data as $reference_id => $loc_files) {
                    foreach ($loc_files as $k => $file_name) {
                        $doc_reference = 'ssid'.$account_id.'_'.$reference_id;

                        ## Prepare temp table
                        switch($document_group) {
                            case 'site':
                                $folder			= 'sites';
                                $target_table 	= 'site_document_uploads';
                                $doc_reference .= (!empty($document_data['site_id'])) ? 'vreg'.$document_data['site_id'] : '';
                                $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                                break;
                            case 'asset':
                                $folder			= 'assets';
                                $asset_id		= (!empty($document_data['asset_id'])) ? $document_data['asset_id'] : '';
                                $target_table 	= 'asset_document_uploads';
                                $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                                $doc_reference .= (!empty($document_data['asset_id'])) ? 'ast'.$document_data['asset_id'] : '';
                                $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                                break;
                            case 'fleet':
                                $folder			= 'fleet';
                                $target_table 	= 'fleet_document_uploads';
                                $doc_reference .= (!empty($document_data['vehicle_reg'])) ? 'vreg'.preg_replace('/\s+/', '', $document_data['vehicle_reg']) : '';
                                $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                                break;
                            case 'person':
                            case 'people':
                                $folder			= 'people';
                                $target_table 	= 'people_document_uploads';
                                $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                                $doc_reference .= (!empty($document_data['asset_id'])) ? 'ast'.$document_data['asset_id'] : '';
                                $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                                $doc_reference .= (!empty($document_data['person_id'])) ? 'per'.$document_data['person_id'] : '';
                                break;
                            case 'risk_assessment':
                                $folder			= 'risk-assessments';
                                $target_table 	= 'ra_document_uploads';
                                $doc_reference .= (!empty($document_data['job_id'])) ? 'j'.$document_data['job_id'] : '';
                                $doc_reference .= (!empty($document_data['site_id'])) ? 's'.$document_data['site_id'] : '';
                                $doc_reference .= (!empty($document_data['customer_id'])) ? 'c'.$document_data['customer_id'] : '';
                                $doc_reference .= (!empty($document_data['assessment_id'])) ? 'ra'.$document_data['assessment_id'] : '';
                                break;
                            case 'job':
                                $folder			= 'job';
                                $target_table 	= 'job_document_uploads';
                                $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                                $doc_reference .= (!empty($document_data['asset_id'])) ? 'ast'.$document_data['asset_id'] : '';
                                $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                                $doc_reference .= (!empty($document_data['job_id'])) ? 'job'.$document_data['job_id'] : '';
                                break;

                            case 'customer':
                                $folder			= 'customer';
                                $target_table 	= 'customer_document_uploads';
                                $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                                $doc_reference .= (!empty($document_data['asset_id'])) ? 'ast'.$document_data['asset_id'] : '';
                                $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                                $doc_reference .= (!empty($document_data['customer_id'])) ? 'customer'.$document_data['customer_id'] : '';
                                break;
                            case 'premises':
                                $folder			= 'premises';
                                $premises_id	= (!empty($document_data['premises_id'])) ? $document_data['premises_id'] : '';
                                $target_table 	= 'premises_document_uploads';
                                $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                                $doc_reference .= (!empty($document_data['premises_id'])) ? 'ast'.$document_data['premises_id'] : '';
                                $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                                break;
                            case 'generic':
                                $folder			= 'generic';
                                $target_table 	= 'generic_document_uploads';
                                $doc_reference .= 'gen_assets';
                                $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                                $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                                break;

                            default:
                                $target_table 	= 'document_uploads';
                                $folder			= 'others';
                                break;
                        }

                        ## Proces individual files
                        $document_data['doc_type'] 		= (!empty($document_data['doc_type'])) ? ucwords(strtolower($document_data['doc_type'])) : 'Other';
                        $document_data['document_name'] = $file_name;
                        $file_uuid				 		= $doc_reference.'_'.preg_replace('/\s+/', '', $file_name);
                        $document_data['doc_reference'] = $file_uuid;
                        $sub_folder 					= (!empty($document_data['doc_type'])) ? $document_data['doc_type'] : false;

                        $temp_document_id  = $this->_create_document_placeholder($account_id, $document_data, $target_table);

                        if (!empty($temp_document_id)) {
                            $_FILES['doc_file']['name'] 	= preg_replace('/\s+/', '', $file_name);
                            $_FILES['doc_file']['type'] 	= $_FILES['user_files']['type'][$reference_id][$k];
                            $_FILES['doc_file']['tmp_name'] = preg_replace('/\s+/', '', $_FILES['user_files']['tmp_name'][$reference_id][$k]);
                            $_FILES['doc_file']['error'] 	= $_FILES['user_files']['error'][$reference_id][$k];
                            $_FILES['doc_file']['size'] 	= $_FILES['user_files']['size'][$reference_id][$k];

                            $document_path 			 		= '_account_assets/accounts/'.$account_id.'/'.$folder.'/';
                            ##$upload_path 			 		= $this->app_root.$document_path; //uncomment if you want to use the absolute path
                            $upload_path 			 		= $document_path; //Subfolder, resolve path at time of rendering
                            $upload_path 					.= (!empty($asset_id)) ? $asset_id.'/' : '';
                            $upload_path 					.= (strtolower($document_data['doc_type']) == 'profile images') ? 'profile_images/' : (!empty($sub_folder) ? $sub_folder : '');

                            if (!is_dir($upload_path)) {
                                if (!mkdir($upload_path, 0755, true)) {
                                    $this->db->where('account_id', $account_id)
                                        ->where('document_id', $temp_document_id)
                                        ->delete($target_table);
                                    $this->session->set_flashdata('message', 'Error: Unable to create upload location');
                                    return false;
                                }
                            }

                            //$file_name				 = $_FILES['doc_file']['name'];
                            $file_type				 = $_FILES['doc_file']['type'];
                            $file_location			 = $upload_path.$file_uuid;

                            $config['upload_path'] 	 = $upload_path;
                            $config['allowed_types'] = 'pdf|csv|xls|xlsx|doc|docx|gif|jpg|JPG|jpeg|JPEG|png|ods|txt|heif|heifs|heic|heics|bmp|wmpb';
                            $config['max_size']      = 25000; //Approx 16MB
                            $config['file_name'] 	 = strtolower($file_uuid);
                            $config['overwrite']     = true;
                            $config['remove_spaces'] = true;

                            $this->upload->initialize($config);

                            if ($this->upload->do_upload('doc_file')) {
                                $ext = pathinfo(base_url($upload_path.$file_uuid), PATHINFO_EXTENSION);
                                $update_document_data = [
                                    'document_id'		=> $temp_document_id,
                                    'document_name'		=> $file_name,
                                    'document_link'		=> base_url($upload_path.$file_uuid),
                                    'document_location'	=> $file_location,
                                    'document_extension'=> $ext,
                                    'created_by'		=> $this->ion_auth->_current_user->id
                                ];

                                # Update temp file
                                $upload_complete = $this->_update_document($account_id, $temp_document_id, $update_document_data, $target_table);
                                if ($upload_complete) {
                                    $uploaded_data['documents'][$k] = array_merge($document_data, $update_document_data);
                                }
                            } else {
                                ## delete the temp file
                                $this->db->where('account_id', $account_id)
                                    ->where('document_id', $temp_document_id)
                                    ->delete($target_table);

                                $uploaded_data['errors'][$k] = [
                                    'file'=>$file_name,
                                    'error'=>$this->upload->display_errors()
                                ];
                            }
                        } else {
                            $this->session->set_flashdata('message', 'Error: Unable to created a file in the DB ');
                            return false;
                        }
                    }
                }
            }
            if (!empty($uploaded_data)) {
                $errors = !empty($uploaded_data['errors']) ? ', with some Errors!' : '';
                $this->session->set_flashdata('message', 'Documents uploaded successfully'.$errors);
                $result = $uploaded_data;
            }
        } else {
            $this->session->set_flashdata('message', 'No files were selected');
        }
        return $result;
    }


    /** Upload Multiple Jobs using Templatre **/
    public function upload_jobs($account_id = false)
    {
        $result = null;
        if (!empty($account_id)) {
            $uploaddir  = $this->app_root. 'assets' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;

            if (!file_exists($uploaddir)) {
                mkdir($uploaddir);
            }

            for ($i=0; $i < count($_FILES['upload_file']['name']); $i++) {
                //Get the temp file path
                $tmpFilePath = $_FILES['upload_file']['tmp_name'][$i];
                if ($tmpFilePath != '') {
                    $uploadfile = $uploaddir . basename($_FILES['upload_file']['name'][$i]);
                    if (move_uploaded_file($tmpFilePath, $uploadfile)) {
                        //If FILE is CSV process differently
                        $ext = pathinfo($uploadfile, PATHINFO_EXTENSION);
                        if ($ext == 'csv') {
                            $processed = csv_file_to_array($uploadfile);
                            if (!empty($processed)) {
                                $data = $this->_save_job_temp_data($account_id, $processed);
                                if ($data) {
                                    unlink($uploadfile);
                                    $result = true;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    /** Process uploaded array **/
    private function _save_job_temp_data($account_id = false, $raw_data = false)
    {
        $result = null;
        if (!empty($account_id) && !empty($raw_data)) {
            $exists = $new = [];
            foreach ($raw_data as $k => $record) {
                $record = array_change_key_case($record, CASE_LOWER);
                if (!empty($record['client_reference'])) {
                    $check_exists = $this->db->where(['account_id'=>$account_id, 'client_reference'=>$record['client_reference'] ])
                        ->limit(1)
                        ->get('job_upload_temp_data')
                        ->row();

                    if (!empty($check_exists)) {
                        $exists[] 	= $this->ssid_common->_filter_data('job_upload_temp_data', array_map('trim', $record));
                    } else {
                        $new[]  	= $this->ssid_common->_filter_data('job_upload_temp_data', array_map('trim', $record));
                    }
                }
            }

            //Updated existing
            if (!empty($exists)) {
                $this->db->update_batch('job_upload_temp_data', $exists, 'client_reference');
            }
            //Insert new records
            if (!empty($new)) {
                $this->db->insert_batch('job_upload_temp_data', $new);
            }

            $result = ($this->db->trans_status() !== false) ? true : false;
        }
        return $result;
    }

    /** Get records penging from upload **/
    public function get_pending_upload_jobs($account_id = false)
    {
        $result = null;
        if (!empty($account_id)) {
            $query = $this->db->where('account_id', $account_id)
                ->where_in('upload_status', [ 'Pending' ])
                ->order_by('client_reference')
                ->get('job_upload_temp_data');

            if ($query->num_rows() > 0) {
                $data = [];
                foreach ($query->result() as $k => $row) {
                    if (!empty($row->client_reference)) {
                        $job_types = false;
                        $contract  = $this->_verify_contract($account_id, $row->contract_name);
                        if (!empty($contract)) {
                            $job_types_row = $this->db->get_where('job_types', [ 'account_id'=>$account_id, 'contract_id'=>$contract->contract_id ]);
                            if ($job_types_row->num_rows() > 0) {
                                $job_types = json_encode($job_types_row->result());
                            }
                        }
                        $row->job_types				= $job_types;

                        $address_record 			= $this->_get_job_postcode_addresses($account_id, $row->address_postcode, $row->address_line1, $row->address_line2);
                        $row->postcode_addresses	= !empty($address_record['postcode_addresses']) ? json_encode($address_record['postcode_addresses']) : false;
                        $row->suggested_address		= !empty($address_record['suggested_address']) ? $address_record['suggested_address'] : false;
                        $row->region_id				= $this->_get_postcode_region($account_id, $row->address_postcode);

                        $check = $this->db->select('job.job_id, job.client_reference')
                            ->where('job.account_id', $account_id)
                            ->where('job.client_reference', $row->client_reference)
                            ->limit(1)
                            ->get('job')
                            ->row();

                        if (!empty($check->client_reference)) {
                            $data['existing-records'][] = ( array ) $row;
                        } else {
                            $data['new-records'][] = ( array ) $row;
                        }
                    } else {
                        $data['missing-client_reference'][] = ( array ) $row;
                    }
                }
                $result = $data;
            }
        }
        return $result;
    }

    /*
    * Update Job record
    */
    public function update_temp_data($account_id = false, $temp_job_id = false, $temp_jobs_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($temp_job_id) && !empty($temp_jobs_data)) {
            $data  = [];
            $where = [
                'account_id'	=> $account_id,
                'temp_job_id'	=> $temp_job_id
            ];

            foreach ($temp_jobs_data as $key => $value) {
                $data[$key] = trim($value);
            }

            $update_data = array_merge($data, $where);
            $this->db->where($where)
                ->update('job_upload_temp_data', $update_data);

            $result = ($this->db->trans_status() !== 'false') ? true : false;
        }
        return $result;
    }

    /** Get Postcode Address and Suggested Address **/
    private function _get_job_postcode_addresses($account_id = false, $postcode = false, $address_line_1 = false, $address_line_2 = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($postcode)) {
            $data 				= [];
            $uploaded_address 	= '';
            $uploaded_address 	.= !empty($address_line_1) ? $address_line_1 : '';
            $uploaded_address 	.= !empty($address_line_2) ? $address_line_2 : '';
            $uploaded_address 	.= $postcode;
            $uploaded_address 	= !empty($uploaded_address) ? strtolower(strip_all_whitespace($uploaded_address)) : false;
            $query 	= $this->db->where('postcode', $postcode)
                ->or_where('postcode_nospaces', strip_all_whitespace($postcode))
                ->group_by('main_address_id')
                ->get('addresses');

            if ($query->num_rows() > 0) {
                $suggested_address			= false;
                $data['postcode_addresses'] = $query->result();
                foreach ($query->result() as $k => $row) {
                    $actual_address		= '';
                    $actual_address 	.= !empty($row->addressline1) ? $row->addressline1 : '';
                    $actual_address 	.= !empty($row->addressline2) ? $row->addressline2 : '';
                    $actual_address 	.= !empty($row->postcode) ? $row->postcode : '';
                    $actual_address 	= !empty($actual_address) ? strtolower(strip_all_whitespace($actual_address)) : false;

                    if ($actual_address == $uploaded_address) {
                        $suggested_address = $row;
                    }
                }
                $data['suggested_address'] 	= $suggested_address;
            } else {
                $this->load->model('serviceapp/Address_model', 'address_service');
                $api_address = $this->address_service->get_addresses($postcode);
                if (!empty($api_address)) {
                    $data = $this->_get_job_postcode_addresses($account_id, $postcode, $address_line_1, $address_line_2);
                } else {
                    ## Create New Address Unverified Address
                    $new_address_data = [
                        'addressline1'	=> $address_line_1,
                        'addressline2'	=> $address_line_2,
                        'postcode'		=> $postcode
                    ];
                    $new_address = $this->address_service->add_unverified_address($account_id, $new_address_data);
                    if (!empty($new_address)) {
                        $data = $this->_get_job_postcode_addresses($account_id, $postcode, $address_line_1, $address_line_2);
                    }
                }
            }

            $result = !empty($data) ? $data : false;
        }

        return $result;
    }

    /** Get Postcode Region **/
    private function _get_postcode_region($account_id = false, $postcode = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($postcode)) {
            $address_record = $this->db->select('postcode, postcode_district, postcode_area', false)
                ->where('postcode', $postcode)
                ->or_where('postcode_nospaces', strip_all_whitespace($postcode))
                ->limit(1)
                ->get('addresses')
                ->row();

            if (!empty($address_record)) {
                $region = $this->db->select('region_id, postcode_district')
                    ->where('diary_region_postcodes.account_id', $account_id)
                    ->where('diary_region_postcodes.postcode_district', $address_record->postcode_district)
                    ->group_by('diary_region_postcodes.region_id')
                    ->limit(1)
                    ->get('diary_region_postcodes')
                    ->row();
                if (!empty($region)) {
                    $result = $region->region_id;
                }
            }
        }
        return $result;
    }


    /** Process Job Uploads **/
    public function process_job_uploads($account_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($postdata['jobs_data'])) {
            $this->load->model('serviceapp/Job_model', 'job_service');

            $processed_data 	= [];
            $contract_data 		= [];
            $customer_data 		= [];
            $jobs_data 			= [];
            $successful_records = [];

            ## Resolve address type
            $query 		= $this->db->select('address_type_id, address_type_group', false)->get_where('address_types', [ 'account_id' => $account_id ]);
            if ($query->num_rows() > 0) {
                $address_types = [];
                foreach ($query->result() as $id => $col) {
                    $address_types[$col->address_type_group] = $col->address_type_id;
                }
                $address_types = !empty($address_types) ? ( object ) $address_types : false;
            }

            $address_type_id		= (!empty($address_types->main) ? $address_types->main : (!empty($address_types->residential) ? $address_types->residential : (!empty($address_types->business) ? $address_types->business : null)));

            foreach ($postdata['jobs_data'] as $key => $temp_record) {
                if (!empty($temp_record['checked']) && ($temp_record['checked'] == 1) && !empty($temp_record['temp_job_id'])) {
                    $raw_data	= $this->db->get_where('job_upload_temp_data', ['temp_job_id'=>$temp_record['temp_job_id']])->row();

                    if (!empty($raw_data)) {
                        $temp_record = array_merge((array)$raw_data, $temp_record);

                        $address_record 					= $this->_get_job_postcode_addresses($account_id, $temp_record['address_postcode'], $temp_record['address_line1'], $temp_record['address_line2']);
                        $temp_record['postcode_addresses']	= !empty($address_record['postcode_addresses']) ? json_encode($address_record['postcode_addresses']) : false;
                        $temp_record['suggested_address']	= !empty($address_record['suggested_address']) ? $address_record['suggested_address'] : false;
                        $temp_record['region_id']			= $this->_get_postcode_region($account_id, $temp_record['address_postcode']);

                        ## Check Contract
                        $contract = $this->_verify_contract($account_id, $temp_record['contract_name']);
                        if (!empty($contract)) {
                            $job_types = $this->db->get_where('job_types', [ 'account_id'=>$account_id, 'contract_id'=>$contract->contract_id ]);
                        }
                        $temp_record['job_types']	= ($job_types->num_rows() > 0) ? json_encode($job_types->result()) : false;

                        if (!empty($contract)) {
                            $temp_record['contract_id'] 	= $contract->contract_id;
                            $temp_record['main_address_id'] = (!empty($temp_record['address_id']) ? $temp_record['address_id'] : (!empty($temp_record['main_address_id']) ? $temp_record['main_address_id'] : null));

                            if (!empty($temp_record['main_address_id'])) {
                                $temp_record['is_uploaded'] 	= 1;
                                $temp_record['uploaded_record'] = 1;
                                $temp_record['address_type_id']	= $address_type_id;

                                ## Check Customer Details
                                $customer = (array) $this->_verify_customer($account_id, $temp_record);
                                if (!empty($customer)) {
                                    $temp_record['customer_id'] 	= $customer['customer_id'];

                                    ## Verify Job Type
                                    $job_type = $this->job_service->_validate_job_type($account_id, $temp_record['job_type_id']);
                                    if (!empty($job_type)) {
                                        $temp_record['job_type_id'] = $job_type->job_type_id;
                                        $new_job = $this->job_service->create_job($account_id, $temp_record);
                                        if (!empty($new_job)) {
                                            $processed_data['jobs_created_successfully'][] 	= $temp_record;
                                            $successful_records[] = [
                                                'temp_job_id'	=>$temp_record['temp_job_id'],
                                                'upload_status'	=>'Successful'
                                            ];
                                        } else {
                                            $processed_data['job_creation_failed'][] 	= $temp_record;
                                        }
                                    } else {
                                        $processed_data['invalid_job_types'][] 	= $temp_record;
                                    }
                                } else {
                                    $processed_data['invalid_customers'][] 	= $temp_record;
                                }
                            } else {
                                $processed_data['invalid_addresses'][] 	= $temp_record;
                            }
                        } else {
                            $processed_data['invalid_contracts'][] 	= $temp_record;
                        }
                    }
                }
            }

            if (!empty($successful_records)) {
                $delete = array_column($successful_records, 'temp_job_id');
                $this->db->where_in('temp_job_id', $delete)
                    ->delete('job_upload_temp_data');

                $this->ssid_common->_reset_auto_increment('job_upload_temp_data', 'temp_job_id');

                #$this->db->update_batch( 'job_upload_temp_data', $successful_records, 'temp_job_id' );
            }

            $result = $processed_data;
        }
        return $result;
    }

    /** Verify Contract **/
    public function _verify_contract($account_id = false, $contract_name = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($contract_name)) {
            $contract = $this->db->select('contract_id, contract_name')
                ->where('contract_name', $contract_name)
                ->get_where('contract', [ 'account_id'=>$account_id ])
                ->row();

            if (!empty($contract)) {
                $result = $contract;
            }
        }
        return $result;
    }

    /** Verify Customer **/
    public function _verify_customer($account_id = false, $customer_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($customer_data)) {
            /*$customer = $this->db->select( 'customer_id, customer_first_name, customer_last_name' )
                #->where( 'customer_first_name', $customer_data['customer_first_name'] )
                ->where( 'customer_last_name', $customer_data['customer_last_name'] )
                ->get_where( 'customer', [ 'account_id'=>$account_id ] )
                ->row();*/

            $customer = false;

            if (!empty($customer)) {
                $result = $customer;
            } else {
                $customer 					= $this->ssid_common->_filter_data('customer', $customer_data);
                $this->db->insert('customer', $customer);
                $customer['customer_id'] 	= $this->db->insert_id();
                if (!empty($customer['customer_id'])) {
                    $customer_data['address_contact_last_name'] = $customer_data['customer_last_name'];
                    $customer_data['address_contact_number'] 	= $customer_data['customer_main_telephone'];
                    $contact = $this->customer_service->create_contact($account_id, $customer['customer_id'], $customer_data);
                }
                $result = $customer;
            }
        }
        return $result;
    }

    /** Drop Temp Records **/
    public function drop_temp_records($account_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($postdata['jobs_data'])) {
            $to_delete = [];
            foreach ($postdata['jobs_data'] as $key => $temp_record) {
                if (!empty($temp_record['checked']) && ($temp_record['checked'] == 1) && !empty($temp_record['temp_job_id'])) {
                    $to_delete[] = $temp_record['temp_job_id'];
                }
            }
            if (!empty($to_delete)) {
                $this->db->where_in('temp_job_id', $to_delete)
                    ->delete('job_upload_temp_data');

                $result = ($this->db->trans_status() !== false) ? true : false;
                $this->ssid_common->_reset_auto_increment('job_upload_temp_data', 'temp_job_id');
            }
        }
        return $result;
    }


    /** Process files **/
    public function process_signatures($account_id = false, $postdata = false, $document_group = false, $folder = false)
    {
        $result 	= false;
        if (!empty($account_id) && !empty($_FILES['signatures']['name'])) {
            $document_data = [];
            $doc_reference = 'ssid'.$account_id.'_';

            foreach ($postdata as $col=>$val) {
                $val				 = (!is_array($val)) ? trim($val) : $val;
                $document_data[$col] = (in_array($col, $this->numerical_fields)) ? (int)$val : $val;
            }

            if (!empty($document_data)) {
                switch($document_group) {
                    case 'site':
                        $folder			= 'sites';
                        $target_table 	= 'site_document_uploads';
                        $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                        break;
                    case 'asset':
                        $folder			= 'assets';
                        $asset_id		= (!empty($document_data['asset_id'])) ? $document_data['asset_id'] : '';
                        $target_table 	= 'asset_document_uploads';
                        $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                        $doc_reference .= (!empty($document_data['asset_id'])) ? 'ast'.$document_data['asset_id'] : '';
                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                        break;
                    case 'vehicle':
                    case 'fleet':
                        $folder			= 'fleet';
                        $target_table 	= 'fleet_document_uploads';
                        $doc_reference .= (!empty($document_data['vehicle_reg'])) ? 'vreg'.$document_data['vehicle_reg'] : '';
                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                        break;
                    case 'risk_assessment':
                        $folder			= 'risk-assessments';
                        $target_table 	= 'ra_document_uploads';
                        $doc_reference .= (!empty($document_data['job_id'])) ? 'j'.$document_data['job_id'] : '';
                        $doc_reference .= (!empty($document_data['site_id'])) ? 's'.$document_data['site_id'] : '';
                        $doc_reference .= (!empty($document_data['customer_id'])) ? 'c'.$document_data['customer_id'] : '';
                        $doc_reference .= (!empty($document_data['assessment_id'])) ? 'ra'.$document_data['assessment_id'] : '';
                        break;
                    case 'person':
                    case 'people':
                        $folder			= 'people';
                        $target_table 	= 'people_document_uploads';
                        $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                        $doc_reference .= (!empty($document_data['asset_id'])) ? 'ast'.$document_data['asset_id'] : '';
                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                        $doc_reference .= (!empty($document_data['person_id'])) ? 'per'.$document_data['person_id'] : '';
                        break;

                    case 'job':
                        $folder			= 'job';
                        $job_id			= (!empty($document_data['job_id'])) ? $document_data['job_id'] : false;
                        $target_table 	= 'job_document_uploads';
                        $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                        $doc_reference .= (!empty($document_data['asset_id'])) ? 'ast'.$document_data['asset_id'] : '';
                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                        $doc_reference .= (!empty($document_data['job_id'])) ? 'job'.$document_data['job_id'] : '';

                        break;

                    case 'customer':
                        $folder			= 'customer';
                        $target_table 	= 'customer_document_uploads';
                        $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                        $doc_reference .= (!empty($document_data['asset_id'])) ? 'ast'.$document_data['asset_id'] : '';
                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                        $doc_reference .= (!empty($document_data['customer_id'])) ? 'customer'.$document_data['customer_id'] : '';
                        break;
                    case 'premises':
                        $folder			= 'premises';
                        $premises_id		= (!empty($document_data['premises_id'])) ? $document_data['premises_id'] : '';
                        $target_table 	= 'premises_document_uploads';
                        $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                        $doc_reference .= (!empty($document_data['premises_id'])) ? 'ast'.$document_data['premises_id'] : '';
                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                        break;

                    default:
                        $target_table 	= 'document_uploads';
                        $folder			= (!empty($folder)) ? $folder : 'others';
                        break;
                }

                $filesCount = count($_FILES['signatures']['name']);

                for ($i = 1; $i <= $filesCount; $i++) {
                    $signature_type = array_keys($_FILES['signatures']['name'][$i])[0];
                    $document_data['doc_type'] 		= (!empty($signature_type)) ? ucwords(strtolower(str_replace('_', ' ', $signature_type))) : 'Signature';
                    $document_data['document_name'] = (!empty($_FILES['signatures']['name'][$i][$signature_type])) ? $_FILES['signatures']['name'][$i][$signature_type] : null;
                    $file_uuid				 		= $doc_reference.'_'.preg_replace('/\s+/', '', $_FILES['signatures']['name'][$i][$signature_type]);

                    $parse_file_name				= $this->_parse_file_name($file_uuid);
                    $file_uuid 						= ($parse_file_name) ? $parse_file_name : $file_uuid;

                    $document_data['doc_reference'] = $file_uuid;
                    $sub_folder 					= (!empty($document_data['doc_type'])) ? $document_data['doc_type'] : false;

                    $temp_document_id  = $this->_create_document_placeholder($account_id, $document_data, $target_table);

                    if (!empty($temp_document_id)) {
                        $_FILES['doc_file']['name'] 	= $_FILES['signatures']['name'][$i][$signature_type];
                        $_FILES['doc_file']['type'] 	= $_FILES['signatures']['type'][$i][$signature_type];
                        $_FILES['doc_file']['tmp_name'] = $_FILES['signatures']['tmp_name'][$i][$signature_type];
                        $_FILES['doc_file']['error'] 	= $_FILES['signatures']['error'][$i][$signature_type];
                        $_FILES['doc_file']['size'] 	= $_FILES['signatures']['size'][$i][$signature_type];

                        $document_path 			 		= '_account_assets/accounts/'.$account_id.'/'.$folder.'/';
                        ##$upload_path 			 		= $this->app_root.$document_path; //uncomment if you want to use the absolute path
                        $upload_path 			 		= $document_path; //Subfolder, resolve path at time of rendering
                        $upload_path 					.= (!empty($job_id)) ? $job_id.'/' : '';
                        $upload_path 					.= (strtolower($document_data['doc_type']) == 'profile images') ? 'profile_images/' : (!empty($sub_folder) ? $sub_folder : '');

                        if (!is_dir($upload_path)) {
                            if (!mkdir($upload_path, 0755, true)) {
                                $this->db->where('account_id', $account_id)
                                    ->where('document_id', $temp_document_id)
                                    ->delete($target_table);
                                $this->session->set_flashdata('message', 'Error: Unable to create upload location');
                                return false;
                            }
                        }

                        $file_name				 = $_FILES['doc_file']['name'];
                        $file_type				 = $_FILES['doc_file']['type'];
                        $file_location			 = $upload_path.$file_uuid;

                        $config['upload_path'] 	 = $upload_path;
                        $config['allowed_types'] = 'pdf|csv|xls|xlsx|doc|docx|gif|jpg|JPG|jpeg|JPEG|png|ods|txt|heif|heifs|heic|heics|bmp|wmpb';
                        $config['max_size']      = 25000; //Approx 16MB
                        $config['file_name'] 	 = strtolower($file_uuid);
                        $config['overwrite']     = true;
                        $config['remove_spaces'] = true;

                        $this->upload->initialize($config);

                        if ($this->upload->do_upload('doc_file')) {
                            $ext = pathinfo(base_url($upload_path.$file_uuid), PATHINFO_EXTENSION);
                            $update_document_data = [
                                'document_id'=>$temp_document_id,
                                //'doc_reference'=>$file_uuid,
                                'document_name'=>$file_name,
                                'document_link'=>base_url($upload_path.$file_uuid),
                                'document_location'=>$file_location,
                                'document_extension'=> $ext,
                                'created_by'=>$this->ion_auth->_current_user->id
                            ];

                            # Update temp file
                            $upload_complete = $this->_update_document($account_id, $temp_document_id, $update_document_data, $target_table);
                            if ($upload_complete) {
                                $uploaded_data['documents'][$i] = array_merge($document_data, $update_document_data);
                                $this->session->set_flashdata('message', 'Success: Document has been uploaded.');
                            }

                            ## Update the Job with the base64 image
                            $url_link = $update_document_data['document_link'];

                            if (!empty($job_id) && !empty($signature_type)) {
                                switch($signature_type) {
                                    case 'engineer_signature':
                                        $engineer_signature_file = $url_link;
                                        #$engineer_signature	 	 = file_get_contents( $url_link );
                                        #$engineer_signature_blob = base64_encode( $engineer_signature );

                                        $this->db->where('job.account_id', $account_id)
                                            ->where('job.job_id', $job_id)
                                            ->update('job', [ 'engineer_signature' => $engineer_signature_file ]);
                                        #->job( 'job', [ 'engineer_signature' => $engineer_signature_blob ] );

                                        break;

                                    case 'customer_signature':

                                        $customer_signature_file = $url_link;
                                        #$customer_signature	 	 = file_get_contents( $url_link );
                                        #$customer_signature_blob = base64_encode( $customer_signature );

                                        $this->db->where('job.account_id', $account_id)
                                            ->where('job.job_id', $job_id)
                                            ->update('job', [ 'customer_signature' => $customer_signature_file ]);
                                        #->job( 'job', [ 'customer_signature' => $customer_signature_blob ] );

                                        break;
                                }
                            }
                        } else {
                            ## delete the temp file
                            $this->db->where('account_id', $account_id)
                                ->where('document_id', $temp_document_id)
                                ->delete($target_table);

                            $uploaded_data['errors'][$i] = [
                                'file'=>$file_name,
                                'error'=>$this->upload->display_errors()
                            ];
                        }
                    } else {
                        $this->session->set_flashdata('message', 'Error: Unable to created a file in the DB ');
                        return false;
                    }
                }
            }

            if (!empty($uploaded_data)) {
                $errors = !empty($uploaded_data['errors']) ? ', with some Errors!' : '';
                $this->session->set_flashdata('message', 'Documents uploaded successfully'.$errors);
                $result = $uploaded_data;
            }
        } else {
            $this->session->set_flashdata('message', 'No files were selected');
        }
        return $result;
    }


    /** Process Attachments **/
    public function process_attachments($account_id = false, $postdata = false, $document_group = false, $folder = false)
    {
        $result 	= false;
        if (!empty($account_id) && !empty($_FILES['attachments']['name'])) {
            $document_data = [];
            $doc_reference = 'EVI'.$account_id.'_';

            foreach ($postdata as $col=>$val) {
                $val				 = (!is_array($val)) ? trim($val) : $val;
                $document_data[$col] = (in_array($col, $this->numerical_fields)) ? (int)$val : $val;
            }

            if (!empty($document_data)) {
                switch($document_group) {
                    case 'site':
                        $folder			= 'sites';
                        $target_table 	= 'site_document_uploads';
                        $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                        break;
                    case 'asset':
                        $folder			= 'assets';
                        $asset_id		= (!empty($document_data['asset_id'])) ? $document_data['asset_id'] : '';
                        $target_table 	= 'asset_document_uploads';
                        $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                        $doc_reference .= (!empty($document_data['asset_id'])) ? 'ast'.$document_data['asset_id'] : '';
                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                        break;
                    case 'vehicle':
                    case 'fleet':
                        $folder			= 'fleet';
                        $target_table 	= 'fleet_document_uploads';
                        $doc_reference .= (!empty($document_data['vehicle_reg'])) ? 'vreg'.$document_data['vehicle_reg'] : '';
                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                        break;
                    case 'risk_assessment':
                        $folder			= 'risk-assessments';
                        $target_table 	= 'ra_document_uploads';
                        $doc_reference .= (!empty($document_data['job_id'])) ? 'j'.$document_data['job_id'] : '';
                        $doc_reference .= (!empty($document_data['site_id'])) ? 's'.$document_data['site_id'] : '';
                        $doc_reference .= (!empty($document_data['customer_id'])) ? 'c'.$document_data['customer_id'] : '';
                        $doc_reference .= (!empty($document_data['assessment_id'])) ? 'ra'.$document_data['assessment_id'] : '';
                        break;
                    case 'person':
                    case 'people':
                        $folder			= 'people';
                        $target_table 	= 'people_document_uploads';
                        $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                        $doc_reference .= (!empty($document_data['asset_id'])) ? 'ast'.$document_data['asset_id'] : '';
                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                        $doc_reference .= (!empty($document_data['person_id'])) ? 'per'.$document_data['person_id'] : '';
                        break;

                    case 'job':
                        $folder			= 'job';
                        $job_id			= (!empty($document_data['job_id'])) ? $document_data['job_id'] : false;
                        $target_table 	= 'job_document_uploads';
                        $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                        $doc_reference .= (!empty($document_data['asset_id'])) ? 'ast'.$document_data['asset_id'] : '';
                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                        $doc_reference .= (!empty($document_data['job_id'])) ? 'job'.$document_data['job_id'] : '';

                        break;

                    case 'customer':
                        $folder			= 'customer';
                        $target_table 	= 'customer_document_uploads';
                        $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                        $doc_reference .= (!empty($document_data['asset_id'])) ? 'ast'.$document_data['asset_id'] : '';
                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                        $doc_reference .= (!empty($document_data['customer_id'])) ? 'customer'.$document_data['customer_id'] : '';
                        break;
                    case 'premises':
                        $folder			= 'premises';
                        $premises_id		= (!empty($document_data['premises_id'])) ? $document_data['premises_id'] : '';
                        $target_table 	= 'premises_document_uploads';
                        $doc_reference .= (!empty($document_data['site_id'])) ? 'st'.$document_data['site_id'] : '';
                        $doc_reference .= (!empty($document_data['premises_id'])) ? 'ast'.$document_data['premises_id'] : '';
                        $doc_reference .= (!empty($document_data['audit_id'])) ? 'aud'.$document_data['audit_id'] : '';
                        break;

                    default:
                        $target_table 	= 'document_uploads';
                        $folder			= (!empty($folder)) ? $folder : 'others';
                        break;
                }

                $filesCount = count($_FILES['attachments']['name']);

                for ($i = 1; $i <= $filesCount; $i++) {
                    $attachment_type = array_keys($_FILES['attachments']['name'][$i])[0];
                    $document_data['doc_type'] 		= (!empty($attachment_type)) ? ucwords(strtolower(str_replace('_', ' ', $attachment_type))) : 'Attachment';
                    $document_data['document_name'] = (!empty($_FILES['attachments']['name'][$i][$attachment_type])) ? $_FILES['attachments']['name'][$i][$attachment_type] : null;
                    $file_uuid				 		= $doc_reference.'_'.preg_replace('/\s+/', '', $_FILES['attachments']['name'][$i][$attachment_type]);

                    $parse_file_name				= $this->_parse_file_name($file_uuid);
                    $file_uuid 						= ($parse_file_name) ? $parse_file_name : $file_uuid;

                    $document_data['doc_reference'] = $file_uuid;
                    $sub_folder 					= (!empty($document_data['doc_type'])) ? $document_data['doc_type'] : false;

                    $temp_document_id  = $this->_create_document_placeholder($account_id, $document_data, $target_table);

                    if (!empty($temp_document_id)) {
                        $_FILES['doc_file']['name'] 	= $_FILES['attachments']['name'][$i][$attachment_type];
                        $_FILES['doc_file']['type'] 	= $_FILES['attachments']['type'][$i][$attachment_type];
                        $_FILES['doc_file']['tmp_name'] = $_FILES['attachments']['tmp_name'][$i][$attachment_type];
                        $_FILES['doc_file']['error'] 	= $_FILES['attachments']['error'][$i][$attachment_type];
                        $_FILES['doc_file']['size'] 	= $_FILES['attachments']['size'][$i][$attachment_type];

                        $document_path 			 		= '_account_assets/accounts/'.$account_id.'/'.$folder.'/';
                        ##$upload_path 			 		= $this->app_root.$document_path; //uncomment if you want to use the absolute path
                        $upload_path 			 		= $document_path; //Subfolder, resolve path at time of rendering
                        $upload_path 					.= (!empty($job_id)) ? $job_id.'/' : '';
                        $upload_path 					.= (strtolower($document_data['doc_type']) == 'profile images') ? 'profile_images/' : (!empty($sub_folder) ? $sub_folder : '');

                        if (!is_dir($upload_path)) {
                            if (!mkdir($upload_path, 0755, true)) {
                                $this->db->where('account_id', $account_id)
                                    ->where('document_id', $temp_document_id)
                                    ->delete($target_table);
                                $this->session->set_flashdata('message', 'Error: Unable to create upload location');
                                return false;
                            }
                        }

                        $file_name				 = $_FILES['doc_file']['name'];
                        $file_type				 = $_FILES['doc_file']['type'];
                        $file_location			 = $upload_path.$file_uuid;

                        $config['upload_path'] 	 = $upload_path;
                        $config['allowed_types'] = 'pdf|csv|xls|xlsx|doc|docx|gif|jpg|JPG|jpeg|JPEG|png|ods|txt|heif|heifs|heic|heics|bmp|wmpb';
                        $config['max_size']      = 25000; //Approx 16MB
                        $config['file_name'] 	 = strtolower($file_uuid);
                        $config['overwrite']     = true;
                        $config['remove_spaces'] = true;

                        $this->upload->initialize($config);

                        if ($this->upload->do_upload('doc_file')) {
                            $ext = pathinfo(base_url($upload_path.$file_uuid), PATHINFO_EXTENSION);
                            $update_document_data = [
                                'document_id'=>$temp_document_id,
                                //'doc_reference'=>$file_uuid,
                                'document_name'=>$file_name,
                                'document_link'=>base_url($upload_path.$file_uuid),
                                'document_location'=>$file_location,
                                'document_extension'=> $ext,
                                'created_by'=>$this->ion_auth->_current_user->id
                            ];

                            # Update temp file
                            $upload_complete = $this->_update_document($account_id, $temp_document_id, $update_document_data, $target_table);
                            if ($upload_complete) {
                                $uploaded_data['documents'][$i] = array_merge($document_data, $update_document_data);
                                $this->session->set_flashdata('message', 'Success: Document has been uploaded.');
                            }
                        } else {
                            ## delete the temp file
                            $this->db->where('account_id', $account_id)
                                ->where('document_id', $temp_document_id)
                                ->delete($target_table);

                            $uploaded_data['errors'][$i] = [
                                'file'=>$file_name,
                                'error'=>$this->upload->display_errors()
                            ];
                        }
                    } else {
                        $this->session->set_flashdata('message', 'Error: Unable to created a file in the DB ');
                        return false;
                    }
                }
            }

            if (!empty($uploaded_data)) {
                $errors = !empty($uploaded_data['errors']) ? ', with some Errors!' : '';
                $this->session->set_flashdata('message', 'Documents uploaded successfully'.$errors);
                $result = $uploaded_data;
            }
        } else {
            $this->session->set_flashdata('message', 'No files were selected');
        }
        return $result;
    }


    /** Upload Multiple Sites using Template **/
    public function upload_buildings($account_id = false)
    {
        $result = null;
        if (!empty($account_id)) {
            $uploaddir  = $this->app_root. 'assets' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;

            if (!file_exists($uploaddir)) {
                mkdir($uploaddir);
            }

            for ($i=0; $i < count($_FILES['upload_file']['name']); $i++) {
                //Get the temp file path
                $tmpFilePath = $_FILES['upload_file']['tmp_name'][$i];
                if ($tmpFilePath != '') {
                    $uploadfile = $uploaddir . basename($_FILES['upload_file']['name'][$i]);
                    if (move_uploaded_file($tmpFilePath, $uploadfile)) {
                        //If FILE is CSV process differently
                        $ext = pathinfo($uploadfile, PATHINFO_EXTENSION);
                        if ($ext == 'csv') {
                            $processed = csv_file_to_array($uploadfile);
                            if (!empty($processed)) {
                                $data = $this->_save_site_temp_data($account_id, $processed);
                                if ($data) {
                                    unlink($uploadfile);
                                    $result = true;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    /** Process uploaded array **/
    private function _save_site_temp_data($account_id = false, $raw_data = false)
    {
        $result = null;
        if (!empty($account_id) && !empty($raw_data)) {
            $exists = $new = [];
            foreach ($raw_data as $k => $record) {
                $record = array_change_key_case($record, CASE_LOWER);
                if (!empty($record['site_reference'])) {
                    $record['account_id'] = $account_id;
                    $check_exists = $this->db->where(['account_id'=>$account_id, 'site_reference'=>$record['site_reference'] ])
                        ->limit(1)
                        ->get('site_upload_temp_data')
                        ->row();

                    if (!empty($check_exists)) {
                        $exists[] 	= $this->ssid_common->_filter_data('site_upload_temp_data', array_map('trim', $record));
                    } else {
                        $new[]  	= $this->ssid_common->_filter_data('site_upload_temp_data', array_map('trim', $record));
                    }
                }
            }

            //Updated existing
            if (!empty($exists)) {
                $this->db->update_batch('site_upload_temp_data', $exists, 'site_reference');
            }
            //Insert new records
            if (!empty($new)) {
                $this->db->insert_batch('site_upload_temp_data', $new);
            }

            $result = ($this->db->trans_status() !== false) ? true : false;
        }
        return $result;
    }

    /** Get Building records pending from upload **/
    public function get_pending_upload_buildings($account_id = false)
    {
        $result = null;
        if (!empty($account_id)) {
            $query = $this->db->where('account_id', $account_id)
                ->where_in('upload_status', [ 'Pending' ])
                ->order_by('site_reference')
                ->get('site_upload_temp_data');

            if ($query->num_rows() > 0) {
                $data = [];
                foreach ($query->result() as $k => $row) {
                    if (!empty($row->site_reference)) {
                        $address_record 			= $this->_get_job_postcode_addresses($account_id, $row->site_postcodes, $row->site_name);
                        $row->postcode_addresses	= !empty($address_record['postcode_addresses']) ? json_encode($address_record['postcode_addresses']) : false;
                        $row->suggested_address		= !empty($address_record['suggested_address']) ? $address_record['suggested_address'] : false;

                        $check = $this->db->select('site.site_id, site.site_reference')
                            ->where('site.account_id', $account_id)
                            ->where('site.site_reference', $row->site_reference)
                            ->limit(1)
                            ->get('site')
                            ->row();

                        if (!empty($check->site_reference)) {
                            $data['existing-records'][] = ( array ) $row;
                        } else {
                            $data['new-records'][] = ( array ) $row;
                        }
                    } else {
                        $data['missing-site_reference'][] = ( array ) $row;
                    }
                }
                $result = $data;
            }
        }
        return $result;
    }

    /*
    * Update Building record
    */
    public function update_site_temp_data($account_id = false, $temp_site_id = false, $temp_sites_data = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($temp_site_id) && !empty($temp_sites_data)) {
            $data  = [];
            $where = [
                'account_id'	=> $account_id,
                'temp_site_id'	=> $temp_site_id
            ];

            foreach ($temp_sites_data as $key => $value) {
                $data[$key] = trim($value);
            }

            $update_data = array_merge($data, $where);
            $this->db->where($where)
                ->update('site_upload_temp_data', $update_data);

            $result = ($this->db->trans_status() !== 'false') ? true : false;
        }
        return $result;
    }


    /** Process site/building Uploads **/
    public function process_building_uploads($account_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($postdata['sites_data'])) {
            $this->load->model('serviceapp/site_model', 'site_service');

            $processed_data 	= [];
            $successful_records = [];

            foreach ($postdata['sites_data'] as $key => $temp_record) {
                if (!empty($temp_record['checked']) && ($temp_record['checked'] == 1) && !empty($temp_record['temp_site_id'])) {
                    $raw_data	= $this->db->get_where('site_upload_temp_data', ['temp_site_id'=>$temp_record['temp_site_id']])->row();

                    if (!empty($raw_data)) {
                        $temp_record = array_merge((array)$raw_data, $temp_record);

                        $address_record 					= $this->_get_job_postcode_addresses($account_id, $temp_record['site_postcodes'], $temp_record['site_name']);
                        $temp_record['postcode_addresses']	= !empty($address_record['postcode_addresses']) ? json_encode($address_record['postcode_addresses']) : false;
                        $temp_record['suggested_address']	= !empty($address_record['suggested_address']) ? $address_record['suggested_address'] : false;

                        ## Check Contract
                        /*$contract = $this->_verify_contract( $account_id, $temp_record['contract_name'] );
                        if( !empty( $contract ) ){
                            $site_types = $this->db->get_where( 'site_types', [ 'account_id'=>$account_id, 'contract_id'=>$contract->contract_id ] );
                        }*/

                        #$temp_record['contract_id'] 	= $contract->contract_id;
                        $temp_record['site_address_id'] = (!empty($temp_record['site_address_id']) ? $temp_record['site_address_id'] : (!empty($temp_record['address_id']) ? $temp_record['address_id'] : null));

                        if (!empty($temp_record['site_address_id'])) {
                            $temp_record['status_id'] 				= 1;
                            $temp_record['uploaded_record'] 		= 1;
                            $temp_record['audit_result_status_id'] 	= 4;

                            $new_site = $this->site_service->create_site($temp_record);

                            if (!empty($new_site)) {
                                $processed_data['buildings_created_successfully'][] 	= $temp_record;
                                $successful_records[] = [
                                    'temp_site_id'	=>$temp_record['temp_site_id'],
                                    'upload_status'	=>'Successful'
                                ];
                            } else {
                                $processed_data['site_creation_failed'][] 	= $temp_record;
                            }
                        } else {
                            $processed_data['invalid_addresses'][] 	= $temp_record;
                        }
                    }
                }
            }

            if (!empty($successful_records)) {
                $delete = array_column($successful_records, 'temp_site_id');
                $this->db->where_in('temp_site_id', $delete)
                    ->delete('site_upload_temp_data');

                $this->ssid_common->_reset_auto_increment('site_upload_temp_data', 'temp_site_id');

                #$this->db->update_batch( 'site_upload_temp_data', $successful_records, 'temp_site_id' );
            }

            $result = $processed_data;
        }
        return $result;
    }

    /** Drop Temp Records **/
    public function drop_temp_building_records($account_id = false, $postdata = false)
    {
        $result = false;
        if (!empty($account_id) && !empty($postdata['sites_data'])) {
            $to_delete = [];
            foreach ($postdata['sites_data'] as $key => $temp_record) {
                if (!empty($temp_record['checked']) && ($temp_record['checked'] == 1) && !empty($temp_record['temp_site_id'])) {
                    $to_delete[] = $temp_record['temp_site_id'];
                }
            }
            if (!empty($to_delete)) {
                $this->db->where_in('temp_site_id', $to_delete)
                    ->delete('site_upload_temp_data');

                $result = ($this->db->trans_status() !== false) ? true : false;
                $this->ssid_common->_reset_auto_increment('site_upload_temp_data', 'temp_site_id');
            }
        }
        return $result;
    }


    /**
    * Manage uploadImage
    * @return Response
    */
    public function resize_image($file_name = false, $file_path = false)
    {
        if (!$file_name && !$file_path) {
            return false;
        }

        $source_path 	= $file_path . $file_name;
        $target_path 	= $file_path;

        $imagesize 		= getimagesize($source_path);

        if ($imagesize[0] > 800) {
            $config_img_data 	= [
                'image_library' => 'gd2',
                'source_image' 	=> $source_path,
                'new_image' 	=> $target_path,
                'maintain_ratio'=> true,
                'width' 		=> 800,
            ];

            $this->load->library('image_lib', $config_img_data);

            if (!$this->image_lib->resize()) {
                return $this->image_lib->display_errors();
            }

            $this->image_lib->clear();
        }
        return true;
    }


    /**
    * Parse File Name
    */
    private function _parse_file_name($file_str = false)
    {
        if (!empty($file_str)) {
            $file_parts 	= explode('.', $file_str);
            $file_ext		= array_pop($file_parts);
            $file_name		= preg_replace('/[^A-Za-z0-9\-]/', '_', implode('.', $file_parts));
            $file_name  	= trim($file_name).'.'.trim($file_ext);
            return $file_name;
        } else {
            return false;
        }
    }

    /** Optimize Folder Images **/
    public function image_optimisation($account_id = false, $folder_path = false)
    {
        if (!empty($account_id) && !empty($folder_path)) {
            return true;
        //debug($folder_path);
        } else {
        }
    }

    /**
    * Update Document Status
    **/
    public function update_document_status($account_id = false, $document_group = false, $postdata = false)
    {
        $result = false;

        if (!empty($account_id) && !empty($document_group) && !empty($postdata)) {
            switch($document_group) {
                case 'site':
                case 'building':
                    $target_table 	= 'site_document_uploads';
                    break;
                case 'asset':
                    $target_table 	= 'asset_document_uploads';
                    break;
                case 'fleet':
                case 'vehicle':
                    $target_table 	= 'fleet_document_uploads';
                    break;
                case 'person':
                case 'people':
                    $target_table 	= 'people_document_uploads';
                    break;
                case 'job':
                    $target_table 	= 'job_document_uploads';
                    break;
                case 'customer':
                    $target_table 	= 'customer_document_uploads';
                    break;
                case 'premises':
                    $target_table 	= 'premises_document_uploads';
                    break;

                default:
                    $target_table 	= 'document_uploads';
                    break;
            }

            $postdata 		= convert_to_array($postdata);
            $documents_list = !empty($postdata['documents']) ? $postdata['documents'] : false;
            if ($documents_list) {
                $data = [];
                foreach ($documents_list as $doc_id => $document) {
                    $document['account_id'] 			= strval($account_id);
                    $document['approval_status'] 		= in_array(ucwords(strtolower($document['approval_status'])), $this->doc_approval_statuses) ? $document['approval_status'] : 'Pending';
                    $document['approval_action_by'] 	= $this->ion_auth->_current_user->id;
                    $document['approval_action_date'] 	= _datetime();
                    $doc_data = $this->ssid_common->_filter_data($target_table, $document);

                    $this->db->where('document_id', $doc_data['document_id'])
                        ->where('account_id', $account_id)
                        ->update($target_table, $doc_data);

                    if ($this->db->trans_status() !== false) {
                        $data[] = $doc_data;
                    }
                }
                if (!empty($data)) {
                    $result = $data;
                    $this->session->set_flashdata('message', 'Document approval status(es) updated successfully.');
                } else {
                    $this->session->set_flashdata('message', 'Unable to update document approval status.');
                }
            } else {
                $this->session->set_flashdata('message', 'Your request is missing requireed information.');
            }
        } else {
            $this->session->set_flashdata('message', 'Your request is missing requireed information.');
        }

        return $result;
    }
}
