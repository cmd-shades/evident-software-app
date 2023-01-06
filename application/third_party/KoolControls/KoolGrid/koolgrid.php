<?php
$_version = "5.7.0.1";
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
if (!class_exists("KoolGrid", false)) {
  require_once(dirname(__FILE__) . '/GridIValueMap.php');
  function _escape($_val) {
    return _replace("+", " ", urlencode($_val));
  }
  function _unescape($_val) {
    return urldecode(_replace(" ", "+", $_val));
  }
  function _slash_decode($_val) {
    /*
      $_val = _replace("\\'","'",$_val);
      $_val = _replace("\\\"","\"",$_val);
      $_val = _replace("\\\\","\\",$_val);
      return $_val;
     */
    return stripslashes($_val);
  }
  function _slash_encode($_val) {
    /*
      $_val = _replace("\\","\\\\",$_val);
      $_val = _replace("'","\\'",$_val);
      $_val = _replace("\"","\\\"",$_val);
      return $_val;
     */
    return addslashes($_val);
  }
  function _quotes_encode($_val) {
    $_val = _replace("'", "&apos;", $_val);
    $_val = _replace('"', "&quot;", $_val);
    return $_val;
  }
  function _get_key_data($_pool, $_key_names) {
    if( is_null( $_key_names ) ) { return array(); }
    $_arr_keys = explode(",", $_key_names);
    $_res = array();
    if ($_arr_keys != null) {
      for ($i = 0; $i < sizeof($_arr_keys); $i++) {
        $_arr_keys[$i] = trim($_arr_keys[$i]);
        $_res[$_arr_keys[$i]] = $_pool[$_arr_keys[$i]];
      }
    }
    return $_res;
  }
  function _unique_key($_pool) {
    $_text = "";
    foreach ($_pool as $_item) {
      $_text .= $_item;
    }
    return md5($_text);
  }
  function mergesort(&$array, $cmp_function = 'strcmp', $arg = null) {
      if (count($array) < 2) return;
      $halfway = count($array) / 2;
      $array1 = array_slice($array, 0, $halfway);
      $array2 = array_slice($array, $halfway);
      mergesort($array1, $cmp_function, $arg);
      mergesort($array2, $cmp_function, $arg);
      if (call_user_func($cmp_function, end($array1), $array2[0], $arg) < 1) {
          $array = array_merge($array1, $array2);
          return;
      }
      $array = array();
      $ptr1 = $ptr2 = 0;
      while ($ptr1 < count($array1) && $ptr2 < count($array2)) {
          if (call_user_func($cmp_function, $array1[$ptr1], $array2[$ptr2], $arg) < 1) {
              $array[] = $array1[$ptr1++];
          }
          else {
              $array[] = $array2[$ptr2++];
          }
      }
      while ($ptr1 < count($array1)) $array[] = $array1[$ptr1++];
      while ($ptr2 < count($array2)) $array[] = $array2[$ptr2++];
      return;
  }
  function sortcmp($a1, $a2, $order) {
    if ($a1->SortOrder === $a2->SortOrder)
      return 0;
    else {
      if ($order === 'lcfs')
        return ($a1->SortOrder < $a2->SortOrder) ? 1 : -1;
      else if ($order === 'fcfs')
        return ($a1->SortOrder < $a2->SortOrder) ? -1 : 1;
    }
  }
  /*   * ***************************************************************************************** */
  /*   * ***************************************************************************************** */
  /*   * ***************************************************************************************** */
  /*   * ***************************************************************************************** */
  /*   * ***************************************************************************************** */
  class DataSourceFilter {
    var $Field;
    var $Expression;
    var $Value;
    var $NullFilter = false;
    function __construct($_field, $_expression, $_value, $_nullFilter = false) {
      $this->Field = $_field;
      $this->Expression = $_expression;
      $this->Value = _slash_encode($_value);
      $this->NullFilter = $_nullFilter;
    }
  }
  class DataSourceSort {
    var $Field;
    var $Order;
    var $SortOrder;
    function __construct($_field, $_order = "ASC", $_sortOrder = 0) {
      $this->Field = $_field;
      $this->Order = $_order;
      $this->SortOrder = $_sortOrder;
    }
  }
  class DataSourceGroup {
    var $Field;
    function __construct($_field) {
      $this->Field = $_field;
    }
  }
  class DataSource {
    var $Sorts = array();
    var $Filters = array();
    var $Groups = array();
    protected $_valueMap = NULL;
    public function setValueMap($map) {
      $this->_valueMap = $map;
    }
    public function getValueMap() {
      return $this->_valueMap;
    }
    public function getMappedValue($_value, $_column) {
      if (isset($this->_valueMap))
        $_value = $this->_valueMap->mapValue($_value, $_column);
      return $_value;
    }
    public function getInverseMappedValue($_value, $_column) {
      if (isset($this->_valueMap))
        $_value = $this->_valueMap->inverseMapValue($_value, $_column);
      return $_value;
    }
    function Count() {
    }
    function GetFields() {
    }
    function GetData($start = 0, $count = 9999999) {
    }
    function GetAggregates($_arr) {
    }
    function Insert($_associate_array) {
      return false;
    }
    function Update($_associate_array) {
      return false;
    }
    function Delete($_associate_array) {
      return false;
    }
    function AddSort($_sort) {
      array_push($this->Sorts, $_sort);
    }
    function AddFilter($_filter) {
      array_push($this->Filters, $_filter);
    }
    function AddGroup($_group) {
      array_push($this->Groups, $_group);
    }
    function Clear() {
      $this->Sorts = array();
      $this->Filters = array();
      $this->Groups = array();
    }
    function GetError() {
      return "";
    }
    function SetCharSet($_charset) {
    }
    function database_escape($_s) {
      return $_s;
    }
    function GetFilterExpression($_filter) {
      $_expression = "";
      $_value = $this->database_escape($_filter->Value);
      switch ($_filter->Expression) {
        case "Equal":
          $_expression = "=";
          break;
        case "Not_Equal":
          $_expression = "<>";
          break;
        case "Greater_Than":
          $_expression = ">";
          break;
        case "Less_Than":
          $_expression = "<";
          break;
        case "Greater_Than_Or_Equal":
          $_expression = ">=";
          break;
        case "Less_Than_Or_Equal":
          $_expression = "<=";
          break;
        case "Contain":
          $_expression = "LIKE";
          $_value = "%" . $_value . "%";
          break;
        case "Not_Contain":
          $_expression = "NOT LIKE";
          $_value = "%" . $_value . "%";
          break;
        case "Start_With":
          $_expression = "LIKE";
          $_value = $_value . "%";
          break;
        case "End_With":
          $_expression = "LIKE";
          $_value = "%" . $_value;
          break;
        default:
          $_expression = "";
      }
      if (! empty($_expression)) {
        $_s = $_filter->Field . " " . $_expression . " '" . $_value . "'";
        if ($_filter->NullFilter && empty($_filter->Value))
          $_s = '(' . $_s . ' or ' . $_filter->Field . " is null)";
      }
      else
        $_s = "";
      return $_s;
    }
    function ArrangeSorts($MultiSortingOrder) {
      $order = strtolower($MultiSortingOrder);
      if ($order === 'right-left') 
        $this->Sorts = array_reverse($this->Sorts);
      else if ($order === 'fcfs' || $order === 'lcfs') {
        mergesort($this->Sorts, 'sortcmp', $order);
      }
    }
  }
  class MySQLDataSource extends DataSource {
    var $SelectCommand;
    var $UpdateCommand;
    var $InsertCommand;
    var $DeleteCommand;
    var $_Link;
    function __construct($_link) {
      $this->_Link = $_link;
    }
    function database_escape($_s) {
      return mysql_real_escape_string($_s);
    }
    function Count() {
      $_count_command = "SELECT COUNT(*) FROM (" . $this->SelectCommand . ") AS _TMP {where}";
      $_where = "";
      $_filters = $this->Filters;
      for ($i = 0; $i < sizeof($_filters); $i++) {
        $_condition = $this->GetFilterExpression($_filters[$i]);
        if (! empty($_condition))
          $_where.=" and " . $_condition;
      }
      if ($_where != "") {
        $_where = "WHERE " . substr($_where, 5);
      }
      $_count_command = _replace("{where}", $_where, $_count_command);
      $_result = mysql_query($_count_command, $this->_Link);
      $_count = mysql_result($_result, 0, 0);
      mysql_free_result($_result);
      return $_count;
    }
    function GetFields() {
      $_fields = array();
      $_result = mysql_query($this->SelectCommand, $this->_Link);
      while ($_prop = mysql_fetch_field($_result)) {
        $_field = array(
          "Name" => $_prop->name,
          "Type" => $_prop->type,
          "Not_Null" => $_prop->not_null
        );
        array_push($_fields, $_field);
      }
      mysql_free_result($_result);
      return $_fields;
    }
    function GetRawData() {
      $_result = mysql_query($this->SelectCommand, $this->_Link);
      $_rows = array();
      while ($_row = mysql_fetch_assoc($_result)) {
        array_push($_rows, $_row);
      }
      mysql_free_result($_result);
      return $_rows;
    }
    function GetData($_start = 0, $_count = 9999999) {
      $_tpl_select_command = "SELECT * FROM ({SelectCommand}) AS _TMP {where} {orderby} {groupby} {limit}";
      $_where = "";
      $_filters = $this->Filters;
      for ($i = 0; $i < sizeof($_filters); $i++) {
        $_condition = $this->GetFilterExpression($_filters[$i]);
        if (! empty($_condition))
          $_where.=" and " . $_condition;
      }
      if ($_where != "") {
        $_where = "WHERE " . substr($_where, 5);
      }
      $_orderby = "";
      $_orders = $this->Sorts;      
      for ($i = 0; $i < sizeof($_orders); $i++) {
        $_orderby.=", " . $_orders[$i]->Field . " " . $_orders[$i]->Order;
      }
      if ($_orderby != "") {
        $_orderby = "ORDER BY " . substr($_orderby, 2);
      }
      $_groupby = "";
      $_groups = $this->Groups;
      for ($i = 0; $i < sizeof($_groups); $i++) {
        $_groupby.=", " . $_groups[$i]->Field;
      }
      if ($_groupby != "") {
        $_groupby = "GROUP BY " . substr($_groupby, 2);
      }
      $_limit = "LIMIT " . $_start . " , " . $_count;
      $_select_command = _replace("{SelectCommand}", $this->SelectCommand, $_tpl_select_command);
      $_select_command = _replace("{where}", $_where, $_select_command);
      $_select_command = _replace("{orderby}", $_orderby, $_select_command);
      $_select_command = _replace("{groupby}", $_groupby, $_select_command);
      $_select_command = _replace("{limit}", $_limit, $_select_command);
      $_result = mysql_query($_select_command, $this->_Link);
      $_rows = array();
      while ($_row = mysql_fetch_assoc($_result)) {
        foreach ($_row as $_column => & $_value)
          $_value = $this->getMappedValue($_value, $_column);
        array_push($_rows, $_row);
      }
      mysql_free_result($_result);
      return $_rows;
    }
    function ExportData($_settings, $_columns, $_filePath, $_start = 0, $_count = 9999999) {
      $_firstLine = '';
      $_first_col = true;
      foreach ($_columns as $_column) {
        if (!$_first_col)
          $_firstLine .= ",";
        $_firstLine .= "\"" . $_column['HeaderText'] . "\" as " . $_column['DataField'];
        $_first_col = false;
      }
      $_firstLine .= "\r\n";
      $_tpl_select_command = "SELECT " . $_firstLine . " UNION SELECT * FROM ({SelectCommand}) AS _TMP {where} {orderby} {groupby} {limit}";
      $_where = "";
      $_filters = $this->Filters;
      for ($i = 0; $i < sizeof($_filters); $i++) {
        $_condition = $this->GetFilterExpression($_filters[$i]);
        if (! empty($_condition))
          $_where.=" and " . $_condition;
      }
      if ($_where != "") {
        $_where = "WHERE " . substr($_where, 5);
      }
      $_orderby = "";
      $_orders = $this->Sorts;      
      for ($i = 0; $i < sizeof($_orders); $i++) {
        $_orderby.=", " . $_orders[$i]->Field . " " . $_orders[$i]->Order;
      }
      if ($_orderby != "") {
        $_orderby = "ORDER BY " . substr($_orderby, 2);
      }
      $_groupby = "";
      $_groups = $this->Groups;
      for ($i = 0; $i < sizeof($_groups); $i++) {
        $_groupby.=", " . $_groups[$i]->Field;
      }
      if ($_groupby != "") {
        $_groupby = "GROUP BY " . substr($_groupby, 2);
      }
      $_limit = "LIMIT " . $_start . " , " . $_count;
      $_select_command = _replace("{SelectCommand}", $this->SelectCommand, $_tpl_select_command);
      $_select_command = _replace("{where}", $_where, $_select_command);
      $_select_command = _replace("{orderby}", $_orderby, $_select_command);
      $_select_command = _replace("{groupby}", $_groupby, $_select_command);
      $_select_command = _replace("{limit}", $_limit, $_select_command);
      $_select_command .= " INTO OUTFILE '" . $_filePath . "' FIELDS TERMINATED BY '" . $_settings->CsvDelimiter . "' ENCLOSED BY '" . $_settings->CsvQuote ."';";
      $_result = mysql_query($_select_command, $this->_Link);
    }
    function GetAggregates($_arr) { //Only with MYSQLDataSource
      $_tpl_select_command = "SELECT {text} FROM ({SelectCommand}) AS _TMP {where} {orderby} {groupby}";
      $_text = "";
      $_agg_result = array();
      foreach ($_arr as $_aggregate) {
        if (strpos("-|min|max|first|last|count|sum|avg|", "|" . strtolower($_aggregate["Aggregate"]) . "|") > 0) {
          $_text .= ", " . $_aggregate["Aggregate"] . "(" . $_aggregate["DataField"] . ") as " . $_aggregate["Key"];
        }
      }
      if ($_text != "") {
        $_text = substr($_text, 2);
        $_where = "";
        $_filters = $this->Filters;
        for ($i = 0; $i < sizeof($_filters); $i++) {
          $_condition = $this->GetFilterExpression($_filters[$i]);
          if (! empty($_condition))
            $_where.=" and " . $_condition;
        }
        if ($_where != "") {
          $_where = "WHERE " . substr($_where, 5);
        }
        $_orderby = "";
        $_orders = $this->Sorts;
        for ($i = 0; $i < sizeof($_orders); $i++) {
          $_orderby.=", " . $_orders[$i]->Field . " " . $_orders[$i]->Order;
        }
        if ($_orderby != "") {
          $_orderby = "ORDER BY " . substr($_orderby, 2);
        }
        $_groupby = "";
        $_groups = $this->Groups;
        for ($i = 0; $i < sizeof($_groups); $i++) {
          $_groupby.=", " . $_groups[$i]->Field;
        }
        if ($_groupby != "") {
          $_groupby = "GROUP BY " . substr($_groupby, 2);
        }
        $_select_command = _replace("{SelectCommand}", $this->SelectCommand, $_tpl_select_command);
        $_select_command = _replace("{text}", $_text, $_select_command);
        $_select_command = _replace("{where}", $_where, $_select_command);
        $_select_command = _replace("{orderby}", $_orderby, $_select_command);
        $_select_command = _replace("{groupby}", $_groupby, $_select_command);
        $_result = mysql_query($_select_command, $this->_Link);
        $_agg_result = mysql_fetch_assoc($_result);
        foreach ($_agg_result as $_column => & $_value)
          $_value = $this->getMappedValue($_value, $_column);
        mysql_free_result($_result);
      }
      return $_agg_result;
    }
    function Insert($_associate_array, $fileCols = array(), & $rowIds = array()) {
      $_insert_commands = explode(";", $this->InsertCommand);
      foreach ($_associate_array as $_key => $_value) {
        $_value = $this->getInverseMappedValue($_value, $_key);
        for ($i = 0; $i < sizeof($_insert_commands); $i++) {
          $_insert_commands[$i] = preg_replace("/(@" . $_key . ")([\)\'\"]*[\s;,])/", preg_quote(addslashes($_value)) . "$2", $_insert_commands[$i]);
          $_insert_commands[$i] = preg_replace("/(@" . $_key . ")([\)\'\"]*$)/", preg_quote(addslashes($_value)) . "$2", $_insert_commands[$i]);
        }
      }
      foreach ($_insert_commands as $_insert_command) {
        $_insert_command = trim($_insert_command);
        if (!empty($_insert_command))
          if (mysql_query($_insert_command, $this->_Link) == false) {
            return false;
          }
      }
      foreach ($fileCols as $col) {
        if (isset($_associate_array[$col->IdColumn])) {
          array_push($rowIds, $_associate_array[$col->IdColumn]);
        } else {
          array_push($rowIds, mysql_insert_id($this->_Link));
        }
      }
      return true;
    }
    function Update($_associate_array) {
      $_update_commands = explode(";", $this->UpdateCommand);
      foreach ($_associate_array as $_key => $_value) {
        $_value = $this->getInverseMappedValue($_value, $_key);
        for ($i = 0; $i < sizeof($_update_commands); $i++) {
          $_update_commands[$i] = preg_replace("/(@" . $_key . ")([\)\'\"]*[\s;,])/", preg_quote(addslashes($_value)) . "$2", $_update_commands[$i]);
          $_update_commands[$i] = preg_replace("/(@" . $_key . ")([\)\'\"]*$)/", preg_quote(addslashes($_value)) . "$2", $_update_commands[$i]);
        }
      }
      foreach ($_update_commands as $_update_command) {
        if (mysql_query($_update_command, $this->_Link) == false) {
          return false;
        }
      }
      return true;
    }
    function Delete($_associate_array) {
      $_delete_commands = explode(";", $this->DeleteCommand);
      foreach ($_associate_array as $_key => $_value) {
        $_value = $this->getInverseMappedValue($_value, $_key);
        for ($i = 0; $i < sizeof($_delete_commands); $i++) {
          $_delete_commands[$i] = preg_replace("/(@" . $_key . ")([\)\'\"]*[\s;,])/", preg_quote(addslashes($_value)) . "$2", $_delete_commands[$i]);
          $_delete_commands[$i] = preg_replace("/(@" . $_key . ")([\)\'\"]*$)/", preg_quote(addslashes($_value)) . "$2", $_delete_commands[$i]);
        }
      }
      foreach ($_delete_commands as $_delete_command) {
        if (mysql_query($_delete_command, $this->_Link) == false) {
          return false;
        }
      }
      return true;
    }
    function GetError() {
      return mysql_error($this->_Link);
    }
    function SetCharSet($_charset) {
      mysql_set_charset($_charset, $this->_Link);
    }
  }
  class ArrayDataSource extends DataSource {
    var $_Data;
    function __construct($_array_data) {
      $this->_Data = $_array_data;
    }
    function Count() {
      return sizeof($this->_Data);
    }
    function GetFields() {
      $_fields = array();
      $_data = $this->_Data[0];
      foreach ($_data as $_k => $_v) {
        $_field = array(
          "Name" => $_k,
          "Type" => "",
          "Not_Null" => false
        );
        array_push($_fields, $_field);
      }
      return $_fields;
    }
    function GetData($start = 0, $count = 9999999) {
      $arr = array();
      if ($start > $this->Count())
        return $arr;
      if ($start + $count > $this->Count()) {
        $count = $this->Count() - $start;
      }
      for ($i = 0; $i < $count; $i++) {
        array_push($arr, $this->_Data[$start + $i]);
      }
      return $arr;
    }
  }
  /*   * ***************************************************************************************** */
  /*   * ***************************************************************************************** */
  /*   * ***************************************************************************************** */
  /*   * ***************************************************************************************** */
  /*   * ***************************************************************************************** */
  interface _IState {
    function _LoadViewState();
    function _SaveViewState();
  }
  /* =========================================================================== */
  /*
    class _Cipher {
    private $securekey, $iv;
    function __construct($textkey) {
    $this->securekey = hash('sha256',$textkey,TRUE);
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    $this->iv = mcrypt_create_iv($iv_size,MCRYPT_RAND);
    }
    function _encrypt($input) {
    return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->securekey, $input, MCRYPT_MODE_ECB, $this->iv));
    }
    function _decrypt($input) {
    return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->securekey, base64_decode($input), MCRYPT_MODE_ECB, $this->iv));
    }
    }
   */
  class _GridViewState {
    var $_Grid;
    var $_Data;
    var $_Encrypt = true;
    var $_SaveToSession = false;
    function _Init($_grid) {
      $this->_Grid = $_grid;
      $this->_SaveToSession = $_grid->KeepViewStateInSession;
      $_string = (isset($_POST[$this->_Grid->_UniqueID . "_viewstate"])) ? $_POST[$this->_Grid->_UniqueID . "_viewstate"] : "";
      if ($this->_SaveToSession && $_string == "") {
        $_string = (isset($_SESSION[$this->_Grid->_UniqueID . "_viewstate"])) ? $_SESSION[$this->_Grid->_UniqueID . "_viewstate"] : "";
        $this->_Grid->_MasterTableInstance->_Rebind = true;
      }
      if ($_string != "" && $this->_Encrypt) {
        $_string = base64_decode($_string);
      }
      $_string = _replace("\\", "", $_string);
      $this->_Data = json_decode($_string, true);
    }
    function _Render() {
      if (strnatcmp(phpversion(),'5.4.0') >= 0)
        $_statevalue = json_encode($this->_Data, JSON_UNESCAPED_UNICODE);
      else
        $_statevalue = json_encode($this->_Data);
      if ($this->_Encrypt) {
        $_statevalue = base64_encode($_statevalue);
      }
      if ($this->_SaveToSession) {
        $_SESSION[$this->_Grid->_UniqueID . "_viewstate"] = $_statevalue;
      }
      $_tpl_viewstate = "<input id='{id}' name='{id}' type='hidden' value='{value}' autocomplete='off' />";
      $_viewstate = _replace("{id}", $this->_Grid->_UniqueID . "_viewstate", $_tpl_viewstate);
      $_viewstate = _replace("{value}", $_statevalue, $_viewstate);
      return $_viewstate;
    }
  }
  /* =========================================================================== */
  class _GridLocalization {
    var $_Commands;
    var $_Messages;
    function __construct() {
      $this->_Commands = array(
        "Insert" => "Add New Record",
        "Delete" => "Delete",
        "Confirm" => "Confirm",
        "Edit" => "Edit",
        "Cancel" => "Cancel",
        "Refresh" => "Refresh",
        "Done" => "Done",
        "Loading" => "Loading...",
        "Go" => "Go",
        "Next" => "Next",
        "Prev" => "Prev",
        "Last" => "Last",
        "First" => "First",
        "No_Filter" => "[No Filter]",
        "Equal" => "Equal",
        "Not_Equal" => "Not Equal",
        "Greater_Than" => "Greater Than",
        "Less_Than" => "Less Than",
        "Greater_Than_Or_Equal" => "Greater Than Or Equal",
        "Less_Than_Or_Equal" => "Less Than Or Equal",
        "Contain" => "Contain",
        "Not_Contain" => "Not Contain",
        "Start_With" => "Start With",
        "End_With" => "End With",
        "Filter" => "Filter",
        "Action" => "Action",
        "AddFilter" => "Add Filter",
        "AddFilter" => "Add Filter",
        "RemoveFilter" => "Remove Filter",
      );
      $this->_Messages = array(
        "DeleteConfirm" => "Are you sure to delete this row?",
        "PageInfoTemplate" => "Page <strong>{PageIndex}</onstrong> in <strong>{TotalPages}</strong>, items <strong>{FirstIndexInPage}</strong> to <strong>{LastIndexInPage}</strong> of <strong>{TotalRows}</strong>.",
        "ManualPagerTemplate" => "Change page: {TextBox} (of {TotalPage} pages) {GoPageButton}",
        "PageSizeText" => "Page Size:",
        "PageOverlapText" => "Page Overlap:",
        "NextPageToolTip" => "Next Page",
        "PrevPageToolTip" => "Previous Page",
        "FirstPageToolTip" => "First Page",
        "LastPageToolTip" => "Last Page",
        "SortHeaderToolTip" => "Click here to sort",
        "SortAscToolTip" => "Sort Asc",
        "SortDescToolTip" => "Sort Desc",
        "SortNoneToolTip" => "No sort",
        "Order" => "Order",
        "InsertForm_ConfirmButtonToolTip" => "Confirm Insert",
        "InsertForm_CancelButtonToolTip" => "Cancel Insert",
        "EditForm_ConfirmButtonToolTip" => "Confirm Changes",
        "EditForm_CancelButtonToolTip" => "Cancel Changes",
        "RequiredFieldValidator_ErrorMessage" => "Field is required!",
        "RegularExpressionValidator_ErrorMessage" => "Not valid!",
        "RangeValidator_ErrorMessage" => "Value must be in range [{MinValue},{MaxValue}]",
        "GroupPanelGuideText" => "Drag a column header and drop it here to group by that column",
        "GroupItemToolTip" => "Drag out of the bar to ungroup",
        "VirtualScrollingPageToolTip" => "Page {page_index}"
      );
    }
    function Load($_xml_path) {
      $_xmlDoc = new DOMDocument();
      $_xmlDoc->load($_xml_path);
      $_nodes = $_xmlDoc->getElementsByTagName("commands");
      if ($_nodes->length > 0) {
        foreach ($_nodes->item(0)->attributes as $_attributes) {
          $this->_Commands[$_attributes->name] = $_attributes->value;
        }
      }
      $_nodes = $_xmlDoc->getElementsByTagName("messages");
      if ($_nodes->length > 0) {
        foreach ($_nodes->item(0)->attributes as $_attributes) {
          $this->_Messages[$_attributes->name] = $_attributes->value;
        }
      }
    }
  }
  /* =========================================================================== */
  /* =========================================================================== */
  class _GridCommand {
    var $_UniqueID;
    var $_Grid;
    var $_Commands;
    function __construct($_grid) {
      $this->_UniqueID = $_grid->_UniqueID . "_cmd";
      $this->_LoadCommands();
    }
    function _LoadCommands() {
      if (isset($_POST[$this->_UniqueID])) {
        $_string = $_POST[$this->_UniqueID];
        $_string = base64_decode($_string);
        $_string = _replace("\\", "", $_string);
        $this->_Commands = json_decode($_string, true);
      }
    }
    function _Render() {
      $_tpl_command = "<input id='{id}' name='{id}' type='hidden' value='' />";
      $_command = _replace("{id}", $this->_UniqueID, $_tpl_command);
      return $_command;
    }
  }
  /* =========================================================================== */
  class GridRow implements _IState {
    var $_UniqueID;
    var $_ViewState;
    var $_TableView;
    var $_DetailTableViewInstances = array();
    var $_EditForm;
    var $DataItem;
    var $Selected = false;
    var $Expand = false;
    var $EditMode = false;
    var $TableView;
    var $CssClass = "";
    var $_AlterRow = false;
    function _Init($_tableview) {
      $this->_TableView = $_tableview;
      $this->TableView = $_tableview;
      $this->_ViewState = $_tableview->_ViewState;
    }
    function GetUniqueID() {
      return $this->_UniqueID;
    }
    function _LoadViewState() {
      if (isset($this->_ViewState->_Data[$this->_UniqueID])) {
        $_state = $this->_ViewState->_Data[$this->_UniqueID];
        $this->Selected = $_state["Selected"];
        $this->Expand = $_state["Expand"];
        $this->EditMode = $_state["EditMode"];
        $_dataitem = $_state["DataItem"];
        $this->DataItem = array();
        foreach ($_dataitem as $k => $v) {
          $this->DataItem[$k] = _unescape($v);
        }
      }
    }
    function _SaveViewState() {
      $_dataitem = array();
      foreach ($this->DataItem as $k => $v) {
        $_dataitem[$k] = _escape($v);
      }
      $this->_ViewState->_Data[$this->_UniqueID] = array("Selected" => $this->Selected,
        "Expand" => $this->Expand,
        "DataItem" => $_dataitem,
        "EditMode" => $this->EditMode
      );
      foreach ($this->_DetailTableViewInstances as $_detailtable) {
        $_detailtable->_SaveViewState();
      }
    }
    function _AddDetailTable($_tableview) {
      $_tableview->_UniqueID = $this->_UniqueID . "_dt" . sizeof($this->_DetailTableViewInstances);
      array_push($this->_DetailTableViewInstances, $_tableview);
    }
    function GetInstanceDetailTables() {
      return $this->_DetailTableViewInstances;
    }
    function GetTableView() {
      return $this->_TableView;
    }
    function _Revive() {
      $this->_LoadViewState();
      if ($this->EditMode) {
        $this->_EditForm = $this->_TableView->EditSettings->_CreateInstance();
        $this->_EditForm->_Init($this->_TableView);
        $this->_EditForm->_Row = $this;
      }
      if ($this->Expand) {
        foreach ($this->_TableView->_DetailTables as $_detailtable) {
          $_tableinstance = $_detailtable->_CreateInstance();
          $this->_AddDetailTable($_tableinstance);
          $_tableinstance->_Init($this->_TableView->_Grid, $this);
          $_tableinstance->_Revive();
        }
      }
    }
    function _ProcessCommand($_command) {
      if (isset($_command->_Commands[$this->_UniqueID])) {
        $_c = $_command->_Commands[$this->_UniqueID];
        switch ($_c["Command"]) {
          case "Select":
            $this->Selected = true;
            break;
          case "Unselect":
            $this->Selected = false;
            break;
          case "Expand":
            if ($this->_TableView->_Grid->EventHandler->OnBeforeDetailTablesExpand($this, array()) == true) {
              $this->Expand = true;
              $this->_TableView->_Grid->EventHandler->OnDetailTablesExpand($this, array());
            }
            break;
          case "Collapse":
            if ($this->_TableView->_Grid->EventHandler->OnBeforeDetailTablesCollapse($this, array()) == true) {
              $this->Expand = false;
              $this->_DetailTableViewInstances = array();
              $this->_TableView->_Grid->EventHandler->OnDetailTablesCollapse($this, array());
            }
            break;
          case "StartEdit":
            if ($this->_TableView->AllowEditing) {
              if ($this->_TableView->_Grid->EventHandler->OnBeforeRowStartEdit($this, array()) == true) {
                $this->EditMode = true;
                $this->_EditForm = $this->_TableView->EditSettings->_CreateInstance();
                $this->_EditForm->_Command = "StartEdit";
                $this->_EditForm->_Init($this->_TableView);
                $this->_EditForm->_Row = $this;
                $this->_TableView->_Grid->EventHandler->OnRowStartEdit($this, array());
              }
            }
            break;
          case "ConfirmEdit":
            if ($this->EditMode) {
              $this->_EditForm->_Command = "ConfirmEdit";
              $this->_EditForm->_ProcessUpdateCommand();
            }
            break;
          case "CancelEdit":
            if ($this->_TableView->_Grid->EventHandler->OnBeforeRowCancelEdit($this, array()) == true) {
              $this->EditMode = false;
              $this->_TableView->_Grid->EventHandler->OnRowCancelEdit($this, array());
            }
            break;
          case "Delete":
            if ($this->_TableView->AllowDeleting) {
              if ($this->_TableView->_Grid->EventHandler->OnBeforeRowDelete($this, array()) == true) {
                $_command->_Commands[$this->_UniqueID]["Command"] = "NoMore"; //I have to do this trick to prevent deletion of the next row when KeepRowStateAfterRefresh is set to true.
                $_delete_successful = $this->_TableView->DataSource->Delete($this->DataItem);
                $this->_TableView->_Rebind = true;
                $_error = $this->_TableView->DataSource->GetError();
                if ($_error != "")
                  $this->_TableView->_Grid->EventHandler->OnDataSourceError($this, array("Error" => $_error));
                $this->_TableView->_Grid->EventHandler->OnRowDelete($this, array("Successful" => $_delete_successful, "Error" => $_error));
              }
            }
            break;
        }
      }
      $this->_TableView->_Grid->EventHandler->OnRowPreRender($this, array());
      if ($this->Expand && sizeof($this->_DetailTableViewInstances) == 0) {
        foreach ($this->_TableView->_DetailTables as $_detailtable) {
          $_tableinstance = $_detailtable->_CreateInstance();
          $this->_AddDetailTable($_tableinstance);
          $_tableinstance->_Init($this->_TableView->_Grid, $this);
        }
      }
      foreach ($this->_DetailTableViewInstances as $_detailtable) {
        $_detailtable->_ProcessCommand($_command);
      }
    }
    function _Render() {
      $_tpl_tr = "<tr id='{rowid}' class='kgrRow {alt} {selected} {cssclass}'>{tds}</tr>";
      $_tpl_detailtable_tr = "<tr><td class='kgrCell {alt}'>&#160;</td><td colspan='{colspan}' class='kgrDetailTablesPanel'>{tables}</td></tr>";
      $_tpl_desc = "<div class='kgrDesc'>{text}</div>";
      $_tr = "";
      if ($this->EditMode) {
        $_tr = $this->_EditForm->_Render();
      } else {
        $_tds = "";
        for ($i = 0; $i < sizeof($this->_TableView->_Columns); $i++) {
          $_col = $this->_TableView->_Columns[$i];
          $_td = $_col->_Render($this);
          $_tds.=$_td;
        }
        $_tr = _replace("{tds}", $_tds, $_tpl_tr);
      }
      $_tr = _replace("{rowid}", $this->_UniqueID, $_tr);
      $_tr = _replace("{selected}", $this->Selected ? "kgrRowSelected" : "", $_tr);
      $_tr = _replace("{alt}", $this->_AlterRow ? "kgrAltRow" : "", $_tr);
      $_tr = _replace("{cssclass}", $this->CssClass, $_tr);
      if (sizeof($this->_DetailTableViewInstances) > 0) {
        $_tables = "";
        foreach ($this->_DetailTableViewInstances as $_detailtable) {
          $_desc = "";
          if ($_detailtable->_Description !== null) {
            $_desc = _replace("{text}", $_detailtable->_Description, $_tpl_desc);
            foreach ($this->DataItem as $_k => $_v) {
              $_desc = _replace("{" . $_k . "}", $_v, $_desc);
            }
          }
          $_table = $_desc . $_detailtable->_Render();
          $_tables.=$_table;
        }
        $_detailtable_tr = _replace("{colspan}", sizeof($this->_TableView->_Columns) - 1, $_tpl_detailtable_tr);
        $_detailtable_tr = _replace("{alt}", $this->_AlterRow ? "kgrAltRow" : "", $_detailtable_tr);
        $_detailtable_tr = _replace("{tables}", $_tables, $_detailtable_tr);
        $_tr.=$_detailtable_tr;
      }
      return $_tr;
    }
  }
  /* =========================================================================== */
  class _Style {
    var $Wrap;
    var $Align;
    var $Valign;
    function _Init($_col) {
      if ($this->Wrap === null)
        $this->Wrap = $_col->Wrap;
      if ($this->Align === null)
        $this->Align = $_col->Align;
      if ($this->Valign === null)
        $this->Valign = $_col->Valign;
    }
    function _RenderWrap() {
      return "white-space:" . (($this->Wrap) ? "normal" : "nowrap") . ";";
    }
    function _RenderAlign() {
      return ($this->Align) ? "text-align:" . $this->Align . ";" : "";
    }
    function _RenderVAlign() {
      return ($this->Valign) ? "valign='" . $this->Valign . "' " : "";
    }
  }
  /* =========================================================================== */
  class GridValidator {
    var $ErrorMessage;
    function Validate($_value, $_dataitem = null, $_row = null, $_col = null) {
      return true;
    }
  }
  class RequiredFieldValidator extends GridValidator {
    function Validate($_value, $_dataitem = null, $_row = null, $_col = null) {
      if ($_value === null || $_value == "") {
        if ($this->ErrorMessage === null)
          $this->ErrorMessage = $_col->_TableView->_Grid->Localization->_Messages["RequiredFieldValidator_ErrorMessage"];
        return false;
      }
      return true;
    }
  }
  class RegularExpressionValidator extends GridValidator {
    var $ValidationExpression = "";
    function Validate($_value, $_dataitem = null, $_row = null, $_col = null) {
      if (!preg_match($this->ValidationExpression, $_value)) {
        if ($this->ErrorMessage === null)
          $this->ErrorMessage = $_col->_TableView->_Grid->Localization->_Messages["RegularExpressionValidator_ErrorMessage"];
        return false;
      }
      return true;
    }
  }
  class RangeValidator extends GridValidator {
    var $MinValue;
    var $MaxValue;
    function __construct($_min = null, $_max = null) {
      if ($_min !== null)
        $this->MinValue = $_min;
      if ($_max !== null)
        $this->MaxValue = $_max;
    }
    function Validate($_value, $_dataitem = null, $_row = null, $_col = null) {
      if ($_value > $this->MaxValue || $_value < $this->MinValue) {
        if ($this->ErrorMessage === null)
          $this->ErrorMessage = $_col->_TableView->_Grid->Localization->_Messages["RangeValidator_ErrorMessage"];
        $this->ErrorMessage = _replace("{MinValue}", $this->MinValue, $this->ErrorMessage);
        $this->ErrorMessage = _replace("{MaxValue}", $this->MaxValue, $this->ErrorMessage);
        return false;
      }
      return true;
    }
  }
  class CustomValidator extends GridValidator {
    var $ValidateFunction;
    function Validate($_value, $_dataitem = null, $_row = null, $_col = null) {
      $_func = $this->ValidateFunction;
      if ($_func !== null) {
        $_error_message = $_func($_value);
        if ($_error_message !== null) {
          $this->ErrorMessage = $_error_message;
          return false;
        }
      }
      return true;
    }
  }
  /* =========================================================================== */
  class GridColumn implements _IState {
    var $_UniqueID;
    var $_ViewState;
    var $_TableView;
    var $_Validators;
    var $ReadOnly = false;
    var $TableView;
    var $AllowSorting;
    var $AllowResizing;
    var $AllowFiltering;
    var $AllowGrouping;
    var $AllowExporting = true;
    var $Width;
    var $Visible = true;
    var $Filter;
    var $FilterOptions;
    var $FilterActions;
    var $MultiFilters;
    var $HeaderText;
    var $FooterText;
    var $DataField;
    var $DataFieldPrefix;
    var $Sort = 0; //0|1|-1
    var $SortOrder = 0;
    var $Group = false; //true|false
    var $GroupIndex = 0;
    var $GroupSettings;
    var $HeaderStyle;
    var $ItemStyle;
    var $FooterStyle;
    var $Wrap;
    var $Align;
    var $Valign;
    var $CssClass = "";
    var $DefaultValue = null;
    var $NullDisplayText;
    var $Aggregate; //"None","Min","Max","Sum","First", "Last","Count","Avg"
    var $_AggregateResult;
    var $NullFilter = false;
    function __construct() {
      $this->HeaderStyle = new _Style();
      $this->FooterStyle = new _Style();
      $this->ItemStyle = new _Style();
      $this->_Validators = array();
      $this->GroupSettings = new _GridGroupInstance();
    }
    function GetUniqueID() {
      return $this->_UniqueID;
    }
    function _Init($_tableview) {
      $this->_TableView = $_tableview;
      $this->TableView = $_tableview;
      $this->_ViewState = $_tableview->_ViewState;
      if ($this->AllowSorting === null)
        $this->AllowSorting = $this->_TableView->AllowSorting;
      if ($this->AllowResizing === null)
        $this->AllowResizing = $this->_TableView->AllowResizing;
      if ($this->AllowFiltering === null)
        $this->AllowFiltering = $this->_TableView->AllowFiltering;
      if ($this->AllowGrouping === null)
        $this->AllowGrouping = $this->_TableView->AllowGrouping;
      if ($this->Width === null)
        $this->Width = $this->_TableView->ColumnWidth;
      if ($this->Wrap === null)
        $this->Wrap = $this->_TableView->ColumnWrap;
      if ($this->Align === null)
        $this->Align = $this->_TableView->ColumnAlign;
      if ($this->Valign === null)
        $this->Valign = $this->_TableView->ColumnValign;
      if ($this->FilterOptions === null)
        $this->FilterOptions = $this->_TableView->FilterOptions;
      if ($this->FilterActions === null)
        $this->FilterActions = $this->_TableView->FilterActions;
      $this->HeaderStyle->_Init($this);
      $this->FooterStyle->_Init($this);
      $this->ItemStyle->_Init($this);
      if ($this->Filter === null) {
        $this->Filter = array("Value" => "", "Exp" => "No_Filter");
      }
      if ($this->MultiFilters === null) {
        $this->MultiFilters = array($this->Filter);
      }
      $this->GroupSettings->_Column = $this;
    }
    function AddValidator($_validator) {
      array_push($this->_Validators, $_validator);
    }
    function _LoadViewState() {
      if (isset($this->_ViewState->_Data[$this->_UniqueID])) { //Undefined
        $_state = $this->_ViewState->_Data[$this->_UniqueID];
        $this->Sort = $_state["Sort"];
        $this->SortOrder = $_state["SortOrder"];
        $this->Group = $_state["Group"];
        $this->Width = $_state["Width"];
        $this->Visible = $_state["Visible"];
        $this->_AggregateResult = _unescape($_state["AggRes"]);
        $_filter = $_state["Filter"];
        $_filter["Value"] = _unescape($_filter["Value"]);
        $this->Filter = $_filter;
        $_multiFilters = $_state["MultiFilters"];
        foreach ($_multiFilters as $_f) {
          $_f["Value"] = _unescape($_f["Value"]);
        }
        $this->MultiFilters = $_multiFilters;
      }
    }
    function _SaveViewState() {
      $_filter = $this->Filter;
      $_filter["Value"] = _escape($_filter["Value"]);
      $_multiFilters = $this->MultiFilters;
      foreach ($_multiFilters as $_f) {
        $_f["Value"] = _escape($_f["Value"]);
      }
      $this->_ViewState->_Data[$this->_UniqueID] = array(
        "Name" => $this->DataField,
        "Sort" => $this->Sort,
        "SortOrder" => $this->SortOrder,
        "Group" => $this->Group,
        "Visible" => $this->Visible,
        "Filter" => $_filter,
        "MultiFilters" => $_multiFilters,
        "Width" => $this->Width,
        "AggRes" => _escape($this->_AggregateResult)
      );
    }
    function _ProcessCommand($_command) {
      $_tableView = $this->_TableView;
      $_grid = $_tableView->_Grid;
      $_eventHandler = $_grid->EventHandler;
      if (isset($_command->_Commands[$this->_UniqueID])) {
        $_c = $_command->_Commands[$this->_UniqueID];
        switch ($_c["Command"]) {
          case "Sort":
            if ($_eventHandler->OnBeforeColumnSort($this, array("NewSort" => $_c["Args"]["Sort"])) == true) {
              if ($_tableView->SingleColumnSorting) {
                foreach ($_tableView->_Columns as $_col) {
                  $_col->Sort = 0;
                }
                $_tableView->DataSource->Sorts = array();
              }
              $this->Sort = $_c["Args"]["Sort"];
              $this->SortOrder = $_c["Args"]["SortOrder"];
              $_tableView->_Rebind = true;
              $_eventHandler->OnColumnSort($this, array());
            }
            break;
          /* Remove the grouping in column, this will be done at tableview level.	
            case "Group":
            if($_eventHandler->OnBeforeColumnGroup($this,array())==true)
            {
            $this->Group = $_c["Args"]["Group"];
            $_tableView->_Rebind = true;
            $_eventHandler->OnColumnGroup($this,array());
            }
            break;
           */
          case "Filter":
            if ($_eventHandler->OnBeforeColumnFilter($this, array("FilterValue" => $_c["Args"]["Filter"]["Value"], "FilterExp" => $_c["Args"]["Filter"]["Exp"])) == true) {
              $this->Filter["Exp"] = $_c["Args"]["Filter"]["Exp"];
              if ($_c["Args"]["Post"]) {
                $this->MultiFilters = $this->GetMultiFilters();
                $this->Filter["Value"] = $this->GetFilterValue();
              } else {
                $this->Filter["Value"] = _unescape($_c["Args"]["Filter"]["Value"]); // Unscape the escaped value from client
              }
              $_tableView->_Rebind = true;
              $_eventHandler->OnColumnFilter($this, array());
            }
            break;
          case "AddFilter":
            array_push($this->MultiFilters, array("Value"=>"","Exp"=>"No_Filter"));
            break;
          case "RemoveFilter":
            array_splice($this->MultiFilters, $_c["Args"]["Index"], 1);
            $_tableView->_Rebind = true;
            break;
          case "Group":
            if ($this->Group == false) {
              $_position = $_c["Args"]["Position"];
              if ($_eventHandler->OnBeforeColumnGroup($this, array("Position" => $_position)) == true && $_eventHandler->OnBeforeAddGroup($_tableView, array("Position" => $_position)) == true) {
                $_new_group = $this->GroupSettings;
                $_new_group->_Init($_tableView);
                if ($_position === null || ($_position >= sizeof($this->TableView->_Groups))) {
                  array_push($_tableView->_Groups, $_new_group);
                } else {
                  $_tmp_groups = array();
                  for ($i = 0; $i < sizeof($_tableView->_Groups); $i++) {
                    if ($_position == $i) {
                      array_push($_tmp_groups, $_new_group);
                    }
                    array_push($_tmp_groups, $_tableView->_Groups[$i]);
                  }
                  $_tableView->_Groups = $_tmp_groups;
                }
                for ($i = 0; $i < sizeof($_tableView->_Groups); $i++) {
                  $_tableView->_Groups[$i]->_UniqueID = $_tableView->_UniqueID . "_gm" . $i;
                }
                $this->Group = true;
                $_eventHandler->OnColumnGroup($this, array("Position" => $_position));
                $_eventHandler->OnAddGroup($_tableView, array("Position" => $_position));
                $_tableView->_Rebind = true;
              }
            }            
            break;
          case "UnGroup":
            if ($_eventHandler->OnBeforeColumnRemoveGroup($this, array()) == true && $_eventHandler->OnBeforeRemoveGroup($_tableView, array("GroupField" => $this->DataField)) == true) {
              $_tmp_groups = array();
              for ($i = 0; $i < sizeof($_tableView->_Groups); $i++) {
                if ($_tableView->_Groups[$i]->GroupField != $this->DataField) {
                  array_push($_tmp_groups, $_tableView->_Groups[$i]);
                }
              }
              $_tableView->_Groups = $_tmp_groups;
              for ($i = 0; $i < sizeof($_tableView->_Groups); $i++) {
                $_tableView->_Groups[$i]->_UniqueID = $_tableView->_UniqueID . "_gm" . $i;
              }
              $this->Group = false;
              $_eventHandler->OnColumnRemoveGroup($this, array());
              $_eventHandler->OnRemoveGroup($_tableView, array("GroupField" => $this->DataField));
              $_tableView->_Rebind = true;
            }
            break;
        }
      }
      if ($_tableView->_SortOrderMax < $this->SortOrder)
        $_tableView->_SortOrderMax = $this->SortOrder;
      if ($_tableView->_SortOrderMin > $this->SortOrder)
        $_tableView->_SortOrderMin = $this->SortOrder;
      if ($this->Sort != 0) {
        $_tableView->DataSource->AddSort(new DataSourceSort($this->DataFieldPrefix . $this->DataField, ($this->Sort < 0) ? "DESC" : "ASC", $this->SortOrder));
      }
      /*
        if ($this->Group)
        {
        $_tableView->DataSource->AddGroup(new DataSourceGroup($this->DataField));
        }
       */
      if (count($this->MultiFilters) > 0) {
        foreach ($this->MultiFilters as $_filter) {
          $_DSfilter = new DataSourceFilter($this->DataFieldPrefix . $this->DataField, $_filter["Exp"], $_filter["Value"], $this->NullFilter);
          if ($_filter["Exp"] !== "No_Filter")
            $_tableView->DataSource->AddFilter($_DSfilter);
        }
      }
    }
    function CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new GridColumn();
      }
      $_instance->ReadOnly = $this->ReadOnly;
      $_instance->HeaderText = $this->HeaderText;
      $_instance->FooterText = $this->FooterText;
      $_instance->DataField = $this->DataField;
      $_instance->DataFieldPrefix = $this->DataFieldPrefix;
      $_instance->AllowSorting = $this->AllowSorting;
      $_instance->AllowResizing = $this->AllowResizing;
      $_instance->AllowFiltering = $this->AllowFiltering;
      $_instance->AllowGrouping = $this->AllowGrouping;
      $_instance->AllowExporting = $this->AllowExporting;
      $_instance->Width = $this->Width;
      $_instance->Visible = $this->Visible;
      $_instance->Sort = $this->Sort;
      $_instance->Filter = $this->Filter;
      $_instance->MultiFilters = $this->MultiFilters;
      $_instance->FilterOptions = $this->FilterOptions;
      $_instance->FilterActions = $this->FilterActions;
      $_instance->Wrap = $this->Wrap;
      $_instance->Align = $this->Align;
      $_instance->Valign = $this->Valign;
      $_instance->HeaderStyle = $this->HeaderStyle;
      $_instance->FooterStyle = $this->FooterStyle;
      $_instance->ItemStyle = $this->ItemStyle;
      $_instance->CssClass = $this->CssClass;
      $_instance->_Validators = $this->_Validators;
      $_instance->Aggregate = $this->Aggregate;
      $_instance->DefaultValue = $this->DefaultValue;
      $_instance->NullDisplayText = $this->NullDisplayText;
      $_instance->NullFilter = $this->NullFilter;
      $_instance->Group = $this->Group;
      $_instance->GroupIndex = $this->GroupIndex;
      $_instance->GroupSettings = $this->GroupSettings->_CreateInstance();
      return $_instance;
    }
    function InlineEditRender($_row) {
      return $this->Render($_row);
    }
    function FormEditRender($_row) {
      return $this->InlineEditRender($_row);
    }
    function Format($_value) {
      return mb_check_encoding($_value, 'UTF-8') ? $_value : utf8_encode($_value);
    }
    function Render($_row) {
      return $this->Format($_row->DataItem[$this->DataField]);
    }
    function RenderExport($_row) {
      return $this->Render($_row);
    }
    function _Render($_row) {
      $_tpl_td = "<td id='{id}' class='kgrCell {sorted} {visible} {cssclass} {cssclasses}' {valign}><div class='kgrIn' style='{wrap}{align}'>{cell}</div></td>";
      $_td = _replace("{id}", $_row->_UniqueID . "_" . $this->_UniqueID, $_tpl_td);
      $_agent = strtolower($_SERVER["HTTP_USER_AGENT"]);
      $_isIE6 = (strpos($_agent, "msie 6") !== false) && (strpos($_agent, "msie 7") === false) && (strpos($_agent, "msie 8") === false) && (strpos($_agent, "opera") === false);
      $_isIE7 = (strpos($_agent, "msie 7") !== false) && (strpos($_agent, "opera") === false);
      $_td = _replace("{visible}", ($this->Visible || $_isIE6 || $_isIE7) ? "" : "kgrHidden", $_td);
      $_td = _replace("{wrap}", $this->ItemStyle->_RenderWrap(), $_td);
      $_td = _replace("{align}", $this->ItemStyle->_RenderAlign(), $_td);
      $_td = _replace("{valign}", $this->ItemStyle->_RenderValign(), $_td);
      $_td = _replace("{sorted}", ($this->Sort != 0) ? "kgrSorted" : "", $_td);
      $_td = _replace("{cssclass}", $this->CssClass, $_td);
      $_cssClasses = $this->_TableView->CssClasses;
      $_td = _replace("{cssclasses}", isset($_cssClasses['cell']) ? $_cssClasses['cell'] : '', $_td);
      if ($this->DataField !== null && $this->NullDisplayText !== null) {
        $_td = _replace("{cell}", ($_row->DataItem[$this->DataField] === null) ? $this->NullDisplayText : $this->Render($_row), $_td);
      } else {
        $_td = _replace("{cell}", $this->Render($_row), $_td);
      }
      return $_td;
    }
    function _InlineEditRender($_row) {
      $_tpl_td = "<td id='{id}' class='kgrCell {visible} {cssclasses}' {valign}><div class='kgrIn' style='{wrap}{align}' >{cell}</div></td>";
      $_td = _replace("{id}", $_row->_UniqueID . "_" . $this->_UniqueID, $_tpl_td);
      $_td = _replace("{cell}", $this->InlineEditRender($_row), $_td);
      $_agent = strtolower($_SERVER["HTTP_USER_AGENT"]);
      $_isIE6 = (strpos($_agent, "msie 6") !== false) && (strpos($_agent, "msie 7") === false) && (strpos($_agent, "msie 8") === false) && (strpos($_agent, "opera") === false);
      $_isIE7 = (strpos($_agent, "msie 7") !== false) && (strpos($_agent, "opera") === false);
      $_td = _replace("{visible}", ($this->Visible || $_isIE6 || $_isIE7) ? "" : "kgrHidden", $_td);
      $_td = _replace("{wrap}", $this->ItemStyle->_RenderWrap(), $_td);
      $_td = _replace("{align}", $this->ItemStyle->_RenderAlign(), $_td);
      $_td = _replace("{valign}", $this->ItemStyle->_RenderValign(), $_td);
      $_cssClasses = $this->_TableView->CssClasses;
      $_td = _replace("{cssclasses}", isset($_cssClasses['cell']) ? $_cssClasses['cell'] : '', $_td);
      return $_td;
    }
    function _FormEditRender($_row) {
      $_tpl_tr = "<tr style='white-space:nowrap'><td valign='top' style='width:2px;'><label class='kgrCaption' for='{id}'>{text}:</label></td><td valign='top'><div class='kgrInput'>{input}</div></td></tr>";
      $_tr = _replace("{text}", $this->HeaderText, $_tpl_tr);
      $_tr = _replace("{id}", $_row->_UniqueID . "_" . $this->_UniqueID . "_input", $_tr);
      $_tr = _replace("{input}", $this->FormEditRender($_row), $_tr);
      return $_tr;
    }
    function GetEditValue($_row) {
      $_value = $_POST[$_row->_UniqueID . "_" . $this->_UniqueID . "_input"];
      if ($this->_TableView->_Grid->AjaxEnabled === true) {
        $_value = iconv("UTF-8", $this->_TableView->_Grid->CharSet, $_value);
      }
      return $_value;
    }
    function _RenderCol() {
      $_tpl_col = "<col id='{id}' name='{id}' style='{width}' class='{resizable} {visible} {groupable}'/>";
      $_col = _replace("{id}", $this->_UniqueID, $_tpl_col);
      $_col = _replace("{resizable}", ($this->AllowResizing) ? "kgrResizable" : "", $_col);
      $_col = _replace("{groupable}", ($this->AllowGrouping) ? "kgrGroupable" : "", $_col);
      $_col = _replace("{width}", ($this->Width != null) ? "width:" . $this->Width . ";" : "", $_col);
      $_col = _replace("{visible}", ($this->Visible) ? "" : "kgrHidden", $_col);
      return $_col;
    }
    function RenderHeader() {
      $_tableView = $this->_TableView;
      $_localMsgs = $_tableView->_Grid->Localization->_Messages;
      $_tpl_th = "<th id='{id}' class='kgrHeader {visible} {sorted} {cssclasses}' {valign}><div class='kgrIn' style='{wrap}{align}'>{text}&#160;{sign}</div></th>";
      $_tpl_sort_a = "<a href='javascript:void 0' class='kgrSortHeaderText' onclick='grid_sort(\"{id}\",{sort}, {sortOrder})' title='{title}'>{text}</a>";
      $_tpl_sort_sign = "<input type='button' class='nodecor kgrSort{dir}' onclick='grid_sort(\"{id}\",{sort}, {sortOrder})' title='{title}' />";
      $_th = _replace("{id}", $this->_UniqueID . "_hd", $_tpl_th);
      if ($this->AllowSorting) {
        $_sort = 0;
        $_sortOrder = $_tableView->_SortOrderMax + 1;
        $_dir = "None";
        switch ($this->Sort) {
          case 0:
            $_sort = 1;
            $_dir = "None";
            break;
          case 1:
            $_sort = -1;
            $_dir = "Asc";
            break;
          case -1:
            $_sort = 0;
            $_dir = "Desc";
            break;
        }
        $_sort_a = _replace("{id}", $this->_UniqueID, $_tpl_sort_a);
        $_sort_a = _replace("{sort}", $_sort, $_sort_a);
        $_sort_a = _replace("{sortOrder}", $_sortOrder, $_sort_a);
        $_sort_a = _replace("{text}", $this->HeaderText, $_sort_a);
        $_sort_a = _replace("{title}", $_localMsgs["SortHeaderToolTip"], $_sort_a);
        $_sort_sign = _replace("{id}", $this->_UniqueID, $_tpl_sort_sign);
        $_sort_sign = _replace("{sort}", $_sort, $_sort_sign);
        $_sort_sign = _replace("{sortOrder}", $_sortOrder, $_sort_sign);
        $_sort_sign = _replace("{dir}", $_dir, $_sort_sign);
        $_sort_sign = _replace("{title}", $_localMsgs["Sort" . $_dir . "ToolTip"] . ($this->Sort !== 0 ? ' ' . $_localMsgs["Order"] . ' ' . $this->SortOrder : ''), $_sort_sign);
        $_th = _replace("{text}", $_sort_a, $_th);
        $_th = _replace("{sign}", $_sort_sign, $_th);
      } else {
        $_th = _replace("{text}", $this->HeaderText, $_th);
        $_th = _replace("{sign}", "", $_th);
      }
      $_agent = strtolower($_SERVER["HTTP_USER_AGENT"]);
      $_isIE6 = (strpos($_agent, "msie 6") !== false) && (strpos($_agent, "msie 7") === false) && (strpos($_agent, "msie 8") === false) && (strpos($_agent, "opera") === false);
      $_isIE7 = (strpos($_agent, "msie 7") !== false) && (strpos($_agent, "opera") === false);
      $_th = _replace("{visible}", ($this->Visible || $_isIE6 || $_isIE7) ? "" : "kgrHidden", $_th);
      $_th = _replace("{sorted}", ($this->Sort != 0) ? "kgrSorted" : "", $_th);
      $_th = _replace("{wrap}", $this->HeaderStyle->_RenderWrap(), $_th);
      $_th = _replace("{align}", $this->HeaderStyle->_RenderAlign(), $_th);
      $_th = _replace("{valign}", $this->HeaderStyle->_RenderValign(), $_th);
      $_cssClasses = $this->_TableView->CssClasses;
      $_th = _replace("{cssclasses}", isset($_cssClasses['header']) ? $_cssClasses['header'] : '', $_th);
      return $_th;
    }
    function RenderFooter() {
      $_tpl_footer_td = "<td id='{id}' class='kgrFooter {visible}' {valign}><div class='kgrIn' style='{wrap}{align}'><span class='kgrFooterText'>{text}&#160;</span></div></td>";
      $_footer_td = _replace("{id}", $this->_UniqueID . "_ft", $_tpl_footer_td);
      $_text = $this->FooterText;
      if ($this->Aggregate !== null) {
        $_dump_row = new GridRow();
        $_dump_row->DataItem[$this->DataField] = $this->_AggregateResult;
        $_text .= $this->Render($_dump_row);
      }
      $_footer_td = _replace("{text}", $_text, $_footer_td);
      $_agent = strtolower($_SERVER["HTTP_USER_AGENT"]);
      $_isIE6 = (strpos($_agent, "msie 6") !== false) && (strpos($_agent, "msie 7") === false) && (strpos($_agent, "msie 8") === false) && (strpos($_agent, "opera") === false);
      $_isIE7 = (strpos($_agent, "msie 7") !== false) && (strpos($_agent, "opera") === false);
      $_footer_td = _replace("{visible}", ($this->Visible || $_isIE6 || $_isIE7) ? "" : "kgrHidden", $_footer_td);
      $_footer_td = _replace("{wrap}", $this->FooterStyle->_RenderWrap(), $_footer_td);
      $_footer_td = _replace("{align}", $this->FooterStyle->_RenderAlign(), $_footer_td);
      $_footer_td = _replace("{valign}", $this->FooterStyle->_RenderValign(), $_footer_td);
      return $_footer_td;
    }
    function RenderFilter($_filter = null) {
      if (! $_filter)
        $_filter = $this->Filter;
      $_tpl_input = "<div class='kgrEditIn'><input class='kgrFiEnTr' type='text' id='{id}' name='{id}[]' value='{text}' onblur='grid_filter_trigger(\"{colid}\",this)' style='width:100%;' /></div>";
      $_input = _replace("{id}", $this->_UniqueID . "_filter_input", $_tpl_input);
      $_input = _replace("{colid}", $this->_UniqueID, $_input);
      $_input = _replace("{text}", _quotes_encode($_filter["Value"]), $_input);
      return $_input;
    }
    function GetMultiFilters() {
      $_filter_input_name = $this->_UniqueID . "_filter_input";
      $_filter_select_name = $this->_UniqueID . "_filter_select";
      $_filter_inputs = $_POST[$_filter_input_name];
      $_filter_selects = $_POST[$_filter_select_name];
      $_filters = array();
      foreach ($_filter_inputs as $k=>$_filter_input) {
        $_filter_input = $this->GetFilterValue($_filter_input);
        array_push($_filters, array(
          "Value" => $_filter_input,
          "Exp" => $_filter_selects[$k]
        ));
      }
      return $_filters;
    }
    function GetFilterValue($_value = null) {
      if ($_value === null) {
        $_value = $_POST[$this->_UniqueID . "_filter_input"][0];
      }
      if ($this->_TableView->_Grid->AjaxEnabled === true) {
        $_value = iconv("UTF-8", $this->_TableView->_Grid->CharSet, $_value);
      }
      return $_value;
    }
    function _RenderFilter() {
      $_tpl_filter = "<td id='{id}' class='kgrFilterCell {visible} {cssclasses}'>{content}</td>";
      $_filter_content = "&#160;";
      $_agent = strtolower($_SERVER["HTTP_USER_AGENT"]);
      $_isIE6 = (strpos($_agent, "msie 6") !== false) && (strpos($_agent, "msie 7") === false) && (strpos($_agent, "msie 8") === false) && (strpos($_agent, "opera") === false);
      $_isIE7 = (strpos($_agent, "msie 7") !== false) && (strpos($_agent, "opera") === false);
      $_filter_string = _replace("{id}", $this->_UniqueID . "_flt", $_tpl_filter);
      $_filter_string = _replace("{visible}", ($this->Visible || $_isIE6 || $_isIE7) ? "" : "kgrHidden", $_filter_string);
      if ($this->AllowFiltering) {
        $_tpl_filter_content = "<div class='kgrIn'><div>{input}</div><div>{select}</div></div>";
        $_tpl_select = "<select id='{id}' name='{id}[]' onchange='grid_filter_trigger(\"{colid}\",this)' style='width:100%'><optgroup label='" . $this->_TableView->_Grid->Localization->_Commands['Filter'] . "'>{options}</optgroup>{FilterActions}</select>";
        $_tpl_filter_actions = "<optgroup label='" . $this->_TableView->_Grid->Localization->_Commands['Action'] . "'>{options}</optgroup>";
        $_tpl_option = "<option value='{value}' {selected} >{text}</option>";
        $_list_options = $this->FilterOptions;
        $_filter_actions = $this->FilterActions;
        $_filters = $this->MultiFilters;
        foreach ($_filters as $_filter) {
          $_options = "";
          for ($i = 0; $i < sizeof($_list_options); $i++) {
            $_option = _replace("{value}", $_list_options[$i], $_tpl_option);
            $_option = _replace("{text}", $this->_TableView->_Grid->Localization->_Commands[$_list_options[$i]], $_option);
            $_option = _replace("{selected}", ($_filter["Exp"] == $_list_options[$i]) ? "selected" : "", $_option);
            $_options.=$_option;
          }
          $_select = _replace("{id}", $this->_UniqueID . "_filter_select", $_tpl_select);
          $_select = _replace("{colid}", $this->_UniqueID, $_select);
          $_select = _replace("{options}", $_options, $_select);
          $_str_filter_actions = "";
          if (count($_filter_actions) > 0) {
            $_str_filter_actions = $_tpl_filter_actions;
            $_options = "";
            for ($i = 0; $i < sizeof($_filter_actions); $i++) {
              $_option = _replace("{value}", $_filter_actions[$i], $_tpl_option);
              $_option = _replace("{text}", $this->_TableView->_Grid->Localization->_Commands[$_filter_actions[$i]], $_option);
              $_options .= $_option;
            }
            $_str_filter_actions = _replace("{options}", $_options, $_str_filter_actions);
          }
          $_select = _replace("{FilterActions}", $_str_filter_actions, $_select);
          $_filter_content = _replace("{select}", $_select, $_tpl_filter_content);
          $_filter_content = _replace("{input}", $this->RenderFilter($_filter), $_filter_content);
          $_filter_string = _replace("{content}", $_filter_content . "{content}", $_filter_string);
        }
      }
      $_filter_string = _replace("{content}", "", $_filter_string);
      $_cssClasses = $this->_TableView->CssClasses;
      $_filter_string = _replace("{cssclasses}", isset($_cssClasses['filter cell']) ? $_cssClasses['filter cell'] : '', $_filter_string);
      return $_filter_string;
    }
  }
  class _GridTextInputColumn extends GridColumn {
    var $AllowHtmlRender = false;
    function Render($_row) {
      $_ct = $this->Format($_row->DataItem[$this->DataField]);
      if (!$this->AllowHtmlRender) {
        $_ct = _replace("<", "&#60;", $_ct);
        $_ct = _replace(">", "&#62;", $_ct);
      }
      return $_ct;
    }
    function CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new _GridTextInputColumn();
      }
      parent::CreateInstance($_instance);
      $_instance->AllowHtmlRender = $this->AllowHtmlRender;
      return $_instance;
    }
  }
  class GridBoundColumn extends _GridTextInputColumn {
    var $MaxLength = -1;
    var $_lengthErrorMessage = '';
    var $_lengthValidator = null;
    function _Init($_tableview) {
      parent::_Init($_tableview);
      if ($this->MaxLength > -1) {
        $validator = new RegularExpressionValidator();
        $validator->ValidationExpression = "/^.{0,$this->MaxLength}$/"; // Only accept integer.
        if (!empty($this->_lengthErrorMessage))
          $validator->ErrorMessage = $this->_lengthErrorMessage;
        else
          $validator->ErrorMessage = "Please enter less than or equal to $this->MaxLength characters.";
        $this->AddValidator($validator);
        $this->_lengthValidator = $validator;
      }
    }
    function setLengthErrorMessage($s) {
      $this->_lengthErrorMessage = $s;
      if (isset($this->_lengthValidator))
        $this->_lengthValidator->ErrorMessage = $s;
    }
    function InlineEditRender($_row) {
      if (!$this->ReadOnly) {
        $_tpl_cell = "<div class='kgrEditIn kgrECap'><input id='{id}' class='kgrEnNoPo' name='{id}' type='text' value='{value}' {maxlength}  style='width:100%' /></div>"; 
        $_cell = _replace("{id}", $_row->_UniqueID . "_" . $this->_UniqueID . "_input", $_tpl_cell);
        $_cell = _replace("{maxlength}", $this->MaxLength > -1 ? "maxlength=$this->MaxLength" : "" , $_cell);
        $_cell = _replace("{value}", _quotes_encode($this->Format($_row->DataItem[$this->DataField])), $_cell);
        return $_cell;
      } else {
        return $this->Render($_row);
      }
    }
    function FormEditRender($_row) {
      $_tpl_cell = "<input id='{id}' class='kgrEnNoPo' name='{id}' type='text' value='{value}' {maxlength} style='width:90%' />";
      $_cell = _replace("{id}", $_row->_UniqueID . "_" . $this->_UniqueID . "_input", $_tpl_cell);
      $_cell = _replace("{maxlength}", $this->MaxLength > -1 ? "maxlength=$this->MaxLength" : "" , $_cell);
      $_cell = _replace("{value}", _quotes_encode($this->Format($_row->DataItem[$this->DataField])), $_cell);
      return $_cell;
    }
    function CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new GridBoundColumn();
      }
      parent::CreateInstance($_instance);
      $_instance->MaxLength = $this->MaxLength;
      $_instance->_lengthErrorMessage = $this->_lengthErrorMessage;
      return $_instance;
    }
  }
  class GridCalculatedColumn extends GridBoundColumn {
    var $Expression;
    function _Init($_tableview) {
      parent::_Init($_tableview);
      $this->ReadOnly = true;
      $this->Aggregate = null;
    }
    function Render($_row) {
      $_expression = $this->Expression;
      foreach ($_row->DataItem as $_k => $_v) {
        $_expression = _replace("{" . $_k . "}", $_v, $_expression);
      }
      return eval("return " . $_expression . ";");
    }
    function CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new GridCalculatedColumn();
      }
      parent::CreateInstance($_instance);
      $_instance->Expression = $this->Expression;
      return $_instance;
    }
  }
  class GridCalculatedCustomColumn extends GridCalculatedColumn {
    var $InlineEditExpression;
    function InlineEditRender($_row) {
      $_expression = $this->InlineEditExpression;
      foreach ($_row->DataItem as $_k => $_v) {
        $_expression = _replace("{" . $_k . "}", $_v, $_expression);
      }
      return eval("return " . $_expression . ";");
    }
    function CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new GridCalculatedCustomColumn();
      }
      parent::CreateInstance($_instance);
      $_instance->InlineEditExpression = $this->InlineEditExpression;
      return $_instance;
    }
  }
  class GridNumberColumn extends GridBoundColumn {
    var $DecimalNumber = 0;
    var $DecimalPoint = ".";
    var $ThousandSeperate = ",";
    var $FormatString = "{n}";
    function Format($_value) {
      $_number = (float) $_value;
      return _replace("{n}", number_format($_number, $this->DecimalNumber, $this->DecimalPoint, $this->ThousandSeperate), $this->FormatString);
    }
    function Render($_row) {
      $_ct = $this->Format($_row->DataItem[$this->DataField]);
      if (!$this->AllowHtmlRender) {
        $_ct = _replace("<", "&#60;", $_ct);
        $_ct = _replace(">", "&#62;", $_ct);
      }
      return $_ct;
    }
    function InlineEditRender($_row) {
      if (!$this->ReadOnly) {
        $_tpl_cell = "<div class='kgrEditIn kgrECap'><input id='{id}' class='kgrEnNoPo' name='{id}' type='text' value='{value}' style='width:100%' /></div>";
        $_cell = _replace("{id}", $_row->_UniqueID . "_" . $this->_UniqueID . "_input", $_tpl_cell);
        $_ct = $this->Format($_row->DataItem[$this->DataField]);
        $_cell = _replace("{value}", $_ct, $_cell);
        return $_cell;
      } else {
        return $this->Render($_row);
      }
    }
    function FormEditRender($_row) {
      $_tpl_cell = "<input id='{id}' class='kgrEnNoPo' name='{id}' type='text' value='{value}' style='width:90%' />";
      $_cell = _replace("{id}", $_row->_UniqueID . "_" . $this->_UniqueID . "_input", $_tpl_cell);
      $_ct = $this->Format($_row->DataItem[$this->DataField]);
      $_cell = _replace("{value}", $_ct , $_cell);
      return $_cell;
    }
    function GetEditValue($_row) {
      $_value = $_POST[$_row->_UniqueID . "_" . $this->_UniqueID . "_input"];
      $_value = floatval(str_replace($this->DecimalPoint, '.', preg_replace('/[^\d'.preg_quote($this->DecimalPoint).']/', '', $_value)));
      return $_value;
    }
    function CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new GridNumberColumn();
      }
      parent::CreateInstance($_instance);
      $_instance->DecimalNumber = $this->DecimalNumber;
      $_instance->DecimalPoint = $this->DecimalPoint;
      $_instance->ThousandSeperate = $this->ThousandSeperate;
      $_instance->FormatString = $this->FormatString;
      return $_instance;
    }
  }
  class GridCurrencyColumn extends GridBoundColumn {
    var $Locale = "en_US";
    var $FormatString = "%i";
    function Format($_value) {
      setlocale(LC_MONETARY, $this->Locale);
      return money_format($this->FormatString, $_value);
    }
    function CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new GridCurrencyColumn();
      }
      parent::CreateInstance($_instance);
      $_instance->Locale = $this->Locale;
      $_instance->FormatString = $this->FormatString;
      return $_instance;
    }
  }
  class GridTextAreaColumn extends _GridTextInputColumn {
    var $BoxHeight;
    function InlineEditRender($_row) {
      if (!$this->ReadOnly) {
        $_tpl_cell = "<div class='kgrEditIn kgrECap'><textarea id='{id}' class='kgrEnNoPo' name='{id}' style='width:100%;{height}'>{value}</textarea></div>";
        $_cell = _replace("{id}", $_row->_UniqueID . "_" . $this->_UniqueID . "_input", $_tpl_cell);
        $_cell = _replace("{value}", _quotes_encode($this->Format($_row->DataItem[$this->DataField])), $_cell);
        $_cell = _replace("{height}", ($this->BoxHeight) ? "height:" . $this->BoxHeight . ";" : "", $_cell);
        return $_cell;
      } else {
        return $this->Render($_row);
      }
    }
    function FormEditRender($_row) {
      $_tpl_cell = "<textarea id='{id}' class='kgrEnNoPo' name='{id}' style='width:90%;{height}'>{value}</textarea>";
      $_cell = _replace("{id}", $_row->_UniqueID . "_" . $this->_UniqueID . "_input", $_tpl_cell);
      $_cell = _replace("{value}", _quotes_encode($this->Format($_row->DataItem[$this->DataField])), $_cell);
      $_cell = _replace("{height}", ($this->BoxHeight) ? "height:" . $this->BoxHeight . ";" : "", $_cell);
      return $_cell;
    }
    function CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new GridTextAreaColumn();
      }
      parent::CreateInstance($_instance);
      $_instance->BoxHeight = $this->BoxHeight;
      return $_instance;
    }
  }
  class GridDropDownColumn extends GridColumn {
    var $_Items = array();
    function Render($_row) {
      $_value = $_row->DataItem[$this->DataField];
      $_text = $_row->DataItem[$this->DataField];
      for ($i = 0; $i < sizeof($this->_Items); $i++) {
        if ($_value == $this->_Items[$i][1]) {
          $_text = $this->_Items[$i][0];
          break;
        }
      }
      return $_text;
    }
    function AddItem($_text, $_value = null) {
      if ($_value === null)
        $_value = $_text;
      array_push($this->_Items, array($_text, $_value));
    }
    function Clear() {
      $this->_Items = array();
    }
    function _RenderEditTemplate($_row, $_tpl_select) {
      $_tpl_option = "<option value='{value}' {selected}>{text}</option>";
      $_options = "";
      foreach ($this->_Items as $_item) {
        $_option = _replace("{text}", $_item[0], $_tpl_option);
        $_option = _replace("{value}", _quotes_encode($_item[1]), $_option);
        $_option = _replace("{selected}", ($_item[1] == $_row->DataItem[$this->DataField]) ? "selected" : "", $_option);
        $_options.=$_option;
      }
      $_select = _replace("{id}", $_row->_UniqueID . "_" . $this->_UniqueID . "_input", $_tpl_select);
      $_select = _replace("{options}", $_options, $_select);
      return $_select;
    }
    function InlineEditRender($_row) {
      if (!$this->ReadOnly) {
        $_tpl_select = "<span class='kgrECap'><select id='{id}' name='{id}' style='width:100%'>{options}</select></span>";
        return $this->_RenderEditTemplate($_row, $_tpl_select);
      } else {
        return $this->Render($_row);
      }
    }
    function FormEditRender($_row) {
      $_tpl_select = "<select id='{id}' name='{id}' style='width:90%'>{options}</select>";
      return $this->_RenderEditTemplate($_row, $_tpl_select);
    }
    function RenderFilter($_filter = null) {
      if (! $_filter)
        $_filter = $this->Filter;
      $_tpl_select = "<span class='kgrECap'><select id='{id}' name='{id}[]' style='width:100%' onchange='grid_filter_trigger(\"{colid}\",this)'>{options}</select></span>";
      $_tpl_option = "<option value='{value}' {selected}>{text}</option>";
      $_options = "";
      foreach ($this->_Items as $_item) {
        $_option = _replace("{text}", $_item[0], $_tpl_option);
        $_option = _replace("{value}", $_item[1], $_option);
        $_option = _replace("{selected}", ($_item[1] == $_filter["Value"]) ? "selected" : "", $_option);
        $_options.=$_option;
      }
      $_select = _replace("{id}", $this->_UniqueID . "_filter_input", $_tpl_select);
      $_select = _replace("{colid}", $this->_UniqueID, $_select);
      $_select = _replace("{options}", $_options, $_select);
      return $_select;
    }
    function CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new GridDropDownColumn();
      }
      parent::CreateInstance($_instance);
      $_instance->_Items = $this->_Items;
      return $_instance;
    }
  }
  class GridRowSelectColumn extends GridColumn {
    var $Align = "center";
    function _Init($_tableview) {
      parent::_Init($_tableview);
      $this->AllowSorting = false;
      $this->AllowResizing = false;
      $this->AllowFiltering = false;
      $this->AllowGrouping = false;
      $this->AllowExporting = false;
      $this->ReadOnly = true;
      $this->Aggregate = null;
    }
    function Render($_row) {
      $_tpl_check = "<span class='kgrECap'><input type='checkbox' class='kgrSelectSingleRow' {checked} onclick='grid_toggle_select(this)' /></span>";
      $_check = _replace("{checked}", $_row->Selected ? "checked" : "", $_tpl_check);
      return $_check;
    }
    function _RenderCol() {
      $_tpl_col = "<col id='{id}' name='{id}' style='{width}' class='kgrColumnSelect {resizable} {visible}'/>";
      $_col = _replace("{id}", $this->_UniqueID, $_tpl_col);
      $_col = _replace("{resizable}", ($this->AllowResizing) ? "kgrResizable" : "", $_col);
      $_col = _replace("{width}", ($this->Width != null) ? "width:" . $this->Width . ";" : "", $_col);
      $_col = _replace("{visible}", ($this->Visible) ? "" : "kgrHidden", $_col);
      return $_col;
    }
    function RenderHeader() {
      $_tpl_check = "<span class='kgrECap'><input type='checkbox' class='kgrSelectAllRows' {checked} onclick='grid_toggle_select(this)' /></span>";
      $_rows = $this->_TableView->_Rows;
      $_selected = true;
      for ($i = 0; $i < sizeof($_rows); $i++) {
        if (!$_rows[$i]->Selected) {
          $_selected = false;
          break;
        }
      }
      $this->HeaderText = _replace("{checked}", $_selected ? "checked" : "", $_tpl_check);
      return parent::RenderHeader();
    }
    function CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new GridRowSelectColumn();
      }
      parent::CreateInstance($_instance);
      return $_instance;
    }
  }
  class GridBooleanColumn extends GridColumn {
    var $TrueText = "True";
    var $FalseText = "False";
    var $UseCheckBox = false;
    var $FilterActions = array();
    function Render($_row) {
      $_cell = "";
      if ($this->UseCheckBox) {
        $_tpl_cell = "<input type='checkbox' {checked} disabled />";
        $_cell = _replace("{checked}", ($_row->DataItem[$this->DataField]) ? "checked" : "", $_tpl_cell);
      } else {
        $_cell = ($_row->DataItem[$this->DataField]) ? $this->TrueText : $this->FalseText;
      }
      return $_cell;
    }
    function InlineEditRender($_row, $_is_form_render = false) {
      if (!$this->ReadOnly) {
        $_cell = "";
        if ($this->UseCheckBox) {
          $_tpl_cell = "<span class='kgrECap'><input  id='{id}' name='{id}' type='checkbox' {checked} /></span>";
          $_cell = _replace("{id}", $_row->_UniqueID . "_" . $this->_UniqueID . "_input", $_tpl_cell);
          $_cell = _replace("{checked}", ($_row->DataItem[$this->DataField]) ? "checked" : "", $_cell);
        } else {
          $_tpl_select = "<span class='kgrECap'><select id='{id}' name='{id}' style='width:{width}'>{options}</select></span>";
          $_tpl_option = "<option value='{value}' {selected}>{text}</option>";
          $_true_option = _replace("{value}", "1", $_tpl_option);
          $_true_option = _replace("{selected}", ($_row->DataItem[$this->DataField]) ? "selected" : "", $_true_option);
          $_true_option = _replace("{text}", $this->TrueText, $_true_option);
          $_false_option = _replace("{value}", "0", $_tpl_option);
          $_false_option = _replace("{selected}", (!$_row->DataItem[$this->DataField]) ? "selected" : "", $_false_option);
          $_false_option = _replace("{text}", $this->FalseText, $_false_option);
          $_cell = _replace("{id}", $_row->_UniqueID . "_" . $this->_UniqueID . "_input", $_tpl_select);
          $_cell = _replace("{options}", $_true_option . $_false_option, $_cell);
          $_cell = _replace("{width}", ($_is_form_render) ? "90%" : "100%", $_cell);
        }
        return $_cell;
      } else {
        return $this->Render($_row);
      }
    }
    function FormEditRender($_row) {
      return $this->InlineEditRender($_row, true);
    }
    function GetEditValue($_row) {
      if ($this->UseCheckBox) {
        return isset($_POST[$_row->_UniqueID . "_" . $this->_UniqueID . "_input"]) ? 1 : 0;
      } else {
        return parent::GetEditValue($_row);
      }
    }
    function RenderFilter($_filter = null) {
      if (! $_filter)
        $_filter = $this->Filter;
      $_cell = "";
      if ($this->UseCheckBox) {
        $_tpl_cell = "<input  id='{id}' name='{id}' type='checkbox' {checked} onchange='grid_filter_trigger(\"{colid}\",this)' /></span>";
        $_cell = _replace("{id}", $this->_UniqueID . "_filter_input", $_tpl_cell);
        $_cell = _replace("{colid}", $this->_UniqueID, $_cell);
        $_cell = _replace("{checked}", ($_filter["Value"]) ? "checked" : "", $_cell);
      } else {
        $_tpl_select = "<span class='kgrECap'><select id='{id}' name='{id}' style='width:100%' onchange='grid_filter_trigger(\"{colid}\",this)'>{options}</select></span>";
        $_tpl_option = "<option value='{value}' {selected}>{text}</option>";
        $_true_option = _replace("{value}", "1", $_tpl_option);
        $_true_option = _replace("{selected}", ($_filter["Value"]) ? "selected" : "", $_true_option);
        $_true_option = _replace("{text}", $this->TrueText, $_true_option);
        $_false_option = _replace("{value}", "0", $_tpl_option);
        $_false_option = _replace("{selected}", (!$_filter["Value"]) ? "selected" : "", $_false_option);
        $_false_option = _replace("{text}", $this->FalseText, $_false_option);
        $_cell = _replace("{id}", $this->_UniqueID . "_filter_input", $_tpl_select);
        $_cell = _replace("{colid}", $this->_UniqueID, $_cell);
        $_cell = _replace("{options}", $_true_option . $_false_option, $_cell);
      }
      return $_cell;
    }
    function GetMultiFilters() {
      $_filter_input_name = $this->_UniqueID . "_filter_input";
      $_filter_select_name = $this->_UniqueID . "_filter_select";
      $_filter_input = isset($_POST[$_filter_input_name]) ? 1 : 0;
      $_filter_select = $_POST[$_filter_select_name][0];
      $_filters = array(array(
        "Value" => $this->GetFilterValue($_filter_input),
        "Exp" => $_filter_select
      ));
      return $_filters;
    }
    function GetFilterValue($_value = null) {
      if ($this->UseCheckBox) {
        if ($_value === null) {
          $_value = isset($_POST[$this->_UniqueID . "_filter_input"]) ? 1 : 0;
        }
        return $_value;
      } else {
        return parent::GetFilterValue();
      }
    }
    function CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new GridBooleanColumn();
      }
      parent::CreateInstance($_instance);
      $_instance->TrueText = $this->TrueText;
      $_instance->FalseText = $this->FalseText;
      $_instance->UseCheckBox = $this->UseCheckBox;
      return $_instance;
    }
  }
  class GridImageColumn extends GridColumn {
    var $ImageFolder = "";
    var $CssClass = "";
    function Render($_row) {
      $_tpl_cell = "<img src='{src}' class='{class}' alt='' />";
      $_cell = _replace("{src}", (($this->ImageFolder != "") ? ($this->ImageFolder . "/") : "") . $_row->DataItem[$this->DataField], $_tpl_cell);
      $_cell = _replace("{class}", $this->CssClass, $_cell);
      return $_cell;
    }
    function InlineEditRender($_row) {
      return $this->Render($_row);
    }
    function CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new GridImageColumn();
      }
      parent::CreateInstance($_instance);
      $_instance->ImageFolder = $this->ImageFolder;
      $_instance->CssClass = $this->CssClass;
      return $_instance;
    }
  }
  class GridCustomColumn extends GridColumn {
    var $ItemTemplate;
    var $EditItemTemplate;
    var $AllowSorting = false;
    function _Init($_tableview) {
      parent::_Init($_tableview);
      $this->ReadOnly = true;
    }
    function Render($_row) {
      $_cell = $this->ItemTemplate;
      foreach ($_row->DataItem as $_k => $_v) {
        $_cell = _replace("{" . $_k . "}", $_v, $_cell);
      }
      return $_cell;
    }
    function InlineEditRender($_row) {
      $_cell = $this->EditItemTemplate;
      foreach ($_row->DataItem as $_k => $_v) {
        $_cell = _replace("{" . $_k . "}", $_v, $_cell);
      }
      return $_cell;
    }
    function CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new GridCustomColumn();
      }
      parent::CreateInstance($_instance);
      $_instance->ItemTemplate = $this->ItemTemplate;
      $_instance->EditItemTemplate = $this->EditItemTemplate;
      return $_instance;
    }
  }
  class GridCommandColumn extends GridColumn {
    var $CommandText = "Command";
    var $OnClick = "";
    var $CssClass = "";
    function _Init($_tableview) {
      parent::_Init($_tableview);
      $this->AllowSorting = false;
      $this->AllowFiltering = false;
      $this->AllowGrouping = false;
      $this->AllowExporting = false;
      $this->ReadOnly = true;
      $this->Aggregate = null;
    }
    function Render($_row) {
      $_tpl_cell = "<span class='kgrECap'><input type='button' class='{class}' value='{text}' onclick='{onclick}' /></span>";
      $_cell = _replace("{class}", $this->CssClass, $_tpl_cell);
      $_text = $this->CommandText;
      $_onclick = $this->OnClick;
      foreach ($_row->DataItem as $_k => $_v) {
        $_text = _replace("{" . $_k . "}", $_v, $_text);
        $_onclick = _replace("{" . $_k . "}", $_v, $_onclick);
      }
      $_cell = _replace("{text}", $_text, $_cell);
      $_cell = _replace("{onclick}", $_onclick, $_cell);
      return $_cell;
    }
    function InlineEditRender($_row) {
      return $this->Render($_row);
    }
    function CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new GridCommandColumn();
      }
      parent::CreateInstance($_instance);
      $_instance->CommandText = $this->CommandText;
      $_instance->OnClick = $this->OnClick;
      $_instance->CssClass = $this->CssClass;
      return $_instance;
    }
  }
  class GridEditDeleteColumn extends GridColumn {
    var $ButtonType = "Auto"; //"Link"|"Image"|"Button"
    var $EditButtonText;
    var $DeleteButtonText;
    var $ConfirmButtonText;
    var $CancelButtonText;
    var $EditButtonImageUrl = "";
    var $DeleteButtonImageUrl = "";
    var $ConfirmButtonImageUrl = "";
    var $CancelButtonImageUrl = "";
    var $EditButtonCssClass = "";
    var $ConfirmButtonCssClass = "";
    var $CancelButtonCssClass = "";
    var $DeleteButtonCssClass = "";
    var $ShowEditButton = true;
    var $ShowDeleteButton = true;
    function _Init($_tableview) {
      parent::_Init($_tableview);
      $this->AllowSorting = false;
      $this->AllowResizing = false;
      $this->AllowFiltering = false;
      $this->AllowGrouping = false;
      $this->AllowExporting = false;
      $this->ReadOnly = true;
      $this->Aggregate = null;
      if ($this->EditButtonText === null)
        $this->EditButtonText = $_tableview->_Grid->Localization->_Commands["Edit"];
      if ($this->DeleteButtonText === null)
        $this->DeleteButtonText = $_tableview->_Grid->Localization->_Commands["Delete"];
      if ($this->ConfirmButtonText === null)
        $this->ConfirmButtonText = $_tableview->_Grid->Localization->_Commands["Confirm"];
      if ($this->CancelButtonText === null)
        $this->CancelButtonText = $_tableview->_Grid->Localization->_Commands["Cancel"];
    }
    function Render($_row) {
      $_tpl_col = "<span class='kgrECap'>{edit} {delete}</span>";
      $_tpl_button = "";
      switch (strtolower($this->ButtonType)) {
        case "auto":
          $_tpl_col = "<span class='kgrECap' style='white-space:nowrap;'>{edit}{delete}</span>";
          $_tpl_button = "<a type='button' class='{autoclass} {class}' onclick='{onclick}' href='javascript:void 0'>{text}</a>";
          break;
        case "link":
          $_tpl_button = "<a type='button' class='{class}' onclick='{onclick}' href='javascript:void 0'>{text}</a>";
          break;
        case "image":
          $_tpl_button = "<img src='{src}' onclick='{onclick}' class='{class}' />";
          break;
        case "button":
        default:
          $_tpl_button = "<input class='{class}' type='button' value='{text}' onclick='{onclick}' />";
          break;
      }
      $_edit_button = _replace("{text}", $this->EditButtonText, $_tpl_button);
      $_edit_button = _replace("{autoclass}", "kgrLinkEdit", $_edit_button);
      $_edit_button = _replace("{class}", $this->EditButtonCssClass, $_edit_button);
      $_edit_button = _replace("{src}", $this->EditButtonImageUrl, $_edit_button);
      $_edit_button = _replace("{onclick}", "grid_edit(this)", $_edit_button);
      $_delete_button = _replace("{text}", $this->DeleteButtonText, $_tpl_button);
      $_delete_button = _replace("{autoclass}", "kgrLinkDelete", $_delete_button);
      $_delete_button = _replace("{class}", $this->DeleteButtonCssClass, $_delete_button);
      $_delete_button = _replace("{src}", $this->DeleteButtonImageUrl, $_delete_button);
      $_delete_button = _replace("{onclick}", "grid_delete(this)", $_delete_button);
      $_col = _replace("{edit}", ($this->ShowEditButton) ? $_edit_button : "", $_tpl_col);
      $_col = _replace("{delete}", ($this->ShowDeleteButton) ? $_delete_button : "", $_col);
      return $_col;
    }
    function InlineEditRender($_row) {
      if ($this->ShowEditButton) {
        $_tpl_col = "<span class='kgrECap'>{confirm} {cancel}</span>";
        $_tpl_button = "";
        switch (strtolower($this->ButtonType)) {
          case "auto":
            $_tpl_col = "<span class='kgrECap' style='white-space:nowrap;'>{confirm}{cancel}</span>";
            $_tpl_button = "<a type='button' class='{autoclass} {class}' onclick='{onclick}' href='javascript:void 0'>{text}</a>";
            break;
          case "link":
            $_tpl_button = "<a type='button' class='{class}' onclick='{onclick}' href='javascript:void 0'>{text}</a>";
            break;
          case "image":
            $_tpl_button = "<input type='image' src='{src}' onclick='{onclick}' class='{class}' />";
            break;
          case "button":
          default:
            $_tpl_button = "<input class='{class}' type='button' value='{text}' onclick='{onclick}' />";
            break;
        }
        $_confirm_button = _replace("{text}", $this->ConfirmButtonText, $_tpl_button);
        $_confirm_button = _replace("{autoclass}", "kgrLinkConfirm", $_confirm_button);
        $_confirm_button = _replace("{class}", $this->ConfirmButtonCssClass, $_confirm_button);
        $_confirm_button = _replace("{src}", $this->ConfirmButtonImageUrl, $_confirm_button);
        $_confirm_button = _replace("{onclick}", "grid_confirm_edit(this)", $_confirm_button);
        $_cancel_button = _replace("{text}", $this->CancelButtonText, $_tpl_button);
        $_cancel_button = _replace("{autoclass}", "kgrLinkCancel", $_cancel_button);
        $_cancel_button = _replace("{class}", $this->CancelButtonCssClass, $_cancel_button);
        $_cancel_button = _replace("{src}", $this->CancelButtonImageUrl, $_cancel_button);
        $_cancel_button = _replace("{onclick}", "grid_cancel_edit(this)", $_cancel_button);
        $_col = _replace("{confirm}", $_confirm_button, $_tpl_col);
        $_col = _replace("{cancel}", $_cancel_button, $_col);
        return $_col;
      } else {
        return $this->Render($_row);
      }
    }
    function CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new GridEditDeleteColumn();
      }
      parent::CreateInstance($_instance);
      $_instance->ButtonType = $this->ButtonType;
      $_instance->ReadOnly = $this->ReadOnly;
      $_instance->EditButtonText = $this->EditButtonText;
      $_instance->DeleteButtonText = $this->DeleteButtonText;
      $_instance->ConfirmButtonText = $this->ConfirmButtonText;
      $_instance->CancelButtonText = $this->CancelButtonText;
      $_instance->EditButtonImageUrl = $this->EditButtonImageUrl;
      $_instance->DeleteButtonImageUrl = $this->DeleteButtonImageUrl;
      $_instance->ConfirmButtonImageUrl = $this->ConfirmButtonImageUrl;
      $_instance->CancelButtonImageUrl = $this->CancelButtonImageUrl;
      $_instance->EditButtonCssClass = $this->EditButtonCssClass;
      $_instance->DeleteButtonCssClass = $this->DeleteButtonCssClass;
      $_instance->ConfirmButtonCssClass = $this->ConfirmButtonCssClass;
      $_instance->CancelButtonCssClass = $this->CancelButtonCssClass;
      $_instance->ShowEditButton = $this->ShowEditButton;
      $_instance->ShowDeleteButton = $this->ShowDeleteButton;
      return $_instance;
    }
  }
  class GridExpandDetailColumn extends GridColumn {
    function _Init($_tableview) {
      parent::_Init($_tableview);
      $this->AllowSorting = false;
      $this->AllowResizing = false;
      $this->AllowFiltering = false;
      $this->AllowGrouping = false;
      $this->AllowExporting = false;
      $this->ReadOnly = true;
      $this->Aggregate = null;
    }
    function Render($_row) {
      $_tpl_col = "<span class='kgr{status} kgrECap' onclick='grid_{command}(this)'> </span>";
      $_col = _replace("{status}", ($_row->Expand) ? "Expand" : "Collapse", $_tpl_col);
      $_col = _replace("{command}", ($_row->Expand) ? "collapse" : "expand", $_col);
      return $_col;
    }
    function _RenderCol() {
      $_tpl_col = "<col id='{id}' name='{id}' style='{width}' class='kgrColumnExpand {resizable} {visible}'/>";
      $_col = _replace("{id}", $this->_UniqueID, $_tpl_col);
      $_col = _replace("{resizable}", ($this->AllowResizing) ? "kgrResizable" : "", $_col);
      $_col = _replace("{width}", ($this->Width != null) ? "width:" . $this->Width . ";" : "", $_col);
      $_col = _replace("{visible}", ($this->Visible) ? "" : "kgrHidden", $_col);
      return $_col;
    }
    function CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new GridExpandDetailColumn();
      }
      parent::CreateInstance($_instance);
      return $_instance;
    }
  }
  class _GridGroupColumn extends GridColumn {
    function _Init($_tableview) {
      parent::_Init($_tableview);
      $this->AllowSorting = false;
      $this->AllowResizing = false;
      $this->AllowFiltering = false;
      $this->AllowGrouping = false;
      $this->ReadOnly = true;
      $this->Aggregate = null;
      $this->AllowExporting = false;
    }
    function _RenderCol() {
      $_tpl_col = "<col id='{id}' name='{id}' style='{width}' class='kgrColumnGroup'/>";
      $_col = _replace("{id}", $this->_UniqueID, $_tpl_col);
      $_col = _replace("{width}", ($this->Width != null) ? "width:" . $this->Width . ";" : "", $_col);
      return $_col;
    }
    function _Render($_row) {
      $_tpl_td = "<td id='{id}' class='kgrCell kgrGroupCol'><div class='kgrIn' style=''>&#160;</div></td>";
      $_td = _replace("{id}", $_row->_UniqueID . "_" . $this->_UniqueID, $_tpl_td);
      return $_td;
    }
    function CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new _GridGroupColumn();
      }
      parent::CreateInstance($_instance);
      return $_instance;
    }
  }
  class GridDateTimeColumn extends GridColumn {
    var $Picker;
    var $FormatString;
    var $DatabaseFormatString = "Y-m-d H:i:s";
    var $EmptyDate;
    function _Init($_tableview) {
      parent::_Init($_tableview);
      if ($this->FormatString === null) {
        $this->FormatString = "m/d/Y g:i A";
        if ($this->Picker !== null) {
          switch (strtolower(get_class($this->Picker))) {
            case "kooldatetimepicker":
              $this->FormatString = $this->Picker->DateFormat . " " . $this->Picker->TimeFormat;
              break;
            case "kooldatepicker":
              $this->FormatString = $this->Picker->DateFormat;
              break;
            case "kooltimepicker":
              $this->FormatString = $this->Picker->TimeFormat;
              break;
          }
        }
      }
    }
    function InlineEditRender($_row) {
      if (!$this->ReadOnly) {
        $_datetime_string = $_row->DataItem[$this->DataField];
        $_datetime = strtotime($_datetime_string);
        $_is_date_null = ($_datetime == null || mb_strpos($_datetime_string, '0000-00-00') !== false);
        if ($this->Picker !== null) {
          $_edit_formatstring = "m/d/Y g:i A";
          switch (strtolower(get_class($this->Picker))) {
            case "kooldatetimepicker":
              $_edit_formatstring = $this->Picker->DateFormat . " " . $this->Picker->TimeFormat;
              break;
            case "kooldatepicker":
              $_edit_formatstring = $this->Picker->DateFormat;
              break;
            case "kooltimepicker":
              $_edit_formatstring = $this->Picker->TimeFormat;
              break;
          }
          $this->Picker->id = $_row->_UniqueID . "_" . $this->_UniqueID . "_input";
          $this->Picker->Width = "100%";
          $this->Picker->ClientEvents = array();
          if ($this->_TableView->AllowScrolling == true) {
            $this->Picker->ClientEvents["OnBeforeDatePickerOpen"] = $this->Picker->id . "_onbeforeopen";
            $this->Picker->ClientEvents["OnBeforeTimePickerOpen"] = $this->Picker->id . "_onbeforeopen";
            $this->Picker->ClientEvents["OnDatePickerClose"] = $this->Picker->id . "_onclose";
            $this->Picker->ClientEvents["OnTimePickerClose"] = $this->Picker->id . "_onclose";
          }
          $this->Picker->Init();
          if ($_is_date_null) {
            $this->Picker->Value = "";
          } else {
            $this->Picker->Value = date($_edit_formatstring, $_datetime);
          }
          $_tpl_cell = "<div class='kgrECap'>{picker}{js_edit_overflow}</div>";
          if ($this->_TableView->AllowScrolling == true) {
            $_tpl_cell = "<div class='kgrECap'>{js_init_openclose}<div class='kgrDateTimePickerOut'><div class='kgrDateTimePickerIn'>{picker}</div></div>{js_edit_overflow}</div>";
          }
          $_tpl_js_init_openclose = "<script type='text/javascript'>function {id}_onbeforeopen(){grid_on_datetimepicker_open('{id}');return true;} function {id}_onclose(){grid_on_datetimepicker_close('{id}');} </script>";
          $_tpl_js_edit_overflow = "<script type='text/javascript'>document.getElementById('{id}').className+=' kgrEnNoPo';var _agent=navigator.userAgent.toLowerCase();if(!((_agent.indexOf('msie 6')!=-1 || _agent.indexOf('msie 7')!=-1)&&_agent.indexOf('msie 8')==-1 &&_agent.indexOf('opera')==-1)){document.getElementById('{id}_bound').parentNode.parentNode.style.overflow='visible';}</script>";
          $_js_edit_overflow = _replace("{id}", $this->Picker->id, $_tpl_js_edit_overflow);
          $_js_init_openclose = _replace("{id}", $this->Picker->id, $_tpl_js_init_openclose);
          $_cell = _replace("{picker}", $this->Picker->Render(), $_tpl_cell);
          $_cell = _replace("{js_init_openclose}", $_js_init_openclose, $_cell);
          $_cell = _replace("{js_edit_overflow}", $_js_edit_overflow, $_cell);
          return $_cell;
        } else {
          if ($_is_date_null) {
            $_display_value = "";
          } else {
            $_display_value = date($this->FormatString, $_datetime);
          }
          $_tpl_cell = "<div class='kgrEditIn kgrECap'><input id='{id}' class='kgrEnNoPo' name='{id}' type='text' value='{value}' style='width:100%' /></div>";
          $_cell = _replace("{id}", $_row->_UniqueID . "_" . $this->_UniqueID . "_input", $_tpl_cell);
          $_cell = _replace("{value}", ($_datetime_string != "") ? _quotes_encode($_display_value) : "", $_cell);
          return $_cell;
        }
      } else {
        return $this->Render($_row);
      }
    }
    function FormEditRender($_row) {
      $_datetime_string = $_row->DataItem[$this->DataField];
      $_datetime = strtotime($_datetime_string);
      $_is_date_null = ($_datetime == null || mb_strpos($_datetime_string, '0000-00-00') !== false);
      if ($this->Picker !== null) {
        $_edit_formatstring = "m/d/Y g:i A";
        switch (strtolower(get_class($this->Picker))) {
          case "kooldatetimepicker":
            $_edit_formatstring = $this->Picker->DateFormat . " " . $this->Picker->TimeFormat;
            break;
          case "kooldatepicker":
            $_edit_formatstring = $this->Picker->DateFormat;
            break;
          case "kooltimepicker":
            $_edit_formatstring = $this->Picker->TimeFormat;
            break;
        }
        $this->Picker->id = $_row->_UniqueID . "_" . $this->_UniqueID . "_input";
        $this->Picker->Width = "90%";
        $this->Picker->ClientEvents = array();
        $this->Picker->Init();
        if ($_is_date_null) {
          $_display_value = "";
        } else {
          $_display_value = date($_edit_formatstring, $_datetime);
        }
        $this->Picker->Value = $_display_value;
        $_tpl_cell = "<div class='kgrECap'>{picker}{js_edit_overflow}</div>";
        $_tpl_js_edit_overflow = "<script type='text/javascript'>document.getElementById('{id}').className+=' kgrEnNoPo';</script>";
        $_js_edit_overflow = _replace("{id}", $this->Picker->id, $_tpl_js_edit_overflow);
        $_cell = _replace("{picker}", $this->Picker->Render(), $_tpl_cell);
        $_cell = _replace("{js_edit_overflow}", $_js_edit_overflow, $_cell);
        return $_cell;
      } else {
        if ($_is_date_null) {
          $_display_value = "";
        } else {
          $_display_value = date($_edit_formatstring, $_datetime);
        }
        $_tpl_cell = "<div class='kgrEditIn kgrECap'><input id='{id}' class='kgrEnNoPo' name='{id}' type='text' value='{value}' style='width:90%' /></div>";
        $_cell = _replace("{id}", $_row->_UniqueID . "_" . $this->_UniqueID . "_input", $_tpl_cell);
        $_cell = _replace("{value}", ($_datetime_string != "") ? _quotes_encode($_display_value) : "", $_cell);
        return $_cell;
      }
    }
    function RenderFilter($_filter = null) {
      if ($_filter === null)
        $_filter = $this->Filter;
      $_datetime_string = $_filter["Value"];
      $_datetime = strtotime($_datetime_string);
      if ($this->Picker !== null) {
        $_edit_formatstring = "m/d/Y g:i A";
        switch (strtolower(get_class($this->Picker))) {
          case "kooldatetimepicker":
            $_edit_formatstring = $this->Picker->DateFormat . " " . $this->Picker->TimeFormat;
            break;
          case "kooldatepicker":
            $_edit_formatstring = $this->Picker->DateFormat;
            break;
          case "kooltimepicker":
            $_edit_formatstring = $this->Picker->TimeFormat;
            break;
        }
        $_id = $this->Picker->id = $this->_UniqueID . "_filter_input";
        $this->Picker->Width = "100%";
        if ($this->_TableView->AllowScrolling == true) {
          $this->Picker->ClientEvents["OnBeforeDatePickerOpen"] = $this->Picker->id . "_onbeforeopen";
          $this->Picker->ClientEvents["OnBeforeTimePickerOpen"] = $this->Picker->id . "_onbeforeopen";
          $this->Picker->ClientEvents["OnDatePickerClose"] = $this->Picker->id . "_onclose";
          $this->Picker->ClientEvents["OnTimePickerClose"] = $this->Picker->id . "_onclose";
        }
        $this->Picker->ClientEvents["OnSelect"] = $this->Picker->id . "_onselect";
        $this->Picker->Init();        
        $this->Picker->Value = ($_datetime_string != "" && $_datetime_string != $this->EmptyDate ) ? date($_edit_formatstring, $_datetime) : "";
        $_tpl_cell = "<div class='kgrECap'>{picker}{js_init_onselect}</div>";
        if ($this->_TableView->AllowScrolling == true) {
          $_tpl_cell = "<div class='kgrECap'><div class='kgrDateTimePickerOut'><div class='kgrDateTimePickerIn'>{js_init_openclose}{picker}{js_init_onselect}</div></div></div>";
        }
        $_tpl_js_init_onselect = "<script type='text/javascript'>function {id}_onselect(){grid_filter_trigger(\"{colid}\",document.getElementById('{id}'))};var _input = document.getElementById('{id}');_input.className+='kgrFiEnTr'; var _agent=navigator.userAgent.toLowerCase();if(!((_agent.indexOf('msie 6')!=-1 || _agent.indexOf('msie 7')!=-1)&&_agent.indexOf('msie 8')==-1 &&_agent.indexOf('opera')==-1)){document.getElementById('{id}_bound').parentNode.parentNode.parentNode.style.overflow='visible';}</script>";
        $_tpl_js_init_openclose = "<script type='text/javascript'>function {id}_onbeforeopen(){grid_on_datetimepicker_open('{id}');return true;} function {id}_onclose(){grid_on_datetimepicker_close('{id}');}</script>";
        $_js_init_onselect = _replace("{id}", $this->Picker->id, $_tpl_js_init_onselect);
        $_js_init_onselect = _replace("{colid}", $this->_UniqueID, $_js_init_onselect);
        $_js_init_openclose = _replace("{id}", $this->Picker->id, $_tpl_js_init_openclose);
        $_cell = _replace("{picker}", $this->Picker->Render(), $_tpl_cell);
        $_cell = _replace("name='$_id'", "name='$_id" . "[]'", $_cell);
        $_cell = _replace("name=\"$_id\"", "name=\"$_id" . "[]\"", $_cell);
        $_cell = _replace("{js_init_onselect}", $_js_init_onselect, $_cell);
        $_cell = _replace("{js_init_openclose}", $_js_init_openclose, $_cell);
        return $_cell;
      } 
      else {
        $_tpl_input = "<div class='kgrEditIn'><input class='kgrFiEnTr' type='text' id='{id}' name='{id}[]' value='{text}' onblur='grid_filter_trigger(\"{colid}\",this)' style='width:100%;' /></div>";
        $_input = _replace("{id}", $this->_UniqueID . "_filter_input", $_tpl_input);
        $_input = _replace("{colid}", $this->_UniqueID, $_input);
        $_input = _replace("{text}", ($_datetime_string != "") ? _quotes_encode(date($this->FormatString, $_datetime)) : "", $_input);
        return $_input;
      }
    }
    function Render($_row) {
      $_datetime_string = $_row->DataItem[$this->DataField];
      $_datetime = strtotime($_datetime_string);
      $_is_date_null = ($_datetime == null || mb_strpos($_datetime_string, '0000-00-00') !== false);
      if ($_is_date_null) {
        return $this->NullDisplayText;
      } else {
        $_value = date($this->FormatString, $_datetime);
        return $_value;
      }
    }
    function GetEditValue($_row) {
      $_inputId = $_row->_UniqueID . "_" . $this->_UniqueID . "_input";
      $_str = _slash_decode($_POST[$_inputId]);
      if (trim($_str) == "") {
        return $this->EmptyDate;
      } else {
        $_edit_formatstring = $this->FormatString . '|';
        if ($this->Picker !== null) {
          switch (strtolower(get_class($this->Picker))) {
            case "kooldatetimepicker":
              $_edit_formatstring = $this->Picker->DateFormat . " " . $this->Picker->TimeFormat;
              break;
            case "kooldatepicker":
              $_edit_formatstring = $this->Picker->DateFormat . '|';
              break;
            case "kooltimepicker":
              $_edit_formatstring = $this->Picker->TimeFormat;
              break;
          }
        }
        if (strnatcmp(phpversion(),'5.3.7') >= 0 && $_edit_formatstring !== "") {
          $_datetime = DateTime::createFromFormat($_edit_formatstring, $_str);
          if ($_datetime)
            $_datetime = $_datetime->getTimestamp();
          else
            $_datetime = strtotime($_str);
        }
        else {
          $_datetime = strtotime($_str);
        }
        $str = date($this->DatabaseFormatString, $_datetime);
        return $str;
      }
    }
    function GetFilterValue($_value = null) {
      if (! $_value)
        $_value = _slash_decode($_POST[$this->_UniqueID . "_filter_input"][0]);
      if (trim($_value) == "") {
        return $this->EmptyDate;
      } else {
        $_edit_formatstring = $this->FormatString . '|';
        if ($this->Picker !== null) {
          switch (strtolower(get_class($this->Picker))) {
            case "kooldatetimepicker":
              $_edit_formatstring = $this->Picker->DateFormat . " " . $this->Picker->TimeFormat;
              break;
            case "kooldatepicker":
              $_edit_formatstring = $this->Picker->DateFormat . '|';
              break;
            case "kooltimepicker":
              $_edit_formatstring = $this->Picker->TimeFormat;
              break;
          }
        }
        if (strnatcmp(phpversion(),'5.3.7') >= 0 && $_edit_formatstring !== "") {
          $_datetime = DateTime::createFromFormat($_edit_formatstring, $_value);
          if ($_datetime)
            $_datetime = $_datetime->getTimestamp();
          else
            $_datetime = strtotime($_value);
        }
        else {
          $_datetime = strtotime($_value);
        }
        $_date = date($this->DatabaseFormatString, $_datetime);
        return $_date;
      }
    }
    function CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new GridDateTimeColumn();
      }
      parent::CreateInstance($_instance);
      $_instance->Picker = $this->Picker;
      $_instance->FormatString = $this->FormatString;
      $_instance->DatabaseFormatString = $this->DatabaseFormatString;
      $_instance->EmptyDate = $this->EmptyDate;
      return $_instance;
    }
  }
  class GridAutoCompleteColumn extends GridColumn {
    var $serviceFunction = '';
    var $itemTemplate = '{text}';
    var $saveTemplate = '';
    var $defaultSave = '';
    var $footerTemplate = '';
    var $KoolAutoCompleteFolder = '';
    var $HiddenDataField = '';
    var $ClientEvents = array();
    function CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new GridAutoCompleteColumn();
      }
      parent::CreateInstance($_instance);
      $_instance->serviceFunction = $this->serviceFunction;
      $_instance->itemTemplate = $this->itemTemplate;
      $_instance->saveTemplate = $this->saveTemplate;
      $_instance->defaultSave = $this->defaultSave;
      $_instance->footerTemplate = $this->footerTemplate;
      $_instance->KoolAutoCompleteFolder = $this->KoolAutoCompleteFolder;
      $_instance->HiddenDataField = $this->HiddenDataField;
      $_instance->ClientEvents = $this->ClientEvents;
      return $_instance;
    }
    function _EditRender($_row) {
      $_id = $_row->_UniqueID . "_" . $this->_UniqueID . "_input";
      $_value = $_row->DataItem[$this->DataField];
      $_s = '';
      $_s .= "<input type=text id='$_id' name='$_id' value='$_value' autocomplete='off' style='width:90%'>";
      if (!empty($this->HiddenDataField)) {
        $hidden_data = isset($_row->DataItem[$this->HiddenDataField]) ?
            $_row->DataItem[$this->HiddenDataField] : $this->defaultSave;
        $_hidden_id = $_row->_UniqueID . "_" . $this->_UniqueID . "_hidden_input";
        $_s .= "<input type=hidden id='$_hidden_id' name='$_hidden_id' value='$hidden_data'>";
      }
      $_kacId = "kac_" . $_row->_UniqueID . "_" . $this->_UniqueID;
      $_kac = new KoolAutoComplete($_kacId);
      $_kac->styleFolder = $this->KoolAutoCompleteFolder . 'styles/default';
      $_kac->attachTo = $_id;
      if (!empty($_hidden_id)) {
        $_kac->saveTo = $_hidden_id;
        $_kac->saveTemplate = $this->saveTemplate;
        $_kac->defaultSave = $this->defaultSave;
      }
      $_kac->serviceFunction = $this->serviceFunction;
      $_kac->itemTemplate = $this->itemTemplate;
      $_kac->footerTemplate = $this->footerTemplate;
      $_kac->ClientEvents = $this->ClientEvents;
      $_s .= $_kac->Render();
      return $_s;
    }
    function InlineEditRender($_row) {
      return $this->_EditRender($_row);
    }
    function FormEditRender($_row) {
      return $this->_EditRender($_row);
    }
    function GetHiddenEditValue($_row) {
      $_value = $_POST[$_row->_UniqueID . "_" . $this->_UniqueID . "_hidden_input"];
      if ($this->_TableView->_Grid->AjaxEnabled === true) {
        $_value = iconv("UTF-8", $this->_TableView->_Grid->CharSet, $_value);
      }
      return $_value;
    }
    function RenderFilter($_filter = null) {
      if (! $_filter)
        $_filter = $this->Filter;
      $_id = $this->_UniqueID . "_filter_input";
      $_tpl_input = "<div class='kgrEditIn'><input class='kgrFiEnTr' type='text' id='{id}' name='{id}[]' value='{text}' onblur='grid_filter_trigger(\"{colid}\",this)' autocomplete='off' style='width:100%;' /></div>";
      $_input = _replace("{id}", $_id, $_tpl_input);
      $_input = _replace("{colid}", $this->_UniqueID, $_input);
      $_input = _replace("{text}", _quotes_encode($_filter["Value"]), $_input);
      $_kacId = "kac_" . $_id;
      $_kac = new KoolAutoComplete($_kacId);
      $_kac->styleFolder = $this->KoolAutoCompleteFolder . 'styles/default';
      $_kac->attachTo = $_id;
      $_kac->serviceFunction = $this->serviceFunction;
      $_kac->itemTemplate = $this->itemTemplate;
      $_kac->footerTemplate = $this->footerTemplate;
      $_kac->ClientEvents = $this->ClientEvents;
      $_kac->ClientEvents['OnSelectAndClose'] = 'grid_autocomplete_filter_trigger';
      $_kac->ClientEvents['OnBlurAndClose'] = 'grid_autocomplete_filter_trigger';
      $_input .= $_kac->Render();
      return $_input;
    }
  }
  class GridOptionsColumn extends GridColumn {
    var $ChoiceType = 'Checkboxes'; // "Checkboxes" | "RadioButtons"
    var $Options = array();
    var $DefaultOptions = array();
    var $Delimiter = ',';
    function AddOption($_s) {
      if (is_string($_s))
        $_options = explode($this->Delimiter, $_s);
      else if (is_array($_s))
        $_options = $_s;
      $this->Options = array_merge($this->Options, $_options);
      return $this;
    }
    function AddDefaultOption($_s) {
      if (is_string($_s))
        $_options = explode($this->Delimiter, $_s);
      else if (is_array($_s))
        $_options = $_s;
      $this->DefaultOptions = array_merge($this->DefaultOptions, $_options);
      return $this;
    }
    function CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new GridOptionsColumn();
      }
      parent::CreateInstance($_instance);
      $_instance->ChoiceType = $this->ChoiceType;
      $_instance->Options = $this->Options;
      $_instance->DefaultOptions = $this->DefaultOptions;
      $_instance->Delimiter = $this->Delimiter;
      return $_instance;
    }
    function Render($_row) {
      $_items = $_row->DataItem[$this->DataField];
      $_items = explode($this->Delimiter, $_items);
      $_str_list = "<ul style='none'>{item}</ul>";
      $_tpl_item = "<li>{name}</li>";
      foreach ($_items as $_item) {
        $_str_item = str_replace("{name}", $_item, $_tpl_item);
        $_str_list = str_replace("{item}", $_str_item . "{item}", $_str_list);
      }
      $_str_list = str_replace("{item}", "", $_str_list);
      return $_str_list;
    }
    function FormEditRender($_row) {
      $_input_id = $_row->_UniqueID . "_" . $this->_UniqueID . "_input";
      $_items = $_row->DataItem[$this->DataField];
      if (empty($_items)) 
        $_items = $this->DefaultOptions;
      else
        $_items = explode($this->Delimiter, $_items);
      $_s = "<div>{choice}</div>";
      $_choiceType = $this->ChoiceType === 'RadioButtons' ? 'radio' : 'checkbox';
      $_tpl_item = "<label class='option-column'><input class='option-column' type='$_choiceType' value='{option}' name='{id}[]' {checked} />{option}</label>";
      foreach ($this->Options as $_option) {
        $_str_item = str_replace("{option}", $_option, $_tpl_item);
        $_str_item = str_replace("{id}", $_input_id, $_str_item);
        $_str_item = str_replace("{checked}", in_array($_option, $_items) ? 
            "checked" : "", $_str_item);
        $_s = str_replace("{choice}", $_str_item . "{choice}", $_s);
      }
      $_s = str_replace("{choice}", "", $_s);
      return $_s;
    }
    function InlineEditRender($_row) {
      return $this->FormEditRender($_row);
    }
    function GetEditValue($_row) {
      $_input_id = $_row->_UniqueID . "_" . $this->_UniqueID . "_input";
      $_items = $_POST[$_input_id];
      $_items = implode($this->Delimiter, $_items);
      return $_items;
    }
  }
  class GridFileColumn extends GridColumn {
    var $BaseDirectory;
    var $TableName;
    var $IdColumn;
    var $UploadHandlePage;
    var $MustHaveFiles;
    var $MaxFileSize = 10000000;
    var $AllowedExtension = "gif,jpg,doc,pdf,txt";
    var $AllowDelete = false;
    var $AllowDownload = true;
    var $AutoUpload = false;
    var $InitialShownFiles = 3;
    var $ShowDescription = false;
    var $MultipleUpload = true;
    var $KoolUploaderFolder = 'kooluploader';
    function _getFileExtension($_filename) {
      $_ext = substr(strrchr($_filename, '.'), 1);
      $_extToType = array(
        'pdf' => 'pdf_2-16',
        'zip' => 'zip_2-16',
        'rar' => 'zip_2-16',
        '7z' => 'zip_2-16',
        'doc' => 'word_2-16',
        'docx' => 'word_2-16',
        'jpg' => 'image_2-16',
        'jpeg' => 'image_2-16',
        'png' => 'image_2-16',
        'xls' => 'excel_2-16',
        'xlsx' => 'excel_2-16',
        'ppt' => 'powerpoint_2-16',
        'pptx' => 'powerpoint_2-16',
        'txt' => 'text_2-16'
      );
      if (array_key_exists($_ext, $_extToType))
        $_css = $_extToType[$_ext];
      else
        $_css = 'file_2-16';
      return $_css;
    }
    function Render($_row) {
      $_id = 'ul_' . $_row->_UniqueID . "_" . $this->_UniqueID;
      $_s = "<ul class='kgrFileList' id='$_id' >";
      $_l = "<span class='koolphpfileformat {class}'></span>";
      $_l .=  $this->AllowDownload ? 
          "<a href='{filepath}' download>{display}</a>" : "{display}";
      $_files = $_row->DataItem[$this->DataField];
      if (get_magic_quotes_gpc())
        $_files = stripslashes($_files);
      $_files = json_decode($_files, true);
      $_displayCount = 0;
      if (is_array($_files))
        foreach ($_files as $_file) {
          if ($_displayCount < $this->InitialShownFiles)
            $_displayStyle = '';
          else
            $_displayStyle = 'none';
          $_f = $_l;
          $_name = $_file['name'];
          if (!empty($_file['description']))
            $_desc = $_file['description'];
          else
            $_desc = $_name;
          if ($this->ShowDescription) {
            $_display = $_desc;
            $_title = $_name;
          } else {
            $_display = $_name;
            $_title = $_desc;
          }
          $_f = str_replace("{display}", $_display, $_f);
          $_d = $this->BaseDirectory . '/' . $this->TableName . '/' . $_row->DataItem[$this->IdColumn] . '/' . $_name;
          $_f = str_replace("{filepath}", $_d, $_f);
          $_f = str_replace("{class}", 'koolphp-' . $this->_getFileExtension($_name), $_f);
          $_s .= "<li title='" . $_title . "' style='display:" . $_displayStyle . "'>" . $_f . "</li>";
          $_displayCount++;
        }
      if (count($_files) > $this->InitialShownFiles) {
        $_s .= "<li><span class='kgrFileFormat kgrfile'></span>...</li></ul>";
        $_s .= "<a style='cursor:pointer; font-weight:bold;' onclick=ExpandList('$_id')>>></a><a style='cursor:pointer; font-weight:bold; display:none' onclick=\"CollapseList('$_id', $this->InitialShownFiles)\"><<</a>";
      }
      return $_s;
    }
    function FormEditRender($_row) {
      $_kulId = "kul_" . $_row->_UniqueID . "_" . $this->_UniqueID;
      $_hidInputId = $_row->_UniqueID . "_" . $this->_UniqueID . "_input";
      $_s = '';
      $_s .= "<input type=hidden id={id} name={id} getEditValue='{kulId}.getItemsStatus()'>";
      $_s = _replace("{id}", $_hidInputId, $_s);
      $_s = _replace("{kulId}", $_kulId, $_s);
      $_arr = array();
      $_files = $_row->DataItem[$this->DataField];
      if (get_magic_quotes_gpc())
        $_files = stripslashes($_files);
      $_files = json_decode($_files, true);
      if (is_array($_files))
        foreach ($_files as $_file) {
          $_desc = '';
          if (isset($_file['description']))
            $_desc = $_file['description'];
          array_push($_arr, array(
            'name' => $_file['name'],
            'description' => $_desc
          ));
        }
      $kul = new KoolUploader($_kulId);
      $kul->styleFolder = $this->KoolUploaderFolder . 'styles/default';
      $kul->allowedExtension = $this->AllowedExtension;
      $kul->maxFileSize = $this->MaxFileSize;
      $kul->allowDelete = $this->AllowDelete;
      $kul->allowDownload = $this->AllowDownload;
      $kul->autoUpload = $this->AutoUpload;
      $kul->multipleUpload = $this->MultipleUpload;
      $kul->progressTracking = false;
      $kul->updateProgressInterval = 20;
      $kul->targetFolder = $this->BaseDirectory . '/' . $this->TableName . '/' . $_row->DataItem[$this->IdColumn];
      $kul->currentFiles = $_arr;
      $kul->width = '90%';
      $kul->mustHaveFiles = $this->MustHaveFiles;
      $_s .= $kul->Render();
      return $_s;
    }
    function CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new GridFileColumn();
      }
      parent::CreateInstance($_instance);
      $_instance->BaseDirectory = $this->BaseDirectory;
      $_instance->TableName = $this->TableName;
      $_instance->IdColumn = $this->IdColumn;
      $_instance->UploadHandlePage = $this->UploadHandlePage;
      $_instance->MustHaveFiles = $this->MustHaveFiles;
      $_instance->AllowDelete = $this->AllowDelete;
      $_instance->AllowDownload = $this->AllowDownload;
      $_instance->AllowedExtension = $this->AllowedExtension;
      $_instance->AutoUpload = $this->AutoUpload;
      $_instance->InitialShownFiles = $this->InitialShownFiles;
      $_instance->ShowDescription = $this->ShowDescription;
      $_instance->MultipleUpload = $this->MultipleUpload;
      return $_instance;
    }
  }
  /* =========================================================================== */
  class _GridPager implements _IState {
    var $_UniqueID;
    var $_TableView;
    var $_ViewState;
    var $TableView;
    var $PageIndex = 0;
    var $_TotalPages;
    var $_TotalRows;
    var $PageSize;
    var $ShowPageInfo = true;
    var $PageInfoTemplate;
    var $ShowPageSize = false;
    var $PageSizeText;
    var $PageSizeOptions = "5,10,20,40";
    var $Position = "bottom";
    var $ShowPageOverlap = false;
    var $PageOverlap = 0;
    var $PageOverlapText;
    var $PageOverlapOptions = "0,1,2,3,4,5";
    function _Init($_tableview) {
      $this->_TableView = $_tableview;
      $this->TableView = $_tableview;
      $this->_ViewState = $_tableview->_ViewState;
      if ($this->PageSize === null)
        $this->PageSize = $this->_TableView->PageSize;
      if ($this->PageInfoTemplate === null)
        $this->PageInfoTemplate = $_tableview->_Grid->Localization->_Messages["PageInfoTemplate"];
      if ($this->PageSizeText === null)
        $this->PageSizeText = $_tableview->_Grid->Localization->_Messages["PageSizeText"];
      if ($this->PageOverlapText === null)
        $this->PageOverlapText = $_tableview->_Grid->Localization->_Messages["PageOverlapText"];
      if ($this->PageSize <= $this->PageOverlap)
                $this->PageOverlap = 0;
    }
    function _LoadViewState() {
      if (isset($this->_ViewState->_Data[$this->_UniqueID])) {
        $_state = $this->_ViewState->_Data[$this->_UniqueID];
        $this->PageIndex = $_state["PageIndex"];
        $this->_TotalPages = $_state["_TotalPages"];
        $this->_TotalRows = $_state["_TotalRows"];
        $this->PageSize = $_state["PageSize"];
        $this->PageOverlap = $_state["PageOverlap"];
      }
    }
    function _SaveViewState() {
      $this->_ViewState->_Data[$this->_UniqueID] = array("PageIndex" => $this->PageIndex,
        "_TotalPages" => $this->_TotalPages,
        "PageSize" => $this->PageSize,
        "_TotalRows" => $this->_TotalRows,
        "PageOverlap" => $this->PageOverlap,
      );
    }
    function _ProcessCommand($_command) {
      if (isset($_command->_Commands[$this->_UniqueID])) {
        $_c = $_command->_Commands[$this->_UniqueID];
        switch ($_c["Command"]) {
          case "GoPage":
            if ($this->_TableView->_Grid->EventHandler->OnBeforePageIndexChange($this, array("NewPageIndex" => $_c["Args"]["PageIndex"])) == true) {
              $this->PageIndex = $_c["Args"]["PageIndex"];
              $this->_TableView->_Rebind = true;
              $this->_TableView->_Grid->EventHandler->OnPageIndexChange($this, array());
            }
            break;
          case "ChangePageSize":
            if ($this->_TableView->_Grid->EventHandler->OnBeforePageSizeChange($this, array("NewPageSize" => $_c["Args"]["PageSize"])) == true) {
              $this->PageSize = $_c["Args"]["PageSize"];
              if ($this->PageSize < $this->PageOverlap)
                $this->PageOverlap = 0;
              $this->_TableView->_Rebind = true;
              $this->_TableView->_Grid->EventHandler->OnPageSizeChange($this, array());
            }
            break;
          case "ChangePageOverlap":
            if ($this->_TableView->_Grid->EventHandler->OnBeforePageOverlapChange($this, array("NewPageOverlap" => $_c["Args"]["PageOverlap"])) == true) {
              $this->PageOverlap = $_c["Args"]["PageOverlap"];
              if ($this->PageSize <= $this->PageOverlap)
                $this->PageOverlap = 0;
              $this->_TableView->_Rebind = true;
              $this->_TableView->_Grid->EventHandler->OnPageOverlapChange($this, array());
            }
            break;
        }
      }
      $this->_TotalPages = ceil($this->_TotalRows / ($this->PageSize - $this->PageOverlap));
      if ($this->PageIndex >= $this->_TotalPages)
        $this->PageIndex = $this->_TotalPages - 1;
      if ($this->PageIndex < 0)
        $this->PageIndex = 0;
    }
    function getStartRecordIndex() {
      return $this->PageIndex * ($this->PageSize - $this->PageOverlap);
    }
    function _Render() {
      return "";
    }
    function _RenderPageInfo() {
      $_tpl_info = "<div class='kgrInfo {cssclasses}'>{text}</div>";
      $_text = _replace("{PageIndex}", ($this->_TotalPages > 0) ? ($this->PageIndex + 1) : 0, $this->PageInfoTemplate);
      $_text = _replace("{TotalPages}", $this->_TotalPages, $_text);
      $_firstindex = ($this->_TotalPages > 0) ? ($this->getStartRecordIndex() + 1) : 0;
      $_lastindex = $this->getStartRecordIndex() + $this->PageSize;
      if ($_lastindex > $this->_TotalRows)
        $_lastindex = $this->_TotalRows;
      $_text = _replace("{FirstIndexInPage}", $_firstindex, $_text);
      $_text = _replace("{LastIndexInPage}", $_lastindex, $_text);
      $_text = _replace("{TotalRows}", $this->_TotalRows, $_text);
      $_info = _replace("{text}", $_text, $_tpl_info);
      return $_info;
    }
    function _RenderPageSize() {
      $_tpl_pagesize = "<div class='kgrPageSize {cssclasses}'>{text}{select}</div>";
      $_tpl_select = "<select onchange='grid_pagesize_select_onchange(this)'>{options}</select>";
      $_tpl_option = "<option value='{value}' {selected}>{value}</option>";
      $_options = "";
      $_values = explode(',', $this->PageSizeOptions);
      for ($i = 0; $i < sizeof($_values); $i++) {
        $_option = _replace("{value}", $_values[$i], $_tpl_option);
        $_option = _replace("{selected}", ($this->PageSize == (int) $_values[$i]) ? "selected" : "", $_option);
        $_options.=$_option;
      }
      $_select = _replace("{options}", $_options, $_tpl_select);
      $_pagesize = _replace("{text}", $this->PageSizeText, $_tpl_pagesize);
      $_pagesize = _replace("{select}", $_select, $_pagesize);
      return $_pagesize;
    }
    function _RenderPageOverlap() {
      $_tpl_pageoverlap = "<div class='kgrPageSize {cssclasses}'>{text}{select}</div>";
      $_tpl_select = "<select onchange='grid_pageoverlap_select_onchange(this)'>{options}</select>";
      $_tpl_option = "<option value='{value}' {selected}>{value}</option>";
      $_options = "";
      $_values = explode(',', $this->PageOverlapOptions);
      for ($i = 0; $i < sizeof($_values); $i++) {
        $_option = _replace("{value}", $_values[$i], $_tpl_option);
        $_option = _replace("{selected}", ($this->PageOverlap == (int) $_values[$i]) ? "selected" : "", $_option);
        $_options.=$_option;
      }
      $_select = _replace("{options}", $_options, $_tpl_select);
      $_pageoverlap = _replace("{text}", $this->PageOverlapText, $_tpl_pageoverlap);
      $_pageoverlap = _replace("{select}", $_select, $_pageoverlap);
      return $_pageoverlap;
    }
    function _CreateInstance($_instance = null) {
      $_instance->PageIndex = $this->PageIndex;
      $_instance->ShowPageInfo = $this->ShowPageInfo;
      $_instance->PageInfoTemplate = $this->PageInfoTemplate;
      $_instance->ShowPageSize = $this->ShowPageSize;
      $_instance->PageSizeText = $this->PageSizeText;
      $_instance->PageSizeOptions = $this->PageSizeOptions;
      $_instance->PageSize = $this->PageSize;
      $_instance->Position = $this->Position;
      $_instance->ShowPageOverlap = $this->ShowPageOverlap;
      $_instance->PageOverlap = $this->PageOverlap;
      $_instance->PageOverlapText = $this->PageOverlapText;
      $_instance->PageOverlapOptions = $this->PageOverlapOptions;
    }
  }
  class GridPrevNextPager extends _GridPager {
    var $FirstPageText;
    var $FirstPageToolTip;
    var $PrevPageText;
    var $PrevPageToolTip;
    var $NextPageText;
    var $NextPageToolTip;
    var $LastPageText;
    var $LastPageToolTip;
    function _Init($_tableview) {
      parent::_Init($_tableview);
      if ($this->FirstPageText === null)
        $this->FirstPageText = $_tableview->_Grid->Localization->_Commands["First"];
      if ($this->FirstPageToolTip === null)
        $this->FirstPageToolTip = $_tableview->_Grid->Localization->_Messages["FirstPageToolTip"];
      if ($this->PrevPageText === null)
        $this->PrevPageText = $_tableview->_Grid->Localization->_Commands["Prev"];
      if ($this->PrevPageToolTip === null)
        $this->PrevPageToolTip = $_tableview->_Grid->Localization->_Messages["PrevPageToolTip"];
      if ($this->NextPageText === null)
        $this->NextPageText = $_tableview->_Grid->Localization->_Commands["Next"];
      if ($this->NextPageToolTip === null)
        $this->NextPageToolTip = $_tableview->_Grid->Localization->_Messages["NextPageToolTip"];
      if ($this->LastPageText === null)
        $this->LastPageText = $_tableview->_Grid->Localization->_Commands["Last"];
      if ($this->LastPageToolTip === null)
        $this->LastPageToolTip = $_tableview->_Grid->Localization->_Messages["LastPageToolTip"];
    }
    function _Render() {
      $_tpl_pager = "<div class='kgrPager kgrNextPrevNextPager'>{pagesize}{pageoverlap}{nav}{info}<div style='clear:both'></div></div>";
      $_tpl_nav = "<div class='kgrNav {cssclasses}'>{first} {prev} {next} {last}</div>";
      $_tpl_button = "<input type='button' onclick='{onclick}' title='{title}' class='nodecor'/>";
      $_tpl_a = "<a href='javascript:void 0' onclick='{onclick}' title='{title}'>{text}</a>";
      $_tpl_bound = "<span class= '{class}'>{button}</span>";
      $_first_button = _replace("{onclick}", ($this->PageIndex > 0) ? "grid_gopage(this,0)" : "", $_tpl_button);
      $_first_button = _replace("{title}", $this->FirstPageToolTip, $_first_button);
      $_first_a = _replace("{onclick}", ($this->PageIndex > 0 && $this->FirstPageText !== null) ? "grid_gopage(this,0)" : "", $_tpl_a);
      $_first_a = _replace("{text}", $this->FirstPageText, $_first_a);
      $_first_a = _replace("{title}", $this->FirstPageToolTip, $_first_a);
      $_first = _replace("{button}", $_first_button . $_first_a, $_tpl_bound);
      $_first = _replace("{class}", "kgrFirst", $_first);
      $_prev_button = _replace("{onclick}", ($this->PageIndex > 0) ? "grid_gopage(this," . ($this->PageIndex - 1) . ")" : "", $_tpl_button);
      $_prev_button = _replace("{title}", $this->PrevPageToolTip, $_prev_button);
      $_prev_a = _replace("{onclick}", ($this->PageIndex > 0 && $this->PrevPageText !== null) ? "grid_gopage(this," . ($this->PageIndex - 1) . ")" : "", $_tpl_a);
      $_prev_a = _replace("{text}", $this->PrevPageText, $_prev_a);
      $_prev_a = _replace("{title}", $this->PrevPageToolTip, $_prev_a);
      $_prev = _replace("{button}", $_prev_button . $_prev_a, $_tpl_bound);
      $_prev = _replace("{class}", "kgrPrev", $_prev);
      $_next_button = _replace("{onclick}", ($this->PageIndex < $this->_TotalPages - 1) ? "grid_gopage(this," . ($this->PageIndex + 1) . ")" : "", $_tpl_button);
      $_next_button = _replace("{title}", $this->NextPageToolTip, $_next_button);
      $_next_a = _replace("{onclick}", (($this->PageIndex < $this->_TotalPages - 1) && $this->NextPageText !== null) ? "grid_gopage(this," . ($this->PageIndex + 1) . ")" : "", $_tpl_a);
      $_next_a = _replace("{text}", $this->NextPageText, $_next_a);
      $_next_a = _replace("{title}", $this->NextPageToolTip, $_next_a);
      $_next = _replace("{button}", $_next_a . $_next_button, $_tpl_bound);
      $_next = _replace("{class}", "kgrNext", $_next);
      $_last_button = _replace("{onclick}", ($this->PageIndex < $this->_TotalPages - 1) ? "grid_gopage(this," . ($this->_TotalPages - 1) . ")" : "", $_tpl_button);
      $_last_button = _replace("{title}", $this->LastPageToolTip, $_last_button);
      $_last_a = _replace("{onclick}", (($this->PageIndex < $this->_TotalPages - 1) && $this->LastPageText !== null) ? "grid_gopage(this," . ($this->_TotalPages - 1) . ")" : "", $_tpl_a);
      $_last_a = _replace("{text}", $this->LastPageText, $_last_a);
      $_last_a = _replace("{title}", $this->LastPageToolTip, $_last_a);
      $_last = _replace("{button}", $_last_a . $_last_button, $_tpl_bound);
      $_last = _replace("{class}", "kgrLast", $_last);
      $_nav = _replace("{prev}", $_prev, $_tpl_nav);
      $_nav = _replace("{next}", $_next, $_nav);
      $_nav = _replace("{first}", $_first, $_nav);
      $_nav = _replace("{last}", $_last, $_nav);
      $_pagesize = ($this->ShowPageSize) ? $this->_RenderPageSize() : "";
      $_pageoverlap = ($this->ShowPageOverlap) ? $this->_RenderPageOverlap() : "";
      $_info = ($this->ShowPageInfo) ? $this->_RenderPageInfo() : "";
      $_pager = _replace("{nav}", $_nav, $_tpl_pager);
      $_pager = _replace("{info}", $_info, $_pager);
      $_pager = _replace("{pagesize}", $_pagesize, $_pager);
      $_pager = _replace("{pageoverlap}", $_pageoverlap, $_pager);
      return $_pager;
    }
    function _CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new GridPrevNextPager();
      }
      parent::_CreateInstance($_instance);
      $_instance->FirstPageText = $this->FirstPageText;
      $_instance->LastPageText = $this->LastPageText;
      $_instance->FirstPageToolTip = $this->FirstPageToolTip;
      $_instance->LastPageToolTip = $this->LastPageToolTip;
      $_instance->NextPageText = $this->NextPageText;
      $_instance->PrevPageText = $this->PrevPageText;
      $_instance->NextPageToolTip = $this->NextPageToolTip;
      $_instance->PrevPageToolTip = $this->PrevPageToolTip;
      return $_instance;
    }
  }
  class GridNumericPager extends _GridPager {
    var $Range = 10;
    function _Render() {
      $_tpl_pager = "<div class='kgrPager kgrNumericPager'>{pagesize}{pageoverlap}{nav}{info}<div style='clear:both'></div></div>";
      $_tpl_nav = "<div class='kgrNav {cssclasses}'>{numbers}</div>";
      $_tpl_number = "<a class='kgrNum {selected}' {href} {onclick}><span>{number}</span></a> ";
      $_start_num = floor($this->PageIndex / $this->Range) * $this->Range;
      $_numbers = "";
      if ($_start_num > 0) {
        $_number = _replace("{href}", "href='javascript:void 0'", $_tpl_number);
        $_number = _replace("{onclick}", "onclick='grid_gopage(this," . ($_start_num - 1) . ")'", $_number);
        $_number = _replace("{number}", "...", $_number);
        $_numbers .= $_number;
      }
      for ($i = $_start_num; $i < $_start_num + $this->Range && $i < $this->_TotalPages; $i++) {
        $_number = _replace("{number}", ($i + 1), $_tpl_number);
        if ($i == $this->PageIndex) {
          $_number = _replace("{selected}", "kgrNumSelected", $_number);
          $_number = _replace("{href}", "", $_number);
          $_number = _replace("{onclick}", "", $_number);
        } else {
          $_number = _replace("{selected}", "", $_number);
          $_number = _replace("{href}", "href='javascript:void 0'", $_number);
          $_number = _replace("{onclick}", "onclick='grid_gopage(this," . $i . ")'", $_number);
        }
        $_numbers .= $_number;
      }
      if ($_start_num + $this->Range < $this->_TotalPages) {
        $_number = _replace("{href}", "href='javascript:void 0'", $_tpl_number);
        $_number = _replace("{onclick}", "onclick='grid_gopage(this," . ($_start_num + $this->Range) . ")'", $_number);
        $_number = _replace("{number}", "...", $_number);
        $_number = _replace("{selected}", "", $_number);
        $_numbers .= $_number;
      }
      $_nav = _replace("{numbers}", $_numbers, $_tpl_nav);
      $_pagesize = ($this->ShowPageSize) ? $this->_RenderPageSize() : "";
      $_pageoverlap = ($this->ShowPageOverlap) ? $this->_RenderPageOverlap() : "";
      $_info = ($this->ShowPageInfo) ? $this->_RenderPageInfo() : "";
      $_pager = _replace("{nav}", $_nav, $_tpl_pager);
      $_pager = _replace("{info}", $_info, $_pager);
      $_pager = _replace("{pagesize}", $_pagesize, $_pager);
      $_pager = _replace("{pageoverlap}", $_pageoverlap, $_pager);
      return $_pager;
    }
    function _CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new GridNumericPager();
      }
      parent::_CreateInstance($_instance);
      $_instance->Range = $this->Range;
      return $_instance;
    }
  }
  class GridPrevNextAndNumericPager extends _GridPager {
    var $Range = 10;
    var $FirstPageText;
    var $FirstPageToolTip;
    var $PrevPageText;
    var $PrevPageToolTip;
    var $NextPageText;
    var $NextPageToolTip;
    var $LastPageText;
    var $LastPageToolTip;
    function _Init($_tableview) {
      parent::_Init($_tableview);
      if ($this->FirstPageText === null)
        $this->FirstPageText = $_tableview->_Grid->Localization->_Commands["First"];
      if ($this->FirstPageToolTip === null)
        $this->FirstPageToolTip = $_tableview->_Grid->Localization->_Messages["FirstPageToolTip"];
      if ($this->PrevPageText === null)
        $this->PrevPageText = $_tableview->_Grid->Localization->_Commands["Prev"];
      if ($this->PrevPageToolTip === null)
        $this->PrevPageToolTip = $_tableview->_Grid->Localization->_Messages["PrevPageToolTip"];
      if ($this->NextPageText === null)
        $this->NextPageText = $_tableview->_Grid->Localization->_Commands["Next"];
      if ($this->NextPageToolTip === null)
        $this->NextPageToolTip = $_tableview->_Grid->Localization->_Messages["NextPageToolTip"];
      if ($this->LastPageText === null)
        $this->LastPageText = $_tableview->_Grid->Localization->_Commands["Last"];
      if ($this->LastPageToolTip === null)
        $this->LastPageToolTip = $_tableview->_Grid->Localization->_Messages["LastPageToolTip"];
    }
    function _Render() {
      $_tpl_pager = "<div class='kgrPager kgrNextPrevAndNumericPager'>{pagesize}{pageoverlap}{nav}{info}<div style='clear:both'></div></div>";
      $_tpl_nav = "<div class='kgrNav {cssclasses}'>{first} {prev} {numbers} {next} {last}</div>";
      $_tpl_number = "<a class='kgrNum {selected}' {href} {onclick}><span>{number}</span></a> ";
      $_tpl_button = "<input type='button' onclick='{onclick}' title='{title}' class='nodecor'/>";
      $_tpl_a = "<a href='javascript:void 0' onclick='{onclick}' title='{title}'>{text}</a>";
      $_tpl_bound = "<span class= '{class}'>{button}</span>";
      $_start_num = floor($this->PageIndex / $this->Range) * $this->Range;
      $_numbers = "";
      if ($_start_num > 0) {
        $_number = _replace("{href}", "href='javascript:void 0'", $_tpl_number);
        $_number = _replace("{onclick}", "onclick='grid_gopage(this," . ($_start_num - 1) . ")'", $_number);
        $_number = _replace("{number}", "...", $_number);
        $_numbers .= $_number;
      }
      for ($i = $_start_num; $i < $_start_num + $this->Range && $i < $this->_TotalPages; $i++) {
        $_number = _replace("{number}", ($i + 1), $_tpl_number);
        if ($i == $this->PageIndex) {
          $_number = _replace("{selected}", "kgrNumSelected", $_number);
          $_number = _replace("{href}", "", $_number);
          $_number = _replace("{onclick}", "", $_number);
        } else {
          $_number = _replace("{selected}", "", $_number);
          $_number = _replace("{href}", "href='javascript:void 0'", $_number);
          $_number = _replace("{onclick}", "onclick='grid_gopage(this," . $i . ")'", $_number);
        }
        $_numbers .= $_number;
      }
      if ($_start_num + $this->Range < $this->_TotalPages) {
        $_number = _replace("{href}", "href='javascript:void 0'", $_tpl_number);
        $_number = _replace("{onclick}", "onclick='grid_gopage(this," . ($_start_num + $this->Range) . ")'", $_number);
        $_number = _replace("{number}", "...", $_number);
        $_number = _replace("{selected}", "", $_number);
        $_numbers .= $_number;
      }
      $_first_button = _replace("{onclick}", ($this->PageIndex > 0) ? "grid_gopage(this,0)" : "", $_tpl_button);
      $_first_button = _replace("{title}", $this->FirstPageToolTip, $_first_button);
      $_first_a = _replace("{onclick}", ($this->PageIndex > 0 && $this->FirstPageText !== null) ? "grid_gopage(this,0)" : "", $_tpl_a);
      $_first_a = _replace("{text}", $this->FirstPageText, $_first_a);
      $_first_a = _replace("{title}", $this->FirstPageToolTip, $_first_a);
      $_first = _replace("{button}", $_first_button . $_first_a, $_tpl_bound);
      $_first = _replace("{class}", "kgrFirst", $_first);
      $_prev_button = _replace("{onclick}", ($this->PageIndex > 0) ? "grid_gopage(this," . ($this->PageIndex - 1) . ")" : "", $_tpl_button);
      $_prev_button = _replace("{title}", $this->PrevPageToolTip, $_prev_button);
      $_prev_a = _replace("{onclick}", ($this->PageIndex > 0 && $this->PrevPageText !== null) ? "grid_gopage(this," . ($this->PageIndex - 1) . ")" : "", $_tpl_a);
      $_prev_a = _replace("{text}", $this->PrevPageText, $_prev_a);
      $_prev_a = _replace("{title}", $this->PrevPageToolTip, $_prev_a);
      $_prev = _replace("{button}", $_prev_button . $_prev_a, $_tpl_bound);
      $_prev = _replace("{class}", "kgrPrev", $_prev);
      $_next_button = _replace("{onclick}", ($this->PageIndex < $this->_TotalPages - 1) ? "grid_gopage(this," . ($this->PageIndex + 1) . ")" : "", $_tpl_button);
      $_next_button = _replace("{title}", $this->NextPageToolTip, $_next_button);
      $_next_a = _replace("{onclick}", (($this->PageIndex < $this->_TotalPages - 1) && $this->NextPageText !== null) ? "grid_gopage(this," . ($this->PageIndex + 1) . ")" : "", $_tpl_a);
      $_next_a = _replace("{text}", $this->NextPageText, $_next_a);
      $_next_a = _replace("{title}", $this->NextPageToolTip, $_next_a);
      $_next = _replace("{button}", $_next_a . $_next_button, $_tpl_bound);
      $_next = _replace("{class}", "kgrNext", $_next);
      $_last_button = _replace("{onclick}", ($this->PageIndex < $this->_TotalPages - 1) ? "grid_gopage(this," . ($this->_TotalPages - 1) . ")" : "", $_tpl_button);
      $_last_button = _replace("{title}", $this->LastPageToolTip, $_last_button);
      $_last_a = _replace("{onclick}", (($this->PageIndex < $this->_TotalPages - 1) && $this->LastPageText !== null) ? "grid_gopage(this," . ($this->_TotalPages - 1) . ")" : "", $_tpl_a);
      $_last_a = _replace("{text}", $this->LastPageText, $_last_a);
      $_last_a = _replace("{title}", $this->LastPageToolTip, $_last_a);
      $_last = _replace("{button}", $_last_a . $_last_button, $_tpl_bound);
      $_last = _replace("{class}", "kgrLast", $_last);
      $_nav = _replace("{numbers}", $_numbers, $_tpl_nav);
      $_nav = _replace("{prev}", $_prev, $_nav);
      $_nav = _replace("{next}", $_next, $_nav);
      $_nav = _replace("{first}", $_first, $_nav);
      $_nav = _replace("{last}", $_last, $_nav);
      $_pagesize = ($this->ShowPageSize) ? $this->_RenderPageSize() : "";
      $_pageoverlap = ($this->ShowPageOverlap) ? $this->_RenderPageOverlap() : "";
      $_info = ($this->ShowPageInfo) ? $this->_RenderPageInfo() : "";
      $_pager = _replace("{nav}", $_nav, $_tpl_pager);
      $_pager = _replace("{info}", $_info, $_pager);
      $_pager = _replace("{pagesize}", $_pagesize, $_pager);
      $_pager = _replace("{pageoverlap}", $_pageoverlap, $_pager);
      return $_pager;
    }
    function _CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new GridPrevNextAndNumericPager();
      }
      parent::_CreateInstance($_instance);
      $_instance->Range = $this->Range;
      $_instance->FirstPageText = $this->FirstPageText;
      $_instance->LastPageText = $this->LastPageText;
      $_instance->FirstPageToolTip = $this->FirstPageToolTip;
      $_instance->LastPageToolTip = $this->LastPageToolTip;
      $_instance->NextPageText = $this->NextPageText;
      $_instance->PrevPageText = $this->PrevPageText;
      $_instance->NextPageToolTip = $this->NextPageToolTip;
      $_instance->PrevPageToolTip = $this->PrevPageToolTip;
      return $_instance;
    }
  }
  class GridManualPager extends _GridPager {
    var $ManualPagerTemplate;
    var $ButtonType = "Button"; //"Button"|"Link"|"Image"
    var $GoPageButtonText;
    var $TextBoxWidth = "25px";
    function _Init($_tableview) {
      parent::_Init($_tableview);
      if ($this->ManualPagerTemplate === null)
        $this->ManualPagerTemplate = $_tableview->_Grid->Localization->_Messages["ManualPagerTemplate"];
      if ($this->GoPageButtonText === null)
        $this->GoPageButtonText = $_tableview->_Grid->Localization->_Commands["Go"];
    }
    function _ProcessCommand($_command) {
      parent::_ProcessCommand($_command);
      if (isset($_command->_Commands[$this->_UniqueID])) {
        $_c = $_command->_Commands[$this->_UniqueID];
        $this->PageIndex = ((int) $_POST[$this->_UniqueID . "_input"]) - 1;
        if ($this->PageIndex >= $this->_TotalPages)
          $this->PageIndex = $this->_TotalPages - 1;
        if ($this->PageIndex < 0)
          $this->PageIndex = 0;
        $this->_TableView->_Rebind = true;
      }
    }
    function _Render() {
      $_tpl_pager = "<div class='kgrPager kgrManualPager'>{pagesize}{pageoverlap}{nav}{info}<div style='clear:both'></div></div>";
      $_tpl_nav = "<div class='kgrNav {cssclasses}'>{main}</div>";
      $_tpl_textbox = "<input id='{id}' name='{id}' type='textbox' style='width:{width};' value='{text}'/>";
      $_tpl_main = $this->ManualPagerTemplate;
      $_tpl_gopage = "";
      switch (strtolower($this->ButtonType)) {
        case "link":
          $_tpl_gopage = "<a class='kgrGoButton' href='javascript:void 0' onclick='grid_gopage(this,0)'>{text}</a>";
          break;
        case "image":
          $_tpl_gopage = "<input class='kgrGoButton kgrGoImage' type='button' onclick='grid_gopage(this,0)' />";
          break;
        case "button":
        default:
          $_tpl_gopage = "<input class='kgrGoButton' type='button' onclick='grid_gopage(this,0)' value='{text}' />";
          break;
      }
      $_textbox = _replace("{id}", $this->_UniqueID . "_input", $_tpl_textbox);
      $_textbox = _replace("{width}", $this->TextBoxWidth, $_textbox);
      $_textbox = _replace("{text}", $this->PageIndex + 1, $_textbox);
      $_gopage = _replace("{text}", $this->GoPageButtonText, $_tpl_gopage);
      $_main = _replace("{TextBox}", $_textbox, $_tpl_main);
      $_main = _replace("{GoPageButton}", $_gopage, $_main);
      $_main = _replace("{TotalPage}", $this->_TotalPages, $_main);
      $_nav = _replace("{main}", $_main, $_tpl_nav);
      $_pagesize = ($this->ShowPageSize) ? $this->_RenderPageSize() : "";
      $_pageoverlap = ($this->ShowPageOverlap) ? $this->_RenderPageOverlap() : "";
      $_info = ($this->ShowPageInfo) ? $this->_RenderPageInfo() : "";
      $_pager = _replace("{nav}", $_nav, $_tpl_pager);
      $_pager = _replace("{info}", $_info, $_pager);
      $_pager = _replace("{pagesize}", $_pagesize, $_pager);
      $_pager = _replace("{pageoverlap}", $_pageoverlap, $_pager);
      return $_pager;
    }
    function _CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new GridManualPager();
      }
      parent::_CreateInstance($_instance);
      $_instance->ManualPagerTemplate = $this->ManualPagerTemplate;
      $_instance->ButtonType = $this->ButtonType;
      $_instance->GoPageButtonText = $this->GoPageButtonText;
      $_instance->TextBoxWidth = $this->TextBoxWidth;
      return $_instance;
    }
  }
  class GridCustomPager extends _GridPager {
    function Render($_args) {
      return "CustomPager";
    }
    function _Render() {
      $_tpl_pager = "<div class='kgrPager kgrCustomPager'>{pagesize}{pageoverlap}{nav}{info}<div style='clear:both'></div></div>";
      $_tpl_nav = "<div class='kgrNav {cssclasses}'>{main}</div>";
      $_pager = $_tpl_pager;
      $_args = array("PageIndex" => $this->PageIndex, "TotalPages" => $this->_TotalPages);
      $_nav = _replace("{main}", $this->Render($_args), $_tpl_nav);
      $_pager = _replace("{nav}", $_nav, $_pager);
      $_info = ($this->ShowPageInfo) ? $this->_RenderPageInfo() : "";
      $_pager = _replace("{info}", $_info, $_pager);
      $_pagesize = ($this->ShowPageSize) ? $this->_RenderPageSize() : "";
      $_pageoverlap = ($this->ShowPageOverlap) ? $this->_RenderPageOverlap() : "";
      $_info = ($this->ShowPageInfo) ? $this->_RenderPageInfo() : "";
      $_pager = _replace("{info}", $_info, $_pager);
      $_pager = _replace("{pagesize}", $_pagesize, $_pager);
      $_pager = _replace("{pageoverlap}", $_pageoverlap, $_pager);
      return $_pager;
    }
    function _CreateInstance($_ii02 = null) {
      if ($_ii02 === null) {
        eval("\$_ii02 = new " . get_class($this) . "();");
      }
      parent::_CreateInstance($_ii02);
      return $_ii02;
    }
  }
  /* =========================================================================== */
  interface GridTemplate {
    function Render($_Row);
    function GetData($_Row);
  }
  /*
    class _FormTemplate
    {
    var $RenderFunction;
    var $GetDataFunction;
    function _Render($_data)
    {
    if ($this->RenderFunction!=null)
    {
    $_funcRender = $this->RenderFunction;
    return $_funcRender($_data);
    }
    return "";
    }
    function _GetData($_data)
    {
    if ($this->GetDataFunction!=null)
    {
    $_funcGetData = $this->GetDataFunction;
    return $_funcGetData($_data);
    }
    return $_data;
    }
    }
   */
  /* =========================================================================== */
  class _AutoFormSettings {
    var $Mode = "Inline"; //"Inline"|"Form"|"Template"
    var $HeaderCaption;
    var $ColumnNumber = 1;
    var $CancelButtonText;
    var $ConfirmButtonText;
    var $CancelButtonToolTip;
    var $ConfirmButtonToolTip;
    var $Template;
    var $InputFocus = "none"; //"hidegrid"|"blurgrid"
    /* 	
      function __construct()
      {
      $this->Template = new _FormTemplate();
      }
     */
    function _CreateInstance($_instance = null) {
      $_instance->Mode = $this->Mode;
      $_instance->Template = $this->Template;
      $_instance->HeaderCaption = $this->HeaderCaption;
      $_instance->ColumnNumber = $this->ColumnNumber;
      $_instance->CancelButtonText = $this->CancelButtonText;
      $_instance->ConfirmButtonText = $this->ConfirmButtonText;
      $_instance->CancelButtonToolTip = $this->CancelButtonToolTip;
      $_instance->ConfirmButtonToolTip = $this->ConfirmButtonToolTip;
      $_instance->InputFocus = $this->InputFocus;
    }
  }
  class _AutoFormInstance extends _AutoFormSettings {
    var $_UniqueID;
    var $_Command;
  }
  class _EditFormSettings extends _AutoFormSettings {
    function _CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new _EditFormInstance();
      }
      parent::_CreateInstance($_instance);
      return $_instance;
    }
  }
  class _EditFormInstance extends _AutoFormInstance {
    var $_Row;
    var $_NewDataItem;
    function _Init($_tableview) {
      if ($this->CancelButtonText === null)
        $this->CancelButtonText = $_tableview->_Grid->Localization->_Commands["Cancel"];
      if ($this->ConfirmButtonText === null)
        $this->ConfirmButtonText = $_tableview->_Grid->Localization->_Commands["Confirm"];
      if ($this->CancelButtonToolTip === null)
        $this->CancelButtonToolTip = $_tableview->_Grid->Localization->_Messages["EditForm_CancelButtonToolTip"];
      if ($this->ConfirmButtonToolTip === null)
        $this->ConfirmButtonToolTip = $_tableview->_Grid->Localization->_Messages["EditForm_ConfirmButtonToolTip"];
    }
    function _ProcessUpdateCommand() {
      $_dataitem = $this->_Row->DataItem;
      $_fail_validate = false;
      if (strtolower($this->Mode) == "template") {
        $_new_data = $this->Template->GetData($this->_Row);
        foreach ($_new_data as $_k => $_v) {
          $_dataitem[$_k] = $_v;
        }
      } else {
        foreach ($this->_Row->_TableView->_Columns as $_col) {
          if (!$_col->ReadOnly) {
            if (!empty($_col->HiddenDataField))
              $_dataitem[$_col->HiddenDataField] = $_col->GetHiddenEditValue($this->_Row);
            $_dataitem[$_col->DataField] = $_col->GetEditValue($this->_Row);
          }
        }
        foreach ($this->_Row->_TableView->_Columns as $_col) {
          if (!$_col->ReadOnly) {
            foreach ($_col->_Validators as $_validator) {
              $_datafield = $_col->DataField;
              if (!$_validator->Validate($_dataitem[$_datafield], $_dataitem, $this->_Row, $_col)) {
                $_fail_validate = true;
              }
            }
          }
        }
      }
      $this->_NewDataItem = $_dataitem;
      if (!$_fail_validate) {
        if ($this->_Row->_TableView->_Grid->EventHandler->OnBeforeRowConfirmEdit($this->_Row, array("NewDataItem" => &$_dataitem)) == true) {
          $_update_successful = $this->_Row->_TableView->DataSource->Update($_dataitem);
          $_error = $this->_Row->_TableView->DataSource->GetError();
          /*
            if($_update_successful)
            {
            $this->_Row->DataItem = $_dataitem;
            }
           */
          $this->_Row->_TableView->_Rebind = true;
          $this->_Row->EditMode = false;
          if ($_error != "")
            $this->_Row->_TableView->_Grid->EventHandler->OnDataSourceError($this, array("Error" => $_error));
          $this->_Row->_TableView->_Grid->EventHandler->OnRowConfirmEdit($this->_Row, array("NewDataItem" => $_dataitem, "Successful" => $_update_successful, "Error" => $_error));
        }
      }
    }
    function _Render() {
      $_tr = "";
      $_updated_row = new GridRow();
      $_updated_row->_UniqueID = $this->_Row->_UniqueID;
      $_updated_row->_Init($this->_Row->_TableView);
      $_updated_row->DataItem = ($this->_NewDataItem !== null) ? $this->_NewDataItem : $this->_Row->DataItem;
      switch (strtolower($this->Mode)) {
        case "template":
          $_tpl_tr = "<tr id='{rowid}' class='kgrRow {alt} {selected} kgrRowEdit'>{tds}</tr>";
          $_tpl_editform_tr = "<tr><td colspan='{colspan}'><div id='{rowid}_editform' class='kgrEditForm {inputfocus}'>{content}</div></td></tr>";
          $_tds = "";
          for ($i = 0; $i < sizeof($this->_Row->_TableView->_Columns); $i++) {
            $_col = $this->_Row->_TableView->_Columns[$i];
            $_td = $_col->_Render($this->_Row);
            $_tds.=$_td;
          }
          $_tr = _replace("{tds}", $_tds, $_tpl_tr);
          $_editform_tr = _replace("{content}", ($this->Template === null) ? "<b>Notice</b>: No template found!" : $this->Template->Render($this->_Row), $_tpl_editform_tr);
          $_editform_tr = _replace("{colspan}", sizeof($this->_Row->_TableView->_Columns), $_editform_tr);
          switch (strtolower($this->InputFocus)) {
            case "hidegrid":
              $_editform_tr = _replace("{inputfocus}", "kgrInputFocus kgrHideGrid", $_editform_tr);
              break;
            case "blurgrid":
              $_editform_tr = _replace("{inputfocus}", "kgrInputFocus kgrBlurGrid", $_editform_tr);
              break;
            default:
              $_editform_tr = _replace("{inputfocus}", "", $_editform_tr);
              break;
          }
          $_tr.=$_editform_tr;
          break;
        case "form":
          $_tpl_tr = "<tr id='{rowid}' class='kgrRow {alt} {selected} kgrRowEdit'>{tds}</tr>";
          $_tpl_editform_tr = "<tr><td colspan='{colspan}'><div id='{rowid}_editform' class='kgrEditForm {inputfocus}'>{header}{validators}{bigtable}{footer}</div></td></tr>";
          $_tpl_editform_header = "<div class='kgrFormHeader'>{text}</div>";
          $_tpl_editform_footer = "<div class='kgrFormFooter'>{buttons}</div>";
          $_tpl_editform_validators = "<ul class='kgrValidator'>{items}</ul>";
          $_tpl_editform_validator_item = "<li><label for='{id}'>{header}: {error}</label></li>";
          $_tpl_editform_bigtable = "<table style='table-layout:fixed;width:100%;'><tr>{bigtable_tds}</tr></table>";
          $_tpl_editform_bigtable_td = "<td style='vertical-align: top;width:{width}%'>{table{n}}</td>";
          $_tpl_editform_coltable = "<table style='height:{height}px;width:100%;'>{ct_trs}</table>";
          $_tpl_button = "<input type='button' onclick='{onclick}' title='{title}' class='nodecor'/>";
          $_tpl_a = "<a href='javascript:void 0' onclick='{onclick}' title='{title}'>{text}</a>";
          $_tpl_bound = "<span class= '{class}'>{button}{a}</span> ";
          $_unit_height = 35;
          $_tds = "";
          for ($i = 0; $i < sizeof($this->_Row->_TableView->_Columns); $i++) {
            $_col = $this->_Row->_TableView->_Columns[$i];
            $_td = $_col->_Render($this->_Row);
            $_tds.=$_td;
          }
          $_tr = _replace("{tds}", $_tds, $_tpl_tr);
          $_header = "";
          $_header_caption = $this->HeaderCaption;
          if ($_header_caption != null) {
            foreach ($this->_Row->DataItem as $_k => $_v) {
              $_header_caption = _replace("{" . $_k . "}", $_v, $_header_caption);
            }
            $_header = _replace("{text}", $_header_caption, $_tpl_editform_header);
          }
          $_items = "";
          if ($this->_NewDataItem !== null && $this->_Command != "StartEdit") {
            foreach ($_updated_row->_TableView->_Columns as $_col) {
              if (!$_col->ReadOnly) {
                foreach ($_col->_Validators as $_validator) {
                  if (!$_validator->Validate($_updated_row->DataItem[$_col->DataField], $_updated_row->DataItem, $this->_Row, $_col)) {
                    $_editform_validator_item = _replace("{header}", $_col->HeaderText, $_tpl_editform_validator_item);
                    $_editform_validator_item = _replace("{error}", $_validator->ErrorMessage, $_editform_validator_item);
                    $_editform_validator_item = _replace("{id}", $_updated_row->_UniqueID . "_" . $_col->_UniqueID . "_input", $_editform_validator_item);
                    $_items.= $_editform_validator_item;
                  }
                }
              }
            }
          }
          $_validators = _replace("{items}", $_items, $_tpl_editform_validators);
          $_bigtable_tds = "";
          for ($i = 0; $i < $this->ColumnNumber; $i++) {
            $_bigtable_td = _replace("{n}", $i, $_tpl_editform_bigtable_td);
            $_bigtable_td = _replace("{width}", (100 / $this->ColumnNumber), $_bigtable_td);
            $_bigtable_tds.=$_bigtable_td;
          }
          $_bigtable = _replace("{bigtable_tds}", $_bigtable_tds, $_tpl_editform_bigtable);
          $_arr_coltable_tr = array();
          $_added_height = 0;
          for ($i = 0; $i < sizeof($_updated_row->_TableView->_Columns); $i++) {
            $_col = $_updated_row->_TableView->_Columns[$i];
            if (!$_col->ReadOnly) {
              $_coltable_tr = $_col->_FormEditRender($_updated_row);
              array_push($_arr_coltable_tr, $_coltable_tr);
              if (is_a($_col, 'gridfilecolumn'))
                $_added_height += 165;
              if (is_a($_col, 'gridtextareacolumn') && !empty($_col->BoxHeight))
                $_added_height += intval($_col->BoxHeight) - 18;
            }
          }
          $_no_row_each_table = ceil(sizeof($_arr_coltable_tr) / $this->ColumnNumber);
          for ($i = 0; $i < $this->ColumnNumber; $i++) {
            $_col_trs = "";
            for ($j = 0; $j < $_no_row_each_table; $j++) {
              $_index = $_no_row_each_table * $i + $j;
              if ($_index < sizeof($_arr_coltable_tr)) {
                $_col_trs.=$_arr_coltable_tr[$_index];
              }
            }
            $_editform_coltable = _replace("{ct_trs}", $_col_trs, $_tpl_editform_coltable);
            $_editform_coltable = _replace("{height}", $_no_row_each_table * $_unit_height + $_added_height, $_editform_coltable);
            if ($_col_trs == "")
              $_editform_coltable = "";
            $_bigtable = _replace("{table" . $i . "}", $_editform_coltable, $_bigtable);
          }
          $_confirm_button = _replace("{class}", "kgrConfirm", $_tpl_bound);
          $_confirm_button = _replace("{button}", $_tpl_button, $_confirm_button);
          $_confirm_button = _replace("{a}", ($this->ConfirmButtonText != null) ? $_tpl_a : "", $_confirm_button);
          $_confirm_button = _replace("{onclick}", "grid_confirm_edit(this)", $_confirm_button);
          $_confirm_button = _replace("{title}", $this->ConfirmButtonToolTip, $_confirm_button);
          $_confirm_button = _replace("{text}", $this->ConfirmButtonText, $_confirm_button);
          $_cancel_button = _replace("{class}", "kgrCancel", $_tpl_bound);
          $_cancel_button = _replace("{button}", $_tpl_button, $_cancel_button);
          $_cancel_button = _replace("{a}", ($this->CancelButtonText != null) ? $_tpl_a : "", $_cancel_button);
          $_cancel_button = _replace("{onclick}", "grid_cancel_edit(this)", $_cancel_button);
          $_cancel_button = _replace("{title}", $this->CancelButtonToolTip, $_cancel_button);
          $_cancel_button = _replace("{text}", $this->CancelButtonText, $_cancel_button);
          $_footer = _replace("{buttons}", $_confirm_button . $_cancel_button, $_tpl_editform_footer);
          $_editform_tr = _replace("{header}", $_header, $_tpl_editform_tr);
          switch (strtolower($this->InputFocus)) {
            case "hidegrid":
              $_editform_tr = _replace("{inputfocus}", "kgrInputFocus kgrHideGrid", $_editform_tr);
              break;
            case "blurgrid":
              $_editform_tr = _replace("{inputfocus}", "kgrInputFocus kgrBlurGrid", $_editform_tr);
              break;
            default:
              $_editform_tr = _replace("{inputfocus}", "", $_editform_tr);
              break;
          }
          $_editform_tr = _replace("{validators}", $_validators, $_editform_tr);
          $_editform_tr = _replace("{bigtable}", $_bigtable, $_editform_tr);
          $_editform_tr = _replace("{footer}", $_footer, $_editform_tr);
          $_editform_tr = _replace("{colspan}", sizeof($_updated_row->_TableView->_Columns), $_editform_tr);
          $_tr.= $_editform_tr;
          break;
        case "inline":
        default:
          $_tpl_tr_inputfocus = "<tr><td colspan='{colspan}'><div class='kgrInputFocus kgrBlurGrid' style='width:100%'><table class='kgrTable' cellspacing='0' style='table-layout: auto;empty-cells: show;width:100%'><tbody><tr id='{rowid}' class='kgrRow {alt} {selected} kgrRowEdit'>{tds}</tr></tbody></table></div></td></tr>";
          $_tpl_tr = "<tr id='{rowid}' class='kgrRow {alt} {selected} kgrRowEdit'>{tds}</tr>";
          $_tpl_validator_tr = "<tr class='kgrValidator'>{valid_tds}</tr>";
          $_tpl_validator_td = "<td class='kgrCell'><div class='kgrIn' style='white-space:normal;'>{divs}</div></td>";
          $_tpl_validator_div = "<div><label for='{id}'>{error}</label></div>";
          $_tds = "";
          for ($i = 0; $i < sizeof($_updated_row->_TableView->_Columns); $i++) {
            $_col = $_updated_row->_TableView->_Columns[$i];
            $_td = $_col->_InlineEditRender($_updated_row);
            $_tds.=$_td;
          }
          $_tr = _replace("{tds}", $_tds, ($this->InputFocus == "blurgrid") ? $_tpl_tr_inputfocus : $_tpl_tr);
          $_tr = _replace("{colspan}", sizeof($_updated_row->_TableView->_Columns), $_tr);
          $_validator_tr = "";
          if ($this->_NewDataItem !== null && $this->_Command != "StartEdit") {
            $_valid_tds = "";
            for ($i = 0; $i < sizeof($_updated_row->_TableView->_Columns); $i++) {
              $_col = $_updated_row->_TableView->_Columns[$i];
              $_valid_divs = "";
              if (!$_col->ReadOnly) {
                foreach ($_col->_Validators as $_validator) {
                  if (!$_validator->Validate($_updated_row->DataItem[$_col->DataField], $_updated_row->DataItem, $this->_Row, $_col)) {
                    $_valid_div = _replace("{error}", $_validator->ErrorMessage, $_tpl_validator_div);
                    $_valid_div = _replace("{id}", $_updated_row->_UniqueID . "_" . $_col->_UniqueID . "_input", $_valid_div);
                    $_valid_divs.= $_valid_div;
                  }
                }
              }
              $_valid_td = _replace("{divs}", $_valid_divs, $_tpl_validator_td);
              $_valid_tds.=$_valid_td;
            }
            $_validator_tr = _replace("{valid_tds}", $_valid_tds, $_tpl_validator_tr);
          }
          $_tr.=$_validator_tr;
          break;
      }
      return $_tr;
    }
  }
  class _InsertFormSettings extends _AutoFormSettings {
    var $HeaderCaption = "";
    function _CreateInstance($_instance = null) {
      if ($_instance === null) {
        $_instance = new _InsertFormInstance();
      }
      parent::_CreateInstance($_instance);
      return $_instance;
    }
  }
  class _InsertFormInstance extends _AutoFormInstance {
    var $_TableView;
    var $_NewDataItem;
    function _Init($_tableview) {
      $this->_TableView = $_tableview;
      if ($this->CancelButtonText === null)
        $this->CancelButtonText = $_tableview->_Grid->Localization->_Commands["Cancel"];
      if ($this->ConfirmButtonText === null)
        $this->ConfirmButtonText = $_tableview->_Grid->Localization->_Commands["Confirm"];
      if ($this->CancelButtonToolTip === null)
        $this->CancelButtonToolTip = $_tableview->_Grid->Localization->_Messages["InsertForm_CancelButtonToolTip"];
      if ($this->ConfirmButtonToolTip === null)
        $this->ConfirmButtonToolTip = $_tableview->_Grid->Localization->_Messages["InsertForm_ConfirmButtonToolTip"];
    }
    function _ProcessInsertCommand() {
      $_dataitem = array();
      for ($i = 0; $i < sizeof($this->_TableView->_Columns); $i++) {
        if ($this->_TableView->_Columns[$i]->DataField != null) {
          $_dataitem[$this->_TableView->_Columns[$i]->DataField] = $this->_TableView->_Columns[$i]->DefaultValue;
        }
      }
      $_new_row = new GridRow();
      $_new_row->_UniqueID = $this->_TableView->_UniqueID . "_nr";
      $_new_row->_Init($this->_TableView);
      $_fail_validate = false;
      if (strtolower($this->Mode) == "template") {
        $_new_data = $this->Template->GetData($_new_row);
        foreach ($_new_data as $_k => $_v) {
          $_dataitem[$_k] = $_v;
        }
      } else {
        foreach ($this->_TableView->_Columns as $_col) {
          if (!$_col->ReadOnly) {
            if (!empty($_col->HiddenDataField))
              $_dataitem[$_col->HiddenDataField] = $_col->GetHiddenEditValue($_new_row);
            $_dataitem[$_col->DataField] = $_col->GetEditValue($_new_row);
          }
        }
        foreach ($this->_TableView->_Columns as $_col) {
          if (!$_col->ReadOnly) {
            foreach ($_col->_Validators as $_validator) {
              $_datafield = $_col->DataField;
              if (!$_validator->Validate($_dataitem[$_datafield], $_dataitem, $_new_row, $_col)) {
                $_fail_validate = true;
              }
            }
          }
        }
      }
      if ($this->_TableView->_ParentRow !== null) {
        foreach ($this->_TableView->_RelationFields as $_relation_field) {
          $_dataitem[$_relation_field["Detail"]] = $this->_TableView->_ParentRow->DataItem[$_relation_field["Master"]];
        }
      }
      $this->_NewDataItem = $_dataitem;
      if (!$_fail_validate) {
        if ($this->_TableView->_Grid->EventHandler->OnBeforeConfirmInsert($this->_TableView, array("NewDataItem" => &$_dataitem)) == true) {
          $cols = $this->_TableView->_Columns;
          $fileCols = array();
          foreach ($cols as $col) {
            if (is_a($col, 'gridfilecolumn'))
              array_push($fileCols, $col);
          }
          $rowIds = array();
          if (count($fileCols) == 0) {
            $_insert_successful = $this->_TableView->DataSource->Insert($_dataitem);
          } else {
            $_insert_successful = $this->_TableView->DataSource->Insert($_dataitem, $fileCols, $rowIds);
            foreach ($fileCols as $k => $col) {
              $files = $_dataitem[$col->DataField];
              if (get_magic_quotes_gpc())
                $files = stripslashes($files);
              $files = json_decode($files, true);
              $parentPath = '';
              $tempFolder = $col->BaseDirectory . '/' . $col->TableName;
              $targetFolder = $col->BaseDirectory . '/' . $col->TableName . '/' . $parentPath .  $rowIds[$k];
              mkdir($targetFolder);
              foreach ($files as $file) {
                rename($tempFolder . '/' . $file['name'], $targetFolder . '/' . $file['name']);
              }
            }
          }
          $_error = $this->_TableView->DataSource->GetError();
          $this->_TableView->_Rebind = true; //Insert so rebind to keep grid update.
          $this->_TableView->_Inserting = false;
          if ($_error != "")
            $this->_TableView->_Grid->EventHandler->OnDataSourceError($this, array("Error" => $_error));
          $this->_TableView->_Grid->EventHandler->OnConfirmInsert($this->_TableView, array("NewDataItem" => $_dataitem, "Successful" => $_insert_successful, "Error" => $_error));
        }
      }
    }
    function _Render() {
      $_insertform_tr = "";
      $_dataitem = array();
      for ($i = 0; $i < sizeof($this->_TableView->_Columns); $i++) {
        if ($this->_TableView->_Columns[$i]->DataField != null) {
          $_dataitem[$this->_TableView->_Columns[$i]->DataField] = $this->_TableView->_Columns[$i]->DefaultValue;
        }
      }
      if ($this->_NewDataItem === null) {
        $this->_NewDataItem = $_dataitem;
      }
      $_new_row = new GridRow();
      $_new_row->_UniqueID = $this->_TableView->_UniqueID . "_nr";
      $_new_row->_Init($this->_TableView);
      $_new_row->DataItem = $this->_NewDataItem;
      switch (strtolower($this->Mode)) {
        case "template":
          $_tpl_insertform_tr = "<tr><td colspan='{colspan}'><div id='{id}_insertform' class='kgrInsertForm {inputfocus}'>{content}</div></td></tr>";
          $_insertform_tr = _replace("{content}", ($this->Template === null) ? "<b>Notice</b>: Template not found!" : $this->Template->Render($_new_row), $_tpl_insertform_tr);
          $_insertform_tr = _replace("{colspan}", sizeof($this->_TableView->_Columns), $_insertform_tr);
          $_insertform_tr = _replace("{id}", $_new_row->_UniqueID, $_insertform_tr);
          switch (strtolower($this->InputFocus)) {
            case "hidegrid":
              $_insertform_tr = _replace("{inputfocus}", "kgrInputFocus kgrHideGrid", $_insertform_tr);
              break;
            case "blurgrid":
              $_insertform_tr = _replace("{inputfocus}", "kgrInputFocus kgrBlurGrid", $_insertform_tr);
              break;
            default:
              $_insertform_tr = _replace("{inputfocus}", "", $_insertform_tr);
              break;
          }
          break;
        case "form":
        default:
          $_tpl_insertform_tr = "<tr><td colspan='{colspan}'><div id='{id}_insertform' class='kgrInsertForm {inputfocus}'>{header}{validators}{bigtable}{footer}</div></td></tr>";
          $_tpl_insertform_header = "<div class='kgrFormHeader'>{text}</div>";
          $_tpl_insertform_footer = "<div class='kgrFormFooter'>{buttons}</div>";
          $_tpl_insertform_validators = "<ul class='kgrValidator'>{items}</ul>";
          $_tpl_insertform_validator_item = "<li><label for='{id}'>{header}: {error}</label></li>";
          $_tpl_insertform_bigtable = "<table style='table-layout:fixed;width:100%;'><tr>{bigtable_tds}</tr></table>";
          $_tpl_insertform_bigtable_td = "<td style='vertical-align: top;width:{width}%'>{table{n}}</td>";
          $_tpl_insertform_coltable = "<table style='height:{height}px;width:100%;'>{ct_trs}</table>";
          $_tpl_button = "<input type='button' onclick='{onclick}' title='{title}' class='nodecor'/>";
          $_tpl_a = "<a href='javascript:void 0' onclick='{onclick}' title='{title}'>{text}</a>";
          $_tpl_bound = "<span class= '{class}'>{button}{a}</span> ";
          $_unit_height = 35;
          $_header = "";
          if ($this->HeaderCaption != null) {
            $_header = _replace("{text}", $this->HeaderCaption, $_tpl_insertform_header);
          }
          $_items = "";
          if ($this->_NewDataItem !== null && $this->_Command != "StartInsert") {
            foreach ($this->_TableView->_Columns as $_col) {
              if (!$_col->ReadOnly) {
                foreach ($_col->_Validators as $_validator) {
                  if (!$_validator->Validate($_new_row->DataItem[$_col->DataField], $_new_row->DataItem, $_new_row, $_col)) {
                    $_insertform_validator_item = _replace("{header}", $_col->HeaderText, $_tpl_insertform_validator_item);
                    $_insertform_validator_item = _replace("{error}", $_validator->ErrorMessage, $_insertform_validator_item);
                    $_insertform_validator_item = _replace("{id}", $_new_row->_UniqueID . "_" . $_col->_UniqueID . "_input", $_insertform_validator_item);
                    $_items.= $_insertform_validator_item;
                  }
                }
              }
            }
          }
          $_validators = _replace("{items}", $_items, $_tpl_insertform_validators);
          $_bigtable_tds = "";
          for ($i = 0; $i < $this->ColumnNumber; $i++) {
            $_bigtable_td = _replace("{n}", $i, $_tpl_insertform_bigtable_td);
            $_bigtable_td = _replace("{width}", (100 / $this->ColumnNumber), $_bigtable_td);
            $_bigtable_tds.=$_bigtable_td;
          }
          $_bigtable = _replace("{bigtable_tds}", $_bigtable_tds, $_tpl_insertform_bigtable);
          $_arr_coltable_tr = array();
          $_added_height = 0;
          for ($i = 0; $i < sizeof($this->_TableView->_Columns); $i++) {
            $_col = $this->_TableView->_Columns[$i];
            if (!$_col->ReadOnly) {
              $_coltable_tr = $_col->_FormEditRender($_new_row);
              array_push($_arr_coltable_tr, $_coltable_tr);
              if (is_a($_col, 'gridfilecolumn'))
                $_added_height += 165;
              if (is_a($_col, 'gridtextareacolumn') && !empty($_col->BoxHeight))
                $_added_height += intval($_col->BoxHeight) - 18;
            }
          }
          $_no_row_each_table = ceil(sizeof($_arr_coltable_tr) / $this->ColumnNumber);
          for ($i = 0; $i < $this->ColumnNumber; $i++) {
            $_col_trs = "";
            for ($j = 0; $j < $_no_row_each_table; $j++) {
              $_index = $_no_row_each_table * $i + $j;
              if ($_index < sizeof($_arr_coltable_tr)) {
                $_col_trs.=$_arr_coltable_tr[$_index];
              }
            }
            $_insertform_coltable = _replace("{ct_trs}", $_col_trs, $_tpl_insertform_coltable);
            $_insertform_coltable = _replace("{height}", $_unit_height * $_no_row_each_table + $_added_height, $_insertform_coltable);
            if ($_col_trs == "")
              $_insertform_coltable = "";
            $_bigtable = _replace("{table" . $i . "}", $_insertform_coltable, $_bigtable);
          }
          $_confirm_button = _replace("{class}", "kgrConfirm", $_tpl_bound);
          $_confirm_button = _replace("{button}", $_tpl_button, $_confirm_button);
          $_confirm_button = _replace("{a}", ($this->ConfirmButtonText != null) ? $_tpl_a : "", $_confirm_button);
          $_confirm_button = _replace("{onclick}", "grid_confirm_insert(this)", $_confirm_button);
          $_confirm_button = _replace("{title}", $this->ConfirmButtonToolTip, $_confirm_button);
          $_confirm_button = _replace("{text}", $this->ConfirmButtonText, $_confirm_button);
          $_cancel_button = _replace("{class}", "kgrCancel", $_tpl_bound);
          $_cancel_button = _replace("{button}", $_tpl_button, $_cancel_button);
          $_cancel_button = _replace("{a}", ($this->CancelButtonText != null) ? $_tpl_a : "", $_cancel_button);
          $_cancel_button = _replace("{onclick}", "grid_cancel_insert(this)", $_cancel_button);
          $_cancel_button = _replace("{title}", $this->CancelButtonToolTip, $_cancel_button);
          $_cancel_button = _replace("{text}", $this->CancelButtonText, $_cancel_button);
          $_footer = _replace("{buttons}", $_confirm_button . $_cancel_button, $_tpl_insertform_footer);
          $_insertform_tr = _replace("{id}", $_new_row->_UniqueID, $_tpl_insertform_tr);
          $_insertform_tr = _replace("{header}", $_header, $_insertform_tr);
          $_insertform_tr = _replace("{validators}", $_validators, $_insertform_tr);
          $_insertform_tr = _replace("{bigtable}", $_bigtable, $_insertform_tr);
          $_insertform_tr = _replace("{footer}", $_footer, $_insertform_tr);
          $_insertform_tr = _replace("{colspan}", sizeof($this->_TableView->_Columns), $_insertform_tr);
          switch (strtolower($this->InputFocus)) {
            case "hidegrid":
              $_insertform_tr = _replace("{inputfocus}", "kgrInputFocus kgrHideGrid", $_insertform_tr);
              break;
            case "blurgrid":
              $_insertform_tr = _replace("{inputfocus}", "kgrInputFocus kgrBlurGrid", $_insertform_tr);
              break;
            default:
              $_insertform_tr = _replace("{inputfocus}", "", $_insertform_tr);
              break;
          }
          break;
      }
      return $_insertform_tr;
    }
  }
  /* =========================================================================== */
  class GridGroup {
    var $_InfoFields;
    var $GroupField;
    var $Sort = 1;
    var $Expand = true; // The state of group
    var $InfoTemplate;
    var $HeaderText;
    function __construct() {
      $this->_InfoFields = array();
    }
    function AddInfoField($_infofield, $_aggregate = null) {
      array_push($this->_InfoFields, array("InfoField" => $_infofield, "Aggregate" => $_aggregate));
    }
    function _CreateInstance() {
      $_instance = new _GridGroupInstance();
      $_instance->_InfoFields = $this->_InfoFields;
      $_instance->GroupField = $this->GroupField;
      $_instance->Sort = $this->Sort;
      $_instance->Expand = $this->Expand;
      $_instance->InfoTemplate = $this->InfoTemplate;
      $_instance->HeaderText = $this->HeaderText;
      return $_instance;
    }
  }
  class _GridGroupInstance extends GridGroup implements _IState {
    var $_UniqueID;
    var $_TableView;
    var $_ViewState;
    var $_Column;
    function _Init($_tableview) {
      if ($this->_Column !== null) {
        if ($this->GroupField === null)
          $this->GroupField = $this->_Column->DataField;
        if ($this->HeaderText === null)
          $this->HeaderText = $this->_Column->HeaderText;
      }
      $this->_TableView = $_tableview;
      $this->_ViewState = $_tableview->_ViewState;
      if ($this->HeaderText === null)
        $this->HeaderText = $this->GroupField;
      if ($this->InfoTemplate === null)
        $this->InfoTemplate = $this->HeaderText . ": {" . $this->GroupField . "}";
      if (count($this->_InfoFields) == 0)
        $this->AddInfoField($this->GroupField);
    }
    function _LoadViewState() {
      if (isset($this->_ViewState->_Data[$this->_UniqueID])) {
        $_state = $this->_ViewState->_Data[$this->_UniqueID];
        $this->Sort = $_state["Sort"];
        $this->GroupField = $_state["GroupField"];
        $this->Expand = $_state["Expand"];
        $this->InfoTemplate = $_state["InfoTemplate"];
        $this->_InfoFields = $_state["InfoFields"];
        $this->HeaderText = $_state["HeaderText"];
      }
    }
    function _Revive() {
      $this->_LoadViewState();
      if (isset($this->_ViewState->_Data[$this->_UniqueID])) {
        $_state = $this->_ViewState->_Data[$this->_UniqueID];
        if ($_state["ColumnUniqueID"] != null) {
          for ($i = 0; $i < count($this->_TableView->_Columns); $i++) {
            if ($_state["ColumnUniqueID"] == $this->_TableView->_Columns[$i]->_UniqueID) {
              $this->_Column = $this->_TableView->_Columns[$i];
            }
          }
        }
      }
    }
    function _SaveViewState() {
      $this->_ViewState->_Data[$this->_UniqueID] = array("Sort" => $this->Sort,
        "Expand" => $this->Expand,
        "GroupField" => $this->GroupField,
        "HeaderText" => $this->HeaderText,
        "InfoFields" => $this->_InfoFields,
        "InfoTemplate" => $this->InfoTemplate,
        "ColumnUniqueID" => ($this->_Column) ? $this->_Column->_UniqueID : null
      );
    }
    function _ProcessCommand($_command) {
      if (isset($_command->_Commands[$this->_UniqueID])) {
        $_c = $_command->_Commands[$this->_UniqueID];
        switch ($_c["Command"]) {
          case "Sort":
            $this->Sort = $_c["Args"]["Sort"];
            $this->_TableView->_Rebind = true;
            break;
        }
      }
    }
    function _Render() {
      $_tpl_item = "<th id='{id}' class='kgrGroupItem' title='{title}'><div class='kgrIn'>{text}&#160;{sort}</div></th>";
      $_tpl_sort = "<input class='nodecor kgrSort{dir}' type='button' title='{title}' onclick='grid_groupitem_sort(\"{id}\",{sort})' />";
      $_dir_sort = ($this->Sort < 0) ? "Desc" : "Asc";
      $_sort = _replace("{id}", $this->_UniqueID, $_tpl_sort);
      $_sort = _replace("{dir}", $_dir_sort, $_sort);
      $_sort = _replace("{title}", $this->_TableView->_Grid->Localization->_Messages["Sort" . $_dir_sort . "ToolTip"], $_sort);
      $_sort = _replace("{sort}", -$this->Sort, $_sort);
      $_item = _replace("{id}", $this->_UniqueID, $_tpl_item);
      $_item = _replace("{text}", $this->HeaderText, $_item);
      $_item = _replace("{sort}", $_sort, $_item);
      $_item = _replace("{title}", $this->_TableView->_Grid->Localization->_Messages["GroupItemToolTip"], $_item);
      return $_item;
    }
    function _CreateGroup() {
      $_group = new _GridGroup();
      $_group->Expand = $this->Expand; //default model value;
      $_group->_Model = $this;
      return $_group;
    }
  }
  class _GridGroup implements _IState {
    var $_UniqueID;
    var $_TableView;
    var $_Rows;
    var $_SubGroups;
    var $_ViewState;
    var $_Level = 0;
    var $_Value;
    var $_ParentGroup;
    var $_Model; //Contain _GridGroupExpression
    var $_InfoValues;
    var $Expand;
    function _Init($_tableview) {
      $this->_TableView = $_tableview;
      $this->_ViewState = $_tableview->_ViewState;
      $this->_Rows = array();
      $this->_SubGroups = array();
    }
    function _Revive() {
      $this->_LoadViewState();
      foreach ($this->_SubGroups as $_group) {
        $_group->_Revive();
      }
    }
    function _AddRow($_row) {
      array_push($this->_Rows, $_row);
      $this->_Value = $_row->DataItem[$this->_Model->GroupField];
    }
    function _Divide($_rows) {
      $_next_level = $this->_Level + 1;
      if (isset($this->_TableView->_Groups[$_next_level])) {
        $_group_model = $this->_TableView->_Groups[$_next_level];
        $_group = null;
        $group_id = 0;
        for ($i = 0; $i < sizeof($_rows); $i++) {
          if ($_group == null) {
            $_group = $_group_model->_CreateGroup();
            $_group->_UniqueID = $this->_UniqueID . "_gr" . $group_id;
            $_group->_Init($this->_TableView);
            $_group->_Level = $_next_level;
            $_group->_AddRow($_rows[$i]);
            $_group->_ParentGroup = $this;
            array_push($this->_SubGroups, $_group);
          } else {
            if ($_group->_Value == $_rows[$i]->DataItem[$_group->_Model->GroupField]) {
              $_group->_AddRow($_rows[$i]);
            } else {
              $_group->_Divide($_group->_Rows); // Break down to next level.
              $_group = $_group_model->_CreateGroup();
              $group_id++;
              $_group->_UniqueID = $this->_UniqueID . "_gr" . $group_id;
              $_group->_Init($this->_TableView);
              $_group->_Level = $_next_level;
              $_group->_AddRow($_rows[$i]);
              $_group->_ParentGroup = $this;
              array_push($this->_SubGroups, $_group);
            }
          }
          if ($i == sizeof($_rows) - 1) {
            $_group->_Divide($_group->_Rows); // Break down to next level.
          }
        }
      }
    }
    function _LoadViewState() {
      if (isset($this->_ViewState->_Data[$this->_UniqueID])) {
        $_state = $this->_ViewState->_Data[$this->_UniqueID];
        $this->Expand = $_state["Expand"];
      }
    }
    function _SaveViewState() {
      if ($this->_Level > -1) {
        $this->_ViewState->_Data[$this->_UniqueID] = array("Expand" => $this->Expand
        );
      }
      foreach ($this->_SubGroups as $_group) {
        $_group->_SaveViewState(); //All subgrous are force to save state.
      }
    }
    function _ProcessCommand($_command) {
      if (isset($_command->_Commands[$this->_UniqueID])) {
        $_c = $_command->_Commands[$this->_UniqueID];
        switch ($_c["Command"]) {
          case "Expand":
            $this->Expand = true;
            break;
          case "Collapse":
            $this->Expand = false;
            break;
        }
      }
      foreach ($this->_SubGroups as $_group) {
        $_group->_ProcessCommand($_command);
      }
      if ($this->_Model !== null) {
        $_info_values = array();
        $_arr_aggregates = array();
        for ($i = 0; $i < sizeof($this->_Model->_InfoFields); $i++) {
          $_info_field = $this->_Model->_InfoFields[$i];
          if ($_info_field["Aggregate"] == null) {
            $_info_values[$i] = $this->_Rows[0]->DataItem[$_info_field["InfoField"]];
          } else {
            $_info_values[$i] = "";
            array_push($_arr_aggregates, array("Key" => "_" . $i, "Aggregate" => $_info_field["Aggregate"], "DataField" => $_info_field["InfoField"]));
          }
        }
        if (sizeof($_arr_aggregates) > 0) {
          $_origin_filters = $this->_TableView->DataSource->Filters;
          $_parent_group = $this;
          while ($_parent_group !== $this->_TableView->_RootGroup) {
            $this->_TableView->DataSource->AddFilter(new DataSourceFilter($_parent_group->_Model->GroupField, "Equal", $_parent_group->_Value));
            $_parent_group = $_parent_group->_ParentGroup;
          }
          $this->_TableView->DataSource->ArrangeSorts($this->_TableView->MultiSortingOrder);
          $_result = $this->_TableView->DataSource->GetAggregates($_arr_aggregates);
          $_error = $this->_TableView->DataSource->GetError();
          if ($_error != "")
            $this->_TableView->_Grid->EventHandler->OnDataSourceError($this, array("Error" => $_error));
          if ($_result !== null) {
            foreach ($_result as $_k => $_v) {
              $_info_values[_replace("_", "", $_k)] = $_v;
            }
          }
          $this->_TableView->DataSource->Filters = $_origin_filters;
        }
        $this->_InfoValues = $_info_values;
      }
    }
    function _Render() {
      $_trs = "";
      if ($this->_Level > -1) {
        $_tpl_tr = "<tr id='{id}' class='kgrGroup'>{group_tds}<td class='kgrCell' colspan='{colspan}'><div class='kgrIn' style='white-space:nowrap;'><span class='kgrHeaderText' onclick='grid_group_toogle(this)'>{content}</span></div></td></tr>";
        $_tpl_group_td = "<td class='kgrCell'><div class='kgrIn' style='white-space:nowrap;'>{sign}</div></td>";
        $_tpl_group_sign = "<span class='{status}' onclick='grid_group_toogle(this)'></span>";
        $_tr = _replace("{id}", $this->_UniqueID, $_tpl_tr);
        $_group_tds = "";
        for ($i = 0; $i < $this->_Level; $i++) {
          $_group_td = _replace("{sign}", "&#160;", $_tpl_group_td);
          $_group_tds.=$_group_td;
        }
        $_group_sign = _replace("{status}", $this->Expand ? "kgrExpand" : "kgrCollapse", $_tpl_group_sign);
        $_group_td = _replace("{sign}", $_group_sign, $_tpl_group_td);
        $_group_tds.=$_group_td;
        $_colspan = sizeof($this->_TableView->_Columns) - $this->_Level - 1;
        $_tr = _replace("{group_tds}", $_group_tds, $_tr);
        $_tr = _replace("{colspan}", $_colspan, $_tr);
        $_content = $this->_Model->InfoTemplate;
        for ($i = 0; $i < sizeof($this->_Model->_InfoFields); $i++) {
          $_content = _replace("{" . $this->_Model->_InfoFields[$i]["InfoField"] . "}", ($this->_Model->_Column !== null) ? $this->_Model->_Column->Format($this->_InfoValues[$i]) : $this->_InfoValues[$i], $_content);
        }
        $_tr = _replace("{content}", $_content, $_tr);
        $_trs .=$_tr;
        if ($this->Expand) {
          if (sizeof($this->_SubGroups) > 0) {
            foreach ($this->_SubGroups as $_group) {
              $_trs.=$_group->_Render();
            }
          } else {
            foreach ($this->_Rows as $_row) {
              $_trs.=$_row->_Render();
            }
          }
        }
      } else {
        foreach ($this->_SubGroups as $_group) {
          $_trs.=$_group->_Render();
        }
      }
      return $_trs;
    }
  }
  class _GridGroupPanel {
    var $PanelCssClass = "";
    var $ItemCssClass = "";
    var $ItemConnector = "-";
    function _CreateInstance() {
      $_instance = new _GridGroupPanelInstance();
      $_instance->PanelCssClass = $this->PanelCssClass;
      $_instance->ItemCssClass = $this->ItemCssClass;
      $_instance->ItemConnector = $this->ItemConnector;
      return $_instance;
    }
  }
  class _GridGroupPanelInstance extends _GridGroupPanel {
    var $_UniqueID;
    var $_TableView;
    function _Init($_tableview) {
      $this->_TableView = $_tableview;
    }
    function _Render() {
      $_tpl_main = "<div id='{id}' class='kgrGroupPanel' style='position:relative;'><span></span>{indicators}<table class='kgrGroupTable' style='width:100%;border-collapse:collapse;'><tr>{ths}<td id='{id}_tail' style='width:100%;'>{guidetext}</td></tr></table></div>";
      $_tpl_connector = "<td>{ct}</td>";
      $_tpl_indicators = "<div class='kgrTopIndicator' style='position:absolute;display:none;'></div><div class='kgrBottomIndicator' style='position:absolute;display:none;'></div>";
      $_connector = _replace("{ct}", $this->ItemConnector, $_tpl_connector);
      $_ths = "";
      $_groups = $this->_TableView->_Groups;
      for ($i = 0; $i < sizeof($_groups); $i++) {
        $_ths.=$_groups[$i]->_Render();
        if ($i < sizeof($_groups) - 1) {
          $_ths.=$_connector;
        }
      }
      $_main = _replace("{id}", $this->_UniqueID, $_tpl_main);
      $_main = _replace("{ths}", $_ths, $_main);
      $_main = _replace("{indicators}", $_tpl_indicators, $_main);
      $_main = _replace("{guidetext}", (sizeof($_groups) > 0) ? "&#160;" : $this->_TableView->_Grid->Localization->_Messages["GroupPanelGuideText"], $_main);
      return $_main;
    }
  }
  /* =========================================================================== */
  class GridTableView {
    var $DataSource;
    var $DataKeyNames;
    var $Name;
    var $_RelationFields = array();
    var $_Columns = array();
    var $_DetailTables = array();
    var $_Groups = array();
    var $_Description;
    var $Pager;
    var $ShowHeader;
    var $ShowFooter;
    var $Width;
    var $Height;
    var $EditSettings;
    var $InsertSettings;
    var $RowAlternative;
    var $AllowHovering;
    var $AllowSelecting;
    var $AllowMultiSelecting;
    var $AllowEditing;
    var $AllowDeleting;
    var $AllowScrolling;
    var $AllowSorting;
    var $AllowResizing;
    var $AllowFiltering;
    var $AllowGrouping;
    var $SingleColumnSorting;
    var $VirtualScrolling;
    var $FrozenColumnsCount = 0;
    var $PageSize;
    var $ShowFunctionPanel = false;
    var $FunctionPanel;
    var $ShowGroupPanel = false;
    var $GroupPanel;
    var $AutoGenerateRowSelectColumn;
    var $AutoGenerateExpandColumn;
    var $AutoGenerateColumns;
    var $AutoGenerateEditColumn;
    var $AutoGenerateDeleteColumn;
    var $DisableAutoGenerateDataFields;
    var $KeepRowStateOnRefresh;
    var $KeepSelectedRecords; //Work only when DataKeyNames availbles;
    var $ColumnWidth;
    var $ColumnWrap;
    var $ColumnAlign;
    var $ColumnValign;
    var $TableLayout;
    var $FilterOptions;
    var $FilterActions;
    var $_GroupingColumns;
    var $MultiSortingOrder = 'left-right'; //'right-left' | 'FCFS' | 'LCFS'
    var $_SortOrderMin = 0;
    var $_SortOrderMax = 0;
    var $CssClasses = array();
    function __construct($_name = "") {
      $this->Name = $_name;
      $this->EditSettings = new _EditFormSettings();
      $this->InsertSettings = new _InsertFormSettings();
      $this->FunctionPanel = new _FunctionPanel();
      $this->GroupPanel = new _GridGroupPanel();
    }
    function AddGroup($_group) {
      array_push($this->_Groups, $_group);
    }
    function AddColumn($_column) {
      array_push($this->_Columns, $_column);
    }
    function AddDetailTable($_tableview, $_desc = null) {
      $_tableview->_Description = $_desc;
      array_push($this->_DetailTables, $_tableview);
    }
    function AddRelationField($_detail_keyfield, $_master_keyfield) {
      array_push($this->_RelationFields, array("Detail" => $_detail_keyfield, "Master" => $_master_keyfield));
    }
    function _CreateInstance() {
      $_instance = new _GridTableViewInstance();
      $_instance->Name = $this->Name;
      for ($i = 0; $i < sizeof($this->_Groups); $i++) {
        $_instance->_Groups[$i] = $this->_Groups[$i]->_CreateInstance();
      }
      $_group_cols = array();
      for ($i = 0; $i < sizeof($this->_Columns); $i++) {
        $_instance->_Columns[$i] = $this->_Columns[$i]->CreateInstance();
        if ($this->_Columns[$i]->Group) {
          $_group_cols[$i] = $this->_Columns[$i]->GroupIndex;
        }
      }
      asort($_group_cols);
      foreach ($_group_cols as $i => $index) {
        $_instance->_Groups[] = $_instance->_Columns[$i]->GroupSettings;
      }
      $_instance->_DetailTables = $this->_DetailTables;
      $_instance->_RelationFields = $this->_RelationFields;
      $_instance->_Description = $this->_Description;
      if ($this->Pager != null) {
        $_instance->Pager = $this->Pager->_CreateInstance();
      }
      $_instance->DataSource = $this->DataSource;
      $_instance->ShowHeader = $this->ShowHeader;
      $_instance->ShowFooter = $this->ShowFooter;
      $_instance->Width = $this->Width;
      $_instance->Height = $this->Height;
      $_instance->EditSettings = $this->EditSettings;
      $_instance->InsertSettings = $this->InsertSettings;
      $_instance->AllowHovering = $this->AllowHovering;
      $_instance->AllowEditing = $this->AllowEditing;
      $_instance->AllowDeleting = $this->AllowDeleting;
      $_instance->AllowSelecting = $this->AllowSelecting;
      $_instance->AllowMultiSelecting = $this->AllowMultiSelecting;
      $_instance->AllowScrolling = $this->AllowScrolling;
      $_instance->AllowSorting = $this->AllowSorting;
      $_instance->AllowResizing = $this->AllowResizing;
      $_instance->AllowFiltering = $this->AllowFiltering;
      $_instance->AllowGrouping = $this->AllowGrouping;
      $_instance->MultiSortingOrder = $this->MultiSortingOrder;
      $_instance->_SortOrderMin = $this->_SortOrderMin;
      $_instance->_SortOrderMax = $this->_SortOrderMax;
      $_instance->SingleColumnSorting = $this->SingleColumnSorting;
      $_instance->VirtualScrolling = $this->VirtualScrolling;
      $_instance->FrozenColumnsCount = $this->FrozenColumnsCount;
      $_instance->RowAlternative = $this->RowAlternative;
      $_instance->AutoGenerateRowSelectColumn = $this->AutoGenerateRowSelectColumn;
      $_instance->AutoGenerateExpandColumn = $this->AutoGenerateExpandColumn;
      $_instance->AutoGenerateColumns = $this->AutoGenerateColumns;
      $_instance->AutoGenerateEditColumn = $this->AutoGenerateEditColumn;
      $_instance->AutoGenerateDeleteColumn = $this->AutoGenerateDeleteColumn;
      $_instance->DisableAutoGenerateDataFields = $this->DisableAutoGenerateDataFields;
      $_instance->KeepRowStateOnRefresh = $this->KeepRowStateOnRefresh;
      $_instance->KeepSelectedRecords = $this->KeepSelectedRecords;
      $_instance->DataKeyNames = $this->DataKeyNames;
      $_instance->PageSize = $this->PageSize;
      $_instance->ShowFunctionPanel = $this->ShowFunctionPanel;
      $_instance->FunctionPanel = $this->FunctionPanel;
      $_instance->ShowGroupPanel = $this->ShowGroupPanel;
      $_instance->GroupPanel = $this->GroupPanel->_CreateInstance();
      $_instance->ColumnWidth = $this->ColumnWidth;
      $_instance->ColumnWrap = $this->ColumnWrap;
      $_instance->ColumnAlign = $this->ColumnAlign;
      $_instance->ColumnValign = $this->ColumnValign;
      $_instance->TableLayout = $this->TableLayout;
      $_instance->FilterOptions = $this->FilterOptions;
      $_instance->FilterActions = $this->FilterActions;
      $_instance->CssClasses = $this->CssClasses;
      return $_instance;
    }
  }
  class _GridTableViewInstance extends GridTableView implements _IState {
    var $_Grid;
    var $_ParentRow;
    var $_UniqueID;
    var $_ViewState;
    var $Grid;
    var $ParentRow;
    var $_Rows = array();
    var $_RowsCount = 0;
    var $_Rebind = false; //Rebind the data if need.
    var $_Refresh = false;
    var $_scrollTop = 0;
    var $_scrollLeft = 0;
    var $_PartDataHeight = null;
    var $_Inserting = false;
    var $_InsertForm;
    var $_TablePartWidth; // The internal table part width;
    var $_RootGroup;
    var $_GroupSize;
    var $SelectedKeys = array();
    var $_RelationFields;
    function GetUnqiueID() {
      return $this->_UniqueID;
    }
    function _Init($_grid, $_parentrow) {
      $this->_Grid = $_grid;
      $this->_ParentRow = $_parentrow;
      $this->Grid = $_grid;
      $this->ParentRow = $_parentrow;
      $this->_ViewState = $_grid->_ViewState;
      if ($this->KeepSelectedRecords === null)
        $this->KeepSelectedRecords = $this->_Grid->KeepSelectedRecords;
      if ($this->KeepRowStateOnRefresh === null)
        $this->KeepRowStateOnRefresh = $this->_Grid->KeepRowStateOnRefresh;
      if ($this->AllowHovering === null)
        $this->AllowHovering = $this->_Grid->AllowHovering;
      if ($this->AllowEditing === null)
        $this->AllowEditing = $this->_Grid->AllowEditing;
      if ($this->AllowDeleting === null)
        $this->AllowDeleting = $this->_Grid->AllowDeleting;
      if ($this->AllowSelecting === null)
        $this->AllowSelecting = $this->_Grid->AllowSelecting;
      if ($this->AllowMultiSelecting === null)
        $this->AllowMultiSelecting = $this->_Grid->AllowMultiSelecting;
      if ($this->AllowScrolling === null)
        $this->AllowScrolling = $this->_Grid->AllowScrolling;
      if ($this->AllowSorting === null)
        $this->AllowSorting = $this->_Grid->AllowSorting;
      if ($this->AllowResizing === null)
        $this->AllowResizing = $this->_Grid->AllowResizing;
      if ($this->AllowFiltering === null)
        $this->AllowFiltering = $this->_Grid->AllowFiltering;
      if ($this->AllowGrouping === null)
        $this->AllowGrouping = $this->_Grid->AllowGrouping;
      if ($this->SingleColumnSorting === null)
        $this->SingleColumnSorting = $this->_Grid->SingleColumnSorting;
      if ($this->VirtualScrolling === null)
        $this->VirtualScrolling = $this->_Grid->VirtualScrolling;
      if ($this->ShowHeader === null)
        $this->ShowHeader = $this->_Grid->ShowHeader;
      if ($this->ShowFooter === null)
        $this->ShowFooter = $this->_Grid->ShowFooter;
      if ($this->RowAlternative === null)
        $this->RowAlternative = $this->_Grid->RowAlternative;
      if ($this->PageSize === null)
        $this->PageSize = $this->_Grid->PageSize;
      if ($this->DataSource === null)
        $this->DataSource = $this->_Grid->DataSource;
      $this->DataSource->SetCharSet($this->_Grid->CharSet);
      /*
        if($this->Width===null) $this->Width = $this->_Grid->Width;
       */
      if ($this->Width === null)
        $this->Width = "100%";
      if ($_parentrow == null) {
        if ($this->Height === null)
          $this->Height = $this->_Grid->Height;
      }
      if ($this->AutoGenerateRowSelectColumn === null)
        $this->AutoGenerateRowSelectColumn = $this->_Grid->AutoGenerateRowSelectColumn;
      if ($this->AutoGenerateExpandColumn === null)
        $this->AutoGenerateExpandColumn = $this->_Grid->AutoGenerateExpandColumn;
      if ($this->AutoGenerateColumns === null)
        $this->AutoGenerateColumns = $this->_Grid->AutoGenerateColumns;
      if ($this->AutoGenerateEditColumn === null)
        $this->AutoGenerateEditColumn = $this->_Grid->AutoGenerateEditColumn;
      if ($this->AutoGenerateDeleteColumn === null)
        $this->AutoGenerateDeleteColumn = $this->_Grid->AutoGenerateDeleteColumn;
      if ($this->DisableAutoGenerateDataFields === null)
        $this->DisableAutoGenerateDataFields = $this->_Grid->DisableAutoGenerateDataFields;
      if ($this->ColumnWrap === null)
        $this->ColumnWrap = $this->_Grid->ColumnWrap;
      if ($this->ColumnAlign === null)
        $this->ColumnAlign = $this->_Grid->ColumnAlign;
      if ($this->ColumnValign === null)
        $this->ColumnValign = $this->_Grid->ColumnValign;
      if ($this->TableLayout === null)
        $this->TableLayout = $this->_Grid->TableLayout;
      if ($this->FilterOptions === null)
        $this->FilterOptions = $this->_Grid->FilterOptions;
      if ($this->FilterActions === null)
        $this->FilterActions = $this->_Grid->FilterActions;
      if ($this->AllowMultiSelecting) {
        $this->AllowSelecting = true;
      }
      if ($this->AutoGenerateRowSelectColumn) {
        $_rowselect_col = new GridRowSelectColumn();
        $_rowselect_col->Align = "center";
        $_arr = array($_rowselect_col);
        $this->_Columns = array_merge($_arr, $this->_Columns);
      }
      if ($this->AutoGenerateExpandColumn) {
        $_expand_col = new GridExpandDetailColumn();
        $_expand_col->Align = "center";
        $_arr = array($_expand_col);
        $this->_Columns = array_merge($_arr, $this->_Columns);
      }
      if ($this->AutoGenerateColumns) {
        $_fields = $this->DataSource->GetFields();
        $_error = $this->DataSource->GetError();
        if ($_error != "")
          $this->_Grid->EventHandler->OnDataSourceError($this, array("Error" => $_error));
        $_disable_fields = $this->DisableAutoGenerateDataFields . ",";
        foreach ($_fields as $_field) {
          if (strpos($_disable_fields, $_field["Name"] . ",") === false) {
            $_col = new GridBoundColumn();
            $_col->HeaderText = $_field["Name"];
            $_col->DataField = $_field["Name"];
            if ($_field["Not_Null"] == 1) {
              $_col->AddValidator(new RequiredFieldValidator());
            }
            $this->AddColumn($_col);
          }
        }
      }
      if ($this->AutoGenerateEditColumn) {
        $_edit_col = new GridEditDeleteColumn();
        $_edit_col->Align = "center";
        $_edit_col->ShowDeleteButton = false;
        $this->AddColumn($_edit_col);
      }
      if ($this->AutoGenerateDeleteColumn) {
        $_delete_col = new GridEditDeleteColumn();
        $_delete_col->Align = "center";
        $_delete_col->ShowEditButton = false;
        $this->AddColumn($_delete_col);
      }
      for ($i = 0; $i < sizeof($this->_Columns); $i++) {
        $this->_Columns[$i]->_UniqueID = $this->_UniqueID . "_c" . $i;
        $this->_Columns[$i]->_Init($this);
      }
      if ($this->Pager != null) {
        $this->Pager->_UniqueID = $this->_UniqueID . "_pg";
        $this->Pager->_Init($this);
      }
      $this->FunctionPanel->_Init($this);
      $this->GroupPanel->_UniqueID = $this->_UniqueID . "_gp";
      $this->GroupPanel->_Init($this);
      for ($i = 0; $i < sizeof($this->_Groups); $i++) {
        $this->_Groups[$i]->_UniqueID = $this->_UniqueID . "_gm" . $i;
        $this->_Groups[$i]->_Init($this);
      }
    }
    function GetParentRow() {
      return $this->_ParentRow;
    }
    function _LoadViewState() {
      if (isset($this->_ViewState->_Data[$this->_UniqueID])) {
        $_state = $this->_ViewState->_Data[$this->_UniqueID];
        $this->Width = $_state["Width"];
        $this->_TablePartWidth = $_state["TablePartWidth"];
        $this->_RowsCount = $_state["RowsCount"];
        $this->_Inserting = $_state["Inserting"];
        $this->_scrollTop = $_state["scrollTop"];
        $this->_scrollLeft = $_state["scrollLeft"];
        if (isset($_state["SelectedKeys"])) {
          $this->SelectedKeys = $_state["SelectedKeys"];
        }
        if (isset($_state["PartDataHeight"])) {
          $this->_PartDataHeight = $_state["PartDataHeight"];
        }
        $this->_GroupSize = $_state["GroupSize"];
      }
      if ($this->Pager != null) {
        $this->Pager->_LoadViewState();
      }
      for ($i = 0; $i < sizeof($this->_Columns); $i++) {
        $this->_Columns[$i]->_LoadViewState();
      }
    }
    function _SaveViewState() {
      $this->_ViewState->_Data[$this->_UniqueID] = array("RowsCount" => sizeof($this->_Rows),
        "Name" => $this->Name,
        "SelectedKeys" => $this->SelectedKeys,
        "Inserting" => $this->_Inserting,
        "AllowHovering" => $this->AllowHovering,
        "AllowSelecting" => $this->AllowSelecting,
        "AllowMultiSelecting" => $this->AllowMultiSelecting,
        "AllowScrolling" => $this->AllowScrolling,
        "VirtualScrolling" => $this->VirtualScrolling,
        "FrozenColumnsCount" => $this->FrozenColumnsCount,
        "scrollTop" => $this->_scrollTop,
        "scrollLeft" => $this->_scrollLeft,
        "Width" => $this->Width,
        "TablePartWidth" => $this->_TablePartWidth,
        "GroupSize" => sizeof($this->_Groups),
      );
      if ($this->Pager != null) {
        $this->Pager->_SaveViewState();
      }
      for ($i = 0; $i < sizeof($this->_Columns); $i++) {
        $this->_Columns[$i]->_SaveViewState();
      }
      for ($i = 0; $i < sizeof($this->_Rows); $i++) {
        $this->_Rows[$i]->_SaveViewState();
      }
      for ($i = 0; $i < sizeof($this->_Groups); $i++) {
        $this->_Groups[$i]->_SaveViewState();
      }
      if ($this->_RootGroup !== null) {
        $this->_RootGroup->_SaveViewState();
      }
    }
    function _AddRow($_row) {
      $_row->_UniqueID = $this->_UniqueID . "_r" . sizeof($this->_Rows);
      $_row->_Init($this);
      array_push($this->_Rows, $_row);
    }
    function _Revive() {
      $this->_LoadViewState();
      if ($this->_Inserting) {
        $this->_InsertForm = $this->InsertSettings->_CreateInstance();
        $this->_InsertForm->_Init($this);
      }
      for ($i = 0; $i < $this->_RowsCount; $i++) {
        $_row = new GridRow();
        $this->_AddRow($_row);
        $_row->_Revive();
      }
      if ($this->_GroupSize !== null) {
        $this->_Groups = array();
        for ($i = 0; $i < $this->_GroupSize; $i++) {
          $_group_model = new _GridGroupInstance();
          $_group_model->_UniqueID = $this->_UniqueID . "_gm" . $i; //Group Model
          $_group_model->_Init($this);
          $_group_model->_Revive();
          array_push($this->_Groups, $_group_model);
        }
      } else {
        $this->_GroupSize = sizeof($this->_Groups);
      }
    }
    function Refresh() {
      $this->_Refresh = true;
      $this->_Rebind = true;
    }
    function _ProcessCommand($_command) {
      if (!isset($this->_ViewState->_Data[$this->_UniqueID])) {
        $this->_Rebind = true;
      }
      foreach ($this->_Groups as $_group) {
        $_group->_ProcessCommand($_command);
      }
      $this->DataSource->Clear();
      if (isset($_command->_Commands[$this->_UniqueID])) {
        $_c = $_command->_Commands[$this->_UniqueID];
        switch ($_c["Command"]) {
          case "StartInsert":
            if ($this->_Grid->EventHandler->OnBeforeStartInsert($this, array()) == true) {
              $this->_Inserting = true;
              $this->_InsertForm = $this->InsertSettings->_CreateInstance();
              $this->_InsertForm->_Init($this);
              $this->_InsertForm->_Command = "StartInsert";
              $this->_Grid->EventHandler->OnStartInsert($this, array());
            }
            break;
          case "ConfirmInsert":
            $this->_InsertForm->_Command = "ConfirmInsert";
            $this->_InsertForm->_ProcessInsertCommand();
            break;
          case "CancelInsert":
            if ($this->_Grid->EventHandler->OnBeforeCancelInsert($this, array()) == true) {
              $this->_Inserting = false;
              $this->_Grid->EventHandler->OnCancelInsert($this, null);
            }
            break;
          case "Refresh":
            $this->Refresh();
            break;
          case "AddGroup":
            $_group_field = $_c["Args"]["GroupField"];
            $_position = $_c["Args"]["Position"];
            if ($this->_Grid->EventHandler->OnBeforeAddGroup($this, array("Position" => $_position)) == true) {
              $_is_new = true;
              foreach ($this->_Groups as $_group) {
                if ($_group->GroupField == $_group_field) {
                  $_is_new = false;
                }
              }
              if ($_is_new) {
                $_new_group = new _GridGroupInstance();
                $_new_group->GroupField = $_group_field;
                $_new_group->AddInfoField($_group_field);
                foreach ($this->_Columns as $_column) {
                  if ($_column->DataField == $_group_field) {
                    $_new_group->HeaderText = $_column->HeaderText;
                  }
                }
                $_new_group->_Init($this);
                if ($_position === null || ($_position >= sizeof($this->_Groups))) {
                  array_push($this->_Groups, $_new_group);
                } else {
                  $_tmp_groups = array();
                  for ($i = 0; $i < sizeof($this->_Groups); $i++) {
                    if ($_position == $i) {
                      array_push($_tmp_groups, $_new_group);
                    }
                    array_push($_tmp_groups, $this->_Groups[$i]);
                  }
                  $this->_Groups = $_tmp_groups;
                }
                for ($i = 0; $i < sizeof($this->_Groups); $i++) {
                  $this->_Groups[$i]->_UniqueID = $this->_UniqueID . "_gm" . $i;
                }
              }
              $this->_Grid->EventHandler->OnAddGroup($this, array("Position" => $_position));
              $this->Refresh();
            }
            break;
          case "ChangeGroupOrder":
            $_group_field = $_c["Args"]["GroupField"];
            $_new_position = $_c["Args"]["Position"];
            if ($this->_Grid->EventHandler->OnBeforeChangeGroupOrder($this, array("GroupField" => $_group_field, "Position" => $_new_position)) == true) {
              $_old_position = 0;
              for ($i = 0; $i < sizeof($this->_Groups); $i++) {
                if ($this->_Groups[$i]->GroupField == $_group_field) {
                  $_old_position = $i;
                }
              }
              if ($_new_position === null || $_new_position >= sizeof($this->_Groups)) {
                $_new_position = sizeof($this->_Groups);
              }
              $_tmp_groups = array();
              for ($i = 0; $i <= sizeof($this->_Groups); $i++) {
                if ($i == $_new_position) {
                  array_push($_tmp_groups, $this->_Groups[$_old_position]);
                }
                if ($i != $_old_position && $i < sizeof($this->_Groups)) {
                  array_push($_tmp_groups, $this->_Groups[$i]);
                }
              }
              $this->_Groups = $_tmp_groups;
              for ($i = 0; $i < sizeof($this->_Groups); $i++) {
                $this->_Groups[$i]->_UniqueID = $this->_UniqueID . "_gm" . $i;
              }
              $this->_Grid->EventHandler->OnChangeGroupOrder($this, array("GroupField" => $_group_field, "Position" => $_new_position));
              $this->Refresh();
            }
            break;
          case "RemoveGroup":
            $_group_field = $_c["Args"]["GroupField"];
            if ($this->_Grid->EventHandler->OnBeforeRemoveGroup($this, array("GroupField" => $_group_field)) == true) {
              $_tmp_groups = array();
              for ($i = 0; $i < sizeof($this->_Groups); $i++) {
                if ($this->_Groups[$i]->GroupField != $_group_field) {
                  array_push($_tmp_groups, $this->_Groups[$i]);
                }
              }
              $this->_Groups = $_tmp_groups;
              for ($i = 0; $i < sizeof($this->_Groups); $i++) {
                $this->_Groups[$i]->_UniqueID = $this->_UniqueID . "_gm" . $i;
              }
              $this->_Grid->EventHandler->OnRemoveGroup($this, array("GroupField" => $_group_field));
            }
            break;
        }
      }
      if ($this->_ParentRow !== null) {
        if ($this->_ParentRow->_TableView->_Refresh && $this->_ParentRow->_TableView->KeepRowStateOnRefresh) {
          $this->Refresh();
        }
      }
      $this->_Grid->EventHandler->OnTableViewPreRender($this, array());
      foreach ($this->_Columns as $_column) {
        $_column->_ProcessCommand($_command);
      }
      if (sizeof($this->_Groups) > 0) {
        $_arr = array();
        for ($i = 0; $i < sizeof($this->_Groups); $i++) {
          $this->DataSource->AddSort(new DataSourceSort($this->_Groups[$i]->GroupField, ($this->_Groups[$i]->Sort < 0) ? "DESC" : "ASC"));
          $_groupcolumn = new _GridGroupColumn();
          $_groupcolumn->_UniqueID = $this->_UniqueID . "_gc" . $i; // Group column
          $_groupcolumn->_Init($this);
          array_push($_arr, $_groupcolumn);
        }
        $this->_Columns = array_merge($_arr, $this->_Columns);
      }
      foreach ($this->_Rows as $_row) {
        $_row->_ProcessCommand($_command);
      }
      if ($this->_ParentRow != null) {
        foreach ($this->_RelationFields as $_field) {
          $this->DataSource->AddFilter(new DataSourceFilter($_field["Detail"], "Equal", $this->_ParentRow->DataItem[$_field["Master"]]));
        }
      }
      if ($this->Pager != null) {
        $this->Pager->_TotalRows = $this->DataSource->Count();
        $_error = $this->DataSource->GetError();
        if ($_error != "")
          $this->_Grid->EventHandler->OnDataSourceError($this, array("Error" => $_error));
        $this->Pager->_ProcessCommand($_command);
      }
      $_arr_mark = array();
      if ($this->KeepSelectedRecords && $this->DataKeyNames !== null) {
        foreach ($this->SelectedKeys as $_key_data) {
          $_arr_mark[_unique_key($_key_data)] = $_key_data;
        }
        foreach ($this->_Rows as $_row) {
          $_key_data = _get_key_data($_row->DataItem, $this->DataKeyNames);
          $_md5_key = _unique_key($_key_data);
          if ($_row->Selected) {
            $_arr_mark[$_md5_key] = $_key_data;
          } else {
            if (isset($_arr_mark[$_md5_key])) {
              unset($_arr_mark[$_md5_key]);
            }
          }
        }
      }
      if ($this->_Rebind) {
        $_data = array();
        $this->DataSource->ArrangeSorts($this->MultiSortingOrder);
        if ($this->Pager != null) {
          $_data = $this->DataSource->GetData($this->Pager->getStartRecordIndex(), $this->Pager->PageSize);
        } else {
          $_data = $this->DataSource->GetData();
        }
        $_error = $this->DataSource->GetError();
        if ($_error != "")
          $this->_Grid->EventHandler->OnDataSourceError($this, array("Error" => $_error));
        $_old_rows = array();
        if ($this->_Refresh && $this->KeepRowStateOnRefresh && $this->DataKeyNames !== null) {
          foreach ($this->_Rows as $_row) {
            $_key_data = _get_key_data($_row->DataItem, $this->DataKeyNames);
            $_md5_key = _unique_key($_key_data);
            $_old_rows[$_md5_key] = $_row;
          }
        }
        $this->_Rows = array();
        for ($i = 0; $i < sizeof($_data); $i++) {
          $_row = new GridRow();
          $_row->DataItem = $_data[$i];
          if ($this->_Refresh && $this->KeepRowStateOnRefresh && $this->DataKeyNames !== null) {
            $_key_data = _get_key_data($_row->DataItem, $this->DataKeyNames);
            $_md5_key = _unique_key($_key_data);
            if ($_old_rows[$_md5_key] !== null) {
              $_tmp_dataitem = $_row->DataItem;
              $_row = $_old_rows[$_md5_key];
              $_row->DataItem = $_tmp_dataitem;
            }
          }
          $this->_AddRow($_row);
          $_row->_ProcessCommand($_command); //Fix the non-process command at first place, the OnRowPreRender does not run.
        }
        $_arr_aggregates = array();
        foreach ($this->_Columns as $_column) {
          if ($_column->Aggregate !== null) {
            array_push($_arr_aggregates, array("Key" => $_column->_UniqueID, "Aggregate" => $_column->Aggregate, "DataField" => $_column->DataFieldPrefix . $_column->DataField));
          }
        }
        $_result = null;
        if (sizeof($_arr_aggregates) > 0) {
          $this->DataSource->ArrangeSorts($this->MultiSortingOrder);
          $_result = $this->DataSource->GetAggregates($_arr_aggregates);
        }
        $_error = $this->DataSource->GetError();
        if ($_error != "")
          $this->_Grid->EventHandler->OnDataSourceError($this, array("Error" => $_error));
        if ($_result !== null) {
          foreach ($this->_Columns as $_column) {
            if (isset($_result[$_column->_UniqueID])) {
              $_column->_AggregateResult = $_result[$_column->_UniqueID];
            }
          }
        }
      }
      $this->SelectedKeys = array();
      if ($this->KeepSelectedRecords && $this->DataKeyNames !== null) {
        if ($this->_Rebind) {
          foreach ($this->_Rows as $_row) {
            $_key_data = _get_key_data($_row->DataItem, $this->DataKeyNames);
            $_md5_key = _unique_key($_key_data);
            if (isset($_arr_mark[$_md5_key])) {
              $_row->Selected = true;
            }
          }
        }
        foreach ($_arr_mark as $_key_data) {
          array_push($this->SelectedKeys, $_key_data);
        }
      } else {
        foreach ($this->_Rows as $_row) {
          if ($_row->Selected) {
            $_key_data = _get_key_data($_row->DataItem, $this->DataKeyNames);
            array_push($this->SelectedKeys, $_key_data);
          }
        }
      }
      if (sizeof($this->_Groups) > 0) {
        $this->_RootGroup = new _GridGroup();
        $this->_RootGroup->_UniqueID = $this->_UniqueID . "_rg";
        $this->_RootGroup->_Init($this);
        $this->_RootGroup->_Level = -1;
        $this->_RootGroup->_Divide($this->_Rows);
        $this->_RootGroup->_Revive();
        $this->_RootGroup->_ProcessCommand($_command);
      }
    }
    function GetInstanceRows() {
      return $this->_Rows;
    }
    function GetInstanceColumns() {
      return $this->_Columns;
    }
    function GetRelationFields() {
      return $this->_RelationFields;
    }
    function GetCSVData() {
      $_settings = $this->_Grid->ExportSettings;
      $_s = '';
      $_first_col = true;
      foreach ($this->_Columns as $_column) {
        if ($_column->AllowExporting) {
          if (!$_first_col)
            $_s .= $_settings->CsvDelimiter;
          $_s .= $_settings->CsvQuote . $_column->HeaderText . $_settings->CsvQuote;
          $_first_col = false;
        }
      }
      $_s .= "\r\n";
      if ($_settings->IgnorePaging) {
        $this->DataSource->ArrangeSorts($this->MultiSortingOrder);
        $_dataitems = $this->DataSource->GetData();
        $_error = $this->DataSource->GetError();
        if ($_error != "")
          $this->_Grid->EventHandler->OnDataSourceError($this, array("Error" => $_error));
        foreach ($_dataitems as $_dataitem) {
          $_row = new GridRow();
          $_row->DataItem = $_dataitem;
          $_first_col = true;
          foreach ($this->_Columns as $_column) {
            if ($_column->AllowExporting) {
              if (!$_first_col)
                $_s .= $_settings->CsvDelimiter;
              $_s .= $_settings->CsvQuote . $_column->RenderExport($_row) . $_settings->CsvQuote;
              $_first_col = false;
            }
          }
          $_s .= "\r\n";
        }
      }
      else {
        foreach ($this->_Rows as $_row) {
          $_first_col = true;
          foreach ($this->_Columns as $_column) {
            if ($_column->AllowExporting) {
              if (!$_first_col)
                $_s .= $_settings->CsvDelimiter;
              $_s .= $_settings->CsvQuote . $_column->RenderExport($_row) . $_settings->CsvQuote;
              $_first_col = false;
            }
          }
          $_s .= "\r\n";
        }
      }
      return $_s;
    }
    function ExportToCSV() {
      $_settings = $this->_Grid->ExportSettings;
      header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Cache-Control: public");
      header("Content-Description: File Transfer");
      header("Content-Type: application/octet-stream");
      header("Content-Disposition: attachment; filename=\"" . $_settings->FileName . ".csv\"");
      header("Content-Transfer-Encoding: binary");
      $_s = $this->GetCSVData();
      echo $_s;
      exit;
    }
    function ExportToExcel($_exportPath = '') {
      error_reporting(0);
      if (!class_exists("PHPExcel", false)) {
        $_path = dirname(dirname(__FILE__));
        require_once $_path . '/library/PHPExcel/Classes/PHPExcel.php';
        require_once $_path . '/library/PHPExcel/Classes/PHPExcel/Cell.php';
        require_once $_path . '/library/PHPExcel/Classes/PHPExcel/IOFactory.php';
      }
      $_settings = $this->_Grid->ExportSettings;
      $_workbook = new PHPExcel();
      $_workbook->setActiveSheetIndex(0);
      $_col_pos = 0;
      $_row_pos = 1;
      $_maxlength = array();
      foreach ($this->_Columns as $_column) {
        if ($_column->AllowExporting) {
          $_cell = PHPExcel_Cell::stringFromColumnIndex($_col_pos) . $_row_pos;
          $_workbook->getActiveSheet()->setCellValue($_cell, $_column->HeaderText);
          $_workbook->getActiveSheet()->getStyle($_cell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
          $_workbook->getActiveSheet()->getStyle($_cell)->getFont()->setBold(true);
          $_maxlength[$_col_pos] = strlen($_column->HeaderText);
          $_col_pos++;
        }
      }
      $_row_pos++;
      $_col_pos = 0;
      if ($_settings->IgnorePaging) {
        $this->DataSource->ArrangeSorts($this->MultiSortingOrder);
        $_dataitems = $this->DataSource->GetData();
        $_error = $this->DataSource->GetError();
        if ($_error != "")
          $this->_Grid->EventHandler->OnDataSourceError($this, array("Error" => $_error));
        foreach ($_dataitems as $_dataitem) {
          $_row = new GridRow();
          $_row->DataItem = $_dataitem;
          foreach ($this->_Columns as $_column) {
            if ($_column->AllowExporting) {
              $_text = $_column->RenderExport($_row);
              $_cell = PHPExcel_Cell::stringFromColumnIndex($_col_pos) . $_row_pos;
              $_workbook->getActiveSheet()->setCellValue($_cell, $_text);
              $_workbook->getActiveSheet()->getStyle($_cell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
              if ($_maxlength[$_col_pos] < strlen("$_text")) {
                $_maxlength[$_col_pos] = strlen("$_text");
              }
              $_col_pos++;
            }
          }
          $_row_pos++;
          $_col_pos = 0;
        }
      } else {
        foreach ($this->_Rows as $_row) {
          foreach ($this->_Columns as $_column) {
            if ($_column->AllowExporting) {
              $_text = $_column->RenderExport($_row);
              $_cell = PHPExcel_Cell::stringFromColumnIndex($_col_pos) . $_row_pos;
              $_workbook->getActiveSheet()->setCellValue($_cell, $_text);
              $_workbook->getActiveSheet()->getStyle($_cell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
              if ($_maxlength[$_col_pos] < strlen("$_text")) {
                $_maxlength[$_col_pos] = strlen("$_text");
              }
              $_col_pos++;
            }
          }
          $_row_pos++;
          $_col_pos = 0;
        }
      }
      for ($i = 0; $i < sizeof($_maxlength); $i++) {
        $_workbook->getActiveSheet()->getColumnDimensionByColumn($i)->setWidth((($_maxlength[$i] < 45) ? $_maxlength[$i] : 45) + 5);
      }
      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="' . $_settings->FileName . '.xls"');
      header('Cache-Control: max-age=0');
      header('Cache-Control: max-age=1');
      header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
      header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
      header('Pragma: public'); // HTTP/1.0
      $_objWriter = PHPExcel_IOFactory::createWriter($_workbook, 'Excel5');
      $_objWriter->save('php://output');
      exit;
    }
    function QuickExportToExcel() {
      error_reporting(0);
      if (!class_exists("PHPExcel", false)) {
        $_path = dirname(dirname(__FILE__));
        require_once $_path . '/library/PHPExcel/Classes/PHPExcel.php';
        require_once $_path . '/library/PHPExcel/Classes/PHPExcel/Cell.php';
        require_once $_path . '/library/PHPExcel/Classes/PHPExcel/IOFactory.php';
      }
      $_settings = $this->_Grid->ExportSettings;
      $_s = $this->GetCSVData();
      $my_file = 'file.csv';
      $handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
      fwrite($handle, $_s);
      $objReader = PHPExcel_IOFactory::createReader('CSV');
      $objReader->setDelimiter($_settings->CsvDelimiter);
      $objPHPExcel = $objReader->load($my_file);
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="' . $_settings->FileName . '.xls"');
      header('Cache-Control: max-age=0');
      header('Cache-Control: max-age=1');
      header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
      header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
      header('Pragma: public'); // HTTP/1.0
      $objWriter->save('php://output');
      fclose($handle);
      unlink($my_file);
      exit;
    }
    function DirectExportToCSV($_pathFromDatabaseServer, $_pathFromHttpServer) {
      error_reporting(0);
      $_settings = $this->_Grid->ExportSettings;
      $_savePath = $_pathFromDatabaseServer . '/' . $_settings->FileName . '.csv';
      $_openPath = $_pathFromHttpServer . '/' . $_settings->FileName . '.csv';
      unlink($_openPath);
      $_columns = array();
      foreach ($this->_Columns as $_column) {
        if ($_column->AllowExporting) {
          array_push($_columns, array(
            'HeaderText' => $_column->HeaderText, 
            'DataField' => $_column->DataField));
        }
      }
      $this->DataSource->ExportData($_settings, $_columns, $_savePath);      
      header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Cache-Control: public");
      header("Content-Description: File Transfer");
     header("Content-Type: application/octet-stream");
      header("Content-Disposition: attachment; filename=\"" . $_settings->FileName . ".csv\"");
      header("Content-Transfer-Encoding: binary");
      readfile($_openPath);
      exit;
    }
    function DirectExportToExcel($_pathFromDatabaseServer, $_pathFromHttpServer) {
      error_reporting(0);
      if (!class_exists("PHPExcel", false)) {
        $_path = dirname(dirname(__FILE__));
        require_once $_path . '/library/PHPExcel/Classes/PHPExcel.php';
        require_once $_path . '/library/PHPExcel/Classes/PHPExcel/Cell.php';
        require_once $_path . '/library/PHPExcel/Classes/PHPExcel/IOFactory.php';
      }
      $_settings = $this->_Grid->ExportSettings;
      $_savePath = $_pathFromDatabaseServer . '/' . $_settings->FileName . '.csv';
      $_openPath = $_pathFromHttpServer . '/' . $_settings->FileName . '.csv';
      unlink($_openPath);
      $_columns = array();
      foreach ($this->_Columns as $_column) {
        if ($_column->AllowExporting) {
          array_push($_columns, array(
            'HeaderText' => $_column->HeaderText, 
            'DataField' => $_column->DataField));
        }
      }
      $this->DataSource->ExportData($_settings, $_columns, $_savePath);  
      $objReader = PHPExcel_IOFactory::createReader('CSV');
      $objReader->setDelimiter($_settings->CsvDelimiter);
      $objPHPExcel = $objReader->load($_openPath);
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="' . $_settings->FileName . '.xls"');
      header('Cache-Control: max-age=0');
      header('Cache-Control: max-age=1');
      header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
      header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
      header('Pragma: public'); // HTTP/1.0
      $objWriter->save('php://output');
      exit;
    }
    function ExportToWord() {
      $_settings = $this->_Grid->ExportSettings;
      header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Cache-Control: public");
      header("Content-Description: File Transfer");
      header("Content-Type: application/msword");
      header("Content-Disposition: attachment; filename=\"" . $_settings->FileName . ".doc\"");
      header("Content-Transfer-Encoding: binary");
      echo "<table border='1'>";
      echo "<tr>";
      foreach ($this->_Columns as $_column) {
        if ($_column->AllowExporting) {
          echo "<th align='left' style='background-color:#EEEEEE;'>" . $_column->HeaderText . "</th>";
        }
      }
      echo "</tr>";
      if ($_settings->IgnorePaging) {
        $this->DataSource->ArrangeSorts($this->MultiSortingOrder);
        $_dataitems = $this->DataSource->GetData();
        $_error = $this->DataSource->GetError();
        if ($_error != "")
          $this->_Grid->EventHandler->OnDataSourceError($this, array("Error" => $_error));
        foreach ($_dataitems as $_dataitem) {
          $_row = new GridRow();
          $_row->DataItem = $_dataitem;
          echo "<tr>";
          foreach ($this->_Columns as $_column) {
            if ($_column->AllowExporting) {
              echo "<td>" . $_column->RenderExport($_row) . "</td>";
            }
          }
          echo "</tr>";
        }
      } else {
        foreach ($this->_Rows as $_row) {
          echo "<tr>";
          foreach ($this->_Columns as $_column) {
            if ($_column->AllowExporting) {
              echo "<td>" . $_column->RenderExport($_row) . "</td>";
            }
          }
          echo "</tr>";
        }
      }
      echo "</table>";
      exit();
    }
    function ExportToPDF() {
      error_reporting(0);
      if (!class_exists("TCPDF", false)) {
        $_path = dirname(dirname(__FILE__));
        require_once $_path . "/library/tcpdf/config/lang/eng.php";
        require_once $_path . "/library/tcpdf/tcpdf.php";
      }
      $_settings = $this->_Grid->ExportSettings;
      $_pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, $this->_Grid->CharSet, false);
      $_pdf->SetFont('FreeSans', '', 10);
      $_pdf->AddPage();
      $_html = "";
      $_html .= '<table border="1">';
      $_html .= "<tr>";
      foreach ($this->_Columns as $_column) {
        if ($_column->AllowExporting) {
          $_html .= '<th align="left" style="background-color: #EEEEEE;"><b>' . $_column->HeaderText . '</b></th>';
        }
      }
      $_html .= "</tr>";
      if ($_settings->IgnorePaging) {
        $this->DataSource->ArrangeSorts($this->MultiSortingOrder);
        $_dataitems = $this->DataSource->GetData();
        $_error = $this->DataSource->GetError();
        if ($_error != "")
          $this->_Grid->EventHandler->OnDataSourceError($this, array("Error" => $_error));
        foreach ($_dataitems as $_dataitem) {
          $_row = new GridRow();
          $_row->DataItem = $_dataitem;
          $_html .= "<tr>";
          foreach ($this->_Columns as $_column) {
            if ($_column->AllowExporting) {
              $_html .= "<td>" . $_column->RenderExport($_row) . "</td>";
            }
          }
          $_html .= "</tr>";
        }
      } else {
        foreach ($this->_Rows as $_row) {
          $_html .= "<tr>";
          foreach ($this->_Columns as $_column) {
            if ($_column->AllowExporting) {
              $_html .= "<td>" . $_column->RenderExport($_row) . "</td>";
            }
          }
          $_html .= "</tr>";
        }
      }
      $_html .= "</table>";
      $_pdf->writeHTML($_html, true, 0, true, 0);
      $_pdf->Output($_settings->FileName . ".pdf", "D");
      exit();
    }
    function _Render() {
      $_tpl_tableview_div = "<div id='{id}' class='kgrTableView' style='{width}{height}'><div class='kgrTop'>{grouppanel}{pager_top}{functionpanel_top}</div>{tables}<div class='kgrBottom'>{functionpanel_bottom}{pager_bottom}{status}</div></div>";
      $_tpl_table = "<table class='kgrTable' cellspacing='0' style='{style}'>{colgroup}{thead}{tfoot}{tbody}</table>";
      $_tpl_table_part = "<div class='{class}' style='{divstyle}'><table id='{id}_{part}' style='{style}' class='kgrTable'>{colgroup}{tpart}</table></div>";
      $_tpl_colgroup = "<colgroup>{cols}</colgroup>";
      $_tpl_thead = "<thead><tr>{ths}</tr>{insertform}{filter}</thead>";
      $_tpl_filter_tr = "<tr>{tds}</tr>";
      $_tpl_tfoot = "<tfoot>{tfoot_trs}</tfoot>";
      $_tpl_column_footer_tr = "<tr>{col_footer_tds}</tr>";
      $_tpl_tbody = "<tbody>{tbody_trs}</tbody>";
      $_cols = "";
      $_ths = "";
      $_col_footer_tds = "";
      $_num_cols = sizeof($this->_Columns);
      for ($i = 0; $i < $_num_cols; $i++) {
        $_GridColumn = $this->_Columns[$i];
        $_cols .= $_GridColumn->_RenderCol();
        if ($this->ShowHeader) {
          $_ths .= $_GridColumn->RenderHeader();
        }
        if ($this->ShowFooter) {
          $_col_footer_tds .= $_GridColumn->RenderFooter();
        }
      }
      $_colgroup = _replace("{cols}", $_cols, $_tpl_colgroup);
      $_thead = _replace("{ths}", $_ths, $_tpl_thead);
      $_thead = _replace("{insertform}", ($this->_Inserting) ? $this->_InsertForm->_Render() : "", $_thead);
      $_render_filter = false;
      for ($i = 0; $i < sizeof($this->_Columns); $i++) {
        if ($this->_Columns[$i]->AllowFiltering) {
          $_render_filter = true;
        }
      }
      if ($_render_filter) {
        $_filter_tds = "";
        for ($i = 0; $i < sizeof($this->_Columns); $i++) {
          $_filter_tds.=$this->_Columns[$i]->_RenderFilter();
        }
        $_filter_tr = _replace("{tds}", $_filter_tds, $_tpl_filter_tr);
        $_thead = _replace("{filter}", $_filter_tr, $_thead);
      } else {
        $_thead = _replace("{filter}", "", $_thead);
      }
      $_function_panel_top = "";
      $_function_panel_bottom = "";
      if (strtolower($this->ShowFunctionPanel)) {
        $_functionPanelStr = $this->FunctionPanel->_Render();
        $_cssClasses = $this->CssClasses;
        $_functionPanelStr = _replace("{cssclasses}", isset($_cssClasses['function panel']) ? $_cssClasses['function panel'] : '', $_functionPanelStr);
        switch ($this->FunctionPanel->Position) {
          case "top":
            $_function_panel_top = $this->FunctionPanel->_Render();
            break;
          case "top+bottom":
            $_function_panel_top = $this->FunctionPanel->_Render();
            $_function_panel_bottom = $this->FunctionPanel->_Render();
            break;
          case "bottom":
          default:
            $_function_panel_bottom = $this->FunctionPanel->_Render();
            break;
        }
      }
      $_group_panel = "";
      if ($this->ShowGroupPanel) {
        $_group_panel = $this->GroupPanel->_Render();
      }
      $_pager_top = "";
      $_pager_bottom = "";
      if ($this->Pager != null) {
        $_pagerStr = $this->Pager->_Render();
        $_cssClasses = $this->CssClasses;
        $_pagerStr = _replace("{cssclasses}", isset($_cssClasses['pager']) ? $_cssClasses['pager'] : '', $_pagerStr);
        switch (strtolower($this->Pager->Position)) {
          case "top":
            $_pager_top = $_pagerStr;
            break;
          case "top+bottom":
            $_pager_top = $_pagerStr;
            $_pager_bottom = $_pagerStr;
            break;
          case "bottom":
          default:
            $_pager_bottom = $_pagerStr;
            break;
        }
      }
      $_status = "";
      if ($this->_UniqueID == $this->_Grid->_UniqueID . "_mt" && $this->_Grid->ShowStatus) {
        $_status = $this->_Grid->Status->_Render();
      }
      $_tfoot_trs = "";
      if ($this->ShowFooter) {
        $_column_footer_tr = _replace("{col_footer_tds}", $_col_footer_tds, $_tpl_column_footer_tr);
        $_tfoot_trs.=$_column_footer_tr;
      }
      $_tfoot = _replace("{tfoot_trs}", $_tfoot_trs, $_tpl_tfoot);
      $_tbody_trs = "";
      for ($i = 0; $i < sizeof($this->_Rows); $i++) {
        if ($this->RowAlternative) {
          $this->_Rows[$i]->_AlterRow = ($i % 2 != 0);
        }
      }
      if (sizeof($this->_Groups) > 0) {
        $_tbody_trs = $this->_RootGroup->_Render();
      } else {
        for ($i = 0; $i < sizeof($this->_Rows); $i++) {
          $_tbody_trs.=$this->_Rows[$i]->_Render();
        }
      }
      $_tbody = _replace("{tbody_trs}", $_tbody_trs, $_tpl_tbody);
      $_tableview_div = _replace("{id}", $this->_UniqueID, $_tpl_tableview_div);
      if ($this->AllowScrolling == true) {
        $_style = "table-layout:fixed; empty-cells: show; overflow-y:hidden; width:{width};";
        $_style = _replace("{width}", ($this->_TablePartWidth != null) ? $this->_TablePartWidth : "100%", $_style);
        $_table_header = _replace("{id}", $this->_UniqueID, $_tpl_table_part);
        $_table_header = _replace("{part}", "header", $_table_header);
        $_table_header = _replace("{class}", "kgrPartHeader", $_table_header);
        $_table_header = _replace("{colgroup}", $_colgroup, $_table_header);
        $_table_header = _replace("{tpart}", $_thead, $_table_header);
        $_table_header = _replace("{style}", $_style, $_table_header);
        $_table_header = _replace("{divstyle}", "", $_table_header);
        $_table_data = _replace("{id}", $this->_UniqueID, $_tpl_table_part);
        $_table_data = _replace("{part}", "data", $_table_data);
        $_table_data = _replace("{class}", "kgrPartData", $_table_data);
        $_table_data = _replace("{colgroup}", $_colgroup, $_table_data);
        $_table_data = _replace("{tpart}", $_tbody, $_table_data);
        $_table_data = _replace("{style}", $_style, $_table_data);
        $_divstyle = "";
        $_divstyle.=($this->_PartDataHeight) ? "height:" . $this->_PartDataHeight . "px;" : "";
        $_table_data = _replace("{divstyle}", $_divstyle, $_table_data);
        $_table_footer = _replace("{id}", $this->_UniqueID, $_tpl_table_part);
        $_table_footer = _replace("{part}", "footer", $_table_footer);
        $_table_footer = _replace("{class}", "kgrPartFooter", $_table_footer);
        $_table_footer = _replace("{colgroup}", $_colgroup, $_table_footer);
        $_table_footer = _replace("{tpart}", $_tfoot, $_table_footer);
        $_table_footer = _replace("{style}", $_style, $_table_footer);
        $_table_footer = _replace("{divstyle}", "", $_table_footer);
        $_table_data = _replace("{colgroup}", $_colgroup, $_table_data);
        $_table_data = _replace("{tpart}", $_tbody, $_table_data);
        $_tableview_div = _replace("{tables}", $_table_header . $_table_data . $_table_footer, $_tableview_div);
      } else {
        $_table = $_tpl_table;
        $_table = _replace("{colgroup}", $_colgroup, $_table);
        $_table = _replace("{style}", "table-layout: {layout};empty-cells: show;{width}", $_table);
        $_table = _replace("{layout}", $this->TableLayout, $_table);
        if ($this->Width !== null && strpos($this->Width, "%") !== false) {
          $_table = _replace("{width}", "width:100%", $_table);
        }
        $_table = _replace("{thead}", ($this->ShowHeader) ? $_thead : "", $_table);
        $_table = _replace("{tfoot}", $_tfoot, $_table);
        $_table = _replace("{tbody}", $_tbody, $_table);
        $_tableview_div = _replace("{tables}", $_table, $_tableview_div);
      }
      $_tableview_div = _replace("{width}", ($this->Width != "") ? "width:" . $this->Width . ";" : "", $_tableview_div);
      $_tableview_div = _replace("{height}", ($this->Height != "") ? "height:" . $this->Height . ";" : "", $_tableview_div);
      $_tableview_div = _replace("{grouppanel}", $_group_panel, $_tableview_div);
      $_tableview_div = _replace("{functionpanel_top}", $_function_panel_top, $_tableview_div);
      $_tableview_div = _replace("{functionpanel_bottom}", $_function_panel_bottom, $_tableview_div);
      $_tableview_div = _replace("{pager_top}", $_pager_top, $_tableview_div);
      $_tableview_div = _replace("{pager_bottom}", $_pager_bottom, $_tableview_div);
      $_tableview_div = _replace("{status}", $_status, $_tableview_div);
      return $_tableview_div;
    }
  }
  /* =========================================================================== */
  class _StatusBar {
    var $LoadingText;
    var $DoneText;
    function _Init($_grid) {
      if ($this->LoadingText === null)
        $this->LoadingText = $_grid->Localization->_Commands["Loading"];
      if ($this->DoneText === null)
        $this->DoneText = $_grid->Localization->_Commands["Done"];
    }
    function _Render() {
      $_tpl_status = "<div class='kgrStatus'><span class='kgrDoneText'>{donetext}</span><span class='kgrLoadingText'>{loadingtext}</span></div>";
      $_status = _replace("{donetext}", $this->DoneText, $_tpl_status);
      $_status = _replace("{loadingtext}", $this->LoadingText, $_status);
      return $_status;
    }
  }
  /* =========================================================================== */
  class _ExportSettings {
    var $IgnorePaging = false;
    var $FileName = "KoolGridExport";
    var $CsvDelimiter = ',';
    var $CsvQuote = '"';
  }
  class _ClientSettings {
    var $Resizing;
    var $Selecting;
    var $Scrolling;
    var $ClientMessages;
    var $ClientEvents;
    var $_Grid;
    function __construct() {
      $this->Resizing = array("ResizeGridOnColumnResize" => false);
      $this->Selecting = array();
      $this->Scrolling = array("SaveScrollingPosition" => true);
      $this->ClientMessages = array("DeleteConfirm" => null);
      $this->ClientEvents = array();
    }
    function _Init($_grid) {
      $this->_Grid = $_grid;
      if ($this->ClientMessages["DeleteConfirm"] === null)
        $this->ClientMessages["DeleteConfirm"] = $_grid->Localization->_Messages["DeleteConfirm"];
    }
    function _SaveViewState() {
      $_viewstate = $this->_Grid->_ViewState;
      $_grid_id = $this->_Grid->_UniqueID;
      $_viewstate->_Data[$_grid_id]["ClientSettings"] = array();
      $_viewstate->_Data[$_grid_id]["ClientSettings"]["Resizing"] = $this->Resizing;
      $_viewstate->_Data[$_grid_id]["ClientSettings"]["Selecting"] = $this->Selecting;
      $_viewstate->_Data[$_grid_id]["ClientSettings"]["Scrolling"] = $this->Scrolling;
      $_viewstate->_Data[$_grid_id]["ClientSettings"]["ClientMessages"] = $this->ClientMessages;
      $_viewstate->_Data[$_grid_id]["ClientSettings"]["ClientEvents"] = $this->ClientEvents;
    }
  }
  /* =========================================================================== */
  class _FunctionPanel {
    var $ShowRefreshButton = true;
    var $ShowInsertButton = true;
    var $RefreshButtonText;
    var $InsertButtonText;
    var $Position = "bottom";
    var $_TableView;
    var $PanelTemplate = "{Insert} {Refresh}";
    function _Init($_tableview) {
      $this->_TableView = $_tableview;
      if ($this->RefreshButtonText === null)
        $this->RefreshButtonText = $_tableview->_Grid->Localization->_Commands["Refresh"];
      if ($this->InsertButtonText === null)
        $this->InsertButtonText = $_tableview->_Grid->Localization->_Commands["Insert"];
    }
    function _Render() {
      $_tpl_panel = "<div class='kgrFunctionPanel'>{content}</div>";
      $_tpl_button = "<input class='nodecor' type='button' onclick='{onclick}' title='{title}'/>";
      $_tpl_a = "<a href='javascript:void 0' onclick='{onclick}' title='{title}'>{text}</a>";
      $_tpl_bound = "<span class= '{class} {cssclasses}'>{button}{a}</span> ";
      $_refresh_button = _replace("{class}", "kgrRefresh", $_tpl_bound);
      $_refresh_button = _replace("{button}", $_tpl_button, $_refresh_button);
      $_refresh_button = _replace("{a}", ($this->RefreshButtonText != null) ? $_tpl_a : "", $_refresh_button);
      $_refresh_button = _replace("{onclick}", "tableview_refresh(this)", $_refresh_button);
      $_refresh_button = _replace("{title}", "", $_refresh_button);
      $_refresh_button = _replace("{text}", $this->RefreshButtonText, $_refresh_button);
      $_insert_button = _replace("{class}", "kgrInsert", $_tpl_bound);
      $_insert_button = _replace("{button}", $_tpl_button, $_insert_button);
      $_insert_button = _replace("{a}", ($this->InsertButtonText != null) ? $_tpl_a : "", $_insert_button);
      $_insert_button = _replace("{onclick}", "grid_insert(this)", $_insert_button);
      $_insert_button = _replace("{title}", "", $_insert_button);
      $_insert_button = _replace("{text}", $this->InsertButtonText, $_insert_button);
      $_panel = _replace("{content}", $this->PanelTemplate, $_tpl_panel);
      $_panel = _replace("{Refresh}", ($this->ShowRefreshButton) ? $_refresh_button : "", $_panel);
      $_panel = _replace("{Insert}", ($this->ShowInsertButton) ? $_insert_button : "", $_panel);
      return $_panel;
    }
  }
  /* =========================================================================== */
  class _GridBase implements _IState {
    var $_UniqueID;
    var $_ViewState;
    var $MasterTable;
    var $_MasterTableInstance;
    var $AjaxEnabled = false;
    var $AjaxHandlePage;
    var $_Command;
    var $Status;
    var $AllowHovering = false;
    var $AllowSelecting = false;
    var $AllowMultiSelecting = false;
    var $AllowEditing = false;
    var $AllowDeleting = false;
    var $AllowScrolling = false;
    var $AllowSorting = false;
    var $AllowResizing = false;
    var $AllowFiltering = false;
    var $AllowGrouping = false;
    var $VirtualScrolling = false;
    var $SingleColumnSorting = false;
    var $ShowHeader = true;
    var $ShowFooter = false;
    var $RowAlternative = false;
    var $AutoGenerateRowSelectColumn = false;
    var $AutoGenerateExpandColumn = false;
    var $AutoGenerateColumns = false;
    var $AutoGenerateEditColumn = false;
    var $AutoGenerateDeleteColumn = false;
    var $DisableAutoGenerateDataFields = "";
    var $KeepSelectedRecords = false;
    var $ShowStatus = false;
    var $ColumnWrap = false;
    var $ColumnAlign;
    var $ColumnValign;
    var $KeepRowStateOnRefresh = false;
    var $TableLayout = "auto";
    var $Width;
    var $Height;
    var $FilterOptions;
    var $FilterActions;
    var $PageSize = 10;
    var $DataSource;
    var $ClientSettings;
    var $Localization;
    var $CharSet = "UTF-8";
    var $KeepViewStateInSession = false;
    var $KeepGridRefresh = false;
    var $EventHandler;
    var $ExportSettings;
    function __construct($_id) {
      $this->_UniqueID = $_id;
      $this->_ViewState = new _GridViewState();
      $this->Localization = new _GridLocalization();
      $this->MasterTable = new GridTableView("MasterTable");
      $this->_Command = new _GridCommand($this);
      $this->Status = new _StatusBar();
      $this->ClientSettings = new _ClientSettings();
      $this->FilterOptions = array("No_Filter", "Equal", "Not_Equal", "Greater_Than", "Less_Than", "Greater_Than_Or_Equal", "Less_Than_Or_Equal", "Contain", "Not_Contain", "Start_With", "End_With");
      $this->FilterActions = array("AddFilter", "RemoveFilter");
      $this->ExportSettings = new _ExportSettings();
    }
    function _Init() {
      if ($this->EventHandler === null)
        $this->EventHandler = new GridEventHandler();
      $this->_ViewState->_Init($this);
      $this->_MasterTableInstance->_UniqueID = $this->_UniqueID . "_mt";
      $this->_MasterTableInstance->_Init($this, null);
      $this->ClientSettings->_Init($this);
      $this->Status->_Init($this);
      if ($this->DataSource !== null) {
        $this->DataSource->SetCharSet($this->CharSet);
      }
    }
    function _LoadViewState() {
      if (isset($this->_ViewState->_Data[$this->_UniqueID])) {
        $_state = $this->_ViewState->_Data[$this->_UniqueID];
      }
    }
    function _SaveViewState() {
      $this->_ViewState->_Data = array();
      $this->_ViewState->_Data[$this->_UniqueID] = array();
      $this->_MasterTableInstance->_SaveViewState();
      $this->ClientSettings->_SaveViewState();
    }
    function Process() {
      $this->_MasterTableInstance = $this->MasterTable->_CreateInstance();
      $this->_Init();
      $this->_LoadViewState();
      $this->_MasterTableInstance->_Revive();
      if (isset($this->_Command->_Commands[$this->_UniqueID])) {
        $_c = $this->_Command->_Commands[$this->_UniqueID];
        switch ($_c["Command"]) {
          case "Refresh":
            $this->Refresh();
            break;
        }
      }
      if ($this->KeepGridRefresh) {
        $this->Refresh = true;
      }
      $this->EventHandler->OnGridPreRender($this, array());
      $this->_MasterTableInstance->_ProcessCommand($this->_Command);
    }
    function Refresh() {
      if ($this->_MasterTableInstance !== null) {
        $this->_MasterTableInstance->Refresh();
      }
    }
    function GetInstanceMasterTable() {
      return $this->_MasterTableInstance;
    }
    function _RenderGrid() {
      $this->_SaveViewState();
      $_tpl_main = "{mastertable}{viewstate}{command}";
      $_main = _replace("{mastertable}", $this->_MasterTableInstance->_Render(), $_tpl_main);
      $_main = _replace("{viewstate}", $this->_ViewState->_Render(), $_main);
      $_main = _replace("{command}", $this->_Command->_Render(), $_main);
      return $_main;
    }
    /*
      function RegisterEvent($_name,$_handle)
      {
      $this->_eventhandles[$_name] = $_handle;
      }
      function _handleEvent($_name,$_args)
      {
      if(isset($this->_eventhandles[$_name]))
      {
      $_func = $this->_eventhandles[$_name];
      return $_func($this,$_args);
      }
      else
      {
      return true;
      }
      }
     */
    function RegisterClientEvent($_name, $_handle_function_name) {
      $this->ClientSettings->ClientEvents[$_name] = $_handle_function_name;
    }
  }
  class GridEventHandler {
    function OnBeforeDetailTablesExpand($_sender, $_args) {
      return true;
    }
    function OnDetailTablesExpand($_sender, $_args) {
    }
    function OnBeforeDetailTablesCollapse($_sender, $_args) {
      return true;
    }
    function OnDetailTablesCollapse($_sender, $_args) {
    }
    function OnBeforeRowStartEdit($_sender, $_args) {
      return true;
    }
    function OnRowStartEdit($_sender, $_args) {
    }
    function OnBeforeRowCancelEdit($_sender, $_args) {
      return true;
    }
    function OnRowCancelEdit($_sender, $_args) {
    }
    function OnBeforeRowDelete($_sender, $_args) {
      return true;
    }
    function OnRowDelete($_sender, $_args) {
    }
    function OnBeforeColumnSort($_sender, $_args) {
      return true;
    }
    function OnColumnSort($_sender, $_args) {
    }
    function OnBeforeColumnFilter($_sender, $_args) {
      return true;
    }
    function OnColumnFilter($_sender, $_args) {
    }
    function OnBeforeColumnGroup($_sender, $_args) {
      return true;
    }
    function OnColumnGroup($_sender, $_args) {
    }
    function OnBeforeColumnRemoveGroup($_sender, $_args) {
      return true;
    }
    function OnColumnRemoveGroup($_sender, $_args) {
    }
    function OnBeforeAddGroup($_sender, $_args) {
      return true;
    }
    function OnAddGroup($_sender, $_args) {
    }
    function OnBeforeChangeGroupOrder($_sender, $_args) {
      return true;
    }
    function OnChangeGroupOrder($_sender, $_args) {
    }
    function OnBeforeRemoveGroup($_sender, $_args) {
      return true;
    }
    function OnRemoveGroup($_sender, $_args) {
    }
    function OnBeforePageIndexChange($_sender, $_args) {
      return true;
    }
    function OnPageIndexChange($_sender, $_args) {
    }
    function OnBeforePageSizeChange($_sender, $_args) {
      return true;
    }
    function OnPageSizeChange($_sender, $_args) {
    }
     function OnBeforePageOverlapChange($_sender, $_args) {
      return true;
    }
    function OnPageOverlapChange($_sender, $_args) {
    }
    function OnBeforeRowConfirmEdit($_sender, $_args) {
      return true;
    }
    function OnRowConfirmEdit($_sender, $_args) {
    }
    function OnBeforeConfirmInsert($_sender, $_args) {
      return true;
    }
    function OnConfirmInsert($_sender, $_args) {
    }
    function OnBeforeStartInsert($_sender, $_args) {
      return true;
    }
    function OnStartInsert($_sender, $_args) {
    }
    function OnBeforeCancelInsert($_sender, $_args) {
      return true;
    }
    function OnCancelInsert($_sender, $_args) {
    }
    function OnDataSourceError($_sender, $_args) {
    }
    function OnRowPreRender($_sender, $_args) {
    }
    function OnTableViewPreRender($_sender, $_args) {
    }
    function OnGridPreRender($_sender, $_args) {
    }
  }
  /*
   * Server-side event
   * 
   * OnBeforeDetailTablesExpand
   * OnDetailTablesExpand
   * 
   * OnBeforeDetailTablesCollapse
   * OnDetailTablesCollapse
   * 
   * OnBeforeRowStartEdit
   * OnRowStartEdit
   * 
   * OnBeforeRowConfirmEdit
   * OnRowConfirmEdit
   * 
   * OnBeforeRowCancelEdit
   * OnRowCancelEdit
   * 
   * OnBeforeRowDelete
   * OnRowDelete
   * 
   * OnBeforeColumnSort
   * OnColumnSort
   * 
   * OnBeforeColumnGroup
   * OnColumnGroup
   * 
   * OnBeforeColumnFilter
   * OnColumnFilter
   * 
   * OnBeforePageIndexChange
   * OnPageIndexChange
   * 
   * OnBeforePageSizeChange
   * OnPageSizeChange
   * 
   * OnBeforePageOverlapChange
   * OnPageOverlapChange
   * 
   * OnBeforeRefresh
   * OnRefresh
   * 
   * OnColumnInit
   * OnRowDataBound
   * 
   */
  /*   * ***************************************************************************************** */
  /*   * ***************************************************************************************** */
  /*   * ***************************************************************************************** */
  /*   * ***************************************************************************************** */
  /*   * ***************************************************************************************** */
  class KoolGrid extends _GridBase {
    var $_version = "5.7.0.1";
    var $styleFolder;
    var $_style;
    var $scriptFolder;
    var $id;
    var $AjaxLoadingImage;
    function __construct($_id) {
      $this->id = $_id;
      parent::__construct($_id);
    }
    function Render() {
      $_script = "";
      $_script .= $this->RegisterCss();
      $_is_callback = isset($_POST["__koolajax"]) || isset($_GET["__koolajax"]);
      $_script.= ($_is_callback) ? "" : $this->RegisterScript();
      $_script .= $this->RenderGrid();
      $_script.="<script type='text/javascript'>";
      $_script.= $this->StartupScript();
      $_script.="</script>";
      if ($this->AjaxEnabled && class_exists("UpdatePanel")) {
        $_grid_updatepanel = new UpdatePanel($this->id . "_updatepanel");
        $_grid_updatepanel->content = $_script;
        $_grid_updatepanel->cssclass = $this->_style . "KGR_UpdatePanel";
        if ($this->AjaxLoadingImage) {
          $_grid_updatepanel->setLoading($this->AjaxLoadingImage);
        }
        $_script = $_grid_updatepanel->Render();
      }
      return $_script;
    }
    function RenderGrid() {
      $this->_positionStyle();
      $_tpl_main = "{trademark}<div id='{id}' class='{style}KGR' style='{width}'>{content}</div>";
      $_tpl_trademark = "\n<!--KoolGrid version {version} - www.koolphp.net -->\n";
      $_trademark = _replace("{version}", $this->_version, $_tpl_trademark);
      $_main = _replace("{id}", $this->id, $_tpl_main);
      if (true) {
        $_main = _replace("{style}", $this->_style, $_main);
      }
      $_main = _replace("{trademark}", $_trademark, $_main);
      $_main = _replace("{width}", ($this->Width !== null) ? "width:" . $this->Width : "", $_main);
      $_main = _replace("{content}", parent::_RenderGrid(), $_main);
      $_main = _replace("{version}", $this->_version, $_main);
      return $_main;
    }
    function _positionStyle() {
      $this->styleFolder = _replace("\\", "/", $this->styleFolder);
      $_styleFolder = trim($this->styleFolder, "/");
      $_lastpos = strrpos($_styleFolder, "/");
      $this->_style = substr($_styleFolder, ($_lastpos ? $_lastpos : -1) + 1);
    }
    function RegisterCss() {
      $this->_positionStyle();
      $_tpl_script = "<script type='text/javascript'>if (document.getElementById('__{style}KGR')==null){var _head = document.getElementsByTagName('head')[0];var _link = document.createElement('link'); _link.id = '__{style}KGR';_link.rel='stylesheet'; _link.href='{stylepath}/{style}/{style}.css';_head.appendChild(_link);var _link = document.createElement('link'); _link.id = '__KGR';_link.rel='stylesheet'; _link.href='{stylepath}/koolgrid.css';_head.appendChild(_link);}</script>";
      $_script = _replace("{style}", $this->_style, $_tpl_script);
      $_script = _replace("{stylepath}", $this->_getStylePath(), $_script);
      return $_script;
    }
    function RegisterScript() {
      $_tpl_script = "<script type='text/javascript'>if(typeof _libKGR=='undefined'){document.write(unescape(\"%3Cscript type='text/javascript' src='{src}'%3E %3C/script%3E\"));_libKGR=1;}</script>";
			$_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_script);	//Do comment to obfuscate
      return $_script;
    }
    function StartupScript() {
      /*
       * Generate startup script
       */
      $_tpl_script = "var {id}; function {id}_init(){ {id}=new KoolGrid('{id}',{AjaxEnabled},'{AjaxHandlePage}');}";
      $_tpl_script .= "if (typeof(KoolGrid)=='function'){{id}_init();}";
      $_tpl_script .= "else{if(typeof(__KGRInits)=='undefined'){__KGRInits=new Array();} __KGRInits.push({id}_init);{register_script}}";
      $_tpl_register_script = "if(typeof(_libKGR)=='undefined'){var _head = document.getElementsByTagName('head')[0];var _script = document.createElement('script'); _script.type='text/javascript'; _script.src='{src}'; _head.appendChild(_script);_libKGR=1;}";
			$_register_script= _replace("{src}",_replace(".php",".js",$this->_getComponentURI()),$_tpl_register_script); //Do comment to obfuscate
      $_script = _replace("{id}", $this->id, $_tpl_script);
      $_script = _replace("{AjaxEnabled}", $this->AjaxEnabled ? "1" : "0", $_script);
      $_script = _replace("{AjaxHandlePage}", $this->AjaxHandlePage, $_script);
      $_script = _replace("{register_script}", $_register_script, $_script);
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
  /*   * ***************************************************************************************** */
  /*   * ***************************************************************************************** */
  /*   * ***************************************************************************************** */
  /*   * ***************************************************************************************** */
  /*   * ***************************************************************************************** */
}
?>
