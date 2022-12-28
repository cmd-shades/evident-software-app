<?php
$_version = "2.2.0.0";
if (!class_exists("KoolScripting",false))
{
	class KoolScripting
	{
		static function start()
		{
			ob_start();
			return "";
		}
		static function end()
		{
			$content = ob_get_clean();
			$output = "";
			$xmlDoc = new DOMDocument();
			$xmlDoc->loadXML($content);
			$comNode = $xmlDoc->documentElement;
			$id = $comNode->getAttribute("id");
			$name = $comNode->nodeName;
			$id=($id=="")?"dump":$id;
			if (class_exists($name,false))
			{
				eval("$".$id." = new ".$name."('".$id."');");		
				$$id->loadXML($comNode);
				$output = $$id->Render();
			}
			else
			{
				$output.= $content;
			}
			return $output;
		}	
	}
}
if(!defined('KOOLPHPCOMMON'))
{
	function _replace($key,$rep,$str)
	{
		return str_replace($key,$rep,$str);
	}
	function _getRoot()
	{
		$_php_self = _replace("\\","/",strtolower($_SERVER["SCRIPT_NAME"]));// /koolphpsuite/koolajax/example_callback.php		
		$_php_self = _replace(strrchr($_php_self,"/"),"",$_php_self);		
		$_realpath = _replace("\\","/",realpath("."));// D:\xampplite\htdocs\KoolPHPSuite\KoolAjax		
		$_root = _replace($_php_self,"",strtolower($_realpath));
		return $_root;	
	}
	function _esc($_val)
	{
		return _replace("+"," ",urlencode($_val));
	}
	function _getInnerXML($_node,$_doc)
	{
		$_res ="";
		foreach($_node->childNodes as $_subnode)
		{
			$_res.=$_doc->saveXML($_subnode);	
		}
		return trim($_res);
	}	
	@define('KOOLPHPCOMMON', true);
}
if (!class_exists("KoolUploader", false)) {
  class KoolUploadHandler {
    var $targetFolder = "";
    var $allowedExtension = "*";
    var $funcFileHandle = null;
    var $allowDelete = false;
    var $allowDownload = false;
    function _isAllowed($_filename) {
      if ($this->allowedExtension == '*')
        return true;
      $_filename = strtolower($_filename);
      $_allow_mask_list = explode(",", strtolower($this->allowedExtension));
      foreach ($_allow_mask_list as $_allow_mask)
        if (preg_match("/(\." . $_allow_mask . ")$/", $_filename))
          return true;
      return false;
    }
    function handleUpload() {
      $_targetFolder = '';
      if (isset($_POST['targetFolder']))
        $_targetFolder = $_POST['targetFolder'];
      else
        $_targetFolder = _replace("\\", "/", $this->targetFolder);
      if (isset($_GET[md5(__FILE__ . "upload")])) {
        $_results = array();
        if (!file_exists($_targetFolder)) {
          mkdir($_targetFolder, 0777, true);
        }
        $_files = $_FILES["KUL_FILE"];
        if (count($_files['error'])) {
          $_inputFileIds = explode(',', $_POST["fileIds"]);
          for ($i = 0; $i < count($_files['error']); $i++) {
            $_result = "upload_failed";
            $_error = $_files['error'][$i];
            switch ($_error) {
              case 0:
                if ($this->_isAllowed($_files["name"][$i])) {
                  if ($this->funcFileHandle != null) {
                    $aFile = array();
                    $props = array("name", "type", "tmp_name", "error", "size");
                    for ($n = 0; $n<count($props); $n++)
                      $aFile[$props[$n]] = $_files[$props[$n]][$i];
                    $_tempFunc = $this->funcFileHandle;
                    if ($_tempFunc($aFile) == true) {
                      $_result = "upload_successful";
                    } else {
                      $_result = "upload_failed";
                    }
                  } else {
                    if (move_uploaded_file($_files["tmp_name"][$i], $_targetFolder . "/" . $_files["name"][$i])) {
                      $_result = "upload_successful";
                    } else {
                      $_result = "upload_failed";
                    }
                  }
                } else {
                  $_result = "file_not_allowed";
                }
                break;
              case 1:
                $_result = "file_bigger_than_php_allow";
                break;
              case 2:
                $_result = "file_bigger_than_form_allow";
                break;
              case 3:
                $_result = "only_part_of_file_uploaded";
                break;
              case 4:
              default:
                $_result = "upload_failed";
                break;
            }
            $_result = array(
              'id' => $_inputFileIds[$i],
              "name" => $_files["name"][$i],
              "type" => $_files["type"][$i],
              "size" => $_files["size"][$i],
              "result" => $_result
            );
            array_push($_results, $_result);
          }
        }
        $_tpl_return = "<script type='text/javascript'>try{window.parent.kuldonemultiple({info});}catch(e){console.log(e.message);}</script>";
        $_json = json_encode($_results);
        $_return = _replace("{info}", $_json, $_tpl_return);
        if (isset($_POST['xhr'])) {
          ob_end_clean();
          echo $_json;
          exit();
        }
        else
          return $_return;
      } else if (isset($_GET[md5(__FILE__ . "status")])) {
        $_tpl_item_result = "'{id}':{v}";
        $_default_response = "{'time_start':'0','time_last':'0','speed_average':'1','speed_last':'1','bytes_uploaded' :'0','bytes_total':'1','files_uploaded':'1','est_sec':'0'}";
        $_itemids = array();
        if (isset($_GET["itemids"])) {
          $_itemids = $_GET["itemids"];
        }
        $_result = "{";
        for ($i = 0; $i < sizeof($_itemids); $i++) {
          $_value = (function_exists("uploadprogress_get_info")) ? json_encode(uploadprogress_get_info($_itemids[$i])) : $_default_response;
          $_item_result = _replace("{id}", $_itemids[$i], $_tpl_item_result);
          $_item_result = _replace("{v}", $_value, $_item_result);
          $_result .= $_item_result;
          if ($i < sizeof($_itemids) - 1)
            $_result.=",";
        }
        $_result.= "}";
        return $_result;
      }
      else if (isset($_GET[md5(__FILE__ . "progress")])) {
        $version = explode('.', phpversion());
        if (($version[0] * 10000 + $version[1] * 100 + $version[2]) < 50400)
          die('PHP 5.4.0 or higher is required');
        if (!intval(ini_get('session.upload_progress.enabled')))
          die('session.upload_progress.enabled is not enabled');
        error_reporting(0);
        $_tpl_item_result = "'{id}':{v}";
        $_itemids = array();
        if (isset($_GET["itemids"])) {
          $_itemids = $_GET["itemids"];
        }
        $_result = "{";
        for ($i = 0; $i < sizeof($_itemids); $i++) {
          $progress_key = ini_get("session.upload_progress.prefix") . $_itemids[$i];
          $progress = 0;
          if (empty($_SESSION[$progress_key])) {
            $progress = 100;
          } else {
            $upload_progress = $_SESSION[$progress_key];
            /* get percentage */
            $progress = round(($upload_progress['bytes_processed'] / $upload_progress['content_length']) * 100, 2);
          }
          $_value = "{'progress':" . $progress . ",'bytes_processed':" . $upload_progress['bytes_processed'] . ",'content_length':" . $upload_progress['content_length'] . "}";
          $_item_result = _replace("{id}", $_itemids[$i], $_tpl_item_result);
          $_item_result = _replace("{v}", $_value, $_item_result);
          $_result.=$_item_result;
          if ($i < sizeof($_itemids) - 1)
            $_result.=",";
        }
        $_result.= "}";
        return $_result;
      }
      else if (isset($_GET[md5(__FILE__ . "delete")])) {
        if ( ! $this->allowDelete )
          return false;
        $_result = "Begin deleting";
        if (isset($_POST["DELETE_IDENTIFIER"])) {
          $_filename = $_POST["DELETE_IDENTIFIER"];
          $_targetFolder = '';
          if (isset($_POST['targetFolder']))
            $_targetFolder = $_POST['targetFolder'];
          else
            $_targetFolder = _replace("\\", "/", $this->targetFolder);
          $_delete = unlink($_targetFolder . "/" . $_filename);
          if ($_delete)
            $_result = $_targetFolder . "/" . $_filename . " is deleted";
          else
            $_result = $_targetFolder . "/" . $_filename . " is NOT deleted";
        } else
          $_result = "No delete_identifier";
        $_tpl_return = "<script type='text/javascript'>try{window.parent.kuldeletedone('{id}','{result}',{info});}catch(e){}</script>";
        $_return = _replace("{id}", $_POST["UPLOAD_IDENTIFIER"], $_tpl_return);
        $_return = _replace("{result}", $_result, $_return);
        $_return = _replace("{info}", "{}", $_return);
        return $_return;
      }
      else if (isset($_GET[md5(__FILE__ . "download")])) {
        if ( ! $this->allowDownload )
          return false;
        $_result = "Begin downloading";
        if (isset($_POST["DOWNLOAD_IDENTIFIER"])) {
          $_filename = $_POST["DOWNLOAD_IDENTIFIER"];
          $_targetFolder = '';
          if (isset($_POST['targetFolder']))
            $_targetFolder = $_POST['targetFolder'];
          else
            $_targetFolder = _replace("\\", "/", $this->targetFolder);
          $_file = $_targetFolder . "/" . $_filename;
          $_extension = substr(strrchr($_filename, '.'), 1);
          $_contentType = $this->getMimeType($_file);
          if (file_exists($_file)) {
            header('Content-Type: ' . $_contentType);
            header('Content-Disposition: attachment;filename="' . basename($_filename) . '"');
            header('Content-Length: ' . filesize($_file));
            readfile($_file);
          } else {
            header('HTTP/1.1 404 Not Found');
          }
        } else {
          $_result = "No DOWNLOAD_IDENTIFIER";
          $_tpl_return = "<script type='text/javascript'>try{window.parent.kuldownloaddone('{id}','{result}',{info});}catch(e){}</script>";
          $_return = _replace("{id}", $_POST["UPLOAD_IDENTIFIER"], $_tpl_return);
          $_return = _replace("{result}", $_result, $_return);
          $_return = _replace("{info}", "{}", $_return);
          return $_return;
        }
      }
    }
    function getMimeType($filename) {
      $realpath = realpath($filename);
      if ($realpath && function_exists('finfo_file') && function_exists('finfo_open') && defined('FILEINFO_MIME_TYPE')
      ) {
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $realpath);
      }
      if (function_exists('mime_content_type')) {
        return mime_content_type($realpath);
      }
      return false;
    }
    function handleDelete() {
      $_result = "Begin deleting";
      if (isset($_POST["DELETE_IDENTIFIER"])) {
        $_file = $_POST["DELETE_IDENTIFIER"];
        $_targetFolder = '';
        if (isset($_POST['targetFolder']))
          $_targetFolder = $_POST['targetFolder'];
        else
          $_targetFolder = _replace("\\", "/", $this->targetFolder);
        unlink($_targetFolder . "/" . $_file);
        $_result = $_targetFolder . "/" . $_file . " is deleted";
      } else
        $_result = "No delete_identifier";
      $_tpl_return = "<script type='text/javascript'>try{window.parent.kuldeletedone('{id}','{result}',{info});}catch(e){}</script>";
      $_return = _replace("{id}", $_POST["UPLOAD_IDENTIFIER"], $_tpl_return);
      $_return = _replace("{result}", $_result, $_return);
      $_return = _replace("{info}", "{}", $_return);
      return $_return;
    }
  }
  class KoolUploader {
    var $_version = "2.2.0.0";
    var $id;
    var $styleFolder = "";
    var $_style;
    var $scriptFolder = "";
    var $handlePage = "";
    /*
     * "NONE":"PERCENT":"FILESIZE":"TIME_LEFT":
     */
    var $texts = array(
      "BUTTON_ADD" => "Add",
      "BUTTON_UPLOAD_ALL" => "Upload All",
      "BUTTON_CLEAR_ALL" => "Clear All",
      "BUTTON_UPLOAD" => "Upload",
      "BUTTON_REMOVE" => "Remove",
      "BUTTON_CANCEL" => "Cancel",
      "BUTTON_DELETE" => "Delete",
      "MESSAGE_UPLOAD_SUCCESSFUL" => "Uploaded!",
      "MESSAGE_FILE_NOT_ALLOWED" => "Not allowed!",
      "MESSAGE_FILE_BIGGER_THAN_PHP_ALLOW" => "The file is too big!",
      "MESSAGE_FILE_BIGGER_THAN_FORM_ALLOW" => "The file is too big!",
      "MESSAGE_ONLY_PART_OF_FILE_UPLOADED" => "Not completed!",
      "MESSAGE_UPLOAD_FAILED" => "Failed to upload!",
      "MESSAGE_UPLOAD_CANCEL" => "Upload cancelled!",
      "MESSAGE_DELETED" => "Deleted!"
    );
    var $width = "300px";
    var $height = "200px";
    var $updateProgressInterval = 10;
    /*
     * The milliseconds at which the request will send to update information.
     */
    var $progressTracking = false;
    /*
     * Get or set whether KoolUploader use ajax the track upload progress
     * Default value is false. The KoolUploader will need KoolAjax in case progress tracking is enabled
     */
    var $showProgressBar = true;
    /*
     * Get or set whether KoolUploader show the progressbar
     */
    var $maxFileSize = 2000000;
    /*
     * The maximum file size that allow to upload.
     */
    var $uploadedFiles = array();
    /*
     * uploadedFiles is array of filesnames which has been uploaded
     */
    var $allowedExtension = "*";
    /*
     * The extension that kooluploader is allowed.
     */
    var $targetFolder = '';
    var $currentFiles = array();
    var $mustHaveFiles = array();
    var $allowDelete = false;
    var $allowDownload = false;
    var $autoUpload = false;
    var $multipleUpload = false;
    var $_error_message1 = '';
    var $_error_message2 = '';
    var $dragAndDrop = true;
    var $peclUploadProgress = false;
    /*
     * Allow the file upload sequentially, avoiding file upload corrupted.
     */
    function __construct($_id) {
      $this->id = $_id;
      $_text_uploadedFiles = "";
      if (isset($_POST[$_id . "_uploadedFiles"])) {
        $_text_uploadedFiles = $_POST[$_id . "_uploadedFiles"];
      } else if (isset($_GET[$_id . ".uploadedFiles"])) {
        $_text_uploadedFiles = $_GET[$_id . "_uploadedFiles"];
      }
      $_text_uploadedFiles = trim(trim($_text_uploadedFiles, "[|-"), "-|]");
      if (strlen($_text_uploadedFiles) > 0) {
        $_files_text = explode("-|][|-", $_text_uploadedFiles);
        foreach ($_files_text as $_file_text) {
          $_info = explode("-|-", $_file_text);
          $_file_info = array("name" => $_info[0], "type" => $_info[1], "size" => $_info[2]);
          array_push($this->uploadedFiles, $_file_info);
        }
      }
    }
    function LoadXML($_xml) {
      if (gettype($_xml) == "string") {
        $_xmlDoc = new DOMDocument();
        $_xmlDoc->loadXML($_xml);
        $_xml = $_xmlDoc->documentElement;
      }
      $_id = $_xml->getAttribute("id");
      if ($_id != "")
        $this->id = $_id;
      $_styleFolder = $_xml->getAttribute("styleFolder");
      if ($_styleFolder != "")
        $this->styleFolder = $_styleFolder;
    }
    function HandleUpload() {
      $_s = '';
      $kulhandle = new KoolUploadHandler();
      $kulhandle->targetFolder = "../../Temp";
      $kulhandle->allowedExtension = "gif,jpg,doc,pdf,txt";
      $_s .= $kulhandle->handleUpload();
      return $_s;
    }
    function Render() {
      $_script = "\n<!--KoolUploader version " . $this->_version . " - www.koolphp.net -->\n";
      $_script.= $this->RegisterCss();
      $_script.= $this->RenderUploader();
      $_is_callback = isset($_POST["__koolajax"]) || isset($_GET["__koolajax"]);
      $_script.= ($_is_callback) ? "" : $this->RegisterScript();
      $_script.="<script type='text/javascript'>";
      $_script.= $this->StartupScript();
      $_script.="</script>";
      return $_script;
    }
    function RenderUploader() {
      $this->_positionStyle();
      $tpl_main = "{container}{btnAdd}{btnClearAll}{btnUploadAll}";
      $tpl_btnAdd = "{btncontent}";
      $tpl_btnUploadAll = "{btncontent}";
      $tpl_btnClearAll = "{btncontent}";
      $tpl_btnUpload = "{btncontent}";
      $tpl_btnRemove = "{btncontent}";
      $tpl_btnCancel = "{btncontent}";
      $tpl_btnDelete = "{btncontent}";
      include "styles" . "/" . $this->_style . "/" . $this->_style . ".tpl";
      $this->_error_message1 = $error_message1;
      $this->_error_message2 = $error_message2;
      $_tpl_main = "<div id='{id}' class='{style}KUL' style='width:{width};height:{height};'>{maincontent}{uploadedFiles}{templates}</div>";
      $_tpl_template = "<div id='{id}' class='kulTemplate' style='display:none;'>{content}</div>";
      $_tpl_container = "<div id='{id}.container' class='kulContainer'></div>";
      $_tpl_item = "<div class='kulItem'>{tpl_item}</div>";
      $_tpl_btn_text = "<span class='kulText'>{text}</span>";
      $_tpl_uploadedFiles = "<input type='hidden' id='{id}_uploadedFiles' name='{id}_uploadedFiles' />";
      $_tpl_btnAdd = "<span id='{id}.btn.add' class='kulAdd'><a class='kulA'>{tpl_btnAdd}</a></span>";
      $_tpl_inputfile = "<span class='kulFile'><input id='{id}' type='file' /></span>";
      $_tpl_btnUploadAll = "<span id='{id}.btn.uploadall' class='kulUploadAll'><a class='kulA'>{tpl_btnUploadAll}</a></span>";
      $_tpl_btnClearAll = "<span id='{id}.btn.clearall' class='kulClearAll'><a class='kulA'>{tpl_btnClearAll}</a></span>";
      $_tpl_btnUpload = "<span class='kulUpload'><a title='Upload' class='kulA kul-icon kul-upload_brown'>{tpl_btnUpload}</a></span>";
      $_tpl_btnRemove = "<span class='kulRemove'><a title='Remove' class='kulA kul-icon kul-minus_grey'>{tpl_btnRemove}</a></span>";
      $_tpl_btnCancel = "<span class='kulCancel'><a title='Cancel' class='kulA kul-icon kul-cross-grey-16'>{tpl_btnCancel}</a></span>";
      $_tpl_btnDelete = "<span class='kulDelete'><a title='Delete' class='kulA kul-icon kul-cross-grey-16'>{tpl_btnDelete}</a></span>";
      $_tpl_filename = "<span class='kulFileName'> </span>";
      if (!empty($this->mustHaveFiles)) {
        $_tpl_description = "<select class='kulDescription'>{options}</select>";
        $_options = '"<option></option>";';
        foreach ($this->mustHaveFiles as $_must)
          $_options .= "<option value='$_must'>$_must</option>";
        $_tpl_description = _replace("{options}", $_options, $_tpl_description);
      } else
        $_tpl_description = '';
      $_tpl_progress = "<span class='kulProgress'><img class='kulBar {altbar}' {style} alt=''/><span class='kulText'> </span></span>";
      $_tpl_status = "<span class='kulStatus'> </span>";
      $_tpl_thumbnail = "<span class='kulThumbnail'> </span>";
      $_upload_successful_template = _replace("{id}", $this->id . ".message.upload_successful", $_tpl_template);
      $_upload_successful_template = _replace("{content}", $this->texts["MESSAGE_UPLOAD_SUCCESSFUL"], $_upload_successful_template);
      $_failed_to_upload_template = _replace("{id}", $this->id . ".message.upload_failed", $_tpl_template);
      $_failed_to_upload_template = _replace("{content}", $this->texts["MESSAGE_UPLOAD_FAILED"], $_failed_to_upload_template);
      $_file_not_allowed_template = _replace("{id}", $this->id . ".message.file_not_allowed", $_tpl_template);
      $_file_not_allowed_template = _replace("{content}", $this->texts["MESSAGE_FILE_NOT_ALLOWED"], $_file_not_allowed_template);
      $_file_bigger_than_php_allow_template = _replace("{id}", $this->id . ".message.file_bigger_than_php_allow", $_tpl_template);
      $_file_bigger_than_php_allow_template = _replace("{content}", $this->texts["MESSAGE_FILE_BIGGER_THAN_PHP_ALLOW"], $_file_bigger_than_php_allow_template);
      $_file_bigger_than_form_allow_template = _replace("{id}", $this->id . ".message.file_bigger_than_form_allow", $_tpl_template);
      $_file_bigger_than_form_allow_template = _replace("{content}", $this->texts["MESSAGE_FILE_BIGGER_THAN_FORM_ALLOW"], $_file_bigger_than_form_allow_template);
      $_only_part_of_file_uploaded_template = _replace("{id}", $this->id . ".message.only_part_of_file_uploaded", $_tpl_template);
      $_only_part_of_file_uploaded_template = _replace("{content}", $this->texts["MESSAGE_ONLY_PART_OF_FILE_UPLOADED"], $_only_part_of_file_uploaded_template);
      $_upload_cancel_template = _replace("{id}", $this->id . ".message.upload_cancel", $_tpl_template);
      $_upload_cancel_template = _replace("{content}", $this->texts["MESSAGE_UPLOAD_CANCEL"], $_upload_cancel_template);
      $_deleted_template = _replace("{id}", $this->id . ".message.deleted", $_tpl_template);
      $_deleted_template = _replace("{content}", $this->texts["MESSAGE_DELETED"], $_deleted_template);
      $_item_template = _replace("{id}", $this->id . ".template.item", $_tpl_template);
      $_item_template = _replace("{content}", $_tpl_item, $_item_template);
      $_item_template = _replace("{tpl_item}", $tpl_item, $_item_template);
      $_btnRemove = _replace("{tpl_btnRemove}", $tpl_btnRemove, $_tpl_btnRemove);
      $_btnRemove = _replace("{btncontent}", $this->texts["BUTTON_REMOVE"], $_btnRemove);
      $_btnUpload = _replace("{tpl_btnUpload}", $tpl_btnUpload, $_tpl_btnUpload);
      $_btnUpload = _replace("{btncontent}", $this->texts["BUTTON_UPLOAD"], $_btnUpload);
      $_btnCancel = _replace("{tpl_btnCancel}", $tpl_btnUpload, $_tpl_btnCancel);
      $_btnCancel = _replace("{btncontent}", $this->texts["BUTTON_CANCEL"], $_btnCancel);
      $_btnDelete = _replace("{tpl_btnDelete}", $tpl_btnDelete, $_tpl_btnDelete);
      $_btnDelete = _replace("{btncontent}", $this->texts["BUTTON_DELETE"], $_btnDelete);
      $_item_template = _replace("{btnUpload}", $_btnUpload, $_item_template);
      $_item_template = _replace("{btnRemove}", $_btnRemove, $_item_template);
      $_item_template = _replace("{btnCancel}", $_btnCancel, $_item_template);
      $_item_template = _replace("{btnDelete}", $_btnDelete, $_item_template);
      $_item_template = _replace("{filename}", $_tpl_filename, $_item_template);
      $_item_template = _replace("{description}", $_tpl_description, $_item_template);
      $_item_template = _replace("{status}", $_tpl_status, $_item_template);
      $_item_template = _replace("{thumbnail}", $_tpl_thumbnail, $_item_template);
      $_progress = _replace("{styleFolder}", $this->styleFolder, $_tpl_progress);
      /*
        if ((int)$_expiredString<_getTimeNow())
        {
        $this->progressTracking = false;
        }
       */
      $_progress = _replace("{altbar}", ($this->progressTracking) ? (intval(ini_get('session.upload_progress.enabled')) ? "" : "kulAltBar") : "kulAltBar", $_progress);
      $_progress = _replace("{style}", ($this->showProgressBar) ? "" : "style='display:none;'", $_progress);
      $_item_template = _replace("{progress}", $_progress, $_item_template);
      $_btnAdd = _replace("{id}", $this->id, $_tpl_btnAdd);
      $_btnAdd = _replace("{tpl_btnAdd}", $tpl_btnAdd, $_btnAdd);
      $_btnAdd = _replace("{btncontent}", $this->texts["BUTTON_ADD"], $_btnAdd);
      $_btnUploadAll = _replace("{id}", $this->id, $_tpl_btnUploadAll);
      $_btnUploadAll = _replace("{tpl_btnUploadAll}", $tpl_btnUploadAll, $_btnUploadAll);
      $_btnUploadAll = _replace("{btncontent}", $this->texts["BUTTON_UPLOAD_ALL"], $_btnUploadAll);
      $_btnClearAll = _replace("{id}", $this->id, $_tpl_btnClearAll);
      $_btnClearAll = _replace("{tpl_btnClearAll}", $tpl_btnClearAll, $_btnClearAll);
      $_btnClearAll = _replace("{btncontent}", $this->texts["BUTTON_CLEAR_ALL"], $_btnClearAll);
      $_container = _replace("{id}", $this->id, $_tpl_container);
      $_maincontent = _replace("{container}", $_container, $tpl_main);
      $_maincontent = _replace("{btnAdd}", $_btnAdd, $_maincontent);
      $_maincontent = _replace("{btnUploadAll}", $_btnUploadAll, $_maincontent);
      $_maincontent = _replace("{btnClearAll}", $_btnClearAll, $_maincontent);
      $_main = _replace("{id}", $this->id, $_tpl_main);
      $_main = _replace("{style}", $this->_style, $_main);
      $_main = _replace("{width}", $this->width, $_main);
      $_main = _replace("{height}", $this->height, $_main);
      if (true) {
        $_main = _replace("{templates}", $_upload_successful_template . $_file_not_allowed_template . $_failed_to_upload_template . $_item_template . $_file_bigger_than_php_allow_template . $_file_bigger_than_form_allow_template . $_only_part_of_file_uploaded_template . $_upload_cancel_template . $_deleted_template, $_main);
      }
      $_main = _replace("{version}", $this->_version, $_main);
      $_main = _replace("{maincontent}", $_maincontent, $_main);
      $_main = _replace("{uploadedFiles}", _replace("{id}", $this->id, $_tpl_uploadedFiles), $_main);
      return $_main;
    }
    function _positionStyle() {
      $this->styleFolder = _replace("\\", "/", $this->styleFolder);
      $_styleFolder = trim($this->styleFolder, "/");
      $_lastpos = strrpos($_styleFolder, "/");
      $this->_style = substr($_styleFolder, ($_lastpos ? $_lastpos : -1) + 1);
    }
    function RegisterCss() {
      /*
       * Register Css
       */
      $this->_positionStyle();
      $_tpl_script = "<script type='text/javascript'>if (document.getElementById('__{style}KUL')==null){var _head = document.getElementsByTagName('head')[0];var _link = document.createElement('link'); _link.id = '__{style}KUL';_link.rel='stylesheet'; _link.href='{stylepath}/{style}/{style}.css';_head.appendChild(_link); _link = document.createElement('link'); _link.id = '__KUL';_link.rel='stylesheet'; _link.href='{stylepath}/kooluploader.css';_head.appendChild(_link);}</script>";
      $_script = _replace("{style}", $this->_style, $_tpl_script);
      $_script = _replace("{stylepath}", $this->_getStylePath(), $_script);
      return $_script;
    }
    function RegisterScript($_styleInit = true) {
      /*
       * Register javascript
       */
      $this->_positionStyle();
      if ($_styleInit) {
        $_tpl_script = "<script type='text/javascript'>if(typeof _libKUL=='undefined'){document.write(unescape(\"%3Cscript type='text/javascript' src='{src}'%3E %3C/script%3E\"));_libKUL=1;}";
        $_tpl_script .="if(typeof {style}KULInit=='undefined'){document.write(unescape(\"%3Cscript type='text/javascript' src='{stylepath}/{style}/{style}.js'%3E %3C/script%3E\"));}</script>";
      } else
        $_tpl_script = "<script type='text/javascript'>if(typeof _libKUL=='undefined'){document.write(unescape(\"%3Cscript type='text/javascript' src='{src}'%3E %3C/script%3E\"));_libKUL=1;}</script>";
      $_script = _replace("{src}", _replace(".php", ".js", $this->_getComponentURI()), $_tpl_script); //Do comment to obfuscate
      $_script = _replace("{style}", $this->_style, $_script);
      $_script = _replace("{stylepath}", $this->_getStylePath(), $_script);
      return $_script;
    }
    function StartupScript() {
      /*
       * Generate startup script
       */
      $this->_positionStyle();
      $_info = array(
        'id' => $this->id,
        'handlePage' => $this->handlePage,
        'upload' => md5(__FILE__ . "upload"),
        'status' => md5(__FILE__ . "status"),
        'progress' => md5(__FILE__ . "progress"),
        'del' => md5(__FILE__ . "delete"),
        'download' => md5(__FILE__ . "download"),
        'updateProgressInterval' => $this->updateProgressInterval,
        'peclUploadProgress' => ($this->peclUploadProgress) ? (function_exists("uploadprogress_get_info") ? true : false) : false,
        'progressTracking' => ($this->progressTracking) ? (intval(ini_get('session.upload_progress.enabled')) ? true : false) : false,
        'progressTrackingName' => ini_get("session.upload_progress.name"),
        'maxFileSize' => $this->maxFileSize,
        'allowedExtension' => $this->allowedExtension,
        'targetFolder' => $this->targetFolder,
        'currentFiles' => $this->currentFiles,
        'mustHaveFiles' => $this->mustHaveFiles,
        'allowDelete' => $this->allowDelete,
        'allowDownload' => $this->allowDownload,
        'autoUpload' => $this->autoUpload,
        'multipleUpload' => $this->multipleUpload,
        'errorMessage1' => $this->_error_message1,
        'errorMessage2' => $this->_error_message2,
        'dragAndDrop' => $this->dragAndDrop,
      );
        $_tpl = "var upload_progress_name='" . ini_get("session.upload_progress.name") . "'; var {id}=new KoolUploader({info});";
      $_script = _replace("{id}", $this->id, $_tpl);
      $_script = _replace("{style}", $this->_style, $_script);
      $_script = _replace("{info}", json_encode($_info), $_script);
      return $_script;
    }
    function _getComponentURI() {
      if ($this->scriptFolder == "") {
        $_root = _getRoot();
        $_file = substr(_replace("\\", "/", __FILE__), strlen($_root));
        return $_file;
      } else {
        $_file = _replace("\\", "/", __FILE__);
        $_file = $this->scriptFolder . substr($_file, strrpos($_file, "/"));
        return $_file;
      }
    }
    function _getStylePath() {
      $_com_uri = $this->_getComponentURI();
      $_styles_folder = _replace(strrchr($_com_uri, "/"), "", $_com_uri) . "/styles";
      return $_styles_folder;
    }
  }
}
?>
